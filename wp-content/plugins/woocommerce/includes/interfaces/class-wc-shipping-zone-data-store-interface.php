<?php
/**
 * Shipping Zone Data Store Interface
 *
 * @version 3.0.0
 * @package WooCommerce\Interface
 */

/**
 * WC Shipping Zone Data Store Interface.
 *
 * Functions that must be defined by shipping zone store classes.
 *
 * @version  3.0.0
 */
interface WC_Shipping_Zone_Data_Store_Interface {
	/**
	 * Get a list of shipping methods for a specific zone.
	 *
	 * @param  int  $zone_id Zone ID.
	 * @param  bool $enabled_only True to request enabled methods only.
	 * @return array Array of objects containing method_id, method_order, instance_id, is_enabled
	 */
	public function get_methods( $zone_id, $enabled_only );

	/**
	 * Get count of methods for a zone.
	 *
	 * @param int $zone_id Zone ID.
	 * @return int Method Count
	 */
	public function get_method_count( $zone_id );

	/**
	 * Add a shipping method to a zone.
	 *
	 * @param int    $zone_id Zone ID.
	 * @param string $type Method Type/ID.
	 * @param int    $order Method Order ID.
	 * @return int Instance ID
	 */
	public function add_method( $zone_id, $type, $order );

	/**
	 * Delete a method instance.
	 *
	 * @param int $instance_id Instance ID.
	 */
	public function delete_method( $instance_id );

	/**
	 * Get a shipping zone method instance.
	 *
	 * @param int $instance_id Instance ID.
	 * @return object
	 */
	public function get_method( $instance_id );

	/**
	 * Find a matching zone ID for a given package.
	 *
	 * @param object $package Zone package object.
	 * @return int
	 */
	public function get_zone_id_from_package( $package );

	/**
	 * Return an ordered list of zones.
	 *
	 * @return array An array of objects containing a zone_id, zone_name, and zone_order.
	 */
	public function get_zones();

	/**
	 * Return a zone ID from an instance ID.
	 *
	 * @param int $id Instance ID.
	 * @return int
	 */
	public function get_zone_id_by_instance_id( $id );
}
