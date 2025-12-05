<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Export
 */

/**
 * Class WPSEO_Export.
 *
 * Class with functionality to export the WP SEO settings.
 */
class WPSEO_Export {

	/**
	 * Holds the nonce action.
	 *
	 * @var string
	 */
	public const NONCE_ACTION = 'wpseo_export';

	/**
	 * Holds the export data.
	 *
	 * @var string
	 */
	private $export = '';

	/**
	 * Holds whether the export was a success.
	 *
	 * @var bool
	 */
	public $success;

	/**
	 * Handles the export request.
	 *
	 * @return void
	 */
	public function export() {
		check_admin_referer( self::NONCE_ACTION );
		$this->export_settings();
		$this->output();
	}

	/**
	 * Outputs the export.
	 *
	 * @return void
	 */
	public function output() {
		if ( ! WPSEO_Capability_Utils::current_user_can( 'wpseo_manage_options' ) ) {
			esc_html_e( 'You do not have the required rights to export settings.', 'wordpress-seo' );
			return;
		}

		echo '<p id="wpseo-settings-export-desc">';
		printf(
			/* translators: %1$s expands to Import settings */
			esc_html__(
				'Copy all these settings to another site\'s %1$s tab and click "%1$s" there.',
				'wordpress-seo'
			),
			esc_html__(
				'Import settings',
				'wordpress-seo'
			)
		);
		echo '</p>';
		/* translators: %1$s expands to Yoast SEO */
		echo '<label for="wpseo-settings-export" class="yoast-inline-label">' . sprintf( __( 'Your %1$s settings:', 'wordpress-seo' ), 'Yoast SEO' ) . '</label><br />';
		echo '<textarea id="wpseo-settings-export" rows="20" cols="100" aria-describedby="wpseo-settings-export-desc">' . esc_textarea( $this->export ) . '</textarea>';
	}

	/**
	 * Exports the current site's WP SEO settings.
	 *
	 * @return void
	 */
	private function export_settings() {
		$this->export_header();

		foreach ( WPSEO_Options::get_option_names() as $opt_group ) {
			$this->write_opt_group( $opt_group );
		}
	}

	/**
	 * Writes the header of the export.
	 *
	 * @return void
	 */
	private function export_header() {
		$header = sprintf(
			/* translators: %1$s expands to Yoast SEO, %2$s expands to Yoast.com */
			esc_html__( 'These are settings for the %1$s plugin by %2$s', 'wordpress-seo' ),
			'Yoast SEO',
			'Yoast.com'
		);
		$this->write_line( '; ' . $header );
	}

	/**
	 * Writes a line to the export.
	 *
	 * @param string $line          Line string.
	 * @param bool   $newline_first Boolean flag whether to prepend with new line.
	 *
	 * @return void
	 */
	private function write_line( $line, $newline_first = false ) {
		if ( $newline_first ) {
			$this->export .= PHP_EOL;
		}
		$this->export .= $line . PHP_EOL;
	}

	/**
	 * Writes an entire option group to the export.
	 *
	 * @param string $opt_group Option group name.
	 *
	 * @return void
	 */
	private function write_opt_group( $opt_group ) {

		$this->write_line( '[' . $opt_group . ']', true );

		$options = get_option( $opt_group );

		if ( ! is_array( $options ) ) {
			return;
		}

		foreach ( $options as $key => $elem ) {
			if ( is_array( $elem ) ) {
				$count = count( $elem );
				for ( $i = 0; $i < $count; $i++ ) {
					$elem_check = ( $elem[ $i ] ?? null );
					$this->write_setting( $key . '[]', $elem_check );
				}
			}
			else {
				$this->write_setting( $key, $elem );
			}
		}
	}

	/**
	 * Writes a settings line to the export.
	 *
	 * @param string $key Key string.
	 * @param string $val Value string.
	 *
	 * @return void
	 */
	private function write_setting( $key, $val ) {
		if ( is_string( $val ) ) {
			$val = '"' . $val . '"';
		}
		$this->write_line( $key . ' = ' . $val );
	}
}
