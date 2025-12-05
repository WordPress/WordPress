<?php
namespace Elementor\Modules\EditorAppBar;

use Elementor\Core\Base\Module as BaseModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {
	const PACKAGES = [
		'editor-app-bar',
	];

	const STYLES = [
		'editor-v2-app-bar-overrides',
	];

	const POPUP_DISMISSED_OPTION = '_elementor_structure_popup_dismissed';
	const STRUCTURE_POPUP_TARGET_VERSION = '3.32.0';

	public function get_name() {
		return 'editor-app-bar';
	}

	public function __construct() {
		parent::__construct();

		add_filter( 'elementor/editor/v2/packages', fn( $packages ) => $this->add_packages( $packages ) );
		add_filter( 'elementor/editor/v2/styles', fn( $styles ) => $this->add_styles( $styles ) );
		add_filter( 'elementor/editor/templates', fn( $templates ) => $this->remove_templates( $templates ) );

		add_action( 'elementor/editor/v2/scripts/enqueue', fn() => $this->dequeue_scripts() );
		add_action( 'elementor/editor/v2/styles/enqueue', fn() => $this->dequeue_styles() );

		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'maybe_enqueue_structure_popup' ] );
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
	}

	public function register_ajax_actions( $ajax ) {
		$ajax->register_ajax_action( 'structure_popup_dismiss', [ $this, 'ajax_dismiss_structure_popup' ] );
	}

	public function ajax_dismiss_structure_popup( $data ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			throw new \Exception( 'User not authenticated' );
		}

		update_user_meta( $user_id, self::POPUP_DISMISSED_OPTION, true );

		return [
			'success' => true,
			'message' => 'Structure popup dismissed successfully',
		];
	}

	public function maybe_enqueue_structure_popup(): void {

		if ( ! $this->should_show_structure_popup_for_current_user() ) {
			return;
		}

		wp_localize_script( 'elementor-editor', 'elementorShowInfotip', [ 'shouldShow' => '1' ] );
	}

	private function should_show_structure_popup_for_current_user(): bool {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		if ( ! $this->is_existing_user_upgraded_to_target_version( self::STRUCTURE_POPUP_TARGET_VERSION ) ) {
			return false;
		}

		if ( get_user_meta( $user_id, self::POPUP_DISMISSED_OPTION, true ) ) {
			return false;
		}

		return true;
	}

	private function is_existing_user_upgraded_to_target_version( string $target_version ): bool {

		if ( version_compare( ELEMENTOR_VERSION, $target_version, '<' ) ) {
			return false;
		}

		$installs_history = \Elementor\Core\Upgrade\Manager::get_installs_history();

		return ! empty( $installs_history ) &&
			version_compare( array_key_first( $installs_history ), $target_version, '<' );
	}

	private function add_packages( $packages ) {
		return array_merge( $packages, self::PACKAGES );
	}

	private function add_styles( $styles ) {
		return array_merge( $styles, self::STYLES );
	}

	private function remove_templates( $templates ) {
		return array_diff( $templates, [ 'responsive-bar' ] );
	}

	private function dequeue_scripts() {
		wp_dequeue_script( 'elementor-responsive-bar' );
	}

	private function dequeue_styles() {
		wp_dequeue_style( 'elementor-responsive-bar' );
	}
}
