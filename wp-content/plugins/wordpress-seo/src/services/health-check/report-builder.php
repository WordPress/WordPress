<?php

namespace Yoast\WP\SEO\Services\Health_Check;

/**
 * Provides an interface to build WordPress-friendly health check results.
 */
class Report_Builder {

	/**
	 * Passed health check.
	 */
	public const STATUS_GOOD = 'good';

	/**
	 * Changes are recommended but not necessary.
	 */
	public const STATUS_RECOMMENDED = 'recommended';

	/**
	 * Significant issues that the user should consider fixing.
	 */
	public const STATUS_CRITICAL = 'critical';

	/**
	 * The user-facing label.
	 *
	 * @var string
	 */
	private $label = '';

	/**
	 * The identifier that WordPress uses for the health check.
	 *
	 * @var string
	 */
	private $test_identifier = '';

	/**
	 * The test status (good, recommended, critical).
	 *
	 * @var string
	 */
	private $status = '';

	/**
	 * The short description for the result.
	 *
	 * @var string
	 */
	private $description = '';

	/**
	 * Actions that the user can take to solve the health check result.
	 *
	 * @var string
	 */
	private $actions = '';

	/**
	 * Sets the label for the health check that the user can see.
	 *
	 * @param  string $label The label that the user can see.
	 * @return Report_Builder This builder.
	 */
	public function set_label( $label ) {
		$this->label = $label;
		return $this;
	}

	/**
	 * Sets the name for the test that the plugin uses to identify the test.
	 *
	 * @param  string $test_identifier The identifier for the health check.
	 * @return Report_Builder This builder.
	 */
	public function set_test_identifier( $test_identifier ) {
		$this->test_identifier = $test_identifier;
		return $this;
	}

	/**
	 * Sets the status of the test result to GOOD (green label).
	 *
	 * @return Report_Builder This builder.
	 */
	public function set_status_good() {
		$this->status = self::STATUS_GOOD;
		return $this;
	}

	/**
	 * Sets the status of the test result to RECOMMENDED (orange label).
	 *
	 * @return Report_Builder This builder.
	 */
	public function set_status_recommended() {
		$this->status = self::STATUS_RECOMMENDED;
		return $this;
	}

	/**
	 * Sets the status of the test result to CRITICAL (red label).
	 *
	 * @return Report_Builder This builder.
	 */
	public function set_status_critical() {
		$this->status = self::STATUS_CRITICAL;
		return $this;
	}

	/**
	 * Sets a description for the test result. This will be the heading for the result in the user interface.
	 *
	 * @param  string $description The description for the test result.
	 * @return Report_Builder This builder.
	 */
	public function set_description( $description ) {
		$this->description = $description;
		return $this;
	}

	/**
	 * Sets a text that describes how the user can solve the failed health check.
	 *
	 * @param  string $actions The descriptive text.
	 * @return Report_Builder This builder.
	 */
	public function set_actions( $actions ) {
		$this->actions = $actions;
		return $this;
	}

	/**
	 * Builds an array of strings in the format that WordPress uses to display health checks (https://developer.wordpress.org/reference/hooks/site_status_test_result/).
	 *
	 * @return array The report in WordPress' site status report format.
	 */
	public function build() {
		return [
			'label'       => $this->label,
			'status'      => $this->status,
			'badge'       => $this->get_badge(),
			'description' => $this->description,
			'actions'     => $this->get_actions_with_signature(),
			'test'        => $this->test_identifier,
		];
	}

	/**
	 * Generates a badge that the user can see.
	 *
	 * @return string[] The badge.
	 */
	private function get_badge() {
		return [
			'label' => $this->get_badge_label(),
			'color' => $this->get_badge_color(),
		];
	}

	/**
	 * Generates the label for a badge.
	 *
	 * @return string The badge label.
	 */
	private function get_badge_label() {
		return \__( 'SEO', 'wordpress-seo' );
	}

	/**
	 * Generates the color for the badge using the current status.
	 *
	 * @return string The color for the badge's outline.
	 */
	private function get_badge_color() {
		if ( $this->status === self::STATUS_CRITICAL || $this->status === self::STATUS_RECOMMENDED ) {
			return 'red';
		}

		return 'blue';
	}

	/**
	 * Concatenates the set actions with Yoast's signature.
	 *
	 * @return string A string containing the set actions and Yoast's signature.
	 */
	private function get_actions_with_signature() {
		return $this->actions . $this->get_signature();
	}

	/**
	 * Generates Yoast's signature that's displayed at the bottom of the health check result.
	 *
	 * @return string Yoast's signature as an HTML string.
	 */
	private function get_signature() {
		return \sprintf(
			/* translators: 1: Start of a paragraph beginning with the Yoast icon, 2: Expands to 'Yoast SEO', 3: Paragraph closing tag. */
			\esc_html__( '%1$sThis was reported by the %2$s plugin%3$s', 'wordpress-seo' ),
			'<p class="yoast-site-health__signature"><img src="' . \esc_url( \plugin_dir_url( \WPSEO_FILE ) . 'packages/js/images/Yoast_SEO_Icon.svg' ) . '" alt="" height="20" width="20" class="yoast-site-health__signature-icon">',
			'Yoast SEO',
			'</p>'
		);
	}
}
