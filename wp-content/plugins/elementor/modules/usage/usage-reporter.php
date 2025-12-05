<?php
namespace Elementor\Modules\Usage;

use Elementor\Modules\System_Info\Reporters\Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor usage report.
 *
 * Elementor system report handler class responsible for generating a report for
 * the user.
 */
class Usage_Reporter extends Base {

	const RECALC_ACTION = 'elementor_usage_recalc';

	public function get_title() {
		return esc_html__( 'Elements Usage', 'elementor' );
	}

	public function get_fields() {
		return [
			'usage' => '',
		];
	}

	public function print_html_label( $label ) {
		$title = $this->get_title();

		if ( empty( $_GET[ self::RECALC_ACTION ] ) ) { // phpcs:ignore -- nonce validation is not required here.
			$nonce = wp_create_nonce( self::RECALC_ACTION );
			$url = add_query_arg( [
				self::RECALC_ACTION => 1,
				'_wpnonce' => $nonce,
			] );

			$title .= '<a id="elementor-usage-recalc" href="' . esc_url( $url ) . '#elementor-usage-recalc" class="box-title-tool">Recalculate</a>';
		} else {
			$title .= $this->get_remove_recalc_query_string_script();
		}

		parent::print_html_label( $title );
	}

	public function get_usage() {
		/** @var Module $module */
		$module = Module::instance();

		if ( ! empty( $_GET[ self::RECALC_ACTION ] ) ) {
			// phpcs:ignore
			$nonce = Utils::get_super_global_value( $_GET, '_wpnonce' );

			if ( ! wp_verify_nonce( $nonce, self::RECALC_ACTION ) ) {
				wp_die( 'Invalid Nonce', 'Invalid Nonce', [
					'back_link' => true,
				] );
			}

			$module->recalc_usage();
		}

		$usage = '';

		foreach ( $module->get_formatted_usage() as $doc_type => $data ) {
			$usage .= '<tr><td>' . $data['title'] . ' ( ' . $data['count'] . ' )</td><td>';

			foreach ( $data['elements'] as $element => $count ) {
				$usage .= $element . ': ' . $count . PHP_EOL;
			}

			$usage .= '</td></tr>';
		}

		return [
			'value' => $usage,
		];
	}

	public function get_raw_usage() {
		/** @var Module $module */
		$module = Module::instance();
		$usage = PHP_EOL;

		foreach ( $module->get_formatted_usage( 'raw' ) as $doc_type => $data ) {
			$usage .= "\t{$data['title']} : " . $data['count'] . PHP_EOL;

			foreach ( $data['elements'] as $element => $count ) {
				$usage .= "\t\t{$element} : {$count}" . PHP_EOL;
			}
		}

		return [
			'value' => $usage,
		];
	}

	/**
	 * Removes the "elementor_usage_recalc" param from the query string to avoid recalc every refresh.
	 * When using a redirect header in place of this approach it throws an error because some components have already output some content.
	 *
	 * @return string
	 */
	private function get_remove_recalc_query_string_script() {
		ob_start();
		?>
		<script>
			// Origin file: modules/usage/usage-reporter.php - get_remove_recalc_query_string_script()
			{
				const url = new URL( window.location );

				url.hash = '';
				url.searchParams.delete( 'elementor_usage_recalc' );
				url.searchParams.delete( '_wpnonce' );

				history.replaceState( '', window.title, url.toString() );
			}
		</script>
		<?php

		return ob_get_clean();
	}
}
