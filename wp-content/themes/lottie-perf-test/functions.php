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
        add_action('wp_head', function() {
            $base_uri = get_template_directory_uri() . '/assets/js/';
            $base_path = get_template_directory() . '/assets/js/';
            $final_file = 'lottie-light.final.min.js';
            $min_file = 'lottie-light.min.js';
            $dev_file = 'lottie-light.js';
            
            // Use minimal lottie implementation (only 5KB vs 422KB)
            $script = 'lottie-minimal.js';
            
            // Preload the JavaScript file
            echo '<link rel="preload" href="' . esc_url($base_uri . $script) . '?ver=1.0.0" as="script" crossorigin>';
            
            // Load minimal Lottie implementation (5KB vs 422KB)
            echo '<script src="' . esc_url($base_uri . $script) . '?ver=1.0.0" defer crossorigin></script>';
        }, 3);
    }
}
add_action('wp_enqueue_scripts', 'lottie_perf_test_scripts');

// Enqueue only required theme styles (fonts handled locally in head)
function page_styles_enqueue() {
    $dist_dir  = get_template_directory() . '/assets/dist/css/';
    $dist_uri  = get_template_directory_uri() . '/assets/dist/css/';

    // Main compiled stylesheet (minified) - load first
    if (file_exists($dist_dir . 'main.style.min.css')) {
        $ver = filemtime($dist_dir . 'main.style.min.css');
        wp_enqueue_style('lpt-main', $dist_uri . 'main.style.min.css', array(), $ver);
    } elseif (file_exists($dist_dir . 'main.css')) {
        // Fallback if unminified exists
        $ver = filemtime($dist_dir . 'main.css');
        wp_enqueue_style('lpt-main', $dist_uri . 'main.css', array(), $ver);
    }

    // Accordion tab slider styles
    $acc_path = get_template_directory() . '/assets/dist/css/accordion-tabsliderview.min.css';
    if (file_exists($acc_path)) {
        $ver = filemtime($acc_path);
        wp_enqueue_style('lpt-accordion', get_template_directory_uri() . '/assets/dist/css/accordion-tabsliderview.min.css', array('lpt-main'), $ver);
    }
    
    // Additional CSS files if they exist
    $additional_css = array('mega-menu-style.min.css','footer.min.css', 'style.min.css', 'blog-filter-style.min.css', 'cardRenderer.min.css', 'carousel.min.css', 'customer-cards-desktop.min.css', 'testimonial-card-mobile.min.css', 'customer-cards-mobile.min.css');
    foreach ($additional_css as $css_file) {
        if (file_exists($dist_dir . $css_file)) {
            $ver = filemtime($dist_dir . $css_file);
            $handle = 'lpt-' . str_replace('.min.css', '', $css_file);
            wp_enqueue_style($handle, $dist_uri . $css_file, array('lpt-main'), $ver);
        }
    }
}
add_action('wp_enqueue_scripts', 'page_styles_enqueue', 20);

// Add debugging and critical accordion layout fixes
add_action('wp_head', function() {
    // Debug: Check if CSS files are being loaded (only for admins)
    if (current_user_can('administrator')) {
        echo '<!-- CSS Debug: Main CSS loaded: ' . (wp_style_is('lpt-main', 'enqueued') ? 'YES' : 'NO') . ' -->';
        echo '<!-- CSS Debug: Accordion CSS loaded: ' . (wp_style_is('lpt-accordion', 'enqueued') ? 'YES' : 'NO') . ' -->';
        echo '<!-- JS Debug: Lottie script loaded: ' . (wp_script_is('lottie-minimal', 'enqueued') ? 'YES' : 'NO') . ' -->';
    }
    
    echo '<style>
    /* Critical accordion layout fixes */
    .accordion-tab__slider-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        display: flex !important;
        flex-direction: row !important;
        align-items: center;
        gap: var(--wp--custom--min-24-max-80, 80px);
    }
    
    .accordion-tab__slider-wrapper.is-row-reverse {
        flex-direction: row-reverse !important;
    }
    
    .accordion-tab__slider-wrapper .slider-left {
        flex: 1;
        padding: 0 0 0 var(--wp--custom--min-24-max-64, 64px);
    }
    
    .accordion-tab__slider-wrapper .slider-right {
        flex: 1;
        padding-bottom: 0;
    }
    
    .accordion-tab__slider-wrapper .pagination-item h3 {
        display: block !important;
        color: var(--wp--preset--color--synergy-gunmetal, #6c6c6c);
        cursor: pointer;
        margin: 0;
        padding: 15px 0 0 0;
        transition: all 0.4s ease-in-out;
        font-size: 1.25rem;
        font-weight: 500;
    }
    
    .accordion-tab__slider-wrapper .pagination-item h3:hover {
        color: var(--wp--preset--color--synergy-onyx, #141414);
    }
    
    .accordion-tab__slider-wrapper .pagination-item.active-item h3 {
        color: var(--wp--preset--color--synergy-onyx, #141414);
    }
    
    .accordion-tab__slider-wrapper .info-slide {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: all 0.4s ease-in-out;
        padding-bottom: 12px;
    }
    
    .accordion-tab__slider-wrapper .info-slide.active-info-item {
        max-height: 500px;
        opacity: 1;
    }
    
    .accordion-tab__slider-wrapper .info-slide p {
        margin: 0;
        color: var(--wp--preset--color--synergy-gunmetal, #6c6c6c);
        line-height: 1.6;
    }
    
    .accordion-tab__slider-wrapper .media-slide {
        opacity: 0;
        transition: all 0.4s ease-in-out;
        width: 100%;
    }
    
    .accordion-tab__slider-wrapper .media-slide.active-item {
        opacity: 1;
        display: block;
    }
    
    .accordion-tab__slider-wrapper .media-slide img {
        border-radius: var(--wp--custom--min-24-max-40, 40px);
        height: 100%;
        object-fit: cover;
        width: 100%;
    }
    
    /* Mobile responsive */
    @media (max-width: 961px) {
        .accordion-tab__slider-wrapper {
            flex-direction: column !important;
        }
        
        .accordion-tab__slider-wrapper .slider-left {
            order: 2;
            padding: 12px 0 0 0;
        }
        
        .accordion-tab__slider-wrapper .slider-right {
            order: 1;
            padding-bottom: 24px;
        }
        
        .accordion-tab__slider-wrapper .info-slide p {
            padding-bottom: 44px;
        }
    }
    </style>';
    
    // Add Lottie initialization script
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize all Lottie players
        const lottiePlayers = document.querySelectorAll("dotlottie-player");
        console.log("Found " + lottiePlayers.length + " Lottie players");
        
        lottiePlayers.forEach(function(player, index) {
            console.log("Initializing Lottie player " + index + ":", player.src);
            
            // Ensure autoplay is enabled
            if (player.hasAttribute("autoplay")) {
                player.setAttribute("autoplay", "true");
            }
            
            // Add event listeners for debugging
            player.addEventListener("ready", function() {
                console.log("Lottie player " + index + " is ready");
            });
            
            player.addEventListener("play", function() {
                console.log("Lottie player " + index + " started playing");
            });
            
            player.addEventListener("error", function(e) {
                console.error("Lottie player " + index + " error:", e);
            });
        });
    });
    </script>';
}, 25);

// Enqueue accordion tab slider JavaScript
function lottie_perf_test_enqueue_accordion_scripts() {
    if (is_page('local-test')) {
        $js_base = get_template_directory_uri() . '/assets/accordion-tab-slider/';
        
        // Enqueue accordion view script
        if (file_exists(get_template_directory() . '/assets/accordion-tab-slider/view.min.js')) {
            wp_enqueue_script('lpt-accordion-view', $js_base . 'view.min.js', array(), '1.0.0', true);
        }
        
        // Enqueue accordion tab slider view script
        if (file_exists(get_template_directory() . '/assets/accordion-tab-slider/AccordionTabSliderView.min.js')) {
            wp_enqueue_script('lpt-accordion-tab-slider', $js_base . 'AccordionTabSliderView.min.js', array('lpt-accordion-view'), '1.0.0', true);
        }
    }
}
add_action('wp_enqueue_scripts', 'lottie_perf_test_enqueue_accordion_scripts', 30);

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

                // Prefer serving precompressed variants when available and accepted
                $accept_encoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
                $served_compressed = false;
                if (in_array($extension, array('css','js','mjs'))) {
                    header('Vary: Accept-Encoding');
                    if (strpos($accept_encoding, 'br') !== false && file_exists($file_path . '.br')) {
                        header('Content-Encoding: br');
                        header('Cache-Control: public, max-age=31536000, immutable');
                        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
                        readfile($file_path . '.br');
                        $served_compressed = true;
                    } elseif (strpos($accept_encoding, 'gzip') !== false && file_exists($file_path . '.gz')) {
                        header('Content-Encoding: gzip');
                        header('Cache-Control: public, max-age=31536000, immutable');
                        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
                        readfile($file_path . '.gz');
                        $served_compressed = true;
                    }
                }
                if ($served_compressed) {
                    exit;
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
            
            // No deferred CSS preload — we rely on main.css + mega-menu style only.
            
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

// Add resource hints as early as possible in <head>
function lottie_perf_test_resource_hints() {
    // External preconnects for better performance
    echo '<link rel="preconnect" href="https://wordpress-l92nz.wasmer.app" crossorigin>';
    echo '<link rel="preconnect" href="https://f.vimeocdn.com" crossorigin>';
    echo '<link rel="preconnect" href="https://player.vimeo.com" crossorigin>';
    echo '<link rel="preconnect" href="https://vumbnail.com" crossorigin>';
    // Preload local font files (Inter Regular 400, Medium 500)
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/font/Inter-Regular-subset-v1.1.0.woff2" as="font" type="font/woff2" crossorigin>';
    echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/font/Inter-Medium-subset-v1.1.0.woff2" as="font" type="font/woff2" crossorigin>';
    
    // Preload critical Vimeo thumbnails for Speed Index
    echo '<link rel="preload" href="https://vumbnail.com/1121254619.jpg" as="image">';
    echo '<link rel="preload" href="https://vumbnail.com/1118182888.jpg" as="image">';
    
    // Local @font-face to avoid external font CSS
    echo '<style>
        @font-face {
            font-family: "Inter";
            src: url("' . get_template_directory_uri() . '/assets/font/Inter-Regular-subset-v1.1.0.woff2") format("woff2");
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: "Inter";
            src: url("' . get_template_directory_uri() . '/assets/font/Inter-Medium-subset-v1.1.0.woff2") format("woff2");
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }
        body { font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>';
    
    // Preload only the first critical Lottie animation to prevent CLS
    $template = get_page_template_slug();
    if ($template === 'page-local-test.php') {
        echo '<link rel="preload" href="' . get_template_directory_uri() . '/assets/lottie/invoice-capture-agent-1.lottie" as="fetch" crossorigin>';
    }
}
add_action('wp_head', 'lottie_perf_test_resource_hints', 0);

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

// Note: Using Tipalti's lottie-light.js which includes dotlottie-player component
// No additional script loading needed for local-test.php

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
