<?php
namespace Automattic\WooCommerce\StoreApi\Utilities;

/**
 * Pagination class.
 */
class Pagination {

	/**
	 * Add pagination headers to a response object.
	 *
	 * @param \WP_REST_Response $response Reference to the response object.
	 * @param \WP_REST_Request  $request The request object.
	 * @param int               $total_items Total items found.
	 * @param int               $total_pages Total pages found.
	 * @return \WP_REST_Response
	 */
	public function add_headers( $response, $request, $total_items, $total_pages ) {
		$response->header( 'X-WP-Total', $total_items );
		$response->header( 'X-WP-TotalPages', $total_pages );

		$current_page = $this->get_current_page( $request );
		$link_base    = $this->get_link_base( $request );

		if ( $current_page > 1 ) {
			$previous_page = $current_page - 1;
			if ( $previous_page > $total_pages ) {
				$previous_page = $total_pages;
			}
			$this->add_page_link( $response, 'prev', $previous_page, $link_base );
		}

		if ( $total_pages > $current_page ) {
			$this->add_page_link( $response, 'next', ( $current_page + 1 ), $link_base );
		}

		return $response;
	}

	/**
	 * Get current page.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return int Get the page from the request object.
	 */
	protected function get_current_page( $request ) {
		return (int) $request->get_param( 'page' );
	}

	/**
	 * Get base for links from the request object.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return string
	 */
	protected function get_link_base( $request ) {
		return esc_url( add_query_arg( $request->get_query_params(), rest_url( $request->get_route() ) ) );
	}

	/**
	 * Add a page link.
	 *
	 * @param \WP_REST_Response $response Reference to the response object.
	 * @param string            $name Page link name. e.g. prev.
	 * @param int               $page Page number.
	 * @param string            $link_base Base URL.
	 */
	protected function add_page_link( &$response, $name, $page, $link_base ) {
		$response->link_header( $name, add_query_arg( 'page', $page, $link_base ) );
	}
}
