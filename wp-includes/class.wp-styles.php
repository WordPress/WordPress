<?php

class WP_Styles extends WP_Dependencies {
	var $base_url;
	var $default_version;

	function __construct() {
		do_action_ref_array( 'wp_default_styles', array(&$this) );
	}

	function do_item( $handle ) {
		if ( !parent::do_item($handle) )
			return false;

		$ver = $this->registered[$handle]->ver ? $this->registered[$handle]->ver : $this->default_version;
		if ( isset($this->args[$handle]) )
			$ver .= '&amp;' . $this->args[$handle];

		if ( isset($this->registered[$handle]->args) )
			$media = attribute_escape( $this->registered[$handle]->args );
		else
			$media = 'all';

		$src = $this->registered[$handle]->src;
		if ( !preg_match('|^https?://|', $src) ) {
			$src = $this->base_url . $src;
		}

		$src = add_query_arg('ver', $ver, $src);
		$src = clean_url(apply_filters( 'style_loader_src', $src ));

		echo "<link rel='stylesheet' href='$src' type='text/css' media='$media' />\n";

		// Could do something with $this->registered[$handle]->extra here to print out extra CSS rules
//		echo "<style type='text/css'>\n";
//		echo "/* <![CDATA[ */\n";
//		echo "/* ]]> */\n";
//		echo "</style>\n";

		return true;
	}

	function all_deps( $handles, $recursion = false ) {
		$r = parent::all_deps( $handles, $recursion );
		if ( !$recursion )
			$this->to_do = apply_filters( 'print_styles_array', $this->to_do );
		return $r;
	}
}
