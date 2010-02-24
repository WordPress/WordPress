<?php
/**
 * WordPress Administration for Navigation Menus
 * Interface functions
 *
 * @version 1.1.0
 *
 * @package WordPress
 * @subpackage Administration
 */

require_once('admin.php');

if ( ! current_user_can('switch_themes') )
	wp_die( __( 'Cheatin&#8217; uh?' ));

wp_admin_css( 'custom-navigation' );
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-ui-draggable' );
wp_enqueue_script( 'jquery-ui-droppable' );
wp_enqueue_script( 'jquery-ui-sortable' );
wp_enqueue_script( 'jquery-ui-dialog' );
wp_enqueue_script( 'custom-navigation-dynamic-functions' );
wp_enqueue_script( 'custom-navigation-default-items' );
wp_enqueue_script( 'jquery-autocomplete' );
wp_enqueue_script( 'custom-navigation-php-functions' );

require_once('admin-header.php');
require_once(ABSPATH . 'wp-admin/includes/nav-menu.php');

function wp_reset_nav_menu() {
	wp_nav_menu_setup(true);
	return true;
}

$messages_div = '';
$menu_id_in_edit = 0;
$updated = false;
$advanced_option_descriptions = 'no';

// Check which menu is selected and if menu is in edit already
if ( isset( $_GET['edit-menu'] ) ) {
	$menu_selected_id = (int) $_GET['edit-menu'];
	$updated = true;
} elseif ( isset( $_POST[ 'menu-id-in-edit' ] ) ) {
	$menu_selected_id = (int) $_POST[ 'menu-id-in-edit' ];
} else {
	$menu_selected_id = 0;
}

if ( isset( $_POST[ 'delete-menu' ] ) && $menu_selected_id > 0 ) {
	wp_delete_nav_menu( $menu_selected_id );
	$menu_selected_id = 0;
	$updated = true;
}

// Default Menu to show
$custom_menus = wp_get_nav_menus();
if ( ! $menu_selected_id && ! empty( $custom_menus ) )
	$menu_selected_id = $custom_menus[0]->term_id;

$menu_title = '';
if ( $menu_selected_id > 0 ) {
	foreach ( $custom_menus as $menu ) {
		if ( $menu->term_id == $menu_selected_id ) {
			$menu_title = $menu->name;
			break;
		}
	}
}

if ( isset( $_POST['li-count'] ) )
	$post_counter = $_POST['li-count'];
else
	$post_counter = 0;

// Create a new menu. Menus are stored as terms in the 'menu' taxonomy.
if ( isset( $_POST['add-menu'] ) && ! $updated ) {
	$insert_menu_name = $_POST['add-menu-name'];

	if ( $insert_menu_name ) {
		$menu = wp_create_nav_menu( $insert_menu_name );
		if ( is_wp_error( $menu ) ) {
			$messages_div = '<div id="message" class="error fade below-h2"><p>' . $menu->get_error_message() . '</p></div>';
		} else {
			$custom_menus[$menu->term_id] = $menu;
			$menu_selected_id = $menu->term_id;
			$menu_id_in_edit = $menu_selected_id;
			$menu_title = $menu->name;
			$messages_div = '<div id="message" class="updated fade below-h2"><p>' . sprintf( __('&#8220;%s&#8221; menu has been created.'), esc_html( $menu->name ) ) . '</p></div>';
			$post_counter = 0;
		}
	} else {
		$messages_div = '<div id="message" class="error fade below-h2"><p>' . __('Please enter a valid menu name.') . '</p></div>';
	}
	$updated = true;
}

if ( $post_counter > 0 && $menu_selected_id > 0 && ! $updated ) {
	$menu_items = wp_get_nav_menu_items( $menu_selected_id, array('orderby' => 'ID', 'output' => ARRAY_A, 'output_key' => 'ID') );
	$parent_menu_ids = array();

	// Loop through all POST variables
	for ( $k = 1; $k <= $post_counter; $k++ ) {
		$db_id = isset( $_POST['dbid'.$k] )? $_POST['dbid'.$k] : 0;
		$object_id = isset( $_POST['postmenu'.$k] )? $_POST['postmenu'.$k] : 0;
		$parent_id = isset( $_POST['parent'.$k] )? $_POST['parent'.$k] : 0;
		$custom_title = isset( $_POST['title'.$k] )?  $_POST['title'.$k] : '';
		$custom_linkurl = isset( $_POST['linkurl'.$k] )? $_POST['linkurl'.$k] : '';
		$custom_description = isset( $_POST['description'.$k] )? $_POST['description'.$k] : '';
		// doesn't seem to be used by UI
		$icon = isset( $_POST['icon'.$k] )? $_POST['icon'.$k] : 0;
		$position = isset( $_POST['position'.$k] )? $_POST['position'.$k] : 0;
		$linktype = isset( $_POST['linktype'.$k] )? $_POST['linktype'.$k] : 'custom';
		$custom_anchor_title  = isset( $_POST['anchortitle'.$k] )? $_POST['anchortitle'.$k] : $custom_title;
		$new_window = isset( $_POST['newwindow'.$k] )? $_POST['newwindow'.$k] : 0;

		$post = array( 'post_status' => 'publish', 'post_type' => 'nav_menu_item', 'post_author' => $user_ID,
			'ping_status' => 0, 'post_parent' => 0, 'menu_order' => $position,
			'guid' => $custom_linkurl, 'post_excerpt' => $custom_anchor_title, 'tax_input' => array( 'nav_menu' => $menu_title ),
			'post_content' => $custom_description, 'post_title' => $custom_title );
		if ( $new_window )
			$post['post_content_filtered'] = '_blank';
		else
			$post['post_content_filtered'] = '';
		if ( $parent_id > 0 && isset( $parent_menu_ids[$parent_id] ) )
			$post['post_parent'] = $parent_menu_ids[$parent_id];

		// New menu item
		if ( $db_id == 0 ) {
			$db_id = wp_insert_post( $post );
		} elseif ( isset( $menu_items[$db_id] ) ) {
			$post['ID'] = $db_id;
			wp_update_post( $post );
			unset( $menu_items[$db_id] );
		}
		$parent_menu_ids[ $k ] = $db_id;

		update_post_meta( $db_id, 'menu_type', $linktype );
		update_post_meta( $db_id, 'object_id', $object_id );
	}
	if ( !empty( $menu_items ) ) {
		foreach ( array_keys( $menu_items ) as $menu_id ) {
			wp_delete_post( $menu_id );
		}
	}
	$messages_div = '<div id="message" class="updated fade below-h2"><p>' . __('The menu has been updated.') . '</p></div>';
}

?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php esc_html_e('Menus') ?></h2>
	<form onsubmit="updatepostdata()" action="<?php echo admin_url( 'nav-menus.php' ); ?>" method="post" enctype="multipart/form-data">
<?php if ( ! empty( $custom_menus ) && count( $custom_menus ) > 1 ): ?>
		<ul class="subsubsub">
<?php
				foreach ( $custom_menus as $menu ) {
					$sep = end( $custom_menus ) == $menu ? '' : ' | ';
					if ( ( $menu_id_in_edit == $menu->term_id ) || ( $menu_selected_id == $menu->term_id ) ) { ?>
						<li><a href='nav-menus.php?edit-menu=<?php echo esc_attr($menu->term_id); ?>' class="current"><?php echo esc_html( $menu->name ); ?></a><?php echo $sep; ?></li>
<?php				} else { ?>
						<li><a href='nav-menus.php?edit-menu=<?php echo esc_attr($menu->term_id); ?>'><?php echo esc_html( $menu->name ); ?></a><?php echo $sep; ?></li>
<?php				}
				}
?>
		</ul>
		<div class="clear"></div>
<?php endif ?>

	<div class="hide-if-js error"><p><?php _e('You do not have JavaScript enabled in your browser. Please enable it to access the Menus functionality.'); ?></p></div>
	<div class="hide-if-no-js">
	<div id="pages-left">
		<div class="inside">
		<?php if ( ! empty( $custom_menus ) ) : ?>
		<?php echo $messages_div; ?>

		<input type="hidden" name="li-count" id="li-count" value="0" />
		<input type="hidden" name="menu-id-in-edit" id="menu-id-in-edit" value="<?php echo esc_attr( $menu_selected_id ); ?>" />

		<div class="sidebar-name">
			<div class="sidebar-name-arrow">
				<br/>
			</div>
			<h3><?php echo esc_html( $menu_title ); ?></h3>

		</div>

		<div id="nav-container">
			<ul id="custom-nav">

<?php
		if ( $menu_selected_id > 0 ) {
			wp_print_nav_menu( array( 'type' => 'backend', 'name' => $menu_title, 'id' => $menu_selected_id ) );
		}
?>
			</ul>
		</div><!-- /#nav-container -->

		<p class="submit">

		<script type="text/javascript">
			updatepostdata();
		</script>
		<input id="save_bottom" name="save_bottom" type="submit" value="<?php esc_attr_e('Save All Changes'); ?>" />
		<input id="delete-menu" name="delete-menu" type="submit" value="<?php esc_attr_e('Delete This Menu'); ?>" />
		</p>

	<?php else : ?>
		<div class="updated below-h2"><p><?php _e( 'Add a menu to start editing!' ); ?></p></div>
	<?php endif; ?>
		</div><!-- /.inside -->
	</div>

	<div id="menu-right">
		<div class="widgets-holder-wrap">
			<div class="sidebar-name">
				<div class="sidebar-name-arrow"></div>
				<h3><?php esc_html_e('Add Menu'); ?></h3>
			</div>
			<div class="widget-holder">

				<span>
				<input id="add-menu-name" name="add-menu-name" type="text" value=""  />
				<input id="add-menu" type="submit" value="<?php esc_attr_e('Add Menu'); ?>" name="add-menu" class="button" />
				</span>
			</div>
		</div><!-- /.widgets-holder-wrap -->
		<div class="widgets-holder-wrap">
			<div class="sidebar-name">
				<div class="sidebar-name-arrow"></div>
				<h3><?php esc_html_e('Add an Existing Page'); ?></h3>
			</div>
			<div class="widget-holder">
<?php
	$pages_args = array(
		'child_of' => 0,
		'sort_order' => 'ASC',
		'sort_column' => 'post_title',
		'hierarchical' => 1,
		'exclude' => '',
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'parent' => -1,
		'exclude_tree' => '',
		'number' => '',
		'offset' => 0
	);
	$page_name = '';
	$pages_array = get_pages($pages_args);
	if ( $pages_array ) {
		foreach ( $pages_array as $post ) {
			$page_name .= $post->post_title . '|';
		}
	} else {
		$page_name = __('No pages available');
	}
?>
				<script type="text/javascript" charset="<?php bloginfo('charset'); ?>">
					jQuery(document).ready(function(){
						var posts = "<?php echo esc_js( $page_name ); ?>".split("|");
						jQuery("#page-search").autocomplete(posts);
						jQuery("#page-search").result(function(event, data, formatted) {
							jQuery('#existing-pages').css('display','block');
							jQuery("#existing-pages dt:contains('" + data + "')").css("display", "block");
							jQuery('#show-pages').hide();
							jQuery('#hide-pages').show();
						});
						jQuery('#existing-pages').css('display','none');
					});
				</script>
				<input type="text" onfocus="jQuery('#page-search').attr('value','');" id="page-search" value="<?php esc_attr_e('Search Pages'); ?>" />

				<a id="show-pages" style="cursor:pointer;" onclick="jQuery('#existing-pages').css('display','block');jQuery('#page-search').attr('value','');jQuery('#existing-pages dt').css('display','block');jQuery('#show-pages').hide();jQuery('#hide-pages').show();"><?php _e('View All'); ?></a>
				<a id="hide-pages" style="cursor:pointer;" onclick="jQuery('#existing-pages').css('display','none');jQuery('#page-search').attr('value','Search Pages');jQuery('#existing-pages dt').css('display','none');jQuery('#show-pages').show();jQuery('#hide-pages').hide();"><?php _e('Hide All'); ?></a>

				<script type="text/javascript">
					jQuery('#hide-pages').hide();
				</script>

				<ul id="existing-pages" class="list">
<?php
	$items_counter = wp_nav_menu_get_pages( 0,'default' );
?>
				</ul>
				<div class="fix"></div>
			</div>
		</div><!-- /.widgets-holder-wrap -->

		<div class="widgets-holder-wrap">
			<div class="sidebar-name">
				<div class="sidebar-name-arrow"></div>
				<h3><?php esc_html_e('Add an Existing Category'); ?></h3>
			</div>
			<div class="widget-holder">

<?php
	// Custom GET categories query
	// @todo Use API
	$categories = $wpdb->get_results("SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' ORDER BY term_id ASC");
	$cat_name = '';
	if ( $categories ) {
		foreach ( $categories as $category ) {
			$cat_id = $category->term_id;
			$cat_args = array(
				'orderby' => 'name',
				'include' => $cat_id,
				'hierarchical' => 1,
				'order' => 'ASC',
			);
			$category_names = get_categories( $cat_args );
			if ( isset( $category_names[0]->name ) ) {
				$cat_name .= htmlentities( $category_names[0]->name ).'|';
			}
		}
	} else {
		$cat_name = __('No categories available');
	}
?>
				<script type="text/javascript" charset="<?php bloginfo('charset'); ?>">
					jQuery(document).ready(function(){
						var categories = "<?php echo esc_js($cat_name); ?>".split("|");
						jQuery("#cat-search").autocomplete(categories);
						jQuery("#cat-search").result(function(event, data, formatted) {
							jQuery('#existing-categories').css('display','block');
							jQuery("#existing-categories dt:contains('" + data + "')").css("display", "block");
							jQuery('#show-cats').hide();
							jQuery('#hide-cats').show();
						});
						jQuery('#existing-categories').css('display','none');
					});
				</script>

				<input type="text" onfocus="jQuery('#cat-search').attr('value','');" id="cat-search" value="<?php esc_attr_e('Search Categories'); ?>" />

				<a id="show-cats" style="cursor:pointer;" onclick="jQuery('#existing-categories').css('display','block');jQuery('#cat-search').attr('value','');jQuery('#existing-categories dt').css('display','block');jQuery('#show-cats').hide();jQuery('#hide-cats').show();"><?php _e('View All'); ?></a>
				<a id="hide-cats" style="cursor:pointer;" onclick="jQuery('#existing-categories').css('display','none');jQuery('#cat-search').attr('value','Search Categories');jQuery('#existing-categories dt').css('display','none');jQuery('#show-cats').show();jQuery('#hide-cats').hide();"><?php _e('Hide All'); ?></a>

				<script type="text/javascript">
					jQuery('#hide-cats').hide();
				</script>

				<ul id="existing-categories" class="list">
<?php
	$items_counter = wp_nav_menu_get_categories( $items_counter, 'default' );
?>
				</ul>

				<div class="fix"></div>
			</div>
		</div><!-- /.widgets-holder-wrap -->

		<div class="widgets-holder-wrap">
			<div class="sidebar-name">
				<div class="sidebar-name-arrow"></div>
				<h3><?php esc_html_e('Add a Custom Url'); ?></h3>
			</div>
			<div class="widget-holder">
				<input id="custom-menu-item-url" type="text" value="http://"  />
				<label for="custom-menu-item-url"><?php _e('URL'); ?></label><br />
				<?php $template_dir = get_bloginfo('url'); ?>
				<input type="hidden" id="template-dir" value="<?php echo esc_attr($template_dir); ?>" />
				<input id="custom-menu-item-name" type="text" value="<?php echo esc_attr( __('Menu Item') ); ?>" onfocus="jQuery('#custom-menu-item-name').attr('value','');"  />
				<label for="custom-menu-item-name"><?php _e('Menu Text'); ?></label><br />
				<input id="custom_menu_item_description" type="text" value="<?php esc_attr_e('A description'); ?>" <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?> onfocus="jQuery('#custom_menu_item_description').attr('value','');" />
				<label <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?> ><?php _e('Description'); ?></label>
				<a class="addtomenu" onclick="appendToList('<?php echo $template_dir; ?>','<?php echo esc_js( _x('Custom', 'menu nav item type') ); ?>','','','','0','');jQuery('#custom-menu-item-name').attr('value','<?php echo esc_js( __('Menu Item') ); ?>');jQuery('#custom_menu_item_description').attr('value','<?php echo esc_js( __('A description') ); ?>');"><?php _e('Add to menu'); ?></a>
				<div class="fix"></div>
			</div>
		</div><!-- /.widgets-holder-wrap -->
	</div><!-- /.hide-if-no-js -->
	</div>
	</form>
</div>

<div id="dialog-confirm" style="display:none;" title="<?php esc_attr_e('Edit Menu Item'); ?>">
	<input id="edittitle" type="text" name="edittitle" value="" /><label class="editlabel" for="edittitle"><?php _e('Menu Title'); ?></label><br />
	<input id="editlink" type="text" name="editlink" value="" /><label class="editlabel" for="editlink"><?php _e('URL'); ?></label><br />
	<input id="editanchortitle" type="text" name="editanchortitle" value="" /><label class="editlabel" for="editanchortitle"><?php _e('Link Title'); ?></label><br />
	<select id="editnewwindow" name="editnewwindow">
		<option value="1"><?php _e('Yes'); ?></option>
		<option value="0"><?php _e('No'); ?></option>
	</select><label class="editlabel" for="editnewwindow"><?php _e('Open Link in a new window'); ?></label>
	<input id="editdescription" type="text" name="editdescription" value="" <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?> /><label class="editlabel" for="editdescription" <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?> ><?php _e('Description'); ?></label><br />
</div>

<?php

include("admin-footer.php");