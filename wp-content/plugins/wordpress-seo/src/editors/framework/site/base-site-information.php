<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Framework\Site;

use Exception;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Introductions\Infrastructure\Wistia_Embed_Permission_Repository;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;
use Yoast\WP\SEO\Surfaces\Meta_Surface;

/**
 * The Base_Site_Information class.
 */
abstract class Base_Site_Information {

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * The wistia embed permission repository.
	 *
	 * @var Wistia_Embed_Permission_Repository
	 */
	protected $wistia_embed_permission_repository;

	/**
	 * The meta surface.
	 *
	 * @var Meta_Surface
	 */
	protected $meta;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	protected $product_helper;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The promotion manager.
	 *
	 * @var Promotion_Manager
	 */
	protected $promotion_manager;

	/**
	 * The constructor.
	 *
	 * @param Short_Link_Helper                  $short_link_helper                  The short link helper.
	 * @param Wistia_Embed_Permission_Repository $wistia_embed_permission_repository The wistia embed permission
	 *                                                                               repository.
	 * @param Meta_Surface                       $meta                               The meta surface.
	 * @param Product_Helper                     $product_helper                     The product helper.
	 * @param Options_Helper                     $options_helper                     The options helper.
	 * @param Promotion_Manager                  $promotion_manager                  The promotion manager.
	 */
	public function __construct(
		Short_Link_Helper $short_link_helper,
		Wistia_Embed_Permission_Repository $wistia_embed_permission_repository,
		Meta_Surface $meta,
		Product_Helper $product_helper,
		Options_Helper $options_helper,
		Promotion_Manager $promotion_manager
	) {
		$this->short_link_helper                  = $short_link_helper;
		$this->wistia_embed_permission_repository = $wistia_embed_permission_repository;
		$this->meta                               = $meta;
		$this->product_helper                     = $product_helper;
		$this->options_helper                     = $options_helper;
		$this->promotion_manager                  = $promotion_manager;
	}

	/**
	 * Returns site information that is the
	 *
	 * @return array<string, string|array<string, string>>
	 *
	 * @throws Exception If an invalid user ID is supplied to the wistia repository.
	 */
	public function get_site_information(): array {
		return [
			'adminUrl'                  => \admin_url( 'admin.php' ),
			'linkParams'                => $this->short_link_helper->get_query_params(),
			'pluginUrl'                 => \plugins_url( '', \WPSEO_FILE ),
			'wistiaEmbedPermission'     => $this->wistia_embed_permission_repository->get_value_for_user( \get_current_user_id() ),
			'site_name'                 => $this->meta->for_current_page()->site_name,
			'contentLocale'             => \get_locale(),
			'userLocale'                => \get_user_locale(),
			'isRtl'                     => \is_rtl(),
			'isPremium'                 => $this->product_helper->is_premium(),
			'siteIconUrl'               => \get_site_icon_url(),
			'showSocial'                => [
				'facebook' => $this->options_helper->get( 'opengraph', false ),
				'twitter'  => $this->options_helper->get( 'twitter', false ),
			],
			'sitewideSocialImage'       => $this->options_helper->get( 'og_default_image' ),
			// phpcs:ignore Generic.ControlStructures.DisallowYodaConditions -- Bug: squizlabs/PHP_CodeSniffer#2962.
			'isPrivateBlog'             => ( (string) \get_option( 'blog_public' ) ) === '0',
			'currentPromotions'         => $this->promotion_manager->get_current_promotions(),
		];
	}

	/**
	 * Returns site information that is the
	 *
	 * @return array<string, string|array<string, string|array<string, string>>>
	 *
	 * @throws Exception If an invalid user ID is supplied to the wistia repository.
	 */
	public function get_legacy_site_information(): array {
		return [
			'adminUrl'                  => \admin_url( 'admin.php' ),
			'linkParams'                => $this->short_link_helper->get_query_params(),
			'pluginUrl'                 => \plugins_url( '', \WPSEO_FILE ),
			'wistiaEmbedPermission'     => $this->wistia_embed_permission_repository->get_value_for_user( \get_current_user_id() ),
			'sitewideSocialImage'       => $this->options_helper->get( 'og_default_image' ),
			// phpcs:ignore Generic.ControlStructures.DisallowYodaConditions -- Bug: squizlabs/PHP_CodeSniffer#2962.
			'isPrivateBlog'             => ( (string) \get_option( 'blog_public' ) ) === '0',
			'currentPromotions'         => $this->promotion_manager->get_current_promotions(),
			'metabox'                   => [
				'site_name'     => $this->meta->for_current_page()->site_name,
				'contentLocale' => \get_locale(),
				'userLocale'    => \get_user_locale(),
				'isRtl'         => \is_rtl(),
				'isPremium'     => $this->product_helper->is_premium(),
				'siteIconUrl'   => \get_site_icon_url(),
				'showSocial'    => [
					'facebook' => $this->options_helper->get( 'opengraph', false ),
					'twitter'  => $this->options_helper->get( 'twitter', false ),
				],
			],
		];
	}
}
