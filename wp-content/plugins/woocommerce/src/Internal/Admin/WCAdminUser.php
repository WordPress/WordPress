<?php

namespace Automattic\WooCommerce\Internal\Admin;

/**
 * WCAdminUser Class.
 */
class WCAdminUser {

	/**
	 * Class instance.
	 *
	 * @var WCAdminUser instance
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_user_data' ) );
	}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Registers WooCommerce specific user data to the WordPress user API.
	 */
	public function register_user_data() {
		register_rest_field(
			'user',
			'is_super_admin',
			array(
				'get_callback' => function() {
					return is_super_admin();
				},
				'schema'       => null,
			)
		);
		register_rest_field(
			'user',
			'woocommerce_meta',
			array(
				'get_callback'    => array( $this, 'get_user_data_values' ),
				'update_callback' => array( $this, 'update_user_data_values' ),
				'schema'          => null,
			)
		);
	}

	/**
	 * For all the registered user data fields (  Loader::get_user_data_fields ), fetch the data
	 * for returning via the REST API.
	 *
	 * @param WP_User $user Current user.
	 */
	public function get_user_data_values( $user ) {
		$values = array();
		foreach ( $this->get_user_data_fields() as $field ) {
			$values[ $field ] = self::get_user_data_field( $user['id'], $field );
		}
		return $values;
	}

	/**
	 * For all the registered user data fields ( Loader::get_user_data_fields ), update the data
	 * for the REST API.
	 *
	 * @param array   $values   The new values for the meta.
	 * @param WP_User $user     The current user.
	 * @param string  $field_id The field id for the user meta.
	 */
	public function update_user_data_values( $values, $user, $field_id ) {
		if ( empty( $values ) || ! is_array( $values ) || 'woocommerce_meta' !== $field_id ) {
			return;
		}
		$fields  = $this->get_user_data_fields();
		$updates = array();
		foreach ( $values as $field => $value ) {
			if ( in_array( $field, $fields, true ) ) {
				$updates[ $field ] = $value;
				self::update_user_data_field( $user->ID, $field, $value );
			}
		}
		return $updates;
	}

	/**
	 * We store some WooCommerce specific user meta attached to users endpoint,
	 * so that we can track certain preferences or values such as the inbox activity panel last open time.
	 * Additional fields can be added in the function below, and then used via wc-admin's currentUser data.
	 *
	 * @return array Fields to expose over the WP user endpoint.
	 */
	public function get_user_data_fields() {
		/**
		 * Filter user data fields exposed over the WordPress user endpoint.
		 *
		 * @since 4.0.0
		 * @param array $fields Array of fields to expose over the WP user endpoint.
		 */
		return apply_filters( 'woocommerce_admin_get_user_data_fields', array( 'variable_product_tour_shown' ) );
	}

	/**
	 * Helper to update user data fields.
	 *
	 * @param int    $user_id  User ID.
	 * @param string $field Field name.
	 * @param mixed  $value  Field value.
	 */
	public static function update_user_data_field( $user_id, $field, $value ) {
		update_user_meta( $user_id, 'woocommerce_admin_' . $field, $value );
	}

	/**
	 * Helper to retrieve user data fields.
	 *
	 * Migrates old key prefixes as well.
	 *
	 * @param int    $user_id  User ID.
	 * @param string $field Field name.
	 * @return mixed The user field value.
	 */
	public static function get_user_data_field( $user_id, $field ) {
		$meta_value = get_user_meta( $user_id, 'woocommerce_admin_' . $field, true );

		// Migrate old meta values (prefix changed from `wc_admin_` to `woocommerce_admin_`).
		if ( '' === $meta_value ) {
			$old_meta_value = get_user_meta( $user_id, 'wc_admin_' . $field, true );

			if ( '' !== $old_meta_value ) {
				self::update_user_data_field( $user_id, $field, $old_meta_value );
				delete_user_meta( $user_id, 'wc_admin_' . $field );

				$meta_value = $old_meta_value;
			}
		}

		return $meta_value;
	}
}
