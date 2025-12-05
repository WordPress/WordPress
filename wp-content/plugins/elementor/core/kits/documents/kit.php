<?php
namespace Elementor\Core\Kits\Documents;

use Elementor\Core\DocumentTypes\PageBase;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Core\Settings\Page\Manager as PageManager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Kit extends PageBase {
	/**
	 * @var Tabs\Tab_Base[]
	 */
	private $tabs;

	public function __construct( array $data = [] ) {
		parent::__construct( $data );

		$this->register_tabs();
	}

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['has_elements'] = false;
		$properties['show_in_finder'] = false;
		$properties['show_on_admin_bar'] = false;
		$properties['edit_capability'] = 'edit_theme_options';
		$properties['support_kit'] = true;

		return $properties;
	}

	public static function get_type() {
		return 'kit';
	}

	public static function get_title() {
		return esc_html__( 'Kit', 'elementor' );
	}

	/**
	 * @return Tabs\Tab_Base[]
	 */
	public function get_tabs() {
		return $this->tabs;
	}

	/**
	 * Retrieve a tab by ID.
	 *
	 * @param $id
	 *
	 * @return Tabs\Tab_Base
	 */
	public function get_tab( $id ) {
		return self::get_items( $this->get_tabs(), $id );
	}

	protected function get_have_a_look_url() {
		return '';
	}

	public static function get_editor_panel_config() {
		$config = parent::get_editor_panel_config();
		$config['default_route'] = 'panel/global/menu';

		$config['needHelpUrl'] = 'https://go.elementor.com/global-settings/';

		return $config;
	}

	public function get_css_wrapper_selector() {
		return '.elementor-kit-' . $this->get_main_id();
	}

	public function save( $data ) {
		foreach ( $this->tabs as $tab ) {
			$data = $tab->before_save( $data );
		}

		$saved = parent::save( $data );

		if ( ! $saved ) {
			return false;
		}

		// Should set is_saving to true, to avoid infinite loop when updating
		// settings like: 'site_name" or "site_description".
		$this->set_is_saving( true );

		foreach ( $this->tabs as $tab ) {
			$tab->on_save( $data );
		}

		$this->set_is_saving( false );

		// When deleting a global color or typo, the css variable still exists in the frontend
		// but without any value and it makes the element to be un styled even if there is a default style for the base element,
		// for that reason this method removes css files of the entire site.
		Plugin::instance()->files_manager->clear_cache();

		return $saved;
	}

	/**
	 * Register a kit settings menu.
	 *
	 * @param $id
	 * @param $class_name
	 */
	public function register_tab( $id, $class_name ) {
		$this->tabs[ $id ] = new $class_name( $this );
	}

	/**
	 * @inheritDoc
	 */
	protected function get_initial_config() {
		$config = parent::get_initial_config();

		foreach ( $this->tabs as $id => $tab ) {
			$config['tabs'][ $id ] = [
				'id' => $id,
				'title' => $tab->get_title(),
				'icon' => $tab->get_icon(),
				'group' => $tab->get_group(),
				'helpUrl' => $tab->get_help_url(),
				'additionalContent' => $tab->get_additional_tab_content(),
			];
		}

		return $config;
	}

	/**
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->register_document_controls();

		foreach ( $this->tabs as $tab ) {
			$tab->register_controls();
		}
	}

	protected function get_post_statuses() {
		return [
			'draft' => sprintf( '%s (%s)', esc_html__( 'Disabled', 'elementor' ), esc_html__( 'Draft', 'elementor' ) ),
			'publish' => esc_html__( 'Published', 'elementor' ),
		];
	}

	public function add_repeater_row( $control_id, $item ) {
		$meta_key = PageManager::META_KEY;
		$document_settings = $this->get_meta( $meta_key );

		if ( ! $document_settings ) {
			$document_settings = [];
		}

		if ( ! isset( $document_settings[ $control_id ] ) ) {
			$document_settings[ $control_id ] = [];
		}

		$document_settings[ $control_id ][] = $item;

		$page_settings_manager = SettingsManager::get_settings_managers( 'page' );
		$page_settings_manager->save_settings( $document_settings, $this->get_id() );

		/** @var Kit $autosave */
		$autosave = $this->get_autosave();

		if ( $autosave ) {
			$autosave->add_repeater_row( $control_id, $item );
		}

		// Remove Post CSS.
		$post_css = Post_CSS::create( $this->post->ID );

		$post_css->delete();

		// Refresh Cache.
		Plugin::$instance->documents->get( $this->post->ID, false );

		$post_css = Post_CSS::create( $this->post->ID );

		$post_css->enqueue();
	}

	/**
	 * Register default tabs (menu pages) for site settings.
	 */
	private function register_tabs() {
		$tabs = [
			'global-colors' => Tabs\Global_Colors::class,
			'global-typography' => Tabs\Global_Typography::class,
			'theme-style-typography' => Tabs\Theme_Style_Typography::class,
			'theme-style-buttons' => Tabs\Theme_Style_Buttons::class,
			'theme-style-images' => Tabs\Theme_Style_Images::class,
			'theme-style-form-fields' => Tabs\Theme_Style_Form_Fields::class,
			'settings-site-identity' => Tabs\Settings_Site_Identity::class,
			'settings-background' => Tabs\Settings_Background::class,
			'settings-layout' => Tabs\Settings_Layout::class,
			'settings-lightbox' => Tabs\Settings_Lightbox::class,
			'settings-page-transitions' => Tabs\Settings_Page_Transitions::class,
			'settings-custom-css' => Tabs\Settings_Custom_CSS::class,
		];

		foreach ( $tabs as $id => $class ) {
			$this->register_tab( $id, $class );
		}

		do_action( 'elementor/kit/register_tabs', $this );
	}
}
