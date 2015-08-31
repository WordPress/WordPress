<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
jQuery(function() {
    W3tc_Popup_Cdn_Export_Library.nonce = '<?php echo wp_create_nonce('w3tc'); ?>';
	W3tc_Popup_Cdn_Export_Library.init();
});
/*]]>*/</script>

<p><?php _e('This tool will upload files of the selected type to content delivery network provider.', 'w3-total-cache'); ?></p>
<table cellspacing="5">
	<tr>
		<td><?php _e('Total media library attachments:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_library_total"><?php echo $total; ?></td>
	</tr>
	<tr>
		<td><?php _e('Processed:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_library_processed">0</td>
	</tr>
	<tr>
		<td><?php _e('Status:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_library_status">-</td>
	</tr>
	<tr>
		<td><?php _e('Time elapsed:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_library_elapsed">-</td>
	</tr>
	<tr>
		<td><?php _e('Last response:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_library_last_response">-</td>
	</tr>
</table>

<p>
	<input id="cdn_export_library_start" class="button-primary" type="button" value="<?php _e('Start', 'w3-total-cache'); ?>"<?php if (! $total): ?> disabled="disabled"<?php endif; ?> />
</p>

<div id="cdn_export_library_progress" class="media-item">
    <div class="progress"><div class="bar"><div class="filename original"><span class="percent">0%</span></div></div></div>
	<div class="clear"></div>
</div>

<div id="cdn_export_library_log" class="log"></div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>