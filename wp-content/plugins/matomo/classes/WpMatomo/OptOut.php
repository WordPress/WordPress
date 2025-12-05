<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo;

use Piwik\Piwik;
use Piwik\Plugins\PrivacyManager\DoNotTrackHeaderChecker;
use Throwable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class OptOut {
	const OPT_OUT_DIV_ID = 'matomo-opt-out-form-embed';

	private $language = null;

	public function register_hooks() {
		add_shortcode( 'matomo_opt_out', [ $this, 'show_classic_opt_out' ] );
		add_shortcode( 'matomo_opt_out_form', [ $this, 'show_opt_out' ] );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'init', [ $this, 'load_block' ] );
	}

	public function load_scripts() {
		if ( ! is_admin() ) {
			wp_register_script( 'matomo_opt_out_js', plugins_url( 'assets/js/optout.js', MATOMO_ANALYTICS_FILE ), [], 1, true );
		}
	}

	private function translate( $id ) {
		return esc_html( Piwik::translate( $id, [], $this->language ) );
	}

	public function show_opt_out( $atts ) {
		$this->language = $this->get_language_from_atts( $atts );
		$this->language = isset( $this->language ) ? $this->language : 'auto';

		$div_id = self::OPT_OUT_DIV_ID;

		$url = 'app/index.php?module=CoreAdminHome&action=optOutJS&divId=' . $div_id . '&language=' . rawurlencode( $this->language ) . '&showIntro=1';
		$url = plugins_url( $url, MATOMO_ANALYTICS_FILE );

		wp_enqueue_script( 'matomo_opt_out_form_js', $url, [], 1, true ); // output in the footer

		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
		$content = "<div id=\"$div_id\"></div>";
		return $content;
	}

	public function show_classic_opt_out( $atts ) {
		$this->language = $this->get_language_from_atts( $atts );

		try {
			Bootstrap::do_bootstrap();
		} catch ( Throwable $e ) {
			$logger = new Logger();
			$logger->log_exception( 'optout', $e );

			return '<p>An error occurred. Please check Matomo system report in WP-Admin.</p>';
		}

		$dnt_checker = new DoNotTrackHeaderChecker();
		$dnt_enabled = $dnt_checker->isDoNotTrackFound();

		if ( ! empty( $dnt_enabled ) ) {
			return '<p>' . $this->translate( 'CoreAdminHome_OptOutDntFound' ) . '</p>';
		}

		wp_enqueue_script( 'matomo_opt_out_js' );

		$track_visits = empty( $_COOKIE['mtm_consent_removed'] );

		$style_tracking_enabled  = '';
		$style_tracking_disabled = '';
		$checkbox_attr           = '';
		if ( $track_visits ) {
			$style_tracking_enabled = 'style="display:none;"';
			$checkbox_attr          = 'checked="checked"';
		} else {
			$style_tracking_disabled = 'style="display:none;"';
		}

		$content  = '<p id="matomo_opted_out_intro" ' . $style_tracking_enabled . '>' . $this->translate( 'CoreAdminHome_OptOutComplete' ) . ' ' . $this->translate( 'CoreAdminHome_OptOutCompleteBis' ) . '</p>';
		$content .= '<p id="matomo_opted_in_intro" ' . $style_tracking_disabled . '>' . $this->translate( 'CoreAdminHome_YouMayOptOut2' ) . ' ' . $this->translate( 'CoreAdminHome_YouMayOptOut3' ) . '</p>';

		$content .= '<form>
        <input type="checkbox" id="matomo_optout_checkbox" ' . $checkbox_attr . '/>
        <label for="matomo_optout_checkbox"><strong>
        <span id="matomo_opted_in_label" ' . $style_tracking_disabled . '>' . $this->translate( 'CoreAdminHome_YouAreNotOptedOut' ) . ' ' . $this->translate( 'CoreAdminHome_UncheckToOptOut' ) . '</span>
		<span id="matomo_opted_out_label" ' . $style_tracking_enabled . '>' . $this->translate( 'CoreAdminHome_YouAreOptedOut' ) . ' ' . $this->translate( 'CoreAdminHome_CheckToOptIn' ) . '</span>
        </strong></label></form>';
		$content .= '<noscript><p><strong style="color: #ff0000;">This opt out feature requires JavaScript.</strong></p></noscript>';
		$content .= '<p id="matomo_outout_err_cookies" style="display: none;"><strong>' . $this->translate( 'CoreAdminHome_OptOutErrorNoCookies' ) . '</strong></p>';

		return $content;
	}

	public function load_block() {
		// before WordPress 5.0
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		wp_register_script(
			'matomo-opt-out',
			plugins_url( '/assets/js/blocks/matomo_opt_out.js', MATOMO_ANALYTICS_FILE ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			filemtime( plugin_dir_path( MATOMO_ANALYTICS_FILE ) . '/assets/js/blocks/matomo_opt_out.js' ),
			true
		);

		register_block_type(
			'matomo/matomo-opt-out',
			array(
				'editor_script' => 'matomo-opt-out',
			)
		);
	}

	private function get_language_from_atts( $atts ) {
		$a = shortcode_atts(
			[
				'language' => null,
			],
			$atts
		);
		if ( ! empty( $a['language'] ) && strlen( $a['language'] ) < 6 ) {
			return $a['language'];
		}
		return null;
	}
}
