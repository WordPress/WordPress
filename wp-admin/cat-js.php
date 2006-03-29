<?php
require_once('../wp-config.php');
header('Content-type: text/javascript; charset=' . get_settings('blog_charset'), true);
?>
addLoadEvent(function(){catList=new listMan('categorychecklist');catList.ajaxRespEl='jaxcat';catList.clearInputs.push('newcat');});
addLoadEvent(newCatAddIn);
function newCatAddIn() {
	if ( !document.getElementById('jaxcat') ) return false;
	var ajaxcat = document.createElement('span');
	ajaxcat.id = 'ajaxcat';

	newcat = document.createElement('input');
	newcat.type = 'text';
	newcat.name = 'newcat';
	newcat.id = 'newcat';
	newcat.size = '16';
	newcat.setAttribute('autocomplete', 'off');
	newcat.onkeypress = function(e) { return killSubmit("catList.ajaxAdder('category','categorydiv');", e); };

	var newcatSub = document.createElement('input');
	newcatSub.type = 'button';
	newcatSub.name = 'Button';
	newcatSub.id = 'catadd';
	newcatSub.value = 'Add';
	newcatSub.onclick = function() { catList.ajaxAdder('category', 'categorydiv'); };

	ajaxcat.appendChild(newcat);
	ajaxcat.appendChild(newcatSub);
	document.getElementById('jaxcat').appendChild(ajaxcat);

	howto = document.createElement('span');
	howto.innerHTML = "<?php _e('Separate multiple categories with commas.'); ?>";
	howto.id = 'howto';
	ajaxcat.appendChild(howto);
}
