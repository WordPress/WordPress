<?php
/**
 * Add Lottie support to WordPress posts and pages
 */

// Add Lottie field to posts and pages
function add_lottie_meta_box() {
    add_meta_box(
        'lottie_animation',
        'Lottie Animation',
        'lottie_meta_box_callback',
        array('post', 'page'),
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_lottie_meta_box');

function lottie_meta_box_callback($post) {
    wp_nonce_field('lottie_meta_box', 'lottie_meta_box_nonce');
    
    $lottie_file = get_post_meta($post->ID, '_lottie_file', true);
    $lottie_width = get_post_meta($post->ID, '_lottie_width', true) ?: '300';
    $lottie_height = get_post_meta($post->ID, '_lottie_height', true) ?: '300';
    $lottie_autoplay = get_post_meta($post->ID, '_lottie_autoplay', true);
    $lottie_loop = get_post_meta($post->ID, '_lottie_loop', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="lottie_file">Lottie File URL</label></th>';
    echo '<td><input type="url" name="lottie_file" id="lottie_file" value="' . esc_attr($lottie_file) . '" style="width: 100%;" placeholder="https://example.com/animation.lottie" /></td></tr>';
    
    echo '<tr><th><label for="lottie_width">Width (px)</label></th>';
    echo '<td><input type="number" name="lottie_width" id="lottie_width" value="' . esc_attr($lottie_width) . '" /></td></tr>';
    
    echo '<tr><th><label for="lottie_height">Height (px)</label></th>';
    echo '<td><input type="number" name="lottie_height" id="lottie_height" value="' . esc_attr($lottie_height) . '" /></td></tr>';
    
    echo '<tr><th><label for="lottie_autoplay">Autoplay</label></th>';
    echo '<td><input type="checkbox" name="lottie_autoplay" id="lottie_autoplay" value="1" ' . checked($lottie_autoplay, 1, false) . ' /></td></tr>';
    
    echo '<tr><th><label for="lottie_loop">Loop</label></th>';
    echo '<td><input type="checkbox" name="lottie_loop" id="lottie_loop" value="1" ' . checked($lottie_loop, 1, false) . ' /></td></tr>';
    echo '</table>';
}

function save_lottie_meta_box($post_id) {
    if (!isset($_POST['lottie_meta_box_nonce']) || !wp_verify_nonce($_POST['lottie_meta_box_nonce'], 'lottie_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['lottie_file'])) {
        update_post_meta($post_id, '_lottie_file', sanitize_url($_POST['lottie_file']));
    }
    if (isset($_POST['lottie_width'])) {
        update_post_meta($post_id, '_lottie_width', intval($_POST['lottie_width']));
    }
    if (isset($_POST['lottie_height'])) {
        update_post_meta($post_id, '_lottie_height', intval($_POST['lottie_height']));
    }
    if (isset($_POST['lottie_autoplay'])) {
        update_post_meta($post_id, '_lottie_autoplay', 1);
    } else {
        delete_post_meta($post_id, '_lottie_autoplay');
    }
    if (isset($_POST['lottie_loop'])) {
        update_post_meta($post_id, '_lottie_loop', 1);
    } else {
        delete_post_meta($post_id, '_lottie_loop');
    }
}
add_action('save_post', 'save_lottie_meta_box');

// Shortcode to display Lottie animations
function lottie_shortcode($atts) {
    $atts = shortcode_atts(array(
        'file' => '',
        'width' => '300',
        'height' => '300',
        'autoplay' => 'true',
        'loop' => 'true',
        'renderer' => 'svg'
    ), $atts);
    
    if (empty($atts['file'])) {
        return '<p>Error: No Lottie file specified</p>';
    }
    
    $autoplay = $atts['autoplay'] === 'true' ? 'autoplay' : '';
    $loop = $atts['loop'] === 'true' ? 'loop' : '';
    
    return sprintf(
        '<dotlottie-player src="%s" %s %s renderer="%s" style="width: %spx; height: %spx;"></dotlottie-player>',
        esc_url($atts['file']),
        $autoplay,
        $loop,
        esc_attr($atts['renderer']),
        esc_attr($atts['width']),
        esc_attr($atts['height'])
    );
}
add_shortcode('lottie', 'lottie_shortcode');

// Add Lottie player script to head
function enqueue_lottie_player() {
    echo '<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>';
}
add_action('wp_head', 'enqueue_lottie_player');
