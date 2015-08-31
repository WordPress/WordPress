<?php
/**
 * W3 FeedBurner module
 */
if (!defined('W3TC')) {
    die();
}

class W3_FeedBurnerAdmin  {
    function run() {
        add_action('admin_init', array($this, 'admin_init'));
        add_filter('w3tc_extensions', array($this, 'extension'), 10, 2);
        add_action('w3tc_extensions_page-feedburner', array($this, 'extension_header'));
    }


    /**
     * Setups sections
     */
    function admin_init() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');
        // Register our settings field group
        w3tc_add_settings_section(
            'ping', // Unique identifier for the settings section
            'Ping FeedBurner', // Section title
            '__return_false', // Section callback (we don't want anything)
            'feedburner' // extension id, used to uniquely identify the extension;
        );

        $settings = $this->settings();
        foreach($settings as $setting => $meta) {
            /**
             * @var $label
             * @var $description
             * @var $section
             * @var $type
             */
            extract($meta);
            w3tc_add_settings_field($setting, $label,
                array($this, 'print_setting'), 'feedburner', $section,
                array('label_for'=>$setting, 'type'=>$type,
                    'description' => $description));
        }
    }

    /**
     * Display if caching or not.
     */
    function extension_header() {
        $config = w3_instance('W3_Config');
        echo '<p>';
        printf(__('The FeedBurner extension is currently %s ', 'w3-total-cache'),
            '<span class="w3tc-enabled">' . __('enabled', 'w3-total-cache') . '</span>');
        echo '.</p>';
    }


    /**
     *
     * @param $setting
     * @param $args
     */
    function print_setting($setting, $args) {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');
        list($name, $id) = w3tc_get_name_and_id('feedburner', $setting);
        w3_ui_element($args['type'], $setting, $name, w3tc_get_extension_config('feedburner', $setting), w3_extension_is_sealed('feedburner'));
    }


    /**
     * @param $extensions
     * @param W3_Config $config
     * @return mixed
     */
    function extension($extensions, $config) {
        $message = array();
        $message[] = 'FeedBurner';

        $extensions['feedburner'] = array (
            'name' => 'FeedBurner',
            'author' => 'W3 EDGE',
            'description' => sprintf(__('Automatically ping (purge) FeedBurner feeds when pages / posts are modified. Default URL: %s', 'w3-total-cache'),
    !is_network_admin() ? home_url() : __('Network Admin has no main URL.', 'w3-total-cache')),

'author uri' => 'http://www.w3-edge.com/',
            'extension uri' => 'http://www.w3-edge.com/',
            'extension id' => 'feedburner',
            'version' => '0.1',
            'enabled' => true,
            'requirements' => implode(', ', $message),
            'path' => 'w3-total-cache/extensions/FeedBurner.php'
        );

        return $extensions;
    }
    /**
     * @return array
     */
    function settings() {
        return
            array(
                'urls' =>
                array(
                    'type' => 'textarea',
                    'section' => 'ping',
                    'label' => __('Additional URLs:', 'w3-total-cache'),
                    'description' => __('Specify any additional feed URLs to ping on FeedBurner.', 'w3-total-cache')
                ),
            );
    }
}

$ext = new W3_FeedBurnerAdmin();
$ext->run();