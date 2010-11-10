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
	var $content_url;
	var $default_version;
	var $in_footer = array();
	var $concat = '';
	var $concat_version = '';
	var $do_concat = false;
	var $print_html = '';
	var $print_code = '';
	var $ext_handles = '';
	var $ext_version = '';
	var $default_dirs;

	function __construct() {
		do_action_ref_array( 'wp_default_scripts', array(&$this) );
	}

	/**
	 * Prints scripts
	 *
	 * Prints the scripts passed to it or the print queue.  Also prints all necessary dependencies.
	 *
	 * @param mixed handles (optional) Scripts to be printed.  (void) prints queue, (string) prints that script, (array of strings) prints those scripts.
	 * @param int group (optional) If scripts were queued in groups prints this group number.
	 * @return array Scripts that have been printed
	 */
	function print_scripts( $handles = false, $group = false ) {
		return $this->do_items( $handles, $group );
	}

	function print_scripts_l10n( $handle, $echo = true ) {
		if ( empty($this->registered[$handle]->extra['l10n']) || empty($this->registered[$handle]->extra['l10n'][0]) || !is_array($this->registered[$handle]->extra['l10n'][1]) )
			return false;

		$object_name = $this->registered[$handle]->extra['l10n'][0];

		$data = "var $object_name = {\n";
		$eol = '';
		foreach ( $this->registered[$handle]->extra['l10n'][1] as $var => $val ) {
			if ( 'l10n_print_after' == $var ) {
				$after = $val;
				continue;
			}
			$data .= "$eol\t$var: \"" . esc_js( $val ) . '"';
			$eol = ",\n";
		}
		$data .= "\n};\n";
		$data .= isset($after) ? "$after\n" : '';

		if ( $echo ) {
			echo "<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n";
			echo $data;
			echo "/* ]]> */\n";
			echo "</script>\n";
			return true;
		} else {
			return $data;
		}
	}

	function do_item( $handle, $group = false ) {
		if ( !parent::do_item($handle) )
			return false;

		if ( 0 === $group && $this->groups[$handle] > 0 ) {
			$this->in_footer[] = $handle;
			return false;
		}

		if ( false === $group && in_array($handle, $this->in_footer, true) )
			$this->in_footer = array_diff( $this->in_footer, (array) $handle );

		if ( null === $this->registered[$handle]->ver )
			$ver = '';
		else
			$ver = $this->registered[$handle]->ver ? $this->registered[$handle]->ver : $this->default_version;

		if ( isset($this->args[$handle]) )
			$ver = $ver ? $ver . '&amp;' . $this->args[$handle] : $this->args[$handle];

		$src = $this->registered[$handle]->src;

		if ( $this->do_concat ) {
			$srce = apply_filters( 'script_loader_src', $src, $handle );
			if ( $this->in_default_dir($srce) ) {
				$this->print_code .= $this->print_scripts_l10n( $handle, false );
				$this->concat .= "$handle,";
				$this->concat_version .= "$handle$ver";
				return true;
			} else {
				$this->ext_handles .= "$handle,";
				$this->ext_version .= "$handle$ver";
			}
		}

		$this->print_scripts_l10n( $handle );
		if ( !preg_match('|^https?://|', $src) && ! ( $this->content_url && 0 === strpos($src, $this->content_url) ) ) {
			$src = $this->base_url . $src;
		}

		if ( !empty($ver) )
			$src = add_query_arg('ver', $ver, $src);
		$src = esc_url(apply_filters( 'script_loader_src', $src, $handle ));

		if ( $this->do_concat )
			$this->print_html .= "<script type='text/javascript' src='$src'></script>\n";
		else
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

	function set_group( $handle, $recursion, $group = false ) {
		$grp = isset($this->registered[$handle]->extra['group']) ? (int) $this->registered[$handle]->extra['group'] : 0;
		if ( false !== $group && $grp > $group )
			$grp = $group;

		return parent::set_group( $handle, $recursion, $grp );
	}

	function all_deps( $handles, $recursion = false, $group = false ) {
		$r = parent::all_deps( $handles, $recursion );
		if ( !$recursion )
			$this->to_do = apply_filters( 'print_scripts_array', $this->to_do );
		return $r;
	}

	function do_head_items() {
		$this->do_items(false, 0);
		return $this->done;
	}

	function do_footer_items() {
		if ( !empty($this->in_footer) ) {
			foreach( $this->in_footer as $key => $handle ) {
				if ( !in_array($handle, $this->done, true) && isset($this->registered[$handle]) ) {
					$this->do_item($handle);
					$this->done[] = $handle;
					unset( $this->in_footer[$key] );
				}
			}
		}
		return $this->done;
	}

	function in_default_dir($src) {
		if ( ! $this->default_dirs )
			return true;

		if ( 0 === strpos( $src, '/wp-includes/js/l10n' ) )
			return false;
			
		foreach ( (array) $this->default_dirs as $test ) {
			if ( 0 === strpos($src, $test) )
				return true;
		}
		return false;
	}

	function reset() {
		$this->do_concat = false;
		$this->print_code = '';
		$this->concat = '';
		$this->concat_version = '';
		$this->print_html = '';
		$this->ext_version = '';
		$this->ext_handles = '';
	}
}
