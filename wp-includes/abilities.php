<?php
/**
 * Core Abilities registration.
 *
 * @package WordPress
 * @subpackage Abilities_API
 * @since 6.9.0
 */

declare( strict_types = 1 );

/**
 * Registers the core ability categories.
 *
 * @since 6.9.0
 */
function wp_register_core_ability_categories(): void {
	wp_register_ability_category(
		'site',
		array(
			'label'       => __( 'Site' ),
			'description' => __( 'Abilities that retrieve or modify site information and settings.' ),
		)
	);

	wp_register_ability_category(
		'user',
		array(
			'label'       => __( 'User' ),
			'description' => __( 'Abilities that retrieve or modify user information and settings.' ),
		)
	);
}

/**
 * Registers the default core abilities.
 *
 * @since 6.9.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 */
function wp_register_core_abilities(): void {
	$category_site = 'site';
	$category_user = 'user';

	$site_info_properties = array(
		'name'        => array(
			'type'        => 'string',
			'description' => __( 'The site title.' ),
		),
		'description' => array(
			'type'        => 'string',
			'description' => __( 'The site tagline.' ),
		),
		'url'         => array(
			'type'        => 'string',
			'description' => __( 'The site home URL.' ),
		),
		'wpurl'       => array(
			'type'        => 'string',
			'description' => __( 'The WordPress installation URL.' ),
		),
		'admin_email' => array(
			'type'        => 'string',
			'description' => __( 'The site administrator email address.' ),
		),
		'charset'     => array(
			'type'        => 'string',
			'description' => __( 'The site character encoding.' ),
		),
		'language'    => array(
			'type'        => 'string',
			'description' => __( 'The site language locale code.' ),
		),
		'version'     => array(
			'type'        => 'string',
			'description' => __( 'The WordPress version.' ),
		),
	);
	$site_info_fields     = array_keys( $site_info_properties );

	wp_register_ability(
		'core/get-site-info',
		array(
			'label'               => __( 'Get Site Information' ),
			'description'         => __( 'Returns site information configured in WordPress. By default returns all fields, or optionally a filtered subset.' ),
			'category'            => $category_site,
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'fields' => array(
						'type'        => 'array',
						'items'       => array(
							'type' => 'string',
							'enum' => $site_info_fields,
						),
						'description' => __( 'Optional: Limit response to specific fields. If omitted, all fields are returned.' ),
					),
				),
				'additionalProperties' => false,
				'default'              => array(),
			),
			'output_schema'       => array(
				'type'                 => 'object',
				'properties'           => $site_info_properties,
				'additionalProperties' => false,
			),
			'execute_callback'    => static function ( $input = array() ) use ( $site_info_fields ): array {
				$input = is_array( $input ) ? $input : array();
				$requested_fields = ! empty( $input['fields'] ) ? $input['fields'] : $site_info_fields;

				$result = array();
				foreach ( $requested_fields as $field ) {
					if ( 'language' === $field ) {
						$result[ $field ] = str_replace( '_', '-', get_locale() );
					} else {
						$result[ $field ] = get_bloginfo( $field );
					}
				}

				return $result;
			},
			'permission_callback' => static function (): bool {
				return current_user_can( 'manage_options' );
			},
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

	$user_info_properties = array(
		'id'            => array(
			'type'        => 'integer',
			'title'       => __( 'User ID' ),
			'description' => __( 'Unique numeric identifier for the user.' ),
		),
		'display_name'  => array(
			'type'        => 'string',
			'title'       => __( 'Display Name' ),
			'description' => __( 'Public-facing name selected by the user.' ),
		),
		'user_nicename' => array(
			'type'        => 'string',
			'title'       => __( 'User Nicename' ),
			'description' => __( 'URL-friendly slug for the user. Defaults to the username.' ),
		),
		'user_login'    => array(
			'type'        => 'string',
			'title'       => __( 'Username' ),
			'description' => __( 'Login identifier for the user. Cannot be changed once set.' ),
		),
		'roles'         => array(
			'type'        => 'array',
			'title'       => __( 'Roles' ),
			'description' => __( 'Roles assigned to the user, such as administrator, editor, author, contributor, or subscriber.' ),
			'items'       => array(
				'type' => 'string',
			),
		),
		'locale'        => array(
			'type'        => 'string',
			'title'       => __( 'Language' ),
			'description' => __( 'Locale code for the user, such as en_US.' ),
		),
		'first_name'    => array(
			'type'        => 'string',
			'title'       => __( 'First Name' ),
			'description' => __( 'Given name.' ),
		),
		'last_name'     => array(
			'type'        => 'string',
			'title'       => __( 'Last Name' ),
			'description' => __( 'Family name.' ),
		),
		'nickname'      => array(
			'type'        => 'string',
			'title'       => __( 'Nickname' ),
			'description' => __( 'Informal name. Defaults to the username.' ),
		),
		'description'   => array(
			'type'        => 'string',
			'title'       => __( 'Biographical Info' ),
			'description' => __( 'User-authored biography, often shown on author pages.' ),
		),
		'user_url'      => array(
			'type'        => 'string',
			'title'       => __( 'Website' ),
			'description' => __( 'Personal website URL.' ),
		),
	);
	$user_info_fields     = array_keys( $user_info_properties );

	wp_register_ability(
		'core/get-user-info',
		array(
			'label'               => __( 'Get User Information' ),
			'description'         => __( 'Returns profile details for the current authenticated user to support personalization, auditing, and access-aware behavior. By default returns all fields, or optionally a filtered subset.' ),
			'category'            => $category_user,
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'fields' => array(
						'type'        => 'array',
						'items'       => array(
							'type' => 'string',
							'enum' => $user_info_fields,
						),
						'description' => __( 'Optional: Limit response to specific fields. If omitted, all fields are returned.' ),
					),
				),
				'additionalProperties' => false,
				'default'              => array(),
			),
			'output_schema'       => array(
				'type'                 => 'object',
				'properties'           => $user_info_properties,
				'additionalProperties' => false,
			),
			'execute_callback'    => static function ( $input = array() ) use ( $user_info_fields ): array {
				$input            = is_array( $input ) ? $input : array();
				$requested_fields = ! empty( $input['fields'] ) ? $input['fields'] : $user_info_fields;
				$current_user     = wp_get_current_user();

				$all = array(
					'id'            => $current_user->ID,
					'display_name'  => $current_user->display_name,
					'user_nicename' => $current_user->user_nicename,
					'user_login'    => $current_user->user_login,
					// Ensure roles are encoded as a JSON array, regardless of their array keys.
					'roles'         => array_values( $current_user->roles ),
					'locale'        => get_user_locale( $current_user ),
					'first_name'    => $current_user->first_name,
					'last_name'     => $current_user->last_name,
					'nickname'      => $current_user->nickname,
					'description'   => $current_user->description,
					'user_url'      => $current_user->user_url,
				);

				return array_intersect_key( $all, array_flip( $requested_fields ) );
			},
			'permission_callback' => static function (): bool {
				return is_user_logged_in();
			},
			'meta'                => array(
				'annotations'  => array(
					'readonly'    => true,
					'destructive' => false,
					'idempotent'  => true,
				),
				'show_in_rest' => false,
			),
		)
	);

	wp_register_ability(
		'core/get-environment-info',
		array(
			'label'               => __( 'Get Environment Info' ),
			'description'         => __( 'Returns core details about the site\'s runtime context for diagnostics and compatibility (environment, PHP runtime, database server info, WordPress version).' ),
			'category'            => $category_site,
			'output_schema'       => array(
				'type'                 => 'object',
				'required'             => array( 'environment', 'php_version', 'db_server_info', 'wp_version' ),
				'properties'           => array(
					'environment'    => array(
						'type'        => 'string',
						'description' => __( 'The site\'s runtime environment classification (can be one of these: production, staging, development, local).' ),
						'enum'        => array( 'production', 'staging', 'development', 'local' ),
					),
					'php_version'    => array(
						'type'        => 'string',
						'description' => __( 'The PHP runtime version executing WordPress.' ),
					),
					'db_server_info' => array(
						'type'        => 'string',
						'description' => __( 'The database server vendor and version string reported by the driver.' ),
					),
					'wp_version'     => array(
						'type'        => 'string',
						'description' => __( 'The WordPress core version running on this site.' ),
					),
				),
				'additionalProperties' => false,
			),
			'execute_callback'    => static function (): array {
				global $wpdb;

				$env          = wp_get_environment_type();
				$php_version  = phpversion();
				$db_server_info  = '';
				if ( method_exists( $wpdb, 'db_server_info' ) ) {
					$db_server_info = $wpdb->db_server_info() ?? '';
				}
				$wp_version   = get_bloginfo( 'version' );

				return array(
					'environment'    => $env,
					'php_version'    => $php_version,
					'db_server_info' => $db_server_info,
					'wp_version'     => $wp_version,
				);
			},
			'permission_callback' => static function (): bool {
				return current_user_can( 'manage_options' );
			},
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
