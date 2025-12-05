<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Editors\Application\Site\Website_Information_Repository;
use Yoast\WP\SEO\Presenters\Admin\Alert_Presenter;
use Yoast\WP\SEO\Presenters\Admin\Meta_Fields_Presenter;

/**
 * This class generates the metabox on the edit post / page as well as contains all page analysis functionality.
 */
class WPSEO_Metabox extends WPSEO_Meta {

	/**
	 * Whether the social tab is enabled.
	 *
	 * @var bool
	 */
	private $social_is_enabled;

	/**
	 * Helper to determine whether the SEO analysis is enabled.
	 *
	 * @var WPSEO_Metabox_Analysis_SEO
	 */
	protected $seo_analysis;

	/**
	 * Helper to determine whether the readability analysis is enabled.
	 *
	 * @var WPSEO_Metabox_Analysis_Readability
	 */
	protected $readability_analysis;

	/**
	 * Helper to determine whether the inclusive language analysis is enabled.
	 *
	 * @var WPSEO_Metabox_Analysis_Inclusive_Language
	 */
	protected $inclusive_language_analysis;

	/**
	 * The metabox editor object.
	 *
	 * @var WPSEO_Metabox_Editor
	 */
	protected $editor;

	/**
	 * The Metabox post.
	 *
	 * @var WP_Post|null
	 */
	protected $post = null;

	/**
	 * Whether the advanced metadata is enabled.
	 *
	 * @var bool
	 */
	protected $is_advanced_metadata_enabled;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( $this->is_internet_explorer() ) {
			add_action( 'add_meta_boxes', [ $this, 'internet_explorer_metabox' ] );

			return;
		}

		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'wp_insert_post', [ $this, 'save_postdata' ] );
		add_action( 'edit_attachment', [ $this, 'save_postdata' ] );
		add_action( 'add_attachment', [ $this, 'save_postdata' ] );

		$this->social_is_enabled            = WPSEO_Options::get( 'opengraph', false, [ 'wpseo_social' ] ) || WPSEO_Options::get( 'twitter', false, [ 'wpseo_social' ] );
		$this->is_advanced_metadata_enabled = WPSEO_Capability_Utils::current_user_can( 'wpseo_edit_advanced_metadata' ) || WPSEO_Options::get( 'disableadvanced_meta', null, [ 'wpseo' ] ) === false;

		$this->seo_analysis                = new WPSEO_Metabox_Analysis_SEO();
		$this->readability_analysis        = new WPSEO_Metabox_Analysis_Readability();
		$this->inclusive_language_analysis = new WPSEO_Metabox_Analysis_Inclusive_Language();
	}

	/**
	 * Checks whether the request comes from an IE 11 browser.
	 *
	 * @return bool Whether the request comes from an IE 11 browser.
	 */
	public static function is_internet_explorer() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return false;
		}

		$user_agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );

		if ( stripos( $user_agent, 'Trident/7.0' ) === false ) {
			return false;
		}

		return true;
	}

	/**
	 * Adds an alternative metabox for internet explorer users.
	 *
	 * @return void
	 */
	public function internet_explorer_metabox() {
		$post_types = WPSEO_Post_Type::get_accessible_post_types();
		$post_types = array_filter( $post_types, [ $this, 'display_metabox' ] );

		if ( ! is_array( $post_types ) || $post_types === [] ) {
			return;
		}

		$product_title = $this->get_product_title();

		foreach ( $post_types as $post_type ) {
			add_filter( "postbox_classes_{$post_type}_wpseo_meta", [ $this, 'wpseo_metabox_class' ] );

			add_meta_box(
				'wpseo_meta',
				$product_title,
				[ $this, 'render_internet_explorer_notice' ],
				$post_type,
				'normal',
				apply_filters( 'wpseo_metabox_prio', 'high' ),
				[ '__block_editor_compatible_meta_box' => true ]
			);
		}
	}

	/**
	 * Renders the content for the internet explorer metabox.
	 *
	 * @return void
	 */
	public function render_internet_explorer_notice() {
		$content = sprintf(
			/* translators: 1: Link start tag to the Firefox website, 2: Link start tag to the Chrome website, 3: Link start tag to the Edge website, 4: Link closing tag. */
			esc_html__( 'The browser you are currently using is unfortunately rather dated. Since we strive to give you the best experience possible, we no longer support this browser. Instead, please use %1$sFirefox%4$s, %2$sChrome%4$s or %3$sMicrosoft Edge%4$s.', 'wordpress-seo' ),
			'<a href="https://www.mozilla.org/firefox/new/">',
			'<a href="https://www.google.com/chrome/">',
			'<a href="https://www.microsoft.com/windows/microsoft-edge">',
			'</a>'
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped above.
		echo new Alert_Presenter( $content );
	}

	/**
	 * Translates text strings for use in the meta box.
	 *
	 * IMPORTANT: if you want to add a new string (option) somewhere, make sure you add that array key to
	 * the main meta box definition array in the class WPSEO_Meta() as well!!!!
	 *
	 * @deprecated 23.5
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public static function translate_meta_boxes() {
		_deprecated_function( __METHOD__, 'Yoast SEO 23.5' );

		WPSEO_Meta::$meta_fields['general']['title']['title']    = __( 'SEO title', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['general']['metadesc']['title'] = __( 'Meta description', 'wordpress-seo' );

		/* translators: %s expands to the post type name. */
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-noindex']['title'] = __( 'Allow search engines to show this %s in search results?', 'wordpress-seo' );
		if ( (string) get_option( 'blog_public' ) === '0' ) {
			WPSEO_Meta::$meta_fields['advanced']['meta-robots-noindex']['description'] = '<span class="error-message">' . __( 'Warning: even though you can set the meta robots setting here, the entire site is set to noindex in the sitewide privacy settings, so these settings won\'t have an effect.', 'wordpress-seo' ) . '</span>';
		}
		/* translators: %1$s expands to Yes or No,  %2$s expands to the post type name.*/
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-noindex']['options']['0'] = __( 'Default for %2$s, currently: %1$s', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-noindex']['options']['2'] = __( 'Yes', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-noindex']['options']['1'] = __( 'No', 'wordpress-seo' );

		/* translators: %1$s expands to the post type name.*/
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-nofollow']['title']        = __( 'Should search engines follow links on this %1$s?', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-nofollow']['options']['0'] = __( 'Yes', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-nofollow']['options']['1'] = __( 'No', 'wordpress-seo' );

		WPSEO_Meta::$meta_fields['advanced']['meta-robots-adv']['title']                   = __( 'Meta robots advanced', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-adv']['description']             = __( 'If you want to apply advanced <code>meta</code> robots settings for this page, please define them in the following field.', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-adv']['options']['noimageindex'] = __( 'No Image Index', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-adv']['options']['noarchive']    = __( 'No Archive', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['meta-robots-adv']['options']['nosnippet']    = __( 'No Snippet', 'wordpress-seo' );

		WPSEO_Meta::$meta_fields['advanced']['bctitle']['title']       = __( 'Breadcrumbs Title', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['bctitle']['description'] = __( 'Title to use for this page in breadcrumb paths', 'wordpress-seo' );

		WPSEO_Meta::$meta_fields['advanced']['canonical']['title'] = __( 'Canonical URL', 'wordpress-seo' );

		WPSEO_Meta::$meta_fields['advanced']['canonical']['description'] = sprintf(
			/* translators: 1: link open tag; 2: link close tag. */
			__( 'The canonical URL that this page should point to. Leave empty to default to permalink. %1$sCross domain canonical%2$s supported too.', 'wordpress-seo' ),
			'<a href="https://googlewebmastercentral.blogspot.com/2009/12/handling-legitimate-cross-domain.html" target="_blank" rel="noopener">',
			WPSEO_Admin_Utils::get_new_tab_message() . '</a>'
		);

		WPSEO_Meta::$meta_fields['advanced']['redirect']['title']       = __( '301 Redirect', 'wordpress-seo' );
		WPSEO_Meta::$meta_fields['advanced']['redirect']['description'] = __( 'The URL that this page should redirect to.', 'wordpress-seo' );

		do_action_deprecated( 'wpseo_tab_translate', [], 'Yoast SEO 23.5', '', 'WPSEO_Metabox::translate_meta_boxes is deprecated.' );
	}

	/**
	 * Determines whether the metabox should be shown for the passed identifier.
	 *
	 * By default the check is done for post types, but can also be used for taxonomies.
	 *
	 * @param string|null $identifier The identifier to check.
	 * @param string      $type       The type of object to check. Defaults to post_type.
	 *
	 * @return bool Whether or not the metabox should be displayed.
	 */
	public function display_metabox( $identifier = null, $type = 'post_type' ) {
		return WPSEO_Utils::is_metabox_active( $identifier, $type );
	}

	/**
	 * Adds the Yoast SEO meta box to the edit boxes in the edit post, page,
	 * attachment, and custom post types pages.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		$post_types = WPSEO_Post_Type::get_accessible_post_types();
		$post_types = array_filter( $post_types, [ $this, 'display_metabox' ] );

		if ( ! is_array( $post_types ) || $post_types === [] ) {
			return;
		}

		$product_title = $this->get_product_title();

		foreach ( $post_types as $post_type ) {
			add_filter( "postbox_classes_{$post_type}_wpseo_meta", [ $this, 'wpseo_metabox_class' ] );

			add_meta_box(
				'wpseo_meta',
				$product_title,
				[ $this, 'meta_box' ],
				$post_type,
				'normal',
				apply_filters( 'wpseo_metabox_prio', 'high' ),
				[ '__block_editor_compatible_meta_box' => true ]
			);
		}
	}

	/**
	 * Adds CSS classes to the meta box.
	 *
	 * @param string[] $classes An array of postbox CSS classes.
	 *
	 * @return string[]  List of classes that will be applied to the editbox container.
	 */
	public function wpseo_metabox_class( $classes ) {
		$classes[] = 'yoast wpseo-metabox';

		return $classes;
	}

	/**
	 * Passes variables to js for use with the post-scraper.
	 *
	 * @return array<string, string|array<string|int|bool>|bool|int>
	 */
	public function get_metabox_script_data() {
		$permalink = $this->get_permalink();

		$post_formatter = new WPSEO_Metabox_Formatter(
			new WPSEO_Post_Metabox_Formatter( $this->get_metabox_post(), [], $permalink )
		);

		$values = $post_formatter->get_values();
		/** This filter is documented in admin/filters/class-cornerstone-filter.php. */
		$post_types = apply_filters( 'wpseo_cornerstone_post_types', WPSEO_Post_Type::get_accessible_post_types() );
		if ( $values['cornerstoneActive'] && ! in_array( $this->get_metabox_post()->post_type, $post_types, true ) ) {
			$values['cornerstoneActive'] = false;
		}

		if ( $values['semrushIntegrationActive'] && $this->post->post_type === 'attachment' ) {
			$values['semrushIntegrationActive'] = 0;
		}

		if ( $values['wincherIntegrationActive'] && $this->post->post_type === 'attachment' ) {
			$values['wincherIntegrationActive'] = 0;
		}

		return $values;
	}

	/**
	 * Determines whether or not the current post type has registered taxonomies.
	 *
	 * @return bool Whether the current post type has taxonomies.
	 */
	private function current_post_type_has_taxonomies() {
		$post_taxonomies = get_object_taxonomies( get_post_type() );

		return ! empty( $post_taxonomies );
	}

	/**
	 * Determines the scope based on the post type.
	 * This can be used by the replacevar plugin to determine if a replacement needs to be executed.
	 *
	 * @return string String describing the current scope.
	 */
	private function determine_scope() {
		if ( $this->get_metabox_post()->post_type === 'page' ) {
			return 'page';
		}

		return 'post';
	}

	/**
	 * Outputs the meta box.
	 *
	 * @return void
	 */
	public function meta_box() {
		$this->render_hidden_fields();
		$this->render_tabs();
	}

	/**
	 * Renders the metabox hidden fields.
	 *
	 * @return void
	 */
	protected function render_hidden_fields() {
		wp_nonce_field( 'yoast_free_metabox', 'yoast_free_metabox_nonce' );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped in class.
		echo new Meta_Fields_Presenter( $this->get_metabox_post(), 'general' );

		if ( $this->is_advanced_metadata_enabled ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped in class.
			echo new Meta_Fields_Presenter( $this->get_metabox_post(), 'advanced' );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped in class.
		echo new Meta_Fields_Presenter( $this->get_metabox_post(), 'schema', $this->get_metabox_post()->post_type );

		if ( $this->social_is_enabled ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped in class.
			echo new Meta_Fields_Presenter( $this->get_metabox_post(), 'social' );
		}

		/**
		 * Filter: 'wpseo_content_meta_section_content' - Allow filtering the metabox content before outputting.
		 *
		 * @param string $post_content The metabox content string.
		 */
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output should be escaped in the filter.
		echo apply_filters( 'wpseo_content_meta_section_content', '' );
	}

	/**
	 * Renders the metabox tabs.
	 *
	 * @return void
	 */
	protected function render_tabs() {
		echo '<div class="wpseo-metabox-content">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: $this->get_product_title() returns a hard-coded string.
		printf( '<div class="wpseo-metabox-menu"><ul role="tablist" class="yoast-aria-tabs" aria-label="%s">', $this->get_product_title() );

		$tabs = $this->get_tabs();

		foreach ( $tabs as $tab ) {
			if ( $tab->name === 'premium' ) {
				continue;
			}

			$tab->display_link();
		}

		echo '</ul></div>';

		foreach ( $tabs as $tab ) {
			$tab->display_content();
		}

		echo '</div>';
	}

	/**
	 * Returns the relevant metabox tabs for the current view.
	 *
	 * @return WPSEO_Metabox_Section[]
	 */
	private function get_tabs() {
		$tabs = [];

		$label = __( 'SEO', 'wordpress-seo' );
		if ( $this->seo_analysis->is_enabled() ) {
			$label = '<span class="wpseo-score-icon-container" id="wpseo-seo-score-icon"></span>' . $label;
		}
		$tabs[] = new WPSEO_Metabox_Section_React( 'content', $label );

		if ( $this->readability_analysis->is_enabled() ) {
			$tabs[] = new WPSEO_Metabox_Section_Readability();
		}

		if ( $this->inclusive_language_analysis->is_enabled() ) {
			$tabs[] = new WPSEO_Metabox_Section_Inclusive_Language();
		}

		if ( $this->is_advanced_metadata_enabled ) {
			$tabs[] = new WPSEO_Metabox_Section_React(
				'schema',
				'<span class="wpseo-schema-icon"></span>' . __( 'Schema', 'wordpress-seo' ),
				''
			);
		}

		if ( $this->social_is_enabled ) {
			$tabs[] = new WPSEO_Metabox_Section_React(
				'social',
				'<span class="dashicons dashicons-share"></span>' . __( 'Social', 'wordpress-seo' ),
				'',
				[
					'html_after' => '<div id="wpseo-section-social"></div>',
				]
			);
		}

		$tabs = array_merge( $tabs, $this->get_additional_tabs() );

		return $tabs;
	}

	/**
	 * Returns the metabox tabs that have been added by other plugins.
	 *
	 * @return WPSEO_Metabox_Section_Additional[]
	 */
	protected function get_additional_tabs() {
		$tabs = [];

		/**
		 * Private filter: 'yoast_free_additional_metabox_sections'.
		 *
		 * Meant for internal use only. Allows adding additional tabs to the Yoast SEO metabox.
		 *
		 * @since 11.9
		 *
		 * @param array[] $tabs {
		 *     An array of arrays with tab specifications.
		 *
		 *     @type array $tab {
		 *          A tab specification.
		 *
		 *          @type string $name         The name of the tab. Used in the HTML IDs, href and aria properties.
		 *          @type string $link_content The content of the tab link.
		 *          @type string $content      The content of the tab.
		 *          @type array $options {
		 *              Optional. Extra options.
		 *
		 *              @type string $link_class      Optional. The class for the tab link.
		 *              @type string $link_aria_label Optional. The aria label of the tab link.
		 *          }
		 *     }
		 * }
		 */
		$requested_tabs = apply_filters( 'yoast_free_additional_metabox_sections', [] );

		foreach ( $requested_tabs as $tab ) {
			if ( is_array( $tab ) && array_key_exists( 'name', $tab ) && array_key_exists( 'link_content', $tab ) && array_key_exists( 'content', $tab ) ) {
				$options = array_key_exists( 'options', $tab ) ? $tab['options'] : [];
				$tabs[]  = new WPSEO_Metabox_Section_Additional(
					$tab['name'],
					$tab['link_content'],
					$tab['content'],
					$options
				);
			}
		}

		return $tabs;
	}

	/**
	 * Adds a line in the meta box.
	 *
	 * @deprecated 23.5
	 * @codeCoverageIgnore
	 *
	 * @param string[] $meta_field_def Contains the vars based on which output is generated.
	 * @param string   $key            Internal key (without prefix).
	 *
	 * @return string
	 */
	public function do_meta_box( $meta_field_def, $key = '' ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 23.5' );

		$content      = '';
		$esc_form_key = esc_attr( WPSEO_Meta::$form_prefix . $key );
		$meta_value   = WPSEO_Meta::get_value( $key, $this->get_metabox_post()->ID );

		$class = '';
		if ( isset( $meta_field_def['class'] ) && $meta_field_def['class'] !== '' ) {
			$class = ' ' . $meta_field_def['class'];
		}

		$placeholder = '';
		if ( isset( $meta_field_def['placeholder'] ) && $meta_field_def['placeholder'] !== '' ) {
			$placeholder = $meta_field_def['placeholder'];
		}

		$aria_describedby = '';
		$description      = '';
		if ( isset( $meta_field_def['description'] ) ) {
			$aria_describedby = ' aria-describedby="' . $esc_form_key . '-desc"';
			$description      = '<p id="' . $esc_form_key . '-desc" class="yoast-metabox__description">' . $meta_field_def['description'] . '</p>';
		}

		// Add a hide_on_pages option that returns nothing when the field is rendered on a page.
		if ( isset( $meta_field_def['hide_on_pages'] ) && $meta_field_def['hide_on_pages'] && get_post_type() === 'page' ) {
			return '';
		}

		switch ( $meta_field_def['type'] ) {
			case 'text':
				$ac = '';
				if ( isset( $meta_field_def['autocomplete'] ) && $meta_field_def['autocomplete'] === false ) {
					$ac = 'autocomplete="off" ';
				}
				if ( $placeholder !== '' ) {
					$placeholder = ' placeholder="' . esc_attr( $placeholder ) . '"';
				}
				$content .= '<input type="text"' . $placeholder . ' id="' . $esc_form_key . '" ' . $ac . 'name="' . $esc_form_key . '" value="' . esc_attr( $meta_value ) . '" class="large-text' . $class . '"' . $aria_describedby . '/>';
				break;

			case 'url':
				if ( $placeholder !== '' ) {
					$placeholder = ' placeholder="' . esc_attr( $placeholder ) . '"';
				}
				$content .= '<input type="url"' . $placeholder . ' id="' . $esc_form_key . '" name="' . $esc_form_key . '" value="' . esc_attr( urldecode( $meta_value ) ) . '" class="large-text' . $class . '"' . $aria_describedby . '/>';
				break;

			case 'textarea':
				$rows = 3;
				if ( isset( $meta_field_def['rows'] ) && $meta_field_def['rows'] > 0 ) {
					$rows = $meta_field_def['rows'];
				}
				$content .= '<textarea class="large-text' . $class . '" rows="' . esc_attr( $rows ) . '" id="' . $esc_form_key . '" name="' . $esc_form_key . '"' . $aria_describedby . '>' . esc_textarea( $meta_value ) . '</textarea>';
				break;

			case 'hidden':
				$default = '';
				if ( isset( $meta_field_def['default'] ) ) {
					$default = sprintf( ' data-default="%s"', esc_attr( $meta_field_def['default'] ) );
				}
				$content .= '<input type="hidden" id="' . $esc_form_key . '" name="' . $esc_form_key . '" value="' . esc_attr( $meta_value ) . '"' . $default . '/>' . "\n";
				break;
			case 'select':
				if ( isset( $meta_field_def['options'] ) && is_array( $meta_field_def['options'] ) && $meta_field_def['options'] !== [] ) {
					$content .= '<select name="' . $esc_form_key . '" id="' . $esc_form_key . '" class="yoast' . $class . '">';
					foreach ( $meta_field_def['options'] as $val => $option ) {
						$selected = selected( $meta_value, $val, false );
						$content .= '<option ' . $selected . ' value="' . esc_attr( $val ) . '">' . esc_html( $option ) . '</option>';
					}
					unset( $val, $option, $selected );
					$content .= '</select>';
				}
				break;

			case 'multiselect':
				if ( isset( $meta_field_def['options'] ) && is_array( $meta_field_def['options'] ) && $meta_field_def['options'] !== [] ) {

					// Set $meta_value as $selected_arr.
					$selected_arr = $meta_value;

					// If the multiselect field is 'meta-robots-adv' we should explode on ,.
					if ( $key === 'meta-robots-adv' ) {
						$selected_arr = explode( ',', $meta_value );
					}

					if ( ! is_array( $selected_arr ) ) {
						$selected_arr = (array) $selected_arr;
					}

					$options_count = count( $meta_field_def['options'] );

					$content .= '<select multiple="multiple" size="' . esc_attr( $options_count ) . '" name="' . $esc_form_key . '[]" id="' . $esc_form_key . '" class="yoast' . $class . '"' . $aria_describedby . '>';
					foreach ( $meta_field_def['options'] as $val => $option ) {
						$selected = '';
						if ( in_array( $val, $selected_arr, true ) ) {
							$selected = ' selected="selected"';
						}
						$content .= '<option ' . $selected . ' value="' . esc_attr( $val ) . '">' . esc_html( $option ) . '</option>';
					}
					$content .= '</select>';
					unset( $val, $option, $selected, $selected_arr, $options_count );
				}
				break;

			case 'checkbox':
				$checked  = checked( $meta_value, 'on', false );
				$expl     = ( isset( $meta_field_def['expl'] ) ) ? esc_html( $meta_field_def['expl'] ) : '';
				$content .= '<input type="checkbox" id="' . $esc_form_key . '" name="' . $esc_form_key . '" ' . $checked . ' value="on" class="yoast' . $class . '"' . $aria_describedby . '/> <label for="' . $esc_form_key . '">' . $expl . '</label>';
				unset( $checked, $expl );
				break;

			case 'radio':
				if ( isset( $meta_field_def['options'] ) && is_array( $meta_field_def['options'] ) && $meta_field_def['options'] !== [] ) {
					foreach ( $meta_field_def['options'] as $val => $option ) {
						$checked  = checked( $meta_value, $val, false );
						$content .= '<input type="radio" ' . $checked . ' id="' . $esc_form_key . '_' . esc_attr( $val ) . '" name="' . $esc_form_key . '" value="' . esc_attr( $val ) . '"/> <label for="' . $esc_form_key . '_' . esc_attr( $val ) . '">' . esc_html( $option ) . '</label> ';
					}
					unset( $val, $option, $checked );
				}
				break;
		}

		$html = '';
		if ( $content === '' ) {
			$content = apply_filters_deprecated( 'wpseo_do_meta_box_field_' . $key, [ $content, $meta_value, $esc_form_key, $meta_field_def, $key ], 'Yoast SEO 23.5', '', 'do_meta_box is deprecated' );
		}

		if ( $content !== '' ) {

			$title = esc_html( $meta_field_def['title'] );

			// By default, use the field title as a label element.
			$label = '<label for="' . $esc_form_key . '">' . $title . '</label>';

			// Set the inline help and help panel, if any.
			$help_button = '';
			$help_panel  = '';
			if ( isset( $meta_field_def['help'] ) && $meta_field_def['help'] !== '' ) {
				$help        = new WPSEO_Admin_Help_Panel( $key, $meta_field_def['help-button'], $meta_field_def['help'] );
				$help_button = $help->get_button_html();
				$help_panel  = $help->get_panel_html();
			}

			// If it's a set of radio buttons, output proper fieldset and legend.
			if ( $meta_field_def['type'] === 'radio' ) {
				return '<fieldset><legend>' . $title . '</legend>' . $help_button . $help_panel . $content . $description . '</fieldset>';
			}

			// If it's a single checkbox, ignore the title.
			if ( $meta_field_def['type'] === 'checkbox' ) {
				$label = '';
			}

			// Other meta box content or form fields.
			if ( $meta_field_def['type'] === 'hidden' ) {
				$html = $content;
			}
			else {
				$html = $label . $description . $help_button . $help_panel . $content;
			}
		}

		return $html;
	}

	/**
	 * Saves the WP SEO metadata for posts.
	 *
	 * {@internal $_POST parameters are validated via sanitize_post_meta().}}
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool|void Boolean false if invalid save post request.
	 */
	public function save_postdata( $post_id ) {
		// Bail if this is a multisite installation and the site has been switched.
		if ( is_multisite() && ms_is_switched() ) {
			return false;
		}

		if ( $post_id === null ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in wp_verify_none.
		if ( ! isset( $_POST['yoast_free_metabox_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['yoast_free_metabox_nonce'] ), 'yoast_free_metabox' ) ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			$post_id = wp_is_post_revision( $post_id );
		}

		/**
		 * Determine we're not accidentally updating a different post.
		 * We can't use filter_input here as the ID isn't available at this point, other than in the $_POST data.
		 */
		if ( ! isset( $_POST['ID'] ) || $post_id !== (int) $_POST['ID'] ) {
			return false;
		}

		clean_post_cache( $post_id );
		$post = get_post( $post_id );

		if ( ! is_object( $post ) ) {
			// Non-existent post.
			return false;
		}

		do_action( 'wpseo_save_compare_data', $post );

		$social_fields = [];
		if ( $this->social_is_enabled ) {
			$social_fields = WPSEO_Meta::get_meta_field_defs( 'social' );
		}

		$meta_boxes = apply_filters( 'wpseo_save_metaboxes', [] );
		$meta_boxes = array_merge(
			$meta_boxes,
			WPSEO_Meta::get_meta_field_defs( 'general', $post->post_type ),
			WPSEO_Meta::get_meta_field_defs( 'advanced' ),
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
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- We're preparing to do just that.
					$data = wp_unslash( $_POST[ $field_name ] );

					// For multi-select.
					if ( is_array( $data ) ) {
						$data = array_map( [ 'WPSEO_Utils', 'sanitize_text_field' ], $data );
					}

					if ( is_string( $data ) ) {
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

		do_action( 'wpseo_saved_postdata' );
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
	 * @todo [JRF => whomever] Create css/metabox-mp6.css file and add it to the below allowed colors array when done.
	 *
	 * @return void
	 */
	public function enqueue() {
		global $pagenow;

		if ( $this->readability_analysis->is_enabled() ) {
			$this->editor = new WPSEO_Metabox_Editor();
			$this->editor->register_hooks();
		}

		$asset_manager = new WPSEO_Admin_Asset_Manager();

		if ( self::is_post_overview( $pagenow ) ) {
			return;
		}

		/* Filter 'wpseo_always_register_metaboxes_on_admin' documented in wpseo-main.php */
		if ( ( self::is_post_edit( $pagenow ) === false && apply_filters( 'wpseo_always_register_metaboxes_on_admin', false ) === false ) || $this->display_metabox() === false ) {
			return;
		}

		$post_id = get_queried_object_id();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( empty( $post_id ) && isset( $_GET['post'] ) && is_string( $_GET['post'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			$post_id = sanitize_text_field( wp_unslash( $_GET['post'] ) );
		}

		if ( $post_id !== 0 ) {
			// Enqueue files needed for upload functionality.
			wp_enqueue_media( [ 'post' => $post_id ] );
		}

		$asset_manager->enqueue_style( 'metabox-css' );
		if ( $this->readability_analysis->is_enabled() ) {
			$asset_manager->enqueue_style( 'scoring' );
		}
		$asset_manager->enqueue_style( 'monorepo' );
		$asset_manager->enqueue_style( 'ai-generator' );
		$asset_manager->enqueue_style( 'ai-fix-assessments' );

		$is_block_editor  = WP_Screen::get()->is_block_editor();
		$post_edit_handle = 'post-edit';
		if ( ! $is_block_editor ) {
			$post_edit_handle = 'post-edit-classic';
		}
		$asset_manager->enqueue_script( $post_edit_handle );
		$asset_manager->enqueue_style( 'admin-css' );

		/**
		 * Removes the emoji script as it is incompatible with both React and any
		 * contenteditable fields.
		 */
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

		$asset_manager->localize_script( $post_edit_handle, 'wpseoAdminL10n', WPSEO_Utils::get_admin_l10n() );

		$plugins_script_data = [
			'replaceVars' => [
				'replace_vars'             => $this->get_replace_vars(),
				'hidden_replace_vars'      => $this->get_hidden_replace_vars(),
				'recommended_replace_vars' => $this->get_recommended_replace_vars(),
				'scope'                    => $this->determine_scope(),
				'has_taxonomies'           => $this->current_post_type_has_taxonomies(),
			],
			'shortcodes' => [
				'wpseo_shortcode_tags'          => $this->get_valid_shortcode_tags(),
				'wpseo_filter_shortcodes_nonce' => wp_create_nonce( 'wpseo-filter-shortcodes' ),
			],
		];

		$worker_script_data = [
			'url'                     => YoastSEO()->helpers->asset->get_asset_url( 'yoast-seo-analysis-worker' ),
			'dependencies'            => YoastSEO()->helpers->asset->get_dependency_urls_by_handle( 'yoast-seo-analysis-worker' ),
			'keywords_assessment_url' => YoastSEO()->helpers->asset->get_asset_url( 'yoast-seo-used-keywords-assessment' ),
			'log_level'               => WPSEO_Utils::get_analysis_worker_log_level(),
		];

		$page_on_front    = (int) get_option( 'page_on_front' );
		$homepage_is_page = get_option( 'show_on_front' ) === 'page';
		$is_front_page    = $homepage_is_page && $page_on_front === (int) $post_id;

		$script_data = [
			'metabox'                    => $this->get_metabox_script_data(),
			'isPost'                     => true,
			'isBlockEditor'              => $is_block_editor,
			'postId'                     => $post_id,
			'postStatus'                 => get_post_status( $post_id ),
			'postType'                   => get_post_type( $post_id ),
			'isPage'                     => get_post_type( $post_id ) === 'page',
			'usedKeywordsNonce'          => wp_create_nonce( 'wpseo-keyword-usage-and-post-types' ),
			'analysis'                   => [
				'plugins' => $plugins_script_data,
				'worker'  => $worker_script_data,
			],
			'isFrontPage'                => $is_front_page,
		];

		/**
		 * The website information repository.
		 *
		 * @var Website_Information_Repository $repo
		 */
		$repo             = YoastSEO()->classes->get( Website_Information_Repository::class );
		$site_information = $repo->get_post_site_information();
		$site_information->set_permalink( $this->get_permalink() );
		$script_data = array_merge_recursive( $site_information->get_legacy_site_information(), $script_data );

		if ( ! $is_block_editor && post_type_supports( get_post_type(), 'thumbnail' ) ) {
			$asset_manager->enqueue_style( 'featured-image' );
		}

		$asset_manager->localize_script( $post_edit_handle, 'wpseoScriptData', $script_data );
	}

	/**
	 * Returns post in metabox context.
	 *
	 * @return WP_Post|array<string|int|bool>
	 */
	protected function get_metabox_post() {
		if ( $this->post !== null ) {
			return $this->post;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['post'] ) && is_string( $_GET['post'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, Sanitization happens in the validate_int function.
			$post_id = (int) WPSEO_Utils::validate_int( wp_unslash( $_GET['post'] ) );

			$this->post = get_post( $post_id );

			return $this->post;
		}

		if ( isset( $GLOBALS['post'] ) ) {
			$this->post = $GLOBALS['post'];

			return $this->post;
		}

		return [];
	}

	/**
	 * Returns an array with shortcode tags for all registered shortcodes.
	 *
	 * @return string[]
	 */
	private function get_valid_shortcode_tags() {
		$shortcode_tags = [];

		foreach ( $GLOBALS['shortcode_tags'] as $tag => $description ) {
			$shortcode_tags[] = $tag;
		}

		return $shortcode_tags;
	}

	/**
	 * Prepares the replace vars for localization.
	 *
	 * @return string[] Replace vars.
	 */
	private function get_replace_vars() {
		$cached_replacement_vars = [];

		$vars_to_cache = [
			'date',
			'id',
			'sitename',
			'sitedesc',
			'sep',
			'page',
			'currentdate',
			'currentyear',
			'currentmonth',
			'currentday',
			'post_year',
			'post_month',
			'post_day',
			'name',
			'author_first_name',
			'author_last_name',
			'permalink',
			'post_content',
			'category_title',
			'tag',
			'category',
		];

		foreach ( $vars_to_cache as $var ) {
			$cached_replacement_vars[ $var ] = wpseo_replace_vars( '%%' . $var . '%%', $this->get_metabox_post() );
		}

		// Merge custom replace variables with the WordPress ones.
		return array_merge( $cached_replacement_vars, $this->get_custom_replace_vars( $this->get_metabox_post() ) );
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
	 * Prepares the recommended replace vars for localization.
	 *
	 * @return array<string[]> Recommended replacement variables.
	 */
	private function get_recommended_replace_vars() {
		$recommended_replace_vars = new WPSEO_Admin_Recommended_Replace_Vars();

		// What is recommended depends on the current context.
		$post_type = $recommended_replace_vars->determine_for_post( $this->get_metabox_post() );

		return $recommended_replace_vars->get_recommended_replacevars_for( $post_type );
	}

	/**
	 * Gets the custom replace variables for custom taxonomies and fields.
	 *
	 * @param WP_Post $post The post to check for custom taxonomies and fields.
	 *
	 * @return array<string[]> Array containing all the replacement variables.
	 */
	private function get_custom_replace_vars( $post ) {
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
	 * @return array<string[]> Array containing all the replacement variables.
	 */
	private function get_custom_taxonomies_replace_vars( $post ) {
		$taxonomies          = get_object_taxonomies( $post, 'objects' );
		$custom_replace_vars = [];

		foreach ( $taxonomies as $taxonomy_name => $taxonomy ) {

			if ( is_string( $taxonomy ) ) { // If attachment, see https://core.trac.wordpress.org/ticket/37368 .
				$taxonomy_name = $taxonomy;
				$taxonomy      = get_taxonomy( $taxonomy_name );
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
	 * @return array<string[]> Array containing all the replacement variables.
	 */
	private function get_custom_fields_replace_vars( $post ) {
		$custom_replace_vars = [];

		// If no post object is passed, return the empty custom_replace_vars array.
		if ( ! is_object( $post ) ) {
			return $custom_replace_vars;
		}

		$custom_fields = get_post_custom( $post->ID );

		// If $custom_fields is an empty string or generally not an array, return early.
		if ( ! is_array( $custom_fields ) ) {
			return $custom_replace_vars;
		}

		$meta = YoastSEO()->meta->for_post( $post->ID );

		if ( ! $meta ) {
			return $custom_replace_vars;
		}

		// Simply concatenate all fields containing replace vars so we can handle them all with a single regex find.
		$replace_vars_fields = implode(
			' ',
			[
				$meta->presentation->title,
				$meta->presentation->meta_description,
			]
		);

		preg_match_all( '/%%cf_([A-Za-z0-9_]+)%%/', $replace_vars_fields, $matches );
		$fields_to_include = $matches[1];
		foreach ( $custom_fields as $custom_field_name => $custom_field ) {
			// Skip private custom fields.
			if ( substr( $custom_field_name, 0, 1 ) === '_' ) {
				continue;
			}

			// Skip custom fields that are not used, new ones will be fetched dynamically.
			if ( ! in_array( $custom_field_name, $fields_to_include, true ) ) {
				continue;
			}

			// Skip custom field values that are serialized.
			if ( is_serialized( $custom_field[0] ) ) {
				continue;
			}

			$custom_replace_vars[ $custom_field_name ] = $custom_field[0];
		}

		return $custom_replace_vars;
	}

	/**
	 * Checks if the page is the post overview page.
	 *
	 * @param string $page The page to check for the post overview page.
	 *
	 * @return bool Whether or not the given page is the post overview page.
	 */
	public static function is_post_overview( $page ) {
		return $page === 'edit.php';
	}

	/**
	 * Checks if the page is the post edit page.
	 *
	 * @param string $page The page to check for the post edit page.
	 *
	 * @return bool Whether or not the given page is the post edit page.
	 */
	public static function is_post_edit( $page ) {
		return $page === 'post.php'
			|| $page === 'post-new.php';
	}

	/**
	 * Retrieves the product title.
	 *
	 * @return string The product title.
	 */
	protected function get_product_title() {
		return YoastSEO()->helpers->product->get_product_name();
	}

	/**
	 * Gets the permalink.
	 *
	 * @return string
	 */
	protected function get_permalink() {
		$permalink = '';

		if ( is_object( $this->get_metabox_post() ) ) {
			$permalink = get_sample_permalink( $this->get_metabox_post()->ID );
			$permalink = $permalink[0];
		}

		return $permalink;
	}
}
