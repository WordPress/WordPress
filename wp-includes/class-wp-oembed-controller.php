<?php
/**
 * WP_oEmbed_Controller class, used to provide an oEmbed endpoint.
 *
 * @package WordPress
 * @subpackage Embeds
 * @since 4.4.0
 */

/**
 * oEmbed API endpoint controller.
 *
 * Registers the API route and delivers the response data.
 * The output format (XML or JSON) is handled by the REST API.
 *
 * @since 4.4.0
 */
final class WP_oEmbed_Controller {
	/**
	 * Register the oEmbed REST API route.
	 *
	 * @since 4.4.0
	 */
	public function register_routes() {
		/**
		 * Filter the maxwidth oEmbed parameter.
		 *
		 * @since 4.4.0
		 *
		 * @param int $maxwidth Maximum allowed width. Default 600.
		 */
		$maxwidth = apply_filters( 'oembed_default_width', 600 );

		register_rest_route( 'oembed/1.0/', '/embed', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_item' ),
				'args'     => array(
					'url'      => array(
						'required'          => true,
						'sanitize_callback' => 'esc_url_raw',
					),
					'format'   => array(
						'default'           => 'json',
						'sanitize_callback' => 'wp_oembed_ensure_format',
					),
					'maxwidth' => array(
						'default'           => $maxwidth,
						'sanitize_callback' => 'absint',
					),
				),
			),
		) );
	}

	/**
	 * Callback for the API endpoint.
	 *
	 * Returns the JSON object for the post.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|array oEmbed response data or WP_Error on failure.
	 */
	public function get_item( $request ) {
		$post_id = url_to_postid( $request['url'] );

		/**
		 * Filter the determined post ID.
		 *
		 * @since 4.4.0
		 *
		 * @param int    $post_id The post ID.
		 * @param string $url     The requested URL.
		 */
		$post_id = apply_filters( 'oembed_request_post_id', $post_id, $request['url'] );

		$data = get_oembed_response_data( $post_id, $request['maxwidth'] );

		if ( ! $data ) {
			return new WP_Error( 'oembed_invalid_url', get_status_header_desc( 404 ), array( 'status' => 404 ) );
		}

		return $data;
	}
}
