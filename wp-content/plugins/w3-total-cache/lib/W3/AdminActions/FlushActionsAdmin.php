<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_FlushActionsAdmin {

    /**
     * @var W3_Config $_config
     */
    private $_config = null;

    function __construct() {
        $this->_config = w3_instance('W3_Config');
    }

    /**
     * Flush all caches action
     *
     * @return void
     */
    function action_flush_all() {
        $this->flush_all();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_all'
        ), true);
    }

    /**
     * Flush memcache cache action
     *
     * @return void
     */
    function action_flush_memcached() {
        $this->flush_memcached();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_memcached'
        ), true);
    }

    /**
     * Flush opcode caches action
     *
     * @return void
     */
    function action_flush_opcode() {
        $this->flush_opcode();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_opcode'
        ), true);
    }

    /**
     * Flush opcode caches action
     *
     * @return void
     */
    function action_flush_apc_system() {
        $this->flush_apc_system();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_apc_system'
        ), true);
    }

    /**
     * Flush file caches action
     *
     * @return void
     */
    function action_flush_file() {
        $this->flush_file();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_file'
        ), true);
    }

    /**
     * Flush page cache action
     *
     * @return void
     */
    function action_flush_pgcache() {
        $this->flush_pgcache();

        $this->_config->set('notes.need_empty_pgcache', false);
        $this->_config->set('notes.plugins_updated', false);

        $this->_config->save();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_pgcache'
        ), true);
    }

    /**
     * Flush database cache action
     *
     * @return void
     */
    function action_flush_dbcache() {
        $this->flush_dbcache();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_dbcache'
        ), true);
    }

    /**
     * Flush object cache action
     *
     * @return void
     */
    function action_flush_objectcache() {
        $this->flush_objectcache();

        $this->_config->set('notes.need_empty_objectcache', false);

        $this->_config->save();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_objectcache'
        ), true);
    }


    /**
     * Flush fragment cache action
     *
     * @return void
     */
    function action_flush_fragmentcache() {
        $this->flush_fragmentcache();

        $this->_config->set('notes.need_empty_fragmentcache', false);

        $this->_config->save();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_fragmentcache'
        ), true);
    }

    /**
     * Flush minify action
     *
     * @return void
     */
    function action_flush_minify() {
        $this->flush_minify();

        $this->_config->set('notes.need_empty_minify', false);

        $this->_config->save();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_minify'
        ), true);
    }

    /**
     * Flush browser cache action
     *
     * @return void
     */
    function action_flush_browser_cache() {
        $this->flush_browser_cache();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_browser_cache'
        ), true);
    }

    /*
	 * Flush varnish cache
     */
    function action_flush_varnish() {
        $this->flush_varnish();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_varnish'
        ), true);
    }

    /*
	 * Flush CDN mirror
     */
    function action_flush_cdn() {
        $this->flush_cdn();

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_cdn'
        ), true);
    }


    /**
     * PgCache purge post
     *
     * @return void
     */
    function action_flush_pgcache_purge_post() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $post_id = W3_Request::get_integer('post_id');
        do_action('w3tc_purge_from_pgcache', $post_id);

        w3_admin_redirect(array(
            'w3tc_note' => 'pgcache_purge_post'
        ), true);
    }

    /**
     * PgCache purge page
     *
     * @return void
     */
    function action_flush_pgcache_purge_page() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $post_id = W3_Request::get_integer('post_id');
        do_action('w3tc_purge_from_pgcache', $post_id);

        w3_admin_redirect(array(
            'w3tc_note' => 'pgcache_purge_page'
        ), true);
    }

    /**
     * Flush specified cache
     *
     * @param string $type
     * @return void
     */
    function flush($type) {

        if ($this->_config->get_string('pgcache.engine') == $type && $this->_config->get_boolean('pgcache.enabled')) {
            $this->_config->set('notes.need_empty_pgcache', false);
            $this->_config->set('notes.plugins_updated', false);
            $this->_config->save();
            $this->flush_pgcache();
        }

        if ($this->_config->get_string('dbcache.engine') == $type && $this->_config->get_boolean('dbcache.enabled')) {
            $this->flush_dbcache();
        }

        if ($this->_config->get_string('objectcache.engine') == $type && $this->_config->get_boolean('objectcache.enabled')) {
            $this->flush_objectcache();
        }

        if ($this->_config->get_string('fragmentcache.engine') == $type && $this->_config->get_boolean('fragmentcache.enabled')) {
            $this->flush_fragmentcache();
        }

        if ($this->_config->get_string('minify.engine') == $type && $this->_config->get_boolean('minify.enabled')) {
            $this->_config->set('notes.need_empty_minify', false);
            $this->_config->save();
            $this->flush_minify();
        }
    }

    /**
     * Flush memcached cache
     *
     * @return void
     */
    function flush_memcached() {
        $this->flush('memcached');
    }

    /**
     * Flush APC cache
     *
     * @return void
     */
    function flush_opcode() {
        $this->flush('apc');
        $this->flush('eaccelerator');
        $this->flush('xcache');
        $this->flush('wincache');
    }

    /**
     * Flush APC system cache
     */
    function flush_apc_system() {
        $cacheflush = w3_instance('W3_CacheFlush');
        $cacheflush->apc_system_flush();
    }

    /**
     * Flush file cache
     *
     * @return void
     */
    function flush_file() {
        $this->flush('file');
        $this->flush('file_generic');
    }

    /**
     * Flush all cache
     *
     * @param bool $flush_cf
     * @return void
     */
    function flush_all($flush_cf = true) {
        $this->flush_memcached();
        $this->flush_opcode();
        $this->flush_file();
        $this->flush_browser_cache();
        if ($this->_config->get_boolean('varnish.enabled'))
            $this->flush_varnish();
        do_action('w3tc_flush_all');
    }

    /**
     * Flush page cache
     *
     * @return void
     */
    function flush_pgcache() {
        $flusher = w3_instance('W3_CacheFlush');
        $flusher->pgcache_flush();
    }

    /**
     * Flush database cache
     *
     * @return void
     */
    function flush_dbcache() {
        $flusher = w3_instance('W3_CacheFlush');
        $flusher->dbcache_flush();
    }

    /**
     * Flush object cache
     *
     * @return void
     */
    function flush_objectcache() {
        $flusher = w3_instance('W3_CacheFlush');
        $flusher->objectcache_flush();
    }

    /**
     * Flush fragment cache
     */
    function flush_fragmentcache() {
        $flusher = w3_instance('W3_CacheFlush');
        $flusher->fragmentcache_flush();
    }

    /**
     * Flush minify cache
     *
     * @return void
     */
    function flush_minify() {
        $w3_minify = w3_instance('W3_Minify');
        $w3_minify->flush();
    }

    /**
     * Flush browsers cache
     */
    function flush_browser_cache() {
        if ($this->_config->get_boolean('browsercache.enabled')) {
            $this->_config->set('browsercache.timestamp', time());

            $this->_config->save();
        }
    }

    /**
     * Flush varnish cache
     */
    function flush_varnish() {
        $cacheflush = w3_instance('W3_CacheFlush');
        $cacheflush->varnish_flush();
    }

    /**
     * Flush CDN mirror
     */
    function flush_cdn() {
        $cacheflush = w3_instance('W3_CacheFlush');
        $cacheflush->cdncache_purge();
    }
}
