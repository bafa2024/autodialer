<?php

namespace Config;

class App
{
    private static $config = [
        'app_name' => 'AutoDial Pro',
        'app_version' => '2.0.0',
        'app_url' => 'http://localhost',
        'timezone' => 'UTC',
        'locale' => 'en',
        'debug' => true,
        'log_level' => 'debug',
        
        // Security Settings
        'security' => [
            'jwt_secret' => 'your-super-secret-jwt-key-change-this-in-production',
            'jwt_expiration' => 3600, // 1 hour
            'refresh_token_expiration' => 604800, // 7 days
            'password_min_length' => 8,
            'password_require_special' => true,
            'max_login_attempts' => 5,
            'lockout_duration' => 900, // 15 minutes
            'session_timeout' => 1800, // 30 minutes
            'csrf_token_expiration' => 3600,
            'rate_limit' => [
                'api' => 100, // requests per minute
                'auth' => 5,  // login attempts per minute
                'dialing' => 1000 // calls per minute
            ]
        ],
        
        // Real-time Features
        'realtime' => [
            'websocket_port' => 8080,
            'pusher' => [
                'app_id' => 'your-pusher-app-id',
                'app_key' => 'your-pusher-key',
                'app_secret' => 'your-pusher-secret',
                'cluster' => 'us2'
            ]
        ],
        
        // VoIP Integration
        'voip' => [
            'twilio' => [
                'account_sid' => 'your-twilio-account-sid',
                'auth_token' => 'your-twilio-auth-token',
                'phone_number' => '+1234567890'
            ],
            'vonage' => [
                'api_key' => 'your-vonage-api-key',
                'api_secret' => 'your-vonage-api-secret'
            ]
        ],
        
        // Email Configuration
        'email' => [
            'driver' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'noreply@autodialpro.com',
            'password' => 'your-email-password',
            'from_address' => 'noreply@autodialpro.com',
            'from_name' => 'AutoDial Pro'
        ],
        
        // AI Services
        'ai' => [
            'openai' => [
                'api_key' => 'your-openai-api-key',
                'model' => 'gpt-4',
                'max_tokens' => 1000
            ],
            'sentiment_analysis' => true,
            'call_summarization' => true,
            'voice_recognition' => true
        ],
        
        // File Storage
        'storage' => [
            'driver' => 'local',
            'path' => 'storage/',
            'max_file_size' => 10485760, // 10MB
            'allowed_extensions' => ['mp3', 'wav', 'csv', 'xlsx', 'xls', 'pdf']
        ],
        
        // Features
        'features' => [
            'ai_agents' => true,
            'call_recording' => true,
            'voicemail_detection' => true,
            'crm_integration' => true,
            'email_campaigns' => true,
            'real_time_analytics' => true,
            'multi_tenant' => false
        ]
    ];

    public static function get($key = null, $default = null)
    {
        if ($key === null) {
            return self::$config;
        }
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    public static function set($key, $value)
    {
        $keys = explode('.', $key);
        $config = &self::$config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }

    public static function isFeatureEnabled($feature)
    {
        return self::get("features.{$feature}", false);
    }
} 