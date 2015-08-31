<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * Main frontend class for Yoast SEO, responsible for the SEO output as well as removing
 * default WordPress output.
 */
class WPSEO_Frontend {

	/**
	 * @var    object    Instance of this class
	 */
	public static $instance;

	/**
	 * @var array Holds the plugins options.
	 */
	public $options = array();

	/**
	 * @var boolean Boolean indicating wether output buffering has been started
	 */
	private $ob_started = false;

	/**
	 * Holds the canonical URL for the current page
	 *
	 * @var string
	 */
	private $canonical = null;

	/**
	 * Holds the canonical URL for the current page that cannot be overriden by a manual canonical input
	 *
	 * @var string
	 */
	private $canonical_no_override = null;

	/**
	 * Holds the canonical URL for the current page without pagination
	 *
	 * @var string
	 */
	private $canonical_unpaged = null;

	/**
	 * Holds the pages meta description
	 *
	 * @var string
	 */
	private $metadesc = null;

	/**
	 * Holds the generated title for the page
	 *
	 * @var string
	 */
	private $title = null;

	/**
	 * Class constructor
	 *
	 * Adds and removes a lot of filters.
	 */
	protected function __construct() {

		$this->options = WPSEO_Options::get_all();

		add_action( 'wp_head', array( $this, 'front_page_specific_init' ), 0 );
		add_action( 'wp_head', array( $this, 'head' ), 1 );

		// The head function here calls action wpseo_head, to which we hook all our functionality.
		add_action( 'wpseo_head', array( $this, 'debug_marker' ), 2 );
		add_action( 'wpseo_head', array( $this, 'robots' ), 6 );
		add_action( 'wpseo_head', array( $this, 'metadesc' ), 10 );
		add_action( 'wpseo_head', array( $this, 'metakeywords' ), 11 );
		add_action( 'wpseo_head', array( $this, 'canonical' ), 20 );
		add_action( 'wpseo_head', array( $this, 'adjacent_rel_links' ), 21 );
		add_action( 'wpseo_head', array( $this, 'publisher' ), 22 );

		// Remove actions that we will handle through our wpseo_head call, and probably change the output of.
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'start_post_rel_link' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		remove_action( 'wp_head', 'noindex', 1 );

		add_filter( 'wp_title', array( $this, 'title' ), 15, 3 );
		add_filter( 'thematic_doctitle', array( $this, 'title' ), 15 );

		add_action( 'wp', array( $this, 'page_redirect' ), 99 );

		add_action( 'template_redirect', array( $this, 'noindex_feed' ) );

		add_filter( 'loginout', array( $this, 'nofollow_link' ) );
		add_filter( 'register', array( $this, 'nofollow_link' ) );

		// Fix the WooThemes woo_title() output.
		add_filter( 'woo_title', array( $this, 'fix_woo_title' ), 99 );

		if ( $this->options['hide-rsdlink'] === true ) {
			remove_action( 'wp_head', 'rsd_link' );
		}
		if ( $this->options['hide-wlwmanifest'] === true ) {
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}
		if ( $this->options['hide-shortlink'] === true ) {
			remove_action( 'wp_head', 'wp_shortlink_wp_head' );
			remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
		}
		if ( $this->options['hide-feedlinks'] === true ) {
			// @todo: add option to display just normal feed and hide comment feed.
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}

		if ( ( $this->options['disable-date'] === true ||
		       $this->options['disable-author'] === true ) ||
		     ( isset( $this->options['disable-post_formats'] ) && $this->options['disable-post_formats'] )
		) {
			add_action( 'wp', array( $this, 'archive_redirect' ) );
		}
		if ( $this->options['redirectattachment'] === true ) {
			add_action( 'template_redirect', array( $this, 'attachment_redirect' ), 1 );
		}
		if ( $this->options['trailingslash'] === true ) {
			add_filter( 'user_trailingslashit', array( $this, 'add_trailingslash' ), 10, 2 );
		}
		if ( $this->options['cleanpermalinks'] === true ) {
			add_action( 'template_redirect', array( $this, 'clean_permalink' ), 1 );
		}
		if ( $this->options['cleanreplytocom'] === true ) {
			add_filter( 'comment_reply_link', array( $this, 'remove_reply_to_com' ) );
			add_action( 'template_redirect', array( $this, 'replytocom_redirect' ), 1 );
		}
		add_filter( 'the_content_feed', array( $this, 'embed_rssfooter' ) );
		add_filter( 'the_excerpt_rss', array( $this, 'embed_rssfooter_excerpt' ) );

		if ( $this->options['forcerewritetitle'] === true ) {
			add_action( 'template_redirect', array( $this, 'force_rewrite_output_buffer' ), 99999 );
			add_action( 'wp_footer', array( $this, 'flush_cache' ), - 1 );
		}

		if ( $this->options['title_test'] > 0 ) {
			add_filter( 'wpseo_title', array( $this, 'title_test_helper' ) );
		}
	}

	/**
	 * Initialize the functions that only need to run on the frontpage
	 */
	public function front_page_specific_init() {
		if ( ! is_front_page() ) {
			return;
		}

		new WPSEO_JSON_LD;
		add_action( 'wpseo_head', array( $this, 'webmaster_tools_authentication' ), 90 );
	}

	/**
	 * Resets the entire class so canonicals, titles etc can be regenerated.
	 */
	public function reset() {
		foreach ( get_class_vars( __CLASS__ ) as $name => $default ) {
			if ( $name == 'instance' ) {
				self::$instance = null;
			}
			else {
				$this->$name = $default;
			}
		}
		$this->options = WPSEO_Options::get_all();
	}

	/**
	 * Get the singleton instance of this class
	 *
	 * @return WPSEO_Frontend
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Override Woo's title with our own.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public function fix_woo_title( $title ) {
		return $this->title( $title );
	}

	/**
	 * Determine whether this is the homepage and shows posts.
	 *
	 * @return bool
	 */
	public function is_home_posts_page() {
		return ( is_home() && 'posts' == get_option( 'show_on_front' ) );
	}

	/**
	 * Determine whether the this is the static frontpage.
	 *
	 * @return bool
	 */
	public function is_home_static_page() {
		return ( is_front_page() && 'page' == get_option( 'show_on_front' ) && is_page( get_option( 'page_on_front' ) ) );
	}

	/**
	 * Determine whether this is the posts page, when it's not the frontpage.
	 *
	 * @return bool
	 */
	public function is_posts_page() {
		return ( is_home() && 'page' == get_option( 'show_on_front' ) );
	}

	/**
	 * Used for static home and posts pages as well as singular titles.
	 *
	 * @param object|null $object If filled, object to get the title for.
	 *
	 * @return string
	 */
	public function get_content_title( $object = null ) {
		if ( is_null( $object ) ) {
			$object = $GLOBALS['wp_query']->get_queried_object();
		}

		$title = WPSEO_Meta::get_value( 'title', $object->ID );

		if ( $title !== '' ) {
			return wpseo_replace_vars( $title, $object );
		}

		$post_type = ( isset( $object->post_type ) ? $object->post_type : $object->query_var );

		return $this->get_title_from_options( 'title-' . $post_type, $object );
	}

	/**
	 * Used for category, tag, and tax titles.
	 *
	 * @return string
	 */
	public function get_taxonomy_title() {
		$object = $GLOBALS['wp_query']->get_queried_object();

		$title = WPSEO_Taxonomy_Meta::get_term_meta( $object, $object->taxonomy, 'title' );

		if ( is_string( $title ) && $title !== '' ) {
			return wpseo_replace_vars( $title, $object );
		}
		else {
			return $this->get_title_from_options( 'title-tax-' . $object->taxonomy, $object );
		}
	}

	/**
	 * Used for author titles.
	 *
	 * @return string
	 */
	public function get_author_title() {
		$author_id = get_query_var( 'author' );
		$title     = trim( get_the_author_meta( 'wpseo_title', $author_id ) );

		if ( $title !== '' ) {
			return wpseo_replace_vars( $title, array() );
		}

		return $this->get_title_from_options( 'title-author-wpseo' );
	}

	/**
	 * Simple function to use to pull data from $options.
	 *
	 * All titles pulled from options will be run through the wpseo_replace_vars function.
	 *
	 * @param string       $index      name of the page to get the title from the settings for.
	 * @param object|array $var_source possible object to pull variables from.
	 *
	 * @return string
	 */
	public function get_title_from_options( $index, $var_source = array() ) {
		if ( ! isset( $this->options[ $index ] ) || $this->options[ $index ] === '' ) {
			if ( is_singular() ) {
				return wpseo_replace_vars( '%%title%% %%sep%% %%sitename%%', $var_source );
			}
			else {
				return '';
			}
		}
		else {
			return wpseo_replace_vars( $this->options[ $index ], $var_source );
		}
	}

	/**
	 * Get the default title for the current page.
	 *
	 * This is the fallback title generator used when a title hasn't been set for the specific content, taxonomy, author
	 * details, or in the options. It scrubs off any present prefix before or after the title (based on $seplocation) in
	 * order to prevent duplicate seperations from appearing in the title (this happens when a prefix is supplied to the
	 * wp_title call on singular pages).
	 *
	 * @param string $sep         The separator used between variables.
	 * @param string $seplocation Whether the separator should be left or right.
	 * @param string $title       Possible title that's already set.
	 *
	 * @return string
	 */
	public function get_default_title( $sep, $seplocation, $title = '' ) {
		if ( 'right' == $seplocation ) {
			$regex = '`\s*' . preg_quote( trim( $sep ), '`' ) . '\s*`u';
		}
		else {
			$regex = '`^\s*' . preg_quote( trim( $sep ), '`' ) . '\s*`u';
		}
		$title = preg_replace( $regex, '', $title );

		if ( ! is_string( $title ) || ( is_string( $title ) && $title === '' ) ) {
			$title = get_bloginfo( 'name' );
			$title = $this->add_paging_to_title( $sep, $seplocation, $title );
			$title = $this->add_to_title( $sep, $seplocation, $title, strip_tags( get_bloginfo( 'description' ) ) );

			return $title;
		}

		$title = $this->add_paging_to_title( $sep, $seplocation, $title );
		$title = $this->add_to_title( $sep, $seplocation, $title, strip_tags( get_bloginfo( 'name' ) ) );

		return $title;
	}

	/**
	 * This function adds paging details to the title.
	 *
	 * @param string $sep         Separator used in the title.
	 * @param string $seplocation Whether the separator should be left or right.
	 * @param string $title       The title to append the paging info to.
	 *
	 * @return string
	 */
	public function add_paging_to_title( $sep, $seplocation, $title ) {
		global $wp_query;

		if ( ! empty( $wp_query->query_vars['paged'] ) && $wp_query->query_vars['paged'] > 1 ) {
			return $this->add_to_title( $sep, $seplocation, $title, $wp_query->query_vars['paged'] . '/' . $wp_query->max_num_pages );
		}

		return $title;
	}

	/**
	 * Add part to title, while ensuring that the $seplocation variable is respected.
	 *
	 * @param string $sep         Separator used in the title.
	 * @param string $seplocation Whether the separator should be left or right.
	 * @param string $title       The title to append the title_part to.
	 * @param string $title_part  The part to append to the title.
	 *
	 * @return string
	 */
	public function add_to_title( $sep, $seplocation, $title, $title_part ) {
		if ( 'right' === $seplocation ) {
			return $title . $sep . $title_part;
		}

		return $title_part . $sep . $title;
	}

	/**
	 * Main title function.
	 *
	 * @param string $title              Title that might have already been set.
	 * @param string $separator          Separator determined in theme (unused).
	 * @param string $separator_location Whether the separator should be left or right.
	 *
	 * @return string
	 */
	public function title( $title, $separator = '', $separator_location = '' ) {
		if ( is_null( $this->title ) ) {
			$this->title = $this->generate_title( $title, $separator_location );
		}

		return $this->title;
	}

	/**
	 * Main title generation function.
	 *
	 * @param string $title              Title that might have already been set.
	 * @param string $separator_location Whether the separator should be left or right.
	 *
	 * @return string
	 */
	private function generate_title( $title, $separator_location ) {

		if ( is_feed() ) {
			return $title;
		}

		$separator = wpseo_replace_vars( '%%sep%%', array() );
		$separator = ' ' . trim( $separator ) . ' ';

		if ( '' === trim( $separator_location ) ) {
			$separator_location = ( is_rtl() ) ? 'left' : 'right';
		}

		// This needs to be kept track of in order to generate
		// default titles for singular pages.
		$original_title = $title;

		// This flag is used to determine if any additional
		// processing should be done to the title after the
		// main section of title generation completes.
		$modified_title = true;

		// This variable holds the page-specific title part
		// that is used to generate default titles.
		$title_part = '';

		if ( $this->is_home_static_page() ) {
			$title = $this->get_content_title();
		}
		elseif ( $this->is_home_posts_page() ) {
			$title = $this->get_title_from_options( 'title-home-wpseo' );
		}
		elseif ( $this->is_posts_page() ) {
			$title = $this->get_content_title( get_post( get_option( 'page_for_posts' ) ) );
		}
		elseif ( is_singular() ) {
			$title = $this->get_content_title();

			if ( ! is_string( $title ) || '' === $title ) {
				$title_part = $original_title;
			}
		}
		elseif ( is_search() ) {
			$title = $this->get_title_from_options( 'title-search-wpseo' );

			if ( ! is_string( $title ) || '' === $title ) {
				$title_part = sprintf( __( 'Search for "%s"', 'wordpress-seo' ), esc_html( get_search_query() ) );
			}
		}
		elseif ( is_category() || is_tag() || is_tax() ) {
			$title = $this->get_taxonomy_title();

			if ( ! is_string( $title ) || '' === $title ) {
				if ( is_category() ) {
					$title_part = single_cat_title( '', false );
				}
				elseif ( is_tag() ) {
					$title_part = single_tag_title( '', false );
				}
				else {
					$title_part = single_term_title( '', false );
					if ( $title_part === '' ) {
						$term       = $GLOBALS['wp_query']->get_queried_object();
						$title_part = $term->name;
					}
				}
			}
		}
		elseif ( is_author() ) {
			$title = $this->get_author_title();

			if ( ! is_string( $title ) || '' === $title ) {
				$title_part = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
			}
		}
		elseif ( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );

			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}

			$title = $this->get_title_from_options( 'title-ptarchive-' . $post_type );

			if ( ! is_string( $title ) || '' === $title ) {
				$post_type_obj = get_post_type_object( $post_type );
				if ( isset( $post_type_obj->labels->menu_name ) ) {
					$title_part = $post_type_obj->labels->menu_name;
				}
				elseif ( isset( $post_type_obj->name ) ) {
					$title_part = $post_type_obj->name;
				}
				else {
					$title_part = ''; // To be determined what this should be.
				}
			}
		}
		elseif ( is_archive() ) {
			$title = $this->get_title_from_options( 'title-archive-wpseo' );

			// @todo [JRF => Yoast] Should these not use the archive default if no title found ?
			// WPSEO_Options::get_default( 'wpseo_titles', 'title-archive-wpseo' )
			// Replacement would be needed!
			if ( empty( $title ) ) {
				if ( is_month() ) {
					$title_part = sprintf( __( '%s Archives', 'wordpress-seo' ), single_month_title( ' ', false ) );
				}
				elseif ( is_year() ) {
					$title_part = sprintf( __( '%s Archives', 'wordpress-seo' ), get_query_var( 'year' ) );
				}
				elseif ( is_day() ) {
					$title_part = sprintf( __( '%s Archives', 'wordpress-seo' ), get_the_date() );
				}
				else {
					$title_part = __( 'Archives', 'wordpress-seo' );
				}
			}
		}
		elseif ( is_404() ) {

			if ( 0 !== get_query_var( 'year' ) || ( 0 !== get_query_var( 'monthnum' ) || 0 !== get_query_var( 'day' ) ) ) {
				// @todo [JRF => Yoast] Should these not use the archive default if no title found ?
				if ( 0 !== get_query_var( 'day' ) ) {
					$date       = sprintf( '%04d-%02d-%02d 00:00:00', get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
					$date       = mysql2date( get_option( 'date_format' ), $date, true );
					$date       = apply_filters( 'get_the_date', $date, '' );
					$title_part = sprintf( __( '%s Archives', 'wordpress-seo' ), $date );
				}
				elseif ( 0 !== get_query_var( 'monthnum' ) ) {
					$title_part = sprintf( __( '%s Archives', 'wordpress-seo' ), single_month_title( ' ', false ) );
				}
				elseif ( 0 !== get_query_var( 'year' ) ) {
					$title_part = sprintf( __( '%s Archives', 'wordpress-seo' ), get_query_var( 'year' ) );
				}
				else {
					$title_part = __( 'Archives', 'wordpress-seo' );
				}
			}
			else {
				$title = $this->get_title_from_options( 'title-404-wpseo' );

				// @todo [JRF => Yoast] Should these not use the 404 default if no title found ?
				// WPSEO_Options::get_default( 'wpseo_titles', 'title-404-wpseo' )
				// Replacement would be needed!
				if ( empty( $title ) ) {
					$title_part = __( 'Page not found', 'wordpress-seo' );
				}
			}
		}
		else {
			// In case the page type is unknown, leave the title alone.
			$modified_title = false;

			// If you would like to generate a default title instead,
			// the following code could be used
			// $title_part = $title;
			// instead of the line above.
		}

		if ( ( $modified_title && empty( $title ) ) || ! empty( $title_part ) ) {
			$title = $this->get_default_title( $separator, $separator_location, $title_part );
		}

		if ( defined( 'ICL_LANGUAGE_CODE' ) && false !== strpos( $title, ICL_LANGUAGE_CODE ) ) {
			$title = str_replace( ' @' . ICL_LANGUAGE_CODE, '', $title );
		}

		/**
		 * Filter: 'wpseo_title' - Allow changing the Yoast SEO <title> output
		 *
		 * @api string $title The page title being put out.
		 */

		return esc_html( strip_tags( stripslashes( apply_filters( 'wpseo_title', $title ) ) ) );
	}

	/**
	 * Function used when title needs to be force overridden.
	 *
	 * @return string
	 */
	function force_wp_title() {
		global $wp_query;
		$old_wp_query = null;

		if ( ! $wp_query->is_main_query() ) {
			$old_wp_query = $wp_query;
			wp_reset_query();
		}

		$title = $this->title( '' );

		if ( ! empty( $old_wp_query ) ) {
			$GLOBALS['wp_query'] = $old_wp_query;
			unset( $old_wp_query );
		}

		return $title;
	}

	/**
	 * Outputs or returns the debug marker, which is also used for title replacement when force rewrite is active.
	 *
	 * @param bool $echo Whether or not to echo the debug marker.
	 *
	 * @return string
	 */
	public function debug_marker( $echo = true ) {
		$marker = '<!-- This site is optimized with the ' . $this->head_product_name() . ' v' . WPSEO_VERSION . ' - https://yoast.com/wordpress/plugins/seo/ -->';
		if ( $echo === false ) {
			return $marker;
		}
		else {
			echo "\n${marker}\n";
		}
	}

	/**
	 * Output Webmaster Tools authentication strings
	 */
	public function webmaster_tools_authentication() {
		// Alexa.
		if ( $this->options['alexaverify'] !== '' ) {
			echo '<meta name="alexaVerifyID" content="' . esc_attr( $this->options['alexaverify'] ) . "\" />\n";
		}

		// Bing.
		if ( $this->options['msverify'] !== '' ) {
			echo '<meta name="msvalidate.01" content="' . esc_attr( $this->options['msverify'] ) . "\" />\n";
		}

		// Google.
		if ( $this->options['googleverify'] !== '' ) {
			echo '<meta name="google-site-verification" content="' . esc_attr( $this->options['googleverify'] ) . "\" />\n";
		}

		// Pinterest.
		if ( $this->options['pinterestverify'] !== '' ) {
			echo '<meta name="p:domain_verify" content="' . esc_attr( $this->options['pinterestverify'] ) . "\" />\n";
		}

		// Yandex.
		if ( $this->options['yandexverify'] !== '' ) {
			echo '<meta name="yandex-verification" content="' . esc_attr( $this->options['yandexverify'] ) . "\" />\n";
		}
	}

	/**
	 * Main wrapper function attached to wp_head. This combines all the output on the frontend of the Yoast SEO plugin.
	 */
	public function head() {
		global $wp_query;

		$old_wp_query = null;

		if ( ! $wp_query->is_main_query() ) {
			$old_wp_query = $wp_query;
			wp_reset_query();
		}

		/**
		 * Action: 'wpseo_head' - Allow other plugins to output inside the Yoast SEO section of the head section.
		 */
		do_action( 'wpseo_head' );

		echo '<!-- / ', $this->head_product_name(), ". -->\n\n";

		if ( ! empty( $old_wp_query ) ) {
			$GLOBALS['wp_query'] = $old_wp_query;
			unset( $old_wp_query );
		}

		return;
	}


	/**
	 * Output the meta robots value.
	 *
	 * @return string
	 */
	public function robots() {
		global $wp_query;

		$robots           = array();
		$robots['index']  = 'index';
		$robots['follow'] = 'follow';
		$robots['other']  = array();

		if ( is_singular() ) {
			global $post;

			if ( is_object( $post ) && ( isset( $this->options[ 'noindex-' . $post->post_type ] ) && $this->options[ 'noindex-' . $post->post_type ] === true ) ) {
				$robots['index'] = 'noindex';
			}

			if ( 'private' == $post->post_status ) {
				$robots['index'] = 'noindex';
			}

			$robots = $this->robots_for_single_post( $robots );

		}
		else {
			if ( is_search() ) {
				$robots['index'] = 'noindex';
			}
			elseif ( is_tax() || is_tag() || is_category() ) {
				$term = $wp_query->get_queried_object();
				if ( is_object( $term ) && ( isset( $this->options[ 'noindex-tax-' . $term->taxonomy ] ) && $this->options[ 'noindex-tax-' . $term->taxonomy ] === true ) ) {
					$robots['index'] = 'noindex';
				}

				// Three possible values, index, noindex and default, do nothing for default.
				$term_meta = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'noindex' );
				if ( is_string( $term_meta ) && 'default' !== $term_meta ) {
					$robots['index'] = $term_meta;
				}
			}
			elseif (
				( is_author() && $this->options['noindex-author-wpseo'] === true ) ||
				( is_date() && $this->options['noindex-archive-wpseo'] === true )
			) {
				$robots['index'] = 'noindex';
			}
			elseif ( is_home() ) {
				if ( get_query_var( 'paged' ) > 1 && $this->options['noindex-subpages-wpseo'] === true ) {
					$robots['index'] = 'noindex';
				}

				$page_for_posts = get_option( 'page_for_posts' );
				if ( $page_for_posts ) {
					$robots = $this->robots_for_single_post( $robots, $page_for_posts );
				}
				unset( $page_for_posts );

			}
			elseif ( is_post_type_archive() ) {
				$post_type = get_query_var( 'post_type' );

				if ( is_array( $post_type ) ) {
					$post_type = reset( $post_type );
				}

				if ( isset( $this->options[ 'noindex-ptarchive-' . $post_type ] ) && $this->options[ 'noindex-ptarchive-' . $post_type ] === true ) {
					$robots['index'] = 'noindex';
				}
			}

			if ( isset( $wp_query->query_vars['paged'] ) && ( $wp_query->query_vars['paged'] && $wp_query->query_vars['paged'] > 1 ) && ( $this->options['noindex-subpages-wpseo'] === true ) ) {
				$robots['index']  = 'noindex';
				$robots['follow'] = 'follow';
			}

			foreach ( array( 'noodp', 'noydir' ) as $robot ) {
				if ( $this->options[ $robot ] === true ) {
					$robots['other'][] = $robot;
				}
			}
			unset( $robot );
		}

		// Force override to respect the WP settings.
		if ( '0' == get_option( 'blog_public' ) || isset( $_GET['replytocom'] ) ) {
			$robots['index'] = 'noindex';
		}


		$robotsstr = $robots['index'] . ',' . $robots['follow'];

		if ( $robots['other'] !== array() ) {
			$robots['other'] = array_unique( $robots['other'] ); // TODO Most likely no longer needed, needs testing.
			$robotsstr .= ',' . implode( ',', $robots['other'] );
		}

		$robotsstr = preg_replace( '`^index,follow,?`', '', $robotsstr );

		/**
		 * Filter: 'wpseo_robots' - Allows filtering of the meta robots output of Yoast SEO
		 *
		 * @api string $robotsstr The meta robots directives to be echoed.
		 */
		$robotsstr = apply_filters( 'wpseo_robots', $robotsstr );

		if ( is_string( $robotsstr ) && $robotsstr !== '' ) {
			echo '<meta name="robots" content="', esc_attr( $robotsstr ), '"/>', "\n";
		}

		return $robotsstr;
	}

	/**
	 * Determine $robots values for a single post
	 *
	 * @param array      $robots
	 * @param int|string $post_id The post ID for which to determine the $robots values, defaults to current post.
	 *
	 * @return    array
	 */
	public function robots_for_single_post( $robots, $post_id = 0 ) {
		$noindex = WPSEO_Meta::get_value( 'meta-robots-noindex', $post_id );
		if ( $noindex === '1' ) {
			$robots['index'] = 'noindex';
		}
		elseif ( $noindex === '2' ) {
			$robots['index'] = 'index';
		}

		if ( WPSEO_Meta::get_value( 'meta-robots-nofollow', $post_id ) === '1' ) {
			$robots['follow'] = 'nofollow';
		}

		$meta_robots_adv = WPSEO_Meta::get_value( 'meta-robots-adv', $post_id );

		if ( $meta_robots_adv !== '' && ( $meta_robots_adv !== '-' && $meta_robots_adv !== 'none' ) ) {
			$meta_robots_adv = explode( ',', $meta_robots_adv );
			foreach ( $meta_robots_adv as $robot ) {
				$robots['other'][] = $robot;
			}
			unset( $robot );
		}
		elseif ( $meta_robots_adv === '' || $meta_robots_adv === '-' ) {
			foreach ( array( 'noodp', 'noydir' ) as $robot ) {
				if ( $this->options[ $robot ] === true ) {
					$robots['other'][] = $robot;
				}
			}
			unset( $robot );
		}
		unset( $meta_robots_adv );

		return $robots;
	}

	/**
	 * This function normally outputs the canonical but is also used in other places to retrieve
	 * the canonical URL for the current page.
	 *
	 * @param bool $echo        Whether or not to output the canonical element.
	 * @param bool $un_paged    Whether or not to return the canonical with or without pagination added to the URL.
	 * @param bool $no_override Whether or not to return a manually overridden canonical.
	 *
	 * @return string $canonical
	 */
	public function canonical( $echo = true, $un_paged = false, $no_override = false ) {
		if ( is_null( $this->canonical ) ) {
			$this->generate_canonical();
		}

		$canonical = $this->canonical;

		if ( $un_paged ) {
			$canonical = $this->canonical_unpaged;
		}
		elseif ( $no_override ) {
			$canonical = $this->canonical_no_override;
		}

		if ( $echo === false ) {
			return $canonical;
		}

		if ( is_string( $canonical ) && '' !== $canonical ) {
			echo '<link rel="canonical" href="' . esc_url( $canonical, null, 'other' ) . '" />' . "\n";
		}
	}

	/**
	 * This function normally outputs the canonical but is also used in other places to retrieve
	 * the canonical URL for the current page.
	 *
	 * @return void
	 */
	private function generate_canonical() {
		$canonical          = false;
		$canonical_override = false;

		// Set decent canonicals for homepage, singulars and taxonomy pages.
		if ( is_singular() ) {
			$obj       = get_queried_object();
			$canonical = get_permalink( $obj->ID );

			$this->canonical_unpaged = $canonical;

			$canonical_override = WPSEO_Meta::get_value( 'canonical' );

			// Fix paginated pages canonical, but only if the page is truly paginated.
			if ( get_query_var( 'page' ) > 1 ) {
				$num_pages = ( substr_count( $obj->post_content, '<!--nextpage-->' ) + 1 );
				if ( $num_pages && get_query_var( 'page' ) <= $num_pages ) {
					if ( ! $GLOBALS['wp_rewrite']->using_permalinks() ) {
						$canonical = add_query_arg( 'page', get_query_var( 'page' ), $canonical );
					}
					else {
						$canonical = user_trailingslashit( trailingslashit( $canonical ) . get_query_var( 'page' ) );
					}
				}
			}
		}
		else {
			if ( is_search() ) {
				$canonical = get_search_link();
			}
			elseif ( is_front_page() ) {
				$canonical = home_url();
			}
			elseif ( $this->is_posts_page() ) {
				$canonical = get_permalink( get_option( 'page_for_posts' ) );
			}
			elseif ( is_tax() || is_tag() || is_category() ) {
				$term = get_queried_object();

				$canonical_override = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'canonical' );

				$canonical = get_term_link( $term, $term->taxonomy );
			}
			elseif ( is_post_type_archive() ) {
				$post_type = get_query_var( 'post_type' );
				if ( is_array( $post_type ) ) {
					$post_type = reset( $post_type );
				}
				$canonical = get_post_type_archive_link( $post_type );
			}
			elseif ( is_author() ) {
				$canonical = get_author_posts_url( get_query_var( 'author' ), get_query_var( 'author_name' ) );
			}
			elseif ( is_archive() ) {
				if ( is_date() ) {
					if ( is_day() ) {
						$canonical = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
					}
					elseif ( is_month() ) {
						$canonical = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
					}
					elseif ( is_year() ) {
						$canonical = get_year_link( get_query_var( 'year' ) );
					}
				}
			}

			$this->canonical_unpaged = $canonical;

			if ( $canonical && get_query_var( 'paged' ) > 1 ) {
				global $wp_rewrite;
				if ( ! $wp_rewrite->using_permalinks() ) {
					if ( is_front_page() ) {
						$canonical = trailingslashit( $canonical );
					}
					$canonical = add_query_arg( 'paged', get_query_var( 'paged' ), $canonical );
				}
				else {
					if ( is_front_page() ) {
						$canonical = wpseo_xml_sitemaps_base_url( '' );
					}
					$canonical = user_trailingslashit( trailingslashit( $canonical ) . trailingslashit( $wp_rewrite->pagination_base ) . get_query_var( 'paged' ) );
				}
			}
		}

		$this->canonical_no_override = $canonical;

		if ( is_string( $canonical ) && $canonical !== '' ) {
			// Force canonical links to be absolute, relative is NOT an option.
			if ( WPSEO_Utils::is_url_relative( $canonical ) === true ) {
				$canonical = $this->base_url( $canonical );
			}
		}

		/**
		 * Filter: 'wpseo_canonical' - Allow filtering of the canonical URL put out by Yoast SEO
		 *
		 * @api string $canonical The canonical URL
		 */
		$canonical = apply_filters( 'wpseo_canonical', $canonical );

		if ( is_string( $canonical_override ) && $canonical_override !== '' ) {
			$this->canonical = $canonical_override;
		}
		else {
			$this->canonical = $canonical;
		}
	}

	/**
	 * Parse the home URL setting to find the base URL for relative URLs.
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	private function base_url( $path = null ) {
		$url = get_option( 'home' );

		$parts = parse_url( $url );

		$base_url = trailingslashit( $parts['scheme'] . '://' . $parts['host'] );

		if ( ! is_null( $path ) ) {
			$base_url .= ltrim( $path, '/' );
		}

		return $base_url;
	}

	/**
	 * Adds 'prev' and 'next' links to archives.
	 *
	 * @link  http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
	 * @since 1.0.3
	 */
	public function adjacent_rel_links() {
		// Don't do this for Genesis, as the way Genesis handles homepage functionality is different and causes issues sometimes.
		/**
		 * Filter 'wpseo_genesis_force_adjacent_rel_home' - Allows devs to allow echoing rel="next" / rel="prev" by Yoast SEO on Genesis installs
		 *
		 * @api bool $unsigned Whether or not to rel=next / rel=prev
		 */
		if ( is_home() && function_exists( 'genesis' ) && apply_filters( 'wpseo_genesis_force_adjacent_rel_home', false ) === false ) {
			return;
		}

		global $wp_query;

		if ( ! is_singular() ) {
			$url = $this->canonical( false, true, true );

			if ( is_string( $url ) && $url !== '' ) {
				$paged = get_query_var( 'paged' );

				if ( 0 == $paged ) {
					$paged = 1;
				}

				if ( $paged == 2 ) {
					$this->adjacent_rel_link( 'prev', $url, ( $paged - 1 ), true );
				}

				// Make sure to use index.php when needed, done after paged == 2 check so the prev links to homepage will not have index.php erroneously.
				if ( is_front_page() ) {
					$url = wpseo_xml_sitemaps_base_url( '' );
				}

				if ( $paged > 2 ) {
					$this->adjacent_rel_link( 'prev', $url, ( $paged - 1 ), true );
				}

				if ( $paged < $wp_query->max_num_pages ) {
					$this->adjacent_rel_link( 'next', $url, ( $paged + 1 ), true );
				}
			}
		}
		else {
			$numpages = 0;
			if ( isset( $wp_query->post->post_content ) ) {
				$numpages = ( substr_count( $wp_query->post->post_content, '<!--nextpage-->' ) + 1 );
			}
			if ( $numpages > 1 ) {
				$page = get_query_var( 'page' );
				if ( ! $page ) {
					$page = 1;
				}

				$url = get_permalink( $wp_query->post->ID );

				// If the current page is the frontpage, pagination should use /base/.
				if ( $this->is_home_static_page() ) {
					$usebase = true;
				}
				else {
					$usebase = false;
				}

				if ( $page > 1 ) {
					$this->adjacent_rel_link( 'prev', $url, ( $page - 1 ), $usebase, 'single_paged' );
				}
				if ( $page < $numpages ) {
					$this->adjacent_rel_link( 'next', $url, ( $page + 1 ), $usebase, 'single_paged' );
				}
			}
		}
	}

	/**
	 * Get adjacent pages link for archives
	 *
	 * @since 1.0.2
	 *
	 * @param string  $rel                  Link relationship, prev or next.
	 * @param string  $url                  the un-paginated URL of the current archive.
	 * @param string  $page                 the page number to add on to $url for the $link tag.
	 * @param boolean $incl_pagination_base whether or not to include /page/ or not.
	 *
	 * @return void
	 */
	private function adjacent_rel_link( $rel, $url, $page, $incl_pagination_base ) {
		global $wp_rewrite;
		if ( ! $wp_rewrite->using_permalinks() ) {
			if ( $page > 1 ) {
				$url = add_query_arg( 'paged', $page, $url );
			}
		}
		else {
			if ( $page > 1 ) {
				$base = '';
				if ( $incl_pagination_base ) {
					$base = trailingslashit( $wp_rewrite->pagination_base );
				}
				$url = user_trailingslashit( trailingslashit( $url ) . $base . $page );
			}
		}
		/**
		 * Filter: 'wpseo_' . $rel . '_rel_link' - Allow changing link rel output by Yoast SEO
		 *
		 * @api string $unsigned The full `<link` element.
		 */
		$link = apply_filters( 'wpseo_' . $rel . '_rel_link', '<link rel="' . esc_attr( $rel ) . '" href="' . esc_url( $url ) . "\" />\n" );

		if ( is_string( $link ) && $link !== '' ) {
			echo $link;
		}
	}

	/**
	 * Output the rel=publisher code on every page of the site.
	 *
	 * @return boolean Boolean indicating whether the publisher link was printed
	 */
	public function publisher() {

		if ( $this->options['plus-publisher'] !== '' ) {
			echo '<link rel="publisher" href="', esc_url( $this->options['plus-publisher'] ), '"/>', "\n";

			return true;
		}

		return false;
	}

	/**
	 * Outputs the meta keywords element.
	 *
	 * @return void
	 */
	public function metakeywords() {
		global $wp_query, $post;

		if ( $this->options['usemetakeywords'] === false ) {
			return;
		}

		$keywords = '';

		if ( is_singular() ) {
			$keywords = WPSEO_Meta::get_value( 'metakeywords' );
			if ( $keywords === '' && ( is_object( $post ) && ( ( isset( $this->options[ 'metakey-' . $post->post_type ] ) && $this->options[ 'metakey-' . $post->post_type ] !== '' ) ) ) ) {
				$keywords = wpseo_replace_vars( $this->options[ 'metakey-' . $post->post_type ], $post );
			}
		}
		else {
			if ( $this->is_home_posts_page() && $this->options['metakey-home-wpseo'] !== '' ) {
				$keywords = wpseo_replace_vars( $this->options['metakey-home-wpseo'], array() );
			}
			elseif ( $this->is_home_static_page() ) {
				$keywords = WPSEO_Meta::get_value( 'metakeywords' );
				if ( $keywords === '' && ( is_object( $post ) && ( isset( $this->options[ 'metakey-' . $post->post_type ] ) && $this->options[ 'metakey-' . $post->post_type ] !== '' ) ) ) {
					$keywords = wpseo_replace_vars( $this->options[ 'metakey-' . $post->post_type ], $post );
				}
			}
			elseif ( is_category() || is_tag() || is_tax() ) {
				$term = $wp_query->get_queried_object();

				if ( is_object( $term ) ) {
					$keywords = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'metakey' );
					if ( ( ! is_string( $keywords ) || $keywords === '' ) && ( isset( $this->options[ 'metakey-tax-' . $term->taxonomy ] ) && $this->options[ 'metakey-tax-' . $term->taxonomy ] !== '' ) ) {
						$keywords = wpseo_replace_vars( $this->options[ 'metakey-tax-' . $term->taxonomy ], $term );
					}
				}
			}
			elseif ( is_author() ) {
				$author_id = get_query_var( 'author' );
				$keywords  = get_the_author_meta( 'metakey', $author_id );
				if ( ! $keywords && $this->options['metakey-author-wpseo'] !== '' ) {
					$keywords = wpseo_replace_vars( $this->options['metakey-author-wpseo'], $wp_query->get_queried_object() );
				}
			}
			elseif ( is_post_type_archive() ) {
				$post_type = get_query_var( 'post_type' );
				if ( is_array( $post_type ) ) {
					$post_type = reset( $post_type );
				}
				if ( isset( $this->options[ 'metakey-ptarchive-' . $post_type ] ) && $this->options[ 'metakey-ptarchive-' . $post_type ] !== '' ) {
					$keywords = wpseo_replace_vars( $this->options[ 'metakey-ptarchive-' . $post_type ], $wp_query->get_queried_object() );
				}
			}
		}

		$keywords = apply_filters( 'wpseo_metakey', trim( $keywords ) ); // TODO Make deprecated.

		/**
		 * Filter: 'wpseo_metakeywords' - Allow changing the Yoast SEO meta keywords
		 *
		 * @api string $keywords The meta keywords to be echoed.
		 */
		$keywords = apply_filters( 'wpseo_metakeywords', trim( $keywords ) ); // More appropriately named.

		if ( is_string( $keywords ) && $keywords !== '' ) {
			echo '<meta name="keywords" content="', esc_attr( strip_tags( stripslashes( $keywords ) ) ), '"/>', "\n";
		}
	}

	/**
	 * Outputs the meta description element or returns the description text.
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function metadesc( $echo = true ) {
		if ( is_null( $this->metadesc ) ) {
			$this->generate_metadesc();
		}

		if ( $echo !== false ) {
			if ( is_string( $this->metadesc ) && $this->metadesc !== '' ) {
				echo '<meta name="description" content="', esc_attr( strip_tags( stripslashes( $this->metadesc ) ) ), '"/>', "\n";
			}
			elseif ( current_user_can( 'manage_options' ) && is_singular() ) {
				echo '<!-- ', __( 'Admin only notice: this page doesn\'t show a meta description because it doesn\'t have one, either write it for this page specifically or go into the SEO -> Titles menu and set up a template.', 'wordpress-seo' ), ' -->', "\n";
			}
		}
		else {
			return $this->metadesc;
		}
	}

	/**
	 * Generates the meta description text.
	 */
	private function generate_metadesc() {
		global $post, $wp_query;

		$metadesc          = '';
		$metadesc_override = false;
		$post_type         = '';
		$template          = '';

		if ( is_object( $post ) && ( isset( $post->post_type ) && $post->post_type !== '' ) ) {
			$post_type = $post->post_type;
		}

		if ( is_singular() ) {
			if ( ( $metadesc === '' && $post_type !== '' ) && isset( $this->options[ 'metadesc-' . $post_type ] ) ) {
				$template = $this->options[ 'metadesc-' . $post_type ];
				$term     = $post;
			}
			$metadesc_override = WPSEO_Meta::get_value( 'metadesc' );
		}
		else {
			if ( is_search() ) {
				$metadesc = '';
			}
			elseif ( $this->is_home_posts_page() ) {
				$template = $this->options['metadesc-home-wpseo'];
				$term     = array();

				if ( empty( $template ) ) {
					$template = get_bloginfo( 'description' );
				}
			}
			elseif ( $this->is_posts_page() ) {
				$metadesc = WPSEO_Meta::get_value( 'metadesc', get_option( 'page_for_posts' ) );
				if ( ( $metadesc === '' && $post_type !== '' ) && isset( $this->options[ 'metadesc-' . $post_type ] ) ) {
					$page     = get_post( get_option( 'page_for_posts' ) );
					$template = $this->options[ 'metadesc-' . $post_type ];
					$term     = $page;
				}
			}
			elseif ( $this->is_home_static_page() ) {
				$metadesc = WPSEO_Meta::get_value( 'metadesc' );
				if ( ( $metadesc === '' && $post_type !== '' ) && isset( $this->options[ 'metadesc-' . $post_type ] ) ) {
					$template = $this->options[ 'metadesc-' . $post_type ];
				}
			}
			elseif ( is_category() || is_tag() || is_tax() ) {
				$term              = $wp_query->get_queried_object();
				$metadesc_override = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'desc' );
				if ( is_object( $term ) && isset( $term->taxonomy, $this->options[ 'metadesc-tax-' . $term->taxonomy ] ) ) {
					$template = $this->options[ 'metadesc-tax-' . $term->taxonomy ];
				}
			}
			elseif ( is_author() ) {
				$author_id = get_query_var( 'author' );
				$metadesc  = get_the_author_meta( 'wpseo_metadesc', $author_id );
				if ( ( ! is_string( $metadesc ) || $metadesc === '' ) && '' !== $this->options['metadesc-author-wpseo'] ) {
					$template = $this->options['metadesc-author-wpseo'];
				}
			}
			elseif ( is_post_type_archive() ) {
				$post_type = get_query_var( 'post_type' );
				if ( is_array( $post_type ) ) {
					$post_type = reset( $post_type );
				}
				if ( isset( $this->options[ 'metadesc-ptarchive-' . $post_type ] ) ) {
					$template = $this->options[ 'metadesc-ptarchive-' . $post_type ];
				}
			}
			elseif ( is_archive() ) {
				$template = $this->options['metadesc-archive-wpseo'];
			}

			// If we're on a paginated page, and the template doesn't change for paginated pages, bail.
			if ( ( ! is_string( $metadesc ) || $metadesc === '' ) && get_query_var( 'paged' ) && get_query_var( 'paged' ) > 1 && $template !== '' ) {
				if ( strpos( $template, '%%page' ) === false ) {
					$metadesc = '';
				}
			}
		}

		$post_data = $post;

		if ( is_string( $metadesc_override ) && '' !== $metadesc_override ) {
			$metadesc = $metadesc_override;
			if ( isset( $term ) ) {
				$post_data = $term;
			}
		}
		else if ( ( ! is_string( $metadesc ) || '' === $metadesc ) && '' !== $template ) {
			if ( ! isset( $term ) ) {
				$term = $wp_query->get_queried_object();
			}

			$metadesc  = $template;
			$post_data = $term;
		}

		$metadesc = wpseo_replace_vars( $metadesc, $post_data );

		/**
		 * Filter: 'wpseo_metadesc' - Allow changing the Yoast SEO meta description sentence.
		 *
		 * @api string $metadesc The description sentence.
		 */
		$this->metadesc = apply_filters( 'wpseo_metadesc', trim( $metadesc ) );
	}

	/**
	 * Based on the redirect meta value, this function determines whether it should redirect the current post / page.
	 *
	 * @return boolean
	 */
	function page_redirect() {
		if ( is_singular() ) {
			global $post;
			if ( ! isset( $post ) || ! is_object( $post ) ) {
				return false;
			}

			$redir = WPSEO_Meta::get_value( 'redirect', $post->ID );
			if ( $redir !== '' ) {
				wp_redirect( $redir, 301 );
				exit;
			}
		}

		return false;
	}

	/**
	 * Outputs noindex values for the current page.
	 */
	public function noindex_page() {
		echo '<meta name="robots" content="noindex" />', "\n";
	}

	/**
	 * Send a Robots HTTP header preventing URL from being indexed in the search results while allowing search engines
	 * to follow the links in the object at the URL.
	 *
	 * @since 1.1.7
	 * @return boolean Boolean indicating whether the noindex header was sent
	 */
	public function noindex_feed() {

		if ( ( is_feed() || is_robots() ) && headers_sent() === false ) {
			header( 'X-Robots-Tag: noindex,follow', true );

			return true;
		}

		return false;
	}

	/**
	 * Adds rel="nofollow" to a link, only used for login / registration links.
	 *
	 * @param string $input The link element as a string.
	 *
	 * @return string
	 */
	public function nofollow_link( $input ) {
		return str_replace( '<a ', '<a rel="nofollow" ', $input );
	}

	/**
	 * When certain archives are disabled, this redirects those to the homepage.
	 * @return boolean False when no redirect was triggered
	 */
	function archive_redirect() {
		global $wp_query;

		if (
			( $this->options['disable-date'] === true && $wp_query->is_date ) ||
			( $this->options['disable-author'] === true && $wp_query->is_author ) ||
			( isset( $this->options['disable-post_formats'] ) && $this->options['disable-post_formats'] && $wp_query->is_tax( 'post_format' ) )
		) {
			wp_safe_redirect( get_bloginfo( 'url' ), 301 );
			exit;
		}

		return false;
	}

	/**
	 * If the option to redirect attachments to their parent is checked, this performs the redirect.
	 *
	 * An extra check is done for when the attachment has no parent.
	 *
	 * @return boolean False when no redirect was triggered
	 */
	function attachment_redirect() {
		global $post;
		if ( is_attachment() && ( ( is_object( $post ) && isset( $post->post_parent ) ) && ( is_numeric( $post->post_parent ) && $post->post_parent != 0 ) ) ) {
			wp_safe_redirect( get_permalink( $post->post_parent ), 301 );
			exit;
		}

		return false;
	}

	/**
	 * Trailing slashes for everything except is_single().
	 *
	 * Thanks to Mark Jaquith for this code.
	 *
	 * @param string $url
	 * @param string $type
	 *
	 * @return string
	 */
	function add_trailingslash( $url, $type ) {
		if ( 'single' === $type || 'single_paged' === $type ) {
			return $url;
		}
		else {
			return trailingslashit( $url );
		}
	}

	/**
	 * Removes the ?replytocom variable from the link, replacing it with a #comment-<number> anchor.
	 *
	 * @todo Should this function also allow for relative urls ?
	 *
	 * @param string $link The comment link as a string.
	 *
	 * @return string
	 */
	public function remove_reply_to_com( $link ) {
		return preg_replace( '`href=(["\'])(?:.*(?:\?|&|&#038;)replytocom=(\d+)#respond)`', 'href=$1#comment-$2', $link );
	}

	/**
	 * Redirect out the ?replytocom variables when cleanreplytocom is enabled
	 *
	 * @since 1.4.13
	 * @return boolean
	 */
	function replytocom_redirect() {

		if ( isset( $_GET['replytocom'] ) && is_singular() ) {
			$url          = get_permalink( $GLOBALS['post']->ID );
			$hash         = sanitize_text_field( $_GET['replytocom'] );
			$query_string = remove_query_arg( 'replytocom', sanitize_text_field( $_SERVER['QUERY_STRING'] ) );
			if ( ! empty( $query_string ) ) {
				$url .= '?' . $query_string;
			}
			$url .= '#comment-' . $hash;
			wp_safe_redirect( $url, 301 );
			exit;
		}

		return false;
	}

	/**
	 * Removes unneeded query variables from the URL.
	 * @return boolean
	 */
	public function clean_permalink() {
		if ( is_robots() || get_query_var( 'sitemap' ) ) {
			return false;
		}

		global $wp_query;

		// Recreate current URL.
		$cururl = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
			$cururl .= 's';
		}
		$cururl .= '://';

		if ( $_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443' ) {
			$cururl .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		}
		else {
			$cururl .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		$properurl = '';

		if ( is_singular() ) {
			global $post;
			if ( empty( $post ) ) {
				$post = $wp_query->get_queried_object();
			}

			$properurl = get_permalink( $post->ID );

			$page = get_query_var( 'page' );
			if ( $page && $page != 1 ) {
				$post       = get_post( $post->ID );
				$page_count = substr_count( $post->post_content, '<!--nextpage-->' );
				if ( $page > ( $page_count + 1 ) ) {
					$properurl = user_trailingslashit( trailingslashit( $properurl ) . ( $page_count + 1 ) );
				}
				else {
					$properurl = user_trailingslashit( trailingslashit( $properurl ) . $page );
				}
			}

			// Fix reply to comment links, whoever decided this should be a GET variable?
			if ( preg_match( '`(\?replytocom=[^&]+)`', sanitize_text_field( $_SERVER['REQUEST_URI'] ), $matches ) ) {
				$properurl .= str_replace( '?replytocom=', '#comment-', $matches[0] );
			}
			unset( $matches );

			// Prevent cleaning out posts & page previews for people capable of viewing them.
			if ( isset( $_GET['preview'], $_GET['preview_nonce'] ) && current_user_can( 'edit_post' ) ) {
				$properurl = '';
			}
		}
		elseif ( is_front_page() ) {
			if ( $this->is_home_posts_page() ) {
				$properurl = get_bloginfo( 'url' ) . '/';
			}
			elseif ( $this->is_home_static_page() ) {
				$properurl = get_permalink( $GLOBALS['post']->ID );
			}
		}
		elseif ( is_category() || is_tag() || is_tax() ) {
			$term = $wp_query->get_queried_object();
			if ( is_feed() ) {
				$properurl = get_term_feed_link( $term->term_id, $term->taxonomy );
			}
			else {
				$properurl = get_term_link( $term, $term->taxonomy );
			}
		}
		elseif ( is_search() ) {
			$s         = urlencode( preg_replace( '`(%20|\+)`', ' ', get_search_query() ) );
			$properurl = get_bloginfo( 'url' ) . '/?s=' . $s;
		}
		elseif ( is_404() ) {
			if ( is_multisite() && ! is_subdomain_install() && is_main_site() ) {
				if ( $cururl == get_bloginfo( 'url' ) . '/blog/' || $cururl == get_bloginfo( 'url' ) . '/blog' ) {
					if ( $this->is_home_static_page() ) {
						$properurl = get_permalink( get_option( 'page_for_posts' ) );
					}
					else {
						$properurl = get_bloginfo( 'url' ) . '/';
					}
				}
			}
		}

		if ( ! empty( $properurl ) && $wp_query->query_vars['paged'] != 0 && $wp_query->post_count != 0 ) {
			if ( is_search() && ! empty( $s ) ) {
				$properurl = get_bloginfo( 'url' ) . '/page/' . $wp_query->query_vars['paged'] . '/?s=' . $s;
			}
			else {
				$properurl = user_trailingslashit( trailingslashit( $properurl ) . 'page/' . $wp_query->query_vars['paged'] );
			}
		}

		// Prevent cleaning out the WP Subscription managers interface for everyone.
		if ( isset( $_GET['wp-subscription-manager'] ) ) {
			$properurl = '';
		}

		/**
		 * Filter: 'wpseo_whitelist_permalink_vars' - Allow plugins to register their own variables not to clean
		 *
		 * @api array $unsigned Array of permalink variables _not_ to clean. Empty by default.
		 */
		$whitelisted_extravars = apply_filters( 'wpseo_whitelist_permalink_vars', array() );

		if ( $this->options['cleanpermalink-googlesitesearch'] === true ) {
			// Prevent cleaning out Google Site searches.
			$whitelisted_extravars = array_merge( $whitelisted_extravars, array(
				'q',
				'cx',
				'debug',
				'cof',
				'ie',
				'sa',
			) );
		}

		if ( $this->options['cleanpermalink-googlecampaign'] === true ) {
			// Prevent cleaning out Google Analytics campaign variables.
			$whitelisted_extravars = array_merge( $whitelisted_extravars, array(
				'utm_campaign',
				'utm_medium',
				'utm_source',
				'utm_content',
				'utm_term',
				'utm_id',
				'gclid',
			) );
		}

		if ( $this->options['cleanpermalink-extravars'] !== '' ) {
			$extravars             = explode( ',', $this->options['cleanpermalink-extravars'] );
			$extravars             = array_map( 'trim', $extravars );
			$whitelisted_extravars = array_merge( $whitelisted_extravars, $extravars );
			unset( $extravars );
		}

		foreach ( $whitelisted_extravars as $get ) {
			if ( isset( $_GET[ trim( $get ) ] ) ) {
				$properurl = '';
			}
		}
		unset( $get );

		if ( ! empty( $properurl ) && $cururl != $properurl ) {
			wp_safe_redirect( $properurl, 301 );
			exit;
		}
	}

	/**
	 * Replaces the possible RSS variables with their actual values.
	 *
	 * @param string $content The RSS content that should have the variables replaced.
	 *
	 * @return string
	 */
	function rss_replace_vars( $content ) {
		global $post;

		/**
		 * Allow the developer to determine whether or not to follow the links in the bits Yoast SEO adds to the RSS feed, defaults to true.
		 *
		 * @api   bool $unsigned Whether or not to follow the links in RSS feed, defaults to true.
		 *
		 * @since 1.4.20
		 */
		$no_follow      = apply_filters( 'nofollow_rss_links', true );
		$no_follow_attr = '';
		if ( $no_follow === true ) {
			$no_follow_attr = 'rel="nofollow" ';
		}

		$author_link = '';
		if ( is_object( $post ) ) {
			$author_link = '<a ' . $no_follow_attr . 'href="' . esc_url( get_author_posts_url( $post->post_author ) ) . '">' . get_the_author() . '</a>';
		}

		$post_link      = '<a ' . $no_follow_attr . 'href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a>';
		$blog_link      = '<a ' . $no_follow_attr . 'href="' . esc_url( get_bloginfo( 'url' ) ) . '">' . get_bloginfo( 'name' ) . '</a>';
		$blog_desc_link = '<a ' . $no_follow_attr . 'href="' . esc_url( get_bloginfo( 'url' ) ) . '">' . get_bloginfo( 'name' ) . ' - ' . strip_tags( get_bloginfo( 'description' ) ) . '</a>';

		$content = stripslashes( trim( $content ) );
		$content = str_replace( '%%AUTHORLINK%%', $author_link, $content );
		$content = str_replace( '%%POSTLINK%%', $post_link, $content );
		$content = str_replace( '%%BLOGLINK%%', $blog_link, $content );
		$content = str_replace( '%%BLOGDESCLINK%%', $blog_desc_link, $content );

		return $content;
	}

	/**
	 * Adds the RSS footer (or header) to the full RSS feed item.
	 *
	 * @param string $content Feed item content.
	 *
	 * @return string
	 */
	function embed_rssfooter( $content ) {
		return $this->embed_rss( $content, 'full' );
	}

	/**
	 * Adds the RSS footer (or header) to the excerpt RSS feed item.
	 *
	 * @param string $content Feed item excerpt.
	 *
	 * @return string
	 */
	function embed_rssfooter_excerpt( $content ) {
		return $this->embed_rss( $content, 'excerpt' );
	}

	/**
	 * Adds the RSS footer and/or header to an RSS feed item.
	 *
	 * @since 1.4.14
	 *
	 * @param string $content Feed item content.
	 * @param string $context Feed item context, either 'excerpt' or 'full'.
	 *
	 * @return string
	 */
	function embed_rss( $content, $context = 'full' ) {

		/**
		 * Filter: 'wpseo_include_rss_footer' - Allow the the RSS footer to be dynamically shown/hidden
		 *
		 * @api boolean $show_embed Indicates if the RSS footer should be shown or not
		 * @param string $context The context of the RSS content - 'full' or 'excerpt'.
		 */
		if ( ! apply_filters( 'wpseo_include_rss_footer', true, $context ) ) {
			return $content;
		}

		if ( is_feed() ) {
			$before = '';
			$after  = '';

			if ( $this->options['rssbefore'] !== '' ) {
				$before = wpautop( $this->rss_replace_vars( $this->options['rssbefore'] ) );
			}
			if ( $this->options['rssafter'] !== '' ) {
				$after = wpautop( $this->rss_replace_vars( $this->options['rssafter'] ) );
			}
			if ( $before !== '' || $after !== '' ) {
				if ( ( isset( $context ) && $context === 'excerpt' ) && trim( $content ) !== '' ) {
					$content = wpautop( $content );
				}
				$content = $before . $content . $after;
			}
		}

		return $content;
	}


	/**
	 * Used in the force rewrite functionality this retrieves the output, replaces the title with the proper SEO
	 * title and then flushes the output.
	 */
	function flush_cache() {

		global $wp_query;

		if ( $this->ob_started !== true ) {
			return false;
		}

		$content = ob_get_contents();
		ob_end_clean();

		$old_wp_query = $wp_query;

		wp_reset_query();

		$title = $this->title( '' );

		// Find all titles, strip them out and add the new one in within the debug marker, so it's easily identified whether a site uses force rewrite.
		$content = preg_replace( '/<title.*?\/title>/i', '', $content );
		$content = str_replace( $this->debug_marker( false ), $this->debug_marker( false ) . "\n" . '<title>' . $title . '</title>', $content );

		$GLOBALS['wp_query'] = $old_wp_query;

		echo $content;

		return true;
	}

	/**
	 * Starts the output buffer so it can later be fixed by flush_cache()
	 */
	function force_rewrite_output_buffer() {
		$this->ob_started = true;
		ob_start();
	}

	/**
	 * Function used in testing whether the title should be force rewritten or not.
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	function title_test_helper( $title ) {
		$wpseo_titles = get_option( 'wpseo_titles' );

		$wpseo_titles['title_test'] ++;
		update_option( 'wpseo_titles', $wpseo_titles );

		// Prevent this setting from being on forever when something breaks, as it breaks caching.
		if ( $wpseo_titles['title_test'] > 5 ) {
			$wpseo_titles['title_test'] = 0;
			update_option( 'wpseo_titles', $wpseo_titles );

			remove_filter( 'wpseo_title', array( $this, 'title_test_helper' ) );

			return $title;
		}

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		if ( ! defined( 'DONOTCACHCEOBJECT' ) ) {
			define( 'DONOTCACHCEOBJECT', true );
		}
		if ( ! defined( 'DONOTMINIFY' ) ) {
			define( 'DONOTMINIFY', true );
		}

		if ( $_SERVER['HTTP_USER_AGENT'] === "WordPress/{$GLOBALS['wp_version']}; " . get_bloginfo( 'url' ) . ' - Yoast' ) {
			return 'This is a Yoast Test Title';
		}

		return $title;
	}

	/**
	 * Get the product name in the head section
	 *
	 * @return string
	 */
	private function head_product_name() {
		if ( file_exists( WPSEO_PATH . 'premium/' ) ) {
			return 'Yoast SEO Premium plugin';
		}
		else {
			return 'Yoast SEO plugin';
		}
	}

} /* End of class */
