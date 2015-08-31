<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * This class handles the Breadcrumbs generation and display
 */
class WPSEO_Breadcrumbs {

	/**
	 * @var    object    Instance of this class
	 */
	public static $instance;

	/**
	 * @var string    Last used 'before' string
	 */
	public static $before = '';

	/**
	 * @var string    Last used 'after' string
	 */
	public static $after = '';


	/**
	 * @var    string    Blog's show on front setting, 'page' or 'posts'
	 */
	private $show_on_front;

	/**
	 * @var    mixed    Blog's page for posts setting, page id or false
	 */
	private $page_for_posts;

	/**
	 * @var mixed    Current post object
	 */
	private $post;

	/**
	 * @var    array    WPSEO options array from get_all()
	 */
	private $options;


	/**
	 * @var string    HTML wrapper element for a single breadcrumb element
	 */
	private $element = 'span';

	/**
	 * @var string    Yoast SEO breadcrumb separator
	 */
	private $separator = '';

	/**
	 * @var string    HTML wrapper element for the Yoast SEO breadcrumbs output
	 */
	private $wrapper = 'span';


	/**
	 * @var    array    Array of crumbs
	 *
	 * Each element of the crumbs array can either have one of these keys:
	 *    "id"         for post types;
	 *    "ptarchive"  for a post type archive;
	 *    "term"       for a taxonomy term.
	 * OR it consists of a predefined set of 'text', 'url' and 'allow_html'
	 */
	private $crumbs = array();

	/**
	 * @var array    Count of the elements in the $crumbs property
	 */
	private $crumb_count = 0;

	/**
	 * @var array    Array of individual (linked) html strings created from crumbs
	 */
	private $links = array();

	/**
	 * @var    string    Breadcrumb html string
	 */
	private $output;


	/**
	 * Create the breadcrumb
	 */
	private function __construct() {
		$this->options        = WPSEO_Options::get_all();
		$this->post           = ( isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null );
		$this->show_on_front  = get_option( 'show_on_front' );
		$this->page_for_posts = get_option( 'page_for_posts' );

		$this->filter_element();
		$this->filter_separator();
		$this->filter_wrapper();

		$this->set_crumbs();
		$this->prepare_links();
		$this->links_to_string();
		$this->wrap_breadcrumb();
	}

	/**
	 * Get breadcrumb string using the singleton instance of this class
	 *
	 * @param string $before
	 * @param string $after
	 * @param bool   $display Echo or return.
	 *
	 * @return object
	 */
	public static function breadcrumb( $before = '', $after = '', $display = true ) {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}
		// Remember the last used before/after for use in case the object goes __toString().
		self::$before = $before;
		self::$after  = $after;

		$output = $before . self::$instance->output . $after;

		if ( $display === true ) {
			echo $output;

			return true;
		}
		else {
			return $output;
		}
	}

	/**
	 * Magic method to use in case the class would be send to string
	 *
	 * @return string
	 */
	public function __toString() {
		return self::$before . $this->output . self::$after;
	}


	/**
	 * Filter: 'wpseo_breadcrumb_single_link_wrapper' - Allows developer to change or wrap each breadcrumb element
	 *
	 * @api string $element
	 */
	private function filter_element() {
		$this->element = esc_attr( apply_filters( 'wpseo_breadcrumb_single_link_wrapper', $this->element ) );
	}

	/**
	 * Filter: 'wpseo_breadcrumb_separator' - Allow (theme) developer to change the Yoast SEO breadcrumb separator.
	 *
	 * @api string $breadcrumbs_sep Breadcrumbs separator
	 */
	private function filter_separator() {
		$separator       = apply_filters( 'wpseo_breadcrumb_separator', $this->options['breadcrumbs-sep'] );
		$this->separator = ' ' . $separator . ' ';
	}

	/**
	 * Filter: 'wpseo_breadcrumb_output_wrapper' - Allow changing the HTML wrapper element for the Yoast SEO breadcrumbs output
	 *
	 * @api string $wrapper The wrapper element
	 */
	private function filter_wrapper() {
		$wrapper = apply_filters( 'wpseo_breadcrumb_output_wrapper', $this->wrapper );
		$wrapper = tag_escape( $wrapper );
		if ( is_string( $wrapper ) && '' !== $wrapper ) {
			$this->wrapper = $wrapper;
		}
	}


	/**
	 * Get a term's parents.
	 *
	 * @param    object $term Term to get the parents for.
	 *
	 * @return    array
	 */
	private function get_term_parents( $term ) {
		$tax     = $term->taxonomy;
		$parents = array();
		while ( $term->parent != 0 ) {
			$term      = get_term( $term->parent, $tax );
			$parents[] = $term;
		}

		return array_reverse( $parents );
	}

	/**
	 * Find the deepest term in an array of term objects
	 *
	 * @param  array $terms
	 *
	 * @return object
	 */
	private function find_deepest_term( $terms ) {
		/*
		Let's find the deepest term in this array, by looping through and then
		   unsetting every term that is used as a parent by another one in the array.
		*/
		$terms_by_id = array();
		foreach ( $terms as $term ) {
			$terms_by_id[ $term->term_id ] = $term;
		}
		foreach ( $terms as $term ) {
			unset( $terms_by_id[ $term->parent ] );
		}
		unset( $term );

		/*
		As we could still have two subcategories, from different parent categories,
		   let's pick the one with the lowest ordered ancestor.
		*/
		$parents_count = 0;
		$term_order    = 9999; // Because ASC.
		reset( $terms_by_id );
		$deepest_term = current( $terms_by_id );
		foreach ( $terms_by_id as $term ) {
			$parents = $this->get_term_parents( $term );

			if ( count( $parents ) >= $parents_count ) {
				$parents_count = count( $parents );

				// If higher count.
				if ( count( $parents ) > $parents_count ) {
					// Reset order.
					$term_order = 9999;
				}

				$parent_order = 9999; // Set default order.
				foreach ( $parents as $parent ) {
					if ( $parent->parent == 0 && isset( $parent->term_order ) ) {
						$parent_order = $parent->term_order;
					}
				}
				unset( $parent );

				// Check if parent has lowest order.
				if ( $parent_order < $term_order ) {
					$term_order   = $parent_order;
					$deepest_term = $term;
				}
			}
		}

		return $deepest_term;
	}

	/**
	 * Retrieve the hierachical ancestors for the current 'post'
	 *
	 * @return array
	 */
	private function get_post_ancestors() {
		$ancestors = array();

		if ( isset( $this->post->ancestors ) ) {
			if ( is_array( $this->post->ancestors ) ) {
				$ancestors = array_values( $this->post->ancestors );
			}
			else {
				$ancestors = array( $this->post->ancestors );
			}
		}
		elseif ( isset( $this->post->post_parent ) ) {
			$ancestors = array( $this->post->post_parent );
		}

		/**
		 * Filter: Allow changing the ancestors for the Yoast SEO breadcrumbs output
		 *
		 * @api array $ancestors Ancestors
		 */
		$ancestors = apply_filters( 'wp_seo_get_bc_ancestors', $ancestors );

		if ( ! is_array( $ancestors ) ) {
			trigger_error( 'The return value for the "wp_seo_get_bc_ancestors" filter should be an array.', E_USER_WARNING );
			$ancestors = (array) $ancestors;
		}

		// Reverse the order so it's oldest to newest.
		$ancestors = array_reverse( $ancestors );

		return $ancestors;
	}

	/**
	 * Determine the crumbs which should form the breadcrumb.
	 */
	private function set_crumbs() {
		/** @var WP_Query $wp_query */
		global $wp_query;

		$this->add_home_crumb();
		$this->maybe_add_blog_crumb();

		if ( ( $this->show_on_front === 'page' && is_front_page() ) || ( $this->show_on_front === 'posts' && is_home() ) ) {
			// Do nothing.
		}
		elseif ( $this->show_on_front == 'page' && is_home() ) {
			$this->add_blog_crumb();
		}
		elseif ( is_singular() ) {
			$this->maybe_add_pt_archive_crumb_for_post();

			if ( isset( $this->post->post_parent ) && 0 == $this->post->post_parent ) {
				$this->maybe_add_taxonomy_crumbs_for_post();
			}
			else {
				$this->add_post_ancestor_crumbs();
			}

			if ( isset( $this->post->ID ) ) {
				$this->add_single_post_crumb( $this->post->ID );
			}
		}
		else {
			if ( is_post_type_archive() ) {
				$post_type = $wp_query->get( 'post_type' );

				if ( $post_type ) {
					$this->add_ptarchive_crumb( $post_type );
				}
			}
			elseif ( is_tax() || is_tag() || is_category() ) {
				$this->add_crumbs_for_taxonomy();
			}
			elseif ( is_date() ) {
				if ( is_day() ) {
					$this->add_linked_month_year_crumb();
					$this->add_date_crumb();
				}
				elseif ( is_month() ) {
					$this->add_month_crumb();
				}
				elseif ( is_year() ) {
					$this->add_year_crumb();
				}
			}
			elseif ( is_author() ) {
				$user = $wp_query->get_queried_object();
				$this->add_predefined_crumb(
					$this->options['breadcrumbs-archiveprefix'] . ' ' . $user->display_name,
					null,
					true
				);
			}
			elseif ( is_search() ) {
				$this->add_predefined_crumb(
					$this->options['breadcrumbs-searchprefix'] . ' "' . esc_html( get_search_query() ) . '"',
					null,
					true
				);
			}
			elseif ( is_404() ) {

				if ( 0 !== get_query_var( 'year' ) || ( 0 !== get_query_var( 'monthnum' ) || 0 !== get_query_var( 'day' ) ) ) {
					if ( 'page' == $this->show_on_front && ! is_home() ) {
						if ( $this->page_for_posts && $this->options['breadcrumbs-blog-remove'] === false ) {
							$this->add_blog_crumb();
						}
					}

					if ( 0 !== get_query_var( 'day' ) ) {
						$this->add_linked_month_year_crumb();

						$date = sprintf( '%04d-%02d-%02d 00:00:00', get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
						$this->add_date_crumb( $date );
					}
					elseif ( 0 !== get_query_var( 'monthnum' ) ) {
						$this->add_month_crumb();
					}
					elseif ( 0 !== get_query_var( 'year' ) ) {
						$this->add_year_crumb();
					}
				}
				else {
					$this->add_predefined_crumb(
						$this->options['breadcrumbs-404crumb'],
						null,
						true
					);
				}
			}
		}

		/**
		 * Filter: 'wpseo_breadcrumb_links' - Allow the developer to filter the Yoast SEO breadcrumb links, add to them, change order, etc.
		 *
		 * @api array $crumbs The crumbs array
		 */
		$this->crumbs = apply_filters( 'wpseo_breadcrumb_links', $this->crumbs );

		$this->crumb_count = count( $this->crumbs );
	}


	/**
	 * Add a single id based crumb to the crumbs property
	 *
	 * @param int $id
	 */
	private function add_single_post_crumb( $id ) {
		$this->crumbs[] = array(
			'id' => $id,
		);
	}

	/**
	 * Add a term based crumb to the crumbs property
	 *
	 * @param object $term
	 */
	private function add_term_crumb( $term ) {
		$this->crumbs[] = array(
			'term' => $term,
		);
	}

	/**
	 * Add a ptarchive based crumb to the crumbs property
	 *
	 * @param string $pt Post type.
	 */
	private function add_ptarchive_crumb( $pt ) {
		$this->crumbs[] = array(
			'ptarchive' => $pt,
		);
	}

	/**
	 * Add a predefined crumb to the crumbs property
	 *
	 * @param string $text
	 * @param string $url
	 * @param bool   $allow_html
	 */
	private function add_predefined_crumb( $text, $url = '', $allow_html = false ) {
		$this->crumbs[] = array(
			'text'       => $text,
			'url'        => $url,
			'allow_html' => $allow_html,
		);
	}

	/**
	 * Add Homepage crumb to the crumbs property
	 */
	private function add_home_crumb() {
		$this->add_predefined_crumb(
			$this->options['breadcrumbs-home'],
			get_home_url(),
			true
		);
	}

	/**
	 * Add Blog crumb to the crumbs property
	 */
	private function add_blog_crumb() {
		$this->add_single_post_crumb( $this->page_for_posts );
	}

	/**
	 * Add Blog crumb to the crumbs property for single posts where Home != blogpage
	 */
	private function maybe_add_blog_crumb() {
		if ( ( 'page' === $this->show_on_front && 'post' === get_post_type() ) && ( ! is_home() && ! is_search() ) ) {
			if ( $this->page_for_posts && $this->options['breadcrumbs-blog-remove'] === false ) {
				$this->add_blog_crumb();
			}
		}
	}

	/**
	 * Add ptarchive crumb to the crumbs property if it can be linked to, for a single post
	 */
	private function maybe_add_pt_archive_crumb_for_post() {
		if ( isset( $this->post->post_type ) && get_post_type_archive_link( $this->post->post_type ) ) {
			$this->add_ptarchive_crumb( $this->post->post_type );
		}
	}

	/**
	 * Add taxonomy crumbs to the crumbs property for a single post
	 */
	private function maybe_add_taxonomy_crumbs_for_post() {
		if ( isset( $this->options[ 'post_types-' . $this->post->post_type . '-maintax' ] ) && $this->options[ 'post_types-' . $this->post->post_type . '-maintax' ] != '0' ) {
			$main_tax = $this->options[ 'post_types-' . $this->post->post_type . '-maintax' ];
			if ( isset( $this->post->ID ) ) {
				$terms = wp_get_object_terms( $this->post->ID, $main_tax );

				if ( is_array( $terms ) && $terms !== array() ) {

					$deepest_term = $this->find_deepest_term( $terms );

					if ( is_taxonomy_hierarchical( $main_tax ) && $deepest_term->parent != 0 ) {
						$parent_terms = $this->get_term_parents( $deepest_term );
						foreach ( $parent_terms as $parent_term ) {
							$this->add_term_crumb( $parent_term );
						}
					}

					$this->add_term_crumb( $deepest_term );
				}
			}
		}
	}

	/**
	 * Add hierarchical ancestor crumbs to the crumbs property for a single post
	 */
	private function add_post_ancestor_crumbs() {
		$ancestors = $this->get_post_ancestors();
		if ( is_array( $ancestors ) && $ancestors !== array() ) {
			foreach ( $ancestors as $ancestor ) {
				$this->add_single_post_crumb( $ancestor );
			}
		}
	}

	/**
	 * Add taxonomy parent crumbs to the crumbs property for a taxonomy
	 */
	private function add_crumbs_for_taxonomy() {
		$term = $GLOBALS['wp_query']->get_queried_object();

		// @todo adjust function name!!
		$this->maybe_add_preferred_term_parent_crumb( $term );

		$this->maybe_add_term_parent_crumbs( $term );

		$this->add_term_crumb( $term );
	}

	/**
	 * Add parent taxonomy crumb based on user defined preference
	 *
	 * @param object $term
	 */
	private function maybe_add_preferred_term_parent_crumb( $term ) {
		if ( isset( $this->options[ 'taxonomy-' . $term->taxonomy . '-ptparent' ] ) && $this->options[ 'taxonomy-' . $term->taxonomy . '-ptparent' ] != '0' ) {
			if ( 'post' == $this->options[ 'taxonomy-' . $term->taxonomy . '-ptparent' ] && $this->show_on_front == 'page' ) {
				if ( $this->page_for_posts ) {
					$this->add_blog_crumb();
				}
			}
			else {
				$this->add_ptarchive_crumb( $this->options[ 'taxonomy-' . $term->taxonomy . '-ptparent' ] );
			}
		}
	}

	/**
	 * Add parent taxonomy crumbs to the crumb property for hierachical taxonomy
	 *
	 * @param object $term
	 */
	private function maybe_add_term_parent_crumbs( $term ) {
		if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent != 0 ) {
			foreach ( $this->get_term_parents( $term ) as $parent_term ) {
				$this->add_term_crumb( $parent_term );
			}
		}
	}

	/**
	 * Add month-year crumb to crumbs property
	 */
	private function add_linked_month_year_crumb() {
		$this->add_predefined_crumb(
			$GLOBALS['wp_locale']->get_month( get_query_var( 'monthnum' ) ) . ' ' . get_query_var( 'year' ),
			get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) )
		);
	}

	/**
	 * Add (non-link) month crumb to crumbs property
	 */
	private function add_month_crumb() {
		$this->add_predefined_crumb(
			$this->options['breadcrumbs-archiveprefix'] . ' ' . esc_html( single_month_title( ' ', false ) ),
			null,
			true
		);
	}

	/**
	 * Add (non-link) year crumb to crumbs property
	 */
	private function add_year_crumb() {
		$this->add_predefined_crumb(
			$this->options['breadcrumbs-archiveprefix'] . ' ' . esc_html( get_query_var( 'year' ) ),
			null,
			true
		);
	}

	/**
	 * Add (non-link) date crumb to crumbs property
	 *
	 * @param string $date
	 */
	private function add_date_crumb( $date = null ) {
		if ( is_null( $date ) ) {
			$date = get_the_date();
		}
		else {
			$date = mysql2date( get_option( 'date_format' ), $date, true );
			$date = apply_filters( 'get_the_date', $date, '' );
		}

		$this->add_predefined_crumb(
			$this->options['breadcrumbs-archiveprefix'] . ' ' . esc_html( $date ),
			null,
			true
		);
	}


	/**
	 * Take the crumbs array and convert each crumb to a single breadcrumb string.
	 *
	 * @link http://support.google.com/webmasters/bin/answer.py?hl=en&answer=185417 Google documentation on RDFA
	 */
	private function prepare_links() {
		if ( ! is_array( $this->crumbs ) || $this->crumbs === array() ) {
			return;
		}

		foreach ( $this->crumbs as $i => $crumb ) {
			$link_info = $crumb; // Keep pre-set url/text combis.

			if ( isset( $crumb['id'] ) ) {
				$link_info = $this->get_link_info_for_id( $crumb['id'] );
			}
			if ( isset( $crumb['term'] ) ) {
				$link_info = $this->get_link_info_for_term( $crumb['term'] );
			}
			if ( isset( $crumb['ptarchive'] ) ) {
				$link_info = $this->get_link_info_for_ptarchive( $crumb['ptarchive'] );
			}

			$this->links[] = $this->crumb_to_link( $link_info, $i );
		}
	}

	/**
	 * Retrieve link url and text based on post id
	 *
	 * @param int $id Post ID.
	 *
	 * @return array Array of link text and url
	 */
	private function get_link_info_for_id( $id ) {
		$link = array();

		$link['url']  = get_permalink( $id );
		$link['text'] = WPSEO_Meta::get_value( 'bctitle', $id );
		if ( $link['text'] === '' ) {
			$link['text'] = strip_tags( get_the_title( $id ) );
		}

		/**
		 * Filter: 'wp_seo_get_bc_title' - Allow developer to filter the Yoast SEO Breadcrumb title.
		 *
		 * @api string $link_text The Breadcrumb title text
		 *
		 * @param int $link_id The post ID
		 */
		$link['text'] = apply_filters( 'wp_seo_get_bc_title', $link['text'], $id );

		return $link;
	}

	/**
	 * Retrieve link url and text based on term object
	 *
	 * @param object $term Term object.
	 *
	 * @return array Array of link text and url
	 */
	private function get_link_info_for_term( $term ) {
		$link = array();

		$bctitle = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'bctitle' );
		if ( ! is_string( $bctitle ) || $bctitle === '' ) {
			$bctitle = $term->name;
		}

		$link['url']  = get_term_link( $term );
		$link['text'] = $bctitle;

		return $link;
	}

	/**
	 * Retrieve link url and text based on post type
	 *
	 * @param string $pt Post type.
	 *
	 * @return array Array of link text and url
	 */
	private function get_link_info_for_ptarchive( $pt ) {
		$link          = array();
		$archive_title = '';

		if ( isset( $this->options[ 'bctitle-ptarchive-' . $pt ] ) && $this->options[ 'bctitle-ptarchive-' . $pt ] !== '' ) {

			$archive_title = $this->options[ 'bctitle-ptarchive-' . $pt ];
		}
		else {
			$post_type_obj = get_post_type_object( $pt );
			if ( is_object( $post_type_obj ) ) {
				if ( isset( $post_type_obj->label ) && $post_type_obj->label !== '' ) {
					$archive_title = $post_type_obj->label;
				}
				elseif ( isset( $post_type_obj->labels->menu_name ) && $post_type_obj->labels->menu_name !== '' ) {
					$archive_title = $post_type_obj->labels->menu_name;
				}
				else {
					$archive_title = $post_type_obj->name;
				}
			}
		}

		$link['url']  = get_post_type_archive_link( $pt );
		$link['text'] = $archive_title;

		return $link;
	}


	/**
	 * Create a breadcrumb element string
	 *
	 * @todo The `$paged` variable only works for archives, not for paged articles, so this does not work
	 * for paged article at this moment.
	 *
	 * @param  array $link Link info array containing the keys:
	 *                     'text'    => (string) link text
	 *                     'url'    => (string) link url
	 *                     (optional) 'allow_html'    => (bool) whether to (not) escape html in the link text
	 *                     This prevents html stripping from the text strings set in the
	 *                     WPSEO -> Internal Links options page.
	 * @param  int   $i    Index for the current breadcrumb.
	 *
	 * @return string
	 */
	private function crumb_to_link( $link, $i ) {
		$link_output = '';

		if ( isset( $link['text'] ) && ( is_string( $link['text'] ) && $link['text'] !== '' ) ) {

			$link['text'] = trim( $link['text'] );
			if ( ! isset( $link['allow_html'] ) || $link['allow_html'] !== true ) {
				$link['text'] = esc_html( $link['text'] );
			}

			$inner_elm = 'span';
			if ( $this->options['breadcrumbs-boldlast'] === true && $i === ( $this->crumb_count - 1 ) ) {
				$inner_elm = 'strong';
			}

			if ( ( isset( $link['url'] ) && ( is_string( $link['url'] ) && $link['url'] !== '' ) ) &&
			     ( $i < ( $this->crumb_count - 1 ) )
			) {
				if ( $i === 0 ) {
					$link_output .= '<' . $this->element . ' typeof="v:Breadcrumb">';
				}
				else {
					$link_output .= '<' . $this->element . ' rel="v:child" typeof="v:Breadcrumb">';
				}
				$link_output .= '<a href="' . esc_url( $link['url'] ) . '" rel="v:url" property="v:title">' . $link['text'] . '</a>';
			}
			else {
				$link_output .= '<' . $inner_elm . ' class="breadcrumb_last">' . $link['text'] . '</' . $inner_elm . '>';
				// This is the last element, now close all previous elements.
				while ( $i > 0 ) {
					$link_output .= '</' . $this->element . '>';
					$i--;
				}
			}
		}

		/**
		 * Filter: 'wpseo_breadcrumb_single_link' - Allow changing of each link being put out by the Yoast SEO breadcrumbs class
		 *
		 * @api string $link_output The output string
		 *
		 * @param array $link The link array.
		 */

		return apply_filters( 'wpseo_breadcrumb_single_link', $link_output, $link );
	}


	/**
	 * Create a complete breadcrumb string from an array of breadcrumb element strings
	 */
	private function links_to_string() {
		if ( is_array( $this->links ) && $this->links !== array() ) {
			// Remove any effectively empty links.
			$links = array_map( 'trim', $this->links );
			$links = array_filter( $links );

			$this->output = implode( $this->separator, $links );
		}
	}

	/**
	 * Wrap a complete breadcrumb string in a Breadcrumb RDFA wrapper
	 */
	private function wrap_breadcrumb() {
		if ( is_string( $this->output ) && $this->output !== '' ) {
			$output = '<' . $this->wrapper . $this->get_output_id() . $this->get_output_class() . ' xmlns:v="http://rdf.data-vocabulary.org/#">' . $this->output . '</' . $this->wrapper . '>';

			/**
			 * Filter: 'wpseo_breadcrumb_output' - Allow changing the HTML output of the Yoast SEO breadcrumbs class
			 *
			 * @api string $unsigned HTML output
			 */
			$output = apply_filters( 'wpseo_breadcrumb_output', $output );

			if ( $this->options['breadcrumbs-prefix'] !== '' ) {
				$output = "\t" . $this->options['breadcrumbs-prefix'] . "\n" . $output;
			}

			$this->output = $output;
		}
	}


	/**
	 * Filter: 'wpseo_breadcrumb_output_id' - Allow changing the HTML ID on the Yoast SEO breadcrumbs wrapper element
	 *
	 * @api string $unsigned ID to add to the wrapper element
	 */
	private function get_output_id() {
		$id = apply_filters( 'wpseo_breadcrumb_output_id', '' );
		if ( is_string( $id ) && '' !== $id ) {
			$id = ' id="' . esc_attr( $id ) . '"';
		}

		return $id;
	}

	/**
	 * Filter: 'wpseo_breadcrumb_output_class' - Allow changing the HTML class on the Yoast SEO breadcrumbs wrapper element
	 *
	 * @api string $unsigned class to add to the wrapper element
	 */
	private function get_output_class() {
		$class = apply_filters( 'wpseo_breadcrumb_output_class', '' );
		if ( is_string( $class ) && '' !== $class ) {
			$class = ' class="' . esc_attr( $class ) . '"';
		}

		return $class;
	}


	/********************** DEPRECATED METHODS **********************/

	/**
	 * Wrapper function for the breadcrumb so it can be output for the supported themes.
	 *
	 * @deprecated 1.5.0
	 */
	public function breadcrumb_output() {
		_deprecated_function( __METHOD__, '1.5.0', 'yoast_breadcrumb' );
		self::breadcrumb( '<div id="wpseobreadcrumb">', '</div>' );
	}

	/**
	 * Take the links array and return a full breadcrumb string.
	 *
	 * @deprecated 1.5.2.3
	 *
	 * @param string $links
	 * @param string $wrapper
	 * @param string $element
	 *
	 * @return void
	 */
	public function create_breadcrumbs_string( $links, $wrapper = 'span', $element = 'span' ) {
		_deprecated_function( __METHOD__, 'WPSEO 1.5.2.3', 'yoast_breadcrumbs' );
	}


} /* End of class */
