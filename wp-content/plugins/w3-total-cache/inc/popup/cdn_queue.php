<?php if (!defined('W3TC')) die(); ?>
<?php include W3TC_INC_DIR . '/popup/common/header.php'; ?>

<p><?php _e('This tool lists the pending file uploads and deletions.', 'w3-total-cache'); ?></p>
<p id="w3tc-options-menu">
	<a href="#cdn_queue_upload" rel="#cdn_queue_upload" class="tab<?php if ($cdn_queue_tab == 'upload'): ?> tab-selected<?php endif; ?>"><?php _e('Upload queue', 'w3-total-cache'); ?></a> |
	<a href="#cdn_queue_delete" rel="#cdn_queue_delete" class="tab<?php if ($cdn_queue_tab == 'delete'): ?> tab-selected<?php endif; ?>"><?php _e('Delete queue', 'w3-total-cache'); ?></a> |
		<a href="#cdn_queue_purge" rel="#cdn_queue_purge" class="tab<?php if ($cdn_queue_tab == 'purge'): ?> tab-selected<?php endif; ?>"><?php _e('Purge queue', 'w3-total-cache'); ?></a>
</p>

<div id="cdn_queue_upload" class="tab-content"<?php if ($cdn_queue_tab != 'upload'): ?> style="display: none;"<?php endif; ?>>
<?php if (! empty($queue[W3TC_CDN_COMMAND_UPLOAD])): ?>
	<table class="table queue">
		<tr>
			<th><?php _e('Local Path', 'w3-total-cache'); ?></th>
			<th><?php _e('Remote Path', 'w3-total-cache'); ?></th>
			<th><?php _e('Last Error', 'w3-total-cache'); ?></th>
			<th><?php _e('Date', 'w3-total-cache'); ?></th>
			<th><?php _e('Delete', 'w3-total-cache'); ?></th>
		</tr>
		<?php foreach ((array) $queue[W3TC_CDN_COMMAND_UPLOAD] as $result): ?>
		<tr>
			<td><?php echo htmlspecialchars($result->local_path); ?></td>
			<td><?php echo htmlspecialchars($result->remote_path); ?></td>
			<td><?php echo htmlspecialchars($result->last_error); ?></td>
			<td align="center"><?php echo htmlspecialchars($result->date); ?></td>
			<td align="center">
				<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=upload&amp;cdn_queue_action=delete&amp;cdn_queue_id=<?php echo $result->id; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_delete"><?php _e('Delete', 'w3-total-cache'); ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=upload&amp;cdn_queue_action=empty&amp;cdn_queue_type=<?php echo W3TC_CDN_COMMAND_UPLOAD; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_empty"><?php _e('Empty upload queue', 'w3-total-cache'); ?></a>
	</p>
	<p>
		<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=upload&amp;cdn_queue_action=process&amp;_wpnonce=<?php echo $nonce; ?>"><?php _e('Process CDN queue now', 'w3-total-cache'); ?></a>
	</p>
<?php else: ?>
	<p class="empty"><?php _e('Upload queue is empty', 'w3-total-cache'); ?></p>
<?php endif; ?>
</div>

<div id="cdn_queue_delete" class="tab-content"<?php if ($cdn_queue_tab != 'delete'): ?> style="display: none;"<?php endif; ?>>
<?php if (! empty($queue[W3TC_CDN_COMMAND_DELETE])): ?>
	<table class="table queue">
		<tr>
			<th><?php _e('Local Path', 'w3-total-cache'); ?></th>
			<th><?php _e('Remote Path', 'w3-total-cache'); ?></th>
			<th><?php _e('Last Error', 'w3-total-cache'); ?></th>
			<th width="25%"><?php _e('Date', 'w3-total-cache'); ?></th>
			<th width="10%"><?php _e('Delete', 'w3-total-cache'); ?></th>
		</tr>
		<?php foreach ((array) $queue[W3TC_CDN_COMMAND_DELETE] as $result): ?>
		<tr>
			<td><?php echo htmlspecialchars($result->local_path); ?></td>
			<td><?php echo htmlspecialchars($result->remote_path); ?></td>
			<td><?php echo htmlspecialchars($result->last_error); ?></td>
			<td align="center"><?php echo htmlspecialchars($result->date); ?></td>
			<td align="center">
				<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=delete&amp;cdn_queue_action=delete&amp;cdn_queue_id=<?php echo $result->id; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_delete"><?php _e('Delete', 'w3-total-cache'); ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=delete&amp;cdn_queue_action=empty&amp;cdn_queue_type=<?php echo W3TC_CDN_COMMAND_DELETE; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_empty"><?php _e('Empty delete queue', 'w3-total-cache'); ?></a>
	</p>
<?php else: ?>
	<p class="empty"><?php _e('Delete queue is empty', 'w3-total-cache'); ?></p>
<?php endif; ?>
</div>

<div id="cdn_queue_purge" class="tab-content"<?php if ($cdn_queue_tab != 'purge'): ?> style="display: none;"<?php endif; ?>>
<?php if (! empty($queue[W3TC_CDN_COMMAND_PURGE])): ?>
	<table class="table queue">
		<tr>
			<th><?php _e('Local Path', 'w3-total-cache'); ?></th>
			<th><?php _e('Remote Path', 'w3-total-cache'); ?></th>
			<th><?php _e('Last Error', 'w3-total-cache'); ?></th>
			<th width="25%"><?php _e('Date', 'w3-total-cache'); ?></th>
			<th width="10%"><?php _e('Delete', 'w3-total-cache'); ?></th>
		</tr>
		<?php foreach ((array) $queue[W3TC_CDN_COMMAND_PURGE] as $result): ?>
		<tr>
			<td><?php echo htmlspecialchars($result->local_path); ?></td>
			<td><?php echo htmlspecialchars($result->remote_path); ?></td>
			<td><?php echo htmlspecialchars($result->last_error); ?></td>
			<td align="center"><?php echo htmlspecialchars($result->date); ?></td>
			<td align="center">
				<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=purge&amp;cdn_queue_action=delete&amp;cdn_queue_id=<?php echo $result->id; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_delete"><?php _e('Delete', 'w3-total-cache'); ?></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<a href="admin.php?page=w3tc_cdn&amp;w3tc_cdn_queue&amp;cdn_queue_tab=purge&amp;cdn_queue_action=empty&amp;cdn_queue_type=<?php echo W3TC_CDN_COMMAND_PURGE; ?>&amp;_wpnonce=<?php echo $nonce; ?>" class="cdn_queue_empty"><?php _e('Empty purge queue', 'w3-total-cache'); ?></a>
	</p>
<?php else: ?>
	<p class="empty"><?php _e('Purge queue is empty', 'w3-total-cache'); ?></p>
<?php endif; ?>
</div>

<?php include W3TC_INC_DIR . '/popup/common/footer.php'; ?>