<?php
/*
  Plugin Name: WP Slider
  Description: A multi-effect Wordpress slider based on jQuery Cycle Plugin. 
  Author: Fractalia - Applications lab
  Author URI: http://fractalia.pe
  Version: 0.4.1
  Tags: fractalia, wordpress, jquery, slider, animated, animation, wp

 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

if (!class_exists('wp_slider')) {

    class wp_slider {

        var $effects = array(
            'blindX',
            'blindY',
            'blindZ',
            'cover',
            'curtainX',
            'curtainY',
            'fade',
            'fadeZoom',
            'growX',
            'growY',
            'scrollUp',
            'scrollDown',
            'scrollLeft',
            'scrollRight',
            'scrollHorz',
            'scrollVert',
            'shuffle',
            'slideX',
            'slideY',
            'toss',
            'turnUp',
            'turnDown',
            'turnLeft',
            'turnRight',
            'uncover',
            'wipe',
            'zoom'
        );
        var $easings = array(
            'easein',
            'easeinout',
            'easeout',
            'expoin',
            'expoout',
            'expoinout',
            'bouncein',
            'bounceout',
            'bounceinout',
            'elasin',
            'elasout',
            'elasinout',
            'backin',
            'backout',
            'backinout',
            'linear'
        );

        public function __construct() {
            global $wpdb;
            $wpdb->slider = $wpdb->prefix . "slider";
            $wpdb->slider_element = $wpdb->prefix . "slider_element";
            add_action('wp_head', array($this,'wp_head'));
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_print_scripts', array($this, 'admin_print_scripts'));
            add_action('admin_print_styles', array($this, 'admin_print_styles'));
            add_action('wp_print_styles', array($this, 'wp_print_styles'));
            register_activation_hook(__FILE__, array($this, 'register_activation_hook'));
            load_plugin_textdomain('wp-slider', false, dirname(plugin_basename(__FILE__)) . '/languages/');
            wp_register_script('jquery.cycle', WP_PLUGIN_URL . '/wp-slider/js/jquery.cycle.js', array('jquery'), '2.9993', true);
            wp_register_script('jquery.easing', WP_PLUGIN_URL . '/wp-slider/js/jquery.easing.js', array('jquery'), '1.3', true);
            wp_register_script('jquery.easing.compatibility', WP_PLUGIN_URL . '/wp-slider/js/jquery.easing.compatibility.js', array('jquery'), '1.00', true);
            wp_enqueue_script('jquery.cycle');
            wp_enqueue_script('jquery.easing');
            wp_enqueue_script('jquery.easing.compatibility');
        }

        public function check_dir() {
            if (!is_dir(WP_CONTENT_DIR . '/slides')) {
                @mkdir(WP_CONTENT_DIR . '/slides');
            }
        }

        public function register_activation_hook() {
            global $wpdb;
            
            $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->slider}` (
                    `id` int(11) NOT NULL auto_increment,
                    `name` varchar(200) NOT NULL,
                    `key` varchar(200) NOT NULL,
                    `options` text,
                    `effect` text,
                    `date_add` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
                    PRIMARY KEY  (`id`)
                )");
            
            $wpdb->query("CREATE TABLE `{$wpdb->slider_element}` (
                    `id` int(11) NOT NULL auto_increment,
                    `slider_id` int(11) NOT NULL,
                    `filename` varchar(200) NOT NULL,
                    `title` varchar(200) default NULL,
                    `description` text,
                    `url` text,
                    `target` enum('_self','_blank') default NULL,
                    `order` int(11) NOT NULL default '0',
                    `status` enum('active','inactive') NOT NULL default 'active',
                    `date_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
                    PRIMARY KEY  (`id`)
                )");
            $this->check_dir();
        }

        public function wp_head() { ?>
        <script type="text/javascript">
            var wp_slider = {};
            
            function wp_slider_command(e, command){
                jQuery('#slider-' + e).children('ul').cycle(command);
            }
        </script>
        <?php
        }

        public function admin_menu() {
            add_menu_page('Slider', 'Slider', 8, __FILE__, array($this, 'add_menu_page'), WP_PLUGIN_URL . '/wp-slider/icon.png');
        }

        public function admin_print_scripts() {
            wp_enqueue_script('wp-slider-admin-js', WP_PLUGIN_URL . '/wp-slider/js/admin.js', array('jquery'), '0.2', true);
        }

        public function admin_print_styles() {
            wp_enqueue_style('wp-slider-admin-css', WP_PLUGIN_URL . '/wp-slider/css/admin.css');
        }

        public function wp_print_styles() {
            wp_enqueue_style('wp-slider-css', WP_PLUGIN_URL . '/wp-slider/css/wp-slider.css');
        }

        public function is_url($string) {
            $string = ((strpos($string, 'http://') === false) && (strpos($string, 'https://') === false)) ? 'http://' . $string : $string;
            $pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
            return (preg_match($pattern, $string)) ? $string : false;
        }

        function get_slider_elements($slider_id = 0, $status = null) {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM {$wpdb->slider_element} WHERE slider_id = {$slider_id} " . (empty($status) ? "" : "AND `status` = 'active'") . " ORDER BY `order`", ARRAY_A);
        }

        function get_sliders($slider_id = 0) {
            global $wpdb;
            return $wpdb->get_results("SELECT * FROM {$wpdb->slider}", ARRAY_A);
        }

        function get_slider($slider_id = 0) {
            global $wpdb;
            $slider = $wpdb->get_row("SELECT * FROM {$wpdb->slider} WHERE id = {$slider_id}", ARRAY_A);
            $slider['options'] = unserialize($slider['options']);
            $slider['effect'] = unserialize($slider['effect']);
            return $slider;
        }

        function get_id_by_key($key) {
            global $wpdb;
            return $wpdb->get_var("SELECT `id` FROM {$wpdb->slider} WHERE `key`= '{$key}'");
        }

        function save_slider($arr_data) {
            global $wpdb;
            $options = array(
                'width' => $arr_data['width'],
                'height' => $arr_data['height'],
                'css' => $arr_data['css']
            );

            $effect = array(
                'effect' => in_array($arr_data['effect'], $this->effects) ? $arr_data['effect'] : 'fade',
                'easing' => in_array($arr_data['easing'], $this->easings) ? $arr_data['easing'] : '',
                'frecuency' => intval($arr_data['frecuency']),
                'delay' => intval($arr_data['delay']),
                'before' => $arr_data['before'],
                'after' => $arr_data['after']
            );

            $arr_data['key'] = sanitize_title($arr_data['name']);

            if ($arr_data['id'] == '') {
                $wpdb->insert($wpdb->slider, array(
                    'name' => $arr_data['name'],
                    'key' => $arr_data['key'],
                    'options' => serialize($options),
                    'effect' => serialize($effect)
                ));
                return $wpdb->insert_id;
            } else {
                $wpdb->update($wpdb->slider, array(
                    'name' => $arr_data['name'],
                    'key' => $arr_data['key'],
                    'options' => serialize($options),
                    'effect' => serialize($effect)
                        ), array('id' => $arr_data['id']));
                return $arr_data['id'];
            }
        }

        public function delete_slider($int_slider_id) {
            global $wpdb;
            $o = $wpdb->query("DELETE FROM {$wpdb->slider} WHERE id = {$int_slider_id}");
            $p = $this->delete_slider_element('slider_id = ' . $int_slider_id);
            if ($o != false && $p != false) {
                return true;
            }
            return false;
        }

        public function delete_slider_element($where) {
            global $wpdb;
            return $wpdb->query("DELETE FROM {$wpdb->slider_element} WHERE {$where}");
        }

        public function save_slider_elements($arr_slides) {
            foreach ($arr_slides['sid'] as $id) {
                $id = intval($id);
                $this->save_slider_element(array(
                    'id' => $id,
                    'slider_id' => $arr_slides['id'],
                    'current_filename' => $arr_slides['current_filename'][$id],
                    'title' => trim($arr_slides['title'][$id]),
                    'description' => trim($arr_slides['description'][$id]),
                    'url' => $this->is_url($arr_slides['url'][$id]),
                    'target' => $arr_slides['target'][$id],
                    'order' => $arr_slides['order'][$id],
                    'status' => $arr_slides['status'][$id]
                ));
            }
            
            return true;
        }

        public function save_slider_element($arr_element) {
            /*if ($arr_element['title'] == '') {
                return;
            }*/
            error_reporting(E_ALL);
            ini_set("display_errors", 1); 
            $this->check_dir();
            $id = $arr_element['id'];
            if ($_FILES['filename']['name'][$id] != '' && (strpos($_FILES['filename']['type'][$id], 'image') >= 0)) {
                $arr_element['filename'] = uniqid() . strstr($_FILES['filename']['name'][$id], '.');                
                if(move_uploaded_file($_FILES['filename']['tmp_name'][$id], '../wp-content/slides/' . $arr_element['filename'])){
                    @unlink('../wp-content/slides/' . $arr_element['current_filename']);
                } else {
                    return false;
                }
            } else {
                if ($arr_element['current_filename'] != '') {
                    $arr_element['filename'] = $arr_element['current_filename'];
                } else {
                    return false;
                }
            }

            global $wpdb;
            if ($id == '' || $id == 0) {
                return $wpdb->insert($wpdb->slider_element, array(
                    'slider_id' => $arr_element['slider_id'],
                    'filename' => $arr_element['filename'],
                    'title' => $arr_element['title'],
                    'description' => $arr_element['description'],
                    'url' => $arr_element['url'],
                    'target' => $arr_element['target'],
                    'order' => $arr_element['order'],
                ));
            } else {
                return $wpdb->update($wpdb->slider_element, array(
                    'filename' => $arr_element['filename'],
                    'title' => $arr_element['title'],
                    'description' => $arr_element['description'],
                    'url' => $arr_element['url'],
                    'target' => $arr_element['target'],
                    'order' => $arr_element['order'],
                    'status' => $arr_element['status']
                        ), array('id' => $id));
            }
        }

        public function add_menu_page() {
            include_once (dirname(__FILE__) . '/admin.php');
            slider_add_menu_page();
        }

        public function show($slider_id) {
            include_once (dirname(__FILE__) . '/public.php');
            slider_show($slider_id);
        }

    }

    global $wp_slider;
    $wp_slider = new wp_slider();

    function wp_slider($key) {
        global $wp_slider;
        $slider_id = $wp_slider->get_id_by_key($key);
        $wp_slider->show($slider_id);
    }

    function wp_slider_shortcode($atts) {
        ob_start();
        wp_slider($atts[0]);
        $content = ob_get_contents();
        ob_end_clean();
        return $conotent;
    }

    add_shortcode('wp-slider', 'wp_slider_shortcode');

}