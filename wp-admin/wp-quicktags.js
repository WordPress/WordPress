// new edit toolbar used with permission
// by Alex King
// http://www.alexking.org/

function edButton() {
	this.id = '';		// used to name the toolbar button
	this.display = '';	// label on button
	this.tagStart = ''; // open tag
	this.tagEnd = '';	// close tag
	this.open = 0;		// set to -1 if tag does not need to be closed
	this.access = ''; // access key
}

var edOpenTags = new Array();

function edAddTag(button) {
	if (eval('ed' + button + '.tagEnd') != '') {
		edOpenTags[edOpenTags.length] = button;
		document.getElementById(eval('ed' + button + '.id')).value = '/' + document.getElementById(eval('ed' + button + '.id')).value;

	}
}

function edRemoveTag(button) {
	for (i = 0; i < edOpenTags.length; i++) {
		if (edOpenTags[i] == button) {
			edOpenTags.splice(i, 1);
			document.getElementById(eval('ed' + button + '.id')).value = 		document.getElementById(eval('ed' + button + '.id')).value.replace('/', '');
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

/*
** TODO: Make it so ins and del have proper datetime attributes, formatted as so: **
1999-02-22T16:03:30-08:00

** Here's my start **
var now = new Date();
var datetime = now.getFullYear() + '-' + 

*/

var ed0 = new edButton();
ed0.id = 'ed_strong';
ed0.display = 'B';
ed0.tagStart = '<strong>';
ed0.tagEnd = '</strong>';
ed0.access = 'b';

var ed1 = new edButton();
ed1.id = 'ed_em';
ed1.display = 'i';
ed1.tagStart = '<em>';
ed1.tagEnd = '</em>';
ed1.access = 'i';

var ed2 = new edButton();
ed2.id = 'ed_del';
ed2.display = 'del';
ed2.tagStart = '<del>';
ed2.tagEnd = '</del>';
ed2.access = 'd';

var ed3 = new edButton();
ed3.id = 'ed_ins';
ed3.display = 'ins';
ed3.tagStart = '<ins>';
ed3.tagEnd = '</ins>';
ed3.access = 'n';

var ed4 = new edButton();
ed4.id = 'ed_link';
ed4.display = 'link';
ed4.tagStart = ''; // special case
ed4.tagEnd = '</a>';
ed4.access = 'a';

var ed5 = new edButton();
ed5.id = 'ed_img';
ed5.display = 'img';
ed5.tagStart = ''; // special case
ed5.tagEnd = '';
ed5.open = -1;
ed5.access = 'm';

var ed6 = new edButton();
ed6.id = 'ed_ul';
ed6.display = 'ul';
ed6.tagStart = '<ul>\n';
ed6.tagEnd = '</ul>';
ed6.access = 'u';

var ed7 = new edButton();
ed7.id = 'ed_ol';
ed7.display = 'ol';
ed7.tagStart = '<ol>\n';
ed7.tagEnd = '</ol>';
ed7.access = 'o';

var ed8 = new edButton();
ed8.id = 'ed_li';
ed8.display = 'li';
ed8.tagStart = '\t<li>';
ed8.tagEnd = '</li>\n';
ed8.access = 'l';

var ed9 = new edButton();
ed9.id = 'ed_block';
ed9.display = 'b-quote';
ed9.tagStart = '<blockquote>';
ed9.tagEnd = '</blockquote>';
ed9.access = 'q';

var ed10 = new edButton();
ed10.id = 'ed_pre';
ed10.display = 'pre';
ed10.tagStart = '<pre>';
ed10.tagEnd = '</pre>';
ed10.access = '';

var ed11 = new edButton();
ed11.id = 'ed_more';
ed11.display = 'more';
ed11.tagStart = '<!--more-->';
ed11.tagEnd = '';
ed11.access = 't';

var ed12 = new edButton();
ed12.id = 'ed_next';
ed12.display = 'n-page';
ed12.tagStart = '<!--nextpage-->';
ed12.tagEnd = '';
ed12.access = 'p';

var edButtonCount = 13;

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

function edLink() {
	this.display = '';
	this.URL = '';
	this.newWin = 0;
}

var edLink0 = new edLink;
edLink0.display = 'WordPress';
edLink0.URL = 'http://wordpress.org/';

var edLink1 = new edLink;
edLink1.display = 'alexking.org';
edLink1.URL = 'http://www.alexking.org/';

var edLinkCount = 2;

function edShowLinks() {
	var tempStr = '<select onchange="edQuickLink(this.options[this.selectedIndex].value, this);"><option value="-1" selected>(Quick Links)</option>';
	for (i = 0; i < edLinkCount; i++) {
		tempStr += '<option value="' + i + '">' + eval('edLink' + i + '.display') + '</option>';
	}
	tempStr += '</select>';
	document.write(tempStr);
}

function edQuickLink(i, thisSelect) {
	if (i > -1) {
		var newWin = '';
		if (eval('edLink' + i + '.newWin') == 1) {
			newWin = ' target="_blank"';
		}
		var tempStr = '<a href="' + eval('edLink' + i + '.URL') + '"' + newWin + '>' + eval('edLink' + i + '.display') + '</a>';
		edInsertContent(edCanvas, tempStr);
	}
	thisSelect.selectedIndex = 0;
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
		word = prompt('Enter a word to look up:', '');
	}
	if (word != '') {
		window.open('http://dictionary.reference.com/search?q=' + word);
	}
}

function edToolbar() {
	document.write('<div id="ed_toolbar">');
	for (i = 0; i < edButtonCount; i++) {
		edShowButton(eval('ed' + i), i);
	}
	document.write('<input type="button" id="ed_spell" class="ed_button" onclick="edSpell(edCanvas);" title="Dictionary lookup" value="Dict." />');
	document.write('<input type="button" id="ed_close" class="ed_button" onclick="edCloseAllTags();" title="Close all open tags" value="Close Tags" />');
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
			sel.text = eval('ed' + i + '.tagStart') + sel.text + eval('ed' + i + '.tagEnd');
		}
		else {
			if (!edCheckOpenTags(i) || eval('ed' + i + '.tagEnd') == '') {
				sel.text = eval('ed' + i + '.tagStart');
				edAddTag(i);
			}
			else {
				sel.text = eval('ed' + i + '.tagEnd');
				edRemoveTag(i);
			}
		}
		myField.focus();
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		var cursorPos;
		if (startPos != endPos) {
			myField.value = myField.value.substring(0, startPos)
			              + eval('ed' + i + '.tagStart')
			              + myField.value.substring(startPos, endPos) 
			              + eval('ed' + i + '.tagEnd') 
			              + myField.value.substring(endPos, myField.value.length);
			cursorPos = endPos + eval('ed' + i + '.tagStart').length + eval('ed' + i + '.tagEnd').length;
		}
		else {
			if (!edCheckOpenTags(i) || eval('ed' + i + '.tagEnd') == '') {
				myField.value = myField.value.substring(0, startPos) 
				              + eval('ed' + i + '.tagStart') 
				              + myField.value.substring(endPos, myField.value.length);
				edAddTag(i);
				cursorPos = startPos + eval('ed' + i + '.tagStart').length;
			}
			else {
				myField.value = myField.value.substring(0, startPos) 
				              + eval('ed' + i + '.tagEnd') 
				              + myField.value.substring(endPos, myField.value.length);
				edRemoveTag(i);
				cursorPos = startPos + eval('ed' + i + '.tagEnd').length;
			}
		}
		myField.focus();
		myField.selectionStart = cursorPos;
		myField.selectionEnd = cursorPos;
	}
	else {
		if (!edCheckOpenTags(i) || eval('ed' + i + '.tagEnd') == '') {
			myField.value += eval('ed' + i + '.tagStart');
			edAddTag(i);
		}
		else {
			myField.value += eval('ed' + i + '.tagEnd');
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

function edInsertLink(myField, i) {
	if (!edCheckOpenTags(i)) {
		eval('ed' + i + '.tagStart = \'<a href="\' + prompt(\'Enter the URL\', \'http://\') + \'">\'');
	}
	edInsertTag(myField, i);
}

function edInsertImage(myField) {
	var myValue = '<img src="' + prompt('Enter the URL of the image', 'http://') + '" alt="' + prompt('Enter a description of the image', '') + '" />';
	edInsertContent(myField, myValue);
}
