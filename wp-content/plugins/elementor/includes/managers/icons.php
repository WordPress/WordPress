<?php
namespace Elementor;

use Elementor\Core\Files\File_Types\Svg;
use Elementor\Core\Page_Assets\Data_Managers\Font_Icon_Svg\Manager as Font_Icon_Svg_Data_Manager;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor icons manager.
 *
 * Elementor icons manager handler class
 *
 * @since 2.4.0
 */
class Icons_Manager {

	const NEEDS_UPDATE_OPTION = 'icon_manager_needs_update';

	const FONT_ICON_SVG_CLASS_NAME = 'e-font-icon-svg';

	const LOAD_FA4_SHIM_OPTION_KEY = 'elementor_load_fa4_shim';

	const ELEMENTOR_ICONS_VERSION = '5.44.0';

	/**
	 * Tabs.
	 *
	 * Holds the list of all the tabs.
	 *
	 * @access private
	 * @static
	 * @since 2.4.0
	 * @var array
	 */
	private static $tabs;

	private static $data_manager;

	private static function get_needs_upgrade_option() {
		return get_option( 'elementor_' . self::NEEDS_UPDATE_OPTION, null );
	}

	/**
	 * @param array  $icon
	 * @param array  $attributes
	 * @param string $tag
	 * @return bool|mixed|string
	 */
	public static function try_get_icon_html( $icon, $attributes = [], $tag = 'i' ) {
		if ( empty( $icon['library'] ) ) {
			return '';
		}

		return static::get_icon_html( $icon, $attributes, $tag );
	}

	/**
	 * @param array  $icon
	 * @param array  $attributes
	 * @param string $tag
	 * @return bool|mixed|string
	 */
	private static function get_icon_html( array $icon, array $attributes, $tag ) {
		/**
		 * When the library value is svg it means that it's a SVG media attachment uploaded by the user.
		 * Otherwise, it's the name of the font family that the icon belongs to.
		 */
		if ( 'svg' === $icon['library'] ) {
			$output = self::render_uploaded_svg_icon( $icon['value'] );
		} else {
			$output = self::render_font_icon( $icon, $attributes, $tag );
		}
		return $output;
	}

	/**
	 * Register styles
	 *
	 * Used to register all icon types stylesheets so they could be enqueued later by widgets
	 */
	public function register_styles() {
		$config = self::get_icon_manager_tabs_config();

		$shared_styles = [];

		foreach ( $config as $type => $icon_type ) {
			if ( ! isset( $icon_type['url'] ) ) {
				continue;
			}
			$dependencies = [];
			if ( ! empty( $icon_type['enqueue'] ) ) {
				foreach ( (array) $icon_type['enqueue'] as $font_css_url ) {
					if ( ! in_array( $font_css_url, array_keys( $shared_styles ), true ) ) {
						$style_handle = 'elementor-icons-shared-' . count( $shared_styles );
						wp_register_style(
							$style_handle,
							$font_css_url,
							[],
							$icon_type['ver']
						);
						$shared_styles[ $font_css_url ] = $style_handle;
					}
					$dependencies[] = $shared_styles[ $font_css_url ];
				}
			}
			wp_register_style(
				'elementor-icons-' . $icon_type['name'],
				$icon_type['url'],
				$dependencies,
				$icon_type['ver']
			);
		}
	}

	/**
	 * Init Tabs
	 *
	 * Initiate Icon Manager Tabs.
	 *
	 * @access private
	 * @static
	 * @since 2.4.0
	 */
	private static function init_tabs() {
		$initial_tabs = [
			'fa-regular' => [
				'name' => 'fa-regular',
				'label' => esc_html__( 'Font Awesome - Regular', 'elementor' ),
				'url' => self::get_fa_asset_url( 'regular' ),
				'enqueue' => [ self::get_fa_asset_url( 'fontawesome' ) ],
				'prefix' => 'fa-',
				'displayPrefix' => 'far',
				'labelIcon' => 'fab fa-font-awesome-alt',
				'ver' => '5.15.3',
				'fetchJson' => self::get_fa_asset_url( 'regular', 'js', false ),
				'native' => true,
			],
			'fa-solid' => [
				'name' => 'fa-solid',
				'label' => esc_html__( 'Font Awesome - Solid', 'elementor' ),
				'url' => self::get_fa_asset_url( 'solid' ),
				'enqueue' => [ self::get_fa_asset_url( 'fontawesome' ) ],
				'prefix' => 'fa-',
				'displayPrefix' => 'fas',
				'labelIcon' => 'fab fa-font-awesome',
				'ver' => '5.15.3',
				'fetchJson' => self::get_fa_asset_url( 'solid', 'js', false ),
				'native' => true,
			],
			'fa-brands' => [
				'name' => 'fa-brands',
				'label' => esc_html__( 'Font Awesome - Brands', 'elementor' ),
				'url' => self::get_fa_asset_url( 'brands' ),
				'enqueue' => [ self::get_fa_asset_url( 'fontawesome' ) ],
				'prefix' => 'fa-',
				'displayPrefix' => 'fab',
				'labelIcon' => 'fab fa-font-awesome-flag',
				'ver' => '5.15.3',
				'fetchJson' => self::get_fa_asset_url( 'brands', 'js', false ),
				'native' => true,
			],
		];

		/**
		 * Initial icon manager tabs.
		 *
		 * Filters the list of initial icon manager tabs.
		 *
		 * @param array $icon_manager_tabs Initial icon manager tabs.
		 */
		$initial_tabs = apply_filters( 'elementor/icons_manager/native', $initial_tabs );

		self::$tabs = $initial_tabs;
	}

	/**
	 * Get Icon Manager Tabs
	 *
	 * @return array
	 */
	public static function get_icon_manager_tabs() {
		if ( self::is_font_icon_inline_svg() && ! Plugin::$instance->editor->is_edit_mode() && ! Plugin::$instance->preview->is_preview_mode() ) {
			self::$tabs = [];
		} elseif ( ! self::$tabs ) {
			self::init_tabs();
		}

		$additional_tabs = [];

		/**
		 * Additional icon manager tabs.
		 *
		 * Filters additional icon manager tabs.
		 *
		 * @param array $additional_tabs Additional icon manager tabs. Default is an empty array.
		 */
		$additional_tabs = apply_filters( 'elementor/icons_manager/additional_tabs', $additional_tabs );

		return array_replace( self::$tabs, $additional_tabs );
	}

	public static function enqueue_shim() {
		//phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_enqueue_script(
			'font-awesome-4-shim',
			self::get_fa_asset_url( 'v4-shims', 'js' ),
			[],
			ELEMENTOR_VERSION
		);
		// Make sure that the CSS in the 'all' file does not override FA Pro's CSS
		if ( ! wp_script_is( 'font-awesome-pro' ) ) {
			wp_enqueue_style(
				'font-awesome-5-all',
				self::get_fa_asset_url( 'all' ),
				[],
				ELEMENTOR_VERSION
			);
		}
		wp_enqueue_style(
			'font-awesome-4-shim',
			self::get_fa_asset_url( 'v4-shims' ),
			[],
			ELEMENTOR_VERSION
		);
	}

	private static function get_fa_asset_url( $filename, $ext_type = 'css', $add_suffix = true ) {
		static $is_test_mode = null;
		if ( null === $is_test_mode ) {
			$is_test_mode = Utils::is_script_debug() || Utils::is_elementor_tests();
		}
		$url = ELEMENTOR_ASSETS_URL . 'lib/font-awesome/' . $ext_type . '/' . $filename;
		if ( ! $is_test_mode && $add_suffix ) {
			$url .= '.min';
		}

		return $url . '.' . $ext_type;
	}

	public static function get_icon_manager_tabs_config() {
		$tabs = [
			'all' => [
				'name' => 'all',
				'label' => esc_html__( 'All Icons', 'elementor' ),
				'labelIcon' => 'eicon-filter',
				'native' => true,
			],
		];

		return array_values( array_merge( $tabs, self::get_icon_manager_tabs() ) );
	}

	private static function is_font_icon_inline_svg() {
		return Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' );
	}

	/**
	 * @deprecated 3.8.0
	 */
	public static function render_svg_symbols() {}

	public static function get_icon_svg_data( $icon ) {
		return self::$data_manager->get_font_icon_svg_data( $icon );
	}

	/**
	 * Get font awesome svg.
	 *
	 * @param $icon array [ 'value' => string, 'library' => string ]
	 *
	 * @return bool|mixed|string
	 */
	public static function get_font_icon_svg( $icon, $attributes = [] ) {
		// Load the SVG from the database.
		$icon_data = self::get_icon_svg_data( $icon );

		if ( ! $icon_data['path'] ) {
			return '';
		}

		if ( ! empty( $attributes['class'] ) && ! is_array( $attributes['class'] ) ) {
			$attributes['class'] = [ $attributes['class'] ];
		}

		$attributes['class'][] = self::FONT_ICON_SVG_CLASS_NAME;
		$attributes['class'][] = 'e-' . $icon_data['key'];
		$attributes['viewBox'] = '0 0 ' . $icon_data['width'] . ' ' . $icon_data['height'];
		$attributes['xmlns'] = 'http://www.w3.org/2000/svg';

		return (
			'<svg ' . Utils::render_html_attributes( $attributes ) . '>' .
				'<path d="' . esc_attr( $icon_data['path'] ) . '"></path>' .
			'</svg>'
		);
	}

	public static function render_uploaded_svg_icon( $value ) {
		if ( ! isset( $value['id'] ) ) {
			return '';
		}

		return Svg::get_inline_svg( $value['id'] );
	}

	public static function render_font_icon( $icon, $attributes = [], $tag = 'i' ) {
		$icon_types = self::get_icon_manager_tabs();

		if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
			return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
		}

		$content = '';

		$font_icon_svg_family = self::is_font_icon_inline_svg() ? Font_Icon_Svg_Data_Manager::get_font_family( $icon['library'] ) : '';

		if ( $font_icon_svg_family ) {
			$icon['font_family'] = $font_icon_svg_family;

			$content = self::get_font_icon_svg( $icon, $attributes );

			if ( $content ) {
				return $content;
			}
		}

		if ( ! $content ) {
			if ( empty( $attributes['class'] ) ) {
				$attributes['class'] = $icon['value'];
			} elseif ( is_array( $attributes['class'] ) ) {
					$attributes['class'][] = $icon['value'];
			} else {
				$attributes['class'] .= ' ' . $icon['value'];
			}
		}

		return '<' . $tag . ' ' . Utils::render_html_attributes( $attributes ) . '>' . $content . '</' . $tag . '>';
	}

	/**
	 * Render Icon
	 *
	 * Used to render Icon for \Elementor\Controls_Manager::ICONS
	 *
	 * @param array  $icon             Icon Type, Icon value.
	 * @param array  $attributes       Icon HTML Attributes.
	 * @param string $tag             Icon HTML tag, defaults to <i>.
	 *
	 * @return mixed|string
	 */
	public static function render_icon( $icon, $attributes = [], $tag = 'i' ) {
		if ( empty( $icon['library'] ) ) {
			return false;
		}

		$output = static::get_icon_html( $icon, $attributes, $tag );

		Utils::print_unescaped_internal_string( $output );

		return true;
	}

	/**
	 * Font Awesome 4 to font Awesome 5 Value Migration
	 *
	 * Used to convert string value of Icon control to array value of Icons control
	 * ex: 'fa fa-star' => [ 'value' => 'fas fa-star', 'library' => 'fa-solid' ]
	 *
	 * @param $value
	 *
	 * @return array
	 */
	public static function fa4_to_fa5_value_migration( $value ) {
		static $migration_dictionary = false;
		if ( '' === $value ) {
			return [
				'value' => '',
				'library' => '',
			];
		}
		if ( false === $migration_dictionary ) {
			$migration_dictionary = json_decode( Utils::file_get_contents( ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/migration/mapping.js' ), true );
		}
		if ( isset( $migration_dictionary[ $value ] ) ) {
			return $migration_dictionary[ $value ];
		}

		return [
			'value' => 'fas ' . str_replace( 'fa ', '', $value ),
			'library' => 'fa-solid',
		];
	}

	/**
	 * On_import_migration
	 *
	 * @param array  $element        settings array.
	 * @param string $old_control   old control id.
	 * @param string $new_control   new control id.
	 * @param bool   $remove_old      boolean whether to remove old control or not.
	 *
	 * @return array
	 */
	public static function on_import_migration( array $element, $old_control = '', $new_control = '', $remove_old = false ) {

		if ( ! isset( $element['settings'][ $old_control ] ) || isset( $element['settings'][ $new_control ] ) ) {
			return $element;
		}

		// Case when old value is saved as empty string
		$new_value = [
			'value' => '',
			'library' => '',
		];

		// Case when old value needs migration
		if ( ! empty( $element['settings'][ $old_control ] ) && ! self::is_migration_allowed() ) {
			$new_value = self::fa4_to_fa5_value_migration( $element['settings'][ $old_control ] );
		}

		$element['settings'][ $new_control ] = $new_value;

		// remove old value
		if ( $remove_old ) {
			unset( $element['settings'][ $old_control ] );
		}

		return $element;
	}

	public static function is_migration_allowed() {
		static $migration_allowed = false;
		if ( false === $migration_allowed ) {
			$migration_allowed = null === self::get_needs_upgrade_option();

			/**
			 * Is icon migration allowed.
			 *
			 * Filters whether the icons migration allowed.
			 *
			 * @param bool $migration_allowed Is icon migration is allowed.
			 */
			$migration_allowed = apply_filters( 'elementor/icons_manager/migration_allowed', $migration_allowed );
		}
		return $migration_allowed;
	}

	/**
	 * Register_Admin Settings
	 *
	 * Adds Font Awesome migration / update admin settings
	 *
	 * @param Settings $settings
	 */
	public function register_admin_settings( Settings $settings ) {
		$settings->add_field(
			Settings::TAB_ADVANCED,
			Settings::TAB_ADVANCED,
			'load_fa4_shim',
			[
				'label' => esc_html__( 'Load Font Awesome 4 Support', 'elementor' ),
				'field_args' => [
					'type' => 'select',
					'std' => '',
					'options' => [
						'' => esc_html__( 'No', 'elementor' ),
						'yes' => esc_html__( 'Yes', 'elementor' ),
					],
					'desc' => esc_html__( 'Font Awesome 4 support script (shim.js) is a script that makes sure all previously selected Font Awesome 4 icons are displayed correctly while using Font Awesome 5 library.', 'elementor' ),
				],
			]
		);
	}

	public function register_admin_tools_settings( Tools $settings ) {
		$settings->add_tab( 'fontawesome4_migration', [ 'label' => esc_html__( 'Font Awesome Upgrade', 'elementor' ) ] );

		$settings->add_section( 'fontawesome4_migration', 'fontawesome4_migration', [
			'callback' => function() {
				echo '<h2>' . esc_html__( 'Font Awesome Upgrade', 'elementor' ) . '</h2>';
				echo '<p>' . // PHPCS - Plain Text
				esc_html__( 'Access 1,500+ amazing Font Awesome 5 icons and enjoy faster performance and design flexibility.', 'elementor' ) . '<br>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html__( 'By upgrading, whenever you edit a page containing a Font Awesome 4 icon, Elementor will convert it to the new Font Awesome 5 icon.', 'elementor' ) . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'</p><p><strong>' .
				esc_html__( 'Please note that the upgrade process may cause some of the previously used Font Awesome 4 icons to look a bit different due to minor design changes made by Font Awesome.', 'elementor' ) . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'</strong></p><p>' .
				esc_html__( 'The upgrade process includes a database update', 'elementor' ) . ' - ' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html__( 'We highly recommend backing up your database before performing this upgrade.', 'elementor' ) . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'</p>' .
				esc_html__( 'This action is not reversible and cannot be undone by rolling back to previous versions.', 'elementor' ) . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'</p>';
			},
			'fields' => [
				[
					'label' => esc_html__( 'Font Awesome Upgrade', 'elementor' ),
					'field_args' => [
						'type' => 'raw_html',
						'html' => sprintf( '<span data-action="%s" data-_nonce="%s" data-redirect-url="%s" class="button" id="elementor_upgrade_fa_button">%s</span>',
							self::NEEDS_UPDATE_OPTION . '_upgrade',
							wp_create_nonce( self::NEEDS_UPDATE_OPTION ),
							esc_url( $this->get_upgrade_redirect_url() ),
							esc_html__( 'Upgrade To Font Awesome 5', 'elementor' )
						),
					],
				],
			],
		] );
	}

	/**
	 * Get redirect URL when upgrading font awesome.
	 *
	 * @return string
	 */
	public function get_upgrade_redirect_url() {

		if ( ! wp_verify_nonce( Utils::get_super_global_value( $_GET, '_wpnonce' ), 'tools-page-from-editor' ) ) {
			return '';
		}

		$document_id = ! empty( $_GET['redirect_to_document'] ) ? absint( $_GET['redirect_to_document'] ) : null;

		if ( ! $document_id ) {
			return '';
		}

		$document = Plugin::$instance->documents->get( $document_id );

		if ( ! $document ) {
			return '';
		}

		return $document->get_edit_url();
	}

	/**
	 * Ajax Upgrade to FontAwesome 5
	 */
	public function ajax_upgrade_to_fa5() {
		check_ajax_referer( self::NEEDS_UPDATE_OPTION, '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permission denied' );
		}

		delete_option( 'elementor_' . self::NEEDS_UPDATE_OPTION );

		wp_send_json_success( [ 'message' => esc_html__( 'Hurray! The upgrade process to Font Awesome 5 was completed successfully.', 'elementor' ) ] );
	}

	/**
	 * Add Update Needed Flag
	 *
	 * @param array $settings
	 *
	 * @return array;
	 */
	public function add_update_needed_flag( $settings ) {
		$settings['icons_update_needed'] = true;
		return $settings;
	}

	public function enqueue_fontawesome_css() {
		if ( ! self::is_migration_allowed() ) {
			wp_enqueue_style( 'font-awesome' );
		} else {
			$current_filter = current_filter();
			$load_shim = get_option( self::LOAD_FA4_SHIM_OPTION_KEY, false );
			if ( 'elementor/editor/after_enqueue_styles' === $current_filter ) {
				self::enqueue_shim();
			} elseif ( 'yes' === $load_shim ) {
				self::enqueue_shim();
			}
		}
	}

	/**
	 * @deprecated 3.1.0
	 */
	public function add_admin_strings() {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.1.0' );

		return [];
	}

	/**
	 * Icons Manager constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			// @todo: remove once we deprecate fa4
			add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_settings' ], 100 );
		}

		if ( self::is_font_icon_inline_svg() ) {
			self::$data_manager = new Font_Icon_Svg_Data_Manager();
		}

		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_fontawesome_css' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );

		if ( ! self::is_migration_allowed() ) {
			add_filter( 'elementor/editor/localize_settings', [ $this, 'add_update_needed_flag' ] );
			add_action( 'elementor/admin/after_create_settings/' . Tools::PAGE_ID, [ $this, 'register_admin_tools_settings' ], 100 );

			if ( ! empty( $_POST ) ) { // phpcs:ignore -- nonce validation done in callback
				add_action( 'wp_ajax_' . self::NEEDS_UPDATE_OPTION . '_upgrade', [ $this, 'ajax_upgrade_to_fa5' ] );
			}
		}
	}
}
