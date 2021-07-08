<?php
/**
 * Privacy tools, Export Personal Data screen.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'export_others_personal_data' ) ) {
	wp_die( __( 'Sorry, you are not allowed to export personal data on this site.' ) );
}

// Contextual help - choose Help on the top right of admin panel to preview this.
get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' =>
					'<p>' . __( 'This screen allows you to manage requests for an export of personal data.' ) . '</p>' .
					'<p>' . __( 'Providing an export of all data that a business or website has collected about an individual is a requirement of many Privacy Laws around the world, and is sometimes referred to as the "Right To Data Portability".' ) . '</p>' .
					'<p>' . __( 'The tool associates data stored in WordPress by a supplied email address, including profile data and comments.' ) . '</p>' .
					'<p><strong>' . __( 'Note: As this tool only gathers data from WordPress and participating plugins, you may need to do more to comply with export requests. You should also send the requestor any data collected by or stored with any 3rd party services used by your business or site.' ) . '</strong></p>',
	)
);

get_current_screen()->add_help_tab(
	array(
		'id'      => 'default-data',
		'title'   => __( 'Default Data' ),
		'content' =>
					'<p>' . __( 'Personal data that is collected by WordPress and is displayed in the export files include:' ) . '</p>' .
					'<p>' . __( '<strong>Profile Information</strong> &mdash; user email address, username, display name, nickname, first name, last name, description/bio, and registration date.' ) . '</p>' .
					'<p>' . __( '<strong>Community Events Location</strong> &mdash; The IP Address of the user used for the Upcoming Community Events shown in the dashboard widget.' ) . '</p>' .
					'<p>' . __( '<strong>Session Tokens</strong> &mdash; User login information, IP Addresses, Expiration Date, User Agent (Browser/OS), and Last Login.' ) . '</p>' .
					'<p>' . __( '<strong>Comments</strong> &mdash; For any comments made by the user, Email Address, IP Address, User Agent (Browser/OS), Date/Time, Comment Content, and Content URL.' ) . '</p>' .
					'<p>' . __( '<strong>Media</strong> &mdash; A list of URLs for all media file uploads made by the user.' ) . '</p>',
	)
);

$privacy_policy_guide = '<p>' . sprintf(
	/* translators: %s: URL to Privacy Policy Guide screen. */
	__( 'If you are not sure, check the plugin documentation or contact the plugin author to see if the plugin collects data and if it supports the Data Exporter tool. This information may be available in the <a href="%s">Privacy Policy Guide</a>.' ),
	admin_url( 'options-privacy.php?tab=policyguide' )
) . '</p>';

get_current_screen()->add_help_tab(
	array(
		'id'      => 'plugin-data',
		'title'   => __( 'Plugin Data' ),
		'content' =>
					'<p>' . __( 'Many plugins may collect or store personal data either in the WordPress database or remotely. Any Export Personal Data request should include data from plugins as well.' ) . '</p>' .
					'<p>' . __( 'Plugin authors can <a href="https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-exporter-to-your-plugin/" target="_blank">learn more about how to add the Personal Data Exporter to a plugin here</a>.' ) . '</p>' .
					$privacy_policy_guide,
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/tools-export-personal-data-screen/">Documentation on Export Personal Data</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

// Handle list table actions.
_wp_personal_data_handle_actions();

// Cleans up failed and expired requests before displaying the list table.
_wp_personal_data_cleanup_requests();

wp_enqueue_script( 'privacy-tools' );

add_screen_option(
	'per_page',
	array(
		'default' => 20,
		'option'  => 'export_personal_data_requests_per_page',
	)
);

$_list_table_args = array(
	'plural'   => 'privacy_requests',
	'singular' => 'privacy_request',
);

$requests_table = _get_list_table( 'WP_Privacy_Data_Export_Requests_List_Table', $_list_table_args );

$requests_table->screen->set_screen_reader_content(
	array(
		'heading_views'      => __( 'Filter export personal data list' ),
		'heading_pagination' => __( 'Export personal data list navigation' ),
		'heading_list'       => __( 'Export personal data list' ),
	)
);

$requests_table->process_bulk_action();
$requests_table->prepare_items();

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="wrap nosubsub">
	<h1><?php esc_html_e( 'Export Personal Data' ); ?></h1>
	<p><?php _e( 'This tool helps site owners comply with local laws and regulations by exporting known data for a given user in a .zip file.' ); ?></p>
	<hr class="wp-header-end" />

	<?php settings_errors(); ?>

	<form action="<?php echo esc_url( admin_url( 'export-personal-data.php' ) ); ?>" method="post" class="wp-privacy-request-form">
		<h2><?php esc_html_e( 'Add Data Export Request' ); ?></h2>
		<div class="wp-privacy-request-form-field">
		<table class="form-table">
				<tr>
					<th scope="row">
						<label for="username_or_email_for_privacy_request"><?php esc_html_e( 'Username or email address' ); ?></label>
					</th>
					<td>
						<input type="text" required class="regular-text ltr" id="username_or_email_for_privacy_request" name="username_or_email_for_privacy_request" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php _e( 'Confirmation email' ); ?>
					</th>
					<td>
						<label for="send_confirmation_email">
							<input type="checkbox" name="send_confirmation_email" id="send_confirmation_email" value="1" checked="checked" />
							<?php _e( 'Send personal data export confirmation email.' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<p class="submit">
				<?php submit_button( __( 'Send Request' ), 'secondary', 'submit', false ); ?>
			</p>
		</div>
		<?php wp_nonce_field( 'personal-data-request' ); ?>
		<input type="hidden" name="action" value="add_export_personal_data_request" />
		<input type="hidden" name="type_of_action" value="export_personal_data" />
	</form>
	<hr />

	<?php $requests_table->views(); ?>

	<form class="search-form wp-clearfix">
		<?php $requests_table->search_box( __( 'Search Requests' ), 'requests' ); ?>
		<input type="hidden" name="filter-status" value="<?php echo isset( $_REQUEST['filter-status'] ) ? esc_attr( sanitize_text_field( $_REQUEST['filter-status'] ) ) : ''; ?>" />
		<input type="hidden" name="orderby" value="<?php echo isset( $_REQUEST['orderby'] ) ? esc_attr( sanitize_text_field( $_REQUEST['orderby'] ) ) : ''; ?>" />
		<input type="hidden" name="order" value="<?php echo isset( $_REQUEST['order'] ) ? esc_attr( sanitize_text_field( $_REQUEST['order'] ) ) : ''; ?>" />
	</form>

	<form method="post">
		<?php
		$requests_table->display();
		$requests_table->embed_scripts();
		?>
	</form>
</div>

<?php
require_once ABSPATH . 'wp-admin/admin-footer.php';
