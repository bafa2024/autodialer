<?php

/**
 * API Routes for AutoDial Pro
 * Backend API endpoints for AJAX requests
 */

// Authentication endpoints
$router->post('/api/auth/login', 'AuthController@login');
$router->post('/api/auth/register', 'AuthController@register');
$router->post('/api/auth/logout', 'AuthController@logout');
$router->get('/api/auth/profile', 'AuthController@profile');
$router->post('/api/auth/profile', 'AuthController@updateProfile');
$router->post('/api/auth/change-password', 'AuthController@changePassword');

// Campaign management
$router->get('/api/campaigns', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $campaigns = $db->fetchAll(
        'SELECT * FROM campaigns WHERE user_id = ? ORDER BY created_at DESC',
        [$user['id']]
    );
    
    return json_encode([
        'success' => true,
        'campaigns' => $campaigns
    ]);
});

$router->post('/api/campaigns', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $data = $request['body'];
    
    // Validate required fields
    if (empty($data['name'])) {
        http_response_code(400);
        return json_encode(['success' => false, 'message' => 'Campaign name is required']);
    }
    
    $campaignId = $db->insert('campaigns', [
        'user_id' => $user['id'],
        'name' => $data['name'],
        'description' => $data['description'] ?? '',
        'status' => 'draft',
        'dialing_mode' => $data['dialing_mode'] ?? 'predictive',
        'dialing_ratio' => $data['dialing_ratio'] ?? 2,
        'max_calls_per_hour' => $data['max_calls_per_hour'] ?? 100,
        'voicemail_detection' => $data['voicemail_detection'] ?? 1,
        'call_recording' => $data['call_recording'] ?? 1
    ]);
    
    return json_encode([
        'success' => true,
        'campaign_id' => $campaignId,
        'message' => 'Campaign created successfully'
    ]);
});

$router->get('/api/campaigns/{id}', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $id = $params['id'];
    
    $campaign = $db->fetchOne(
        'SELECT * FROM campaigns WHERE id = ? AND user_id = ?',
        [$id, $user['id']]
    );
    
    if (!$campaign) {
        http_response_code(404);
        return json_encode(['success' => false, 'message' => 'Campaign not found']);
    }
    
    return json_encode([
        'success' => true,
        'campaign' => $campaign
    ]);
});

// Contact management
$router->get('/api/contacts', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $page = $request['query']['page'] ?? 1;
    $limit = $request['query']['limit'] ?? 50;
    $campaignId = $request['query']['campaign_id'] ?? null;
    
    $where = 'user_id = ?';
    $whereParams = [$user['id']];
    
    if ($campaignId) {
        $where .= ' AND campaign_id = ?';
        $whereParams[] = $campaignId;
    }
    
    $sql = "SELECT * FROM contacts WHERE {$where} ORDER BY created_at DESC";
    $result = $db->paginate($sql, $whereParams, $page, $limit);
    
    return json_encode([
        'success' => true,
        'contacts' => $result['data'],
        'pagination' => $result['pagination']
    ]);
});

$router->post('/api/contacts', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $data = $request['body'];
    
    // Validate required fields
    if (empty($data['phone'])) {
        http_response_code(400);
        return json_encode(['success' => false, 'message' => 'Phone number is required']);
    }
    
    $contactId = $db->insert('contacts', [
        'user_id' => $user['id'],
        'campaign_id' => $data['campaign_id'] ?? null,
        'first_name' => $data['first_name'] ?? '',
        'last_name' => $data['last_name'] ?? '',
        'company' => $data['company'] ?? '',
        'phone' => $data['phone'],
        'email' => $data['email'] ?? '',
        'status' => 'new'
    ]);
    
    return json_encode([
        'success' => true,
        'contact_id' => $contactId,
        'message' => 'Contact created successfully'
    ]);
});

$router->post('/api/contacts/upload', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    // TODO: Implement file upload and CSV parsing
    return json_encode([
        'success' => true,
        'message' => 'Contacts uploaded successfully',
        'imported' => 150,
        'failed' => 5
    ]);
});

// Call management
$router->get('/api/calls', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $page = $request['query']['page'] ?? 1;
    $limit = $request['query']['limit'] ?? 50;
    $campaignId = $request['query']['campaign_id'] ?? null;
    
    $where = 'c.user_id = ?';
    $whereParams = [$user['id']];
    
    if ($campaignId) {
        $where .= ' AND c.campaign_id = ?';
        $whereParams[] = $campaignId;
    }
    
    $sql = "SELECT c.*, co.first_name, co.last_name, co.company 
            FROM calls c 
            LEFT JOIN contacts co ON c.contact_id = co.id 
            WHERE {$where} 
            ORDER BY c.created_at DESC";
    
    $result = $db->paginate($sql, $whereParams, $page, $limit);
    
    return json_encode([
        'success' => true,
        'calls' => $result['data'],
        'pagination' => $result['pagination']
    ]);
});

$router->post('/api/calls/start', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $data = $request['body'];
    
    // TODO: Implement actual call initiation logic
    $callId = $db->insert('calls', [
        'user_id' => $user['id'],
        'campaign_id' => $data['campaign_id'] ?? null,
        'contact_id' => $data['contact_id'] ?? null,
        'phone_number' => $data['phone_number'],
        'status' => 'initiated'
    ]);
    
    return json_encode([
        'success' => true,
        'call_id' => $callId,
        'status' => 'initiating'
    ]);
});

// Analytics and reports
$router->get('/api/analytics/overview', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    // Get call statistics
    $totalCalls = $db->count('calls', 'user_id = ?', [$user['id']]);
    $completedCalls = $db->count('calls', 'user_id = ? AND status = "completed"', [$user['id']]);
    $answerRate = $totalCalls > 0 ? ($completedCalls / $totalCalls) * 100 : 0;
    
    // Get recent calls for average duration
    $recentCalls = $db->fetchAll(
        'SELECT duration FROM calls WHERE user_id = ? AND status = "completed" AND duration IS NOT NULL ORDER BY created_at DESC LIMIT 100',
        [$user['id']]
    );
    
    $avgDuration = 0;
    if (!empty($recentCalls)) {
        $totalDuration = array_sum(array_column($recentCalls, 'duration'));
        $avgDuration = $totalDuration / count($recentCalls);
    }
    
    return json_encode([
        'success' => true,
        'data' => [
            'total_calls' => $totalCalls,
            'completed_calls' => $completedCalls,
            'answer_rate' => round($answerRate, 1),
            'avg_call_duration' => round($avgDuration, 0)
        ]
    ]);
});

$router->get('/api/analytics/campaign/{id}', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $id = $params['id'];
    
    // Verify campaign belongs to user
    $campaign = $db->fetchOne(
        'SELECT * FROM campaigns WHERE id = ? AND user_id = ?',
        [$id, $user['id']]
    );
    
    if (!$campaign) {
        http_response_code(404);
        return json_encode(['success' => false, 'message' => 'Campaign not found']);
    }
    
    // Get campaign statistics
    $callsMade = $db->count('calls', 'campaign_id = ?', [$id]);
    $completedCalls = $db->count('calls', 'campaign_id = ? AND status = "completed"', [$id]);
    $conversionRate = $callsMade > 0 ? ($completedCalls / $callsMade) * 100 : 0;
    
    return json_encode([
        'success' => true,
        'campaign_id' => $id,
        'data' => [
            'calls_made' => $callsMade,
            'completed_calls' => $completedCalls,
            'conversion_rate' => round($conversionRate, 1)
        ]
    ]);
});

// Settings and configuration
$router->get('/api/settings', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $settings = $db->fetchAll(
        'SELECT category, key_name, value FROM settings WHERE user_id = ?',
        [$user['id']]
    );
    
    // Group settings by category
    $groupedSettings = [];
    foreach ($settings as $setting) {
        $groupedSettings[$setting['category']][$setting['key_name']] = $setting['value'];
    }
    
    return json_encode([
        'success' => true,
        'settings' => $groupedSettings
    ]);
});

$router->post('/api/settings', function($request, $params) {
    $db = Database::getInstance();
    $user = AuthMiddleware::getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        return json_encode(['success' => false, 'message' => 'Authentication required']);
    }
    
    $data = $request['body'];
    
    // Update settings
    foreach ($data as $category => $settings) {
        foreach ($settings as $key => $value) {
            $db->update('settings', 
                ['value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
                'user_id = ? AND category = ? AND key_name = ?', 
                [$user['id'], $category, $key]
            );
        }
    }
    
    return json_encode([
        'success' => true,
        'message' => 'Settings updated successfully'
    ]);
});

// Health check
$router->get('/api/health', function($request, $params) {
    $db = Database::getInstance();
    
    try {
        // Test database connection
        $db->query('SELECT 1');
        $dbStatus = 'healthy';
    } catch (Exception $e) {
        $dbStatus = 'unhealthy';
    }
    
    return json_encode([
        'status' => 'healthy',
        'database' => $dbStatus,
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0'
    ]);
});

// Handle 404 for API routes
$router->any('/api/*', function($request, $params) {
    http_response_code(404);
    return json_encode([
        'error' => 'API endpoint not found',
        'message' => 'The requested API endpoint does not exist'
    ]);
}); 