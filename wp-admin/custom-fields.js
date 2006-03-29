addLoadEvent(customFieldsAddIn);
function customFieldsAddIn() {
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

	document.getElementById('metakeyinput').onkeypress = function(e) {return killSubmit('theList.inputData+="&id="+document.getElementById("post_ID").value;theList.ajaxAdder("meta", "newmeta", customFieldsOnComplete);', e); };
	document.getElementById('updatemetasub').onclick = function(e) {return killSubmit('theList.inputData+="&id="+document.getElementById("post_ID").value;theList.ajaxAdder("meta", "newmeta", customFieldsOnComplete);', e); };
	theList.clearInputs.push('metakeyselect','metakeyinput','metavalue');
}
function customFieldsOnComplete() {
	var pidEl = document.getElementById('post_ID');
	pidEl.name = 'post_ID';
	pidEl.value = getNodeValue(theList.ajaxAdd.responseXML, 'postid');
	var aEl = document.getElementById('hiddenaction')
	if ( aEl.value == 'post' ) aEl.value = 'postajaxpost';
}
