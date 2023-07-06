<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_HOOKS
{

    public static function woof_get_front_css_file_link()
    {
        return apply_filters('woof_get_front_css_file_link', WOOF_LINK . 'css/front.css');
    }

}
