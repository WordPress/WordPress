<?php
class Advman_Template_Editor
{
	function display($ads)
	{
		//Editor page, so we need to output this editor button code
?><script language="JavaScript" type="text/javascript">
<!--
	var advman_select = document.createElement('select');
	advman_select.setAttribute('onchange', 'advman_add_post_ad(this)');
	advman_select.setAttribute('class', 'ed_button');
	advman_select.setAttribute('title', '<?php _e('Insert an ad from Advertising Manager to my content', 'advman'); ?>');
	advman_select.setAttribute('id', 'advman_select');

	var advman_option = document.createElement('option');
	advman_option.value='';
	advman_option.innerHTML='<?php _e('Insert Ad...', 'advman'); ?>';
	advman_option.style.fontWeight='bold';
	advman_select.appendChild(advman_option);
	
	advman_option = document.createElement('option');
	advman_option.value='';
	advman_option.innerHTML='<?php _e('Default Ad', 'advman'); ?>';
	
	advman_select.appendChild(advman_option);
<?php
		if (!empty($ads)) {
			$names = array();
			foreach($ads as $ad) {
				$name = $ad->name;
				if (!in_array($name, $names)) {
					$names[] = $name;
?>	advman_option = document.createElement('option');
	advman_option.value='<?php echo $name; ?>';
	advman_option.innerHTML='#<?php echo $name; ?>';
	advman_select.appendChild(advman_option);
<?php
					}
				}
			}
?>	var advman_tb = document.getElementById('ed_toolbar');
	if (advman_tb) {
		advman_tb.insertBefore(advman_select, document.getElementById('ed_spell'));
		/* Below is a Kludge for IE, which causes it to re-read the state of onChange etc. set above. Tut tut tut */
		if (navigator.appName == 'Microsoft Internet Explorer') {
			advman_tb.innerHTML = advman_tb.innerHTML; 
		}
	}	
	
	function advman_add_post_ad(element)
	{
		if (element.selectedIndex != 0) {
			var advman_code = (element.value == '') ? '[ad]' : '[ad#' + element.value + ']';
			var advman_content = document.getElementById('content');
			if (document.selection && !window.opera) {
				// IE compatibility
				advman_content.value += advman_code;
			} else {
				if (advman_content.selectionStart || advman_content.selectionStart == '0') {
						var startPos = advman_content.selectionStart;
						var endPos = advman_content.selectionEnd;
						advman_content.value = advman_content.value.substring(0, startPos) + advman_code + advman_content.value.substring(endPos, advman_content.value.length);
				} else {
					advman_content.value += advman_code;
				}
				element.selectedIndex = 0;
			}
		}
	}
// -->
</script>
<?php
	}
}
?>