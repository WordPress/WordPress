<?php

if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/themes.php');
w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_MinifyAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_minify';

    /**
     * Minify tab
     *
     * @return void
     */
    function view() {
        $minify_enabled = $this->_config->get_boolean('minify.enabled');

        $minify_rewrite_disabled = (w3_is_network() && !$this->is_master() && !$this->_config_master->get_boolean('minify.rewrite'));
        $themes = w3_get_themes();
        $templates = array();

        $current_theme = w3tc_get_current_theme_name();
        $current_theme_key = '';

        foreach ($themes as $theme_key => $theme_name) {
            if ($theme_name == $current_theme) {
                $current_theme_key = $theme_key;
            }

            $templates[$theme_key] = w3_get_theme_templates($theme_name);
        }

        $css_imports_values = array(
            '' => 'None',
            'bubble' => 'Bubble',
            'process' => 'Process',
        );

        $auto = $this->_config->get_boolean('minify.auto');

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $js_theme = W3_Request::get_string('js_theme', $current_theme_key);
        $js_groups = $this->_config->get_array('minify.js.groups');

        $css_theme = W3_Request::get_string('css_theme', $current_theme_key);
        $css_groups = $this->_config->get_array('minify.css.groups');

        $js_engine = $this->_config->get_string('minify.js.engine');
        $css_engine = $this->_config->get_string('minify.css.engine');
        $html_engine = $this->_config->get_string('minify.html.engine');

        $css_imports = $this->_config->get_string('minify.css.imports');

        // Required for Update Media Query String button
        $browsercache_enabled = $this->_config->get_boolean('browsercache.enabled');
        $browsercache_update_media_qs = ($this->_config->get_boolean('browsercache.cssjs.replace'));

        include W3TC_INC_DIR . '/options/minify.php';
    }

    function recommendations() {
        $themes = w3_get_themes();

        $current_theme = w3tc_get_current_theme_name();
        $current_theme_key = array_search($current_theme, $themes);

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $theme_key = W3_Request::get_string('theme_key', $current_theme_key);
        $theme_name = (isset($themes[$theme_key]) ? $themes[$theme_key] : $current_theme);

        $templates = w3_get_theme_templates($theme_name);
        $recommendations = $this->get_theme_recommendations($theme_name);

        list ($js_groups, $css_groups) = $recommendations;

        $minify_js_groups = $this->_config->get_array('minify.js.groups');
        $minify_css_groups = $this->_config->get_array('minify.css.groups');

        $checked_js = array();
        $checked_css = array();

        $locations_js = array();

        if (isset($minify_js_groups[$theme_key])) {
            foreach ((array) $minify_js_groups[$theme_key] as $template => $locations) {
                foreach ((array) $locations as $location => $config) {
                    if (isset($config['files'])) {
                        foreach ((array) $config['files'] as $file) {
                            if (!isset($js_groups[$template]) || !in_array($file, $js_groups[$template])) {
                                $js_groups[$template][] = $file;
                            }

                            $checked_js[$template][$file] = true;
                            $locations_js[$template][$file] = $location;
                        }
                    }
                }
            }
        }

        if (isset($minify_css_groups[$theme_key])) {
            foreach ((array) $minify_css_groups[$theme_key] as $template => $locations) {
                foreach ((array) $locations as $location => $config) {
                    if (isset($config['files'])) {
                        foreach ((array) $config['files'] as $file) {
                            if (!isset($css_groups[$template]) || !in_array($file, $css_groups[$template])) {
                                $css_groups[$template][] = $file;
                            }

                            $checked_css[$template][$file] = true;
                        }
                    }
                }
            }
        }

        include W3TC_INC_DIR . '/lightbox/minify_recommendations.php';
    }

    /**
     * Returns array of detected URLs for theme templates
     *
     * @param string $theme_name
     * @return array
     */
    function get_theme_urls($theme_name) {
        $urls = array();
        $theme = w3tc_get_theme($theme_name);

        if ($theme && isset($theme['Template Files'])) {
            $front_page_template = false;

            if (get_option('show_on_front') == 'page') {
                $front_page_id = get_option('page_on_front');

                if ($front_page_id) {
                    $front_page_template_file = get_post_meta($front_page_id, '_wp_page_template', true);

                    if ($front_page_template_file) {
                        $front_page_template = basename($front_page_template_file, '.php');
                    }
                }
            }

            $home_url = w3_get_home_url();
            $template_files = (array) $theme['Template Files'];

            $mime_types = get_allowed_mime_types();
            $custom_mime_types = array();

            foreach ($mime_types as $mime_type) {
                list ($type1, $type2) = explode('/', $mime_type);
                $custom_mime_types = array_merge($custom_mime_types, array(
                    $type1,
                    $type2,
                    $type1 . '_' . $type2
                ));
            }

            foreach ($template_files as $template_file) {
                $link = false;
                $template = basename($template_file, '.php');

                /**
                 * Check common templates
                 */
                switch (true) {
                    /**
                     * Handle home.php or index.php or front-page.php
                     */
                    case (!$front_page_template && $template == 'home'):
                    case (!$front_page_template && $template == 'index'):
                    case (!$front_page_template && $template == 'front-page'):

                        /**
                         * Handle custom home page
                         */
                    case ($template == $front_page_template):
                        $link = $home_url . '/';
                        break;

                    /**
                     * Handle 404.php
                     */
                    case ($template == '404'):
                        $permalink = get_option('permalink_structure');
                        if ($permalink) {
                            $link = sprintf('%s/%s/', $home_url, '404_test');
                        } else {
                            $link = sprintf('%s/?p=%d', $home_url, 999999999);
                        }
                        break;

                    /**
                     * Handle search.php
                     */
                    case ($template == 'search'):
                        $link = sprintf('%s/?s=%s', $home_url, 'search_test');
                        break;

                    /**
                     * Handle date.php or archive.php
                     */
                    case ($template == 'date'):
                    case ($template == 'archive'):
                        $posts = get_posts(array(
                            'numberposts' => 1,
                            'orderby' => 'rand'
                        ));
                        if (is_array($posts) && count($posts)) {
                            $time = strtotime($posts[0]->post_date);
                            $link = get_day_link(date('Y', $time), date('m', $time), date('d', $time));
                        }
                        break;

                    /**
                     * Handle author.php
                     */
                    case ($template == 'author'):
                        $author_id = false;
                        if (function_exists('get_users')) {
                            $users = get_users();
                            if (is_array($users) && count($users)) {
                                $user = current($users);
                                $author_id = $user->ID;
                            }
                        } else {
                            $author_ids = get_author_user_ids();
                            if (is_array($author_ids) && count($author_ids)) {
                                $author_id = $author_ids[0];
                            }
                        }
                        if ($author_id) {
                            $link = get_author_posts_url($author_id);
                        }
                        break;

                    /**
                     * Handle category.php
                     */
                    case ($template == 'category'):
                        $category_ids = get_all_category_ids();
                        if (is_array($category_ids) && count($category_ids)) {
                            $link = get_category_link($category_ids[0]);
                        }
                        break;

                    /**
                     * Handle tag.php
                     */
                    case ($template == 'tag'):
                        $term_ids = get_terms('post_tag', 'fields=ids');
                        if (is_array($term_ids) && count($term_ids)) {
                            $link = get_term_link($term_ids[0], 'post_tag');
                        }
                        break;

                    /**
                     * Handle taxonomy.php
                     */
                    case ($template == 'taxonomy'):
                        $taxonomy = '';
                        if (isset($GLOBALS['wp_taxonomies']) && is_array($GLOBALS['wp_taxonomies'])) {
                            foreach ($GLOBALS['wp_taxonomies'] as $wp_taxonomy) {
                                if (!in_array($wp_taxonomy->name, array(
                                    'category',
                                    'post_tag',
                                    'link_category'
                                ))) {
                                    $taxonomy = $wp_taxonomy->name;
                                    break;
                                }
                            }
                        }
                        if ($taxonomy) {
                            $terms = get_terms($taxonomy, array(
                                'number' => 1
                            ));
                            if (is_array($terms) && count($terms)) {
                                $link = get_term_link($terms[0], $taxonomy);
                            }
                        }
                        break;

                    /**
                     * Handle attachment.php
                     */
                    case ($template == 'attachment'):
                        $attachments = get_posts(array(
                            'post_type' => 'attachment',
                            'numberposts' => 1,
                            'orderby' => 'rand'
                        ));
                        if (is_array($attachments) && count($attachments)) {
                            $link = get_attachment_link($attachments[0]->ID);
                        }
                        break;

                    /**
                     * Handle single.php
                     */
                    case ($template == 'single'):
                        $posts = get_posts(array(
                            'numberposts' => 1,
                            'orderby' => 'rand'
                        ));
                        if (is_array($posts) && count($posts)) {
                            $link = get_permalink($posts[0]->ID);
                        }
                        break;

                    /**
                     * Handle page.php
                     */
                    case ($template == 'page'):
                        $pages_ids = get_all_page_ids();
                        if (is_array($pages_ids) && count($pages_ids)) {
                            $link = get_page_link($pages_ids[0]);
                        }
                        break;

                    /**
                     * Handle comments-popup.php
                     */
                    case ($template == 'comments-popup'):
                        $posts = get_posts(array(
                            'numberposts' => 1,
                            'orderby' => 'rand'
                        ));
                        if (is_array($posts) && count($posts)) {
                            $link = sprintf('%s/?comments_popup=%d', $home_url, $posts[0]->ID);
                        }
                        break;

                    /**
                     * Handle paged.php
                     */
                    case ($template == 'paged'):
                        global $wp_rewrite;
                        if ($wp_rewrite->using_permalinks()) {
                            $link = sprintf('%s/page/%d/', $home_url, 1);
                        } else {
                            $link = sprintf('%s/?paged=%d', 1);
                        }
                        break;

                    /**
                     * Handle author-id.php or author-nicename.php
                     */
                    case preg_match('~^author-(.+)$~', $template, $matches):
                        if (is_numeric($matches[1])) {
                            $link = get_author_posts_url($matches[1]);
                        } else {
                            $link = get_author_posts_url(null, $matches[1]);
                        }
                        break;

                    /**
                     * Handle category-id.php or category-slug.php
                     */
                    case preg_match('~^category-(.+)$~', $template, $matches):
                        if (is_numeric($matches[1])) {
                            $link = get_category_link($matches[1]);
                        } else {
                            $term = get_term_by('slug', $matches[1], 'category');
                            if (is_object($term)) {
                                $link = get_category_link($term->term_id);
                            }
                        }
                        break;

                    /**
                     * Handle tag-id.php or tag-slug.php
                     */
                    case preg_match('~^tag-(.+)$~', $template, $matches):
                        if (is_numeric($matches[1])) {
                            $link = get_tag_link($matches[1]);
                        } else {
                            $term = get_term_by('slug', $matches[1], 'post_tag');
                            if (is_object($term)) {
                                $link = get_tag_link($term->term_id);
                            }
                        }
                        break;

                    /**
                     * Handle taxonomy-taxonomy-term.php
                     */
                    case preg_match('~^taxonomy-(.+)-(.+)$~', $template, $matches):
                        $link = get_term_link($matches[2], $matches[1]);
                        break;

                    /**
                     * Handle taxonomy-taxonomy.php
                     */
                    case preg_match('~^taxonomy-(.+)$~', $template, $matches):
                        $terms = get_terms($matches[1], array(
                            'number' => 1
                        ));
                        if (is_array($terms) && count($terms)) {
                            $link = get_term_link($terms[0], $matches[1]);
                        }
                        break;

                    /**
                     * Handle MIME_type.php
                     */
                    case in_array($template, $custom_mime_types):
                        $posts = get_posts(array(
                            'post_mime_type' => '%' . $template . '%',
                            'post_type' => 'attachment',
                            'numberposts' => 1,
                            'orderby' => 'rand'
                        ));
                        if (is_array($posts) && count($posts)) {
                            $link = get_permalink($posts[0]->ID);
                        }
                        break;

                    /**
                     * Handle single-posttype.php
                     */
                    case preg_match('~^single-(.+)$~', $template, $matches):
                        $posts = get_posts(array(
                            'post_type' => $matches[1],
                            'numberposts' => 1,
                            'orderby' => 'rand'
                        ));

                        if (is_array($posts) && count($posts)) {
                            $link = get_permalink($posts[0]->ID);
                        }
                        break;

                    /**
                     * Handle page-id.php or page-slug.php
                     */
                    case preg_match('~^page-(.+)$~', $template, $matches):
                        if (is_numeric($matches[1])) {
                            $link = get_permalink($matches[1]);
                        } else {
                            $posts = get_posts(array(
                                'pagename' => $matches[1],
                                'post_type' => 'page',
                                'numberposts' => 1
                            ));

                            if (is_array($posts) && count($posts)) {
                                $link = get_permalink($posts[0]->ID);
                            }
                        }
                        break;

                    /**
                     * Try to handle custom template
                     */
                    default:
                        $posts = get_posts(array(
                            'pagename' => $template,
                            'post_type' => 'page',
                            'numberposts' => 1
                        ));

                        if (is_array($posts) && count($posts)) {
                            $link = get_permalink($posts[0]->ID);
                        }
                        break;
                }

                if ($link && !is_wp_error($link)) {
                    $urls[$template] = $link;
                }
            }
        }

        return $urls;
    }

    /**
     * Returns theme recommendations
     *
     * @param string $theme_name
     * @return array
     */
    function get_theme_recommendations($theme_name) {
        $urls = $this->get_theme_urls($theme_name);

        $js_groups = array();
        $css_groups = array();

        @set_time_limit($this->_config->get_integer('timelimit.minify_recommendations'));

        foreach ($urls as $template => $url) {
            /**
             * Append theme identifier
             */
            $url .= (strstr($url, '?') !== false ? '&' : '?') . 'w3tc_theme=' . urlencode($theme_name);

            /**
             * If preview mode enabled append w3tc_preview
             */
            if ($this->_config->is_preview()) {
                $url .= '&w3tc_preview=1';
            }

            /**
             * Get page contents
             */
            $response = w3_http_get($url);

            if (!is_wp_error($response) && ($response['response']['code'] == 200 || ($response['response']['code'] == 404 && $template == '404'))) {
                $js_files = $this->get_recommendations_js($response['body']);
                $css_files = $this->get_recommendations_css($response['body']);

                $js_groups[$template] = $js_files;
                $css_groups[$template] = $css_files;
            }
        }

        $js_groups = $this->get_theme_recommendations_by_groups($js_groups);
        $css_groups = $this->get_theme_recommendations_by_groups($css_groups);

        $recommendations = array(
            $js_groups,
            $css_groups
        );

        return $recommendations;
    }

    /**
     * Find common files and place them into default group
     *
     * @param array $groups
     * @return array
     */
    function get_theme_recommendations_by_groups($groups) {
        /**
         * First calculate file usage count
         */
        $all_files = array();

        foreach ($groups as $template => $files) {
            foreach ($files as $file) {
                if (!isset($all_files[$file])) {
                    $all_files[$file] = 0;
                }

                $all_files[$file]++;
            }
        }

        /**
         * Determine default group files
         */
        $default_files = array();
        $count = count($groups);

        foreach ($all_files as $all_file => $all_file_count) {
            /**
             * If file usage count == groups count then file is common
             */
            if ($count == $all_file_count) {
                $default_files[] = $all_file;

                /**
                 * If common file found unset it from all groups
                 */
                foreach ($groups as $template => $files) {
                    foreach ($files as $index => $file) {
                        if ($file == $all_file) {
                            array_splice($groups[$template], $index, 1);
                            if (!count($groups[$template])) {
                                unset($groups[$template]);
                            }
                            break;
                        }
                    }
                }
            }
        }

        /**
         * If there are common files append add them into default group
         */
        if (count($default_files)) {
            $new_groups = array();
            $new_groups['default'] = $default_files;

            foreach ($groups as $template => $files) {
                $new_groups[$template] = $files;
            }

            $groups = $new_groups;
        }

        /**
         * Unset empty templates
         */
        foreach ($groups as $template => $files) {
            if (!count($files)) {
                unset($groups[$template]);
            }
        }

        return $groups;
    }

    /**
     * Parse content and return JS recommendations
     *
     * @param string $content
     * @return array
     */
    function get_recommendations_js(&$content) {
        w3_require_once(W3TC_INC_DIR . '/functions/extract.php');

        $files = w3_extract_js($content);

        $files = array_map('w3_normalize_file_minify', $files);
        $files = array_unique($files);
        $ignore_files = $this->_config->get_array('minify.reject.files.js');
        $files = array_diff($files, $ignore_files);
        return $files;
    }

    /**
     * Parse content and return CSS recommendations
     *
     * @param string $content
     * @return array
     */
    function get_recommendations_css(&$content) {
        w3_require_once(W3TC_INC_DIR . '/functions/extract.php');

        $files = w3_extract_css($content);

        $files = array_map('w3_normalize_file_minify', $files);
        $files = array_unique($files);
        $ignore_files = $this->_config->get_array('minify.reject.files.css');
        $files = array_diff($files, $ignore_files);

        return $files;
    }
}