<?php
/**
 * Abilities API: WP_Abilities_Settings class.
 *
 * @package WordPress
 * @subpackage Abilities API
 * @since 7.2.0
 */

declare( strict_types = 1 );

/**
 * Core class used to register settings-related abilities.
 *
 * Provides the read-only `core/settings-get` ability and the shared building blocks
 * (exposed-settings discovery and schema generation) that are intended to also back a
 * future write-oriented `core/settings-update` ability.
 *
 * Unlike the other core abilities, which are self-contained closures registered directly
 * in wp_register_core_abilities(), the settings abilities live in a dedicated class
 * because they share state: the set of exposed settings is computed once at registration
 * and reused by the input schema, the output schema, and the execute callback, and the
 * same helpers are meant to be shared with the future write ability.
 *
 * The exposed settings are captured when the ability registers, the first time the abilities
 * registry is used in a request. Settings registered later in that request are not exposed.
 * Core registers its own settings in time, see _wp_register_initial_settings_for_abilities().
 *
 * Only register() is public. The ability callbacks are closures that call private
 * methods, so callers go through the Abilities API, such as
 * `wp_get_ability( 'core/settings-get' )->execute()`, which validates the input and
 * checks permissions before running them.
 *
 * @since 7.2.0
 *
 * @access private
 */
final class WP_Abilities_Settings {

	/**
	 * The ability category used for settings abilities.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	private const CATEGORY = 'site';

	/**
	 * Settings exposed through the Abilities API, computed once at registration.
	 *
	 * @since 7.2.0
	 * @var array<string, array{option: string, group: string, default: mixed, schema: array<string, mixed>}>
	 */
	private $exposed_settings = array();

	/**
	 * Registers all settings abilities.
	 *
	 * Must run on the `wp_abilities_api_init` hook. Registers nothing when no setting is
	 * exposed to abilities.
	 *
	 * @since 7.2.0
	 */
	public function register(): void {
		$this->exposed_settings = $this->get_exposed_settings();
		if ( empty( $this->exposed_settings ) ) {
			return;
		}

		$this->register_settings_get();
	}

	/**
	 * Registers the read-only `core/settings-get` ability.
	 *
	 * @since 7.2.0
	 */
	private function register_settings_get(): void {
		$groups = array_values( array_unique( array_filter( array_column( $this->exposed_settings, 'group' ) ) ) );

		wp_register_ability(
			'core/settings-get',
			array(
				'label'               => __( 'Get Settings' ),
				'description'         => __( 'Returns WordPress settings as a flat map of setting name to value. By default returns all settings exposed to abilities, or optionally a subset filtered by settings group, by setting name, or both. A setting whose value does not match its schema is left out. Requires an authenticated user who can manage options.' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->get_settings_get_input_schema( $groups, array_map( 'strval', array_keys( $this->exposed_settings ) ) ),
				'output_schema'       => $this->get_settings_get_output_schema(),
				'execute_callback'    => function ( $input = array() ): array {
					return $this->execute_settings_get( $input );
				},
				'permission_callback' => function (): bool {
					return $this->check_permission();
				},
				'meta'                => array(
					'annotations' => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'public'      => true,
				),
			)
		);
	}

	/**
	 * Executes the `core/settings-get` ability.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $input Optional. The ability input. Default empty array.
	 * @return array<string, mixed> Map of exposed setting name to current value.
	 */
	private function execute_settings_get( $input = array() ): array {
		$input  = rest_sanitize_object( $input );
		$group  = isset( $input['group'] ) && is_string( $input['group'] ) ? $input['group'] : '';
		$fields = rest_sanitize_array( $input['fields'] ?? array() );

		$result = array();
		foreach ( $this->exposed_settings as $exposed_name => $setting ) {
			if ( '' !== $group && $setting['group'] !== $group ) {
				continue;
			}
			if ( ! empty( $fields ) && ! in_array( (string) $exposed_name, $fields, true ) ) {
				continue;
			}

			/*
			 * As the settings endpoint does, read a setting that was never saved as its registered
			 * default, or leave it out when it has none: get_option() would return false, which a
			 * boolean schema accepts as a value and an array schema reads as an empty list.
			 */
			$value = get_option( $setting['option'], $setting['default'] );
			if ( null === $value ) {
				continue;
			}

			// WordPress stores false as '', which the boolean schema rejects.
			if ( '' === $value && 'boolean' === $setting['schema']['type'] ) {
				$value = false;
			}

			/*
			 * Leave out a value its schema rejects, before sanitizing (which could make it pass) or
			 * after (which could make it fail), instead of failing output validation for every setting.
			 */
			if ( is_wp_error( rest_validate_value_from_schema( $value, $setting['schema'] ) ) ) {
				continue;
			}

			$value = rest_sanitize_value_from_schema( $value, $setting['schema'] );
			if ( is_wp_error( rest_validate_value_from_schema( $value, $setting['schema'] ) ) ) {
				continue;
			}

			// Object (not array()) so an empty object value is serialized as {}, consistent with type:object.
			$result[ $exposed_name ] = 'object' === $setting['schema']['type'] ? (object) $value : $value;
		}

		return $result;
	}

	/**
	 * Permission callback for the settings abilities.
	 *
	 * The settings abilities read site options, so they are gated on the `manage_options`
	 * capability regardless of the input.
	 *
	 * @since 7.2.0
	 *
	 * @return bool True if the current user can manage options.
	 */
	private function check_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Builds the input schema for the `core/settings-get` ability: optional filters by group and/or name.
	 *
	 * Both `group` and `fields` are optional; supplying both narrows the response to their
	 * intersection, and supplying neither returns every exposed setting.
	 *
	 * @since 7.2.0
	 *
	 * @param list<string> $groups      Available settings groups.
	 * @param list<string> $field_names Available exposed setting names.
	 * @return array<string, mixed> The input JSON Schema.
	 */
	private function get_settings_get_input_schema( array $groups, array $field_names ): array {
		return array(
			'type'                 => 'object',
			'default'              => array(),
			'properties'           => array(
				'group'  => array(
					'type'        => 'string',
					'enum'        => $groups,
					'description' => __( 'Return only settings that belong to this settings group. If omitted, settings of every group are returned.' ),
				),
				'fields' => array(
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array(
						'type' => 'string',
						'enum' => $field_names,
					),
					'description' => __( 'Return only the settings with these names. If omitted or empty, all settings allowed by the group filter are returned. When both filters are given, only settings matching both are returned.' ),
				),
			),
			'additionalProperties' => false,
		);
	}

	/**
	 * Builds the output schema for the `core/settings-get` ability.
	 *
	 * No setting is marked required because the `group` and `fields` inputs let the caller
	 * request any subset, and a setting whose value does not match its schema is left out.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, mixed> The output JSON Schema.
	 */
	private function get_settings_get_output_schema(): array {
		return array(
			'type'                 => 'object',
			'description'          => __( 'A map of setting name to its current value.' ),
			'properties'           => wp_list_pluck( $this->exposed_settings, 'schema' ),
			'additionalProperties' => false,
		);
	}

	/**
	 * Returns the settings exposed through the Abilities API.
	 *
	 * Reads {@see get_registered_settings()} and keeps only settings flagged with a truthy
	 * `show_in_abilities` argument, of a type the settings endpoint supports. Each entry is
	 * keyed by its exposed name and carries the underlying option name, the settings group,
	 * the registered default (null when there is none), and a JSON Schema describing the value.
	 *
	 * Two settings exposed under the same name trigger a notice, and the later one is exposed.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, array{option: string, group: string, default: mixed, schema: array<string, mixed>}> Settings keyed by exposed name.
	 */
	private function get_exposed_settings(): array {
		$settings = array();

		foreach ( get_registered_settings() as $option_name => $args ) {
			if ( empty( $args['show_in_abilities'] ) ) {
				continue;
			}

			$show = $this->get_exposure_args( $args );

			$schema = $this->value_schema( $args, $show );
			if ( ! in_array( $schema['type'], array( 'number', 'integer', 'string', 'boolean', 'array', 'object' ), true ) ) {
				continue;
			}

			$option_name  = (string) $option_name;
			$exposed_name = empty( $show['name'] ) ? $option_name : (string) $show['name'];

			if ( isset( $settings[ $exposed_name ] ) ) {
				_doing_it_wrong(
					__METHOD__,
					sprintf(
						/* translators: 1: Option name, 2: Setting name exposed to abilities, 3: Option name. */
						__( 'The "%1$s" setting is exposed to abilities as "%2$s", a name already used by the "%3$s" setting, which is no longer exposed.' ),
						esc_html( $option_name ),
						esc_html( $exposed_name ),
						esc_html( $settings[ $exposed_name ]['option'] )
					),
					'7.2.0'
				);
			}

			$settings[ $exposed_name ] = array(
				'option'  => $option_name,
				'group'   => $args['group'] ?? '',
				'default' => $args['default'] ?? null,
				'schema'  => $schema,
			);
		}

		return $settings;
	}

	/**
	 * Returns the name and schema overrides used to expose a setting to abilities.
	 *
	 * When `show_in_abilities` is `true`, the setting is exposed the same way as in the
	 * REST API: it uses the `name` and `schema` from `show_in_rest`. An array is used
	 * as is.
	 *
	 * @since 7.2.0
	 *
	 * @param array<string, mixed> $args The setting registration arguments.
	 * @return array<string, mixed> The exposure arguments, with optional `name` and `schema` keys.
	 */
	private function get_exposure_args( array $args ): array {
		if ( is_array( $args['show_in_abilities'] ) ) {
			return $args['show_in_abilities'];
		}

		return is_array( $args['show_in_rest'] ) ? $args['show_in_rest'] : array();
	}

	/**
	 * Builds the JSON Schema describing a single setting's value.
	 *
	 * As in the settings endpoint, objects in the schema reject properties they do not declare,
	 * unless the schema allows them.
	 *
	 * @since 7.2.0
	 *
	 * @param array<string, mixed> $args The setting registration arguments.
	 * @param array<string, mixed> $show The exposure arguments, see get_exposure_args().
	 * @return array<string, mixed> The value JSON Schema.
	 */
	private function value_schema( array $args, $show ): array {
		$schema = array(
			'type' => $args['type'],
		);
		if ( ! empty( $args['label'] ) ) {
			$schema['title'] = $args['label'];
		}
		if ( ! empty( $args['description'] ) ) {
			$schema['description'] = $args['description'];
		}
		if ( isset( $show['schema'] ) && is_array( $show['schema'] ) ) {
			/** @var array<string, mixed> $show_schema */
			$show_schema = $show['schema'];
			$schema      = array_merge( $schema, $show_schema );
		}

		return rest_default_additional_properties_to_false( $schema );
	}
}
