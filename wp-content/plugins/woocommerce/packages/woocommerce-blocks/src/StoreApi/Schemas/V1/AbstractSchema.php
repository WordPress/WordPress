<?php
namespace Automattic\WooCommerce\StoreApi\Schemas\V1;

use Automattic\WooCommerce\StoreApi\SchemaController;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;

/**
 * AbstractSchema class.
 *
 * For REST Route Schemas
 */
abstract class AbstractSchema {
	/**
	 * The schema item name.
	 *
	 * @var string
	 */
	protected $title = 'Schema';

	/**
	 * Rest extend instance.
	 *
	 * @var ExtendSchema
	 */
	protected $extend;

	/**
	 * Schema Controller instance.
	 *
	 * @var SchemaController
	 */
	protected $controller;

	/**
	 * Extending key that gets added to endpoint.
	 *
	 * @var string
	 */
	const EXTENDING_KEY = 'extensions';

	/**
	 * Constructor.
	 *
	 * @param ExtendSchema     $extend Rest Extending instance.
	 * @param SchemaController $controller Schema Controller instance.
	 */
	public function __construct( ExtendSchema $extend, SchemaController $controller ) {
		$this->extend     = $extend;
		$this->controller = $controller;
	}

	/**
	 * Returns the full item schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->title,
			'type'       => 'object',
			'properties' => $this->get_properties(),
		);
	}

	/**
	 * Return schema properties.
	 *
	 * @return array
	 */
	abstract public function get_properties();

	/**
	 * Recursive removal of arg_options.
	 *
	 * @param array $properties Schema properties.
	 */
	protected function remove_arg_options( $properties ) {
		return array_map(
			function( $property ) {
				if ( isset( $property['properties'] ) ) {
					$property['properties'] = $this->remove_arg_options( $property['properties'] );
				} elseif ( isset( $property['items']['properties'] ) ) {
					$property['items']['properties'] = $this->remove_arg_options( $property['items']['properties'] );
				}
				unset( $property['arg_options'] );
				return $property;
			},
			(array) $properties
		);
	}

	/**
	 * Returns the public schema.
	 *
	 * @return array
	 */
	public function get_public_item_schema() {
		$schema = $this->get_item_schema();

		if ( isset( $schema['properties'] ) ) {
			$schema['properties'] = $this->remove_arg_options( $schema['properties'] );
		}

		return $schema;
	}

	/**
	 * Returns extended data for a specific endpoint.
	 *
	 * @param string $endpoint The endpoint identifier.
	 * @param array  ...$passed_args An array of arguments to be passed to callbacks.
	 * @return object the data that will get added.
	 */
	protected function get_extended_data( $endpoint, ...$passed_args ) {
		return $this->extend->get_endpoint_data( $endpoint, $passed_args );
	}

	/**
	 * Gets an array of schema defaults recursively.
	 *
	 * @param array $properties Schema property data.
	 * @return array Array of defaults, pulled from arg_options
	 */
	protected function get_recursive_schema_property_defaults( $properties ) {
		$defaults = [];

		foreach ( $properties as $property_key => $property_value ) {
			if ( isset( $property_value['arg_options']['default'] ) ) {
				$defaults[ $property_key ] = $property_value['arg_options']['default'];
			} elseif ( isset( $property_value['properties'] ) ) {
				$defaults[ $property_key ] = $this->get_recursive_schema_property_defaults( $property_value['properties'] );
			}
		}

		return $defaults;
	}

	/**
	 * Gets a function that validates recursively.
	 *
	 * @param array $properties Schema property data.
	 * @return function Anonymous validation callback.
	 */
	protected function get_recursive_validate_callback( $properties ) {
		/**
		 * Validate a request argument based on details registered to the route.
		 *
		 * @param mixed            $values
		 * @param \WP_REST_Request $request
		 * @param string           $param
		 * @return true|\WP_Error
		 */
		return function ( $values, $request, $param ) use ( $properties ) {
			foreach ( $properties as $property_key => $property_value ) {
				$current_value = isset( $values[ $property_key ] ) ? $values[ $property_key ] : null;

				if ( isset( $property_value['arg_options']['validate_callback'] ) ) {
					$callback = $property_value['arg_options']['validate_callback'];
					$result   = is_callable( $callback ) ? $callback( $current_value, $request, $param ) : false;
				} else {
					$result = rest_validate_value_from_schema( $current_value, $property_value, $param . ' > ' . $property_key );
				}

				if ( ! $result || is_wp_error( $result ) ) {
					return $result;
				}

				if ( isset( $property_value['properties'] ) ) {
					$validate_callback = $this->get_recursive_validate_callback( $property_value['properties'] );
					return $validate_callback( $current_value, $request, $param . ' > ' . $property_key );
				}
			}

			return true;
		};
	}

	/**
	 * Gets a function that sanitizes recursively.
	 *
	 * @param array $properties Schema property data.
	 * @return function Anonymous validation callback.
	 */
	protected function get_recursive_sanitize_callback( $properties ) {
		/**
		 * Validate a request argument based on details registered to the route.
		 *
		 * @param mixed            $values
		 * @param \WP_REST_Request $request
		 * @param string           $param
		 * @return true|\WP_Error
		 */
		return function ( $values, $request, $param ) use ( $properties ) {
			$sanitized_values = [];

			foreach ( $properties as $property_key => $property_value ) {
				$current_value = isset( $values[ $property_key ] ) ? $values[ $property_key ] : null;

				if ( isset( $property_value['arg_options']['sanitize_callback'] ) ) {
					$callback      = $property_value['arg_options']['sanitize_callback'];
					$current_value = is_callable( $callback ) ? $callback( $current_value, $request, $param ) : $current_value;
				} else {
					$current_value = rest_sanitize_value_from_schema( $current_value, $property_value, $param . ' > ' . $property_key );
				}

				// If sanitization failed, return the WP_Error object straight away.
				if ( is_wp_error( $current_value ) ) {
					return $current_value;
				}

				if ( isset( $property_value['properties'] ) ) {
					$sanitize_callback                 = $this->get_recursive_sanitize_callback( $property_value['properties'] );
					$sanitized_values[ $property_key ] = $sanitize_callback( $current_value, $request, $param . ' > ' . $property_key );
				} else {
					$sanitized_values[ $property_key ] = $current_value;
				}
			}

			return $sanitized_values;
		};
	}

	/**
	 * Returns extended schema for a specific endpoint.
	 *
	 * @param string $endpoint The endpoint identifer.
	 * @param array  ...$passed_args An array of arguments to be passed to callbacks.
	 * @return array the data that will get added.
	 */
	protected function get_extended_schema( $endpoint, ...$passed_args ) {
		$extended_schema = $this->extend->get_endpoint_schema( $endpoint, $passed_args );
		$defaults        = $this->get_recursive_schema_property_defaults( $extended_schema );

		return [
			'type'        => 'object',
			'context'     => [ 'view', 'edit' ],
			'arg_options' => [
				'default'           => $defaults,
				'validate_callback' => $this->get_recursive_validate_callback( $extended_schema ),
				'sanitize_callback' => $this->get_recursive_sanitize_callback( $extended_schema ),
			],
			'properties'  => $extended_schema,
		];
	}

	/**
	 * Apply a schema get_item_response callback to an array of items and return the result.
	 *
	 * @param AbstractSchema $schema Schema class instance.
	 * @param array          $items Array of items.
	 * @return array Array of values from the callback function.
	 */
	protected function get_item_responses_from_schema( AbstractSchema $schema, $items ) {
		$items = array_filter( $items );

		if ( empty( $items ) ) {
			return [];
		}

		return array_values( array_map( [ $schema, 'get_item_response' ], $items ) );
	}

	/**
	 * Retrieves an array of endpoint arguments from the item schema for the controller.
	 *
	 * @uses rest_get_endpoint_args_for_schema()
	 * @param string $method Optional. HTTP method of the request.
	 * @return array Endpoint arguments.
	 */
	public function get_endpoint_args_for_item_schema( $method = \WP_REST_Server::CREATABLE ) {
		$schema        = $this->get_item_schema();
		$endpoint_args = rest_get_endpoint_args_for_schema( $schema, $method );
		$endpoint_args = $this->remove_arg_options( $endpoint_args );
		return $endpoint_args;
	}


	/**
	 * Force all schema properties to be readonly.
	 *
	 * @param array $properties Schema.
	 * @return array Updated schema.
	 */
	protected function force_schema_readonly( $properties ) {
		return array_map(
			function( $property ) {
				$property['readonly'] = true;
				if ( isset( $property['items']['properties'] ) ) {
					$property['items']['properties'] = $this->force_schema_readonly( $property['items']['properties'] );
				}
				return $property;
			},
			(array) $properties
		);
	}

	/**
	 * Returns consistent currency schema used across endpoints for prices.
	 *
	 * @return array
	 */
	protected function get_store_currency_properties() {
		return [
			'currency_code'               => [
				'description' => __( 'Currency code (in ISO format) for returned prices.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'currency_symbol'             => [
				'description' => __( 'Currency symbol for the currency which can be used to format returned prices.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'currency_minor_unit'         => [
				'description' => __( 'Currency minor unit (number of digits after the decimal separator) for returned prices.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => [ 'view', 'edit' ],
				'readonly'    => true,
			],
			'currency_decimal_separator'  => array(
				'description' => __( 'Decimal separator for the currency which can be used to format returned prices.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'currency_thousand_separator' => array(
				'description' => __( 'Thousand separator for the currency which can be used to format returned prices.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'currency_prefix'             => array(
				'description' => __( 'Price prefix for the currency which can be used to format returned prices.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'currency_suffix'             => array(
				'description' => __( 'Price prefix for the currency which can be used to format returned prices.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		];
	}

	/**
	 * Adds currency data to an array of monetary values.
	 *
	 * @param array $values Monetary amounts.
	 * @return array Monetary amounts with currency data appended.
	 */
	protected function prepare_currency_response( $values ) {
		return $this->extend->get_formatter( 'currency' )->format( $values );
	}

	/**
	 * Convert monetary values from WooCommerce to string based integers, using
	 * the smallest unit of a currency.
	 *
	 * @param string|float $amount Monetary amount with decimals.
	 * @param int          $decimals Number of decimals the amount is formatted with.
	 * @param int          $rounding_mode Defaults to the PHP_ROUND_HALF_UP constant.
	 * @return string      The new amount.
	 */
	protected function prepare_money_response( $amount, $decimals = 2, $rounding_mode = PHP_ROUND_HALF_UP ) {
		return $this->extend->get_formatter( 'money' )->format(
			$amount,
			[
				'decimals'      => $decimals,
				'rounding_mode' => $rounding_mode,
			]
		);
	}

	/**
	 * Prepares HTML based content, such as post titles and content, for the API response.
	 *
	 * @param string|array $response Data to format.
	 * @return string|array Formatted data.
	 */
	protected function prepare_html_response( $response ) {
		return $this->extend->get_formatter( 'html' )->format( $response );
	}
}
