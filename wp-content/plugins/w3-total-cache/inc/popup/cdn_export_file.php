<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
var files = [
<?php $files_count = count($files); foreach ($files as $index => $file): ?>
	'<?php echo addslashes($file); ?>'<?php if ($index < $files_count - 1): ?>,<?php endif; ?>
<?php endforeach; ?>
];

jQuery(function() {
    W3tc_Popup_Cdn_Export_File.nonce = '<?php echo wp_create_nonce('w3tc'); ?>';
    W3tc_Popup_Cdn_Export_File.files = files;
	W3tc_Popup_Cdn_Export_File.init();
});
/*]]>*/</script>

<p><?php _e('This tool will upload files of the selected type to content delivery network provider.', 'w3-total-cache'); ?></p>
<table cellspacing="5">
	<tr>
		<td><?php _e('Total files:', 'w3-total-cache'); ?></td>
		<td><?php echo $files_count; ?></td>
	</tr>
	<tr>
		<td><?php _e('Processed:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_file_processed">0</td>
	</tr>
	<tr>
		<td><?php _e('Status:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_file_status">-</td>
	</tr>
	<tr>
		<td><?php _e('Time elapsed:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_file_elapsed">-</td>
	</tr>
	<tr>
		<td><?php _e('Last response:', 'w3-total-cache'); ?></td>
		<td id="cdn_export_file_last_response">-</td>
	</tr>
</table>

<p>
	<input id="cdn_export_file_start" class="button-primary" type="button" value="<?php _e('Start', 'w3-total-cache'); ?>"<?php if (! $files_count): ?> disabled="disabled"<?php endif; ?> />
</p>

<div id="cdn_export_file_progress" class="media-item">
    <div class="progress"><div class="bar"><div class="filename original"><span class="percent">0%</span></div></div></div>
	<div class="clear"></div>
</div>

<div id="cdn_export_file_log" class="log"></div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>