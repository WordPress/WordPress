<?php
/**
 * Initialize the options before anything else. 
 */
add_action( 'admin_init', '_custom_theme_options', 1 );

/**
 * Theme Mode demo code of all the available option types.
 *
 * @return    void
 *
 * @access    private
 * @since     2.0
 */
function _custom_theme_options() {
  
  /**
   * Get a copy of the saved settings array. 
   */
  $saved_settings = get_option( 'option_tree_settings', array() );
  
  /**
   * Create a custom settings array that we pass to 
   * the OptionTree Settings API Class.
   */
  $custom_settings = array(
    'contextual_help' => array(
      'content'       => array( 
        array(
          'id'        => 'general_help',
          'title'     => 'General',
          'content'   => '<p>Help content goes here!</p>'
        )
      ),
      'sidebar'       => '<p>Sidebar content goes here!</p>'
    ),
    'sections'        => array(
      array(
        'title'       => 'General',
        'id'          => 'general_default'
      ),
      array(
        'title'       => 'Miscellaneous ',
        'id'          => 'miscellaneous'
      )
    ),
    'settings'        => array(
      array(
        'label'       => 'Background',
        'id'          => 'my_background',
        'type'        => 'background',
        'desc'        => 'BlahLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Category Checkbox',
        'id'          => 'my_category_checkbox',
        'type'        => 'category-checkbox',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Category Select',
        'id'          => 'my_category_select',
        'type'        => 'category-select',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Checkbox',
        'id'          => 'my_checkbox',
        'type'        => 'checkbox',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'choices'     => array(
          array (
            'label'       => 'Yes',
            'value'       => 'Yes'
          )
        ),
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Colorpicker',
        'id'          => 'my_colorpicker',
        'type'        => 'colorpicker',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'CSS',
        'id'          => 'my_css',
        'type'        => 'css',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '20',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Custom Post Type Checkbox',
        'id'          => 'my_custom_post_type_checkbox',
        'type'        => 'custom-post-type-checkbox',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => 'post',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Custom Post Type Select',
        'id'          => 'my_custom_post_type_select',
        'type'        => 'custom-post-type-select',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => 'post',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'List Item',
        'id'          => 'my_list_item',
        'type'        => 'list-item',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'settings'    => array(
          array(
            'label'       => 'Upload',
            'id'          => 'my_list_item_upload',
            'type'        => 'upload',
            'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'std'         => '',
            'rows'        => '',
            'post_type'   => '',
            'taxonomy'    => '',
            'class'       => ''
          ),
          array(
            'label'       => 'Text',
            'id'          => 'my_list_item_text',
            'type'        => 'text',
            'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'std'         => '',
            'rows'        => '',
            'post_type'   => '',
            'taxonomy'    => '',
            'class'       => ''
          ),
          array(
            'label'       => 'Textarea Simple',
            'id'          => 'my_list_item_textarea_simple',
            'type'        => 'textarea-simple',
            'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'std'         => '',
            'rows'        => '10',
            'post_type'   => '',
            'taxonomy'    => '',
            'class'       => ''
          )
        ),
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Measurement',
        'id'          => 'my_measurement',
        'type'        => 'measurement',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Page Checkbox',
        'id'          => 'my_page_checkbox',
        'type'        => 'page-checkbox',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Page Select',
        'id'          => 'my_page_select',
        'type'        => 'page-select',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Post Checkbox',
        'id'          => 'my_post_checkbox',
        'type'        => 'post-checkbox',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Post Select',
        'id'          => 'my_post_select',
        'type'        => 'post-select',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'general_default'
      ),
      array(
        'label'       => 'Radio',
        'id'          => 'my_radio',
        'type'        => 'radio',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'choices'     => array(
          array(
            'label'       => 'Yes',
            'value'       => 'yes'
          ),
          array(
            'label'       => 'No',
            'value'       => 'no'
          ), 
          array(
            'label'       => 'Maybe',
            'value'       => 'maybe'
          )
        ),
        'std'         => 'yes',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Radio Image',
        'id'          => 'my_radio_image',
        'type'        => 'radio-image',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => 'right-sidebar',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ), 
      array(
        'label'       => 'Select',
        'id'          => 'my_select',
        'type'        => 'select',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'choices'     => array(
          array(
            'label'       => 'Yes',
            'value'       => 'yes'
          ),
          array(
            'label'       => 'No',
            'value'       => 'no'
          ),
          array(
            'label'       => 'Maybe',
            'value'       => 'maybe'
          )
        ),
        'std'         => 'maybe',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Slider',
        'id'          => 'my_slider',
        'type'        => 'slider',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Tag Checkbox',
        'id'          => 'my_tag_checkbox',
        'type'        => 'tag-checkbox',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Tag Select',
        'id'          => 'my_tag_select',
        'type'        => 'tag-select',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Taxonomy Checkbox',
        'id'          => 'my_taxonomy_checkbox',
        'type'        => 'taxonomy-checkbox',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => 'category,post_tag',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Taxonomy Select',
        'id'          => 'my_taxonomy_select',
        'type'        => 'taxonomy-select',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => 'category,post_tag',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Text',
        'id'          => 'my_text',
        'type'        => 'text',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Textarea',
        'id'          => 'my_textarea',
        'type'        => 'textarea',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '15',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Textarea Simple',
        'id'          => 'my_textarea_simple',
        'type'        => 'textarea-simple',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '10',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Textblock',
        'id'          => 'my_textblock',
        'type'        => 'textblock',
        'desc'        => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Textblock Titled',
        'id'          => 'my_textblock_titled',
        'type'        => 'textblock-titled',
        'desc'        => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Typography',
        'id'          => 'my_typography',
        'type'        => 'typography',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      ),
      array(
        'label'       => 'Upload',
        'id'          => 'my_upload',
        'type'        => 'upload',
        'desc'        => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'std'         => '',
        'rows'        => '',
        'post_type'   => '',
        'taxonomy'    => '',
        'class'       => '',
        'section'     => 'miscellaneous'
      )
    )
  );
  
  /* allow settings to be filtered before saving */
  $custom_settings = apply_filters( 'option_tree_settings_args', $custom_settings );
  
  /* settings are not the same update the DB */
  if ( $saved_settings !== $custom_settings ) {
    update_option( 'option_tree_settings', $custom_settings ); 
  }
  
}