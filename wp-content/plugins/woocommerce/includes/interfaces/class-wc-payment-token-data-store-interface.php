<?php
/**
 * Payment Token Data Store Interface
 *
 * @version 3.0.0
 * @package WooCommerce\Interface
 */

/**
 * WC Payment Token Data Store Interface
 *
 * Functions that must be defined by payment token store classes.
 *
 * @version  3.0.0
 */
interface WC_Payment_Token_Data_Store_Interface {
	/**
	 * Returns an array of objects (stdObject) matching specific token criteria.
	 * Accepts token_id, user_id, gateway_id, and type.
	 * Each object should contain the fields token_id, gateway_id, token, user_id, type, is_default.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	public function get_tokens( $args );

	/**
	 * Returns an stdObject of a token for a user's default token.
	 * Should contain the fields token_id, gateway_id, token, user_id, type, is_default.
	 *
	 * @param int $user_id User ID.
	 * @return object
	 */
	public function get_users_default_token( $user_id );

	/**
	 * Returns an stdObject of a token.
	 * Should contain the fields token_id, gateway_id, token, user_id, type, is_default.
	 *
	 * @param int $token_id Token ID.
	 * @return object
	 */
	public function get_token_by_id( $token_id );

	/**
	 * Returns metadata for a specific payment token.
	 *
	 * @param int $token_id Token ID.
	 * @return array
	 */
	public function get_metadata( $token_id );

	/**
	 * Get a token's type by ID.
	 *
	 * @param int $token_id Token ID.
	 * @return string
	 */
	public function get_token_type_by_id( $token_id );

	/**
	 * Update's a tokens default status in the database. Used for quickly
	 * looping through tokens and setting their statuses instead of creating a bunch
	 * of objects.
	 *
	 * @param int  $token_id Token ID.
	 * @param bool $status If should update status.
	 * @return string
	 */
	public function set_default_status( $token_id, $status = true );
}
