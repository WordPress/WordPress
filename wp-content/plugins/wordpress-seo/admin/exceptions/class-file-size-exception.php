<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Exceptions
 */

/**
 * Represents named methods for exceptions.
 */
class WPSEO_File_Size_Exception extends Exception {

	/**
	 * Gets the exception for an externally hosted file.
	 *
	 * @param string $file_url The file url.
	 *
	 * @return WPSEO_File_Size_Exception Instance of the exception.
	 */
	public static function externally_hosted( $file_url ) {
		$message = sprintf(
			/* translators: %1$s expands to the requested url */
			__( 'Cannot get the size of %1$s because it is hosted externally.', 'wordpress-seo' ),
			$file_url
		);

		return new self( $message );
	}

	/**
	 * Gets the exception for when a unknown error occurs.
	 *
	 * @param string $file_url The file url.
	 *
	 * @return WPSEO_File_Size_Exception Instance of the exception.
	 */
	public static function unknown_error( $file_url ) {
		$message = sprintf(
			/* translators: %1$s expands to the requested url */
			__( 'Cannot get the size of %1$s because of unknown reasons.', 'wordpress-seo' ),
			$file_url
		);

		return new self( $message );
	}
}
