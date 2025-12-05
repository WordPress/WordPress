<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor image size control.
 *
 * A base control for creating image size control. Displays input fields to define
 * one of the default image sizes (thumbnail, medium, medium_large, large) or custom
 * image dimensions.
 *
 * @since 1.0.0
 */
class Group_Control_Image_Size extends Group_Control_Base {

	/**
	 * Fields.
	 *
	 * Holds all the image size control fields.
	 *
	 * @since 1.2.2
	 * @access protected
	 * @static
	 *
	 * @var array Image size control fields.
	 */
	protected static $fields;

	/**
	 * Get image size control type.
	 *
	 * Retrieve the control type, in this case `image-size`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return 'image-size';
	}

	/**
	 * Get attachment image HTML.
	 *
	 * Retrieve the attachment image HTML code.
	 *
	 * Note that some widgets use the same key for the media control that allows
	 * the image selection and for the image size control that allows the user
	 * to select the image size, in this case the third parameter should be null
	 * or the same as the second parameter. But when the widget uses different
	 * keys for the media control and the image size control, when calling this
	 * method you should pass the keys.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array  $settings       Control settings.
	 * @param string $image_size_key Optional. Settings key for image size.
	 *                               Default is `image`.
	 * @param string $image_key      Optional. Settings key for image. Default
	 *                               is null. If not defined uses image size key
	 *                               as the image key.
	 *
	 * @return string Image HTML.
	 */
	public static function get_attachment_image_html( $settings, $image_size_key = 'image', $image_key = null ) {
		if ( ! $image_key ) {
			$image_key = $image_size_key;
		}

		$image = $settings[ $image_key ];

		// Old version of image settings.
		if ( ! isset( $settings[ $image_size_key . '_size' ] ) ) {
			$settings[ $image_size_key . '_size' ] = '';
		}

		$size = $settings[ $image_size_key . '_size' ];

		$image_class = ! empty( $settings['hover_animation'] ) ? 'elementor-animation-' . $settings['hover_animation'] : '';

		$html = '';

		// If is the new version - with image size.
		$image_sizes = get_intermediate_image_sizes();

		$image_sizes[] = 'full';

		if ( ! empty( $image['id'] ) && ! wp_attachment_is_image( $image['id'] ) ) {
			$image['id'] = '';
		}

		$is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();

		// On static mode don't use WP responsive images.
		if ( ! empty( $image['id'] ) && in_array( $size, $image_sizes ) && ! $is_static_render_mode ) {
			$image_class .= " attachment-$size size-$size wp-image-{$image['id']}";
			$image_attr = [
				'class' => trim( $image_class ),
			];

			$html .= wp_get_attachment_image( $image['id'], $size, false, $image_attr );
		} else {
			$image_src = self::get_attachment_image_src( $image['id'], $image_size_key, $settings );

			if ( ! $image_src && isset( $image['url'] ) ) {
				$image_src = $image['url'];
			}

			if ( ! empty( $image_src ) ) {
				$image_class_html = ! empty( $image_class ) ? ' class="' . esc_attr( $image_class ) . '"' : '';

				$html .= sprintf(
					'<img src="%1$s" title="%2$s" alt="%3$s"%4$s loading="lazy" />',
					esc_url( $image_src ),
					esc_attr( Control_Media::get_image_title( $image ) ),
					esc_attr( Control_Media::get_image_alt( $image ) ),
					$image_class_html
				);
			}
		}

		/**
		 * Get Attachment Image HTML
		 *
		 * Filters the Attachment Image HTML
		 *
		 * @since 2.4.0
		 * @param string $html the attachment image HTML string
		 * @param array  $settings       Control settings.
		 * @param string $image_size_key Optional. Settings key for image size.
		 *                               Default is `image`.
		 * @param string $image_key      Optional. Settings key for image. Default
		 *                               is null. If not defined uses image size key
		 *                               as the image key.
		 */
		return apply_filters( 'elementor/image_size/get_attachment_image_html', $html, $settings, $image_size_key, $image_key );
	}

	/**
	 * Safe print attachment image HTML.
	 *
	 * @uses get_attachment_image_html.
	 *
	 * @access public
	 * @static
	 *
	 * @param array  $settings       Control settings.
	 * @param string $image_size_key Optional. Settings key for image size.
	 *                               Default is `image`.
	 * @param string $image_key      Optional. Settings key for image. Default
	 *                               is null. If not defined uses image size key
	 *                               as the image key.
	 */
	public static function print_attachment_image_html( array $settings, $image_size_key = 'image', $image_key = null ) {
		Utils::print_wp_kses_extended( self::get_attachment_image_html( $settings, $image_size_key, $image_key ), [ 'image' ] );
	}

	/**
	 * Get all image sizes.
	 *
	 * Retrieve available image sizes with data like `width`, `height` and `crop`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array An array of available image sizes.
	 */
	public static function get_all_image_sizes() {
		global $_wp_additional_image_sizes;

		$default_image_sizes = [ 'thumbnail', 'medium', 'medium_large', 'large' ];

		$image_sizes = [];

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ] = [
				'width' => (int) get_option( $size . '_size_w' ),
				'height' => (int) get_option( $size . '_size_h' ),
				'crop' => (bool) get_option( $size . '_crop' ),
			];
		}

		if ( $_wp_additional_image_sizes ) {
			$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		}

		/** This filter is documented in wp-admin/includes/media.php */
		return apply_filters( 'image_size_names_choose', $image_sizes );
	}

	/**
	 * Get attachment image src.
	 *
	 * Retrieve the attachment image source URL.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $attachment_id  The attachment ID.
	 * @param string $image_size_key Settings key for image size.
	 * @param array  $settings       Control settings.
	 *
	 * @return string Attachment image source URL.
	 */
	public static function get_attachment_image_src( $attachment_id, $image_size_key, array $settings ) {
		if ( empty( $attachment_id ) ) {
			return false;
		}

		$size = $settings[ $image_size_key . '_size' ];

		if ( 'custom' !== $size ) {
			$attachment_size = $size;
		} else {
			// Use BFI_Thumb script
			// TODO: Please rewrite this code.
			require_once ELEMENTOR_PATH . 'includes/libraries/bfi-thumb/bfi-thumb.php';

			$custom_dimension = $settings[ $image_size_key . '_custom_dimension' ];

			$attachment_size = [
				// Defaults sizes
				0 => null, // Width.
				1 => null, // Height.

				'bfi_thumb' => true,
				'crop' => true,
			];

			$has_custom_size = false;
			if ( ! empty( $custom_dimension['width'] ) ) {
				$has_custom_size = true;
				$attachment_size[0] = $custom_dimension['width'];
			}

			if ( ! empty( $custom_dimension['height'] ) ) {
				$has_custom_size = true;
				$attachment_size[1] = $custom_dimension['height'];
			}

			if ( ! $has_custom_size ) {
				$attachment_size = 'full';
			}
		}

		$image_src = wp_get_attachment_image_src( $attachment_id, $attachment_size );

		if ( empty( $image_src[0] ) && 'thumbnail' !== $attachment_size ) {
			$image_src = wp_get_attachment_image_src( $attachment_id );
		}

		return ! empty( $image_src[0] ) ? $image_src[0] : '';
	}

	/**
	 * Get child default arguments.
	 *
	 * Retrieve the default arguments for all the child controls for a specific group
	 * control.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @return array Default arguments for all the child controls.
	 */
	protected function get_child_default_args() {
		return [
			'include' => [],
			'exclude' => [],
		];
	}

	/**
	 * Init fields.
	 *
	 * Initialize image size control fields.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @return array Control fields.
	 */
	protected function init_fields() {
		$fields = [];

		$fields['size'] = [
			'label' => esc_html__( 'Image Resolution', 'elementor' ),
			'type' => Controls_Manager::SELECT,
		];

		$fields['custom_dimension'] = [
			'label' => esc_html__( 'Image Dimension', 'elementor' ),
			'type' => Controls_Manager::IMAGE_DIMENSIONS,
			'description' => esc_html__( 'You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'elementor' ),
			'condition' => [
				'size' => 'custom',
			],
		];

		return $fields;
	}

	/**
	 * Prepare fields.
	 *
	 * Process image size control fields before adding them to `add_control()`.
	 *
	 * @since 1.2.2
	 * @access protected
	 *
	 * @param array $fields Image size control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$image_sizes = $this->get_image_sizes();

		$args = $this->get_args();

		if ( ! empty( $args['default'] ) && isset( $image_sizes[ $args['default'] ] ) ) {
			$default_value = $args['default'];
		} else {
			// Get the first item for default value.
			$default_value = array_keys( $image_sizes );
			$default_value = array_shift( $default_value );
		}

		$fields['size']['options'] = $image_sizes;

		$fields['size']['default'] = $default_value;

		if ( ! isset( $image_sizes['custom'] ) ) {
			unset( $fields['custom_dimension'] );
		}

		return parent::prepare_fields( $fields );
	}

	/**
	 * Get image sizes.
	 *
	 * Retrieve available image sizes after filtering `include` and `exclude` arguments.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return array Filtered image sizes.
	 */
	private function get_image_sizes() {
		$wp_image_sizes = self::get_all_image_sizes();

		$args = $this->get_args();

		if ( $args['include'] ) {
			$wp_image_sizes = array_intersect_key( $wp_image_sizes, array_flip( $args['include'] ) );
		} elseif ( $args['exclude'] ) {
			$wp_image_sizes = array_diff_key( $wp_image_sizes, array_flip( $args['exclude'] ) );
		}

		$image_sizes = [];

		foreach ( $wp_image_sizes as $size_key => $size_attributes ) {
			$control_title = ucwords( str_replace( '_', ' ', $size_key ) );
			if ( is_array( $size_attributes ) ) {
				$control_title .= sprintf( ' - %d x %d', $size_attributes['width'], $size_attributes['height'] );
			}

			$image_sizes[ $size_key ] = $control_title;
		}

		$image_sizes['full'] = esc_html__( 'Full', 'elementor' );

		if ( ! empty( $args['include']['custom'] ) || ! in_array( 'custom', $args['exclude'] ) ) {
			$image_sizes['custom'] = esc_html__( 'Custom', 'elementor' );
		}

		return $image_sizes;
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the image size control. Used to return the
	 * default options while initializing the image size control.
	 *
	 * @since 1.9.0
	 * @access protected
	 *
	 * @return array Default image size control options.
	 */
	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}
