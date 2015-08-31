<?php

/**
 * W3 Total Cache Menus
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
class W3_Menus {

    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_dashboard';

    /**
     * @var W3_Config
     */
    private $_config_admin;

    /**
     * @var W3_Config
     */
    private $_config;

    function __construct() {
        $this->_config_admin = w3_instance('W3_ConfigAdmin');
        $this->_config = w3_instance('W3_Config');
    }

    function generate_menu_array() {
        $pages = array(
            'w3tc_dashboard' => array(
                __('Dashboard', 'w3-total-cache'),
                __('Dashboard', 'w3-total-cache'),
                'network_show' => true
            ),
            'w3tc_general' => array(
                __('General Settings', 'w3-total-cache'),
                __('General Settings', 'w3-total-cache'),
                'network_show' => false
            ),
            'w3tc_pgcache' => array(
                __('Page Cache', 'w3-total-cache'),
                __('Page Cache', 'w3-total-cache'),
                'network_show' => false
            ),
            'w3tc_minify' => array(
                __('Minify', 'w3-total-cache'),
                __('Minify', 'w3-total-cache'),
                'network_show' => false
            ),
            'w3tc_dbcache' => array(
                __('Database Cache', 'w3-total-cache'),
                __('Database Cache', 'w3-total-cache'),
                'network_show' => false
            ),
            'w3tc_objectcache' => array(
                __('Object Cache', 'w3-total-cache'),
                __('Object Cache', 'w3-total-cache'),
                'network_show' => false
            )
        );
        if (w3_is_pro($this->_config) || w3_is_enterprise($this->_config)) {
            $pages['w3tc_fragmentcache'] = array(
                __('Fragment Cache', 'w3-total-cache'),
                __('Fragment Cache', 'w3-total-cache'),
                'network_show' => false
            );
        }
        $pages = array_merge($pages, array(
            'w3tc_browsercache' => array(
                __('Browser Cache', 'w3-total-cache'),
                __('Browser Cache', 'w3-total-cache'),
                'network_show' => false
            ),
            'w3tc_mobile' => array(
                __('User Agent Groups', 'w3-total-cache'),
                __('User Agent Groups', 'w3-total-cache'),
                'network_show' => false
            ),
            'w3tc_referrer' => array(
                __('Referrer Groups', 'w3-total-cache'),
                __('Referrer Groups', 'w3-total-cache'),
                'network_show' => false
            ),
            'w3tc_cdn' => array(
                __('Content Delivery Network', 'w3-total-cache'),
                __('<acronym title="Content Delivery Network">CDN</acronym>', 'w3-total-cache'),
                'network_show' => $this->_config->get_boolean('cdn.enabled')
            ),
            'w3tc_monitoring' => array(
                __('Monitoring', 'w3-total-cache'),
                __('Monitoring', 'w3-total-cache'),
                'network_show' => false
            )
        ));
        $pages_tail = array(
            'w3tc_faq' => array(
                __('FAQ', 'w3-total-cache'),
                __('FAQ', 'w3-total-cache'),
                'network_show' => true
            ),
            'w3tc_support' => array(
                __('Support', 'w3-total-cache'),
                __('<span style="color: red;">Support</span>', 'w3-total-cache'),
                'network_show' => true
            ),
            'w3tc_install' => array(
                __('Install', 'w3-total-cache'),
                __('Install', 'w3-total-cache'),
                'network_show' => false
            ),
            'w3tc_about' => array(
                __('About', 'w3-total-cache'),
                __('About', 'w3-total-cache'),
                'network_show' => true
            )
        );
        $pages = apply_filters('w3tc_menu', $pages, $this->_config, $this->_config_admin);
        $pages = array_merge($pages, $pages_tail);
        return $pages;
    }
    function generate() {
        $pages = $this->generate_menu_array();
        add_menu_page(__('Performance', 'w3-total-cache'), __('Performance', 'w3-total-cache'), 'manage_options', 'w3tc_dashboard', '', 'div');

        $submenu_pages = array();

        foreach ($pages as $slug => $titles) {
            if (($this->_config_admin->get_boolean('common.visible_by_master_only') && $titles['network_show']) ||
                (!$this->_config_admin->get_boolean('common.visible_by_master_only') ||
                    (is_super_admin() && (!w3_force_master() || is_network_admin())))
            ) {
                $submenu_pages[] = add_submenu_page('w3tc_dashboard', $titles[0] . ' | W3 Total Cache', $titles[1], 'manage_options', $slug, array(
                    &$this,
                    'options'
                ));
            }
        }
        return $submenu_pages;
    }


    /**
     * Options page
     *
     * @return void
     */
    function options() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $this->_page = W3_Request::get_string('page');
        if (strpos($this->_page, 'w3tc_') === false) {
            $this->_page = 'w3tc_dashboard';
        }
        /*
         * Hidden pages
         */
        if (isset($_REQUEST['w3tc_dbcluster_config'])) {
            $options_dbcache = w3_instance('W3_UI_DbCacheAdminView');
            $options_dbcache->dbcluster_config();
        }

        /**
         * Show tab
         */
        switch ($this->_page) {
            case 'w3tc_dashboard':
                $options_dashboard = w3_instance('W3_UI_DashboardAdminView');
                $options_dashboard->options();
                break;

            case 'w3tc_general':
                $options_general = w3_instance('W3_UI_GeneralAdminView');
                $options_general->options();
                break;

            case 'w3tc_pgcache':
                $options_pgcache = w3_instance('W3_UI_PgCacheAdminView');
                $options_pgcache->options();
                break;

            case 'w3tc_minify':
                $options_minify = w3_instance('W3_UI_MinifyAdminView');
                $options_minify->options();
                break;

            case 'w3tc_dbcache':
                $options_dbcache = w3_instance('W3_UI_DbCacheAdminView');
                $options_dbcache->options();
                break;

            case 'w3tc_objectcache':
                $options_objectcache = w3_instance('W3_UI_ObjectCacheAdminView');
                $options_objectcache->options();
                break;

            case 'w3tc_fragmentcache':
                $options_fragmentcache = w3_instance('W3_UI_FragmentCacheAdminView');
                $options_fragmentcache->options();
                break;

            case 'w3tc_browsercache':
                $options_browsercache = w3_instance('W3_UI_BrowserCacheAdminView');
                $options_browsercache->options();
                break;

            case 'w3tc_mobile':
                $options_mobile = w3_instance('W3_UI_UserAgentGroupsAdminView');
                $options_mobile->options();
                break;

            case 'w3tc_referrer':
                $options_referrer = w3_instance('W3_UI_ReferrerGroupsAdminView');
                $options_referrer->options();
                break;

            case 'w3tc_cdn':
                $options_cdn = w3_instance('W3_UI_CdnAdminView');
                $options_cdn->options();
                break;

            case 'w3tc_monitoring':
                $options_monitoring = w3_instance('W3_UI_MonitoringAdminView');
                $options_monitoring->options();
                break;

            case 'w3tc_faq':
                $options_faq = w3_instance('W3_UI_FAQAdminView');
                $options_faq->options();
                break;

            case 'w3tc_support':
                $options_support = w3_instance('W3_UI_SupportAdminView');
                $options_support->options();
                break;

            case 'w3tc_install':
                $options_install = w3_instance('W3_UI_InstallAdminView');
                $options_install->options();
                break;

            case 'w3tc_about':
                $options_about = w3_instance('W3_UI_AboutAdminView');
                $options_about->options();
                break;
            default:
                do_action("w3tc_menu-{$this->_page}");
                break;
        }
    }
}