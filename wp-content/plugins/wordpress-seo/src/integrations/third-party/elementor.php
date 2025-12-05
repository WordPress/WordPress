<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use WP_Post;
use WP_Screen;
use WPSEO_Admin_Asset_Manager;
use WPSEO_Admin_Recommended_Replace_Vars;
use WPSEO_Meta;
use WPSEO_Metabox_Analysis_Inclusive_Language;
use WPSEO_Metabox_Analysis_Readability;
use WPSEO_Metabox_Analysis_SEO;
use WPSEO_Metabox_Formatter;
use WPSEO_Post_Metabox_Formatter;
use WPSEO_Replace_Vars;
use WPSEO_Utils;
use Yoast\WP\SEO\Conditionals\Third_Party\Elementor_Edit_Conditional;
use Yoast\WP\SEO\Editors\Application\Site\Website_Information_Repository;
use Yoast\WP\SEO\Elementor\Infrastructure\Request_Post;
use Yoast\WP\SEO\Helpers\Capability_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Meta_Fields_Presenter;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Integrates the Yoast SEO metabox in the Elementor editor.
 */
class Elementor implements Integration_Interface {

	/**
	 * Represents the post.
	 *
	 * @var WP_Post|null
	 */
	protected $post;

	/**
	 * Represents the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * Represents the options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * Represents the capability helper.
	 *
	 * @var Capability_Helper
	 */
	protected $capability;

	/**
	 * Holds the Request_Post.
	 *
	 * @var Request_Post
	 */
	private $request_post;

	/**
	 * Holds whether the socials are enabled.
	 *
	 * @var bool
	 */
	protected $social_is_enabled;

	/**
	 * Holds whether the advanced settings are enabled.
	 *
	 * @var bool
	 */
	protected $is_advanced_metadata_enabled;

	/**
	 * Helper to determine whether or not the SEO analysis is enabled.
	 *
	 * @var WPSEO_Metabox_Analysis_SEO
	 */
	protected $seo_analysis;

	/**
	 * Helper to determine whether or not the readability analysis is enabled.
	 *
	 * @var WPSEO_Metabox_Analysis_Readability
	 */
	protected $readability_analysis;

	/**
	 * Helper to determine whether or not the inclusive language analysis is enabled.
	 *
	 * @var WPSEO_Metabox_Analysis_Inclusive_Language
	 */
	protected $inclusive_language_analysis;

	/**
	 * Holds the promotion manager.
	 *
	 * @var Promotion_Manager
	 */
	protected $promotion_manager;

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Elementor_Edit_Conditional::class ];
	}

	/**
	 * Constructor.
	 *
	 * @param WPSEO_Admin_Asset_Manager $asset_manager The asset manager.
	 * @param Options_Helper            $options       The options helper.
	 * @param Capability_Helper         $capability    The capability helper.
	 * @param Request_Post              $request_post  The Request_Post.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		Options_Helper $options,
		Capability_Helper $capability,
		Request_Post $request_post
	) {
		$this->asset_manager = $asset_manager;
		$this->options       = $options;
		$this->capability    = $capability;
		$this->request_post  = $request_post;

		$this->seo_analysis                 = new WPSEO_Metabox_Analysis_SEO();
		$this->readability_analysis         = new WPSEO_Metabox_Analysis_Readability();
		$this->inclusive_language_analysis  = new WPSEO_Metabox_Analysis_Inclusive_Language();
		$this->social_is_enabled            = $this->options->get( 'opengraph', false ) || $this->options->get( 'twitter', false );
		$this->is_advanced_metadata_enabled = $this->capability->current_user_can( 'wpseo_edit_advanced_metadata' ) || $this->options->get( 'disableadvanced_meta' ) === false;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wp_ajax_wpseo_elementor_save', [ $this, 'save_postdata' ] );

		// We need to delay the post type lookup to give other plugins a chance to register custom post types.
		\add_action( 'init', [ $this, 'register_elementor_hooks' ], \PHP_INT_MAX );
	}

	/**
	 * Registers our Elementor hooks.
	 * This is done for pages with metabox on page load and not on ajax request.
	 *
	 * @return void
	 */
	public function register_elementor_hooks() {
		if ( $this->get_metabox_post() === null || ! $this->display_metabox( $this->get_metabox_post()->post_type ) ) {
			return;
		}

		\add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'init' ] );
		\add_action( 'elementor/editor/footer', [ $this, 'start_output_buffering' ], 0 );
		\add_action( 'elementor/editor/footer', [ $this, 'inject_yoast_tab' ], 999 );
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function init() {
		$this->asset_manager->register_assets();
		$this->enqueue();
		$this->render_hidden_fields();
	}

	/**
	 * Start capturing buffer.
	 *
	 * @return void
	 */
	public function start_output_buffering() {
		\ob_start();
	}

	/**
	 * Injects the Yoast SEO tab into the Elements panel of the Elementor editor.
	 *
	 * @return void
	 */
	public function inject_yoast_tab() {
		$output = \ob_get_clean();

		// If the buffer is empty or the call failed, bail out.
		if ( empty( $output ) ) {
			return;
		}

		$search  = '/(<(div|button) class="elementor-component-tab elementor-panel-navigation-tab" data-tab="global">.*<\/(div|button)>)/m';
		$replace = '${1}<${2} class="elementor-component-tab elementor-panel-navigation-tab" data-tab="yoast-seo-tab">Yoast SEO</${2}>';

		$modified_output = \preg_replace( $search, $replace, $output );

		// Check if preg_replace failed. If so, fallback to original output.
		if ( $modified_output === null ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Already escaped output.
			echo $output;
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Already escaped output.
		echo $modified_output;
	}

	// Below is mostly copied from `class-metabox.php`. That constructor has side-effects we do not need.

	/**
	 * Determines whether the metabox should be shown for the passed identifier.
	 *
	 * By default, the check is done for post types, but can also be used for taxonomies.
	 *
	 * @param string|null $identifier The identifier to check.
	 * @param string      $type       The type of object to check. Defaults to post_type.
	 *
	 * @return bool Whether the metabox should be displayed.
	 */
	public function display_metabox( $identifier = null, $type = 'post_type' ) {
		return WPSEO_Utils::is_metabox_active( $identifier, $type );
	}

	/**
	 * Saves the WP SEO metadata for posts.
	 *
	 * Outputs JSON via wp_send_json then stops code execution.
	 *
	 * {@internal $_POST parameters are validated via sanitize_post_meta().}}
	 *
	 * @return void
	 */
	public function save_postdata() {
		global $post;

		if ( ! isset( $_POST['post_id'] ) || ! \is_string( $_POST['post_id'] ) ) {
			\wp_send_json_error( 'Bad Request', 400 );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: No sanitization needed because we cast to an integer.
		$post_id = (int) \wp_unslash( $_POST['post_id'] );

		if ( $post_id <= 0 ) {
			\wp_send_json_error( 'Bad Request', 400 );
		}

		if ( ! \current_user_can( 'edit_post', $post_id ) ) {
			\wp_send_json_error( 'Forbidden', 403 );
		}

		\check_ajax_referer( 'wpseo_elementor_save', '_wpseo_elementor_nonce' );

		// Bail if this is a multisite installation and the site has been switched.
		if ( \is_multisite() && \ms_is_switched() ) {
			\wp_send_json_error( 'Switched multisite', 409 );
		}

		\clean_post_cache( $post_id );
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- To setup the post we need to do this explicitly.
		$post = \get_post( $post_id );

		if ( ! \is_object( $post ) ) {
			// Non-existent post.
			\wp_send_json_error( 'Post not found', 400 );
		}

		\do_action( 'wpseo_save_compare_data', $post );

		// Initialize meta, amongst other things it registers sanitization.
		WPSEO_Meta::init();

		$social_fields = [];
		if ( $this->social_is_enabled ) {
			$social_fields = WPSEO_Meta::get_meta_field_defs( 'social', $post->post_type );
		}

		// The below methods use the global post so make sure it is setup.
		\setup_postdata( $post );
		$meta_boxes = \apply_filters( 'wpseo_save_metaboxes', [] );
		$meta_boxes = \array_merge(
			$meta_boxes,
			WPSEO_Meta::get_meta_field_defs( 'general', $post->post_type ),
			WPSEO_Meta::get_meta_field_defs( 'advanced', $post->post_type ),
			$social_fields,
			WPSEO_Meta::get_meta_field_defs( 'schema', $post->post_type )
		);

		foreach ( $meta_boxes as $key => $meta_box ) {
			// If analysis is disabled remove that analysis score value from the DB.
			if ( $this->is_meta_value_disabled( $key ) ) {
				WPSEO_Meta::delete( $key, $post_id );
				continue;
			}

			$data       = null;
			$field_name = WPSEO_Meta::$form_prefix . $key;

			if ( $meta_box['type'] === 'checkbox' ) {
				$data = isset( $_POST[ $field_name ] ) ? 'on' : 'off';
			}
			else {
				if ( isset( $_POST[ $field_name ] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Sanitized through sanitize_post_meta.
					$data = \wp_unslash( $_POST[ $field_name ] );

					// For multi-select.
					if ( \is_array( $data ) ) {
						$data = \array_map( [ 'WPSEO_Utils', 'sanitize_text_field' ], $data );
					}

					if ( \is_string( $data ) ) {
						$data = ( $key !== 'canonical' ) ? WPSEO_Utils::sanitize_text_field( $data ) : WPSEO_Utils::sanitize_url( $data );
					}
				}

				// Reset options when no entry is present with multiselect - only applies to `meta-robots-adv` currently.
				if ( ! isset( $_POST[ $field_name ] ) && ( $meta_box['type'] === 'multiselect' ) ) {
					$data = [];
				}
			}

			if ( $data !== null ) {
				WPSEO_Meta::set_value( $key, $data, $post_id );
			}
		}

		if ( isset( $_POST[ WPSEO_Meta::$form_prefix . 'slug' ] ) && \is_string( $_POST[ WPSEO_Meta::$form_prefix . 'slug' ] ) ) {
			$slug = \sanitize_title( \wp_unslash( $_POST[ WPSEO_Meta::$form_prefix . 'slug' ] ) );
			if ( $post->post_name !== $slug ) {
				$post_array              = $post->to_array();
				$post_array['post_name'] = $slug;

				$save_successful = \wp_insert_post( $post_array );
				if ( \is_wp_error( $save_successful ) ) {
					\wp_send_json_error( 'Slug not saved', 400 );
				}

				// Update the post object to ensure we have the actual slug.
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Updating the post is needed to get the current slug.
				$post = \get_post( $post_id );
				if ( ! \is_object( $post ) ) {
					\wp_send_json_error( 'Updated slug not found', 400 );
				}
			}
		}

		\do_action( 'wpseo_saved_postdata' );

		// Output the slug, because it is processed by WP and we need the actual slug again.
		\wp_send_json_success( [ 'slug' => $post->post_name ] );
	}

	/**
	 * Determines if the given meta value key is disabled.
	 *
	 * @param string $key The key of the meta value.
	 *
	 * @return bool Whether the given meta value key is disabled.
	 */
	public function is_meta_value_disabled( $key ) {
		if ( $key === 'linkdex' && ! $this->seo_analysis->is_enabled() ) {
			return true;
		}

		if ( $key === 'content_score' && ! $this->readability_analysis->is_enabled() ) {
			return true;
		}

		if ( $key === 'inclusive_language_score' && ! $this->inclusive_language_analysis->is_enabled() ) {
			return true;
		}

		return false;
	}

	/**
	 * Enqueues all the needed JS and CSS.
	 *
	 * @return void
	 */
	public function enqueue() {
		$post_id = \get_queried_object_id();
		if ( empty( $post_id ) ) {
			$post_id = 0;
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			if ( isset( $_GET['post'] ) && \is_string( $_GET['post'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Recommended -- Reason: No sanitization needed because we cast to an integer,We are not processing form information.
				$post_id = (int) \wp_unslash( $_GET['post'] );
			}
		}

		if ( $post_id !== 0 ) {
			// Enqueue files needed for upload functionality.
			\wp_enqueue_media( [ 'post' => $post_id ] );
		}

		$this->asset_manager->enqueue_style( 'admin-global' );
		$this->asset_manager->enqueue_style( 'metabox-css' );
		if ( $this->readability_analysis->is_enabled() ) {
			$this->asset_manager->enqueue_style( 'scoring' );
		}
		$this->asset_manager->enqueue_style( 'monorepo' );
		$this->asset_manager->enqueue_style( 'admin-css' );
		$this->asset_manager->enqueue_style( 'ai-generator' );
		$this->asset_manager->enqueue_style( 'elementor' );

		$this->asset_manager->enqueue_script( 'admin-global' );
		$this->asset_manager->enqueue_script( 'elementor' );

		$this->asset_manager->localize_script( 'elementor', 'wpseoAdminGlobalL10n', \YoastSEO()->helpers->wincher->get_admin_global_links() );
		$this->asset_manager->localize_script( 'elementor', 'wpseoAdminL10n', WPSEO_Utils::get_admin_l10n() );
		$this->asset_manager->localize_script( 'elementor', 'wpseoFeaturesL10n', WPSEO_Utils::retrieve_enabled_features() );

		$plugins_script_data = [
			'replaceVars' => [
				'replace_vars'             => $this->get_replace_vars(),
				'recommended_replace_vars' => $this->get_recommended_replace_vars(),
				'hidden_replace_vars'      => $this->get_hidden_replace_vars(),
				'scope'                    => $this->determine_scope(),
				'has_taxonomies'           => $this->current_post_type_has_taxonomies(),
			],
			'shortcodes'  => [
				'wpseo_shortcode_tags'          => $this->get_valid_shortcode_tags(),
				'wpseo_filter_shortcodes_nonce' => \wp_create_nonce( 'wpseo-filter-shortcodes' ),
			],
		];

		$worker_script_data = [
			'url'                     => \YoastSEO()->helpers->asset->get_asset_url( 'yoast-seo-analysis-worker' ),
			'dependencies'            => \YoastSEO()->helpers->asset->get_dependency_urls_by_handle( 'yoast-seo-analysis-worker' ),
			'keywords_assessment_url' => \YoastSEO()->helpers->asset->get_asset_url( 'yoast-seo-used-keywords-assessment' ),
			'log_level'               => WPSEO_Utils::get_analysis_worker_log_level(),
			// We need to make the feature flags separately available inside of the analysis web worker.
			'enabled_features'        => WPSEO_Utils::retrieve_enabled_features(),
		];

		$permalink        = $this->get_permalink();
		$page_on_front    = (int) \get_option( 'page_on_front' );
		$homepage_is_page = \get_option( 'show_on_front' ) === 'page';
		$is_front_page    = $homepage_is_page && $page_on_front === $post_id;

		$script_data = [
			'metabox'                   => $this->get_metabox_script_data( $permalink ),
			'isPost'                    => true,
			'isBlockEditor'             => WP_Screen::get()->is_block_editor(),
			'isElementorEditor'         => true,
			'isAlwaysIntroductionV2'    => $this->is_elementor_version_compatible_with_introduction_v2(),
			'postStatus'                => \get_post_status( $post_id ),
			'postType'                  => \get_post_type( $post_id ),
			'analysis'                  => [
				'plugins' => $plugins_script_data,
				'worker'  => $worker_script_data,
			],
			'usedKeywordsNonce'         => \wp_create_nonce( 'wpseo-keyword-usage-and-post-types' ),
			'isFrontPage'               => $is_front_page,
		];

		/**
		 * The website information repository.
		 *
		 * @var Website_Information_Repository $repo
		 */
		$repo             = \YoastSEO()->classes->get( Website_Information_Repository::class );
		$site_information = $repo->get_post_site_information();
		$site_information->set_permalink( $permalink );
		$script_data = \array_merge_recursive( $site_information->get_legacy_site_information(), $script_data );

		$this->asset_manager->localize_script( 'elementor', 'wpseoScriptData', $script_data );
	}

	/**
	 * Checks whether the current Elementor version is compatible with our introduction v2.
	 *
	 * In version 3.30.0, Elementor removed the experimental flag for the editor v2.
	 * Resulting in the editor v2 being the default.
	 *
	 * @return bool Whether the Elementor version is compatible with introduction v2.
	 */
	private function is_elementor_version_compatible_with_introduction_v2(): bool {
		if ( ! \defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}

		// Take the semver version from their version string.
		$matches = [];
		$version = ( \preg_match( '/^([0-9]+.[0-9]+.[0-9]+)/', \ELEMENTOR_VERSION, $matches ) > 0 ) ? $matches[1] : \ELEMENTOR_VERSION;

		// Check if the version is 3.30.0 or higher. This is where the editor v2 was taken out of the experimental into the default state.
		return \version_compare( $version, '3.30.0', '>=' );
	}

	/**
	 * Renders the metabox hidden fields.
	 *
	 * @return void
	 */
	protected function render_hidden_fields() {
		// Wrap in a form with an action and post_id for the submit.
		\printf(
			'<form id="yoast-form" method="post" action="%1$s"><input type="hidden" name="action" value="wpseo_elementor_save" /><input type="hidden" id="post_ID" name="post_id" value="%2$s" />',
			\esc_url( \admin_url( 'admin-ajax.php' ) ),
			\esc_attr( $this->get_metabox_post()->ID )
		);

		\wp_nonce_field( 'wpseo_elementor_save', '_wpseo_elementor_nonce' );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Meta_Fields_Presenter->present is considered safe.
		echo new Meta_Fields_Presenter( $this->get_metabox_post(), 'general' );

		if ( $this->is_advanced_metadata_enabled ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Meta_Fields_Presenter->present is considered safe.
			echo new Meta_Fields_Presenter( $this->get_metabox_post(), 'advanced' );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Meta_Fields_Presenter->present is considered safe.
		echo new Meta_Fields_Presenter( $this->get_metabox_post(), 'schema', $this->get_metabox_post()->post_type );

		if ( $this->social_is_enabled ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Meta_Fields_Presenter->present is considered safe.
			echo new Meta_Fields_Presenter( $this->get_metabox_post(), 'social' );
		}

		\printf(
			'<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />',
			\esc_attr( WPSEO_Meta::$form_prefix . 'slug' ),
			/**
			 * It is important that this slug value is the same as in the database.
			 * If the DB value is empty we can auto-generate a slug.
			 * But if not empty, we should not touch it anymore.
			 */
			\esc_attr( $this->get_metabox_post()->post_name )
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output should be escaped in the filter.
		echo \apply_filters( 'wpseo_elementor_hidden_fields', '' );

		echo '</form>';
	}

	/**
	 * Returns post in metabox context.
	 *
	 * @return WP_Post|null
	 */
	protected function get_metabox_post() {
		if ( $this->post !== null ) {
			return $this->post;
		}

		$this->post = $this->request_post->get_post();

		return $this->post;
	}

	/**
	 * Passes variables to js for use with the post-scraper.
	 *
	 * @param string $permalink The permalink.
	 *
	 * @return array
	 */
	protected function get_metabox_script_data( $permalink ) {
		$post_formatter = new WPSEO_Metabox_Formatter(
			new WPSEO_Post_Metabox_Formatter( $this->get_metabox_post(), [], $permalink )
		);

		$values = $post_formatter->get_values();

		/** This filter is documented in admin/filters/class-cornerstone-filter.php. */
		$post_types = \apply_filters( 'wpseo_cornerstone_post_types', \YoastSEO()->helpers->post_type->get_accessible_post_types() );
		if ( $values['cornerstoneActive'] && ! \in_array( $this->get_metabox_post()->post_type, $post_types, true ) ) {
			$values['cornerstoneActive'] = false;
		}

		$values['elementorMarkerStatus'] = $this->is_highlighting_available() ? 'enabled' : 'hidden';

		return $values;
	}

	/**
	 * Gets the permalink.
	 *
	 * @return string
	 */
	protected function get_permalink(): string {
		$permalink = '';

		if ( \is_object( $this->get_metabox_post() ) ) {
			$permalink = \get_sample_permalink( $this->get_metabox_post()->ID );
			$permalink = $permalink[0];
		}

		return $permalink;
	}

	/**
	 * Checks whether the highlighting functionality is available for Elementor:
	 * - in Free it's always available (as an upsell).
	 * - in Premium it's available as long as the version is 21.8-RC0 or above.
	 *
	 * @return bool Whether the highlighting functionality is available.
	 */
	private function is_highlighting_available() {
		$is_premium      = \YoastSEO()->helpers->product->is_premium();
		$premium_version = \YoastSEO()->helpers->product->get_premium_version();

		return ! $is_premium || \version_compare( $premium_version, '21.8-RC0', '>=' );
	}

	/**
	 * Prepares the replace vars for localization.
	 *
	 * @return array Replace vars.
	 */
	protected function get_replace_vars() {
		$cached_replacement_vars = [];

		$vars_to_cache = [
			'date',
			'id',
			'sitename',
			'sitedesc',
			'sep',
			'page',
			'currentyear',
			'currentdate',
			'currentmonth',
			'currentday',
			'tag',
			'category',
			'category_title',
			'primary_category',
			'pt_single',
			'pt_plural',
			'modified',
			'name',
			'user_description',
			'pagetotal',
			'pagenumber',
			'post_year',
			'post_month',
			'post_day',
			'author_first_name',
			'author_last_name',
			'permalink',
			'post_content',
		];

		foreach ( $vars_to_cache as $var ) {
			$cached_replacement_vars[ $var ] = \wpseo_replace_vars( '%%' . $var . '%%', $this->get_metabox_post() );
		}

		// Merge custom replace variables with the WordPress ones.
		return \array_merge( $cached_replacement_vars, $this->get_custom_replace_vars( $this->get_metabox_post() ) );
	}

	/**
	 * Prepares the recommended replace vars for localization.
	 *
	 * @return array Recommended replacement variables.
	 */
	protected function get_recommended_replace_vars() {
		$recommended_replace_vars = new WPSEO_Admin_Recommended_Replace_Vars();

		// What is recommended depends on the current context.
		$post_type = $recommended_replace_vars->determine_for_post( $this->get_metabox_post() );

		return $recommended_replace_vars->get_recommended_replacevars_for( $post_type );
	}

	/**
	 * Returns the list of replace vars that should be hidden inside the editor.
	 *
	 * @return string[] The hidden replace vars.
	 */
	protected function get_hidden_replace_vars() {
		return ( new WPSEO_Replace_Vars() )->get_hidden_replace_vars();
	}

	/**
	 * Gets the custom replace variables for custom taxonomies and fields.
	 *
	 * @param WP_Post $post The post to check for custom taxonomies and fields.
	 *
	 * @return array Array containing all the replacement variables.
	 */
	protected function get_custom_replace_vars( $post ) {
		return [
			'custom_fields'     => $this->get_custom_fields_replace_vars( $post ),
			'custom_taxonomies' => $this->get_custom_taxonomies_replace_vars( $post ),
		];
	}

	/**
	 * Gets the custom replace variables for custom taxonomies.
	 *
	 * @param WP_Post $post The post to check for custom taxonomies.
	 *
	 * @return array Array containing all the replacement variables.
	 */
	protected function get_custom_taxonomies_replace_vars( $post ) {
		$taxonomies          = \get_object_taxonomies( $post, 'objects' );
		$custom_replace_vars = [];

		foreach ( $taxonomies as $taxonomy_name => $taxonomy ) {

			if ( \is_string( $taxonomy ) ) { // If attachment, see https://core.trac.wordpress.org/ticket/37368 .
				$taxonomy_name = $taxonomy;
				$taxonomy      = \get_taxonomy( $taxonomy_name );
			}

			if ( $taxonomy->_builtin && $taxonomy->public ) {
				continue;
			}

			$custom_replace_vars[ $taxonomy_name ] = [
				'name'        => $taxonomy->name,
				'description' => $taxonomy->description,
			];
		}

		return $custom_replace_vars;
	}

	/**
	 * Gets the custom replace variables for custom fields.
	 *
	 * @param WP_Post $post The post to check for custom fields.
	 *
	 * @return array Array containing all the replacement variables.
	 */
	protected function get_custom_fields_replace_vars( $post ) {
		$custom_replace_vars = [];

		// If no post object is passed, return the empty custom_replace_vars array.
		if ( ! \is_object( $post ) ) {
			return $custom_replace_vars;
		}

		$custom_fields = \get_post_custom( $post->ID );

		// Simply concatenate all fields containing replace vars so we can handle them all with a single regex find.
		$replace_vars_fields = \implode(
			' ',
			[
				\YoastSEO()->meta->for_post( $post->ID )->presentation->title,
				\YoastSEO()->meta->for_post( $post->ID )->presentation->meta_description,
			]
		);

		\preg_match_all( '/%%cf_([A-Za-z0-9_]+)%%/', $replace_vars_fields, $matches );
		$fields_to_include = $matches[1];
		foreach ( $custom_fields as $custom_field_name => $custom_field ) {
			// Skip private custom fields.
			if ( \substr( $custom_field_name, 0, 1 ) === '_' ) {
				continue;
			}

			// Skip custom fields that are not used, new ones will be fetched dynamically.
			if ( ! \in_array( $custom_field_name, $fields_to_include, true ) ) {
				continue;
			}

			// Skip custom field values that are serialized.
			if ( \is_serialized( $custom_field[0] ) ) {
				continue;
			}

			$custom_replace_vars[ $custom_field_name ] = $custom_field[0];
		}

		return $custom_replace_vars;
	}

	/**
	 * Determines the scope based on the post type.
	 * This can be used by the replacevar plugin to determine if a replacement needs to be executed.
	 *
	 * @return string String describing the current scope.
	 */
	protected function determine_scope() {
		if ( $this->get_metabox_post()->post_type === 'page' ) {
			return 'page';
		}

		return 'post';
	}

	/**
	 * Determines whether or not the current post type has registered taxonomies.
	 *
	 * @return bool Whether the current post type has taxonomies.
	 */
	protected function current_post_type_has_taxonomies() {
		$post_taxonomies = \get_object_taxonomies( $this->get_metabox_post()->post_type );

		return ! empty( $post_taxonomies );
	}

	/**
	 * Returns an array with shortcode tags for all registered shortcodes.
	 *
	 * @return array
	 */
	protected function get_valid_shortcode_tags() {
		$shortcode_tags = [];

		foreach ( $GLOBALS['shortcode_tags'] as $tag => $description ) {
			$shortcode_tags[] = $tag;
		}

		return $shortcode_tags;
	}
}
