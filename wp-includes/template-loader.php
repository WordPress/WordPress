<?php
/**
 * Loads the correct template based on the visitor's url
 * @package WordPress
 */
if ( defined('WP_USE_THEMES') && constant('WP_USE_THEMES') ) {
	do_action('template_redirect');
	if ( is_robots() ) {
		do_action('do_robots');
		return;
	} else if ( is_feed() ) {
		do_feed();
		return;
	} else if ( is_trackback() ) {
		include(ABSPATH . 'wp-trackback.php');
		return;
	} else if ( is_404() && $template = get_404_template() ) {
		include($template);
		return;
	} else if ( is_search() && $template = get_search_template() ) {
		include($template);
		return;
	} else if ( is_home() && $template = get_home_template() ) {
		include($template);
		return;
	} else if ( is_attachment() && $template = get_attachment_template() ) {
		remove_filter('the_content', 'prepend_attachment');
		include($template);
		return;
	} else if ( is_single() && $template = get_single_template() ) {
		include($template);
		return;
	} else if ( is_page() && $template = get_page_template() ) {
		include($template);
		return;
	} else if ( is_category() && $template = get_category_template()) {
		include($template);
		return;
	} else if ( is_tag() && $template = get_tag_template()) {
		include($template);
		return;
	} else if ( is_tax() && $template = get_taxonomy_template()) {
		include($template);
		return;
	} else if ( is_author() && $template = get_author_template() ) {
		include($template);
		return;
	} else if ( is_date() && $template = get_date_template() ) {
		include($template);
		return;
	} else if ( is_archive() && $template = get_archive_template() ) {
		include($template);
		return;
	} else if ( is_comments_popup() && $template = get_comments_popup_template() ) {
		include($template);
		return;
	} else if ( is_paged() && $template = get_paged_template() ) {
		include($template);
		return;
	} else if ( file_exists(TEMPLATEPATH . "/index.php") ) {
		include(TEMPLATEPATH . "/index.php");
		return;
	}
} else {
	// Process feeds and trackbacks even if not using themes.
	if ( is_robots() ) {
		do_action('do_robots');
		return;
	} else if ( is_feed() ) {
		do_feed();
		return;
	} else if ( is_trackback() ) {
		include(ABSPATH . 'wp-trackback.php');
		return;
	}
}

?>