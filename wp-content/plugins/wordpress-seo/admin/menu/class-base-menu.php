<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Menu
 */

use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Admin menu base class.
 */
abstract class WPSEO_Base_Menu implements WPSEO_WordPress_Integration {

	/**
	 * A menu.
	 *
	 * @var WPSEO_Menu
	 */
	protected $menu;

	/**
	 * Constructs the Admin Menu.
	 *
	 * @param WPSEO_Menu $menu Menu to use.
	 */
	public function __construct( WPSEO_Menu $menu ) {
		$this->menu = $menu;
	}

	/**
	 * Returns the list of registered submenu pages.
	 *
	 * @return array List of registered submenu pages.
	 */
	abstract public function get_submenu_pages();

	/**
	 * Creates a submenu formatted array.
	 *
	 * @param string          $page_title Page title to use.
	 * @param string          $page_slug  Page slug to use.
	 * @param callable|null   $callback   Optional. Callback which handles the page request.
	 * @param callable[]|null $hook       Optional. Hook to trigger when the page is registered.
	 *
	 * @return array Formatted submenu.
	 */
	protected function get_submenu_page( $page_title, $page_slug, $callback = null, $hook = null ) {
		if ( $callback === null ) {
			$callback = $this->get_admin_page_callback();
		}

		return [
			$this->get_page_identifier(),
			'',
			$page_title,
			$this->get_manage_capability(),
			$page_slug,
			$callback,
			$hook,
		];
	}

	/**
	 * Registers submenu pages as menu pages.
	 *
	 * This method should only be used if the user does not have the required capabilities
	 * to access the parent menu page.
	 *
	 * @param array $submenu_pages List of submenu pages to register.
	 *
	 * @return void
	 */
	protected function register_menu_pages( $submenu_pages ) {
		if ( ! is_array( $submenu_pages ) || empty( $submenu_pages ) ) {
			return;
		}

		// Loop through submenu pages and add them.
		array_walk( $submenu_pages, [ $this, 'register_menu_page' ] );
	}

	/**
	 * Registers submenu pages.
	 *
	 * @param array $submenu_pages List of submenu pages to register.
	 *
	 * @return void
	 */
	protected function register_submenu_pages( $submenu_pages ) {
		if ( ! is_array( $submenu_pages ) || empty( $submenu_pages ) ) {
			return;
		}

		// Loop through submenu pages and add them.
		array_walk( $submenu_pages, [ $this, 'register_submenu_page' ] );
	}

	/**
	 * Registers a submenu page as a menu page.
	 *
	 * This method should only be used if the user does not have the required capabilities
	 * to access the parent menu page.
	 *
	 * @param array $submenu_page {
	 *     Submenu page definition.
	 *
	 *     @type string   $0 Parent menu page slug.
	 *     @type string   $1 Page title, currently unused.
	 *     @type string   $2 Title to display in the menu.
	 *     @type string   $3 Required capability to access the page.
	 *     @type string   $4 Page slug.
	 *     @type callable $5 Callback to run when the page is rendered.
	 *     @type array    $6 Optional. List of callbacks to run when the page is loaded.
	 * }
	 *
	 * @return void
	 */
	protected function register_menu_page( $submenu_page ) {

		// If the submenu page requires the general manage capability, it must be added as an actual submenu page.
		if ( $submenu_page[3] === $this->get_manage_capability() ) {
			return;
		}

		$page_title = 'Yoast SEO: ' . $submenu_page[2];

		// Register submenu page as menu page.
		$hook_suffix = add_menu_page(
			$page_title,
			$submenu_page[2],
			$submenu_page[3],
			$submenu_page[4],
			$submenu_page[5],
			$this->get_icon_svg(),
			99
		);

		// If necessary, add hooks for the submenu page.
		if ( isset( $submenu_page[6] ) && ( is_array( $submenu_page[6] ) ) ) {
			$this->add_page_hooks( $hook_suffix, $submenu_page[6] );
		}
	}

	/**
	 * Registers a submenu page.
	 *
	 * This method will override the capability of the page to automatically use the
	 * general manage capability. Use the `register_menu_page()` method if the submenu
	 * page should actually use a different capability.
	 *
	 * @param array $submenu_page {
	 *     Submenu page definition.
	 *
	 *     @type string   $0 Parent menu page slug.
	 *     @type string   $1 Page title, currently unused.
	 *     @type string   $2 Title to display in the menu.
	 *     @type string   $3 Required capability to access the page.
	 *     @type string   $4 Page slug.
	 *     @type callable $5 Callback to run when the page is rendered.
	 *     @type array    $6 Optional. List of callbacks to run when the page is loaded.
	 * }
	 *
	 * @return void
	 */
	protected function register_submenu_page( $submenu_page ) {
		$page_title = $submenu_page[2];

		/*
		 * Handle the Google Search Console special case by passing a fake parent
		 * page slug. This way, the sub-page is stil registered and can be accessed
		 * directly. Its menu item won't be displayed.
		 */
		if ( $submenu_page[4] === 'wpseo_search_console' ) {
			// Set the parent page slug to a non-existing one.
			$submenu_page[0] = 'wpseo_fake_menu_parent_page_slug';
		}

		$page_title .= ' - Yoast SEO';

		// Register submenu page.
		$hook_suffix = add_submenu_page(
			$submenu_page[0],
			$page_title,
			$submenu_page[2],
			$submenu_page[3],
			$submenu_page[4],
			$submenu_page[5]
		);

		// If necessary, add hooks for the submenu page.
		if ( isset( $submenu_page[6] ) && ( is_array( $submenu_page[6] ) ) ) {
			$this->add_page_hooks( $hook_suffix, $submenu_page[6] );
		}
	}

	/**
	 * Adds hook callbacks for a given admin page hook suffix.
	 *
	 * @param string $hook_suffix Admin page hook suffix, as returned by `add_menu_page()`
	 *                            or `add_submenu_page()`.
	 * @param array  $callbacks   Callbacks to add.
	 *
	 * @return void
	 */
	protected function add_page_hooks( $hook_suffix, array $callbacks ) {
		foreach ( $callbacks as $callback ) {
			add_action( 'load-' . $hook_suffix, $callback );
		}
	}

	/**
	 * Gets the main admin page identifier.
	 *
	 * @return string Admin page identifier.
	 */
	protected function get_page_identifier() {
		return $this->menu->get_page_identifier();
	}

	/**
	 * Checks whether the current user has capabilities to manage all options.
	 *
	 * @return bool True if capabilities are sufficient, false otherwise.
	 */
	protected function check_manage_capability() {
		return WPSEO_Capability_Utils::current_user_can( $this->get_manage_capability() );
	}

	/**
	 * Returns the capability that is required to manage all options.
	 *
	 * @return string Capability to check against.
	 */
	abstract protected function get_manage_capability();

	/**
	 * Returns the page handler callback.
	 *
	 * @return array Callback page handler.
	 */
	protected function get_admin_page_callback() {
		return [ $this->menu, 'load_page' ];
	}

	/**
	 * Returns the page title to use for the licenses page.
	 *
	 * @deprecated 25.5
	 * @codeCoverageIgnore
	 *
	 * @return string The title for the license page.
	 */
	protected function get_license_page_title() {
		static $title = null;

		_deprecated_function( __METHOD__, 'Yoast SEO 25.5' );

		if ( $title === null ) {
			$title = __( 'Upgrades', 'wordpress-seo' );
		}

		if ( YoastSEO()->classes->get( Promotion_Manager::class )->is( 'black-friday-promotion' ) && ! YoastSEO()->helpers->product->is_premium() ) {
			$title = __( 'Upgrades', 'wordpress-seo' ) . '<span class="yoast-menu-bf-sale-badge">' . __( '30% OFF', 'wordpress-seo' ) . '</span>';
		}

		return $title;
	}

	/**
	 * Returns a base64 URL for the svg for use in the menu.
	 *
	 * @param bool $base64 Whether or not to return base64'd output.
	 *
	 * @return string SVG icon.
	 */
	public function get_icon_svg( $base64 = true ) {
		$svg = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="100%" height="100%" style="fill:#82878c" viewBox="0 0 512 512" role="img" aria-hidden="true" focusable="false"><g><g><g><g><path d="M203.6,395c6.8-17.4,6.8-36.6,0-54l-79.4-204h70.9l47.7,149.4l74.8-207.6H116.4c-41.8,0-76,34.2-76,76V357c0,41.8,34.2,76,76,76H173C189,424.1,197.6,410.3,203.6,395z"/></g><g><path d="M471.6,154.8c0-41.8-34.2-76-76-76h-3L285.7,365c-9.6,26.7-19.4,49.3-30.3,68h216.2V154.8z"/></g></g><path stroke-width="2.974" stroke-miterlimit="10" d="M338,1.3l-93.3,259.1l-42.1-131.9h-89.1l83.8,215.2c6,15.5,6,32.5,0,48c-7.4,19-19,37.3-53,41.9l-7.2,1v76h8.3c81.7,0,118.9-57.2,149.6-142.9L431.6,1.3H338z M279.4,362c-32.9,92-67.6,128.7-125.7,131.8v-45c37.5-7.5,51.3-31,59.1-51.1c7.5-19.3,7.5-40.7,0-60l-75-192.7h52.8l53.3,166.8l105.9-294h58.1L279.4,362z"/></g></g></svg>';

		if ( $base64 ) {
			//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- This encoding is intended.
			return 'data:image/svg+xml;base64,' . base64_encode( $svg );
		}

		return $svg;
	}
}
