<?php

/**
 * Authentication Middleware
 * Handles user authentication and authorization
 */
class AuthMiddleware
{
    /**
     * Handle the middleware
     * @param array $request The request data
     * @param array $params The route parameters
     * @return mixed|null Response if authentication fails, null if successful
     */
    public function handle($request, $params)
    {
        // Check if user is authenticated
        if (!$this->isAuthenticated()) {
            if ($this->isApiRequest($request)) {
                http_response_code(401);
                return json_encode([
                    'success' => false,
                    'message' => 'Authentication required',
                    'redirect' => '/login'
                ]);
            } else {
                // Redirect to login page for web requests
                header('Location: /login');
                exit;
            }
        }

        // Check if user has required permissions (if specified)
        if (isset($params['permissions'])) {
            if (!$this->hasPermissions($params['permissions'])) {
                if ($this->isApiRequest($request)) {
                    http_response_code(403);
                    return json_encode([
                        'success' => false,
                        'message' => 'Insufficient permissions'
                    ]);
                } else {
                    http_response_code(403);
                    return '<!DOCTYPE html>
                    <html>
                    <head>
                        <title>403 - Access Denied</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                    </head>
                    <body>
                        <div class="container d-flex align-items-center justify-content-center min-vh-100">
                            <div class="text-center">
                                <h1 class="display-1 text-danger">403</h1>
                                <h2>Access Denied</h2>
                                <p class="text-muted">You do not have permission to access this resource.</p>
                                <a href="/dashboard" class="btn btn-primary">Go to Dashboard</a>
                            </div>
                        </div>
                    </body>
                    </html>';
                }
            }
        }

        return null; // Continue to the next middleware or route handler
    }

    /**
     * Check if the user is authenticated
     * @return bool
     */
    private function isAuthenticated()
    {
        // Check for session-based authentication
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && isset($_SESSION['user_token'])) {
            return $this->validateSession($_SESSION['user_id'], $_SESSION['user_token']);
        }

        // Check for token-based authentication (API)
        $token = $this->extractToken();
        if ($token) {
            return $this->validateToken($token);
        }

        return false;
    }

    /**
     * Extract token from request headers
     * @return string|null
     */
    private function extractToken()
    {
        $headers = getallheaders();
        
        // Check Authorization header
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        // Check for token in query parameters
        if (isset($_GET['token'])) {
            return $_GET['token'];
        }

        return null;
    }

    /**
     * Validate session-based authentication
     * @param int $userId
     * @param string $token
     * @return bool
     */
    private function validateSession($userId, $token)
    {
        // TODO: Implement actual session validation
        // This should check against the database and validate the token
        
        // For now, return true if both values exist
        return !empty($userId) && !empty($token);
    }

    /**
     * Validate token-based authentication
     * @param string $token
     * @return bool
     */
    private function validateToken($token)
    {
        // TODO: Implement actual token validation
        // This should validate JWT tokens or other token types
        
        // For now, check if token starts with 'sample_token_'
        return strpos($token, 'sample_token_') === 0;
    }

    /**
     * Check if user has required permissions
     * @param array $requiredPermissions
     * @return bool
     */
    private function hasPermissions($requiredPermissions)
    {
        // TODO: Implement actual permission checking
        // This should check user roles and permissions from the database
        
        // For now, return true for admin users
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Check if the request is an API request
     * @param array $request
     * @return bool
     */
    private function isApiRequest($request)
    {
        $path = $request['path'] ?? '';
        return strpos($path, '/api/') === 0 || 
               isset($request['headers']['Accept']) && strpos($request['headers']['Accept'], 'application/json') !== false;
    }

    /**
     * Get current authenticated user
     * @return array|null
     */
    public static function getCurrentUser()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            // TODO: Fetch user data from database
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? 'Unknown User',
                'email' => $_SESSION['user_email'] ?? '',
                'role' => $_SESSION['user_role'] ?? 'user'
            ];
        }

        return null;
    }

    /**
     * Login user and create session
     * @param array $userData
     * @return bool
     */
    public static function login($userData)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_name'] = $userData['name'];
        $_SESSION['user_email'] = $userData['email'];
        $_SESSION['user_role'] = $userData['role'];
        $_SESSION['user_token'] = $userData['token'] ?? 'token_' . time();
        $_SESSION['login_time'] = time();

        return true;
    }

    /**
     * Logout user and destroy session
     * @return bool
     */
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear all session data
        session_unset();
        session_destroy();

        return true;
    }

    /**
     * Check if user has a specific role
     * @param string $role
     * @return bool
     */
    public static function hasRole($role)
    {
        $user = self::getCurrentUser();
        return $user && $user['role'] === $role;
    }

    /**
     * Check if user has any of the specified roles
     * @param array $roles
     * @return bool
     */
    public static function hasAnyRole($roles)
    {
        $user = self::getCurrentUser();
        return $user && in_array($user['role'], $roles);
    }
} 