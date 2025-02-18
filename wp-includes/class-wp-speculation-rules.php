<?php
/**
 * Class 'WP_Speculation_Rules'.
 *
 * @package WordPress
 * @subpackage Speculative Loading
 * @since 6.8.0
 */

/**
 * Class representing a set of speculation rules.
 *
 * @since 6.8.0
 * @access private
 */
final class WP_Speculation_Rules implements JsonSerializable {

	/**
	 * Stored rules, as a map of `$mode => $rules` pairs.
	 *
	 * Every `$rules` value is a map of `$id => $rule` pairs.
	 *
	 * @since 6.8.0
	 * @var array<string, array<string, mixed>>
	 */
	private $rules_by_mode = array();

	/**
	 * The allowed speculation rules modes as a map, used for validation.
	 *
	 * @since 6.8.0
	 * @var array<string, bool>
	 */
	private static $mode_allowlist = array(
		'prefetch'  => true,
		'prerender' => true,
	);

	/**
	 * The allowed speculation rules eagerness levels as a map, used for validation.
	 *
	 * @since 6.8.0
	 * @var array<string, bool>
	 */
	private static $eagerness_allowlist = array(
		'immediate'    => true,
		'eager'        => true,
		'moderate'     => true,
		'conservative' => true,
	);

	/**
	 * The allowed speculation rules sources as a map, used for validation.
	 *
	 * @since 6.8.0
	 * @var array<string, bool>
	 */
	private static $source_allowlist = array(
		'list'     => true,
		'document' => true,
	);

	/**
	 * Adds a speculation rule to the speculation rules to consider.
	 *
	 * @since 6.8.0
	 *
	 * @param string               $mode Speculative loading mode. Either 'prefetch' or 'prerender'.
	 * @param string               $id   Unique string identifier for the speculation rule.
	 * @param array<string, mixed> $rule Associative array of rule arguments.
	 * @return bool True on success, false if invalid parameters are provided.
	 */
	public function add_rule( string $mode, string $id, array $rule ): bool {
		if ( ! self::is_valid_mode( $mode ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: invalid mode value */
					__( 'The value "%s" is not a valid speculation rules mode.' ),
					esc_html( $mode )
				),
				'6.8.0'
			);
			return false;
		}

		if ( ! $this->is_valid_id( $id ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: invalid ID value */
					__( 'The value "%s" is not a valid ID for a speculation rule.' ),
					esc_html( $id )
				),
				'6.8.0'
			);
			return false;
		}

		if ( $this->has_rule( $mode, $id ) ) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: %s: invalid ID value */
					__( 'A speculation rule with ID "%s" already exists.' ),
					esc_html( $id )
				),
				'6.8.0'
			);
			return false;
		}

		/*
		 * Perform some basic speculation rule validation.
		 * Every rule must have either a 'where' key or a 'urls' key, but not both.
		 * The presence of a 'where' key implies a 'source' of 'document', while the presence of a 'urls' key implies
		 * a 'source' of 'list'.
		 */
		if (
			( ! isset( $rule['where'] ) && ! isset( $rule['urls'] ) ) ||
			( isset( $rule['where'] ) && isset( $rule['urls'] ) )
		) {
			_doing_it_wrong(
				__METHOD__,
				sprintf(
					/* translators: 1: allowed key, 2: alternative allowed key */
					__( 'A speculation rule must include either a "%1$s" key or a "%2$s" key, but not both.' ),
					'where',
					'urls'
				),
				'6.8.0'
			);
			return false;
		}
		if ( isset( $rule['source'] ) ) {
			if ( ! self::is_valid_source( $rule['source'] ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %s: invalid source value */
						__( 'The value "%s" is not a valid source for a speculation rule.' ),
						esc_html( $rule['source'] )
					),
					'6.8.0'
				);
				return false;
			}

			if ( 'list' === $rule['source'] && isset( $rule['where'] ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: 1: source value, 2: forbidden key */
						__( 'A speculation rule of source "%1$s" must not include a "%2$s" key.' ),
						'list',
						'where'
					),
					'6.8.0'
				);
				return false;
			}

			if ( 'document' === $rule['source'] && isset( $rule['urls'] ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: 1: source value, 2: forbidden key */
						__( 'A speculation rule of source "%1$s" must not include a "%2$s" key.' ),
						'document',
						'urls'
					),
					'6.8.0'
				);
				return false;
			}
		}

		// If there is an 'eagerness' key specified, make sure it's valid.
		if ( isset( $rule['eagerness'] ) ) {
			if ( ! self::is_valid_eagerness( $rule['eagerness'] ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %s: invalid eagerness value */
						__( 'The value "%s" is not a valid eagerness for a speculation rule.' ),
						esc_html( $rule['eagerness'] )
					),
					'6.8.0'
				);
				return false;
			}

			if ( isset( $rule['where'] ) && 'immediate' === $rule['eagerness'] ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: %s: forbidden eagerness value */
						__( 'The eagerness value "%s" is forbidden for document-level speculation rules.' ),
						'immediate'
					),
					'6.8.0'
				);
				return false;
			}
		}

		if ( ! isset( $this->rules_by_mode[ $mode ] ) ) {
			$this->rules_by_mode[ $mode ] = array();
		}

		$this->rules_by_mode[ $mode ][ $id ] = $rule;
		return true;
	}

	/**
	 * Checks whether a speculation rule for the given mode and ID already exists.
	 *
	 * @since 6.8.0
	 *
	 * @param string $mode Speculative loading mode. Either 'prefetch' or 'prerender'.
	 * @param string $id   Unique string identifier for the speculation rule.
	 * @return bool True if the rule already exists, false otherwise.
	 */
	public function has_rule( string $mode, string $id ): bool {
		return isset( $this->rules_by_mode[ $mode ][ $id ] );
	}

	/**
	 * Returns the speculation rules data ready to be JSON-encoded.
	 *
	 * @since 6.8.0
	 *
	 * @return array<string, array<string, mixed>> Speculation rules data.
	 */
	#[ReturnTypeWillChange]
	public function jsonSerialize() {
		// Strip the IDs for JSON output, since they are not relevant for the Speculation Rules API.
		return array_map(
			static function ( array $rules ) {
				return array_values( $rules );
			},
			array_filter( $this->rules_by_mode )
		);
	}

	/**
	 * Checks whether the given ID is valid.
	 *
	 * @since 6.8.0
	 *
	 * @param string $id Unique string identifier for the speculation rule.
	 * @return bool True if the ID is valid, false otherwise.
	 */
	private function is_valid_id( string $id ): bool {
		return (bool) preg_match( '/^[a-z][a-z0-9_-]+$/', $id );
	}

	/**
	 * Checks whether the given speculation rules mode is valid.
	 *
	 * @since 6.8.0
	 *
	 * @param string $mode Speculation rules mode.
	 * @return bool True if valid, false otherwise.
	 */
	public static function is_valid_mode( string $mode ): bool {
		return isset( self::$mode_allowlist[ $mode ] );
	}

	/**
	 * Checks whether the given speculation rules eagerness is valid.
	 *
	 * @since 6.8.0
	 *
	 * @param string $eagerness Speculation rules eagerness.
	 * @return bool True if valid, false otherwise.
	 */
	public static function is_valid_eagerness( string $eagerness ): bool {
		return isset( self::$eagerness_allowlist[ $eagerness ] );
	}

	/**
	 * Checks whether the given speculation rules source is valid.
	 *
	 * @since 6.8.0
	 *
	 * @param string $source Speculation rules source.
	 * @return bool True if valid, false otherwise.
	 */
	public static function is_valid_source( string $source ): bool {
		return isset( self::$source_allowlist[ $source ] );
	}
}
