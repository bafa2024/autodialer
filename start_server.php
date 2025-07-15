<?php

require_once __DIR__ . '/vendor/autoload.php';

use Core\WebSocketServer;
use Config\App;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Set timezone
date_default_timezone_set(App::get('timezone'));

echo "Starting AutoDial Pro WebSocket Server...\n";
echo "Version: " . App::get('app_version') . "\n";
echo "Port: " . App::get('realtime.websocket_port') . "\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "=====================================\n";

// Start the WebSocket server
WebSocketServer::start(); 