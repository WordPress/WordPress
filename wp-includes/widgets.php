<?php

/* Global Variables */

$wp_registered_sidebars = array();
$wp_registered_widgets = array();
$wp_registered_widget_controls = array();
$wp_registered_widget_styles = array();
$wp_register_widget_defaults = false;

/* Template tags & API functions */

if ( !function_exists( 'register_sidebars' ) ):
function register_sidebars($number = 1, $args = array()) {
	$number = (int) $number;

	if ( is_string($args) )
		parse_str($args, $args);

	$i = 1;

	while ( $i <= $number ) {
		$_args = $args;
		if ( $number > 1 ) {
			$_args['name'] = isset($args['name']) ? $args['name'] : sprintf(__('Sidebar %d'), $i);
		} else {
			$_args['name'] = isset($args['name']) ? $args['name'] : __('Sidebar');
		}
		$_args['id'] = isset($args['id']) ? $args['id'] : "sidebar-$i";
		register_sidebar($_args);
		++$i;
	}
}
endif;

if ( !function_exists( 'register_sidebar' ) ):
function register_sidebar($args = array()) {
	global $wp_registered_sidebars;

	if ( is_string($args) )
		parse_str($args, $args);

	$i = count($wp_registered_sidebars) + 1;

	$defaults = array(
		'name' => sprintf(__('Sidebar %d'), count($wp_registered_sidebars) + 1 ),
		'id' => "sidebar-$i",
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => "</li>\n",
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => "</h2>\n",
	);

	$sidebar = array_merge($defaults, $args);

	$wp_registered_sidebars[$sidebar['id']] = $sidebar;

	return $sidebar['id'];
}
endif;

if ( !function_exists( 'unregister_sidebar' ) ):
function unregister_sidebar( $name ) {
	global $wp_registered_sidebars;
		
	if ( isset( $wp_registered_sidebars[$name] ) )
		unset( $wp_registered_sidebars[$name] );
}
endif;

if ( !function_exists( 'register_sidebar_widget' ) ):
function register_sidebar_widget($name, $output_callback, $classname = '', $id = '') {
	global $wp_registered_widgets, $wp_register_widget_defaults;

	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	// Last resort -- this can be broken when names get translated so please provide a unique id.
	if ( !isset($id) )
		$id = sanitize_title($name);

	if ( (!isset($classname) || empty($classname) || !is_string($classname)) && is_string($output_callback) )
			$classname = $output_callback;

	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $output_callback,
		'classname' => $classname,
		'params' => array_slice(func_get_args(), 4)
	);

	if ( empty($output_callback) )
		unset($wp_registered_widgets[$id]);
	elseif ( is_callable($output_callback) && ( !isset($wp_registered_widgets[$id]) || !$wp_register_widget_defaults) )
		$wp_registered_widgets[$id] = $widget;
}
endif;

if ( !function_exists( 'unregister_sidebar_widget' ) ):
function unregister_sidebar_widget($id) {
	$id = sanitize_title($id);
	register_sidebar_widget('', '', '', $id);
	unregister_widget_control($id);
}
endif;

if ( !function_exists( 'register_widget_control' ) ):
function register_widget_control($name, $control_callback, $width = 300, $height = 200, $id = '') {
	global $wp_registered_widget_controls, $wp_register_widget_defaults;

	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	if ( !isset($id) || empty($id) )
		$id = $name;

	$id = sanitize_title($id);

	$width = (int) $width > 90 ? (int) $width + 60 : 360;
	$height = (int) $height > 60 ? (int) $height + 40 : 240;

	if ( empty($control_callback) )
		unset($wp_registered_widget_controls[$name]);
	elseif ( !isset($wp_registered_widget_controls[$name]) || !$wp_register_widget_defaults )
		$wp_registered_widget_controls[$id] = array(
			'name' => $name,
			'id' => $id,
			'callback' => $control_callback,
			'width' => $width,
			'height' => $height,
			'params' => array_slice(func_get_args(), 5)
		);
}
endif;

if ( !function_exists( 'unregister_widget_control' ) ):
function unregister_widget_control($id) {
	$id = sanitize_title($id);
	return register_widget_control($id, '');
}
endif;

if ( !function_exists( 'dynamic_sidebar' ) ):
function dynamic_sidebar($index = 1) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	if ( is_int($index) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title($index);
	}

	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( empty($wp_registered_sidebars[$index]) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
		return false;

	$sidebar = $wp_registered_sidebars[$index];

	$did_one = false;
	foreach ( $sidebars_widgets[$index] as $id ) {
		$callback = $wp_registered_widgets[$id]['callback'];

		$params = array_merge(array($sidebar), (array) $wp_registered_widgets[$id]['params']);

		// Substitute HTML id and class attributes into before_widget
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $wp_registered_widgets[$id]['classname']);

		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	return $did_one;
}
endif;

if ( !function_exists( 'is_active_widget' ) ):
function is_active_widget($callback) {
	global $wp_registered_widgets;

	$sidebars_widgets = wp_get_sidebars_widgets(false);

	if ( is_array($sidebars_widgets) ) foreach ( $sidebars_widgets as $sidebar => $widgets )
		if ( is_array($widgets) ) foreach ( $widgets as $widget )
			if ( $wp_registered_widgets[$widget]['callback'] == $callback )
				return true;

	return false;
}
endif;

if ( !function_exists( 'is_dynamic_sidebar' ) ):
function is_dynamic_sidebar() {
	global $wp_registered_widgets, $wp_registered_sidebars;
	$sidebars_widgets = get_option('sidebars_widgets');
	foreach ( $wp_registered_sidebars as $index => $sidebar ) {
		if ( count($sidebars_widgets[$index]) ) {
			foreach ( $sidebars_widgets[$index] as $widget )
				if ( array_key_exists($widget, $wp_registered_widgets) )
					return true;
		}
	}
	return false;
}
endif;

/* Internal Functions */

function wp_get_sidebars_widgets($update = true) {
	global $wp_registered_widgets;

	$sidebars_widgets = get_option('sidebars_widgets');
	$_sidebars_widgets = array();

	if ( !isset($sidebars_widgets['array_version']) )
		$sidebars_widgets['array_version'] = 1;

	switch ( $sidebars_widgets['array_version'] ) {
		case 1 :
			foreach ( $sidebars_widgets as $index => $sidebar )
			if ( is_array($sidebar) )
			foreach ( $sidebar as $i => $name ) {
				$id = strtolower($name);
				if ( isset($wp_registered_widgets[$id]) ) {
					$_sidebars_widgets[$index][$i] = $id;
					continue;
				}
				$id = sanitize_title($name);
				if ( isset($wp_registered_widgets[$id]) ) {
					$_sidebars_widgets[$index][$i] = $id;
					continue;
				}
				unset($_sidebars_widgets[$index][$i]);
			}
			$_sidebars_widgets['array_version'] = 2;
			if ( $update )
				update_option('sidebars_widgets', $_sidebars_widgets);
			break;
		case 2 :
			$_sidebars_widgets = $sidebars_widgets;
			break;
	}

	unset($_sidebars_widgets['array_version']);

	return $_sidebars_widgets;
}

function wp_set_sidebars_widgets( $sidebars_widgets ) {
	update_option( 'sidebars_widgets', $sidebars_widgets );
}

function wp_get_widget_defaults() {
	global $wp_registered_sidebars;

	$defaults = array();

	foreach ( $wp_registered_sidebars as $index => $sidebar )
		$defaults[$index] = array();

	return $defaults;
}

/* Default Widgets */

function wp_widget_pages($args) {
	extract($args);
	$options = get_option('widget_pages');
	$title = empty($options['title']) ? __('Pages') : $options['title'];
	echo $before_widget . $before_title . $title . $after_title . "<ul>\n";
	wp_list_pages("title_li=");
	echo "</ul>\n" . $after_widget;
}

function wp_widget_pages_control() {
	$options = $newoptions = get_option('widget_pages');
	if ( $_POST["pages-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["pages-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_pages', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="pages-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="pages-title" name="pages-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="pages-submit" name="pages-submit" value="1" />
<?php
}

function wp_widget_links($args) {
	global $wp_db_version;
	extract($args);
	if ( $wp_db_version < 3582 ) {
		// This ONLY works with li/h2 sidebars.
		get_links_list();
	} else {
		wp_list_bookmarks(array(
			'title_before' => $before_title, 'title_after' => $after_title, 
			'category_before' => $before_widget, 'category_after' => $after_widget, 
			'show_images' => true, 'class' => 'linkcat widget'
		));
	}
}

function wp_widget_search($args) {
	extract($args);
?>
		<?php echo $before_widget; ?>
			<form id="searchform" method="get" action="<?php bloginfo('home'); ?>">
			<div>
			<input type="text" name="s" id="s" size="15" /><br />
			<input type="submit" value="<?php _e('Search'); ?>" />
			</div>
			</form>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_archives($args) {
	extract($args);
	$options = get_option('widget_archives');
	$c = $options['count'] ? '1' : '0';
	$title = empty($options['title']) ? __('Archives') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php wp_get_archives("type=monthly&show_post_count=$c"); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_archives_control() {
	$options = $newoptions = get_option('widget_archives');
	if ( $_POST["archives-submit"] ) {
		$newoptions['count'] = isset($_POST['archives-count']);
		$newoptions['title'] = strip_tags(stripslashes($_POST["archives-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_archives', $options);
	}
	$count = $options['count'] ? 'checked="checked"' : '';
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="archives-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="archives-title" name="archives-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p style="text-align:right;margin-right:40px;"><label for="archives-count">Show post counts <input class="checkbox" type="checkbox" <?php echo $count; ?> id="archives-count" name="archives-count" /></label></p>
			<input type="hidden" id="archives-submit" name="archives-submit" value="1" />
<?php
}

function wp_widget_meta($args) {
	extract($args);
	$options = get_option('widget_meta');
	$title = empty($options['title']) ? __('Meta') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS 2.0'); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="http://wordpress.org/" title="<?php _e('Powered by WordPress, state-of-the-art semantic personal publishing platform.'); ?>">WordPress.org</a></li>
			<?php wp_meta(); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}
function wp_widget_meta_control() {
	$options = $newoptions = get_option('widget_meta');
	if ( $_POST["meta-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["meta-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_meta', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="meta-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="meta-title" name="meta-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="meta-submit" name="meta-submit" value="1" />
<?php
}

function wp_widget_calendar($args) {
	extract($args);
	$options = get_option('widget_calendar');
	$title = $options['title'];
	if ( empty($title) )
		$title = '&nbsp;';
	echo $before_widget . $before_title . $title . $after_title;
	echo '<div id="calendar_wrap">';
	get_calendar();
	echo '</div>';
	echo $after_widget;
}
function wp_widget_calendar_control() {
	$options = $newoptions = get_option('widget_calendar');
	if ( $_POST["calendar-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["calendar-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_calendar', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="calendar-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="calendar-title" name="calendar-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="calendar-submit" name="calendar-submit" value="1" />
<?php
}

function wp_widget_text($args, $number = 1) {
	extract($args);
	$options = get_option('widget_text');
	$title = $options[$number]['title'];
	if ( empty($title) )
		$title = '&nbsp;';
	$text = $options[$number]['text'];
?>
		<?php echo $before_widget; ?>
			<?php $title ? print($before_title . $title . $after_title) : null; ?>
			<div class="textwidget"><?php echo $text; ?></div>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_text_control($number) {
	$options = $newoptions = get_option('widget_text');
	if ( !is_array($options) )
		$options = $newoptions = array();
	if ( $_POST["text-submit-$number"] ) {
		$newoptions[$number]['title'] = strip_tags(stripslashes($_POST["text-title-$number"]));
		$newoptions[$number]['text'] = stripslashes($_POST["text-text-$number"]);
		if ( !current_user_can('unfiltered_html') )
			$newoptions[$number]['text'] = stripslashes(wp_filter_post_kses($newoptions[$number]['text']));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_text', $options);
	}
	$title = htmlspecialchars($options[$number]['title'], ENT_QUOTES);
	$text = htmlspecialchars($options[$number]['text'], ENT_QUOTES);
?>
			<input style="width: 450px;" id="text-title-<?php echo "$number"; ?>" name="text-title-<?php echo "$number"; ?>" type="text" value="<?php echo $title; ?>" />
			<textarea style="width: 450px; height: 280px;" id="text-text-<?php echo "$number"; ?>" name="text-text-<?php echo "$number"; ?>"><?php echo $text; ?></textarea>
			<input type="hidden" id="text-submit-<?php echo "$number"; ?>" name="text-submit-<?php echo "$number"; ?>" value="1" />
<?php
}

function wp_widget_text_setup() {
	$options = $newoptions = get_option('widget_text');
	if ( isset($_POST['text-number-submit']) ) {
		$number = (int) $_POST['text-number'];
		if ( $number > 9 ) $number = 9;
		if ( $number < 1 ) $number = 1;
		$newoptions['number'] = $number;
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_text', $options);
		widget_text_register($options['number']);
	}
}

function wp_widget_text_page() {
	$options = $newoptions = get_option('widget_text');
?>
	<div class="wrap">
		<form method="POST">
			<h2><?php _e('Text Widgets'); ?></h2>
			<p style="line-height: 30px;"><?php _e('How many text widgets would you like?'); ?>
			<select id="text-number" name="text-number" value="<?php echo $options['number']; ?>">
<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
			</select>
			<span class="submit"><input type="submit" name="text-number-submit" id="text-number-submit" value="<?php _e('Save'); ?>" /></span></p>
		</form>
	</div>
<?php
}

function wp_widget_text_register() {
	$options = get_option('widget_text');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= 9; $i++) {
		$name = sprintf(__('Text %d'), $i);
		$id = "text-$i"; // Never never never translate an id
		register_sidebar_widget($name, $i <= $number ? 'widget_text' : /* unregister */ '', null, $id, $i);
		register_widget_control($name, $i <= $number ? 'widget_text_control' : /* unregister */ '', 460, 350, $id, $i);
	}
	add_action('sidebar_admin_setup', 'wp_widget_text_setup');
	add_action('sidebar_admin_page', 'wp_widget_text_page');
}

function wp_widget_categories($args) {
	extract($args);
	$options = get_option('widget_categories');
	$c = $options['count'] ? '1' : '0';
	$h = $options['hierarchical'] ? '1' : '0';
	$title = empty($options['title']) ? __('Categories') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php wp_list_cats("sort_column=name&optioncount=$c&hierarchical=$h"); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_categories_control() {
	$options = $newoptions = get_option('widget_categories');
	if ( $_POST['categories-submit'] ) {
		$newoptions['count'] = isset($_POST['categories-count']);
		$newoptions['hierarchical'] = isset($_POST['categories-hierarchical']);
		$newoptions['title'] = strip_tags(stripslashes($_POST['categories-title']));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_categories', $options);
	}
	$count = $options['count'] ? 'checked="checked"' : '';
	$hierarchical = $options['hierarchical'] ? 'checked="checked"' : '';
	$title = wp_specialchars($options['title']);
?>
			<p><label for="categories-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="categories-title" name="categories-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p style="text-align:right;margin-right:40px;"><label for="categories-count"><?php _e('Show post counts'); ?> <input class="checkbox" type="checkbox" <?php echo $count; ?> id="categories-count" name="categories-count" /></label></p>
			<p style="text-align:right;margin-right:40px;"><label for="categories-hierarchical" style="text-align:right;"><?php _e('Show hierarchy'); ?> <input class="checkbox" type="checkbox" <?php echo $hierarchical; ?> id="categories-hierarchical" name="categories-hierarchical" /></label></p>
			<input type="hidden" id="categories-submit" name="categories-submit" value="1" />
<?php
}

function wp_widget_recent_entries($args) {
	if ( $output = wp_cache_get('widget_recent_entries') )
		return print($output);

	ob_start();
	extract($args);
	$options = get_option('widget_recent_entries');
	$title = empty($options['title']) ? __('Recent Posts') : $options['title'];
	if ( !$number = (int) $options['number'] )
		$number = 10;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 15 )
		$number = 15;

	$r = new WP_Query("showposts=$number&what_to_show=posts&nopaging=0");
	if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php  while ($r->have_posts()) : $r->the_post(); ?>
			<li><a href="<?php the_permalink() ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></li>
			<?php endwhile; ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
	endif;
	wp_cache_add('widget_recent_entries', ob_get_flush());
}

function wp_flush_widget_recent_entries() {
	wp_cache_delete('widget_recent_entries');
}

add_action('save_post', 'wp_flush_widget_recent_entries');
add_action('post_deleted', 'wp_flush_widget_recent_entries');

function wp_widget_recent_entries_control() {
	$options = $newoptions = get_option('widget_recent_entries');
	if ( $_POST["recent-entries-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["recent-entries-title"]));
		$newoptions['number'] = (int) $_POST["recent-entries-number"];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_recent_entries', $options);
		wp_flush_widget_recent_entries();
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
	if ( !$number = (int) $options['number'] )
		$number = 5;
?>
			<p><label for="recent-entries-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="recent-entries-title" name="recent-entries-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><label for="recent-entries-number"><?php _e('Number of posts to show:'); ?> <input style="width: 25px; text-align: center;" id="recent-entries-number" name="recent-entries-number" type="text" value="<?php echo $number; ?>" /></label> <?php _e('(at most 15)'); ?></p>
			<input type="hidden" id="recent-entries-submit" name="recent-entries-submit" value="1" />
<?php
}

function wp_widget_recent_comments($args) {
	global $wpdb, $comments, $comment;
	extract($args, EXTR_SKIP);
	$options = get_option('widget_recent_comments');
	$title = empty($options['title']) ? __('Recent Comments') : $options['title'];
	if ( !$number = (int) $options['number'] )
		$number = 5;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 15 )
		$number = 15;

	if ( !$comments = wp_cache_get( 'recent_comments', 'widget' ) ) {
		$comments = $wpdb->get_results("SELECT comment_author, comment_author_url, comment_ID, comment_post_ID FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT $number");
		wp_cache_add( 'recent_comments', $comments, 'widget' );
	}
?>

		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul id="recentcomments"><?php
			if ( $comments ) : foreach ($comments as $comment) :
			echo  '<li class="recentcomments">' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
			endforeach; endif;?></ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_delete_recent_comments_cache() {
	wp_cache_delete( 'recent_comments', 'widget' );
}
add_action( 'comment_post', 'wp_delete_recent_comments_cache' );
add_action( 'wp_set_comment_status', 'wp_delete_recent_comments_cache' );

function wp_widget_recent_comments_control() {
	$options = $newoptions = get_option('widget_recent_comments');
	if ( $_POST["recent-comments-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["recent-comments-title"]));
		$newoptions['number'] = (int) $_POST["recent-comments-number"];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_recent_comments', $options);
		delete_recent_comments_cache();
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
	if ( !$number = (int) $options['number'] )
		$number = 5;
?>
			<p><label for="recent-comments-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="recent-comments-title" name="recent-comments-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><label for="recent-comments-number"><?php _e('Number of comments to show:'); ?> <input style="width: 25px; text-align: center;" id="recent-comments-number" name="recent-comments-number" type="text" value="<?php echo $number; ?>" /></label> <?php _e('(at most 15)'); ?></p>
			<input type="hidden" id="recent-comments-submit" name="recent-comments-submit" value="1" />
<?php
}

function wp_widget_recent_comments_style() {
?>
<style type="text/css">.recentcomments a{display:inline !important;padding: 0 !important;margin: 0 !important;}</style>
<?php
}

function wp_widget_recent_comments_register() {
	register_sidebar_widget(__('Recent Comments'), 'wp_widget_recent_comments', null, 'recent-comments');
	register_widget_control(__('Recent Comments'), 'wp_widget_recent_comments_control', 320, 90, 'recent-comments');
	
	if ( is_active_widget('wp_widget_recent_comments') )
		add_action('wp_head', 'wp_widget_recent_comments_style');
}

function wp_widget_rss($args, $number = 1) {
	require_once(ABSPATH . WPINC . '/rss.php');
	extract($args);
	$options = get_option('widget_rss');
	if ( isset($options['error']) && $options['error'] )
		return;
	$num_items = (int) $options[$number]['items'];
	$show_summary = $options[$number]['show_summary'];
	if ( empty($num_items) || $num_items < 1 || $num_items > 10 ) $num_items = 10;
	$url = $options[$number]['url'];
	while ( strstr($url, 'http') != $url )
		$url = substr($url, 1);
	if ( empty($url) )
		return;
	$rss = fetch_rss_summary($url, array( 'link', 'title', 'description' ) );
	$link = wp_specialchars(strip_tags($rss->channel['link']), 1);
	while ( strstr($link, 'http') != $link )
		$link = substr($link, 1);
	$desc = wp_specialchars(strip_tags(html_entity_decode($rss->channel['description'], ENT_QUOTES)), 1);
	$title = $options[$number]['title'];
	if ( empty($title) )
		$title = htmlentities(strip_tags($rss->channel['title']));
	if ( empty($title) )
		$title = $desc;
	if ( empty($title) )
		$title = __('Unknown Feed');
	$url = wp_specialchars(strip_tags($url), 1);
	if ( file_exists(dirname(__FILE__) . '/rss.png') )
		$icon = str_replace(ABSPATH, get_option('siteurl').'/', dirname(__FILE__)) . '/rss.png';
	else
		$icon = get_option('siteurl').'/wp-includes/images/rss.png';
	$title = "<a class='rsswidget' href='$url' title='Syndicate this content'><img style='background:orange;color:white;border:none;' width='14' height='14' src='$icon' alt='RSS' /></a> <a class='rsswidget' href='$link' title='$desc'>$title</a>";
?>
		<?php echo $before_widget; ?>
			<?php $title ? print($before_title . $title . $after_title) : null; ?>
			<ul>
<?php
	if ( is_array( $rss->items ) ) {
		$rss->items = array_slice($rss->items, 0, $num_items);
		foreach ($rss->items as $item ) {
			while ( strstr($item['link'], 'http') != $item['link'] )
				$item['link'] = substr($item['link'], 1);
			$link = wp_specialchars(strip_tags($item['link']), 1);
			$title = wp_specialchars(strip_tags($item['title']), 1);
			if ( empty($title) )
				$title = __('Untitled');
			$desc = '';
			if ( $show_summary ) {
				$summary = '<div class="rssSummary">' . $item['description'] . '</div>';
			} else {
				if ( isset( $item['description'] ) && is_string( $item['description'] ) )
					$desc = str_replace(array("\n", "\r"), ' ', wp_specialchars(strip_tags(html_entity_decode($item['description'], ENT_QUOTES)), 1));
				$summary = '';
			}
			echo "<li><a class='rsswidget' href='$link' title='$desc'>$title</a>$summary</li>";
		}
	} else {
		echo __('<li>An error has occured; the feed is probably down. Try again later.</li>');
	}
?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_rss_control($number) {
	$options = $newoptions = get_option('widget_rss');
	if ( $_POST["rss-submit-$number"] ) {
		$newoptions[$number]['items'] = (int) $_POST["rss-items-$number"];
		$url = strip_tags(stripslashes($_POST["rss-url-$number"]));
		$newoptions[$number]['title'] = trim(strip_tags(stripslashes($_POST["rss-title-$number"])));print_r($_POST);
		if ( $url !== $options[$number]['url'] ) {
			require_once(ABSPATH . WPINC . '/rss.php');
			$rss = fetch_rss_summary($url);
			if ( is_object($rss) && $rss->status == 200 ) {
				$newoptions[$number]['url'] = $url;
				$newoptions[$number]['error'] = false;
			} else {
				$newoptions[$number]['error'] = true;
				$newoptions[$number]['url'] = wp_specialchars(__('Error: could not find an RSS or ATOM feed at that URL.'), 1);
				$error = sprintf(__('Error in RSS %1$d: %2$s', 'sandbox'), $number, $newoptions[$number]['error']);
			}
		}
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_rss', $options);
	}
	$url = htmlspecialchars($options[$number]['url'], ENT_QUOTES);
	$items = (int) $options[$number]['items'];
	$title = htmlspecialchars($options[$number]['title'], ENT_QUOTES);
	if ( empty($items) || $items < 1 ) $items = 10;
?>
			<p style="text-align:center;"><?php _e('Enter the RSS feed URL here:'); ?></p>
			<input style="width: 400px;" id="rss-url-<?php echo "$number"; ?>" name="rss-url-<?php echo "$number"; ?>" type="text" value="<?php echo $url; ?>" />
			<p style="text-align:center;"><?php _e('Give the feed a title (optional):'); ?></p>
			<input style="width: 400px;" id="rss-title-<?php echo "$number"; ?>" name="rss-title-<?php echo "$number"; ?>" type="text" value="<?php echo $title; ?>" />
			<p style="text-align:center; line-height: 30px;"><?php _e('How many items would you like to display?'); ?> <select id="rss-items-<?php echo $number; ?>" name="rss-items-<?php echo $number; ?>"><?php for ( $i = 1; $i <= 10; ++$i ) echo "<option value='$i' ".($items==$i ? "selected='selected'" : '').">$i</option>"; ?></select></p>
			<input type="hidden" id="rss-submit-<?php echo "$number"; ?>" name="rss-submit-<?php echo "$number"; ?>" value="1" />
<?php
}

function wp_widget_rss_setup() {
	$options = $newoptions = get_option('widget_rss');
	if ( isset($_POST['rss-number-submit']) ) {
		$number = (int) $_POST['rss-number'];
		if ( $number > 9 ) $number = 9;
		if ( $number < 1 ) $number = 1;
		$newoptions['number'] = $number;
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_rss', $options);
		widget_rss_register($options['number']);
	}
}

function wp_widget_rss_page() {
	$options = $newoptions = get_option('widget_rss');
?>
	<div class="wrap">
		<form method="POST">
			<h2><?php _e('RSS Feed Widgets'); ?></h2>
			<p style="line-height: 30px;"><?php _e('How many RSS widgets would you like?'); ?>
			<select id="rss-number" name="rss-number" value="<?php echo $options['number']; ?>">
<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?>
			</select>
			<span class="submit"><input type="submit" name="rss-number-submit" id="rss-number-submit" value="<?php _e('Save'); ?>" /></span></p>
		</form>
	</div>
<?php
}

function wp_widget_rss_register() {
	$options = get_option('widget_rss');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	for ($i = 1; $i <= 9; $i++) {
		$name = sprintf(__('RSS %d'), $i);
		$id = "rss-$i"; // Never never never translate an id
		register_sidebar_widget($name, $i <= $number ? 'widget_rss' : /* unregister */ '', null, $id, $i);
		register_widget_control($name, $i <= $number ? 'widget_rss_control' : /* unregister */ '', 410, 200, $id, $i);
	}
	add_action('sidebar_admin_setup', 'wp_widget_rss_setup');
	add_action('sidebar_admin_page', 'wp_widget_rss_page');
}

function wp_widgets_init() {
	global $wp_register_widget_defaults;

	$wp_register_widget_defaults = true;

	register_sidebar_widget(__('Pages'), 'wp_widget_pages', null, 'pages');
	register_widget_control(__('Pages'), 'wp_widget_pages_control', 300, 90, 'pages');
	register_sidebar_widget(__('Calendar'), 'wp_widget_calendar', null, 'calendar');
	register_widget_control(__('Calendar'), 'wp_widget_calendar_control', 300, 90, 'calendar');
	register_sidebar_widget(__('Archives'), 'wp_widget_archives', null, 'archives');
	register_widget_control(__('Archives'), 'wp_widget_archives_control', 300, 90, 'archives');
	register_sidebar_widget(__('Links'), 'wp_widget_links', null, 'links');
	register_sidebar_widget(__('Meta'), 'wp_widget_meta', null, 'meta');
	register_widget_control(__('Meta'), 'wp_widget_meta_control', 300, 90, 'meta');
	register_sidebar_widget(__('Search'), 'wp_widget_search', null, 'search');
	register_sidebar_widget(__('Categories'), 'wp_widget_categories', null, 'categories');
	register_widget_control(__('Categories'), 'wp_widget_categories_control', 300, 150, 'categories');
	register_sidebar_widget(__('Recent Posts'), 'wp_widget_recent_entries', null, 'recent-posts');
	register_widget_control(__('Recent Posts'), 'wp_widget_recent_entries_control', 300, 90, 'recent-posts');
	wp_widget_text_register();
	wp_widget_rss_register();
	wp_widget_recent_comments_register();

	$wp_register_widget_defaults = false;

	do_action('widgets_init');
}

?>