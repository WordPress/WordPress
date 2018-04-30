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
			sprintf(
				// translators: URL to Customizer -> Menus
				__( 'Privacy policy page updated successfully. Remember to <a href="%s">update your menus</a>!' ),
				'customize.php?autofocus[panel]=nav_menus'
			),
			'updated'
		);
	} elseif ( 'create-privacy-page' === $action ) {
		if ( ! class_exists( 'WP_Privacy_Policy_Content' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/misc.php' );
		}

		$privacy_policy_page_content = WP_Privacy_Policy_Content::get_default_content();
		$privacy_policy_page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Privacy Policy' ),
				'post_status'  => 'draft',
				'post_type'    => 'page',
				'post_content' => $privacy_policy_page_content,
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
				sprintf(
					// translators: URL to edit Privacy Policy page
					__( 'Your Privacy Policy page created successfully. You&#8217;ll want to <a href="%s">review and edit your policy</a> next.' ),
					'post.php?post=' . $privacy_policy_page_id . '&action=edit'
				),
				'updated'
			);
		}
	}
}

// If a privacy policy page ID is available, make sure the page actually exists. If not, display an error.
$privacy_policy_page_exists = false;
$privacy_policy_page_id     = (int) get_option( 'wp_page_for_privacy_policy' );

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
					// translators: URL to Pages Trash.
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
	'content' => '<p>' . __( 'This page provides tools with which you can manage your user&#8217;s personal data and site&#8217;s privacy policy.' ) . '</p>',
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
	<h2><?php _e( 'Privacy Policy page' ); ?></h2>
	<p>
		<?php _e( 'The first step towards privacy laws compliance is to have a comprehensive Privacy Policy.' ); ?>
		<?php _e( 'If you already have a Privacy Policy page, please select it below. If not, create one.' ); ?>
		<?php _e( 'The new policy will include the suggested privacy text.' ); ?>
	</p>
	<p>
		<?php _e( 'After your Privacy Policy page is set, we suggest that you edit it.' ); ?>
		<?php _e( 'On the edit page screen you will find additional privacy information added by your plugins.' ); ?>
		<?php _e( 'We would also suggest reviewing your privacy policy from time to time, after a WordPress or a plugin update.' ); ?>
		<?php _e( 'There may be changes or new suggested information for you to consider adding to your policy.' ); ?>
	</p>
	<?php
	if ( $privacy_policy_page_exists ) {
		$edit_href = add_query_arg(
			array(
				'post'   => $privacy_policy_page_id,
				'action' => 'edit',
			),
			admin_url( 'post.php' )
		);

		$view_href = get_permalink( $privacy_policy_page_id );
		?>
		<p class="tools-privacy-edit"><strong>
			<?php
			printf(
				// translators: %1$s URL to Edit Page, %2$s URL to View Page
				__( '<a href="%1$s">Edit</a> or <a href="%2$s">view</a> your privacy policy page content.' ),
				$edit_href,
				$view_href
			);
			?>
		</strong></p>
		<?php
	}
	?>
	<hr>
	<table class="form-table tools-privacy-policy-page">
		<tr>
			<th scope="row">
				<?php
				if ( $privacy_policy_page_exists ) {
					_e( 'Change your Privacy Policy page' );
				} else {
					_e( 'Select a Privacy Policy page' );
				}
				?>
			</th>
			<td>
				<form method="post" action="">
					<label for="page_for_privacy_policy">
						<?php _e( 'Either select an existing page:' ); ?>
					</label>
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

					submit_button( __( 'Use This Page' ), 'primary', 'submit', false, array( 'id' => 'set-page' ) );
					?>
				</form>

				<form method="post" action="">
					<input type="hidden" name="action" value="create-privacy-page" />
					<span>
						<?php _e( 'Or create a new page: ' ); ?>
					</span>
					<?php
					wp_nonce_field( 'create-privacy-page' );
	
					submit_button( __( 'Create New Page' ), 'primary', 'submit', false, array( 'id' => 'create-page' ) );
					?>
				</form>
			</td>
		</tr>
	</table>
</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );
