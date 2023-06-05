<?php
namespace Automattic\WooCommerce\StoreApi\Schemas;

use Automattic\WooCommerce\StoreApi\Schemas\V1\CartItemSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\ProductSchema;
use Automattic\WooCommerce\StoreApi\Formatters;

/**
 * Provides utility functions to extend Store API schemas.
 *
 * Note there are also helpers that map to these methods.
 *
 * @see woocommerce_store_api_register_endpoint_data()
 * @see woocommerce_store_api_register_update_callback()
 * @see woocommerce_store_api_register_payment_requirements()
 * @see woocommerce_store_api_get_formatter()
 */
final class ExtendSchema {
	/**
	 * List of Store API schema that is allowed to be extended by extensions.
	 *
	 * @var string[]
	 */
	private $endpoints = [
		CartItemSchema::IDENTIFIER,
		CartSchema::IDENTIFIER,
		CheckoutSchema::IDENTIFIER,
		ProductSchema::IDENTIFIER,
	];

	/**
	 * Holds the formatters class instance.
	 *
	 * @var Formatters
	 */
	private $formatters;

	/**
	 * Data to be extended
	 *
	 * @var array
	 */
	private $extend_data = [];

	/**
	 * Data to be extended
	 *
	 * @var array
	 */
	private $callback_methods = [];

	/**
	 * Array of payment requirements
	 *
	 * @var array
	 */
	private $payment_requirements = [];

	/**
	 * Constructor
	 *
	 * @param Formatters $formatters An instance of the formatters class.
	 */
	public function __construct( Formatters $formatters ) {
		$this->formatters = $formatters;
	}

	/**
	 * Register endpoint data under a specified namespace
	 *
	 * @param array $args {
	 *     An array of elements that make up a post to update or insert.
	 *
	 *     @type string   $endpoint Required. The endpoint to extend.
	 *     @type string   $namespace Required. Plugin namespace.
	 *     @type callable $schema_callback Callback executed to add schema data.
	 *     @type callable $data_callback Callback executed to add endpoint data.
	 *     @type string   $schema_type The type of data, object or array.
	 * }
	 *
	 * @throws \Exception On failure to register.
	 */
	public function register_endpoint_data( $args ) {
		$args = wp_parse_args(
			$args,
			[
				'endpoint'        => '',
				'namespace'       => '',
				'schema_callback' => null,
				'data_callback'   => null,
				'schema_type'     => ARRAY_A,
			]
		);

		if ( ! is_string( $args['namespace'] ) || empty( $args['namespace'] ) ) {
			$this->throw_exception( 'You must provide a plugin namespace when extending a Store REST endpoint.' );
		}

		if ( ! in_array( $args['endpoint'], $this->endpoints, true ) ) {
			$this->throw_exception(
				sprintf( 'You must provide a valid Store REST endpoint to extend, valid endpoints are: %1$s. You provided %2$s.', implode( ', ', $this->endpoints ), $args['endpoint'] )
			);
		}

		if ( ! is_null( $args['schema_callback'] ) && ! is_callable( $args['schema_callback'] ) ) {
			$this->throw_exception( '$schema_callback must be a callable function.' );
		}

		if ( ! is_null( $args['data_callback'] ) && ! is_callable( $args['data_callback'] ) ) {
			$this->throw_exception( '$data_callback must be a callable function.' );
		}

		if ( ! in_array( $args['schema_type'], [ ARRAY_N, ARRAY_A ], true ) ) {
			$this->throw_exception(
				sprintf( 'Data type must be either ARRAY_N for a numeric array or ARRAY_A for an object like array. You provided %1$s.', $args['schema_type'] )
			);
		}

		$this->extend_data[ $args['endpoint'] ][ $args['namespace'] ] = [
			'schema_callback' => $args['schema_callback'],
			'data_callback'   => $args['data_callback'],
			'schema_type'     => $args['schema_type'],
		];
	}

	/**
	 * Add callback functions that can be executed by the cart/extensions endpoint.
	 *
	 * @param array $args {
	 *     An array of elements that make up the callback configuration.
	 *
	 *     @type string   $namespace Required. Plugin namespace.
	 *     @type callable $callback Required. The function/callable to execute.
	 * }
	 *
	 * @throws \Exception On failure to register.
	 */
	public function register_update_callback( $args ) {
		$args = wp_parse_args(
			$args,
			[
				'namespace' => '',
				'callback'  => null,
			]
		);

		if ( ! is_string( $args['namespace'] ) || empty( $args['namespace'] ) ) {
			throw new \Exception( 'You must provide a plugin namespace when extending a Store REST endpoint.' );
		}

		if ( ! is_callable( $args['callback'] ) ) {
			throw new \Exception( 'There is no valid callback supplied to register_update_callback.' );
		}

		$this->callback_methods[ $args['namespace'] ] = $args;
	}

	/**
	 * Registers and validates payment requirements callbacks.
	 *
	 * @param array $args {
	 *     Array of registration data.
	 *
	 *     @type callable $data_callback Required. Callback executed to add payment requirements data.
	 * }
	 *
	 * @throws \Exception On failure to register.
	 */
	public function register_payment_requirements( $args ) {
		if ( empty( $args['data_callback'] ) || ! is_callable( $args['data_callback'] ) ) {
			$this->throw_exception( '$data_callback must be a callable function.' );
		}
		$this->payment_requirements[] = $args['data_callback'];
	}

	/**
	 * Returns a formatter instance.
	 *
	 * @param string $name Formatter name.
	 * @return FormatterInterface
	 */
	public function get_formatter( $name ) {
		return $this->formatters->$name;
	}

	/**
	 * Get callback for a specific endpoint and namespace.
	 *
	 * @param string $namespace The namespace to get callbacks for.
	 *
	 * @return callable The callback registered by the extension.
	 * @throws \Exception When callback is not callable or parameters are incorrect.
	 */
	public function get_update_callback( $namespace ) {
		if ( ! is_string( $namespace ) ) {
			throw new \Exception( 'You must provide a plugin namespace when extending a Store REST endpoint.' );
		}

		if ( ! array_key_exists( $namespace, $this->callback_methods ) ) {
			throw new \Exception( sprintf( 'There is no such namespace registered: %1$s.', $namespace ) );
		}

		if ( ! array_key_exists( 'callback', $this->callback_methods[ $namespace ] ) || ! is_callable( $this->callback_methods[ $namespace ]['callback'] ) ) {
			throw new \Exception( sprintf( 'There is no valid callback registered for: %1$s.', $namespace ) );
		}

		return $this->callback_methods[ $namespace ]['callback'];
	}

	/**
	 * Returns the registered endpoint data
	 *
	 * @param string $endpoint    A valid identifier.
	 * @param array  $passed_args Passed arguments from the Schema class.
	 * @return object Returns an casted object with registered endpoint data.
	 * @throws \Exception If a registered callback throws an error, or silently logs it.
	 */
	public function get_endpoint_data( $endpoint, array $passed_args = [] ) {
		$registered_data = [];

		if ( isset( $this->extend_data[ $endpoint ] ) ) {
			foreach ( $this->extend_data[ $endpoint ] as $namespace => $callbacks ) {
				if ( is_null( $callbacks['data_callback'] ) ) {
					continue;
				}
				try {
					$data = $callbacks['data_callback']( ...$passed_args );

					if ( ! is_array( $data ) ) {
						$data = [];
						throw new \Exception( '$data_callback must return an array.' );
					}
				} catch ( \Throwable $e ) {
					$this->throw_exception( $e );
				}

				$registered_data[ $namespace ] = $data;
			}
		}

		return (object) $registered_data;
	}

	/**
	 * Returns the registered endpoint schema
	 *
	 * @param string $endpoint    A valid identifier.
	 * @param array  $passed_args Passed arguments from the Schema class.
	 * @return object Returns an array with registered schema data.
	 * @throws \Exception If a registered callback throws an error, or silently logs it.
	 */
	public function get_endpoint_schema( $endpoint, array $passed_args = [] ) {
		$registered_schema = [];

		if ( isset( $this->extend_data[ $endpoint ] ) ) {
			foreach ( $this->extend_data[ $endpoint ] as $namespace => $callbacks ) {
				if ( is_null( $callbacks['schema_callback'] ) ) {
					continue;
				}
				try {
					$schema = $callbacks['schema_callback']( ...$passed_args );

					if ( ! is_array( $schema ) ) {
						$schema = [];
						throw new \Exception( '$schema_callback must return an array.' );
					}
				} catch ( \Throwable $e ) {
					$this->throw_exception( $e );
				}

				$registered_schema[ $namespace ] = $this->format_extensions_properties( $namespace, $schema, $callbacks['schema_type'] );
			}
		}

		return (object) $registered_schema;
	}

	/**
	 * Returns the additional payment requirements for the cart which are required to make payments. Values listed here
	 * are compared against each Payment Gateways "supports" flag.
	 *
	 * @param array $requirements list of requirements that should be added to the collected requirements.
	 * @return array Returns a list of payment requirements.
	 * @throws \Exception If a registered callback throws an error, or silently logs it.
	 */
	public function get_payment_requirements( array $requirements = [ 'products' ] ) {
		if ( ! empty( $this->payment_requirements ) ) {
			foreach ( $this->payment_requirements as $callback ) {
				try {
					$data = $callback();

					if ( ! is_array( $data ) ) {
						throw new \Exception( '$data_callback must return an array.' );
					}

					$requirements = array_unique( array_merge( $requirements, $data ) );
				} catch ( \Throwable $e ) {
					$this->throw_exception( $e );
				}
			}
		}
		return $requirements;
	}

	/**
	 * Throws error and/or silently logs it.
	 *
	 * @param string|\Throwable $exception_or_error Error message or \Exception.
	 * @throws \Exception An error to throw if we have debug enabled and user is admin.
	 */
	private function throw_exception( $exception_or_error ) {
		$exception = is_string( $exception_or_error ) ? new \Exception( $exception_or_error ) : $exception_or_error;

		wc_caught_exception( $exception );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_woocommerce' ) ) {
			throw $exception;
		}
	}

	/**
	 * Format schema for an extension.
	 *
	 * @param string $namespace Error message or \Exception.
	 * @param array  $schema An error to throw if we have debug enabled and user is admin.
	 * @param string $schema_type How should data be shaped.
	 * @return array Formatted schema.
	 */
	private function format_extensions_properties( $namespace, $schema, $schema_type ) {
		if ( ARRAY_N === $schema_type ) {
			return [
				/* translators: %s: extension namespace */
				'description' => sprintf( __( 'Extension data registered by %s', 'woocommerce' ), $namespace ),
				'type'        => 'array',
				'context'     => [ 'view', 'edit' ],
				'items'       => $schema,
			];
		}
		return [
			/* translators: %s: extension namespace */
			'description' => sprintf( __( 'Extension data registered by %s', 'woocommerce' ), $namespace ),
			'type'        => 'object',
			'context'     => [ 'view', 'edit' ],
			'properties'  => $schema,
		];
	}
}
