<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<script type="text/javascript">/*<![CDATA[*/
jQuery(function() {
	W3tc_Popup_Cdn_Rename_Domain.nonce = '<?php echo wp_create_nonce('w3tc'); ?>';
	W3tc_Popup_Cdn_Rename_Domain.init('');
});
/*]]>*/</script>

<p><?php _e('This tool allows you to modify the URL of Media Library attachments. Use it if the "WordPress address (<acronym title="Uniform Resource Indicator">URL</acronym>)" value has been changed in the past.', 'w3-total-cache'); ?></p>
<table cellspacing="5">
	<tr>
		<td><?php _e('Total posts:', 'w3-total-cache'); ?></td>
		<td id="cdn_rename_domain_total"><?php echo $total; ?></td>
	</tr>
	<tr>
		<td><?php _e('Processed:', 'w3-total-cache'); ?></td>
		<td id="cdn_rename_domain_processed">0</td>
	</tr>
	<tr>
		<td><?php _e('Status:', 'w3-total-cache'); ?></td>
		<td id="cdn_rename_domain_status">-</td>
	</tr>
	<tr>
		<td><?php _e('Time elapsed:', 'w3-total-cache'); ?></td>
		<td id="cdn_rename_domain_elapsed">-</td>
	</tr>
	<tr>
		<td><?php _e('Last response:', 'w3-total-cache'); ?></td>
		<td id="cdn_rename_domain_last_response">-</td>
	</tr>
	<tr>
		<td><?php _e('Domains to rename:', 'w3-total-cache'); ?></td>
		<td>
			<textarea cols="40" rows="3" id="cdn_rename_domain_names"></textarea><br />
			<?php _e('e.g.: domain.com', 'w3-total-cache'); ?>
		</td>
	</tr>
</table>

<p>
	<input id="cdn_rename_domain_start" class="button-primary" type="button" value="<?php _e('Start', 'w3-total-cache'); ?>"<?php if (! $total): ?> disabled="disabled"<?php endif; ?> />
</p>

<div id="cdn_rename_domain_progress" class="media-item">
    <div class="progress"><div class="bar"><div class="filename original"><span class="percent">0%</span></div></div></div>
	<div class="clear"></div>
</div>

<div id="cdn_rename_domain_log" class="log"></div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>
