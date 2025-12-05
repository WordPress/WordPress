<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Premium_Inactive_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Introductions\Infrastructure\Wistia_Embed_Permission_Repository;

/**
 * Redirects_Page_Integration class.
 */
class Redirects_Page_Integration implements Integration_Interface {

	/**
	 * The page identifier.
	 */
	public const PAGE = 'wpseo_redirects';

	/**
	 * The current page helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * The Wistia embed permission repository.
	 *
	 * @var Wistia_Embed_Permission_Repository
	 */
	private $wistia_embed_permission_repository;

	/**
	 * Constructor.
	 *
	 * @param Current_Page_Helper                $current_page_helper                The current page helper.
	 * @param User_Helper                        $user_helper                        The user helper.
	 * @param Wistia_Embed_Permission_Repository $wistia_embed_permission_repository The Wistia embed permission
	 *                                                                               repository.
	 */
	public function __construct(
		Current_Page_Helper $current_page_helper,
		User_Helper $user_helper,
		Wistia_Embed_Permission_Repository $wistia_embed_permission_repository
	) {
		$this->current_page_helper                = $current_page_helper;
		$this->user_helper                        = $user_helper;
		$this->wistia_embed_permission_repository = $wistia_embed_permission_repository;
	}

	/**
	 * Sets up the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_submenu_pages', [ $this, 'add_submenu_page' ], 9 );
		if ( $this->current_page_helper->get_current_yoast_seo_page() === self::PAGE ) {
			\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			\add_action( 'in_admin_header', [ $this, 'remove_notices' ], \PHP_INT_MAX );
		}
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * In this case: only when on an admin page and Premium is not active.
	 *
	 * @return array The conditionals.
	 */
	public static function get_conditionals() {
		return [
			Admin_Conditional::class,
			Premium_Inactive_Conditional::class,
		];
	}

	/**
	 * Adds the redirects submenu page.
	 *
	 * @param array $submenu_pages The Yoast SEO submenu pages.
	 *
	 * @return array The filtered submenu pages.
	 */
	public function add_submenu_page( $submenu_pages ) {
		$submenu_pages[] = [
			'wpseo_dashboard',
			'',
			\__( 'Redirects', 'wordpress-seo' ) . ' <span class="yoast-badge yoast-premium-badge"></span>',
			'edit_others_posts',
			self::PAGE,
			[ $this, 'display' ],
		];

		return $submenu_pages;
	}

	/**
	 * Enqueue assets on the redirects page.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_script( 'redirects' );
		$asset_manager->enqueue_style( 'redirects' );
		$user_id = $this->user_helper->get_current_user_id();
		$asset_manager->localize_script(
			'redirects',
			'wpseoScriptData',
			[
				'preferences'           => [
					'isRtl'                 => \is_rtl(),
					'isComingFromToolsPage' => $this->is_coming_from_tools_page(),
				],
				'linkParams'            => \YoastSEO()->helpers->short_link->get_query_params(),
				'pluginUrl'             => \plugins_url( '', \WPSEO_FILE ),
				'wistiaEmbedPermission' => $this->wistia_embed_permission_repository->get_value_for_user( $user_id ),
			]
		);
	}

	/**
	 * Displays the redirects page.
	 *
	 * @return void
	 */
	public function display() {
		require \WPSEO_PATH . 'admin/pages/redirects.php';
	}

	/**
	 * Removes all current WP notices.
	 *
	 * @return void
	 */
	public function remove_notices() {
		\remove_all_actions( 'admin_notices' );
		\remove_all_actions( 'user_admin_notices' );
		\remove_all_actions( 'network_admin_notices' );
		\remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * Checks whether the user is coming from the tools page.
	 *
	 * @return bool
	 */
	public function is_coming_from_tools_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are simply checking against a set value.
		return isset( $_GET['from_tools'] ) && $_GET['from_tools'] === '1';
	}
}
