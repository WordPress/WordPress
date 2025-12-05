<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Application\Tracking;

use Yoast\WP\SEO\Dashboard\Infrastructure\Tracking\Setup_Steps_Tracking_Repository_Interface;

/**
 * Tracks the setup steps.
 */
class Setup_Steps_Tracking {

	/**
	 * The setup steps tracking repository.
	 *
	 * @var Setup_Steps_Tracking_Repository_Interface
	 */
	private $setup_steps_tracking_repository;

	/**
	 * Constructs the class.
	 *
	 * @param Setup_Steps_Tracking_Repository_Interface $setup_steps_tracking_repository The setup steps tracking repository.
	 */
	public function __construct( Setup_Steps_Tracking_Repository_Interface $setup_steps_tracking_repository ) {
		$this->setup_steps_tracking_repository = $setup_steps_tracking_repository;
	}

	/**
	 * If the Site Kit setup widget has been loaded.
	 *
	 * @return string "yes" on "no".
	 */
	public function get_setup_widget_loaded(): string {
		return $this->setup_steps_tracking_repository->get_setup_steps_tracking_element( 'setup_widget_loaded' );
	}

	/**
	 * Gets the stage of the first interaction.
	 *
	 * @return string The stage name.
	 */
	public function get_first_interaction_stage(): string {
		return $this->setup_steps_tracking_repository->get_setup_steps_tracking_element( 'first_interaction_stage' );
	}

	/**
	 * Gets the stage of the last interaction.
	 *
	 * @return string The stage name.
	 */
	public function get_last_interaction_stage(): string {
		return $this->setup_steps_tracking_repository->get_setup_steps_tracking_element( 'last_interaction_stage' );
	}

	/**
	 * If the setup widget has been temporarily dismissed.
	 *
	 * @return string "yes" on "no".
	 */
	public function get_setup_widget_temporarily_dismissed(): string {
		return $this->setup_steps_tracking_repository->get_setup_steps_tracking_element( 'setup_widget_temporarily_dismissed' );
	}

	/**
	 * If the setup widget has been permanently dismissed.
	 *
	 * @return string "yes" on "no".
	 */
	public function get_setup_widget_permanently_dismissed(): string {
		return $this->setup_steps_tracking_repository->get_setup_steps_tracking_element( 'setup_widget_permanently_dismissed' );
	}

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string> The tracking data
	 */
	public function to_array(): array {
		return [
			'setupWidgetLoaded'               => $this->get_setup_widget_loaded(),
			'firstInteractionStage'           => $this->get_first_interaction_stage(),
			'lastInteractionStage'            => $this->get_last_interaction_stage(),
			'setupWidgetTemporarilyDismissed' => $this->get_setup_widget_temporarily_dismissed(),
			'setupWidgetPermanentlyDismissed' => $this->get_setup_widget_permanently_dismissed(),
		];
	}
}
