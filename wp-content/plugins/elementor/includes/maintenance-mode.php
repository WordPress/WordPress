<?php
namespace Elementor;

use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor maintenance mode.
 *
 * Elementor maintenance mode handler class is responsible for the Elementor
 * "Maintenance Mode" and the "Coming Soon" features.
 *
 * @since 1.4.0
 */
class Maintenance_Mode {

	/**
	 * The options prefix.
	 */
	const OPTION_PREFIX = 'elementor_maintenance_mode_';

	/**
	 * The maintenance mode.
	 */
	const MODE_MAINTENANCE = 'maintenance';

	/**
	 * The coming soon mode.
	 */
	const MODE_COMING_SOON = 'coming_soon';

	/**
	 * Get elementor option.
	 *
	 * Retrieve elementor option from the database.
	 *
	 * @since 1.4.0
	 * @access public
	 * @static
	 *
	 * @param string $option        Option name. Expected to not be SQL-escaped.
	 * @param mixed  $default_value Optional. Default value to return if the option
	 *                              does not exist. Default is false.
	 *
	 * @return bool False if value was not updated and true if value was updated.
	 */
	public static function get( $option, $default_value = false ) {
		return get_option( self::OPTION_PREFIX . $option, $default_value );
	}

	/**
	 * Set elementor option.
	 *
	 * Update elementor option in the database.
	 *
	 * @since 1.4.0
	 * @access public
	 * @static
	 *
	 * @param string $option Option name. Expected to not be SQL-escaped.
	 * @param mixed  $value  Option value. Must be serializable if non-scalar.
	 *                       Expected to not be SQL-escaped.
	 *
	 * @return bool False if value was not updated and true if value was updated.
	 */
	public static function set( $option, $value ) {
		return update_option( self::OPTION_PREFIX . $option, $value );
	}

	/**
	 * Body class.
	 *
	 * Add "Maintenance Mode" CSS classes to the body tag.
	 *
	 * Fired by `body_class` filter.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param array $classes An array of body classes.
	 *
	 * @return array An array of body classes.
	 */
	public function body_class( $classes ) {
		$classes[] = 'elementor-maintenance-mode';

		return $classes;
	}

	/**
	 * Template redirect.
	 *
	 * Redirect to the "Maintenance Mode" template.
	 *
	 * Fired by `template_redirect` action.
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function template_redirect() {
		if ( Plugin::$instance->preview->is_preview_mode() ) {
			return;
		}

		$user = wp_get_current_user();

		$exclude_mode = self::get( 'exclude_mode', [] );

		$is_login_page = false;

		/**
		 * Is login page
		 *
		 * Filters whether the maintenance mode displaying the login page or a regular page.
		 *
		 * @since 1.0.4
		 *
		 * @param bool $is_login_page Whether its a login page.
		 */
		$is_login_page = apply_filters( 'elementor/maintenance_mode/is_login_page', $is_login_page );

		if ( $is_login_page ) {
			return;
		}

		if ( 'logged_in' === $exclude_mode && is_user_logged_in() ) {
			return;
		}

		if ( 'custom' === $exclude_mode ) {
			$exclude_roles = self::get( 'exclude_roles', [] );
			$user_roles = $user->roles;

			if ( is_multisite() && is_super_admin() ) {
				$user_roles[] = 'super_admin';
			}

			$compare_roles = array_intersect( $user_roles, $exclude_roles );

			if ( ! empty( $compare_roles ) ) {
				return;
			}
		}

		add_filter( 'body_class', [ $this, 'body_class' ] );

		if ( 'maintenance' === self::get( 'mode' ) ) {
			$protocol = wp_get_server_protocol();
			header( "$protocol 503 Service Unavailable", true, 503 );
			header( 'Content-Type: text/html; charset=utf-8' );
			header( 'Retry-After: 600' );
		}

		// Setup global post for Elementor\frontend so `_has_elementor_in_page = true`.
		$GLOBALS['post'] = get_post( self::get( 'template_id' ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		// Set the template as `$wp_query->current_object` for `wp_title` and etc.
		query_posts( [
			'p' => self::get( 'template_id' ),
			'post_type' => Source_Local::CPT,
		] );
	}

	/**
	 * Register settings fields.
	 *
	 * Adds new "Maintenance Mode" settings fields to Elementor admin page.
	 *
	 * The method need to receive the an instance of the Tools settings page
	 * to add the new maintenance mode functionality.
	 *
	 * Fired by `elementor/admin/after_create_settings/{$page_id}` action.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param Tools $tools An instance of the Tools settings page.
	 */
	public function register_settings_fields( Tools $tools ) {
		$templates = Plugin::$instance->templates_manager->get_source( 'local' )->get_items( [
			'type' => 'page',
		] );

		$templates_options = [];

		foreach ( $templates as $template ) {
			$templates_options[ $template['template_id'] ] = esc_html( $template['title'] );
		}

		ob_start();

		$this->print_template_description();

		$template_description = ob_get_clean();

		$tools->add_tab(
			'maintenance_mode', [
				'label' => esc_html__( 'Maintenance Mode', 'elementor' ),
				'sections' => [
					'maintenance_mode' => [
						'callback' => function() {
							echo '<h2>' . esc_html__( 'Maintenance Mode', 'elementor' ) . '</h2>';
							echo '<p>' . esc_html__( 'Set your entire website as MAINTENANCE MODE, meaning the site is offline temporarily for maintenance, or set it as COMING SOON mode, meaning the site is offline until it is ready to be launched.', 'elementor' ) . '</p>';
						},
						'fields' => [
							'maintenance_mode_mode' => [
								'label' => esc_html__( 'Choose Mode', 'elementor' ),
								'field_args' => [
									'type' => 'select',
									'std' => '',
									'options' => [
										'' => esc_html__( 'Disabled', 'elementor' ),
										self::MODE_COMING_SOON => esc_html__( 'Coming Soon', 'elementor' ),
										self::MODE_MAINTENANCE => esc_html__( 'Maintenance', 'elementor' ),
									],
									'desc' => '<div class="elementor-maintenance-mode-description" data-value="" style="display: none">' .
												esc_html__( 'Choose between Coming Soon mode (returning HTTP 200 code) or Maintenance Mode (returning HTTP 503 code).', 'elementor' ) .
												'</div>' .
												'<div class="elementor-maintenance-mode-description" data-value="maintenance" style="display: none">' .
												esc_html__( 'Maintenance Mode returns HTTP 503 code, so search engines know to come back a short time later. It is not recommended to use this mode for more than a couple of days.', 'elementor' ) .
												'</div>' .
												'<div class="elementor-maintenance-mode-description" data-value="coming_soon" style="display: none">' .
												esc_html__( 'Coming Soon returns HTTP 200 code, meaning the site is ready to be indexed.', 'elementor' ) .
												'</div>',
								],
							],
							'maintenance_mode_exclude_mode' => [
								'label' => esc_html__( 'Who Can Access', 'elementor' ),
								'field_args' => [
									'class' => 'elementor-default-hide',
									'type' => 'select',
									'std' => 'logged_in',
									'options' => [
										'logged_in' => esc_html__( 'Logged In', 'elementor' ),
										'custom' => esc_html__( 'Custom', 'elementor' ),
									],
								],
							],
							'maintenance_mode_exclude_roles' => [
								'label' => esc_html__( 'Roles', 'elementor' ),
								'field_args' => [
									'class' => 'elementor-default-hide',
									'type' => 'checkbox_list_roles',
								],
								'setting_args' => [ __NAMESPACE__ . '\Settings_Validations', 'checkbox_list' ],
							],
							'maintenance_mode_template_id' => [
								'label' => esc_html__( 'Choose Template', 'elementor' ),
								'field_args' => [
									'class' => 'elementor-default-hide',
									'type' => 'select',
									'std' => '',
									'show_select' => true,
									'options' => $templates_options,
									'desc' => $template_description,
								],
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Add menu in admin bar.
	 *
	 * Adds "Maintenance Mode" items to the WordPress admin bar.
	 *
	 * Fired by `admin_bar_menu` filter.
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 */
	public function add_menu_in_admin_bar( \WP_Admin_Bar $wp_admin_bar ) {
		$wp_admin_bar->add_node( [
			'id' => 'elementor-maintenance-on',
			'title' => esc_html__( 'Maintenance Mode ON', 'elementor' ),
			'href' => Tools::get_url() . '#tab-maintenance_mode',
		] );

		$document = Plugin::$instance->documents->get( self::get( 'template_id' ) );

		$wp_admin_bar->add_node( [
			'id' => 'elementor-maintenance-edit',
			'parent' => 'elementor-maintenance-on',
			'title' => esc_html__( 'Edit Template', 'elementor' ),
			'href' => $document ? $document->get_edit_url() : '',
		] );
	}

	/**
	 * Print style.
	 *
	 * Adds custom CSS to the HEAD html tag. The CSS that emphasise the maintenance
	 * mode with red colors.
	 *
	 * Fired by `admin_head` and `wp_head` filters.
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function print_style() {
		?>
		<style>#wp-admin-bar-elementor-maintenance-on > a { background-color: #dc3232; }
			#wp-admin-bar-elementor-maintenance-on > .ab-item:before { content: "\f160"; top: 2px; }</style>
		<?php
	}

	public function on_update_mode( $old_value, $value ) {
		if ( $old_value !== $value ) {
			do_action( 'elementor/maintenance_mode/mode_changed', $old_value, $value );
		}
	}

	/**
	 * Maintenance mode constructor.
	 *
	 * Initializing Elementor maintenance mode.
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'update_option_elementor_maintenance_mode_mode', [ $this, 'on_update_mode' ], 10, 2 );

		$is_enabled = (bool) self::get( 'mode' ) && (bool) self::get( 'template_id' );

		if ( is_admin() ) {
			$page_id = Tools::PAGE_ID;
			add_action( "elementor/admin/after_create_settings/{$page_id}", [ $this, 'register_settings_fields' ] );
		}

		if ( ! $is_enabled ) {
			return;
		}

		add_action( 'admin_bar_menu', [ $this, 'add_menu_in_admin_bar' ], 300 );
		add_action( 'admin_head', [ $this, 'print_style' ] );
		add_action( 'wp_head', [ $this, 'print_style' ] );

		// Priority = 11 that is *after* WP default filter `redirect_canonical` in order to avoid redirection loop.
		add_action( 'template_redirect', [ $this, 'template_redirect' ], 11 );
	}

	/**
	 * Print Template Description
	 *
	 * Prints the template description
	 *
	 * @since 2.2.0
	 * @access private
	 */
	private function print_template_description() {
		$template_id = self::get( 'template_id' );

		$edit_url = '';

		if ( $template_id && get_post( $template_id ) ) {
			$edit_url = Plugin::$instance->documents->get( $template_id )->get_edit_url();
		}

		?>
		<a target="_blank" class="elementor-edit-template" style="display: none" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html__( 'Edit Template', 'elementor' ); ?></a>
		<div class="elementor-maintenance-mode-error"><?php echo esc_html__( 'To enable maintenance mode you have to set a template for the maintenance mode page.', 'elementor' ); ?></div>
		<div class="elementor-maintenance-mode-error">
			<?php
				printf(
					/* translators: %1$s Link open tag, %2$s: Link close tag. */
					esc_html__( 'Select one or go ahead and %1$screate one%2$s now.', 'elementor' ),
					'<a target="_blank" href="' . esc_url( admin_url( 'post-new.php?post_type=' . Source_Local::CPT ) ) . '">',
					'</a>'
				);
			?>
		</div>
		<?php
	}
}
