<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Presents a set of different messages for the Postname_Permalink health check.
 */
class Postname_Permalink_Reports {

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
	 * Returns the report for when permalinks are set to contain the post name.
	 *
	 * @return string[] The message as a WordPress site status report.
	 */
	public function get_success_result() {
		return $this->get_report_builder()
			->set_label( \esc_html__( 'Your permalink structure includes the post name', 'wordpress-seo' ) )
			->set_status_good()
			->set_description( \__( 'You do have your postname in the URL of your posts and pages.', 'wordpress-seo' ) )
			->build();
	}

	/**
	 * Returns the report for when permalinks are not set to contain the post name.
	 *
	 * @return string[] The message as a WordPress site status report.
	 */
	public function get_has_no_postname_in_permalink_result() {
		return $this->get_report_builder()
			->set_label( \__( 'You do not have your postname in the URL of your posts and pages', 'wordpress-seo' ) )
			->set_status_recommended()
			->set_description( $this->get_has_no_postname_in_permalink_description() )
			->set_actions( $this->get_has_no_postname_in_permalink_actions() )
			->build();
	}

	/**
	 * Returns the description for when permalinks are not set to contain the post name.
	 *
	 * @return string The description as a string.
	 */
	private function get_has_no_postname_in_permalink_description() {
		return \sprintf(
			/* translators: %s expands to '/%postname%/' */
			\__( 'It\'s highly recommended to have your postname in the URL of your posts and pages. Consider setting your permalink structure to %s.', 'wordpress-seo' ),
			'<strong>/%postname%/</strong>'
		);
	}

	/**
	 * Returns the actions for when permalinks are not set to contain the post name.
	 *
	 * @return string The actions as a string.
	 */
	private function get_has_no_postname_in_permalink_actions() {
		return \sprintf(
			/* translators: %1$s is a link start tag to the permalink settings page, %2$s is the link closing tag. */
			\__( 'You can fix this on the %1$sPermalink settings page%2$s.', 'wordpress-seo' ),
			'<a href="' . \admin_url( 'options-permalink.php' ) . '">',
			'</a>'
		);
	}
}
