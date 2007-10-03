<?php

/* Global Variables */

global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_styles, $wp_registered_widget_defaults;

$wp_registered_sidebars = array();
$wp_registered_widgets = array();
$wp_registered_widget_controls = array();
$wp_registered_widget_styles = array();
$wp_register_widget_defaults = false;

/* Template tags & API functions */

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

function register_sidebar($args = array()) {
	global $wp_registered_sidebars;

	if ( is_string($args) )
		parse_str($args, $args);

	$i = count($wp_registered_sidebars) + 1;

	$defaults = array(
		'name' => sprintf(__('Sidebar %d'), $i ),
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

function unregister_sidebar( $name ) {
	global $wp_registered_sidebars;

	if ( isset( $wp_registered_sidebars[$name] ) )
		unset( $wp_registered_sidebars[$name] );
}

function register_sidebar_widget($name, $output_callback, $classname = '') {
	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	$id = sanitize_title($name);
	$options = array();
	if ( !empty($classname) && is_string($classname) )
		$options['classname'] = $classname;
	$params = array_slice(func_get_args(), 2);
	$args = array($id, $name, $output_callback, $options);
	if ( !empty($params) )
		$args = array_merge($args, $params);

	call_user_func_array('wp_register_sidebar_widget', $args);
}

function wp_register_sidebar_widget($id, $name, $output_callback, $options = array()) {

	global $wp_registered_widgets, $wp_register_widget_defaults;

	$id = sanitize_title($id);

	if ( empty($output_callback) ) {
		unset($wp_registered_widgets[$id]);
		return;
	}

	$defaults = array('classname' => $output_callback);
	$options = wp_parse_args($options, $defaults);
	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $output_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	if ( is_callable($output_callback) && ( !isset($wp_registered_widgets[$id]) || !$wp_register_widget_defaults) )
		$wp_registered_widgets[$id] = $widget;
}

function unregister_sidebar_widget($id) {
	return wp_unregister_sidebar_widget($id);
}

function wp_unregister_sidebar_widget($id) {
	wp_register_sidebar_widget($id, '', '');
	wp_unregister_widget_control($id);
}

function register_widget_control($name, $control_callback, $width = '', $height = '') {
	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	$id = sanitize_title($name);
	$options = array();
	if ( !empty($width) )
		$options['width'] = $width;
	if ( !empty($height) )
		$options['height'] = $height;
	$params = array_slice(func_get_args(), 4);
	$args = array($id, $name, $control_callback, $options);
	if ( !empty($params) )
		$args = array_merge($args, $params);

	call_user_func_array('wp_register_widget_control', $args);
}

function wp_register_widget_control($id, $name, $control_callback, $options = array()) {
	global $wp_registered_widget_controls, $wp_register_widget_defaults;

	$id = sanitize_title($id);

	if ( empty($control_callback) ) {
		unset($wp_registered_widget_controls[$id]);
		return;
	}

	if ( isset($wp_registered_widget_controls[$id]) && $wp_register_widget_defaults )
		return;

	$defaults = array('width' => 300, 'height' => 200);
	$options = wp_parse_args($options, $defaults);
	$options['width'] = (int) $options['width'];
	$options['height'] = (int) $options['height'];
	$options['width'] = $options['width'] > 90 ? $options['width'] + 60 : 360;
	$options['height'] = $options['height'] > 60 ? $options['height'] + 40 : 240;

	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $control_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	$wp_registered_widget_controls[$id] = $widget;
}

function unregister_widget_control($id) {
	return wp_unregister_widget_control($id);
}

function wp_unregister_widget_control($id) {
	return wp_register_widget_control($id, '', '');
}

function dynamic_sidebar($index = 1) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	if ( is_int($index) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title($index);
		foreach ( $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title($value['name']) == $index ) {
				$index = $key;
				break;
			}
		}
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
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		}
		$classname_ = ltrim($classname_, '_');
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	return $did_one;
}

function is_active_widget($callback) {
	global $wp_registered_widgets;

	$sidebars_widgets = wp_get_sidebars_widgets(false);

	if ( is_array($sidebars_widgets) ) foreach ( $sidebars_widgets as $sidebar => $widgets )
		if ( is_array($widgets) ) foreach ( $widgets as $widget )
			if ( $wp_registered_widgets[$widget]['callback'] == $callback )
				return true;

	return false;
}

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

/* Internal Functions */

function wp_get_sidebars_widgets($update = true) {
	global $wp_registered_widgets, $wp_registered_sidebars;

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
			$sidebars_widgets = $_sidebars_widgets;
			unset($_sidebars_widgets);

		case 2 :
			$sidebars = array_keys( $wp_registered_sidebars );
			if ( !empty( $sidebars ) ) {
				// Move the known-good ones first
				foreach ( $sidebars as $id ) {
					if ( array_key_exists( $id, $sidebars_widgets ) ) {
						$_sidebars_widgets[$id] = $sidebars_widgets[$id];
						unset($sidebars_widgets[$id], $sidebars[$id]);
					}
				}

				// Assign to each unmatched registered sidebar the first available orphan
				unset( $sidebars_widgets[ 'array_version' ] );
				while ( ( $sidebar = array_shift( $sidebars ) ) && $widgets = array_shift( $sidebars_widgets ) )
					$_sidebars_widgets[ $sidebar ] = $widgets;

				$_sidebars_widgets['array_version'] = 3;
				$sidebars_widgets = $_sidebars_widgets;
				unset($_sidebars_widgets);
			}

			if ( $update )
				update_option('sidebars_widgets', $sidebars_widgets);
	}

	unset($sidebars_widgets['array_version']);

	return $sidebars_widgets;
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

function wp_widget_pages( $args ) {
	extract( $args );
	$options = get_option( 'widget_pages' );

	$title = empty( $options['title'] ) ? __( 'Pages' ) : $options['title'];
	$sortby = empty( $options['sortby'] ) ? 'menu_order' : $options['sortby'];
	$exclude = empty( $options['exclude'] ) ? '' : $options['exclude'];

	if ( $sortby == 'menu_order' ) {
		$sortby = 'menu_order, post_title';
	}

	$out = wp_list_pages( array('title_li' => '', 'echo' => 0, 'sort_column' => $sortby, 'exclude' => $exclude) );

	if ( !empty( $out ) ) {
?>
	<?php echo $before_widget; ?>
		<?php echo $before_title . $title . $after_title; ?>
		<ul>
			<?php echo $out; ?>
		</ul>
	<?php echo $after_widget; ?>
<?php
	}
}

function wp_widget_pages_control() {
	$options = $newoptions = get_option('widget_pages');
	if ( $_POST['pages-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['pages-title']));

		$sortby = stripslashes( $_POST['pages-sortby'] );

		if ( in_array( $sortby, array( 'post_title', 'menu_order', 'ID' ) ) ) {
			$newoptions['sortby'] = $sortby;
		} else {
			$newoptions['sortby'] = 'menu_order';
		}

		$newoptions['exclude'] = strip_tags( stripslashes( $_POST['pages-exclude'] ) );
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_pages', $options);
	}
	$title = attribute_escape($options['title']);
	$exclude = attribute_escape( $options['exclude'] );
?>
			<p><label for="pages-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="pages-title" name="pages-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><label for="pages-sortby"><?php _e( 'Sort by:' ); ?>
				<select name="pages-sortby" id="pages-sortby">
					<option value="post_title"<?php selected( $options['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
					<option value="menu_order"<?php selected( $options['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
					<option value="ID"<?php selected( $options['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
				</select></label></p>
			<p><label for="pages-exclude"><?php _e( 'Exclude:' ); ?> <input type="text" value="<?php echo $exclude; ?>" name="pages-exclude" id="pages-exclude" style="width: 180px;" /></label><br />
			<small><?php _e( 'Page IDs, separated by commas.' ); ?></small></p>
			<input type="hidden" id="pages-submit" name="pages-submit" value="1" />
<?php
}

function wp_widget_links($args) {
	extract($args, EXTR_SKIP);
	wp_list_bookmarks(array(
		'title_before' => $before_title, 'title_after' => $after_title,
		'category_before' => $before_widget, 'category_after' => $after_widget,
		'show_images' => true, 'class' => 'linkcat widget'
	));
}

function wp_widget_search($args) {
	extract($args);
?>
		<?php echo $before_widget; ?>
			<form id="searchform" method="get" action="<?php bloginfo('home'); ?>">
			<div>
			<input type="text" name="s" id="s" size="15" /><br />
			<input type="submit" value="<?php echo attribute_escape(__('Search')); ?>" />
			</div>
			</form>
		<?php echo $after_widget; ?>
<?php
}

function wp_widget_archives($args) {
	extract($args);
	$options = get_option('widget_archives');
	$c = $options['count'] ? '1' : '0';
	$d = $options['dropdown'] ? '1' : '0';
	$title = empty($options['title']) ? __('Archives') : $options['title'];

	echo $before_widget;
	echo $before_title . $title . $after_title;

	if($d) {
?>
		<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'> <option value=""><?php echo attribute_escape(__('Select Month')); ?></option> <?php wp_get_archives("type=monthly&format=option&show_post_count=$c"); ?> </select>
<?php
	} else {
?>
		<ul>
		<?php wp_get_archives("type=monthly&show_post_count=$c"); ?>
		</ul>
<?php
	}

	echo $after_widget;
}

function wp_widget_archives_control() {
	$options = $newoptions = get_option('widget_archives');
	if ( $_POST["archives-submit"] ) {
		$newoptions['count'] = isset($_POST['archives-count']);
		$newoptions['dropdown'] = isset($_POST['archives-dropdown']);
		$newoptions['title'] = strip_tags(stripslashes($_POST["archives-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_archives', $options);
	}
	$count = $options['count'] ? 'checked="checked"' : '';
	$dropdown = $options['dropdown'] ? 'checked="checked"' : '';
	$title = attribute_escape($options['title']);
?>
			<p><label for="archives-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="archives-title" name="archives-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p style="text-align:right;margin-right:40px;"><label for="archives-count"><?php _e('Show post counts'); ?> <input class="checkbox" type="checkbox" <?php echo $count; ?> id="archives-count" name="archives-count" /></label></p>
			<p style="text-align:right;margin-right:40px;"><label for="archives-dropdown"><?php _e('Display as a drop down'); ?> <input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="archives-dropdown" name="archives-dropdown" /></label></p>
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
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php echo attribute_escape(__('Syndicate this site using RSS 2.0')); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php echo attribute_escape(__('The latest comments to all posts in RSS')); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="http://wordpress.org/" title="<?php echo attribute_escape(__('Powered by WordPress, state-of-the-art semantic personal publishing platform.')); ?>">WordPress.org</a></li>
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
	$title = attribute_escape($options['title']);
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
	$title = attribute_escape($options['title']);
?>
			<p><label for="calendar-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="calendar-title" name="calendar-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="calendar-submit" name="calendar-submit" value="1" />
<?php
}

function wp_widget_text($args, $number = 1) {
	extract($args);
	$options = get_option('widget_text');
	$title = $options[$number]['title'];
	$text = apply_filters( 'widget_text', $options[$number]['text'] );
?>
		<?php echo $before_widget; ?>
			<?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
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
	$title = attribute_escape($options[$number]['title']);
	$text = format_to_edit($options[$number]['text']);
?>
			<input style="width: 450px;" id="text-title-<?php echo $number; ?>" name="text-title-<?php echo $number; ?>" type="text" value="<?php echo $title; ?>" />
			<textarea style="width: 450px; height: 280px;" id="text-text-<?php echo $number; ?>" name="text-text-<?php echo $number; ?>"><?php echo $text; ?></textarea>
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
		wp_widget_text_register($options['number']);
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
			<span class="submit"><input type="submit" name="text-number-submit" id="text-number-submit" value="<?php echo attribute_escape(__('Save')); ?>" /></span></p>
		</form>
	</div>
<?php
}

function wp_widget_text_register() {
	$options = get_option('widget_text');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	$dims = array('width' => 460, 'height' => 350);
	$class = array('classname' => 'widget_text');
	for ($i = 1; $i <= 9; $i++) {
		$name = sprintf(__('Text %d'), $i);
		$id = "text-$i"; // Never never never translate an id
		wp_register_sidebar_widget($id, $name, $i <= $number ? 'wp_widget_text' : /* unregister */ '', $class, $i);
		wp_register_widget_control($id, $name, $i <= $number ? 'wp_widget_text_control' : /* unregister */ '', $dims, $i);
	}
	add_action('sidebar_admin_setup', 'wp_widget_text_setup');
	add_action('sidebar_admin_page', 'wp_widget_text_page');
}

function wp_widget_categories($args, $number = 1) {
	extract($args);
	$options = get_option('widget_categories');

	$c = $options[$number]['count'] ? '1' : '0';
	$h = $options[$number]['hierarchical'] ? '1' : '0';
	$d = $options[$number]['dropdown'] ? '1' : '0';

	$title = empty($options[$number]['title']) ? __('Categories') : $options[$number]['title'];

	echo $before_widget;
	echo $before_title . $title . $after_title;

	$cat_args = "orderby=name&show_count={$c}&hierarchical={$h}";

	if ( $d ) {
		wp_dropdown_categories($cat_args . '&show_option_none= ' . __('Select Category'));
?>

<script lang='javascript'><!--
    var dropdown = document.getElementById("cat");
    function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo get_option('home'); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
    }
    dropdown.onchange = onCatChange;
--></script>

<?php
	} else {
?>
		<ul>
		<?php wp_list_categories($cat_args . '&title_li='); ?>
		</ul>
<?php
	}

	echo $after_widget;
}

function wp_widget_categories_control( $number ) {
	$options = $newoptions = get_option('widget_categories');

	if ( !is_array( $options ) ) {
		$options = $newoptions = get_option( 'widget_categories' );
	}

	if ( $_POST['categories-submit-' . $number] ) {
		$newoptions[$number]['count'] = isset($_POST['categories-count-' . $number]);
		$newoptions[$number]['hierarchical'] = isset($_POST['categories-hierarchical-' . $number]);
		$newoptions[$number]['dropdown'] = isset($_POST['categories-dropdown-' . $number]);
		$newoptions[$number]['title'] = strip_tags(stripslashes($_POST['categories-title-' . $number]));
	}

	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_categories', $options);
	}

	$title = attribute_escape( $options[$number]['title'] );
?>
			<p><label for="categories-title-<?php echo $number; ?>">
				<?php _e( 'Title:' ); ?> <input style="width:300px" id="categories-title-<?php echo $number; ?>" name="categories-title-<?php echo $number; ?>" type="text" value="<?php echo $title; ?>" />
			</label></p>

			<p><label for="categories-dropdown-<?php echo $number; ?>">
				<input type="checkbox" class="checkbox" id="categories-dropdown-<?php echo $number; ?>" name="categories-dropdown-<?php echo $number; ?>"<?php echo $options[$number]['dropdown'] ? ' checked="checked"' : ''; ?> /> <?php _e( 'Show as dropdown' ); ?>
			</label></p>

			<p><label for="categories-count-<?php echo $number; ?>">
				<input type="checkbox" class="checkbox" id="categories-count-<?php echo $number; ?>" name="categories-count-<?php echo $number; ?>"<?php echo $options[$number]['count'] ? ' checked="checked"' : ''; ?> /> <?php _e( 'Show post counts' ); ?>
			</label></p>

			<p><label for="categories-hierarchical-<?php echo $number; ?>">
				<input type="checkbox" class="checkbox" id="categories-hierarchical-<?php echo $number; ?>" name="categories-hierarchical-<?php echo $number; ?>"<?php echo $options[$number]['hierarchical'] ? ' checked="checked"' : ''; ?> /> <?php _e( 'Show hierarchy' ); ?>
			</label></p>

			<input type="hidden" id="categories-submit-<?php echo $number; ?>" name="categories-submit-<?php echo $number; ?>" value="1" />
<?php
}

function wp_widget_categories_setup() {
	$options = $newoptions = get_option( 'widget_categories' );

	if ( isset( $_POST['categories-number-submit'] ) ) {
		$number = (int) $_POST['categories-number'];

		if ( $number > 9 ) {
			$number = 9;
		} elseif ( $number < 1 ) {
			$number = 1;
		}

		$newoptions['number'] = $number;
	}

	if ( $newoptions != $options ) {
		$options = $newoptions;
		update_option( 'widget_categories', $options );
		wp_widget_categories_register( $options['number'] );
	}
}

function wp_widget_categories_page() {
	$options = get_option( 'widget_categories' );
?>
	<div class="wrap">
		<form method="post">
			<h2><?php _e( 'Categories Widgets' ); ?></h2>
			<p style="line-height: 30px;"><?php _e( 'How many categories widgets would you like?' ); ?>
				<select id="categories-number" name="categories-number" value="<?php echo attribute_escape( $options['number'] ); ?>">
					<?php
						for ( $i = 1; $i < 10; $i++ ) {
							echo '<option value="' . $i . '"' . ( $i == $options['number'] ? ' selected="selected"' : '' ) . '>' . $i . "</option>\n";
						}
					?>
				</select>
				<span class="submit">
					<input type="submit" value="<?php echo attribute_escape( __( 'Save' ) ); ?>" id="categories-number-submit" name="categories-number-submit" />
				</span>
			</p>
		</form>
	</div>
<?php
}

function wp_widget_categories_upgrade() {
	$options = get_option( 'widget_categories' );

	$newoptions = array( 'number' => 1, 1 => $options );

	update_option( 'widget_categories', $newoptions );

	$sidebars_widgets = get_option( 'sidebars_widgets' );
	if ( is_array( $sidebars_widgets ) ) {
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( is_array( $widgets ) ) {
				foreach ( $widgets as $widget )
					$new_widgets[$sidebar][] = ( $widget == 'categories' ) ? 'categories-1' : $widget;
			} else {
				$new_widgets[$sidebar] = $widgets;
			}
		}
		if ( $new_widgets != $sidebars_widgets )
			update_option( 'sidebars_widgets', $new_widgets );
	}

	if ( isset( $_POST['categories-submit'] ) ) {
		$_POST['categories-submit-1'] = $_POST['categories-submit'];
		$_POST['categories-count-1'] = $_POST['categories-count'];
		$_POST['categories-hierarchical-1'] = $_POST['categories-hierarchical'];
		$_POST['categories-dropdown-1'] = $_POST['categories-dropdown'];
		$_POST['categories-title-1'] = $_POST['categories-title'];
		foreach ( $_POST as $k => $v )
			if ( substr($k, -5) == 'order' )
				$_POST[$k] = str_replace('categories', 'categories-1', $v);
	}

	return $newoptions;
}

function wp_widget_categories_register() {
	$options = get_option( 'widget_categories' );
	if ( !isset($options['number']) )
		$options = wp_widget_categories_upgrade();
	$number = (int) $options['number'];

	if ( $number > 9 ) {
		$number = 9;
	} elseif ( $number < 1 ) {
		$number = 1;
	}

	$dims = array( 'width' => 350, 'height' => 170 );
	$class = array( 'classname' => 'widget_categories' );

	for ( $i = 1; $i <= 9; $i++ ) {
		$name = sprintf( __( 'Categories %d' ), $i );
		$id = 'categories-' . $i;

		$widget_callback = ( $i <= $number ) ? 'wp_widget_categories' : '';
		$control_callback = ( $i <= $number ) ? 'wp_widget_categories_control' : '';

		wp_register_sidebar_widget( $id, $name, $widget_callback, $class, $i );
		wp_register_widget_control( $id, $name, $control_callback, $dims, $i );
	}

	add_action( 'sidebar_admin_setup', 'wp_widget_categories_setup' );
	add_action( 'sidebar_admin_page', 'wp_widget_categories_page' );
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

	$r = new WP_Query("showposts=$number&what_to_show=posts&nopaging=0&post_status=publish");
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
add_action('deleted_post', 'wp_flush_widget_recent_entries');

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
	$title = attribute_escape($options['title']);
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
		wp_delete_recent_comments_cache();
	}
	$title = attribute_escape($options['title']);
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
	$dims = array('width' => 320, 'height' => 90);
	$class = array('classname' => 'widget_recent_comments');
	wp_register_sidebar_widget('recent-comments', __('Recent Comments'), 'wp_widget_recent_comments', $class);
	wp_register_widget_control('recent-comments', __('Recent Comments'), 'wp_widget_recent_comments_control', $dims);

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
	$rss = fetch_rss($url);
	$link = clean_url(strip_tags($rss->channel['link']));
	while ( strstr($link, 'http') != $link )
		$link = substr($link, 1);
	$desc = attribute_escape(strip_tags(html_entity_decode($rss->channel['description'], ENT_QUOTES)));
	$title = $options[$number]['title'];
	if ( empty($title) )
		$title = htmlentities(strip_tags($rss->channel['title']));
	if ( empty($title) )
		$title = $desc;
	if ( empty($title) )
		$title = __('Unknown Feed');
	$url = clean_url(strip_tags($url));
	if ( file_exists(dirname(__FILE__) . '/rss.png') )
		$icon = str_replace(ABSPATH, get_option('siteurl').'/', dirname(__FILE__)) . '/rss.png';
	else
		$icon = get_option('siteurl').'/wp-includes/images/rss.png';
	$title = "<a class='rsswidget' href='$url' title='" . attribute_escape(__('Syndicate this content')) ."'><img style='background:orange;color:white;border:none;' width='14' height='14' src='$icon' alt='RSS' /></a> <a class='rsswidget' href='$link' title='$desc'>$title</a>";
?>
		<?php echo $before_widget; ?>
			<?php $title ? print($before_title . $title . $after_title) : null; ?>
<?php
	if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
		$rss->items = array_slice($rss->items, 0, $num_items);
		echo '<ul>';
		foreach ($rss->items as $item ) {
			while ( strstr($item['link'], 'http') != $item['link'] )
				$item['link'] = substr($item['link'], 1);
			$link = clean_url(strip_tags($item['link']));
			$title = attribute_escape(strip_tags($item['title']));
			if ( empty($title) )
				$title = __('Untitled');
			$desc = '';
			if ( $show_summary ) {
				$summary = '<div class="rssSummary">' . $item['description'] . '</div>';
			} else {
				if ( isset( $item['description'] ) && is_string( $item['description'] ) )
					$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['description'], ENT_QUOTES))));
				$summary = '';
			}
			echo "<li><a class='rsswidget' href='$link' title='$desc'>$title</a>$summary</li>";
		}
		echo '</ul>';
	} else {
		echo '<ul><li>' . __( 'An error has occurred; the feed is probably down. Try again later.' ) . '</li></ul>';
	}

	echo $after_widget;
}

function wp_widget_rss_control($number) {
	$options = $newoptions = get_option('widget_rss');
	if ( $_POST["rss-submit-$number"] ) {
		$newoptions[$number]['items'] = (int) $_POST["rss-items-$number"];
		$url = sanitize_url(strip_tags(stripslashes($_POST["rss-url-$number"])));
		$newoptions[$number]['title'] = trim(strip_tags(stripslashes($_POST["rss-title-$number"])));
		if ( $url !== $options[$number]['url'] ) {
			require_once(ABSPATH . WPINC . '/rss.php');
			$rss = fetch_rss($url);
			if ( is_object($rss) ) {
				$newoptions[$number]['url'] = $url;
				$newoptions[$number]['error'] = false;
			} else {
				$newoptions[$number]['error'] = true;
				$newoptions[$number]['url'] = wp_specialchars(__('Error: could not find an RSS or ATOM feed at that URL.'), 1);
				$error = sprintf(__('Error in RSS %1$d: %2$s'), $number, $newoptions[$number]['error']);
			}
		}
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_rss', $options);
	}
	$url = attribute_escape($options[$number]['url']);
	$items = (int) $options[$number]['items'];
	$title = attribute_escape($options[$number]['title']);
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
		wp_widget_rss_register($options['number']);
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
			<span class="submit"><input type="submit" name="rss-number-submit" id="rss-number-submit" value="<?php echo attribute_escape(__('Save')); ?>" /></span></p>
		</form>
	</div>
<?php
}

function wp_widget_rss_register() {
	$options = get_option('widget_rss');
	$number = $options['number'];
	if ( $number < 1 ) $number = 1;
	if ( $number > 9 ) $number = 9;
	$dims = array('width' => 410, 'height' => 200);
	$class = array('classname' => 'widget_rss');
	for ($i = 1; $i <= 9; $i++) {
		$name = sprintf(__('RSS %d'), $i);
		$id = "rss-$i"; // Never never never translate an id
		wp_register_sidebar_widget($id, $name, $i <= $number ? 'wp_widget_rss' : /* unregister */ '', $class, $i);
		wp_register_widget_control($id, $name, $i <= $number ? 'wp_widget_rss_control' : /* unregister */ '', $dims, $i);
	}
	add_action('sidebar_admin_setup', 'wp_widget_rss_setup');
	add_action('sidebar_admin_page', 'wp_widget_rss_page');
}

function wp_widget_tag_cloud($args) {
	extract($args);
	$options = get_option('widget_tag_cloud');
	$title = empty($options['title']) ? __('Tags') : $options['title'];

	echo $before_widget;
	echo $before_title . $title . $after_title;
	wp_tag_cloud();
	echo $after_widget;
}

function wp_widget_tag_cloud_control() {
	$options = $newoptions = get_option('widget_tag_cloud');

	if ( $_POST['tag-cloud-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['tag-cloud-title']));
	}

	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_tag_cloud', $options);
	}

	$title = attribute_escape( $options['title'] );
?>
	<p><label for="tag-cloud-title">
	<?php _e('Title:') ?> <input type="text" style="width:300px" id="tag-cloud-title" name="tag-cloud-title" value="<?php echo $title ?>" /></label>
	</p>
	<input type="hidden" name="tag-cloud-submit" id="tag-cloud-submit" value="1" />
<?php
}

function wp_widgets_init() {
	if ( !is_blog_installed() )
		return;

	$GLOBALS['wp_register_widget_defaults'] = true;

	$dims90 = array( 'height' => 90, 'width' => 300 );
	$dims100 = array( 'height' => 100, 'width' => 300 );
	$dims150 = array( 'height' => 150, 'width' => 300 );

	$class = array('classname' => 'widget_pages');
	wp_register_sidebar_widget('pages', __('Pages'), 'wp_widget_pages', $class);
	wp_register_widget_control('pages', __('Pages'), 'wp_widget_pages_control', $dims150);

	$class['classname'] = 'widget_calendar';
	wp_register_sidebar_widget('calendar', __('Calendar'), 'wp_widget_calendar', $class);
	wp_register_widget_control('calendar', __('Calendar'), 'wp_widget_calendar_control', $dims90);

	$class['classname'] = 'widget_archives';
	wp_register_sidebar_widget('archives', __('Archives'), 'wp_widget_archives', $class);
	wp_register_widget_control('archives', __('Archives'), 'wp_widget_archives_control', $dims100);

	$class['classname'] = 'widget_links';
	wp_register_sidebar_widget('links', __('Links'), 'wp_widget_links', $class);

	$class['classname'] = 'widget_meta';
	wp_register_sidebar_widget('meta', __('Meta'), 'wp_widget_meta', $class);
	wp_register_widget_control('meta', __('Meta'), 'wp_widget_meta_control', $dims90);

	$class['classname'] = 'widget_search';
	wp_register_sidebar_widget('search', __('Search'), 'wp_widget_search', $class);

	$class['classname'] = 'widget_recent_entries';
	wp_register_sidebar_widget('recent-posts', __('Recent Posts'), 'wp_widget_recent_entries', $class);
	wp_register_widget_control('recent-posts', __('Recent Posts'), 'wp_widget_recent_entries_control', $dims90);

	$class['classname'] = 'widget_tag_cloud';
	wp_register_sidebar_widget('tag_cloud', __('Tag Cloud'), 'wp_widget_tag_cloud', $class);
	wp_register_widget_control('tag_cloud', __('Tag Cloud'), 'wp_widget_tag_cloud_control', 'width=300&height=160');

	wp_widget_categories_register();
	wp_widget_text_register();
	wp_widget_rss_register();
	wp_widget_recent_comments_register();

	$GLOBALS['wp_register_widget_defaults'] = false;

	do_action('widgets_init');
}

add_action('init', 'wp_widgets_init', 1);

?>
