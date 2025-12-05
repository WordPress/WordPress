<?php

namespace Yoast\WP\SEO\Content_Type_Visibility\Application;

use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast_Notification_Center;

/**
 * Handles dismissing notifications and "New" badges for new content types.
 */
class Content_Type_Visibility_Dismiss_Notifications {

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Constructs Content_Type_Visibility_Dismiss_New_Route.
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		$this->options = $options;
	}

	/**
	 * Removes New badge from a post type in the Settings, remove notifications if needed.
	 *
	 * @param string $post_type_name The post type name from the request.
	 * @return array The response.
	 */
	public function post_type_dismiss( $post_type_name ) {
		$success                 = true;
		$message                 = \__( 'Post type is not new.', 'wordpress-seo' );
		$post_types_needs_review = $this->options->get( 'new_post_types', [] );

		if ( $post_types_needs_review && \in_array( $post_type_name, $post_types_needs_review, true ) ) {
			$new_needs_review = \array_diff( $post_types_needs_review, [ $post_type_name ] );
			$success          = $this->options->set( 'new_post_types', $new_needs_review );
			$message          = ( $success ) ? \__( 'Post type is no longer new.', 'wordpress-seo' ) : \__( 'Error: Post type was not removed from new_post_types list.', 'wordpress-seo' );
			if ( $success ) {
				$this->maybe_dismiss_notifications( [ 'new_post_types' => $new_needs_review ] );
			}
		}

		$status = ( $success ) ? 200 : 400;

		return [
			'message' => $message,
			'success' => $success,
			'status'  => $status,
		];
	}

	/**
	 * Removes New badge from a taxonomy in the Settings, remove notifications if needed.
	 *
	 * @param string $taxonomy_name The taxonomy name from the request.
	 * @return array The response.
	 */
	public function taxonomy_dismiss( $taxonomy_name ) {
		$success                 = true;
		$message                 = \__( 'Taxonomy is not new.', 'wordpress-seo' );
		$taxonomies_needs_review = $this->options->get( 'new_taxonomies', [] );

		if ( \in_array( $taxonomy_name, $taxonomies_needs_review, true ) ) {

			$new_needs_review = \array_diff( $taxonomies_needs_review, [ $taxonomy_name ] );
			$success          = $this->options->set( 'new_taxonomies', $new_needs_review );
			$message          = ( $success ) ? \__( 'Taxonomy is no longer new.', 'wordpress-seo' ) : \__( 'Error: Taxonomy was not removed from new_taxonomies list.', 'wordpress-seo' );
			if ( $success ) {
				$this->maybe_dismiss_notifications( [ 'new_taxonomies' => $new_needs_review ] );
			}
		}

		$status = ( $success ) ? 200 : 400;

		return [
			'message' => $message,
			'success' => $success,
			'status'  => $status,
		];
	}

	/**
	 * Checks if there are new content types or taxonomies.
	 *
	 * @param array $new_content_types The new content types.
	 * @return void
	 */
	public function maybe_dismiss_notifications( $new_content_types = [] ) {

		$post_types_needs_review = ( \array_key_exists( 'new_post_types', $new_content_types ) ) ? $new_content_types['new_post_types'] : $this->options->get( 'new_post_types', [] );
		$taxonomies_needs_review = ( \array_key_exists( 'new_taxonomies', $new_content_types ) ) ? $new_content_types['new_taxonomies'] : $this->options->get( 'new_taxonomies', [] );

		if ( $post_types_needs_review || $taxonomies_needs_review ) {
			return;
		}
		$this->dismiss_notifications();
	}

	/**
	 * Dismisses the notification in the notification center when there are no more new content types.
	 *
	 * @return bool
	 */
	public function dismiss_notifications() {
		$notification_center = Yoast_Notification_Center::get();
		$notification_center->remove_notification_by_id( 'content-types-made-public' );
		return $this->options->set( 'show_new_content_type_notification', false );
	}

	/**
	 * Check if there is a new content type to show notification only once in the settings.
	 *
	 * @return bool Should the notification be shown.
	 */
	public function maybe_add_settings_notification() {
		$show_new_content_type_notification = $this->options->get( 'show_new_content_type_notification', false );
		if ( $show_new_content_type_notification ) {
			$this->options->set( 'show_new_content_type_notification', false );
		}
		return $show_new_content_type_notification;
	}
}
