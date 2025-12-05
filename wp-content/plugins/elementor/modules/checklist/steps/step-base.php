<?php

namespace Elementor\Modules\Checklist\Steps;

use Elementor\Core\Isolation\Wordpress_Adapter;
use Elementor\Core\Isolation\Wordpress_Adapter_Interface;
use Elementor\Core\Isolation\Elementor_Adapter;
use Elementor\Core\Isolation\Elementor_Adapter_Interface;
use Elementor\Modules\Checklist\Module as Checklist_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Step_Base {
	/**
	 * @var string
	 * This is the key to be set to true if the step can be completed, and still be considered completed even if the user later did something to the should have it marked as not completed
	 */
	const IS_COMPLETION_IMMUTABLE = 'is_completion_immutable';
	const MARKED_AS_COMPLETED_KEY = 'is_marked_completed';
	const IMMUTABLE_COMPLETION_KEY = 'is_immutable_completed';
	const ABSOLUTE_COMPLETION_KEY = 'is_absolute_completed';

	private array $user_progress;
	protected Wordpress_Adapter_Interface $wordpress_adapter;
	protected Elementor_Adapter_Interface $elementor_adapter;
	protected ?array $promotion_data;
	protected Checklist_Module $module;

	/**
	 * Returns a steps current completion status
	 *
	 * @return bool
	 */
	abstract protected function is_absolute_completed(): bool;

	/**
	 * @return string
	 */
	abstract public function get_id(): string;

	/**
	 * @return string
	 */
	abstract public function get_title(): string;

	/**
	 * @return string
	 */
	abstract public function get_description(): string;

	/**
	 * For instance; 'Create 3 pages'
	 *
	 * @return string
	 */
	abstract public function get_cta_text(): string;

	/**
	 * @return string
	 */
	abstract public function get_cta_url(): string;

	/**
	 * @return bool
	 */
	abstract public function get_is_completion_immutable(): bool;

	/**
	 * @return string
	 */
	abstract public function get_image_src(): string;

	/**
	 * Step_Base constructor.
	 *
	 * @param Checklist_Module             $module
	 * @param ?Wordpress_Adapter_Interface $wordpress_adapter
	 * @param ?Elementor_Adapter_Interface $elementor_adapter
	 * @return void
	 */
	public function __construct( Checklist_Module $module, ?Wordpress_Adapter_Interface $wordpress_adapter = null, ?Elementor_Adapter_Interface $elementor_adapter = null, $promotion_data = null ) {
		$this->module = $module;
		$this->wordpress_adapter = $wordpress_adapter ?? new Wordpress_Adapter();
		$this->elementor_adapter = $elementor_adapter ?? new Elementor_Adapter();
		$this->promotion_data = $promotion_data;

		$this->user_progress = $module->get_step_progress( $this->get_id() ) ?? $this->get_step_initial_progress();
	}

	/**
	 * Returns step visibility (by-default step is visible)
	 *
	 * @return bool
	 */
	public function is_visible(): bool {
		return true;
	}

	public function get_learn_more_text(): string {
		return esc_html__( 'Learn more', 'elementor' );
	}

	public function get_learn_more_url(): string {
		return 'https://go.elementor.com/getting-started-with-elementor/';
	}

	public function update_step( array $step_data ): void {
		$allowed_properties = [
			self::MARKED_AS_COMPLETED_KEY => $step_data[ self::MARKED_AS_COMPLETED_KEY ] ?? null,
			self::IMMUTABLE_COMPLETION_KEY => $step_data[ self::IMMUTABLE_COMPLETION_KEY ] ?? null,
			self::ABSOLUTE_COMPLETION_KEY => $step_data[ self::ABSOLUTE_COMPLETION_KEY ] ?? null,
		];

		foreach ( $allowed_properties as $key => $value ) {
			if ( null !== $value ) {
				$this->user_progress[ $key ] = $value;
			}
		}

		$this->set_step_progress();
	}

	/**
	 * Marking a step as completed based on user's desire
	 *
	 * @return void
	 */
	public function mark_as_completed(): void {
		$this->update_step( [ self::MARKED_AS_COMPLETED_KEY => true ] );
	}

	/**
	 * Unmarking a step as completed based on user's desire
	 *
	 * @return void
	 */
	public function unmark_as_completed(): void {
		$this->update_step( [ self::MARKED_AS_COMPLETED_KEY => false ] );
	}

	/**
	 * Marking a step as completed if it was completed once, and it's suffice to marketing's requirements
	 *
	 * @return void
	 */
	public function maybe_immutably_mark_as_completed(): void {
		$is_immutable_completed = $this->user_progress[ self::IMMUTABLE_COMPLETION_KEY ] ?? false;

		if ( ! $is_immutable_completed && $this->get_is_completion_immutable() && $this->is_absolute_completed() ) {
			$this->update_step( [
				self::MARKED_AS_COMPLETED_KEY => false,
				self::IMMUTABLE_COMPLETION_KEY => true,
			] );
		}
	}

	/**
	 * Returns the step marked as completed value
	 *
	 * @return bool
	 */
	public function is_marked_as_completed(): bool {
		return $this->user_progress[ self::MARKED_AS_COMPLETED_KEY ];
	}

	/**
	 * Returns the step completed value
	 *
	 * @return bool
	 */
	public function is_immutable_completed(): bool {
		return $this->get_is_completion_immutable() && $this->user_progress[ self::IMMUTABLE_COMPLETION_KEY ] ?? false;
	}

	/**
	 * Sets and returns the initial progress of the step
	 *
	 * @return array
	 */
	public function get_step_initial_progress(): array {
		$initial_progress = [
			self::MARKED_AS_COMPLETED_KEY => false,
			self::IMMUTABLE_COMPLETION_KEY => false,
		];

		$this->module->set_step_progress( $this->get_id(), $initial_progress );

		return $initial_progress;
	}

	/**
	 * @return ?array
	 */
	public function get_promotion_data(): ?array {
		return $this->promotion_data;
	}

	/**
	 * Sets the step progress
	 *
	 * @return void
	 */
	private function set_step_progress(): void {
		$this->module->set_step_progress( $this->get_id(), $this->user_progress );
	}
}
