<?php

    /**
     * @package Redux_Tracking
     */
    if ( ! class_exists( 'ReduxFramework' ) ) {
        return;
    }

    /**
     * Class that creates the tracking functionality for Redux, as the core class might be used in more plugins,
     * it's checked for existence first.
     * NOTE: this functionality is opt-in. Disabling the tracking in the settings or saying no when asked will cause
     * this file to not even be loaded.
     */
    if ( ! class_exists( 'Redux_Tracking' ) ) {

        /**
         * Class Redux_Tracking
         */
        class Redux_Tracking {

            public $options = array();
            public $parent;

            /** Refers to a single instance of this class. */
            private static $instance = null;

            /**
             * Creates or returns an instance of this class.
             *
             * @return Redux_Tracking A single instance of this class.
             */
            public static function get_instance() {

                if ( null == self::$instance ) {
                    self::$instance = new self;
                }

                return self::$instance;
            }
            // end get_instance;

            /**
             * Class constructor
             */

            function __construct() {


            }

            /**
             * @param ReduxFramework $parent
             */
            public function load( $parent ) {
                $this->parent = $parent;


                $this->options             = get_option( 'redux-framework-tracking' );
                $this->options['dev_mode'] = $parent->args['dev_mode'];


                if ( ! isset( $this->options['hash'] ) || ! $this->options['hash'] || empty( $this->options['hash'] ) ) {
                    $this->options['hash'] = md5( network_site_url() . '-' . $_SERVER['REMOTE_ADDR'] );
                    update_option( 'redux-framework-tracking', $this->options );
                }

                if ( isset( $_GET['redux_framework_disable_tracking'] ) && ! empty( $_GET['redux_framework_disable_tracking'] ) ) {
                    $this->options['allow_tracking'] = 'no';
                    update_option( 'redux-framework-tracking', $this->options );
                }

                if ( isset( $_GET['redux_framework_enable_tracking'] ) && ! empty( $_GET['redux_framework_enable_tracking'] ) ) {
                    $this->options['allow_tracking'] = 'yes';
                    update_option( 'redux-framework-tracking', $this->options );
                }

                if ( isset( $_GET['page'] ) && $_GET['page'] == $this->parent->args['page_slug'] ) {
                    if ( ! isset( $this->options['allow_tracking'] ) ) {
                        add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_tracking' ) );
                    } else if ( ! isset( $this->options['tour'] ) && ( $this->parent->args['dev_mode'] == "true" || $this->parent->args['page_slug'] == "redux_demo" ) ) {
                        add_action( 'admin_enqueue_scripts', array( $this, '_enqueue_newsletter' ) );
                    }
                }

                $hash = md5( trailingslashit( network_site_url() ) . '-redux' );
                add_action( 'wp_ajax_nopriv_' . $hash, array( $this, 'tracking_arg' ) );
                add_action( 'wp_ajax_' . $hash, array( $this, 'tracking_arg' ) );

                $hash = md5( md5( AUTH_KEY . SECURE_AUTH_KEY . '-redux' ) . '-support' );
                add_action( 'wp_ajax_nopriv_' . $hash, array( $this, 'support_args' ) );
                add_action( 'wp_ajax_' . $hash, array( $this, 'support_args' ) );

                if ( isset( $this->options['allow_tracking'] ) && $this->options['allow_tracking'] == 'yes' ) {
                    // The tracking checks daily, but only sends new data every 7 days.
                    if ( ! wp_next_scheduled( 'redux_tracking' ) ) {
                        wp_schedule_event( time(), 'daily', 'redux_tracking' );
                    }
                    add_action( 'redux_tracking', array( $this, 'tracking' ) );
                }
            }

            function _enqueue_tracking() {
                wp_enqueue_style( 'wp-pointer' );
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui' );
                wp_enqueue_script( 'wp-pointer' );
                wp_enqueue_script( 'utils' );
                add_action( 'admin_print_footer_scripts', array( $this, 'tracking_request' ) );
            }

            function _enqueue_newsletter() {
                wp_enqueue_style( 'wp-pointer' );
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui' );
                wp_enqueue_script( 'wp-pointer' );
                wp_enqueue_script( 'utils' );
                add_action( 'admin_print_footer_scripts', array( $this, 'newsletter_request' ) );
            }

            /**
             * Shows a popup that asks for permission to allow tracking.
             */
            function tracking_request() {
                $id    = '#wpadminbar';
                $nonce = wp_create_nonce( 'redux_activate_tracking' );

                $content = '<h3>' . esc_html__( 'Help improve Our Panel', 'redux-framework' ) . '</h3>';
                $content .= '<p>' . esc_html__( 'Please helps us improve our panel by allowing us to gather anonymous usage stats so we know which configurations, plugins and themes to test to ensure compatibility.', 'redux-framework' ) . '</p>';
                $opt_arr = array(
                    'content'  => $content,
                    'position' => array( 'edge' => 'top', 'align' => 'center' )
                );
                $button2 = esc_html__( 'Allow tracking', 'redux-framework' );

                $function2 = 'redux_store_answer("yes","' . $nonce . '")';
                $function1 = 'redux_store_answer("no","' . $nonce . '")';

                $this->print_scripts( $id, $opt_arr, esc_html__( 'Do not allow tracking', 'redux-framework' ), $button2, $function2, $function1 );
            }

            /**
             * Shows a popup that asks for permission to allow tracking.
             */
            function newsletter_request() {
                $id    = '#wpadminbar';
                $nonce = wp_create_nonce( 'redux_activate_tracking' );


                $content = '<h3>' . esc_html__( 'Welcome to the Redux Demo Panel', 'redux-framework' ) . '</h3>';
                $content .= '<p><strong>' . esc_html__( 'Getting Started', 'redux-framework' ) . '</strong><br>' . sprintf( __( 'This panel demonstrates the many features of Redux.  Before digging in, we suggest you get up to speed by reviewing %1$s.', 'redux-framework' ), '<a href="' . 'http://' . 'docs.reduxframework.com/redux-framework/getting-started/" target="_blank">' . esc_html__( 'our documentation', 'redux-framework' ) . '</a>' );
                $content .= '<p><strong>' . esc_html__( 'Redux Generator', 'redux-framework' ) . '</strong><br>' . sprintf( __( 'Want to get a head start? Use the %1$s. It will create a customized boilerplate theme or a standalone admin folder complete with all things Redux (with the help of Underscores and TGM). Save yourself a headache and try it today.', 'redux-framework' ), '<a href="' . 'http://' . 'generate.reduxframework.com/" target="_blank">' . esc_html__( 'Redux Generator', 'redux-framework' ) . '</a>' );
                $content .= '<p><strong>' . esc_html__( 'Redux Extensions', 'redux-framework' ) . '</strong><br>' . sprintf( __( 'Did you know we have extensions, which greatly enhance the features of Redux?  Visit our %1$s to learn more!', 'redux-framework' ), '<a href="' . 'http://' . 'reduxframework.com/extensions/" target="_blank">' . esc_html__( 'extensions directory', 'redux-framework' ) . '</a>' );
                $content .= '<p><strong>' . esc_html__( 'Like Redux?', 'redux-framework' ) . '</strong><br>' . sprintf( __( 'If so, please %1$s and consider making a %2$s to keep development of Redux moving forward.', 'redux-framework' ), '<a target="_blank" href="' . 'http://' . 'wordpress.org/support/view/plugin-reviews/redux-framework">' . esc_html__( 'leave us a favorable review on WordPress.org', 'redux-framework' ) . '</a>', '<a href="' . 'https://' . 'www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=N5AD7TSH8YA5U" target="_blank">' . esc_html__( 'donation', 'redux-framework' ) . '</a>' );
                $content .= '<p><strong>' . esc_html__( 'Newsletter', 'redux-framework' ) . '</strong><br>' . esc_html__( 'If you\'d like to keep up to with all things Redux, please subscribe to our newsletter', 'redux-framework' ) . ':</p>';
                $content .= '<form action="' . 'http://' . 'reduxframework.us7.list-manage2.com/subscribe/post?u=564f5178f6cc288064f332efd&amp;id=ace5bbc1f9&SOURCE=panel" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate><p style="text-align: center;"><label for="mce-EMAIL">' . esc_html__( 'Email address', 'redux-framework' ) . ' </label><input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL"><input type="hidden" value="panel" name="SOURCE">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="' . esc_html__( 'Subscribe', 'redux-framework' ) . '" name="subscribe" id="mc-embedded-subscribe" class="button button-primary"></p></form>';
                $opt_arr = array(
                    'content'      => $content,
                    'position'     => array( 'edge' => 'top', 'align' => 'center' ),
                    'pointerWidth' => 450
                );

                $function1 = 'redux_store_answer("tour","' . $nonce . '")';

                $this->print_scripts( $id, $opt_arr, esc_html__( 'Close', 'redux-framework' ), false, '', $function1 );
            }

            /**
             * Prints the pointer script
             *
             * @param string      $selector         The CSS selector the pointer is attached to.
             * @param array       $options          The options for the pointer.
             * @param string      $button1          Text for button 1
             * @param string|bool $button2          Text for button 2 (or false to not show it, defaults to false)
             * @param string      $button2_function The JavaScript function to attach to button 2
             * @param string      $button1_function The JavaScript function to attach to button 1
             */
            function print_scripts( $selector, $options, $button1, $button2 = false, $button2_function = '', $button1_function = '' ) {
                ?>
                <script type="text/javascript">
                    //<![CDATA[
                    //
                    (function( $ ) {
                        $( document ).ready(
                            function() {
                                var redux_pointer_options = <?php echo json_encode($options); ?>, setup;

                                function redux_store_answer( input, nonce ) {
                                    var redux_tracking_data = {
                                        action: 'redux_allow_tracking',
                                        allow_tracking: input,
                                        nonce: nonce
                                    }
                                    jQuery.post(
                                        ajaxurl, redux_tracking_data, function() {
                                            jQuery( '#wp-pointer-0' ).remove();
                                        }
                                    );
                                }

                                redux_pointer_options = $.extend(
                                    redux_pointer_options, {
                                        buttons: function( event, t ) {
                                            button = jQuery( '<a id="pointer-close" style="margin-left:5px" class="button-secondary">' + '<?php echo esc_js($button1); ?>' + '</a>' );
                                            button.bind(
                                                'click.pointer', function() {
                                                    t.element.pointer( 'close' );
                                                    //console.log( 'close button' );
                                                }
                                            );
                                            return button;
                                        },
                                        close: function() {
                                        }
                                    }
                                );

                                setup = function() {
                                    $( '<?php echo esc_js($selector); ?>' ).pointer( redux_pointer_options ).pointer( 'open' );
                                    <?php if ($button2) { ?>
                                    jQuery( '#pointer-close' ).after( '<a id="pointer-primary" class="button-primary">' + '<?php echo esc_js($button2); ?>' + '</a>' );
                                    jQuery( '#pointer-primary' ).click(
                                        function() {
                                            <?php echo esc_js($button2_function); ?>
                                        }
                                    );
                                    jQuery( '#pointer-close' ).click(
                                        function() {
                                            <?php if ($button1_function == '') { ?>
                                            redux_store_answer( input, nonce )
                                            //redux_setIgnore("tour", "wp-pointer-0", "<?php echo esc_js(wp_create_nonce('redux-ignore')); ?>");
                                            <?php } else { ?>
                                            <?php echo esc_js($button1_function); ?>
                                            <?php } ?>
                                        }
                                    );
                                    <?php } else if ($button1 && !$button2) { ?>
                                    jQuery( '#pointer-close' ).click(
                                        function() {
                                            <?php if ($button1_function != '') { ?>
                                            <?php echo esc_js($button1_function); ?>
                                            <?php } ?>
                                        }
                                    );
                                    <?php } ?>
                                };

                                if ( redux_pointer_options.position && redux_pointer_options.position.defer_loading )
                                    $( window ).bind( 'load.wp-pointers', setup );
                                else
                                    $( document ).ready( setup );
                            }
                        );
                    })( jQuery );
                    //]]>
                </script>
            <?php
            }

            function trackingObject() {
                global $blog_id, $wpdb;
                $pts = array();

                foreach ( get_post_types( array( 'public' => true ) ) as $pt ) {
                    $count      = wp_count_posts( $pt );
                    $pts[ $pt ] = $count->publish;
                }

                $comments_count = wp_count_comments();
                $theme_data     = wp_get_theme();
                $theme          = array(
                    'version'  => $theme_data->Version,
                    'name'     => $theme_data->Name,
                    'author'   => $theme_data->Author,
                    'template' => $theme_data->Template,
                );

                if ( ! function_exists( 'get_plugin_data' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/admin.php';
                }

                $plugins = array();
                foreach ( get_option( 'active_plugins', array() ) as $plugin_path ) {
                    $plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );

                    $slug             = str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
                    $plugins[ $slug ] = array(
                        'version'    => $plugin_info['Version'],
                        'name'       => $plugin_info['Name'],
                        'plugin_uri' => $plugin_info['PluginURI'],
                        'author'     => $plugin_info['AuthorName'],
                        'author_uri' => $plugin_info['AuthorURI'],
                    );
                }
                if ( is_multisite() ) {
                    foreach ( get_option( 'active_sitewide_plugins', array() ) as $plugin_path ) {
                        $plugin_info      = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );
                        $slug             = str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
                        $plugins[ $slug ] = array(
                            'version'    => $plugin_info['Version'],
                            'name'       => $plugin_info['Name'],
                            'plugin_uri' => $plugin_info['PluginURI'],
                            'author'     => $plugin_info['AuthorName'],
                            'author_uri' => $plugin_info['AuthorURI'],
                        );
                    }
                }


                $version = explode( '.', PHP_VERSION );
                $version = array(
                    'major'   => $version[0],
                    'minor'   => $version[0] . '.' . $version[1],
                    'release' => PHP_VERSION
                );

                $user_query     = new WP_User_Query( array( 'blog_id' => $blog_id, 'count_total' => true, ) );
                $comments_query = new WP_Comment_Query();
                $data           = array(
                    '_id'       => $this->options['hash'],
                    'localhost' => ( $_SERVER['REMOTE_ADDR'] === '127.0.0.1' ) ? 1 : 0,
                    'php'       => $version,
                    'site'      => array(
                        'hash'      => $this->options['hash'],
                        'version'   => get_bloginfo( 'version' ),
                        'multisite' => is_multisite(),
                        'users'     => $user_query->get_total(),
                        'lang'      => get_locale(),
                        'wp_debug'  => ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? true : false : false ),
                        'memory'    => WP_MEMORY_LIMIT,
                    ),
                    'pts'       => $pts,
                    'comments'  => array(
                        'total'    => $comments_count->total_comments,
                        'approved' => $comments_count->approved,
                        'spam'     => $comments_count->spam,
                        'pings'    => $comments_query->query( array( 'count' => true, 'type' => 'pingback' ) ),
                    ),
                    'options'   => apply_filters( 'redux/tracking/options', array() ),
                    'theme'     => $theme,
                    'redux'     => array(
                        'mode'      => ReduxFramework::$_is_plugin ? 'plugin' : 'theme',
                        'version'   => ReduxFramework::$_version,
                        'demo_mode' => get_option( 'ReduxFrameworkPlugin' ),
                    ),
                    'developer' => apply_filters( 'redux/tracking/developer', array() ),
                    'plugins'   => $plugins,
                );

                $parts    = explode( ' ', $_SERVER['SERVER_SOFTWARE'] );
                $software = array();
                foreach ( $parts as $part ) {
                    if ( $part[0] == "(" ) {
                        continue;
                    }
                    if ( strpos( $part, '/' ) !== false ) {
                        $chunk                               = explode( "/", $part );
                        $software[ strtolower( $chunk[0] ) ] = $chunk[1];
                    }
                }
                $software['full']    = $_SERVER['SERVER_SOFTWARE'];
                $data['environment'] = $software;
                //if ( function_exists( 'mysql_get_server_info' ) ) {
                //    $data['environment']['mysql'] = mysql_get_server_info();
                //}
                $data['environment']['mysql'] = $wpdb->db_version();
                        
                if ( empty( $data['developer'] ) ) {
                    unset( $data['developer'] );
                }

                return $data;
            }

            /**
             * Main tracking function.
             */
            function tracking() {
                // Start of Metrics
                global $blog_id, $wpdb;

                $data = get_transient( 'redux_tracking_cache' );
                if ( ! $data ) {

                    $args = array(
                        'body' => $this->trackingObject()
                    );

                    $response = wp_remote_post( 'https://redux-tracking.herokuapp.com', $args );

                    // Store for a week, then push data again.
                    set_transient( 'redux_tracking_cache', true, WEEK_IN_SECONDS );
                }
            }

            function tracking_arg() {
                echo md5( AUTH_KEY . SECURE_AUTH_KEY . '-redux' );
                die();
            }

            function support_args() {
                header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
                header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
                header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
                header( 'Cache-Control: no-store, no-cache, must-revalidate' );
                header( 'Cache-Control: post-check=0, pre-check=0', false );
                header( 'Pragma: no-cache' );
                $instances = ReduxFrameworkInstances::get_all_instances();

                if ( isset( $_REQUEST['i'] ) && ! empty( $_REQUEST['i'] ) ) {
                    if ( is_array( $instances ) && ! empty( $instances ) ) {
                        foreach ( $instances as $opt_name => $data ) {
                            if ( md5( $opt_name . '-debug' ) == $_REQUEST['i'] ) {
                                $array = $instances[ $opt_name ];
                            }
                        }
                    }
                    if ( isset( $array ) ) {
                        if ( isset( $array->extensions ) && is_array( $array->extensions ) && ! empty( $array->extensions ) ) {
                            foreach ( $array->extensions as $key => $extension ) {
                                if ( isset( $extension->$version ) ) {
                                    $array->extensions[ $key ] = $extension->$version;
                                } else {
                                    $array->extensions[ $key ] = true;
                                }
                            }
                        }
                        if ( isset( $array->import_export ) ) {
                            unset( $array->import_export );
                        }
                        if ( isset( $array->debug ) ) {
                            unset( $array->debug );
                        }
                    } else {
                        die();
                    }

                } else {
                    $array = $this->trackingObject();
                    if ( is_array( $instances ) && ! empty( $instances ) ) {
                        $array['instances'] = array();
                        foreach ( $instances as $opt_name => $data ) {
                            $array['instances'][] = $opt_name;
                        }
                    }
                    $array['key'] = md5( AUTH_KEY . SECURE_AUTH_KEY );
                }

                echo @json_encode( $array, true );
                die();
            }

        }

        Redux_Tracking::get_instance();

        /**
         * Adds tracking parameters for Redux settings. Outside of the main class as the class could also be in use in other ways.
         *
         * @param array $options
         *
         * @return array
         */
        function redux_tracking_additions( $options ) {
            $opt = array();

            $options['redux'] = array(
                'demo_mode' => get_option( 'ReduxFrameworkPlugin' ),
            );

            return $options;
        }

        add_filter( 'redux/tracking/options', 'redux_tracking_additions' );

        function redux_allow_tracking_callback() {
            // Verify that the incoming request is coming with the security nonce
            if ( wp_verify_nonce( $_REQUEST['nonce'], 'redux_activate_tracking' ) ) {
                $options = get_option( 'redux-framework-tracking' );

                if ( $_REQUEST['allow_tracking'] == "tour" ) {
                    $options['tour'] = 1;
                } else {
                    $options['allow_tracking'] = $_REQUEST['allow_tracking'];
                }

                if ( update_option( 'redux-framework-tracking', $options ) ) {
                    die( '1' );
                } else {
                    die( '0' );
                }
            } else {
                // Send -1 if the attempt to save via Ajax was completed invalid.
                die( '-1' );
            } // end if
        }

        add_action( 'wp_ajax_redux_allow_tracking', 'redux_allow_tracking_callback' );

    }
