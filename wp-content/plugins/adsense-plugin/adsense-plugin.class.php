<?php

/* Class of Google AdSense functions */
class adsns {
	var $page_title; /* Title for options page */
	var $menu_title; /* Name in menu */
	var $adsns_options;

	/* Constructor */
	function adsns() {
		global $wpmu;
		if ( 1 == $wpmu )
			$this->adsns_options = get_site_option( 'adsns_settings' );
		else
			$this->adsns_options = get_option( 'adsns_settings' );
		$this->adsns_options['code']		=	stripslashes( $this->adsns_options['code'] );
		$this->adsns_options['num_show']	=	0;
		update_option( 'adsns_settings', $this->adsns_options );
	}

	/* Show ads after post on a single page */
	function adsns_end_post_ad( $content ) {
		global $adsns_count;
		/*$this->adsns_donate();*/  /* Calling a donate function */
		if ( ! is_feed() && is_single() && $adsns_count < $this->adsns_options['max_ads'] && $adsns_count < $this->adsns_options['max_homepostads'] ) {  /* Checking if we are on a single page */
			$content.= '<div id="end_post_ad" class="ads">' . $this->adsns_options['code'] . '</div>';  /* Adding an ad code on page */
			$this->adsns_options['num_show'] ++;  /* Counting views */
			update_option( 'adsns_settings', $this->adsns_options );
			$adsns_count = $this->adsns_options['num_show'];
		}
		return $content;
	}

	/* Show ads after comment form */
	function adsns_end_comment_ad() {
		global $adsns_count;
		/*$this->adsns_donate();*/
		if( ! is_feed() && $adsns_count < $this->adsns_options['max_ads'] && $adsns_count < $this->adsns_options['max_homepostads'] ) {
			echo '<div id="end_comment_ad" class="ads">' . $this->adsns_options['code'] . '</div>';
			$this->adsns_options['num_show'] ++;  // Counting views
			update_option( 'adsns_settings', $this->adsns_options );
			$adsns_count = $this->adsns_options['num_show'];
		}
	}

	/* Show ads after post on home page */
	function adsns_end_home_post_ad( $content ) {
		global $adsns_count;
		if ( $adsns_count < $this->adsns_options['max_ads'] && $adsns_count < $this->adsns_options['max_homepostads'] ) {
			if ( ! is_feed() && ( is_home() || is_front_page() ) ) {
		/*	if( ! is_feed() && is_home() ) { */
				/*$this->adsns_donate();*/		/* Calling a donate function */
				$content .= '<div class="ads">' . $this->adsns_options['code'] . '</div>';
				$this->adsns_options['num_show'] ++;  /* Counting views */
				update_option( 'adsns_settings', $this->adsns_options );
				$adsns_count = $this->adsns_options['num_show']; /* Restore count value */
			}
		}
		return $content;
	}

	/* Show ads in footer */
	function adsns_end_footer_ad() {
		global $adsns_count;
		/*$this->adsns_donate();*/
		if( ! is_feed() && $adsns_count < $this->adsns_options['max_ads'] && $adsns_count < $this->adsns_options['max_homepostads'] ) {
			echo '<div id="end_footer_ad" class="ads">' . $this->adsns_options['code'] . '</div>';
			$this->adsns_options['num_show'] ++;  /* Counting views */
			update_option( 'adsns_settings', $this->adsns_options );
			$adsns_count = $this->adsns_options['num_show']; /* Restore count value */
		}
	}

	/* Add 'BWS Plugins' menu at the left side in administer panel */
	function adsns_add_admin_menu() {
		global $bstwbsftwppdtplgns_options, $wpmu, $bstwbsftwppdtplgns_added_menu;
		$bws_menu_version = get_plugin_data( plugin_dir_path( __FILE__ ) . "bws_menu/bws_menu.php" );
		$bws_menu_version = $bws_menu_version["Version"];
		$base = plugin_basename( __FILE__ );

		if ( ! isset( $bstwbsftwppdtplgns_options ) ) {
			if ( 1 == $wpmu ) {
				if ( ! get_site_option( 'bstwbsftwppdtplgns_options' ) )
					add_site_option( 'bstwbsftwppdtplgns_options', array(), '', 'yes' );
				$bstwbsftwppdtplgns_options = get_site_option( 'bstwbsftwppdtplgns_options' );
			} else {
				if ( ! get_option( 'bstwbsftwppdtplgns_options' ) )
					add_option( 'bstwbsftwppdtplgns_options', array(), '', 'yes' );
				$bstwbsftwppdtplgns_options = get_option( 'bstwbsftwppdtplgns_options' );
			}
		}

		if ( isset( $bstwbsftwppdtplgns_options['bws_menu_version'] ) ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			unset( $bstwbsftwppdtplgns_options['bws_menu_version'] );
			update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] ) || $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] < $bws_menu_version ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_added_menu ) ) {
			$plugin_with_newer_menu = $base;
			foreach ( $bstwbsftwppdtplgns_options['bws_menu']['version'] as $key => $value ) {
				if ( $bws_menu_version < $value && is_plugin_active( $base ) ) {
					$plugin_with_newer_menu = $key;
				}
			}
			$plugin_with_newer_menu = explode( '/', $plugin_with_newer_menu );
			$wp_content_dir = defined( 'WP_CONTENT_DIR' ) ? basename( WP_CONTENT_DIR ) : 'wp-content';
			if ( file_exists( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' ) )
				require_once( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' );
			else
				require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
			$bstwbsftwppdtplgns_added_menu = true;
		}

		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( "images/px.png", __FILE__ ), 1001 );
		add_submenu_page( 'bws_plugins', __( 'AdSense Settings', 'adsense'), __( 'AdSense', 'adsense' ), 'manage_options', "adsense-plugin.php", array( $this, 'adsns_settings_page' ) );
	}

	/* Add a link for settings page */
	function adsns_plugin_action_links( $links, $file ) {
		global $this_adsns_plugin;
		if ( $file == $this_adsns_plugin ) {
			$settings_link = '<a href="admin.php?page=adsense-plugin.php">' . __( 'Settings', 'adsense' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	function adsns_register_plugin_links( $links, $file ) {
		global $this_adsns_plugin;
		if ( $file == $this_adsns_plugin ) {
			$links[]	=	'<a href="admin.php?page=adsense-plugin.php">' . __( 'Settings', 'adsense' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/adsense-plugin/faq/" target="_blank">' . __( 'FAQ', 'adsense' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'adsense' ) . '</a>';
		}
		return $links;
	}

	function adsns_plugin_init() {
		/* Internationalization */
		load_plugin_textdomain( 'adsense', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		/* Call register settings function */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "adsense-plugin.php" == $_GET['page'] ) )
			$this->adsns_activate();
	}

	function adsns_plugin_admin_init() {
		global $bws_plugin_info, $adsns_plugin_info;
		
		if ( ! $adsns_plugin_info )
			$adsns_plugin_info = get_plugin_data( plugin_dir_path( __FILE__ ) . "adsense-plugin.php" );

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '80', 'version' => $adsns_plugin_info["Version"] );

		$this->adsns_version_check();
	}

	/* Creating a default options for showing ads. Starts on plugin activation. */
	function adsns_activate() {
		global $wpmu, $adsns_options, $count, $current_count, $adsns_count, $max_ads, $adsns_plugin_info;

		if ( ! $adsns_plugin_info ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$adsns_plugin_info = get_plugin_data( plugin_dir_path( __FILE__ ) . "adsense-plugin.php" );
		}

		$adsns_options_defaults = array(
			'plugin_option_version'	=>	$adsns_plugin_info["Version"],
			'num_show'				=>	'0',
			'donate'				=>	'0',
			'max_ads'				=>	'3',
			'max_homepostads'		=>	'1',
			'clientid'				=>	'',
			'clientid_prefix'		=>	'pub',
			'donate_id'				=>	'1662250046693311',
			'adtypeselect'			=>	'text',
			'donate_width'			=>	'',
			'donate_height'			=>	'',
			'default'				=>	'468x60',
			'image_only'			=>	'',
			'link_unit'				=>	'',
			'adtype'				=>	'adunit',
			'corner_style'			=>	'none',
			'border'				=>	'#FFFFFF',
			'title'					=>	'#0000FF',
			'background'			=>	'#FFFFFF',
			'text'					=>	'#000000',
			'url'					=>	'#008000',
			'pallete'				=>	'Default Google pallete',
			'position'				=>	'homepostend',
			'widget_title'			=>	'',
			'code'					=> '	<script type="text/javascript">
												google_ad_client	=	"pub-1662250046693311";
												google_ad_width		=	468;
												google_ad_height	=	60;
												google_ad_format	=	"468x60_as";
												google_ad_type		=	"text";
												google_color_border	=	"#FFFFFF";
												google_color_bg		=	"#FFFFFF";
												google_color_link	=	"#0000FF";
												google_color_text	=	"#000000";
												google_color_url	=	"#008000";
											</script><input type="hidden" value="Version: 1.11" />'
		);

		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'adsns_settings' ) )
				add_site_option( 'adsns_settings', $new_options, '', 'yes' );
		} else {
			if ( ! get_option( 'adsns_settings' ) )
				add_option( 'adsns_settings', $new_options, '', 'yes' );
		}

		$count			=	0;	/* Current number of showed ads */
		$current_count	=	0; 	/* Tmp var for storing a number of already showed ads */
		$adsns_count	=	0; 	/* Number of posts on home page */

		if ( 1 == $wpmu )
			$adsns_options = get_site_option( 'adsns_settings' );
		else
			$adsns_options = get_option( 'adsns_settings' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $adsns_options['plugin_option_version'] ) || $adsns_options['plugin_option_version'] != $adsns_plugin_info["Version"] ) {
			$adsns_options = array_merge( $adsns_options_defaults, $adsns_options );
			$adsns_options['plugin_option_version'] = $adsns_plugin_info["Version"];
			update_option( 'adsns_settings', $adsns_options );
		}

		$max_ads = $adsns_options['max_ads']; /* Max number of ads */
	}

	/* Donate settings */
	function adsns_donate() {		
		if ( $this->adsns_options['donate'] > 0 ) {
			$don = intval( 100/$this->adsns_options['donate'] ); /* Calculating number of donate ads for showing */
		}
		if ( $this->adsns_options['donate'] > 0 && $this->adsns_options['num_show'] % $don == 0) { /* Checking if now showing ad must be a donate ad */
			$dimensions = explode( "x", $this->adsns_options['default'] ); /* Calculating dimensions of ad block */
			$this->adsns_options['donate_width']	=	$dimensions[0]; /* Width */
			$this->adsns_options['donate_height']	=	$dimensions[1]; /* Height */
			$don_code = '<script type="text/javascript">
							google_ad_client	=	"pub-' . $this->adsns_options['donate_id'] . '";
							google_ad_width		=	' . $this->adsns_options['donate_width'] . ';
							google_ad_height	=	' . $this->adsns_options['donate_height'] . ';
							google_ad_format	=	"' . $this->adsns_options['default'] . '_as";
							google_ad_type		=	"text";
							google_color_border	=	"' . $this->adsns_options['border'] . '";
							google_color_bg		=	"' . $this->adsns_options['background'] . '";
							google_color_link	=	"' . $this->adsns_options['title'] . '";
							google_color_text	=	"' . $this->adsns_options['text'] . '";
							google_color_url	=	"' . $this->adsns_options['url'] . '";
						</script><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script><input type="hidden" value="Version: 1.11" />';
			$this->adsns_options['code'] = $don_code;
			/* update_option( 'adsns_settings', $this->adsns_options ); */
		} else {
			if ( 'ad_unit' == $this->adsns_options['adtype'] ) {
				if ( 'default_image' == $this->adsns_options['adtypeselect'] )
					$adtypeselect = 'default';
				else
					$adtypeselect = $this->adsns_options['adtypeselect'];
				$dimensions	=	explode( "x", $this->adsns_options[ $adtypeselect ] ); /* Calculating dimensions of ad block */
				$format		=	$this->adsns_options[ $adtypeselect ];
				$format		.=	'_as';
				switch( $this->adsns_options['adtypeselect'] ) {
						case 'image_only':
							$type = 'google_ad_type = "image";';
							break;
						case 'default_image':
							$type = 'google_ad_type = "text_image";';
							break;
						default:
							$type = 'google_ad_type = "text";';
							break;
				}
			} else {
				$dimensions	=	explode( "x", $this->adsns_options[ $this->adsns_options['adtype'] ] ); /* Calculating dimensions of ad block */
				$format		=	$this->adsns_options[ $this->adsns_options['adtype'] ];
				$format		.=	'_0ads_al';
				$type		=	'';
			}

			if ( 'none' == $this->adsns_options['corner_style'] )
				$features = '';
			else
				$features = 'google_ui_features = "rc:' . $this->adsns_options['corner_style'] . '";';
			$this->adsns_options['donate_width']	=	$dimensions[0]; /* Width */
			$this->adsns_options['donate_height']	=	$dimensions[1]; /* Height */
			$don_code = '<script type="text/javascript">
							google_ad_client	=	"' . $this->adsns_options['clientid_prefix'] . '-' . $this->adsns_options['clientid'] . '";
							google_ad_width		=	' . $this->adsns_options['donate_width'] . ';
							google_ad_height	=	' . $this->adsns_options['donate_height'] . ';
							google_ad_format	=	"' . $format . '";
							' . $type . '
							google_color_border	=	"' . $this->adsns_options['border'] . '";
							google_color_bg		=	"' . $this->adsns_options['background'] . '";
							google_color_link	=	"' . $this->adsns_options['title'] . '";
							google_color_text	=	"' . $this->adsns_options['text'] . '";
							google_color_url	=	"' . $this->adsns_options['url'] . '";
							' . $features . '
						</script><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script><input type="hidden" value="Version: 1.11" />';
			$this->adsns_options['code'] = $don_code;
			/* update_option( 'adsns_settings', $this->adsns_options ); */
		}
	}

	/* Function check if plugin is compatible with current WP version  */
	function adsns_version_check() {
		global $wp_version, $adsns_plugin_info;
		$require_wp		=	"3.0"; /* Wordpress at least requires version */
		$plugin			=	plugin_basename( plugin_dir_path( __FILE__ ) . "adsense-plugin.php" );
		if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>" . $adsns_plugin_info['Name'] . " </strong> " . __( 'requires', 'adsense' ) . " <strong>WordPress " . $require_wp . "</strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'adsense') . "<br /><br />" . __( 'Back to the WordPress', 'adsense') . " <a href='" . get_admin_url( null, 'plugins.php' ) . "'>" . __( 'Plugins page', 'adsense') . "</a>." );
			}
		}
	}

	/* Saving settings */
	function adsns_settings_page() {
		global $adsns_plugin_info, $adsns_options;
		echo '
		<div class="wrap" id="adsns_wrap">
		<div class="icon32 icon32-bws" id="icon-options-general"></div>
		<h2>' . $this->page_title . '</h2>';
		if ( isset( $_REQUEST['adsns_update'] ) && check_admin_referer( plugin_basename(__FILE__), 'adsns_nonce_name' )  ) { /* if click on Save Changes button */
			if ( 0 < strlen( $_REQUEST['client_id'] ) ) {
				echo "<div class='updated'><p>" . __( "Settings saved", 'adsense' ) . "</p></div>";
				if ( 3 <= strlen( trim( $_REQUEST['clientid_prefix'] ) ) && 'pub' == substr( trim( $_REQUEST['clientid_prefix'] ) , -3, 3 ) ) {
					$this->adsns_options['clientid_prefix'] = $_REQUEST['clientid_prefix'];
					if ( isset( $_REQUEST['client_id'] ) ) { /* client */
						$this->adsns_options['clientid'] = $_REQUEST['client_id'];
					}
					if ( isset( $_REQUEST['mycode'] ) ) { /* ad code */
						$id = stripslashes( $_REQUEST['mycode'] );
						if ( 0 < strlen( $id ) ) {
							//$this->adsns_options['code'] = $id;
						}
					}
					if ( isset( $_REQUEST['homeAds'] ) ) { /* select */
						$this->adsns_options['max_homepostads'] = $_REQUEST['homeAds'];
					}
					if ( isset( $_REQUEST['adtypeselect'] ) ) { /* adtypeselect */
						$this->adsns_options['adtypeselect'] = $_REQUEST['adtypeselect'];
					} else {
						$this->adsns_options['adtypeselect'] = '';
					}
					if ( isset( $_REQUEST['default'] ) ) { /* format */
						$this->adsns_options['default'] = $_REQUEST['default'];
					} else {
						$this->adsns_options['default'] = '';
					}
					if ( isset( $_REQUEST['image_only'] ) ) { /* format */
						$this->adsns_options['image_only'] = $_REQUEST['image_only'];
					} else {
						$this->adsns_options['image_only'] = '';
					}
					if ( isset( $_REQUEST['link_unit'] ) ) { /* format */
						$this->adsns_options['link_unit'] = $_REQUEST['link_unit'];
					} else {
						$this->adsns_options['link_unit'] = '';
					}
					if ( isset( $_REQUEST['adtype'] ) ) { /* adtype */
						$this->adsns_options['adtype'] = $_REQUEST['adtype'];
					}
					if ( isset( $_REQUEST['corner_style'] ) ) { /* corner_style */
						$this->adsns_options['corner_style'] = $_REQUEST['corner_style'];
					}
					if ( isset( $_REQUEST['pallete'] ) ) { /* pallete */
						$this->adsns_options['pallete'] = $_REQUEST['pallete'];
					}
					if ( isset( $_REQUEST['border'] ) ) { /* border */
						$this->adsns_options['border'] = $_REQUEST['border'];
					}
					if ( isset( $_REQUEST['title'] ) ) { /* title */
						$this->adsns_options['title'] = $_REQUEST['title'];
					}
					if ( isset( $_REQUEST['background'] ) ) { /* background */
						$this->adsns_options['background'] = $_REQUEST['background'];
					}
					if ( isset( $_REQUEST['text'] ) ) { /* text */
						$this->adsns_options['text'] = $_REQUEST['text'];
					}
					if ( isset( $_REQUEST['url'] ) ) { /* url */
						$this->adsns_options['url'] = $_REQUEST['url'];
					}
					if ( isset( $_REQUEST['position'] ) ) { /* position */
						$this->adsns_options['position'] = $_REQUEST['position'];
					}
					if ( isset( $_REQUEST['donate'] ) ) { /* donate */
						$this->adsns_options['donate'] = $_REQUEST['donate'];
					}
					if ( 'ad_unit' == $this->adsns_options['adtype'] ) {
						if ( 'default_image' == $this->adsns_options['adtypeselect'] )
							$adtypeselect = 'default';
						else
							$adtypeselect = $this->adsns_options['adtypeselect'];
						$dimensions = explode( "x", $this->adsns_options[ $adtypeselect ] ); /* Calculating dimensions of ad block */
						$format = $this->adsns_options[ $adtypeselect ];
						$format .= '_as';
						switch($this->adsns_options['adtypeselect']) {
								case 'image_only':
									$type = 'google_ad_type = "image";';
									break;
								case 'default_image':
									$type = 'google_ad_type = "text_image";';
									break;
								default:
									$type = 'google_ad_type = "text";';
									break;
						}
					} else {
						$dimensions = explode( "x", $this->adsns_options[ $this->adsns_options['adtype'] ] ); /* Calculating dimensions of ad block */
						$format = $this->adsns_options[ $this->adsns_options['adtype'] ];
						$format .= '_0ads_al';
						$type = '';
					}

					$this->adsns_options['donate_width']	=	$dimensions[0]; /* Width */
					$this->adsns_options['donate_height']	=	$dimensions[1]; /* Height */
					$don_code = '<script type="text/javascript">
									google_ad_client	=	"'. $this->adsns_options['clientid_prefix'] . '-' . $this->adsns_options['clientid'] . '";
									google_ad_width		=	' . $this->adsns_options['donate_width'] . ';
									google_ad_height	=	' . $this->adsns_options['donate_height'] . ';
									google_ad_format	=	"' . $format . '";
									' . $type . '
									google_color_border	=	"' . $this->adsns_options['border'] . '";
									google_color_bg		=	"' . $this->adsns_options['background'] . '";
									google_color_link	=	"' . $this->adsns_options['title'] . '";
									google_color_text	=	"' . $this->adsns_options['text'] . '";
									google_color_url	=	"' . $this->adsns_options['url'] . '";
								</script><script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script><input type="hidden" value="Version: 1.11" />';
					$this->adsns_options['code']					=	$don_code;
					update_option( 'adsns_settings', $this->adsns_options );
				} else
					echo "<div class='error'><p>" . __( "Please enter valid Publisher ID.", 'adsense' ) . "</p></div>";
			} else
				echo "<div class='error'><p>" . __( "Please enter your Publisher ID.", 'adsense' ) . "</p></div>";
		} /* Click on Save Changes button end */
		$this->adsns_view_options_page();
		echo '</div>';
	}

	/* Admin interface of plugin */
	function adsns_view_options_page() {
		global $wpmu, $adsns_options;
		static $sp_nonce_flag = false;
		?>
		<div id="adsns_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'adsense' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'adsense' ); ?></p></div>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="admin.php?page=adsense-plugin.php"><?php _e( 'Settings', 'adsense' ); ?></a>
			<a class="nav-tab" href="http://bestwebsoft.com/plugin/google-adsense-plugin/#faq" target="_blank"><?php _e( 'FAQ', 'adsense' ); ?></a>
		</h2>
		<form id="adsns_settings_form" name="option" action="" method="post">
			<table id="adsns_main">
				<tr class="settings_head_1">
					<th colspan="2"><?php _e( 'Network', 'adsense' ); ?></th>
				</tr>
				<tr class="settings_body_1">
					<td id="network" class="left" ><?php _e( 'Publisher ID:', 'adsense' ); ?></td>
					<td class="right">
						<input type="text" id="clientid_prefix" name="clientid_prefix" size="8" maxlength="10" value="<?php echo $this->adsns_options['clientid_prefix'] ?>" />
						-
						<input type="hidden" id="client_id_val" name="client_id_val" value="<?php echo $this->adsns_options['clientid'] ?>" />
						<input type="text" id="client_id" name="client_id" class ="positive-integer" size="20" maxlength="16" value="<?php echo $this->adsns_options['clientid'] ?>" />
						<br />
						<span class="description"><?php _e( 'Publisher ID is a unique identifier of', 'adsense' ); ?> <a target="_blank" href="https://www.google.com/adsense"><?php _e( 'your account', 'adsense' ); ?></a> <?php _e( 'in Google AdSense.', 'adsense' ); ?></span>
					</td>
				</tr>
				<tr class="adsns_empty"></tr>
				<tr class="settings_head_2">
					<th colspan="2"><?php _e( 'Ad Type &amp; Format', 'adsense' ); ?></th>
				</tr>
				<tr class="settings_body_2">
					<td class="left"><?php _e( 'Type:', 'adsense' ); ?></td>
					<td class="right">
						<input type="hidden" id="adtype_val" value="<?php echo $this->adsns_options['adtype'] ?>">
						<input type="radio" name="adtype" id="ad_type1" value="ad_unit" <?php if ( 'ad_unit' == $this->adsns_options['adtype'] || 'adunit' == $this->adsns_options['adtype'] ) echo 'checked="checked"'; ?> />
						<label for="ad_type1"><?php _e( 'Ad block', 'adsense' ); ?></label>
						<input type="hidden" id="adtypesel_val" value="<?php echo $this->adsns_options['adtypeselect'] ?>">
						<select id="adtypeselect" name ="adtypeselect" style="margin-left: 10px;">
							<option value="default_image" <?php if ( 'default_image' == $this->adsns_options['adtypeselect'] || 'text_image' == $this->adsns_options['adtypeselect'] ) echo 'selected="selected"'; ?>><?php _e( 'Text and image ads', 'adsense' ); ?></option>
							<option value="default" <?php if ( 'default' == $this->adsns_options['adtypeselect'] || 'text' == $this->adsns_options['adtypeselect'] ) echo 'selected="selected"'; ?>><?php _e( 'Text ads only (default)', 'adsense' ); ?></option>
							<option value="image_only" <?php if ( 'image_only' == $this->adsns_options['adtypeselect'] || 'image' == $this->adsns_options['adtypeselect'] ) echo 'selected="selected"'; ?>><?php _e( 'Image ads only', 'adsense' ); ?></option>
						</select>
						<br />
						<input type="radio" name="adtype" id="ad_type2" value="link_unit" <?php if ( 'link_unit' == $this->adsns_options['adtype'] ) echo 'checked="checked"'; ?> />
						<label for="ad_type2"><?php _e( 'Block of links', 'adsense' ); ?></label>
					</td>
				</tr>
				<tr class="settings_body_2">
					<td class="left"><?php _e( 'Format:', 'adsense' ); ?></td>
					<td class="right">
						<div id="def" <?php if( 'ad_unit' == $this->adsns_options['adtype'] && ( 'default' == $this->adsns_options['adtypeselect'] || 'default_image' == $this->adsns_options['adtypeselect'] ) || 'text' == $this->adsns_options['adtypeselect'] || 'text_image' == $this->adsns_options['adtypeselect'] ) echo 'style="visibility: visible;"'; else echo 'style="visibility: hidden;"'; ?>>
							<input type="hidden" id="default_val" value="<?php echo $this->adsns_options['default'] ?>" />
							<select id="default" name="default">
								<optgroup label="Horizontal">
									<option value="728x90" <?php if ( '728x90' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>728x90 Leaderboard</option>
									<option value="468x60" <?php if ( '468x60' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>468x60 Banner</option>
									<option value="234x60" <?php if ( '234x60' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>234x60 Half Banner</option>
								</optgroup>
								<optgroup label="Vertical">
									<option value="120x600" <?php if ( '120x600' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>120x600 Skyscraper</option>
									<option value="160x600" <?php if ( '160x600' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>160x600 Wide Skyscraper</option>
									<option value="120x240" <?php if ( '120x240' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>120x240 Vertical Banner</option>
								</optgroup>
								<optgroup label="Square">
									<option value="336x280" <?php if ( '336x280' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>336x280 Large Rectangle</option>
									<option value="300x250" <?php if ( '300x250' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>300x250 Medium Rectangle</option>
									<option value="250x250" <?php if ( '250x250' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>250x250 Square</option>
									<option value="200x200" <?php if ( '200x200' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>200x200 Small Square</option>
									<option value="180x150" <?php if ( '180x150' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>180x150 Small Rectangle</option>
									<option value="125x125" <?php if ( '125x125' == $this->adsns_options['default'] ) echo 'selected="selected"'; ?>>125x125 Button</option>
								</optgroup>
							</select>
						</div>
						<div id="img_only" <?php if ( 'ad_unit' == $this->adsns_options['adtype'] && ( 'image_only' == $this->adsns_options['adtypeselect'] || 'image' == $this->adsns_options['adtypeselect'] ) ) echo 'style="visibility: visible;"'; else echo 'style="visibility: hidden;"'; ?> class="right_img">
							<input type="hidden" id="image_only_val" value="<?php echo $this->adsns_options['image_only'] ?>" />
							<select id="image_only" name="image_only">
								<optgroup label="Horizontal">
									<option value="728x90" <?php if ( '728x90' == $this->adsns_options['image_only'] ) echo 'selected="selected"'; ?>>728x90 Leaderboard</option>
									<option value="468x60" <?php if ( '468x60' == $this->adsns_options['image_only'] ) echo 'selected="selected"'; ?>>468x60 Banner</option>
								</optgroup>
								<optgroup label="Vertical">
									<option value="120x600" <?php if ( '120x600' == $this->adsns_options['image_only'] ) echo 'selected="selected"'; ?>>120x600 Skyscraper</option>
									<option value="160x600" <?php if ( '160x600' == $this->adsns_options['image_only'] ) echo 'selected="selected"'; ?>>160x600 Wide Skyscraper</option>
								</optgroup>
								<optgroup label="Square">
									<option value="336x280" <?php if ( '336x280' == $this->adsns_options['image_only'] ) echo 'selected="selected"'; ?>>336x280 Large Rectangle</option>
									<option value="300x250" <?php if ( '300x250' == $this->adsns_options['image_only'] ) echo 'selected="selected"'; ?>>300x250 Medium Rectangle</option>
									<option value="250x250" <?php if ( '250x250' == $this->adsns_options['image_only'] ) echo 'selected="selected"'; ?>>250x250 Square</option>
									<option value="200x200" <?php if ( '200x200' == $this->adsns_options['image_only'] ) echo 'selected="selected"'; ?>>200x200 Small Square</option>
								</optgroup>
							</select>
						</div>
						<div id="lnk_unit" <?php if ( 'link_unit' == $this->adsns_options['adtype'] ) echo 'style="visibility: visible;margin-top: -24px"'; else echo 'style="visibility: hidden;margin-top: -32px"'; ?> class="right">
							<input type="hidden" id="link_unit_val" value="<?php echo $this->adsns_options['link_unit'] ?>" />
							<select id="link_unit" name="link_unit">
								<optgroup label="Horizontal">
									<option value="728x15" <?php if ( '728x15' == $this->adsns_options['link_unit'] ) echo 'selected="selected"'; ?>>728x15</option>
									<option value="468x15" <?php if ( '468x15' == $this->adsns_options['link_unit'] ) echo 'selected="selected"'; ?>>468x15</option>
								</optgroup>
								<optgroup label="Square">
									<option value="200x90" <?php if ( '200x90' == $this->adsns_options['link_unit'] ) echo 'selected="selected"'; ?>>200x90</option>
									<option value="180x90" <?php if ( '180x90' == $this->adsns_options['link_unit'] ) echo 'selected="selected"'; ?>>180x90</option>
									<option value="160x90" <?php if ( '160x90' == $this->adsns_options['link_unit'] ) echo 'selected="selected"'; ?>>160x90</option>
									<option value="120x90" <?php if ( '120x90' == $this->adsns_options['link_unit'] ) echo 'selected="selected"'; ?>>120x90</option>
								</optgroup>
							</select>
						</div>
					</td>
				</tr>
				<tr class="adsns_empty" ></tr>
				<tr class="settings_head_3">
					<th colspan="2"><?php _e( 'Position &amp; amount of ads', 'adsense' ); ?></th>
				</tr>
				<tr id="pos_num" class="settings_body_3">
					<td class="left"><?php _e( 'Position:', 'adsense' ); ?></td>
					<td class="right">
						<input type="hidden" id="position_val" value="<?php echo $this->adsns_options['position'] ?>" />
						<select name="position" id="position">
							<option value="postend" <?php if ( 'postend' == $this->adsns_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'Below the post (Single post page)', 'adsense' ); ?></option>
							<option value="homepostend" <?php if ( 'homepostend' == $this->adsns_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'Below the post (Home page)', 'adsense' ); ?></option>
							<option value="homeandpostend" <?php if ( 'homeandpostend' == $this->adsns_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'Below the post (Single post page and Home page)', 'adsense' ); ?></option>
							<option value="commentform" <?php if ( 'commentform' == $this->adsns_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'Below the comment form', 'adsense' ); ?></option>
							<option value="footer" <?php if ( 'footer' == $this->adsns_options['position'] ) echo 'selected="selected"'; ?>><?php _e( 'Above the footer', 'adsense' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="settings_body_3">
					<td class="left"><?php _e( 'Number of Ads:', 'adsense' ); ?></td>
					<td class="right">
						<input type="hidden" id="homeads_val" name="homeads_val" value="<?php echo $this->adsns_options['max_homepostads'] ?>" />
						<select name="homeAds" id="homeAds" style="width: 40px;">
							<option value="1" <?php if ( isset( $this->adsns_options['max_homepostads'] ) && '1' == $this->adsns_options['max_homepostads'] ) echo 'selected="selected"'; ?>>1</option>
							<option value="2" <?php if ( isset( $this->adsns_options['max_homepostads'] ) && '2' == $this->adsns_options['max_homepostads'] ) echo 'selected="selected"'; ?>>2</option>
							<option value="3" <?php if ( isset( $this->adsns_options['max_homepostads'] ) && '3' == $this->adsns_options['max_homepostads'] ) echo 'selected="selected"'; ?>>3</option>
						</select>
						<br />
						<span class="description"><?php _e( 'Number of ads below the posts on the home page.', 'adsense' ); ?></span>
					</td>
				</tr>
				<tr class="adsns_empty"></tr>
				<tr class="settings_head_4">
					<th colspan="2"><?php _e( 'Visualisation', 'adsense' ); ?></th>
				</tr>
				<tr id="visual" class="settings_body_4">
					<td>
						<label for="Border" class="left"><?php _e( 'Colors:', 'adsense' ); ?></label>
						<input type="hidden" id="border_val" value="<?php echo $this->adsns_options['border'] ?>" />
						<input type="hidden" id="title_val" value="<?php echo $this->adsns_options['title'] ?>" />
						<input type="hidden" id="background_val" value="<?php echo $this->adsns_options['background'] ?>" />
						<input type="hidden" id="text_val" value="<?php echo $this->adsns_options['text'] ?>" />
						<input type="hidden" id="url_val" value="<?php echo $this->adsns_options['url'] ?>" />
					</td>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" class="right">
							<tr class="paddings">
								<td align="right" class="adsns_editional_css">
									<label for="Border"><?php _e( 'Border', 'adsense' ); ?>&nbsp;&nbsp;&nbsp;</label>
								</td>
								<td>
									<input type="text" id="Border" size="7" maxlength="7" name="border" value="<?php echo $this->adsns_options['border']; ?>" />
								</td>
							</tr>
							<tr class="adsns_colorpicker">
								<td id="colorpicker1" class="col_pal" colspan="2"></td>
							</tr>
							<tr class="paddings">
								<td align="right" class="adsns_editional_css">
									<label for="Title"><?php _e( 'Title', 'adsense' ); ?>&nbsp;&nbsp;&nbsp;</label>
								</td>
								<td>
									<input type="text" id="Title" size="7" maxlength="7" name="title" value="<?php echo $this->adsns_options['title']; ?>" />
								</td>
							</tr>
							<tr class="adsns_colorpicker">
								<td id="colorpicker2" class="col_pal" colspan="2"></td>
							</tr>
							<tr class="paddings">
								<td align="right" class="adsns_editional_css">
									<label for="Background"><?php _e( 'Background', 'adsense' ); ?>&nbsp;&nbsp;&nbsp;</label>
								</td>
								<td>
									<input type="text" id="Background" size="7" maxlength="7" name="background" value="<?php echo $this->adsns_options['background']; ?>" />
								</td>
							</tr>
							<tr class="adsns_colorpicker">
								<td id="colorpicker3" class="col_pal" colspan="2"></td>
							</tr>
							<tr class="paddings">
								<td align="right" class="adsns_editional_css">
									<label for="Text"><?php _e( 'Text', 'adsense' ); ?>&nbsp;&nbsp;&nbsp;</label>
								</td>
								<td>
									<input type="text" id="Text" size="7" maxlength="7" name="text" value="<?php echo $this->adsns_options['text']; ?>" />
								</td>
							</tr>
							<tr class="adsns_colorpicker">
								<td id="colorpicker4" class="col_pal" colspan="2"></td>
							</tr>
							<tr class="paddings">
								<td align="right" class="adsns_editional_css">
									<label for="URL"><?php _e( 'URL', 'adsense' ); ?>&nbsp;&nbsp;&nbsp;</label>
								</td>
								<td>
									<input type="text" id="URL" size="7" maxlength="7" name="url" value="<?php echo $this->adsns_options['url']; ?>" />
								</td>
							</tr>
							<tr class="adsns_colorpicker">
								<td id="colorpicker5" class="col_pal" colspan="2"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="settings_body_4">
					<td class="left"><?php _e( 'Palette:', 'adsense' ); ?></td>
					<td class="right">
						<select id="pallete" name="pallete">
							<optgroup label="Default Pallete">
								<option value="Default Google pallete" <?php if( 'Default Google pallete' == $this->adsns_options['pallete'] ) echo 'selected="selected"';?>><?php _e( 'Default Google pallete', 'adsense' ); ?></option>
							</optgroup>
							<optgroup label="AdSense Pallete">
								<option value="Open Air" <?php if ( 'Open Air' == $this->adsns_options['pallete'] ) echo 'selected="selected"';?>><?php _e( 'Open Air', 'adsense' ); ?></option>
								<option value="Seaside" <?php if ( 'Seaside' == $this->adsns_options['pallete'] ) echo 'selected="selected"';?>><?php _e( 'Seaside', 'adsense' ); ?></option>
								<option value="Shadow" <?php if ( 'Shadow' == $this->adsns_options['pallete'] ) echo 'selected="selected"';?>><?php _e( 'Shadow', 'adsense' ); ?></option>
								<option value="Blue Mix" <?php if ( 'Blue Mix' == $this->adsns_options['pallete'] ) echo 'selected="selected"';?>><?php _e( 'Blue Mix', 'adsense' ); ?></option>
								<option value="Ink" <?php if ( 'Ink' == $this->adsns_options['pallete'] ) echo 'selected="selected"';?>><?php _e( 'Ink', 'adsense' ); ?></option>
								<option value="Graphite" <?php if ( 'Graphite' == $this->adsns_options['pallete'] ) echo 'selected="selected"';?>><?php _e( 'Graphite', 'adsense' ); ?></option>
							</optgroup>
						</select>
						<br />
						<span class="description"><?php _e( 'These are the standard Google color palettes.', 'adsense' ); ?></span>
					</td>
				</tr>
				<tr class="settings_body_4">
					<td class="left"><?php _e( 'Corner Style:', 'adsense' ); ?></td>
					<td class="right">
						<input type="hidden" id="corner_style_val" value="<?php echo $this->adsns_options['corner_style'] ?>" />
						<select name="corner_style" id="corner_style">
							<option value="none" <?php if ( 'none' == $this->adsns_options['corner_style'] ) echo 'selected="selected"';?>> <?php _e( 'Select a corner style', 'adsense' ); ?> </option>
							<option value="0" <?php if ( '0' == $this->adsns_options['corner_style'] ) echo 'selected="selected"';?>> <?php _e( 'Square corners', 'adsense' ); ?> </option>
							<option value="6" <?php if ( '6' == $this->adsns_options['corner_style'] ) echo 'selected="selected"';?>> <?php _e( 'Slightly rounded corners', 'adsense' ); ?> </option>
							<option value="10" <?php if ( '10' == $this->adsns_options['corner_style'] ) echo 'selected="selected"';?>> <?php _e( 'Rounded corners', 'adsense' ); ?> </option>
						</select>
						<br />
						<span class="description"><?php _e( 'Corner style property will help you to make your Ad corners rounded.', 'adsense' ); ?></span>
					</td>
				</tr>
				<tr class="adsns_empty"></tr>
				<!--<tr class="settings_head_5">
					<th colspan="2"><?php _e( 'Donations', 'adsense' ); ?></th>
				</tr>
				<tr class="settings_body_5">
					<td id="donate_menu" class="left"><?php _e( 'Donate:', 'adsense' ); ?></td>
					<td class="right">
						<input type="hidden" id="donate_val" value="<?php echo $this->adsns_options['donate'] ?>" />
						<input type="text" id="donate" size="2" maxlength="2" name="donate" style="padding-left: 10px; padding-right: 10px; text-align: center;" value="<?php echo $this->adsns_options['donate'] ?>" />%
						<br />
						<span class="description"><?php _e( 'Support us by Donating Ad Space.', 'adsense' ); ?></span>
						<br />
						<span class="description"><?php _e( 'Please enter a percentage value of the ad slots you are ready to share [Default: 0%].', 'adsense' ); ?></span>
					</td>
				</tr>
				<tr id="code_generate">
					<td colspan="2">
						<div>
							<textarea id="mycode" name="mycode" rows="15" cols="60"></textarea>
							<input type="button" id="update" value="Update!" />
							<input type="button" id="generate" value="Generate!" />
							<div id="ads_generate"></div>
						</div>
					</td>
				</tr>-->
				<tr>
					<td colspan="2" class="adsns_save_button">
						<input type="submit" class="button-primary" name="adsns_update" id="adsns_update" value="<?php _e( 'Save Changes', 'adsense' ) ?>" />
					</td>
				</tr>
			</table>
				<?php wp_nonce_field( plugin_basename(__FILE__), 'adsns_nonce_name' ); ?>
		</form>
		<div class="bws-plugin-reviews">
			<div class="bws-plugin-reviews-rate">
				<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'adsense' ); ?>: 
				<a href="http://wordpress.org/support/view/plugin-reviews/adsense-plugin" target="_blank" title="Google AdSense reviews"><?php _e( 'Rate the plugin', 'adsense' ); ?></a>
			</div>
			<div class="bws-plugin-reviews-support">
				<?php _e( 'If there is something wrong about it, please contact us', 'adsense' ); ?>: 
				<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
			</div>
		</div>
	<?php
	}

	/* Including scripts and stylesheets for admin interface of plugin */
	public function adsns_write_admin_head() {
		global $wp_version;
		if ( isset( $_GET['page'] ) && "adsense-plugin.php" == $_GET['page'] ) {
			if ( $wp_version < 3.8 )
				wp_enqueue_style( 'adsns_stylesheet', plugins_url( 'css/style_wp_before_3.8.css', __FILE__ ) );	
			else
				wp_enqueue_style( 'adsns_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'adsns_admin_script', plugins_url( 'js/admin.js' , __FILE__ ) );
			wp_enqueue_script( 'adsns_numeric_script', plugins_url( 'js/numeric.js' , __FILE__ ) );
			wp_enqueue_script( 'adsns_farbtastic_script', plugins_url( 'farbtastic/farbtastic.js' , __FILE__ ) );
			wp_enqueue_style( 'adsns_farbtastic_stylesheet', plugins_url( 'farbtastic/farbtastic.css' , __FILE__ ) ) ;
		}
	}

	/* Stylesheets for ads */
	function adsns_head() {
		echo <<<EOF
		<style type="text/css">
			.ads {
				position: relative;
				text-align: center;
				clear: both;
			}
		</style>
EOF;
	}

	/*
	*displays AdSense in widget
	*@return array()
	*/
	function adsns_widget_display() {
		global $wpmu, $adsns_count, $adsns_options;
		$title = $this->adsns_options['widget_title'];
		echo '<aside class="widget widget-container adsns_widget"><h1 class="widget-title">' . $title . '</h1>';
		if ( $adsns_count < $this->adsns_options['max_ads'] ) {
			$this->adsns_donate();
			echo '<div class="ads">' . $this->adsns_options['code'] . '</div>';
			$this->adsns_options['num_show']++;
			
			update_option( 'adsns_settings', $this->adsns_options );
			$adsns_count = $this->adsns_options['num_show'];
		}
		echo "</aside>";
	}

	/*
	*Register widget for use in sidebars.
	*Registers widget control callback for customizing options
	*/
	function adsns_register_widget() {
		wp_register_sidebar_widget(
			'adsns_widget', /* Unique widget id */
			'AdSense', /* Widget name */
			array( $this, 'adsns_widget_display' ), /* Callback function */
			array( 'description' => 'Widget displays AdSense' ) /* Options */
		);
		wp_register_widget_control(
			'adsns_widget', /* Unique widget id */
			'AdSense', /* Widget name */
			array( $this, 'adsns_widget_control' ) /* Callback function */
		);
	}

	/*
	*Registers widget control callback for customizing options
	*@return array
	*/
	function adsns_widget_control() {
		global $adsns_options;
		if ( isset( $_POST["adsns-widget-submit"] ) ) {
			$this->adsns_options['widget_title'] = strip_tags( stripslashes( $_POST["adsns-widget-title"] ) );
			update_option( 'adsns_settings', $this->adsns_options );
		}
		$title = $this->adsns_options['widget_title'];
		echo '<p><label for="adsns-widget-title">' . __( 'Title', 'adsns' ) . '<input class="widefat" id="adsns-widget-title" name="adsns-widget-title" type="text" value="' . $title . '" /></label></p>';
		echo '<input type="hidden" id="adsns-widget-submit" name="adsns-widget-submit" value="1" />';
	}
} /* Class */
?>