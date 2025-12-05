<?php

namespace Yoast\WP\SEO\Introductions\User_Interface;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Introductions\Application\Current_Page_Trait;
use Yoast\WP\SEO\Introductions\Application\Introductions_Collector;
use Yoast\WP\SEO\Introductions\Infrastructure\Introductions_Seen_Repository;
use Yoast\WP\SEO\Introductions\Infrastructure\Wistia_Embed_Permission_Repository;

/**
 * Loads introduction modal scripts, when there are applicable introductions.
 */
class Introductions_Integration implements Integration_Interface {

	use Current_Page_Trait;

	public const SCRIPT_HANDLE = 'introductions';

	/**
	 * Holds the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $admin_asset_manager;

	/**
	 * Holds the introduction collector.
	 *
	 * @var Introductions_Collector
	 */
	private $introductions_collector;

	/**
	 * Holds the product helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * Holds the user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Holds the short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	private $short_link_helper;

	/**
	 * Holds the repository.
	 *
	 * @var Wistia_Embed_Permission_Repository
	 */
	private $wistia_embed_permission_repository;

	/**
	 * Holds the WooCommerce conditional.
	 *
	 * @var WooCommerce_Conditional
	 */
	private $woocommerce_conditional;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * In this case: when on an admin page.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Constructs the integration.
	 *
	 * @param WPSEO_Admin_Asset_Manager          $admin_asset_manager                The admin asset manager.
	 * @param Introductions_Collector            $introductions_collector            The introductions' collector.
	 * @param Product_Helper                     $product_helper                     The product helper.
	 * @param User_Helper                        $user_helper                        The user helper.
	 * @param Short_Link_Helper                  $short_link_helper                  The short link helper.
	 * @param Wistia_Embed_Permission_Repository $wistia_embed_permission_repository The repository.
	 * @param WooCommerce_Conditional            $woocommerce_conditional            The WooCommerce conditional.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $admin_asset_manager,
		Introductions_Collector $introductions_collector,
		Product_Helper $product_helper,
		User_Helper $user_helper,
		Short_Link_Helper $short_link_helper,
		Wistia_Embed_Permission_Repository $wistia_embed_permission_repository,
		WooCommerce_Conditional $woocommerce_conditional
	) {
		$this->admin_asset_manager                = $admin_asset_manager;
		$this->introductions_collector            = $introductions_collector;
		$this->product_helper                     = $product_helper;
		$this->user_helper                        = $user_helper;
		$this->short_link_helper                  = $short_link_helper;
		$this->wistia_embed_permission_repository = $wistia_embed_permission_repository;
		$this->woocommerce_conditional            = $woocommerce_conditional;
	}

	/**
	 * Registers the action to enqueue the needed script(s).
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( $this->is_on_installation_page() ) {
			return;
		}
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue the new features assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$user_id = $this->user_helper->get_current_user_id();
		$this->update_metadata_for( $user_id );
		$introductions = $this->introductions_collector->get_for( $user_id );

		if ( ! $introductions ) {
			// Bail when there are no introductions to show.
			return;
		}
		// Update user meta to have "seen" these introductions.
		$this->update_user_introductions( $user_id, $introductions );

		$this->admin_asset_manager->enqueue_script( self::SCRIPT_HANDLE );
		$this->admin_asset_manager->localize_script(
			self::SCRIPT_HANDLE,
			'wpseoIntroductions',
			[
				'introductions'         => $introductions,
				'isPremium'             => $this->product_helper->is_premium(),
				'isRtl'                 => \is_rtl(),
				'linkParams'            => $this->short_link_helper->get_query_params(),
				'pluginUrl'             => \plugins_url( '', \WPSEO_FILE ),
				'wistiaEmbedPermission' => $this->wistia_embed_permission_repository->get_value_for_user( $user_id ),
				'isWooEnabled'          => $this->woocommerce_conditional->is_met(),
			]
		);
		$this->admin_asset_manager->enqueue_style( 'introductions' );
	}

	/**
	 * Updates the user metadata to have "seen" the introductions.
	 *
	 * @param int   $user_id       The user ID.
	 * @param array $introductions The introductions.
	 *
	 * @return void
	 */
	private function update_user_introductions( $user_id, $introductions ) {
		$metadata = $this->user_helper->get_meta( $user_id, Introductions_Seen_Repository::USER_META_KEY, true );
		if ( ! \is_array( $metadata ) ) {
			$metadata = [];
		}

		if ( empty( $introductions ) || ! \is_array( $introductions ) ) {
			return;
		}

		// Find the introduction with the highest priority, because JS will only show that one.
		$highest_priority_intro = \array_reduce(
			$introductions,
			static function ( $carry, $item ) {
				return ( $carry === null || $item['priority'] < $carry['priority'] ) ? $item : $carry;
			},
			null
		);

		if ( $highest_priority_intro === null ) {
			return;
		}

		// Mark the introduction with the highest priority as seen.
		$metadata[ $highest_priority_intro['id'] ]['is_seen'] = true;
		$metadata[ $highest_priority_intro['id'] ]['seen_on'] = \time();

		$this->user_helper->update_meta( $user_id, Introductions_Seen_Repository::USER_META_KEY, $metadata );
	}

	/**
	 * Updates the introductions metadata format for the user
	 * This is needed because we're introducing timestamps for introductions that have been seen, thus changing the format.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	private function update_metadata_for( int $user_id ) {
		$metadata = $this->introductions_collector->get_metadata( $user_id );
		foreach ( $metadata as $introduction_name => $introduction_data ) {
			if ( \is_bool( $introduction_data ) ) {
				$metadata[ $introduction_name ] = [
					'is_seen' => $introduction_data,
					'seen_on' => ( $introduction_data === true ) ? \time() : 0,
				];
			}
		}
		$this->user_helper->update_meta( $user_id, Introductions_Seen_Repository::USER_META_KEY, $metadata );
	}
}
