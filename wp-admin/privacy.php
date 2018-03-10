<?php
/**
 * Privacy Tools Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'Sorry, you are not allowed to manage privacy on this site.' ) );
}

// "Borrow" xfn.js for now so we don't have to create new files.
// wp_enqueue_script( 'xfn' );

$action = isset( $_POST['action'] ) ? $_POST['action'] : '';

if ( ! empty( $action ) ) {
	check_admin_referer( $action );

	if ( 'set-privacy-page' === $action ) {
		$privacy_policy_page_id = isset( $_POST['page_for_privacy_policy'] ) ? (int) $_POST['page_for_privacy_policy'] : 0;
		update_option( 'wp_page_for_privacy_policy', $privacy_policy_page_id );

		add_settings_error(
			'page_for_privacy_policy',
			'page_for_privacy_policy',
			__( 'Privacy policy page updated successfully.' ),
			'updated'
		);
	} elseif ( 'create-privacy-page' === $action ) {
		$privacy_policy_page_id = wp_insert_post(
			array(
				'post_title'  => __( 'Privacy Policy' ),
				'post_status' => 'draft',
				'post_type'   => 'page',
			),
			true
		);

		if ( is_wp_error( $privacy_policy_page_id ) ) {
			add_settings_error(
				'page_for_privacy_policy',
				'page_for_privacy_policy',
				__( 'Unable to create privacy policy page.' ),
				'error'
			);
		} else {
			update_option( 'wp_page_for_privacy_policy', $privacy_policy_page_id );
			add_settings_error(
				'page_for_privacy_policy',
				'page_for_privacy_policy',
				__( 'Privacy policy page created successfully.' ),
				'updated'
			);
		}
	}
}

// If a privacy policy page ID is available, make sure the page actually exists. If not, display an error.
$privacy_policy_page_exists = false;
$privacy_policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );

if ( ! empty( $privacy_policy_page_id ) ) {
		$privacy_policy_page = get_post( $privacy_policy_page_id );
		if ( ! $privacy_policy_page instanceof WP_Post ) {
			add_settings_error(
				'page_for_privacy_policy',
				'page_for_privacy_policy',
				__( 'The currently selected privacy policy page does not exist. Please create or select new page.' ),
				'error'
			);
		} else {
			if ( 'trash' === $privacy_policy_page->post_status ) {
				add_settings_error(
					'page_for_privacy_policy',
					'page_for_privacy_policy',
					sprintf(
						__( 'The currently selected privacy policy page is in the trash. Please create or select new privacy policy page or <a href="%s">restore the current page</a>.' ),
						'edit.php?post_status=trash&post_type=page'
					),
					'error'
				);
			} else {
				$privacy_policy_page_exists = true;
			}
		}
}

get_current_screen()->add_help_tab( array(
	'id'      => 'privacy',
	'title'   => __( 'Privacy' ),
	'content' => '<p>' . __( 'This page provides tools with which you can manage your user\'s personal data and site\'s privacy policy.' ) . '</p>',
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="#">Documentation on privacy</a>' ) . '</p>'
);

require_once( ABSPATH . 'wp-admin/admin-header.php' );

?>
<div class="wrap">
	<h1><?php _e( 'Privacy Tools' ); ?></h1>
	<?php settings_errors(); ?>

	<h2><?php _e( 'Privacy policy page' ); ?></h2>

	<?php
	if ( $privacy_policy_page_exists ) {
		$edit_href = add_query_arg(
			array(
				'post'  => $privacy_policy_page_id,
				'action' => 'edit',
			),
			admin_url( 'post.php' )
		);
		$view_href = get_permalink( $privacy_policy_page_id );

		?>
		<p><strong>
			<?php
			printf(
				__( '<a href="%1$s">Edit</a> or <a href="%2$s">view</a> your privacy policy.' ),
				$edit_href,
				$view_href
			);
			?>
		</strong></p>
		<?php
	}
	?>

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="page_for_privacy_policy">
					<?php

					if ( $privacy_policy_page_exists ) {
						_e( 'Select another page for your privacy policy' );
					} else {
						_e( 'Select an existing privacy policy page' );
					}

					?>
				</label>
			</th>
			<td id="front-static-pages">
				<form method="post" action="">
					<input type="hidden" name="action" value="set-privacy-page" />
					<?php

					wp_dropdown_pages(
						array(
							'name'              => 'page_for_privacy_policy',
							'show_option_none'  => __( '&mdash; Select &mdash;' ),
							'option_none_value' => '0',
							'selected'          => $privacy_policy_page_id,
							'post_status'       => array( 'draft', 'publish' ),
						)
					);

					wp_nonce_field( 'set-privacy-page' );
					submit_button( __( 'Set Page' ), 'primary', 'submit', true, array( 'id' => 'set-page' ) );

					?>
				</form>
			</td>
		</tr>
		<?php

		if ( ! $privacy_policy_page_exists ) {
			?>
			<tr>
				<th scope="row"><?php _e( 'Create new page for your privacy policy' ); ?></th>
				<td>
					<form method="post" action="">
						<input type="hidden" name="action" value="create-privacy-page" />
						<?php

						wp_nonce_field( 'create-privacy-page' );
						submit_button( __( 'Create Page' ), 'primary', 'submit', true, array( 'id' => 'create-page' ) );

						?>
					</form>
				</td>
			</tr>
			<?php
		}

		?>
	</table>
</div>

<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );
