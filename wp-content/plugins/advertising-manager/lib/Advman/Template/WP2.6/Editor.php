<?php
class Advman_Template_Editor
{
	function display($ads)
	{
		//Editor page, so we need to output this editor button code
?>			<script language="JavaScript" type="text/javascript">
			<!--
				var ed_advman = document.createElement("select");
				ed_advman.setAttribute("onchange", "advman_add_post_ad(this)");
				ed_advman.setAttribute("class", "ed_button");
				ed_advman.setAttribute("title", "<?php _e('Insert an ad from Advertising Manager to my content', 'advman'); ?>");
				ed_advman.setAttribute("id", "ed_advman");					
				adh = document.createElement("option");
				adh.value='';
				adh.innerHTML='<?php _e('Insert Ad...', 'advman'); ?>';
				adh.style.fontWeight='bold';
				ed_advman.appendChild(adh);

				def = document.createElement("option");
				def.value='';
				def.innerHTML='<?php _e('Default Ad', 'advman'); ?>';

				ed_advman.appendChild(def);
<?php
			if (!empty($ads)) {
				$names = array();
				foreach($ads as $ad) {
					$name = $ad->name;
					if (!in_array($name, $names)) {
						$names[] = $name;
?>				var opt = document.createElement("option");
				opt.value='<?php echo $name; ?>';
				opt.innerHTML='#<?php echo $name; ?>';
				ed_advman.appendChild(opt);
<?php
					}
				}
			}
?>				document.getElementById("ed_toolbar").insertBefore(ed_advman, document.getElementById("ed_spell"));
				/* Below is a Kludge for IE, which causes it to re-read the state of onChange etc. set above. Tut tut tut */
				if (navigator.appName == 'Microsoft Internet Explorer') {
					document.getElementById("ed_toolbar").innerHTML=document.getElementById("ed_toolbar").innerHTML; 
				}
				
			    function advman_add_post_ad(element)
			    {
					if(element.selectedIndex!=0){
	
					if(element.value=='')
						{advman_code = '[ad]';}
					else
						{advman_code = '[ad#' + element.value + ']';}

					contentField = document.getElementById("content");
					if (document.selection && !window.opera) {
						// IE compatibility
						contentField.value += advman_code;
					} else
					if (contentField.selectionStart || contentField.selectionStart == '0') {

						var startPos = contentField.selectionStart;
						var endPos = contentField.selectionEnd;
						contentField.value = contentField.value.substring(0, startPos) + advman_code + contentField.value.substring(endPos, contentField.value.length);

					} else {

						contentField.value += advman_code;
					}
						element.selectedIndex=0;

					}
				}
			// -->
			</script>
<?php
	}
}
?>