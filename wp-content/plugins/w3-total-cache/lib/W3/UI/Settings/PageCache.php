<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_PageCache extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'pgcache.engine' => __('Page cache method:', 'w3-total-cache'),
                'pgcache.enabled' => __('Page cache:', 'w3-total-cache'),
                'pgcache.debug' =>  __('Page Cache', 'w3-total-cache')
            ),
            'settings' => array(
                'pgcache.cache.home' => get_option('show_on_front') == 'posts' ? __('Cache front page', 'w3-total-cache'): __('Cache posts page', 'w3-total-cache'),
                'pgcache.reject.front_page' => __('Don\'t cache front page', 'w3-total-cache'),
                'pgcache.cache.feed' => __('Cache feeds: site, categories, tags, comments', 'w3-total-cache'),
                'pgcache.cache.ssl' => __('Cache <acronym title="Secure Socket Layer">SSL</acronym> (<acronym title="HyperText Transfer Protocol over SSL">https</acronym>) requests', 'w3-total-cache'),
                'pgcache.cache.query' =>  __('Cache <acronym title="Uniform Resource Identifier">URI</acronym>s with query string variables', 'w3-total-cache'),
                'pgcache.cache.404' => __('Cache 404 (not found) pages', 'w3-total-cache'),
                'pgcache.check.domain' =>  sprintf(__('Cache requests only for %s site address', 'w3-total-cache'), w3_get_home_domain() ),
                'pgcache.reject.logged'  => __('Don\'t cache pages for logged in users', 'w3-total-cache'),
                'pgcache.reject.logged_roles' =>  __('Don\'t cache pages for following user roles', 'w3-total-cache'),
                'pgcache.prime.enabled' => __('Automatically prime the page cache', 'w3-total-cache'),
                'pgcache.prime.interval' => __('Update interval:', 'w3-total-cache'),
                'pgcache.prime.limit' => __('Pages per interval:', 'w3-total-cache'),
                'pgcache.prime.sitemap' =>__('Sitemap <acronym title="Uniform Resource Indicator">URL</acronym>:', 'w3-total-cache'),
                'pgcache.prime.post.enabled' => __('Preload the post cache upon publish events.', 'w3-total-cache'),
                'pgcache.purge.front_page' => __('Front page', 'w3-total-cache'),
                'pgcache.purge.home' => get_option('show_on_front') == 'posts' ? __('Front page', 'w3-total-cache'): __('Posts page', 'w3-total-cache'),
                'pgcache.purge.post' => __('Post page', 'w3-total-cache'),
                'pgcache.purge.feed.blog' => __('Blog feed', 'w3-total-cache'),
                'pgcache.purge.comments' => __('Post comments pages', 'w3-total-cache'),
                'pgcache.purge.author' => __('Post author pages', 'w3-total-cache'),
                'pgcache.purge.terms' => __('Post terms pages', 'w3-total-cache'),
                'pgcache.purge.feed.comments' => __('Post comments feed', 'w3-total-cache'),
                'pgcache.purge.feed.author' => __('Post author feed', 'w3-total-cache'),
                'pgcache.purge.feed.terms' => __('Post terms feeds', 'w3-total-cache'),
                'pgcache.purge.archive.daily' => __('Daily archive pages', 'w3-total-cache'),
                'pgcache.purge.archive.monthly' => __('Monthly archive pages', 'w3-total-cache'),
                'pgcache.purge.archive.yearly' => __('Yearly archive pages', 'w3-total-cache'),
                'pgcache.purge.feed.types' =>  __('Specify the feed types to purge:', 'w3-total-cache'),
                'pgcache.purge.postpages_limit' =>  __('Purge Limit:', 'w3-total-cache'),
                'pgcache.purge.pages' =>  __('Additional pages:', 'w3-total-cache'),
                'pgcache.purge.sitemap_regex' =>  __('Purge sitemaps:', 'w3-total-cache'),
                'pgcache.memcached.servers' => __('Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:', 'w3-total-cache'),
                'pgcache.compatibility' => __('Enable compatibility mode', 'w3-total-cache'),
                'pgcache.remove_charset' =>  __('Disable UTF-8 blog charset support' ,'w3-total-cache'),
                'pgcache.reject.request_head' => __(' Disable caching of HEAD <acronym title="Hypertext Transfer Protocol">HTTP</acronym> requests', 'w3-total-cache'),
                'pgcache.lifetime' =>  __('Maximum lifetime of cache objects:', 'w3-total-cache'),
                'pgcache.file.gc' =>  __('Garbage collection interval:', 'w3-total-cache'),
                'pgcache.comment_cookie_ttl' => __('Comment cookie lifetime:', 'w3-total-cache'),
                'pgcache.accept.qs' =>  __('Accepted query strings:', 'w3-total-cache'),
                'pgcache.reject.ua' =>  __('Rejected user agents:', 'w3-total-cache'),
                'pgcache.reject.cookie' => __('Rejected cookies:', 'w3-total-cache'),
                'pgcache.reject.uri' =>  __('Never cache the following pages:', 'w3-total-cache'),
                'pgcache.accept.files' =>  __('Cache exception list:', 'w3-total-cache'),
                'pgcache.accept.uri' =>  __('Non-trailing slash pages:', 'w3-total-cache'),
                'pgcache.cache.headers' =>  __('Specify page headers:', 'w3-total-cache'),
                'pgcache.cache.nginx_handle_xml' => __('Handle <acronym title="Extensible Markup Language">XML</acronym> mime type', 'w3-total-cache'),
    )
        );
    }
}