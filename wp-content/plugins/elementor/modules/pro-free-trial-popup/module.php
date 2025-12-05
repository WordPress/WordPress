<?php
/**
 * Pro Free Trial Popup Module
 *
 * @package Elementor\Modules\ProFreeTrialPopup
 * @since 3.32.0
 */

namespace Elementor\Modules\ProFreeTrialPopup;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Core\Utils\Ab_Test;
use Elementor\Core\Isolation\Elementor_Adapter;
use Elementor\Core\Isolation\Elementor_Adapter_Interface;
use Elementor\Modules\ElementorCounter\Module as Elementor_Counter;
use Elementor\Utils;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const EXPERIMENT_NAME = 'e_pro_free_trial_popup';
	const MODULE_NAME = 'pro-free-trial-popup';
	const POPUP_DISPLAYED_OPTION = '_e_pro_free_trial_popup_displayed';
	const AB_TEST_NAME = 'pro_free_trial_popup';
	const REQUIRED_VISIT_COUNT = 4;
	const EXTERNAL_DATA_URL = 'https://assets.elementor.com/pro-free-trial-popup/v1/pro-free-trial-popup.json';
	const ACTIVE = 'active';

	private Elementor_Adapter_Interface $elementor_adapter;

	public function __construct() {
		parent::__construct();

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! Plugin::$instance->experiments->is_feature_active( self::EXPERIMENT_NAME ) ) {
			return;
		}

		if ( Utils::has_pro() ) {
			return;
		}

		$this->elementor_adapter = new Elementor_Adapter();

		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'maybe_enqueue_popup' ] );
	}

	public function get_name() {
		return 'pro-free-trial-popup';
	}

	public static function get_experimental_data(): array {
		return [
			'name' => self::EXPERIMENT_NAME,
			'title' => esc_html__( 'Pro Free Trial Popup', 'elementor' ),
			'description' => esc_html__( 'Show Pro free trial popup on 4th editor visit', 'elementor' ),
			'hidden' => true,
			'default' => Experiments_Manager::STATE_INACTIVE,
			'new_site' => [
				'default_active' => true,
				'minimum_installation_version' => '3.32.0',
			],
		];
	}

	/**
	 * Check if popup should be enqueued and enqueue if needed
	 */
	public function maybe_enqueue_popup(): void {
		if ( ! $this->should_show_popup() ) {
			return;
		}

		$this->enqueue_scripts();
		$this->set_popup_as_displayed();
	}

	/**
	 * Determine if popup should be shown
	 *
	 * @return bool True if popup should be shown
	 */
	private function should_show_popup(): bool {

		if ( ! $this->is_feature_enabled() ) {
			return false;
		}

		if ( $this->is_before_fourth_visit() ) {
			return false;
		}

		if ( $this->has_popup_been_displayed() ) {
			return false;
		}

		$result = Ab_Test::should_show_feature( self::AB_TEST_NAME );

		return $result;
	}

	/**
	 * Check if feature is enabled via external JSON
	 *
	 * @return bool True if feature is enabled
	 */
	private function is_feature_enabled(): bool {
		$data = $this->get_external_data();
		return ( self::ACTIVE === $data['pro-free-trial-popup'][0]['status'] );
	}

	/**
	 * Get external JSON data
	 *
	 * @return array External data or empty array on failure
	 */
	private function get_external_data(): array {
		$cached_data = get_transient( 'elementor_pro_free_trial_data' );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		$response = wp_remote_get( self::EXTERNAL_DATA_URL );

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			return [];
		}

		set_transient( 'elementor_pro_free_trial_data', $data, HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Check if current visit is before the 4th visit
	 *
	 * @return bool True if before 4th visit
	 */
	private function is_before_fourth_visit(): bool {
		if ( ! $this->elementor_adapter ) {
			return true;
		}

		$editor_visit_count = $this->elementor_adapter->get_count( Elementor_Counter::EDITOR_COUNTER_KEY );
		return $editor_visit_count < self::REQUIRED_VISIT_COUNT;
	}

	/**
	 * Check if popup has already been displayed to this user
	 *
	 * @return bool True if already displayed
	 */
	private function has_popup_been_displayed(): bool {
		return (bool) get_user_meta( $this->get_current_user_id(), self::POPUP_DISPLAYED_OPTION, true );
	}

	/**
	 * Mark popup as displayed for current user
	 */
	private function set_popup_as_displayed(): void {
		$user_id = $this->get_current_user_id();
		update_user_meta( $user_id, self::POPUP_DISPLAYED_OPTION, true );
	}

	/**
	 * Enqueue popup scripts
	 */
	private function enqueue_scripts(): void {
		$min_suffix = Utils::is_script_debug() ? '' : '.min';
		$script_url = ELEMENTOR_ASSETS_URL . 'js/pro-free-trial-popup' . $min_suffix . '.js';

		wp_enqueue_script(
			self::MODULE_NAME,
			$script_url,
			[
				'react',
				'react-dom',
				'elementor-common',
				'elementor-v2-ui',
			],
			ELEMENTOR_VERSION,
			true
		);

		$external_data = $this->get_external_data();
		$popup_data = $this->extract_popup_data( $external_data );

		wp_localize_script( self::MODULE_NAME, 'elementorProFreeTrialData', $popup_data );

		wp_set_script_translations( self::MODULE_NAME, 'elementor' );
	}

	/**
	 * Extract popup data from external data
	 *
	 * @param array $external_data The full external data array
	 * @return array Popup data or empty array if not found
	 */
	private function extract_popup_data( array $external_data ): array {
		if ( ! isset( $external_data['pro-free-trial-popup'] ) || ! is_array( $external_data['pro-free-trial-popup'] ) ) {
			return [];
		}

		return $external_data['pro-free-trial-popup'][0];
	}

	/**
	 * Get current user ID
	 *
	 * @return int Current user ID
	 */
	private function get_current_user_id(): int {
		$current_user = wp_get_current_user();
		return $current_user->ID ?? 0;
	}
}
