<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Watcher for the wpseo option.
 *
 * Represents the option wpseo watcher.
 */
class Option_Wpseo_Watcher implements Integration_Interface {

	use No_Conditionals;

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'update_option_wpseo', [ $this, 'check_semrush_option_disabled' ], 10, 2 );
		\add_action( 'update_option_wpseo', [ $this, 'check_wincher_option_disabled' ], 10, 2 );
		\add_action( 'update_option_wpseo', [ $this, 'check_toggle_usage_tracking' ], 10, 2 );
	}

	/**
	 * Checks if the SEMrush integration is disabled; if so, deletes the tokens.
	 *
	 * We delete the tokens if the SEMrush integration is disabled, no matter if
	 * the value has actually changed or not.
	 *
	 * @param array $old_value The old value of the option.
	 * @param array $new_value The new value of the option.
	 *
	 * @return bool Whether the SEMrush tokens have been deleted or not.
	 */
	public function check_semrush_option_disabled( $old_value, $new_value ) {
		return $this->check_token_option_disabled( 'semrush_integration_active', 'semrush_tokens', $new_value );
	}

	/**
	 * Checks if the Wincher integration is disabled; if so, deletes the tokens
	 * and website id.
	 *
	 * We delete them if the Wincher integration is disabled, no matter if the
	 * value has actually changed or not.
	 *
	 * @param array $old_value The old value of the option.
	 * @param array $new_value The new value of the option.
	 *
	 * @return bool Whether the Wincher tokens have been deleted or not.
	 */
	public function check_wincher_option_disabled( $old_value, $new_value ) {
		$disabled = $this->check_token_option_disabled( 'wincher_integration_active', 'wincher_tokens', $new_value );
		if ( $disabled ) {
			\YoastSEO()->helpers->options->set( 'wincher_website_id', '' );
		}

		return $disabled;
	}

	/**
	 * Checks if the WordProof integration is disabled; if so, deletes the tokens
	 *
	 * We delete them if the WordProof integration is disabled, no matter if the
	 * value has actually changed or not.
	 *
	 * @deprecated 22.10
	 * @codeCoverageIgnore
	 *
	 * @param array $old_value The old value of the option.
	 * @param array $new_value The new value of the option.
	 *
	 * @return bool Whether the WordProof tokens have been deleted or not.
	 */
	public function check_wordproof_option_disabled( $old_value, $new_value ) {
		\_deprecated_function( __METHOD__, 'Yoast SEO 22.10' );

		return true;
	}

	/**
	 * Checks if the usage tracking feature is toggled; if so, set an option to stop us from messing with it.
	 *
	 * @param array $old_value The old value of the option.
	 * @param array $new_value The new value of the option.
	 *
	 * @return bool Whether the option is set.
	 */
	public function check_toggle_usage_tracking( $old_value, $new_value ) {
		$option_name = 'tracking';

		if ( \array_key_exists( $option_name, $old_value ) && \array_key_exists( $option_name, $new_value ) && $old_value[ $option_name ] !== $new_value[ $option_name ] && $old_value['toggled_tracking'] === false ) {
			\YoastSEO()->helpers->options->set( 'toggled_tracking', true );

			return true;
		}

		return false;
	}

	/**
	 * Checks if the passed integration is disabled; if so, deletes the tokens.
	 *
	 * We delete the tokens if the integration is disabled, no matter if
	 * the value has actually changed or not.
	 *
	 * @param string $integration_option The intergration option name.
	 * @param string $target_option      The target option to remove the tokens from.
	 * @param array  $new_value          The new value of the option.
	 *
	 * @return bool Whether the tokens have been deleted or not.
	 */
	protected function check_token_option_disabled( $integration_option, $target_option, $new_value ) {
		if ( \array_key_exists( $integration_option, $new_value ) && $new_value[ $integration_option ] === false ) {
			\YoastSEO()->helpers->options->set( $target_option, [] );

			return true;
		}

		return false;
	}
}
