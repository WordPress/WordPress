<?php
/*
Plugin Name: Sharedaddy
Description: The most super duper sharing tool on the interwebs.
Version: 0.3.1
Author: Automattic, Inc.
Author URI: http://automattic.com/
Plugin URI: http://en.blog.wordpress.com/2010/08/24/more-ways-to-share/
*/

require_once plugin_dir_path( __FILE__ ).'sharing.php';

function sharing_email_send_post( $data ) {
	$content  = sprintf( __( '%1$s (%2$s) thinks you may be interested in the following post:', 'jetpack' ), $data['name'], $data['source'] );
	$content .= "\n\n";
	$content .= $data['post']->post_title."\n";
	$content .= get_permalink( $data['post']->ID )."\n";

	$headers[] = sprintf( 'From: %1$s <%2$s>', $data['name'], $data['source'] );

	wp_mail( $data['target'], '['.__( 'Shared Post', 'jetpack' ).'] '.$data['post']->post_title, $content, $headers );
}

function sharing_add_meta_box() {
	$post_types = get_post_types( array( 'public' => true ) );
	$title = apply_filters( 'sharing_meta_box_title', __( 'Sharing', 'jetpack' ) );
	foreach( $post_types as $post_type ) {
		add_meta_box( 'sharing_meta', $title, 'sharing_meta_box_content', $post_type, 'advanced', 'high' );
	}
}

function sharing_meta_box_content( $post ) {
	do_action( 'start_sharing_meta_box_content', $post );

	$disabled = get_post_meta( $post->ID, 'sharing_disabled', true ); ?>

	<p>
		<label for="enable_post_sharing">
			<input type="checkbox" name="enable_post_sharing" id="enable_post_sharing" value="1" <?php checked( !$disabled ); ?>>
			<?php _e( 'Show sharing buttons.' , 'jetpack'); ?>
		</label>
		<input type="hidden" name="sharing_status_hidden" value="1" />
	</p>

	<?php
	do_action( 'end_sharing_meta_box_content', $post );
}

function sharing_meta_box_save( $post_id ) {
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	// Record sharing disable
	if ( isset( $_POST['post_type'] ) && ( 'post' == $_POST['post_type'] || 'page' == $_POST['post_type'] ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['sharing_status_hidden'] ) ) {
				if ( !isset( $_POST['enable_post_sharing'] ) ) {
					update_post_meta( $post_id, 'sharing_disabled', 1 );
				} else {
					delete_post_meta( $post_id, 'sharing_disabled' );
				}
			}
		}
	}

  	return $post_id;
}

function sharing_meta_box_protected( $protected, $meta_key, $meta_type ) {
	if ( 'sharing_disabled' == $meta_key )
		$protected = true;

	return $protected;
}

add_filter( 'is_protected_meta', 'sharing_meta_box_protected', 10, 3 );

function sharing_plugin_settings( $links ) {
	$settings_link = '<a href="options-general.php?page=sharing.php">'.__( 'Settings', 'jetpack' ).'</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

function sharing_add_plugin_settings($links, $file) {
	if ( $file == basename( dirname( __FILE__ ) ).'/'.basename( __FILE__ ) ) {
		$links[] = '<a href="options-general.php?page=sharing.php">' . __( 'Settings', 'jetpack' ) . '</a>';
		$links[] = '<a href="http://support.wordpress.com/sharing/">' . __( 'Support', 'jetpack' ) . '</a>';
	}

	return $links;
}

function sharing_restrict_to_single( $services ) {
	// This removes Press This from non-multisite blogs - doesn't make much sense
	if ( is_multisite() === false ) {
		unset( $services['press-this'] );
	}

	return $services;
}

function sharing_init() {
	if ( get_option( 'sharedaddy_disable_resources' ) ) {
		add_filter( 'sharing_js', 'sharing_disable_js' );
		remove_action( 'wp_head', 'sharing_add_header', 1 );
	}
}

function sharing_disable_js() {
	return false;
}

function sharing_global_resources() {
	$disable = get_option( 'sharedaddy_disable_resources' );
?>
<tr valign="top">
	<th scope="row"><label for="disable_css"><?php _e( 'Disable CSS and JS', 'jetpack' ); ?></label></th>
	<td>
		<input id="disable_css" type="checkbox" name="disable_resources" <?php if ( $disable == 1 ) echo ' checked="checked"'; ?>/>  <small><em><?php _e( 'Advanced.  If this option is checked, you must include these files in your theme manually for the sharing links to work.', 'jetpack' ); ?></em></small>
	</td>
</tr>
<?php
}

function sharing_global_resources_save() {
	update_option( 'sharedaddy_disable_resources', isset( $_POST['disable_resources'] ) ? 1 : 0 );
}

function sharing_email_dialog() {
	echo '<div class="recaptcha" id="sharing_recaptcha"></div><input type="hidden" name="recaptcha_public_key" id="recaptcha_public_key" value="'.(defined( 'RECAPTCHA_PUBLIC_KEY' ) ? esc_attr( RECAPTCHA_PUBLIC_KEY ) : '').'" />';
}

function sharing_email_check( $true, $post, $data ) {
	require_once plugin_dir_path( __FILE__ ).'recaptchalib.php';

	$recaptcha_result = recaptcha_check_answer( RECAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $data["recaptcha_challenge_field"], $data["recaptcha_response_field"] );

	return $recaptcha_result->is_valid;
}

add_action( 'init', 'sharing_init' );
add_action( 'admin_init', 'sharing_add_meta_box' );
add_action( 'save_post', 'sharing_meta_box_save' );
add_action( 'sharing_email_send_post', 'sharing_email_send_post' );
add_action( 'sharing_global_options', 'sharing_global_resources', 30 );
add_action( 'sharing_admin_update', 'sharing_global_resources_save' );
add_filter( 'sharing_services', 'sharing_restrict_to_single' );
add_action( 'plugin_action_links_'.basename( dirname( __FILE__ ) ).'/'.basename( __FILE__ ), 'sharing_plugin_settings', 10, 4 );
add_filter( 'plugin_row_meta', 'sharing_add_plugin_settings', 10, 2 );

if ( defined( 'RECAPTCHA_PRIVATE_KEY' ) ) {
	add_action( 'sharing_email_dialog', 'sharing_email_dialog' );
	add_filter( 'sharing_email_check', 'sharing_email_check', 10, 3 );
}
