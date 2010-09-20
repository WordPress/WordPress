<?php
/**
 * BackPress Scripts enqueue.
 *
 * These classes were refactored from the WordPress WP_Scripts and WordPress
 * script enqueue API.
 *
 * @package BackPress
 * @since r74
 */

/**
 * BackPress enqueued dependiences class.
 *
 * @package BackPress
 * @uses _WP_Dependency
 * @since r74
 */
class WP_Dependencies {
	var $registered = array();
	var $queue = array();
	var $to_do = array();
	var $done = array();
	var $args = array();
	var $groups = array();
	var $group = 0;

	/**
	 * Do the dependencies
	 *
	 * Process the items passed to it or the queue.  Processes all dependencies.
	 *
	 * @param mixed handles (optional) items to be processed.  (void) processes queue, (string) process that item, (array of strings) process those items
	 * @return array Items that have been processed
	 */
	function do_items( $handles = false, $group = false ) {
		// Print the queue if nothing is passed.  If a string is passed, print that script.  If an array is passed, print those scripts.
		$handles = false === $handles ? $this->queue : (array) $handles;
		$this->all_deps( $handles );

		foreach( $this->to_do as $key => $handle ) {
			if ( !in_array($handle, $this->done) && isset($this->registered[$handle]) ) {

				if ( ! $this->registered[$handle]->src ) { // Defines a group.
					$this->done[] = $handle;
					continue;
				}

				if ( $this->do_item( $handle, $group ) )
					$this->done[] = $handle;

				unset( $this->to_do[$key] );
			}
		}

		return $this->done;
	}

	function do_item( $handle ) {
		return isset($this->registered[$handle]);
	}

	/**
	 * Determines dependencies
	 *
	 * Recursively builds array of items to process taking dependencies into account.  Does NOT catch infinite loops.
	 *

	 * @param mixed handles Accepts (string) dep name or (array of strings) dep names
	 * @param bool recursion Used internally when function calls itself
	 */
	function all_deps( $handles, $recursion = false, $group = false ) {
		if ( !$handles = (array) $handles )
			return false;

		foreach ( $handles as $handle ) {
			$handle_parts = explode('?', $handle);
			$handle = $handle_parts[0];
			$queued = in_array($handle, $this->to_do, true);

			if ( in_array($handle, $this->done, true) ) // Already done
				continue;

			$moved = $this->set_group( $handle, $recursion, $group );

			if ( $queued && !$moved ) // already queued and in the right group
				continue;

			$keep_going = true;
			if ( !isset($this->registered[$handle]) )
				$keep_going = false; // Script doesn't exist
			elseif ( $this->registered[$handle]->deps && array_diff($this->registered[$handle]->deps, array_keys($this->registered)) )
				$keep_going = false; // Script requires deps which don't exist (not a necessary check.  efficiency?)
			elseif ( $this->registered[$handle]->deps && !$this->all_deps( $this->registered[$handle]->deps, true, $group ) )
				$keep_going = false; // Script requires deps which don't exist

			if ( !$keep_going ) { // Either script or its deps don't exist.
				if ( $recursion )
					return false; // Abort this branch.
				else
					continue; // We're at the top level.  Move on to the next one.
			}

			if ( $queued ) // Already grobbed it and its deps
				continue;

			if ( isset($handle_parts[1]) )
				$this->args[$handle] = $handle_parts[1];

			$this->to_do[] = $handle;
		}

		return true;
	}

	/**
	 * Adds item
	 *
	 * Adds the item only if no item of that name already exists
	 *
	 * @param string handle Script name
	 * @param string src Script url
	 * @param array deps (optional) Array of script names on which this script depends
	 * @param string ver (optional) Script version (used for cache busting)
	 * @return array Hierarchical array of dependencies
	 */
	function add( $handle, $src, $deps = array(), $ver = false, $args = null ) {
		if ( isset($this->registered[$handle]) )
			return false;
		$this->registered[$handle] = new _WP_Dependency( $handle, $src, $deps, $ver, $args );
		return true;
	}

	/**
	 * Adds extra data
	 *
	 * Adds data only if script has already been added
	 *
	 * @param string handle Script name
	 * @param string data_name Name of object in which to store extra data
	 * @param array data Array of extra data
	 * @return bool success
	 */
	function add_data( $handle, $data_name, $data ) {
		if ( !isset($this->registered[$handle]) )
			return false;
		return $this->registered[$handle]->add_data( $data_name, $data );
	}

	function remove( $handles ) {
		foreach ( (array) $handles as $handle )
			unset($this->registered[$handle]);
	}

	function enqueue( $handles ) {
		foreach ( (array) $handles as $handle ) {
			$handle = explode('?', $handle);
			if ( !in_array($handle[0], $this->queue) && isset($this->registered[$handle[0]]) ) {
				$this->queue[] = $handle[0];
				if ( isset($handle[1]) )
					$this->args[$handle[0]] = $handle[1];
			}
		}
	}

	function dequeue( $handles ) {
		foreach ( (array) $handles as $handle ) {
			$handle = explode('?', $handle);
			$key = array_search($handle[0], $this->queue);
			if ( false !== $key ) {
				unset($this->queue[$key]);
				unset($this->args[$handle[0]]);
			}
		}
	}

	function query( $handle, $list = 'registered' ) { // registered, queue, done, to_do
		switch ( $list ) :
		case 'registered':
		case 'scripts': // back compat
			if ( isset($this->registered[$handle]) )
				return $this->registered[$handle];
			break;
		case 'to_print': // back compat
		case 'printed': // back compat
			if ( 'to_print' == $list )
				$list = 'to_do';
			else
				$list = 'printed';
		default:
			if ( in_array($handle, $this->$list) )
				return true;
			break;
		endswitch;
		return false;
	}

	function set_group( $handle, $recursion, $group ) {
		$group = (int) $group;

		if ( $recursion )
			$group = min($this->group, $group);
		else
			$this->group = $group;

		if ( isset($this->groups[$handle]) && $this->groups[$handle] <= $group )
			return false;

		$this->groups[$handle] = $group;
		return true;
	}

}

class _WP_Dependency {
	var $handle;
	var $src;
	var $deps = array();
	var $ver = false;
	var $args = null;

	var $extra = array();

	function _WP_Dependency() {
		@list($this->handle, $this->src, $this->deps, $this->ver, $this->args) = func_get_args();
		if ( !is_array($this->deps) )
			$this->deps = array();
	}

	function add_data( $name, $data ) {
		if ( !is_scalar($name) )
			return false;
		$this->extra[$name] = $data;
		return true;
	}
}
