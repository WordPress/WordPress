<?php
namespace Elementor\Core\Experiments;

use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Wp_Cli extends \WP_CLI_Command {

	/**
	 * Activate an Experiment
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp elementor experiments activate container
	 *      - This will activate the Container experiment.
	 *
	 * @param array      $args
	 * @param array|null $assoc_args - Arguments from WP CLI command.
	 */
	public function activate( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( 'Please specify an experiment.' );
		}

		$is_network = $this->is_network( $assoc_args );

		$experiments = $this->parse_experiments( $args[0] );
		$plural = $this->get_plural( $experiments );
		$success = 'Experiment' . $plural . ' activated successfully';
		$error = 'Cannot activate experiment' . $plural;

		if ( $is_network ) {
			$success .= " for site {$site}";
			$error .= " for site {$site}";
		}

		$experiments_manager = Plugin::instance()->experiments;
		if ( ! $this->check_experiments_exist( $experiments_manager, $experiments ) ) {
			\WP_CLI::error( 'Experiments do not exist' . $args[0] );
		}

		if ( $is_network ) {
			$this->foreach_sites( $this->update_experiment_state, $experiments, Experiments_Manager::STATE_ACTIVE, $is_network, $success, $error );
		} else {
			$this->update_experiment_state( $experiments, Experiments_Manager::STATE_ACTIVE, $is_network, $success, $error );
		}
	}

	/**
	 * Deactivate an Experiment
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp elementor experiments deactivate container
	 *      - This will deactivate the Container experiment.
	 *
	 * @param array      $args
	 * @param array|null $assoc_args - Arguments from WP CLI command.
	 */
	public function deactivate( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( 'Please specify an experiment.' );
		}

		$is_network = $this->is_network( $assoc_args );

		$experiments = $this->parse_experiments( $args[0] );
		$plural = $this->get_plural( $experiments );
		$success = 'Experiment' . $plural . ' deactivated successfully';
		$error = 'Cannot deactivate experiment' . $plural;

		$experiments_manager = Plugin::instance()->experiments;
		if ( ! $this->check_experiments_exist( $experiments_manager, $experiments ) ) {
			\WP_CLI::error( 'Experiments do not exist' );
		}

		if ( $is_network ) {
			$this->foreach_sites( $this->update_experiment_state, $experiments, Experiments_Manager::STATE_INACTIVE, $is_network, $success, $error );
		} else {
			$this->update_experiment_state( $experiments, Experiments_Manager::STATE_INACTIVE, $is_network, $success, $error );
		}
	}

	/**
	 * Experiment Status
	 *
	 * ## EXAMPLES
	 *
	 * 1. wp elementor experiments status container
	 *      - This will return the status of Container experiment. (active/inactive)
	 *
	 * @param array $args
	 */
	public function status( $args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( 'Please specify an experiment.' );
		}

		$experiments_manager = Plugin::$instance->experiments;
		$experiments_status = $experiments_manager->is_feature_active( $args[0] ) ? 'active' : 'inactive';

		\WP_CLI::line( $experiments_status );
	}

	/**
	 * Determine if the current website is a multisite.
	 *
	 * @param array|null $assoc_args - Arguments from WP CLI command.
	 *
	 * @return bool
	 */
	private function is_network( $assoc_args ) {
		return ! empty( $assoc_args['network'] ) && is_multisite();
	}

	/**
	 * Iterate over network sites and execute a callback.
	 *
	 * @param callable $callback - Callback to execute. Gets the site name & id as parameters.
	 *
	 * @return void
	 */
	private function foreach_sites( callable $callback, $experiments, $state, $is_network, $success, $error ) {
		$blog_ids = get_sites( [
			'fields' => 'ids',
			'number' => 0,
		] );

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );

			$callback( get_option( 'home' ), $experiments, $state, $is_network, $success, $error );

			restore_current_blog();
		}
	}

	/**
	 * @param string $experiments_str comma delimited string of experiments.
	 *
	 * @return array array of experiments
	 */
	private function parse_experiments( $experiments_str ) {
		return explode( ',', $experiments_str );
	}

	/**
	 * @param array $experiments experiments.
	 *
	 * @return string plural
	 */
	private function get_plural( $experiments ) {
		return count( $experiments ) > 0 ? 's' : '';
	}

	/**
	 * @param Experiments_Manager $experiments_manager manager.
	 * @param array               $experiments experiments.
	 *
	 * @return bool true when all experiments exist, otherwise false
	 */
	private function check_experiments_exist( $experiments_manager, $experiments ) {
		foreach ( $experiments as $experiment ) {
			$feature = $experiments_manager->get_features( $experiment );
			if ( ! $feature ) {
				return false;
			}
		}
		return true;
	}

	private function update_experiment_state( $experiments, $state, $is_network, $success_message, $error_message, $site_id = '' ) {
		if ( $is_network ) {
			$success_message .= " for site {$site}";
			$error_message .= " for site {$site}";
		}

		$experiments_manager = Plugin::instance()->experiments;
		foreach ( $experiments as $experiment ) {
			$option = $experiments_manager->get_feature_option_key( $experiment );
			update_option( $option, $state );
		}

		try {
			\WP_CLI::success( $success_message );
		} catch ( \Exception $e ) {
			\WP_CLI::error( $error_message );
		}
	}
}
