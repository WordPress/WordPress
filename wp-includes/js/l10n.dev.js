//Used to ensure that Entities used in L10N strings are correct
function convertEntities(o) {
	var c, v;
	c = function(s) {
		if (/&[^;]+;/.test(s)) {
			var e = document.createElement("div");
			e.innerHTML = s;
			return !e.firstChild ? s : e.firstChild.nodeValue;
		}
		return s;
	}

	if ( typeof o === 'string' ) {
		return c(o);
	} else if ( typeof o === 'object' ) {
		for (v in o) {
			if ( typeof o[v] === 'string' ) {
				o[v] = c(o[v]);
			}
		}
	}
	return o;
}