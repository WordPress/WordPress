<?php

namespace Yoast\WP\SEO\Elementor\Infrastructure;

use WP_Post;

/**
 * Retrieve the WP_Post from the request.
 */
class Request_Post {

	/**
	 * Retrieves the WP_Post, applicable to the current request.
	 *
	 * @return WP_Post|null
	 */
	public function get_post(): ?WP_Post {
		return \get_post( $this->get_post_id() );
	}

	/**
	 * Retrieves the post ID, applicable to the current request.
	 *
	 * @return int|null The post ID.
	 */
	public function get_post_id(): ?int {
		switch ( $this->get_server_request_method() ) {
			case 'GET':
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
				if ( isset( $_GET['post'] ) && \is_numeric( $_GET['post'] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Recommended -- Reason: No sanitization needed because we cast to an integer,We are not processing form information.
					return (int) \wp_unslash( $_GET['post'] );
				}

				break;
			case 'POST':
				// Only allow POST requests when doing AJAX.
				if ( ! \wp_doing_ajax() ) {
					break;
				}

				switch ( $this->get_post_action() ) {
					// Our Yoast SEO form submission, it should include `post_id`.
					case 'wpseo_elementor_save':
						// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information.
						if ( isset( $_POST['post_id'] ) && \is_numeric( $_POST['post_id'] ) ) {
							// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing -- Reason: No sanitization needed because we cast to an integer,We are not processing form information.
							return (int) \wp_unslash( $_POST['post_id'] );
						}

						break;
					// Elementor editor AJAX request.
					case 'elementor_ajax':
						return $this->get_document_id();
				}

				break;
		}

		return null;
	}

	/**
	 * Returns the server request method.
	 *
	 * @return string|null The server request method, in upper case.
	 */
	private function get_server_request_method(): ?string {
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) ) {
			return null;
		}

		if ( ! \is_string( $_SERVER['REQUEST_METHOD'] ) ) {
			return null;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are only comparing it later.
		return \strtoupper( \wp_unslash( $_SERVER['REQUEST_METHOD'] ) );
	}

	/**
	 * Retrieves the action from the POST request.
	 *
	 * @return string|null The action or null if not found.
	 */
	private function get_post_action(): ?string {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information.
		if ( isset( $_POST['action'] ) && \is_string( $_POST['action'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, we are only strictly comparing.
			return (string) \wp_unslash( $_POST['action'] );
		}

		return null;
	}

	/**
	 * Retrieves the document ID from the POST request.
	 *
	 * Note: this is specific to Elementor' `elementor_ajax` action. And then the `get_document_config` internal action.
	 * Currently, you can see this in play when:
	 * - showing the Site Settings in the Elementor editor
	 * - going to another Recent post/page in the Elementor editor V2
	 *
	 * @return int|null The document ID or null if not found.
	 */
	private function get_document_id(): ?int {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information.
		if ( ! ( isset( $_POST['actions'] ) && \is_string( $_POST['actions'] ) ) ) {
			return null;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing -- Reason: No sanitization needed because we cast to an integer (after JSON decode and type/exist checks),We are not processing form information.
		$actions = \json_decode( \wp_unslash( $_POST['actions'] ), true );
		if ( ! \is_array( $actions ) ) {
			return null;
		}

		// Elementor sends everything in a `document-{ID}` format.
		$action = \array_shift( $actions );
		if ( $action === null ) {
			return null;
		}

		// There are multiple action types. We only care about the "get_document_config" one.
		if ( ! ( isset( $action['action'] ) && $action['action'] === 'get_document_config' ) ) {
			return null;
		}

		// Return the ID from the data, if it is set and numeric.
		if ( isset( $action['data']['id'] ) && \is_numeric( $action['data']['id'] ) ) {
			return (int) $action['data']['id'];
		}

		return null;
	}
}
