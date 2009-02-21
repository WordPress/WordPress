/*
 * CodePress regular expressions for CSS syntax highlighting
 */

// CSS
Language.syntax = [
	{ input : /(.*?){(.*?)}/g,output : '<b>$1</b>{<u>$2</u>}' }, // tags, ids, classes, values
	{ input : /([\w-]*?):([^\/])/g,output : '<a>$1</a>:$2' }, // keys
	{ input : /\((.*?)\)/g,output : '(<s>$1</s>)' }, // parameters
	{ input : /\/\*(.*?)\*\//g,output : '<i>/*$1*/</i>'} // comments
]

Language.snippets = []

Language.complete = [
	{ input : '\'',output : '\'$0\'' },
	{ input : '"', output : '"$0"' },
	{ input : '(', output : '\($0\)' },
	{ input : '[', output : '\[$0\]' },
	{ input : '{', output : '{\n\t$0\n}' }		
]

Language.shortcuts = []
