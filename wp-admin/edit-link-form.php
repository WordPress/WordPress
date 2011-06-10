<?php
/**
 * Edit links form for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

if ( ! empty($link_id) ) {
	$heading = sprintf( __( '<a href="%s">Links</a> / Edit Link' ), 'link-manager.php' );
	$submit_text = __('Update Link');
	$form = '<form name="editlink" id="editlink" method="post" action="link.php">';
	$nonce_action = 'update-bookmark_' . $link_id;
} else {
	$heading = sprintf( __( '<a href="%s">Links</a> / Add New Link' ), 'link-manager.php' );
	$submit_text = __('Add Link');
	$form = '<form name="addlink" id="addlink" method="post" action="link.php">';
	$nonce_action = 'add-bookmark';
}

require_once('./includes/meta-boxes.php');

add_meta_box('linksubmitdiv', __('Save'), 'link_submit_meta_box', 'link', 'side', 'core');
add_meta_box('linkcategorydiv', __('Categories'), 'link_categories_meta_box', 'link', 'normal', 'core');
add_meta_box('linktargetdiv', __('Target'), 'link_target_meta_box', 'link', 'normal', 'core');
add_meta_box('linkxfndiv', __('Link Relationship (XFN)'), 'link_xfn_meta_box', 'link', 'normal', 'core');
add_meta_box('linkadvanceddiv', __('Advanced'), 'link_advanced_meta_box', 'link', 'normal', 'core');

do_action('add_meta_boxes', 'link', $link);
do_action('add_meta_boxes_link', $link);

do_action('do_meta_boxes', 'link', 'normal', $link);
do_action('do_meta_boxes', 'link', 'advanced', $link);
do_action('do_meta_boxes', 'link', 'side', $link);

add_screen_option('layout_columns', array('max' => 2) );

add_contextual_help($current_screen,
	'<p>' . __( 'You can add or edit links on this screen by entering information in each of the boxes. Only the link&#8217;s web address and name (the text you want to display on your site as the link) are required fields.' ) . '</p>' .
	'<p>' . __( 'The boxes for link name, web address, and description have fixed positions, while the others may be repositioned using drag and drop. You can also hide boxes you don&#8217;t use in the Screen Options tab, or minimize boxes by clicking on the title bar of the box.' ) . '</p>' .
	'<p>' . __( 'XFN stands for <a href="http://gmpg.org/xfn/" target="_blank">XHTML Friends Network</a>, which is optional. WordPress allows the generation of XFN attributes to show how you are related to the authors/owners of the site to which you are linking.' ) . '</p>' .
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="http://codex.wordpress.org/Links_Add_New_Screen" target="_blank">Documentation on Creating Links</a>' ) . '</p>' .
	'<p>' . __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>' ) . '</p>'
);

require_once ('admin-header.php');

?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?>  <a href="link-add.php" class="add-new-h2"><?php echo esc_html_x('Add New', 'link'); ?></a></h2>

<?php if ( isset( $_GET['added'] ) ) : ?>
<div id="message" class="updated"><p><?php _e('Link added.'); ?></p></div>
<?php endif; ?>

<?php
if ( !empty($form) )
	echo $form;
if ( !empty($link_added) )
	echo $link_added;

wp_nonce_field( $nonce_action );
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">

<div id="side-info-column" class="inner-sidebar">
<?php

do_action('submitlink_box');
$side_meta_boxes = do_meta_boxes( 'link', 'side', $link );

?>
</div>

<div id="post-body">
<div id="post-body-content">
<div id="namediv" class="stuffbox">
<h3><label for="link_name"><?php _ex('Name', 'link name') ?></label></h3>
<div class="inside">
	<input type="text" name="link_name" size="30" tabindex="1" value="<?php echo esc_attr($link->link_name); ?>" id="link_name" />
    <p><?php _e('Example: Nifty blogging software'); ?></p>
</div>
</div>

<div id="addressdiv" class="stuffbox">
<h3><label for="link_url"><?php _e('Web Address') ?></label></h3>
<div class="inside">
	<input type="text" name="link_url" size="30" class="code" tabindex="1" value="<?php echo esc_attr($link->link_url); ?>" id="link_url" />
    <p><?php _e('Example: <code>http://wordpress.org/</code> &#8212; don&#8217;t forget the <code>http://</code>'); ?></p>
</div>
</div>

<div id="descriptiondiv" class="stuffbox">
<h3><label for="link_description"><?php _e('Description') ?></label></h3>
<div class="inside">
	<input type="text" name="link_description" size="30" tabindex="1" value="<?php echo isset($link->link_description) ? esc_attr($link->link_description) : ''; ?>" id="link_description" />
    <p><?php _e('This will be shown when someone hovers over the link in the blogroll, or optionally below the link.'); ?></p>
</div>
</div>

<?php

do_meta_boxes('link', 'normal', $link);

do_meta_boxes('link', 'advanced', $link);

if ( $link_id ) : ?>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="link_id" value="<?php echo (int) $link_id; ?>" />
<input type="hidden" name="order_by" value="<?php echo esc_attr($order_by); ?>" />
<input type="hidden" name="cat_id" value="<?php echo (int) $cat_id ?>" />
<?php else: ?>
<input type="hidden" name="action" value="add" />
<?php endif; ?>

</div>
</div>
</div>

</form>
</div>
