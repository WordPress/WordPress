<?php

namespace Yoast\WP\SEO\User_Profiles_Additions\User_Interface;

use WP_User;
use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\User_Profile_Conditional;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Adds a new hook in the user profiles edit screen to add content.
 */
class User_Profiles_Additions_Ui implements Integration_Interface {

	/**
	 * Holds the Product_Helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * Holds the WPSEO_Admin_Asset_Manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Constructs Academy_Integration.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager  The WPSEO_Admin_Asset_Manager.
	 * @param Product_Helper            $product_helper The Product_Helper.
	 */
	public function __construct( WPSEO_Admin_Asset_Manager $asset_manager, Product_Helper $product_helper ) {
		$this->asset_manager  = $asset_manager;
		$this->product_helper = $product_helper;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ User_Profile_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'show_user_profile', [ $this, 'add_hook_to_user_profile' ] );
		\add_action( 'edit_user_profile', [ $this, 'add_hook_to_user_profile' ] );
	}

	/**
	 * Enqueues the assets needed for this integration.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( $this->product_helper->is_premium() ) {
			$this->asset_manager->enqueue_style( 'introductions' );
		}
	}

	/**
	 * Add the inputs needed for SEO values to the User Profile page.
	 *
	 * @param WP_User $user User instance to output for.
	 *
	 * @return void
	 */
	public function add_hook_to_user_profile( $user ) {
		$this->enqueue_assets();
		echo '<div class="yoast yoast-settings">';

		/**
		 * Fires in the user profile.
		 *
		 * @internal
		 *
		 * @param WP_User $user The current WP_User object.
		 */
		\do_action( 'wpseo_user_profile_additions', $user );
		echo '</div>';
	}
}
