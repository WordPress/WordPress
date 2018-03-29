<?php

class ET_Builder_Section extends ET_Builder_Structure_Element {
	function init() {
		$this->name = esc_html__( 'Section', 'et_builder' );
		$this->slug = 'et_pb_section';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'transparent_background',
			'transparent_background_fb',
			'background_color',
			'inner_shadow',
			'parallax_1',
			'parallax_2',
			'parallax_3',
			'parallax_method_1',
			'parallax_method_2',
			'parallax_method_3',
			'custom_padding',
			'custom_padding_tablet',
			'custom_padding_phone',
			'custom_padding_last_edited',
			'padding_mobile',
			'module_id',
			'module_class',
			'make_fullwidth',
			'use_custom_width',
			'width_unit',
			'custom_width_px',
			'custom_width_percent',
			'make_equal',
			'use_custom_gutter',
			'gutter_width',
			'columns',
			'fullwidth',
			'specialty',
			'background_color_1',
			'background_color_2',
			'background_color_3',
			'bg_img_1',
			'bg_img_2',
			'bg_img_3',
			'padding_top_1',
			'padding_right_1',
			'padding_bottom_1',
			'padding_left_1',
			'padding_top_2',
			'padding_right_2',
			'padding_bottom_2',
			'padding_left_2',
			'padding_top_3',
			'padding_right_3',
			'padding_bottom_3',
			'padding_left_3',
			'padding_1_tablet',
			'padding_2_tablet',
			'padding_3_tablet',
			'padding_1_phone',
			'padding_2_phone',
			'padding_3_phone',
			'padding_1_last_edited',
			'padding_2_last_edited',
			'padding_3_last_edited',
			'admin_label',
			'module_id_1',
			'module_id_2',
			'module_id_3',
			'module_class_1',
			'module_class_2',
			'module_class_3',
			'custom_css_before_1',
			'custom_css_before_2',
			'custom_css_before_3',
			'custom_css_main_1',
			'custom_css_main_2',
			'custom_css_main_3',
			'custom_css_after_1',
			'custom_css_after_2',
			'custom_css_after_3',
			'background_size_1',
			'background_position_1',
			'background_repeat_1',
			'background_blend_1',
			'use_background_color_gradient_1',
			'background_color_gradient_start_1',
			'background_color_gradient_end_1',
			'background_color_gradient_type_1',
			'background_color_gradient_direction_1',
			'background_color_gradient_direction_radial_1',
			'background_color_gradient_start_position_1',
			'background_color_gradient_end_position_1',
			'background_size_2',
			'background_position_2',
			'background_repeat_2',
			'background_blend_2',
			'use_background_color_gradient_2',
			'background_color_gradient_start_2',
			'background_color_gradient_end_2',
			'background_color_gradient_type_2',
			'background_color_gradient_direction_2',
			'background_color_gradient_direction_radial_2',
			'background_color_gradient_start_position_2',
			'background_color_gradient_end_position_2',
			'background_size_3',
			'background_position_3',
			'background_repeat_3',
			'background_blend_3',
			'use_background_color_gradient_3',
			'background_color_gradient_start_3',
			'background_color_gradient_end_3',
			'background_color_gradient_type_3',
			'background_color_gradient_direction_3',
			'background_color_gradient_direction_radial_3',
			'background_color_gradient_start_position_3',
			'background_color_gradient_end_position_3',
			'background_video_mp4_1',
			'background_video_webm_1',
			'background_video_width_1',
			'background_video_height_1',
			'allow_player_pause_1',
			'__video_background_1',
			'background_video_mp4_2',
			'background_video_webm_2',
			'background_video_width_2',
			'background_video_height_2',
			'allow_player_pause_2',
			'__video_background_2',
			'background_video_mp4_3',
			'background_video_webm_3',
			'background_video_width_3',
			'background_video_height_3',
			'allow_player_pause_3',
			'__video_background_3',
		);

		$this->options_toggles = array(
			'general' => array(
				'toggles' => array(
					'background'     => array(
						'title'       => esc_html__( 'Background', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
						),
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'          => esc_html__( 'Layout', 'et_builder' ),
					'width'           => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
					'margin_padding'  => array(
						'title'       => esc_html__( 'Spacing', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
						),
						'priority'   => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'classes' => array(
						'title'  => esc_html__( 'CSS ID & Classes', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
						),
					),
					'custom_css' => array(
						'title'  => esc_html__( 'Custom CSS', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
						),
					),
				),
			),
		);

		$this->advanced_options = array(
			'background' => array(
				'use_background_color'          => false,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'use_background_video'          => true,
				'css'                           => array(
					'important' => 'all',
					'main'      => 'div.et_pb_section%%order_class%%',
				),
			),
		);

		$this->fields_defaults = array(
			'transparent_background' => array( 'default' ),
			'background_color'       => array( '', 'only_default_setting' ),
			'allow_player_pause'     => array( 'off' ),
			'inner_shadow'           => array( 'off' ),
			'parallax'               => array( 'off' ),
			'parallax_method'        => array( 'on' ),
			'padding_mobile'         => array( '' ),
			'make_fullwidth'         => array( 'off' ),
			'use_custom_width'       => array( 'off' ),
			'width_unit'             => array( 'on' ),
			'custom_width_px'        => array( '1080px', 'only_default_setting' ),
			'custom_width_percent'   => array( '80%', 'only_default_setting' ),
			'make_equal'             => array( 'off' ),
			'use_custom_gutter'      => array( 'off' ),
			'gutter_width'           => array( '' ),
			'fullwidth'              => array( 'off' ),
			'specialty'              => array( 'off' ),
			'custom_padding_tablet'  => array( '' ),
			'custom_padding_phone'   => array( '' ),
		);
	}

	function get_fields() {
		$fields = array(
			'background_color' => array(
				'label'           => esc_html__( 'Background Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'default'         => '#ffffff',
				'depends_show_if' => 'off',
				'description'     => esc_html__( 'Define a custom background color for your module, or leave blank to use the default color.', 'et_builder' ),
				'additional_code' => '<span class="et-pb-reset-setting reset-default-color" style="display: none;"></span>',
				'toggle_slug'     => 'background',
			),
			'transparent_background' => array(
				'label'             => esc_html__( 'Transparent Background Color', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'color_option',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'background_color',
				),
				'description'       => esc_html__( 'Enabling this option will remove the background color of this section, allowing the website background color or background image to show through.', 'et_builder' ),
				'toggle_slug'       => 'background',
			),
			'transparent_background_fb' => array(
				'type' => 'skip',
			),
			'inner_shadow' => array(
				'label'           => esc_html__( 'Show Inner Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'         => 'off',
				'description'     => esc_html__( 'Here you can select whether or not your section has an inner shadow. This can look great when you have colored backgrounds or background images.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'custom_padding' => array(
				'label'           => esc_html__( 'Custom Padding', 'et_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
			),
			'custom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'custom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'padding_mobile' => array(
				'label' => esc_html__( 'Keep Custom Padding on Mobile', 'et_builder' ),
				'type'        => 'skip', // Remaining attribute for backward compatibility
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'make_fullwidth' => array(
				'label'             => esc_html__( 'Make This Section Fullwidth', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'use_custom_width' => array(
				'label'             => esc_html__( 'Use Custom Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'make_fullwidth',
					'custom_width',
					'width_unit',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'width_unit' => array(
				'label'             => esc_html__( 'Unit', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'px', 'et_builder' ),
					'off' => '%',
				),
				'default'           => 'on',
				'button_options'    => array(
					'button_type' => 'equal',
				),
				'depends_show_if'   => 'on',
				'affects'           => array(
					'custom_width_px',
					'custom_width_percent',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'custom_width_px' => array(
				'label'               => esc_html__( 'Custom Width', 'et_builder' ),
				'type'                => 'range',
				'option_category'     => 'layout',
				'depends_show_if_not' => 'off',
				'validate_unit'       => true,
				'fixed_unit'          => 'px',
				'range_settings'      => array(
					'min'  => 500,
					'max'  => 2600,
					'step' => 1,
				),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'width',
				'specialty_only'      => 'yes',
			),
			'custom_width_percent' => array(
				'label'           => esc_html__( 'Custom Width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'depends_show_if' => 'off',
				'validate_unit'   => true,
				'fixed_unit'      => '%',
				'range_settings'  => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'specialty_only'  => 'yes',
			),
			'make_equal' => array(
				'label'             => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'use_custom_gutter' => array(
				'label'             => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'gutter_width',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'gutter_width' => array(
				'label'           => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'range_settings'  => array(
					'min'  => 1,
					'max'  => 4,
					'step' => 1,
				),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'specialty_only'  => 'yes',
				'validate_unit'   => false,
				'fixed_range'     => true,
			),
			'columns_background' => array(
				'type'            => 'column_settings_background',
				'option_category' => 'configuration',
				'toggle_slug'     => 'background',
				'specialty_only'  => 'yes',
			),
			'columns_padding' => array(
				'type'            => 'column_settings_padding',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
				'specialty_only'  => 'yes',
			),
			'fullwidth' => array(
				'type' => 'skip',
			),
			'specialty' => array(
				'type' => 'skip',
			),
			'parallax_1' => array(
				'type' => 'skip',
			),
			'parallax_2' => array(
				'type' => 'skip',
			),
			'parallax_3' => array(
				'type' => 'skip',
			),
			'parallax_method_1' => array(
				'type' => 'skip',
			),
			'parallax_method_2' => array(
				'type' => 'skip',
			),
			'parallax_method_3' => array(
				'type' => 'skip',
			),
			'background_color_1' => array(
				'type' => 'skip',
			),
			'background_color_2' => array(
				'type' => 'skip',
			),
			'background_color_3' => array(
				'type' => 'skip',
			),
			'bg_img_1' => array(
				'type' => 'skip',
			),
			'bg_img_2' => array(
				'type' => 'skip',
			),
			'bg_img_3' => array(
				'type' => 'skip',
			),
			'background_size_1' => array(
				'type' => 'skip',
			),
			'background_size_2' => array(
				'type' => 'skip',
			),
			'background_size_3' => array(
				'type' => 'skip',
			),
			'background_position_1' => array(
				'type' => 'skip',
			),
			'background_position_2' => array(
				'type' => 'skip',
			),
			'background_position_3' => array(
				'type' => 'skip',
			),
			'background_repeat_1' => array(
				'type' => 'skip',
			),
			'background_repeat_2' => array(
				'type' => 'skip',
			),
			'background_repeat_3' => array(
				'type' => 'skip',
			),
			'background_blend_1' => array(
				'type' => 'skip',
			),
			'background_blend_2' => array(
				'type' => 'skip',
			),
			'background_blend_3' => array(
				'type' => 'skip',
			),
			'padding_top_1' => array(
				'type' => 'skip',
			),
			'padding_right_1' => array(
				'type' => 'skip',
			),
			'padding_bottom_1' => array(
				'type' => 'skip',
			),
			'padding_left_1' => array(
				'type' => 'skip',
			),
			'padding_top_2' => array(
				'type' => 'skip',
			),
			'padding_right_2' => array(
				'type' => 'skip',
			),
			'padding_bottom_2' => array(
				'type' => 'skip',
			),
			'padding_left_2' => array(
				'type' => 'skip',
			),
			'padding_top_3' => array(
				'type' => 'skip',
			),
			'padding_right_3' => array(
				'type' => 'skip',
			),
			'padding_bottom_3' => array(
				'type' => 'skip',
			),
			'padding_left_3' => array(
				'type' => 'skip',
			),
			'padding_1_tablet' => array(
				'type' => 'skip',
			),
			'padding_2_tablet' => array(
				'type' => 'skip',
			),
			'padding_3_tablet' => array(
				'type' => 'skip',
			),
			'padding_1_phone' => array(
				'type' => 'skip',
			),
			'padding_2_phone' => array(
				'type' => 'skip',
			),
			'padding_3_phone' => array(
				'type' => 'skip',
			),
			'padding_1_last_edited' => array(
				'type' => 'skip',
			),
			'padding_2_last_edited' => array(
				'type' => 'skip',
			),
			'padding_3_last_edited' => array(
				'type' => 'skip',
			),
			'module_id_1' => array(
				'type' => 'skip',
			),
			'module_id_2' => array(
				'type' => 'skip',
			),
			'module_id_3' => array(
				'type' => 'skip',
			),
			'module_class_1' => array(
				'type' => 'skip',
			),
			'module_class_2' => array(
				'type' => 'skip',
			),
			'module_class_3' => array(
				'type' => 'skip',
			),
			'custom_css_before_1' => array(
				'type' => 'skip',
			),
			'custom_css_before_2' => array(
				'type' => 'skip',
			),
			'custom_css_before_3' => array(
				'type' => 'skip',
			),
			'custom_css_main_1' => array(
				'type' => 'skip',
			),
			'custom_css_main_2' => array(
				'type' => 'skip',
			),
			'custom_css_main_3' => array(
				'type' => 'skip',
			),
			'custom_css_after_1' => array(
				'type' => 'skip',
			),
			'custom_css_after_2' => array(
				'type' => 'skip',
			),
			'custom_css_after_3' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_1' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_2' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_3' => array(
				'type' => 'skip',
			),
			'background_video_mp4_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_webm_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_width_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_height_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'allow_player_pause_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'__video_background_1' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_1',
					'background_video_webm_1',
					'background_video_width_1',
					'background_video_height_1',
				),
				'computed_minimum' => array(
					'background_video_mp4_1',
					'background_video_webm_1',
				),
			),
			'background_video_mp4_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_webm_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_width_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_height_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'allow_player_pause_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'__video_background_2' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_2',
					'background_video_webm_2',
					'background_video_width_2',
					'background_video_height_2',
				),
				'computed_minimum' => array(
					'background_video_mp4_2',
					'background_video_webm_2',
				),
			),
			'background_video_mp4_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_webm_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_width_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_height_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'allow_player_pause_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'__video_background_3' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_3',
					'background_video_webm_3',
					'background_video_width_3',
					'background_video_height_3',
				),
				'computed_minimum' => array(
					'background_video_mp4_3',
					'background_video_webm_3',
				),
			),
			'columns_css' => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'custom_css',
				'priority'        => '20',
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
				'description' => esc_html__( 'This will change the label of the section in the builder for easy identification when collapsed.', 'et_builder' ),
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
			'columns_css_fields' => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
			'custom_padding_last_edited' =>array(
				'type'           => 'skip',
				'tab_slug'       => 'advanced',
				'specialty_only' => 'yes',
			),
			'__video_background' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Section', 'get_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum' => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$background_image        = $this->shortcode_atts['background_image'];
		$background_color        = $this->shortcode_atts['background_color'];
		$background_video_mp4    = $this->shortcode_atts['background_video_mp4'];
		$background_video_webm   = $this->shortcode_atts['background_video_webm'];
		$inner_shadow            = $this->shortcode_atts['inner_shadow'];
		$parallax                = $this->shortcode_atts['parallax'];
		$parallax_method         = $this->shortcode_atts['parallax_method'];
		$fullwidth               = $this->shortcode_atts['fullwidth'];
		$specialty               = $this->shortcode_atts['specialty'];
		$transparent_background  = $this->shortcode_atts['transparent_background'];
		$custom_padding          = $this->shortcode_atts['custom_padding'];
		$custom_padding_tablet   = $this->shortcode_atts['custom_padding_tablet'];
		$custom_padding_phone    = $this->shortcode_atts['custom_padding_phone'];
		$custom_padding_last_edited = $this->shortcode_atts['custom_padding_last_edited'];
		$background_color_1      = $this->shortcode_atts['background_color_1'];
		$background_color_2      = $this->shortcode_atts['background_color_2'];
		$background_color_3      = $this->shortcode_atts['background_color_3'];
		$bg_img_1                = $this->shortcode_atts['bg_img_1'];
		$bg_img_2                = $this->shortcode_atts['bg_img_2'];
		$bg_img_3                = $this->shortcode_atts['bg_img_3'];
		$background_size_1       = $this->shortcode_atts['background_size_1'];
		$background_size_2       = $this->shortcode_atts['background_size_2'];
		$background_size_3       = $this->shortcode_atts['background_size_3'];
		$background_position_1   = $this->shortcode_atts['background_position_1'];
		$background_position_2   = $this->shortcode_atts['background_position_2'];
		$background_position_3   = $this->shortcode_atts['background_position_3'];
		$background_repeat_1     = $this->shortcode_atts['background_repeat_1'];
		$background_repeat_2     = $this->shortcode_atts['background_repeat_2'];
		$background_repeat_3     = $this->shortcode_atts['background_repeat_3'];
		$background_blend_1      = $this->shortcode_atts['background_blend_1'];
		$background_blend_2      = $this->shortcode_atts['background_blend_2'];
		$background_blend_3      = $this->shortcode_atts['background_blend_3'];
		$parallax_1              = $this->shortcode_atts['parallax_1'];
		$parallax_2              = $this->shortcode_atts['parallax_2'];
		$parallax_3              = $this->shortcode_atts['parallax_3'];
		$parallax_method_1       = $this->shortcode_atts['parallax_method_1'];
		$parallax_method_2       = $this->shortcode_atts['parallax_method_2'];
		$parallax_method_3       = $this->shortcode_atts['parallax_method_3'];
		$padding_top_1           = $this->shortcode_atts['padding_top_1'];
		$padding_right_1         = $this->shortcode_atts['padding_right_1'];
		$padding_bottom_1        = $this->shortcode_atts['padding_bottom_1'];
		$padding_left_1          = $this->shortcode_atts['padding_left_1'];
		$padding_top_2           = $this->shortcode_atts['padding_top_2'];
		$padding_right_2         = $this->shortcode_atts['padding_right_2'];
		$padding_bottom_2        = $this->shortcode_atts['padding_bottom_2'];
		$padding_left_2          = $this->shortcode_atts['padding_left_2'];
		$padding_top_3           = $this->shortcode_atts['padding_top_3'];
		$padding_right_3         = $this->shortcode_atts['padding_right_3'];
		$padding_bottom_3        = $this->shortcode_atts['padding_bottom_3'];
		$padding_left_3          = $this->shortcode_atts['padding_left_3'];
		$padding_1_tablet        = $this->shortcode_atts['padding_1_tablet'];
		$padding_2_tablet        = $this->shortcode_atts['padding_2_tablet'];
		$padding_3_tablet        = $this->shortcode_atts['padding_3_tablet'];
		$padding_1_phone         = $this->shortcode_atts['padding_1_phone'];
		$padding_2_phone         = $this->shortcode_atts['padding_2_phone'];
		$padding_3_phone         = $this->shortcode_atts['padding_3_phone'];
		$padding_1_last_edited   = $this->shortcode_atts['padding_1_last_edited'];
		$padding_2_last_edited   = $this->shortcode_atts['padding_2_last_edited'];
		$padding_3_last_edited   = $this->shortcode_atts['padding_3_last_edited'];
		$padding_mobile          = $this->shortcode_atts['padding_mobile'];
		$gutter_width            = $this->shortcode_atts['gutter_width'];
		$use_custom_width        = $this->shortcode_atts['use_custom_width'];
		$custom_width_px         = $this->shortcode_atts['custom_width_px'];
		$custom_width_percent    = $this->shortcode_atts['custom_width_percent'];
		$width_unit              = $this->shortcode_atts['width_unit'];
		$make_equal              = $this->shortcode_atts['make_equal'];
		$make_fullwidth          = $this->shortcode_atts['make_fullwidth'];
		$global_module           = $this->shortcode_atts['global_module'];
		$use_custom_gutter       = $this->shortcode_atts['use_custom_gutter'];
		$module_id_1             = $this->shortcode_atts['module_id_1'];
		$module_id_2             = $this->shortcode_atts['module_id_2'];
		$module_id_3             = $this->shortcode_atts['module_id_3'];
		$module_class_1          = $this->shortcode_atts['module_class_1'];
		$module_class_2          = $this->shortcode_atts['module_class_2'];
		$module_class_3          = $this->shortcode_atts['module_class_3'];
		$custom_css_before_1     = $this->shortcode_atts['custom_css_before_1'];
		$custom_css_before_2     = $this->shortcode_atts['custom_css_before_2'];
		$custom_css_before_3     = $this->shortcode_atts['custom_css_before_3'];
		$custom_css_main_1       = $this->shortcode_atts['custom_css_main_1'];
		$custom_css_main_2       = $this->shortcode_atts['custom_css_main_2'];
		$custom_css_main_3       = $this->shortcode_atts['custom_css_main_3'];
		$custom_css_after_1      = $this->shortcode_atts['custom_css_after_1'];
		$custom_css_after_2      = $this->shortcode_atts['custom_css_after_2'];
		$custom_css_after_3      = $this->shortcode_atts['custom_css_after_3'];
		$use_background_color_gradient_1              = $this->shortcode_atts['use_background_color_gradient_1'];
		$use_background_color_gradient_2              = $this->shortcode_atts['use_background_color_gradient_2'];
		$use_background_color_gradient_3              = $this->shortcode_atts['use_background_color_gradient_3'];
		$background_color_gradient_type_1             = $this->shortcode_atts['background_color_gradient_type_1'];
		$background_color_gradient_type_2             = $this->shortcode_atts['background_color_gradient_type_2'];
		$background_color_gradient_type_3             = $this->shortcode_atts['background_color_gradient_type_3'];
		$background_color_gradient_direction_1        = $this->shortcode_atts['background_color_gradient_direction_1'];
		$background_color_gradient_direction_2        = $this->shortcode_atts['background_color_gradient_direction_2'];
		$background_color_gradient_direction_3        = $this->shortcode_atts['background_color_gradient_direction_3'];
		$background_color_gradient_direction_radial_1 = $this->shortcode_atts['background_color_gradient_direction_radial_1'];
		$background_color_gradient_direction_radial_2 = $this->shortcode_atts['background_color_gradient_direction_radial_2'];
		$background_color_gradient_direction_radial_3 = $this->shortcode_atts['background_color_gradient_direction_radial_3'];
		$background_color_gradient_start_1            = $this->shortcode_atts['background_color_gradient_start_1'];
		$background_color_gradient_start_2            = $this->shortcode_atts['background_color_gradient_start_2'];
		$background_color_gradient_start_3            = $this->shortcode_atts['background_color_gradient_start_3'];
		$background_color_gradient_end_1              = $this->shortcode_atts['background_color_gradient_end_1'];
		$background_color_gradient_end_2              = $this->shortcode_atts['background_color_gradient_end_2'];
		$background_color_gradient_end_3              = $this->shortcode_atts['background_color_gradient_end_3'];
		$background_color_gradient_start_position_1   = $this->shortcode_atts['background_color_gradient_start_position_1'];
		$background_color_gradient_start_position_2   = $this->shortcode_atts['background_color_gradient_start_position_2'];
		$background_color_gradient_start_position_3   = $this->shortcode_atts['background_color_gradient_start_position_3'];
		$background_color_gradient_end_position_1     = $this->shortcode_atts['background_color_gradient_end_position_1'];
		$background_color_gradient_end_position_2     = $this->shortcode_atts['background_color_gradient_end_position_2'];
		$background_color_gradient_end_position_3     = $this->shortcode_atts['background_color_gradient_end_position_3'];
		$background_video_mp4_1     = $this->shortcode_atts['background_video_mp4_1'];
		$background_video_mp4_2     = $this->shortcode_atts['background_video_mp4_2'];
		$background_video_mp4_3     = $this->shortcode_atts['background_video_mp4_3'];
		$background_video_webm_1    = $this->shortcode_atts['background_video_webm_1'];
		$background_video_webm_2    = $this->shortcode_atts['background_video_webm_2'];
		$background_video_webm_3    = $this->shortcode_atts['background_video_webm_3'];
		$background_video_width_1   = $this->shortcode_atts['background_video_width_1'];
		$background_video_width_2   = $this->shortcode_atts['background_video_width_2'];
		$background_video_width_3   = $this->shortcode_atts['background_video_width_3'];
		$background_video_height_1  = $this->shortcode_atts['background_video_height_1'];
		$background_video_height_2  = $this->shortcode_atts['background_video_height_2'];
		$background_video_height_3  = $this->shortcode_atts['background_video_height_3'];
		$allow_player_pause_1       = $this->shortcode_atts['allow_player_pause_1'];
		$allow_player_pause_2       = $this->shortcode_atts['allow_player_pause_2'];
		$allow_player_pause_3       = $this->shortcode_atts['allow_player_pause_3'];

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
		$gutter_class = '';

		$custom_padding_responsive_active = et_pb_get_responsive_status( $custom_padding_last_edited );

		$padding_mobile_values = $custom_padding_responsive_active ? array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		) : array(
			'tablet' => false,
			'phone' => false,
		);

		if ( 'on' === $specialty ) {
			global $et_pb_all_column_settings, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

			$et_pb_all_column_settings_backup = $et_pb_all_column_settings;

			$et_pb_all_column_settings = ! isset( $et_pb_all_column_settings ) ?  array() : $et_pb_all_column_settings;

			$module_class .= 'on' === $make_equal ? ' et_pb_equal_columns' : '';

			if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
				$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter to 1 if 0 entered by user
				$gutter_class .= ' et_pb_gutters' . $gutter_width;
			}

			$et_pb_columns_counter = 0;
			$et_pb_column_backgrounds = array(
				array(
					'color'          => $background_color_1,
					'image'          => $bg_img_1,
					'image_size'     => $background_size_1,
					'image_position' => $background_position_1,
					'image_repeat'   => $background_repeat_1,
					'image_blend'    => $background_blend_1,
				),
				array(
					'color'          => $background_color_2,
					'image'          => $bg_img_2,
					'image_size'     => $background_size_2,
					'image_position' => $background_position_2,
					'image_repeat'   => $background_repeat_2,
					'image_blend'    => $background_blend_2,
				),
				array(
					'color'          => $background_color_3,
					'image'          => $bg_img_3,
					'image_size'     => $background_size_3,
					'image_position' => $background_position_3,
					'image_repeat'   => $background_repeat_3,
					'image_blend'    => $background_blend_3,
				),
			);

			$et_pb_column_backgrounds_gradient = array(
				array(
					'active'           => $use_background_color_gradient_1,
					'type'             => $background_color_gradient_type_1,
					'direction'        => $background_color_gradient_direction_1,
					'radial_direction' => $background_color_gradient_direction_radial_1,
					'color_start'      => $background_color_gradient_start_1,
					'color_end'        => $background_color_gradient_end_1,
					'start_position'   => $background_color_gradient_start_position_1,
					'end_position'     => $background_color_gradient_end_position_1,
				),
				array(
					'active'           => $use_background_color_gradient_2,
					'type'             => $background_color_gradient_type_2,
					'direction'        => $background_color_gradient_direction_2,
					'radial_direction' => $background_color_gradient_direction_radial_2,
					'color_start'      => $background_color_gradient_start_2,
					'color_end'        => $background_color_gradient_end_2,
					'start_position'   => $background_color_gradient_start_position_2,
					'end_position'     => $background_color_gradient_end_position_2,
				),
				array(
					'active'           => $use_background_color_gradient_3,
					'type'             => $background_color_gradient_type_3,
					'direction'        => $background_color_gradient_direction_3,
					'radial_direction' => $background_color_gradient_direction_radial_3,
					'color_start'      => $background_color_gradient_start_3,
					'color_end'        => $background_color_gradient_end_3,
					'start_position'   => $background_color_gradient_start_position_3,
					'end_position'     => $background_color_gradient_end_position_3,
				),
			);

			$et_pb_column_backgrounds_video = array(
				array(
					'background_video_mp4'         => $background_video_mp4_1,
					'background_video_webm'        => $background_video_webm_1,
					'background_video_width'       => $background_video_width_1,
					'background_video_height'      => $background_video_height_1,
					'background_video_allow_pause' => $allow_player_pause_1,
				),
				array(
					'background_video_mp4'         => $background_video_mp4_2,
					'background_video_webm'        => $background_video_webm_2,
					'background_video_width'       => $background_video_width_2,
					'background_video_height'      => $background_video_height_2,
					'background_video_allow_pause' => $allow_player_pause_2,
				),
				array(
					'background_video_mp4'         => $background_video_mp4_3,
					'background_video_webm'        => $background_video_webm_3,
					'background_video_width'       => $background_video_width_3,
					'background_video_height'      => $background_video_height_3,
					'background_video_allow_pause' => $allow_player_pause_3,
				),
			);

			$et_pb_column_paddings = array(
				array(
					'padding-top'    => $padding_top_1,
					'padding-right'  => $padding_right_1,
					'padding-bottom' => $padding_bottom_1,
					'padding-left'   => $padding_left_1
				),
				array(
					'padding-top'    => $padding_top_2,
					'padding-right'  => $padding_right_2,
					'padding-bottom' => $padding_bottom_2,
					'padding-left'   => $padding_left_2
				),
				array(
					'padding-top'    => $padding_top_3,
					'padding-right'  => $padding_right_3,
					'padding-bottom' => $padding_bottom_3,
					'padding-left'   => $padding_left_3
				),
			);

			$et_pb_column_paddings_mobile = array(
				array(
					'tablet' => explode( '|', $padding_1_tablet ),
					'phone'  => explode( '|', $padding_1_phone ),
					'last_edited' => $padding_1_last_edited,
				),
				array(
					'tablet' => explode( '|', $padding_2_tablet ),
					'phone'  => explode( '|', $padding_2_phone ),
					'last_edited' => $padding_2_last_edited,
				),
				array(
					'tablet' => explode( '|', $padding_3_tablet ),
					'phone'  => explode( '|', $padding_3_phone ),
					'last_edited' => $padding_3_last_edited,
				),
			);

			$et_pb_column_parallax = array(
				array( $parallax_1, $parallax_method_1 ),
				array( $parallax_2, $parallax_method_2 ),
				array( $parallax_3, $parallax_method_3 ),
			);

			if ( 'on' === $make_fullwidth && 'off' === $use_custom_width ) {
				$module_class .= ' et_pb_specialty_fullwidth';
			}

			if ( 'on' === $use_custom_width ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% > .et_pb_row',
					'declaration' => sprintf(
						'max-width:%1$s !important;',
						'on' === $width_unit ? esc_attr( sprintf( '%1$spx', intval( $custom_width_px ) ) ) : esc_attr( sprintf( '%1$s%%', intval( $custom_width_percent ) ) )
					),
				) );
			}

			$et_pb_column_css = array(
				'css_class'         => array( $module_class_1, $module_class_2, $module_class_3 ),
				'css_id'            => array( $module_id_1, $module_id_2, $module_id_3 ),
				'custom_css_before' => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3 ),
				'custom_css_main'   => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3 ),
				'custom_css_after'  => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3 ),
			);

			$internal_columns_settings_array = array(
				'keep_column_padding_mobile' => 'on',
				'et_pb_column_backgrounds' => $et_pb_column_backgrounds,
				'et_pb_column_backgrounds_gradient' => $et_pb_column_backgrounds_gradient,
				'et_pb_column_backgrounds_video' => $et_pb_column_backgrounds_video,
				'et_pb_column_parallax' => $et_pb_column_parallax,
				'et_pb_columns_counter' => $et_pb_columns_counter,
				'et_pb_column_paddings' => $et_pb_column_paddings,
				'et_pb_column_paddings_mobile' => $et_pb_column_paddings_mobile,
				'et_pb_column_css' => $et_pb_column_css,
			);

			$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

			$et_pb_all_column_settings[ $current_row_position ] = $internal_columns_settings_array;

			if ( $et_pb_rendering_column_content ) {
				$et_pb_rendering_column_content_row = true;
			}
		}

		$background_video = '';

		if ( '' !== $background_video_mp4 || '' !== $background_video_webm ) {
			$background_video = $this->video_background();
		}

		// set the correct default value for $transparent_background option if plugin activated.
		if ( et_is_builder_plugin_active() && 'default' === $transparent_background ) {
			$transparent_background = '' !== $background_color ? 'off' : 'on';
		} elseif ( 'default' === $transparent_background ) {
			$transparent_background = 'off';
		}

		if ( '' !== $background_color && 'off' === $transparent_background ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_section',
				'declaration' => sprintf(
					'background-color:%s !important;',
					esc_attr( $background_color )
				),
			) );
		}

		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of sections supports only top and bottom padding, so we need to handle it along with the full padding in the recent version
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right' => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left' => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '%%order_class%%.et_pb_section',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default)
					if ( in_array( $padding_mobile, array( 'on', 'off' ) ) && 'on' !== $padding_mobile && ! $custom_padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				et_pb_generate_responsive_css( $padding_mobile_values_processed, '%%order_class%%.et_pb_section', '', $function_name );
			}
		}

		if ( '' !== $background_video_mp4 || '' !== $background_video_webm || ( '' !== $background_color && 'off' === $transparent_background ) || '' !== $background_image ) {
			$module_class .= ' et_pb_with_background';
		}

		$output = sprintf(
			'<div%7$s class="et_pb_section%3$s%4$s%5$s%6$s%8$s%12$s%13$s"%14$s>
				%11$s
				%9$s
					%2$s
					%1$s
				%10$s
			</div> <!-- .et_pb_section -->',
			do_shortcode( et_pb_fix_shortcodes( $content ) ),
			$background_video,
			( '' !== $background_video ? ' et_pb_section_video et_pb_preload' : '' ),
			( ( 'off' !== $inner_shadow && ! ( '' !== $background_image && 'on' === $parallax && 'off' === $parallax_method ) ) ? ' et_pb_inner_shadow' : '' ),
			( 'on' === $parallax ? ' et_pb_section_parallax' : '' ),
			( 'off' !== $fullwidth ? ' et_pb_fullwidth_section' : '' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'on' === $specialty ?
				sprintf( '<div class="et_pb_row%1$s">', $gutter_class )
				: '' ),
			( 'on' === $specialty ? '</div> <!-- .et_pb_row -->' : '' ),
			( '' !== $background_image && 'on' === $parallax
				? sprintf(
					'<div class="et_parallax_bg%2$s%3$s" style="background-image: url(%1$s);"></div>',
					esc_attr( $background_image ),
					( 'off' === $parallax_method ? ' et_pb_parallax_css' : '' ),
					( ( 'off' !== $inner_shadow && 'off' === $parallax_method ) ? ' et_pb_inner_shadow' : '' )
				)
				: ''
			),
			( 'on' === $specialty ? ' et_section_specialty' : ' et_section_regular' ),
			( 'on' === $transparent_background ? ' et_section_transparent' : '' ),
			$this->get_module_data_attributes()
		);

		if ( 'on' === $specialty ) {
			// reset the global column settings to make sure they are not affected by internal content
			$et_pb_all_column_settings = $et_pb_all_column_settings_backup;

			if ( $et_pb_rendering_column_content_row ) {
				$et_pb_rendering_column_content_row = false;
			}
		}

		return $output;

	}

}
new ET_Builder_Section;

class ET_Builder_Row extends ET_Builder_Structure_Element {
	function init() {
		$this->name = esc_html__( 'Row', 'et_builder' );
		$this->slug = 'et_pb_row';
		$this->fb_support = true;

		$this->advanced_options = array(
			'background' => array(
				'use_background_color' => true,
				'use_background_image' => true,
				'use_background_color_gradient' => true,
				'use_background_video' => true,
			),
			'custom_margin_padding' => array(
				'use_padding'       => false,
				'custom_margin'     => array(
					'priority' => 1,
				),
				'css' => array(
					'main' => '%%order_class%%.et_pb_row',
					'important' => 'all',
				),
				'toggle_slug' => 'margin_padding',
			),
		);

		$this->options_toggles = array(
			'general' => array(
				'toggles' => array(
					'background'     => array(
						'title'       => esc_html__( 'Background', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
							'column_4' => esc_html__( 'Column 4', 'et_builder' ),
						),
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'width'          => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
					'margin_padding' => array(
						'title'       => esc_html__( 'Spacing', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
							'column_4' => esc_html__( 'Column 4', 'et_builder' ),
						),
						'priority' => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'classes' => array(
						'title'  => esc_html__( 'CSS ID & Classes', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
							'column_4' => esc_html__( 'Column 4', 'et_builder' ),
						),
					),
					'custom_css' => array(
						'title'  => esc_html__( 'Custom CSS', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
							'column_4' => esc_html__( 'Column 4', 'et_builder' ),
						),
					),
				),
			),
		);

		$this->whitelisted_fields = array(
			'make_fullwidth',
			'use_custom_width',
			'width_unit',
			'custom_width_px',
			'custom_width_percent',
			'use_custom_gutter',
			'gutter_width',
			'custom_padding',
			'custom_padding_tablet',
			'custom_padding_phone',
			'custom_padding_last_edited',
			'padding_mobile',
			'column_padding_mobile',
			'module_id',
			'module_class',
			'make_equal',
			'columns',
			'background_color_1',
			'background_color_2',
			'background_color_3',
			'background_color_4',
			'bg_img_1',
			'bg_img_2',
			'bg_img_3',
			'bg_img_4',
			'padding_top_1',
			'padding_right_1',
			'padding_bottom_1',
			'padding_left_1',
			'padding_top_2',
			'padding_right_2',
			'padding_bottom_2',
			'padding_left_2',
			'padding_top_3',
			'padding_right_3',
			'padding_bottom_3',
			'padding_left_3',
			'padding_top_4',
			'padding_right_4',
			'padding_bottom_4',
			'padding_left_4',
			'padding_1_tablet',
			'padding_2_tablet',
			'padding_3_tablet',
			'padding_4_tablet',
			'padding_1_phone',
			'padding_2_phone',
			'padding_3_phone',
			'padding_4_phone',
			'padding_1_last_edited',
			'padding_2_last_edited',
			'padding_3_last_edited',
			'padding_4_last_edited',
			'admin_label',
			'parallax_1',
			'parallax_method_1',
			'parallax_2',
			'parallax_method_2',
			'parallax_3',
			'parallax_method_3',
			'parallax_4',
			'parallax_method_4',
			'module_id_1',
			'module_id_2',
			'module_id_3',
			'module_id_4',
			'module_class_1',
			'module_class_2',
			'module_class_3',
			'module_class_4',
			'custom_css_before_1',
			'custom_css_before_2',
			'custom_css_before_3',
			'custom_css_before_4',
			'custom_css_main_1',
			'custom_css_main_2',
			'custom_css_main_3',
			'custom_css_main_4',
			'custom_css_after_1',
			'custom_css_after_2',
			'custom_css_after_3',
			'custom_css_after_4',
			'background_size_1',
			'background_position_1',
			'background_repeat_1',
			'background_blend_1',
			'use_background_color_gradient_1',
			'background_color_gradient_start_1',
			'background_color_gradient_end_1',
			'background_color_gradient_type_1',
			'background_color_gradient_direction_1',
			'background_color_gradient_direction_radial_1',
			'background_color_gradient_start_position_1',
			'background_color_gradient_end_position_1',
			'background_size_2',
			'background_position_2',
			'background_repeat_2',
			'background_blend_2',
			'use_background_color_gradient_2',
			'background_color_gradient_start_2',
			'background_color_gradient_end_2',
			'background_color_gradient_type_2',
			'background_color_gradient_direction_2',
			'background_color_gradient_direction_radial_2',
			'background_color_gradient_start_position_2',
			'background_color_gradient_end_position_2',
			'background_size_3',
			'background_position_3',
			'background_repeat_3',
			'background_blend_3',
			'use_background_color_gradient_3',
			'background_color_gradient_start_3',
			'background_color_gradient_end_3',
			'background_color_gradient_type_3',
			'background_color_gradient_direction_3',
			'background_color_gradient_direction_radial_3',
			'background_color_gradient_start_position_3',
			'background_color_gradient_end_position_3',
			'background_size_4',
			'background_position_4',
			'background_repeat_4',
			'background_blend_4',
			'use_background_color_gradient_4',
			'background_color_gradient_start_4',
			'background_color_gradient_end_4',
			'background_color_gradient_type_4',
			'background_color_gradient_direction_4',
			'background_color_gradient_direction_radial_4',
			'background_color_gradient_start_position_4',
			'background_color_gradient_end_position_4',
			'background_video_mp4_1',
			'background_video_webm_1',
			'background_video_width_1',
			'background_video_height_1',
			'allow_player_pause_1',
			'__video_background_1',
			'background_video_mp4_2',
			'background_video_webm_2',
			'background_video_width_2',
			'background_video_height_2',
			'allow_player_pause_2',
			'__video_background_2',
			'background_video_mp4_3',
			'background_video_webm_3',
			'background_video_width_3',
			'background_video_height_3',
			'allow_player_pause_3',
			'__video_background_3',
			'background_video_mp4_4',
			'background_video_webm_4',
			'background_video_width_4',
			'background_video_height_4',
			'allow_player_pause_4',
			'__video_background_4',
		);

		$this->fields_defaults = array(
			'make_fullwidth'        => array( 'off' ),
			'use_custom_width'      => array( 'off' ),
			'width_unit'            => array( 'on' ),
			'custom_width_px'       => array( '1080px', 'only_default_setting' ),
			'custom_width_percent'  => array( '80%', 'only_default_setting' ),
			'use_custom_gutter'     => array( 'off' ),
			'gutter_width'          => array( et_get_option( 'gutter_width', 3 ) ),
			'padding_mobile'        => array( '' ),
			'column_padding_mobile' => array( '' ),
			'background_color'      => array( '', 'only_default_setting' ),
			'allow_player_pause'    => array( 'off' ),
			'parallax'              => array( 'off' ),
			'parallax_method'       => array( 'on' ),
			'make_equal'            => array( 'off' ),
			'parallax_1'            => array( 'off' ),
			'parallax_method_1'     => array( 'on' ),
			'parallax_2'            => array( 'off' ),
			'parallax_method_2'     => array( 'on' ),
			'parallax_3'            => array( 'off' ),
			'parallax_method_3'     => array( 'on' ),
			'parallax_4'            => array( 'off' ),
			'parallax_method_4'     => array( 'on' ),
			'custom_padding_tablet' => array( '' ),
			'custom_padding_phone'  => array( '' ),
		);
	}

	function get_fields() {
		$fields = array(
			'make_fullwidth' => array(
				'label'             => esc_html__( 'Make This Row Fullwidth', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Enable this option to extend the width of this row to the edge of the browser window.', 'et_builder' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'use_custom_width' => array(
				'label'             => esc_html__( 'Use Custom Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'make_fullwidth',
					'custom_width',
					'width_unit',
				),
				'description'       => esc_html__( 'Change to Yes if you would like to adjust the width of this row to a non-standard width.', 'et_builder' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'width_unit' => array(
				'label'             => esc_html__( 'Unit', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'px', 'et_builder' ),
					'off' => '%',
				),
				'default'           => 'on',
				'button_options'    => array(
					'button_type' => 'equal',
				),
				'depends_show_if'   => 'on',
				'affects'           => array(
					'custom_width_px',
					'custom_width_percent',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'custom_width_px' => array(
				'label'               => esc_html__( 'Custom Width', 'et_builder' ),
				'type'                => 'range',
				'option_category'     => 'layout',
				'depends_show_if_not' => 'off',
				'validate_unit'       => true,
				'fixed_unit'          => 'px',
				'range_settings'      => array(
					'min'  => 500,
					'max'  => 2600,
					'step' => 1,
				),
				'description'         => esc_html__( 'Define custom width for this Row', 'et_builder' ),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'width',
			),
			'custom_width_percent' => array(
				'label'           => esc_html__( 'Custom Width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'depends_show_if' => 'off',
				'validate_unit'   => true,
				'fixed_unit'      => '%',
				'range_settings'  => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'description'     => esc_html__( 'Define custom width for this Row', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
			),
			'use_custom_gutter' => array(
				'label'             => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'gutter_width',
				),
				'description'       => esc_html__( 'Enable this option to define custom gutter width for this row.', 'et_builder' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'gutter_width' => array(
				'label'           => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'range_settings'  => array(
					'min'  => 1,
					'max'  => 4,
					'step' => 1,
				),
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'Adjust the spacing between each column in this row.', 'et_builder' ),
				'validate_unit'   => false,
				'fixed_range'     => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
			),
			'custom_padding' => array(
				'label'           => esc_html__( 'Custom Padding', 'et_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
			),
			'custom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'custom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'padding_mobile' => array(
				'label' => esc_html__( 'Keep Custom Padding on Mobile', 'et_builder' ),
				'type'        => 'skip', // Remaining attribute for backward compatibility
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'custom_margin' => array(
				'label'           => esc_html__( 'Custom Margin', 'et_builder' ),
				'type'            => 'custom_margin',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
			),
			'make_equal' => array(
				'label'             => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'background_color' => array(
				'label'        => esc_html__( 'Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'columns_background' => array(
				'type'            => 'column_settings_background',
				'option_category' => 'configuration',
				'toggle_slug'     => 'background',
			),
			'columns_padding' => array(
				'type'            => 'column_settings_padding',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
			),
			'column_padding_mobile' => array(
				'label' => esc_html__( 'Keep Column Padding on Mobile', 'et_builder' ),
				'type'  => 'skip', // Remaining attribute for backward compatibility
			),
			'background_color_1' => array(
				'type' => 'skip',
			),
			'background_color_2' => array(
				'type' => 'skip',
			),
			'background_color_3' => array(
				'type' => 'skip',
			),
			'background_color_4' => array(
				'type' => 'skip',
			),
			'bg_img_1' => array(
				'type' => 'skip',
			),
			'bg_img_2' => array(
				'type' => 'skip',
			),
			'bg_img_3' => array(
				'type' => 'skip',
			),
			'bg_img_4' => array(
				'type' => 'skip',
			),
			'padding_top_1' => array(
				'type' => 'skip',
			),
			'padding_right_1' => array(
				'type' => 'skip',
			),
			'padding_bottom_1' => array(
				'type' => 'skip',
			),
			'padding_left_1' => array(
				'type' => 'skip',
			),
			'padding_top_2' => array(
				'type' => 'skip',
			),
			'padding_right_2' => array(
				'type' => 'skip',
			),
			'padding_bottom_2' => array(
				'type' => 'skip',
			),
			'padding_left_2' => array(
				'type' => 'skip',
			),
			'padding_top_3' => array(
				'type' => 'skip',
			),
			'padding_right_3' => array(
				'type' => 'skip',
			),
			'padding_bottom_3' => array(
				'type' => 'skip',
			),
			'padding_left_3' => array(
				'type' => 'skip',
			),
			'padding_top_4' => array(
				'type' => 'skip',
			),
			'padding_right_4' => array(
				'type' => 'skip',
			),
			'padding_bottom_4' => array(
				'type' => 'skip',
			),
			'padding_left_4' => array(
				'type' => 'skip',
			),
			'parallax_1' => array(
				'type' => 'skip',
			),
			'parallax_method_1' => array(
				'type' => 'skip',
			),
			'parallax_2' => array(
				'type' => 'skip',
			),
			'parallax_method_2' => array(
				'type' => 'skip',
			),
			'parallax_3' => array(
				'type' => 'skip',
			),
			'parallax_method_3' => array(
				'type' => 'skip',
			),
			'parallax_4' => array(
				'type' => 'skip',
			),
			'parallax_method_4' => array(
				'type' => 'skip',
			),
			'background_size_1' => array(
				'type' => 'skip',
			),
			'background_size_2' => array(
				'type' => 'skip',
			),
			'background_size_3' => array(
				'type' => 'skip',
			),
			'background_size_4' => array(
				'type' => 'skip',
			),
			'background_position_1' => array(
				'type' => 'skip',
			),
			'background_position_2' => array(
				'type' => 'skip',
			),
			'background_position_3' => array(
				'type' => 'skip',
			),
			'background_position_4' => array(
				'type' => 'skip',
			),
			'background_repeat_1' => array(
				'type' => 'skip',
			),
			'background_repeat_2' => array(
				'type' => 'skip',
			),
			'background_repeat_3' => array(
				'type' => 'skip',
			),
			'background_repeat_4' => array(
				'type' => 'skip',
			),
			'background_blend_1' => array(
				'type' => 'skip',
			),
			'background_blend_2' => array(
				'type' => 'skip',
			),
			'background_blend_3' => array(
				'type' => 'skip',
			),
			'background_blend_4' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_1' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_2' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_3' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_4' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_4' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_4' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_4' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_4' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_4' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_4' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_4' => array(
				'type' => 'skip',
			),
			'background_video_mp4_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_webm_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_width_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_height_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'allow_player_pause_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'__video_background_1' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_1',
					'background_video_webm_1',
					'background_video_width_1',
					'background_video_height_1',
				),
				'computed_minimum' => array(
					'background_video_mp4_1',
					'background_video_webm_1',
				),
			),
			'background_video_mp4_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_webm_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_width_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_height_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'allow_player_pause_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'__video_background_2' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_2',
					'background_video_webm_2',
					'background_video_width_2',
					'background_video_height_2',
				),
				'computed_minimum' => array(
					'background_video_mp4_2',
					'background_video_webm_2',
				),
			),
			'background_video_mp4_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_webm_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_width_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_height_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'allow_player_pause_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'__video_background_3' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_3',
					'background_video_webm_3',
					'background_video_width_3',
					'background_video_height_3',
				),
				'computed_minimum' => array(
					'background_video_mp4_3',
					'background_video_webm_3',
				),
			),
			'background_video_mp4_4' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_4',
				),
			),
			'background_video_webm_4' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_4',
				),
			),
			'background_video_width_4' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_4',
				),
			),
			'background_video_height_4' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_4',
				),
			),
			'allow_player_pause_4' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_4',
				),
			),
			'__video_background_4' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_4',
					'background_video_webm_4',
					'background_video_width_4',
					'background_video_height_4',
				),
				'computed_minimum' => array(
					'background_video_mp4_4',
					'background_video_webm_4',
				),
			),
			'padding_1_tablet' => array(
				'type' => 'skip',
			),
			'padding_2_tablet' => array(
				'type' => 'skip',
			),
			'padding_3_tablet' => array(
				'type' => 'skip',
			),
			'padding_4_tablet' => array(
				'type' => 'skip',
			),
			'padding_1_phone' => array(
				'type' => 'skip',
			),
			'padding_2_phone' => array(
				'type' => 'skip',
			),
			'padding_3_phone' => array(
				'type' => 'skip',
			),
			'padding_4_phone' => array(
				'type' => 'skip',
			),
			'padding_1_last_edited' => array(
				'type' => 'skip',
			),
			'padding_2_last_edited' => array(
				'type' => 'skip',
			),
			'padding_3_last_edited' => array(
				'type' => 'skip',
			),
			'padding_4_last_edited' => array(
				'type' => 'skip',
			),
			'module_id_1' => array(
				'type' => 'skip',
			),
			'module_id_2' => array(
				'type' => 'skip',
			),
			'module_id_3' => array(
				'type' => 'skip',
			),
			'module_id_4' => array(
				'type' => 'skip',
			),
			'module_class_1' => array(
				'type' => 'skip',
			),
			'module_class_2' => array(
				'type' => 'skip',
			),
			'module_class_3' => array(
				'type' => 'skip',
			),
			'module_class_4' => array(
				'type' => 'skip',
			),
			'custom_css_before_1' => array(
				'type' => 'skip',
			),
			'custom_css_before_2' => array(
				'type' => 'skip',
			),
			'custom_css_before_3' => array(
				'type' => 'skip',
			),
			'custom_css_before_4' => array(
				'type' => 'skip',
			),
			'custom_css_main_1' => array(
				'type' => 'skip',
			),
			'custom_css_main_2' => array(
				'type' => 'skip',
			),
			'custom_css_main_3' => array(
				'type' => 'skip',
			),
			'custom_css_main_4' => array(
				'type' => 'skip',
			),
			'custom_css_after_1' => array(
				'type' => 'skip',
			),
			'custom_css_after_2' => array(
				'type' => 'skip',
			),
			'custom_css_after_3' => array(
				'type' => 'skip',
			),
			'custom_css_after_4' => array(
				'type' => 'skip',
			),
			'columns_css' => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'custom_css',
				'priority'        => '20',
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
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the row in the builder for easy identification when collapsed.', 'et_builder' ),
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
			'columns_css_fields' => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
			'custom_padding_last_edited' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'__video_background' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Row', 'get_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum' => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$custom_padding          = $this->shortcode_atts['custom_padding'];
		$custom_padding_tablet   = $this->shortcode_atts['custom_padding_tablet'];
		$custom_padding_phone    = $this->shortcode_atts['custom_padding_phone'];
		$custom_padding_last_edited = $this->shortcode_atts['custom_padding_last_edited'];
		$column_padding_mobile   = $this->shortcode_atts['column_padding_mobile'];
		$make_fullwidth          = $this->shortcode_atts['make_fullwidth'];
		$make_equal              = $this->shortcode_atts['make_equal'];
		$background_color_1      = $this->shortcode_atts['background_color_1'];
		$background_color_2      = $this->shortcode_atts['background_color_2'];
		$background_color_3      = $this->shortcode_atts['background_color_3'];
		$background_color_4      = $this->shortcode_atts['background_color_4'];
		$bg_img_1                = $this->shortcode_atts['bg_img_1'];
		$bg_img_2                = $this->shortcode_atts['bg_img_2'];
		$bg_img_3                = $this->shortcode_atts['bg_img_3'];
		$bg_img_4                = $this->shortcode_atts['bg_img_4'];
		$background_size_1       = $this->shortcode_atts['background_size_1'];
		$background_size_2       = $this->shortcode_atts['background_size_2'];
		$background_size_3       = $this->shortcode_atts['background_size_3'];
		$background_size_4       = $this->shortcode_atts['background_size_4'];
		$background_position_1   = $this->shortcode_atts['background_position_1'];
		$background_position_2   = $this->shortcode_atts['background_position_2'];
		$background_position_3   = $this->shortcode_atts['background_position_3'];
		$background_position_4   = $this->shortcode_atts['background_position_4'];
		$background_repeat_1     = $this->shortcode_atts['background_repeat_1'];
		$background_repeat_2     = $this->shortcode_atts['background_repeat_2'];
		$background_repeat_3     = $this->shortcode_atts['background_repeat_3'];
		$background_repeat_4     = $this->shortcode_atts['background_repeat_4'];
		$background_blend_1      = $this->shortcode_atts['background_blend_1'];
		$background_blend_2      = $this->shortcode_atts['background_blend_2'];
		$background_blend_3      = $this->shortcode_atts['background_blend_3'];
		$background_blend_4      = $this->shortcode_atts['background_blend_4'];
		$padding_top_1           = $this->shortcode_atts['padding_top_1'];
		$padding_right_1         = $this->shortcode_atts['padding_right_1'];
		$padding_bottom_1        = $this->shortcode_atts['padding_bottom_1'];
		$padding_left_1          = $this->shortcode_atts['padding_left_1'];
		$padding_top_2           = $this->shortcode_atts['padding_top_2'];
		$padding_right_2         = $this->shortcode_atts['padding_right_2'];
		$padding_bottom_2        = $this->shortcode_atts['padding_bottom_2'];
		$padding_left_2          = $this->shortcode_atts['padding_left_2'];
		$padding_top_3           = $this->shortcode_atts['padding_top_3'];
		$padding_right_3         = $this->shortcode_atts['padding_right_3'];
		$padding_bottom_3        = $this->shortcode_atts['padding_bottom_3'];
		$padding_left_3          = $this->shortcode_atts['padding_left_3'];
		$padding_top_4           = $this->shortcode_atts['padding_top_4'];
		$padding_right_4         = $this->shortcode_atts['padding_right_4'];
		$padding_bottom_4        = $this->shortcode_atts['padding_bottom_4'];
		$padding_left_4          = $this->shortcode_atts['padding_left_4'];
		$padding_1_tablet        = $this->shortcode_atts['padding_1_tablet'];
		$padding_2_tablet        = $this->shortcode_atts['padding_2_tablet'];
		$padding_3_tablet        = $this->shortcode_atts['padding_3_tablet'];
		$padding_4_tablet        = $this->shortcode_atts['padding_4_tablet'];
		$padding_1_phone         = $this->shortcode_atts['padding_1_phone'];
		$padding_2_phone         = $this->shortcode_atts['padding_2_phone'];
		$padding_3_phone         = $this->shortcode_atts['padding_3_phone'];
		$padding_4_phone         = $this->shortcode_atts['padding_4_phone'];
		$padding_1_last_edited   = $this->shortcode_atts['padding_1_last_edited'];
		$padding_2_last_edited   = $this->shortcode_atts['padding_2_last_edited'];
		$padding_3_last_edited   = $this->shortcode_atts['padding_3_last_edited'];
		$padding_4_last_edited   = $this->shortcode_atts['padding_4_last_edited'];
		$padding_mobile          = $this->shortcode_atts['padding_mobile'];
		$gutter_width            = $this->shortcode_atts['gutter_width'];
		$use_custom_width        = $this->shortcode_atts['use_custom_width'];
		$custom_width_px         = $this->shortcode_atts['custom_width_px'];
		$custom_width_percent    = $this->shortcode_atts['custom_width_percent'];
		$width_unit              = $this->shortcode_atts['width_unit'];
		$global_module           = $this->shortcode_atts['global_module'];
		$use_custom_gutter       = $this->shortcode_atts['use_custom_gutter'];
		$parallax_1              = $this->shortcode_atts['parallax_1'];
		$parallax_method_1       = $this->shortcode_atts['parallax_method_1'];
		$parallax_2              = $this->shortcode_atts['parallax_2'];
		$parallax_method_2       = $this->shortcode_atts['parallax_method_2'];
		$parallax_3              = $this->shortcode_atts['parallax_3'];
		$parallax_method_3       = $this->shortcode_atts['parallax_method_3'];
		$parallax_4              = $this->shortcode_atts['parallax_4'];
		$parallax_method_4       = $this->shortcode_atts['parallax_method_4'];
		$module_id_1             = $this->shortcode_atts['module_id_1'];
		$module_id_2             = $this->shortcode_atts['module_id_2'];
		$module_id_3             = $this->shortcode_atts['module_id_3'];
		$module_id_4             = $this->shortcode_atts['module_id_4'];
		$module_class_1          = $this->shortcode_atts['module_class_1'];
		$module_class_2          = $this->shortcode_atts['module_class_2'];
		$module_class_3          = $this->shortcode_atts['module_class_3'];
		$module_class_4          = $this->shortcode_atts['module_class_4'];
		$custom_css_before_1     = $this->shortcode_atts['custom_css_before_1'];
		$custom_css_before_2     = $this->shortcode_atts['custom_css_before_2'];
		$custom_css_before_3     = $this->shortcode_atts['custom_css_before_3'];
		$custom_css_before_4     = $this->shortcode_atts['custom_css_before_4'];
		$custom_css_main_1       = $this->shortcode_atts['custom_css_main_1'];
		$custom_css_main_2       = $this->shortcode_atts['custom_css_main_2'];
		$custom_css_main_3       = $this->shortcode_atts['custom_css_main_3'];
		$custom_css_main_4       = $this->shortcode_atts['custom_css_main_4'];
		$custom_css_after_1      = $this->shortcode_atts['custom_css_after_1'];
		$custom_css_after_2      = $this->shortcode_atts['custom_css_after_2'];
		$custom_css_after_3      = $this->shortcode_atts['custom_css_after_3'];
		$custom_css_after_4      = $this->shortcode_atts['custom_css_after_4'];
		$use_background_color_gradient_1              = $this->shortcode_atts['use_background_color_gradient_1'];
		$use_background_color_gradient_2              = $this->shortcode_atts['use_background_color_gradient_2'];
		$use_background_color_gradient_3              = $this->shortcode_atts['use_background_color_gradient_3'];
		$use_background_color_gradient_4              = $this->shortcode_atts['use_background_color_gradient_4'];
		$background_color_gradient_type_1             = $this->shortcode_atts['background_color_gradient_type_1'];
		$background_color_gradient_type_2             = $this->shortcode_atts['background_color_gradient_type_2'];
		$background_color_gradient_type_3             = $this->shortcode_atts['background_color_gradient_type_3'];
		$background_color_gradient_type_4             = $this->shortcode_atts['background_color_gradient_type_4'];
		$background_color_gradient_direction_1        = $this->shortcode_atts['background_color_gradient_direction_1'];
		$background_color_gradient_direction_2        = $this->shortcode_atts['background_color_gradient_direction_2'];
		$background_color_gradient_direction_3        = $this->shortcode_atts['background_color_gradient_direction_3'];
		$background_color_gradient_direction_4        = $this->shortcode_atts['background_color_gradient_direction_4'];
		$background_color_gradient_direction_radial_1 = $this->shortcode_atts['background_color_gradient_direction_radial_1'];
		$background_color_gradient_direction_radial_2 = $this->shortcode_atts['background_color_gradient_direction_radial_2'];
		$background_color_gradient_direction_radial_3 = $this->shortcode_atts['background_color_gradient_direction_radial_3'];
		$background_color_gradient_direction_radial_4 = $this->shortcode_atts['background_color_gradient_direction_radial_4'];
		$background_color_gradient_start_1            = $this->shortcode_atts['background_color_gradient_start_1'];
		$background_color_gradient_start_2            = $this->shortcode_atts['background_color_gradient_start_2'];
		$background_color_gradient_start_3            = $this->shortcode_atts['background_color_gradient_start_3'];
		$background_color_gradient_start_4            = $this->shortcode_atts['background_color_gradient_start_4'];
		$background_color_gradient_end_1              = $this->shortcode_atts['background_color_gradient_end_1'];
		$background_color_gradient_end_2              = $this->shortcode_atts['background_color_gradient_end_2'];
		$background_color_gradient_end_3              = $this->shortcode_atts['background_color_gradient_end_3'];
		$background_color_gradient_end_4              = $this->shortcode_atts['background_color_gradient_end_4'];
		$background_color_gradient_start_position_1   = $this->shortcode_atts['background_color_gradient_start_position_1'];
		$background_color_gradient_start_position_2   = $this->shortcode_atts['background_color_gradient_start_position_2'];
		$background_color_gradient_start_position_3   = $this->shortcode_atts['background_color_gradient_start_position_3'];
		$background_color_gradient_start_position_4   = $this->shortcode_atts['background_color_gradient_start_position_4'];
		$background_color_gradient_end_position_1     = $this->shortcode_atts['background_color_gradient_end_position_1'];
		$background_color_gradient_end_position_2     = $this->shortcode_atts['background_color_gradient_end_position_2'];
		$background_color_gradient_end_position_3     = $this->shortcode_atts['background_color_gradient_end_position_3'];
		$background_color_gradient_end_position_4     = $this->shortcode_atts['background_color_gradient_end_position_4'];
		$background_video_mp4_1     = $this->shortcode_atts['background_video_mp4_1'];
		$background_video_mp4_2     = $this->shortcode_atts['background_video_mp4_2'];
		$background_video_mp4_3     = $this->shortcode_atts['background_video_mp4_3'];
		$background_video_mp4_4     = $this->shortcode_atts['background_video_mp4_4'];
		$background_video_webm_1    = $this->shortcode_atts['background_video_webm_1'];
		$background_video_webm_2    = $this->shortcode_atts['background_video_webm_2'];
		$background_video_webm_3    = $this->shortcode_atts['background_video_webm_3'];
		$background_video_webm_4    = $this->shortcode_atts['background_video_webm_4'];
		$background_video_width_1   = $this->shortcode_atts['background_video_width_1'];
		$background_video_width_2   = $this->shortcode_atts['background_video_width_2'];
		$background_video_width_3   = $this->shortcode_atts['background_video_width_3'];
		$background_video_width_4   = $this->shortcode_atts['background_video_width_4'];
		$background_video_height_1  = $this->shortcode_atts['background_video_height_1'];
		$background_video_height_2  = $this->shortcode_atts['background_video_height_2'];
		$background_video_height_3  = $this->shortcode_atts['background_video_height_3'];
		$background_video_height_4  = $this->shortcode_atts['background_video_height_4'];
		$allow_player_pause_1       = $this->shortcode_atts['allow_player_pause_1'];
		$allow_player_pause_2       = $this->shortcode_atts['allow_player_pause_2'];
		$allow_player_pause_3       = $this->shortcode_atts['allow_player_pause_3'];
		$allow_player_pause_4       = $this->shortcode_atts['allow_player_pause_4'];

		global $et_pb_all_column_settings, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

		$et_pb_all_column_settings = ! isset( $et_pb_all_column_settings ) ?  array() : $et_pb_all_column_settings;

		$et_pb_all_column_settings_backup = $et_pb_all_column_settings;

		$keep_column_padding_mobile = $column_padding_mobile;

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module, $function_name );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$custom_padding_responsive_active = et_pb_get_responsive_status( $custom_padding_last_edited );

		$padding_mobile_values = $custom_padding_responsive_active ? array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		) : array(
			'tablet' => false,
			'phone' => false,
		);

		$et_pb_columns_counter = 0;

		$et_pb_column_backgrounds = array(
			array(
				'color'          => $background_color_1,
				'image'          => $bg_img_1,
				'image_size'     => $background_size_1,
				'image_position' => $background_position_1,
				'image_repeat'   => $background_repeat_1,
				'image_blend'    => $background_blend_1,
			),
			array(
				'color'          => $background_color_2,
				'image'          => $bg_img_2,
				'image_size'     => $background_size_2,
				'image_position' => $background_position_2,
				'image_repeat'   => $background_repeat_2,
				'image_blend'    => $background_blend_2,
			),
			array(
				'color'          => $background_color_3,
				'image'          => $bg_img_3,
				'image_size'     => $background_size_3,
				'image_position' => $background_position_3,
				'image_repeat'   => $background_repeat_3,
				'image_blend'    => $background_blend_3,
			),
			array(
				'color'          => $background_color_4,
				'image'          => $bg_img_4,
				'image_size'     => $background_size_4,
				'image_position' => $background_position_4,
				'image_repeat'   => $background_repeat_4,
				'image_blend'    => $background_blend_4,
			),
		);

		$et_pb_column_backgrounds_gradient = array(
			array(
				'active'           => $use_background_color_gradient_1,
				'type'             => $background_color_gradient_type_1,
				'direction'        => $background_color_gradient_direction_1,
				'radial_direction' => $background_color_gradient_direction_radial_1,
				'color_start'      => $background_color_gradient_start_1,
				'color_end'        => $background_color_gradient_end_1,
				'start_position'   => $background_color_gradient_start_position_1,
				'end_position'     => $background_color_gradient_end_position_1,
			),
			array(
				'active'           => $use_background_color_gradient_2,
				'type'             => $background_color_gradient_type_2,
				'direction'        => $background_color_gradient_direction_2,
				'radial_direction' => $background_color_gradient_direction_radial_2,
				'color_start'      => $background_color_gradient_start_2,
				'color_end'        => $background_color_gradient_end_2,
				'start_position'   => $background_color_gradient_start_position_2,
				'end_position'     => $background_color_gradient_end_position_2,
			),
			array(
				'active'           => $use_background_color_gradient_3,
				'type'             => $background_color_gradient_type_3,
				'direction'        => $background_color_gradient_direction_3,
				'radial_direction' => $background_color_gradient_direction_radial_3,
				'color_start'      => $background_color_gradient_start_3,
				'color_end'        => $background_color_gradient_end_3,
				'start_position'   => $background_color_gradient_start_position_3,
				'end_position'     => $background_color_gradient_end_position_3,
			),
			array(
				'active'           => $use_background_color_gradient_4,
				'type'             => $background_color_gradient_type_4,
				'direction'        => $background_color_gradient_direction_4,
				'radial_direction' => $background_color_gradient_direction_radial_4,
				'color_start'      => $background_color_gradient_start_4,
				'color_end'        => $background_color_gradient_end_4,
				'start_position'   => $background_color_gradient_start_position_4,
				'end_position'     => $background_color_gradient_end_position_4,
			),
		);

		$et_pb_column_backgrounds_video = array(
			array(
				'background_video_mp4'         => $background_video_mp4_1,
				'background_video_webm'        => $background_video_webm_1,
				'background_video_width'       => $background_video_width_1,
				'background_video_height'      => $background_video_height_1,
				'background_video_allow_pause' => $allow_player_pause_1,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_2,
				'background_video_webm'        => $background_video_webm_2,
				'background_video_width'       => $background_video_width_2,
				'background_video_height'      => $background_video_height_2,
				'background_video_allow_pause' => $allow_player_pause_2,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_3,
				'background_video_webm'        => $background_video_webm_3,
				'background_video_width'       => $background_video_width_3,
				'background_video_height'      => $background_video_height_3,
				'background_video_allow_pause' => $allow_player_pause_3,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_4,
				'background_video_webm'        => $background_video_webm_4,
				'background_video_width'       => $background_video_width_4,
				'background_video_height'      => $background_video_height_4,
				'background_video_allow_pause' => $allow_player_pause_4,
			),
		);

		$et_pb_column_paddings = array(
			array(
				'padding-top'    => $padding_top_1,
				'padding-right'  => $padding_right_1,
				'padding-bottom' => $padding_bottom_1,
				'padding-left'   => $padding_left_1
			),
			array(
				'padding-top'    => $padding_top_2,
				'padding-right'  => $padding_right_2,
				'padding-bottom' => $padding_bottom_2,
				'padding-left'   => $padding_left_2
			),
			array(
				'padding-top'    => $padding_top_3,
				'padding-right'  => $padding_right_3,
				'padding-bottom' => $padding_bottom_3,
				'padding-left'   => $padding_left_3
			),
			array(
				'padding-top'    => $padding_top_4,
				'padding-right'  => $padding_right_4,
				'padding-bottom' => $padding_bottom_4,
				'padding-left'   => $padding_left_4
			),
		);

		$et_pb_column_paddings_mobile = array(
			array(
				'tablet' => explode( '|', $padding_1_tablet ),
				'phone'  => explode( '|', $padding_1_phone ),
				'last_edited' => $padding_1_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_2_tablet ),
				'phone'  => explode( '|', $padding_2_phone ),
				'last_edited' => $padding_2_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_3_tablet ),
				'phone'  => explode( '|', $padding_3_phone ),
				'last_edited' => $padding_3_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_4_tablet ),
				'phone'  => explode( '|', $padding_4_phone ),
				'last_edited' => $padding_4_last_edited,
			),
		);

		$et_pb_column_parallax = array(
			array( $parallax_1, $parallax_method_1 ),
			array( $parallax_2, $parallax_method_2 ),
			array( $parallax_3, $parallax_method_3 ),
			array( $parallax_4, $parallax_method_4 ),
		);

		$et_pb_column_css = array(
			'css_class'         => array( $module_class_1, $module_class_2, $module_class_3, $module_class_4 ),
			'css_id'            => array( $module_id_1, $module_id_2, $module_id_3, $module_id_4 ),
			'custom_css_before' => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3, $custom_css_before_4 ),
			'custom_css_main'   => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3, $custom_css_main_4 ),
			'custom_css_after'  => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3, $custom_css_after_4 ),
		);

		$internal_columns_settings_array = array(
			'keep_column_padding_mobile' => $keep_column_padding_mobile,
			'et_pb_column_backgrounds' => $et_pb_column_backgrounds,
			'et_pb_column_backgrounds_gradient' => $et_pb_column_backgrounds_gradient,
			'et_pb_column_backgrounds_video' => $et_pb_column_backgrounds_video,
			'et_pb_columns_counter' => $et_pb_columns_counter,
			'et_pb_column_paddings' => $et_pb_column_paddings,
			'et_pb_column_paddings_mobile' => $et_pb_column_paddings_mobile,
			'et_pb_column_parallax' => $et_pb_column_parallax,
			'et_pb_column_css' => $et_pb_column_css,
		);

		$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

		$et_pb_all_column_settings[ $current_row_position ] = $internal_columns_settings_array;

		$module_class .= ' et_pb_row';

		if ( $et_pb_rendering_column_content ) {
			$et_pb_rendering_column_content_row = true;
		}

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$module_class .= 'on' === $make_equal ? ' et_pb_equal_columns' : '';

		if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
			$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter width to 1 if 0 entered by user
			$module_class .= ' et_pb_gutters' . $gutter_width;
		}


		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of Rows support only top and bottom padding, so we need to handle it along with the full padding in the recent version
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right' => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left' => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '%%order_class%%.et_pb_row',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default)
					if ( in_array( $padding_mobile, array( 'on', 'off' ) ) && 'on' !== $padding_mobile && ! $custom_padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				et_pb_generate_responsive_css( $padding_mobile_values_processed, '%%order_class%%.et_pb_row', '', $function_name, ' !important; ' );
			}
		}

		if ( 'on' === $make_fullwidth && 'off' === $use_custom_width ) {
			$module_class .= ' et_pb_row_fullwidth';
		}

		if ( 'on' === $use_custom_width ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'max-width:%1$s !important;',
					'on' === $width_unit ? esc_attr( $custom_width_px ) : esc_attr( $custom_width_percent )
				),
			) );
		}

		$parallax_image = $this->get_parallax_image_background();
		$background_video = $this->video_background();

		$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );
		$module_class .= '' == trim( $inner_content ) ? ' et_pb_row_empty' : '';

		if ( $et_pb_rendering_column_content_row ) {
			$et_pb_rendering_column_content_row = false;
		}

		// reset the global column settings to make sure they are not affected by internal content
		$et_pb_all_column_settings = $et_pb_all_column_settings_backup;

		$output = sprintf(
			'<div%4$s class="%2$s%6$s%7$s">
				%8$s
				%5$s
				%1$s
			</div> <!-- .%3$s -->',
			$inner_content,
			esc_attr( $module_class ),
			esc_html( $function_name ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			$background_video,
			( '' !== $background_video ? ' et_pb_section_video et_pb_preload' : '' ),
			( '' !== $parallax_image ? ' et_pb_section_parallax' : '' ),
			$parallax_image
		);

		return $output;
	}
}
new ET_Builder_Row;

class ET_Builder_Row_Inner extends ET_Builder_Structure_Element {
	function init() {
		$this->name = esc_html__( 'Row', 'et_builder' );
		$this->slug = 'et_pb_row_inner';
		$this->fb_support = true;

		$this->advanced_options = array(
			'background' => array(
				'use_background_color' => true,
				'use_background_image' => true,
				'use_background_color_gradient' => true,
				'use_background_video' => true,
			),
			'custom_margin_padding' => array(
				'use_padding'       => false,
				'css'               => array(
					'main' => '%%order_class%%.et_pb_row_inner',
					'important' => 'all',
				),
				'custom_margin'     => array(
					'priority' => 1,
				),
				'toggle_slug'       => 'margin_padding',
			),
		);

		$this->options_toggles = array(
			'general' => array(
				'toggles' => array(
					'background'     => array(
						'title'       => esc_html__( 'Background', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
						),
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'height'         => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
					'margin_padding' => array(
						'title'       => esc_html__( 'Spacing', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
						),
						'priority'    => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'classes' => array(
						'title'  => esc_html__( 'CSS ID & Classes', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
						),
					),
					'custom_css' => array(
						'title'  => esc_html__( 'Custom CSS', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
						),
					),
				),
			),
		);

		$this->whitelisted_fields = array(
			'custom_padding',
			'custom_padding_tablet',
			'custom_padding_phone',
			'custom_padding_last_edited',
			'padding_mobile',
			'column_padding_mobile',
			'use_custom_gutter',
			'gutter_width',
			'module_id',
			'module_class',
			'make_equal',
			'columns',
			'background_color_1',
			'background_color_2',
			'background_color_3',
			'bg_img_1',
			'bg_img_2',
			'bg_img_3',
			'padding_top_1',
			'padding_right_1',
			'padding_bottom_1',
			'padding_left_1',
			'padding_top_2',
			'padding_right_2',
			'padding_bottom_2',
			'padding_left_2',
			'padding_top_3',
			'padding_right_3',
			'padding_bottom_3',
			'padding_left_3',
			'padding_1_tablet',
			'padding_2_tablet',
			'padding_3_tablet',
			'padding_1_phone',
			'padding_2_phone',
			'padding_3_phone',
			'padding_1_last_edited',
			'padding_2_last_edited',
			'padding_3_last_edited',
			'parallax_1',
			'parallax_method_1',
			'parallax_2',
			'parallax_method_2',
			'parallax_3',
			'parallax_method_3',
			'background_size_1',
			'background_position_1',
			'background_repeat_1',
			'background_blend_1',
			'background_size_2',
			'background_position_2',
			'background_repeat_2',
			'background_blend_2',
			'background_size_3',
			'background_position_3',
			'background_repeat_3',
			'background_blend_3',
			'module_id_1',
			'module_id_2',
			'module_id_3',
			'module_class_1',
			'module_class_2',
			'module_class_3',
			'custom_css_before_1',
			'custom_css_before_2',
			'custom_css_before_3',
			'custom_css_main_1',
			'custom_css_main_2',
			'custom_css_main_3',
			'custom_css_after_1',
			'custom_css_after_2',
			'custom_css_after_3',
			'admin_label',
			'use_background_color_gradient_1',
			'background_color_gradient_start_1',
			'background_color_gradient_end_1',
			'background_color_gradient_type_1',
			'background_color_gradient_direction_1',
			'background_color_gradient_direction_radial_1',
			'background_color_gradient_start_position_1',
			'background_color_gradient_end_position_1',
			'use_background_color_gradient_2',
			'background_color_gradient_start_2',
			'background_color_gradient_end_2',
			'background_color_gradient_type_2',
			'background_color_gradient_direction_2',
			'background_color_gradient_direction_radial_2',
			'background_color_gradient_start_position_2',
			'background_color_gradient_end_position_2',
			'use_background_color_gradient_3',
			'background_color_gradient_start_3',
			'background_color_gradient_end_3',
			'background_color_gradient_type_3',
			'background_color_gradient_direction_3',
			'background_color_gradient_direction_radial_3',
			'background_color_gradient_start_position_3',
			'background_color_gradient_end_position_3',
			'background_video_mp4_1',
			'background_video_webm_1',
			'background_video_width_1',
			'background_video_height_1',
			'allow_player_pause_1',
			'__video_background_1',
			'background_video_mp4_2',
			'background_video_webm_2',
			'background_video_width_2',
			'background_video_height_2',
			'allow_player_pause_2',
			'__video_background_2',
			'background_video_mp4_3',
			'background_video_webm_3',
			'background_video_width_3',
			'background_video_height_3',
			'allow_player_pause_3',
			'__video_background_3',
		);

		$this->fields_defaults = array(
			'padding_mobile'        => array( '' ),
			'column_padding_mobile' => array( '' ),
			'use_custom_gutter'     => array( 'off' ),
			'gutter_width'          => array( '' ),
			'make_equal'            => array( 'off' ),
			'background_color_1'    => array( '' ),
			'background_color_2'    => array( '' ),
			'background_color_3'    => array( '' ),
			'bg_img_1'              => array( '' ),
			'bg_img_2'              => array( '' ),
			'bg_img_3'              => array( '' ),
			'padding_top_1'         => array( '' ),
			'padding_right_1'       => array( '' ),
			'padding_bottom_1'      => array( '' ),
			'padding_left_1'        => array( '' ),
			'padding_top_2'         => array( '' ),
			'padding_right_2'       => array( '' ),
			'padding_bottom_2'      => array( '' ),
			'padding_left_2'        => array( '' ),
			'padding_top_3'         => array( '' ),
			'padding_right_3'       => array( '' ),
			'padding_bottom_3'      => array( '' ),
			'padding_left_3'        => array( '' ),
			'parallax_1'            => array( 'off' ),
			'parallax_method_1'     => array( 'on' ),
			'parallax_2'            => array( 'off' ),
			'parallax_method_2'     => array( 'on' ),
			'parallax_3'            => array( 'off' ),
			'parallax_method_3'     => array( 'on' ),
			'custom_padding_tablet' => array( '' ),
			'custom_padding_phone'  => array( '' ),
		);
	}

	function get_fields() {
		$fields = array(
			'custom_padding' => array(
				'label'           => esc_html__( 'Custom Padding', 'et_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
			),
			'custom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'custom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'padding_mobile' => array(
				'label' => esc_html__( 'Keep Custom Padding on Mobile', 'et_builder' ),
				'type'        => 'skip', // Remaining attribute for backward compatibility
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'use_custom_gutter' => array(
				'label'             => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'gutter_width',
				),
				'description'       => esc_html__( 'Enable this option to define custom gutter width for this row.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'height',
			),
			'gutter_width' => array(
				'label'           => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'range_settings'  => array(
					'min'  => 1,
					'max'  => 4,
					'step' => 1,
				),
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'Adjust the spacing between each column in this row.', 'et_builder' ),
				'validate_unit'   => false,
				'fixed_range'     => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'height',
			),
			'make_equal' => array(
				'label'             => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'height',
			),
			'background_color' => array(
				'label'        => esc_html__( 'Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'background',
			),
			'columns_background' => array(
				'type'            => 'column_settings_background',
				'option_category' => 'configuration',
				'toggle_slug'     => 'background',
			),
			'columns_padding' => array(
				'type'            => 'column_settings_padding',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
			),
			'column_padding_mobile' => array(
				'label' => esc_html__( 'Keep Column Padding on Mobile', 'et_builder' ),
				'type'  => 'skip', // Remaining attribute for backward compatibility
			),
			'background_color_1' => array(
				'type' => 'skip',
			),
			'background_color_2' => array(
				'type' => 'skip',
			),
			'background_color_3' => array(
				'type' => 'skip',
			),
			'bg_img_1' => array(
				'type' => 'skip',
			),
			'bg_img_2' => array(
				'type' => 'skip',
			),
			'bg_img_3' => array(
				'type' => 'skip',
			),
			'padding_top_1' => array(
				'type' => 'skip',
			),
			'padding_right_1' => array(
				'type' => 'skip',
			),
			'padding_bottom_1' => array(
				'type' => 'skip',
			),
			'padding_left_1' => array(
				'type' => 'skip',
			),
			'padding_top_2' => array(
				'type' => 'skip',
			),
			'padding_right_2' => array(
				'type' => 'skip',
			),
			'padding_bottom_2' => array(
				'type' => 'skip',
			),
			'padding_left_2' => array(
				'type' => 'skip',
			),
			'padding_top_3' => array(
				'type' => 'skip',
			),
			'padding_right_3' => array(
				'type' => 'skip',
			),
			'padding_bottom_3' => array(
				'type' => 'skip',
			),
			'padding_left_3' => array(
				'type' => 'skip',
			),
			'parallax_1' => array(
				'type' => 'skip',
			),
			'parallax_method_1' => array(
				'type' => 'skip',
			),
			'parallax_2' => array(
				'type' => 'skip',
			),
			'parallax_method_2' => array(
				'type' => 'skip',
			),
			'parallax_3' => array(
				'type' => 'skip',
			),
			'parallax_method_3' => array(
				'type' => 'skip',
			),
			'background_size_1' => array(
				'type' => 'skip',
			),
			'background_size_2' => array(
				'type' => 'skip',
			),
			'background_size_3' => array(
				'type' => 'skip',
			),
			'background_position_1' => array(
				'type' => 'skip',
			),
			'background_position_2' => array(
				'type' => 'skip',
			),
			'background_position_3' => array(
				'type' => 'skip',
			),
			'background_repeat_1' => array(
				'type' => 'skip',
			),
			'background_repeat_2' => array(
				'type' => 'skip',
			),
			'background_repeat_3' => array(
				'type' => 'skip',
			),
			'background_blend_1' => array(
				'type' => 'skip',
			),
			'background_blend_2' => array(
				'type' => 'skip',
			),
			'background_blend_3' => array(
				'type' => 'skip',
			),
			'padding_1_tablet' => array(
				'type' => 'skip',
			),
			'padding_2_tablet' => array(
				'type' => 'skip',
			),
			'padding_3_tablet' => array(
				'type' => 'skip',
			),
			'padding_1_phone' => array(
				'type' => 'skip',
			),
			'padding_2_phone' => array(
				'type' => 'skip',
			),
			'padding_3_phone' => array(
				'type' => 'skip',
			),
			'padding_1_last_edited' => array(
				'type' => 'skip',
			),
			'padding_2_last_edited' => array(
				'type' => 'skip',
			),
			'padding_3_last_edited' => array(
				'type' => 'skip',
			),
			'module_id_1' => array(
				'type' => 'skip',
			),
			'module_id_2' => array(
				'type' => 'skip',
			),
			'module_id_3' => array(
				'type' => 'skip',
			),
			'module_class_1' => array(
				'type' => 'skip',
			),
			'module_class_2' => array(
				'type' => 'skip',
			),
			'module_class_3' => array(
				'type' => 'skip',
			),
			'custom_css_before_1' => array(
				'type' => 'skip',
			),
			'custom_css_before_2' => array(
				'type' => 'skip',
			),
			'custom_css_before_3' => array(
				'type' => 'skip',
			),
			'custom_css_main_1' => array(
				'type' => 'skip',
			),
			'custom_css_main_2' => array(
				'type' => 'skip',
			),
			'custom_css_main_3' => array(
				'type' => 'skip',
			),
			'custom_css_after_1' => array(
				'type' => 'skip',
			),
			'custom_css_after_2' => array(
				'type' => 'skip',
			),
			'custom_css_after_3' => array(
				'type' => 'skip',
			),
			'columns_css' => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'custom_css',
				'priority'        => '20',
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
				'description' => esc_html__( 'This will change the label of the row in the builder for easy identification when collapsed.', 'et_builder' ),
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
			'custom_padding_last_edited' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'columns_css_fields' => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
			),
			'use_background_color_gradient_1' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_2' => array(
				'type' => 'skip',
			),
			'use_background_color_gradient_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_type_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_direction_radial_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_start_position_3' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_1' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_2' => array(
				'type' => 'skip',
			),
			'background_color_gradient_end_position_3' => array(
				'type' => 'skip',
			),
			'background_video_mp4_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_webm_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_width_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'background_video_height_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'allow_player_pause_1' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_1',
				),
			),
			'__video_background_1' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_1',
					'background_video_webm_1',
					'background_video_width_1',
					'background_video_height_1',
				),
				'computed_minimum' => array(
					'background_video_mp4_1',
					'background_video_webm_1',
				),
			),
			'background_video_mp4_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_webm_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_width_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'background_video_height_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'allow_player_pause_2' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_2',
				),
			),
			'__video_background_2' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_2',
					'background_video_webm_2',
					'background_video_width_2',
					'background_video_height_2',
				),
				'computed_minimum' => array(
					'background_video_mp4_2',
					'background_video_webm_2',
				),
			),
			'background_video_mp4_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_webm_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_width_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'background_video_height_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'allow_player_pause_3' => array(
				'type' => 'skip',
				'computed_affects'   => array(
					'__video_background_3',
				),
			),
			'__video_background_3' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Column', 'get_column_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4_3',
					'background_video_webm_3',
					'background_video_width_3',
					'background_video_height_3',
				),
				'computed_minimum' => array(
					'background_video_mp4_3',
					'background_video_webm_3',
				),
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$background_color_1      = $this->shortcode_atts['background_color_1'];
		$background_color_2      = $this->shortcode_atts['background_color_2'];
		$background_color_3      = $this->shortcode_atts['background_color_3'];
		$bg_img_1                = $this->shortcode_atts['bg_img_1'];
		$bg_img_2                = $this->shortcode_atts['bg_img_2'];
		$bg_img_3                = $this->shortcode_atts['bg_img_3'];
		$background_size_1       = $this->shortcode_atts['background_size_1'];
		$background_size_2       = $this->shortcode_atts['background_size_2'];
		$background_size_3       = $this->shortcode_atts['background_size_3'];
		$background_position_1   = $this->shortcode_atts['background_position_1'];
		$background_position_2   = $this->shortcode_atts['background_position_2'];
		$background_position_3   = $this->shortcode_atts['background_position_3'];
		$background_repeat_1     = $this->shortcode_atts['background_repeat_1'];
		$background_repeat_2     = $this->shortcode_atts['background_repeat_2'];
		$background_repeat_3     = $this->shortcode_atts['background_repeat_3'];
		$background_blend_1      = $this->shortcode_atts['background_blend_1'];
		$background_blend_2      = $this->shortcode_atts['background_blend_2'];
		$background_blend_3      = $this->shortcode_atts['background_blend_3'];
		$padding_top_1           = $this->shortcode_atts['padding_top_1'];
		$padding_right_1         = $this->shortcode_atts['padding_right_1'];
		$padding_bottom_1        = $this->shortcode_atts['padding_bottom_1'];
		$padding_left_1          = $this->shortcode_atts['padding_left_1'];
		$padding_top_2           = $this->shortcode_atts['padding_top_2'];
		$padding_right_2         = $this->shortcode_atts['padding_right_2'];
		$padding_bottom_2        = $this->shortcode_atts['padding_bottom_2'];
		$padding_left_2          = $this->shortcode_atts['padding_left_2'];
		$padding_top_3           = $this->shortcode_atts['padding_top_3'];
		$padding_right_3         = $this->shortcode_atts['padding_right_3'];
		$padding_bottom_3        = $this->shortcode_atts['padding_bottom_3'];
		$padding_left_3          = $this->shortcode_atts['padding_left_3'];
		$padding_1_tablet        = $this->shortcode_atts['padding_1_tablet'];
		$padding_2_tablet        = $this->shortcode_atts['padding_2_tablet'];
		$padding_3_tablet        = $this->shortcode_atts['padding_3_tablet'];
		$padding_1_phone         = $this->shortcode_atts['padding_1_phone'];
		$padding_2_phone         = $this->shortcode_atts['padding_2_phone'];
		$padding_3_phone         = $this->shortcode_atts['padding_3_phone'];
		$padding_1_last_edited   = $this->shortcode_atts['padding_1_last_edited'];
		$padding_2_last_edited   = $this->shortcode_atts['padding_2_last_edited'];
		$padding_3_last_edited   = $this->shortcode_atts['padding_3_last_edited'];
		$gutter_width            = $this->shortcode_atts['gutter_width'];
		$make_equal              = $this->shortcode_atts['make_equal'];
		$custom_padding          = $this->shortcode_atts['custom_padding'];
		$padding_mobile          = $this->shortcode_atts['padding_mobile'];
		$custom_padding_tablet   = $this->shortcode_atts['custom_padding_tablet'];
		$custom_padding_phone    = $this->shortcode_atts['custom_padding_phone'];
		$custom_padding_last_edited = $this->shortcode_atts['custom_padding_last_edited'];
		$column_padding_mobile   = $this->shortcode_atts['column_padding_mobile'];
		$global_module           = $this->shortcode_atts['global_module'];
		$use_custom_gutter       = $this->shortcode_atts['use_custom_gutter'];
		$parallax_1              = $this->shortcode_atts['parallax_1'];
		$parallax_method_1       = $this->shortcode_atts['parallax_method_1'];
		$parallax_2              = $this->shortcode_atts['parallax_2'];
		$parallax_method_2       = $this->shortcode_atts['parallax_method_2'];
		$parallax_3              = $this->shortcode_atts['parallax_3'];
		$parallax_method_3       = $this->shortcode_atts['parallax_method_3'];
		$module_id_1             = $this->shortcode_atts['module_id_1'];
		$module_id_2             = $this->shortcode_atts['module_id_2'];
		$module_id_3             = $this->shortcode_atts['module_id_3'];
		$module_class_1          = $this->shortcode_atts['module_class_1'];
		$module_class_2          = $this->shortcode_atts['module_class_2'];
		$module_class_3          = $this->shortcode_atts['module_class_3'];
		$custom_css_before_1     = $this->shortcode_atts['custom_css_before_1'];
		$custom_css_before_2     = $this->shortcode_atts['custom_css_before_2'];
		$custom_css_before_3     = $this->shortcode_atts['custom_css_before_3'];
		$custom_css_main_1       = $this->shortcode_atts['custom_css_main_1'];
		$custom_css_main_2       = $this->shortcode_atts['custom_css_main_2'];
		$custom_css_main_3       = $this->shortcode_atts['custom_css_main_3'];
		$custom_css_after_1      = $this->shortcode_atts['custom_css_after_1'];
		$custom_css_after_2      = $this->shortcode_atts['custom_css_after_2'];
		$custom_css_after_3      = $this->shortcode_atts['custom_css_after_3'];
		$use_background_color_gradient_1              = $this->shortcode_atts['use_background_color_gradient_1'];
		$use_background_color_gradient_2              = $this->shortcode_atts['use_background_color_gradient_2'];
		$use_background_color_gradient_3              = $this->shortcode_atts['use_background_color_gradient_3'];
		$background_color_gradient_type_1             = $this->shortcode_atts['background_color_gradient_type_1'];
		$background_color_gradient_type_2             = $this->shortcode_atts['background_color_gradient_type_2'];
		$background_color_gradient_type_3             = $this->shortcode_atts['background_color_gradient_type_3'];
		$background_color_gradient_direction_1        = $this->shortcode_atts['background_color_gradient_direction_1'];
		$background_color_gradient_direction_2        = $this->shortcode_atts['background_color_gradient_direction_2'];
		$background_color_gradient_direction_3        = $this->shortcode_atts['background_color_gradient_direction_3'];
		$background_color_gradient_direction_radial_1 = $this->shortcode_atts['background_color_gradient_direction_radial_1'];
		$background_color_gradient_direction_radial_2 = $this->shortcode_atts['background_color_gradient_direction_radial_2'];
		$background_color_gradient_direction_radial_3 = $this->shortcode_atts['background_color_gradient_direction_radial_3'];
		$background_color_gradient_start_1            = $this->shortcode_atts['background_color_gradient_start_1'];
		$background_color_gradient_start_2            = $this->shortcode_atts['background_color_gradient_start_2'];
		$background_color_gradient_start_3            = $this->shortcode_atts['background_color_gradient_start_3'];
		$background_color_gradient_end_1              = $this->shortcode_atts['background_color_gradient_end_1'];
		$background_color_gradient_end_2              = $this->shortcode_atts['background_color_gradient_end_2'];
		$background_color_gradient_end_3              = $this->shortcode_atts['background_color_gradient_end_3'];
		$background_color_gradient_start_position_1   = $this->shortcode_atts['background_color_gradient_start_position_1'];
		$background_color_gradient_start_position_2   = $this->shortcode_atts['background_color_gradient_start_position_2'];
		$background_color_gradient_start_position_3   = $this->shortcode_atts['background_color_gradient_start_position_3'];
		$background_color_gradient_end_position_1     = $this->shortcode_atts['background_color_gradient_end_position_1'];
		$background_color_gradient_end_position_2     = $this->shortcode_atts['background_color_gradient_end_position_2'];
		$background_color_gradient_end_position_3     = $this->shortcode_atts['background_color_gradient_end_position_3'];
		$background_video_mp4_1     = $this->shortcode_atts['background_video_mp4_1'];
		$background_video_mp4_2     = $this->shortcode_atts['background_video_mp4_2'];
		$background_video_mp4_3     = $this->shortcode_atts['background_video_mp4_3'];
		$background_video_webm_1    = $this->shortcode_atts['background_video_webm_1'];
		$background_video_webm_2    = $this->shortcode_atts['background_video_webm_2'];
		$background_video_webm_3    = $this->shortcode_atts['background_video_webm_3'];
		$background_video_width_1   = $this->shortcode_atts['background_video_width_1'];
		$background_video_width_2   = $this->shortcode_atts['background_video_width_2'];
		$background_video_width_3   = $this->shortcode_atts['background_video_width_3'];
		$background_video_height_1  = $this->shortcode_atts['background_video_height_1'];
		$background_video_height_2  = $this->shortcode_atts['background_video_height_2'];
		$background_video_height_3  = $this->shortcode_atts['background_video_height_3'];
		$allow_player_pause_1       = $this->shortcode_atts['allow_player_pause_1'];
		$allow_player_pause_2       = $this->shortcode_atts['allow_player_pause_2'];
		$allow_player_pause_3       = $this->shortcode_atts['allow_player_pause_3'];

		global $et_pb_all_column_settings_inner, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

		$et_pb_all_column_settings_inner = ! isset( $et_pb_all_column_settings_inner ) ?  array() : $et_pb_all_column_settings_inner;

		$et_pb_all_column_settings_backup = $et_pb_all_column_settings_inner;

		$keep_column_padding_mobile = $column_padding_mobile;

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module, $function_name );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$custom_padding_responsive_active = et_pb_get_responsive_status( $custom_padding_last_edited );

		$padding_mobile_values = $custom_padding_responsive_active ? array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		) : array(
			'tablet' => false,
			'phone' => false,
		);

		$et_pb_columns_inner_counter = 0;
		$et_pb_column_inner_backgrounds = array(
			array(
				'color'          => $background_color_1,
				'image'          => $bg_img_1,
				'image_size'     => $background_size_1,
				'image_position' => $background_position_1,
				'image_repeat'   => $background_repeat_1,
				'image_blend'    => $background_blend_1,
			),
			array(
				'color'          => $background_color_2,
				'image'          => $bg_img_2,
				'image_size'     => $background_size_2,
				'image_position' => $background_position_2,
				'image_repeat'   => $background_repeat_2,
				'image_blend'    => $background_blend_2,
			),
			array(
				'color'          => $background_color_3,
				'image'          => $bg_img_3,
				'image_size'     => $background_size_3,
				'image_position' => $background_position_3,
				'image_repeat'   => $background_repeat_3,
				'image_blend'    => $background_blend_3,
			),
		);

		$et_pb_column_inner_backgrounds_gradient = array(
			array(
				'active'           => $use_background_color_gradient_1,
				'type'             => $background_color_gradient_type_1,
				'direction'        => $background_color_gradient_direction_1,
				'radial_direction' => $background_color_gradient_direction_radial_1,
				'color_start'      => $background_color_gradient_start_1,
				'color_end'        => $background_color_gradient_end_1,
				'start_position'   => $background_color_gradient_start_position_1,
				'end_position'     => $background_color_gradient_end_position_1,
			),
			array(
				'active'           => $use_background_color_gradient_2,
				'type'             => $background_color_gradient_type_2,
				'direction'        => $background_color_gradient_direction_2,
				'radial_direction' => $background_color_gradient_direction_radial_2,
				'color_start'      => $background_color_gradient_start_2,
				'color_end'        => $background_color_gradient_end_2,
				'start_position'   => $background_color_gradient_start_position_2,
				'end_position'     => $background_color_gradient_end_position_2,
			),
			array(
				'active'           => $use_background_color_gradient_3,
				'type'             => $background_color_gradient_type_3,
				'direction'        => $background_color_gradient_direction_3,
				'radial_direction' => $background_color_gradient_direction_radial_3,
				'color_start'      => $background_color_gradient_start_3,
				'color_end'        => $background_color_gradient_end_3,
				'start_position'   => $background_color_gradient_start_position_3,
				'end_position'     => $background_color_gradient_end_position_3,
			),
		);

		$et_pb_column_inner_backgrounds_video = array(
			array(
				'background_video_mp4'         => $background_video_mp4_1,
				'background_video_webm'        => $background_video_webm_1,
				'background_video_width'       => $background_video_width_1,
				'background_video_height'      => $background_video_height_1,
				'background_video_allow_pause' => $allow_player_pause_1,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_2,
				'background_video_webm'        => $background_video_webm_2,
				'background_video_width'       => $background_video_width_2,
				'background_video_height'      => $background_video_height_2,
				'background_video_allow_pause' => $allow_player_pause_2,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_3,
				'background_video_webm'        => $background_video_webm_3,
				'background_video_width'       => $background_video_width_3,
				'background_video_height'      => $background_video_height_3,
				'background_video_allow_pause' => $allow_player_pause_3,
			),
		);

		$et_pb_column_inner_paddings = array(
			array(
				'padding-top'    => $padding_top_1,
				'padding-right'  => $padding_right_1,
				'padding-bottom' => $padding_bottom_1,
				'padding-left'   => $padding_left_1
			),
			array(
				'padding-top'    => $padding_top_2,
				'padding-right'  => $padding_right_2,
				'padding-bottom' => $padding_bottom_2,
				'padding-left'   => $padding_left_2
			),
			array(
				'padding-top'    => $padding_top_3,
				'padding-right'  => $padding_right_3,
				'padding-bottom' => $padding_bottom_3,
				'padding-left'   => $padding_left_3
			),
		);

		$et_pb_column_parallax = array(
			array( $parallax_1, $parallax_method_1 ),
			array( $parallax_2, $parallax_method_2 ),
			array( $parallax_3, $parallax_method_3 ),
		);

		$et_pb_column_inner_paddings_mobile = array(
			array(
				'tablet' => explode( '|', $padding_1_tablet ),
				'phone'  => explode( '|', $padding_1_phone ),
				'last_edited' => $padding_1_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_2_tablet ),
				'phone'  => explode( '|', $padding_2_phone ),
				'last_edited' => $padding_2_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_3_tablet ),
				'phone'  => explode( '|', $padding_3_phone ),
				'last_edited' => $padding_3_last_edited,
			),
		);

		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of Rows support only top and bottom padding, so we need to handle it along with the full padding in the recent version
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right' => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left' => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '.et_pb_column %%order_class%%',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default)
					if ( in_array( $padding_mobile, array( 'on', 'off' ) ) && 'on' !== $padding_mobile && ! $custom_padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				et_pb_generate_responsive_css( $padding_mobile_values_processed, '.et_pb_column %%order_class%%', '', $function_name, ' !important; ' );
			}
		}

		$et_pb_column_inner_css = array(
			'css_class'         => array( $module_class_1, $module_class_2, $module_class_3 ),
			'css_id'            => array( $module_id_1, $module_id_2, $module_id_3 ),
			'custom_css_before' => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3 ),
			'custom_css_main'   => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3 ),
			'custom_css_after'  => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3 ),
		);

		$internal_columns_settings_array = array(
			'keep_column_padding_mobile' => $keep_column_padding_mobile,
			'et_pb_column_inner_backgrounds' => $et_pb_column_inner_backgrounds,
			'et_pb_column_inner_backgrounds_gradient' => $et_pb_column_inner_backgrounds_gradient,
			'et_pb_column_inner_backgrounds_video' => $et_pb_column_inner_backgrounds_video,
			'et_pb_columns_inner_counter' => $et_pb_columns_inner_counter,
			'et_pb_column_inner_paddings' => $et_pb_column_inner_paddings,
			'et_pb_column_inner_paddings_mobile' => $et_pb_column_inner_paddings_mobile,
			'et_pb_column_parallax' => $et_pb_column_parallax,
			'et_pb_column_inner_css' => $et_pb_column_inner_css,
		);

		$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

		$et_pb_all_column_settings_inner[ $current_row_position ] = $internal_columns_settings_array;

		$module_class .= ' et_pb_row_inner';

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );
		$module_class .= '' == trim( $inner_content ) ? ' et_pb_row_empty' : '';

		$module_class .= 'on' === $make_equal ? ' et_pb_equal_columns' : '';

		if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
			$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter to 1 if 0 entered by user
			$module_class .= ' et_pb_gutters' . $gutter_width;
		}

		// reset the global column settings to make sure they are not affected by internal content
		$et_pb_all_column_settings_inner = $et_pb_all_column_settings_backup;

		$output = sprintf(
			'<div%4$s class="%2$s">
				%1$s
			</div> <!-- .%3$s -->',
			$inner_content,
			esc_attr( $module_class ),
			esc_html( $function_name ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' )
		);

		return $output;
	}
}
new ET_Builder_Row_Inner;

class ET_Builder_Column extends ET_Builder_Structure_Element {
	function init() {
		$this->name                       = esc_html__( 'Column', 'et_builder' );
		$this->slug                       = 'et_pb_column';
		$this->additional_shortcode_slugs = array( 'et_pb_column_inner' );
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'type',
			'specialty_columns',
			'saved_specialty_column_type',
			'use_background_color_gradient',
			'background_color_gradient_start',
			'background_color_gradient_end',
			'background_color_gradient_type',
			'background_color_gradient_direction',
			'background_color_gradient_direction_radial',
			'background_color_gradient_start_position',
			'background_color_gradient_end_position',
			'background_video_mp4',
			'background_video_webm',
			'background_video_width',
			'background_video_height',
			'allow_player_pause',
			'__video_background',
		);

		$this->fields_defaults = array(
			'type'                        => array( '4_4' ),
			'specialty_columns'           => array( '' ),
			'saved_specialty_column_type' => array( '' ),
		);
	}

	function get_fields() {
		$fields = array(
			'type' => array(
				'type' => 'skip',
			),
			'specialty_columns' => array(
				'type' => 'skip',
			),
			'saved_specialty_column_type' => array(
				'type' => 'skip',
			),
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$type                        = $this->shortcode_atts['type'];
		$specialty_columns           = $this->shortcode_atts['specialty_columns'];
		$saved_specialty_column_type = $this->shortcode_atts['saved_specialty_column_type'];

		global $et_pb_all_column_settings,
			$et_pb_all_column_settings_inner,
			$et_specialty_column_type,
			$et_pb_rendering_column_content,
			$et_pb_rendering_column_content_row;

		$is_specialty_column = 'et_pb_column_inner' !== $function_name && '' !== $specialty_columns;

		$current_row_position = $et_pb_rendering_column_content_row ? 'internal_row' : 'regular_row';

		if ( 'et_pb_column_inner' !== $function_name ) {
			$et_specialty_column_type = $type;
			$array_index = isset( $et_pb_all_column_settings[ $current_row_position ] ) ? $et_pb_all_column_settings[ $current_row_position ]['et_pb_columns_counter'] : 0;
			$backgrounds_array = isset( $et_pb_all_column_settings[ $current_row_position ] ) ? $et_pb_all_column_settings[ $current_row_position ]['et_pb_column_backgrounds'] : array();
			$background_gradient = isset( $et_pb_all_column_settings[ $current_row_position ] ) ? $et_pb_all_column_settings[ $current_row_position ]['et_pb_column_backgrounds_gradient'][ $array_index ] : '';
			$background_video = isset( $et_pb_all_column_settings[ $current_row_position ] ) ? $et_pb_all_column_settings[ $current_row_position ]['et_pb_column_backgrounds_video'][ $array_index ] : '';
			$paddings_array = isset( $et_pb_all_column_settings[ $current_row_position ] ) ? $et_pb_all_column_settings[ $current_row_position ]['et_pb_column_paddings'] : array();
			$paddings_mobile_array = isset( $et_pb_all_column_settings[ $current_row_position ] ) ? $et_pb_all_column_settings[ $current_row_position ]['et_pb_column_paddings_mobile'] : array();
			$column_css_array = isset( $et_pb_all_column_settings[ $current_row_position ] ) ? $et_pb_all_column_settings[ $current_row_position ]['et_pb_column_css'] : array();
			$keep_column_padding_mobile = isset( $et_pb_all_column_settings[ $current_row_position ] ) ? $et_pb_all_column_settings[ $current_row_position ]['keep_column_padding_mobile'] : 'on';
			$column_parallax = isset( $et_pb_all_column_settings[ $current_row_position ] ) && isset( $et_pb_all_column_settings[ $current_row_position ]['et_pb_column_parallax'] ) ? $et_pb_all_column_settings[ $current_row_position ]['et_pb_column_parallax'] : '';
			if ( isset( $et_pb_all_column_settings[ $current_row_position ] ) ) {
				$et_pb_all_column_settings[ $current_row_position ]['et_pb_columns_counter']++;
			}
		} else {
			$array_index = $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_columns_inner_counter'];
			$backgrounds_array = $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_inner_backgrounds'];
			$background_gradient = isset( $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_inner_backgrounds_gradient'] ) ? $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_inner_backgrounds_gradient'][ $array_index ] : '';
			$background_video = isset( $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_inner_backgrounds_video'] ) ? $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_inner_backgrounds_video'][ $array_index ] : '';
			$paddings_array = $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_inner_paddings'];
			$column_css_array = $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_inner_css'];
			$et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_columns_inner_counter']++;
			$paddings_mobile_array = $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_inner_paddings_mobile'];
			$keep_column_padding_mobile = $et_pb_all_column_settings_inner[ $current_row_position ]['keep_column_padding_mobile'];
			$column_parallax = isset( $et_pb_all_column_settings_inner[ $current_row_position ] ) && isset( $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_parallax'] ) ? $et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_column_parallax'] : '';
		}

		$background_color = isset( $backgrounds_array[$array_index]['color'] ) ? $backgrounds_array[$array_index]['color'] : '';
		$background_img = isset( $backgrounds_array[$array_index]['image'] ) ? $backgrounds_array[$array_index]['image'] : '';
		$background_size = isset( $backgrounds_array[$array_index]['image_size'] ) ? $backgrounds_array[$array_index]['image_size'] : '';
		$background_position = isset( $backgrounds_array[$array_index]['image_position'] ) ? $backgrounds_array[$array_index]['image_position'] : '';
		$background_repeat = isset( $backgrounds_array[$array_index]['image_repeat'] ) ? $backgrounds_array[$array_index]['image_repeat'] : '';
		$background_blend = isset( $backgrounds_array[$array_index]['image_blend'] ) ? $backgrounds_array[$array_index]['image_blend'] : '';
		$padding_values = isset( $paddings_array[$array_index] ) ? $paddings_array[$array_index] : array();
		$padding_mobile_values = isset( $paddings_mobile_array[$array_index] ) ? $paddings_mobile_array[$array_index] : array();
		$padding_last_edited = isset( $padding_mobile_values['last_edited'] ) ? $padding_mobile_values['last_edited'] : 'off|desktop';
		$padding_responsive_active = et_pb_get_responsive_status( $padding_last_edited );
		$parallax_method = isset( $column_parallax[$array_index][0] ) && 'on' === $column_parallax[$array_index][0] ? $column_parallax[$array_index][1] : '';
		$custom_css_class = isset( $column_css_array['css_class'][$array_index] ) ? ' ' . $column_css_array['css_class'][$array_index] : '';
		$custom_css_id = isset( $column_css_array['css_id'][$array_index] ) ? $column_css_array['css_id'][$array_index] : '';
		$custom_css_before = isset( $column_css_array['custom_css_before'][$array_index] ) ? $column_css_array['custom_css_before'][$array_index] : '';
		$custom_css_main = isset( $column_css_array['custom_css_main'][$array_index] ) ? $column_css_array['custom_css_main'][$array_index] : '';
		$custom_css_after = isset( $column_css_array['custom_css_after'][$array_index] ) ? $column_css_array['custom_css_after'][$array_index] : '';
		$background_images = array();

		if ( '' !== $background_gradient && 'on' === $background_gradient['active'] ) {
			$has_background_gradient = true;

			$default_gradient = apply_filters( 'et_pb_default_gradient', array(
				'type'             => 'linear',
				'direction'        => '180deg',
				'radial_direction' => 'center',
				'color_start'      => '#2b87da',
				'color_end'        => '#29c4a9',
				'start_position'   => '0%',
				'end_position'     => '100%',
			) );

			$background_gradient = wp_parse_args( array_filter( $background_gradient ), $default_gradient );

			$direction               = $background_gradient['type'] === 'linear' ? $background_gradient['direction'] : "circle at {$background_gradient['radial_direction']}";
			$start_gradient_position = et_sanitize_input_unit( $background_gradient['start_position'], false, '%' );
			$end_gradient_position   = et_sanitize_input_unit( $background_gradient['end_position'], false, '%');
			$background_images[]     = "{$background_gradient['type']}-gradient(
				{$direction},
				{$background_gradient['color_start']} ${start_gradient_position},
				{$background_gradient['color_end']} ${end_gradient_position}
			)";
		}

		if ( '' !== $background_img && 'on' !== $parallax_method ) {
			$has_background_image = true;

			$background_images[] = sprintf(
				'url(%s)',
				esc_attr( $background_img )
			);

			if ( '' !== $background_size ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-size:%s;',
						esc_attr( $background_size )
					),
				) );
			}

			if ( '' !== $background_position ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-position:%s;',
						esc_attr( str_replace( '_', ' ', $background_position ) )
					),
				) );
			}

			if ( '' !== $background_repeat ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-repeat:%s;',
						esc_attr( $background_repeat )
					),
				) );
			}

			if ( '' !== $background_blend ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-blend-mode:%s;',
						esc_attr( $background_blend )
					),
				) );
			}
		}

		if ( ! empty( $background_images ) ) {
			// The browsers stack the images in the opposite order to what you'd expect.
			$background_images = array_reverse( $background_images );
			$backgorund_images_declaration = sprintf(
				'background-image: %1$s;',
				esc_html( implode( ', ', $background_images ) )
			);

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => esc_attr( $backgorund_images_declaration ),
			) );
		}

		if ( '' !== $background_color && 'rgba(0,0,0,0)' !== $background_color && ! isset( $has_background_gradient, $has_background_image ) ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-color:%s;',
					esc_attr( $background_color )
				),
			) );
		} else if ( isset( $has_background_gradient, $has_background_image ) ) {
			// Force background-color: initial
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => 'background-color: initial;'
			) );
		}

		if ( ! empty( $padding_values ) ) {
			foreach( $padding_values as $position => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '%%order_class%%',
						'declaration' => sprintf(
							'%1$s:%2$s;',
							esc_html( $position ),
							esc_html( et_builder_process_range_value( $value ) )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default)
					if ( in_array( $keep_column_padding_mobile, array( 'on', 'off' ) ) && 'on' !== $keep_column_padding_mobile && ! $padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( $padding_responsive_active && ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				$padding_mobile_selector = 'et_pb_column_inner' !== $function_name ? '.et_pb_row > .et_pb_column%%order_class%%' : '.et_pb_row_inner > .et_pb_column%%order_class%%';
				et_pb_generate_responsive_css( $padding_mobile_values_processed, $padding_mobile_selector, '', $function_name );
			}
		}

		if ( '' !== $custom_css_before ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:before',
				'declaration' => trim( $custom_css_before ),
			) );
		}

		if ( '' !== $custom_css_main ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => trim( $custom_css_main ),
			) );
		}

		if ( '' !== $custom_css_after ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:after',
				'declaration' => trim( $custom_css_after ),
			) );
		}

		if ( 'et_pb_column_inner' === $function_name ) {
			if ( '1_1' === $type ) {
				$type = '4_4';
			}

			$et_specialty_column_type = '' !== $saved_specialty_column_type ? $saved_specialty_column_type : $et_specialty_column_type;

			switch ( $et_specialty_column_type ) {
				case '1_2':
					if ( '1_2' === $type ) {
						$type = '1_4';
					}

					break;
				case '2_3':
					if ( '1_2' === $type ) {
						$type = '1_3';
					}

					break;
				case '3_4':
					if ( '1_2' === $type ) {
						$type = '3_8';
					} else if ( '1_3' === $type ) {
						$type = '1_4';
					}

					break;
			}
		}

		$video_background = trim( $this->video_background( $background_video ) );

		$inner_class = 'et_pb_column_inner' === $function_name ? ' et_pb_column_inner' : '';

		$class = 'et_pb_column_' . $type . $inner_class . $custom_css_class;

		$class = ET_Builder_Element::add_module_order_class( $class, $function_name );

		$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );
		$class .= '' == trim( $inner_content ) ? ' et_pb_column_empty' : '';

		$class .= $is_specialty_column ? ' et_pb_specialty_column' : '';

		$output = sprintf(
			'<div class="et_pb_column %1$s%3$s%6$s"%5$s>
				%7$s
				%4$s
				%2$s
			</div> <!-- .et_pb_column -->',
			esc_attr( $class ),
			$inner_content,
			( '' !== $parallax_method ? ' et_pb_section_parallax' : '' ),
			( '' !== $background_img && '' !== $parallax_method
				? sprintf(
					'<div class="et_parallax_bg%2$s" style="background-image: url(%1$s);"></div>',
					esc_attr( $background_img ),
					( 'off' === $parallax_method ? ' et_pb_parallax_css' : '' )
				)
				: ''
			),
			'' !== $custom_css_id ? sprintf( ' id="%1$s"', esc_attr( $custom_css_id ) ) : '',
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background
		);

		return $output;

	}

}
new ET_Builder_Column;
