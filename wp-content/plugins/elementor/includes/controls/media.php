<?php
namespace Elementor;

use Elementor\Core\Utils\Hints;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor media control.
 *
 * A base control for creating a media chooser control. Based on the WordPress
 * media library. Used to select an image from the WordPress media library.
 *
 * @since 1.0.0
 */
class Control_Media extends Control_Base_Multiple {

	/**
	 * Get media control type.
	 *
	 * Retrieve the control type, in this case `media`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'media';
	}

	/**
	 * Get media control default values.
	 *
	 * Retrieve the default value of the media control. Used to return the default
	 * values while initializing the media control.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Control default value.
	 */
	public function get_default_value() {
		return [
			'url' => '',
			'id' => '',
			'size' => '',
		];
	}

	public function on_export( $settings ) {
		if ( ! empty( $settings['url'] ) ) {
			do_action( 'elementor/templates/collect_media_url', $settings['url'], $settings );
		}

		return $settings;
	}

	/**
	 * Import media images.
	 *
	 * Used to import media control files from external sites while importing
	 * Elementor template JSON file, and replacing the old data.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Control settings.
	 *
	 * @return array Control settings.
	 */
	public function on_import( $settings ) {
		if ( empty( $settings['url'] ) ) {
			return $settings;
		}

		$local_file_path = \Elementor\TemplateLibrary\Classes\Media_Mapper::get_local_file_path( $settings['url'] );
		$imported_attachment = false;

		if ( $local_file_path !== $settings['url'] && file_exists( $local_file_path ) ) {
			$imported_attachment = Plugin::$instance->templates_manager->get_import_images_instance()->import_local_file( $local_file_path );
		}

		if ( $imported_attachment ) {
			return $imported_attachment;
		}

		$settings = Plugin::$instance->templates_manager->get_import_images_instance()->import( $settings );

		if ( ! $settings ) {
			$settings = [
				'id' => '',
				'url' => Utils::get_placeholder_image_src(),
			];
		}

		return $settings;
	}

	/**
	 * Support SVG and JSON Import
	 *
	 * Called by the 'upload_mimes' filter. Adds SVG and JSON mime types to the list of WordPress' allowed mime types.
	 *
	 * @since 3.4.6
	 * @deprecated 3.5.0
	 *
	 * @param mixed $mimes
	 * @return mixed
	 */
	public function support_svg_and_json_import( $mimes ) {
		Plugin::$instance->modules_manager->get_modules( 'dev-tools' )->deprecation->deprecated_function( __METHOD__, '3.5.0' );

		return $mimes;
	}

	/**
	 * Enqueue media control scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by the media
	 * control.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue() {
		global $wp_version;

		$suffix = Utils::is_script_debug() ? '' : '.min';
		wp_enqueue_media();

		wp_enqueue_style(
			'media',
			admin_url( '/css/media' . $suffix . '.css' ),
			[],
			$wp_version
		);

		wp_register_script(
			'image-edit',
			'/wp-admin/js/image-edit' . $suffix . '.js',
			[
				'jquery',
				'json2',
				'imgareaselect',
			],
			$wp_version,
			true
		);

		wp_enqueue_script( 'image-edit' );
	}

	/**
	 * Render media control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		?>
		<#
			// For BC.
			if ( data.media_type ) {
				data.media_types = [ data.media_type ];
			}

			if ( data.should_include_svg_inline_option ) {
				data.media_types.push( 'svg' );
			}

			// Determine if the current media type is viewable.
			const isViewable = () => {
				const viewable = [
					'image',
					'video',
					'svg',
				];

				// Make sure that all media types are viewable.
				return data.media_types.every( ( type ) => viewable.includes( type ) );
			};

			// Get the preview type for the current media type.
			const getPreviewType = () => {
				if ( data.media_types.includes( 'video' ) ) {
					return 'video';
				}

				if ( data.media_types.includes( 'image' ) || data.media_types.includes( 'svg' ) ) {
					return 'image';
				}

				return 'none';
			}

			// Retrieve a button label by media type.
			const getButtonLabel = ( mediaType ) => {
				switch( mediaType ) {
					case 'image':
						return '<?php esc_html_e( 'Choose Image', 'elementor' ); ?>';

					case 'video':
						return '<?php esc_html_e( 'Choose Video', 'elementor' ); ?>';

					case 'svg':
						return '<?php esc_html_e( 'Choose SVG', 'elementor' ); ?>';

					default:
						return '<?php esc_html_e( 'Choose File', 'elementor' ); ?>';
				}
			}
		#>
		<div class="elementor-control-field elementor-control-media">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<#
			if ( isViewable() ) {
				let inputWrapperClasses = 'elementor-control-input-wrapper';

				if ( ! data.label_block ) {
					inputWrapperClasses += ' elementor-control-unit-5';
				}
			#>
				<div class="{{{ inputWrapperClasses }}}">
					<div class="elementor-control-media__content elementor-control-tag-area elementor-control-preview-area">
						<div class="elementor-control-media-area">
							<div class="elementor-control-media__remove elementor-control-media__content__remove" data-tooltip="<?php echo esc_attr__( 'Remove', 'elementor' ); ?>">
								<i class="eicon-trash-o" aria-hidden="true"></i>
								<span class="elementor-screen-only"><?php echo esc_html__( 'Remove', 'elementor' ); ?></span>
							</div>
							<#
								switch( getPreviewType() ) {
									case 'image':
										#>
										<div class="elementor-control-media__preview"></div>
										<#
										break;

									case 'video':
										#>
										<video class="elementor-control-media-video" preload="metadata"></video>
										<i class="eicon-video-camera" aria-hidden="true"></i>
										<#
										break;
								}
							#>
						</div>
						<div class="elementor-control-media-upload-button elementor-control-media__content__upload-button">
							<i class="eicon-plus-circle" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php echo esc_html__( 'Add', 'elementor' ); ?></span>
						</div>
						<div class="elementor-control-media__tools elementor-control-dynamic-switcher-wrapper">
							<#
								data.media_types.forEach( ( type ) => {
									#>
									<div class="elementor-control-media__tool elementor-control-media__replace" data-media-type="{{{ type }}}">{{{ getButtonLabel( type ) }}}</div>
									<#
								} );
							#>
						</div>
					</div>

					<?php
					/*
					?>
					<div class="elementor-control-media__warnings" role="alert" style="display: none;">
						<?php
						Hints::get_notice_template( [
							'type' => 'warning',
							'content' => esc_html__( 'This image doesn’t contain ALT text - which is necessary for accessibility and SEO.', 'elementor' ),
							'icon' => true,
						] );
						?>
					</div>
					<?php
					*/ ?>
					<?php $this->maybe_display_io_hints(); ?>
				</div>
			<# } /* endif isViewable() */ else { #>
				<div class="elementor-control-media__file elementor-control-preview-area">
					<div class="elementor-control-media__file__content">
						<div class="elementor-control-media__file__content__label"><?php echo esc_html__( 'Click the media icon to upload file', 'elementor' ); ?></div>
						<div class="elementor-control-media__file__content__info">
							<div class="elementor-control-media__file__content__info__icon">
								<i class="eicon-document-file"></i>
							</div>
							<div class="elementor-control-media__file__content__info__name"></div>
						</div>
					</div>
					<div class="elementor-control-media__file__controls">
						<div class="elementor-control-media__remove elementor-control-media__file__controls__remove" data-tooltip="<?php echo esc_attr__( 'Remove', 'elementor' ); ?>">
							<i class="eicon-trash-o" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php echo esc_html__( 'Remove', 'elementor' ); ?></span>
						</div>
						<div class="elementor-control-media__file__controls__upload-button elementor-control-media-upload-button" data-tooltip="<?php echo esc_attr__( 'Upload', 'elementor' ); ?>">
							<i class="eicon-upload" aria-hidden="true"></i>
							<span class="elementor-screen-only"><?php echo esc_html__( 'Upload', 'elementor' ); ?></span>
						</div>
					</div>
				</div>
			<# } #>
			<# if ( data.description ) { #>
				<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>

			<# if ( data.has_sizes ) { #>
			<div class="elementor-control-type-select e-control-image-size">
				<div class="elementor-control-field">
					<label class="elementor-control-title" data-e-responsive-switcher-sibling="false" for="<?php $this->print_control_uid( 'size' ); ?>"><?php echo esc_html__( 'Image Resolution', 'elementor' ); ?></label>
					<div class="elementor-control-input-wrapper elementor-control-unit-5">
						<select class="e-image-size-select" id="<?php $this->print_control_uid( 'size' ); ?>" data-setting="size">
							<?php foreach ( $this->get_image_sizes() as $size_key => $size_title ) : ?>
								<option value="<?php echo esc_attr( $size_key ); ?>"><?php echo esc_html( $size_title ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="elementor-control-field-description"><?php echo esc_html__( 'Image size settings don’t apply to Dynamic Images.', 'elementor' ); ?></div>
			</div>
			<# } #>

			<input type="hidden" data-setting="{{ data.name }}"/>
		</div>
		<?php
	}

	private function maybe_display_io_hints() {
		if ( Hints::should_display_hint( 'image-optimization' ) ) {
			$content_text = esc_html__( 'Optimize your images to enhance site performance by using Image Optimizer.', 'elementor' );
			$button_text = Hints::is_plugin_installed( 'image-optimization' ) ? esc_html__( 'Activate Plugin', 'elementor' ) : esc_html__( 'Install Plugin', 'elementor' );
			$action_url = Hints::get_plugin_action_url( 'image-optimization' );
		} elseif ( Hints::should_display_hint( 'image-optimization-connect' ) ) {
			$content_text = esc_html__( "This image isn't optimized. You need to connect your Image Optimizer account first.", 'elementor' );
			$button_text = esc_html__( 'Connect Now', 'elementor' );
			$action_url = admin_url( 'admin.php?page=image-optimization-settings' );
		} else {
			return;
		}

		?>
		<div class="elementor-control-media__promotions" role="alert" style="display: none;">
			<?php
			Hints::get_notice_template( [
				'display' => ! Hints::is_dismissed( 'image-optimization' ),
				'type' => 'info',
				'content' => $content_text,
				'icon' => true,
				'dismissible' => 'image_optimizer_hint',
				'button_text' => $button_text,
				'button_event' => 'image_optimizer_hint',
				'button_data' => [
					'action_url' => $action_url,
				],
			] ); ?>
		</div>
		<?php
	}

	private function get_image_sizes(): array {
		$wp_image_sizes = Group_Control_Image_Size::get_all_image_sizes();

		$image_sizes = [];

		foreach ( $wp_image_sizes as $size_key => $size_attributes ) {
			$control_title = ucwords( str_replace( '_', ' ', $size_key ) );
			if ( is_array( $size_attributes ) ) {
				$control_title .= sprintf( ' - %d x %d', $size_attributes['width'], $size_attributes['height'] );
			}

			$image_sizes[ $size_key ] = $control_title;
		}

		$image_sizes[''] = esc_html_x( 'Full', 'Image Size Control', 'elementor' );

		return $image_sizes;
	}

	/**
	 * Get media control default settings.
	 *
	 * Retrieve the default settings of the media control. Used to return the default
	 * settings while initializing the media control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'label_block' => true,
			'has_sizes' => false,
			'ai' => [
				'active' => true,
				'type' => 'media',
				'category' => 'photographic',
			],
			'media_types' => [
				'image',
			],
			'dynamic' => [
				'categories' => [ TagsModule::IMAGE_CATEGORY ],
				'returnType' => 'object',
			],
		];
	}

	/**
	 * Get media control image title.
	 *
	 * Retrieve the `title` of the image selected by the media control.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $attachment Media attachment.
	 *
	 * @return string Image title.
	 */
	public static function get_image_title( $attachment ) {
		if ( empty( $attachment['id'] ) ) {
			return '';
		}

		return get_the_title( $attachment['id'] );
	}

	/**
	 * Get media control image alt.
	 *
	 * Retrieve the `alt` value of the image selected by the media control.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $instance Media attachment.
	 *
	 * @return string Image alt.
	 */
	public static function get_image_alt( $instance ) {
		if ( empty( $instance['id'] ) ) {
			// For `Insert From URL` images.
			return isset( $instance['alt'] ) ? trim( self::sanitise_text( $instance['alt'] ) ) : '';
		}

		$attachment_id = $instance['id'];
		if ( ! $attachment_id ) {
			return '';
		}

		$attachment = get_post( $attachment_id );
		if ( ! $attachment ) {
			return '';
		}

		$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		if ( ! $alt ) {
			if ( Utils::has_invalid_post_permissions( $attachment ) ) {
				return '';
			}

			$alt = $attachment->post_excerpt;
			if ( ! $alt ) {
				$alt = $attachment->post_title;
			}
		}
		return trim( self::sanitise_text( $alt ) );
	}

	public function get_style_value( $css_property, $control_value, array $control_data ) {
		if ( 'URL' !== $css_property || empty( $control_value['id'] ) ) {
			return parent::get_style_value( $css_property, $control_value, $control_data );
		}

		if ( empty( $control_value['size'] ) ) {
			$control_value['size'] = 'full';
		}

		return wp_get_attachment_image_url( $control_value['id'], $control_value['size'] );
	}

	public static function sanitise_text( $text ) {
		return esc_attr( strip_tags( $text ) );
	}
}
