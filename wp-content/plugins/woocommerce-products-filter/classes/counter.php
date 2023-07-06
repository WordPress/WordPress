<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WP_QueryWoofCounter {

    public $post_count = 0;
    public $found_posts = 0;
    public $key_string = "";
    public $table = "";
    public $expire = DAY_IN_SECONDS;
    public $use_wp_cache = false;

    public function __construct($query) {
        $saving_memory = apply_filters('woof_counter_method', false);
        global $wpdb;

        $query = (array) $query;
        if ($saving_memory) {
            $query["nopaging"] = false;
            $query["posts_per_page"] = 1;
        }

        $key = md5(json_encode($query));
        //***
        $this->key_string = 'woof_count_cache_' . $key;
        $this->table = WOOF::$query_cache_table;
        //***
        $woof_settings = get_option('woof_settings', array());

        WOOF_REQUEST::set('woof_before_recount_query', 1);
        if (isset($woof_settings['cache_count_data']) AND $woof_settings['cache_count_data']) {
            $this->expire = $woof_settings['cache_count_data_auto_clean'];

            /*
             * If the user uses the object cache (otherwise it will give nothing)
             * and passing true on the hook will work wp-cache which is more optimized.
             * Showed good results in tests.
             */
            $this->use_wp_cache = apply_filters('woof_use_wp_cache', false);


            $value = $this->get_value();
            if ($value != -1) {
                $this->post_count = $this->found_posts = $value;
            } else {
                $q = new WP_QueryWOOFCounterIn($query);
                if ($saving_memory) {
                    $this->post_count = $this->found_posts = $q->found_posts;
                } else {
                    $this->post_count = $this->found_posts = $q->post_count;
                }
                unset($q);
                $this->set_value();
            }
        } else {
            $q = new WP_QueryWOOFCounterIn($query);
            if ($saving_memory) {
                $this->post_count = $this->found_posts = $q->found_posts;
            } else {
                $this->post_count = $this->found_posts = $q->post_count;
            }
            unset($q);
        }

        WOOF_REQUEST::del('woof_before_recount_query');
    }

    private function set_value() {

        if ($this->use_wp_cache) {
            wp_cache_set($this->key_string, $this->post_count, 'woocs_count', $this->expire);
            return;
        }

        global $wpdb;
        $data = array(
            array(
                'type' => 'string',
                'val' => $this->key_string
            ),
            array(
                'type' => 'int',
                'val' => $this->post_count,
            ),
            array(
                'type' => 'int',
                'val' => $this->post_count,
            ),
        );
        //$wpdb->query(WOOF_HELPER::woof_prepare("INSERT INTO {$this->table} (mkey, mvalue) VALUES (%s, %d)", $data));
        $wpdb->query(WOOF_HELPER::woof_prepare("INSERT INTO {$this->table} (mkey, mvalue) VALUES (%s, %d) ON DUPLICATE KEY UPDATE mvalue=%d", $data));
    }

    private function get_value() {

        global $wpdb;
        $result = -1;
        if ($this->use_wp_cache) {
            $value = wp_cache_get($this->key_string, 'woocs_count');
            if (!empty($value)) {
                $result = $value;
            }
            return $result;
        }
        $data = array(
            array(
                'type' => 'string',
                'val' => $this->key_string
            )
        );
        $sql = WOOF_HELPER::woof_prepare("SELECT mkey,mvalue FROM {$this->table} WHERE mkey='%s'", $data);
        $value = $wpdb->get_results($sql);

        if (!empty($value)) {
            $value = end($value);
            if (isset($value->mkey)) {
                $result = $value->mvalue;
            }
        }

        return $result;
    }

}

final class WP_QueryWOOFCounterIn extends WP_Query {

    function __construct($query = '') {
        parent::__construct($query);
    }

    function set_found_posts($q, $limits) {
        return false;
    }

    function setup_postdata($post) {
        return false;
    }

    function the_post() {
        return FALSE;
    }

    function have_posts() {
        return FALSE;
    }

}
