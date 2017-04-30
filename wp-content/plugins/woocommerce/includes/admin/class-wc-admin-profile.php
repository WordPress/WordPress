<?php
/**
 * Add extra profile fields for users in admin.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Profile' ) ) :

/**
 * WC_Admin_Profile Class
 */
class WC_Admin_Profile {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'add_customer_meta_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_customer_meta_fields' ) );

		add_action( 'personal_options_update', array( $this, 'save_customer_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_customer_meta_fields' ) );

		add_action( 'show_user_profile', array( $this, 'add_api_key_field' ) );
		add_action( 'edit_user_profile', array( $this, 'add_api_key_field' ) );

		add_action( 'personal_options_update', array( $this, 'generate_api_key' ) );
		add_action( 'edit_user_profile_update', array( $this, 'generate_api_key' ) );
	}

	/**
	 * Get Address Fields for the edit user pages.
	 *
	 * @return array Fields to display which are filtered through woocommerce_customer_meta_fields before being returned
	 */
	public function get_customer_meta_fields() {
		$show_fields = apply_filters('woocommerce_customer_meta_fields', array(
			'billing' => array(
				'title' => __( 'Customer Billing Address', 'woocommerce' ),
				'fields' => array(
					'billing_first_name' => array(
							'label' => __( 'First name', 'woocommerce' ),
							'description' => ''
						),
					'billing_last_name' => array(
							'label' => __( 'Last name', 'woocommerce' ),
							'description' => ''
						),
					'billing_company' => array(
							'label' => __( 'Company', 'woocommerce' ),
							'description' => ''
						),
					'billing_address_1' => array(
							'label' => __( 'Address 1', 'woocommerce' ),
							'description' => ''
						),
					'billing_address_2' => array(
							'label' => __( 'Address 2', 'woocommerce' ),
							'description' => ''
						),
					'billing_city' => array(
							'label' => __( 'City', 'woocommerce' ),
							'description' => ''
						),
					'billing_postcode' => array(
							'label' => __( 'Postcode', 'woocommerce' ),
							'description' => ''
						),
					'billing_state' => array(
							'label' => __( 'State/County', 'woocommerce' ),
							'description' => __( 'State/County or state code', 'woocommerce' ),
						),
					'billing_country' => array(
							'label' => __( 'Country', 'woocommerce' ),
							'description' => __( '2 letter Country code', 'woocommerce' ),
						),
					'billing_phone' => array(
							'label' => __( 'Telephone', 'woocommerce' ),
							'description' => ''
						),
					'billing_email' => array(
							'label' => __( 'Email', 'woocommerce' ),
							'description' => ''
						)
				)
			),
			'shipping' => array(
				'title' => __( 'Customer Shipping Address', 'woocommerce' ),
				'fields' => array(
					'shipping_first_name' => array(
							'label' => __( 'First name', 'woocommerce' ),
							'description' => ''
						),
					'shipping_last_name' => array(
							'label' => __( 'Last name', 'woocommerce' ),
							'description' => ''
						),
					'shipping_company' => array(
							'label' => __( 'Company', 'woocommerce' ),
							'description' => ''
						),
					'shipping_address_1' => array(
							'label' => __( 'Address 1', 'woocommerce' ),
							'description' => ''
						),
					'shipping_address_2' => array(
							'label' => __( 'Address 2', 'woocommerce' ),
							'description' => ''
						),
					'shipping_city' => array(
							'label' => __( 'City', 'woocommerce' ),
							'description' => ''
						),
					'shipping_postcode' => array(
							'label' => __( 'Postcode', 'woocommerce' ),
							'description' => ''
						),
					'shipping_state' => array(
							'label' => __( 'State/County', 'woocommerce' ),
							'description' => __( 'State/County or state code', 'woocommerce' )
						),
					'shipping_country' => array(
							'label' => __( 'Country', 'woocommerce' ),
							'description' => __( '2 letter Country code', 'woocommerce' )
						)
				)
			)
		));
		return $show_fields;
	}

	/**
	 * Show Address Fields on edit user pages.
	 *
	 * @param mixed $user User (object) being displayed
	 */
	public function add_customer_meta_fields( $user ) {
		if ( ! current_user_can( 'manage_woocommerce' ) )
			return;

		$show_fields = $this->get_customer_meta_fields();

		foreach( $show_fields as $fieldset ) :
			?>
			<h3><?php echo $fieldset['title']; ?></h3>
			<table class="form-table">
				<?php
				foreach( $fieldset['fields'] as $key => $field ) :
					?>
					<tr>
						<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<input type="text" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>" class="regular-text" /><br/>
							<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
						</td>
					</tr>
					<?php
				endforeach;
				?>
			</table>
			<?php
		endforeach;
	}

	/**
	 * Save Address Fields on edit user pages
	 *
	 * @param mixed $user_id User ID of the user being saved
	 */
	public function save_customer_meta_fields( $user_id ) {
	 	$save_fields = $this->get_customer_meta_fields();

	 	foreach( $save_fields as $fieldset )
	 		foreach( $fieldset['fields'] as $key => $field )
	 			if ( isset( $_POST[ $key ] ) )
	 				update_user_meta( $user_id, $key, wc_clean( $_POST[ $key ] ) );
	}

	/**
	 * Display the API key info for a user
	 *
	 * @since 2.1
	 * @param WP_User $user
	 */
	public function add_api_key_field( $user ) {

		if ( ! current_user_can( 'manage_woocommerce' ) )
			return;

		$permissions = array(
			'read'       => __( 'Read', 'woocommerce' ),
			'write'      => __( 'Write', 'woocommerce' ),
			'read_write' => __( 'Read/Write', 'woocommerce' ),
		);

		if ( current_user_can( 'edit_user', $user->ID ) ) {
			?>
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="woocommerce_api_keys"><?php _e( 'WooCommerce API Keys', 'woocommerce' ); ?></label></th>
							<td>
								<?php if ( empty( $user->woocommerce_api_consumer_key ) ) : ?>
									<input name="woocommerce_generate_api_key" type="checkbox" id="woocommerce_generate_api_key" value="0" />
									<span class="description"><?php _e( 'Generate API Key', 'woocommerce' ); ?></span>
								<?php else : ?>
									<strong><?php _e( 'Consumer Key:', 'woocommerce' ); ?>&nbsp;</strong><code id="woocommerce_api_consumer_key"><?php echo $user->woocommerce_api_consumer_key ?></code><br/>
									<strong><?php _e( 'Consumer Secret:', 'woocommerce' ); ?>&nbsp;</strong><code id="woocommerce_api_consumer_secret"><?php echo $user->woocommerce_api_consumer_secret; ?></code><br/>
									<strong><?php _e( 'Permissions:', 'woocommerce' ); ?>&nbsp;</strong><span id="woocommerce_api_key_permissions"><select name="woocommerce_api_key_permissions" id="woocommerce_api_key_permissions"><?php
										foreach ( $permissions as $permission_key => $permission_name ) { echo '<option value="' . esc_attr( $permission_key ) . '" '.selected($permission_key, $user->woocommerce_api_key_permissions, false).'>'.esc_html( $permission_name ) . '</option>';} ?>
									</select></span><br/>
									<input name="woocommerce_generate_api_key" type="checkbox" id="woocommerce_generate_api_key" value="0" />
									<span class="description"><?php _e( 'Revoke API Key', 'woocommerce' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
			<?php
		}
	}

	/**
	 * Generate and save (or delete) the API keys for a user
	 *
	 * @since 2.1
	 * @param int $user_id
	 */
	public function generate_api_key( $user_id ) {

		if ( current_user_can( 'edit_user', $user_id ) ) {

			$user = get_userdata( $user_id );

			// creating/deleting key
			if ( isset( $_POST['woocommerce_generate_api_key'] ) ) {

				// consumer key
				if ( empty( $user->woocommerce_api_consumer_key ) ) {

					$consumer_key = 'ck_' . hash( 'md5', $user->user_login . date( 'U' ) . mt_rand() );

					update_user_meta( $user_id, 'woocommerce_api_consumer_key', $consumer_key );

				} else {

					delete_user_meta( $user_id, 'woocommerce_api_consumer_key' );
				}

				// consumer secret
				if ( empty( $user->woocommerce_api_consumer_secret ) ) {

					$consumer_secret = 'cs_' . hash( 'md5', $user->ID . date( 'U' ) . mt_rand() );

					update_user_meta( $user_id, 'woocommerce_api_consumer_secret', $consumer_secret );

				} else {

					delete_user_meta( $user_id, 'woocommerce_api_consumer_secret' );
				}

				// permissions
				if ( empty( $user->woocommerce_api_key_permissions ) ) {

					if ( isset( $_POST['woocommerce_api_key_permissions'] ) ) {

						$permissions = ( in_array( $_POST['woocommerce_api_key_permissions'], array( 'read', 'write', 'read_write' ) ) ) ? $_POST['woocommerce_api_key_permissions'] : 'read';

					} else {

						$permissions = 'read';
					}

					update_user_meta( $user_id, 'woocommerce_api_key_permissions', $permissions );

				} else {

					delete_user_meta( $user_id, 'woocommerce_api_key_permissions' );
				}

			} else {

				// updating permissions for key
				if ( ! empty( $_POST['woocommerce_api_key_permissions'] ) && $user->woocommerce_api_key_permissions !== $_POST['woocommerce_api_key_permissions'] ) {

					$permissions = ( ! in_array( $_POST['woocommerce_api_key_permissions'], array( 'read', 'write', 'read_write' ) ) ) ? 'read' : $_POST['woocommerce_api_key_permissions'];

					update_user_meta( $user_id, 'woocommerce_api_key_permissions', $permissions );
				}
			}
		}
	}

}

endif;

return new WC_Admin_Profile();
