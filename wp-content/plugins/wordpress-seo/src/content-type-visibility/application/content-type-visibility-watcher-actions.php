<?php

namespace Yoast\WP\SEO\Content_Type_Visibility\Application;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Handles showing notifications for new content types.
 * Actions are used in the indexable taxonomy and post type change watchers.
 */
class Content_Type_Visibility_Watcher_Actions implements Integration_Interface {

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The notifications center.
	 *
	 * @var Yoast_Notification_Center
	 */
	private $notification_center;

	/**
	 * The notifications center.
	 *
	 * @var Content_Type_Visibility_Dismiss_Notifications
	 */
	private $content_type_dismiss_notifications;

	/**
	 * Indexable_Post_Type_Change_Watcher constructor.
	 *
	 * @param Options_Helper                                $options                            The options helper.
	 * @param Yoast_Notification_Center                     $notification_center                The notification center.
	 * @param Content_Type_Visibility_Dismiss_Notifications $content_type_dismiss_notifications The content type dismiss notifications.
	 */
	public function __construct(
		Options_Helper $options,
		Yoast_Notification_Center $notification_center,
		Content_Type_Visibility_Dismiss_Notifications $content_type_dismiss_notifications
	) {
		$this->options                            = $options;
		$this->notification_center                = $notification_center;
		$this->content_type_dismiss_notifications = $content_type_dismiss_notifications;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * Register actions that are used in the post types and taxonomies indexable watcher.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Used in Idexable_Post_Type_Change_Watcher class.
		\add_action( 'new_public_post_type_notifications', [ $this, 'new_post_type' ], 10, 1 );
		\add_action( 'clean_new_public_post_type_notifications', [ $this, 'clean_new_public_post_type' ], 10, 1 );

		// Used in Idexable_Taxonomy_Change_Watcher class.
		\add_action( 'new_public_taxonomy_notifications', [ $this, 'new_taxonomy' ], 10, 1 );
		\add_action( 'clean_new_public_taxonomy_notifications', [ $this, 'clean_new_public_taxonomy' ], 10, 1 );
	}

	/**
	 * Update db and tigger notification when a new post type is registered.
	 *
	 * @param array $newly_made_public_post_types The newly made public post types.
	 * @return void
	 */
	public function new_post_type( $newly_made_public_post_types ) {
		$this->options->set( 'new_post_types', $newly_made_public_post_types );
		$this->options->set( 'show_new_content_type_notification', true );
		$this->maybe_add_notification();
	}

	/**
	 * Update db when a post type is made removed.
	 *
	 * @param array $newly_made_non_public_post_types The newly made non public post types.
	 * @return void
	 */
	public function clean_new_public_post_type( $newly_made_non_public_post_types ) {
		// See if post types that needs review were removed and update option.
		$needs_review     = $this->options->get( 'new_post_types', [] );
		$new_needs_review = \array_diff( $needs_review, $newly_made_non_public_post_types );
		if ( \count( $new_needs_review ) !== \count( $needs_review ) ) {
			$this->options->set( 'new_post_types', $new_needs_review );
			$this->content_type_dismiss_notifications->maybe_dismiss_notifications( [ 'new_post_types' => $new_needs_review ] );
		}
	}

	/**
	 * Update db and tigger notification when a new taxonomy is registered.
	 *
	 * @param array $newly_made_public_taxonomies The newly made public post types.
	 * @return void
	 */
	public function new_taxonomy( $newly_made_public_taxonomies ) {
		$this->options->set( 'new_taxonomies', $newly_made_public_taxonomies );
		$this->options->set( 'show_new_content_type_notification', true );
		$this->maybe_add_notification();
	}

	/**
	 * Update db when a post type is made removed.
	 *
	 * @param array $newly_made_non_public_taxonomies The newly made non public post types.
	 * @return void
	 */
	public function clean_new_public_taxonomy( $newly_made_non_public_taxonomies ) {
		// See if post types that needs review were removed and update option.
		$needs_review     = $this->options->get( 'new_taxonomies', [] );
		$new_needs_review = \array_diff( $needs_review, $newly_made_non_public_taxonomies );
		if ( \count( $new_needs_review ) !== \count( $needs_review ) ) {
			$this->options->set( 'new_taxonomies', $new_needs_review );
			$this->content_type_dismiss_notifications->maybe_dismiss_notifications( [ 'new_taxonomies' => $new_needs_review ] );
		}
	}

	/**
	 * Decides if a notification should be added in the notification center.
	 *
	 * @return void
	 */
	public function maybe_add_notification() {
		$notification = $this->notification_center->get_notification_by_id( 'content-types-made-public' );
		if ( $notification === null ) {
			$this->add_notification();
		}
	}

	/**
	 * Adds a notification to be shown on the next page request since posts are updated in an ajax request.
	 *
	 * @return void
	 */
	private function add_notification() {
		$message = \sprintf(
			/* translators: 1: Opening tag of the link to the Search appearance settings page, 2: Link closing tag. */
			\esc_html__( 'You\'ve added a new type of content. We recommend that you review the corresponding %1$sSearch appearance settings%2$s.', 'wordpress-seo' ),
			'<a href="' . \esc_url( \admin_url( 'admin.php?page=wpseo_page_settings' ) ) . '">',
			'</a>'
		);

		$notification = new Yoast_Notification(
			$message,
			[
				'type'         => Yoast_Notification::WARNING,
				'id'           => 'content-types-made-public',
				'capabilities' => 'wpseo_manage_options',
				'priority'     => 0.8,
			]
		);

		$this->notification_center->add_notification( $notification );
	}
}
