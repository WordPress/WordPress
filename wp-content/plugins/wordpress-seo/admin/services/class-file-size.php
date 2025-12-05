<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Services
 */

/**
 * Represents the file size service.
 */
class WPSEO_File_Size_Service {

	/**
	 * Retrieves an indexable.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The response.
	 */
	public function get( WP_REST_Request $request ) {
		try {
			$file_url = $this->get_file_url( $request );

			return new WP_REST_Response(
				[
					'type'          => 'success',
					'size_in_bytes' => $this->get_file_size( $file_url ),
				],
				200
			);
		} catch ( WPSEO_File_Size_Exception $exception ) {
			return new WP_REST_Response(
				[
					'type'     => 'failure',
					'response' => $exception->getMessage(),
				],
				404
			);
		}
	}

	/**
	 * Retrieves the file url.
	 *
	 * @param WP_REST_Request $request The request to retrieve file url from.
	 *
	 * @return string The file url.
	 * @throws WPSEO_File_Size_Exception The file is hosted externally.
	 */
	protected function get_file_url( WP_REST_Request $request ) {
		$file_url = rawurldecode( $request->get_param( 'url' ) );

		if ( ! $this->is_externally_hosted( $file_url ) ) {
			return $file_url;
		}

		throw WPSEO_File_Size_Exception::externally_hosted( $file_url );
	}

	/**
	 * Checks if the file is hosted externally.
	 *
	 * @param string $file_url The file url.
	 *
	 * @return bool True if it is hosted externally.
	 */
	protected function is_externally_hosted( $file_url ) {
		return wp_parse_url( home_url(), PHP_URL_HOST ) !== wp_parse_url( $file_url, PHP_URL_HOST );
	}

	/**
	 * Returns the file size.
	 *
	 * @param string $file_url The file url to get the size for.
	 *
	 * @return int The file size.
	 * @throws WPSEO_File_Size_Exception Retrieval of file size went wrong for unknown reasons.
	 */
	protected function get_file_size( $file_url ) {
		$file_config = wp_upload_dir();
		$file_url    = str_replace( $file_config['baseurl'], '', $file_url );
		$file_size   = $this->calculate_file_size( $file_url );

		if ( ! $file_size ) {
			throw WPSEO_File_Size_Exception::unknown_error( $file_url );
		}

		return $file_size;
	}

	/**
	 * Calculates the file size using the Utils class.
	 *
	 * @param string $file_url The file to retrieve the size for.
	 *
	 * @return int|bool The file size or False if it could not be retrieved.
	 */
	protected function calculate_file_size( $file_url ) {
		return WPSEO_Image_Utils::get_file_size(
			[
				'path' => $file_url,
			]
		);
	}
}
