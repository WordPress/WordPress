<?php
namespace Elementor\Modules\DevTools;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Deprecation {
	const SOFT_VERSIONS_COUNT = 4;
	const HARD_VERSIONS_COUNT = 8;

	private $current_version = null;
	private $soft_deprecated_notices = [];

	public function __construct( $current_version ) {
		$this->current_version = $current_version;
	}

	public function get_settings() {
		return [
			'soft_notices' => $this->soft_deprecated_notices,
			'soft_version_count' => self::SOFT_VERSIONS_COUNT,
			'hard_version_count' => self::HARD_VERSIONS_COUNT,
			'current_version' => ELEMENTOR_VERSION,
		];
	}

	/**
	 * Get total of major.
	 *
	 * Since `get_total_major` cannot determine how much really versions between 2.9.0 and 3.3.0 if there is 2.10.0 version for example,
	 * versions with major2 more then 9 will be added to total.
	 *
	 * @since 3.1.0
	 *
	 * @param array $parsed_version
	 *
	 * @return int
	 */
	public function get_total_major( $parsed_version ) {
		$major1 = $parsed_version['major1'];
		$major2 = $parsed_version['major2'];
		$major2 = $major2 > 9 ? 9 : $major2;
		$minor = 0;

		$total = intval( "{$major1}{$major2}{$minor}" );

		if ( $total > 99 ) {
			$total = $total / 10;
		} else {
			$total = intval( $total / 10 );
		}

		if ( $parsed_version['major2'] > 9 ) {
			$total += $parsed_version['major2'] - 9;
		}

		return $total;
	}

	/**
	 * Get next version.
	 *
	 * @since 3.1.0
	 *
	 * @param string $version
	 * @param int    $count
	 *
	 * @return string|false
	 */
	public function get_next_version( $version, $count = 1 ) {
		$version = $this->parse_version( $version );

		if ( ! $version ) {
			return false;
		}

		$version['total'] = $this->get_total_major( $version ) + $count;

		$total = $version['total'];

		if ( $total > 9 ) {
			$version['major1'] = intval( $total / 10 );
			$version['major2'] = $total % 10;
		} else {
			$version['major1'] = 0;
			$version['major2'] = $total;
		}

		$version['minor'] = 0;

		return $this->implode_version( $version );
	}

	/**
	 * Implode parsed version to string version.
	 *
	 * @since 3.1.0
	 *
	 * @param array $parsed_version
	 *
	 * @return string
	 */
	public function implode_version( $parsed_version ) {
		$major1 = $parsed_version['major1'];
		$major2 = $parsed_version['major2'];
		$minor = $parsed_version['minor'];

		return "{$major1}.{$major2}.{$minor}";
	}

	/**
	 * Parse to an informative array.
	 *
	 * @since 3.1.0
	 *
	 * @param string $version
	 *
	 * @return array|false
	 */
	public function parse_version( $version ) {
		$version_explode = explode( '.', $version );
		$version_explode_count = count( $version_explode );

		if ( $version_explode_count < 3 || $version_explode_count > 4 ) {
			trigger_error( 'Invalid Semantic Version string provided' );

			return false;
		}

		list( $major1, $major2, $minor ) = $version_explode;

		$result = [
			'major1' => intval( $major1 ),
			'major2' => intval( $major2 ),
			'minor' => intval( $minor ),
		];

		if ( $version_explode_count > 3 ) {
			$result['build'] = $version_explode[3];
		}

		return $result;
	}

	/**
	 * Compare two versions, result is equal to diff of major versions.
	 * Notice: If you want to compare between 2.9.0 and 3.3.0, and there is also a 2.10.0 version, you cannot get the right comparison
	 * Since $this->deprecation->get_total_major cannot determine how much really versions between 2.9.0 and 3.3.0.
	 *
	 * @since 3.1.0
	 *
	 * @param {string} $version1
	 * @param {string} $version2
	 *
	 * @return int|false
	 */
	public function compare_version( $version1, $version2 ) {
		$version1 = self::parse_version( $version1 );
		$version2 = self::parse_version( $version2 );

		if ( $version1 && $version2 ) {
			$versions = [ &$version1, &$version2 ];

			foreach ( $versions as &$version ) {
				$version['total'] = self::get_total_major( $version );
			}

			return $version1['total'] - $version2['total'];
		}

		return false;
	}

	/**
	 * Check Deprecation
	 *
	 * Checks whether the given entity is valid. If valid, this method checks whether the deprecation
	 * should be soft (browser console notice) or hard (use WordPress' native deprecation methods).
	 *
	 * @since 3.1.0
	 *
	 * @param string $entity - The Deprecated entity (the function/hook itself)
	 * @param string $version
	 * @param string $replacement Optional
	 * @param string $base_version Optional. Default is `null`
	 *
	 * @return bool
	 * @throws \Exception Invalid deprecation.
	 */
	private function check_deprecation( $entity, $version, $replacement, $base_version = null ) {
		if ( null === $base_version ) {
			$base_version = $this->current_version;
		}

		$diff = $this->compare_version( $base_version, $version );

		if ( false === $diff ) {
			throw new \Exception( 'Invalid deprecation diff.' );
		}

		$print_deprecated = false;

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && $diff <= self::SOFT_VERSIONS_COUNT ) {
			// Soft deprecated.
			if ( ! isset( $this->soft_deprecated_notices[ $entity ] ) ) {
				$this->soft_deprecated_notices[ $entity ] = [
					$version,
					$replacement,
				];
			}

			if ( Utils::is_elementor_debug() ) {
				$print_deprecated = true;
			}
		}

		return $print_deprecated;
	}

	/**
	 * Deprecated Function
	 *
	 * Handles the deprecation process for functions.
	 *
	 * @since 3.1.0
	 *
	 * @param string $function_name
	 * @param string $version
	 * @param string $replacement   Optional. Default is ''
	 * @param string $base_version  Optional. Default is `null`
	 * @throws \Exception Deprecation error.
	 */
	public function deprecated_function( $function_name, $version, $replacement = '', $base_version = null ) {
		$print_deprecated = $this->check_deprecation( $function_name, $version, $replacement, $base_version );

		if ( $print_deprecated ) {
			// PHPCS - We need to echo special characters because they can exist in function calls.
			_deprecated_function( $function_name, esc_html( $version ), $replacement );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Deprecated Hook
	 *
	 * Handles the deprecation process for hooks.
	 *
	 * @param string $hook
	 * @param string $version
	 * @param string $replacement Optional. Default is ''
	 * @param string $base_version Optional. Default is `null`
	 * @throws \Exception Deprecation error.
	 * @since 3.1.0
	 */
	public function deprecated_hook( $hook, $version, $replacement = '', $base_version = null ) {
		$print_deprecated = $this->check_deprecation( $hook, $version, $replacement, $base_version );

		if ( $print_deprecated ) {
			_deprecated_hook( esc_html( $hook ), esc_html( $version ), esc_html( $replacement ) );
		}
	}

	/**
	 * Deprecated Argument
	 *
	 * Handles the deprecation process for function arguments.
	 *
	 * @since 3.1.0
	 *
	 * @param string $argument
	 * @param string $version
	 * @param string $replacement
	 * @param string $message
	 * @throws \Exception Deprecation error.
	 */
	public function deprecated_argument( $argument, $version, $replacement = '', $message = '' ) {
		$print_deprecated = $this->check_deprecation( $argument, $version, $replacement );

		if ( $print_deprecated ) {
			$message = empty( $message ) ? '' : ' ' . $message;
			// These arguments are escaped because they are printed later, and are not escaped when printed.
			$error_message_args = [ esc_html( $argument ), esc_html( $version ) ];

			if ( $replacement ) {
				/* translators: 1: Function argument, 2: Elementor version number, 3: Replacement argument name. */
				$translation_string = esc_html__( 'The %1$s argument is deprecated since version %2$s! Use %3$s instead.', 'elementor' );
				$error_message_args[] = $replacement;
			} else {
				/* translators: 1: Function argument, 2: Elementor version number. */
				$translation_string = esc_html__( 'The %1$s argument is deprecated since version %2$s!', 'elementor' );
			}

			trigger_error(
				vsprintf(
					// PHPCS - $translation_string is already escaped above.
					$translation_string,  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					// PHPCS - $error_message_args is an array.
					$error_message_args  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				) . esc_html( $message ),
				E_USER_DEPRECATED
			);
		}
	}

	/**
	 * Do Deprecated Action
	 *
	 * A method used to run deprecated actions through Elementor's deprecation process.
	 *
	 * @param string      $hook
	 * @param array       $args
	 * @param string      $version
	 * @param string      $replacement
	 * @param null|string $base_version
	 *
	 * @throws \Exception Deprecation error.
	 * @since 3.1.0
	 */
	public function do_deprecated_action( $hook, $args, $version, $replacement = '', $base_version = null ) {
		if ( ! has_action( $hook ) ) {
			return;
		}

		$this->deprecated_hook( $hook, $version, $replacement, $base_version );

		do_action_ref_array( $hook, $args );
	}

	/**
	 * Apply Deprecated Filter
	 *
	 * A method used to run deprecated filters through Elementor's deprecation process.
	 *
	 * @param string      $hook
	 * @param array       $args
	 * @param string      $version
	 * @param string      $replacement
	 * @param null|string $base_version
	 *
	 * @return mixed
	 * @throws \Exception Deprecation error.
	 * @since 3.2.0
	 */
	public function apply_deprecated_filter( $hook, $args, $version, $replacement = '', $base_version = null ) {
		if ( ! has_action( $hook ) ) {
			// `$args` should be an array, but in order to keep BC, we need to support non-array values.
			if ( is_array( $args ) ) {
				return $args[0] ?? null;
			}

			return $args;
		}

		// BC - See the comment above.
		if ( ! is_array( $args ) ) {
			$args = [ $args ];
		}

		// Avoid associative arrays.
		$args = array_values( $args );

		$this->deprecated_hook( $hook, $version, $replacement, $base_version );

		return apply_filters_ref_array( $hook, $args );
	}
}
