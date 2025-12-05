<?php

namespace Yoast\WP\SEO\Actions;

use Yoast\WP\SEO\Helpers\User_Helper;

/**
 * Class Alert_Dismissal_Action.
 */
class Alert_Dismissal_Action {

	public const USER_META_KEY = '_yoast_alerts_dismissed';

	/**
	 * Holds the user helper instance.
	 *
	 * @var User_Helper
	 */
	protected $user;

	/**
	 * Constructs Alert_Dismissal_Action.
	 *
	 * @param User_Helper $user User helper.
	 */
	public function __construct( User_Helper $user ) {
		$this->user = $user;
	}

	/**
	 * Dismisses an alert.
	 *
	 * @param string $alert_identifier Alert identifier.
	 *
	 * @return bool Whether the dismiss was successful or not.
	 */
	public function dismiss( $alert_identifier ) {
		$user_id = $this->user->get_current_user_id();
		if ( $user_id === 0 ) {
			return false;
		}

		if ( $this->is_allowed( $alert_identifier ) === false ) {
			return false;
		}

		$dismissed_alerts = $this->get_dismissed_alerts( $user_id );
		if ( $dismissed_alerts === false ) {
			return false;
		}

		if ( \array_key_exists( $alert_identifier, $dismissed_alerts ) === true ) {
			// The alert is already dismissed.
			return true;
		}

		// Add this alert to the dismissed alerts.
		$dismissed_alerts[ $alert_identifier ] = true;

		// Save.
		return $this->user->update_meta( $user_id, static::USER_META_KEY, $dismissed_alerts ) !== false;
	}

	/**
	 * Resets an alert.
	 *
	 * @param string $alert_identifier Alert identifier.
	 *
	 * @return bool Whether the reset was successful or not.
	 */
	public function reset( $alert_identifier ) {
		$user_id = $this->user->get_current_user_id();
		if ( $user_id === 0 ) {
			return false;
		}

		if ( $this->is_allowed( $alert_identifier ) === false ) {
			return false;
		}

		$dismissed_alerts = $this->get_dismissed_alerts( $user_id );
		if ( $dismissed_alerts === false ) {
			return false;
		}

		$amount_of_dismissed_alerts = \count( $dismissed_alerts );
		if ( $amount_of_dismissed_alerts === 0 ) {
			// No alerts: nothing to reset.
			return true;
		}

		if ( \array_key_exists( $alert_identifier, $dismissed_alerts ) === false ) {
			// Alert not found: nothing to reset.
			return true;
		}

		if ( $amount_of_dismissed_alerts === 1 ) {
			// The 1 remaining dismissed alert is the alert to reset: delete the alerts user meta row.
			return $this->user->delete_meta( $user_id, static::USER_META_KEY, $dismissed_alerts );
		}

		// Remove this alert from the dismissed alerts.
		unset( $dismissed_alerts[ $alert_identifier ] );

		// Save.
		return $this->user->update_meta( $user_id, static::USER_META_KEY, $dismissed_alerts ) !== false;
	}

	/**
	 * Returns if an alert is dismissed or not.
	 *
	 * @param string $alert_identifier Alert identifier.
	 *
	 * @return bool Whether the alert has been dismissed.
	 */
	public function is_dismissed( $alert_identifier ) {
		$user_id = $this->user->get_current_user_id();
		if ( $user_id === 0 ) {
			return false;
		}

		if ( $this->is_allowed( $alert_identifier ) === false ) {
			return false;
		}

		$dismissed_alerts = $this->get_dismissed_alerts( $user_id );
		if ( $dismissed_alerts === false ) {
			return false;
		}

		return \array_key_exists( $alert_identifier, $dismissed_alerts );
	}

	/**
	 * Returns an object with all alerts dismissed by current user.
	 *
	 * @return array|false An array with the keys of all Alerts that have been dismissed
	 *                     by the current user or `false`.
	 */
	public function all_dismissed() {
		$user_id = $this->user->get_current_user_id();
		if ( $user_id === 0 ) {
			return false;
		}

		$dismissed_alerts = $this->get_dismissed_alerts( $user_id );
		if ( $dismissed_alerts === false ) {
			return false;
		}

		return $dismissed_alerts;
	}

	/**
	 * Returns if an alert is allowed or not.
	 *
	 * @param string $alert_identifier Alert identifier.
	 *
	 * @return bool Whether the alert is allowed.
	 */
	public function is_allowed( $alert_identifier ) {
		return \in_array( $alert_identifier, $this->get_allowed_dismissable_alerts(), true );
	}

	/**
	 * Retrieves the dismissed alerts.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return string[]|false The dismissed alerts. False for an invalid $user_id.
	 */
	protected function get_dismissed_alerts( $user_id ) {
		$dismissed_alerts = $this->user->get_meta( $user_id, static::USER_META_KEY, true );
		if ( $dismissed_alerts === false ) {
			// Invalid user ID.
			return false;
		}

		if ( $dismissed_alerts === '' ) {
			/*
			 * When no database row exists yet, an empty string is returned because of the `single` parameter.
			 * We do want a single result returned, but the default should be an empty array instead.
			 */
			return [];
		}

		return $dismissed_alerts;
	}

	/**
	 * Retrieves the allowed dismissable alerts.
	 *
	 * @return string[] The allowed dismissable alerts.
	 */
	protected function get_allowed_dismissable_alerts() {
		/**
		 * Filter: 'wpseo_allowed_dismissable_alerts' - List of allowed dismissable alerts.
		 *
		 * @param string[] $allowed_dismissable_alerts Allowed dismissable alerts list.
		 */
		$allowed_dismissable_alerts = \apply_filters( 'wpseo_allowed_dismissable_alerts', [] );

		if ( \is_array( $allowed_dismissable_alerts ) === false ) {
			return [];
		}

		// Only allow strings.
		$allowed_dismissable_alerts = \array_filter( $allowed_dismissable_alerts, 'is_string' );

		// Filter unique and reorder indices.
		$allowed_dismissable_alerts = \array_values( \array_unique( $allowed_dismissable_alerts ) );

		return $allowed_dismissable_alerts;
	}
}
