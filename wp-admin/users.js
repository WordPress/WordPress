addLoadEvent(function() {
	theListEls = document.getElementsByTagName('tbody');
	theUserLists = new Array();
	for ( var l = 0; l < theListEls.length; l++ ) {
		if ( theListEls[l].id )
			theUserLists[theListEls[l].id] = new listMan(theListEls[l].id);
	}
	addUserInputs = document.getElementById('adduser').getElementsByTagName('input');
	for ( var i = 0; i < addUserInputs.length; i++ ) {
		addUserInputs[i].onkeypress = function(e) { return killSubmit('addUserSubmit();', e); }
	}
	document.getElementById('addusersub').onclick = function(e) { return killSubmit('addUserSubmit();', e); }
}
);

function addUserSubmit() {
	var roleEl = document.getElementById('role');
	var role = roleEl.options[roleEl.selectedIndex].value;
	if ( !theUserLists['role-' + role] ) return true;
	return theUserLists['role-' + role].ajaxAdder('user', 'adduser');
}
