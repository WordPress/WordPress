<?php
namespace Elementor\Core\Admin\UI\Components;

use Elementor\Core\Base\Base_Object;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Button extends Base_Object {

	private $options;

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'admin-button';
	}

	public function print_button() {
		$options = $this->get_options();

		if ( empty( $options['text'] ) ) {
			return;
		}

		$html_tag = ! empty( $options['url'] ) ? 'a' : 'button';
		$before = '';
		$icon = '';
		$attributes = [];

		if ( ! empty( $options['icon'] ) ) {
			$icon = '<i class="' . esc_attr( $options['icon'] ) . '"></i>';
		}

		$classes = $options['classes'];

		$default_classes = $this->get_default_options( 'classes' );

		$classes = array_merge( $classes, $default_classes );

		if ( ! empty( $options['type'] ) ) {
			$classes[] = 'e-button--' . $options['type'];
		}

		if ( ! empty( $options['variant'] ) ) {
			$classes[] = 'e-button--' . $options['variant'];
		}

		if ( ! empty( $options['before'] ) ) {
			$before = '<span>' . wp_kses_post( $options['before'] ) . '</span>';
		}

		if ( ! empty( $options['url'] ) ) {
			$attributes['href'] = $options['url'];
			if ( $options['new_tab'] ) {
				$attributes['target'] = '_blank';
			}
		}

		$attributes['class'] = $classes;

		$html = $before . '<' . $html_tag . ' ' . Utils::render_html_attributes( $attributes ) . '>';
		$html .= $icon;
		$html .= '<span>' . sanitize_text_field( $options['text'] ) . '</span>';
		$html .= '</' . $html_tag . '>';

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * @param string $option Optional default is null.
	 * @return array|mixed
	 */
	private function get_options( $option = null ) {
		return $this->get_items( $this->options, $option );
	}

	/**
	 * @param null $option
	 * @return array
	 */
	private function get_default_options( $option = null ) {
		$default_options = [
			'classes' => [ 'e-button' ],
			'icon' => '',
			'new_tab' => false,
			'text' => '',
			'type' => '',
			'url' => '',
			'variant' => '',
			'before' => '',
		];

		if ( null !== $option && -1 !== in_array( $option, $default_options, true ) ) {
			return $default_options[ $option ];
		}

		return $default_options;
	}

	public function __construct( array $options ) {
		$this->options = $this->merge_properties( $this->get_default_options(), $options );
	}
}
