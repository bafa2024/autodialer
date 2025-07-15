<?php

namespace Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\App;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use ParagonIE\Sodium\Compat;

class Security
{
    private $logger;
    private $config;
    private static $instance = null;

    private function __construct()
    {
        $this->logger = new Logger('security');
        $this->logger->pushHandler(new StreamHandler('logs/security.log', Logger::DEBUG));
        $this->config = App::get('security');
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // JWT Token Management
    public function generateToken($payload, $expiration = null)
    {
        $expiration = $expiration ?? $this->config['jwt_expiration'];
        
        $token = [
            'iss' => App::get('app_name'),
            'aud' => App::get('app_url'),
            'iat' => time(),
            'exp' => time() + $expiration,
            'nbf' => time(),
            'jti' => $this->generateUUID(),
            'data' => $payload
        ];

        try {
            $jwt = JWT::encode($token, $this->config['jwt_secret'], 'HS256');
            $this->logger->info('JWT token generated', ['user_id' => $payload['user_id'] ?? 'unknown']);
            return $jwt;
        } catch (\Exception $e) {
            $this->logger->error('Failed to generate JWT token: ' . $e->getMessage());
            return false;
        }
    }

    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->config['jwt_secret'], 'HS256'));
            
            if ($decoded->exp < time()) {
                $this->logger->warning('JWT token expired', ['token' => substr($token, 0, 20) . '...']);
                return false;
            }
            
            $this->logger->info('JWT token validated', ['user_id' => $decoded->data->user_id ?? 'unknown']);
            return $decoded->data;
        } catch (\Exception $e) {
            $this->logger->error('JWT token validation failed: ' . $e->getMessage());
            return false;
        }
    }

    public function refreshToken($token)
    {
        $payload = $this->validateToken($token);
        if (!$payload) {
            return false;
        }
        
        return $this->generateToken((array) $payload, $this->config['refresh_token_expiration']);
    }

    // Password Management
    public function hashPassword($password)
    {
        if (!$this->validatePassword($password)) {
            return false;
        }
        
        $hash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        
        $this->logger->info('Password hashed successfully');
        return $hash;
    }

    public function verifyPassword($password, $hash)
    {
        $result = password_verify($password, $hash);
        
        if ($result) {
            $this->logger->info('Password verified successfully');
        } else {
            $this->logger->warning('Password verification failed');
        }
        
        return $result;
    }

    public function validatePassword($password)
    {
        $minLength = $this->config['password_min_length'];
        $requireSpecial = $this->config['password_require_special'];
        
        if (strlen($password) < $minLength) {
            return false;
        }
        
        if ($requireSpecial && !preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
            return false;
        }
        
        return true;
    }

    public function generateSecurePassword($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }

    // CSRF Protection
    public function generateCSRFToken()
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        $this->logger->info('CSRF token generated');
        return $token;
    }

    public function validateCSRFToken($token)
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        $storedToken = $_SESSION['csrf_token'];
        $tokenTime = $_SESSION['csrf_token_time'];
        $expiration = $this->config['csrf_token_expiration'];
        
        if (time() - $tokenTime > $expiration) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }
        
        $result = hash_equals($storedToken, $token);
        
        if ($result) {
            $this->logger->info('CSRF token validated');
        } else {
            $this->logger->warning('CSRF token validation failed');
        }
        
        return $result;
    }

    // Rate Limiting
    public function checkRateLimit($key, $limit, $window = 60)
    {
        $redis = $this->getRedis();
        $current = time();
        $windowStart = $current - $window;
        
        // Remove old entries
        $redis->zRemRangeByScore($key, 0, $windowStart);
        
        // Count current requests
        $count = $redis->zCard($key);
        
        if ($count >= $limit) {
            $this->logger->warning('Rate limit exceeded', ['key' => $key, 'limit' => $limit]);
            return false;
        }
        
        // Add current request
        $redis->zAdd($key, $current, uniqid());
        $redis->expire($key, $window);
        
        return true;
    }

    public function getRemainingRateLimit($key, $limit, $window = 60)
    {
        $redis = $this->getRedis();
        $current = time();
        $windowStart = $current - $window;
        
        $redis->zRemRangeByScore($key, 0, $windowStart);
        $count = $redis->zCard($key);
        
        return max(0, $limit - $count);
    }

    // Input Sanitization
    public function sanitizeInput($input, $type = 'string')
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'string':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }

    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePhone($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid US phone number (10 or 11 digits)
        return strlen($phone) >= 10 && strlen($phone) <= 11;
    }

    // Encryption/Decryption
    public function encrypt($data, $key = null)
    {
        $key = $key ?? $this->config['jwt_secret'];
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($data, $nonce, $key);
        
        return base64_encode($nonce . $ciphertext);
    }

    public function decrypt($encryptedData, $key = null)
    {
        $key = $key ?? $this->config['jwt_secret'];
        $data = base64_decode($encryptedData);
        
        $nonce = substr($data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = substr($data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        
        return sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
    }

    // Session Security
    public function secureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
    }

    // Utility Methods
    public function generateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function generateRandomString($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    private function getRedis()
    {
        // This would be implemented with a Redis connection
        // For now, we'll use a simple file-based approach
        return new class {
            private $data = [];
            
            public function zAdd($key, $score, $member) {
                if (!isset($this->data[$key])) {
                    $this->data[$key] = [];
                }
                $this->data[$key][$member] = $score;
            }
            
            public function zRemRangeByScore($key, $min, $max) {
                if (!isset($this->data[$key])) return 0;
                $count = 0;
                foreach ($this->data[$key] as $member => $score) {
                    if ($score >= $min && $score <= $max) {
                        unset($this->data[$key][$member]);
                        $count++;
                    }
                }
                return $count;
            }
            
            public function zCard($key) {
                return isset($this->data[$key]) ? count($this->data[$key]) : 0;
            }
            
            public function expire($key, $seconds) {
                // Simple expiration simulation
                return true;
            }
        };
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
} 