<?php
/**
 * Abilities API: WP_Abilities_Users class.
 *
 * @package WordPress
 * @subpackage Abilities API
 * @since 7.2.0
 */

declare( strict_types = 1 );

/**
 * Core class used to register user-related abilities.
 *
 * Registers the read-only `core/users-query` ability, which retrieves a paginated
 * list of readable WordPress users, optionally filtered by IDs, slugs, usernames,
 * roles, or published-post authorship. Field-level access is enforced per user by
 * omitting fields the current user cannot view.
 *
 * Unlike the core abilities registered directly in wp_register_core_abilities() as
 * self-contained closures, the users ability lives in a dedicated class
 * because its callbacks and schemas share helpers: the permission and execute
 * callbacks read the input through the same code, and the input schema, output
 * schema, and field normalization are built from the same field definitions.
 * Future write-oriented user abilities can reuse them as well.
 *
 * Only register() is public. The ability callbacks are closures that call private
 * methods, so callers go through the Abilities API, such as
 * `wp_get_ability( 'core/users-query' )->execute()`, which validates the input and
 * checks permissions before running them.
 *
 * @since 7.2.0
 *
 * @access private
 */
final class WP_Abilities_Users {

	/**
	 * The ability category used for user abilities.
	 *
	 * @since 7.2.0
	 * @var string
	 */
	private const CATEGORY = 'user';

	/**
	 * Default number of users returned per page, unless `where` lists users by ID, slug, or username.
	 *
	 * @since 7.2.0
	 * @var int
	 */
	private const DEFAULT_PER_PAGE = 10;

	/**
	 * Maximum number of users returned per page.
	 *
	 * Each identifier list in `where` holds at most this many values, so the users it
	 * lists fit on one page.
	 *
	 * @since 7.2.0
	 * @var int
	 */
	private const MAX_PER_PAGE = 100;

	/**
	 * The WP_User_Query argument that each identifier list in `where` sets.
	 *
	 * The lists filter the results but do not order them, as in the REST users controller,
	 * so results keep WP_User_Query's default order, by login.
	 *
	 * @since 7.2.0
	 * @var array<string, string>
	 */
	private const IDENTIFIER_LISTS = array(
		'ids'       => 'include',
		'slugs'     => 'nicename__in',
		'usernames' => 'login__in',
	);

	/**
	 * Default fields returned when the caller does not request a field subset.
	 *
	 * @since 7.2.0
	 * @var string[]
	 */
	private const DEFAULT_FIELDS = array(
		'id',
		'name',
		'link',
		'slug',
		'avatar_urls',
	);

	/**
	 * Registers all user abilities.
	 *
	 * Must run on the `wp_abilities_api_init` hook.
	 *
	 * @since 7.2.0
	 */
	public function register(): void {
		$this->register_users_query();
	}

	/**
	 * Registers the read-only `core/users-query` ability.
	 *
	 * Unlike `core/get-user-info`, which only returns the current user's own profile,
	 * this ability is generic and reads any user the current user is allowed to see.
	 *
	 * @since 7.2.0
	 */
	private function register_users_query(): void {
		wp_register_ability(
			'core/users-query',
			array(
				'label'               => __( 'Query Users' ),
				'description'         => __( 'Retrieves a paginated list of readable WordPress users, optionally filtered by IDs, slugs, usernames, roles, or published-post authorship. Callers without permission to list users only see public authors. To read your own profile, use `core/get-user-info`. Requires an authenticated user.' ),
				'category'            => self::CATEGORY,
				'input_schema'        => $this->get_users_query_input_schema(),
				'output_schema'       => $this->get_users_query_output_schema(),
				'execute_callback'    => function ( $input = array() ) {
					return $this->execute_users_query( $input );
				},
				'permission_callback' => function ( $input = array() ): bool {
					return $this->check_permission( $input );
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
	 * Permission callback for the `core/users-query` ability.
	 *
	 * Performs request-level checks only, and never looks users up. Which users are
	 * returned is decided by the query arguments in {@see self::execute_users_query()}.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $input Optional. The ability input. Default empty array.
	 * @return bool True if the request may proceed, false otherwise.
	 */
	private function check_permission( $input = array() ): bool {
		$input = rest_sanitize_object( $input );

		if ( ! is_user_logged_in() ) {
			return false;
		}

		/*
		 * A `where` that is not an object is let through, so that the execute callback can
		 * report it as an invalid filter.
		 */
		$where = $this->get_input_object( $input, 'where' ) ?? array();

		// Roles and usernames are not public, so only callers who can list users may filter by them.
		return ( empty( $where['roles'] ) && empty( $where['usernames'] ) ) || current_user_can( 'list_users' );
	}

	/**
	 * Executes the `core/users-query` ability.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $input Optional. The ability input. Default empty array.
	 * @return array<string, mixed>|WP_Error A page of user data, or a WP_Error on failure.
	 */
	private function execute_users_query( $input = array() ) {
		$input  = rest_sanitize_object( $input );
		$fields = $this->normalize_fields( $input );

		/*
		 * A `where` that is not an object cannot be honored. Reject it rather than reading it
		 * as no filter at all, which would widen the request to every readable user.
		 */
		$where = $this->get_input_object( $input, 'where' );
		if ( null === $where ) {
			return $this->invalid_filter_error( 'where', __( 'The where filter must be an object.' ) );
		}

		// Pagination that is not an object falls back to the defaults, like page values that cannot be parsed.
		$pagination = $this->get_input_object( $input, 'pagination' ) ?? array();
		$page       = $this->parse_filter_int( $pagination['page'] ?? 1, 1 ) ?? 1;

		/*
		 * On transports that skip schema validation, a filter that cannot be honored must
		 * not be dropped, which would silently widen the query. Reject it instead, so the
		 * query fails closed, as the content query does with its filters. WP_User_Query
		 * ignores an empty list, so a list with no valid value left would return every user.
		 */
		$query_args = array();
		foreach ( self::IDENTIFIER_LISTS as $key => $query_var ) {
			if ( ! isset( $where[ $key ] ) ) {
				continue;
			}

			$query_args[ $query_var ] = 'ids' === $key ? $this->normalize_id_list( $where[ $key ] ) : $this->normalize_string_list( $where[ $key ] );
			if ( empty( $query_args[ $query_var ] ) ) {
				return $this->invalid_filter_error(
					"where[{$key}]",
					/* translators: %s: The name of the filter, such as ids. */
					sprintf( __( 'The %s filter must list one or more valid values.' ), $key )
				);
			}
		}

		/*
		 * An explicit `per_page` always wins. Otherwise a request that lists users by ID, slug,
		 * or username returns every matching user on the first page, since the input schema
		 * caps each list at the maximum page size.
		 */
		$default_per_page     = empty( $query_args ) ? self::DEFAULT_PER_PAGE : self::MAX_PER_PAGE;
		$per_page             = min( self::MAX_PER_PAGE, $this->parse_filter_int( $pagination['per_page'] ?? null, 1 ) ?? $default_per_page );
		$query_args['number'] = $per_page;
		$query_args['paged']  = $page;

		if ( isset( $where['roles'] ) ) {
			$query_args['role__in'] = $this->normalize_string_list( $where['roles'] );
			if ( empty( $query_args['role__in'] ) ) {
				return $this->invalid_filter_error( 'where[roles]', __( 'The roles filter must list one or more role names.' ) );
			}
		}

		/*
		 * The permission callback already refuses the role and username filters to callers
		 * who cannot list users. Refuse them here too, rather than dropping them, which would
		 * widen the query to every public author, so a direct call fails closed.
		 */
		$can_list_users = current_user_can( 'list_users' );
		if ( isset( $where['roles'] ) && ! $can_list_users ) {
			return new WP_Error(
				'users_cannot_filter_by_role',
				__( 'Sorry, you are not allowed to filter users by role.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( isset( $where['usernames'] ) && ! $can_list_users ) {
			return new WP_Error(
				'users_cannot_filter_by_username',
				__( 'Sorry, you are not allowed to filter users by username.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$has_published_posts = $this->normalize_has_published_posts( $where );
		if ( array_key_exists( 'has_published_posts', $where ) && null === $has_published_posts ) {
			return $this->invalid_filter_error( 'where[has_published_posts]', __( 'The has_published_posts filter must be true or list one or more post type names.' ) );
		}

		/*
		 * Callers who cannot list users only see public authors, as in the REST users
		 * controller, so the filter is always applied for them. This intentionally excludes
		 * the caller's own account when they have no published posts, which
		 * `core/get-user-info` returns instead.
		 */
		if ( null !== $has_published_posts || ! $can_list_users ) {
			/*
			 * The post types are always resolved here rather than passed as `true`.
			 * WP_User_Query reads `true` as `get_post_types( array( 'public' => true ) )`,
			 * which is not the same as the publicly viewable set the rest of the ability
			 * uses. Resolving here also picks up post types registered or unregistered
			 * after the input schema was built.
			 */
			$public_post_types = $this->get_public_post_types();

			$has_published_posts = is_array( $has_published_posts )
				? array_values( array_intersect( $public_post_types, $has_published_posts ) )
				: $public_post_types;

			if ( empty( $has_published_posts ) ) {
				return $this->prepare_output( array(), 0, 0 );
			}

			$query_args['has_published_posts'] = $has_published_posts;
		}

		$query = new WP_User_Query( $query_args );

		/*
		 * The rows and the totals come from the same query, so they agree. As in the REST
		 * users controller, rows are not filtered by site membership afterwards: on
		 * multisite, WP_User_Query already limits the query to members of the current site.
		 */
		$total_users = $query->get_total();
		$total_pages = (int) ceil( $total_users / $per_page );

		/*
		 * A page past the last one does not exist, so report it as not found instead of
		 * returning a bare empty list. A genuinely empty result set still returns zero
		 * totals and no error.
		 */
		if ( $total_users > 0 && $page > $total_pages ) {
			return $this->invalid_page_number_error();
		}

		$users = array();
		foreach ( $query->get_results() as $user ) {
			if ( ! $user instanceof WP_User ) {
				continue;
			}

			$users[] = $this->format_user( $user, $fields );
		}

		return $this->prepare_output( $users, $total_users, $total_pages );
	}

	/**
	 * Reads an object member of the input, such as `where` or `pagination`.
	 *
	 * Accepts what schema validation reads as an object: an array, a `stdClass`, or an
	 * empty string. Only the REST run controller converts input to the schema types, so
	 * callers that bypass it, such as a direct WP_Ability::execute() call, can pass any of them.
	 *
	 * @since 7.2.0
	 *
	 * @param array<mixed> $input The ability input.
	 * @param string       $key   The member name.
	 * @return array<mixed>|null The member as an array, an empty array when it is omitted,
	 *                           or null when it is not an object.
	 */
	private function get_input_object( array $input, string $key ): ?array {
		if ( ! array_key_exists( $key, $input ) ) {
			return array();
		}

		return rest_is_object( $input[ $key ] ) ? rest_sanitize_object( $input[ $key ] ) : null;
	}

	/**
	 * Parses a raw input value into an integer of at least a minimum, or null when invalid.
	 *
	 * Accepts the whole numbers that the integer schema accepts: native integers, floats such
	 * as the 2.0 that JSON encoders produce, and integer strings such as "2", "2.0", or "+2".
	 * Only the REST run controller converts input to the schema types, so other callers, such
	 * as the MCP adapter or a direct WP_Ability::execute() call, pass them on unconverted.
	 * Anything else, such as a fraction, is rejected rather than coerced: a malformed ID would
	 * otherwise resolve to another user.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $value The raw input value.
	 * @param int   $min   The smallest acceptable value.
	 * @return int|null The parsed integer, or null when the value is not an integer >= $min.
	 */
	private function parse_filter_int( $value, int $min ): ?int {
		if ( is_int( $value ) ) {
			return $value >= $min ? $value : null;
		}

		if ( is_string( $value ) && '' !== $value && ctype_digit( $value ) ) {
			$int = (int) $value;

			return $int >= $min ? $int : null;
		}

		if ( ! rest_is_integer( $value ) ) {
			return null;
		}

		/*
		 * Other whole numbers are read as floats, which hold every integer only up to 2 ** 53.
		 * Past that, the value may not be the one that was sent, and casting a float beyond the
		 * integer range wraps it to an arbitrary integer, such as the ID of another user.
		 */
		$number = (float) $value;

		return $number >= $min && $number <= 2 ** 53 ? (int) $number : null;
	}

	/**
	 * Returns publicly viewable post types.
	 *
	 * Uses {@see is_post_type_viewable()} rather than the `public` registration
	 * argument, since `public` alone does not guarantee a post type is viewable
	 * on the front end. Deliberately resolved on every call rather than cached:
	 * post types can be unregistered or re-registered with different arguments
	 * between the ability being registered and the ability being used.
	 *
	 * @since 7.2.0
	 *
	 * @return string[] Publicly viewable post type names.
	 */
	private function get_public_post_types(): array {
		return array_values( array_filter( get_post_types(), 'is_post_type_viewable' ) );
	}

	/**
	 * Returns the requested fields, or a lean default set when none are given.
	 *
	 * An empty or absent `fields` value selects a lean set of common read fields.
	 * Otherwise the requested fields are returned. The input schema has already
	 * validated the names against the supported set before the ability executes.
	 *
	 * The `id` field is always included, matching the REST users controller
	 * where `id` is present in every context. This also guarantees the result
	 * is never empty, so it always serializes as a JSON object.
	 *
	 * @since 7.2.0
	 *
	 * @param array<mixed> $input The ability input.
	 * @return string[] List of requested field names.
	 */
	private function normalize_fields( array $input ): array {
		$fields = isset( $input['fields'] ) ? $this->normalize_string_list( $input['fields'] ) : array();
		if ( empty( $fields ) ) {
			$fields = self::DEFAULT_FIELDS;
		}

		if ( ! in_array( 'id', $fields, true ) ) {
			array_unshift( $fields, 'id' );
		}

		return $fields;
	}

	/**
	 * Returns the user field definitions, keyed by field name in output order.
	 *
	 * This is the single source of truth for the ability's user fields: the output
	 * schema uses the definitions directly, while the input schema and field
	 * normalization use the keys. The field set is deliberately unconditional:
	 * the registered schemas are a registration-time snapshot, so conditional
	 * availability (such as `avatar_urls` honoring the `show_avatars` option) is
	 * enforced per call in {@see self::format_user()} instead of here, where the
	 * option could change between registration and use.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, mixed> User field definitions.
	 */
	private function get_user_properties(): array {
		return array(
			'id'              => array(
				'type'        => 'integer',
				'description' => __( 'The user ID.' ),
			),
			'name'            => array(
				'type'        => 'string',
				'description' => __( 'The display name for the user.' ),
			),
			'description'     => array(
				'type'        => 'string',
				'description' => __( 'Description of the user. Present when the current user can view it.' ),
			),
			'url'             => array(
				/*
				 * Unlike the REST users controller, `url` declares no `uri` format. It is
				 * empty for users without a website, and clients that check formats,
				 * such as the abilities JS client when it re-validates the output, would
				 * reject the empty string and fail the whole call.
				 */
				'type'        => 'string',
				'description' => __( 'URL of the user. Present when the current user can view it.' ),
			),
			'link'            => array(
				'type'        => 'string',
				'format'      => 'uri',
				'description' => __( 'Author archive URL for the user.' ),
			),
			'slug'            => array(
				'type'        => 'string',
				'description' => __( 'An alphanumeric identifier for the user.' ),
			),
			'avatar_urls'     => array(
				'type'                 => 'object',
				'description'          => __( 'Avatar URLs for the user, keyed by image size in pixels. A size is null when no avatar URL can be resolved for it. Present when the show_avatars option is enabled.' ),
				'additionalProperties' => array(
					'type'   => array( 'string', 'null' ),
					'format' => 'uri',
				),
			),
			'username'        => array(
				'type'        => 'string',
				'description' => __( 'Login name for the user. Present when the current user can view it.' ),
			),
			'email'           => array(
				'type'        => array( 'string', 'null' ),
				'format'      => 'email',
				'description' => __( 'The email address for the user. Null when the user has no stored address, or when the stored address is not a valid email. Present when the current user can view it.' ),
			),
			'first_name'      => array(
				'type'        => 'string',
				'description' => __( 'First name for the user. Present when the current user can view it.' ),
			),
			'last_name'       => array(
				'type'        => 'string',
				'description' => __( 'Last name for the user. Present when the current user can view it.' ),
			),
			'nickname'        => array(
				'type'        => 'string',
				'description' => __( 'The nickname for the user. Present when the current user can view it.' ),
			),
			'locale'          => array(
				'type'        => 'string',
				'description' => __( 'Locale for the user. Present when the current user can view it.' ),
			),
			'registered_date' => array(
				'type'        => array( 'string', 'null' ),
				'format'      => 'date-time',
				'description' => __( 'Registration date for the user. Null when the stored date is not a valid date. Present when the current user can view it.' ),
			),
			'roles'           => array(
				'type'        => 'array',
				'description' => __( 'Roles assigned to the user. Present when the current user can view them.' ),
				/*
				 * Output roles are not pinned to an enum. The schema is a
				 * registration-time snapshot, but a role can be registered after
				 * registration and still be held by a returned user; a snapshot enum
				 * would reject that legitimate value during output validation and
				 * fail the whole call. This also matches the REST users controller,
				 * whose `roles` output items are plain strings.
				 */
				'items'       => array(
					'type' => 'string',
				),
			),
		);
	}

	/**
	 * Normalizes a mixed value into a list of user IDs.
	 *
	 * Accepts a list, a single ID, or a comma-separated string, which it parses with
	 * wp_parse_list(), as schema validation does. Each item is read with parse_filter_int(),
	 * so an item that is not a whole number of at least 1, such as a fraction or a negative
	 * number, is dropped rather than truncated or made positive, which would read it as the
	 * ID of another user.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $value Raw value.
	 * @return int[] Unique user IDs.
	 */
	private function normalize_id_list( $value ): array {
		$items = is_array( $value ) || is_string( $value ) ? wp_parse_list( $value ) : array( $value );
		$ids   = array();
		foreach ( $items as $item ) {
			$ids[] = $this->parse_filter_int( $item, 1 );
		}

		return array_values( array_unique( array_filter( $ids ) ) );
	}

	/**
	 * Normalizes a mixed value into a list of strings.
	 *
	 * Accepts arrays and CSV strings, which it parses with wp_parse_list(), as schema
	 * validation does. Schema validation accepts a CSV string for an array, and only the
	 * REST run controller converts input to the schema types, so callers that bypass it,
	 * such as a direct WP_Ability::execute() call, can pass one. Empty and duplicate
	 * items need no handling here, because validation has already rejected them.
	 *
	 * @since 7.2.0
	 *
	 * @param mixed $value Raw value.
	 * @return string[] Normalized strings.
	 */
	private function normalize_string_list( $value ): array {
		if ( ! is_array( $value ) && ! is_string( $value ) ) {
			return array();
		}

		return array_values( array_filter( wp_parse_list( $value ), 'is_string' ) );
	}

	/**
	 * Normalizes the `has_published_posts` filter.
	 *
	 * Accepts the string and integer forms of `true` alongside the native boolean.
	 * Schema validation accepts them, and only the REST run controller converts input
	 * to the schema types, so callers that bypass it, such as a direct
	 * WP_Ability::execute() call, can pass one.
	 *
	 * @since 7.2.0
	 *
	 * @param array<mixed> $where The `where` filters.
	 * @return bool|string[]|null Normalized query value, or null when omitted.
	 */
	private function normalize_has_published_posts( array $where ) {
		if ( ! array_key_exists( 'has_published_posts', $where ) ) {
			return null;
		}

		$value = $where['has_published_posts'];

		if ( rest_is_boolean( $value ) && rest_sanitize_boolean( $value ) ) {
			return true;
		}

		$post_types = $this->normalize_string_list( $value );

		return empty( $post_types ) ? null : $post_types;
	}

	/**
	 * Builds the input schema for the `core/users-query` ability.
	 *
	 * The input has three members: `where` selects the users, `fields` selects the fields
	 * returned for each user, and `pagination` selects the page of results. The `where`
	 * filters can be combined in any way, and a user must match all of them.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, mixed> The input JSON Schema.
	 */
	private function get_users_query_input_schema(): array {
		/*
		 * Input enums intentionally reflect roles and post types available at
		 * ability registration time. This makes the schema a stable contract that
		 * developers can filter when registering the ability.
		 */
		$role_names        = array_keys( wp_roles()->roles );
		$public_post_types = $this->get_public_post_types();

		return array(
			'type'                 => 'object',
			'default'              => array(),
			'additionalProperties' => false,
			'properties'           => array(
				'where'      => array(
					'type'                 => 'object',
					'additionalProperties' => false,
					'properties'           => array(
						'ids'                 => array(
							'type'        => 'array',
							'uniqueItems' => true,
							'minItems'    => 1,
							'maxItems'    => self::MAX_PER_PAGE,
							'items'       => array(
								'type'    => 'integer',
								'minimum' => 1,
							),
							'description' => __( 'Limit results to users with these IDs. If `pagination.per_page` is omitted, every matching user is returned on the first page.' ),
						),
						'slugs'               => array(
							'type'        => 'array',
							'uniqueItems' => true,
							'minItems'    => 1,
							'maxItems'    => self::MAX_PER_PAGE,
							'items'       => array(
								'type' => 'string',
							),
							'description' => __( 'Limit results to users with these slugs. If `pagination.per_page` is omitted, every matching user is returned on the first page.' ),
						),
						'usernames'           => array(
							'type'        => 'array',
							'uniqueItems' => true,
							'minItems'    => 1,
							'maxItems'    => self::MAX_PER_PAGE,
							'items'       => array(
								'type' => 'string',
							),
							'description' => __( 'Limit results to users with these usernames. Requires permission to list users. If `pagination.per_page` is omitted, every matching user is returned on the first page.' ),
						),
						'roles'               => array(
							'type'        => 'array',
							'uniqueItems' => true,
							'minItems'    => 1,
							'items'       => array(
								'type' => 'string',
								'enum' => $role_names,
							),
							'description' => __( 'Filter users by one or more roles. Requires permission to list users.' ),
						),
						'has_published_posts' => array(
							'anyOf'       => array(
								// True matches authors of any publicly viewable post type.
								array(
									'type' => 'boolean',
									'enum' => array( true ),
								),
								// A list matches authors of the listed post types.
								array(
									'type'        => 'array',
									'uniqueItems' => true,
									'minItems'    => 1,
									'items'       => array(
										'type' => 'string',
										'enum' => $public_post_types,
									),
								),
							),
							'description' => __( 'Limit results to users with published posts. Use true for all publicly viewable post types, or provide post type names.' ),
						),
					),
					'description'          => __( 'Selects the users to return. Every given filter must match, and a list matches users with any of its values. Results are ordered by username, and values that match no readable user are left out rather than causing an error. If omitted, every readable user is returned. Callers without permission to list users only see public authors, who have published posts in a publicly viewable post type. To read your own profile, use the `core/get-user-info` ability.' ),
				),
				'fields'     => array(
					'type'        => 'array',
					'uniqueItems' => true,
					'items'       => array(
						'type' => 'string',
						'enum' => array_keys( $this->get_user_properties() ),
					),
					'description' => __( 'Limit each returned user to these fields. If omitted or empty, a lean set of common read fields is returned. The `id` field is always included, and fields the current user cannot view are omitted rather than causing an error.' ),
				),
				'pagination' => array(
					'type'                 => 'object',
					'additionalProperties' => false,
					'properties'           => array(
						'page'     => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'description' => __( 'Page of results to return. Requesting a page beyond the last one is an error. Check `pagination.total_pages` in the result before requesting later pages.' ),
						),
						'per_page' => array(
							'type'        => 'integer',
							'minimum'     => 1,
							'maximum'     => self::MAX_PER_PAGE,
							'description' => __( 'Maximum number of users to return per page.' ),
						),
					),
					'description'          => __( 'Selects the page of results to return.' ),
				),
			),
		);
	}

	/**
	 * Builds the output schema for the `core/users-query` ability.
	 *
	 * Every request returns the same shape: a page of users in `items`, and the totals
	 * across all pages in `pagination`.
	 *
	 * Only `id` is required in a user, because it is always returned. The other fields
	 * are optional because the `fields` input lets the caller request any subset, and
	 * restricted fields are omitted when unavailable.
	 *
	 * @since 7.2.0
	 *
	 * @return array<string, mixed> The output JSON Schema.
	 */
	private function get_users_query_output_schema(): array {
		return array(
			'type'                 => 'object',
			'additionalProperties' => false,
			'required'             => array( 'items', 'pagination' ),
			'properties'           => array(
				'items'      => array(
					'type'        => 'array',
					'description' => __( 'The readable users on the requested page.' ),
					'items'       => array(
						'type'                 => 'object',
						'additionalProperties' => false,
						'required'             => array( 'id' ),
						'properties'           => $this->get_user_properties(),
					),
				),
				'pagination' => array(
					'type'                 => 'object',
					'additionalProperties' => false,
					'required'             => array( 'total', 'total_pages' ),
					'properties'           => array(
						'total'       => array(
							'type'        => 'integer',
							'description' => __( 'Total number of users matching the request, across all pages.' ),
						),
						'total_pages' => array(
							'type'        => 'integer',
							'description' => __( 'Total number of result pages available for the request.' ),
						),
					),
					'description'          => __( 'The totals of the request, across all pages.' ),
				),
			),
		);
	}

	/**
	 * Formats a user into the ability output shape.
	 *
	 * Only the requested fields the current user can see are included, except
	 * `id`, which {@see self::normalize_fields()} always requests.
	 *
	 * @since 7.2.0
	 *
	 * @param WP_User  $user   The user object.
	 * @param string[] $fields The requested field names.
	 * @return array<string, mixed> The formatted user data.
	 */
	private function format_user( WP_User $user, array $fields ): array {
		$fields_requested = static function ( string $field ) use ( $fields ): bool {
			return in_array( $field, $fields, true );
		};

		$can_view_sensitive = get_current_user_id() === $user->ID || current_user_can( 'edit_user', $user->ID );

		$data = array();

		if ( $fields_requested( 'id' ) ) {
			$data['id'] = $user->ID;
		}
		if ( $fields_requested( 'name' ) ) {
			$data['name'] = (string) $user->display_name;
		}
		if ( $fields_requested( 'description' ) ) {
			$data['description'] = (string) $user->description;
		}
		if ( $fields_requested( 'url' ) ) {
			$data['url'] = (string) $user->user_url;
		}
		if ( $fields_requested( 'link' ) ) {
			$data['link'] = (string) get_author_posts_url( $user->ID, $user->user_nicename );
		}
		if ( $fields_requested( 'slug' ) ) {
			$data['slug'] = (string) $user->user_nicename;
		}
		/*
		 * The schemas always declare avatar_urls; availability is enforced here,
		 * since the option can change after the schemas are registered.
		 */
		if ( $fields_requested( 'avatar_urls' ) && get_option( 'show_avatars' ) ) {
			$data['avatar_urls'] = array_map(
				static function ( $url ) {
					return is_string( $url ) ? $url : null;
				},
				rest_get_avatar_urls( $user )
			);
		}

		if ( $can_view_sensitive ) {
			if ( $fields_requested( 'username' ) ) {
				$data['username'] = (string) $user->user_login;
			}
			if ( $fields_requested( 'email' ) ) {
				$data['email'] = is_email( $user->user_email ) ? (string) $user->user_email : null;
			}
			if ( $fields_requested( 'first_name' ) ) {
				$data['first_name'] = (string) $user->first_name;
			}
			if ( $fields_requested( 'last_name' ) ) {
				$data['last_name'] = (string) $user->last_name;
			}
			if ( $fields_requested( 'nickname' ) ) {
				$data['nickname'] = (string) $user->nickname;
			}
			if ( $fields_requested( 'locale' ) ) {
				$data['locale'] = (string) get_user_locale( $user );
			}
			if ( $fields_requested( 'registered_date' ) ) {
				/*
				 * The zero date, the column default, formats with a negative year that the
				 * `date-time` format rejects, so it is reported as null, like an unusable email.
				 */
				$registered_timestamp    = strtotime( $user->user_registered );
				$registered_date         = false !== $registered_timestamp ? gmdate( 'c', $registered_timestamp ) : '';
				$data['registered_date'] = rest_parse_date( $registered_date ) ? $registered_date : null;
			}
		}

		/*
		 * Roles reveal a user's privilege level, so they are gated like the other
		 * sensitive fields: visible only for the current user or a user the caller
		 * can edit. `list_users` alone (which grants no edit rights) is not enough,
		 * matching the REST users controller, where `roles` is an edit-context
		 * field and rows the caller cannot edit are dropped from collections.
		 */
		if ( $fields_requested( 'roles' ) && $can_view_sensitive ) {
			$data['roles'] = array_values( $user->roles );
		}

		return $data;
	}

	/**
	 * Builds the ability output for a page of formatted users.
	 *
	 * @since 7.2.0
	 *
	 * @param array<int, array<string, mixed>> $items       The formatted users on the page.
	 * @param int                              $total       Total number of users across all pages.
	 * @param int                              $total_pages Total number of pages.
	 * @return array<string, mixed> The ability output.
	 */
	private function prepare_output( array $items, int $total, int $total_pages ): array {
		return array(
			'items'      => $items,
			'pagination' => array(
				'total'       => $total,
				'total_pages' => $total_pages,
			),
		);
	}

	/**
	 * Builds the error for a page past the last page of results.
	 *
	 * A page that does not exist is reported as not found rather than as an empty list,
	 * so callers can tell it apart from a request that matches no users.
	 *
	 * @since 7.2.0
	 *
	 * @return WP_Error The invalid page number error.
	 */
	private function invalid_page_number_error(): WP_Error {
		return new WP_Error(
			'users_invalid_page_number',
			__( 'The page number requested is larger than the number of pages available.' ),
			array( 'status' => 404 )
		);
	}

	/**
	 * Builds the error for a filter that cannot be honored.
	 *
	 * As in the REST API's `rest_invalid_param` errors, the error data maps the filter's
	 * input path to the message under `params`, so callers can tell which filter failed
	 * without parsing the translated message.
	 *
	 * @since 7.2.0
	 *
	 * @param string $param   The filter's input path, such as `where[ids]`.
	 * @param string $message The error message.
	 * @return WP_Error The invalid filter error.
	 */
	private function invalid_filter_error( string $param, string $message ): WP_Error {
		return new WP_Error(
			'users_invalid_filter',
			$message,
			array(
				'status' => 400,
				'params' => array( $param => $message ),
			)
		);
	}
}
