<?php
namespace Elementor\Core\Admin\Menu;

use Elementor\Core\Base\Base_Object;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base extends Base_Object {

	private $args;

	private $options;

	private $submenus = [];

	abstract protected function get_init_args();

	public function __construct() {
		$this->init_args();

		$this->init_options();

		add_action( 'admin_menu', function() {
			$this->register();
		} );

		if ( $this->options['separator'] ) {
			add_action( 'admin_menu', function() {
				$this->add_menu_separator();
			} );

			add_filter( 'custom_menu_order', '__return_true' );

			add_filter( 'menu_order', function( $menu_order ) {
				return $this->rearrange_menu_separator( $menu_order );
			} );
		}
	}

	public function get_args( $arg = null ) {
		return self::get_items( $this->args, $arg );
	}

	public function add_submenu( $submenu_args ) {
		$default_submenu_args = [
			'page_title' => '',
			'capability' => $this->args['capability'],
			'function' => null,
			'index' => null,
		];

		$this->submenus[] = array_merge( $default_submenu_args, $submenu_args );
	}

	protected function get_init_options() {
		return [];
	}

	protected function register_default_submenus() {}

	protected function register() {
		$args = $this->args;

		add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position'] );

		$this->register_default_submenus();

		do_action( 'elementor/admin/menu_registered/' . $args['menu_slug'], $this );

		usort( $this->submenus, function( $a, $b ) {
			return $a['index'] - $b['index'];
		} );

		foreach ( $this->submenus as $index => $submenu_item ) {
			$submenu_args = [
				$args['menu_slug'],
				$submenu_item['page_title'],
				$submenu_item['menu_title'],
				$submenu_item['capability'],
				$submenu_item['menu_slug'],
				$submenu_item['function'],
			];

			if ( 0 === $submenu_item['index'] ) {
				$submenu_args[] = 0;
			}

			add_submenu_page( ...$submenu_args );

			if ( ! empty( $submenu_item['class'] ) ) {
				global $submenu;

				$submenu[ $args['menu_slug'] ][ $index + 1 ][4] = $submenu_item['class']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}
	}

	private function init_args() {
		$default_args = [
			'function' => null,
			'icon_url' => null,
			'position' => null,
		];

		$this->args = array_merge( $default_args, $this->get_init_args() );
	}

	private function init_options() {
		$default_options = [
			'separator' => false,
		];

		$this->options = array_merge( $default_options, $this->get_init_options() );
	}

	private function add_menu_separator() {
		global $menu;

		$slug = $this->args['menu_slug'];

		$menu[] = [ '', 'read', 'separator-' . $slug, '', 'wp-menu-separator ' . $slug ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}

	private function rearrange_menu_separator( $menu_order ) {
		// Initialize our custom order array.
		$custom_menu_order = [];

		$slug = $this->args['menu_slug'];

		$separator_name = 'separator-' . $slug;

		// Get the index of our custom separator.
		$custom_separator = array_search( $separator_name, $menu_order, true );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $item ) {
			if ( $slug === $item ) {
				$custom_menu_order[] = $separator_name;
				$custom_menu_order[] = $item;

				unset( $menu_order[ $custom_separator ] );
			} elseif ( $separator_name !== $item ) {
				$custom_menu_order[] = $item;
			}
		}

		// Return order.
		return $custom_menu_order;
	}
}
