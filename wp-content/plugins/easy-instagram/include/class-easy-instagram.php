<?php
// [todo] - Video play option - default thickbox

if ( ! defined( 'ABSPATH' ) ) exit;

require 'class-easy-instagram-utils.php';
require 'class-easy-instagram-cache.php';

class Easy_Instagram {
	protected $defaults = array();
	protected $cache = null;

	protected $load_scripts_and_styles = false;

	public function __construct() {
		$this->cache = new Easy_Instagram_Cache();

		$this->defaults = array(
			'max_images' => 20,
			'caption_char_limit' => 100,
			'author_text' => __( 'by %s', 'Easy_Instagram' ),
			'thumb_click' => '',
			'time_text' => __( 'posted #T#', 'Easy_Instagram' ), // #T# will be replaced with the specified time_format
			'time_format' => __( '#R#', 'Easy_Instagram' ), // Relative time
			'thumb_size' => '', // Leave empty for default Instagram thumb size
			'min_thumb_size' => 10,
			'minimum_cache_expire_minutes' => 10,
			'template' => 'default',
			'ajax' => 'true'
		);

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'register_scripts_and_styles' ) );
		add_action( 'wp_footer', array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'wp_ajax_easy_instagram_content', array( $this, 'generate_content_ajax' ) );
		add_action( 'wp_ajax_nopriv_easy_instagram_content', array( $this, 'generate_content_ajax' ) );

		add_action( 'easy_instagram_clear_cache_event', array( $this, 'clear_cache_event_action' ) );

		add_shortcode( 'easy-instagram', array( $this, 'shortcode' ) );
	}

	public function init() {
		add_thickbox();
	}

	public function get_defaults() {
		return $this->defaults;
	}

	public function get_thumb_click_options() {
		return array(
			''			=> __( 'Do Nothing', 'Easy_Instagram' ),
			'thickbox'	=> __( 'Show in Thickbox', 'Easy_Instagram' ) ,
			'colorbox'	=> __( 'Show in Colorbox', 'Easy_Instagram' ),
			'original'	=> __( 'Show original in a new tab', 'Easy_Instagram' )
		);
	}

	//=========================================================================

	public function admin_menu() {
		add_options_page(
			__( 'Easy Instagram', 'Easy_Instagram' ),
			__( 'Easy Instagram', 'Easy_Instagram' ),
			'manage_options',
			'easy-instagram',
			array( $this, 'admin_settings_page' ) );
	}

	//=========================================================================
	public function admin_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'Easy_Instagram') );
		}

		$easy_instagram = $this;
		include 'views/admin-settings.php';
	}

	//=========================================================================

	function register_scripts_and_styles() {
		if ( ! is_admin() ) {
			wp_register_style( 'easy-instagram', plugins_url( 'assets/css/style.min.css', dirname( __FILE__ ) ) );
			wp_register_style( 'colorbox', plugins_url( 'assets/colorbox/colorbox.css', dirname( __FILE__ ) ) );
			wp_register_style( 'video-js', plugins_url( 'assets/video-js/video-js.css', dirname( __FILE__ ) ) );

			wp_register_script( 'colorbox', plugins_url( 'assets/colorbox/jquery.colorbox-min.js', dirname( __FILE__ ) ), array( 'jquery' ) );
			wp_register_script( 'video-js', plugins_url( 'assets/video-js/video.js', dirname( __FILE__ ) ), array( 'jquery' ) );
			wp_register_script( 'easy-instagram', plugins_url( 'assets/js/main.min.js', dirname( __FILE__ ) ), array( 'jquery', 'colorbox', 'video-js' ) );

			$after_ajax_content_load = get_option( 'easy_instagram_after_ajax_content_load' );
			wp_localize_script( 'easy-instagram', 'Easy_Instagram_Settings',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'after_ajax_content_load' => $after_ajax_content_load,
					'videojs_flash_swf_url' => plugins_url( 'assets/video-js/video-js.swf', dirname( __FILE__ ) )
				)
			);
		}
	}

	//=========================================================================

	public function plugin_action_links( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=easy-instagram' ) . '">'.__( 'Settings', 'Easy_Instagram' ).'</a>';
		return $links;
	}

	//=========================================================================

	public function enqueue_scripts_and_styles() {
		if ( true == $this->load_scripts_and_styles ) {
			wp_enqueue_style( 'easy-instagram' );
			wp_enqueue_style( 'colorbox' );
			wp_enqueue_style( 'video-js' );

			wp_enqueue_script( 'easy-instagram' );
			wp_enqueue_script( 'colorbox' );
			wp_enqueue_script( 'video-js' );
		}
	}

	//=========================================================================

	public function admin_init() {
		wp_register_style( 'Easy_Instagram_Admin', plugins_url( 'assets/css/admin.min.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'Easy_Instagram_Admin' );

		add_settings_section(
			'easy_instagram_general_settings',
			__( 'General Settings', 'Easy_Instagram' ),
			array( $this, 'easy_instagram_general_settings_callback'),
			'easy_instagram_general'
		);

		add_settings_section(
			'easy_instagram_account_section',
			__( 'Instagram Account', 'Easy_Instagram' ),
			array( $this, 'easy_instagram_account_callback' ),
			'easy_instagram_account'
		);

		add_settings_section(
			'easy_instagram_help_section',
			__( 'Help', 'Easy_Instagram' ),
			array( $this, 'easy_instagram_help_callback' ),
			'easy_instagram_help'
		);

			// Fields
		add_settings_field(
			'easy_instagram_client_id',
			__( 'Application Client ID', 'Easy_Instagram' ),
			array( $this, 'text_field_callback' ),
			'easy_instagram_general',
			'easy_instagram_general_settings',
			array(
				'field' => 'easy_instagram_client_id',
				'description' => __( 'This is the ID of your Instagram application', 'Easy_Instagram' )
			)
		);

		add_settings_field(
			'easy_instagram_client_secret',
			__( 'Application Client Secret', 'Easy_Instagram' ),
			array( $this, 'text_field_callback' ),
			'easy_instagram_general',
			'easy_instagram_general_settings',
			array(
				'field' => 'easy_instagram_client_secret',
				'description' => __( 'This is your Instagram application secret', 'Easy_Instagram' )
			)
		);

		add_settings_field(
			'easy_instagram_cache_expire_time',
			__( 'Cache Expire Time (minutes)', 'Easy_Instagram' ),
			array( $this, 'cache_time_callback' ),
			'easy_instagram_general',
			'easy_instagram_general_settings',
			array(
				'field' => 'easy_instagram_cache_expire_time',
				'description' =>  sprintf( __( 'Minimum expire time: %d minutes.', 'Easy_Instagram' ), $this->defaults['minimum_cache_expire_minutes'] )
			)
		);

		add_settings_field(
			'easy_instagram_cache_dir_option',
			__( 'Cache Directory', 'Easy_Instagram' ),
			array( $this, 'radio_field_callback'),
			'easy_instagram_general',
			'easy_instagram_general_settings',
			array(
				'field' => 'easy_instagram_cache_dir_option'
			)
		);


		add_settings_field(
			'easy_instagram_after_ajax_content_load',
			__( 'Extra JS to run after AJAX Easy Instagram content load', 'Easy_Instagram' ),
			array( $this, 'textarea_field_callback'),
			'easy_instagram_general',
			'easy_instagram_general_settings',
			array(
				'field' => 'easy_instagram_after_ajax_content_load',
				'description' => __( 'Deprecated. Use "jQuery(document).on(\'afterEasyInstagramLoad\', your_js_handler);" in your theme instead.', 'Easy_Instagram' )
				)
		);


		register_setting( 'easy_instagram_group', 'easy_instagram_client_id', array( $this, 'id_field_validate' ) );
		register_setting( 'easy_instagram_group', 'easy_instagram_client_secret', array( $this, 'secret_field_validate' ) );
		register_setting( 'easy_instagram_group', 'easy_instagram_cache_expire_time', array( $this, 'cache_expire_time_sanitize' ) );
		register_setting( 'easy_instagram_group', 'easy_instagram_cache_dir_option', array($this, 'cache_dir_option_validate' ) );
		register_setting( 'easy_instagram_group', 'easy_instagram_after_ajax_content_load', array( $this, 'text_field_validate' ) );

		global $pagenow;
		if ( 'options-general.php' == $pagenow ) {
			wp_register_script( 'Easy_Instagram_Admin', plugins_url( 'assets/js/admin.js', dirname( __FILE__ ) ) );
			wp_enqueue_script( 'Easy_Instagram_Admin' );
		}
	}
	//=========================================================================

	public function easy_instagram_general_settings_callback() {
	}

	//=========================================================================

	public function easy_instagram_account_callback() {
		_e( 'Instagram Account', 'Easy_Instagram' );
	}

	//=========================================================================

	public function easy_instagram_help_callback() {
		$this->print_help_page();
	}

	//=========================================================================
	public function text_field_callback( $args ) {
		$field = $args['field'];
		$value = get_option( $field );
		
		printf( '<input type="text" name="%s" id="%s" class="regular-text" value="%s" />', 
			esc_html( $field ), esc_html( $field ), esc_html( $value ) );

		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}

	//=========================================================================
	public function cache_time_callback( $args ) {
		$field = $args['field'];
		$value = get_option( $field, $this->defaults['minimum_cache_expire_minutes'] );
		
		printf( '<input type="text" name="%s" id="%s" class="regular-text" value="%s" />', 
			esc_html( $field ), esc_html( $field ), esc_html( $value ) );

		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}

	//=========================================================================
	public function radio_field_callback( $args ) {
		$field =  $args['field'];
		$value = get_option( $field, 'default' );
		
		printf( '<input type="radio" name="%s" id="%s-default" class="radio-button" value="default" %s />', 
			esc_html( $field ), esc_html( $field ), checked('default', $value, false ) );
		printf( '<label for="%s-default">%s</label><br>', esc_html( $field ), __( 'Default', 'Easy_Instagram' ) );

		printf( '<input type="radio" name="%s" id="%s-uploads" class="radio-button" value="uploads" %s />', 
			esc_html( $field ), esc_html( $field ), checked( 'uploads', $value, false ) );
		printf('<label for="%s-uploads">%s</label>', esc_html( $field ), __( 'Uploads', 'Easy_Instagram' ) );
	
		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}

	//=========================================================================
	public function textarea_field_callback( $args ) {
		$field = $args['field'];
		$value = get_option( $field );
		
		printf( '<textarea name="%s" id="%s" />%s</textarea>', 
			esc_html( $field ), esc_html( $field ), esc_html( $value ) );
		
		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}


	//=========================================================================
	public function cache_expire_time_sanitize( $field ) {
		return max(
			array( absint( $field ), $this->defaults['minimum_cache_expire_minutes'] ) );
	}


	//=========================================================================
	public function id_field_validate( $field ) {
		$output = get_option( $field );
		if ( !empty( $field ) ) {
			$output = trim( $field );
		} else {
			$message = __( 'Please enter your Instagram client id', 'Easy_Instagram' );
			add_settings_error( 'easy-instagram', 'empty-id', $message );
		}
		return $output;
	}


	//=========================================================================
	public function secret_field_validate( $field ) {
		$output = get_option( $field );
		if ( !empty( $field ) ) {
			$output = trim( $field );
		} else {
			$message = __( 'Please enter your Instagram client secret', 'Easy_Instagram' );
			add_settings_error( 'easy-instagram', 'empty-secret', $message );
		}
		return $output;
	}

	//=========================================================================

	public function get_cache_directory() {
		return get_option( 'easy_instagram_cache_dir_path', $this->cache->get_default_cache_path() );
	}

	/**
	 * Check if the default cache directory exists and is writable.
	 *
	 * @return string Cache directory type.
	 */
	private function validate_default_cache_directory() {
		$cache_directory = $this->cache->get_default_cache_path();
		
		if ( ! file_exists( $cache_directory ) || ! is_dir( $cache_directory ) ) {
			$error_message = sprintf( __( 'The cache directory [%s] does not exist.', 'Easy_Instagram' ), $cache_directory );
		}
		else if ( ! is_writable( $cache_directory ) ) {
			$error_message = sprintf( __( 'Cannot write to the cache directory [%s].', 'Easy_Instagram' ), $cache_directory );
		}
		if ( isset( $error_message ) ) {
			add_settings_error( 'easy-instagram', 'cache-directory-error', $error_message , 'error' );
		}
		
		$this->set_default_cache_settings();
		return 'default';
	}
	
	/**
	 * Check if the uploads cache directory exists, otherwise try to create it.
	 *
	 * @return string Cache directory type.
	 */
	private function validate_uploads_cache_directory() {
		$output = 'default';
		
		$upload_d = wp_upload_dir();
		
		if ( false === $upload_d['error'] ) {
			// Uploads directory found.
			$cache_directory_name = $this->cache->get_cache_directory_name();
			
			$cache_directory = sprintf( '%s/easy-instagram-%s', $upload_d['basedir'], $cache_directory_name );
			$cache_url = sprintf( '%s/easy-instagram-%s', $upload_d['baseurl'], $cache_directory_name );
			
			if ( ! file_exists( $cache_directory ) || ! is_dir( $cache_directory ) ) {
				// Try to create the cache directory.
				if ( wp_mkdir_p( $cache_directory ) ) {
					chmod( $cache_directory, 0777 );
				}
				else {
					$error_message = sprintf( __( 'Cannot create the cache directory [%s].', 'Easy_Instagram' ), $cache_directory );
				}
			}
			
			if ( is_dir( $cache_directory ) && ! is_writable( $cache_directory ) ) {
				$error_message = sprintf( __( 'Cannot write to the cache directory [%s].', 'Easy_Instagram' ), $cache_directory );
			}
		} 
		else {
			$error_message = $upload_d['error'];
		}
		
		if ( isset( $error_message ) ) {
			add_settings_error( 'easy-instagram', 'cache-directory-error', $error_message , 'error' );
			$this->set_default_cache_settings();
			$output = 'default';
		}
		else {
			update_option( 'easy_instagram_cache_dir_path', $cache_directory );
			update_option( 'easy_instagram_cache_dir_url', $cache_url );
			$output = 'uploads';
		}

		return $output;
	}

	/**
	 * Verify if the selected cache directory exists and it is writable.
	 *
	 * @param string Cache directory type, default or uploads.
	 * @return string Cache directory type.
	 */
	public function cache_dir_option_validate( $field ) {
		$default_cache_path = $this->cache->get_default_cache_path();
		$output = $field;
		
		switch ( $field ) {
			case 'default':
				$output = $this->validate_default_cache_directory();
				break;
				
			case 'uploads':
				$output = $this->validate_uploads_cache_directory();
				break;
				
			default:
				$output = 'default';
				break;
		}
		return $output;
	}
	
	//=========================================================================
	private function set_default_cache_settings() {
		update_option( 'easy_instagram_cache_dir_path', $this->cache->get_default_cache_path() );
		update_option( 'easy_instagram_cache_dir_url', $this->cache->get_default_cache_url() );
	}

	//=========================================================================
	public function text_field_validate( $field ) {
		$output = trim( $field );
		return $output;
		//return apply_filters( 'text_field_validate', $output, $field );
	}

	//=========================================================================

	public function get_instagram_settings() {
		$client_id = get_option( 'easy_instagram_client_id' );
		$client_secret = get_option( 'easy_instagram_client_secret' );

		$redirect_uri = admin_url( 'options-general.php?page=easy-instagram' );
		return array( $client_id, $client_secret, $redirect_uri );
	}

	//=========================================================================

	public function get_instagram_config() {
		list( $client_id, $client_secret, $redirect_uri ) = $this->get_instagram_settings();

		return array(
			'client_id' 	=> $client_id,
			'client_secret' => $client_secret,
			'grant_type' 	=> 'authorization_code',
			'redirect_uri' 	=> $redirect_uri
		);
	}

	//=========================================================================

	public function print_help_page() {
		$usage_file = trailingslashit( dirname( __FILE__ ) ) . '../usage.html';
		include( $usage_file );
	}

	//=========================================================================

	public function set_instagram_user_data( $username, $id ) {
		update_option( 'easy_instagram_username', $username );
		update_option( 'easy_instagram_user_id', $id );
	}

	//=========================================================================

	public function get_instagram_user_data() {
		$username = get_option( 'easy_instagram_username' );
		$user_id = get_option( 'easy_instagram_user_id' );
		return array( $username, $user_id );
	}

	//=========================================================================

	public function set_access_token( $access_token ) {
		update_option( 'easy_instagram_access_token', $access_token );
	}

	//=========================================================================

	public function get_access_token() {
		return get_option( 'easy_instagram_access_token' );
	}

	//=========================================================================

	private function get_live_data( $instagram, $endpoint, $endpoint_type, $limit = 1 ) {
		switch ( $endpoint_type ) {
			case 'user':
				$live_data = $instagram->getUserRecent( $endpoint );
				break;

			case 'tag':
				$live_data = $instagram->getRecentTags( $endpoint );
				break;

			default:
				$live_data = null;
				break;
		}

		if ( ! is_null( $live_data ) ) {
			$recent = json_decode( $live_data );
			if ( is_null( $recent ) ) {
				$live_data = null;
			}
			else {
				if ( $limit > $this->defaults['max_images'] ) {
					$limit = $this->defaults['max_images'];
				}

				if ( ! isset( $recent->data ) || ( is_null( $recent->data ) ) ) {
					$live_data = null;
				}

				$live_data = array_slice( $recent->data, 0, $limit );
			}
		}
		return $live_data;
	}

	//=========================================================================

	public function shortcode( $attributes ) {
		extract( shortcode_atts(
			array(
				'tag'					=> '',
				'user_id'				=> '',
				'limit'					=> 1,
				'caption_hashtags'		=> true,
				'caption_char_limit'	=> $this->defaults['caption_char_limit'],
				'author_text'			=> $this->defaults['author_text'],
				'author_full_name'		=> false,
				'thumb_click'			=> $this->defaults['thumb_click'],
				'time_text'				=> $this->defaults['time_text'],
				'time_format'			=> $this->defaults['time_format'],
				'thumb_size'			=> $this->defaults['thumb_size'],
				'template'				=> 'default',
				'ajax'                  => $this->defaults['ajax']
			), $attributes )
		);

		$params = array(
			'tag'				=> $tag,
			'user_id'			=> $user_id,
			'limit'				=> $limit,
			'caption_hashtags'	=> $caption_hashtags,
			'caption_char_limit'=> $caption_char_limit,
			'author_text'		=> $author_text,
			'author_full_name'	=> $author_full_name,
			'thumb_click'		=> $thumb_click,
			'time_text'			=> $time_text,
			'time_format'		=> $time_format,
			'thumb_size'		=> $thumb_size,
			'template'			=> $template,
			'ajax'              => $ajax
		);

		return $this->generate_content( $params );
	}

	//================================================================

	private function _get_data_for_user_or_tag( $instagram, $endpoint_id, $limit, $endpoint_type, &$error ) {
		if ( empty( $endpoint_id ) || empty( $endpoint_type ) ) {
			return null;
		}

		// Get cached data if available. Get live data if no cached data found.
		list( $data, $expired ) = $this->cache->get_cached_data_for_user_or_tag( $endpoint_id, $limit, $endpoint_type );

		$live_data = null;
		if ( $expired || ( is_null( $data ) ) ) {
			$live_data = $this->get_live_data( $instagram, $endpoint_id, $endpoint_type, $limit );
		}

		if ( empty( $live_data ) ) {
			if ( ! empty( $data ) ) {
				return $data['data'];
			}
			else {
				return null;
			}
		}

		// Cache live data
		try {
			$cache_data = $this->cache->cache_live_data( $live_data, $endpoint_id, $endpoint_type, $limit );
			if ( false === $cache_data ) {
				return null;
			}
		} catch( Exception $ex ) {
			$error = $ex->getMessage();
			return null;
		}

		return $cache_data['data'];
	}

	//================================================================

	public function get_thumb_size_from_params( $param_thumb_size ) {
		$thumb_size = trim( $param_thumb_size );
		
		$thumb_w = 0;
		$thumb_h = 0;

		if ( preg_match( '/^([0-9]+)(?:\s*)?x(?:\s*)?([0-9]+)$/', $thumb_size, $matches ) ) {
			$w = (int) $matches[1];
			$h = (int) $matches[2];
			if ( $w >= $this->defaults['min_thumb_size'] && $h >= $this->defaults['min_thumb_size'] ) {
				$thumb_w = $w;
				$thumb_h = $h;
			}
		}
		else {
			if ( preg_match( '/^([0-9]+)(?:\s*px)?$/', $thumb_size, $matches ) ) {
				$w = (int) $matches[1];
				if ( $w >= $this->defaults['min_thumb_size'] ) {
					$thumb_w = $thumb_h = $w;
				}
			}
		}
		return array( $thumb_w, $thumb_h );
	}

	//================================================================

	public function _get_render_elements_for_ajax( $args ) {
		extract( $args );
		$time_text = trim( $time_text );
		$time_format = trim( $time_format );
		$thumb_size = trim( $thumb_size );

		// Generate a unique id for the wrapper div
		$wrapper_id = 'eitw-' . md5( microtime() . rand( 1, 1000 ) );
		$loading_image = '<img src="' . plugins_url( 'assets/images/ajax-loader.gif', dirname( __FILE__ ) ) . '" alt="' .  __( 'Loading...', 'Easy_Instagram' ) . '" />';
		$content_loading_info = apply_filters( 'easy_instagram_content_loading_info', $loading_image );

		$out = '';

		$out .= '<div class="easy-instagram-container" id="' . $wrapper_id . '">';
		$out .= $content_loading_info;
		$out .= '<form action="" style="display:none;">';
		$out .= '<input type="hidden" name="action" value="easy_instagram_content" />';
		$out .= '<input type="hidden" name="tag" value="' . esc_attr( $tag ) .'" />';
		$out .= '<input type="hidden" name="user_id" value="' . esc_attr( $user_id ) .'" />';
		$out .= '<input type="hidden" name="limit" value="' . absint( $limit ) .'" />';
		$out .= '<input type="hidden" name="caption_hashtags" value="' . esc_attr( $caption_hashtags ) .'" />';
		$out .= '<input type="hidden" name="caption_char_limit" value="' . esc_attr( $caption_char_limit ) .'" />';
		$out .= '<input type="hidden" name="author_text" value="' . esc_attr( $author_text ) .'" />';
		$out .= '<input type="hidden" name="author_full_name" value="' . esc_attr( $author_full_name ) .'" />';
		$out .= '<input type="hidden" name="thumb_click" value="' . esc_attr( $thumb_click ) .'" />';
		$out .= '<input type="hidden" name="time_text" value="' . esc_attr( $time_text ) .'" />';
		$out .= '<input type="hidden" name="time_format" value="' . esc_attr( $time_format ) .'" />';
		$out .= '<input type="hidden" name="thumb_size" value="' . esc_attr( $thumb_size ) .'" />';
		$out .= '<input type="hidden" name="template" value="' . esc_attr( $template ) .'" />';
		$out .= '<input type="hidden" name="easy_instagram_content_security" value="' . wp_create_nonce( 'easy-instagram-content-nonce' ) .'" />';
		$out .= '</form>';
		$out .= '</div>';
		return $out;
	}

	//================================================================
	
	private function _get_render_elements_no_ajax( $args ) {
		extract( $args );
		
		$access_token = $this->get_access_token();
		if ( empty( $access_token ) ) {
			$rendered = __( 'Invalid access token. Please check your Instagram settings', 'Easy_Instagram' );
			return $rendered;
		}

		$config = $this->get_instagram_config();
		$instagram = new MC_Instagram_Connector( $config );
		$instagram->setAccessToken( $access_token );

		//Select which Instagram endpoint to use
		if ( ! empty( $user_id ) ) {
			$endpoint_id = $user_id;
			$endpoint_type = 'user';
		}
		else {
			if ( ! empty( $tag ) ) {
				$endpoint_id = $tag;
				$endpoint_type = 'tag';
			}
		}
		
		$error = '';
		$instagram_elements = $this->_get_data_for_user_or_tag( $instagram, $endpoint_id, $limit, $endpoint_type, $error );
		if ( is_null( $instagram_elements ) ) {
			$rendered = $error;
		}
		else {
			try {
				$rendered = $this->_get_render_elements( $instagram_elements, $args );
			} catch ( Exception $ex ) {
				$rendered = $ex->getMessage();
			}
		}
		return $rendered;
	}

	private function _get_render_elements( $instagram_elements, $args ) {
		$out = '';
		if ( empty( $instagram_elements ) ) {
			return $out;
		}
		
		extract( $args );
		
		$time_text = trim( $time_text );
		$time_format = trim( $time_format );

		list( $thumb_w, $thumb_h ) = $this->get_thumb_size_from_params( $thumb_size );

		$utils = new Easy_Instagram_Utils();

		$template_elements = array();

		$crt = 0;
		foreach ( $instagram_elements as $elem ) {
			$current_template_element = array();
			
			$large_image_url = $elem['standard_resolution']['url'];
			$normal_image_url = $elem['low_resolution']['url'];
			$thumbnail_url = $elem['thumbnail']['url'];
			$instagram_image_original_link = $elem['link'];
			$type = $elem['type'];
			$unique_rel = $elem['unique_rel'];
			
			$video_url = $video_width = $video_height = '';
			if ( isset( $elem['video_standard_resolution'] ) ) {
				if ( isset( $elem['video_standard_resolution']['url'] ) ) {
					$video_url = $elem['video_standard_resolution']['url'];
				}
				
				if ( isset( $elem['video_standard_resolution']['width'] ) ) {
					$video_width = $elem['video_standard_resolution']['width'];
				}
				
				if ( isset( $elem['video_standard_resolution']['height'] ) ) {
					$video_height = $elem['video_standard_resolution']['height'];
				}
			}
			
			$video_id = $elem['id'];
			$video_large_image = $elem['standard_resolution']['url'];
			
			if ( 'dynamic_thumbnail' == $thumb_size ) {
				$dynamic_thumb = 'dynamic_thumbnail';
			}
			
			if ( 'dynamic_normal' == $thumb_size ) {
				$dynamic_thumb = 'dynamic_normal';
			}
		
			if ( 'dynamic_large' == $thumb_size ) {
				$dynamic_thumb = 'dynamic_large';
			}

			if ( $thumb_w > 0 && $thumb_h > 0 ) {
				//TODO: generate new thumbnails
				$width = $thumb_w;
				$height = $thumb_h;
				$thumbnail_url = $this->cache->get_custom_thumbnail_url( $elem, $width, $height );
			}
			else {
				$width = $elem['thumbnail']['width'];
				$height = $elem['thumbnail']['height'];
			}

			if ( empty( $caption_hashtags ) ) {
				$caption_hashtags = false;
			}

			$caption_text = $utils->get_caption_text( $elem, $caption_hashtags, $caption_char_limit );
			$thickbox_caption_text = $utils->get_caption_text( $elem, false, 100 );

			$current_template_element['type'] = $type;
			$current_template_element['unique_rel'] = $unique_rel;
			$current_template_element['thumbnail_large_link_url'] = $large_image_url;
			$current_template_element['thumbnail_normal_link_url'] = $normal_image_url;
			
			$current_template_element['thumbnail_click'] = $thumb_click;

			switch ( $thumb_click ) {
				case 'thickbox':
				case 'colorbox':
					$current_template_element['thumbnail_link_title'] = $thickbox_caption_text;
					$current_template_element['thumbnail_link_url'] = $large_image_url;
					if ( 'video' == $type ) {
						$current_template_element['video_url'] = $video_url;
						$current_template_element['video_width'] = $video_width;
						$current_template_element['video_height'] = $video_height;
						$current_template_element['video_id'] = $video_id;
						$current_template_element['video_large_url'] = $video_large_image;
					}
					break;
				
				case 'original':
					$current_template_element['thumbnail_link_title'] = $caption_text;
					$current_template_element['thumbnail_link_url'] = $instagram_image_original_link;
					if ( 'video' == $type ) {
						$current_template_element['video_url'] = $video_url;
					}
					break;

				default:
					$current_template_element['thumbnail_link_title'] = '';
					$current_template_element['thumbnail_link_url'] = '';
					break;
			}

			$current_template_element['thumbnail_url'] = $thumbnail_url;
			$current_template_element['thumbnail_width'] = $width;
			$current_template_element['thumbnail_height'] = $height;
			if ( isset( $dynamic_thumb ) ) {
				$current_template_element['dynamic_thumb'] = $dynamic_thumb;
			}
			
			if ( empty( $elem['caption_from'] ) ) {
				$current_template_element['author'] = '';
			}
			else {
				// Make a link only from the user name, not all the 'published by' text
				if ( preg_match( '/^(.*)%s(.*)$/', $author_text, $matches ) ) {
					$published_by = $matches[1] . '<a href="http://instagram.com/' . $elem['user_name'] . '" target="_blank">';
					$published_by .= ( 'true' == $author_full_name ) ? $elem['caption_from'] : $elem['user_name'];
					$published_by .= '</a>';
					$published_by .= $matches[2];
				}
				else {
					$published_by = $author_text;
				}

				$current_template_element['author'] = $published_by;
			}

			if ( $caption_char_limit > 0 ) {
				$current_template_element['thumbnail_caption'] = $caption_text;
			}
			else {
				$current_template_element['thumbnail_caption'] = '';
			}

			if ( is_null( $elem['caption_created_time'] ) ) {
				$elem_time = $elem['created_time'];
			}
			else {
				$elem_time = max( array( $elem['caption_created_time'], $elem['created_time'] ) );
			}
			$current_template_element['created_at'] = $elem_time;
			
			if ( empty( $time_text ) ) {
				$current_template_element['created_at_formatted'] = '';
			}
			else {
				if ( preg_match( '/^(.*)#T#(.*)$/', $time_text, $matches ) ) {
					if ( '' != $time_format ) {
						if ( '#R#' == $time_format ) { // Relative
							$time_string = $utils->relative_time( $elem_time );
						}
						else {
							$time_string = strftime( $time_format, $elem_time );
						}
					}
					else {
						$time_string = '';
					}

					$time_string = $matches[1] . $time_string . $matches[2];
				}
				else {
					$time_string = $time_text; // No interpolation
				}
				
				$current_template_element['created_at_formatted'] = $time_string;
			}

			$template_elements[] = $current_template_element;

			$crt++;
			if ( $crt >= $limit ) {
				break;
			}
		}

		$out = $this->load_template( $template, $template_elements );

		return $out;
	}

	private function load_template( $template_name = 'default', $template_elements ) {
		$name = trim( $template_name );
		if ( preg_match( '/^(.*)\.php$/', $name, $matches ) ) {
			$name = $matches[1];
		}
		
		$theme_template = locate_template( sprintf( 'easy-instagram-%s.php', $name ) );
		if ( empty( $theme_template ) ) {
			// Load the template from plugin
			$template_path = sprintf( '%stemplates/%s.php', plugin_dir_path( dirname( __FILE__ ) ), $name );
		
			if ( ! file_exists( $template_path ) ) {
				throw new Exception( sprintf( __( 'Template file [%s.php] does not exist !', 'Easy_Instagram' ), $name ) );
			}
		}
		else {
			// Load theme's template
			$template_path = $theme_template;
		}
		
		$easy_instagram_elements = $template_elements;
		
		$ret = ob_start();
		
		include $template_path;
		
		$out = ob_get_clean();
		return $out;
	}

	public function generate_content( $params ) {
		$tag     = $params['tag'];
		$user_id = $params['user_id'];
		$ajax = isset( $params['ajax'] ) ? ( 'true' == $params['ajax'] ) : false;

		$this->load_scripts_and_styles = true;

		if ( empty( $tag ) && empty( $user_id ) ) {
			return '';
		}

		$access_token = $this->get_access_token();
		if ( empty( $access_token ) ) {
			return '';
		}
		
		if ( $ajax ) {
			return $this->_get_render_elements_for_ajax( $params );	
		}
		else {
			return $this->_get_render_elements_no_ajax( $params );
		}
	}

	//=========================================================================

	public function generate_content_ajax() {
		check_ajax_referer( 'easy-instagram-content-nonce', 'easy_instagram_content_security' );

		$error_reporting_status = error_reporting();
		// Disable error reporting. Protect against AJAX call completely failing.
		error_reporting( 0 );

		$tag     = $_GET['tag'];
		$user_id = $_GET['user_id'];
		$limit   = $_GET['limit'];

		$data = array( 'status' => 'ERROR' );

		if ( empty( $tag ) && empty( $user_id ) ) {
			echo json_encode( $data );
			error_reporting( $error_reporting_status );
			exit;
		}

		$access_token = $this->get_access_token();
		if ( empty( $access_token ) ) {
			$data['error'] = __( 'Invalid access token. Please check your Instagram settings', 'Easy_Instagram' );
			echo json_encode( $data );
			error_reporting( $error_reporting_status );
			exit;
		}

		$config = $this->get_instagram_config();
		$instagram = new MC_Instagram_Connector( $config );
		$instagram->setAccessToken( $access_token );

		//Select which Instagram endpoint to use
		if ( ! empty( $user_id ) ) {
			$endpoint_id = $user_id;
			$endpoint_type = 'user';
		}
		else {
			if ( ! empty( $tag ) ) {
				$endpoint_id = $tag;
				$endpoint_type = 'tag';
			}
		}

		$error = '';
		$instagram_elements = $this->_get_data_for_user_or_tag( $instagram, $endpoint_id, $limit, $endpoint_type, $error );
		if ( is_null( $instagram_elements ) ) {
			$data['error'] = $error;
		}
		else {
			try {
				$rendered = $this->_get_render_elements( $instagram_elements, $_GET );
				$data['status'] = 'SUCCESS';
				$data['output'] = $rendered;
			} catch ( Exception $ex ) {
				$data['error'] = $ex->getMessage();
			}
		}

		echo json_encode( $data );
		error_reporting( $error_reporting_status );
		exit;
	}

	//=====================================================================

	public function plugin_activation() {
		wp_schedule_event(
			time(),
			'daily',
			'easy_instagram_clear_cache_event' );
	}

	//=====================================================================

	public function debug_cron( $schedules ) {
		$schedules['every_five_minutes'] = array(
			'interval' => 300, // 5 min in seconds
			'display'  => __( 'Every Five Minutes', 'Easy_Instagram' ),
		);

		return $schedules;
	}

	//=====================================================================

	public function plugin_deactivation() {
		wp_clear_scheduled_hook( 'easy_instagram_clear_cache_event' );
	}

	//================================================================

	public function clear_cache_event_action() {
		$this->cache->clear_expired_cache_action();
	}
}
