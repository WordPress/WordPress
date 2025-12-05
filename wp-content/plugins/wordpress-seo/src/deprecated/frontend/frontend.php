<?php
/**
 * Backwards compatibility class for WPSEO_Frontend.
 *
 * @package Yoast\YoastSEO\Backwards_Compatibility
 */

use Yoast\WP\SEO\Memoizers\Meta_Tags_Context_Memoizer;
use Yoast\WP\SEO\Presenters\Canonical_Presenter;
use Yoast\WP\SEO\Presenters\Meta_Description_Presenter;
use Yoast\WP\SEO\Presenters\Rel_Next_Presenter;
use Yoast\WP\SEO\Presenters\Rel_Prev_Presenter;
use Yoast\WP\SEO\Presenters\Robots_Presenter;
use Yoast\WP\SEO\Surfaces\Helpers_Surface;

/**
 * Class WPSEO_Frontend
 *
 * @codeCoverageIgnore Because of deprecation.
 */
class WPSEO_Frontend {

	/**
	 * Instance of this class.
	 *
	 * @var WPSEO_Frontend
	 */
	public static $instance;

	/**
	 * The memoizer for the meta tags context.
	 *
	 * @var Meta_Tags_Context_Memoizer
	 */
	private $context_memoizer;

	/**
	 * The WPSEO Replace Vars object.
	 *
	 * @var WPSEO_Replace_Vars
	 */
	private $replace_vars;

	/**
	 * The helpers surface.
	 *
	 * @var Helpers_Surface
	 */
	private $helpers;

	/**
	 * WPSEO_Frontend constructor.
	 */
	public function __construct() {
		$this->context_memoizer = YoastSEO()->classes->get( Meta_Tags_Context_Memoizer::class );
		$this->replace_vars     = YoastSEO()->classes->get( WPSEO_Replace_Vars::class );
		$this->helpers          = YoastSEO()->classes->get( Helpers_Surface::class );
	}

	/**
	 * Catches call to methods that don't exist and might deprecated.
	 *
	 * @param string $method    The called method.
	 * @param array  $arguments The given arguments.
	 *
	 * @return mixed
	 */
	public function __call( $method, $arguments ) {
		_deprecated_function( $method, 'Yoast SEO 14.0' );

		$title_methods = [
			'title',
			'fix_woo_title',
			'get_content_title',
			'get_seo_title',
			'get_taxonomy_title',
			'get_author_title',
			'get_title_from_options',
			'get_default_title',
			'force_wp_title',
		];
		if ( in_array( $method, $title_methods, true ) ) {
			return $this->get_title();
		}

		return null;
	}

	/**
	 * Retrieves an instance of the class.
	 *
	 * @return static The instance.
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Outputs the canonical value.
	 *
	 * @param bool $echo        Whether or not to output the canonical element.
	 * @param bool $un_paged    Whether or not to return the canonical with or without pagination added to the URL.
	 * @param bool $no_override Whether or not to return a manually overridden canonical.
	 *
	 * @return string|void
	 */
	public function canonical( $echo = true, $un_paged = false, $no_override = false ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

		$presentation = $this->get_current_page_presentation();
		$presenter    = new Canonical_Presenter();

		/** This filter is documented in src/integrations/front-end-integration.php */
		$presenter->presentation = $presentation;
		$presenter->helpers      = $this->helpers;
		$presenter->replace_vars = $this->replace_vars;

		if ( ! $echo ) {
			return $presenter->get();
		}

		echo $presenter->present();
	}

	/**
	 * Retrieves the meta robots value.
	 *
	 * @return string
	 */
	public function get_robots() {
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

		$presentation = $this->get_current_page_presentation();
		return $presentation->robots;
	}

	/**
	 * Outputs the meta robots value.
	 *
	 * @return void
	 */
	public function robots() {
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

		$presentation            = $this->get_current_page_presentation();
		$presenter               = new Robots_Presenter();
		$presenter->presentation = $presentation;
		$presenter->helpers      = $this->helpers;
		$presenter->replace_vars = $this->replace_vars;
		echo $presenter->present();
	}

	/**
	 * Determine $robots values for a single post.
	 *
	 * @param array $robots  Robots data array.
	 * @param int   $post_id The post ID for which to determine the $robots values, defaults to current post.
	 *
	 * @return array
	 */
	public function robots_for_single_post( $robots, $post_id = 0 ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

		$presentation = $this->get_current_page_presentation();

		return $presentation->robots;
	}

	/**
	 * Used for static home and posts pages as well as singular titles.
	 *
	 * @param object|null $object If filled, object to get the title for.
	 *
	 * @return string The content title.
	 */
	private function get_title( $object = null ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

		$presentation = $this->get_current_page_presentation();
		$title        = $presentation->title;

		return $this->replace_vars->replace( $title, $presentation->source );
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
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

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
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

		if ( $seplocation === 'right' ) {
			return $title . $sep . $title_part;
		}

		return $title_part . $sep . $title;
	}

	/**
	 * Adds 'prev' and 'next' links to archives.
	 *
	 * @link http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
	 *
	 * @return void
	 */
	public function adjacent_rel_links() {
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

		$presentation = $this->get_current_page_presentation();

		$rel_prev_presenter               = new Rel_Prev_Presenter();
		$rel_prev_presenter->presentation = $presentation;
		$rel_prev_presenter->helpers      = $this->helpers;
		$rel_prev_presenter->replace_vars = $this->replace_vars;
		echo $rel_prev_presenter->present();

		$rel_next_presenter               = new Rel_Next_Presenter();
		$rel_next_presenter->presentation = $presentation;
		$rel_next_presenter->helpers      = $this->helpers;
		$rel_next_presenter->replace_vars = $this->replace_vars;
		echo $rel_next_presenter->present();
	}

	/**
	 * Outputs the meta description element or returns the description text.
	 *
	 * @param bool $echo Echo or return output flag.
	 *
	 * @return string
	 */
	public function metadesc( $echo = true ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 14.0' );

		$presentation            = $this->get_current_page_presentation();
		$presenter               = new Meta_Description_Presenter();
		$presenter->presentation = $presentation;
		$presenter->helpers      = $this->helpers;
		$presenter->replace_vars = $this->replace_vars;

		if ( ! $echo ) {
			return $presenter->get();
		}
		$presenter->present();
	}

	/**
	 * Returns the current page presentation.
	 *
	 * @return Indexable_Presentation The current page presentation.
	 */
	private function get_current_page_presentation() {
		$context = $this->context_memoizer->for_current_page();

		/** This filter is documented in src/integrations/front-end-integration.php */
		return apply_filters( 'wpseo_frontend_presentation', $context->presentation, $context );
	}
}
