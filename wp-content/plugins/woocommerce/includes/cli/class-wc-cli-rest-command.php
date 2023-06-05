<?php
/**
 * WP_CLI_Rest_Command class file.
 *
 * @package WooCommerce\CLI
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Utilities\NumberUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Command for WooCommerce CLI.
 *
 * Since a lot of WC operations can be handled via the REST API, we base our CLI
 * off of Restful to generate commands for each WooCommerce REST API endpoint
 * so most of the logic is shared.
 *
 * Forked from wp-cli/restful (by Daniel Bachhuber, released under the MIT license https://opensource.org/licenses/MIT).
 * https://github.com/wp-cli/restful
 *
 * @version 3.0.0
 * @package WooCommerce
 */
class WC_CLI_REST_Command {
	/**
	 * Endpoints that have a parent ID.
	 * Ex: Product reviews, which has a product ID and a review ID.
	 *
	 * @var array
	 */
	protected $routes_with_parent_id = array(
		'customer_download',
		'product_review',
		'order_note',
		'shop_order_refund',
	);

	/**
	 * Name of command/endpoint object.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Endpoint route.
	 *
	 * @var string
	 */
	private $route;

	/**
	 * Main resource ID.
	 *
	 * @var int
	 */
	private $resource_identifier;

	/**
	 * Schema for command.
	 *
	 * @var array
	 */
	private $schema;

	/**
	 * List of supported IDs and their description (name => desc).
	 *
	 * @var array
	 */
	private $supported_ids = array();

	/**
	 * Sets up REST Command.
	 *
	 * @param string $name   Name of endpoint object (comes from schema).
	 * @param string $route  Path to route of this endpoint.
	 * @param array  $schema Schema object.
	 */
	public function __construct( $name, $route, $schema ) {
		$this->name = $name;

		preg_match_all( '#\([^\)]+\)#', $route, $matches );
		$first_match  = $matches[0];
		$resource_id  = ! empty( $matches[0] ) ? array_pop( $matches[0] ) : null;
		$this->route  = rtrim( $route );
		$this->schema = $schema;

		$this->resource_identifier = $resource_id;
		if ( in_array( $name, $this->routes_with_parent_id, true ) ) {
			$is_singular = substr( $this->route, - strlen( $resource_id ) ) === $resource_id;
			if ( ! $is_singular ) {
				$this->resource_identifier = $first_match[0];
			}
		}
	}

	/**
	 * Passes supported ID arguments (things like product_id, order_id, etc) that we should look for in addition to id.
	 *
	 * @param array $supported_ids List of supported IDs.
	 */
	public function set_supported_ids( $supported_ids = array() ) {
		$this->supported_ids = $supported_ids;
	}

	/**
	 * Returns an ID of supported ID arguments (things like product_id, order_id, etc) that we should look for in addition to id.
	 *
	 * @return array
	 */
	public function get_supported_ids() {
		return $this->supported_ids;
	}

	/**
	 * Create a new item.
	 *
	 * @subcommand create
	 *
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public function create_item( $args, $assoc_args ) {
		$assoc_args            = self::decode_json( $assoc_args );
		list( $status, $body ) = $this->do_request( 'POST', $this->get_filled_route( $args ), $assoc_args );
		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'porcelain' ) ) {
			WP_CLI::line( $body['id'] );
		} else {
			WP_CLI::success( "Created {$this->name} {$body['id']}." );
		}
	}

	/**
	 * Delete an existing item.
	 *
	 * @subcommand delete
	 *
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public function delete_item( $args, $assoc_args ) {
		list( $status, $body ) = $this->do_request( 'DELETE', $this->get_filled_route( $args ), $assoc_args );
		$object_id = isset( $body['id'] ) ? $body['id'] : '';
		if ( ! $object_id && isset( $body['slug'] ) ) {
			$object_id = $body['slug'];
		}

		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'porcelain' ) ) {
			WP_CLI::line( $object_id );
		} else {
			if ( empty( $assoc_args['force'] ) ) {
				WP_CLI::success( __( 'Trashed', 'woocommerce' ) . " {$this->name} {$object_id}" );
			} else {
				WP_CLI::success( __( 'Deleted', 'woocommerce' ) . " {$this->name} {$object_id}." );
			}
		}
	}

	/**
	 * Get a single item.
	 *
	 * @subcommand get
	 *
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public function get_item( $args, $assoc_args ) {
		$route                           = $this->get_filled_route( $args );
		list( $status, $body, $headers ) = $this->do_request( 'GET', $route, $assoc_args );

		if ( ! empty( $assoc_args['fields'] ) ) {
			$body = self::limit_item_to_fields( $body, $assoc_args['fields'] );
		}

		if ( empty( $assoc_args['format'] ) ) {
			$assoc_args['format'] = 'table';
		}

		if ( 'headers' === $assoc_args['format'] ) {
			echo wp_json_encode( $headers );
		} elseif ( 'body' === $assoc_args['format'] ) {
			echo wp_json_encode( $body );
		} elseif ( 'envelope' === $assoc_args['format'] ) {
			echo wp_json_encode(
				array(
					'body'    => $body,
					'headers' => $headers,
					'status'  => $status,
				)
			);
		} else {
			$formatter = $this->get_formatter( $assoc_args );
			$formatter->display_item( $body );
		}
	}

	/**
	 * List all items.
	 *
	 * @subcommand list
	 *
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public function list_items( $args, $assoc_args ) {
		if ( ! empty( $assoc_args['format'] ) && 'count' === $assoc_args['format'] ) {
			$method = 'HEAD';
		} else {
			$method = 'GET';
		}

		if ( ! isset( $assoc_args['per_page'] ) || empty( $assoc_args['per_page'] ) ) {
			$assoc_args['per_page'] = '100';
		}

		list( $status, $body, $headers ) = $this->do_request( $method, $this->get_filled_route( $args ), $assoc_args );
		if ( ! empty( $assoc_args['format'] ) && 'ids' === $assoc_args['format'] ) {
			$items = array_column( $body, 'id' );
		} else {
			$items = $body;
		}

		if ( ! empty( $assoc_args['fields'] ) ) {
			foreach ( $items as $key => $item ) {
				$items[ $key ] = self::limit_item_to_fields( $item, $assoc_args['fields'] );
			}
		}

		if ( empty( $assoc_args['format'] ) ) {
			$assoc_args['format'] = 'table';
		}

		if ( ! empty( $assoc_args['format'] ) && 'count' === $assoc_args['format'] ) {
			echo (int) $headers['X-WP-Total'];
		} elseif ( 'headers' === $assoc_args['format'] ) {
			echo wp_json_encode( $headers );
		} elseif ( 'body' === $assoc_args['format'] ) {
			echo wp_json_encode( $body );
		} elseif ( 'envelope' === $assoc_args['format'] ) {
			echo wp_json_encode(
				array(
					'body'    => $body,
					'headers' => $headers,
					'status'  => $status,
					'api_url' => $this->api_url,
				)
			);
		} else {
			$formatter = $this->get_formatter( $assoc_args );
			$formatter->display_items( $items );
		}
	}

	/**
	 * Update an existing item.
	 *
	 * @subcommand update
	 *
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public function update_item( $args, $assoc_args ) {
		$assoc_args            = self::decode_json( $assoc_args );
		list( $status, $body ) = $this->do_request( 'POST', $this->get_filled_route( $args ), $assoc_args );
		if ( \WP_CLI\Utils\get_flag_value( $assoc_args, 'porcelain' ) ) {
			WP_CLI::line( $body['id'] );
		} else {
			WP_CLI::success( __( 'Updated', 'woocommerce' ) . " {$this->name} {$body['id']}." );
		}
	}

	/**
	 * Do a REST Request
	 *
	 * @param string $method Request method. Examples: 'POST', 'PUT', 'DELETE' or 'GET'.
	 * @param string $route Resource route.
	 * @param array  $assoc_args Associative arguments passed to the originating WP-CLI command.
	 *
	 * @return array
	 */
	private function do_request( $method, $route, $assoc_args ) {
		wc_maybe_define_constant( 'REST_REQUEST', true );

		$request = new WP_REST_Request( $method, $route );
		if ( in_array( $method, array( 'POST', 'PUT' ), true ) ) {
			$request->set_body_params( $assoc_args );
		} else {
			foreach ( $assoc_args as $key => $value ) {
				$request->set_param( $key, $value );
			}
		}
		if ( Constants::is_true( 'SAVEQUERIES' ) ) {
			$original_queries = is_array( $GLOBALS['wpdb']->queries ) ? array_keys( $GLOBALS['wpdb']->queries ) : array();
		}
		$response = rest_do_request( $request );
		if ( Constants::is_true( 'SAVEQUERIES' ) ) {
			$performed_queries = array();
			foreach ( (array) $GLOBALS['wpdb']->queries as $key => $query ) {
				if ( in_array( $key, $original_queries, true ) ) {
					continue;
				}
				$performed_queries[] = $query;
			}
			usort(
				$performed_queries,
				function( $a, $b ) {
					if ( $a[1] === $b[1] ) {
						return 0;
					}
					return ( $a[1] > $b[1] ) ? -1 : 1;
				}
			);

			$query_count      = count( $performed_queries );
			$query_total_time = 0;
			foreach ( $performed_queries as $query ) {
				$query_total_time += $query[1];
			}
			$slow_query_message = '';
			if ( $performed_queries && 'wc' === WP_CLI::get_config( 'debug' ) ) {
				$slow_query_message .= '. Ordered by slowness, the queries are:' . PHP_EOL;
				foreach ( $performed_queries as $i => $query ) {
					$i++;
					$bits                = explode( ', ', $query[2] );
					$backtrace           = implode( ', ', array_slice( $bits, 13 ) );
					$seconds             = NumberUtil::round( $query[1], 6 );
					$slow_query_message .= <<<EOT
{$i}:
- {$seconds} seconds
- {$backtrace}
- {$query[0]}
EOT;
					$slow_query_message .= PHP_EOL;
				}
			} elseif ( 'wc' !== WP_CLI::get_config( 'debug' ) ) {
				$slow_query_message = '. Use --debug=wc to see all queries.';
			}
			$query_total_time = NumberUtil::round( $query_total_time, 6 );
			WP_CLI::debug( "wc command executed {$query_count} queries in {$query_total_time} seconds{$slow_query_message}", 'wc' );
		}

		$error = $response->as_error();

		if ( $error ) {
			// For authentication errors (status 401), include a reminder to set the --user flag.
			// WP_CLI::error will only return the first message from WP_Error, so we will pass a string containing both instead.
			if ( 401 === $response->get_status() ) {
				$errors   = $error->get_error_messages();
				$errors[] = __( 'Make sure to include the --user flag with an account that has permissions for this action.', 'woocommerce' ) . ' {"status":401}';
				$error    = implode( "\n", $errors );
			}
			WP_CLI::error( $error );
		}
		return array( $response->get_status(), $response->get_data(), $response->get_headers() );
	}

	/**
	 * Get Formatter object based on supplied parameters.
	 *
	 * @param array $assoc_args Parameters passed to command. Determines formatting.
	 * @return \WP_CLI\Formatter
	 */
	protected function get_formatter( &$assoc_args ) {
		if ( ! empty( $assoc_args['fields'] ) ) {
			if ( is_string( $assoc_args['fields'] ) ) {
				$fields = explode( ',', $assoc_args['fields'] );
			} else {
				$fields = $assoc_args['fields'];
			}
		} else {
			if ( ! empty( $assoc_args['context'] ) ) {
				$fields = $this->get_context_fields( $assoc_args['context'] );
			} else {
				$fields = $this->get_context_fields( 'view' );
			}
		}
		return new \WP_CLI\Formatter( $assoc_args, $fields );
	}

	/**
	 * Get a list of fields present in a given context
	 *
	 * @param string $context Scope under which the request is made. Determines fields present in response.
	 * @return array
	 */
	private function get_context_fields( $context ) {
		$fields = array();
		foreach ( $this->schema['properties'] as $key => $args ) {
			if ( empty( $args['context'] ) || in_array( $context, $args['context'], true ) ) {
				$fields[] = $key;
			}
		}
		return $fields;
	}

	/**
	 * Get the route for this resource
	 *
	 * @param  array $args Positional arguments passed to the originating WP-CLI command.
	 * @return string
	 */
	private function get_filled_route( $args = array() ) {
		$supported_id_matched = false;
		$route                = $this->route;

		foreach ( $this->get_supported_ids() as $id_name => $id_desc ) {
			if ( 'id' !== $id_name && strpos( $route, '<' . $id_name . '>' ) !== false && ! empty( $args ) ) {
				$route                = str_replace( array( '(?P<' . $id_name . '>[\d]+)', '(?P<' . $id_name . '>\w[\w\s\-]*)' ), $args[0], $route );
				$supported_id_matched = true;
			}
		}

		if ( ! empty( $args ) ) {
			$id_replacement = $supported_id_matched && ! empty( $args[1] ) ? $args[1] : $args[0];
			$route          = str_replace( array( '(?P<id>[\d]+)', '(?P<id>[\w-]+)' ), $id_replacement, $route );
		}

		return rtrim( $route );
	}

	/**
	 * Reduce an item to specific fields.
	 *
	 * @param  array $item Item to reduce.
	 * @param  array $fields Fields to keep.
	 * @return array
	 */
	private static function limit_item_to_fields( $item, $fields ) {
		if ( empty( $fields ) ) {
			return $item;
		}
		if ( is_string( $fields ) ) {
			$fields = explode( ',', $fields );
		}
		foreach ( $item as $i => $field ) {
			if ( ! in_array( $i, $fields, true ) ) {
				unset( $item[ $i ] );
			}
		}
		return $item;
	}

	/**
	 * JSON can be passed in some more complicated objects, like the payment gateway settings array.
	 * This function decodes the json (if present) and tries to get it's value.
	 *
	 * @param array $arr Array that will be scanned for JSON encoded values.
	 *
	 * @return array
	 */
	protected function decode_json( $arr ) {
		foreach ( $arr as $key => $value ) {
			if ( '[' === substr( $value, 0, 1 ) || '{' === substr( $value, 0, 1 ) ) {
				$arr[ $key ] = json_decode( $value, true );
			} else {
				continue;
			}
		}
		return $arr;
	}

}
