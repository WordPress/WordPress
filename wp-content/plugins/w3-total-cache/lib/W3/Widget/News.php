<?php
/**
 * W3 Forum Widget
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
w3_require_once(W3TC_INC_DIR . '/functions/widgets.php');

/**
 * Class W3_Widget_Forum
 */
class W3_Widget_News extends W3_Plugin {

    function run() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        if(w3tc_get_current_wp_page() == 'w3tc_dashboard')
            add_action('admin_enqueue_scripts', array($this,'enqueue'));

        add_action('w3tc_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));
        add_action('w3tc_network_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));

        if (is_admin()) {
            add_action('wp_ajax_w3tc_widget_latest_news_ajax', array($this, 'action_widget_latest_news_ajax'));
        }
    }

    /**
     * Dashboard setup action
     *
     * @return void
     */
    function wp_dashboard_setup() {
        w3tc_add_dashboard_widget('w3tc_latest_news', __('News', 'w3-total-cache'), array(
            &$this,
            'widget_latest'
        ), array(
            &$this,
            'widget_latest_control'
        ), 'side');
    }

    /**
     * Returns key for transient cache of "widget latest"
     *
     * @return string
     */
    function _widget_latest_cache_key() {
        return 'dash_' . md5('w3tc_latest_news');
    }

    /**
     * Prints latest widget contents
     *
     * @return void
     */
    function widget_latest() {
        if (false !== ($output = get_transient($this->_widget_latest_cache_key())))
            echo $output;
        else
            include W3TC_INC_DIR . '/widget/latest_news.php';
    }

    /**
     * Prints latest widget contents
     *
     * @return void
     */
    function action_widget_latest_news_ajax() {
        // load content of feed
        global $wp_version;

        $items = array();
        $items_count = $this->_config->get_integer('widget.latest_news.items');

        if ($wp_version >= 2.8) {
            include_once (ABSPATH . WPINC . '/feed.php');
            $feed = fetch_feed(W3TC_NEWS_FEED_URL);

            if (!is_wp_error($feed)) {
                $feed_items = $feed->get_items(0, $items_count);

                foreach ($feed_items as $feed_item) {
                    $items[] = array(
                        'link' => $feed_item->get_link(),
                        'title' => $feed_item->get_title(),
                        'description' => $feed_item->get_description()
                    );
                }
            }
        } else {
            include_once (ABSPATH . WPINC . '/rss.php');
            $rss = fetch_rss(W3TC_NEWS_FEED_URL);

            if (is_object($rss)) {
                $items = array_slice($rss->items, 0, $items_count);
            }
        }

        // Removes feedburner tracking images when site is https
        if (w3_is_https()) {
            $total = sizeof($items);
            for($i = 0; $i < $total; $i++) {
                if (isset($items[$i]['description'])) {
                    $items[$i]['description'] = preg_replace('/<img[^>]+src[^>]+W3TOTALCACHE[^>]+>/',
                        '', $items[$i]['description']);
                }
            }
        }

        ob_start();
        include W3TC_INC_DIR . '/widget/latest_news_ajax.php';

        // Default lifetime in cache of 12 hours (same as the feeds)
        set_transient($this->_widget_latest_cache_key(), ob_get_flush(), 43200);
        die();
    }

    /**
     * Latest widget control
     *
     * @param integer $widget_id
     * @param array $form_inputs
     * @return void
     */
    function widget_latest_control($widget_id, $form_inputs = array()) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

            $this->_config->set('widget.latest_news.items', W3_Request::get_integer('w3tc_widget_latest_news_items', 3));
            $this->_config->save();
            delete_transient($this->_widget_latest_cache_key());
        }
        include W3TC_INC_DIR . '/widget/latest_news_control.php';
    }

    public function enqueue() {
        wp_enqueue_style('w3tc-widget');
        wp_enqueue_script('w3tc-metadata');
        wp_enqueue_script('w3tc-widget');
    }
}
