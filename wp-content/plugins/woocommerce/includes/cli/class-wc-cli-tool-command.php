<?php
/**
 * WC_CLI_Tool_Command class file.
 *
 * @package WooCommerce\CLI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hooks up our system status tools to the CLI.
 *
 * Forked from wp-cli/restful (by Daniel Bachhuber, released under the MIT license https://opensource.org/licenses/MIT).
 * https://github.com/wp-cli/restful
 *
 * @version 3.0.0
 * @package WooCommerce
 */
class WC_CLI_Tool_Command {

	/**
	 * Registers just a 'list' and 'run' command to the WC CLI
	 * since we only want to enable certain actions on the system status
	 * tools endpoints.
	 */
	public static function register_commands() {
		global $wp_rest_server;

		$request       = new WP_REST_Request( 'OPTIONS', '/wc/v2/system_status/tools' );
		$response      = $wp_rest_server->dispatch( $request );
		$response_data = $response->get_data();
		if ( empty( $response_data ) ) {
			return;
		}

		$parent             = 'wc tool';
		$supported_commands = array( 'list', 'run' );
		foreach ( $supported_commands as $command ) {
			$synopsis = array();
			if ( 'run' === $command ) {
				$synopsis[] = array(
					'name'        => 'id',
					'type'        => 'positional',
					'description' => __( 'The id for the resource.', 'woocommerce' ),
					'optional'    => false,
				);
				$method     = 'update_item';
				$route      = '/wc/v2/system_status/tools/(?P<id>[\w-]+)';
			} elseif ( 'list' === $command ) {
				$synopsis[] = array(
					'name'        => 'fields',
					'type'        => 'assoc',
					'description' => __( 'Limit response to specific fields. Defaults to all fields.', 'woocommerce' ),
					'optional'    => true,
				);
				$synopsis[] = array(
					'name'        => 'field',
					'type'        => 'assoc',
					'description' => __( 'Get the value of an individual field.', 'woocommerce' ),
					'optional'    => true,
				);
				$synopsis[] = array(
					'name'        => 'format',
					'type'        => 'assoc',
					'description' => __( 'Render response in a particular format.', 'woocommerce' ),
					'optional'    => true,
					'default'     => 'table',
					'options'     => array(
						'table',
						'json',
						'csv',
						'ids',
						'yaml',
						'count',
						'headers',
						'body',
						'envelope',
					),
				);
				$method     = 'list_items';
				$route      = '/wc/v2/system_status/tools';
			}

			$before_invoke = null;
			if ( empty( $command_args['when'] ) && WP_CLI::get_config( 'debug' ) ) {
				$before_invoke = function() {
					wc_maybe_define_constant( 'SAVEQUERIES', true );
				};
			}

			$rest_command = new WC_CLI_REST_Command( 'system_status_tool', $route, $response_data['schema'] );

			WP_CLI::add_command(
				"{$parent} {$command}",
				array( $rest_command, $method ),
				array(
					'synopsis'      => $synopsis,
					'when'          => ! empty( $command_args['when'] ) ? $command_args['when'] : '',
					'before_invoke' => $before_invoke,
				)
			);
		}
	}

}
