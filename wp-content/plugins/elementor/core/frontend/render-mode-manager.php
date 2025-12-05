<?php
namespace Elementor\Core\Frontend;

use Elementor\Core\Frontend\RenderModes\Render_Mode_Base;
use Elementor\Core\Frontend\RenderModes\Render_Mode_Normal;
use Elementor\Modules\CloudLibrary\Render_Mode_Preview;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Render_Mode_Manager {
	const QUERY_STRING_PARAM_NAME = 'render_mode';
	const QUERY_STRING_POST_ID = 'post_id';

	const QUERY_STRING_TEMPLATE_ID = 'template_id';

	const QUERY_STRING_NONCE_PARAM_NAME = 'render_mode_nonce';
	const NONCE_ACTION_PATTERN = 'render_mode_{post_id}';

	/**
	 * @var Render_Mode_Base
	 */
	private $current;

	/**
	 * @var Render_Mode_Base[]
	 */
	private $render_modes = [];

	/**
	 * @param $post_id
	 * @param $render_mode_name
	 *
	 * @return string
	 */
	public static function get_base_url( $post_id, $render_mode_name ) {
		return add_query_arg( [
			self::QUERY_STRING_POST_ID => $post_id,
			self::QUERY_STRING_PARAM_NAME => $render_mode_name,
			self::QUERY_STRING_NONCE_PARAM_NAME => wp_create_nonce( self::get_nonce_action( $post_id ) ),
			'ver' => time(),
		], get_permalink( $post_id ) );
	}

	/**
	 * @param $post_id
	 *
	 * @return string
	 */
	public static function get_nonce_action( $post_id ) {
		return str_replace( '{post_id}', $post_id, self::NONCE_ACTION_PATTERN );
	}

	/**
	 * Register a new render mode into the render mode manager.
	 *
	 * @param $class_name
	 *
	 * @return $this
	 * @throws \Exception If the class does not extend Render_Mode_Base.
	 */
	public function register_render_mode( $class_name ) {
		if ( ! is_subclass_of( $class_name, Render_Mode_Base::class ) ) {
			throw new \Exception( sprintf( "'%s' must extend 'Render_Mode_Base'.", esc_html( $class_name ) ) );
		}

		$this->render_modes[ $class_name::get_name() ] = $class_name;

		return $this;
	}

	/**
	 * Get the current render mode.
	 *
	 * @return Render_Mode_Base
	 */
	public function get_current() {
		return $this->current;
	}

	/**
	 * @param Render_Mode_Base $render_mode
	 *
	 * @return $this
	 */
	private function set_current( Render_Mode_Base $render_mode ) {
		$this->current = $render_mode;

		return $this;
	}

	/**
	 * Set render mode.
	 *
	 * @return $this
	 */
	private function choose_render_mode() {
		$post_id = null;
		$template_id = null;
		$key = null;
		$nonce = null;
		$kit_preview = null;

		if ( isset( $_GET[ self::QUERY_STRING_POST_ID ] ) ) {
			$post_id = $_GET[ self::QUERY_STRING_POST_ID ]; // phpcs:ignore -- Nonce will be checked next line.
		}

		if ( isset( $_GET[ self::QUERY_STRING_NONCE_PARAM_NAME ] ) ) {
			$nonce = $_GET[ self::QUERY_STRING_NONCE_PARAM_NAME ]; // phpcs:ignore -- Nonce will be checked next line.
		}

		if ( isset( $_GET[ self::QUERY_STRING_PARAM_NAME ] ) ) {
			$key = $_GET[ self::QUERY_STRING_PARAM_NAME ]; // phpcs:ignore -- Nonce will be checked next line.
		}

		if ( isset( $_GET[ self::QUERY_STRING_TEMPLATE_ID ] ) ) {
			$template_id = $_GET[ self::QUERY_STRING_TEMPLATE_ID ]; // phpcs:ignore -- Nonce will be checked next line.
		}

		if (
			$post_id &&
			$nonce &&
			wp_verify_nonce( $nonce, self::get_nonce_action( $post_id ) ) &&
			$key &&
			array_key_exists( $key, $this->render_modes )
		) {
			$this->set_current( new $this->render_modes[ $key ]( $post_id ) );
		} elseif ( $this->is_template_preview_mode( $template_id, $key, $nonce ) ) {
			$this->set_current( new $this->render_modes[ $key ]( $template_id ) );
		} else {
			$this->set_current( new Render_Mode_Normal( $post_id ) );
		}

		return $this;
	}

	private function is_template_preview_mode( $template_id, $render_mode, $nonce ) {
		if ( empty( $template_id ) ) {
			return false;
		}

		if ( Render_Mode_Preview::MODE !== $render_mode ) {
			return false;
		}

		if ( ! array_key_exists( $render_mode, $this->render_modes ) ) {
			return false;
		}

		if ( ! wp_verify_nonce( $nonce, self::get_nonce_action( $template_id ) ) ) {
			wp_die( esc_html__( 'Not Authorized', 'elementor' ), esc_html__( 'Error', 'elementor' ), 403 );
		}

		return true;
	}

	/**
	 * Add actions base on the current render.
	 *
	 * @throws \Requests_Exception_HTTP_403 If the current render mode does not have the required permissions.
	 * @throws \WpOrg\Requests\Exception\Http\Status403 If the current render mode does not have the required permissions.
	 */
	private function add_current_actions() {
		if ( ! $this->current->get_permissions_callback() ) {
			// WP >= 6.2-alpha
			if ( class_exists( '\WpOrg\Requests\Exception\Http\Status403' ) ) {
				throw new \WpOrg\Requests\Exception\Http\Status403();
			} else {
				throw new \Requests_Exception_HTTP_403();
			}
		}

		// Run when 'template-redirect' actually because the the class is instantiate when 'template-redirect' run.
		$this->current->prepare_render();
	}

	/**
	 * Render_Mode_Manager constructor.
	 *
	 * @throws \Exception If the render mode registration fails.
	 */
	public function __construct() {
		$this->register_render_mode( Render_Mode_Normal::class );

		do_action( 'elementor/frontend/render_mode/register', $this );

		$this->choose_render_mode();
		$this->add_current_actions();
	}
}
