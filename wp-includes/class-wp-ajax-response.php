<?php
/**
 * Send XML response back to AJAX request.
 *
 * @package WordPress
 * @since 2.1.0
 */
class WP_Ajax_Response {
	/**
	 * Store XML responses to send.
	 *
	 * @since 2.1.0
	 * @var array
	 * @access private
	 */
	var $responses = array();

	/**
	 * Constructor - Passes args to {@link WP_Ajax_Response::add()}.
	 *
	 * @since 2.1.0
	 * @see WP_Ajax_Response::add()
	 *
	 * @param string|array $args Optional. Will be passed to add() method.
	 * @return WP_Ajax_Response
	 */
	function __construct( $args = '' ) {
		if ( !empty($args) )
			$this->add($args);
	}

	/**
	 * Append to XML response based on given arguments.
	 *
	 * The arguments that can be passed in the $args parameter are below. It is
	 * also possible to pass a WP_Error object in either the 'id' or 'data'
	 * argument. The parameter isn't actually optional, content should be given
	 * in order to send the correct response.
	 *
	 * 'what' argument is a string that is the XMLRPC response type.
	 * 'action' argument is a boolean or string that acts like a nonce.
	 * 'id' argument can be WP_Error or an integer.
	 * 'old_id' argument is false by default or an integer of the previous ID.
	 * 'position' argument is an integer or a string with -1 = top, 1 = bottom,
	 * html ID = after, -html ID = before.
	 * 'data' argument is a string with the content or message.
	 * 'supplemental' argument is an array of strings that will be children of
	 * the supplemental element.
	 *
	 * @since 2.1.0
	 *
	 * @param string|array $args Override defaults.
	 * @return string XML response.
	 */
	function add( $args = '' ) {
		$defaults = array(
			'what' => 'object', 'action' => false,
			'id' => '0', 'old_id' => false,
			'position' => 1,
			'data' => '', 'supplemental' => array()
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
		$position = preg_replace( '/[^a-z0-9:_-]/i', '', $position );

		if ( is_wp_error($id) ) {
			$data = $id;
			$id = 0;
		}

		$response = '';
		if ( is_wp_error($data) ) {
			foreach ( (array) $data->get_error_codes() as $code ) {
				$response .= "<wp_error code='$code'><![CDATA[" . $data->get_error_message($code) . "]]></wp_error>";
				if ( !$error_data = $data->get_error_data($code) )
					continue;
				$class = '';
				if ( is_object($error_data) ) {
					$class = ' class="' . get_class($error_data) . '"';
					$error_data = get_object_vars($error_data);
				}

				$response .= "<wp_error_data code='$code'$class>";

				if ( is_scalar($error_data) ) {
					$response .= "<![CDATA[$error_data]]>";
				} elseif ( is_array($error_data) ) {
					foreach ( $error_data as $k => $v )
						$response .= "<$k><![CDATA[$v]]></$k>";
				}

				$response .= "</wp_error_data>";
			}
		} else {
			$response = "<response_data><![CDATA[$data]]></response_data>";
		}

		$s = '';
		if ( is_array($supplemental) ) {
			foreach ( $supplemental as $k => $v )
				$s .= "<$k><![CDATA[$v]]></$k>";
			$s = "<supplemental>$s</supplemental>";
		}

		if ( false === $action )
			$action = $_POST['action'];

		$x = '';
		$x .= "<response action='{$action}_$id'>"; // The action attribute in the xml output is formatted like a nonce action
		$x .=	"<$what id='$id' " . ( false === $old_id ? '' : "old_id='$old_id' " ) . "position='$position'>";
		$x .=		$response;
		$x .=		$s;
		$x .=	"</$what>";
		$x .= "</response>";

		$this->responses[] = $x;
		return $x;
	}

	/**
	 * Display XML formatted responses.
	 *
	 * Sets the content type header to text/xml.
	 *
	 * @since 2.1.0
	 */
	function send() {
		header('Content-Type: text/xml');
		echo "<?xml version='1.0' standalone='yes'?><wp_ajax>";
		foreach ( (array) $this->responses as $response )
			echo $response;
		echo '</wp_ajax>';
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			wp_die();
		else
			die();
	}
}
