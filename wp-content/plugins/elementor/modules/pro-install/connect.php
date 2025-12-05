<?php
namespace Elementor\Modules\ProInstall;

use Elementor\Core\Common\Modules\Connect\Apps\Library;
use Elementor\Utils as ElementorUtils;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Connect extends Library {

	const API_URL = 'https://my.elementor.com/api/v2/artifacts/PLUGIN/';

	public function get_title() {
		return esc_html__( 'pro-install', 'elementor' );
	}

	protected function get_api_url() {
		return static::API_URL . '/';
	}

	public function get_download_link() {
		$response = $this->http_request(
			'GET',
			'latest/download-link',
			[],
			[
				'return_type' => static::HTTP_RETURN_TYPE_ARRAY,
				'with_error_data' => true,
			]
		);

		if ( is_wp_error( $response ) || empty( $response['downloadLink'] ) ) {
			return false;
		}

		return $response['downloadLink'];
	}

	protected function init() {}
}
