<?php

class ET_Builder_Module_Panel extends ET_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Panel', 'et_builder' );
		$this->slug = 'et_pb_ca_panel';
		
		$this->whitelisted_fields = array(
			'panel_layout',
			'icon',
			'title',
			'content_new',
		);
		
		$this->fields_defaults = array(
			'panel_layout' => array( 'default' ),
		);

		$this->main_css_element = '%%order_class%%';
		
		
	}

	function get_fields() {
		$fields = array(
			'panel_layout' => array(
				'label'             => esc_html__( 'Panel Style','et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'none' => esc_html__( 'None','et_builder'),
					'default' => esc_html__( 'Default','et_builder'),
					'standout'  => esc_html__( 'Standout','et_builder'),
					'standout highlight'  => esc_html__( 'Standout Highlight','et_builder'),
				),
				'description'       => esc_html__( 'Here you can choose the style of panel to display','et_builder' ),
			),			
			'title' => array(
				'label'           => esc_html__( 'Heading','et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can enter a Heading Title.','et_builder' ),
			),		
			'icon' => array(
				'label'           => esc_html__( 'Heading Icon','et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can enter a Heading Icon. Note: Not all icons may be available in Version 4.','et_builder' ),
			),					
			'content_new' => array(
				'label'           => esc_html__( 'Content','et_builder'),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the content that will be used within the module.','et_builder' ),
			),
		);
		return $fields;
	}
	function shortcode_callback( $atts, $content = null, $function_name ) {
		$icon               = $this->shortcode_atts['icon'];
		$panel_layout    = $this->shortcode_atts['panel_layout'];
		$title    = $this->shortcode_atts['title'];
		
	
		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
		
		$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );
		
		$class = $panel_layout ;
					
		$output = sprintf(
			'<div class="et_pb_panel panel panel-' . $class . '">
			<h2 class="panel-heading"><span class="ca-gov-icon-' . $icon . '"></span>' . $title . '</h2>
				<div class="panel-body">
					%1$s
				</div>
			</div> <!-- .et_pb_panel -->',
			$this->shortcode_content
		);

		return $output;
	}	
}

new ET_Builder_Module_Panel;

class ET_Builder_Module_Header_Banner extends ET_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Header Banner', 'et_builder' );
		$this->slug = 'et_pb_ca_banner';		$this->child_slug      = 'et_pb_ca_banner_item';		$this->child_item_text = esc_html__( 'Slide', 'et_builder' );
		
		$this->whitelisted_fields = array(
			'banner_style',
		);

		$this->fields_defaults = array(
			'banner_style' => array( 'slideshow' ),					);

		$this->main_css_element = '%%order_class%%.et_pb_slider';
				
	}

	function get_fields() {
		$fields = array();
		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_hide_mobile, 
		$et_pb_slider_custom_icon, $et_pb_slider_item_num;

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
		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );		
		
		$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );
		
		$output = sprintf(
			'<div id="et_pb_ca_banner" class="header-slideshow-banner">
				<div id="primary-carousel" class="carousel carousel-banner">
					%1$s							
				</div>
				<div class="explore-invite">
					<div class="text-center">
						<a href="">
						<span class="explore-title">Explore</span>	
						<span class="ca-gov-icon-arrow-down" aria-hidden="true"></span>
						</a>
					</div>	
				</div>
			</div> <!-- .et_pb_ca_banner -->',
			$this->shortcode_content
		);

		return $output;
	}
}

new ET_Builder_Module_Header_Banner;

class ET_Builder_Module_Banner_Item_Slide extends ET_Builder_Module {	
function init() {		

	$this->name = esc_html__( 'Banner Slide', 'et_builder' );		
	$this->slug = 'et_pb_ca_banner_item';			
	$this->type = 'child';				
	$this->child_title_var = 'admin_title';		
	$this->child_title_fallback_var = 'heading';		
	$this->whitelisted_fields = array(		
		'heading',		
		'button_text',		
		'button_link',		
		'background_image',		
		);				
	
	$this->fields_defaults = array(		
	'button_link' => array( '#' ),
	);				
	
	$this->advanced_setting_title_text = esc_html__( 'New Slide', 'et_builder' );
	$this->settings_text = esc_html__( 'Slide Settings', 'et_builder' );
	$this->main_css_element = '%%order_class%%';
}

function get_fields() {
	$fields = array(
		'heading' => array(
		'label' => esc_html__( 'Heading', 'et_builder' ),
		'type' => 'text',
		'option_category' => 'basic_option',
		'description'     => esc_html__( 'Define the title text for your slide.', 'et_builder' ),
		),
		'button_text' => array(
		'label' => esc_html__( 'Button Text', 'et_builder' ),
		'type'=> 'text',
		'option_category' => 'basic_option',
		'description' => esc_html__( 'Define the text for the slide button', 'et_builder' ),
		),
		'button_link' => array(
		'label' => esc_html__( 'Button URL', 'et_builder' ),
		'type' => 'text',
		'option_category' => 'basic_option',
		'description' => esc_html__( 'Input a destination URL for the slide button.', 'et_builder' ),
		),
		'background_image' => array(
				'label' => esc_html__( 'Background Image', 'et_builder' ),
				'type' => 'upload',
				'option_category' => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text' => esc_attr__( 'Choose a Background Image', 'et_builder' ),
				'update_text' => esc_attr__( 'Set As Background', 'et_builder' ),
				'description' => esc_html__( 'If defined, this image will be used as the background for this module. To remove a background image, simply delete the URL from the settings field.', 'et_builder' ),
				),
		);
		return $fields;
}
function shortcode_callback( $atts, $content = null, $function_name ) {
	$heading = $this->shortcode_atts['heading'];
	$button_text = $this->shortcode_atts['button_text'];
	$button_link = $this->shortcode_atts['button_link'];
	$background_image = $this->shortcode_atts['background_image'];
	global $et_pb_slider_item_num;
	$et_pb_slider_item_num++;
	
	$class = ET_Builder_Element::add_module_order_class( $class, $function_name );
	$output = sprintf(
		'<div class="et_pb_ca_banner_item slide" style="background-image:' . " url('" . $background_image . "')" . ';">
				<a href="' . $button_link .'">
				<p class="slide-text"><span class="title">' . $heading . '</span><br>' .
				$button_text . '</p> </a>
		</div> <!-- .et_pb_ca_banner_item -->'
		);
	return $output;
}
}

new ET_Builder_Module_Banner_Item_Slide;


class ET_Builder_Module_Profile_Banner extends ET_Builder_Module {	function init() {			$this->name = esc_html__( 'Profile Banner', 'et_builder' );	$this->slug = 'et_pb_ca_profile_banner';	$this->whitelisted_fields = array(				'entity',				'name',				'link',				'image',				);		$this->fields_defaults = array(				'link' => array( '#' ),					);				$this->main_css_element = '%%order_class%%';		}function get_fields() {		$fields = array(	'entity' => array(	'label' => esc_html__( 'Government Entity Title', 'et_builder' ),	'type' => 'text',	'description' => esc_html__( 'Enter the Government Entity Title.', 'et_builder' ),	),	'name' => array(	'label' => esc_html__( 'Official Name', 'et_builder' ),	'type' => 'text',	'description' => esc_html__( 'Enter the Official Name.', 'et_builder' ),	),	'link' => array( 	'label' => esc_html__( 'Profile Link', 'et_builder' ),	'type' => 'text',	'option_category' => 'basic_option',	'description' => esc_html__( 'Input a destination URL for the profile.', 'et_builder' ),	),	'image' => array(	'label' => esc_html__( 'Profile Image', 'et_builder' ),	'type' => 'upload',	'option_category'    => 'basic_option',	'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),	'choose_text'        => esc_attr__( 'Choose a Profile Image', 'et_builder' ),	'update_text'        => esc_attr__( 'Set As Profile Image', 'et_builder' ),	'description'        => esc_html__( 'This image will be used as the profile image. To remove a profile image, simply delete the URL from the settings field.', 'et_builder' ),	),	);	return $fields;	}function shortcode_callback( $atts, $content = null, $function_name ) {		$entity = $this->shortcode_atts['entity'];		$name = $this->shortcode_atts['name'];		$link = $this->shortcode_atts['link'];		$image = $this->shortcode_atts['image'];				$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );		$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );				$class = " et_pb_module et_pb_bg_layout_{$background_layout} et_pb_text_align_{$text_orientation}";						$output = sprintf(		'<div class="profile-banner">		<div class="inner" style="background:url(' . get_stylesheet_directory() . $image .') no-repeat right bottom">		<div class="banner-subtitle">' . $entity . '</div>		<div class="banner-title">' . $name . '</div>		<div class="banner-link"><a href="' . $link . '">View More</a></div>		</div></div><!-- .et_pb_ca_profile_banner -->');		return $output;		}}new ET_Builder_Module_Profile_Banner;class ET_Builder_Module_Section_Footer extends ET_Builder_Module {	function init() {		$this->name = esc_html__( 'Section Footer', 'et_builder' );		$this->slug = 'et_pb_ca_section_footer';				$this->child_slug      = 'et_pb_ca_footer_group_item';				$this->child_item_text = esc_html__( 'Group', 'et_builder' );						$this->main_css_element = '%%order_class%%.et_pb_ca_section_footer';					}	function get_fields() {		$fields = array(					);		return $fields;	}	function pre_shortcode_content() {		global  $et_pb_ca_footer_group_item_num;		$et_pb_ca_footer_group_item_num = 0;		$parallax                = $this->shortcode_atts['parallax'];		$parallax_method         = $this->shortcode_atts['parallax_method'];		$hide_content_on_mobile  = $this->shortcode_atts['hide_content_on_mobile'];		$hide_cta_on_mobile      = $this->shortcode_atts['hide_cta_on_mobile'];		$button_custom           = $this->shortcode_atts['custom_button'];		$custom_icon             = $this->shortcode_atts['button_icon'];		$et_pb_slider_has_video = false;		$et_pb_slider_parallax = $parallax;		$et_pb_slider_parallax_method = $parallax_method;		$et_pb_slider_hide_mobile = array(			'hide_content_on_mobile'  => $hide_content_on_mobile,			'hide_cta_on_mobile'      => $hide_cta_on_mobile,		);		$et_pb_slider_custom_icon = 'on' === $button_custom ? $custom_icon : '';	}	function shortcode_callback( $atts, $content = null, $function_name ) {	$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );				$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );		$class = $banner_style ;		$output = sprintf(			'<div class="section section-impact">				<div class="container">					<div class="row group">					%1$s									</div>				</div>			</div> <!-- .et_pb_ca_section_footer -->',			$this->shortcode_content		);		return $output;	}}new ET_Builder_Module_Section_Footer;class ET_Builder_Module_Section_Footer_Group extends ET_Builder_Module {	function init() {		$this->name = esc_html__( 'Section Group', 'et_builder' );		$this->slug = 'et_pb_ca_footer_group_item';			$this->type = 'child';				$this->child_title_var = 'admin_title';		$this->child_title_fallback_var = 'heading';		$this->whitelisted_fields = array(		'section_url',		'group_style','heading', 'group_icon',				'url_text1', 'url1',		'url_text2', 'url2',		'url_text3','url3',		'url_text4','url4',		);								$this->advanced_setting_title_text = esc_html__( 'New Group', 'et_builder' );				$this->settings_text = esc_html__( 'Group Settings', 'et_builder' );						$this->fields_defaults = array(			'group_style' => array( 'list-standout' ),				'url1' => array( '#' ),							'url2' => array( '#' ),							'url3' => array( '#' ),							'url4' => array( '#' ),						);		$this->main_css_element = '%%order_class%%';			}		function get_fields() {				$fields = array(				'heading' => array(								'label' => esc_html__( 'Section Heading', 'et_builder' ), 				'type' => 'text',				'option_category' => 'basic_option',				'description'     => esc_html__( 'Define the title for the section.', 'et_builder' ),				),				'section_url' => array(								'label' => esc_html__( 'Section Url', 'et_builder' ), 				'type' => 'text',				'option_category' => 'basic_option',				'description'     => esc_html__( 'Define the URL for the section.', 'et_builder' ),				),				'group_style'         => array(				'label'           => esc_html__( 'Section Style', 'et_builder' ),				'type'            => 'select',				'option_category' => 'configuration',				'options'         => array(					'list-unstyled'  => esc_html__( 'Unstyled', 'et_builder' ),					'list-standout' => esc_html__( 'Standout', 'et_builder' ),								),				'description'     => esc_html__( 'Choose a section style.', 'et_builder' ),				),				'group_icon' => array(				'label' => esc_html__( 'Section Icon', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the icon for this section.', 'et_builder' ),				),				'url_text1' => array(				'label' => esc_html__( 'Title 1', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the text to display.', 'et_builder' ),				),				'url1' => array(				'label' => esc_html__( 'Url 1', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the first URL.', 'et_builder' ),				),				'url_text2' => array(				'label' => esc_html__( 'Title 2', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the text to display.', 'et_builder' ),				),				'url2' => array(				'label' => esc_html__( 'Url 2', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the second URL.', 'et_builder' ),				),				'url_text3' => array(				'label' => esc_html__( 'Title 3', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the text to display.', 'et_builder' ),				),				'url3' => array(				'label' => esc_html__( 'Url 3', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the third URL.', 'et_builder' ),				),				'url_text4' => array(				'label' => esc_html__( 'Title 4', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the text to display.', 'et_builder' ),				),				'url4' => array(				'label' => esc_html__( 'Url 4', 'et_builder' ),				'type'=> 'text',				'option_category' => 'basic_option',				'description' => esc_html__( 'Define the fourth URL.', 'et_builder' ),				),			);					return $fields;			}function shortcode_callback( $atts, $content = null, $function_name ) {		$section_url = $this->shortcode_atts['section_url'];	$group_style = $this->shortcode_atts['group_style'];	$heading = $this->shortcode_atts['heading'];	$group_icon = $this->shortcode_atts['group_icon'];	$url_text1 = $this->shortcode_atts['url_text1'];	$url_text2 = $this->shortcode_atts['url_text2'];	$url_text3 = $this->shortcode_atts['url_text3'];	$url_text4 = $this->shortcode_atts['url_text4'];	$url1 = $this->shortcode_atts['url1'];	$url2 = $this->shortcode_atts['url2'];	$url3 = $this->shortcode_atts['url3'];	$url4 = $this->shortcode_atts['url4'];		global $et_pb_ca_footer_group_item_num;		$et_pb_ca_footer_group_item_num++;		$class = ET_Builder_Element::add_module_order_class( $class, $function_name );		if($group_style == "list-unstyled" && $group_icon != "")		$icon = 'class="ca-gov-icon-' . $group_icon . '"';			$output = 		'<div class="et_pb_ca_footer_group_item quarter">			<h4>' . $heading .'</h4>			<ul class="' . $group_style . '">';				if($group_style == "list-unstyled")					$link_style = ' class="btn btn-default btn-xs"';									if($url_text1 != "")					$output .= '<li><a href="' . $url1 . '" ' . $link_style . '><span ' . $icon . '></span>' . $url_text1 . '</a></li>';				if($url_text2 != "")					$output .= '<li><a href="' . $url2 . '" ' . $link_style . '><span ' . $icon . '></span>' . $url_text2 . '</a></li>';				if($url_text3 != "")					$output .= '<li><a href="' . $url3 . '" ' . $link_style . '><span ' . $icon . '></span>' . $url_text3 . '</a></li>';				if($url_text4 != "")					$output .= '<li><a href="' . $url4 . '" ' . $link_style . '><span ' . $icon . '></span>' . $url_text4 . '</a></li>';									$output .= '<a href="' . $section_url . '" class="btn btn-primary">Read More</a>		</div> <!-- .et_pb_ca_footer_group_item -->';								return $output;			}}new ET_Builder_Module_Section_Footer_Group;
?>