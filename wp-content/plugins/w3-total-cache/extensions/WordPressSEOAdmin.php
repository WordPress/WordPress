<?php
/**
 * W3 WordPressSEOAdmin module
 */
if (!defined('W3TC')) {
    die();
}
class W3_WordPressSEOAdmin {
    function run() {
        add_action('admin_init', array($this, 'admin_init'));
        add_filter('w3tc_extensions', array($this, 'extension'), 10, 2);
        add_filter('w3tc_extension_plugin_links-wordpress-seo', array($this, 'remove_settings'));
        add_action('w3tc_extensions_page-wordpress-seo', array($this, 'extension_header'));
        add_action('w3tc_activate_extension-wordpress-seo', array($this, 'activate'));
        add_action('w3tc_deactivate_extension-wordpress-seo', array($this, 'deactivate'));
    }

    public function admin_init() {
        if (w3tc_show_extension_notification('wordpress-seo', $this->criteria_match()))
            add_action('admin_notices', array($this, 'admin_notices'));
        $config = w3_instance('W3_Config');
        $groups = $config->get_array('mobile.rgroups');
        if (w3tc_edge_mode() && isset($groups['google']) && sizeof($groups['google']['agents']) == 1 && $groups['google']['agents'][0] == 'googlebot') {
            w3tc_delete_user_agent_group('google');
        }
    }

    /**
     *
     */
    public function admin_notices() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');
        w3_e_extension_activation_notification('WordPress SEO', 'wordpress-seo');
    }

    /**
     * @param $links
     * @return mixed
     */
    public function remove_settings($links) {
        array_pop($links);
        return $links;
    }
    /**
     * Display if caching or not.
     */
    function extension_header() {
        echo '<p>';
        printf(__('The WordPress SEO extension is currently %s ', 'w3-total-cache'),
            '<span class="w3tc-enabled">' . __('enabled', 'w3-total-cache') . '</span>');
        echo '.</p>';
    }

    /**
     * @param $extensions
     * @param W3_Config $config
     * @return mixed
     */
    function extension($extensions, $config) {
        $message = array();
        $message[] = 'WordPress SEO by Yoast';

        $extensions['wordpress-seo'] = array (
            'name' => 'WordPress SEO by Yoast',
            'author' => 'W3 EDGE',
            'description' => __('Configures W3 Total Cache to comply with WordPress SEO requirements automatically.', 'w3-total-cache'),

            'author uri' => 'http://www.w3-edge.com/',
            'extension uri' => 'http://www.w3-edge.com/',
            'extension id' => 'wordpress-seo',
            'version' => '1.0',
            'enabled' => $this->criteria_match(),
            'requirements' => implode(', ', $message),
            'path' => 'w3-total-cache/extensions/WordPressSEO.php'
        );

        return $extensions;
    }

    private function criteria_match() {
        return defined('WPSEO_VERSION');
    }

    public function activate() {
        try {
            $config = w3_instance('W3_Config');
            $config->set('pgcache.prime.enabled', true);
            $config->set('pgcache.prime.sitemap', '/sitemap_index.xml');
            $config->save();
        } catch (Exception $ex) {}
    }

    public function deactivate() {
        try {
            $config = w3_instance('W3_Config');
            $config->set('pgcache.prime.enabled', false);
            $config->save();
        } catch (Exception $ex) {}
    }
}

$ext = new W3_WordPressSEOAdmin();
$ext->run();
