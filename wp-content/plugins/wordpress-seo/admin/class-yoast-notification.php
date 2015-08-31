<?php
/**
 * @package    WPSEO\Admin
 * @since      1.5.3
 */

/**
 * Implements individual notification.
 */
class Yoast_Notification {
	/**
	 * Contains optional arguments:
	 *
	 * -  type: The notification type, i.e. 'updated' or 'error'
	 * -    id: The ID of the notification
	 * - nonce: Security nonce to use in case of dismissible notice.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Contains default values for the optional arguments
	 *
	 * @var array
	 */
	private $defaults = array(
		'type'  => 'updated',
		'id'    => '',
		'nonce' => null,
	);

	/**
	 * The Constructor
	 *
	 * @param string $message
	 * @param array  $options
	 */
	public function __construct( $message, $options = array() ) {
		$this->options         = wp_parse_args( $options, $this->defaults );
		$this->message         = $message;
	}

	/**
	 * Return the object properties as an array
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'message' => $this->message,
			'options' => $this->options,
		);
	}

	/**
	 * Adds string (view) behaviour to the Notification
	 *
	 * @return string
	 */
	public function __toString() {
		return '<div class="yoast-notice notice is-dismissible ' . esc_attr( $this->options['type'] ) . '" id="' . esc_attr( $this->options['id'] ) . '"' . $this->parse_nonce_attribute() . '>' . wpautop( $this->message ) . '</div>' . PHP_EOL;
	}

	/**
	 * Returns a data attribute containing the nonce if present
	 *
	 * @return string
	 */
	private function parse_nonce_attribute() {
		return ( ! empty( $this->options['nonce'] ) ? ' data-nonce="' . $this->options['nonce'] . '"' : '' );
	}
}
