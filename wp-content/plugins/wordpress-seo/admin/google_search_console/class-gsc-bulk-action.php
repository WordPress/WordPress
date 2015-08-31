<?php
/**
 * @package WPSEO\Admin|Google_Search_Console
 */

/**
 * Class WPSEO_GSC_Bulk_Action
 */
class WPSEO_GSC_Bulk_Action {

	/**
	 * Setting the listener on the bulk action post
	 */
	public function __construct() {
		if ( wp_verify_nonce( filter_input( INPUT_POST, 'wpseo_gsc_nonce' ), 'wpseo_gsc_nonce' ) ) {
			$this->handle_bulk_action();
		}
	}

	/**
	 * Handles the bulk action when there is an action posted
	 */
	private function handle_bulk_action() {
		if ( $bulk_action = $this->determine_bulk_action() ) {
			$this->run_bulk_action( $bulk_action, $this->posted_issues() );

			wp_redirect( filter_input( INPUT_POST, '_wp_http_referer' ) );
			exit;
		}
	}

	/**
	 * Determine which bulk action is selected and return that value
	 *
	 * @return string|bool
	 */
	private function determine_bulk_action() {
		// If posted action is the selected one above the table, return that value.
		if ( $action = filter_input( INPUT_POST, 'action' ) ) {
			return $action;
		}

		// If posted action is the selected one below the table, return that value.
		if ( $action = filter_input( INPUT_POST, 'action2' ) ) {
			return $action;
		}

		return false;
	}

	/**
	 * Get the posted issues and return them
	 *
	 * @return array
	 */
	private function posted_issues() {
		if ( $issues = filter_input( INPUT_POST, 'wpseo_crawl_issues', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ) {
			return $issues;
		}

		// Fallback if issues are empty.
		return array();
	}

	/**
	 * Runs the bulk action
	 *
	 * @param string $bulk_action
	 * @param array  $issues
	 */
	private function run_bulk_action( $bulk_action, $issues ) {
		switch ( $bulk_action ) {
			case 'mark_as_fixed' :
				array_map( array( $this, 'action_mark_as_fixed' ), $issues );

				break;
		}
	}

	/**
	 * Marks the issue as fixed
	 *
	 * @param string $issue
	 *
	 * @return string
	 */
	private function action_mark_as_fixed( $issue ) {
		new WPSEO_GSC_Marker( $issue );

		return $issue;
	}

}
