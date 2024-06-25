<?php
/**
 * Edit links form for inclusion in administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! empty( $link_id ) ) {
	/* translators: %s: URL to Links screen. */
	$heading      = sprintf( __( '<a href="%s">Links</a> / Edit Link' ), 'link-manager.php' );
	$submit_text  = __( 'Update Link' );
	$form_name    = 'editlink';
	$nonce_action = 'update-bookmark_' . $link_id;
} else {
	/* translators: %s: URL to Links screen. */
	$heading      = sprintf( __( '<a href="%s">Links</a> / Add New Link' ), 'link-manager.php' );
	$submit_text  = __( 'Add Link' );
	$form_name    = 'addlink';
	$nonce_action = 'add-bookmark';
}

require_once ABSPATH . 'wp-admin/includes/meta-boxes.php';

add_meta_box( 'linksubmitdiv', __( 'Save' ), 'link_submit_meta_box', null, 'side', 'core' );
add_meta_box( 'linkcategorydiv', __( 'Categories' ), 'link_categories_meta_box', null, 'normal', 'core' );
add_meta_box( 'linktargetdiv', __( 'Target' ), 'link_target_meta_box', null, 'normal', 'core' );
add_meta_box( 'linkxfndiv', __( 'Link Relationship (XFN)' ), 'link_xfn_meta_box', null, 'normal', 'core' );
add_meta_box( 'linkadvanceddiv', __( 'Advanced' ), 'link_advanced_meta_box', null, 'normal', 'core' );

/** This action is documented in wp-admin/includes/meta-boxes.php */
do_action( 'add_meta_boxes', 'link', $link );

/**
 * Fires when link-specific meta boxes are added.
 *
 * @since 3.0.0
 *
 * @param object $link Link object.
 */
do_action( 'add_meta_boxes_link', $link );

/** This action is documented in wp-admin/includes/meta-boxes.php */
do_action( 'do_meta_boxes', 'link', 'normal', $link );
/** This action is documented in wp-admin/includes/meta-boxes.php */
do_action( 'do_meta_boxes', 'link', 'advanced', $link );
/** This action is documented in wp-admin/includes/meta-boxes.php */
do_action( 'do_meta_boxes', 'link', 'side', $link );

add_screen_option(
	'layout_columns',
	array(
		'max'     => 2,
		'default' => 2,
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
		'<p>' . __( 'You can add or edit links on this screen by entering information in each of the boxes. Only the link&#8217;s web address and name (the text you want to display on your site as the link) are required fields.' ) . '</p>' .
		'<p>' . __( 'The boxes for link name, web address, and description have fixed positions, while the others may be repositioned using drag and drop. You can also hide boxes you do not use in the Screen Options tab, or minimize boxes by clicking on the title bar of the box.' ) . '</p>' .
		'<p>' . __( 'XFN stands for <a href="https://gmpg.org/xfn/">XHTML Friends Network</a>, which is optional. WordPress allows the generation of XFN attributes to show how you are related to the authors/owners of the site to which you are linking.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.wordpress.org/Links_Add_New_Screen">Documentation on Creating Links</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/forums/">Support forums</a>' ) . '</p>'
);

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="wrap">
<h1 class="wp-heading-inline">
<?php
echo esc_html( $title );
?>
</h1>

<a href="link-add.php" class="page-title-action"><?php echo esc_html__( 'Add New Link' ); ?></a>

<hr class="wp-header-end">

<?php
if ( isset( $_GET['added'] ) ) {
	wp_admin_notice(
		__( 'Link added.' ),
		array(
			'id'                 => 'message',
			'additional_classes' => array( 'updated' ),
			'dismissible'        => true,
		)
	);
}
?>

<form name="<?php echo esc_attr( $form_name ); ?>" id="<?php echo esc_attr( $form_name ); ?>" method="post" action="link.php">
<?php
if ( ! empty( $link_added ) ) {
	echo $link_added;
}

wp_nonce_field( $nonce_action );
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
?>

<div id="poststuff">

<div id="post-body" class="metabox-holder columns-<?php echo ( 1 === get_current_screen()->get_columns() ) ? '1' : '2'; ?>">
<div id="post-body-content">
<div id="namediv" class="postbox">
<h2 class="postbox-header"><label for="link_name"><?php _ex( 'Name', 'link name' ); ?></label></h2>
<div class="inside">
	<input type="text" name="link_name" size="30" maxlength="255" value="<?php echo esc_attr( $link->link_name ); ?>" id="link_name" />
	<p><?php _e( 'Example: Nifty blogging software' ); ?></p>
</div>
</div>

<div id="addressdiv" class="postbox">
<h2 class="postbox-header"><label for="link_url"><?php _e( 'Web Address' ); ?></label></h2>
<div class="inside">
	<input type="text" name="link_url" size="30" maxlength="255" class="code" value="<?php echo esc_url( $link->link_url ); ?>" id="link_url" />
	<p><?php _e( 'Example: <code>https://wordpress.org/</code> &#8212; do not forget the <code>https://</code>' ); ?></p>
</div>
</div>

<div id="descriptiondiv" class="postbox">
<h2 class="postbox-header"><label for="link_description"><?php _e( 'Description' ); ?></label></h2>
<div class="inside">
	<input type="text" name="link_description" size="30" maxlength="255" value="<?php echo isset( $link->link_description ) ? esc_attr( $link->link_description ) : ''; ?>" id="link_description" />
	<p><?php _e( 'This will be shown when someone hovers over the link in the blogroll, or optionally below the link.' ); ?></p>
</div>
</div>
</div><!-- /post-body-content -->

<div id="postbox-container-1" class="postbox-container">
<?php

/** This action is documented in wp-admin/includes/meta-boxes.php */
do_action( 'submitlink_box' );
$side_meta_boxes = do_meta_boxes( 'link', 'side', $link );

?>
</div>
<div id="postbox-container-2" class="postbox-container">
<?php

do_meta_boxes( null, 'normal', $link );

do_meta_boxes( null, 'advanced', $link );

?>
</div>
<?php

if ( $link_id ) :
	?>
<input type="hidden" name="action" value="save" />
<input type="hidden" name="link_id" value="<?php echo (int) $link_id; ?>" />
<input type="hidden" name="cat_id" value="<?php echo (int) $cat_id; ?>" />
<?php else : ?>
<input type="hidden" name="action" value="add" />
<?php endif; ?>

</div>
</div>

</form>
</div>
