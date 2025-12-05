<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Helpers\Language_Helper;
use Yoast\WP\SEO\Presenters\Admin\Alert_Presenter;

/**
 * Class for managing feature toggles.
 */
class Yoast_Feature_Toggles {

	/**
	 * Available feature toggles.
	 *
	 * @var array
	 */
	protected $toggles;

	/**
	 * Instance holder.
	 *
	 * @var self|null
	 */
	protected static $instance = null;

	/**
	 * Gets the main feature toggles manager instance used.
	 *
	 * This essentially works like a Singleton, but for its drawbacks does not restrict
	 * instantiation otherwise.
	 *
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Gets all available feature toggles.
	 *
	 * @return array List of sorted Yoast_Feature_Toggle instances.
	 */
	public function get_all() {
		if ( $this->toggles === null ) {
			$this->toggles = $this->load_toggles();
		}

		return $this->toggles;
	}

	/**
	 * Loads the available feature toggles.
	 *
	 * Also ensures that the toggles are all Yoast_Feature_Toggle instances and sorted by their order value.
	 *
	 * @return array List of sorted Yoast_Feature_Toggle instances.
	 */
	protected function load_toggles() {
		$xml_sitemap_extra = false;
		if ( WPSEO_Options::get( 'enable_xml_sitemap' ) ) {
			$xml_sitemap_extra = '<a href="' . esc_url( WPSEO_Sitemaps_Router::get_base_url( 'sitemap_index.xml' ) )
								. '" target="_blank">' . esc_html__( 'See the XML sitemap.', 'wordpress-seo' ) . '</a>';
		}

		$feature_toggles = [
			(object) [
				'name'            => __( 'SEO analysis', 'wordpress-seo' ),
				'setting'         => 'keyword_analysis_active',
				'label'           => __( 'The SEO analysis offers suggestions to improve the SEO of your text.', 'wordpress-seo' ),
				'read_more_label' => __( 'Learn how the SEO analysis can help you rank.', 'wordpress-seo' ),
				'read_more_url'   => 'https://yoa.st/2ak',
				'order'           => 10,
			],
			(object) [
				'name'            => __( 'Readability analysis', 'wordpress-seo' ),
				'setting'         => 'content_analysis_active',
				'label'           => __( 'The readability analysis offers suggestions to improve the structure and style of your text.', 'wordpress-seo' ),
				'read_more_label' => __( 'Discover why readability is important for SEO.', 'wordpress-seo' ),
				'read_more_url'   => 'https://yoa.st/2ao',
				'order'           => 20,
			],
			(object) [
				'name'                => __( 'Inclusive language analysis', 'wordpress-seo' ),
				'supported_languages' => Language_Helper::$languages_with_inclusive_language_support,
				'setting'             => 'inclusive_language_analysis_active',
				'label'               => __( 'The inclusive language analysis offers suggestions to write more inclusive copy.', 'wordpress-seo' ),
				'read_more_label'     => __( 'Discover why inclusive language is important for SEO.', 'wordpress-seo' ),
				'read_more_url'       => 'https://yoa.st/inclusive-language-features-free',
				'order'               => 25,
			],
			(object) [
				'name'            => __( 'Cornerstone content', 'wordpress-seo' ),
				'setting'         => 'enable_cornerstone_content',
				'label'           => __( 'The cornerstone content feature lets you to mark and filter cornerstone content on your website.', 'wordpress-seo' ),
				'read_more_label' => __( 'Find out how cornerstone content can help you improve your site structure.', 'wordpress-seo' ),
				'read_more_url'   => 'https://yoa.st/dashboard-help-cornerstone',
				'order'           => 30,
			],
			(object) [
				'name'            => __( 'Text link counter', 'wordpress-seo' ),
				'setting'         => 'enable_text_link_counter',
				'label'           => __( 'The text link counter helps you improve your site structure.', 'wordpress-seo' ),
				'read_more_label' => __( 'Find out how the text link counter can enhance your SEO.', 'wordpress-seo' ),
				'read_more_url'   => 'https://yoa.st/2aj',
				'order'           => 40,
			],
			(object) [
				'name'            => __( 'Insights', 'wordpress-seo' ),
				'setting'         => 'enable_metabox_insights',
				'label'           => __( 'Find relevant data about your content right in the Insights section in the Yoast SEO metabox. You’ll see what words you use most often and if they’re a match with your keywords! ', 'wordpress-seo' ),
				'read_more_label' => __( 'Find out how Insights can help you improve your content.', 'wordpress-seo' ),
				'read_more_url'   => 'https://yoa.st/4ew',
				'premium_url'     => 'https://yoa.st/2ai',
				'order'           => 41,
			],
			(object) [
				'name'               => __( 'Link suggestions', 'wordpress-seo' ),
				'premium'            => true,
				'setting'            => 'enable_link_suggestions',
				'label'              => __( 'Get relevant internal linking suggestions — while you’re writing! The link suggestions metabox shows a list of posts on your blog with similar content that might be interesting to link to. ', 'wordpress-seo' ),
				'read_more_label'    => __( 'Read more about how internal linking can improve your site structure.', 'wordpress-seo' ),
				'read_more_url'      => 'https://yoa.st/4ev',
				'premium_url'        => 'https://yoa.st/17g',
				'premium_upsell_url' => 'https://yoa.st/get-link-suggestions',
				'order'              => 42,
			],
			(object) [
				'name'            => __( 'XML sitemaps', 'wordpress-seo' ),
				'setting'         => 'enable_xml_sitemap',
				/* translators: %s: Yoast SEO */
				'label'           => sprintf( __( 'Enable the XML sitemaps that %s generates.', 'wordpress-seo' ), 'Yoast SEO' ),
				'read_more_label' => __( 'Read why XML Sitemaps are important for your site.', 'wordpress-seo' ),
				'read_more_url'   => 'https://yoa.st/2a-',
				'extra'           => $xml_sitemap_extra,
				'after'           => $this->sitemaps_toggle_after(),
				'order'           => 60,
			],
			(object) [
				'name'    => __( 'Admin bar menu', 'wordpress-seo' ),
				'setting' => 'enable_admin_bar_menu',
				/* translators: 1: Yoast SEO */
				'label'   => sprintf( __( 'The %1$s admin bar menu contains useful links to third-party tools for analyzing pages and makes it easy to see if you have new notifications.', 'wordpress-seo' ), 'Yoast SEO' ),
				'order'   => 80,
			],
			(object) [
				'name'    => __( 'Security: no advanced or schema settings for authors', 'wordpress-seo' ),
				'setting' => 'disableadvanced_meta',
				'label'   => sprintf(
				/* translators: 1: Yoast SEO, 2: translated version of "Off" */
					__( 'The advanced section of the %1$s meta box allows a user to remove posts from the search results or change the canonical. The settings in the schema tab allows a user to change schema meta data for a post. These are things you might not want any author to do. That\'s why, by default, only editors and administrators can do this. Setting to "%2$s" allows all users to change these settings.', 'wordpress-seo' ),
					'Yoast SEO',
					__( 'Off', 'wordpress-seo' )
				),
				'order'   => 90,
			],
			(object) [
				'name'            => __( 'Usage tracking', 'wordpress-seo' ),
				'label'           => __( 'Usage tracking', 'wordpress-seo' ),
				'setting'         => 'tracking',
				'read_more_label' => sprintf(
				/* translators: 1: Yoast SEO */
					__( 'Allow us to track some data about your site to improve our plugin.', 'wordpress-seo' ),
					'Yoast SEO'
				),
				'read_more_url'   => 'https://yoa.st/usage-tracking-2',
				'order'           => 95,
			],
			(object) [
				'name'    => __( 'REST API: Head endpoint', 'wordpress-seo' ),
				'setting' => 'enable_headless_rest_endpoints',
				'label'   => sprintf(
				/* translators: 1: Yoast SEO */
					__( 'This %1$s REST API endpoint gives you all the metadata you need for a specific URL. This will make it very easy for headless WordPress sites to use %1$s for all their SEO meta output.', 'wordpress-seo' ),
					'Yoast SEO'
				),
				'order'   => 100,
			],
			(object) [
				'name'            => __( 'Enhanced Slack sharing', 'wordpress-seo' ),
				'setting'         => 'enable_enhanced_slack_sharing',
				'label'           => __( 'This adds an author byline and reading time estimate to the article’s snippet when shared on Slack.', 'wordpress-seo' ),
				'read_more_label' => __( 'Find out how a rich snippet can improve visibility and click-through-rate.', 'wordpress-seo' ),
				'read_more_url'   => 'https://yoa.st/help-slack-share',
				'order'           => 105,
			],
			(object) [
				'name'               => __( 'IndexNow', 'wordpress-seo' ),
				'premium'            => true,
				'setting'            => 'enable_index_now',
				'label'              => __( 'Automatically ping search engines like Bing and Yandex whenever you publish, update or delete a post.', 'wordpress-seo' ),
				'read_more_label'    => __( 'Find out how IndexNow can help your site.', 'wordpress-seo' ),
				'read_more_url'      => 'https://yoa.st/index-now-read-more',
				'premium_url'        => 'https://yoa.st/index-now-feature',
				'premium_upsell_url' => 'https://yoa.st/get-indexnow',
				'order'              => 110,
			],
			(object) [
				'name'               => __( 'AI title & description generator', 'wordpress-seo' ),
				'premium'            => true,
				'setting'            => 'enable_ai_generator',
				'label'              => __( 'Use the power of Yoast AI to automatically generate compelling titles and descriptions for your posts and pages.', 'wordpress-seo' ),
				'read_more_label'    => __( 'Learn more', 'wordpress-seo' ),
				'read_more_url'      => 'https://yoa.st/ai-generator-read-more',
				'premium_url'        => 'https://yoa.st/ai-generator-feature',
				'premium_upsell_url' => 'https://yoa.st/get-ai-generator',
				'order'              => 115,
			],
		];

		/**
		 * Filter to add feature toggles from add-ons.
		 *
		 * @param array $feature_toggles Array with feature toggle objects where each object
		 *                               should have a `name`, `setting` and `label` property.
		 */
		$feature_toggles = apply_filters( 'wpseo_feature_toggles', $feature_toggles );

		$feature_toggles = array_map( [ $this, 'ensure_toggle' ], $feature_toggles );
		usort( $feature_toggles, [ $this, 'sort_toggles_callback' ] );

		return $feature_toggles;
	}

	/**
	 * Returns html for a warning that core sitemaps are enabled when yoast seo sitemaps are disabled.
	 *
	 * @return string HTML string for the warning.
	 */
	protected function sitemaps_toggle_after() {
		$out   = '<div id="yoast-seo-sitemaps-disabled-warning" style="display:none;">';
		$alert = new Alert_Presenter(
		/* translators: %1$s: expands to an opening anchor tag, %2$s: expands to a closing anchor tag */
			sprintf( esc_html__( 'Disabling Yoast SEO\'s XML sitemaps will not disable WordPress\' core sitemaps. In some cases, this %1$s may result in SEO errors on your site%2$s. These may be reported in Google Search Console and other tools.', 'wordpress-seo' ), '<a target="_blank" href="' . WPSEO_Shortlinker::get( 'https://yoa.st/44z' ) . '">', '</a>' ),
			'warning'
		);
		$out .= $alert->present();
		$out .= '</div>';

		return $out;
	}

	/**
	 * Ensures that the passed value is a Yoast_Feature_Toggle.
	 *
	 * @param Yoast_Feature_Toggle|object|array $toggle_data Feature toggle instance, or raw object or array
	 *                                                       containing feature toggle data.
	 *
	 * @return Yoast_Feature_Toggle Feature toggle instance based on $toggle_data.
	 */
	protected function ensure_toggle( $toggle_data ) {
		if ( $toggle_data instanceof Yoast_Feature_Toggle ) {
			return $toggle_data;
		}

		if ( is_object( $toggle_data ) ) {
			$toggle_data = get_object_vars( $toggle_data );
		}

		return new Yoast_Feature_Toggle( $toggle_data );
	}

	/**
	 * Callback for sorting feature toggles by their order.
	 *
	 * {@internal Once the minimum PHP version goes up to PHP 7.0, the logic in the function
	 * can be replaced with the spaceship operator `<=>`.}
	 *
	 * @param Yoast_Feature_Toggle $feature_a Feature A.
	 * @param Yoast_Feature_Toggle $feature_b Feature B.
	 *
	 * @return int An integer less than, equal to, or greater than zero indicating respectively
	 *             that feature A is considered to be less than, equal to, or greater than feature B.
	 */
	protected function sort_toggles_callback( Yoast_Feature_Toggle $feature_a, Yoast_Feature_Toggle $feature_b ) {
		return ( $feature_a->order - $feature_b->order );
	}
}
