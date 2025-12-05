<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Inc
 */

/**
 * Represents the post type utils.
 */
class WPSEO_Post_Type {

	/**
	 * Returns an array with the accessible post types.
	 *
	 * An accessible post type is a post type that is public and isn't set as no-index (robots).
	 *
	 * @return array Array with all the accessible post_types.
	 */
	public static function get_accessible_post_types() {
		return YoastSEO()->helpers->post_type->get_accessible_post_types();
	}

	/**
	 * Returns whether the passed post type is considered accessible.
	 *
	 * @param string $post_type The post type to check.
	 *
	 * @return bool Whether or not the post type is considered accessible.
	 */
	public static function is_post_type_accessible( $post_type ) {
		return in_array( $post_type, self::get_accessible_post_types(), true );
	}

	/**
	 * Checks if the request post type is public and indexable.
	 *
	 * @param string $post_type_name The name of the post type to lookup.
	 *
	 * @return bool True when post type is set to index.
	 */
	public static function is_post_type_indexable( $post_type_name ) {
		return YoastSEO()->helpers->post_type->is_indexable( $post_type_name );
	}

	/**
	 * Filters the attachment post type from an array with post_types.
	 *
	 * @param array $post_types The array to filter the attachment post type from.
	 *
	 * @return array The filtered array.
	 */
	public static function filter_attachment_post_type( array $post_types ) {
		if ( WPSEO_Options::get( 'disable-attachment' ) === true ) {
			unset( $post_types['attachment'] );
		}

		return $post_types;
	}

	/**
	 * Checks if the post type is enabled in the REST API.
	 *
	 * @param string $post_type The post type to check.
	 *
	 * @return bool Whether or not the post type is available in the REST API.
	 */
	public static function is_rest_enabled( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		if ( $post_type_object === null ) {
			return false;
		}

		return $post_type_object->show_in_rest === true;
	}

	/**
	 * Checks if the current post type has an archive.
	 *
	 * Context: The has_archive value can be a string or a boolean. In most case it will be a boolean,
	 * but it can be defined as a string. When it is a string the archive_slug will be overwritten to
	 * define another endpoint.
	 *
	 * @param WP_Post_Type $post_type The post type object.
	 *
	 * @return bool True whether the post type has an archive.
	 */
	public static function has_archive( $post_type ) {
		return YoastSEO()->helpers->post_type->has_archive( $post_type );
	}

	/**
	 * Checks if the Yoast Metabox has been enabled for the post type.
	 *
	 * @param string $post_type The post type name.
	 *
	 * @return bool True whether the metabox is enabled.
	 */
	public static function has_metabox_enabled( $post_type ) {
		return WPSEO_Options::get( 'display-metabox-pt-' . $post_type, false );
	}

	/* ********************* DEPRECATED METHODS ********************* */

	/**
	 * Removes the notification related to the post types which have been made public.
	 *
	 * @deprecated 20.10
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public static function remove_post_types_made_public_notification() {
		_deprecated_function( __METHOD__, 'Yoast SEO 20.10', 'Content_Type_Visibility_Dismiss_Notifications::dismiss_notifications' );
		$notification_center = Yoast_Notification_Center::get();
		$notification_center->remove_notification_by_id( 'post-types-made-public' );
	}

	/**
	 * Removes the notification related to the taxonomies which have been made public.
	 *
	 * @deprecated 20.10
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public static function remove_taxonomies_made_public_notification() {
		_deprecated_function( __METHOD__, 'Yoast SEO 20.10', 'Content_Type_Visibility_Dismiss_Notifications::dismiss_notifications' );
		$notification_center = Yoast_Notification_Center::get();
		$notification_center->remove_notification_by_id( 'taxonomies-made-public' );
	}
}
