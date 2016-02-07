<?php
/**
 * Site API: WP_Site class
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 4.5.0
 */

/**
 * Core class used for interacting with a multisite site.
 *
 * This class is used during load to populate the `$current_blog` global and
 * setup the current site.
 *
 * @since 4.5.0
 */
final class WP_Site {

	/**
	 * Site ID.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $blog_id;

	/**
	 * Domain of the site.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $domain = '';

	/**
	 * Path of the site.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $path = '';

	/**
	 * The ID of the site's parent network.
	 *
	 * Named "site" vs. "network" for legacy reasons. An individual site's "site" is
	 * its network.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $site_id = '0';

	/**
	 * The date on which the site was created or registered.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string Date in MySQL's datetime format.
	 */
	public $registered = '0000-00-00 00:00:00';

	/**
	 * The date and time on which site settings were last updated.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string Date in MySQL's datetime format.
	 */
	public $last_updated = '0000-00-00 00:00:00';

	/**
	 * Whether the site should be treated as public.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $public = '1';

	/**
	 * Whether the site should be treated as archived.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $archived = '0';

	/**
	 * Whether the site should be treated as mature.
	 *
	 * Handling for this does not exist throughout WordPress core, but custom
	 * implementations exist that require the property to be present.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $mature = '0';

	/**
	 * Whether the site should be treated as spam.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $spam = '0';

	/**
	 * Whether the site should be treated as deleted.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $deleted = '0';

	/**
	 * The language pack associated with this site.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @access public
	 * @var string
	 */
	public $lang_id = '0';

	/**
	 * Retrieves a site from the database by its ID.
	 *
	 * @static
	 * @since 4.5.0
	 * @access public
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int $site_id The ID of the site to retrieve.
	 * @return WP_Site|false The site's object if found. False if not.
	 */
	public static function get_instance( $site_id ) {
		global $wpdb;

		$site_id = (int) $site_id;
		if ( ! $site_id ) {
			return false;
		}

		$_site = wp_cache_get( $site_id, 'sites' );

		if ( ! $_site ) {
			$_site = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->blogs} WHERE blog_id = %d LIMIT 1", $site_id ) );

			if ( empty( $_site ) || is_wp_error( $_site ) ) {
				return false;
			}

			wp_cache_add( $site_id, $_site, 'sites' );
		}

		return new WP_Site( $_site );
	}

	/**
	 * Creates a new WP_Site object.
	 *
	 * Will populate object properties from the object provided and assign other
	 * default properties based on that information.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param WP_Site|object $site A site object.
	 */
	public function __construct( $site ) {
		foreach( get_object_vars( $site ) as $key => $value ) {
			$this->$key = $value;
		}
	}
}
