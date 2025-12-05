<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Presents a set of different messages for the Page_Comments health check.
 */
class Page_Comments_Reports {

	use Reports_Trait;

	/**
	 * Constructor.
	 *
	 * @param  Report_Builder_Factory $report_builder_factory The factory for result builder objects.
	 *                                                        This class uses the report builder to generate WordPress-friendly
	 *                                                        health check results.
	 */
	public function __construct( Report_Builder_Factory $report_builder_factory ) {
		$this->report_builder_factory = $report_builder_factory;
	}

	/**
	 * Returns the report for when comments are set to be all on one page.
	 *
	 * @return string[] The message as a WordPress site status report.
	 */
	public function get_success_result() {
		return $this->get_report_builder()
			->set_label( \esc_html__( 'Comments are displayed on a single page', 'wordpress-seo' ) )
			->set_status_good()
			->set_description( \__( 'Comments on your posts are displayed on a single page. This is just like we\'d suggest it. You\'re doing well!', 'wordpress-seo' ) )
			->build();
	}

	/**
	 * Returns the report for when comments are set to be broken up across multiple pages.
	 *
	 * @return string[] The message as a WordPress site status report.
	 */
	public function get_has_comments_on_multiple_pages_result() {
		return $this->get_report_builder()
			->set_label( \__( 'Comments break into multiple pages', 'wordpress-seo' ) )
			->set_status_recommended()
			->set_description( \__( 'Comments on your posts break into multiple pages. As this is not needed in 999 out of 1000 cases, we recommend you disable it. To fix this, uncheck "Break comments into pages..." on the Discussion Settings page.', 'wordpress-seo' ) )
			->set_actions( $this->get_has_comments_on_multiple_pages_actions() )
			->build();
	}

	/**
	 * Returns the actions for when the comments are set to be broken up across multiple pages.
	 *
	 * @return string The actions as a string.
	 */
	private function get_has_comments_on_multiple_pages_actions() {
		return \sprintf(
			/* translators: 1: Opening tag of the link to the discussion settings page, 2: Link closing tag. */
			\esc_html__( '%1$sGo to the Discussion Settings page%2$s', 'wordpress-seo' ),
			'<a href="' . \esc_url( \admin_url( 'options-discussion.php' ) ) . '">',
			'</a>'
		);
	}
}
