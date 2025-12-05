<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use Exception;
use WpMatomo\Capabilities;
use WpMatomo\Settings;
use WpMatomo\Site;
use WpMatomo\Site\Sync\SyncConfig as SiteConfigSync;
use WpMatomo\TrackingCode\GeneratorOptions;
use WpMatomo\TrackingCode\TrackingCodeGenerator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}
/**
 * TODO: maybe we can move the form data collection to a single class
 * Note: nonce verification exists, but phpcs can't tell since it's in a method
 * that calls other methods that then access post data.
 * phpcs:disable WordPress.Security.NonceVerification.Missing
 */
class TrackingSettings implements AdminSettingsInterface {
	const FORM_NAME                              = 'matomo';
	const NONCE_NAME                             = 'matomo_settings';
	const TRACK_MODE_DEFAULT                     = 'default';
	const TRACK_MODE_DISABLED                    = 'disabled';
	const TRACK_MODE_MANUALLY                    = 'manually';
	const TRACK_MODE_TAGMANAGER                  = 'tagmanager';
	const NONCE_NAME_GENERATE_TRACKING_CODE_AJAX = 'matomo-tracking-settings-code';

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @param Settings $settings
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
		$this->add_hooks();
	}

	public function get_title() {
		return esc_html__( 'Tracking', 'matomo' );
	}

	private function update_if_submitted() {
		if ( $this->form_submitted() === true
			 && check_admin_referer( self::NONCE_NAME ) ) {
			$this->apply_settings();

			return true;
		}

		return false;
	}

	public function can_user_manage() {
		return current_user_can( Capabilities::KEY_SUPERUSER );
	}

	private function apply_settings() {
		$keys_to_keep = [
			'track_mode',
			'track_across',
			'track_across_alias',
			'track_crossdomain_linking',
			'track_feed',
			'track_feed_addcampaign',
			'track_feed_campaign',
			'track_heartbeat',
			'track_user_id',
			'track_datacfasync',
			'tagmanger_container_ids',
			'set_download_extensions',
			'set_download_classes',
			'set_link_classes',
			'track_admin',
			'limit_cookies_referral',
			'limit_cookies_session',
			'limit_cookies_visitor',
			'limit_cookies',
			'force_post',
			'disable_cookies',
			'cookie_consent',
			'add_download_extensions',
			'track_404',
			'track_search',
			'add_post_annotations',
			'track_content',
			'track_ecommerce',
			'track_noscript',
			'noscript_code',
			'track_codeposition',
			'tracking_code',
			'force_protocol',
			'track_js_endpoint',
			'track_jserrors',
			'track_api_endpoint',
			Settings::SITE_CURRENCY,
			Settings::USE_SESSION_VISITOR_ID_OPTION_NAME,
		];

		if ( matomo_has_tag_manager() ) {
			$keys_to_keep[] = 'tagmanger_container_ids';
		}

		$values = [];

		// default value in case no role/ post type is selected to make sure we unset it if no role /post type is selected
		$values['add_post_annotations']    = [];
		$values['tagmanger_container_ids'] = [];

		$valid_currencies = $this->get_supported_currencies();

		if ( ! empty( $_POST[ self::FORM_NAME ]['tracker_debug'] ) ) {
			$site_config_sync = new SiteConfigSync( $this->settings );
			switch ( $_POST[ self::FORM_NAME ]['tracker_debug'] ) {
				case 'always':
					$site_config_sync->set_config_value( 'Tracker', 'debug', 1 );
					$site_config_sync->set_config_value( 'Tracker', 'debug_on_demand', 0 );
					break;
				case 'on_demand':
					$site_config_sync->set_config_value( 'Tracker', 'debug', 0 );
					$site_config_sync->set_config_value( 'Tracker', 'debug_on_demand', 1 );
					break;
				default:
					$site_config_sync->set_config_value( 'Tracker', 'debug', 0 );
					$site_config_sync->set_config_value( 'Tracker', 'debug_on_demand', 0 );
			}
		}

		if ( empty( $_POST[ self::FORM_NAME ][ Settings::SITE_CURRENCY ] )
			 || ! array_key_exists( sanitize_text_field( wp_unslash( $_POST[ self::FORM_NAME ][ Settings::SITE_CURRENCY ] ) ), $valid_currencies ) ) {
			$_POST[ self::FORM_NAME ][ Settings::SITE_CURRENCY ] = 'USD';
		}

		if ( ! empty( $_POST[ self::FORM_NAME ]['track_mode'] ) ) {
			$track_mode = $this->get_track_mode();
			if ( self::TRACK_MODE_TAGMANAGER === $track_mode ) {
				// no noscript mode in this case
				$_POST[ self::FORM_NAME ]['track_noscript'] = '';
				$_POST[ self::FORM_NAME ]['noscript_code']  = '';
			} else {
				unset( $_POST['tagmanger_container_ids'] );
			}
			if ( $this->must_update_tracker() === true ) {
				// We want to keep the tracking code when user switches between disabled and manually or disabled to disabled.
				if ( ! empty( $_POST[ self::FORM_NAME ]['tracking_code'] ) ) {
					// don't process, this is a script
					// phpcs:disable WordPress.Security.ValidatedSanitizedInput
					$_POST[ self::FORM_NAME ]['tracking_code'] = stripslashes( $_POST[ self::FORM_NAME ]['tracking_code'] );
					// phpcs:enable WordPress.Security.ValidatedSanitizedInput
				} else {
					$_POST[ self::FORM_NAME ]['tracking_code'] = '';
				}
				if ( ! empty( $_POST[ self::FORM_NAME ]['noscript_code'] ) ) {
					// don't process, this is a script
					// phpcs:disable WordPress.Security.ValidatedSanitizedInput
					$_POST[ self::FORM_NAME ]['noscript_code'] = stripslashes( $_POST[ self::FORM_NAME ]['noscript_code'] );
					// phpcs:enable WordPress.Security.ValidatedSanitizedInput
				} else {
					$_POST[ self::FORM_NAME ]['noscript_code'] = '';
				}
			} else {
				$_POST[ self::FORM_NAME ]['noscript_code'] = '';
				$_POST[ self::FORM_NAME ]['tracking_code'] = '';
			}
		}
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput
		foreach ( $_POST[ self::FORM_NAME ] as $name => $value ) {
			if ( in_array( $name, $keys_to_keep, true ) ) {
				$values[ $name ] = $value;
			}
		}
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput
		$this->settings->apply_tracking_related_changes( $values );

		return true;
	}

	private function get_track_mode() {
		if ( ! empty( $_POST[ self::FORM_NAME ]['track_mode'] ) ) {
			return sanitize_text_field( wp_unslash( $_POST[ self::FORM_NAME ]['track_mode'] ) );
		}
		return '';
	}
	/**
	 * Reauires form to be posted
	 *
	 * @return bool
	 */
	private function must_update_tracker() {
		$track_mode         = $this->get_track_mode();
		$previus_track_mode = $this->settings->get_global_option( 'track_mode' );
		$must_update        = false;
		if ( self::TRACK_MODE_MANUALLY === $track_mode
			 || ( self::TRACK_MODE_DISABLED === $track_mode &&
				  in_array( $previus_track_mode, [ self::TRACK_MODE_DISABLED, self::TRACK_MODE_MANUALLY ], true ) ) ) {
			// We want to keep the tracking code when user switches between disabled and manually or disabled to disabled.
			$must_update = true;
		}

		return $must_update;
	}

	/**
	 * @return bool
	 */
	private function form_submitted() {
		return isset( $_POST ) && ! empty( $_POST[ self::FORM_NAME ] )
			   && is_admin()
			   && $this->can_user_manage();
	}

	/**
	 * @param string $field
	 *
	 * @return bool
	 */
	private function has_valid_html_comments( $field ) {
		$valid = true;
		if ( $this->form_submitted() === true ) {
			if ( $this->must_update_tracker() === true ) {
				if ( ! empty( $_POST[ self::FORM_NAME ][ $field ] ) ) {
					// phpcs:disable WordPress.Security.ValidatedSanitizedInput
					$valid = $this->validate_html_comments( $_POST[ self::FORM_NAME ][ $field ] );
					// phpcs:enable WordPress.Security.ValidatedSanitizedInput
				}
			}
		}

		return $valid;
	}

	/**
	 * @param string $html html content to validate
	 *
	 * @returns boolean
	 */
	public function validate_html_comments( $html ) {
		$opening = substr_count( $html, '<!--' );
		$closing = substr_count( $html, '-->' );

		return ( $opening === $closing );
	}

	public function show_settings() {
		$was_updated     = false;
		$settings_errors = [];
		if ( $this->has_valid_html_comments( 'tracking_code' ) !== true ) {
			$settings_errors[] = __( 'Settings have not been saved. There is an issue with the HTML comments in the field "Tracking code". Make sure all opened comments (<!--) are closed (-->) correctly.', 'matomo' );
		}
		if ( $this->has_valid_html_comments( 'noscript_code' ) !== true ) {
			$settings_errors[] = __( 'Settings have not been saved. There is an issue with the HTML comments in the field "Noscript code". Make sure all opened comments (<!--) are closed (-->) correctly.', 'matomo' );
		}
		if ( count( $settings_errors ) === 0 ) {
			$was_updated = $this->update_if_submitted();
		}

		$settings = $this->settings;

		$containers = $this->get_active_containers();

		$track_modes = [
			self::TRACK_MODE_DEFAULT    => [
				'name'     => esc_html__( 'Auto (recommended)', 'matomo' ),
				'disabled' => false,
			],
			self::TRACK_MODE_MANUALLY   => [
				'name'     => esc_html__( 'Manual', 'matomo' ),
				'disabled' => false,
			],
			self::TRACK_MODE_TAGMANAGER => [
				'name'     => esc_html__( 'Tag Manager', 'matomo' ),
				'disabled' => false,
			],
			self::TRACK_MODE_DISABLED   => [
				'name'     => esc_html__( 'Disabled', 'matomo' ),
				'disabled' => false,
			],
		];

		$matomo_track_mode_descriptions = [
			self::TRACK_MODE_DISABLED   => esc_html__( 'Matomo will not add the tracking code itself. Use this mode if you want to add the tracking code by hand to your template files or you want to use another plugin to add the tracking code.', 'matomo' ),
			self::TRACK_MODE_DEFAULT    => esc_html__( 'Matomo will automatically generate and embed the tracking code based on the Auto Tracking settings below.', 'matomo' ) . ' ' . esc_html__( 'This is the recommended mode for most users.', 'matomo' ),
			self::TRACK_MODE_MANUALLY   => sprintf(
				esc_html__( '%1$sDefine your own tracking JavaScript by hand below%2$s, and Matomo will embed it into your website.', 'matomo' ) . ( $settings->is_network_enabled() ? ' ' . esc_html__( 'Make sure to use the placeholder {ID} to add the Matomo site ID.', 'matomo' ) : '' ),
				'<a href="#manual-tracking-settings">',
				'</a>'
			),
			self::TRACK_MODE_TAGMANAGER => esc_html__( 'If you\'ve created containers in the Tag Manager, you can use this tracking mode to embed one or more of them into your website automatically.', 'matomo' ),
		];

		if ( empty( $containers ) ) {
			$track_modes[ self::TRACK_MODE_TAGMANAGER ]['disabled']         = true;
			$track_modes[ self::TRACK_MODE_TAGMANAGER ]['tooltip']          = esc_html__( 'No containers were found. Create one to be able to use the Tag Manager tracking mode.', 'matomo' );
			$matomo_track_mode_descriptions[ self::TRACK_MODE_TAGMANAGER ] .= ' ' . esc_html__( 'This mode is not selectable since no containers have been created in the Tag Manager.', 'matomo' );
		} else {
			$container_select = '<div style="margin-left:1.5em" class="tagmanager-container-select">'
				. '<label for="tagmanger_container_ids">' . esc_html__( 'Select which Tag Manager containers will be added to each page', 'matomo' ) . ':</label>';

			$selected_container_ids = $settings->get_global_option( 'tagmanger_container_ids' );
			foreach ( $containers as $container_id => $container_name ) {
				$container_select .= '<input type="checkbox" ' . ( isset( $selected_container_ids [ $container_id ] ) && $selected_container_ids [ $container_id ] ? 'checked="checked" ' : '' ) . 'value="1" name="matomo[tagmanger_container_ids][' . esc_attr( $container_id ) . ']" /> <strong>' . esc_html( $container_name ) . '</strong> (ID: ' . esc_html( $container_id ) . ')&nbsp; <br />';
			}

			$container_select .= '<a style="margin-top:.5em;display:inline-block;" href="' . esc_url( menu_page_url( \WpMatomo\Admin\Menu::SLUG_TAGMANAGER, false ) ) . '" rel="noreferrer noopener" target="_blank">Edit containers <span class="dashicons-before dashicons-external"></span></a>';
			$container_select .= '<p style="margin-top:1em"><span class="dashicons dashicons-info-outline"></span> ' . sprintf( esc_html__( 'For Matomo to track you will need to %1$sadd a Matomo Tag to the container%2$s. It otherwise won\'t track automatically.', 'matomo' ), '<a href="https://matomo.org/faq/tag-manager/how-do-i-track-pageviews-of-my-website-using-matomo-tag-manager/" target="_blank" rel="noreferrer noopener">', '</a>' ) . '</p>';
			$container_select .= '</div>';

			$matomo_track_mode_descriptions[ self::TRACK_MODE_TAGMANAGER ] .= $container_select;
		}

		$matomo_track_mode_descriptions[ self::TRACK_MODE_TAGMANAGER ] .= '<a id="tagmanager-read-more-link" style="display:inline-block" href="https://matomo.org/guide/tag-manager/getting-started-with-tag-manager/" target="_blank" rel="noreferrer noopener">' . esc_html__( 'Read our documentation on the Matomo Tag Manager to learn more.', 'matomo' ) . '</a>';

		// /var/www/html/test/wp-content/uploads/wp-statistics/GeoLite2-City.mmdb
		$site   = new Site();
		$idsite = $site->get_current_matomo_site_id();

		$matomo_currencies = $this->get_supported_currencies();

		$cookie_consent_modes = $this->get_cookie_consent_modes();

		$tracking_code_generator      = new TrackingCodeGenerator( $this->settings, new GeneratorOptions( $this->settings ) );
		$matomo_default_tracking_code = $tracking_code_generator->prepare_tracking_code( $idsite );

		$matomo_exclusion_settings_url = home_url( '/wp-admin/admin.php?page=matomo-settings&tab=exlusions' );

		include dirname( __FILE__ ) . '/views/tracking.php';
	}

	/**
	 * @return string[]
	 */
	private function get_cookie_consent_modes() {
		$modes = [];
		foreach ( CookieConsent::get_available_options() as $option => $description ) {
			$modes[ $option ] = $description;
		}

		return $modes;
	}

	private function get_supported_currencies() {
		$all        = include dirname( MATOMO_ANALYTICS_FILE ) . '/app/core/Intl/Data/Resources/currencies.php';
		$currencies = [];
		foreach ( $all as $key => $single ) {
			$currencies[ $key ] = $single[0] . ' ' . $single[1];
		}

		return $currencies;
	}

	public function get_active_containers() {
		// we don't use Matomo API here to avoid needing to bootstrap Matomo which is slow and could break things
		$containers = [];
		if ( matomo_has_tag_manager() ) {
			global $wpdb;
			$db_settings     = new \WpMatomo\Db\Settings();
			$container_table = $db_settings->prefix_table_name( 'tagmanager_container' );
			try {
				// phpcs:disable WordPress.DB
				$containers = $wpdb->get_results( sprintf( 'SELECT `idcontainer`, `name` FROM %s where `status` = "active"', $container_table ) );
				// phpcs:enable WordPress.DB
			} catch ( Exception $e ) {
				// table may not exist yet etc
				$containers = [];
			}
		}
		$by_id = [];
		foreach ( $containers as $container ) {
			$by_id[ $container->idcontainer ] = $container->name;
		}

		return $by_id;
	}

	private function add_hooks() {
		add_action(
			'admin_enqueue_scripts',
			function ( $page ) {
				if ( 'matomo-analytics_page_matomo-settings' !== $page ) {
					return;
				}

				wp_enqueue_script(
					'matomo-tracking-settings',
					plugins_url( '/assets/js/settings.js', MATOMO_ANALYTICS_FILE ),
					[ 'jquery' ],
					'1.0.1',
					true
				);

				wp_localize_script(
					'matomo-tracking-settings',
					'mtmTrackingSettingsAjax',
					[
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( self::NONCE_NAME_GENERATE_TRACKING_CODE_AJAX ),
					]
				);
			}
		);
	}

	public static function register_ajax() {
		add_action( 'wp_ajax_matomo_generate_tracking_code', [ self::class, 'generate_tracking_code' ] );
	}

	public static function generate_tracking_code() {
		check_ajax_referer( self::NONCE_NAME_GENERATE_TRACKING_CODE_AJAX );

		$blog_id = get_current_blog_id();
		$idsite  = Site::get_matomo_site_id( $blog_id );

		// phpcs complains if reading from $_POST is not done this way
		$overrides = [
			'track_datacfasync'         => isset( $_POST['track_datacfasync'] ) ? ( boolval( wp_unslash( $_POST['track_datacfasync'] ) ) ) : false,
			'track_content'             => isset( $_POST['track_content'] ) ? ( sanitize_text_field( wp_unslash( $_POST['track_content'] ) ) ) : '',
			'track_heartbeat'           => isset( $_POST['track_heartbeat'] ) ? ( intval( wp_unslash( $_POST['track_heartbeat'] ) ) ) : 0,
			'limit_cookies'             => isset( $_POST['limit_cookies'] ) ? ( boolval( wp_unslash( $_POST['limit_cookies'] ) ) ) : false,
			'limit_cookies_visitor'     => isset( $_POST['limit_cookies_visitor'] ) ? ( intval( wp_unslash( $_POST['limit_cookies_visitor'] ) ) ) : 0,
			'limit_cookies_session'     => isset( $_POST['limit_cookies_session'] ) ? ( intval( wp_unslash( $_POST['limit_cookies_session'] ) ) ) : 0,
			'limit_cookies_referral'    => isset( $_POST['limit_cookies_referral'] ) ? ( intval( wp_unslash( $_POST['limit_cookies_referral'] ) ) ) : 0,
			'cookie_consent'            => isset( $_POST['cookie_consent'] ) ? ( sanitize_text_field( wp_unslash( $_POST['cookie_consent'] ) ) ) : '',
			'force_post'                => isset( $_POST['force_post'] ) ? ( wp_unslash( boolval( $_POST['force_post'] ) ) ) : false,
			'track_across_alias'        => isset( $_POST['track_across_alias'] ) ? ( boolval( wp_unslash( $_POST['track_across_alias'] ) ) ) : false,
			'track_across'              => isset( $_POST['track_across'] ) ? ( boolval( wp_unslash( $_POST['track_across'] ) ) ) : false,
			'track_crossdomain_linking' => isset( $_POST['track_crossdomain_linking'] ) ? ( boolval( wp_unslash( $_POST['track_crossdomain_linking'] ) ) ) : false,
			'track_jserrors'            => isset( $_POST['track_jserrors'] ) ? ( boolval( wp_unslash( $_POST['track_jserrors'] ) ) ) : false,
			'disable_cookies'           => isset( $_POST['disable_cookies'] ) ? ( boolval( wp_unslash( $_POST['disable_cookies'] ) ) ) : false,
			'set_link_classes'          => isset( $_POST['set_link_classes'] ) ? ( sanitize_text_field( wp_unslash( $_POST['set_link_classes'] ) ) ) : '',
			'set_download_classes'      => isset( $_POST['set_download_classes'] ) ? ( sanitize_text_field( wp_unslash( $_POST['set_download_classes'] ) ) ) : '',
			'add_download_extensions'   => isset( $_POST['add_download_extensions'] ) ? ( sanitize_text_field( wp_unslash( $_POST['add_download_extensions'] ) ) ) : '',
			'track_api_endpoint'        => isset( $_POST['track_api_endpoint'] ) ? ( sanitize_text_field( wp_unslash( $_POST['track_api_endpoint'] ) ) ) : '',
			'force_protocol'            => isset( $_POST['force_protocol'] ) ? ( boolval( wp_unslash( $_POST['force_protocol'] ) ) ) : false,
			'track_js_endpoint'         => isset( $_POST['track_js_endpoint'] ) ? ( sanitize_text_field( wp_unslash( $_POST['track_js_endpoint'] ) ) ) : '',
			'set_download_extensions'   => isset( $_POST['set_download_extensions'] ) ? ( sanitize_text_field( wp_unslash( $_POST['set_download_extensions'] ) ) ) : '',
		];

		$generator     = new TrackingCodeGenerator( \WpMatomo::$settings, new GeneratorOptions( \WpMatomo::$settings, $overrides ) );
		$tracking_code = $generator->prepare_tracking_code( $idsite );

		wp_send_json( $tracking_code );
	}
}
