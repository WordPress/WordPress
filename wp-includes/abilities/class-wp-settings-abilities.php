<?php
/**
 * Registers core settings abilities.
 *
 * This is a utility class to encapsulate the registration of settings-related abilities.
 * It is not intended to be instantiated or consumed directly by any other code or plugin.
 *
 * @package WordPress
 * @subpackage Abilities_API
 * @since 7.0.0
 *
 * @internal This class is not part of the public API.
 * @access private
 */

declare( strict_types=1 );

/**
 * Registers core settings abilities.
 *
 * @since 7.0.0
 * @access private
 */
class WP_Settings_Abilities {

	/**
	 * Available setting groups with show_in_abilities enabled.
	 *
	 * @since 7.0.0
	 * @var string[]
	 */
	private static $available_groups;

	/**
	 * Dynamic output schema built from registered settings.
	 *
	 * @since 7.0.0
	 * @var array
	 */
	private static $output_schema;

	/**
	 * Available setting slugs with show_in_abilities enabled.
	 *
	 * @since 7.0.0
	 * @var string[]
	 */
	private static $available_slugs;

	/**
	 * Registers all settings abilities.
	 *
	 * @since 7.0.0
	 *
	 * @return void
	 */
	public static function register(): void {
		self::init();
		self::register_get_settings();
	}

	/**
	 * Initializes shared data for settings abilities.
	 *
	 * @since 7.0.0
	 *
	 * @return void
	 */
	private static function init(): void {
		self::$available_groups = self::get_available_groups();
		self::$available_slugs  = self::get_available_slugs();
		self::$output_schema    = self::build_output_schema();
	}

	/**
	 * Gets registered settings that have show_in_abilities enabled.
	 *
	 * @since 7.0.0
	 *
	 * @return array Associative array of option_name => args for allowed settings.
	 */
	private static function get_allowed_settings(): array {
		$settings = array();

		foreach ( get_registered_settings() as $option_name => $args ) {
			if ( ! empty( $args['show_in_abilities'] ) ) {
				$settings[ $option_name ] = $args;
			}
		}

		return $settings;
	}

	/**
	 * Gets unique setting groups that have show_in_abilities enabled.
	 *
	 * @since 7.0.0
	 *
	 * @return string[] List of unique group names.
	 */
	private static function get_available_groups(): array {
		$groups = array();

		foreach ( self::get_allowed_settings() as $args ) {
			$group = $args['group'] ?? 'general';
			if ( ! in_array( $group, $groups, true ) ) {
				$groups[] = $group;
			}
		}

		sort( $groups );

		return $groups;
	}

	/**
	 * Gets unique setting slugs that have show_in_abilities enabled.
	 *
	 * @since 7.0.0
	 *
	 * @return string[] List of unique setting slugs.
	 */
	private static function get_available_slugs(): array {
		$slugs = array();

		foreach ( self::get_allowed_settings() as $option_name => $args ) {
			$slugs[] = $option_name;
		}

		sort( $slugs );

		return $slugs;
	}

	/**
	 * Builds a rich output schema from registered settings metadata.
	 *
	 * Creates a JSON Schema that documents each setting group and its settings
	 * with their types, titles, descriptions, defaults, and any additional
	 * schema properties from show_in_rest.
	 *
	 * @since 7.0.0
	 *
	 * @return array JSON Schema for the output.
	 */
	private static function build_output_schema(): array {
		$group_properties = array();

		foreach ( self::get_allowed_settings() as $option_name => $args ) {
			$group = $args['group'] ?? 'general';

			$setting_schema = array(
				'type' => $args['type'] ?? 'string',
			);

			if ( ! empty( $args['label'] ) ) {
				$setting_schema['title'] = $args['label'];
			}

			if ( ! empty( $args['description'] ) ) {
				$setting_schema['description'] = $args['description'];
			} elseif ( ! empty( $args['label'] ) ) {
				$setting_schema['description'] = $args['label'];
			}

			if ( ! isset( $group_properties[ $group ] ) ) {
				$group_properties[ $group ] = array(
					'type'                 => 'object',
					'properties'           => array(),
					'additionalProperties' => false,
				);
			}

			$group_properties[ $group ]['properties'][ $option_name ] = $setting_schema;
		}

		ksort( $group_properties );

		return array(
			'type'                 => 'object',
			'description'          => __( 'Settings grouped by registration group. Each group contains settings with their current values.' ),
			'properties'           => $group_properties,
			'additionalProperties' => false,
		);
	}

	/**
	 * Registers the core/get-settings ability.
	 *
	 * @since 7.0.0
	 *
	 * @return void
	 */
	private static function register_get_settings(): void {
		wp_register_ability(
			'core/get-settings',
			array(
				'label'               => __( 'Get Settings' ),
				'description'         => __( 'Returns registered WordPress settings grouped by their registration group. Returns key-value pairs per setting.' ),
				'category'            => 'site',
				'input_schema'        => array(
					'default' => (object) array(),
					'oneOf'   => array(
						// Branch 1: No filter (empty object).
						array(
							'type'                 => 'object',
							'additionalProperties' => false,
							'maxProperties'        => 0,
						),
						// Branch 2: Filter by group only.
						array(
							'type'                 => 'object',
							'properties'           => array(
								'group' => array(
									'type'        => 'string',
									'description' => __( 'Filter settings by group name.' ),
									'enum'        => self::$available_groups,
								),
							),
							'required'             => array( 'group' ),
							'additionalProperties' => false,
						),
						// Branch 3: Filter by slugs only.
						array(
							'type'                 => 'object',
							'properties'           => array(
								'slugs' => array(
									'type'        => 'array',
									'description' => __( 'Filter settings by specific setting slugs.' ),
									'items'       => array(
										'type' => 'string',
										'enum' => self::$available_slugs,
									),
								),
							),
							'required'             => array( 'slugs' ),
							'additionalProperties' => false,
						),
					),
				),
				'output_schema'       => self::$output_schema,
				'execute_callback'    => array( __CLASS__, 'execute_get_settings' ),
				'permission_callback' => array( __CLASS__, 'check_manage_options' ),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Permission callback for settings abilities.
	 *
	 * @since 7.0.0
	 *
	 * @return bool True if the current user can manage options, false otherwise.
	 */
	public static function check_manage_options(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Execute callback for core/get-settings ability.
	 *
	 * Retrieves all registered settings that are exposed through the Abilities API,
	 * grouped by their registration group.
	 *
	 * @since 7.0.0
	 *
	 * @param array $input {
	 *     Optional. Input parameters.
	 *
	 *     @type string   $group Optional. Filter settings by group name. Cannot be used with slugs.
	 *     @type string[] $slugs Optional. Filter settings by specific setting slugs. Cannot be used with group.
	 * }
	 * @return array Settings grouped by registration group.
	 */
	public static function execute_get_settings( $input = array() ): array {
		$input        = is_array( $input ) ? $input : array();
		$filter_group = ! empty( $input['group'] ) ? $input['group'] : null;
		$filter_slugs = ! empty( $input['slugs'] ) ? $input['slugs'] : null;

		$settings_by_group = array();

		foreach ( self::get_allowed_settings() as $option_name => $args ) {
			$group = $args['group'] ?? 'general';

			if ( $filter_group && $group !== $filter_group ) {
				continue;
			}

			if ( $filter_slugs && ! in_array( $option_name, $filter_slugs, true ) ) {
				continue;
			}

			$default = $args['default'] ?? null;

			$value = get_option( $option_name, $default );
			$value = self::cast_value( $value, $args['type'] ?? 'string' );

			if ( ! isset( $settings_by_group[ $group ] ) ) {
				$settings_by_group[ $group ] = array();
			}

			$settings_by_group[ $group ][ $option_name ] = $value;
		}

		ksort( $settings_by_group );

		return $settings_by_group;
	}

	/**
	 * Casts a value to the appropriate type based on the setting's registered type.
	 *
	 * @since 7.0.0
	 *
	 * @param mixed  $value The value to cast.
	 * @param string $type  The registered type (string, boolean, integer, number, array, object).
	 * @return string|bool|int|float|array The cast value.
	 */
	private static function cast_value( $value, string $type ) {
		switch ( $type ) {
			case 'boolean':
				return (bool) $value;
			case 'integer':
				return (int) $value;
			case 'number':
				return (float) $value;
			case 'array':
			case 'object':
				return is_array( $value ) ? $value : array();
			case 'string':
			default:
				return (string) $value;
		}
	}
}
