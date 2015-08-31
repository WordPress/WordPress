<?php
// To support legacy updates with old add-ins
if (class_exists('W3_ObjectCache'))
    return;

/**
 * W3 Object Cache object
 */
class W3_ObjectCache {
    /**
     * Internal cache array
     *
     * @var array
     */
    var $cache = array();

    /**
     * Array of global groups
     *
     * @var array
     */
    var $global_groups = array();

    /**
     * List of non-persistent groups
     *
     * @var array
     */
    var $nonpersistent_groups = array();

    /**
     * Total count of calls
     *
     * @var integer
     */
    var $cache_total = 0;

    /**
     * Cache hits count
     *
     * @var integer
     */
    var $cache_hits = 0;

    /**
     * Cache misses count
     *
     * @var integer
     */
    var $cache_misses = 0;

    /**
     * Total time
     *
     * @var integer
     */
    var $time_total = 0;

    /**
     * Store debug information of w3tc using
     *
     * @var array
     */
    var $debug_info = array();

    /**
     * Blog id of cache
     *
     * @var integer
     */
    private $_blog_id;

    /**
     * Key cache
     *
     * @var array
     */
    var $_key_cache = array();

    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * Caching flag
     *
     * @var boolean
     */
    var $_caching = false;

    /**
     * Dynamic Caching flag
     *
     * @var boolean
     */
    var $_can_cache_dynamic = null;
    /**
     * Cache reject reason
     *
     * @var string
     */
    private $cache_reject_reason = '';

    /**
     * Lifetime
     *
     * @var integer
     */
    var $_lifetime = null;

    /**
     * Debug flag
     *
     * @var boolean
     */
    var $_debug = false;

    /**
     * Returns instance. for backward compatibility with 0.9.2.3 version of /wp-content files
     *
     * @return W3_ObjectCache
     */
    function instance() {
        return w3_instance('W3_ObjectCache');
    }

    /**
     * PHP5 style constructor
     */
    function __construct() {
        global $_wp_using_ext_object_cache;

        $this->_config = w3_instance('W3_Config');
        $this->_lifetime = $this->_config->get_integer('objectcache.lifetime');
        $this->_debug = $this->_config->get_boolean('objectcache.debug');
        $this->_caching = $_wp_using_ext_object_cache = $this->_can_cache();
        $this->global_groups = $this->_config->get_array('objectcache.groups.global');
        $this->nonpersistent_groups = $this->_config->get_array('objectcache.groups.nonpersistent');

        $this->_blog_id = w3_get_blog_id();
    }

    /**
     * Get from the cache
     *
     * @param string $id
     * @param string $group
     * @return mixed
     */
    function get($id, $group = 'default') {
        if ($this->_debug) {
            $time_start = w3_microtime();
        }

        $key = $this->_get_cache_key($id, $group);
        $internal = isset($this->cache[$key]);

        if ($internal) {
            $value = $this->cache[$key];
        } elseif ($this->_caching &&
                  !in_array($group, $this->nonpersistent_groups) &&
                    $this->_check_can_cache_runtime($group)) {
            $cache = $this->_get_cache(null, $group);
            $v = $cache->get($key);
            if (is_array($v) && $v['content'] != null)
                $value = $v['content'];
            else
                $value = false;
        } else {
            $value = false;
        }

        if ($value === null) {
            $value = false;
        }

        if (is_object($value)) {
            $value = clone( $value );
        }

        $this->cache[$key] = $value;
        $this->cache_total++;

        if ($value !== false) {
            $cached = true;
            $this->cache_hits++;
        } else {
            $cached = false;
            $this->cache_misses++;
        }

        /**
         * Add debug info
         */
        if ($this->_debug) {
            $time = w3_microtime() - $time_start;
            $this->time_total += $time;

            if (!$group) {
                $group = 'default';
            }

            $this->debug_info[] = array(
                'id' => $id,
                'group' => $group,
                'cached' => $cached,
                'internal' => $internal,
                'data_size' => ($value ? strlen(serialize($value)) : ''),
                'time' => $time
            );
        }

        return $value;
    }

    /**
     * Set to the cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function set($id, $data, $group = 'default', $expire = 0) {
        $key = $this->_get_cache_key($id, $group);

        if (is_object($data)) {
            $data = clone( $data );
        }

        $this->cache[$key] = $data;

        if ($this->_caching && 
                !in_array($group, $this->nonpersistent_groups) &&
                $this->_check_can_cache_runtime($group)) {
            $cache = $this->_get_cache(null, $group);

            if ($id == 'alloptions' && $group == 'options') {
                // alloptions are deserialized on the start when some classes are not loaded yet
                // so postpone it until requested
                foreach ($data as $k => $v) {
                    if (is_object($v)) {
                        $data[$k] = serialize($v);
                    }
                }
            }

            $v = array('content' => $data);
            return $cache->set($key, $v,
                ($expire ? $expire : $this->_lifetime));
        }

        return true;
    }

    /**
     * Delete from the cache
     *
     * @param string $id
     * @param string $group
     * @param bool $force
     * @return boolean
     */
    function delete($id, $group = 'default', $force = false) {
        if (!$force && $this->get($id, $group) === false) {
            return false;
        }

        $key = $this->_get_cache_key($id, $group);

        unset($this->cache[$key]);

        if ($this->_caching && !in_array($group, $this->nonpersistent_groups)) {
            $cache = $this->_get_cache(null, $group);

            return $cache->delete($key);
        }

        return true;
    }

    /**
     * Add to the cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function add($id, $data, $group = 'default', $expire = 0) {
        if ($this->get($id, $group) !== false) {
            return false;
        }

        return $this->set($id, $data, $group, $expire);
    }

    /**
     * Replace in the cache
     *
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function replace($id, $data, $group = 'default', $expire = 0) {
        if ($this->get($id, $group) === false) {
            return false;
        }

        return $this->set($id, $data, $group, $expire);
    }

    /**
     * Reset keys
     *
     * @return boolean
     */
    function reset() {
        global $_wp_using_ext_object_cache;

        $_wp_using_ext_object_cache = $this->_caching;

        return true;
    }

    /**
     * Flush cache
     *
     * @return boolean
     */
    function flush() {
        $this->cache = array();

        global $w3_multisite_blogs;
        if (isset($w3_multisite_blogs)) {
            foreach($w3_multisite_blogs as $blog ){
                $cache = $this->_get_cache($blog->userblog_id);
                $cache->flush();
            }
        } else {
            //Global groups are now stored in master cache so need to be able to flush it form network
            if (is_network_admin())
                $cache = $this->_get_cache(0);
            else
                $cache = $this->_get_cache();

            return $cache->flush();
        }

        return true;
    }

    /**
     * Add global groups
     *
     * @param array $groups
     * @return void
     */
    function add_global_groups($groups) {
        if (!is_array($groups)) {
            $groups = (array) $groups;
        }

        $this->global_groups = array_merge($this->global_groups, $groups);
        $this->global_groups = array_unique($this->global_groups);
    }

    /**
     * Add non-persistent groups
     *
     * @param array $groups
     * @return void
     */
    function add_nonpersistent_groups($groups) {
        if (!is_array($groups)) {
            $groups = (array) $groups;
        }

        $this->nonpersistent_groups = array_merge($this->nonpersistent_groups, $groups);
        $this->nonpersistent_groups = array_unique($this->nonpersistent_groups);
    }

    /**
     * Increment numeric cache item's value
     *
     * @param int|string $key The cache key to increment
     * @param int $offset The amount by which to increment the item's value. Default is 1.
     * @param string $group The group the key is in.
     * @return bool|int False on failure, the item's new value on success.
     */
    function incr( $key, $offset = 1, $group = 'default' ) {
        $value = $this->get($key, $group);
        if ($value === false)
            return false;

        if (!is_numeric($value))
            $value = 0;

        $offset = (int) $offset;
        $value += $offset;

        if ( $value < 0 )
            $value = 0;
        $this->replace($key, $value, $group);
        return $value;
    }

    /**
     * Decrement numeric cache item's value
     *
     * @param int|string $key The cache key to increment
     * @param int $offset The amount by which to decrement the item's value. Default is 1.
     * @param string $group The group the key is in.
     * @return bool|int False on failure, the item's new value on success.
     */
    function decr( $key, $offset = 1, $group = 'default' ) {
        $value = $this->get($key, $group);
        if ($value === false)
            return false;

        if (!is_numeric($value))
            $value = 0;

        $offset = (int) $offset;
        $value -= $offset;

        if ( $value < 0 )
            $value = 0;
        $this->replace($key, $value, $group);
        return $value;
    }

    /**
     * Print Object Cache stats
     *
     * @return void
     */
    function stats()
    {
        echo '<h2>Summary</h2>';
        echo '<p>';
        echo '<strong>Engine</strong>: ' . w3_get_engine_name($this->_config->get_string('objectcache.engine')) . '<br />';
        echo '<strong>Caching</strong>: ' . ($this->_caching ? 'enabled' : 'disabled') . '<br />';

        if (!$this->_caching) {
            echo '<strong>Reject reason</strong>: ' . $this->get_reject_reason() . '<br />';
        }

        echo '<strong>Total calls</strong>: ' . $this->cache_total . '<br />';
        echo '<strong>Cache hits</strong>: ' . $this->cache_hits . '<br />';
        echo '<strong>Cache misses</strong>: ' . $this->cache_misses . '<br />';
        echo '<strong>Total time</strong>: '. round($this->time_total, 4) . 's';
        echo '</p>';

        echo '<h2>Cache info</h2>';

        if ($this->_debug) {
            echo '<table cellpadding="0" cellspacing="3" border="1">';
            echo '<tr><td>#</td><td>Status</td><td>Source</td><td>Data size (b)</td><td>Query time (s)</td><td>ID:Group</td></tr>';

            foreach ($this->debug_info as $index => $debug) {
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td>' . ($debug['cached'] ? 'cached' : 'not cached') . '</td>';
                echo '<td>' . ($debug['internal'] ? 'internal' : 'persistent') . '</td>';
                echo '<td>' . $debug['data_size'] . '</td>';
                echo '<td>' . round($debug['time'], 4) . '</td>';
                echo '<td>' . sprintf('%s:%s', $debug['id'], $debug['group']) . '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>Enable debug mode.</p>';
        }
    }

    /**
     * Switches context to another blog
     *
     * @param integer $blog_id
     */
    function switch_blog($blog_id) {
        $this->reset();
        $this->_blog_id = $blog_id;
    }

    /**
     * Returns cache key
     *
     * @param string $id
     * @param string $group
     * @return string
     */
    function _get_cache_key($id, $group = 'default') {
        if (!$group) {
            $group = 'default';
        }

        $blog_id = $this->_blog_id;
        if (in_array($group, $this->global_groups))
            $blog_id = 0;

        $key_cache_id = $blog_id . $group . $id;

        if (isset($this->_key_cache[$key_cache_id])) {
            $key = $this->_key_cache[$key_cache_id];
        } else {
            $key = md5($blog_id . $group . $id);
            $this->_key_cache[$key_cache_id] = $key;
        }

        return $key;
    }

    /**
     * Returns cache object
     *
     * @param int|null $blog_id
     * @param string $group
     * @return W3_Cache_Base
     */
    function _get_cache($blog_id = null, $group = '') {
        static $cache = array();

        if (is_null($blog_id) && !in_array($group, $this->global_groups))
            $blog_id = $this->_blog_id;
        elseif (is_null($blog_id))
            $blog_id = 0;

        if (!isset($cache[$blog_id])) {
            $engine = $this->_config->get_string('objectcache.engine');

            switch ($engine) {
                case 'memcached':
                    $engineConfig = array(
                        'servers' => $this->_config->get_array('objectcache.memcached.servers'),
                        'persistant' => $this->_config->get_boolean('objectcache.memcached.persistant')
                    );
                    break;

                case 'file':
                    $engineConfig = array(
                        'section' => 'object',
                        'locking' => $this->_config->get_boolean('objectcache.file.locking'),
                        'flush_timelimit' => $this->_config->get_integer('timelimit.cache_flush')
                    );
                    break;

                default:
                    $engineConfig = array();
            }
            $engineConfig['blog_id'] = $blog_id;
            $engineConfig['module'] = 'object';
            $engineConfig['host'] = w3_get_host();
            $engineConfig['instance_id'] = w3_get_instance_id();

            w3_require_once(W3TC_LIB_W3_DIR . '/Cache.php');

            $cache[$blog_id] = W3_Cache::instance($engine, $engineConfig);
        }

        return $cache[$blog_id];
    }

    /**
     * Check if caching allowed on init
     *
     * @return boolean
     */
    function _can_cache() {
        /**
         * Skip if disabled
         */
        if (!$this->_config->get_boolean('objectcache.enabled')) {
            $this->cache_reject_reason = 'objectcache.disabled';

            return false;
        }

        /**
         * Check for DONOTCACHEOBJECT constant
         */
        if (defined('DONOTCACHEOBJECT') && DONOTCACHEOBJECT) {
            $this->cache_reject_reason = 'DONOTCACHEOBJECT';

            return false;
        }

        return true;
    }

    /**
     * Returns if we can cache, that condition can change in runtime
     *
     * @param $group
     * @return boolean
     */
    function _check_can_cache_runtime($group) {
        //Need to be handled in wp admin as well as frontend
        if (in_array($group, array('transient', 'site-transient')))
            return true;

        if ($this->_can_cache_dynamic != null)
            return $this->_can_cache_dynamic;

        if ($this->_caching) {
            if (defined('WP_ADMIN')) {
                $this->_can_cache_dynamic = false;
                $this->cache_reject_reason = 'WP_ADMIN defined';
                return $this->_can_cache_dynamic;
            }
        }

        return $this->_caching;
    }

    /**
     * Returns debug info
     *
     * @return string
     */
    function _get_debug_info() {
        $debug_info = "<!-- W3 Total Cache: Object Cache debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), w3_get_engine_name($this->_config->get_string('objectcache.engine')));
        $debug_info .= sprintf("%s%s\r\n", str_pad('Caching: ', 20), ($this->_caching ? 'enabled' : 'disabled'));

        if (!$this->_caching) {
            $debug_info .= sprintf("%s%s\r\n", str_pad('Reject reason: ', 20), $this->cache_reject_reason);
        }

        $debug_info .= sprintf("%s%d\r\n", str_pad('Total calls: ', 20), $this->cache_total);
        $debug_info .= sprintf("%s%d\r\n", str_pad('Cache hits: ', 20), $this->cache_hits);
        $debug_info .= sprintf("%s%d\r\n", str_pad('Cache misses: ', 20), $this->cache_misses);
        $debug_info .= sprintf("%s%.4f\r\n", str_pad('Total time: ', 20), $this->time_total);

        $debug_info .= "W3TC Object Cache info:\r\n";
        $debug_info .= sprintf("%s | %s | %s | %s | %s | %s\r\n",
                               str_pad('#', 5, ' ', STR_PAD_LEFT),
                               str_pad('Status', 15, ' ', STR_PAD_BOTH),
                               str_pad('Source', 15, ' ', STR_PAD_BOTH),
                               str_pad('Data size (b)', 13, ' ', STR_PAD_LEFT),
                               str_pad('Query time (s)', 14, ' ', STR_PAD_LEFT),
                               'ID:Group');

        foreach ($this->debug_info as $index => $debug) {
            $debug_info .= sprintf("%s | %s | %s | %s | %s | %s\r\n",
                                   str_pad($index + 1, 5, ' ', STR_PAD_LEFT),
                                   str_pad(($debug['cached'] ? 'cached' : 'not cached'), 15, ' ', STR_PAD_BOTH),
                                   str_pad(($debug['internal'] ? 'internal' : 'persistent'), 15, ' ', STR_PAD_BOTH),
                                   str_pad($debug['data_size'], 13, ' ', STR_PAD_LEFT),
                                   str_pad(round($debug['time'], 4), 14, ' ', STR_PAD_LEFT),
                                   sprintf('%s:%s', $debug['id'], $debug['group']));
        }

        $debug_info .= '-->';

        return $debug_info;
    }
    public function get_reject_reason() {
        if (is_null($this->cache_reject_reason))
            return '';
        return $this->_get_reject_reason_message($this->cache_reject_reason);
    }

    /**
     * @param $key
     * @return string|void
     */
    private function _get_reject_reason_message($key) {
        if (!function_exists('__'))
            return $key;

        switch ($key) {
            case 'objectcache.disabled':
                return __('Object caching is disabled', 'w3-total-cache');
            case 'DONOTCACHEOBJECT':
                return __('DONOTCACHEOBJECT constant is defined', 'w3-total-cache');
            default:
                return '';
        }
    }
}
