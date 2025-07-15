<?php

namespace Core;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Config\App;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class WebSocketServer implements MessageComponentInterface
{
    protected $clients;
    protected $logger;
    protected $config;
    protected $rooms = [];
    protected $userConnections = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->logger = new Logger('websocket');
        $this->logger->pushHandler(new StreamHandler('logs/websocket.log', Logger::DEBUG));
        $this->config = App::get('realtime');
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->logger->info('New WebSocket connection', ['id' => $conn->resourceId]);
        
        // Send welcome message
        $conn->send(json_encode([
            'type' => 'connection',
            'status' => 'connected',
            'message' => 'Connected to AutoDial Pro WebSocket Server'
        ]));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        
        if (!$data) {
            $this->logger->warning('Invalid JSON received', ['message' => $msg]);
            return;
        }

        $this->logger->debug('Message received', ['type' => $data['type'] ?? 'unknown', 'data' => $data]);

        switch ($data['type'] ?? '') {
            case 'auth':
                $this->handleAuthentication($from, $data);
                break;
            case 'join_room':
                $this->handleJoinRoom($from, $data);
                break;
            case 'leave_room':
                $this->handleLeaveRoom($from, $data);
                break;
            case 'call_status':
                $this->handleCallStatus($from, $data);
                break;
            case 'agent_status':
                $this->handleAgentStatus($from, $data);
                break;
            case 'analytics_update':
                $this->handleAnalyticsUpdate($from, $data);
                break;
            case 'notification':
                $this->handleNotification($from, $data);
                break;
            default:
                $this->logger->warning('Unknown message type', ['type' => $data['type'] ?? 'unknown']);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->removeFromRooms($conn);
        $this->logger->info('WebSocket connection closed', ['id' => $conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logger->error('WebSocket error: ' . $e->getMessage(), ['id' => $conn->resourceId]);
        $conn->close();
    }

    protected function handleAuthentication($conn, $data)
    {
        $token = $data['token'] ?? '';
        
        if (empty($token)) {
            $this->sendError($conn, 'Authentication token required');
            return;
        }

        try {
            $decoded = JWT::decode($token, new Key(App::get('security.jwt_secret'), 'HS256'));
            $userId = $decoded->data->user_id ?? null;
            
            if (!$userId) {
                $this->sendError($conn, 'Invalid token');
                return;
            }

            $this->userConnections[$conn->resourceId] = [
                'user_id' => $userId,
                'authenticated' => true,
                'rooms' => []
            ];

            $conn->send(json_encode([
                'type' => 'auth',
                'status' => 'success',
                'message' => 'Authentication successful'
            ]));

            $this->logger->info('User authenticated', ['user_id' => $userId, 'conn_id' => $conn->resourceId]);
        } catch (\Exception $e) {
            $this->sendError($conn, 'Authentication failed: ' . $e->getMessage());
        }
    }

    protected function handleJoinRoom($conn, $data)
    {
        if (!$this->isAuthenticated($conn)) {
            $this->sendError($conn, 'Authentication required');
            return;
        }

        $room = $data['room'] ?? '';
        
        if (empty($room)) {
            $this->sendError($conn, 'Room name required');
            return;
        }

        if (!isset($this->rooms[$room])) {
            $this->rooms[$room] = new \SplObjectStorage;
        }

        $this->rooms[$room]->attach($conn);
        $this->userConnections[$conn->resourceId]['rooms'][] = $room;

        $conn->send(json_encode([
            'type' => 'room',
            'action' => 'joined',
            'room' => $room,
            'message' => "Joined room: {$room}"
        ]));

        $this->logger->info('User joined room', [
            'user_id' => $this->userConnections[$conn->resourceId]['user_id'],
            'room' => $room
        ]);
    }

    protected function handleLeaveRoom($conn, $data)
    {
        $room = $data['room'] ?? '';
        
        if (!empty($room) && isset($this->rooms[$room])) {
            $this->rooms[$room]->detach($conn);
            
            if ($this->rooms[$room]->count() === 0) {
                unset($this->rooms[$room]);
            }
        }

        $this->removeFromRooms($conn);

        $conn->send(json_encode([
            'type' => 'room',
            'action' => 'left',
            'room' => $room,
            'message' => "Left room: {$room}"
        ]));
    }

    protected function handleCallStatus($conn, $data)
    {
        if (!$this->isAuthenticated($conn)) {
            return;
        }

        $callData = [
            'type' => 'call_status',
            'call_id' => $data['call_id'] ?? '',
            'status' => $data['status'] ?? '',
            'agent_id' => $data['agent_id'] ?? '',
            'contact' => $data['contact'] ?? '',
            'duration' => $data['duration'] ?? 0,
            'timestamp' => time()
        ];

        $this->broadcastToRoom('calls', $callData);
    }

    protected function handleAgentStatus($conn, $data)
    {
        if (!$this->isAuthenticated($conn)) {
            return;
        }

        $agentData = [
            'type' => 'agent_status',
            'agent_id' => $data['agent_id'] ?? '',
            'status' => $data['status'] ?? '',
            'current_call' => $data['current_call'] ?? null,
            'timestamp' => time()
        ];

        $this->broadcastToRoom('agents', $agentData);
    }

    protected function handleAnalyticsUpdate($conn, $data)
    {
        if (!$this->isAuthenticated($conn)) {
            return;
        }

        $analyticsData = [
            'type' => 'analytics_update',
            'metrics' => $data['metrics'] ?? [],
            'timestamp' => time()
        ];

        $this->broadcastToRoom('analytics', $analyticsData);
    }

    protected function handleNotification($conn, $data)
    {
        if (!$this->isAuthenticated($conn)) {
            return;
        }

        $notificationData = [
            'type' => 'notification',
            'title' => $data['title'] ?? '',
            'message' => $data['message'] ?? '',
            'level' => $data['level'] ?? 'info',
            'timestamp' => time()
        ];

        $this->broadcastToRoom('notifications', $notificationData);
    }

    public function broadcastToRoom($room, $data)
    {
        if (!isset($this->rooms[$room])) {
            return;
        }

        $message = json_encode($data);
        
        foreach ($this->rooms[$room] as $client) {
            $client->send($message);
        }

        $this->logger->debug('Broadcasted to room', ['room' => $room, 'data' => $data]);
    }

    public function sendToUser($userId, $data)
    {
        $message = json_encode($data);
        
        foreach ($this->clients as $client) {
            if (isset($this->userConnections[$client->resourceId]) && 
                $this->userConnections[$client->resourceId]['user_id'] == $userId) {
                $client->send($message);
            }
        }
    }

    public function broadcastToAll($data)
    {
        $message = json_encode($data);
        
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    protected function sendError($conn, $message)
    {
        $conn->send(json_encode([
            'type' => 'error',
            'message' => $message
        ]));
    }

    protected function isAuthenticated($conn)
    {
        return isset($this->userConnections[$conn->resourceId]) && 
               $this->userConnections[$conn->resourceId]['authenticated'];
    }

    protected function removeFromRooms($conn)
    {
        if (!isset($this->userConnections[$conn->resourceId])) {
            return;
        }

        foreach ($this->userConnections[$conn->resourceId]['rooms'] as $room) {
            if (isset($this->rooms[$room])) {
                $this->rooms[$room]->detach($conn);
                
                if ($this->rooms[$room]->count() === 0) {
                    unset($this->rooms[$room]);
                }
            }
        }

        unset($this->userConnections[$conn->resourceId]);
    }

    public function getStats()
    {
        return [
            'total_connections' => $this->clients->count(),
            'authenticated_users' => count(array_filter($this->userConnections, function($conn) {
                return $conn['authenticated'];
            })),
            'active_rooms' => count($this->rooms),
            'rooms' => array_map(function($room) {
                return $room->count();
            }, $this->rooms)
        ];
    }

    public static function start($port = null)
    {
        $port = $port ?? App::get('realtime.websocket_port');
        
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new self()
                )
            ),
            $port
        );

        echo "WebSocket server started on port {$port}\n";
        $server->run();
    }
} 