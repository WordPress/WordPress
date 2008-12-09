<?php
/**
 * BackPress Scripts enqueue.
 *
 * These classes were refactored from the WordPress WP_Scripts and WordPress
 * script enqueue API.
 *
 * @package BackPress
 * @since r16
 */

/**
 * BackPress Scripts enqueue class.
 *
 * @package BackPress
 * @uses WP_Dependencies
 * @since r16
 */
class WP_Scripts extends WP_Dependencies {
	var $base_url; // Full URL with trailing slash
	var $default_version;

	function __construct() {
		do_action_ref_array( 'wp_default_scripts', array(&$this) );
	}

	/**
	 * Prints scripts
	 *
	 * Prints the scripts passed to it or the print queue.  Also prints all necessary dependencies.
	 *
	 * @param mixed handles (optional) Scripts to be printed.  (void) prints queue, (string) prints that script, (array of strings) prints those scripts.
	 * @return array Scripts that have been printed
	 */
	function print_scripts( $handles = false ) {
		return $this->do_items( $handles );
	}

	function print_scripts_l10n( $handle ) {
		if ( empty($this->registered[$handle]->extra['l10n']) || empty($this->registered[$handle]->extra['l10n'][0]) || !is_array($this->registered[$handle]->extra['l10n'][1]) )
			return false;

		$object_name = $this->registered[$handle]->extra['l10n'][0];

		echo "<script type='text/javascript'>\n";
		echo "/* <![CDATA[ */\n";
		echo "\t$object_name = {\n";
		$eol = '';
		foreach ( $this->registered[$handle]->extra['l10n'][1] as $var => $val ) {
			if ( 'l10n_print_after' == $var ) {
				$after = $val;
				continue;
			}
			echo "$eol\t\t$var: \"" . js_escape( $val ) . '"';
			$eol = ",\n";
		}
		echo "\n\t}\n";
		echo isset($after) ? "\t$after\n" : '';
		echo "/* ]]> */\n";
		echo "</script>\n";

		return true;
	}

	function do_item( $handle ) {
		if ( !parent::do_item($handle) )
			return false;

		$ver = $this->registered[$handle]->ver ? $this->registered[$handle]->ver : $this->default_version;
		if ( isset($this->args[$handle]) )
			$ver .= '&amp;' . $this->args[$handle];

		$src = $this->registered[$handle]->src;
		if ( !preg_match('|^https?://|', $src) && !preg_match('|^' . preg_quote(WP_CONTENT_URL) . '|', $src) ) {
			$src = $this->base_url . $src;
		}

		$src = add_query_arg('ver', $ver, $src);
		$src = clean_url(apply_filters( 'script_loader_src', $src, $handle ));

		$this->print_scripts_l10n( $handle );

		echo "<script type='text/javascript' src='$src'></script>\n";

		return true;
	}

	/**
	 * Localizes a script
	 *
	 * Localizes only if script has already been added
	 *
	 * @param string handle Script name
	 * @param string object_name Name of JS object to hold l10n info
	 * @param array l10n Array of JS var name => localized string
	 * @return bool Successful localization
	 */
	function localize( $handle, $object_name, $l10n ) {
		if ( !$object_name || !$l10n )
			return false;
		return $this->add_data( $handle, 'l10n', array( $object_name, $l10n ) );
	}

	function all_deps( $handles, $recursion = false ) {
		$r = parent::all_deps( $handles, $recursion );
		if ( !$recursion )
			$this->to_do = apply_filters( 'print_scripts_array', $this->to_do );
		return $r;
	}
}
