<?php

/**
 * Returns themes array
 *
 * @return array
 */
function w3_get_themes() {
    $themes = array();
    $wp_themes = w3tc_get_themes();

    foreach ($wp_themes as $wp_theme) {
        $theme_key = w3_get_theme_key($wp_theme['Theme Root'], $wp_theme['Template'], $wp_theme['Stylesheet']);
        $themes[$theme_key] = $wp_theme['Name'];
    }

    return $themes;
}

/**
 * Returns minify groups
 *
 * @param string $theme_name
 * @return array
 */
function w3_get_theme_templates($theme_name) {
    $groups = array(
        'default' => __('All Templates', 'w3-total-cache')
    );

    $templates = w3_get_theme_files($theme_name);

    foreach ($templates as $template) {
        $basename = basename($template, '.php');

        $groups[$basename] = ucfirst($basename);
    }

    return $groups;
}


/**
 * Returns array of theme groups
 *
 * @param string $theme_name
 * @return array
 */
function w3_get_theme_files($theme_name) {
    $patterns = array(
        '404',
        'search',
        'taxonomy(-.*)?',
        'front-page',
        'home',
        'index',
        '(image|video|text|audio|application).*',
        'attachment',
        'single(-.*)?',
        'page(-.*)?',
        'category(-.*)?',
        'tag(-.*)?',
        'author(-.*)?',
        'date',
        'archive',
        'comments-popup',
        'paged'
    );

    $templates = array();
    $theme = w3tc_get_theme($theme_name);

    if ($theme && isset($theme['Template Files'])) {
        $template_files = (array) $theme['Template Files'];

        foreach ($template_files as $template_file) {
            /**
             * Check file name
             */
            $template = basename($template_file, '.php');

            foreach ($patterns as $pattern) {
                $regexp = '~^' . $pattern . '$~';

                if (preg_match($regexp, $template)) {
                    $templates[] = $template_file;
                    continue 2;
                }
            }

            /**
             * Check get_header function call
             */
            $template_content = @file_get_contents($template_file);

            if ($template_content && preg_match('~\s*get_header[0-9_]*\s*\(~', $template_content)) {
                $templates[] = $template_file;
            }
        }

        sort($templates);
        reset($templates);
    }

    return $templates;
}

