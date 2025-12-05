<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Presents a set of different messages for the Default_Tagline health check.
 */
class Default_Tagline_Reports {

	use Reports_Trait;

	/**
	 * Constructor
	 *
	 * @param  Report_Builder_Factory $report_builder_factory The factory for result builder objects.
	 *                                                        This class uses the report builder to generate WordPress-friendly
	 *                                                        health check results.
	 */
	public function __construct( Report_Builder_Factory $report_builder_factory ) {
		$this->report_builder_factory = $report_builder_factory;
	}

	/**
	 * Returns the message for a successful health check.
	 *
	 * @return string[] The message as a WordPress site status report.
	 */
	public function get_success_result() {
		return $this->get_report_builder()
			->set_label( \__( 'You changed the default WordPress tagline', 'wordpress-seo' ) )
			->set_status_good()
			->set_description( \__( 'You are using a custom tagline or an empty one.', 'wordpress-seo' ) )
			->build();
	}

	/**
	 * Returns the message for a failed health check. In this case, when the user still has the default WordPress tagline set.
	 *
	 * @return string[] The message as a WordPress site status report.
	 */
	public function get_has_default_tagline_result() {
		return $this->get_report_builder()
			->set_label( \__( 'You should change the default WordPress tagline', 'wordpress-seo' ) )
			->set_status_recommended()
			->set_description( \__( 'You still have the default WordPress tagline. Even an empty one is probably better.', 'wordpress-seo' ) )
			->set_actions( $this->get_actions() )
			->build();
	}

	/**
	 * Returns the actions that the user should take when his tagline is still set to the WordPress default.
	 *
	 * @return string The actions as an HTML string.
	 */
	private function get_actions() {
		$query_args    = [
			'autofocus[control]' => 'blogdescription',
		];
		$customize_url = \add_query_arg( $query_args, \wp_customize_url() );

		return \sprintf(
			/* translators: 1: link open tag; 2: link close tag. */
			\esc_html__( '%1$sYou can change the tagline in the customizer%2$s.', 'wordpress-seo' ),
			'<a href="' . \esc_url( $customize_url ) . '">',
			'</a>'
		);
	}
}
