<?php
namespace Elementor\App\Modules\KitLibrary\Connect;

use Elementor\Core\Common\Modules\Connect\Apps\Base_App;
use Elementor\Core\Common\Modules\Connect\Apps\Library;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Kit_Library extends Library {
	const DEFAULT_BASE_ENDPOINT = 'https://my.elementor.com/api/v1/kits-library';
	const FALLBACK_BASE_ENDPOINT = 'https://ms-8874.elementor.com/api/v1/kits-library';

	public function get_title() {
		return esc_html__( 'Kit Library', 'elementor' );
	}

	public function get_all( $args = [] ) {
		return $this->http_request( 'GET', 'kits/plugin-version/' . ELEMENTOR_VERSION, $args );
	}

	public function get_by_id( $id ) {
		return $this->http_request( 'GET', 'kits/' . $id );
	}

	public function get_taxonomies() {
		return $this->http_request( 'GET', 'taxonomies' );
	}

	public function get_manifest( $id ) {
		return $this->http_request( 'GET', "kits/{$id}/manifest" );
	}

	public function download_link( $id ) {
		return $this->http_request( 'GET', "kits/{$id}/download-link" );
	}

	protected function get_api_url() {
		return [
			static::DEFAULT_BASE_ENDPOINT,
			static::FALLBACK_BASE_ENDPOINT,
		];
	}

	/**
	 * Get all the connect information
	 *
	 * @return array
	 */
	protected function get_connect_info() {
		$connect_info = $this->get_base_connect_info();

		$additional_info = [];

		// BC Support.
		$old_kit_library = new \Elementor\Core\App\Modules\KitLibrary\Connect\Kit_Library();

		/**
		 * Additional connect info.
		 *
		 * Filters the connection information when connecting to Elementor servers.
		 * This hook can be used to add more information or add more data.
		 *
		 * @param array    $additional_info Additional connecting information array.
		 * @param Base_App $this            The base app instance.
		 */
		$additional_info = apply_filters( 'elementor/connect/additional-connect-info', $additional_info, $old_kit_library );

		return array_merge( $connect_info, $additional_info );
	}

	protected function init() {
		// Remove parent init actions.
	}
}
