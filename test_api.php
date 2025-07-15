<?php
/**
 * AutoDial Pro API Test Script
 * 
 * This script tests the main API endpoints to ensure they're working correctly.
 */

echo "=====================================\n";
echo "AutoDial Pro API Test Script\n";
echo "=====================================\n\n";

// Configuration
$base_url = 'http://localhost:8000/api';
$test_email = 'admin@autodialpro.com';
$test_password = 'Admin123!';

// Test functions
function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    $default_headers = ['Content-Type: application/json'];
    $headers = array_merge($default_headers, $headers);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error, 'http_code' => 0];
    }
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true),
        'raw_response' => $response
    ];
}

// Test 1: Health Check
echo "1. Testing Health Check...\n";
$health_result = makeRequest($base_url . '/health');
if ($health_result['http_code'] === 200) {
    echo "✅ Health check passed\n";
    print_r($health_result['response']);
} else {
    echo "❌ Health check failed (HTTP {$health_result['http_code']})\n";
    if (isset($health_result['error'])) {
        echo "Error: " . $health_result['error'] . "\n";
    }
}
echo "\n";

// Test 2: Login
echo "2. Testing Login...\n";
$login_data = [
    'email' => $test_email,
    'password' => $test_password,
    'remember' => true
];

$login_result = makeRequest($base_url . '/auth/login', 'POST', $login_data);

if ($login_result['http_code'] === 200 && isset($login_result['response']['success']) && $login_result['response']['success']) {
    echo "✅ Login successful\n";
    $token = $login_result['response']['data']['token'];
    echo "Token received: " . substr($token, 0, 20) . "...\n";
} else {
    echo "❌ Login failed (HTTP {$login_result['http_code']})\n";
    if (isset($login_result['response']['message'])) {
        echo "Error: " . $login_result['response']['message'] . "\n";
    }
    if (isset($login_result['error'])) {
        echo "Curl Error: " . $login_result['error'] . "\n";
    }
    echo "Raw response: " . $login_result['raw_response'] . "\n";
}
echo "\n";

// Test 3: User Profile (if login was successful)
if (isset($token)) {
    echo "3. Testing User Profile...\n";
    $profile_result = makeRequest($base_url . '/users/profile', 'GET', null, [
        'Authorization: Bearer ' . $token
    ]);
    
    if ($profile_result['http_code'] === 200) {
        echo "✅ User profile retrieved successfully\n";
        if (isset($profile_result['response']['data']['user'])) {
            $user = $profile_result['response']['data']['user'];
            echo "User: {$user['first_name']} {$user['last_name']} ({$user['email']})\n";
        }
    } else {
        echo "❌ User profile failed (HTTP {$profile_result['http_code']})\n";
        if (isset($profile_result['response']['message'])) {
            echo "Error: " . $profile_result['response']['message'] . "\n";
        }
    }
    echo "\n";
}

// Test 4: Registration (with new email)
echo "4. Testing Registration...\n";
$register_data = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'testuser' . time() . '@example.com',
    'password' => 'TestPass123!',
    'company' => 'Test Company'
];

$register_result = makeRequest($base_url . '/auth/register', 'POST', $register_data);

if ($register_result['http_code'] === 200 && isset($register_result['response']['success']) && $register_result['response']['success']) {
    echo "✅ Registration successful\n";
    echo "New user created: {$register_data['email']}\n";
} else {
    echo "❌ Registration failed (HTTP {$register_result['http_code']})\n";
    if (isset($register_result['response']['message'])) {
        echo "Error: " . $register_result['response']['message'] . "\n";
    }
}
echo "\n";

// Test 5: Logout (if we have a token)
if (isset($token)) {
    echo "5. Testing Logout...\n";
    $logout_result = makeRequest($base_url . '/auth/logout', 'POST', null, [
        'Authorization: Bearer ' . $token
    ]);
    
    if ($logout_result['http_code'] === 200) {
        echo "✅ Logout successful\n";
    } else {
        echo "❌ Logout failed (HTTP {$logout_result['http_code']})\n";
        if (isset($logout_result['response']['message'])) {
            echo "Error: " . $logout_result['response']['message'] . "\n";
        }
    }
    echo "\n";
}

// Test 6: Campaigns (if we have a token)
if (isset($token)) {
    echo "6. Testing Campaigns List...\n";
    $campaigns_result = makeRequest($base_url . '/campaigns', 'GET', null, [
        'Authorization: Bearer ' . $token
    ]);
    
    if ($campaigns_result['http_code'] === 200) {
        echo "✅ Campaigns retrieved successfully\n";
        if (isset($campaigns_result['response']['data'])) {
            $campaigns = $campaigns_result['response']['data'];
            echo "Found " . count($campaigns) . " campaigns\n";
        }
    } else {
        echo "❌ Campaigns failed (HTTP {$campaigns_result['http_code']})\n";
        if (isset($campaigns_result['response']['message'])) {
            echo "Error: " . $campaigns_result['response']['message'] . "\n";
        }
    }
    echo "\n";
}

echo "=====================================\n";
echo "API Test Complete\n";
echo "=====================================\n";

// Summary
echo "\nSummary:\n";
echo "- Make sure your web server is running on port 8000\n";
echo "- Ensure the database is properly configured\n";
echo "- Check that all required PHP extensions are loaded\n";
echo "- Verify the .env file is properly configured\n";
echo "\nIf you see any ❌ errors, check the logs in the logs/ directory\n"; 