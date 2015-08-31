<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
jQuery(function() {
    W3tc_Popup_Cdn_Import_Library.nonce = '<?php echo wp_create_nonce('w3tc'); ?>';
    W3tc_Popup_Cdn_Import_Library.cdn_host = '<?php echo $cdn_host; ?>';
	W3tc_Popup_Cdn_Import_Library.init();
});
/*]]>*/</script>

<p><?php _e('This tool will copy post or page attachments into the Media Library allowing WordPress to work as intended.', 'w3-total-cache'); ?></p>
<table cellspacing="5">
	<tr>
		<td><?php _e('Total posts:', 'w3-total-cache'); ?></td>
		<td id="cdn_import_library_total"><?php echo $total; ?></td>
	</tr>
	<tr>
		<td><?php _e('Processed:', 'w3-total-cache'); ?></td>
		<td id="cdn_import_library_processed">0</td>
	</tr>
	<tr>
		<td><?php _e('Status:', 'w3-total-cache'); ?></td>
		<td id="cdn_import_library_status">-</td>
	</tr>
	<tr>
		<td><?php _e('Time elapsed:', 'w3-total-cache'); ?></td>
		<td id="cdn_import_library_elapsed">-</td>
	</tr>
	<tr>
		<td><?php _e('Last response:', 'w3-total-cache'); ?></td>
		<td id="cdn_import_library_last_response">-</td>
	</tr>
	<tr>
		<td colspan="2">
			<label><input id="cdn_import_library_redirect_permanent" type="checkbox" checked="checked" /> <?php _e('Create a list of permanent (301) redirects for use in your site\'s .htaccess file', 'w3-total-cache'); ?></label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label><input id="cdn_import_library_redirect_cdn" type="checkbox" /> <?php _e('Create a list of redirects to <acronym title="Content Delivery Network">CDN</acronym> (hostname specified in hostname field #1.)', 'w3-total-cache'); ?></label>
		</td>
	</tr>
</table>

<p>
	<input id="cdn_import_library_start" class="button-primary" type="button" value="<?php _e('Start', 'w3-total-cache'); ?>"<?php if (! $total): ?> disabled="disabled"<?php endif; ?> />
</p>

<div id="cdn_import_library_progress" class="media-item">
    <div class="progress"><div class="bar"><div class="filename original"><span class="percent">0%</span></div></div></div>
	<div class="clear"></div>
</div>

<div id="cdn_import_library_log" class="log"></div>

<p>
	<?php _e('Add the following directives to your .htaccess file or if there are several hundred they should be added directly to your configuration file:', 'w3-total-cache'); ?>
</p>

<p>
	<textarea rows="10" cols="90" id="cdn_import_library_rules" class="rules"></textarea>
</p>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>