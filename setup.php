<?php

/**
 * AutoDial Pro Setup Script
 * Initializes the database and creates necessary directories
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🚀 AutoDial Pro Setup\n";
echo "=====================\n\n";

// Load required files
require_once 'core/Database.php';

try {
    // Create necessary directories
    $directories = [
        'database',
        'storage',
        'storage/recordings',
        'storage/temp',
        'storage/uploads',
        'logs',
        'views',
        'views/errors'
    ];

    echo "📁 Creating directories...\n";
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "  ✓ Created: {$dir}\n";
            } else {
                echo "  ✗ Failed to create: {$dir}\n";
            }
        } else {
            echo "  - Already exists: {$dir}\n";
        }
    }

    // Initialize database
    echo "\n🗄️  Initializing database...\n";
    $db = Database::getInstance();
    echo "  ✓ Database connection established\n";

    // Check if database was created
    if (file_exists('database/autodialer.db')) {
        echo "  ✓ Database file created\n";
        
        // Get database stats
        $stats = $db->getStats();
        echo "  📊 Database statistics:\n";
        foreach ($stats as $table => $count) {
            echo "    - {$table}: {$count} records\n";
        }
    } else {
        echo "  ✗ Database file not created\n";
        exit(1);
    }

    // Create error view files
    echo "\n📄 Creating error pages...\n";
    
    // 404 Error Page
    $error404 = '<!DOCTYPE html>
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

    file_put_contents('views/errors/404.php', $error404);
    echo "  ✓ Created: views/errors/404.php\n";

    // 500 Error Page
    $error500 = '<!DOCTYPE html>
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

    file_put_contents('views/errors/500.php', $error500);
    echo "  ✓ Created: views/errors/500.php\n";

    // Test database operations
    echo "\n🧪 Testing database operations...\n";
    
    // Test user creation
    $testUser = $db->fetchOne('SELECT * FROM users WHERE email = ?', ['admin@autodialpro.com']);
    if ($testUser) {
        echo "  ✓ Admin user exists\n";
    } else {
        echo "  ✗ Admin user not found\n";
    }

    // Test settings
    $settings = $db->fetchAll('SELECT * FROM settings WHERE user_id = 1');
    if (count($settings) > 0) {
        echo "  ✓ Default settings created\n";
    } else {
        echo "  ✗ Default settings not found\n";
    }

    // Test table structure
    $tables = $db->getTables();
    $expectedTables = [
        'users', 'user_sessions', 'campaigns', 'contacts', 'calls',
        'ai_agents', 'call_recordings', 'crm_integrations', 'caller_ids',
        'dnc_list', 'settings', 'analytics'
    ];

    echo "  📋 Checking table structure:\n";
    foreach ($expectedTables as $table) {
        if (in_array($table, $tables)) {
            echo "    ✓ {$table}\n";
        } else {
            echo "    ✗ {$table} (missing)\n";
        }
    }

    // Set file permissions
    echo "\n🔐 Setting file permissions...\n";
    $files = [
        'database/autodialer.db' => 0644,
        'storage' => 0755,
        'storage/recordings' => 0755,
        'storage/temp' => 0755,
        'storage/uploads' => 0755,
        'logs' => 0755
    ];

    foreach ($files as $file => $permission) {
        if (file_exists($file)) {
            if (chmod($file, $permission)) {
                echo "  ✓ Set permissions on: {$file}\n";
            } else {
                echo "  ✗ Failed to set permissions on: {$file}\n";
            }
        }
    }

    // Create .htaccess for storage protection
    echo "\n🛡️  Creating security files...\n";
    
    $storageHtaccess = 'Deny from all';
    file_put_contents('storage/.htaccess', $storageHtaccess);
    echo "  ✓ Created: storage/.htaccess\n";

    // Create sample data (optional)
    echo "\n📊 Creating sample data...\n";
    
    // Check if sample data already exists
    $campaignCount = $db->count('campaigns');
    if ($campaignCount == 0) {
        // Create sample campaign
        $campaignId = $db->insert('campaigns', [
            'user_id' => 1,
            'name' => 'Sample Sales Campaign',
            'description' => 'A sample campaign for testing purposes',
            'status' => 'draft',
            'dialing_mode' => 'predictive',
            'dialing_ratio' => 2,
            'max_calls_per_hour' => 100
        ]);
        echo "  ✓ Created sample campaign\n";

        // Create sample contacts
        $sampleContacts = [
            ['John', 'Smith', 'john@example.com', '(555) 123-4567'],
            ['Sarah', 'Johnson', 'sarah@example.com', '(555) 987-6543'],
            ['Mike', 'Davis', 'mike@example.com', '(555) 456-7890']
        ];

        foreach ($sampleContacts as $contact) {
            $db->insert('contacts', [
                'user_id' => 1,
                'campaign_id' => $campaignId,
                'first_name' => $contact[0],
                'last_name' => $contact[1],
                'email' => $contact[2],
                'phone' => $contact[3],
                'status' => 'new'
            ]);
        }
        echo "  ✓ Created sample contacts\n";
    } else {
        echo "  - Sample data already exists\n";
    }

    echo "\n✅ Setup completed successfully!\n\n";
    echo "🎉 AutoDial Pro is ready to use!\n\n";
    echo "📝 Next steps:\n";
    echo "  1. Visit your application in the browser\n";
    echo "  2. Login with: admin@autodialpro.com / password\n";
    echo "  3. Start creating campaigns and contacts\n";
    echo "  4. Configure your dialing settings\n\n";
    echo "🔧 For support, check the documentation or contact support.\n";

} catch (Exception $e) {
    echo "\n❌ Setup failed: " . $e->getMessage() . "\n";
    echo "\nPlease check the error and try again.\n";
    exit(1);
} 