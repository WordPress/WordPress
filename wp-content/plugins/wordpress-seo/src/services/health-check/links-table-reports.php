<?php

namespace Yoast\WP\SEO\Services\Health_Check;

use WPSEO_Admin_Utils;
use WPSEO_Shortlinker;

/**
 * Presents a set of different messages for the Links_Table health check.
 */
class Links_Table_Reports {

	use Reports_Trait;

	/**
	 * Shortlinker object used to create short links for reports.
	 *
	 * @var WPSEO_Shortlinker
	 */
	private $shortlinker;

	/**
	 * Constructor
	 *
	 * @param  Report_Builder_Factory $report_builder_factory The factory for result builder objects.
	 *                                                        This class uses the report builder to generate WordPress-friendly
	 *                                                        health check results.
	 * @param  WPSEO_Shortlinker      $shortlinker            Object used to add short links to the report description.
	 */
	public function __construct(
		Report_Builder_Factory $report_builder_factory,
		WPSEO_Shortlinker $shortlinker
	) {
		$this->report_builder_factory = $report_builder_factory;
		$this->shortlinker            = $shortlinker;
	}

	/**
	 * Returns the message for a successful health check.
	 *
	 * @return string[] The message as a WordPress site status report.
	 */
	public function get_success_result() {
		return $this->get_report_builder()
			->set_label( \__( 'The text link counter is working as expected', 'wordpress-seo' ) )
			->set_status_good()
			->set_description( $this->get_success_description() )
			->build();
	}

	/**
	 * Returns the message for a failed health check.
	 *
	 * @return string[] The message as a WordPress site status report.
	 */
	public function get_links_table_not_accessible_result() {
		return $this->get_report_builder()
			->set_label( \__( 'The text link counter feature is not working as expected', 'wordpress-seo' ) )
			->set_status_recommended()
			->set_description( $this->get_links_table_not_accessible_description() )
			->set_actions( $this->get_actions() )
			->build();
	}

	/**
	 * Returns the description for when the health check was successful.
	 *
	 * @return string The description as a string.
	 */
	private function get_success_description() {
		return \sprintf(
			/* translators: 1: Link to the Yoast SEO blog, 2: Link closing tag. */
			\esc_html__( 'The text link counter helps you improve your site structure. %1$sFind out how the text link counter can enhance your SEO%2$s.', 'wordpress-seo' ),
			'<a href="' . $this->shortlinker->get( 'https://yoa.st/3zw' ) . '" target="_blank">',
			WPSEO_Admin_Utils::get_new_tab_message() . '</a>'
		);
	}

	/**
	 * Returns the description for when the health couldn't access the links table.
	 *
	 * @return string The description as a string.
	 */
	private function get_links_table_not_accessible_description() {
		return \sprintf(
			/* translators: 1: Yoast SEO. */
			\__( 'For this feature to work, %1$s needs to create a table in your database. We were unable to create this table automatically.', 'wordpress-seo' ),
			'Yoast SEO'
		);
	}

	/**
	 * Returns the actions that the user should take when the links table is not accessible.
	 *
	 * @return string The actions as a string.
	 */
	private function get_actions() {
		return \sprintf(
			/* translators: 1: Link to the Yoast help center, 2: Link closing tag. */
			\esc_html__( '%1$sFind out how to solve this problem on our help center%2$s.', 'wordpress-seo' ),
			'<a href="' . $this->shortlinker->get( 'https://yoa.st/3zv' ) . '" target="_blank">',
			WPSEO_Admin_Utils::get_new_tab_message() . '</a>'
		);
	}
}
