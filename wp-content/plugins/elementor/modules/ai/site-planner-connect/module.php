<?php

namespace Elementor\Modules\Ai\SitePlannerConnect;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module {
	const NOT_TRANSLATED_APP_NAME = 'Site Planner';
	const PLANNER_ORIGIN = 'https://planner.elementor.com';
	const HIDDEN_PAGE_SLUG = '';

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'on_rest_init' ] );
		add_action( 'admin_menu', [ $this, 'register_menu_page' ], 100 );
		add_filter( 'rest_prepare_application_password', function ( $response, $item, $request ) {
			if ( '/wp/v2/users/me/application-passwords' === $request->get_route() && is_user_logged_in() ) {
				$user = wp_get_current_user();
				$response->data['user_login'] = $user->user_login;
			}
			return $response;
		}, 10, 3 );
	}

	public function on_rest_init(): void {
		( new Wp_Rest_Api() )->register();
	}

	public function register_menu_page() {
		add_submenu_page(
			self::HIDDEN_PAGE_SLUG,
			'App Password Generator',
			'App Password',
			'manage_options',
			'e-site-planner-password-generator',
			[ $this, 'render_menu_page' ]
		);
	}

	public function render_menu_page() {
		ob_start();
		require_once __DIR__ . '/view.php';
		$content = ob_get_clean();
		$vars = [
			'%app_name%' => self::NOT_TRANSLATED_APP_NAME,
			'%safe_origin%' => esc_url( self::PLANNER_ORIGIN ),
			'%domain%' => isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '',
			'%title%' => esc_html__( 'Connect to Site Planner', 'elementor' ),
			'%description%' => esc_html__( 'To connect your site to Site Planner, you need to generate an app password.', 'elementor' ),
			'%cta%' => esc_html__( 'Approve & Connect', 'elementor' ),
		];

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo strtr( $content, $vars );
	}
}
