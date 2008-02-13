<?php

// Registers dashboard widgets, handles POST data, sets up filters
function wp_dashboard_setup() {
	global $wpdb, $wp_dashboard_sidebars;
	$update = false;
	if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
		$widget_options = array();


	/* Register WP Dashboard Dynamic Sidebar */
	register_sidebar( array(
		'name' => 'WordPress Dashboard',
		'id' => 'wp_dashboard',
		'before_widget' => "\t<div class='dashboard-widget-holder %2\$s' id='%1\$s'>\n\n\t\t<div class='dashboard-widget'>\n\n",
		'after_widget' => "\t\t</div>\n\n\t</div>\n\n",
		'before_title' => "\t\t\t<h3 class='dashboard-widget-title'>",
		'after_title' => "</h3>\n\n"
	) );


	/* Register Widgets and Controls */

	// Recent Comments Widget
	if ( $mod_comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'") ) {
		$notice = sprintf( __ngettext( '%d comment awaiting moderation', '%d comments awaiting moderation', $mod_comments ), $mod_comments );
		$notice = "<a href='moderation.php'>$notice</a>";
	} else {
		$notice = '';
	}
	wp_register_sidebar_widget( 'dashboard_recent_comments', __( 'Recent Comments' ), 'wp_dashboard_recent_comments',
		array( 'all_link' => 'edit-comments.php', 'notice' => $notice, 'width' => 'half' )
	);


	// Incoming Links Widget
	if ( !isset( $widget_options['dashboard_incoming_links'] ) ) {
		$update = true;
		$widget_options['dashboard_incoming_links'] = array(
			'link' => apply_filters( 'dashboard_incoming_links_link', 'http://blogsearch.google.com/blogsearch?hl=en&scoring=d&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
			'url' => apply_filters( 'dashboard_incoming_links_feed', 'http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
			'items' => 5,
			'show_date' => 0
		);
	}
	wp_register_sidebar_widget( 'dashboard_incoming_links', __( 'Incoming Links' ), 'wp_dashboard_empty',
		array( 'all_link' => $widget_options['dashboard_incoming_links']['link'], 'feed_link' => $widget_options['dashboard_incoming_links']['url'], 'width' => 'half' )
	);
	wp_register_widget_control( 'dashboard_incoming_links', __( 'Incoming Links' ), 'wp_dashboard_rss_control', array(),
		array( 'widget_id' => 'dashboard_incoming_links', 'form_inputs' => array( 'title' => false, 'show_summary' => false, 'show_author' => false ) )
	);


	// WP Plugins Widget
	wp_register_sidebar_widget( 'dashboard_plugins', __( 'Plugins' ), 'wp_dashboard_empty',
		array( 'all_link' => 'http://wordpress.org/extend/plugins/', 'feed_link' => 'http://wordpress.org/extend/plugins/rss/', 'width' => 'half' )
	);
	wp_register_widget_control( 'dashboard_plugins', __( 'Plugins' ), 'wp_dashboard_empty', array(),
		array( 'widget_id' => 'dashboard_plugins' )
	);


	// Primary feed (Dev Blog) Widget
	if ( !isset( $widget_options['dashboard_primary'] ) ) {
		$update = true;
		$widget_options['dashboard_primary'] = array(
			'link' => apply_filters( 'dashboard_primary_link', 'http://wordpress.org/development/' ),
			'url' => apply_filters( 'dashboard_primary_feed', 'http://wordpress.org/development/feed/' ),
			'title' => apply_filters( 'dashboard_primary_title', __( 'WordPress Development Blog' ) ),
			'items' => 2,
			'show_summary' => 1,
			'show_author' => 0,
			'show_date' => 1
		);
	}
	wp_register_sidebar_widget( 'dashboard_primary', $widget_options['dashboard_primary']['title'], 'wp_dashboard_empty',
		array( 'all_link' => $widget_options['dashboard_primary']['link'], 'feed_link' => $widget_options['dashboard_primary']['url'], 'width' => 'half', 'class' => 'widget_rss' )
	);
	wp_register_widget_control( 'dashboard_primary', __( 'Primary Feed' ), 'wp_dashboard_rss_control', array(),
		array( 'widget_id' => 'dashboard_primary' )
	);


	// Secondary Feed (Planet) Widget
	if ( !isset( $widget_options['dashboard_secondary'] ) ) {
		$update = true;
		$widget_options['dashboard_secondary'] = array(
			'link' => apply_filters( 'dashboard_secondary_link', 'http://planet.wordpress.org/' ),
			'url' => apply_filters( 'dashboard_secondary_feed', 'http://planet.wordpress.org/feed/' ),
			'title' => apply_filters( 'dashboard_secondary_title', __( 'Other WordPress News' ) ),
			'items' => 15
		);
	}
	wp_register_sidebar_widget( 'dashboard_secondary', $widget_options['dashboard_secondary']['title'], 'wp_dashboard_empty',
		array( 'all_link' => $widget_options['dashboard_secondary']['link'], 'feed_link' => $widget_options['dashboard_secondary']['url'], 'width' => 'full' )
	);
	wp_register_widget_control( 'dashboard_secondary', __( 'Secondary Feed' ), 'wp_dashboard_rss_control', array(),
		array( 'widget_id' => 'dashboard_secondary', 'form_inputs' => array( 'show_summary' => false, 'show_author' => false, 'show_date' => false ) )
	);


	// Hook to register new widgets
	do_action( 'wp_dashboard_setup' );

	// Hard code the sidebar's widgets and order
	$dashboard_widgets = array( 'dashboard_recent_comments', 'dashboard_incoming_links', 'dashboard_primary', 'dashboard_plugins', 'dashboard_secondary' );

	// Filter widget order
	$dashboard_widgets = apply_filters( 'wp_dashboard_widgets', $dashboard_widgets );

	$wp_dashboard_sidebars = array( 'wp_dashboard' => $dashboard_widgets, 'array_version' => 3.5 );

	add_filter( 'dynamic_sidebar_params', 'wp_dashboard_dynamic_sidebar_params' );

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget_id']) ) {
		ob_start(); // hack - but the same hack wp-admin/widgets.php uses
		wp_dashbaord_trigger_widget_control( $_POST['widget_id'] );
		ob_end_clean();
		wp_redirect( remove_query_arg( 'edit' ) );
		exit;
	}

	if ( $update )
		update_option( 'dashboard_widget_options', $widget_options );
}

// Echoes out the dashboard
function wp_dashboard() {
	echo "<div id='dashboard-widgets'>\n\n";

	// We're already filtering dynamic_sidebar_params obove
	add_filter( 'option_sidebars_widgets', 'wp_dashboard_sidebars_widgets' ); // here there be hackery
	dynamic_sidebar( 'wp_dashboard' );
	remove_filter( 'option_sidebars_widgets', 'wp_dashboard_sidebars_widgets' );

	echo "<br class='clear' />\n</div>\n\n\n";
}

// Makes sidebar_widgets option reflect the dashboard settings
function wp_dashboard_sidebars_widgets() { // hackery
	return $GLOBALS['wp_dashboard_sidebars'];
}

// Modifies sidbar params on the fly to set up ids, class names, titles for each widget (called once per widget)
// Switches widget to edit mode if $_GET['edit']
function wp_dashboard_dynamic_sidebar_params( $params ) {
	global $wp_registered_widgets, $wp_registered_widget_controls;

	extract( $params[0], EXTR_PREFIX_ALL, 'sidebar' );
	extract( $wp_registered_widgets[$sidebar_widget_id], EXTR_PREFIX_ALL, 'widget' );

	$the_classes = array();
	if ( in_array($widget_width, array( 'third', 'fourth', 'full' ) ) )
		$the_classes[] = $widget_width;

	if ( 'double' == $widget_height )
		$the_classes[] = 'double';

	if ( $widget_class )
		$the_classes[] = $widget_class;

	// Add classes to the widget holder
	if ( $the_classes )
		$sidebar_before_widget = str_replace( "<div class='dashboard-widget-holder ", "<div class='dashboard-widget-holder " . join( ' ', $the_classes ) . ' ', $sidebar_before_widget );

	$links = array();
	if ( $widget_all_link )
		$links[] = '<a href="' . clean_url( $widget_all_link ) . '">' . __( 'See&nbsp;All' ) . '</a>';

	$content_class = 'dashboard-widget-content';
	if ( current_user_can( 'edit_dashboard' ) && isset($wp_registered_widget_controls[$widget_id]) && is_callable($wp_registered_widget_controls[$widget_id]['callback']) ) {
		// Switch this widget to edit mode
		if ( isset($_GET['edit']) && $_GET['edit'] == $widget_id ) {
			$content_class .= ' dashboard-widget-control';
			$wp_registered_widgets[$widget_id]['callback'] = 'wp_dashboard_empty';
			$sidebar_widget_name = $wp_registered_widget_controls[$widget_id]['name'];
			$params[1] = $widget_id;
			$sidebar_before_widget .= '<form action="' . remove_query_arg( 'edit' )  . '" method="post">';
			$sidebar_after_widget   = "<div class='dashboard-widget-submit'><input type='hidden' name='sidebar' value='wp_dashboard' /><input type='hidden' name='widget_id' value='$widget_id' /><input type='submit' value='" . __( 'Save' ) . "' /></div></form>$sidebar_after_widget";
			$links[] = '<a href="' . remove_query_arg( 'edit' ) . '">' . __( 'Cancel' ) . '</a>';
		} else {
			$links[] = '<a href="' . add_query_arg( 'edit', $widget_id ) . "#$widget_id" . '">' . __( 'Edit' ) . '</a>';
		}
	}

	if ( $widget_feed_link )
		$links[] = '<img class="rss-icon" src="' . get_option( 'siteurl' ) . '/' . WPINC . '/images/rss.png" alt="' . __( 'rss icon' ) . '" /> <a href="' . clean_url( $widget_feed_link ) . '">' . __( 'RSS' ) . '</a>';

	$links = apply_filters( "wp_dashboard_widget_links_$widget_id", $links );

	// Add links to widget's title bar
	if ( $links ) {
		$sidebar_before_title .= '<span>';
		$sidebar_after_title   = '</span><small>' . join( '&nbsp;|&nbsp;', $links ) . "</small><br class='clear' />$sidebar_after_title";
	}

	// Could have put this in widget-content.  Doesn't really matter
	if ( $widget_notice )
		$sidebar_after_title .= "\t\t\t<div class='dashboard-widget-notice'>$widget_notice</div>\n\n";

	if ( $widget_error )
		$sidebar_after_title .= "\t\t\t<div class='dashboard-widget-error'>$widget_error</div>\n\n";

	$sidebar_after_title .= "\t\t\t<div class='$content_class'>\n\n";

	$sidebar_after_widget .= "\t\t\t</div>\n\n";

	foreach( array_keys( $params[0] ) as $key )
		$$key = ${'sidebar_' . $key};

	$params[0] = compact( array_keys( $params[0] ) );

	return $params;
}


/* Dashboard Widgets */

function wp_dashboard_recent_comments( $sidebar_args ) {
	global $comment;

	extract( $sidebar_args, EXTR_SKIP );

	echo $before_widget;

	echo $before_title;
	echo $widget_name;
	echo $after_title;

	$lambda = create_function( '', 'return 5;' );
	add_filter( 'option_posts_per_rss', $lambda ); // hack - comments query doesn't accept per_page parameter
	$comments_query = new WP_Query('feed=rss2&withcomments=1');
	remove_filter( 'option_posts_per_rss', $lambda );

	$is_first = true;

	if ( $comments_query->have_comments() ) {
		while ( $comments_query->have_comments() ) { $comments_query->the_comment();

			$comment_post_url = get_permalink( $comment->comment_post_ID );
			$comment_post_title = get_the_title( $comment->comment_post_ID );
			$comment_post_link = "<a href='$comment_post_url'>$comment_post_title</a>";
			$comment_link = '<a class="comment-link" href="' . get_comment_link() . '">#</a>';
			$comment_meta = sprintf( __( 'From <strong>%s</strong> on %s %s' ), get_comment_author(), $comment_post_link, $comment_link );

			if ( $is_first ) : $is_first = false;
?>
				<blockquote><?php comment_text(); ?></blockquote>
				<p class='comment-meta'><?php echo $comment_meta; ?></p>

				<ul id="dashboard-comments-list">
<?php
			else :
?>

					<li class='comment-meta'><?php echo $comment_meta; ?></li>
<?php
			endif;
		}
?>

				</ul>

<?php

	}

	echo $after_widget;
}

// Empty widget used for JS/AJAX created output.  Also used when widget is in edit mode.
function wp_dashboard_empty( $sidebar_args, $widget_control_id = false ) {
	extract( $sidebar_args, EXTR_SKIP );

	echo $before_widget;

	echo $before_title;
	echo $widget_name;
	echo $after_title;

	if ( $widget_control_id ) // If in edit mode
		wp_dashbaord_trigger_widget_control( $widget_control_id );

	echo $after_widget;
}

/* Dashboard Widgets Controls. Ssee also wp_dashboard_empty() */

// Calls widget_control callback
function wp_dashbaord_trigger_widget_control( $widget_control_id = false ) {
	global $wp_registered_widget_controls;
	if ( is_scalar($widget_control_id) && $widget_control_id && isset($wp_registered_widget_controls[$widget_control_id]) && is_callable($wp_registered_widget_controls[$widget_control_id]['callback']) )
		call_user_func_array( $wp_registered_widget_controls[$widget_control_id]['callback'], $wp_registered_widget_controls[$widget_control_id]['params'] );
}

// Sets up $args to be used as input to wp_widget_rss_form(), handles POST data from RSS-type widgets
function wp_dashboard_rss_control( $args ) {
	extract( $args );
	if ( !$widget_id )
		return false;

	if ( !$widget_options = get_option( 'dashboard_widget_options' ) )
		$widget_options = array();

	if ( !isset($widget_options[$widget_id]) )
		$widget_options[$widget_id] = array();

	$number = 1; // Hack to use wp_widget_rss_form()
	$widget_options[$widget_id]['number'] = $number;

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-rss'][$number]) ) {
		$_POST['widget-rss'][$number] = stripslashes_deep( $_POST['widget-rss'][$number] );
		$widget_options[$widget_id] = wp_widget_rss_process( $_POST['widget-rss'][$number] );
		// title is optional.  If black, fill it if possible
		if ( !$widget_options[$widget_id]['title'] && isset($_POST['widget-rss'][$number]['title']) ) {
			require_once(ABSPATH . WPINC . '/rss.php');
			$rss = fetch_rss($widget_options[$widget_id]['url']);
			$widget_options[$widget_id]['title'] = htmlentities(strip_tags($rss->channel['title']));
		}
		update_option( 'dashboard_widget_options', $widget_options );
	}

	wp_widget_rss_form( $widget_options[$widget_id], $form_inputs );
}

?>
