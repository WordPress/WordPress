<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_Minify extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'minify.engine' => __('Minify cache method:', 'w3-total-cache'),
                'minify.enabled' => __('Minify:', 'w3-total-cache'),
                'minify.debug' =>  __('Minify', 'w3-total-cache'),
                'minify.html.engine' => __('<acronym title="Hypertext Markup Language">HTML</acronym> minifier:', 'w3-total-cache'),
                'minify.js.engine' =>  __('<acronym title="JavaScript">JS</acronym> minifier:', 'w3-total-cache'),
                'minify.css.engine'=>  __('<acronym title="Cascading Style Sheets">CSS</acronym> minifier:', 'w3-total-cache'),
                'minify.auto' => __('Minify mode:', 'w3-total-cache')
            ),
            'settings' => array(
                'minify.rewrite' => __('Rewrite <acronym title="Uniform Resource Locator">URL</acronym> structure', 'w3-total-cache'),
                'minify.reject.logged' => __('Disable minify for logged in users', 'w3-total-cache'),
                'minify.error.notification' => __('Minify error notification:', 'w3-total-cache'),
                'minify.html.enable' => __('Enable', 'w3-total-cache'),
                'minify.html.inline.css' => __('Inline <acronym title="Cascading Style Sheet">CSS</acronym> minification', 'w3-total-cache'),
                'minify.html.inline.js' => __('Inline <acronym title="JavaScript">JS</acronym> minification', 'w3-total-cache'),
                'minify.html.reject.feed' => __('Don\'t minify feeds', 'w3-total-cache'),
                'minify.html.comments.ignore' => __('Ignored comment stems:', 'w3-total-cache'),
                'minify.js.enable' => __('Enable', 'w3-total-cache'),
                'minify.js.header.embed_type' => __('Embed type:', 'w3-total-cache'),
                'minify.js.combine.header' =>  __('Combine only', 'w3-total-cache'),
                'minify.js.body.embed_type' => __('After <span class="html-tag">&lt;body&gt;</span>', 'w3-total-cache'),
                'minify.js.combine.body' => __('Combine only', 'w3-total-cache'),
                'minify.js.footer.embed_type' => __('Before <span class="html-tag">&lt;/body&gt;</span>', 'w3-total-cache'),
                'minify.js.combine.footer' => __('Combine only', 'w3-total-cache'),
                'minify.css.enable' => __('Enable', 'w3-total-cache'),
                'minify.css.combine' => __('Combine only', 'w3-total-cache'),
                'minify.css.imports' => __('@import handling:', 'w3-total-cache'),
                'minify.auto.disable_filename_length_test' => __('Disable minify automatic file name length test', 'w3-total-cache'),
                'minify.auto.filename_length' => __('Filename length:', 'w3-total-cache'),
                'minify.memcached.servers' => __('Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:', 'w3-total-cache'),
                'minify.lifetime' => __('Update external files every:', 'w3-total-cache'),
                'minify.file.gc' => __('Garbage collection interval:', 'w3-total-cache'),
                'minify.reject.uri' => __('Never minify the following pages:', 'w3-total-cache'),
                'minify.reject.files.js' => __('Never minify the following JS files:', 'w3-total-cache'),
                'minify.reject.files.css' => __('Never minify the following CSS files:', 'w3-total-cache'),
                'minify.reject.ua' => __('Rejected user agents:', 'w3-total-cache'),
                'minify.cache.files' => __('Include external files/libaries:', 'w3-total-cache'),
// options->minify->ccjs
                'minify.ccjs.options.formatting' => __('Pretty print', 'w3-total-cache'),
// options->minify->ccjs2
                'minify.ccjs.path.java' => __('Path to JAVA executable:', 'w3-total-cache'),
                'minify.ccjs.path.jar' => __('Path to JAR file:', 'w3-total-cache'),
                'minify.ccjs.options.compilation_level' => __('Compilation level:', 'w3-total-cache'),
// options->minify->css
                'minify.css.strip.comments' => __('Preserved comment removal (not applied when combine only is active)', 'w3-total-cache'),
                'minify.css.strip.crlf' => __('Line break removal (not applied when combine only is active)', 'w3-total-cache'),
// options->minify->csstidy
                'minify.csstidy.options.remove_bslash' => __('Remove unnecessary backslashes', 'w3-total-cache'),
                'minify.csstidy.options.compress_colors' => __('Compress colors', 'w3-total-cache'),
                'minify.csstidy.options.compress_font-weight' => __('Compress font-weight', 'w3-total-cache'),
                'minify.csstidy.options.lowercase_s' => __('Lowercase selectors', 'w3-total-cache'),
                'minify.csstidy.options.remove_last_;' => __('Remove last ;', 'w3-total-cache'),
                'minify.csstidy.options.sort_properties' => __('Sort Properties', 'w3-total-cache'),
                'minify.csstidy.options.sort_selectors' => __('Sort Selectors (caution)', 'w3-total-cache'),
                'minify.csstidy.options.discard_invalid_properties' => __('Discard invalid properties', 'w3-total-cache'),
                'minify.csstidy.options.preserve_css' => __('Preserve CSS', 'w3-total-cache'),
                'minify.csstidy.options.timestamp' => __('Add timestamp', 'w3-total-cache'),
// options->minify->csstidy2
                'minify.csstidy.options.template' => __('Compression:', 'w3-total-cache'),
                'minify.csstidy.options.optimise_shorthands' => __('Optimize shorthands:', 'w3-total-cache'),
                'minify.csstidy.options.case_properties' => __('Case for properties:', 'w3-total-cache'),
                'minify.csstidy.options.merge_selectors' => __('Regroup selectors:', 'w3-total-cache'),
// options->minify->html
                'minify.html.strip.crlf' => __('Line break removal', 'w3-total-cache'),
// options->minify_>htmltidy
                'minify.htmltidy.options.clean' => __('Clean', 'w3-total-cache'),
                'minify.htmltidy.options.hide-comments' => __('Hide comments', 'w3-total-cache'),
// options->minify->htmltidy2
                'minify.htmltidy.options.wrap' => __('Wrap after:', 'w3-total-cache'),
// options->minify->js
                'minify.js.strip.comments' => __('Preserved comment removal (not applied when combine only is active)', 'w3-total-cache'),
                'minify.js.strip.crlf' => __('Line break removal (not safe, not applied when combine only is active)', 'w3-total-cache'),
// options->minify->yuicss2
                'minify.yuicss.path.java' => __('Path to JAVA executable:', 'w3-total-cache'),
                'minify.yuicss.path.jar' => __('Path to JAR file:', 'w3-total-cache'),
                'minify.yuicss.options.line-break' => __('Line break after:', 'w3-total-cache'),
// options->minify->yuijs
                'minify.yuijs.options.nomunge' => __('Minify only, do not obfuscate local symbols', 'w3-total-cache'),
                'minify.yuijs.options.preserve-semi' => __('Preserve unnecessary semicolons', 'w3-total-cache'),
                'minify.yuijs.options.disable-optimizations' => __('Disable all the built-in micro optimizations', 'w3-total-cache'),
                'minify.yuijs.options.line-break' => __('Line break after:', 'w3-total-cache')
    )
        );
    }
}