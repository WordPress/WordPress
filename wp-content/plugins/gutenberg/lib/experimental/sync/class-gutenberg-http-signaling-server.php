<?php
/**
 * Gutenberg_HTTP_Signaling_Server class
 *
 * @package    Gutenberg
 */

/**
 * Gutenberg class that contains an HTTP based signaling server used for collaborative editing.
 *
 * @access private
 * @internal
 */
class Gutenberg_HTTP_Signaling_Server {


	/**
	 * Adds a wp_ajax action to handle the signaling server requests.
	 */
	public static function init() {
		$gutenberg_experiments = get_option( 'gutenberg-experiments' );
		if ( ! $gutenberg_experiments || ! array_key_exists( 'gutenberg-sync-collaboration', $gutenberg_experiments ) ) {
			return;
		}
		add_action( 'wp_ajax_gutenberg_signaling_server', array( __CLASS__, 'do_wp_ajax_action' ) );
	}

	/**
	 * Handles a wp_ajax signaling server request.
	 */
	public static function do_wp_ajax_action() {
		if ( empty( $_REQUEST ) || empty( $_REQUEST['subscriber_id'] ) ) {
			die( 'no identifier' );
		}

		// Contains the subscriber id of the client reading or sending messages.
		$subscriber_id = $_REQUEST['subscriber_id'];

		// Example inside file: array( 2323232121 => array( 'message hello','handshake message' ) ).
		$subscriber_to_messages_path = get_temp_dir() . DIRECTORY_SEPARATOR . 'subscribers_to_messages.txt';

		// Example inside file: array( 'doc1: array( 2323232121 ), 'doc2: array( 2323232123, 2323232121 ) ).
		$topics_to_subscribers_path = get_temp_dir() . DIRECTORY_SEPARATOR . 'topics_to_subscribers.txt';

		if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {
			static::handle_read_pending_messages( $subscriber_to_messages_path, $subscriber_id );
		} else {
			if ( empty( $_POST ) || empty( $_POST['message'] ) ) {
				die( 'no message' );
			}
			$message = json_decode( wp_unslash( $_POST['message'] ), true );
			if ( ! $message ) {
				die( 'no message' );
			}

			switch ( $message['type'] ) {
				case 'subscribe':
					static::handle_subscribe_to_topics( $topics_to_subscribers_path, $subscriber_id, $message['topics'] );
					break;
				case 'unsubscribe':
					static::handle_unsubscribe_from_topics( $topics_to_subscribers_path, $subscriber_id, $message['topics'] );
					break;
				case 'publish':
					static::handle_publish_message( $topics_to_subscribers_path, $subscriber_to_messages_path, $subscriber_id, $message );
					break;
				case 'ping':
					static::handle_ping( $subscriber_to_messages_path, $subscriber_id );
					break;
			}
			echo wp_json_encode( array( 'result' => 'ok' ) ), PHP_EOL, PHP_EOL;
		}

		static::clean_up_old_connections( $topics_to_subscribers_path, $subscriber_to_messages_path, $subscriber_id );
		exit;
	}

	/**
	 * Reads the contents of $fd and returns them deserialized.
	 *
	 * @access private
	 * @internal
	 *
	 * @param resource $fd  A file descriptor.
	 *
	 * @return array Deserialized contents of fd.
	 */
	private static function get_contents_from_file_descriptor( $fd ) {
		$contents_raw = stream_get_contents( $fd );
		$result       = array();
		if ( $contents_raw ) {
			$result = unserialize( $contents_raw );
		}
		return $result;
	}

	/**
	 * Locks a file with an exclusive lock and returns its contents deserialized.
	 *
	 * @access private
	 * @internal
	 *
	 * @param string $path  The path of a file.
	 *
	 * @return array File descriptor as the first element of the array and the contents of the file as the second element.
	 */
	private static function get_contents_and_lock_file( $path ) {
		$fd = fopen( $path, 'c+' );
		if ( ! $fd ) {
			return array( $fd, null );
		}
		flock( $fd, LOCK_EX );
		return array( $fd, static::get_contents_from_file_descriptor( $fd ) );
	}

	/**
	 * Makes the file descriptor content of $fd equal to the serialization of content.
	 * Overwrites what was previously in $fd.
	 * Unlocks the file descriptor and closes it.
	 *
	 * @access private
	 * @internal
	 *
	 * @param resource $fd      A file descriptor.
	 * @param array    $content An array with the contents to serialized and written to the file.
	 */
	private static function save_contents_and_unlock_file( $fd, $content ) {
		static::save_contents_to_file_descriptor( $fd, $content );
		flock( $fd, LOCK_UN );
		fclose( $fd );
	}

	/**
	 * Makes the file descriptor content of $fd equal to the serialization of content.
	 * Overwrites what was previously in $fd.
	 *
	 * @access private
	 * @internal
	 *
	 * @param resource $fd      A file descriptor.
	 * @param array    $content Content to be serialized and written in a file descriptor.
	 */
	private static function save_contents_to_file_descriptor( $fd, $content ) {
		rewind( $fd );
		$data = serialize( $content );
		fwrite( $fd, $data );
		ftruncate( $fd, strlen( $data ) );
	}

	/**
	 * Handles a wp_ajax signaling server request of client that wants to retrieve its messages.
	 *
	 * It returns the client a response following the
	 * {@link https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events#event_stream_format Event stream format}.
	 *
	 * ```
	 * id: <Event ID>
	 * retry: <Reconnection time, in ms>
	 * event: <The type of event>
	 * data: <The message to be sent>
	 * ```
	 *
	 * @access private
	 * @internal
	 *
	 * @param string $subscriber_to_messages_path The path to the file that contains the messages of the subscribers.
	 * @param string $subscriber_id               The subscriber id.
	 */
	private static function handle_read_pending_messages( $subscriber_to_messages_path, $subscriber_id ) {
		header( 'Content-Type: text/event-stream' );
		header( 'Cache-Control: no-cache' );
		list( $fd, $subscriber_to_messages ) = static::get_contents_and_lock_file( $subscriber_to_messages_path );
		if ( ! $fd ) {
			$retries = isset( $_COOKIE['signaling_server_retries'] ) ? intval( $_COOKIE['signaling_server_retries'] ) : 0;
			$secure  = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
			setcookie( 'signaling_server_retries', (string) ( $retries + 1 ), time() + DAY_IN_SECONDS, SITECOOKIEPATH, '', $secure );
			echo 'id: ' . time() . PHP_EOL;
			echo 'event: error' . PHP_EOL;
			echo 'data: ' . 'Could not open required file.' . PHP_EOL . PHP_EOL;
			echo 'retry: ' . 3000 * pow( 2, $retries ) . PHP_EOL;
			exit;
		}

		if ( isset( $_COOKIE['signaling_server_retries'] ) ) {
			$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
			// unset the cookie using a past expiration date.
			setcookie( 'signaling_server_retries', '0', time() - DAY_IN_SECONDS, SITECOOKIEPATH, '', $secure );
		}

		echo 'retry: 3000' . PHP_EOL;
		if ( isset( $subscriber_to_messages[ $subscriber_id ] ) && count( $subscriber_to_messages[ $subscriber_id ] ) > 0 ) {
			echo 'id: ' . time() . PHP_EOL;
			echo 'event: message' . PHP_EOL;
			echo 'data: ' . wp_json_encode( $subscriber_to_messages[ $subscriber_id ] ) . PHP_EOL . PHP_EOL;
			$subscriber_to_messages[ $subscriber_id ] = array();
			static::save_contents_to_file_descriptor( $fd, $subscriber_to_messages );
		} else {
			echo PHP_EOL;
		}
		flock( $fd, LOCK_UN );
		fclose( $fd );
	}

	/**
	 * Handles a wp_ajax signaling server request of client that wants to subscribe to a set of topics its messages.
	 *
	 * @access private
	 * @internal
	 *
	 * @param string $topics_to_subscribers_path Topics to subscribers path.
	 * @param string $subscriber_id              The subscriber id.
	 * @param array  $topics                     An array of topics e.g: array( 'doc1', 'doc2' ).
	 */
	private static function handle_subscribe_to_topics( $topics_to_subscribers_path, $subscriber_id, $topics ) {
		list( $fd, $topics_to_subscribers ) = static::get_contents_and_lock_file( $topics_to_subscribers_path );
		if ( ! $fd ) {
			die( 'Could not open required file.' );
		}
		foreach ( $topics as $topic ) {
			if ( ! isset( $topics_to_subscribers[ $topic ] ) ) {
				$topics_to_subscribers[ $topic ] = array();
			}
			$topics_to_subscribers[ $topic ][] = $subscriber_id;
			$topics_to_subscribers[ $topic ]   = array_unique( $topics_to_subscribers[ $topic ] );
		}
		static::save_contents_and_unlock_file( $fd, $topics_to_subscribers );
	}

	/**
	 * Handles a wp_ajax signaling server request of client that wants to unsubscribe from a set of topics its messages.
	 *
	 * @access private
	 * @internal
	 *
	 * @param string $topics_to_subscribers_path Topics to subscribers path.
	 * @param string $subscriber_id              The subscriber id.
	 * @param array  $topics                     An array of topics e.g: array( 'doc1', 'doc2' ).
	 */
	private static function handle_unsubscribe_from_topics( $topics_to_subscribers_path, $subscriber_id, $topics ) {
		list( $fd, $topics_to_subscribers ) = static::get_contents_and_lock_file( $topics_to_subscribers_path );
		if ( ! $fd ) {
			die( 'Could not open required file.' );
		}
		foreach ( $topics as $topic ) {
			if ( $topics_to_subscribers[ $topic ] ) {
				$topics_to_subscribers[ $topic ] = array_diff( $topics_to_subscribers[ $topic ], array( $subscriber_id ) );
			}
		}
		static::save_contents_and_unlock_file( $fd, $topics_to_subscribers );
	}

	/**
	 * Handles a wp_ajax signaling server request of client that wants to publish a message.
	 *
	 * @access private
	 * @internal
	 *
	 * @param string $topics_to_subscribers_path  Topics to subscribers path.
	 * @param string $subscriber_to_messages_path The path to the file that contains the messages of the subscribers.
	 * @param string $subscriber_id               The subscriber id.
	 * @param array  $message                     The message associative array.
	 */
	private static function handle_publish_message( $topics_to_subscribers_path, $subscriber_to_messages_path, $subscriber_id, $message ) {
		list( $fd_topics_subscriber, $topics_to_subscribers )       = static::get_contents_and_lock_file( $topics_to_subscribers_path );
		list( $fd_subscriber_to_messages, $subscriber_to_messages ) = static::get_contents_and_lock_file( $subscriber_to_messages_path );
		if ( ! $fd_topics_subscriber || ! $fd_subscriber_to_messages ) {
			die( 'Could not open required file.' );
		}
		$topic     = $message['topic'];
		$receivers = $topics_to_subscribers[ $topic ];
		if ( $receivers && count( $receivers ) > 0 ) {
			$message['clients'] = count( $receivers );
			foreach ( $receivers as $receiver ) {
				if ( ! isset( $subscriber_to_messages[ $receiver ] ) ) {
					$subscriber_to_messages[ $receiver ] = array();
				}
				$subscriber_to_messages[ $receiver ][] = $message;
			}
			static::save_contents_to_file_descriptor( $fd_subscriber_to_messages, $subscriber_to_messages );
		}
		flock( $fd_subscriber_to_messages, LOCK_UN );
		fclose( $fd_subscriber_to_messages );
		flock( $fd_topics_subscriber, LOCK_UN );
		fclose( $fd_topics_subscriber );
	}

	/**
	 * Handles a wp_ajax signaling server request for the ping of a client.
	 *
	 * @access private
	 * @internal
	 *
	 * @param string $subscriber_to_messages_path The path to the file that contains the messages of the subscribers.
	 * @param string $subscriber_id               The subscriber id.
	 */
	private static function handle_ping( $subscriber_to_messages_path, $subscriber_id ) {
		list( $fd_subscriber_to_messages, $subscriber_to_messages ) = static::get_contents_and_lock_file( $subscriber_to_messages_path );
		if ( ! $fd_subscriber_to_messages ) {
			die( 'Could not open required file.' );
		}
		if ( ! $subscriber_to_messages[ $subscriber_id ] ) {
			$subscriber_to_messages[ $subscriber_id ] = array();
		}
		$subscriber_to_messages[ $subscriber_id ][] = array( 'type' => 'pong' );
		static::save_contents_and_unlock_file( $fd_subscriber_to_messages, $subscriber_to_messages );
	}


	/**
	 * Deletes messages and subscriber information of clients that have not interacted with the signaling server in a long time.
	 *
	 * @access private
	 * @internal
	 *
	 * @param string $topics_to_subscribers_path  The path to the file that contains the subscribers of the topics.
	 * @param string $subscriber_to_messages_path The path to the file that contains the messages of the subscribers.
	 * @param string $connected_subscriber_id     The subscriber id.
	 */
	private static function clean_up_old_connections( $topics_to_subscribers_path, $subscriber_to_messages_path, $connected_subscriber_id ) {
		$subscribers_to_last_connection_path = get_temp_dir() . DIRECTORY_SEPARATOR . 'subscribers_to_last_connection.txt';
		// Example: array( 2323232121 => 34343433323(timestamp) ).

		$fd_subscribers_last_connection = fopen( $subscribers_to_last_connection_path, 'c+' );
		if ( ! $fd_subscribers_last_connection ) {
			die( 'Could not open required file.' );
		}
		flock( $fd_subscribers_last_connection, LOCK_EX );
		$subscribers_to_last_connection_time                             = static::get_contents_from_file_descriptor( $fd_subscribers_last_connection );
		$subscribers_to_last_connection_time[ $connected_subscriber_id ] = time();
		$needs_cleanup = false;
		foreach ( $subscribers_to_last_connection_time as $subscriber_id => $last_connection_time ) {
			// cleanup connections older than 1 hour.
			if ( $last_connection_time < time() - 1 * 60 * 60 ) {
				unset( $subscribers_to_last_connection_time[ $subscriber_id ] );
				$needs_cleanup = true;
			}
		}
		static::save_contents_to_file_descriptor( $fd_subscribers_last_connection, $subscribers_to_last_connection_time );

		if ( $needs_cleanup ) {
			$fd_subscriber_messages = fopen( $subscriber_to_messages_path, 'c+' );
			if ( ! $fd_subscriber_messages ) {
				die( 'Could not open required file.' );
			}
			flock( $fd_subscriber_messages, LOCK_EX );
			$subscriber_to_messages = static::get_contents_from_file_descriptor( $fd_subscriber_messages );
			foreach ( $subscriber_to_messages as $subscriber_id => $messages ) {
				if ( ! isset( $subscribers_to_last_connection_time[ $subscriber_id ] ) ) {
					unset( $subscriber_to_messages[ $subscriber_id ] );
				}
			}
			static::save_contents_to_file_descriptor( $fd_subscriber_messages, $subscriber_to_messages );

			$fd_topics_subscriber = fopen( $topics_to_subscribers_path, 'c+' );
			if ( ! $fd_topics_subscriber ) {
				die( 'Could not open required file.' );
			}
			flock( $fd_topics_subscriber, LOCK_EX );
			$topics_to_subscribers = static::get_contents_from_file_descriptor( $fd_topics_subscriber );
			foreach ( $topics_to_subscribers as $topic => $subscribers ) {
				foreach ( $subscribers as $subscriber_id ) {
					if ( ! isset( $subscribers_to_last_connection_time[ $subscriber_id ] ) ) {
						$topics_to_subscribers[ $topic ] = array_diff( $topics_to_subscribers[ $topic ], array( $subscriber_id ) );
					}
				}
			}
			static::save_contents_to_file_descriptor( $fd_topics_subscriber, $topics_to_subscribers );

			flock( $fd_subscriber_messages, LOCK_UN );
			fclose( $fd_subscriber_messages );

			flock( $fd_topics_subscriber, LOCK_UN );
			fclose( $fd_topics_subscriber );
		}

		flock( $fd_subscribers_last_connection, LOCK_UN );
		fclose( $fd_subscribers_last_connection );
	}
}

Gutenberg_HTTP_Signaling_Server::init();
