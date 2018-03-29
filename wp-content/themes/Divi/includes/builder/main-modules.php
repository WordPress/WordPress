<?php
class ET_Builder_Module_Image extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Image', 'et_builder' );
		$this->slug       = 'et_pb_image';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'src',
			'alt',
			'title_text',
			'show_in_lightbox',
			'url',
			'url_new_window',
			'animation',
			'sticky',
			'align',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'force_fullwidth',
			'always_center_on_mobile',
			'use_overlay',
			'overlay_icon_color',
			'hover_overlay_color',
			'hover_icon',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited',
		);

		$animation_option_name = sprintf( '%1$s-animation', $this->slug );
		$global_animation_direction = ET_Global_Settings::get_value( $animation_option_name );
		$default_animation = $global_animation_direction && '' !== $global_animation_direction ? $global_animation_direction : 'left';

		$this->fields_defaults = array(
			'show_in_lightbox'        => array( 'off' ),
			'url_new_window'          => array( 'off' ),
			'animation'               => array( $default_animation ),
			'sticky'                  => array( 'off' ),
			'align'                   => array( 'left' ),
			'force_fullwidth'         => array( 'off' ),
			'always_center_on_mobile' => array( 'on' ),
			'use_overlay'             => array( 'off' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Image', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'alignment'  => esc_html__( 'Alignment', 'et_builder' ),
					'width'      => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_options = array(
			'border'                => array(),
			'custom_margin_padding' => array(
				'use_padding' => false,
				'css' => array(
					'important' => 'all',
				),
			),
		);
	}

	function get_fields() {
		// List of animation options
		$animation_options_list = array(
			'left'    => esc_html__( 'Left To Right', 'et_builder' ),
			'right'   => esc_html__( 'Right To Left', 'et_builder' ),
			'top'     => esc_html__( 'Top To Bottom', 'et_builder' ),
			'bottom'  => esc_html__( 'Bottom To Top', 'et_builder' ),
			'fade_in' => esc_html__( 'Fade In', 'et_builder' ),
			'off'     => esc_html__( 'No Animation', 'et_builder' ),
		);

		$animation_option_name       = sprintf( '%1$s-animation', $this->slug );
		$default_animation_direction = ET_Global_Settings::get_value( $animation_option_name );

		// If user modifies default animation option via Customizer, we'll need to change the order
		if ( 'left' !== $default_animation_direction && ! empty( $default_animation_direction ) && array_key_exists( $default_animation_direction, $animation_options_list ) ) {
			// The options, sans user's preferred direction
			$animation_options_wo_default = $animation_options_list;
			unset( $animation_options_wo_default[ $default_animation_direction ] );

			// All animation options
			$animation_options = array_merge(
				array( $default_animation_direction => $animation_options_list[$default_animation_direction] ),
				$animation_options_wo_default
			);
		} else {
			// Simply copy the animation options
			$animation_options = $animation_options_list;
		}

		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'affects'            => array(
					'alt',
					'title_text',
				),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
			),
			'alt' => array(
				'label'           => esc_html__( 'Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_default' => true,
				'depends_to'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'title_text' => array(
				'label'           => esc_html__( 'Image Title Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_default' => true,
				'depends_to'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML Title text.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'show_in_lightbox' => array(
				'label'             => esc_html__( 'Open in Lightbox', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( "No", 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'url',
					'url_new_window',
					'use_overlay'
				),
				'toggle_slug'       => 'link',
				'description'       => esc_html__( 'Here you can choose whether or not the image should open in Lightbox. Note: if you select to open the image in Lightbox, url options below will be ignored.', 'et_builder' ),
			),
			'url' => array(
				'label'           => esc_html__( 'Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'affects'         => array(
					'use_overlay',
				),
				'description'     => esc_html__( 'If you would like your image to be a link, input your destination URL here. No link will be created if this field is left blank.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'url_new_window' => array(
				'label'             => esc_html__( 'Url Opens', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'link',
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'use_overlay' => array(
				'label'             => esc_html__( 'Image Overlay', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects'           => array(
					'overlay_icon_color',
					'hover_overlay_color',
					'hover_icon',
				),
				'depends_default'   => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the image', 'et_builder' ),
			),
			'overlay_icon_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay icon', 'et_builder' ),
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'depends_show_if'     => 'on',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
				'description'         => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
			),
			'animation' => array(
				'label'             => esc_html__( 'Animation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => $animation_options,
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
			),
			'sticky' => array(
				'label'             => esc_html__( 'Remove Space Below The Image', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off'     => esc_html__( 'No', 'et_builder' ),
					'on'      => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'alignment',
				'description'       => esc_html__( 'Here you can choose whether or not the image should have a space below it.', 'et_builder' ),
			),
			'align' => array(
				'label'           => esc_html__( 'Image Alignment', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'left'   => esc_html__( 'Left', 'et_builder' ),
					'center' => esc_html__( 'Center', 'et_builder' ),
					'right'  => esc_html__( 'Right', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'alignment',
				'description'       => esc_html__( 'Here you can choose the image alignment.', 'et_builder' ),
			),
			'max_width' => array(
				'label'           => esc_html__( 'Image Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'max_width_last_edited' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'force_fullwidth' => array(
				'label'             => esc_html__( 'Force Fullwidth', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( "No", 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'always_center_on_mobile' => array(
				'label'             => esc_html__( 'Always Center Image On Mobile', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( "No", 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'alignment',
			),
			'max_width_tablet' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'max_width_phone' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$src                     = $this->shortcode_atts['src'];
		$alt                     = $this->shortcode_atts['alt'];
		$title_text              = $this->shortcode_atts['title_text'];
		$animation               = $this->shortcode_atts['animation'];
		$url                     = $this->shortcode_atts['url'];
		$url_new_window          = $this->shortcode_atts['url_new_window'];
		$show_in_lightbox        = $this->shortcode_atts['show_in_lightbox'];
		$sticky                  = $this->shortcode_atts['sticky'];
		$align                   = $this->shortcode_atts['align'];
		$max_width               = $this->shortcode_atts['max_width'];
		$max_width_tablet        = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone         = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited   = $this->shortcode_atts['max_width_last_edited'];
		$force_fullwidth         = $this->shortcode_atts['force_fullwidth'];
		$always_center_on_mobile = $this->shortcode_atts['always_center_on_mobile'];
		$overlay_icon_color      = $this->shortcode_atts['overlay_icon_color'];
		$hover_overlay_color     = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon              = $this->shortcode_atts['hover_icon'];
		$use_overlay             = $this->shortcode_atts['use_overlay'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( 'on' === $always_center_on_mobile ) {
			$module_class .= ' et_always_center_on_mobile';
		}

		// overlay can be applied only if image has link or if lightbox enabled
		$is_overlay_applied = 'on' === $use_overlay && ( 'on' === $show_in_lightbox || ( 'off' === $show_in_lightbox && '' !== $url ) ) ? 'on' : 'off';

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );

			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ? $max_width_phone : '',
			);

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name );
		}

		if ( 'on' === $force_fullwidth ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% img',
				'declaration' => 'width: 100%;',
			) );		}

		if ( $this->fields_defaults['align'][0] !== $align ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'text-align: %1$s;',
					esc_html( $align )
				),
			) );
		}

		if ( 'center' !== $align ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'margin-%1$s: 0;',
					esc_html( $align )
				),
			) );
		}

		if ( 'on' === $is_overlay_applied ) {
			if ( '' !== $overlay_icon_color ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_overlay:before',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $overlay_icon_color )
					),
				) );
			}

			if ( '' !== $hover_overlay_color ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_overlay',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $hover_overlay_color )
					),
				) );
			}

			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$overlay_output = sprintf(
				'<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		$output = sprintf(
			'<img src="%1$s" alt="%2$s"%3$s />
			%4$s',
			esc_url( $src ),
			esc_attr( $alt ),
			( '' !== $title_text ? sprintf( ' title="%1$s"', esc_attr( $title_text ) ) : '' ),
			'on' === $is_overlay_applied ? $overlay_output : ''
		);

		if ( 'on' === $show_in_lightbox ) {
			$output = sprintf( '<a href="%1$s" class="et_pb_lightbox_image" title="%3$s">%2$s</a>',
				esc_url( $src ),
				$output,
				esc_attr( $alt )
			);
		} else if ( '' !== $url ) {
			$output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				$output,
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		}

		$animation = '' === $animation ? ET_Global_Settings::get_value( 'et_pb_image-animation' ) : $animation;

		$output = sprintf(
			'<div%5$s class="et_pb_module et-waypoint et_pb_image%2$s%3$s%4$s%6$s">
				%1$s
			</div>',
			$output,
			esc_attr( " et_pb_animation_{$animation}" ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			( 'on' === $sticky ? esc_attr( ' et_pb_image_sticky' ) : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			'on' === $is_overlay_applied ? ' et_pb_has_overlay' : ''
		);

		return $output;
	}
}
new ET_Builder_Module_Image;

class ET_Builder_Module_Gallery extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Gallery', 'et_builder' );
		$this->slug       = 'et_pb_gallery';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'src',
			'gallery_ids',
			'gallery_orderby',
			'fullwidth',
			'posts_number',
			'show_title_and_caption',
			'show_pagination',
			'background_layout',
			'auto',
			'auto_speed',
			'admin_label',
			'module_id',
			'module_class',
			'zoom_icon_color',
			'hover_overlay_color',
			'hover_icon',
			'orientation',
		);

		$this->fields_defaults = array(
			'fullwidth'              => array( 'off' ),
			'posts_number'           => array( 4, 'add_default_setting' ),
			'show_title_and_caption' => array( 'on' ),
			'show_pagination'        => array( 'on' ),
			'background_layout'      => array( 'light' ),
			'auto'                   => array( 'off' ),
			'auto_speed'             => array( '7000' ),
			'orientation'            => array( 'landscape' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Images', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'  => esc_html__( 'Layout', 'et_builder' ),
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'text'    => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->main_css_element = '%%order_class%%.et_pb_gallery';
		$this->advanced_options = array(
			'fonts' => array(
				'title'   => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_gallery_title",
					),
				),
				'caption' => array(
					'label'    => esc_html__( 'Caption', 'et_builder' ),
					'use_all_caps' => true,
					'css'      => array(
						'main' => "{$this->main_css_element} .mfp-title, {$this->main_css_element} .et_pb_gallery_caption",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'depends_show_if'   => 'off'
				),
			),
			'border' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_gallery_item",
				),
			),
		);

		$this->custom_css_options = array(
			'gallery_item' => array(
				'label'       => esc_html__( 'Gallery Item', 'et_builder' ),
				'selector'    => '.et_pb_gallery_item',
			),
			'overlay' => array(
				'label'       => esc_html__( 'Overlay', 'et_builder' ),
				'selector'    => '.et_overlay',
			),
			'overlay_icon' => array(
				'label'       => esc_html__( 'Overlay Icon', 'et_builder' ),
				'selector'    => '.et_overlay:before',
			),
			'gallery_item_title' => array(
				'label'       => esc_html__( 'Gallery Item Title', 'et_builder' ),
				'selector'    => '.et_pb_gallery_title',
			),
			'gallery_item_caption' => array(
				'label'       => esc_html__( 'Gallery Item Caption', 'et_builder' ),
				'selector'    => '.et_pb_gallery_caption',
			),
			'gallery_pagination' => array(
				'label'       => esc_html__( 'Gallery Pagination', 'et_builder' ),
				'selector'    => '.et_pb_gallery_pagination',
			),
			'gallery_pagination_active' => array(
				'label'       => esc_html__( 'Pagination Active Page', 'et_builder' ),
				'selector'    => '.et_pb_gallery_pagination a.active',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src' => array(
				'label'           => esc_html__( 'Gallery Images', 'et_builder' ),
				'renderer'        => 'et_builder_get_gallery_settings',
				'option_category' => 'basic_option',
				'overwrite'       => array(
					'ids'         => 'gallery_ids',
					'orderby'     => 'gallery_orderby',
				),
				'toggle_slug'     => 'main_content',
			),
			'gallery_ids' => array(
				'type'  => 'hidden',
				'class' => array( 'et-pb-gallery-ids-field' ),
				'computed_affects'   => array(
					'__gallery',
				),
			),
			'gallery_orderby' => array(
				'label' => esc_html__( 'Gallery Images', 'et_builder' ),
				'type'  => 'hidden',
				'class' => array( 'et-pb-gallery-ids-field' ),
				'computed_affects'   => array(
					'__gallery',
				),
				'toggle_slug' => 'main_content',
			),
			'fullwidth' => array(
				'label'             => esc_html__( 'Layout', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Grid', 'et_builder' ),
					'on'  => esc_html__( 'Slider', 'et_builder' ),
				),
				'description'       => esc_html__( 'Toggle between the various blog layout types.', 'et_builder' ),
				'affects'           => array(
					'zoom_icon_color',
					'caption_font',
					'caption_text_color',
					'caption_line_height',
					'caption_font_size',
					'caption_all_caps',
					'caption_letter_spacing',
					'hover_overlay_color',
					'auto',
					'posts_number',
					'show_title_and_caption',
					'orientation'
				),
				'computed_affects'   => array(
					'__gallery',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'posts_number' => array(
				'label'             => esc_html__( 'Images Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Define the number of images that should be displayed per page.', 'et_builder' ),
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'main_content',
			),
			'orientation'            => array(
				'label'              => esc_html__( 'Thumbnail Orientation', 'et_builder' ),
				'type'               => 'select',
				'options_category'   => 'configuration',
				'options'            => array(
					'landscape' => esc_html__( 'Landscape', 'et_builder' ),
					'portrait'  => esc_html__( 'Portrait', 'et_builder' )
				),
				'description'        => sprintf(
					'%1$s<br><small><em><strong>%2$s:</strong> %3$s <a href="//wordpress.org/plugins/force-regenerate-thumbnails" target="_blank">%4$s</a>.</em></small>',
					esc_html__( 'Choose the orientation of the gallery thumbnails.', 'et_builder' ),
					esc_html__( 'Note', 'et_builder' ),
					esc_html__( 'If this option appears to have no effect, you might need to', 'et_builder' ),
					esc_html__( 'regenerate your thumbnails', 'et_builder')
				),
				'depends_show_if'    => 'off',
				'computed_affects'   => array(
					'__gallery',
				),
				'tab_slug'           => 'advanced',
				'toggle_slug'        => 'layout',
			),
			'show_title_and_caption' => array(
				'label'              => esc_html__( 'Show Title and Caption', 'et_builder' ),
				'type'               => 'yes_no_button',
				'option_category'    => 'configuration',
				'options'            => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'        => esc_html__( 'Whether or not to show the title and caption for images (if available).', 'et_builder' ),
				'depends_show_if'    => 'off',
				'toggle_slug'        => 'elements',
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'        => 'elements',
				'description'        => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'color_option',
				'options'           => array(
					'light'  => esc_html__( 'Dark', 'et_builder' ),
					'dark' => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'auto' => array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
				),
				'depends_show_if'   => 'on',
				'depends_to'        => array(
					'fullwidth',
				),
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
			),
			'auto_speed' => array(
				'label'             => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_default'   => true,
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
			),
			'zoom_icon_color' => array(
				'label'             => esc_html__( 'Zoom Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__gallery' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Gallery', 'get_gallery' ),
				'computed_depends_on' => array(
					'gallery_ids',
					'gallery_orderby',
					'fullwidth',
					'orientation',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get attachment data for gallery module
	 *
	 * @param array $args {
	 *     Gallery Options
	 *
	 *     @type array  $gallery_ids     Attachment Ids of images to be included in gallery.
	 *     @type string $gallery_orderby `orderby` arg for query. Optional.
	 *     @type string $fullwidth       on|off to determine grid / slider layout
	 *     @type string $orientation     Orientation of thumbnails (landscape|portrait).
	 * }
	 * @param array $conditional_tags
	 * @param array $current_page
	 *
	 * @return array Attachments data
	 */
	static function get_gallery( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$attachments = array();

		$defaults = array(
			'gallery_ids'     => array(),
			'gallery_orderby' => '',
			'fullwidth'       => 'off',
			'orientation'     => 'landscape',
		);

		$args = wp_parse_args( $args, $defaults );

		$attachments_args = array(
			'include'        => $args['gallery_ids'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'post__in',
		);

		if ( 'rand' === $args['gallery_orderby'] ) {
			$attachments_args['orderby'] = 'rand';
		}

		if ( 'on' === $args['fullwidth'] ) {
			$width  = 1080;
			$height = 9999;
		} else {
			$width  =  400;
			$height = ( 'landscape' === $args['orientation'] ) ? 284 : 516;
		}

		$width  = (int) apply_filters( 'et_pb_gallery_image_width', $width );
		$height = (int) apply_filters( 'et_pb_gallery_image_height', $height );

		$_attachments = get_posts( $attachments_args );

		foreach ( $_attachments as $key => $val ) {
			$attachments[$key] = $_attachments[$key];
			$attachments[$key]->image_src_full  = wp_get_attachment_image_src( $val->ID, 'full' );
			$attachments[$key]->image_src_thumb = wp_get_attachment_image_src( $val->ID, array( $width, $height ) );
		}

		return $attachments;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id              = $this->shortcode_atts['module_id'];
		$module_class           = $this->shortcode_atts['module_class'];
		$gallery_ids            = $this->shortcode_atts['gallery_ids'];
		$fullwidth              = $this->shortcode_atts['fullwidth'];
		$show_title_and_caption = $this->shortcode_atts['show_title_and_caption'];
		$background_layout      = $this->shortcode_atts['background_layout'];
		$posts_number           = $this->shortcode_atts['posts_number'];
		$show_pagination        = $this->shortcode_atts['show_pagination'];
		$gallery_orderby        = $this->shortcode_atts['gallery_orderby'];
		$zoom_icon_color        = $this->shortcode_atts['zoom_icon_color'];
		$hover_overlay_color    = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon             = $this->shortcode_atts['hover_icon'];
		$auto                   = $this->shortcode_atts['auto'];
		$auto_speed             = $this->shortcode_atts['auto_speed'];
		$orientation            = $this->shortcode_atts['orientation'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $zoom_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $zoom_icon_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay',
				'declaration' => sprintf(
					'background-color: %1$s;
					border-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		// Get gallery item data
		$attachments = self::get_gallery( array(
			'gallery_ids'     => $gallery_ids,
			'gallery_orderby' => $gallery_orderby,
			'fullwidth'       => $fullwidth,
			'orientation'     => $orientation,
		) );

		if ( empty( $attachments ) ) {
			return '';
		}

		wp_enqueue_script( 'hashchange' );

		$fullwidth_class = 'on' === $fullwidth ?  ' et_pb_slider et_pb_gallery_fullwidth' : ' et_pb_gallery_grid';
		$background_class = " et_pb_bg_layout_{$background_layout}";

		$module_class .= 'on' === $auto && 'on' === $fullwidth ? ' et_slider_auto et_slider_speed_' . esc_attr( $auto_speed ) : '';

		$posts_number = 0 === intval( $posts_number ) ? 4 : intval( $posts_number );

		$output = sprintf(
			'<div%1$s class="et_pb_module et_pb_gallery%2$s%3$s%4$s clearfix">
				<div class="et_pb_gallery_items et_post_gallery" data-per_page="%5$d">',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			esc_attr( $fullwidth_class ),
			esc_attr( $background_class ),
			esc_attr( $posts_number )
		);

		foreach ( $attachments as $id => $attachment ) {
			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$image_output = sprintf(
				'<a href="%1$s" title="%2$s">
					<img src="%3$s" alt="%2$s" />
					<span class="et_overlay%4$s"%5$s></span>
				</a>',
				esc_url( $attachment->image_src_full[0] ),
				esc_attr( $attachment->post_title ),
				esc_url( $attachment->image_src_thumb[0] ),
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);

			$output .= sprintf(
				'<div class="et_pb_gallery_item%2$s%1$s">',
				esc_attr( $background_class ),
				( 'on' !== $fullwidth ? ' et_pb_grid_item' : '' )
			);
			$output .= "
				<div class='et_pb_gallery_image {$orientation}'>
					$image_output
				</div>";

			if ( 'on' !== $fullwidth && 'on' === $show_title_and_caption ) {
				if ( trim($attachment->post_title) ) {
					$output .= "
						<h3 class='et_pb_gallery_title'>
						" . wptexturize($attachment->post_title) . "
						</h3>";
				}
				if ( trim($attachment->post_excerpt) ) {
				$output .= "
						<p class='et_pb_gallery_caption'>
						" . wptexturize($attachment->post_excerpt) . "
						</p>";
				}
			}
			$output .= "</div>";
		}

		$output .= "</div><!-- .et_pb_gallery_items -->";

		if ( 'on' !== $fullwidth && 'on' === $show_pagination ) {
			$output .= "<div class='et_pb_gallery_pagination'></div>";
		}

		$output .= "</div><!-- .et_pb_gallery -->";

		return $output;
	}
}
new ET_Builder_Module_Gallery;

class ET_Builder_Module_Video extends ET_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Video', 'et_builder' );
		$this->slug = 'et_pb_video';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'src',
			'src_webm',
			'image_src',
			'play_icon_color',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Video', 'et_builder' ),
					'overlay'      => esc_html__( 'Overlay', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'play_icon' => esc_html__( 'Play Icon', 'et_builder' ),
				),
			),
		);

		$this->custom_css_options = array(
			'video_icon' => array(
				'label'    => esc_html__( 'Video Icon', 'et_builder' ),
				'selector' => '.et_pb_video_play',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Video MP4/URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects' => array(
					'__video',
				),
			),
			'src_webm' => array(
				'label'              => esc_html__( 'Video Webm', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload the .WEBM version of your video here. All uploaded videos should be in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects' => array(
					'__video',
				),
			),
			'image_src' => array(
				'label'              => esc_html__( 'Image Overlay URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'additional_button'  => sprintf(
					'<input type="button" class="button et-pb-video-image-button" value="%1$s" />',
					esc_attr__( 'Generate From Video', 'et_builder' )
				),
				'additional_button_type' => 'generate_image_url_from_video',
				'additional_button_attrs' => array(
					'video_source' => 'src',
				),
				'classes'            => 'et_pb_video_overlay',
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display over your video. You can also generate a still image from your video.', 'et_builder' ),
				'toggle_slug'        => 'overlay',
				'computed_affects' => array(
					'__video_cover_src',
				),
			),
			'play_icon_color' => array(
				'label'             => esc_html__( 'Play Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'play_icon',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__video' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Video', 'get_video' ),
				'computed_depends_on' => array(
					'src',
					'src_webm',
				),
				'computed_minimum' => array(
					'src',
					'src_webm',
				),
			),
			'__video_cover_src' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Video', 'get_video_cover_src' ),
				'computed_depends_on' => array(
					'image_src',
				),
				'computed_minimum' => array(
					'image_src',
				),
			),

		);
		return $fields;
	}

	static function get_video( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'src'      => '',
			'src_webm' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$video_src = '';

		if ( false !== et_pb_check_oembed_provider( esc_url( $args['src'] ) ) ) {
			$video_src = wp_oembed_get( esc_url( $args['src'] ) );
		} else {
			$video_src = sprintf( '
				<video controls>
					%1$s
					%2$s
				</video>',
				( '' !== $args['src'] ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $args['src'] ) ) : '' ),
				( '' !== $args['src_webm'] ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $args['src_webm'] ) ) : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		return $video_src;
	}

	static function get_video_cover_src( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'image_src' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$image_output = '';

		if ( '' !== $args['image_src'] ) {
			$image_output = et_pb_set_video_oembed_thumbnail_resolution( $args['image_src'], 'high' );
		}

		return $image_output;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id       = $this->shortcode_atts['module_id'];
		$module_class    = $this->shortcode_atts['module_class'];
		$src             = $this->shortcode_atts['src'];
		$src_webm        = $this->shortcode_atts['src_webm'];
		$image_src       = $this->shortcode_atts['image_src'];
		$play_icon_color = $this->shortcode_atts['play_icon_color'];

		$video_src       = self::get_video( array(
			'src'      => $src,
			'src_webm' => $src_webm,
		) );

		$image_output = self::get_video_cover_src( array(
			'image_src' => $image_src,
		) );

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $play_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_video_play',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $play_icon_color )
				),
			) );
		}

		$output = sprintf(
			'<div%2$s class="et_pb_module et_pb_video%3$s">
				<div class="et_pb_video_box">
					%1$s
				</div>
				%4$s
			</div>',
			( '' !== $video_src ? $video_src : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $image_output
				? sprintf(
					'<div class="et_pb_video_overlay" style="background-image: url(%1$s);">
						<div class="et_pb_video_overlay_hover">
							<a href="#" class="et_pb_video_play"></a>
						</div>
					</div>',
					esc_attr( $image_output )
				)
				: ''
			)
		);

		return $output;
	}
}
new ET_Builder_Module_Video;

class ET_Builder_Module_Video_Slider extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Video Slider', 'et_builder' );
		$this->slug            = 'et_pb_video_slider';
		$this->fb_support 	   = true;
		$this->child_slug      = 'et_pb_video_slider_item';
		$this->child_item_text = esc_html__( 'Video', 'et_builder' );

		$this->whitelisted_fields = array(
			'show_image_overlay',
			'show_arrows',
			'show_thumbnails',
			'controls_color',
			'admin_label',
			'module_id',
			'module_class',
			'play_icon_color',
			'thumbnail_overlay_color',
		);

		$this->fields_defaults = array(
			'show_image_overlay' => array( 'hide' ),
			'show_arrows'        => array( 'on' ),
			'show_thumbnails'    => array( 'on' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements' => esc_html__( 'Elements', 'et_builder' ),
					'overlay'  => esc_html__( 'Overlay', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'colors' => esc_html__( 'Controls Colors', 'et_builder' ),
				),
			),
		);

		$this->custom_css_options = array(
			'play_button' => array(
				'label'    => esc_html__( 'Play Button', 'et_builder' ),
				'selector' => '.et_pb_video_play',
			),
			'thumbnail_item' => array(
				'label'    => esc_html__( 'Thumbnail Item', 'et_builder' ),
				'selector' => '.et_pb_carousel_item',
			),
			'arrows' => array(
				'label'    => esc_html__( 'Slider Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_image_overlay' => array(
				'label'           => esc_html__( 'Display Image Overlays on Main Video', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'hide' => esc_html__( 'Hide', 'et_builder' ),
					'show' => esc_html__( 'Show', 'et_builder' ),
				),
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'This option will cover the player UI on the main video. This image can either be uploaded in each video setting or auto-generated by Divi.', 'et_builder' ),
			),
			'show_arrows' => array(
				'label'           => esc_html__( 'Arrows', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Show Arrows', 'et_builder' ),
					'off' => esc_html__( 'Hide Arrows', 'et_builder' ),
				),
				'toggle_slug'        => 'elements',
				'description'        => esc_html__( 'This setting will turn on and off the navigation arrows.', 'et_builder' ),
			),
			'show_thumbnails' => array(
				'label'             => esc_html__( 'Slider Controls', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Use Thumbnail Track', 'et_builder' ),
					'off' => esc_html__( 'Use Dot Navigation', 'et_builder' ),
				),
				'toggle_slug'        => 'elements',
				'description'        => esc_html__( 'This setting will let you choose to use the thumbnail track controls below the slider or dot navigation at the bottom of the slider.', 'et_builder' ),
			),
			'controls_color' => array(
				'label'             => esc_html__( 'Slider Controls Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'color_option',
				'options'           => array(
					'light' => esc_html__( 'Light', 'et_builder' ),
					'dark'  => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'colors',
				'description'       => esc_html__( 'This setting will make your slider controls either light or dark in color. Slider controls are either the arrows on the thumbnail track or the circles in dot navigation.', 'et_builder' ),
			),
			'play_icon_color' => array(
				'label'             => esc_html__( 'Play Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'colors',
			),
			'thumbnail_overlay_color' => array(
				'label'             => esc_html__( 'Thumbnail Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'colors',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_slider_image_overlay;

		$show_image_overlay = $this->shortcode_atts['show_image_overlay'];

		$et_pb_slider_image_overlay = $show_image_overlay;

	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$show_arrows        = $this->shortcode_atts['show_arrows'];
		$show_thumbnails    = $this->shortcode_atts['show_thumbnails'];
		$controls_color     = $this->shortcode_atts['controls_color'];
		$play_icon_color = $this->shortcode_atts['play_icon_color'];
		$thumbnail_overlay_color = $this->shortcode_atts['thumbnail_overlay_color'];

		global $et_pb_slider_image_overlay;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $play_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_video_play, %%order_class%% .et_pb_carousel .et_pb_video_play',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $play_icon_color )
				),
			) );
		}

		if ( '' !== $thumbnail_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_carousel_item .et_pb_video_overlay_hover:hover, %%order_class%%.et_pb_video_slider .et_pb_slider:hover .et_pb_video_overlay_hover, %%order_class%% .et_pb_carousel_item.et-pb-active-control .et_pb_video_overlay_hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $thumbnail_overlay_color )
				),
			) );
		}

		$class  = '';
		$class .= 'off' === $show_arrows ? ' et_pb_slider_no_arrows' : '';
		$class .= 'on' === $show_thumbnails ? ' et_pb_slider_carousel et_pb_slider_no_pagination' : '';
		$class .= 'off' === $show_thumbnails ? ' et_pb_slider_dots' : '';
		$class .= " et_pb_controls_{$controls_color}";

		$content = $this->shortcode_content;

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_video_slider%4$s">
				<div class="et_pb_slider et_pb_preload%1$s">
					<div class="et_pb_slides">
						%2$s
					</div> <!-- .et_pb_slides -->
				</div> <!-- .et_pb_slider -->
			</div> <!-- .et_pb_video_slider -->
			',
			esc_attr( $class ),
			$content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Video_Slider;

class ET_Builder_Module_Video_Slider_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Video', 'et_builder' );
		$this->slug                        = 'et_pb_video_slider_item';
		$this->fb_support 				   = true;
		$this->type                        = 'child';
		$this->custom_css_tab              = false;
		$this->child_title_var             = 'admin_title';
		$this->advanced_setting_title_text = esc_html__( 'New Video', 'et_builder' );
		$this->settings_text               = esc_html__( 'Video Settings', 'et_builder' );

		$this->whitelisted_fields = array(
			'admin_title',
			'src',
			'src_webm',
			'image_src',
			'background_layout',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Video', 'et_builder' ),
					'overlay'      => esc_html__( 'Overlay', 'et_builder' ),
					'admin_label'  => esc_html__( 'Admin Label', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'arrows_color' => esc_html__( 'Arrows Color', 'et_builder' ),
				),
			),
		);

		$this->fields_defaults = array(
			'background_layout' => array( 'dark' ),
		);
	}

	function get_fields() {
		$fields = array(
			'admin_title' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the video in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'src' => array(
				'label'              => esc_html__( 'Video MP4/URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects' => array(
					'__get_oembed',
					'__oembed_thumbnail',
					'__is_oembed',
				),
			),
			'src_webm' => array(
				'label'              => esc_html__( 'Video Webm', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload the .WEBM version of your video here. All uploaded videos should be in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
			),
			'image_src' => array(
				'label'              => esc_html__( 'Image Overlay URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'additional_button'  => sprintf(
					'<input type="button" class="button et-pb-video-image-button" value="%1$s" />',
					esc_attr__( 'Generate From Video', 'et_builder' )
				),
				'additional_button_type' => 'generate_image_url_from_video',
				'additional_button_attrs' => array(
					'video_source' => 'src',
				),
				'classes'            => 'et_pb_video_overlay',
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display over your video. You can also generate a still image from your video.', 'et_builder' ),
				'toggle_slug'        => 'overlay',
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Slider Arrows Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'arrows_color',
				'description' => esc_html__( 'This setting will make your slider arrows either light or dark in color.', 'et_builder' ),
			),
			'__oembed_thumbnail' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Video_Slider_Item', 'get_oembed_thumbnail' ),
				'computed_depends_on' => array(
					'image_src',
				),
				'computed_minimum' => array(
					'image_src',
				),
			),
			'__is_oembed' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Video_Slider_Item', 'is_oembed' ),
				'computed_depends_on' => array(
					'src',
				),
				'computed_minimum' => array(
					'src',
				),
			),
			'__get_oembed' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Video_Slider_Item', 'get_oembed' ),
				'computed_depends_on' => array(
					'src',
				),
				'computed_minimum' => array(
					'src',
				),
			),
		);
		return $fields;
	}

	static function get_oembed_thumbnail( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'image_src'
		);

		$args = wp_parse_args( $args, $defaults );

		return et_pb_set_video_oembed_thumbnail_resolution( $args['image_src'], 'high' );
	}

	static function is_oembed( $args = array(), $conditional_tags = array(), $current_page = array() ){
		$defaults = array(
			'src'
		);

		$args = wp_parse_args( $args, $defaults );

		return et_pb_check_oembed_provider( esc_url( $args['src'] ) );
	}

 	static function get_oembed( $args = array(), $conditional_tags = array(), $current_page = array() ) {
 		$defaults = array(
 			'src' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// Save thumbnail
		$thumbnail_track_output = wp_oembed_get( esc_url( $args['src'] ) );

		return $thumbnail_track_output;
 	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$src               = $this->shortcode_atts['src'];
		$src_webm          = $this->shortcode_atts['src_webm'];
		$image_src         = $this->shortcode_atts['image_src'];
		$background_layout = $this->shortcode_atts['background_layout'];
		$video_src         = '';

		global $et_pb_slider_image_overlay;

		$class  = '';
		$class .= " et_pb_bg_layout_{$background_layout}";

		if ( '' !== $image_src ) {
			$image_overlay_output = et_pb_set_video_oembed_thumbnail_resolution( $image_src, 'high' );
			$thumbnail_track_output = $image_src;
		} else {
			$image_overlay_output = '';
			if ( false !== et_pb_check_oembed_provider( esc_url( $src ) ) ) {
				add_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
				// Save thumbnail
				$thumbnail_track_output = wp_oembed_get( esc_url( $src ) );
				// Set back to normal
				remove_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
			} else {
				$thumbnail_track_output = '';
			}
		}

		if ( '' !== $src ) {
			if ( false !== et_pb_check_oembed_provider( esc_url( $src ) ) ) {
				$video_src = wp_oembed_get( esc_url( $src ) );
			} else {
				$video_src = sprintf( '
					<video controls>
						%1$s
						%2$s
					</video>',
					( '' !== $src ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $src ) ) : '' ),
					( '' !== $src_webm ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $src_webm ) ) : '' )
				);

				wp_enqueue_style( 'wp-mediaelement' );
				wp_enqueue_script( 'wp-mediaelement' );
			}
		}

		$video_output = sprintf(
			'<div class="et_pb_video_wrap">
				<div class="et_pb_video_box">
					%1$s
				</div>
				%2$s
			</div>',
			( '' !== $video_src ? $video_src : '' ),
			(
				( '' !== $image_overlay_output && $et_pb_slider_image_overlay == 'show' )
					? sprintf(
						'<div class="et_pb_video_overlay" style="background-image: url(%1$s);">
							<div class="et_pb_video_overlay_hover">
								<a href="#" class="et_pb_video_play"></a>
							</div>
						</div>',
						esc_attr( $image_overlay_output )
					)
					: ''
			)
		);

		$output = sprintf(
			'<div class="et_pb_slide%1$s"%3$s>
				%2$s
			</div> <!-- .et_pb_slide -->
			',
			esc_attr( $class ),
			( '' !== $video_output ? $video_output : '' ),
			( '' !== $thumbnail_track_output ? sprintf( ' data-image="%1$s"', esc_attr( $thumbnail_track_output ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Video_Slider_Item;

class ET_Builder_Module_Text extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Text', 'et_builder' );
		$this->slug       = 'et_pb_text';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'background_layout',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited',
		);

		$this->fields_defaults = array(
			'background_layout' => array( 'light' ),
			'text_orientation'  => array( 'left' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'width' => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->main_css_element = '%%order_class%%';
		$this->advanced_options = array(
			'fonts' => array(
				'text'   => array(
					'label'    => esc_html__( 'Text', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
						'color' => "{$this->main_css_element}.et_pb_text",
					),
					'toggle_slug' => 'text',
				),
				'header'   => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h1",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'background_layout' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'This controls the how your text is aligned within the module.', 'et_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the content that will be used within the module.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'max_width' => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'mobile_options'  => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'validate_unit'   => true,
			),
			'max_width_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id            = $this->shortcode_atts['module_id'];
		$module_class         = $this->shortcode_atts['module_class'];
		$background_layout    = $this->shortcode_atts['background_layout'];
		$text_orientation     = $this->shortcode_atts['text_orientation'];
		$max_width            = $this->shortcode_atts['max_width'];
		$max_width_tablet     = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone      = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited = $this->shortcode_atts['max_width_last_edited'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );

			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ? $max_width_phone : '',
			);

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name );
		}

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

		$output = sprintf(
			'<div%3$s class="et_pb_text%2$s%4$s%5$s%7$s">
				%8$s
				%6$s
				<div class="et_pb_text_inner">
					%1$s
				</div>
			</div> <!-- .et_pb_text -->',
			$this->shortcode_content,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Text;

class ET_Builder_Module_Blurb extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Blurb', 'et_builder' );
		$this->slug             = 'et_pb_blurb';
		$this->fb_support       = true;
		$this->main_css_element = '%%order_class%%.et_pb_blurb';

		$this->whitelisted_fields = array(
			'title',
			'url',
			'url_new_window',
			'use_icon',
			'font_icon',
			'icon_color',
			'use_circle',
			'circle_color',
			'use_circle_border',
			'circle_border_color',
			'image',
			'alt',
			'icon_placement',
			'animation',
			'background_layout',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'use_icon_font_size',
			'icon_font_size',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited',
			'icon_font_size_tablet',
			'icon_font_size_phone',
			'icon_font_size_last_edited',
		);

		$et_accent_color = et_builder_accent_color();

		$this->fields_defaults = array(
			'url_new_window'      => array( 'off' ),
			'use_icon'            => array( 'off' ),
			'icon_color'          => array( $et_accent_color, 'add_default_setting' ),
			'use_circle'          => array( 'off' ),
			'circle_color'        => array( $et_accent_color, 'only_default_setting' ),
			'use_circle_border'   => array( 'off' ),
			'circle_border_color' => array( $et_accent_color, 'only_default_setting' ),
			'icon_placement'      => array( 'top' ),
			'animation'           => array( 'top' ),
			'background_layout'   => array( 'light' ),
			'text_orientation'    => array( 'left' ),
			'use_icon_font_size'  => array( 'off' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
					'image'        => esc_html__( 'Image & Icon', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon_settings' => esc_html__( 'Image & Icon', 'et_builder' ),
					'text'          => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'width'         => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h4, {$this->main_css_element} h4 a",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);
		$this->custom_css_options = array(
			'blurb_image' => array(
				'label'    => esc_html__( 'Blurb Image', 'et_builder' ),
				'selector' => '.et_pb_main_blurb_image',
			),
			'blurb_title' => array(
				'label'    => esc_html__( 'Blurb Title', 'et_builder' ),
				'selector' => 'h4',
			),
			'blurb_content' => array(
				'label'    => esc_html__( 'Blurb Content', 'et_builder' ),
				'selector' => '.et_pb_blurb_content',
			),
		);
	}

	function get_fields() {
		$et_accent_color = et_builder_accent_color();

		$image_icon_placement = array(
			'top' => esc_html__( 'Top', 'et_builder' ),
		);

		if ( ! is_rtl() ) {
			$image_icon_placement['left'] = esc_html__( 'Left', 'et_builder' );
		} else {
			$image_icon_placement['right'] = esc_html__( 'Right', 'et_builder' );
		}

		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title of your blurb will appear in bold below your blurb image.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'url' => array(
				'label'           => esc_html__( 'Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to make your blurb a link, input your destination URL here.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'link',
				'description'     => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'use_icon' => array(
				'label'           => esc_html__( 'Use Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'toggle_slug'     => 'image',
				'affects'         => array(
					'font_icon',
					'max_width',
					'use_icon_font_size',
					'use_circle',
					'icon_color',
					'image',
					'alt',
				),
				'description' => esc_html__( 'Here you can choose whether icon set below should be used.', 'et_builder' ),
			),
			'font_icon' => array(
				'label'               => esc_html__( 'Icon', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'toggle_slug'         => 'image',
				'description'         => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'depends_default'     => true,
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom color for your icon.', 'et_builder' ),
				'depends_default'   => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon_settings',
			),
			'use_circle' => array(
				'label'           => esc_html__( 'Circle Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'use_circle_border',
					'circle_color',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'description'      => esc_html__( 'Here you can choose whether icon set above should display within a circle.', 'et_builder' ),
				'depends_default'  => true,
			),
			'circle_color' => array(
				'label'           => esc_html__( 'Circle Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for the icon circle.', 'et_builder' ),
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
			),
			'use_circle_border' => array(
				'label'           => esc_html__( 'Show Circle Border', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'circle_border_color',
				),
				'description' => esc_html__( 'Here you can choose whether if the icon circle border should display.', 'et_builder' ),
				'depends_default'   => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon_settings',
			),
			'circle_border_color' => array(
				'label'           => esc_html__( 'Circle Border Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for the icon circle border.', 'et_builder' ),
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
			),
			'image' => array(
				'label'              => esc_html__( 'Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'depends_show_if'    => 'off',
				'description'        => esc_html__( 'Upload an image to display at the top of your blurb.', 'et_builder' ),
				'toggle_slug'        => 'image',
			),
			'alt' => array(
				'label'           => esc_html__( 'Image Alt Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'et_builder' ),
				'depends_show_if' => 'off',
				'toggle_slug'     => 'image',
			),
			'icon_placement' => array(
				'label'             => esc_html__( 'Image/Icon Placement', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => $image_icon_placement,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon_settings',
				'description'       => esc_html__( 'Here you can choose where to place the icon.', 'et_builder' ),
			),
			'animation' => array(
				'label'             => esc_html__( 'Image/Icon Animation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'top'    => esc_html__( 'Top To Bottom', 'et_builder' ),
					'left'   => esc_html__( 'Left To Right', 'et_builder' ),
					'right'  => esc_html__( 'Right To Left', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom To Top', 'et_builder' ),
					'off'    => esc_html__( 'No Animation', 'et_builder' ),
				),
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'color_option',
				'options'           => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'This will control how your blurb text is aligned.', 'et_builder' ),
			),
			'content_new' => array(
				'label'             => esc_html__( 'Content', 'et_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
			),
			'max_width' => array(
				'label'           => esc_html__( 'Image Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
				'depends_show_if' => 'off',
			),
			'use_icon_font_size' => array(
				'label'           => esc_html__( 'Use Icon Font Size', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'font_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'     => array(
					'icon_font_size',
				),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
			),
			'icon_font_size_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'icon_settings',
			),
			'icon_font_size' => array(
				'label'           => esc_html__( 'Icon Font Size', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'font_option',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'default'         => '96px',
				'range_settings' => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'  => true,
				'depends_default' => true,
			),
			'max_width_tablet' => array (
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_phone' => array (
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'icon_font_size_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'icon_settings',
			),
			'icon_font_size_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'icon_settings',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id             = $this->shortcode_atts['module_id'];
		$module_class          = $this->shortcode_atts['module_class'];
		$title                 = $this->shortcode_atts['title'];
		$url                   = $this->shortcode_atts['url'];
		$image                 = $this->shortcode_atts['image'];
		$url_new_window        = $this->shortcode_atts['url_new_window'];
		$alt                   = $this->shortcode_atts['alt'];
		$background_layout     = $this->shortcode_atts['background_layout'];
		$text_orientation      = $this->shortcode_atts['text_orientation'];
		$animation             = $this->shortcode_atts['animation'];
		$icon_placement        = $this->shortcode_atts['icon_placement'];
		$font_icon             = $this->shortcode_atts['font_icon'];
		$use_icon              = $this->shortcode_atts['use_icon'];
		$use_circle            = $this->shortcode_atts['use_circle'];
		$use_circle_border     = $this->shortcode_atts['use_circle_border'];
		$icon_color            = $this->shortcode_atts['icon_color'];
		$circle_color          = $this->shortcode_atts['circle_color'];
		$circle_border_color   = $this->shortcode_atts['circle_border_color'];
		$max_width             = $this->shortcode_atts['max_width'];
		$max_width_tablet      = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone       = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited = $this->shortcode_atts['max_width_last_edited'];
		$use_icon_font_size    = $this->shortcode_atts['use_icon_font_size'];
		$icon_font_size        = $this->shortcode_atts['icon_font_size'];
		$icon_font_size_tablet = $this->shortcode_atts['icon_font_size_tablet'];
		$icon_font_size_phone  = $this->shortcode_atts['icon_font_size_phone'];
		$icon_font_size_last_edited  = $this->shortcode_atts['icon_font_size_last_edited'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( 'off' !== $use_icon_font_size ) {
			$font_size_responsive_active = et_pb_get_responsive_status( $icon_font_size_last_edited );

			$font_size_values = array(
				'desktop' => $icon_font_size,
				'tablet'  => $font_size_responsive_active ? $icon_font_size_tablet : '',
				'phone'   => $font_size_responsive_active ? $icon_font_size_phone : '',
			);

			et_pb_generate_responsive_css( $font_size_values, '%%order_class%% .et-pb-icon', 'font-size', $function_name );
		}

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );

			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ? $max_width_phone : '',
			);

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%% .et_pb_main_blurb_image img', 'max-width', $function_name );
		}

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		if ( is_rtl() && 'left' === $icon_placement ) {
			$icon_placement = 'right';
		}

		if ( '' !== $title && '' !== $url ) {
			$title = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				esc_html( $title ),
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		}

		if ( '' !== $title ) {
			$title = "<h4>{$title}</h4>";
		}

		if ( 'off' === $use_icon ) {
			$image = ( '' !== trim( $image ) ) ? sprintf(
				'<img src="%1$s" alt="%2$s" class="et-waypoint%3$s" />',
				esc_url( $image ),
				esc_attr( $alt ),
				esc_attr( " et_pb_animation_{$animation}" )
			) : '';
		} else {
			$icon_style = sprintf( 'color: %1$s;', esc_attr( $icon_color ) );

			if ( 'on' === $use_circle ) {
				$icon_style .= sprintf( ' background-color: %1$s;', esc_attr( $circle_color ) );

				if ( 'on' === $use_circle_border ) {
					$icon_style .= sprintf( ' border-color: %1$s;', esc_attr( $circle_border_color ) );
				}
			}

			$image = ( '' !== $font_icon ) ? sprintf(
				'<span class="et-pb-icon et-waypoint%2$s%3$s%4$s" style="%5$s">%1$s</span>',
				esc_attr( et_pb_process_font_icon( $font_icon ) ),
				esc_attr( " et_pb_animation_{$animation}" ),
				( 'on' === $use_circle ? ' et-pb-icon-circle' : '' ),
				( 'on' === $use_circle && 'on' === $use_circle_border ? ' et-pb-icon-circle-border' : '' ),
				$icon_style
			) : '';
		}

		$image = $image ? sprintf(
			'<div class="et_pb_main_blurb_image">%1$s</div>',
			( '' !== $url
				? sprintf(
					'<a href="%1$s"%3$s>%2$s</a>',
					esc_url( $url ),
					$image,
					( 'on' === $url_new_window ? ' target="_blank"' : '' )
				)
				: $image
			)
		) : '';

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

		$output = sprintf(
			'<div%5$s class="et_pb_blurb%4$s%6$s%7$s%8$s%10$s">
				%11$s
				%9$s
				<div class="et_pb_blurb_content">
					%2$s
					<div class="et_pb_blurb_container">
						%3$s
						%1$s
					</div>
				</div> <!-- .et_pb_blurb_content -->
			</div> <!-- .et_pb_blurb -->',
			$this->shortcode_content,
			$image,
			$title,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			sprintf( ' et_pb_blurb_position_%1$s', esc_attr( $icon_placement ) ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Blurb;

class ET_Builder_Module_Tabs extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Tabs', 'et_builder' );
		$this->slug            = 'et_pb_tabs';
		$this->fb_support      = true;
		$this->child_slug      = 'et_pb_tab';
		$this->child_item_text = esc_html__( 'Tab', 'et_builder' );

		$this->whitelisted_fields = array(
			'admin_label',
			'module_id',
			'module_class',
			'active_tab_background_color',
			'inactive_tab_background_color',
		);

		$this->main_css_element = '%%order_class%%.et_pb_tabs';

		$this->advanced_options = array(
			'fonts' => array(
				'tab' => array(
					'label'    => esc_html__( 'Tab', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_tabs_controls li, {$this->main_css_element} .et_pb_tabs_controls li a",
						'color' => "{$this->main_css_element} .et_pb_tabs_controls li a",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_all_tabs .et_pb_tab",
						'plugin_main' => "{$this->main_css_element} .et_pb_all_tabs .et_pb_tab, {$this->main_css_element} .et_pb_all_tabs .et_pb_tab p",
						'line_height' => "{$this->main_css_element} .et_pb_tab p",
					),
				),
			),
			'background' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_all_tabs",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
		);
		$this->custom_css_options = array(
			'tabs_controls' => array(
				'label'    => esc_html__( 'Tabs Controls', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls',
			),
			'tab' => array(
				'label'    => esc_html__( 'Tab', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls li',
			),
			'active_tab' => array(
				'label'    => esc_html__( 'Active Tab', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls li.et_pb_tab_active',
			),
			'tabs_content' => array(
				'label'    => esc_html__( 'Tabs Content', 'et_builder' ),
				'selector' => '.et_pb_tab',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'active_tab_background_color' => array(
				'label'             => esc_html__( 'Active Tab Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'inactive_tab_background_color' => array(
				'label'             => esc_html__( 'Inactive Tab Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                         = $this->shortcode_atts['module_id'];
		$module_class                      = $this->shortcode_atts['module_class'];
		$active_tab_background_color       = $this->shortcode_atts['active_tab_background_color'];
		$inactive_tab_background_color     = $this->shortcode_atts['inactive_tab_background_color'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$all_tabs_content = $this->shortcode_content;

		global $et_pb_tab_titles;
		global $et_pb_tab_classes;

		if ( '' !== $inactive_tab_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_tabs_controls li',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $inactive_tab_background_color )
				),
			) );
		}

		if ( '' !== $active_tab_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_tabs_controls li.et_pb_tab_active',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $active_tab_background_color )
				),
			) );
		}

		$tabs = '';

		$i = 0;
		if ( ! empty( $et_pb_tab_titles ) ) {
			foreach ( $et_pb_tab_titles as $tab_title ){
				++$i;
				$tabs .= sprintf( '<li class="%3$s%1$s"><a href="#">%2$s</a></li>',
					( 1 == $i ? ' et_pb_tab_active' : '' ),
					esc_html( $tab_title ),
					esc_attr( ltrim( $et_pb_tab_classes[ $i-1 ] ) )
				);
			}
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$et_pb_tab_titles = $et_pb_tab_classes = array();

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_tabs%4$s%5$s%7$s">
				%8$s
				%6$s
				<ul class="et_pb_tabs_controls clearfix">
					%1$s
				</ul>
				<div class="et_pb_all_tabs">
					%2$s
				</div> <!-- .et_pb_all_tabs -->
			</div> <!-- .et_pb_tabs -->',
			$tabs,
			$all_tabs_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background

		);

		return $output;
	}
}
new ET_Builder_Module_Tabs;

class ET_Builder_Module_Tabs_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Tab', 'et_builder' );
		$this->slug                        = 'et_pb_tab';
		$this->fb_support                  = true;
		$this->type                        = 'child';
		$this->child_title_var             = 'title';

		$this->whitelisted_fields = array(
			'title',
			'content_new',
		);

		$this->advanced_setting_title_text = esc_html__( 'New Tab', 'et_builder' );
		$this->settings_text               = esc_html__( 'Tab Settings', 'et_builder' );
		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'tab' => array(
					'label'    => esc_html__( 'Tab', 'et_builder' ),
					'css'      => array(
						'main'      => ".et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element}, .et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element} a",
						'color'     => ".et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element} a",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => ".et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab",
						'line_height' => ".et_pb_tabs {$this->main_css_element}.et_pb_tab p",
						'plugin_main' => ".et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab, .et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab p",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
			'background' => array(
				'css' => array(
					'main' => ".et_pb_tabs {$this->main_css_element}.et_pb_tab",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
		);

		$this->custom_css_options = array(
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'selector' => ".et_pb_tabs div{$this->main_css_element}.et_pb_tab",
			)
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'       => esc_html__( 'Title', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'The title will be used within the tab button for this tab.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'content_new' => array(
				'label'       => esc_html__( 'Content', 'et_builder' ),
				'type'        => 'tiny_mce',
				'description' => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_tab_titles;
		global $et_pb_tab_classes;

		$title = $this->shortcode_atts['title'];

		$module_class = ET_Builder_Element::add_module_order_class( '', $function_name );

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$i = 0;

		$et_pb_tab_titles[]  = '' !== $title ? $title : esc_html__( 'Tab', 'et_builder' );
		$et_pb_tab_classes[] = $module_class;

		$output = sprintf(
			'<div class="et_pb_tab clearfix%2$s%3$s%4$s%6$s">
				%7$s
				%5$s
				<div class="et_pb_tab_content">
					%1$s
				</div><!-- .et_pb_tab_content" -->
			</div> <!-- .et_pb_tab -->',
			$this->shortcode_content,
			( 1 === count( $et_pb_tab_titles ) ? ' et_pb_active_content' : '' ),
			esc_attr( $module_class ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Tabs_Item;

class ET_Builder_Module_Slider extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Slider', 'et_builder' );
		$this->slug            = 'et_pb_slider';
		$this->fb_support      = true;
		$this->child_slug      = 'et_pb_slide';
		$this->child_item_text = esc_html__( 'Slide', 'et_builder' );

		$this->whitelisted_fields = array(
			'show_arrows',
			'show_pagination',
			'auto',
			'auto_speed',
			'auto_ignore_hover',
			'parallax',
			'parallax_method',
			'remove_inner_shadow',
			'background_position',
			'background_size',
			'admin_label',
			'module_id',
			'module_class',
			'top_padding',
			'bottom_padding',
			'hide_content_on_mobile',
			'hide_cta_on_mobile',
			'show_image_video_mobile',
			'top_padding_tablet',
			'top_padding_phone',
			'top_padding_last_edited',
			'bottom_padding_tablet',
			'bottom_padding_phone',
			'bottom_padding_last_edited',
		);

		$this->fields_defaults = array(
			'show_arrows'             => array( 'on' ),
			'show_pagination'         => array( 'on' ),
			'auto'                    => array( 'off' ),
			'auto_speed'              => array( '7000' ),
			'auto_ignore_hover'       => array( 'off' ),
			'parallax'                => array( 'off' ),
			'parallax_method'         => array( 'off' ),
			'remove_inner_shadow'     => array( 'off' ),
			'background_position'     => array( 'center' ),
			'background_size'         => array( 'cover' ),
			'hide_content_on_mobile'  => array( 'off' ),
			'hide_cta_on_mobile'      => array( 'off' ),
			'show_image_video_mobile' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_slider';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'    => esc_html__( 'Elements', 'et_builder' ),
					'background'  => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'     => esc_html__( 'Layout', 'et_builder' ),
					'padding'  => array(
						'title'       => esc_html__( 'Spacing', 'et_builder' ),
						'priority'   => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title",
						'plugin_main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title, {$this->main_css_element} .et_pb_slide_description .et_pb_slide_title a",
						'font_size_tablet' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_description .et_pb_slide_title",
						'font_size_phone'  => "{$this->main_css_element} .et_pb_slides .et_pb_slide_description .et_pb_slide_title",
						'important' => array( 'size', 'font-size', 'plugin_all' ),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element}",
						'main' => "{$this->main_css_element} .et_pb_slide_content",
						'line_height_tablet' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'line_height_phone' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'font_size_tablet' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'font_size_phone' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'important' => array( 'size', 'font-size' ),
					),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
					),
				),
			),
		);
		$this->custom_css_options = array(
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description .et_pb_slide_title',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector' => '.et_pb_slider .et_pb_slide .et_pb_slide_description a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_controllers' => array(
				'label'    => esc_html__( 'Slide Controllers', 'et_builder' ),
				'selector' => '.et-pb-controllers',
			),
			'slide_active_controller' => array(
				'label'    => esc_html__( 'Slide Active Controller', 'et_builder' ),
				'selector' => '.et-pb-controllers .et-pb-active-control',
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
			'slide_arrows' => array(
				'label'    => esc_html__( 'Slide Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_arrows'         => array(
				'label'           => esc_html__( 'Arrows', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Show Arrows', 'et_builder' ),
					'off' => esc_html__( 'Hide Arrows', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'This setting will turn on and off the navigation arrows.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Controls', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'This setting will turn on and off the circle buttons at the bottom of the slider.', 'et_builder' ),
			),
			'auto' => array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
					'auto_ignore_hover',
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
			),
			'auto_speed' => array(
				'label'             => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_default'   => true,
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
			),
			'auto_ignore_hover' => array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'et_builder' ),
			),
			'parallax' => array(
				'label'           => esc_html__( 'Use Parallax effect', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'parallax_method',
					'background_position',
					'background_size',
				),
				'tab_slug'           => 'general',
				'toggle_slug'        => 'background',
				'description'        => esc_html__( 'Enabling this option will give your background images a fixed position as you scroll.', 'et_builder' ),
			),
			'parallax_method' => array(
				'label'           => esc_html__( 'Parallax method', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'CSS', 'et_builder' ),
					'on'  => esc_html__( 'True Parallax', 'et_builder' ),
				),
				'tab_slug'          => 'general',
				'toggle_slug'       => 'background',
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Define the method, used for the parallax effect.', 'et_builder' ),
			),
			'remove_inner_shadow' => array(
				'label'           => esc_html__( 'Remove Inner Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'background_position' => array(
				'label'           => esc_html__( 'Background Image Position', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'top_left'      => esc_html__( 'Top Left', 'et_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'et_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'et_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'et_builder' ),
					'center'        => esc_html__( 'Center', 'et_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'et_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'et_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'et_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'et_builder' ),
				),
				'default'           => 'center',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			),
			'background_size' => array(
				'label'           => esc_html__( 'Background Image Size', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'cover'   => esc_html__( 'Cover', 'et_builder' ),
					'contain' => esc_html__( 'Fit', 'et_builder' ),
					'initial' => esc_html__( 'Actual Size', 'et_builder' ),
				),
				'default'           => 'cover',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			),
			'top_padding' => array(
				'label'           => esc_html__( 'Top Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'bottom_padding' => array(
				'label'           => esc_html__( 'Bottom Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'hide_content_on_mobile' => array(
				'label'           => esc_html__( 'Hide Content On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'hide_cta_on_mobile' => array(
				'label'           => esc_html__( 'Hide CTA On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'show_image_video_mobile' => array(
				'label'           => esc_html__( 'Show Image / Video On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'top_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'top_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'top_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'bottom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'bottom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'bottom_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_hide_mobile, $et_pb_slider_custom_icon, $et_pb_slider_item_num;

		$et_pb_slider_item_num = 0;

		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$hide_content_on_mobile  = $this->shortcode_atts['hide_content_on_mobile'];
		$hide_cta_on_mobile      = $this->shortcode_atts['hide_cta_on_mobile'];
		$button_custom           = $this->shortcode_atts['custom_button'];
		$custom_icon             = $this->shortcode_atts['button_icon'];

		$et_pb_slider_has_video = false;

		$et_pb_slider_parallax = $parallax;

		$et_pb_slider_parallax_method = $parallax_method;

		$et_pb_slider_hide_mobile = array(
			'hide_content_on_mobile'  => $hide_content_on_mobile,
			'hide_cta_on_mobile'      => $hide_cta_on_mobile,
		);

		$et_pb_slider_custom_icon = 'on' === $button_custom ? $custom_icon : '';

	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$show_arrows             = $this->shortcode_atts['show_arrows'];
		$show_pagination         = $this->shortcode_atts['show_pagination'];
		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$auto                    = $this->shortcode_atts['auto'];
		$auto_speed              = $this->shortcode_atts['auto_speed'];
		$auto_ignore_hover       = $this->shortcode_atts['auto_ignore_hover'];
		$top_padding             = $this->shortcode_atts['top_padding'];
		$body_font_size 		 = $this->shortcode_atts['body_font_size'];
		$bottom_padding          = $this->shortcode_atts['bottom_padding'];
		$top_padding_tablet      = $this->shortcode_atts['top_padding_tablet'];
		$top_padding_phone       = $this->shortcode_atts['top_padding_phone'];
		$top_padding_last_edited = $this->shortcode_atts['top_padding_last_edited'];
		$bottom_padding_tablet   = $this->shortcode_atts['bottom_padding_tablet'];
		$bottom_padding_phone    = $this->shortcode_atts['bottom_padding_phone'];
		$bottom_padding_last_edited = $this->shortcode_atts['bottom_padding_last_edited'];
		$remove_inner_shadow     = $this->shortcode_atts['remove_inner_shadow'];
		$hide_content_on_mobile  = $this->shortcode_atts['hide_content_on_mobile'];
		$hide_cta_on_mobile      = $this->shortcode_atts['hide_cta_on_mobile'];
		$show_image_video_mobile = $this->shortcode_atts['show_image_video_mobile'];
		$background_position     = $this->shortcode_atts['background_position'];
		$background_size         = $this->shortcode_atts['background_size'];

		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_hide_mobile, $et_pb_slider_custom_icon;

		$content = $this->shortcode_content;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $top_padding || '' !== $top_padding_tablet || '' !== $top_padding_phone ) {
			$top_padding_responsive_active = et_pb_get_responsive_status( $top_padding_last_edited );

			$padding_values = array(
				'desktop' => $top_padding,
				'tablet'  => $top_padding_responsive_active ? $top_padding_tablet : '',
				'phone'   => $top_padding_responsive_active ? $top_padding_phone : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description', 'padding-top', $function_name );
		}

		if ( '' !== $bottom_padding || '' !== $bottom_padding_tablet || '' !== $bottom_padding_phone ) {
			$bottom_padding_responsive_active = et_pb_get_responsive_status( $bottom_padding_last_edited );

			$padding_values = array(
				'desktop' => $bottom_padding,
				'tablet'  => $bottom_padding_responsive_active ? $bottom_padding_tablet : '',
				'phone'   => $bottom_padding_responsive_active ? $bottom_padding_phone : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description', 'padding-bottom', $function_name );
		}

		if ( '' !== $bottom_padding || '' !== $top_padding ) {
			ET_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description',
				'declaration' => 'padding-right: 0; padding-left: 0;',
			) );
		}

		if ( '' !== $background_position && 'default' !== $background_position  && 'off' === $parallax ) {
			$processed_position = str_replace( '_', ' ', $background_position );

			ET_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide',
				'declaration' => sprintf(
					'background-position: %1$s;',
					esc_html( $processed_position )
				),
			) );
		}

		// Handle slider's previous background size default value ("default") as well
		if ( '' !== $background_size && 'default' !== $background_size && 'off' === $parallax ) {
			ET_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide',
				'declaration' => sprintf(
					'-moz-background-size: %1$s;
					-webkit-background-size: %1$s;
					background-size: %1$s;',
					esc_html( $background_size )
				),
			) );
		}

		$fullwidth = 'et_pb_fullwidth_slider' === $function_name ? 'on' : 'off';

		$class  = '';
		$class .= 'off' === $fullwidth ? ' et_pb_slider_fullwidth_off' : '';
		$class .= 'off' === $show_arrows ? ' et_pb_slider_no_arrows' : '';
		$class .= 'off' === $show_pagination ? ' et_pb_slider_no_pagination' : '';
		$class .= 'on' === $parallax ? ' et_pb_slider_parallax' : '';
		$class .= 'on' === $auto ? ' et_slider_auto et_slider_speed_' . esc_attr( $auto_speed ) : '';
		$class .= 'on' === $auto_ignore_hover ? ' et_slider_auto_ignore_hover' : '';
		$class .= 'on' === $remove_inner_shadow ? ' et_pb_slider_no_shadow' : '';
		$class .= 'on' === $show_image_video_mobile ? ' et_pb_slider_show_image' : '';

		$output = sprintf(
			'<div%4$s class="et_pb_module et_pb_slider%1$s%3$s%5$s">
				<div class="et_pb_slides">
					%2$s
				</div> <!-- .et_pb_slides -->
			</div> <!-- .et_pb_slider -->
			',
			$class,
			$content,
			( $et_pb_slider_has_video ? ' et_pb_preload' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Slider;

class ET_Builder_Module_Slider_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Slide', 'et_builder' );
		$this->slug                        = 'et_pb_slide';
		$this->fb_support                  = true;
		$this->type                        = 'child';
		$this->child_title_var             = 'admin_title';
		$this->child_title_fallback_var    = 'heading';

		$this->whitelisted_fields = array(
			'heading',
			'admin_title',
			'button_text',
			'button_link',
			'background_image',
			'parallax',
			'parallax_method',
			'background_size',
			'background_position',
			'background_repeat',
			'background_blend',
			'background_color',
			'image',
			'alignment',
			'video_url',
			'image_alt',
			'background_layout',
			'video_bg_mp4',
			'video_bg_webm',
			'video_bg_width',
			'video_bg_height',
			'allow_player_pause',
			'content_new',
			'arrows_custom_color',
			'dot_nav_custom_color',
			'use_bg_overlay',
			'use_text_overlay',
			'bg_overlay_color',
			'text_overlay_color',
			'text_border_radius',
		);

		$this->fields_defaults = array(
			'button_link'         => array( '#' ),
			'background_position' => array( 'center' ),
			'background_size'     => array( 'cover' ),
			'background_color'    => array( '#ffffff', 'only_default_setting' ),
			'alignment'           => array( 'center' ),
			'background_layout'   => array( 'dark' ),
			'allow_player_pause'  => array( 'off' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Slide', 'et_builder' );
		$this->settings_text               = esc_html__( 'Slide Settings', 'et_builder' );
		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
					'image_video'  => esc_html__( 'Image & Video', 'et_builder' ),
					'player_pause' => esc_html__( 'Player Pause', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
					'admin_label'  => esc_html__( 'Admin Label', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'navigation' => esc_html__( 'Navigation', 'et_builder' ),
					'alignment'  => esc_html__( 'Alignment', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title",
						'plugin_main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title, .et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title a",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '0.1',
						),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'        => ".et_pb_slider.et_pb_module {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_content",
						'line_height' => "{$this->main_css_element} p",
						'important'   => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '0.1',
						),
					),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css'      => array(
						'main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_button",
						'plugin_main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_more_button.et_pb_button",
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_image'          => false,
				'use_background_color_gradient' => true,
				'use_background_video'          => false,
			),
		);

		$this->custom_css_options = array(
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description h2',
			),
			'slide_container' => array(
				'label'    => esc_html__( 'Slide Description Container', 'et_builder' ),
				'selector' => '.et_pb_container',
			),
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector' => '.et_pb_slide .et_pb_container a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'heading' => array(
				'label'           => esc_html__( 'Heading', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the title text for your slide.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the text for the slide button', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_link' => array(
				'label'           => esc_html__( 'Button URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a destination URL for the slide button.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'background_image' => array(
				'label'              => esc_html__( 'Background Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background', 'et_builder' ),
				'description'        => esc_html__( 'If defined, this image will be used as the background for this module. To remove a background image, simply delete the URL from the settings field.', 'et_builder' ),
				'toggle_slug'        => 'background',
			),
			'parallax' => array(
				'label'           => esc_html__( 'Use Parallax effect', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'parallax_method',
					'background_size',
					'background_position',
					'background_repeat',
					'background_blend',
				),
				'default'            => 'off',
				'tab_slug'           => 'general',
				'toggle_slug'        => 'background',
				'description'        => esc_html__( 'Enabling this option will give your background images a fixed position as you scroll.', 'et_builder' ),
			),
			'parallax_method' => array(
				'label'           => esc_html__( 'Parallax method', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'CSS', 'et_builder' ),
					'on'  => esc_html__( 'True Parallax', 'et_builder' ),
				),
				'tab_slug'          => 'general',
				'toggle_slug'       => 'background',
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Define the method, used for the parallax effect.', 'et_builder' ),
			),
			'background_position' => array(
				'label'           => esc_html__( 'Background Image Position', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'top_left'      => esc_html__( 'Top Left', 'et_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'et_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'et_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'et_builder' ),
					'center'        => esc_html__( 'Center', 'et_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'et_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'et_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'et_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'et_builder' ),
				),
				'default'           => 'center',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			),
			'background_size' => array(
				'label'           => esc_html__( 'Background Image Size', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'cover'   => esc_html__( 'Cover', 'et_builder' ),
					'contain' => esc_html__( 'Fit', 'et_builder' ),
					'initial' => esc_html__( 'Actual Size', 'et_builder' ),
				),
				'default'           => 'cover',
				'shortcode_default' => 'cover',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			),
			'background_repeat' => array(
				'label'           => esc_html__( 'Background Image Repeat', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'repeat'      => esc_html__( 'Repeat', 'et_builder' ),
					'repeat-x'    => esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
					'repeat-y'    => esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
					'space'       => esc_html__( 'Space', 'et_builder' ),
					'round'       => esc_html__( 'Round', 'et_builder' ),
					'no-repeat'   => esc_html__( 'No Repeat', 'et_builder' ),
				),
				'default'         => 'no-repeat',
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			),
			'background_blend' => array(
				'label'           => esc_html__( 'Background Image Blend', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'normal'      => esc_html__( 'Normal', 'et_builder' ),
					'multiply'    => esc_html__( 'Multiply', 'et_builder' ),
					'screen'      => esc_html__( 'Screen', 'et_builder' ),
					'overlay'     => esc_html__( 'Overlay', 'et_builder' ),
					'darken'      => esc_html__( 'Darken', 'et_builder' ),
					'lighten'     => esc_html__( 'Lighten', 'et_builder' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'et_builder' ),
					'color-burn'  => esc_html__( 'Color Burn', 'et_builder' ),
					'hard-light'  => esc_html__( 'Hard Light', 'et_builder' ),
					'soft-light'  => esc_html__( 'Soft Light', 'et_builder' ),
					'difference'  => esc_html__( 'Difference', 'et_builder' ),
					'exclusion'   => esc_html__( 'Exclusion', 'et_builder' ),
					'hue'         => esc_html__( 'Hue', 'et_builder' ),
					'saturation'  => esc_html__( 'Saturation', 'et_builder' ),
					'color'       => esc_html__( 'Color', 'et_builder' ),
					'luminosity'  => esc_html__( 'Luminosity', 'et_builder' ),
				),
				'default'         => 'normal',
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			),
			'background_color' => array(
				'label'       => esc_html__( 'Background Color', 'et_builder' ),
				'type'        => 'color-alpha',
				'toggle_slug' => 'background',
				'description' => esc_html__( 'Use the color picker to choose a background color for this module.', 'et_builder' ),
			),
			'image' => array(
				'label'              => esc_html__( 'Slide Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'configuration',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Slide Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Slide Image', 'et_builder' ),
				'affects'            => array(
					'image_alt',
				),
				'description'        => esc_html__( 'If defined, this slide image will appear to the left of your slide text. Upload an image, or leave blank for a text-only slide.', 'et_builder' ),
				'toggle_slug'        => 'image_video',
			),
			'use_bg_overlay'      => array(
				'label'           => esc_html__( 'Use Background Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'yes', 'et_builder' ),
				),
				'affects'           => array(
					'bg_overlay_color',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'et_builder' ),
			),
			'bg_overlay_color' => array(
				'label'             => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'et_builder' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'use_text_overlay'      => array(
				'label'           => esc_html__( 'Use Text Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'yes', 'et_builder' ),
				),
				'affects'           => array(
					'text_overlay_color',
					'text_border_radius',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'et_builder' ),
			),
			'text_overlay_color' => array(
				'label'             => esc_html__( 'Text Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'et_builder' ),
			),
			'alignment' => array(
				'label'           => esc_html__( 'Slide Image Vertical Alignment', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'center' => esc_html__( 'Center', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'This setting determines the vertical alignment of your slide image. Your image can either be vertically centered, or aligned to the bottom of your slide.', 'et_builder' ),
			),
			'video_url' => array(
				'label'           => esc_html__( 'Slide Video', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If defined, this video will appear to the left of your slide text. Enter youtube or vimeo page url, or leave blank for a text-only slide.', 'et_builder' ),
				'toggle_slug'     => 'image_video',
				'computed_affects' => array(
					'__video_embed',
				),
			),
			'image_alt' => array(
				'label'           => esc_html__( 'Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_default' => true,
				'depends_to'      => array(
					'image',
				),
				'description'     => esc_html__( 'If you have a slide image defined, input your HTML ALT text for the image here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text is light or dark. If you have a slide with a dark background, then choose light text. If you have a light background, then use dark text.' , 'et_builder' ),
			),
			'video_bg_mp4' => array(
				'label'              => esc_html__( 'Background Video MP4', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
				'description'        => et_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .MP4 version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'et_builder' ) ),
				'toggle_slug'        => 'background',
				'computed_affects' => array(
					'__video_background',
				),
			),
			'video_bg_webm' => array(
				'label'              => esc_html__( 'Background Video Webm', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
				'description'        => et_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .WEBM version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'et_builder' ) ),
				'toggle_slug'        => 'background',
				'computed_affects' => array(
					'__video_background',
				),
			),
			'video_bg_width' => array(
				'label'           => esc_html__( 'Background Video Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'background',
				'description'     => esc_html__( 'In order for videos to be sized correctly, you must input the exact width (in pixels) of your video here.' ,'et_builder' ),
				'computed_affects' => array(
					'__video_background',
				),
			),
			'video_bg_height' => array(
				'label'           => esc_html__( 'Background Video Height', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'background',
				'description'     => esc_html__( 'In order for videos to be sized correctly, you must input the exact height (in pixels) of your video here.' ,'et_builder' ),
				'computed_affects' => array(
					'__video_background',
				),
			),
			'allow_player_pause' => array(
				'label'           => esc_html__( 'Pause Video', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'toggle_slug'     => 'player_pause',
				'description'     => esc_html__( 'Allow video to be paused by other players when they begin playing' ,'et_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your main slide text content here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'arrows_custom_color' => array(
				'label'        => esc_html__( 'Arrows Custom Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'dot_nav_custom_color' => array(
				'label'        => esc_html__( 'Dot Nav Custom Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'admin_title' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the slide in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'text_border_radius' => array(
				'label'           => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'default'         => '3',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
			),
			'__video_embed' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Slider_Item', 'get_video_embed' ),
				'computed_depends_on' => array(
					'video_url',
				),
				'computed_minimum' => array(
					'video_url',
				),
			),
			'__video_background' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Slider_Item', 'get_slide_video_background' ),
				'computed_depends_on' => array(
					'video_bg_mp4',
					'video_bg_webm',
					'video_bg_width',
					'video_bg_height',
					'background_image',
				),
				'computed_minimum' => array(
					'video_bg_mp4',
					'video_bg_webm',
				),
			),
		);

		return $fields;
	}

	static function get_video_embed( $args = array(), $conditonal_args = array(), $current_page = array() ) {
		global $wp_embed;

		$video_url = esc_url( $args['video_url'] );

		$autoembed      = $wp_embed->autoembed( $video_url );
		$is_local_video = has_shortcode( $autoembed, 'video' );
		$video_embed    = '';

		if ( $is_local_video ) {
			$video_embed = wp_video_shortcode( array( 'src' => $video_url ) );
		} else {
			$video_embed = wp_oembed_get( $video_url );

			$video_embed = preg_replace( '/<embed /','<embed wmode="transparent" ', $video_embed );

			$video_embed = preg_replace( '/<\/object>/','<param name="wmode" value="transparent" /></object>', $video_embed );
		}

		return $video_embed;
	}

	static function get_slide_video_background( $args = array() ) {
		$defaults = array(
			'video_bg_mp4'    => '',
			'video_bg_webm'   => '',
			'video_bg_width'  => '',
			'video_bg_height' => '',
			'background_image'=> '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( '' === $args['video_bg_mp4'] && '' === $args['video_bg_webm'] ) {
			return '';
		}

		return do_shortcode( sprintf( '
			<video loop="loop"%3$s%4$s%5$s>
				%1$s
				%2$s
			</video>',
			( '' !== $args['video_bg_mp4'] ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $args['video_bg_mp4'] ) ) : '' ),
			( '' !== $args['video_bg_webm'] ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $args['video_bg_webm'] ) ) : '' ),
			( '' !== $args['video_bg_width'] ? sprintf( ' width="%s"', esc_attr( intval( $args['video_bg_width'] ) ) ) : '' ),
			( '' !== $args['video_bg_height'] ? sprintf( ' height="%s"', esc_attr( intval( $args['video_bg_height'] ) ) ) : '' ),
			( '' !== $args['background_image'] ? sprintf( ' poster="%s"', esc_url( $args['background_image'] ) ) : '' )
		) );
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$alignment            = $this->shortcode_atts['alignment'];
		$heading              = $this->shortcode_atts['heading'];
		$button_text          = $this->shortcode_atts['button_text'];
		$button_link          = $this->shortcode_atts['button_link'];
		$background_color     = $this->shortcode_atts['background_color'];
		$background_image     = $this->shortcode_atts['background_image'];
		$image                = $this->shortcode_atts['image'];
		$image_alt            = $this->shortcode_atts['image_alt'];
		$background_layout    = $this->shortcode_atts['background_layout'];
		$video_bg_webm        = $this->shortcode_atts['video_bg_webm'];
		$video_bg_mp4         = $this->shortcode_atts['video_bg_mp4'];
		$video_bg_width       = $this->shortcode_atts['video_bg_width'];
		$video_bg_height      = $this->shortcode_atts['video_bg_height'];
		$video_url            = $this->shortcode_atts['video_url'];
		$allow_player_pause   = $this->shortcode_atts['allow_player_pause'];
		$dot_nav_custom_color = $this->shortcode_atts['dot_nav_custom_color'];
		$arrows_custom_color  = $this->shortcode_atts['arrows_custom_color'];
		$custom_icon          = $this->shortcode_atts['button_icon'];
		$button_custom        = $this->shortcode_atts['custom_button'];
		$parallax             = $this->shortcode_atts['parallax'];
		$background_position  = $this->shortcode_atts['background_position'];
		$background_size      = $this->shortcode_atts['background_size'];
		$background_repeat    = $this->shortcode_atts['background_repeat'];
		$background_blend     = $this->shortcode_atts['background_blend'];
		$use_bg_overlay       = $this->shortcode_atts['use_bg_overlay'];
		$bg_overlay_color     = $this->shortcode_atts['bg_overlay_color'];
		$use_text_overlay     = $this->shortcode_atts['use_text_overlay'];
		$text_overlay_color   = $this->shortcode_atts['text_overlay_color'];
		$text_border_radius   = $this->shortcode_atts['text_border_radius'];
		$use_background_color_gradient              = $this->shortcode_atts['use_background_color_gradient'];
		$background_color_gradient_type             = $this->shortcode_atts['background_color_gradient_type'];
		$background_color_gradient_direction        = $this->shortcode_atts['background_color_gradient_direction'];
		$background_color_gradient_direction_radial = $this->shortcode_atts['background_color_gradient_direction_radial'];
		$background_color_gradient_start            = $this->shortcode_atts['background_color_gradient_start'];
		$background_color_gradient_end              = $this->shortcode_atts['background_color_gradient_end'];
		$background_color_gradient_start_position   = $this->shortcode_atts['background_color_gradient_start_position'];
		$background_color_gradient_end_position     = $this->shortcode_atts['background_color_gradient_end_position'];
		$background_images  = array();

		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_hide_mobile, $et_pb_slider_custom_icon, $et_pb_slider_item_num;

		$background_video = '';

		$et_pb_slider_item_num++;

		$hide_on_mobile_class = self::HIDE_ON_MOBILE;

		$first_video = false;

		$custom_slide_icon = 'on' === $button_custom && '' !== $custom_icon ? $custom_icon : $et_pb_slider_custom_icon;

		if ( '' !== $video_bg_mp4 || '' !== $video_bg_webm ) {
			if ( ! $et_pb_slider_has_video )
				$first_video = true;

			$background_video = sprintf(
				'<div class="et_pb_section_video_bg%2$s%3$s">
					%1$s
				</div>',
				self::get_slide_video_background( array(
					'video_bg_mp4'     => $video_bg_mp4,
					'video_bg_webm'    => $video_bg_webm,
					'video_bg_width'   => $video_bg_width,
					'video_bg_height'  => $video_bg_height,
					'background_image' => $background_image,
				) ),
				( $first_video ? ' et_pb_first_video' : '' ),
				( 'on' === $allow_player_pause ? ' et_pb_allow_player_pause' : '' )
			);

			$et_pb_slider_has_video = true;

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		$parallax_image_background = $this->get_parallax_image_background();

		if ( '' !== $heading ) {
			if ( '#' !== $button_link ) {
				$heading = sprintf( '<a href="%1$s">%2$s</a>',
					esc_url( $button_link ),
					$heading
				);
			}

			$heading = '<h2 class="et_pb_slide_title">' . $heading . '</h2>';
		}

		$button = '';
		if ( '' !== $button_text ) {
			$button = sprintf( '<a href="%1$s" class="et_pb_more_button et_pb_button%3$s%5$s"%4$s>%2$s</a>',
				esc_url( $button_link ),
				esc_html( $button_text ),
				( 'on' === $et_pb_slider_hide_mobile['hide_cta_on_mobile'] ? esc_attr( " {$hide_on_mobile_class}" ) : '' ),
				'' !== $custom_slide_icon ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_slide_icon ) )
				) : '',
				'' !== $custom_slide_icon ? ' et_pb_custom_button_icon' : ''
			);
		}

		$style = $class = '';

		if ( '' !== $background_color ) {
			$style .= sprintf( 'background-color:%s;',
				esc_attr( $background_color )
			);
		}

		if ( 'on' === $use_background_color_gradient ) {
			$background_images[] = $this->get_gradient( array(
				'type'             => $background_color_gradient_type,
				'direction'        => $background_color_gradient_direction,
				'radial_direction' => $background_color_gradient_direction_radial,
				'color_start'      => $background_color_gradient_start,
				'color_end'        => $background_color_gradient_end,
				'start_position'   => $background_color_gradient_start_position,
				'end_position'     => $background_color_gradient_end_position,
			) );
		}

		if ( '' !== $background_image && ( 'on' !== $et_pb_slider_parallax && 'on' !== $parallax ) ) {
			$background_images[] = sprintf( 'url(%s)',
				esc_attr( $background_image )
			);

			if ( '' !== $background_position && 'default' !== $background_position ) {
				$processed_position = str_replace( '_', ' ', $background_position );

				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '.et_pb_slider %%order_class%%.et_pb_slide',
					'declaration' => sprintf(
						'background-position: %1$s;',
						esc_html( $processed_position )
					),
				) );
			}

			if ( '' !== $background_size && 'default' !== $background_size ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '.et_pb_slider .et_pb_slides %%order_class%%',
					'declaration' => sprintf(
						'-moz-background-size: %1$s;
						-webkit-background-size: %1$s;
						background-size: %1$s;',
						esc_html( $background_size )
					),
				) );

				if ( 'initial' === $background_size ) {
					ET_Builder_Module::set_style( $function_name, array(
						'selector'    => 'body.ie %%order_class%%.et_pb_slide',
						'declaration' => '-moz-background-size: auto; -webkit-background-size: auto; background-size: auto;',
					) );
				}
			}

			if ( '' !== $background_repeat ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '.et_pb_slider .et_pb_slides %%order_class%%',
					'declaration' => sprintf(
						'background-repeat: %1$s;',
						esc_html( $background_repeat )
					),
				) );
			}

			if ( '' !== $background_blend ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '.et_pb_slider .et_pb_slides %%order_class%%',
					'declaration' => sprintf(
						'background-blend-mode: %1$s;',
						esc_html( $background_blend )
					),
				) );
			}
		}

		if ( ! empty( $background_images ) ) {
			$background_images = array_reverse( $background_images );

			$style .= sprintf(
				'background-image: %1$s;',
				esc_html( join( ', ', $background_images ) )
			);

			if ( count( $background_images ) > 1 ) {
				$style .= ' background-color: initial;';
			}
		}

		if ( 'on' === $use_bg_overlay && '' !== $bg_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_slide .et_pb_slide_overlay_container',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $bg_overlay_color )
				),
			) );
		}

		if ( 'on' === $use_text_overlay && '' !== $text_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_slide .et_pb_slide_title, %%order_class%%.et_pb_slide .et_pb_slide_content',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $text_overlay_color )
				),
			) );
		}

		if ( '' !== $text_border_radius ) {
			$border_radius_value = et_builder_process_range_value( $text_border_radius );
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_slider_with_text_overlay h2.et_pb_slide_title',
				'declaration' => sprintf(
					'-webkit-border-top-left-radius: %1$s;
					-webkit-border-top-right-radius: %1$s;
					-moz-border-radius-topleft: %1$s;
					-moz-border-radius-topright: %1$s;
					border-top-left-radius: %1$s;
					border-top-right-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_slider_with_text_overlay .et_pb_slide_content',
				'declaration' => sprintf(
					'-webkit-border-bottom-right-radius: %1$s;
					-webkit-border-bottom-left-radius: %1$s;
					-moz-border-radius-bottomright: %1$s;
					-moz-border-radius-bottomleft: %1$s;
					border-bottom-right-radius: %1$s;
					border-bottom-left-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );
		}

		$style = '' !== $style ? " style='{$style}'" : '';

		$image = '' !== $image
			? sprintf( '<div class="et_pb_slide_image"><img src="%1$s" alt="%2$s" /></div>',
				esc_url( $image ),
				esc_attr( $image_alt )
			)
			: '';

		if ( '' !== $video_url ) {
			$video_embed = self::get_video_embed(array(
				'video_url' => $video_url,
			));

			$image = sprintf( '<div class="et_pb_slide_video">%1$s</div>',
				$video_embed
			);
		}

		if ( '' !== $image ) $class = ' et_pb_slide_with_image';

		if ( '' !== $video_url ) $class .= ' et_pb_slide_with_video';

		$class .= " et_pb_bg_layout_{$background_layout}";

		$class .= 'on' === $use_bg_overlay ? ' et_pb_slider_with_overlay' : '';
		$class .= 'on' === $use_text_overlay ? ' et_pb_slider_with_text_overlay' : '';

		if ( 'bottom' !== $alignment ) {
			$class .= " et_pb_media_alignment_{$alignment}";
		}

		$data_dot_nav_custom_color = '' !== $dot_nav_custom_color
			? sprintf( ' data-dots_color="%1$s"', esc_attr( $dot_nav_custom_color ) )
			: '';

		$data_arrows_custom_color = '' !== $arrows_custom_color
			? sprintf( ' data-arrows_color="%1$s"', esc_attr( $arrows_custom_color ) )
			: '';

		$class = ET_Builder_Element::add_module_order_class( $class, $function_name );

		if ( 1 === $et_pb_slider_item_num ) {
			$class .= " et-pb-active-slide";
		}

		$output = sprintf(
			'<div class="et_pb_slide%6$s%13$s"%4$s%10$s%11$s>
				%8$s
				%14$s
				%12$s
				<div class="et_pb_container clearfix">
					<div class="et_pb_slider_container_inner">
						%5$s
						<div class="et_pb_slide_description">
							%1$s
							<div class="et_pb_slide_content%9$s">%2$s</div>
							%3$s
						</div> <!-- .et_pb_slide_description -->
					</div>
				</div> <!-- .et_pb_container -->
				%7$s
			</div> <!-- .et_pb_slide -->
			',
			$heading,
			$this->shortcode_content,
			$button,
			$style,
			$image,
			esc_attr( $class ),
			( '' !== $background_video ? $background_video : '' ),
			( '' !== $background_image && 'on' === $et_pb_slider_parallax ? sprintf( '<div class="et_parallax_bg%2$s" style="background-image: url(%1$s);"></div>', esc_attr( $background_image ), ( 'off' === $et_pb_slider_parallax_method ? ' et_pb_parallax_css' : '' ) ) : '' ),
			( 'on' === $et_pb_slider_hide_mobile['hide_content_on_mobile'] ? esc_attr( " {$hide_on_mobile_class}" ) : '' ),
			$data_dot_nav_custom_color,
			$data_arrows_custom_color,
			'on' === $use_bg_overlay ? '<div class="et_pb_slide_overlay_container"></div>' : '',
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Slider_Item;

class ET_Builder_Module_Post_Slider extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Post Slider', 'et_builder' );
		$this->slug       = 'et_pb_post_slider';
		$this->fb_support = true;

		// need to use global settings from the slider module
		$this->global_settings_slug = 'et_pb_slider';

		$this->whitelisted_fields = array(
			'show_arrows',
			'show_pagination',
			'auto',
			'auto_speed',
			'auto_ignore_hover',
			'parallax',
			'parallax_method',
			'remove_inner_shadow',
			'background_position',
			'background_size',
			'admin_label',
			'module_id',
			'module_class',
			'top_padding',
			'bottom_padding',
			'hide_content_on_mobile',
			'hide_cta_on_mobile',
			'show_image_video_mobile',
			'include_categories',
			'posts_number',
			'show_more_button',
			'more_text',
			'content_source',
			'background_color',
			'show_image',
			'image_placement',
			'background_image',
			'background_layout',
			'use_bg_overlay',
			'use_text_overlay',
			'bg_overlay_color',
			'text_overlay_color',
			'orderby',
			'show_meta',
			'use_manual_excerpt',
			'excerpt_length',
			'text_border_radius',
			'arrows_custom_color',
			'dot_nav_custom_color',
			'top_padding_tablet',
			'top_padding_phone',
			'top_padding_last_edited',
			'bottom_padding_tablet',
			'bottom_padding_phone',
			'bottom_padding_last_edited',
		);

		$this->fields_defaults = array(
			'show_arrows'             => array( 'on' ),
			'show_pagination'         => array( 'on' ),
			'auto'                    => array( 'off' ),
			'auto_speed'              => array( '7000' ),
			'auto_ignore_hover'       => array( 'off' ),
			'parallax'                => array( 'off' ),
			'parallax_method'         => array( 'off' ),
			'remove_inner_shadow'     => array( 'off' ),
			'background_position'     => array( 'center' ),
			'background_size'         => array( 'cover' ),
			'hide_content_on_mobile'  => array( 'off' ),
			'hide_cta_on_mobile'      => array( 'off' ),
			'show_image_video_mobile' => array( 'off' ),
			'more_text'               => array( 'Read More' ),
			'background_color'        => array( '' ),
			'image_placement'         => array( 'background' ),
			'background_layout'       => array( 'dark' ),
			'orderby'                 => array( 'date_desc' ),
			'excerpt_length'          => array( '270' ),
			'use_bg_overlay'          => array( 'on' ),
			'show_meta'               => array( 'on' ),
			'show_more_button'        => array( 'on' ),
			'show_image'              => array( 'on' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_slider';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'   => esc_html__( 'Content', 'et_builder' ),
					'elements'       => esc_html__( 'Elements', 'et_builder' ),
					'featured_image' => esc_html__( 'Featured Image', 'et_builder' ),
					'background'     => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'     => esc_html__( 'Layout', 'et_builder' ),
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'navigation' => esc_html__( 'Navigation', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'padding'  => array(
						'title'    => esc_html__( 'Spacing', 'et_builder' ),
						'priority' => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title",
						'important' => array( 'size', 'font-size', 'plugin_all' ),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element}, {$this->main_css_element} .et_pb_slide_content",
						'main' => "{$this->main_css_element} .et_pb_slide_content, {$this->main_css_element} .et_pb_slide_content div",
						'important' => 'all',
					),
				),
				'meta'   => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_slide_content .post-meta, {$this->main_css_element} .et_pb_slide_content .post-meta a",
						'plugin_main' => "{$this->main_css_element} .et_pb_slide_content .post-meta, {$this->main_css_element} .et_pb_slide_content .post-meta a, {$this->main_css_element} .et_pb_slide_content .post-meta span",
						'important' => 'all',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '16px',
					),
					'letter_spacing' => array(
						'default' => '0',
					),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
					),
				),
			),
			'background' => array(
				'use_background_color'          => true,
				'use_background_color_gradient' => true,
				'use_background_image'          => true,
				'use_background_video'          => true,
			),
		);

		$this->custom_css_options = array(
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description .et_pb_slide_title',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector' => '.et_pb_slider a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_controllers' => array(
				'label'    => esc_html__( 'Slide Controllers', 'et_builder' ),
				'selector' => '.et-pb-controllers',
			),
			'slide_active_controller' => array(
				'label'    => esc_html__( 'Slide Active Controller', 'et_builder' ),
				'selector' => '.et-pb-controllers .et-pb-active-control',
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
			'slide_arrows' => array(
				'label'    => esc_html__( 'Slide Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'posts_number' => array(
				'label'             => esc_html__( 'Posts Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Choose how many posts you would like to display in the slider.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'renderer'         => 'et_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'description'      => esc_html__( 'Choose which categories you would like to include in the slider.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
			),
			'orderby' => array(
				'label'             => esc_html__( 'Order By', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'date_desc'  => esc_html__( 'Date: new to old', 'et_builder' ),
					'date_asc'   => esc_html__( 'Date: old to new', 'et_builder' ),
					'title_asc'  => esc_html__( 'Title: a-z', 'et_builder' ),
					'title_desc' => esc_html__( 'Title: z-a', 'et_builder' ),
					'rand'       => esc_html__( 'Random', 'et_builder' ),
				),
				'toggle_slug'       => 'main_content',
				'description'       => esc_html__( 'Here you can adjust the order in which posts are displayed.', 'et_builder' ),
				'computed_affects'  => array(
					'__posts',
				),
			),
			'show_arrows'         => array(
				'label'           => esc_html__( 'Show Arrows', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'This setting will turn on and off the navigation arrows.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Controls', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'This setting will turn on and off the circle buttons at the bottom of the slider.', 'et_builder' ),
			),
			'show_more_button' => array(
				'label'             => esc_html__( 'Show Read More Button', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					'more_text',
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'This setting will turn on and off the read more button.', 'et_builder' ),
			),
			'more_text' => array(
				'label'             => esc_html__( 'Button Text', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'main_content',
				'description'       => esc_html__( 'Define the text which will be displayed on "Read More" button. leave blank for default ( Read More )', 'et_builder' ),
			),
			'content_source' => array(
				'label'             => esc_html__( 'Content Display', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'Show Excerpt', 'et_builder' ),
					'on'  => esc_html__( 'Show Content', 'et_builder' ),
				),
				'affects' => array(
					'use_manual_excerpt',
					'excerpt_length',
				),
				'description'       => esc_html__( 'Showing the full content will not truncate your posts in the slider. Showing the excerpt will only display excerpt text.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'use_manual_excerpt' => array(
				'label'             => esc_html__( 'Use Post Excerpt if Defined', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Disable this option if you want to ignore manually defined excerpts and always generate it automatically.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'excerpt_length' => array(
				'label'             => esc_html__( 'Automatic Excerpt Length', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Define the length of automatically generated excerpts. Leave blank for default ( 270 ) ', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'show_meta' => array(
				'label'           => esc_html__( 'Show Post Meta', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'This setting will turn on and off the meta section.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text is light or dark. If you have a slide with a dark background, then choose light text. If you have a light background, then use dark text.' , 'et_builder' ),
			),
			'show_image' => array(
				'label'             => esc_html__( 'Show Featured Image', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					'image_placement',
				),
				'toggle_slug'       => 'featured_image',
				'description'       => esc_html__( 'This setting will turn on and off the featured image in the slider.', 'et_builder' ),
			),
			'image_placement' => array(
				'label'             => esc_html__( 'Image Placement', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'background' => esc_html__( 'Background', 'et_builder' ),
					'left'       => esc_html__( 'Left', 'et_builder' ),
					'right'      => esc_html__( 'Right', 'et_builder' ),
					'top'        => esc_html__( 'Top', 'et_builder' ),
					'bottom'     => esc_html__( 'Bottom', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'featured_image',
				'description'       => esc_html__( 'Select how you would like to display the featured image in slides', 'et_builder' ),
			),
			'use_bg_overlay'      => array(
				'label'           => esc_html__( 'Use Background Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'bg_overlay_color',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'et_builder' ),
			),
			'bg_overlay_color' => array(
				'label'             => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'et_builder' ),
			),
			'use_text_overlay'      => array(
				'label'           => esc_html__( 'Use Text Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'yes', 'et_builder' ),
				),
				'affects'           => array(
					'text_overlay_color',
					'text_border_radius',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'description'      => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'et_builder' ),
			),
			'text_overlay_color' => array(
				'label'             => esc_html__( 'Text Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'et_builder' ),
			),
			'remove_inner_shadow' => array(
				'label'           => esc_html__( 'Remove Inner Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'auto' => array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
					'auto_ignore_hover',
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
			),
			'auto_speed' => array(
				'label'             => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_default'   => true,
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
			),
			'auto_ignore_hover' => array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'et_builder' ),
			),
			'top_padding' => array(
				'label'           => esc_html__( 'Top Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'top_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'bottom_padding' => array(
				'label'           => esc_html__( 'Bottom Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'bottom_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'hide_content_on_mobile' => array(
				'label'           => esc_html__( 'Hide Content On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'hide_cta_on_mobile' => array(
				'label'           => esc_html__( 'Hide CTA On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'show_image_video_mobile' => array(
				'label'           => esc_html__( 'Show Image On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'text_border_radius' => array(
				'label'           => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'default'         => '3',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
			),
			'arrows_custom_color' => array(
				'label'        => esc_html__( 'Arrows Custom Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'dot_nav_custom_color' => array(
				'label'        => esc_html__( 'Dot Nav Custom Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'top_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'top_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bottom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bottom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'__posts' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Post_Slider', 'get_blog_posts' ),
				'computed_depends_on' => array(
					'posts_number',
					'include_categories',
					'orderby',
					'content_source',
					'use_manual_excerpt',
					'excerpt_length',
				),
			),
		);

		return $fields;
	}

	static function get_blog_posts( $args = array(), $conditional_tags = array(), $current_page = array(), $is_ajax_request = true ) {
		$defaults = array(
			'posts_number'       => '',
			'include_categories' => '',
			'orderby'            => '',
			'content_source'     => '',
			'use_manual_excerpt' => '',
			'excerpt_length'     => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array(
			'posts_per_page' => (int) $args['posts_number'],
			'post_status'    => 'publish',
		);

		if ( '' !== $args['include_categories'] ) {
			$query_args['cat'] = $args['include_categories'];
		}

		if ( 'date_desc' !== $args['orderby'] ) {
			switch( $args['orderby'] ) {
				case 'date_asc' :
					$query_args['orderby'] = 'date';
					$query_args['order'] = 'ASC';
					break;
				case 'title_asc' :
					$query_args['orderby'] = 'title';
					$query_args['order'] = 'ASC';
					break;
				case 'title_desc' :
					$query_args['orderby'] = 'title';
					$query_args['order'] = 'DESC';
					break;
				case 'rand' :
					$query_args['orderby'] = 'rand';
					break;
			}
		}

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_author_id = $query->posts[ $post_index ]->post_author;

				$categories = array();

				$categories_object = get_the_terms( get_the_ID(), 'category' );

				if ( ! empty( $categories_object ) ) {
					foreach ( $categories_object as $category ) {
						$categories[] = array(
							'id' => $category->term_id,
							'label' => $category->name,
							'permalink' => get_term_link( $category ),
						);
					}
				}

				$query->posts[ $post_index ]->post_featured_image = esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) );
				$query->posts[ $post_index ]->has_post_thumbnail  = has_post_thumbnail();
				$query->posts[ $post_index ]->post_permalink      = get_the_permalink();
				$query->posts[ $post_index ]->post_author_url     = get_author_posts_url( $post_author_id );
				$query->posts[ $post_index ]->post_author_name    = get_the_author_meta( 'display_name', $post_author_id );
				$query->posts[ $post_index ]->post_date_readable  = get_the_date();
				$query->posts[ $post_index ]->categories          = $categories;
				$query->posts[ $post_index ]->post_comment_popup  = sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) );

				$post_content = et_strip_shortcodes( get_the_content(), true );

				global $et_fb_processing_shortcode_object, $et_pb_rendering_column_content;

				$global_processing_original_value = $et_fb_processing_shortcode_object;

				// reset the fb processing flag
				$et_fb_processing_shortcode_object = false;
				// set the flag to indicate that we're processing internal content
				$et_pb_rendering_column_content = true;

				if ( $is_ajax_request ) {
					// reset all the attributes required to properly generate the internal styles
					ET_Builder_Element::clean_internal_modules_styles();
				}

				if ( 'on' === $args['content_source'] ) {
					global $more;

					// page builder doesn't support more tag, so display the_content() in case of post made with page builder
					if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
						$more = 1;

						$builder_post_content = et_is_builder_plugin_active() ? do_shortcode( $post_content ) : apply_filters( 'the_content', $post_content );

						// Overwrite default content, in case the content is protected
						$query->posts[ $post_index ]->post_content = $builder_post_content;
					} else {
						$more = null;

						// Overwrite default content, in case the content is protected
						$query->posts[ $post_index ]->post_content = et_is_builder_plugin_active() ? do_shortcode( get_the_content( '' ) ) : apply_filters( 'the_content', get_the_content( '' ) );
					}
				} else {
					if ( has_excerpt() && 'off' !== $args['use_manual_excerpt'] ) {
						$query->posts[ $post_index ]->post_content =  et_is_builder_plugin_active() ? do_shortcode( et_strip_shortcodes( get_the_excerpt(), true ) ) : apply_filters( 'the_content', et_strip_shortcodes( get_the_excerpt(), true ) );
					} else {
						$query->posts[ $post_index ]->post_content = strip_shortcodes( truncate_post( intval( $args['excerpt_length'] ), false, '', true ) );
					}
				}

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				if ( $is_ajax_request ) {
					// retrieve the styles for the modules inside Blog content
					$internal_style = ET_Builder_Element::get_style( true );

					// reset all the attributes after we retrieved styles
					ET_Builder_Element::clean_internal_modules_styles( false );

					$query->posts[ $post_index ]->internal_styles = $internal_style;
				}

				$et_pb_rendering_column_content = false;

				$post_index++;
			} // end while
			wp_reset_query();
		} // end if

		return $query;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$show_arrows             = $this->shortcode_atts['show_arrows'];
		$show_pagination         = $this->shortcode_atts['show_pagination'];
		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$auto                    = $this->shortcode_atts['auto'];
		$auto_speed              = $this->shortcode_atts['auto_speed'];
		$auto_ignore_hover       = $this->shortcode_atts['auto_ignore_hover'];
		$top_padding             = $this->shortcode_atts['top_padding'];
		$body_font_size          = $this->shortcode_atts['body_font_size'];
		$bottom_padding          = $this->shortcode_atts['bottom_padding'];
		$remove_inner_shadow     = $this->shortcode_atts['remove_inner_shadow'];
		$hide_content_on_mobile  = $this->shortcode_atts['hide_content_on_mobile'];
		$hide_cta_on_mobile      = $this->shortcode_atts['hide_cta_on_mobile'];
		$show_image_video_mobile = $this->shortcode_atts['show_image_video_mobile'];
		$background_position     = $this->shortcode_atts['background_position'];
		$background_size         = $this->shortcode_atts['background_size'];
		$background_repeat       = $this->shortcode_atts['background_repeat'];
		$background_blend        = $this->shortcode_atts['background_blend'];
		$posts_number            = $this->shortcode_atts['posts_number'];
		$include_categories      = $this->shortcode_atts['include_categories'];
		$show_more_button        = $this->shortcode_atts['show_more_button'];
		$more_text               = $this->shortcode_atts['more_text'];
		$content_source          = $this->shortcode_atts['content_source'];
		$background_color        = $this->shortcode_atts['background_color'];
		$show_image              = $this->shortcode_atts['show_image'];
		$image_placement         = $this->shortcode_atts['image_placement'];
		$background_image        = $this->shortcode_atts['background_image'];
		$background_layout       = $this->shortcode_atts['background_layout'];
		$use_bg_overlay          = $this->shortcode_atts['use_bg_overlay'];
		$bg_overlay_color        = $this->shortcode_atts['bg_overlay_color'];
		$use_text_overlay        = $this->shortcode_atts['use_text_overlay'];
		$text_overlay_color      = $this->shortcode_atts['text_overlay_color'];
		$orderby                 = $this->shortcode_atts['orderby'];
		$show_meta               = $this->shortcode_atts['show_meta'];
		$button_custom           = $this->shortcode_atts['custom_button'];
		$custom_icon             = $this->shortcode_atts['button_icon'];
		$use_manual_excerpt      = $this->shortcode_atts['use_manual_excerpt'];
		$excerpt_length          = $this->shortcode_atts['excerpt_length'];
		$text_border_radius      = $this->shortcode_atts['text_border_radius'];
		$dot_nav_custom_color    = $this->shortcode_atts['dot_nav_custom_color'];
		$arrows_custom_color     = $this->shortcode_atts['arrows_custom_color'];
		$top_padding_tablet      = $this->shortcode_atts['top_padding_tablet'];
		$top_padding_phone       = $this->shortcode_atts['top_padding_phone'];
		$top_padding_last_edited = $this->shortcode_atts['top_padding_last_edited'];
		$bottom_padding_tablet   = $this->shortcode_atts['bottom_padding_tablet'];
		$bottom_padding_phone    = $this->shortcode_atts['bottom_padding_phone'];
		$bottom_padding_last_edited = $this->shortcode_atts['bottom_padding_last_edited'];

		$post_index = 0;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$hide_on_mobile_class = self::HIDE_ON_MOBILE;

		if ( '' !== $top_padding || '' !== $top_padding_tablet || '' !== $top_padding_phone ) {
			$top_padding_responsive_active = et_pb_get_responsive_status( $top_padding_last_edited );

			$padding_values = array(
				'desktop' => $top_padding,
				'tablet'  => $top_padding_responsive_active ? $top_padding_tablet : '',
				'phone'   => $top_padding_responsive_active ? $top_padding_phone : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description', 'padding-top', $function_name );
		}

		if ( '' !== $bottom_padding || '' !== $bottom_padding_tablet || '' !== $bottom_padding_phone ) {
			$bottom_padding_responsive_active = et_pb_get_responsive_status( $bottom_padding_last_edited );

			$padding_values = array(
				'desktop' => $bottom_padding,
				'tablet'  => $bottom_padding_responsive_active ? $bottom_padding_tablet : '',
				'phone'   => $bottom_padding_responsive_active ? $bottom_padding_phone : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description', 'padding-bottom', $function_name );
		}

		if ( '' !== $bottom_padding || '' !== $top_padding ) {
			ET_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description',
				'declaration' => 'padding-right: 0; padding-left: 0;',
			) );
		}

		// Applying backround-related style to slide item since advanced_option only targets module wrapper
		if ( 'on' === $this->shortcode_atts['show_image'] && 'background' === $this->shortcode_atts['image_placement'] && 'off' === $parallax ) {
			if ('' !== $background_color) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide:not(.et_pb_slide_with_no_image)',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $background_color )
					),
				) );
			}

			if ( '' !== $background_size && 'default' !== $background_size ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'-moz-background-size: %1$s;
						-webkit-background-size: %1$s;
						background-size: %1$s;',
						esc_html( $background_size )
					),
				) );

				if ( 'initial' === $background_size ) {
					ET_Builder_Module::set_style( $function_name, array(
						'selector'    => 'body.ie %%order_class%% .et_pb_slide',
						'declaration' => '-moz-background-size: auto; -webkit-background-size: auto; background-size: auto;',
					) );
				}
			}

			if ( '' !== $background_position && 'default' !== $background_position ) {
				$processed_position = str_replace( '_', ' ', $background_position );

				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'background-position: %1$s;',
						esc_html( $processed_position )
					),
				) );
			}

			if ( '' !== $background_repeat ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'background-repeat: %1$s;',
						esc_html( $background_repeat )
					),
				) );
			}

			if ( '' !== $background_blend ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'background-blend-mode: %1$s;',
						esc_html( $background_blend )
					),
				) );
			}
		}

		if ( 'on' === $use_bg_overlay && '' !== $bg_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide .et_pb_slide_overlay_container',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $bg_overlay_color )
				),
			) );
		}

		if ( 'on' === $use_text_overlay && '' !== $text_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide .et_pb_slide_title, %%order_class%% .et_pb_slide .et_pb_slide_content',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $text_overlay_color )
				),
			) );
		}

		if ( '' !== $text_border_radius ) {
			$border_radius_value = et_builder_process_range_value( $text_border_radius );
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_slider_with_text_overlay h2.et_pb_slide_title',
				'declaration' => sprintf(
					'-webkit-border-top-left-radius: %1$s;
					-webkit-border-top-right-radius: %1$s;
					-moz-border-radius-topleft: %1$s;
					-moz-border-radius-topright: %1$s;
					border-top-left-radius: %1$s;
					border-top-right-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_slider_with_text_overlay .et_pb_slide_content',
				'declaration' => sprintf(
					'-webkit-border-bottom-right-radius: %1$s;
					-webkit-border-bottom-left-radius: %1$s;
					-moz-border-radius-bottomright: %1$s;
					-moz-border-radius-bottomleft: %1$s;
					border-bottom-right-radius: %1$s;
					border-bottom-left-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );
		}

		$fullwidth = 'et_pb_fullwidth_slider' === $function_name ? 'on' : 'off';

		$class  = '';
		$class .= 'off' === $fullwidth ? ' et_pb_slider_fullwidth_off' : '';
		$class .= 'off' === $show_arrows ? ' et_pb_slider_no_arrows' : '';
		$class .= 'off' === $show_pagination ? ' et_pb_slider_no_pagination' : '';
		$class .= 'on' === $parallax ? ' et_pb_slider_parallax' : '';
		$class .= 'on' === $auto ? ' et_slider_auto et_slider_speed_' . esc_attr( $auto_speed ) : '';
		$class .= 'on' === $auto_ignore_hover ? ' et_slider_auto_ignore_hover' : '';
		$class .= 'on' === $remove_inner_shadow ? ' et_pb_slider_no_shadow' : '';
		$class .= 'on' === $show_image_video_mobile ? ' et_pb_slider_show_image' : '';
		$class .= ' et_pb_post_slider_image_' . $image_placement;
		$class .= 'on' === $use_bg_overlay ? ' et_pb_slider_with_overlay' : '';
		$class .= 'on' === $use_text_overlay ? ' et_pb_slider_with_text_overlay' : '';

		$data_dot_nav_custom_color = '' !== $dot_nav_custom_color
			? sprintf( ' data-dots_color="%1$s"', esc_attr( $dot_nav_custom_color ) )
			: '';

		$data_arrows_custom_color = '' !== $arrows_custom_color
			? sprintf( ' data-arrows_color="%1$s"', esc_attr( $arrows_custom_color ) )
			: '';

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		ob_start();

		// Re-used self::get_blog_posts() for builder output
		$query = self::get_blog_posts(array(
			'posts_number'       => $posts_number,
			'include_categories' => $include_categories,
			'orderby'            => $orderby,
			'content_source'     => $content_source,
			'use_manual_excerpt' => $use_manual_excerpt,
			'excerpt_length'     => $excerpt_length,
		), array(), array(), false );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$slide_class = 'off' !== $show_image && in_array( $image_placement, array( 'left', 'right' ) ) && has_post_thumbnail() ? ' et_pb_slide_with_image' : '';
				$slide_class .= 'off' !== $show_image && ! has_post_thumbnail() ? ' et_pb_slide_with_no_image' : '';
				$slide_class .= " et_pb_bg_layout_{$background_layout}";
			?>
			<div class="et_pb_slide et_pb_media_alignment_center<?php echo esc_attr( $slide_class ); ?>" <?php if ( 'on' !== $parallax && 'off' !== $show_image && 'background' === $image_placement ) { printf( 'style="background-image:url(%1$s)"', esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ) );  } ?><?php echo $data_dot_nav_custom_color; echo $data_arrows_custom_color; ?>>
				<?php if ( 'on' === $parallax && 'off' !== $show_image && 'background' === $image_placement ) { ?>
					<div class="et_parallax_bg<?php if ( 'off' === $parallax_method ) { echo ' et_pb_parallax_css'; } ?>" style="background-image: url(<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>);"></div>
				<?php } ?>
				<?php if ( 'on' === $use_bg_overlay ) { ?>
					<div class="et_pb_slide_overlay_container"></div>
				<?php } ?>
				<div class="et_pb_container clearfix">
					<div class="et_pb_slider_container_inner">
						<?php if ( 'off' !== $show_image && has_post_thumbnail() && ! in_array( $image_placement, array( 'background', 'bottom' ) ) ) { ?>
							<div class="et_pb_slide_image">
								<?php the_post_thumbnail(); ?>
							</div>
						<?php } ?>
						<div class="et_pb_slide_description">
							<h2 class="et_pb_slide_title"><?php the_title(); ?></h2>
							<div class="et_pb_slide_content <?php if ( 'on' === $hide_content_on_mobile ) { echo esc_attr( $hide_on_mobile_class ); } ?>">
								<?php
								if ( 'off' !== $show_meta ) {
									printf(
										'<p class="post-meta">%1$s | %2$s | %3$s | %4$s</p>',
										et_get_safe_localization( sprintf( __( 'by %s', 'et_builder' ), '<span class="author vcard">' .  et_pb_get_the_author_posts_link() . '</span>' ) ),
										et_get_safe_localization( sprintf( __( '%s', 'et_builder' ), '<span class="published">' . esc_html( get_the_date() ) . '</span>' ) ),
										get_the_category_list(', '),
										sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) )
									);
								}
								?>
								<?php
									echo $query->posts[ $post_index ]->post_content;
								?>
							</div>
							<?php if ( 'off' !== $show_more_button && '' !== $more_text ) {
									printf(
										'<a href="%1$s" class="et_pb_more_button et_pb_button%4$s%5$s"%3$s>%2$s</a>',
										esc_url( get_permalink() ),
										esc_html( $more_text ),
										'' !== $custom_icon && 'on' === $button_custom ? sprintf(
											' data-icon="%1$s"',
											esc_attr( et_pb_process_font_icon( $custom_icon ) )
										) : '',
										'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : '',
										'on' === $hide_cta_on_mobile ? esc_attr( " {$hide_on_mobile_class}" ) : ''
									);
								}
							?>
						</div> <!-- .et_pb_slide_description -->
						<?php if ( 'off' !== $show_image && has_post_thumbnail() && 'bottom' === $image_placement ) { ?>
							<div class="et_pb_slide_image">
								<?php the_post_thumbnail(); ?>
							</div>
						<?php } ?>
					</div>
				</div> <!-- .et_pb_container -->
			</div> <!-- .et_pb_slide -->
		<?php
			$post_index++;

			} // end while
			wp_reset_query();
		} // end if

		$content = ob_get_contents();

		ob_end_clean();

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_slider et_pb_post_slider%1$s%4$s%5$s%7$s">
				%8$s
				%6$s
				<div class="et_pb_slides">
					%2$s
				</div> <!-- .et_pb_slides -->
			</div> <!-- .et_pb_slider -->
			',
			$class,
			$content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Post_Slider;

class ET_Builder_Module_Testimonial extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Testimonial', 'et_builder' );
		$this->slug       = 'et_pb_testimonial';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'author',
			'job_title',
			'company_name',
			'url',
			'url_new_window',
			'portrait_url',
			'quote_icon',
			'use_background_color',
			'background_color',
			'background_layout',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'quote_icon_color',
			'quote_icon_background_color',
			'portrait_border_radius',
			'portrait_width',
			'portrait_height',
		);

		$this->fields_defaults = array(
			'url_new_window'       => array( 'off' ),
			'quote_icon'           => array( 'on' ),
			'use_background_color' => array( 'on' ),
			'background_color'     => array( '#f5f5f5', 'add_default_setting' ),
			'background_layout'    => array( 'dark' ),
			'text_orientation'     => array( 'left' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_testimonial';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'image'        => esc_html__( 'Image', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon'       => esc_html__( 'Quote Icon', 'et_builder' ),
					'portrait'   => esc_html__( 'Portrait', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'body'   => array(
					'label' => esc_html__( 'Body', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} *",
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_color_gradient' => true,
				'settings'                      => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);

		$this->custom_css_options = array(
			'testimonial_portrait' => array(
				'label'    => esc_html__( 'Testimonial Portrait', 'et_builder' ),
				'selector' => '.et_pb_testimonial_portrait',
			),
			'testimonial_description' => array(
				'label'    => esc_html__( 'Testimonial Description', 'et_builder' ),
				'selector' => '.et_pb_testimonial_description',
			),
			'testimonial_author' => array(
				'label'    => esc_html__( 'Testimonial Author', 'et_builder' ),
				'selector' => '.et_pb_testimonial_author',
			),
			'testimonial_meta' => array(
				'label'    => esc_html__( 'Testimonial Meta', 'et_builder' ),
				'selector' => '.et_pb_testimonial_meta',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'author' => array(
				'label'           => esc_html__( 'Author Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the testimonial author.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'job_title' => array(
				'label'           => esc_html__( 'Job Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the job title.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'company_name' => array(
				'label'           => esc_html__( 'Company Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the company.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'url' => array(
				'label'           => esc_html__( 'Author/Company URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the website of the author or leave blank for no link.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'URLs Open', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'link',
				'description'     => esc_html__( 'Choose whether or not the URL should open in a new window.', 'et_builder' ),
			),
			'portrait_url' => array(
				'label'              => esc_html__( 'Portrait Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'image',
			),
			'quote_icon' => array(
				'label'           => esc_html__( 'Quote Icon', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Visible', 'et_builder' ),
					'off' => esc_html__( 'Hidden', 'et_builder' ),
				),
				'description'     => esc_html__( 'Choose whether or not the quote icon should be visible.', 'et_builder' ),
				'toggle_slug'     => 'elements',
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom background color for your CTA.', 'et_builder' ),
				'depends_default'   => true,
				'toggle_slug'       => 'background',
			),
			'use_background_color' => array(
				'label'           => esc_html__( 'Use Background Color', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'background_color',
				),
				'toggle_slug'     => 'background',
				'description'     => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'This will adjust the alignment of the module text.', 'et_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'quote_icon_color' => array(
				'label'             => esc_html__( 'Quote Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
			),
			'quote_icon_background_color' => array(
				'label'             => esc_html__( 'Quote Icon Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
				'default'           => '#f5f5f5',
				'shortcode_default' => '#f5f5f5',
			),
			'portrait_border_radius' => array(
				'label'           => esc_html__( 'Portrait Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'portrait',
			),
			'portrait_width' => array(
				'label'           => esc_html__( 'Portrait Width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'portrait',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
			),
			'portrait_height' => array(
				'label'           => esc_html__( 'Portrait Height', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'portrait',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id              = $this->shortcode_atts['module_id'];
		$module_class           = $this->shortcode_atts['module_class'];
		$author                 = $this->shortcode_atts['author'];
		$job_title              = $this->shortcode_atts['job_title'];
		$portrait_url           = $this->shortcode_atts['portrait_url'];
		$company_name           = $this->shortcode_atts['company_name'];
		$url                    = $this->shortcode_atts['url'];
		$quote_icon             = $this->shortcode_atts['quote_icon'];
		$url_new_window         = $this->shortcode_atts['url_new_window'];
		$use_background_color   = $this->shortcode_atts['use_background_color'];
		$background_color       = $this->shortcode_atts['background_color'];
		$background_layout      = $this->shortcode_atts['background_layout'];
		$text_orientation       = $this->shortcode_atts['text_orientation'];
		$quote_icon_color       = $this->shortcode_atts['quote_icon_color'];
		$quote_icon_background_color = $this->shortcode_atts['quote_icon_background_color'];
		$portrait_border_radius = $this->shortcode_atts['portrait_border_radius'];
		$portrait_width         = $this->shortcode_atts['portrait_width'];
		$portrait_height        = $this->shortcode_atts['portrait_height'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $portrait_border_radius ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_testimonial_portrait, %%order_class%% .et_pb_testimonial_portrait:before',
				'declaration' => sprintf(
					'-webkit-border-radius: %1$s; -moz-border-radius: %1$s; border-radius: %1$s;',
					esc_html( et_builder_process_range_value( $portrait_border_radius ) )
				),
			) );
		}

		if ( '' !== $portrait_width ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_testimonial_portrait',
				'declaration' => sprintf(
					'width: %1$s;',
					esc_html( et_builder_process_range_value( $portrait_width ) )
				),
			) );
		}

		if ( '' !== $portrait_height ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_testimonial_portrait',
				'declaration' => sprintf(
					'height: %1$s;',
					esc_html( et_builder_process_range_value( $portrait_height ) )
				),
			) );
		}

		$style = '';

		if ( 'on' === $use_background_color && $this->fields_defaults['background_color'][0] !== $background_color ) {
			$style .= sprintf(
				'background-color: %1$s !important; ',
				esc_html( $background_color )
			);
		}

		if ( '' !== $style ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_testimonial',
				'declaration' => rtrim( $style ),
			) );
		}

		if ( '' !== $quote_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_testimonial:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $quote_icon_color )
				),
			) );
		}

		if ( '' !== $quote_icon_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_testimonial:before',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $quote_icon_background_color )
				),
			) );
		}

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$portrait_image = '';

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

		if ( ! isset( $atts['quote_icon'] ) ) {
			$class .= "	et_pb_testimonial_old_layout";
		}

		if ( '' !== $portrait_url ) {
			$portrait_image = sprintf(
				'<div class="et_pb_testimonial_portrait" style="background-image: url(%1$s);">
				</div>',
				esc_attr( $portrait_url )
			);
		}

		if ( '' !== $url && ( '' !== $company_name || '' !== $author ) ) {
			$link_output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				( '' !== $company_name ? esc_html( $company_name ) : esc_html( $author ) ),
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);

			if ( '' !== $company_name ) {
				$company_name = $link_output;
			} else {
				$author = $link_output;
			}
		}

		$output = sprintf(
			'<div%3$s class="et_pb_testimonial%4$s%5$s%9$s%10$s%12$s%13$s clearfix%15$s"%11$s>
				%16$s
				%14$s
				%8$s
				<div class="et_pb_testimonial_description">
					<div class="et_pb_testimonial_description_inner">
					%1$s
					<strong class="et_pb_testimonial_author">%2$s</strong>
					<p class="et_pb_testimonial_meta">%6$s%7$s</p>
					</div> <!-- .et_pb_testimonial_description_inner -->
				</div> <!-- .et_pb_testimonial_description -->
			</div> <!-- .et_pb_testimonial -->',
			$this->shortcode_content,
			$author,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'off' === $quote_icon ? ' et_pb_icon_off' : '' ),
			( '' !== $job_title ? esc_html( $job_title ) : '' ),
			( '' !== $company_name
				? sprintf( '%2$s%1$s',
					$company_name,
					( '' !== $job_title ? ', ' : '' )
				)
				: ''
			),
			( '' !== $portrait_image ? $portrait_image : '' ),
			( '' === $portrait_image ? ' et_pb_testimonial_no_image' : '' ),
			esc_attr( $class ),
			( 'on' === $use_background_color
				? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
				: ''
			),
			( 'off' === $use_background_color ? ' et_pb_testimonial_no_bg' : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background

		);

		return $output;
	}
}
new ET_Builder_Module_Testimonial;

class ET_Builder_Module_Pricing_Tables extends ET_Builder_Module {
	function init() {
		$this->name                 = esc_html__( 'Pricing Tables', 'et_builder' );
		$this->slug                 = 'et_pb_pricing_tables';
		$this->fb_support           = true;
		$this->main_css_element 	= '%%order_class%%.et_pb_pricing';
		$this->child_slug           = 'et_pb_pricing_table';
		$this->child_item_text      = esc_html__( 'Pricing Table', 'et_builder' );

		$this->whitelisted_fields = array(
			'admin_label',
			'module_id',
			'module_class',
			'featured_table_background_color',
			'header_background_color',
			'featured_table_header_background_color',
			'featured_table_header_text_color',
			'featured_table_subheader_text_color',
			'featured_table_price_color',
			'featured_table_text_color',
			'show_bullet',
			'bullet_color',
			'featured_table_bullet_color',
			'remove_featured_drop_shadow',
			'center_list_items',
		);

		$this->fields_defaults = array(
			'show_bullet'                 => array( 'on' ),
			'remove_featured_drop_shadow' => array( 'off' ),
			'center_list_items'           => array( 'off' ),
		);

		$this->additional_shortcode = 'et_pb_pricing_item';
		$this->main_css_element = '%%order_class%%';
		$this->custom_css_options = array(
			'pricing_heading' => array(
				'label'    => esc_html__( 'Pricing Heading', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading',
			),
			'pricing_title' => array(
				'label'    => esc_html__( 'Pricing Title', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading h2',
			),
			'pricing_subtitle' => array(
				'label'    => esc_html__( 'Pricing Subtitle', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading .et_pb_best_value',
			),
			'pricing_top' => array(
				'label'    => esc_html__( 'Pricing Top', 'et_builder' ),
				'selector' => '.et_pb_pricing_content_top',
			),
			'price' => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => '.et_pb_et_price',
			),
			'currency' => array(
				'label'    => esc_html__( 'Currency', 'et_builder' ),
				'selector' => '.et_pb_dollar_sign',
			),
			'frequency' => array(
				'label'    => esc_html__( 'Frequency', 'et_builder' ),
				'selector' => '.et_pb_frequency',
			),
			'pricing_content' => array(
				'label'    => esc_html__( 'Pricing Content', 'et_builder' ),
				'selector' => '.et_pb_pricing_content',
			),
			'pricing_item' => array(
				'label'    => esc_html__( 'Pricing Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li',
			),
			'pricing_item_excluded' => array(
				'label'    => esc_html__( 'Excluded Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li.et_pb_not_available',
			),
			'pricing_button' => array(
				'label'    => esc_html__( 'Pricing Button', 'et_builder' ),
				'selector' => '.et_pb_pricing_table_button',
			),
			'featured_table' => array(
				'label'    => esc_html__( 'Featured Table', 'et_builder' ),
				'selector' => '.et_pb_featured_table',
			),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'    => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
					'bullet' => esc_html__( 'Bullet', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_pricing_heading h2",
						'important' => 'all',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_pricing li",
						'plugin_main' => "{$this->main_css_element} .et_pb_pricing li, {$this->main_css_element} .et_pb_pricing li span, {$this->main_css_element} .et_pb_pricing li a",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'font_size' => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'subheader' => array(
					'label'    => esc_html__( 'Subheader', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_best_value",
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'currency_frequency' => array(
					'label'    => esc_html__( 'Currency &amp; Frequency', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_dollar_sign, {$this->main_css_element} .et_pb_frequency",
					),
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_sum",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
			'background' => array(
				'use_background_image' => true,
				'css'  => array(
					'main' => "{$this->main_css_element} .et_pb_pricing_table",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_pricing_table",
				),
				'additional_elements' => array(
					"{$this->main_css_element} .et_pb_pricing_content_top" => array( 'bottom' ),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_pricing_table_button.et_pb_button",
					),
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'featured_table_background_color' => array(
				'label'             => esc_html__( 'Featured Table Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
				'priority'          => 23,
			),
			'header_background_color' => array(
				'label'             => esc_html__( 'Table Header Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'featured_table_header_background_color' => array(
				'label'             => esc_html__( 'Featured Table Header Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
				'priority'          => 21,
			),
			'featured_table_header_text_color' => array(
				'label'             => esc_html__( 'Featured Table Header Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'header',
				'priority'          => 20,
			),
			'featured_table_subheader_text_color' => array(
				'label'             => esc_html__( 'Featured Table Subheader Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'subheader',
				'priority'          => 20,
			),
			'featured_table_price_color' => array(
				'label'             => esc_html__( 'Featured Table Price Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'price',
				'priority'          => 20,
			),
			'featured_table_text_color' => array(
				'label'             => esc_html__( 'Featured Table Body Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'body',
				'priority'          => 22,
			),
			'show_bullet' => array(
				'label'           => esc_html__( 'Show Bullet', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'affects'         => array(
					'bullet_color',
				),
			),
			'bullet_color' => array(
				'label'             => esc_html__( 'Bullet Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'bullet',
				'depends_show_if'   => 'on',
			),
			'featured_table_bullet_color' => array(
				'label'             => esc_html__( 'Featured Table Bullet Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'bullet',
				'priority'          => 22,
			),
			'remove_featured_drop_shadow' => array(
				'label'           => esc_html__( 'Remove Featured Table Drop Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'priority'        => 24,
			),
			'center_list_items' => array(
				'label'           => esc_html__( 'Center List Items', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_pricing_tables_num, $et_pb_pricing_tables_icon;

		$button_custom = $this->shortcode_atts['custom_button'];
		$custom_icon   = $this->shortcode_atts['button_icon'];

		$et_pb_pricing_tables_num = 0;

		$et_pb_pricing_tables_icon = 'on' === $button_custom ? $custom_icon : '';
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                              = $this->shortcode_atts['module_id'];
		$module_class                           = $this->shortcode_atts['module_class'];
		$featured_table_background_color        = $this->shortcode_atts['featured_table_background_color'];
		$featured_table_text_color              = $this->shortcode_atts['featured_table_text_color'];
		$header_background_color                = $this->shortcode_atts['header_background_color'];
		$featured_table_header_background_color = $this->shortcode_atts['featured_table_header_background_color'];
		$featured_table_header_text_color       = $this->shortcode_atts['featured_table_header_text_color'];
		$featured_table_subheader_text_color    = $this->shortcode_atts['featured_table_subheader_text_color'];
		$featured_table_price_color             = $this->shortcode_atts['featured_table_price_color'];
		$bullet_color                           = $this->shortcode_atts['bullet_color'];
		$featured_table_bullet_color            = $this->shortcode_atts['featured_table_bullet_color'];
		$remove_featured_drop_shadow            = $this->shortcode_atts['remove_featured_drop_shadow'];
		$center_list_items                      = $this->shortcode_atts['center_list_items'];
		$show_bullet                            = $this->shortcode_atts['show_bullet'];

		global $et_pb_pricing_tables_num, $et_pb_pricing_tables_icon;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( 'on' === $remove_featured_drop_shadow ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_featured_table',
				'declaration' => '-moz-box-shadow: none; -webkit-box-shadow: none; box-shadow: none;',
			) );
		}

		if ( 'off' === $show_bullet ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_pricing li span:before',
				'declaration' => 'display: none;',
			) );
		}

		if ( 'on' === $center_list_items ) {
			$module_class .= ' et_pb_centered_pricing_items';
		}

		if ( '' !== $featured_table_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_featured_table',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $featured_table_background_color )
				),
			) );
		}

		if ( '' !== $header_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_pricing_heading',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $header_background_color )
				),
			) );
		}

		if ( '' !== $featured_table_header_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_pricing_heading',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $featured_table_header_background_color )
				),
			) );
		}

		if ( '' !== $featured_table_header_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_pricing_heading h2',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $featured_table_header_text_color )
				),
			) );
		}

		if ( '' !== $featured_table_subheader_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_best_value',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $featured_table_subheader_text_color )
				),
			) );
		}

		if ( '' !== $featured_table_price_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_sum',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $featured_table_price_color )
				),
			) );
		}

		if ( '' !== $featured_table_text_color ) {
			$featured_table_text_color_selector = et_is_builder_plugin_active() ? '%%order_class%% .et_pb_featured_table .et_pb_pricing_content li, %%order_class%% .et_pb_featured_table .et_pb_pricing_content li span, %%order_class%% .et_pb_featured_table .et_pb_pricing_content li a' : '%%order_class%% .et_pb_featured_table .et_pb_pricing_content li';
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => $featured_table_text_color_selector,
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $featured_table_text_color )
				),
			) );
		}

		if ( '' !== $bullet_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_pricing li span:before',
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $bullet_color )
				),
			) );
		}

		if ( '' !== $featured_table_bullet_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_pricing li span:before',
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $featured_table_bullet_color )
				),
			) );
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$content = $this->shortcode_content;

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_pricing clearfix%2$s%4$s%5$s%7$s">
				%8$s
				%6$s
				<div class="et_pb_pricing_table_wrap">
					%1$s
				</div>
			</div>',
			$content,
			esc_attr( " et_pb_pricing_{$et_pb_pricing_tables_num}" ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}

	function additional_shortcode_callback( $atts, $content = null, $function_name ) {
		$attributes = shortcode_atts( array(
			'available' => 'on',
		), $atts );

		$output = sprintf( '<li%2$s><span>%1$s</span></li>',
			$content,
			( 'on' !== $attributes['available'] ? ' class="et_pb_not_available"' : '' )
		);
		return $output;
	}
}
new ET_Builder_Module_Pricing_Tables;

class ET_Builder_Module_Pricing_Tables_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Pricing Table', 'et_builder' );
		$this->slug                        = 'et_pb_pricing_table';
		$this->fb_support                  = true;
		$this->main_css_element 		   = '%%order_class%%.et_pb_pricing';
		$this->type                        = 'child';
		$this->child_title_var             = 'title';

		$this->whitelisted_fields = array(
			'featured',
			'title',
			'subtitle',
			'currency',
			'per',
			'sum',
			'button_url',
			'button_text',
			'content_new',
			'pricing_item_excluded_color',
		);

		$this->fields_defaults = array(
			'featured' => array( 'off' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Pricing Table', 'et_builder' );
		$this->settings_text               = esc_html__( 'Pricing Table Settings', 'et_builder' );
		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'   => esc_html__( 'Layout', 'et_builder' ),
					'bullet'   => esc_html__( 'Bullet', 'et_builder' ),
					'excluded' => esc_html__( 'Excluded Item', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_pricing_heading h2",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_pricing li",
						'plugin_main' => "{$this->main_css_element} .et_pb_pricing li, {$this->main_css_element} .et_pb_pricing li span, {$this->main_css_element} .et_pb_pricing li a",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'subheader' => array(
					'label'    => esc_html__( 'Subheader', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_best_value",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'currency_frequency' => array(
					'label'    => esc_html__( 'Currency &amp; Frequency', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_dollar_sign, {$this->main_css_element} .et_pb_frequency",
					),
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_sum",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),

			),
			'background' => array(
				'use_background_image' => true,
				'css' => array(
					'main' => "{$this->main_css_element}.et_pb_pricing_table",
				),
				'settings' => array(
					'color'       => 'alpha',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css'      => array(
						'main' => ".et_pb_pricing {$this->main_css_element} .et_pb_button",
						'plugin_main' => ".et_pb_pricing {$this->main_css_element} .et_pb_pricing_table_button.et_pb_button",
					),
				),
			),
		);

		$this->custom_css_options = array(
			'pricing_heading' => array(
				'label'    => esc_html__( 'Pricing Heading', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading',
			),
			'pricing_title' => array(
				'label'    => esc_html__( 'Pricing Title', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading h2',
			),
			'pricing_subtitle' => array(
				'label'    => esc_html__( 'Pricing Subtitle', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading .et_pb_best_value',
			),
			'pricing_top' => array(
				'label'    => esc_html__( 'Pricing Top', 'et_builder' ),
				'selector' => '.et_pb_pricing_content_top',
			),
			'price' => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => '.et_pb_et_price',
			),
			'currency' => array(
				'label'    => esc_html__( 'Currency', 'et_builder' ),
				'selector' => '.et_pb_dollar_sign',
			),
			'frequency' => array(
				'label'    => esc_html__( 'Frequency', 'et_builder' ),
				'selector' => '.et_pb_frequency',
			),
			'pricing_content' => array(
				'label'    => esc_html__( 'Pricing Content', 'et_builder' ),
				'selector' => '.et_pb_pricing_content',
			),
			'pricing_item' => array(
				'label'    => esc_html__( 'Pricing Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li',
			),
			'pricing_item_excluded' => array(
				'label'    => esc_html__( 'Excluded Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li.et_pb_not_available',
			),
			'pricing_button' => array(
				'label'    => esc_html__( 'Pricing Button', 'et_builder' ),
				'selector' => '.et_pb_pricing_table_button',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'featured' => array(
				'label'           => esc_html__( 'Make This Table Featured', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'description'     => esc_html__( 'Featuring a table will make it stand out from the rest.', 'et_builder' ),
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a title for the pricing table.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'subtitle' => array(
				'label'           => esc_html__( 'Subtitle', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a sub title for the table if desired.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'currency' => array(
				'label'           => esc_html__( 'Currency', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired currency symbol here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'per' => array(
				'label'           => esc_html__( 'Per', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If your pricing is subscription based, input the subscription payment cycle here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'sum' => array(
				'label'           => esc_html__( 'Price', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the value of the product here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_url' => array(
				'label'           => esc_html__( 'Button URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for the signup button.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Adjust the text used from the signup button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => sprintf(
					'%1$s<br/> + %2$s<br/> - %3$s',
					esc_html__( 'Input a list of features that are/are not included in the product. Separate items on a new line, and begin with either a + or - symbol: ', 'et_builder' ),
					esc_html__( 'Included option', 'et_builder' ),
					esc_html__( 'Excluded option', 'et_builder' )
				),
				'toggle_slug'     => 'main_content',
			),
			'pricing_item_excluded_color' => array(
				'label'             => esc_html__( 'Excluded Item Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'excluded',
				'priority'          => 22,
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_pricing_tables_num, $et_pb_pricing_tables_icon;

		$featured      = $this->shortcode_atts['featured'];
		$title         = $this->shortcode_atts['title'];
		$subtitle      = $this->shortcode_atts['subtitle'];
		$currency      = $this->shortcode_atts['currency'];
		$per           = $this->shortcode_atts['per'];
		$sum           = $this->shortcode_atts['sum'];
		$button_url    = $this->shortcode_atts['button_url'];
		$button_text   = $this->shortcode_atts['button_text'];
		$button_custom = $this->shortcode_atts['custom_button'];
		$custom_icon   = $this->shortcode_atts['button_icon'];
		$pricing_item_excluded_color = $this->shortcode_atts['pricing_item_excluded_color'];

		$et_pb_pricing_tables_num++;

		$module_class = ET_Builder_Element::add_module_order_class( '', $function_name );

		$custom_table_icon = 'on' === $button_custom && '' !== $custom_icon ? $custom_icon : $et_pb_pricing_tables_icon;

		if ( '' !== $pricing_item_excluded_color ) {
			$pricing_item_excluded_color_selector = et_is_builder_plugin_active() ? '%%order_class%% ul.et_pb_pricing li.et_pb_not_available, %%order_class%% ul.et_pb_pricing li.et_pb_not_available span, %%order_class%% ul.et_pb_pricing li.et_pb_not_available a' : '%%order_class%% ul.et_pb_pricing li.et_pb_not_available';
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => $pricing_item_excluded_color_selector,
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $pricing_item_excluded_color )
				),
			) );
		}

		$button_url = trim( $button_url );

		if ( '' !== $button_url && '' !== $button_text ) {
			$button_text = sprintf( '<a class="et_pb_pricing_table_button et_pb_button%4$s" href="%1$s"%3$s>%2$s</a>',
				esc_url( $button_url ),
				esc_html( $button_text ),
				'' !== $custom_table_icon ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_table_icon ) )
				) : '',
				'' !== $custom_table_icon ? ' et_pb_custom_button_icon' : ''
			);
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$output = sprintf(
			'<div class="et_pb_pricing_table%1$s%9$s%10$s%12$s">
				%13$s
				%11$s
				<div class="et_pb_pricing_heading">
					%2$s
					%3$s
				</div> <!-- .et_pb_pricing_heading -->
				<div class="et_pb_pricing_content_top">
					<span class="et_pb_et_price">%6$s%7$s%8$s</span>
				</div> <!-- .et_pb_pricing_content_top -->
				<div class="et_pb_pricing_content">
					<ul class="et_pb_pricing">
						%4$s
					</ul>
				</div> <!-- .et_pb_pricing_content -->
				%5$s
			</div>',
			( 'off' !== $featured ? ' et_pb_featured_table' : '' ),
			( '' !== $title ? sprintf( '<h2 class="et_pb_pricing_title">%1$s</h2>', esc_html( $title ) ) : '' ),
			( '' !== $subtitle ? sprintf( '<span class="et_pb_best_value">%1$s</span>', esc_html( $subtitle ) ) : '' ),
			do_shortcode( et_pb_fix_shortcodes( et_pb_extract_items( $content ) ) ),
			$button_text,
			( '' !== $currency ? sprintf( '<span class="et_pb_dollar_sign">%1$s</span>', esc_html( $currency ) ) : '' ),
			( '' !== $sum ? sprintf( '<span class="et_pb_sum">%1$s</span>', esc_html( $sum ) ) : '' ),
			( '' !== $per ? sprintf( '<span class="et_pb_frequency">/%1$s</span>', esc_html( $per ) ) : '' ),
			esc_attr( $module_class ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Pricing_Tables_Item;

class ET_Builder_Module_CTA extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Call To Action', 'et_builder' );
		$this->slug       = 'et_pb_cta';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'title',
			'button_url',
			'url_new_window',
			'button_text',
			'use_background_color',
			'background_color',
			'background_layout',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited'
		);

		$this->fields_defaults = array(
			'url_new_window'       => array( 'off' ),
			'use_background_color' => array( 'on' ),
			'background_color'     => array( et_builder_accent_color(), 'add_default_setting' ),
			'background_layout'    => array( 'dark' ),
			'text_orientation'     => array( 'center' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_promo';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text'  => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'width' => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2",
						'important' => 'all',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
						'plugin_main' => "{$this->main_css_element} p"
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_color_gradient' => true,
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_promo_button.et_pb_button",
					),
				),
			),
		);
		$this->custom_css_options = array(
			'promo_description' => array(
				'label'    => esc_html__( 'Promo Description', 'et_builder' ),
				'selector' => '.et_pb_promo_description',
			),
			'promo_button' => array(
				'label'    => esc_html__( 'Promo Button', 'et_builder' ),
				'selector' => '.et_pb_promo .et_pb_button.et_pb_promo_button',
				'no_space_before_selector' => true,
			),
			'promo_title' => array(
				'label'    => esc_html__( 'Promo Title', 'et_builder' ),
				'selector' => '.et_pb_promo_description h2',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your value to action title here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_url' => array(
				'label'           => esc_html__( 'Button URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for your CTA button.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'link',
				'description'     => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text, or leave blank for no button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'depends_default'   => true,
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Here you can define a custom background color for your CTA.', 'et_builder' ),
			),
			'use_background_color' => array(
				'label'           => esc_html__( 'Use Background Color', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'background_color',
				),
				'toggle_slug'     => 'background',
				'description'     => esc_html__( 'Here you can choose whether background color setting above should be used or not.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'This will adjust the alignment of the module text.', 'et_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'max_width' => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'max_width_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id            = $this->shortcode_atts['module_id'];
		$module_class         = $this->shortcode_atts['module_class'];
		$title                = $this->shortcode_atts['title'];
		$button_url           = $this->shortcode_atts['button_url'];
		$button_text          = $this->shortcode_atts['button_text'];
		$background_color     = $this->shortcode_atts['background_color'];
		$background_layout    = $this->shortcode_atts['background_layout'];
		$text_orientation     = $this->shortcode_atts['text_orientation'];
		$use_background_color = $this->shortcode_atts['use_background_color'];
		$url_new_window       = $this->shortcode_atts['url_new_window'];
		$max_width            = $this->shortcode_atts['max_width'];
		$max_width_tablet     = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone      = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited = $this->shortcode_atts['max_width_last_edited'];
		$custom_icon          = $this->shortcode_atts['button_icon'];
		$button_custom        = $this->shortcode_atts['custom_button'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );

			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ? $max_width_phone : '',
			);

			$additional_css = 'center' === $text_orientation ? '; margin: 0 auto;' : '';

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name, $additional_css );
		}

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$button_url = trim( $button_url );

		$output = sprintf(
			'<div%6$s class="et_pb_promo%4$s%7$s%8$s%9$s%11$s"%5$s>
				%12$s
				%10$s
				<div class="et_pb_promo_description">
					%1$s
					%2$s
				</div>
				%3$s
			</div>',
			( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ),
			$this->shortcode_content,
			(
				'' !== $button_url && '' !== $button_text
					? sprintf( '<a class="et_pb_promo_button et_pb_button%5$s" href="%1$s"%3$s%4$s>%2$s</a>',
						esc_url( $button_url ),
						esc_html( $button_text ),
						( 'on' === $url_new_window ? ' target="_blank"' : '' ),
						'' !== $custom_icon && 'on' === $button_custom ? sprintf(
							' data-icon="%1$s"',
							esc_attr( et_pb_process_font_icon( $custom_icon ) )
						) : '',
						'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : ''
					)
					: ''
			),
			esc_attr( $class ),
			( 'on' === $use_background_color
				? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
				: ''
			),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'on' !== $use_background_color ? ' et_pb_no_bg' : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_CTA;

class ET_Builder_Module_Button extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Button', 'et_builder' );
		$this->slug       = 'et_pb_button';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'button_url',
			'url_new_window',
			'button_rel',
			'button_text',
			'background_layout',
			'button_alignment',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'url_new_window'    => array( 'off' ),
			'background_color'  => array( et_builder_accent_color(), 'add_default_setting' ),
			'background_layout' => array( 'light' ),
		);

		$this->main_css_element = '%%order_class%%';

		$this->custom_css_options = array(
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'selector' => '.et_pb_button.et_pb_module',
				'no_space_before_selector' => true,
			)
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'alignment'  => esc_html__( 'Alignment', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_options = array(
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'main' => $this->main_css_element,
						'plugin_main' => "{$this->main_css_element}.et_pb_module",
					),
				),
			),
		);
	}

	function get_rel_values() {
		return array(
			'bookmark',
			'external',
			'nofollow',
			'noreferrer',
			'noopener',
		);
	}

	function get_fields() {
		$fields = array(
			'button_url' => array(
				'label'           => esc_html__( 'Button URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for your button.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'link',
				'description'     => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_alignment' => array(
				'label'           => esc_html__( 'Button alignment', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'left'   => esc_html__( 'Left', 'et_builder' ),
					'center' => esc_html__( 'Center', 'et_builder' ),
					'right'  => esc_html__( 'Right', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'Here you can define the alignment of Button', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'button_rel' => array(
				'label'           => esc_html__( 'Button Relationship', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'option_category' => 'configuration',
				'options'         => $this->get_rel_values(),
				'description'     => et_get_safe_localization( __( "Specify the value of your link's <em>rel</em> attribute. The <em>rel</em> attribute specifies the relationship between the current document and the linked document.<br><strong>Tip:</strong> Search engines can use this attribute to get more information about a link.", 'et_builder' ) ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$button_url        = $this->shortcode_atts['button_url'];
		$button_rel        = $this->shortcode_atts['button_rel'];
		$button_text       = $this->shortcode_atts['button_text'];
		$background_layout = $this->shortcode_atts['background_layout'];
		$url_new_window    = $this->shortcode_atts['url_new_window'];
		$custom_icon       = $this->shortcode_atts['button_icon'];
		$button_custom     = $this->shortcode_atts['custom_button'];
		$button_alignment  = $this->shortcode_atts['button_alignment'];

		// Nothing to output if neither Button Text nor Button URL defined
		$button_url = trim( $button_url );

		if ( '' === $button_text && '' === $button_url ) {
			return;
		}

		$rel_attributes = array();

		if ( $button_rel ) {
			$rel_values    = $this->get_rel_values();
			$selected_rels = explode( '|', $button_rel );

			foreach ( $selected_rels as $index => $selected_rel ) {
				if ( ! $selected_rel || 'off' === $selected_rel ) {
					continue;
				}

				$rel_attributes[] = $rel_values[ $index ];
			}
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$module_class .= " et_pb_module et_pb_bg_layout_{$background_layout}";

		$output = sprintf(
			'<div class="et_pb_button_module_wrapper et_pb_module%9$s">
				<a class="et_pb_button%5$s%7$s" href="%1$s"%3$s%4$s%6$s%8$s>%2$s</a>
			</div>',
			esc_url( $button_url ),
			'' !== $button_text ? esc_html( $button_text ) : esc_url( $button_url ),
			( 'on' === $url_new_window ? ' target="_blank"' : '' ),
			'' !== $custom_icon && 'on' === $button_custom ? sprintf(
				' data-icon="%1$s"',
				esc_attr( et_pb_process_font_icon( $custom_icon ) )
			) : '',
			'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : '',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			$rel_attributes ? sprintf( ' rel="%1$s"', esc_attr( implode( ' ', $rel_attributes ) ) ) : '',
			'right' === $button_alignment || 'center' === $button_alignment ? sprintf( ' et_pb_button_alignment_%1$s', esc_attr( $button_alignment ) )  : ''
		);

		return $output;
	}
}
new ET_Builder_Module_Button;

class ET_Builder_Module_Audio extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Audio', 'et_builder' );
		$this->slug       = 'et_pb_audio';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'audio',
			'title',
			'artist_name',
			'album_name',
			'image_url',
			'background_color',
			'background_layout',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'background_color'  => array( et_builder_accent_color(), 'add_default_setting' ),
			'background_layout' => array( 'dark' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_audio_module';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'audio'        => esc_html__( 'Audio', 'et_builder' ),
					'image'        => esc_html__( 'Image', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2",
						'important' => 'plugin_only',
					),
				),
				'caption'   => array(
					'label'    => esc_html__( 'Caption', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
						'main' => "{$this->main_css_element} p",
						'plugin_main' => "{$this->main_css_element} p, {$this->main_css_element} p strong",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);
		$this->custom_css_options = array(
			'audio_cover_art' => array(
				'label'    => esc_html__( 'Audio Cover Art', 'et_builder' ),
				'selector' => '.et_pb_audio_cover_art',
			),
			'audio_content' => array(
				'label'    => esc_html__( 'Audio Content', 'et_builder' ),
				'selector' => '.et_pb_audio_module_content',
			),
			'audio_title' => array(
				'label'    => esc_html__( 'Audio Title', 'et_builder' ),
				'selector' => '.et_pb_audio_module_content h2',
			),
			'audio_meta' => array(
				'label'    => esc_html__( 'Audio Meta', 'et_builder' ),
				'selector' => '.et_audio_module_meta',
			),
			'audio_buttons' => array(
				'label'    => esc_html__( 'Player Buttons', 'et_builder' ),
				'selector' => "{$this->main_css_element} .mejs-button.mejs-playpause-button button:before,{$this->main_css_element} .mejs-button.mejs-volume-button.mejs-mute button:before",
			),
			'audio_timer' => array(
				'label'    => esc_html__( 'Player Timer', 'et_builder' ),
				'selector' => '.mejs-time.mejs-currenttime-container.custom',
			),
			'audio_sliders' => array(
				'label'    => esc_html__( 'Player Sliders', 'et_builder' ),
				'selector' => "{$this->main_css_element} .et_audio_container .mejs-controls .mejs-time-rail .mejs-time-total,{$this->main_css_element} .et_audio_container .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-total",
			),
			'audio_sliders_current' => array(
				'label'    => esc_html__( 'Player Sliders Current', 'et_builder' ),
				'selector' => "{$this->main_css_element} .et_audio_container .mejs-controls .mejs-time-rail .mejs-time-current,{$this->main_css_element} .et_audio_container .mejs-controls .mejs-time-rail .mejs-time-handle,{$this->main_css_element} .et_audio_container .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current,{$this->main_css_element} .et_audio_container .mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-handle",
			),
		);
	}

	function get_fields() {
		$fields = array(
			'audio' => array(
				'label'              => esc_html__( 'Audio', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'audio',
				'upload_button_text' => esc_attr__( 'Upload an audio file', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Audio file', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Audio for the module', 'et_builder' ),
				'description'        => esc_html__( 'Define the audio file for use in the module. To remove an audio file from the module, simply delete the URL from the settings field.', 'et_builder' ),
				'toggle_slug'        => 'audio',
				'computed_affects'   => array(
					'__audio',
				),
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a title.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'artist_name' => array(
				'label'           => esc_html__( 'Artist Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define an artist name.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'album_name' => array(
				'label'           => esc_html__( 'Album name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define an album name.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'image_url' => array(
				'label'              => esc_html__( 'Cover Art Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'computed_affects'   => array(
					'__audio',
				),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Define a custom background color for your module, or leave blank to use the default color.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'color_option',
				'options'           => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__audio' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Audio', 'get_audio' ),
				'computed_depends_on' => array(
					'audio',
				),
				'computed_minimum' => array(
					'audio',
				),
			),
		);
		return $fields;
	}

	static function get_audio( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'audio' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class');

		return do_shortcode( sprintf( '[audio src="%s" /]', $args['audio'] ) );
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$audio             = $this->shortcode_atts['audio'];
		$title             = $this->shortcode_atts['title'];
		$artist_name       = $this->shortcode_atts['artist_name'];
		$album_name        = $this->shortcode_atts['album_name'];
		$image_url         = $this->shortcode_atts['image_url'];
		$background_color  = "" !== $this->shortcode_atts['background_color'] ? $this->shortcode_atts['background_color'] : $this->fields_defaults['background_color'][0];
		$background_layout = $this->shortcode_atts['background_layout'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$meta = $cover_art = '';
		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		if ( 'light' === $background_layout ) {
			$class .= " et_pb_text_color_dark";
		}

		if ( '' !== $artist_name || '' !== $album_name ) {
			if ( '' !== $artist_name && '' !== $album_name ) {
				$album_name = ' | ' . $album_name;
			}

			if ( '' !== $artist_name ) {
				$artist_name = sprintf(
					et_get_safe_localization( _x( 'by <strong>%1$s</strong>', 'Audio Module meta information', 'et_builder' ) ),
					esc_html( $artist_name )
				);
			}

			$meta = sprintf( '%1$s%2$s',
				$artist_name,
				esc_html( $album_name )
			);

			$meta = sprintf( '<p class="et_audio_module_meta">%1$s</p>', $meta );
		}

		if ( '' !== $image_url ) {
			$cover_art = sprintf(
				'<div class="et_pb_audio_cover_art" style="background-image: url(%1$s);">
				</div>',
				esc_attr( $image_url )
			);
		}
		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// some themes do not include these styles/scripts so we need to enqueue them in this module
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'et-builder-mediaelement' );

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class');

		$video_background = $this->video_background();

		$output = sprintf(
			'<div%8$s class="et_pb_audio_module clearfix%4$s%7$s%9$s%10$s%12$s"%5$s>
				%13$s
				%11$s
				%6$s

				<div class="et_pb_audio_module_content et_audio_container">
					%1$s
					%2$s
					%3$s
				</div>
			</div>',
			( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ),
			$meta,
			self::get_audio( array(
				'audio' => $audio,
			) ),
			esc_attr( $class ),
			sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) ),
			$cover_art,
			( '' === $image_url ? ' et_pb_audio_no_image' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Audio;

class ET_Builder_Module_Signup extends ET_Builder_Module {

	public static $enabled_providers = array(
		'aweber'     => 'Aweber',
		'mailchimp'  => 'MailChimp',
	);

	function init() {
		$this->name       = esc_html__( 'Email Optin', 'et_builder' );
		$this->slug       = 'et_pb_signup';
		$this->fb_support = true;

		self::$enabled_providers = apply_filters( 'et_builder_module_signup_enabled_providers', self::$enabled_providers );

		$this->whitelisted_fields = array(
			'provider',
			'feedburner_uri',
			'mailchimp_list',
			'aweber_list',
			'title',
			'button_text',
			'use_background_color',
			'background_color',
			'background_layout',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'form_field_background_color',
			'form_field_text_color',
			'focus_background_color',
			'focus_text_color',
			'use_focus_border_color',
			'focus_border_color',
		);

		$this->fields_defaults = array(
			'provider'               => array( 'mailchimp' ),
			'button_text'            => array( esc_html__( 'Subscribe', 'et_builder' ) ),
			'use_background_color'   => array( 'on' ),
			'background_color'       => array( et_builder_accent_color(), 'add_default_setting' ),
			'background_layout'      => array( 'dark' ),
			'text_orientation'       => array( 'left' ),
			'use_focus_border_color' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_subscribe';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'provider'     => esc_html__( 'Email Account', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'fields' => esc_html__( 'Fields', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_newsletter_description h2",
						'important' => 'all',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_newsletter_description, {$this->main_css_element} .et_pb_newsletter_form",
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_newsletter_button.et_pb_button",
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
			),
		);
		$this->custom_css_options = array(
			'newsletter_description' => array(
				'label'    => esc_html__( 'Newsletter Description', 'et_builder' ),
				'selector' => '.et_pb_newsletter_description',
			),
			'newsletter_form' => array(
				'label'    => esc_html__( 'Newsletter Form', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form',
			),
			'newsletter_fields' => array(
				'label'    => esc_html__( 'Newsletter Fields', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form input',
			),
			'newsletter_button' => array(
				'label'    => esc_html__( 'Newsletter Button', 'et_builder' ),
				'selector' => '.et_pb_subscribe .et_pb_newsletter_button.et_pb_button',
				'no_space_before_selector' => true,
			),
		);
	}

	function get_fields( $force = '' ) {
		$lists  = self::get_lists( $force );
		$manage = esc_html__( 'Manage', 'et_builder' );

		$action_buttons = array(
			array(
				'type'  => 'button',
				'class' => 'et_pb_email_add_account',
				'text'  => esc_html__( 'Add', 'et_builder' ),
			),
			array(
				'type'       => 'button',
				'class'      => 'et_pb_email_remove_account',
				'text'       => esc_html__( 'Remove', 'et_builder' ),
				'attributes' => array(
					'data-confirm_text' => esc_attr__( 'Confirm', 'et_builder' ),
				),
			),
			array(
				'type'       => 'button',
				'class'      => 'et_pb_email_force_fetch_lists',
				'text'       => esc_html__( 'Fetch Lists', 'et_builder' ),
				'attributes' => array(
					'data-cancel_text' => esc_attr__( 'Cancel', 'et_builder' ),
				),
			),
		);

		$submit_and_cancel_buttons = array(
			array(
				'type'  => 'button',
				'class' => 'et_pb_email_cancel',
				'text'  => esc_html__( 'Cancel', 'et_builder' ),
			),
			array(
				'type'  => 'button',
				'class' => 'et_pb_email_submit',
				'text'  => esc_html__( 'Submit', 'et_builder' ),
			),
		);

		$list_fields_attributes = array(
			'data-confirm_remove_text'     => esc_attr__( 'The following account will be removed:', 'et_builder' ),
			'data-adding_new_account_text' => esc_attr__( 'Use the fields below to add a new account.', 'et_builder' ),
		);

		foreach ( $lists as &$accounts_list ) {
			array_unshift( $accounts_list, array( 'none' => esc_html__( 'Select a list', 'et_builder' ) ) );
			$accounts_list[ $manage ] = array(
				'add_new_account' => '',
				'remove_account'  => '',
				'fetch_lists'     => esc_html__( 'Fetching lists...', 'et_builder' ),
			);
		}

		$fields = array(
			'provider' => array(
				'label'           => esc_html__( 'Service Provider', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'mailchimp'  => esc_html( 'MailChimp' ),
					'feedburner' => esc_html( 'FeedBurner' ),
					'aweber'     => esc_html( 'Aweber' ),
				),
				'affects' => array(
					'feedburner_uri',
					'mailchimp_list',
					'aweber_list',
				),
				'description'      => esc_html__( 'Choose a service provider.', 'et_builder' ),
				'toggle_slug'      => 'provider',
			),
			'feedburner_uri' => array(
				'label'           => esc_html__( 'Feed Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'feedburner',
				'description'     => et_get_safe_localization( sprintf( __( 'Enter <a href="%1$s" target="_blank">Feed Title</a>.', 'et_builder' ), esc_url( 'http://feedburner.google.com/fb/a/myfeeds' ) ) ),
				'toggle_slug'      => 'provider',
			),
			'mailchimp_list' => array(
				'label'           => esc_html__( 'MailChimp List', 'et_builder' ),
				'type'            => 'select_with_option_groups',
				'option_category' => 'basic_option',
				'options'         => $lists['mailchimp'],
				'description'     => esc_html__( 'Choose a MailChimp list. If you don\'t see any lists, click "Add" to add a MailChimp account.' ),
				'depends_show_if' => 'mailchimp',
				'after'           => $action_buttons,
				'attributes'      => $list_fields_attributes,
				'default'         => '0|none',
				'affects' => array(
					'mailchimp_name',
					'mailchimp_key',
				),
				'toggle_slug'      => 'provider',
			),
			'mailchimp_name'  => array(
				'label'           => esc_html__( 'Account Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'A name to associate with the account when displayed in the Mailchimp Lists select field.', 'et_builder' ),
				'depends_show_if' => $manage . '|add_new_account',
				'class'           => 'et_pb_account_name',
				'toggle_slug'     => 'provider',
			),
			'mailchimp_key'  => array(
				'label'           => esc_html__( 'API Key', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => et_get_safe_localization( sprintf( __( 'Enter your MailChimp API key. You can create an api key %1$s here %2$s', 'et_builder' ), '<a target="_blank" href="https://us3.admin.mailchimp.com/account/api">', '</a>.' ) ),
				'depends_show_if' => $manage . '|add_new_account',
				'class'           => 'et_pb_api_key',
				'after'           => $submit_and_cancel_buttons,
				'toggle_slug'     => 'provider',
			),
			'aweber_list' => array(
				'label'           => esc_html__( 'Aweber List', 'et_builder' ),
				'type'            => 'select_with_option_groups',
				'option_category' => 'basic_option',
				'options'         => $lists['aweber'],
				'description'     => esc_html__( 'Choose an AWeber list. If you don\'t see any lists, click "Add" to add an AWeber account.' ),
				'depends_show_if' => 'aweber',
				'after'           => $action_buttons,
				'attributes'      => $list_fields_attributes,
				'default'         => '0|none',
				'affects' => array(
					'aweber_name',
					'aweber_key',
				),
				'toggle_slug'      => 'provider',
			),
			'aweber_name'  => array(
				'label'           => esc_html__( 'Account Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'A name to associate with the account when displayed in the Aweber Lists select field.', 'et_builder' ),
				'depends_show_if' => $manage . '|add_new_account',
				'class'           => 'et_pb_account_name',
				'toggle_slug'     => 'provider',
			),
			'aweber_key'  => array(
				'label'           => esc_html__( 'API Key', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => et_get_safe_localization( sprintf( __( 'Paste the authorization code from the Aweber page that was just opened in a new tab here. You can generate a new authorization code %1$s here %2$s', 'et_builder' ), '<a target="_blank" href="https://auth.aweber.com/1.0/oauth/authorize_app/b17f3351">', '</a>.' ) ),
				'depends_show_if' => $manage . '|add_new_account',
				'class'           => 'et_pb_api_key',
				'after'           => $submit_and_cancel_buttons,
				'toggle_slug'     => 'provider',
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose a title of your signup box.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_text' => array(
				'label'             => esc_html__( 'Button Text', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Here you can change the text used for the signup button.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Define a custom background color for your module, or leave blank to use the default color.', 'et_builder' ),
				'depends_default'   => true,
				'toggle_slug'       => 'background',
			),
			'use_background_color' => array(
				'label'             => esc_html__( 'Use Background Color', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'background_color',
				),
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can adjust the alignment of your text.', 'et_builder' ),
			),
			'content_new' => array(
				'label'             => esc_html__( 'Content', 'et_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
			),
			'form_field_background_color' => array(
				'label'             => esc_html__( 'Form Field Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'form_field_text_color' => array(
				'label'             => esc_html__( 'Form Field Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'focus_background_color' => array(
				'label'             => esc_html__( 'Focus Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'focus_text_color' => array(
				'label'             => esc_html__( 'Focus Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'use_focus_border_color' => array(
				'label'           => esc_html__( 'Use Focus Border Color', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'     => array(
					'focus_border_color',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'fields',
			),
			'focus_border_color' => array(
				'label'             => esc_html__( 'Focus Border Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_default'   => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	public static function get_lists( $force = '' ) {
		static $lists = null;

		if ( null !== $lists && empty( $force ) && ! empty( $lists ) ) {
			return $lists;
		}

		et_builder_email_maybe_migrate_accounts();

		$providers    = et_core_api_email_providers();
		$all_accounts = $providers->accounts();
		$lists        = array();
		$provider     = null;

		foreach( self::$enabled_providers as $provider_slug => $provider_name ) {
			if ( empty( $all_accounts[ $provider_slug ] ) ) {
				$lists[ $provider_slug ] = array();
				continue;
			}

			foreach( $all_accounts[ $provider_slug ] as $account_name => $account_details ) {
				if ( 'fetch' !== $force && empty( $account_details['lists'] ) ) {
					continue;
				}

				$account_lists = empty( $account_details['lists'] ) ? array() : $account_details['lists'];

				if ( 'fetch' === $force ) {
					$provider      = $providers->get( $provider_name, $account_name, 'builder' );
					$result        = $provider->fetch_subscriber_lists();
					$account_lists = 'success' === $result ? $provider->data['lists'] : $account_lists;
				}

				foreach( $account_lists as $list_id => $list_details ) {
					if ( ! empty( $list_details['name'] ) ) {
						$lists[ $provider_slug ][ $account_name ][ $list_id ] = esc_html( $list_details['name'] );
					}
				}
			}

			if ( empty( $lists[ $provider_slug ] ) ) {
				$lists[ $provider_slug ] = array();
			}
		}

		return $lists;
	}

	public static function get_account_name_for_list_id( $provider_slug, $list_id ) {
		$providers    = new ET_Core_API_Email_Providers();
		$all_accounts = $providers->accounts();
		$result       = '';

		if ( ! isset( $all_accounts[ $provider_slug ] ) ) {
			return $result;
		}

		foreach ( $all_accounts[ $provider_slug ] as $account_name => $account_details ) {
			if ( ! empty( $account_details['lists'][ $list_id ] ) ) {
				$result = $account_name;
				break;
			}
		}

		return $result;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                   = $this->shortcode_atts['module_id'];
		$module_class                = $this->shortcode_atts['module_class'];
		$title                       = $this->shortcode_atts['title'];
		$button_text                 = $this->shortcode_atts['button_text'];
		$background_color            = $this->shortcode_atts['background_color'];
		$mailchimp_list              = $this->shortcode_atts['mailchimp_list'];
		$aweber_list                 = $this->shortcode_atts['aweber_list'];
		$text_orientation            = $this->shortcode_atts['text_orientation'];
		$use_background_color        = $this->shortcode_atts['use_background_color'];
		$provider                    = $this->shortcode_atts['provider'];
		$feedburner_uri              = $this->shortcode_atts['feedburner_uri'];
		$background_layout           = $this->shortcode_atts['background_layout'];
		$form_field_background_color = $this->shortcode_atts['form_field_background_color'];
		$form_field_text_color       = $this->shortcode_atts['form_field_text_color'];
		$focus_background_color      = $this->shortcode_atts['focus_background_color'];
		$focus_text_color            = $this->shortcode_atts['focus_text_color'];
		$use_focus_border_color      = $this->shortcode_atts['use_focus_border_color'];
		$focus_border_color          = $this->shortcode_atts['focus_border_color'];
		$button_custom               = $this->shortcode_atts['custom_button'];
		$custom_icon                 = $this->shortcode_atts['button_icon'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $focus_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input.input:focus',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $focus_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $focus_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input.input:focus',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $focus_text_color )
				),
			) );
		}

		if ( 'off' !== $use_focus_border_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input.input:focus',
				'declaration' => sprintf(
					'border: 1px solid %1$s !important;',
					esc_html( $focus_border_color )
				),
			) );
		}

		if ( '' !== $form_field_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input[type="text"], %%order_class%% textarea',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $form_field_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $form_field_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input[type="text"], %%order_class%% textarea',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $form_field_text_color )
				),
			) );
		}

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

		$form = '';

		$firstname     = esc_html__( 'First Name', 'et_builder' );
		$lastname      = esc_html__( 'Last Name', 'et_builder' );
		$email_address = esc_html__( 'Email Address', 'et_builder' );

		switch ( $provider ) {
			case 'mailchimp' :
				if ( ! in_array( $mailchimp_list, array( '', 'none' ) ) ) {
					if ( false !== strpos( $mailchimp_list, '|' ) ) {
						list( $account_name, $mailchimp_list ) = explode( '|', $mailchimp_list );
					} else {
						$account_name = self::get_account_name_for_list_id( 'mailchimp', $mailchimp_list );
					}

					$form = sprintf( '
						<div class="et_pb_newsletter_form">
							<div class="et_pb_newsletter_result"></div>
							<p>
								<label class="et_pb_contact_form_label" for="et_pb_signup_firstname" style="display: none;">%3$s</label>
								<input id="et_pb_signup_firstname" class="input" type="text" value="%4$s" name="et_pb_signup_firstname">
							</p>
							<p>
								<label class="et_pb_contact_form_label" for="et_pb_signup_lastname" style="display: none;">%5$s</label>
								<input id="et_pb_signup_lastname" class="input" type="text" value="%6$s" name="et_pb_signup_lastname">
							</p>
							<p>
								<label class="et_pb_contact_form_label" for="et_pb_signup_email" style="display: none;">%7$s</label>
								<input id="et_pb_signup_email" class="input" type="text" value="%8$s" name="et_pb_signup_email">
							</p>
							<p><a class="et_pb_newsletter_button et_pb_button%10$s" href="#"%9$s><span class="et_subscribe_loader"></span><span class="et_pb_newsletter_button_text">%1$s</span></a></p>
							<input type="hidden" value="%2$s" name="et_pb_signup_list_id" />
							<input type="hidden" value="%11$s" name="et_pb_signup_account_name" />
						</div>',
						esc_html( $button_text ),
						( ! in_array( $mailchimp_list, array( '', 'none' ) ) ? esc_attr( $mailchimp_list ) : '' ),
						esc_html( $firstname ),
						esc_attr( $firstname ),
						esc_html( $lastname ), // #5
						esc_attr( $lastname ),
						esc_html( $email_address ),
						esc_attr( $email_address ),
						'' !== $custom_icon && 'on' === $button_custom ? sprintf(
							' data-icon="%1$s"',
							esc_attr( et_pb_process_font_icon( $custom_icon ) )
						) : '',
						'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : '', // #10
						esc_attr( $account_name )
					);
				}

				break;
			case 'feedburner':
				$form = sprintf( '
					<div class="et_pb_newsletter_form et_pb_feedburner_form">
						<form action="https://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open(\'https://feedburner.google.com/fb/a/mailverify?uri=%4$s\', \'popupwindow\', \'scrollbars=yes,width=550,height=520\'); return true">
						<p>
							<label class="et_pb_contact_form_label" for="email" style="display: none;">%2$s</label>
							<input id="email" class="input" type="text" value="%3$s" name="email">
						</p>
						<p><button class="et_pb_newsletter_button et_pb_button%7$s" type="submit"%6$s>%1$s</button></p>
						<input type="hidden" value="%4$s" name="uri" />
						<input type="hidden" name="loc" value="%5$s" />
						</form>
					</div>',
					esc_html( $button_text ),
					esc_html( $email_address ),
					esc_attr( $email_address ),
					esc_attr( $feedburner_uri ),
					esc_attr( get_locale() ),
					'' !== $custom_icon && 'on' === $button_custom ? sprintf(
							' data-icon="%1$s"',
							esc_attr( et_pb_process_font_icon( $custom_icon ) )
					) : '',
					'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : ''
				);

				break;
			case 'aweber' :
				$firstname = esc_html__( 'Name', 'et_builder' );

				if ( ! in_array( $aweber_list, array( '', 'none' ) ) ) {
					if ( false !== strpos( $aweber_list, '|' ) ) {
						list( $account_name, $aweber_list ) = explode( '|', $aweber_list );
					} else {
						$account_name = self::get_account_name_for_list_id( 'aweber', $aweber_list );
					}

					$form = sprintf( '
						<div class="et_pb_newsletter_form" data-service="aweber">
							<div class="et_pb_newsletter_result"></div>
							<p>
								<label class="et_pb_contact_form_label" for="et_pb_signup_firstname" style="display: none;">%3$s</label>
								<input id="et_pb_signup_firstname" class="input" type="text" value="%4$s" name="et_pb_signup_firstname">
							</p>
							<p>
								<label class="et_pb_contact_form_label" for="et_pb_signup_email" style="display: none;">%5$s</label>
								<input id="et_pb_signup_email" class="input" type="text" value="%6$s" name="et_pb_signup_email">
							</p>
							<p><a class="et_pb_newsletter_button et_pb_button%8$s" href="#"%7$s><span class="et_subscribe_loader"></span><span class="et_pb_newsletter_button_text">%1$s</span></a></p>
							<input type="hidden" value="%2$s" name="et_pb_signup_list_id" />
							<input type="hidden" value="%9$s" name="et_pb_signup_account_name" />
						</div>',
						esc_html( $button_text ),
						( ! in_array( $aweber_list, array( '', 'none' ) ) ? esc_attr( $aweber_list ) : '' ),
						esc_html( $firstname ),
						esc_attr( $firstname ),
						esc_html( $email_address ), // #5
						esc_attr( $email_address ),
						'' !== $custom_icon && 'on' === $button_custom ? sprintf(
							' data-icon="%1$s"',
							esc_attr( et_pb_process_font_icon( $custom_icon ) )
						) : '',
						'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : '',
						esc_attr( $account_name ) // #9
					);
				}

				break;
		}

		$output = sprintf(
			'<div%6$s class="et_pb_newsletter et_pb_subscribe clearfix%4$s%7$s%8$s%9$s%11$s"%5$s>
				%12$s
				%10$s
				<div class="et_pb_newsletter_description">
					%1$s
					%2$s
				</div>
				%3$s
			</div>',
			( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ),
			$this->shortcode_content,
			$form,
			esc_attr( $class ),
			( 'on' === $use_background_color
				? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
				: ''
			),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'on' !== $use_background_color ? ' et_pb_no_bg' : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Signup;

class ET_Builder_Module_Login extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Login', 'et_builder' );
		$this->slug       = 'et_pb_login';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'title',
			'current_page_redirect',
			'use_background_color',
			'background_color',
			'background_layout',
			'text_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'form_field_background_color',
			'form_field_text_color',
			'focus_background_color',
			'focus_text_color',
			'use_focus_border_color',
			'focus_border_color',
		);

		$this->fields_defaults = array(
			'current_page_redirect'  => array( 'off' ),
			'use_background_color'   => array( 'on' ),
			'background_color'       => array( et_builder_accent_color(), 'add_default_setting' ),
			'background_layout'      => array( 'dark' ),
			'text_orientation'       => array( 'left' ),
			'use_focus_border_color' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_login';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'redirect'     => esc_html__( 'Redirect', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'fields' => esc_html__( 'Fields', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2",
						'important' => 'all',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
						'font' => "{$this->main_css_element}, {$this->main_css_element} .et_pb_newsletter_description_content, {$this->main_css_element} p, {$this->main_css_element} span",
					),
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_newsletter_button.et_pb_button",
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
			),
		);
		$this->custom_css_options = array(
			'newsletter_description' => array(
				'label'    => esc_html__( 'Login Description', 'et_builder' ),
				'selector' => '.et_pb_newsletter_description',
			),
			'newsletter_form' => array(
				'label'    => esc_html__( 'Login Form', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form',
			),
			'newsletter_fields' => array(
				'label'    => esc_html__( 'Login Fields', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form input',
			),
			'newsletter_button' => array(
				'label'    => esc_html__( 'Login Button', 'et_builder' ),
				'selector' => '.et_pb_login .et_pb_login_form .et_pb_newsletter_button.et_pb_button',
				'no_space_before_selector' => true,
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose a title of your login box.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'current_page_redirect' => array(
				'label'           => esc_html__( 'Redirect To The Current Page', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'toggle_slug'     => 'redirect',
				'description'     => esc_html__( 'Here you can choose whether the user should be redirected to the current page.', 'et_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Define a custom background color for your module, or leave blank to use the default color.', 'et_builder' ),
				'depends_default'   => true,
				'toggle_slug'       => 'background',
			),
			'use_background_color' => array(
				'label'           => esc_html__( 'Use Background Color', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'on'          => esc_html__( 'Yes', 'et_builder' ),
					'off'         => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					'background_color',
				),
				'toggle_slug'     => 'background',
				'description'     => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'      	  => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can adjust the alignment of your text.', 'et_builder' ),
			),
			'content_new' => array(
				'label'             => esc_html__( 'Content', 'et_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
			),
			'form_field_background_color' => array(
				'label'             => esc_html__( 'Form Field Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'form_field_text_color' => array(
				'label'             => esc_html__( 'Form Field Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'focus_background_color' => array(
				'label'             => esc_html__( 'Focus Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'focus_text_color' => array(
				'label'             => esc_html__( 'Focus Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'use_focus_border_color' => array(
				'label'           => esc_html__( 'Use Focus Border Color', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'     => array(
					'focus_border_color',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'fields',
			),
			'focus_border_color' => array(
				'label'             => esc_html__( 'Focus Border Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_default'   => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                   = $this->shortcode_atts['module_id'];
		$module_class                = $this->shortcode_atts['module_class'];
		$title                       = $this->shortcode_atts['title'];
		$background_color            = $this->shortcode_atts['background_color'];
		$background_layout           = $this->shortcode_atts['background_layout'];
		$text_orientation            = $this->shortcode_atts['text_orientation'];
		$use_background_color        = $this->shortcode_atts['use_background_color'];
		$current_page_redirect       = $this->shortcode_atts['current_page_redirect'];
		$form_field_background_color = $this->shortcode_atts['form_field_background_color'];
		$form_field_text_color       = $this->shortcode_atts['form_field_text_color'];
		$focus_background_color      = $this->shortcode_atts['focus_background_color'];
		$focus_text_color            = $this->shortcode_atts['focus_text_color'];
		$use_focus_border_color      = $this->shortcode_atts['use_focus_border_color'];
		$focus_border_color          = $this->shortcode_atts['focus_border_color'];
		$button_custom               = $this->shortcode_atts['custom_button'];
		$custom_icon                 = $this->shortcode_atts['button_icon'];
		$content                     = $this->shortcode_content;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $focus_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input:focus',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $focus_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $focus_text_color ) {
			$placeholder_selectors = array(
				'%%order_class%% .et_pb_newsletter_form p input:focus::-webkit-input-placeholder',
				'%%order_class%% .et_pb_newsletter_form p input:focus::-moz-placeholder',
				'%%order_class%% .et_pb_newsletter_form p input:focus:-ms-input-placeholder',
			);

			foreach ( $placeholder_selectors as $single_selector ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $single_selector,
					'declaration' => sprintf(
						'color: %1$s;',
						esc_html( $focus_text_color )
					),
				) );
			}

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input:focus',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $focus_text_color )
				),
			) );
		}

		if ( 'off' !== $use_focus_border_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input:focus',
				'declaration' => sprintf(
					'border: 1px solid %1$s !important;',
					esc_html( $focus_border_color )
				),
			) );
		}

		if ( '' !== $form_field_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input[type="text"], %%order_class%% textarea, %%order_class%% .input',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $form_field_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $form_field_text_color ) {
			$placeholder_selectors = array(
				'%%order_class%% .input::-webkit-input-placeholder',
				'%%order_class%% .input::-moz-placeholder',
				'%%order_class%% .input:-ms-input-placeholder',
			);

			foreach ( $placeholder_selectors as $single_selector ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $single_selector,
					'declaration' => sprintf(
						'color: %1$s;',
						esc_html( $form_field_text_color )
					),
				) );
			}

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input[type="text"], %%order_class%% textarea, %%order_class%% .input',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $form_field_text_color )
				),
			) );
		}

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$redirect_url = 'on' === $current_page_redirect
			? ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
			: '';

		if ( is_user_logged_in() && ! is_customize_preview() && ! is_et_pb_preview() ) {
			$current_user = wp_get_current_user();

			$content .= sprintf( '<br/>%1$s <a href="%2$s">%3$s</a>',
				sprintf( esc_html__( 'Logged in as %1$s', 'et_builder' ), esc_html( $current_user->display_name ) ),
				esc_url( wp_logout_url( $redirect_url ) ),
				esc_html__( 'Log out', 'et_builder' )
			);
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

		$form = '';

		if ( ! is_user_logged_in() || is_customize_preview() || is_et_pb_preview() ) {
			$username = esc_html__( 'Username', 'et_builder' );
			$password = esc_html__( 'Password', 'et_builder' );

			$form = sprintf( '
				<div class="et_pb_newsletter_form et_pb_login_form">
					<form action="%7$s" method="post">
						<p>
							<label class="et_pb_contact_form_label" for="user_login" style="display: none;">%3$s</label>
							<input id="user_login" placeholder="%4$s" class="input" type="text" value="" name="log" />
						</p>
						<p>
							<label class="et_pb_contact_form_label" for="user_pass" style="display: none;">%5$s</label>
							<input id="user_pass" placeholder="%6$s" class="input" type="password" value="" name="pwd" />
						</p>
						<p class="et_pb_forgot_password"><a href="%2$s">%1$s</a></p>
						<p>
							<button type="submit" class="et_pb_newsletter_button et_pb_button%11$s"%10$s>%8$s</button>
							%9$s
						</p>
					</form>
				</div>',
				esc_html__( 'Forgot your password?', 'et_builder' ),
				esc_url( wp_lostpassword_url() ),
				esc_html( $username ),
				esc_attr( $username ),
				esc_html( $password ),
				esc_attr( $password ),
				esc_url( site_url( 'wp-login.php', 'login_post' ) ),
				esc_html__( 'Login', 'et_builder' ),
				( 'on' === $current_page_redirect
					? sprintf( '<input type="hidden" name="redirect_to" value="%1$s" />',  $redirect_url )
					: ''
				),
				'' !== $custom_icon && 'on' === $button_custom ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon ) )
				) : '',
				'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : ''
			);
		}

		$output = sprintf(
			'<div%6$s class="et_pb_newsletter et_pb_login clearfix%4$s%7$s%8$s%9$s%11$s"%5$s>
				%12$s
				%10$s
				<div class="et_pb_newsletter_description">
					%1$s
					%2$s
				</div>
				%3$s
			</div>',
			( '' !== $title ? '<h2>' . esc_html( $title ) . '</h2>' : '' ),
			( '' !== $content ? '<div class="et_pb_newsletter_description_content">' . $content . '</div>' : '' ),
			$form,
			esc_attr( $class ),
			( 'on' === $use_background_color
				? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
				: ''
			),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			is_customize_preview() || is_et_pb_preview() ? ' et_pb_in_customizer' : '',
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Login;

class ET_Builder_Module_Portfolio extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Portfolio', 'et_builder' );
		$this->slug       = 'et_pb_portfolio';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'fullwidth',
			'posts_number',
			'include_categories',
			'show_title',
			'show_categories',
			'show_pagination',
			'background_layout',
			'admin_label',
			'module_id',
			'module_class',
			'zoom_icon_color',
			'hover_overlay_color',
			'hover_icon',
		);

		$this->fields_defaults = array(
			'fullwidth'         => array( 'on' ),
			'posts_number'      => array( 10, 'add_default_setting' ),
			'show_title'        => array( 'on' ),
			'show_categories'   => array( 'on' ),
			'show_pagination'   => array( 'on' ),
			'background_layout' => array( 'light' ),
		);

		$this->main_css_element = '%%order_class%% .et_pb_portfolio_item';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'  => esc_html__( 'Layout', 'et_builder' ),
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'text'    => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title'   => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2, {$this->main_css_element} h2 a",
						'important' => 'all',
					),
				),
				'caption' => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
		);
		$this->custom_css_options = array(
			'portfolio_image' => array(
				'label'    => esc_html__( 'Portfolio Image', 'et_builder' ),
				'selector' => '.et_portfolio_image',
			),
			'overlay' => array(
				'label'    => esc_html__( 'Overlay', 'et_builder' ),
				'selector' => '.et_overlay',
			),
			'overlay_icon' => array(
				'label'    => esc_html__( 'Overlay Icon', 'et_builder' ),
				'selector' => '.et_overlay:before',
			),
			'portfolio_title' => array(
				'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item h2',
			),
			'portfolio_post_meta' => array(
				'label'    => esc_html__( 'Portfolio Post Meta', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item .post-meta',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'fullwidth' => array(
				'label'           => esc_html__( 'Layout', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Fullwidth', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'affects' => array(
					'hover_icon',
					'zoom_icon_color',
					'hover_overlay_color',
				),
				'description'       => esc_html__( 'Choose your desired portfolio layout style.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'posts_number' => array(
				'label'             => esc_html__( 'Posts Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Define the number of projects that should be displayed per page.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'toggle_slug'       => 'main_content',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'renderer'         => 'et_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select the categories that you would like to include in the feed.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__projects',
				),
				'taxonomy_name' => 'project_category',
			),
			'show_title' => array(
				'label'           => esc_html__( 'Show Title', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turn project titles on or off.', 'et_builder' ),
			),
			'show_categories' => array(
				'label'           => esc_html__( 'Show Categories', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turn the category links on or off.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'           => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light'  => esc_html__( 'Dark', 'et_builder' ),
					'dark' => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'zoom_icon_color' => array(
				'label'             => esc_html__( 'Zoom Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'depends_show_if'     => 'off',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__projects'          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Portfolio', 'get_portfolio_item' ),
				'computed_depends_on' => array(
					'posts_number',
					'include_categories',
					'fullwidth',
				),
			),
		);
		return $fields;
	}

	/**
	 * Get portfolio objects for portfolio module
	 *
	 * @param array  arguments that affect et_pb_portfolio query
	 * @param array  passed conditional tag for update process
	 * @param array  passed current page params
	 * @return array portfolio item data
	 */
	static function get_portfolio_item( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $et_fb_processing_shortcode_object;

		$global_processing_original_value = $et_fb_processing_shortcode_object;

		$defaults = array(
			'posts_number'       => 10,
			'include_categories' => 0,
			'fullwidth'          => 'on',
		);

		$args          = wp_parse_args( $args, $defaults );

		// Native conditional tag only works on page load. Data update needs $conditional_tags data
		$is_front_page = et_fb_conditional_tag( 'is_front_page', $conditional_tags );
		$is_search     = et_fb_conditional_tag( 'is_search', $conditional_tags );

		// Prepare query arguments
		$query_args    = array(
			'posts_per_page' => (int) $args['posts_number'],
			'post_type'      => 'project',
			'post_status'    => 'publish',
		);

		// Conditionally get paged data
		if ( defined( 'DOING_AJAX' ) && isset( $current_page[ 'paged'] ) ) {
			$et_paged = intval( $current_page[ 'paged' ] );
		} else {
			$et_paged = $is_front_page ? get_query_var( 'page' ) : get_query_var( 'paged' );
		}

		if ( $is_front_page ) {
			$paged = $et_paged;
		}

		if ( ! is_search() ) {
			$query_args['paged'] = $et_paged;
		}

		// Passed categories parameter
		if ( '' !== $args['include_categories'] ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'project_category',
					'field'    => 'id',
					'terms'    => explode( ',', $args['include_categories'] ),
					'operator' => 'IN',
				)
			);
		}

		// Get portfolio query
		$query = new WP_Query( $query_args );

		// Format portfolio output, and add supplementary data
		$width     = 'on' === $args['fullwidth'] ?  1080 : 400;
		$width     = (int) apply_filters( 'et_pb_portfolio_image_width', $width );
		$height    = 'on' === $args['fullwidth'] ?  9999 : 284;
		$height    = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
		$classtext = 'on' === $args['fullwidth'] ? 'et_pb_post_main_image' : '';
		$titletext = get_the_title();

		// Loop portfolio item data and add supplementary data
		if ( $query->have_posts() ) {
			$post_index = 0;
			while( $query->have_posts() ) {
				$query->the_post();

				$categories = array();

				$categories_object = get_the_terms( get_the_ID(), 'project_category' );

				if ( ! empty( $categories_object ) ) {
					foreach ( $categories_object as $category ) {
						$categories[] = array(
							'id' => $category->term_id,
							'label' => $category->name,
							'permalink' => get_term_link( $category ),
						);
					}
				}

				// need to disable processnig to make sure get_thumbnail() doesn't generate errors
				$et_fb_processing_shortcode_object = false;

				// Get thumbnail
				$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				// Append value to query post
				$query->posts[ $post_index ]->post_permalink 	= get_permalink();
				$query->posts[ $post_index ]->post_thumbnail 	= print_thumbnail( $thumbnail['thumb'], $thumbnail['use_timthumb'], $titletext, $width, $height, '', false, true );
				$query->posts[ $post_index ]->post_categories 	= $categories;
				$query->posts[ $post_index ]->post_class_name 	= get_post_class( '', get_the_ID() );

				$post_index++;
			}

			$query->posts_next = array(
				'label' => esc_html__( '&laquo; Older Entries', 'et_builder' ),
				'url' => next_posts( $query->max_num_pages, false ),
			);

			$query->posts_prev = array(
				'label' => esc_html__( 'Next Entries &raquo;', 'et_builder' ),
				'url' => ( $et_paged > 1 ) ? previous_posts( false ) : '',
			);

			// Added wp_pagenavi support
			$query->wp_pagenavi = function_exists( 'wp_pagenavi' ) ? wp_pagenavi( array(
				'query' => $query,
				'echo' => false
			) ) : false;
		}

		wp_reset_postdata();

		return $query;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$fullwidth          = $this->shortcode_atts['fullwidth'];
		$posts_number       = $this->shortcode_atts['posts_number'];
		$include_categories = $this->shortcode_atts['include_categories'];
		$show_title         = $this->shortcode_atts['show_title'];
		$show_categories    = $this->shortcode_atts['show_categories'];
		$show_pagination    = $this->shortcode_atts['show_pagination'];
		$background_layout  = $this->shortcode_atts['background_layout'];
		$zoom_icon_color     = $this->shortcode_atts['zoom_icon_color'];
		$hover_overlay_color = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon          = $this->shortcode_atts['hover_icon'];

		global $paged;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		// Set inline style
		if ( '' !== $zoom_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $zoom_icon_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay',
				'declaration' => sprintf(
					'background-color: %1$s;
					border-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		$container_is_closed = false;

		// Get loop data
		$portfolio = self::get_portfolio_item( array(
			'posts_number'       => $posts_number,
			'include_categories' => $include_categories,
			'fullwidth'          => $fullwidth,
		) );

		// setup overlay
		if ( 'on' !== $fullwidth ) {
			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$overlay = sprintf( '<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		ob_start();

		if ( $portfolio->have_posts() ) {
			while( $portfolio->have_posts() ) {
				$portfolio->the_post();

				// Get $post data of current loop
				global $post;

				array_push( $post->post_class_name, 'et_pb_portfolio_item' );

				if ( 'on' !== $fullwidth ) {
					array_push( $post->post_class_name, 'et_pb_grid_item' );
				}

				?>
				<div id="post-<?php echo esc_attr( $post->ID ); ?>" class="<?php echo esc_attr( join( $post->post_class_name, ' ' ) ); ?>">

					<?php if ( '' !== $post->post_thumbnail ) { ?>
					<a href="<?php echo esc_url( $post->post_permalink ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
						<?php if ( 'on' === $fullwidth ) { ?>
							<img src="<?php echo esc_url( $post->post_thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1080" height="9999" />
						<?php } else { ?>
							<span class="et_portfolio_image">
								<img src="<?php echo esc_url( $post->post_thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="400" height="284" />
								<?php echo $overlay; ?>
							</span>
						<?php } ?>
					</a>
					<?php } ?>

					<?php if ( 'on' === $show_title ) { ?>
						<h2>
							<a href="<?php echo esc_url( $post->post_permalink ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
								<?php echo esc_html( get_the_title() ); ?>
							</a>
						</h2>
					<?php } ?>


					<?php if ( 'on' === $show_categories && ! empty( $post->post_categories ) ) : ?>
						<p class="post-meta">
							<?php
								$category_index = 0;
								foreach( $post->post_categories as $category ) {
									$category_index++;
									$separator =  $category_index < count(  $post->post_categories ) ? ', ' : '';
									echo '<a href="'. esc_url( $category['permalink'] ) .'" title="' . esc_attr( $category['label'] ) . '">' . esc_html( $category['label'] ) . '</a>' . $separator;
								}
							?>
						</p>
					<?php endif; ?>

				</div><!-- .et_pb_portfolio_item -->
				<?php
			}

			if ( 'on' === $show_pagination && ! is_search() ) {
				if ( function_exists( 'wp_pagenavi' ) ) {
					$pagination = wp_pagenavi( array( 'query' => $portfolio, 'echo' => false ) );
				} else {
					$next_posts_link_html = $prev_posts_link_html = '';

					if ( ! empty( $portfolio->posts_next['url'] ) ) {
						$next_posts_link_html = sprintf(
							'<div class="alignleft">
								<a href="%1$s">%2$s</a>
							</div>',
							esc_url( $portfolio->posts_next['url'] ),
							esc_html( $portfolio->posts_next['label'] )
						);
					}

					if ( ! empty( $portfolio->posts_prev['url'] ) ) {
						$prev_posts_link_html = sprintf(
							'<div class="alignright">
								<a href="%1$s">%2$s</a>
							</div>',
							esc_url( $portfolio->posts_prev['url'] ),
							esc_html( $portfolio->posts_prev['label'] )
						);
					}

					$pagination = sprintf(
						'<div class="pagination clearfix">
							%1$s
							%2$s
						</div>',
						$next_posts_link_html,
						$prev_posts_link_html
					);
				}
			}
		} else {
			if ( et_is_builder_plugin_active() ) {
				include( ET_BUILDER_PLUGIN_DIR . 'includes/no-results.php' );
			} else {
				get_template_part( 'includes/no-results', 'index' );
			}
		}

		// Reset post data
		wp_reset_postdata();

		$posts = ob_get_contents();

		ob_end_clean();

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";
		$fullwidth = 'on' === $fullwidth;

		$output = sprintf(
			'<div%5$s class="%1$s%3$s%6$s%7$s%9$s">
				%10$s
				%8$s
				%11$s
					%2$s
				%12$s
				%13$s
			%4$s',
			$fullwidth ? 'et_pb_portfolio' : 'et_pb_portfolio_grid clearfix',
			$posts,
			esc_attr( $class ),
			( ! $container_is_closed ? '</div> <!-- .et_pb_portfolio -->' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ), //#5
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background, //#10
			$fullwidth ? '' : '<div class="et_pb_portfolio_grid_items">',
			$fullwidth ? '' : '</div>',
			isset( $pagination ) ? $pagination : '' //#13
		);

		return $output;
	}
}
new ET_Builder_Module_Portfolio;

class ET_Builder_Module_Filterable_Portfolio extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Filterable Portfolio', 'et_builder' );
		$this->slug       = 'et_pb_filterable_portfolio';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'fullwidth',
			'posts_number',
			'include_categories',
			'show_title',
			'show_categories',
			'show_pagination',
			'background_layout',
			'admin_label',
			'module_id',
			'module_class',
			'hover_icon',
			'zoom_icon_color',
			'hover_overlay_color',
		);

		$this->fields_defaults = array(
			'fullwidth'         => array( 'on' ),
			'posts_number'      => array( 10, 'add_default_setting' ),
			'show_title'        => array( 'on' ),
			'show_categories'   => array( 'on' ),
			'show_pagination'   => array( 'on' ),
			'background_layout' => array( 'light' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_filterable_portfolio';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'  => esc_html__( 'Layout', 'et_builder' ),
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'text'    => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title'   => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2",
						'plugin_main' => "{$this->main_css_element} h2, {$this->main_css_element} h2 a",
						'important' => 'all',
					),
				),
				'filter' => array(
					'label'    => esc_html__( 'Filter', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_portfolio_filter",
						'plugin_main' => "{$this->main_css_element} .et_pb_portfolio_filter, {$this->main_css_element} .et_pb_portfolio_filter a",
						'color' => "{$this->main_css_element} .et_pb_portfolio_filter a",
					),
				),
				'caption' => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_portfolio_item",
				),
			),
		);
		$this->custom_css_options = array(
			'portfolio_filters' => array(
				'label'    => esc_html__( 'Portfolio Filters', 'et_builder' ),
				'selector' => '.et_pb_filterable_portfolio .et_pb_portfolio_filters',
				'no_space_before_selector' => true,
			),
			'active_portfolio_filter' => array(
				'label'    => esc_html__( 'Active Portfolio Filter', 'et_builder' ),
				'selector' => '.et_pb_filterable_portfolio .et_pb_portfolio_filters li a.active',
				'no_space_before_selector' => true,
			),
			'portfolio_image' => array(
				'label'    => esc_html__( 'Portfolio Image', 'et_builder' ),
				'selector' => '.et_portfolio_image',
			),
			'overlay' => array(
				'label'    => esc_html__( 'Overlay', 'et_builder' ),
				'selector' => '.et_overlay',
			),
			'overlay_icon' => array(
				'label'    => esc_html__( 'Overlay Icon', 'et_builder' ),
				'selector' => '.et_overlay:before',
			),
			'portfolio_title' => array(
				'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item h2',
			),
			'portfolio_post_meta' => array(
				'label'    => esc_html__( 'Portfolio Post Meta', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item .post-meta',
			),
			'portfolio_pagination' => array(
				'label'    => esc_html__( 'Portfolio Pagination', 'et_builder' ),
				'selector' => '.et_pb_portofolio_pagination',
			),
			'portfolio_pagination_active' => array(
				'label'    => esc_html__( 'Pagination Active Page', 'et_builder' ),
				'selector' => '.et_pb_portofolio_pagination a.active',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'fullwidth' => array(
				'label'           => esc_html__( 'Layout', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Fullwidth', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'affects' => array(
					'hover_icon',
					'zoom_icon_color',
					'hover_overlay_color',
				),
				'description'      => esc_html__( 'Choose your desired portfolio layout style.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
			),
			'posts_number' => array(
				'label'            => esc_html__( 'Posts Number', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Define the number of projects that should be displayed per page.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'toggle_slug'      => 'main_content',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'renderer'         => 'et_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select the categories that you would like to include in the feed.', 'et_builder' ),
				'computed_affects' => array(
					'__project_terms',
					'__projects',
				),
				'taxonomy_name'    => 'project_category',
				'toggle_slug'      => 'main_content',
			),
			'show_title' => array(
				'label'             => esc_html__( 'Show Title', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Turn project titles on or off.', 'et_builder' ),
			),
			'show_categories' => array(
				'label'             => esc_html__( 'Show Categories', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Turn the category links on or off.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options' => array(
					'light'  => esc_html__( 'Dark', 'et_builder' ),
					'dark' => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'depends_show_if'     => 'off',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'zoom_icon_color' => array(
				'label'             => esc_html__( 'Zoom Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'__project_terms' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Filterable_Portfolio', 'get_portfolio_terms' ),
				'computed_depends_on' => array(
					'include_categories',
				),
			),
			'__projects' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Filterable_Portfolio', 'get_portfolio_item' ),
				'computed_depends_on' => array(
					'show_pagination',
					'posts_number',
					'include_categories',
					'fullwidth',
				),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	static function get_portfolio_item( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $et_fb_processing_shortcode_object;

		$global_processing_original_value = $et_fb_processing_shortcode_object;

		$defaults = array(
			'show_pagination'    => 'on',
			'posts_number'       => '10',
			'include_categories' => '',
			'fullwidth'          => 'on',
		);

		$args = wp_parse_args( $args, $defaults );

		if( 'on' === $args['show_pagination'] ) {
			$query_args['nopaging'] = true;
		} else {
			$query_args['posts_per_page'] = (int) $args['posts_number'];
		}

		if ( '' !== $args['include_categories'] ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'project_category',
					'field' => 'id',
					'terms' => explode( ',', $args['include_categories'] ),
					'operator' => 'IN',
				)
			);
		}

		$default_query_args = array(
			'post_type'   => 'project',
			'post_status' => 'publish',
		);

		$query_args = wp_parse_args( $query_args, $default_query_args );

		// Get portfolio query
		$query = new WP_Query( $query_args );

		// Format portfolio output, and add supplementary data
		$width     = 'on' === $args['fullwidth'] ?  1080 : 400;
		$width     = (int) apply_filters( 'et_pb_portfolio_image_width', $width );
		$height    = 'on' === $args['fullwidth'] ?  9999 : 284;
		$height    = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
		$classtext = 'on' === $args['fullwidth'] ? 'et_pb_post_main_image' : '';
		$titletext = get_the_title();

		// Loop portfolio item and add supplementary data
		if( $query->have_posts() ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();

				$categories = array();

				$category_classes = array( 'et_pb_portfolio_item' );

				if ( 'on' !== $args['fullwidth'] ) {
					$category_classes[] = 'et_pb_grid_item';
				}

				$categories_object = get_the_terms( get_the_ID(), 'project_category' );
				if ( ! empty( $categories_object ) ) {
					foreach ( $categories_object as $category ) {
						// Update category classes which will be used for post_class
						$category_classes[] = 'project_category_' . urldecode( $category->slug );

						// Push category data
						$categories[] = array(
							'id'        => $category->term_id,
							'slug'      => $category->slug,
							'label'     => $category->name,
							'permalink' => get_term_link( $category ),
						);
					}
				}

				// need to disable processnig to make sure get_thumbnail() doesn't generate errors
				$et_fb_processing_shortcode_object = false;

				// Get thumbnail
				$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				// Append value to query post
				$query->posts[ $post_index ]->post_permalink 	= get_permalink();
				$query->posts[ $post_index ]->post_thumbnail 	= print_thumbnail( $thumbnail['thumb'], $thumbnail['use_timthumb'], $titletext, $width, $height, '', false, true );
				$query->posts[ $post_index ]->post_categories 	= $categories;
				$query->posts[ $post_index ]->post_class_name 	= array_merge( get_post_class( '', get_the_ID() ), $category_classes );

				// Append category classes
				$category_classes = implode( ' ', $category_classes );

				$post_index++;
			}
		}

		wp_reset_postdata();

		return $query;
	}

	static function get_portfolio_terms( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$portfolio = self::get_portfolio_item( $args, $conditional_tags, $current_page );

		$terms = array();

		if ( ! empty( $portfolio->posts ) ) {
			foreach ( $portfolio->posts as $post ) {
				if ( ! empty( $post->post_categories ) ) {
					foreach ( $post->post_categories as $category ) {
						$terms[ $category['slug'] ] = $category;
					}
				}
			}
		}

		return $terms;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$fullwidth          = $this->shortcode_atts['fullwidth'];
		$posts_number       = $this->shortcode_atts['posts_number'];
		$include_categories = $this->shortcode_atts['include_categories'];
		$show_title         = $this->shortcode_atts['show_title'];
		$show_categories    = $this->shortcode_atts['show_categories'];
		$show_pagination    = $this->shortcode_atts['show_pagination'];
		$background_layout  = $this->shortcode_atts['background_layout'];
		$hover_icon          = $this->shortcode_atts['hover_icon'];
		$zoom_icon_color     = $this->shortcode_atts['zoom_icon_color'];
		$hover_overlay_color = $this->shortcode_atts['hover_overlay_color'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		wp_enqueue_script( 'hashchange' );

		if ( '' !== $zoom_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $zoom_icon_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay',
				'declaration' => sprintf(
					'background-color: %1$s;
					border-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		$projects = self::get_portfolio_item( array(
			'show_pagination'    => $show_pagination,
			'posts_number'       => $posts_number,
			'include_categories' => $include_categories,
			'fullwidth'          => $fullwidth,
		) );

		$categories_included = array();
		ob_start();
		if( $projects->post_count > 0 ) {
			while ( $projects->have_posts() ) {
				$projects->the_post();

				$category_classes = array();
				$categories = get_the_terms( get_the_ID(), 'project_category' );
				if ( $categories ) {
					foreach ( $categories as $category ) {
						$category_classes[] = 'project_category_' . urldecode( $category->slug );
						$categories_included[] = $category->term_id;
					}
				}

				$category_classes = implode( ' ', $category_classes );

				$main_post_class = sprintf(
					'et_pb_portfolio_item%1$s %2$s',
					( 'on' !== $fullwidth ? ' et_pb_grid_item' : '' ),
					$category_classes
				);

				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class( $main_post_class ); ?>>
				<?php
					$thumb = '';

					$width = 'on' === $fullwidth ?  1080 : 400;
					$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

					$height = 'on' === $fullwidth ?  9999 : 284;
					$height = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
					$classtext = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
					$titletext = get_the_title();
					$permalink = get_permalink();
					$post_meta = get_the_term_list( get_the_ID(), 'project_category', '', ', ' );
					$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
					$thumb = $thumbnail["thumb"];


					if ( '' !== $thumb ) : ?>
						<a href="<?php echo esc_url( $permalink ); ?>">
						<?php if ( 'on' !== $fullwidth ) : ?>
							<span class="et_portfolio_image">
						<?php endif; ?>
								<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height ); ?>
						<?php if ( 'on' !== $fullwidth ) :

								$data_icon = '' !== $hover_icon
									? sprintf(
										' data-icon="%1$s"',
										esc_attr( et_pb_process_font_icon( $hover_icon ) )
									)
									: '';

								printf( '<span class="et_overlay%1$s"%2$s></span>',
									( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
									$data_icon
								);

						?>
							</span>
						<?php endif; ?>
						</a>
				<?php
					endif;
				?>

				<?php if ( 'on' === $show_title ) : ?>
					<h2><a href="<?php echo esc_url( $permalink ); ?>"><?php echo $titletext; ?></a></h2>
				<?php endif; ?>

				<?php if ( 'on' === $show_categories ) : ?>
					<p class="post-meta"><?php echo $post_meta; ?></p>
				<?php endif; ?>

				</div><!-- .et_pb_portfolio_item -->
				<?php
			}
		}

		wp_reset_postdata();

		$posts = ob_get_clean();

		$categories_included = explode ( ',', $include_categories );
		$terms_args = array(
			'include' => $categories_included,
			'orderby' => 'name',
			'order' => 'ASC',
		);
		$terms = get_terms( 'project_category', $terms_args );

		$category_filters = '<ul class="clearfix">';
		$category_filters .= sprintf( '<li class="et_pb_portfolio_filter et_pb_portfolio_filter_all"><a href="#" class="active" data-category-slug="all">%1$s</a></li>',
			esc_html__( 'All', 'et_builder' )
		);
		foreach ( $terms as $term  ) {
			$category_filters .= sprintf( '<li class="et_pb_portfolio_filter"><a href="#" data-category-slug="%1$s">%2$s</a></li>',
				esc_attr( urldecode( $term->slug ) ),
				esc_html( $term->name )
			);
		}
		$category_filters .= '</ul>';

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$output = sprintf(
			'<div%5$s class="et_pb_filterable_portfolio et_pb_portfolio %1$s%4$s%6$s%11$s%13$s" data-posts-number="%7$d"%10$s>
				%14$s
				%12$s
				<div class="et_pb_portfolio_filters clearfix">%2$s</div><!-- .et_pb_portfolio_filters -->

				<div class="et_pb_portfolio_items_wrapper %8$s">
					<div class="et_pb_portfolio_items">%3$s</div><!-- .et_pb_portfolio_items -->
				</div>
				%9$s
			</div> <!-- .et_pb_filterable_portfolio -->',
			( 'on' === $fullwidth ? 'et_pb_filterable_portfolio_fullwidth' : 'et_pb_filterable_portfolio_grid clearfix' ),
			$category_filters,
			$posts,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $posts_number),
			('on' === $show_pagination ? '' : 'no_pagination' ),
			('on' === $show_pagination ? '<div class="et_pb_portofolio_pagination"></div>' : '' ),
			is_rtl() ? ' data-rtl="true"' : '',
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Filterable_Portfolio;

class ET_Builder_Module_Bar_Counters extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Bar Counters', 'et_builder' );
		$this->slug            = 'et_pb_counters';
		$this->fb_support      = true;
		$this->child_slug      = 'et_pb_counter';
		$this->child_item_text = esc_html__( 'Bar Counter', 'et_builder' );

		$this->whitelisted_fields = array(
			'background_layout',
			'background_color',
			'bar_bg_color',
			'use_percentages',
			'admin_label',
			'module_id',
			'module_class',
			'bar_top_padding',
			'bar_bottom_padding',
			'border_radius',
			'bar_bottom_padding_tablet',
			'bar_top_padding_tablet',
			'bar_bottom_padding_phone',
			'bar_top_padding_phone',
			'bar_bottom_padding_last_edited',
			'bar_top_padding_last_edited',
		);

		$this->fields_defaults = array(
			'background_layout' => array( 'light' ),
			'background_color'  => array( '#dddddd', 'add_default_setting' ),
			'bar_bg_color'      => array( et_builder_accent_color(), 'add_default_setting' ),
			'use_percentages'   => array( 'on' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_counters';
		$this->defaults         = array(
			'border_radius' => '0',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'   => esc_html__( 'Elements', 'et_builder' ),
					'background' => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'padding'  => array(
						'title'    => esc_html__( 'Spacing', 'et_builder' ),
						'priority' => 70,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_counter_title",
					),
				),
				'percent'   => array(
					'label'    => esc_html__( 'Percent', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_counter_amount",
					),
				),
			),
			'border' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_counter_container",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_counter_container",
				),
			),
		);
		$this->custom_css_options = array(
			'counter_title' => array(
				'label'    => esc_html__( 'Counter Title', 'et_builder' ),
				'selector' => '.et_pb_counter_title',
			),
			'counter_container' => array(
				'label'    => esc_html__( 'Counter Container', 'et_builder' ),
				'selector' => '.et_pb_counter_container',
			),
			'counter_amount' => array(
				'label'    => esc_html__( 'Counter Amount', 'et_builder' ),
				'selector' => '.et_pb_counter_amount',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'This will adjust the color of the empty space in the bar (currently gray).', 'et_builder' ),
			),
			'bar_bg_color' => array(
				'label'             => esc_html__( 'Bar Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
			),
			'use_percentages' => array(
				'label'             => esc_html__( 'Use Percentages', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
			),
			'bar_top_padding' => array(
				'label'           => esc_html__( 'Bar Top Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'bar_bottom_padding' => array(
				'label'           => esc_html__( 'Bar Bottom Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'border_radius' => array(
				'label'             => esc_html__( 'Border Radius', 'et_builder' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'border',
			),
			'bar_bottom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bar_bottom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bar_bottom_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bar_top_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bar_top_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bar_top_padding_last_edited' =>array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_counters_settings;

		$background_color          = $this->shortcode_atts['background_color'];
		$background_image          = $this->shortcode_atts['background_image'];
		$parallax                  = $this->shortcode_atts['parallax'];
		$parallax_method           = $this->shortcode_atts['parallax_method'];
		$background_video_mp4      = $this->shortcode_atts['background_video_mp4'];
		$background_video_webm     = $this->shortcode_atts['background_video_webm'];
		$background_video_width    = $this->shortcode_atts['background_video_width'];
		$background_video_height   = $this->shortcode_atts['background_video_height'];
		$allow_player_pause        = $this->shortcode_atts['allow_player_pause'];
		$bar_bg_color              = $this->shortcode_atts['bar_bg_color'];
		$use_percentages           = $this->shortcode_atts['use_percentages'];
		$bar_top_padding           = $this->shortcode_atts['bar_top_padding'];
		$bar_bottom_padding        = $this->shortcode_atts['bar_bottom_padding'];
		$bar_top_padding_tablet    = $this->shortcode_atts['bar_top_padding_tablet'];
		$bar_bottom_padding_tablet = $this->shortcode_atts['bar_bottom_padding_tablet'];
		$bar_top_padding_phone     = $this->shortcode_atts['bar_top_padding_phone'];
		$bar_bottom_padding_phone  = $this->shortcode_atts['bar_bottom_padding_phone'];
		$bar_top_padding_last_edited     = $this->shortcode_atts['bar_top_padding_last_edited'];
		$bar_bottom_padding_last_edited  = $this->shortcode_atts['bar_bottom_padding_last_edited'];
		$border_radius             = $this->shortcode_atts['border_radius'];

		$et_pb_counters_settings = array(
			'background_color'          => $background_color,
			'background_image'          => $background_image,
			'parallax'                  => $parallax,
			'parallax_method'           => $parallax_method,
			'background_video_mp4'      => $background_video_mp4,
			'background_video_webm'     => $background_video_webm,
			'background_video_width'    => $background_video_width,
			'background_video_height'   => $background_video_height,
			'allow_player_pause'        => $allow_player_pause,
			'bar_bg_color'              => $bar_bg_color,
			'use_percentages'           => $use_percentages,
			'bar_top_padding'           => $bar_top_padding,
			'bar_bottom_padding'        => $bar_bottom_padding,
			'bar_top_padding_tablet'    => $bar_top_padding_tablet,
			'bar_bottom_padding_tablet' => $bar_bottom_padding_tablet,
			'bar_top_padding_phone'     => $bar_top_padding_phone,
			'bar_bottom_padding_phone'  => $bar_bottom_padding_phone,
			'bar_top_padding_last_edited'     => $bar_top_padding_last_edited,
			'bar_bottom_padding_last_edited'  => $bar_bottom_padding_last_edited,
			'border_radius'             => $border_radius,
		);
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$background_layout  = $this->shortcode_atts['background_layout'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$video_background = $this->video_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$output = sprintf(
			'<ul%3$s class="et_pb_counters et-waypoint%2$s%4$s%5$s">
				%1$s
			</ul> <!-- .et_pb_counters -->',
			$this->shortcode_content,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : ''
		);

		return $output;
	}
}
new ET_Builder_Module_Bar_Counters;

class ET_Builder_Module_Bar_Counters_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Bar Counter', 'et_builder' );
		$this->slug                        = 'et_pb_counter';
		$this->fb_support                  = true;
		$this->type                        = 'child';
		$this->child_title_var             = 'content_new';

		$this->whitelisted_fields = array(
			'content_new',
			'percent',
			'background_color',
			'bar_background_color',
			'label_color',
			'percentage_color',
		);

		$this->fields_defaults = array(
			'percent' => array( '0' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Bar Counter', 'et_builder' );
		$this->settings_text               = esc_html__( 'Bar Counter Settings', 'et_builder' );
		$this->main_css_element            = '%%order_class%%';
		$this->defaults                    = array(
			'border_radius' => '0',
		);

		$this->advanced_options = array(
			'background' => array(
				'use_background_color'          => false,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'css' => array(
					'main' => ".et_pb_counters {$this->main_css_element} .et_pb_counter_container",
				),
			),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'label'      => esc_html__( 'Label', 'et_builder' ),
					'percentage' => esc_html__( 'Percentage', 'et_builder' ),
				),
			),
		);

		$this->custom_css_options = array(
			'counter_title' => array(
				'label'    => esc_html__( 'Counter Title', 'et_builder' ),
				'selector' => '.et_pb_counter_title',
			),
			'counter_container' => array(
				'label'    => esc_html__( 'Counter Container', 'et_builder' ),
				'selector' => '.et_pb_counter_container',
			),
			'counter_amount' => array(
				'label'    => esc_html__( 'Counter Amount', 'et_builder' ),
				'selector' => '.et_pb_counter_amount',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'content_new' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for your bar.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'percent' => array(
				'label'           => esc_html__( 'Percent', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a percentage for this bar.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'background_color' => array(
				'label'        => esc_html__( 'Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'bar_background_color' => array(
				'label'        => esc_html__( 'Bar Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'label_color' => array(
				'label'        => esc_html__( 'Label Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'label',
			),
			'percentage_color' => array(
				'label'        => esc_html__( 'Percentage Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'percentage',
			),
		);

		return $fields;
	}

	function get_parallax_image_background() {
		global $et_pb_counters_settings;

		// Parallax setting is only derived from parent if bar counter item has no background
		$use_counter_value   = '' !== $this->shortcode_atts['background_color'] || 'on' === $this->shortcode_atts['use_background_color_gradient'] || '' !== $this->shortcode_atts['background_image'] || '' !== $this->shortcode_atts['background_video_mp4'] || '' !== $this->shortcode_atts['background_video_webm'];
		$background_image    = $use_counter_value ? $this->shortcode_atts['background_image'] : $et_pb_counters_settings['background_image'];
		$parallax            = $use_counter_value ? $this->shortcode_atts['parallax'] : $et_pb_counters_settings['parallax'];
		$parallax_method     = $use_counter_value ? $this->shortcode_atts['parallax_method'] : $et_pb_counters_settings['parallax_method'];
		$parallax_background = '';

		if ( '' !== $background_image && 'on' == $parallax ) {
			$parallax_classname = array(
				'et_parallax_bg'
			);

			if ( 'off' === $parallax_method ) {
				$parallax_classname[] = 'et_pb_parallax_css';
			}

			$parallax_background = sprintf( '<div
					class="%1$s"
					style="background-image: url(%2$s);"
					></div>',
				esc_attr( implode( ' ', $parallax_classname ) ),
				esc_attr( $background_image )
			);
		}

		return $parallax_background;
	}

	function video_background( $args = array() ) {
		global $et_pb_counters_settings;

		$use_counter_value       = '' !== $this->shortcode_atts['background_color'] || 'on' === $this->shortcode_atts['use_background_color_gradient'] || '' !== $this->shortcode_atts['background_image'] || '' !== $this->shortcode_atts['background_video_mp4'] || '' !== $this->shortcode_atts['background_video_webm'];
		$background_video_mp4    = $use_counter_value ? $this->shortcode_atts['background_video_mp4'] : $et_pb_counters_settings['background_video_mp4'];
		$background_video_webm   = $use_counter_value ? $this->shortcode_atts['background_video_webm'] : $et_pb_counters_settings['background_video_webm'];
		$background_video_width  = $use_counter_value ? $this->shortcode_atts['background_video_width'] : $et_pb_counters_settings['background_video_width'];
		$background_video_height = $use_counter_value ? $this->shortcode_atts['background_video_height'] : $et_pb_counters_settings['background_video_height'];

		if ( ! empty( $args ) ) {
			$background_video = self::get_video_background( $args );

			$allow_player_pause = isset( $args['allow_player_pause' ] ) ? $args['allow_player_pause' ] : 'off';
		} else {
			$background_video = self::get_video_background( array(
				'background_video_mp4'    => $background_video_mp4,
				'background_video_webm'   => $background_video_webm,
				'background_video_width'  => $background_video_width,
				'background_video_height' => $background_video_height,
			) );

			$allow_player_pause = $use_counter_value ? $this->shortcode_atts['allow_player_pause'] : $et_pb_counters_settings['allow_player_pause'];
		}

		$video_background = '';

		if ( $background_video ) {
			$video_background = sprintf(
				'<div class="et_pb_section_video_bg%2$s">
					%1$s
				</div>',
				$background_video,
				( 'on' === $allow_player_pause ? ' et_pb_allow_player_pause' : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		return $video_background;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_counters_settings;

		$percent                       = $this->shortcode_atts['percent'];
		$background_color              = $this->shortcode_atts['background_color'];
		$bar_background_color          = $this->shortcode_atts['bar_background_color'];
		$background_image              = $this->shortcode_atts['background_image'];
		$use_background_color_gradient = $this->shortcode_atts['use_background_color_gradient'];
		$label_color                   = $this->shortcode_atts['label_color'];
		$percentage_color              = $this->shortcode_atts['percentage_color'];

		$module_class = ET_Builder_Element::add_module_order_class( '', $function_name );

		// Add % only if it hasn't been added to the attribute
		if ( '%' !== substr( trim( $percent ), -1 ) ) {
			$percent .= '%';
		}

		$background_color_style = $bar_bg_color_style = '';

		if ( '' === $background_color && isset( $et_pb_counters_settings['background_color'] ) && '' !== $et_pb_counters_settings['background_color'] ) {
			$background_color_style = sprintf( ' style="background-color: %1$s;"', esc_attr( $et_pb_counters_settings['background_color'] ) );
		}

		if ( '' !== $background_color ) {
			if ( empty( $background_image ) && 'on' !== $use_background_color_gradient ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '.et_pb_counters %%order_class%% .et_pb_counter_container',
					'declaration' => 'background-image: none;',
				) );
			}
		}

		if ( '' === $bar_background_color && isset( $et_pb_counters_settings['bar_bg_color'] ) && '' !== $et_pb_counters_settings['bar_bg_color'] ) {
			$bar_bg_color_style = sprintf( ' background-color: %1$s;', esc_attr( $et_pb_counters_settings['bar_bg_color'] ) );
		}

		if ( ! empty( $et_pb_counters_settings['border_radius'] ) && $this->defaults['border_radius'] !== $et_pb_counters_settings['border_radius'] ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_counter_container, %%order_class%% .et_pb_counter_amount',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s; -webkit-border-radius: %1$s; border-radius: %1$s;',
					esc_html( et_builder_process_range_value( $et_pb_counters_settings['border_radius'] ) )
				),
			) );
		}

		if ( ( isset( $et_pb_counters_settings['bar_top_padding'] ) && '' !== $et_pb_counters_settings['bar_top_padding'] ) || ( isset( $et_pb_counters_settings['bar_top_padding_tablet'] ) && '' !== $et_pb_counters_settings['bar_top_padding_tablet'] ) || ( isset( $et_pb_counters_settings['bar_top_padding_phone'] ) && '' !== $et_pb_counters_settings['bar_top_padding_phone'] ) ) {
			$bar_top_padding_responsive_active = et_pb_get_responsive_status( $et_pb_counters_settings['bar_top_padding_last_edited'] );

			$padding_values = array(
				'desktop' => isset( $et_pb_counters_settings['bar_top_padding'] ) ? $et_pb_counters_settings['bar_top_padding'] : '',
				'tablet'  => isset( $et_pb_counters_settings['bar_top_padding_tablet'] ) && $bar_top_padding_responsive_active ? $et_pb_counters_settings['bar_top_padding_tablet'] : '',
				'phone'   => isset( $et_pb_counters_settings['bar_top_padding_phone'] ) && $bar_top_padding_responsive_active ? $et_pb_counters_settings['bar_top_padding_phone'] : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_counter_amount', 'padding-top', $function_name );
		}

		if ( ( isset( $et_pb_counters_settings['bar_bottom_padding'] ) && '' !== $et_pb_counters_settings['bar_bottom_padding'] ) || ( isset( $et_pb_counters_settings['bar_bottom_padding_tablet'] ) && '' !== $et_pb_counters_settings['bar_bottom_padding_tablet'] ) || ( isset( $et_pb_counters_settings['bar_bottom_padding_phone'] ) && '' !== $et_pb_counters_settings['bar_bottom_padding_phone'] ) ) {
			$bar_bottom_padding_responsive_active = et_pb_get_responsive_status( $et_pb_counters_settings['bar_bottom_padding_last_edited'] );

			$padding_values = array(
				'desktop' => isset( $et_pb_counters_settings['bar_bottom_padding'] ) ? $et_pb_counters_settings['bar_bottom_padding'] : '',
				'tablet'  => isset( $et_pb_counters_settings['bar_bottom_padding_tablet'] ) && $bar_bottom_padding_responsive_active ? $et_pb_counters_settings['bar_bottom_padding_tablet'] : '',
				'phone'   => isset( $et_pb_counters_settings['bar_bottom_padding_phone'] ) && $bar_bottom_padding_responsive_active ? $et_pb_counters_settings['bar_bottom_padding_phone'] : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_counter_amount', 'padding-bottom', $function_name );
		}

		if ( '' !== $background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_counter_container',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_color )
				),
			) );
		}

		if ( '' !== $bar_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_counter_amount',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $bar_background_color )
				),
			) );
		}

		if ( '' !== $label_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '.et_pb_counters %%order_class%% span.et_pb_counter_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $label_color )
				),
			) );
		}

		if ( '' !== $percentage_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '.et_pb_counters %%order_class%% span.et_pb_counter_amount',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $percentage_color )
				),
			) );
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$output = sprintf(
			'<li class="%6$s%7$s%9$s">
				<span class="et_pb_counter_title">%1$s</span>
				<span class="et_pb_counter_container"%4$s>
					%10$s
					%8$s
					<span class="et_pb_counter_amount" style="%5$s" data-width="%3$s"><span class="et_pb_counter_amount_number">%2$s</span></span>
				</span>
			</li>',
			sanitize_text_field( $content ),
			( isset( $et_pb_counters_settings['use_percentages'] ) && 'on' === $et_pb_counters_settings['use_percentages'] ? esc_html( $percent ) : '' ),
			esc_attr( $percent ),
			$background_color_style,
			$bar_bg_color_style,
			esc_attr( ltrim( $module_class ) ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Bar_Counters_Item;

class ET_Builder_Module_Circle_Counter extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Circle Counter', 'et_builder' );
		$this->slug       = 'et_pb_circle_counter';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'title',
			'number',
			'percent_sign',
			'background_layout',
			'bar_bg_color',
			'admin_label',
			'module_id',
			'module_class',
			'circle_color',
			'circle_color_alpha',
		);

		$this->fields_defaults = array(
			'number'            => array( '0' ),
			'percent_sign'      => array( 'on' ),
			'background_layout' => array( 'light' ),
			'bar_bg_color'      => array( et_builder_accent_color(), 'add_default_setting' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_circle_counter';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'circle' => esc_html__( 'Circle', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h3",
						'important' => 'plugin_only',
					),
				),
				'number'   => array(
					'label'    => esc_html__( 'Number', 'et_builder' ),
					'hide_line_height' => true,
					'css'      => array(
						'main' => "{$this->main_css_element} .percent p",
					),
				),
			),
		);
		$this->custom_css_options = array(
			'percent' => array(
				'label'    => esc_html__( 'Percent Container', 'et_builder' ),
				'selector' => '.percent',
			),
			'circle_counter_title' => array(
				'label'    => esc_html__( 'Circle Counter Title', 'et_builder' ),
				'selector' => 'h3',
			),
			'percent_text' => array(
				'label'    => esc_html__( 'Percent Text', 'et_builder' ),
				'selector' => '.percent p',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for the circle counter.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'number' => array(
				'label'             => esc_html__( 'Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'number_validation' => true,
				'value_type'        => 'int',
				'value_min'         => 0,
				'value_max'         => 100,
				'description'       => et_get_safe_localization( __( "Define a number for the circle counter. (Don't include the percentage sign, use the option below.). <strong>Note: You can use only natural numbers from 0 to 100</strong>", 'et_builder' ) ),
				'toggle_slug'       => 'main_content',
			),
			'percent_sign' => array(
				'label'           => esc_html__( 'Percent Sign', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Here you can choose whether the percent sign should be added after the number set above.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'bar_bg_color' => array(
				'label'             => esc_html__( 'Bar Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
			),
			'circle_color' => array(
				'label'             => esc_html__( 'Circle Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'circle',
			),
			'circle_color_alpha' => array(
				'label'           => esc_html__( 'Circle Color Opacity', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'range_settings'  => array(
					'min'  => '0.1',
					'max'  => '1.0',
					'step' => '0.05',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'circle',
				'validate_unit'   => false,
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		wp_enqueue_script( 'easypiechart' );
		$number                      = $this->shortcode_atts['number'];
		$percent_sign                = $this->shortcode_atts['percent_sign'];
		$title                       = $this->shortcode_atts['title'];
		$module_id                   = $this->shortcode_atts['module_id'];
		$module_class                = $this->shortcode_atts['module_class'];
		$background_layout           = $this->shortcode_atts['background_layout'];
		$bar_bg_color                = $this->shortcode_atts['bar_bg_color'];
		$circle_color                = $this->shortcode_atts['circle_color'];
		$circle_color_alpha          = $this->shortcode_atts['circle_color_alpha'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$number = str_ireplace( '%', '', $number );

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$circle_color_data = '' !== $circle_color ?
			sprintf( ' data-color="%1$s"', esc_attr( $circle_color ) )
			: '';
		$circle_color_alpha_data = '' !== $circle_color_alpha ?
			sprintf( ' data-alpha="%1$s"', esc_attr( $circle_color_alpha ) )
			: '';

		$output = sprintf(
			'<div%1$s class="et_pb_circle_counter container-width-change-notify%2$s%3$s" data-number-value="%4$s" data-bar-bg-color="%5$s"%8$s%9$s>
					<div class="percent"><p><span class="percent-value"></span>%6$s</p></div>
					%7$s
			</div><!-- .et_pb_circle_counter -->',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			esc_attr( $class ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $number ),
			esc_attr( $bar_bg_color ),
			( 'on' == $percent_sign ? '%' : ''),
			( '' !== $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' ),
			$circle_color_data,
			$circle_color_alpha_data
		);

		return $output;
	}
}
new ET_Builder_Module_Circle_Counter;

class ET_Builder_Module_Number_Counter extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Number Counter', 'et_builder' );
		$this->slug       = 'et_pb_number_counter';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'title',
			'number',
			'percent_sign',
			'counter_color',
			'background_layout',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'number'            => array( '0' ),
			'percent_sign'      => array( 'on' ),
			'number_text_color' => array( et_builder_accent_color(), 'add_default_setting' ),
			'background_layout' => array( 'light' ),
		);

		$this->custom_css_options = array(
			'percent' => array(
				'label'    => esc_html__( 'Percent', 'et_builder' ),
				'selector' => '.percent',
			),
			'number_counter_title' => array(
				'label'    => esc_html__( 'Number Counter Title', 'et_builder' ),
				'selector' => 'h3',
			),
		);

		$this->main_css_element = '%%order_class%%.et_pb_number_counter';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h3",
						'important' => 'plugin_only',
					),
				),
				'number'   => array(
					'label'    => esc_html__( 'Number', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .percent p",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'text_color' => array(
						'old_option_ref' => 'counter_color',
						'default' => et_builder_accent_color(),
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'use_margin' => false,
				'css' => array(
					'important' => 'all',
				),
			),
		);

		if ( et_is_builder_plugin_active() ) {
			$this->advanced_options['fonts']['number']['css']['important'] = 'all';
		}
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for the counter.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'number' => array(
				'label'           => esc_html__( 'Number', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'value_type'      => 'float',
				'description'     => esc_html__( "Define a number for the counter. (Don't include the percentage sign, use the option below.)", 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'percent_sign' => array(
				'label'             => esc_html__( 'Percent Sign', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether the percent sign should be added after the number set above.', 'et_builder' ),
			),
			'counter_color' => array(
				'type'              => 'hidden',
				'shortcode_default' => '',
				'tab_slug'          => 'advanced',
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your title text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		wp_enqueue_script( 'easypiechart' );
		$number            = $this->shortcode_atts['number'];
		$percent_sign      = $this->shortcode_atts['percent_sign'];
		$title             = $this->shortcode_atts['title'];
		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$counter_color     = $this->shortcode_atts['counter_color'];
		$background_layout = $this->shortcode_atts['background_layout'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( et_is_builder_plugin_active() ) {
			wp_enqueue_script( 'fittext' );
		}

		$separator = strpos( $number, ',' ) ? ',' : '';
		$number = str_ireplace( array( '%', ',' ), '', $number );

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$output = sprintf(
			'<div%1$s class="et_pb_number_counter%2$s%3$s%9$s%11$s" data-number-value="%4$s" data-number-separator="%8$s">
				%12$s
				%10$s
				<div class="percent"%5$s><p><span class="percent-value"></span>%6$s</p></div>
				%7$s
			</div><!-- .et_pb_number_counter -->',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			esc_attr( $class ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $number ),
			( '' !== $counter_color ? sprintf( ' style="color:%s"', esc_attr( $counter_color ) ) : '' ),
			( 'on' == $percent_sign ? '%' : ''),
			( '' !== $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' ),
			esc_attr( $separator ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		 );

		return $output;
	}
}
new ET_Builder_Module_Number_Counter;

class ET_Builder_Module_Accordion extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Accordion', 'et_builder' );
		$this->slug       = 'et_pb_accordion';
		$this->fb_support = true;
		$this->child_slug = 'et_pb_accordion_item';

		$this->whitelisted_fields = array(
			'admin_label',
			'module_id',
			'module_class',
			'open_toggle_background_color',
			'closed_toggle_background_color',
			'icon_color',
			'closed_toggle_text_color',
			'open_toggle_text_color',
		);

		$this->main_css_element = '%%order_class%%.et_pb_accordion';

		$this->options_toggles = array(
			'advanced' => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element} .et_pb_toggle_content",
						'plugin_main' => "{$this->main_css_element} .et_pb_toggle_content, {$this->main_css_element} .et_pb_toggle_content p",
						'line_height' => "{$this->main_css_element} .et_pb_toggle_content p",
					),
				),
				'toggle' => array(
					'label'    => esc_html__( 'Toggle', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h5.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_color_gradient' => false,
				'use_background_image'          => false,
				'use_background_video'          => false,
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_toggle_content",
				),
			),
			'border' => array(
				'css'        => array(
					'main' => "{$this->main_css_element} .et_pb_toggle",
				),
			),
			'custom_margin_padding' => array(
				'use_margin' => false,
				'css'        => array(
					'main' => "{$this->main_css_element} .et_pb_toggle_content",
					'important' => 'all',
				),
			),
		);
		$this->custom_css_options = array(
			'toggle' => array(
				'label'    => esc_html__( 'Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle',
			),
			'open_toggle' => array(
				'label'    => esc_html__( 'Open Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle_open',
			),
			'toggle_title' => array(
				'label'    => esc_html__( 'Toggle Title', 'et_builder' ),
				'selector' => '.et_pb_toggle_title',
			),
			'toggle_icon' => array(
				'label'    => esc_html__( 'Toggle Icon', 'et_builder' ),
				'selector' => '.et_pb_toggle_title:before',
			),
			'toggle_content' => array(
				'label'    => esc_html__( 'Toggle Content', 'et_builder' ),
				'selector' => '.et_pb_toggle_content',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'open_toggle_background_color' => array(
				'label'             => esc_html__( 'Open Toggle Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'open_toggle_text_color' => array(
				'label'             => esc_html__( 'Open Toggle Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
			),
			'closed_toggle_background_color' => array(
				'label'             => esc_html__( 'Closed Toggle Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'closed_toggle_text_color' => array(
				'label'             => esc_html__( 'Closed Toggle Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_accordion_item_number;

		$et_pb_accordion_item_number = 1;

	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                      = $this->shortcode_atts['module_id'];
		$module_class                   = $this->shortcode_atts['module_class'];
		$open_toggle_background_color   = $this->shortcode_atts['open_toggle_background_color'];
		$closed_toggle_background_color = $this->shortcode_atts['closed_toggle_background_color'];
		$icon_color                     = $this->shortcode_atts['icon_color'];
		$closed_toggle_text_color       = $this->shortcode_atts['closed_toggle_text_color'];
		$open_toggle_text_color         = $this->shortcode_atts['open_toggle_text_color'];

		global $et_pb_accordion_item_number;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $open_toggle_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_toggle_open',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $open_toggle_background_color )
				),
			) );
		}

		if ( '' !== $closed_toggle_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_toggle_close',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $closed_toggle_background_color )
				),
			) );
		}

		if ( '' !== $icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_toggle_title:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $icon_color )
				),
			) );
		}

		if ( '' !== $closed_toggle_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_toggle_close h5.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $closed_toggle_text_color )
				),
			) );
		}

		if ( '' !== $open_toggle_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_toggle_open h5.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $open_toggle_text_color )
				),
			) );
		}

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_accordion%2$s">
				%1$s
			</div> <!-- .et_pb_accordion -->',
			$this->shortcode_content,
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Accordion;

class ET_Builder_Module_Accordion_Item extends ET_Builder_Module {
	function init() {
		$this->name                  = esc_html__( 'Accordion', 'et_builder' );
		$this->slug                  = 'et_pb_accordion_item';
		$this->fb_support            = true;
		$this->type                  = 'child';
		$this->child_title_var       = 'title';
		$this->no_shortcode_callback = true;

		$this->whitelisted_fields = array(
			'title',
			'content_new',
			'open_toggle_background_color',
			'open_toggle_text_color',
			'closed_toggle_background_color',
			'closed_toggle_text_color',
			'icon_color',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options      = array(
			'background' => array(
				'use_background_color'          => false,
				'use_background_color_gradient' => false,
				'use_background_image'          => false,
				'use_background_video'          => false,
			),
			'custom_margin_padding' => array(
				'use_margin' => false,
				'css' => array(
					'important' => 'all'
				)
			),
		);

		$this->custom_css_options = array(
			'toggle' => array(
				'label'    => esc_html__( 'Toggle', 'et_builder' ),
			),
			'open_toggle' => array(
				'label'    => esc_html__( 'Open Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle_open',
				'no_space_before_selector' => true,
			),
			'toggle_title' => array(
				'label'    => esc_html__( 'Toggle Title', 'et_builder' ),
				'selector' => '.et_pb_toggle_title',
			),
			'toggle_icon' => array(
				'label'    => esc_html__( 'Toggle Icon', 'et_builder' ),
				'selector' => '.et_pb_toggle_title:before',
			),
			'toggle_content' => array(
				'label'    => esc_html__( 'Toggle Content', 'et_builder' ),
				'selector' => '.et_pb_toggle_content',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The toggle title will appear above the content and when the toggle is closed.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'open_toggle_background_color' => array(
				'label'             => esc_html__( 'Open Toggle Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'open_toggle_text_color' => array(
				'label'             => esc_html__( 'Open Toggle Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
			),
			'closed_toggle_background_color' => array(
				'label'             => esc_html__( 'Closed Toggle Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'closed_toggle_text_color' => array(
				'label'             => esc_html__( 'Closed Toggle Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
			),
		);
		return $fields;
	}
}
new ET_Builder_Module_Accordion_Item;

class ET_Builder_Module_Toggle extends ET_Builder_Module {
	function init() {
		$this->name                       = esc_html__( 'Toggle', 'et_builder' );
		$this->slug                       = 'et_pb_toggle';
		$this->fb_support                 = true;
		$this->additional_shortcode_slugs = array( 'et_pb_accordion_item' );

		$this->whitelisted_fields = array(
			'title',
			'open',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'open_toggle_background_color',
			'closed_toggle_background_color',
			'icon_color',
			'closed_toggle_text_color',
			'open_toggle_text_color',
		);

		$this->fields_defaults = array(
			'open' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_toggle';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'state'        => esc_html__( 'State', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h5",
						'important' => 'plugin_only',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element}",
						'plugin_main' => "{$this->main_css_element}, {$this->main_css_element} p",
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'use_margin' => false,
				'css' => array(
					'important' => 'all',
				),
			),
		);
		$this->custom_css_options = array(
			'open_toggle' => array(
				'label'    => esc_html__( 'Open Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle.et_pb_toggle_open',
				'no_space_before_selector' => true,
			),
			'toggle_title' => array(
				'label'    => esc_html__( 'Toggle Title', 'et_builder' ),
				'selector' => '.et_pb_toggle_title',
			),
			'toggle_icon' => array(
				'label'    => esc_html__( 'Toggle Icon', 'et_builder' ),
				'selector' => '.et_pb_toggle_title:before',
			),
			'toggle_content' => array(
				'label'    => esc_html__( 'Toggle Content', 'et_builder' ),
				'selector' => '.et_pb_toggle_content',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The toggle title will appear above the content and when the toggle is closed.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'open' => array(
				'label'           => esc_html__( 'State', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'Close', 'et_builder' ),
					'on'  => esc_html__( 'Open', 'et_builder' ),
				),
				'toggle_slug'     => 'state',
				'description'     => esc_html__( 'Choose whether or not this toggle should start in an open or closed state.', 'et_builder' ),
			),
			'content_new' => array(
				'label'             => esc_html__( 'Content', 'et_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
			),
			'open_toggle_background_color' => array(
				'label'             => esc_html__( 'Open Toggle Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'open_toggle_text_color' => array(
				'label'             => esc_html__( 'Open Toggle Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
			),
			'closed_toggle_background_color' => array(
				'label'             => esc_html__( 'Closed Toggle Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'closed_toggle_text_color' => array(
				'label'             => esc_html__( 'Closed Toggle Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                      = $this->shortcode_atts['module_id'];
		$module_class                   = $this->shortcode_atts['module_class'];
		$title                          = $this->shortcode_atts['title'];
		$open                           = $this->shortcode_atts['open'];
		$open_toggle_background_color   = $this->shortcode_atts['open_toggle_background_color'];
		$closed_toggle_background_color = $this->shortcode_atts['closed_toggle_background_color'];
		$icon_color                     = $this->shortcode_atts['icon_color'];
		$closed_toggle_text_color       = $this->shortcode_atts['closed_toggle_text_color'];
		$open_toggle_text_color         = $this->shortcode_atts['open_toggle_text_color'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $open_toggle_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_open',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $open_toggle_background_color )
				),
			) );
		}

		if ( '' !== $closed_toggle_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_close',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $closed_toggle_background_color )
				),
			) );
		}

		if ( '' !== $icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_toggle_title:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $icon_color )
				),
			) );
		}

		if ( '' !== $closed_toggle_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_toggle_close h5.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $closed_toggle_text_color )
				),
			) );
		}

		if ( '' !== $open_toggle_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_toggle_open h5.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $open_toggle_text_color )
				),
			) );
		}

		if ( 'et_pb_accordion_item' === $function_name ) {
			global $et_pb_accordion_item_number;

			$open = 1 === $et_pb_accordion_item_number ? 'on' : 'off';

			$et_pb_accordion_item_number++;
		}

		// Adding "_item" class for toggle module for customizer targetting. There's no proper selector
		// for toggle module styles since both accordion and toggle module use the same selector
		if( 'et_pb_toggle' === $function_name ){
			$module_class .= " et_pb_toggle_item";
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$output = sprintf(
			'<div%4$s class="et_pb_module et_pb_toggle %2$s%5$s%6$s%8$s">
				%9$s
				%7$s
				<h5 class="et_pb_toggle_title">%1$s</h5>
				<div class="et_pb_toggle_content clearfix">
					%3$s
				</div> <!-- .et_pb_toggle_content -->
			</div> <!-- .et_pb_toggle -->',
			esc_html( $title ),
			( 'on' === $open ? 'et_pb_toggle_open' : 'et_pb_toggle_close' ),
			$this->shortcode_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Toggle;

class ET_Builder_Module_Contact_Form extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Contact Form', 'et_builder' );
		$this->slug            = 'et_pb_contact_form';
		$this->fb_support      = true;
		$this->child_slug      = 'et_pb_contact_field';
		$this->child_item_text = esc_html__( 'Field', 'et_builder' );

		$this->whitelisted_fields = array(
			'captcha',
			'email',
			'title',
			'admin_label',
			'module_id',
			'module_class',
			'form_background_color',
			'input_border_radius',
			'submit_button_text',
			'custom_message',
			'use_redirect',
			'redirect_url',
			'success_message',
		);

		$this->fields_defaults = array(
			'captcha'      => array( 'on' ),
			'use_redirect' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_contact_form_container';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'email'        => esc_html__( 'Email', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
					'redirect'     => esc_html__( 'Redirect', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h1",
					),
				),
				'form_field'   => array(
					'label'    => esc_html__( 'Form Field', 'et_builder' ),
					'css'      => array(
						'main' => array(
							"{$this->main_css_element} .input",
							"{$this->main_css_element} .input::-webkit-input-placeholder",
							"{$this->main_css_element} .input::-moz-placeholder",
							"{$this->main_css_element} .input:-ms-input-placeholder",
							"{$this->main_css_element} .input[type=checkbox] + label",
							"{$this->main_css_element} .input[type=radio] + label",
						),
						'important' => 'plugin_only',
					),
				),
			),
			'border' => array(
				'css'      => array(
					'main' => sprintf(
						'%1$s .input,
						%1$s .input[type="checkbox"] + label i,
						%1$s .input[type="radio"] + label i',
						$this->main_css_element
					),
					'important' => 'plugin_only',
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element}.et_pb_module .et_pb_button",
					),
				),
			),
		);
		$this->custom_css_options = array(
			'contact_title' => array(
				'label'    => esc_html__( 'Contact Title', 'et_builder' ),
				'selector' => '.et_pb_contact_main_title',
			),
			'contact_button' => array(
				'label'    => esc_html__( 'Contact Button', 'et_builder' ),
				'selector' => '.et_pb_contact_form_container .et_contact_bottom_container .et_pb_contact_submit.et_pb_button',
				'no_space_before_selector' => true,
			),
			'contact_fields' => array(
				'label'    => esc_html__( 'Form Fields', 'et_builder' ),
				'selector' => 'input',
			),
			'text_field' => array(
				'label'    => esc_html__( 'Message Field', 'et_builder' ),
				'selector' => 'textarea.et_pb_contact_message',
			),
			'captcha_field' => array(
				'label'    => esc_html__( 'Captcha Field', 'et_builder' ),
				'selector' => 'input.et_pb_contact_captcha',
			),
			'captcha_label' => array(
				'label'    => esc_html__( 'Captcha Text', 'et_builder' ),
				'selector' => '.et_pb_contact_right p',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'captcha' => array(
				'label'           => esc_html__( 'Display Captcha', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turn the captcha on or off using this option.', 'et_builder' ),
			),
			'email' => array(
				'label'           => esc_html__( 'Email', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => et_get_safe_localization( sprintf(
					__( 'Input the email address where messages should be sent.<br /><br /> Note: email delivery and spam prevention are complex processes. We recommend using a delivery service such as <a href="%1$s">Mandrill</a>, <a href="%2$s">SendGrid</a>, or other similar service to ensure the deliverability of messages that are submitted through this form', 'et_builder' ),
					'http://mandrill.com/',
					'https://sendgrid.com/'
				) ),
				'toggle_slug'     => 'email',
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a title for your contact form.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'custom_message' => array(
				'label'           => esc_html__( 'Message Pattern', 'et_builder' ),
				'type'            => 'textarea',
				'option_category' => 'configuration',
				'description'     => et_get_safe_localization( __( 'Here you can define the custom pattern for the email Message. Fields should be included in following format - <strong>%%field_id%%</strong>. For example if you want to include the field with id = <strong>phone</strong> and field with id = <strong>message</strong>, then you can use the following pattern: <strong>My message is %%message%% and phone number is %%phone%%</strong>. Leave blank for default.', 'et_builder' ) ),
				'toggle_slug'     => 'email',
			),
			'use_redirect' => array(
				'label'           => esc_html__( 'Enable Redirect URL', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects' => array(
					'redirect_url',
				),
				'toggle_slug'     => 'redirect',
				'description'     => esc_html__( 'Redirect users after successful form submission.', 'et_builder' ),
			),
			'redirect_url' => array(
				'label'           => esc_html__( 'Redirect URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'on',
				'toggle_slug'     => 'redirect',
				'description'     => esc_html__( 'Type the Redirect URL', 'et_builder' ),
			),
			'success_message' => array(
				'label'           => esc_html__( 'Success Message', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Type the message you want to display after successful form submission. Leave blank for default', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'submit_button_text' => array(
				'label'           => esc_html__( 'Submit Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the text of the form submit button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'form_background_color' => array(
				'label'             => esc_html__( 'Form Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'input_border_radius'   => array(
				'label'             => esc_html__( 'Input Border Radius', 'et_builder' ),
				'type'              => 'range',
				'default'           => '0',
				'range_settings'    => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'option_category'   => 'layout',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'border',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function predefined_child_modules() {
		$output = sprintf(
			'[et_pb_contact_field field_title="%1$s" field_type="input" field_id="Name" required_mark="on" fullwidth_field="off" /][et_pb_contact_field field_title="%2$s" field_type="email" field_id="Email" required_mark="on" fullwidth_field="off" /][et_pb_contact_field field_title="%3$s" field_type="text" field_id="Message" required_mark="on" fullwidth_field="on" /]',
			esc_attr__( 'Name', 'et_builder' ),
			esc_attr__( 'Email Address', 'et_builder' ),
			esc_attr__( 'Message', 'et_builder' )
		);

		return $output;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id             = $this->shortcode_atts['module_id'];
		$module_class          = $this->shortcode_atts['module_class'];
		$captcha               = $this->shortcode_atts['captcha'];
		$email                 = $this->shortcode_atts['email'];
		$title                 = $this->shortcode_atts['title'];
		$form_field_text_color = $this->shortcode_atts['form_field_text_color'];
		$form_background_color = $this->shortcode_atts['form_background_color'];
		$input_border_radius   = $this->shortcode_atts['input_border_radius'];
		$button_custom         = $this->shortcode_atts['custom_button'];
		$custom_icon           = $this->shortcode_atts['button_icon'];
		$submit_button_text    = $this->shortcode_atts['submit_button_text'];
		$custom_message        = $this->shortcode_atts['custom_message'];
		$use_redirect          = $this->shortcode_atts['use_redirect'];
		$redirect_url          = $this->shortcode_atts['redirect_url'];
		$success_message       = $this->shortcode_atts['success_message'];

		global $et_pb_contact_form_num;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $form_field_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .input[type="checkbox"]:checked + label i:before',
				'declaration' => sprintf(
					'color: %1$s%2$s;',
					esc_html( $form_field_text_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .input[type="radio"]:checked + label i:before',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $form_field_text_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $form_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .input, %%order_class%% .input[type="checkbox"] + label i, %%order_class%% .input[type="radio"] + label i',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $form_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( ! in_array( $input_border_radius, array( '', '0' ) ) ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .input, %%order_class%% .input[type="checkbox"] + label i',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s%2$s; -webkit-border-radius: %1$s%2$s; border-radius: %1$s%2$s;',
					esc_html( et_builder_process_range_value( $input_border_radius ) ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		$success_message = '' !== $success_message ? $success_message : esc_html__( 'Thanks for contacting us', 'et_builder' );

		$et_pb_contact_form_num = $this->shortcode_callback_num();

		$content = $this->shortcode_content;

		$et_error_message = '';
		$et_contact_error = false;
		$current_form_fields = isset( $_POST['et_pb_contact_email_fields_' . $et_pb_contact_form_num] ) ? $_POST['et_pb_contact_email_fields_' . $et_pb_contact_form_num] : '';
		$contact_email = '';
		$processed_fields_values = array();

		$nonce_result = isset( $_POST['_wpnonce-et-pb-contact-form-submitted'] ) && wp_verify_nonce( $_POST['_wpnonce-et-pb-contact-form-submitted'], 'et-pb-contact-form-submit' ) ? true : false;

		// check that the form was submitted and et_pb_contactform_validate field is empty to protect from spam
		if ( $nonce_result && isset( $_POST['et_pb_contactform_submit_' . $et_pb_contact_form_num] ) && empty( $_POST['et_pb_contactform_validate_' . $et_pb_contact_form_num] ) ) {
			if ( '' !== $current_form_fields ) {
				$fields_data_json = str_replace( '\\', '' ,  $current_form_fields );
				$fields_data_array = json_decode( $fields_data_json, true );

				// check whether captcha field is not empty
				if ( 'on' === $captcha && ( ! isset( $_POST['et_pb_contact_captcha_' . $et_pb_contact_form_num] ) || empty( $_POST['et_pb_contact_captcha_' . $et_pb_contact_form_num] ) ) ) {
					$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you entered the captcha.', 'et_builder' ) );
					$et_contact_error = true;
				}

				// check all fields on current form and generate error message if needed
				if ( ! empty( $fields_data_array ) ) {
					foreach( $fields_data_array as $index => $value ) {
						// check all the required fields, generate error message if required field is empty
						if ( 'required' === $value['required_mark'] && empty( $_POST[ $value['field_id'] ] ) ) {
							$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you fill in all required fields.', 'et_builder' ) );
							$et_contact_error = true;
							continue;
						}

						// additional check for email field
						if ( 'email' === $value['field_type'] && 'required' === $value['required_mark'] && ! empty( $_POST[ $value['field_id'] ] ) ) {
							$contact_email = sanitize_email( $_POST[ $value['field_id'] ] );
							if ( ! is_email( $contact_email ) ) {
								$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Invalid Email.', 'et_builder' ) );
								$et_contact_error = true;
							}
						}

						// prepare the array of processed field values in convenient format
						if ( false === $et_contact_error ) {
							$processed_fields_values[ $value['original_id'] ]['value'] = isset( $_POST[ $value['field_id'] ] ) ? $_POST[ $value['field_id'] ] : '';
							$processed_fields_values[ $value['original_id'] ]['label'] = $value['field_label'];
						}
					}
				}
			} else {
				$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you fill in all required fields.', 'et_builder' ) );
				$et_contact_error = true;
			}
		} else {
			if ( false === $nonce_result && isset( $_POST['et_pb_contactform_submit_' . $et_pb_contact_form_num] ) && empty( $_POST['et_pb_contactform_validate_' . $et_pb_contact_form_num] ) ) {
				$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Please refresh the page and try again.', 'et_builder' ) );
			}
			$et_contact_error = true;
		}

		// generate digits for captcha
		$et_pb_first_digit = rand( 1, 15 );
		$et_pb_second_digit = rand( 1, 15 );

		if ( ! $et_contact_error && $nonce_result ) {
			$et_email_to = '' !== $email
				? $email
				: get_site_option( 'admin_email' );

			$et_site_name = get_option( 'blogname' );

			$contact_name = isset( $processed_fields_values['name'] ) ? stripslashes( sanitize_text_field( $processed_fields_values['name']['value'] ) ) : '';

			if ( '' !== $custom_message ) {
				$message_pattern = et_builder_convert_line_breaks( $custom_message, "\r\n" );

				// insert the data from contact form into the message pattern
				foreach ( $processed_fields_values as $key => $value ) {
					$message_pattern = str_ireplace( "%%{$key}%%", $value['value'], $message_pattern );
				}
			} else {
				// use default message pattern if custom pattern is not defined
				$message_pattern = isset( $processed_fields_values['message']['value'] ) ? $processed_fields_values['message']['value'] : '';

				// Add all custom fields into the message body by default
				foreach ( $processed_fields_values as $key => $value ) {
					if ( ! in_array( $key, array( 'message', 'name', 'email' ) ) ) {
						$message_pattern .= "\r\n";
						$message_pattern .= sprintf(
							'%1$s: %2$s',
							'' !== $value['label'] ? $value['label'] : $key,
							$value['value']
						);
					}
				}
			}

			$http_host = str_replace( 'www.', '', $_SERVER['HTTP_HOST'] );

			$headers[] = "From: \"{$contact_name}\" <mail@{$http_host}>";
			$headers[] = "Reply-To: \"{$contact_name}\" <{$contact_email}>";

			add_filter( 'et_get_safe_localization', 'et_allow_ampersand' );

			$email_message = trim( stripslashes( wp_strip_all_tags( $message_pattern ) ) );

			wp_mail( apply_filters( 'et_contact_page_email_to', $et_email_to ),
				et_get_safe_localization( sprintf(
					__( 'New Message From %1$s%2$s', 'et_builder' ),
					sanitize_text_field( html_entity_decode( $et_site_name ) ),
					( '' !== $title ? sprintf( _x( ' - %s', 'contact form title separator', 'et_builder' ), sanitize_text_field( html_entity_decode( $title ) ) ) : '' )
				) ),
				! empty( $email_message ) ? $email_message : ' ',
				apply_filters( 'et_contact_page_headers', $headers, $contact_name, $contact_email )
			);

			remove_filter( 'et_get_safe_localization', 'et_allow_ampersand' );

			$et_error_message = sprintf( '<p>%1$s</p>', esc_html( $success_message ) );
		}

		$form = '';

		$et_pb_captcha = sprintf( '
			<div class="et_pb_contact_right">
				<p class="clearfix">
					<span class="et_pb_contact_captcha_question">%1$s</span> = <input type="text" size="2" class="input et_pb_contact_captcha" data-first_digit="%3$s" data-second_digit="%4$s" value="" name="et_pb_contact_captcha_%2$s" data-required_mark="required">
				</p>
			</div> <!-- .et_pb_contact_right -->',
			sprintf( '%1$s + %2$s', esc_html( $et_pb_first_digit ), esc_html( $et_pb_second_digit ) ),
			esc_attr( $et_pb_contact_form_num ),
			esc_attr( $et_pb_first_digit ),
			esc_attr( $et_pb_second_digit )
		);

		if ( '' === trim( $content ) ) {
			$content = do_shortcode( $this->predefined_child_modules() );
		}

		if ( $et_contact_error ) {
			// Make sure submit button text is not just a space
			$submit_button_text = trim( $submit_button_text );

			// We can't use `empty( trim() )` because that throws
			// an error on old(er) PHP versions
			if ( empty( $submit_button_text ) ) {
				$submit_button_text = __( 'Submit', 'et_builder' );
			}

			$form = sprintf( '
				<div class="et_pb_contact">
					<form class="et_pb_contact_form clearfix" method="post" action="%1$s">
						%8$s
						<input type="hidden" value="et_contact_proccess" name="et_pb_contactform_submit_%7$s">
						<input type="text" value="" name="et_pb_contactform_validate_%7$s" class="et_pb_contactform_validate_field" />
						<div class="et_contact_bottom_container">
							%2$s
							<button type="submit" class="et_pb_contact_submit et_pb_button%6$s"%5$s>%3$s</button>
						</div>
						%4$s
					</form>
				</div> <!-- .et_pb_contact -->',
				esc_url( get_permalink( get_the_ID() ) ),
				(  'on' === $captcha ? $et_pb_captcha : '' ),
				esc_html( $submit_button_text ),
				wp_nonce_field( 'et-pb-contact-form-submit', '_wpnonce-et-pb-contact-form-submitted', true, false ),
				'' !== $custom_icon && 'on' === $button_custom ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon ) )
				) : '',
				'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : '',
				esc_attr( $et_pb_contact_form_num ),
				$content
			);
		}

		$output = sprintf( '
			<div id="%4$s" class="et_pb_module et_pb_contact_form_container clearfix%5$s" data-form_unique_num="%6$s"%7$s>
				%1$s
				<div class="et-pb-contact-message">%2$s</div>
				%3$s
			</div> <!-- .et_pb_contact_form_container -->
			',
			( '' !== $title ? sprintf( '<h1 class="et_pb_contact_main_title">%1$s</h1>', esc_html( $title ) ) : '' ),
			'' !== $et_error_message ? $et_error_message : '',
			$form,
			( '' !== $module_id
				? esc_attr( $module_id )
				: esc_attr( 'et_pb_contact_form_' . $et_pb_contact_form_num )
			),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $et_pb_contact_form_num ),
			'on' === $use_redirect && '' !== $redirect_url ? sprintf( ' data-redirect_url="%1$s"', esc_attr( $redirect_url ) ) : ''
		);

		return $output;
	}
}
new ET_Builder_Module_Contact_Form;

class ET_Builder_Module_Contact_Form_Item extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Field', 'et_builder' );
		$this->slug            = 'et_pb_contact_field';
		$this->fb_support      = true;
		$this->type            = 'child';
		$this->child_title_var = 'field_id';

		$this->whitelisted_fields = array(
			'field_title',
			'field_type',
			'field_id',
			'required_mark',
			'fullwidth_field',
			'input_border_radius',
			'field_background_color',
			'checkbox_checked',
			'radio_options',
			'select_options',
			'conditional_logic',
			'conditional_logic_relation',
			'conditional_logic_rules',
			'min_length',
			'max_length',
			'allowed_symbols',
		);

		$this->fields_defaults = array(
			'field_title'                => array( esc_html__( 'New Field', 'et_builder' ) ),
			'field_type'                 => array( 'input' ),
			'field_id'                   => array( '' ),
			'fullwidth_field'            => array( 'off' ),
			'required_mark'              => array( 'on' ),
			'checkbox_checked'           => array( 'off' ),
			'conditional_logic'          => array( 'off' ),
			'conditional_logic_relation' => array( 'off' ),
			'min_length'                 => array( '0' ),
			'max_length'                 => array( '0' ),
			'allowed_symbols'            => array( 'all' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Field', 'et_builder' );
		$this->settings_text               = esc_html__( 'Field Settings', 'et_builder' );
		$this->main_css_element = '%%order_class%%.et_pb_contact_field .input';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'      => esc_html__( 'Text', 'et_builder' ),
					'field_options'     => esc_html__( 'Field Options', 'et_builder' ),
					'conditional_logic' => esc_html__( 'Conditional Logic', 'et_builder' ),
					'background'        => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'form_field'   => array(
					'label'    => esc_html__( 'Field', 'et_builder' ),
					'css'      => array(
						'main' => array(
							"{$this->main_css_element}",
							"{$this->main_css_element}::-webkit-input-placeholder",
							"{$this->main_css_element}::-moz-placeholder",
							"{$this->main_css_element}:-ms-input-placeholder",
							"{$this->main_css_element}[type=checkbox] + label",
							"{$this->main_css_element}[type=radio] + label",
						),
						'important' => 'plugin_only',
					),
				),
			),
			'border' => array(
				'css'      => array(
					'main' => sprintf(
						'%1$s,
						%1$s[type="checkbox"] + label i,
						%1$s[type="radio"] + label i',
						$this->main_css_element
					),
					'important' => 'plugin_only',
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'field_id' => array(
				'label'       => esc_html__( 'Field ID', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Define the unique ID of this field. You should use only English characters without special characters and spaces.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'field_title' => array(
				'label'       => esc_html__( 'Title', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'field_type' => array(
				'label'       => esc_html__( 'Type', 'et_builder' ),
				'type'        => 'select',
				'default'     => 'input',
				'option_category' => 'basic_option',
				'options'         => array(
					'input'    => esc_html__( 'Input Field', 'et_builder' ),
					'email'    => esc_html__( 'Email Field', 'et_builder' ),
					'text'     => esc_html__( 'Textarea', 'et_builder' ),
					'checkbox' => esc_html__( 'Checkbox', 'et_builder' ),
					'radio'    => esc_html__( 'Radio Buttons', 'et_builder' ),
					'select'   => esc_html__( 'Select Dropdown', 'et_builder' ),
				),
				'description' => esc_html__( 'Choose the type of field', 'et_builder' ),
				'affects'     => array(
					'checkbox_checked',
					'radio_options',
					'select_options',
					'min_length',
					'max_length',
					'allowed_symbols',
				),
				'toggle_slug' => 'field_options',
			),
			'checkbox_checked' => array(
				'label'           => esc_html__( 'Checked By Default', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'default'         => 'off',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if' => 'checkbox',
				'toggle_slug'     => 'field_options',
			),
			'radio_options' => array(
				'label'           => esc_html__( 'Options', 'et_builder' ),
				'type'            => 'options_list',
				'radio'           => true,
				'option_category' => 'basic_option',
				'depends_show_if' => 'radio',
				'toggle_slug'     => 'field_options',
			),
			'select_options' => array(
				'label'           => esc_html__( 'Options', 'et_builder' ),
				'type'            => 'options_list',
				'option_category' => 'basic_option',
				'depends_show_if' => 'select',
				'toggle_slug'     => 'field_options',
			),
			'min_length'   => array(
				'label'          => esc_html__( 'Minimum Length', 'et_builder' ),
				'description'    => esc_html__( 'Leave at 0 to remove restriction', 'et_builder' ),
				'type'           => 'range',
				'default'        => '0',
				'fixed_unit'     => null,
				'range_settings' => array(
					'min'  => '0',
					'max'  => '255',
					'step' => '1',
				),
				'option_category' => 'basic_option',
				'depends_show_if' => 'input',
				'toggle_slug'     => 'field_options',
			),
			'max_length'   => array(
				'label'          => esc_html__( 'Maximum Length', 'et_builder' ),
				'description'    => esc_html__( 'Leave at 0 to remove restriction', 'et_builder' ),
				'type'           => 'range',
				'default'        => '0',
				'fixed_unit'     => null,
				'range_settings' => array(
					'min'  => '0',
					'max'  => '255',
					'step' => '1',
				),
				'option_category' => 'basic_option',
				'depends_show_if' => 'input',
				'toggle_slug'     => 'field_options',
			),
			'allowed_symbols' => array(
				'label'       => esc_html__( 'Allowed Symbols', 'et_builder' ),
				'type'        => 'select',
				'default'     => 'all',
				'options'     => array(
					'all'          => esc_html__( 'All', 'et_builder' ),
					'letters'      => esc_html__( 'Letters Only (A-Z)', 'et_builder' ),
					'numbers'      => esc_html__( 'Numbers Only (0-9)', 'et_builder' ),
					'alphanumeric' => esc_html__( 'Alphanumeric Only (A-Z, 0-9)', 'et_builder' ),
				),
				'option_category' => 'basic_option',
				'depends_show_if' => 'input',
				'toggle_slug'     => 'field_options',
			),
			'required_mark' => array(
				'label'           => esc_html__( 'Required Field', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'default'         => 'on',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'     => esc_html__( 'Define whether the field should be required or optional', 'et_builder' ),
				'toggle_slug'     => 'field_options',
			),
			'fullwidth_field' => array(
				'label'           => esc_html__( 'Make Fullwidth', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'description'     => esc_html__( 'If enabled, the field will take 100% of the width of the content area, otherwise it will take 50%', 'et_builder' ),
			),
			'conditional_logic' => array(
				'label'           => esc_html__( 'Enable', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'default'         => 'off',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'         => array(
					'conditional_logic_rules',
					'conditional_logic_relation',
				),
				'description' => et_get_safe_localization( __( "Enabling conditional logic makes this field only visible when any or all of the rules below are fulfilled<br><strong>Note:</strong> Only fields with an unique and non-empty field ID can be used", 'et_builder' ) ),
				'toggle_slug' => 'conditional_logic',
			),
			'conditional_logic_relation' => array(
				'label'             => esc_html__( 'Relation', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'All', 'et_builder' ),
					'off' => esc_html__( 'Any', 'et_builder' ),
				),
				'default'           => 'off',
				'button_options'    => array(
					'button_type' => 'equal',
				),
				'depends_show_if' => 'on',
				'description' => esc_html__( 'Choose whether any or all of the rules should be fulfilled', 'et_builder' ),
				'toggle_slug' => 'conditional_logic',
			),
			'conditional_logic_rules' => array(
				'label'           => esc_html__( 'Rules', 'et_builder' ),
				'type'            => 'conditional_logic',
				'option_category' => 'layout',
				'depends_show_if' => 'on',
				'toggle_slug'     => 'conditional_logic',
			),
			'field_background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
			),
			'input_border_radius'   => array(
				'label'             => esc_html__( 'Border Radius', 'et_builder' ),
				'type'              => 'range',
				'default'           => '0',
				'range_settings'    => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'option_category'   => 'layout',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'border',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$field_title                = $this->shortcode_atts['field_title'];
		$field_type                 = $this->shortcode_atts['field_type'];
		$field_id                   = $this->shortcode_atts['field_id'];
		$required_mark              = $this->shortcode_atts['required_mark'];
		$fullwidth_field            = $this->shortcode_atts['fullwidth_field'];
		$form_field_text_color      = $this->shortcode_atts['form_field_text_color'];
		$field_background_color     = $this->shortcode_atts['field_background_color'];
		$input_border_radius        = $this->shortcode_atts['input_border_radius'];
		$checkbox_checked           = $this->shortcode_atts['checkbox_checked'];
		$radio_options              = $this->shortcode_atts['radio_options'];
		$select_options             = $this->shortcode_atts['select_options'];
		$min_length                 = $this->shortcode_atts['min_length'];
		$max_length                 = $this->shortcode_atts['max_length'];
		$conditional_logic          = $this->shortcode_atts['conditional_logic'];
		$conditional_logic_relation = $this->shortcode_atts['conditional_logic_relation'];
		$conditional_logic_rules    = $this->shortcode_atts['conditional_logic_rules'];
		$allowed_symbols            = $this->shortcode_atts['allowed_symbols'];

		global $et_pb_contact_form_num;

		// do not output the fields with empty ID
		if ( '' === $field_id ) {
			return;
		}

		$field_id = strtolower( $field_id );

		$current_module_num = '' === $et_pb_contact_form_num ? 0 : intval( $et_pb_contact_form_num ) + 1;

		$module_class = ET_Builder_Element::add_module_order_class( '', $function_name );

		$this->half_width_counter = ! isset( $this->half_width_counter ) ? 0 : $this->half_width_counter;

		// count fields to add the et_pb_contact_field_last properly
		if ( 'off' === $fullwidth_field ) {
			$this->half_width_counter++;
		} else {
			$this->half_width_counter = 0;
		}

		$input_field = '';

		if ( '' !== $form_field_text_color ) {
			if ( 'checkbox' === $field_type ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .input + label, %%order_class%% .input + label i:before',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $form_field_text_color )
					),
				) );
			}

			if ( 'radio' === $field_type ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .input + label',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $form_field_text_color )
					),
				) );

				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .input + label i:before',
					'declaration' => sprintf(
						'background-color: %1$s !important;',
						esc_html( $form_field_text_color )
					),
				) );
			}
		}

		if ( '' !== $field_background_color ) {
			$input_selector = '%%order_class%% .input';

			if ( in_array( $field_type, array( 'checkbox', 'radio' ) ) ) {
				$input_selector = '%%order_class%% .input + label i';
			}

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => $input_selector,
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $field_background_color )
				),
			) );
		}

		if ( ! in_array( $input_border_radius, array( '', '0' ) ) ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input.input, %%order_class%% input.input[type="checkbox"] + label i',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s%2$s; -webkit-border-radius: %1$s%2$s; border-radius: %1$s%2$s;',
					esc_html( et_builder_process_range_value( $input_border_radius ) ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		$pattern         = '';
		$title           = '';
		$min_length      = intval( $min_length );
		$max_length      = intval( $max_length );
		$max_length_attr = '';
		$symbols_pattern = '.';
		$length_pattern  = '*';

		if ( in_array( $allowed_symbols, array( 'letters', 'numbers', 'alphanumeric' ) ) ) {
			switch ( $allowed_symbols ) {
				case 'letters':
					$symbols_pattern = '[A-Z|a-z]';
					$title           = __( 'Only letters allowed.', 'et_builder' );
					break;
				case 'numbers':
					$symbols_pattern = '[0-9]';
					$title           = __( 'Only numbers allowed.', 'et_builder' );
					break;
				case 'alphanumeric':
					$symbols_pattern = '[A-Z|a-z|0-9]';
					$title           = __( 'Only letters and numbers allowed.', 'et_builder' );
					break;
			}
		}

		if ( 0 !== $min_length && 0 !== $max_length ) {
			$max_length = max( $min_length, $max_length );
			$min_length = min( $min_length, $max_length );

			if ( $max_length > 0 ) {
				$max_length_attr = sprintf(
					' maxlength="%1$d"',
					$max_length
				);
			}
		}

		if ( 0 !== $min_length || 0 !== $max_length ) {
			$length_pattern = '{';

			if ( 0 !== $min_length ) {
				$length_pattern .= $min_length;
				$title   .= sprintf( __( 'Minimum length: %1$d characters. ', 'et_builder' ), $min_length );
			}

			if ( 0 === $max_length ) {
				$length_pattern .= ',';
			}

			if ( 0 === $min_length ) {
				$length_pattern .= '0';
			}

			if ( 0 !== $max_length ) {
				$length_pattern .= ",{$max_length}";
				$title   .= sprintf( __( 'Maximum length: %1$d characters.', 'et_builder' ), $max_length );
			}


			$length_pattern .= '}';
		}

		if ( '.' !== $symbols_pattern || '*' !== $length_pattern ) {
			$pattern = sprintf(
				' pattern="%1$s%2$s"',
				esc_attr( $symbols_pattern ),
				esc_attr( $length_pattern )
			);
		}

		if ( '' !== $title ) {
			$title = sprintf(
				' title="%1$s"',
				esc_attr( $title )
			);
		}

		$conditional_logic_attr = '';

		if ( 'on' === $conditional_logic && ! empty( $conditional_logic_rules ) ) {
			$option_search           = array( '&#91;', '&#93;' );
			$option_replace          = array( '[', ']' );
			$conditional_logic_rules = str_replace( $option_search, $option_replace, $conditional_logic_rules );
			$condition_rows          = json_decode( $conditional_logic_rules );
			$ruleset                 = array();

			foreach ( $condition_rows as $condition_row ) {
				$condition_value = isset( $condition_row->value ) ? $condition_row->value : '';

				$ruleset[] = array(
					$condition_row->field,
					$condition_row->condition,
					$condition_value,
				);
			}

			if ( ! empty( $ruleset ) ) {
				$json     = json_encode( $ruleset );
				$relation = $conditional_logic_relation === 'off' ? 'any' : 'all';

				$conditional_logic_attr = sprintf(
					' data-conditional-logic="%1$s" data-conditional-relation="%2$s"',
					esc_attr( $json ),
					$relation
				);
			}
		}

		switch( $field_type ) {
			case 'text':
				$input_field = sprintf(
					'<textarea name="et_pb_contact_%3$s_%2$s" id="et_pb_contact_%3$s_%2$s" class="et_pb_contact_message input" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s" placeholder="%5$s">%1$s</textarea>',
					( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_html( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
					esc_attr( $current_module_num ),
					esc_attr( $field_id ),
					esc_attr( $field_type ),
					esc_attr( $field_title ),
					'off' === $required_mark ? 'not_required' : 'required'
				);
				break;
			case 'input' :
			case 'email' :
				if ( 'email' === $field_type ) {
					$pattern = '';
				}

				$input_field = sprintf(
					'<input type="text" id="et_pb_contact_%3$s_%2$s" class="input" value="%1$s" name="et_pb_contact_%3$s_%2$s" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s" placeholder="%5$s"%7$s%8$s%9$s>',
					( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_attr( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
					esc_attr( $current_module_num ),
					esc_attr( $field_id ),
					esc_attr( $field_type ),
					esc_attr( $field_title ),
					'off' === $required_mark ? 'not_required' : 'required',
					$pattern,
					$title,
					$max_length_attr
				);
				break;
			case 'checkbox' :
				$input_field = sprintf(
					'<input type="checkbox" id="et_pb_contact_%3$s_%2$s_checkbox" class="input" value="%8$s" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s"%9$s>
					<label for="et_pb_contact_%3$s_%2$s_checkbox"><i></i>%5$s</label>
					<input id="et_pb_contact_%3$s_%2$s" name="et_pb_contact_%3$s_%2$s" type="text" value="%8$s" data-checked="%7$s" data-unchecked="%8$s">',
					( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_attr( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
					esc_attr( $current_module_num ),
					esc_attr( $field_id ),
					esc_attr( $field_type ),
					esc_attr( $field_title ),
					'off' === $required_mark ? 'not_required' : 'required',
					esc_attr__( 'checked', 'et_builder' ),
					esc_attr__( 'not checked', 'et_builder' ),
					'on' === $checkbox_checked ? 'checked="checked"' : ''
				);
				break;
			case 'radio' :
				$input_field = '';

				if ( $radio_options ) {
					$option_search  = array( '&#91;', '&#93;' );
					$option_replace = array( '[', ']' );
					$radio_options  = str_replace( $option_search, $option_replace, $radio_options );
					$radio_options  = json_decode( $radio_options );

					foreach ( $radio_options as $index => $option ) {
						$is_checked = 1 === $option->checked ? true : false;

						$input_field .= sprintf(
							'<span class="et_pb_contact_field_radio">
								<input type="radio" id="et_pb_contact_%3$s_%2$s_%7$s" class="input" value="%8$s" name="et_pb_contact_%3$s_%2$s" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s" %9$s>
								<label for="et_pb_contact_%3$s_%2$s_%7$s"><i></i>%8$s</label>
							</span>',
							( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_attr( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
							esc_attr( $current_module_num ),
							esc_attr( $field_id ),
							esc_attr( $field_type ),
							esc_attr( $field_title ),
							'off' === $required_mark ? 'not_required' : 'required',
							esc_attr( $index ),
							wp_strip_all_tags( $option->value ),
							checked( $is_checked, true, false )
						);
					}
				} else {
					$input_field .= esc_html__( 'No options added.', 'et_builder' );
				}

				$input_field = sprintf(
					'<span class="et_pb_contact_field_radio_wrapper">
						<span class="et_pb_contact_field_radio_title">%1$s</span>
						<span class="et_pb_contact_field_radio_list">%2$s</span>
					</span>',
					esc_html( $field_title ),
					$input_field
				);

				break;
			case 'select' :
				$options = sprintf(
					'<option value="">-- %1$s --</option>',
					esc_attr( $field_title )
				);

				if ( $select_options ) {
					$option_search  = array( '&#91;', '&#93;' );
					$option_replace = array( '[', ']' );
					$select_options = str_replace( $option_search, $option_replace, $select_options );
					$select_options = json_decode( $select_options );

					foreach ( $select_options as $option ) {
						$options .= sprintf(
							'<option value="%1$s">%2$s</option>',
							esc_attr( wp_strip_all_tags( $option->value ) ),
							wp_strip_all_tags( $option->value )
						);
					}
				}

				$input_field = sprintf(
					'<select id="et_pb_contact_%3$s_%2$s" class="et_pb_contact_select input" name="et_pb_contact_%3$s_%2$s" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s">
						%7$s
					</select>',
					( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_attr( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
					esc_attr( $current_module_num ),
					esc_attr( $field_id ),
					esc_attr( $field_type ),
					esc_attr( $field_title ),
					'off' === $required_mark ? 'not_required' : 'required',
					$options
				);
				break;
		}

		$output = sprintf(
			'<p class="et_pb_contact_field%5$s%6$s%7$s"%8$s data-id="%3$s" data-type="%9$s">
				<label for="et_pb_contact_%3$s_%2$s" class="et_pb_contact_form_label">%1$s</label>
				%4$s
			</p>',
			esc_html( $field_title ),
			esc_attr( $current_module_num ),
			esc_attr( $field_id ),
			$input_field,
			esc_attr( $module_class ),
			'off' === $fullwidth_field ? ' et_pb_contact_field_half' : '',
			0 === $this->half_width_counter % 2 ? ' et_pb_contact_field_last' : '',
			$conditional_logic_attr,
			$field_type
		);

		return $output;
	}
}
new ET_Builder_Module_Contact_Form_Item;

class ET_Builder_Module_Sidebar extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Sidebar', 'et_builder' );
		$this->slug       = 'et_pb_sidebar';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'orientation',
			'area',
			'background_layout',
			'admin_label',
			'module_id',
			'module_class',
			'remove_border',
		);

		$this->fields_defaults = array(
			'orientation'       => array( 'left' ),
			'background_layout' => array( 'light' ),
			'remove_border'     => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_widget_area';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'     => esc_html__( 'Layout', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h3:first-of-type, {$this->main_css_element} h4:first-of-type, {$this->main_css_element} h5:first-of-type, {$this->main_css_element} h6:first-of-type, {$this->main_css_element} h2:first-of-type, {$this->main_css_element} h1:first-of-type, {$this->main_css_element} .widget-title, {$this->main_css_element} .widgettitle",
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element}, {$this->main_css_element} li, {$this->main_css_element} li:before, {$this->main_css_element} a",
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
		);
		$this->custom_css_options = array(
			'widget' => array(
				'label'    => esc_html__( 'Widget', 'et_builder' ),
				'selector' => '.et_pb_widget',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => 'h4.widgettitle',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'orientation' => array(
				'label'             => esc_html__( 'Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'left'  => esc_html__( 'Left', 'et_builder' ),
					'right' => esc_html__( 'Right', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'layout',
				'description'       => esc_html__( 'Choose which side of the page your sidebar will be on. This setting controls text orientation and border position.', 'et_builder' ),
			),
			'area' => array(
				'label'            => esc_html__( 'Widget Area', 'et_builder' ),
				'renderer'         => 'et_builder_get_widget_areas',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select a widget-area that you would like to display. You can create new widget areas within the Appearances > Widgets tab.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__sidebars',
				),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'remove_border' => array(
				'label'           => esc_html__( 'Remove Border Separator', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__sidebars'          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Sidebar', 'get_sidebar' ),
				'computed_depends_on' => array(
					'area',
				),
			),
		);
		return $fields;
	}

	static function get_default_area() {
		global $wp_registered_sidebars;

		if ( ! empty( $wp_registered_sidebars ) ) {
			// Pluck sidebar ids
			$sidebar_ids = wp_list_pluck( $wp_registered_sidebars, 'id' );

			// Return first sidebar id
			return array_shift( $sidebar_ids );
		}

		return "";
	}

	/**
	 * Get sidebar data for sidebar module
	 *
	 * @param string comma separated gallery ID
	 * @param string on|off to determine grid / slider layout
	 * @param array  passed current page params
	 *
	 * @return string JSON encoded array of attachments data
	 */
	static function get_sidebar( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'area' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get any available widget areas so it isn't empty
		if ( '' === $args['area'] ) {
			$args['area'] = self::get_default_area();
		}

		// Outputs sidebar
		$widgets = '';

		ob_start();

		if ( is_active_sidebar( $args['area'] ) ) {
			dynamic_sidebar( $args['area'] );
		}

		$widgets = ob_get_contents();

		ob_end_clean();

		return $widgets;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$orientation       = $this->shortcode_atts['orientation'];
		$area              = "" === $this->shortcode_atts['area'] ? self::get_default_area() : $this->shortcode_atts['area'];
		$background_layout = $this->shortcode_atts['background_layout'];
		$remove_border     = $this->shortcode_atts['remove_border'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$widgets = '';

		ob_start();

		if ( 'on' === $remove_border ) {
			$module_class = rtrim( $module_class ) . ' et_pb_sidebar_no_border';
		}

		if ( is_active_sidebar( $area ) )
			dynamic_sidebar( $area );

		$widgets = ob_get_contents();

		ob_end_clean();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$output = sprintf(
			'<div%4$s class="et_pb_widget_area %2$s clearfix%3$s%5$s">
				%1$s
			</div> <!-- .et_pb_widget_area -->',
			$widgets,
			esc_attr( "et_pb_widget_area_{$orientation}" ),
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Sidebar;

class ET_Builder_Module_Divider extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Divider', 'et_builder' );
		$this->slug       = 'et_pb_divider';
		$this->fb_support = true;

		$style_option_name = sprintf( '%1$s-divider_style', $this->slug );
		$global_divider_style = ET_Global_Settings::get_value( $style_option_name );
		$position_option_name = sprintf( '%1$s-divider_position', $this->slug );
		$global_divider_position = ET_Global_Settings::get_value( $position_option_name );
		$weight_option_name = sprintf( '%1$s-divider_weight', $this->slug );
		$global_divider_weight = ET_Global_Settings::get_value( $weight_option_name );

		$this->defaults = array(
			'divider_style'    => $global_divider_style && '' !== $global_divider_style ? $global_divider_style : 'solid',
			'divider_position' => $global_divider_position && '' !== $global_divider_position ? $global_divider_position : 'top',
			'divider_weight'   => $global_divider_weight && '' !== $global_divider_weight ? $global_divider_weight : '1px',
		);

		// Show divider options is modifieable via customizer
		$this->show_divider_options = array(
			'off' => esc_html__( "Don't Show Divider", 'et_builder' ),
			'on'  => esc_html__( 'Show Divider', 'et_builder' ),
		);

		if ( ! et_is_builder_plugin_active() && true === et_get_option( 'et_pb_divider-show_divider', false ) ) {
			$this->show_divider_options = array_reverse( $this->show_divider_options );
			$show_divider_default = 'on';
		} else {
			$show_divider_default = 'off';
		}

		$this->whitelisted_fields = array(
			'color',
			'show_divider',
			'height',
			'admin_label',
			'module_id',
			'module_class',
			'divider_style',
			'divider_position',
			'divider_weight',
			'hide_on_mobile',
		);

		$this->options_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Visibility', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'color'  => esc_html__( 'Color', 'et_builder' ),
					'styles' => esc_html__( 'Styles', 'et_builder' ),
					'height' => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->fields_defaults = array(
			'color'          => array( '#ffffff', 'only_default_setting' ),
			'show_divider'   => array( $show_divider_default ),
			'hide_on_mobile' => array( 'on' ),
		);
	}

	function get_fields() {
		$fields = array(
			'color' => array(
				'label'           => esc_html__( 'Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'tab_slug'        => 'advanced',
				'description'     => esc_html__( 'This will adjust the color of the 1px divider line.', 'et_builder' ),
				'depends_show_if' => 'on',
				'toggle_slug'     => 'color',
			),
			'show_divider' => array(
				'label'             => esc_html__( 'Visibility', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => $this->show_divider_options,
				'affects' => array(
					'divider_style',
					'divider_position',
					'divider_weight',
					'color',
				),
				'toggle_slug'       => 'main_content',
				'description'       => esc_html__( 'This settings turns on and off the 1px divider line, but does not affect the divider height.', 'et_builder' ),
			),
			'height' => array(
				'label'           => esc_html__( 'Height', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'height',
				'description'     => esc_html__( 'Define how much space should be added below the divider.', 'et_builder' ),
			),
			'divider_style' => array(
				'label'             => esc_html__( 'Divider Style', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_border_styles(),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'styles',
			),
			'divider_position' => array(
				'label'           => esc_html__( 'Divider Position', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'top'    => esc_html__( 'Top', 'et_builder' ),
					'center' => esc_html__( 'Vertically Centered', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'styles',
			),
			'divider_weight' => array(
				'label'             => esc_html__( 'Divider Weight', 'et_builder' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'styles',
			),
			'hide_on_mobile' => array(
				'label'             => esc_html__( 'Hide On Mobile', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'visibility',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id        = $this->shortcode_atts['module_id'];
		$module_class     = $this->shortcode_atts['module_class'];
		$color            = $this->shortcode_atts['color'];
		$show_divider     = $this->shortcode_atts['show_divider'];
		$height           = $this->shortcode_atts['height'];
		$divider_style    = $this->shortcode_atts['divider_style'];
		$divider_position = $this->shortcode_atts['divider_position'];
		$divider_position_customizer = ! et_is_builder_plugin_active() ? et_get_option( 'et_pb_divider-divider_position', 'top' ) : 'top';
		$divider_weight   = $this->shortcode_atts['divider_weight'];
		$hide_on_mobile   = $this->shortcode_atts['hide_on_mobile'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$style = '';

		if ( '' !== $color && 'on' === $show_divider ) {
			$style .= sprintf( ' border-top-color: %s;',
				esc_attr( $color )
			);

			if ( '' !== $divider_style && $this->defaults['divider_style'] !== $divider_style ) {
				$style .= sprintf( ' border-top-style: %s;',
					esc_attr( $divider_style )
				);
			}

			if ( '' !== $divider_weight && $this->defaults['divider_weight'] !== $divider_weight ) {
				$divider_weight_processed = false === strpos( $divider_weight, 'px' ) ? $divider_weight . 'px' : $divider_weight;

				$style .= sprintf( ' border-top-width: %1$s;',
					esc_attr( $divider_weight_processed )
				);
			}

			if ( '' !== $style ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%:before',
					'declaration' => ltrim( $style )
				) );
			}

			if ( $this->defaults['divider_position'] !== $divider_position ) {
				$module_class .= " et_pb_divider_position_{$divider_position}";
			} elseif ( $this->defaults['divider_position'] !== $divider_position_customizer ) {
				$module_class .= " et_pb_divider_position_{$divider_position_customizer} customized_et_pb_divider_position";
			}
		}

		if ( '' !== $height ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'height: %s;',
					esc_attr( et_builder_process_range_value( $height ) )
				),
			) );
		}

		if ( 'on' === $hide_on_mobile ) {
			$module_class .= ' ' . self::HIDE_ON_MOBILE;
		}

		$output = sprintf(
			'<div%2$s class="et_pb_module et_pb_space%1$s%3$s"><div class="et_pb_divider_internal"></div></div>',
			( 'on' === $show_divider ? ' et_pb_divider' : ' et_pb_divider_hidden' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Divider;

class ET_Builder_Module_Team_Member extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Person', 'et_builder' );
		$this->slug       = 'et_pb_team_member';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'name',
			'position',
			'image_url',
			'animation',
			'background_layout',
			'facebook_url',
			'twitter_url',
			'google_url',
			'linkedin_url',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'icon_color',
			'icon_hover_color',
		);

		$this->fields_defaults = array(
			'animation'         => array( 'off' ),
			'background_layout' => array( 'light' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_team_member';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'image'        => esc_html__( 'Image', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon'       => esc_html__( 'Icon', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h4",
						'important' => 'plugin_only',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} *",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);
		$this->custom_css_options = array(
			'member_image' => array(
				'label'    => esc_html__( 'Member Image', 'et_builder' ),
				'selector' => '.et_pb_team_member_image',
			),
			'member_description' => array(
				'label'    => esc_html__( 'Member Description', 'et_builder' ),
				'selector' => '.et_pb_team_member_description',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => '.et_pb_team_member_description h4',
			),
			'member_position' => array(
				'label'    => esc_html__( 'Member Position', 'et_builder' ),
				'selector' => '.et_pb_member_position',
			),
			'member_social_links' => array(
				'label'    => esc_html__( 'Member Social Links', 'et_builder' ),
				'selector' => '.et_pb_member_social_links',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'name' => array(
				'label'           => esc_html__( 'Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the person', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'position' => array(
				'label'           => esc_html__( 'Position', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( "Input the person's position.", 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'image_url' => array(
				'label'              => esc_html__( 'Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'image',
			),
			'animation' => array(
				'label'             => esc_html__( 'Animation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off'     => esc_html__( 'No Animation', 'et_builder' ),
					'fade_in' => esc_html__( 'Fade In', 'et_builder' ),
					'left'    => esc_html__( 'Left To Right', 'et_builder' ),
					'right'   => esc_html__( 'Right To Left', 'et_builder' ),
					'top'     => esc_html__( 'Top To Bottom', 'et_builder' ),
					'bottom'  => esc_html__( 'Bottom To Top', 'et_builder' ),
				),
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'           => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),
			'facebook_url' => array(
				'label'           => esc_html__( 'Facebook Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input Facebook Profile Url.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'twitter_url' => array(
				'label'           => esc_html__( 'Twitter Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input Twitter Profile Url', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'google_url' => array(
				'label'           => esc_html__( 'Google+ Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input Google+ Profile Url', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'linkedin_url' => array(
				'label'           => esc_html__( 'LinkedIn Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input LinkedIn Profile Url', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'content_new' => array(
				'label'           => esc_html__( 'Description', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
			),
			'icon_hover_color' => array(
				'label'             => esc_html__( 'Icon Hover Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$name              = $this->shortcode_atts['name'];
		$position          = $this->shortcode_atts['position'];
		$image_url         = $this->shortcode_atts['image_url'];
		$animation         = $this->shortcode_atts['animation'];
		$facebook_url      = $this->shortcode_atts['facebook_url'];
		$twitter_url       = $this->shortcode_atts['twitter_url'];
		$google_url        = $this->shortcode_atts['google_url'];
		$linkedin_url      = $this->shortcode_atts['linkedin_url'];
		$background_layout = $this->shortcode_atts['background_layout'];
		$icon_color        = $this->shortcode_atts['icon_color'];
		$icon_hover_color  = $this->shortcode_atts['icon_hover_color'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$image = $social_links = '';

		if ( '' !== $icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_member_social_links a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $icon_color )
				),
			) );
		}

		if ( '' !== $icon_hover_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_member_social_links a:hover',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $icon_hover_color )
				),
			) );
		}

		if ( '' !== $facebook_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_facebook_icon"><span>%2$s</span></a></li>',
				esc_url( $facebook_url ),
				esc_html__( 'Facebook', 'et_builder' )
			);
		}

		if ( '' !== $twitter_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_twitter_icon"><span>%2$s</span></a></li>',
				esc_url( $twitter_url ),
				esc_html__( 'Twitter', 'et_builder' )
			);
		}

		if ( '' !== $google_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_google_icon"><span>%2$s</span></a></li>',
				esc_url( $google_url ),
				esc_html__( 'Google+', 'et_builder' )
			);
		}

		if ( '' !== $linkedin_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_linkedin_icon"><span>%2$s</span></a></li>',
				esc_url( $linkedin_url ),
				esc_html__( 'LinkedIn', 'et_builder' )
			);
		}

		if ( '' !== $social_links ) {
			$social_links = sprintf( '<ul class="et_pb_member_social_links">%1$s</ul>', $social_links );
		}

		if ( '' !== $image_url ) {
			$image = sprintf(
				'<div class="et_pb_team_member_image et-waypoint%3$s">
					<img src="%1$s" alt="%2$s" />
				</div>',
				esc_url( $image_url ),
				esc_attr( $name ),
				esc_attr( " et_pb_animation_{$animation}" )
			);
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_team_member%4$s%9$s%10$s%12$s et_pb_bg_layout_%8$s clearfix">
				%13$s
				%11$s
				%2$s
				<div class="et_pb_team_member_description">
					%5$s
					%6$s
					%1$s
					%7$s
				</div> <!-- .et_pb_team_member_description -->
			</div> <!-- .et_pb_team_member -->',
			$this->shortcode_content,
			( '' !== $image ? $image : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $name ? sprintf( '<h4>%1$s</h4>', esc_html( $name ) ) : '' ),
			( '' !== $position ? sprintf( '<p class="et_pb_member_position">%1$s</p>', esc_html( $position ) ) : '' ),
			$social_links,
			$background_layout,
			( '' === $image ? ' et_pb_team_member_no_image' : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Team_Member;

class ET_Builder_Module_Blog extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Blog', 'et_builder' );
		$this->slug       = 'et_pb_blog';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'fullwidth',
			'posts_number',
			'include_categories',
			'meta_date',
			'show_thumbnail',
			'show_content',
			'show_more',
			'show_author',
			'show_date',
			'show_categories',
			'show_comments',
			'show_pagination',
			'offset_number',
			'background_layout',
			'admin_label',
			'module_id',
			'module_class',
			'masonry_tile_background_color',
			'use_dropshadow',
			'use_overlay',
			'overlay_icon_color',
			'hover_overlay_color',
			'hover_icon',
		);

		$this->fields_defaults = array(
			'fullwidth'         => array( 'on' ),
			'posts_number'      => array( 10, 'add_default_setting' ),
			'meta_date'         => array( 'M j, Y', 'add_default_setting' ),
			'show_thumbnail'    => array( 'on' ),
			'show_content'      => array( 'off' ),
			'show_more'         => array( 'off' ),
			'show_author'       => array( 'on' ),
			'show_date'         => array( 'on' ),
			'show_categories'   => array( 'on' ),
			'show_comments'     => array( 'off' ),
			'show_pagination'   => array( 'on' ),
			'offset_number'     => array( 0, 'only_default_setting' ),
			'background_layout' => array( 'light' ),
			'use_dropshadow'    => array( 'off' ),
			'use_overlay'       => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%% .et_pb_post';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'  => esc_html__( 'Layout', 'et_builder' ),
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'text'    => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .entry-title",
						'color' => "{$this->main_css_element} .entry-title a",
						'plugin_main' => "{$this->main_css_element} .entry-title, {$this->main_css_element} .entry-title a",
						'important' => 'all',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element}, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content p, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content p",
						'color'       => "{$this->main_css_element}, {$this->main_css_element} .post-content *",
						'line_height' => "{$this->main_css_element} p",
						'plugin_main' => "{$this->main_css_element}, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content p, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content p, %%order_class%%.et_pb_bg_layout_light .et_pb_post a.more-link, %%order_class%%.et_pb_bg_layout_dark .et_pb_post a.more-link",
					),
				),
				'meta' => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
						'plugin_main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a, {$this->main_css_element} .post-meta span",
					),
				),
			),
			'border' => array(
				'css'      => array(
					'main' => "%%order_class%%.et_pb_module .et_pb_post",
					'important' => 'plugin_only',
				),
			),
		);
		$this->custom_css_options = array(
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => '.et_pb_post .entry-title',
			),
			'post_meta' => array(
				'label'    => esc_html__( 'Post Meta', 'et_builder' ),
				'selector' => '.et_pb_post .post-meta',
			),
			'pagenavi' => array(
				'label'    => esc_html__( 'Pagenavi', 'et_builder' ),
				'selector' => '.wp_pagenavi',
			),
			'featured_image' => array(
				'label'    => esc_html__( 'Featured Image', 'et_builder' ),
				'selector' => '.et_pb_image_container',
			),
			'read_more' => array(
				'label'    => esc_html__( 'Read More Button', 'et_builder' ),
				'selector' => '.et_pb_post .more-link',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'fullwidth' => array(
				'label'             => esc_html__( 'Layout', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Fullwidth', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'affects'           => array(
					'background_layout',
					'use_dropshadow',
					'masonry_tile_background_color',
				),
				'description'        => esc_html__( 'Toggle between the various blog layout types.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'tab_slug'           => 'advanced',
				'toggle_slug'        => 'layout',
			),
			'posts_number' => array(
				'label'             => esc_html__( 'Posts Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Choose how much posts you would like to display per page.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'       => 'main_content',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'renderer'         => 'et_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'description'      => esc_html__( 'Choose which categories you would like to include in the feed.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
			),
			'meta_date' => array(
				'label'             => esc_html__( 'Meta Date Format', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'If you would like to adjust the date format, input the appropriate PHP date format here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'show_thumbnail' => array(
				'label'             => esc_html__( 'Show Featured Image', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'       => esc_html__( 'This will turn thumbnails on and off.', 'et_builder' ),
				'computed_affects'  => array(
					'__posts',
				),
				'toggle_slug'       => 'elements',
			),
			'show_content' => array(
				'label'             => esc_html__( 'Content', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'Show Excerpt', 'et_builder' ),
					'on'  => esc_html__( 'Show Content', 'et_builder' ),
				),
				'affects'           => array(
					'show_more',
				),
				'description'       => esc_html__( 'Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'show_more' => array(
				'label'             => esc_html__( 'Read More Button', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Here you can define whether to show "read more" link after the excerpts or not.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'       => 'elements',
			),
			'show_author' => array(
				'label'             => esc_html__( 'Show Author', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'        => esc_html__( 'Turn on or off the author link.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'        => 'elements',
			),
			'show_date' => array(
				'label'             => esc_html__( 'Show Date', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'        => esc_html__( 'Turn the date on or off.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'        => 'elements',
			),
			'show_categories' => array(
				'label'             => esc_html__( 'Show Categories', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'        => esc_html__( 'Turn the category links on or off.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'        => 'elements',
			),
			'show_comments' => array(
				'label'             => esc_html__( 'Show Comment Count', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'        => esc_html__( 'Turn comment count on and off.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'        => 'elements',
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'        => esc_html__( 'Turn pagination on and off.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'        => 'elements',
			),
			'offset_number' => array(
				'label'            => esc_html__( 'Offset Number', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose how many posts you would like to offset by', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
			),
			'use_overlay' => array(
				'label'             => esc_html__( 'Featured Image Overlay', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects'           => array(
					'overlay_icon_color',
					'hover_overlay_color',
					'hover_icon',
				),
				'description'       => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the featured image of a post.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'overlay_icon_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay icon', 'et_builder' ),
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'depends_show_if'     => 'on',
				'description'         => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
				'computed_affects'    => array(
					'__posts',
				),
			),
			'background_layout' => array(
				'label'       => esc_html__( 'Text Color', 'et_builder' ),
				'type'        => 'select',
				'option_category' => 'color_option',
				'options'           => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'masonry_tile_background_color' => array(
				'label'             => esc_html__( 'Grid Tile Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'background',
				'depends_show_if'   => 'off',
				'depends_to'        => array(
					'fullwidth'
				),
			),
			'use_dropshadow' => array(
				'label'             => esc_html__( 'Use Dropshadow', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'layout',
				'depends_show_if'   => 'off',
				'depends_to'        => array(
					'fullwidth'
				),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__posts' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Blog', 'get_blog_posts' ),
				'computed_depends_on' => array(
					'fullwidth',
					'posts_number',
					'include_categories',
					'meta_date',
					'show_thumbnail',
					'show_content',
					'show_more',
					'show_author',
					'show_date',
					'show_categories',
					'show_comments',
					'show_pagination',
					'offset_number',
					'use_overlay',
					'hover_icon',
				),
				'computed_minimum' => array(
					'posts_number',
				),
			),
		);
		return $fields;
	}

	/**
	 * Get blog posts for blog module
	 *
	 * @param array   arguments that is being used by et_pb_blog
	 * @return string blog post markup
	 */
	static function get_blog_posts( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $paged, $post, $wp_query, $et_fb_processing_shortcode_object, $et_pb_rendering_column_content;

		$global_processing_original_value = $et_fb_processing_shortcode_object;

		// Default params are combination of attributes that is used by et_pb_blog and
		// conditional tags that need to be simulated (due to AJAX nature) by passing args
		$defaults = array(
			'fullwidth'                     => '',
			'posts_number'                  => '',
			'include_categories'            => '',
			'meta_date'                     => '',
			'show_thumbnail'                => '',
			'show_content'                  => '',
			'show_author'                   => '',
			'show_date'                     => '',
			'show_categories'               => '',
			'show_comments'                 => '',
			'show_pagination'               => '',
			'background_layout'             => '',
			'show_more'                     => '',
			'offset_number'                 => '',
			'masonry_tile_background_color' => '',
			'use_dropshadow'                => '',
			'overlay_icon_color'            => '',
			'hover_overlay_color'           => '',
			'hover_icon'                    => '',
			'use_overlay'                   => '',
		);

		// WordPress' native conditional tag is only available during page load. It'll fail during component update because
		// et_pb_process_computed_property() is loaded in admin-ajax.php. Thus, use WordPress' conditional tags on page load and
		// rely to passed $conditional_tags for AJAX call
		$is_front_page               = et_fb_conditional_tag( 'is_front_page', $conditional_tags );
		$is_search                   = et_fb_conditional_tag( 'is_search', $conditional_tags );
		$is_single                   = et_fb_conditional_tag( 'is_single', $conditional_tags );
		$et_is_builder_plugin_active = et_fb_conditional_tag( 'et_is_builder_plugin_active', $conditional_tags );

		$container_is_closed = false;

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class');

		$args = wp_parse_args( $args, $defaults );

		$overlay_output = '';
		$hover_icon = '';

		if ( 'on' === $args['use_overlay'] ) {
			$data_icon = '' !== $args['hover_icon']
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $args['hover_icon'] ) )
				)
				: '';

			$overlay_output = sprintf(
				'<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $args['hover_icon'] ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		$overlay_class = 'on' === $args['use_overlay'] ? ' et_pb_has_overlay' : '';

		$query_args = array(
			'posts_per_page' => intval( $args['posts_number'] ),
			'post_status'    => 'publish',
		);

		if ( defined( 'DOING_AJAX' ) && isset( $current_page[ 'paged'] ) ) {
			$paged = intval( $current_page[ 'paged' ] );
		} else {
			$paged = $is_front_page ? get_query_var( 'page' ) : get_query_var( 'paged' );
		}

		if ( '' !== $args['include_categories'] ) {
			$query_args['cat'] = $args['include_categories'];
		}

		if ( ! $is_search ) {
			$query_args['paged'] = $paged;
		}

		if ( '' !== $args['offset_number'] && ! empty( $args['offset_number'] ) ) {
			/**
			 * Offset + pagination don't play well. Manual offset calculation required
			 * @see: https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			if ( $paged > 1 ) {
				$query_args['offset'] = ( ( $paged - 1 ) * intval( $args['posts_number'] ) ) + intval( $args['offset_number'] );
			} else {
				$query_args['offset'] = intval( $args['offset_number'] );
			}
		}

		if ( $is_single ) {
			$query_args['post__not_in'][] = get_the_ID();
		}

		// Get query
		$query = new WP_Query( $query_args );

		// Keep page's $wp_query global
		$wp_query_page = $wp_query;

		// Turn page's $wp_query into this module's query
		$wp_query = $query;

		ob_start();

		if ( $query->have_posts() ) {

			while( $query->have_posts() ) {
				$query->the_post();
				global $et_fb_processing_shortcode_object;

				$global_processing_original_value = $et_fb_processing_shortcode_object;

				// reset the fb processing flag
				$et_fb_processing_shortcode_object = false;

				$thumb          = '';
				$width          = 'on' === $args['fullwidth'] ? 1080 : 400;
				$width          = (int) apply_filters( 'et_pb_blog_image_width', $width );
				$height         = 'on' === $args['fullwidth'] ? 675 : 250;
				$height         = (int) apply_filters( 'et_pb_blog_image_height', $height );
				$classtext      = 'on' === $args['fullwidth'] ? 'et_pb_post_main_image' : '';
				$titletext      = get_the_title();
				$thumbnail      = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
				$thumb          = $thumbnail["thumb"];
				$no_thumb_class = '' === $thumb || 'off' === $args['show_thumbnail'] ? ' et_pb_no_thumb' : '';

				$post_format = et_pb_post_format();
				if ( in_array( $post_format, array( 'video', 'gallery' ) ) ) {
					$no_thumb_class = '';
				}

				// Print output
				?>
					<article id="" <?php post_class( 'et_pb_post clearfix' . $no_thumb_class . $overlay_class ) ?>>
						<?php
							et_divi_post_format_content();

							if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) {
								if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) :
									$video_overlay = has_post_thumbnail() ? sprintf(
										'<div class="et_pb_video_overlay" style="background-image: url(%1$s); background-size: cover;">
											<div class="et_pb_video_overlay_hover">
												<a href="#" class="et_pb_video_play"></a>
											</div>
										</div>',
										$thumb
									) : '';

									printf(
										'<div class="et_main_video_container">
											%1$s
											%2$s
										</div>',
										$video_overlay,
										$first_video
									);
								elseif ( 'gallery' === $post_format ) :
									et_pb_gallery_images( 'slider' );
								elseif ( '' !== $thumb && 'on' === $args['show_thumbnail'] ) :
									if ( 'on' !== $args['fullwidth'] ) echo '<div class="et_pb_image_container">'; ?>
										<a href="<?php esc_url( the_permalink() ); ?>" class="entry-featured-image-url">
											<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height ); ?>
											<?php if ( 'on' === $args['use_overlay'] ) {
												echo $overlay_output;
											} ?>
										</a>
								<?php
									if ( 'on' !== $args['fullwidth'] ) echo '</div> <!-- .et_pb_image_container -->';
								endif;
							}
						?>

						<?php if ( 'off' === $args['fullwidth'] || ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) { ?>
							<?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) ) { ?>
								<h2 class="entry-title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h2>
							<?php } ?>

							<?php
								if ( 'on' === $args['show_author'] || 'on' === $args['show_date'] || 'on' === $args['show_categories'] || 'on' === $args['show_comments'] ) {
									printf( '<p class="post-meta">%1$s %2$s %3$s %4$s %5$s %6$s %7$s</p>',
										(
											'on' === $args['show_author']
												? et_get_safe_localization( sprintf( __( 'by %s', 'et_builder' ), '<span class="author vcard">' .  et_pb_get_the_author_posts_link() . '</span>' ) )
												: ''
										),
										(
											( 'on' === $args['show_author'] && 'on' === $args['show_date'] )
												? ' | '
												: ''
										),
										(
											'on' === $args['show_date']
												? et_get_safe_localization( sprintf( __( '%s', 'et_builder' ), '<span class="published">' . esc_html( get_the_date( $args['meta_date'] ) ) . '</span>' ) )
												: ''
										),
										(
											(( 'on' === $args['show_author'] || 'on' === $args['show_date'] ) && 'on' === $args['show_categories'] )
												? ' | '
												: ''
										),
										(
											'on' === $args['show_categories']
												? get_the_category_list(', ')
												: ''
										),
										(
											(( 'on' === $args['show_author'] || 'on' === $args['show_date'] || 'on' === $args['show_categories'] ) && 'on' === $args['show_comments'])
												? ' | '
												: ''
										),
										(
											'on' === $args['show_comments']
												? sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) )
												: ''
										)
									);
								}

								$post_content = et_strip_shortcodes( et_delete_post_first_video( get_the_content() ), true );

								// reset the fb processing flag
								$et_fb_processing_shortcode_object = false;
								// set the flag to indicate that we're processing internal content
								$et_pb_rendering_column_content = true;
								// reset all the attributes required to properly generate the internal styles
								ET_Builder_Element::clean_internal_modules_styles();

								echo '<div class="post-content">';

								if ( 'on' === $args['show_content'] ) {
									global $more;

									// page builder doesn't support more tag, so display the_content() in case of post made with page builder
									if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
										$more = 1;

										echo apply_filters( 'the_content', $post_content );

									} else {
										$more = null;
										echo apply_filters( 'the_content', et_delete_post_first_video( get_the_content( esc_html__( 'read more...', 'et_builder' ) ) ) );
									}
								} else {
									if ( has_excerpt() ) {
										the_excerpt();
									} else {
										if ( '' !== $post_content ) {
											// set the $et_fb_processing_shortcode_object to false, to retrieve the content inside truncate_post() correctly
											$et_fb_processing_shortcode_object = false;
											echo wpautop( et_delete_post_first_video( strip_shortcodes( truncate_post( 270, false, '', true ) ) ) );
											// reset the $et_fb_processing_shortcode_object to its original value
											$et_fb_processing_shortcode_object = $global_processing_original_value;
										} else {
											echo '';
										}
									}
								}

								$et_fb_processing_shortcode_object = $global_processing_original_value;
								// retrieve the styles for the modules inside Blog content
								$internal_style = ET_Builder_Element::get_style( true );
								// reset all the attributes after we retrieved styles
								ET_Builder_Element::clean_internal_modules_styles( false );
								$et_pb_rendering_column_content = false;
								// append styles to the blog content
								if ( $internal_style ) {
									printf(
										'<style type="text/css" class="et_fb_blog_inner_content_styles">
											%1$s
										</style>',
										$internal_style
									);
								}

								echo '</div>';

								if ( 'on' !== $args['show_content'] ) {
									$more = 'on' == $args['show_more'] ? sprintf( ' <a href="%1$s" class="more-link" >%2$s</a>' , esc_url( get_permalink() ), esc_html__( 'read more', 'et_builder' ) )  : '';
									echo $more;
								}
								?>
						<?php } // 'off' === $fullwidth || ! in_array( $post_format, array( 'link', 'audio', 'quote', 'gallery' ?>
					</article>
				<?php

				$et_fb_processing_shortcode_object = $global_processing_original_value;
			} // endwhile

			if ( 'on' === $args['show_pagination'] && ! $is_search ) {
				// echo '</div> <!-- .et_pb_posts -->'; // @todo this causes closing tag issue

				$container_is_closed = true;

				if ( function_exists( 'wp_pagenavi' ) ) {
					wp_pagenavi( array(
						'query' => $query
					) );
				} else {
					if ( $et_is_builder_plugin_active ) {
						include( ET_BUILDER_PLUGIN_DIR . 'includes/navigation.php' );
					} else {
						get_template_part( 'includes/navigation', 'index' );
					}
				}
			}

			wp_reset_query();
		} else {
			if ( $et_is_builder_plugin_active ) {
				include( ET_BUILDER_PLUGIN_DIR . 'includes/no-results.php' );
			} else {
				get_template_part( 'includes/no-results', 'index' );
			}
		}

		wp_reset_postdata();

		// Reset $wp_query to its origin
		$wp_query = $wp_query_page;

		$posts = ob_get_contents();

		ob_end_clean();

		return $posts;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		/**
		 * Cached $wp_filter so it can be restored at the end of the callback.
		 * This is needed because this callback uses the_content filter / calls a function
		 * which uses the_content filter. WordPress doesn't support nested filter
		 */
		global $wp_filter;
		$wp_filter_cache = $wp_filter;

		$module_id           = $this->shortcode_atts['module_id'];
		$module_class        = $this->shortcode_atts['module_class'];
		$fullwidth           = $this->shortcode_atts['fullwidth'];
		$posts_number        = $this->shortcode_atts['posts_number'];
		$include_categories  = $this->shortcode_atts['include_categories'];
		$meta_date           = $this->shortcode_atts['meta_date'];
		$show_thumbnail      = $this->shortcode_atts['show_thumbnail'];
		$show_content        = $this->shortcode_atts['show_content'];
		$show_author         = $this->shortcode_atts['show_author'];
		$show_date           = $this->shortcode_atts['show_date'];
		$show_categories     = $this->shortcode_atts['show_categories'];
		$show_comments       = $this->shortcode_atts['show_comments'];
		$show_pagination     = $this->shortcode_atts['show_pagination'];
		$background_layout   = $this->shortcode_atts['background_layout'];
		$show_more           = $this->shortcode_atts['show_more'];
		$offset_number       = $this->shortcode_atts['offset_number'];
		$masonry_tile_background_color = $this->shortcode_atts['masonry_tile_background_color'];
		$use_dropshadow      = $this->shortcode_atts['use_dropshadow'];
		$overlay_icon_color  = $this->shortcode_atts['overlay_icon_color'];
		$hover_overlay_color = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon          = $this->shortcode_atts['hover_icon'];
		$use_overlay         = $this->shortcode_atts['use_overlay'];

		global $paged;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$container_is_closed = false;

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class');

		if ( '' !== $masonry_tile_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_blog_grid .et_pb_post',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $masonry_tile_background_color )
				),
			) );
		}

		if ( '' !== $overlay_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $overlay_icon_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		if ( 'on' === $use_overlay ) {
			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$overlay_output = sprintf(
				'<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		$overlay_class = 'on' === $use_overlay ? ' et_pb_has_overlay' : '';

		if ( 'on' !== $fullwidth ){
			if ( 'on' === $use_dropshadow ) {
				$module_class .= ' et_pb_blog_grid_dropshadow';
			}

			wp_enqueue_script( 'salvattore' );

			$background_layout = 'light';
		}

		$args = array( 'posts_per_page' => (int) $posts_number );

		$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );

		if ( is_front_page() ) {
			$paged = $et_paged;
		}

		if ( '' !== $include_categories )
			$args['cat'] = $include_categories;

		if ( ! is_search() ) {
			$args['paged'] = $et_paged;
		}

		if ( '' !== $offset_number && ! empty( $offset_number ) ) {
			/**
			 * Offset + pagination don't play well. Manual offset calculation required
			 * @see: https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			if ( $paged > 1 ) {
				$args['offset'] = ( ( $et_paged - 1 ) * intval( $posts_number ) ) + intval( $offset_number );
			} else {
				$args['offset'] = intval( $offset_number );
			}
		}

		if ( is_single() && ! isset( $args['post__not_in'] ) ) {
			$args['post__not_in'] = array( get_the_ID() );
		}

		ob_start();

		query_posts( $args );

		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$post_format = et_pb_post_format();

				$thumb = '';

				$width = 'on' === $fullwidth ? 1080 : 400;
				$width = (int) apply_filters( 'et_pb_blog_image_width', $width );

				$height = 'on' === $fullwidth ? 675 : 250;
				$height = (int) apply_filters( 'et_pb_blog_image_height', $height );
				$classtext = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
				$titletext = get_the_title();
				$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
				$thumb = $thumbnail["thumb"];

				$no_thumb_class = '' === $thumb || 'off' === $show_thumbnail ? ' et_pb_no_thumb' : '';

				if ( in_array( $post_format, array( 'video', 'gallery' ) ) ) {
					$no_thumb_class = '';
				} ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post clearfix' . $no_thumb_class . $overlay_class  ); ?>>

			<?php
				et_divi_post_format_content();

				if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) {
					if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) :
						$video_overlay = has_post_thumbnail() ? sprintf(
							'<div class="et_pb_video_overlay" style="background-image: url(%1$s); background-size: cover;">
								<div class="et_pb_video_overlay_hover">
									<a href="#" class="et_pb_video_play"></a>
								</div>
							</div>',
							$thumb
						) : '';

						printf(
							'<div class="et_main_video_container">
								%1$s
								%2$s
							</div>',
							$video_overlay,
							$first_video
						);
					elseif ( 'gallery' === $post_format ) :
						et_pb_gallery_images( 'slider' );
					elseif ( '' !== $thumb && 'on' === $show_thumbnail ) :
						if ( 'on' !== $fullwidth ) echo '<div class="et_pb_image_container">'; ?>
							<a href="<?php esc_url( the_permalink() ); ?>" class="entry-featured-image-url">
								<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height ); ?>
								<?php if ( 'on' === $use_overlay ) {
									echo $overlay_output;
								} ?>
							</a>
					<?php
						if ( 'on' !== $fullwidth ) echo '</div> <!-- .et_pb_image_container -->';
					endif;
				} ?>

			<?php if ( 'off' === $fullwidth || ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) { ?>
				<?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) ) { ?>
					<h2 class="entry-title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h2>
				<?php } ?>

				<?php
					if ( 'on' === $show_author || 'on' === $show_date || 'on' === $show_categories || 'on' === $show_comments ) {
						printf( '<p class="post-meta">%1$s %2$s %3$s %4$s %5$s %6$s %7$s</p>',
							(
								'on' === $show_author
									? et_get_safe_localization( sprintf( __( 'by %s', 'et_builder' ), '<span class="author vcard">' .  et_pb_get_the_author_posts_link() . '</span>' ) )
									: ''
							),
							(
								( 'on' === $show_author && 'on' === $show_date )
									? ' | '
									: ''
							),
							(
								'on' === $show_date
									? et_get_safe_localization( sprintf( __( '%s', 'et_builder' ), '<span class="published">' . esc_html( get_the_date( $meta_date ) ) . '</span>' ) )
									: ''
							),
							(
								(( 'on' === $show_author || 'on' === $show_date ) && 'on' === $show_categories)
									? ' | '
									: ''
							),
							(
								'on' === $show_categories
									? get_the_category_list(', ')
									: ''
							),
							(
								(( 'on' === $show_author || 'on' === $show_date || 'on' === $show_categories ) && 'on' === $show_comments)
									? ' | '
									: ''
							),
							(
								'on' === $show_comments
									? sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) )
									: ''
							)
						);
					}

					echo '<div class="post-content">';
					global $et_pb_rendering_column_content;

					$post_content = et_strip_shortcodes( et_delete_post_first_video( get_the_content() ), true );

					$et_pb_rendering_column_content = true;

					if ( 'on' === $show_content ) {
						global $more;

						// page builder doesn't support more tag, so display the_content() in case of post made with page builder
						if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
							$more = 1;
							echo apply_filters( 'the_content', $post_content );
						} else {
							$more = null;
							echo apply_filters( 'the_content', et_delete_post_first_video( get_the_content( esc_html__( 'read more...', 'et_builder' ) ) ) );
						}
					} else {
						if ( has_excerpt() ) {
							the_excerpt();
						} else {
							echo wpautop( et_delete_post_first_video( strip_shortcodes( truncate_post( 270, false, '', true ) ) ) );
						}
					}

					$et_pb_rendering_column_content = false;

					if ( 'on' !== $show_content ) {
						$more = 'on' == $show_more ? sprintf( ' <a href="%1$s" class="more-link" >%2$s</a>' , esc_url( get_permalink() ), esc_html__( 'read more', 'et_builder' ) )  : '';
						echo $more;
					}

					echo '</div>';
					?>
			<?php } // 'off' === $fullwidth || ! in_array( $post_format, array( 'link', 'audio', 'quote', 'gallery' ?>

			</article> <!-- .et_pb_post -->
	<?php
			} // endwhile

			if ( 'on' === $show_pagination && ! is_search() ) {
				echo '</div> <!-- .et_pb_posts -->';

				$container_is_closed = true;

				if ( function_exists( 'wp_pagenavi' ) ) {
					wp_pagenavi();
				} else {
					if ( et_is_builder_plugin_active() ) {
						include( ET_BUILDER_PLUGIN_DIR . 'includes/navigation.php' );
					} else {
						get_template_part( 'includes/navigation', 'index' );
					}
				}
			}
		} else {
			if ( et_is_builder_plugin_active() ) {
				include( ET_BUILDER_PLUGIN_DIR . 'includes/no-results.php' );
			} else {
				get_template_part( 'includes/no-results', 'index' );
			}
		}

		wp_reset_query();

		$posts = ob_get_contents();

		ob_end_clean();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$output = sprintf(
			'<div%5$s class="%1$s%3$s%6$s"%7$s>
				%2$s
			%4$s',
			( 'on' === $fullwidth ? 'et_pb_posts' : 'et_pb_blog_grid clearfix' ),
			$posts,
			esc_attr( $class ),
			( ! $container_is_closed ? '</div> <!-- .et_pb_posts -->' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'on' !== $fullwidth ? ' data-columns' : '' )
		);

		if ( 'on' !== $fullwidth )
			$output = sprintf( '<div class="et_pb_blog_grid_wrapper">%1$s</div>', $output );

		// Restore $wp_filter
		$wp_filter = $wp_filter_cache;
		unset($wp_filter_cache);

		return $output;
	}
}
new ET_Builder_Module_Blog;

class ET_Builder_Module_Shop extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Shop', 'et_builder' );
		$this->slug       = 'et_pb_shop';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'type',
			'posts_number',
			'columns_number',
			'include_categories',
			'orderby',
			'admin_label',
			'module_id',
			'module_class',
			'sale_badge_color',
			'icon_hover_color',
			'hover_overlay_color',
			'hover_icon',
		);

		$this->fields_defaults = array(
			'type'           => array( 'recent' ),
			'posts_number'   => array( '12', 'add_default_setting' ),
			'columns_number' => array( '0' ),
			'orderby'        => array( 'menu_order' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_shop';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'badge'   => esc_html__( 'Sale Badge', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce ul.products li.product h3, {$this->main_css_element} .woocommerce ul.products li.product h1, {$this->main_css_element} .woocommerce ul.products li.product h2, {$this->main_css_element} .woocommerce ul.products li.product h4, {$this->main_css_element} .woocommerce ul.products li.product h5, {$this->main_css_element} .woocommerce ul.products li.product h6",
						'important' => 'plugin_only',
					),
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce ul.products li.product .price, {$this->main_css_element} .woocommerce ul.products li.product .price .amount",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
		);

		$this->custom_css_options = array(
			'product' => array(
				'label'    => esc_html__( 'Product', 'et_builder' ),
				'selector' => 'li.product',
			),
			'onsale' => array(
				'label'    => esc_html__( 'Onsale', 'et_builder' ),
				'selector' => 'li.product .onsale',
			),
			'image' => array(
				'label'    => esc_html__( 'Image', 'et_builder' ),
				'selector' => '.et_shop_image',
			),
			'overlay' => array(
				'label'    => esc_html__( 'Overlay', 'et_builder' ),
				'selector' => '.et_overlay',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => $this->get_title_selector(),
			),
			'rating' => array(
				'label'    => esc_html__( 'Rating', 'et_builder' ),
				'selector' => '.star-rating',
			),
			'price' => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => 'li.product .price',
			),
			'price_old' => array(
				'label'    => esc_html__( 'Old Price', 'et_builder' ),
				'selector' => 'li.product .price del span.amount',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'type' => array(
				'label'           => esc_html__( 'Type', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'recent'  => esc_html__( 'Recent Products', 'et_builder' ),
					'featured' => esc_html__( 'Featured Products', 'et_builder' ),
					'sale' => esc_html__( 'Sale Products', 'et_builder' ),
					'best_selling' => esc_html__( 'Best Selling Products', 'et_builder' ),
					'top_rated' => esc_html__( 'Top Rated Products', 'et_builder' ),
					'product_category' => esc_html__( 'Product Category', 'et_builder' ),
				),
				'description'      => esc_html__( 'Choose which type of products you would like to display.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__shop',
				),
			),
			'posts_number' => array(
				'label'             => esc_html__( 'Product Count', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Control how many products are displayed.', 'et_builder' ),
				'computed_affects'  => array(
					'__shop',
				),
				'toggle_slug'       => 'main_content',
			),
			'include_categories'   => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'type'             => 'basic_option',
				'renderer'         => 'et_builder_include_categories_shop_option',
				'renderer_options' => array(
					'use_terms'    => true,
					'term_name'    => 'product_cat',
				),
				'description'      => esc_html__( 'Choose which categories you would like to include.', 'et_builder' ),
				'taxonomy_name'    => 'product_category',
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__shop',
				),
			),
			'columns_number' => array(
				'label'             => esc_html__( 'Columns Number', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'0' => esc_html__( 'default', 'et_builder' ),
					'6' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '6' ) ),
					'5' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '5' ) ),
					'4' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '4' ) ),
					'3' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '3' ) ),
					'2' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '2' ) ),
					'1' => esc_html__( '1 Column', 'et_builder' ),
				),
				'description'       => esc_html__( 'Choose how many columns to display.', 'et_builder' ),
				'computed_affects'  => array(
					'__shop',
				),
				'toggle_slug'       => 'main_content',
			),
			'orderby' => array(
				'label'             => esc_html__( 'Order By', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'menu_order'  => esc_html__( 'Default Sorting', 'et_builder' ),
					'popularity' => esc_html__( 'Sort By Popularity', 'et_builder' ),
					'rating' => esc_html__( 'Sort By Rating', 'et_builder' ),
					'date' => esc_html__( 'Sort By Date: Oldest To Newest', 'et_builder' ),
					'date-desc' => esc_html__( 'Sort By Date: Newest To Oldest', 'et_builder' ),
					'price' => esc_html__( 'Sort By Price: Low To High', 'et_builder' ),
					'price-desc' => esc_html__( 'Sort By Price: High To Low', 'et_builder' ),
				),
				'description'       => esc_html__( 'Choose how your products should be ordered.', 'et_builder' ),
				'computed_affects'  => array(
					'__shop',
				),
				'toggle_slug'       => 'main_content',
			),
			'sale_badge_color' => array(
				'label'             => esc_html__( 'Sale Badge Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'badge',
			),
			'icon_hover_color' => array(
				'label'             => esc_html__( 'Icon Hover Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__shop' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Shop', 'get_shop_html' ),
				'computed_depends_on' => array(
					'type',
					'include_categories',
					'posts_number',
					'orderby',
					'columns_number',
				),
				'computed_minimum' => array(
					'posts_number',
				),
			),
		);
		return $fields;
	}

	function add_product_class_name( $classes ) {
		$classes[] = 'product';

		return $classes;
	}

	function get_shop() {
		$type               = $this->shortcode_atts['type'];
		$include_categories = $this->shortcode_atts['include_categories'];
		$posts_number       = $this->shortcode_atts['posts_number'];
		$orderby            = $this->shortcode_atts['orderby'];
		$columns            = $this->shortcode_atts['columns_number'];

		$woocommerce_shortcodes_types = array(
			'recent'           => 'recent_products',
			'featured'         => 'featured_products',
			'sale'             => 'sale_products',
			'best_selling'     => 'best_selling_products',
			'top_rated'        => 'top_rated_products',
			'product_category' => 'product_category',
		);

		/**
		 * Actually, orderby parameter used by WooCommerce shortcode is equal to orderby parameter used by WP_Query
		 * Hence customize WooCommerce' product query via modify_woocommerce_shortcode_products_query method
		 * @see http://docs.woothemes.com/document/woocommerce-shortcodes/#section-5
		 */
		$modify_woocommerce_query = 'best_selling' !== $type && in_array( $orderby, array( 'menu_order', 'price', 'price-desc', 'date', 'date-desc', 'rating', 'popularity' ) );

		if ( $modify_woocommerce_query ) {
			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'modify_woocommerce_shortcode_products_query' ), 10, 2 );
		}

		do_action( 'et_pb_shop_before_print_shop' );

		$shop = do_shortcode(
			sprintf( '[%1$s per_page="%2$s" orderby="%3$s" columns="%4$s" category="%5$s"]',
				esc_html( $woocommerce_shortcodes_types[$type] ),
				esc_attr( $posts_number ),
				esc_attr( $orderby ),
				esc_attr( $columns ),
				esc_attr( $include_categories )
			)
		);

		do_action( 'et_pb_shop_after_print_shop' );

		/**
		 * Remove modify_woocommerce_shortcode_products_query method after being used
		 */
		if ( $modify_woocommerce_query ) {
			remove_filter( 'woocommerce_shortcode_products_query', array( $this, 'modify_woocommerce_shortcode_products_query' ) );

			if ( function_exists( 'WC' ) ) {
				WC()->query->remove_ordering_args(); // remove args added by woocommerce to avoid errors in sql queries performed afterwards
			}
		}

		return $shop;
	}

	/**
	 * Get shop HTML for shop module
	 *
	 * @param array   arguments that affect shop output
	 * @param array   passed conditional tag for update process
	 * @param array   passed current page params
	 * @return string HTML markup for shop module
	 */
	static function get_shop_html( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$shop = new self();

		do_action( 'et_pb_get_shop_html_before' );

		$shop->shortcode_atts = $args;

		// Force product loop to have 'product' class name. It appears that 'product' class disappears
		// when $this->get_shop() is being called for update / from admin-ajax.php
		add_filter( 'post_class', array( $shop, 'add_product_class_name' ) );

		// Get product HTML
		$output = $shop->get_shop();

		// Remove 'product' class addition to product loop's post class
		remove_filter( 'post_class', array( $shop, 'add_product_class_name' ) );

		do_action( 'et_pb_get_shop_html_after' );

		return $output;
	}


	// WooCommerce changed the title tag from h3 to h2 in 3.0.0
	function get_title_selector() {
		$title_selector = 'li.product h3';

		if ( class_exists( 'WooCommerce' ) ) {
			global $woocommerce;

			if ( version_compare( $woocommerce->version, '3.0.0', '>=' ) ) {
				$title_selector = 'li.product h2';
			}
		}

		return $title_selector;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$type                    = $this->shortcode_atts['type'];
		$include_categories      = $this->shortcode_atts['include_categories'];
		$posts_number            = $this->shortcode_atts['posts_number'];
		$orderby                 = $this->shortcode_atts['orderby'];
		$columns                 = $this->shortcode_atts['columns_number'];
		$sale_badge_color        = $this->shortcode_atts['sale_badge_color'];
		$icon_hover_color        = $this->shortcode_atts['icon_hover_color'];
		$hover_overlay_color     = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon              = $this->shortcode_atts['hover_icon'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $sale_badge_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% span.onsale',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $sale_badge_color )
				),
			) );
		}

		if ( '' !== $icon_hover_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $icon_hover_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay',
				'declaration' => sprintf(
					'background-color: %1$s !important;
					border-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		$data_icon = '' !== $hover_icon
			? sprintf(
				' data-icon="%1$s"',
				esc_attr( et_pb_process_font_icon( $hover_icon ) )
			)
			: '';

		$output = sprintf(
			'<div%2$s class="et_pb_module et_pb_shop%3$s%4$s"%5$s>
				%1$s
			</div>',
			$this->get_shop(),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'0' === $columns ? ' et_pb_shop_grid' : '',
			$data_icon
		);

		return $output;
	}

	/**
	 * Modifying WooCommerce' product query filter based on $orderby value given
	 * @see WC_Query->get_catalog_ordering_args()
	 */
	function modify_woocommerce_shortcode_products_query( $args, $atts ) {

		if ( function_exists( 'WC' ) ) {
			// Default to ascending order
			$orderby = $this->shortcode_atts['orderby'];
			$order   = 'ASC';

			// Switch to descending order if orderby is 'price-desc' or 'date-desc'
			if ( in_array( $orderby, array( 'price-desc', 'date-desc' ) ) ) {
				$order = 'DESC';
			}

			// Supported orderby arguments (as defined by WC_Query->get_catalog_ordering_args() ): rand | date | price | popularity | rating | title
			$orderby = in_array( $orderby, array( 'price-desc', 'date-desc' ) ) ? str_replace( '-desc', '', $orderby ) : $orderby;

			// Get arguments for the given non-native orderby
			$query_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

			// Confirm that returned argument isn't empty then merge returned argument with default argument
			if( is_array( $query_args ) && ! empty( $query_args ) ) {
				$args = array_merge( $args, $query_args );
			}
		}

		return $args;
	}
}
new ET_Builder_Module_Shop;

class ET_Builder_Module_Countdown_Timer extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Countdown Timer', 'et_builder' );
		$this->slug       = 'et_pb_countdown_timer';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'title',
			'date_time',
			'background_layout',
			'use_background_color',
			'background_color',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'background_layout'    => array( 'dark' ),
			'use_background_color' => array( 'on' ),
			'background_color'     => array( et_builder_accent_color(), 'only_default_setting' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_countdown_timer';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => esc_html__( 'Text', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h4",
						'important' => array( 'size', 'plugin_all' ),
					),
				),
				'numbers' => array(
					'label'    => esc_html__( 'Numbers', 'et_builder' ),
					'css'      => array(
						'main' => "#main-content {$this->main_css_element} .section p",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'label' => array(
					'label'    => esc_html__( 'Label', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} .section p.label",
						'important' => array(
							'size',
							'line-height',
						),
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_color_gradient' => true,
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
		);
		$this->custom_css_options = array(
			'container' => array(
				'label'    => esc_html__( 'Container', 'et_builder' ),
				'selector' => '.et_pb_countdown_timer_container',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => '.title',
			),
			'timer_section' => array(
				'label'    => esc_html__( 'Timer Section', 'et_builder' ),
				'selector' => '.section',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Countdown Timer Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This is the title displayed for the countdown timer.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'date_time' => array(
				'label'           => esc_html__( 'Countdown To', 'et_builder' ),
				'type'            => 'date_picker',
				'option_category' => 'basic_option',
				'description'     => et_get_safe_localization( sprintf( __( 'This is the date the countdown timer is counting down to. Your countdown timer is based on your timezone settings in your <a href="%1$s" target="_blank" title="WordPress General Settings">WordPress General Settings</a>', 'et_builder' ), esc_url( admin_url( 'options-general.php' ) ) ) ),
				'toggle_slug'     => 'main_content',
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'depends_default'   => true,
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Here you can define a custom background color for your countdown timer.', 'et_builder' ),
			),
			'use_background_color' => array(
				'label'           => esc_html__( 'Use Background Color', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'on' => esc_html__( 'Yes', 'et_builder' ),
					'off'  => esc_html__( 'No', 'et_builder' ),
				),
				'affects'        => array(
					'background_color',
				),
				'toggle_slug'    => 'background',
				'description'    => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'et_builder' ),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id            = $this->shortcode_atts['module_id'];
		$module_class         = $this->shortcode_atts['module_class'];
		$title                = $this->shortcode_atts['title'];
		$date_time            = $this->shortcode_atts['date_time'];
		$background_layout    = $this->shortcode_atts['background_layout'];
		$background_color     = $this->shortcode_atts['background_color'];
		$use_background_color = $this->shortcode_atts['use_background_color'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$module_id = '' !== $module_id ? sprintf( ' id="%s"', esc_attr( $module_id ) ) : '';
		$module_class = '' !== $module_class ? sprintf( ' %s', esc_attr( $module_class ) ) : '';

		$background_layout = sprintf( ' et_pb_bg_layout_%s', esc_attr( $background_layout ) );

		$end_date = gmdate( 'M d, Y H:i:s', strtotime( $date_time ) );
		$gmt_offset        = get_option( 'gmt_offset' );
		$gmt_divider       = '-' === substr( $gmt_offset, 0, 1 ) ? '-' : '+';
		$gmt_offset_hour   = str_pad( abs( intval( $gmt_offset ) ), 2, "0", STR_PAD_LEFT );
		$gmt_offset_minute = str_pad( ( ( abs( $gmt_offset ) * 100 ) % 100 ) * ( 60 / 100 ), 2, "0", STR_PAD_LEFT );
		$gmt               = "GMT{$gmt_divider}{$gmt_offset_hour}{$gmt_offset_minute}";

		if ( '' !== $title ) {
			$title = sprintf( '<h4 class="title">%s</h4>', esc_html( $title ) );
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$background_color_style = '';
		if ( ! empty( $background_color ) && 'on' == $use_background_color ) {
			$background_color_style = sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) );
		}

		$output = sprintf(
			'<div%1$s class="et_pb_module et_pb_countdown_timer%2$s%3$s%15$s%17$s%19$s"%4$s data-end-timestamp="%5$s">
				%18$s
				%16$s
				<div class="et_pb_countdown_timer_container clearfix">
					%6$s
					<div class="days section values" data-short="%14$s" data-full="%7$s">
						<p class="value"></p>
						<p class="label">%7$s</p>
					</div>
					<div class="sep section"><p>:</p></div>
					<div class="hours section values" data-short="%9$s" data-full="%8$s">
						<p class="value"></p>
						<p class="label">%8$s</p>
					</div>
					<div class="sep section"><p>:</p></div>
					<div class="minutes section values" data-short="%11$s" data-full="%10$s">
						<p class="value"></p>
						<p class="label">%10$s</p>
					</div>
					<div class="sep section"><p>:</p></div>
					<div class="seconds section values" data-short="%13$s" data-full="%12$s">
						<p class="value"></p>
						<p class="label">%12$s</p>
					</div>
				</div>
			</div>',
			$module_id,
			$background_layout,
			$module_class,
			$background_color_style,
			esc_attr( strtotime( "{$end_date} {$gmt}" ) ),
			$title,
			esc_html__( 'Day(s)', 'et_builder' ),
			esc_html__( 'Hour(s)', 'et_builder' ),
			esc_attr__( 'Hrs', 'et_builder' ),
			esc_html__( 'Minute(s)', 'et_builder' ),
			esc_attr__( 'Min', 'et_builder' ),
			esc_html__( 'Second(s)', 'et_builder' ),
			esc_attr__( 'Sec', 'et_builder' ),
			esc_attr__( 'Day', 'et_builder' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background,
			( 'on' !== $use_background_color ? ' et_pb_no_bg' : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Countdown_Timer;

class ET_Builder_Module_Map extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Map', 'et_builder' );
		$this->slug            = 'et_pb_map';
		$this->fb_support      = true;
		$this->child_slug      = 'et_pb_map_pin';
		$this->child_item_text = esc_html__( 'Pin', 'et_builder' );

		$this->whitelisted_fields = array(
			'address',
			'zoom_level',
			'address_lat',
			'address_lng',
			'map_center_map',
			'mouse_wheel',
			'mobile_dragging',
			'admin_label',
			'module_id',
			'module_class',
			'use_grayscale_filter',
			'grayscale_filter_amount',
		);

		$this->fields_defaults = array(
			'zoom_level'              => array( '18', 'only_default_setting' ),
			'mouse_wheel'             => array( 'on' ),
			'mobile_dragging'         => array( 'on' ),
			'use_grayscale_filter'    => array( 'off' ),
			'grayscale_filter_amount' => array( '0' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'map' => esc_html__( 'Map', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'controls' => esc_html__( 'Controls', 'et_builder' ),
					'filter'   => esc_html__( 'Filter', 'et_builder' ),
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'google_maps_script_notice' => array(
				'type'              => 'warning',
				'value'             => et_pb_enqueue_google_maps_script(),
				'display_if'        => false,
				'message'           => esc_html__(
					sprintf(
						'The Google Maps API Script is currently disabled in the <a href="%s" target="_blank">Theme Options</a>. This module will not function properly without the Google Maps API.',
						admin_url( 'admin.php?page=et_divi_options' )
					),
					'et_builder'
				),
				'toggle_slug'       => 'map',
			),
			'google_api_key' => array(
				'label'             => esc_html__( 'Google API Key', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'attributes'        => 'readonly',
				'additional_button' => sprintf(
					' <a href="%2$s" target="_blank" class="et_pb_update_google_key button" data-empty_text="%3$s">%1$s</a>',
					esc_html__( 'Change API Key', 'et_builder' ),
					esc_url( et_pb_get_options_page_link() ),
					esc_attr__( 'Add Your API Key', 'et_builder' )
				),
				'additional_button_type' => 'change_google_api_key',
				'class' => array( 'et_pb_google_api_key', 'et-pb-helper-field' ),
				'description'       => et_get_safe_localization( sprintf( __( 'The Maps module uses the Google Maps API and requires a valid Google API Key to function. Before using the map module, please make sure you have added your API key inside the Divi Theme Options panel. Learn more about how to create your Google API Key <a href="%1$s" target="_blank">here</a>.', 'et_builder' ), esc_url( 'http://www.elegantthemes.com/gallery/divi/documentation/map/#gmaps-api-key' ) ) ),
				'toggle_slug'       => 'map',
			),
			'address' => array(
				'label'             => esc_html__( 'Map Center Address', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'additional_button' => sprintf(
					' <a href="#" class="et_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'et_builder' )
				),
				'class' => array( 'et_pb_address' ),
				'description'       => esc_html__( 'Enter an address for the map center point, and the address will be geocoded and displayed on the map below.', 'et_builder' ),
				'toggle_slug'       => 'map',
			),
			'zoom_level' => array(
				'type'    => 'hidden',
				'class'   => array( 'et_pb_zoom_level' ),
			),
			'address_lat' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_address_lat' ),
			),
			'address_lng' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_address_lng' ),
			),
			'map_center_map' => array(
				'renderer'              => 'et_builder_generate_center_map_setting',
				'use_container_wrapper' => false,
				'option_category'       => 'basic_option',
				'toggle_slug'           => 'map',
			),
			'mouse_wheel' => array(
				'label'           => esc_html__( 'Mouse Wheel Zoom', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options' => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'controls',
				'description'     => esc_html__( 'Here you can choose whether the zoom level will be controlled by mouse wheel or not.', 'et_builder' ),
			),
			'mobile_dragging' => array(
				'label'           => esc_html__( 'Draggable on Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'controls',
				'description'     => esc_html__( 'Here you can choose whether or not the map will be draggable on mobile devices.', 'et_builder' ),
			),
			'use_grayscale_filter' => array(
				'label'           => esc_html__( 'Use Grayscale Filter', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'     => array(
					'grayscale_filter_amount',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'filter',
			),
			'grayscale_filter_amount' => array(
				'label'           => esc_html__( 'Grayscale Filter Amount (%)', 'et_builder' ),
				'type'            => 'range',
				'default'         => '0',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'filter',
				'depends_show_if' => 'on',
				'validate_unit'   => false,
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$address_lat             = $this->shortcode_atts['address_lat'];
		$address_lng             = $this->shortcode_atts['address_lng'];
		$zoom_level              = $this->shortcode_atts['zoom_level'];
		$mouse_wheel             = $this->shortcode_atts['mouse_wheel'];
		$mobile_dragging         = $this->shortcode_atts['mobile_dragging'];
		$use_grayscale_filter    = $this->shortcode_atts['use_grayscale_filter'];
		$grayscale_filter_amount = $this->shortcode_atts['grayscale_filter_amount'];

		if ( et_pb_enqueue_google_maps_script() ) {
			wp_enqueue_script( 'google-maps-api' );
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$all_pins_content = $this->shortcode_content;

		$grayscale_filter_data = '';
		if ( 'on' === $use_grayscale_filter && '' !== $grayscale_filter_amount ) {
			$grayscale_filter_data = sprintf( ' data-grayscale="%1$s"', esc_attr( $grayscale_filter_amount ) );
		}

		$output = sprintf(
			'<div%5$s class="et_pb_module et_pb_map_container%6$s"%8$s>
				<div class="et_pb_map" data-center-lat="%1$s" data-center-lng="%2$s" data-zoom="%3$d" data-mouse-wheel="%7$s" data-mobile-dragging="%9$s"></div>
				%4$s
			</div>',
			esc_attr( $address_lat ),
			esc_attr( $address_lng ),
			esc_attr( $zoom_level ),
			$all_pins_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $mouse_wheel ),
			$grayscale_filter_data,
			esc_attr( $mobile_dragging )
		);

		return $output;
	}
}
new ET_Builder_Module_Map;

class ET_Builder_Module_Map_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Pin', 'et_builder' );
		$this->slug                        = 'et_pb_map_pin';
		$this->fb_support                  = true;
		$this->type                        = 'child';
		$this->child_title_var             = 'title';
		$this->custom_css_tab              = false;

		$this->whitelisted_fields = array(
			'title',
			'pin_address',
			'zoom_level',
			'pin_address_lat',
			'pin_address_lng',
			'map_center_map',
			'content_new',
		);

		$this->advanced_setting_title_text = esc_html__( 'New Pin', 'et_builder' );
		$this->settings_text               = esc_html__( 'Pin Settings', 'et_builder' );

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'map'          => esc_html__( 'Map', 'et_builder' ),
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title will be used within the tab button for this tab.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'pin_address' => array(
				'label'             => esc_html__( 'Map Pin Address', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'class'             => array( 'et_pb_pin_address' ),
				'description'       => esc_html__( 'Enter an address for this map pin, and the address will be geocoded and displayed on the map below.', 'et_builder' ),
				'additional_button' => sprintf(
					'<a href="#" class="et_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'et_builder' )
				),
				'toggle_slug'       => 'map',
			),
			'zoom_level' => array(
				'renderer'        => 'et_builder_generate_pin_zoom_level_input',
				'option_category' => 'basic_option',
				'class'           => array( 'et_pb_zoom_level' ),
			),
			'pin_address_lat' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_pin_address_lat' ),
			),
			'pin_address_lng' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_pin_address_lng' ),
			),
			'map_center_map' => array(
				'renderer'              => 'et_builder_generate_center_map_setting',
				'option_category'       => 'basic_option',
				'use_container_wrapper' => false,
				'toggle_slug'           => 'map',
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the infobox for the pin.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_tab_titles;

		$title = $this->shortcode_atts['title'];
		$pin_address_lat = $this->shortcode_atts['pin_address_lat'];
		$pin_address_lng = $this->shortcode_atts['pin_address_lng'];

		$replace_htmlentities = array( '&#8221;' => '', '&#8243;' => '' );

		if ( ! empty( $pin_address_lat ) ) {
			$pin_address_lat = strtr( $pin_address_lat, $replace_htmlentities );
		}
		if ( ! empty( $pin_address_lng ) ) {
			$pin_address_lng = strtr( $pin_address_lng, $replace_htmlentities );
		}

		$content = $this->shortcode_content;

		$output = sprintf(
			'<div class="et_pb_map_pin" data-lat="%1$s" data-lng="%2$s" data-title="%5$s">
				<h3 style="margin-top: 10px;">%3$s</h3>
				%4$s
			</div>',
			esc_attr( $pin_address_lat ),
			esc_attr( $pin_address_lng ),
			esc_html( $title ),
			( '' != $content ? sprintf( '<div class="infowindow">%1$s</div>', $content ) : '' ),
			esc_attr( $title )
		);

		return $output;
	}
}
new ET_Builder_Module_Map_Item;

class ET_Builder_Module_Social_Media_Follow extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Social Media Follow', 'et_builder' );
		$this->slug            = 'et_pb_social_media_follow';
		$this->fb_support      = true;
		$this->child_slug      = 'et_pb_social_media_follow_network';
		$this->child_item_text = esc_html__( 'Social Network', 'et_builder' );

		$this->main_css_element = 'ul%%order_class%%';

		$this->whitelisted_fields = array(
			'link_shape',
			'background_layout',
			'url_new_window',
			'follow_button',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => esc_html__( 'Text', 'et_builder' ),
				),
			),
		);

		$this->fields_defaults = array(
			'link_shape'        => array( 'rounded_rectangle' ),
			'background_layout' => array( 'light' ),
			'url_new_window'    => array( 'on' ),
			'follow_button'     => array( 'off' ),
		);

		$this->custom_css_options = array(
			'before' => array(
				'label'    => esc_html__( 'Before', 'et_builder' ),
				'selector' => 'ul%%order_class%%:before',
			),
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'selector' => 'ul%%order_class%%',
			),
			'after' => array(
				'label'    => esc_html__( 'After', 'et_builder' ),
				'selector' => 'ul%%order_class%%:after',
			),
			'social_follow' => array(
				'label'    => esc_html__( 'Social Follow', 'et_builder' ),
				'selector' => 'li',
			),
			'social_icon' => array(
				'label'    => esc_html__( 'Social Icon', 'et_builder' ),
				'selector' => 'li a.icon',
			),
			'follow_button' => array(
				'label'    => esc_html__( 'Follow Button', 'et_builder' ),
				'selector' => 'li a.follow_button',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'link_shape' => array(
				'label'           => esc_html__( 'Link Shape', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'rounded_rectangle' => esc_html__( 'Rounded Rectangle', 'et_builder' ),
					'circle'            => esc_html__( 'Circle', 'et_builder' ),
				),
				'toggle_slug'     => 'icon',
				'description'     => esc_html__( 'Here you can choose the shape of your social network icons.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'icon',
				'description'     => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'follow_button' => array(
				'label'           => esc_html__( 'Follow Button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'toggle_slug'     => 'icon',
				'description'     => esc_html__( 'Here you can choose whether or not to include the follow button next to the icon.', 'et_builder' ),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_social_media_follow_link;

		$link_shape        = $this->shortcode_atts['link_shape'];
		$url_new_window    = $this->shortcode_atts['url_new_window'];
		$follow_button     = $this->shortcode_atts['follow_button'];

		$et_pb_social_media_follow_link = array(
			'url_new_window' => $url_new_window,
			'shape'          => $link_shape,
			'follow_button'  => $follow_button,
		);
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_social_media_follow_link;

		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$background_layout = $this->shortcode_atts['background_layout'];

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$output = sprintf(
			'<ul%3$s class="et_pb_social_media_follow%2$s%4$s%5$s clearfix">
				%1$s
			</ul> <!-- .et_pb_counters -->',
			$this->shortcode_content,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'on' === $et_pb_social_media_follow_link['follow_button'] ? ' has_follow_button' : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Social_Media_Follow;

class ET_Builder_Module_Social_Media_Follow_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Social Network', 'et_builder' );
		$this->slug                        = 'et_pb_social_media_follow_network';
		$this->fb_support                  = true;
		$this->type                        = 'child';
		$this->child_title_var             = 'content_new';

		$this->whitelisted_fields = array(
			'social_network',
			'content_new',
			'url',
			'bg_color',
			'skype_url',
			'skype_action',
		);

		$this->fields_defaults = array(
			'url'          => array( '#' ),
			'bg_color'     => array( et_builder_accent_color(), 'only_default_setting' ),
			'skype_action' => array( 'call' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Network', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
				),
			),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Social Network', 'et_builder' );
		$this->settings_text               = esc_html__( 'Social Network Settings', 'et_builder' );

		$this->custom_css_options = array(
			'before' => array(
				'label'    => esc_html__( 'Before', 'et_builder' ),
				'selector' => '.et_pb_social_media_follow li%%order_class%%:before',
			),
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'selector' => '.et_pb_social_media_follow li%%order_class%%',
			),
			'after' => array(
				'label'    => esc_html__( 'After', 'et_builder' ),
				'selector' => '.et_pb_social_media_follow li%%order_class%%:after',
			),
			'social_icon' => array(
				'label'    => esc_html__( 'Social Icon', 'et_builder' ),
				'selector' => '.et_pb_social_network_link a.icon',
				'no_space_before_selector' => true,
			),
			'follow_button' => array(
				'label'    => esc_html__( 'Follow Button', 'et_builder' ),
				'selector' => '.et_pb_social_network_link a.follow_button',
				'no_space_before_selector' => true,
			),
		);
	}

	function get_fields() {
		$fields = array(
			'social_network' => array(
				'label'           => esc_html__( 'Social Network', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'class'           => 'et-pb-social-network',
				'options' => array(
					''            => esc_html__( 'Select a Network', 'et_builder' ),
					'facebook'    => array(
						'value' => esc_html__( 'Facebook', 'et_builder' ),
						'data'  => array( 'color' => '#3b5998' ),
					),
					'twitter'     => array(
						'value' => esc_html__( 'Twitter', 'et_builder' ),
						'data'  => array( 'color' => '#00aced' ),
					),
					'google-plus' => array(
						'value' => esc_html__( 'Google+', 'et_builder' ),
						'data'  => array( 'color' => '#dd4b39' ),
					),
					'pinterest'   => array(
						'value' => esc_html__( 'Pinterest', 'et_builder' ),
						'data'  => array( 'color' => '#cb2027' ),
					),
					'linkedin'    => array(
						'value' => esc_html__( 'LinkedIn', 'et_builder' ),
						'data'  => array( 'color' => '#007bb6' ),
					),
					'tumblr'      => array(
						'value' => esc_html__( 'tumblr', 'et_builder' ),
						'data'  => array( 'color' => '#32506d' ),
					),
					'instagram'   => array(
						'value' => esc_html__( 'Instagram', 'et_builder' ),
						'data'  => array( 'color' => '#517fa4' ),
					),
					'skype'       => array(
						'value' => esc_html__( 'skype', 'et_builder' ),
						'data'  => array( 'color' => '#12A5F4' ),
					),
					'flikr'       => array(
						'value' => esc_html__( 'flikr', 'et_builder' ),
						'data'  => array( 'color' => '#ff0084' ),
					),
					'myspace'     => array(
						'value' => esc_html__( 'MySpace', 'et_builder' ),
						'data'  => array( 'color' => '#3b5998' ),
					),
					'dribbble'    => array(
						'value' => esc_html__( 'dribbble', 'et_builder' ),
						'data'  => array( 'color' => '#ea4c8d' ),
					),
					'youtube'     => array(
						'value' => esc_html__( 'Youtube', 'et_builder' ),
						'data'  => array( 'color' => '#a82400' ),
					),
					'vimeo'       => array(
						'value' => esc_html__( 'Vimeo', 'et_builder' ),
						'data'  => array( 'color' => '#45bbff' ),
					),
					'rss'         => array(
						'value' => esc_html__( 'RSS', 'et_builder' ),
						'data'  => array( 'color' => '#ff8a3c' ),
					),
				),
				'affects'           => array(
					'url',
					'skype_url',
					'skype_action',
				),
				'overwrite_onchange' => array(
					'bg_color'
				),
				'description' => esc_html__( 'Choose the social network', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'content_new' => array(
				'label'       => esc_html__( 'Content', 'et_builder' ),
				'type'        => 'hidden',
				'toggle_slug' => 'main_content',
			),
			'url' => array(
				'label'               => esc_html__( 'Account URL', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'description'         => esc_html__( 'The URL for this social network link.', 'et_builder' ),
				'depends_show_if_not' => 'skype',
				'depends_to'          => array(
					'social_network'
				),
				'toggle_slug'         => 'link',
			),
			'skype_url' => array(
				'label'           => esc_html__( 'Account Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The Skype account name.', 'et_builder' ),
				'depends_show_if' => 'skype',
				'depends_to'          => array(
					'social_network'
				),
				'toggle_slug'     => 'main_content',
			),
			'skype_action' => array(
				'label'           => esc_html__( 'Skype Button Action', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'call' => esc_html__( 'Call', 'et_builder' ),
					'chat' => esc_html__( 'Chat', 'et_builder' ),
				),
				'depends_show_if' => 'skype',
				'depends_to'          => array(
					'social_network'
				),
				'description'     => esc_html__( 'Here you can choose which action to execute on button click', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'bg_color' => array(
				'label'           => esc_html__( 'Icon Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'This will change the icon color.', 'et_builder' ),
				'additional_code' => '<span class="et-pb-reset-setting reset-default-color" style="display: none;"></span>',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon',
			),
		);

		// Automatically parse social_network's option as value_overwrite
		foreach ( $fields['social_network']['options'] as $value_overwrite_key => $value_overwrite ) {
			if ( is_array( $value_overwrite ) && isset( $value_overwrite['data'] ) && $value_overwrite['data']['color'] ) {
				$fields['social_network']['value_overwrite'][ $value_overwrite_key ] = $value_overwrite['data']['color'];
			}
		}

		return $fields;
	}

	function get_network_name( $network ) {
		$all_fields = $this->get_fields();
		$network_names_mapping = $all_fields['social_network']['options'];

		if ( isset( $network_names_mapping[ $network ] ) && isset( $network_names_mapping[ $network ]['value'] ) ) {
			return $network_names_mapping[ $network ]['value'];
		}

		return $network;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_social_media_follow_link;

		$social_network = $this->shortcode_atts['social_network'];
		$url            = $this->shortcode_atts['url'];
		$bg_color       = $this->shortcode_atts['bg_color'];
		$skype_url      = $this->shortcode_atts['skype_url'];
		$skype_action   = $this->shortcode_atts['skype_action'];
		$follow_button  = '';
		$is_skype       = false;

		if ( isset( $bg_color ) && '' !== $bg_color ) {
			$bg_color_style = sprintf( 'background-color: %1$s;', esc_attr( $bg_color ) );
		}

		if ( 'skype' === $social_network ) {
			$skype_url = sprintf(
				'skype:%1$s?%2$s',
				sanitize_text_field( $skype_url ),
				sanitize_text_field( $skype_action )
			);
			$is_skype = true;
		}

		if ( 'on' === $et_pb_social_media_follow_link['follow_button'] ) {
			$follow_button = sprintf(
				'<a href="%1$s" class="follow_button" title="%2$s"%3$s>%4$s</a>',
				! $is_skype ? esc_url( $url ) : $skype_url,
				esc_attr( $this->get_network_name( trim( wp_strip_all_tags( $content ) ) ) ),
				( 'on' === $et_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
				esc_html__( 'Follow', 'et_builder' )
			);
		}

		$social_network = ET_Builder_Element::add_module_order_class( $social_network, $function_name );

		$output = sprintf(
			'<li class="et_pb_social_icon et_pb_social_network_link%1$s">
				<a href="%4$s" class="icon%2$s" title="%5$s"%7$s style="%3$s"><span>%6$s</span></a>
				%8$s
			</li>',
			( '' !== $social_network ? sprintf( ' et-social-%s', esc_attr( $social_network ) ) : '' ),
			( '' !== $et_pb_social_media_follow_link['shape'] ? sprintf( ' %s', esc_attr( $et_pb_social_media_follow_link['shape'] ) ) : '' ),
			$bg_color_style,
			! $is_skype ? esc_url( $url ) : $skype_url,
			esc_attr( $this->get_network_name( trim( wp_strip_all_tags( $content ) ) ) ),
			sanitize_text_field( $this->get_network_name( $content ) ),
			( 'on' === $et_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
			$follow_button
		);

		return $output;
	}
}
new ET_Builder_Module_Social_Media_Follow_Item;

class ET_Builder_Module_Post_Title extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Post Title', 'et_builder' );
		$this->slug             = 'et_pb_post_title';
		$this->fb_support       = true;
		$this->defaults         = array();

		$this->whitelisted_fields = array(
			'title',
			'meta',
			'author',
			'date',
			'date_format',
			'categories',
			'comments',
			'featured_image',
			'featured_placement',
			'parallax_effect',
			'parallax_method',
			'background_size',
			'background_position',
			'background_repeat',
			'background_blend',
			'text_orientation',
			'text_color',
			'text_background',
			'text_bg_color',
			'module_bg_color',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'title'              => array( 'on' ),
			'meta'               => array( 'on' ),
			'author'             => array( 'on' ),
			'date'               => array( 'on' ),
			'date_format'        => array( 'M j, Y' ),
			'categories'         => array( 'on' ),
			'comments'           => array( 'on' ),
			'featured_image'     => array( 'on' ),
			'featured_placement' => array( 'below' ),
			'parallax_effect'    => array( 'off' ),
			'parallax_method'    => array( 'on' ),
			'text_orientation'   => array( 'left' ),
			'text_color'         => array( 'dark' ),
			'text_background'    => array( 'off' ),
			'text_bg_color'      => array( 'rgba(255,255,255,0.9)', 'only_default_setting' ),
		);

		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'   => esc_html__( 'Elements', 'et_builder' ),
					'background' => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text'     => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'border'                => array(
				'css' => array(
					'main' => "{$this->main_css_element}.et_pb_featured_bg, {$this->main_css_element}",
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'main' => ".et_pb_section {$this->main_css_element}.et_pb_post_title",
					'important' => 'all',
				),
			),
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'use_all_caps' => true,
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_title_container h1.entry-title",
					),
				),
				'meta'   => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container, {$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container a",
						'plugin_main' => "{$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container, {$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container a, {$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container span",
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_image'          => false,
				'use_background_color_gradient' => true,
			),
		);
		$this->custom_css_options = array(
			'post_title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => 'h1',
			),
			'post_meta' => array(
				'label'    => esc_html__( 'Meta', 'et_builder' ),
				'selector' => '.et_pb_title_meta_container',
			),
			'post_image' => array(
				'label'    => esc_html__( 'Featured Image', 'et_builder' ),
				'selector' => '.et_pb_title_featured_container',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'             => esc_html__( 'Show Title', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Post Title', 'et_builder' ),
			),
			'meta' => array(
				'label'             => esc_html__( 'Show Meta', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'author',
					'date',
					'categories',
					'comments',
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Post Meta', 'et_builder' ),
			),
			'author' => array(
				'label'             => esc_html__( 'Show Author', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Author Name in Post Meta', 'et_builder' ),
			),
			'date' => array(
				'label'             => esc_html__( 'Show Date', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'affects'           => array(
					'date_format'
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Date in Post Meta', 'et_builder' ),
			),
			'date_format' => array(
				'label'             => esc_html__( 'Date Format', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can define the Date Format in Post Meta. Default is \'M j, Y\'', 'et_builder' ),
			),
			'categories' => array(
				'label'             => esc_html__( 'Show Post Categories', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Categories in Post Meta. Note: This option doesn\'t work with custom post types.', 'et_builder' ),
			),
			'comments' => array(
				'label'             => esc_html__( 'Show Comments Count', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Comments Count in Post Meta.', 'et_builder' ),
			),
			'featured_image' => array(
				'label'             => esc_html__( 'Show Featured Image', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'featured_placement',
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Featured Image', 'et_builder' ),
			),
			'featured_placement' => array(
				'label'             => esc_html__( 'Featured Image Placement', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'below'      => esc_html__( 'Below Title', 'et_builder' ),
					'above'      => esc_html__( 'Above Title', 'et_builder' ),
					'background' => esc_html__( 'Title/Meta Background Image', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'affects'           => array(
					'parallax_effect',
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose where to place the Featured Image', 'et_builder' ),
			),
			'parallax_effect' => array(
				'label'             => esc_html__( 'Use Parallax Effect', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'background',
				'affects'           => array(
					'parallax_method',
					'background_size',
					'background_position',
					'background_repeat',
					'background_blend',
				),
				'tab_slug'          => 'general',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Here you can choose whether or not use parallax effect for the featured image', 'et_builder' ),
			),
			'parallax_method' => array(
				'label'             => esc_html__( 'Parallax Method', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'CSS', 'et_builder' ),
					'off' => esc_html__( 'True Parallax', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'general',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Here you can choose which parallax method to use for the featured image', 'et_builder' ),
			),
			'background_size' => array(
				'label'           => esc_html__( 'Background Image Size', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'cover'   => esc_html__( 'Cover', 'et_builder' ),
					'contain' => esc_html__( 'Fit', 'et_builder' ),
					'initial' => esc_html__( 'Actual Size', 'et_builder' ),
				),
				'default'         => 'cover',
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			),
			'background_position' => array(
				'label'           => esc_html__( 'Background Image Position', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'top_left'      => esc_html__( 'Top Left', 'et_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'et_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'et_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'et_builder' ),
					'center'        => esc_html__( 'Center', 'et_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'et_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'et_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'et_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'et_builder' ),
				),
				'default'           => 'center',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			),
			'background_repeat' => array(
				'label'           => esc_html__( 'Background Image Repeat', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'repeat'      => esc_html__( 'Repeat', 'et_builder' ),
					'repeat-x'    => esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
					'repeat-y'    => esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
					'space'       => esc_html__( 'Space', 'et_builder' ),
					'round'       => esc_html__( 'Round', 'et_builder' ),
					'no-repeat'   => esc_html__( 'No Repeat', 'et_builder' ),
				),
				'default'         => 'no-repeat',
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			),
			'background_blend' => array(
				'label'           => esc_html__( 'Background Image Blend', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'normal'      => esc_html__( 'Normal', 'et_builder' ),
					'multiply'    => esc_html__( 'Multiply', 'et_builder' ),
					'screen'      => esc_html__( 'Screen', 'et_builder' ),
					'overlay'     => esc_html__( 'Overlay', 'et_builder' ),
					'darken'      => esc_html__( 'Darken', 'et_builder' ),
					'lighten'     => esc_html__( 'Lighten', 'et_builder' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'et_builder' ),
					'color-burn'  => esc_html__( 'Color Burn', 'et_builder' ),
					'hard-light'  => esc_html__( 'Hard Light', 'et_builder' ),
					'soft-light'  => esc_html__( 'Soft Light', 'et_builder' ),
					'difference'  => esc_html__( 'Difference', 'et_builder' ),
					'exclusion'   => esc_html__( 'Exclusion', 'et_builder' ),
					'hue'         => esc_html__( 'Hue', 'et_builder' ),
					'saturation'  => esc_html__( 'Saturation', 'et_builder' ),
					'color'       => esc_html__( 'Color', 'et_builder' ),
					'luminosity'  => esc_html__( 'Luminosity', 'et_builder' ),
				),
				'default'         => 'normal',
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'left'   => esc_html__( 'Left', 'et_builder' ),
					'center' => esc_html__( 'Center', 'et_builder' ),
					'right'  => esc_html__( 'Right', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose the orientation for the Title/Meta text', 'et_builder' ),
			),
			'text_color' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'color_option',
				'options'           => array(
					'dark'  => esc_html__( 'Dark', 'et_builder' ),
					'light' => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose the color for the Title/Meta text', 'et_builder' ),
			),
			'text_background' => array(
				'label'             => esc_html__( 'Use Text Background Color', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'color_option',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'text_bg_color',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose whether or not use the background color for the Title/Meta text', 'et_builder' ),
			),
			'text_bg_color' => array(
				'label'             => esc_html__( 'Text Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
			),
			'module_bg_color' => array(
				'label'        => esc_html__( 'Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$title              = $this->shortcode_atts['title'];
		$meta               = $this->shortcode_atts['meta'];
		$author             = $this->shortcode_atts['author'];
		$date               = $this->shortcode_atts['date'];
		$date_format        = $this->shortcode_atts['date_format'];
		$categories         = $this->shortcode_atts['categories'];
		$comments           = $this->shortcode_atts['comments'];
		$featured_image     = $this->shortcode_atts['featured_image'];
		$featured_placement = $this->shortcode_atts['featured_placement'];
		$parallax_effect    = $this->shortcode_atts['parallax_effect'];
		$parallax_method    = $this->shortcode_atts['parallax_method'];
		$background_size    = $this->shortcode_atts['background_size'];
		$background_position = $this->shortcode_atts['background_position'];
		$background_repeat  = $this->shortcode_atts['background_repeat'];
		$background_blend   = $this->shortcode_atts['background_blend'];
		$text_orientation   = $this->shortcode_atts['text_orientation'];
		$text_color         = $this->shortcode_atts['text_color'];
		$text_background    = $this->shortcode_atts['text_background'];
		$text_bg_color      = $this->shortcode_atts['text_bg_color'];
		$module_bg_color    = $this->shortcode_atts['module_bg_color'];
		$use_background_color_gradient              = $this->shortcode_atts['use_background_color_gradient'];
		$background_color_gradient_type             = $this->shortcode_atts['background_color_gradient_type'];
		$background_color_gradient_direction        = $this->shortcode_atts['background_color_gradient_direction'];
		$background_color_gradient_direction_radial = $this->shortcode_atts['background_color_gradient_direction_radial'];
		$background_color_gradient_start            = $this->shortcode_atts['background_color_gradient_start'];
		$background_color_gradient_end              = $this->shortcode_atts['background_color_gradient_end'];
		$background_color_gradient_start_position   = $this->shortcode_atts['background_color_gradient_start_position'];
		$background_color_gradient_end_position     = $this->shortcode_atts['background_color_gradient_end_position'];
		$background_images  = array();

		// display the shortcode only on singlular pages
		if ( ! is_singular() ) {
			return;
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
		$this->process_additional_options( $function_name );

		$output = '';
		$featured_image_output = '';
		$parallax_background_contaier = '';

		if ( 'on' === $featured_image && ( 'above' === $featured_placement || 'below' === $featured_placement ) ) {
			$featured_image_output = sprintf( '<div class="et_pb_title_featured_container">%1$s</div>',
				get_the_post_thumbnail( get_the_ID(), 'large' )
			);
		}

		if ( 'on' === $title ) {
			if ( is_et_pb_preview() && isset( $_POST['post_title'] ) && wp_verify_nonce( $_POST['et_pb_preview_nonce'], 'et_pb_preview_nonce' ) ) {
				$post_title = sanitize_text_field( wp_unslash( $_POST['post_title'] ) );
			} else {
				$post_title = get_the_title();
			}

			$output .= sprintf( '<h1 class="entry-title">%s</h1>',
				$post_title
			);
		}

		if ( 'on' === $meta ) {
			$meta_array = array();
			foreach( array( 'author', 'date', 'categories', 'comments' ) as $single_meta ) {
				if ( 'on' === $$single_meta && ( 'categories' !== $single_meta || ( 'categories' === $single_meta && is_singular( 'post' ) ) ) ) {
					 $meta_array[] = $single_meta;
				}
			}

			$output .= sprintf( '<p class="et_pb_title_meta_container">%1$s</p>',
				et_pb_postinfo_meta( $meta_array, $date_format, esc_html__( '0 comments', 'et_builder' ), esc_html__( '1 comment', 'et_builder' ), '% ' . esc_html__( 'comments', 'et_builder' ) )
			);
		}

		if ( 'on' === $use_background_color_gradient ) {
			$has_background_gradient = true;

			$background_images[] = $this->get_gradient( array(
				'type'             => $background_color_gradient_type,
				'direction'        => $background_color_gradient_direction,
				'radial_direction' => $background_color_gradient_direction_radial,
				'color_start'      => $background_color_gradient_start,
				'color_end'        => $background_color_gradient_end,
				'start_position'   => $background_color_gradient_start_position,
				'end_position'     => $background_color_gradient_end_position,
			) );
		}

		if ( 'on' === $featured_image && 'background' === $featured_placement ) {
			$featured_image_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

			if ( 'on' === $parallax_effect ) {
				$parallax_background_contaier = sprintf( '<div class="et_parallax_bg%1$s"></div>',
					'on' === $parallax_method ? ' et_pb_parallax_css' : ''
				);

				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%.et_pb_featured_bg .et_parallax_bg',
					'declaration' => sprintf(
						'background-image: url("%1$s");',
						esc_url( $featured_image_src[0] )
					),
				) );
			} else {
				$has_background_image = true;

				$background_images[] = sprintf(
					'url(%1$s)',
					esc_url( $featured_image_src[0] )
				);

				if ( '' !== $background_size ) {
					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%.et_pb_featured_bg',
						'declaration' => sprintf(
							'background-size: %1$s;',
							esc_html( $background_size )
						),
					) );
				}

				if ( '' !== $background_position ) {
					$processed_position = str_replace( '_', ' ', $background_position );

					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%.et_pb_featured_bg',
						'declaration' => sprintf(
							'background-position: %1$s;',
							esc_html( $processed_position )
						),
					) );
				}

				if ( '' !== $background_repeat ) {
					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%.et_pb_featured_bg',
						'declaration' => sprintf(
							'background-repeat: %1$s;',
							esc_html( $background_repeat )
						),
					) );
				}

				if ( '' !== $background_blend ) {
					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%.et_pb_featured_bg',
						'declaration' => sprintf(
							'background-blend-mode: %1$s;',
							esc_html( $background_blend )
						),
					) );
				}
			}
		}

		if ( ! empty( $background_images ) ) {
			$background_images = array_reverse( $background_images );

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => 'background' === $featured_placement ? '%%order_class%%.et_pb_featured_bg' : '%%order_class%%',
				'declaration' => sprintf(
					'background-image: %1$s;',
					esc_html( join( ', ', $background_images ) )
				),
			) );
		}

		if ( 'on' === $text_background ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_title_container',
				'declaration' => sprintf(
					'background-color: %1$s; padding: 1em 1.5em;',
					esc_html( $text_bg_color )
				),
			) );
		}

		ET_Builder_Element::set_style( $function_name, array(
			'selector'    => '%%order_class%%',
			'declaration' => sprintf(
				'text-align: %1$s;',
				esc_html( $text_orientation )
			),
		) );

		$video_background = $this->video_background();

		$background_layout = 'dark' === $text_color ? 'light' : 'dark';
		$module_class .= ' et_pb_bg_layout_' . $background_layout;

		if ( ! isset ( $has_background_image, $has_background_gradient ) ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $module_bg_color )
				),
			) );
		} else {
			// Force background-color: initial
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => 'background-color: initial;',
			) );
		}

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_post_title %2$s%4$s%8$s">
				%5$s
				%9$s
				%6$s
				<div class="et_pb_title_container">
					%1$s
				</div>
				%7$s
			</div>',
			$output,
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			'on' === $featured_image && 'background' === $featured_placement ? ' et_pb_featured_bg' : '',
			$parallax_background_contaier,
			'on' === $featured_image && 'above' === $featured_placement ? $featured_image_output : '',
			'on' === $featured_image && 'below' === $featured_placement ? $featured_image_output : '',
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background

		);

		return $output;
	}
}
new ET_Builder_Module_Post_Title;

class ET_Builder_Module_Search extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Search', 'et_builder' );
		$this->slug       = 'et_pb_search';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'background_layout',
			'text_orientation',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'include_categories',
			'exclude_posts',
			'exclude_pages',
			'button_color',
			'field_bg',
			'hide_button',
			'placeholder',
			'placeholder_color',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited',
		);

		$this->fields_defaults = array(
			'background_layout' => array( 'light' ),
			'text_orientation'  => array( 'left' ),
		);

		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
					'exceptions'   => esc_html__( 'Exceptions', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'field' => esc_html__( 'Search Field', 'et_builder' ),
					'text'  => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'width' => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
		);
		$this->advanced_options = array(
			'fonts' => array(
				'input' => array(
					'label'    => esc_html__( 'Input', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} input.et_pb_s",
						'placeholder' => true,
						'important' => array( 'line-height' ),
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'button' => array(
					'label'          => esc_html__( 'Button', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} input.et_pb_searchsubmit",
						'important' => array( 'line-height' ),
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'main'      => "{$this->main_css_element} input.et_pb_s",
					'important' => 'all',
				),
			),
		);

		$this->custom_css_options = array(
			'input_field' => array(
				'label'    => esc_html__( 'Input Field', 'et_builder' ),
				'selector' => 'input.et_pb_s',
			),
			'button'      => array(
				'label'    => esc_html__( 'Button', 'et_builder' ),
				'selector' => 'input.et_pb_searchsubmit',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'           => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'left'   => esc_html__( 'Left', 'et_builder' ),
					'right'  => esc_html__( 'Right', 'et_builder' ),
					'center' => esc_html__( 'Center', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'This controls the how your text is aligned within the module.', 'et_builder' ),
			),
			'exclude_pages' => array(
				'label'           => esc_html__( 'Exclude Pages', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'description'     => esc_html__( 'Turning this on will exclude Pages from search results', 'et_builder' ),
				'toggle_slug'     => 'exceptions',
			),
			'exclude_posts' => array(
				'label'           => esc_html__( 'Exclude Posts', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'         => array(
					'include_categories',
				),
				'description'     => esc_html__( 'Turning this on will exclude Posts from search results', 'et_builder' ),
				'toggle_slug'     => 'exceptions',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Exclude Categories', 'et_builder' ),
				'renderer'         => 'et_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'depends_show_if'  => 'off',
				'description'      => esc_html__( 'Choose which categories you would like to exclude from the search results.', 'et_builder' ),
				'toggle_slug'      => 'exceptions',
			),
			'hide_button' => array(
				'label'           => esc_html__( 'Hide Button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turning this on will hide the Search button', 'et_builder' ),
			),
			'placeholder' => array(
				'label'       => esc_html__( 'Placeholder Text', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Type the text you want to use as placeholder for the search field.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'max_width' => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'max_width_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'button_color' => array(
				'label'        => esc_html__( 'Button and Border Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'button',
			),
			'field_bg' => array(
				'label'        => esc_html__( 'Input Field Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'field',
			),
			'placeholder_color' => array(
				'label'        => esc_html__( 'Placeholder Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'field',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$background_layout  = $this->shortcode_atts['background_layout'];
		$text_orientation   = $this->shortcode_atts['text_orientation'];
		$exclude_categories = $this->shortcode_atts['include_categories'];
		$max_width          = $this->shortcode_atts['max_width'];
		$exclude_posts      = $this->shortcode_atts['exclude_posts'];
		$exclude_pages      = $this->shortcode_atts['exclude_pages'];
		$button_color       = $this->shortcode_atts['button_color'];
		$field_bg           = $this->shortcode_atts['field_bg'];
		$hide_button        = $this->shortcode_atts['hide_button'];
		$placeholder        = $this->shortcode_atts['placeholder'];
		$placeholder_color  = $this->shortcode_atts['placeholder_color'];
		$max_width_tablet   = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone    = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited = $this->shortcode_atts['max_width_last_edited'];
		$input_line_height  = $this->shortcode_atts['input_line_height'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );

			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ? $max_width_phone : '',
			);

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name );
		}

		if ( '' !== $button_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input.et_pb_searchsubmit',
				'declaration' => sprintf(
					'background: %1$s !important;border-color:%1$s !important;',
					esc_html( $button_color )
				),
			) );

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input.et_pb_s',
				'declaration' => sprintf(
					'border-color:%1$s !important;',
					esc_html( $button_color )
				),
			) );
		}

		if ( '' !== $field_bg ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input.et_pb_s',
				'declaration' => sprintf(
					'background: %1$s%2$s;',
					esc_html( $field_bg ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $placeholder_color ) {
			$placeholder_selectors = array(
				'%%order_class%% form input.et_pb_s::-webkit-input-placeholder',
				'%%order_class%% form input.et_pb_s::-moz-placeholder',
				'%%order_class%% form input.et_pb_s:-ms-input-placeholder',
			);

			foreach ( $placeholder_selectors as $single_selector ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $single_selector,
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $placeholder_color )
					),
				) );
			}
		}

		if ( '' !== $input_line_height ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input.et_pb_s',
				'declaration' => 'height: auto; min-height: 0;',
			) );
		}

		$custom_margin = explode('|', $this->shortcode_atts['custom_margin']);
		$has_custom_margin = isset( $custom_margin[0], $custom_margin[1], $custom_margin[2],  $custom_margin[3] );
		$custom_margin_units = array();

		if ( $has_custom_margin ) {
			$button_top    = $custom_margin[0];
			$button_bottom = $custom_margin[2];
			$custom_margin_left_unit = et_pb_get_value_unit( $custom_margin[3] );
			$button_right  = ( 0 - floatval( $custom_margin[3] ) ) . $custom_margin_left_unit;

			$custom_margin_units = array(
				et_pb_get_value_unit( $custom_margin[0] ),
				et_pb_get_value_unit( $custom_margin[1] ),
				et_pb_get_value_unit( $custom_margin[2] ),
				$custom_margin_left_unit,
			);

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_search input.et_pb_searchsubmit',
				'declaration' => sprintf(
					'min-height: 0 !important; top: %1$s; right: %2$s; bottom: %3$s;',
					esc_html( $button_top ),
					esc_html( $button_right ),
					esc_html( $button_bottom )
				),
			) );
		}

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";
		$class .= 'on' === $hide_button ? ' et_pb_hide_search_button' : '';

		if ( ! empty( $custom_margin_units ) && in_array( '%', $custom_margin_units ) ) {
			$class .= " et_pb_search_percentage_custom_margin";
		}

		$output = sprintf(
			'<div%3$s class="et_pb_search%2$s%4$s">
				<form role="search" method="get" class="et_pb_searchform" action="%1$s">
					<div>
						<label class="screen-reader-text" for="s">%9$s</label>
						<input type="text" value="" name="s" class="et_pb_s"%8$s>
						<input type="hidden" name="et_pb_searchform_submit" value="et_search_proccess" />
						%5$s
						%6$s
						%7$s
						<input type="submit" value="%10$s" class="et_pb_searchsubmit">
					</div>
				</form>
			</div> <!-- .et_pb_text -->',
			esc_url( home_url( '/' ) ),
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $exclude_categories ? sprintf( '<input type="hidden" name="et_pb_search_cat" value="%1$s" />', esc_attr( $exclude_categories ) ) : '',
			'on' !== $exclude_posts ? '<input type="hidden" name="et_pb_include_posts" value="yes" />' : '',
			'on' !== $exclude_pages ? '<input type="hidden" name="et_pb_include_pages" value="yes" />' : '',
			'' !== $placeholder ? sprintf( ' placeholder="%1$s"', esc_attr( $placeholder ) ) : '',
			esc_html__( 'Search for:', 'et_builder' ),
			esc_attr__( 'Search', 'et_builder' )
		);

		return $output;
	}
}
new ET_Builder_Module_Search;

class ET_Builder_Module_Comments extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Comments', 'et_builder' );
		$this->slug       = 'et_pb_comments';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'admin_label',
			'module_id',
			'module_class',
			'form_background_color',
			'input_border_radius',
			'show_count',
			'show_reply',
			'show_avatar',
			'background_layout',
		);

		$this->fields_defaults = array(
			'input_border_radius' => array( '0px', 'add_default_setting' ),
			'background_layout'   => array( 'light' ),
			'show_count'          => array( 'on' ),
			'show_reply'          => array( 'on' ),
			'show_avatar'         => array( 'on' ),
		);

		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'   => esc_html__( 'Elements', 'et_builder' ),
					'background' => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'fonts' => array(
				'body' => array(
					'label'          => esc_html__( 'Comment', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .comment-content p",
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'form_field' => array(
					'label'          => esc_html__( 'Field', 'et_builder' ),
					'css'            => array(
						'main'      => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url'], {$this->main_css_element} #commentform label",
						'important' => 'all',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '18px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'meta' => array(
					'label'          => esc_html__( 'Meta', 'et_builder' ),
					'css'            => array(
						'main'      => "{$this->main_css_element} .comment_postinfo span",
						'important' => 'all',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'border' => array(
				'label'    => esc_html__( 'Field border', 'et_builder' ),
				'css'      => array(
					'main'      => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url']",
					'important' => 'all',
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element}.et_pb_comments_module .et_pb_button",
					),
				),
			),
		);

		$this->custom_css_options = array(
			'main_header' => array(
				'label'    => esc_html__( 'Comments Count', 'et_builder' ),
				'selector' => 'h1#comments',
			),
			'comment_body' => array(
				'label'    => esc_html__( 'Comment Body', 'et_builder' ),
				'selector' => '.comment-body',
			),
			'comment_meta' => array(
				'label'    => esc_html__( 'Comment Meta', 'et_builder' ),
				'selector' => '.comment_postinfo',
			),
			'comment_content' => array(
				'label'    => esc_html__( 'Comment Content', 'et_builder' ),
				'selector' => '.comment_area .comment-content',
			),
			'comment_avatar' => array(
				'label'    => esc_html__( 'Comment Avatar', 'et_builder' ),
				'selector' => '.comment_avatar',
			),
			'reply_button' => array(
				'label'    => esc_html__( 'Reply Button', 'et_builder' ),
				'selector' => '.comment-reply-link.et_pb_button',
			),
			'new_title' => array(
				'label'    => esc_html__( 'New Comment Title', 'et_builder' ),
				'selector' => 'h3#reply-title',
			),
			'message_field' => array(
				'label'    => esc_html__( 'Message Field', 'et_builder' ),
				'selector' => '.comment-form-comment textarea#comment',
			),
			'name_field' => array(
				'label'    => esc_html__( 'Name Field', 'et_builder' ),
				'selector' => '.comment-form-author input',
			),
			'email_field' => array(
				'label'    => esc_html__( 'Email Field', 'et_builder' ),
				'selector' => '.comment-form-email input',
			),
			'website_field' => array(
				'label'    => esc_html__( 'Website Field', 'et_builder' ),
				'selector' => '.comment-form-url input',
			),
			'submit_button' => array(
				'label'    => esc_html__( 'Submit Button', 'et_builder' ),
				'selector' => '.form-submit .et_pb_button#et_pb_submit',
			),
		);
	}

	function get_fields() {

		$fields = array(
			'show_avatar' => array(
				'label'           => esc_html__( 'Show author avatar', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
			),
			'show_reply' => array(
				'label'           => esc_html__( 'Show reply button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
			),
			'show_count' => array(
				'label'           => esc_html__( 'Show comments count', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),
			'form_background_color' => array(
				'label'        => esc_html__( 'Fields Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'input_border_radius' => array(
				'label'           => esc_html__( 'Fields Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'border',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			)
		);

		return $fields;
	}

	/**
	 * Get comments markup for comments module
	 *
	 * @return string of comment section markup
	 */
	static function get_comments() {
		global $et_pb_comments_print;

		// Globally flag that comment module is being printed
		$et_pb_comments_print = true;

		// remove filters to make sure comments module rendered correctly if the below filters were applied earlier.
		remove_filter( 'get_comments_number', '__return_zero' );
		remove_filter( 'comments_open', '__return_false' );
		remove_filter( 'comments_array', '__return_empty_array' );

		ob_start();
		comments_template( '', true );
		$comments_content = ob_get_contents();
		ob_end_clean();

		// Globally flag that comment module has been printed
		$et_pb_comments_print = false;

		return $comments_content;
	}

	function et_pb_comments_template() {
		return dirname(__FILE__) . '/comments_template.php';
	}

	function et_pb_comments_submit_button( $submit_button ) {
		return sprintf(
			'<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			esc_attr( 'submit' ),
			esc_attr( 'et_pb_submit' ),
			esc_attr( 'submit' ),
			esc_html__( 'Submit Comment', 'et_builder' )
		);
	}

	function et_pb_modify_comments_request( $params ) {
		// modify the request parameters the way it doesn't change the result just to make request with unique parameters
		$params->query_vars['type__not_in'] = 'et_pb_comments_random_type_' . $this->et_pb_unique_comments_module_class;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id             = $this->shortcode_atts['module_id'];
		$module_class          = $this->shortcode_atts['module_class'];
		$button_custom         = $this->shortcode_atts['custom_button'];
		$custom_icon           = $this->shortcode_atts['button_icon'];
		$form_background_color = $this->shortcode_atts['form_background_color'];
		$input_border_radius   = $this->shortcode_atts['input_border_radius'];
		$show_avatar           = $this->shortcode_atts['show_avatar'];
		$show_reply            = $this->shortcode_atts['show_reply'];
		$show_count            = $this->shortcode_atts['show_count'];
		$background_layout     = $this->shortcode_atts['background_layout'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$this->et_pb_unique_comments_module_class = $module_class; // use this variable to make the comments request unique for each module instance

		if ( '' !== $form_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% #commentform textarea, %%order_class%% #commentform input[type="text"], %%order_class%% #commentform input[type="email"], %%order_class%% #commentform input[type="url"]',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $form_background_color )
				),
			) );
		}

		if ( ! in_array( $input_border_radius, array( '', '0' ) ) ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% #commentform textarea, %%order_class%% #commentform input[type="text"], %%order_class%% #commentform input[type="email"], %%order_class%% #commentform input[type="url"]',
				'declaration' => sprintf(
					'-moz-border-radius: %1$s; -webkit-border-radius: %1$s; border-radius: %1$s;',
					esc_html( et_builder_process_range_value( $input_border_radius ) )
				),
			) );
		}

		$module_class .= 'off' === $show_avatar ? ' et_pb_no_avatar' : '';
		$module_class .= 'off' === $show_reply ? ' et_pb_no_reply_button' : '';
		$module_class .= 'off' === $show_count ? ' et_pb_no_comments_count' : '';

		$module_class .= ' et_pb_bg_layout_' . $background_layout;

		// Modify the comments request to make sure it's unique.
		// Otherwise WP generates SQL error and doesn't allow multiple comments sections on single page
		add_action( 'pre_get_comments', array( $this, 'et_pb_modify_comments_request' ), 1 );

		// include custom comments_template to display the comment section with Divi style
		add_filter( 'comments_template', array( $this, 'et_pb_comments_template' ) );

		// Modify submit button to be advanced button style ready
		add_filter( 'comment_form_submit_button', array( $this, 'et_pb_comments_submit_button' ) );

		$comments_content = self::get_comments();

		// remove all the actions and filters to not break the default comments section from theme
		remove_filter( 'comments_template', array( $this, 'et_pb_comments_template' ) );
		remove_action( 'pre_get_comments', array( $this, 'et_pb_modify_comments_request' ), 1 );

		$comments_custom_icon = 'on' === $button_custom ? $custom_icon : '';

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_comments_module %2$s"%4$s>
				%1$s
			</div>',
			$comments_content,
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			'' !== $comments_custom_icon ? sprintf( ' data-icon="%1$s"', esc_attr( et_pb_process_font_icon( $comments_custom_icon ) ) ) : ''
		);

		return $output;
	}
}
new ET_Builder_Module_Comments;

class ET_Builder_Module_Posts_Navigation extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Post Navigation', 'et_builder' );
		$this->slug             = 'et_pb_post_nav';
		$this->fb_support       = true;
		$this->main_css_element = '%%order_class%%';

		$this->defaults = array();

		$this->fields_defaults = array(
			'hide_prev'          => array( 'off' ),
			'hide_next'          => array( 'off' ),
		);

		$this->whitelisted_fields = array(
			'in_same_term',
			'taxonomy_name',
			'hide_prev',
			'hide_next',
			'prev_text',
			'next_text',
			'module_id',
			'module_class',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'categories'   => esc_html__( 'Categories', 'et_builder' ),
					'navigation'   => esc_html__( 'Navigation', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Links', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} span a, {$this->main_css_element} span a span",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'border'                => array(
				'css' => array(
					'main' => "{$this->main_css_element} span.nav-previous a, {$this->main_css_element} span.nav-next a",
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'main' => "{$this->main_css_element} span.nav-previous a, {$this->main_css_element} span.nav-next a",
				),
			),
		);

		$this->custom_css_options = array(
			'links' => array(
				'label'    => esc_html__( 'Links', 'et_builder' ),
				'selector' => 'span a',
			),
			'prev_link' => array(
				'label'    => esc_html__( 'Previous Link', 'et_builder' ),
				'selector' => 'span.nav-previous a',
			),
			'prev_link_arrow' => array(
				'label'    => esc_html__( 'Previous Link Arrow', 'et_builder' ),
				'selector' => 'span.nav-previous a span',
			),
			'next_link' => array(
				'label'    => esc_html__( 'Next Link', 'et_builder' ),
				'selector' => 'span.nav-next a',
			),
			'next_link_arrow' => array(
				'label'    => esc_html__( 'Next Link Arrow', 'et_builder' ),
				'selector' => 'span.nav-next a span',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'in_same_term' => array(
				'label'           => esc_html__( 'In the same category', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'taxonomy_name',
				),
				'description'      => esc_html__( 'Here you can define whether previous and next posts must be within the same taxonomy term as the current post', 'et_builder' ),
				'toggle_slug'      => 'categories',
				'computed_affects' => array(
					'__posts_navigation',
				),
			),
			'taxonomy_name' => array(
				'label'            => esc_html__( 'Custom Taxonomy Name', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'depends_show_if'  => 'on',
				'description'      => esc_html__( 'Leave blank if you\'re using this module on a Project or Post. Otherwise type the taxonomy name to make the \'In the Same Category\' option work correctly', 'et_builder' ),
				'toggle_slug'      => 'categories',
				'computed_affects' => array(
					'__posts_navigation',
				),
			),
			'hide_prev' => array(
				'label'           => esc_html__( 'Hide Previous Post Link', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'prev_text',
				),
				'toggle_slug'       => 'navigation',
				'description'       => esc_html__( 'Here you can choose whether to hide or show the previous post link', 'et_builder' ),
			),
			'hide_next' => array(
				'label'           => esc_html__( 'Hide Next Post Link', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'next_text',
				),
				'toggle_slug'       => 'navigation',
				'description'       => esc_html__( 'Here you can choose whether to hide or show the next post link', 'et_builder' ),
			),
			'prev_text' => array(
				'label'           => esc_html__( 'Previous Link Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'off',
				'computed_affects' => array(
					'__posts_navigation',
				),
				'description'     => et_get_safe_localization( __( 'Define custom text for the previous link. You can use the <strong>%title</strong> variable to include the post title. Leave blank for default.', 'et_builder' ) ),
				'toggle_slug'     => 'main_content',
			),
			'next_text' => array(
				'label'           => esc_html__( 'Next Link Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'off',
				'computed_affects' => array(
					'__posts_navigation',
				),
				'description'     => et_get_safe_localization( __( 'Define custom text for the next link. You can use the <strong>%title</strong> variable to include the post title. Leave blank for default.', 'et_builder' ) ),
				'toggle_slug'     => 'main_content',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__posts_navigation' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Posts_Navigation', 'get_posts_navigation' ),
				'computed_depends_on' => array(
					'in_same_term',
					'taxonomy_name',
					'prev_text',
					'next_text'
				),
			),
		);
		return $fields;
	}

	/**
	 * Get prev and next post link data for frontend builder's post navigation module component
	 *
	 * @param int    post ID
	 * @param bool   show posts which uses same link only or not
	 * @param string excluded terms name
	 * @param string taxonomy name for in_same_terms
	 *
	 * @return string JSON encoded array of post's next and prev link
	 */
	static function get_posts_navigation( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $post;

		$defaults = array(
			'in_same_term'   => 'off',
			'taxonomy_name'  => 'category',
			'prev_text'      => '%title',
			'next_text'      => '%title',
		);

		$args = wp_parse_args( $args, $defaults );

		// taxonomy name overwrite if in_same_term option is set to off and no taxonomy name defined
		if ( '' === $args['taxonomy_name'] || 'on' !== $args['in_same_term'] ) {
			$is_singular_project   = isset( $conditional_tags['is_singular_project'] ) ? $conditional_tags['is_singular_project'] === 'true' : is_singular( 'project' );
			$args['taxonomy_name'] = $is_singular_project ? 'project_category' : 'category';
		}

		$in_same_term = ! $args['in_same_term'] || 'off' === $args['in_same_term'] ? false : true;

		if ( ! isset( $post ) && defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST['et_post_id'] ) ) {
			$post_id = sanitize_text_field( $_POST['et_post_id'] );
		} else if ( isset( $current_page['id'] ) ) {
			// Overwrite global $post value in this scope
			$post_id = intval( $current_page['id'] );
		} else if ( is_object( $post ) && isset( $post->ID ) ) {
			$post_id = $post->ID;
		} else {
			return array(
				'next' => '',
				'prev' => '',
			);
		}

		// Set current post as global $post
		$post = get_post( $post_id );

		// Get next post
		$next_post = get_next_post( $in_same_term, '', $args['taxonomy_name'] );

		$next = new stdClass();

		if ( ! empty( $next_post ) ) {

			$next_title = isset($next_post->post_title) ? esc_html( $next_post->post_title ) : esc_html__( 'Next Post' );

			$next_date = mysql2date( get_option( 'date_format' ), $next_post->post_date );
			$next_permalink = isset($next_post->ID) ? esc_url( get_the_permalink( $next_post->ID ) ) : '';

			$next_processed_title = '' === $args['next_text'] ? '%title' : $args['next_text'];

			// process Wordpress' wildcards
			$next_processed_title = str_replace( '%title', $next_title, $next_processed_title );
			$next_processed_title = str_replace( '%date', $next_date, $next_processed_title );
			$next_processed_title = str_replace( '%link', $next_permalink, $next_processed_title );

			$next->title = $next_processed_title;
			$next->id = isset($next_post->ID) ? intval( $next_post->ID ) : '';
			$next->permalink = $next_permalink;
		}

		// Get prev post
		$prev_post = get_previous_post( $in_same_term, '', $args['taxonomy_name'] );

		$prev = new stdClass();

		if ( ! empty( $prev_post ) ) {

			$prev_title = isset($prev_post->post_title) ? esc_html( $prev_post->post_title ) : esc_html__( 'Previous Post' );

			$prev_date = mysql2date( get_option( 'date_format' ), $prev_post->post_date );

			$prev_permalink = isset($prev_post->ID) ? esc_url( get_the_permalink( $prev_post->ID ) ) : '';

			$prev_processed_title = '' === $args['prev_text'] ? '%title' : $args['prev_text'];

			// process Wordpress' wildcards
			$prev_processed_title = str_replace( '%title', $prev_title, $prev_processed_title );
			$prev_processed_title = str_replace( '%date', $prev_date, $prev_processed_title );
			$prev_processed_title = str_replace( '%link', $prev_permalink, $prev_processed_title );

			$prev->title = $prev_processed_title;
			$prev->id = isset($prev_post->ID) ? intval( $prev_post->ID ) : '';
			$prev->permalink = $prev_permalink;
		}

		// Formatting returned value
		$posts_navigation = array(
			'next' => $next,
			'prev' => $prev,
		);

		return $posts_navigation;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id     = $this->shortcode_atts['module_id'];
		$module_class  = $this->shortcode_atts['module_class'];
		$in_same_term  = $this->shortcode_atts['in_same_term'];
		$taxonomy_name = $this->shortcode_atts['taxonomy_name'];
		$hide_prev     = $this->shortcode_atts['hide_prev'];
		$hide_next     = $this->shortcode_atts['hide_next'];
		$prev_text     = $this->shortcode_atts['prev_text'];
		$next_text     = $this->shortcode_atts['next_text'];

		// do not output anything if both prev and next links are disabled
		if ( 'on' === $hide_prev && 'on' === $hide_next ) {
			return;
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$posts_navigation = self::get_posts_navigation( array(
			'in_same_term'  => $in_same_term,
			'taxonomy_name' => $taxonomy_name,
			'prev_text'     => $prev_text,
			'next_text'     => $next_text,
		) );

		ob_start();

		if ( 'on' !== $hide_prev && ! empty( $posts_navigation['prev']->permalink ) ) {
			$prev_link_text = '' !== $prev_text ? $prev_text : $posts_navigation['prev']->title;

			?>
				<span class="nav-previous">
					<a href="<?php echo esc_url( $posts_navigation['prev']->permalink ); ?>" rel="prev">
						<span class="meta-nav">&larr; </span><span><?php echo esc_html( $posts_navigation['prev']->title ); ?></span>
					</a>
				</span>
			<?php
		}

		if ( 'on' !== $hide_next && ! empty( $posts_navigation['next']->permalink ) ) {
			$next_link_text = '' !== $next_text ? $next_text : $posts_navigation['next']->title;

			?>
				<span class="nav-next">
					<a href="<?php echo esc_url( $posts_navigation['next']->permalink ); ?>" rel="next">
						<span><?php echo esc_html( $posts_navigation['next']->title ); ?></span><span class="meta-nav"> &rarr;</span>
					</a>
				</span>
			<?php
		}

		$page_links = ob_get_contents();

		ob_end_clean();

		$output = sprintf(
			'<div class="et_pb_posts_nav et_pb_module nav-single%2$s"%1$s>
				%3$s
			</div>',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			$page_links
		);

		return $output;
	}
}
new ET_Builder_Module_Posts_Navigation;

class ET_Builder_Module_Fullwidth_Header extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Fullwidth Header', 'et_builder' );
		$this->slug             = 'et_pb_fullwidth_header';
		$this->fb_support       = true;
		$this->fullwidth        = true;
		$this->main_css_element = '%%order_class%%';

		$this->whitelisted_fields = array(
			'title',
			'subhead',
			'background_layout',
			'text_orientation',
			'header_fullscreen',
			'header_scroll_down',
			'scroll_down_icon',
			'scroll_down_icon_color',
			'scroll_down_icon_size',
			'scroll_down_icon_size_tablet',
			'scroll_down_icon_size_phone',
			'scroll_down_icon_size_last_edited',
			'title_font_color',
			'subhead_font_color',
			'content_font_color',
			'max_width',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited',
			'button_one_text',
			'button_one_url',
			'button_two_text',
			'button_two_url',
			'background_image',
			'background_color',
			'background_overlay_color',
			'parallax',
			'parallax_method',
			'background_size',
			'background_position',
			'background_repeat',
			'background_blend',
			'logo_image_url',
			'logo_title',
			'logo_alt_text',
			'content_orientation',
			'header_image_url',
			'image_orientation',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'background_layout'   => array( 'light' ),
			'text_orientation'    => array( 'left' ),
			'header_fullscreen'   => array( 'off' ),
			'header_scroll_down'  => array( 'off' ),
			'scroll_down_icon'    => array( ';', 'add_default_setting' ),
			'parallax'            => array( 'off' ),
			'parallax_method'     => array( 'off' ),
			'content_orientation' => array( 'center' ),
			'image_orientation'   => array( 'center' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'links'        => esc_html__( 'Links', 'et_builder' ),
					'images'       => esc_html__( 'Images', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'      => esc_html__( 'Layout', 'et_builder' ),
					'scroll_down' => esc_html__( 'Scroll Down Icon', 'et_builder' ),
					'image'       => esc_html__( 'Image', 'et_builder' ),
					'text'        => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'width'       => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%%.et_pb_fullwidth_header .header-content h1",
					),
					'font_size' => array(
						'default'      => '30px',
					),
					'hide_line_height'    => true,
					'hide_text_color'     => true,
					'hide_letter_spacing' => true,
				),
				'content' => array(
					'label'    => esc_html__( 'Content', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%%.et_pb_fullwidth_header p",
					),
					'font_size' => array(
						'default'      => '14px',
					),
					'hide_line_height'    => true,
					'hide_text_color'     => true,
					'hide_letter_spacing' => true,
				),
				'subhead' => array(
					'label'    => esc_html__( 'Subhead', 'et_builder' ),
					'css'      => array(
						'main' => "%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_subhead",
					),
					'hide_line_height'    => true,
					'hide_text_color'     => true,
					'hide_letter_spacing' => true,
				),
			),
			'button' => array(
				'button_two' => array(
					'label' => esc_html__( 'Button Two', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_button_two.et_pb_button",
					),
				),
				'button_one' => array(
					'label' => esc_html__( 'Button One', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_button_one.et_pb_button",
					),
				),
			),
			'background' => array(
				'use_background_color'          => true,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'css'                           => array(
					'main' => '.et_pb_fullwidth_header%%order_class%%',
				),
			),
		);

		$this->custom_css_options = array(
			'header_container' => array(
				'label'    => esc_html__( 'Header Container', 'et_builder' ),
				'selector' => '.et_pb_fullwidth_header_container',
			),
			'header_image' => array(
				'label'    => esc_html__( 'Header Image', 'et_builder' ),
				'selector' => '.et_pb_fullwidth_header_container .header-image img',
			),
			'logo' => array(
				'label'    => esc_html__( 'Logo', 'et_builder' ),
				'selector' => '.header-content img.header-logo',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => '.header-content h1',
			),
			'subtitle' => array(
				'label'    => esc_html__( 'Subtitle', 'et_builder' ),
				'selector' => '.header-content .et_pb_fullwidth_header_subhead',
			),
			'button_1' => array(
				'label'    => esc_html__( 'Button One', 'et_builder' ),
				'selector' => '.header-content-container .header-content .et_pb_button_one.et_pb_button',
			),
			'button_2' => array(
				'label'    => esc_html__( 'Button Two', 'et_builder' ),
				'selector' => '.header-content-container .header-content .et_pb_button_two.et_pb_button',
			),
			'scroll_button' => array(
				'label'    => esc_html__( 'Scroll Down Button', 'et_builder' ),
				'selector' => '.et_pb_fullwidth_header_scroll a .et-pb-icon',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter your page title here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'subhead' => array(
				'label'           => esc_html__( 'Subheading Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to use a subhead, add it here. Your subhead will appear below your title in a small font.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text & Logo Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'layout',
				'description'       => esc_html__( 'This controls the how your text is aligned within the module.', 'et_builder' ),
			),

			'header_fullscreen' => array(
				'label'           => esc_html__( 'Make Fullscreen', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'content_orientation',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'description'     => esc_html__( 'Here you can choose whether the header is expanded to fullscreen size.', 'et_builder' ),
			),
			'header_scroll_down' => array(
				'label'           => esc_html__( 'Show Scroll Down Button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'scroll_down_icon',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'scroll_down',
				'description'       => esc_html__( 'Here you can choose whether the scroll down button is shown.', 'et_builder' ),
			),
			'scroll_down_icon' => array(
				'label'               => esc_html__( 'Icon', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_down_icon_list',
				'renderer_with_field' => true,
				'description'         => esc_html__( 'Choose an icon to display for the scroll down button.', 'et_builder' ),
				'depends_show_if'     => 'on',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'scroll_down',
			),
			'scroll_down_icon_color' => array(
				'label'             => esc_html__( 'Scroll Down Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'scroll_down',
			),
			'scroll_down_icon_size' => array(
				'label'           => esc_html__( 'Scroll Down Icon Size', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'mobile_options'  => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'scroll_down',
			),
			'scroll_down_icon_size_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'scroll_down',
			),
			'scroll_down_icon_size_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'scroll_down',
			),
			'scroll_down_icon_size_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'scroll_down',
			),
			'title_font_color' => array(
				'label'             => esc_html__( 'Title Font Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'title',
			),
			'subhead_font_color' => array(
				'label'             => esc_html__( 'Subhead Font Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'subhead',
			),
			'content_font_color' => array(
				'label'             => esc_html__( 'Content Font Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'content',
			),
			'max_width' => array(
				'label'           => esc_html__( 'Text Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'max_width_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'button_one_text' => array(
				'label'           => sprintf( esc_html__( 'Button %1$s Text', 'et_builder' ), '#1' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the text for the Button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_one_url' => array(
				'label'           => sprintf( esc_html__( 'Button %1$s URL', 'et_builder' ), '#1' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the URL for the Button.', 'et_builder' ),
				'toggle_slug'     => 'links',
			),
			'button_two_text' => array(
				'label'           => sprintf( esc_html__( 'Button %1$s Text', 'et_builder' ), '#2' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the text for the Button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_two_url' => array(
				'label'           => sprintf( esc_html__( 'Button %1$s URL', 'et_builder' ), '#2' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the URL for the Button.', 'et_builder' ),
				'toggle_slug'     => 'links',
			),
			'background_overlay_color' => array(
				'label'             => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'toggle_slug'       => 'background',
			),
			'logo_image_url' => array(
				'label'              => esc_html__( 'Logo Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'affects'            => array(
					'logo_alt_text',
					'logo_title',
				),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'images',
			),
			'logo_alt_text' => array(
				'label'           => esc_html__( 'Logo Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_default' => true,
				'depends_to'      => array(
					'logo_image_url',
				),
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'logo_title' => array(
				'label'           => esc_html__( 'Logo Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_default' => true,
				'depends_to'      => array(
					'logo_image_url',
				),
				'description'     => esc_html__( 'This defines the HTML Title text.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'content_orientation' => array(
				'label'           => esc_html__( 'Text Vertical Alignment', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'center'  => esc_html__( 'Center', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'This setting determines the vertical alignment of your content. Your content can either be vertically centered, or aligned to the bottom.', 'et_builder' ),
				'depends_show_if' => 'on',
			),

			'header_image_url' => array(
				'label'              => esc_html__( 'Header Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'images',
			),
			'image_orientation' => array(
				'label'           => esc_html__( 'Image Vertical Alignment', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'center'  => esc_html__( 'Vertically Centered', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image',
				'description'     => esc_html__( 'This controls the orientation of the image within the module.', 'et_builder' ),
			),
			'content_new' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Content entered here will appear below the subheading text.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                    = $this->shortcode_atts['module_id'];
		$module_class                 = $this->shortcode_atts['module_class'];
		$title                        = $this->shortcode_atts['title'];
		$subhead                      = $this->shortcode_atts['subhead'];
		$background_layout            = $this->shortcode_atts['background_layout'];
		$text_orientation             = $this->shortcode_atts['text_orientation'];
		$title_font_color             = $this->shortcode_atts['title_font_color'];
		$subhead_font_color           = $this->shortcode_atts['subhead_font_color'];
		$content_font_color           = $this->shortcode_atts['content_font_color'];
		$button_one_text              = $this->shortcode_atts['button_one_text'];
		$button_one_url               = $this->shortcode_atts['button_one_url'];
		$button_two_text              = $this->shortcode_atts['button_two_text'];
		$button_two_url               = $this->shortcode_atts['button_two_url'];
		$header_fullscreen            = $this->shortcode_atts['header_fullscreen'];
		$header_scroll_down           = $this->shortcode_atts['header_scroll_down'];
		$scroll_down_icon             = $this->shortcode_atts['scroll_down_icon'];
		$scroll_down_icon_color       = $this->shortcode_atts['scroll_down_icon_color'];
		$scroll_down_icon_size        = $this->shortcode_atts['scroll_down_icon_size'];
		$scroll_down_icon_size_tablet = $this->shortcode_atts['scroll_down_icon_size_tablet'];
		$scroll_down_icon_size_phone  = $this->shortcode_atts['scroll_down_icon_size_phone'];
		$scroll_down_icon_size_last_edited = $this->shortcode_atts['scroll_down_icon_size_last_edited'];
		$background_image             = $this->shortcode_atts['background_image'];
		$background_overlay_color     = $this->shortcode_atts['background_overlay_color'];
		$parallax                     = $this->shortcode_atts['parallax'];
		$parallax_method              = $this->shortcode_atts['parallax_method'];
		$logo_image_url               = $this->shortcode_atts['logo_image_url'];
		$header_image_url             = $this->shortcode_atts['header_image_url'];
		$content_orientation          = $this->shortcode_atts['content_orientation'];
		$image_orientation            = $this->shortcode_atts['image_orientation'];
		$custom_icon_1                = $this->shortcode_atts['button_one_icon'];
		$button_custom_1              = $this->shortcode_atts['custom_button_one'];
		$custom_icon_2                = $this->shortcode_atts['button_two_icon'];
		$button_custom_2              = $this->shortcode_atts['custom_button_two'];
		$max_width                    = $this->shortcode_atts['max_width'];
		$max_width_tablet             = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone              = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited        = $this->shortcode_atts['max_width_last_edited'];
		$logo_title                   = $this->shortcode_atts['logo_title'];
		$logo_alt_text                = $this->shortcode_atts['logo_alt_text'];

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );
			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ?  $max_width_phone : '',
			);


			$additional_css = ' !important;';

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%% .header-content', 'max-width', $function_name, $additional_css );
		}

		if ( '' !== $title_font_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_header .header-content h1',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $title_font_color )
				),
			) );
		}

		if ( '' !== $subhead_font_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_subhead',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $subhead_font_color )
				),
			) );
		}

		if ( '' !== $content_font_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_header p',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $content_font_color )
				),
			) );
		}

		if ( '' !== $scroll_down_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a .et-pb-icon',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $scroll_down_icon_color )
				),
			) );
		}

		if ( '' !== $scroll_down_icon_size || '' !== $scroll_down_icon_size_tablet || '' !== $scroll_down_icon_size_phone ) {
			$icon_size_responsive_active = et_pb_get_responsive_status( $scroll_down_icon_size_last_edited );

			$icon_size_values = array(
				'desktop' => $scroll_down_icon_size,
				'tablet'  => $icon_size_responsive_active ? $scroll_down_icon_size_tablet : '',
				'phone'   => $icon_size_responsive_active ? $scroll_down_icon_size_phone : '',
			);

			et_pb_generate_responsive_css( $icon_size_values, '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_scroll a .et-pb-icon', 'font-size', $function_name );
		}

		if ( '' !== $background_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_header .et_pb_fullwidth_header_overlay',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_overlay_color )
				),
			) );
		}

		$button_output = '';
		if ( '' !== $button_one_text ) {
			$button_output .= sprintf(
				'<a href="%2$s" class="et_pb_more_button et_pb_button et_pb_button_one%4$s"%3$s>%1$s</a>',
				( '' !== $button_one_text ? esc_attr( $button_one_text ) : '' ),
				( '' !== $button_one_url ? esc_url( $button_one_url ) : '#' ),
				'' !== $custom_icon_1 && 'on' === $button_custom_1 ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon_1 ) )
				) : '',
				'' !== $custom_icon_1 && 'on' === $button_custom_1 ? ' et_pb_custom_button_icon' : ''
			);
		}

		if ( '' !== $button_two_text ) {
			$button_output .= sprintf(
				'<a href="%2$s" class="et_pb_more_button et_pb_button et_pb_button_two%4$s"%3$s>%1$s</a>',
				( '' !== $button_two_text ? esc_attr( $button_two_text ) : '' ),
				( '' !== $button_two_url ? esc_url( $button_two_url ) : '#' ),
				'' !== $custom_icon_2 && 'on' === $button_custom_2 ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon_2 ) )
				) : '',
				'' !== $custom_icon_2 && 'on' === $button_custom_2 ? ' et_pb_custom_button_icon' : ''
			);
		}

		$video_background = $this->video_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";

		$header_content = '';
		if ( '' !== $title || '' !== $subhead || '' !== $content || '' !== $button_output || '' !== $logo_image_url ) {
			$logo_image = '';
			if ( '' !== $logo_image_url ){
				$logo_image = sprintf(
					'<img src="%1$s" alt="%2$s"%3$s class="header-logo" />',
					esc_url( $logo_image_url ),
					esc_attr( $logo_alt_text ),
					( '' !== $logo_title ? sprintf( ' title="%1$s"', esc_attr( $logo_title ) ) : '' )
				);
			}
			$header_content = sprintf(
				'<div class="header-content-container%6$s">
					<div class="header-content">
						%3$s
						%1$s
						%2$s
						%4$s
						%5$s
					</div>
				</div>',
				( $title ? sprintf( '<h1>%1$s</h1>', $title ) : '' ),
				( $subhead ? sprintf( '<span class="et_pb_fullwidth_header_subhead">%1$s</span>', $subhead ) : '' ),
				$logo_image,
				$this->shortcode_content,
				( '' !== $button_output ? $button_output : '' ),
				( '' !== $content_orientation ? sprintf( ' %1$s', $content_orientation ) : '' )
			);
		}

		$header_image = '';
		if ( '' !== $header_image_url ) {
			$header_image = sprintf(
				'<div class="header-image-container%2$s">
					<div class="header-image">
						<img src="%1$s" />
					</div>
				</div>',
				( '' !== $header_image_url ? esc_url( $header_image_url ) : ''),
				( '' !== $image_orientation ? sprintf( ' %1$s', $image_orientation ) : '' )
			);

			$module_class .= ' et_pb_header_with_image';

		}

		$scroll_down_output = '';
		if ( 'off' !== $header_scroll_down || '' !== $scroll_down_icon ) {
			$scroll_down_output .= sprintf(
				'<a href="#"><span class="scroll-down et-pb-icon">%1$s</span></a>',
				esc_html( et_pb_process_font_icon( $scroll_down_icon, 'et_pb_get_font_down_icon_symbols' ) )
			);
		}

		$output = sprintf(
			'<section%9$s class="et_pb_fullwidth_header%1$s%7$s%8$s%10$s%11$s">
				%6$s
				%12$s
				<div class="et_pb_fullwidth_header_container%5$s">
					%2$s
					%3$s
				</div>
				<div class="et_pb_fullwidth_header_overlay"></div>
				<div class="et_pb_fullwidth_header_scroll">%4$s</div>
			</section>',
			( 'off' !== $header_fullscreen ? ' et_pb_fullscreen' : '' ),
			( '' !== $header_content ? $header_content : '' ),
			( '' !== $header_image ? $header_image : '' ),
			( 'off' !== $header_scroll_down ? $scroll_down_output : '' ),
			( '' !== $text_orientation ? sprintf( ' %1$s', esc_attr( $text_orientation ) ) : '' ),
			( '' !== $background_image && 'on' === $parallax
				? sprintf(
					'<div class="et_parallax_bg%2$s" style="background-image: url(%1$s);"></div>',
					esc_attr( $background_image ),
					( 'off' === $parallax_method ? ' et_pb_parallax_css' : '' )
				)
				: ''
			),
			( '' !== $background_image && 'on' === $parallax ? ' et_pb_section_parallax' : '' ),
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background
		);

		return $output;
	}
}
new ET_Builder_Module_Fullwidth_Header;

class ET_Builder_Module_Fullwidth_Menu extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Menu', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_menu';
		$this->fb_support = true;
		$this->fullwidth  = true;

		$this->whitelisted_fields = array(
			'menu_id',
			'background_color',
			'background_layout',
			'text_orientation',
			'submenu_direction',
			'admin_label',
			'module_id',
			'module_class',
			'fullwidth_menu',
			'active_link_color',
			'dropdown_menu_bg_color',
			'dropdown_menu_line_color',
			'dropdown_menu_text_color',
			'dropdown_menu_animation',
			'mobile_menu_bg_color',
			'mobile_menu_text_color',
		);

		$this->main_css_element = '%%order_class%%.et_pb_fullwidth_menu';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
					'links'  => esc_html__( 'Links', 'et_builder' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'menu' => array(
					'label'    => esc_html__( 'Menu', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} ul li a",
						'plugin_main' => "{$this->main_css_element} ul li a, {$this->main_css_element} ul li",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
						'range_settings' => array(
							'min'  => '12',
							'max'  => '24',
							'step' => '1',
						),
					),
					'letter_spacing' => array(
						'default' => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '8',
							'step' => '1',
						),
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
			),
		);

		$this->custom_css_options = array(
			'menu_link' => array(
				'label'    => esc_html__( 'Menu Link', 'et_builder' ),
				'selector' => '.fullwidth-menu-nav li a',
			),
			'active_menu_link' => array(
				'label'    => esc_html__( 'Active Menu Link', 'et_builder' ),
				'selector' => '.fullwidth-menu-nav li.current-menu-item a',
			),
			'dropdown_container' => array(
				'label'    => esc_html__( 'Dropdown Menu Container', 'et_builder' ),
				'selector' => '.fullwidth-menu-nav li ul.sub-menu',
			),
			'dropdown_links' => array(
				'label'    => esc_html__( 'Dropdown Menu Links', 'et_builder' ),
				'selector' => '.fullwidth-menu-nav li ul.sub-menu a',
			),
		);

		$this->fields_defaults = array(
			'background_color'        => array( '#ffffff', 'only_default_setting' ),
			'background_layout'       => array( 'light' ),
			'text_orientation'        => array( 'left' ),
			'dropdown_menu_animation' => array( 'fade' ),
		);
	}

	function get_fields() {
		$fields = array(
			'menu_id' => array(
				'label'           => esc_html__( 'Menu', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => et_builder_get_nav_menus_options(),
				'description'     => sprintf(
					'<p class="description">%2$s. <a href="%1$s" target="_blank">%3$s</a>.</p>',
					esc_url( admin_url( 'nav-menus.php' ) ),
					esc_html__( 'Select a menu that should be used in the module', 'et_builder' ),
					esc_html__( 'Click here to create new menu', 'et_builder' )
				),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__menu',
				),
			),
			'background_color' => array(
				'label'       => esc_html__( 'Background Color', 'et_builder' ),
				'type'        => 'color-alpha',
				'toggle_slug' => 'background',
				'description' => esc_html__( 'Use the color picker to choose a background color for this module.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'links',
				'description'     => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_text_orientation_options(),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'links',
				'description'       => esc_html__( 'This controls the how your text is aligned within the module.', 'et_builder' ),
			),
			'submenu_direction' => array(
				'label'           => esc_html__( 'Sub-Menus Open', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'downwards' => esc_html__( 'Downwards', 'et_builder' ),
					'upwards'   => esc_html__( 'Upwards', 'et_builder' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Here you can choose the direction that your sub-menus will open. You can choose to have them open downwards or upwards.', 'et_builder' ),
				'computed_affects' => array(
					'__menu',
				),
			),
			'fullwidth_menu' => array(
				'label'           => esc_html__( 'Make Menu Links Fullwidth', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'active_link_color' => array(
				'label'        => esc_html__( 'Active Link Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'links',
			),
			'dropdown_menu_bg_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'dropdown_menu_line_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Line Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'layout',
			),
			'dropdown_menu_text_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Text Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'links',
			),
			'dropdown_menu_animation' => array(
				'label'             => esc_html__( 'Dropdown Menu Animation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'fade'     => esc_html__( 'Fade', 'et_builder' ),
					'expand'   => esc_html__( 'Expand', 'et_builder' ),
					'slide'	   => esc_html__( 'Slide', 'et_builder' ),
					'flip'	   => esc_html__( 'Flip', 'et_builder' ),
				),
				'tab_slug'     => 'custom_css',
				'toggle_slug'  => 'animation',
			),
			'mobile_menu_bg_color' => array(
				'label'        => esc_html__( 'Mobile Menu Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'mobile_menu_text_color' => array(
				'label'        => esc_html__( 'Mobile Menu Text Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'links',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__menu' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Fullwidth_Menu', 'get_fullwidth_menu' ),
				'computed_depends_on' => array(
					'menu_id',
					'submenu_direction',
				),
			),
		);

		return $fields;
	}

	/**
	 * Add the class with page ID to menu item so it can be easily found by ID in Frontend Builder
	 *
	 * @return menu item object
	 */
	static function modify_fullwidth_menu_item( $menu_item ) {
		if ( esc_url( home_url( '/' ) ) === $menu_item->url ) {
			$fw_menu_custom_class = 'et_pb_menu_page_id-home';
		} else {
			$fw_menu_custom_class = 'et_pb_menu_page_id-' . $menu_item->object_id;
		}

		$menu_item->classes[] = $fw_menu_custom_class;
		return $menu_item;
	}

	/**
	 * Get fullwidth menu markup for fullwidth menu module
	 *
	 * @return string of fullwidth menu markup
	 */
	static function get_fullwidth_menu( $args = array() ) {
		$defaults = array(
			'submenu_direction' => '',
			'menu_id'           => '',
		);

		// modify the menu item to include the required data
		add_filter( 'wp_setup_nav_menu_item', array( 'ET_Builder_Module_Fullwidth_Menu', 'modify_fullwidth_menu_item' ) );

		$args = wp_parse_args( $args, $defaults );

		$menu = '<nav class="fullwidth-menu-nav">';

		$menuClass = 'fullwidth-menu nav';

		if ( ! et_is_builder_plugin_active() && 'on' == et_get_option( 'divi_disable_toptier' ) ) {
			$menuClass .= ' et_disable_top_tier';
		}
		$menuClass .= ( '' !== $args['submenu_direction'] ? sprintf( ' %s', esc_attr( $args['submenu_direction'] ) ) : '' );

		$primaryNav = '';

		$menu_args = array(
			'theme_location' => 'primary-menu',
			'container'      => '',
			'fallback_cb'    => '',
			'menu_class'     => $menuClass,
			'menu_id'        => '',
			'echo'           => false,
		);

		if ( '' !== $args['menu_id'] ) {
			$menu_args['menu'] = (int) $args['menu_id'];
		}

		$primaryNav = wp_nav_menu( apply_filters( 'et_fullwidth_menu_args', $menu_args ) );

		if ( '' == $primaryNav ) {
			$menu .= sprintf(
				'<ul class="%1$s">
					%2$s',
				esc_attr( $menuClass ),
				( ! et_is_builder_plugin_active() && 'on' === et_get_option( 'divi_home_link' )
					? sprintf( '<li%1$s><a href="%2$s">%3$s</a></li>',
						( is_home() ? ' class="current_page_item"' : '' ),
						esc_url( home_url( '/' ) ),
						esc_html__( 'Home', 'et_builder' )
					)
					: ''
				)
			);

			ob_start();

			// @todo: check if Fullwidth Menu module works fine with no menu selected in settings
			if ( et_is_builder_plugin_active() ) {
				wp_page_menu();
			} else {
				show_page_menu( $menuClass, false, false );
				show_categories_menu( $menuClass, false );
			}

			$menu .= ob_get_contents();

			$menu .= '</ul>';

			ob_end_clean();
		} else {
			$menu .= $primaryNav;
		}

		$menu .= '</nav>';

		remove_filter( 'wp_setup_nav_menu_item', array( 'ET_Builder_Module_Fullwidth_Menu', 'modify_fullwidth_menu_item' ) );

		return $menu;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id         = $this->shortcode_atts['module_id'];
		$module_class      = $this->shortcode_atts['module_class'];
		$background_color  = $this->shortcode_atts['background_color'];
		$background_layout = $this->shortcode_atts['background_layout'];
		$text_orientation  = $this->shortcode_atts['text_orientation'];
		$menu_id           = $this->shortcode_atts['menu_id'];
		$submenu_direction = $this->shortcode_atts['submenu_direction'];
		$fullwidth_menu           = $this->shortcode_atts['fullwidth_menu'] === 'on' ? ' et_pb_fullwidth_menu_fullwidth' : '';
		$active_link_color        = $this->shortcode_atts['active_link_color'];
		$dropdown_menu_bg_color   = $this->shortcode_atts['dropdown_menu_bg_color'];
		$dropdown_menu_line_color = $this->shortcode_atts['dropdown_menu_line_color'];
		$dropdown_menu_text_color = $this->shortcode_atts['dropdown_menu_text_color'];
		$dropdown_menu_animation  = $this->shortcode_atts['dropdown_menu_animation'];
		$mobile_menu_bg_color     = $this->shortcode_atts['mobile_menu_bg_color'];
		$mobile_menu_text_color   = $this->shortcode_atts['mobile_menu_text_color'];

		if ( is_rtl() && 'left' === $text_orientation ) {
			$text_orientation = 'right';
		}

		$style = '';

		if ( '' !== $background_color ) {
			$style .= sprintf( ' style="background-color: %s;"',
				esc_attr( $background_color )
			);
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation} et_dropdown_animation_{$dropdown_menu_animation}{$fullwidth_menu}";

		$menu = self::get_fullwidth_menu( array(
			'menu_id'           => $menu_id,
			'submenu_direction' => $submenu_direction,
		) );

		if ( '' !== $active_link_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu ul li.current-menu-item a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $active_link_color )
				),
			) );
		}

		if ( '' !== $background_color || '' !== $dropdown_menu_bg_color ) {
			$et_menu_bg_color = '' !== $dropdown_menu_bg_color ? $dropdown_menu_bg_color : $background_color;

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .nav li ul',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $et_menu_bg_color )
				),
			) );
		}

		if ( '' !== $dropdown_menu_line_color ) {

			$dropdown_menu_line_color_selector = 'upwards' === $submenu_direction ? '%%order_class%%.et_pb_fullwidth_menu .fullwidth-menu-nav > ul.upwards li ul' : '%%order_class%%.et_pb_fullwidth_menu .nav li ul';

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => $dropdown_menu_line_color_selector,
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $dropdown_menu_line_color )
				),
			) );

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .et_mobile_menu',
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $dropdown_menu_line_color )
				),
			) );
		}

		if ( '' !== $dropdown_menu_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .nav li ul a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $dropdown_menu_text_color )
				),
			) );
		}

		if ( '' !== $mobile_menu_bg_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .et_mobile_menu, %%order_class%%.et_pb_fullwidth_menu .et_mobile_menu ul',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $mobile_menu_bg_color )
				),
			) );
		}

		if ( '' !== $mobile_menu_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .et_mobile_menu a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $mobile_menu_text_color )
				),
			) );
		}

		$output = sprintf(
			'<div%4$s class="et_pb_fullwidth_menu%3$s%5$s%6$s%8$s"%2$s>
				%9$s
				%7$s
				<div class="et_pb_row clearfix">
					%1$s
					<div class="et_mobile_nav_menu">
						<a href="#" class="mobile_nav closed">
							<span class="mobile_menu_bar"></span>
						</a>
					</div>
				</div>
			</div>',
			$menu,
			$style,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Fullwidth_Menu;

class ET_Builder_Module_Fullwidth_Slider extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Fullwidth Slider', 'et_builder' );
		$this->slug            = 'et_pb_fullwidth_slider';
		$this->fb_support      = true;
		$this->fullwidth       = true;
		$this->child_slug      = 'et_pb_slide';
		$this->child_item_text = esc_html__( 'Slide', 'et_builder' );

		$this->whitelisted_fields = array(
			'show_arrows',
			'show_pagination',
			'auto',
			'auto_speed',
			'auto_ignore_hover',
			'parallax',
			'parallax_method',
			'remove_inner_shadow',
			'background_position',
			'background_size',
			'admin_label',
			'module_id',
			'module_class',
			'top_padding',
			'bottom_padding',
			'hide_content_on_mobile',
			'hide_cta_on_mobile',
			'show_image_video_mobile',
			'bottom_padding_tablet',
			'bottom_padding_phone',
			'bottom_padding_last_edited',
			'top_padding_tablet',
			'top_padding_phone',
			'top_padding_last_edited',

		);

		$this->fields_defaults = array(
			'show_arrows'             => array( 'on' ),
			'show_pagination'         => array( 'on' ),
			'auto'                    => array( 'off' ),
			'auto_speed'              => array( '7000' ),
			'auto_ignore_hover'       => array( 'off' ),
			'parallax'                => array( 'off' ),
			'parallax_method'         => array( 'off' ),
			'remove_inner_shadow'     => array( 'off' ),
			'background_position'     => array( 'center' ),
			'background_size'         => array( 'cover' ),
			'hide_content_on_mobile'  => array( 'off' ),
			'hide_cta_on_mobile'      => array( 'off' ),
			'show_image_video_mobile' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_slider';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'   => esc_html__( 'Elements', 'et_builder' ),
					'background' => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'    => esc_html__( 'Layout', 'et_builder' ),
					'padding'  => array(
						'title'    => esc_html__( 'Spacing', 'et_builder' ),
						'priority' => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title",
						'plugin_main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title, {$this->main_css_element} .et_pb_slide_description .et_pb_slide_title a",
						'important' => array(
							'color',
							'size',
							'font-size',
							'plugin_all',
						),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element}.et_pb_module .et_pb_slides .et_pb_slide_content",
						'line_height' => "{$this->main_css_element} p",
						'important' => array( 'size', 'font-size' ),
					),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
					),
				),
			),
		);
		$this->custom_css_options = array(
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description .et_pb_slide_title',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector' => '.et_pb_slider .et_pb_slide .et_pb_slide_description a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_controllers' => array(
				'label'    => esc_html__( 'Slide Controllers', 'et_builder' ),
				'selector' => '.et-pb-controllers',
			),
			'slide_active_controller' => array(
				'label'    => esc_html__( 'Slide Active Controller', 'et_builder' ),
				'selector' => '.et-pb-controllers .et-pb-active-control',
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
			'slide_arrows' => array(
				'label'    => esc_html__( 'Slide Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_arrows' => array(
				'label'           => esc_html__( 'Arrows', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Show Arrows', 'et_builder' ),
					'off' => esc_html__( 'Hide Arrows', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'This setting allows you to turn the navigation arrows on or off.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'           => esc_html__( 'Controls', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Show Slider Controls', 'et_builder' ),
					'off' => esc_html__( 'Hide Slider Controls', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Disabling this option will remove the circle button at the bottom of the slider.', 'et_builder' ),
			),
			'auto' => array(
				'label'             => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off'  => esc_html__( 'Off', 'et_builder' ),
					'on' => esc_html__( 'On', 'et_builder' ),
				),
				'affects'           => array(
					'auto_speed',
					'auto_ignore_hover',
				),
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
			),
			'auto_speed' => array(
				'label'             => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_default'   => true,
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
			),
			'auto_ignore_hover' => array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options' => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'et_builder' ),
			),
			'parallax' => array(
				'label'           => esc_html__( 'Use Parallax effect', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off'  => esc_html__( 'No', 'et_builder' ),
					'on' => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'         => array(
					'parallax_method',
					'background_position',
					'background_size',
				),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'background',
				'description'     => esc_html__( 'If enabled, your background images will have a fixed position as your scroll, creating a fun parallax-like effect.', 'et_builder' ),
			),
			'parallax_method' => array(
				'label'           => esc_html__( 'Parallax method', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'CSS', 'et_builder' ),
					'on'  => esc_html__( 'True Parallax', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'general',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Define the method, used for the parallax effect.', 'et_builder' ),
			),
			'remove_inner_shadow' => array(
				'label'           => esc_html__( 'Remove Inner Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'background_position' => array(
				'label'           => esc_html__( 'Background Image Position', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'top_left'      => esc_html__( 'Top Left', 'et_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'et_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'et_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'et_builder' ),
					'center'        => esc_html__( 'Center', 'et_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'et_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'et_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'et_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'et_builder' ),
				),
				'default'           => 'center',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			),
			'background_size' => array(
				'label'           => esc_html__( 'Background Image Size', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'cover'   => esc_html__( 'Cover', 'et_builder' ),
					'contain' => esc_html__( 'Fit', 'et_builder' ),
					'initial' => esc_html__( 'Actual Size', 'et_builder' ),
				),
				'default'         => 'cover',
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			),
			'top_padding' => array(
				'label'           => esc_html__( 'Top Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'bottom_padding' => array(
				'label'           => esc_html__( 'Bottom Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'top_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'top_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'top_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bottom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bottom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bottom_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'hide_content_on_mobile' => array(
				'label'           => esc_html__( 'Hide Content On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'hide_cta_on_mobile' => array(
				'label'           => esc_html__( 'Hide CTA On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'show_image_video_mobile' => array(
				'label'            => esc_html__( 'Show Image / Video On Mobile', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_hide_mobile, $et_pb_slider_custom_icon, $et_pb_slider_item_num;

		$et_pb_slider_item_num = 0;

		$parallax        = $this->shortcode_atts['parallax'];
		$parallax_method = $this->shortcode_atts['parallax_method'];
		$hide_content_on_mobile  = $this->shortcode_atts['hide_content_on_mobile'];
		$hide_cta_on_mobile      = $this->shortcode_atts['hide_cta_on_mobile'];
		$button_custom           = $this->shortcode_atts['custom_button'];
		$custom_icon             = $this->shortcode_atts['button_icon'];

		$et_pb_slider_has_video = false;

		$et_pb_slider_parallax = $parallax;

		$et_pb_slider_parallax_method = $parallax_method;

		$et_pb_slider_hide_mobile = array(
			'hide_content_on_mobile'  => $hide_content_on_mobile,
			'hide_cta_on_mobile'      => $hide_cta_on_mobile,
		);

		$et_pb_slider_custom_icon = 'on' === $button_custom ? $custom_icon : '';

	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$show_arrows             = $this->shortcode_atts['show_arrows'];
		$show_pagination         = $this->shortcode_atts['show_pagination'];
		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$auto                    = $this->shortcode_atts['auto'];
		$auto_speed              = $this->shortcode_atts['auto_speed'];
		$auto_ignore_hover       = $this->shortcode_atts['auto_ignore_hover'];
		$top_padding             = $this->shortcode_atts['top_padding'];
		$bottom_padding          = $this->shortcode_atts['bottom_padding'];
		$top_padding_tablet      = $this->shortcode_atts['top_padding_tablet'];
		$bottom_padding_tablet   = $this->shortcode_atts['bottom_padding_tablet'];
		$top_padding_phone       = $this->shortcode_atts['top_padding_phone'];
		$bottom_padding_phone    = $this->shortcode_atts['bottom_padding_phone'];
		$top_padding_last_edited = $this->shortcode_atts['top_padding_last_edited'];
		$bottom_padding_last_edited = $this->shortcode_atts['bottom_padding_last_edited'];
		$remove_inner_shadow     = $this->shortcode_atts['remove_inner_shadow'];
		$show_image_video_mobile = $this->shortcode_atts['show_image_video_mobile'];
		$background_position     = $this->shortcode_atts['background_position'];
		$background_size         = $this->shortcode_atts['background_size'];

		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_hide_mobile, $et_pb_slider_custom_icon;

		$content = $this->shortcode_content;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $top_padding || '' !== $top_padding_tablet || '' !== $top_padding_phone ) {
			$top_padding_responsive_active = et_pb_get_responsive_status( $top_padding_last_edited );

			$padding_values = array(
				'desktop' => $top_padding,
				'tablet'  => $top_padding_responsive_active ? $top_padding_tablet : '',
				'phone'   => $top_padding_responsive_active ? $top_padding_phone : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_slide_description', 'padding-top', $function_name );
		}

		if ( '' !== $bottom_padding || '' !== $bottom_padding_tablet || '' !== $bottom_padding_phone ) {
			$bottom_padding_responsive_active = et_pb_get_responsive_status( $bottom_padding_last_edited );

			$padding_values = array(
				'desktop' => $bottom_padding,
				'tablet'  => $bottom_padding_responsive_active ? $bottom_padding_tablet : '',
				'phone'   => $bottom_padding_responsive_active ? $bottom_padding_phone : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_slide_description', 'padding-bottom', $function_name );
		}

		if ( '' !== $background_position && 'default' !== $background_position && 'off' === $parallax ) {
			$processed_position = str_replace( '_', ' ', $background_position );

			ET_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide',
				'declaration' => sprintf(
					'background-position: %1$s;',
					esc_html( $processed_position )
				),
			) );
		}

		if ( '' !== $background_size && 'default' !== $background_size && 'off' === $parallax ) {
			ET_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide',
				'declaration' => sprintf(
					'-moz-background-size: %1$s;
					-webkit-background-size: %1$s;
					background-size: %1$s;',
					esc_html( $background_size )
				),
			) );
		}

		$fullwidth = 'et_pb_fullwidth_slider' === $function_name ? 'on' : 'off';

		$class  = '';
		$class .= 'off' === $fullwidth ? ' et_pb_slider_fullwidth_off' : '';
		$class .= 'off' === $show_arrows ? ' et_pb_slider_no_arrows' : '';
		$class .= 'off' === $show_pagination ? ' et_pb_slider_no_pagination' : '';
		$class .= 'on' === $parallax ? ' et_pb_slider_parallax' : '';
		$class .= 'on' === $auto ? ' et_slider_auto et_slider_speed_' . esc_attr( $auto_speed ) : '';
		$class .= 'on' === $auto_ignore_hover ? ' et_slider_auto_ignore_hover' : '';
		$class .= 'on' === $remove_inner_shadow ? ' et_pb_slider_no_shadow' : '';
		$class .= 'on' === $show_image_video_mobile ? ' et_pb_slider_show_image' : '';

		$output = sprintf(
			'<div%4$s class="et_pb_module et_pb_slider%1$s%3$s%5$s">
				<div class="et_pb_slides">
					%2$s
				</div> <!-- .et_pb_slides -->
			</div> <!-- .et_pb_slider -->
			',
			$class,
			$content,
			( $et_pb_slider_has_video ? ' et_pb_preload' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Fullwidth_Slider;

class ET_Builder_Module_Fullwidth_Portfolio extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Portfolio', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_portfolio';
		$this->fb_support = true;
		$this->fullwidth  = true;

		// need to use global settings from the slider module
		$this->global_settings_slug = 'et_pb_portfolio';

		$this->whitelisted_fields = array(
			'title',
			'fullwidth',
			'include_categories',
			'posts_number',
			'show_title',
			'show_date',
			'background_layout',
			'auto',
			'auto_speed',
			'hover_icon',
			'hover_overlay_color',
			'zoom_icon_color',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'   => esc_html__( 'Layout', 'et_builder' ),
					'overlay'  => esc_html__( 'Overlay', 'et_builder' ),
					'rotation' => esc_html__( 'Rotation', 'et_builder' ),
					'text'     => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title'   => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h3",
						'important' => 'all',
					),
				),
				'caption' => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'border' => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_portfolio_item",
				),
			),
		);

		$this->custom_css_options = array(
			'portfolio_title' => array(
				'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
				'selector' => '> h2',
			),
			'portfolio_item' => array(
				'label'    => esc_html__( 'Portfolio Item', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item',
			),
			'portfolio_overlay' => array(
				'label'    => esc_html__( 'Item Overlay', 'et_builder' ),
				'selector' => 'span.et_overlay',
			),
			'portfolio_item_title' => array(
				'label'    => esc_html__( 'Item Title', 'et_builder' ),
				'selector' => '.meta h3',
			),
			'portfolio_meta' => array(
				'label'    => esc_html__( 'Meta', 'et_builder' ),
				'selector' => '.meta p',
			),
			'portfolio_arrows' => array(
				'label'    => esc_html__( 'Navigation Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);

		$this->fields_defaults = array(
			'fullwidth'         => array( 'on' ),
			'show_title'        => array( 'on' ),
			'show_date'         => array( 'on' ),
			'background_layout' => array( 'light' ),
			'auto'              => array( 'off' ),
			'auto_speed'        => array( '7000' ),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Portfolio Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Title displayed above the portfolio.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'fullwidth' => array(
				'label'             => esc_html__( 'Layout', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Carousel', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'affects'           => array(
					'auto',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'layout',
				'description'       => esc_html__( 'Choose your desired portfolio layout style.', 'et_builder' ),
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'renderer'         => 'et_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select the categories that you would like to include in the feed.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'taxonomy_name'    => 'project_category',
				'toggle_slug'      => 'main_content',
			),
			'posts_number' => array(
				'label'            => esc_html__( 'Posts Number', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Control how many projects are displayed. Leave blank or use 0 to not limit the amount.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'toggle_slug'      => 'main_content',
			),
			'show_title' => array(
				'label'             => esc_html__( 'Show Title', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Turn project titles on or off.', 'et_builder' ),
			),
			'show_date' => array(
				'label'             => esc_html__( 'Show Date', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Turn the date display on or off.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'color_option',
				'options'           => array(
					'light'  => esc_html__( 'Dark', 'et_builder' ),
					'dark' => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'auto' => array(
				'label'             => esc_html__( 'Automatic Carousel Rotation', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off'  => esc_html__( 'Off', 'et_builder' ),
					'on' => esc_html__( 'On', 'et_builder' ),
				),
				'affects'           => array(
					'auto_speed',
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( 'If you the carousel layout option is chosen and you would like the carousel to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
			),
			'auto_speed' => array(
				'label'             => esc_html__( 'Automatic Carousel Rotation Speed (in ms)', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_default'   => true,
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( "Here you can designate how fast the carousel rotates, if 'Automatic Carousel Rotation' option is enabled above. The higher the number the longer the pause between each rotation. (Ex. 1000 = 1 sec)", 'et_builder' ),
			),
			'zoom_icon_color' => array(
				'label'             => esc_html__( 'Zoom Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__projects'          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Fullwidth_Portfolio', 'get_portfolio_item' ),
				'computed_depends_on' => array(
					'posts_number',
					'include_categories',
				),
			),
		);
		return $fields;
	}

	/**
	 * Get portfolio objects for portfolio module
	 *
	 * @param array  arguments that affect et_pb_portfolio query
	 * @param array  passed conditional tag for update process
	 * @param array  passed current page params
	 * @return array portfolio item data
	 */
	static function get_portfolio_item( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'posts_number'       => '',
			'include_categories' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array(
			'post_type'   => 'project',
			'post_status' => 'publish',
		);

		if ( is_numeric( $args['posts_number'] ) && $args['posts_number'] > 0 ) {
			$query_args['posts_per_page'] = $args['posts_number'];
		} else {
			$query_args['nopaging'] = true;
		}

		if ( '' !== $args['include_categories'] ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'project_category',
					'field' => 'id',
					'terms' => explode( ',', $args['include_categories'] ),
					'operator' => 'IN'
				)
			);
		}

		// Get portfolio query
		$query = new WP_Query( $query_args );

		// Format portfolio output, add supplementary data
		$width  = (int) apply_filters( 'et_pb_portfolio_image_width', 510 );
		$height = (int) apply_filters( 'et_pb_portfolio_image_height', 382 );

		if( $query->post_count > 0 ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();

				// Get thumbnail
				$thumbnail   = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array( $width, $height ) );

				if ( isset( $thumbnail[2] ) && isset( $thumbnail[1] ) ) {
					$orientation = ( $thumbnail[2] > $thumbnail[1] ) ? 'portrait' : 'landscape';
				} else {
					$orientation = false;
				}

				// Append value to query post
				$query->posts[ $post_index ]->post_permalink             = get_permalink();
				$query->posts[ $post_index ]->post_thumbnail             = isset( $thumbnail[0] ) ? $thumbnail[0] : false;
				$query->posts[ $post_index ]->post_thumbnail_orientation = $orientation;
				$query->posts[ $post_index ]->post_date_readable         = get_the_date();
				$query->posts[ $post_index ]->post_class_name            = get_post_class( 'et_pb_portfolio_item et_pb_grid_item ' );

				$post_index++;
			}
		}

		wp_reset_postdata();

		return $query;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$title               = $this->shortcode_atts['title'];
		$module_id           = $this->shortcode_atts['module_id'];
		$module_class        = $this->shortcode_atts['module_class'];
		$fullwidth           = $this->shortcode_atts['fullwidth'];
		$include_categories  = $this->shortcode_atts['include_categories'];
		$posts_number        = $this->shortcode_atts['posts_number'];
		$show_title          = $this->shortcode_atts['show_title'];
		$show_date           = $this->shortcode_atts['show_date'];
		$background_layout   = $this->shortcode_atts['background_layout'];
		$auto                = $this->shortcode_atts['auto'];
		$auto_speed          = $this->shortcode_atts['auto_speed'];
		$zoom_icon_color     = $this->shortcode_atts['zoom_icon_color'];
		$hover_overlay_color = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon          = $this->shortcode_atts['hover_icon'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$zoom_and_hover_selector = '.et_pb_fullwidth_portfolio%%order_class%% .et_pb_portfolio_image';

		if ( '' !== $zoom_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => "{$zoom_and_hover_selector} .et_overlay:before",
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $zoom_icon_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => "{$zoom_and_hover_selector} .et_overlay",
				'declaration' => sprintf(
					'background-color: %1$s;
					border-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		$args = array();
		if ( is_numeric( $posts_number ) && $posts_number > 0 ) {
			$args['posts_per_page'] = $posts_number;
		} else {
			$args['nopaging'] = true;
		}

		if ( '' !== $include_categories ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'project_category',
					'field' => 'id',
					'terms' => explode( ',', $include_categories ),
					'operator' => 'IN'
				)
			);
		}

		$projects = self::get_portfolio_item( array(
			'posts_number'       => $posts_number,
			'include_categories' => $include_categories,
		) );

		ob_start();
		if( $projects->post_count > 0 ) {
			while ( $projects->have_posts() ) {
				$projects->the_post();
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_portfolio_item et_pb_grid_item ' ); ?>>
				<?php
					$thumb = '';

					$width = 510;
					$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

					$height = 382;
					$height = (int) apply_filters( 'et_pb_portfolio_image_height', $height );

					list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array( $width, $height ) );

					$orientation = ( $thumb_height > $thumb_width ) ? 'portrait' : 'landscape';

					if ( '' !== $thumb_src ) : ?>
						<div class="et_pb_portfolio_image <?php echo esc_attr( $orientation ); ?>">
							<img src="<?php echo esc_url( $thumb_src ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"/>
							<div class="meta">
								<a href="<?php esc_url( the_permalink() ); ?>">
								<?php
									$data_icon = '' !== $hover_icon
										? sprintf(
											' data-icon="%1$s"',
											esc_attr( et_pb_process_font_icon( $hover_icon ) )
										)
										: '';

									printf( '<span class="et_overlay%1$s"%2$s></span>',
										( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
										$data_icon
									);
								?>
									<?php if ( 'on' === $show_title ) : ?>
										<h3><?php the_title(); ?></h3>
									<?php endif; ?>

									<?php if ( 'on' === $show_date ) : ?>
										<p class="post-meta"><?php echo get_the_date(); ?></p>
									<?php endif; ?>
								</a>
							</div>
						</div>
				<?php endif; ?>
				</div>
				<?php
			}
		}

		wp_reset_postdata();

		$posts = ob_get_clean();

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$output = sprintf(
			'<div%4$s class="et_pb_fullwidth_portfolio %1$s%3$s%5$s%9$s%11$s" data-auto-rotate="%6$s" data-auto-rotate-speed="%7$s">
				%12$s
				%10$s
				%8$s
				<div class="et_pb_portfolio_items clearfix" data-portfolio-columns="">
					%2$s
				</div><!-- .et_pb_portfolio_items -->
			</div> <!-- .et_pb_fullwidth_portfolio -->',
			( 'on' === $fullwidth ? 'et_pb_fullwidth_portfolio_carousel' : 'et_pb_fullwidth_portfolio_grid clearfix' ),
			$posts,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $auto && in_array( $auto, array('on', 'off') ) ? esc_attr( $auto ) : 'off' ),
			( '' !== $auto_speed && is_numeric( $auto_speed ) ? esc_attr( $auto_speed ) : '7000' ),
			( '' !== $title ? sprintf( '<h2>%s</h2>', esc_html( $title ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}
new ET_Builder_Module_Fullwidth_Portfolio;

class ET_Builder_Module_Fullwidth_Map extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Fullwidth Map', 'et_builder' );
		$this->slug            = 'et_pb_fullwidth_map';
		$this->fb_support      = true;
		$this->fullwidth       = true;
		$this->child_slug      = 'et_pb_map_pin';
		$this->child_item_text = esc_html__( 'Pin', 'et_builder' );

		$this->whitelisted_fields = array(
			'address',
			'zoom_level',
			'address_lat',
			'address_lng',
			'map_center_map',
			'mouse_wheel',
			'mobile_dragging',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'zoom_level'  => array( '18', 'only_default_setting' ),
			'mouse_wheel' => array( 'on' ),
			'mobile_dragging' => array( 'on' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'map' => esc_html__( 'Map', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'controls' => esc_html__( 'Controls', 'et_builder' ),
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'google_maps_script_notice' => array(
				'type'              => 'warning',
				'value'             => et_pb_enqueue_google_maps_script(),
				'display_if'        => false,
				'message'          => esc_html__(
					sprintf(
						'The Google Maps API Script is currently disabled in the <a href="%s" target="_blank">Theme Options</a>. This module will not function properly without the Google Maps API.',
						admin_url( 'admin.php?page=et_divi_options' )
					),
					'et_builder'
				),
				'toggle_slug'     => 'map',
			),
			'google_api_key' => array(
				'label'             => esc_html__( 'Google API Key', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'attributes'        => 'readonly',
				'additional_button' => sprintf(
					' <a href="%2$s" target="_blank" class="et_pb_update_google_key button" data-empty_text="%3$s">%1$s</a>',
					esc_html__( 'Change API Key', 'et_builder' ),
					esc_url( et_pb_get_options_page_link() ),
					esc_attr__( 'Add Your API Key', 'et_builder' )
				),
				'additional_button_type' => 'change_google_api_key',
				'class' => array( 'et_pb_google_api_key', 'et-pb-helper-field' ),
				'description'       => et_get_safe_localization( sprintf( __( 'The Maps module uses the Google Maps API and requires a valid Google API Key to function. Before using the map module, please make sure you have added your API key inside the Divi Theme Options panel. Learn more about how to create your Google API Key <a href="%1$s" target="_blank">here</a>.', 'et_builder' ), esc_url( 'http://www.elegantthemes.com/gallery/divi/documentation/map/#gmaps-api-key' ) ) ),
				'toggle_slug'       => 'map',
			),
			'address' => array(
				'label'             => esc_html__( 'Map Center Address', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'additional_button' => sprintf(
					' <a href="#" class="et_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'et_builder' )
				),
				'class'             => array( 'et_pb_address' ),
				'description'       => esc_html__( 'Enter an address for the map center point, and the address will be geocoded and displayed on the map below.', 'et_builder' ),
				'toggle_slug'       => 'map',
			),
			'zoom_level' => array(
				'type'    => 'hidden',
				'class'   => array( 'et_pb_zoom_level' ),
			),
			'address_lat' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_address_lat' ),
			),
			'address_lng' => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_address_lng' ),
			),
			'map_center_map' => array(
				'renderer'              => 'et_builder_generate_center_map_setting',
				'use_container_wrapper' => false,
				'option_category'       => 'basic_option',
				'toggle_slug'           => 'map',
			),
			'mouse_wheel' => array(
				'label'           => esc_html__( 'Mouse Wheel Zoom', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'controls',
				'description'     => esc_html__( 'Here you can choose whether the zoom level will be controlled by mouse wheel or not.', 'et_builder' ),
			),
			'mobile_dragging' => array(
				'label'           => esc_html__( 'Draggable on Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'controls',
				'description'     => esc_html__( 'Here you can choose whether or not the map will be draggable on mobile devices.', 'et_builder' ),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id    = $this->shortcode_atts['module_id'];
		$module_class = $this->shortcode_atts['module_class'];
		$address_lat  = $this->shortcode_atts['address_lat'];
		$address_lng  = $this->shortcode_atts['address_lng'];
		$zoom_level   = $this->shortcode_atts['zoom_level'];
		$mouse_wheel  = $this->shortcode_atts['mouse_wheel'];
		$mobile_dragging = $this->shortcode_atts['mobile_dragging'];

		if ( et_pb_enqueue_google_maps_script() ) {
			wp_enqueue_script( 'google-maps-api' );
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$all_pins_content = $this->shortcode_content;

		$output = sprintf(
			'<div%5$s class="et_pb_module et_pb_map_container%6$s">
				<div class="et_pb_map" data-center-lat="%1$s" data-center-lng="%2$s" data-zoom="%3$d" data-mouse-wheel="%7$s" data-mobile-dragging="%8$s"></div>
				%4$s
			</div>',
			esc_attr( $address_lat ),
			esc_attr( $address_lng ),
			esc_attr( $zoom_level ),
			$all_pins_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			esc_attr( $mouse_wheel ),
			esc_attr( $mobile_dragging )
		);

		return $output;
	}
}
new ET_Builder_Module_Fullwidth_Map;

class ET_Builder_Module_Code extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Code', 'et_builder' );
		$this->slug            = 'et_pb_code';
		$this->fb_support      = true;
		$this->use_row_content = true;
		$this->decode_entities = true;

		$this->whitelisted_fields = array(
			'raw_content',
			'admin_label',
			'module_id',
			'module_class',
			'max_width',
			'max_width_tablet',
			'max_width_phone',
			'max_width_last_edited',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'width' => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
		);

		// wptexturize is often incorrectly parsed single and double quotes
		// This disables wptexturize on this module
		add_filter( 'no_texturize_shortcodes', array( $this, 'disable_wptexturize' ) );
	}

	function get_fields() {
		$fields = array(
			'raw_content' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'textarea',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the content that will be used within the module.', 'et_builder' ),
				'is_fb_content'   => true,
				'toggle_slug'     => 'main_content',
			),
			'max_width' => array(
				'label'           => esc_html__( 'Max Width', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'max_width_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'max_width_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id        = $this->shortcode_atts['module_id'];
		$module_class     = $this->shortcode_atts['module_class'];
		$max_width        = $this->shortcode_atts['max_width'];
		$max_width_tablet = $this->shortcode_atts['max_width_tablet'];
		$max_width_phone  = $this->shortcode_atts['max_width_phone'];
		$max_width_last_edited = $this->shortcode_atts['max_width_last_edited'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$this->shortcode_content = et_builder_convert_line_breaks( et_builder_replace_code_content_entities( $this->shortcode_content ) );

		if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
			$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );

			$max_width_values = array(
				'desktop' => $max_width,
				'tablet'  => $max_width_responsive_active ? $max_width_tablet : '',
				'phone'   => $max_width_responsive_active ? $max_width_phone : '',
			);

			et_pb_generate_responsive_css( $max_width_values, '%%order_class%%', 'max-width', $function_name );
		}

		$output = sprintf(
			'<div%2$s class="et_pb_code et_pb_module%3$s">
				%1$s
			</div> <!-- .et_pb_code -->',
			$this->shortcode_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Code;

class ET_Builder_Module_Fullwidth_Code extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Fullwidth Code', 'et_builder' );
		$this->slug            = 'et_pb_fullwidth_code';
		$this->fb_support      = true;
		$this->fullwidth       = true;
		$this->use_row_content = true;
		$this->decode_entities = true;

		$this->whitelisted_fields = array(
			'raw_content',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
		);

		// wptexturize is often incorrectly parsed single and double quotes
		// This disables wptexturize on this module
		add_filter( 'no_texturize_shortcodes', array( $this, 'disable_wptexturize' ) );
	}

	function get_fields() {
		$fields = array(
			'raw_content' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'textarea',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the content that will be used within the module.', 'et_builder' ),
				'is_fb_content'   => true,
				'toggle_slug'     => 'main_content',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id    = $this->shortcode_atts['module_id'];
		$module_class = $this->shortcode_atts['module_class'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$this->shortcode_content = et_builder_convert_line_breaks( et_builder_replace_code_content_entities( $this->shortcode_content ) );

		$output = sprintf(
			'<div%2$s class="et_pb_fullwidth_code et_pb_module%3$s">
				%1$s
			</div> <!-- .et_pb_fullwidth_code -->',
			$this->shortcode_content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Module_Fullwidth_Code;

class ET_Builder_Module_Fullwidth_Image extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Image', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_image';
		$this->fb_support = true;
		$this->fullwidth  = true;
		$this->defaults   = array(
			'align' => 'left',
		);

		$this->whitelisted_fields = array(
			'src',
			'alt',
			'title_text',
			'show_in_lightbox',
			'url',
			'url_new_window',
			'animation',
			'admin_label',
			'module_id',
			'module_class',
			'use_overlay',
			'overlay_icon_color',
			'hover_overlay_color',
			'hover_icon',
		);

		$this->fields_defaults = array(
			'show_in_lightbox' => array( 'off' ),
			'url_new_window'   => array( 'off' ),
			'animation'        => array( 'left' ),
			'use_overlay'      => array( 'off' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Image', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_options = array(
			'border'                => array(),
			'custom_margin_padding' => array(
				'use_padding' => false,
				'css' => array(
					'important' => 'all',
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Image URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'affects'            => array(
					'alt',
					'title_text',
				),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
			),
			'alt' => array(
				'label'           => esc_html__( 'Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_default' => true,
				'depends_to'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'title_text' => array(
				'label'           => esc_html__( 'Image Title Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_default' => true,
				'depends_to'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML Title text.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
			),
			'show_in_lightbox' => array(
				'label'             => esc_html__( 'Open In Lightbox', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'url',
					'url_new_window',
					'use_overlay',
				),
				'toggle_slug'       => 'link',
				'description'       => esc_html__( 'Here you can choose whether or not the image should open in Lightbox. Note: if you select to open the image in Lightbox, url options below will be ignored.', 'et_builder' ),
			),
			'url' => array(
				'label'           => esc_html__( 'Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'affects'         => array(
					'use_overlay',
				),
				'description'     => esc_html__( 'If you would like your image to be a link, input your destination URL here. No link will be created if this field is left blank.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'url_new_window' => array(
				'label'             => esc_html__( 'Url Opens', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'link',
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'use_overlay' => array(
				'label'             => esc_html__( 'Image Overlay', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects'           => array(
					'overlay_icon_color',
					'hover_overlay_color',
					'hover_icon',
				),
				'depends_default'   => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the image', 'et_builder' ),
			),
			'overlay_icon_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay icon', 'et_builder' ),
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'depends_show_if'     => 'on',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
				'description'         => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
			),
			'animation' => array(
				'label'             => esc_html__( 'Animation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'left'    => esc_html__( 'Left To Right', 'et_builder' ),
					'right'   => esc_html__( 'Right To Left', 'et_builder' ),
					'top'     => esc_html__( 'Top To Bottom', 'et_builder' ),
					'bottom'  => esc_html__( 'Bottom To Top', 'et_builder' ),
					'fade_in' => esc_html__( 'Fade In', 'et_builder' ),
					'off'     => esc_html__( 'No Animation', 'et_builder' ),
				),
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id           = $this->shortcode_atts['module_id'];
		$module_class        = $this->shortcode_atts['module_class'];
		$src                 = $this->shortcode_atts['src'];
		$alt                 = $this->shortcode_atts['alt'];
		$title_text          = $this->shortcode_atts['title_text'];
		$animation           = $this->shortcode_atts['animation'];
		$url                 = $this->shortcode_atts['url'];
		$url_new_window      = $this->shortcode_atts['url_new_window'];
		$show_in_lightbox    = $this->shortcode_atts['show_in_lightbox'];
		$overlay_icon_color  = $this->shortcode_atts['overlay_icon_color'];
		$hover_overlay_color = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon          = $this->shortcode_atts['hover_icon'];
		$use_overlay         = $this->shortcode_atts['use_overlay'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		// overlay can be applied only if image has link or if lightbox enabled
		$is_overlay_applied = 'on' === $use_overlay && ( 'on' === $show_in_lightbox || ( 'off' === $show_in_lightbox && '' !== $url ) ) ? 'on' : 'off';

		if ( 'on' === $is_overlay_applied ) {
			if ( '' !== $overlay_icon_color ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_overlay:before',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $overlay_icon_color )
					),
				) );
			}

			if ( '' !== $hover_overlay_color ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_overlay',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $hover_overlay_color )
					),
				) );
			}

			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$overlay_output = sprintf(
				'<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		$output = sprintf(
			'<img src="%1$s" alt="%2$s"%3$s />
			%4$s',
			esc_url( $src ),
			esc_attr( $alt ),
			( '' !== $title_text ? sprintf( ' title="%1$s"', esc_attr( $title_text ) ) : '' ),
			'on' === $is_overlay_applied ? $overlay_output : ''
		);

		if ( 'on' === $show_in_lightbox ) {
			$output = sprintf( '<a href="%1$s" class="et_pb_lightbox_image" title="%3$s">%2$s</a>',
				esc_url( $src ),
				$output,
				esc_attr( $alt )
			);
		} else if ( '' !== $url ) {
			$output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				$output,
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		}

		$output = sprintf(
			'<div%4$s class="et_pb_module et-waypoint et_pb_fullwidth_image%2$s%3$s%5$s">
				%1$s
			</div>',
			$output,
			esc_attr( " et_pb_animation_{$animation}" ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			'on' === $is_overlay_applied ? ' et_pb_has_overlay' : ''
		);

		return $output;
	}
}
new ET_Builder_Module_Fullwidth_Image;

class ET_Builder_Module_Fullwidth_Post_Title extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Fullwidth Post Title', 'et_builder' );
		$this->slug             = 'et_pb_fullwidth_post_title';
		$this->fb_support       = true;
		$this->fullwidth        = true;
		$this->defaults         = array();

		$this->whitelisted_fields = array(
			'title',
			'meta',
			'author',
			'date',
			'date_format',
			'categories',
			'comments',
			'featured_image',
			'featured_placement',
			'parallax_effect',
			'parallax_method',
			'background_size',
			'background_position',
			'background_repeat',
			'background_blend',
			'text_orientation',
			'text_color',
			'text_background',
			'text_bg_color',
			'module_bg_color',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'title'              => array( 'on' ),
			'meta'               => array( 'on' ),
			'author'             => array( 'on' ),
			'date'               => array( 'on' ),
			'date_format'        => array( 'M j, Y' ),
			'categories'         => array( 'on' ),
			'comments'           => array( 'on' ),
			'featured_image'     => array( 'on' ),
			'featured_placement' => array( 'below' ),
			'parallax_effect'    => array( 'off' ),
			'parallax_method'    => array( 'on' ),
			'text_orientation'   => array( 'left' ),
			'text_color'         => array( 'dark' ),
			'text_background'    => array( 'off' ),
			'text_bg_color'      => array( 'rgba(255,255,255,0.9)', 'only_default_setting' ),
		);

		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'    => esc_html__( 'Elements', 'et_builder' ),
					'background'  => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'border'                => array(
				'css' => array(
					'main' => "{$this->main_css_element}.et_pb_featured_bg, {$this->main_css_element}",
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'main' => ".et_pb_fullwidth_section {$this->main_css_element}.et_pb_post_title",
					'important' => 'all',
				),
			),
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'use_all_caps' => true,
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_title_container h1.entry-title",
					),
				),
				'meta'   => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container, {$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container a",
						'plugin_main' => "{$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container, {$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container a, {$this->main_css_element} .et_pb_title_container .et_pb_title_meta_container span",
					),
				),
			),
			'background' => array(
				'use_background_color'          => false,
				'use_background_color_gradient' => true,
				'use_background_image'          => false,
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'             => esc_html__( 'Show Title', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Post Title', 'et_builder' ),
			),
			'meta' => array(
				'label'             => esc_html__( 'Show Meta', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'author',
					'date',
					'categories',
					'comments',
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Post Meta', 'et_builder' ),
			),
			'author' => array(
				'label'             => esc_html__( 'Show Author', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Author Name in Post Meta', 'et_builder' ),
			),
			'date' => array(
				'label'             => esc_html__( 'Show Date', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'affects'           => array(
					'date_format'
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Date in Post Meta', 'et_builder' ),
			),
			'date_format' => array(
				'label'             => esc_html__( 'Date Format', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can define the Date Format in Post Meta. Default is \'M j, Y\'', 'et_builder' ),
			),
			'categories' => array(
				'label'             => esc_html__( 'Show Post Categories', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Categories in Post Meta. Note: This option doesn\'t work with custom post types.', 'et_builder' ),
			),
			'comments' => array(
				'label'             => esc_html__( 'Show Comments Count', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Comments Count in Post Meta.', 'et_builder' ),
			),
			'featured_image' => array(
				'label'             => esc_html__( 'Show Featured Image', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'featured_placement',
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether or not display the Featured Image', 'et_builder' ),
			),
			'featured_placement' => array(
				'label'             => esc_html__( 'Featured Image Placement', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'below'      => esc_html__( 'Below Title', 'et_builder' ),
					'above'      => esc_html__( 'Above Title', 'et_builder' ),
					'background' => esc_html__( 'Title/Meta Background Image', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'affects'           => array(
					'parallax_effect',
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose where to place the Featured Image', 'et_builder' ),
			),
			'parallax_effect' => array(
				'label'             => esc_html__( 'Use Parallax Effect', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'background',
				'affects'           => array(
					'parallax_method',
					'background_size',
					'background_position',
					'background_repeat',
					'background_blend',
				),
				'tab_slug'          => 'general',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Here you can choose whether or not use parallax effect for the featured image', 'et_builder' ),
			),
			'parallax_method' => array(
				'label'             => esc_html__( 'Parallax Method', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'CSS', 'et_builder' ),
					'off' => esc_html__( 'True Parallax', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'general',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'Here you can choose which parallax method to use for the featured image', 'et_builder' ),
			),
			'background_size' => array(
				'label'           => esc_html__( 'Background Image Size', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'cover'   => esc_html__( 'Cover', 'et_builder' ),
					'contain' => esc_html__( 'Fit', 'et_builder' ),
					'initial' => esc_html__( 'Actual Size', 'et_builder' ),
				),
				'default'           => 'cover',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			),
			'background_position' => array(
				'label'           => esc_html__( 'Background Image Position', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'top_left'      => esc_html__( 'Top Left', 'et_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'et_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'et_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'et_builder' ),
					'center'        => esc_html__( 'Center', 'et_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'et_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'et_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'et_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'et_builder' ),
				),
				'default'           => 'center',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'background',
			),
			'background_repeat' => array(
				'label'           => esc_html__( 'Background Image Repeat', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'repeat'      => esc_html__( 'Repeat', 'et_builder' ),
					'repeat-x'    => esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
					'repeat-y'    => esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
					'space'       => esc_html__( 'Space', 'et_builder' ),
					'round'       => esc_html__( 'Round', 'et_builder' ),
					'no-repeat'   => esc_html__( 'No Repeat', 'et_builder' ),
				),
				'default'         => 'no-repeat',
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			),
			'background_blend' => array(
				'label'           => esc_html__( 'Background Image Blend', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options' => array(
					'normal'      => esc_html__( 'Normal', 'et_builder' ),
					'multiply'    => esc_html__( 'Multiply', 'et_builder' ),
					'screen'      => esc_html__( 'Screen', 'et_builder' ),
					'overlay'     => esc_html__( 'Overlay', 'et_builder' ),
					'darken'      => esc_html__( 'Darken', 'et_builder' ),
					'lighten'     => esc_html__( 'Lighten', 'et_builder' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'et_builder' ),
					'color-burn'  => esc_html__( 'Color Burn', 'et_builder' ),
					'hard-light'  => esc_html__( 'Hard Light', 'et_builder' ),
					'soft-light'  => esc_html__( 'Soft Light', 'et_builder' ),
					'difference'  => esc_html__( 'Difference', 'et_builder' ),
					'exclusion'   => esc_html__( 'Exclusion', 'et_builder' ),
					'hue'         => esc_html__( 'Hue', 'et_builder' ),
					'saturation'  => esc_html__( 'Saturation', 'et_builder' ),
					'color'       => esc_html__( 'Color', 'et_builder' ),
					'luminosity'  => esc_html__( 'Luminosity', 'et_builder' ),
				),
				'default'         => 'normal',
				'depends_show_if' => 'off',
				'toggle_slug'     => 'background',
			),
			'text_orientation' => array(
				'label'             => esc_html__( 'Text Orientation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'left'   => esc_html__( 'Left', 'et_builder' ),
					'center' => esc_html__( 'Center', 'et_builder' ),
					'right'  => esc_html__( 'Right', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose the orientation for the Title/Meta text', 'et_builder' ),
			),
			'text_color' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'color_option',
				'options'           => array(
					'dark'  => esc_html__( 'Dark', 'et_builder' ),
					'light' => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose the color for the Title/Meta text', 'et_builder' ),
			),
			'text_background' => array(
				'label'             => esc_html__( 'Use Text Background Color', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'color_option',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'text_bg_color',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose whether or not use the background color for the Title/Meta text', 'et_builder' ),
			),
			'text_bg_color' => array(
				'label'             => esc_html__( 'Text Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
			),
			'module_bg_color' => array(
				'label'        => esc_html__( 'Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$title              = $this->shortcode_atts['title'];
		$meta               = $this->shortcode_atts['meta'];
		$author             = $this->shortcode_atts['author'];
		$date               = $this->shortcode_atts['date'];
		$date_format        = $this->shortcode_atts['date_format'];
		$categories         = $this->shortcode_atts['categories'];
		$comments           = $this->shortcode_atts['comments'];
		$featured_image     = $this->shortcode_atts['featured_image'];
		$featured_placement = $this->shortcode_atts['featured_placement'];
		$parallax_effect    = $this->shortcode_atts['parallax_effect'];
		$parallax_method    = $this->shortcode_atts['parallax_method'];
		$background_size    = $this->shortcode_atts['background_size'];
		$background_position = $this->shortcode_atts['background_position'];
		$background_repeat  = $this->shortcode_atts['background_repeat'];
		$background_blend   = $this->shortcode_atts['background_blend'];
		$text_orientation   = $this->shortcode_atts['text_orientation'];
		$text_color         = $this->shortcode_atts['text_color'];
		$text_background    = $this->shortcode_atts['text_background'];
		$text_bg_color      = $this->shortcode_atts['text_bg_color'];
		$module_bg_color    = $this->shortcode_atts['module_bg_color'];
		$use_background_color_gradient              = $this->shortcode_atts['use_background_color_gradient'];
		$background_color_gradient_type             = $this->shortcode_atts['background_color_gradient_type'];
		$background_color_gradient_direction        = $this->shortcode_atts['background_color_gradient_direction'];
		$background_color_gradient_direction_radial = $this->shortcode_atts['background_color_gradient_direction_radial'];
		$background_color_gradient_start            = $this->shortcode_atts['background_color_gradient_start'];
		$background_color_gradient_end              = $this->shortcode_atts['background_color_gradient_end'];
		$background_color_gradient_start_position   = $this->shortcode_atts['background_color_gradient_start_position'];
		$background_color_gradient_end_position     = $this->shortcode_atts['background_color_gradient_end_position'];
		$background_images  = array();

		// display the shortcode only on singlular pages
		if ( ! is_singular() ) {
			return;
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$output = '';
		$featured_image_output = '';
		$parallax_background_contaier = '';

		if ( 'on' === $featured_image && ( 'above' === $featured_placement || 'below' === $featured_placement ) ) {
			$featured_image_output = sprintf( '<div class="et_pb_title_featured_container">%1$s</div>',
				get_the_post_thumbnail( get_the_ID(), 'large' )
			);
		}

		if ( 'on' === $title ) {
			if ( is_et_pb_preview() && isset( $_POST['post_title'] ) && wp_verify_nonce( $_POST['et_pb_preview_nonce'], 'et_pb_preview_nonce' ) ) {
				$post_title = sanitize_text_field( wp_unslash( $_POST['post_title'] ) );
			} else {
				$post_title = get_the_title();
			}

			$output .= sprintf( '<h1 class="entry-title">%s</h1>',
				$post_title
			);
		}

		if ( 'on' === $meta ) {
			$meta_array = array();
			foreach( array( 'author', 'date', 'categories', 'comments' ) as $single_meta ) {
				if ( 'on' === $$single_meta && ( 'categories' !== $single_meta || ( 'categories' === $single_meta && is_singular( 'post' ) ) ) ) {
					 $meta_array[] = $single_meta;
				}
			}

			$output .= sprintf( '<p class="et_pb_title_meta_container">%1$s</p>',
				et_pb_postinfo_meta( $meta_array, $date_format, esc_html__( '0 comments', 'et_builder' ), esc_html__( '1 comment', 'et_builder' ), '% ' . esc_html__( 'comments', 'et_builder' ) )
			);
		}

		if ( 'on' === $use_background_color_gradient ) {
			$has_background_gradient = true;

			$background_images[] = $this->get_gradient( array(
				'type'             => $background_color_gradient_type,
				'direction'        => $background_color_gradient_direction,
				'radial_direction' => $background_color_gradient_direction_radial,
				'color_start'      => $background_color_gradient_start,
				'color_end'        => $background_color_gradient_end,
				'start_position'   => $background_color_gradient_start_position,
				'end_position'     => $background_color_gradient_end_position,
			) );
		}

		if ( 'on' === $featured_image && 'background' === $featured_placement ) {
			$featured_image_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

			if ( 'on' === $parallax_effect ) {
				$parallax_background_contaier = sprintf( '<div class="et_parallax_bg%1$s"></div>',
					'on' === $parallax_method ? ' et_pb_parallax_css' : ''
				);

				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%.et_pb_featured_bg .et_parallax_bg',
					'declaration' => sprintf(
						'background-image: url("%1$s");',
						esc_url( $featured_image_src[0] )
					),
				) );
			} else {
				$has_background_image = true;

				$background_images[] = sprintf(
					'url(%1$s)',
					esc_url( $featured_image_src[0] )
				);

				if ( '' !== $background_size ) {
					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%.et_pb_featured_bg',
						'declaration' => sprintf(
							'background-size: %1$s;',
							esc_html( $background_size )
						),
					) );
				}

				if ( '' !== $background_position && 'default' !== $background_position ) {
					$processed_position = str_replace( '_', ' ', $background_position );

					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%.et_pb_featured_bg',
						'declaration' => sprintf(
							'background-position: %1$s;',
							esc_html( $processed_position )
						),
					) );
				}

				if ( '' !== $background_repeat ) {
					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%.et_pb_featured_bg',
						'declaration' => sprintf(
							'background-repeat: %1$s;',
							esc_html( $background_repeat )
						),
					) );
				}

				if ( '' !== $background_blend ) {
					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%.et_pb_featured_bg',
						'declaration' => sprintf(
							'background-blend-mode: %1$s;',
							esc_html( $background_blend )
						),
					) );
				}
			}
		}

		if ( ! empty( $background_images ) ) {
			$background_images = array_reverse( $background_images );

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => 'background' === $featured_placement ? '%%order_class%%.et_pb_featured_bg' : '%%order_class%%',
				'declaration' => sprintf(
					'background-image: %1$s;',
					esc_html( join( ', ', $background_images ) )
				),
			) );
		}

		if ( 'on' === $text_background ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_title_container',
				'declaration' => sprintf(
					'background-color: %1$s; padding: 1em 1.5em;',
					esc_html( $text_bg_color )
				),
			) );
		}

		ET_Builder_Element::set_style( $function_name, array(
			'selector'    => '%%order_class%%',
			'declaration' => sprintf(
				'text-align: %1$s;',
				esc_html( $text_orientation )
			),
		) );

		$video_background = $this->video_background();

		$background_layout = 'dark' === $text_color ? 'light' : 'dark';
		$module_class .= ' et_pb_bg_layout_' . $background_layout;

		$module_class .= 'above' === $featured_placement ? ' et_pb_image_above' : '';
		$module_class .= 'below' === $featured_placement ? ' et_pb_image_below' : '';

		if ( ! isset ( $has_background_gradient, $has_background_image ) ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $module_bg_color )
				),
			) );
		} else {
			// Force background-color: initial
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => 'background-color: initial;',
			) );
		}


		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_post_title %2$s%4$s%8$s">
				%5$s
				%9$s
				%6$s
				<div class="et_pb_title_container">
					%1$s
				</div>
				%7$s
			</div>',
			$output,
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			'on' === $featured_image && 'background' === $featured_placement ? ' et_pb_featured_bg' : '',
			$parallax_background_contaier,
			'on' === $featured_image && 'above' === $featured_placement ? $featured_image_output : '',
			'on' === $featured_image && 'below' === $featured_placement ? $featured_image_output : '',
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background
		);

		return $output;
	}
}
new ET_Builder_Module_Fullwidth_Post_Title;

class ET_Builder_Module_Fullwidth_Post_Slider extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Post Slider', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_post_slider';
		$this->fb_support = true;
		$this->fullwidth  = true;

		// need to use global settings from the fullwidth slider module
		$this->global_settings_slug = 'et_pb_fullwidth_slider';

		$this->whitelisted_fields = array(
			'show_arrows',
			'show_pagination',
			'auto',
			'auto_speed',
			'auto_ignore_hover',
			'parallax',
			'parallax_method',
			'remove_inner_shadow',
			'background_position',
			'background_size',
			'admin_label',
			'module_id',
			'module_class',
			'top_padding',
			'bottom_padding',
			'hide_content_on_mobile',
			'hide_cta_on_mobile',
			'show_image_video_mobile',
			'include_categories',
			'posts_number',
			'show_more_button',
			'more_text',
			'content_source',
			'background_color',
			'show_image',
			'image_placement',
			'background_image',
			'background_layout',
			'use_bg_overlay',
			'use_text_overlay',
			'bg_overlay_color',
			'text_overlay_color',
			'orderby',
			'show_meta',
			'use_manual_excerpt',
			'excerpt_length',
			'text_border_radius',
			'arrows_custom_color',
			'dot_nav_custom_color',
			'top_padding_tablet',
			'top_padding_phone',
			'top_padding_last_edited',
			'bottom_padding_tablet',
			'bottom_padding_phone',
			'bottom_padding_last_edited',
		);

		$this->fields_defaults = array(
			'show_arrows'             => array( 'on' ),
			'show_pagination'         => array( 'on' ),
			'auto'                    => array( 'off' ),
			'auto_speed'              => array( '7000' ),
			'auto_ignore_hover'       => array( 'off' ),
			'parallax'                => array( 'off' ),
			'parallax_method'         => array( 'off' ),
			'remove_inner_shadow'     => array( 'off' ),
			'background_position'     => array( 'center' ),
			'background_size'         => array( 'cover' ),
			'hide_content_on_mobile'  => array( 'off' ),
			'hide_cta_on_mobile'      => array( 'off' ),
			'show_image_video_mobile' => array( 'off' ),
			'more_text'               => array( 'Read More' ),
			'background_color'        => array( '' ),
			'image_placement'         => array( 'background' ),
			'background_layout'       => array( 'dark' ),
			'orderby'                 => array( 'date_desc' ),
			'excerpt_length'          => array( '270' ),
			'use_bg_overlay'          => array( 'on' ),
			'show_meta'               => array( 'on' ),
			'show_more_button'        => array( 'on' ),
			'show_image'              => array( 'on' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_slider';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'   => esc_html__( 'Content', 'et_builder' ),
					'elements'       => esc_html__( 'Elements', 'et_builder' ),
					'featured_image' => esc_html__( 'Featured Image', 'et_builder' ),
					'background'     => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'     => esc_html__( 'Layout', 'et_builder' ),
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'navigation' => esc_html__( 'Navigation', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'padding'    => array(
						'title'    => esc_html__( 'Spacing', 'et_builder' ),
						'priority' => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Header', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title",
						'important' => array( 'size', 'font-size' ),
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element}",
						'main' => "{$this->main_css_element} .et_pb_slide_content, {$this->main_css_element} .et_pb_slide_content div",
						'important' => 'all',
					),
				),
				'meta'   => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_slide_content .post-meta, {$this->main_css_element} .et_pb_slide_content .post-meta a",
						'plugin_main' => "{$this->main_css_element} .et_pb_slide_content .post-meta, {$this->main_css_element} .et_pb_slide_content .post-meta a, {$this->main_css_element} .et_pb_slide_content .post-meta span",
						'important' => 'all',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '16px',
					),
					'letter_spacing' => array(
						'default' => '0',
					),
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
					),
				),
			),
			'background' => array(
				'use_background_color'          => true,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'css' => array(
					'main' => '%%order_class%%, %%order_class%%.et_pb_bg_layout_dark, %%order_class%%.et_pb_bg_layout_light',
				),
			),
		);
		$this->custom_css_options = array(
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description .et_pb_slide_title',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector' => '.et_pb_slider a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_controllers' => array(
				'label'    => esc_html__( 'Slide Controllers', 'et_builder' ),
				'selector' => '.et-pb-controllers',
			),
			'slide_active_controller' => array(
				'label'    => esc_html__( 'Slide Active Controller', 'et_builder' ),
				'selector' => '.et-pb-controllers .et-pb-active-control',
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
			'slide_arrows' => array(
				'label'    => esc_html__( 'Slide Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);
	}

	static function get_blog_posts( $args = array(), $conditional_tags = array(), $current_page = array(), $is_ajax_request = true ) {
		$defaults = array(
			'posts_number'       => '',
			'include_categories' => '',
			'orderby'            => '',
			'content_source'     => '',
			'use_manual_excerpt' => '',
			'excerpt_length'     => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array(
			'posts_per_page' => (int) $args['posts_number'],
			'post_status'    => 'publish',
		);

		if ( '' !== $args['include_categories'] ) {
			$query_args['cat'] = $args['include_categories'];
		}

		if ( 'date_desc' !== $args['orderby'] ) {
			switch( $args['orderby'] ) {
				case 'date_asc' :
					$query_args['orderby'] = 'date';
					$query_args['order'] = 'ASC';
					break;
				case 'title_asc' :
					$query_args['orderby'] = 'title';
					$query_args['order'] = 'ASC';
					break;
				case 'title_desc' :
					$query_args['orderby'] = 'title';
					$query_args['order'] = 'DESC';
					break;
				case 'rand' :
					$query_args['orderby'] = 'rand';
					break;
			}
		}

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();

				$post_author_id = $query->posts[ $post_index ]->post_author;

				$categories = array();

				$categories_object = get_the_terms( get_the_ID(), 'category' );

				if ( ! empty( $categories_object ) ) {
					foreach ( $categories_object as $category ) {
						$categories[] = array(
							'id' => $category->term_id,
							'label' => $category->name,
							'permalink' => get_term_link( $category ),
						);
					}
				}

				$query->posts[ $post_index ]->post_featured_image = esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) );
				$query->posts[ $post_index ]->has_post_thumbnail  = has_post_thumbnail();
				$query->posts[ $post_index ]->post_permalink      = get_the_permalink();
				$query->posts[ $post_index ]->post_author_url     = get_author_posts_url( $post_author_id );
				$query->posts[ $post_index ]->post_author_name    = get_the_author_meta( 'display_name', $post_author_id );
				$query->posts[ $post_index ]->post_date_readable  = get_the_date();
				$query->posts[ $post_index ]->categories          = $categories;
				$query->posts[ $post_index ]->post_comment_popup  = sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) );

				$post_content = et_strip_shortcodes( get_the_content(), true );

				global $et_fb_processing_shortcode_object, $et_pb_rendering_column_content;

				$global_processing_original_value = $et_fb_processing_shortcode_object;

				// reset the fb processing flag
				$et_fb_processing_shortcode_object = false;
				// set the flag to indicate that we're processing internal content
				$et_pb_rendering_column_content = true;

				if ( $is_ajax_request ) {
					// reset all the attributes required to properly generate the internal styles
					ET_Builder_Element::clean_internal_modules_styles();
				}

				if ( 'on' === $args['content_source'] ) {
					global $more;

					// page builder doesn't support more tag, so display the_content() in case of post made with page builder
					if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {

						$builder_post_content = et_is_builder_plugin_active() ? do_shortcode( $post_content ) : apply_filters( 'the_content', $post_content );

						// Overwrite default content, in case the content is protected
						$query->posts[ $post_index ]->post_content = $builder_post_content;
					} else {
						$more = null;

						// Overwrite default content, in case the content is protected
						$query->posts[ $post_index ]->post_content = et_is_builder_plugin_active() ? do_shortcode( get_the_content('') ) : apply_filters( 'the_content', get_the_content('') );
					}
				} else {
					if ( has_excerpt() && 'off' !== $args['use_manual_excerpt'] ) {
						$query->posts[ $post_index ]->post_content = et_is_builder_plugin_active() ? do_shortcode( et_strip_shortcodes( get_the_excerpt(), true ) ) : apply_filters( 'the_content', et_strip_shortcodes( get_the_excerpt(), true ) );
					} else {
						$query->posts[ $post_index ]->post_content = strip_shortcodes( truncate_post( intval( $args['excerpt_length'] ), false, '', true ) );
					}
				}

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				if ( $is_ajax_request ) {
					// retrieve the styles for the modules inside Blog content
					$internal_style = ET_Builder_Element::get_style( true );

					// reset all the attributes after we retrieved styles
					ET_Builder_Element::clean_internal_modules_styles( false );

					$query->posts[ $post_index ]->internal_styles = $internal_style;
				}

				$et_pb_rendering_column_content = false;

				$post_index++;
			} // end while
			wp_reset_query();
		} // end if

		return $query;
	}

	function get_fields() {
		$fields = array(
			'posts_number' => array(
				'label'             => esc_html__( 'Posts Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Choose how many posts you would like to display in the slider.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'       => 'main_content',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'renderer'         => 'et_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'description'      => esc_html__( 'Choose which categories you would like to include in the slider.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
			),
			'orderby' => array(
				'label'             => esc_html__( 'Order By', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'date_desc'  => esc_html__( 'Date: new to old', 'et_builder' ),
					'date_asc'   => esc_html__( 'Date: old to new', 'et_builder' ),
					'title_asc'  => esc_html__( 'Title: a-z', 'et_builder' ),
					'title_desc' => esc_html__( 'Title: z-a', 'et_builder' ),
					'rand'       => esc_html__( 'Random', 'et_builder' ),
				),
				'description'       => esc_html__( 'Here you can adjust the order in which posts are displayed.', 'et_builder' ),
				'computed_affects'   => array(
					'__posts',
				),
				'toggle_slug'    => 'main_content',
			),
			'show_arrows'         => array(
				'label'           => esc_html__( 'Show Arrows', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'This setting will turn on and off the navigation arrows.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Controls', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'This setting will turn on and off the circle buttons at the bottom of the slider.', 'et_builder' ),
			),
			'show_more_button' => array(
				'label'             => esc_html__( 'Show Read More Button', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					'more_text',
				),
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'This setting will turn on and off the read more button.', 'et_builder' ),
			),
			'more_text' => array(
				'label'             => esc_html__( 'Button Text', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'main_content',
				'description'       => esc_html__( 'Define the text which will be displayed on "Read More" button. Leave blank for default ( Read More )', 'et_builder' ),
			),
			'content_source' => array(
				'label'             => esc_html__( 'Content Display', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'Show Excerpt', 'et_builder' ),
					'on'  => esc_html__( 'Show Content', 'et_builder' ),
				),
				'affects' => array(
					'use_manual_excerpt',
					'excerpt_length',
				),
				'description'       => esc_html__( 'Showing the full content will not truncate your posts in the slider. Showing the excerpt will only display excerpt text.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'use_manual_excerpt' => array(
				'label'             => esc_html__( 'Use Post Excerpt if Defined', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Disable this option if you want to ignore manually defined excerpts and always generate it automatically.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'excerpt_length' => array(
				'label'             => esc_html__( 'Automatic Excerpt Length', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Define the length of automatically generated excerpts. Leave blank for default ( 270 ) ', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'computed_affects'  => array(
					'__posts',
				),
			),
			'show_meta' => array(
				'label'           => esc_html__( 'Show Post Meta', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'This setting will turn on and off the meta section.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text is light or dark. If you have a slide with a dark background, then choose light text. If you have a light background, then use dark text.' , 'et_builder' ),
			),
			'show_image' => array(
				'label'             => esc_html__( 'Show Featured Image', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					'image_placement',
				),
				'toggle_slug'       => 'featured_image',
				'description'       => esc_html__( 'This setting will turn on and off the featured image in the slider.', 'et_builder' ),
			),
			'image_placement' => array(
				'label'             => esc_html__( 'Image Placement', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'background' => esc_html__( 'Background', 'et_builder' ),
					'left'       => esc_html__( 'Left', 'et_builder' ),
					'right'      => esc_html__( 'Right', 'et_builder' ),
					'top'        => esc_html__( 'Top', 'et_builder' ),
					'bottom'     => esc_html__( 'Bottom', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'toggle_slug'       => 'featured_image',
				'description'       => esc_html__( 'Select how you would like to display the featured image in slides', 'et_builder' ),
			),
			'use_bg_overlay'      => array(
				'label'           => esc_html__( 'Use Background Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'         => array(
					'bg_overlay_color',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'et_builder' ),
			),
			'bg_overlay_color' => array(
				'label'             => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'et_builder' ),
			),
			'use_text_overlay'      => array(
				'label'           => esc_html__( 'Use Text Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'yes', 'et_builder' ),
				),
				'affects'         => array(
					'text_overlay_color',
					'text_border_radius',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'et_builder' ),
			),
			'text_overlay_color' => array(
				'label'             => esc_html__( 'Text Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'et_builder' ),
			),
			'remove_inner_shadow' => array(
				'label'           => esc_html__( 'Remove Inner Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'auto' => array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
					'auto_ignore_hover',
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
			),
			'auto_speed' => array(
				'label'             => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'depends_default'   => true,
				'tab_slug'          => 'custom_css',
				'toggle_slug'       => 'animation',
				'description'       => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
			),
			'auto_ignore_hover' => array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'et_builder' ),
			),
			'top_padding' => array(
				'label'           => esc_html__( 'Top Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'bottom_padding' => array(
				'label'           => esc_html__( 'Bottom Padding', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'padding',
				'mobile_options'  => true,
				'validate_unit'   => true,
			),
			'hide_content_on_mobile' => array(
				'label'           => esc_html__( 'Hide Content On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'hide_cta_on_mobile' => array(
				'label'           => esc_html__( 'Hide CTA On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'show_image_video_mobile' => array(
				'label'           => esc_html__( 'Show Image On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'text_border_radius' => array(
				'label'           => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'default'         => '3',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
			),
			'arrows_custom_color' => array(
				'label'        => esc_html__( 'Arrows Custom Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'dot_nav_custom_color' => array(
				'label'        => esc_html__( 'Dot Nav Custom Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'top_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'top_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'top_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bottom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bottom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'bottom_padding_last_edited' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'padding',
			),
			'__posts' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Fullwidth_Post_Slider', 'get_blog_posts' ),
				'computed_depends_on' => array(
					'posts_number',
					'include_categories',
					'orderby',
					'content_source',
					'use_manual_excerpt',
					'excerpt_length',
				),
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		/**
		 * Cached $wp_filter so it can be restored at the end of the callback.
		 * This is needed because this callback uses the_content filter / calls a function
		 * which uses the_content filter. WordPress doesn't support nested filter
		 */
		global $wp_filter;
		$wp_filter_cache = $wp_filter;

		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$show_arrows             = $this->shortcode_atts['show_arrows'];
		$show_pagination         = $this->shortcode_atts['show_pagination'];
		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$auto                    = $this->shortcode_atts['auto'];
		$auto_speed              = $this->shortcode_atts['auto_speed'];
		$auto_ignore_hover       = $this->shortcode_atts['auto_ignore_hover'];
		$top_padding             = $this->shortcode_atts['top_padding'];
		$body_font_size 		 = $this->shortcode_atts['body_font_size'];
		$bottom_padding          = $this->shortcode_atts['bottom_padding'];
		$remove_inner_shadow     = $this->shortcode_atts['remove_inner_shadow'];
		$hide_content_on_mobile  = $this->shortcode_atts['hide_content_on_mobile'];
		$hide_cta_on_mobile      = $this->shortcode_atts['hide_cta_on_mobile'];
		$show_image_video_mobile = $this->shortcode_atts['show_image_video_mobile'];
		$background_position     = $this->shortcode_atts['background_position'];
		$background_size         = $this->shortcode_atts['background_size'];
		$posts_number            = $this->shortcode_atts['posts_number'];
		$include_categories      = $this->shortcode_atts['include_categories'];
		$show_more_button        = $this->shortcode_atts['show_more_button'];
		$more_text               = $this->shortcode_atts['more_text'];
		$content_source          = $this->shortcode_atts['content_source'];
		$background_color        = $this->shortcode_atts['background_color'];
		$show_image              = $this->shortcode_atts['show_image'];
		$image_placement         = $this->shortcode_atts['image_placement'];
		$background_image        = $this->shortcode_atts['background_image'];
		$background_layout       = $this->shortcode_atts['background_layout'];
		$background_repeat       = $this->shortcode_atts['background_repeat'];
		$background_blend        = $this->shortcode_atts['background_blend'];
		$use_bg_overlay          = $this->shortcode_atts['use_bg_overlay'];
		$bg_overlay_color        = $this->shortcode_atts['bg_overlay_color'];
		$use_text_overlay        = $this->shortcode_atts['use_text_overlay'];
		$text_overlay_color      = $this->shortcode_atts['text_overlay_color'];
		$orderby                 = $this->shortcode_atts['orderby'];
		$show_meta               = $this->shortcode_atts['show_meta'];
		$button_custom           = $this->shortcode_atts['custom_button'];
		$custom_icon             = $this->shortcode_atts['button_icon'];
		$use_manual_excerpt      = $this->shortcode_atts['use_manual_excerpt'];
		$excerpt_length          = $this->shortcode_atts['excerpt_length'];
		$text_border_radius      = $this->shortcode_atts['text_border_radius'];
		$dot_nav_custom_color    = $this->shortcode_atts['dot_nav_custom_color'];
		$arrows_custom_color     = $this->shortcode_atts['arrows_custom_color'];
		$top_padding_tablet      = $this->shortcode_atts['top_padding_tablet'];
		$top_padding_phone       = $this->shortcode_atts['top_padding_phone'];
		$top_padding_last_edited = $this->shortcode_atts['top_padding_last_edited'];
		$bottom_padding_tablet   = $this->shortcode_atts['bottom_padding_tablet'];
		$bottom_padding_phone    = $this->shortcode_atts['bottom_padding_phone'];
		$bottom_padding_last_edited = $this->shortcode_atts['bottom_padding_last_edited'];

		$post_index = 0;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$hide_on_mobile_class = self::HIDE_ON_MOBILE;

		if ( '' !== $top_padding || '' !== $top_padding_tablet || '' !== $top_padding_phone ) {
			$top_padding_responsive_active = et_pb_get_responsive_status( $top_padding_last_edited );

			$padding_values = array(
				'desktop' => $top_padding,
				'tablet'  => $top_padding_responsive_active ? $top_padding_tablet : '',
				'phone'   => $top_padding_responsive_active ? $top_padding_phone : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_slide_description', 'padding-top', $function_name );
		}

		if ( '' !== $bottom_padding || '' !== $bottom_padding_tablet || '' !== $bottom_padding_phone ) {
			$bottom_padding_responsive_active = et_pb_get_responsive_status( $bottom_padding_last_edited );

			$padding_values = array(
				'desktop' => $bottom_padding,
				'tablet'  => $bottom_padding_responsive_active ? $bottom_padding_tablet : '',
				'phone'   => $bottom_padding_responsive_active ? $bottom_padding_phone : '',
			);

			et_pb_generate_responsive_css( $padding_values, '%%order_class%% .et_pb_slide_description', 'padding-bottom', $function_name );
		}

		if ( '' !== $bottom_padding || '' !== $top_padding ) {
			ET_Builder_Module::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide_description',
				'declaration' => 'padding-right: 0; padding-left: 0;',
			) );
		}

		// Applying backround-related style to slide item since advanced_option only targets module wrapper
		if ( 'on' === $this->shortcode_atts['show_image'] && 'background' === $this->shortcode_atts['image_placement'] && 'off' === $parallax ) {
			if ('' !== $background_color) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide:not(.et_pb_slide_with_no_image)',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $background_color )
					),
				) );
			}

			if ( '' !== $background_size && 'default' !== $background_size ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'-moz-background-size: %1$s;
						-webkit-background-size: %1$s;
						background-size: %1$s;',
						esc_html( $background_size )
					),
				) );

				if ( 'initial' === $background_size ) {
					ET_Builder_Module::set_style( $function_name, array(
						'selector'    => 'body.ie %%order_class%% .et_pb_slide',
						'declaration' => '-moz-background-size: auto; -webkit-background-size: auto; background-size: auto;',
					) );
				}
			}

			if ( '' !== $background_position && 'default' !== $background_position ) {
				$processed_position = str_replace( '_', ' ', $background_position );

				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'background-position: %1$s;',
						esc_html( $processed_position )
					),
				) );
			}

			if ( '' !== $background_repeat ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'background-repeat: %1$s;',
						esc_html( $background_repeat )
					),
				) );
			}

			if ( '' !== $background_blend ) {
				ET_Builder_Module::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_slide',
					'declaration' => sprintf(
						'background-blend-mode: %1$s;',
						esc_html( $background_blend )
					),
				) );
			}
		}

		if ( 'on' === $use_bg_overlay && '' !== $bg_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide .et_pb_slide_overlay_container',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $bg_overlay_color )
				),
			) );
		}

		if ( 'on' === $use_text_overlay && '' !== $text_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_slide .et_pb_slide_title, %%order_class%% .et_pb_slide .et_pb_slide_content',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $text_overlay_color )
				),
			) );
		}

		if ( '' !== $text_border_radius ) {
			$border_radius_value = et_builder_process_range_value( $text_border_radius );
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_slider_with_text_overlay h2.et_pb_slide_title',
				'declaration' => sprintf(
					'-webkit-border-top-left-radius: %1$s;
					-webkit-border-top-right-radius: %1$s;
					-moz-border-radius-topleft: %1$s;
					-moz-border-radius-topright: %1$s;
					border-top-left-radius: %1$s;
					border-top-right-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_slider_with_text_overlay .et_pb_slide_content',
				'declaration' => sprintf(
					'-webkit-border-bottom-right-radius: %1$s;
					-webkit-border-bottom-left-radius: %1$s;
					-moz-border-radius-bottomright: %1$s;
					-moz-border-radius-bottomleft: %1$s;
					border-bottom-right-radius: %1$s;
					border-bottom-left-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );
		}

		$fullwidth = 'et_pb_fullwidth_post_slider' === $function_name ? 'on' : 'off';

		$class  = '';
		$class .= 'off' === $fullwidth ? ' et_pb_slider_fullwidth_off' : '';
		$class .= 'off' === $show_arrows ? ' et_pb_slider_no_arrows' : '';
		$class .= 'off' === $show_pagination ? ' et_pb_slider_no_pagination' : '';
		$class .= 'on' === $parallax ? ' et_pb_slider_parallax' : '';
		$class .= 'on' === $auto ? ' et_slider_auto et_slider_speed_' . esc_attr( $auto_speed ) : '';
		$class .= 'on' === $auto_ignore_hover ? ' et_slider_auto_ignore_hover' : '';
		$class .= 'on' === $remove_inner_shadow ? ' et_pb_slider_no_shadow' : '';
		$class .= 'on' === $show_image_video_mobile ? ' et_pb_slider_show_image' : '';
		$class .= ' et_pb_post_slider_image_' . $image_placement;
		$class .= 'on' === $use_bg_overlay ? ' et_pb_slider_with_overlay' : '';
		$class .= 'on' === $use_text_overlay ? ' et_pb_slider_with_text_overlay' : '';

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$data_dot_nav_custom_color = '' !== $dot_nav_custom_color
			? sprintf( ' data-dots_color="%1$s"', esc_attr( $dot_nav_custom_color ) )
			: '';

		$data_arrows_custom_color = '' !== $arrows_custom_color
			? sprintf( ' data-arrows_color="%1$s"', esc_attr( $arrows_custom_color ) )
			: '';

		ob_start();

		// Re-used self::get_blog_posts() for builder output
		$query = self::get_blog_posts(array(
			'posts_number'       => $posts_number,
			'include_categories' => $include_categories,
			'orderby'            => $orderby,
			'content_source'     => $content_source,
			'use_manual_excerpt' => $use_manual_excerpt,
			'excerpt_length'     => $excerpt_length,
		), array(), array(), false );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$slide_class = 'off' !== $show_image && in_array( $image_placement, array( 'left', 'right' ) ) && has_post_thumbnail() ? ' et_pb_slide_with_image' : '';
				$slide_class .= 'off' !== $show_image && ! has_post_thumbnail() ? ' et_pb_slide_with_no_image' : '';
				$slide_class .= " et_pb_bg_layout_{$background_layout}";
			?>
			<div class="et_pb_slide et_pb_media_alignment_center<?php echo esc_attr( $slide_class ); ?>" <?php if ( 'on' !== $parallax && 'off' !== $show_image && 'background' === $image_placement ) { printf( 'style="background-image:url(%1$s)"', esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ) );  } ?><?php echo $data_dot_nav_custom_color; echo $data_arrows_custom_color; ?>>
				<?php if ( 'on' === $parallax && 'off' !== $show_image && 'background' === $image_placement ) { ?>
					<div class="et_parallax_bg<?php if ( 'off' === $parallax_method ) { echo ' et_pb_parallax_css'; } ?>" style="background-image: url(<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ); ?>);"></div>
				<?php } ?>
				<?php if ( 'on' === $use_bg_overlay ) { ?>
					<div class="et_pb_slide_overlay_container"></div>
				<?php } ?>
				<div class="et_pb_container clearfix">
					<div class="et_pb_slider_container_inner">
						<?php if ( 'off' !== $show_image && has_post_thumbnail() && ! in_array( $image_placement, array( 'background', 'bottom' ) ) ) { ?>
							<div class="et_pb_slide_image">
								<?php the_post_thumbnail(); ?>
							</div>
						<?php } ?>
						<div class="et_pb_slide_description">
							<h2 class="et_pb_slide_title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h2>
							<div class="et_pb_slide_content <?php if ( 'on' === $hide_content_on_mobile ) { echo esc_attr( $hide_on_mobile_class ); } ?>">
								<?php
								if ( 'off' !== $show_meta ) {
									printf(
										'<p class="post-meta">%1$s | %2$s | %3$s | %4$s</p>',
										et_get_safe_localization( sprintf( __( 'by %s', 'et_builder' ), '<span class="author vcard">' .  et_pb_get_the_author_posts_link() . '</span>' ) ),
										et_get_safe_localization( sprintf( __( '%s', 'et_builder' ), '<span class="published">' . esc_html( get_the_date() ) . '</span>' ) ),
										get_the_category_list(', '),
										sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) )
									);
								}
								?>
								<?php
									echo $query->posts[ $post_index ]->post_content;
								?>
							</div>
							<?php if ( 'off' !== $show_more_button && '' !== $more_text ) {
									printf(
										'<a href="%1$s" class="et_pb_more_button et_pb_button%4$s%5$s"%3$s>%2$s</a>',
										esc_url( get_permalink() ),
										esc_html( $more_text ),
										'' !== $custom_icon && 'on' === $button_custom ? sprintf(
											' data-icon="%1$s"',
											esc_attr( et_pb_process_font_icon( $custom_icon ) )
										) : '',
										'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : '',
										'on' === $hide_cta_on_mobile ? esc_attr( " {$hide_on_mobile_class}" ) : ''
									);
								}
							?>
						</div> <!-- .et_pb_slide_description -->
						<?php if ( 'off' !== $show_image && has_post_thumbnail() && 'bottom' === $image_placement ) { ?>
							<div class="et_pb_slide_image">
								<?php the_post_thumbnail(); ?>
							</div>
						<?php } ?>
					</div>
				</div> <!-- .et_pb_container -->
			</div> <!-- .et_pb_slide -->
		<?php
			$post_index++;

			} // end while
			wp_reset_query();
		} // end if

		$content = ob_get_contents();

		ob_end_clean();

		$output = sprintf(
			'<div%3$s class="et_pb_module et_pb_slider et_pb_post_slider%1$s%4$s%5$s%7$s">
				%8$s
				%6$s
				<div class="et_pb_slides">
					%2$s
				</div> <!-- .et_pb_slides -->
			</div> <!-- .et_pb_slider -->
			',
			$class,
			$content,
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		// Restore $wp_filter
		$wp_filter = $wp_filter_cache;
		unset($wp_filter_cache);

		return $output;
	}
}

new ET_Builder_Module_Fullwidth_Post_Slider;
