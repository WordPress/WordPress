<?php

namespace Elementor\Modules\ContentSanitizer;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const WIDGET_TO_SANITIZE = 'heading';

	public function __construct() {
		parent::__construct();

		add_filter( 'elementor/document/save/data', [ $this, 'sanitize_content' ], 10, 2 );
	}

	public function get_name() {
		return 'content-sanitizer';
	}

	public function sanitize_content( $data, $document ): array {
		if ( current_user_can( 'manage_options' ) || empty( $data['elements'] ) ) {
			return $data;
		}

		if ( ! $this->is_widget_present( $data ) ) {
			return $data;
		}

		return Plugin::$instance->db->iterate_data( $data, function ( $element ) {
			if ( $this->is_target_widget( $element ) ) {
				$element['settings']['title'] = Plugin::$instance->widgets_manager->get_widget_types( self::WIDGET_TO_SANITIZE )->sanitize( $element['settings']['title'] );
			}

			return $element;
		});
	}

	private function is_target_widget( $element ) {
		return self::WIDGET_TO_SANITIZE === $element['widgetType'];
	}

	private function is_widget_present( array $elements ): bool {
		$json = wp_json_encode( $elements );

		return false !== strpos( $json, '"widgetType":"' . self::WIDGET_TO_SANITIZE . '"' );
	}
}
