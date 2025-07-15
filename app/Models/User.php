<?php

namespace App\Models;

use Core\Model;
use Core\Security;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'company',
        'role', 'status', 'timezone', 'language', 'settings'
    ];
    protected $hidden = ['password', 'reset_token', 'email_verification_token'];
    protected $casts = [
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login' => 'datetime',
        'email_verified_at' => 'datetime'
    ];
    protected $rules = [
        'email' => 'required|email|unique',
        'password' => 'required|min:8',
        'first_name' => 'required|max:50',
        'last_name' => 'required|max:50',
        'phone' => 'max:20',
        'role' => 'required|in:admin,manager,agent,viewer'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function authenticate($email, $password)
    {
        $user = $this->findBy('email', $email);
        
        if (!$user) {
            return false;
        }

        if ($user['status'] !== 'active') {
            $this->logger->warning('Login attempt for inactive user', ['email' => $email]);
            return false;
        }

        $security = Security::getInstance();
        if (!$security->verifyPassword($password, $user['password'])) {
            $this->logger->warning('Failed login attempt', ['email' => $email]);
            return false;
        }

        // Update last login
        $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        $this->logger->info('User authenticated successfully', ['user_id' => $user['id']]);
        return $user;
    }

    public function createUser($data)
    {
        $security = Security::getInstance();
        
        // Hash password
        $data['password'] = $security->hashPassword($data['password']);
        
        // Set default values
        $data['status'] = $data['status'] ?? 'active';
        $data['role'] = $data['role'] ?? 'agent';
        $data['settings'] = $data['settings'] ?? [];
        
        return $this->create($data);
    }

    public function updatePassword($userId, $newPassword)
    {
        $security = Security::getInstance();
        
        if (!$security->validatePassword($newPassword)) {
            return false;
        }
        
        $hashedPassword = $security->hashPassword($newPassword);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    public function generateResetToken($email)
    {
        $user = $this->findBy('email', $email);
        
        if (!$user) {
            return false;
        }

        $security = Security::getInstance();
        $token = $security->generateRandomString(64);
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        $this->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expires_at' => $expiresAt
        ]);

        return $token;
    }

    public function resetPassword($token, $newPassword)
    {
        $user = $this->findBy('reset_token', $token);
        
        if (!$user || $user['reset_token_expires_at'] < date('Y-m-d H:i:s')) {
            return false;
        }

        $security = Security::getInstance();
        $hashedPassword = $security->hashPassword($newPassword);
        
        return $this->update($user['id'], [
            'password' => $hashedPassword,
            'reset_token' => null,
            'reset_token_expires_at' => null
        ]);
    }

    public function hasPermission($userId, $permission)
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return false;
        }

        $permissions = $this->getRolePermissions($user['role']);
        return in_array($permission, $permissions);
    }

    private function getRolePermissions($role)
    {
        $permissions = [
            'admin' => [
                'manage_users', 'manage_campaigns', 'view_analytics', 'manage_system',
                'manage_ai_agents', 'manage_crm', 'export_data', 'manage_billing'
            ],
            'manager' => [
                'manage_campaigns', 'view_analytics', 'manage_agents', 'export_data',
                'view_reports', 'manage_contacts'
            ],
            'agent' => [
                'make_calls', 'view_own_calls', 'update_dispositions', 'view_scripts',
                'manage_own_profile'
            ],
            'viewer' => [
                'view_analytics', 'view_reports', 'view_campaigns'
            ]
        ];

        return $permissions[$role] ?? [];
    }

    public function getActiveAgents()
    {
        return $this->findAll(['status' => 'active', 'role' => 'agent'], 'last_name ASC');
    }

    public function getUsersByRole($role)
    {
        return $this->findAll(['role' => $role], 'last_name ASC');
    }

    public function searchUsers($query, $filters = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($query)) {
            $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
            $searchTerm = "%{$query}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($filters['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filters['role'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY last_name ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function updateSettings($userId, $settings)
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        $currentSettings = $user['settings'] ?? [];
        $newSettings = array_merge($currentSettings, $settings);

        return $this->update($userId, ['settings' => $newSettings]);
    }

    public function getDashboardStats($userId)
    {
        $user = $this->find($userId);
        if (!$user) {
            return [];
        }

        $stats = [
            'total_calls' => 0,
            'successful_calls' => 0,
            'conversion_rate' => 0,
            'avg_call_duration' => 0
        ];

        // Get call statistics for the user
        $callModel = new Call();
        $callStats = $callModel->getUserStats($userId);

        return array_merge($stats, $callStats);
    }

    public function deactivateUser($userId)
    {
        return $this->update($userId, ['status' => 'inactive']);
    }

    public function activateUser($userId)
    {
        return $this->update($userId, ['status' => 'active']);
    }

    public function deleteUser($userId)
    {
        // Soft delete - just mark as deleted
        return $this->update($userId, ['status' => 'deleted']);
    }

    public function getOnlineUsers()
    {
        $sql = "SELECT * FROM {$this->table} WHERE last_activity > ? AND status = 'active'";
        $cutoffTime = date('Y-m-d H:i:s', time() - 300); // 5 minutes ago
        
        return $this->db->fetchAll($sql, [$cutoffTime]);
    }

    public function updateLastActivity($userId)
    {
        return $this->update($userId, ['last_activity' => date('Y-m-d H:i:s')]);
    }
} 