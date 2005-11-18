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
<script type="text/javascript" src="../wp-includes/js/tw-sack.js"></script>
<script type="text/javascript" src="list-manipulation.js"></script>
<?php if ( isset( $editing ) ) : ?>
<?php if ( 'true' == get_user_option('rich_editing') ) :?>
<script language="javascript" type="text/javascript" src="../wp-includes/js/tinymce/tiny_mce_gzip.php?index=0&theme=advanced&plugins=wordpress,autosave"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "specific_textareas",
	textarea_trigger : "title",
	width : "100%",
	theme : "advanced",
	theme_advanced_buttons1 : "bold,italic,strikethrough,separator,bullist,numlist,outdent,indent,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,image,emotions,separator,wordpress,separator,undo,redo,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	theme_advanced_resizing : true,
	browsers : "msie,gecko",
	dialog_type : "modal",
	theme_advanced_resize_horizontal : false,
	entity_encoding : "raw",
	relative_urls : false,
	remove_script_host : false,
	force_p_newlines : true,
	force_br_newlines : false,
	convert_newlines_to_brs : false,
	remove_linebreaks : true,
	save_callback : "wp_save_callback",
	valid_elements : "-a[id|href|title|rel],-strong/b,-em/i,-strike,-del,-u,p[class|align],-ol,-ul,-li,br,img[class|src|alt|title|width|height|align],-sub,-sup,-blockquote,-table[border=0|cellspacing|cellpadding|width|height|class|align],tr[class|rowspan|width|height|align|valign],td[dir|class|colspan|rowspan|width|height|align|valign],-div[dir|class|align],-span[class|align],-pre[class],address,-h1[class|align],-h2[class|align],-h3[class|align],-h4[class|align],-h5[class|align],-h6[class|align],hr",
	plugins : "wordpress,autosave"
	<?php do_action('mce_options'); ?>
});
</script>
<?php endif; ?>
<script type="text/javascript" src="../wp-includes/js/dbx.js"></script>
<script type="text/javascript" src="../wp-includes/js/dbx-key.js"></script>

<?php if ( current_user_can('manage_categories') ) : ?>
<style type="text/css">
#newcat { width: 120px; margin-right: 5px; }
input#catadd { 	background: #a4a4a4;
	border-bottom: 1px solid #898989;
	border-left: 1px solid #bcbcbc;
	border-right: 1px solid #898989;
	border-top: 1px solid #bcbcbc;
	color: #fff;
	font-size: 10px;
	padding: 0;
	margin: 0;
	font-weight: bold;
	height: 20px;
	margin-bottom: 2px;
	text-align: center;
	width: 37px; }
#howto {
	font-size: 11px;
	margin: 0 5px;
	display: block;
}
#jaxcat {
	margin: 0;
	padding: 0;
}
</style>
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
	newcatSub.id = 'catadd';
	newcatSub.value = '<?php _e('Add'); ?>';
	newcatSub.onclick = ajaxNewCat;

	ajaxcat.appendChild(newcat);
	ajaxcat.appendChild(newcatSub);
	document.getElementById('jaxcat').appendChild(ajaxcat);

	howto = document.createElement('span');
	howto.innerHTML = '<?php _e('Separate multiple categories with commas.'); ?>';
	howto.id = 'howto';
	ajaxcat.appendChild(howto);
}

addLoadEvent(newCatAddIn);

function getResponseElement() {
	var p = document.getElementById('ajaxcatresponse');
	if (!p) {
		p = document.createElement('p');
		document.getElementById('jaxcat').appendChild(p);
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
	p.innerHTML = 'Processing Request...';
}

function newCatCompletion() {
	var p = getResponseElement();
//	alert(ajaxCat.response);
	var id    = 0;
	var ids   = new Array();
	var names = new Array();
	
	ids   = myPload( ajaxCat.response );
	names = myPload( newcat.value );

	for ( i = 0; i < ids.length; i++ ) {
		id = ids[i];
//		alert(id);
		if ( id == '-1' ) {
			p.innerHTML = "You don't have permission to do that.";
			return;
		}
		if ( id == '0' ) {
			p.innerHTML = "That category name is invalid.  Try something else.";
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

	catString = 'ajaxnewcat=' + encodeURIComponent(newcat.value);
	ajaxCat.requestFile = 'edit-form-ajax-cat.php';
	ajaxCat.method = 'GET';
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

if ( $parent_file == 'options-personal.php' ) {
	require(ABSPATH . '/wp-admin/options-head.php');
}
?>
