<?php
namespace Elementor\Core\Common\Modules\Connect;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\Connect\Apps\Base_App;
use Elementor\Core\Common\Modules\Connect\Apps\Common_App;
use Elementor\Core\Common\Modules\Connect\Apps\Connect;
use Elementor\Core\Common\Modules\Connect\Apps\Library;
use Elementor\Plugin;
use Elementor\Utils;
use WP_User_Query;
use Elementor\Core\Common\Modules\Connect\Rest\Rest_Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {
	const ACCESS_LEVEL_CORE = 0;
	const ACCESS_LEVEL_PRO = 1;
	const ACCESS_LEVEL_EXPERT = 20;

	const ACCESS_TIER_FREE = 'free';
	const ACCESS_TIER_ESSENTIAL = 'essential';
	const ACCESS_TIER_ESSENTIAL_OCT_2023 = 'essential-oct2023';
	const ACCESS_TIER_ADVANCED = 'advanced';
	const ACCESS_TIER_EXPERT = 'expert';
	const ACCESS_TIER_AGENCY = 'agency';
	const ACCESS_TIER_PRO_LEGACY = 'pro';

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function get_name() {
		return 'connect';
	}

	/**
	 * @var array
	 */
	protected $registered_apps = [];

	/**
	 * Apps Instances.
	 *
	 * Holds the list of all the apps instances.
	 *
	 * @since 2.3.0
	 * @access protected
	 *
	 * @var Base_App[]
	 */
	protected $apps = [];

	/**
	 * Registered apps categories.
	 *
	 * Holds the list of all the registered apps categories.
	 *
	 * @since 2.3.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $categories = [];

	protected $admin_page;

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function __construct() {
		$this->registered_apps = [
			'connect' => Connect::get_class_name(),
			'library' => Library::get_class_name(),
		];

		// When using REST API the parent module is construct after the action 'elementor/init'
		// so this part of code make sure to register the module "apps".
		if ( did_action( 'elementor/init' ) ) {
			$this->init();
		} else {
			// Note: The priority 11 is for allowing plugins to add their register callback on elementor init.
			add_action( 'elementor/init', [ $this, 'init' ], 11 );
		}

		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );

		add_filter( 'elementor/tracker/send_tracking_data_params', function ( $params ) {
			return $this->add_tracking_data( $params );
		} );
	}

	/**
	 * Register default apps.
	 *
	 * Registers the default apps.
	 *
	 * @since 2.3.0
	 * @access public
	 */
	public function init() {
		if ( is_admin() ) {
			$this->admin_page = new Admin();
		}

		/**
		 * Register Elementor apps.
		 *
		 * Fires after Elementor registers the default apps.
		 *
		 * @since 2.3.0
		 *
		 * @param self $this The apps manager instance.
		 */
		do_action( 'elementor/connect/apps/register', $this );

		foreach ( $this->registered_apps as $slug => $class ) {
			$this->apps[ $slug ] = new $class();
		}
	}

	/**
	 * Register app.
	 *
	 * Registers an app.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param string $slug       App slug.
	 * @param string $class_name App full class name.
	 *
	 * @return self The updated apps manager instance.
	 */
	public function register_app( $slug, $class_name ) {
		$this->registered_apps[ $slug ] = $class_name;

		return $this;
	}

	/**
	 * Get app instance.
	 *
	 * Retrieve the app instance.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param $slug
	 *
	 * @return Base_App|null
	 */
	public function get_app( $slug ) {
		if ( isset( $this->apps[ $slug ] ) ) {
			return $this->apps[ $slug ];
		}

		return null;
	}

	/**
	 * @since 2.3.0
	 * @access public
	 * @return Base_App[]
	 */
	public function get_apps() {
		return $this->apps;
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function register_category( $slug, $args ) {
		$this->categories[ $slug ] = $args;
		return $this;
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function get_categories() {
		return $this->categories;
	}

	/**
	 * @param string $context Where this subscription plan should be shown.
	 *
	 * @return array
	 */
	public function get_subscription_plans( $context = '' ) {
		$base_url = Utils::has_pro() ? 'https://my.elementor.com/upgrade-subscription' : 'https://elementor.com/pro';
		$promotion_url = $base_url . '/?utm_source=' . $context . '&utm_medium=wp-dash&utm_campaign=gopro';

		return [
			static::ACCESS_TIER_FREE => [
				'label' => null,
				'promotion_url' => null,
				'color' => null,
			],
			static::ACCESS_TIER_ESSENTIAL => [
				'label' => 'Pro',
				'promotion_url' => $promotion_url,
				'color' => '#92003B',
			],
			static::ACCESS_TIER_ESSENTIAL_OCT_2023 => [
				'label' => 'Advanced', // Should be the same label as "Advanced".
				'promotion_url' => $promotion_url,
				'color' => '#92003B',
			],
			static::ACCESS_TIER_ADVANCED => [
				'label' => 'Advanced',
				'promotion_url' => $promotion_url,
				'color' => '#92003B',
			],
			static::ACCESS_TIER_EXPERT => [
				'label' => 'Expert',
				'promotion_url' => $promotion_url,
				'color' => '#92003B',
			],
			static::ACCESS_TIER_AGENCY => [
				'label' => 'Agency',
				'promotion_url' => $promotion_url,
				'color' => '#92003B',
			],
		];
	}

	private function add_tracking_data( $params ) {
		$users = [];

		$users_query = new WP_User_Query( [
			'count_total' => false, // Disable SQL_CALC_FOUND_ROWS.
			'meta_query' => [
				'key' => Common_App::OPTION_CONNECT_COMMON_DATA_KEY,
				'compare' => 'EXISTS',
			],
		] );

		foreach ( $users_query->get_results() as $user ) {
			$connect_common_data = get_user_option( Common_App::OPTION_CONNECT_COMMON_DATA_KEY, $user->ID );

			if ( $connect_common_data ) {
				$users [] = [
					'id' => $user->ID,
					'email' => $connect_common_data['user']->email,
					'roles' => implode( ', ', $user->roles ),
				];
			}
		}

		$params['usages'][ $this->get_name() ] = [
			'site_key' => get_option( Base_App::OPTION_CONNECT_SITE_KEY ),
			'count' => count( $users ),
			'users' => $users,
		];

		return $params;
	}

	public function register_rest_routes() {
		$rest_api = new Rest_Api();
		$rest_api->register_routes();
	}
}
