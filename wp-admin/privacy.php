<?php
/**
 * Privacy Settings Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! current_user_can( 'manage_privacy_options' ) ) {
	wp_die( __( 'Sorry, you are not allowed to manage privacy on this site.' ) );
}

$action = isset( $_POST['action'] ) ? $_POST['action'] : '';

if ( ! empty( $action ) ) {
	check_admin_referer( $action );

	if ( 'set-privacy-page' === $action ) {
		$privacy_policy_page_id = isset( $_POST['page_for_privacy_policy'] ) ? (int) $_POST['page_for_privacy_policy'] : 0;
		update_option( 'wp_page_for_privacy_policy', $privacy_policy_page_id );

		$privacy_page_updated_message = __( 'Privacy Policy page updated successfully.' );

		if ( $privacy_policy_page_id ) {
			/*
			 * Don't always link to the menu customizer:
			 *
			 * - Unpublished pages can't be selected by default.
			 * - `WP_Customize_Nav_Menus::__construct()` checks the user's capabilities.
			 * - Themes might not "officially" support menus.
			 */
			if (
				'publish' === get_post_status( $privacy_policy_page_id )
				&& current_user_can( 'edit_theme_options' )
				&& current_theme_supports( 'menus' )
			) {
				$privacy_page_updated_message = sprintf(
					/* translators: %s: URL to Customizer -> Menus */
					__( 'Privacy Policy page setting updated successfully. Remember to <a href="%s">update your menus</a>!' ),
					esc_url( add_query_arg( 'autofocus[panel]', 'nav_menus', admin_url( 'customize.php' ) ) )
				);
			}
		}

		add_settings_error(
			'page_for_privacy_policy',
			'page_for_privacy_policy',
			$privacy_page_updated_message,
			'updated'
		);
	} elseif ( 'create-privacy-page' === $action ) {

		if ( ! class_exists( 'WP_Privacy_Policy_Content' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/misc.php' );
		}

		$privacy_policy_page_content = WP_Privacy_Policy_Content::get_default_content();
		$privacy_policy_page_id      = wp_insert_post(
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
				__( 'Unable to create a Privacy Policy page.' ),
				'error'
			);
		} else {
			update_option( 'wp_page_for_privacy_policy', $privacy_policy_page_id );

			wp_redirect( admin_url( 'post.php?post=' . $privacy_policy_page_id . '&action=edit' ) );
			exit;
		}
	}
}

// If a Privacy Policy page ID is available, make sure the page actually exists. If not, display an error.
$privacy_policy_page_exists = false;
$privacy_policy_page_id     = (int) get_option( 'wp_page_for_privacy_policy' );

if ( ! empty( $privacy_policy_page_id ) ) {

	$privacy_policy_page = get_post( $privacy_policy_page_id );

	if ( ! $privacy_policy_page instanceof WP_Post ) {
		add_settings_error(
			'page_for_privacy_policy',
			'page_for_privacy_policy',
			__( 'The currently selected Privacy Policy page does not exist. Please create or select a new page.' ),
			'error'
		);
	} else {
		if ( 'trash' === $privacy_policy_page->post_status ) {
			add_settings_error(
				'page_for_privacy_policy',
				'page_for_privacy_policy',
				sprintf(
					/* translators: URL to Pages Trash */
					__( 'The currently selected Privacy Policy page is in the trash. Please create or select a new Privacy Policy page or <a href="%s">restore the current page</a>.' ),
					'edit.php?post_status=trash&post_type=page'
				),
				'error'
			);
		} else {
			$privacy_policy_page_exists = true;
		}
	}
}

$title       = __( 'Privacy Settings' );
$parent_file = 'options-general.php';

require_once( ABSPATH . 'wp-admin/admin-header.php' );

?>
<div class="wrap">
	<h1><?php echo $title; ?></h1>
	<h2><?php _e( 'Privacy Policy page' ); ?></h2>
	<p>
		<?php _e( 'As a website owner, you may need to follow national or international privacy laws. For example, you may need to create and display a Privacy Policy.' ); ?>
		<?php _e( 'If you already have a Privacy Policy page, please select it below. If not, please create one.' ); ?>
	</p>
	<p>
		<?php _e( 'The new page will include help and suggestions for your Privacy Policy.' ); ?>
		<?php _e( 'However, it is your responsibility to use those resources correctly, to provide the information that your Privacy Policy requires, and to keep that information current and accurate.' ); ?>
	</p>
	<p>
		<?php _e( 'After your Privacy Policy page is set, we suggest that you edit it.' ); ?>
		<?php _e( 'We would also suggest reviewing your Privacy Policy from time to time, especially after installing or updating any themes or plugins. There may be changes or new suggested information for you to consider adding to your policy.' ); ?>
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

			if ( 'publish' === get_post_status( $privacy_policy_page_id ) ) {
				/* translators: 1: URL to edit page, 2: URL to view page */
				printf( __( '<a href="%1$s">Edit</a> or <a href="%2$s">view</a> your Privacy Policy page content.' ), $edit_href, $view_href );
			} else {
				/* translators: 1: URL to edit page, 2: URL to preview page */
				printf( __( '<a href="%1$s">Edit</a> or <a href="%2$s">preview</a> your Privacy Policy page content.' ), $edit_href, $view_href );
			}

			?>
		</strong></p>
		<?php
	}
	?>
	<p>
		<?php

		printf(
			/* translators: 1: Privacy Policy guide URL, 2: additional link attributes, 3: accessibility text */
			__( 'Need help putting together your new Privacy Policy page? <a href="%1$s" %2$s>Check out our guide%3$s</a> for recommendations on what content to include, along with policies suggested by your plugins and theme.' ),
			admin_url( 'tools.php?wp-privacy-policy-guide' ),
			'',
			''
		);

		?>
	</p>

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
				<?php
				$has_pages = (bool) get_posts(
					array(
						'post_type'      => 'page',
						'posts_per_page' => 1,
						'post_status'    => array(
							'publish',
							'draft',
						),
					)
				);

				if ( $has_pages ) :
					?>
					<form method="post" action="">
						<label for="page_for_privacy_policy">
							<?php _e( 'Select an existing page:' ); ?>
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
				<?php endif; ?>

				<form class="wp-create-privacy-page" method="post" action="">
					<input type="hidden" name="action" value="create-privacy-page" />
					<span>
						<?php
						if ( $has_pages ) {
							_e( 'Or:' );
						} else {
							_e( 'There are no pages.' );
						}
						?>
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
