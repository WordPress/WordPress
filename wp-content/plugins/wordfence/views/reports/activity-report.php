<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * @var wfActivityReportView $this
 */
?>
<a class="wf-logo" href="//www.wordfence.com/zz8/"><img src="<?php echo wfUtils::getBaseURL(); ?>images/logo.png" alt=""/></a>

<h2>Top <?php echo (int) $limit; ?> IPs Blocked</h2>

<?php wfHelperString::cycle(); ?>

<table class="wf-striped-table wf-fixed-table">
	<thead>
		<tr>
			<th width="50%">IP</th>
			<th width="25%">Country</th>
			<th width="25%">Block Count</th> 
		</tr>
	</thead>
	<tbody>
		<?php if ($top_ips_blocked): ?>
			<?php foreach ($top_ips_blocked as $row): ?>
				<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
					<td class="wf-split-word"><code><?php echo wfUtils::inet_ntop($row->IP) ?></code></td>
					<td>
						<?php if ($row->countryCode): ?>
							<img src="<?php echo wfUtils::getBaseURL() . 'images/flags/' . esc_attr(strtolower($row->countryCode)) ?>.png" class="wfFlag" height="11" width="16" alt="<?php echo esc_attr($row->countryName) ?>" title="<?php echo esc_attr($row->countryName) ?>">
							&nbsp;
							<?php echo esc_html($row->countryCode) ?>
						<?php else: ?>
							(Unknown)
						<?php endif ?>
					</td>
					<td><?php echo (int) $row->blockCount ?></td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="3">
					No IPs blocked yet.
				</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>

<p>
	<a class="button button-primary" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF#top#blocking') ?>">Update Blocked IPs</a>
</p>

<?php wfHelperString::cycle(); ?>

<h2>Top <?php echo (int) $limit; ?> Countries Blocked</h2>

<table class="wf-striped-table wf-fixed-table">
	<thead>
		<tr>
			<th>Country</th>
			<th>Total IPs Blocked</th>
			<th>Block Count</th>
		</tr>
	</thead>
	<tbody>
		<?php if ($top_countries_blocked): ?>
			<?php foreach ($top_countries_blocked as $row): ?>
				<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
					<td>
						<?php if ($row->countryCode): ?>
							<img src="<?php echo wfUtils::getBaseURL() . 'images/flags/' . strtolower($row->countryCode) ?>.png" class="wfFlag" height="11" width="16" alt="<?php echo esc_attr($row->countryName) ?>" title="<?php echo esc_attr($row->countryName) ?>">
							&nbsp;
							<?php echo esc_html($row->countryCode) ?>
						<?php else: ?>
							(Unknown)
						<?php endif ?>
					</td>
					<td><?php echo esc_html($row->totalIPs) ?></td>
					<td><?php echo (int) $row->totalBlockCount ?></td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="3">
					No requests blocked yet.
				</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>

<p>
	<a class="button button-primary" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF#top#blocking') ?>">Update Blocked Countries</a>
</p>

<?php wfHelperString::cycle(); ?>

<h2>Top <?php echo (int) $limit; ?> Failed Logins</h2>

<table class="wf-striped-table wf-fixed-table">
	<thead>
		<tr>
			<th>Username</th>
			<th>Login Attempts</th>
			<th>Existing User</th>
		</tr>
	</thead>
	<tbody>
		<?php if ($top_failed_logins): ?>
			<?php foreach ($top_failed_logins as $row): ?>
				<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
					<td><?php echo esc_html($row->username) ?></td>
					<td><?php echo esc_html($row->fail_count) ?></td>
					<td class="<?php echo sanitize_html_class($row->is_valid_user ? 'loginFailValidUsername' : 'loginFailInvalidUsername') ?>"><?php echo $row->is_valid_user ? 'Yes' : 'No' ?></td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<tr>
				<td colspan="3">
					No failed logins yet.
				</td>
			</tr>
		<?php endif ?>
	</tbody>
</table>

<p>
	<a class="button button-primary" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#waf-options-bruteforce') ?>">Update Login Security Options</a>
</p>

<?php wfHelperString::cycle(); ?>

<?php /*?>
<h2>Recently Modified Files</h2>

<table class="activity-table recently-modified-files">
	<thead>
		<tr>
			<th>Modified</th>
			<th>File</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($recently_modified_files as $file_row):
			list($file, $mod_time) = $file_row;
			?>
			<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
				<td style="white-space: nowrap;"><?php echo $this->modTime($mod_time) ?></td>
				<td class="display-file-table-cell">
					<pre class="display-file"><?php echo esc_html($this->displayFile($file)) ?></pre>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php */ ?>


<?php wfHelperString::cycle(); ?>

<h2>Updates Needed</h2>

<?php if ($updates_needed['core']): ?>
	<h4>Core</h4>
	<ul>
		<li>A new version of WordPress (v<?php echo esc_html($updates_needed['core']) ?>) is available.</li>
	</ul>
<?php endif ?>
<?php if ($updates_needed['plugins']): ?>
	<h4>Plugins</h4>
	<ul>
		<?php
		foreach ($updates_needed['plugins'] as $plugin):
			$newVersion = ($plugin['newVersion'] == 'Unknown' ? $plugin['newVersion'] : "v{$plugin['newVersion']}");
		?>
			<li>
				A new version of the plugin "<?php echo esc_html("{$plugin['Name']} ({$newVersion})") ?>" is available.
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>
<?php if ($updates_needed['themes']): ?>
	<h4>Themes</h4>
	<ul>
		<?php
		foreach ($updates_needed['themes'] as $theme):
			$newVersion = ($theme['newVersion'] == 'Unknown' ? $theme['newVersion'] : "v{$theme['newVersion']}");
		?>
			<li>
				A new version of the theme "<?php echo esc_html("{$theme['name']} ({$newVersion})") ?>" is available.
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>

<?php if ($updates_needed['core'] || $updates_needed['plugins'] || $updates_needed['themes']): ?>
	<p><a class="button button-primary" href="<?php echo esc_attr(network_admin_url('update-core.php')) ?>">Update Now</a></p>
<?php else: ?>
	<p>No updates are available at this time.</p>
<?php endif ?>
<?php if ((defined('WP_DEBUG') && WP_DEBUG) || wfConfig::get('debugOn')): ?>
	<p>Generated in <?php printf('%.4f seconds', $microseconds) ?></p>
<?php endif ?>
