<?php
namespace Elementor\Core\RoleManager;

use Elementor\Core\Admin\Menu\Admin_Menu_Manager;
use Elementor\Core\Utils\Promotions\Filtered_Promotions_Manager;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Settings_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Role_Manager extends Settings_Page {

	const PAGE_ID = 'elementor-role-manager';

	const ROLE_MANAGER_OPTION_NAME = 'exclude_user_roles';

	const ROLE_MANAGER_ADVANCED = 'role-manager';

	private static $advanced_options = [];

	/**
	 * @since 2.0.0
	 * @access public
	 */
	public function get_role_manager_options() {
		return get_option( 'elementor_' . self::ROLE_MANAGER_OPTION_NAME, [] );
	}

	public function get_role_manager_advanced_options() {
		return get_option( 'elementor_' . self::ROLE_MANAGER_ADVANCED, [] );
	}

	public function get_user_advanced_options() {
		if ( ! empty( static::$advanced_options ) ) {
			return static::$advanced_options;
		}

		static::$advanced_options = $this->get_role_manager_advanced_options();
		return static::$advanced_options;
	}

	/**
	 * @since 2.0.0
	 * @access protected
	 */
	protected function get_page_title() {
		return esc_html__( 'Role Manager', 'elementor' );
	}

	/**
	 * @since 2.0.0
	 * @access public
	 */
	public function register_admin_menu( Admin_Menu_Manager $admin_menu ) {
		$admin_menu->register( static::PAGE_ID, new Role_Manager_Menu_Item( $this ) );
	}

	/**
	 * @since 2.0.0
	 * @access protected
	 */
	protected function create_tabs() {
		$validation_class = 'Elementor\Settings_Validations';
		return [
			'general' => [
				'label' => esc_html__( 'General', 'elementor' ),
				'sections' => [
					'tools' => [
						'fields' => [
							'exclude_user_roles' => [
								'label' => esc_html__( 'Exclude Roles', 'elementor' ),
								'field_args' => [
									'type' => 'checkbox_list_roles',
									'exclude' => [ 'super_admin', 'administrator' ],
								],
								'setting_args' => [
									'sanitize_callback' => [ $validation_class, 'checkbox_list' ],
								],
							],
							self::ROLE_MANAGER_ADVANCED => [
								'field_args' => [
									'type' => 'raw_html',
									'html' => '',
								],
								'setting_args' => [
									'sanitize_callback' => [ $this, 'save_advanced_options' ],
								],
							],
						],
					],
				],
			],
		];
	}

	public function save_advanced_options( $input ) {
		return $input;
	}

	/**
	 * @since 2.0.0
	 * @access public
	 */
	public function display_settings_page() {
		$this->get_tabs();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( $this->get_page_title() ); ?></h1>

			<div id="elementor-role-manager">
				<h3><?php echo esc_html__( 'Manage What Your Users Can Edit In Elementor', 'elementor' ); ?></h3>
				<form id="elementor-settings-form" method="post" action="options.php">
					<?php
					settings_fields( static::PAGE_ID );
					echo '<div class="elementor-settings-form-page elementor-active">';
					foreach ( get_editable_roles() as $role_slug => $role_data ) {
						if ( 'administrator' === $role_slug ) {
							continue;
						}
						$this->display_role_controls( $role_slug, $role_data );
					}
					submit_button( __( 'Save Changes', 'elementor' ), 'primary', 'submit', true, [ 'data-id' => 'elementor-role-manager-button-save-changes' ] );
					?>
				</form>
			</div>
		</div><!-- /.wrap -->
		<?php
	}

	/**
	 * @since 2.0.0
	 * @access private
	 *
	 * @param string $role_slug The role slug.
	 * @param array  $role_data An array with role data.
	 */
	private function display_role_controls( $role_slug, $role_data ) {
		static $excluded_options = false;
		if ( false === $excluded_options ) {
			$excluded_options = $this->get_role_manager_options();
		}

		?>
		<div class="elementor-role-row <?php echo esc_attr( $role_slug ); ?>">
			<div class="elementor-role-label">
				<span class="elementor-role-name"><?php echo esc_html( translate_user_role( $role_data['name'] ) ); ?></span>
				<span data-excluded-label="<?php esc_attr_e( 'Role Excluded', 'elementor' ); ?>" class="elementor-role-excluded-indicator"></span>
				<span class="elementor-role-toggle"><span class="dashicons dashicons-arrow-down"></span></span>
			</div>
			<div class="elementor-role-controls hidden">
				<div class="elementor-role-control">
					<label>
						<input type="checkbox" name="elementor_exclude_user_roles[]" value="<?php echo esc_attr( $role_slug ); ?>"<?php checked( in_array( $role_slug, $excluded_options, true ), true ); ?>>
						<?php echo esc_html__( 'No access to editor', 'elementor' ); ?>
					</label>
				</div>
				<div class="elementor-role-controls-advanced">
					<?php
					/**
					 * Role restrictions controls.
					 *
					 * Fires after the role manager checkbox that allows the user to
					 * exclude the role.
					 *
					 * This filter allows developers to add custom controls to the role
					 * manager.
					 *
					 * @since 2.0.0
					 *
					 * @param string $role_slug The role slug.
					 * @param array  $role_data An array with role data.
					 */
					do_action( 'elementor/role/restrictions/controls', $role_slug, $role_data );
					?>
				</div>
			</div>
		</div>
		<?php
	}

	public function add_json_enable_control( $role_slug ) {
		$value = 'json-upload';
		$id = self::ROLE_MANAGER_ADVANCED . '_' . $role_slug . '_' . $value;
		$name = 'elementor_' . self::ROLE_MANAGER_ADVANCED . '[' . $role_slug . '][]';

		$advanced_options = $this->get_user_advanced_options();
		$checked = isset( $advanced_options[ $role_slug ] ) ? $advanced_options[ $role_slug ] : [];
		?>
		<div class="elementor-role-control">
			<label for="<?php echo esc_attr( $id ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $checked ), true ); ?>>
				<?php echo esc_html__( 'Enable the option to upload JSON files', 'elementor' ); ?>
			</label>
			<p class="elementor-role-control-warning"><strong><?php echo esc_html__( 'Heads up', 'elementor' ); ?>:</strong> <?php echo esc_html__( 'Giving broad access to upload JSON files can pose a security risk to your website because such files may contain malicious scripts, etc.', 'elementor' ); ?></p>
		</div>
		<?php
	}

	public function add_custom_html_enable_control( $role_slug ) {
		$value = 'custom-html';
		$id = self::ROLE_MANAGER_ADVANCED . '_' . $role_slug . '_' . $value;
		$name = 'elementor_' . self::ROLE_MANAGER_ADVANCED . '[' . $role_slug . '][]';

		$advanced_options = $this->get_user_advanced_options();
		$checked = isset( $advanced_options[ $role_slug ] ) ? $advanced_options[ $role_slug ] : [];
		?>
		<div class="elementor-role-control">
			<label for="<?php echo esc_attr( $id ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $checked ), true ); ?>>
				<?php echo esc_html__( 'Enable the option to use the HTML widget', 'elementor' ); ?>
			</label>
			<p class="elementor-role-control-warning"><strong><?php echo esc_html__( 'Heads up', 'elementor' ); ?>:</strong> <?php echo esc_html__( 'Giving broad access to edit the HTML widget can pose a security risk to your website because it enables users to run malicious scripts, etc.', 'elementor' ); ?></p>
		</div>
		<?php
	}

	/**
	 * @since 2.0.0
	 * @access public
	 */
	public function get_go_pro_link_html() {
		$promotion = $this->get_go_pro_link_content();

		?>
		<div class="elementor-role-go-pro">
			<div class="elementor-role-go-pro__desc"><?php echo esc_html( $promotion['description'] ); ?></div>
			<div class="elementor-role-go-pro__link"><a class="elementor-button go-pro" target="_blank" href="<?php echo esc_url( $promotion['upgrade_url'] ); ?>"><?php echo esc_html( $promotion['upgrade_text'] ); ?></a></div>
		</div>
		<?php
	}

	public function get_go_pro_link_content() {
		$upgrade_url = 'https://go.elementor.com/go-pro-role-manager/';

		$promotion = [
			'description' => esc_html__( 'Want to give access only to content?', 'elementor' ),
			'upgrade_url' => esc_url( $upgrade_url ),
			'upgrade_text' => esc_html__( 'Upgrade', 'elementor' ),
		];

		return Filtered_Promotions_Manager::get_filtered_promotion_data( $promotion, 'elementor/role/custom_promotion', 'upgrade_url' );
	}

	/**
	 * @since 2.0.0
	 * @access public
	 */
	public function get_user_restrictions_array() {
		$user = wp_get_current_user();
		$user_roles = $user->roles;
		$options = $this->get_user_restrictions();
		$restrictions = [];
		if ( empty( $options ) ) {
			return $restrictions;
		}

		foreach ( $user_roles as $role ) {
			if ( ! isset( $options[ $role ] ) ) {
				continue;
			}
			$restrictions = array_merge( $restrictions, $options[ $role ] );
		}
		return array_unique( $restrictions );
	}

	/**
	 * @since 2.0.0
	 * @access private
	 */
	private function get_user_restrictions() {
		static $restrictions = false;
		if ( ! $restrictions ) {
			$restrictions = [];

			/**
			 * Editor user restrictions.
			 *
			 * Filters the user restrictions in the editor.
			 *
			 * @since 2.0.0
			 *
			 * @param array $restrictions User restrictions.
			 */
			$restrictions = apply_filters( 'elementor/editor/user/restrictions', $restrictions );
		}
		return $restrictions;
	}

	/**
	 * @since 2.0.0
	 * @access public
	 *
	 * @param $capability
	 *
	 * @return bool
	 */
	public function user_can( $capability ) {
		$options = $this->get_user_restrictions_array();

		if ( in_array( $capability, $options, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @since 2.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'elementor/admin/menu/register', function ( Admin_Menu_Manager $admin_menu ) {
			$this->register_admin_menu( $admin_menu );
		}, Settings::ADMIN_MENU_PRIORITY + 10 );

		add_action( 'elementor/role/restrictions/controls', [ $this, 'add_json_enable_control' ] );
		add_action( 'elementor/role/restrictions/controls', [ $this, 'add_custom_html_enable_control' ] );
		add_action( 'elementor/role/restrictions/controls', [ $this, 'get_go_pro_link_html' ] );

		add_filter( 'elementor/editor/user/restrictions', [ $this, 'get_role_manager_advanced_options' ] );
	}
}
