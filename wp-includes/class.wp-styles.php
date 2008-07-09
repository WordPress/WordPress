<?php

class WP_Styles extends WP_Dependencies {
	var $base_url;
	var $default_version;
	var $text_direction = 'ltr';

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

		$href = $this->_css_href( $this->registered[$handle]->src, $ver, $handle );

		$end_cond = '';
		if ( isset($this->registered[$handle]->extra['conditional']) && $this->registered[$handle]->extra['conditional'] ) {
			echo "<!--[if {$this->registered[$handle]->extra['conditional']}]>\n";
			$end_cond = "<![endif]-->\n";
		}

		echo apply_filters( 'style_loader_tag', "<link rel='stylesheet' href='$href' type='text/css' media='$media' />\n", $handle );
		if ( 'rtl' === $this->text_direction && isset($this->registered[$handle]->extra['rtl']) && $this->registered[$handle]->extra['rtl'] ) {
			if ( is_bool( $this->registered[$handle]->extra['rtl'] ) )
				$rtl_href = str_replace( '.css', '-rtl.css', $href );
			else
				$rtl_href = $this->_css_href( $this->registered[$handle]->extra['rtl'], $ver, "$handle-rtl" );

			echo apply_filters( 'style_loader_tag', "<link rel='stylesheet' href='$rtl_href' type='text/css' media='$media' />\n", $handle );
		}

		echo $end_cond;

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

	function _css_href( $src, $ver, $handle ) {
		if ( !preg_match('|^https?://|', $src) && !preg_match('|^' . preg_quote(WP_CONTENT_URL) . '|', $src) ) {
			$src = $this->base_url . $src;
		}

		$src = add_query_arg('ver', $ver, $src);
		$src = apply_filters( 'style_loader_src', $src, $handle );
		return clean_url( $src );
	}

}
