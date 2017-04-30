<?php
/*
Plugin Name: WPsite Follow Us Badges
plugin URI:	http://www.wpsite.net/social-media-follow-us-badges
Description: The WPsite Follow Us Badges showcases your Facebook, Twitter, Google+, LinkedIn, & Pinterest badges for instant likes, follows, and sharing of your website.
version: 1.1.5
Author: WPSITE.net
Author URI: http://wpsite.net
License: GPL2
*/

/**
 * Global Definitions
 */

/* Plugin Name */

if (!defined('WPSITE_FOLLOW_US_PLUGIN_NAME'))
    define('WPSITE_FOLLOW_US_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

/* Plugin directory */

if (!defined('WPSITE_FOLLOW_US_PLUGIN_DIR'))
    define('WPSITE_FOLLOW_US_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPSITE_FOLLOW_US_PLUGIN_NAME);

/* Plugin url */

if (!defined('WPSITE_FOLLOW_US_PLUGIN_URL'))
    define('WPSITE_FOLLOW_US_PLUGIN_URL', WP_PLUGIN_URL . '/' . WPSITE_FOLLOW_US_PLUGIN_NAME);

/* Plugin verison */

if (!defined('WPSITE_FOLLOW_US_VERSION_NUM'))
    define('WPSITE_FOLLOW_US_VERSION_NUM', '1.1.5');


/**
 * Activatation / Deactivation
 */

register_activation_hook( __FILE__, array('WPsiteFollowUs', 'register_activation'));
add_action('widgets_init', array('WPsiteFollowUs', 'wpsite_register_widget'));

/**
 * Hooks / Filter
 */

add_action('init', array('WPsiteFollowUs', 'load_textdoamin'));
add_action('admin_menu', array('WPsiteFollowUs', 'add_menu_page'));
add_action('wp_enqueue_scripts', array('WPsiteFollowUs', 'include_styles_scripts'));

/**
 * AJAX
 */

add_action('wp_ajax_wpsite_save_order', array('WPsiteFollowUs', 'save_order'));

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", array('WPsiteFollowUs', 'wpsite_follow_us_badges_settings_link'));

/**
 *  WPsiteFollowUs main class
 *
 * @since 1.0.0
 * @using Wordpress 3.8
 */

class WPsiteFollowUs extends WP_Widget {

	/* Properties */

	private static $text_domain = 'wpsite-follow-us-badges';

	private static $prefix = 'wpsite_follow_us_';

	private static $settings_page = 'wpsite-follow-us-badges-settings';

	private static $default = array(
		'order'		=> array('twitter', 'facebook', 'google', 'linkedin', 'pinterest'),
		'twitter'	=> array(
			'active'	=> false,
			'user'		=> 'WPsite',
			'args'		=> array(
				'link'						=> false,
				'followers_count_display' 	=> true,
				'language'					=> 'en',
				'width'						=> '100%',
				'alignment'					=> 'left',
				'show_screen_name'			=> false,
				'size'						=> 'medium'
				//'opt_out'					=> false
			)
		),
		'facebook'	=> array(
			'active'	=> false,
			'user'		=> 'WPsite',
			'args'		=> array(
				'link'					=> false,
				'width'					=> '',
				'language'				=> 'en_US',
				'layout'				=> 'standard',
				'action_type'			=> 'like',
				'colorscheme'			=> 'light',
				'show_friends_faces'	=> false,
				'include_share_button'	=> false
			)
		),
		'google'	=> array(
			'active'	=> false,
			'user'		=> '106771475441130344412',
			'args'		=> array(
				'link'			=> false,
				'size'			=> '20',
				'annotation'	=> 'bubble',
				'language'		=> 'en-US',
				'asynchronous' 	=> true,
				'parse_tags'	=> 'default'
			)
		),
		'linkedin'	=> array(
			'active'	=> false,
			'user'		=> '2839460',
			'args'		=> array(
				'link'			=> false,
				'type'			=> 'company',
				'count_mode'	=> 'right',
				'language'		=> 'en_US'
			)
		),
		'pinterest'	=> array(
			'active'	=> false,
			'user'		=> 'http://www.pinterest.com/wpsite/',
			'args'		=> array(
				'link'	=> false,
				'name'	=> 'WPsite'
			)
		)
	);

	private static $twitter_supported_languages = array(
		'en',
		'fr',
		'de',
		'it',
		'es',
		'ko',
		'ja'
	);

	private static $facebook_supported_languages = array(
		'af_ZA',
		'ar_AR',
		'az_AZ',
		'be_BY',
		'bg_BG',
		'bn_IN',
		'bs_BA',
		'ca_ES',
		'cs_CZ',
		'cx_PH',
		'cy_GB',
		'da_DK',
		'de_DE',
		'el_GR',
		'en_GB',
		'en_PI',
		'en_UD',
		'en_US',
		'eo_EO',
		'es_ES',
		'es_LA',
		'et_EE',
		'eu_ES',
		'fa_IR',
		'fb_LT',
		'fi_FI',
		'fo_FO',
		'fr_CA',
		'fr_FR',
		'fy_NL',
		'ga_IE',
		'gl_ES',
		'gn_PY',
		'he_IL',
		'hi_IN',
		'hr_HR',
		'hu_HU',
		'hy_AM',
		'id_ID',
		'is_IS',
		'it_IT',
		'ja_JP',
		'ka_GE',
		'km_KH',
		'ko_KR',
		'ku_TR',
		'la_VA',
		'lt_LT',
		'lv_LV',
		'mk_MK',
		'ml_IN',
		'ms_MY',
		'nb_NO',
		'ne_NP',
		'nl_NL',
		'nn_NO',
		'pa_IN',
		'pl_PL',
		'ps_AF',
		'pt_BR',
		'pt_PT',
		'ro_RO',
		'ru_RU',
		'sk_SK',
		'sl_SI',
		'sq_AL',
		'sr_RS',
		'sv_SE',
		'sw_KE',
		'ta_IN',
		'te_IN',
		'th_TH',
		'tl_PH',
		'tr_TR',
		'uk_UA',
		'ur_PK',
		'vi_VN',
		'zh_CN',
		'zh_HK',
		'zh_TW'
	);

	private static $google_supported_languages = array(
		'af',
		'am',
		'ar',
		'eu',
		'bn',
		'bg',
		'ca',
		'zh-HK',
		'zh-CN',
		'zh-TW',
		'hr',
		'cs',
		'da',
		'nl',
		'en-GB',
		'en-US',
		'et',
		'fil',
		'fi',
		'fr',
		'fr-CA',
		'gl',
		'de',
		'el',
		'gu',
		'iw',
		'hi',
		'hu',
		'is',
		'id',
		'id',
		'ja',
		'kn',
		'ko',
		'lv',
		'lt',
		'ms',
		'ml',
		'mr',
		'no',
		'fa',
		'pl',
		'pt-BR',
		'pt-PT',
		'ro',
		'ru',
		'sr',
		'sk',
		'sl',
		'es',
		'es-419',
		'sw',
		'sv',
		'ta',
		'te',
		'th',
		'tr',
		'uk',
		'ur',
		'vi',
		'zu'
	);

	private static $linkedin_supported_languages = array(
		'en_US',
		'fr_FR',
		'es_ES',
		'ru_RU',
		'de_DE',
		'it_IT',
		'pt_BR',
		'ro_RO',
		'tr_TR',
		'jp_JP',
		'in_ID',
		'ms_MY',
		'ko_KR',
		'sv_SE',
		'cs_CZ',
		'nl_NL',
		'pl_PL',
		'no_NO',
		'da_DK'
	);

	/**
	 * Hooks to 'register_activation_hook'
	 *
	 * @since 1.0.0
	 */
	static function register_activation() {

		/* Check if multisite, if so then save as site option */

		if (is_multisite()) {
			add_site_option('wpsite_follow_us_badges_version', WPSITE_FOLLOW_US_VERSION_NUM);
		} else {
			add_option('wpsite_follow_us_badges_version', WPSITE_FOLLOW_US_VERSION_NUM);
		}
	}

	/**
	 * Register the Widget
	 *
	 * @since 1.0.0
	 */
	static function wpsite_register_widget() {
	    register_widget( 'WPsiteFollowUs' );
	}

	/**
	 * Load the text domain
	 *
	 * @since 1.0.0
	 */
	static function load_textdoamin() {
		load_plugin_textdomain(self::$text_domain, false, WPSITE_FOLLOW_US_PLUGIN_DIR . '/languages');
	}

	/**
	 * Hooks to 'admin_menu'
	 *
	 * @since 1.0.0
	 */
	static function add_menu_page() {

		/* Cast the first sub menu to the top menu */

	    $settings_page_load = add_submenu_page(
	    	'options-general.php', 										// parent slug
	    	__('WPsite Follow Us', self::$text_domain), 				// Page title
	    	__('WPsite Follow Us', self::$text_domain), 				// Menu name
	    	'manage_options', 											// Capabilities
	    	self::$settings_page, 										// slug
	    	array('WPsiteFollowUs', 'wpsite_follow_us_admin_settings')	// Callback function
	    );
	    add_action("admin_print_scripts-$settings_page_load", array('WPsiteFollowUs', 'wpsite_follow_us_include_admin_scripts'));
	}

	/**
	 * Hooks to 'plugin_action_links_' filter
	 *
	 * @since 1.0.0
	 */
	static function wpsite_follow_us_badges_settings_link($links) {
		$settings_link = '<a href="options-general.php?page=' . self::$settings_page . '">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	/**
	 * Hooks to 'admin_print_scripts-$page'
	 *
	 * @since 1.0.0
	 */
	static function wpsite_follow_us_include_admin_scripts() {

		/* CSS */

		wp_register_style('wpsite_follow_us_admin_css', WPSITE_FOLLOW_US_PLUGIN_URL . '/css/wpsite_follow_us_admin.css');
		wp_enqueue_style('wpsite_follow_us_admin_css');

		/* Javascript */

		/*
wp_register_script('wpsite_follow_us_admin_js', WPSITE_FOLLOW_US_PLUGIN_URL . '/include/js/wpsite_follow_us_admin.js');
		wp_enqueue_script('wpsite_follow_us_admin_js');
*/
	}

	/**
	 * Displays the HTML for the 'general-admin-menu-settings' admin page
	 *
	 * @since 1.0.0
	 */
	static function wpsite_follow_us_admin_settings() {

		$settings = get_option('wpsite_follow_us_settings');

		/* Default values */

		if ($settings === false) {
			$settings = self::$default;
		}

		/* Save data nd check nonce */

		if (isset($_POST['submit']) && check_admin_referer('wpsite_follow_us_admin_settings')) {

			$settings = get_option('wpsite_follow_us_settings');

			/* Default values */

			if ($settings === false) {
				$settings = self::$default;
			}

			$settings = array(
				'order'		=> $settings['order'],
				'twitter'	=> array(
					'active'	=> isset($_POST['wpsite_follow_us_settings_twitter_active']) && $_POST['wpsite_follow_us_settings_twitter_active'] ? true : false,
					'user'		=> isset($_POST['wpsite_follow_us_settings_twitter_user']) ?stripcslashes(sanitize_text_field($_POST['wpsite_follow_us_settings_twitter_user'])) : '',
					'args'		=> array(
						'link' 	=> isset($_POST['wpsite_follow_us_settings_twitter_args_link']) && $_POST['wpsite_follow_us_settings_twitter_args_link'] ? true : false,
						'followers_count_display' 	=> isset($_POST['wpsite_follow_us_settings_twitter_args_followers_count_display']) && $_POST['wpsite_follow_us_settings_twitter_args_followers_count_display'] ? true : false,
						'language'					=> $_POST['wpsite_follow_us_settings_twitter_args_language'],
						'width'						=> isset($_POST['wpsite_follow_us_settings_twitter_args_width']) ?stripcslashes(sanitize_text_field($_POST['wpsite_follow_us_settings_twitter_args_width'])) : '',
						'alignment'					=> $_POST['wpsite_follow_us_settings_twitter_args_alignment'],
						'show_screen_name'			=> isset($_POST['wpsite_follow_us_settings_twitter_args_show_screen_name']) && $_POST['wpsite_follow_us_settings_twitter_args_show_screen_name'] ? true : false,
						'size'						=> $_POST['wpsite_follow_us_settings_twitter_args_size']
						//'opt_out'					=> isset($_POST['wpsite_follow_us_settings_twitter_args_opt_out']) && $_POST['wpsite_follow_us_settings_twitter_args_opt_out'] ? true : false
					)
				),
				'facebook'	=> array(
					'active'	=> isset($_POST['wpsite_follow_us_settings_facebook_active']) && $_POST['wpsite_follow_us_settings_facebook_active'] ? true : false,
					'user'		=> isset($_POST['wpsite_follow_us_settings_facebook_user']) ?stripcslashes(sanitize_text_field($_POST['wpsite_follow_us_settings_facebook_user'])) : '',
					'args'		=> array(
						'link' 	=> isset($_POST['wpsite_follow_us_settings_facebook_args_link']) && $_POST['wpsite_follow_us_settings_facebook_args_link'] ? true : false,
						'width'					=> isset($_POST['wpsite_follow_us_settings_facebook_args_width']) ?stripcslashes(sanitize_text_field($_POST['wpsite_follow_us_settings_facebook_args_width'])) : '',
						'layout'				=> $_POST['wpsite_follow_us_settings_facebook_args_layout'],
						'language'				=> $_POST['wpsite_follow_us_settings_facebook_args_language'],
						'action_type'			=> $_POST['wpsite_follow_us_settings_facebook_args_action_type'],
						'colorscheme'			=> $_POST['wpsite_follow_us_settings_facebook_args_colorscheme'],
						'show_friends_faces'	=> isset($_POST['wpsite_follow_us_settings_facebook_args_show_friends_faces']) && $_POST['wpsite_follow_us_settings_facebook_args_show_friends_faces'] ? true : false,
						'include_share_button'	=> isset($_POST['wpsite_follow_us_settings_facebook_args_include_share_button']) && $_POST['wpsite_follow_us_settings_facebook_args_include_share_button'] ? true : false
					)
				),
				'google'	=> array(
					'active'	=> isset($_POST['wpsite_follow_us_settings_google_active']) && $_POST['wpsite_follow_us_settings_google_active'] ? true : false,
					'user'		=> isset($_POST['wpsite_follow_us_settings_google_user']) ?stripcslashes(sanitize_text_field($_POST['wpsite_follow_us_settings_google_user'])) : '',
					'args' 		=> array(
						'link' 	=> isset($_POST['wpsite_follow_us_settings_google_args_link']) && $_POST['wpsite_follow_us_settings_google_args_link'] ? true : false,
						'size'			=> $_POST['wpsite_follow_us_settings_google_args_size'],
						'annotation'	=> $_POST['wpsite_follow_us_settings_google_args_annotation'],
						'language'		=> $_POST['wpsite_follow_us_settings_google_args_language'],
						//'asynchronous' 	=> isset($_POST['wpsite_follow_us_settings_google_asynchronous']) && $_POST['wpsite_follow_us_settings_google_asynchronous'] ? true : false,
						//'parse_tags'	=> $_POST['wpsite_follow_us_settings_google_args_parse_tags']
					)
				),
				'linkedin'	=> array(
					'active'	=> isset($_POST['wpsite_follow_us_settings_linkedin_active']) && $_POST['wpsite_follow_us_settings_linkedin_active'] ? true : false,
					'user'		=> isset($_POST['wpsite_follow_us_settings_linkedin_user']) ?stripcslashes(sanitize_text_field($_POST['wpsite_follow_us_settings_linkedin_user'])) : '',
					'args'		=> array(
						'link' 	=> isset($_POST['wpsite_follow_us_settings_linkedin_args_link']) && $_POST['wpsite_follow_us_settings_linkedin_args_link'] ? true : false,
						'type'			=> $_POST['wpsite_follow_us_settings_linkedin_args_type'],
						'count_mode'	=> $_POST['wpsite_follow_us_settings_linkedin_args_count_mode'],
						'language'		=> $_POST['wpsite_follow_us_settings_linkedin_args_language'],
					)
				),
				'pinterest'	=> array(
					'active'	=> isset($_POST['wpsite_follow_us_settings_pinterest_active']) && $_POST['wpsite_follow_us_settings_pinterest_active'] ? true : false,
					'user'		=> isset($_POST['wpsite_follow_us_settings_pinterest_user']) ?stripcslashes(sanitize_text_field($_POST['wpsite_follow_us_settings_pinterest_user'])) : '',
					'args'		=> array(
						'link' 	=> isset($_POST['wpsite_follow_us_settings_pinterest_args_link']) && $_POST['wpsite_follow_us_settings_pinterest_args_link'] ? true : false,
						'name'	=> isset($_POST['wpsite_follow_us_settings_pinterest_args_name']) ?stripcslashes(sanitize_text_field($_POST['wpsite_follow_us_settings_pinterest_args_name'])) : '',
					)
				)
			);

			update_option('wpsite_follow_us_settings', $settings);
		}

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-sortable');
		?>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$( "#tabs" ).tabs();


			$("#sortable").sortable({
				revert: true,
				update: function (event, ui) {

			        var data = {
						action: 'wpsite_save_order',
						order: $(this).sortable('toArray')
					};

			        // POST to server using $.post or $.ajax
			        $.post(ajaxurl, data, function(response) {});
			    }
			});
		});
		</script>

<?php
// Load the WPsite Follow Us plugin
 	require_once( 'admin/wpsite-follow-us-admin.php' );
 ?>

<?php
	}

	/**
	 * AJAX with action 'wpsite_save_order'
	 *
	 * @since 1.0.0
	 */
	static function save_order() {

		$settings = get_option('wpsite_follow_us_settings');

		/* Default values */

		if ($settings === false) {
			$settings = self::$default;
		}

		$settings['order'] = $_POST['order'];

		update_option('wpsite_follow_us_settings', $settings);

		die(); // this is required to return a proper result
	}

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'wpsite_follow_us_badges', // Base ID
			__('WPsite Follow Us Badges', self::$text_domain), // Name
			array( 'description' => __( 'Add follow buttons to your sidebar', self::$text_domain), ) // Args
		);
	}

	/**
	 * Hooks to 'wp_enqueue_scripts'
	 *
	 * @since 1.0.0
	 */
	static function include_styles_scripts() {

		/* CSS */

		wp_register_style('wpsite_follow_us_admin_css', WPSITE_FOLLOW_US_PLUGIN_URL . '/css/wpsite_follow_us_admin.css');
		wp_enqueue_style('wpsite_follow_us_admin_css');
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		$settings = get_option('wpsite_follow_us_settings');

		/* Default values */

		if ($settings === false) {
			$settings = self::$default;
		}

		echo $args['before_widget'];

		if (!empty( $title ))
			echo $args['before_title'] . $title . $args['after_title'];

		$content = '';

		foreach ($settings['order'] as $order) {

			// Style credit to http://www.flashuser.net/css3-social-media-buttons

			// Twitter

			if ($order == 'twitter') {
				if (isset($settings['twitter']['active']) && $settings['twitter']['active']) {

					if (isset($settings['twitter']['args']['link']) && $settings['twitter']['args']['link']) {
						$content .= '<div class="wpsite_follow_us_div_link"><a class="twitter" href="https://twitter.com/' . $settings['twitter']['user'] . '" target="_blank">Twitter</a></div>';
					} else {
						$content .= '<div class="wpsite_follow_us_div twitterbox"><a href="https://twitter.com/' . $settings['twitter']['user'] . '" class="twitter-follow-button"';

						if (isset($settings['twitter']['args']['followers_count_display']) && $settings['twitter']['args']['followers_count_display']) {
							$content .=  ' data-show-count="true"';
						} else {
							$content .=  ' data-show-count="false"';
						}

						if (isset($settings['twitter']['args']['opt_out']) && $settings['twitter']['args']['opt_out']) {
							$content .= ' data-dnt="true"';
						} else {
							$content .= ' data-dnt="false"';
						}

						if (isset($settings['twitter']['args']['show_screen_name']) && $settings['twitter']['args']['show_screen_name']) {
							$content .= ' data-show-screen-name="true"';
						} else {
							$content .= ' data-show-screen-name="false"';
						}

						if (isset($settings['twitter']['args']['size'])) {
							$content .= ' data-size="' . $settings['twitter']['args']['size'] .'"';
						}

						if (isset($settings['twitter']['args']['language'])) {
							$content .= ' data-lang="' . $settings['twitter']['args']['language'] .'"';
						}

						if (isset($settings['twitter']['args']['alignment'])) {
							$content .= ' data-align="' . $settings['twitter']['args']['alignment'] .'"';
						}

						if (isset($settings['twitter']['args']['width']) && $settings['twitter']['args']['width'] != '') {
							$content .= ' data-width="' . $settings['twitter']['args']['width'] .'"';
						}

						$content .= '></a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>
						';
					}
				}
			}

			// Facebook

			else if ($order == 'facebook') {
				if (isset($settings['facebook']['active']) && $settings['facebook']['active']) {

					if (isset($settings['facebook']['args']['link']) && $settings['facebook']['args']['link']) {
						$content .= '<div class="wpsite_follow_us_div_link"><a class="facebook" href="https://facebook.com/' . $settings['facebook']['user'] . '" target="_blank">Facebook</a></div>';
					} else {
						$content .= '<div class="wpsite_follow_us_div facebookbox"><div class="fb-like" data-href="https://facebook.com/' . $settings['facebook']['user'] . '"';

						if (isset($settings['facebook']['args']['include_share_button']) && $settings['facebook']['args']['include_share_button']) {
							$content .= ' data-share="true"';
						} else {
							$content .= ' data-share="false"';
						}

						if (isset($settings['facebook']['args']['show_friends_faces']) && $settings['facebook']['args']['show_friends_faces']) {
							$content .= ' data-show-faces="true"';
						} else {
							$content .= ' data-show-faces="false"';
						}

						if (isset($settings['facebook']['args']['layout'])) {
							$content .= ' data-layout="' . $settings['facebook']['args']['layout'] .'"';
						}

						if (isset($settings['facebook']['args']['action_type'])) {
							$content .= ' data-action="' . $settings['facebook']['args']['action_type'] .'"';
						}

						if (isset($settings['facebook']['args']['colorscheme'])) {
							$content .= ' data-colorscheme="' . $settings['facebook']['args']['colorscheme'] .'"';
						}

						if (isset($settings['facebook']['args']['width']) && $settings['facebook']['args']['width'] != '') {
							$content .= ' data-width="' . $settings['facebook']['args']['width'] .'"';
						}

						$content .= '></div>
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/';

						if (isset($settings['facebook']['args']['language'])) {
							$content .= $settings['facebook']['args']['language'];
						}

						$content .= '/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, "script", "facebook-jssdk"));</script></div>
						';
					}
				}
			}

			// Google+

			else if ($order == 'google') {
				if (isset($settings['google']['active']) && $settings['google']['active']) {

					if (isset($settings['google']['args']['link']) && $settings['google']['args']['link']) {
						$content .= '<div class="wpsite_follow_us_div_link"><a class="google" href="//plus.google.com/' . $settings['google']['user'] . '" target="_blank">Google+</a></div>';
					} else {
						$content .= '<div class="wpsite_follow_us_div googlebox"><div class="g-follow" data-href="//plus.google.com/' . $settings['google']['user'] . '" data-rel="publisher"';

						if (isset($settings['google']['args']['annotation'])) {
							$content .= ' data-annotation="' . $settings['google']['args']['annotation'] .'"';
						}

						if (isset($settings['google']['args']['size'])) {
							$content .= ' data-height="' . $settings['google']['args']['size'] .'"';
						}

						$content .= '></div><!-- Place this tag after the last widget tag. -->
							<script type="text/javascript">';

						if (isset($settings['google']['args']['language'])) {
							$content .= 'window.___gcfg = {lang: "' . $settings['google']['args']['language'] . '"};';
						}

						$content .= '(function() {
							    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
							    po.src = "https://apis.google.com/js/platform.js";
							    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
							  })();
							</script></div>';
					}
				}
			}

			// LinkedIn

			else if ($order == 'linkedin') {
				if (isset($settings['linkedin']['active']) && $settings['linkedin']['active']) {

					if (isset($settings['linkedin']['args']['link']) && $settings['linkedin']['args']['link']) {

						if (isset($settings['linkedin']['args']['type']) && $settings['linkedin']['args']['type'] == 'company') {
							$content .= '<div class="wpsite_follow_us_div_link"><a class="linkedin" href="https://www.linkedin.com/company/' . $settings['linkedin']['user'] . '" target="_blank">LinkedIn</a></div>';
						}else {
							$content .= '<div class="wpsite_follow_us_div_link"><a class="linkedin" href="https://www.linkedin.com/profile/view?id=' . $settings['linkedin']['user'] . '" target="_blank">LinkedIn</a></div>';
						}

					} else {
						$content .= '<div class="wpsite_follow_us_div linkedinbox"><script src="//platform.linkedin.com/in.js" type="text/javascript">';

						if (isset($settings['linkedin']['args']['language'])) {
							$content .= 'lang: ' . $settings['linkedin']['args']['language'];
						}

						$content .= '</script>
								<script type="IN/FollowCompany" data-id="' . $settings['linkedin']['user'] . '"';

						if (isset($settings['linkedin']['args']['count_mode'])) {
							$content .= ' data-counter="' . $settings['linkedin']['args']['count_mode'] .'"';
						}

						$content .= '></script></div>';
					}
				}
			}

			// Pinterest

			else if ($order == 'pinterest') {
				if (isset($settings['pinterest']['active']) && $settings['pinterest']['active']) {

					if (isset($settings['pinterest']['args']['link']) && $settings['pinterest']['args']['link']) {
						$content .= '<div class="wpsite_follow_us_div_link"><a class="pinterest" href="' . $settings['pinterest']['user'] . '" target="_blank">Pinterest</a></div>';
					} else {
						$content .= '<div class="wpsite_follow_us_div pinterestbox"><a data-pin-do="buttonFollow" href="' . $settings['pinterest']['user'] . '" >';

						if (isset($settings['pinterest']['args']['name'])) {
							$content .= $settings['pinterest']['args']['name'];
						}

						$content .= '</a><!-- Please call pinit.js only once per page --><script type="text/javascript" async src="//assets.pinterest.com/js/pinit.js"></script></div>';
					}
				}
			}
		}

		echo $content;

		echo $args['after_widget'];

		/* CSS */

		wp_register_style('wpsite_follow_us_badges_widget_css', WPSITE_FOLLOW_US_PLUGIN_URL . '/css/widget_output.css');
		wp_enqueue_style('wpsite_follow_us_badges_widget_css');
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		// Title

		if (isset( $instance[ 'title' ]))
			$title = $instance[ 'title' ];
		else
			$title = __('Follow Us', self::$text_domain);

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}
