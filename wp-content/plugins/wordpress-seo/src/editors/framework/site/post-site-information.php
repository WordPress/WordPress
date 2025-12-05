<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Framework\Site;

use Yoast\WP\SEO\Actions\Alert_Dismissal_Action;
use Yoast\WP\SEO\Alerts\Infrastructure\Default_SEO_Data\Default_SEO_Data_Collector;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Introductions\Infrastructure\Wistia_Embed_Permission_Repository;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;
use Yoast\WP\SEO\Surfaces\Meta_Surface;

/**
 * The Post_Site_Information class.
 */
class Post_Site_Information extends Base_Site_Information {

	/**
	 * The permalink.
	 *
	 * @var string
	 */
	private $permalink;

	/**
	 * The alert dismissal action.
	 *
	 * @var Alert_Dismissal_Action
	 */
	private $alert_dismissal_action;

	/**
	 * The default SEO data collector.
	 *
	 * @var Default_SEO_Data_Collector
	 */
	private $default_seo_data_collector;

	/**
	 * Constructs the class.
	 *
	 * @param Short_Link_Helper                  $short_link_helper                  The short link helper.
	 * @param Wistia_Embed_Permission_Repository $wistia_embed_permission_repository The wistia embed permission
	 *                                                                               repository.
	 * @param Meta_Surface                       $meta                               The meta surface.
	 * @param Product_Helper                     $product_helper                     The product helper.
	 * @param Alert_Dismissal_Action             $alert_dismissal_action             The alert dismissal action.
	 * @param Options_Helper                     $options_helper                     The options helper.
	 * @param Promotion_Manager                  $promotion_manager                  The promotion manager.
	 * @param Default_SEO_Data_Collector         $default_seo_data_collector         The default SEO data collector.
	 *
	 * @return void
	 */
	public function __construct(
		Short_Link_Helper $short_link_helper,
		Wistia_Embed_Permission_Repository $wistia_embed_permission_repository,
		Meta_Surface $meta,
		Product_Helper $product_helper,
		Alert_Dismissal_Action $alert_dismissal_action,
		Options_Helper $options_helper,
		Promotion_Manager $promotion_manager,
		Default_SEO_Data_Collector $default_seo_data_collector
	) {
		parent::__construct( $short_link_helper, $wistia_embed_permission_repository, $meta, $product_helper, $options_helper, $promotion_manager );
		$this->alert_dismissal_action     = $alert_dismissal_action;
		$this->default_seo_data_collector = $default_seo_data_collector;
	}

	/**
	 * Sets the permalink.
	 *
	 * @param string $permalink The permalink.
	 *
	 * @return void
	 */
	public function set_permalink( string $permalink ): void {
		$this->permalink = $permalink;
	}

	/**
	 * Returns post specific site information together with the generic site information.
	 *
	 * @return array<string, string|array<string, string>>
	 */
	public function get_legacy_site_information(): array {
		$dismissed_alerts = $this->alert_dismissal_action->all_dismissed();

		$data = [
			'dismissedAlerts'              => $dismissed_alerts,
			'webinarIntroBlockEditorUrl'   => $this->short_link_helper->get( 'https://yoa.st/webinar-intro-block-editor' ),
			'metabox'                      => [
				'search_url'    => $this->search_url(),
				'post_edit_url' => $this->edit_url(),
				'base_url'      => $this->base_url_for_js(),
			],
			'isRecentTitlesDefault'        => \count( $this->default_seo_data_collector->get_posts_with_default_seo_title() ) > 4,
			'isRecentDescriptionsDefault'  => \count( $this->default_seo_data_collector->get_posts_with_default_seo_description() ) > 4,
		];

		return \array_merge_recursive( $data, parent::get_legacy_site_information() );
	}

	/**
	 * Returns post specific site information together with the generic site information.
	 *
	 * @return array<string, string|string[]>
	 */
	public function get_site_information(): array {
		$dismissed_alerts = $this->alert_dismissal_action->all_dismissed();

		$data = [
			'dismissedAlerts'             => $dismissed_alerts,
			'webinarIntroBlockEditorUrl'  => $this->short_link_helper->get( 'https://yoa.st/webinar-intro-block-editor' ),
			'search_url'                  => $this->search_url(),
			'post_edit_url'               => $this->edit_url(),
			'base_url'                    => $this->base_url_for_js(),
			'isRecentTitlesDefault'       => \count( $this->default_seo_data_collector->get_posts_with_default_seo_title() ) > 4,
			'isRecentDescriptionsDefault' => \count( $this->default_seo_data_collector->get_posts_with_default_seo_description() ) > 4,
		];

		return \array_merge( $data, parent::get_site_information() );
	}

	/**
	 * Returns the url to search for keyword for the post.
	 *
	 * @return string
	 */
	private function search_url(): string {
		return \admin_url( 'edit.php?seo_kw_filter={keyword}' );
	}

	/**
	 * Returns the url to edit the taxonomy.
	 *
	 * @return string
	 */
	private function edit_url(): string {
		return \admin_url( 'post.php?post={id}&action=edit' );
	}

	/**
	 * Returns a base URL for use in the JS, takes permalink structure into account.
	 *
	 * @return string
	 */
	private function base_url_for_js(): string {
		global $pagenow;

		// The default base is the home_url.
		$base_url = \home_url( '/', null );

		if ( $pagenow === 'post-new.php' ) {
			return $base_url;
		}

		// If %postname% is the last tag, just strip it and use that as a base.
		if ( \preg_match( '#%postname%/?$#', $this->permalink ) === 1 ) {
			$base_url = \preg_replace( '#%postname%/?$#', '', $this->permalink );
		}

		// If %pagename% is the last tag, just strip it and use that as a base.
		if ( \preg_match( '#%pagename%/?$#', $this->permalink ) === 1 ) {
			$base_url = \preg_replace( '#%pagename%/?$#', '', $this->permalink );
		}

		return $base_url;
	}
}
