<?php

	/* if uninstall not called from WordPress exit */

	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
		exit ();

	/* Delete all existence of this plugin */

	global $wpdb;

	$blog_option_name = 'wpsite_follow_us_settings';
	$version_option_name = 'wpsite_follow_us_badges_version';

	if ( !is_multisite() ) {
		delete_option($version_option_name);
	} else {
		delete_site_option($version_option_name);

		/* Used to delete each option from each blog */

	    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	    foreach ( $blog_ids as $blog_id ) {
	        switch_to_blog( $blog_id );

	        /* Delete blog option */

			delete_option($blog_option_name);
	    }

	    restore_current_blog();
	}
?>