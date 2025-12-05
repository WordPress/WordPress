<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Notifies the user to update the Search Appearance settings when the site is set to represent a Person,
 * but no person (name) has been chosen.
 */
class WPSEO_Schema_Person_Upgrade_Notification implements WPSEO_WordPress_Integration {

	/**
	 * Registers all hooks to WordPress
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'handle_notification' ] );
	}

	/**
	 * Handles if the notification should be added or removed.
	 *
	 * @return void
	 */
	public function handle_notification() {
		$company_or_person_user_id = WPSEO_Options::get( 'company_or_person_user_id', false );
		if ( WPSEO_Options::get( 'company_or_person' ) === 'person' && empty( $company_or_person_user_id ) ) {
			$this->add_notification();
			return;
		}

		$this->remove_notification();
	}

	/**
	 * Adds a notification to the notification center.
	 *
	 * @return void
	 */
	protected function add_notification() {
		$notification_center = Yoast_Notification_Center::get();
		$notification_center->add_notification( $this->get_notification() );
	}

	/**
	 * Removes a notification to the notification center.
	 *
	 * @return void
	 */
	protected function remove_notification() {
		$notification_center = Yoast_Notification_Center::get();
		$notification_center->remove_notification( $this->get_notification() );
	}

	/**
	 * Gets the notification object.
	 *
	 * @return Yoast_Notification
	 */
	protected function get_notification() {
		$message = sprintf(
			/* translators: %1$s is a link start tag to the Search Appearance settings, %2$s is the link closing tag. */
			__( 'You have previously set your site to represent a person. We’ve improved our functionality around Schema and the Knowledge Graph, so you should go in and %1$scomplete those settings%2$s.', 'wordpress-seo' ),
			'<a href="' . esc_url( admin_url( 'admin.php?page=wpseo_page_settings#/site-representation' ) ) . '">',
			'</a>'
		);

		$notification = new Yoast_Notification(
			$message,
			[
				'type'         => Yoast_Notification::WARNING,
				'id'           => 'wpseo-schema-person-upgrade',
				'capabilities' => 'wpseo_manage_options',
				'priority'     => 0.8,
			]
		);

		return $notification;
	}
}
