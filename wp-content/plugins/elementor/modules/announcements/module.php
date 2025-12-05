<?php

namespace Elementor\Modules\Announcements;

use Elementor\Core\Base\App as BaseApp;
use Elementor\Modules\Ai\Preferences;
use Elementor\Modules\Announcements\Classes\Announcement;
use Elementor\Settings as ElementorSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseApp {

	const AI_ASSETS_BASE_URL = 'https://assets.elementor.com/ai/v1/';

	/**
	 * @return bool
	 */
	public static function is_active(): bool {
		return is_admin();
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'announcements';
	}

	/**
	 * Render wrapper for the app to load.
	 */
	private function render_app_wrapper() {
		?>
		<div id="e-announcements-root"></div>
		<?php
	}

	/**
	 * Enqueue app scripts.
	 */
	private function enqueue_scripts() {
		wp_enqueue_script(
			'announcements-app',
			$this->get_js_assets_url( 'announcements-app' ),
			[
				'wp-i18n',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'announcements-app', 'elementor' );

		$this->print_config( 'announcements-app' );
	}

	/**
	 * Get initialization settings to use in frontend.
	 *
	 * @return array[]
	 */
	protected function get_init_settings(): array {
		$active_announcements = $this->get_active_announcements();
		$additional_settings = [];

		foreach ( $active_announcements as $announcement ) {
			$additional_settings[] = $announcement->get_prepared_data();
			// @TODO - replace with ajax request from the front after actually triggered
			$announcement->after_triggered();
		}

		return [
			'announcements' => $additional_settings,
		];
	}

	/**
	 * Enqueue the module styles.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'announcements-app',
			$this->get_css_assets_url( 'modules/announcements/announcements' ),
			[],
			ELEMENTOR_VERSION
		);
	}

	/**
	 * Retrieve all announcement in raw format ( array ).
	 *
	 * @return array[]
	 */
	private function get_raw_announcements(): array {
		$raw_announcements = [];

		if ( Preferences::is_ai_enabled( get_current_user_id() ) ) {
			$raw_announcements[] = $this->get_ai_announcement_data();
		}

		// DO NOT USE THIS FILTER
		return apply_filters( 'elementor/announcements/raw_announcements', $raw_announcements );
	}

	private function get_ai_announcement_data(): array {
		return [
			'title' => __( 'Discover your new superpowers ', 'elementor' ),
			'description' => __( '<p>With AI for text, code, image generation and editing, you can bring your vision to life faster than ever. Start your free trial now - <b>no credit card required!</b></p>', 'elementor' ),
			'media' => [
				'type' => 'image',
				'src' => self::AI_ASSETS_BASE_URL . 'images/ai-social-hd.gif',
			],
			'cta' => [
				[
					'label' => __( 'Let\'s do it', 'elementor' ),
					'variant' => 'primary',
					'target' => '_top',
					'url' => '#welcome-ai',
				],
				[
					'label' => __( 'Skip', 'elementor' ),
					'variant' => 'secondary',
				],
			],
			'triggers' => [
				[
					'action' => 'aiStarted',
				],
			],
		];
	}

	/**
	 * Retrieve all announcement objects.
	 *
	 * @return array
	 */
	private function get_announcements(): array {
		$announcements = [];
		foreach ( $this->get_raw_announcements() as $announcement_data ) {
			$announcements[] = new Announcement( $announcement_data );
		}

		return $announcements;
	}

	/**
	 * Retrieve all active announcement objects.
	 *
	 * @return array
	 */
	private function get_active_announcements(): array {
		$active_announcements = [];
		foreach ( $this->get_announcements() as $announcement ) {
			if ( $announcement->is_active() ) {
				$active_announcements[] = $announcement;
			}
		}

		return $active_announcements;
	}

	public function __construct() {
		parent::__construct();

		add_action( 'elementor/init', [ $this, 'on_elementor_init' ] );
	}

	public function on_elementor_init() {
		if ( empty( $this->get_active_announcements() ) ) {
			return;
		}

		add_action( 'elementor/editor/footer', function () {
			$this->render_app_wrapper();
		} );

		add_action( 'elementor/editor/after_enqueue_scripts', function () {
			$this->enqueue_scripts();
			$this->enqueue_styles();
		} );
	}
}
