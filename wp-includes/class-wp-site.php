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
 *
 * @property int    $id
 * @property int    $network_id
 * @property string $blogname
 * @property string $siteurl
 * @property int    $post_count
 * @property string $home
 */
final class WP_Site {

	/**
	 * Site ID.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $blog_id;

	/**
	 * Domain of the site.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $domain = '';

	/**
	 * Path of the site.
	 *
	 * @since 4.5.0
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
	 * @var string
	 */
	public $site_id = '0';

	/**
	 * The date on which the site was created or registered.
	 *
	 * @since 4.5.0
	 * @var string Date in MySQL's datetime format.
	 */
	public $registered = '0000-00-00 00:00:00';

	/**
	 * The date and time on which site settings were last updated.
	 *
	 * @since 4.5.0
	 * @var string Date in MySQL's datetime format.
	 */
	public $last_updated = '0000-00-00 00:00:00';

	/**
	 * Whether the site should be treated as public.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $public = '1';

	/**
	 * Whether the site should be treated as archived.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
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
	 * @var string
	 */
	public $mature = '0';

	/**
	 * Whether the site should be treated as spam.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $spam = '0';

	/**
	 * Whether the site should be treated as deleted.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $deleted = '0';

	/**
	 * The language pack associated with this site.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.5.0
	 * @var string
	 */
	public $lang_id = '0';

	/**
	 * Retrieves a site from the database by its ID.
	 *
	 * @static
	 * @since 4.5.0
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
	 *
	 * @param WP_Site|object $site A site object.
	 */
	public function __construct( $site ) {
		foreach( get_object_vars( $site ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Converts an object to array.
	 *
	 * @since 4.6.0
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

	/**
	 * Getter.
	 *
	 * Allows current multisite naming conventions when getting properties.
	 * Allows access to extended site properties.
	 *
	 * @since 4.6.0
	 *
	 * @param string $key Property to get.
	 * @return mixed Value of the property. Null if not available.
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'id':
				return (int) $this->blog_id;
			case 'network_id':
				return (int) $this->site_id;
			case 'blogname':
			case 'siteurl':
			case 'post_count':
			case 'home':
			default: // Custom properties added by 'site_details' filter.
				if ( ! did_action( 'ms_loaded' ) ) {
					return null;
				}

				$details = $this->get_details();
				if ( isset( $details->$key ) ) {
					return $details->$key;
				}
		}

		return null;
	}

	/**
	 * Isset-er.
	 *
	 * Allows current multisite naming conventions when checking for properties.
	 * Checks for extended site properties.
	 *
	 * @since 4.6.0
	 *
	 * @param string $key Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $key ) {
		switch ( $key ) {
			case 'id':
			case 'network_id':
				return true;
			case 'blogname':
			case 'siteurl':
			case 'post_count':
			case 'home':
				if ( ! did_action( 'ms_loaded' ) ) {
					return false;
				}
				return true;
			default: // Custom properties added by 'site_details' filter.
				if ( ! did_action( 'ms_loaded' ) ) {
					return false;
				}

				$details = $this->get_details();
				if ( isset( $details->$key ) ) {
					return true;
				}
		}

		return false;
	}

	/**
	 * Setter.
	 *
	 * Allows current multisite naming conventions while setting properties.
	 *
	 * @since 4.6.0
	 *
	 * @param string $key   Property to set.
	 * @param mixed  $value Value to assign to the property.
	 */
	public function __set( $key, $value ) {
		switch ( $key ) {
			case 'id':
				$this->blog_id = (string) $value;
				break;
			case 'network_id':
				$this->site_id = (string) $value;
				break;
			default:
				$this->$key = $value;
		}
	}

	/**
	 * Retrieves the details for this site.
	 *
	 * This method is used internally to lazy-load the extended properties of a site.
	 *
	 * @since 4.6.0
	 *
	 * @see WP_Site::__get()
	 *
	 * @return stdClass A raw site object with all details included.
	 */
	private function get_details() {
		$details = wp_cache_get( $this->blog_id, 'site-details' );

		if ( false === $details ) {

			switch_to_blog( $this->blog_id );
			// Create a raw copy of the object for backwards compatibility with the filter below.
			$details = new stdClass();
			foreach ( get_object_vars( $this ) as $key => $value ) {
				$details->$key = $value;
			}
			$details->blogname   = get_option( 'blogname' );
			$details->siteurl    = get_option( 'siteurl' );
			$details->post_count = get_option( 'post_count' );
			$details->home       = get_option( 'home' );
			restore_current_blog();

			wp_cache_set( $this->blog_id, $details, 'site-details' );
		}

		/**
		 * Filters a blog's details.
		 *
		 * @since MU (3.0.0)
		 * @deprecated 4.7.0 Use site_details
		 *
		 * @param object $details The blog details.
		 */
		$details = apply_filters_deprecated( 'blog_details', array( $details ), '4.7.0', 'site_details' );

		/**
		 * Filters a site's extended properties.
		 *
		 * @since 4.6.0
		 *
		 * @param stdClass $details The site details.
		 */
		$details = apply_filters( 'site_details', $details );

		return $details;
	}
}
