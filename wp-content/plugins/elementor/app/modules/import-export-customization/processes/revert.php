<?php
namespace Elementor\App\Modules\ImportExportCustomization\Processes;

use Elementor\App\Modules\ImportExportCustomization\Module;
use Elementor\App\Modules\ImportExportCustomization\Runners\Revert\Elementor_Content;
use Elementor\App\Modules\ImportExportCustomization\Runners\Revert\Revert_Runner_Base;
use Elementor\App\Modules\ImportExportCustomization\Runners\Revert\Plugins;
use Elementor\App\Modules\ImportExportCustomization\Runners\Revert\Site_Settings;
use Elementor\App\Modules\ImportExportCustomization\Runners\Revert\Taxonomies;
use Elementor\App\Modules\ImportExportCustomization\Runners\Revert\Templates;
use Elementor\App\Modules\ImportExportCustomization\Runners\Revert\Wp_Content;
use Elementor\App\Modules\ImportExportCustomization\Utils;

class Revert {

	/**
	 * @var Revert_Runner_Base[]
	 */
	protected $runners = [];

	private $import_sessions;

	private $revert_sessions;

	public function __construct() {
		$this->import_sessions = self::get_import_sessions();
		$this->revert_sessions = self::get_revert_sessions();
	}

	/**
	 * Register a runner.
	 *
	 * @param Revert_Runner_Base $runner_instance
	 */
	public function register( Revert_Runner_Base $runner_instance ) {
		$this->runners[ $runner_instance::get_name() ] = $runner_instance;
	}

	public function register_default_runners() {
		$this->register( new Site_Settings() );
		$this->register( new Plugins() );
		$this->register( new Templates() );
		$this->register( new Taxonomies() );
		$this->register( new Elementor_Content() );
		$this->register( new Wp_Content() );
	}

	/**
	 * Execute the revert process.
	 *
	 * @throws \Exception If no revert runners have been specified.
	 */
	public function run() {
		if ( empty( $this->runners ) ) {
			throw new \Exception( 'Couldn’t execute the revert process because no revert runners have been specified. Try again by specifying revert runners.' );
		}

		$import_session = $this->get_last_import_session();

		if ( empty( $import_session ) ) {
			throw new \Exception( 'Couldn’t execute the revert process because there are no import sessions to revert.' );
		}

		// fallback if the import session failed and doesn't have the runners metadata
		if ( ! isset( $import_session['runners'] ) && isset( $import_session['instance_data'] ) ) {
			$import_session['runners'] = $import_session['instance_data']['runners_import_metadata'] ?? [];
		}

		foreach ( $this->runners as $runner ) {
			if ( $runner->should_revert( $import_session ) ) {
				$runner->revert( $import_session );
			}
		}

		$this->revert_attachments( $import_session );

		$this->delete_last_import_data();
	}

	public static function get_import_sessions() {
		$import_sessions = Utils::get_import_sessions();

		if ( ! $import_sessions ) {
			return [];
		}

		usort( $import_sessions, function( $a, $b ) {
			return strcmp( $a['start_timestamp'], $b['start_timestamp'] );
		} );

		return $import_sessions;
	}

	public static function get_revert_sessions() {
		$revert_sessions = get_option( Module::OPTION_KEY_ELEMENTOR_REVERT_SESSIONS );

		if ( ! $revert_sessions ) {
			return [];
		}

		return $revert_sessions;
	}

	public function get_last_import_session() {
		$import_sessions = $this->import_sessions;

		if ( empty( $import_sessions ) ) {
			return [];
		}

		return end( $import_sessions );
	}

	public function get_penultimate_import_session() {
		$sessions_data = $this->import_sessions;
		$penultimate_element_value = [];

		if ( empty( $sessions_data ) ) {
			return [];
		}

		end( $sessions_data );

		prev( $sessions_data );

		if ( ! is_null( key( $sessions_data ) ) ) {
			$penultimate_element_value = current( $sessions_data );
		}

		return $penultimate_element_value;
	}

	private function delete_last_import_data() {
		$import_sessions = $this->import_sessions;
		$revert_sessions = $this->revert_sessions;

		$reverted_session = array_pop( $import_sessions );

		$revert_sessions[] = [
			'session_id' => $reverted_session['session_id'],
			'kit_title' => $reverted_session['kit_title'],
			'kit_name' => $reverted_session['kit_name'],
			'kit_thumbnail' => $reverted_session['kit_thumbnail'],
			'source' => $reverted_session['kit_source'],
			'user_id' => get_current_user_id(),
			'import_timestamp' => $reverted_session['start_timestamp'],
			'revert_timestamp' => current_time( 'timestamp' ),
		];

		update_option( Module::OPTION_KEY_ELEMENTOR_IMPORT_SESSIONS, $import_sessions, false );
		update_option( Module::OPTION_KEY_ELEMENTOR_REVERT_SESSIONS, $revert_sessions, false );

		$this->import_sessions = $import_sessions;
		$this->revert_sessions = $revert_sessions;
	}

	private function revert_attachments( $data ) {
		$query_args = [
			'post_type' => 'attachment',
			'post_status' => 'any',
			'posts_per_page' => -1,
			'meta_query' => [
				[
					'key' => Module::META_KEY_ELEMENTOR_IMPORT_SESSION_ID,
					'value' => $data['session_id'],
				],
			],
		];

		$query = new \WP_Query( $query_args );

		foreach ( $query->posts as $post ) {
			wp_delete_attachment( $post->ID, true );
		}
	}
}
