<?php
/**
 * WordPress Admin Setup Script
 * Run this once to create an admin user and bypass Wasmer authentication
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Check if we're running this script
if (isset($_GET['setup_admin']) && $_GET['setup_admin'] === 'true') {
    
    // Create admin user if it doesn't exist
    $admin_user = get_user_by('login', 'admin');
    
    if (!$admin_user) {
        $user_id = wp_create_user('admin', 'admin123', 'admin@example.com');
        
        if (!is_wp_error($user_id)) {
            // Make user admin
            $user = new WP_User($user_id);
            $user->set_role('administrator');
            
            echo "<h2>✅ Admin User Created Successfully!</h2>";
            echo "<p><strong>Username:</strong> admin</p>";
            echo "<p><strong>Password:</strong> admin123</p>";
            echo "<p><strong>Email:</strong> admin@example.com</p>";
            echo "<hr>";
            echo "<p><a href='/wp-admin/' style='background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;'>Go to WordPress Admin</a></p>";
        } else {
            echo "<h2>❌ Error creating admin user:</h2>";
            echo "<p>" . $user_id->get_error_message() . "</p>";
        }
    } else {
        echo "<h2>ℹ️ Admin user already exists!</h2>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<hr>";
        echo "<p><a href='/wp-admin/' style='background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;'>Go to WordPress Admin</a></p>";
    }
    
    echo "<hr>";
    echo "<p><small>You can delete this file after setting up your admin account.</small></p>";
    
} else {
    // Show setup form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>WordPress Admin Setup</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .setup-box { background: #f1f1f1; padding: 20px; border-radius: 5px; margin: 20px 0; }
            .button { background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; display: inline-block; }
        </style>
    </head>
    <body>
        <h1>WordPress Admin Setup</h1>
        
        <div class="setup-box">
            <h2>🔧 Fix Wasmer Admin Access</h2>
            <p>This script will create a WordPress admin user to bypass Wasmer's authentication system.</p>
            
            <p><strong>What this will do:</strong></p>
            <ul>
                <li>Create an admin user with username: <code>admin</code></li>
                <li>Set password to: <code>admin123</code></li>
                <li>Enable standard WordPress login</li>
            </ul>
            
            <p><a href="?setup_admin=true" class="button">Create Admin User</a></p>
        </div>
        
        <div class="setup-box">
            <h2>📋 Manual Steps (if needed)</h2>
            <p>If the automatic setup doesn't work, you can manually access WordPress admin:</p>
            <ol>
                <li>Go to: <code>https://your-app.wasmer.app/wp-admin/</code></li>
                <li>Use username: <code>admin</code></li>
                <li>Use password: <code>admin123</code></li>
            </ol>
        </div>
        
        <div class="setup-box">
            <h2>🎨 Lottie Performance Test Theme</h2>
            <p>Once logged in, activate the "Lottie Performance Test" theme and create pages using these templates:</p>
            <ul>
                <li><code>page-global.php</code> - CDN Global Mode</li>
                <li><code>page-defer.php</code> - Local Deferred Mode</li>
                <li><code>page-lazy.php</code> - Lazy Loading Mode</li>
                <li><code>page-canvas.php</code> - Canvas Renderer Mode</li>
            </ul>
        </div>
    </body>
    </html>
    <?php
}
?>
