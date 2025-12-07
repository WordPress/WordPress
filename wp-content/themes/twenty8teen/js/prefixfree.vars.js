/**
 * -prefix-free plugin for rudimentary CSS variables support
 * @author Lea Verou
 */

(function() {

if(!window.StyleFix || !window.PrefixFree) {
	return;
}

// Feature test
var prefix = PrefixFree.prefix, dummy = document.createElement('_').style;

dummy.cssText = '--foo: red; background: var(--foo);';

if (dummy.background) { // Unprefixed support
	return;
}

// If we’re here, there’s no support.
// But fear not young padawan, cause it’s time foooor… —wait for it— polyfilling!

var vars = {},

	varUsage = function($0, id, fallback) {
		var extra = '',
			found,left,right;
		if (fallback) {
			fallback = fallback.replace(/var\(\s*--([\w-]+)\s*(?:,(.*))?\)/gi, varUsage);
			right = fallback.indexOf(')');
			left = fallback.indexOf('(');
			if ( right > -1 && ((right < left) || left == -1) ) {
				found = fallback.match( /([^)]*)\)(.*)/ );
				if ( found ) {
					fallback = found[1];
					extra = found[2] + ')';
				}
			}
		}
		else fallback = 'initial';
		return (vars[id] || fallback) + extra;
	},

	varFix = function(css, raw, element) {
		return css.replace(/var\(\s*--([\w-]+)\s*(?:,(.*))?\)/gi, varUsage) || css;
	},

	styleElement = function(style) {
		if (style.hasAttribute('data-noprefix')) {
			return;
		}
		var disabled = style.disabled;
		style.textContent = varFix(style.textContent, true, style);
		style.disabled = disabled;
	},

	styleAttribute = function(element) {
		var css = element.getAttribute('style');
		css = varFix(css, false, element);
		element.setAttribute('style', css);
	},

	secondPass = function() {
		// Inline stylesheets
		$('style').forEach(styleElement);
		// Inline styles
		$('[style]').forEach(styleAttribute);
	};

StyleFix.register(function(css) {
	return css.replace(/(?:^|\{|\s|;)--([\w-]+)\s*:([^;}]+)/gi, function($0, id, value) {
		vars[id] = value;
		return $0;  // this keeps the original intact
	});
}, 1);	 // use a low index to get values before other changes

document.addEventListener('StyleFixProcessed', secondPass, false);

function $(expr, con) {
	return [].slice.call((con || document).querySelectorAll(expr));
}

})();
