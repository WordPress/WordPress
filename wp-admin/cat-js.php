<?php
require_once('admin.php');
header('Content-type: text/javascript; charset=' . get_settings('blog_charset'), true);
?>
var ajaxCat = new sack();
var newcat;
 
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
	newcat.onkeypress = ajaxNewCatKeyPress;

	var newcatSub = document.createElement('input');
	newcatSub.type = 'button';
	newcatSub.name = 'Button';
	newcatSub.id = 'catadd';
	newcatSub.value = '<?php echo addslashes(__('Add')); ?>';
	newcatSub.onclick = ajaxNewCat;

	ajaxcat.appendChild(newcat);
	ajaxcat.appendChild(newcatSub);
	document.getElementById('jaxcat').appendChild(ajaxcat);

	howto = document.createElement('span');
	howto.innerHTML = '<?php echo addslashes(__('Separate multiple categories with commas.')); ?>';
	howto.id = 'howto';
	ajaxcat.appendChild(howto);
}

addLoadEvent(newCatAddIn);

function getResponseElement() {
	var p = document.getElementById('ajaxcatresponse');
	if (!p) {
		p = document.createElement('span');
		document.getElementById('jaxcat').appendChild(p);
		p.id = 'ajaxcatresponse';
	}
	return p;
}

function newCatLoading() {
	var p = getResponseElement();
	p.innerHTML = '<?php echo addslashes(__('Sending Data...')); ?>';
}

function newCatLoaded() {
	var p = getResponseElement();
	p.innerHTML = '<?php echo addslashes(__('Data Sent...')); ?>';
}

function newCatInteractive() {
	var p = getResponseElement();
	p.innerHTML = '<?php echo addslashes(__('Processing Request...')); ?>';
}

function newCatCompletion() {
	var p = getResponseElement();
	var id    = 0;
	var ids   = new Array();
	var names = new Array();
	
	ids   = myPload( ajaxCat.response );
	names = myPload( newcat.value );
	for ( i = 0; i < ids.length; i++ ) {
		id = ids[i].replace(/[\n\r]+/g, "");
		if ( id == '-1' ) {
			p.innerHTML = "<?php echo addslashes(__("You don't have permission to do that.")); ?>";
			return;
		}
		if ( id == '0' ) {
			p.innerHTML = "<?php echo addslashes(__('That category name is invalid.  Try something else.')); ?>";
			return;
		}
		
		var exists = document.getElementById('category-' + id);
		
		if (exists) {
			var moveIt = exists.parentNode;
			var container = moveIt.parentNode;
			container.removeChild(moveIt);
			container.insertBefore(moveIt, container.firstChild);
			moveIt.id = 'new-category-' + id;
			exists.checked = 'checked';
			var nowClass = moveIt.className;
			moveIt.className = nowClass + ' fade';
			Fat.fade_all();
			moveIt.className = nowClass;
		} else {
			var catDiv = document.getElementById('categorychecklist');
			var newLabel = document.createElement('label');
			newLabel.setAttribute('for', 'category-' + id);
			newLabel.id = 'new-category-' + id;
			newLabel.className = 'selectit fade';
	
			var newCheck = document.createElement('input');
			newCheck.type = 'checkbox';
			newCheck.value = id;
			newCheck.name = 'post_category[]';
			newCheck.id = 'category-' + id;
			newLabel.appendChild(newCheck);
	
			var newLabelText = document.createTextNode(' ' + names[i]);
			newLabel.appendChild(newLabelText);
	
			catDiv.insertBefore(newLabel, catDiv.firstChild);
			newCheck.checked = 'checked';
	
			Fat.fade_all();
			newLabel.className = 'selectit';
		}
		newcat.value = '';
	}
	p.parentNode.removeChild(p);
//	var id = parseInt(ajaxCat.response, 10);
}

function ajaxNewCatKeyPress(e) {
	if (!e) {
		if (window.event) {
			e = window.event;
		} else {
			return;
		}
	}
	if (e.keyCode == 13) {
		ajaxNewCat();
		e.returnValue = false;
		e.cancelBubble = true;
		return false;
	}
}

function ajaxNewCat() {
	var newcat = document.getElementById('newcat');
	var split_cats = new Array(1);
	var catString = '';

	catString = ajaxCat.encVar('ajaxnewcat', newcat.value) + '&' + ajaxCat.encVar('cookie', document.cookie);
	ajaxCat.requestFile = 'edit-form-ajax-cat.php';
	ajaxCat.method = 'POST';
	ajaxCat.onLoading = newCatLoading;
	ajaxCat.onLoaded = newCatLoaded;
	ajaxCat.onInteractive = newCatInteractive;
	ajaxCat.onCompletion = newCatCompletion;
	ajaxCat.runAJAX(catString);
}

function myPload( str ) {
	var fixedExplode = new Array();
	var comma = new String(',');
	var count = 0;
	var currentElement = '';

	for( x=0; x < str.length; x++) {
		andy = str.charAt(x);
		if ( comma.indexOf(andy) != -1 ) {
			currentElement = currentElement.replace(new RegExp('^\\s*(.*?)\\s*$', ''), '$1'); // trim
			fixedExplode[count] = currentElement;
			currentElement = "";
			count++;
		} else {
			currentElement += andy;
		}
	}

	if ( currentElement != "" )
		fixedExplode[count] = currentElement;
	return fixedExplode;
}
