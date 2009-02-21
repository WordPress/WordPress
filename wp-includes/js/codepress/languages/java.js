/*
 * CodePress regular expressions for Java syntax highlighting
 */
 
// Java
Language.syntax = [
	{ input : /\"(.*?)(\"|<br>|<\/P>)/g, output : '<s>"$1$2</s>'}, // strings double quote
	{ input : /\'(.*?)(\'|<br>|<\/P>)/g, output : '<s>\'$1$2</s>'}, // strings single quote
	{ input : /\b(abstract|continue|for|new|switch|assert|default|goto|package|synchronized|boolean|do|if|private|this|break|double|implements|protected|throw|byte|else|import|public|throws|case|enum|instanceof|return|transient|catch|extends|int|short|try|char|final|interface|static|void|class|finally|long|strictfp|volatile|const|float|native|super|while)\b/g, output : '<b>$1</b>'}, // reserved words
	{ input : /([^:]|^)\/\/(.*?)(<br|<\/P)/g, output : '$1<i>//$2</i>$3'}, // comments //	
	{ input : /\/\*(.*?)\*\//g, output : '<i>/*$1*/</i>' }// comments /* */
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
