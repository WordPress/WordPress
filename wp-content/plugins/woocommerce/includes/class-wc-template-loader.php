<?php
/**
 * Template Loader
 *
 * @package WooCommerce\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template loader class.
 */
class WC_Template_Loader {

	/**
	 * Store the shop page ID.
	 *
	 * @var integer
	 */
	private static $shop_page_id = 0;

	/**
	 * Store whether we're processing a product inside the_content filter.
	 *
	 * @var boolean
	 */
	private static $in_content_filter = false;

	/**
	 * Is WooCommerce support defined?
	 *
	 * @var boolean
	 */
	private static $theme_support = false;

	/**
	 * Hook in methods.
	 */
	public static function init() {
		self::$theme_support = wc_current_theme_supports_woocommerce_or_fse();
		self::$shop_page_id  = wc_get_page_id( 'shop' );

		// Supported themes.
		if ( self::$theme_support ) {
			add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
			add_filter( 'comments_template', array( __CLASS__, 'comments_template_loader' ) );

			// Loads gallery scripts on Product page for FSE themes.
			if ( wc_current_theme_is_fse_theme() ) {
				self::add_support_for_product_page_gallery();
			}
		} else {
			// Unsupported themes.
			add_action( 'template_redirect', array( __CLASS__, 'unsupported_theme_init' ) );
		}
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the theme's.
	 *
	 * Templates are in the 'templates' folder. WooCommerce looks for theme
	 * overrides in /theme/woocommerce/ by default.
	 *
	 * For beginners, it also looks for a woocommerce.php template first. If the user adds
	 * this to the theme (containing a woocommerce() inside) this will be used for all
	 * WooCommerce templates.
	 *
	 * @param string $template Template to load.
	 * @return string
	 */
	public static function template_loader( $template ) {
		if ( is_embed() ) {
			return $template;
		}

		$default_file = self::get_template_loader_default_file();

		if ( $default_file ) {
			/**
			 * Filter hook to choose which files to find before WooCommerce does it's own logic.
			 *
			 * @since 3.0.0
			 * @var array
			 */
			$search_files = self::get_template_loader_files( $default_file );
			$template     = locate_template( $search_files );

			if ( ! $template || WC_TEMPLATE_DEBUG_MODE ) {
				if ( false !== strpos( $default_file, 'product_cat' ) || false !== strpos( $default_file, 'product_tag' ) ) {
					$cs_template = str_replace( '_', '-', $default_file );
					$template    = WC()->plugin_path() . '/templates/' . $cs_template;
				} else {
					$template = WC()->plugin_path() . '/templates/' . $default_file;
				}
			}
		}

		return $template;
	}

	/**
	 * Checks whether a block template for a given taxonomy exists.
	 *
	 * **Note:** This checks both the `templates` and `block-templates` directories
	 * as both conventions should be supported.
	 *
	 * @param object $taxonomy Object taxonomy to check.
	 * @return boolean
	 */
	private static function taxonomy_has_block_template( $taxonomy ) : bool {
		if ( taxonomy_is_product_attribute( $taxonomy->taxonomy ) ) {
			$template_name = 'taxonomy-product_attribute';
		} else {
			$template_name = 'taxonomy-' . $taxonomy->taxonomy;
		}

		return self::has_block_template( $template_name );
	}

	/**
	 * Checks whether a block template with that name exists.
	 *
	 * **Note: ** This checks both the `templates` and `block-templates` directories
	 * as both conventions should be supported.
	 *
	 * @since  5.5.0
	 * @param string $template_name Template to check.
	 * @return boolean
	 */
	private static function has_block_template( $template_name ) {
		if ( ! $template_name ) {
			return false;
		}

		$has_template            = false;
		$template_filename       = $template_name . '.html';
		// Since Gutenberg 12.1.0, the conventions for block templates directories have changed,
		// we should check both these possible directories for backwards-compatibility.
		$possible_templates_dirs = array( 'templates', 'block-templates' );

		// Combine the possible root directory names with either the template directory
		// or the stylesheet directory for child themes, getting all possible block templates
		// locations combinations.
		$filepath        = DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template_filename;
		$legacy_filepath = DIRECTORY_SEPARATOR . 'block-templates' . DIRECTORY_SEPARATOR . $template_filename;
		$possible_paths  = array(
			get_stylesheet_directory() . $filepath,
			get_stylesheet_directory() . $legacy_filepath,
			get_template_directory() . $filepath,
			get_template_directory() . $legacy_filepath,
		);

		// Check the first matching one.
		foreach ( $possible_paths as $path ) {
			if ( is_readable( $path ) ) {
				$has_template = true;
				break;
			}
		}

		/**
		 * Filters the value of the result of the block template check.
		 *
		 * @since x.x.x
		 *
		 * @param boolean $has_template value to be filtered.
		 * @param string $template_name The name of the template.
		 */
		return (bool) apply_filters( 'woocommerce_has_block_template', $has_template, $template_name );
	}

	/**
	 * Get the default filename for a template except if a block template with
	 * the same name exists.
	 *
	 * @since  3.0.0
	 * @since  5.5.0 If a block template with the same name exists, return an
	 * empty string.
	 * @since  6.3.0 It checks custom product taxonomies
	 * @return string
	 */
	private static function get_template_loader_default_file() {
		if (
			is_singular( 'product' ) &&
			! self::has_block_template( 'single-product' )
		) {
			$default_file = 'single-product.php';
		} elseif ( is_product_taxonomy() ) {
			$object = get_queried_object();

			if ( self::taxonomy_has_block_template( $object ) ) {
				$default_file = '';
			} else {
				if ( taxonomy_is_product_attribute( $object->taxonomy ) ) {
					$default_file = 'taxonomy-product-attribute.php';
				} elseif ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
					$default_file = 'taxonomy-' . $object->taxonomy . '.php';
				} elseif ( ! self::has_block_template( 'archive-product' ) ) {
					$default_file = 'archive-product.php';
				} else {
					$default_file = '';
				}
			}
		} elseif (
			( is_post_type_archive( 'product' ) || is_page( wc_get_page_id( 'shop' ) ) ) &&
			! self::has_block_template( 'archive-product' )
		) {
			$default_file = self::$theme_support ? 'archive-product.php' : '';
		} else {
			$default_file = '';
		}
		return $default_file;
	}

	/**
	 * Get an array of filenames to search for a given template.
	 *
	 * @since  3.0.0
	 * @param  string $default_file The default file name.
	 * @return string[]
	 */
	private static function get_template_loader_files( $default_file ) {
		$templates   = apply_filters( 'woocommerce_template_loader_files', array(), $default_file );
		$templates[] = 'woocommerce.php';

		if ( is_page_template() ) {
			$page_template = get_page_template_slug();

			if ( $page_template ) {
				$validated_file = validate_file( $page_template );
				if ( 0 === $validated_file ) {
					$templates[] = $page_template;
				} else {
					error_log( "WooCommerce: Unable to validate template path: \"$page_template\". Error Code: $validated_file." ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				}
			}
		}

		if ( is_singular( 'product' ) ) {
			$object       = get_queried_object();
			$name_decoded = urldecode( $object->post_name );
			if ( $name_decoded !== $object->post_name ) {
				$templates[] = "single-product-{$name_decoded}.php";
			}
			$templates[] = "single-product-{$object->post_name}.php";
		}

		if ( is_product_taxonomy() ) {
			$object = get_queried_object();

			if ( taxonomy_is_product_attribute( $object->taxonomy ) ) {
				$templates[] = 'taxonomy-product_attribute.php';
				$templates[] = WC()->template_path() . 'taxonomy-product_attribute.php';
				$templates[] = $default_file;
			} else {
				$templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
				$templates[] = WC()->template_path() . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
				$templates[] = 'taxonomy-' . $object->taxonomy . '.php';
				$templates[] = WC()->template_path() . 'taxonomy-' . $object->taxonomy . '.php';

				if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
					$cs_taxonomy = str_replace( '_', '-', $object->taxonomy );
					$cs_default  = str_replace( '_', '-', $default_file );
					$templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php';
					$templates[] = WC()->template_path() . 'taxonomy-' . $cs_taxonomy . '-' . $object->slug . '.php';
					$templates[] = 'taxonomy-' . $object->taxonomy . '.php';
					$templates[] = WC()->template_path() . 'taxonomy-' . $cs_taxonomy . '.php';
					$templates[] = $cs_default;
				}
			}
		}

		$templates[] = $default_file;
		if ( isset( $cs_default ) ) {
			$templates[] = WC()->template_path() . $cs_default;
		}
		$templates[] = WC()->template_path() . $default_file;

		return array_unique( $templates );
	}

	/**
	 * Load comments template.
	 *
	 * @param string $template template to load.
	 * @return string
	 */
	public static function comments_template_loader( $template ) {
		if ( get_post_type() !== 'product' ) {
			return $template;
		}

		$check_dirs = array(
			trailingslashit( get_stylesheet_directory() ) . WC()->template_path(),
			trailingslashit( get_template_directory() ) . WC()->template_path(),
			trailingslashit( get_stylesheet_directory() ),
			trailingslashit( get_template_directory() ),
			trailingslashit( WC()->plugin_path() ) . 'templates/',
		);

		if ( WC_TEMPLATE_DEBUG_MODE ) {
			$check_dirs = array( array_pop( $check_dirs ) );
		}

		foreach ( $check_dirs as $dir ) {
			if ( file_exists( trailingslashit( $dir ) . 'single-product-reviews.php' ) ) {
				return trailingslashit( $dir ) . 'single-product-reviews.php';
			}
		}
	}

	/**
	 * Unsupported theme compatibility methods.
	 */

	/**
	 * Hook in methods to enhance the unsupported theme experience on pages.
	 *
	 * @since 3.3.0
	 */
	public static function unsupported_theme_init() {
		if ( 0 < self::$shop_page_id ) {
			if ( is_product_taxonomy() ) {
				self::unsupported_theme_tax_archive_init();
			} elseif ( is_product() ) {
				self::unsupported_theme_product_page_init();
			} else {
				self::unsupported_theme_shop_page_init();
			}
		}
	}

	/**
	 * Hook in methods to enhance the unsupported theme experience on the Shop page.
	 *
	 * @since 3.3.0
	 */
	private static function unsupported_theme_shop_page_init() {
		add_filter( 'the_content', array( __CLASS__, 'unsupported_theme_shop_content_filter' ), 10 );
		add_filter( 'the_title', array( __CLASS__, 'unsupported_theme_title_filter' ), 10, 2 );
		add_filter( 'comments_number', array( __CLASS__, 'unsupported_theme_comments_number_filter' ) );
	}

	/**
	 * Hook in methods to enhance the unsupported theme experience on Product pages.
	 *
	 * @since 3.3.0
	 */
	private static function unsupported_theme_product_page_init() {
		add_filter( 'the_content', array( __CLASS__, 'unsupported_theme_product_content_filter' ), 10 );
		add_filter( 'post_thumbnail_html', array( __CLASS__, 'unsupported_theme_single_featured_image_filter' ) );
		add_filter( 'woocommerce_product_tabs', array( __CLASS__, 'unsupported_theme_remove_review_tab' ) );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		self::add_support_for_product_page_gallery();
	}

	/**
	 * Add theme support for Product page gallery.
	 *
	 * @since x.x.x
	 */
	private static function add_support_for_product_page_gallery() {
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}

	/**
	 * Enhance the unsupported theme experience on Product Category and Attribute pages by rendering
	 * those pages using the single template and shortcode-based content. To do this we make a dummy
	 * post and set a shortcode as the post content. This approach is adapted from bbPress.
	 *
	 * @since 3.3.0
	 */
	private static function unsupported_theme_tax_archive_init() {
		global $wp_query, $post;

		$queried_object = get_queried_object();
		$args           = self::get_current_shop_view_args();
		$shortcode_args = array(
			'page'     => $args->page,
			'columns'  => $args->columns,
			'rows'     => $args->rows,
			'orderby'  => '',
			'order'    => '',
			'paginate' => true,
			'cache'    => false,
		);

		if ( is_product_category() ) {
			$shortcode_args['category'] = sanitize_title( $queried_object->slug );
		} elseif ( taxonomy_is_product_attribute( $queried_object->taxonomy ) ) {
			$shortcode_args['attribute'] = sanitize_title( $queried_object->taxonomy );
			$shortcode_args['terms']     = sanitize_title( $queried_object->slug );
		} elseif ( is_product_tag() ) {
			$shortcode_args['tag'] = sanitize_title( $queried_object->slug );
		} else {
			// Default theme archive for all other taxonomies.
			return;
		}

		// Description handling.
		if ( ! empty( $queried_object->description ) && ( empty( $_GET['product-page'] ) || 1 === absint( $_GET['product-page'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$prefix = '<div class="term-description">' . wc_format_content( wp_kses_post( $queried_object->description ) ) . '</div>';
		} else {
			$prefix = '';
		}

		add_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'unsupported_archive_layered_nav_compatibility' ) );
		$shortcode = new WC_Shortcode_Products( $shortcode_args );
		remove_filter( 'woocommerce_shortcode_products_query', array( __CLASS__, 'unsupported_archive_layered_nav_compatibility' ) );
		$shop_page = get_post( self::$shop_page_id );

		$dummy_post_properties = array(
			'ID'                    => 0,
			'post_status'           => 'publish',
			'post_author'           => $shop_page->post_author,
			'post_parent'           => 0,
			'post_type'             => 'page',
			'post_date'             => $shop_page->post_date,
			'post_date_gmt'         => $shop_page->post_date_gmt,
			'post_modified'         => $shop_page->post_modified,
			'post_modified_gmt'     => $shop_page->post_modified_gmt,
			'post_content'          => $prefix . $shortcode->get_content(),
			'post_title'            => wc_clean( $queried_object->name ),
			'post_excerpt'          => '',
			'post_content_filtered' => '',
			'post_mime_type'        => '',
			'post_password'         => '',
			'post_name'             => $queried_object->slug,
			'guid'                  => '',
			'menu_order'            => 0,
			'pinged'                => '',
			'to_ping'               => '',
			'ping_status'           => '',
			'comment_status'        => 'closed',
			'comment_count'         => 0,
			'filter'                => 'raw',
		);

		// Set the $post global.
		$post = new WP_Post( (object) $dummy_post_properties ); // @codingStandardsIgnoreLine.

		// Copy the new post global into the main $wp_query.
		$wp_query->post  = $post;
		$wp_query->posts = array( $post );

		// Prevent comments form from appearing.
		$wp_query->post_count    = 1;
		$wp_query->is_404        = false;
		$wp_query->is_page       = true;
		$wp_query->is_single     = true;
		$wp_query->is_archive    = false;
		$wp_query->is_tax        = true;
		$wp_query->max_num_pages = 0;

		// Prepare everything for rendering.
		setup_postdata( $post );
		remove_all_filters( 'the_content' );
		remove_all_filters( 'the_excerpt' );
		add_filter( 'template_include', array( __CLASS__, 'force_single_template_filter' ) );
	}

	/**
	 * Add layered nav args to WP_Query args generated by the 'products' shortcode.
	 *
	 * @since 3.3.4
	 * @param array $query WP_Query args.
	 * @return array
	 */
	public static function unsupported_archive_layered_nav_compatibility( $query ) {
		foreach ( WC()->query->get_layered_nav_chosen_attributes() as $taxonomy => $data ) {
			$query['tax_query'][] = array(
				'taxonomy'         => $taxonomy,
				'field'            => 'slug',
				'terms'            => $data['terms'],
				'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
				'include_children' => false,
			);
		}
		return $query;
	}

	/**
	 * Force the loading of one of the single templates instead of whatever template was about to be loaded.
	 *
	 * @since 3.3.0
	 * @param string $template Path to template.
	 * @return string
	 */
	public static function force_single_template_filter( $template ) {
		$possible_templates = array(
			'page',
			'single',
			'singular',
			'index',
		);

		foreach ( $possible_templates as $possible_template ) {
			$path = get_query_template( $possible_template );
			if ( $path ) {
				return $path;
			}
		}

		return $template;
	}

	/**
	 * Get information about the current shop page view.
	 *
	 * @since 3.3.0
	 * @return array
	 */
	private static function get_current_shop_view_args() {
		return (object) array(
			'page'    => absint( max( 1, absint( get_query_var( 'paged' ) ) ) ),
			'columns' => wc_get_default_products_per_row(),
			'rows'    => wc_get_default_product_rows_per_page(),
		);
	}

	/**
	 * Filter the title and insert WooCommerce content on the shop page.
	 *
	 * For non-WC themes, this will setup the main shop page to be shortcode based to improve default appearance.
	 *
	 * @since 3.3.0
	 * @param string $title Existing title.
	 * @param int    $id ID of the post being filtered.
	 * @return string
	 */
	public static function unsupported_theme_title_filter( $title, $id ) {
		if ( self::$theme_support || ! $id !== self::$shop_page_id ) {
			return $title;
		}

		if ( is_page( self::$shop_page_id ) || ( is_home() && 'page' === get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) === self::$shop_page_id ) ) {
			$args         = self::get_current_shop_view_args();
			$title_suffix = array();

			if ( $args->page > 1 ) {
				/* translators: %d: Page number. */
				$title_suffix[] = sprintf( esc_html__( 'Page %d', 'woocommerce' ), $args->page );
			}

			if ( $title_suffix ) {
				$title = $title . ' &ndash; ' . implode( ', ', $title_suffix );
			}
		}
		return $title;
	}

	/**
	 * Filter the content and insert WooCommerce content on the shop page.
	 *
	 * For non-WC themes, this will setup the main shop page to be shortcode based to improve default appearance.
	 *
	 * @since 3.3.0
	 * @param string $content Existing post content.
	 * @return string
	 */
	public static function unsupported_theme_shop_content_filter( $content ) {
		global $wp_query;

		if ( self::$theme_support || ! is_main_query() || ! in_the_loop() ) {
			return $content;
		}

		self::$in_content_filter = true;

		// Remove the filter we're in to avoid nested calls.
		remove_filter( 'the_content', array( __CLASS__, 'unsupported_theme_shop_content_filter' ) );

		// Unsupported theme shop page.
		if ( is_page( self::$shop_page_id ) ) {
			$args      = self::get_current_shop_view_args();
			$shortcode = new WC_Shortcode_Products(
				array_merge(
					WC()->query->get_catalog_ordering_args(),
					array(
						'page'     => $args->page,
						'columns'  => $args->columns,
						'rows'     => $args->rows,
						'orderby'  => '',
						'order'    => '',
						'paginate' => true,
						'cache'    => false,
					)
				),
				'products'
			);

			// Allow queries to run e.g. layered nav.
			add_action( 'pre_get_posts', array( WC()->query, 'product_query' ) );

			$content = $content . $shortcode->get_content();

			// Remove actions and self to avoid nested calls.
			remove_action( 'pre_get_posts', array( WC()->query, 'product_query' ) );
			WC()->query->remove_ordering_args();
		}

		self::$in_content_filter = false;

		return $content;
	}

	/**
	 * Filter the content and insert WooCommerce content on the shop page.
	 *
	 * For non-WC themes, this will setup the main shop page to be shortcode based to improve default appearance.
	 *
	 * @since 3.3.0
	 * @param string $content Existing post content.
	 * @return string
	 */
	public static function unsupported_theme_product_content_filter( $content ) {
		global $wp_query;

		if ( self::$theme_support || ! is_main_query() || ! in_the_loop() ) {
			return $content;
		}

		self::$in_content_filter = true;

		// Remove the filter we're in to avoid nested calls.
		remove_filter( 'the_content', array( __CLASS__, 'unsupported_theme_product_content_filter' ) );

		if ( is_product() ) {
			$content = do_shortcode( '[product_page id="' . get_the_ID() . '" show_title=0 status="any"]' );
		}

		self::$in_content_filter = false;

		return $content;
	}

	/**
	 * Suppress the comments number on the Shop page for unsupported themes since there is no commenting on the Shop page.
	 *
	 * @since 3.4.5
	 * @param string $comments_number The comments number text.
	 * @return string
	 */
	public static function unsupported_theme_comments_number_filter( $comments_number ) {
		if ( is_page( self::$shop_page_id ) ) {
			return '';
		}

		return $comments_number;
	}

	/**
	 * Are we filtering content for unsupported themes?
	 *
	 * @since 3.3.2
	 * @return bool
	 */
	public static function in_content_filter() {
		return (bool) self::$in_content_filter;
	}

	/**
	 * Prevent the main featured image on product pages because there will be another featured image
	 * in the gallery.
	 *
	 * @since 3.3.0
	 * @param string $html Img element HTML.
	 * @return string
	 */
	public static function unsupported_theme_single_featured_image_filter( $html ) {
		if ( self::in_content_filter() || ! is_product() || ! is_main_query() ) {
			return $html;
		}

		return '';
	}

	/**
	 * Remove the Review tab and just use the regular comment form.
	 *
	 * @param array $tabs Tab info.
	 * @return array
	 */
	public static function unsupported_theme_remove_review_tab( $tabs ) {
		unset( $tabs['reviews'] );
		return $tabs;
	}
}

add_action( 'init', array( 'WC_Template_Loader', 'init' ) );
