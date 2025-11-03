<?php
/**
 * REST API: WP_REST_Response class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.4.0
 */

/**
 * Core class used to implement a REST response object.
 *
 * @since 4.4.0
 *
 * @see WP_HTTP_Response
 */
class WP_REST_Response extends WP_HTTP_Response {

	/**
	 * Links related to the response.
	 *
	 * @since 4.4.0
	 * @var array
	 */
	protected $links = array();

	/**
	 * The route that was to create the response.
	 *
	 * @since 4.4.0
	 * @var string
	 */
	protected $matched_route = '';

	/**
	 * The handler that was used to create the response.
	 *
	 * @since 4.4.0
	 * @var null|array
	 */
	protected $matched_handler = null;

	/**
	 * Adds a link to the response.
	 *
	 * {@internal The $rel parameter is first, as this looks nicer when sending multiple.}
	 *
	 * @since 4.4.0
	 *
	 * @link https://tools.ietf.org/html/rfc5988
	 * @link https://www.iana.org/assignments/link-relations/link-relations.xml
	 *
	 * @param string $rel        Link relation. Either an IANA registered type,
	 *                           or an absolute URL.
	 * @param string $href       Target URI for the link.
	 * @param array  $attributes Optional. Link parameters to send along with the URL. Default empty array.
	 */
	public function add_link( $rel, $href, $attributes = array() ) {
		if ( empty( $this->links[ $rel ] ) ) {
			$this->links[ $rel ] = array();
		}

		if ( isset( $attributes['href'] ) ) {
			// Remove the href attribute, as it's used for the main URL.
			unset( $attributes['href'] );
		}

		$this->links[ $rel ][] = array(
			'href'       => $href,
			'attributes' => $attributes,
		);
	}

	/**
	 * Removes a link from the response.
	 *
	 * @since 4.4.0
	 *
	 * @param string      $rel  Link relation. Either an IANA registered type, or an absolute URL.
	 * @param string|null $href Optional. Only remove links for the relation matching the given href.
	 *                          Default null.
	 */
	public function remove_link( $rel, $href = null ) {
		if ( ! isset( $this->links[ $rel ] ) ) {
			return;
		}

		if ( $href ) {
			$this->links[ $rel ] = wp_list_filter( $this->links[ $rel ], array( 'href' => $href ), 'NOT' );
		} else {
			$this->links[ $rel ] = array();
		}

		if ( ! $this->links[ $rel ] ) {
			unset( $this->links[ $rel ] );
		}
	}

	/**
	 * Adds multiple links to the response.
	 *
	 * Link data should be an associative array with link relation as the key.
	 * The value can either be an associative array of link attributes
	 * (including `href` with the URL for the response), or a list of these
	 * associative arrays.
	 *
	 * @since 4.4.0
	 *
	 * @param array $links Map of link relation to list of links.
	 */
	public function add_links( $links ) {
		foreach ( $links as $rel => $set ) {
			// If it's a single link, wrap with an array for consistent handling.
			if ( isset( $set['href'] ) ) {
				$set = array( $set );
			}

			foreach ( $set as $attributes ) {
				$this->add_link( $rel, $attributes['href'], $attributes );
			}
		}
	}

	/**
	 * Retrieves links for the response.
	 *
	 * @since 4.4.0
	 *
	 * @return array List of links.
	 */
	public function get_links() {
		return $this->links;
	}

	/**
	 * Sets a single link header.
	 *
	 * {@internal The $rel parameter is first, as this looks nicer when sending multiple.}
	 *
	 * @since 4.4.0
	 *
	 * @link https://tools.ietf.org/html/rfc5988
	 * @link https://www.iana.org/assignments/link-relations/link-relations.xml
	 *
	 * @param string $rel   Link relation. Either an IANA registered type, or an absolute URL.
	 * @param string $link  Target IRI for the link.
	 * @param array  $other Optional. Other parameters to send, as an associative array.
	 *                      Default empty array.
	 */
	public function link_header( $rel, $link, $other = array() ) {
		$header = '<' . $link . '>; rel="' . $rel . '"';

		foreach ( $other as $key => $value ) {
			if ( 'title' === $key ) {
				$value = '"' . $value . '"';
			}

			$header .= '; ' . $key . '=' . $value;
		}
		$this->header( 'Link', $header, false );
	}

	/**
	 * Retrieves the route that was used.
	 *
	 * @since 4.4.0
	 *
	 * @return string The matched route.
	 */
	public function get_matched_route() {
		return $this->matched_route;
	}

	/**
	 * Sets the route (regex for path) that caused the response.
	 *
	 * @since 4.4.0
	 *
	 * @param string $route Route name.
	 */
	public function set_matched_route( $route ) {
		$this->matched_route = $route;
	}

	/**
	 * Retrieves the handler that was used to generate the response.
	 *
	 * @since 4.4.0
	 *
	 * @return null|array The handler that was used to create the response.
	 */
	public function get_matched_handler() {
		return $this->matched_handler;
	}

	/**
	 * Sets the handler that was responsible for generating the response.
	 *
	 * @since 4.4.0
	 *
	 * @param array $handler The matched handler.
	 */
	public function set_matched_handler( $handler ) {
		$this->matched_handler = $handler;
	}

	/**
	 * Checks if the response is an error, i.e. >= 400 response code.
	 *
	 * @since 4.4.0
	 *
	 * @return bool Whether the response is an error.
	 */
	public function is_error() {
		return $this->get_status() >= 400;
	}

	/**
	 * Retrieves a WP_Error object from the response.
	 *
	 * @since 4.4.0
	 *
	 * @return WP_Error|null WP_Error or null on not an errored response.
	 */
	public function as_error() {
		if ( ! $this->is_error() ) {
			return null;
		}

		$error = new WP_Error();

		if ( is_array( $this->get_data() ) ) {
			$data = $this->get_data();
			$error->add( $data['code'], $data['message'], $data['data'] );

			if ( ! empty( $data['additional_errors'] ) ) {
				foreach ( $data['additional_errors'] as $err ) {
					$error->add( $err['code'], $err['message'], $err['data'] );
				}
			}
		} else {
			$error->add( $this->get_status(), '', array( 'status' => $this->get_status() ) );
		}

		return $error;
	}

	/**
	 * Retrieves the CURIEs (compact URIs) used for relations.
	 *
	 * @since 4.5.0
	 *
	 * @return array Compact URIs.
	 */
	public function get_curies() {
		$curies = array(
			array(
				'name'      => 'wp',
				'href'      => 'https://api.w.org/{rel}',
				'templated' => true,
			),
		);

		/**
		 * Filters extra CURIEs available on REST API responses.
		 *
		 * CURIEs allow a shortened version of URI relations. This allows a more
		 * usable form for custom relations than using the full URI. These work
		 * similarly to how XML namespaces work.
		 *
		 * Registered CURIES need to specify a name and URI template. This will
		 * automatically transform URI relations into their shortened version.
		 * The shortened relation follows the format `{name}:{rel}`. `{rel}` in
		 * the URI template will be replaced with the `{rel}` part of the
		 * shortened relation.
		 *
		 * For example, a CURIE with name `example` and URI template
		 * `http://w.org/{rel}` would transform a `http://w.org/term` relation
		 * into `example:term`.
		 *
		 * Well-behaved clients should expand and normalize these back to their
		 * full URI relation, however some naive clients may not resolve these
		 * correctly, so adding new CURIEs may break backward compatibility.
		 *
		 * @since 4.5.0
		 *
		 * @param array $additional Additional CURIEs to register with the REST API.
		 */
		$additional = apply_filters( 'rest_response_link_curies', array() );

		return array_merge( $curies, $additional );
	}
}
