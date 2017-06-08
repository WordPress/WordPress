<?php

class US_Plugin_Upgrader_Skin extends WP_Upgrader_Skin {

	public $plugin = '';
	public $plugin_active = false;
	public $plugin_network_active = false;
	public $messages = array();

	/**
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {

		$defaults = array( 'url' => '', 'plugin' => '', 'nonce' => '', 'title' => '' );
		$args = wp_parse_args( $args, $defaults );

		$this->plugin = $args['plugin'];

		$this->plugin_active = is_plugin_active( $this->plugin );
		$this->plugin_network_active = is_plugin_active_for_network( $this->plugin );

		parent::__construct( $args );
	}

	/**
	 * @access public
	 */
	public function after() { }
	public function header() { }
	public function footer() { }

	public function feedback($string) {
		if ( isset( $this->upgrader->strings[$string] ) )
			$string = $this->upgrader->strings[$string];

		if ( strpos($string, '%') !== false ) {
			$args = func_get_args();
			$args = array_splice( $args, 1 );
			if ( $args ) {
				$args = array_map( 'strip_tags', $args );
				$args = array_map( 'esc_html', $args );
				$string = vsprintf( $string, $args );
			}
		}
		if ( empty( $string ) )
			return;

		$this->messages[] = $string;
	}
}
