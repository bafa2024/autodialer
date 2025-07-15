<?php

/**
 * Web Routes for AutoDial Pro
 * Frontend routes that serve HTML pages
 */

// Home page
$router->get('/', function($request, $params) {
    return file_get_contents('index.html');
});

// Authentication pages
$router->get('/login', function($request, $params) {
    return file_get_contents('login.html');
});

$router->get('/signup', function($request, $params) {
    return file_get_contents('signup.html');
});

// Dashboard and main application routes
$router->get('/dashboard', function($request, $params) {
    return file_get_contents('dashboard.html');
});

$router->get('/dashboard/{section}', function($request, $params) {
    return file_get_contents('index.html');
});

// Campaign management
$router->get('/campaigns', function($request, $params) {
    return file_get_contents('index.html');
});

$router->get('/campaigns/{id}', function($request, $params) {
    return file_get_contents('index.html');
});

// Contact management
$router->get('/contacts', function($request, $params) {
    return file_get_contents('index.html');
});

$router->get('/contacts/{id}', function($request, $params) {
    return file_get_contents('index.html');
});

// Analytics and reports
$router->get('/analytics', function($request, $params) {
    return file_get_contents('index.html');
});

$router->get('/reports', function($request, $params) {
    return file_get_contents('index.html');
});

// Settings
$router->get('/settings', function($request, $params) {
    return file_get_contents('index.html');
});

$router->get('/profile', function($request, $params) {
    return file_get_contents('index.html');
});

// CRM integration
$router->get('/crm', function($request, $params) {
    return file_get_contents('index.html');
});

// Call recordings
$router->get('/recordings', function($request, $params) {
    return file_get_contents('index.html');
});

// AI agents
$router->get('/ai-agents', function($request, $params) {
    return file_get_contents('index.html');
});

// Voice selection
$router->get('/voice-selection', function($request, $params) {
    return file_get_contents('index.html');
});

// Conversation designer
$router->get('/conversation-designer', function($request, $params) {
    return file_get_contents('index.html');
});

// Call summarization
$router->get('/call-summarization', function($request, $params) {
    return file_get_contents('index.html');
});

// Consent capture
$router->get('/consent-capture', function($request, $params) {
    return file_get_contents('index.html');
});

// DNC management
$router->get('/dnc-management', function($request, $params) {
    return file_get_contents('index.html');
});

// Access controls
$router->get('/access-controls', function($request, $params) {
    return file_get_contents('index.html');
});

// Lead/Customer form
$router->get('/lead-form', function($request, $params) {
    return file_get_contents('index.html');
});

// Pipeline/Follow-up
$router->get('/pipeline', function($request, $params) {
    return file_get_contents('index.html');
});

// Contact support
$router->get('/contact-support', function($request, $params) {
    return file_get_contents('index.html');
});

// Manual dial
$router->get('/manual-dial', function($request, $params) {
    return file_get_contents('index.html');
});

// Dialing ratio
$router->get('/dialing-ratio', function($request, $params) {
    return file_get_contents('index.html');
});

// Caller ID management
$router->get('/caller-id', function($request, $params) {
    return file_get_contents('index.html');
});

// Call queue
$router->get('/call-queue', function($request, $params) {
    return file_get_contents('index.html');
});

// Call dispositions
$router->get('/call-dispositions', function($request, $params) {
    return file_get_contents('index.html');
});

// Agent assist
$router->get('/agent-assist', function($request, $params) {
    return file_get_contents('index.html');
});

// Upload contacts
$router->get('/upload-contacts', function($request, $params) {
    return file_get_contents('index.html');
});

// Voicemail detection setup
$router->get('/voicemail-detection', function($request, $params) {
    return file_get_contents('index.html');
});

// Power dialer
$router->get('/power-dialer', function($request, $params) {
    return file_get_contents('index.html');
});

// Email campaign
$router->get('/email-campaign', function($request, $params) {
    return file_get_contents('index.html');
});

// CRM connection
$router->get('/crm-connection', function($request, $params) {
    return file_get_contents('index.html');
});

// Reports & History
$router->get('/reports-history', function($request, $params) {
    return file_get_contents('index.html');
});

// Static assets (CSS, JS, images)
$router->get('/css/{file}', function($request, $params) {
    $file = $params['file'] ?? '';
    $filePath = "css/{$file}";
    
    if (file_exists($filePath)) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $contentTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml'
        ];
        
        if (isset($contentTypes[$extension])) {
            header("Content-Type: {$contentTypes[$extension]}");
        }
        
        return file_get_contents($filePath);
    }
    
    http_response_code(404);
    return 'File not found';
});

$router->get('/js/{file}', function($request, $params) {
    $file = $params['file'] ?? '';
    $filePath = "js/{$file}";
    
    if (file_exists($filePath)) {
        header("Content-Type: application/javascript");
        return file_get_contents($filePath);
    }
    
    http_response_code(404);
    return 'File not found';
});

// Component loading
$router->get('/load_component.php', function($request, $params) {
    $component = $_GET['component'] ?? '';
    $componentFile = "components/{$component}.html";
    
    if (file_exists($componentFile)) {
        return file_get_contents($componentFile);
    }
    
    http_response_code(404);
    return 'Component not found';
});

// Handle 404 for all other routes
$router->any('*', function($request, $params) {
    http_response_code(404);
    return '<!DOCTYPE html>
    <html>
    <head>
        <title>404 - Page Not Found</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="text-center">
                <h1 class="display-1 text-muted">404</h1>
                <h2>Page Not Found</h2>
                <p class="text-muted">The page you are looking for does not exist.</p>
                <a href="/" class="btn btn-primary">Go Home</a>
            </div>
        </div>
    </body>
    </html>';
}); 