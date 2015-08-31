<?php
/**
 * @package WPSEO\Admin\Export
 */

/**
 * Class WPSEO_Export
 *
 * Class with functionality to export the WP SEO settings
 */
class WPSEO_Export {

	/**
	 * @var string
	 */
	private $export = '';

	/**
	 * @var string
	 */
	private $error = '';

	/**
	 * @var string
	 */
	public $export_zip_url = '';

	/**
	 * @var boolean
	 */
	public $success;

	/**
	 * Whether or not the export will include taxonomy metadata
	 *
	 * @var boolean
	 */
	private $include_taxonomy;

	/**
	 * @var array
	 */
	private $dir = array();


	/**
	 * Class constructor
	 *
	 * @param boolean $include_taxonomy Whether to include the taxonomy metadata the plugin creates.
	 */
	public function __construct( $include_taxonomy = false ) {
		$this->include_taxonomy = $include_taxonomy;
		$this->dir              = wp_upload_dir();
		$this->success          = $this->export_settings();
	}

	/**
	 * Returns an array with status and output message.
	 *
	 * @return array $results
	 */
	public function get_results() {
		$results = array();
		if ( $this->success ) {
			$results['status'] = 'success';
			$results['msg']    = sprintf( __( 'Export created: %1$sdownload your export file here%2$s.', 'wordpress-seo' ), '<a href="' . $this->export_zip_url . '">', '</a>' );
		}
		else {
			$results['status'] = 'failure';
			/* translators: %1$s expands to Yoast SEO */
			$results['msg']    = sprintf( __( 'Error creating %1$s export: ', 'wordpress-seo' ), 'Yoast SEO' ) . $this->error;
		}

		return $results;
	}

	/**
	 * Exports the current site's WP SEO settings.
	 *
	 * @return boolean|string $return true when success, error when failed.
	 */
	private function export_settings() {

		$this->export_header();

		foreach ( WPSEO_Options::get_option_names() as $opt_group ) {
			$this->write_opt_group( $opt_group, $this->export );
		}

		$this->taxonomy_metadata();

		if ( $this->write_file() ) {
			if ( $this->zip_file() ) {
				return true;
			}
			else {
				$this->error = __( 'Could not zip settings-file.', 'wordpress-seo' );

				return false;
			}
		}
		$this->error = __( 'Could not write settings to file.', 'wordpress-seo' );

		return false;
	}

	/**
	 * Writes the header of the export file.
	 */
	private function export_header() {
		/* translators: %1$s expands to Yoast SEO */
		$this->write_line( '; ' . sprintf( __( 'This is a settings export file for the %1$s plugin by Yoast.com', 'wordpress-seo' ), 'Yoast SEO' ) . ' - https://yoast.com/wordpress/plugins/seo/' );
		if ( $this->include_taxonomy ) {
			$this->write_line( '; ' . __( 'This export includes taxonomy metadata', 'wordpress-seo' ) );
		}
	}

	/**
	 * Writes a line to the export
	 *
	 * @param string  $line
	 * @param boolean $newline_first
	 */
	private function write_line( $line, $newline_first = false ) {
		if ( $newline_first ) {
			$this->export .= PHP_EOL;
		}
		$this->export .= $line . PHP_EOL;
	}

	/**
	 * Writes an entire option group to the export
	 *
	 * @param string $opt_group
	 */
	private function write_opt_group( $opt_group ) {
		$this->write_line( '[' . $opt_group . ']', true );

		$options = get_option( $opt_group );

		if ( ! is_array( $options ) ) {
			return;
		}

		foreach ( $options as $key => $elem ) {
			if ( is_array( $elem ) ) {
				for ( $i = 0; $i < count( $elem ); $i ++ ) {
					$this->write_setting( $key . '[]', $elem[ $i ] );
				}
			}
			else {
				$this->write_setting( $key, $elem );
			}
		}
	}

	/**
	 * Writes a settings line to the export
	 *
	 * @param string $key
	 * @param string $val
	 */
	private function write_setting( $key, $val ) {
		if ( is_string( $val ) ) {
			$val = '"' . $val . '"';
		}
		$this->write_line( $key . ' = ' . $val );
	}

	/**
	 * Adds the taxonomy meta data if there is any
	 */
	private function taxonomy_metadata() {
		if ( $this->include_taxonomy ) {
			$taxonomy_meta = get_option( 'wpseo_taxonomy_meta' );
			if ( is_array( $taxonomy_meta ) ) {
				$this->write_line( '[wpseo_taxonomy_meta]', true );
				$this->write_setting( 'wpseo_taxonomy_meta', urlencode( json_encode( $taxonomy_meta ) ) );
			}
			else {
				$this->write_line( '; ' . __( 'No taxonomy metadata found', 'wordpress-seo' ), true );
			}
		}
	}

	/**
	 * Writes the settings to our temporary settings.ini file
	 *
	 * @return boolean unsigned
	 */
	private function write_file() {
		$handle = fopen( $this->dir['path'] . '/settings.ini', 'w' );
		if ( ! $handle ) {
			return false;
		}

		$res = fwrite( $handle, $this->export );
		if ( ! $res ) {
			return false;
		}

		fclose( $handle );

		return true;
	}

	/**
	 * Zips the settings ini file
	 *
	 * @return boolean unsigned
	 */
	private function zip_file() {
		chdir( $this->dir['path'] );
		$zip = new PclZip( './settings.zip' );
		if ( 0 === $zip->create( './settings.ini' ) ) {
			return false;
		}

		$this->export_zip_url = $this->dir['url'] . '/settings.zip';

		return true;
	}

}
