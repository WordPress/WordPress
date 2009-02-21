/*
 * CodePress regular expressions for XSL syntax highlighting
 * By RJ Bruneel
 */

Language.syntax = [ // XSL
	{
	input : /(&lt;[^!]*?&gt;)/g,
	output : '<b>$1</b>' // all tags
	},{
	input : /(&lt;a.*?&gt;|&lt;\/a&gt;)/g,
	output : '<a>$1</a>' // links
	},{
	input : /(&lt;img .*?&gt;)/g,
	output : '<big>$1</big>' // images
	},{
	input : /(&lt;\/?(button|textarea|form|input|select|option|label).*?&gt;)/g,
	output : '<u>$1</u>' // forms
	},{
	input : /(&lt;style.*?&gt;)(.*?)(&lt;\/style&gt;)/g,
	output : '<em>$1</em><em>$2</em><em>$3</em>' // style tags
	},{
	input : /(&lt;script.*?&gt;)(.*?)(&lt;\/script&gt;)/g,
	output : '<strong>$1</strong><tt>$2</tt><strong>$3</strong>' // script tags
	},{	
	input : /(&lt;xsl.*?&gt;|&lt;\/xsl.*?&gt;)/g,
	output : '<xsl>$1</xsl>' // xsl
	},{
	input : /=(".*?")/g,
	output : '=<s>$1</s>' // atributes double quote
	},{
	input : /=('.*?')/g,
	output : '=<s>$1</s>' // atributes single quote
	},{
	input : /(&lt;!--.*?--&gt.)/g,
	output : '<ins>$1</ins>' // comments 
	},{
	input : /\b(alert|window|document|break|continue|do|for|new|this|void|case|default|else|function|return|typeof|while|if|label|switch|var|with|catch|boolean|int|try|false|throws|null|true|goto)\b/g,
	output : '<i>$1</i>' // script reserved words
	}
];

Language.snippets = [
	{input : 'aref', output : '<a href="$0"></a>' },
	{input : 'h1', output : '<h1>$0</h1>' },
	{input : 'h2', output : '<h2>$0</h2>' },
	{input : 'h3', output : '<h3>$0</h3>' },
	{input : 'h4', output : '<h4>$0</h4>' },
	{input : 'h5', output : '<h5>$0</h5>' },
	{input : 'h6', output : '<h6>$0</h6>' },
	{input : 'html', output : '<html>\n\t$0\n</html>' },
	{input : 'head', output : '<head>\n\t<meta http-equiv="content-type" content="text/html; charset=utf-8" />\n\t<title>$0</title>\n\t\n</head>' },
	{input : 'img', output : '<img src="$0" width="" height="" alt="" border="0" />' },
	{input : 'input', output : '<input name="$0" id="" type="" value="" />' },
	{input : 'label', output : '<label for="$0"></label>' },
	{input : 'legend', output : '<legend>\n\t$0\n</legend>' },
	{input : 'link', output : '<link rel="stylesheet" href="$0" type="text/css" media="screen" charset="utf-8" />' },		
	{input : 'base', output : '<base href="$0" />' }, 
	{input : 'body', output : '<body>\n\t$0\n</body>' }, 
	{input : 'css', output : '<link rel="stylesheet" href="$0" type="text/css" media="screen" charset="utf-8" />' },
	{input : 'div', output : '<div>\n\t$0\n</div>' },
	{input : 'divid', output : '<div id="$0">\n\t\n</div>' },
	{input : 'dl', output : '<dl>\n\t<dt>\n\t\t$0\n\t</dt>\n\t<dd></dd>\n</dl>' },
	{input : 'fieldset', output : '<fieldset>\n\t$0\n</fieldset>' },
	{input : 'form', output : '<form action="$0" method="" name="">\n\t\n</form>' },
	{input : 'meta', output : '<meta name="$0" content="" />' },
	{input : 'p', output : '<p>$0</p>' },
	{input : 'b', output : '<b>$0</b>' },
	{input : 'li', output : '<li>$0</li>' },
	{input : 'ul', output : '<ul>$0</ul>' },
	{input : 'ol', output : '<ol>$0</ol>' },
	{input : 'strong', output : '<strong>$0</strong>' },
	{input : 'br', output : '<br />' },
	{input : 'script', output : '<script type="text/javascript" language="javascript" charset="utf-8">\n\t$0\t\n</script>' },
	{input : 'scriptsrc', output : '<script src="$0" type="text/javascript" language="javascript" charset="utf-8"></script>' },
	{input : 'span', output : '<span>$0</span>' },
	{input : 'table', output : '<table border="$0" cellspacing="" cellpadding="">\n\t<tr><th></th></tr>\n\t<tr><td></td></tr>\n</table>' },
	{input : 'style', output : '<style type="text/css" media="screen">\n\t$0\n</style>' },
	{input : 'xsl:stylesheet', output : '<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">' },
	{input : 'xsl:template', output : '<xsl:template>$0</xsl:template>' },
	{input : 'xsl:for-each', output : '<xsl:for-each select="$0"></xsl:for-each>' },
	{input : 'xsl:choose', output : '<xsl:choose>$0<\xsl:choose>' },
	{input : 'xsl:param', output : '<xsl:param name="$0" />' },
	{input : 'xsl:variable', output : '<xsl:variable name="$0"></xsl:variable>' },
	{input : 'xsl:if', output : '<xsl:if test="$0"></xsl:if>' },
	{input : 'xsl:when', output : '<xsl:when test="$0"></xsl:when>' },
	{input : 'xsl:otherwise', output : '<xsl:otherwise>$0</xsl:otherwise>' },
	{input : 'xsl:attribute', output : '<xsl:attribute name="$0"></xsl:attribute>' },
	{input : 'xsl:value-of', output : '<xsl:value-of select="$0"/>' },
	{input : 'xsl:with-param', output : '<xsl:with-param name="$0" select="" />' },
	{input : 'xsl:call-template', output : '<xsl:call-template name="$0">' }

];
	
Language.complete = [ // Auto complete only for 1 character
	{input : '\'',output : '\'$0\'' },
	{input : '"', output : '"$0"' },
	{input : '(', output : '\($0\)' },
	{input : '[', output : '\[$0\]' },
	{input : '{', output : '{\n\t$0\n}' }		
];

Language.shortcuts = [];