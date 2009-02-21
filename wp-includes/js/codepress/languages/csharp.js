/*
 * CodePress regular expressions for C# syntax highlighting
 * By Edwin de Jonge
 */
 
Language.syntax = [ // C#
	{ input : /\"(.*?)(\"|<br>|<\/P>)/g, output : '<s>"$1$2</s>' }, // strings double quote
	{ input : /\'(.?)(\'|<br>|<\/P>)/g, output : '<s>\'$1$2</s>' }, // strings single quote 
	{ input : /\b(abstract|as|base|break|case|catch|checked|continue|default|delegate|do|else|event|explicit|extern|false|finally|fixed|for|foreach|get|goto|if|implicit|in|interface|internal|is|lock|namespace|new|null|object|operator|out|override|params|partial|private|protected|public|readonly|ref|return|set|sealed|sizeof|static|stackalloc|switch|this|throw|true|try|typeof|unchecked|unsafe|using|value|virtual|while)\b/g, output : '<b>$1</b>' }, // reserved words
	{ input : /\b(bool|byte|char|class|double|float|int|interface|long|string|struct|void)\b/g, output : '<a>$1</a>' }, // types
	{ input : /([^:]|^)\/\/(.*?)(<br|<\/P)/g, output : '$1<i>//$2</i>$3' }, // comments //	
	{ input : /\/\*(.*?)\*\//g, output : '<i>/*$1*/</i>' } // comments /* */
];

Language.snippets = [];

Language.complete = [ // Auto complete only for 1 character
	{input : '\'',output : '\'$0\'' },
	{input : '"', output : '"$0"' },
	{input : '(', output : '\($0\)' },
	{input : '[', output : '\[$0\]' },
	{input : '{', output : '{\n\t$0\n}' }		
];

Language.shortcuts = [];