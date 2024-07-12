<?php
/**
 * kses 0.2.2 - HTML/XHTML filter that only allows some elements and attributes
 * Copyright (C) 2002, 2003, 2005  Ulf Harnhammar
 *
 * This program is free software and open source software; you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA
 * http://www.gnu.org/licenses/gpl.html
 *
 * [kses strips evil scripts!]
 *
 * Added wp_ prefix to avoid conflicts with existing kses users
 *
 * @version 0.2.2
 * @copyright (C) 2002, 2003, 2005
 * @author Ulf Harnhammar <http://advogato.org/person/metaur/>
 *
 * @package External
 * @subpackage KSES
 */

/**
 * Specifies the default allowable HTML tags.
 *
 * Using `CUSTOM_TAGS` is not recommended and should be considered deprecated. The
 * {@see 'wp_kses_allowed_html'} filter is more powerful and supplies context.
 *
 * When using this constant, make sure to set all of these globals to arrays:
 *
 *  - `$allowedposttags`
 *  - `$allowedtags`
 *  - `$allowedentitynames`
 *  - `$allowedxmlentitynames`
 *
 * @see wp_kses_allowed_html()
 * @since 1.2.0
 *
 * @var array[]|false Array of default allowable HTML tags, or false to use the defaults.
 */
if ( ! defined( 'CUSTOM_TAGS' ) ) {
	define( 'CUSTOM_TAGS', false );
}

// Ensure that these variables are added to the global namespace
// (e.g. if using namespaces / autoload in the current PHP environment).
global $allowedposttags, $allowedtags, $allowedentitynames, $allowedxmlentitynames;

if ( ! CUSTOM_TAGS ) {
	/**
	 * KSES global for default allowable HTML tags.
	 *
	 * Can be overridden with the `CUSTOM_TAGS` constant.
	 *
	 * @var array[] $allowedposttags Array of default allowable HTML tags.
	 * @since 2.0.0
	 */
	$allowedposttags = array(
		'address'    => array(),
		'a'          => array(
			'href'     => true,
			'rel'      => true,
			'rev'      => true,
			'name'     => true,
			'target'   => true,
			'download' => array(
				'valueless' => 'y',
			),
		),
		'abbr'       => array(),
		'acronym'    => array(),
		'area'       => array(
			'alt'    => true,
			'coords' => true,
			'href'   => true,
			'nohref' => true,
			'shape'  => true,
			'target' => true,
		),
		'article'    => array(
			'align' => true,
		),
		'aside'      => array(
			'align' => true,
		),
		'audio'      => array(
			'autoplay' => true,
			'controls' => true,
			'loop'     => true,
			'muted'    => true,
			'preload'  => true,
			'src'      => true,
		),
		'b'          => array(),
		'bdo'        => array(),
		'big'        => array(),
		'blockquote' => array(
			'cite' => true,
		),
		'br'         => array(),
		'button'     => array(
			'disabled' => true,
			'name'     => true,
			'type'     => true,
			'value'    => true,
		),
		'caption'    => array(
			'align' => true,
		),
		'cite'       => array(),
		'code'       => array(),
		'col'        => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'span'    => true,
			'valign'  => true,
			'width'   => true,
		),
		'colgroup'   => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'span'    => true,
			'valign'  => true,
			'width'   => true,
		),
		'del'        => array(
			'datetime' => true,
		),
		'dd'         => array(),
		'dfn'        => array(),
		'details'    => array(
			'align' => true,
			'open'  => true,
		),
		'div'        => array(
			'align' => true,
		),
		'dl'         => array(),
		'dt'         => array(),
		'em'         => array(),
		'fieldset'   => array(),
		'figure'     => array(
			'align' => true,
		),
		'figcaption' => array(
			'align' => true,
		),
		'font'       => array(
			'color' => true,
			'face'  => true,
			'size'  => true,
		),
		'footer'     => array(
			'align' => true,
		),
		'h1'         => array(
			'align' => true,
		),
		'h2'         => array(
			'align' => true,
		),
		'h3'         => array(
			'align' => true,
		),
		'h4'         => array(
			'align' => true,
		),
		'h5'         => array(
			'align' => true,
		),
		'h6'         => array(
			'align' => true,
		),
		'header'     => array(
			'align' => true,
		),
		'hgroup'     => array(
			'align' => true,
		),
		'hr'         => array(
			'align'   => true,
			'noshade' => true,
			'size'    => true,
			'width'   => true,
		),
		'i'          => array(),
		'img'        => array(
			'alt'      => true,
			'align'    => true,
			'border'   => true,
			'height'   => true,
			'hspace'   => true,
			'loading'  => true,
			'longdesc' => true,
			'vspace'   => true,
			'src'      => true,
			'usemap'   => true,
			'width'    => true,
		),
		'ins'        => array(
			'datetime' => true,
			'cite'     => true,
		),
		'kbd'        => array(),
		'label'      => array(
			'for' => true,
		),
		'legend'     => array(
			'align' => true,
		),
		'li'         => array(
			'align' => true,
			'value' => true,
		),
		'main'       => array(
			'align' => true,
		),
		'map'        => array(
			'name' => true,
		),
		'mark'       => array(),
		'menu'       => array(
			'type' => true,
		),
		'nav'        => array(
			'align' => true,
		),
		'object'     => array(
			'data' => array(
				'required'       => true,
				'value_callback' => '_wp_kses_allow_pdf_objects',
			),
			'type' => array(
				'required' => true,
				'values'   => array( 'application/pdf' ),
			),
		),
		'p'          => array(
			'align' => true,
		),
		'pre'        => array(
			'width' => true,
		),
		'q'          => array(
			'cite' => true,
		),
		'rb'         => array(),
		'rp'         => array(),
		'rt'         => array(),
		'rtc'        => array(),
		'ruby'       => array(),
		's'          => array(),
		'samp'       => array(),
		'span'       => array(
			'align' => true,
		),
		'section'    => array(
			'align' => true,
		),
		'small'      => array(),
		'strike'     => array(),
		'strong'     => array(),
		'sub'        => array(),
		'summary'    => array(
			'align' => true,
		),
		'sup'        => array(),
		'table'      => array(
			'align'       => true,
			'bgcolor'     => true,
			'border'      => true,
			'cellpadding' => true,
			'cellspacing' => true,
			'rules'       => true,
			'summary'     => true,
			'width'       => true,
		),
		'tbody'      => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'valign'  => true,
		),
		'td'         => array(
			'abbr'    => true,
			'align'   => true,
			'axis'    => true,
			'bgcolor' => true,
			'char'    => true,
			'charoff' => true,
			'colspan' => true,
			'headers' => true,
			'height'  => true,
			'nowrap'  => true,
			'rowspan' => true,
			'scope'   => true,
			'valign'  => true,
			'width'   => true,
		),
		'textarea'   => array(
			'cols'     => true,
			'rows'     => true,
			'disabled' => true,
			'name'     => true,
			'readonly' => true,
		),
		'tfoot'      => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'valign'  => true,
		),
		'th'         => array(
			'abbr'    => true,
			'align'   => true,
			'axis'    => true,
			'bgcolor' => true,
			'char'    => true,
			'charoff' => true,
			'colspan' => true,
			'headers' => true,
			'height'  => true,
			'nowrap'  => true,
			'rowspan' => true,
			'scope'   => true,
			'valign'  => true,
			'width'   => true,
		),
		'thead'      => array(
			'align'   => true,
			'char'    => true,
			'charoff' => true,
			'valign'  => true,
		),
		'title'      => array(),
		'tr'         => array(
			'align'   => true,
			'bgcolor' => true,
			'char'    => true,
			'charoff' => true,
			'valign'  => true,
		),
		'track'      => array(
			'default' => true,
			'kind'    => true,
			'label'   => true,
			'src'     => true,
			'srclang' => true,
		),
		'tt'         => array(),
		'u'          => array(),
		'ul'         => array(
			'type' => true,
		),
		'ol'         => array(
			'start'    => true,
			'type'     => true,
			'reversed' => true,
		),
		'var'        => array(),
		'video'      => array(
			'autoplay'    => true,
			'controls'    => true,
			'height'      => true,
			'loop'        => true,
			'muted'       => true,
			'playsinline' => true,
			'poster'      => true,
			'preload'     => true,
			'src'         => true,
			'width'       => true,
		),
	);

	/**
	 * @var array[] $allowedtags Array of KSES allowed HTML elements.
	 * @since 1.0.0
	 */
	$allowedtags = array(
		'a'          => array(
			'href'  => true,
			'title' => true,
		),
		'abbr'       => array(
			'title' => true,
		),
		'acronym'    => array(
			'title' => true,
		),
		'b'          => array(),
		'blockquote' => array(
			'cite' => true,
		),
		'cite'       => array(),
		'code'       => array(),
		'del'        => array(
			'datetime' => true,
		),
		'em'         => array(),
		'i'          => array(),
		'q'          => array(
			'cite' => true,
		),
		's'          => array(),
		'strike'     => array(),
		'strong'     => array(),
	);

	/**
	 * @var string[] $allowedentitynames Array of KSES allowed HTML entity names.
	 * @since 1.0.0
	 */
	$allowedentitynames = array(
		'nbsp',
		'iexcl',
		'cent',
		'pound',
		'curren',
		'yen',
		'brvbar',
		'sect',
		'uml',
		'copy',
		'ordf',
		'laquo',
		'not',
		'shy',
		'reg',
		'macr',
		'deg',
		'plusmn',
		'acute',
		'micro',
		'para',
		'middot',
		'cedil',
		'ordm',
		'raquo',
		'iquest',
		'Agrave',
		'Aacute',
		'Acirc',
		'Atilde',
		'Auml',
		'Aring',
		'AElig',
		'Ccedil',
		'Egrave',
		'Eacute',
		'Ecirc',
		'Euml',
		'Igrave',
		'Iacute',
		'Icirc',
		'Iuml',
		'ETH',
		'Ntilde',
		'Ograve',
		'Oacute',
		'Ocirc',
		'Otilde',
		'Ouml',
		'times',
		'Oslash',
		'Ugrave',
		'Uacute',
		'Ucirc',
		'Uuml',
		'Yacute',
		'THORN',
		'szlig',
		'agrave',
		'aacute',
		'acirc',
		'atilde',
		'auml',
		'aring',
		'aelig',
		'ccedil',
		'egrave',
		'eacute',
		'ecirc',
		'euml',
		'igrave',
		'iacute',
		'icirc',
		'iuml',
		'eth',
		'ntilde',
		'ograve',
		'oacute',
		'ocirc',
		'otilde',
		'ouml',
		'divide',
		'oslash',
		'ugrave',
		'uacute',
		'ucirc',
		'uuml',
		'yacute',
		'thorn',
		'yuml',
		'quot',
		'amp',
		'lt',
		'gt',
		'apos',
		'OElig',
		'oelig',
		'Scaron',
		'scaron',
		'Yuml',
		'circ',
		'tilde',
		'ensp',
		'emsp',
		'thinsp',
		'zwnj',
		'zwj',
		'lrm',
		'rlm',
		'ndash',
		'mdash',
		'lsquo',
		'rsquo',
		'sbquo',
		'ldquo',
		'rdquo',
		'bdquo',
		'dagger',
		'Dagger',
		'permil',
		'lsaquo',
		'rsaquo',
		'euro',
		'fnof',
		'Alpha',
		'Beta',
		'Gamma',
		'Delta',
		'Epsilon',
		'Zeta',
		'Eta',
		'Theta',
		'Iota',
		'Kappa',
		'Lambda',
		'Mu',
		'Nu',
		'Xi',
		'Omicron',
		'Pi',
		'Rho',
		'Sigma',
		'Tau',
		'Upsilon',
		'Phi',
		'Chi',
		'Psi',
		'Omega',
		'alpha',
		'beta',
		'gamma',
		'delta',
		'epsilon',
		'zeta',
		'eta',
		'theta',
		'iota',
		'kappa',
		'lambda',
		'mu',
		'nu',
		'xi',
		'omicron',
		'pi',
		'rho',
		'sigmaf',
		'sigma',
		'tau',
		'upsilon',
		'phi',
		'chi',
		'psi',
		'omega',
		'thetasym',
		'upsih',
		'piv',
		'bull',
		'hellip',
		'prime',
		'Prime',
		'oline',
		'frasl',
		'weierp',
		'image',
		'real',
		'trade',
		'alefsym',
		'larr',
		'uarr',
		'rarr',
		'darr',
		'harr',
		'crarr',
		'lArr',
		'uArr',
		'rArr',
		'dArr',
		'hArr',
		'forall',
		'part',
		'exist',
		'empty',
		'nabla',
		'isin',
		'notin',
		'ni',
		'prod',
		'sum',
		'minus',
		'lowast',
		'radic',
		'prop',
		'infin',
		'ang',
		'and',
		'or',
		'cap',
		'cup',
		'int',
		'sim',
		'cong',
		'asymp',
		'ne',
		'equiv',
		'le',
		'ge',
		'sub',
		'sup',
		'nsub',
		'sube',
		'supe',
		'oplus',
		'otimes',
		'perp',
		'sdot',
		'lceil',
		'rceil',
		'lfloor',
		'rfloor',
		'lang',
		'rang',
		'loz',
		'spades',
		'clubs',
		'hearts',
		'diams',
		'sup1',
		'sup2',
		'sup3',
		'frac14',
		'frac12',
		'frac34',
		'there4',
	);

	/**
	 * @var string[] $allowedxmlentitynames Array of KSES allowed XML entity names.
	 * @since 5.5.0
	 */
	$allowedxmlentitynames = array(
		'amp',
		'lt',
		'gt',
		'apos',
		'quot',
	);

	$allowedposttags = array_map( '_wp_add_global_attributes', $allowedposttags );
} else {
	$required_kses_globals = array(
		'allowedposttags',
		'allowedtags',
		'allowedentitynames',
		'allowedxmlentitynames',
	);
	$missing_kses_globals  = array();

	foreach ( $required_kses_globals as $global_name ) {
		if ( ! isset( $GLOBALS[ $global_name ] ) || ! is_array( $GLOBALS[ $global_name ] ) ) {
			$missing_kses_globals[] = '<code>$' . $global_name . '</code>';
		}
	}

	if ( $missing_kses_globals ) {
		_doing_it_wrong(
			'wp_kses_allowed_html',
			sprintf(
				/* translators: 1: CUSTOM_TAGS, 2: Global variable names. */
				__( 'When using the %1$s constant, make sure to set these globals to an array: %2$s.' ),
				'<code>CUSTOM_TAGS</code>',
				implode( ', ', $missing_kses_globals )
			),
			'6.2.0'
		);
	}

	$allowedtags     = wp_kses_array_lc( $allowedtags );
	$allowedposttags = wp_kses_array_lc( $allowedposttags );
}

/**
 * Filters text content and strips out disallowed HTML.
 *
 * This function makes sure that only the allowed HTML element names, attribute
 * names, attribute values, and HTML entities will occur in the given text string.
 *
 * This function expects unslashed data.
 *
 * @see wp_kses_post() for specifically filtering post content and fields.
 * @see wp_allowed_protocols() for the default allowed protocols in link URLs.
 *
 * @since 1.0.0
 *
 * @param string         $content           Text content to filter.
 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
 *                                          for the list of accepted context names.
 * @param string[]       $allowed_protocols Optional. Array of allowed URL protocols.
 *                                          Defaults to the result of wp_allowed_protocols().
 * @return string Filtered content containing only the allowed HTML.
 */
function wp_kses( $content, $allowed_html, $allowed_protocols = array() ) {
	if ( empty( $allowed_protocols ) ) {
		$allowed_protocols = wp_allowed_protocols();
	}

	$content = wp_kses_no_null( $content, array( 'slash_zero' => 'keep' ) );
	$content = wp_kses_normalize_entities( $content );
	$content = wp_kses_hook( $content, $allowed_html, $allowed_protocols );

	return wp_kses_split( $content, $allowed_html, $allowed_protocols );
}

/**
 * Filters one HTML attribute and ensures its value is allowed.
 *
 * This function can escape data in some situations where `wp_kses()` must strip the whole attribute.
 *
 * @since 4.2.3
 *
 * @param string $attr    The 'whole' attribute, including name and value.
 * @param string $element The HTML element name to which the attribute belongs.
 * @return string Filtered attribute.
 */
function wp_kses_one_attr( $attr, $element ) {
	$uris              = wp_kses_uri_attributes();
	$allowed_html      = wp_kses_allowed_html( 'post' );
	$allowed_protocols = wp_allowed_protocols();
	$attr              = wp_kses_no_null( $attr, array( 'slash_zero' => 'keep' ) );

	// Preserve leading and trailing whitespace.
	$matches = array();
	preg_match( '/^\s*/', $attr, $matches );
	$lead = $matches[0];
	preg_match( '/\s*$/', $attr, $matches );
	$trail = $matches[0];
	if ( empty( $trail ) ) {
		$attr = substr( $attr, strlen( $lead ) );
	} else {
		$attr = substr( $attr, strlen( $lead ), -strlen( $trail ) );
	}

	// Parse attribute name and value from input.
	$split = preg_split( '/\s*=\s*/', $attr, 2 );
	$name  = $split[0];
	if ( count( $split ) === 2 ) {
		$value = $split[1];

		/*
		 * Remove quotes surrounding $value.
		 * Also guarantee correct quoting in $attr for this one attribute.
		 */
		if ( '' === $value ) {
			$quote = '';
		} else {
			$quote = $value[0];
		}
		if ( '"' === $quote || "'" === $quote ) {
			if ( ! str_ends_with( $value, $quote ) ) {
				return '';
			}
			$value = substr( $value, 1, -1 );
		} else {
			$quote = '"';
		}

		// Sanitize quotes, angle braces, and entities.
		$value = esc_attr( $value );

		// Sanitize URI values.
		if ( in_array( strtolower( $name ), $uris, true ) ) {
			$value = wp_kses_bad_protocol( $value, $allowed_protocols );
		}

		$attr  = "$name=$quote$value$quote";
		$vless = 'n';
	} else {
		$value = '';
		$vless = 'y';
	}

	// Sanitize attribute by name.
	wp_kses_attr_check( $name, $value, $attr, $vless, $element, $allowed_html );

	// Restore whitespace.
	return $lead . $attr . $trail;
}

/**
 * Returns an array of allowed HTML tags and attributes for a given context.
 *
 * @since 3.5.0
 * @since 5.0.1 `form` removed as allowable HTML tag.
 *
 * @global array $allowedposttags
 * @global array $allowedtags
 * @global array $allowedentitynames
 *
 * @param string|array $context The context for which to retrieve tags. Allowed values are 'post',
 *                              'strip', 'data', 'entities', or the name of a field filter such as
 *                              'pre_user_description', or an array of allowed HTML elements and attributes.
 * @return array Array of allowed HTML tags and their allowed attributes.
 */
function wp_kses_allowed_html( $context = '' ) {
	global $allowedposttags, $allowedtags, $allowedentitynames;

	if ( is_array( $context ) ) {
		// When `$context` is an array it's actually an array of allowed HTML elements and attributes.
		$html    = $context;
		$context = 'explicit';

		/**
		 * Filters the HTML tags that are allowed for a given context.
		 *
		 * HTML tags and attribute names are case-insensitive in HTML but must be
		 * added to the KSES allow list in lowercase. An item added to the allow list
		 * in upper or mixed case will not recognized as permitted by KSES.
		 *
		 * @since 3.5.0
		 *
		 * @param array[] $html    Allowed HTML tags.
		 * @param string  $context Context name.
		 */
		return apply_filters( 'wp_kses_allowed_html', $html, $context );
	}

	switch ( $context ) {
		case 'post':
			/** This filter is documented in wp-includes/kses.php */
			$tags = apply_filters( 'wp_kses_allowed_html', $allowedposttags, $context );

			// 5.0.1 removed the `<form>` tag, allow it if a filter is allowing it's sub-elements `<input>` or `<select>`.
			if ( ! CUSTOM_TAGS && ! isset( $tags['form'] ) && ( isset( $tags['input'] ) || isset( $tags['select'] ) ) ) {
				$tags = $allowedposttags;

				$tags['form'] = array(
					'action'         => true,
					'accept'         => true,
					'accept-charset' => true,
					'enctype'        => true,
					'method'         => true,
					'name'           => true,
					'target'         => true,
				);

				/** This filter is documented in wp-includes/kses.php */
				$tags = apply_filters( 'wp_kses_allowed_html', $tags, $context );
			}

			return $tags;

		case 'user_description':
		case 'pre_user_description':
			$tags             = $allowedtags;
			$tags['a']['rel'] = true;
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', $tags, $context );

		case 'strip':
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', array(), $context );

		case 'entities':
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', $allowedentitynames, $context );

		case 'data':
		default:
			/** This filter is documented in wp-includes/kses.php */
			return apply_filters( 'wp_kses_allowed_html', $allowedtags, $context );
	}
}

/**
 * You add any KSES hooks here.
 *
 * There is currently only one KSES WordPress hook, {@see 'pre_kses'}, and it is called here.
 * All parameters are passed to the hooks and expected to receive a string.
 *
 * @since 1.0.0
 *
 * @param string         $content           Content to filter through KSES.
 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
 *                                          for the list of accepted context names.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 * @return string Filtered content through {@see 'pre_kses'} hook.
 */
function wp_kses_hook( $content, $allowed_html, $allowed_protocols ) {
	/**
	 * Filters content to be run through KSES.
	 *
	 * @since 2.3.0
	 *
	 * @param string         $content           Content to filter through KSES.
	 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
	 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
	 *                                          for the list of accepted context names.
	 * @param string[]       $allowed_protocols Array of allowed URL protocols.
	 */
	return apply_filters( 'pre_kses', $content, $allowed_html, $allowed_protocols );
}

/**
 * Returns the version number of KSES.
 *
 * @since 1.0.0
 *
 * @return string KSES version number.
 */
function wp_kses_version() {
	return '0.2.2';
}

/**
 * Searches for HTML tags, no matter how malformed.
 *
 * It also matches stray `>` characters.
 *
 * @since 1.0.0
 * @since 6.6.0 Recognize additional forms of invalid HTML which convert into comments.
 *
 * @global array[]|string $pass_allowed_html      An array of allowed HTML elements and attributes,
 *                                                or a context name such as 'post'.
 * @global string[]       $pass_allowed_protocols Array of allowed URL protocols.
 *
 * @param string         $content           Content to filter.
 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
 *                                          for the list of accepted context names.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 * @return string Content with fixed HTML tags
 */
function wp_kses_split( $content, $allowed_html, $allowed_protocols ) {
	global $pass_allowed_html, $pass_allowed_protocols;

	$pass_allowed_html      = $allowed_html;
	$pass_allowed_protocols = $allowed_protocols;

	$token_pattern = <<<REGEX
~
	(                      # Detect comments of various flavors before attempting to find tags.
		(<!--.*?(-->|$))   #  - Normative HTML comments.
		|
		</[^a-zA-Z][^>]*>  #  - Closing tags with invalid tag names.
		|
		<![^>]*>           #  - Invalid markup declaration nodes. Not all invalid nodes
		                   #    are matched so as to avoid breaking legacy behaviors.
	)
	|
	(<[^>]*(>|$)|>)        # Tag-like spans of text.
~x
REGEX;
	return preg_replace_callback( $token_pattern, '_wp_kses_split_callback', $content );
}

/**
 * Returns an array of HTML attribute names whose value contains a URL.
 *
 * This function returns a list of all HTML attributes that must contain
 * a URL according to the HTML specification.
 *
 * This list includes URI attributes both allowed and disallowed by KSES.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes
 *
 * @since 5.0.1
 *
 * @return string[] HTML attribute names whose value contains a URL.
 */
function wp_kses_uri_attributes() {
	$uri_attributes = array(
		'action',
		'archive',
		'background',
		'cite',
		'classid',
		'codebase',
		'data',
		'formaction',
		'href',
		'icon',
		'longdesc',
		'manifest',
		'poster',
		'profile',
		'src',
		'usemap',
		'xmlns',
	);

	/**
	 * Filters the list of attributes that are required to contain a URL.
	 *
	 * Use this filter to add any `data-` attributes that are required to be
	 * validated as a URL.
	 *
	 * @since 5.0.1
	 *
	 * @param string[] $uri_attributes HTML attribute names whose value contains a URL.
	 */
	$uri_attributes = apply_filters( 'wp_kses_uri_attributes', $uri_attributes );

	return $uri_attributes;
}

/**
 * Callback for `wp_kses_split()`.
 *
 * @since 3.1.0
 * @access private
 * @ignore
 *
 * @global array[]|string $pass_allowed_html      An array of allowed HTML elements and attributes,
 *                                                or a context name such as 'post'.
 * @global string[]       $pass_allowed_protocols Array of allowed URL protocols.
 *
 * @param array $matches preg_replace regexp matches
 * @return string
 */
function _wp_kses_split_callback( $matches ) {
	global $pass_allowed_html, $pass_allowed_protocols;

	return wp_kses_split2( $matches[0], $pass_allowed_html, $pass_allowed_protocols );
}

/**
 * Callback for `wp_kses_split()` for fixing malformed HTML tags.
 *
 * This function does a lot of work. It rejects some very malformed things like
 * `<:::>`. It returns an empty string, if the element isn't allowed (look ma, no
 * `strip_tags()`!). Otherwise it splits the tag into an element and an attribute
 * list.
 *
 * After the tag is split into an element and an attribute list, it is run
 * through another filter which will remove illegal attributes and once that is
 * completed, will be returned.
 *
 * @access private
 * @ignore
 * @since 1.0.0
 * @since 6.6.0 Recognize additional forms of invalid HTML which convert into comments.
 *
 * @param string         $content           Content to filter.
 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
 *                                          for the list of accepted context names.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 *
 * @return string Fixed HTML element
 */
function wp_kses_split2( $content, $allowed_html, $allowed_protocols ) {
	$content = wp_kses_stripslashes( $content );

	/*
	 * The regex pattern used to split HTML into chunks attempts
	 * to split on HTML token boundaries. This function should
	 * thus receive chunks that _either_ start with meaningful
	 * syntax tokens, like a tag `<div>` or a comment `<!-- ... -->`.
	 *
	 * If the first character of the `$content` chunk _isn't_ one
	 * of these syntax elements, which always starts with `<`, then
	 * the match had to be for the final alternation of `>`. In such
	 * case, it's probably standing on its own and could be encoded
	 * with a character reference to remove ambiguity.
	 *
	 * In other words, if this chunk isn't from a match of a syntax
	 * token, it's just a plaintext greater-than (`>`) sign.
	 */
	if ( ! str_starts_with( $content, '<' ) ) {
		return '&gt;';
	}

	/*
	 * When certain invalid syntax constructs appear, the HTML parser
	 * shifts into what's called the "bogus comment state." This is a
	 * plaintext state that consumes everything until the nearest `>`
	 * and then transforms the entire span into an HTML comment.
	 *
	 * Preserve these comments and do not treat them like tags.
	 *
	 * @see https://html.spec.whatwg.org/#bogus-comment-state
	 */
	if ( 1 === preg_match( '~^(?:</[^a-zA-Z][^>]*>|<![a-z][^>]*>)$~', $content ) ) {
		/**
		 * Since the pattern matches `</…>` and also `<!…>`, this will
		 * preserve the type of the cleaned-up token in the output.
		 */
		$opener  = $content[1];
		$content = substr( $content, 2, -1 );

		do {
			$prev    = $content;
			$content = wp_kses( $content, $allowed_html, $allowed_protocols );
		} while ( $prev !== $content );

		// Recombine the modified inner content with the original token structure.
		return "<{$opener}{$content}>";
	}

	/*
	 * Normative HTML comments should be handled separately as their
	 * parsing rules differ from those for tags and text nodes.
	 */
	if ( str_starts_with( $content, '<!--' ) ) {
		$content = str_replace( array( '<!--', '-->' ), '', $content );

		while ( ( $newstring = wp_kses( $content, $allowed_html, $allowed_protocols ) ) !== $content ) {
			$content = $newstring;
		}

		if ( '' === $content ) {
			return '';
		}

		// Prevent multiple dashes in comments.
		$content = preg_replace( '/--+/', '-', $content );
		// Prevent three dashes closing a comment.
		$content = preg_replace( '/-$/', '', $content );

		return "<!--{$content}-->";
	}

	// It's seriously malformed.
	if ( ! preg_match( '%^<\s*(/\s*)?([a-zA-Z0-9-]+)([^>]*)>?$%', $content, $matches ) ) {
		return '';
	}

	$slash    = trim( $matches[1] );
	$elem     = $matches[2];
	$attrlist = $matches[3];

	if ( ! is_array( $allowed_html ) ) {
		$allowed_html = wp_kses_allowed_html( $allowed_html );
	}

	// They are using a not allowed HTML element.
	if ( ! isset( $allowed_html[ strtolower( $elem ) ] ) ) {
		return '';
	}

	// No attributes are allowed for closing elements.
	if ( '' !== $slash ) {
		return "</$elem>";
	}

	return wp_kses_attr( $elem, $attrlist, $allowed_html, $allowed_protocols );
}

/**
 * Removes all attributes, if none are allowed for this element.
 *
 * If some are allowed it calls `wp_kses_hair()` to split them further, and then
 * it builds up new HTML code from the data that `wp_kses_hair()` returns. It also
 * removes `<` and `>` characters, if there are any left. One more thing it does
 * is to check if the tag has a closing XHTML slash, and if it does, it puts one
 * in the returned code as well.
 *
 * An array of allowed values can be defined for attributes. If the attribute value
 * doesn't fall into the list, the attribute will be removed from the tag.
 *
 * Attributes can be marked as required. If a required attribute is not present,
 * KSES will remove all attributes from the tag. As KSES doesn't match opening and
 * closing tags, it's not possible to safely remove the tag itself, the safest
 * fallback is to strip all attributes from the tag, instead.
 *
 * @since 1.0.0
 * @since 5.9.0 Added support for an array of allowed values for attributes.
 *              Added support for required attributes.
 *
 * @param string         $element           HTML element/tag.
 * @param string         $attr              HTML attributes from HTML element to closing HTML element tag.
 * @param array[]|string $allowed_html      An array of allowed HTML elements and attributes,
 *                                          or a context name such as 'post'. See wp_kses_allowed_html()
 *                                          for the list of accepted context names.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 * @return string Sanitized HTML element.
 */
function wp_kses_attr( $element, $attr, $allowed_html, $allowed_protocols ) {
	if ( ! is_array( $allowed_html ) ) {
		$allowed_html = wp_kses_allowed_html( $allowed_html );
	}

	// Is there a closing XHTML slash at the end of the attributes?
	$xhtml_slash = '';
	if ( preg_match( '%\s*/\s*$%', $attr ) ) {
		$xhtml_slash = ' /';
	}

	// Are any attributes allowed at all for this element?
	$element_low = strtolower( $element );
	if ( empty( $allowed_html[ $element_low ] ) || true === $allowed_html[ $element_low ] ) {
		return "<$element$xhtml_slash>";
	}

	// Split it.
	$attrarr = wp_kses_hair( $attr, $allowed_protocols );

	// Check if there are attributes that are required.
	$required_attrs = array_filter(
		$allowed_html[ $element_low ],
		static function ( $required_attr_limits ) {
			return isset( $required_attr_limits['required'] ) && true === $required_attr_limits['required'];
		}
	);

	/*
	 * If a required attribute check fails, we can return nothing for a self-closing tag,
	 * but for a non-self-closing tag the best option is to return the element with attributes,
	 * as KSES doesn't handle matching the relevant closing tag.
	 */
	$stripped_tag = '';
	if ( empty( $xhtml_slash ) ) {
		$stripped_tag = "<$element>";
	}

	// Go through $attrarr, and save the allowed attributes for this element in $attr2.
	$attr2 = '';
	foreach ( $attrarr as $arreach ) {
		// Check if this attribute is required.
		$required = isset( $required_attrs[ strtolower( $arreach['name'] ) ] );

		if ( wp_kses_attr_check( $arreach['name'], $arreach['value'], $arreach['whole'], $arreach['vless'], $element, $allowed_html ) ) {
			$attr2 .= ' ' . $arreach['whole'];

			// If this was a required attribute, we can mark it as found.
			if ( $required ) {
				unset( $required_attrs[ strtolower( $arreach['name'] ) ] );
			}
		} elseif ( $required ) {
			// This attribute was required, but didn't pass the check. The entire tag is not allowed.
			return $stripped_tag;
		}
	}

	// If some required attributes weren't set, the entire tag is not allowed.
	if ( ! empty( $required_attrs ) ) {
		return $stripped_tag;
	}

	// Remove any "<" or ">" characters.
	$attr2 = preg_replace( '/[<>]/', '', $attr2 );

	return "<$element$attr2$xhtml_slash>";
}

/**
 * Determines whether an attribute is allowed.
 *
 * @since 4.2.3
 * @since 5.0.0 Added support for `data-*` wildcard attributes.
 *
 * @param string $name         The attribute name. Passed by reference. Returns empty string when not allowed.
 * @param string $value        The attribute value. Passed by reference. Returns a filtered value.
 * @param string $whole        The `name=value` input. Passed by reference. Returns filtered input.
 * @param string $vless        Whether the attribute is valueless. Use 'y' or 'n'.
 * @param string $element      The name of the element to which this attribute belongs.
 * @param array  $allowed_html The full list of allowed elements and attributes.
 * @return bool Whether or not the attribute is allowed.
 */
function wp_kses_attr_check( &$name, &$value, &$whole, $vless, $element, $allowed_html ) {
	$name_low    = strtolower( $name );
	$element_low = strtolower( $element );

	if ( ! isset( $allowed_html[ $element_low ] ) ) {
		$name  = '';
		$value = '';
		$whole = '';
		return false;
	}

	$allowed_attr = $allowed_html[ $element_low ];

	if ( ! isset( $allowed_attr[ $name_low ] ) || '' === $allowed_attr[ $name_low ] ) {
		/*
		 * Allow `data-*` attributes.
		 *
		 * When specifying `$allowed_html`, the attribute name should be set as
		 * `data-*` (not to be mixed with the HTML 4.0 `data` attribute, see
		 * https://www.w3.org/TR/html40/struct/objects.html#adef-data).
		 *
		 * Note: the attribute name should only contain `A-Za-z0-9_-` chars.
		 */
		if ( str_starts_with( $name_low, 'data-' ) && ! empty( $allowed_attr['data-*'] )
			&& preg_match( '/^data-[a-z0-9_-]+$/', $name_low, $match )
		) {
			/*
			 * Add the whole attribute name to the allowed attributes and set any restrictions
			 * for the `data-*` attribute values for the current element.
			 */
			$allowed_attr[ $match[0] ] = $allowed_attr['data-*'];
		} else {
			$name  = '';
			$value = '';
			$whole = '';
			return false;
		}
	}

	if ( 'style' === $name_low ) {
		$new_value = safecss_filter_attr( $value );

		if ( empty( $new_value ) ) {
			$name  = '';
			$value = '';
			$whole = '';
			return false;
		}

		$whole = str_replace( $value, $new_value, $whole );
		$value = $new_value;
	}

	if ( is_array( $allowed_attr[ $name_low ] ) ) {
		// There are some checks.
		foreach ( $allowed_attr[ $name_low ] as $currkey => $currval ) {
			if ( ! wp_kses_check_attr_val( $value, $vless, $currkey, $currval ) ) {
				$name  = '';
				$value = '';
				$whole = '';
				return false;
			}
		}
	}

	return true;
}

/**
 * Builds an attribute list from string containing attributes.
 *
 * This function does a lot of work. It parses an attribute list into an array
 * with attribute data, and tries to do the right thing even if it gets weird
 * input. It will add quotes around attribute values that don't have any quotes
 * or apostrophes around them, to make it easier to produce HTML code that will
 * conform to W3C's HTML specification. It will also remove bad URL protocols
 * from attribute values. It also reduces duplicate attributes by using the
 * attribute defined first (`foo='bar' foo='baz'` will result in `foo='bar'`).
 *
 * @since 1.0.0
 *
 * @param string   $attr              Attribute list from HTML element to closing HTML element tag.
 * @param string[] $allowed_protocols Array of allowed URL protocols.
 * @return array[] Array of attribute information after parsing.
 */
function wp_kses_hair( $attr, $allowed_protocols ) {
	$attrarr  = array();
	$mode     = 0;
	$attrname = '';
	$uris     = wp_kses_uri_attributes();

	// Loop through the whole attribute list.

	while ( strlen( $attr ) !== 0 ) {
		$working = 0; // Was the last operation successful?

		switch ( $mode ) {
			case 0:
				if ( preg_match( '/^([_a-zA-Z][-_a-zA-Z0-9:.]*)/', $attr, $match ) ) {
					$attrname = $match[1];
					$working  = 1;
					$mode     = 1;
					$attr     = preg_replace( '/^[_a-zA-Z][-_a-zA-Z0-9:.]*/', '', $attr );
				}

				break;

			case 1:
				if ( preg_match( '/^\s*=\s*/', $attr ) ) { // Equals sign.
					$working = 1;
					$mode    = 2;
					$attr    = preg_replace( '/^\s*=\s*/', '', $attr );
					break;
				}

				if ( preg_match( '/^\s+/', $attr ) ) { // Valueless.
					$working = 1;
					$mode    = 0;

					if ( false === array_key_exists( $attrname, $attrarr ) ) {
						$attrarr[ $attrname ] = array(
							'name'  => $attrname,
							'value' => '',
							'whole' => $attrname,
							'vless' => 'y',
						);
					}

					$attr = preg_replace( '/^\s+/', '', $attr );
				}

				break;

			case 2:
				if ( preg_match( '%^"([^"]*)"(\s+|/?$)%', $attr, $match ) ) {
					// "value"
					$thisval = $match[1];
					if ( in_array( strtolower( $attrname ), $uris, true ) ) {
						$thisval = wp_kses_bad_protocol( $thisval, $allowed_protocols );
					}

					if ( false === array_key_exists( $attrname, $attrarr ) ) {
						$attrarr[ $attrname ] = array(
							'name'  => $attrname,
							'value' => $thisval,
							'whole' => "$attrname=\"$thisval\"",
							'vless' => 'n',
						);
					}

					$working = 1;
					$mode    = 0;
					$attr    = preg_replace( '/^"[^"]*"(\s+|$)/', '', $attr );
					break;
				}

				if ( preg_match( "%^'([^']*)'(\s+|/?$)%", $attr, $match ) ) {
					// 'value'
					$thisval = $match[1];
					if ( in_array( strtolower( $attrname ), $uris, true ) ) {
						$thisval = wp_kses_bad_protocol( $thisval, $allowed_protocols );
					}

					if ( false === array_key_exists( $attrname, $attrarr ) ) {
						$attrarr[ $attrname ] = array(
							'name'  => $attrname,
							'value' => $thisval,
							'whole' => "$attrname='$thisval'",
							'vless' => 'n',
						);
					}

					$working = 1;
					$mode    = 0;
					$attr    = preg_replace( "/^'[^']*'(\s+|$)/", '', $attr );
					break;
				}

				if ( preg_match( "%^([^\s\"']+)(\s+|/?$)%", $attr, $match ) ) {
					// value
					$thisval = $match[1];
					if ( in_array( strtolower( $attrname ), $uris, true ) ) {
						$thisval = wp_kses_bad_protocol( $thisval, $allowed_protocols );
					}

					if ( false === array_key_exists( $attrname, $attrarr ) ) {
						$attrarr[ $attrname ] = array(
							'name'  => $attrname,
							'value' => $thisval,
							'whole' => "$attrname=\"$thisval\"",
							'vless' => 'n',
						);
					}

					// We add quotes to conform to W3C's HTML spec.
					$working = 1;
					$mode    = 0;
					$attr    = preg_replace( "%^[^\s\"']+(\s+|$)%", '', $attr );
				}

				break;
		} // End switch.

		if ( 0 === $working ) { // Not well-formed, remove and try again.
			$attr = wp_kses_html_error( $attr );
			$mode = 0;
		}
	} // End while.

	if ( 1 === $mode && false === array_key_exists( $attrname, $attrarr ) ) {
		/*
		 * Special case, for when the attribute list ends with a valueless
		 * attribute like "selected".
		 */
		$attrarr[ $attrname ] = array(
			'name'  => $attrname,
			'value' => '',
			'whole' => $attrname,
			'vless' => 'y',
		);
	}

	return $attrarr;
}

/**
 * Finds all attributes of an HTML element.
 *
 * Does not modify input.  May return "evil" output.
 *
 * Based on `wp_kses_split2()` and `wp_kses_attr()`.
 *
 * @since 4.2.3
 *
 * @param string $element HTML element.
 * @return array|false List of attributes found in the element. Returns false on failure.
 */
function wp_kses_attr_parse( $element ) {
	$valid = preg_match( '%^(<\s*)(/\s*)?([a-zA-Z0-9]+\s*)([^>]*)(>?)$%', $element, $matches );
	if ( 1 !== $valid ) {
		return false;
	}

	$begin  = $matches[1];
	$slash  = $matches[2];
	$elname = $matches[3];
	$attr   = $matches[4];
	$end    = $matches[5];

	if ( '' !== $slash ) {
		// Closing elements do not get parsed.
		return false;
	}

	// Is there a closing XHTML slash at the end of the attributes?
	if ( 1 === preg_match( '%\s*/\s*$%', $attr, $matches ) ) {
		$xhtml_slash = $matches[0];
		$attr        = substr( $attr, 0, -strlen( $xhtml_slash ) );
	} else {
		$xhtml_slash = '';
	}

	// Split it.
	$attrarr = wp_kses_hair_parse( $attr );
	if ( false === $attrarr ) {
		return false;
	}

	// Make sure all input is returned by adding front and back matter.
	array_unshift( $attrarr, $begin . $slash . $elname );
	array_push( $attrarr, $xhtml_slash . $end );

	return $attrarr;
}

/**
 * Builds an attribute list from string containing attributes.
 *
 * Does not modify input.  May return "evil" output.
 * In case of unexpected input, returns false instead of stripping things.
 *
 * Based on `wp_kses_hair()` but does not return a multi-dimensional array.
 *
 * @since 4.2.3
 *
 * @param string $attr Attribute list from HTML element to closing HTML element tag.
 * @return array|false List of attributes found in $attr. Returns false on failure.
 */
function wp_kses_hair_parse( $attr ) {
	if ( '' === $attr ) {
		return array();
	}

	$regex =
		'(?:
				[_a-zA-Z][-_a-zA-Z0-9:.]* # Attribute name.
			|
				\[\[?[^\[\]]+\]\]?        # Shortcode in the name position implies unfiltered_html.
		)
		(?:                               # Attribute value.
			\s*=\s*                       # All values begin with "=".
			(?:
				"[^"]*"                   # Double-quoted.
			|
				\'[^\']*\'                # Single-quoted.
			|
				[^\s"\']+                 # Non-quoted.
				(?:\s|$)                  # Must have a space.
			)
		|
			(?:\s|$)                      # If attribute has no value, space is required.
		)
		\s*                               # Trailing space is optional except as mentioned above.
		';

	/*
	 * Although it is possible to reduce this procedure to a single regexp,
	 * we must run that regexp twice to get exactly the expected result.
	 *
	 * Note: do NOT remove the `x` modifiers as they are essential for the above regex!
	 */

	$validation = "/^($regex)+$/x";
	$extraction = "/$regex/x";

	if ( 1 === preg_match( $validation, $attr ) ) {
		preg_match_all( $extraction, $attr, $attrarr );
		return $attrarr[0];
	} else {
		return false;
	}
}

/**
 * Performs different checks for attribute values.
 *
 * The currently implemented checks are "maxlen", "minlen", "maxval", "minval",
 * and "valueless".
 *
 * @since 1.0.0
 *
 * @param string $value      Attribute value.
 * @param string $vless      Whether the attribute is valueless. Use 'y' or 'n'.
 * @param string $checkname  What $checkvalue is checking for.
 * @param mixed  $checkvalue What constraint the value should pass.
 * @return bool Whether check passes.
 */
function wp_kses_check_attr_val( $value, $vless, $checkname, $checkvalue ) {
	$ok = true;

	switch ( strtolower( $checkname ) ) {
		case 'maxlen':
			/*
			 * The maxlen check makes sure that the attribute value has a length not
			 * greater than the given value. This can be used to avoid Buffer Overflows
			 * in WWW clients and various Internet servers.
			 */

			if ( strlen( $value ) > $checkvalue ) {
				$ok = false;
			}
			break;

		case 'minlen':
			/*
			 * The minlen check makes sure that the attribute value has a length not
			 * smaller than the given value.
			 */

			if ( strlen( $value ) < $checkvalue ) {
				$ok = false;
			}
			break;

		case 'maxval':
			/*
			 * The maxval check does two things: it checks that the attribute value is
			 * an integer from 0 and up, without an excessive amount of zeroes or
			 * whitespace (to avoid Buffer Overflows). It also checks that the attribute
			 * value is not greater than the given value.
			 * This check can be used to avoid Denial of Service attacks.
			 */

			if ( ! preg_match( '/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value ) ) {
				$ok = false;
			}
			if ( $value > $checkvalue ) {
				$ok = false;
			}
			break;

		case 'minval':
			/*
			 * The minval check makes sure that the attribute value is a positive integer,
			 * and that it is not smaller than the given value.
			 */

			if ( ! preg_match( '/^\s{0,6}[0-9]{1,6}\s{0,6}$/', $value ) ) {
				$ok = false;
			}
			if ( $value < $checkvalue ) {
				$ok = false;
			}
			break;

		case 'valueless':
			/*
			 * The valueless check makes sure if the attribute has a value
			 * (like `<a href="blah">`) or not (`<option selected>`). If the given value
			 * is a "y" or a "Y", the attribute must not have a value.
			 * If the given value is an "n" or an "N", the attribute must have a value.
			 */

			if ( strtolower( $checkvalue ) !== $vless ) {
				$ok = false;
			}
			break;

		case 'values':
			/*
			 * The values check is used when you want to make sure that the attribute
			 * has one of the given values.
			 */

			if ( false === array_search( strtolower( $value ), $checkvalue, true ) ) {
				$ok = false;
			}
			break;

		case 'value_callback':
			/*
			 * The value_callback check is used when you want to make sure that the attribute
			 * value is accepted by the callback function.
			 */

			if ( ! call_user_func( $checkvalue, $value ) ) {
				$ok = false;
			}
			break;
	} // End switch.

	return $ok;
}

/**
 * Sanitizes a string and removed disallowed URL protocols.
 *
 * This function removes all non-allowed protocols from the beginning of the
 * string. It ignores whitespace and the case of the letters, and it does
 * understand HTML entities. It does its work recursively, so it won't be
 * fooled by a string like `javascript:javascript:alert(57)`.
 *
 * @since 1.0.0
 *
 * @param string   $content           Content to filter bad protocols from.
 * @param string[] $allowed_protocols Array of allowed URL protocols.
 * @return string Filtered content.
 */
function wp_kses_bad_protocol( $content, $allowed_protocols ) {
	$content = wp_kses_no_null( $content );

	// Short-circuit if the string starts with `https://` or `http://`. Most common cases.
	if (
		( str_starts_with( $content, 'https://' ) && in_array( 'https', $allowed_protocols, true ) ) ||
		( str_starts_with( $content, 'http://' ) && in_array( 'http', $allowed_protocols, true ) )
	) {
		return $content;
	}

	$iterations = 0;

	do {
		$original_content = $content;
		$content          = wp_kses_bad_protocol_once( $content, $allowed_protocols );
	} while ( $original_content !== $content && ++$iterations < 6 );

	if ( $original_content !== $content ) {
		return '';
	}

	return $content;
}

/**
 * Removes any invalid control characters in a text string.
 *
 * Also removes any instance of the `\0` string.
 *
 * @since 1.0.0
 *
 * @param string $content Content to filter null characters from.
 * @param array  $options Set 'slash_zero' => 'keep' when '\0' is allowed. Default is 'remove'.
 * @return string Filtered content.
 */
function wp_kses_no_null( $content, $options = null ) {
	if ( ! isset( $options['slash_zero'] ) ) {
		$options = array( 'slash_zero' => 'remove' );
	}

	$content = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $content );
	if ( 'remove' === $options['slash_zero'] ) {
		$content = preg_replace( '/\\\\+0+/', '', $content );
	}

	return $content;
}

/**
 * Strips slashes from in front of quotes.
 *
 * This function changes the character sequence `\"` to just `"`. It leaves all other
 * slashes alone. The quoting from `preg_replace(//e)` requires this.
 *
 * @since 1.0.0
 *
 * @param string $content String to strip slashes from.
 * @return string Fixed string with quoted slashes.
 */
function wp_kses_stripslashes( $content ) {
	return preg_replace( '%\\\\"%', '"', $content );
}

/**
 * Converts the keys of an array to lowercase.
 *
 * @since 1.0.0
 *
 * @param array $inarray Unfiltered array.
 * @return array Fixed array with all lowercase keys.
 */
function wp_kses_array_lc( $inarray ) {
	$outarray = array();

	foreach ( (array) $inarray as $inkey => $inval ) {
		$outkey              = strtolower( $inkey );
		$outarray[ $outkey ] = array();

		foreach ( (array) $inval as $inkey2 => $inval2 ) {
			$outkey2                         = strtolower( $inkey2 );
			$outarray[ $outkey ][ $outkey2 ] = $inval2;
		}
	}

	return $outarray;
}

/**
 * Handles parsing errors in `wp_kses_hair()`.
 *
 * The general plan is to remove everything to and including some whitespace,
 * but it deals with quotes and apostrophes as well.
 *
 * @since 1.0.0
 *
 * @param string $attr
 * @return string
 */
function wp_kses_html_error( $attr ) {
	return preg_replace( '/^("[^"]*("|$)|\'[^\']*(\'|$)|\S)*\s*/', '', $attr );
}

/**
 * Sanitizes content from bad protocols and other characters.
 *
 * This function searches for URL protocols at the beginning of the string, while
 * handling whitespace and HTML entities.
 *
 * @since 1.0.0
 *
 * @param string   $content           Content to check for bad protocols.
 * @param string[] $allowed_protocols Array of allowed URL protocols.
 * @param int      $count             Depth of call recursion to this function.
 * @return string Sanitized content.
 */
function wp_kses_bad_protocol_once( $content, $allowed_protocols, $count = 1 ) {
	$content  = preg_replace( '/(&#0*58(?![;0-9])|&#x0*3a(?![;a-f0-9]))/i', '$1;', $content );
	$content2 = preg_split( '/:|&#0*58;|&#x0*3a;|&colon;/i', $content, 2 );

	if ( isset( $content2[1] ) && ! preg_match( '%/\?%', $content2[0] ) ) {
		$content  = trim( $content2[1] );
		$protocol = wp_kses_bad_protocol_once2( $content2[0], $allowed_protocols );
		if ( 'feed:' === $protocol ) {
			if ( $count > 2 ) {
				return '';
			}
			$content = wp_kses_bad_protocol_once( $content, $allowed_protocols, ++$count );
			if ( empty( $content ) ) {
				return $content;
			}
		}
		$content = $protocol . $content;
	}

	return $content;
}

/**
 * Callback for `wp_kses_bad_protocol_once()` regular expression.
 *
 * This function processes URL protocols, checks to see if they're in the
 * list of allowed protocols or not, and returns different data depending
 * on the answer.
 *
 * @access private
 * @ignore
 * @since 1.0.0
 *
 * @param string   $scheme            URI scheme to check against the list of allowed protocols.
 * @param string[] $allowed_protocols Array of allowed URL protocols.
 * @return string Sanitized content.
 */
function wp_kses_bad_protocol_once2( $scheme, $allowed_protocols ) {
	$scheme = wp_kses_decode_entities( $scheme );
	$scheme = preg_replace( '/\s/', '', $scheme );
	$scheme = wp_kses_no_null( $scheme );
	$scheme = strtolower( $scheme );

	$allowed = false;
	foreach ( (array) $allowed_protocols as $one_protocol ) {
		if ( strtolower( $one_protocol ) === $scheme ) {
			$allowed = true;
			break;
		}
	}

	if ( $allowed ) {
		return "$scheme:";
	} else {
		return '';
	}
}

/**
 * Converts and fixes HTML entities.
 *
 * This function normalizes HTML entities. It will convert `AT&T` to the correct
 * `AT&amp;T`, `&#00058;` to `&#058;`, `&#XYZZY;` to `&amp;#XYZZY;` and so on.
 *
 * When `$context` is set to 'xml', HTML entities are converted to their code points.  For
 * example, `AT&T&hellip;&#XYZZY;` is converted to `AT&amp;T…&amp;#XYZZY;`.
 *
 * @since 1.0.0
 * @since 5.5.0 Added `$context` parameter.
 *
 * @param string $content Content to normalize entities.
 * @param string $context Context for normalization. Can be either 'html' or 'xml'.
 *                        Default 'html'.
 * @return string Content with normalized entities.
 */
function wp_kses_normalize_entities( $content, $context = 'html' ) {
	// Disarm all entities by converting & to &amp;
	$content = str_replace( '&', '&amp;', $content );

	// Change back the allowed entities in our list of allowed entities.
	if ( 'xml' === $context ) {
		$content = preg_replace_callback( '/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', 'wp_kses_xml_named_entities', $content );
	} else {
		$content = preg_replace_callback( '/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', 'wp_kses_named_entities', $content );
	}
	$content = preg_replace_callback( '/&amp;#(0*[0-9]{1,7});/', 'wp_kses_normalize_entities2', $content );
	$content = preg_replace_callback( '/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', 'wp_kses_normalize_entities3', $content );

	return $content;
}

/**
 * Callback for `wp_kses_normalize_entities()` regular expression.
 *
 * This function only accepts valid named entity references, which are finite,
 * case-sensitive, and highly scrutinized by HTML and XML validators.
 *
 * @since 3.0.0
 *
 * @global array $allowedentitynames
 *
 * @param array $matches preg_replace_callback() matches array.
 * @return string Correctly encoded entity.
 */
function wp_kses_named_entities( $matches ) {
	global $allowedentitynames;

	if ( empty( $matches[1] ) ) {
		return '';
	}

	$i = $matches[1];
	return ( ! in_array( $i, $allowedentitynames, true ) ) ? "&amp;$i;" : "&$i;";
}

/**
 * Callback for `wp_kses_normalize_entities()` regular expression.
 *
 * This function only accepts valid named entity references, which are finite,
 * case-sensitive, and highly scrutinized by XML validators.  HTML named entity
 * references are converted to their code points.
 *
 * @since 5.5.0
 *
 * @global array $allowedentitynames
 * @global array $allowedxmlentitynames
 *
 * @param array $matches preg_replace_callback() matches array.
 * @return string Correctly encoded entity.
 */
function wp_kses_xml_named_entities( $matches ) {
	global $allowedentitynames, $allowedxmlentitynames;

	if ( empty( $matches[1] ) ) {
		return '';
	}

	$i = $matches[1];

	if ( in_array( $i, $allowedxmlentitynames, true ) ) {
		return "&$i;";
	} elseif ( in_array( $i, $allowedentitynames, true ) ) {
		return html_entity_decode( "&$i;", ENT_HTML5 );
	}

	return "&amp;$i;";
}

/**
 * Callback for `wp_kses_normalize_entities()` regular expression.
 *
 * This function helps `wp_kses_normalize_entities()` to only accept 16-bit
 * values and nothing more for `&#number;` entities.
 *
 * @access private
 * @ignore
 * @since 1.0.0
 *
 * @param array $matches `preg_replace_callback()` matches array.
 * @return string Correctly encoded entity.
 */
function wp_kses_normalize_entities2( $matches ) {
	if ( empty( $matches[1] ) ) {
		return '';
	}

	$i = $matches[1];

	if ( valid_unicode( $i ) ) {
		$i = str_pad( ltrim( $i, '0' ), 3, '0', STR_PAD_LEFT );
		$i = "&#$i;";
	} else {
		$i = "&amp;#$i;";
	}

	return $i;
}

/**
 * Callback for `wp_kses_normalize_entities()` for regular expression.
 *
 * This function helps `wp_kses_normalize_entities()` to only accept valid Unicode
 * numeric entities in hex form.
 *
 * @since 2.7.0
 * @access private
 * @ignore
 *
 * @param array $matches `preg_replace_callback()` matches array.
 * @return string Correctly encoded entity.
 */
function wp_kses_normalize_entities3( $matches ) {
	if ( empty( $matches[1] ) ) {
		return '';
	}

	$hexchars = $matches[1];

	return ( ! valid_unicode( hexdec( $hexchars ) ) ) ? "&amp;#x$hexchars;" : '&#x' . ltrim( $hexchars, '0' ) . ';';
}

/**
 * Determines if a Unicode codepoint is valid.
 *
 * @since 2.7.0
 *
 * @param int $i Unicode codepoint.
 * @return bool Whether or not the codepoint is a valid Unicode codepoint.
 */
function valid_unicode( $i ) {
	$i = (int) $i;

	return ( 0x9 === $i || 0xa === $i || 0xd === $i ||
		( 0x20 <= $i && $i <= 0xd7ff ) ||
		( 0xe000 <= $i && $i <= 0xfffd ) ||
		( 0x10000 <= $i && $i <= 0x10ffff )
	);
}

/**
 * Converts all numeric HTML entities to their named counterparts.
 *
 * This function decodes numeric HTML entities (`&#65;` and `&#x41;`).
 * It doesn't do anything with named entities like `&auml;`, but we don't
 * need them in the allowed URL protocols system anyway.
 *
 * @since 1.0.0
 *
 * @param string $content Content to change entities.
 * @return string Content after decoded entities.
 */
function wp_kses_decode_entities( $content ) {
	$content = preg_replace_callback( '/&#([0-9]+);/', '_wp_kses_decode_entities_chr', $content );
	$content = preg_replace_callback( '/&#[Xx]([0-9A-Fa-f]+);/', '_wp_kses_decode_entities_chr_hexdec', $content );

	return $content;
}

/**
 * Regex callback for `wp_kses_decode_entities()`.
 *
 * @since 2.9.0
 * @access private
 * @ignore
 *
 * @param array $matches preg match
 * @return string
 */
function _wp_kses_decode_entities_chr( $matches ) {
	return chr( $matches[1] );
}

/**
 * Regex callback for `wp_kses_decode_entities()`.
 *
 * @since 2.9.0
 * @access private
 * @ignore
 *
 * @param array $matches preg match
 * @return string
 */
function _wp_kses_decode_entities_chr_hexdec( $matches ) {
	return chr( hexdec( $matches[1] ) );
}

/**
 * Sanitize content with allowed HTML KSES rules.
 *
 * This function expects slashed data.
 *
 * @since 1.0.0
 *
 * @param string $data Content to filter, expected to be escaped with slashes.
 * @return string Filtered content.
 */
function wp_filter_kses( $data ) {
	return addslashes( wp_kses( stripslashes( $data ), current_filter() ) );
}

/**
 * Sanitize content with allowed HTML KSES rules.
 *
 * This function expects unslashed data.
 *
 * @since 2.9.0
 *
 * @param string $data Content to filter, expected to not be escaped.
 * @return string Filtered content.
 */
function wp_kses_data( $data ) {
	return wp_kses( $data, current_filter() );
}

/**
 * Sanitizes content for allowed HTML tags for post content.
 *
 * Post content refers to the page contents of the 'post' type and not `$_POST`
 * data from forms.
 *
 * This function expects slashed data.
 *
 * @since 2.0.0
 *
 * @param string $data Post content to filter, expected to be escaped with slashes.
 * @return string Filtered post content with allowed HTML tags and attributes intact.
 */
function wp_filter_post_kses( $data ) {
	return addslashes( wp_kses( stripslashes( $data ), 'post' ) );
}

/**
 * Sanitizes global styles user content removing unsafe rules.
 *
 * @since 5.9.0
 *
 * @param string $data Post content to filter.
 * @return string Filtered post content with unsafe rules removed.
 */
function wp_filter_global_styles_post( $data ) {
	$decoded_data        = json_decode( wp_unslash( $data ), true );
	$json_decoding_error = json_last_error();
	if (
		JSON_ERROR_NONE === $json_decoding_error &&
		is_array( $decoded_data ) &&
		isset( $decoded_data['isGlobalStylesUserThemeJSON'] ) &&
		$decoded_data['isGlobalStylesUserThemeJSON']
	) {
		unset( $decoded_data['isGlobalStylesUserThemeJSON'] );

		$data_to_encode = WP_Theme_JSON::remove_insecure_properties( $decoded_data, 'custom' );

		$data_to_encode['isGlobalStylesUserThemeJSON'] = true;
		return wp_slash( wp_json_encode( $data_to_encode ) );
	}
	return $data;
}

/**
 * Sanitizes content for allowed HTML tags for post content.
 *
 * Post content refers to the page contents of the 'post' type and not `$_POST`
 * data from forms.
 *
 * This function expects unslashed data.
 *
 * @since 2.9.0
 *
 * @param string $data Post content to filter.
 * @return string Filtered post content with allowed HTML tags and attributes intact.
 */
function wp_kses_post( $data ) {
	return wp_kses( $data, 'post' );
}

/**
 * Navigates through an array, object, or scalar, and sanitizes content for
 * allowed HTML tags for post content.
 *
 * @since 4.4.2
 *
 * @see map_deep()
 *
 * @param mixed $data The array, object, or scalar value to inspect.
 * @return mixed The filtered content.
 */
function wp_kses_post_deep( $data ) {
	return map_deep( $data, 'wp_kses_post' );
}

/**
 * Strips all HTML from a text string.
 *
 * This function expects slashed data.
 *
 * @since 2.1.0
 *
 * @param string $data Content to strip all HTML from.
 * @return string Filtered content without any HTML.
 */
function wp_filter_nohtml_kses( $data ) {
	return addslashes( wp_kses( stripslashes( $data ), 'strip' ) );
}

/**
 * Adds all KSES input form content filters.
 *
 * All hooks have default priority. The `wp_filter_kses()` function is added to
 * the 'pre_comment_content' and 'title_save_pre' hooks.
 *
 * The `wp_filter_post_kses()` function is added to the 'content_save_pre',
 * 'excerpt_save_pre', and 'content_filtered_save_pre' hooks.
 *
 * @since 2.0.0
 */
function kses_init_filters() {
	// Normal filtering.
	add_filter( 'title_save_pre', 'wp_filter_kses' );

	// Comment filtering.
	if ( current_user_can( 'unfiltered_html' ) ) {
		add_filter( 'pre_comment_content', 'wp_filter_post_kses' );
	} else {
		add_filter( 'pre_comment_content', 'wp_filter_kses' );
	}

	// Global Styles filtering: Global Styles filters should be executed before normal post_kses HTML filters.
	add_filter( 'content_save_pre', 'wp_filter_global_styles_post', 9 );
	add_filter( 'content_filtered_save_pre', 'wp_filter_global_styles_post', 9 );

	// Post filtering.
	add_filter( 'content_save_pre', 'wp_filter_post_kses' );
	add_filter( 'excerpt_save_pre', 'wp_filter_post_kses' );
	add_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );
}

/**
 * Removes all KSES input form content filters.
 *
 * A quick procedural method to removing all of the filters that KSES uses for
 * content in WordPress Loop.
 *
 * Does not remove the `kses_init()` function from {@see 'init'} hook (priority is
 * default). Also does not remove `kses_init()` function from {@see 'set_current_user'}
 * hook (priority is also default).
 *
 * @since 2.0.6
 */
function kses_remove_filters() {
	// Normal filtering.
	remove_filter( 'title_save_pre', 'wp_filter_kses' );

	// Comment filtering.
	remove_filter( 'pre_comment_content', 'wp_filter_post_kses' );
	remove_filter( 'pre_comment_content', 'wp_filter_kses' );

	// Global Styles filtering.
	remove_filter( 'content_save_pre', 'wp_filter_global_styles_post', 9 );
	remove_filter( 'content_filtered_save_pre', 'wp_filter_global_styles_post', 9 );

	// Post filtering.
	remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
	remove_filter( 'excerpt_save_pre', 'wp_filter_post_kses' );
	remove_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );
}

/**
 * Sets up most of the KSES filters for input form content.
 *
 * First removes all of the KSES filters in case the current user does not need
 * to have KSES filter the content. If the user does not have `unfiltered_html`
 * capability, then KSES filters are added.
 *
 * @since 2.0.0
 */
function kses_init() {
	kses_remove_filters();

	if ( ! current_user_can( 'unfiltered_html' ) ) {
		kses_init_filters();
	}
}

/**
 * Filters an inline style attribute and removes disallowed rules.
 *
 * @since 2.8.1
 * @since 4.4.0 Added support for `min-height`, `max-height`, `min-width`, and `max-width`.
 * @since 4.6.0 Added support for `list-style-type`.
 * @since 5.0.0 Added support for `background-image`.
 * @since 5.1.0 Added support for `text-transform`.
 * @since 5.2.0 Added support for `background-position` and `grid-template-columns`.
 * @since 5.3.0 Added support for `grid`, `flex` and `column` layout properties.
 *              Extended `background-*` support for individual properties.
 * @since 5.3.1 Added support for gradient backgrounds.
 * @since 5.7.1 Added support for `object-position`.
 * @since 5.8.0 Added support for `calc()` and `var()` values.
 * @since 6.1.0 Added support for `min()`, `max()`, `minmax()`, `clamp()`,
 *              nested `var()` values, and assigning values to CSS variables.
 *              Added support for `object-fit`, `gap`, `column-gap`, `row-gap`, and `flex-wrap`.
 *              Extended `margin-*` and `padding-*` support for logical properties.
 * @since 6.2.0 Added support for `aspect-ratio`, `position`, `top`, `right`, `bottom`, `left`,
 *              and `z-index` CSS properties.
 * @since 6.3.0 Extended support for `filter` to accept a URL and added support for repeat().
 *              Added support for `box-shadow`.
 * @since 6.4.0 Added support for `writing-mode`.
 * @since 6.5.0 Added support for `background-repeat`.
 * @since 6.6.0 Added support for `grid-column`, `grid-row`, and `container-type`.
 *
 * @param string $css        A string of CSS rules.
 * @param string $deprecated Not used.
 * @return string Filtered string of CSS rules.
 */
function safecss_filter_attr( $css, $deprecated = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '2.8.1' ); // Never implemented.
	}

	$css = wp_kses_no_null( $css );
	$css = str_replace( array( "\n", "\r", "\t" ), '', $css );

	$allowed_protocols = wp_allowed_protocols();

	$css_array = explode( ';', trim( $css ) );

	/**
	 * Filters the list of allowed CSS attributes.
	 *
	 * @since 2.8.1
	 *
	 * @param string[] $attr Array of allowed CSS attributes.
	 */
	$allowed_attr = apply_filters(
		'safe_style_css',
		array(
			'background',
			'background-color',
			'background-image',
			'background-position',
			'background-repeat',
			'background-size',
			'background-attachment',
			'background-blend-mode',

			'border',
			'border-radius',
			'border-width',
			'border-color',
			'border-style',
			'border-right',
			'border-right-color',
			'border-right-style',
			'border-right-width',
			'border-bottom',
			'border-bottom-color',
			'border-bottom-left-radius',
			'border-bottom-right-radius',
			'border-bottom-style',
			'border-bottom-width',
			'border-bottom-right-radius',
			'border-bottom-left-radius',
			'border-left',
			'border-left-color',
			'border-left-style',
			'border-left-width',
			'border-top',
			'border-top-color',
			'border-top-left-radius',
			'border-top-right-radius',
			'border-top-style',
			'border-top-width',
			'border-top-left-radius',
			'border-top-right-radius',

			'border-spacing',
			'border-collapse',
			'caption-side',

			'columns',
			'column-count',
			'column-fill',
			'column-gap',
			'column-rule',
			'column-span',
			'column-width',

			'color',
			'filter',
			'font',
			'font-family',
			'font-size',
			'font-style',
			'font-variant',
			'font-weight',
			'letter-spacing',
			'line-height',
			'text-align',
			'text-decoration',
			'text-indent',
			'text-transform',

			'height',
			'min-height',
			'max-height',

			'width',
			'min-width',
			'max-width',

			'margin',
			'margin-right',
			'margin-bottom',
			'margin-left',
			'margin-top',
			'margin-block-start',
			'margin-block-end',
			'margin-inline-start',
			'margin-inline-end',

			'padding',
			'padding-right',
			'padding-bottom',
			'padding-left',
			'padding-top',
			'padding-block-start',
			'padding-block-end',
			'padding-inline-start',
			'padding-inline-end',

			'flex',
			'flex-basis',
			'flex-direction',
			'flex-flow',
			'flex-grow',
			'flex-shrink',
			'flex-wrap',

			'gap',
			'column-gap',
			'row-gap',

			'grid-template-columns',
			'grid-auto-columns',
			'grid-column-start',
			'grid-column-end',
			'grid-column',
			'grid-column-gap',
			'grid-template-rows',
			'grid-auto-rows',
			'grid-row-start',
			'grid-row-end',
			'grid-row',
			'grid-row-gap',
			'grid-gap',

			'justify-content',
			'justify-items',
			'justify-self',
			'align-content',
			'align-items',
			'align-self',

			'clear',
			'cursor',
			'direction',
			'float',
			'list-style-type',
			'object-fit',
			'object-position',
			'opacity',
			'overflow',
			'vertical-align',
			'writing-mode',

			'position',
			'top',
			'right',
			'bottom',
			'left',
			'z-index',
			'box-shadow',
			'aspect-ratio',
			'container-type',

			// Custom CSS properties.
			'--*',
		)
	);

	/*
	 * CSS attributes that accept URL data types.
	 *
	 * This is in accordance to the CSS spec and unrelated to
	 * the sub-set of supported attributes above.
	 *
	 * See: https://developer.mozilla.org/en-US/docs/Web/CSS/url
	 */
	$css_url_data_types = array(
		'background',
		'background-image',

		'cursor',
		'filter',

		'list-style',
		'list-style-image',
	);

	/*
	 * CSS attributes that accept gradient data types.
	 *
	 */
	$css_gradient_data_types = array(
		'background',
		'background-image',
	);

	if ( empty( $allowed_attr ) ) {
		return $css;
	}

	$css = '';
	foreach ( $css_array as $css_item ) {
		if ( '' === $css_item ) {
			continue;
		}

		$css_item        = trim( $css_item );
		$css_test_string = $css_item;
		$found           = false;
		$url_attr        = false;
		$gradient_attr   = false;
		$is_custom_var   = false;

		if ( ! str_contains( $css_item, ':' ) ) {
			$found = true;
		} else {
			$parts        = explode( ':', $css_item, 2 );
			$css_selector = trim( $parts[0] );

			// Allow assigning values to CSS variables.
			if ( in_array( '--*', $allowed_attr, true ) && preg_match( '/^--[a-zA-Z0-9-_]+$/', $css_selector ) ) {
				$allowed_attr[] = $css_selector;
				$is_custom_var  = true;
			}

			if ( in_array( $css_selector, $allowed_attr, true ) ) {
				$found         = true;
				$url_attr      = in_array( $css_selector, $css_url_data_types, true );
				$gradient_attr = in_array( $css_selector, $css_gradient_data_types, true );
			}

			if ( $is_custom_var ) {
				$css_value     = trim( $parts[1] );
				$url_attr      = str_starts_with( $css_value, 'url(' );
				$gradient_attr = str_contains( $css_value, '-gradient(' );
			}
		}

		if ( $found && $url_attr ) {
			// Simplified: matches the sequence `url(*)`.
			preg_match_all( '/url\([^)]+\)/', $parts[1], $url_matches );

			foreach ( $url_matches[0] as $url_match ) {
				// Clean up the URL from each of the matches above.
				preg_match( '/^url\(\s*([\'\"]?)(.*)(\g1)\s*\)$/', $url_match, $url_pieces );

				if ( empty( $url_pieces[2] ) ) {
					$found = false;
					break;
				}

				$url = trim( $url_pieces[2] );

				if ( empty( $url ) || wp_kses_bad_protocol( $url, $allowed_protocols ) !== $url ) {
					$found = false;
					break;
				} else {
					// Remove the whole `url(*)` bit that was matched above from the CSS.
					$css_test_string = str_replace( $url_match, '', $css_test_string );
				}
			}
		}

		if ( $found && $gradient_attr ) {
			$css_value = trim( $parts[1] );
			if ( preg_match( '/^(repeating-)?(linear|radial|conic)-gradient\(([^()]|rgb[a]?\([^()]*\))*\)$/', $css_value ) ) {
				// Remove the whole `gradient` bit that was matched above from the CSS.
				$css_test_string = str_replace( $css_value, '', $css_test_string );
			}
		}

		if ( $found ) {
			/*
			 * Allow CSS functions like var(), calc(), etc. by removing them from the test string.
			 * Nested functions and parentheses are also removed, so long as the parentheses are balanced.
			 */
			$css_test_string = preg_replace(
				'/\b(?:var|calc|min|max|minmax|clamp|repeat)(\((?:[^()]|(?1))*\))/',
				'',
				$css_test_string
			);

			/*
			 * Disallow CSS containing \ ( & } = or comments, except for within url(), var(), calc(), etc.
			 * which were removed from the test string above.
			 */
			$allow_css = ! preg_match( '%[\\\(&=}]|/\*%', $css_test_string );

			/**
			 * Filters the check for unsafe CSS in `safecss_filter_attr`.
			 *
			 * Enables developers to determine whether a section of CSS should be allowed or discarded.
			 * By default, the value will be false if the part contains \ ( & } = or comments.
			 * Return true to allow the CSS part to be included in the output.
			 *
			 * @since 5.5.0
			 *
			 * @param bool   $allow_css       Whether the CSS in the test string is considered safe.
			 * @param string $css_test_string The CSS string to test.
			 */
			$allow_css = apply_filters( 'safecss_filter_attr_allow_css', $allow_css, $css_test_string );

			// Only add the CSS part if it passes the regex check.
			if ( $allow_css ) {
				if ( '' !== $css ) {
					$css .= ';';
				}

				$css .= $css_item;
			}
		}
	}

	return $css;
}

/**
 * Helper function to add global attributes to a tag in the allowed HTML list.
 *
 * @since 3.5.0
 * @since 5.0.0 Added support for `data-*` wildcard attributes.
 * @since 6.0.0 Added `dir`, `lang`, and `xml:lang` to global attributes.
 * @since 6.3.0 Added `aria-controls`, `aria-current`, and `aria-expanded` attributes.
 * @since 6.4.0 Added `aria-live` and `hidden` attributes.
 *
 * @access private
 * @ignore
 *
 * @param array $value An array of attributes.
 * @return array The array of attributes with global attributes added.
 */
function _wp_add_global_attributes( $value ) {
	$global_attributes = array(
		'aria-controls'    => true,
		'aria-current'     => true,
		'aria-describedby' => true,
		'aria-details'     => true,
		'aria-expanded'    => true,
		'aria-hidden'      => true,
		'aria-label'       => true,
		'aria-labelledby'  => true,
		'aria-live'        => true,
		'class'            => true,
		'data-*'           => true,
		'dir'              => true,
		'hidden'           => true,
		'id'               => true,
		'lang'             => true,
		'style'            => true,
		'title'            => true,
		'role'             => true,
		'xml:lang'         => true,
	);

	if ( true === $value ) {
		$value = array();
	}

	if ( is_array( $value ) ) {
		return array_merge( $value, $global_attributes );
	}

	return $value;
}

/**
 * Helper function to check if this is a safe PDF URL.
 *
 * @since 5.9.0
 * @access private
 * @ignore
 *
 * @param string $url The URL to check.
 * @return bool True if the URL is safe, false otherwise.
 */
function _wp_kses_allow_pdf_objects( $url ) {
	// We're not interested in URLs that contain query strings or fragments.
	if ( str_contains( $url, '?' ) || str_contains( $url, '#' ) ) {
		return false;
	}

	// If it doesn't have a PDF extension, it's not safe.
	if ( ! str_ends_with( $url, '.pdf' ) ) {
		return false;
	}

	// If the URL host matches the current site's media URL, it's safe.
	$upload_info = wp_upload_dir( null, false );
	$parsed_url  = wp_parse_url( $upload_info['url'] );
	$upload_host = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';
	$upload_port = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';

	if ( str_starts_with( $url, "http://$upload_host$upload_port/" )
		|| str_starts_with( $url, "https://$upload_host$upload_port/" )
	) {
		return true;
	}

	return false;
}
