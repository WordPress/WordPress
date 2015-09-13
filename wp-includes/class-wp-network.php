<?php
/**
 * Network API: WP_Network object class
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
 */
class WP_Network {

	/**
	 * Network ID.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $id;

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
	 * @since 4.4.0
	 * @access public
	 * @var int
	 */
	public $blog_id = 0;

	/**
	 * Domain used to set cookies for this network.
	 *
	 * @since 4.4.0
	 * @access public
	 * @var int
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

		$this->_set_cookie_domain();
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
}
