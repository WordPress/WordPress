<?php

namespace HelloTheme\Modules\AdminHome\Rest;

use HelloTheme\Includes\Utils;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Promotions extends Rest_Base {

	public function get_promotions() {
		$action_links_data = [];

		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) && Utils::is_elementor_active() ) {
			$action_links_data[] = [
				'type' => 'go-pro',
				'image' => HELLO_THEME_IMAGES_URL . 'go-pro.svg',
				'url' => 'https://go.elementor.com/hello-upgrade-epro/',
				'alt' => __( 'Elementor Pro', 'hello-elementor' ),
				'title' => __( 'Bring your vision to life', 'hello-elementor' ),
				'messages' => [
					__( 'Get complete design flexibility for your website with Elementor Pro’s advanced tools and premium features.', 'hello-elementor' ),
				],
				'button' => __( 'Upgrade Now', 'hello-elementor' ),
				'upgrade' => true,
				'features' => [
					__( 'Popup Builder', 'hello-elementor' ),
					__( 'Custom Code & CSS', 'hello-elementor' ),
					__( 'E-commerce Features', 'hello-elementor' ),
					__( 'Collaborative Notes', 'hello-elementor' ),
					__( 'Form Submission', 'hello-elementor' ),
					__( 'Form Integrations', 'hello-elementor' ),
					__( 'Customs Attribute', 'hello-elementor' ),
					__( 'Role Manager', 'hello-elementor' ),
				],
			];
		}

		if (
			! defined( 'ELEMENTOR_IMAGE_OPTIMIZER_VERSION' ) &&
			! defined( 'IMAGE_OPTIMIZATION_VERSION' )
		) {
			$action_links_data[] = [
				'type' => 'go-image-optimizer',
				'image' => HELLO_THEME_IMAGES_URL . 'image-optimizer.svg',
				'url' => Utils::get_plugin_install_url( 'image-optimization' ),
				'alt' => __( 'Elementor Image Optimizer', 'hello-elementor' ),
				'title' => '',
				'messages' => [
					__( 'Optimize Images.', 'hello-elementor' ),
					__( 'Reduce Size.', 'hello-elementor' ),
					__( 'Improve Speed.', 'hello-elementor' ),
					__( 'Try Image Optimizer for free', 'hello-elementor' ),
				],
				'button' => __( 'Install', 'hello-elementor' ),
				'width' => 72,
				'height' => 'auto',
				'target' => '_self',
				'backgroundImage' => HELLO_THEME_IMAGES_URL . 'image-optimization-bg.svg',
			];
		}

		if ( ! defined( 'SEND_VERSION' ) ) {
			$action_links_data[] = [
				'type' => 'go-send',
				'image' => HELLO_THEME_IMAGES_URL . 'send-logo.gif',
				'backgroundColor' => '#EFEFFF',
				'url' => Utils::get_plugin_install_url( 'send-app' ),
				'alt' => __( 'Send', 'hello-elementor' ),
				'title' => '',
				'target' => '_self',
				'messages' => [
					__( 'Connect any website to automated Email & SMS workflows in a click with Send.', 'hello-elementor' ),
				],
				'button' => __( 'Install', 'hello-elementor' ),
				'buttonBgColor' => '#524CFF',
				'width' => 72,
				'height' => 'auto',
			];
		} elseif (
			! defined( 'ELEMENTOR_AI_VERSION' ) &&
			Utils::is_elementor_installed()
		) {
			$action_links_data[] = [
				'type' => 'go-ai',
				'image' => HELLO_THEME_IMAGES_URL . 'ai.png',
				'url' => 'https://go.elementor.com/hello-site-planner',
				'alt' => __( 'Elementor AI', 'hello-elementor' ),
				'title' => __( 'Elementor AI', 'hello-elementor' ),
				'messages' => [
					__( 'Boost creativity with Elementor AI. Craft & enhance copy, create custom CSS & Code, and generate images to elevate your website.', 'hello-elementor' ),
				],
				'button' => __( 'Let\'s Go', 'hello-elementor' ),
			];
		}

		return rest_ensure_response( [ 'links' => $action_links_data ] );
	}

	public function register_routes() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'/promotions',
			[
				'methods' => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_promotions' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			]
		);
	}
}
