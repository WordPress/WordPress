<?php
/*
	styles tab for eventon settings
	version: 2.2.11.1
*/
?>

<div id="evcal_3" class="postbox evcal_admin_meta">	
	
	<div class="inside">
		<h2><?php _e('Add your own custom styles','eventon');?></h2>
		<p><i><?php _e('Please use text area below to write your own CSS styles to override or fix style/layout changes in your calendar. <br/>These styles will be appended into the dynamic styles sheet loaded on the front-end.','eventon')?></i></p>
		<table width='100%'>
			<tr><td colspan='2'>
				<textarea style='width:100%; height:350px' name='evcal_styles'><?php echo get_option('evcal_styles');?></textarea>				
			</tr>		
		</table>

		<h2 style='padding-top:30px'><?php _e('Auto generated Dynamic Styles','eventon');?></h2>
		<p><i><?php _e('If your dynamic styles (appearance changes in eventon settings) do not reflect on front-end, it could be that your website is blocking eventon from using wp_filesystems() to write these dynamic styles to "eventon_dynamic_styles.css". <br/>In this case please <b>copy</b> the below CSS styles and paste it on your theme styles (style.css).','eventon')?></i></p>
		<table width='100%'>
			<tr><td colspan='2'>
				<textarea readonly style='width:100%; height:350px; opacity:0.5' name='evcal_styles_dynamic'><?php
					ob_start();
					include(AJDE_EVCAL_PATH.'/assets/css/dynamic_styles.php');

					$content = ob_get_clean();
					echo $content;
				?></textarea>				
			</tr>		
		</table>
		<p><i>NOTE: These styles will update everytime you make changes in eventon appearance settings</i></p>		
	</div>
</div>
<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" />
</form>