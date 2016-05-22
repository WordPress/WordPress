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
	 */
	public $responses = array();

	/**
	 * Constructor - Passes args to WP_Ajax_Response::add().
	 *
	 * @since 2.1.0
	 * @see WP_Ajax_Response::add()
	 *
	 * @param string|array $args Optional. Will be passed to add() method.
	 */
	public function __construct( $args = '' ) {
		if ( !empty($args) )
			$this->add($args);
	}

	/**
	 * Appends data to an XML response based on given arguments.
	 *
	 * With `$args` defaults, extra data output would be:
	 *
	 *     <response action='{$action}_$id'>
	 *      <$what id='$id' position='$position'>
	 *          <response_data><![CDATA[$data]]></response_data>
	 *      </$what>
	 *     </response>
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @param string|array $args {
	 *     Optional. An array or string of XML response arguments.
	 *
	 *     @type string          $what         XML-RPC response type. Used as a child element of `<response>`.
	 *                                         Default 'object' (`<object>`).
	 *     @type string|false    $action       Value to use for the `action` attribute in `<response>`. Will be
	 *                                         appended with `_$id` on output. If false, `$action` will default to
	 *                                         the value of `$_POST['action']`. Default false.
	 *     @type int|WP_Error    $id           The response ID, used as the response type `id` attribute. Also
	 *                                         accepts a `WP_Error` object if the ID does not exist. Default 0.
	 *     @type int|false       $old_id       The previous response ID. Used as the value for the response type
	 *                                         `old_id` attribute. False hides the attribute. Default false.
	 *     @type string          $position     Value of the response type `position` attribute. Accepts 1 (bottom),
	 *                                         -1 (top), html ID (after), or -html ID (before). Default 1 (bottom).
	 *     @type string|WP_Error $data         The response content/message. Also accepts a WP_Error object if the
	 *                                         ID does not exist. Default empty.
	 *     @type array           $supplemental An array of extra strings that will be output within a `<supplemental>`
	 *                                         element as CDATA. Default empty array.
	 * }
	 * @return string XML response.
	 */
	public function add( $args = '' ) {
		$defaults = array(
			'what' => 'object', 'action' => false,
			'id' => '0', 'old_id' => false,
			'position' => 1,
			'data' => '', 'supplemental' => array()
		);

		$r = wp_parse_args( $args, $defaults );

		$position = preg_replace( '/[^a-z0-9:_-]/i', '', $r['position'] );
		$id = $r['id'];
		$what = $r['what'];
		$action = $r['action'];
		$old_id = $r['old_id'];
		$data = $r['data'];

		if ( is_wp_error( $id ) ) {
			$data = $id;
			$id = 0;
		}

		$response = '';
		if ( is_wp_error( $data ) ) {
			foreach ( (array) $data->get_error_codes() as $code ) {
				$response .= "<wp_error code='$code'><![CDATA[" . $data->get_error_message( $code ) . "]]></wp_error>";
				if ( ! $error_data = $data->get_error_data( $code ) ) {
					continue;
				}
				$class = '';
				if ( is_object( $error_data ) ) {
					$class = ' class="' . get_class( $error_data ) . '"';
					$error_data = get_object_vars( $error_data );
				}

				$response .= "<wp_error_data code='$code'$class>";

				if ( is_scalar( $error_data ) ) {
					$response .= "<![CDATA[$error_data]]>";
				} elseif ( is_array( $error_data ) ) {
					foreach ( $error_data as $k => $v ) {
						$response .= "<$k><![CDATA[$v]]></$k>";
					}
				}

				$response .= "</wp_error_data>";
			}
		} else {
			$response = "<response_data><![CDATA[$data]]></response_data>";
		}

		$s = '';
		if ( is_array( $r['supplemental'] ) ) {
			foreach ( $r['supplemental'] as $k => $v ) {
				$s .= "<$k><![CDATA[$v]]></$k>";
			}
			$s = "<supplemental>$s</supplemental>";
		}

		if ( false === $action ) {
			$action = $_POST['action'];
		}
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
	public function send() {
		header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ) );
		echo "<?xml version='1.0' encoding='" . get_option( 'blog_charset' ) . "' standalone='yes'?><wp_ajax>";
		foreach ( (array) $this->responses as $response )
			echo $response;
		echo '</wp_ajax>';
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			wp_die();
		else
			die();
	}
}
