function customFieldsOnComplete() {
	var pidEl = $('post_ID');
	pidEl.name = 'post_ID';
	pidEl.value = getNodeValue(theList.ajaxAdd.responseXML, 'postid');
	var aEl = $('hiddenaction')
	if ( aEl.value == 'post' ) aEl.value = 'postajaxpost';
}
addLoadEvent(customFieldsAddIn);
function customFieldsAddIn() {
	theList.showLink=0;
	theList.addComplete = customFieldsOnComplete;
	if (!theList.theList) return false;
	inputs = theList.theList.getElementsByTagName('input');
	for ( var i=0; i < inputs.length; i++ ) {
		if ('text' == inputs[i].type) {
			inputs[i].setAttribute('autocomplete', 'off');
		        inputs[i].onkeypress = function(e) {return killSubmit('theList.ajaxUpdater("meta", "meta-' + parseInt(this.name.slice(5),10) + '");', e); };
		}
		if ('updatemeta' == inputs[i].className) {
		        inputs[i].onclick = function(e) {return killSubmit('theList.ajaxUpdater("meta", "meta-' + parseInt(this.parentNode.parentNode.id.slice(5),10) + '");', e); };
		}
	}

	$('metakeyinput').onkeypress = function(e) {return killSubmit('theList.inputData+="&id="+$("post_ID").value;theList.ajaxAdder("meta", "newmeta");', e); };
	$('updatemetasub').onclick = function(e) {return killSubmit('theList.inputData+="&id="+$("post_ID").value;theList.ajaxAdder("meta", "newmeta");', e); };
}
