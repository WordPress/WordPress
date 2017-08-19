<?php
/**
 * Add block to WordPress theme customizer
 * @package Travelify
 */
function travelify_options_register_theme_customizer($wp_customize)
{
    require_once(trailingslashit(get_template_directory()) . '/library/panel/travelify-custom-control.php');

    global $travelify_theme_options_settings, $travelify_theme_options_defaults;
    $options = get_option('travelify_theme_options');

    /* Main option Settings Panel */
    $wp_customize->add_panel('travelify_main_options', array(
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Travelify Main Options', 'travelify'),
        'description' => __('Panel to update travelify theme options', 'travelify'), // Include html tags such as <p>.
        'priority' => 10 // Mixed with top-level-section hierarchy.
    ));

    /* Travelify Header Area */
    $wp_customize->add_section('travelify_menu_options', array(
        'title' => __('Travelify Header Area', 'travelify'),
        'description' => sprintf(__('Use the following settings change color for menu and website title', 'travelify')),
        'priority' => 31,
        'panel' => 'travelify_main_options'
    ));
        $wp_customize->add_setting('travelify_logo_color', array(
            'default' => '#57ad68',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_logo_color', array(
            'label' => __('Website Title Color', 'travelify'),
            'section' => 'travelify_menu_options',
            'settings' => 'travelify_logo_color',
            'priority' => 1
        )));

        $wp_customize->add_setting('travelify_logo_hover_color', array(
            'default' => '#439f55',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_logo_hover_color', array(
            'label' => __('Website Title Hover Color', 'travelify'),
            'section' => 'travelify_menu_options',
            'settings' => 'travelify_logo_hover_color',
            'priority' => 2
        )));

        $wp_customize->add_setting('travelify_menu_color', array(
            'default' => '#57ad68',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_menu_color', array(
            'label' => __('Menu Bar Color', 'travelify'),
            'section' => 'travelify_menu_options',
            'settings' => 'travelify_menu_color'
        )));

        $wp_customize->add_setting('travelify_menu_hover_color', array(
            'default' => '#439f55',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_menu_hover_color', array(
            'label' => __('Menu Bar Hover Color', 'travelify'),
            'section' => 'travelify_menu_options',
            'settings' => 'travelify_menu_hover_color'
        )));

        $wp_customize->add_setting('travelify_menu_item_color', array(
            'default' => '#FFF',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_menu_item_color', array(
            'label' => __('Menu Item Text Color', 'travelify'),
            'section' => 'travelify_menu_options',
            'settings' => 'travelify_menu_item_color',
            'priority' => 3
        )));

        $wp_customize->add_setting('travelify_social_color', array(
            'default' => '#D0D0D0',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_social_color', array(
            'label' => __('Social Icon Color', 'travelify'),
            'section' => 'travelify_menu_options',
            'settings' => 'travelify_social_color',
            'priority' => 13
        )));

    /* Travelify Element Color */
    $wp_customize->add_section('travelify_element_options', array(
        'title' => __('Travelify Element Color', 'travelify'),
        'description' => sprintf(__('Use the following settings change color for website elements', 'travelify')),
        'priority' => 32,
        'panel' => 'travelify_main_options'
    ));
        $wp_customize->add_setting('travelify_element_color', array(
            'default' => '#57ad68',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_element_color', array(
            'label' => __('Element Color', 'travelify'),
            'section' => 'travelify_element_options',
            'settings' => 'travelify_element_color',
            'priority' => 4
        )));

        $wp_customize->add_setting('travelify_element_hover_color', array(
            'default' => '#439f55',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_element_hover_color', array(
            'label' => __('Element Hover Color', 'travelify'),
            'section' => 'travelify_element_options',
            'settings' => 'travelify_element_hover_color',
            'priority' => 5
        )));

        $wp_customize->add_setting('travelify_wrapper_color', array(
            'default' => '#F8F8F8',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_wrapper_color', array(
            'label' => __('Wrapper Color', 'travelify'),
            'section' => 'travelify_element_options',
            'settings' => 'travelify_wrapper_color',
            'priority' => 6
        )));

        $wp_customize->add_setting('travelify_content_bg_color', array(
            'default' => '#FFF',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_content_bg_color', array(
            'label' => __('Content Background Color', 'travelify'),
            'section' => 'travelify_element_options',
            'settings' => 'travelify_content_bg_color',
            'priority' => 7
        )));

    /* Travelify Typography Color */
    $wp_customize->add_section('travelify_typography_options', array(
        'title' => __('Travelify Typography Color', 'travelify'),
        'description' => sprintf(__('Use the following settings change color for typography such as links, headings and content', 'travelify')),
        'priority' => 33,
        'panel' => 'travelify_main_options'
    ));
        $wp_customize->add_setting('travelify_entry_color', array(
            'default' => '#444',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_entry_color', array(
            'label' => __('Entry Content Color', 'travelify'),
            'section' => 'travelify_typography_options',
            'settings' => 'travelify_entry_color'
        )));

        $wp_customize->add_setting('travelify_header_color', array(
            'default' => '#444',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_header_color', array(
            'label' => __('Header/Title Color', 'travelify'),
            'section' => 'travelify_typography_options',
            'settings' => 'travelify_header_color',
            'priority' => 8
        )));

        $wp_customize->add_setting('travelify_link_color', array(
            'default' => '#57ad68',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_link_color', array(
            'label' => __('Link Color', 'travelify'),
            'section' => 'travelify_typography_options',
            'settings' => 'travelify_link_color',
            'priority' => 11
        )));

        $wp_customize->add_setting('travelify_link_hover_color', array(
            'default' => '#439f55',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'travelify_link_hover_color', array(
            'label' => __('Link Hover Color', 'travelify'),
            'section' => 'travelify_typography_options',
            'settings' => 'travelify_link_hover_color',
            'priority' => 12
        )));

    /* Travelify Footer Section */
    $wp_customize->add_section('travelify_footer_options', array(
        'title' => __('Travelify Footer', 'travelify'),
        'description' => sprintf(__('Use the following settings customize Footer', 'travelify')),
        'priority' => 34,
        'panel' => 'travelify_main_options'
    ));
        $wp_customize->add_setting('travelify_footer_textbox', array(
            'default' => 'Default footer text',
            'sanitize_callback' => 'travelify_sanitize_hexcolor'
        ));
        $wp_customize->add_control('travelify_footer_textbox', array(
            'label' => __('Copyright text', 'travelify'),
            'section' => 'travelify_footer_options',
            'type' => 'text'
        ));

    /* Header Options */
    $wp_customize->add_section('travelify_header_options', array(
        'priority' => 10,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Header Options', 'travelify'),
        'description' => __('Section to update theme options for header', 'travelify'),
        'panel' => 'travelify_main_options'
    ));
        $wp_customize->add_setting('travelify_theme_options[header_logo]', array(
            'default' => $travelify_theme_options_defaults['header_logo'],
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'esc_url_raw'
        ));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'travelify_theme_options[header_logo]', array(
            'label' => __('Header Logo', 'travelify'),
            'section' => 'travelify_header_options',
            'mime_type' => 'image',
            'settings' => 'travelify_theme_options[header_logo]'
        )));

        $wp_customize->add_setting('travelify_theme_options[header_show]', array(
            'default' => $travelify_theme_options_defaults['header_show'],
            'type' => 'option',
            'sanitize_callback' => 'travelify_sanitize_radio_header'
        ));
        $wp_customize->add_control('travelify_theme_options[header_show]', array(
            'type' => 'radio',
            'label' => __('Show', 'travelify'),
            'section' => 'travelify_header_options',
            'choices' => array(
                'header-logo' => __('Header Logo Only', 'travelify'),
                'header-text' => __('Header Text Only', 'travelify'),
                'disable-both' => __('Disable', 'travelify')
            )
        ));

    /* Layout Options */
    $wp_customize->add_section('travelify_layout_options', array(
        'priority' => 30,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Layout Options', 'travelify'),
        'panel' => 'travelify_main_options'
    ));
        $wp_customize->add_setting('travelify_theme_options[default_layout]', array(
            'default' => $travelify_theme_options_defaults['default_layout'],
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'travelify_sanitize_radio_layout'
        ));
        $wp_customize->add_control(new Travelify_Layout_Picker_Custom_Control($wp_customize, 'travelify_theme_options[default_layout]', array(
            'description' => __('This will set the default layout style. However, you can choose different layout for each page via editor', 'travelify'),
            'section' => 'travelify_layout_options',
            'type' => 'radio-image',
            'settings' => 'travelify_theme_options[default_layout]',
            'choices' => array(
                'no-sidebar' => __('No Sidebar', 'travelify'),
                'no-sidebar-full-width' => __('No Sidebar, Full Width', 'travelify'),
                'no-sidebar-one-column' => __('No Sidebar, One Column', 'travelify'),
                'left-sidebar' => __('Left Sidebar', 'travelify'),
                'right-sidebar' => __('Right Sidebar', 'travelify')
            )
        )));

        $wp_customize->add_setting('travelify_theme_options[reset_layout]', array(
            'default' => $travelify_theme_options_defaults['reset_layout'],
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'travelify_sanitize_checkbox'
        ));
        $wp_customize->add_control('travelify_theme_options[reset_layout]', array(
            'label' => __('Check to reset Layout', 'travelify'),
            'section' => 'travelify_layout_options',
            'type' => 'checkbox',
            'settings' => 'travelify_theme_options[reset_layout]'
        ));

    /* RSS URL */
    $wp_customize->add_section('travelify_rss_options', array(
        'priority' => 50,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('RSS URL', 'travelify'),
        'description' => __('Enter your preferred RSS URL. (Feedburner or other)', 'travelify'),
        'panel' => 'travelify_main_options'
    ));
        $wp_customize->add_setting('travelify_theme_options[feed_url]', array(
            'default' => $travelify_theme_options_defaults['feed_url'],
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'esc_url_raw'
        ));
        $wp_customize->add_control('travelify_theme_options[feed_url]', array(
            'label' => __('Feed Redirect URL', 'travelify'),
            'section' => 'travelify_rss_options'
        ));

    /* Homepage Post Options */
    $wp_customize->add_section('travelify_homepage_post_options', array(
        'priority' => 60,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Homepage Post Options', 'travelify'),
        'panel' => 'travelify_main_options'
    ));
        $wp_customize->add_setting('travelify_theme_options[front_page_category]', array(
            'default' => $travelify_theme_options_defaults['front_page_category'],
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'travelify_sanitize_multiselect'
        ));
        $wp_customize->add_control(new tavelify_Customize_Control_Multi_Select_Category($wp_customize, 'travelify_theme_options[front_page_category]', array(
            'description' => __('You may select multiple categories by holding down the CTRL (Windows) or cmd (Mac).', 'travelify'),
            'section' => 'travelify_homepage_post_options',
            'priority' => 10,
            'type' => 'multi-select-cat'
        )));

    /* Featured Slider Settings Panel */
    $wp_customize->add_panel('travelify_slider_options', array(
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Travelify Featured Slider', 'travelify'),
        'description' => __('Panel to update travelify theme options', 'travelify'), // Include html tags such as <p>.
        'priority' => 15 // Mixed with top-level-section hierarchy.
    ));
    $wp_customize->add_section('travelify_post_slider_options', array(
        'priority' => 60,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Featured Post/Page Slider Options', 'travelify'),
        'panel' => 'travelify_slider_options'
    ));
        $wp_customize->add_setting('travelify_theme_options[exclude_slider_post]', array(
            'default' => 0,
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'travelify_sanitize_checkbox'
        ));
        $wp_customize->add_control('travelify_theme_options[exclude_slider_post]', array(
            'label' => __('Check to exclude slider post from Homepage posts', 'travelify'),
            'section' => 'travelify_post_slider_options',
            'type' => 'checkbox',
            'settings' => 'travelify_theme_options[exclude_slider_post]'
        ));

        $wp_customize->add_setting( 'travelify_theme_options[featured_post_slider]', array(
            'default' => $travelify_theme_options_defaults['featured_post_slider'],
            'type'    => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'travelify_sanitize_slider',
            'transport' => 'postMessage'
        ) );
        $wp_customize->add_control(
        new Travelify_Featured_Slider_Custom_Control(
        $wp_customize,
            'travelify_theme_options[featured_post_slider]', array(
            'label' => __( 'Number of slides', 'travelify' ),
            'section' => 'travelify_post_slider_options',
            'settings'    => 'travelify_theme_options[featured_post_slider]',
            'type'  => 'featured-slider'
        )
        ));

    $wp_customize->add_section('travelify_slide_effect_options', array(
        'priority' => 60,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Slider Options', 'travelify'),
        'panel' => 'travelify_slider_options'
    ));
        $wp_customize->add_setting('travelify_theme_options[disable_slider]', array(
            'default' => 0,
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'travelify_sanitize_checkbox'
        ));
        $wp_customize->add_control('travelify_theme_options[disable_slider]', array(
            'label' => __('Check to disable Slider', 'travelify'),
            'section' => 'travelify_slide_effect_options',
            'type' => 'checkbox',
            'settings' => 'travelify_theme_options[disable_slider]'
        ));

        $wp_customize->add_setting('travelify_theme_options[transition_effect]', array(
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'travelify_sanitize_nohtml'
        ));
        $wp_customize->add_control('travelify_theme_options[transition_effect]', array(
            'label' => __('Transition Effect', 'travelify'),
            'section' => 'travelify_slide_effect_options',
            'type'    => 'select',
            'choices'    => array(
                'fade'          =>  'fade',
                'wipe'          =>  'wipe',
                'scrollUp'      =>  'scrollUp',
                'scrollDown'    =>  'scrollDown',
                'scrollLeft'    =>  'scrollLeft',
                'scrollRight'   =>  'scrollRight',
                'blindX'        =>  'blindX',
                'blindY'        =>  'blindY',
                'blindZ'        =>  'blindZ',
                'cover'         =>  'cover',
                'shuffle'       =>  'shuffle'
            ),
        ));

        $wp_customize->add_setting('travelify_theme_options[transition_delay]', array(
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'travelify_sanitize_number'
        ));
        $wp_customize->add_control('travelify_theme_options[transition_delay]', array(
            'label' => __('Transition delay( in second(s) )', 'travelify'),
            'section' => 'travelify_slide_effect_options',
        ));

        $wp_customize->add_setting('travelify_theme_options[transition_duration]', array(
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'travelify_sanitize_number'
        ));
        $wp_customize->add_control('travelify_theme_options[transition_duration]', array(
            'label' => __('Transition length (in second(s))', 'travelify'),
            'section' => 'travelify_slide_effect_options',
        ));

    /* Social Links Settings Panel */
    $wp_customize->add_section('travelify_social_url_options', array(
        'priority' => 17,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Travelify Social Links', 'travelify'),
        'description' => __('Enter URLs for your social networks e.g.', 'travelify') . 'https://twitter.com/colorlib'
    ));

        $social_links = array( 'Facebook' => 'social_facebook', 'Twitter' => 'social_twitter', 'Google-Plus' => 'social_googleplus', 'Pinterest' => 'social_pinterest', 'YouTube' => 'social_youtube', 'Vimeo' => 'social_vimeo', 'LinkedIn' => 'social_linkedin', 'Flickr' => 'social_flickr', 'Tumblr' => 'social_tumblr', 'Instagram' => 'social_instagram', 'RSS' => 'social_rss', 'GitHub' => 'social_github' );
        foreach ($social_links as $key => $val) {

            $wp_customize->add_setting('travelify_theme_options[' . $val . ']', array(
                'default' => '',
                'type' => 'option',
                'capability' => 'edit_theme_options',
                'transport' => 'postMessage',
                'sanitize_callback' => 'esc_url_raw'
            ));
            $wp_customize->add_control('travelify_theme_options[' . $val . ']', array(
                'label' => sprintf(__('%s', 'travelify'), $key),
                'section' => 'travelify_social_url_options',
                'settings' => 'travelify_theme_options[' . $val . ']',
                'type' => 'text'
            ));
        }

    /* Other options Section */
    $wp_customize->add_section('travelify_others_options', array(
        'priority' => 19,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Travelify Other Options', 'travelify'),
        'description' => __('Enter your custom CSS styles.', 'travelify')
    ));
        $wp_customize->add_setting('travelify_theme_options[custom_css]', array(
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'travelify_sanitize_strip_slashes'
        ));
        $wp_customize->add_control('travelify_theme_options[custom_css]', array(
            'label' => __('This CSS will overwrite the CSS of style.css file.', 'travelify'),
            'section' => 'travelify_others_options',
            'settings' => 'travelify_theme_options[custom_css]',
            'type' => 'textarea'
        ));

    $wp_customize->add_section('travelify_important_links', array(
        'priority' => 6,
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Travelify Important Links', 'travelify'),
    ));
        $wp_customize->add_setting('travelify_theme_options[imp_links]', array(
          'capability' => 'edit_theme_options',
          'sanitize_callback' => 'esc_url_raw'
       ));
        $wp_customize->add_control(
        new Travelify_Important_Links(
        $wp_customize,
            'travelify_theme_options[imp_links]', array(
            'section' => 'travelify_important_links',
            'type' => 'travelify-important-links'
        )));

    $wp_customize->get_setting('travelify_menu_color')->transport       = 'postMessage';
    $wp_customize->get_setting('travelify_menu_hover_color')->transport = 'postMessage';
    $wp_customize->get_setting('travelify_entry_color')->transport      = 'postMessage';
    $wp_customize->get_setting('travelify_element_color')->transport    = 'postMessage';
    $wp_customize->get_setting('travelify_logo_color')->transport       = 'postMessage';
    $wp_customize->get_setting('travelify_header_color')->transport     = 'postMessage';
    $wp_customize->get_setting('travelify_wrapper_color')->transport    = 'postMessage';
    $wp_customize->get_setting('travelify_content_bg_color')->transport = 'postMessage';
    $wp_customize->get_setting('travelify_menu_item_color')->transport  = 'postMessage';
    $wp_customize->get_setting('travelify_theme_options[header_logo]')->transport  = 'postMessage';
    $wp_customize->get_setting('travelify_theme_options[header_show]')->transport  = 'postMessage';
    $wp_customize->get_setting('travelify_theme_options[default_layout]')->transport  = 'postMessage';
}

/**
 * Adds sanitization callback function: colors
 * @package Travelify
 */
function travelify_sanitize_hexcolor($color) {
    if ($unhashed = sanitize_hex_color_no_hash($color))
        return '#' . $unhashed;
    return $color;
}

/**
 * Adds sanitization callback function: text
 * @package Travelify
 */
function travelify_sanitize_text($input) {
    return wp_kses_post(force_balance_tags($input));
}

/**
 * Adds sanitization callback function: Number
 * @package Travelify
 */
function travelify_sanitize_number($input) {
    if ( isset( $input ) && is_numeric( $input ) ) {
        return $input;
    }
}

/**
 * Adds sanitization callback function: Strip Slashes
 * @package Travelify
 */
function travelify_sanitize_strip_slashes($input) {
    return wp_kses_stripslashes($input);
}

/**
 * Adds sanitization callback function: Nohtml
 * @package Travelify
 */
function travelify_sanitize_nohtml($input) {
    return wp_filter_nohtml_kses($input);
}

/**
 * Adds sanitization callback function: Checkbox
 * @package Travelify
 */
function travelify_sanitize_checkbox( $input ) {
    $output = ( $input ) ? '1' : false;
    return $output;
}

/**
 * Adds sanitization callback function: Radio Header
 * @package Travelify
 */
function travelify_sanitize_radio_header( $input ) {
    $valid = array( 'header-logo' => 'Header Logo Only','header-text' => 'Header Text Only','disable-both' => 'Disable' );
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}

/**
 * Adds sanitization callback function: Radio Layout
 * @package Travelify
 */
function travelify_sanitize_radio_layout( $input ) {
    $valid = array( 'no-sidebar' => 'No Sidebar','no-sidebar-full-width' => 'No Sidebar, Full Width', 'no-sidebar-one-column' => 'No Sidebar, One Column', 'left-sidebar' => 'Left Sidebar', 'right-sidebar' => 'Right Sidebar' ); 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}

/**
 * Adds sanitization callback function: Multiselect
 * @package Travelify
 */
function travelify_sanitize_multiselect( $values ) {
    $multi_values = !is_array( $values ) ? explode( ',', $values ) : $values;
    return !empty( $multi_values ) ? array_map( 'travelify_sanitize_text', $multi_values ) : array();
}

/**
 * Adds sanitization callback function: Custom Slider
 * @package Travelify
 */
function travelify_sanitize_slider( $values ) {
    $output = array();
    $slider_values = !is_array( $values ) ? json_decode( $values ) : $values;
    if( !empty( $slider_values ) ){
        $i = 1;
        foreach( $slider_values as $val ){
            if( is_numeric( $val ) && !empty( $val ) ) {
                   $output[$i] = $val;
                   $i++;
            }
        }
    }
    return $output;
}

/**
 * Output custom CSS in theme header
 * @package Travelify
 */
function travelify_customizer_css() {
    ?>
    <style type="text/css">
        a { color: <?php echo get_theme_mod('travelify_link_color', '#57AD68'); ?>; }
        #site-title a { color: <?php echo get_theme_mod('travelify_logo_color'); ?>; }
        #site-title a:hover { color: <?php echo get_theme_mod('travelify_logo_hover_color'); ?>; }
        .wrapper { background: <?php echo get_theme_mod('travelify_wrapper_color', '#F8F8F8'); ?>; }
        .social-icons ul li a { color: <?php echo get_theme_mod('travelify_social_color', '#d0d0d0'); ?>; }
        #main-nav a, #main-nav a:hover,  #main-nav a:focus, #main-nav ul li.current-menu-item a,#main-nav ul li.current_page_ancestor a,#main-nav ul li.current-menu-ancestor a,#main-nav ul li.current_page_item a,#main-nav ul li:hover > a { color: <?php echo get_theme_mod('travelify_menu_item_color', '#fff'); ?>; }
        .widget, article { background: <?php echo get_theme_mod('travelify_content_bg_color', '#fff'); ?>; }
        .entry-title, .entry-title a, .entry-title a:focus, h1, h2, h3, h4, h5, h6, .widget-title  { color: <?php echo get_theme_mod('travelify_header_color', '#1b1e1f'); ?>; }
        a:focus, a:active, a:hover, .tags a:hover, .custom-gallery-title a, .widget-title a, #content ul a:hover,#content ol a:hover, .widget ul li a:hover, .entry-title a:hover, .entry-meta a:hover, #site-generator .copyright a:hover { color: <?php echo get_theme_mod('travelify_link_hover_color', '#439f55'); ?>; }
        #main-nav { background: <?php echo get_theme_mod('travelify_menu_color', '#57ad68'); ?>; border-color: <?php echo get_theme_mod('travelify_menu_color', '#57ad68'); ?>; }
        #main-nav ul li ul, body { border-color: <?php echo get_theme_mod('travelify_menu_color', '#439f55'); ?>; }
        #main-nav a:hover,#main-nav ul li.current-menu-item a,#main-nav ul li.current_page_ancestor a,#main-nav ul li.current-menu-ancestor a,#main-nav ul li.current_page_item a,#main-nav ul li:hover > a, #main-nav li:hover > a,#main-nav ul ul :hover > a,#main-nav a:focus { background: <?php echo get_theme_mod('travelify_menu_hover_color', '#439f55'); ?>; }
        #main-nav ul li ul li a:hover,#main-nav ul li ul li:hover > a,#main-nav ul li.current-menu-item ul li a:hover { color: <?php echo get_theme_mod('travelify_menu_hover_color', '#439f55'); ?>; }
        .entry-content { color: <?php echo get_theme_mod('travelify_entry_color', '#1D1D1D'); ?>; }
        input[type="reset"], input[type="button"], input[type="submit"], .entry-meta-bar .readmore, #controllers a:hover, #controllers a.active, .pagination span, .pagination a:hover span, .wp-pagenavi .current, .wp-pagenavi a:hover { background: <?php echo get_theme_mod('travelify_element_color', '#57ad68'); ?>; border-color: <?php echo get_theme_mod('travelify_element_color', '#57ad68'); ?> !important; }
        ::selection { background: <?php echo get_theme_mod('travelify_element_color', '#57ad68'); ?>; }
        blockquote { border-color: <?php echo get_theme_mod('travelify_element_color', '#439f55'); ?>; }
        #controllers a:hover, #controllers a.active { color: <?php echo get_theme_mod('travelify_element_color', ' #439f55'); ?>; }
        input[type="reset"]:hover,input[type="button"]:hover,input[type="submit"]:hover,input[type="reset"]:active,input[type="button"]:active,input[type="submit"]:active, .entry-meta-bar .readmore:hover, .entry-meta-bar .readmore:active, ul.default-wp-page li a:hover, ul.default-wp-page li a:active { background: <?php echo get_theme_mod('travelify_element_hover_color', '#439f55'); ?>; border-color: <?php echo get_theme_mod('travelify_element_hover_color', '#439f55'); ?>; }
    </style>
    <?php
}


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 * @package Travelify
 */
function travelify_customize_preview_js() {
    wp_enqueue_script('travelify_customizer', get_template_directory_uri() . '/library/js/customizer.js', array('customize-preview'), '20151005', true);
}


/**
 * Validate all theme options values
 *
 * @uses esc_url_raw, absint, esc_textarea, sanitize_text_field, travelify_invalidate_caches
 */
function travelify_theme_options_validate( $options ) {
	global $travelify_theme_options_settings, $travelify_theme_options_defaults;
	$input_validated = $travelify_theme_options_settings;
	$input = array();
	$input = $options;
        $input_validated = $input;

   	if ( isset( $input[ 'featured_post_slider' ] ) ) {

            $slide_count = count( $input[ 'featured_post_slider' ] );

            // Slider settings updation
            $input_validated[ 'slider_quantity' ] = $slide_count > 0 ? $slide_count : 3;
        }

	// Layout settings verification
	if (isset($input['reset_layout'])) {
            $input_validated['reset_layout'] = 0;
        }
        if (0 == $input['reset_layout']) {
            if (isset($input['default_layout'])) {
                $input_validated['default_layout'] = $input['default_layout'];
            }
        } else {
            $input_validated['default_layout'] = $travelify_theme_options_defaults['default_layout'];
        }

        //Clearing the theme option cache
	    if( function_exists( 'travelify_themeoption_invalidate_caches' ) ) travelify_themeoption_invalidate_caches();

   return $input_validated;
}

/**
 * Clearing the cache if any changes in Customizer
 */
function travelify_themeoption_invalidate_caches(){
	delete_transient( 'travelify_featured_post_slider' );
	delete_transient( 'travelify_socialnetworks' );
	delete_transient( 'travelify_footercode' );
	delete_transient( 'travelify_internal_css' );
	delete_transient( 'travelify_headercode' );
}

/**
 * Clearing the cache if any changes in post or page
 */
function travelify_post_invalidate_caches(){
   delete_transient( 'travelify_featured_post_slider' );
}

/**
 * Register options and validation callbacks
 *
 * @uses register_setting
 */
function travelify_register_settings() {
   register_setting( 'travelify_theme_options', 'travelify_theme_options', 'travelify_theme_options_validate' );
}

add_action('customize_register', 'travelify_options_register_theme_customizer');
add_action('wp_head', 'travelify_customizer_css');
add_action('customize_preview_init', 'travelify_customize_preview_js');
add_action( 'admin_init', 'travelify_register_settings' );
add_action( 'save_post', 'travelify_post_invalidate_caches' );
?>