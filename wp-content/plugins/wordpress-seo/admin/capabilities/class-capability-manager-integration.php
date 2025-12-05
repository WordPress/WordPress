<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Capabilities
 */

/**
 * Integrates Yoast SEO capabilities with third party role manager plugins.
 *
 * Integrates with: Members
 * Integrates with: User Role Editor
 */
class WPSEO_Capability_Manager_Integration implements WPSEO_WordPress_Integration {

	/**
	 * Capability manager to use.
	 *
	 * @var WPSEO_Capability_Manager
	 */
	public $manager;

	/**
	 * WPSEO_Capability_Manager_Integration constructor.
	 *
	 * @param WPSEO_Capability_Manager $manager The capability manager to use.
	 */
	public function __construct( WPSEO_Capability_Manager $manager ) {
		$this->manager = $manager;
	}

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'members_get_capabilities', [ $this, 'get_capabilities' ] );
		add_action( 'members_register_cap_groups', [ $this, 'action_members_register_cap_group' ] );

		add_filter( 'ure_capabilities_groups_tree', [ $this, 'filter_ure_capabilities_groups_tree' ] );
		add_filter( 'ure_custom_capability_groups', [ $this, 'filter_ure_custom_capability_groups' ], 10, 2 );
	}

	/**
	 * Get the Yoast SEO capabilities.
	 * Optionally append them to an existing array.
	 *
	 * @param  array $caps Optional existing capability list.
	 * @return array
	 */
	public function get_capabilities( array $caps = [] ) {
		if ( ! did_action( 'wpseo_register_capabilities' ) ) {
			do_action( 'wpseo_register_capabilities' );
		}

		return array_merge( $caps, $this->manager->get_capabilities() );
	}

	/**
	 * Add capabilities to its own group in the Members plugin.
	 *
	 * @see members_register_cap_group()
	 *
	 * @return void
	 */
	public function action_members_register_cap_group() {
		if ( ! function_exists( 'members_register_cap_group' ) ) {
			return;
		}

		// Register the yoast group.
		$args = [
			'label'      => esc_html__( 'Yoast SEO', 'wordpress-seo' ),
			'caps'       => $this->get_capabilities(),
			'icon'       => 'dashicons-admin-plugins',
			'diff_added' => true,
		];
		members_register_cap_group( 'wordpress-seo', $args );
	}

	/**
	 * Adds Yoast SEO capability group in the User Role Editor plugin.
	 *
	 * @see URE_Capabilities_Groups_Manager::get_groups_tree()
	 *
	 * @param array $groups Current groups.
	 *
	 * @return array Filtered list of capabilty groups.
	 */
	public function filter_ure_capabilities_groups_tree( $groups = [] ) {
		$groups = (array) $groups;

		$groups['wordpress-seo'] = [
			'caption' => 'Yoast SEO',
			'parent'  => 'custom',
			'level'   => 3,
		];

		return $groups;
	}

	/**
	 * Adds capabilities to the Yoast SEO group in the User Role Editor plugin.
	 *
	 * @see URE_Capabilities_Groups_Manager::get_cap_groups()
	 *
	 * @param array  $groups Current capability groups.
	 * @param string $cap_id Capability identifier.
	 *
	 * @return array List of filtered groups.
	 */
	public function filter_ure_custom_capability_groups( $groups = [], $cap_id = '' ) {
		if ( in_array( $cap_id, $this->get_capabilities(), true ) ) {
			$groups   = (array) $groups;
			$groups[] = 'wordpress-seo';
		}

		return $groups;
	}
}
