<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Import
 */

/**
 * Class WPSEO_ImportStatus.
 *
 * Holds the status of and message about imports.
 */
class WPSEO_Import_Status {

	/**
	 * The import status.
	 *
	 * @var bool
	 */
	public $status = false;

	/**
	 * The import message.
	 *
	 * @var string
	 */
	private $msg = '';

	/**
	 * The type of action performed.
	 *
	 * @var string
	 */
	private $action;

	/**
	 * WPSEO_Import_Status constructor.
	 *
	 * @param string $action The type of import action.
	 * @param bool   $status The status of the import.
	 * @param string $msg    Extra messages about the status.
	 */
	public function __construct( $action, $status, $msg = '' ) {
		$this->action = $action;
		$this->status = $status;
		$this->msg    = $msg;
	}

	/**
	 * Get the import message.
	 *
	 * @return string Message about current status.
	 */
	public function get_msg() {
		if ( $this->msg !== '' ) {
			return $this->msg;
		}

		if ( $this->status === false ) {
			/* translators: %s is replaced with the name of the plugin we're trying to find data from. */
			return __( '%s data not found.', 'wordpress-seo' );
		}

		return $this->get_default_success_message();
	}

	/**
	 * Get the import action.
	 *
	 * @return string Import action type.
	 */
	public function get_action() {
		return $this->action;
	}

	/**
	 * Set the import action, set status to false.
	 *
	 * @param string $action The type of action to set as import action.
	 *
	 * @return void
	 */
	public function set_action( $action ) {
		$this->action = $action;
		$this->status = false;
	}

	/**
	 * Sets the importer status message.
	 *
	 * @param string $msg The message to set.
	 *
	 * @return void
	 */
	public function set_msg( $msg ) {
		$this->msg = $msg;
	}

	/**
	 * Sets the importer status.
	 *
	 * @param bool $status The status to set.
	 *
	 * @return WPSEO_Import_Status The current object.
	 */
	public function set_status( $status ) {
		$this->status = (bool) $status;

		return $this;
	}

	/**
	 * Returns a success message depending on the action.
	 *
	 * @return string Returns a success message for the current action.
	 */
	private function get_default_success_message() {
		switch ( $this->action ) {
			case 'import':
				/* translators: %s is replaced with the name of the plugin we're importing data from. */
				return __( '%s data successfully imported.', 'wordpress-seo' );
			case 'cleanup':
				/* translators: %s is replaced with the name of the plugin we're removing data from. */
				return __( '%s data successfully removed.', 'wordpress-seo' );
			case 'detect':
			default:
				/* translators: %s is replaced with the name of the plugin we've found data from. */
				return __( '%s data found.', 'wordpress-seo' );
		}
	}
}
