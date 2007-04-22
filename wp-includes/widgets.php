<?php

/* Global Variables */

$wp_registered_sidebars = array();
$wp_registered_widgets = array();
$wp_registered_widget_controls = array();
$wp_registered_widget_styles = array();
$wp_register_widget_defaults = false;

/* Template tags & API functions */

if ( !function_exists( 'register_sidebars' ) ) {
	function register_sidebars( $number = 1, $args = array() ) {
		$number = (int) $number;
	
		if ( is_string( $args ) ) {
			parse_str( $args, $args );
		}
	
		$name = ( !empty( $args['name'] ) ) ? $args['name'] : __( 'Sidebar' );
	
		for ( $i = 1; $i <= $number; $i++ ) {
			if ( isset( $args['name'] ) && $number > 1 ) {
				if ( strpos( $name, '%d' ) === false ) {
					$name = $name . ' %d';
				}
			
				$args['name'] = sprintf( $name, $i );
			}
		
			register_sidebar( $args );
		}
	}
}

if ( !function_exists( 'register_sidebar' ) ) {
	function register_sidebar( $args = array() ) {
		global $wp_registered_sidebars;
		
		if ( is_string( $args ) ) {
			parse_str( $args, $args );
		}
		
		$defaults = array(
			'name' => sprintf( __( 'Sidebar %d' ), count( $wp_registered_sidebars ) + 1 ), 
			'before_widget' => '<li id="%1$s" class="widget %2$s">', 
			'after_widget' => "</li>\n", 
			'before_title' => '<h2 class="widgettitle">', 
			'after_title' => "</h2>\n"
		);
		
		$defaults = apply_filters( 'register_sidebar_defaults', $defaults, $args );
		
		$sidebar = array_merge( $defaults, $args );
		
		$sidebar['id'] = sanitize_title( $sidebar['name'] );
		
		$wp_registered_sidebars[$sidebar['id']] = $sidebar;
		
		return $sidebar['id'];
	}
}

if ( !function_exists( 'unregister_sidebar' ) ) {
	function unregister_sidebar( $name ) {
		global $wp_registered_sidebars;
		
		if ( isset( $wp_registered_sidebars[$name] ) ) {
			unset( $wp_registered_sidebars[$name] );
		}
	}
}

if ( !function_exists( 'register_sidebar_widget' ) ) {
	function register_sidebar_widget( $name, $output_callback, $classname = '' ) {
		global $wp_registered_widgets, $wp_register_widget_defaults;
		
		if ( is_array( $name ) ) {
			$id = sanitize_title( sprintf( $name[0], $name[2] ) );
			$name = sprintf( __( $name[0], $name[1] ), $name[2] );
		} else {
			$id = sanitize_title( $name );
			$name = __( $name );
		}
		
		if ( ( empty( $classname ) || !is_string( $classname ) ) && is_string( $output_callback ) ) {
			$classname = $output_callback;
		}
		
		$widget = array(
			'id' => $id, 
			'callback' => $output_callback, 
			'classname' => $classname, 
			'params' => array_slice( func_get_args(), 2 )
		);
		
		if ( empty( $output_callback ) ) {
			unset( $wp_registered_widgets[$name] );
		} elseif ( is_callable( $output_callback ) && ( !isset( $wp_registered_widgets[$name] ) || !$wp_register_widget_defaults ) ) {
			$wp_registered_widgets[$name] = $widget;
		}
	}
}

if ( !function_exists( 'unregister_sidebar_widget' ) ) {
	function unregister_sidebar_widget( $name ) {
		register_sidebar_widget( $name, '' );
		unregister_widget_control( $name );
	}
}

if ( !function_exists( 'register_widget_control' ) ) {
	function register_widget_control( $name, $control_callback, $width = 300, $height = 200 ) {
		global $wp_registered_widget_controls, $wp_registered_sidebar_defaults;
		
		$width = (int) $width;
		$height = (int) $height;
		
		if ( is_array( $name ) ) {
			$id = sanitize_title( sprintf( $name[0], $name[2] ) );
			$name = sprintf( __( $name[0], $name[1] ), $name[2] );
		} else {
			$id = sanitize_title( $name );
			$name = __( $name );
		}
		
		$width = ( $width > 90 ) ? $width + 60 : 360;
		$height = ( $height > 60 ) ? $height + 40 : 240;
		
		if ( empty( $control_callback ) ) {
			unset( $wp_registered_widget_controls[$name] );
		} elseif ( !isset( $wp_registered_widget_controls[$name] ) || !$wp_registered_sidebar_defaults ) {
			$wp_registered_widget_controls[$name] = array(
				'id' => $id, 
				'callback' => $control_callback, 
				'width' => $width, 
				'height' => $height, 
				'params' => array_slice( func_get_args(), 4 )
			);
		}
	}
}

if ( !function_exists( 'unregister_widget_control' ) ) {
	function unregister_widget_control( $name ) {
		register_sidebar_control( $name, '' );
	}
}

if ( !function_exists( 'dynamic_sidebar' ) ) {
	function dynamic_sidebar( $name = 1 ) {
		global $wp_registered_sidebars, $wp_registered_widgets;
		
		if ( is_int( $name ) ) {
			$index = sanitize_title( __( 'Sidebar' ) . ' ' . $name );
			$name = sprintf( __( 'Sidebar %d' ), $name );
		} else {
			$index = sanitize_title( $name );
		}
		
		$sidebars_widgets = wp_get_sidebars_widgets();
		
		$sidebar = $wp_registered_sidebars[$index];
		
		if ( empty( $sidebar ) || !is_array( $sidebars_widgets[$index] ) || empty( $sidebars_widgets[$index] ) ) {
			return false;
		}
		
		$did_one = false;
		
		foreach ( $sidebars_widgets[$index] as $name ) {
			$callback = $wp_registered_widgets[$name]['callback'];
			
			$params = array_merge( array( $sidebar ), (array) $wp_registered_widgets[$name]['params'] );
			$params[0]['before_widget'] = sprintf( $params[0]['before_widget'], $wp_registered_widgets[$name]['id'], $wp_registered_widgets[$name]['classname'] );
			
			if ( is_callable( $callback ) ) {
				call_user_func_array( $callback, $params );
				$did_one = true;
			}
		}
		
		return $did_one;
	}
}

if ( !function_exists( 'is_active_widget' ) ) {
	function is_active_widget( $callback ) {
		global $wp_registered_widgets;
		
		$sidebars_widgets = wp_get_sidebars_widgets();
		
		if ( is_array( $sidebars_widgets ) ) {
			foreach ( $sidebars_widgets as $sidebar => $widgets ) {
				if ( is_array( $widgets) ) {
					foreach ( $widgets as $widget ) {
						if ( $wp_registered_widgets[$widget]['callback'] == $callback ) {
							return true;
						}
					}
				}
			}
		}
		
		return false;
	}
}

if ( !function_exists( 'is_dynamic_sidebar' ) ) {
	function is_dynamic_sidebar() {
		global $wp_registered_sidebars, $wp_registered_widgets;
		
		$sidebars_widgets = wp_get_sidebars_widgets();
		
		foreach ( $wp_registered_sidebars as $index => $sidebar ) {
			if ( count( $sidebars_widgets[$index] ) > 0 ) {
				foreach ( $sidebars_widgets[$index] as $widget ) {
					if ( array_key_exists( $widget, $wp_registered_sidebars ) ) {
						return true;
					}
				}
			}
		}
		
		return false;
	}
}

/* Internal Functions */

function wp_get_sidebars_widgets() {
	return get_option( 'wp_sidebars_widgets' );
}

function wp_set_sidebars_widgets( $sidebars_widgets ) {
	update_option( 'wp_sidebars_widgets', $sidebars_widgets );
}

function wp_get_widget_defaults() {
	global $wp_registered_sidebars;
	
	$defaults = array();
	
	foreach ( $wp_registered_sidebars as $index => $sidebar ) {
		$defaults[$index] = array();
	}
	
	return $defaults;
}

/* Default Widgets */

function wp_widget_pages( $args ) {
	extract( $args );
	
	$options = get_option( 'wp_widget_pages' );
	
	$title = ( empty( $options['title'] ) ) ? __( 'Pages' ) : $options['title'];
	
	echo $before_widget . $before_title . $title . $after_title . "<ul>\n";
	wp_list_pages( 'title_li=' );
	echo "</ul>\n" . $after_widget;
}

function wp_widget_pages_control() {
	$options = $newoptions = get_option( 'wp_widget_pages' );
	
	if ( isset( $_POST['pages-submit'] ) ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['pages-title'] ) );
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_pages', $options );
		}
	}
	
	$title = htmlspecialchars( $options['title'], ENT_QUOTES );
?>
		<p><label for="pages-title"><?php _e( 'Title:' ); ?> <input style="width:250px" id="pages-title" name="pages-title" type="text" value="<?php echo $title; ?>" /></label></p>
		<input type="hidden" id="pages-submit" name="pages-submit" value="1" />
<?php
}

function wp_widget_links( $args ) {
	global $wp_db_version;
	extract( $args );
	
	if ( $wp_db_version < 3582 ) {
		get_links_list();
	} else {
		wp_list_bookmarks( array(
			'title_before' => $before_title, 'title_after' => $after_title, 
			'category_before' => $before_widget, 'category_after' => $after_widget
		) );
	}
}

function wp_widget_search( $args ) {
	extract( $args );
?>
		<?php echo $before_widget; ?>
			<form id="searchform" action="<?php bloginfo( 'url' ); ?>" method="get">
				<div><input type="text" name="s" id="s" size="15" /><br />
					<input type="submit" value="<?php _e( 'Search' ); ?>" /></div>
			</form>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_archives( $args ) {
	extract( $args );
	
	$options = get_option( 'wp_widget_archives' );
	$c = ( $options['count'] ) ? '1' : '0';
	$title = ( empty( $options['title'] ) ) ? __( 'Archives' ) : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php wp_get_archives( 'type=monthly&show_post_count=' . $c ); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_archives_control() {
	$options = $newoptions = get_option( 'wp_widget_archives' );
	
	if ( isset( $_POST['archives-submit'] ) ) {
		$newoptions['count'] = isset( $_POST['archives-count'] );
		$newoptions['title'] = strip_tags( stripslashes( $_POST['archives-title'] ) );
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_archives', $options );
		}
	}
	
	$count = ( isset( $options['count'] ) ) ? ' checked="checked"' : '';
	$title = htmlspecialchars( $options['title'], ENT_QUOTES );
?>
		<p><label for="archives-title"><?php _e( 'Title:' ); ?> <input style="width:250px" id="archives-title" name="archives-title" type="text" value="<?php echo $title; ?>" /></label></p>
		<p style="text-align:right;margin-right:40px"><label for="archives-count"><?php _e( 'Show post counts?' ); ?> <input type="checkbox" class="checkbox" name="archives-count" id="archives-count" <?php echo $count; ?>/></p>
		<input type="hidden" name="archives-submit" id="archives-submit" value="1" />
<?php
}

function wp_widget_meta( $args ) {
	extract( $args );
	
	$options = get_option( 'wp_widget_meta' );
	$title = ( empty( $options['title'] ) ) ? __( 'Meta' ) : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
				<?php wp_register(); ?>
				<li><?php wp_loginout(); ?></li>
				<li><a href="<?php bloginfo( 'rss2_url' ); ?>" title="<?php _e( 'Syndicate this site using RSS 2.0' ); ?>"><?php _e( 'Entries <abbr title="Really Simple Syndication">RSS</abbr>' ); ?></a></li>
				<li><a href="<?php bloginfo( 'comments_rss2_url' ); ?>" title="<?php _e( 'The latest comments to all posts in RSS' ); ?>"><?php _e( 'Comments <abbr title="Really Simple Syndication">RSS</abbr>' ); ?></a></li>
				<li><a href="http://wordpress.org/" title="<?php _e( 'Powered by WordPress, state-of-the-art semantic personal publishing platform.' ); ?>">WordPress</a></li>
				<?php wp_meta(); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_meta_control() {
	$options = $newoptions = get_option( 'wp_widget_meta' );
	
	if ( isset( $_POST['meta-submit'] ) ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['meta-title'] ) );
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_meta', $options );
		}
	}
	
	$title = htmlspecialchars( $options['title'], ENT_QUOTES );
?>
		<p><label for="meta-title"><?php _e( 'Title:' ); ?> <input type="text" name="meta-title" id="meta-title" style="width:250px" value="<?php echo $title; ?>" /></label></p>
		<input type="hidden" name="meta-submit" id="meta-submit" value="1" />
<?php
}

function wp_widget_calendar( $args ) {
	extract( $args );
	
	$title = ( empty( $options['title'] ) ) ? '&nbsp;' : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<div id="calendar_wrap">
				<?php get_calendar(); ?>
			</div>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_calendar_control() {
	$options = $newoptions = get_option( 'wp_widget_calendar' );
	
	if ( isset( $_POST['calendar-submit'] ) ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['calendar-title'] ) );
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_calendar', $options );
		}
	}
	
	$title = htmlspecialchars( $options['title'], ENT_QUOTES );
?>
		<p><label for="calendar-title"><?php _e( 'Title:' ); ?> <input type="text" style="width:250px" id="calendar-title" name="calendar-title" value="<?php echo $title; ?>" /></label></p>
		<input type="hidden" id="calendar-title" name="calendar-title" value="1" />
<?php
}

function wp_widget_text( $args, $i = 1 ) {
	extract( $args );
	
	$options = get_option( 'wp_widget_text' );
	
	$title = ( empty( $options[$i]['title'] ) ) ? '' : $options[$i]['title'];
?>
		<?php echo $before_widget; ?>
			<?php print ( empty( $title ) ) ? '' : $before_title . $title . $after_title; ?>
			<div class="textwidget"><?php echo $text; ?></div>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_text_control( $i ) {
	$options = $newoptions = get_option( 'wp_widget_text' );
	
	if ( isset( $_POST['text-submit-' . $i] ) ) {
		$newoptions[$i]['title'] = strip_tags( stripslashes( $_POST['text-title-' . $i] ) );
		$newoptions[$i]['text'] = stripslashes( $_POST['text-text-' . $i] );
		
		if ( !current_user_can( 'unfiltered_html' ) ) {
			$newoptions[$i]['text'] = stripslashes( wp_filter_post_kses( $newoptions[$i]['text'] ) );
		}
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_options( 'wp_widget_text', $options );
		}
	}
	
	$title = htmlspecialchars( $options[$i]['title'], ENT_QUOTES );
	$text = htmlspecialchars( $options[$i]['text'], ENT_QUOTES );
?>
		<input style="width:450px" id="text-title-<?php echo $i; ?>" name="text-title-<?php echo $i; ?>" type="text" value="<?php echo $title; ?>" />
		<textarea style="width:450px;height:280px" id="text-text-<?php echo $i; ?>" name="text-text-<?php echo $i; ?>"><?php echo $text; ?></textarea>
		<input type="hidden" id="text-submit-<?php echo $i; ?>" name="text-submit-<?php echo $i; ?>" value="1" />
<?php
}

function wp_widget_text_setup() {
	$options = $newoptions = get_option( 'wp_widget_text' );
	
	if ( isset( $_POST['text-number-submit'] ) ) {
		$i = (int) $_POST['text-number'];
		
		if ( $i > 9 ) {
			$i = 9;
		} elseif ( $i < 1 ) {
			$i = 1;
		}
		
		$newoptions['number'] = $i;
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_text', $options );
		}
	}
}

function wp_widget_text_page() {
	$options = get_option( 'widget_text' );
	
	$i = $options['number'];
?>
	<div class="wrap">
		<form method="post">
			<h2><?php _e( 'Text Widgets' ); ?></h2>
			
			<p style="line-height:30px"><?php _e( 'How many widgets would you like?' ); ?> 
				<select id="text-number" name="text-number" value="<?php echo $i; ?>">
				<?php for ( $j = 1; $j < 10; $j++ ) { ?>
					<option value="<?php echo $j; ?>"<?php if ( $i == $j ) { ?> selected="selected"<?php } ?>><?php echo $j; ?></option>
				<?php } ?>
				</select>
				<span class="submit"><input type="submit" name="text-number-submit" id="text-number-submit" value="<?php _e( 'Save' ); ?>" /></span>
			</p>
		</form>
	</div>
<?php
}

function wp_widget_text_register() {
	$options = get_option( 'wp_widget_text' );
	
	$i = $options['number'];
	
	if ( $i < 1 ) {
		$i = 1;
	} elseif ( $i > 9 ) {
		$i = 9;
	}
	
	for ( $j = 1; $j <= 9; $j++ ) {
		$name = array( 'Text %s', '', $i );
		register_sidebar_widget( $name, ( $j <= $i ) ? 'wp_widget_text' : '', $j );
		register_widget_control( $name, ( $j <= $i ) ? 'wp_widget_text_control' : '', 460, 350, $j );
	}
	
	add_action( 'sidebar_admin_setup', 'wp_widget_text_setup' );
	add_action( 'sidebar_admin_page', 'wp_widget_text_page' );
}

function wp_widget_categories( $args ) {
	extract( $args );
	
	$options = get_option( 'wp_widget_categories' );
	
	$title = ( empty( $options['title'] ) ) ? __( 'Categories' ) : $options['title'];
	$c = ( $options['count'] ) ? '1' : '0';
	$h = ( $options['hierarchical'] ) ? '1' : '0';
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
				<?php wp_list_cats( 'sort_column=name&optioncount=' . $c . '&hierarchical=' . $h ); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_categories_control() {
	$options = $newoptions = get_option( 'wp_widget_categories' );
	
	if ( isset( $_POST['categories-submit'] ) ) {
		$newoptions['count'] = isset( $_POST['categories-count'] );
		$newoptions['hierarchical'] = isset( $_POST['categories-hierarchical'] );
		$newoptions['title'] = strip_tags( stripslashes( $_POST['categories-title'] ) );
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_categories', $options );
		}
	}
	
	$count = ( $options['count'] ) ? ' checked="checked"' : '';
	$hierarchical = ( $options['hierarchical'] ) ? ' checked="checked"' : '';
	$title = wp_specialchars( $options['title'] );
?>
		<p><label for="categories-title"><?php _e( 'Title:' ); ?> <input type="text" value="<?php echo $title; ?>" id="categories-title" name="categories-title" /></label></p>
		<p style="text-align:right;margin-right:40px"><label for="categories-count"><?php _e( 'Show post counts? '); ?> <input type="checkbox" class="checkbox"<?php echo $count; ?> id="categories-count" name="categories-count" /></label></p>
		<p style="text-align:right;margin-right:40px"><label for="categories-hierarchical"><?php _e( 'Show hierarchy?' ); ?> <input type="checkbox" class="checkbox"<?php echo $hierarchical; ?> id="categories-hierarchical" name="categories-hierarchical" /></label></p>
		<input type="hidden" name="categories-submit" id="categories-submit" value="1" />
<?php
}

function wp_widget_recent_entries( $args ) {
	extract( $args );
	
	$title = __( 'Recent Posts' );
	
	$query = new WP_Query( 'showposts=10' );
	
	if ( !$query->have_posts() ) {
		return;
	}
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php while ( $query->have_posts() ) {
				$query->the_post(); ?>
				<li><a href="<?php the_permalink(); ?>"><?php 
					if ( get_the_title() != '' ) {
						the_title();
					} else {
						the_ID();
					}
				?></a></li>
			<?php } ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_recent_comments( $args ) {
	global $wpdb;
	
	extract( $args );
	
	$options = get_option( 'wp_widget_recent_comments' );
	
	$title = ( empty( $options['title'] ) ) ? __( 'Recent Comments' ) : $options['title'];
	
	$comments = $wpdb->get_results( "SELECT * FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 5" );
	
	if ( is_array( $comments ) && count( $comments ) > 0 ) {
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul id="recentcomments">
			<?php foreach ( $comments as $comment ) { ?>
				<li class="recentcomments"><?php echo sprintf( __( '%1$s on %2$s' ), get_comment_author_link(), '<a href="' . get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID . '">' . get_the_title( $comment->comment_post_ID ) . '</a>' ); ?></li>
			<?php } ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
	}
}

function wp_widget_recent_comments_control() {
	$options = $newoptions = get_option( 'wp_widget_recent_comments' );
	
	if ( isset( $_POST['recent-comments-submit'] ) ) {
		$newoptions['title'] = strip_tags( stripslashes( $_POST['recent-comments-title'] ) );
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_recent_comments', $options );
		}
	}
	
	$title = htmlspecialchars( $options['title'], ENT_QUOTES );
?>
		<p><label for="recent-comments-title"><?php _e( 'Title:' ); ?> <input type="text" style="width:250px" id="recent-comments-title" name="recent-comments-title" value="<?php echo $title; ?>" /></label></p>
		<input type="hidden" name="recent-comments-submit" id="recent-comments-submit" value="1" />
<?php
}

function wp_widget_recent_comments_wphead() {
?>
		<style type="text/css">
			.recentcomments a {
				display: inline !important;
				padding: 0 !important;
				margin: 0 !important;
			}
		</style>
<?php
}

function wp_widget_recent_comments_register() {
	register_sidebar_widget( array( 'Recent Comments', '' ), 'wp_widget_recent_comments' );
	register_widget_control( array( 'Recent Comments', '' ), 'wp_widget_recent_comments_control' );
	
	if ( is_active_widget( 'wp_widget_recent_comments' ) ) {
		add_action( 'wp_head', 'wp_widget_recent_comments_wphead' );
	}
}

function wp_widget_rss( $args, $i = 1 ) {
	extract( $args );
	
	if ( file_exists( ABSPATH . WPINC . '/rss.php' ) ) {
		require_once ABSPATH . WPINC . '/rss.php';
	} else {
		require_once ABSPATH . WPINC . '/rss-functions.php';
	}
	
	$options = get_option( 'wp_widget_rss' );
	
	$number_items = (int) $options[$i]['number_items'];
	$show_summary = $options[$i]['show_summary'];
	
	if ( empty( $number_items ) || $number_items < 1 || $number_items > 10 ) {
		$number_items = 10;
	}
	
	$url = $options[$i]['url'];
	
	if ( empty( $url ) ) {
		return;
	}
	
	while ( strstr( $url, 'http' ) != $url ) {
		$url = substr( $url, 1 );
	}
	
	$rss = fetch_rss( $url );
	
	$link = wp_specialchars( strip_tags( $rss->channel['link'] ), 1 );
	
	while ( strstr( $link, 'http' ) != $link ) {
		$link = substr( $link, 1 );
	}
	
	$desc = wp_specialchars( strip_tags( html_entity_decode( $rss->channel['description'], ENT_QUOTES ) ), 1 );
	
	$title = $options[$i]['title'];
	
	if ( empty( $title ) ) {
		$title = htmlentities( strip_tags( $rss->channel['title'] ) );
	}
	
	if ( empty( $title ) ) {
		$title = $desc;
	}
	
	if ( empty( $title ) ) {
		$title = __( 'Unknown Feed' );
	}
	
	$url = wp_specialchars( strip_tags( $url ), 1 );
	
	if ( file_exists( ABSPATH . 'wp-content/rss.png' ) ) {
		$icon = get_bloginfo( 'wpurl' ) . '/wp-content/rss.png';
	} else {
		$icon = get_bloginfo( 'wpurl' ) . '/wp-includes/images/rss.png';
	}
	
	$h2 = '<a href="%1$s" class="rsswidget" title="%2$s"><img src="%3$s" style="width:14px;height:14px" alt="%4$s" /></a> <a href="%5$s" class="rsswidget" title="%6$s">%7$s</a>';
	$h2 = sprintf( $h2, $url, __( 'Syndicate this content' ), $icon, __( 'RSS' ), $link, $desc, $title );
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $h2 . $after_title; ?>
			<ul>
			<?php
				if ( is_array( $rss->items ) ) {
					$rss->items = array_slice( $rss->items, 0, $number_items );
					
					foreach ( $rss->items as $item ) {
						while ( strstr( $item['link'], 'http' ) != $item['link'] ) {
							$item['link'] = substr( $item['link'], 1 );
						}
						
						$link = wp_specialchars( strip_tags( $item['link'] ), 1 );
						$title = wp_specialchars( strip_tags( $item['title'] ), 1 );
						
						if ( empty( $title ) ) {
							$title = __( 'Untitled' );
						}
						
						$desc = '';
						
						if ( $show_summary ) {
							$summary = '<div class="rssSummary">' . $item['description'] . '</div>';
						} else {
							$desc = str_replace( array( "\r", "\n" ), ' ', wp_specialchars( strip_tags( html_entity_decode( $item['description'], ENT_QUOTES ) ), 1 ) );
							$summary = '';
						}
			?>
				<li><a class="rsswidget" href="<?php echo $link; ?>" title="<?php echo $desc; ?>"><?php echo $title; ?></a><?php echo $summary; ?></li>
			<?php
					}
				} else {
			?>
				<li><?php _e( 'An error has occurred; the feed is probably down. Try again later.' ); ?></li>
			<?php
				}
			?>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_rss_control( $i ) {
	$options = $newoptions = get_option( 'wp_widget_rss' );
	
	if ( isset( $_POST['rss-submit-' . $i] ) ) {
		$newoptions[$i]['number_items'] = (int) $_POST['rss-items-' . $i];
		$newoptions[$i]['url'] = strip_tags( stripslashes( $_POST['rss-url-' . $i] ) );
		$newoptions[$i]['title'] = trim( strip_tags( stripslashes( $_POST['rss-title-' . $i] ) ) );
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_rss', $options );
		}
	}
	
	$url = htmlspecialchars( $options[$i]['url'], ENT_QUOTES );
	$number_items = (int) $options[$i]['number_items'];
	$title = htmlspecialchars( $options[$i]['title'], ENT_QUOTES );
	
	if ( empty( $number_items ) || $number_items < 1 ) {
		$number_items = 10;
	}
?>
		<p style="text-align:center;"><?php _e( 'Enter the RSS feed URL here:' ); ?></p>
		<input style="width: 400px;" id="rss-url-<?php echo $i; ?>" name="rss-url-<?php echo $i; ?>" type="text" value="<?php echo $url; ?>" />
		<p style="text-align:center;"><?php _e( 'Give the feed a title (optional):' ); ?></p>
		<input style="width: 400px;" id="rss-title-<?php echo $i; ?>" name="rss-title-<?php echo $i; ?>" type="text" value="<?php echo $title; ?>" />
		<p style="text-align:center; line-height: 30px;"><?php _e('How many items would you like to display?', 'widgets'); ?> <select id="rss-items-<?php echo $i; ?>" name="rss-items-<?php echo $i; ?>"><?php for ( $j = 1; $j <= 10; $j++ ) echo "<option value='$j' ".($i==$j ? "selected='selected'" : '').">$j</option>"; ?></select></p>
		<input type="hidden" id="rss-submit-<?php echo $i; ?>" name="rss-submit-<?php echo $i; ?>" value="1" />
<?php
}

function wp_widget_rss_setup() {
	$options = $newoptions = get_option( 'wp_widget_rss' );
	
	if ( isset( $_POST['rss-number-submit'] ) ) {
		$i = (int) $_POST['rss-number'];
		
		if ( $i > 9 ) {
			$number = 9;
		} elseif ( $i < 1 ) {
			$number = 1;
		}
		
		$newoptions['number'] = $i;
		
		if ( $newoptions != $options ) {
			$options = $newoptions;
			update_option( 'wp_widget_rss', $options );
			widget_rss_register( $options['number'] );
		}
	}
}

function wp_widget_rss_page() {
	$options = get_option( 'wp_widget_rss' );
	
	$i = $options['number'];
?>
	<div class="wrap">
		<form method="post">
			<h2><?php _e( 'RSS Feed Widgets' ); ?></h2>
			
			<p style="line-height:30px"><?php _e( 'How many RSS widgets would you like?' ); ?>
				<select id="rss-number" name="rss-number" value="<?php echo $i; ?>">
				<?php for ( $j = 1; $j < 10; $j++ ) { ?>
					<option value="<?php echo $j; ?>"<?php
						if ( $i == $j ) {
							echo ' selected="selected"';
						}
					?>><?php echo $j; ?></option>
				<?php } ?>
				</select>
			</p>
		</form>
	</div>
<?php
}

function wp_widget_rss_register() {
	$options = get_option( 'wp_widget_rss' );
	
	$i = $options['number'];
	
	if ( $i < 1 ) {
		$i = 1;
	} elseif ( $i > 9 ) {
		$i = 9;
	}
	
	for ( $j = 1; $j <= 9; $j++ ) {
		$name = array( 'RSS %s', '', $j );
		register_sidebar_widget( $name, ( $j <= $i ) ? 'wp_widget_rss' : '', $j );
		register_widget_control( $name, ( $j <= $i ) ? 'wp_widget_rss_control' : '', 410, 200, $j );
	}
	
	add_action( 'sidebar_admin_setup', 'wp_widget_rss_setup' );
	add_action( 'sidebar_admin_page', 'wp_widget_rss_page' );
	
	if ( is_active_widget( 'wp_widget_rss' ) ) {
		add_action( 'wp_head', 'wp_widget_rss_wphead' );
	}
}

function wp_widget_rss_wphead() {
?>
		<style type="text/css">
			a.rsswidget {
				display: inline !important;
			}
			
			a.rsswidget img {
				background: orange;
				color: white;
			}
		</style>
<?php
}

function wp_widgets_init() {
	global $wp_register_widget_defaults;
	
	$wp_register_widget_defaults = true;
	
	wp_widget_text_register();
	wp_widget_rss_register();
	wp_widget_recent_comments_register();
	
	register_sidebar_widget( 'Pages', 'wp_widget_pages' );
	register_widget_control( 'Pages', 'wp_widget_pages_control', 300, 90 );
	
	register_sidebar_widget( 'Calendar', 'wp_widget_calendar' );
	register_widget_control( 'Calendar', 'wp_widget_calendar_control', 300, 90 );
	
	register_sidebar_widget( 'Archives', 'wp_widget_archives' );
	register_widget_control( 'Archives', 'wp_widget_archives_control', 300, 90 );
	
	register_sidebar_widget( 'Links', 'wp_widget_links' );
	
	register_sidebar_widget( 'Meta', 'wp_widget_meta' );
	register_widget_control( 'Meta', 'wp_widget_meta_control', 300, 90 );
	
	register_sidebar_widget( 'Search', 'wp_widget_search' );
	
	register_sidebar_widget( 'Categories', 'wp_widget_categories' );
	register_widget_control( 'Categories', 'wp_widget_categories_control', 300, 150 );
	
	register_sidebar_widget( 'Recent Posts', 'wp_widget_recent_entries' );
	
	$wp_register_widget_defaults = false;
	
	do_action( 'widgets_init' );
}

?>