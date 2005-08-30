<?php 
@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if (!isset($_GET["page"])) require_once('admin.php'); ?>
<?php get_admin_page_title(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; WordPress</title>
<link rel="stylesheet" href="<?php echo get_settings('siteurl') ?>/wp-admin/wp-admin.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_settings('blog_charset'); ?>" />

<script type="text/javascript">
//<![CDATA[

function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}

<?php if ( isset($xfn) ) : ?>

function GetElementsWithClassName(elementName, className) {
	var allElements = document.getElementsByTagName(elementName);
	var elemColl = new Array();
	for (i = 0; i < allElements.length; i++) {
		if (allElements[i].className == className) {
			elemColl[elemColl.length] = allElements[i];
		}
	}
	return elemColl;
}

function meChecked() {
  var undefined;
  var eMe = document.getElementById('me');
  if (eMe == undefined) return false;
  else return eMe.checked;
}

function upit() {
	var isMe = meChecked(); //document.getElementById('me').checked;
	var inputColl = GetElementsWithClassName('input', 'valinp');
	var results = document.getElementById('rel');
	var linkText, linkUrl, inputs = '';
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

	var aInputs = document.getElementsByTagName('input');

	for (var i = 0; i < aInputs.length; i++) {		
		 aInputs[i].onclick = aInputs[i].onkeyup = upit;
	}
}

addLoadEvent(blurry);
<?php endif; ?>
//]]>
</script>
<script type="text/javascript" src="../wp-includes/js/fat.js"></script>
<?php if ( isset( $editing ) ) : ?>
<?php if ( 'true' == get_user_option('rich_editing') ) :?>
<script type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce_src.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "specific_textareas",
	textarea_trigger : "title",
	width : "100%",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,strikethrough,separator,bullist,numlist,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,image,emotions,separator,undo,redo,wordpress,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	entity_encoding : "raw",
	extended_valid_elements : "a[id|href|title|onclick],img[class|src|alt|title|width|height|align]",
	plugins : "emotions,wordpress"
	<?php do_action('mce_options'); ?>
});
</script>
<?php endif; ?>
<script type="text/javascript" src="../wp-includes/js/dbx.js"></script>
<script type="text/javascript" src="../wp-includes/js/dbx-key.js"></script>

<?php if ( current_user_can('manage_categories') ) : ?>
<script type="text/javascript" src="../wp-includes/js/tw-sack.js"></script>
<script type="text/javascript">
var ajaxCat = new sack();
var newcat;
 
function newCatAddIn() {
	var ajaxcat = document.createElement('p');
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
	newcatSub.value = '+';
	newcat.onkeypress = ajaxNewCatKeyPress;

	ajaxcat.appendChild(newcat);
	ajaxcat.appendChild(newcatSub);
	document.getElementById('categorychecklist').parentNode.appendChild(ajaxcat);
}

addLoadEvent(newCatAddIn);

function getResponseElement() {
	var p = document.getElementById('ajaxcatresponse');
	if (!p) {
		p = document.createElement('p');
		document.getElementById('categorydiv').appendChild(p);
		p.id = 'ajaxcatresponse';
	}
	return p;
}

function newCatLoading() {
	var p = getResponseElement();
	p.innerHTML = 'Sending Data...';
}

function newCatLoaded() {
	var p = getResponseElement();
	p.innerHTML = 'Data Sent...';
}

function newCatInteractive() {
	var p = getResponseElement();
	p.innerHTML = 'Processing Data...';
}

function newCatCompletion() {
	var p = getResponseElement();
	var id = parseInt(ajaxCat.response, 10);
	if ( id == '-1' ) {
		p.innerHTML = "You don't have permission to do that.";
		return;
	}
	if ( id == '0' ) {
		p.innerHTML = "That category name is invalid.  Try something else.";
		return;
	}
	p.parentNode.removeChild(p);
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

		var newLabelText = document.createTextNode(' ' + newcat.value);
		newLabel.appendChild(newLabelText);

		catDiv.insertBefore(newLabel, catDiv.firstChild);
		newCheck.checked = 'checked';

		Fat.fade_all();
		newLabel.className = 'selectit';
	}
	newcat.value = '';
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
	var catString = 'ajaxnewcat=' + encodeURIComponent(newcat.value);
	ajaxCat.requestFile = 'edit-form-ajax-cat.php';
	ajaxCat.method = 'GET';
	ajaxCat.onLoading = newCatLoading;
	ajaxCat.onLoaded = newCatLoaded;
	ajaxCat.onInteractive = newCatInteractive;
	ajaxCat.onCompletion = newCatCompletion;
	ajaxCat.runAJAX(catString);
}
</script>
<?php endif; ?>

<?php endif; ?>

<?php do_action('admin_head'); ?>
</head>
<body>

<div id="wphead">
<h1><?php echo wptexturize(get_settings(('blogname'))); ?> <span>(<a href="<?php echo get_settings('home') . '/'; ?>"><?php _e('View site') ?> &raquo;</a>)</span></h1>
</div>

<div id="user_info"><p><?php printf(__('Howdy, <strong>%s</strong>.'), $user_identity) ?> [<a href="<?php echo get_settings('siteurl')
	 ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>"><?php _e('Sign Out'); ?></a>, <a href="profile.php"><?php _e('My Account'); ?></a>] </p></div>

<?php
require(ABSPATH . '/wp-admin/menu-header.php');

if ( $parent_file == 'options-general.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>
