<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\User;

use Exception;
use Piwik\Access;
use Piwik\Access\Role\Admin;
use Piwik\Access\Role\View;
use Piwik\Access\Role\Write;
use Piwik\Auth\Password;
use Piwik\Common;
use Piwik\Date;
use Piwik\Plugin;
use Piwik\Plugins\LanguagesManager\API;
use Piwik\Plugins\UsersManager;
use Piwik\Plugins\UsersManager\Model;
use WP_User;
use WpMatomo\Bootstrap;
use WpMatomo\Capabilities;
use WpMatomo\Logger;
use WpMatomo\ScheduledTasks;
use WpMatomo\Site;
use WpMatomo\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Sync {

	/**
	 * actually allowed is 100 characters...
	 * but we do -5 to have some room to append `wp_`.$login.XYZ if needed
	 */
	const MAX_USER_NAME_LENGTH = 95;

	/**
	 * @var Logger
	 */
	private $logger;

	public function __construct() {
		$this->logger = new Logger();
	}

	public function register_hooks() {
		add_action( 'add_user_role', [ $this, 'sync_current_users_1000' ], $prio = 10, $args = 0 );
		add_action( 'remove_user_role', [ $this, 'sync_current_users_1000' ], $prio = 10, $args = 0 );
		add_action( 'add_user_to_blog', [ $this, 'sync_current_users_1000' ], $prio = 10, $args = 0 );
		add_action( 'remove_user_from_blog', [ $this, 'sync_current_users_1000' ], $prio = 10, $args = 0 );
		add_action( 'user_register', [ $this, 'sync_current_users_1000' ], $prio = 10, $args = 0 );
		add_action( 'update_option_WPLANG', [ $this, 'on_site_language_change' ], $prio = 10, $args = 0 );
		add_action( 'profile_update', [ $this, 'sync_maybe_background' ], $prio = 10, $args = 0 );
	}

	public function sync_maybe_background() {
		global $pagenow;
		if ( is_admin() && 'users.php' === $pagenow ) {
			// eg for profile update we don't want to sync directly see #365 as it could cause issues with other plugins
			// if they eg alter `get_users` option
			wp_schedule_single_event( time() + 5, ScheduledTasks::EVENT_SYNC );
		} else {
			$this->sync_current_users_1000();
		}
	}

	public function on_site_language_change() {
		unset( $GLOBALS['locale'] ); // same thing that's done after saving in options.php

		$this->sync_current_users_1000();
	}

	public function sync_all() {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			foreach ( get_sites() as $site ) {
				if ( 1 === (int) $site->deleted ) {
					continue;
				}

				switch_to_blog( $site->blog_id );

				$idsite = Site::get_matomo_site_id( $site->blog_id );

				try {
					if ( $idsite ) {
						$users = $this->get_users( [ 'blog_id' => $site->blog_id ] );
						$this->sync_users( $users, $idsite );
					}
				} catch ( Exception $e ) {
					// we don't want to rethrow exception otherwise some other blogs might never sync
					$this->logger->log_exception( 'user_sync ', $e );
				}

				restore_current_blog();
			}
		} else {
			$this->sync_current_users();
		}
	}

	private function get_users( $options = [] ) {
		/** @var WP_User[] $users */
		$users = get_users( $options );

		$current_user = wp_get_current_user();
		if ( ! empty( $current_user ) && ! empty( $current_user->user_login ) ) {
			// refs https://github.com/matomo-org/matomo-for-wordpress/issues/365
			// some other plugins may under circumstances overwrite the get_users query and not return all users
			// as a result we would delete some users in the matomo users table. this way we make sure at least the current
			// user will be added and not deleted even if the list of users is not complete
			$found = false;
			foreach ( $users as $user ) {
				if ( $user->user_login === $current_user->user_login ) {
					$found = true;
					break;
				}
			}
			if ( ! $found ) {
				$users[] = $current_user;
			}
		}

		if ( is_multisite() ) {
			$super_admins = get_super_admins();
			if ( ! empty( $super_admins ) ) {
				foreach ( $super_admins as $super_admin ) {
					$found = false;
					foreach ( $users as $user ) {
						if ( $user->user_login === $super_admin ) {
							$found = true;
							break;
						}
					}
					if ( ! $found ) {
						$user = get_user_by( 'login', $super_admin );
						if ( ! empty( $user ) ) {
							$users[] = $user;
						}
					}
				}
			}
		}

		return $users;
	}

	public function sync_current_users() {
		$idsite = Site::get_matomo_site_id( get_current_blog_id() );
		if ( $idsite ) {
			$users = $this->get_users();
			$this->sync_users( $users, $idsite );
		}
	}

	/**
	 * similar method to sync_current_users which synchronise on the fly only if we have less than 1000 users.
	 * Otherwise it will be done by a background task
	 *
	 * @return void
	 * @see https://github.com/matomo-org/matomo-for-wordpress/issues/460
	 * @see Sync::sync_current_users()
	 */
	public function sync_current_users_1000() {
		if ( ! is_plugin_active( 'matomo/matomo.php' ) ) {
			// @see https://github.com/matomo-org/matomo-for-wordpress/issues/577
			return;
		}
		$idsite = Site::get_matomo_site_id( get_current_blog_id() );
		if ( $idsite ) {
			$num_users = count_users();
			$num_users = $num_users['total_users'];
			if ( $num_users < 1000 ) {
				$users = $this->get_users();
				$this->sync_users( $users, $idsite );
			}
		}
	}

	/**
	 * Sync all users. Make sure to always pass all sites that exist within a given site... you cannot just sync an individual
	 * user... we would delete all other users
	 *
	 * @param WP_User[]  $users
	 * @param int|string $idsite
	 */
	protected function sync_users( $users, $idsite ) {
		Bootstrap::do_bootstrap();

		$this->logger->log( 'Matomo will now sync ' . count( $users ) . ' users' );

		$super_users                  = [];
		$logins_with_some_view_access = [ 'anonmyous' ]; // may or may not exist... we don't want to delete this user though
		$user_model                   = new Model();

		// need to make sure we recreate new instance later with latest dependencies in case they changed
		API::unsetInstance();

		foreach ( $users as $user ) {
			$user_id = $user->ID;

			// todo if we used transactions we could commit it after a possibly new access has been added
			// to prevent UI preventing randomly saying no access between deleting and adding access

			$mapped_matomo_login = User::get_matomo_user_login( $user_id );

			$matomo_login = null;

			if ( user_can( $user, Capabilities::KEY_SUPERUSER ) ) {
				$matomo_login                   = $this->ensure_user_exists( $user );
				$super_users[ $matomo_login ]   = $user;
				$logins_with_some_view_access[] = $matomo_login;
			} elseif ( user_can( $user, Capabilities::KEY_ADMIN ) ) {
				$matomo_login = $this->ensure_user_exists( $user );
				$user_model->deleteUserAccess( $mapped_matomo_login, [ $idsite ] );
				$user_model->addUserAccess( $matomo_login, Admin::ID, [ $idsite ] );
				$user_model->setSuperUserAccess( $matomo_login, false );
				$logins_with_some_view_access[] = $matomo_login;
			} elseif ( user_can( $user, Capabilities::KEY_WRITE ) ) {
				$matomo_login = $this->ensure_user_exists( $user );
				$user_model->deleteUserAccess( $mapped_matomo_login, [ $idsite ] );
				$user_model->addUserAccess( $matomo_login, Write::ID, [ $idsite ] );
				$user_model->setSuperUserAccess( $matomo_login, false );
				$logins_with_some_view_access[] = $matomo_login;
			} elseif ( user_can( $user, Capabilities::KEY_VIEW ) ) {
				$matomo_login = $this->ensure_user_exists( $user );
				$user_model->deleteUserAccess( $mapped_matomo_login, [ $idsite ] );
				$user_model->addUserAccess( $matomo_login, View::ID, [ $idsite ] );
				$user_model->setSuperUserAccess( $matomo_login, false );
				$logins_with_some_view_access[] = $matomo_login;
			} elseif ( $mapped_matomo_login ) {
				$user_model->deleteUserAccess( $mapped_matomo_login, [ $idsite ] );
			}

			if ( $matomo_login ) {
				$locale      = get_user_locale( $user->ID );
				$locale_dash = Common::mb_strtolower( str_replace( '_', '-', $locale ) );
				$parts       = [];
				if ( $locale && in_array( $locale_dash, [ 'zh-cn', 'zh-tw', 'pt-br', 'es-ar' ], true ) ) {
					$parts = [ $locale_dash ];
				} elseif ( ! empty( $locale ) && is_string( $locale ) ) {
					$parts = explode( '_', $locale );
				}

				if ( ! empty( $parts[0] ) ) {
					$lang = $parts[0];
					if ( Plugin\Manager::getInstance()->isPluginActivated( 'LanguagesManager' )
						 && Plugin\Manager::getInstance()->isPluginInstalled( 'LanguagesManager' )
						 && API::getInstance()->isLanguageAvailable( $lang ) ) {
						$user_lang_model = new \Piwik\Plugins\LanguagesManager\Model();
						$user_lang_model->setLanguageForUser( $matomo_login, $lang );
					}
				}
			}
			// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( 1 != $idsite ) {
				// only needed if the actual site is not the default site... makes sure when they click in Matomo
				// UI on "Dashboard" that the correct site is being opened by default
				// eg if the linked site is actually idSite=2.
				Access::doAsSuperUser(
					function () use ( $matomo_login, &$idsite ) {
						try {
							UsersManager\API::unsetInstance();
							// we need to unset the instance to make sure it fetches the
							// up to date dependencies eg current plugin manager etc

							UsersManager\API::getInstance()->setUserPreference(
								$matomo_login,
								UsersManager\API::PREFERENCE_DEFAULT_REPORT,
								$idsite
							);
							//phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						} catch ( Exception $e ) {
							// ignore any error for now
						}
					}
				);
			}
		}

		foreach ( $super_users as $matomo_login => $user ) {
			$user_model->setSuperUserAccess( $matomo_login, true );
		}

		$logins_with_some_view_access = array_unique( $logins_with_some_view_access );
		$all_users                    = $user_model->getUsers( [] );
		foreach ( $all_users as $all_user ) {
			if ( ! in_array( $all_user['login'], $logins_with_some_view_access, true )
				 && ! empty( $all_user['login'] ) ) {
				Access::doAsSuperUser(
					function () use ( $user_model, $all_user ) {
						$user_model->deleteUserOnly( $all_user['login'] );
						$user_model->deleteUserOptions( $all_user['login'] );
						$user_model->deleteUserAccess( $all_user['login'] );
					}
				);
			}
		}
	}

	/**
	 * @param WP_User $wp_user
	 */
	protected function ensure_user_exists( $wp_user ) {
		$user_model = new Model();
		$user_id    = $wp_user->ID;
		$login      = $wp_user->user_login;

		$matomo_user_login = User::get_matomo_user_login( $user_id );
		$user_in_matomo    = null;

		if ( $matomo_user_login ) {
			$user_in_matomo = $user_model->getUser( $matomo_user_login );
		} else {
			$user_by_email = $user_model->getUserByEmail( $wp_user->user_email );

			// the user was deleted without matomo being notified. delete user so we can recreate it
			// below.
			//
			// note: it's also possible there are multiple users with the same email address,
			// but this is currently unsupported in matomo so we don't take that into consideration.
			if ( $user_by_email ) {
				$this->logger->log_exception(
					'user_sync',
					new \Exception(
						'Syncing user with email identical to a user already synced in Matomo. ' .
						'This means there are multiple WP users with the same email, which Matomo ' .
						'does not support, or something has deleted the WP option mapping WP user ' .
						'to Matomo user. Assuming this is a new user to sync and deleting existing user ' .
						'preferences and options.'
					)
				);

				$user_model->deleteUser( $user_by_email['login'] );
			}

			// wp usernames may include whitespace etc
			$login = preg_replace( '/[^A-Za-zÄäÖöÜüß0-9_.@+-]+/D', '_', $login );
			$login = substr( $login, 0, self::MAX_USER_NAME_LENGTH );

			if ( ! $user_model->getUser( $login ) ) {
				// username is available...
				$matomo_user_login = $login;
			} else {
				// this username seems taken... lets create another one

				$index = 0;
				do {
					if ( ! $index ) {
						$matomo_user_login = 'wp_' . $login;
					} else {
						$matomo_user_login = 'wp_' . $login . $index;
					}

					$index ++;
				} while ( $user_model->getUser( $matomo_user_login ) );
			}
		}

		if ( ! $matomo_user_login || empty( $user_in_matomo ) ) {
			$this->logger->log( 'Matomo is now creating a user for user id ' . $user_id . ' with matomo login ' . $matomo_user_login );

			$now      = Date::now()->getDatetime();
			$password = new Password();
			// we generate some random password since log in using matomo won't be happening anyway
			$password = $password->hash( $login . $now . Common::getRandomString( 200 ) . microtime( true ) . Common::generateUniqId() );

			$user_model->addUser( $matomo_user_login, $password, $wp_user->user_email, $now );

			User::map_matomo_user_login( $user_id, $matomo_user_login );
		} elseif ( $user_in_matomo['email'] !== $wp_user->user_email ) {
			$this->logger->log( 'Matomo is now updating the email for wpUserID ' . $user_id . ' matomo login ' . $matomo_user_login );
			$user_model->updateUserFields( $matomo_user_login, [ 'email' => $wp_user->user_email ] );
		}

		return $matomo_user_login;
	}
}
