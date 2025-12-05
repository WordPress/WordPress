<?php
/**
 * Backwards compatibility class for breadcrumbs.
 *
 * @package Yoast\YoastSEO\Backwards_Compatibility
 */

use Yoast\WP\SEO\Memoizers\Meta_Tags_Context_Memoizer;
use Yoast\WP\SEO\Presenters\Breadcrumbs_Presenter;
use Yoast\WP\SEO\Surfaces\Helpers_Surface;

/**
 * Class WPSEO_Breadcrumbs
 *
 * @codeCoverageIgnore Because of deprecation.
 */
class WPSEO_Breadcrumbs {

	/**
	 * Instance of this class.
	 *
	 * @var WPSEO_Breadcrumbs
	 */
	public static $instance;

	/**
	 * Last used 'before' string.
	 *
	 * @var string
	 */
	public static $before = '';

	/**
	 * Last used 'after' string.
	 *
	 * @var string
	 */
	public static $after = '';

	/**
	 * The memoizer for the meta tags context.
	 *
	 * @var Meta_Tags_Context_Memoizer
	 */
	protected $context_memoizer;

	/**
	 * The helpers surface.
	 *
	 * @var Helpers_Surface
	 */
	protected $helpers;

	/**
	 * The replace vars helper
	 *
	 * @var WPSEO_Replace_Vars
	 */
	protected $replace_vars;

	/**
	 * WPSEO_Breadcrumbs constructor.
	 */
	public function __construct() {
		$this->context_memoizer = YoastSEO()->classes->get( Meta_Tags_Context_Memoizer::class );
		$this->helpers          = YoastSEO()->classes->get( Helpers_Surface::class );
		$this->replace_vars     = YoastSEO()->classes->get( WPSEO_Replace_Vars::class );
	}

	/**
	 * Get breadcrumb string using the singleton instance of this class.
	 *
	 * @param string $before  Optional string to prepend.
	 * @param string $after   Optional string to append.
	 * @param bool   $display Echo or return flag.
	 *
	 * @return string Returns the breadcrumbs as a string.
	 */
	public static function breadcrumb( $before = '', $after = '', $display = true ) {
		// Remember the last used before/after for use in case the object goes __toString().
		self::$before = $before;
		self::$after  = $after;
		$output       = $before . self::get_instance()->render() . $after;

		if ( $display === true ) {
			echo $output;

			return '';
		}

		return $output;
	}

	/**
	 * Magic method to use in case the class would be send to string.
	 *
	 * @return string The rendered breadcrumbs.
	 */
	public function __toString() {
		return self::$before . $this->render() . self::$after;
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
	 * Returns the collected links for the breadcrumbs.
	 *
	 * @return array The collected links.
	 */
	public function get_links() {
		$context = $this->context_memoizer->for_current_page();

		return $context->presentation->breadcrumbs;
	}

	/**
	 * Renders the breadcrumbs.
	 *
	 * @return string The rendered breadcrumbs.
	 */
	private function render() {
		$presenter = new Breadcrumbs_Presenter();
		$context   = $this->context_memoizer->for_current_page();

		/** This filter is documented in src/integrations/front-end-integration.php */
		$presentation            = apply_filters( 'wpseo_frontend_presentation', $context->presentation, $context );
		$presenter->presentation = $presentation;
		$presenter->replace_vars = $this->replace_vars;
		$presenter->helpers      = $this->helpers;

		return $presenter->present();
	}
}
