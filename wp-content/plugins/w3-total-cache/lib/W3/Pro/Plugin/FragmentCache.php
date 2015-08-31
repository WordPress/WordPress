<?php

/**
 * W3 FragmentCache plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_FragmentCache
 */
class W3_Pro_Plugin_FragmentCache extends W3_Plugin {
    private $_fragment_groups = array();
    private $_fragment_groups_global = array();
    private $_actions = array();
    private $_actions_global = array();

    /**
     * Runs plugin
     */
    function run() {
        add_action('init', array($this, 'on_init'),9999999);

        add_filter('cron_schedules', array(
            &$this,
            'cron_schedules'
        ));

        if ($this->_config->get_string('fragmentcache.engine') == 'file') {
            add_action('w3_fragmentcache_cleanup', array(
                &$this,
                'cleanup'
            ));
        }

        add_action('switch_blog', array(
            &$this,
            'switch_blog'
        ), 0, 2);

        $groups = $this->_config->get_array('fragmentcache.groups');
        foreach ($groups as $group) {
            $split = explode(',', $group);
            $group = array_shift($split);
            $actions = $split;
            $this->register_group($group, $actions, 
                $this->_config->get_integer('fragmentcache.lifetime'));
        }
    }

    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        $this->get_admin()->cleanup();
    }

    /**
     * Cron schedules filter
     *
     * @param array $schedules
     * @return array
     */
    function cron_schedules($schedules) {
        $gc_interval = $this->_config->get_integer('fragmentcache.file.gc');

        return array_merge($schedules, array(
            'w3_fragmentcache_cleanup' => array(
                'interval' => $gc_interval,
                'display' => sprintf('[W3TC] Fragment Cache file GC (every %d seconds)', $gc_interval)
            ),
        ));
    }

    /**
     * Register actions on init
     */
    function on_init() {
        do_action('w3tc_register_fragment_groups');
        $actions = $this->get_registered_actions();
        foreach ($actions as $action => $groups) {
            add_action($action,array($this, 'on_action'), 0,0);
        }
        if (w3_is_network()) {
            $global_actions = $this->get_registered_global_actions();
            foreach ($global_actions as $action => $groups) {
                add_action($action,array($this, 'on_action_global'), 0,0);
            }
        }
    }

    /**
     * Flush action
     */
    function on_action() {
        $w3_fragmentcache = w3_instance('W3_Pro_FragmentCache');
        $actions = $this->get_registered_actions();
        $action = current_filter();
        $groups = $actions[$action];
        foreach($groups as $group) {
            $w3_fragmentcache->flush_group($group);
        }
    }

    /**
     * Flush action global
     */
    function on_action_global() {
        $w3_fragmentcache = w3_instance('W3_Pro_FragmentCache');
        $global_actions = $this->get_registered_global_actions();
        $action = current_filter();
        $global_groups = $global_actions[$action];
        foreach($global_groups as $group) {
            $w3_fragmentcache->flush_group($group, true);
        }
    }

    /**
     * Instantiates worker on demand
     *
     * @return W3_Plugin_FragmentCacheAdmin
     */
    function get_admin() {
        return w3_instance('W3_Pro_Plugin_FragmentCacheAdmin');
    }

    /**
     * Register transients group
     *
     * @param $group
     * @param $actions
     * @param $expiration
     */
    function register_group($group, $actions, $expiration) {
        if (empty($group) || empty($actions) || empty($expiration))
            return;

        if (!is_int($expiration)) {
            $expiration = (int) $expiration;
            trigger_error(__METHOD__ . ' needs expiration parameter to be an int.', E_USER_WARNING);
        }

        $this->_fragment_groups[$group] = array(
            'actions' => $actions,
            'expiration' => $expiration
        );

        foreach ($actions as $action) {
            if (!isset($this->_actions[$action]))
                $this->_actions[$action] = array();
            $this->_actions[$action][] = $group;
        }
    }

    /**
     * Register site-transients group
     *
     * @param string $group
     * @param array $actions
     * @param int $expiration
     */
    function register_global_group($group, $actions, $expiration) {
        if (empty($group) || empty($actions) || empty($expiration))
            return;

        if (!is_int($expiration)) {
            $expiration = (int) $expiration;
            trigger_error(__METHOD__ . ' needs expiration parameter to be an int.', E_USER_WARNING);
        }

        $this->_fragment_groups_global[$group] = array(
            'actions' => $actions,
            'expiration' => $expiration
        );
        foreach ($actions as $action) {
            if (!isset($this->_actions_global[$action]))
                $this->_actions_global[$action] = array();
            $this->_actions_global[$action][] = $group;
        }
    }

    /**
     * Returns registered fragment groups, ie transients.
     *
     * @return array array('group' => array('action1','action2'))
     */
    function get_registered_fragment_groups() {
        return $this->_fragment_groups;
    }

    /**
     * Returns registered actions and transient groups that should be purged per action
     * @return array array('action' => array('group1', 'group2'))
     */
    function get_registered_actions() {
        return $this->_actions;
    }

    /**
     * Returns registered global fragment groups, ie site-transients.
     *
     * @return array array('group' => array('action1','action2'))
     */
    function get_registered_global_fragment_groups() {
        return $this->_fragment_groups_global;
    }

    /**
     * Returns registered actions and site-transient groups that should be purged per action
     * @return array array('action' => array('group1', 'group2'))
     */
    function get_registered_global_actions() {
        return $this->_actions_global;
    }

    /**
     * Switch blog action
     */
    function switch_blog($blog_id, $previous_blog_id) {
        $o = w3_instance('W3_Pro_FragmentCache');
        $o->switch_blog($blog_id);
    }
}
