<?php
/**
 * Handles polling and storage of specs
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Specs data source poller class.
 * This handles polling specs from JSON endpoints, and
 * stores the specs in to the database as an option.
 */
class DataSourcePoller extends \Automattic\WooCommerce\Admin\DataSourcePoller {
	const ID           = 'remote_inbox_notifications';
	const DATA_SOURCES = array(
		'https://woocommerce.com/wp-json/wccom/inbox-notifications/1.0/notifications.json',
	);
	/**
	 * Class instance.
	 *
	 * @var Analytics instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self(
				self::ID,
				self::DATA_SOURCES,
				array(
					'spec_key' => 'slug',
				)
			);
		}
		return self::$instance;
	}

	/**
	 * Validate the spec.
	 *
	 * @param object $spec The spec to validate.
	 * @param string $url  The url of the feed that provided the spec.
	 *
	 * @return bool The result of the validation.
	 */
	protected function validate_spec( $spec, $url ) {
		$logger         = self::get_logger();
		$logger_context = array( 'source' => $url );

		if ( ! isset( $spec->slug ) ) {
			$logger->error(
				'Spec is invalid because the slug is missing in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $spec, true ), $logger_context );

			return false;
		}

		if ( ! isset( $spec->status ) ) {
			$logger->error(
				'Spec is invalid because the status is missing in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $spec, true ), $logger_context );

			return false;
		}

		if ( ! isset( $spec->locales ) || ! is_array( $spec->locales ) ) {
			$logger->error(
				'Spec is invalid because the status is missing or empty in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $spec, true ), $logger_context );

			return false;
		}

		if ( null === SpecRunner::get_locale( $spec->locales ) ) {
			$logger->error(
				'Spec is invalid because the locale could not be retrieved in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $spec, true ), $logger_context );

			return false;
		}

		if ( ! isset( $spec->type ) ) {
			$logger->error(
				'Spec is invalid because the type is missing in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $spec, true ), $logger_context );

			return false;
		}

		if ( isset( $spec->actions ) && is_array( $spec->actions ) ) {
			foreach ( $spec->actions as $action ) {
				if ( ! $this->validate_action( $action, $url ) ) {
					$logger->error(
						'Spec is invalid because an action is invalid in feed',
						$logger_context
					);
					// phpcs:ignore
					$logger->error( print_r( $spec, true ), $logger_context );

					return false;
				}
			}
		}

		if ( isset( $spec->rules ) && is_array( $spec->rules ) ) {
			foreach ( $spec->rules as $rule ) {
				if ( ! isset( $rule->type ) ) {
					$logger->error(
						'Spec is invalid because a rule type is empty in feed',
						$logger_context
					);
					// phpcs:ignore
					$logger->error( print_r( $rule, true ), $logger_context );
					// phpcs:ignore
					$logger->error( print_r( $spec, true ), $logger_context );

					return false;
				}

				$processor = GetRuleProcessor::get_processor( $rule->type );

				if ( ! $processor->validate( $rule ) ) {
					$logger->error(
						'Spec is invalid because a rule is invalid in feed',
						$logger_context
					);
					// phpcs:ignore
					$logger->error( print_r( $rule, true ), $logger_context );
					// phpcs:ignore
					$logger->error( print_r( $spec, true ), $logger_context );

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Validate the action.
	 *
	 * @param object $action The action to validate.
	 * @param string $url    The url of the feed containing the action (for error reporting).
	 *
	 * @return bool The result of the validation.
	 */
	private function validate_action( $action, $url ) {
		$logger         = self::get_logger();
		$logger_context = array( 'source' => $url );

		if ( ! isset( $action->locales ) || ! is_array( $action->locales ) ) {
			$logger->error(
				'Action is invalid because it has empty or missing locales in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $action, true ), $logger_context );

			return false;
		}

		if ( null === SpecRunner::get_action_locale( $action->locales ) ) {
			$logger->error(
				'Action is invalid because the locale could not be retrieved in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $action, true ), $logger_context );

			return false;
		}

		if ( ! isset( $action->name ) ) {
			$logger->error(
				'Action is invalid because the name is missing in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $action, true ), $logger_context );

			return false;
		}

		if ( ! isset( $action->status ) ) {
			$logger->error(
				'Action is invalid because the status is missing in feed',
				$logger_context
			);
			// phpcs:ignore
			$logger->error( print_r( $action, true ), $logger_context );

			return false;
		}

		return true;
	}
}
