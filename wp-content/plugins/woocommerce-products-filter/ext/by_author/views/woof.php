<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

if (isset(woof()->settings['by_author']))
{
    if (woof()->settings['by_author']['show'])
    {
        $placeholder = '';
        $role = '';
        if (isset(woof()->settings['by_author']['placeholder']))
        {
            WOOF_HELPER::wpml_translate(null, woof()->settings['by_author']['placeholder']);
        }

        if (isset(woof()->settings['by_author']['role']))
        {
            $role = woof()->settings['by_author']['role'];
        }

        echo do_shortcode('[woof_author_filter role="' . sanitize_text_field($role) . '" placeholder="' . sanitize_text_field($placeholder) . '"]');
    }
}

