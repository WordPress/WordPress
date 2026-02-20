<?php
/**
 * WP AI Client: WP_AI_Client_Event_Dispatcher class
 *
 * @package WordPress
 * @subpackage AI
 * @since 7.0.0
 */

use WordPress\AiClientDependencies\Psr\EventDispatcher\EventDispatcherInterface;

/**
 * WordPress-specific PSR-14 event dispatcher for the AI Client.
 *
 * Bridges PSR-14 events to WordPress action hooks, enabling plugins to hook
 * into AI client lifecycle events.
 *
 * @since 7.0.0
 * @internal Intended only to wire up the PHP AI Client SDK to WordPress's hook system.
 * @access private
 */
class WP_AI_Client_Event_Dispatcher implements EventDispatcherInterface {

	/**
	 * Dispatches an event to WordPress action hooks.
	 *
	 * Converts the event class name to a WordPress action hook name and fires it.
	 * For example, BeforeGenerateResultEvent becomes wp_ai_client_before_generate_result.
	 *
	 * @since 7.0.0
	 *
	 * @param object $event The event object to dispatch.
	 * @return object The same event object, potentially modified by listeners.
	 */
	public function dispatch( object $event ): object {
		$event_name = $this->get_hook_name_portion_for_event( $event );

		/**
		 * Fires when an AI client event is dispatched.
		 *
		 * The dynamic portion of the hook name, `$event_name`, refers to the
		 * snake_case version of the event class name, without the `_event` suffix.
		 *
		 * For example, an event class named `BeforeGenerateResultEvent` will fire the
		 * `wp_ai_client_before_generate_result` action hook.
		 *
		 * In practice, the available action hook names are:
		 *
		 * - wp_ai_client_before_generate_result
		 * - wp_ai_client_after_generate_result
		 *
		 * @since 7.0.0
		 *
		 * @param object $event The event object.
		 */
		do_action( "wp_ai_client_{$event_name}", $event );

		return $event;
	}

	/**
	 * Converts an event object class name to a WordPress action hook name portion.
	 *
	 * @since 7.0.0
	 *
	 * @param object $event The event object.
	 * @return string The hook name portion derived from the event class name.
	 */
	private function get_hook_name_portion_for_event( object $event ): string {
		$class_name = get_class( $event );
		$pos        = strrpos( $class_name, '\\' );
		$short_name = false !== $pos ? substr( $class_name, $pos + 1 ) : $class_name;

		// Convert PascalCase to snake_case.
		$snake_case = strtolower( (string) preg_replace( '/([a-z])([A-Z])/', '$1_$2', $short_name ) );

		// Strip '_event' suffix if present.
		if ( str_ends_with( $snake_case, '_event' ) ) {
			$snake_case = (string) substr( $snake_case, 0, -6 );
		}

		return $snake_case;
	}
}
