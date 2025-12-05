<?php

namespace Elementor\Core\Responsive\Files;

use Elementor\Core\Breakpoints\Breakpoint;
use Elementor\Core\Files\Base;
use Elementor\Core\Responsive\Responsive;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Frontend extends Base {

	const META_KEY = 'elementor-custom-breakpoints-files';

	private $template_file;

	/**
	 * @since 2.1.0
	 * @access public
	 */
	public function __construct( $file_name, $template_file = null ) {
		$this->template_file = $template_file;

		parent::__construct( $file_name );
	}

	/**
	 * @since 2.1.0
	 * @access public
	 */
	public function parse_content() {
		$breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		$breakpoints_keys = array_keys( $breakpoints );

		$file_content = Utils::file_get_contents( $this->template_file );

		// The regex pattern parses placeholders located in the frontend _templates.scss file.
		$file_content = preg_replace_callback( '/ELEMENTOR_SCREEN_([A-Z_]+)(?:_(MIN|MAX|NEXT))/', function ( $placeholder_data ) use ( $breakpoints_keys, $breakpoints ) {
			// Handle BC for legacy template files and Elementor Pro builds.
			$placeholder_data = $this->maybe_convert_placeholder_data( $placeholder_data );

			$breakpoint_index = array_search( strtolower( $placeholder_data[1] ), $breakpoints_keys, true );

			if ( 'DESKTOP' === $placeholder_data[1] ) {
				if ( 'MIN' === $placeholder_data[2] ) {
					$value = Plugin::$instance->breakpoints->get_desktop_min_point();
				} elseif ( isset( $breakpoints['widescreen'] ) ) {
					// If the 'widescreen' breakpoint is active, the Desktop's max value is the Widescreen breakpoint - 1px.
					$value = $breakpoints['widescreen']->get_value() - 1;
				} else {
					// If the 'widescreen' breakpoint is not active, the Desktop device should not have a max value.
					$value = 99999;
				}
			} elseif ( false === $breakpoint_index ) {
				// If the breakpoint in the placeholder is not active - use a -1 value for the media query, to make
				// sure the setting is printed (to avoid a PHP error) but doesn't apply.
				return -1;
			} elseif ( 'WIDESCREEN' === $placeholder_data[1] ) {
				$value = $breakpoints['widescreen']->get_value();
			} else {
				$breakpoint_index = array_search( strtolower( $placeholder_data[1] ), $breakpoints_keys, true );

				$is_max_point = 'MAX' === $placeholder_data[2];

				// If the placeholder capture is `MOBILE_NEXT` or `TABLET_NEXT`, the original breakpoint value is used.
				if ( ! $is_max_point && 'NEXT' !== $placeholder_data[2] ) {
					$breakpoint_index--;
				}

				$value = $breakpoints[ $breakpoints_keys[ $breakpoint_index ] ]->get_value();

				if ( ! $is_max_point ) {
					$value++;
				}
			}

			return $value . 'px';
		}, $file_content );

		return $file_content;
	}

	/**
	 * Load meta.
	 *
	 * Retrieve the file meta data.
	 *
	 * @since 2.1.0
	 * @access protected
	 */
	protected function load_meta() {
		$option = $this->load_meta_option();

		$file_meta_key = $this->get_file_meta_key();

		if ( empty( $option[ $file_meta_key ] ) ) {
			return [];
		}

		return $option[ $file_meta_key ];
	}

	/**
	 * Update meta.
	 *
	 * Update the file meta data.
	 *
	 * @since 2.1.0
	 * @access protected
	 *
	 * @param array $meta New meta data.
	 */
	protected function update_meta( $meta ) {
		$option = $this->load_meta_option();

		$option[ $this->get_file_meta_key() ] = $meta;

		update_option( static::META_KEY, $option );
	}

	/**
	 * Delete meta.
	 *
	 * Delete the file meta data.
	 *
	 * @since 2.1.0
	 * @access protected
	 */
	protected function delete_meta() {
		$option = $this->load_meta_option();

		$file_meta_key = $this->get_file_meta_key();

		if ( isset( $option[ $file_meta_key ] ) ) {
			unset( $option[ $file_meta_key ] );
		}

		if ( $option ) {
			update_option( static::META_KEY, $option );
		} else {
			delete_option( static::META_KEY );
		}
	}

	/**
	 * @since 2.1.0
	 * @access private
	 */
	private function get_file_meta_key() {
		return pathinfo( $this->get_file_name(), PATHINFO_FILENAME );
	}

	/**
	 * @since 2.1.0
	 * @access private
	 */
	private function load_meta_option() {
		$option = get_option( static::META_KEY );

		if ( ! $option ) {
			$option = [];
		}

		return $option;
	}

	/**
	 * Maybe Convert Placeholder Data
	 *
	 * Converts responsive placeholders in Elementor CSS template files from the legacy format into the new format.
	 * Used for backwards compatibility for old Pro versions that were built with an Elementor Core version <3.2.0.
	 *
	 * @since 3.2.3
	 */
	private function maybe_convert_placeholder_data( $placeholder_data ) {
		switch ( $placeholder_data[1] ) {
			case 'SM':
				$placeholder_data[1] = 'MOBILE';
				break;
			case 'MD':
				$placeholder_data[1] = 'TABLET';
				break;
			case 'LG':
				$placeholder_data[1] = 'DESKTOP';
		}

		return $placeholder_data;
	}
}
