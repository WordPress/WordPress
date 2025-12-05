<?php
namespace Elementor\Modules\CloudLibrary;

use Elementor\Core\Frontend\RenderModes\Render_Mode_Base;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Render_Mode_Preview extends Render_Mode_Base {
	const ENQUEUE_SCRIPTS_PRIORITY = 1000;

	const MODE = 'cloud-template-preview';

	protected int $template_id;

	public function __construct( $template_id ) {
		$this->template_id = $template_id;
		$this->document = $this->create_document();

		Plugin::$instance->db->switch_to_post( $this->document->get_main_id() );

		Plugin::$instance->documents->switch_to_document( $this->document );

		add_filter( 'template_include', [ $this, 'filter_template' ] );

		add_action( 'wp_footer', [ $this, 'cleanup' ], 999 );

		parent::__construct( $this->document->get_main_id() );
	}

	public static function get_name() {
		return self::MODE;
	}

	public function prepare_render() {
		parent::prepare_render();
		show_admin_bar( false );
	}

	public function filter_template() {
		return ELEMENTOR_PATH . 'modules/page-templates/templates/canvas.php';
	}

	public function cleanup() {
		if ( $this->document && $this->document->get_main_id() ) {
			wp_delete_post( $this->document->get_main_id(), true );
		}
	}

	public function enqueue_scripts() {
		$suffix = ( Utils::is_script_debug() || Utils::is_elementor_tests() ) ? '' : '.min';

		wp_enqueue_script(
			'cloud-library-screenshot',
			ELEMENTOR_ASSETS_URL . "/js/cloud-library-screenshot{$suffix}.js",
			[],
			ELEMENTOR_VERSION,
			true
		);

		$config = [
			'selector' => '.elementor-' . $this->document->get_main_id(),
			'home_url' => home_url(),
			'post_id' => $this->document->get_main_id(),
			'template_id' => $this->template_id,
		];

		wp_add_inline_script( 'cloud-library-screenshot', 'var ElementorScreenshotConfig = ' . wp_json_encode( $config ) . ';' );
	}

	private function create_document() {
		if ( ! Plugin::$instance->common ) {
			Plugin::$instance->init_common();
		}

		$document = Plugin::$instance->templates_manager->get_source( 'cloud' )->create_document_for_preview( $this->template_id );

		if ( is_wp_error( $document ) ) {
			wp_die();
		}

		return $document;
	}
}
