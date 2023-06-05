<?php
namespace Automattic\WooCommerce\Blocks\Integrations;

/**
 * Class used for tracking registered integrations with various Block types.
 */
class IntegrationRegistry {
	/**
	 * Integration identifier is used to construct hook names and is given when the integration registry is initialized.
	 *
	 * @var string
	 */
	protected $registry_identifier = '';

	/**
	 * Registered integrations, as `$name => $instance` pairs.
	 *
	 * @var IntegrationInterface[]
	 */
	protected $registered_integrations = [];

	/**
	 * Initializes all registered integrations.
	 *
	 * Integration identifier is used to construct hook names and is given when the integration registry is initialized.
	 *
	 * @param string $registry_identifier Identifier for this registry.
	 */
	public function initialize( $registry_identifier = '' ) {
		if ( $registry_identifier ) {
			$this->registry_identifier = $registry_identifier;
		}

		if ( empty( $this->registry_identifier ) ) {
			_doing_it_wrong( __METHOD__, esc_html__( 'Integration registry requires an identifier.', 'woocommerce' ), '4.6.0' );
			return false;
		}

		/**
		 * Fires when the IntegrationRegistry is initialized.
		 *
		 * Runs before integrations are initialized allowing new integration to be registered for use. This should be
		 * used as the primary hook for integrations to include their scripts, styles, and other code extending the
		 * blocks.
		 *
		 * @since 4.6.0
		 *
		 * @param IntegrationRegistry $this Instance of the IntegrationRegistry class which exposes the IntegrationRegistry::register() method.
		 */
		do_action( 'woocommerce_blocks_' . $this->registry_identifier . '_registration', $this );

		foreach ( $this->get_all_registered() as $registered_integration ) {
			$registered_integration->initialize();
		}
	}

	/**
	 * Registers an integration.
	 *
	 * @param IntegrationInterface $integration An instance of IntegrationInterface.
	 *
	 * @return boolean True means registered successfully.
	 */
	public function register( IntegrationInterface $integration ) {
		$name = $integration->get_name();

		if ( $this->is_registered( $name ) ) {
			/* translators: %s: Integration name. */
			_doing_it_wrong( __METHOD__, esc_html( sprintf( __( '"%s" is already registered.', 'woocommerce' ), $name ) ), '4.6.0' );
			return false;
		}

		$this->registered_integrations[ $name ] = $integration;
		return true;
	}

	/**
	 * Checks if an integration is already registered.
	 *
	 * @param string $name Integration name.
	 * @return bool True if the integration is registered, false otherwise.
	 */
	public function is_registered( $name ) {
		return isset( $this->registered_integrations[ $name ] );
	}

	/**
	 * Un-register an integration.
	 *
	 * @param string|IntegrationInterface $name Integration name, or alternatively a IntegrationInterface instance.
	 * @return boolean|IntegrationInterface Returns the unregistered integration instance if unregistered successfully.
	 */
	public function unregister( $name ) {
		if ( $name instanceof IntegrationInterface ) {
			$name = $name->get_name();
		}

		if ( ! $this->is_registered( $name ) ) {
			/* translators: %s: Integration name. */
			_doing_it_wrong( __METHOD__, esc_html( sprintf( __( 'Integration "%s" is not registered.', 'woocommerce' ), $name ) ), '4.6.0' );
			return false;
		}

		$unregistered = $this->registered_integrations[ $name ];
		unset( $this->registered_integrations[ $name ] );
		return $unregistered;
	}

	/**
	 * Retrieves a registered Integration by name.
	 *
	 * @param string $name Integration name.
	 * @return IntegrationInterface|null The registered integration, or null if it is not registered.
	 */
	public function get_registered( $name ) {
		return $this->is_registered( $name ) ? $this->registered_integrations[ $name ] : null;
	}

	/**
	 * Retrieves all registered integrations.
	 *
	 * @return IntegrationInterface[]
	 */
	public function get_all_registered() {
		return $this->registered_integrations;
	}

	/**
	 * Gets an array of all registered integration's script handles for the editor.
	 *
	 * @return string[]
	 */
	public function get_all_registered_editor_script_handles() {
		$script_handles          = [];
		$registered_integrations = $this->get_all_registered();

		foreach ( $registered_integrations as $registered_integration ) {
			$script_handles = array_merge(
				$script_handles,
				$registered_integration->get_editor_script_handles()
			);
		}

		return array_unique( array_filter( $script_handles ) );
	}

	/**
	 * Gets an array of all registered integration's script handles.
	 *
	 * @return string[]
	 */
	public function get_all_registered_script_handles() {
		$script_handles          = [];
		$registered_integrations = $this->get_all_registered();

		foreach ( $registered_integrations as $registered_integration ) {
			$script_handles = array_merge(
				$script_handles,
				$registered_integration->get_script_handles()
			);
		}

		return array_unique( array_filter( $script_handles ) );
	}

	/**
	 * Gets an array of all registered integration's script data.
	 *
	 * @return array
	 */
	public function get_all_registered_script_data() {
		$script_data             = [];
		$registered_integrations = $this->get_all_registered();

		foreach ( $registered_integrations as $registered_integration ) {
			$script_data[ $registered_integration->get_name() . '_data' ] = $registered_integration->get_script_data();
		}

		return array_filter( $script_data );
	}
}
