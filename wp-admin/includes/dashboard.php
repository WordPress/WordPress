<?php

// Registers dashboard widgets, handles POST data, sets up filters
function wp_dashboard_setup() {
	global $wpdb, $wp_dashboard_sidebars;
	$update = false;
	$widget_options = get_option( 'dashboard_widget_options' );
	if ( !$widget_options || !is_array($widget_options) )
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
	$mod_comments = wp_count_comments();
	$mod_comments = $mod_comments->moderated;
	if ( current_user_can( 'moderate_comments' ) && $mod_comments ) {
		$notice = sprintf( __ngettext( '%d comment awaiting moderation', '%d comments awaiting moderation', $mod_comments ), $mod_comments );
		$notice = "<a href='edit-comments.php?comment_status=moderated'>$notice</a>";
	} else {
		$notice = '';
	}
	wp_register_sidebar_widget( 'dashboard_recent_comments', __( 'Recent Comments' ), 'wp_dashboard_recent_comments',
		array( 'all_link' => 'edit-comments.php', 'notice' => $notice, 'width' => 'half' )
	);

	// Incoming Links Widget
	if ( !isset( $widget_options['dashboard_incoming_links'] ) || !isset( $widget_options['dashboard_incoming_links']['home'] ) || $widget_options['dashboard_incoming_links']['home'] != get_option('home') ) {
		$update = true;
		$widget_options['dashboard_incoming_links'] = array(
			'home' => get_option('home'),
			'link' => apply_filters( 'dashboard_incoming_links_link', 'http://blogsearch.google.com/blogsearch?hl=en&scoring=d&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
			'url' => apply_filters( 'dashboard_incoming_links_feed', 'http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&partner=wordpress&q=link:' . trailingslashit( get_option('home') ) ),
			'items' => 5,
			'show_date' => 0
		);
	}
	wp_register_sidebar_widget( 'dashboard_incoming_links', __( 'Incoming Links' ), 'wp_dashboard_empty',
		array( 'all_link' => $widget_options['dashboard_incoming_links']['link'], 'feed_link' => $widget_options['dashboard_incoming_links']['url'], 'width' => 'half' ),
		'wp_dashboard_cached_rss_widget', 'wp_dashboard_incoming_links_output'
	);
	wp_register_widget_control( 'dashboard_incoming_links', __( 'Incoming Links' ), 'wp_dashboard_rss_control', array(),
		array( 'widget_id' => 'dashboard_incoming_links', 'form_inputs' => array( 'title' => false, 'show_summary' => false, 'show_author' => false ) )
	);


	// WP Plugins Widget
	wp_register_sidebar_widget( 'dashboard_plugins', __( 'Plugins' ), 'wp_dashboard_empty',
		array( 'all_link' => 'http://wordpress.org/extend/plugins/', 'feed_link' => 'http://wordpress.org/extend/plugins/rss/topics/', 'width' => 'half' ),
		'wp_dashboard_cached_rss_widget', 'wp_dashboard_plugins_output',
		array( 'http://wordpress.org/extend/plugins/rss/browse/popular/', 'http://wordpress.org/extend/plugins/rss/browse/new/', 'http://wordpress.org/extend/plugins/rss/browse/updated/' )
	);

	// Primary feed (Dev Blog) Widget
	if ( !isset( $widget_options['dashboard_primary'] ) ) {
		$update = true;
		$widget_options['dashboard_primary'] = array(
			'link' => apply_filters( 'dashboard_primary_link',  __( 'http://wordpress.org/development/' ) ),
			'url' => apply_filters( 'dashboard_primary_feed',  __( 'http://wordpress.org/development/feed/' ) ),
			'title' => apply_filters( 'dashboard_primary_title', __( 'WordPress Development Blog' ) ),
			'items' => 2,
			'show_summary' => 1,
			'show_author' => 0,
			'show_date' => 1
		);
	}
	wp_register_sidebar_widget( 'dashboard_primary', $widget_options['dashboard_primary']['title'], 'wp_dashboard_empty',
		array( 'all_link' => $widget_options['dashboard_primary']['link'], 'feed_link' => $widget_options['dashboard_primary']['url'], 'width' => 'half', 'class' => 'widget_rss' ),
		'wp_dashboard_cached_rss_widget', 'wp_dashboard_rss_output'
	);
	wp_register_widget_control( 'dashboard_primary', __( 'Primary Feed' ), 'wp_dashboard_rss_control', array(),
		array( 'widget_id' => 'dashboard_primary' )
	);


	// Secondary Feed (Planet) Widget
	if ( !isset( $widget_options['dashboard_secondary'] ) ) {
		$update = true;
		$widget_options['dashboard_secondary'] = array(
			'link' => apply_filters( 'dashboard_secondary_link',  __( 'http://planet.wordpress.org/' ) ),
			'url' => apply_filters( 'dashboard_secondary_feed',  __( 'http://planet.wordpress.org/feed/' ) ),
			'title' => apply_filters( 'dashboard_secondary_title', __( 'Other WordPress News' ) ),
			'items' => 15
		);
	}
	wp_register_sidebar_widget( 'dashboard_secondary', $widget_options['dashboard_secondary']['title'], 'wp_dashboard_empty',
		array( 'all_link' => $widget_options['dashboard_secondary']['link'], 'feed_link' => $widget_options['dashboard_secondary']['url'], 'width' => 'full' ),
		'wp_dashboard_cached_rss_widget', 'wp_dashboard_secondary_output'
	);
	wp_register_widget_control( 'dashboard_secondary', __( 'Secondary Feed' ), 'wp_dashboard_rss_control', array(),
		array( 'widget_id' => 'dashboard_secondary', 'form_inputs' => array( 'show_summary' => false, 'show_author' => false, 'show_date' => false ) )
	);


		/* Dashboard Widget Template
		wp_register_sidebar_widget( $widget_id (unique slug) , $widget_title, $output_callback,
			array(
				'all_link'  => full url for "See All" link,
				'feed_link' => full url for "RSS" link,
				'width'     => 'fourth', 'third', 'half', 'full' (defaults to 'half'),
				'height'    => 'single', 'double' (defaults to 'single'),
			),
			$wp_dashboard_empty_callback (only needed if using 'wp_dashboard_empty' as your $output_callback),
			$arg, $arg, $arg... (further args passed to callbacks)
		);

		// optional: if you want users to be able to edit the settings of your widget, you need to register a widget_control
		wp_register_widget_control( $widget_id, $widget_control_title, $control_output_callback,
			array(), // leave an empty array here: oddity in widget code
			array(
				'widget_id' => $widget_id, // Yes - again.  This is required: oddity in widget code
				'arg'       => an arg to pass to the $control_output_callback,
				'another'   => another arg to pass to the $control_output_callback,
				...
			)
		);
		*/

	// Hook to register new widgets
	do_action( 'wp_dashboard_setup' );

	// Hard code the sidebar's widgets and order
	$dashboard_widgets = array();
	$dashboard_widgets[] = 'dashboard_recent_comments';
	$dashboard_widgets[] = 'dashboard_incoming_links';
	$dashboard_widgets[] = 'dashboard_primary';
	if ( current_user_can( 'activate_plugins' ) )
		$dashboard_widgets[] = 'dashboard_plugins';
	$dashboard_widgets[] = 'dashboard_secondary';

	// Filter widget order
	$dashboard_widgets = apply_filters( 'wp_dashboard_widgets', $dashboard_widgets );

	$wp_dashboard_sidebars = array( 'wp_dashboard' => $dashboard_widgets, 'array_version' => 3.5 );

	add_filter( 'dynamic_sidebar_params', 'wp_dashboard_dynamic_sidebar_params' );

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget_id']) ) {
		ob_start(); // hack - but the same hack wp-admin/widgets.php uses
		wp_dashboard_trigger_widget_control( $_POST['widget_id'] );
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

	$sidebar_defaults = array('widget_id' => 0, 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '');
	extract( $sidebar_defaults, EXTR_PREFIX_ALL, 'sidebar' );
	extract( $params[0], EXTR_PREFIX_ALL, 'sidebar' );

	if ( !isset($wp_registered_widgets[$sidebar_widget_id]) || !is_array($wp_registered_widgets[$sidebar_widget_id]) ) {
		return $params;
	}
	$widget_defaults = array('id' => '', 'width' => '', 'height' => '', 'class' => '', 'feed_link' => '', 'all_link' => '', 'notice' => false, 'error' => false);
	extract( $widget_defaults, EXTR_PREFIX_ALL, 'widget' );
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
			$params[1] = 'wp_dashboard_trigger_widget_control';
			$sidebar_before_widget .= '<form action="' . clean_url(remove_query_arg( 'edit' ))  . '" method="post">';
			$sidebar_after_widget   = "<div class='dashboard-widget-submit'><input type='hidden' name='sidebar' value='wp_dashboard' /><input type='hidden' name='widget_id' value='$widget_id' /><input type='submit' value='" . __( 'Save' ) . "' /></div></form>$sidebar_after_widget";
			$links[] = '<a href="' . clean_url(remove_query_arg( 'edit' )) . '">' . __( 'Cancel' ) . '</a>';
		} else {
			$links[] = '<a href="' . clean_url(add_query_arg( 'edit', $widget_id )) . "#$widget_id" . '">' . __( 'Edit' ) . '</a>';
		}
	}

	if ( $widget_feed_link )
		$links[] = '<img class="rss-icon" src="' . includes_url('images/rss.png') . '" alt="' . __( 'rss icon' ) . '" /> <a href="' . clean_url( $widget_feed_link ) . '">' . __( 'RSS' ) . '</a>';

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
	$comments_query = new WP_Query(array('feed' => 'rss2', 'withcomments' => 1));
	remove_filter( 'option_posts_per_rss', $lambda );

	$is_first = true;

	if ( $comments_query->have_comments() ) {
		while ( $comments_query->have_comments() ) { $comments_query->the_comment();

			$comment_post_url = get_permalink( $comment->comment_post_ID );
			$comment_post_title = get_the_title( $comment->comment_post_ID );
			$comment_post_link = "<a href='$comment_post_url'>$comment_post_title</a>";
			$comment_link = '<a class="comment-link" href="' . get_comment_link() . '">#</a>';
			$comment_meta = sprintf( __( 'From <strong>%1$s</strong> on %2$s %3$s' ), get_comment_author(), $comment_post_link, $comment_link );

			if ( $is_first ) : $is_first = false;
?>
				<blockquote><p>&#8220;<?php comment_excerpt(); ?>&#8221;</p></blockquote>
				<p class='comment-meta'><?php echo $comment_meta; ?></p>
<?php
				if ( $comments_query->comment_count > 1 ) : ?>
				<ul id="dashboard-comments-list">
<?php
				endif; // comment_count
			else : // is_first
?>

					<li class='comment-meta'><?php echo $comment_meta; ?></li>
<?php
			endif; // is_first
		}

		if ( $comments_query->comment_count > 1 ) : ?>
				</ul>
<?php
		endif; // comment_count;

	}

	echo $after_widget;
}

// $sidebar_args are handled by wp_dashboard_empty()
function wp_dashboard_incoming_links_output() {
	$widgets = get_option( 'dashboard_widget_options' );
	@extract( @$widgets['dashboard_incoming_links'], EXTR_SKIP );
	$rss = @fetch_rss( $url );
	if ( isset($rss->items) && 0 < count($rss->items) )  {

		echo "<ul>\n";

		$rss->items = array_slice($rss->items, 0, $items);
		foreach ( $rss->items as $item ) {
			$publisher = '';
			$site_link = '';
			$link = '';
			$content = '';
			$date = '';
			$link = clean_url( strip_tags( $item['link'] ) );

			if ( isset( $item['author_uri'] ) )
				$site_link = clean_url( strip_tags( $item['author_uri'] ) );

			if ( !$publisher = wp_specialchars( strip_tags( isset($item['dc']['publisher']) ? $item['dc']['publisher'] : $item['author_name'] ) ) )
				$publisher = __( 'Somebody' );
			if ( $site_link )
				$publisher = "<a href='$site_link'>$publisher</a>";
			else
				$publisher = "<strong>$publisher</strong>";

			if ( isset($item['description']) )
				$content = $item['description'];
			elseif ( isset($item['summary']) )
				$content = $item['summary'];
			elseif ( isset($item['atom_content']) )
				$content = $item['atom_content'];
			else
				$content = __( 'something' );
			$content = wp_html_excerpt($content, 50) . ' ...';
			if ( $link )
				$text = _c( '%1$s linked here <a href="%2$s">saying</a>, "%3$s"|feed_display' );
			else
				$text = _c( '%1$s linked here saying, "%3$s"|feed_display' );

			if ( $show_date ) {
				if ( $show_author || $show_summary )
					$text .= _c( ' on %4$s|feed_display' );
				$date = wp_specialchars( strip_tags( isset($item['pubdate']) ? $item['pubdate'] : $item['published'] ) );
				$date = strtotime( $date );
				$date = gmdate( get_option( 'date_format' ), $date );
			}

			echo "\t<li>" . sprintf( _c( "$text|feed_display" ), $publisher, $link, $content, $date ) . "</li>\n";
		}

		echo "</ul>\n";

	} else {
		echo '<p>' . __('This dashboard widget queries <a href="http://blogsearch.google.com/">Google Blog Search</a> so that when another blog links to your site it will show up here. It has found no incoming links&hellip; yet. It&#8217;s okay &#8212; there is no rush.') . "</p>\n";
	}
}

// $sidebar_args are handled by wp_dashboard_empty()
function wp_dashboard_rss_output( $widget_id ) {
	$widgets = get_option( 'dashboard_widget_options' );
	wp_widget_rss_output( $widgets[$widget_id] );
}

// $sidebar_args are handled by wp_dashboard_empty()
function wp_dashboard_secondary_output() {
	$widgets = get_option( 'dashboard_widget_options' );
	@extract( @$widgets['dashboard_secondary'], EXTR_SKIP );
	$rss = @fetch_rss( $url );
	if ( !isset($rss->items) || 0 == count($rss->items) )
		return false;

	echo "<ul id='planetnews'>\n";

	$rss->items = array_slice($rss->items, 0, $items);
	foreach ($rss->items as $item ) {
		$title = wp_specialchars($item['title']);
		list($author,$post) = explode( ':', $title, 2 );
		$link = clean_url($item['link']);

		echo "\t<li><a href='$link'><span class='post'>$post</span><span class='hidden'> - </span><cite>$author</cite></a></li>\n";
	}

	echo "</ul>\n<br class='clear' />\n";
}

// $sidebar_args are handled by wp_dashboard_empty()
function wp_dashboard_plugins_output() {
	$popular = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/popular/' );
	$new     = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/new/' );
	$updated = @fetch_rss( 'http://wordpress.org/extend/plugins/rss/browse/updated/' );

	foreach ( array( 'popular' => __('Most Popular'), 'new' => __('Newest Plugins'), 'updated' => __('Recently Updated') ) as $feed => $label ) {
		if ( !isset($$feed->items) || 0 == count($$feed->items) )
			continue;

		$$feed->items = array_slice($$feed->items, 0, 5);
		$item_key = array_rand($$feed->items);

		// Eliminate some common badly formed plugin descriptions
		while ( ( null !== $item_key = array_rand($$feed->items) ) && false !== strpos( $$feed->items[$item_key]['description'], 'Plugin Name:' ) )
			unset($$feed->items[$item_key]);

		if ( !isset($$feed->items[$item_key]) )
			continue;

		$item = $$feed->items[$item_key];

		// current bbPress feed item titles are: user on "topic title"
		if ( preg_match( '/"(.*)"/s', $item['title'], $matches ) )
			$title = $matches[1];
		else // but let's make it forward compatible if things change
			$title = $item['title'];
		$title = wp_specialchars( $title );

		$description = wp_specialchars( strip_tags(html_entity_decode($item['description'], ENT_QUOTES)) );

		list($link, $frag) = explode( '#', $item['link'] );

		$link = clean_url($link);
		$dlink = rtrim($link, '/') . '/download/';

		echo "<h4>$label</h4>\n";
		echo "<h5><a href='$link'>$title</a></h5>&nbsp;<span>(<a href='$dlink'>" . __( 'Download' ) . "</a>)</span>\n";
		echo "<p>$description</p>\n";
	}
}

// Checks to see if all of the feed url in $check_urls are cached.
// If $check_urls is empty, look for the rss feed url found in the dashboard widget optios of $widget_id.
// If cached, call $callback, a function that echoes out output for this widget.
// If not cache, echo a "Loading..." stub which is later replaced by AJAX call (see top of /wp-admin/index.php)
function wp_dashboard_cached_rss_widget( $widget_id, $callback, $check_urls = array() ) {
	$loading = '<p class="widget-loading">' . __( 'Loading&#8230;' ) . '</p>';

	if ( empty($check_urls) ) {
		$widgets = get_option( 'dashboard_widget_options' );
		if ( empty($widgets[$widget_id]['url']) ) {
			echo $loading;
			return false;
		}
		$check_urls = array( $widgets[$widget_id]['url'] );
	}


	require_once( ABSPATH . WPINC . '/rss.php' );
	init(); // initialize rss constants

	$cache = new RSSCache( MAGPIE_CACHE_DIR, MAGPIE_CACHE_AGE );

	foreach ( $check_urls as $check_url ) {
		$status = $cache->check_cache( $check_url );
		if ( 'HIT' !== $status ) {
			echo $loading;
			return false;
		}
	}

	if ( $callback && is_callable( $callback ) ) {
		$args = array_slice( func_get_args(), 2 );
		array_unshift( $args, $widget_id );
		call_user_func_array( $callback, $args );
	}

	return true;
}

// Empty widget used for JS/AJAX created output.
// Callback inserts content between before_widget and after_widget.  Used when widget is in edit mode.  Can also be used for custom widgets.
function wp_dashboard_empty( $sidebar_args, $callback = false ) {
	extract( $sidebar_args, EXTR_SKIP );

	echo $before_widget;

	echo $before_title;
	echo $widget_name;
	echo $after_title;

	// When in edit mode, the callback passed to this function is the widget_control callback
	if ( $callback && is_callable( $callback ) ) {
		$args = array_slice( func_get_args(), 2 );
		array_unshift( $args, $widget_id );
		call_user_func_array( $callback, $args );
	}

	echo $after_widget;
}

/* Dashboard Widgets Controls. Ssee also wp_dashboard_empty() */

// Calls widget_control callback
function wp_dashboard_trigger_widget_control( $widget_control_id = false ) {
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
