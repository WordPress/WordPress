<?php
/**
 * Class that handles conditional logic related to users.
 *
 * @package WPCode
 */

/**
 * The WPCode_Conditional_User class.
 */
class WPCode_Conditional_User extends WPCode_Conditional_Type {

	/**
	 * The type unique name (slug).
	 *
	 * @var string
	 */
	public $name = 'user';

	/**
	 * Set the translatable label.
	 *
	 * @return void
	 */
	protected function set_label() {
		$this->label = __( 'User', 'insert-headers-and-footers' );
	}

	/**
	 * Set the type options for the admin mainly.
	 *
	 * @return void
	 */
	public function load_type_options() {
		$this->options = array(
			'logged_in' => array(
				'label'    => __( 'Logged-in', 'insert-headers-and-footers' ),
				'type'     => 'select',
				'options'  => array(
					array(
						'label' => __( 'True', 'insert-headers-and-footers' ),
						'value' => true,
					),
					array(
						'label' => __( 'False', 'insert-headers-and-footers' ),
						'value' => false,
					),
				),
				'callback' => 'is_user_logged_in',
			),
			'user_role' => array(
				'label'    => __( 'User Role', 'insert-headers-and-footers' ),
				'type'     => 'select',
				'options'  => $this->get_options_user_roles(),
				'callback' => array( $this, 'get_user_role' ),
			),
		);
	}

	/**
	 * Get a list of options for user roles.
	 *
	 * @return array
	 */
	protected function get_options_user_roles() {
		$user_roles = wp_roles()->roles;
		$options    = array();
		foreach ( $user_roles as $key => $role ) {
			$options[] = array(
				'label' => $role['name'],
				'value' => $key,
			);
		}

		return $options;
	}

	/**
	 * Get an array of user roles for the current user.
	 *
	 * @return string[]
	 */
	public function get_user_role() {
		$user = wp_get_current_user();

		return $user->roles;
	}
}

new WPCode_Conditional_User();
