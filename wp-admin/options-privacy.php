<?php
/**
 * Privacy Settings Screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'manage_privacy_options' ) ) {
	wp_die( __( 'Sorry, you are not allowed to manage privacy options on this site.' ) );
}

if ( isset( $_GET['tab'] ) && 'policyguide' === $_GET['tab'] ) {
	require_once dirname( __FILE__ ) . '/privacy-policy-guide.php';
	return;
}

add_filter(
	'admin_body_class',
	static function( $body_class ) {
		$body_class .= ' privacy-settings ';

		return $body_class;
	}
);

$action = isset( $_POST['action'] ) ? $_POST['action'] : '';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
				'<p>' . __( 'The Privacy screen lets you either build a new privacy-policy page or choose one you already have to show.' ) . '</p>' .
				'<p>' . __( 'This screen includes suggestions to help you write your own privacy policy. However, it is your responsibility to use these resources correctly, to provide the information required by your privacy policy, and to keep this information current and accurate.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/settings-privacy-screen/">Documentation on Privacy Settings</a>' ) . '</p>'
);

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
					/* translators: %s: URL to Customizer -> Menus. */
					__( 'Privacy Policy page setting updated successfully. Remember to <a href="%s">update your menus</a>!' ),
					esc_url( add_query_arg( 'autofocus[panel]', 'nav_menus', admin_url( 'customize.php' ) ) )
				);
			}
		}

		add_settings_error( 'page_for_privacy_policy', 'page_for_privacy_policy', $privacy_page_updated_message, 'success' );
	} elseif ( 'create-privacy-page' === $action ) {

		if ( ! class_exists( 'WP_Privacy_Policy_Content' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-privacy-policy-content.php';
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
					/* translators: %s: URL to Pages Trash. */
					__( 'The currently selected Privacy Policy page is in the Trash. Please create or select a new Privacy Policy page or <a href="%s">restore the current page</a>.' ),
					'edit.php?post_status=trash&post_type=page'
				),
				'error'
			);
		} else {
			$privacy_policy_page_exists = true;
		}
	}
}

$parent_file = 'options-general.php';

wp_enqueue_script( 'privacy-tools' );

require_once ABSPATH . 'wp-admin/admin-header.php';

?>
<div class="privacy-settings-header">
	<div class="privacy-settings-title-section">
		<h1>
			<?php _e( 'Privacy' ); ?>
		</h1>
	</div>

	<nav class="privacy-settings-tabs-wrapper hide-if-no-js" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'options-privacy.php' ) ); ?>" class="privacy-settings-tab active" aria-current="true">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( 'Settings', 'Privacy Settings' );
			?>
		</a>

		<a href="<?php echo esc_url( admin_url( 'options-privacy.php?tab=policyguide' ) ); ?>" class="privacy-settings-tab">
			<?php
			/* translators: Tab heading for Site Health Status page. */
			_ex( 'Policy Guide', 'Privacy Settings' );
			?>
		</a>
	</nav>
</div>

<hr class="wp-header-end">

<div class="notice notice-error hide-if-js">
	<p><?php _e( 'The Privacy Settings require JavaScript.' ); ?></p>
</div>

<div class="privacy-settings-body hide-if-no-js">
	<h2><?php _e( 'Privacy Settings' ); ?></h2>
	<p>
		<?php _e( 'As a website owner, you may need to follow national or international privacy laws. For example, you may need to create and display a Privacy Policy.' ); ?>
		<?php _e( 'If you already have a Privacy Policy page, please select it below. If not, please create one.' ); ?>
	</p>
	<p>
		<?php _e( 'The new page will include help and suggestions for your Privacy Policy.' ); ?>
		<?php _e( 'However, it is your responsibility to use those resources correctly, to provide the information that your Privacy Policy requires, and to keep that information current and accurate.' ); ?>
	</p>
	<p>
		<?php _e( 'After your Privacy Policy page is set, you should edit it.' ); ?>
		<?php _e( 'You should also review your Privacy Policy from time to time, especially after installing or updating any themes or plugins. There may be changes or new suggested information for you to consider adding to your policy.' ); ?>
	</p>
	<p>
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
				<strong>
				<?php
				if ( 'publish' === get_post_status( $privacy_policy_page_id ) ) {
					printf(
						/* translators: 1: URL to edit Privacy Policy page, 2: URL to view Privacy Policy page. */
						__( '<a href="%1$s">Edit</a> or <a href="%2$s">view</a> your Privacy Policy page content.' ),
						esc_url( $edit_href ),
						esc_url( $view_href )
					);
				} else {
					printf(
						/* translators: 1: URL to edit Privacy Policy page, 2: URL to preview Privacy Policy page. */
						__( '<a href="%1$s">Edit</a> or <a href="%2$s">preview</a> your Privacy Policy page content.' ),
						esc_url( $edit_href ),
						esc_url( $view_href )
					);
				}
				?>
				</strong>
			<?php
		}
		printf(
			/* translators: 1: Privacy Policy guide URL, 2: Additional link attributes, 3: Accessibility text. */
			__( 'Need help putting together your new Privacy Policy page? <a href="%1$s" %2$s>Check out our Privacy Policy guide%3$s</a> for recommendations on what content to include, along with policies suggested by your plugins and theme.' ),
			esc_url( admin_url( 'options-privacy.php?tab=policyguide' ) ),
			'',
			''
		);
		?>
	</p>
	<hr>
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
	?>
	<table class="form-table tools-privacy-policy-page" role="presentation">
		<tr>
			<th scope="row">
				<label for="create-page">
				<?php
				if ( $has_pages ) {
					_e( 'Create a new Privacy Policy Page' );
				} else {
					_e( 'There are no pages.' );
				}
				?>
				</label>
			</th>
			<td>
				<form class="wp-create-privacy-page" method="post" action="">
					<input type="hidden" name="action" value="create-privacy-page" />
					<?php
					wp_nonce_field( 'create-privacy-page' );
					submit_button( __( 'Create' ), 'secondary', 'submit', false, array( 'id' => 'create-page' ) );
					?>
				</form>
			</td>
		</tr>
		<?php if ( $has_pages ) : ?>
		<tr>
			<th scope="row">
				<label for="page_for_privacy_policy">
					<?php
					if ( $privacy_policy_page_exists ) {
						_e( 'Change your Privacy Policy page' );
					} else {
						_e( 'Select a Privacy Policy page' );
					}
					?>
				</label>
			</th>
			<td>
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

					submit_button( __( 'Use This Page' ), 'primary', 'submit', false, array( 'id' => 'set-page' ) );
					?>
				</form>
			</td>
		</tr>
		<?php endif; ?>
	</table>
</div>
<?php

require_once ABSPATH . 'wp-admin/admin-footer.php';
