<?php

/**
 * Authentication Controller
 * Handles user login, registration, and session management
 */
class AuthController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Handle user login
     */
    public function login($request, $params)
    {
        $email = $request['body']['email'] ?? '';
        $password = $request['body']['password'] ?? '';
        $remember = $request['body']['remember'] ?? false;

        // Validate input
        if (empty($email) || empty($password)) {
            http_response_code(400);
            return json_encode([
                'success' => false,
                'message' => 'Email and password are required'
            ]);
        }

        try {
            // Find user by email
            $user = $this->db->fetchOne(
                'SELECT * FROM users WHERE email = ? AND status = "active"',
                [$email]
            );

            if (!$user) {
                http_response_code(401);
                return json_encode([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);
            }

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                http_response_code(401);
                return json_encode([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);
            }

            // Generate session token
            $token = $this->generateToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Create session
            $this->db->insert('user_sessions', [
                'user_id' => $user['id'],
                'token' => $token,
                'expires_at' => $expiresAt
            ]);

            // Update last login
            $this->db->update('users', 
                ['last_login' => date('Y-m-d H:i:s')], 
                'id = ?', 
                [$user['id']]
            );

            // Set session data
            AuthMiddleware::login([
                'id' => $user['id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'token' => $token
            ]);

            return json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'company' => $user['company_name']
                ],
                'token' => $token
            ]);

        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'An error occurred during login'
            ]);
        }
    }

    /**
     * Handle user registration
     */
    public function register($request, $params)
    {
        $data = $request['body'];

        // Validate required fields
        $required = ['first_name', 'last_name', 'email', 'password', 'company_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
                ]);
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            return json_encode([
                'success' => false,
                'message' => 'Invalid email format'
            ]);
        }

        // Validate password strength
        if (strlen($data['password']) < 8) {
            http_response_code(400);
            return json_encode([
                'success' => false,
                'message' => 'Password must be at least 8 characters long'
            ]);
        }

        try {
            // Check if email already exists
            if ($this->db->exists('users', 'email = ?', [$data['email']])) {
                http_response_code(409);
                return json_encode([
                    'success' => false,
                    'message' => 'Email already registered'
                ]);
            }

            // Hash password
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            // Create user
            $userId = $this->db->insert('users', [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password_hash' => $passwordHash,
                'company_name' => $data['company_name'],
                'role' => 'user',
                'status' => 'active',
                'email_verified' => 0
            ]);

            // Create default settings for the user
            $this->createDefaultSettings($userId);

            return json_encode([
                'success' => true,
                'message' => 'Account created successfully',
                'user_id' => $userId
            ]);

        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'An error occurred during registration'
            ]);
        }
    }

    /**
     * Handle user logout
     */
    public function logout($request, $params)
    {
        try {
            $user = AuthMiddleware::getCurrentUser();
            
            if ($user) {
                // Remove session from database
                $this->db->delete('user_sessions', 'user_id = ?', [$user['id']]);
                
                // Clear session
                AuthMiddleware::logout();
            }

            return json_encode([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);

        } catch (Exception $e) {
            error_log('Logout error: ' . $e->getMessage());
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'An error occurred during logout'
            ]);
        }
    }

    /**
     * Get current user profile
     */
    public function profile($request, $params)
    {
        try {
            $user = AuthMiddleware::getCurrentUser();
            
            if (!$user) {
                http_response_code(401);
                return json_encode([
                    'success' => false,
                    'message' => 'Not authenticated'
                ]);
            }

            // Get user data from database
            $userData = $this->db->fetchOne(
                'SELECT id, first_name, last_name, email, company_name, role, status, timezone, language, created_at, last_login FROM users WHERE id = ?',
                [$user['id']]
            );

            return json_encode([
                'success' => true,
                'user' => $userData
            ]);

        } catch (Exception $e) {
            error_log('Profile error: ' . $e->getMessage());
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'An error occurred while fetching profile'
            ]);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile($request, $params)
    {
        try {
            $user = AuthMiddleware::getCurrentUser();
            
            if (!$user) {
                http_response_code(401);
                return json_encode([
                    'success' => false,
                    'message' => 'Not authenticated'
                ]);
            }

            $data = $request['body'];
            $updateData = [];

            // Allow updating these fields
            $allowedFields = ['first_name', 'last_name', 'company_name', 'timezone', 'language'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $data[$field];
                }
            }

            if (empty($updateData)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'message' => 'No valid fields to update'
                ]);
            }

            $updateData['updated_at'] = date('Y-m-d H:i:s');

            // Update user
            $this->db->update('users', $updateData, 'id = ?', [$user['id']]);

            return json_encode([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        } catch (Exception $e) {
            error_log('Update profile error: ' . $e->getMessage());
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'An error occurred while updating profile'
            ]);
        }
    }

    /**
     * Change password
     */
    public function changePassword($request, $params)
    {
        try {
            $user = AuthMiddleware::getCurrentUser();
            
            if (!$user) {
                http_response_code(401);
                return json_encode([
                    'success' => false,
                    'message' => 'Not authenticated'
                ]);
            }

            $data = $request['body'];
            $currentPassword = $data['current_password'] ?? '';
            $newPassword = $data['new_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'message' => 'Current password and new password are required'
                ]);
            }

            if (strlen($newPassword) < 8) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'message' => 'New password must be at least 8 characters long'
                ]);
            }

            // Get current password hash
            $userData = $this->db->fetchOne(
                'SELECT password_hash FROM users WHERE id = ?',
                [$user['id']]
            );

            // Verify current password
            if (!password_verify($currentPassword, $userData['password_hash'])) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ]);
            }

            // Hash new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password
            $this->db->update('users', 
                ['password_hash' => $newPasswordHash, 'updated_at' => date('Y-m-d H:i:s')], 
                'id = ?', 
                [$user['id']]
            );

            return json_encode([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (Exception $e) {
            error_log('Change password error: ' . $e->getMessage());
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'An error occurred while changing password'
            ]);
        }
    }

    /**
     * Generate a secure token
     */
    private function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Create default settings for a new user
     */
    private function createDefaultSettings($userId)
    {
        $defaultSettings = [
            ['dialing', 'default_mode', 'predictive'],
            ['dialing', 'default_ratio', '2'],
            ['dialing', 'max_calls_per_hour', '100'],
            ['dialing', 'voicemail_detection', '1'],
            ['dialing', 'call_recording', '1'],
            ['system', 'timezone', 'UTC'],
            ['system', 'language', 'en'],
            ['system', 'date_format', 'Y-m-d'],
            ['system', 'time_format', 'H:i:s']
        ];

        foreach ($defaultSettings as $setting) {
            $this->db->insert('settings', [
                'user_id' => $userId,
                'category' => $setting[0],
                'key_name' => $setting[1],
                'value' => $setting[2]
            ]);
        }
    }
} 