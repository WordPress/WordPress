<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\XML_Sitemaps
 */

/**
 * Class WPSEO_Sitemaps.
 *
 * @todo This class could use a general description with some explanation on sitemaps. OR.
 */
class WPSEO_Sitemaps {

	/**
	 * Sitemap index identifier.
	 *
	 * @var string
	 */
	public const SITEMAP_INDEX_TYPE = '1';

	/**
	 * Content of the sitemap to output.
	 *
	 * @var string
	 */
	protected $sitemap = '';

	/**
	 * Flag to indicate if this is an invalid or empty sitemap.
	 *
	 * @var bool
	 */
	public $bad_sitemap = false;

	/**
	 * Whether or not the XML sitemap was served from a transient or not.
	 *
	 * @var bool
	 */
	private $transient = false;

	/**
	 * HTTP protocol to use in headers.
	 *
	 * @since 3.2
	 *
	 * @var string
	 */
	protected $http_protocol = 'HTTP/1.1';

	/**
	 * Holds the n variable.
	 *
	 * @var int
	 */
	private $current_page = 1;

	/**
	 * The sitemaps router.
	 *
	 * @since 3.2
	 *
	 * @var WPSEO_Sitemaps_Router
	 */
	public $router;

	/**
	 * The sitemap renderer.
	 *
	 * @since 3.2
	 *
	 * @var WPSEO_Sitemaps_Renderer
	 */
	public $renderer;

	/**
	 * The sitemap cache.
	 *
	 * @since 3.2
	 *
	 * @var WPSEO_Sitemaps_Cache
	 */
	public $cache;

	/**
	 * The sitemap providers.
	 *
	 * @since 3.2
	 *
	 * @var WPSEO_Sitemap_Provider[]
	 */
	public $providers;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'after_setup_theme', [ $this, 'init_sitemaps_providers' ] );
		add_action( 'after_setup_theme', [ $this, 'reduce_query_load' ], 99 );
		add_action( 'pre_get_posts', [ $this, 'redirect' ], 1 );
		add_action( 'wpseo_hit_sitemap_index', [ $this, 'hit_sitemap_index' ] );

		$this->router   = new WPSEO_Sitemaps_Router();
		$this->renderer = new WPSEO_Sitemaps_Renderer();
		$this->cache    = new WPSEO_Sitemaps_Cache();

		if ( ! empty( $_SERVER['SERVER_PROTOCOL'] ) ) {
			$this->http_protocol = sanitize_text_field( wp_unslash( $_SERVER['SERVER_PROTOCOL'] ) );
		}
	}

	/**
	 * Initialize sitemap providers classes.
	 *
	 * @since 5.3
	 *
	 * @return void
	 */
	public function init_sitemaps_providers() {

		$this->providers = [
			new WPSEO_Post_Type_Sitemap_Provider(),
			new WPSEO_Taxonomy_Sitemap_Provider(),
			new WPSEO_Author_Sitemap_Provider(),
		];

		$external_providers = apply_filters( 'wpseo_sitemaps_providers', [] );

		foreach ( $external_providers as $provider ) {
			if ( is_object( $provider ) && $provider instanceof WPSEO_Sitemap_Provider ) {
				$this->providers[] = $provider;
			}
		}
	}

	/**
	 * Check the current request URI, if we can determine it's probably an XML sitemap, kill loading the widgets.
	 *
	 * @return void
	 */
	public function reduce_query_load() {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$extension   = substr( $request_uri, -4 );
		if ( stripos( $request_uri, 'sitemap' ) !== false && in_array( $extension, [ '.xml', '.xsl' ], true ) ) {
			remove_all_actions( 'widgets_init' );
		}
	}

	/**
	 * Register your own sitemap. Call this during 'init'.
	 *
	 * @param string   $name              The name of the sitemap.
	 * @param callback $building_function Function to build your sitemap.
	 * @param string   $rewrite           Optional. Regular expression to match your sitemap with.
	 *
	 * @return void
	 */
	public function register_sitemap( $name, $building_function, $rewrite = '' ) {
		add_action( 'wpseo_do_sitemap_' . $name, $building_function );
		if ( $rewrite ) {
			Yoast_Dynamic_Rewrites::instance()->add_rule( $rewrite, 'index.php?sitemap=' . $name, 'top' );
		}
	}

	/**
	 * Register your own XSL file. Call this during 'init'.
	 *
	 * @since 1.4.23
	 *
	 * @param string   $name              The name of the XSL file.
	 * @param callback $building_function Function to build your XSL file.
	 * @param string   $rewrite           Optional. Regular expression to match your sitemap with.
	 *
	 * @return void
	 */
	public function register_xsl( $name, $building_function, $rewrite = '' ) {
		add_action( 'wpseo_xsl_' . $name, $building_function );
		if ( $rewrite ) {
			Yoast_Dynamic_Rewrites::instance()->add_rule( $rewrite, 'index.php?yoast-sitemap-xsl=' . $name, 'top' );
		}
	}

	/**
	 * Set the sitemap current page to allow creating partial sitemaps with WP-CLI
	 * in a one-off process.
	 *
	 * @param int $current_page The part that should be generated.
	 *
	 * @return void
	 */
	public function set_n( $current_page ) {
		if ( is_scalar( $current_page ) && intval( $current_page ) > 0 ) {
			$this->current_page = intval( $current_page );
		}
	}

	/**
	 * Set the sitemap content to display after you have generated it.
	 *
	 * @param string $sitemap The generated sitemap to output.
	 *
	 * @return void
	 */
	public function set_sitemap( $sitemap ) {
		$this->sitemap = $sitemap;
	}

	/**
	 * Set as true to make the request 404. Used stop the display of empty sitemaps or invalid requests.
	 *
	 * @param bool $is_bad Is this a bad request. True or false.
	 *
	 * @return void
	 */
	public function set_bad_sitemap( $is_bad ) {
		$this->bad_sitemap = (bool) $is_bad;
	}

	/**
	 * Prevent stupid plugins from running shutdown scripts when we're obviously not outputting HTML.
	 *
	 * @since 1.4.16
	 *
	 * @return void
	 */
	public function sitemap_close() {
		remove_all_actions( 'wp_footer' );
		exit();
	}

	/**
	 * Hijack requests for potential sitemaps and XSL files.
	 *
	 * @param WP_Query $query Main query instance.
	 *
	 * @return void
	 */
	public function redirect( $query ) {

		if ( ! $query->is_main_query() ) {
			return;
		}

		$yoast_sitemap_xsl = get_query_var( 'yoast-sitemap-xsl' );

		if ( ! empty( $yoast_sitemap_xsl ) ) {
			/*
			 * This is a method to provide the XSL via the home_url.
			 * Needed when the site_url and home_url are not the same.
			 * Loading the XSL needs to come from the same domain, protocol and port as the XML.
			 *
			 * Whenever home_url and site_url are the same, the file can be loaded directly.
			 */
			$this->xsl_output( $yoast_sitemap_xsl );
			$this->sitemap_close();

			return;
		}

		$type = get_query_var( 'sitemap' );

		if ( empty( $type ) ) {
			return;
		}

		if ( get_query_var( 'sitemap_n' ) === '1' || get_query_var( 'sitemap_n' ) === '0' ) {
			wp_safe_redirect( home_url( "/$type-sitemap.xml" ), 301, 'Yoast SEO' );
			exit;
		}

		$this->set_n( get_query_var( 'sitemap_n' ) );

		if ( ! $this->get_sitemap_from_cache( $type, $this->current_page ) ) {
			$this->build_sitemap( $type );
		}

		if ( $this->bad_sitemap ) {
			$query->set_404();
			status_header( 404 );

			return;
		}

		$this->output();
		$this->sitemap_close();
	}

	/**
	 * Try to get the sitemap from cache.
	 *
	 * @param string $type        Sitemap type.
	 * @param int    $page_number The page number to retrieve.
	 *
	 * @return bool If the sitemap has been retrieved from cache.
	 */
	private function get_sitemap_from_cache( $type, $page_number ) {

		$this->transient = false;

		if ( $this->cache->is_enabled() !== true ) {
			return false;
		}

		/**
		 * Fires before the attempt to retrieve XML sitemap from the transient cache.
		 *
		 * @param WPSEO_Sitemaps $sitemaps Sitemaps object.
		 */
		do_action( 'wpseo_sitemap_stylesheet_cache_' . $type, $this );

		$sitemap_cache_data = $this->cache->get_sitemap_data( $type, $page_number );

		// No cache was found, refresh it because cache is enabled.
		if ( empty( $sitemap_cache_data ) ) {
			return $this->refresh_sitemap_cache( $type, $page_number );
		}

		// Cache object was found, parse information.
		$this->transient = true;

		$this->sitemap     = $sitemap_cache_data->get_sitemap();
		$this->bad_sitemap = ! $sitemap_cache_data->is_usable();

		return true;
	}

	/**
	 * Build and save sitemap to cache.
	 *
	 * @param string $type        Sitemap type.
	 * @param int    $page_number The page number to save to.
	 *
	 * @return bool
	 */
	private function refresh_sitemap_cache( $type, $page_number ) {
		$this->set_n( $page_number );
		$this->build_sitemap( $type );

		return $this->cache->store_sitemap( $type, $page_number, $this->sitemap, ! $this->bad_sitemap );
	}

	/**
	 * Attempts to build the requested sitemap.
	 *
	 * Sets $bad_sitemap if this isn't for the root sitemap, a post type or taxonomy.
	 *
	 * @param string $type The requested sitemap's identifier.
	 *
	 * @return void
	 */
	public function build_sitemap( $type ) {

		/**
		 * Filter the type of sitemap to build.
		 *
		 * @param string $type Sitemap type, determined by the request.
		 */
		$type = apply_filters( 'wpseo_build_sitemap_post_type', $type );

		if ( $type === '1' ) {
			$this->build_root_map();

			return;
		}

		$entries_per_page = $this->get_entries_per_page();

		foreach ( $this->providers as $provider ) {
			if ( ! $provider->handles_type( $type ) ) {
				continue;
			}

			try {
				$links = $provider->get_sitemap_links( $type, $entries_per_page, $this->current_page );
			} catch ( OutOfBoundsException $exception ) {
				$this->bad_sitemap = true;

				return;
			}

			$this->sitemap = $this->renderer->get_sitemap( $links, $type, $this->current_page );

			return;
		}

		if ( has_action( 'wpseo_do_sitemap_' . $type ) ) {
			/**
			 * Fires custom handler, if hooked to generate sitemap for the type.
			 */
			do_action( 'wpseo_do_sitemap_' . $type );

			return;
		}

		$this->bad_sitemap = true;
	}

	/**
	 * Build the root sitemap (example.com/sitemap_index.xml) which lists sub-sitemaps for other content types.
	 *
	 * @return void
	 */
	public function build_root_map() {

		$links            = [];
		$entries_per_page = $this->get_entries_per_page();

		foreach ( $this->providers as $provider ) {
			$links = array_merge( $links, $provider->get_index_links( $entries_per_page ) );
		}

		/**
		 * Filter the sitemap links array before the index sitemap is built.
		 *
		 * @param array  $links Array of sitemap links
		 */
		$links = apply_filters( 'wpseo_sitemap_index_links', $links );

		if ( empty( $links ) ) {
			$this->bad_sitemap = true;
			$this->sitemap     = '';

			return;
		}

		$this->sitemap = $this->renderer->get_index( $links );
	}

	/**
	 * Spits out the XSL for the XML sitemap.
	 *
	 * @since 1.4.13
	 *
	 * @param string $type Type to output.
	 *
	 * @return void
	 */
	public function xsl_output( $type ) {

		if ( $type !== 'main' ) {

			/**
			 * Fires for the output of XSL for XML sitemaps, other than type "main".
			 */
			do_action( 'wpseo_xsl_' . $type );

			return;
		}

		header( $this->http_protocol . ' 200 OK', true, 200 );
		// Prevent the search engines from indexing the XML Sitemap.
		header( 'X-Robots-Tag: noindex, follow', true );
		header( 'Content-Type: text/xml' );

		// Make the browser cache this file properly.
		$expires = YEAR_IN_SECONDS;
		header( 'Pragma: public' );
		header( 'Cache-Control: max-age=' . $expires );
		header( 'Expires: ' . YoastSEO()->helpers->date->format_timestamp( ( time() + $expires ), 'D, d M Y H:i:s' ) . ' GMT' );

		// Don't use WP_Filesystem() here because that's not initialized yet. See https://yoast.atlassian.net/browse/QAK-2043.
		readfile( WPSEO_PATH . 'css/main-sitemap.xsl' );
	}

	/**
	 * Spit out the generated sitemap.
	 *
	 * @return void
	 */
	public function output() {
		$this->send_headers();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaping sitemap as either xml or html results in empty document.
		echo $this->renderer->get_output( $this->sitemap );
	}

	/**
	 * Makes a request to the sitemap index to cache it before the arrival of the search engines.
	 *
	 * @return void
	 */
	public function hit_sitemap_index() {
		if ( ! $this->cache->is_enabled() ) {
			return;
		}

		wp_remote_get( WPSEO_Sitemaps_Router::get_base_url( 'sitemap_index.xml' ) );
	}

	/**
	 * Get the GMT modification date for the last modified post in the post type.
	 *
	 * @since 3.2
	 *
	 * @param string|array $post_types Post type or array of types.
	 * @param bool         $return_all Flag to return array of values.
	 *
	 * @return string|array|false
	 */
	public static function get_last_modified_gmt( $post_types, $return_all = false ) {

		global $wpdb;

		static $post_type_dates = null;

		if ( ! is_array( $post_types ) ) {
			$post_types = [ $post_types ];
		}

		foreach ( $post_types as $post_type ) {
			if ( ! isset( $post_type_dates[ $post_type ] ) ) { // If we hadn't seen post type before. R.
				$post_type_dates = null;
				break;
			}
		}

		if ( $post_type_dates === null ) {

			$post_type_dates = [];
			$post_type_names = WPSEO_Post_Type::get_accessible_post_types();

			if ( ! empty( $post_type_names ) ) {
				$post_statuses = array_map( 'esc_sql', self::get_post_statuses() );
				$replacements  = array_merge(
					[
						'post_type',
						'post_modified_gmt',
						'date',
						$wpdb->posts,
						'post_status',
					],
					$post_statuses,
					[ 'post_type' ],
					array_keys( $post_type_names ),
					[
						'post_type',
						'date',
					]
				);

				//phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We need to use a direct query here.
				//phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
				$dates = $wpdb->get_results(
					//phpcs:disable WordPress.DB.PreparedSQLPlaceholders -- %i placeholder is still not recognized.
					$wpdb->prepare(
						'
					SELECT %i, MAX(%i) AS %i
					FROM %i
					WHERE %i IN (' . implode( ', ', array_fill( 0, count( $post_statuses ), '%s' ) ) . ')
						AND %i IN (' . implode( ', ', array_fill( 0, count( $post_type_names ), '%s' ) ) . ')
					GROUP BY %i
					ORDER BY %i DESC
				',
						$replacements
					)
				);

				foreach ( $dates as $obj ) {
					$post_type_dates[ $obj->post_type ] = $obj->date;
				}
			}
		}

		$dates = array_intersect_key( $post_type_dates, array_flip( $post_types ) );

		if ( count( $dates ) > 0 ) {
			if ( $return_all ) {
				return $dates;
			}

			return max( $dates );
		}

		return false;
	}

	/**
	 * Get the modification date for the last modified post in the post type.
	 *
	 * @param array $post_types Post types to get the last modification date for.
	 *
	 * @return string
	 */
	public function get_last_modified( $post_types ) {
		return YoastSEO()->helpers->date->format( self::get_last_modified_gmt( $post_types ) );
	}

	/**
	 * Get the maximum number of entries per XML sitemap.
	 *
	 * @return int The maximum number of entries.
	 */
	protected function get_entries_per_page() {
		/**
		 * Filter the maximum number of entries per XML sitemap.
		 *
		 * After changing the output of the filter, make sure that you disable and enable the
		 * sitemaps to make sure the value is picked up for the sitemap cache.
		 *
		 * @param int $entries The maximum number of entries per XML sitemap.
		 */
		$entries = (int) apply_filters( 'wpseo_sitemap_entries_per_page', 1000 );

		return $entries;
	}

	/**
	 * Get post statuses for post_type or the root sitemap.
	 *
	 * @since 10.2
	 *
	 * @param string $type Provide a type for a post_type sitemap, SITEMAP_INDEX_TYPE for the root sitemap.
	 *
	 * @return array List of post statuses.
	 */
	public static function get_post_statuses( $type = self::SITEMAP_INDEX_TYPE ) {
		/**
		 * Filter post status list for sitemap query for the post type.
		 *
		 * @param array  $post_statuses Post status list, defaults to array( 'publish' ).
		 * @param string $type          Post type or SITEMAP_INDEX_TYPE.
		 */
		$post_statuses = apply_filters( 'wpseo_sitemap_post_statuses', [ 'publish' ], $type );

		if ( ! is_array( $post_statuses ) || empty( $post_statuses ) ) {
			$post_statuses = [ 'publish' ];
		}

		if ( ( $type === self::SITEMAP_INDEX_TYPE || $type === 'attachment' )
			&& ! in_array( 'inherit', $post_statuses, true )
		) {
			$post_statuses[] = 'inherit';
		}

		return $post_statuses;
	}

	/**
	 * Sends all the required HTTP Headers.
	 *
	 * @return void
	 */
	private function send_headers() {
		if ( headers_sent() ) {
			return;
		}

		$headers = [
			$this->http_protocol . ' 200 OK' => 200,
			// Prevent the search engines from indexing the XML Sitemap.
			'X-Robots-Tag: noindex, follow'  => '',
			'Content-Type: text/xml; charset=' . esc_attr( $this->renderer->get_output_charset() ) => '',
		];

		/**
		 * Filter the HTTP headers we send before an XML sitemap.
		 *
		 * @param array  $headers The HTTP headers we're going to send out.
		 */
		$headers = apply_filters( 'wpseo_sitemap_http_headers', $headers );

		foreach ( $headers as $header => $status ) {
			if ( is_numeric( $status ) ) {
				header( $header, true, $status );
				continue;
			}
			header( $header, true );
		}
	}
}
