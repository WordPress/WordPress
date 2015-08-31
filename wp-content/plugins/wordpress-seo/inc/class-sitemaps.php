<?php
/**
 * @package WPSEO\XML_Sitemaps
 */

/**
 * Class WPSEO_Sitemaps
 *
 * @todo: [JRF => whomever] If at all possible, move the adding of rewrite rules, actions and filters
 * elsewhere and only load this file when an actual sitemap is being requested.
 */
class WPSEO_Sitemaps {
	/**
	 * Content of the sitemap to output.
	 *
	 * @var string $sitemap
	 */
	protected $sitemap = '';

	/**
	 * XSL stylesheet for styling a sitemap for web browsers
	 *
	 * @var string $stylesheet
	 */
	private $stylesheet = '';

	/**
	 * Flag to indicate if this is an invalid or empty sitemap.
	 *
	 * @var bool $bad_sitemap
	 */
	public $bad_sitemap = false;

	/**
	 * Whether or not the XML sitemap was served from a transient or not.
	 *
	 * @var bool $transient
	 */
	private $transient = false;

	/**
	 * The maximum number of entries per sitemap page
	 *
	 * @var int $max_entries
	 */
	private $max_entries;

	/**
	 * Holds the post type's newest publish dates
	 *
	 * @var array $post_type_dates
	 */
	private $post_type_dates;

	/**
	 * Holds the Yoast SEO options
	 *
	 * @var array $options
	 */
	private $options = array();

	/**
	 * Holds the n variable
	 *
	 * @var int $n
	 */
	private $n = 1;

	/**
	 * Holds the home_url() value to speed up loops
	 *
	 * @var string $home_url
	 */
	private $home_url = '';

	/**
	 * Holds the get_bloginfo( 'charset' ) value to reuse for performance
	 *
	 * @var string $charset
	 */
	private $charset = '';

	/**
	 * @var WPSEO_Sitemap_Timezone
	 */
	private $timezone;

	/**
	 * Class constructor
	 */
	function __construct() {
		if ( ! defined( 'ENT_XML1' ) ) {
			define( 'ENT_XML1', 16 );
		}

		add_action( 'after_setup_theme', array( $this, 'reduce_query_load' ), 99 );

		add_action( 'pre_get_posts', array( $this, 'redirect' ), 1 );
		add_filter( 'redirect_canonical', array( $this, 'canonical' ) );
		add_action( 'wpseo_hit_sitemap_index', array( $this, 'hit_sitemap_index' ) );
		add_filter( 'wpseo_sitemap_exclude_author', array( $this, 'user_sitemap_remove_excluded_authors' ), 8 );

		// Default stylesheet.
		$this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . preg_replace( '/(^http[s]?:)/', '', esc_url( home_url( 'main-sitemap.xsl' ) ) ) . '"?>';

		$this->options     = WPSEO_Options::get_all();
		$this->max_entries = $this->options['entries-per-page'];
		$this->home_url    = home_url();
		$this->charset     = get_bloginfo( 'charset' );

		$this->timezone    = new WPSEO_Sitemap_Timezone();

	}

	/**
	 * Check the current request URI, if we can determine it's probably an XML sitemap, kill loading the widgets
	 */
	public function reduce_query_load() {

		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$request_uri = $_SERVER['REQUEST_URI'];
		$extension   = substr( $request_uri, -4 );

		if ( false !== stripos( $request_uri, 'sitemap' ) && ( in_array( $extension, array( '.xml', '.xsl' ) ) ) ) {
			remove_all_actions( 'widgets_init' );
		}
	}

	/**
	 * This query invalidates the main query on purpose so it returns nice and quickly
	 *
	 * @param string $where
	 *
	 * @deprecated The relevant sitemap code now hijacks main query before this filter can act on it.
	 *
	 * @return string
	 */
	function invalidate_main_query( $where ) {

		return $where;
	}


	/**
	 * Returns the server HTTP protocol to use for output, if it's set.
	 *
	 * @return string
	 */
	private function http_protocol() {
		return ( isset( $_SERVER['SERVER_PROTOCOL'] ) && $_SERVER['SERVER_PROTOCOL'] !== '' ) ? sanitize_text_field( $_SERVER['SERVER_PROTOCOL'] ) : 'HTTP/1.1';
	}

	/**
	 * Register your own sitemap. Call this during 'init'.
	 *
	 * @param string   $name     The name of the sitemap.
	 * @param callback $function Function to build your sitemap.
	 * @param string   $rewrite  Optional. Regular expression to match your sitemap with.
	 */
	function register_sitemap( $name, $function, $rewrite = '' ) {
		add_action( 'wpseo_do_sitemap_' . $name, $function );
		if ( ! empty( $rewrite ) ) {
			add_rewrite_rule( $rewrite, 'index.php?sitemap=' . $name, 'top' );
		}
	}

	/**
	 * Register your own XSL file. Call this during 'init'.
	 *
	 * @param string   $name     The name of the XSL file.
	 * @param callback $function Function to build your XSL file.
	 * @param string   $rewrite  Optional. Regular expression to match your sitemap with.
	 */
	function register_xsl( $name, $function, $rewrite = '' ) {
		add_action( 'wpseo_xsl_' . $name, $function );
		if ( ! empty( $rewrite ) ) {
			add_rewrite_rule( $rewrite, 'index.php?xsl=' . $name, 'top' );
		}
	}

	/**
	 * Set the sitemap n to allow creating partial sitemaps with wp-cli
	 * in an one-off process.
	 *
	 * @param integer $n The part that should be generated.
	 */
	public function set_n( $n ) {
		if ( is_scalar( $n ) && intval( $n ) > 0 ) {
			$this->n = intval( $n );
		}
	}

	/**
	 * Set the sitemap content to display after you have generated it.
	 *
	 * @param string $sitemap The generated sitemap to output.
	 */
	function set_sitemap( $sitemap ) {
		$this->sitemap = $sitemap;
	}

	/**
	 * Set a custom stylesheet for this sitemap. Set to empty to just remove
	 * the default stylesheet.
	 *
	 * @param string $stylesheet Full xml-stylesheet declaration.
	 */
	public function set_stylesheet( $stylesheet ) {
		$this->stylesheet = $stylesheet;
	}

	/**
	 * Set as true to make the request 404. Used stop the display of empty sitemaps or
	 * invalid requests.
	 *
	 * @param bool $bool Is this a bad request. True or false.
	 */
	function set_bad_sitemap( $bool ) {
		$this->bad_sitemap = (bool) $bool;
	}

	/**
	 * Prevent stupid plugins from running shutdown scripts when we're obviously not outputting HTML.
	 *
	 * @since 1.4.16
	 */
	function sitemap_close() {
		remove_all_actions( 'wp_footer' );
		die();
	}

	/**
	 * Hijack requests for potential sitemaps and XSL files.
	 *
	 * @param \WP_Query $query
	 */
	function redirect( $query ) {

		if ( ! $query->is_main_query() ) {
			return;
		}

		$xsl = get_query_var( 'xsl' );
		if ( ! empty( $xsl ) ) {
			$this->xsl_output( $xsl );
			$this->sitemap_close();
		}

		$type = get_query_var( 'sitemap' );
		if ( empty( $type ) ) {
			return;
		}

		$this->set_n( get_query_var( 'sitemap_n' ) );

		/**
		 * Filter: 'wpseo_enable_xml_sitemap_transient_caching' - Allow disabling the transient cache
		 *
		 * @api bool $unsigned Enable cache or not, defaults to true
		 */
		$caching = apply_filters( 'wpseo_enable_xml_sitemap_transient_caching', true );

		if ( $caching ) {
			do_action( 'wpseo_sitemap_stylesheet_cache_' . $type, $this );
			$this->sitemap = get_transient( 'wpseo_sitemap_cache_' . $type . '_' . $this->n );
		}

		if ( ! $this->sitemap || '' == $this->sitemap ) {
			$this->build_sitemap( $type );

			// 404 for invalid or emtpy sitemaps.
			if ( $this->bad_sitemap ) {
				$GLOBALS['wp_query']->set_404();
				status_header( 404 );

				return;
			}

			if ( $caching ) {
				set_transient( 'wpseo_sitemap_cache_' . $type . '_' . $this->n, $this->sitemap, DAY_IN_SECONDS );
			}
		}
		else {
			$this->transient = true;
		}

		$this->output();
		$this->sitemap_close();
	}

	/**
	 * Attempt to build the requested sitemap. Sets $bad_sitemap if this isn't
	 * for the root sitemap, a post type or taxonomy.
	 *
	 * @param string $type The requested sitemap's identifier.
	 */
	function build_sitemap( $type ) {

		$type = apply_filters( 'wpseo_build_sitemap_post_type', $type );

		if ( $type == 1 ) {
			$this->build_root_map();
		}
		elseif ( post_type_exists( $type ) ) {
			$this->build_post_type_map( $type );
		}
		elseif ( $tax = get_taxonomy( $type ) ) {
			$this->build_tax_map( $tax );
		}
		elseif ( $type == 'author' ) {
			$this->build_user_map();
		}
		elseif ( has_action( 'wpseo_do_sitemap_' . $type ) ) {
			do_action( 'wpseo_do_sitemap_' . $type );
		}
		else {
			$this->bad_sitemap = true;
		}
	}

	/**
	 * Build the root sitemap -- example.com/sitemap_index.xml -- which lists sub-sitemaps
	 * for other content types.
	 */
	function build_root_map() {

		global $wpdb;

		$this->sitemap = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		// Reference post type specific sitemaps.
		$post_types = get_post_types( array( 'public' => true ) );
		if ( is_array( $post_types ) && $post_types !== array() ) {

			foreach ( $post_types as $post_type ) {
				if ( isset( $this->options[ 'post_types-' . $post_type . '-not_in_sitemap' ] ) && $this->options[ 'post_types-' . $post_type . '-not_in_sitemap' ] === true ) {
					continue;
				}
				else {
					if ( apply_filters( 'wpseo_sitemap_exclude_post_type', false, $post_type ) ) {
						continue;
					}
				}

				// Using same filters for filtering join and where parts of the query.
				$join_filter  = apply_filters( 'wpseo_typecount_join', '', $post_type );
				$where_filter = apply_filters( 'wpseo_typecount_where', '', $post_type );

				// Using the same query with build_post_type_map($post_type) function to count number of posts.
				$query = $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts {$join_filter} WHERE post_status IN ('publish','inherit') AND post_password = '' AND post_date != '0000-00-00 00:00:00' AND post_type = %s " . $where_filter, $post_type );

				$count = $wpdb->get_var( $query );
				if ( $count == 0  ) {
					continue;
				}

				$n = ( $count > $this->max_entries ) ? (int) ceil( $count / $this->max_entries ) : 1;
				for ( $i = 0; $i < $n; $i ++ ) {
					$count = ( $n > 1 ) ? ( $i + 1 ) : '';

					if ( empty( $count ) || $count == $n ) {
						$date = $this->get_last_modified( $post_type );
					}
					else {
						if ( ! isset( $all_dates ) ) {
							$all_dates = $wpdb->get_col( $wpdb->prepare( "SELECT post_modified_gmt FROM (SELECT @rownum:=@rownum+1 rownum, $wpdb->posts.post_modified_gmt FROM (SELECT @rownum:=0) r, $wpdb->posts WHERE post_status IN ('publish','inherit') AND post_type = %s ORDER BY post_modified_gmt ASC) x WHERE rownum %%%d=0", $post_type, $this->max_entries ) );
						}
						$date = $this->timezone->get_datetime_with_timezone( $all_dates[ $i ] );
						unset( $all_dates );
					}

					$this->sitemap .= '<sitemap>' . "\n";
					$this->sitemap .= '<loc>' . wpseo_xml_sitemaps_base_url( $post_type . '-sitemap' . $count . '.xml' ) . '</loc>' . "\n";
					$this->sitemap .= '<lastmod>' . htmlspecialchars( $date ) . '</lastmod>' . "\n";
					$this->sitemap .= '</sitemap>' . "\n";
				}
				unset( $count, $n, $i, $date );
			}
		}
		unset( $post_types, $post_type, $join_filter, $where_filter, $query );

		// Reference taxonomy specific sitemaps.
		$taxonomies     = get_taxonomies( array( 'public' => true ), 'objects' );
		$taxonomy_names = array_keys( $taxonomies );

		if ( is_array( $taxonomy_names ) && $taxonomy_names !== array() ) {
			foreach ( $taxonomy_names as $tax ) {
				if ( in_array( $tax, array( 'link_category', 'nav_menu', 'post_format' ) ) ) {
					unset( $taxonomy_names[ $tax ], $taxonomies[ $tax ] );
					continue;
				}

				if ( apply_filters( 'wpseo_sitemap_exclude_taxonomy', false, $tax ) ) {
					unset( $taxonomy_names[ $tax ], $taxonomies[ $tax ] );
					continue;
				}

				if ( isset( $this->options[ 'taxonomies-' . $tax . '-not_in_sitemap' ] ) && $this->options[ 'taxonomies-' . $tax . '-not_in_sitemap' ] === true ) {
					unset( $taxonomy_names[ $tax ], $taxonomies[ $tax ] );
					continue;
				}
			}
			unset( $tax );

			// Retrieve all the taxonomies and their terms so we can do a proper count on them.
			$hide_empty         = ( apply_filters( 'wpseo_sitemap_exclude_empty_terms', true, $taxonomy_names ) ) ? 'count != 0 AND' : '';
			$query              = "SELECT taxonomy, term_id FROM $wpdb->term_taxonomy WHERE $hide_empty taxonomy IN ('" . implode( "','", $taxonomy_names ) . "');";
			$all_taxonomy_terms = $wpdb->get_results( $query );
			$all_taxonomies     = array();
			foreach ( $all_taxonomy_terms as $obj ) {
				$all_taxonomies[ $obj->taxonomy ][] = $obj->term_id;
			}
			unset( $hide_empty, $query, $all_taxonomy_terms, $obj );

			foreach ( $taxonomies as $tax_name => $tax ) {

				if ( ! isset( $all_taxonomies[ $tax_name ] ) ) { // No eligible terms found.
					continue;
				}

				$steps = $this->max_entries;
				$count = ( isset( $all_taxonomies[ $tax_name ] ) ) ? count( $all_taxonomies[ $tax_name ] ) : 1;
				$n     = ( $count > $this->max_entries ) ? (int) ceil( $count / $this->max_entries ) : 1;

				for ( $i = 0; $i < $n; $i ++ ) {
					$count = ( $n > 1 ) ? ( $i + 1 ) : '';

					if ( ! is_array( $tax->object_type ) || count( $tax->object_type ) == 0 ) {
						continue;
					}

					if ( ( empty( $count ) || $count == $n ) ) {
						$date = $this->get_last_modified( $tax->object_type );
					}
					else {
						$terms = array_splice( $all_taxonomies[ $tax_name ], 0, $steps );
						if ( ! $terms ) {
							continue;
						}

						$args  = array(
							'post_type' => $tax->object_type,
							'tax_query' => array(
								array(
									'taxonomy' => $tax_name,
									'terms'    => $terms,
								),
							),
							'orderby'   => 'modified',
							'order'     => 'DESC',
						);
						$query = new WP_Query( $args );

						$date = '';
						if ( $query->have_posts() ) {
							$date = $this->timezone->get_datetime_with_timezone( $query->posts[0]->post_modified_gmt );
						}
						else {
							$date = $this->get_last_modified( $tax->object_type );
						}
						unset( $terms, $args, $query );
					}

					$this->sitemap .= '<sitemap>' . "\n";
					$this->sitemap .= '<loc>' . wpseo_xml_sitemaps_base_url( $tax_name . '-sitemap' . $count . '.xml' ) . '</loc>' . "\n";
					$this->sitemap .= '<lastmod>' . htmlspecialchars( $date ) . '</lastmod>' . "\n";
					$this->sitemap .= '</sitemap>' . "\n";
				}
				unset( $steps, $count, $n, $i, $date );
			}
		}
		unset( $taxonomies, $taxonomy_names, $all_taxonomies, $tax_name, $tax );

		if ( $this->options['disable-author'] === false && $this->options['disable_author_sitemap'] === false ) {

			// Reference user profile specific sitemaps.
			$users = get_users( array( 'who' => 'authors' ) );
			$users = apply_filters( 'wpseo_sitemap_exclude_author', $users );
			$users = wp_list_pluck( $users, 'ID' );

			$count = count( $users );
			$n     = ( $count > 0 ) ? 1 : 0;

			if ( $count > $this->max_entries ) {
				$n = (int) ceil( $count / $this->max_entries );
			};

			for ( $i = 0; $i < $n; $i ++ ) {
				$count = ( $n > 1 ) ? ( $i + 1 ) : '';

				// Must use custom raw query because WP User Query does not support ordering by usermeta.
				// Retrieve the newest updated profile timestamp overall.
				// TODO order by usermeta supported since WP 3.7, update implementation? R.
				$date_query = "
						SELECT mt1.meta_value FROM $wpdb->users
						INNER JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id)
						INNER JOIN $wpdb->usermeta AS mt1 ON ($wpdb->users.ID = mt1.user_id) WHERE 1=1
						AND ( ($wpdb->usermeta.meta_key = %s AND CAST($wpdb->usermeta.meta_value AS CHAR) != '0')
						AND mt1.meta_key = '_yoast_wpseo_profile_updated' ) ORDER BY mt1.meta_value
					";

				if ( empty( $count ) || $count == $n ) {
					$date = $wpdb->get_var(
						$wpdb->prepare(
							$date_query . ' ASC LIMIT 1',
							$wpdb->get_blog_prefix() . 'user_level'
						)
					);

					// Retrieve the newest updated profile timestamp by an offset.
				}
				else {
					$date = $wpdb->get_var(
						$wpdb->prepare(
							$date_query . ' DESC LIMIT 1 OFFSET %d',
							$wpdb->get_blog_prefix() . 'user_level',
							( ( $this->max_entries * ( $i + 1 ) ) - 1 )
						)
					);
				}
				$date = $this->timezone->get_datetime_with_timezone( '@' . $date );

				$this->sitemap .= '<sitemap>' . "\n";
				$this->sitemap .= '<loc>' . wpseo_xml_sitemaps_base_url( 'author-sitemap' . $count . '.xml' ) . '</loc>' . "\n";
				$this->sitemap .= '<lastmod>' . htmlspecialchars( $date ) . '</lastmod>' . "\n";
				$this->sitemap .= '</sitemap>' . "\n";
			}
			unset( $users, $count, $n, $i, $date_query, $date );
		}

		// Allow other plugins to add their sitemaps to the index.
		$this->sitemap .= apply_filters( 'wpseo_sitemap_index', '' );
		$this->sitemap .= '</sitemapindex>';
	}

	/**
	 * Function to dynamically filter the change frequency
	 *
	 * @param string $filter  Expands to wpseo_sitemap_$filter_change_freq, allowing for a change of the frequency for numerous specific URLs.
	 * @param string $default The default value for the frequency.
	 * @param string $url     The URL of the current entry.
	 *
	 * @return mixed|void
	 */
	private function filter_frequency( $filter, $default, $url ) {
		/**
		 * Filter: 'wpseo_sitemap_' . $filter . '_change_freq' - Allow filtering of the specific change frequency
		 *
		 * @api string $default The default change frequency
		 */
		$change_freq = apply_filters( 'wpseo_sitemap_' . $filter . '_change_freq', $default, $url );

		if ( ! in_array( $change_freq, array(
			'always',
			'hourly',
			'daily',
			'weekly',
			'monthly',
			'yearly',
			'never',
		) )
		) {
			$change_freq = $default;
		}

		return $change_freq;
	}

	/**
	 * Build a sub-sitemap for a specific post type -- example.com/post_type-sitemap.xml
	 *
	 * @param string $post_type Registered post type's slug.
	 */
	function build_post_type_map( $post_type ) {
		global $wpdb;

		if (
			( isset( $this->options[ 'post_types-' . $post_type . '-not_in_sitemap' ] ) && $this->options[ 'post_types-' . $post_type . '-not_in_sitemap' ] === true )
			|| in_array( $post_type, array( 'revision', 'nav_menu_item' ) )
			|| apply_filters( 'wpseo_sitemap_exclude_post_type', false, $post_type )
		) {
			$this->bad_sitemap = true;

			return;
		}

		$output = '';

		$steps  = ( 100 > $this->max_entries ) ? $this->max_entries : 100;
		$n      = (int) $this->n;
		$offset = ( $n > 1 ) ? ( ( $n - 1 ) * $this->max_entries ) : 0;
		$total  = ( $offset + $this->max_entries );

		$join_filter  = apply_filters( 'wpseo_typecount_join', '', $post_type );
		$where_filter = apply_filters( 'wpseo_typecount_where', '', $post_type );

		$query = $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts {$join_filter} WHERE post_status IN ('publish','inherit') AND post_password = '' AND post_date != '0000-00-00 00:00:00' AND post_type = %s " . $where_filter, $post_type );

		$typecount = $wpdb->get_var( $query );

		if ( $total > $typecount ) {
			$total = $typecount;
		}

		if ( $n === 1 ) {
			$front_id = get_option( 'page_on_front' );
			if ( ! $front_id && ( $post_type == 'post' || $post_type == 'page' ) ) {
				$output .= $this->sitemap_url(
					array(
						'loc' => $this->home_url,
						'pri' => 1,
						'chf' => $this->filter_frequency( 'homepage', 'daily', $this->home_url ),
					)
				);
			}
			elseif ( $front_id && $post_type == 'post' ) {
				$page_for_posts = get_option( 'page_for_posts' );
				if ( $page_for_posts ) {
					$page_for_posts_url = get_permalink( $page_for_posts );
					$output            .= $this->sitemap_url(
						array(
							'loc' => $page_for_posts_url,
							'pri' => 1,
							'chf' => $this->filter_frequency( 'blogpage', 'daily', $page_for_posts_url ),
						)
					);
					unset( $page_for_posts_url );
				}
			}

			$archive_url = get_post_type_archive_link( $post_type );
			/**
			 * Filter: 'wpseo_sitemap_post_type_archive_link' - Allow changing the URL Yoast SEO uses in the XML sitemap for this post type archive.
			 *
			 * @api float $archive_url The URL of this archive
			 *
			 * @param string $post_type The post type this archive is for.
			 */
			$archive_url = apply_filters( 'wpseo_sitemap_post_type_archive_link', $archive_url, $post_type );
			if ( $archive_url ) {
				/**
				 * Filter: 'wpseo_xml_post_type_archive_priority' - Allow changing the priority of the URL Yoast SEO uses in the XML sitemap.
				 *
				 * @api float $priority The priority for this URL, ranging from 0 to 1
				 *
				 * @param string $post_type The post type this archive is for.
				 */
				$output .= $this->sitemap_url(
					array(
						'loc' => $archive_url,
						'pri' => apply_filters( 'wpseo_xml_post_type_archive_priority', 0.8, $post_type ),
						'chf' => $this->filter_frequency( $post_type . '_archive', 'weekly', $archive_url ),
						'mod' => $this->get_last_modified( $post_type ),
						// Function get_lastpostmodified( 'gmt', $post_type ) #17455.
					)
				);
			}
		}

		if ( $typecount == 0 && empty( $archive ) ) {
			$this->bad_sitemap = true;

			return;
		}

		$stackedurls = array();

		// Make sure you're wpdb->preparing everything you throw into this!!
		$join_filter  = apply_filters( 'wpseo_posts_join', false, $post_type );
		$where_filter = apply_filters( 'wpseo_posts_where', false, $post_type );

		$status = ( $post_type == 'attachment' ) ? 'inherit' : 'publish';

		$parsed_home = parse_url( $this->home_url );
		$host        = '';
		$scheme      = 'http';
		if ( isset( $parsed_home['host'] ) && ! empty( $parsed_home['host'] ) ) {
			$host = str_replace( 'www.', '', $parsed_home['host'] );
		}
		if ( isset( $parsed_home['scheme'] ) && ! empty( $parsed_home['scheme'] ) ) {
			$scheme = $parsed_home['scheme'];
		}


		/**
		 * We grab post_date, post_name and post_status too so we can throw these objects
		 * into get_permalink, which saves a get_post call for each permalink.
		 */
		while ( $total > $offset ) {

			// Optimized query per this thread: http://wordpress.org/support/topic/plugin-wordpress-seo-by-yoast-performance-suggestion.
			// Also see http://explainextended.com/2009/10/23/mysql-order-by-limit-performance-late-row-lookups/.
			$query = $wpdb->prepare( "SELECT l.ID, post_title, post_content, post_name, post_parent, post_modified_gmt, post_date, post_date_gmt FROM ( SELECT ID FROM $wpdb->posts {$join_filter} WHERE post_status = '%s' AND post_password = '' AND post_type = '%s' AND post_date != '0000-00-00 00:00:00' {$where_filter} ORDER BY post_modified ASC LIMIT %d OFFSET %d ) o JOIN $wpdb->posts l ON l.ID = o.ID ORDER BY l.ID",
				$status, $post_type, $steps, $offset
			);

			$posts = $wpdb->get_results( $query );

			$post_ids = array();
			foreach ( $posts as $p ) {
				$post_ids[] = $p->ID;
			}
			unset( $p );

			if ( count( $post_ids ) > 0 ) {
				update_meta_cache( 'post', $post_ids );

				$imploded_post_ids = implode( $post_ids, ',' );

				$attachments = $this->get_attachments( $imploded_post_ids );
				$thumbnails  = $this->get_thumbnails( $imploded_post_ids );

				$this->do_attachment_ids_caching( $attachments, $thumbnails );

				unset( $imploded_post_ids );
			}
			unset( $post_ids );

			$offset = ( $offset + $steps );

			$posts_to_exclude = explode( ',', $this->options['excluded-posts'] );

			if ( is_array( $posts ) && $posts !== array() ) {
				foreach ( $posts as $p ) {
					$p->post_type   = $post_type;
					$p->post_status = 'publish';
					$p->filter      = 'sample';

					if ( WPSEO_Meta::get_value( 'meta-robots-noindex', $p->ID ) === '1' ) {
						continue;
					}
					if ( in_array( $p->ID, $posts_to_exclude ) ) {
						continue;
					}

					$url = array();

					if ( isset( $p->post_modified_gmt ) && $p->post_modified_gmt != '0000-00-00 00:00:00' && $p->post_modified_gmt > $p->post_date_gmt ) {
						$url['mod'] = $p->post_modified_gmt;
					}
					else {
						if ( '0000-00-00 00:00:00' != $p->post_date_gmt ) {
							$url['mod'] = $p->post_date_gmt;
						}
						else {
							$url['mod'] = $p->post_date; // TODO does this ever happen? will wreck timezone later R.
						}
					}

					$url['loc'] = get_permalink( $p );

					/**
					 * Filter: 'wpseo_xml_sitemap_post_url' - Allow changing the URL Yoast SEO uses in the XML sitemap.
					 *
					 * Note that only absolute local URLs are allowed as the check after this removes external URLs.
					 *
					 * @api string $url URL to use in the XML sitemap
					 *
					 * @param object $p Post object for the URL.
					 */
					$url['loc'] = apply_filters( 'wpseo_xml_sitemap_post_url', $url['loc'], $p );

					$url['chf'] = $this->filter_frequency( $post_type . '_single', 'weekly', $url['loc'] );

					/**
					 * Do not include external URLs.
					 * @see https://wordpress.org/plugins/page-links-to/ can rewrite permalinks to external URLs.
					 */
					if ( false === strpos( $url['loc'], $this->home_url ) ) {
						continue;
					}

					$canonical = WPSEO_Meta::get_value( 'canonical', $p->ID );
					if ( $canonical !== '' && $canonical !== $url['loc'] ) {
						/*
						Let's assume that if a canonical is set for this page and it's different from
						   the URL of this post, that page is either already in the XML sitemap OR is on
						   an external site, either way, we shouldn't include it here.
						*/
						continue;
					}
					else {
						if ( $this->options['trailingslash'] === true && $p->post_type != 'post' ) {
							$url['loc'] = trailingslashit( $url['loc'] );
						}
					}
					unset( $canonical );

					$url['pri'] = $this->calculate_priority( $p );

					$url['images'] = array();

					$content = $p->post_content;
					$content = '<p><img src="' . $this->image_url( get_post_thumbnail_id( $p->ID ) ) . '" alt="' . $p->post_title . '" /></p>' . $content;

					if ( preg_match_all( '`<img [^>]+>`', $content, $matches ) ) {
						$url['images'] = $this->parse_matched_images( $matches, $p, $scheme, $host );
					}
					unset( $content, $matches, $img );

					if ( strpos( $p->post_content, '[gallery' ) !== false ) {
						if ( is_array( $attachments ) && $attachments !== array() ) {
							$url['images'] = $this->parse_attachments( $attachments, $p );
						}
						unset( $attachment, $src, $image, $alt );
					}

					$url['images'] = apply_filters( 'wpseo_sitemap_urlimages', $url['images'], $p->ID );

					if ( ! in_array( $url['loc'], $stackedurls ) ) {
						// Use this filter to adjust the entry before it gets added to the sitemap.
						$url = apply_filters( 'wpseo_sitemap_entry', $url, 'post', $p );
						if ( is_array( $url ) && $url !== array() ) {
							$output       .= $this->sitemap_url( $url );
							$stackedurls[] = $url['loc'];
						}
					}

					// Clear the post_meta and the term cache for the post
					// wp_cache_delete( $p->ID, 'post_meta' );
					// clean_object_term_cache( $p->ID, $post_type );
					// as we no longer need it now.
				}
				unset( $p, $url );
			}
		}

		if ( empty( $output ) ) {
			$this->bad_sitemap = true;

			return;
		}

		$this->sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
		$this->sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
		$this->sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		$this->sitemap .= $output;

		// Filter to allow adding extra URLs, only do this on the first XML sitemap, not on all.
		if ( $n === 1 ) {
			$this->sitemap .= apply_filters( 'wpseo_sitemap_' . $post_type . '_content', '' );
		}

		$this->sitemap .= '</urlset>';
	}

	/**
	 * Parsing the matched images
	 *
	 * @param array  $matches
	 * @param object $p
	 * @param string $scheme
	 * @param string $host
	 *
	 * @return array
	 */
	private function parse_matched_images( $matches, $p, $scheme, $host ) {

		$return = array();

		foreach ( $matches[0] as $img ) {
			if ( preg_match( '`src=["\']([^"\']+)["\']`', $img, $match ) ) {
				$src = $match[1];
				if ( WPSEO_Utils::is_url_relative( $src ) === true ) {
					if ( $src[0] !== '/' ) {
						continue;
					}
					else {
						// The URL is relative, we'll have to make it absolute.
						$src = $this->home_url . $src;
					}
				}
				elseif ( strpos( $src, 'http' ) !== 0 ) {
					// Protocol relative url, we add the scheme as the standard requires a protocol.
					$src = $scheme . ':' . $src;

				}

				if ( strpos( $src, $host ) === false ) {
					continue;
				}

				if ( $src != esc_url( $src ) ) {
					continue;
				}

				if ( isset( $return[ $src ] ) ) {
					continue;
				}

				$image = array(
					'src' => apply_filters( 'wpseo_xml_sitemap_img_src', $src, $p ),
				);

				if ( preg_match( '`title=["\']([^"\']+)["\']`', $img, $title_match ) ) {
					$image['title'] = str_replace( array( '-', '_' ), ' ', $title_match[1] );
				}
				unset( $title_match );

				if ( preg_match( '`alt=["\']([^"\']+)["\']`', $img, $alt_match ) ) {
					$image['alt'] = str_replace( array( '-', '_' ), ' ', $alt_match[1] );
				}
				unset( $alt_match );

				$image    = apply_filters( 'wpseo_xml_sitemap_img', $image, $p );
				$return[] = $image;
			}
			unset( $match, $src );
		}

		return $return;
	}

	/**
	 * Build a sub-sitemap for a specific taxonomy -- example.com/tax-sitemap.xml
	 *
	 * @param string $taxonomy Registered taxonomy's slug.
	 */
	function build_tax_map( $taxonomy ) {
		if (
			( isset( $this->options[ 'taxonomies-' . $taxonomy->name . '-not_in_sitemap' ] ) && $this->options[ 'taxonomies-' . $taxonomy->name . '-not_in_sitemap' ] === true )
			|| in_array( $taxonomy, array( 'link_category', 'nav_menu', 'post_format' ) )
			|| apply_filters( 'wpseo_sitemap_exclude_taxonomy', false, $taxonomy->name )
		) {
			$this->bad_sitemap = true;

			return;
		}

		global $wpdb;

		$output = '';

		$steps  = $this->max_entries;
		$n      = (int) $this->n;
		$offset = ( $n > 1 ) ? ( ( $n - 1 ) * $this->max_entries ) : 0;

		/**
		 * Filter: 'wpseo_sitemap_exclude_empty_terms' - Allow people to include empty terms in sitemap
		 *
		 * @api bool $hide_empty Whether or not to hide empty terms, defaults to true.
		 *
		 * @param object $taxonomy The taxonomy we're getting terms for.
		 */
		$hide_empty = apply_filters( 'wpseo_sitemap_exclude_empty_terms', true, $taxonomy );
		$terms      = get_terms( $taxonomy->name, array( 'hide_empty' => $hide_empty ) );
		$terms      = array_splice( $terms, $offset, $steps );

		if ( is_array( $terms ) && $terms !== array() ) {
			foreach ( $terms as $c ) {
				$url = array();

				$tax_noindex     = WPSEO_Taxonomy_Meta::get_term_meta( $c, $c->taxonomy, 'noindex' );
				$tax_sitemap_inc = WPSEO_Taxonomy_Meta::get_term_meta( $c, $c->taxonomy, 'sitemap_include' );

				if ( ( is_string( $tax_noindex ) && $tax_noindex === 'noindex' ) && ( ! is_string( $tax_sitemap_inc ) || $tax_sitemap_inc !== 'always' ) ) {
					continue;
				}

				if ( $tax_sitemap_inc === 'never' ) {
					continue;
				}

				$url['loc'] = WPSEO_Taxonomy_Meta::get_term_meta( $c, $c->taxonomy, 'canonical' );
				if ( ! is_string( $url['loc'] ) || $url['loc'] === '' ) {
					$url['loc'] = get_term_link( $c, $c->taxonomy );
					if ( $this->options['trailingslash'] === true ) {
						$url['loc'] = trailingslashit( $url['loc'] );
					}
				}
				if ( $c->count > 10 ) {
					$url['pri'] = 0.6;
				}
				else {
					if ( $c->count > 3 ) {
						$url['pri'] = 0.4;
					}
					else {
						$url['pri'] = 0.2;
					}
				}

				// Grab last modified date.
				$sql        = $wpdb->prepare(
					"
						SELECT MAX(p.post_modified_gmt) AS lastmod
						FROM	$wpdb->posts AS p
						INNER JOIN $wpdb->term_relationships AS term_rel
							ON		term_rel.object_id = p.ID
						INNER JOIN $wpdb->term_taxonomy AS term_tax
							ON		term_tax.term_taxonomy_id = term_rel.term_taxonomy_id
							AND		term_tax.taxonomy = %s
							AND		term_tax.term_id = %d
						WHERE	p.post_status IN ('publish','inherit')
							AND		p.post_password = ''",
					$c->taxonomy,
					$c->term_id
				);
				$url['mod'] = $wpdb->get_var( $sql );
				$url['chf'] = $this->filter_frequency( $c->taxonomy . '_term', 'weekly', $url['loc'] );

				// Use this filter to adjust the entry before it gets added to the sitemap.
				$url = apply_filters( 'wpseo_sitemap_entry', $url, 'term', $c );

				if ( is_array( $url ) && $url !== array() ) {
					$output .= $this->sitemap_url( $url );
				}
			}
			unset( $c, $url, $tax_noindex, $tax_sitemap_inc, $sql );
		}

		if ( empty( $output ) ) {
			$this->bad_sitemap = true;

			return;
		}

		$this->sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
		$this->sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
		$this->sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		if ( is_string( $output ) && trim( $output ) !== '' ) {
			$this->sitemap .= $output;
		}
		else {
			// If the sitemap is empty, add the homepage URL to make sure it doesn't throw errors in GWT.
			$this->sitemap .= $this->sitemap_url( home_url() );
		}
		$this->sitemap .= '</urlset>';
	}


	/**
	 * Build the sub-sitemap for authors
	 *
	 * @since 1.4.8
	 */
	function build_user_map() {
		if ( $this->options['disable-author'] === true || $this->options['disable_author_sitemap'] === true ) {
			$this->bad_sitemap = true;

			return;
		}

		$output = '';

		$steps  = $this->max_entries;
		$n      = (int) $this->n;
		$offset = ( $n > 1 ) ? ( ( $n - 1 ) * $this->max_entries ) : 0;

		// Initial query to fill in missing usermeta with the current timestamp.
		$users = get_users(
			array(
				'who'        => 'authors',
				'meta_query' => array(
					array(
						'key'     => '_yoast_wpseo_profile_updated',
						'value'   => 'needs-a-value-anyway', // This is ignored, but is necessary...
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);

		if ( is_array( $users ) && $users !== array() ) {
			foreach ( $users as $user ) {
				update_user_meta( $user->ID, '_yoast_wpseo_profile_updated', time() );
			}
		}
		unset( $users, $user );

		// Query for users with this meta.
		$users = get_users(
			array(
				'who'      => 'authors',
				'offset'   => $offset,
				'number'   => $steps,
				'meta_key' => '_yoast_wpseo_profile_updated',
				'orderby'  => 'meta_value_num',
				'order'    => 'ASC',
			)
		);

		$users = apply_filters( 'wpseo_sitemap_exclude_author', $users );

		// Ascending sort.
		usort( $users, array( $this, 'user_map_sorter' ) );

		if ( is_array( $users ) && $users !== array() ) {
			foreach ( $users as $user ) {
				$author_link = get_author_posts_url( $user->ID );
				if ( $author_link !== '' ) {
					$url = array(
						'loc' => $author_link,
						'pri' => 0.8,
						'chf' => $this->filter_frequency( 'author_archive', 'daily', $author_link ),
						'mod' => date( 'c', isset( $user->_yoast_wpseo_profile_updated ) ? $user->_yoast_wpseo_profile_updated : time() ),
					);
					// Use this filter to adjust the entry before it gets added to the sitemap.
					$url = apply_filters( 'wpseo_sitemap_entry', $url, 'user', $user );

					if ( is_array( $url ) && $url !== array() ) {
						$output .= $this->sitemap_url( $url );
					}
				}
			}
			unset( $user, $author_link, $url );
		}

		if ( empty( $output ) ) {
			$this->bad_sitemap = true;

			return;
		}

		$this->sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
		$this->sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
		$this->sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		$this->sitemap .= $output;

		// Filter to allow adding extra URLs, only do this on the first XML sitemap, not on all.
		if ( $n === 1 ) {
			$this->sitemap .= apply_filters( 'wpseo_sitemap_author_content', '' );
		}

		$this->sitemap .= '</urlset>';
	}

	/**
	 * Spits out the XSL for the XML sitemap.
	 *
	 * @param string $type
	 *
	 * @since 1.4.13
	 */
	function xsl_output( $type ) {
		if ( $type == 'main' ) {
			header( $this->http_protocol() . ' 200 OK', true, 200 );
			// Prevent the search engines from indexing the XML Sitemap.
			header( 'X-Robots-Tag: noindex, follow', true );
			header( 'Content-Type: text/xml' );

			// Make the browser cache this file properly.
			$expires = YEAR_IN_SECONDS;
			header( 'Pragma: public' );
			header( 'Cache-Control: maxage=' . $expires );
			header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', ( time() + $expires ) ) . ' GMT' );

			require_once( WPSEO_PATH . 'css/xml-sitemap-xsl.php' );
		}
		else {
			do_action( 'wpseo_xsl_' . $type );
		}
	}

	/**
	 * Spit out the generated sitemap and relevant headers and encoding information.
	 */
	function output() {
		if ( ! headers_sent() ) {
			header( $this->http_protocol() . ' 200 OK', true, 200 );
			// Prevent the search engines from indexing the XML Sitemap.
			header( 'X-Robots-Tag: noindex,follow', true );
			header( 'Content-Type: text/xml' );
		}
		echo '<?xml version="1.0" encoding="', esc_attr( $this->charset ), '"?>';
		if ( $this->stylesheet ) {
			echo apply_filters( 'wpseo_stylesheet_url', $this->stylesheet ), "\n";
		}
		echo $this->sitemap;
		echo "\n", '<!-- XML Sitemap generated by Yoast SEO -->';

		$debug_display = defined( 'WP_DEBUG_DISPLAY' ) && true === WP_DEBUG_DISPLAY;
		$debug         = defined( 'WP_DEBUG' ) && true === WP_DEBUG;
		$wpseo_debug   = defined( 'WPSEO_DEBUG' ) && true === WPSEO_DEBUG;

		if ( $debug_display && ( $debug || $wpseo_debug ) ) {
			if ( $this->transient ) {
				echo "\n", '<!-- ', number_format( ( memory_get_peak_usage() / 1024 / 1024 ), 2 ), 'MB | Served from transient cache -->';
			}
			else {
				echo "\n", '<!-- ', number_format( ( memory_get_peak_usage() / 1024 / 1024 ), 2 ), 'MB | ', esc_attr( $GLOBALS['wpdb']->num_queries ), ' -->';
				if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
					echo "\n", '<!--', print_r( $GLOBALS['wpdb']->queries, true ), '-->';
				}
			}
		}
	}

	/**
	 * Build the <url> tag for a given URL.
	 *
	 * @param array $url Array of parts that make up this entry.
	 *
	 * @return string
	 */
	function sitemap_url( $url ) {

		$date = null;

		if ( ! empty( $url['mod'] ) ) {
			// Create a DateTime object date in the correct timezone.
			$date = $this->timezone->get_datetime_with_timezone( $url['mod'] );
		}

		$url['loc'] = htmlspecialchars( $url['loc'] );

		$output = "\t<url>\n";
		$output .= "\t\t<loc>" . $url['loc'] . "</loc>\n";
		$output .= empty( $date ) ? '' : "\t\t<lastmod>" . $date . "</lastmod>\n";
		$output .= "\t\t<changefreq>" . $url['chf'] . "</changefreq>\n";
		$output .= "\t\t<priority>" . str_replace( ',', '.', $url['pri'] ) . "</priority>\n";

		if ( isset( $url['images'] ) && ( is_array( $url['images'] ) && $url['images'] !== array() ) ) {
			foreach ( $url['images'] as $img ) {
				if ( ! isset( $img['src'] ) || empty( $img['src'] ) ) {
					continue;
				}
				$output .= "\t\t<image:image>\n";
				$output .= "\t\t\t<image:loc>" . esc_html( $img['src'] ) . "</image:loc>\n";
				if ( isset( $img['title'] ) && ! empty( $img['title'] ) ) {
					$output .= "\t\t\t<image:title><![CDATA[" . _wp_specialchars( html_entity_decode( $img['title'], ENT_QUOTES, $this->charset ) ) . "]]></image:title>\n";
				}
				if ( isset( $img['alt'] ) && ! empty( $img['alt'] ) ) {
					$output .= "\t\t\t<image:caption><![CDATA[" . _wp_specialchars( html_entity_decode( $img['alt'], ENT_QUOTES, $this->charset ) ) . "]]></image:caption>\n";
				}
				$output .= "\t\t</image:image>\n";
			}
			unset( $img );
		}
		$output .= "\t</url>\n";

		return $output;
	}

	/**
	 * Make a request for the sitemap index so as to cache it before the arrival of the search engines.
	 */
	function hit_sitemap_index() {
		$url = wpseo_xml_sitemaps_base_url( 'sitemap_index.xml' );
		wp_remote_get( $url );
	}

	/**
	 * Hook into redirect_canonical to stop trailing slashes on sitemap.xml URLs
	 *
	 * @param string $redirect The redirect URL currently determined.
	 *
	 * @return bool|string $redirect
	 */
	public function canonical( $redirect ) {
		$sitemap = get_query_var( 'sitemap' );
		if ( ! empty( $sitemap ) ) {
			return false;
		}

		$xsl = get_query_var( 'xsl' );
		if ( ! empty( $xsl ) ) {
			return false;
		}

		return $redirect;
	}

	/**
	 * Get the modification date for the last modified post in the post type:
	 *
	 * @param array $post_types Post types to get the last modification date for.
	 *
	 * @return string
	 */
	function get_last_modified( $post_types ) {
		global $wpdb;

		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		// We need to do this only once, as otherwise we'd be doing a query for each post type.
		if ( ! is_array( $this->post_type_dates ) ) {
			$this->post_type_dates = array();
			$query                 = "SELECT post_type, MAX(post_modified_gmt) AS date FROM $wpdb->posts WHERE post_status IN ('publish','inherit') AND post_type IN ('" . implode( "','", get_post_types( array( 'public' => true ) ) ) . "') GROUP BY post_type ORDER BY post_modified_gmt DESC";
			$results               = $wpdb->get_results( $query );
			foreach ( $results as $obj ) {
				$this->post_type_dates[ $obj->post_type ] = $obj->date;
			}
			unset( $query, $results, $obj );
		}

		if ( count( $post_types ) === 1 && isset( $this->post_type_dates[ $post_types[0] ] ) ) {
			$result = $this->post_type_dates[ $post_types[0] ];
		}
		else {
			$result = null;
			foreach ( $post_types as $post_type ) {
				if ( isset( $this->post_type_dates[ $post_type ] ) && strtotime( $this->post_type_dates[ $post_type ] ) > $result ) {
					$result = $this->post_type_dates[ $post_type ];
				}
			}
			unset( $post_type );
		}

		return $this->timezone->get_datetime_with_timezone( $result );
	}


	/**
	 * Sorts an array of WP_User by the _yoast_wpseo_profile_updated meta field
	 *
	 * @since 1.6
	 *
	 * @param Wp_User $a The first WP user.
	 * @param Wp_User $b The second WP user.
	 *
	 * @return int 0 if equal, 1 if $a is larger else or -1;
	 */
	private function user_map_sorter( $a, $b ) {
		if ( ! isset( $a->_yoast_wpseo_profile_updated ) ) {
			$a->_yoast_wpseo_profile_updated = time();
		}
		if ( ! isset( $b->_yoast_wpseo_profile_updated ) ) {
			$b->_yoast_wpseo_profile_updated = time();
		}

		if ( $a->_yoast_wpseo_profile_updated == $b->_yoast_wpseo_profile_updated ) {
			return 0;
		}

		return ( ( $a->_yoast_wpseo_profile_updated > $b->_yoast_wpseo_profile_updated ) ? 1 : -1 );
	}

	/**
	 * Filter users that should be excluded from the sitemap (by author metatag: wpseo_excludeauthorsitemap).
	 *
	 * Also filtering users that should be exclude by excluded role.
	 *
	 * @param array $users
	 *
	 * @return array all the user that aren't excluded from the sitemap
	 */
	public function user_sitemap_remove_excluded_authors( $users ) {

		if ( is_array( $users ) && $users !== array() ) {
			$options = get_option( 'wpseo_xml' );

			foreach ( $users as $user_key => $user ) {
				$exclude_user = false;

				$is_exclude_on = get_the_author_meta( 'wpseo_excludeauthorsitemap', $user->ID );
				if ( $is_exclude_on === 'on' ) {
					$exclude_user = true;
				}
				elseif ( $options['disable_author_noposts'] === true ) {
					$count_posts  = count_user_posts( $user->ID );
					$exclude_user = ( $count_posts == 0 );
					unset( $count_posts );
				}
				else {
					$user_role    = $user->roles[0];
					$target_key   = "user_role-{$user_role}-not_in_sitemap";
					$exclude_user = $options[ $target_key ];
					unset( $user_rol, $target_key );
				}

				if ( $exclude_user === true ) {
					unset( $users[ $user_key ] );
				}
			}
		}

		return $users;
	}

	/**
	 * Get attached image URL - Adapted from core for speed
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	private function image_url( $post_id ) {

		static $uploads;

		if ( empty( $uploads ) ) {
			$uploads = wp_upload_dir();
		}

		if ( false !== $uploads['error'] ) {
			return '';
		}

		$url = '';

		if ( $file = get_post_meta( $post_id, '_wp_attached_file', true ) ) { // Get attached file.
			if ( 0 === strpos( $file, $uploads['basedir'] ) ) { // Check that the upload base exists in the file location.
				$url = str_replace( $uploads['basedir'], $uploads['baseurl'], $file );
			}
			// Replace file location with url location.
			elseif ( false !== strpos( $file, 'wp-content/uploads' ) ) {
				$url = $uploads['baseurl'] . substr( $file, ( strpos( $file, 'wp-content/uploads' ) + 18 ) );
			}
			// It's a newly uploaded file, therefore $file is relative to the baseurl.
			else {
				$url = $uploads['baseurl'] . "/$file";
			}
		}

		return $url;
	}


	/**
	 * Getting the attachments from database
	 *
	 * @param string $post_ids
	 *
	 * @return mixed
	 */
	private function get_attachments( $post_ids ) {
		global $wpdb;
		$child_query = "SELECT ID, post_title, post_parent FROM $wpdb->posts WHERE post_status = 'inherit' AND post_type = 'attachment' AND post_parent IN (" . $post_ids . ')';
		$wpdb->query( $child_query );
		$attachments = $wpdb->get_results( $child_query );

		return $attachments;
	}

	/**
	 * Getting thumbnails
	 *
	 * @param array $post_ids
	 *
	 * @return mixed
	 */
	private function get_thumbnails( $post_ids ) {
		global $wpdb;

		$thumbnail_query = "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_thumbnail_id' AND post_id IN (" . $post_ids . ')';
		$wpdb->query( $thumbnail_query );
		$thumbnails = $wpdb->get_results( $thumbnail_query );

		return $thumbnails;
	}

	/**
	 * Parsing attachment_ids and do the caching
	 *
	 * Function will pluck ID from attachments and meta_value from thumbnails and marge them into one array. This
	 * array will be used to do the caching
	 *
	 * @param array $attachments
	 * @param array $thumbnails
	 */
	private function do_attachment_ids_caching( $attachments, $thumbnails ) {
		$attachment_ids = wp_list_pluck( $attachments, 'ID' );
		$thumbnail_ids  = wp_list_pluck( $thumbnails, 'meta_value' );

		$attachment_ids = array_unique( array_merge( $thumbnail_ids, $attachment_ids ) );

		_prime_post_caches( $attachment_ids );
		update_meta_cache( 'post', $attachment_ids );
	}

	/**
	 * Parses the given attachments
	 *
	 * @param array     $attachments
	 * @param stdobject $post
	 *
	 * @return array
	 */
	private function parse_attachments( $attachments, $post ) {

		$return = array();

		foreach ( $attachments as $attachment ) {
			if ( $attachment->post_parent !== $post->ID ) {
				continue;
			}

			$src   = $this->image_url( $attachment->ID );
			$image = array(
				'src' => apply_filters( 'wpseo_xml_sitemap_img_src', $src, $post ),
			);

			$alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
			if ( $alt !== '' ) {
				$image['alt'] = $alt;
			}
			unset( $alt );

			$image['title'] = $attachment->post_title;

			$image = apply_filters( 'wpseo_xml_sitemap_img', $image, $post );

			$return[] = $image;
		}

		return $return;
	}

	/**
	 * Calculate the priority of the post
	 *
	 * @param stdobject $post
	 *
	 * @return float|mixed
	 */
	private function calculate_priority( $post ) {

		$return = 0.6;
		if ( $post->post_parent == 0 && $post->post_type == 'page' ) {
			$return = 0.8;
		}

		$front_id = get_option( 'page_on_front' );

		if ( isset( $front_id ) && $post->ID == $front_id ) {
			$return = 1.0;
		}

		/**
		 * Filter: 'wpseo_xml_post_type_archive_priority' - Allow changing the priority of the URL
		 * Yoast SEO uses in the XML sitemap.
		 *
		 * @api float $priority The priority for this URL, ranging from 0 to 1
		 *
		 * @param string $post_type The post type this archive is for.
		 * @param object $p         The post object.
		 */
		$return = apply_filters( 'wpseo_xml_sitemap_post_priority', $return, $post->post_type, $post );

		return $return;
	}

} /* End of class */
