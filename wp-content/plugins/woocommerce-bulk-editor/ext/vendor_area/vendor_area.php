<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOBE_VENDOR_AREA extends WOOBE_EXT {

    public $user_roles = array();

    public function __construct() {
        //'test_vendor'
        add_filter('woobe_apply_query_filter_data', array($this, 'add_query'));

        add_filter('woobe_user_can_edit', array($this, 'user_can'), 10, 3);
    }

    private function get_user_roles() {
        global $WOOBE;

        $vendors_str = $WOOBE->settings->vendor_roles;

        return explode(',', $vendors_str);
    }

    public function add_query($args) {
        $user = wp_get_current_user();
        $match = array_intersect((array) $user->roles, $this->get_user_roles());
        if (count($match)) {
            $args['author'] = $user->ID;
        }
        return $args;
    }

    public function user_can($visibility, $field_key, $shop_manager_visibility) {
        $user = wp_get_current_user();
        $match = array_intersect((array) $user->roles, $this->get_user_roles());
        if (count($match)) {
            if ($field_key == 'post_author') {
                return 0;
            }
        }
        return $visibility;
    }

}
