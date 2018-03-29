<?php

class UM_Tracking {

	private $data;

	public function __construct() {

		$this->schedule_send();

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 10 );
	
	}

	/***
	***	@setup info array
	***/
	private function setup_data() {
		global $ultimatemember;

		$data = array();

		// Retrieve current theme info
		if ( get_bloginfo( 'version' ) < '3.4' ) {
			$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$theme      = $theme_data['Name'];
			$theme_ver  = $theme_data['Version'];
		} else {
			$theme_data = wp_get_theme();
			$theme      = $theme_data->Name;
			$theme_ver  = $theme_data->Version;
		}

		$data['url'] = home_url();
		$data['theme'] = $theme;
		$data['theme_version'] = $theme_ver;
		$data['wp_version'] = get_bloginfo( 'version' );
		$data['version'] = ultimatemember_version;

		// Retrieve current plugin information
		if( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins        = array_keys( get_plugins() );
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $key => $plugin ) {
			if ( in_array( $plugin, $active_plugins ) ) {
				// Remove active plugins from list so we can show active and inactive separately
				unset( $plugins[ $key ] );
			}
		}

		$data['active_plugins']   = $active_plugins;
		$data['inactive_plugins'] = $plugins;
		$data['language'] = get_bloginfo('language');
		$data['multisite'] = ( is_multisite() ) ? 1 : 0;
		
		if ( !get_option('__ultimatemember_sitekey') ) {
			$ultimatemember->setup->install_basics();
		}
		
		$data['email'] = get_option('admin_email');
		$data['unique_sitekey'] = get_option('__ultimatemember_sitekey');
		
		$this->data = $data;

	}

	/***
	***	@check if tracking is allowed
	***/
	private function tracking_allowed() {
		if ( !um_get_option('allow_tracking') )
			return 0;
		return 1;
	}
	
	/***
	***	@get last send time
	***/
	private function get_last_send() {
		return get_option( 'um_tracking_last_send' );
	}
	
	/***
	***	@send a report
	***/
	public function send_checkin( $override = false ) {
		
		if( ! $this->tracking_allowed() && ! $override )
			return;

		// Send a maximum of once per period
		$last_send = $this->get_last_send();
		if( $last_send && $last_send > strtotime( '-1 day' ) )
			return;
		
		$this->setup_data();
		
		if ( !get_option('__ultimatemember_coupon_sent') ) {
			$this->data['send_discount'] = 1;
		} else {
			$this->data['send_discount'] = 0;
		}
		
		$request = wp_remote_post( 'https://ultimatemember.com/?um_action=checkin', array(
			'method'      => 'POST',
			'timeout'     => 20,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'body'        => $this->data,
			'user-agent'  => 'UM/' . ultimatemember_version . '; ' . get_bloginfo( 'url' ),
		) );

		update_option( 'um_tracking_last_send', time() );
		update_option( '__ultimatemember_coupon_sent', 1 );
	}
	
	/***
	***	@run a scheduled report
	***/
	private function schedule_send() {
		add_action( 'um_daily_scheduled_events', array( $this, 'send_checkin' ) );
	}

	/***
	***	@show admin notices
	***/
	public function admin_notices() {

		if( ! current_user_can( 'manage_options' ) )
			return;
			
		$hide_notice = get_option('um_tracking_notice');
		
		if ( $hide_notice )
			return;

		$optin_url  =  esc_url( add_query_arg( 'um_adm_action', 'opt_into_tracking' ) );
		$optout_url =  esc_url( add_query_arg( 'um_adm_action', 'opt_out_of_tracking' ) );

		echo '<div class="updated um-admin-notice"><p>';
		
		echo __( 'Allow Ultimate Member to track plugin usage? Opt-in to tracking and our newsletter and we will immediately e-mail you a 20% discount which you can use to purchase our core extensions bundle. No sensitive data is tracked.', 'ultimate-member');
		
		echo '</p>';
		
		echo '<p><a href="' . esc_url( $optin_url ) . '" class="button button-primary">' . __( 'Allow tracking', 'ultimate-member') . '</a>';
		echo '&nbsp;<a href="' . esc_url( $optout_url ) . '" class="button-secondary">' . __( 'Do not allow tracking', 'ultimate-member') . '</a></p></div>';
		
	}

}