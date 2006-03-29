addLoadEvent(newCategoryAddIn);
function newCategoryAddIn() {
	if (!theList.theList) return false;
	document.forms.addcat.submit.onclick = function(e) {return killSubmit('theList.ajaxAdder("cat", "addcat");', e); };
	theList.clearInputs.push('cat_name','category_parent','category_description');
}
