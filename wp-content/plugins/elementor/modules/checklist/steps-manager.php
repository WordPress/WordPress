<?php

namespace Elementor\Modules\Checklist;

use Elementor\Modules\Checklist\Steps\Assign_Homepage;
use Elementor\Modules\Checklist\Steps\Create_Pages;
use Elementor\Modules\Checklist\Steps\Setup_Header;
use Elementor\Modules\Checklist\Steps\Add_Logo;
use Elementor\Modules\Checklist\Steps\Step_Base;
use Elementor\Modules\Checklist\Steps\Set_Fonts_And_Colors;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Steps_Manager {
	/** @var Step_Base[] $step_instances */
	private array $step_instances = [];

	private static array $step_ids = [
		Add_Logo::STEP_ID,
		Set_Fonts_And_Colors::STEP_ID,
		Create_Pages::STEP_ID,
		Setup_Header::STEP_ID,
		Assign_Homepage::STEP_ID,
	];

	private Checklist_Module_Interface $module;

	public function __construct( Checklist_Module_Interface $module ) {
		$this->module = $module;
		$this->register_steps();

		add_action( 'elementor/init', function() {
			$this->filter_steps();
		} );
	}

	/**
	 * Gets formatted and ordered array of step ( step data, is_marked_completed and is_completed )
	 *
	 * @return array
	 */
	public function get_steps_for_frontend(): array {
		$formatted_steps = [];

		foreach ( self::$step_ids as $step_id ) {
			$instance = $this->step_instances[ $step_id ];
			$instance->maybe_immutably_mark_as_completed();

			$step = [
				Step_Base::MARKED_AS_COMPLETED_KEY => $instance->is_marked_as_completed(),
				Step_Base::IMMUTABLE_COMPLETION_KEY => $instance->is_immutable_completed(),
				Step_Base::ABSOLUTE_COMPLETION_KEY => $instance->is_absolute_completed(),
				'config' => $this->get_step_config( $step_id ),
			];

			$formatted_steps[] = $step;
		}

		return $formatted_steps;
	}

	public function update_step( string $step_id, array $data ): void {
		$step = $this->get_step_by_id( $step_id );

		if ( ! $step ) {
			return;
		}

		$step->update_step( $data );
	}

	/**
	 * Marks a step as completed, returns true if the step was found and marked or false otherwise
	 *
	 * @param string $step_id
	 *
	 * @return void
	 */
	public function mark_step_as_completed( string $step_id ): void {
		$this->update_step( $step_id, [ Step_Base::MARKED_AS_COMPLETED_KEY => true ] );
	}

	/**
	 * Unmarks a step as completed, returns true if the step was found and unmarked or false otherwise
	 *
	 * @param string $step_id
	 *
	 * @return void
	 */
	public function unmark_step_as_completed( string $step_id ): void {
		$this->update_step( $step_id, [ Step_Base::MARKED_AS_COMPLETED_KEY => false ] );
	}

	/**
	 * Maybe marks a step as completed (depending on if source allows it), returns true if the step was found and marked or false otherwise
	 *
	 * @param $step_id
	 *
	 * @return void
	 */
	public function maybe_set_step_as_immutable_completed( string $step_id ): void {
		$step = $this->get_step_by_id( $step_id );

		if ( ! $step ) {
			return;
		}

		$step->maybe_immutably_mark_as_completed();
	}

	public function get_step_by_id( string $step_id ): ?Step_Base {
		return $this->step_instances[ $step_id ] ?? null;
	}

	/**
	 * @return array
	 */
	public function get_step_config( $step_id ): array {
		$step_instance = $this->step_instances[ $step_id ];

		return $step_instance
			? [
				'id' => $step_instance->get_id(),
				'title' => $step_instance->get_title(),
				'description' => $step_instance->get_description(),
				'learn_more_text' => $step_instance->get_learn_more_text(),
				'learn_more_url' => $step_instance->get_learn_more_url(),
				Step_Base::IS_COMPLETION_IMMUTABLE => $step_instance->get_is_completion_immutable(),
				'cta_text' => $step_instance->get_cta_text(),
				'cta_url' => $step_instance->get_cta_url(),
				'image_src' => $step_instance->get_image_src(),
				'promotion_data' => $step_instance->get_promotion_data(),
			]
			: [];
	}

	/**
	 * Getting the step instances array based on source's order
	 *
	 * @return void
	 */
	private function register_steps(): void {
		foreach ( self::$step_ids as $step_id ) {
			$step_instance = $this->get_step_instance( $step_id );

			if ( $step_instance && ! isset( $this->step_instances[ $step_id ] ) ) {
				$this->step_instances[ $step_id ] = $step_instance;
			}
		}
	}

	/**
	 * Returns the steps config from source
	 *
	 * @return array
	 */
	public static function get_step_ids(): array {
		return self::$step_ids;
	}

	/**
	 * Using step data->id, instantiates and returns the step class or null if the class does not exist
	 *
	 * @param $step_data
	 *
	 * @return Step_Base|null
	 */
	private function get_step_instance( string $step_id ): ?Step_Base {
		$class_name = '\\Elementor\\Modules\\Checklist\\Steps\\' . $step_id;

		if ( ! class_exists( $class_name ) ) {
			return null;
		}

		/** @var Step_Base $step */
		return new $class_name( $this->module, $this->module->get_wordpress_adapter(), $this->module->get_elementor_adapter() );
	}

	private function filter_steps() {
		$step_ids  = [];
		$filtered_steps = apply_filters( 'elementor/checklist/steps', $this->step_instances );

		foreach ( $filtered_steps as $step_id => $step_instance ) {
			if ( ! $step_instance instanceof Step_Base ) {
				continue;
			}

			if ( ! $step_instance->is_visible() ) {
				continue;
			}

			$this->step_instances[ $step_id ] = $step_instance;
			$step_ids[] = $step_id;
		}

		self::$step_ids = $step_ids;
	}
}
