function checkAll(form) {
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick',2))) {
			if(form.elements[i].checked == true)
				form.elements[i].checked = false;
			else
				form.elements[i].checked = true;
		}
	}
}

function getNumChecked(form) {
	var num = 0;
	for (i = 0, n = form.elements.length; i < n; i++) {
		if (form.elements[i].type == "checkbox") {
			if (form.elements[i].checked == true)
				num++;
		}
	}
	return num;
}

function checkAllUsers(role) {
 var checkboxs = document.getElementsByTagName('input');
 for(var i = 0, inp; inp = checkboxs[i]; i++)
 	if(inp.type.toLowerCase() == 'checkbox' && inp.className == role)
		if(inp.checked == false)
			inp.checked = true;
		else
			inp.checked = false;
}