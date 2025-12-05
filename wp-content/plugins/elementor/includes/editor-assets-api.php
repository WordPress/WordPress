<?php
namespace Elementor\Includes;

class EditorAssetsAPI {
	protected array $config;

	const ASSETS_DATA_TRANSIENT_KEY = 'ASSETS_DATA_TRANSIENT_KEY';

	const ASSETS_DATA_URL = 'ASSETS_DATA_URL';

	const ASSETS_DATA_KEY = 'ASSETS_DATA_KEY';

	public function __construct( array $config ) {
		$this->config = $config;
	}

	public function config( $key ): string {
		return $this->config[ $key ] ?? '';
	}

	public function get_assets_data( $force_request = false ): array {
		$assets_data = $this->get_transient( $this->config( static::ASSETS_DATA_TRANSIENT_KEY ) );

		if ( $force_request || false === $assets_data ) {
			$assets_data = $this->fetch_data();
			$this->set_transient( $this->config( static::ASSETS_DATA_TRANSIENT_KEY ), $assets_data, '+1 hour' );
		}

		return $assets_data;
	}

	private function fetch_data(): array {
		$response = wp_remote_get( $this->config( static::ASSETS_DATA_URL ) );

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $data[ $this->config( static::ASSETS_DATA_KEY ) ] ) || ! is_array( $data[ $this->config( static::ASSETS_DATA_KEY ) ] ) ) {
			return [];
		}

		return $data[ $this->config( static::ASSETS_DATA_KEY ) ];
	}

	private function get_transient( $cache_key ) {
		$cache = get_option( $cache_key );

		if ( empty( $cache['timeout'] ) ) {
			return false;
		}

		if ( current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false;
		}

		return json_decode( $cache['value'], true );
	}

	private function set_transient( $cache_key, $value, $expiration = '+12 hours' ): bool {
		$data = [
			'timeout' => strtotime( $expiration, current_time( 'timestamp' ) ),
			'value' => wp_json_encode( $value ),
		];

		return update_option( $cache_key, $data, false );
	}
}
