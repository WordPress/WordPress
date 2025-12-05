<?php
namespace Elementor\Modules\Notifications;

use Elementor\User;

class API {

	const NOTIFICATIONS_URL = 'https://assets.elementor.com/notifications/v1/notifications.json';

	public static function get_notifications_by_conditions( $force_request = false ) {
		$notifications = static::get_notifications( $force_request );

		$filtered_notifications = [];

		foreach ( $notifications as $notification ) {
			if ( empty( $notification['conditions'] ) ) {
				$filtered_notifications = static::add_to_array( $filtered_notifications, $notification );

				continue;
			}

			if ( ! static::check_conditions( $notification['conditions'] ) ) {
				continue;
			}

			$filtered_notifications = static::add_to_array( $filtered_notifications, $notification );
		}

		return $filtered_notifications;
	}

	private static function get_notifications( $force_request = false ) {
		$notifications = self::get_transient( '_elementor_notifications_data' );

		if ( $force_request || false === $notifications ) {
			$notifications = static::fetch_data();

			static::set_transient( '_elementor_notifications_data', $notifications, '+1 hour' );
		}

		$notifications = apply_filters( 'elementor/core/admin/notifications', $notifications );

		return $notifications;
	}

	private static function fetch_data(): array {
		$response = wp_remote_get( self::NOTIFICATIONS_URL );

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $data['notifications'] ) || ! is_array( $data['notifications'] ) ) {
			return [];
		}

		return $data['notifications'];
	}

	private static function add_to_array( $filtered_notifications, $notification ) {
		foreach ( $filtered_notifications as $filtered_notification ) {
			if ( $filtered_notification['id'] === $notification['id'] ) {
				return $filtered_notifications;
			}
		}

		$filtered_notifications[] = $notification;

		return $filtered_notifications;
	}

	private static function check_conditions( $groups ) {
		foreach ( $groups as $group ) {
			if ( static::check_group( $group ) ) {
				return true;
			}
		}

		return false;
	}

	private static function check_group( $group ) {
		$is_or_relation = ! empty( $group['relation'] ) && 'OR' === $group['relation'];
		unset( $group['relation'] );
		$result = false;

		foreach ( $group as $condition ) {
			// Reset results for each condition.
			$result = false;
			switch ( $condition['type'] ) {
				case 'wordpress': // phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText
					// include an unmodified $wp_version
					include ABSPATH . WPINC . '/version.php';
					$result = version_compare( $wp_version, $condition['version'], $condition['operator'] );
					break;
				case 'multisite':
					$result = is_multisite() === $condition['multisite'];
					break;
				case 'language':
					$in_array = in_array( get_locale(), $condition['languages'], true );
					$result = 'in' === $condition['operator'] ? $in_array : ! $in_array;
					break;
				case 'plugin':
					if ( ! function_exists( 'is_plugin_active' ) ) {
						require_once ABSPATH . 'wp-admin/includes/plugin.php';
					}

					$is_plugin_active = is_plugin_active( $condition['plugin'] );

					if ( empty( $condition['operator'] ) ) {
						$condition['operator'] = '==';
					}

					$result = '==' === $condition['operator'] ? $is_plugin_active : ! $is_plugin_active;
					break;
				case 'theme':
					$theme = wp_get_theme();
					if ( wp_get_theme()->parent() ) {
						$theme = wp_get_theme()->parent();
					}

					if ( $theme->get_template() === $condition['theme'] ) {
						$version = $theme->version;
					} else {
						$version = '';
					}

					$result = version_compare( $version, $condition['version'], $condition['operator'] );
					break;
				case 'introduction_meta':
					$result = User::get_introduction_meta( $condition['meta'] );
					break;

				default:
					/**
					 * Filters the notification condition, whether to check the group or not.
					 *
					 * The dynamic portion of the hook name, `$condition['type']`, refers to the condition type.
					 *
					 * @since 3.19.0
					 *
					 * @param bool  $result    Whether to check the group.
					 * @param array $condition Notification condition.
					 */
					$result = apply_filters( "elementor/notifications/condition/{$condition['type']}", $result, $condition );
					break;
			}

			if ( ( $is_or_relation && $result ) || ( ! $is_or_relation && ! $result ) ) {
				return $result;
			}
		}

		return $result;
	}

	private static function get_transient( $cache_key ) {
		$cache = get_option( $cache_key );

		if ( empty( $cache['timeout'] ) ) {
			return false;
		}

		if ( current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false;
		}

		return json_decode( $cache['value'], true );
	}

	private static function set_transient( $cache_key, $value, $expiration = '+12 hours' ) {
		$data = [
			'timeout' => strtotime( $expiration, current_time( 'timestamp' ) ),
			'value' => json_encode( $value ),
		];

		return update_option( $cache_key, $data, false );
	}
}
