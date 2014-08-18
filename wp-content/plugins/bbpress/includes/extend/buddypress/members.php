<?php

/**
 * bbPress BuddyPress Members Class
 *
 * @package bbPress
 * @subpackage BuddyPress
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BBP_Forums_Members' ) ) :
/**
 * Member profile modifications
 *
 * @since bbPress (r4395)
 *
 * @package bbPress
 * @subpackage BuddyPress
 */
class BBP_BuddyPress_Members {

	/**
	 * Main constructor for modifying bbPress profile links
	 *
	 * @since bbPress (r4395)
	 */
	public function __construct() {
		$this->setup_actions();
		$this->setup_filters();
	}

	/**
	 * Setup the actions
	 *
	 * @since bbPress (r4395)
	 *
	 * @access private
	 * @uses add_filter() To add various filters
	 * @uses add_action() To add various actions
	 */
	private function setup_actions() {

		// Allow unsubscribe/unfavorite links to work
		add_action( 'bp_template_redirect', array( $this, 'set_member_forum_query_vars' ) );

		/** Favorites *********************************************************/

		// Move handler to 'bp_actions' - BuddyPress bypasses template_loader
		remove_action( 'template_redirect', 'bbp_favorites_handler', 1 );
		add_action(    'bp_actions',        'bbp_favorites_handler', 1 );

		/** Subscriptions *****************************************************/

		// Move handler to 'bp_actions' - BuddyPress bypasses template_loader
		remove_action( 'template_redirect', 'bbp_subscriptions_handler', 1 );
		add_action(    'bp_actions',        'bbp_subscriptions_handler', 1 );
	}

	/**
	 * Setup the filters
	 *
	 * @since bbPress (r4395)
	 *
	 * @access private
	 * @uses add_filter() To add various filters
	 * @uses add_action() To add various actions
	 */
	private function setup_filters() {
		add_filter( 'bbp_pre_get_user_profile_url',    array( $this, 'user_profile_url'            )        );
		add_filter( 'bbp_get_favorites_permalink',     array( $this, 'get_favorites_permalink'     ), 10, 2 );
		add_filter( 'bbp_get_subscriptions_permalink', array( $this, 'get_subscriptions_permalink' ), 10, 2 );
	}

	/** Filters ***************************************************************/

	/**
	 * Override bbPress profile URL with BuddyPress profile URL
	 *
	 * @since bbPress (r3401)
	 * @param string $url
	 * @param int $user_id
	 * @param string $user_nicename
	 * @return string
	 */
	public function user_profile_url( $user_id ) {

		// Define local variable(s)
		$profile_url    = '';
		$component_slug = bbpress()->extend->buddypress->slug;

		// Special handling for forum component
		if ( bp_is_current_component( $component_slug ) ) {

			// Empty action or 'topics' action
			if ( !bp_current_action() || bp_is_current_action( bbp_get_topic_archive_slug() ) ) {
				$profile_url = bp_core_get_user_domain( $user_id ) . $component_slug . '/' . bbp_get_topic_archive_slug();

			// Empty action or 'topics' action
			} elseif ( bp_is_current_action( bbp_get_reply_archive_slug() ) ) {
				$profile_url = bp_core_get_user_domain( $user_id ) . $component_slug . '/' . bbp_get_reply_archive_slug();

			// 'favorites' action
			} elseif ( bbp_is_favorites_active() && bp_is_current_action( bbp_get_user_favorites_slug() ) ) {
				$profile_url = $this->get_favorites_permalink( '', $user_id );

			// 'subscriptions' action
			} elseif ( bbp_is_subscriptions_active() && bp_is_current_action( bbp_get_user_subscriptions_slug() ) ) {
				$profile_url = $this->get_subscriptions_permalink( '', $user_id );
			}

		// Not in users' forums area
		} else {
			$profile_url = bp_core_get_user_domain( $user_id );
		}

		return trailingslashit( $profile_url );
	}

	/**
	 * Override bbPress favorites URL with BuddyPress profile URL
	 *
	 * @since bbPress (r3721)
	 * @param string $url
	 * @param int $user_id
	 * @return string
	 */
	public function get_favorites_permalink( $url, $user_id ) {
		$component_slug = bbpress()->extend->buddypress->slug;
		$url            = trailingslashit( bp_core_get_user_domain( $user_id ) . $component_slug . '/' . bbp_get_user_favorites_slug() );
		return $url;
	}

	/**
	 * Override bbPress subscriptions URL with BuddyPress profile URL
	 *
	 * @since bbPress (r3721)
	 * @param string $url
	 * @param int $user_id
	 * @return string
	 */
	public function get_subscriptions_permalink( $url, $user_id ) {
		$component_slug = bbpress()->extend->buddypress->slug;
		$url            = trailingslashit( bp_core_get_user_domain( $user_id ) . $component_slug . '/' . bbp_get_user_subscriptions_slug() );
		return $url;
	}

	/**
	 * Set favorites and subscriptions query variables if viewing member profile
	 * pages.
	 *
	 * @since bbPress (r4615)
	 *
	 * @global WP_Query $wp_query
	 * @return If not viewing your own profile
	 */
	public function set_member_forum_query_vars() {

		// Special handling for forum component
		if ( ! bp_is_my_profile() )
			return;

		global $wp_query;

		// 'favorites' action
		if ( bbp_is_favorites_active() && bp_is_current_action( bbp_get_user_favorites_slug() ) ) {
			$wp_query->bbp_is_single_user_favs = true;

		// 'subscriptions' action
		} elseif ( bbp_is_subscriptions_active() && bp_is_current_action( bbp_get_user_subscriptions_slug() ) ) {
			$wp_query->bbp_is_single_user_subs = true;
		}
	}
}
endif;
