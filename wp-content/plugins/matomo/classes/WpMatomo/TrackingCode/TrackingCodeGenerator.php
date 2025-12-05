<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\TrackingCode;

use WP_Query;
use WpMatomo\Admin\CookieConsent;
use WpMatomo\Admin\TrackingSettings;
use WpMatomo\Logger;
use WpMatomo\Paths;
use WpMatomo\Settings;
use WpMatomo\Site;
// phpcs:ignore PHPCompatibility.UseDeclarations.NewUseConstFunction.Found
use function is_user_logged_in;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class TrackingCodeGenerator {
	const TRACKPAGEVIEW = "_paq.push(['trackPageView']);";
	const MTM_INIT      = 'var _mtm = _mtm || [];';

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @var GeneratorOptions
	 */
	private $options;

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct( Settings $settings, GeneratorOptions $options ) {
		$this->settings = $settings;
		$this->options  = $options;
		$this->logger   = new Logger();
	}

	public static function get_disable_cookies_partial() {
		// if ecommerce tracking is enabled, disableCookies can be added to _paq multiple times
		// (since ecommerce tracking methods can be called before the main tracking JS in some situations).
		// piwik.js complains if the initial _paq array has more than one of the same method, so
		// we only add it if it's not there to begin with.
		return 'if (!window._paq.find || !window._paq.find(function (m) { return m[0] === "disableCookies"; })) {
	window._paq.push(["disableCookies"]);
}';
	}

	public function register_hooks() {
		add_action( 'matomo_site_synced', [ $this, 'update_tracking_code' ], $prio = 10, $args = 0 );
		add_action( 'matomo_tracking_settings_changed', [ $this, 'update_tracking_code' ], $prio = 10, $args = 0 );
	}

	public function update_tracking_code( $force = false ) {
		if (
			$this->settings->is_current_tracking_code()
			&& $this->settings->get_option( 'tracking_code' )
			&& ! $force
		) {
			return false;
		}

		$track_mode = $this->settings->get_global_option( 'track_mode' );

		if ( ! $this->settings->is_tracking_enabled()
			 || TrackingSettings::TRACK_MODE_MANUALLY === $track_mode ) {
			return false;
		}

		$blod_id = get_current_blog_id();
		$idsite  = Site::get_matomo_site_id( $blod_id );

		if ( ! $idsite ) {
			$this->logger->log( 'Found no related idSite for blog ' . get_current_blog_id() );

			return false;
		}

		if ( TrackingSettings::TRACK_MODE_DEFAULT === $track_mode ) {
			$result = $this->prepare_tracking_code( $idsite );

			if ( ! $this->settings->get_global_option( 'track_noscript' ) ) {
				$result['noscript'] = '';
			}
		} elseif ( TrackingSettings::TRACK_MODE_TAGMANAGER === $track_mode && matomo_has_tag_manager() ) {
			$result = $this->prepare_tagmanger_code( $this->settings, $this->logger );
		} else {
			$result = [
				'script'   => '<!-- Matomo: no supported track_mode selected -->',
				'noscript' => '',
			];
		}

		if ( ! empty( $result['script'] ) ) {
			$this->settings->set_option( 'tracking_code', $result['script'] );
			$this->settings->set_option( 'noscript_code', $result['noscript'] );
		}

		$this->settings->set_option( Settings::OPTION_LAST_TRACKING_CODE_UPDATE, time() );
		$this->settings->save();

		return $result;
	}

	public function get_noscript_code() {
		$this->update_tracking_code();

		return $this->settings->get_noscript_tracking_code();
	}

	public function get_tracking_code() {
		$this->update_tracking_code();

		$tracking_code = $this->settings->get_js_tracking_code();

		if ( $this->settings->track_user_id_enabled() ) {
			$tracking_code = $this->apply_user_tracking( $tracking_code );
		}
		if ( $this->settings->track_404_enabled() && is_404() ) {
			$tracking_code = $this->apply_404_changes( $tracking_code );
		}
		if ( $this->settings->track_search_enabled() ) {
			$tracking_code = $this->apply_search_changes( $tracking_code );
		}

		return $tracking_code;
	}

	/**
	 * @param Settings $settings
	 * @param Logger   $logger
	 *
	 * @return array
	 */
	private function prepare_tagmanger_code( $settings, $logger ) {
		$logger->log( 'Apply tag manager code changes:' );

		$container_ids = $settings->get_global_option( 'tagmanger_container_ids' );

		$code = '<!-- Matomo Tag Manager -->';

		if ( ! empty( $container_ids ) && is_array( $container_ids ) ) {
			$paths      = new Paths();
			$upload_url = $paths->get_upload_base_url();

			foreach ( $container_ids as $container_id => $enabled ) {
				if ( $enabled
					 && ctype_alnum( $container_id )
					 && strlen( $container_id ) <= 16 ) {
					$container_url = $upload_url . '/container_' . rawurlencode( $container_id ) . '.js';

					$data_cf_async = '';
					if ( $settings->get_global_option( 'track_datacfasync' ) ) {
						$data_cf_async = 'data-cfasync="false"';
					}

					if ( $settings->get_global_option( 'force_protocol' ) === 'https' ) {
						$container_url = preg_replace( '(^http://)', 'https://', $container_url );
					}

					$code .= '
<script ' . $data_cf_async . '>
' . self::MTM_INIT . '
_mtm.push({\'mtm.startTime\': (new Date().getTime()), \'event\': \'mtm.Start\'});
var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0];
g.type=\'text/javascript\'; g.async=true; g.src="' . $container_url . '"; s.parentNode.insertBefore(g,s);
</script>';
				}
			}
		}

		$code .= '<!-- End Matomo Tag Manager -->';

		return [
			'script'   => $code,
			'noscript' => '',
		];
	}

	public function get_tracker_endpoint() {
		$paths = new Paths();

		if ( $this->options->get_track_api_endpoint() === 'restapi' ) {
			$tracker_endpoint = $paths->get_tracker_api_rest_api_endpoint();
		} else {
			$tracker_endpoint = $paths->get_tracker_api_url_in_matomo_dir();
		}

		if ( $this->options->get_force_protocol() === 'https' ) {
			$tracker_endpoint = preg_replace( '(^http://)', 'https://', $tracker_endpoint );
		} else {
			$tracker_endpoint = preg_replace( '(^https?://)', '//', $tracker_endpoint );
		}

		return $tracker_endpoint;
	}

	public function get_js_endpoint() {
		$paths = new Paths();
		if ( $this->options->get_track_js_endpoint() === 'restapi' ) {
			$js_endpoint = $paths->get_js_tracker_rest_api_endpoint();
		} elseif ( $this->options->get_track_js_endpoint() === 'plugin' ) {
			$js_endpoint = plugins_url( 'app/matomo.js', MATOMO_ANALYTICS_FILE );
		} else {
			$js_endpoint = $paths->get_js_tracker_url_in_matomo_dir();
		}

		if ( $this->options->get_force_protocol() === 'https' ) {
			$js_endpoint = preg_replace( '(^http://)', 'https://', $js_endpoint );
		} else {
			$js_endpoint = preg_replace( '(^https?://)', '//', $js_endpoint );
		}

		return $js_endpoint;
	}

	/**
	 * @param int|string $idsite
	 *
	 * @return array
	 */
	public function prepare_tracking_code( $idsite ) {
		$log_level = is_admin() ? Logger::LEVEL_DEBUG : Logger::LEVEL_INFO;

		$this->logger->log( 'Apply tracking code changes:', $log_level );

		$tracker_endpoint = $this->get_tracker_endpoint();
		$js_endpoint      = $this->get_js_endpoint();

		$options = [];

		if ( $this->options->get_set_download_extensions() ) {
			$options[] = "_paq.push(['setDownloadExtensions', " . wp_json_encode( $this->options->get_set_download_extensions() ) . ']);';
		}
		if ( $this->options->get_add_download_extensions() ) {
			$options[] = "_paq.push(['addDownloadExtensions', " . wp_json_encode( $this->options->get_add_download_extensions() ) . ']);';
		}
		if ( $this->options->get_set_download_classes() ) {
			$options[] = "_paq.push(['setDownloadClasses', " . wp_json_encode( $this->options->get_set_download_classes() ) . ']);';
		}
		if ( $this->options->get_set_link_classes() ) {
			$options[] = "_paq.push(['setLinkClasses', " . wp_json_encode( $this->options->get_set_link_classes() ) . ']);';
		}
		if ( $this->options->get_disable_cookies() ) {
			$options[] = self::get_disable_cookies_partial();
		}
		if ( $this->options->get_track_crossdomain_linking() ) {
			$options[] = "_paq.push(['enableCrossDomainLinking']);";
		}
		if ( $this->options->get_track_jserrors() ) {
			$options[] = "_paq.push(['enableJSErrorTracking']);";
		}

		$cookie_domain = $this->get_tracking_cookie_domain();
		if ( ! empty( $cookie_domain ) ) {
			$options[] = '_paq.push(["setCookieDomain", ' . wp_json_encode( $cookie_domain ) . ']);';
		}

		$track_across_alias = $this->options->get_track_across_alias();

		if ( $track_across_alias ) {
			// todo detect more hosts such as when using WPML etc
			$hosts = [ wp_parse_url( home_url(), PHP_URL_HOST ) ];
			$hosts = array_filter( $hosts );
			$hosts = array_map(
				function ( $host ) {
					return '*.' . $host;
				},
				$hosts
			);
			if ( ! empty( $hosts ) ) {
				$options[] = '_paq.push(["setDomains", ' . wp_json_encode( $hosts ) . ']);';
			}
		}
		if ( $this->options->get_force_post() ) {
			$options[] = "_paq.push(['setRequestMethod', 'POST']);";
		}

		$cookie_consent        = new CookieConsent();
		$cookie_consent_option = $cookie_consent->get_tracking_consent_option( $this->options->get_cookie_consent() );
		// for unit test cases
		if ( ! empty( $cookie_consent_option ) ) {
			$options[] = $cookie_consent_option;
		}

		if ( $this->options->get_limit_cookies() ) {
			$options[] = "_paq.push(['setVisitorCookieTimeout', " . wp_json_encode( $this->options->get_limit_cookies_visitor() ) . ']);';
			$options[] = "_paq.push(['setSessionCookieTimeout', " . wp_json_encode( $this->options->get_limit_cookies_session() ) . ']);';
			$options[] = "_paq.push(['setReferralCookieTimeout', " . wp_json_encode( $this->options->get_limit_cookies_referral() ) . ']);';
		}
		if ( $this->options->get_track_content() === 'all' ) {
			$options[] = "_paq.push(['trackAllContentImpressions']);";
		} elseif ( $this->options->get_track_content() === 'visible' ) {
			$options[] = "_paq.push(['trackVisibleContentImpressions']);";
		}
		if ( (int) $this->options->get_track_heartbeat() > 0 ) {
			$options[] = "_paq.push(['enableHeartBeatTimer', " . intval( $this->options->get_track_heartbeat() ) . ']);';
		}

		$data_cf_async        = '';
		$data_of_async_option = [];
		if ( $this->options->get_track_datacfasync() ) {
			$data_cf_async                        = 'data-cfasync="false"';
			$data_of_async_option['data-cfasync'] = 'false';
		}

		$script  = "var _paq = window._paq = window._paq || [];\n";
		$script .= implode( "\n", $options );
		$script .= self::TRACKPAGEVIEW;
		$script .= "_paq.push(['enableLinkTracking']);_paq.push(['alwaysUseSendBeacon']);";
		$script .= "_paq.push(['setTrackerUrl', " . wp_json_encode( $tracker_endpoint ) . ']);';
		$script .= "_paq.push(['setSiteId', '" . intval( $idsite ) . "']);";
		$script .= "var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
g.type='text/javascript'; g.async=true; g.src=" . wp_json_encode( $js_endpoint ) . '; s.parentNode.insertBefore(g,s);';

		$script = <<<EOF
(function () {
function initTracking() {
$script
}
if (document.prerendering) {
	document.addEventListener('prerenderingchange', initTracking, {once: true});
} else {
	initTracking();
}
})();
EOF;

		if ( function_exists( 'wp_get_inline_script_tag' ) ) {
			$script = wp_get_inline_script_tag(
				$script,
				$data_of_async_option
			);
		} else {
			/*
			 * method wp_get_inline_script_tag add a line feed.
			 * to get the unit tests pass, we add a line feed when not using the method
			 */
			$script = '<script ' . $data_cf_async . ">\n" . $script . "\n</script>\n";
		}

		$script = '<!-- Matomo -->' . $script . '<!-- End Matomo Code -->';

		$no_script = '<noscript><p><img referrerpolicy="no-referrer-when-downgrade" src="' . esc_url( $tracker_endpoint ) . '?idsite=' . intval( $idsite ) . '&amp;rec=1" style="border:0;" alt="" /></p></noscript>';

		$script = apply_filters( 'matomo_tracking_code_script', $script, $idsite );
		$script = apply_filters( 'matomo_tracking_code_noscript', $script, $idsite );

		$this->logger->log( 'Finished tracking code: ' . $script, $log_level );
		$this->logger->log( 'Finished noscript code: ' . $no_script, $log_level );

		return [
			'script'   => $script,
			'noscript' => $no_script,
		];
	}

	public function get_tracking_cookie_domain() {
		if ( $this->options->get_track_across()
			|| $this->options->get_track_crossdomain_linking() ) {
			$host = wp_parse_url( home_url(), PHP_URL_HOST );
			if ( ! empty( $host ) ) {
				return '*.' . $host;
			}
		}

		return '';
	}

	private function apply_404_changes( $tracking_code ) {
		$this->logger->log( 'Apply 404 tracking changes. Blog ID: ' . get_current_blog_id() );

		$code          = "_paq.push(['setDocumentTitle', '404/URL = '+String(document.location.pathname+document.location.search).replace(/\//g,'%2f') + '/From = ' + String(document.referrer).replace(/\//g,'%2f')]);";
		$tracking_code = str_replace( self::TRACKPAGEVIEW, $code . self::TRACKPAGEVIEW, $tracking_code );
		$tracking_code = str_replace( self::MTM_INIT, $code . self::MTM_INIT, $tracking_code );

		return $tracking_code;
	}

	private function apply_search_changes( $tracking_code ) {
		$this->logger->log( 'Apply search tracking changes. Blog ID: ' . get_current_blog_id() );
		$obj_search       = new WP_Query( 's=' . get_search_query() . '&showposts=-1' );
		$int_result_count = $obj_search->post_count;

		$code          = "window._paq = window._paq || []; window._paq.push(['trackSiteSearch','" . get_search_query() . "', false, " . $int_result_count . "]);\n";
		$tracking_code = str_replace( self::TRACKPAGEVIEW, $code . self::TRACKPAGEVIEW, $tracking_code );
		$tracking_code = str_replace( self::MTM_INIT, $code . self::MTM_INIT, $tracking_code );

		return $tracking_code;
	}

	private function apply_user_tracking( $tracking_code ) {
		$user_id_to_track = null;
		if ( is_user_logged_in() ) {
			// Get the User ID Admin option, and the current user's data
			$uid_from     = $this->settings->get_global_option( 'track_user_id' );
			$current_user = wp_get_current_user(); // current user
			// Get the user ID based on the admin setting
			if ( 'uid' === $uid_from ) {
				$user_id_to_track = $current_user->ID;
			} elseif ( 'email' === $uid_from ) {
				$user_id_to_track = $current_user->user_email;
			} elseif ( 'username' === $uid_from ) {
				$user_id_to_track = $current_user->user_login;
			} elseif ( 'displayname' === $uid_from ) {
				$user_id_to_track = $current_user->display_name;
			}
		}
		$user_id_to_track = apply_filters( 'matomo_tracking_user_id', $user_id_to_track );
		// Check we got a User ID to track, and track it
		if ( isset( $user_id_to_track ) && ! empty( $user_id_to_track ) ) {
			$code          = "window._paq = window._paq || []; window._paq.push(['setUserId', '" . esc_js( $user_id_to_track ) . "']);\n";
			$tracking_code = str_replace( self::TRACKPAGEVIEW, $code . self::TRACKPAGEVIEW, $tracking_code );
			$tracking_code = str_replace( self::MTM_INIT, $code . self::MTM_INIT, $tracking_code );
		}

		return $tracking_code;
	}
}
