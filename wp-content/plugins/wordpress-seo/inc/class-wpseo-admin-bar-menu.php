<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Score_Icon_Helper;
use Yoast\WP\SEO\Integrations\Support_Integration;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Presenters\Admin\Premium_Badge_Presenter;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Class for the Yoast SEO admin bar menu.
 */
class WPSEO_Admin_Bar_Menu implements WPSEO_WordPress_Integration {

	/**
	 * The identifier used for the menu.
	 *
	 * @var string
	 */
	public const MENU_IDENTIFIER = 'wpseo-menu';

	/**
	 * The identifier used for the Keyword Research submenu.
	 *
	 * @var string
	 */
	public const KEYWORD_RESEARCH_SUBMENU_IDENTIFIER = 'wpseo-kwresearch';

	/**
	 * The identifier used for the Analysis submenu.
	 *
	 * @var string
	 */
	public const ANALYSIS_SUBMENU_IDENTIFIER = 'wpseo-analysis';

	/**
	 * The identifier used for the Settings submenu.
	 *
	 * @var string
	 */
	public const SETTINGS_SUBMENU_IDENTIFIER = 'wpseo-settings';

	/**
	 * The identifier used for the Network Settings submenu.
	 *
	 * @var string
	 */
	public const NETWORK_SETTINGS_SUBMENU_IDENTIFIER = 'wpseo-network-settings';

	/**
	 * Asset manager instance.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * Holds the Score_Icon_Helper instance.
	 *
	 * @var Score_Icon_Helper
	 */
	protected $indexable_repository;

	/**
	 * Holds the Score_Icon_Helper instance.
	 *
	 * @var Score_Icon_Helper
	 */
	protected $score_icon_helper;

	/**
	 * Holds the Product_Helper instance.
	 *
	 * @var Product_Helper
	 */
	protected $product_helper;

	/**
	 * Holds the shortlinker instance.
	 *
	 * @var WPSEO_Shortlinker
	 */
	protected $shortlinker;

	/**
	 * Whether SEO Score is enabled.
	 *
	 * @var bool|null
	 */
	protected $is_seo_enabled = null;

	/**
	 * Whether readability is enabled.
	 *
	 * @var bool|null
	 */
	protected $is_readability_enabled = null;

	/**
	 * The indexable for the current WordPress page, if found.
	 *
	 * @var Indexable|bool|null
	 */
	protected $current_indexable = null;

	/**
	 * Constructs the WPSEO_Admin_Bar_Menu.
	 *
	 * @param WPSEO_Admin_Asset_Manager|null $asset_manager        Optional. Asset manager to use.
	 * @param Indexable_Repository|null      $indexable_repository Optional. The Indexable_Repository.
	 * @param Score_Icon_Helper|null         $score_icon_helper    Optional. The Score_Icon_Helper.
	 * @param Product_Helper|null            $product_helper       Optional. The product helper.
	 * @param WPSEO_Shortlinker|null         $shortlinker          The shortlinker.
	 */
	public function __construct(
		?WPSEO_Admin_Asset_Manager $asset_manager = null,
		?Indexable_Repository $indexable_repository = null,
		?Score_Icon_Helper $score_icon_helper = null,
		?Product_Helper $product_helper = null,
		?WPSEO_Shortlinker $shortlinker = null
	) {
		if ( ! $asset_manager ) {
			$asset_manager = new WPSEO_Admin_Asset_Manager();
		}
		if ( ! $indexable_repository ) {
			$indexable_repository = YoastSEO()->classes->get( Indexable_Repository::class );
		}
		if ( ! $score_icon_helper ) {
			$score_icon_helper = YoastSEO()->helpers->score_icon;
		}
		if ( ! $product_helper ) {
			$product_helper = YoastSEO()->helpers->product;
		}
		if ( ! $shortlinker ) {
			$shortlinker = new WPSEO_Shortlinker();
		}

		$this->product_helper       = $product_helper;
		$this->asset_manager        = $asset_manager;
		$this->indexable_repository = $indexable_repository;
		$this->score_icon_helper    = $score_icon_helper;
		$this->shortlinker          = $shortlinker;
	}

	/**
	 * Gets whether SEO score is enabled, with cache applied.
	 *
	 * @return bool True if SEO score is enabled, false otherwise.
	 */
	protected function get_is_seo_enabled() {
		if ( $this->is_seo_enabled === null ) {
			$this->is_seo_enabled = ( new WPSEO_Metabox_Analysis_SEO() )->is_enabled();
		}

		return $this->is_seo_enabled;
	}

	/**
	 * Gets whether readability is enabled, with cache applied.
	 *
	 * @return bool True if readability is enabled, false otherwise.
	 */
	protected function get_is_readability_enabled() {
		if ( $this->is_readability_enabled === null ) {
			$this->is_readability_enabled = ( new WPSEO_Metabox_Analysis_Readability() )->is_enabled();
		}

		return $this->is_readability_enabled;
	}

	/**
	 * Returns the indexable for the current WordPress page, with cache applied.
	 *
	 * @return bool|Indexable The indexable, false if none could be found.
	 */
	protected function get_current_indexable() {
		if ( $this->current_indexable === null ) {
			$this->current_indexable = $this->indexable_repository->for_current_page();
		}

		return $this->current_indexable;
	}

	/**
	 * Adds the admin bar menu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	public function add_menu( WP_Admin_Bar $wp_admin_bar ) {
		// On block editor pages, the admin bar only shows on mobile, where having this menu icon is not very helpful.
		if ( is_admin() ) {
			$screen = get_current_screen();
			if ( isset( $screen ) && $screen->is_block_editor() ) {
				return;
			}
		}

		// If the current user can't write posts, this is all of no use, so let's not output an admin menu.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$this->add_root_menu( $wp_admin_bar );

		/**
		 * Adds a submenu item in the top of the adminbar.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar    Admin bar instance to add the menu to.
		 * @param string       $menu_identifier The menu identifier.
		 */
		do_action( 'wpseo_add_adminbar_submenu', $wp_admin_bar, self::MENU_IDENTIFIER );

		if ( ! is_admin() ) {

			if ( is_singular() || is_tag() || is_tax() || is_category() ) {
				$is_seo_enabled         = $this->get_is_seo_enabled();
				$is_readability_enabled = $this->get_is_readability_enabled();

				$indexable = $this->get_current_indexable();

				if ( $is_seo_enabled ) {
					$focus_keyword = ( ! is_a( $indexable, 'Yoast\WP\SEO\Models\Indexable' ) || $indexable->primary_focus_keyword === null ) ? __( 'not set', 'wordpress-seo' ) : $indexable->primary_focus_keyword;

					$wp_admin_bar->add_menu(
						[
							'parent' => self::MENU_IDENTIFIER,
							'id'     => 'wpseo-seo-focus-keyword',
							'title'  => __( 'Focus keyphrase: ', 'wordpress-seo' ) . '<span class="wpseo-focus-keyword">' . $focus_keyword . '</span>',
							'meta'   => [ 'tabindex' => '0' ],
						]
					);
					$wp_admin_bar->add_menu(
						[
							'parent' => self::MENU_IDENTIFIER,
							'id'     => 'wpseo-seo-score',
							'title'  => __( 'SEO score', 'wordpress-seo' ) . ': ' . $this->score_icon_helper->for_seo( $indexable, 'adminbar-sub-menu-score' )
									->present(),
							'meta'   => [ 'tabindex' => '0' ],
						]
					);
				}

				if ( $is_readability_enabled ) {
					$wp_admin_bar->add_menu(
						[
							'parent' => self::MENU_IDENTIFIER,
							'id'     => 'wpseo-readability-score',
							'title'  => __( 'Readability', 'wordpress-seo' ) . ': ' . $this->score_icon_helper->for_readability( $indexable->readability_score, 'adminbar-sub-menu-score' )
									->present(),
							'meta'   => [ 'tabindex' => '0' ],
						]
					);
				}

				if ( ! $this->product_helper->is_premium() ) {
					$wp_admin_bar->add_menu(
						[
							'parent' => self::MENU_IDENTIFIER,
							'id'     => 'wpseo-frontend-inspector',
							'href'   => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-frontend-inspector' ),
							'title'  => __( 'Front-end SEO inspector', 'wordpress-seo' ) . new Premium_Badge_Presenter( 'wpseo-frontend-inspector-badge' ),
							'meta'   => [
								'tabindex' => '0',
								'target'   => '_blank',
							],
						]
					);
				}
			}
			$this->add_analysis_submenu( $wp_admin_bar );
			$this->add_seo_tools_submenu( $wp_admin_bar );
			$this->add_how_to_submenu( $wp_admin_bar );
			$this->add_get_help_submenu( $wp_admin_bar );
		}

		if ( ! is_admin() || is_blog_admin() ) {
			$this->add_settings_submenu( $wp_admin_bar );
		}
		elseif ( is_network_admin() ) {
			$this->add_network_settings_submenu( $wp_admin_bar );
		}

		$this->add_premium_link( $wp_admin_bar );
	}

	/**
	 * Enqueues admin bar assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		// If the current user can't write posts, this is all of no use, so let's not output an admin menu.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$this->asset_manager->register_assets();
		$this->asset_manager->enqueue_style( 'adminbar' );
	}

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! $this->meets_requirements() ) {
			return;
		}

		add_action( 'admin_bar_menu', [ $this, 'add_menu' ], 95 );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Checks whether the requirements to use this class are met.
	 *
	 * @return bool True if requirements are met, false otherwise.
	 */
	public function meets_requirements() {
		if ( is_network_admin() ) {
			return WPSEO_Utils::is_plugin_network_active();
		}

		if ( WPSEO_Options::get( 'enable_admin_bar_menu' ) !== true ) {
			return false;
		}

		return ! is_admin() || is_blog_admin();
	}

	/**
	 * Adds the admin bar root menu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	protected function add_root_menu( WP_Admin_Bar $wp_admin_bar ) {
		$title = $this->get_title();

		$score              = '';
		$settings_url       = '';
		$counter            = '';
		$notification_popup = '';
		$notification_count = 0;

		$post = $this->get_singular_post();
		if ( $post ) {
			$score = $this->get_post_score( $post );
		}

		$term = $this->get_singular_term();
		if ( $term ) {
			$score = $this->get_term_score( $term );
		}

		$can_manage_options = $this->can_manage_options();

		if ( $can_manage_options ) {
			$settings_url = $this->get_settings_page_url();
		}

		if ( empty( $score ) && ! is_network_admin() && $can_manage_options ) {
			$notification_center = Yoast_Notification_Center::get();
			$notification_count  = $notification_center->get_notification_count();

			$counter            = $this->get_notification_counter( $notification_count );
			$notification_popup = $this->get_notification_popup();
		}

		$admin_bar_menu_args = [
			'id'    => self::MENU_IDENTIFIER,
			'title' => $title . $score . $counter . $notification_popup,
			'href'  => $settings_url,
			'meta'  => [ 'tabindex' => ! empty( $settings_url ) ? false : '0' ],
		];
		$wp_admin_bar->add_menu( $admin_bar_menu_args );

		if ( $notification_count > 0 ) {
			$admin_bar_menu_args = [
				'parent' => self::MENU_IDENTIFIER,
				'id'     => 'wpseo-notifications',
				'title'  => __( 'Notifications', 'wordpress-seo' ) . $counter,
				'href'   => empty( $settings_url ) ? '' : $settings_url . '#/alert-center',
				'meta'   => [ 'tabindex' => ! empty( $settings_url ) ? false : '0' ],
			];
			$wp_admin_bar->add_menu( $admin_bar_menu_args );
		}
	}

	/**
	 * Adds the admin bar analysis submenu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	protected function add_analysis_submenu( WP_Admin_Bar $wp_admin_bar ) {
		try {
			$url = YoastSEO()->meta->for_current_page()->canonical;
		} catch ( Exception $e ) {
			// This is not the type of error we can handle here.
			return;
		}

		if ( ! $url ) {
			return;
		}

		$menu_args = [
			'parent' => self::MENU_IDENTIFIER,
			'id'     => self::ANALYSIS_SUBMENU_IDENTIFIER,
			'title'  => __( 'Analyze this page', 'wordpress-seo' ),
			'meta'   => [ 'tabindex' => '0' ],
		];
		$wp_admin_bar->add_menu( $menu_args );

		$encoded_url   = rawurlencode( $url );
		$submenu_items = [
			[
				'id'    => 'wpseo-inlinks',
				'title' => __( 'Check links to this URL', 'wordpress-seo' ),
				'href'  => 'https://search.google.com/search-console/links/drilldown?resource_id=' . rawurlencode( get_option( 'siteurl' ) ) . '&type=EXTERNAL&target=' . $encoded_url . '&domain=',
			],
			[
				'id'    => 'wpseo-structureddata',
				'title' => __( 'Google Rich Results Test', 'wordpress-seo' ),
				'href'  => 'https://search.google.com/test/rich-results?url=' . $encoded_url,
			],
			[
				'id'    => 'wpseo-facebookdebug',
				'title' => __( 'Facebook Debugger', 'wordpress-seo' ),
				'href'  => '//developers.facebook.com/tools/debug/?q=' . $encoded_url,
			],
			[
				'id'    => 'wpseo-pagespeed',
				'title' => __( 'Google Page Speed Test', 'wordpress-seo' ),
				'href'  => '//developers.google.com/speed/pagespeed/insights/?url=' . $encoded_url,
			],
		];

		$this->add_submenu_items( $submenu_items, $wp_admin_bar, self::ANALYSIS_SUBMENU_IDENTIFIER );
	}

	/**
	 * Adds the admin bar tools submenu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	protected function add_seo_tools_submenu( WP_Admin_Bar $wp_admin_bar ) {
		$menu_args = [
			'parent' => self::MENU_IDENTIFIER,
			'id'     => 'wpseo-sub-tools',
			'title'  => __( 'SEO Tools', 'wordpress-seo' ),
			'meta'   => [ 'tabindex' => '0' ],
		];
		$wp_admin_bar->add_menu( $menu_args );

		$submenu_items = [
			[
				'id'    => 'wpseo-semrush',
				'title' => 'Semrush',
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-semrush' ),
			],
			[
				'id'    => 'wpseo-wincher',
				'title' => 'Wincher',
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-wincher' ),
			],
			[
				'id'    => 'wpseo-google-trends',
				'title' => 'Google trends',
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-gtrends' ),
			],
		];

		$this->add_submenu_items( $submenu_items, $wp_admin_bar, 'wpseo-sub-tools' );
	}

	/**
	 * Adds the admin bar How To submenu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	protected function add_how_to_submenu( WP_Admin_Bar $wp_admin_bar ) {
		$menu_args = [
			'parent' => self::MENU_IDENTIFIER,
			'id'     => 'wpseo-sub-howto',
			'title'  => __( 'How to', 'wordpress-seo' ),
			'meta'   => [ 'tabindex' => '0' ],
		];
		$wp_admin_bar->add_menu( $menu_args );

		$submenu_items = [
			[
				'id'    => 'wpseo-learn-seo',
				'title' => __( 'Learn more SEO', 'wordpress-seo' ),
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-learn-more-seo' ),
			],
			[
				'id'    => 'wpseo-improve-blogpost',
				'title' => __( 'Improve your blog post', 'wordpress-seo' ),
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-improve-blog-post' ),
			],
			[
				'id'    => 'wpseo-write-better-content',
				'title' => __( 'Write better content', 'wordpress-seo' ),
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-write-better' ),
			],
		];

		$this->add_submenu_items( $submenu_items, $wp_admin_bar, 'wpseo-sub-howto' );
	}

	/**
	 * Adds the admin bar How To submenu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	protected function add_get_help_submenu( WP_Admin_Bar $wp_admin_bar ) {
		$menu_args = [
			'parent' => self::MENU_IDENTIFIER,
			'id'     => 'wpseo-sub-get-help',
			'title'  => __( 'Help', 'wordpress-seo' ),
			'meta'   => [ 'tabindex' => '0' ],
		];

		if ( current_user_can( Support_Integration::CAPABILITY ) ) {
			$menu_args['href'] = admin_url( 'admin.php?page=' . Support_Integration::PAGE );
			$wp_admin_bar->add_menu( $menu_args );

			return;
		}
		$wp_admin_bar->add_menu( $menu_args );

		$submenu_items = [
			[
				'id'    => 'wpseo-yoast-help',
				'title' => __( 'Yoast.com help section', 'wordpress-seo' ),
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-yoast-help' ),
			],
			[
				'id'    => 'wpseo-premium-support',
				'title' => __( 'Yoast Premium support', 'wordpress-seo' ),
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-premium-support' ),
			],
			[
				'id'    => 'wpseo-wp-support-forums',
				'title' => __( 'WordPress.org support forums', 'wordpress-seo' ),
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-wp-support-forums' ),
			],
			[
				'id'    => 'wpseo-learn-seo-2',
				'title' => __( 'Learn more SEO', 'wordpress-seo' ),
				'href'  => $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-learn-more-seo-help' ),
			],
		];

		$this->add_submenu_items( $submenu_items, $wp_admin_bar, 'wpseo-sub-get-help' );
	}

	/**
	 * Adds the admin bar How To submenu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	protected function add_premium_link( WP_Admin_Bar $wp_admin_bar ) {
		$link            = $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-get-premium' );
		$has_woocommerce = ( new Woocommerce_Conditional() )->is_met();

		if ( $this->product_helper->is_premium() ) {
			$link = $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-get-ai-insights' );
		}
		elseif ( $has_woocommerce ) {
			$link = $this->shortlinker->build_shortlink( 'https://yoa.st/admin-bar-get-premium-woocommerce' );
		}

		$button_label = esc_html__( 'Upgrade', 'wordpress-seo' );
		$badge        = '';
		if ( $this->product_helper->is_premium() ) {
			$badge = '<div id="wpseo-new-badge-upgrade">' . __( 'New', 'wordpress-seo' ) . '</div>';
		}

		if ( YoastSEO()->classes->get( Promotion_Manager::class )->is( 'black-friday-promotion' ) ) {
			$button_label = esc_html__( '30% off - BF Sale', 'wordpress-seo' );
		}
		$wp_admin_bar->add_menu(
			[
				'parent' => self::MENU_IDENTIFIER,
				'id'     => 'wpseo-get-premium',
				// Circumvent an issue in the WP admin bar API in order to pass `data` attributes. See https://core.trac.wordpress.org/ticket/38636.
				'title'  => sprintf(
					'<a href="%1$s" target="_blank" data-action="load-nfd-ctb" data-ctb-id="f6a84663-465f-4cb5-8ba5-f7a6d72224b2">%2$s</a>%3$s',
					esc_url( $link ),
					$button_label,
					$badge,
				),
				'meta'   => [
					'tabindex' => '0',
				],
			]
		);
	}

	/**
	 * Adds the admin bar settings submenu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	protected function add_settings_submenu( WP_Admin_Bar $wp_admin_bar ) {
		if ( ! $this->can_manage_options() ) {
			return;
		}

		$admin_menu    = new WPSEO_Admin_Menu( new WPSEO_Menu() );
		$submenu_pages = $admin_menu->get_submenu_pages();

		$menu_args = [
			'parent' => self::MENU_IDENTIFIER,
			'id'     => self::SETTINGS_SUBMENU_IDENTIFIER,
			'title'  => __( 'SEO Settings', 'wordpress-seo' ),
			'meta'   => [ 'tabindex' => '0' ],
		];
		$wp_admin_bar->add_menu( $menu_args );

		foreach ( $submenu_pages as $submenu_page ) {
			if ( ! current_user_can( $submenu_page[3] ) ) {
				continue;
			}

			// Don't add the Google Search Console menu item.
			if ( $submenu_page[4] === 'wpseo_search_console' ) {
				continue;
			}

			$id = 'wpseo-' . str_replace( '_', '-', str_replace( 'wpseo_', '', $submenu_page[4] ) );
			if ( $id === 'wpseo-dashboard' ) {
				$id = 'wpseo-general';
			}

			$menu_args = [
				'parent' => self::SETTINGS_SUBMENU_IDENTIFIER,
				'id'     => $id,
				'title'  => $submenu_page[2],
				'href'   => admin_url( 'admin.php?page=' . rawurlencode( $submenu_page[4] ) ),
			];
			$wp_admin_bar->add_menu( $menu_args );
		}
	}

	/**
	 * Adds the admin bar network settings submenu.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance to add the menu to.
	 *
	 * @return void
	 */
	protected function add_network_settings_submenu( WP_Admin_Bar $wp_admin_bar ) {
		if ( ! $this->can_manage_options() ) {
			return;
		}

		$network_admin_menu = new WPSEO_Network_Admin_Menu( new WPSEO_Menu() );
		$submenu_pages      = $network_admin_menu->get_submenu_pages();

		$menu_args = [
			'parent' => self::MENU_IDENTIFIER,
			'id'     => self::NETWORK_SETTINGS_SUBMENU_IDENTIFIER,
			'title'  => __( 'SEO Settings', 'wordpress-seo' ),
			'meta'   => [ 'tabindex' => '0' ],
		];
		$wp_admin_bar->add_menu( $menu_args );

		foreach ( $submenu_pages as $submenu_page ) {
			if ( ! current_user_can( $submenu_page[3] ) ) {
				continue;
			}

			$id = 'wpseo-' . str_replace( '_', '-', str_replace( 'wpseo_', '', $submenu_page[4] ) );
			if ( $id === 'wpseo-dashboard' ) {
				$id = 'wpseo-general';
			}

			$menu_args = [
				'parent' => self::NETWORK_SETTINGS_SUBMENU_IDENTIFIER,
				'id'     => $id,
				'title'  => $submenu_page[2],
				'href'   => network_admin_url( 'admin.php?page=' . rawurlencode( $submenu_page[4] ) ),
			];
			$wp_admin_bar->add_menu( $menu_args );
		}
	}

	/**
	 * Gets the menu title markup.
	 *
	 * @return string Admin bar title markup.
	 */
	protected function get_title() {
		return '<div id="yoast-ab-icon" class="ab-item yoast-logo svg"><span class="screen-reader-text">' . __( 'SEO', 'wordpress-seo' ) . '</span></div>';
	}

	/**
	 * Gets the current post if in a singular post context.
	 *
	 * @global string       $pagenow Current page identifier.
	 * @global WP_Post|null $post    Current post object, or null if none available.
	 *
	 * @return WP_Post|null Post object, or null if not in singular context.
	 */
	protected function get_singular_post() {
		global $pagenow, $post;

		if ( ! is_singular() && ( ! is_blog_admin() || ! WPSEO_Metabox::is_post_edit( $pagenow ) ) ) {
			return null;
		}

		if ( ! isset( $post ) || ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return null;
		}

		return $post;
	}

	/**
	 * Gets the focus keyword for a given post.
	 *
	 * @param WP_Post $post Post object to get its focus keyword.
	 *
	 * @return string Focus keyword, or empty string if none available.
	 */
	protected function get_post_focus_keyword( $post ) {
		if ( ! is_object( $post ) || ! property_exists( $post, 'ID' ) ) {
			return '';
		}

		/**
		 * Filter: 'wpseo_use_page_analysis' Determines if the analysis should be enabled.
		 *
		 * @param bool $enabled Determines if the analysis should be enabled.
		 */
		if ( apply_filters( 'wpseo_use_page_analysis', true ) !== true ) {
			return '';
		}

		return WPSEO_Meta::get_value( 'focuskw', $post->ID );
	}

	/**
	 * Gets the score for a given post.
	 *
	 * @param WP_Post $post Post object to get its score.
	 *
	 * @return string Score markup, or empty string if none available.
	 */
	protected function get_post_score( $post ) {
		if ( ! is_object( $post ) || ! property_exists( $post, 'ID' ) ) {
			return '';
		}

		if ( apply_filters( 'wpseo_use_page_analysis', true ) !== true ) {
			return '';
		}

		return $this->get_score_icon();
	}

	/**
	 * Gets the current term if in a singular term context.
	 *
	 * @global string       $pagenow  Current page identifier.
	 * @global WP_Query     $wp_query Current query object.
	 * @global WP_Term|null $tag      Current term object, or null if none available.
	 *
	 * @return WP_Term|null Term object, or null if not in singular context.
	 */
	protected function get_singular_term() {
		global $pagenow, $wp_query, $tag;

		if ( is_category() || is_tag() || is_tax() ) {
			return $wp_query->get_queried_object();
		}

		if ( WPSEO_Taxonomy::is_term_edit( $pagenow ) && ! WPSEO_Taxonomy::is_term_overview( $pagenow ) && isset( $tag ) && is_object( $tag ) && ! is_wp_error( $tag ) ) {
			return get_term( $tag->term_id );
		}

		return null;
	}

	/**
	 * Gets the score for a given term.
	 *
	 * @param WP_Term $term Term object to get its score.
	 *
	 * @return string Score markup, or empty string if none available.
	 */
	protected function get_term_score( $term ) {
		if ( ! is_object( $term ) || ! property_exists( $term, 'term_id' ) || ! property_exists( $term, 'taxonomy' ) ) {
			return '';
		}

		return $this->get_score_icon();
	}

	/**
	 * Create the score icon.
	 *
	 * @return string The score icon, or empty string.
	 */
	protected function get_score_icon() {
		$is_seo_enabled         = $this->get_is_seo_enabled();
		$is_readability_enabled = $this->get_is_readability_enabled();

		$indexable = $this->get_current_indexable();

		if ( $is_seo_enabled ) {
			return $this->score_icon_helper->for_seo( $indexable, 'adminbar-seo-score' )->present();
		}

		if ( $is_readability_enabled ) {
			return $this->score_icon_helper->for_readability( $indexable->readability_score, 'adminbar-seo-score' )
				->present();
		}

		return '';
	}

	/**
	 * Gets the URL to the main admin settings page.
	 *
	 * @return string Admin settings page URL.
	 */
	protected function get_settings_page_url() {
		return self_admin_url( 'admin.php?page=' . WPSEO_Admin::PAGE_IDENTIFIER );
	}

	/**
	 * Gets the notification counter if in a valid context.
	 *
	 * @param int $notification_count Number of notifications.
	 *
	 * @return string Notification counter markup, or empty string if not available.
	 */
	protected function get_notification_counter( $notification_count ) {
		/* translators: Hidden accessibility text; %s: number of notifications. */
		$counter_screen_reader_text = sprintf( _n( '%s notification', '%s notifications', $notification_count, 'wordpress-seo' ), number_format_i18n( $notification_count ) );

		return sprintf(
			' <div class="wp-core-ui wp-ui-notification yoast-issue-counter%s"><span class="yoast-issues-count" aria-hidden="true">%d</span><span class="screen-reader-text">%s</span></div>',
			( $notification_count ) ? '' : ' wpseo-no-adminbar-notifications',
			$notification_count,
			$counter_screen_reader_text
		);
	}

	/**
	 * Gets the notification popup if in a valid context.
	 *
	 * @return string Notification popup markup, or empty string if not available.
	 */
	protected function get_notification_popup() {
		$notification_center     = Yoast_Notification_Center::get();
		$new_notifications       = $notification_center->get_new_notifications();
		$new_notifications_count = count( $new_notifications );

		if ( ! $new_notifications_count ) {
			return '';
		}

		$notification = sprintf(
			_n(
				'There is a new notification.',
				'There are new notifications.',
				$new_notifications_count,
				'wordpress-seo'
			),
			$new_notifications_count
		);

		return '<div class="yoast-issue-added">' . $notification . '</div>';
	}

	/**
	 * Checks whether the current user can manage options in the current context.
	 *
	 * @return bool True if capabilities are sufficient, false otherwise.
	 */
	protected function can_manage_options() {
		return ( is_network_admin() && current_user_can( 'wpseo_manage_network_options' ) )
			|| ( ! is_network_admin() && WPSEO_Capability_Utils::current_user_can( 'wpseo_manage_options' ) );
	}

	/**
	 * Add submenu items to a menu item.
	 *
	 * @param array        $submenu_items Submenu items array.
	 * @param WP_Admin_Bar $wp_admin_bar  Admin bar object.
	 * @param string       $parent_id     Parent menu item ID.
	 *
	 * @return void
	 */
	protected function add_submenu_items( array $submenu_items, WP_Admin_Bar $wp_admin_bar, $parent_id ) {
		foreach ( $submenu_items as $menu_item ) {
			$menu_args = [
				'parent' => $parent_id,
				'id'     => $menu_item['id'],
				'title'  => $menu_item['title'],
				'href'   => $menu_item['href'],
				'meta'   => [ 'target' => '_blank' ],
			];
			$wp_admin_bar->add_menu( $menu_args );
		}
	}
}
