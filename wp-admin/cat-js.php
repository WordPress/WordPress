<?php
require_once('../wp-config.php');
cache_javascript_headers();
?>
addLoadEvent(function(){catList=new listMan('categorychecklist');catList.ajaxRespEl='jaxcat';catList.topAdder=1;catList.alt=0;catList.showLink=0;});
addLoadEvent(newCatAddIn);
function newCatAddIn() {
	var jaxcat = $('jaxcat');
	if ( !jaxcat )
		return false;
	Element.update(jaxcat,'<span id="ajaxcat"><input type="text" name="newcat" id="newcat" size="16" autocomplete="off"/><input type="button" name="Button" id="catadd" value="<?php echo js_escape(__('Add')); ?>"/><span id="howto"><?php echo js_escape(__('Separate multiple categories with commas.')); ?></span></span>');
	$('newcat').onkeypress = function(e) { return killSubmit("catList.ajaxAdder('category','jaxcat');", e); };
	$('catadd').onclick = function() { catList.ajaxAdder('category', 'jaxcat'); };
}
