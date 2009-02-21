/*
 * CodePress regular expressions for ASP-vbscript syntax highlighting
 */

// ASP VBScript
Language.syntax = [
// all tags
	{ input : /(&lt;[^!%|!%@]*?&gt;)/g, output : '<b>$1</b>' }, 
// style tags	
	{ input : /(&lt;style.*?&gt;)(.*?)(&lt;\/style&gt;)/g, output : '<em>$1</em><em>$2</em><em>$3</em>' }, 
// script tags	
	{ input : /(&lt;script.*?&gt;)(.*?)(&lt;\/script&gt;)/g, output : '<ins>$1</ins><ins>$2</ins><ins>$3</ins>' }, 
// strings "" and attributes
	{ input : /\"(.*?)(\"|<br>|<\/P>)/g, output : '<s>"$1$2</s>' }, 
// ASP Comment
	{ input : /\'(.*?)(\'|<br>|<\/P>)/g, output : '<dfn>\'$1$2</dfn>'}, 
// <%.*
	{ input : /(&lt;%)/g, output : '<strong>$1' }, 
// .*%>	
	{ input : /(%&gt;)/g, output : '$1</strong>' }, 
// <%@...%>	
	{ input : /(&lt;%@)(.+?)(%&gt;)/gi, output : '$1<span>$2</span>$3' }, 
//Numbers	
	{ input : /\b([\d]+)\b/g, output : '<var>$1</var>' }, 
// Reserved Words 1 (Blue)
	{ input : /\b(And|As|ByRef|ByVal|Call|Case|Class|Const|Dim|Do|Each|Else|ElseIf|Empty|End|Eqv|Exit|False|For|Function)\b/gi, output : '<a>$1</a>' }, 
	{ input : /\b(Get|GoTo|If|Imp|In|Is|Let|Loop|Me|Mod|Enum|New|Next|Not|Nothing|Null|On|Option|Or|Private|Public|ReDim|Rem)\b/gi, output : '<a>$1</a>' }, 
	{ input : /\b(Resume|Select|Set|Stop|Sub|Then|To|True|Until|Wend|While|With|Xor|Execute|Randomize|Erase|ExecuteGlobal|Explicit|step)\b/gi, output : '<a>$1</a>' }, 
// Reserved Words 2 (Purple)	
	{ input : /\b(Abandon|Abs|AbsolutePage|AbsolutePosition|ActiveCommand|ActiveConnection|ActualSize|AddHeader|AddNew|AppendChunk)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(AppendToLog|Application|Array|Asc|Atn|Attributes|BeginTrans|BinaryRead|BinaryWrite|BOF|Bookmark|Boolean|Buffer|Byte)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(CacheControl|CacheSize|Cancel|CancelBatch|CancelUpdate|CBool|CByte|CCur|CDate|CDbl|Charset|Chr|CInt|Clear)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(ClientCertificate|CLng|Clone|Close|CodePage|CommandText|CommandType|CommandTimeout|CommitTrans|CompareBookmarks|ConnectionString|ConnectionTimeout)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(Contents|ContentType|Cookies|Cos|CreateObject|CreateParameter|CSng|CStr|CursorLocation|CursorType|DataMember|DataSource|Date|DateAdd|DateDiff)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(DatePart|DateSerial|DateValue|Day|DefaultDatabase|DefinedSize|Delete|Description|Double|EditMode|Eof|EOF|err|Error)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(Exp|Expires|ExpiresAbsolute|Filter|Find|Fix|Flush|Form|FormatCurrency|FormatDateTime|FormatNumber|FormatPercent)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(GetChunk|GetLastError|GetRows|GetString|Global|HelpContext|HelpFile|Hex|Hour|HTMLEncode|IgnoreCase|Index|InStr|InStrRev)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(Int|Integer|IsArray|IsClientConnected|IsDate|IsolationLevel|Join|LBound|LCase|LCID|Left|Len|Lock|LockType|Log|Long|LTrim)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(MapPath|MarshalOptions|MaxRecords|Mid|Minute|Mode|Month|MonthName|Move|MoveFirst|MoveLast|MoveNext|MovePrevious|Name|NextRecordset)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(Now|Number|NumericScale|ObjectContext|Oct|Open|OpenSchema|OriginalValue|PageCount|PageSize|Pattern|PICS|Precision|Prepared|Property)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(Provider|QueryString|RecordCount|Redirect|RegExp|Remove|RemoveAll|Replace|Requery|Request|Response|Resync|Right|Rnd)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(RollbackTrans|RTrim|Save|ScriptTimeout|Second|Seek|Server|ServerVariables|Session|SessionID|SetAbort|SetComplete|Sgn)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(Sin|Size|Sort|Source|Space|Split|Sqr|State|StaticObjects|Status|StayInSync|StrComp|String|StrReverse|Supports|Tan|Time)\b/gi, output : '<u>$1</u>' },
	{ input : /\b(Timeout|Timer|TimeSerial|TimeValue|TotalBytes|Transfer|Trim|Type|Type|UBound|UCase|UnderlyingValue|UnLock|Update|UpdateBatch)\b/gi, output : '<u>$1</u>' }, 
	{ input : /\b(URLEncode|Value|Value|Version|Weekday|WeekdayName|Write|Year)\b/gi, output : '<u>$1</u>' }, 
// Reserved Words 3 (Turquis)
	{ input : /\b(vbBlack|vbRed|vbGreen|vbYellow|vbBlue|vbMagenta|vbCyan|vbWhite|vbBinaryCompare|vbTextCompare)\b/gi, output : '<i>$1</i>' }, 
  	{ input : /\b(vbSunday|vbMonday|vbTuesday|vbWednesday|vbThursday|vbFriday|vbSaturday|vbUseSystemDayOfWeek)\b/gi, output : '<i>$1</i>' }, 
	{ input : /\b(vbFirstJan1|vbFirstFourDays|vbFirstFullWeek|vbGeneralDate|vbLongDate|vbShortDate|vbLongTime|vbShortTime)\b/gi, output : '<i>$1</i>' }, 
	{ input : /\b(vbObjectError|vbCr|VbCrLf|vbFormFeed|vbLf|vbNewLine|vbNullChar|vbNullString|vbTab|vbVerticalTab|vbUseDefault|vbTrue)\b/gi, output : '<i>$1</i>' }, 
	{ input : /\b(vbFalse|vbEmpty|vbNull|vbInteger|vbLong|vbSingle|vbDouble|vbCurrency|vbDate|vbString|vbObject|vbError|vbBoolean|vbVariant)\b/gi, output : '<i>$1</i>' }, 
	{ input : /\b(vbDataObject|vbDecimal|vbByte|vbArray)\b/gi, output : '<i>$1</i>' },
// html comments
	{ input : /(&lt;!--.*?--&gt.)/g, output : '<big>$1</big>' } 
]

Language.Functions = [ 
  	// Output at index 0, must be the desired tagname surrounding a $1
	// Name is the index from the regex that marks the functionname
	{input : /(function|sub)([ ]*?)(\w+)([ ]*?\()/gi , output : '<ins>$1</ins>', name : '$3'}
]

Language.snippets = [
//Conditional
	{ input : 'if', output : 'If $0 Then\n\t\nEnd If' },
	{ input : 'ifelse', output : 'If $0 Then\n\t\n\nElse\n\t\nEnd If' },
	{ input : 'case', output : 'Select Case $0\n\tCase ?\n\tCase Else\nEnd Select'},
//Response
	{ input : 'rw', output : 'Response.Write( $0 )' },
	{ input : 'resc', output : 'Response.Cookies( $0 )' },
	{ input : 'resb', output : 'Response.Buffer'},
	{ input : 'resflu', output : 'Response.Flush()'},
	{ input : 'resend', output : 'Response.End'},
//Request
	{ input : 'reqc', output : 'Request.Cookies( $0 )' },
	{ input : 'rq', output : 'Request.Querystring("$0")' },
	{ input : 'rf', output : 'Request.Form("$0")' },
//FSO
	{ input : 'fso', output : 'Set fso = Server.CreateObject("Scripting.FileSystemObject")\n$0' },
	{ input : 'setfo', output : 'Set fo = fso.getFolder($0)' },
	{ input : 'setfi', output : 'Set fi = fso.getFile($0)' },
	{ input : 'twr', output : 'Set f = fso.CreateTextFile($0,true)\'overwrite\nf.WriteLine()\nf.Close'},
	{ input : 'tre', output : 'Set f = fso.OpenTextFile($0, 1)\nf.ReadAll\nf.Close'},
//Server
	{ input : 'mapp', output : 'Server.Mappath($0)' },
//Loops
	{ input : 'foreach', output : 'For Each $0 in ?\n\t\nNext' },
	{ input : 'for', output : 'For $0 to ? step ?\n\t\nNext' },
	{ input : 'do', output : 'Do While($0)\n\t\nLoop' },
	{ input : 'untilrs', output : 'do until rs.eof\n\t\nrs.movenext\nloop' },
//ADO
	{ input : 'adorec', output : 'Set rs = Server.CreateObject("ADODB.Recordset")' },
	{ input : 'adocon', output : 'Set Conn = Server.CreateObject("ADODB.Connection")' },
	{ input : 'adostr', output : 'Set oStr = Server.CreateObject("ADODB.Stream")' },
//Http Request
	{ input : 'xmlhttp', output : 'Set xmlHttp = Server.CreateObject("Microsoft.XMLHTTP")\nxmlHttp.open("GET", $0, false)\nxmlHttp.send()\n?=xmlHttp.responseText' },
	{ input : 'xmldoc', output : 'Set xmldoc = Server.CreateObject("Microsoft.XMLDOM")\nxmldoc.async=false\nxmldoc.load(request)'},
//Functions
	{ input : 'func', output : 'Function $0()\n\t\n\nEnd Function'},
	{ input : 'sub', output : 'Sub $0()\n\t\nEnd Sub'}

]

Language.complete = [
	//{ input : '\'', output : '\'$0\'' },
	{ input : '"', output : '"$0"' },
	{ input : '(', output : '\($0\)' },
	{ input : '[', output : '\[$0\]' },
	{ input : '{', output : '{\n\t$0\n}' }		
]

Language.shortcuts = [
	{ input : '[space]', output : '&nbsp;' },
	{ input : '[enter]', output : '<br />' } ,
	{ input : '[j]', output : 'testing' },
	{ input : '[7]', output : '&amp;' }
]