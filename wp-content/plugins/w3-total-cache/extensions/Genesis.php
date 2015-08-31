<?php

class W3_Genesis {
    /**
     * Request URI
     * @var string
     */
    private $_request_uri = '';
    /**
     * @var W3_Config $_config
     */
    private $_config;

    function run() {
        add_action('w3tc_register_fragment_groups', array($this, 'register_groups'));
        $this->_config = w3_instance('W3_Config');
        if ((w3_is_pro($this->_config) || w3_is_enterprise($this->_config)) && 
                $this->_config->get_boolean('fragmentcache.enabled')) {
            if (!is_admin()) {
                /**
                 * Register the caching of content to specific hooks
                 */
                foreach(array('genesis_header', 'genesis_footer', 'genesis_sidebar', 'genesis_loop', 'wp_head', 'wp_footer', 'genesis_comments', 'genesis_pings') as $hook) {
                    add_action($hook, array($this, 'cache_genesis_start'),-999999999);
                    add_action($hook, array($this, 'cache_genesis_end'), 999999999);
                }
                foreach(array('genesis_do_subnav', 'genesis_do_nav') as $filter) {
                    add_filter($filter, array($this, 'cache_genesis_filter_start'),-999999999);
                    add_filter($filter, array($this, 'cache_genesis_filter_end'), 999999999);
                }
            }

            /**
             * Since posts pages etc are cached individually need to be able to flush just those and not all fragment
             */
            add_action('clean_post_cache', array($this, 'flush_post_fragment'));
            add_action('clean_post_cache', array($this, 'flush_terms_fragment'), 0, 0);

            $this->_request_uri = $_SERVER['REQUEST_URI'];
        }
    }

    /**
     * Start outputbuffering or return fragment on a per page/hook basis
     */
    function cache_genesis_start() {
        $hook = current_filter();
        $keys = $this->_get_id_group($hook);
        if (is_null($keys))
            return;
        list($id, $group) = $keys;
        w3tc_fragmentcache_start($id, $group, $hook);
    }

    /**
     * Store the output buffer per page/post hook basis.
     */
    function cache_genesis_end() {
        $keys = $this->_get_id_group(current_filter());
        if (is_null($keys))
            return;
        list($id, $group) = $keys;
        w3tc_fragmentcache_end($id, $group, $this->_config->get_boolean('fragmentcache.debug'));
    }

    /**
     * Start filter buffering and return filter result
     */
    function cache_genesis_filter_start($data) {
        $hook = current_filter();
        $keys = $this->_get_id_group($hook, strpos($data,'current')!==false);
        if (is_null($keys))
            return $data;
        list($id, $group) = $keys;
        return w3tc_fragmentcache_filter_start($id, $group, $hook, $data);
    }

    /**
     * Store the filter result and return filter result.
     */
    function cache_genesis_filter_end($data) {
        $keys = $this->_get_id_group(current_filter(), strpos($data,'current')!==false);
        if (is_null($keys))
            return $data;
        list($id, $group) = $keys;
        return w3tc_fragmentcache_filter_end($id, $group, $data);
    }

    /**
     * Constructs the fragment grouping for a subgroup
     * @param $subgroup
     * @param $state
     * @return string
     */
    private function _genesis_group($subgroup, $state = false) {
        $postfix = '';
        if ($state && is_user_logged_in())
            $postfix = 'logged_in_';
        return ($subgroup ? "genesis_fragment_{$subgroup}_" : 'genesis_fragment_') . $postfix;
    }

    /**
     * Constructs the correct fragment group and id for the hook
     * @param $hook
     * @param bool $current_menu
     * @return array|null
     */
    private function _get_id_group($hook, $current_menu = false) {
        if ($this->cannot_cache_current_hook()) {
            return null;
        }
        switch (true) {
            case $keys = $this->generate_sidebar_keys():
                list($group, $genesis_id) = $keys;
                break;
            case $keys = $this->generate_genesis_loop_keys():
                list($group, $genesis_id) = $keys;
                break;
            case $keys = $this->generate_genesis_comments_pings_keys():
                list($group, $genesis_id) = $keys;
                break;
            case $keys = $this->generate_genesis_navigation_keys($current_menu):
                list($group, $genesis_id) = $keys;
                break;
            default:
                $group = $hook;
                $genesis_id = $this->get_page_slug();
                if (is_paged())
                    $genesis_id .= $this->get_paged_page_key();
                break;
        }

        if ($this->_cache_group($group) && !$this->_exclude_page($group)) {
            return array($genesis_id, $this->_genesis_group($group, true));
        }

        return null;
    }

    /**
     * Checks if the fragment group should be cached
     *
     * @param $group
     * @return array|bool|int|null|string
     */
    private function _cache_group($group) {
        return  w3tc_get_extension_config('genesis.theme', $group);
    }

    /**
     * Checks if current page is excluded from caching
     *
     * @param $group
     * @return bool
     */
    private function _exclude_page($group) {
        $reject_uri  = w3tc_get_extension_config('genesis.theme', "{$group}_excluded");
        if ($reject_uri)
            $reject_uri = explode("\n", $reject_uri);

        if (is_null($reject_uri) || !is_array($reject_uri) || empty($reject_uri)) {
            return false;
        }

        $auto_reject_uri = array(
            'wp-login',
            'wp-register'
        );
        foreach ($auto_reject_uri as $uri) {
            if (strstr($this->_request_uri, $uri) !== false) {
                return true;
            }
        }

        $reject_uri = array_map('w3_parse_path', $reject_uri);

        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $this->_request_uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register the various fragments groups to be used. no_action is used since fragments requires actions.
     */
    function register_groups() {
        //blog specific group and an array of actions that will trigger a flush of the group
        $groups = array (
            $this->_genesis_group('') => array(
                'clean_post_cache', 
                'update_option_sidebars_widgets', 
                'wp_update_nav_menu_item'),
            $this->_genesis_group('sidebar') => array(
                'update_option_sidebars_widgets'),
            $this->_genesis_group('loop_single') => array(
                'no_action'),
            $this->_genesis_group('loop_front_page') => array(
                'clean_post_cache'),
            $this->_genesis_group('loop_terms') => array(
                'no_action')
        );
        foreach($groups as $group => $actions)
            w3tc_register_fragment_group($group, $actions, 3600);
    }

    /**
     * Flush the fragments connected to a post id
     * @param $post_ID
     */
    function flush_post_fragment($post_ID) {
        /**
         * @var W3_SharedPageUrls $W3_SharedPageUrls
         */
        $W3_SharedPageUrls = w3_instance('W3_SharedPageUrls');
        $page_slug = $this->get_page_slug($post_ID);
        $urls = $W3_SharedPageUrls->get_post_urls($post_ID);
        $hooks = array('genesis_loop', 'genesis_comments', 'genesis_pings');
        foreach($hooks as $hook) {
            $genesis_id = $page_slug;
            $genesis_id = "{$hook}_{$genesis_id}";

            w3tc_fragmentcache_flush_fragment($genesis_id, $this->_genesis_group('loop_single_logged_in'));
            w3tc_fragmentcache_flush_fragment($genesis_id, $this->_genesis_group('loop_single'));
            for($page = 0; $page<=sizeof($urls); $page++) {
                $genesis_id = $page_slug;
                $genesis_id .= $this->get_paged_page_key($page);
                $genesis_id = "{$hook}_{$genesis_id}";

                w3tc_fragmentcache_flush_fragment($genesis_id, $this->_genesis_group('loop_single_logged_in'));
                w3tc_fragmentcache_flush_fragment($genesis_id, $this->_genesis_group('loop_single'));
            }
        }
    }

    public function flush_terms_fragment() {
        if (w3tc_get_extension_config('genesis.theme', 'flush_terms')) {
            w3tc_fragmentcache_flush_group('loop_terms');
        }
    }

    /**
     * @return bool
     */
    private function cannot_cache_current_hook() {
        if (is_user_logged_in() && w3tc_get_extension_config('genesis.theme', 'reject_logged_roles')) {
            $roles = w3tc_get_extension_config('genesis.theme', 'reject_roles');
            if ($roles) {
                $hooks = w3tc_get_extension_config('genesis.theme', 'reject_logged_roles_on_actions');
                $hook = current_filter();
                foreach ($roles as $role) {
                    if ($hooks && current_user_can($role) && in_array($hook, $hooks)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @return array
     */
    private function generate_genesis_loop_keys() {
        if (($hook = current_filter()) != 'genesis_loop')
            return null;

        if (is_front_page()) {
            $group = 'loop_front_page';
        } elseif (is_single()) {
            $group = 'loop_single';
        } else {
            $group = 'loop_terms';
        }
        $genesis_id = $this->get_page_slug();
        if (is_paged())
            $genesis_id .= $this->get_paged_page_key();
        $genesis_id = "{$hook}_{$genesis_id}";

        return array($group, $genesis_id);
    }

    /**
     * @return array
     */
    private function generate_sidebar_keys() {
        if (strpos($hook = current_filter(), 'sidebar') !== true)
            return null;

        $genesis_id = $hook;
        $group = 'sidebar';
        return array($group, $genesis_id);
    }

    /**
     * @return array|null
     */
    private function generate_genesis_comments_pings_keys() {
        if (($hook = current_filter()) != 'genesis_comments')
            return null;
        $group = 'loop_single';

        $genesis_id = $this->get_page_slug();
        if (is_paged())
            $genesis_id .= $this->get_paged_page_key();
        $genesis_id = "{$hook}_{$genesis_id}";

        return array($group, $genesis_id);
    }

    /**
     * @param string $current_menu
     * @return array|null
     */
    private function generate_genesis_navigation_keys($current_menu) {
        if (!(strpos(($hook = current_filter()), '_nav') && $current_menu))
            return null;

        $genesis_id = $this->get_page_slug();
        if (is_paged())
            $genesis_id .= $this->get_paged_page_key();
        return array($hook, $genesis_id);
    }

    private function get_page_slug($post_ID = null) {
        if ($post_ID) {
            $purl = get_permalink($post_ID);
            return str_replace('/','-', trim(str_replace(home_url(), '', $purl),"/"));
        }
        if (is_front_page())
            return 'front_page';
        return str_replace('/','-', trim($_SERVER['REQUEST_URI'],"/"));
    }

    /**
     * @param int|null $page
     * @return string _pagenumber_
     */
    private function get_paged_page_key($page=null) {
        if (is_null($page)) {
            global $wp_query;
            return '_' . $wp_query->query_vars['paged'] . '_';
        }

        return '_' . $page . '_';
    }
}

$ext = new W3_Genesis();
$ext->run();