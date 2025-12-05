<?php
namespace Elementor\Modules\PageTemplates;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Kits\Documents\Kit;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Core\DocumentTypes\PageBase;
use Elementor\Modules\Library\Documents\Page as LibraryPageDocument;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor page templates module.
 *
 * Elementor page templates module handler class is responsible for registering
 * and managing Elementor page templates modules.
 *
 * @since 2.0.0
 */
class Module extends BaseModule {

	/**
	 * The of the theme.
	 */
	const TEMPLATE_THEME = 'elementor_theme';

	/**
	 * Elementor Canvas template name.
	 */
	const TEMPLATE_CANVAS = 'elementor_canvas';

	/**
	 * Elementor Header & Footer template name.
	 */
	const TEMPLATE_HEADER_FOOTER = 'elementor_header_footer';

	/**
	 * Print callback.
	 *
	 * Holds the page template callback content.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var callable
	 */
	protected $print_callback;

	/**
	 * Get module name.
	 *
	 * Retrieve the page templates module name.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'page-templates';
	}

	/**
	 * Template include.
	 *
	 * Update the path for the Elementor Canvas template.
	 *
	 * Fired by `template_include` filter.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $template The path of the template to include.
	 *
	 * @return string The path of the template to include.
	 */
	public function template_include( $template ) {
		if ( is_singular() ) {
			$document = Plugin::$instance->documents->get_doc_for_frontend( get_the_ID() );

			if ( $document && $document::get_property( 'support_wp_page_templates' ) ) {
				$page_template = $document->get_meta( '_wp_page_template' );

				$template_path = $this->get_template_path( $page_template );

				if ( self::TEMPLATE_THEME !== $page_template && ! $template_path && $document->is_built_with_elementor() ) {
					$kit_default_template = Plugin::$instance->kits_manager->get_current_settings( 'default_page_template' );
					$template_path = $this->get_template_path( $kit_default_template );
				}

				if ( $template_path ) {
					$template = $template_path;

					Plugin::$instance->inspector->add_log( 'Page Template', Plugin::$instance->inspector->parse_template_path( $template ), $document->get_edit_url() );
				}
			}
		}

		return $template;
	}

	/**
	 * Add WordPress templates.
	 *
	 * Adds Elementor templates to all the post types that support
	 * Elementor.
	 *
	 * Fired by `init` action.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function add_wp_templates_support() {
		$post_types = get_post_types_by_support( 'elementor' );

		foreach ( $post_types as $post_type ) {
			add_filter( "theme_{$post_type}_templates", [ $this, 'add_page_templates' ], 10, 4 );
		}
	}

	/**
	 * Add page templates.
	 *
	 * Add the Elementor page templates to the theme templates.
	 *
	 * Fired by `theme_{$post_type}_templates` filter.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @param array     $page_templates Array of page templates. Keys are filenames, checks are translated names.
	 * @param \WP_Theme $wp_theme
	 * @param \WP_Post  $post
	 *
	 * @return array Page templates.
	 */
	public function add_page_templates( $page_templates, $wp_theme, $post ) {
		if ( $post ) {
			// FIX ME: Gutenberg not send $post as WP_Post object, just the post ID.
			$post_id = ! empty( $post->ID ) ? $post->ID : $post;

			$document = Plugin::$instance->documents->get( $post_id );
			if ( $document && ! $document::get_property( 'support_wp_page_templates' ) ) {
				return $page_templates;
			}
		}

		$page_templates = [
			self::TEMPLATE_CANVAS => esc_html__( 'Elementor Canvas', 'elementor' ),
			self::TEMPLATE_HEADER_FOOTER => esc_html__( 'Elementor Full Width', 'elementor' ),
			self::TEMPLATE_THEME => esc_html__( 'Theme', 'elementor' ),
		] + $page_templates;

		return $page_templates;
	}

	/**
	 * Set print callback.
	 *
	 * Set the page template callback.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param callable $callback
	 */
	public function set_print_callback( $callback ) {
		$this->print_callback = $callback;
	}

	/**
	 * Print callback.
	 *
	 * Prints the page template content using WordPress loop.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function print_callback() {
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
	}

	/**
	 * Print content.
	 *
	 * Prints the page template content.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function print_content() {
		if ( ! $this->print_callback ) {
			$this->print_callback = [ $this, 'print_callback' ];
		}

		call_user_func( $this->print_callback );
	}

	/**
	 * Get page template path.
	 *
	 * Retrieve the path for any given page template.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $page_template The page template name.
	 *
	 * @return string Page template path.
	 */
	public function get_template_path( $page_template ) {
		$template_path = '';
		switch ( $page_template ) {
			case self::TEMPLATE_CANVAS:
				$template_path = __DIR__ . '/templates/canvas.php';
				break;
			case self::TEMPLATE_HEADER_FOOTER:
				$template_path = __DIR__ . '/templates/header-footer.php';
				break;
		}

		return $template_path;
	}

	/**
	 * Register template control.
	 *
	 * Adds custom controls to any given document.
	 *
	 * Fired by `update_post_metadata` action.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param Document $document The document instance.
	 */
	public function action_register_template_control( $document ) {
		if (
			( $document instanceof PageBase || $document instanceof LibraryPageDocument ) &&
			$document::get_property( 'support_page_layout' )
		) {
			$this->register_template_control( $document );
		}
	}

	/**
	 * Register template control.
	 *
	 * Adds custom controls to any given document.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param Document $document   The document instance.
	 * @param string   $control_id Optional. The control ID. Default is `template`.
	 */
	public function register_template_control( $document, $control_id = 'template' ) {
		if ( ! Utils::is_cpt_custom_templates_supported() ) {
			return;
		}

		require_once ABSPATH . '/wp-admin/includes/template.php';

		$document->start_injection( [
			'of' => 'post_status',
			'fallback' => [
				'of' => 'post_title',
			],
		] );

		$control_options = [
			'options' => array_flip( get_page_templates( null, $document->get_main_post()->post_type ) ),
		];

		$this->add_template_controls( $document, $control_id, $control_options );

		$document->end_injection();
	}

	/**
	 * The $options variable is an array of $control_options to overwrite the default.
	 */
	public function add_template_controls( Document $document, $control_id, $control_options ) {
		// Default Control Options
		$default_control_options = [
			'label' => esc_html__( 'Page Layout', 'elementor' ),
			'type' => Controls_Manager::SELECT,
			'default' => 'default',
			'options' => [
				'default' => esc_html__( 'Default', 'elementor' ),
			],
		];

		$control_options = array_replace_recursive( $default_control_options, $control_options );

		$document->add_control(
			$control_id,
			$control_options
		);

		$document->add_control(
			$control_id . '_default_description',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>' . esc_html__( 'The default page template as defined in Elementor Panel → Hamburger Menu → Site Settings.', 'elementor' ) . '</b>',
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					$control_id => 'default',
				],
			]
		);

		$document->add_control(
			$control_id . '_theme_description',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>' . esc_html__( 'Default Page Template from your theme.', 'elementor' ) . '</b>',
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					$control_id => self::TEMPLATE_THEME,
				],
			]
		);

		$document->add_control(
			$control_id . '_canvas_description',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>' . esc_html__( 'No header, no footer, just Elementor', 'elementor' ) . '</b>',
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					$control_id => self::TEMPLATE_CANVAS,
				],
			]
		);

		$document->add_control(
			$control_id . '_header_footer_description',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>' . esc_html__( 'This template includes the header, full-width content and footer', 'elementor' ) . '</b>',
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					$control_id => self::TEMPLATE_HEADER_FOOTER,
				],
			]
		);

		if ( $document instanceof Kit ) {
			$document->add_control(
				'reload_preview_description',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => esc_html__( 'Changes will be reflected in the preview only after the page reloads.', 'elementor' ),
					'content_classes' => 'elementor-descriptor',
				]
			);
		}
	}

	/**
	 * Filter metadata update.
	 *
	 * Filters whether to update metadata of a specific type.
	 *
	 * Elementor don't allow WordPress to update the parent page template
	 * during `wp_update_post`.
	 *
	 * Fired by `update_{$meta_type}_metadata` filter.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param bool   $check     Whether to allow updating metadata for the given type.
	 * @param int    $object_id Object ID.
	 * @param string $meta_key  Meta key.
	 *
	 * @return bool Whether to allow updating metadata of a specific type.
	 */
	public function filter_update_meta( $check, $object_id, $meta_key ) {
		if ( '_wp_page_template' === $meta_key && Plugin::$instance->common ) {
			/** @var \Elementor\Core\Common\Modules\Ajax\Module $ajax */
			$ajax = Plugin::$instance->common->get_component( 'ajax' );

			$ajax_data = $ajax->get_current_action_data();

			$is_autosave_action = $ajax_data && 'save_builder' === $ajax_data['action'] && Document::STATUS_AUTOSAVE === $ajax_data['data']['status'];

			// Don't allow WP to update the parent page template.
			// (during `wp_update_post` from page-settings or save_plain_text).
			if ( $is_autosave_action && ! wp_is_post_autosave( $object_id ) && Document::STATUS_DRAFT !== get_post_status( $object_id ) ) {
				$check = false;
			}
		}

		return $check;
	}

	/**
	 * Page templates module constructor.
	 *
	 * Initializing Elementor page templates module.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'add_wp_templates_support' ] );

		add_filter( 'template_include', [ $this, 'template_include' ], 11 /* After Plugins/WooCommerce */ );

		add_action( 'elementor/documents/register_controls', [ $this, 'action_register_template_control' ] );

		add_filter( 'update_post_metadata', [ $this, 'filter_update_meta' ], 10, 3 );
	}
}
