<?php
/**
 * Theme Name: Lottie Performance Test
 * Description: Tipalti Finance AI replica with 4 Lottie integration strategies for performance testing
 * Version: 1.0.0
 * Author: Performance Test Team
 * Text Domain: lottie-perf-test
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme setup
function lottie_perf_test_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
}
add_action('after_setup_theme', 'lottie_perf_test_setup');

// Critical CSS Inline Function
function lottie_perf_test_critical_css() {
    $template = get_page_template_slug();
    $lottie_templates = array(
        'page-local-test.php',
        'page-canvas-mode-test.php', 
        'page-defer-test.php',
        'page-lazy-test.php',
        'page-cache-test.php',
        'page-conditional-test.php',
        'page-poster-test.php',
        'page-home.php'
    );
    
    if (in_array($template, $lottie_templates)) {
        $critical_css_path = get_template_directory() . '/assets/css/critical.css';
        if (file_exists($critical_css_path)) {
            echo '<style id="critical-css">' . file_get_contents($critical_css_path) . '</style>';
        }
    }
}
add_action('wp_head', 'lottie_perf_test_critical_css', 1);

// Non-Critical CSS with media="print" + onload trick
function lottie_perf_test_non_critical_css() {
    $template = get_page_template_slug();
    $lottie_templates = array(
        'page-local-test.php',
        'page-canvas-mode-test.php', 
        'page-defer-test.php',
        'page-lazy-test.php',
        'page-cache-test.php',
        'page-conditional-test.php',
        'page-poster-test.php',
        'page-home.php'
    );
    
    if (in_array($template, $lottie_templates)) {
        $non_critical_css_url = get_template_directory_uri() . '/assets/css/non-critical.css';
        echo '<link rel="stylesheet" href="' . esc_url($non_critical_css_url) . '" media="print" onload="this.media=\'all\'">';
        echo '<noscript><link rel="stylesheet" href="' . esc_url($non_critical_css_url) . '"></noscript>';
    }
}
add_action('wp_head', 'lottie_perf_test_non_critical_css', 2);

// 7-Step Lottie Performance Optimization Scripts
function lottie_perf_test_scripts() {
    // Get current page template
    $template = get_page_template_slug();
    
    // Define all Lottie test page templates
    $lottie_templates = array(
        'page-local-test.php',
        'page-canvas-mode-test.php', 
        'page-defer-test.php',
        'page-lazy-test.php',
        'page-cache-test.php',
        'page-conditional-test.php',
        'page-poster-test.php',
        'page-home.php'
    );
    
    if (in_array($template, $lottie_templates)) {
        // CSS loading is now handled by critical CSS inline and non-critical CSS with media="print" trick
        // No need to enqueue CSS files here anymore
        
        // Step 1: Local Player File (Basic) - OPTIMIZED with module loading
        if ($template === 'page-local-test.php') {
            // Load self-hosted Lottie library first
            add_action('wp_head', function() {
                echo '<script src="' . get_template_directory_uri() . '/assets/js/lottie-light.js?ver=1.0.0"></script>';
            }, 3);            
        }
        
        // Step 2: Local Player File + Canvas Renderer - OPTIMIZED with module loading
        elseif ($template === 'page-canvas-mode-test.php') {
            // Load ES module with proper type attribute
            add_action('wp_head', function() {
                echo '<script type="module" src="' . get_template_directory_uri() . '/assets/js/dotlottie-player-correct.mjs?ver=1.0.0" async></script>';
            }, 5);
            wp_enqueue_script('lottie-step2-canvas', get_template_directory_uri() . '/assets/js/lottie-step2-canvas.js', array(), '1.0.0', true);
            wp_script_add_data('lottie-step2-canvas', 'async', true);
        }
        
        // Step 3: Step 2 + Deferred/Async Load Method - OPTIMIZED with module loading
        elseif ($template === 'page-defer-test.php') {
            // Load ES module with proper type attribute
            add_action('wp_head', function() {
                echo '<script type="module" src="' . get_template_directory_uri() . '/assets/js/dotlottie-player-correct.mjs?ver=1.0.0" async></script>';
            }, 5);
            wp_enqueue_script('lottie-step3-defer', get_template_directory_uri() . '/assets/js/lottie-step3-defer.js', array(), '1.0.0', true);
            wp_script_add_data('lottie-step3-defer', 'async', true);
        }
        
        // Step 4: Step 3 + Lazy Loading with IntersectionObserver - OPTIMIZED with module loading
        elseif ($template === 'page-lazy-test.php') {
            // Load ES module with proper type attribute
            add_action('wp_head', function() {
                echo '<script type="module" src="' . get_template_directory_uri() . '/assets/js/dotlottie-player-correct.mjs?ver=1.0.0" async></script>';
            }, 5);
            wp_enqueue_script('lottie-step4-lazy', get_template_directory_uri() . '/assets/js/lottie-step4-lazy.js', array(), '1.0.0', true);
            wp_script_add_data('lottie-step4-lazy', 'async', true);
        }
        
        // Step 5: Step 4 + Asset Compression & Caching (Brotli + Cache-Control) - OPTIMIZED with module loading
        elseif ($template === 'page-cache-test.php') {
            // Load ES module with proper type attribute
            add_action('wp_head', function() {
                echo '<script type="module" src="' . get_template_directory_uri() . '/assets/js/dotlottie-player-correct.mjs?ver=1.0.0" async></script>';
            }, 5);
            wp_enqueue_script('lottie-step5-cache', get_template_directory_uri() . '/assets/js/lottie-step5-cache.js', array(), '1.0.0', true);
            wp_script_add_data('lottie-step5-cache', 'async', true);
        }
        
        // Step 6: Step 5 + Conditional Enqueue (Per-Page) - OPTIMIZED with module loading
        elseif ($template === 'page-conditional-test.php') {
            // Load ES module with proper type attribute
            add_action('wp_head', function() {
                echo '<script type="module" src="' . get_template_directory_uri() . '/assets/js/dotlottie-player-correct.mjs?ver=1.0.0" async></script>';
            }, 5);
            wp_enqueue_script('lottie-step6-conditional', get_template_directory_uri() . '/assets/js/lottie-step6-conditional.js', array(), '1.0.0', true);
            wp_script_add_data('lottie-step6-conditional', 'async', true);
        }
        
        // Step 7: Step 6 + Poster Fallback for Below-Fold Animations - OPTIMIZED with module loading
        elseif ($template === 'page-poster-test.php') {
            // Load ES module with proper type attribute
            add_action('wp_head', function() {
                echo '<script type="module" src="' . get_template_directory_uri() . '/assets/js/dotlottie-player-correct.mjs?ver=1.0.0" async></script>';
            }, 5);
            wp_enqueue_script('lottie-step7-poster', get_template_directory_uri() . '/assets/js/lottie-step7-poster.js', array(), '1.0.0', true);
            wp_script_add_data('lottie-step7-poster', 'async', true);
        }
    }
}
add_action('wp_enqueue_scripts', 'lottie_perf_test_scripts');

// Strategy 5: Local Hosting with Long-term Caching
function lottie_perf_test_add_caching_headers() {
    // Only apply to Lottie files and only if we're in a WordPress context
    if (defined('ABSPATH') && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Only apply to Lottie files
        if (strpos($request_uri, '.lottie') !== false || 
            strpos($request_uri, 'dotlottie-player-correct.mjs') !== false) {
            
            // Check if file exists before trying to get ETag
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $request_uri;
            if (file_exists($file_path)) {
                // Set long-term caching headers
                header('Cache-Control: public, max-age=31536000, immutable'); // 1 year
                header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
                header('ETag: "' . md5_file($file_path) . '"');
                
                // Enable compression
                if (extension_loaded('zlib') && !ob_get_level()) {
                    ob_start('ob_gzhandler');
                }
            }
        }
    }
}
add_action('init', 'lottie_perf_test_add_caching_headers');

// Handle static file serving to prevent WordPress from processing them
function lottie_perf_test_handle_static_files() {
    if (isset($_SERVER['REQUEST_URI'])) {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Check if this is a request for static assets
        if (preg_match('/\.(css|js|mjs|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot|lottie|dotlottie)$/', $request_uri)) {
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $request_uri;
            
            // If file exists, serve it directly
            if (file_exists($file_path)) {
                // Set proper headers
                $mime_types = array(
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'mjs' => 'application/javascript',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2',
                    'ttf' => 'font/ttf',
                    'eot' => 'font/eot',
                    'lottie' => 'application/json',
                    'dotlottie' => 'application/json'
                );
                
                $extension = pathinfo($file_path, PATHINFO_EXTENSION);
                if (isset($mime_types[$extension])) {
                    header('Content-Type: ' . $mime_types[$extension]);
                }
                
                // Set caching headers
                header('Cache-Control: public, max-age=31536000, immutable');
                header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
                
                // Output file content
                readfile($file_path);
                exit;
            }
        }
    }
}
add_action('init', 'lottie_perf_test_handle_static_files', 1);

    // Add performance optimizations
    function lottie_perf_test_performance_optimizations() {
        // Only add performance optimizations if we're not serving static files
        if (!isset($_SERVER['REQUEST_URI']) || 
            (strpos($_SERVER['REQUEST_URI'], '.css') === false && 
             strpos($_SERVER['REQUEST_URI'], '.js') === false && 
             strpos($_SERVER['REQUEST_URI'], '.mjs') === false)) {
            
            // Add preconnect hints for external resources
            echo '<link rel="preconnect" href="https://unpkg.com" crossorigin>';
            echo '<link rel="dns-prefetch" href="https://unpkg.com">';
            
            // Add resource hints for local assets
            echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/css/non-critical.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
            
            // Add compression headers only for HTML pages
            if (!headers_sent()) {
                header('Vary: Accept-Encoding');
                header('Cache-Control: public, max-age=3600'); // 1 hour cache
                if (extension_loaded('zlib') && !ob_get_level()) {
                    ob_start('ob_gzhandler');
                }
            }
        }
    }
add_action('wp_head', 'lottie_perf_test_performance_optimizations', 1);

// Fix WordPress Lottie file support
function lottie_perf_test_add_lottie_support($mimes) {
    $mimes['lottie'] = 'application/json';
    $mimes['dotlottie'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'lottie_perf_test_add_lottie_support');

// Allow Lottie files in uploads
function lottie_perf_test_allow_lottie_uploads($file) {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if ($ext === 'lottie' || $ext === 'dotlottie') {
        $file['type'] = 'application/json';
        $file['ext'] = $ext;
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'lottie_perf_test_allow_lottie_uploads');

// Add Lottie player script to head for global mode
function lottie_perf_test_head_scripts() {
    $template = get_page_template_slug();
    if ($template === 'page-global.php') {
        echo '<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>';
    }
}
add_action('wp_head', 'lottie_perf_test_head_scripts');

// Register navigation menus
function lottie_perf_test_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'lottie-perf-test'),
        'footer' => __('Footer Menu', 'lottie-perf-test'),
    ));
}
add_action('init', 'lottie_perf_test_menus');

// Add custom post types for performance test pages
function lottie_perf_test_post_types() {
    register_post_type('performance_test', array(
        'labels' => array(
            'name' => 'Performance Tests',
            'singular_name' => 'Performance Test',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-performance',
    ));
}
add_action('init', 'lottie_perf_test_post_types');

// Add custom fields for performance metrics
function lottie_perf_test_meta_boxes() {
    add_meta_box(
        'performance_metrics',
        'Performance Metrics',
        'lottie_perf_test_metrics_callback',
        'performance_test',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'lottie_perf_test_meta_boxes');

function lottie_perf_test_metrics_callback($post) {
    wp_nonce_field('lottie_perf_test_metrics', 'lottie_perf_test_metrics_nonce');
    
    $performance_score = get_post_meta($post->ID, '_performance_score', true);
    $lcp = get_post_meta($post->ID, '_lcp', true);
    $tbt = get_post_meta($post->ID, '_tbt', true);
    $integration_mode = get_post_meta($post->ID, '_integration_mode', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="integration_mode">Integration Mode</label></th>';
    echo '<td><select name="integration_mode" id="integration_mode">';
    echo '<option value="global"' . selected($integration_mode, 'global', false) . '>Global CDN</option>';
    echo '<option value="defer"' . selected($integration_mode, 'defer', false) . '>Deferred Local</option>';
    echo '<option value="lazy"' . selected($integration_mode, 'lazy', false) . '>Lazy Loading</option>';
    echo '<option value="canvas"' . selected($integration_mode, 'canvas', false) . '>Canvas Renderer</option>';
    echo '</select></td></tr>';
    echo '<tr><th><label for="performance_score">Performance Score</label></th>';
    echo '<td><input type="number" name="performance_score" id="performance_score" value="' . esc_attr($performance_score) . '" /></td></tr>';
    echo '<tr><th><label for="lcp">LCP (seconds)</label></th>';
    echo '<td><input type="number" step="0.1" name="lcp" id="lcp" value="' . esc_attr($lcp) . '" /></td></tr>';
    echo '<tr><th><label for="tbt">TBT (ms)</label></th>';
    echo '<td><input type="number" name="tbt" id="tbt" value="' . esc_attr($tbt) . '" /></td></tr>';
    echo '</table>';
}

// Save custom fields
function lottie_perf_test_save_meta($post_id) {
    if (!isset($_POST['lottie_perf_test_metrics_nonce']) || !wp_verify_nonce($_POST['lottie_perf_test_metrics_nonce'], 'lottie_perf_test_metrics')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['integration_mode'])) {
        update_post_meta($post_id, '_integration_mode', sanitize_text_field($_POST['integration_mode']));
    }
    if (isset($_POST['performance_score'])) {
        update_post_meta($post_id, '_performance_score', intval($_POST['performance_score']));
    }
    if (isset($_POST['lcp'])) {
        update_post_meta($post_id, '_lcp', floatval($_POST['lcp']));
    }
    if (isset($_POST['tbt'])) {
        update_post_meta($post_id, '_tbt', intval($_POST['tbt']));
    }
}
add_action('save_post', 'lottie_perf_test_save_meta');

// Add admin menu for performance dashboard
function lottie_perf_test_admin_menu() {
    add_menu_page(
        'Performance Dashboard',
        'Performance',
        'manage_options',
        'lottie-performance',
        'lottie_perf_test_dashboard',
        'dashicons-performance',
        30
    );
}
add_action('admin_menu', 'lottie_perf_test_admin_menu');

// Include Lottie content functionality
require_once get_template_directory() . '/lottie-content.php';

// Fix Wasmer admin access
function fix_wasmer_admin_access() {
    // Remove Wasmer magic login redirects
    remove_action('init', 'wasmer_magic_login');
    remove_action('wp_ajax_wasmer_magic_login', 'wasmer_magic_login_handler');
    remove_action('wp_ajax_nopriv_wasmer_magic_login', 'wasmer_magic_login_handler');
    
    // Ensure standard WordPress login works
    if (is_admin() && !is_user_logged_in()) {
        // Allow access to wp-login.php
        if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
            return;
        }
    }
}
add_action('init', 'fix_wasmer_admin_access', 1);

function lottie_perf_test_dashboard() {
    $tests = get_posts(array(
        'post_type' => 'performance_test',
        'posts_per_page' => -1,
        'meta_key' => '_performance_score',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    ));
    
    echo '<div class="wrap">';
    echo '<h1>Lottie Performance Dashboard</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Test Name</th><th>Integration Mode</th><th>Performance Score</th><th>LCP</th><th>TBT</th><th>Actions</th></tr></thead>';
    echo '<tbody>';
    
    foreach ($tests as $test) {
        $mode = get_post_meta($test->ID, '_integration_mode', true);
        $score = get_post_meta($test->ID, '_performance_score', true);
        $lcp = get_post_meta($test->ID, '_lcp', true);
        $tbt = get_post_meta($test->ID, '_tbt', true);
        
        echo '<tr>';
        echo '<td>' . esc_html($test->post_title) . '</td>';
        echo '<td>' . esc_html(ucfirst($mode)) . '</td>';
        echo '<td>' . esc_html($score) . '</td>';
        echo '<td>' . esc_html($lcp) . 's</td>';
        echo '<td>' . esc_html($tbt) . 'ms</td>';
        echo '<td><a href="' . get_edit_post_link($test->ID) . '">Edit</a></td>';
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '</div>';
}
