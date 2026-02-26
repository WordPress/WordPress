<?php
/**
 * Connectors API.
 *
 * @package WordPress
 * @subpackage Connectors
 * @since 7.0.0
 */

use WordPress\AiClient\AiClient;
use WordPress\AiClient\Providers\Http\DTO\ApiKeyRequestAuthentication;

/**
 * Registers the Connectors menu item under Settings.
 *
 * @since 7.0.0
 * @access private
 */
function _wp_connectors_add_settings_menu_item(): void {
	if ( ! class_exists( '\WordPress\AiClient\AiClient' ) || ! function_exists( 'wp_connectors_wp_admin_render_page' ) ) {
		return;
	}

	add_submenu_page(
		'options-general.php',
		__( 'Connectors' ),
		__( 'Connectors' ),
		'manage_options',
		'connectors-wp-admin',
		'wp_connectors_wp_admin_render_page',
		1
	);
}
add_action( 'admin_menu', '_wp_connectors_add_settings_menu_item' );

/**
 * Masks an API key, showing only the last 4 characters.
 *
 * @since 7.0.0
 * @access private
 *
 * @param string $key The API key to mask.
 * @return string The masked key, e.g. "************fj39".
 */
function _wp_connectors_mask_api_key( string $key ): string {
	if ( strlen( $key ) <= 4 ) {
		return $key;
	}

	return str_repeat( "\u{2022}", min( strlen( $key ) - 4, 16 ) ) . substr( $key, -4 );
}

/**
 * Checks whether an API key is valid for a given provider.
 *
 * @since 7.0.0
 * @access private
 *
 * @param string $key         The API key to check.
 * @param string $provider_id The WP AI client provider ID.
 * @return bool|null True if valid, false if invalid, null if unable to determine.
 */
function _wp_connectors_is_api_key_valid( string $key, string $provider_id ): ?bool {
	try {
		$registry = AiClient::defaultRegistry();

		if ( ! $registry->hasProvider( $provider_id ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				sprintf(
					/* translators: %s: AI provider ID. */
					__( 'The provider "%s" is not registered in the AI client registry.' ),
					$provider_id
				),
				'7.0.0'
			);
			return null;
		}

		$registry->setProviderRequestAuthentication(
			$provider_id,
			new ApiKeyRequestAuthentication( $key )
		);

		return $registry->isProviderConfigured( $provider_id );
	} catch ( Exception $e ) {
		wp_trigger_error( __FUNCTION__, $e->getMessage() );
		return null;
	}
}

/**
 * Retrieves the real (unmasked) value of a connector API key.
 *
 * Temporarily removes the masking filter, reads the option, then re-adds it.
 *
 * @since 7.0.0
 * @access private
 *
 * @param string   $option_name   The option name for the API key.
 * @param callable $mask_callback The mask filter function.
 * @return string The real API key value.
 */
function _wp_connectors_get_real_api_key( string $option_name, callable $mask_callback ): string {
	remove_filter( "option_{$option_name}", $mask_callback );
	$value = get_option( $option_name, '' );
	add_filter( "option_{$option_name}", $mask_callback );
	return (string) $value;
}

/**
 * Gets the registered connector provider settings.
 *
 * @since 7.0.0
 * @access private
 *
 * @return array<string, array{provider: string, label: string, description: string, mask: callable, sanitize: callable}> Provider settings keyed by setting name.
 */
function _wp_connectors_get_provider_settings(): array {
	$providers = array(
		'google'    => array(
			'name' => 'Google',
		),
		'openai'    => array(
			'name' => 'OpenAI',
		),
		'anthropic' => array(
			'name' => 'Anthropic',
		),
	);

	$provider_settings = array();
	foreach ( $providers as $provider => $data ) {
		$setting_name = "connectors_ai_{$provider}_api_key";

		$provider_settings[ $setting_name ] = array(
			'provider'    => $provider,
			'label'       => sprintf(
				/* translators: %s: AI provider name. */
				__( '%s API Key' ),
				$data['name']
			),
			'description' => sprintf(
				/* translators: %s: AI provider name. */
				__( 'API key for the %s AI provider.' ),
				$data['name']
			),
			'mask'        => '_wp_connectors_mask_api_key',
			'sanitize'    => static function ( string $value ) use ( $provider ): string {
				$value = sanitize_text_field( $value );
				if ( '' === $value ) {
					return $value;
				}

				$valid = _wp_connectors_is_api_key_valid( $value, $provider );
				return true === $valid ? $value : '';
			},
		);
	}
	return $provider_settings;
}

/**
 * Validates connector API keys in the REST response when explicitly requested.
 *
 * Runs on `rest_post_dispatch` for `/wp/v2/settings` requests that include connector
 * fields via `_fields`. For each requested connector field, it validates the unmasked
 * key against the provider and replaces the response value with `invalid_key` if
 * validation fails.
 *
 * @since 7.0.0
 * @access private
 *
 * @param WP_REST_Response $response The response object.
 * @param WP_REST_Server   $server   The server instance.
 * @param WP_REST_Request  $request  The request object.
 * @return WP_REST_Response The potentially modified response.
 */
function _wp_connectors_validate_keys_in_rest( WP_REST_Response $response, WP_REST_Server $server, WP_REST_Request $request ): WP_REST_Response {
	if ( '/wp/v2/settings' !== $request->get_route() ) {
		return $response;
	}

	if ( ! class_exists( '\WordPress\AiClient\AiClient' ) ) {
		return $response;
	}

	$fields = $request->get_param( '_fields' );
	if ( ! $fields ) {
		return $response;
	}

	if ( is_array( $fields ) ) {
		$requested = $fields;
	} else {
		$requested = array_map( 'trim', explode( ',', $fields ) );
	}

	$data = $response->get_data();
	if ( ! is_array( $data ) ) {
		return $response;
	}

	foreach ( _wp_connectors_get_provider_settings() as $setting_name => $config ) {
		if ( ! in_array( $setting_name, $requested, true ) ) {
			continue;
		}

		$real_key = _wp_connectors_get_real_api_key( $setting_name, $config['mask'] );
		if ( '' === $real_key ) {
			continue;
		}

		if ( true !== _wp_connectors_is_api_key_valid( $real_key, $config['provider'] ) ) {
			$data[ $setting_name ] = 'invalid_key';
		}
	}

	$response->set_data( $data );
	return $response;
}
add_filter( 'rest_post_dispatch', '_wp_connectors_validate_keys_in_rest', 10, 3 );

/**
 * Registers default connector settings and mask/sanitize filters.
 *
 * @since 7.0.0
 * @access private
 */
function _wp_register_default_connector_settings(): void {
	if ( ! class_exists( '\WordPress\AiClient\AiClient' ) ) {
		return;
	}

	foreach ( _wp_connectors_get_provider_settings() as $setting_name => $config ) {
		register_setting(
			'connectors',
			$setting_name,
			array(
				'type'              => 'string',
				'label'             => $config['label'],
				'description'       => $config['description'],
				'default'           => '',
				'show_in_rest'      => true,
				'sanitize_callback' => $config['sanitize'],
			)
		);
		add_filter( "option_{$setting_name}", $config['mask'] );
	}
}
add_action( 'init', '_wp_register_default_connector_settings' );

/**
 * Passes stored connector API keys to the WP AI client.
 *
 * @since 7.0.0
 * @access private
 */
function _wp_connectors_pass_default_keys_to_ai_client(): void {
	if ( ! class_exists( '\WordPress\AiClient\AiClient' ) ) {
		return;
	}
	try {
		$registry = AiClient::defaultRegistry();
		foreach ( _wp_connectors_get_provider_settings() as $setting_name => $config ) {
			$api_key = _wp_connectors_get_real_api_key( $setting_name, $config['mask'] );
			if ( '' === $api_key || ! $registry->hasProvider( $config['provider'] ) ) {
				continue;
			}

			$registry->setProviderRequestAuthentication(
				$config['provider'],
				new ApiKeyRequestAuthentication( $api_key )
			);
		}
	} catch ( Exception $e ) {
			wp_trigger_error( __FUNCTION__, $e->getMessage() );
	}
}
add_action( 'init', '_wp_connectors_pass_default_keys_to_ai_client' );
