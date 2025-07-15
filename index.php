<?php

/**
 * AutoDial Pro - Main Entry Point
 * Handles all incoming requests through the routing system
 */

// Start session
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Load core classes
require_once 'core/Database.php';
require_once 'core/Router.php';
require_once 'middleware/AuthMiddleware.php';
require_once 'app/Controllers/AuthController.php';

// Create router instance
$router = new Router();

// Load routes
require_once 'routes/web.php';
require_once 'routes/api.php';

// Add global middleware
$router->middleware(function($request, $params) {
    // Add CORS headers for API requests
    if (strpos($request['path'], '/api/') === 0) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        // Handle preflight requests
        if ($request['method'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
    
    // Add security headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    
    return null; // Continue to next middleware
});

// Dispatch the request
try {
    $response = $router->dispatch();
    
    if ($response !== null) {
        echo $response;
    }
} catch (Exception $e) {
    // Log the error
    error_log("Router Error: " . $e->getMessage());
    
    // Return error response
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Internal server error',
            'message' => 'An unexpected error occurred'
        ]);
    } else {
        http_response_code(500);
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>500 - Internal Server Error</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container d-flex align-items-center justify-content-center min-vh-100">
                <div class="text-center">
                    <h1 class="display-1 text-danger">500</h1>
                    <h2>Internal Server Error</h2>
                    <p class="text-muted">Something went wrong on our end. Please try again later.</p>
                    <a href="/" class="btn btn-primary">Go Home</a>
                </div>
            </div>
        </body>
        </html>';
    }
}
