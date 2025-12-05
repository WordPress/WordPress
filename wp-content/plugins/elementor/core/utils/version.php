<?php
namespace Elementor\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Version {
	const PART_MAJOR_1 = 'major1';
	const PART_MAJOR_2 = 'major2';
	const PART_PATCH = 'patch';
	const PART_STAGE = 'stage';

	/**
	 * First number of a version 0.x.x
	 *
	 * @var string
	 */
	public $major1;

	/**
	 * Second number of a version x.0.x
	 *
	 * @var string
	 */
	public $major2;

	/**
	 * Third number of a version x.x.0
	 *
	 * @var string
	 */
	public $patch;

	/**
	 * The stage of a version x.x.x-stage.
	 * e.g: x.x.x-dev1, x.x.x-beta3, x.x.x-rc
	 *
	 * @var string|null
	 */
	public $stage;

	/**
	 * Version constructor.
	 *
	 * @param $major1
	 * @param $major2
	 * @param $patch
	 * @param $stage
	 */
	public function __construct( $major1, $major2, $patch, $stage = null ) {
		$this->major1 = $major1;
		$this->major2 = $major2;
		$this->patch  = $patch;
		$this->stage  = $stage;
	}

	/**
	 * Create Version instance.
	 *
	 * @param string $major1
	 * @param string $major2
	 * @param string $patch
	 * @param null   $stage
	 *
	 * @return static
	 */
	public static function create( $major1 = '0', $major2 = '0', $patch = '0', $stage = null ) {
		return new static( $major1, $major2, $patch, $stage );
	}

	/**
	 * Checks if the current version string is valid.
	 *
	 * @param $version
	 *
	 * @return bool
	 */
	public static function is_valid_version( $version ) {
		return (bool) preg_match( '/^(\d+\.)?(\d+\.)?(\*|\d+)(-.+)?$/', $version );
	}

	/**
	 * Creates a Version instance from a string.
	 *
	 * @param      $version
	 * @param bool $should_validate
	 *
	 * @return static
	 * @throws \Exception If version comparison fails or invalid version format is provided.
	 */
	public static function create_from_string( $version, $should_validate = true ) {
		if ( $should_validate && ! static::is_valid_version( $version ) ) {
			throw new \Exception( sprintf( '%s is an invalid version.', esc_html( $version ) ) );
		}

		$parts = explode( '.', $version );
		$patch_parts = [];

		$major1 = '0';
		$major2 = '0';
		$patch = '0';
		$stage = null;

		if ( isset( $parts[0] ) ) {
			$major1 = $parts[0];
		}

		if ( isset( $parts[1] ) ) {
			$major2 = $parts[1];
		}

		if ( isset( $parts[2] ) ) {
			$patch_parts = explode( '-', $parts[2] );

			$patch = $patch_parts[0];
		}

		if ( isset( $patch_parts[1] ) ) {
			$stage = $patch_parts[1];
		}

		return static::create( $major1, $major2, $patch, $stage );
	}

	/**
	 * Compare the current version instance with another version.
	 *
	 * @param        $operator
	 * @param        $version
	 * @param string $part
	 *
	 * @return bool
	 * @throws \Exception If version validation fails or parsing errors occur.
	 */
	public function compare( $operator, $version, $part = self::PART_STAGE ) {
		if ( ! ( $version instanceof Version ) ) {
			if ( ! static::is_valid_version( $version ) ) {
				$version = '0.0.0';
			}

			$version = static::create_from_string( $version, false );
		}

		$current_version = clone $this;
		$compare_version = clone $version;

		if ( in_array( $part, [ self::PART_PATCH, self::PART_MAJOR_2, self::PART_MAJOR_1 ], true ) ) {
			$current_version->stage = null;
			$compare_version->stage = null;
		}

		if ( in_array( $part, [ self::PART_MAJOR_2, self::PART_MAJOR_1 ], true ) ) {
			$current_version->patch = '0';
			$compare_version->patch = '0';
		}

		if ( self::PART_MAJOR_1 === $part ) {
			$current_version->major2 = '0';
			$compare_version->major2 = '0';
		}

		return version_compare(
			$current_version,
			$compare_version,
			$operator
		);
	}

	/**
	 * Implode the version and return it as string.
	 *
	 * @return string
	 */
	public function __toString() {
		$version = implode( '.', [ $this->major1, $this->major2, $this->patch ] );

		if ( $this->stage ) {
			$version .= '-' . $this->stage;
		}

		return $version;
	}
}
