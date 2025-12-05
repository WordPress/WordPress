<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Site;

use Exception;
use Piwik\Access;
use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\Intl\Data\Provider\CurrencyDataProvider;
use Piwik\Plugins\SitesManager;
use Piwik\Plugins\SitesManager\Model;
use WP_Site;
use WpMatomo\Bootstrap;
use WpMatomo\Installer;
use WpMatomo\Logger;
use WpMatomo\Settings;
use WpMatomo\Site;
use WpMatomo\Site\Sync\SyncConfig;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

/**
 * Properties coming from matomo
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */
class Sync {
	const MAX_LENGTH_SITE_NAME = 90;

	/**
	 * @var Logger
	 */
	private $logger;

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * @var SyncConfig
	 */
	private $config_sync;

	public function __construct( Settings $settings ) {
		$this->logger      = new Logger();
		$this->settings    = $settings;
		$this->config_sync = new SyncConfig( $settings );
	}

	public function register_hooks() {
		add_action( 'update_option_blogname', [ $this, 'sync_current_site_ignore_error' ] );
		add_action( 'update_option_home', [ $this, 'sync_current_site_ignore_error' ] );
		add_action( 'update_option_siteurl', [ $this, 'sync_current_site_ignore_error' ] );
		add_action( 'update_option_timezone_string', [ $this, 'sync_current_site_ignore_error' ] );
		add_action( 'matomo_setting_change_track_ecommerce', [ $this, 'sync_current_site_ignore_error' ] );
		add_action( 'matomo_setting_change_site_currency', [ $this, 'sync_current_site_ignore_error' ] );
	}

	public function sync_current_site_ignore_error() {
		if ( ! is_plugin_active( 'matomo/matomo.php' ) ) {
			// @see https://github.com/matomo-org/matomo-for-wordpress/issues/577
			return;
		}
		try {
			$this->sync_current_site();
		} catch ( Exception $e ) {
			$this->logger->log( 'Ignoring site sync error: ' . $e->getMessage() );
			$this->logger->log_exception( 'sync_site_ignore', $e );
		}
	}

	public function sync_all() {
		$succeed_all = true;

		Bootstrap::do_bootstrap();

		if ( is_multisite() && function_exists( 'get_sites' ) ) {
			foreach ( get_sites() as $site ) {
				if ( 1 === (int) $site->deleted ) {
					continue;
				}

				/** @var WP_Site $site */
				switch_to_blog( $site->blog_id );
				try {
					$installer = new Installer( $this->settings );
					if ( ! $installer->looks_like_it_is_installed() ) {
						$this->logger->log( sprintf( 'Matomo was not installed yet for blog: %s installing now.', $site->blog_id ) );

						// prevents error that it wouldn't fully install matomo for a different site as it would think it already did install it etc.
						// and would otherwise think plugins are already activated etc
						Bootstrap::set_not_bootstrapped();
						$config                        = Config::getInstance();
						$installed                     = $config->PluginsInstalled;
						$installed['PluginsInstalled'] = [];
						$config->PluginsInstalled      = $installed;

						if ( $installer->can_be_installed() ) {
							$installer->install();
						} else {
							continue;
						}
					}

					$success = $this->sync_site( $site->blog_id, $site->blogname, $site->siteurl );
				} catch ( Exception $e ) {
					$success = false;
					// we don't want to rethrow exception otherwise some other blogs might never sync
					$this->logger->log( 'Matomo error syncing site: ' . $e->getMessage() );
				}

				$succeed_all = $succeed_all && $success;
				restore_current_blog();
			}
		} else {
			$success     = $this->sync_current_site();
			$succeed_all = $succeed_all && $success;
		}

		return $succeed_all;
	}

	public function sync_current_site() {
		return $this->sync_site( get_current_blog_id(), get_bloginfo( 'name' ), get_bloginfo( 'url' ) );
	}

	public function sync_site( $blog_id, $blog_name, $blog_url ) {
		Bootstrap::do_bootstrap();
		$this->logger->log( 'Matomo is now syncing blogId ' . $blog_id );

		$idsite = Site::get_matomo_site_id( $blog_id );

		$sites_manager_model = new Model();

		if ( ! is_multisite() ) {
			// we should have here only one record in the matomo_site table then...
			$matomo_id_sites = $sites_manager_model->getSitesId();
			if ( count( $matomo_id_sites ) === 1 ) {
				$matomo_id_site = (int) $matomo_id_sites[0];
				if ( empty( $idsite ) ) {
					// we have one record in the matomo_site table but the mapping does not exist. Force usage of the ID found.
					$idsite = $matomo_id_site;
					$this->logger->log( "Can't find the id site in the mapping, but there is already an existing site. Use its ID " . $idsite . ' for blog' );
					wp_cache_flush();
					add_site_option( Site::SITE_MAPPING_PREFIX . $blog_id, $idsite );
				} else {
					if ( (int) $idsite !== $matomo_id_site ) {
						// the mapped id in the WP config is different from the id in the matomo_site table: we force usage of the matomo table site ID
						$idsite = $matomo_id_site;
						$this->logger->log( 'The id site in the mapping is different from the id site in the matomo table. Force usage of Matomo ID ' . $idsite . ' for blog' );
						Site::map_matomo_site_id( $blog_id, $idsite );
					}
				}
			} else {
				if ( count( $matomo_id_sites ) > 1 ) {
					// there are more than one record: we'll have to identify which one has data and which one must be removed from the matomo_site table
					$this->logger->log( 'There is a problem in your configuration. Please contact support at wordpress@matomo.org' );
					return false;
				}
			}
		}

		if ( empty( $blog_name ) ) {
			$blog_name = esc_html__( 'Default', 'matomo' );
		} else {
			$blog_name = substr( $blog_name, 0, self::MAX_LENGTH_SITE_NAME );
		}

		$track_ecommerce   = (int) $this->settings->get_global_option( 'track_ecommerce' );
		$site_currency     = $this->settings->get_global_option( Settings::SITE_CURRENCY );
		$detected_timezone = $this->detect_timezone();

		$data_provider    = StaticContainer::get( CurrencyDataProvider::class );
		$valid_currencies = $data_provider->getCurrencyList();
		if ( ! array_key_exists( $site_currency, $valid_currencies ) ) {
			$site_currency = 'USD';
		}

		if ( ! empty( $idsite ) ) {
			$this->logger->log( 'Matomo site is known for blog (' . $idsite . ')... will update' );

			$site = $sites_manager_model->getSiteFromId( $idsite );
			if ( ! empty( $site ) ) {
				// if site has changed then we have to update it
				if ( $site['name'] !== $blog_name
					 || $site['main_url'] !== $blog_url
				     // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					 || $site['ecommerce'] != $track_ecommerce
					 || $site['currency'] !== $site_currency
					 || $site['timezone'] !== $detected_timezone ) {

					/** @var WP_Site $site */
					$params = [
						'name'      => $blog_name,
						'main_url'  => $blog_url,
						'ecommerce' => $track_ecommerce,
						'currency'  => $site_currency,
						'timezone'  => $detected_timezone,
					];
					$sites_manager_model->updateSite( $params, $idsite );

					do_action( 'matomo_site_synced', $idsite, $blog_id );

					// no actual setting changed but we make sure the tracking code will be updated after an update
					$this->settings->apply_tracking_related_changes( [] );
				}

				$this->config_sync->sync_config_for_current_site();

				return true;
			}
		}

		$this->logger->log( 'Matomo site is not known for blog... will create site' );

		/** @var WP_Site $site */
		$idsite = null;

		$this->set_enable_sites_admin( 1 );

		Access::doAsSuperUser(
			function () use ( $blog_name, $blog_url, $detected_timezone, $track_ecommerce, &$idsite, $site_currency ) {
				SitesManager\API::unsetInstance();
				// we need to unset the instance to make sure it fetches the
				// up to date dependencies eg current plugin manager etc

				$idsite                         = SitesManager\API::getInstance()->addSite(
					$blog_name,
					[ $blog_url ],
					$track_ecommerce,
					$site_search                = null,
					$search_keyword_parameters  = null,
					$search_category_parameters = null,
					$excluded_ips               = null,
					$excluded_query_parameters  = null,
					$detected_timezone,
					$site_currency
				);
			}
		);
		$this->set_enable_sites_admin( 0 );

		$this->logger->log( 'Matomo created site with ID ' . $idsite . ' for blog' );

		if ( ! is_numeric( $idsite ) || 0 === $idsite || '0' === $idsite ) {
			$this->logger->log( sprintf( 'Creating the website failed: %s', wp_json_encode( $blog_id ) ) );

			return false;
		}

		Site::map_matomo_site_id( $blog_id, $idsite );

		$this->config_sync->sync_config_for_current_site();

		do_action( 'matomo_site_synced', $idsite, $blog_id );

		return true;
	}

	private function set_enable_sites_admin( $enabled ) {
		$general                       = Config::getInstance()->General;
		$general['enable_sites_admin'] = (int) $enabled;
		Config::getInstance()->General = $general;
	}

	private function detect_timezone() {
		$timezone = get_option( 'timezone_string' );

		if ( $timezone && $this->check_and_try_to_set_default_timezone( $timezone ) ) {
			return $timezone;
		}

		// older WordPress
		$utc_offset = (int) get_option( 'gmt_offset', 0 );

		if ( 0 === $utc_offset ) {
			return 'UTC';
		}

		$utc_offset_in_seconds = $utc_offset * 3600;
		$timezone              = timezone_name_from_abbr( '', $utc_offset_in_seconds );

		if ( $timezone && $this->check_and_try_to_set_default_timezone( $timezone ) ) {
			return $timezone;
		}

		$dst = (bool) gmdate( 'I' );
		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( $dst === (bool) $city['dst']
					 && $city['timezone_id']
					 && (int) $city['offset'] === $utc_offset_in_seconds ) {
					return $city['timezone_id'];
				}
			}
		}

		if ( is_numeric( $utc_offset ) ) {
			if ( $utc_offset > 0 ) {
				$timezone = 'UTC+' . $utc_offset;
			} else {
				$timezone = 'UTC' . $utc_offset;
			}

			if ( $this->check_and_try_to_set_default_timezone( $timezone ) ) {
				return $timezone;
			}
		}

		return 'UTC';
	}

	private function check_and_try_to_set_default_timezone( $timezone ) {
		try {
			Access::doAsSuperUser(
				function () use ( $timezone ) {
					// make sure we're loading the latest instance with all up to date dependencies... mainly needed for tests
					SitesManager\API::unsetInstance();
					SitesManager\API::getInstance()->setDefaultTimezone( $timezone );
				}
			);
		} catch ( Exception $e ) {
			return false;
		}

		return true;
	}
}
