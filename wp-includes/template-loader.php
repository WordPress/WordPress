<?php
if ( defined('WP_USE_THEMES') && constant('WP_USE_THEMES') ) {
	do_action('template_redirect');
	if ( is_feed() ) {
		include(ABSPATH . '/wp-feed.php');
		exit;
	} else if ( is_trackback() ) {
		include(ABSPATH . '/wp-trackback.php');
		exit;
	} else if ( is_404() && get_404_template() ) {
		include(get_404_template());
		exit;
	} else if ( is_search() && get_search_template() ) {
		include(get_search_template());
		exit;
	} else if ( is_home() && get_home_template() ) {
		include(get_home_template());
		exit;
	} else if ( is_subpost() && get_subpost_template() ) {
		include(get_subpost_template());
		exit;
	} else if ( is_single() && get_single_template() ) {
		if ( is_subpost() )
			add_filter('the_content', 'prepend_object');
		include(get_single_template());
		exit;
	} else if ( is_page() && get_page_template() ) {
		if ( is_subpost() )
			add_filter('the_content', 'prepend_object');
		include(get_page_template());
		exit;
	} else if ( is_category() && get_category_template()) {
		include(get_category_template());
		exit;		
	} else if ( is_author() && get_author_template() ) {
		include(get_author_template());
		exit;
	} else if ( is_date() && get_date_template() ) {
		include(get_date_template());
		exit;
	} else if ( is_archive() && get_archive_template() ) {
		include(get_archive_template());
		exit;
	} else if ( is_comments_popup() && get_comments_popup_template() ) {
		include(get_comments_popup_template());
		exit;
	} else if ( is_paged() && get_paged_template() ) {
		include(get_paged_template());
		exit;
	} else if ( file_exists(TEMPLATEPATH . "/index.php") ) {
		include(TEMPLATEPATH . "/index.php");
		exit;
	}
} else {
	// Process feeds and trackbacks even if not using themes.
	if ( is_feed() ) {
		include(ABSPATH . '/wp-feed.php');
		exit;
	} else if ( is_trackback() ) {
		include(ABSPATH . '/wp-trackback.php');
		exit;
	}
}

?>
