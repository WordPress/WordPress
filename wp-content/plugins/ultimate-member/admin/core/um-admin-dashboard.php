<?php

class UM_Admin_Dashboard {

	function __construct() {
		
		$this->slug = 'ultimatemember';
		
		$this->about_tabs['about'] = 'About';
		$this->about_tabs['start'] = 'Getting Started';

		add_action('admin_menu', array(&$this, 'primary_admin_menu'), 0);
		add_action('admin_menu', array(&$this, 'secondary_menu_items'), 1000);
		add_action('admin_menu', array(&$this, 'extension_menu'), 9999); 
		
		add_action( 'admin_head', array( $this, 'menu_order_count' ) );
		
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1000 );
		
		add_action( 'wp_ajax_ultimatemember_rated', array( $this, 'ultimatemember_rated' ) );
		add_action( 'wp_ajax_nopriv_ultimatemember_rated', array( $this, 'ultimatemember_rated' ) );
		
	}
	
	/**
	 * Change the admin footer text on UM admin pages
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
		
		// Add the dashboard pages
		$um_pages[] = 'toplevel_page_ultimatemember';
		$um_pages[] = 'admin_page_ultimatemember-about';
		$um_pages[] = 'ultimate-member_page_um_options';
		$um_pages[] = 'edit-um_form';
		$um_pages[] = 'edit-um_role';
		$um_pages[] = 'edit-um_directory';
		$um_pages[] = 'ultimate-member_page_ultimatemember-extensions';
		
		if ( isset( $current_screen->id ) && in_array( $current_screen->id, $um_pages ) ) {
			// Change the footer text
			if ( ! get_option( 'um_admin_footer_text_rated' ) ) {
				
			$footer_text = sprintf( __( 'If you like Ultimate Member please consider leaving a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s review. It will help us to grow the plugin and make it more popular. Thank you.', 'ultimate-member'), '<a href="https://wordpress.org/support/plugin/ultimate-member/reviews/?filter=5" target="_blank" class="um-admin-rating-link" data-rated="' . __( 'Thanks :)', 'ultimate-member') . '">', '</a>' );
			
			$footer_text .= "<script type='text/javascript'>
					jQuery('a.um-admin-rating-link').click(function() {
						jQuery.post( '" . admin_url( 'admin-ajax.php', 'relative' ) . "', { action: 'ultimatemember_rated' } );
						jQuery(this).parent().text( jQuery(this).data( 'rated' ) );
					});
				</script>";
			}
		}

		return $footer_text;
	}
	
	/**
	 * When user clicks the review link in backend
	 */
	function ultimatemember_rated() {
		update_option('um_admin_footer_text_rated', 1 );
		die();
	}
	
	/**
	 * Get pending users (in queue)
	 */
	function get_pending_users_count() {
		
		if ( get_option('um_cached_users_queue') > 0 && ! isset( $_REQUEST['delete_count'] ) ) {
			return get_option('um_cached_users_queue');
		}
		
		$args = array( 'fields' => 'ID', 'number' => 100 );
		$args['meta_query']['relation'] = 'OR';
		$args['meta_query'][] = array(
				'key' => 'account_status',
				'value' => 'awaiting_email_confirmation',
				'compare' => '='
		);
		$args['meta_query'][] = array(
				'key' => 'account_status',
				'value' => 'awaiting_admin_review',
				'compare' => '='
		);
		$args = apply_filters('um_admin_pending_queue_filter', $args );
		$users = new WP_User_Query( $args );
		
		delete_option('um_cached_users_queue');
		add_option('um_cached_users_queue', count($users->results), '', 'no' );
		
		return count($users->results);
	}
	
	/**
	 * Manage order of admin menu items
	 */
	public function menu_order_count() {
		global $menu, $submenu;
		
		if ( !current_user_can( 'list_users' ) ) return;
		
		$count = $this->get_pending_users_count();
		if(is_array($menu)){
			foreach( $menu as $key => $menu_item ) {
				if ( 0 === strpos( $menu_item[0], _x( 'Users', 'Admin menu name' ) ) ) {
					$menu[ $key ][0] .= ' <span class="update-plugins count-'.$count.'"><span class="processing-count">'.$count.'</span></span>';
				}
			}
			
		}
		if(is_array($submenu)){
			foreach ( $submenu['users.php'] as $key => $menu_item ) {
				if ( 0 === strpos( $menu_item[0], _x( 'All Users', 'Admin menu name' ) ) ) {
					$submenu['users.php'][ $key ][0] .= ' <span class="update-plugins count-'.$count.'"><span class="processing-count">'.$count.'</span></span>';
				}
			}
		}
	}
	
	/***
	***	@setup admin menu
	***/
	function primary_admin_menu() {
		
		$this->pagehook = add_menu_page( __('Ultimate Member', $this->slug), __('Ultimate Member', $this->slug), 'manage_options', $this->slug, array(&$this, 'admin_page'), 'dashicons-admin-users', '42.78578');
		add_action('load-'.$this->pagehook, array(&$this, 'on_load_page'));
		
		add_submenu_page( $this->slug, __('Dashboard', $this->slug), __('Dashboard', $this->slug), 'manage_options', $this->slug, array(&$this, 'admin_page') );

		foreach( $this->about_tabs as $k => $tab ) {
			add_submenu_page( '_'. $k . '_um', sprintf(__('%s | Ultimate Member', $this->slug), $tab), sprintf(__('%s | Ultimate Member', $this->slug), $tab), 'manage_options', $this->slug . '-' . $k, array(&$this, 'admin_page') );
		}
		
	}

	/***
	***	@secondary admin menu (after settings)
	***/
	function secondary_menu_items() {

		add_submenu_page( $this->slug, __('Forms', $this->slug), __('Forms', $this->slug), 'manage_options', 'edit.php?post_type=um_form', '', '' );
		add_submenu_page( $this->slug, __('User Roles', $this->slug), __('User Roles', $this->slug), 'manage_options', 'edit.php?post_type=um_role', '', '' );

		if ( um_get_option('members_page' ) || !get_option('um_options') ){
			add_submenu_page( $this->slug, __('Member Directories', $this->slug), __('Member Directories', $this->slug), 'manage_options', 'edit.php?post_type=um_directory', '', '' );
		}
		
		do_action('um_extend_admin_menu');
	
	}
	
	/***
	***	@extension menu
	***/
	function extension_menu() {
		
		add_submenu_page( $this->slug, __('Extensions', $this->slug), '<span style="color: #00B9EB">' .__('Extensions', $this->slug) . '</span>', 'manage_options', $this->slug . '-extensions', array(&$this, 'admin_page') );
		
		remove_submenu_page('tools.php','redux-about');
		
	}
	
	/***
	***	@load metabox stuff
	***/
	function on_load_page() {
		global $ultimatemember;
		
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		/** custom metaboxes for dashboard defined here **/
		
		add_meta_box('um-metaboxes-contentbox-1', __('Users Overview','ultimate-member'), array(&$this, 'users_overview'), $this->pagehook, 'core', 'core');
		
		add_meta_box('um-metaboxes-mainbox-1', __('Latest from our blog','ultimate-member'), array(&$this, 'um_news'), $this->pagehook, 'normal', 'core');
		
		add_meta_box('um-metaboxes-sidebox-1', __('Purge Temp Files','ultimate-member'), array(&$this, 'purge_temp'), $this->pagehook, 'side', 'core');
		add_meta_box('um-metaboxes-sidebox-2', __('User Cache','ultimate-member'), array(&$this, 'user_cache'), $this->pagehook, 'side', 'core');
		
		if ( $this->language_avaialable_not_installed() ) {
			add_meta_box('um-metaboxes-sidebox-2', __('Language','ultimate-member'), array(&$this, 'dl_language'), $this->pagehook, 'side', 'core');
		} else if ( $this->language_avaialable_installed() ) {
			add_meta_box('um-metaboxes-sidebox-2', __('Language','ultimate-member'), array(&$this, 'up_language'), $this->pagehook, 'side', 'core');
		} else if ( $this->language_not_available() ) {
			add_meta_box('um-metaboxes-sidebox-2', __('Language','ultimate-member'), array(&$this, 'ct_language'), $this->pagehook, 'side', 'core');
		}
		
	}
	
	function up_language() {
		global $ultimatemember;
		$locale = get_option('WPLANG');
		include_once um_path . 'admin/templates/dashboard/language-update.php';
	}
	
	function dl_language() {
		global $ultimatemember;
		$locale = get_option('WPLANG');
		include_once um_path . 'admin/templates/dashboard/language-download.php';
	}
	
	function ct_language() {
		global $ultimatemember;
		$locale = get_option('WPLANG');
		include_once um_path . 'admin/templates/dashboard/language-contrib.php';
	}
	
	function um_news() {
		global $ultimatemember;
		include_once um_path . 'admin/templates/dashboard/feed.php';
	}
	
	function users_overview() {
		global $ultimatemember;
		include_once um_path . 'admin/templates/dashboard/users.php';
	}
	
	function purge_temp() {
		global $ultimatemember;
		include_once um_path . 'admin/templates/dashboard/purge.php';
	}
	
	function user_cache() {
		global $ultimatemember;
		include_once um_path . 'admin/templates/dashboard/cache.php';
	}
	
	/***
	***	@language not available
	***/
	function language_not_available() {
		$locale = get_option('WPLANG');
		if ( $locale && !strstr($locale, 'en_') && !isset( $ultimatemember->available_languages[$locale] ) && !file_exists( WP_LANG_DIR . '/plugins/ultimatemember-' . $locale . '.mo' ) )
			return true;
		return false;
	}
	
	/***
	***	@language available but not installed
	***/
	function language_avaialable_not_installed() {
		global $ultimatemember;
		$locale = get_option('WPLANG');
		if ( $locale && isset( $ultimatemember->available_languages[$locale] ) && !file_exists( WP_LANG_DIR . '/plugins/ultimatemember-' . $locale . '.mo' ) )
			return true;
		return false;
	}
	
	/***
	***	@language available and installed
	***/
	function language_avaialable_installed() {
		global $ultimatemember;
		$locale = get_option('WPLANG');
		if ( $locale && isset( $ultimatemember->available_languages[$locale] ) && file_exists( WP_LANG_DIR . '/plugins/ultimatemember-' . $locale . '.mo' ) )
			return true;
		return false;
	}
	
	/***
	***	@get a directory size
	***/
	function dir_size( $directory ) {
		global $ultimatemember;
		if ( $directory == 'temp' ) {
			$directory = $ultimatemember->files->upload_temp;
			$size = 0;
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
				$size+=$file->getSize();
			}
			return round ( $size / 1048576, 2);
		}
		return 0;
	}
	
	/***
	***	@which admin page to show?
	***/
	function admin_page() {
		
		$page = $_REQUEST['page'];
		if ( $page == 'ultimatemember' && ! isset( $_REQUEST['um-addon'] ) ) {

			?>
			
			<div id="um-metaboxes-general" class="wrap">
			
				<h2>Ultimate Member <sup><?php echo ultimatemember_version; ?></sup></h2>

					<?php wp_nonce_field('um-metaboxes-general'); ?>
					<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
					<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
					
					<input type="hidden" name="action" value="save_um_metaboxes_general" />
					
					<div id="dashboard-widgets-wrap">
			
						 <div id="dashboard-widgets" class="metabox-holder um-metabox-holder"> 

								<div id="postbox-container-1" class="postbox-container"><?php do_meta_boxes($this->pagehook,'core',null);  ?></div>
								<div id="postbox-container-2" class="postbox-container"><?php do_meta_boxes($this->pagehook,'normal',null); ?></div>
								<div id="postbox-container-3" class="postbox-container"><?php do_meta_boxes($this->pagehook,'side',null); ?></div>

						 </div>

					</div>

			</div><div class="um-admin-clear"></div>
			
			<div class="um-admin-dash-share"><?php global $reduxConfig; foreach ( $reduxConfig->args['share_icons'] as $k => $arr ) { ?><a href="<?php echo $arr['url']; ?>" class="um-about-icon um-admin-tipsy-n" title="<?php echo $arr['title']; ?>" target="_blank"><i class="<?php echo $arr['icon']; ?>"></i></a><?php } ?>	
			</div><div class="um-admin-clear"></div>
			
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ready( function($) {
					// postboxes setup
					postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
				});
				//]]>
			</script>
			
			<?php
			
		} else if ( $page == 'ultimatemember-extensions' ) {
			
			include_once um_path . 'admin/templates/extensions.php';
			
		} else if (  $page == 'ultimatemember-about' ) {

				include_once um_path . 'admin/templates/welcome/about.php';
		
		} else if (  $page == 'ultimatemember-start' ) {

				include_once um_path . 'admin/templates/welcome/start.php';
		
		} 
	
	}

}

$um_dashboard = new UM_Admin_Dashboard();