<?php

/**
 * Returns path of pgcache cache rules file
 * Moved to separate file to not load rule.php for each disk enhanced request
 *
 * @return string
 */
function w3_get_pgcache_rules_cache_path() {
    switch (true) {
        case w3_is_apache():
        case w3_is_litespeed():
            if (w3_is_network()) {
                $url = w3_get_home_url();
                $match = null;
                if (preg_match('~http(s)?://(.+?)(/)?$~', $url, $match)) {
                    $home_path = $match[2];

                    return W3TC_CACHE_PAGE_ENHANCED_DIR . '/' .
                        $home_path . '/.htaccess';
                }
            }

            return W3TC_CACHE_PAGE_ENHANCED_DIR . '/.htaccess';

        case w3_is_nginx():
            return w3_get_nginx_rules_path();
    }

    return false;
}
