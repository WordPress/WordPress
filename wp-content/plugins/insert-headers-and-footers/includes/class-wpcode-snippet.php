<?php
/**
 * The main class to work with single WPCode snippets.
 *
 * @package wpcode
 */

/**
 * WPCode_Snippet class.
 */
class WPCode_Snippet {

	/**
	 * Post type used to store.
	 *
	 * @var string
	 */
	public $post_type = 'wpcode';
	/**
	 * Location taxonomy name.
	 *
	 * @var string
	 */
	public $location_taxonomy = 'wpcode_location';
	/**
	 * Code type taxonomy name.
	 *
	 * @var string
	 */
	public $code_type_taxonomy = 'wpcode_type';
	/**
	 * Tags taxonomy name.
	 *
	 * @var string
	 */
	public $tags_taxonomy = 'wpcode_tags';
	/**
	 * The snippet id.
	 *
	 * @var int
	 */
	public $id;
	/**
	 * The WP_Post object for the snippet.
	 *
	 * @var WP_Post
	 */
	public $post_data;
	/**
	 * The snippet title.
	 *
	 * @var string
	 */
	public $title;
	/**
	 * The actual snippet code.
	 *
	 * @var string
	 */
	public $code;
	/**
	 * The code language/type.
	 *
	 * @var string
	 */
	public $code_type;
	/**
	 * The location where the snippet is added.
	 *
	 * @var string
	 */
	public $location;
	/**
	 * Auto-insert or use shortcode?
	 *
	 * @var int
	 */
	public $auto_insert;
	/**
	 * The insert number for paragraphs or posts in an archive.
	 *
	 * @var int
	 */
	public $insert_number;
	/**
	 * If the snippet is active or not, for now this is handled by the post status draft vs published.
	 *
	 * @var bool
	 */
	public $active;
	/**
	 * An array of tags.
	 *
	 * @var string[]
	 */
	public $tags;
	/**
	 * When we load the location from the terms let's store the object here.
	 *
	 * @var WP_Term
	 */
	private $location_term;
	/**
	 * When we load the code type from the terms let's store the object here.
	 *
	 * @var WP_Term
	 */
	private $code_type_term;
	/**
	 * The tag terms.
	 *
	 * @var WP_Term[]
	 */
	private $tags_terms;
	/**
	 * Whether the conditional rules are enabled or disabled.
	 *
	 * @var bool
	 */
	private $use_rules;
	/**
	 * The conditional logic rules.
	 *
	 * @var array
	 */
	private $rules;
	/**
	 * The priority for loading.
	 *
	 * @var int
	 */
	private $priority;

	/**
	 * The library id, if the snippet is created from the snippet library.
	 *
	 * @var int
	 */
	private $library_id;

	/**
	 * The cloud id, if the snippet has been saved to the user's cloud.
	 *
	 * @var string
	 */
	private $cloud_id;

	/**
	 * The custom shortcode name.
	 *
	 * @var string
	 */
	public $custom_shortcode;

	/**
	 * The version of the snippet from the library.
	 *
	 * @var string
	 */
	private $library_version;

	/**
	 * The snippet note.
	 *
	 * @var string
	 */
	private $note;
	/**
	 * The name of the generator used for this snippet (if any).
	 *
	 * @var string
	 */
	private $generator;
	/**
	 * The generator fields.
	 *
	 * @var array
	 */
	private $generator_data;
	/**
	 * The type of device to load this snippet on.
	 *
	 * @var string
	 */
	public $device_type;

	/**
	 * Schedule parameters for this snippet.
	 *
	 * @var array
	 */
	public $schedule;

	/**
	 * Location extra parameters.
	 * This is used to store extra parameters for the location.
	 *
	 * @var array
	 */
	public $location_extra;

	/**
	 * Get an array of the shortcode attributes for this snippet.
	 *
	 * @var array
	 */
	public $shortcode_attributes;

	/**
	 * Used to store the shortcode attributes values.
	 *
	 * @var array
	 */
	public $attributes;

	/**
	 * Constructor. If the post passed is not the correct post type
	 * the object will clear itself.
	 *
	 * @param array|int|WP_Post $snippet Load a snippet by id, WP_Post or array.
	 */
	public function __construct( $snippet ) {
		if ( is_int( $snippet ) ) {
			$this->load_from_id( $snippet );
		} elseif ( $snippet instanceof WP_Post ) {
			$this->post_data = $snippet;
		} elseif ( is_array( $snippet ) ) {
			$this->load_from_array( $snippet );
		}
		if ( isset( $this->post_data ) && $this->post_type !== $this->post_data->post_type ) {
			unset( $this->post_data );
			unset( $this->id );
		}
	}

	/**
	 * Load a snippet by its ID.
	 *
	 * @param int $snippet_id The snippet id.
	 *
	 * @return void
	 */
	public function load_from_id( $snippet_id ) {
		$this->post_data = get_post( $snippet_id );
		if ( $this->post_data ) {
			$this->id = $this->post_data->ID;
		}
	}

	/**
	 * Load snippet from array - useful for creating a new snippet.
	 *
	 * @param array $snippet_data The array of data to load.
	 *
	 * @return void
	 */
	public function load_from_array( $snippet_data ) {
		$available_options = get_object_vars( $this );
		foreach ( $available_options as $key => $value ) {
			if ( isset( $snippet_data[ $key ] ) ) {
				$this->$key = $snippet_data[ $key ];
			}
		}
	}

	/**
	 * Get the snippet location.
	 *
	 * @return string
	 */
	public function get_location() {
		if ( ! isset( $this->location ) ) {
			$this->set_location();
		}

		return $this->location;
	}

	/**
	 * Grab and set the location term and location string.
	 *
	 * @return void
	 */
	public function set_location() {
		// If something below fails, let's not try again.
		$this->location      = '';
		$this->location_term = $this->get_single_term( $this->location_taxonomy );
		if ( $this->location_term ) {
			$this->location = $this->location_term->slug;
		}
	}

	/**
	 * Get a single term for this snippet based on the taxonomy.
	 *
	 * @param string $taxonomy The taxonomy to grab data for.
	 *
	 * @return false|WP_Term
	 */
	public function get_single_term( $taxonomy ) {
		if ( ! isset( $this->post_data ) ) {
			return false;
		}
		$taxonomy_terms = wp_get_post_terms( $this->post_data->ID, $taxonomy );
		if ( ! empty( $taxonomy_terms ) && ! is_wp_error( $taxonomy_terms ) ) {
			return $taxonomy_terms[0];
		}

		return false;
	}

	/**
	 * Get the author from the post object.
	 *
	 * @return int
	 */
	public function get_snippet_author() {
		if ( isset( $this->post_data ) ) {
			return $this->post_data->post_author;
		}

		return 0;
	}

	/**
	 * Get the post data object.
	 *
	 * @return WP_Post
	 */
	public function get_post_data() {
		return isset( $this->post_data ) ? $this->post_data : null;
	}

	/**
	 * Get the snippet title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( ! isset( $this->title ) ) {
			$this->title = isset( $this->post_data ) ? $this->post_data->post_title : '';
		}

		return $this->title;
	}

	/**
	 * Get the snippet code.
	 *
	 * @return string
	 */
	public function get_code() {
		if ( ! isset( $this->code ) ) {
			$this->code = isset( $this->post_data ) ? $this->post_data->post_content : '';
		}

		return $this->code;
	}

	/**
	 * Get location term.
	 *
	 * @return WP_Term
	 */
	public function get_location_term() {
		if ( ! isset( $this->location_term ) ) {
			$this->set_location();
		}

		return $this->location_term;
	}

	/**
	 * Get the code type term.
	 *
	 * @return WP_Term
	 */
	public function get_code_type_term() {
		if ( ! isset( $this->code_type_term ) ) {
			$this->set_code_type();
		}

		return $this->code_type_term;
	}

	/**
	 * Is the snippet active?
	 *
	 * @return boolean
	 */
	public function is_active() {
		if ( ! isset( $this->active ) ) {
			$this->active = isset( $this->post_data->post_status ) && 'publish' === $this->post_data->post_status;
		}

		return $this->active;
	}

	/**
	 * Shorthand for activating.
	 *
	 * @return void
	 */
	public function activate() {
		$this->active = true;
		$this->get_id();
		$this->save();
	}

	/**
	 * Get the snippet id.
	 *
	 * @return int
	 */
	public function get_id() {
		if ( ! isset( $this->id ) ) {
			$this->id = isset( $this->post_data ) ? $this->post_data->ID : 0;
		}

		return $this->id;
	}

	/**
	 * Store current object in the db.
	 *
	 * @return int|false
	 */
	public function save() {
		$post_args = array(
			'post_type' => $this->post_type,
		);
		if ( isset( $this->id ) && 0 !== $this->id ) {
			$post_args['ID'] = $this->id;
			$this->load_from_id( $this->id );
		}
		if ( isset( $this->title ) ) {
			$post_args['post_title'] = $this->title;
		}
		if ( isset( $this->code ) ) {
			$post_args['post_content'] = $this->code;
		}

		// If the user is not allowed to activate/deactivate snippets, prevent it and show error.
		if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
			wpcode()->error->add_error(
				array(
					'message' => __( 'You are not allowed to change snippet status, please contact your webmaster.', 'insert-headers-and-footers' ),
					'type'    => 'permissions',
				)
			);
			unset( $this->active );
		}

		if ( isset( $this->active ) ) {
			// If we're going to activate a snippet let's check if errors will be thrown.
			$this->run_activation_checks();
			$post_args['post_status'] = $this->active ? 'publish' : 'draft';
		}

		do_action( 'wpcode_before_snippet_save', $this );

		if ( isset( $post_args['ID'] ) ) {
			$insert_result = wp_update_post( $post_args );
		} else {
			if ( empty( $post_args['post_title'] ) ) {
				$post_args['post_title'] = $this->get_untitled_title();
			}
			$insert_result = wp_insert_post( $post_args );
		}

		if ( 0 === $insert_result || is_wp_error( $insert_result ) ) {
			return false;
		}
		$this->id = $insert_result;

		// Remove recently deactivated snippet meta.
		$this->reset_recently_deactivated();

		if ( isset( $this->code_type ) ) {
			wp_set_post_terms( $this->id, $this->code_type, $this->code_type_taxonomy );
		}
		if ( isset( $this->auto_insert ) ) {
			// Save this value for reference, but we never query by it.
			update_post_meta( $this->id, '_wpcode_auto_insert', $this->auto_insert );
		}
		if ( isset( $this->location ) && 1 === $this->auto_insert ) {
			wp_set_post_terms( $this->id, $this->location, $this->location_taxonomy );
		} elseif ( isset( $this->auto_insert ) ) {
			// If auto insert is disabled we just empty the taxonomy.
			wp_set_post_terms( $this->id, array(), $this->location_taxonomy );
		}
		if ( isset( $this->tags ) ) {
			wp_set_post_terms( $this->id, $this->tags, $this->tags_taxonomy );
		}
		if ( isset( $this->insert_number ) ) {
			update_post_meta( $this->id, '_wpcode_auto_insert_number', $this->insert_number );
		}
		if ( isset( $this->use_rules ) ) {
			update_post_meta( $this->id, '_wpcode_conditional_logic_enabled', $this->use_rules );
		}
		if ( isset( $this->rules ) ) {
			update_post_meta( $this->id, '_wpcode_conditional_logic', $this->rules );
		}
		if ( isset( $this->priority ) ) {
			update_post_meta( $this->id, '_wpcode_priority', $this->priority );
		}
		if ( isset( $this->library_id ) ) {
			update_post_meta( $this->id, '_wpcode_library_id', $this->library_id );
		}
		if ( isset( $this->library_version ) ) {
			update_post_meta( $this->id, '_wpcode_library_version', $this->library_version );
		}
		if ( isset( $this->note ) ) {
			update_post_meta( $this->id, '_wpcode_note', $this->note );
		}
		if ( isset( $this->generator ) ) {
			update_post_meta( $this->id, '_wpcode_generator', $this->generator );
		}
		if ( isset( $this->generator_data ) ) {
			update_post_meta( $this->id, '_wpcode_generator_data', $this->generator_data );
		}
		if ( isset( $this->location_extra ) ) {
			update_post_meta( $this->id, '_wpcode_location_extra', $this->location_extra );
		}
		if ( isset( $this->cloud_id ) ) {
			$auth_username = wpcode()->library_auth->get_auth_username();
			$cloud_ids     = get_post_meta( $this->id, '_wpcode_cloud_id', true );
			if ( empty( $cloud_ids ) || ! is_array( $cloud_ids ) ) {
				$cloud_ids = array();
			}
			if ( empty( $this->cloud_id ) && isset( $cloud_ids[ $auth_username ] ) ) {
				unset( $cloud_ids[ $auth_username ] );
			} elseif ( ! empty( $this->cloud_id ) ) {
				$cloud_ids[ $auth_username ] = $this->cloud_id;
			}
			update_post_meta(
				$this->id,
				'_wpcode_cloud_id',
				$cloud_ids
			);
		}
		if ( isset( $this->custom_shortcode ) ) {
			if ( empty( $this->custom_shortcode ) ) {
				// Delete this meta if empty because we query by it.
				delete_post_meta( $this->id, '_wpcode_custom_shortcode' );
			} else {
				update_post_meta( $this->id, '_wpcode_custom_shortcode', $this->custom_shortcode );
			}
		}
		if ( isset( $this->device_type ) ) {
			update_post_meta( $this->id, '_wpcode_device_type', $this->device_type );
		}
		if ( isset( $this->schedule ) ) {
			update_post_meta( $this->id, '_wpcode_schedule', $this->schedule );
		}
		if ( isset( $this->shortcode_attributes ) ) {
			update_post_meta( $this->id, '_wpcode_shortcode_attributes', $this->shortcode_attributes );
		}

		/**
		 * Run extra logic after the snippet is saved.
		 *
		 * @param int            $id The id of the updated snippet.
		 * @param WPCode_Snippet $snippet The snippet object.
		 */
		do_action( 'wpcode_snippet_after_update', $this->id, $this );

		wpcode()->cache->cache_all_loaded_snippets();

		return $this->id;
	}

	/**
	 * Check if a snippet can be run without errors before activating it.
	 *
	 * @return void
	 */
	public function run_activation_checks() {
		$executed_types = array(
			'php',
			'universal',
		);
		if ( ! in_array( $this->get_code_type(), $executed_types, true ) ) {
			// If the code is not getting executed just skip.
			return;
		}
		if ( false === $this->active || isset( $this->post_data ) && 'publish' === $this->post_data->post_status ) {
			// If we're not trying to activate or the snippet is already active, bail.
			return;
		}
		// Make sure no errors are added by something else.
		wpcode()->error->clear_errors();
		// Try running the code.
		// Grab the executor class specific to the code type.
		$executor = wpcode()->execute->get_type_execute_class( $this->get_code_type() );
		// Mark this as an activation attempt.
		wpcode()->execute->doing_activation();
		/**
		 * Added for convenience.
		 *
		 * @var WPCode_Snippet_Execute_Type $executor
		 */
		$execute = new $executor( $this );
		// Grab the output that executes the code.
		$execute->get_output();
		// If any errors are caught, prevent the status from being changed.
		$has_error = wpcode()->error->has_error();
		if ( $has_error ) {
			$this->active = false;
		}

		wpcode()->execute->not_doing_activation();
	}

	/**
	 * Return the code type.
	 *
	 * @return string
	 */
	public function get_code_type() {
		if ( ! isset( $this->code_type ) ) {
			$this->set_code_type();
		}

		return $this->code_type;
	}

	/**
	 * Grab the code type from the taxonomy.
	 *
	 * @return void
	 */
	public function set_code_type() {
		// If something below fails, let's not try again.
		$this->code_type      = '';
		$this->code_type_term = $this->get_single_term( $this->code_type_taxonomy );
		if ( $this->code_type_term ) {
			$this->code_type = $this->code_type_term->slug;
		}
	}

	/**
	 * Get the default title for snippets with no title set.
	 *
	 * @return string
	 */
	public function get_untitled_title() {
		return __( 'Untitled Snippet', 'insert-headers-and-footers' );
	}

	/**
	 * Shorthand for deactivating a snippet.
	 *
	 * @return void
	 */
	public function deactivate() {
		$this->active = false;
		$this->get_id();
		$this->save();
	}

	/**
	 * This deactivates the snippet without regardless of user permissions.
	 * Should only be used for unattended auto-deactivation when a snippet throws a potentially blocking error.
	 *
	 * @return void
	 */
	public function force_deactivate() {
		global $wpdb;

		// We need to make a direct call as using wp_update_post will load the post content and if the current user
		// doesn't have the unfiltered_html capability, the code will be changed unexpectedly.
		$update = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->posts,
			array(
				'post_status' => 'draft',
			),
			array(
				'ID' => $this->get_id(),
			)
		);

		if ( $update ) {
			wpcode()->error->add_error(
				array(
					'message' => sprintf(
					/* translators: %s: Snippet title and ID used in the error log for clarity. */
						esc_html__( 'Snippet %s was automatically deactivated due to a fatal error.', 'insert-headers-and-footers' ),
						sprintf( '"%s" (#%d)', $this->get_title(), $this->get_id() )
					),
				)
			);

			// Rebuild cache to avoid the snippet being loaded again.
			wpcode()->cache->cache_all_loaded_snippets();

			// Finally, if all went well, let's mark the snippet as recently deactivated and keep a log of the time when this happened.
			$this->set_recently_deactivated();
		}
	}

	/**
	 * Add a meta to mark the snippet as recently deactivated + keep a timestamp of when the snippet was deactivated.
	 *
	 * @return void
	 */
	public function set_recently_deactivated() {
		update_post_meta( $this->get_id(), '_wpcode_recently_deactivated', time() );
	}

	/**
	 * Remove the meta that marks the snippet as recently deactivated.
	 *
	 * @return void
	 */
	public function reset_recently_deactivated() {
		delete_post_meta( $this->get_id(), '_wpcode_recently_deactivated' );
	}

	/**
	 * Remove the meta that marks the snippet as recently deactivated.
	 *
	 * @return mixed
	 */
	public function get_recently_deactivated_time() {
		return get_post_meta( $this->get_id(), '_wpcode_recently_deactivated', time() );
	}

	/**
	 * Get the auto insert number.
	 *
	 * @return int
	 */
	public function get_auto_insert_number() {
		if ( ! isset( $this->insert_number ) ) {
			$this->insert_number = absint( get_post_meta( $this->get_id(), '_wpcode_auto_insert_number', true ) );
			// Default value should be 1.
			if ( 0 === $this->insert_number ) {
				$this->insert_number = 1;
			}
		}

		return $this->insert_number;
	}

	/**
	 * Get the auto-insert value.
	 *
	 * @return int
	 */
	public function get_auto_insert() {
		if ( ! isset( $this->auto_insert ) ) {
			$this->auto_insert = absint( get_post_meta( $this->get_id(), '_wpcode_auto_insert', true ) );
		}

		return $this->auto_insert;
	}

	/**
	 * Get the array of tag slugs.
	 *
	 * @return string[]
	 */
	public function get_tags() {
		if ( ! isset( $this->tags ) ) {
			$this->set_tags();
		}

		return $this->tags;
	}

	/**
	 * Set the tags for the current snippet.
	 *
	 * @return void
	 */
	public function set_tags() {
		$tags      = wp_get_post_terms( $this->get_id(), $this->tags_taxonomy );
		$tag_slugs = array();
		foreach ( $tags as $tag ) {
			/**
			 * The tag term object.
			 *
			 * @var WP_Term $tag
			 */
			$tag_slugs[] = $tag->slug;
		}
		$this->tags       = $tag_slugs;
		$this->tags_terms = $tags;
	}

	/**
	 * Get the conditional logic rules from the db.
	 *
	 * @return array
	 */
	public function get_conditional_rules() {
		if ( ! isset( $this->rules ) ) {
			$rules = get_post_meta( $this->get_id(), '_wpcode_conditional_logic', true );
			if ( empty( $rules ) ) {
				$rules = array();
			}
			$this->rules = $rules;
		}

		return $this->rules;
	}

	/**
	 * Are the conditional logic rules enabled?
	 *
	 * @return bool
	 */
	public function conditional_rules_enabled() {
		if ( ! isset( $this->use_rules ) ) {
			$enabled = get_post_meta( $this->get_id(), '_wpcode_conditional_logic_enabled', true );
			if ( '' === $enabled ) {
				$enabled = false;
			}
			$this->use_rules = boolval( $enabled );
		}

		return $this->use_rules;
	}

	/**
	 * Get the note for this snippet.
	 *
	 * @return string
	 */
	public function get_note() {
		if ( ! isset( $this->note ) ) {
			$this->note = get_post_meta( $this->get_id(), '_wpcode_note', true );
		}

		return $this->note;
	}

	/**
	 * Get the priority number for this snippet.
	 *
	 * @return int
	 */
	public function get_priority() {
		if ( ! isset( $this->priority ) ) {
			$priority = get_post_meta( $this->get_id(), '_wpcode_priority', true );
			if ( '' === $priority ) {
				$priority = 10;
			}
			$this->priority = intval( $priority );
		}

		return $this->priority;
	}

	/**
	 * Get essential data for caching.
	 *
	 * @return array
	 */
	public function get_data_for_caching() {
		return array(
			'id'                   => $this->get_id(),
			'title'                => $this->get_title(),
			'code'                 => $this->get_code(),
			'code_type'            => $this->get_code_type(),
			'location'             => $this->get_location(),
			'auto_insert'          => $this->get_auto_insert(),
			'insert_number'        => $this->get_auto_insert_number(),
			'use_rules'            => $this->conditional_rules_enabled(),
			'rules'                => $this->get_conditional_rules(),
			'priority'             => $this->get_priority(),
			'location_extra'       => $this->get_location_extra(),
			'shortcode_attributes' => $this->get_shortcode_attributes(),
		);
	}

	/**
	 * Get the cloud id for this snippet.
	 *
	 * @return string
	 */
	public function get_cloud_id() {
		if ( ! isset( $this->cloud_id ) ) {
			if ( wpcode()->library_auth->has_auth() ) {
				$cloud_id = get_post_meta( $this->get_id(), '_wpcode_cloud_id', true );
				if ( empty( $cloud_id ) || ! is_array( $cloud_id ) ) {
					$cloud_id = array();
				}
				$auth_username  = wpcode()->library_auth->get_auth_username();
				$this->cloud_id = isset( $cloud_id[ $auth_username ] ) ? $cloud_id[ $auth_username ] : false;
			} else {
				$this->cloud_id = false;
			}
		}

		return $this->cloud_id;
	}

	/**
	 * Set the cloud id.
	 *
	 * @param string $cloud_id The cloud id to use.
	 *
	 * @return void
	 */
	public function set_cloud_id( $cloud_id ) {
		$this->cloud_id = $cloud_id;
	}

	/**
	 * Get the custom shortcode value.
	 *
	 * @return string
	 */
	public function get_custom_shortcode() {
		if ( ! isset( $this->custom_shortcode ) ) {
			$this->custom_shortcode = get_post_meta( $this->get_id(), '_wpcode_custom_shortcode', true );
		}

		return $this->custom_shortcode;
	}

	/**
	 * Get the device type for this snippet.
	 *
	 * @return string
	 */
	public function get_device_type() {
		if ( ! isset( $this->device_type ) ) {
			$this->device_type = get_post_meta( $this->get_id(), '_wpcode_device_type', true );
			if ( empty( $this->device_type ) ) {
				$this->device_type = 'any';
			}
		}

		return $this->device_type;
	}

	/**
	 * Get the schedule data for this snippet.
	 *
	 * @return array
	 */
	public function get_schedule() {
		if ( ! isset( $this->schedule ) ) {
			$this->schedule = wp_parse_args(
				get_post_meta( $this->get_id(), '_wpcode_schedule', true ),
				array(
					'start' => '',
					'end'   => '',
				)
			);
		}

		return $this->schedule;
	}

	/**
	 * Get the generator data for this snippet, if any.
	 *
	 * @return array|false
	 */
	public function get_generator_data() {
		if ( ! isset( $this->generator_data ) ) {
			$generator_data       = get_post_meta( $this->get_id(), '_wpcode_generator_data', true );
			$this->generator_data = empty( $generator_data ) ? false : $generator_data;
		}

		return $this->generator_data;
	}

	/**
	 * Get the generator name for this snippet.
	 *
	 * @return array|false
	 */
	public function get_generator() {
		if ( ! isset( $this->generator ) ) {
			$generator_name  = get_post_meta( $this->get_id(), '_wpcode_generator', true );
			$this->generator = empty( $generator_name ) ? false : $generator_name;
		}

		return $this->generator;
	}

	/**
	 * Check if the snippet is generated using a WPCode generator..
	 *
	 * @return bool
	 */
	public function is_generated() {
		return ! empty( $this->get_generator() );
	}

	/**
	 * Is this snippet scheduled?
	 *
	 * @return bool
	 */
	public function is_scheduled() {
		$schedule = $this->get_schedule();

		return ! empty( $schedule['start'] ) || ! empty( $schedule['end'] );
	}

	/**
	 * Extra data for the selected auto-insert location.
	 *
	 * @return array
	 */
	public function get_location_extra() {
		if ( ! isset( $this->location_extra ) ) {
			$this->location_extra = get_post_meta( $this->get_id(), '_wpcode_location_extra', true );
		}

		return $this->location_extra;
	}

	/**
	 * Load the shortcode attributes and return.
	 *
	 * @return array
	 */
	public function get_shortcode_attributes() {
		if ( ! isset( $this->shortcode_attributes ) ) {
			$attributes = get_post_meta( $this->get_id(), '_wpcode_shortcode_attributes', true );
			if ( ! is_array( $attributes ) ) {
				$attributes = array();
			}
			$this->shortcode_attributes = $attributes;
		}

		return $this->shortcode_attributes;
	}

	/**
	 * Set shortcode attribute value.
	 *
	 * @param string $key The attribute key.
	 * @param string $value The value for the attribute.
	 *
	 * @return void
	 */
	public function set_attribute( $key, $value ) {
		$this->attributes[ $key ] = $value;
	}
}
