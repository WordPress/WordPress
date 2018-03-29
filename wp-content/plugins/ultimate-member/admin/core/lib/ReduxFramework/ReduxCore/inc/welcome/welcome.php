<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }


    class Redux_Welcome {

        /**
         * @var string The capability users should have to view the page
         */
        public $minimum_capability = 'manage_options';
        public $display_version = "";
        public $redux_loaded = false;

        /**
         * Get things started
         *
         * @since 1.4
         */
        public function __construct() {

            add_action( 'redux/loaded', array( $this, 'init' ) );

            add_action( 'wp_ajax_redux_support_hash', array( $this, 'support_hash' ) );

        }

        public function init() {

            if ( $this->redux_loaded ) {
                return;
            }
            $this->redux_loaded = true;
            add_action( 'admin_menu', array( $this, 'admin_menus' ) );

            if ( isset( $_GET['page'] ) ) {
                if ( substr( $_GET['page'], 0, 6 ) == "redux-" ) {
                    $version               = explode( '.', ReduxFramework::$_version );
                    $this->display_version = $version[0] . '.' . $version[1];
                    add_filter( 'admin_footer_text', array( $this, 'change_wp_footer' ) );
                    add_action( 'admin_head', array( $this, 'admin_head' ) );
                } else {
                    $this->check_version();
                }
            } else {
                $this->check_version();
            }
            update_option( 'redux_version_upgraded_from', ReduxFramework::$_version );
            set_transient( '_redux_activation_redirect', true, 30 );

        }


        public function check_version() {
            global $pagenow;

            if ( $pagenow == "admin-ajax.php" || ( $GLOBALS['pagenow'] == "customize" && isset( $_GET['theme'] ) && ! empty( $_GET['theme'] ) ) ) {
                return;
            }

            $saveVer = Redux_Helpers::major_version( get_option( 'redux_version_upgraded_from' ) );
            $curVer  = Redux_Helpers::major_version( ReduxFramework::$_version );
            $compare = false;

            if ( Redux_Helpers::isLocalHost() ) {
                $compare = true;
            } else if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
                $compare = true;
            } else {
                $redux = ReduxFrameworkInstances::get_all_instances();

                if ( is_array( $redux ) ) {
                    foreach ( $redux as $panel ) {
                        if ( $panel->args['dev_mode'] == 1 ) {
                            $compare = true;
                            break;
                        }
                    }
                }
            }

            if ( $compare ) {
                $redirect = false;
                if ( empty( $saveVer ) ) {
                    $redirect = true; // First time
                }
                // Removing redirect except for the first time with the plugin installed. :)  Less annoying until we actually use this page.
                //else if ( version_compare( $curVer, $saveVer, '>' ) ) {
                //    $redirect = true; // Previous version
                //}
                if ( $redirect && ! defined( 'WP_TESTS_DOMAIN' ) && ReduxFramework::$_as_plugin ) {
                    add_action( 'init', array( $this, 'do_redirect' ) );
                }
            }
        }

        public function do_redirect() {
            if ( ! defined( 'WP_CLI' ) ) {
                wp_redirect( admin_url( 'tools.php?page=redux-about' ) );
                exit();
            }
        }

        public function change_wp_footer() {
            echo __( 'If you like <strong>Redux</strong> please leave us a <a href="https://wordpress.org/support/view/plugin-reviews/redux-framework?filter=5#postform" target="_blank" class="redux-rating-link" data-rated="Thanks :)">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. A huge thank you from Redux in advance!', 'redux-framework' );
        }

        public function support_hash() {

            if ( ! wp_verify_nonce( $_POST['nonce'], 'redux-support-hash' ) ) {
                die();
            }

            $data          = get_option( 'redux_support_hash' );
            $data          = wp_parse_args( $data, array( 'check' => '', 'identifier' => '' ) );
            $generate_hash = true;
            $system_info   = Redux_Helpers::compileSystemStatus();
            $newHash       = md5( json_encode( $system_info ) );
            $return        = array();
            if ( $newHash == $data['check'] ) {
                unset( $generate_hash );
            }

            $post_data = array(
                'hash'          => md5( network_site_url() . '-' . $_SERVER['REMOTE_ADDR'] ),
                'site'          => esc_url( home_url( '/' ) ),
                'tracking'      => Redux_Helpers::getTrackingObject(),
                'system_status' => $system_info,
            );
            //$post_data = json_encode( $post_data );
            $post_data = serialize( $post_data );

            if ( isset( $generate_hash ) && $generate_hash ) {
                
                $data['check']      = $newHash;
                $data['identifier'] = "";
                $response           = wp_remote_post( 'http://support.redux.io/v1/', array(
                        'method'      => 'POST',
                        'timeout'     => 65,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking'    => true,
                        'compress'    => true,
                        'headers'     => array(),
                        'body'        => array(
                            'data'      => $post_data,
                            'serialize' => 1
                        )
                    )
                );

                if ( is_wp_error( $response ) ) {
                    echo json_encode( array(
                        'status'  => 'error',
                        'message' => $response->get_error_message()
                    ) );
                    die( 1 );
                } else {
                    $response_code = wp_remote_retrieve_response_code( $response );
                    if ( $response_code == 200 ) {
                        $response = wp_remote_retrieve_body( $response );
                        $return   = json_decode( $response, true );
                        if ( isset( $return['identifier'] ) ) {
                            $data['identifier'] = $return['identifier'];
                            update_option( 'redux_support_hash', $data );
                        }
                    } else {
                        $response = wp_remote_retrieve_body( $response );
                        echo json_encode( array(
                            'status'  => 'error',
                            'message' => $response
                        ) );
                    }
                }
            }

            if ( ! empty( $data['identifier'] ) ) {
                $return['status']     = "success";
                $return['identifier'] = $data['identifier'];
            } else {
                $return['status']  = "error";
                $return['message'] = esc_html__( "Support hash could not be generated. Please try again later.", 'redux-framework' );
            }

            echo json_encode( $return );

            die( 1 );
        }

        /**
         * Register the Dashboard Pages which are later hidden but these pages
         * are used to render the Welcome and Credits pages.
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function admin_menus() {

            $page = 'add_management_page';

            // About Page
            $page(
                esc_html__( 'Welcome to Redux Framework', 'redux-framework' ), esc_html__( 'Redux Framework', 'redux-framework' ), $this->minimum_capability, 'redux-about', array(
                    $this,
                    'about_screen'
                )
            );

            // Changelog Page
            $page(
                esc_html__( 'Redux Framework Changelog', 'redux-framework' ), esc_html__( 'Redux Framework Changelog', 'redux-framework' ), $this->minimum_capability, 'redux-changelog', array(
                    $this,
                    'changelog_screen'
                )
            );

            // Support Page
            $page(
                esc_html__( 'Get Support', 'redux-framework' ), esc_html__( 'Get Support', 'redux-framework' ), $this->minimum_capability, 'redux-support', array(
                    $this,
                    'get_support'
                )
            );

            // Support Page
            $page(
                esc_html__( 'Redux Extensions', 'redux-framework' ), esc_html__( 'Redux Extensions', 'redux-framework' ), $this->minimum_capability, 'redux-extensions', array(
                    $this,
                    'redux_extensions'
                )
            );


            // Credits Page
            $page(
                esc_html__( 'The people that develop Redux Framework', 'redux-framework' ), esc_html__( 'The people that develop Redux Framework', 'redux-framework' ), $this->minimum_capability, 'redux-credits', array(
                    $this,
                    'credits_screen'
                )
            );

            // Status Page
            $page(
                esc_html__( 'Redux Framework Status', 'redux-framework' ), esc_html__( 'Redux Framework Status', 'redux-framework' ), $this->minimum_capability, 'redux-status', array(
                    $this,
                    'status_screen'
                )
            );

            //remove_submenu_page( 'tools.php', 'redux-about' );
            remove_submenu_page( 'tools.php', 'redux-status' );
            remove_submenu_page( 'tools.php', 'redux-changelog' );
            remove_submenu_page( 'tools.php', 'redux-getting-started' );
            remove_submenu_page( 'tools.php', 'redux-credits' );
            remove_submenu_page( 'tools.php', 'redux-support' );
            remove_submenu_page( 'tools.php', 'redux-extensions' );


        }

        /**
         * Hide Individual Dashboard Pages
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function admin_head() {

            // Badge for welcome page
            //$badge_url = ReduxFramework::$_url . 'assets/images/redux-badge.png';
            ?>

            <script
                id="redux-qtip-js"
                src='<?php echo esc_url( ReduxFramework::$_url ); ?>assets/js/vendor/qtip/jquery.qtip.js'>
            </script>

            <script
                id="redux-welcome-admin-js"
                src='<?php echo esc_url( ReduxFramework::$_url ) ?>inc/welcome/js/redux-welcome-admin.js'>
            </script>

            <?php
            if ( isset ( $_GET['page'] ) && $_GET['page'] == "redux-support" ) :
                ?>
                <script
                    id="jquery-easing"
                    src='<?php echo esc_url( ReduxFramework::$_url ); ?>inc/welcome/js/jquery.easing.min.js'>
                </script>
            <?php endif; ?>

            <link rel='stylesheet' id='redux-qtip-css'
                href='<?php echo esc_url( ReduxFramework::$_url ); ?>assets/css/vendor/qtip/jquery.qtip.css'
                type='text/css' media='all'/>

            <link rel='stylesheet' id='elusive-icons'
                href='<?php echo esc_url( ReduxFramework::$_url ); ?>assets/css/vendor/elusive-icons/elusive-icons.css'
                type='text/css' media='all'/>

            <link rel='stylesheet' id='redux-welcome-css'
                href='<?php echo esc_url( ReduxFramework::$_url ); ?>inc/welcome/css/redux-welcome.css'
                type='text/css' media='all'/>
            <style type="text/css">
                .redux-badge:before {
                <?php echo esc_js(is_rtl() ? 'right' : 'left'); ?> : 0;
                }

                .about-wrap .redux-badge {
                <?php echo esc_js(is_rtl() ? 'left' : 'right'); ?> : 0;
                }

                .about-wrap .feature-rest div {
                    padding- <?php echo esc_js(is_rtl() ? 'left' : 'right'); ?>: 100px;
                }

                .about-wrap .feature-rest div.last-feature {
                    padding- <?php echo esc_js(is_rtl() ? 'right' : 'left'); ?>: 100px;
                    padding- <?php echo esc_js(is_rtl() ? 'left' : 'right'); ?>: 0;
                }

                .about-wrap .feature-rest div.icon:before {
                    margin: <?php echo esc_js(is_rtl() ? '0 -100px 0 0' : '0 0 0 -100px'); ?>;
                }
            </style>
            <?php
        }

        /**
         * Navigation tabs
         *
         * @access public
         * @since  1.9
         * @return void
         */
        public function tabs() {
            $selected = isset ( $_GET['page'] ) ? esc_attr( $_GET['page'] ) : 'redux-about';
            $nonce    = wp_create_nonce( 'redux-support-hash' );
            ?>
            <input type="hidden" id="redux_support_nonce" value="<?php echo esc_attr( $nonce ); ?>"/>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab <?php echo $selected == 'redux-about' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'redux-about' ), 'tools.php' ) ) ); ?>">
                    <?php esc_attr_e( "What's New", 'redux-framework' ); ?>
                </a> <a class="nav-tab <?php echo $selected == 'redux-extensions' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'redux-extensions' ), 'tools.php' ) ) ); ?>">
                    <?php esc_attr_e( 'Extensions', 'redux-framework' ); ?>
                </a> <a class="nav-tab <?php echo $selected == 'redux-changelog' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'redux-changelog' ), 'tools.php' ) ) ); ?>">
                    <?php esc_attr_e( 'Changelog', 'redux-framework' ); ?>
                </a> <a class="nav-tab <?php echo $selected == 'redux-credits' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'redux-credits' ), 'tools.php' ) ) ); ?>">
                    <?php _e( 'Credits', 'redux-framework' ); ?>
                </a> <a class="nav-tab <?php echo $selected == 'redux-support' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'redux-support' ), 'tools.php' ) ) ); ?>">
                    <?php esc_attr_e( 'Support', 'redux-framework' ); ?>
                </a> <a class="nav-tab <?php echo $selected == 'redux-status' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'redux-status' ), 'tools.php' ) ) ); ?>">
                    <?php esc_attr_e( 'Status', 'redux-framework' ); ?>
                </a>
            </h2>
            <?php
        }

        /**
         * Render About Screen
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function about_screen() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            require_once 'views/about.php';

        }

        /**
         * Render Changelog Screen
         *
         * @access public
         * @since  2.0.3
         * @return void
         */
        public function changelog_screen() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            require_once 'views/changelog.php';

        }

        /**
         * Render Changelog Screen
         *
         * @access public
         * @since  2.0.3
         * @return void
         */
        public function redux_extensions() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            require_once 'views/extensions.php';

        }


        /**
         * Render Get Support Screen
         *
         * @access public
         * @since  1.9
         * @return void
         */
        public function get_support() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            require_once 'views/support.php';

        }

        /**
         * Render Credits Screen
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function credits_screen() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            require_once 'views/credits.php';

        }

        /**
         * Render Status Report Screen
         *
         * @access public
         * @since  1.4
         * @return void
         */
        public function status_screen() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';

            require_once 'views/status_report.php';

        }

        /**
         * Parse the Redux readme.txt file
         *
         * @since 2.0.3
         * @return string $readme HTML formatted readme file
         */
        public function parse_readme() {
            if ( file_exists( ReduxFramework::$_dir . 'inc/fields/raw/parsedown.php' ) ) {
                require_once ReduxFramework::$_dir . 'inc/fields/raw/parsedown.php';
                $Parsedown = new Parsedown();
                $data = @wp_remote_get( ReduxFramework::$_url . '../CHANGELOG.md' );
                if ( isset( $data ) && ! empty( $data ) ) {
                    $data = @wp_remote_retrieve_body( $data );
                    return $Parsedown->text( trim( str_replace( '# Redux Framework Changelog', '', $data ) ) );
                }
            }

            return '<script src="' . 'http://gist-it.appspot.com/https://github.com/reduxframework/redux-framework/blob/master/CHANGELOG.md?slice=2:0&footer=0">// <![CDATA[// ]]></script>';

        }

        public function actions() {
            ?>
            <p class="redux-actions">
                <a href="http://docs.reduxframework.com/" class="docs button button-primary">Docs</a>
                <a href="http://wordpress.org/plugins/redux-framework/" class="review-us button button-primary"
                    target="_blank">Review Us</a>
                <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MMFMHWUPKHKPW"
                    class="review-us button button-primary" target="_blank">Donate</a>
                <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://reduxframework.com"
                    data-text="Reduce your dev time! Redux is the most powerful option framework for WordPress on the web"
                    data-via="ReduxFramework" data-size="large" data-hashtags="Redux">Tweet</a>
                <script>!function( d, s, id ) {
                        var js, fjs = d.getElementsByTagName( s )[0], p = /^http:/.test( d.location ) ? 'http' : 'https';
                        if ( !d.getElementById( id ) ) {
                            js = d.createElement( s );
                            js.id = id;
                            js.src = p + '://platform.twitter.com/widgets.js';
                            fjs.parentNode.insertBefore( js, fjs );
                        }
                    }( document, 'script', 'twitter-wjs' );</script>
            </p>
            <?php
        }

        /**
         * Render Contributors List
         *
         * @since 1.4
         * @uses  Redux_Welcome::get_contributors()
         * @return string $contributor_list HTML formatted list of all the contributors for Redux
         */
        public function contributors() {
            $contributors = $this->get_contributors();

            if ( empty ( $contributors ) ) {
                return '';
            }

            $contributor_list = '<ul class="wp-people-group">';

            foreach ( $contributors as $contributor ) {
                $contributor_list .= '<li class="wp-person">';
                $contributor_list .= sprintf( '<a href="%s" title="%s" target="_blank">', esc_url( 'https://github.com/' . $contributor->login ), esc_html( sprintf( __( 'View %s', 'redux-framework' ), esc_html( $contributor->login ) ) )
                );
                $contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
                $contributor_list .= '</a>';
                $contributor_list .= sprintf( '<a class="web" href="%s" target="_blank">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
                $contributor_list .= '</a>';
                $contributor_list .= '</li>';
            }

            $contributor_list .= '</ul>';

            return $contributor_list;
        }

        /**
         * Retreive list of contributors from GitHub.
         *
         * @access public
         * @since  1.4
         * @return array $contributors List of contributors
         */
        public function get_contributors() {
            $contributors = get_transient( 'redux_contributors' );

            if ( false !== $contributors ) {
                return $contributors;
            }

            $response = wp_remote_get( 'https://api.github.com/repos/ReduxFramework/redux-framework/contributors', array( 'sslverify' => false ) );

            if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
                return array();
            }

            $contributors = json_decode( wp_remote_retrieve_body( $response ) );

            if ( ! is_array( $contributors ) ) {
                return array();
            }

            set_transient( 'redux_contributors', $contributors, 3600 );

            return $contributors;
        }
    }

    new Redux_Welcome();

