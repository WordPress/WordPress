// new edit toolbar used with permission
// by Alex King
// http://www.alexking.org/

var edButtons = new Array();
var edLinks = new Array();
var edOpenTags = new Array();

function edButton(id, display, tagStart, tagEnd, access, open) {
	this.id = id;				// used to name the toolbar button
	this.display = display;		// label on button
	this.tagStart = tagStart; 	// open tag
	this.tagEnd = tagEnd;		// close tag
	this.access = access;		// access key
	this.open = open;			// set to -1 if tag does not need to be closed
}

function zeroise(number, threshold) {
	// FIXME: or we could use an implementation of printf in js here
	var str = number.toString();
	if (number < 0) { str = str.substr(1, str.length) }
	while (str.length < threshold) { str = "0" + str }
	if (number < 0) { str = '-' + str }
	return str;
}

var now = new Date();
var datetime = now.getUTCFullYear() + '-' +
zeroise(now.getUTCMonth() + 1, 2) + '-' +
zeroise(now.getUTCDate(), 2) + 'T' +
zeroise(now.getUTCHours(), 2) + ':' +
zeroise(now.getUTCMinutes(), 2) + ':' +
zeroise(now.getUTCSeconds() ,2) +
'+00:00';

edButtons[edButtons.length] =
new edButton('ed_strong'
,'b'
,'<strong>'
,'</strong>'
,'b'
);

edButtons[edButtons.length] =
new edButton('ed_em'
,'i'
,'<em>'
,'</em>'
,'i'
);

edButtons[edButtons.length] =
new edButton('ed_link'
,'link'
,''
,'</a>'
,'a'
); // special case

edButtons[edButtons.length] =
new edButton('ed_block'
,'b-quote'
,'\n\n<blockquote>'
,'</blockquote>\n\n'
,'q'
);


edButtons[edButtons.length] =
new edButton('ed_del'
,'del'
,'<del datetime="' + datetime + '">'
,'</del>'
,'d'
);

edButtons[edButtons.length] =
new edButton('ed_ins'
,'ins'
,'<ins datetime="' + datetime + '">'
,'</ins>'
,'s'
);

edButtons[edButtons.length] =
new edButton('ed_img'
,'img'
,''
,''
,'m'
,-1
); // special case

edButtons[edButtons.length] =
new edButton('ed_ul'
,'ul'
,'<ul>\n'
,'</ul>\n\n'
,'u'
);

edButtons[edButtons.length] =
new edButton('ed_ol'
,'ol'
,'<ol>\n'
,'</ol>\n\n'
,'o'
);

edButtons[edButtons.length] =
new edButton('ed_li'
,'li'
,'\t<li>'
,'</li>\n'
,'l'
);

edButtons[edButtons.length] =
new edButton('ed_code'
,'code'
,'<code>'
,'</code>'
,'c'
);

edButtons[edButtons.length] =
new edButton('ed_more'
,'more'
,'<!--more-->'
,''
,'t'
,-1
);
/*
edButtons[edButtons.length] =
new edButton('ed_next'
,'page'
,'<!--nextpage-->'
,''
,'p'
,-1
);
*/
function edLink() {
	this.display = '';
	this.URL = '';
	this.newWin = 0;
}

edLinks[edLinks.length] = new edLink('WordPress'
                                    ,'http://wordpress.org/'
                                    );

edLinks[edLinks.length] = new edLink('alexking.org'
                                    ,'http://www.alexking.org/'
                                    );

function edShowButton(button, i) {
	if (button.id == 'ed_img') {
		document.write('<input type="button" id="' + button.id + '" accesskey="' + button.access + '" class="ed_button" onclick="edInsertImage(edCanvas);" value="' + button.display + '" />');
	}
	else if (button.id == 'ed_link') {
		document.write('<input type="button" id="' + button.id + '" accesskey="' + button.access + '" class="ed_button" onclick="edInsertLink(edCanvas, ' + i + ');" value="' + button.display + '" />');
	}
	else {
		document.write('<input type="button" id="' + button.id + '" accesskey="' + button.access + '" class="ed_button" onclick="edInsertTag(edCanvas, ' + i + ');" value="' + button.display + '"  />');
	}
}

function edShowLinks() {
	var tempStr = '<select onchange="edQuickLink(this.options[this.selectedIndex].value, this);"><option value="-1" selected>' + quicktagsL10n.quickLinks + '</option>';
	for (i = 0; i < edLinks.length; i++) {
		tempStr += '<option value="' + i + '">' + edLinks[i].display + '</option>';
	}
	tempStr += '</select>';
	document.write(tempStr);
}

function edAddTag(button) {
	if (edButtons[button].tagEnd != '') {
		edOpenTags[edOpenTags.length] = button;
		document.getElementById(edButtons[button].id).value = '/' + document.getElementById(edButtons[button].id).value;
	}
}

function edRemoveTag(button) {
	for (i = 0; i < edOpenTags.length; i++) {
		if (edOpenTags[i] == button) {
			edOpenTags.splice(i, 1);
			document.getElementById(edButtons[button].id).value = 		document.getElementById(edButtons[button].id).value.replace('/', '');
		}
	}
}

function edCheckOpenTags(button) {
	var tag = 0;
	for (i = 0; i < edOpenTags.length; i++) {
		if (edOpenTags[i] == button) {
			tag++;
		}
	}
	if (tag > 0) {
		return true; // tag found
	}
	else {
		return false; // tag not found
	}
}

function edCloseAllTags() {
	var count = edOpenTags.length;
	for (o = 0; o < count; o++) {
		edInsertTag(edCanvas, edOpenTags[edOpenTags.length - 1]);
	}
}

function edQuickLink(i, thisSelect) {
	if (i > -1) {
		var newWin = '';
		if (edLinks[i].newWin == 1) {
			newWin = ' target="_blank"';
		}
		var tempStr = '<a href="' + edLinks[i].URL + '"' + newWin + '>'
		            + edLinks[i].display
		            + '</a>';
		thisSelect.selectedIndex = 0;
		edInsertContent(edCanvas, tempStr);
	}
	else {
		thisSelect.selectedIndex = 0;
	}
}

function edSpell(myField) {
	var word = '';
	if (document.selection) {
		myField.focus();
	    var sel = document.selection.createRange();
		if (sel.text.length > 0) {
			word = sel.text;
		}
	}
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		if (startPos != endPos) {
			word = myField.value.substring(startPos, endPos);
		}
	}
	if (word == '') {
		word = prompt(quicktagsL10n.wordLookup, '');
	}
	if (word !== null && /^\w[\w ]*$/.test(word)) {
		window.open('http://www.answers.com/' + escape(word));
	}
}

function edToolbar() {
	document.write('<div id="ed_toolbar">');
	for (i = 0; i < edButtons.length; i++) {
		edShowButton(edButtons[i], i);
	}
	document.write('<input type="button" id="ed_spell" class="ed_button" onclick="edSpell(edCanvas);" title="' + quicktagsL10n.dictionaryLookup + '" value="' + quicktagsL10n.lookup + '" />');
	document.write('<input type="button" id="ed_close" class="ed_button" onclick="edCloseAllTags();" title="' + quicktagsL10n.closeAllOpenTags + '" value="' + quicktagsL10n.closeTags + '" />');
//	edShowLinks(); // disabled by default
	document.write('</div>');
}

// insertion code

function edInsertTag(myField, i) {
	//IE support
	if (document.selection) {
		myField.focus();
	    sel = document.selection.createRange();
		if (sel.text.length > 0) {
			sel.text = edButtons[i].tagStart + sel.text + edButtons[i].tagEnd;
		}
		else {
			if (!edCheckOpenTags(i) || edButtons[i].tagEnd == '') {
				sel.text = edButtons[i].tagStart;
				edAddTag(i);
			}
			else {
				sel.text = edButtons[i].tagEnd;
				edRemoveTag(i);
			}
		}
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var cursorPos = endPos;
		var scrollTop = myField.scrollTop;

		if (startPos != endPos) {
			myField.value = myField.value.substring(0, startPos)
			              + edButtons[i].tagStart
			              + myField.value.substring(startPos, endPos)
			              + edButtons[i].tagEnd
			              + myField.value.substring(endPos, myField.value.length);
			cursorPos += edButtons[i].tagStart.length + edButtons[i].tagEnd.length;
		}
		else {
			if (!edCheckOpenTags(i) || edButtons[i].tagEnd == '') {
				myField.value = myField.value.substring(0, startPos)
				              + edButtons[i].tagStart
				              + myField.value.substring(endPos, myField.value.length);
				edAddTag(i);
				cursorPos = startPos + edButtons[i].tagStart.length;
			}
			else {
				myField.value = myField.value.substring(0, startPos)
				              + edButtons[i].tagEnd
				              + myField.value.substring(endPos, myField.value.length);
				edRemoveTag(i);
				cursorPos = startPos + edButtons[i].tagEnd.length;
			}
		}
		myField.focus();
		myField.selectionStart = cursorPos;
		myField.selectionEnd = cursorPos;
		myField.scrollTop = scrollTop;
	}
	else {
		if (!edCheckOpenTags(i) || edButtons[i].tagEnd == '') {
			myField.value += edButtons[i].tagStart;
			edAddTag(i);
		}
		else {
			myField.value += edButtons[i].tagEnd;
			edRemoveTag(i);
		}
		myField.focus();
	}
}

function edInsertContent(myField, myValue) {
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = myValue;
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		              + myValue
                      + myField.value.substring(endPos, myField.value.length);
		myField.focus();
		myField.selectionStart = startPos + myValue.length;
		myField.selectionEnd = startPos + myValue.length;
	} else {
		myField.value += myValue;
		myField.focus();
	}
}

function edInsertLink(myField, i, defaultValue) {
	if (!defaultValue) {
		defaultValue = 'http://';
	}
	if (!edCheckOpenTags(i)) {
		var URL = prompt(quicktagsL10n.enterURL, defaultValue);
		if (URL) {
			edButtons[i].tagStart = '<a href="' + URL + '">';
			edInsertTag(myField, i);
		}
	}
	else {
		edInsertTag(myField, i);
	}
}

function edInsertImage(myField) {
	var myValue = prompt(quicktagsL10n.enterImageURL, 'http://');
	if (myValue) {
		myValue = '<img src="'
				+ myValue
				+ '" alt="' + prompt(quicktagsL10n.enterImageDescription, '')
				+ '" />';
		edInsertContent(myField, myValue);
	}
}


// Allow multiple instances.
// Name = unique value, id = textarea id, container = container div.
// Can disable some buttons by passing comma delimited string as 4th param.
var QTags = function(name, id, container, disabled) {
	var t = this, cont = document.getElementById(container);

	t.Buttons = [];
	t.Links = [];
	t.OpenTags = [];
	t.Canvas = document.getElementById(id);

	if ( ! t.Canvas || ! cont )
		return;

	disabled = ( typeof disabled != 'undefined' ) ? ','+disabled+',' : '';

	t.edShowButton = function(button, i) {
		if ( disabled && (disabled.indexOf(','+button.display+',') != -1) )
			return '';
		else if ( button.id == name+'_img' )
			return '<input type="button" id="' + button.id + '" accesskey="' + button.access + '" class="ed_button" onclick="edInsertImage('+name+'.Canvas);" value="' + button.display + '" />';
		else if (button.id == name+'_link')
			return '<input type="button" id="' + button.id + '" accesskey="' + button.access + '" class="ed_button" onclick="'+name+'.edInsertLink('+i+');" value="'+button.display+'" />';
		else
			return '<input type="button" id="' + button.id + '" accesskey="'+button.access+'" class="ed_button" onclick="'+name+'.edInsertTag('+i+');" value="'+button.display+'" />';
	};

	t.edAddTag = function(button) {
		if ( t.Buttons[button].tagEnd != '' ) {
			t.OpenTags[t.OpenTags.length] = button;
			document.getElementById(t.Buttons[button].id).value = '/' + document.getElementById(t.Buttons[button].id).value;
		}
	};

	t.edRemoveTag = function(button) {
		for ( var i = 0; i < t.OpenTags.length; i++ ) {
			if ( t.OpenTags[i] == button ) {
				t.OpenTags.splice(i, 1);
				document.getElementById(t.Buttons[button].id).value = document.getElementById(t.Buttons[button].id).value.replace('/', '');
			}
		}
	};

	t.edCheckOpenTags = function(button) {
		var tag = 0;
		for ( var i = 0; i < t.OpenTags.length; i++ ) {
			if ( t.OpenTags[i] == button )
				tag++;
		}
		if ( tag > 0 ) return true; // tag found
		else return false; // tag not found
	};

	this.edCloseAllTags = function() {
		var count = t.OpenTags.length;
		for ( var o = 0; o < count; o++ )
			t.edInsertTag(t.OpenTags[t.OpenTags.length - 1]);
	};

	this.edQuickLink = function(i, thisSelect) {
		if ( i > -1 ) {
			var newWin = '';
			if ( Links[i].newWin == 1 ) {
				newWin = ' target="_blank"';
			}
			var tempStr = '<a href="' + Links[i].URL + '"' + newWin + '>'
			            + Links[i].display
			            + '</a>';
			thisSelect.selectedIndex = 0;
			edInsertContent(t.Canvas, tempStr);
		} else {
			thisSelect.selectedIndex = 0;
		}
	};

	// insertion code
	t.edInsertTag = function(i) {
		//IE support
		if ( document.selection ) {
			t.Canvas.focus();
		    sel = document.selection.createRange();
			if ( sel.text.length > 0 ) {
				sel.text = t.Buttons[i].tagStart + sel.text + t.Buttons[i].tagEnd;
			} else {
				if ( ! t.edCheckOpenTags(i) || t.Buttons[i].tagEnd == '' ) {
					sel.text = t.Buttons[i].tagStart;
					t.edAddTag(i);
				} else {
					sel.text = t.Buttons[i].tagEnd;
					t.edRemoveTag(i);
				}
			}
			t.Canvas.focus();
		} else if ( t.Canvas.selectionStart || t.Canvas.selectionStart == '0' ) { //MOZILLA/NETSCAPE support
			var startPos = t.Canvas.selectionStart;
			var endPos = t.Canvas.selectionEnd;
			var cursorPos = endPos;
			var scrollTop = t.Canvas.scrollTop;

			if ( startPos != endPos ) {
				t.Canvas.value = t.Canvas.value.substring(0, startPos)
				              + t.Buttons[i].tagStart
				              + t.Canvas.value.substring(startPos, endPos)
				              + t.Buttons[i].tagEnd
				              + t.Canvas.value.substring(endPos, t.Canvas.value.length);
				cursorPos += t.Buttons[i].tagStart.length + t.Buttons[i].tagEnd.length;
			} else {
				if ( !t.edCheckOpenTags(i) || t.Buttons[i].tagEnd == '' ) {
					t.Canvas.value = t.Canvas.value.substring(0, startPos)
					              + t.Buttons[i].tagStart
					              + t.Canvas.value.substring(endPos, t.Canvas.value.length);
					t.edAddTag(i);
					cursorPos = startPos + t.Buttons[i].tagStart.length;
				} else {
					t.Canvas.value = t.Canvas.value.substring(0, startPos)
					              + t.Buttons[i].tagEnd
					              + t.Canvas.value.substring(endPos, t.Canvas.value.length);
					t.edRemoveTag(i);
					cursorPos = startPos + t.Buttons[i].tagEnd.length;
				}
			}
			t.Canvas.focus();
			t.Canvas.selectionStart = cursorPos;
			t.Canvas.selectionEnd = cursorPos;
			t.Canvas.scrollTop = scrollTop;
		} else {
			if ( ! t.edCheckOpenTags(i) || t.Buttons[i].tagEnd == '' ) {
				t.Canvas.value += Buttons[i].tagStart;
				t.edAddTag(i);
			} else {
				t.Canvas.value += Buttons[i].tagEnd;
				t.edRemoveTag(i);
			}
			t.Canvas.focus();
		}
	};

	this.edInsertLink = function(i, defaultValue) {
		if ( ! defaultValue )
			defaultValue = 'http://';

		if ( ! t.edCheckOpenTags(i) ) {
			var URL = prompt(quicktagsL10n.enterURL, defaultValue);
			if ( URL ) {
				t.Buttons[i].tagStart = '<a href="' + URL + '">';
				t.edInsertTag(i);
			}
		} else {
			t.edInsertTag(i);
		}
	};

	this.edInsertImage = function() {
		var myValue = prompt(quicktagsL10n.enterImageURL, 'http://');
		if ( myValue ) {
			myValue = '<img src="'
					+ myValue
					+ '" alt="' + prompt(quicktagsL10n.enterImageDescription, '')
					+ '" />';
			edInsertContent(t.Canvas, myValue);
		}
	};

	t.Buttons[t.Buttons.length] = new edButton(name+'_strong','b','<strong>','</strong>','b');
	t.Buttons[t.Buttons.length] = new edButton(name+'_em','i','<em>','</em>','i');
	t.Buttons[t.Buttons.length] = new edButton(name+'_link','link','','</a>','a'); // special case
	t.Buttons[t.Buttons.length] = new edButton(name+'_block','b-quote','\n\n<blockquote>','</blockquote>\n\n','q');
	t.Buttons[t.Buttons.length] = new edButton(name+'_del','del','<del datetime="' + datetime + '">','</del>','d');
	t.Buttons[t.Buttons.length] = new edButton(name+'_ins','ins','<ins datetime="' + datetime + '">','</ins>','s');
	t.Buttons[t.Buttons.length] = new edButton(name+'_img','img','','','m',-1); // special case
	t.Buttons[t.Buttons.length] = new edButton(name+'_ul','ul','<ul>\n','</ul>\n\n','u');
	t.Buttons[t.Buttons.length] = new edButton(name+'_ol','ol','<ol>\n','</ol>\n\n','o');
	t.Buttons[t.Buttons.length] = new edButton(name+'_li','li','\t<li>','</li>\n','l');
	t.Buttons[t.Buttons.length] = new edButton(name+'_code','code','<code>','</code>','c');
	t.Buttons[t.Buttons.length] = new edButton(name+'_more','more','<!--more-->','','t',-1);
//	t.Buttons[t.Buttons.length] = new edButton(name+'_next','page','<!--nextpage-->','','p',-1);

	var tb = document.createElement('div');
	tb.id = name+'_qtags';

	var html = '<div id="'+name+'_toolbar">';
	for (var i = 0; i < t.Buttons.length; i++)
		html += t.edShowButton(t.Buttons[i], i);

	html += '<input type="button" id="'+name+'_ed_spell" class="ed_button" onclick="edSpell('+name+'.Canvas);" title="' + quicktagsL10n.dictionaryLookup + '" value="' + quicktagsL10n.lookup + '" />';
	html += '<input type="button" id="'+name+'_ed_close" class="ed_button" onclick="'+name+'.edCloseAllTags();" title="' + quicktagsL10n.closeAllOpenTags + '" value="' + quicktagsL10n.closeTags + '" /></div>';

	tb.innerHTML = html;
	cont.parentNode.insertBefore(tb, cont);

};
