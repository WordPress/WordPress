<HTML>
<HEAD><TITLE>Loading Spell Checker</TITLE>
<SCRIPT ID=clientEventHandlersJS LANGUAGE=javascript>
<!--

function window_onload() {
document.SPELLDATA.formname.value=opener.document.SPELLDATA.formname.value
document.SPELLDATA.subjectname.value=opener.document.SPELLDATA.subjectname.value
document.SPELLDATA.messagebodyname.value=opener.document.SPELLDATA.messagebodyname.value
document.SPELLDATA.companyID.value=opener.document.SPELLDATA.companyID.value
document.SPELLDATA.language.value=opener.document.SPELLDATA.language.value
document.SPELLDATA.opener.value=opener.document.SPELLDATA.opener.value
document.SPELLDATA.action=opener.document.SPELLDATA.formaction.value
													

var flen=opener.document.forms.length

var index=flen
for(i=0; i<flen; i++){
	if(opener.document.forms[i].name==document.SPELLDATA.formname.value){
		index=i
		i=flen
		}
	}
	
if(index<flen){
	var ilen=opener.document.forms[index].elements.length
	var indexcontrol=ilen
	if(document.SPELLDATA.subjectname.value!=""){
		for(i=0; i<ilen; i++){
			if(opener.document.forms[index].elements[i].name==document.SPELLDATA.subjectname.value){
				indexcontrol=i
				i=ilen
				}
			}	
		if(indexcontrol<ilen)		
			document.SPELLDATA.subject.value=opener.document.forms[index].elements[indexcontrol].value
		}
				
	if(document.SPELLDATA.messagebodyname.value!=""){
		indexcontrol=ilen
		for(i=0; i<ilen; i++){
			if(opener.document.forms[index].elements[i].name==document.SPELLDATA.messagebodyname.value){
				indexcontrol=i
				i=ilen
				}
			}	
		if(indexcontrol<ilen)		
			document.SPELLDATA.messagebody.value=opener.document.forms[index].elements[indexcontrol].value
		}	
	document.SPELLDATA.submit()
	}else{
		alert("no form found.  Check java function call")
		window.close()
		}
}

//-->
</SCRIPT>
</HEAD>
<BODY LANGUAGE=javascript onload="return window_onload()">
<FORM action="" method=post name=SPELLDATA LANGUAGE=javascript>

	<H1>Loading Spell Checker. Please wait</H1>
   <INPUT name="formname"  type=hidden > 
   <INPUT name="messagebodyname" type=hidden > 
   <INPUT name="subjectname" type=hidden >
   <INPUT name="companyID"  type=hidden >
   <INPUT name="language"  type=hidden >
   <INPUT name="opener"  type=hidden > 
   <INPUT name="closer"  type=hidden value="finish.asp"> 
   <INPUT name="IsHTML"  type=hidden value=0> 
   
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <P>&nbsp;</P>
   <TEXTAREA name=subject></TEXTAREA> 
   <TEXTAREA name=messagebody></TEXTAREA>
</FORM>
</BODY>
</HTML>
