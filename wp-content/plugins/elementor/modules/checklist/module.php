<?php

namespace Elementor\Modules\Checklist;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Modules\ElementorCounter\Module as Elementor_Counter;
use Elementor\Core\Isolation\Wordpress_Adapter;
use Elementor\Core\Isolation\Wordpress_Adapter_Interface;
use Elementor\Core\Isolation\Elementor_Adapter;
use Elementor\Core\Isolation\Elementor_Adapter_Interface;
use Elementor\Core\Isolation\Elementor_Counter_Adapter_Interface;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Modules\Checklist\Data\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule implements Checklist_Module_Interface {
	const DB_OPTION_KEY = 'elementor_checklist';
	const VISIBILITY_SWITCH_ID = 'show_launchpad_checklist';
	const FIRST_CLOSED_CHECKLIST_IN_EDITOR = 'first_closed_checklist_in_editor';
	const LAST_OPENED_TIMESTAMP = 'last_opened_timestamp';
	const SHOULD_OPEN_IN_EDITOR = 'should_open_in_editor';
	const IS_POPUP_MINIMIZED_KEY = 'is_popup_minimized';

	private Steps_Manager $steps_manager;
	private Wordpress_Adapter_Interface $wordpress_adapter;
	private Elementor_Adapter_Interface $elementor_adapter;
	private Elementor_Counter_Adapter_Interface $counter_adapter;
	private $user_progress = null;

	/**
	 * @param ?Wordpress_Adapter_Interface $wordpress_adapter
	 * @param ?Elementor_Adapter_Interface $elementor_adapter
	 *
	 * @return void
	 */
	public function __construct(
		?Wordpress_Adapter_Interface $wordpress_adapter = null,
		?Elementor_Adapter_Interface $elementor_adapter = null
	) {
		$this->wordpress_adapter = $wordpress_adapter ?? new Wordpress_Adapter();
		$this->elementor_adapter = $elementor_adapter ?? new Elementor_Adapter();

		parent::__construct();
		$this->init_user_progress();

		Plugin::$instance->data_manager_v2->register_controller( new Controller() );
		$this->user_progress = $this->user_progress ?? $this->get_user_progress_from_db();
		$this->handle_checklist_visibility_with_kit();
		$this->steps_manager = new Steps_Manager( $this );

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->enqueue_editor_scripts();
	}

	/**
	 * Get the module name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'e-checklist';
	}

	/**
	 * Gets user's progress from db
	 *
	 * @return array {
	 *      @type bool $is_hidden
	 *      @type int $last_opened_timestamp
	 *      @type array $steps {
	 *          @type string $step_id => {
	 *              @type bool $is_marked_completed
	 *              @type bool $is_absolute_competed
	 *              @type bool $is_immutable_completed
	 *          }
	 *      }
	 *  }
	 */
	public function get_user_progress_from_db(): array {
		$db_progress = json_decode( $this->wordpress_adapter->get_option( self::DB_OPTION_KEY ), true );
		$db_progress = is_array( $db_progress ) ? $db_progress : [];

		$progress = array_merge( $this->get_default_user_progress(), $db_progress );

		$editor_visit_count = $this->elementor_adapter->get_count( Elementor_Counter::EDITOR_COUNTER_KEY );
		$progress[ self::SHOULD_OPEN_IN_EDITOR ] = 2 === $editor_visit_count && ! $progress[ self::LAST_OPENED_TIMESTAMP ];

		return $progress;
	}

	/**
	 * Using the step's ID, get the progress of the step should it exist
	 *
	 * @param $step_id
	 *
	 * @return null|array {
	 *      @type bool $is_marked_completed
	 *      @type bool $is_completed
	 *  }
	 */
	public function get_step_progress( $step_id ): ?array {
		return $this->user_progress['steps'][ $step_id ] ?? null;
	}

	/**
	 * Update the progress of a step
	 *
	 * @param $step_id
	 * @param $step_progress
	 *
	 * @return void
	 */
	public function set_step_progress( $step_id, $step_progress ): void {
		$this->user_progress['steps'][ $step_id ] = $step_progress;
		$this->update_user_progress_in_db();
	}

	public function update_user_progress( $new_data ): void {
		$allowed_properties = [
			self::FIRST_CLOSED_CHECKLIST_IN_EDITOR => $new_data[ self::FIRST_CLOSED_CHECKLIST_IN_EDITOR ] ?? null,
			self::LAST_OPENED_TIMESTAMP => $new_data[ self::LAST_OPENED_TIMESTAMP ] ?? null,
			self::IS_POPUP_MINIMIZED_KEY => $new_data[ self::IS_POPUP_MINIMIZED_KEY ] ?? null,
		];

		foreach ( $allowed_properties as $key => $value ) {
			if ( null !== $value ) {
				$this->user_progress[ $key ] = $this->get_formatted_value( $key, $value );
			}
		}

		$this->update_user_progress_in_db();

		if ( isset( $new_data[ Elementor_Counter::EDITOR_COUNTER_KEY ] ) ) {
			$this->elementor_adapter->set_count( Elementor_Counter::EDITOR_COUNTER_KEY, $new_data[ Elementor_Counter::EDITOR_COUNTER_KEY ] );
		}
	}

	/**
	 * @return Steps_Manager
	 */
	public function get_steps_manager(): Steps_Manager {
		return $this->steps_manager;
	}

	/**
	 * @return Wordpress_Adapter
	 */
	public function get_wordpress_adapter(): Wordpress_Adapter {
		return $this->wordpress_adapter;
	}

	/**
	 * @return Elementor_Adapter
	 */
	public function get_elementor_adapter(): Elementor_Adapter {
		return $this->elementor_adapter;
	}

	public function enqueue_editor_scripts(): void {
		add_action( 'elementor/editor/before_enqueue_scripts', function () {
			$min_suffix = Utils::is_script_debug() ? '' : '.min';

			wp_enqueue_script(
				$this->get_name(),
				ELEMENTOR_ASSETS_URL . 'js/checklist' . $min_suffix . '.js',
				[
					'react',
					'react-dom',
					'elementor-common',
					'elementor-v2-ui',
					'elementor-v2-icons',
					'elementor-v2-editor-app-bar',
					'elementor-web-cli',
				],
				ELEMENTOR_VERSION,
				true
			);

			wp_set_script_translations( $this->get_name(), 'elementor' );
		} );
	}

	public function is_preference_switch_on(): bool {
		if ( $this->should_switch_preferences_off() ) {
			return false;
		}

		$user_preferences = $this->wordpress_adapter->get_user_preferences( self::VISIBILITY_SWITCH_ID );

		return 'yes' === $user_preferences || $this->wordpress_adapter->is_new_installation();
	}

	public function should_switch_preferences_off(): bool {
		return ! $this->elementor_adapter->is_active_kit_default() && ! $this->user_progress[ self::LAST_OPENED_TIMESTAMP ] && ! $this->elementor_adapter->get_count( Elementor_Counter::EDITOR_COUNTER_KEY );
	}

	private function init_user_progress(): void {
		$default_settings = $this->get_default_user_progress();

		$this->wordpress_adapter->add_option( self::DB_OPTION_KEY, wp_json_encode( $default_settings ) );
	}

	private function get_default_user_progress(): array {
		return [
			self::LAST_OPENED_TIMESTAMP => null,
			self::FIRST_CLOSED_CHECKLIST_IN_EDITOR => false,
			self::IS_POPUP_MINIMIZED_KEY => false,
			'steps' => [],
		];
	}

	private function update_user_progress_in_db(): void {
		$this->wordpress_adapter->update_option( self::DB_OPTION_KEY, wp_json_encode( $this->user_progress ) );
	}

	private function get_formatted_value( $key, $value ) {
		if ( self::LAST_OPENED_TIMESTAMP === $key ) {
			return $value ? time() : null;
		}

		return $value;
	}

	private function handle_checklist_visibility_with_kit() {
		if ( ! $this->should_switch_preferences_off() ) {
			return;
		}

		add_action( 'elementor/editor/init', function () {
			$this->wordpress_adapter->set_user_preferences( self::VISIBILITY_SWITCH_ID, '' );
		}, 11 );
	}

	public static function should_display_checklist_toggle_control(): bool {
		return current_user_can( 'manage_options' );
	}
}
