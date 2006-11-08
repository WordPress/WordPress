<?php
class WP_Scripts {
	var $scripts = array();
	var $queue = array();
	var $printed = array();
	var $args = array();

	function WP_Scripts() {
		$this->default_scripts();
	}

	function default_scripts() {
		$this->add( 'dbx', '/wp-includes/js/dbx.js', false, '2.05' );
		$this->add( 'fat', '/wp-includes/js/fat.js', false, '1.0-RC1_3660' );
		$this->add( 'sack', '/wp-includes/js/tw-sack.js', false, '1.6.1' );
		$this->add( 'quicktags', '/wp-includes/js/quicktags.js', false, '3517' );
		$this->add( 'colorpicker', '/wp-includes/js/colorpicker.js', false, '3517' );
		$this->add( 'tiny_mce', '/wp-includes/js/tinymce/tiny_mce_gzip.php', false, '09212006' );
		$this->add( 'wp_tiny_mce', '/wp-includes/js/tinymce/tiny_mce_config.php', array('tiny_mce'), '09212006' );
		$this->add( 'prototype', '/wp-includes/js/prototype.js', false, '1.5.0');
		$this->add( 'autosave', '/wp-includes/js/autosave.js.php', array('prototype', 'sack'), '4206');
		$this->add( 'wp-ajax', '/wp-includes/js/wp-ajax-js.php', array('prototype'), '4459');
		$this->add( 'listman', '/wp-includes/js/list-manipulation-js.php', array('wp-ajax', 'fat'), '4459');
		if ( is_admin() ) {
			$this->add( 'dbx-admin-key', '/wp-admin/dbx-admin-key-js.php', array('dbx'), '3651' );
			$this->add( 'ajaxcat', '/wp-admin/cat-js.php', array('listman'), '3684' );
			$this->add( 'admin-categories', '/wp-admin/categories.js', array('listman'), '3684' );
			$this->add( 'admin-custom-fields', '/wp-admin/custom-fields.js', array('listman'), '3733' );
			$this->add( 'admin-comments', '/wp-admin/edit-comments.js', array('listman'), '3847' );
			$this->add( 'admin-users', '/wp-admin/users.js', array('listman'), '3684' );
			$this->add( 'xfn', '/wp-admin/xfn.js', false, '3517' );
			$this->add( 'upload', '/wp-admin/upload-js.php', array('prototype'), '4355' );
		}
	}

	/**
	 * Prints script tags
	 *
	 * Prints the scripts passed to it or the print queue.  Also prints all necessary dependencies.
	 *
	 * @param mixed handles (optional) Scripts to be printed.  (void) prints queue, (string) prints that script, (array of strings) prints those scripts.
	 * @return array Scripts that have been printed
	 */
	function print_scripts( $handles = false ) {
		// Print the queue if nothing is passed.  If a string is passed, print that script.  If an array is passed, print those scripts.
		$handles = false === $handles ? $this->queue : (array) $handles;
		$handles = $this->all_deps( $handles );
		$this->_print_scripts( $handles );
		return $this->printed;
	}

	/**
	 * Internally used helper function for printing script tags
	 *
	 * @param array handles Hierarchical array of scripts to be printed
	 * @see WP_Scripts::all_deps()
	 */
	function _print_scripts( $handles ) {
		global $wp_db_version;

		foreach( array_keys($handles) as $handle ) {
			if ( !$handles[$handle] )
				return;
			elseif ( is_array($handles[$handle]) )
				$this->_print_scripts( $handles[$handle] );
			if ( !in_array($handle, $this->printed) && isset($this->scripts[$handle]) ) {
				$ver = $this->scripts[$handle]->ver ? $this->scripts[$handle]->ver : $wp_db_version;
				if ( isset($this->args[$handle]) )
					$ver .= '&amp;' . $this->args[$handle];
				$src = 0 === strpos($this->scripts[$handle]->src, 'http://') ? $this->scripts[$handle]->src : get_option( 'siteurl' ) . $this->scripts[$handle]->src;
				echo "<script type='text/javascript' src='$src?ver=$ver'></script>\n";
				$this->printed[] = $handle;
			}
		}
	}
				

	/**
	 * Determines dependencies of scripts
	 *
	 * Recursively builds hierarchical array of script dependencies.  Does NOT catch infinite loops.
	 *
	 * @param mixed handles Accepts (string) script name or (array of strings) script names
	 * @param bool recursion Used internally when function calls itself
	 * @return array Hierarchical array of dependencies
	 */
	function all_deps( $handles, $recursion = false ) {
		if ( ! $handles = (array) $handles )
			return array();
		$return = array();
		foreach ( $handles as $handle ) {
			$handle = explode('?', $handle);
			if ( isset($handle[1]) )
				$this->args[$handle[0]] = $handle[1];
			$handle = $handle[0];
			if ( is_null($return[$handle]) ) // Prime the return array with $handles
				$return[$handle] = true;
			if ( $this->scripts[$handle]->deps ) {
				if ( false !== $return[$handle] && array_diff($this->scripts[$handle]->deps, array_keys($this->scripts)) )
					$return[$handle] = false; // Script required deps which don't exist
				else
					$return[$handle] = $this->all_deps( $this->scripts[$handle]->deps, true ); // Build the hierarchy
			}
			if ( $recursion && false === $return[$handle] )
				return false; // Cut the branch
		}
		return $return;
	}

	/**
	 * Adds script
	 *
	 * Adds the script only if no script of that name already exists
	 *
	 * @param string handle Script name
	 * @param string src Script url
	 * @param array deps (optional) Array of script names on which this script depends
	 * @param string ver (optional) Script version (used for cache busting)
	 * @return array Hierarchical array of dependencies
	 */
	function add( $handle, $src, $deps = array(), $ver = false ) {
		if ( isset($this->scripts[$handle]) )
			return false;
		$this->scripts[$handle] = new _WP_Script( $handle, $src, $deps, $ver );
		return true;
	}

	function remove( $handles ) {
		foreach ( (array) $handles as $handle )
			unset($this->scripts[$handle]);
	}

	function enqueue( $handles ) {
		foreach ( (array) $handles as $handle ) {
			$handle = explode('?', $handle);
			if ( !in_array($handle[0], $this->queue) && isset($this->scripts[$handle[0]]) ) {
				$this->queue[] = $handle[0];
				if ( isset($handle[1]) )
					$this->args[$handle[0]] = $handle[1];
			}
		}
	}

	function dequeue( $handles ) {
		foreach ( (array) $handles as $handle )
			unset( $this->queue[$handle] );
	}

	function query( $handle, $list = 'scripts' ) { // scripts, queue, or printed
		switch ( $list ) :
		case 'scripts':
			if ( isset($this->scripts[$handle]) )
				return $this->scripts[$handle];
			break;
		default:
			if ( in_array($handle, $this->$list) )
				return true;
			break;
		endswitch;
		return false;
	}
			
}

class _WP_Script {
	var $handle;
	var $src;
	var $deps = array();
	var $ver = false;
	var $args = false;

	function _WP_Script() {
		@list($this->handle, $this->src, $this->deps, $this->ver) = func_get_args();
		if ( !is_array($this->deps) )
			$this->deps = array();
		if ( !$this->ver )
			$this->ver = false;
	}
}

/**
 * Prints script tags in document head
 *
 * Called by admin-header.php and by wp_head hook. Since it is called by wp_head on every page load,
 * the function does not instantiate the WP_Scripts object unless script names are explicitly passed.
 * Does make use of already instantiated $wp_scripts if present.
 * Use provided wp_print_scripts hook to register/enqueue new scripts.
 *
 * @see WP_Scripts::print_scripts()
 */
function wp_print_scripts( $handles = false ) {
	do_action( 'wp_print_scripts' );
	if ( '' === $handles ) // for wp_head
		$handles = false;

	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') ) {
		if ( !$handles )
			return array(); // No need to instantiate if nothing's there.
		else
			$wp_scripts = new WP_Scripts();
	}

	return $wp_scripts->print_scripts( $handles );
}

function wp_register_script( $handle, $src, $deps = array(), $ver = false ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	$wp_scripts->add( $handle, $src, $deps, $ver );
}

function wp_deregister_script( $handle ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	$wp_scripts->remove( $handle );
}

/**
 * Equeues script
 *
 * Registers the script if src provided (does NOT overwrite) and enqueues.
 *
 * @see WP_Script::add(), WP_Script::enqueue()
*/
function wp_enqueue_script( $handle, $src = false, $deps = array(), $ver = false ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_scripts->add( $_handle[0], $src, $deps, $ver );
	}
	$wp_scripts->enqueue( $handle );
}
?>
