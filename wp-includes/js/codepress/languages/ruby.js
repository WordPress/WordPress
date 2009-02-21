/*
 * CodePress regular expressions for Perl syntax highlighting
 */

// Ruby
Language.syntax = [
	{ input : /\"(.*?)(\"|<br>|<\/P>)/g, output : '<s>"$1$2</s>' }, // strings double quote 
	{ input : /\'(.*?)(\'|<br>|<\/P>)/g, output : '<s>\'$1$2</s>' }, // strings single quote
	{ input : /([\$\@\%]+)([\w\.]*)/g, output : '<a>$1$2</a>' }, // vars
	{ input : /(def\s+)([\w\.]*)/g, output : '$1<em>$2</em>' }, // functions
	{ input : /\b(alias|and|BEGIN|begin|break|case|class|def|defined|do|else|elsif|END|end|ensure|false|for|if|in|module|next|nil|not|or|redo|rescue|retry|return|self|super|then|true|undef|unless|until|when|while|yield)\b/g, output : '<b>$1</b>' }, // reserved words
	{ input  : /([\(\){}])/g, output : '<u>$1</u>' }, // special chars
	{ input  : /#(.*?)(<br>|<\/P>)/g, output : '<i>#$1</i>$2' } // comments
];

Language.snippets = []

Language.complete = [
	{ input : '\'',output : '\'$0\'' },
	{ input : '"', output : '"$0"' },
	{ input : '(', output : '\($0\)' },
	{ input : '[', output : '\[$0\]' },
	{ input : '{', output : '{\n\t$0\n}' }		
]

Language.shortcuts = []
