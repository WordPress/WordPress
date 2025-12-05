<?php
namespace Elementor\Core\DynamicTags;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor tag.
 *
 * An abstract class to register new Elementor tag.
 *
 * @since 2.0.0
 * @abstract
 */
abstract class Tag extends Base_Tag {

	const WRAPPED_TAG = false;

	/**
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $options
	 *
	 * @return string
	 */
	public function get_content( array $options = [] ) {
		$settings = $this->get_settings();

		ob_start();

		$this->render();

		$value = ob_get_clean();

		if ( ! Utils::is_empty( $value ) ) {
			// TODO: fix spaces in `before`/`after` if WRAPPED_TAG ( conflicted with .elementor-tag { display: inline-flex; } );
			if ( ! Utils::is_empty( $settings, 'before' ) ) {
				$value = wp_kses_post( $settings['before'] ) . $value;
			}

			if ( ! Utils::is_empty( $settings, 'after' ) ) {
				$value .= wp_kses_post( $settings['after'] );
			}

			if ( static::WRAPPED_TAG ) :
				$value = '<span id="elementor-tag-' . esc_attr( $this->get_id() ) . '" class="elementor-tag">' . $value . '</span>';
			endif;

		} elseif ( ! Utils::is_empty( $settings, 'fallback' ) ) {
			$value = wp_kses_post_deep( $settings['fallback'] );
		}

		return $value;
	}

	/**
	 * @since 2.0.0
	 * @access public
	 */
	final public function get_content_type() {
		return 'ui';
	}

	/**
	 * @since 2.0.9
	 * @access public
	 */
	public function get_editor_config() {
		$config = parent::get_editor_config();

		$config['wrapped_tag'] = $this::WRAPPED_TAG;

		return $config;
	}

	/**
	 * @since 2.0.0
	 * @access protected
	 */
	protected function register_advanced_section() {
		$this->start_controls_section(
			'advanced',
			[
				'label' => esc_html__( 'Advanced', 'elementor' ),
			]
		);

		$this->add_control(
			'before',
			[
				'label' => esc_html__( 'Before', 'elementor' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'after',
			[
				'label' => esc_html__( 'After', 'elementor' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'fallback',
			[
				'label' => esc_html__( 'Fallback', 'elementor' ),
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->end_controls_section();
	}
}
