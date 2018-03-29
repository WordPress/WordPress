<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<script type="text/x-jquery-template" id="wafTmpl_install">
	<div class="wf-modal">
		<div class="wf-modal-header">
			<div class="wf-modal-header-content">
				<div class="wf-modal-title">
					<strong><?php _e('Optimize Wordfence Firewall', 'wordfence'); ?></strong>
				</div>
			</div>
			<div class="wf-modal-header-action">
				<div><?php printf(__('If you cannot complete the setup process, <a target="_blank" rel="noopener noreferrer" href="%s">click here for help</a>', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_WAF_INSTALL_MANUALLY)); ?></div>
				<div class="wf-padding-add-left-small wf-modal-header-action-close"><a href="#" onclick="WFAD.colorboxClose(); return false"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
			</div>
		</div>
		<div class="wf-modal-content">
			<?php
			$currentAutoPrependFile = ini_get('auto_prepend_file');
			if (empty($currentAutoPrependFile)):
			?>
			<p><?php _e('To make your site as secure as possible, the Wordfence Web Application Firewall is designed to run via a PHP setting called <code>auto_prepend_file</code>, which ensures it runs before any potentially vulnerable code runs.', 'wordfence'); ?></p>
			<?php else: ?>
			<p><?php _e('To make your site as secure as possible, the Wordfence Web Application Firewall is designed to run via a PHP setting called <code>auto_prepend_file</code>, which ensures it runs before any potentially vulnerable code runs. This PHP setting is currently in use, and is including this file:', 'wordfence'); ?></p>
			<pre class='wf-pre'><?php echo esc_html($currentAutoPrependFile); ?></pre>
			<p><?php _e('If you don\'t recognize this file, please <a href="https://wordpress.org/support/plugin/wordfence" target="_blank" rel="noopener noreferrer">contact us on the
					WordPress support forums</a> before proceeding.', 'wordfence'); ?></p>
			<p><?php _e('You can proceed with the installation and we will include this from within our <code>wordfence-waf.php</code> file which should maintain compatibility with your site, or you can opt to override the existing PHP setting.', 'wordfence'); ?></p>
			<ul id="wf-waf-include-prepend" class="wf-switch"><li class="wf-active" data-option-value="include"><?php _e('Include', 'wordfence'); ?></li><li data-option-value="override"><?php _e('Override', 'wordfence'); ?></li></ul>
			<?php endif; ?>
			<div class="wf-notice"><strong><?php _e('NOTE:', 'wordfence'); ?></strong> <?php _e('If you have separate WordPress installations with Wordfence installed within a subdirectory of this site, it is recommended that you perform the Firewall installation procedure on those sites before this one.', 'wordfence'); ?></div>
			<?php
			$serverInfo = wfWebServerInfo::createFromEnvironment();
			$dropdown = array(
				array("apache-mod_php", __('Apache + mod_php', 'wordfence'), $serverInfo->isApacheModPHP(), wfWAFAutoPrependHelper::helper('apache-mod_php')->getFilesNeededForBackup()),
				array("apache-suphp", __('Apache + suPHP', 'wordfence'), $serverInfo->isApacheSuPHP(), wfWAFAutoPrependHelper::helper('apache-suphp')->getFilesNeededForBackup()),
				array("cgi", __('Apache + CGI/FastCGI', 'wordfence'), $serverInfo->isApache() && !$serverInfo->isApacheSuPHP() && ($serverInfo->isCGI() || $serverInfo->isFastCGI()), wfWAFAutoPrependHelper::helper('cgi')->getFilesNeededForBackup()),
				array("litespeed", __('LiteSpeed/lsapi', 'wordfence'), $serverInfo->isLiteSpeed(), wfWAFAutoPrependHelper::helper('litespeed')->getFilesNeededForBackup()),
				array("nginx", __('NGINX', 'wordfence'), $serverInfo->isNGINX(), wfWAFAutoPrependHelper::helper('nginx')->getFilesNeededForBackup()),
				array("iis", __('Windows (IIS)', 'wordfence'), $serverInfo->isIIS(), wfWAFAutoPrependHelper::helper('iis')->getFilesNeededForBackup()),
				/*array("manual", __('Manual Configuration', 'wordfence'), false, array()),*/
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
				<p><?php _e('We\'ve preselected your server configuration based on our tests, but if you know your web server\'s configuration, please select it now.', 'wordfence'); /*_e('We\'ve preselected your server configuration based on our tests, but if you know your web server\'s configuration, please select it now. You can also choose "Manual Configuration" for alternate installation details.', 'wordfence');*/ ?></p>
			<?php endif; ?>
			<select name='serverConfiguration' id='wf-waf-server-config'>
				<?php echo $wafPrependOptions; ?>
			</select>
			<div class="wf-notice wf-nginx-waf-config" style="display: none;"><?php printf(__('Part of the Firewall configuration procedure for NGINX depends on creating a <code>%s</code> file in the root of your WordPress installation. This file can contain sensitive information and public access to it should be restricted. We have <a href="%s">instructions on our documentation site</a> on what directives to put in your nginx.conf to fix this.', 'wordfence'), esc_html(ini_get('user_ini.filename') ? ini_get('user_ini.filename') : __('(.user.ini)', 'wordfence')), wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_WAF_INSTALL_NGINX)); ?></div>
			<div class="wf-manual-waf-config" style="display: none;">
				<p><?php printf(__('If you are using a web server not listed in the dropdown or if file permissions prevent the installer from completing successfully, you will need to perform the change manually. Insert the following code into your <code>php.ini</code>:', 'wordfence')); ?></p>
				<pre class="wf-pre">auto_prepend_file = '<?php echo esc_html(addcslashes(wordfence::getWAFBootstrapPath(), "'")); ?>'</pre>
			</div>
			<p class="wf-waf-automatic-only"><?php _e('Please download a backup of the following files before we make the necessary changes:', 'wordfence'); ?></p>
			<?php
			$adminURL = network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options&action=configureAutoPrepend');
			$wfnonce = wp_create_nonce('wfWAFAutoPrepend');
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
		</div>
		<div class="wf-modal-footer">
			<ul class="wf-flex-horizontal wf-flex-full-width">
				<li class="wf-waf-automatic-only"><?php _e('Once you have downloaded the files, click Continue to complete the setup.', 'wordfence'); ?></li>
				<li class="wf-right"><a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle wf-disabled" id="wf-waf-install-continue"><?php _e('Continue', 'wordfence'); ?></a></li>
			</ul>
		</div>
	</div>
</script>