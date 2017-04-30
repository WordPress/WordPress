<?php
/**
 * Plugin Name: OptionTree
 * Plugin URI:  https://github.com/valendesigns/option-tree/
 * Description: Theme Options UI Builder for WordPress. A simple way to create & save Theme Options and Meta Boxes for free or premium themes.
 * Version:     2.4.0
 * Author:      Derek Herman
 * Author URI:  http://valendesigns.com
 * License:     GPLv3
 */

/**
 * Forces Plugin Mode when OptionTree is already loaded and displays an admin notice.
 */
if ( class_exists( 'OT_Loader' ) && defined( 'OT_PLUGIN_MODE' ) && OT_PLUGIN_MODE == true ) {
  
  add_filter( 'ot_theme_mode', '__return_false', 999 );
  
  function ot_conflict_notice() {
    
    echo '<div class="error"><p>' . __( 'OptionTree is installed as a plugin and also embedded in your current theme. Please deactivate the plugin to load the theme dependent version of OptionTree, and remove this warning.', 'option-tree' ) . '</p></div>';
    
  }
  
  add_action( 'admin_notices', 'ot_conflict_notice' );
  
}

/**
 * This is the OptionTree loader class.
 *
 * @package   OptionTree
 * @author    Derek Herman <derek@valendesigns.com>
 * @copyright Copyright (c) 2013, Derek Herman
 */
if ( ! class_exists( 'OT_Loader' ) ) {

  class OT_Loader {
    
    /**
     * PHP5 constructor method.
     *
     * This method loads other methods of the class.
     *
     * @return    void
     *
     * @access    public
     * @since     2.0
     */
    public function __construct() {
      
      /* load languages */
      $this->load_languages();
      
      /* load OptionTree */
      add_action( 'after_setup_theme', array( $this, 'load_option_tree' ), 1 );
      
    }
    
    /**
     * Load the languages before everything else.
     *
     * @return    void
     *
     * @access    private
     * @since     2.1.3
     */
    private function load_languages() {
    
      /**
       * A quick check to see if we're in plugin mode.
       *
       * @since     2.1.3
       */
      define( 'OT_PLUGIN_MODE', strpos( dirname( __FILE__ ), 'plugins' . DIRECTORY_SEPARATOR . basename( dirname( __FILE__ ) ) ) !== false ? true : false );
      
      /**
       * Path to the languages directory. 
       *
       * This path will be relative in plugin mode and absolute in theme mode.
       *
       * @since     2.0.10
       */
      define( 'OT_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR );

      /* load the text domain  */
      if ( OT_PLUGIN_MODE ) {
      
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        
      } else {
      
        add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );
        
      }
      
    }
    
    /**
     * Load the text domain.
     *
     * @return    void
     *
     * @access    private
     * @since     2.0
     */
    public function load_textdomain() {
    
      if ( OT_PLUGIN_MODE ) {
      
        load_plugin_textdomain( 'option-tree', false, OT_LANG_DIR );
        
      } else {
      
        load_theme_textdomain( 'option-tree', OT_LANG_DIR . 'theme-mode' );
        
      }
      
    }
    
    /** 
     * Load OptionTree on the 'after_setup_theme' action. Then filters will 
     * be availble to the theme, and not only when in Theme Mode.
     *
     * @return    void
     *
     * @access    public
     * @since     2.1.2
     */
    public function load_option_tree() {
    
      /* setup the constants */
      $this->constants();
      
      /* include the required admin files */
      $this->admin_includes();
      
      /* include the required files */
      $this->includes();
      
      /* hook into WordPress */
      $this->hooks();
      
    }

    /**
     * Constants
     *
     * Defines the constants for use within OptionTree. Constants 
     * are prefixed with 'OT_' to avoid any naming collisions.
     *
     * @return    void
     *
     * @access    private
     * @since     2.0
     */
    private function constants() {
      
      /**
       * Current Version number.
       */
      define( 'OT_VERSION', '2.4.0' );
      
      /**
       * For developers: Theme mode.
       *
       * Run a filter and set to true to enable OptionTree theme mode.
       * You must have this files parent directory inside of 
       * your themes root directory. As well, you must include 
       * a reference to this file in your themes functions.php.
       *
       * @since     2.0
       */
      define( 'OT_THEME_MODE', apply_filters( 'ot_theme_mode', false ) );
      
      /**
       * For developers: Child Theme mode. TODO document
       *
       * Run a filter and set to true to enable OptionTree child theme mode.
       * You must have this files parent directory inside of 
       * your themes root directory. As well, you must include 
       * a reference to this file in your themes functions.php.
       *
       * @since     2.0.15
       */
      define( 'OT_CHILD_THEME_MODE', apply_filters( 'ot_child_theme_mode', false ) );
      
      /**
       * For developers: Show Pages.
       *
       * Run a filter and set to false if you don't want to load the
       * settings & documentation pages in the admin area of WordPress.
       *
       * @since     2.0
       */
      define( 'OT_SHOW_PAGES', apply_filters( 'ot_show_pages', true ) );
      
      /**
       * For developers: Show Theme Options UI Builder
       *
       * Run a filter and set to false if you want to hide the
       * Theme Options UI page in the admin area of WordPress.
       *
       * @since     2.1
       */
      define( 'OT_SHOW_OPTIONS_UI', apply_filters( 'ot_show_options_ui', true ) );
      
      /**
       * For developers: Show Settings Import
       *
       * Run a filter and set to false if you want to hide the
       * Settings Import options on the Import page.
       *
       * @since     2.1
       */
      define( 'OT_SHOW_SETTINGS_IMPORT', apply_filters( 'ot_show_settings_import', true ) );
      
      /**
       * For developers: Show Settings Export
       *
       * Run a filter and set to false if you want to hide the
       * Settings Import options on the Import page.
       *
       * @since     2.1
       */
      define( 'OT_SHOW_SETTINGS_EXPORT', apply_filters( 'ot_show_settings_export', true ) );
      
      /**
       * For developers: Show New Layout.
       *
       * Run a filter and set to false if you don't want to show the
       * "New Layout" section at the top of the theme options page.
       *
       * @since     2.0.10
       */
      define( 'OT_SHOW_NEW_LAYOUT', apply_filters( 'ot_show_new_layout', true ) );
      
      /**
       * For developers: Show Documentation
       *
       * Run a filter and set to false if you want to hide the Documentation.
       *
       * @since     2.1
       */
      define( 'OT_SHOW_DOCS', apply_filters( 'ot_show_docs', true ) );
      
      /**
       * For developers: Custom Theme Option page
       *
       * Run a filter and set to false if you want to hide the OptionTree 
       * Theme Option page and build your own.
       *
       * @since     2.1
       */
      define( 'OT_USE_THEME_OPTIONS', apply_filters( 'ot_use_theme_options', true ) );
      
      /**
       * For developers: Meta Boxes.
       *
       * Run a filter and set to false to keep OptionTree from
       * loading the meta box resources.
       *
       * @since     2.0
       */
      define( 'OT_META_BOXES', apply_filters( 'ot_meta_boxes', true ) );
      
      /**
       * For developers: Allow Unfiltered HTML in all the textareas.
       *
       * Run a filter and set to true if you want all the
       * users to be able to post anything in the textareas.
       * WARNING: This opens a security hole for low level users
       * to be able to post malicious scripts, you've been warned.
       *
       * @since     2.0
       */
      define( 'OT_ALLOW_UNFILTERED_HTML', apply_filters( 'ot_allow_unfiltered_html', false ) );

      /**
       * For developers: Post Formats.
       *
       * Run a filter and set to true if you want OptionTree 
       * to load meta boxes for post formats.
       *
       * @since     2.4.0
       */
      define( 'OT_POST_FORMATS', apply_filters( 'ot_post_formats', false ) );
      
      /**
       * Check if in theme mode.
       *
       * If OT_THEME_MODE and OT_CHILD_THEME_MODE is false, set the 
       * directory path & URL like any other plugin. Otherwise, use 
       * the parent or child themes root directory. 
       *
       * @since     2.0
       */
      if ( false == OT_THEME_MODE && false == OT_CHILD_THEME_MODE ) {
        define( 'OT_DIR', plugin_dir_path( __FILE__ ) );
        define( 'OT_URL', plugin_dir_url( __FILE__ ) );
      } else {
        if ( true == OT_CHILD_THEME_MODE ) {
          $path = ltrim( end( @explode( get_stylesheet(), str_replace( '\\', '/', dirname( __FILE__ ) ) ) ), '/' );
          define( 'OT_DIR', trailingslashit( trailingslashit( get_stylesheet_directory() ) . $path ) );
          define( 'OT_URL', trailingslashit( trailingslashit( get_stylesheet_directory_uri() ) . $path ) );
        } else {
          $path = ltrim( end( @explode( get_template(), str_replace( '\\', '/', dirname( __FILE__ ) ) ) ), '/' );
          define( 'OT_DIR', trailingslashit( trailingslashit( get_template_directory() ) . $path ) );
          define( 'OT_URL', trailingslashit( trailingslashit( get_template_directory_uri() ) . $path ) );
        }
      }
      
      /**
       * Template directory URI for the current theme.
       *
       * @since     2.1
       */
      if ( true == OT_CHILD_THEME_MODE ) {
        define( 'OT_THEME_URL', get_stylesheet_directory_uri() );
      } else {
        define( 'OT_THEME_URL', get_template_directory_uri() );
      }
      
    }
    
    /**
     * Include admin files
     *
     * These functions are included on admin pages only.
     *
     * @return    void
     *
     * @access    private
     * @since     2.0
     */
    private function admin_includes() {
      
      /* exit early if we're not on an admin page */
      if ( ! is_admin() )
        return false;
      
      /* global include files */
      $files = array( 
        'ot-functions-admin',
        'ot-functions-option-types',
        'ot-functions-compat',
        'ot-settings-api'
      );
      
      /* include the meta box api */
      if ( OT_META_BOXES == true ) {
        $files[] = 'ot-meta-box-api';
      }
      
      /* include the post formats api */
      if ( OT_META_BOXES == true && OT_POST_FORMATS == true ) {
        $files[] = 'ot-post-formats-api';
      }
      
      /* include the settings & docs pages */
      if ( OT_SHOW_PAGES == true ) {
        $files[] = 'ot-functions-settings-page';
        $files[] = 'ot-functions-docs-page';
      }
      
      /* require the files */
      foreach ( $files as $file ) {
        $this->load_file( OT_DIR . "includes" . DIRECTORY_SEPARATOR . "{$file}.php" );
      }
      
      /* Registers the Theme Option page */
      add_action( 'init', 'ot_register_theme_options_page' );
      
      /* Registers the Settings page */
      if ( OT_SHOW_PAGES == true ) {
        add_action( 'init', 'ot_register_settings_page' );
      }
      
    }
    
    /**
     * Include front-end files
     *
     * These functions are included on every page load 
     * incase other plugins need to access them.
     *
     * @return    void
     *
     * @access    private
     * @since     2.0
     */
    private function includes() {
    
      $files = array( 
        'ot-functions',
        'ot-functions-deprecated'
      );

      /* require the files */
      foreach ( $files as $file ) {
        $this->load_file( OT_DIR . "includes" . DIRECTORY_SEPARATOR . "{$file}.php" );
      }
      
    }
    
    /**
     * Execute the WordPress Hooks
     *
     * @return    void
     *
     * @access    public
     * @since     2.0
     */
    private function hooks() {
      
      // Attempt to migrate the settings
      if ( function_exists( 'ot_maybe_migrate_settings' ) )
        add_action( 'init', 'ot_maybe_migrate_settings', 1 );
      
      // Attempt to migrate the Options
      if ( function_exists( 'ot_maybe_migrate_options' ) )
        add_action( 'init', 'ot_maybe_migrate_options', 1 );
      
      // Attempt to migrate the Layouts
      if ( function_exists( 'ot_maybe_migrate_layouts' ) )
        add_action( 'init', 'ot_maybe_migrate_layouts', 1 );

      /* load the Meta Box assets */
      if ( OT_META_BOXES == true ) {
      
        /* add scripts for metaboxes to post-new.php & post.php */
        add_action( 'admin_print_scripts-post-new.php', 'ot_admin_scripts', 11 );
        add_action( 'admin_print_scripts-post.php', 'ot_admin_scripts', 11 );
              
        /* add styles for metaboxes to post-new.php & post.php */
        add_action( 'admin_print_styles-post-new.php', 'ot_admin_styles', 11 );
        add_action( 'admin_print_styles-post.php', 'ot_admin_styles', 11 );
      
      }
      
      /* Adds the Theme Option page to the admin bar */
      add_action( 'admin_bar_menu', 'ot_register_theme_options_admin_bar_menu', 999 );
      
      /* prepares the after save do_action */
      add_action( 'admin_init', 'ot_after_theme_options_save', 1 );
      
      /* default settings */
      add_action( 'admin_init', 'ot_default_settings', 2 );
      
      /* add xml to upload filetypes array */
      add_action( 'admin_init', 'ot_add_xml_to_upload_filetypes', 3 );
      
      /* import */
      add_action( 'admin_init', 'ot_import', 4 );
      
      /* export */
      add_action( 'admin_init', 'ot_export', 5 );
      
      /* save settings */
      add_action( 'admin_init', 'ot_save_settings', 6 );
      
      /* save layouts */
      add_action( 'admin_init', 'ot_modify_layouts', 7 );
      
      /* create media post */
      add_action( 'admin_init', 'ot_create_media_post', 8 );
      
      /* global CSS */
      add_action( 'admin_head', array( $this, 'global_admin_css' ) );
      
      /* dynamic front-end CSS */
      add_action( 'wp_enqueue_scripts', 'ot_load_dynamic_css', 999 );

      /* insert theme CSS dynamically */
      add_action( 'ot_after_theme_options_save', 'ot_save_css' );
      
      /* AJAX call to create a new section */
      add_action( 'wp_ajax_add_section', array( $this, 'add_section' ) );
      
      /* AJAX call to create a new setting */
      add_action( 'wp_ajax_add_setting', array( $this, 'add_setting' ) );
      
      /* AJAX call to create a new contextual help */
      add_action( 'wp_ajax_add_the_contextual_help', array( $this, 'add_the_contextual_help' ) );
      
      /* AJAX call to create a new choice */
      add_action( 'wp_ajax_add_choice', array( $this, 'add_choice' ) );
      
      /* AJAX call to create a new list item setting */
      add_action( 'wp_ajax_add_list_item_setting', array( $this, 'add_list_item_setting' ) );
      
      /* AJAX call to create a new layout */
      add_action( 'wp_ajax_add_layout', array( $this, 'add_layout' ) );
      
      /* AJAX call to create a new list item */
      add_action( 'wp_ajax_add_list_item', array( $this, 'add_list_item' ) );
      
      /* AJAX call to create a new social link */
      add_action( 'wp_ajax_add_social_links', array( $this, 'add_social_links' ) );
      
      // Adds the temporary hacktastic shortcode
      add_filter( 'media_view_settings', array( $this, 'shortcode' ), 10, 2 );
    
      // AJAX update
      add_action( 'wp_ajax_gallery_update', array( $this, 'ajax_gallery_update' ) );
      
      /* Modify the media uploader button */
      add_filter( 'gettext', array( $this, 'change_image_button' ), 10, 3 );
      
    }
    
    /**
     * Load a file
     *
     * @return    void
     *
     * @access    private
     * @since     2.0.15
     */
    private function load_file( $file ){
      
      include_once( $file );
      
    }
    
    /**
     * Adds the global CSS to fix the menu icon.
     */
    public function global_admin_css() {
      global $wp_version;
      
      $wp_38plus = version_compare( $wp_version, '3.8', '>=' ) ? true : false;
      $fontsize = $wp_38plus ? '20px' : '16px';
      $wp_38minus = '';
      
      if ( ! $wp_38plus ) {
        $wp_38minus = '
        #adminmenu #toplevel_page_ot-settings .menu-icon-generic div.wp-menu-image {
          background: none;
        }
        #adminmenu #toplevel_page_ot-settings .menu-icon-generic div.wp-menu-image:before {
          padding-left: 6px;
        }';
      }

      echo '
      <style>
        @font-face {
          font-family: "option-tree-font";
          src:url("' . OT_URL . 'assets/fonts/option-tree-font.eot");
          src:url("' . OT_URL . 'assets/fonts/option-tree-font.eot?#iefix") format("embedded-opentype"),
            url("' . OT_URL . 'assets/fonts/option-tree-font.woff") format("woff"),
            url("' . OT_URL . 'assets/fonts/option-tree-font.ttf") format("truetype"),
            url("' . OT_URL . 'assets/fonts/option-tree-font.svg#option-tree-font") format("svg");
          font-weight: normal;
          font-style: normal;
        }
        #adminmenu #toplevel_page_ot-settings .menu-icon-generic div.wp-menu-image:before,
        #option-tree-header #option-tree-logo a:before {
          font: normal ' . $fontsize . '/1 "option-tree-font" !important;
          speak: none;
          padding: 6px 0;
          height: 34px;
          width: 20px;
          display: inline-block;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
          -webkit-transition: all .1s ease-in-out;
          -moz-transition:    all .1s ease-in-out;
          transition:         all .1s ease-in-out;
        }
        #adminmenu #toplevel_page_ot-settings .menu-icon-generic div.wp-menu-image:before,
        #option-tree-header #option-tree-logo a:before {
          content: "\e785";
        }
        #option-tree-header #option-tree-logo a:before {
          font-size: 20px !important;
          height: 24px;
          padding: 2px 0;
        }'  . $wp_38minus . '
      </style>
      ';
    }
    
    /**
     * AJAX utility function for adding a new section.
     */
    public function add_section() {
      echo ot_sections_view( ot_settings_id() . '[sections]', $_REQUEST['count'] );
      die();
    }
    
    /**
     * AJAX utility function for adding a new setting.
     */
    public function add_setting() {
      echo ot_settings_view( $_REQUEST['name'], $_REQUEST['count'] );
      die();
    }
    
    /**
     * AJAX utility function for adding a new list item setting.
     */
    public function add_list_item_setting() {
      echo ot_settings_view( $_REQUEST['name'] . '[settings]', $_REQUEST['count'] );
      die();
    }
    
    /**
     * AJAX utility function for adding new contextual help content.
     */
    public function add_the_contextual_help() {
      echo ot_contextual_help_view( $_REQUEST['name'], $_REQUEST['count'] );
      die();
    }
    
    /**
     * AJAX utility function for adding a new choice.
     */
    public function add_choice() {
      echo ot_choices_view( $_REQUEST['name'], $_REQUEST['count'] );
      die();
    }
    
    /**
     * AJAX utility function for adding a new layout.
     */
    public function add_layout() {
      echo ot_layout_view( $_REQUEST['count'] );
      die();
    }
    
    /**
     * AJAX utility function for adding a new list item.
     */
    public function add_list_item() {
      ot_list_item_view( $_REQUEST['name'], $_REQUEST['count'], array(), $_REQUEST['post_id'], $_REQUEST['get_option'], unserialize( ot_decode( $_REQUEST['settings'] ) ), $_REQUEST['type'] );
      die();
    }
    
    /**
     * AJAX utility function for adding a new social link.
     */
    public function add_social_links() {
      ot_social_links_view( $_REQUEST['name'], $_REQUEST['count'], array(), $_REQUEST['post_id'], $_REQUEST['get_option'], unserialize( ot_decode( $_REQUEST['settings'] ) ), $_REQUEST['type'] );
      die();
    }
    
    /**
     * Fake the gallery shortcode
     *
     * The JS takes over and creates the actual shortcode with 
     * the real attachment IDs on the fly. Here we just need to 
     * pass in the post ID to get the ball rolling.
     *
     * @param     array     The current settings
     * @param     object    The post object
     * @return    array
     *
     * @access    public
     * @since     2.2.0
     */
    public function shortcode( $settings, $post ) {
  
      // Set the OptionTree post ID
      if ( ! is_object( $post ) )
        $settings['post']['id'] = ot_get_media_post_ID();
      
      // No ID return settings
      if ( $settings['post']['id'] == 0 )
        return $settings;
  
      // Set the fake shortcode
      $settings['ot_gallery'] = array( 'shortcode' => "[gallery id='{$settings['post']['id']}']" );
      
      // Return settings
      return $settings;
      
    }
    
    /**
     * Returns the AJAX images
     *
     * @return    string
     *
     * @access    public
     * @since     2.2.0
     */
    public function ajax_gallery_update() {
    
      if ( ! empty( $_POST['ids'] ) )  {
        
        $return = '';
        
        foreach( $_POST['ids'] as $id ) {
        
          $thumbnail = wp_get_attachment_image_src( $id, 'thumbnail' );
          
          $return .= '<li><img  src="' . $thumbnail[0] . '" width="75" height="75" /></li>';
          
        }
        
        echo $return;
        exit();
      
      }
      
    }
    
    /**
     * Filters the media uploader button.
     *
     * @return    string
     *
     * @access    public
     * @since     2.1
     */
    public function change_image_button( $translation, $text, $domain ) {
      global $pagenow;
    
      if ( $pagenow == 'themes.php' && 'default' == $domain && 'Insert into post' == $text ) {
        
        // Once is enough.
        remove_filter( 'gettext', array( $this, 'ot_change_image_button' ) );
        return apply_filters( 'ot_upload_text', __( 'Send to OptionTree', 'option-tree' ) );
        
      }
      
      return $translation;
      
    }
    
    
  }
  
  /**
   * Instantiate the OptionTree loader class.
   *
   * @since     2.0
   */
  $ot_loader = new OT_Loader();

}

/* End of file ot-loader.php */
/* Location: ./ot-loader.php */