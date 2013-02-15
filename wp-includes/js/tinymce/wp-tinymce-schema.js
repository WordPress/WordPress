/**
 * TinyMCE Schema.js
 *
 * Duck-punched by WordPress core to support a sane schema superset.
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

(function(tinymce) {
	var mapCache = {}, makeMap = tinymce.makeMap, each = tinymce.each;

	function split(str, delim) {
		return str.split(delim || ',');
	};

	/**
	 * Unpacks the specified lookup and string data it will also parse it into an object
	 * map with sub object for it's children. This will later also include the attributes.
	 */
	function unpack(lookup, data) {
		var key, elements = {};

		function replace(value) {
			return value.replace(/[A-Z]+/g, function(key) {
				return replace(lookup[key]);
			});
		};

		// Unpack lookup
		for (key in lookup) {
			if (lookup.hasOwnProperty(key))
				lookup[key] = replace(lookup[key]);
		}

		// Unpack and parse data into object map
		replace(data).replace(/#/g, '#text').replace(/(\w+)\[([^\]]+)\]\[([^\]]*)\]/g, function(str, name, attributes, children) {
			attributes = split(attributes, '|');

			elements[name] = {
				attributes : makeMap(attributes),
				attributesOrder : attributes,
				children : makeMap(children, '|', {'#comment' : {}})
			}
		});

		return elements;
	};

	/**
	 * Returns the HTML5 schema and caches it in the mapCache.
	 */
	function getHTML5() {
		var html5 = mapCache.html5;

		if (!html5) {
			html5 = mapCache.html5 = unpack({
					A : 'accesskey|class|contextmenu|dir|draggable|dropzone|hidden|id|inert|itemid|itemprop|itemref|itemscope|itemtype|lang|spellcheck|style|tabindex|title|translate|item|role|subject|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup',
					B : '#|a|abbr|area|audio|b|bdi|bdo|br|button|canvas|cite|code|command|data|datalist|del|dfn|em|embed|i|iframe|img|input|ins|kbd|keygen|label|link|map|mark|math|meta|meter|noscript|object|output|progress|q|ruby|s|samp|script|select|small|span|strong|sub|sup|svg|textarea|time|u|var|video|wbr',
					C : '#|a|abbr|area|address|article|aside|audio|b|bdi|bdo|blockquote|br|button|canvas|cite|code|command|data|datalist|del|details|dfn|dialog|div|dl|em|embed|fieldset|figure|footer|form|h1|h2|h3|h4|h5|h6|header|hgroup|hr|i|iframe|img|input|ins|kbd|keygen|label|link|map|mark|math|menu|meta|meter|nav|noscript|ol|object|output|p|pre|progress|q|ruby|s|samp|script|section|select|small|span|strong|style|sub|sup|svg|table|textarea|time|u|ul|var|video|wbr'
				}, 'html[A|manifest][body|head]' +
					'head[A][base|command|link|meta|noscript|script|style|title]' +
					'title[A][#]' +
					'base[A|href|target][]' +
					'link[A|href|rel|media|type|sizes|crossorigin|hreflang][]' +
					'meta[A|http-equiv|name|content|charset][]' +
					'style[A|type|media|scoped][#]' +
					'script[A|charset|type|src|defer|async|crossorigin][#]' +
					'noscript[A][C]' +
					'body[A|onafterprint|onbeforeprint|onbeforeunload|onblur|onerror|onfocus|onfullscreenchange|onfullscreenerror|onhashchange|onload|onmessage|onoffline|ononline|onpagehide|onpageshow|onpopstate|onresize|onscroll|onstorage|onunload][C]' +
					'section[A][C]' +
					'nav[A][C]' +
					'article[A][C]' +
					'aside[A][C]' +
					'h1[A][B]' +
					'h2[A][B]' +
					'h3[A][B]' +
					'h4[A][B]' +
					'h5[A][B]' +
					'h6[A][B]' +
					'hgroup[A][h1|h2|h3|h4|h5|h6]' +
					'header[A][C]' +
					'footer[A][C]' +
					'address[A][C]' +
					'p[A][B]' +
					'br[A][]' +
					'pre[A][B]' +
					'dialog[A|open][C|dd|dt]' +
					'blockquote[A|cite][C]' +
					'ol[A|start|reversed][li]' +
					'ul[A][li]' +
					'li[A|value][C]' +
					'dl[A][dd|dt]' +
					'dt[A][C|B]' +
					'dd[A][C]' +
					'a[A|href|target|download|ping|rel|media|type][B]' +
					'em[A][B]' +
					'strong[A][B]' +
					'small[A][B]' +
					's[A][B]' +
					'cite[A][B]' +
					'q[A|cite][B]' +
					'dfn[A][B]' +
					'abbr[A][B]' +
					'code[A][B]' +
					'var[A][B]' +
					'samp[A][B]' +
					'kbd[A][B]' +
					'sub[A][B]' +
					'sup[A][B]' +
					'i[A][B]' +
					'b[A][B]' +
					'u[A][B]' +
					'mark[A][B]' +
					'progress[A|value|max][B]' +
					'meter[A|value|min|max|low|high|optimum][B]' +
					'time[A|datetime][B]' +
					'ruby[A][B|rt|rp]' +
					'rt[A][B]' +
					'rp[A][B]' +
					'bdi[A][B]' +
					'bdo[A][B]' +
					'span[A][B]' +
					'ins[A|cite|datetime][C|B]' +
					'del[A|cite|datetime][C|B]' +
					'figure[A][C|legend|figcaption]' +
					'figcaption[A][C]' +
					'img[A|alt|src|srcset|crossorigin|usemap|ismap|width|height][]' +
					'iframe[A|name|src|srcdoc|height|width|sandbox|seamless|allowfullscreen][C|B]' +
					'embed[A|src|height|width|type][]' +
					'object[A|data|type|typemustmatch|name|usemap|form|width|height][C|B|param]' +
					'param[A|name|value][]' +
					'summary[A][B]' +
					'details[A|open][C|legend|summary]' +
					'command[A|type|label|icon|disabled|checked|radiogroup|command][]' +
					'menu[A|type|label][C|li]' +
					'legend[A][C|B]' +
					'div[A][C]' +
					'source[A|src|type|media][]' +
					'track[A|kind|src|srclang|label|default][]' +
					'audio[A|src|autobuffer|autoplay|loop|controls|crossorigin|preload|mediagroup|muted][C|source|track]' +
					'video[A|src|autobuffer|autoplay|loop|controls|width|height|poster|crossorigin|preload|mediagroup|muted][C|source|track]' +
					'hr[A][]' +
					'form[A|accept-charset|action|autocomplete|enctype|method|name|novalidate|target][C]' +
					'fieldset[A|disabled|form|name][C|legend]' +
					'label[A|form|for][B]' +
					'input[A|type|accept|alt|autocomplete|autofocus|checked|dirname|disabled|form|formaction|formenctype|formmethod|formnovalidate|formtarget|height|inputmode|list|max|maxlength|min|multiple|name|pattern|placeholder|readonly|required|size|src|step|value|width|files][]' +
					'button[A|autofocus|disabled|form|formaction|formenctype|formmethod|formnovalidate|formtarget|name|type|value][B]' +
					'select[A|autofocus|disabled|form|multiple|name|required|size][option|optgroup]' +
					'data[A|value][B]' +
					'datalist[A][B|option]' +
					'optgroup[A|disabled|label][option]' +
					'option[A|disabled|selected|label|value][#]' +
					'textarea[A|autocomplete|autofocus|cols|dirname|disabled|form|inputmode|maxlength|name|placeholder|readonly|required|rows|wrap][#]' +
					'keygen[A|autofocus|challenge|disabled|form|keytype|name][]' +
					'output[A|for|form|name][B]' +
					'canvas[A|width|height][a|button|input]' +
					'map[A|name][C|B]' +
					'area[A|alt|coords|shape|href|target|download|ping|rel|media|hreflang|type][]' +
					'math[A][]' +
					'svg[A][]' +
					'table[A][caption|colgroup|thead|tfoot|tbody|tr]' +
					'caption[A][C]' +
					'colgroup[A|span][col]' +
					'col[A|span][]' +
					'thead[A][tr]' +
					'tfoot[A][tr]' +
					'tbody[A][tr]' +
					'tr[A][th|td]' +
					'th[A|headers|rowspan|colspan|scope][C]' +
					'td[A|headers|rowspan|colspan][C]' +
					'wbr[A][]'
			);
		}

		return html5;
	};

	/**
	 * Returns the HTML4 schema and caches it in the mapCache.
	 */
	function getHTML4() {
		var html4 = mapCache.html4;

		if (!html4) {
			// This is the XHTML 1.0 transitional elements with it's attributes and children packed to reduce it's size
			html4 = mapCache.html4 = unpack({
				Z : 'H|K|N|O|P',
				Y : 'X|form|R|Q',
				ZG : 'E|span|width|align|char|charoff|valign',
				X : 'p|T|div|U|W|isindex|fieldset|table',
				ZF : 'E|align|char|charoff|valign',
				W : 'pre|hr|blockquote|address|center|noframes',
				ZE : 'abbr|axis|headers|scope|rowspan|colspan|align|char|charoff|valign|nowrap|bgcolor|width|height',
				ZD : '[E][S]',
				U : 'ul|ol|dl|menu|dir',
				ZC : 'p|Y|div|U|W|table|br|span|bdo|object|applet|img|map|K|N|Q',
				T : 'h1|h2|h3|h4|h5|h6',
				ZB : 'X|S|Q',
				S : 'R|P',
				ZA : 'a|G|J|M|O|P',
				R : 'a|H|K|N|O',
				Q : 'noscript|P',
				P : 'ins|del|script',
				O : 'input|select|textarea|label|button',
				N : 'M|L',
				M : 'em|strong|dfn|code|q|samp|kbd|var|cite|abbr|acronym',
				L : 'sub|sup',
				K : 'J|I',
				J : 'tt|i|b|u|s|strike',
				I : 'big|small|font|basefont',
				H : 'G|F',
				G : 'br|span|bdo',
				F : 'object|applet|img|map|iframe',
				E : 'A|B|C',
				D : 'accesskey|tabindex|onfocus|onblur',
				C : 'onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup',
				B : 'lang|xml:lang|dir',
				A : 'id|class|style|title'
			}, 'script[id|charset|type|language|src|defer|xml:space][]' +
				'style[B|id|type|media|title|xml:space][]' +
				'object[E|declare|classid|codebase|data|type|codetype|archive|standby|width|height|usemap|name|tabindex|align|border|hspace|vspace][#|param|Y]' +
				'param[id|name|value|valuetype|type][]' +
				'p[E|align][#|S]' +
				'a[E|D|charset|type|name|href|hreflang|rel|rev|shape|coords|target][#|Z]' +
				'br[A|clear][]' +
				'span[E][#|S]' +
				'bdo[A|C|B][#|S]' +
				'applet[A|codebase|archive|code|object|alt|name|width|height|align|hspace|vspace][#|param|Y]' +
				'h1[E|align][#|S]' +
				'img[E|src|alt|name|longdesc|width|height|usemap|ismap|align|border|hspace|vspace][]' +
				'map[B|C|A|name][X|form|Q|area]' +
				'h2[E|align][#|S]' +
				'iframe[A|longdesc|name|src|frameborder|marginwidth|marginheight|scrolling|align|width|height][#|Y]' +
				'h3[E|align][#|S]' +
				'tt[E][#|S]' +
				'i[E][#|S]' +
				'b[E][#|S]' +
				'u[E][#|S]' +
				's[E][#|S]' +
				'strike[E][#|S]' +
				'big[E][#|S]' +
				'small[E][#|S]' +
				'font[A|B|size|color|face][#|S]' +
				'basefont[id|size|color|face][]' +
				'em[E][#|S]' +
				'strong[E][#|S]' +
				'dfn[E][#|S]' +
				'code[E][#|S]' +
				'q[E|cite][#|S]' +
				'samp[E][#|S]' +
				'kbd[E][#|S]' +
				'var[E][#|S]' +
				'cite[E][#|S]' +
				'abbr[E][#|S]' +
				'acronym[E][#|S]' +
				'sub[E][#|S]' +
				'sup[E][#|S]' +
				'input[E|D|type|name|value|checked|disabled|readonly|size|maxlength|src|alt|usemap|onselect|onchange|accept|align][]' +
				'select[E|name|size|multiple|disabled|tabindex|onfocus|onblur|onchange][optgroup|option]' +
				'optgroup[E|disabled|label][option]' +
				'option[E|selected|disabled|label|value][]' +
				'textarea[E|D|name|rows|cols|disabled|readonly|onselect|onchange][]' +
				'label[E|for|accesskey|onfocus|onblur][#|S]' +
				'button[E|D|name|value|type|disabled][#|p|T|div|U|W|table|G|object|applet|img|map|K|N|Q]' +
				'h4[E|align][#|S]' +
				'ins[E|cite|datetime][#|Y]' +
				'h5[E|align][#|S]' +
				'del[E|cite|datetime][#|Y]' +
				'h6[E|align][#|S]' +
				'div[E|align][#|Y]' +
				'ul[E|type|compact][li]' +
				'li[E|type|value][#|Y]' +
				'ol[E|type|compact|start][li]' +
				'dl[E|compact][dt|dd]' +
				'dt[E][#|S]' +
				'dd[E][#|Y]' +
				'menu[E|compact][li]' +
				'dir[E|compact][li]' +
				'pre[E|width|xml:space][#|ZA]' +
				'hr[E|align|noshade|size|width][]' +
				'blockquote[E|cite][#|Y]' +
				'address[E][#|S|p]' +
				'center[E][#|Y]' +
				'noframes[E][#|Y]' +
				'isindex[A|B|prompt][]' +
				'fieldset[E][#|legend|Y]' +
				'legend[E|accesskey|align][#|S]' +
				'table[E|summary|width|border|frame|rules|cellspacing|cellpadding|align|bgcolor][caption|col|colgroup|thead|tfoot|tbody|tr]' +
				'caption[E|align][#|S]' +
				'col[ZG][]' +
				'colgroup[ZG][col]' +
				'thead[ZF][tr]' +
				'tr[ZF|bgcolor][th|td]' +
				'th[E|ZE][#|Y]' +
				'form[E|action|method|name|enctype|onsubmit|onreset|accept|accept-charset|target][#|X|R|Q]' +
				'noscript[E][#|Y]' +
				'td[E|ZE][#|Y]' +
				'tfoot[ZF][tr]' +
				'tbody[ZF][tr]' +
				'area[E|D|shape|coords|href|nohref|alt|target][]' +
				'base[id|href|target][]' +
				'body[E|onload|onunload|background|bgcolor|text|link|vlink|alink][#|Y]'
			);
		}

		return html4;
	};

	/**
	 * WordPress Core
	 *
	 * Returns a schema that is the result of a deep merge between the HTML5
	 * and HTML4 schemas.
	 */
	function getSaneSchema() {
		var cachedMapCache = mapCache,
			html5, html4;

		if ( mapCache.sane )
			return mapCache.sane;

		// Bust the mapCache so we're not dealing with the other schema objects.
		mapCache = {};
		html5 = getHTML5();
		html4 = getHTML4();
		mapCache = cachedMapCache;

		each( html4, function( html4settings, tag ) {
			var html5settings = html5[ tag ],
				difference = [];

			// Merge tags missing in HTML5 mode.
			if ( ! html5settings ) {
				html5[ tag ] = html4settings;
				return;
			}

			// Merge attributes missing from this HTML5 tag.
			each( html4settings.attributes, function( attribute, key ) {
				if ( ! html5settings.attributes[ key ] )
					html5settings.attributes[ key ] = attribute;
			});

			// Merge any missing attributes into the attributes order.
			each( html4settings.attributesOrder, function( key ) {
				if ( -1 === tinymce.inArray( html5settings.attributesOrder, key ) )
					difference.push( key );
			});

			html5settings.attributesOrder = html5settings.attributesOrder.concat( difference );

			// Merge children missing from this HTML5 tag.
			each( html4settings.children, function( child, key ) {
				if ( ! html5settings.children[ key ] )
					html5settings.children[ key ] = child;
			});
		});

		return mapCache.sane = html5;
	}

	/**
	 * Schema validator class.
	 *
	 * @class tinymce.html.Schema
	 * @example
	 *  if (tinymce.activeEditor.schema.isValidChild('p', 'span'))
	 *    alert('span is valid child of p.');
	 *
	 *  if (tinymce.activeEditor.schema.getElementRule('p'))
	 *    alert('P is a valid element.');
	 *
	 * @class tinymce.html.Schema
	 * @version 3.4
	 */

	/**
	 * Constructs a new Schema instance.
	 *
	 * @constructor
	 * @method Schema
	 * @param {Object} settings Name/value settings object.
	 */
	tinymce.html.Schema = function(settings) {
		var self = this, elements = {}, children = {}, patternElements = [], validStyles, schemaItems;
		var whiteSpaceElementsMap, selfClosingElementsMap, shortEndedElementsMap, boolAttrMap, blockElementsMap, nonEmptyElementsMap, customElementsMap = {};

		// Creates an lookup table map object for the specified option or the default value
		function createLookupTable(option, default_value, extend) {
			var value = settings[option];

			if (!value) {
				// Get cached default map or make it if needed
				value = mapCache[option];

				if (!value) {
					value = makeMap(default_value, ' ', makeMap(default_value.toUpperCase(), ' '));
					value = tinymce.extend(value, extend);

					mapCache[option] = value;
				}
			} else {
				// Create custom map
				value = makeMap(value, ',', makeMap(value.toUpperCase(), ' '));
			}

			return value;
		};

		settings = settings || {};

		/**
		 * WordPress core uses a sane schema in place of the default "HTML5" schema.
		 */
		schemaItems = settings.schema == "html5" ? getSaneSchema() : getHTML4();

		// Allow all elements and attributes if verify_html is set to false
		if (settings.verify_html === false)
			settings.valid_elements = '*[*]';

		// Build styles list
		if (settings.valid_styles) {
			validStyles = {};

			// Convert styles into a rule list
			each(settings.valid_styles, function(value, key) {
				validStyles[key] = tinymce.explode(value);
			});
		}

		// Setup map objects
		whiteSpaceElementsMap = createLookupTable('whitespace_elements', 'pre script noscript style textarea');
		selfClosingElementsMap = createLookupTable('self_closing_elements', 'colgroup dd dt li option p td tfoot th thead tr');
		shortEndedElementsMap = createLookupTable('short_ended_elements', 'area base basefont br col frame hr img input isindex link meta param embed source wbr');
		boolAttrMap = createLookupTable('boolean_attributes', 'checked compact declare defer disabled ismap multiple nohref noresize noshade nowrap readonly selected autoplay loop controls');
		nonEmptyElementsMap = createLookupTable('non_empty_elements', 'td th iframe video audio object', shortEndedElementsMap);
		textBlockElementsMap = createLookupTable('text_block_elements', 'h1 h2 h3 h4 h5 h6 p div address pre form ' +
						'blockquote center dir fieldset header footer article section hgroup aside nav figure');
		blockElementsMap = createLookupTable('block_elements', 'hr table tbody thead tfoot ' +
						'th tr td li ol ul caption dl dt dd noscript menu isindex option datalist select optgroup', textBlockElementsMap);

		// Converts a wildcard expression string to a regexp for example *a will become /.*a/.
		function patternToRegExp(str) {
			return new RegExp('^' + str.replace(/([?+*])/g, '.$1') + '$');
		};

		// Parses the specified valid_elements string and adds to the current rules
		// This function is a bit hard to read since it's heavily optimized for speed
		function addValidElements(valid_elements) {
			var ei, el, ai, al, yl, matches, element, attr, attrData, elementName, attrName, attrType, attributes, attributesOrder,
				prefix, outputName, globalAttributes, globalAttributesOrder, transElement, key, childKey, value,
				elementRuleRegExp = /^([#+\-])?([^\[\/]+)(?:\/([^\[]+))?(?:\[([^\]]+)\])?$/,
				attrRuleRegExp = /^([!\-])?(\w+::\w+|[^=:<]+)?(?:([=:<])(.*))?$/,
				hasPatternsRegExp = /[*?+]/;

			if (valid_elements) {
				// Split valid elements into an array with rules
				valid_elements = split(valid_elements);

				if (elements['@']) {
					globalAttributes = elements['@'].attributes;
					globalAttributesOrder = elements['@'].attributesOrder;
				}

				// Loop all rules
				for (ei = 0, el = valid_elements.length; ei < el; ei++) {
					// Parse element rule
					matches = elementRuleRegExp.exec(valid_elements[ei]);
					if (matches) {
						// Setup local names for matches
						prefix = matches[1];
						elementName = matches[2];
						outputName = matches[3];
						attrData = matches[4];

						// Create new attributes and attributesOrder
						attributes = {};
						attributesOrder = [];

						// Create the new element
						element = {
							attributes : attributes,
							attributesOrder : attributesOrder
						};

						// Padd empty elements prefix
						if (prefix === '#')
							element.paddEmpty = true;

						// Remove empty elements prefix
						if (prefix === '-')
							element.removeEmpty = true;

						// Copy attributes from global rule into current rule
						if (globalAttributes) {
							for (key in globalAttributes)
								attributes[key] = globalAttributes[key];

							attributesOrder.push.apply(attributesOrder, globalAttributesOrder);
						}

						// Attributes defined
						if (attrData) {
							attrData = split(attrData, '|');
							for (ai = 0, al = attrData.length; ai < al; ai++) {
								matches = attrRuleRegExp.exec(attrData[ai]);
								if (matches) {
									attr = {};
									attrType = matches[1];
									attrName = matches[2].replace(/::/g, ':');
									prefix = matches[3];
									value = matches[4];

									// Required
									if (attrType === '!') {
										element.attributesRequired = element.attributesRequired || [];
										element.attributesRequired.push(attrName);
										attr.required = true;
									}

									// Denied from global
									if (attrType === '-') {
										delete attributes[attrName];
										attributesOrder.splice(tinymce.inArray(attributesOrder, attrName), 1);
										continue;
									}

									// Default value
									if (prefix) {
										// Default value
										if (prefix === '=') {
											element.attributesDefault = element.attributesDefault || [];
											element.attributesDefault.push({name: attrName, value: value});
											attr.defaultValue = value;
										}

										// Forced value
										if (prefix === ':') {
											element.attributesForced = element.attributesForced || [];
											element.attributesForced.push({name: attrName, value: value});
											attr.forcedValue = value;
										}

										// Required values
										if (prefix === '<')
											attr.validValues = makeMap(value, '?');
									}

									// Check for attribute patterns
									if (hasPatternsRegExp.test(attrName)) {
										element.attributePatterns = element.attributePatterns || [];
										attr.pattern = patternToRegExp(attrName);
										element.attributePatterns.push(attr);
									} else {
										// Add attribute to order list if it doesn't already exist
										if (!attributes[attrName])
											attributesOrder.push(attrName);

										attributes[attrName] = attr;
									}
								}
							}
						}

						// Global rule, store away these for later usage
						if (!globalAttributes && elementName == '@') {
							globalAttributes = attributes;
							globalAttributesOrder = attributesOrder;
						}

						// Handle substitute elements such as b/strong
						if (outputName) {
							element.outputName = elementName;
							elements[outputName] = element;
						}

						// Add pattern or exact element
						if (hasPatternsRegExp.test(elementName)) {
							element.pattern = patternToRegExp(elementName);
							patternElements.push(element);
						} else
							elements[elementName] = element;
					}
				}
			}
		};

		function setValidElements(valid_elements) {
			elements = {};
			patternElements = [];

			addValidElements(valid_elements);

			each(schemaItems, function(element, name) {
				children[name] = element.children;
			});
		};

		// Adds custom non HTML elements to the schema
		function addCustomElements(custom_elements) {
			var customElementRegExp = /^(~)?(.+)$/;

			if (custom_elements) {
				each(split(custom_elements), function(rule) {
					var matches = customElementRegExp.exec(rule),
						inline = matches[1] === '~',
						cloneName = inline ? 'span' : 'div',
						name = matches[2];

					children[name] = children[cloneName];
					customElementsMap[name] = cloneName;

					// If it's not marked as inline then add it to valid block elements
					if (!inline) {
						blockElementsMap[name.toUpperCase()] = {};
						blockElementsMap[name] = {};
					}

					// Add elements clone if needed
					if (!elements[name]) {
						elements[name] = elements[cloneName];
					}

					// Add custom elements at span/div positions
					each(children, function(element, child) {
						if (element[cloneName])
							element[name] = element[cloneName];
					});
				});
			}
		};

		// Adds valid children to the schema object
		function addValidChildren(valid_children) {
			var childRuleRegExp = /^([+\-]?)(\w+)\[([^\]]+)\]$/;

			if (valid_children) {
				each(split(valid_children), function(rule) {
					var matches = childRuleRegExp.exec(rule), parent, prefix;

					if (matches) {
						prefix = matches[1];

						// Add/remove items from default
						if (prefix)
							parent = children[matches[2]];
						else
							parent = children[matches[2]] = {'#comment' : {}};

						parent = children[matches[2]];

						each(split(matches[3], '|'), function(child) {
							if (prefix === '-')
								delete parent[child];
							else
								parent[child] = {};
						});
					}
				});
			}
		};

		function getElementRule(name) {
			var element = elements[name], i;

			// Exact match found
			if (element)
				return element;

			// No exact match then try the patterns
			i = patternElements.length;
			while (i--) {
				element = patternElements[i];

				if (element.pattern.test(name))
					return element;
			}
		};

		if (!settings.valid_elements) {
			// No valid elements defined then clone the elements from the schema spec
			each(schemaItems, function(element, name) {
				elements[name] = {
					attributes : element.attributes,
					attributesOrder : element.attributesOrder
				};

				children[name] = element.children;
			});

			// Switch these on HTML4
			if (settings.schema != "html5") {
				each(split('strong/b,em/i'), function(item) {
					item = split(item, '/');
					elements[item[1]].outputName = item[0];
				});
			}

			// Add default alt attribute for images
			elements.img.attributesDefault = [{name: 'alt', value: ''}];

			// Remove these if they are empty by default
			each(split('ol,ul,sub,sup,blockquote,span,font,a,table,tbody,tr'), function(name) {
				if (elements[name]) {
					elements[name].removeEmpty = true;
				}
			});

			// Padd these by default
			each(split('p,h1,h2,h3,h4,h5,h6,th,td,pre,div,address,caption'), function(name) {
				elements[name].paddEmpty = true;
			});
		} else
			setValidElements(settings.valid_elements);

		addCustomElements(settings.custom_elements);
		addValidChildren(settings.valid_children);
		addValidElements(settings.extended_valid_elements);

		// Todo: Remove this when we fix list handling to be valid
		addValidChildren('+ol[ul|ol],+ul[ul|ol]');

		// Delete invalid elements
		if (settings.invalid_elements) {
			tinymce.each(tinymce.explode(settings.invalid_elements), function(item) {
				if (elements[item])
					delete elements[item];
			});
		}

		// If the user didn't allow span only allow internal spans
		if (!getElementRule('span'))
			addValidElements('span[!data-mce-type|*]');

		/**
		 * Name/value map object with valid parents and children to those parents.
		 *
		 * @example
		 * children = {
		 *    div:{p:{}, h1:{}}
		 * };
		 * @field children
		 * @type {Object}
		 */
		self.children = children;

		/**
		 * Name/value map object with valid styles for each element.
		 *
		 * @field styles
		 * @type {Object}
		 */
		self.styles = validStyles;

		/**
		 * Returns a map with boolean attributes.
		 *
		 * @method getBoolAttrs
		 * @return {Object} Name/value lookup map for boolean attributes.
		 */
		self.getBoolAttrs = function() {
			return boolAttrMap;
		};

		/**
		 * Returns a map with block elements.
		 *
		 * @method getBlockElements
		 * @return {Object} Name/value lookup map for block elements.
		 */
		self.getBlockElements = function() {
			return blockElementsMap;
		};

		/**
		 * Returns a map with text block elements. Such as: p,h1-h6,div,address
		 *
		 * @method getTextBlockElements
		 * @return {Object} Name/value lookup map for block elements.
		 */
		self.getTextBlockElements = function() {
			return textBlockElementsMap;
		};

		/**
		 * Returns a map with short ended elements such as BR or IMG.
		 *
		 * @method getShortEndedElements
		 * @return {Object} Name/value lookup map for short ended elements.
		 */
		self.getShortEndedElements = function() {
			return shortEndedElementsMap;
		};

		/**
		 * Returns a map with self closing tags such as <li>.
		 *
		 * @method getSelfClosingElements
		 * @return {Object} Name/value lookup map for self closing tags elements.
		 */
		self.getSelfClosingElements = function() {
			return selfClosingElementsMap;
		};

		/**
		 * Returns a map with elements that should be treated as contents regardless if it has text
		 * content in them or not such as TD, VIDEO or IMG.
		 *
		 * @method getNonEmptyElements
		 * @return {Object} Name/value lookup map for non empty elements.
		 */
		self.getNonEmptyElements = function() {
			return nonEmptyElementsMap;
		};

		/**
		 * Returns a map with elements where white space is to be preserved like PRE or SCRIPT.
		 *
		 * @method getWhiteSpaceElements
		 * @return {Object} Name/value lookup map for white space elements.
		 */
		self.getWhiteSpaceElements = function() {
			return whiteSpaceElementsMap;
		};

		/**
		 * Returns true/false if the specified element and it's child is valid or not
		 * according to the schema.
		 *
		 * @method isValidChild
		 * @param {String} name Element name to check for.
		 * @param {String} child Element child to verify.
		 * @return {Boolean} True/false if the element is a valid child of the specified parent.
		 */
		self.isValidChild = function(name, child) {
			var parent = children[name];

			return !!(parent && parent[child]);
		};

		/**
		 * Returns true/false if the specified element name and optional attribute is
		 * valid according to the schema.
		 *
		 * @method isValid
		 * @param {String} name Name of element to check.
		 * @param {String} attr Optional attribute name to check for.
		 * @return {Boolean} True/false if the element and attribute is valid.
		 */
		self.isValid = function(name, attr) {
			var attrPatterns, i, rule = getElementRule(name);

			// Check if it's a valid element
			if (rule) {
				if (attr) {
					// Check if attribute name exists
					if (rule.attributes[attr]) {
						return true;
					}

					// Check if attribute matches a regexp pattern
					attrPatterns = rule.attributePatterns;
					if (attrPatterns) {
						i = attrPatterns.length;
						while (i--) {
							if (attrPatterns[i].pattern.test(name)) {
								return true;
							}
						}
					}
				} else {
					return true;
				}
			}

			// No match
			return false;
		};

		/**
		 * Returns true/false if the specified element is valid or not
		 * according to the schema.
		 *
		 * @method getElementRule
		 * @param {String} name Element name to check for.
		 * @return {Object} Element object or undefined if the element isn't valid.
		 */
		self.getElementRule = getElementRule;

		/**
		 * Returns an map object of all custom elements.
		 *
		 * @method getCustomElements
		 * @return {Object} Name/value map object of all custom elements.
		 */
		self.getCustomElements = function() {
			return customElementsMap;
		};

		/**
		 * Parses a valid elements string and adds it to the schema. The valid elements format is for example "element[attr=default|otherattr]".
		 * Existing rules will be replaced with the ones specified, so this extends the schema.
		 *
		 * @method addValidElements
		 * @param {String} valid_elements String in the valid elements format to be parsed.
		 */
		self.addValidElements = addValidElements;

		/**
		 * Parses a valid elements string and sets it to the schema. The valid elements format is for example "element[attr=default|otherattr]".
		 * Existing rules will be replaced with the ones specified, so this extends the schema.
		 *
		 * @method setValidElements
		 * @param {String} valid_elements String in the valid elements format to be parsed.
		 */
		self.setValidElements = setValidElements;

		/**
		 * Adds custom non HTML elements to the schema.
		 *
		 * @method addCustomElements
		 * @param {String} custom_elements Comma separated list of custom elements to add.
		 */
		self.addCustomElements = addCustomElements;

		/**
		 * Parses a valid children string and adds them to the schema structure. The valid children format is for example: "element[child1|child2]".
		 *
		 * @method addValidChildren
		 * @param {String} valid_children Valid children elements string to parse
		 */
		self.addValidChildren = addValidChildren;

		self.elements = elements;
	};
})(tinymce);
