<?php
/**
 * Move Lottie files to WordPress uploads directory
 * Run this once to fix the file locations
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Get uploads directory
$upload_dir = wp_upload_dir();
$lottie_dir = $upload_dir['basedir'] . '/lottie/';

// Create lottie directory if it doesn't exist
if (!file_exists($lottie_dir)) {
    wp_mkdir_p($lottie_dir);
}

// Source directory
$source_dir = get_template_directory() . '/assets/lottie/';

// List of Lottie files
$lottie_files = [
    'approval-chains-and-audit-trail.lottie',
    'bill-approvers-agent.lottie',
    'duplicate-bill-detection.lottie',
    'erp-sync-resolution-agent.lottie',
    'invoice-capture-agent-1.lottie',
    'invoice-capture-agent-2.lottie',
    'po-matching-agent.lottie',
    'po-request-agent.lottie',
    'scan-expenses-receipt-agent.lottie',
    'two-and-three-way-po-matching.lottie'
];

echo "<h2>Moving Lottie Files to WordPress Uploads</h2>";

foreach ($lottie_files as $file) {
    $source_path = $source_dir . $file;
    $dest_path = $lottie_dir . $file;
    
    if (file_exists($source_path)) {
        if (copy($source_path, $dest_path)) {
            echo "<p>✅ Moved: $file</p>";
        } else {
            echo "<p>❌ Failed to move: $file</p>";
        }
    } else {
        echo "<p>⚠️ Source file not found: $file</p>";
    }
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Go to WordPress Admin → Media → Library</li>";
echo "<li>Click 'Add Media File'</li>";
echo "<li>Upload the Lottie files from: " . $lottie_dir . "</li>";
echo "<li>Or use the files directly from the theme directory</li>";
echo "</ol>";

echo "<h3>File URLs:</h3>";
echo "<p>Lottie files are now available at:</p>";
echo "<ul>";
foreach ($lottie_files as $file) {
    $url = $upload_dir['baseurl'] . '/lottie/' . $file;
    echo "<li><a href='$url' target='_blank'>$file</a></li>";
}
echo "</ul>";
?>
