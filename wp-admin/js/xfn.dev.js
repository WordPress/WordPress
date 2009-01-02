function GetElementsWithClassName(elementName, className) {
	var allElements = document.getElementsByTagName(elementName), elemColl = new Array(), i;
	for (i = 0; i < allElements.length; i++) {
		if (allElements[i].className == className) {
			elemColl[elemColl.length] = allElements[i];
		}
	}
	return elemColl;
}

function meChecked() {
	var undefined, eMe = document.getElementById('me');
	if (eMe == undefined) return false;
	else return eMe.checked;
}

function upit() {
	var isMe = meChecked(), inputColl = GetElementsWithClassName('input', 'valinp'), results = document.getElementById('link_rel'), inputs = '', i;
	for (i = 0; i < inputColl.length; i++) {
		 inputColl[i].disabled = isMe;
		 inputColl[i].parentNode.className = isMe ? 'disabled' : '';
		 if (!isMe && inputColl[i].checked && inputColl[i].value != '') {
			inputs += inputColl[i].value + ' ';
				}
		 }
	inputs = inputs.substr(0,inputs.length - 1);
	if (isMe) inputs='me';
	results.value = inputs;
	}

function blurry() {
	if (!document.getElementById) return;

	var aInputs = document.getElementsByTagName('input'), i;

	for ( i = 0; i < aInputs.length; i++) {
		 aInputs[i].onclick = aInputs[i].onkeyup = upit;
	}
}

addLoadEvent(blurry);