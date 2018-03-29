<?php

/**
 * Manages a frontend inline CSS or JavaScript resource.
 *
 * If possible, the resource will be served as a static file for better performance. It can be
 * tied to a specific post or it can be 'global'. The resource can be output, static or inline,
 * to one of four locations on the page:
 *
 *   * `head-early`: right AFTER theme styles have been enqueued
 *   * `head`      : right BEFORE the theme and wp's inline custom css
 *   * `head-late` : right AFTER the theme and wp's inline custom css
 *   * `footer`    : in the footer
 *
 * The first time the class is instantiated, a static callback method will be registered for each
 * output location. Inside each callback, we'll iterate over any/all instances that are assigned
 * to the current output location and perform the following steps:
 *
 *   1. If a static file exists for the resource, go to the next step. Otherwise, try to create
 *      a static file for the resource if it has `data`. If it doesn't have `data`, assign it to
 *      the next output location and then move on to the next resource (continue).
 *   2. If a static file exists for the resource, enqueue it (via WP or manually) and then move on
 *      to the next resource (continue). If no static file exists, go to the next step.
 *   3. Output the resource inline.
 *
 * @since   2.0
 *
 * @package ET\Core
 */
class ET_Core_PageResource {
	/**
	 * Output locations.
	 *
	 * @var string[]
	 */
	private static $_OUTPUT_LOCATIONS = array(
		'head-early',
		'head',
		'head-late',
		'footer',
	);

	/**
	 * Resource owners.
	 *
	 * @var string[]
	 */
	private static $_OWNERS = array(
		'divi',
		'builder',
		'epanel',
		'extra',
		'core',
		'bloom',
		'monarch',
		'custom',
		'all',
	);

	/**
	 * All current resource paths.
	 *
	 * @var array[] {
	 *
	 *     @type string[] $post|$global {
	 *
	 *         @type string Filesystem path for a resource.
	 *     }
	 * }
	 */
	private static $_PATHS;

	/**
	 * Resource paths currently stored in the database.
	 *
	 * @var array[] {
	 *
	 *     @type string[] $post|$global {
	 *
	 *         @type string Filesystem path for a resource.
	 *     }
	 * }
	 */
	private static $_PATHS_IN_DB;

	/**
	 * Whether or not our resources array has been retrieved from the database.
	 *
	 * @var bool
	 */
	private static $_RESOURCES_LOADED = false;

	/**
	 * Resource scopes.
	 *
	 * @var string[]
	 */
	private static $_SCOPES = array(
		'global',
		'post',
	);

	/**
	 * Resource types.
	 *
	 * @var string[]
	 */
	private static $_TYPES = array(
		'style',
		'script',
	);

	/**
	 * Whether or not we have write access to the filesystem.
	 *
	 * @var bool
	 */
	private static $_can_write;

	private static $_onerror = 'et_core_page_resource_fallback(this, true)';
	private static $_onload  = 'et_core_page_resource_fallback(this)';

	/**
	 * All instances of this class.
	 *
	 * @var ET_Core_PageResource[] {
	 *
	 *     @type ET_Core_PageResource $slug
	 * }
	 */
	private static $_resources;

	/**
	 * All instances of this class organized by output location and sorted by priority.
	 *
	 * @var array[] {
	 *
	 *     @type array[] $location {@see self::$_OUTPUT_LOCATIONS} {
	 *
	 *         @type ET_Core_PageResource[] $priority {
	 *
	 *             @type ET_Core_PageResource $slug
	 *         }
	 *     }
	 * }
	 */
	private static $_resources_by_location;

	/**
	 * All instances of this class organized by scope.
	 *
	 * @var array[] {
	 *
	 *     @type ET_Core_PageResource[] $post|$global {
	 *
	 *         @type ET_Core_PageResource $slug
	 *     }
	 * }
	 */
	private static $_resources_by_scope;

	/**
	 * @var string
	 */
	public static $WP_CONTENT_DIR;

	/**
	 * @var string
	 */
	public static $current_output_location;

	/**
	 * @var ET_Core_Data_Utils
	 */
	public static $data_utils;

	/**
	 * @var \WP_Filesystem_Base|null
	 */
	public static $wpfs;

	/**
	 * The absolute path to the directory where the static resource will be stored.
	 *
	 * @var string
	 */
	public $BASE_PATH;

	/**
	 * The absolute path to the static resource on the server.
	 *
	 * @var string
	 */
	public $PATH;

	/**
	 * The absolute URL through which the static resource can be downloaded.
	 *
	 * @var string
	 */
	public $URL;

	/**
	 * The data/contents for/of the static resource sorted by priority.
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Whether or not this resource has been disabled.
	 *
	 * @var bool
	 */
	public $disabled;

	/**
	 * Whether or not the static resource file has been enqueued.
	 *
	 * @var bool
	 */
	public $enqueued;

	/**
	 * Whether or not this resource is forced inline.
	 *
	 * @var bool
	 */
	public $forced_inline;

	/**
	 * @var string
	 */
	public $filename;

	/**
	 * Whether or not the resource has already been output to the page inline.
	 *
	 * @var bool
	 */
	public $inlined;

	/**
	 * The owner of this instance.
	 *
	 * @var string
	 */
	public $owner;

	/**
	 * The id of the post to which this resource belongs.
	 *
	 * @var string
	 */
	public $post_id;

	/**
	 * The priority of this resource.
	 *
	 * @var int
	 */
	public $priority;

	/**
	 * A unique identifier for this resource.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * The resource type (style|script).
	 *
	 * @var string
	 */
	public $type;

	/**
	 * The output location during which this resource's static file should be generated.
	 *
	 * @var string
	 */
	public $write_file_location;

	/**
	 * ET_Core_PageResource constructor
	 *
	 * @param string     $owner    The owner of the instance (core|divi|builder|bloom|monarch|custom).
	 * @param string     $slug     A string that uniquely identifies the resource.
	 * @param string|int $post_id  The post id that the resource is associated with or `global`.
	 *                             If `null`, {@link get_the_ID()} will be used.
	 * @param string     $type     The resource type (style|script). Default: `style`.
	 * @param string     $location Where the resource should be output (head|footer). Default: `head`.
	 */
	public function __construct( $owner, $slug, $post_id = null, $priority = 10, $location = 'head-late', $type = 'style' ) {
		$this->owner    = self::_validate_property( 'owner', $owner );
		$this->post_id  = $post_id ? $post_id : et_core_page_resource_get_the_ID();

		$this->type     = self::_validate_property( 'type', $type );
		$this->location = self::_validate_property( 'location', $location );

		$this->write_file_location = $this->location;

		$slug           = sanitize_text_field( $slug );
		$this->filename = "et-{$this->owner}-{$slug}";
		$this->slug     = "{$this->filename}-cached-inline-{$this->type}s";

		$this->data     = array();
		$this->priority = $priority;

		self::startup();

		$this->_initialize_resource();
	}

	/**
	 * Activates the class
	 */
	public static function startup() {
		if ( null !== self::$_resources ) {
			// Class has already been initialized
			return;
		}

		self::$_resources = array();
		self::$data_utils = new ET_Core_Data_Utils();

		foreach ( self::$_OUTPUT_LOCATIONS as $location ) {
			self::$_resources_by_location[ $location ] = array();
		}

		foreach( self::$_SCOPES as $scope ) {
			self::$_resources_by_scope[ $scope ] = array();
			self::$_PATHS[ $scope ]              = array();
			self::$_PATHS_IN_DB[ $scope ]        = array();
		}

		self::$WP_CONTENT_DIR = self::$data_utils->normalize_path( WP_CONTENT_DIR );

		self::_register_callbacks();
		self::_setup_wp_filesystem();
	}

	/**
	 * Updates our resource array in the database if needed.
	 */
	public static function shutdown() {
		if ( empty( self::$_resources ) || is_admin() || 'wp_footer' !== current_action() ) {
			// Nothing to do
			return;
		}

		// Update our resources array in the database only if needed.
		$post_id              = et_core_page_resource_get_the_ID();
		$new_resources        = array_diff( self::$_PATHS['post'], self::$_PATHS_IN_DB['post'] );
		$new_resources_global = array_diff( self::$_PATHS['global'], self::$_PATHS_IN_DB['global'] );

		// Note: Calling update_*() is essentially the same as calling get_*() and then update_*().
		// We don't want that because calling get_*() results in new instances being created. So,
		// if we need to update the resource arrays in the database, we MUST delete them first.
		if ( $new_resources ) {
			delete_post_meta( $post_id, '_et_core_cached_page_resources' );
			update_post_meta( $post_id, '_et_core_cached_page_resources', self::$_resources_by_scope['post'] );
		}

		if ( $new_resources_global ) {
			et_delete_option( '_et_core_cached_page_resources' );
			et_update_option( '_et_core_cached_page_resources', self::$_resources_by_scope['global'] );
		}
	}

	protected static function _assign_output_location( $location, $resource ) {
		$priority_existed = isset( self::$_resources_by_location[ $location ][ $resource->priority ] );

		self::$_resources_by_location[ $location ][ $resource->priority ][ $resource->slug ] = $resource;

		if ( ! $priority_existed ) {
			// We've added a new priority to the list, so put them back in sorted order.
			ksort( self::$_resources_by_location[ $location ], SORT_NUMERIC );
		}
	}

	/**
	 * Enqueues static file for provided script resource.
	 *
	 * @param ET_Core_PageResource $resource
	 */
	protected static function _enqueue_script( $resource ) {
		$can_enqueue = 0 === did_action( 'wp_print_scripts' );

		if ( $can_enqueue ) {
			wp_enqueue_script( $resource->slug, set_url_scheme( $resource->URL ), array(), ET_CORE_VERSION, true );
		} else {
			printf(
				'<script id="%1$s" src="%2$s"></script>',
				esc_attr( $resource->slug ),
				esc_url( set_url_scheme( $resource->URL ) )
			);
		}

		$resource->enqueued = true;
	}

	/**
	 * Enqueues static file for provided style resource.
	 *
	 * @param ET_Core_PageResource $resource
	 */
	protected static function _enqueue_style( $resource ) {
		if ( 'footer' === self::$current_output_location ) {
			return;
		}

		$can_enqueue = 0 === did_action( 'wp_print_styles' );

		if ( $can_enqueue ) {
			wp_enqueue_style( $resource->slug, set_url_scheme( $resource->URL ) );
		} else {
			printf(
				'<link rel="stylesheet" id="%1$s" href="%2$s" onerror="%3$s" onload="%4$s" />',
				esc_attr( $resource->slug ),
				esc_url( set_url_scheme( $resource->URL ) ),
				self::$_onerror,
				self::$_onload
			);
		}

		$resource->enqueued = true;
	}

	/**
	 * Returns the next output location.
	 *
	 * @see self::$_OUTPUT_LOCATIONS
	 *
	 * @return string
	 */
	protected static function _get_next_output_location() {
		$current_index = array_search( self::$current_output_location, self::$_OUTPUT_LOCATIONS );

		if ( false === $current_index || ! is_int( $current_index ) ) {
			ET_Core_Logger::error( '$current_output_location is invalid!' );
		}

		$current_index += 1;

		return self::$_OUTPUT_LOCATIONS[ $current_index ];
	}

	/**
	 * Creates static resource files for an output location if needed.
	 *
	 * @param string $location {@link self::$_OUTPUT_LOCATIONS}
	 */
	protected static function _maybe_create_static_resources( $location ) {
		self::$current_output_location = $location;

		if ( ! self::$_can_write ) {
			return;
		}

		$sorted_resources = self::get_resources_by_output_location( $location );

		foreach ( $sorted_resources as $priority => $resources ) {
			foreach ( $resources as $slug => $resource ) {
				if ( $resource->forced_inline || $resource->has_file() ) {
					continue;
				}

				if ( $resource->write_file_location !== $location ) {
					// This resource's static file needs to be generated later on.
					self::_assign_output_location( $resource->write_file_location, $resource );
					continue;
				}

				$data = $resource->get_data( 'file' );

				if ( empty( $data ) && 'footer' !== $location ) {
					// This resource doesn't have any data yet so we'll assign it to the next output location.
					$next_location = self::_get_next_output_location();

					$resource->set_output_location( $next_location );

					continue;
				}

				if ( empty( $data ) ) {
					continue;
				}

				// Make sure directory exists.
				if ( ! self::$data_utils->ensure_directory_exists( $resource->BASE_PATH ) ) {
					self::$_can_write = false;
					return;
				}

				// Create the file
				if ( ! self::$wpfs->put_contents( $resource->PATH, $data ) ) {
					// There's no point in continuing, so bail.
					self::$_can_write = false;
					return;
				}

				if ( ! defined( 'DONOTCACHEPAGE' ) ) {
					define( 'DONOTCACHEPAGE', true );
				}
			}
		}
	}

	/**
	 * Enqueues static files for an output location if available.
	 *
	 * @param string $location {@link self::$_OUTPUT_LOCATIONS}
	 */
	protected static function _maybe_enqueue_static_resources( $location ) {
		$sorted_resources = self::get_resources_by_output_location( $location );

		foreach ( $sorted_resources as $priority => $resources ) {
			foreach ( $resources as $slug => $resource ) {
				if ( $resource->disabled ) {
					// Resource is disabled. Remove it from the queue.
					self::_unassign_output_location( $location, $resource );
					continue;
				}

				if ( $resource->forced_inline || ! $resource->URL || ! $resource->has_file() ) {
					continue;
				}

				if ( 'style' === $resource->type ) {
					self::_enqueue_style( $resource );
				} else if ( 'script' === $resource->type ) {
					self::_enqueue_script( $resource );
				}

				if ( $resource->enqueued ) {
					self::_unassign_output_location( $location, $resource );
				}
			}
		}
	}

	/**
	 * Outputs all non-enqueued resources for an output location inline.
	 *
	 * @param string $location {@link self::$_OUTPUT_LOCATIONS}
	 */
	protected static function _maybe_output_inline_resources( $location ) {
		$sorted_resources = self::get_resources_by_output_location( $location );

		foreach ( $sorted_resources as $priority => $resources ) {
			foreach ( $resources as $slug => $resource ) {
				if ( $resource->disabled ) {
					// Resource is disabled. Remove it from the queue.
					self::_unassign_output_location( $location, $resource );
					continue;
				}

				$data = $resource->get_data( 'inline' );

				$same_write_file_location = $resource->write_file_location === $resource->location;

				if ( empty( $data ) && 'footer' !== $location && $same_write_file_location ) {
					// This resource doesn't have any data yet so we'll assign it to the next output location.
					$next_location = self::_get_next_output_location();
					$resource->set_output_location( $next_location );
					continue;
				} else if ( empty( $data ) ) {
					continue;
				}

				printf(
					'<%1$s id="%2$s">%3$s</%1$s>',
					esc_html( $resource->type ),
					esc_attr( $resource->slug ),
					wp_strip_all_tags( $data )
				);

				if ( $same_write_file_location ) {
					// File wasn't created during this location's callback and it won't be created later
					$resource->inlined = true;

					// Don't allow inlined resources to be stored in the database
					self::_unregister_resource( $resource );
				}
			}
		}
	}

	/**
	 * Registers necessary callbacks.
	 */
	protected static function _register_callbacks() {
		$class = 'ET_Core_PageResource';

		// Get any existing resources from database as soon as possible.
		add_action( 'wp', array( $class, 'load_resources_from_database' ), 9 );

		// Output Location: head-early, right after theme styles have been enqueued.
		add_action( 'wp_enqueue_scripts', array( $class, 'head_early_output_cb' ), 11 );

		// Output Location: head, right BEFORE the theme and wp's custom css.
		add_action( 'wp_head', array( $class, 'head_output_cb' ), 99 );

		// Output Location: head-late, right AFTER the theme and wp's custom css.
		add_action( 'wp_head', array( $class, 'head_late_output_cb' ), 103 );

		// Output Location: footer
		add_action( 'wp_footer', array( $class, 'footer_output_cb' ), 20 );

		// Always delete cached resources for a post upon saving.
		add_action( 'save_post', array( $class, 'save_post_cb' ), 10, 3 );

		// Always delete cached resources for theme customizer upon saving.
		add_action( 'customize_save_after', array( $class, 'customize_save_after_cb') );

		// Schedule shutdown
		add_action( 'wp_footer', array( $class, 'shutdown' ), 100 );

		// Add fallback callbacks (lol) to link/script tags
		add_filter( 'style_loader_tag', array( $class, 'link_and_script_tags_filter_cb' ), 999, 4 );
	}

	/**
	 * Initializes the WPFilesystem class.
	 */
	protected static function _setup_wp_filesystem() {
		require_once ABSPATH . '/wp-admin/includes/file.php';

		if ( null !== self::$wpfs || ! self::can_write_to_filesystem() ) {
			return;
		}

		if ( ! WP_Filesystem( true ) ) {
			self::$_can_write = false;
			return;
		}

		global $wp_filesystem;
		self::$wpfs = $wp_filesystem;
	}

	/**
	 * Unassign a resource from an output location.
	 *
	 * @param string               $location {@link self::$_OUTPUT_LOCATIONS}
	 * @param ET_Core_PageResource $resource
	 */
	protected static function _unassign_output_location( $location, $resource ) {
		unset( self::$_resources_by_location[ $location ][ $resource->priority ][ $resource->slug ] );
	}

	protected static function _unregister_resource( $resource ) {
		$scope = 'global' === $resource->post_id ? 'global' : 'post';
		$key   = array_search( $resource->PATH, self::$_PATHS[ $scope ] );

		if ( false !== $key ) {
			unset( self::$_PATHS[ $scope ][ $key ] );
		}

		unset( self::$_resources_by_scope[ $scope ][ $resource->slug ] );
	}

	protected static function _validate_property( $property, $value ) {
		$valid_values = array(
			'location' => self::$_OUTPUT_LOCATIONS,
			'owner'    => self::$_OWNERS,
			'type'     => self::$_TYPES,
		);

		switch( $property ) {
			case 'path':
				$value    = self::$data_utils->normalize_path( realpath( $value ) );
				$is_valid = 0 === strpos( $value, self::$WP_CONTENT_DIR . '/cache/et' );
				break;
			case 'url':
				$content_url = content_url( '/cache/et' );
				$is_valid    = 0 === strpos( $value, set_url_scheme( $content_url, 'http' ) );
				$is_valid    = $is_valid ? $is_valid : 0 === strpos( $value, set_url_scheme( $content_url, 'https' ) );
				break;
			case 'post_id':
				$is_valid = 'global' === $value || 'all' === $value || is_numeric( $value );
				break;
			default:
				$is_valid = isset( $valid_values[ $property ] ) && in_array( $value, $valid_values[ $property ] );
				break;
		}

		return $is_valid ? $value : '';
	}

	/**
	 * Whether or not we are able to write to the filesystem.
	 *
	 * @return bool
	 */
	public static function can_write_to_filesystem() {
		if ( null === self::$_can_write ) {
			self::$_can_write = 'direct' === get_filesystem_method( array(), self::$WP_CONTENT_DIR );
		}

		return self::$_can_write;
	}

	/**
	 * Output Location: footer
	 * {@see 'wp_footer' (20) Allow third-party extensions some room to do what they do}
	 */
	public static function footer_output_cb() {
		self::_maybe_create_static_resources( 'footer' );
		self::_maybe_enqueue_static_resources( 'footer' );
		self::_maybe_output_inline_resources( 'footer' );
	}

	/**
	 * Returns the absolute path to our cache directory.
	 *
	 * @param string $path_type The desired path type. Accepts 'absolute', 'relative'. Default 'absolute'.
	 *
	 * @return string
	 */
	public static function get_cache_directory( $path_type = 'absolute' ) {
		if ( 'absolute' === $path_type ) {
			$cache_dir = self::$WP_CONTENT_DIR . '/cache/et';
		} else {
			$cache_dir = 'cache/et';
		}

		if ( is_multisite() ) {
			$site       = get_site();
			$network_id = $site->site_id;
			$site_id    = $site->blog_id;
			$cache_dir  = "${cache_dir}/{$network_id}/{$site_id}";
		}

		return $cache_dir;
	}

	/**
	 * Returns all current resources.
	 *
	 * @return array {@link self::$_resources}
	 */
	public static function get_resources() {
		return self::$_resources;
	}

	/**
	 * Returns the current resources for the provided output location, sorted by priority.
	 *
	 * @param string $location The desired output location {@see self::$_OUTPUT_LOCATIONS}.
	 *
	 * @return array[] {
	 *
	 *     @type ET_Core_PageResource[] $priority {
	 *
	 *         @type ET_Core_PageResource $slug Resource.
	 *         ...
	 *     }
	 *     ...
	 * }
	 */
	public static function get_resources_by_output_location( $location ) {
		return self::$_resources_by_location[ $location ];
	}

	/**
	 * Returns the current resources for the provided scope.
	 *
	 * @param string $scope The desired scope (post|global).
	 *
	 * @return ET_Core_PageResource[]
	 */
	public static function get_resources_by_scope( $scope ) {
		return self::$_resources_by_scope[ $scope ];
	}

	/**
	 * Output Location: head-early
	 * {@see 'wp_enqueue_scripts' (11) Should run right after the theme enqueues its styles.}
	 */
	public static function head_early_output_cb() {
		self::_maybe_create_static_resources( 'head-early' );
		self::_maybe_enqueue_static_resources( 'head-early' );
		self::_maybe_output_inline_resources( 'head-early' );
	}

	/**
	 * Output Location: head
	 * {@see 'wp_head' (99) Must run BEFORE the theme and WP's custom css callbacks.}
	 */
	public static function head_output_cb() {
		self::_maybe_create_static_resources( 'head' );
		self::_maybe_enqueue_static_resources( 'head' );
		self::_maybe_output_inline_resources( 'head' );
	}

	/**
	 * Output Location: head-late
	 * {@see 'wp_head' (103) Must run AFTER the theme and WP's custom css callbacks.}
	 */
	public static function head_late_output_cb() {
		self::_maybe_create_static_resources( 'head-late' );
		self::_maybe_enqueue_static_resources( 'head-late' );
		self::_maybe_output_inline_resources( 'head-late' );
	}

	/**
	 * Adds fallback handlers to the link and script tags of our page resources.
	 * {@see 'style_loader_tag'}
	 * {@see 'script_loader_tag'}
	 *
	 * @param string $tag
	 * @param string $handle
	 * @param string $href
	 * @param string $media
	 *
	 * @return string $tag
	 */
	public static function link_and_script_tags_filter_cb( $tag, $handle, $href, $media ) {
		if ( ! isset( self::$_PATHS[ $handle ] ) ) {
			return $tag;
		}

		if ( function_exists( 'et_get_option' ) && 'off' === et_get_option( 'et_pb_static_css_file', 'on' ) ) {
			return $tag;
		}

		$existing_onerror = "/(?<=onerror=')(.*?)(;?')/";
		$existing_onload  = "/(?<=onload=')(.*?)(;?')/"; // Internet Explorer :face_with_rolling_eyes:

		$onerror_callback = self::$_onerror;
		$onload_callback  = self::$_onload;

		$onerror_replacement = $onerror_callback . ';$1';
		$onload_replacement  = $onload_callback . ';$1';

		$tag = preg_replace( $existing_onerror, $onerror_replacement, $tag, 1, $onerror_replaced_count );
		$tag = preg_replace( $existing_onload, $onload_replacement, $tag, 1, $onload_replaced_count );

		if ( 1 === $onerror_replaced_count && 1 === $onload_replaced_count ) {
			return $tag;
		}

		if ( 1 !== $onerror_replaced_count ) {
			$tag = str_replace( '/>', "onerror='{$onerror_callback}' />", $tag );
		}

		if ( 1 !== $onload_replaced_count ) {
			$tag = str_replace( '/>', "onload='{$onload_callback}' />", $tag );
		}

		return $tag;
	}

	/**
	 * {@see 'wp' (9) Must run before builder is initialized}
	 */
	public static function load_resources_from_database() {
		if ( self::$_RESOURCES_LOADED ) {
			return;
		}

		$_ = get_post_meta( et_core_page_resource_get_the_ID(), '_et_core_cached_page_resources', true );
		$_ = function_exists( 'et_get_option' ) ? et_get_option( '_et_core_cached_page_resources' ) : '';

		self::$_RESOURCES_LOADED = true;
	}

	/**
	 * {@see 'customize_save_after'}
	 *
	 * @param WP_Customize_Manager $manager
	 */
	public static function customize_save_after_cb( $manager ) {
		self::remove_static_resources( 'all', 'all' );
	}

	/**
	 * {@see 'save_post'}
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 * @param bool    $update
	 */
	public static function save_post_cb( $post_id, $post, $update ) {
		if ( ! $update ) {
			return;
		}

		$post_types = array( 'post', 'page', 'project' );

		if ( function_exists( 'et_builder_get_builder_post_types' ) ) {
			$post_types = array_merge( $post_types, et_builder_get_builder_post_types() );
		}

		if ( ! in_array( $post->post_type, $post_types ) ) {
			return;
		}

		self::remove_static_resources( $post_id, 'all' );
	}

	/**
	 * Remove static resources for a post, or optionally all resources, if any exist.
	 *
	 * @param string|int $post_id
	 * @param string     $owner
	 */
	public static function remove_static_resources( $post_id, $owner = 'core', $force = false ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( ! self::can_write_to_filesystem() ) {
			return;
		}

		$post_id = self::_validate_property( 'post_id', $post_id );
		$owner   = self::_validate_property( 'owner', $owner );

		if ( '' === $owner || '' === $post_id ) {
			return;
		}

		$post_id = 'all' === $post_id ? '*' : $post_id;
		$owner   = 'all' === $owner ? '*' : $owner;

		$cache_dir = self::get_cache_directory();
		$pattern   = "{$cache_dir}/{$post_id}/et-{$owner}-*";

		$files = glob( $pattern );

		if ( $force ) {
			$more_files = glob( "{$cache_dir}/et-{$owner}-*" );

			if ( $more_files && is_array( $more_files ) ) {
				$files = is_array( $files ) ? array_merge( $files, $more_files ) : $more_files;
			}
		}

		foreach( (array) $files as $file ) {
			$file = self::$data_utils->normalize_path( $file );

			if ( 0 !== strpos( $file, self::$WP_CONTENT_DIR . '/cache/et' ) ) {
				// File is not located inside cache directory so skip it.
				continue;
			}

			if ( is_file( $file ) ) {
				self::$wpfs->delete( $file );
			}
		}

		// Remove empty directories
		self::$data_utils->remove_empty_directories( $cache_dir );

		// Clear cache managed by 3rd-party cache plugins
		$post_id = in_array( $post_id, array( '*', 'global' ) ) ? '' : $post_id;
		et_core_clear_wp_cache( $post_id );

		// Set our DONOTCACHEPAGE file for the next request.
		self::$wpfs->put_contents( $cache_dir . '/DONOTCACHEPAGE', '' );

		if ( $force ) {
			delete_option( 'et_core_page_resource_remove_all' );
		}
	}

	protected function _initialize_resource() {
		$time = (string) microtime( true );
		$time = str_replace( '.', '', $time );

		$file_extension = 'style' === $this->type ? '.min.css' : '.min.js';
		$absolute_path  = self::get_cache_directory();
		$relative_path  = self::get_cache_directory( 'relative' );

		$relative_path .= "/{$this->post_id}/{$this->filename}-{$time}{$file_extension}";
		$absolute_path .= "/{$this->post_id}/{$this->filename}-{$time}{$file_extension}";

		$this->BASE_PATH = self::$_can_write ? self::$data_utils->normalize_path( dirname( $absolute_path ) ) : '';
		$this->PATH      = self::$_can_write ? $absolute_path : '';
		$this->URL       = self::$_can_write ? content_url( $relative_path ) : '';

		$this->_register_resource();
	}

	protected function _register_resource() {
		$this->enqueued = false;
		$this->inlined  = false;

		$scope = 'global' === $this->post_id ? 'global' : 'post';

		self::$_PATHS[ $scope ][] = $this->PATH;
		self::$_resources[ $this->slug ] = $this;

		self::$_resources_by_scope[ $scope ][ $this->slug ] = $this;

		self::_assign_output_location( $this->location, $this );
	}

	public function __sleep() {
		// Only allow these properties to be serialized
		return array(
			'BASE_PATH',
			'PATH',
			'URL',
			'filename',
			'location',
			'owner',
			'post_id',
			'priority',
			'slug',
			'type',
		);
	}

	public function __wakeup() {
		if ( did_action( 'wp_footer' ) > 0 || ! $this->post_id ) {
			return;
		}

		if ( null === self::$_resources ) {
			self::startup();
		}

		if ( is_preview() || isset( $_GET['et_pb_preview'] ) ) {
			return;
		}

		$this->BASE_PATH = self::_validate_property( 'path', $this->BASE_PATH );
		$this->PATH      = self::_validate_property( 'path', $this->PATH );
		$this->URL       = self::_validate_property( 'url', $this->URL );
		$this->location  = self::_validate_property( 'location', $this->location );
		$this->owner     = self::_validate_property( 'owner', $this->owner );
		$this->post_id   = self::_validate_property( 'post_id', $this->post_id );
		$this->type      = self::_validate_property( 'type', $this->type );

		if ( ! $this->_has_required_properties() ) {
			return;
		}

		$scope = 'global' === $this->post_id ? 'global' : 'post';

		if ( in_array( $this->PATH, self::$_PATHS_IN_DB[ $scope ] ) || ! $this->has_file() ) {
			return;
		}

		self::$_PATHS_IN_DB[ $scope ][] = $this->PATH;

		$this->_register_resource();
	}

	protected function _has_required_properties() {
		$props = array();

		foreach ( $this->__sleep() as $prop ) {
			$props[] = $this->$prop;
		}

		return self::$data_utils->all( $props );
	}

	public function get_data( $context ) {
		$result = '';

		ksort( $this->data, SORT_NUMERIC );

		/**
		 * Filters the resource's data array.
		 *
		 * @since 3.0.52
		 *
		 * @param array[]              $data {
		 *
		 *     @type string[] $priority Resource data.
		 *     ...
		 * }
		 * @param string               $context  Where the data will be used. Accepts 'inline', 'file'.
		 * @param ET_Core_PageResource $resource The resource instance.
		 */
		$resource_data = apply_filters( 'et_core_page_resource_get_data', $this->data, $context, $this );

		foreach ( $resource_data as $priority => $data_part ) {
			foreach ( $data_part as $data ) {
				$result .= $data;
			}
		}

		return $result;
	}

	/**
	 * Whether or not a static resource exists on the filesystem for this instance.
	 *
	 * @return bool
	 */
	public function has_file() {
		if ( ! self::$wpfs || empty( $this->PATH ) || ! self::can_write_to_filesystem() ) {
			return false;
		}

		return self::$wpfs->exists( $this->PATH );
	}

	/**
	 * Set the resource's data.
	 *
	 * @param string $data
	 * @param int    $priority
	 */
	public function set_data( $data, $priority = 10 ) {
		if ( 'style' === $this->type ) {
			$data = et_core_data_utils_minify_css( $data );
			// Remove empty media queries
			//           @media   only..and  (feature:value)    { }
			$pattern = '/@media\s+([\w\s]+)?\([\w-]+:[\w\d-]+\)\{\s*\}/';
			$data    = preg_replace( $pattern, '', $data );
		}

		$this->data[ $priority ][] = trim( strip_tags( str_replace( '\n', '', $data ) ) );
	}

	public function set_output_location( $location ) {
		if ( ! self::_validate_property( 'location', $location ) ) {
			return;
		}

		$current_location = $this->location;

		self::_assign_output_location( $location, $this );
		self::_unassign_output_location( $current_location, $this );

		$this->location = $location;
	}
}
