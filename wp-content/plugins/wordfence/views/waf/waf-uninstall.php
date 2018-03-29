<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<script type="text/x-jquery-template" id="wafTmpl_uninstall">
	<div class="wf-modal">
		<div class="wf-modal-header">
			<div class="wf-modal-header-content">
				<div class="wf-modal-title">
					<strong><?php _e('Uninstall Wordfence Firewall', 'wordfence'); ?></strong>
				</div>
			</div>
			<div class="wf-modal-header-action">
				<div><?php printf(__('If you cannot complete the uninstall process, <a target="_blank" rel="noopener noreferrer" href="%s">click here for help</a>', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_WAF_REMOVE_MANUALLY)); ?></div>
				<div class="wf-padding-add-left-small wf-modal-header-action-close"><a href="#" onclick="WFAD.colorboxClose(); return false"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
			</div>
		</div>
		<div class="wf-modal-content">
		<?php
		$currentAutoPrependFile = ini_get('auto_prepend_file');
		?>
			<p><?php _e('Extended Protection Mode of the Wordfence Web Application Firewall uses the PHP ini setting called <code>auto_prepend_file</code> in order to ensure it runs before any potentially vulnerable code runs. This PHP setting currently refers to the Wordfence file at:', 'wordfence'); ?></p>
			<pre class='wf-pre'><?php echo esc_html($currentAutoPrependFile); ?></pre>
		<?php
		$contents = file_get_contents($currentAutoPrependFile);
		$refersToWAF = preg_match('/define\s*\(\s*(["\'])WFWAF_LOG_PATH\1\s*,\s*(["\']).+?\2\s*\)\s*/', $contents);
		
		if (!$refersToWAF):
		?>
			<p><?php printf(__('Automatic uninstallation cannot be completed, but you may still be able to <a href="%s" target="_blank" rel="noopener noreferrer">manually uninstall extended protection</a>.', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_WAF_REMOVE_MANUALLY)); ?></p>
		<?php else: ?>
				<p><?php _e('Before this file can be deleted, the configuration for the <code>auto_prepend_file</code> setting needs to be removed.', 'wordfence'); ?></p>
				<?php
				$serverInfo = wfWebServerInfo::createFromEnvironment();
				$dropdown = array(
					array("apache-mod_php", __('Apache + mod_php', 'wordfence'), $serverInfo->isApacheModPHP(), wfWAFAutoPrependHelper::helper('apache-mod_php')->getFilesNeededForBackup()),
					array("apache-suphp", __('Apache + suPHP', 'wordfence'), $serverInfo->isApacheSuPHP(), wfWAFAutoPrependHelper::helper('apache-suphp')->getFilesNeededForBackup()),
					array("cgi", __('Apache + CGI/FastCGI', 'wordfence'), $serverInfo->isApache() && !$serverInfo->isApacheSuPHP() && ($serverInfo->isCGI() || $serverInfo->isFastCGI()), wfWAFAutoPrependHelper::helper('cgi')->getFilesNeededForBackup()),
					array("litespeed", __('LiteSpeed/lsapi', 'wordfence'), $serverInfo->isLiteSpeed(), wfWAFAutoPrependHelper::helper('litespeed')->getFilesNeededForBackup()),
					array("nginx", __('NGINX', 'wordfence'), $serverInfo->isNGINX(), wfWAFAutoPrependHelper::helper('nginx')->getFilesNeededForBackup()),
					array("iis", __('Windows (IIS)', 'wordfence'), $serverInfo->isIIS(), wfWAFAutoPrependHelper::helper('iis')->getFilesNeededForBackup()),
				);
				
				$hasRecommendedOption = false;
				$wafPrependOptions = '';
				foreach ($dropdown as $option) {
					list($optionValue, $optionText, $selected) = $option;
					$wafPrependOptions .= "<option value=\"{$optionValue}\"" . ($selected ? ' selected' : '') . ">{$optionText}" . ($selected ? ' (recommended based on our tests)' : '') . "</option>\n";
					if ($selected) {
						$hasRecommendedOption = true;
					}
				}
				
				if (!$hasRecommendedOption): ?>
					<p><?php _e('If you know your web server\'s configuration, please select it from the list below.', 'wordfence'); ?></p>
				<?php else: ?>
					<p><?php _e('We\'ve preselected your server configuration based on our tests, but if you know your web server\'s configuration, please select it now.', 'wordfence'); ?></p>
				<?php endif; ?>
				<select name='serverConfiguration' id='wf-waf-server-config'>
					<?php echo $wafPrependOptions; ?>
				</select>
				<p><?php _e('Please download a backup of the following files before we make the necessary changes:', 'wordfence'); ?></p>
				<?php
				$adminURL = network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options&action=removeAutoPrepend');
				$wfnonce = wp_create_nonce('wfWAFRemoveAutoPrepend');
				foreach ($dropdown as $option):
					list($optionValue, $optionText, $selected) = $option;
					$class = preg_replace('/[^a-z0-9\-]/i', '', $optionValue);
					$helper = new wfWAFAutoPrependHelper($optionValue, null);
					$backups = $helper->getFilesNeededForBackup();
					$jsonBackups = json_encode(array_map('basename', $backups));
					?>
					<div class="wf-waf-backups wf-waf-backups-<?php echo $class; ?>" style="display: none;" data-backups="<?php echo esc_attr($jsonBackups); ?>">
						<ul class="wf-waf-backup-file-list">
							<?php
							foreach ($backups as $index => $backup) {
								echo '<li><a class="wf-btn wf-btn-default wf-waf-backup-download" data-backup-index="' . $index . '" href="' .
									esc_url(add_query_arg(array(
										'downloadBackup'      => 1,
										'backupIndex'         => $index,
										'serverConfiguration' => $helper->getServerConfig(),
										'wfnonce'             => $wfnonce,
									), $adminURL)) . '">' . sprintf(__('Download %s', 'wordfence'), esc_html(basename($backup))) . '</a></li>';
							}
							?>
						</ul>
					</div>
				<?php endforeach; ?>
		<?php endif; ?>
		</div>
		<div class="wf-modal-footer">
			<ul class="wf-flex-horizontal wf-flex-full-width">
				<li><?php _e('Once you have downloaded the files, click Continue to complete uninstallation.', 'wordfence'); ?></li>
				<li class="wf-right"><a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle wf-disabled" id="wf-waf-uninstall-continue"><?php _e('Continue', 'wordfence'); ?></a></li>
			</ul>
		</div>
	</div>
</script>