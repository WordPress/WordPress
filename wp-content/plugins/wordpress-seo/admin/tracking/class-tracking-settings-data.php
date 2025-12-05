<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Tracking
 */

/**
 * Collects anonymized settings data.
 */
class WPSEO_Tracking_Settings_Data implements WPSEO_Collection {

	/**
	 * The options that need to be anonymized before they can be sent elsewhere.
	 *
	 * @var array All of the option_names which need to be
	 * anonymized before they can be sent elsewhere.
	 */
	private $anonymous_settings = [
		'baiduverify',
		'googleverify',
		'msverify',
		'yandexverify',
		'ahrefsverify',
		'myyoast-oauth',
		'website_name',
		'alternate_website_name',
		'company_logo',
		'company_name',
		'company_alternate_name',
		'person_name',
		'person_logo',
		'person_logo_id',
		'company_logo_id',
		'facebook_site',
		'instagram_url',
		'linkedin_url',
		'myspace_url',
		'og_default_image',
		'og_default_image_id',
		'og_frontpage_title',
		'og_frontpage_desc',
		'og_frontpage_image',
		'og_frontpage_image_id',
		'open_graph_frontpage_title',
		'open_graph_frontpage_desc',
		'open_graph_frontpage_image',
		'open_graph_frontpage_image_id',
		'other_social_urls',
		'mastodon_url',
		'pinterest_url',
		'pinterestverify',
		'twitter_site',
		'youtube_url',
		'wikipedia_url',
		'semrush_tokens',
		'wincher_tokens',
		'wincher_website_id',
		'least_readability_ignore_list',
		'least_seo_score_ignore_list',
		'most_linked_ignore_list',
		'least_linked_ignore_list',
		'indexables_page_reading_list',
		'publishing_principles_id',
		'ownership_funding_info_id',
		'actionable_feedback_policy_id',
		'corrections_policy_id',
		'ethics_policy_id',
		'diversity_policy_id',
		'diversity_staffing_report_id',
	];

	/**
	 * The options we want to track.
	 *
	 * @var array The option_names for the options we want to track.
	 */
	private $include_list = [
		'ms_defaults_set',
		'version',
		'disableadvanced_meta',
		'ryte_indexability',
		'baiduverify',
		'googleverify',
		'ahrefsverify',
		'msverify',
		'yandexverify',
		'site_type',
		'has_multiple_authors',
		'environment_type',
		'content_analysis_active',
		'keyword_analysis_active',
		'inclusive_language_analysis_active',
		'enable_admin_bar_menu',
		'enable_cornerstone_content',
		'enable_xml_sitemap',
		'enable_text_link_counter',
		'show_onboarding_notice',
		'first_activated_on',
		'myyoast-oauth',
		'dynamic_permalinks',
		'website_name',
		'alternate_website_name',
		'company_logo',
		'company_name',
		'company_or_person',
		'person_name',
		'forcerewritetitle',
		'separator',
		'title-home-wpseo',
		'title-author-wpseo',
		'title-archive-wpseo',
		'title-search-wpseo',
		'title-404-wpseo',
		'metadesc-home-wpseo',
		'metadesc-author-wpseo',
		'metadesc-archive-wpseo',
		'rssbefore',
		'rssafter',
		'noindex-author-wpseo',
		'noindex-author-noposts-wpseo',
		'noindex-archive-wpseo',
		'disable-author',
		'disable-date',
		'disable-post_format',
		'disable-attachment',
		'breadcrumbs-404crumb',
		'breadcrumbs-display-blog-page',
		'breadcrumbs-boldlast',
		'breadcrumbs-archiveprefix',
		'breadcrumbs-enable',
		'breadcrumbs-home',
		'breadcrumbs-prefix',
		'breadcrumbs-searchprefix',
		'breadcrumbs-sep',
		'person_logo',
		'person_logo_id',
		'company_logo_id',
		'company_or_person_user_id',
		'stripcategorybase',
		'noindex-post',
		'display-metabox-pt-post',
		'noindex-page',
		'display-metabox-pt-page',
		'noindex-attachment',
		'display-metabox-pt-attachment',
		'display-metabox-tax-category',
		'noindex-tax-category',
		'display-metabox-tax-post_tag',
		'noindex-tax-post_tag',
		'display-metabox-tax-post_format',
		'noindex-tax-post_format',
		'taxonomy-category-ptparent',
		'taxonomy-post_tag-ptparent',
		'taxonomy-post_format-ptparent',
		'breadcrumbs-blog-remove',
		'hideeditbox-post',
		'hideeditbox-page',
		'hideeditbox-attachment',
		'hideeditbox-tax-category',
		'hideeditbox-tax-post_tag',
		'hideeditbox-tax-post_format',
		'facebook_site',
		'instagram_url',
		'linkedin_url',
		'myspace_url',
		'og_default_image',
		'og_default_image_id',
		'og_frontpage_title',
		'og_frontpage_desc',
		'og_frontpage_image',
		'og_frontpage_image_id',
		'open_graph_frontpage_title',
		'open_graph_frontpage_desc',
		'open_graph_frontpage_image',
		'open_graph_frontpage_image_id',
		'opengraph',
		'pinterest_url',
		'pinterestverify',
		'twitter',
		'twitter_site',
		'twitter_card_type',
		'youtube_url',
		'wikipedia_url',
		'mastodon_url',
		'indexables_indexing_completed',
		'semrush_integration_active',
		'semrush_tokens',
		'semrush_country_code',
		'enable_enhanced_slack_sharing',
		'enable_metabox_insights',
		'enable_link_suggestions',
		'enable_index_now',
		'enable_ai_generator',
		'workouts',
		'wincher_integration_active',
		'wincher_tokens',
		'wincher_website_id',
		'wincher_automatically_add_keyphrases',
		'first_time_install',
		'other_social_urls',
		'remove_feed_global',
		'remove_feed_global_comments',
		'remove_feed_post_comments',
		'remove_feed_authors',
		'remove_feed_categories',
		'remove_feed_tags',
		'remove_feed_custom_taxonomies',
		'remove_feed_post_types',
		'remove_feed_search',
		'remove_atom_rdf_feeds',
		'remove_shortlinks',
		'remove_rest_api_links',
		'remove_rsd_wlw_links',
		'remove_oembed_links',
		'remove_generator',
		'remove_emoji_scripts',
		'remove_powered_by_header',
		'remove_pingback_header',
		'clean_campaign_tracking_urls',
		'clean_permalinks',
		'clean_permalinks_extra_variables',
		'search_cleanup',
		'search_cleanup_emoji',
		'search_cleanup_patterns',
		'search_character_limit',
		'redirect_search_pretty_urls',
		'indexables_overview_state',
		'deny_search_crawling',
		'deny_wp_json_crawling',
		'deny_adsbot_crawling',
		'deny_ccbot_crawling',
		'deny_google_extended_crawling',
		'deny_gptbot_crawling',
		'last_known_no_unindexed',
		'site_kit_connected',
		'site_kit_tracking_setup_widget_loaded',
		'site_kit_tracking_first_interaction_stage',
		'site_kit_tracking_last_interaction_stage',
		'site_kit_tracking_setup_widget_temporarily_dismissed',
		'site_kit_tracking_setup_widget_permanently_dismissed',
		'google_site_kit_feature_enabled',
		'ai_free_sparks_started_on',
		'enable_llms_txt',
		'llms_txt_selection_mode',
		'configuration_finished_steps',
	];

	/**
	 * Returns the collection data.
	 *
	 * @return array The collection data.
	 */
	public function get() {
		/**
		 * Filter: 'wpseo_tracking_settings_include_list' - Allow filtering the settings included in tracking.
		 *
		 * @param string $include_list The list with included setting names.
		 */
		$this->include_list = apply_filters( 'wpseo_tracking_settings_include_list', $this->include_list );

		$options = WPSEO_Options::get_all();
		// Returns the settings of which the keys intersect with the values of the include list.
		$options = array_intersect_key( $options, array_flip( $this->include_list ) );

		return [
			'settings' => $this->anonymize_settings( $options ),
		];
	}

	/**
	 * Anonimizes the WPSEO_Options array by replacing all $anonymous_settings values to 'used'.
	 *
	 * @param array $settings The settings.
	 *
	 * @return array The anonymized settings.
	 */
	private function anonymize_settings( $settings ) {
		foreach ( $this->anonymous_settings as $setting ) {
			if ( ! empty( $settings[ $setting ] ) ) {
				$settings[ $setting ] = 'used';
			}
		}

		return $settings;
	}
}
