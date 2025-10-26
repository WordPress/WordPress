<?php
/**
 * WordPress Password Reset Script
 * Run this to reset the admin password
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Check if we're running this script
if (isset($_GET['reset_password']) && $_GET['reset_password'] === 'true') {
    
    // Get the admin user
    $admin_user = get_user_by('login', 'auraxpro');
    
    if ($admin_user) {
        // Set new password
        $new_password = 'admin123';
        wp_set_password($new_password, $admin_user->ID);
        
        echo "<h2>✅ Password Reset Successfully!</h2>";
        echo "<p><strong>Username:</strong> auraxpro</p>";
        echo "<p><strong>New Password:</strong> admin123</p>";
        echo "<p><strong>Email:</strong> " . $admin_user->user_email . "</p>";
        echo "<hr>";
        echo "<p><a href='/wp-admin/' style='background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;'>Go to WordPress Admin</a></p>";
        
        // Also create a backup admin user
        $backup_user = get_user_by('login', 'admin');
        if (!$backup_user) {
            $user_id = wp_create_user('admin', 'admin123', 'admin@example.com');
            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role('administrator');
                echo "<hr>";
                echo "<h3>🔧 Backup Admin User Created:</h3>";
                echo "<p><strong>Username:</strong> admin</p>";
                echo "<p><strong>Password:</strong> admin123</p>";
            }
        }
        
    } else {
        echo "<h2>❌ User 'auraxpro' not found!</h2>";
        echo "<p>Creating new admin user...</p>";
        
        $user_id = wp_create_user('admin', 'admin123', 'admin@example.com');
        if (!is_wp_error($user_id)) {
            $user = new WP_User($user_id);
            $user->set_role('administrator');
            
            echo "<h2>✅ New Admin User Created!</h2>";
            echo "<p><strong>Username:</strong> admin</p>";
            echo "<p><strong>Password:</strong> admin123</p>";
            echo "<p><strong>Email:</strong> admin@example.com</p>";
            echo "<hr>";
            echo "<p><a href='/wp-admin/' style='background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;'>Go to WordPress Admin</a></p>";
        } else {
            echo "<h2>❌ Error creating admin user:</h2>";
            echo "<p>" . $user_id->get_error_message() . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<p><small>You can delete this file after resetting your password.</small></p>";
    
} else {
    // Show reset form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>WordPress Password Reset</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .reset-box { background: #f1f1f1; padding: 20px; border-radius: 5px; margin: 20px 0; }
            .button { background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; display: inline-block; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <h1>WordPress Password Reset</h1>
        
        <div class="warning">
            <h3>⚠️ Password Issue Detected</h3>
            <p>The password in the database is hashed and not readable. This script will reset it to a working password.</p>
        </div>
        
        <div class="reset-box">
            <h2>🔧 Reset Admin Password</h2>
            <p>This will reset the password for user <strong>auraxpro</strong> to a simple, working password.</p>
            
            <p><strong>What this will do:</strong></p>
            <ul>
                <li>Reset password for user: <code>auraxpro</code></li>
                <li>Set new password to: <code>admin123</code></li>
                <li>Create backup admin user: <code>admin</code> / <code>admin123</code></li>
            </ul>
            
            <p><a href="?reset_password=true" class="button">Reset Password</a></p>
        </div>
        
        <div class="reset-box">
            <h2>📋 After Reset</h2>
            <p>Use these credentials to login:</p>
            <ul>
                <li><strong>Username:</strong> auraxpro</li>
                <li><strong>Password:</strong> admin123</li>
            </ul>
            <p>Or use the backup admin:</p>
            <ul>
                <li><strong>Username:</strong> admin</li>
                <li><strong>Password:</strong> admin123</li>
            </ul>
        </div>
    </body>
    </html>
    <?php
}
?>
