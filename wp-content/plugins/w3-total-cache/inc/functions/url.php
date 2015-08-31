<?php

/**
 * Formats URL
 *
 * @param string $url
 * @param array $params
 * @param boolean $skip_empty
 * @param string $separator
 * @return string
 */
function w3_url_format($url = '', $params = array(), $skip_empty = false, $separator = '&') {
    if ($url != '') {
        $parse_url = @parse_url($url);
        $url = '';

        if (!empty($parse_url['scheme'])) {
            $url .= $parse_url['scheme'] . '://';

            if (!empty($parse_url['user'])) {
                $url .= $parse_url['user'];

                if (!empty($parse_url['pass'])) {
                    $url .= ':' . $parse_url['pass'];
                }
            }

            if (!empty($parse_url['host'])) {
                $url .= $parse_url['host'];
            }

            if (!empty($parse_url['port']) && $parse_url['port'] != 80) {
                $url .= ':' . (int) $parse_url['port'];
            }
        }

        if (!empty($parse_url['path'])) {
            $url .= $parse_url['path'];
        }

        if (!empty($parse_url['query'])) {
            $old_params = array();
            parse_str($parse_url['query'], $old_params);

            $params = array_merge($old_params, $params);
        }

        $query = w3_url_query($params);

        if ($query != '') {
            $url .= '?' . $query;
        }

        if (!empty($parse_url['fragment'])) {
            $url .= '#' . $parse_url['fragment'];
        }
    } else {
        $query = w3_url_query($params, $skip_empty, $separator);

        if ($query != '') {
            $url = '?' . $query;
        }
    }

    return $url;
}

/**
 * Formats query string
 *
 * @param array $params
 * @param boolean $skip_empty
 * @param string $separator
 * @return string
 */
function w3_url_query($params = array(), $skip_empty = false, $separator = '&') {
    $str = '';
    static $stack = array();

    foreach ((array) $params as $key => $value) {
        if ($skip_empty === true && empty($value)) {
            continue;
        }

        array_push($stack, $key);

        if (is_array($value)) {
            if (count($value)) {
                $str .= ($str != '' ? '&' : '') . w3_url_query($value, $skip_empty, $key);
            }
        } else {
            $name = '';
            foreach ($stack as $key) {
                $name .= ($name != '' ? '[' . $key . ']' : $key);
            }
            $str .= ($str != '' ? $separator : '') . $name . '=' . rawurlencode($value);
        }

        array_pop($stack);
    }

    return $str;
}