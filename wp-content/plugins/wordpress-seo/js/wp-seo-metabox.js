/* browser:true */
/* global wpseoMetaboxL10n */
/* global ajaxurl */
/* global tinyMCE */
/* global replacedVars */
/* jshint -W097 */
/* jshint -W003 */
'use strict';

/**
 * Cleans up a string, removing script tags etc.
 *
 * @param {string} str
 * @returns {string}
 */
function ystClean( str ) {
	if ( str === '' || typeof(str) === 'undefined' ) {
		return '';
	}

	try {
		str = str.replace( /<\/?[^>]+>/gi, '' );
		str = str.replace( /\[(.+?)](.+?\[\/\\1])?/g, '' );
		str = jQuery( '<div/>' ).html( str ).text();
	}
	catch ( e ) {
	}

	return str;
}

/**
 * Tests whether given element `str` matches `p`.
 *
 * @param {string} str The string to match
 * @param {RegExp} p The regex to match
 * @returns {string}
 */
function ystFocusKwTest( str, p ) {
	str = ystClean( str );
	str = str.toLocaleLowerCase();
	str = ystRemoveLowerCaseDiacritics( str );
	var r = str.match( p );

	if ( r !== null ) {
		return '<span class="good">Yes (' + r.length + ')</span>';
	}

	return '<span class="wrong">No</span>';
}

/**
 * The funciton name says it all, removes lower case diacritics
 *
 * @param {string} str
 * @returns {string}
 */
function ystRemoveLowerCaseDiacritics( str ) {
	var diacriticsRemovalMap = [
		{
			base: 'a',
			letters: /[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g
		},
		{ base: 'aa', letters: /[\uA733]/g },
		{ base: 'ae', letters: /[\u00E6\u01FD\u01E3]/g },
		{ base: 'ao', letters: /[\uA735]/g },
		{ base: 'au', letters: /[\uA737]/g },
		{ base: 'av', letters: /[\uA739\uA73B]/g },
		{ base: 'ay', letters: /[\uA73D]/g },
		{ base: 'b', letters: /[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g },
		{ base: 'c', letters: /[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g },
		{
			base: 'd',
			letters: /[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g
		},
		{ base: 'dz', letters: /[\u01F3\u01C6]/g },
		{
			base: 'e',
			letters: /[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g
		},
		{ base: 'f', letters: /[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g },
		{
			base: 'g',
			letters: /[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g
		},
		{
			base: 'h',
			letters: /[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g
		},
		{ base: 'hv', letters: /[\u0195]/g },
		{
			base: 'i',
			letters: /[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g
		},
		{ base: 'j', letters: /[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g },
		{
			base: 'k',
			letters: /[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g
		},
		{
			base: 'l',
			letters: /[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g
		},
		{ base: 'lj', letters: /[\u01C9]/g },
		{ base: 'm', letters: /[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g },
		{
			base: 'n',
			letters: /[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g
		},
		{ base: 'nj', letters: /[\u01CC]/g },
		{
			base: 'o',
			letters: /[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g
		},
		{ base: 'oi', letters: /[\u01A3]/g },
		{ base: 'ou', letters: /[\u0223]/g },
		{ base: 'oo', letters: /[\uA74F]/g },
		{ base: 'p', letters: /[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g },
		{ base: 'q', letters: /[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g },
		{
			base: 'r',
			letters: /[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g
		},
		{
			base: 's',
			letters: /[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g
		},
		{
			base: 't',
			letters: /[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g
		},
		{ base: 'tz', letters: /[\uA729]/g },
		{
			base: 'u',
			letters: /[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g
		},
		{ base: 'v', letters: /[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g },
		{ base: 'vy', letters: /[\uA761]/g },
		{ base: 'w', letters: /[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g },
		{ base: 'x', letters: /[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g },
		{
			base: 'y',
			letters: /[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g
		},
		{
			base: 'z',
			letters: /[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g
		}
	];
	for ( var i = 0; i < diacriticsRemovalMap.length; i++ ) {
		str = str.replace( diacriticsRemovalMap[ i ].letters, diacriticsRemovalMap[ i ].base );
	}
	return str;
}

/**
 * Tests whether the focus keyword is used in title, body and description
 */
function ystTestFocusKw() {
	// Retrieve focus keyword and trim
	var focuskw = jQuery.trim( jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'focuskw' ).val() );

	focuskw = ystEscapeFocusKw( focuskw ).toLowerCase();

	var postname;
	var url;
	if ( jQuery( '#editable-post-name-full' ).length ) {
		postname = jQuery( '#editable-post-name-full' ).text();
		url = wpseoMetaboxL10n.wpseo_permalink_template.replace( '%postname%', postname ).replace( 'http://', '' );
	}
	var p = new RegExp( '(^|[ \\s\n\r\t\\.,\'\\("\\+;!?:\\-])' + ystRemoveLowerCaseDiacritics( focuskw ) + '($|[\\s\n\r\t.,\'\\)"\\+!?:;\\-])', 'gim' );

	//remove diacritics of a lower cased focuskw for url matching in foreign lang
	var focuskwNoDiacritics = ystRemoveLowerCaseDiacritics( focuskw );
	var p2 = new RegExp( focuskwNoDiacritics.replace( /\s+/g, '[-_\\\//]' ), 'gim' );

	var focuskwresults = jQuery( '#focuskwresults' );
	var metadesc = jQuery( '#wpseosnippet' ).find( '.desc span.content' ).text();

	if ( focuskw !== '' ) {
		var html = '<p>' + wpseoMetaboxL10n.keyword_header + '</p>';
		html += '<ul>';
		if ( jQuery( '#title' ).length ) {
			html += '<li>' + wpseoMetaboxL10n.article_header_text + ystFocusKwTest( jQuery( '#title' ).val(), p ) + '</li>';
		}
		html += '<li>' + wpseoMetaboxL10n.page_title_text + ystFocusKwTest( jQuery( '#wpseosnippet_title' ).text(), p ) + '</li>';
		html += '<li>' + wpseoMetaboxL10n.page_url_text + ystFocusKwTest( url, p2 ) + '</li>';
		if ( jQuery( '#content' ).length ) {
			html += '<li>' + wpseoMetaboxL10n.content_text + ystFocusKwTest( jQuery( '#content' ).val(), p ) + '</li>';
		}
		html += '<li>' + wpseoMetaboxL10n.meta_description_text + ystFocusKwTest( metadesc, p ) + '</li>';
		html += '</ul>';
		focuskwresults.html( html );
	}
	else {
		focuskwresults.html( '' );
	}
}

/**
 * This callback is used for variable replacement
 *
 * This is done through a callback as it _could_ be that `ystReplaceVariables` has to do an AJAX request.
 *
 * @callback replaceVariablesCallback
 * @param {string} str The string with the replaced variables in it
 */

/**
 * Replaces variables either with values from wpseoMetaboxL10n, by grabbing them from the page or (ultimately) getting them through AJAX
 *
 * @param {string} str The string with variables to be replaced
 * @param {replaceVariablesCallback} callback Callback function for when the
 */
function ystReplaceVariables( str, callback ) {
	if ( typeof str === 'undefined' ) {
		return;
	}
	// title
	if ( jQuery( '#title' ).length ) {
		str = str.replace( /%%title%%/g, jQuery( '#title' ).val().replace( /(<([^>]+)>)/ig, '' ) );
	}

	// These are added in the head for performance reasons.
	str = str.replace( /%%sitedesc%%/g, wpseoMetaboxL10n.sitedesc );
	str = str.replace( /%%sitename%%/g, wpseoMetaboxL10n.sitename );
	str = str.replace( /%%sep%%/g, wpseoMetaboxL10n.sep );
	str = str.replace( /%%date%%/g, wpseoMetaboxL10n.date );
	str = str.replace( /%%id%%/g, wpseoMetaboxL10n.id );
	str = str.replace( /%%page%%/g, wpseoMetaboxL10n.page );
	str = str.replace( /%%currenttime%%/g, wpseoMetaboxL10n.currenttime );
	str = str.replace( /%%currentdate%%/g, wpseoMetaboxL10n.currentdate );
	str = str.replace( /%%currentday%%/g, wpseoMetaboxL10n.currentday );
	str = str.replace( /%%currentmonth%%/g, wpseoMetaboxL10n.currentmonth );
	str = str.replace( /%%currentyear%%/g, wpseoMetaboxL10n.currentyear );

	str = str.replace( /%%focuskw%%/g, jQuery( '#yoast_wpseo_focuskw' ).val().replace( /(<([^>]+)>)/ig, '' ) );
	// excerpt
	var excerpt = '';
	if ( jQuery( '#excerpt' ).length ) {
		excerpt = ystClean( jQuery( '#excerpt' ).val().replace( /(<([^>]+)>)/ig, '' ) );
		str = str.replace( /%%excerpt_only%%/g, excerpt );
	}
	if ( '' === excerpt && jQuery( '#content' ).length ) {
		excerpt = jQuery( '#content' ).val().replace( /(<([^>]+)>)/ig, '' ).substring( 0, wpseoMetaboxL10n.wpseo_meta_desc_length - 1 );
	}
	str = str.replace( /%%excerpt%%/g, excerpt );

	// parent page
	if ( jQuery( '#parent_id' ).length && jQuery( '#parent_id option:selected' ).text() !== wpseoMetaboxL10n.no_parent_text ) {
		str = str.replace( /%%parent_title%%/g, jQuery( '#parent_id option:selected' ).text() );
	}

	// remove double separators
	var esc_sep = ystEscapeFocusKw( wpseoMetaboxL10n.sep );
	var pattern = new RegExp( esc_sep + ' ' + esc_sep, 'g' );
	str = str.replace( pattern, wpseoMetaboxL10n.sep );

	if ( str.indexOf( '%%' ) !== -1 && str.match( /%%[a-z0-9_-]+%%/i ) !== null ) {
		var regex = /%%[a-z0-9_-]+%%/gi;
		var matches = str.match( regex );
		for ( var i = 0; i < matches.length; i++ ) {
			if ( typeof( replacedVars[ matches[ i ] ] ) === 'undefined' ) {
				str = str.replace( matches[ i ], replacedVars[ matches[ i ] ] );
			}
			else {
				var replaceableVar = matches[ i ];

				// create the cache already, so we don't do the request twice.
				replacedVars[ replaceableVar ] = '';
				ystAjaxReplaceVariables( replaceableVar, callback );
			}
		}
	}
	callback( str );
}

/**
 * Replace a variable with a string, through an AJAX call to WP
 *
 * @param {string} replaceableVar
 * @param {replaceVariablesCallback} callback
 */
function ystAjaxReplaceVariables( replaceableVar, callback ) {
	jQuery.post( ajaxurl, {
			action: 'wpseo_replace_vars',
			string: replaceableVar,
			post_id: jQuery( '#post_ID' ).val(),
			_wpnonce: wpseoMetaboxL10n.wpseo_replace_vars_nonce
		}, function( data ) {
			if ( data ) {
				replacedVars[ replaceableVar ] = data;
			}

			ystReplaceVariables( replaceableVar, callback );
		}
	);
}

/**
 * Updates the title in the snippet preview
 *
 * @param {boolean} [force = false]
 */
function ystUpdateTitle( force ) {
	var title = '';
	var titleElm = jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'title' );
	var titleLengthError = jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'title-length-warning' );
	var divHtml = jQuery( '<div />' );
	var snippetTitle = jQuery( '#wpseosnippet_title' );

	if ( titleElm.val() ) {
		title = titleElm.val().replace( /(<([^>]+)>)/ig, '' );
	}
	else {
		title = wpseoMetaboxL10n.wpseo_title_template;
		title = divHtml.html( title ).text();
	}
	if ( title === '' ) {
		snippetTitle.html( '' );
		titleLengthError.hide();
		return;
	}

	title = ystClean( title );
	title = jQuery.trim( title );
	title = divHtml.text( title ).html();

	if ( force ) {
		titleElm.val( title );
	}

	title = ystReplaceVariables( title, function( title ) {
			title = ystSanitizeTitle( title );

			jQuery( '#wpseosnippet_title' ).html( title );

			// do the placeholder
			var placeholder_title = divHtml.html( title ).text();
			titleElm.attr( 'placeholder', placeholder_title );

			var titleElement = document.getElementById( 'wpseosnippet_title' );
			if ( titleElement !== null ) {
				if ( titleElement.scrollWidth > titleElement.clientWidth ) {
					titleLengthError.show();
				}
				else {
					titleLengthError.hide();
				}
			}

			ystTestFocusKw();
		}
	);
}

/**
 * Cleans the title before use
 *
 * @param {string} title
 * @returns {string}
 */
function ystSanitizeTitle( title ) {
	title = ystClean( title );

	// and now the snippet preview title
	title = ystBoldKeywords( title, false );

	return title;
}

/**
 * Updates the meta description in the snippet preview
 */
function ystUpdateDesc() {
	var desc = jQuery.trim( ystClean( jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'metadesc' ).val() ) );
	var divHtml = jQuery( '<div />' );
	var snippet = jQuery( '#wpseosnippet' );

	if ( desc === '' && wpseoMetaboxL10n.wpseo_metadesc_template !== '' ) {
		desc = wpseoMetaboxL10n.wpseo_metadesc_template;
	}

	if ( desc !== '' ) {
		desc = ystReplaceVariables( desc, function( desc ) {
				desc = divHtml.text( desc ).html();
				desc = ystClean( desc );

				var len = wpseoMetaboxL10n.wpseo_meta_desc_length - desc.length;

				if ( len < 0 ) {
					len = '<span class="wrong">' + len + '</span>';
				}
				else {
					len = '<span class="good">' + len + '</span>';
				}
				jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'metadesc-length' ).html( len );

				desc = ystSanitizeDesc( desc );

				// Clear the autogen description.
				snippet.find( '.desc span.autogen' ).html( '' );
				// Set our new one.
				snippet.find( '.desc span.content' ).html( desc );

				ystTestFocusKw();
			}
		);
	}
	else {
		// Clear the generated description
		snippet.find( '.desc span.content' ).html( '' );
		ystTestFocusKw();

		if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( 'excerpt' ) !== null ) {
			desc = tinyMCE.get( 'excerpt' ).getContent();
			desc = ystClean( desc );
		}

		if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( 'content' ) !== null && desc.length === 0 ) {
			desc = tinyMCE.get( 'content' ).getContent();

			desc = ystClean( desc );
		}

		var focuskw = ystEscapeFocusKw( jQuery.trim( jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'focuskw' ).val() ) );
		if ( focuskw !== '' ) {
			var descsearch = new RegExp( focuskw, 'gim' );
			if ( desc.search( descsearch ) !== -1 && desc.length > wpseoMetaboxL10n.wpseo_meta_desc_length ) {
				desc = desc.substr( desc.search( descsearch ), wpseoMetaboxL10n.wpseo_meta_desc_length );
			}
			else {
				desc = desc.substr( 0, wpseoMetaboxL10n.wpseo_meta_desc_length );
			}
		}
		else {
			desc = desc.substr( 0, wpseoMetaboxL10n.wpseo_meta_desc_length );
		}

		desc = ystSanitizeDesc( desc );

		snippet.find( '.desc span.autogen' ).html( desc );
	}
}

/**
 * Sanitized the description
 *
 * @param {string} desc
 * @returns {string}
 */
function ystSanitizeDesc( desc ) {
	desc = ystTrimDesc( desc );
	desc = ystBoldKeywords( desc, false );

	return desc;
}

/**
 * Trims the description to the desired length
 *
 * @param {string} desc
 * @returns {string}
 */
function ystTrimDesc( desc ) {
	if ( desc.length > wpseoMetaboxL10n.wpseo_meta_desc_length ) {
		var space;
		if ( desc.length > wpseoMetaboxL10n.wpseo_meta_desc_length ) {
			space = desc.lastIndexOf( ' ', ( wpseoMetaboxL10n.wpseo_meta_desc_length - 3 ) );
		}
		else {
			space = wpseoMetaboxL10n.wpseo_meta_desc_length;
		}
		desc = desc.substring( 0, space ).concat( ' ...' );
	}
	return desc;
}

/**
 * Updates the URL in the snippet preview
 */
function ystUpdateURL() {
	var url;
	if ( jQuery( '#editable-post-name-full' ).length ) {
		var name = jQuery( '#editable-post-name-full' ).text();
		url = wpseoMetaboxL10n.wpseo_permalink_template.replace( '%postname%', name ).replace( 'http://', '' );
	}
	url = ystBoldKeywords( url, true );
	jQuery( '#wpseosnippet' ).find( '.url' ).html( url );
	ystTestFocusKw();
}

/**
 * Bolds the keywords in a string
 *
 * @param {string} str
 * @param {boolean} url
 * @returns {string}
 */
function ystBoldKeywords( str, url ) {
	var focuskw = ystEscapeFocusKw( jQuery.trim( jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'focuskw' ).val() ) );
	var keywords;

	if ( focuskw === '' ) {
		return str;
	}
	if ( focuskw.search( ' ' ) !== -1 ) {
		keywords = focuskw.split( ' ' );
	}
	else {
		keywords = new Array( focuskw );
	}
	for ( var i = 0; i < keywords.length; i++ ) {
		var kw = ystClean( keywords[ i ] );
		var kwregex = '';
		if ( url ) {
			kw = kw.replace( ' ', '-' ).toLowerCase();
			kwregex = new RegExp( '([-/])(' + kw + ')([-/])?' );
		}
		else {
			kwregex = new RegExp( '(^|[ \\s\n\r\t\\.,\'\\("\\+;!?:\\-]+)(' + kw + ')($|[ \\s\n\r\t\\.,\'\\)"\\+;!?:\\-]+)', 'gim' );
		}
		if ( str !== undefined ) {
			str = str.replace( kwregex, '$1<strong>$2</strong>$3' );
		}
	}
	return str;
}

/**
 * Updates the entire snippet preview
 */
function ystUpdateSnippet() {
	ystUpdateURL();
	ystUpdateTitle();
	ystUpdateDesc();
}

/**
 * Escapres the focus keyword
 *
 * @param {string} str
 * @returns {string}
 */
function ystEscapeFocusKw( str ) {
	return str.replace( /[\-\[\]\/\{}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&' );
}

(function() {
	var timer = 0;
	return function( callback, ms ) {
		clearTimeout( timer );
		timer = setTimeout( callback, ms );
	};
})();

jQuery( document ).ready( function() {
		/*
		 * Used for caching the replaced vars so we only do an AJAX request for each var once
		 * Ignore for JSHint as it is used but JSHint doesn't properly reflect that
		 */
		var replacedVars = [];  // jshint ignore:line

		if ( jQuery( '.wpseo-metabox-tabs-div' ).length > 0 ) {
			var active_tab = window.location.hash;
			if ( active_tab === '' || active_tab.search( 'wpseo' ) === -1 ) {
				active_tab = 'general';
			}
			else {
				active_tab = active_tab.replace( '#wpseo_', '' );
			}
			jQuery( '.' + active_tab ).addClass( 'active' );

			var descElm = jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'metadesc' );
			var desc = jQuery.trim( ystClean( descElm.val() ) );
			desc = jQuery( '<div />' ).html( desc ).text();
			descElm.val( desc );

			jQuery( 'a.wpseo_tablink' ).click( function() {
					jQuery( '.wpseo-metabox-tabs li' ).removeClass( 'active' );
					jQuery( '.wpseotab' ).removeClass( 'active' );

					var id = jQuery( this ).attr( 'href' ).replace( '#wpseo_', '' );
					jQuery( '.' + id ).addClass( 'active' );
					jQuery( this ).parent('li').addClass( 'active' );

					if ( jQuery( this ).hasClass( 'scroll' ) ) {
						var scrollto = jQuery( this ).attr( 'href' ).replace( 'wpseo_', '' );
						jQuery( 'html, body' ).animate( {
								scrollTop: jQuery( scrollto ).offset().top
							}, 500
						);
					}
				}
			);
		}

		jQuery( '.wpseo-heading' ).hide();
		jQuery( '.wpseo-metabox-tabs' ).show();
		// End Tabs code

		var cache = {}, lastXhr, focuskwHelpTriggered = false;

		jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'focuskw' ).autocomplete( {
				minLength: 3,
				formatResult: function( row ) {
					return jQuery( '<div/>' ).html( row ).html();
				},
				source: function( request, response ) {
					var term = request.term;
					if ( term in cache ) {
						response( cache[ term ] );
						return;
					}
					request._ajax_nonce = wpseoMetaboxL10n.wpseo_keyword_suggest_nonce;
					request.action = 'wpseo_get_suggest';

					lastXhr = jQuery.getJSON( ajaxurl, request, function( data, status, xhr ) {
							cache[ term ] = data;
							if ( xhr === lastXhr ) {
								response( data );
							}
						}
					);
				}
			}
		);

		jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'title' ).keyup( function() {
				ystUpdateTitle();
			}
		);
		jQuery( '#title' ).keyup( function() {
				ystUpdateTitle();
				ystUpdateDesc();
			}
		);
		jQuery( '#parent_id' ).change( function() {
				ystUpdateTitle();
				ystUpdateDesc();
			}
		);
		// DON'T 'optimize' this to use descElm! descElm might not be defined and will cause js errors (Soliloquy issue)
		jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'metadesc' ).keyup( function() {
				ystUpdateDesc();
			}
		);

		// Set time out because of tinymce is initialized later then this is done
		setTimeout(
			function() {
				ystUpdateSnippet();

				// Adding events to content and excerpt
				if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( 'content' ) !== null ) {
					tinyMCE.get( 'content' ).on( 'blur', ystUpdateDesc );
				}

				if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( 'excerpt' ) !== null ) {
					tinyMCE.get( 'excerpt' ).on( 'blur', ystUpdateDesc );
				}
			},
			500
		);

		jQuery( document ).on( 'change', '#' + wpseoMetaboxL10n.field_prefix + 'focuskw', function() {
				var focuskwhelpElm = jQuery( '#focuskwhelp' );
				if ( jQuery( '#' + wpseoMetaboxL10n.field_prefix + 'focuskw' ).val().search( ',' ) !== -1 ) {
					focuskwhelpElm.click();
					focuskwHelpTriggered = true;
				}
				else if ( focuskwHelpTriggered ) {
					focuskwhelpElm.qtip( 'hide' );
					focuskwHelpTriggered = false;
				}

				ystUpdateSnippet();
			}
		);

		jQuery( '.yoast_help' ).qtip(
			{
				content: {
					attr: 'alt'
				},
				position: {
					my: 'bottom left',
					at: 'top center'
				},
				style: {
					tip: {
						corner: true
					},
					classes: 'yoast-qtip qtip-rounded qtip-blue'
				},
				show: 'click',
				hide: {
					fixed: true,
					delay: 500
				}

			}
		);
	}
);
