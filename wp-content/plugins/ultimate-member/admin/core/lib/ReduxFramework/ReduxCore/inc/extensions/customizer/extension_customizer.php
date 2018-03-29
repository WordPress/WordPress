<?php
    // <input type="radio" value="1" name="_customize-radio-redux_demo[opt-radio]" data-customize-setting-link="redux_demo[opt-color-title]">
    //return;
    /**
     * Redux Framework is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 2 of the License, or
     * any later version.
     * Redux Framework is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     * You should have received a copy of the GNU General Public License
     * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
     *
     * @package     ReduxFramework
     * @author      Dovy Paukstys (dovy)
     * @version     0.1.0
     */

    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    // Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_extension_customizer' ) ) {

        /**
         * Main ReduxFramework customizer extension class
         *
         * @since       1.0.0
         */
        class ReduxFramework_extension_customizer {

            // Protected vars
            protected $redux;
            private $_extension_url;
            private $_extension_dir;
            private $parent;
            private $orig_options = array();
            private static $post_values = array();
            public static $version = "2.0.0";
            private $options = array();

            /**
             * Class Constructor. Defines the args for the extions class
             *
             * @since       1.0.0
             * @access      public
             *
             * @param       array $sections   Panel sections.
             * @param       array $args       Class constructor arguments.
             * @param       array $extra_tabs Extra panel tabs.
             *
             * @return      void
             */
            public function __construct( $parent ) {

                $this->parent = $parent;

                $this->upload_dir = ReduxFramework::$_upload_dir . 'advanced-customizer/';

                //add_action('wp_head', array( $this, '_enqueue_new' ));
                if ( $parent->args['customizer'] == false ) {
                    return;
                }

                // Override the ReduxCore class
                add_filter( "redux/extension/{$this->parent->args['opt_name']}/customizer", array(
                    $this,
                    'remove_core_customizer_class'
                ) );

                global $pagenow, $wp_customize;
                if ( ! isset( $wp_customize ) && $pagenow !== "customize.php" && $pagenow !== "admin-ajax.php" ) {
                    return;
                }
                if ( ( $pagenow !== "customize.php" && $pagenow !== "admin-ajax.php" && ! isset( $GLOBALS['wp_customize'] ) ) ) {
                    //return;
                }

                if ( empty( $this->_extension_dir ) ) {
                    $this->_extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                    $this->_extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->_extension_dir ) );
                }

                self::get_post_values();

                // Create defaults array
                $defaults = array();
                /*
                  customize_controls_init
                  customize_controls_enqueue_scripts
                  customize_controls_print_styles
                  customize_controls_print_scripts
                  customize_controls_print_footer_scripts
                 */

                //add_action('customize_save', );

                if ( isset( $_POST['wp_customize'] ) && $_POST['wp_customize'] == "on" ) {
                    $this->parent->args['customizer_only'] = true;
                }

                if ( isset( $_POST['wp_customize'] ) && $_POST['wp_customize'] == "on" && isset( $_POST['customized'] ) && ! empty( $_POST['customized'] ) && ! isset( $_POST['action'] ) ) {
                    add_action( "redux/options/{$this->parent->args['opt_name']}/options", array(
                        $this,
                        '_override_values'
                    ), 100 );
                }

                add_action( 'customize_register', array(
                    $this,
                    '_register_customizer_controls'
                ) ); // Create controls

                add_action( 'wp_head', array( $this, 'customize_preview_init' ) );


                //add_action( 'customize_save', array( $this, 'customizer_save_before' ) ); // Before save
                add_action( 'customize_save_after', array( &$this, 'customizer_save_after' ) ); // After save

                // Add global controls CSS file
                add_action( 'customize_controls_print_scripts', array( $this, 'enqueue_controls_css' ) );

                add_action( 'customize_controls_init', array( $this, 'enqueue_panel_css' ) );


                //add_action( 'wp_enqueue_scripts', array( &$this, '_enqueue_previewer_css' ) ); // Enqueue previewer css
                //add_action( 'wp_enqueue_scripts', array( &$this, '_enqueue_previewer_js' ) ); // Enqueue previewer javascript
                //add_action( "wp_footer", array( $this, '_enqueue_new' ), 100 );
                //$this->_enqueue_new();


            }

            function enqueue_controls_css() {

                require_once ReduxFramework::$_dir . 'core/enqueue.php';
                $enqueue = new reduxCoreEnqueue ( $this->parent );
                $enqueue->get_warnings_and_errors_array();
                $enqueue->init();
                wp_enqueue_style( 'redux-extension-advanced-customizer', $this->_extension_url . 'extension_customizer.css', '', time() );

                wp_enqueue_script(
                    'redux-extension-customizer',
                    $this->_extension_url . 'extension_customizer' . Redux_Functions::isMin() . '.js',
                    array( 'jquery', 'redux-js' ),
                    ReduxFramework_extension_customizer::$version,
                    true
                );
                wp_localize_script( 'redux-extension-customizer', 'redux_customizer', array( 'body_class' => sanitize_html_class( 'admin-color-' . get_user_option( 'admin_color' ), 'fresh' ) ) );
            }

            function enqueue_panel_css() {

            }

            function remove_core_customizer_class( $path ) {
                return "";
            }

            function customize_preview_init() {
                do_action( 'redux/customizer/live_preview' );
            }

            protected static function get_post_values() {
                if ( empty( self::$post_values ) && isset( $_POST['customized'] ) && ! empty( $_POST['customized'] ) ) {
                    self::$post_values = json_decode( stripslashes_deep( $_POST['customized'] ), true );
                }
            }

            public function _override_values( $data ) {

                self::get_post_values();


                if ( isset( $_POST['customized'] ) && ! empty( self::$post_values ) ) {

                    if ( is_array( self::$post_values ) ) {
                        foreach ( self::$post_values as $key => $value ) {
                            if ( strpos( $key, $this->parent->args['opt_name'] ) !== false ) {

                                //if (is_array($value)) {
                                //    $value = @stripslashes( $value );
                                //    if ( function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc() ) {
                                //        $value = @array_map( 'stripslashes_deep', $value );
                                //        $value = @array_map( 'urldecode', $value );
                                //    }
                                //} else {
                                //    $value = @urldecode($value);
                                //}
                                $key                                                       = str_replace( $this->parent->args['opt_name'] . '[', '', rtrim( $key, "]" ) );
                                $data[ $key ]                                              = $value;
                                $GLOBALS[ $this->parent->args['global_variable'] ][ $key ] = $value;
                                $this->parent->options[ $key ]                             = $value;
                            }
                        }
                    }

                }

                return $data;
            }

            public function _enqueue_new() {
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/codemirror.min.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/colors-control.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/customizer-control.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/fonts-customizer-admin.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/header-control.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/header-models.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/jquery.slimscroll.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/jquery.ui.droppable.min.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/media-editor.min.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/new-customizer.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/previewing.js'."'></script>";
                //echo "<script type='text/javascript' src='".$this->_extension_url . 'new/theme-customizer.js'."'></script>";

                /*
                  wp_enqueue_script('redux-extension-customizer-codemirror-js', $this->_extension_url . 'new/codemirror.min.js');
                  wp_enqueue_script('redux-extension-customizer-color-js', $this->_extension_url . 'new/colors-control.js');
                  wp_enqueue_script('redux-extension-customizer-controls-js', $this->_extension_url . 'new/customizer-control.js');
                  wp_enqueue_script('redux-extension-customizer-fonts-js', $this->_extension_url . 'new/fonts-customizer-admin.js');
                  wp_enqueue_script('redux-extension-customizer-header-js', $this->_extension_url . 'new/header-control.js');
                  wp_enqueue_script('redux-extension-customizer-models-js', $this->_extension_url . 'new/header-models.js');
                  wp_enqueue_script('redux-extension-customizer-slimscroll-js', $this->_extension_url . 'new/jquery.slimscroll.js');
                  wp_enqueue_script('redux-extension-customizer-droppable-js', $this->_extension_url . 'new/jquery.ui.droppable.min.js');
                  wp_enqueue_script('redux-extension-customizer-editor-js', $this->_extension_url . 'new/media-editor.min.js');
                  wp_enqueue_script('redux-extension-customizer-new-js', $this->_extension_url . 'new/new-customizer.js');
                  wp_enqueue_script('redux-extension-customizer-previewing-js', $this->_extension_url . 'new/previewing.js');
                  wp_enqueue_script('redux-extension-customizer-theme-js', $this->_extension_url . 'new/theme-customizer.js');
                 */
            }

            public function render( $control ) {
                $fieldID = str_replace( $this->parent->args['opt_name'] . '-', '', $control->redux_id );
                $field   = $this->options[ $fieldID ];

                if ( isset( $field['compiler'] ) && ! empty( $field['compiler'] ) ) {
                    echo '<tr class="compiler">';
                } else {
                    echo '<tr>';
                }
                echo '<th scope="row">' . $this->parent->field_head[ $field['id'] ] . '</th>';
                echo '<td>';
                //$field['data-customize-setting-link'] = array(
                //    'name' => $field['name'],
                //    'suffix' => isset($field['name_suffix']) ? $field['name_suffix'] : ''
                //);
                //
                $field['name'] = $field['id'];
                $this->parent->_field_input( $field );
                echo '</td>';
                echo '</tr>';
            }

            // All sections, settings, and controls will be added here
            public function _register_customizer_controls( $wp_customize ) {

                if ( ! class_exists( 'Redux_Customizer_Section' ) ) {
                    require_once dirname( __FILE__ ) . '/inc/customizer_section.php';
                    if ( method_exists( $wp_customize, 'register_section_type' ) ) {
                        $wp_customize->register_section_type( 'Redux_Customizer_Section' );
                    }
                }
                if ( ! class_exists( 'Redux_Customizer_Panel' ) ) {
                    require_once dirname( __FILE__ ) . '/inc/customizer_panel.php';
                    if ( method_exists( $wp_customize, 'register_panel_type' ) ) {
                        $wp_customize->register_panel_type( 'Redux_Customizer_Panel' );
                    }
                }
                if ( ! class_exists( 'Redux_Customizer_Control' ) ) {
                    require_once dirname( __FILE__ ) . '/inc/customizer_control.php';
                }

                require_once dirname( __FILE__ ) . '/inc/customizer_fields.php';
                require_once dirname( __FILE__ ) . '/inc/customizer_devs.php';

                do_action( "redux/extension/customizer/control/includes" );

                //if ($this->parent->args['dev_mode']) {
                //    $section = new Redux_Customizer_rAds( $wp_customize, 'redux_rAds', array(
                //        'priority'    => 0,
                //    ) );
                //    $wp_customize->add_section( $section, array(
                //        'priority'    => 0,
                //    ) );
                //
                //    //$wp_customize->add_control( new Redux_Customizer_Control_rAds( $wp_customize, 'reduxAdsDisplay', array(
                //    //    'section'        => 'redux_rAds',
                //    //    'settings'       => 'redux_rAds_field',
                //    //    'type'           => 'redux-rAds',
                //    //) ) );
                //
                //
                //
                //
                //}
                if ( $this->parent->args['dev_mode'] ) {
                    //$args = array(
                    //    'priority'    => 0,
                    //);
                    ////$section = new Redux_Customizer_Section( $wp_customize, 'redux_rAds', $args );
                    ////$wp_customize->add_section( $section, $args );
                    //$this->add_section( 'redux_rAds', array(
                    //    'title'       => '',
                    //    'priority'    => 1,
                    //    'description' => '',
                    //    'capability'  => 'edit_theme_options',
                    //), $wp_customize );
                    //
                    //$wp_customize->add_control( new WP_Customize_Color_Control(
                    //    $wp_customize,
                    //    'redux_rAds_display',
                    //    array(
                    //        'section'    => 'redux_rAds',
                    //        'settings'   => 'redux_rAds_display',
                    //    )
                    //));
                    ////$wp_customize->add_control( new Redux_Customizer_Control_rAds( $wp_customize, 'reduxAdsDisplay', array(
                    ////    'section'        => 'redux_rAds',
                    ////    'settings'       => 'redux_rAds_field',
                    ////    'type'           => 'redux-rAds',
                    ////) ) );
                    //start copyright settings

                    //$section = new Redux_Customizer_section_rAds( $wp_customize, 'redux_rAds', array(
                    //    'priority'    => -999,
                    //) );
                    //$wp_customize->add_section( $section, array(
                    //    'priority'    => -999,
                    //) );
                    //$wp_customize->add_setting(
                    //    'redux_rAds_empty'
                    //);
                    //$wp_customize->add_control(
                    //    new Redux_Customizer_Control_rAds(
                    //        $wp_customize,
                    //        'redux_rAds_empty',
                    //        array(
                    //            'section'    => 'redux_rAds',
                    //            'settings'   => 'redux_rAds_empty'
                    //        )
                    //    )
                    //);
                }


                $order    = array(
                    'heading' => - 500,
                    'option'  => - 500,
                );
                $defaults = array(
                    'default-color'          => '',
                    'default-image'          => '',
                    'wp-head-callback'       => '',
                    'admin-head-callback'    => '',
                    'admin-preview-callback' => ''
                );
                $panel    = "";

                $this->parent->args['options_api'] = false;
                $this->parent->_register_settings();

                foreach ( $this->parent->sections as $key => $section ) {

                    // Not a type that should go on the customizer
                    if ( isset( $section['type'] ) && ( $section['type'] == "divide" ) ) {
                        continue;
                    }

                    if ( isset( $section['id'] ) && $section['id'] == "import/export" ) {
                        continue;
                    }

                    // If section customizer is set to false
                    if ( isset( $section['customizer'] ) && $section['customizer'] === false ) {
                        continue;
                    }

                    $section['permissions'] = isset( $section['permissions'] ) ? $section['permissions'] : 'edit_theme_options';

                    // No errors please
                    if ( ! isset( $section['desc'] ) ) {
                        $section['desc'] = "";
                    }

                    // Fill the description if there is a subtitle
                    if ( empty( $section['desc'] ) && ! empty( $section['subtitle'] ) ) {
                        $section['desc'] = $section['subtitle'];
                    }

                    // Let's make a section ID from the title
                    if ( empty( $section['id'] ) ) {
                        $section['id'] = strtolower( str_replace( " ", "", $section['title'] ) );
                    }

                    // No title is present, let's show what section is missing a title
                    if ( ! isset( $section['title'] ) ) {
                        $section['title'] = "";
                    }

                    // Let's set a default priority
                    if ( empty( $section['priority'] ) ) {
                        $section['priority'] = $order['heading'];
                        $order['heading'] ++;
                    }

                    //print_r($section);
                    //print_r($this->parent->sections[$key+1]);
                    //echo $key;
                    //exit();


                    if ( method_exists( $wp_customize, 'add_panel' ) && ( ! isset( $section['subsection'] ) || ( isset( $section['subsection'] ) && $section['subsection'] != true ) ) && isset( $this->parent->sections[ ( $key + 1 ) ]['subsection'] ) && $this->parent->sections[ ( $key + 1 ) ]['subsection'] ) {

                        $this->add_panel( $section['id'], array(
                            'priority'    => $section['priority'],
                            'capability'  => $section['permissions'],
                            //'theme_supports' => '',
                            'title'       => $section['title'],
                            'section'     => $section,
                            'opt_name'    => $this->parent->args['opt_name'],
                            'description' => '',
                        ), $wp_customize );
                        $panel = $section['id'];

                        $this->add_section( $section['id'], array(
                            'title'       => $section['title'],
                            'priority'    => $section['priority'],
                            'description' => $section['desc'],
                            'section'     => $section,
                            'opt_name'    => $this->parent->args['opt_name'],
                            'capability'  => $section['permissions'],
                            'panel'       => $panel
                        ), $wp_customize );


                    } else {
                        if ( ! isset( $section['subsection'] ) || ( isset( $section['subsection'] ) && $section['subsection'] != true ) ) {
                            $panel = "";
                        }
                        $this->add_section( $section['id'], array(
                            'title'       => $section['title'],
                            'priority'    => $section['priority'],
                            'description' => $section['desc'],
                            'opt_name'    => $this->parent->args['opt_name'],
                            'section'     => $section,
                            'capability'  => $section['permissions'],
                            'panel'       => $panel
                        ), $wp_customize );
                    }

                    if ( ! isset( $section['fields'] ) || ( isset( $section['fields'] ) && empty( $section['fields'] ) ) ) {
                        continue;
                    }

                    foreach ( $section['fields'] as $skey => $option ) {

                        if ( isset( $option['customizer'] ) && $option['customizer'] === false ) {
                            continue;
                        }

                        if ( $this->parent->args['customizer'] === false && ( ! isset( $option['customizer'] ) || $option['customizer'] !== true ) ) {
                            continue;
                        }

                        $this->options[ $option['id'] ] = $option;
                        add_action( 'redux/advanced_customizer/control/render/' . $this->parent->args['opt_name'] . '-' . $option['id'], array(
                            $this,
                            'render'
                        ) );

                        $option['permissions'] = isset( $option['permissions'] ) ? $option['permissions'] : 'edit_theme_options';

                        //
                        //if ( isset( $option['validate_callback'] ) && ! empty( $option['validate_callback'] ) ) {
                        //    continue;
                        //}


                        //Change the item priority if not set
                        if ( $option['type'] != 'heading' && ! isset( $option['priority'] ) ) {
                            $option['priority'] = $order['option'];
                            $order['option'] ++;
                        }

                        if ( ! empty( $this->options_defaults[ $option['id'] ] ) ) {
                            $option['default'] = $this->options_defaults['option']['id'];
                        }

                        //$option['id'] = $this->parent->args['opt_name'].'['.$option['id'].']';
                        //echo $option['id'];

                        if ( ! isset( $option['default'] ) ) {
                            $option['default'] = "";
                        }
                        if ( ! isset( $option['title'] ) ) {
                            $option['title'] = "";
                        }


                        $option['id'] = $this->parent->args['opt_name'] . '[' . $option['id'] . ']';

                        if ( $option['type'] != "heading" && $option['type'] != "import_export" && ! empty( $option['type'] ) ) {

                            $wp_customize->add_setting( $option['id'],
                                array(
                                    'default'           => $option['default'],
                                    //'type'              => 'option',
                                    //'capabilities'     => $option['permissions'],
                                    //'capabilities'      => 'edit_theme_options',
                                    //'capabilities'   => $this->parent->args['page_permissions'],
                                    'transport'         => 'refresh',
                                    'opt_name'          => $this->parent->args['opt_name'],
                                    //'theme_supports'    => '',
                                    //'sanitize_callback' => '__return_false',
                                    'sanitize_callback' => array( $this, '_field_validation' ),
                                    //'sanitize_js_callback' =>array( &$parent, '_field_input' ),
                                )
                            );

                        }

                        if ( ! empty( $option['data'] ) && empty( $option['options'] ) ) {
                            if ( empty( $option['args'] ) ) {
                                $option['args'] = array();
                            }

                            if ( $option['data'] == "elusive-icons" || $option['data'] == "elusive-icon" || $option['data'] == "elusive" ) {
                                $icons_file = ReduxFramework::$_dir . 'inc/fields/select/elusive-icons.php';
                                $icons_file = apply_filters( 'redux-font-icons-file', $icons_file );

                                if ( file_exists( $icons_file ) ) {
                                    require_once $icons_file;
                                }
                            }
                            $option['options'] = $this->parent->get_wordpress_data( $option['data'], $option['args'] );
                        }

                        $class_name = 'Redux_Customizer_Control_' . $option['type'];

                        do_action( 'redux/extension/customizer/control_init', $option );

                        if ( ! class_exists( $class_name ) ) {
                            continue;
                        }

                        $wp_customize->add_control( new $class_name( $wp_customize, $option['id'], array(
                            'label'          => $option['title'],
                            'section'        => $section['id'],
                            'settings'       => $option['id'],
                            'type'           => 'redux-' . $option['type'],
                            'field'          => $option,
                            'ReduxFramework' => $this->parent,
                            'priority'       => $option['priority'],
                        ) ) );

                        $section['fields'][ $skey ]['name'] = $option['id'];
                        if ( ! isset ( $section['fields'][ $skey ]['class'] ) ) { // No errors please
                            $section['fields'][ $skey ]['class'] = "";
                        }

                        $this->controls[ $section['fields'][ $skey ]['id'] ] = $section['fields'][ $skey ];

                        add_action( 'redux/advanced_customizer/render/' . $option['id'], array(
                            $this,
                            'field_render'
                        ), $option['priority'] );


                    }
                }

            }

            public function add_section( $id, $args = array(), $wp_customize ) {

                if ( is_a( $id, 'WP_Customize_Section' ) ) {
                    $section = $id;
                } else {

                    $section_class = apply_filters( 'redux/customizer/section/class_name', "Redux_Customizer_Section" );
                    $section       = new $section_class( $wp_customize, $id, $args );
                }

                $wp_customize->add_section( $section, $args );

            }

            /**
             * Add a customize panel.
             *
             * @since  4.0.0
             * @access public
             *
             * @param WP_Customize_Panel|string $id   Customize Panel object, or Panel ID.
             * @param array                     $args Optional. Panel arguments. Default empty array.
             */
            public function add_panel( $id, $args = array(), $wp_customize ) {
                if ( is_a( $id, 'WP_Customize_Panel' ) ) {
                    $panel = $id;
                } else {
                    $panel_class = apply_filters( 'redux/customizer/panel/class_name', "Redux_Customizer_Panel" );
                    $panel       = new $panel_class( $wp_customize, $id, $args );
                }

                $wp_customize->add_panel( $panel, $args );
            }

            public function field_render( $option ) {
                echo '1';
                preg_match_all( "/\[([^\]]*)\]/", $option->id, $matches );
                $id = $matches[1][0];
                echo $option->link();
                //$link = $option->link();
                //echo $link;

                $this->parent->_field_input( $this->controls[ $id ] );
                echo '2';
            }

            public function customizer_save_before( $plugin_options ) {
                $this->before_save = $this->parent->options;
                //$parent->_field_input( $plugin_options );
            }

            public function customizer_save_after( $wp_customize ) {

                if ( empty( $this->parent->options ) ) {
                    $this->parent->get_options();
                }
                if ( empty( $this->orig_options ) && ! empty( $this->parent->options ) ) {
                    $this->orig_options = $this->parent->options;
                }

                $options  = json_decode( stripslashes_deep( $_POST['customized'] ), true );
                $compiler = false;
                $changed  = false;

                foreach ( $options as $key => $value ) {
                    if ( strpos( $key, $this->parent->args['opt_name'] ) !== false ) {
                        $key = str_replace( $this->parent->args['opt_name'] . '[', '', rtrim( $key, "]" ) );

                        if ( ! isset( $this->orig_options[ $key ] ) || $this->orig_options[ $key ] != $value || ( isset( $this->orig_options[ $key ] ) && ! empty( $this->orig_options[ $key ] ) && empty( $value ) ) ) {
                            $this->parent->options[ $key ] = $value;
                            $changed                       = true;
                            if ( isset( $this->parent->compiler_fields[ $key ] ) ) {
                                $compiler = true;
                            }
                        }
                    }
                }

                if ( $changed ) {
                    $this->parent->set_options( $this->parent->options );
                    if ( $compiler ) {
                        // Have to set this to stop the output of the CSS and typography stuff.
                        $this->parent->no_output = true;
                        $this->parent->_enqueue_output();
                        do_action( "redux/options/{$this->parent->args['opt_name']}/compiler", $this->parent->options, $this->parent->compilerCSS );
                        do_action( "redux/options/{$this->args['opt_name']}/compiler/advanced", $this->parent );
                    }
                }

            }

            /**
             * Enqueue CSS/JS for preview pane
             *
             * @since       1.0.0
             * @access      public
             * @global      $wp_styles
             * @return      void
             */
            public function _enqueue_previewer() {
                wp_enqueue_script(
                    'redux-extension-previewer-js',
                    $this->_extension_url . 'assets/js/preview.js'
                );

                $localize = array(
                    'save_pending'   => __( 'You have changes that are not saved. Would you like to save them now?', 'redux-framework' ),
                    'reset_confirm'  => __( 'Are you sure? Resetting will lose all custom values.', 'redux-framework' ),
                    'preset_confirm' => __( 'Your current options will be replaced with the values of this preset. Would you like to proceed?', 'redux-framework' ),
                    'opt_name'       => $this->args['opt_name'],
                    //'folds'             => $this->folds,
                    'options'        => $this->parent->options,
                    'defaults'       => $this->parent->options_defaults,
                );

                wp_localize_script(
                    'redux-extension-previewer-js',
                    'reduxPost',
                    $localize
                );
            }

            /**
             * Enqueue CSS/JS for the customizer controls
             *
             * @since       1.0.0
             * @access      public
             * @global      $wp_styles
             * @return      void
             */
            public function _enqueue() {
                global $wp_styles;

                //wp_enqueue_style( 'wp-pointer' );
                //wp_enqueue_script( 'wp-pointer' );
                // Remove when code is in place!
                //wp_enqueue_script('redux-extension-customizer-js', $this->_extension_url . 'assets/js/customizer.js');
                // Get styles
                //wp_enqueue_style('redux-extension-customizer-css', $this->_extension_url . 'assets/css/customizer.css');

                $localize = array(
                    'save_pending'   => __( 'You have changes that are not saved.  Would you like to save them now?', 'redux-framework' ),
                    'reset_confirm'  => __( 'Are you sure?  Resetting will lose all custom values.', 'redux-framework' ),
                    'preset_confirm' => __( 'Your current options will be replaced with the values of this preset.  Would you like to proceed?', 'redux-framework' ),
                    'opt_name'       => $this->args['opt_name'],
                    //'folds'             => $this->folds,
                    'field'          => $this->parent->options,
                    'defaults'       => $this->parent->options_defaults,
                );

                // Values used by the javascript
                wp_localize_script(
                    'redux-js',
                    'redux_opts',
                    $localize
                );

                do_action( 'redux-enqueue-' . $this->args['opt_name'] );


                foreach ( $this->sections as $section ) {
                    if ( isset( $section['fields'] ) ) {
                        foreach ( $section['fields'] as $field ) {
                            if ( isset( $field['type'] ) ) {
                                $field_class = 'ReduxFramework_' . $field['type'];

                                if ( ! class_exists( $field_class ) ) {
                                    $class_file = apply_filters( 'redux-typeclass-load', $this->path . 'inc/fields/' . $field['type'] . '/field_' . $field['type'] . '.php', $field_class );
                                    if ( $class_file ) {
                                        /** @noinspection PhpIncludeInspection */
                                        require_once( $class_file );
                                    }
                                }

                                if ( class_exists( $field_class ) && method_exists( $field_class, 'enqueue' ) ) {
                                    $enqueue = new $field_class( '', '', $this );
                                    $enqueue->enqueue();
                                }
                            }
                        }
                    }
                }
            }

            /**
             * Register Option for use
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function _register_setting() {

            }

            /**
             * Validate the options before insertion
             *
             * @since       3.0.0
             * @access      public
             *
             * @param       array $plugin_options The options array
             *
             * @return
             */
            public function _field_validation( $value ) {
                //print_r( $value );
                //print_r( $_POST );

                return $value;

                //return $this->parent->_validate_options( $plugin_options );
            }

            /**
             * HTML OUTPUT.
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function _customizer_html_output() {

            }
        } // class
        function redux_customizer_custom_validation( $field ) {
            return $field;
        }
    } // if
