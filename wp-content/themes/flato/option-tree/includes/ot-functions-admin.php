<?php if ( ! defined( 'OT_VERSION' ) ) exit( 'No direct script access allowed' );
/**
 * Functions used only while viewing the admin UI.
 *
 * Limit loading these function only when needed 
 * and not in the front end.
 *
 * @package   OptionTree
 * @author    Derek Herman <derek@valendesigns.com>
 * @copyright Copyright (c) 2013, Derek Herman
 * @since     2.0
 */

/**
 * Registers the Theme Option page
 *
 * @uses      ot_register_settings()
 *
 * @return    void
 *
 * @access    public
 * @since     2.1
 */
if ( ! function_exists( 'ot_register_theme_options_page' ) ) {

  function ot_register_theme_options_page() {
  
    /* get the settings array */
    $get_settings = get_option( 'option_tree_settings' );
    
    /* sections array */
    $sections = isset( $get_settings['sections'] ) ? $get_settings['sections'] : array();
    
    /* settings array */
    $settings = isset( $get_settings['settings'] ) ? $get_settings['settings'] : array();
    
    /* contexual_help array */
    $contextual_help = isset( $get_settings['contextual_help'] ) ? $get_settings['contextual_help'] : array();
    
    /* build the Theme Options */
    if ( function_exists( 'ot_register_settings' ) && OT_USE_THEME_OPTIONS ) {
      
      ot_register_settings( array(
          array(
            'id'                  => 'option_tree',
            'pages'               => array( 
              array(
                'id'              => 'ot_theme_options',
                'parent_slug'     => apply_filters( 'ot_theme_options_parent_slug', 'themes.php' ),
                'page_title'      => apply_filters( 'ot_theme_options_page_title', __( 'Theme Options', 'option-tree' ) ),
                'menu_title'      => apply_filters( 'ot_theme_options_menu_title', __( 'Theme Options', 'option-tree' ) ),
                'capability'      => $caps = apply_filters( 'ot_theme_options_capability', 'edit_theme_options' ),
                'menu_slug'       => apply_filters( 'ot_theme_options_menu_slug', 'ot-theme-options' ),
                'icon_url'        => apply_filters( 'ot_theme_options_icon_url', null ),
                'position'        => apply_filters( 'ot_theme_options_position', null ),
                'updated_message' => apply_filters( 'ot_theme_options_updated_message', __( 'Theme Options updated.', 'option-tree' ) ),
                'reset_message'   => apply_filters( 'ot_theme_options_reset_message', __( 'Theme Options reset.', 'option-tree' ) ),
                'button_text'     => apply_filters( 'ot_theme_options_button_text', __( 'Save Changes', 'option-tree' ) ),
                'screen_icon'     => 'themes',
                'contextual_help' => $contextual_help,
                'sections'        => $sections,
                'settings'        => $settings
              )
            )
          )
        ) 
      );
      
      // Filters the options.php to add the minimum user capabilities.
      add_filter( 'option_page_capability_option_tree', create_function( '$caps', "return '$caps';" ), 999 );
    
    }
  
  }

}

/**
 * Registers the Settings page
 *
 * @uses      ot_register_settings()
 *
 * @return    void
 *
 * @access    public
 * @since     2.1
 */
if ( ! function_exists( 'ot_register_settings_page' ) ) {

  function ot_register_settings_page() {
  
    // Create the filterable pages array
    $ot_register_pages_array =  array( 
      array( 
        'id'              => 'ot',
        'page_title'      => __( 'OptionTree', 'option-tree' ),
        'menu_title'      => __( 'OptionTree', 'option-tree' ),
        'capability'      => 'edit_theme_options',
        'menu_slug'       => 'ot-settings',
        'icon_url'        => OT_URL . '/assets/images/ot-logo-mini.png',
        'position'        => 61,
        'hidden_page'     => true
      ),
      array(
        'id'              => 'settings',
        'parent_slug'     => 'ot-settings',
        'page_title'      => __( 'Settings', 'option-tree' ),
        'menu_title'      => __( 'Settings', 'option-tree' ),
        'capability'      => 'edit_theme_options',
        'menu_slug'       => 'ot-settings',
        'icon_url'        => null,
        'position'        => null,
        'updated_message' => __( 'Theme Options updated.', 'option-tree' ),
        'reset_message'   => __( 'Theme Options reset.', 'option-tree' ),
        'button_text'     => __( 'Save Settings', 'option-tree' ),
        'show_buttons'    => false,
        'screen_icon'     => 'themes',
        'sections'        => array(
          array(
            'id'          => 'create_setting',
            'title'       => __( 'Theme Options UI', 'option-tree' )
          ),
          array(
            'id'          => 'import',
            'title'       => __( 'Import', 'option-tree' )
          ),
          array(
            'id'          => 'export',
            'title'       => __( 'Export', 'option-tree' )
          ),
          array(
            'id'          => 'layouts',
            'title'       => __( 'Layouts', 'option-tree' )
          )
        ),
        'settings'        => array(
          array(
            'id'          => 'theme_options_ui_text',
            'label'       => __( 'Theme Options UI Builder', 'option-tree' ),
            'type'        => 'theme_options_ui',
            'section'     => 'create_setting'
          ),
          array(
            'id'          => 'import_xml_text',
            'label'       => __( 'Settings XML', 'option-tree' ),
            'type'        => 'import-xml',
            'section'     => 'import'
          ),
          array(
            'id'          => 'import_settings_text',
            'label'       => __( 'Settings', 'option-tree' ),
            'type'        => 'import-settings',
            'section'     => 'import'
          ),
          array(
            'id'          => 'import_data_text',
            'label'       => __( 'Theme Options', 'option-tree' ),
            'type'        => 'import-data',
            'section'     => 'import'
          ),
          array(
            'id'          => 'import_layouts_text',
            'label'       => __( 'Layouts', 'option-tree' ),
            'type'        => 'import-layouts',
            'section'     => 'import'
          ),
          array(
            'id'          => 'export_settings_file_text',
            'label'       => __( 'Settings PHP File', 'option-tree' ),
            'type'        => 'export-settings-file',
            'section'     => 'export'
          ),
          array(
            'id'          => 'export_settings_text',
            'label'       => __( 'Settings', 'option-tree' ),
            'type'        => 'export-settings',
            'section'     => 'export'
          ),
          array(
            'id'          => 'export_data_text',
            'label'       => __( 'Theme Options', 'option-tree' ),
            'type'        => 'export-data',
            'section'     => 'export'
          ),
          array(
            'id'          => 'export_layout_text',
            'label'       => __( 'Layouts', 'option-tree' ),
            'type'        => 'export-layouts',
            'section'     => 'export'
          ),
          array(
            'id'          => 'modify_layouts_text',
            'label'       => __( 'Layout Management', 'option-tree' ),
            'type'        => 'modify-layouts',
            'section'     => 'layouts'
          )
        )
      ),
      array(
        'id'              => 'documentation',
        'parent_slug'     => 'ot-settings',
        'page_title'      => __( 'Documentation', 'option-tree' ),
        'menu_title'      => __( 'Documentation', 'option-tree' ),
        'capability'      => 'edit_theme_options',
        'menu_slug'       => 'ot-documentation',
        'icon_url'        => null,
        'position'        => null,
        'updated_message' => __( 'Theme Options updated.', 'option-tree' ),
        'reset_message'   => __( 'Theme Options reset.', 'option-tree' ),
        'button_text'     => __( 'Save Settings', 'option-tree' ),
        'show_buttons'    => false,
        'screen_icon'     => 'themes',
        'sections'        => array(
          array(
            'id'          => 'creating_options',
            'title'       => __( 'Creating Options', 'option-tree' )
          ),
          array(
            'id'          => 'option_types',
            'title'       => __( 'Option Types', 'option-tree' )
          ),
          array(
            'id'          => 'functions',
            'title'       => __( 'Function References', 'option-tree' )
          ),
          array(
            'id'          => 'theme_mode',
            'title'       => __( 'Theme Mode', 'option-tree' )
          ),
          array(
            'id'          => 'meta_boxes',
            'title'       => __( 'Meta Boxes', 'option-tree' )
          ),
          array(
            'id'          => 'examples',
            'title'       => __( 'Code Examples', 'option-tree' )
          ),
          array(
            'id'          => 'layouts_overview',
            'title'       => __( 'Layouts Overview', 'option-tree' )
          )
        ),
        'settings'        => array(
          array(
            'id'          => 'creating_options_text',
            'label'       => __( 'Overview of available Theme Option fields.', 'option-tree' ),
            'type'        => 'creating-options',
            'section'     => 'creating_options'
          ),
          array(
            'id'          => 'option_types_text',
            'label'       => __( 'Option types in alphabetical order & hooks to filter them.', 'option-tree' ),
            'type'        => 'option-types',
            'section'     => 'option_types'
          ),
          array(
            'id'          => 'functions_ot_get_option',
            'label'       => __( 'Function Reference:ot_get_option()', 'option-tree' ),
            'type'        => 'ot-get-option',
            'section'     => 'functions'
          ),
          array(
            'id'          => 'functions_get_option_tree',
            'label'       => __( 'Function Reference:get_option_tree()', 'option-tree' ),
            'type'        => 'get-option-tree',
            'section'     => 'functions'
          ),
          array(
            'id'          => 'theme_mode_text',
            'label'       => __( 'Theme Mode', 'option-tree' ),
            'type'        => 'theme-mode',
            'section'     => 'theme_mode'
          ),
          array(
            'id'          => 'meta_boxes_text',
            'label'       => __( 'Meta Boxes', 'option-tree' ),
            'type'        => 'meta-boxes',
            'section'     => 'meta_boxes'
          ),
          array(
            'id'          => 'example_text',
            'label'       => __( 'Code examples for front-end development.', 'option-tree' ),
            'type'        => 'examples',
            'section'     => 'examples'
          ),
          array(
            'id'          => 'layouts_overview_text',
            'label'       => __( 'What\'s a layout anyhow?', 'option-tree' ),
            'type'        => 'layouts-overview',
            'section'     => 'layouts_overview'
          )
        )
      )
    );
    
    // Loop over the settings and remove as needed.
    foreach( $ot_register_pages_array as $key => $page ) {
      
      // Remove various options from the Settings UI.
      if ( $page['id'] == 'settings' ) {
        
        // Remove the Theme Options UI
        if ( OT_SHOW_OPTIONS_UI == false ) {
        
          foreach( $page['sections'] as $section_key => $section ) {
            if ( $section['id'] == 'create_setting' ) {
              unset($ot_register_pages_array[$key]['sections'][$section_key]);
            }
          }
          
          foreach( $page['settings'] as $setting_key => $setting ) {
            if ( $setting['section'] == 'create_setting' ) {
              unset($ot_register_pages_array[$key]['settings'][$setting_key]);
            }
          }
        
        }
        
        // Remove parts of the Imports UI
        if ( OT_SHOW_SETTINGS_IMPORT == false ) {
          
          foreach( $page['settings'] as $setting_key => $setting ) {
            if ( $setting['section'] == 'import' && in_array( $setting['id'], array('import_xml_text', 'import_settings_text' ) ) ) {
              unset($ot_register_pages_array[$key]['settings'][$setting_key]);
            }
          }
        
        }
        
        // Remove parts of the Export UI
        if ( OT_SHOW_SETTINGS_EXPORT == false ) {
          
          foreach( $page['settings'] as $setting_key => $setting ) {
            if ( $setting['section'] == 'export' && in_array( $setting['id'], array('export_settings_file_text', 'export_settings_text' ) ) ) {
              unset($ot_register_pages_array[$key]['settings'][$setting_key]);
            }
          }
        
        }
        
        // Remove the Layouts UI
        if ( OT_SHOW_NEW_LAYOUT == false ) {
  
          foreach( $page['sections'] as $section_key => $section ) {
            if ( $section['id'] == 'layouts' ) {
              unset($ot_register_pages_array[$key]['sections'][$section_key]);
            }
          }
          
          foreach( $page['settings'] as $setting_key => $setting ) {
            if ( $setting['section'] == 'layouts' ) {
              unset($ot_register_pages_array[$key]['settings'][$setting_key]);
            }
          }
        
        }
      
      }
      
      // Remove the Documentation UI.
      if ( OT_SHOW_DOCS == false && $page['id'] == 'documentation' ) {
        
        unset( $ot_register_pages_array[$key] );
      
      }
    
    }
    
    $ot_register_pages_array = apply_filters( 'ot_register_pages_array', $ot_register_pages_array );
    
    // Register the pages.
    ot_register_settings( array(
        array(
          'id'              => 'option_tree_settings',
          'pages'           => $ot_register_pages_array
        )
      )
    );
  
  }

}

/**
 * Runs directly after the Theme Options are save.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_after_theme_options_save' ) ) {

  function ot_after_theme_options_save() {
  
    $page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
    $updated = isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] == 'true' ? true : false;
    
    /* only execute after the theme options are saved */
    if ( apply_filters( 'ot_theme_options_menu_slug', 'ot-theme-options' ) == $page && $updated ) {
      
      /* grab a copy of the theme options */
      $options = get_option( 'option_tree' );
      
      /* execute the action hook and pass the theme options to it */
      do_action( 'ot_after_theme_options_save', $options );
      
    }
  
  }

}

/**
 * Validate the options by type before saving.
 *
 * This function will run on only some of the option types
 * as all of them don't need to be validated, just the
 * ones users are going to input data into; because they
 * can't be trusted.
 *
 * @param     mixed     Setting value
 * @param     string    Setting type
 * @param     string    Setting field ID
 * @param     string    WPML field ID
 * @return    mixed
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_validate_setting' ) ) {

  function ot_validate_setting( $input, $type, $field_id, $wmpl_id = '' ) {
    
    /* exit early if missing data */
    if ( ! $input || ! $type || ! $field_id )
      return $input;
    
    $input = apply_filters( 'ot_validate_setting', $input, $type, $field_id );
    
    /* WPML Register and Unregister strings */
    if ( ! empty( $wmpl_id ) ) {
    
      /* Allow filtering on the WPML option types */
      $single_string_types = apply_filters( 'ot_wpml_option_types', array( 'text', 'textarea', 'textarea-simple' ) );
              
      if ( in_array( $type, $single_string_types ) ) {
      
        if ( ! empty( $input ) ) {
        
          ot_wpml_register_string( $wmpl_id, $input );
          
        } else {
        
          ot_wpml_unregister_string( $wmpl_id );
          
        }
      
      }
    
    }
            
    if ( 'background' == $type ) {

      $input['background-color'] = ot_validate_setting( $input['background-color'], 'colorpicker', $field_id );
      
      $input['background-image'] = ot_validate_setting( $input['background-image'], 'upload', $field_id );
      
    } else if ( 'colorpicker' == $type ) {

      /* return empty & set error */
      if ( 0 === preg_match( '/^#([a-f0-9]{6}|[a-f0-9]{3})$/i', $input ) ) {
        
        $input = '';
        
        add_settings_error( 'option-tree', 'invalid_hex', __( 'The Colorpicker only allows valid hexadecimal values.', 'option-tree' ), 'error' );
      
      }
    
    } else if ( in_array( $type, array( 'css', 'text', 'textarea', 'textarea-simple' ) ) ) {
      
      if ( ! current_user_can( 'unfiltered_html' ) && OT_ALLOW_UNFILTERED_HTML == false ) {
      
        $input = wp_kses_post( $input );
        
      }
            
    } else if ( 'measurement' == $type ) {
    
      $input[0] = sanitize_text_field( $input[0] );
      
    } else if ( 'typography' == $type && isset( $input['font-color'] ) ) {
      
      $input['font-color'] = ot_validate_setting( $input['font-color'], 'colorpicker', $field_id );
    
    } else if ( 'upload' == $type ) {

      $input = sanitize_text_field( $input );
         
    }
    
    $input = apply_filters( 'ot_after_validate_setting', $input, $type, $field_id );
 
    return $input;
    
  }

}
  
/**
 * Setup the default admin styles
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_admin_styles' ) ) {

  function ot_admin_styles() {
  
    wp_enqueue_style( 'ot-admin-css', OT_URL . 'assets/css/ot-admin.css', false, OT_VERSION );
    
  }
  
}

/**
 * Setup the default admin scripts
 *
 * @uses      add_thickbox()          Include Thickbox for file uploads
 * @uses      wp_enqueue_script()     Add OptionTree scripts
 * @uses      wp_localize_script()    Used to include arbitrary Javascript data
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_admin_scripts' ) ) {

  function ot_admin_scripts() {

    if ( function_exists( 'wp_enqueue_media' ) ) {
      /* WP 3.5 Media Uploader */
      wp_enqueue_media();
    } else {
      /* Legacy Thickbox */
      add_thickbox();
    }
    
    /* load the colorpicker */
    wp_enqueue_script( 'ot-colorpicker-js', OT_URL . 'assets/js/ot-colorpicker.js', array( 'jquery' ), OT_VERSION );
    
    /* load jQuery-ui slider */
    wp_enqueue_script( 'jquery-ui-slider' );
    
    /* load all the required scripts */
    wp_enqueue_script( 'ot-admin-js', OT_URL . 'assets/js/ot-admin.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-sortable', 'media-upload', 'thickbox' ), OT_VERSION );
    
    /* create localized JS array */
    $localized_array = array( 
      'ajax'                  => admin_url( 'admin-ajax.php' ),
      'upload_text'           => __( 'Send to OptionTree', 'option-tree' ),
      'remove_media_text'     => __( 'Remove Media', 'option-tree' ),
      'reset_agree'           => __( 'Are you sure you want to reset back to the defaults?', 'option-tree' ),
      'remove_no'             => __( 'You can\'t remove this! But you can edit the values.', 'option-tree' ),
      'remove_agree'          => __( 'Are you sure you want to remove this?', 'option-tree' ),
      'activate_layout_agree' => __( 'Are you sure you want to activate this layout?', 'option-tree' ),
      'setting_limit'         => __( 'Sorry, you can\'t have settings three levels deep.', 'option-tree' )
    );
    
    /* localized script attached to 'option_tree' */
    wp_localize_script( 'ot-admin-js', 'option_tree', $localized_array );

  }
  
}

/**
 * Returns the ID of a custom post type by post_name.
 *
 * @uses        get_results()
 *
 * @return      int
 *
 * @access      public
 * @since       2.0
 */
if ( ! function_exists( 'ot_get_media_post_ID' ) ) {

  function ot_get_media_post_ID() {
    global $wpdb;
    
    return $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE `post_name` = 'media' AND `post_type` = 'option-tree' AND `post_status` = 'private'" );
    
  }

}

/**
 * Register custom post type & create the media post used to attach images.
 *
 * @uses        get_results()
 *
 * @return      void
 *
 * @access      public
 * @since       2.0
 */
if ( ! function_exists( 'ot_create_media_post' ) ) {
  
  function ot_create_media_post() {
    
    register_post_type( 'option-tree', array(
      'labels'              => array( 'name' => __( 'Option Tree', 'option-tree' ) ),
      'public'              => false,
      'show_ui'             => false,
      'capability_type'     => 'post',
      'exclude_from_search' => true,
      'hierarchical'        => false,
      'rewrite'             => false,
      'supports'            => array( 'title', 'editor' ),
      'can_export'          => false,
      'show_in_nav_menus'   => false
    ) );
  
    /* look for custom page */
    $post_id = ot_get_media_post_ID();
      
    /* no post exists */
    if ( $post_id == 0 ) {
      
      /* create post object */
      $_p = array();
      $_p['post_title']     = 'Media';
      $_p['post_status']    = 'private';
      $_p['post_type']      = 'option-tree';
      $_p['comment_status'] = 'closed';
      $_p['ping_status']    = 'closed';
      
      /* insert the post into the database */
      wp_insert_post( $_p );
      
    }
  
  }

}

/**
 * Setup default settings array.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_default_settings' ) ) {

  function ot_default_settings() {
    global $wpdb, $table_prefix;
    
    if ( ! get_option( 'option_tree_settings' ) ) {
      
      $section_count = 0;
      $settings_count = 0;
      $settings = array();
      
      if ( mysql_num_rows( mysql_query( "SHOW TABLES LIKE '{$table_prefix}option_tree'" ) ) == 1 && $old_settings = $wpdb->get_results( "SELECT * FROM {$table_prefix}option_tree ORDER BY item_sort ASC" ) ) {
        
        foreach ( $old_settings as $setting ) {
          
          /* heading is a section now */
          if ( $setting->item_type == 'heading' ) {
            
            /* add section to the sections array */
            $settings['sections'][$section_count]['id'] = $setting->item_id;
            $settings['sections'][$section_count]['title'] = $setting->item_title;
            
            /* save the last section id to use in creating settings */
            $section = $setting->item_id;
            
            /* increment the section count */
            $section_count++;
            
          } else {
            
            /* add setting to the settings array */
            $settings['settings'][$settings_count]['id'] = $setting->item_id;
            $settings['settings'][$settings_count]['label'] = $setting->item_title;
            $settings['settings'][$settings_count]['desc'] = $setting->item_desc;
            $settings['settings'][$settings_count]['section'] = $section;
            $settings['settings'][$settings_count]['type'] = ot_map_old_option_types( $setting->item_type );
            $settings['settings'][$settings_count]['std'] = '';
            $settings['settings'][$settings_count]['class'] = '';
            
            /* textarea rows */
            $rows = '';
            if ( in_array( $settings['settings'][$settings_count]['type'], array( 'css', 'textarea' ) ) ) {
              if ( (int) $setting->item_options > 0 ) {
                $rows = (int) $setting->item_options;
              } else {
                $rows = 15;
              }
            }
            $settings['settings'][$settings_count]['rows'] = $rows;
            
            /* post type */
            $post_type = '';
            if ( in_array( $settings['settings'][$settings_count]['type'], array( 'custom-post-type-select', 'custom-post-type-checkbox' ) ) ) {
              if ( '' != $setting->item_options ) {
                $post_type = $setting->item_options;
              } else {
                $post_type = 'post';
              }
            }
            $settings['settings'][$settings_count]['post_type'] = $post_type;
            
            /* choices */
            $choices = array();
            if ( in_array( $settings['settings'][$settings_count]['type'], array( 'checkbox', 'radio', 'select' ) ) ) {
              if ( '' != $setting->item_options ) {
                $choices = ot_convert_string_to_array( $setting->item_options );
              }
            }
            $settings['settings'][$settings_count]['choices'] = $choices;
            
            $settings_count++;
          }
        
        }
        
        /* make sure each setting has a section just incase */
        if ( isset( $settings['sections'] ) && isset( $settings['settings'] ) ) {
          foreach( $settings['settings'] as $k => $setting ) {
            if ( '' == $setting['section'] ) {
              $settings['settings'][$k]['section'] = $settings['sections'][0]['id'];
            }
          }
        }
          
      }
      
      /* if array if not properly formed create fallback settings array */
      if ( ! isset( $settings['sections'] ) || ! isset( $settings['settings'] ) ) {
        
        $settings = array(
          'sections' => array(
            array(
              'id'        => 'general',
              'title'     => __( 'General', 'option-tree' )
            )
          ),
          'settings' => array(
            array(
              'id'        => 'sample_text',
              'label'     => __( 'Sample Text Field Label', 'option-tree' ),
              'desc'      => __( 'Description for the sample text field.', 'option-tree' ),
              'section'   => 'general',
              'type'      => 'text',
              'std'       => '',
              'class'     => '',
              'rows'      => '',
              'post_type' => '',
              'choices'   => array()
            )
          )
        );
        
      }
      
      /* update the settings array */
      update_option( 'option_tree_settings', $settings );
      
      /* get option tree array */
      $options = get_option( 'option_tree' );
      
      /* validate options */
      if ( is_array( $options ) ) {

        foreach( $settings['settings'] as $setting ) {
        
          if ( isset( $options[$setting['id']] ) ) {
            
            $content = ot_stripslashes( $options[$setting['id']] );
            
            $options[$setting['id']] = ot_validate_setting( $content, $setting['type'], $setting['id'] );
            
          }
        
        }
        
        /* execute the action hook and pass the theme options to it */
        do_action( 'ot_before_theme_options_save', $options );
        
        /* update the option tree array */
        update_option( 'option_tree', $options );
        
      }
      
    }
    
  }

}

/**
 * Helper function to update the CSS option type after save.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_save_css' ) ) {

  function ot_save_css( $options ) {
    
    /* grab a copy of the settings */
    $settings = get_option( 'option_tree_settings' );
      
    /* has settings */
    if ( isset( $settings['settings'] ) ) {
        
      /* loop through sections and insert CSS when needed */
      foreach( $settings['settings'] as $k => $setting ) {
        
        /* is the CSS option type */
        if ( isset( $setting['type'] ) && 'css' == $setting['type'] ) {

          /* insert CSS into dynamic.css */
          if ( isset( $options[$setting['id']] ) && '' !== $options[$setting['id']] ) {
            
            ot_insert_css_with_markers( $setting['id'], $options[$setting['id']] );
          
          /* remove old CSS from dynamic.css */
          } else {
          
            ot_remove_old_css( $setting['id'] );
            
          }
          
        }
      
      }
      
    }
    
  }

}
 
/**
 * Helper function to load filters for XML mime type.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_add_xml_to_upload_filetypes' ) ) {

  function ot_add_xml_to_upload_filetypes() {
    
    add_filter( 'upload_mimes', 'ot_upload_mimes' );
    add_filter( 'wp_mime_type_icon', 'ot_xml_mime_type_icon', 10, 2 );
  
  }

}

/**
 * Filter 'upload_mimes' and add xml. 
 *
 * @param     array     $mimes An array of valid upload mime types
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_upload_mimes' ) ) {

  function ot_upload_mimes( $mimes ) {
  
    $mimes['xml'] = 'application/xml';
    
    return $mimes;
    
  }

}

/**
 * Filters 'wp_mime_type_icon' and have xml display as a document.
 *
 * @param     string    $icon The mime icon
 * @param     string    $mime The mime type
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_xml_mime_type_icon' ) ) {

  function ot_xml_mime_type_icon( $icon, $mime ) {
  
    if ( $mime == 'application/xml' || $mime == 'text/xml' )
      return wp_mime_type_icon( 'document' );
      
    return $icon;
    
  }

}

/**
 * Import before the screen is displayed.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_import' ) ) {

  function ot_import() {
    
    /* check and verify import xml nonce */
    if ( isset( $_POST['import_xml_nonce'] ) && wp_verify_nonce( $_POST['import_xml_nonce'], 'import_xml_form' ) ) {

      /* import input value */
      $file = isset( $_POST['import_xml'] ) ? esc_url( $_POST['import_xml'] ) : '';
      
      /* validate xml file */
      if ( preg_match( "/(.xml)$/i", $file ) && class_exists( 'SimpleXMLElement' ) ) {
      
        $settings = ot_import_xml( $file );
        
      }
      
      /* default message */
      $message = 'failed';
      
      /* cleanup, save, & show success message */
      if ( isset( $settings ) && ! empty( $settings ) ) {
        
        /* delete file */
        if ( $file ) {
          global $wpdb;
          $attachmentid = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE guid='$file'" );
          wp_delete_attachment( $attachmentid, true );
        }
        
        /* update settings */
        update_option( 'option_tree_settings', $settings );
        
        /* set message */
        $message = 'success';
        
      }
      
      /* redirect */
      wp_redirect( add_query_arg( array( 'action' => 'import-xml', 'message' => $message ), $_POST['_wp_http_referer'] ) );
      exit;
      
    }
    
    /* check and verify import settings nonce */
    if ( isset( $_POST['import_settings_nonce'] ) && wp_verify_nonce( $_POST['import_settings_nonce'], 'import_settings_form' ) ) {

      /* textarea value */
      $textarea = isset( $_POST['import_settings'] ) ? unserialize( ot_decode( $_POST['import_settings'] ) ) : '';
      
      /* default message */
      $message = 'failed';
      
      /* is array: save & show success message */
      if ( is_array( $textarea ) ) {
        update_option( 'option_tree_settings', $textarea );
        $message = 'success';
      }
      
      /* redirect */
      wp_redirect( add_query_arg( array( 'action' => 'import-settings', 'message' => $message ), $_POST['_wp_http_referer'] ) );
      exit;
      
    }
    
    /* check and verify import theme options data nonce */
    if ( isset( $_POST['import_data_nonce'] ) && wp_verify_nonce( $_POST['import_data_nonce'], 'import_data_form' ) ) {
      
      /* default message */
      $message = 'failed';
      
      /* textarea value */
      $options = isset( $_POST['import_data'] ) ? unserialize( ot_decode( $_POST['import_data'] ) ) : '';
      
      /* get settings array */
      $settings = get_option( 'option_tree_settings' );
      
      /* has options */
      if ( is_array( $options ) ) {
        
        /* validate options */
        if ( is_array( $settings ) ) {
        
          foreach( $settings['settings'] as $setting ) {
          
            if ( isset( $options[$setting['id']] ) ) {
              
              $content = ot_stripslashes( $options[$setting['id']] );
              
              $options[$setting['id']] = ot_validate_setting( $content, $setting['type'], $setting['id'] );
              
            }
          
          }
        
        }
        
        /* execute the action hook and pass the theme options to it */
        do_action( 'ot_before_theme_options_save', $options );
      
        /* update the option tree array */
        update_option( 'option_tree', $options );
        
        $message = 'success';
        
      }
      
      /* redirect accordingly */
      wp_redirect( add_query_arg( array( 'action' => 'import-data', 'message' => $message ), $_POST['_wp_http_referer'] ) );
      exit;
      
    }
    
    /* check and verify import layouts nonce */
    if ( isset( $_POST['import_layouts_nonce'] ) && wp_verify_nonce( $_POST['import_layouts_nonce'], 'import_layouts_form' ) ) {
      
      /* default message */
      $message = 'failed';
      
      /* textarea value */
      $layouts = isset( $_POST['import_layouts'] ) ? unserialize( ot_decode( $_POST['import_layouts'] ) ) : '';
      
      /* get settings array */
      $settings = get_option( 'option_tree_settings' );
      
      /* has layouts */
      if ( is_array( $layouts ) ) {
        
        /* validate options */
        if ( is_array( $settings ) ) {
          
          foreach( $layouts as $key => $value ) {
            
            if ( $key == 'active_layout' )
              continue;
              
            $options = unserialize( ot_decode( $value ) );
            
            foreach( $settings['settings'] as $setting ) {

              if ( isset( $options[$setting['id']] ) ) {
                
                $content = ot_stripslashes( $options[$setting['id']] );
                
                $options[$setting['id']] = ot_validate_setting( $content, $setting['type'], $setting['id'] );
                
              }
            
            }

            $layouts[$key] = ot_encode( serialize( $options ) );
          
          }
        
        }
        
        /* update the option tree array */
        if ( isset( $layouts['active_layout'] ) ) {
          
          $new_options = unserialize( ot_decode( $layouts[$layouts['active_layout']] ) );
          
          /* execute the action hook and pass the theme options to it */
          do_action( 'ot_before_theme_options_save', $new_options );
        
          update_option( 'option_tree', $new_options );
          
        }
        
        /* update the option tree layouts array */
        update_option( 'option_tree_layouts', $layouts );
        
        $message = 'success';
        
      }
        
      /* redirect accordingly */
      wp_redirect( add_query_arg( array( 'action' => 'import-layouts', 'message' => $message ), $_POST['_wp_http_referer'] ) );
      exit;
      
    }
    
    return false;

  }
  
}

/**
 * Export before the screen is displayed.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0.8
 */
if ( ! function_exists( 'ot_export' ) ) {

  function ot_export() {
    
    /* check and verify export settings file nonce */
    if ( isset( $_POST['export_settings_file_nonce'] ) && wp_verify_nonce( $_POST['export_settings_file_nonce'], 'export_settings_file_form' ) ) {

      ot_export_php_settings_array();
      
    }
    
  }
  
}

/**
 * Reusable XMl import helper function.
 *
 * @param     string    $file The path to the file.
 * @return    mixed     False or an array of settings.
 *
 * @access    public
 * @since     2.0.8
 */
if ( ! function_exists( 'ot_import_xml' ) ) {

  function ot_import_xml( $file ) {
    
    $get_data = wp_remote_get( $file );
    
    if ( is_wp_error( $get_data ) )
      return false;
        
    $rawdata = isset( $get_data['body'] ) ? $get_data['body'] : false;

    if ( $rawdata ) {
      
      $section_count = 0;
      $settings_count = 0;
      
      $section = '';
      
      $settings = array();
      $xml = new SimpleXMLElement( $rawdata );
  
      foreach ( $xml->row as $value ) {
        
        /* heading is a section now */
        if ( $value->item_type == 'heading' ) {
          
          /* add section to the sections array */
          $settings['sections'][$section_count]['id'] = (string) $value->item_id;
          $settings['sections'][$section_count]['title'] = (string) $value->item_title;
          
          /* save the last section id to use in creating settings */
          $section = (string) $value->item_id;
          
          /* increment the section count */
          $section_count++;
          
        } else {
          
          /* add setting to the settings array */
          $settings['settings'][$settings_count]['id'] = (string) $value->item_id;
          $settings['settings'][$settings_count]['label'] = (string) $value->item_title;
          $settings['settings'][$settings_count]['desc'] = (string) $value->item_desc;
          $settings['settings'][$settings_count]['section'] = $section;
          $settings['settings'][$settings_count]['type'] = ot_map_old_option_types( (string) $value->item_type );
          $settings['settings'][$settings_count]['std'] = '';
          $settings['settings'][$settings_count]['class'] = '';
          
          /* textarea rows */
          $rows = '';
          if ( in_array( $settings['settings'][$settings_count]['type'], array( 'css', 'textarea' ) ) ) {
            if ( (int) $value->item_options > 0 ) {
              $rows = (int) $value->item_options;
            } else {
              $rows = 15;
            }
          }
          $settings['settings'][$settings_count]['rows'] = $rows;
          
          /* post type */
          $post_type = '';
          if ( in_array( $settings['settings'][$settings_count]['type'], array( 'custom-post-type-select', 'custom-post-type-checkbox' ) ) ) {
            if ( '' != (string) $value->item_options ) {
              $post_type = (string) $value->item_options;
            } else {
              $post_type = 'post';
            }
          }
          $settings['settings'][$settings_count]['post_type'] = $post_type;
          
          /* choices */
          $choices = array();
          if ( in_array( $settings['settings'][$settings_count]['type'], array( 'checkbox', 'radio', 'select' ) ) ) {
            if ( '' != (string) $value->item_options ) {
              $choices = ot_convert_string_to_array( (string) $value->item_options );
            }
          }
          $settings['settings'][$settings_count]['choices'] = $choices;
          
          $settings_count++;
        }
  
      }
      
      /* make sure each setting has a section just incase */
      if ( isset( $settings['sections'] ) && isset( $settings['settings'] ) ) {
        foreach( $settings['settings'] as $k => $setting ) {
          if ( '' == $setting['section'] ) {
            $settings['settings'][$k]['section'] = $settings['sections'][0]['id'];
          }
        }
      }
      
      return $settings;
      
    }
    
    return false;
  }

}

/**
 * Export the Theme Mode theme-options.php
 *
 * @return    attachment
 *
 * @access    public
 * @since     2.0.8
 */
if ( ! function_exists( 'ot_export_php_settings_array' ) ) {

  function ot_export_php_settings_array() {
    
    $content              = '';
    $build_settings       = '';
    $contextual_help      = '';
    $sections             = '';
    $settings             = '';
    $option_tree_settings = get_option( 'option_tree_settings', array() );
    
    header( "Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
    header( "Pragma: no-cache ");
    header( "Content-Description: File Transfer" );
    header( 'Content-Disposition: attachment; filename="theme-options.php"');
    header( "Content-Type: application/octet-stream");
    header( "Content-Transfer-Encoding: binary" );
    
    /* build contextual help content */
    if ( isset( $option_tree_settings['contextual_help']['content'] ) ) {
      $help = '';
      foreach( $option_tree_settings['contextual_help']['content'] as $value ) {
        $_id = isset( $value['id'] ) ? $value['id'] : '';
        $_title = isset( $value['title'] ) ? str_replace( "'", "\'", $value['title'] ) : '';
        $_content = isset( $value['content'] ) ? html_entity_decode(  str_replace( "'", "\'", $value['content'] ) ) : '';
        $help.= "
        array(
          'id'        => '$_id',
          'title'     => '$_title',
          'content'   => '$_content'
        ),";
      }
      $help = substr_replace( $help, '' , -1 );
      $contextual_help = "
      'content'       => array( $help
      ),";
    }
    
    /* build contextual help sidebar */
    if ( isset( $option_tree_settings['contextual_help']['sidebar'] ) ) {
      $contextual_help.= "
      'sidebar'       => '" . html_entity_decode(  str_replace( "'", "\'", $option_tree_settings['contextual_help']['sidebar'] ) ) . "'";
    }
    
    /* check that $contexual_help has a value and add to $build_settings */
    if ( '' != $contextual_help ) {
      $build_settings.= "
    'contextual_help' => array( $contextual_help
    ),";
    }
    
    /* build sections */
    if ( isset( $option_tree_settings['sections'] ) ) {
      foreach( $option_tree_settings['sections'] as $value ) {
        $_id = isset( $value['id'] ) ? $value['id'] : '';
        $_title = isset( $value['title'] ) ? str_replace( "'", "\'", $value['title'] ) : '';
        $sections.= "
      array(
        'id'          => '$_id',
        'title'       => '$_title'
      ),";
      }
      $sections = substr_replace( $sections, '' , -1 );
    }
    
    /* check that $sections has a value and add to $build_settings */
    if ( '' != $sections ) {
      $build_settings.= "
    'sections'        => array( $sections
    )";
    }
    
    /* build settings */
    if ( isset( $option_tree_settings['settings'] ) ) {
      foreach( $option_tree_settings['settings'] as $value ) {
        $_id = isset( $value['id'] ) ? $value['id'] : '';
        $_label = isset( $value['label'] ) ? str_replace( "'", "\'", $value['label'] ) : '';
        $_desc = isset( $value['desc'] ) ? str_replace( "'", "\'", $value['desc'] ) : '';
        $_std = isset( $value['std'] ) ? str_replace( "'", "\'", $value['std'] ) : '';
        $_type = isset( $value['type'] ) ? $value['type'] : '';
        $_section = isset( $value['section'] ) ? $value['section'] : '';
        $_rows = isset( $value['rows'] ) ? $value['rows'] : '';
        $_post_type = isset( $value['post_type'] ) ? $value['post_type'] : '';
        $_taxonomy = isset( $value['taxonomy'] ) ? $value['taxonomy'] : '';
        $_min_max_step = isset( $value['min_max_step'] ) ? $value['min_max_step'] : '';
        $_class = isset( $value['class'] ) ? $value['class'] : '';
        
        $choices = '';
        if ( isset( $value['choices'] ) && ! empty( $value['choices'] ) ) {
          foreach( $value['choices'] as $choice ) {
            $_choice_value = isset( $choice['value'] ) ? str_replace( "'", "\'", $choice['value'] ) : '';
            $_choice_label = isset( $choice['label'] ) ? str_replace( "'", "\'", $choice['label'] ) : '';
            $_choice_src = isset( $choice['src'] ) ? str_replace( "'", "\'", $choice['src'] ) : '';
            $choices.= "
          array(
            'value'       => '$_choice_value',
            'label'       => '$_choice_label',
            'src'         => '$_choice_src'
          ),";
          }
          $choices = substr_replace( $choices, '' , -1 );
          $choices = ",
        'choices'     => array( $choices
        ),";
        }
        
        $std = "'$_std'";
        if ( is_array( $_std ) ) {
          $std_array = array();
          foreach( $_std as $_sk => $_sv ) {
            $std_array[] = "'$_sk' => '$_sv'";
          }
          $std = 'array(
' . implode( ",\n", $std_array ) . '
          )';
        }
        
        $setting_settings = '';
        if ( isset( $value['settings'] ) && ! empty( $value['settings'] ) ) {
          foreach( $value['settings'] as $setting ) {
            $_setting_id = isset( $setting['id'] ) ? $setting['id'] : '';
            $_setting_label = isset( $setting['label'] ) ? str_replace( "'", "\'", $setting['label'] ) : '';
            $_setting_desc = isset( $setting['desc'] ) ? str_replace( "'", "\'", $setting['desc'] ) : '';
            $_setting_std = isset( $setting['std'] ) ? $setting['std'] : '';
            $_setting_type = isset( $setting['type'] ) ? $setting['type'] : '';
            $_setting_rows = isset( $setting['rows'] ) ? $setting['rows'] : '';
            $_setting_post_type = isset( $setting['post_type'] ) ? $setting['post_type'] : '';
            $_setting_taxonomy = isset( $setting['taxonomy'] ) ? $setting['taxonomy'] : '';
            $_setting_min_max_step = isset( $setting['min_max_step'] ) ? $setting['min_max_step'] : '';
            $_setting_class = isset( $setting['class'] ) ? $setting['class'] : '';
            
            $setting_choices = '';
            if ( isset( $setting['choices'] ) && ! empty( $setting['choices'] ) ) {
              foreach( $setting['choices'] as $setting_choice ) {
                $_setting_choice_value = isset( $setting_choice['value'] ) ? $setting_choice['value'] : '';
                $_setting_choice_label = isset( $setting_choice['label'] ) ? str_replace( "'", "\'", $setting_choice['label'] ) : '';
                $_setting_choice_src = isset( $setting_choice['src'] ) ? str_replace( "'", "\'", $setting_choice['src'] ) : '';
                $setting_choices.= "
              array(
                'value'       => '$_setting_choice_value',
                'label'       => '$_setting_choice_label',
                'src'         => '$_setting_choice_src'
              ),";
              }
              $setting_choices = substr_replace( $setting_choices, '' , -1 );
              $setting_choices = ",
            'choices'     => array( $setting_choices
            ),";
            }
            
            $setting_std = "'$_setting_std'";
            if ( is_array( $_setting_std ) ) {
              $setting_std_array = array();
              foreach( $_setting_std as $_ssk => $_ssv ) {
                $setting_std_array[] = "'$_ssk' => '$_ssv'";
              }
              $setting_std = 'array(
' . implode( ",\n", $setting_std_array ) . '
              )';
            }
        
            $setting_settings.= "
          array(
            'id'          => '$_setting_id',
            'label'       => '$_setting_label',
            'desc'        => '$_setting_desc',
            'std'         => $setting_std,
            'type'        => '$_setting_type',
            'rows'        => '$_setting_rows',
            'post_type'   => '$_setting_post_type',
            'taxonomy'    => '$_setting_taxonomy',
            'min_max_step'=> '$_setting_min_max_step',
            'class'       => '$_setting_class'$setting_choices
          ),";
          }
          $setting_settings = substr_replace( $setting_settings, '' , -1 );
          $setting_settings = ",
        'settings'    => array( $setting_settings
        )";
        }
        
        $settings.= "
      array(
        'id'          => '$_id',
        'label'       => '$_label',
        'desc'        => '$_desc',
        'std'         => $std,
        'type'        => '$_type',
        'section'     => '$_section',
        'rows'        => '$_rows',
        'post_type'   => '$_post_type',
        'taxonomy'    => '$_taxonomy',
        'min_max_step'=> '$_min_max_step',
        'class'       => '$_class'$choices$setting_settings
      ),";
      }
      $settings = substr_replace( $settings, '' , -1 );
    }
    
    /* check that $sections has a value and add to $build_settings */
    if ( '' != $settings ) {
      $build_settings.= ",
    'settings'        => array( $settings
    )";
    }
    
    $content.= "<?php
/**
 * Initialize the custom theme options.
 */
add_action( 'admin_init', 'custom_theme_options' );

/**
 * Build the custom settings & update OptionTree.
 */
function custom_theme_options() {
  /**
   * Get a copy of the saved settings array. 
   */
  \$saved_settings = get_option( 'option_tree_settings', array() );
  
  /**
   * Custom settings array that will eventually be 
   * passes to the OptionTree Settings API Class.
   */
  \$custom_settings = array( $build_settings
  );
  
  /* allow settings to be filtered before saving */
  \$custom_settings = apply_filters( 'option_tree_settings_args', \$custom_settings );
  
  /* settings are not the same update the DB */
  if ( \$saved_settings !== \$custom_settings ) {
    update_option( 'option_tree_settings', \$custom_settings ); 
  }
  
}";

    echo $content;
    die();
  }

}

/**
 * Save settings array before the screen is displayed.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_save_settings' ) ) {

  function ot_save_settings() {

    /* check and verify import settings nonce */
    if ( isset( $_POST['option_tree_settings_nonce'] ) && wp_verify_nonce( $_POST['option_tree_settings_nonce'], 'option_tree_settings_form' ) ) {

      /* settings value */
      $settings = isset( $_POST['option_tree_settings'] ) ? $_POST['option_tree_settings'] : '';
      
      /* validate sections */
      if ( isset( $settings['sections'] ) ) {
        
        /* fix numeric keys since drag & drop will change them */
        $settings['sections'] = array_values( $settings['sections'] );
        
        /* loop through sections */
        foreach( $settings['sections'] as $k => $section ) {
          
          /* remove from array if missing values */
          if ( ( ! isset( $section['title'] ) && ! isset( $section['id'] ) ) || ( '' == $section['title'] && '' == $section['id'] ) ) {
          
            unset( $settings['sections'][$k] );
            
          } else {
            
            /* validate label */
            if ( '' != $section['title'] ) {
            
             $settings['sections'][$k]['title'] = wp_kses_post( $section['title'] );
              
            }
            
            /* missing title set to unfiltered ID */
            if ( ! isset( $section['title'] ) || '' == $section['title'] ) {
              
              $settings['sections'][$k]['title'] = wp_kses_post( $section['id'] );
            
            /* missing ID set to title */ 
            } else if ( ! isset( $section['id'] ) || '' == $section['id'] ) {
              
              $section['id'] = wp_kses_post( $section['title'] );
              
            }
            
            /* sanitize ID once everything has been checked first */
            $settings['sections'][$k]['id'] = ot_sanitize_option_id( wp_kses_post( $section['id'] ) );
            
          }
          
        }
        
        $settings['sections'] = ot_stripslashes( $settings['sections'] );
      
      }
      
      /* validate settings by looping over array as many times as it takes */
      if ( isset( $settings['settings'] ) ) {
        
        $settings['settings'] = ot_validate_settings_array( $settings['settings'] );
        
      }
      
      /* validate contextual_help */
      if ( isset( $settings['contextual_help']['content'] ) ) {
        
        /* fix numeric keys since drag & drop will change them */
        $settings['contextual_help']['content'] = array_values( $settings['contextual_help']['content'] );
        
        /* loop through content */
        foreach( $settings['contextual_help']['content'] as $k => $content ) {
          
          /* remove from array if missing values */
          if ( ( ! isset( $content['title'] ) && ! isset( $content['id'] ) ) || ( '' == $content['title'] && '' == $content['id'] ) ) {
          
            unset( $settings['contextual_help']['content'][$k] );
            
          } else {
            
            /* validate label */
            if ( '' != $content['title'] ) {
            
             $settings['contextual_help']['content'][$k]['title'] = wp_kses_post( $content['title'] );
              
            }
          
            /* missing title set to unfiltered ID */
            if ( ! isset( $content['title'] ) || '' == $content['title'] ) {
              
              $settings['contextual_help']['content'][$k]['title'] = wp_kses_post( $content['id'] );
            
            /* missing ID set to title */ 
            } else if ( ! isset( $content['id'] ) || '' == $content['id'] ) {
              
              $content['id'] = wp_kses_post( $content['title'] );
              
            }
            
            /* sanitize ID once everything has been checked first */
            $settings['contextual_help']['content'][$k]['id'] = ot_sanitize_option_id( wp_kses_post( $content['id'] ) );
            
          }
          
          /* validate textarea description */
          if ( isset( $content['content'] ) ) {
          
            $settings['contextual_help']['content'][$k]['content'] = wp_kses_post( $content['content'] );
            
          }
          
        }
      
      }
      
      /* validate contextual_help sidebar */
      if ( isset( $settings['contextual_help']['sidebar'] ) ) {
      
        $settings['contextual_help']['sidebar'] = wp_kses_post( $settings['contextual_help']['sidebar'] );
        
      }
      
      $settings['contextual_help'] = ot_stripslashes( $settings['contextual_help'] );
      
      /* default message */
      $message = 'failed';
      
      /* is array: save & show success message */
      if ( is_array( $settings ) ) {
        
        /* WPML unregister ID's that have been removed */
        if ( function_exists( 'icl_unregister_string' ) ) {
          
          $current = get_option( 'option_tree_settings' );
          $options = get_option( 'option_tree' );
          
          if ( isset( $current['settings'] ) ) {
            
            /* Empty ID array */
            $new_ids = array();
            
            /* Build the WPML IDs array */
            foreach( $settings['settings'] as $setting ) {
            
              if ( $setting['id'] ) {
                
                $new_ids[] = $setting['id'];

              }
              
            }
            
            /* Remove missing IDs from WPML */
            foreach( $current['settings'] as $current_setting ) {
              
              if ( ! in_array( $current_setting['id'], $new_ids ) ) {
              
                if ( ! empty( $options[$current_setting['id']] ) && in_array( $current_setting['type'], array( 'list-item', 'slider' ) ) ) {
                  
                  foreach( $options[$current_setting['id']] as $key => $value ) {
          
                    foreach( $value as $ckey => $cvalue ) {
                      
                      ot_wpml_unregister_string( $current_setting['id'] . '_' . $ckey . '_' . $key );
                      
                    }
                  
                  }
                  
                } else {
                
                  ot_wpml_unregister_string( $current_setting['id'] );
                  
                }
              
              }
              
            }

          }
          
        }
        
        update_option( 'option_tree_settings', $settings );
        $message = 'success';
        
      }
      
      /* redirect */
      wp_redirect( add_query_arg( array( 'action' => 'save-settings', 'message' => $message ), $_POST['_wp_http_referer'] ) );
      exit;
      
    }
    
    return false;

  }
  
}

/**
 * Validate the settings array before save.
 *
 * This function will loop over the settings array as many 
 * times as it takes to validate every sub setting.
 *
 * @param     array     $settings The array of settings.
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_validate_settings_array' ) ) {

  function ot_validate_settings_array( $settings = array() ) {
    
    /* validate settings */
    if ( count( $settings ) > 0 ) {
      
      /* fix numeric keys since drag & drop will change them */
      $settings = array_values( $settings );
      
      /* loop through settings */
      foreach( $settings as $k => $setting ) {
        
        
        /* remove from array if missing values */
        if ( ( ! isset( $setting['label'] ) && ! isset( $setting['id'] ) ) || ( '' == $setting['label'] && '' == $setting['id'] ) ) {
        
          unset( $settings[$k] );
          
        } else {
          
          /* validate label */
          if ( '' != $setting['label'] ) {
          
            $settings[$k]['label'] = wp_kses_post( $setting['label'] );
            
          }
          
          /* missing label set to unfiltered ID */
          if ( ! isset( $setting['label'] ) || '' == $setting['label'] ) {
            
            $settings[$k]['label'] = $setting['id'];
          
          /* missing ID set to label */ 
          } else if ( ! isset( $setting['id'] ) || '' == $setting['id'] ) {
            
            $setting['id'] = wp_kses_post( $setting['label'] );
            
          }
          
          /* sanitize ID once everything has been checked first */
          $settings[$k]['id'] = ot_sanitize_option_id( wp_kses_post( $setting['id'] ) );
          
        }
        
        /* validate description */
        if ( '' != $setting['desc']  ) {
        
          $settings[$k]['desc'] = wp_kses_post( $setting['desc'] );
          
        }
        
        /* validate choices */
        if ( isset( $setting['choices'] ) ) {
          
          /* loop through choices */
          foreach( $setting['choices'] as $ck => $choice ) {
            
            /* remove from array if missing values */
            if ( ( ! isset( $choice['label'] ) && ! isset( $choice['value'] ) ) || ( '' == $choice['label'] && '' == $choice['value'] ) ) {
        
              unset( $setting['choices'][$ck] );
              
            } else {
              
              /* missing label set to unfiltered ID */
              if ( ! isset( $choice['label'] ) || '' == $choice['label'] ) {
                
                $setting['choices'][$ck]['label'] = wp_kses_post( $choice['value'] );
              
              /* missing value set to label */ 
              } else if ( ! isset( $choice['value'] ) || '' == $choice['value'] ) {
                
                $setting['choices'][$ck]['value'] = ot_sanitize_option_id( wp_kses_post( $choice['label'] ) );
                
              }
              
            }
            
          }
          
          /* update keys and push new array values */
          $settings[$k]['choices'] = array_values( $setting['choices'] );
          
        }
        
        /* validate sub settings */
        if ( isset( $setting['settings'] ) ) {

          $settings[$k]['settings'] = ot_validate_settings_array( $setting['settings'] );
          
        }

      }
    
    }
    
    /* return array but strip those damn slashes out first!!! */
    return ot_stripslashes( $settings );
    
  }

}

/**
 * Save layouts array before the screen is displayed.
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_modify_layouts' ) ) {

  function ot_modify_layouts() {

    /* check and verify modify layouts nonce */
    if ( isset( $_POST['option_tree_modify_layouts_nonce'] ) && wp_verify_nonce( $_POST['option_tree_modify_layouts_nonce'], 'option_tree_modify_layouts_form' ) ) {
      
      /* previous layouts value */
      $option_tree_layouts = get_option( 'option_tree_layouts' );
      
      /* new layouts value */
      $layouts = isset( $_POST['option_tree_layouts'] ) ? $_POST['option_tree_layouts'] : '';
      
      /* rebuild layout array */
      $rebuild = array();
      
      /* validate layouts */
      if ( is_array( $layouts ) && ! empty( $layouts ) ) {
        
        /* setup active layout */
        if ( isset( $layouts['active_layout'] ) && ! empty( $layouts['active_layout'] ) ) {
          $rebuild['active_layout'] = $layouts['active_layout'];
        }
        
        /* add new and overwrite active layout */
        if ( isset( $layouts['_add_new_layout_'] ) && ! empty( $layouts['_add_new_layout_'] ) ) {
          $rebuild['active_layout'] = ot_sanitize_layout_id( $layouts['_add_new_layout_'] );
          $rebuild[$rebuild['active_layout']] = ot_encode( serialize( get_option( 'option_tree' ) ) );
        }
        
        $first_layout = '';
        
        /* loop through layouts */
        foreach( $layouts as $key => $layout ) {
          
          /* skip over active layout key */
          if ( $key == 'active_layout' )
            continue;
          
          /* check if the key exists then set value */
          if ( isset( $option_tree_layouts[$key] ) && ! empty( $option_tree_layouts[$key] ) ) {
            $rebuild[$key] = $option_tree_layouts[$key];
            if ( '' == $first_layout ) {
              $first_layout = $key;
            }
          }
          
        }
        
        if ( isset( $rebuild['active_layout'] ) && ! isset( $rebuild[$rebuild['active_layout']] ) && ! empty( $first_layout ) ) {
          $rebuild['active_layout'] = $first_layout;
        }
        
      }
      
      /* default message */
      $message = 'failed';
      
      /* is array: save & show success message */
      if ( count( $rebuild ) > 1 ) {

        /* rebuild the theme options */
        $rebuild_option_tree = unserialize( ot_decode( $rebuild[$rebuild['active_layout']] ) );
        if ( is_array( $rebuild_option_tree ) ) {
          
          /* execute the action hook and pass the theme options to it */
          do_action( 'ot_before_theme_options_save', $rebuild_option_tree );
          
          update_option( 'option_tree', $rebuild_option_tree );
          
        }
        
        /* rebuild the layouts */
        update_option( 'option_tree_layouts', $rebuild );
        
        /* change message */
        $message = 'success';
        
      } else if ( count( $rebuild ) <= 1 ) {

        /* delete layouts option */
        delete_option( 'option_tree_layouts' );
        
        /* change message */
        $message = 'deleted';
        
      }
      
      /* redirect */
      if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == apply_filters( 'ot_theme_options_menu_slug', 'ot-theme-options' ) ) {
        $query_args = add_query_arg( array( 'settings-updated' => 'layout' ), remove_query_arg( array( 'action', 'message' ), $_POST['_wp_http_referer'] ) );
      } else {
        $query_args = add_query_arg( array( 'action' => 'save-layouts', 'message' => $message ), $_POST['_wp_http_referer'] );
      }
      wp_redirect( $query_args );
      exit;
      
    }
    
    return false;

  }
  
}

/**
 * Helper function to display alert messages.
 *
 * @param     array     Page array
 * @return    mixed
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_alert_message' ) ) {

  function ot_alert_message( $page = array() ) {
    
    if ( empty( $page ) )
      return false;
    
    $action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
    $message = isset( $_REQUEST['message'] ) ? $_REQUEST['message'] : '';
    $updated = isset( $_REQUEST['settings-updated'] ) ? $_REQUEST['settings-updated'] : '';
    
    if ( $action == 'save-settings' ) {
    
      if ( $message == 'success' ) {
        
        return '<div id="message" class="updated fade below-h2"><p>' . __( 'Settings updated.', 'option-tree' ) . '</p></div>';
        
      } else if ( $message == 'failed' ) {
        
        return '<div id="message" class="error fade below-h2"><p>' . __( 'Settings could not be saved.', 'option-tree' ) . '</p></div>';
        
      }
      
    } else if ( $action == 'import-xml' || $action == 'import-settings' ) {
      
      if ( $message == 'success' ) {
        
        return '<div id="message" class="updated fade below-h2"><p>' . __( 'Settings Imported.', 'option-tree' ) . '</p></div>';
        
      } else if ( $message == 'failed' ) {
        
        return '<div id="message" class="error fade below-h2"><p>' . __( 'Settings could not be imported.', 'option-tree' ) . '</p></div>';
        
      }
    } else if ( $action == 'import-data' ) {
      
      if ( $message == 'success' ) {
        
        return '<div id="message" class="updated fade below-h2"><p>' . __( 'Data Imported.', 'option-tree' ) . '</p></div>';
        
      } else if ( $message == 'failed' ) {
        
        return '<div id="message" class="error fade below-h2"><p>' . __( 'Data could not be imported.', 'option-tree' ) . '</p></div>';
        
      }
    
    } else if ( $action == 'import-layouts' ) {
      
      if ( $message == 'success' ) {
        
        return '<div id="message" class="updated fade below-h2"><p>' . __( 'Layouts Imported.', 'option-tree' ) . '</p></div>';
        
      } else if ( $message == 'failed' ) {
        
        return '<div id="message" class="error fade below-h2"><p>' . __( 'Layouts could not be imported.', 'option-tree' ) . '</p></div>';
        
      }
           
    } else if ( $action == 'save-layouts' ) {
      
      if ( $message == 'success' ) {
        
        return '<div id="message" class="updated fade below-h2"><p>' . __( 'Layouts Updated.', 'option-tree' ) . '</p></div>';
        
      } else if ( $message == 'failed' ) {
        
        return '<div id="message" class="error fade below-h2"><p>' . __( 'Layouts could not be updated.', 'option-tree' ) . '</p></div>';
        
      } else if ( $message == 'deleted' ) {
        
        return '<div id="message" class="updated fade below-h2"><p>' . __( 'Layouts have been deleted.', 'option-tree' ) . '</p></div>';
        
      }
    
    } else if ( $updated == 'layout' ) {  
       
      return '<div id="message" class="updated fade below-h2"><p>' . __( 'Layout activated.', 'option-tree' ) . '</p></div>';
        
    } else if ( $action == 'reset' ) {
      
      return '<div id="message" class="updated fade below-h2"><p>' . $page['reset_message'] . '</p></div>';
        
    }
    
    do_action( 'ot_custom_page_messages' );
    
    if ( $updated == 'true' ) {  
       
      return '<div id="message" class="updated fade below-h2"><p>' . $page['updated_message'] . '</p></div>';
        
    } 
    
    return false;
    
  }
  
}

/**
 * Setup the default option types.
 *
 * The returned option types are filterable so you can add your own.
 * This is not a task for a beginner as you'll need to add the function
 * that displays the option to the user and validate the saved data.
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_option_types_array' ) ) {

  function ot_option_types_array() {
  
    return apply_filters( 'ot_option_types_array', array( 
      'background'                => 'Background',
      'category-checkbox'         => 'Category Checkbox',
      'category-select'           => 'Category Select',
      'checkbox'                  => 'Checkbox',
      'colorpicker'               => 'Colorpicker',
      'css'                       => 'CSS',
      'custom-post-type-checkbox' => 'Custom Post Type Checkbox',
      'custom-post-type-select'   => 'Custom Post Type Select',
      'list-item'                 => 'List Item',
      'measurement'               => 'Measurement',
      'numeric-slider'            => 'Numeric Slider',
      'page-checkbox'             => 'Page Checkbox',
      'page-select'               => 'Page Select',
      'post-checkbox'             => 'Post Checkbox',
      'post-select'               => 'Post Select',
      'radio'                     => 'Radio',
      'radio-image'               => 'Radio Image',
      'select'                    => 'Select',
      'sidebar-select'            => 'Sidebar Select', 
      'slider'                    => 'Slider',
      'tag-checkbox'              => 'Tag Checkbox',
      'tag-select'                => 'Tag Select',
      'taxonomy-checkbox'         => 'Taxonomy Checkbox',
      'taxonomy-select'           => 'Taxonomy Select',
      'text'                      => 'Text',
      'textarea'                  => 'Textarea',
      'textarea-simple'           => 'Textarea Simple',
      'textblock'                 => 'Textblock',
      'textblock-titled'          => 'Textblock Titled',
      'typography'                => 'Typography',
      'upload'                    => 'Upload'
    ) );
    
  }
}

/**
 * Map old option types for rebuilding XML and Table data.
 *
 * @param     string      $type The old option type
 * @return    string      The new option type
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_map_old_option_types' ) ) {

  function ot_map_old_option_types( $type = '' ) {
    
    if ( ! $type ) 
      return 'text';
      
    $types = array(
      'background'        => 'background',
      'category'          => 'category-select',
      'categories'        => 'category-checkbox',
      'checkbox'          => 'checkbox',
      'colorpicker'       => 'colorpicker',
      'css'               => 'css',
      'custom_post'       => 'custom-post-type-select',
      'custom_posts'      => 'custom-post-type-checkbox',                     
      'input'             => 'text',
      'image'             => 'upload',
      'measurement'       => 'measurement',
      'page'              => 'page-select',
      'pages'             => 'page-checkbox',
      'post'              => 'post-select',
      'posts'             => 'post-checkbox',
      'radio'             => 'radio',
      'select'            => 'select',
      'slider'            => 'slider',
      'tag'               => 'tag-select',
      'tags'              => 'tag-checkbox',
      'textarea'          => 'textarea',
      'textblock'         => 'textblock',
      'typography'        => 'typography',
      'upload'            => 'upload'
    );
    
    if ( isset( $types[$type] ) )
      return $types[$type];
    
    return false;
    
  }
}

/**
 * Recognized font families
 *
 * Returns an array of all recognized font families.
 * Keys are intended to be stored in the database
 * while values are ready for display in html.
 * Renamed in version 2.0 to avoid name collisions.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_recognized_font_families' ) ) {

  function ot_recognized_font_families( $field_id = '' ) {
  
    return apply_filters( 'ot_recognized_font_families', array(
      'arial'     => 'Arial',
      'georgia'   => 'Georgia',
      'helvetica' => 'Helvetica',
      'palatino'  => 'Palatino',
      'tahoma'    => 'Tahoma',
      'times'     => '"Times New Roman", sans-serif',
      'trebuchet' => 'Trebuchet',
      'verdana'   => 'Verdana'
    ), $field_id );
    
  }

}

/**
 * Recognized font sizes
 *
 * Returns an array of all recognized font sizes.
 *
 * @uses      apply_filters()
 *
 * @param     string  $field_id ID that's passed to the filters.
 * @return    array
 *
 * @access    public
 * @since     2.0.12
 */
if ( ! function_exists( 'ot_recognized_font_sizes' ) ) {

  function ot_recognized_font_sizes( $field_id ) {
  
    $range = ot_range( 
      apply_filters( 'ot_font_size_low_range', 0, $field_id ), 
      apply_filters( 'ot_font_size_high_range', 150, $field_id ), 
      apply_filters( 'ot_font_size_range_interval', 1, $field_id )
    );
    
    $unit = apply_filters( 'ot_font_size_unit_type', 'px', $field_id );
    
    foreach( $range as $k => $v ) {
      $range[$k] = $v . $unit;
    }
    
    return $range;
  }

}

/**
 * Recognized font styles
 *
 * Returns an array of all recognized font styles.
 * Renamed in version 2.0 to avoid name collisions.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_recognized_font_styles' ) ) {

  function ot_recognized_font_styles( $field_id = '' ) {
  
    return apply_filters( 'ot_recognized_font_styles', array(
      'normal'  => 'Normal',
      'italic'  => 'Italic',
      'oblique' => 'Oblique',
      'inherit' => 'Inherit'
    ), $field_id );
    
  }

}

/**
 * Recognized font variants
 *
 * Returns an array of all recognized font variants.
 * Renamed in version 2.0 to avoid name collisions.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_recognized_font_variants' ) ) {

  function ot_recognized_font_variants( $field_id = '' ) {
  
    return apply_filters( 'ot_recognized_font_variants', array(
      'normal'      => 'Normal',
      'small-caps'  => 'Small Caps',
      'inherit'     => 'Inherit'
    ), $field_id );
  
  }
  
}

/**
 * Recognized font weights
 *
 * Returns an array of all recognized font weights.
 * Renamed in version 2.0 to avoid name collisions.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_recognized_font_weights' ) ) {

  function ot_recognized_font_weights( $field_id = '' ) {
    
    return apply_filters( 'ot_recognized_font_weights', array(
      'normal'    => 'Normal',
      'bold'      => 'Bold',
      'bolder'    => 'Bolder',
      'lighter'   => 'Lighter',
      '100'       => '100',
      '200'       => '200',
      '300'       => '300',
      '400'       => '400',
      '500'       => '500',
      '600'       => '600',
      '700'       => '700',
      '800'       => '800',
      '900'       => '900',
      'inherit'   => 'Inherit'
    ), $field_id );
  
  }
  
}

/**
 * Recognized letter spacing
 *
 * Returns an array of all recognized line heights.
 *
 * @uses      apply_filters()
 *
 * @param     string  $field_id ID that's passed to the filters.
 * @return    array
 *
 * @access    public
 * @since     2.0.12
 */
if ( ! function_exists( 'ot_recognized_letter_spacing' ) ) {

  function ot_recognized_letter_spacing( $field_id ) {
  
    $range = ot_range( 
      apply_filters( 'ot_letter_spacing_low_range', -0.1, $field_id ), 
      apply_filters( 'ot_letter_spacing_high_range', 0.1, $field_id ), 
      apply_filters( 'ot_letter_spacing_range_interval', 0.01, $field_id )
    );
    
    $unit = apply_filters( 'ot_letter_spacing_unit_type', 'em', $field_id );
    
    foreach( $range as $k => $v ) {
      $range[$k] = $v . $unit;
    }
    
    return $range;
  }

}

/**
 * Recognized line heights
 *
 * Returns an array of all recognized line heights.
 *
 * @uses      apply_filters()
 *
 * @param     string  $field_id ID that's passed to the filters.
 * @return    array
 *
 * @access    public
 * @since     2.0.12
 */
if ( ! function_exists( 'ot_recognized_line_heights' ) ) {

  function ot_recognized_line_heights( $field_id ) {
  
    $range = ot_range( 
      apply_filters( 'ot_line_height_low_range', 0, $field_id ), 
      apply_filters( 'ot_line_height_high_range', 150, $field_id ), 
      apply_filters( 'ot_line_height_unit_type', 1, $field_id )
    );
    
    $unit = apply_filters( 'ot_line_height_unit_type', 'px', $field_id );
    
    foreach( $range as $k => $v ) {
      $range[$k] = $v . $unit;
    }
    
    return $range;
  }

}

/**
 * Recognized text decorations
 *
 * Returns an array of all recognized text decorations.
 * Keys are intended to be stored in the database
 * while values are ready for display in html.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     2.0.10
 */
if ( ! function_exists( 'ot_recognized_text_decorations' ) ) {
  
  function ot_recognized_text_decorations( $field_id = '' ) {
  
    return apply_filters( 'ot_recognized_text_decorations', array(
      'blink'         => 'Blink',
      'inherit'       => 'Inherit',
      'line-through'  => 'Line Through',
      'none'          => 'None',
      'overline'      => 'Overline',
      'underline'     => 'Underline'
    ), $field_id );
    
  }

}

/**
 * Recognized text transformations
 *
 * Returns an array of all recognized text transformations.
 * Keys are intended to be stored in the database
 * while values are ready for display in html.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     2.0.10
 */
if ( ! function_exists( 'ot_recognized_text_transformations' ) ) {
  
  function ot_recognized_text_transformations( $field_id = '' ) {
  
    return apply_filters( 'ot_recognized_text_transformations', array(
      'capitalize'  => 'Capitalize',
      'inherit'     => 'Inherit',
      'lowercase'   => 'Lowercase',
      'none'        => 'None',
      'uppercase'   => 'Uppercase'
    ), $field_id );
    
  }

}

/**
 * Recognized background repeat
 *
 * Returns an array of all recognized background repeat values.
 * Renamed in version 2.0 to avoid name collisions.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_recognized_background_repeat' ) ) {
  
  function ot_recognized_background_repeat( $field_id = '' ) {
  
    return apply_filters( 'ot_recognized_background_repeat', array(
      'no-repeat' => 'No Repeat',
      'repeat'    => 'Repeat All',
      'repeat-x'  => 'Repeat Horizontally',
      'repeat-y'  => 'Repeat Vertically',
      'inherit'   => 'Inherit'
    ), $field_id );
    
  }
  
}

/**
 * Recognized background attachment
 *
 * Returns an array of all recognized background attachment values.
 * Renamed in version 2.0 to avoid name collisions.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_recognized_background_attachment' ) ) {

  function ot_recognized_background_attachment( $field_id = '' ) {
  
    return apply_filters( 'ot_recognized_background_attachment', array(
      "fixed"   => "Fixed",
      "scroll"  => "Scroll",
      "inherit" => "Inherit"
    ), $field_id );
    
  }

}

/**
 * Recognized background position
 *
 * Returns an array of all recognized background position values.
 * Renamed in version 2.0 to avoid name collisions.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_recognized_background_position' ) ) {

  function ot_recognized_background_position( $field_id = '' ) {
  
    return apply_filters( 'ot_recognized_background_position', array(
      "left top"      => "Left Top",
      "left center"   => "Left Center",
      "left bottom"   => "Left Bottom",
      "center top"    => "Center Top",
      "center center" => "Center Center",
      "center bottom" => "Center Bottom",
      "right top"     => "Right Top",
      "right center"  => "Right Center",
      "right bottom"  => "Right Bottom"
    ), $field_id );
    
  }

}

/**
 * Measurement Units
 *
 * Returns an array of all available unit types.
 * Renamed in version 2.0 to avoid name collisions.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_measurement_unit_types' ) ) {
  
  function ot_measurement_unit_types( $field_id = '' ) {
  
    return apply_filters( 'ot_measurement_unit_types', array(
      'px' => 'px',
      '%'  => '%',
      'em' => 'em',
      'pt' => 'pt'
    ), $field_id );
    
  }

}

/**
 * Radio Images default array.
 *
 * Returns an array of all available radio images.
 * You can filter this function to change the images
 * on a per option basis.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_radio_images' ) ) {
  
  function ot_radio_images( $field_id = '' ) {
  
    return apply_filters( 'ot_radio_images', array(
      array(
        'value'   => 'left-sidebar',
        'label'   => __( 'Left Sidebar', 'option-tree' ),
        'src'     => OT_URL . 'assets/images/layout/left-sidebar.png'
      ),
      array(
        'value'   => 'right-sidebar',
        'label'   => __( 'Right Sidebar', 'option-tree' ),
        'src'     => OT_URL . 'assets/images/layout/right-sidebar.png'
      ),
      array(
        'value'   => 'full-width',
        'label'   => __( 'Full Width (no sidebar)', 'option-tree' ),
        'src'     => OT_URL . 'assets/images/layout/full-width.png'
      ),
      array(
        'value'   => 'dual-sidebar',
        'label'   => __( 'Dual Sidebar', 'option-tree' ),
        'src'     => OT_URL . 'assets/images/layout/dual-sidebar.png'
      ),
      array(
        'value'   => 'left-dual-sidebar',
        'label'   => __( 'Left Dual Sidebar', 'option-tree' ),
        'src'     => OT_URL . 'assets/images/layout/left-dual-sidebar.png'
      ),
      array(
        'value'   => 'right-dual-sidebar',
        'label'   => __( 'Right Dual Sidebar', 'option-tree' ),
        'src'     => OT_URL . 'assets/images/layout/right-dual-sidebar.png'
      )
    ), $field_id );
    
  }

}

/**
 * Default List Item Settings array.
 *
 * Returns an array of the default list item settings.
 * You can filter this function to change the settings
 * on a per option basis.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_list_item_settings' ) ) {

  function ot_list_item_settings( $id ) {
    
    $settings = apply_filters( 'ot_list_item_settings', array(
      array(
        'id'        => 'image',
        'label'     => __( 'Image', 'option-tree' ),
        'desc'      => '',
        'std'       => '',
        'type'      => 'upload',
        'rows'      => '',
        'class'     => '',
        'post_type' => '',
        'choices'   => array()
      ),
      array(
        'id'        => 'link',
        'label'     => __( 'Link', 'option-tree' ),
        'desc'      => '',
        'std'       => '',
        'type'      => 'text',
        'rows'      => '',
        'class'     => '',
        'post_type' => '',
        'choices'   => array()
      ),
      array(
        'id'        => 'description',
        'label'     => __( 'Description', 'option-tree' ),
        'desc'      => '',
        'std'       => '',
        'type'      => 'textarea-simple',
        'rows'      => 10,
        'class'     => '',
        'post_type' => '',
        'choices'   => array()
      )
    ), $id );
    
    return $settings;
  
  }

}

/**
 * Default Slider Settings array.
 *
 * Returns an array of the default slider settings.
 * You can filter this function to change the settings
 * on a per option basis.
 *
 * @uses      apply_filters()
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_slider_settings' ) ) {

  function ot_slider_settings( $id ) {
    
    $settings = apply_filters( 'image_slider_fields', array(
      array(
        'name'      => 'image',
        'type'      => 'image',
        'label'     => __( 'Image', 'option-tree' ),
        'class'     => ''
      ),
      array(
        'name'      => 'link',
        'type'      => 'text',
        'label'     => __( 'Link', 'option-tree' ),
        'class'     => ''
      ),
      array(
        'name'      => 'description',
        'type'      => 'textarea',
        'label'     => __( 'Description', 'option-tree' ),
        'class'     => ''
      )
    ), $id );
    
    /* fix the array keys, values, and just get it 2.0 ready */
    foreach( $settings as $_k => $setting ) {
    
      foreach( $setting as $s_key => $s_value ) {
        
        if ( 'name' == $s_key ) {
        
          $settings[$_k]['id'] = $s_value;
          unset($settings[$_k]['name']);
          
        } else if ( 'type' == $s_key ) {
          
          if ( 'input' == $s_value ) {
          
            $settings[$_k]['type'] = 'text';
            
          } else if ( 'textarea' == $s_value ) {
          
            $settings[$_k]['type'] = 'textarea-simple';
            
          } else if ( 'image' == $s_value ) {
          
            $settings[$_k]['type'] = 'upload';
            
          }
          
        }
        
      } 
      
    }
    
    return $settings;
    
  }

}

/**
 * Inserts CSS with field_id markers.
 *
 * Inserts CSS into a dynamic.css file, placing it between
 * BEGIN and END field_id markers. Replaces existing marked info, 
 * but still retains surrounding data.
 *
 * @param     string  $field_id The CSS option field ID.
 * @param     array   $options The current option_tree array.
 * @return    bool    True on write success, false on failure.
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0.12
 */
if ( ! function_exists( 'ot_insert_css_with_markers' ) ) {

  function ot_insert_css_with_markers( $field_id = '', $insertion = '', $meta = false ) {
    
    /* missing $field_id or $insertion exit early */
    if ( '' == $field_id || '' == $insertion )
      return;

    /* path to the dynamic.css file */
    $filepath = get_stylesheet_directory() . '/dynamic.css';
    
    /* allow filter on path */
    $filepath = apply_filters( 'css_option_file_path', $filepath, $field_id );
    
    /* grab a copy of the paths array */
    $ot_css_file_paths = get_option( 'ot_css_file_paths', array() );
    
    /* set the path for this field */
    $ot_css_file_paths[$field_id] = $filepath;
    
    /* update the paths */
    update_option( 'ot_css_file_paths', $ot_css_file_paths );
    
    /* insert CSS into file */
    if ( file_exists( $filepath ) ) {
      
      $insertion   = ot_normalize_css( $insertion );
      $regex       = "/{{([a-zA-Z0-9\_\-\#\|\=]+)}}/";
      $marker      = $field_id;
      
      /* Match custom CSS */
      preg_match_all( $regex, $insertion, $matches );
      
      /* Loop through CSS */
      foreach( $matches[0] as $option ) {

        $value        = '';
        $option_id    = str_replace( array( '{{', '}}' ), '', $option );
        $option_array = explode( '|', $option_id );
        
        /* get the array value */
        if ( $meta ) {
          global $post;
          
          $value = get_post_meta( $post->ID, $option_array[0], true );
          
        } else {
        
          $options = get_option( 'option_tree' );
          
          if ( isset( $options[$option_array[0]] ) ) {
            
            $value = $options[$option_array[0]];
  
          }
          
        }
        
        if ( is_array( $value ) ) {
          
          if ( ! isset( $option_array[1] ) ) {
          
            /* Measurement */
            if ( isset( $value[0] ) && isset( $value[1] ) ) {
              
              /* set $value with measurement properties */
              $value = $value[0].$value[1];
              
            /* typography */
            } else if ( ot_array_keys_exists( $value, array( 'font-color', 'font-family', 'font-size', 'font-style', 'font-variant', 'font-weight', 'letter-spacing', 'line-height', 'text-decoration', 'text-transform' ) ) ) {
              $font = array();
              
              if ( ! empty( $value['font-color'] ) )
                $font[] = "color: " . $value['font-color'] . ";";
              
              if ( ! empty( $value['font-family'] ) ) {
                foreach ( ot_recognized_font_families( $marker ) as $key => $v ) {
                  if ( $key == $value['font-family'] ) {
                    $font[] = "font-family: " . $v . ";";
                  }
                }
              }
              
              if ( ! empty( $value['font-size'] ) )
                $font[] = "font-size: " . $value['font-size'] . ";";
              
              if ( ! empty( $value['font-style'] ) )
                $font[] = "font-style: " . $value['font-style'] . ";";
              
              if ( ! empty( $value['font-variant'] ) )
                $font[] = "font-variant: " . $value['font-variant'] . ";";
              
              if ( ! empty( $value['font-weight'] ) )
                $font[] = "font-weight: " . $value['font-weight'] . ";";
                
              if ( ! empty( $value['letter-spacing'] ) )
                $font[] = "letter-spacing: " . $value['letter-spacing'] . ";";
              
              if ( ! empty( $value['line-height'] ) )
                $font[] = "line-height: " . $value['line-height'] . ";";
              
              if ( ! empty( $value['text-decoration'] ) )
                $font[] = "text-decoration: " . $value['text-decoration'] . ";";
              
              if ( ! empty( $value['text-transform'] ) )
                $font[] = "text-transform: " . $value['text-transform'] . ";";
              
              /* set $value with font properties or empty string */
              $value = ! empty( $font ) ? implode( "\n", $font ) : '';
              
            /* background */
            } else if ( ot_array_keys_exists( $value, array( 'background-color', 'background-image', 'background-repeat', 'background-attachment', 'background-position' ) ) ) {
              $bg = array();
              
              if ( ! empty( $value['background-color'] ) )
                $bg[] = $value['background-color'];
                
              if ( ! empty( $value['background-image'] ) )
                $bg[] = 'url("' . $value['background-image'] . '")';
                
              if ( ! empty( $value['background-repeat'] ) )
                $bg[] = $value['background-repeat'];
                
              if ( ! empty( $value['background-attachment'] ) )
                $bg[] = $value['background-attachment'];
                
              if ( ! empty( $value['background-position'] ) )
                $bg[] = $value['background-position'];
              
              /* set $value with background properties or empty string */
              $value = ! empty( $bg ) ? 'background: ' . implode( " ", $bg ) . ';' : '';
            }
          
          } else {
          
            $value = $value[$option_array[1]];
            
          }
         
        }
        
        // Filter the CSS
         $value = apply_filters( 'ot_insert_css_with_markers_value', $value, $option_id );
         
        /* insert CSS, even if the value is empty */
         $insertion = stripslashes( str_replace( $option, $value, $insertion ) );
         
      }
    
      /* create array from the lines of code */
      $markerdata = explode( "\n", implode( '', file( $filepath ) ) );
      
      /* can't write to the file return false */
      if ( ! $f = ot_file_open( $filepath, 'w' ) )
        return false;
      
      $searching = true;
      $foundit = false;
      
      /* has array of lines */
      if ( ! empty( $markerdata ) ) {
        
        /* foreach line of code */
        foreach( $markerdata as $n => $markerline ) {
          
          /* found begining of marker, set $searching to false  */
          if ( $markerline == "/* BEGIN {$marker} */" )
            $searching = false;
          
          /* keep rewrite each line of CSS  */
          if ( $searching == true ) {
            if ( $n + 1 < count( $markerdata ) )
              ot_file_write( $f, "{$markerline}\n" );
            else
              ot_file_write( $f, "{$markerline}" );
          }
          
          /* found end marker write code */
          if ( $markerline == "/* END {$marker} */" ) {
            ot_file_write( $f, "/* BEGIN {$marker} */\n" );
            ot_file_write( $f, "{$insertion}\n" );
            ot_file_write( $f, "/* END {$marker} */\n" );
            $searching = true;
            $foundit = true;
          }
          
        }
        
      }
      
      /* nothing inserted, write code. DO IT, DO IT! */
      if ( ! $foundit ) {
        ot_file_write( $f, "/* BEGIN {$marker} */\n" );
        ot_file_write( $f, "{$insertion}\n" );
        ot_file_write( $f, "/* END {$marker} */\n" );
      }
      
      /* close file */
      ot_file_close( $f );
      return true;
    }
    
    return false;

  }

}

/**
 * Remove old CSS.
 *
 * Removes CSS when the textarea is empty, but still retains surrounding styles.
 *
 * @param     string  $field_id The CSS option field ID.
 * @return    bool    True on write success, false on failure.
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_remove_old_css' ) ) {

  function ot_remove_old_css( $field_id = '' ) {
    
    /* missing $field_id string */
    if ( '' == $field_id )
      return false;
    
    /* path to the dynamic.css file */
    $filepath = get_stylesheet_directory() . '/dynamic.css';
    
    /* allow filter on path */
    $filepath = apply_filters( 'css_option_file_path', $filepath, $field_id );
    
    /* remove CSS from file */
    if ( is_writeable( $filepath ) ) {
      
      /* get each line in the file */
      $markerdata = explode( "\n", implode( '', file( $filepath ) ) );
      
      /* can't write to the file return false */
      if ( ! $f = ot_file_open( $filepath, 'w' ) )
        return false;
      
      $searching = true;
      
      /* has array of lines */
      if ( ! empty( $markerdata ) ) {
        
        /* foreach line of code */
        foreach ( $markerdata as $n => $markerline ) {
          
          /* found begining of marker, set $searching to false  */
          if ( $markerline == "/* BEGIN {$field_id} */" )
            $searching = false;
          
          /* $searching is true, keep rewrite each line of CSS  */
          if ( $searching == true ) {
            if ( $n + 1 < count( $markerdata ) )
              ot_file_write( $f, "{$markerline}\n" );
            else
              ot_file_write( $f, "{$markerline}" );
          }
          
          /* found end marker delete old CSS */
          if ( $markerline == "/* END {$field_id} */" ) {
            ot_file_write( $f, "" );
            $searching = true;
          }
          
        }
        
      }
      
      /* close file */
      ot_file_close( $f );
      return true;
      
    }
    
    return false;
    
  }
  
}

/**
 * Normalize CSS
 *
 * Normalize & Convert all line-endings to UNIX format.
 *
 * @param     string    $css
 * @return    string
 *
 * @access    public
 * @since     1.1.8
 * @updated   2.0
 */
if ( ! function_exists( 'ot_normalize_css' ) ) {

  function ot_normalize_css( $css ) {
    
    /* Normalize & Convert */
    $css = str_replace( "\r\n", "\n", $css );
    $css = str_replace( "\r", "\n", $css );
    
    /* Don't allow out-of-control blank lines */
    $css = preg_replace( "/\n{2,}/", "\n\n", $css );
    
    return $css;
  }
  
}

/**
 * Helper function to loop over the option types.
 *
 * @param    array    $type The current option type.
 *
 * @return   string
 *
 * @access   public
 * @since    2.0
 */
if ( ! function_exists( 'ot_loop_through_option_types' ) ) {

  function ot_loop_through_option_types( $type = '', $child = false ) {
  
    $content = '';
    $types = ot_option_types_array();
    
    if ( $child )
      unset($types['list-item']);
    
    foreach( $types as $key => $value )
      $content.= '<option value="' . $key . '" ' . selected( $type, $key, false ) . '>'  . $value . '</option>';
    
    return $content;
    
  }
  
}

/**
 * Helper function to loop over choices.
 *
 * @param    string     $name The form element name.
 * @param    array      $choices The array of choices.
 *
 * @return   string
 *
 * @access   public
 * @since    2.0
 */
if ( ! function_exists( 'ot_loop_through_choices' ) ) {

  function ot_loop_through_choices( $name, $choices = array() ) {
    
    $content = '';
    
    foreach( $choices as $key => $choice )
      $content.= '<li class="ui-state-default list-choice">' . ot_choices_view( $name, $key, $choice ) . '</li>';
    
    return $content;
  }
  
}

/**
 * Helper function to loop over sub settings.
 *
 * @param    string     $name The form element name.
 * @param    array      $settings The array of settings.
 *
 * @return   string
 *
 * @access   public
 * @since    2.0
 */
if ( ! function_exists( 'ot_loop_through_sub_settings' ) ) {

  function ot_loop_through_sub_settings( $name, $settings = array() ) {
    
    $content = '';
    
    foreach( $settings as $key => $setting )
      $content.= '<li class="ui-state-default list-sub-setting">' . ot_settings_view( $name, $key, $setting ) . '</li>';
    
    return $content;
  }
  
}

/**
 * Helper function to display sections.
 *
 * This function is used in AJAX to add a new section
 * and when section have already been added and saved.
 *
 * @param    int      $key The array key for the current element.
 * @param    array    An array of values for the current section.
 *
 * @return   void
 *
 * @access   public
 * @since    2.0
 */
if ( ! function_exists( 'ot_sections_view' ) ) {

  function ot_sections_view( $name, $key, $section = array() ) {
  
    return '
    <div class="option-tree-setting is-section">
      <div class="open">' . ( isset( $section['title'] ) ? esc_attr( $section['title'] ) : 'Section ' . ( $key + 1 ) ) . '</div>
      <div class="button-section">
        <a href="javascript:void(0);" class="option-tree-setting-edit option-tree-ui-button left-item" title="' . __( 'edit', 'option-tree' ) . '">
          <span class="icon pencil">' . __( 'Edit', 'option-tree' ) . '</span> 
        </a>
        <a href="javascript:void(0);" class="option-tree-setting-remove option-tree-ui-button red light right-item" title="' . __( 'Delete', 'option-tree' ) . '">
          <span class="icon trash-can">' . __( 'Delete', 'option-tree' ) . '</span>
        </a>
      </div>
      <div class="option-tree-setting-body">
        <div class="format-settings">
          <div class="format-setting type-text">
            <div class="description">' . __( '<strong>Section Title</strong>: Displayed as a menu item on the Theme Options page.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][title]" value="' . ( isset( $section['title'] ) ? esc_attr( $section['title'] ) : '' ) . '" class="widefat option-tree-ui-input option-tree-setting-title section-title" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text">
            <div class="description">' . __( '<strong>Section ID</strong>: A unique lower case alphanumeric string, underscores allowed.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][id]" value="' . ( isset( $section['id'] ) ? esc_attr( $section['id'] ) : '' ) . '" class="widefat option-tree-ui-input section-id" autocomplete="off" />
            </div>
          </div>
        </div>
      </div>
    </div>';
    
  }

}

/**
 * Helper function to display settings.
 *
 * This function is used in AJAX to add a new setting
 * and when settings have already been added and saved.
 *
 * @param    int      $key The array key for the current element.
 * @param    array    An array of values for the current section.
 *
 * @return   void
 *
 * @access   public
 * @since    2.0
 */
if ( ! function_exists( 'ot_settings_view' ) ) {

  function ot_settings_view( $name, $key, $setting = array() ) {
    
    $child = ( strpos( $name, '][settings]') !== false ) ? true : false;
    $type = isset( $setting['type'] ) ? $setting['type'] : '';
    $std = isset( $setting['std'] ) ? $setting['std'] : '';
    
    // Serialize the standard value just incase
    if ( is_array( $std ) ) {
      $std = maybe_serialize( $std );
    }
    
    if ( in_array( $type, array( 'textarea', 'textarea-simple', 'css' ) ) ) {
      $std_form_element = '<textarea class="textarea" rows="10" cols="40" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][std]">' . esc_html( $std ) . '</textarea>';
    } else {
      $std_form_element = '<input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][std]" value="' . esc_attr( $std ) . '" class="widefat option-tree-ui-input" autocomplete="off" />';
    }
    
    return '
    <div class="option-tree-setting">
      <div class="open">' . ( isset( $setting['label'] ) ? esc_attr( $setting['label'] ) : 'Setting ' . ( $key + 1 ) ) . '</div>
      <div class="button-section">
        <a href="javascript:void(0);" class="option-tree-setting-edit option-tree-ui-button left-item" title="' . __( 'Edit', 'option-tree' ) . '">
          <span class="icon pencil">' . __( 'Edit', 'option-tree' ) . '</span>
        </a>
        <a href="javascript:void(0);" class="option-tree-setting-remove option-tree-ui-button red light right-item" title="' . __( 'Delete', 'option-tree' ) . '">
          <span class="icon trash-can">' . __( 'Delete', 'option-tree' ) . '</span>
        </a>
      </div>
      <div class="option-tree-setting-body">
        <div class="format-settings">
          <div class="format-setting type-text wide-desc">
            <div class="description">' . __( '<strong>Label</strong>: Displayed as the label of a form element on the Theme Options page.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][label]" value="' . ( isset( $setting['label'] ) ? esc_attr( $setting['label'] ) : '' ) . '" class="widefat option-tree-ui-input option-tree-setting-title" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text wide-desc">
            <div class="description">' . __( '<strong>ID</strong>: A unique lower case alphanumeric string, underscores allowed.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][id]" value="' . ( isset( $setting['id'] ) ? esc_attr( $setting['id'] ) : '' ) . '" class="widefat option-tree-ui-input" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-select wide-desc">
            <div class="description">' . __( '<strong>Type</strong>: Choose one of the available option types from the dropdown.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <select name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][type]" value="' . esc_attr( $type ) . '" class="option-tree-ui-select">
              ' . ot_loop_through_option_types( $type, $child ) . '                     
               
              </select>
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-textarea wide-desc">
            <div class="description">' . __( '<strong>Description</strong>: Enter a detailed description for the users to read on the Theme Options page, HTML is allowed. This is also where you enter content for both the Textblock & Textblock Titled option types.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <textarea class="textarea" rows="10" cols="40" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][desc]">' . ( isset( $setting['desc'] ) ? esc_html( $setting['desc'] ) : '' ) . '</textarea>
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-textblock wide-desc">
            <div class="description">' . __( '<strong>Choices</strong>: This will only affect the following option types: Checkbox, Radio, Select & Select Image.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <ul class="option-tree-setting-wrap option-tree-sortable" data-name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . ']">
                ' . ( isset( $setting['choices'] ) ? ot_loop_through_choices( $name . '[' . $key . ']', $setting['choices'] ) : '' ) . '
              </ul>
              <a href="javascript:void(0);" class="option-tree-choice-add option-tree-ui-button hug-left">' . __( 'Add Choice', 'option-tree' ) . '</a>
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-textblock wide-desc">
            <div class="description">' . __( '<strong>Settings</strong>: This will only affect the List Item option type.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <ul class="option-tree-setting-wrap option-tree-sortable" data-name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . ']">
                ' . ( isset( $setting['settings'] ) ? ot_loop_through_sub_settings( $name . '[' . $key . '][settings]', $setting['settings'] ) : '' ) . '
              </ul>
              <a href="javascript:void(0);" class="option-tree-list-item-setting-add option-tree-ui-button hug-left">' . __( 'Add Setting', 'option-tree' ) . '</a>
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text wide-desc">
            <div class="description">' . __( '<strong>Standard</strong>: Setting the standard value for your option only works for some option types. Read the <code>OptionTree->Documentation</code> for more information on which ones.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              ' . $std_form_element . '
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text wide-desc">
            <div class="description">' . __( '<strong>Rows</strong>: Enter a numeric value for the number of rows in your textarea. This will only affect the following option types: CSS, Textarea, & Textarea Simple.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][rows]" value="' . ( isset( $setting['rows'] ) ? esc_attr( $setting['rows'] ) : '' ) . '" class="widefat option-tree-ui-input" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text wide-desc">
            <div class="description">' . __( '<strong>Post Type</strong>: Add a comma separated list of post type like \'post,page\'. This will only affect the following option types: Custom Post Type Checkbox, & Custom Post Type Select.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][post_type]" value="' . ( isset( $setting['post_type'] ) ? esc_attr( $setting['post_type'] ) : '' ) . '" class="widefat option-tree-ui-input" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text wide-desc">
            <div class="description">' . __( '<strong>Taxonomy</strong>: Add a comma separated list of any registered taxonomy like \'category,post_tag\'. This will only affect the following option types: Taxonomy Checkbox, & Taxonomy Select.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][taxonomy]" value="' . ( isset( $setting['taxonomy'] ) ? esc_attr( $setting['taxonomy'] ) : '' ) . '" class="widefat option-tree-ui-input" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text wide-desc">
            <div class="description">' . __( '<strong>Min, Max, & Step</strong>: Add a comma separated list of options in the following format <code>0,100,1</code> (slide from <code>0-100</code> in intervals of <code>1</code>). The three values represent the minimum, maximum, and step options and will only affect the Numeric Slider option type.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][min_max_step]" value="' . ( isset( $setting['min_max_step'] ) ? esc_attr( $setting['min_max_step'] ) : '' ) . '" class="widefat option-tree-ui-input" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text wide-desc">
            <div class="description">' . __( '<strong>CSS Class</strong>: Add and optional class to this option type.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][class]" value="' . ( isset( $setting['class'] ) ? esc_attr( $setting['class'] ) : '' ) . '" class="widefat option-tree-ui-input" autocomplete="off" />
            </div>
          </div>
        </div>
      </div>
    </div>
    ' . ( ! $child ? '<input type="hidden" class="hidden-section" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][section]" value="' . ( isset( $setting['section'] ) ? esc_attr( $setting['section'] ) : '' ) . '" />' : '' );
  
  }

}

/**
 * Helper function to display setting choices.
 *
 * This function is used in AJAX to add a new choice
 * and when choices have already been added and saved.
 *
 * @param    string   $name The form element name.
 * @param    array    $key The array key for the current element.
 * @param    array    An array of values for the current choice.
 *
 * @return   void
 *
 * @access   public
 * @since    2.0
 */
if ( ! function_exists( 'ot_choices_view' ) ) {

  function ot_choices_view( $name, $key, $choice = array() ) {
  
    return '
    <div class="option-tree-setting">
      <div class="open">' . ( isset( $choice['label'] ) ? esc_attr( $choice['label'] ) : 'Choice ' . ( $key + 1 ) ) . '</div>
      <div class="button-section">
        <a href="javascript:void(0);" class="option-tree-setting-edit option-tree-ui-button left-item" title="' . __( 'Edit', 'option-tree' ) . '">
          <span class="icon pencil">' . __( 'Edit', 'option-tree' ) . '</span>
        </a>
        <a href="javascript:void(0);" class="option-tree-setting-remove option-tree-ui-button red light right-item" title="' . __( 'Delete', 'option-tree' ) . '">
          <span class="icon trash-can">' . __( 'Delete', 'option-tree' ) . '</span>
        </a>
      </div>
      <div class="option-tree-setting-body">
        <div class="format-settings">
          <div class="format-setting-label">
            <h5>' . __( 'Label', 'option-tree' ) . '</h5>
          </div>
          <div class="format-setting type-text wide-desc">
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[choices][' . esc_attr( $key ) . '][label]" value="' . ( isset( $choice['label'] ) ? esc_attr( $choice['label'] ) : '' ) . '" class="widefat option-tree-ui-input option-tree-setting-title" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting-label">
            <h5>' . __( 'Value', 'option-tree' ) . '</h5>
          </div>
          <div class="format-setting type-text wide-desc">
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[choices][' . esc_attr( $key ) . '][value]" value="' . ( isset( $choice['value'] ) ? esc_attr( $choice['value'] ) : '' ) . '" class="widefat option-tree-ui-input" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting-label">
            <h5>' . __( 'Image Source (Radio Image only)', 'option-tree' ) . '</h5>
          </div>
          <div class="format-setting type-text wide-desc">
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[choices][' . esc_attr( $key ) . '][src]" value="' . ( isset( $choice['src'] ) ? esc_attr( $choice['src'] ) : '' ) . '" class="widefat option-tree-ui-input" autocomplete="off" />
            </div>
          </div>
        </div>
    </div>';
    
  }

}

/**
 * Helper function to display sections.
 *
 * This function is used in AJAX to add a new section
 * and when section have already been added and saved.
 *
 * @param    int      $key The array key for the current element.
 * @param    array    An array of values for the current section.
 *
 * @return   void
 *
 * @access   public
 * @since    2.0
 */
if ( ! function_exists( 'ot_contextual_help_view' ) ) {

  function ot_contextual_help_view( $name, $key, $content = array() ) {
  
    return '
    <div class="option-tree-setting">
      <div class="open">' . ( isset( $content['title'] ) ? esc_attr( $content['title'] ) : 'Content ' . ( $key + 1 ) ) . '</div>
      <div class="button-section">
        <a href="javascript:void(0);" class="option-tree-setting-edit option-tree-ui-button left-item" title="' . __( 'Edit', 'option-tree' ) . '">
          <span class="icon pencil">' . __( 'Edit', 'option-tree' ) . '</span>
        </a>
        <a href="javascript:void(0);" class="option-tree-setting-remove option-tree-ui-button red light right-item" title="' . __( 'Delete', 'option-tree' ) . '">
          <span class="icon trash-can">' . __( 'Delete', 'option-tree' ) . '</span>
        </a>
      </div>
      <div class="option-tree-setting-body">
        <div class="format-settings">
          <div class="format-setting type-text no-desc">
            <div class="description">' . __( '<strong>Title</strong>: Displayed as a contextual help menu item on the Theme Options page.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][title]" value="' . ( isset( $content['title'] ) ? esc_attr( $content['title'] ) : '' ) . '" class="widefat option-tree-ui-input option-tree-setting-title" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-text no-desc">
            <div class="description">' . __( '<strong>ID</strong>: A unique lower case alphanumeric string, underscores allowed.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <input type="text" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][id]" value="' . ( isset( $content['id'] ) ? esc_attr( $content['id'] ) : '' ) . '" class="widefat option-tree-ui-input" autocomplete="off" />
            </div>
          </div>
        </div>
        <div class="format-settings">
          <div class="format-setting type-textarea no-desc">
            <div class="description">' . __( '<strong>Content</strong>: Enter the HTML content about this contextual help item displayed on the Theme Option page for end users to read.', 'option-tree' ) . '</div>
            <div class="format-setting-inner">
              <textarea class="textarea" rows="15" cols="40" name="' . esc_attr( $name ) . '[' . esc_attr( $key ) . '][content]">' . ( isset( $content['content'] ) ? esc_html( $content['content'] ) : '' ) . '</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>';
    
  }

}

/**
 * Helper function to display sections.
 *
 * @param     string      $key
 * @param     string      $data
 * @param     string      $active_layout
 *
 * @return    void
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_layouts_view' ) ) {

  function ot_layout_view( $key, $data = '', $active_layout = '' ) {
  
    return '
    <div class="option-tree-setting">
      <div class="open">' . ( isset( $key ) ? esc_attr( $key ) : __( 'Layout', 'option-tree' ) ) . '</div>
      <div class="button-section">
        <a href="javascript:void(0);" class="option-tree-layout-activate option-tree-ui-button left-item' . ( $active_layout == $key ? ' active' : '' ) . '" title="' . __( 'Activate', 'option-tree' ) . '">
          <span class="icon check">' . __( 'Activate', 'option-tree' ) . '</span>
        </a>
        <a href="javascript:void(0);" class="option-tree-setting-remove option-tree-ui-button red light right-item" title="'. __( 'Delete', 'option-tree' ) . '">
          <span class="icon trash-can">' . __( 'Delete', 'option-tree' ) . '</span>
        </a>
      </div>
      <input type="hidden" name="option_tree_layouts[' . esc_attr( $key ) . ']" value="' . $data . '" />
    </div>';
    
  }

}

/**
 * Helper function to display list items.
 *
 * This function is used in AJAX to add a new list items
 * and when they have already been added and saved.
 *
 * @param     string    $name The form field name.
 * @param     int       $key The array key for the current element.
 * @param     array     An array of values for the current list item.
 *
 * @return   void
 *
 * @access   public
 * @since    2.0
 */
if ( ! function_exists( 'ot_list_item_view' ) ) {

  function ot_list_item_view( $name, $key, $list_item = array(), $post_id = 0, $get_option = '', $settings = array(), $type = '' ) {
    
    /* required title setting */
    $required_setting = array(
      array(
        'id'        => 'title',
        'label'     => __( 'Title', 'option-tree' ),
        'desc'      => '',
        'std'       => '',
        'type'      => 'text',
        'rows'      => '',
        'class'     => 'option-tree-setting-title',
        'post_type' => '',
        'choices'   => array()
      )
    );
    
    /* load the old filterable slider settings */
    if ( 'slider' == $type ) {
      
      $settings = ot_slider_settings( $name );
    
    }
      
    /* if no settings array load the filterable list item settings */
    if ( empty( $settings ) ) {
      
      $settings = ot_list_item_settings( $name );
      
    }
    
    /* merge the two settings array */
    $settings = array_merge( $required_setting, $settings );
    
    echo '
    <div class="option-tree-setting">
      <div class="open">' . ( isset( $list_item['title'] ) ? esc_attr( $list_item['title'] ) : '' ) . '</div>
      <div class="button-section">
        <a href="javascript:void(0);" class="option-tree-setting-edit option-tree-ui-button left-item" title="' . __( 'Edit', 'option-tree' ) . '">
          <span class="icon pencil">' . __( 'Edit', 'option-tree' ) . '</span>
        </a>
        <a href="javascript:void(0);" class="option-tree-setting-remove option-tree-ui-button red light right-item" title="' . __( 'Delete', 'option-tree' ) . '">
          <span class="icon trash-can">' . __( 'Delete', 'option-tree' ) . '</span>
        </a>
      </div>
      <div class="option-tree-setting-body">';
        
      foreach( $settings as $field ) {
        
        // Set field value
        $field_value = isset( $list_item[$field['id']] ) ? $list_item[$field['id']] : '';
        
        /* set default to standard value */
        if ( isset( $field['std'] ) ) {  
          $field_value = ot_filter_std_value( $field_value, $field['std'] );
        }
          
        /* make life easier */
        $_field_name = $get_option ? $get_option . '[' . $name . ']' : $name;
             
        /* build the arguments array */
        $_args = array(
          'type'              => $field['type'],
          'field_id'          => $name . '_' . $field['id'] . '_' . $key,
          'field_name'        => $_field_name . '[' . $key . '][' . $field['id'] . ']',
          'field_value'       => $field_value,
          'field_desc'        => isset( $field['desc'] ) ? $field['desc'] : '',
          'field_std'         => isset( $field['std'] ) ? $field['std'] : '',
          'field_rows'        => isset( $field['rows'] ) ? $field['rows'] : 10,
          'field_post_type'   => isset( $field['post_type'] ) && ! empty( $field['post_type'] ) ? $field['post_type'] : 'post',
          'field_taxonomy'    => isset( $field['taxonomy'] ) && ! empty( $field['taxonomy'] ) ? $field['taxonomy'] : 'category',
          'field_min_max_step'=> isset( $field['min_max_step'] ) && ! empty( $field['min_max_step'] ) ? $field['min_max_step'] : '0,100,1',
          'field_class'       => isset( $field['class'] ) ? $field['class'] : '',
          'field_choices'     => isset( $field['choices'] ) && ! empty( $field['choices'] ) ? $field['choices'] : array(),
          'field_settings'    => isset( $field['settings'] ) && ! empty( $field['settings'] ) ? $field['settings'] : array(),
          'post_id'           => $post_id,
          'get_option'        => $get_option
        );
          
        /* option label */
        echo '<div class="format-settings">';
          
        /* don't show title with textblocks */
        if ( $_args['type'] != 'textblock' && ! empty( $field['label'] ) ) {
          echo '<div class="format-setting-label">';
            echo '<h3 class="label">' . esc_attr( $field['label'] ) . '</h3>';
          echo '</div>';
        }
        
        /* only allow simple textarea inside a list-item due to known DOM issues with wp_editor() */
        if ( $_args['type'] == 'textarea' )
          $_args['type'] = 'textarea-simple';
          
        /* option body, list-item is not allowed inside another list-item */
        if ( $_args['type'] !== 'list-item' && $_args['type'] !== 'slider' ) {
          echo ot_display_by_type( $_args );
        }
        
        echo '</div>';
      
      }
        
    echo
      '</div>
    </div>';
    
  }
  
}

/**
 * Helper function to display Theme Options layouts form.
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_theme_options_layouts_form' ) ) {

  function ot_theme_options_layouts_form( $active = false ) {
    
    echo '<form method="post" id="option-tree-options-layouts-form">';
        
      /* form nonce */
      wp_nonce_field( 'option_tree_modify_layouts_form', 'option_tree_modify_layouts_nonce' );
        
      /* get the saved layouts */
      $layouts = get_option( 'option_tree_layouts' );
      
      /* set active layout */
      $active_layout = isset( $layouts['active_layout'] ) ? $layouts['active_layout'] : '';

      if ( is_array( $layouts ) && count( $layouts ) > 1 ) {
        
        $active_layout = esc_attr( $layouts['active_layout'] );
        
        echo '<input type="hidden" id="the_current_layout" value="' . $active_layout . '" />';
        
        echo '<div class="option-tree-active-layout">';
        
          echo '<select name="option_tree_layouts[active_layout]" class="option-tree-ui-select">';
      
            foreach( $layouts as $key => $data ) { 
              
              if ( $key == 'active_layout' )
                continue;
              
              echo '<option' . selected( $key, $active_layout, false ) . ' value="' . esc_attr( $key ) . '">' . esc_attr( $key ) . '</option>';
            }
           
          echo '</select>';
        
        echo '</div>';
         
        foreach( $layouts as $key => $data ) {
          
          if ( $key == 'active_layout' )
              continue;
              
          echo '<input type="hidden" name="option_tree_layouts[' . $key . ']" value="' . ( isset( $data ) ? $data : '' ) . '" />';
          
        }
       
      }
      
      /* new layout wrapper */
      echo '<div class="option-tree-save-layout' . ( ! empty( $active_layout ) ? ' active-layout' : '' ) . '">';
        
        /* add new layout */
        echo '<input type="text" name="option_tree_layouts[_add_new_layout_]" value="" class="widefat option-tree-ui-input" autocomplete="off" />';
        
        echo '<button type="submit" class="option-tree-ui-button blue save-layout" title="' . __( 'New Layout', 'option-tree' ) . '">' . __( 'New Layout', 'option-tree' ) . '</button>';
      
      echo '</div>';
      
    echo '</form>';
    
  }

}

/**
 * Helper function to validate option ID's
 *
 * @param     string      $input The string to sanitize.
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_sanitize_option_id' ) ) {

  function ot_sanitize_option_id( $input ) {
  
    return preg_replace( '/[^a-z0-9]/', '_', trim( strtolower( $input ) ) );
      
  }

}

/**
 * Helper function to validate layout ID's
 *
 * @param     string      $input The string to sanitize.
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_sanitize_layout_id' ) ) {

  function ot_sanitize_layout_id( $input ) {
  
    return preg_replace( '/[^a-z0-9]/', '-', trim( strtolower( $input ) ) );
      
  }

}

/**
 * Convert choices array to string
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_convert_array_to_string' ) ) {

  function ot_convert_array_to_string( $input ) {

    if ( is_array( $input ) ) {

      foreach( $input as $k => $choice ) {
        $choices[$k] = $choice['value'] . '|' . $choice['label'];
        
        if ( isset( $choice['src'] ) )
          $choices[$k].= '|' . $choice['src'];
          
      }
      
      return implode( ',', $choices );
    }
    
    return false;
  }
}

/**
 * Convert choices string to array
 *
 * @return    array
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_convert_string_to_array' ) ) {

  function ot_convert_string_to_array( $input ) {
    
    if ( '' !== $input ) {
    
      /* empty choices array */
      $choices = array();
      
      /* exlode the string into an array */
      foreach( explode( ',', $input ) as $k => $choice ) {
        
        /* if ":" is splitting the string go deeper */
        if ( preg_match( '/\|/', $choice ) ) {
          $split = explode( '|', $choice );
          $choices[$k]['value'] = trim( $split[0] );
          $choices[$k]['label'] = trim( $split[1] );
          
          /* if radio image there are three values */
          if ( isset( $split[2] ) )
            $choices[$k]['src'] = trim( $split[2] );
            
        } else {
          $choices[$k]['value'] = trim( $choice );
          $choices[$k]['label'] = trim( $choice );
        }
        
      }
      
      /* return a formated choices array */
      return $choices;
    
    }
    
    return false;
    
  }
}

/**
 * Helper function - strpos() with arrays.
 *
 * @param     string    $haystack
 * @param     array     $needles
 * @return    bool
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_strpos_array' ) ) {

  function ot_strpos_array( $haystack, $needles = array() ) {
  
    foreach( $needles as $needle ) {
      $pos = strpos( $haystack, $needle );
      if ( $pos !== false ) {
        return true;
      }
    }
    
    return false;
  }

}

/**
 * Helper function - strpos() with arrays.
 *
 * @param     string    $haystack
 * @param     array     $needles
 * @return    bool
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_array_keys_exists' ) ) {
  
  function ot_array_keys_exists( $array, $keys ) {
    
    foreach($keys as $k) {
      if ( isset($array[$k]) ) {
        return true;
      }
    }
    
    return false;
  }
  
}

/**
 * Custom stripslashes from single value or array.
 *
 * @param       mixed   $input
 * @return      mixed
 *
 * @access      public
 * @since       2.0
 */
if ( ! function_exists( 'ot_stripslashes' ) ) {

  function ot_stripslashes( $input ) {
  
    if ( is_array( $input ) ) {
    
      foreach( $input as &$val ) {
      
        if ( is_array( $val ) ) {
        
          $val = ot_stripslashes( $val );
          
        } else {
        
          $val = stripslashes( trim( $val ) );
          
        }
        
      }
      
    } else {
    
      $input = stripslashes( trim( $input ) );
      
    }
    
    return $input;
    
  }

}

/**
 * Reverse wpautop.
 *
 * @param     string    $string The string to be filtered
 * @return    string
 *
 * @access    public
 * @since     2.0.9
 */
if ( ! function_exists( 'ot_reverse_wpautop' ) ) {

  function ot_reverse_wpautop( $string = '' ) {
    
    /* return if string is empty */
    if ( trim( $string ) === '' )
      return '';
      
    /* remove all new lines & <p> tags */
    $string = str_replace( array( "\n", "<p>" ), "", $string );
  
    /* replace <br /> with \r */
    $string = str_replace( array( "<br />", "<br>", "<br/>" ), "\r", $string );
  
    /* replace </p> with \r\n */
    $string = str_replace( "</p>", "\r\n", $string );
    
    /* return clean string */
    return trim( $string );
                
  }

}

/**
 * Returns an array of elements from start to limit, inclusive.
 *
 * Occasionally zero will be some impossibly large number to 
 * the "E" power when creating a range from negative to positive.
 * This function attempts to fix that by setting that number back to "0".
 *
 * @param     string    $start First value of the sequence.
 * @param     string    $limit The sequence is ended upon reaching the limit value.
 * @param     string    $step If a step value is given, it will be used as the increment 
 *                      between elements in the sequence. step should be given as a 
 *                      positive number. If not specified, step will default to 1.
 * @return    array
 *
 * @access    public
 * @since     2.0.12
 */
function ot_range( $start, $limit, $step = 1 ) {
  
  if ( $step < 0 )
    $step = 1;
    
  $range = range( $start, $limit, $step );
  
  foreach( $range as $k => $v ) {
    if ( strpos( $v, 'E' ) ) {
      $range[$k] = 0;
    }
  }
  
  return $range;
}

/**
 * Helper function to return encoded strings
 *
 * @return    string
 *
 * @access    public
 * @since     2.0.13
 */
function ot_encode( $value ) {

  $func = 'base64' . '_encode';
  return $func( $value );
  
}

/**
 * Helper function to return decoded strings
 *
 * @return    string
 *
 * @access    public
 * @since     2.0.13
 */
function ot_decode( $value ) {

  $func = 'base64' . '_decode';
  return $func( $value );
  
}

/**
 * Helper function to open a file
 *
 * @access    public
 * @since     2.0.13
 */
function ot_file_open( $handle, $mode ) {

  $func = 'f' . 'open';
  return @$func( $handle, $mode );
  
}

/**
 * Helper function to close a file
 *
 * @access    public
 * @since     2.0.13
 */
function ot_file_close( $handle ) {

  $func = 'f' . 'close';
  return $func( $handle );
  
}

/**
 * Helper function to write to an open file
 *
 * @access    public
 * @since     2.0.13
 */
function ot_file_write( $handle, $string ) {

  $func = 'f' . 'write';
  return $func( $handle, $string );
  
}

/**
 * Helper function to filter standard option values.
 *
 * @param     mixed     $value Saved string or array value
 * @param     mixed     $std Standard string or array value
 * @return    mixed     String or array
 *
 * @access    public
 * @since     2.0.15
 */
function ot_filter_std_value( $value = '', $std = '' ) {
  
  $std = maybe_unserialize( $std );
  
  if ( is_array( $value ) && is_array( $std ) ) {
  
    foreach( $value as $k => $v ) {
      
      if ( '' == $value[$k] && isset( $std[$k] ) ) {
      
        $value[$k] = $std[$k];
        
      }
      
    }
  
  } else if ( '' == $value && ! empty( $std ) ) {
  
    $value = $std;
    
  }

  return $value;
  
}

/**
 * Helper function to register a WPML string
 *
 * @access    public
 * @since     2.1
 */
function ot_wpml_register_string( $id, $value ) {

  if ( function_exists( 'icl_register_string' ) ) {
      
    icl_register_string( 'Theme Options', $id, $value );
      
  }
  
}

/**
 * Helper function to unregister a WPML string
 *
 * @access    public
 * @since     2.1
 */
function ot_wpml_unregister_string( $id ) {

  if ( function_exists( 'icl_unregister_string' ) ) {
      
    icl_unregister_string( 'Theme Options', $id );
      
  }
  
}

/* End of file ot-functions-admin.php */
/* Location: ./includes/ot-functions-admin.php */