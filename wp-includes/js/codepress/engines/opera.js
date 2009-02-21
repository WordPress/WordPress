/*
 * CodePress - Real Time Syntax Highlighting Editor written in JavaScript - http://codepress.org/
 * 
 * Copyright (C) 2007 Fernando M.A.d.S. <fermads@gmail.com>
 *
 * Contributors :
 *
 * 	Michael Hurni <michael.hurni@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the 
 * GNU Lesser General Public License as published by the Free Software Foundation.
 * 
 * Read the full licence: http://www.opensource.org/licenses/lgpl-license.php
 */


CodePress = {
	scrolling : false,
	autocomplete : true,

	// set initial vars and start sh
	initialize : function() {
		if(typeof(editor)=='undefined' && !arguments[0]) return;
		chars = '|32|46|62|'; // charcodes that trigger syntax highlighting
		cc = '\u2009'; // control char
		editor = document.getElementsByTagName('body')[0];
		document.designMode = 'on';
		document.addEventListener('keyup', this.keyHandler, true);
		window.addEventListener('scroll', function() { if(!CodePress.scrolling) CodePress.syntaxHighlight('scroll') }, false);
		completeChars = this.getCompleteChars();
//		CodePress.syntaxHighlight('init');
	},

	// treat key bindings
	keyHandler : function(evt) {
    	keyCode = evt.keyCode;	
		charCode = evt.charCode;

		if((evt.ctrlKey || evt.metaKey) && evt.shiftKey && charCode!=90)  { // shortcuts = ctrl||appleKey+shift+key!=z(undo) 
			CodePress.shortcuts(charCode?charCode:keyCode);
		}
		else if(completeChars.indexOf('|'+String.fromCharCode(charCode)+'|')!=-1 && CodePress.autocomplete) { // auto complete
			CodePress.complete(String.fromCharCode(charCode));
		}
	    else if(chars.indexOf('|'+charCode+'|')!=-1||keyCode==13) { // syntax highlighting
		 	CodePress.syntaxHighlight('generic');
		}
		else if(keyCode==9 || evt.tabKey) {  // snippets activation (tab)
			CodePress.snippets(evt);
		}
		else if(keyCode==46||keyCode==8) { // save to history when delete or backspace pressed
		 	CodePress.actions.history[CodePress.actions.next()] = editor.innerHTML;
		}
		else if((charCode==122||charCode==121||charCode==90) && evt.ctrlKey) { // undo and redo
			(charCode==121||evt.shiftKey) ? CodePress.actions.redo() :  CodePress.actions.undo(); 
			evt.preventDefault();
		}
		else if(keyCode==86 && evt.ctrlKey)  { // paste
			// TODO: pasted text should be parsed and highlighted
		}
	},

	// put cursor back to its original position after every parsing
	findString : function() {
		var sel = window.getSelection();
		var range = window.document.createRange();
		var span = window.document.getElementsByTagName('span')[0];
			
		range.selectNode(span);
		sel.removeAllRanges();
		sel.addRange(range);
		span.parentNode.removeChild(span);
		//if(self.find(cc))
		//window.getSelection().getRangeAt(0).deleteContents();
	},
	
	// split big files, highlighting parts of it
	split : function(code,flag) {
		if(flag=='scroll') {
			this.scrolling = true;
			return code;
		}
		else {
			this.scrolling = false;
			mid = code.indexOf('<SPAN>');
			if(mid-2000<0) {ini=0;end=4000;}
			else if(mid+2000>code.length) {ini=code.length-4000;end=code.length;}
			else {ini=mid-2000;end=mid+2000;}
			code = code.substring(ini,end);
			return code;
		}
	},
	
	// syntax highlighting parser
	syntaxHighlight : function(flag) {
		//if(document.designMode=='off') document.designMode='on'
		if(flag!='init') {
			var span = document.createElement('span');
			window.getSelection().getRangeAt(0).insertNode(span);
		}

		o = editor.innerHTML;
//		o = o.replace(/<br>/g,'\r\n');
//		o = o.replace(/<(b|i|s|u|a|em|tt|ins|big|cite|strong)?>/g,'');
		//alert(o)
		o = o.replace(/<(?!span|\/span|br).*?>/gi,'');
//		alert(o)
//		x = o;
		x = z = this.split(o,flag);
		//alert(z)
//		x = x.replace(/\r\n/g,'<br>');
		x = x.replace(/\t/g, '        ');


		if(arguments[1]&&arguments[2]) x = x.replace(arguments[1],arguments[2]);
	
		for(i=0;i<Language.syntax.length;i++) 
			x = x.replace(Language.syntax[i].input,Language.syntax[i].output);

		editor.innerHTML = this.actions.history[this.actions.next()] = (flag=='scroll') ? x : o.split(z).join(x); 

		if(flag!='init') this.findString();
	},
	
	getLastWord : function() {
		var rangeAndCaret = CodePress.getRangeAndCaret();
		words = rangeAndCaret[0].substring(rangeAndCaret[1]-40,rangeAndCaret[1]);
		words = words.replace(/[\s\n\r\);\W]/g,'\n').split('\n');
		return words[words.length-1].replace(/[\W]/gi,'').toLowerCase();
	}, 
	
	snippets : function(evt) {
		var snippets = Language.snippets;	
		var trigger = this.getLastWord();
		for (var i=0; i<snippets.length; i++) {
			if(snippets[i].input == trigger) {
				var content = snippets[i].output.replace(/</g,'&lt;');
				content = content.replace(/>/g,'&gt;');
				if(content.indexOf('$0')<0) content += cc;
				else content = content.replace(/\$0/,cc);
				content = content.replace(/\n/g,'<br>');
				var pattern = new RegExp(trigger+cc,'gi');
				evt.preventDefault(); // prevent the tab key from being added
				this.syntaxHighlight('snippets',pattern,content);
			}
		}
	},
	
	readOnly : function() {
		document.designMode = (arguments[0]) ? 'off' : 'on';
	},

	complete : function(trigger) {
		window.getSelection().getRangeAt(0).deleteContents();
		var complete = Language.complete;
		for (var i=0; i<complete.length; i++) {
			if(complete[i].input == trigger) {
				var pattern = new RegExp('\\'+trigger+cc);
				var content = complete[i].output.replace(/\$0/g,cc);
				parent.setTimeout(function () { CodePress.syntaxHighlight('complete',pattern,content)},0); // wait for char to appear on screen
			}
		}
	},

	getCompleteChars : function() {
		var cChars = '';
		for(var i=0;i<Language.complete.length;i++)
			cChars += '|'+Language.complete[i].input;
		return cChars+'|';
	},

	shortcuts : function() {
		var cCode = arguments[0];
		if(cCode==13) cCode = '[enter]';
		else if(cCode==32) cCode = '[space]';
		else cCode = '['+String.fromCharCode(charCode).toLowerCase()+']';
		for(var i=0;i<Language.shortcuts.length;i++)
			if(Language.shortcuts[i].input == cCode)
				this.insertCode(Language.shortcuts[i].output,false);
	},
	
	getRangeAndCaret : function() {	
		var range = window.getSelection().getRangeAt(0);
		var range2 = range.cloneRange();
		var node = range.endContainer;			
		var caret = range.endOffset;
		range2.selectNode(node);	
		return [range2.toString(),caret];
	},
	
	insertCode : function(code,replaceCursorBefore) {
		var range = window.getSelection().getRangeAt(0);
		var node = window.document.createTextNode(code);
		var selct = window.getSelection();
		var range2 = range.cloneRange();
		// Insert text at cursor position
		selct.removeAllRanges();
		range.deleteContents();
		range.insertNode(node);
		// Move the cursor to the end of text
		range2.selectNode(node);		
		range2.collapse(replaceCursorBefore);
		selct.removeAllRanges();
		selct.addRange(range2);
	},
	
	// get code from editor
	getCode : function() {
		var code = editor.innerHTML;
		code = code.replace(/<br>/g,'\n');
		code = code.replace(/\u2009/g,'');
		code = code.replace(/<.*?>/g,'');
		code = code.replace(/&lt;/g,'<');
		code = code.replace(/&gt;/g,'>');
		code = code.replace(/&amp;/gi,'&');
		return code;
	},

	// put code inside editor
	setCode : function() {
		var code = arguments[0];
		code = code.replace(/\u2009/gi,'');
		code = code.replace(/&/gi,'&amp;');
       	code = code.replace(/</g,'&lt;');
        code = code.replace(/>/g,'&gt;');
		editor.innerHTML = code;
	},

	// undo and redo methods
	actions : {
		pos : -1, // actual history position
		history : [], // history vector
		
		undo : function() {
			if(editor.innerHTML.indexOf(cc)==-1){
				window.getSelection().getRangeAt(0).insertNode(document.createTextNode(cc));
			 	this.history[this.pos] = editor.innerHTML;
			}
			this.pos--;
			if(typeof(this.history[this.pos])=='undefined') this.pos++;
			editor.innerHTML = this.history[this.pos];
			CodePress.findString();
		},
		
		redo : function() {
			this.pos++;
			if(typeof(this.history[this.pos])=='undefined') this.pos--;
			editor.innerHTML = this.history[this.pos];
			CodePress.findString();
		},
		
		next : function() { // get next vector position and clean old ones
			if(this.pos>20) this.history[this.pos-21] = undefined;
			return ++this.pos;
		}
	}
}

Language={};
window.addEventListener('load', function() { CodePress.initialize('new'); }, true);
