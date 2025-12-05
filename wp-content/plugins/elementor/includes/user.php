<?php
namespace Elementor;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor user.
 *
 * Elementor user handler class is responsible for checking if the user can edit
 * with Elementor and displaying different admin notices.
 *
 * @since 1.0.0
 */
class User {

	/**
	 * Holds the admin notices key.
	 *
	 * @var string Admin notices key.
	 */
	const ADMIN_NOTICES_KEY = 'elementor_admin_notices';

	/**
	 * Holds the editor introduction screen key.
	 *
	 * @var string Introduction key.
	 */
	const INTRODUCTION_KEY = 'elementor_introduction';

	/**
	 * Holds the beta tester key.
	 *
	 * @var string Beta tester key.
	 */
	const BETA_TESTER_META_KEY = 'elementor_beta_tester';

	/**
	 * Holds the URL of the Beta Tester Opt-in API.
	 *
	 * @since 1.0.0
	 *
	 * @var string API URL.
	 */
	const BETA_TESTER_API_URL = 'https://my.elementor.com/api/v1/beta_tester/';

	/**
	 * Holds the dismissed editor notices key.
	 *
	 * @since 3.19.0
	 *
	 * @var string Editor notices key.
	 */
	const DISMISSED_EDITOR_NOTICES_KEY = 'elementor_dismissed_editor_notices';

	/**
	 * Init.
	 *
	 * Initialize Elementor user.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() {
		add_action( 'wp_ajax_elementor_set_admin_notice_viewed', [ __CLASS__, 'ajax_set_admin_notice_viewed' ] );
		add_action( 'admin_post_elementor_set_admin_notice_viewed', [ __CLASS__, 'ajax_set_admin_notice_viewed' ] );

		add_action( 'elementor/ajax/register_actions', [ __CLASS__, 'register_ajax_actions' ] );
	}

	/**
	 * @param Ajax $ajax
	 * @since 2.1.0
	 * @access public
	 * @static
	 */
	public static function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'introduction_viewed', [ __CLASS__, 'set_introduction_viewed' ] );
		$ajax->register_ajax_action( 'beta_tester_signup', [ __CLASS__, 'register_as_beta_tester' ] );
		$ajax->register_ajax_action( 'dismissed_editor_notices', [ __CLASS__, 'set_dismissed_editor_notices' ] );
	}

	/**
	 * Is current user can edit.
	 *
	 * Whether the current user can edit the post.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param int $post_id Optional. The post ID. Default is `0`.
	 *
	 * @return bool Whether the current user can edit the post.
	 */
	public static function is_current_user_can_edit( $post_id = 0 ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return false;
		}

		if ( 'trash' === get_post_status( $post->ID ) ) {
			return false;
		}

		if ( ! self::is_current_user_can_edit_post_type( $post->post_type ) ) {
			return false;
		}

		$post_type_object = get_post_type_object( $post->post_type );

		if ( ! isset( $post_type_object->cap->edit_post ) ) {
			return false;
		}

		$edit_cap = $post_type_object->cap->edit_post;
		if ( ! current_user_can( $edit_cap, $post->ID ) ) {
			return false;
		}

		if ( intval( get_option( 'page_for_posts' ) ) === $post->ID ) {
			return false;
		}

		return true;
	}

	/**
	 * Is current user can access elementor.
	 *
	 * Whether the current user role is not excluded by Elementor Settings.
	 *
	 * @since 2.1.7
	 * @access public
	 * @static
	 *
	 * @return bool True if can access, False otherwise.
	 */
	public static function is_current_user_in_editing_black_list() {
		$user = wp_get_current_user();
		$exclude_roles = get_option( 'elementor_exclude_user_roles', [] );

		$compare_roles = array_intersect( $user->roles, $exclude_roles );
		if ( ! empty( $compare_roles ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Is current user can edit post type.
	 *
	 * Whether the current user can edit the given post type.
	 *
	 * @since 1.9.0
	 * @access public
	 * @static
	 *
	 * @param string $post_type the post type slug to check.
	 *
	 * @return bool True if can edit, False otherwise.
	 */
	public static function is_current_user_can_edit_post_type( $post_type ) {
		if ( ! self::is_current_user_in_editing_black_list() ) {
			return false;
		}

		if ( ! Utils::is_post_type_support( $post_type ) ) {
			return false;
		}

		$post_type_object = get_post_type_object( $post_type );

		if ( ! current_user_can( $post_type_object->cap->edit_posts ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get user notices.
	 *
	 * Retrieve the list of notices for the current user.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @return array A list of user notices.
	 */
	public static function get_user_notices() {
		$notices = get_user_meta( get_current_user_id(), self::ADMIN_NOTICES_KEY, true );
		return is_array( $notices ) ? $notices : [];
	}

	/**
	 * Is admin notice viewed.
	 *
	 * Whether the admin notice was viewed by the current user.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param int $notice_id The notice ID.
	 *
	 * @return bool Whether the admin notice was viewed by the user.
	 */
	public static function is_user_notice_viewed( $notice_id ) {
		$notices = self::get_user_notices();

		if ( empty( $notices[ $notice_id ] ) ) {
			return false;
		}

		// BC: Handles old structure ( `[ 'notice_id' => 'true' ]` ).
		if ( 'true' === $notices[ $notice_id ] ) {
			return true;
		}

		return $notices[ $notice_id ]['is_viewed'] ?? false;
	}

	/**
	 * Checks whether the current user is allowed to upload JSON files.
	 *
	 * Note: The 'json-upload' capability is managed by the Role Manager as a part of its blacklist restrictions.
	 * In this context, we are negating the user's permission check to use it as a whitelist, allowing uploads.
	 *
	 * @return bool Whether the current user can upload JSON files.
	 */
	public static function is_current_user_can_upload_json() {
		return current_user_can( 'manage_options' ) || ! Plugin::instance()->role_manager->user_can( 'json-upload' );
	}

	public static function is_current_user_can_use_custom_html() {
		return current_user_can( 'manage_options' ) || ! Plugin::instance()->role_manager->user_can( 'custom-html' );
	}

	/**
	 * Set admin notice as viewed.
	 *
	 * Flag the admin notice as viewed by the current user, using an authenticated ajax request.
	 *
	 * Fired by `wp_ajax_elementor_set_admin_notice_viewed` action.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function ajax_set_admin_notice_viewed() {
		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		$notice_id = Utils::get_super_global_value( $_REQUEST, 'notice_id' );

		if ( ! $notice_id ) {
			wp_die();
		}

		check_admin_referer( 'elementor_set_admin_notice_viewed' );

		self::set_user_notice( $notice_id );

		if ( ! wp_doing_ajax() ) {
			wp_safe_redirect( admin_url() );
			die;
		}

		wp_die();
	}

	/**
	 * @param string $notice_id
	 * @param bool   $is_viewed
	 * @param array  $meta
	 *
	 * @return void
	 */
	public static function set_user_notice( $notice_id, $is_viewed = true, $meta = null ) {
		$notices = self::get_user_notices();

		if ( ! is_array( $meta ) ) {
			$meta = $notices[ $notice_id ]['meta'] ?? [];
		}

		$notices[ $notice_id ] = [
			'is_viewed' => $is_viewed,
			'meta' => $meta,
		];

		update_user_meta( get_current_user_id(), self::ADMIN_NOTICES_KEY, $notices );
	}

	/**
	 * @since 2.1.0
	 * @access public
	 * @static
	 */
	public static function set_introduction_viewed( array $data ) {
		$user_introduction_meta = self::get_introduction_meta();

		$user_introduction_meta[ $data['introductionKey'] ] = true;

		update_user_meta( get_current_user_id(), self::INTRODUCTION_KEY, $user_introduction_meta );
	}

	/**
	 * @throws \Exception If the user cannot install plugins.
	 */
	public static function register_as_beta_tester( array $data ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			throw new \Exception( 'You do not have permission to install plugins.' );
		}

		update_user_meta( get_current_user_id(), self::BETA_TESTER_META_KEY, true );
		$response = wp_safe_remote_post(
			self::BETA_TESTER_API_URL,
			[
				'timeout' => 25,
				'body' => [
					'api_version' => ELEMENTOR_VERSION,
					'site_lang' => get_bloginfo( 'language' ),
					'beta_tester_email' => $data['betaTesterEmail'],
				],
			]
		);

		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 === $response_code ) {
			self::set_introduction_viewed( [
				'introductionKey' => Beta_Testers::BETA_TESTER_SIGNUP,
			] );
		}
	}

	/**
	 * @param string $key
	 *
	 * @return array|mixed|string
	 * @since  2.1.0
	 * @access public
	 * @static
	 */
	public static function get_introduction_meta( $key = '' ) {
		$user_introduction_meta = get_user_meta( get_current_user_id(), self::INTRODUCTION_KEY, true );

		if ( ! $user_introduction_meta ) {
			$user_introduction_meta = [];
		}

		if ( $key ) {
			return empty( $user_introduction_meta[ $key ] ) ? '' : $user_introduction_meta[ $key ];
		}

		return $user_introduction_meta;
	}

	/**
	 * Get a user option with a fallback value.
	 *
	 * @param string $option   Option key.
	 * @param int    $user_id  User ID.
	 * @param mixed  $fallback Default fallback value.
	 *
	 * @return mixed
	 */
	public static function get_user_option_with_default( $option, $user_id, $fallback ) {
		$value = get_user_option( $option, $user_id );

		return ( false === $value ) ? $fallback : $value;
	}

	/**
	 * Get dismissed editor notices.
	 *
	 * Retrieve the list of dismissed editor notices for the current user.
	 *
	 * @since 3.19.0
	 * @access public
	 * @static
	 *
	 * @return array A list of dismissed editor notices.
	 */
	public static function get_dismissed_editor_notices() {
		$notices = get_user_meta( get_current_user_id(), self::DISMISSED_EDITOR_NOTICES_KEY, true );

		return is_array( $notices ) ? $notices : [];
	}

	/**
	 * Set dismissed editor notices for the current user.
	 *
	 * @since 3.19.0
	 * @access public
	 * @static
	 *
	 * @param array $data Editor notices.
	 *
	 * @return void
	 */
	public static function set_dismissed_editor_notices( array $data ) {
		$editor_notices = self::get_dismissed_editor_notices();

		if ( ! in_array( $data['dismissId'], $editor_notices, true ) ) {
			$editor_notices[] = $data['dismissId'];

			update_user_meta( get_current_user_id(), self::DISMISSED_EDITOR_NOTICES_KEY, $editor_notices );
		}
	}
}
