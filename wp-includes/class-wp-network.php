<?php
/**
 * Network API: WP_Network class
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 4.4.0
 */

/**
 * Core class used for interacting with a multisite network.
 *
 * This class is used during load to populate the `$current_site` global and
 * setup the current network.
 *
 * This class is most useful in WordPress multi-network installations where the
 * ability to interact with any network of sites is required.
 *
 * @since 4.4.0
 *
 * @property int $id
 * @property int $site_id
 */
class WP_Network {

	/**
	 * Network ID.
	 *
	 * @since 4.4.0
	 * @since 4.6.0 Converted from public to private to explicitly enable more intuitive
	 *              access via magic methods. As part of the access change, the type was
	 *              also changed from `string` to `int`.
	 * @access private
	 * @var int
	 */
	private $id;

	/**
	 * Domain of the network.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $domain = '';

	/**
	 * Path of the network.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $path = '';

	/**
	 * The ID of the network's main site.
	 *
	 * Named "blog" vs. "site" for legacy reasons. A main site is mapped to
	 * the network when the network is created.
	 *
	 * A numeric string, for compatibility reasons.
	 *
	 * @since 4.4.0
	 * @access private
	 * @var string
	 */
	private $blog_id = '0';

	/**
	 * Domain used to set cookies for this network.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $cookie_domain = '';

	/**
	 * Name of this network.
	 *
	 * Named "site" vs. "network" for legacy reasons.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var string
	 */
	public $site_name = '';

	/**
	 * Retrieve a network from the database by its ID.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int $network_id The ID of the network to retrieve.
	 * @return WP_Network|bool The network's object if found. False if not.
	 */
	public static function get_instance( $network_id ) {
		global $wpdb;

		$network_id = (int) $network_id;
		if ( ! $network_id ) {
			return false;
		}

		$_network = wp_cache_get( $network_id, 'networks' );

		if ( ! $_network ) {
			$_network = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->site} WHERE id = %d LIMIT 1", $network_id ) );

			if ( empty( $_network ) || is_wp_error( $_network ) ) {
				return false;
			}

			wp_cache_add( $network_id, $_network, 'networks' );
		}

		return new WP_Network( $_network );
	}

	/**
	 * Create a new WP_Network object.
	 *
	 * Will populate object properties from the object provided and assign other
	 * default properties based on that information.
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @param WP_Network|object $network A network object.
	 */
	public function __construct( $network ) {
		foreach( get_object_vars( $network ) as $key => $value ) {
			$this->$key = $value;
		}

		$this->_set_site_name();
		$this->_set_cookie_domain();
	}

	/**
	 * Getter.
	 *
	 * Allows current multisite naming conventions when getting properties.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @param string $key Property to get.
	 * @return mixed Value of the property. Null if not available.
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'id';
				return (int) $this->id;
			case 'blog_id':
				return $this->blog_id;
			case 'site_id':
				return (int) $this->blog_id;
		}

		return null;
	}

	/**
	 * Isset-er.
	 *
	 * Allows current multisite naming conventions when checking for properties.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @param string $key Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $key ) {
		switch ( $key ) {
			case 'id':
			case 'blog_id':
			case 'site_id':
				return true;
		}

		return false;
	}

	/**
	 * Setter.
	 *
	 * Allows current multisite naming conventions while setting properties.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @param string $key   Property to set.
	 * @param mixed  $value Value to assign to the property.
	 */
	public function __set( $key, $value ) {
		switch ( $key ) {
			case 'id':
				$this->id = (int) $value;
				break;
			case 'blog_id':
			case 'site_id':
				$this->blog_id = (string) $value;
				break;
			default:
				$this->$key = $value;
		}
	}

	/**
	 * Set the site name assigned to the network if one has not been populated.
	 *
	 * @since 4.4.0
	 * @access private
	 */
	private function _set_site_name() {
		if ( ! empty( $this->site_name ) ) {
			return;
		}

		$default = ucfirst( $this->domain );
		$this->site_name = get_network_option( $this->id, 'site_name', $default );
	}

	/**
	 * Set the cookie domain based on the network domain if one has
	 * not been populated.
	 *
	 * @todo What if the domain of the network doesn't match the current site?
	 *
	 * @since 4.4.0
	 * @access private
	 */
	private function _set_cookie_domain() {
		if ( ! empty( $this->cookie_domain ) ) {
			return;
		}

		$this->cookie_domain = $this->domain;
		if ( 'www.' === substr( $this->cookie_domain, 0, 4 ) ) {
			$this->cookie_domain = substr( $this->cookie_domain, 4 );
		}
	}

	/**
	 * Retrieve the closest matching network for a domain and path.
	 *
	 * This will not necessarily return an exact match for a domain and path. Instead, it
	 * breaks the domain and path into pieces that are then used to match the closest
	 * possibility from a query.
	 *
	 * The intent of this method is to match a network during bootstrap for a
	 * requested site address.
	 *
	 * @since 4.4.0
	 * @access public
	 * @static
	 *
	 * @param string   $domain   Domain to check.
	 * @param string   $path     Path to check.
	 * @param int|null $segments Path segments to use. Defaults to null, or the full path.
	 * @return WP_Network|bool Network object if successful. False when no network is found.
	 */
	public static function get_by_path( $domain = '', $path = '', $segments = null ) {
		global $wpdb;

		$domains = array( $domain );
		$pieces  = explode( '.', $domain );

		/*
		 * It's possible one domain to search is 'com', but it might as well
		 * be 'localhost' or some other locally mapped domain.
		 */
		while ( array_shift( $pieces ) ) {
			if ( ! empty( $pieces ) ) {
				$domains[] = implode( '.', $pieces );
			}
		}

		/*
		 * If we've gotten to this function during normal execution, there is
		 * more than one network installed. At this point, who knows how many
		 * we have. Attempt to optimize for the situation where networks are
		 * only domains, thus meaning paths never need to be considered.
		 *
		 * This is a very basic optimization; anything further could have
		 * drawbacks depending on the setup, so this is best done per-install.
		 */
		$using_paths = true;
		if ( wp_using_ext_object_cache() ) {
			$using_paths = wp_cache_get( 'networks_have_paths', 'site-options' );
			if ( false === $using_paths ) {
				$using_paths = (int) $wpdb->get_var( "SELECT id FROM {$wpdb->site} WHERE path <> '/' LIMIT 1" );
				wp_cache_add( 'networks_have_paths', $using_paths, 'site-options'  );
			}
		}

		$paths = array();
		if ( $using_paths ) {
			$path_segments = array_filter( explode( '/', trim( $path, '/' ) ) );

			/**
			 * Filters the number of path segments to consider when searching for a site.
			 *
			 * @since 3.9.0
			 *
			 * @param int|null $segments The number of path segments to consider. WordPress by default looks at
			 *                           one path segment. The function default of null only makes sense when you
			 *                           know the requested path should match a network.
			 * @param string   $domain   The requested domain.
			 * @param string   $path     The requested path, in full.
			 */
			$segments = apply_filters( 'network_by_path_segments_count', $segments, $domain, $path );

			if ( ( null !== $segments ) && count( $path_segments ) > $segments ) {
				$path_segments = array_slice( $path_segments, 0, $segments );
			}

			while ( count( $path_segments ) ) {
				$paths[] = '/' . implode( '/', $path_segments ) . '/';
				array_pop( $path_segments );
			}

			$paths[] = '/';
		}

		/**
		 * Determine a network by its domain and path.
		 *
		 * This allows one to short-circuit the default logic, perhaps by
		 * replacing it with a routine that is more optimal for your setup.
		 *
		 * Return null to avoid the short-circuit. Return false if no network
		 * can be found at the requested domain and path. Otherwise, return
		 * an object from wp_get_network().
		 *
		 * @since 3.9.0
		 *
		 * @param null|bool|object $network  Network value to return by path.
		 * @param string           $domain   The requested domain.
		 * @param string           $path     The requested path, in full.
		 * @param int|null         $segments The suggested number of paths to consult.
		 *                                   Default null, meaning the entire path was to be consulted.
		 * @param array            $paths    The paths to search for, based on $path and $segments.
		 */
		$pre = apply_filters( 'pre_get_network_by_path', null, $domain, $path, $segments, $paths );
		if ( null !== $pre ) {
			return $pre;
		}

		// @todo Consider additional optimization routes, perhaps as an opt-in for plugins.
		// We already have paths covered. What about how far domains should be drilled down (including www)?

		$search_domains = "'" . implode( "', '", $wpdb->_escape( $domains ) ) . "'";

		if ( ! $using_paths ) {
			$network = $wpdb->get_row( "
				SELECT * FROM {$wpdb->site}
				WHERE domain IN ({$search_domains})
				ORDER BY CHAR_LENGTH(domain)
				DESC LIMIT 1
			" );

			if ( ! empty( $network ) && ! is_wp_error( $network ) ) {
				return new WP_Network( $network );
			}

			return false;

		} else {
			$search_paths = "'" . implode( "', '", $wpdb->_escape( $paths ) ) . "'";
			$networks = $wpdb->get_results( "
				SELECT * FROM {$wpdb->site}
				WHERE domain IN ({$search_domains})
				AND path IN ({$search_paths})
				ORDER BY CHAR_LENGTH(domain) DESC, CHAR_LENGTH(path) DESC
			" );
		}

		/*
		 * Domains are sorted by length of domain, then by length of path.
		 * The domain must match for the path to be considered. Otherwise,
		 * a network with the path of / will suffice.
		 */
		$found = false;
		foreach ( $networks as $network ) {
			if ( ( $network->domain === $domain ) || ( "www.{$network->domain}" === $domain ) ) {
				if ( in_array( $network->path, $paths, true ) ) {
					$found = true;
					break;
				}
			}
			if ( $network->path === '/' ) {
				$found = true;
				break;
			}
		}

		if ( true === $found ) {
			return new WP_Network( $network );
		}

		return false;
	}
}
