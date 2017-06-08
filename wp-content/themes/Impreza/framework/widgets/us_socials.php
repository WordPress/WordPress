<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Socials
 *
 * Class US_Widget_Socials
 */
class US_Widget_Socials extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		parent::before_widget( $args, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$output = $args['before_widget'];

		if ( $title ) {
			$output .= '<h4>' . $title . '</h4>';
		}
		$socials_inline_css = '';
		if ( ! empty( $instance['size'] ) ) {
			$socials_inline_css = ' style="font-size: ' . $instance['size'] . ';"';
		}
		$output .= '<div class="w-socials align_left style_' . $instance['color'] . '"' . $socials_inline_css . '>';
		$output .= '<div class="w-socials-list">';

		if ( isset( $this->config['params'] ) AND is_array( $this->config['params'] ) ) {
			foreach ( $this->config['params'] as $param_name => $param ) {
				if ( $param_name == 'title' OR $param_name == 'size' OR $param_name == 'color' ) {
					// Not all the params are social keys
					continue;
				}
				if ( empty( $instance[ $param_name ] ) ) {
					continue;
				}
				$param['heading'] = isset( $param['heading'] ) ? $param['heading'] : $param_name;
				$value = $instance[ $param_name ];
				$link_target = ' target="_blank"';
				if ( $param_name == 'email' ) {
					$link_target = '';
					if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
						$value = 'mailto:' . $value;
					}
				} elseif ( $param_name == 'skype' ) {
					// Skype link may be some http(s): or skype: link. If protocol is not set, adding "skype:"
					if ( strpos( $value, ':' ) === FALSE ) {
						$value = 'skype:' . esc_attr( $value );
					}
				} else {
					$value = esc_url( $value );
				}
				$output .= '<div class="w-socials-item ' . $param_name . '">';
				$output .= '<a class="w-socials-item-link"' . $link_target . ' href="' . $value . '">';
				$output .= '<span class="w-socials-item-link-hover"></span></a>';
				$output .= '<div class="w-socials-item-popup"><span>' . $param['heading'] . '</span></div>';
				$output .= '</div>';
			}
		}

		$output .= '</div></div>';

		$output .= $args['after_widget'];

		echo $output;
	}
}
