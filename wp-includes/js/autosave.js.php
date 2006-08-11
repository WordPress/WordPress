<?php @require_once('../../wp-config.php');
$expiresOffset = 3600 * 24 * 10;		// 10 days util client cache expires

header("Content-type: text/javascript; charset: UTF-8");
header("Vary: Accept-Encoding"); // Handle proxies
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");


?>
var autosaveLast = '';
function autosave_timer() {
	autosave();
	setTimeout("autosave_timer()", <?php echo apply_filters('autosave_interval', '60000') ?>);
}

function autosave_start_timer() {
	setTimeout("autosave_timer()", <?php echo apply_filters('autosave_start_delay', '60000') ?>);
}
addLoadEvent(autosave_start_timer)

function autosave_cur_time() {
	var now = new Date();
	return "" + ((now.getHours() >12) ? now.getHours() -12 : now.getHours()) + 
	((now.getMinutes() < 10) ? ":0" : ":") + now.getMinutes() +
	((now.getSeconds() < 10) ? ":0" : ":") + now.getSeconds();
}
	
function autosave_update_nonce() {
	var response = nonceAjax.response;
	document.getElementsByName('_wpnonce')[0].value = response;
}

function autosave_update_post_ID() {
	var response = autosaveAjax.response;
	var res = parseInt(response);
	var message;
	
	if(isNaN(res)) {
		message = "<?php _e('Error: '); ?>" + response;
	} else {
		message = "<?php _e('Saved at '); ?>" + autosave_cur_time();
		$('post_ID').name = "post_ID";
		$('post_ID').value = res;
		$('hiddenaction').value = 'editpost';
		// We need new nonces
		nonceAjax = new sack();
		nonceAjax.element = null;
		nonceAjax.setVar("action", "autosave-generate-nonces");
		nonceAjax.setVar("post_ID", res);
		nonceAjax.setVar("cookie", document.cookie);
		nonceAjax.setVar("post_type", $('post_type').value);
		nonceAjax.requestFile = "<?php echo get_bloginfo('siteurl'); ?>/wp-admin/admin-ajax.php";
		nonceAjax.onCompletion = autosave_update_nonce;
		nonceAjax.method = "POST";
		nonceAjax.runAJAX();
		
	}
	$('autosave').innerHTML = message;
}

function autosave_loading() {
	$('autosave').innerHTML = "<?php _e('Saving Draft...'); ?>";
}

function autosave_saved() {
	var response = autosaveAjax.response;
	var res = parseInt(response);
	var message;
	
	if(isNaN(res)) {
		message = "<?php _e('Error: '); ?>" + response;
	} else {
		message = "<?php _e('Saved at '); ?>" + autosave_cur_time() + ".";
	}
	$('autosave').innerHTML = message;
}
	
function autosave() {
	var form = $('post');

	autosaveAjax = new sack();

	/* Gotta do this up here so we can check the length when tinyMCE is in use */
	if ( typeof tinyMCE == "undefined" || tinyMCE.configs.length < 1 ) {
		autosaveAjax.setVar("content", form.content.value);
	} else {
		tinyMCE.wpTriggerSave();
		autosaveAjax.setVar("content", form.content.value);
	}

	if(form.post_title.value.length==0 || form.content.value.length==0 || form.post_title.value+form.content.value == autosaveLast)
		return;

	autosaveLast = form.post_title.value+form.content.value;

	cats = document.getElementsByName("post_category[]");
	goodcats = ([]);
	for(i=0;i<cats.length;i++) {
		if(cats[i].checked)
			goodcats.push(cats[i].value);
	}
	catslist = goodcats.join(",");
	
	autosaveAjax.setVar("action", "autosave");
	autosaveAjax.setVar("cookie", document.cookie);
	autosaveAjax.setVar("catslist", catslist);
	autosaveAjax.setVar("post_ID", $("post_ID").value);
	autosaveAjax.setVar("post_title", form.post_title.value);
	autosaveAjax.setVar("post_type", form.post_type.value);
	if(form.excerpt)
		autosaveAjax.setVar("excerpt", form.excerpt.value);		
		
	if ( typeof tinyMCE == "undefined" || tinyMCE.configs.length < 1 ) {
		autosaveAjax.setVar("content", form.content.value);
	} else {
		tinyMCE.wpTriggerSave();
		autosaveAjax.setVar("content", form.content.value);
	}
		
	autosaveAjax.requestFile = "<?php echo get_bloginfo('siteurl'); ?>/wp-admin/admin-ajax.php";
	autosaveAjax.method = "POST";
	autosaveAjax.element = null;
	autosaveAjax.onLoading = autosave_loading;
	autosaveAjax.onInteractive = autosave_loading;
	if(parseInt($("post_ID").value) < 1)
		autosaveAjax.onCompletion = autosave_update_post_ID;
	else
		autosaveAjax.onCompletion = autosave_saved;
	autosaveAjax.runAJAX();
}