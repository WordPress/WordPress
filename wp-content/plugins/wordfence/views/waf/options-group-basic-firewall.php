<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Basic Firewall Options group.
 *
 * Expects $firewall, $waf, and $stateKey.
 *
 * @var wfFirewall $firewall
 * @var wfWAF $waf
 * @var string $stateKey The key under which the collapse state is stored.
 * @var bool $collapseable If defined, specifies whether or not this grouping can be collapsed. Defaults to true.
 */

$config = $waf->getStorageEngine();

if (!isset($collapseable)) {
	$collapseable = true;
}
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block<?php if (!$collapseable) { echo ' wf-always-active'; } else { echo (wfPersistenceController::shared()->isActive($stateKey) ? ' wf-active' : ''); } ?>" data-persistence-key="<?php echo esc_attr($stateKey); ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Basic Firewall Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list wf-block-list-horizontal">
					<li id="wf-option-wafStatus" class="wf-flex-vertical wf-flex-align-left wf-flex-full-width">
						<h3><?php esc_html_e('Web Application Firewall Status', 'wordfence'); ?></h3>
						<?php if ($firewall->isSubDirectoryInstallation()): ?>
							<p class="wf-no-top"><?php printf(__('You are currently running the Wordfence Web Application Firewall from another WordPress installation. Please <a href="%s">click here</a> to configure the Firewall to run correctly on this site.', 'wordfence'), esc_attr(network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#configureAutoPrepend'))); ?></p>
						<?php else: ?>
							<p class="wf-no-top">
								<?php $wafStatus = $firewall->firewallMode(); ?>
								<span id="wafStatus-enabled-description" class="wafStatus-description"<?php if ($wafStatus != wfFirewall::FIREWALL_MODE_ENABLED) { echo ' style="display: none;"'; } ?>><strong><?php _e('Enabled and Protecting:', 'wordfence'); ?></strong> <?php _e('In this mode, the Wordfence Web Application Firewall is actively blocking requests matching known attack patterns and is actively protecting your site from attackers.', 'wordfence'); ?></span>
								<span id="wafStatus-learning-mode-description" class="wafStatus-description"<?php if ($wafStatus != wfFirewall::FIREWALL_MODE_LEARNING) { echo ' style="display: none;"'; } ?>><strong><?php _e('Learning Mode:', 'wordfence'); ?></strong> <?php printf(__('When you first install the Wordfence Web Application Firewall, it will be in learning mode. This allows Wordfence to learn about your site so that we can understand how to protect it and how to allow normal visitors through the firewall. We recommend you let Wordfence learn for a week before you enable the firewall. <a href="%s" target="_blank" rel="noopener noreferrer">Learn More</a>', 'wordfence'), wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_LEARNING_MODE)); ?></span>
								<span id="wafStatus-disabled-description" class="wafStatus-description"<?php if ($wafStatus != wfFirewall::FIREWALL_MODE_DISABLED) { echo ' style="display: none;"'; } ?>><strong><?php _e('Disabled:', 'wordfence'); ?></strong> <?php _e('In this mode, the Wordfence Web Application Firewall is functionally turned off and does not run any of its rules or analyze the request in any way.', 'wordfence'); ?></span>
							</p>
							<p class="wf-no-top wf-add-bottom">
								<select id="input-wafStatus" data-original-value="<?php echo esc_attr($wafStatus); ?>" name="wafStatus" class="wf-form-control"<?php echo !WFWAF_ENABLED ? ' disabled' : '' ?>>
									<option<?php echo $wafStatus == wfFirewall::FIREWALL_MODE_ENABLED ? ' selected' : '' ?> class="wafStatus-enabled" value="enabled"><?php _e('Enabled and Protecting', 'wordfence'); ?></option>
									<option<?php echo $wafStatus == wfFirewall::FIREWALL_MODE_LEARNING ? ' selected' : '' ?> class="wafStatus-learning-mode" value="learning-mode"><?php _e('Learning Mode', 'wordfence'); ?></option>
									<option<?php echo $wafStatus == wfFirewall::FIREWALL_MODE_DISABLED ? ' selected' : '' ?> class="wafStatus-disabled" value="disabled"><?php _e('Disabled', 'wordfence'); ?></option>
								</select>
								<script type="application/javascript">
									(function($) {
										$(function() {
											$('#input-wafStatus').wfselect2({
												minimumResultsForSearch: -1,
												width: '200px'
											}).on('change', function() {
												var select = $(this);
												var value = select.val();
												var container = $($(this).data('wfselect2').$container);
												container.removeClass('wafStatus-enabled wafStatus-learning-mode wafStatus-disabled')
													.addClass('wafStatus-' + value);

												$('.wafStatus-description').hide();
												$('#wafStatus-' + value + '-description').show();
												if (value == 'learning-mode') {
													$('#waf-learning-mode-grace-period').show();
												}
												else {
													$('#waf-learning-mode-grace-period').hide();
												}

												var originalValue = select.data('originalValue');
												if (originalValue == value) {
													delete WFAD.pendingChanges['wafStatus'];
												}
												else {
													WFAD.pendingChanges['wafStatus'] = value;
												}

												WFAD.updatePendingChanges();
											}).val(<?php echo json_encode($wafStatus) ?>).triggerHandler('change');

											$('#waf-learning-mode-grace-period .wf-datetime').datetimepicker({
												dateFormat: 'yy-mm-dd',
												timezone: <?php echo (int) wfUtils::timeZoneMinutes($config->getConfig('learningModeGracePeriod') ? (int) $config->getConfig('learningModeGracePeriod') : false); ?>,
												showTime: false,
												showTimepicker: false,
												showMonthAfterYear: true
											}).each(function() {
												var el = $(this);
												if (el.attr('data-value')) {
													el.datetimepicker('setDate', new Date(el.attr('data-value') * 1000));
												}
											}).on('change', function() {
												var value = Math.floor($(this).datetimepicker('getDate').getTime() / 1000);
												var originalValue = $('#input-learningModeGracePeriod').data('originalValue');
												if (originalValue == value) {
													delete WFAD.pendingChanges['learningModeGracePeriod'];
												}
												else {
													WFAD.pendingChanges['learningModeGracePeriod'] = $(this).val();
												}
												WFAD.updatePendingChanges();
											});

											$('#waf-learning-mode-grace-period .wf-option-checkbox').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												var originalValue = $(this).data('originalValue');
												var value = originalValue;
												var isActive = $(this).hasClass('wf-checked');
												if (isActive) {
													$(this).removeClass('wf-checked');
													$('#waf-learning-mode-grace-period .wf-datetime').attr('disabled', true);
													value = 0;
												}
												else {
													$(this).addClass('wf-checked');
													$('#waf-learning-mode-grace-period .wf-datetime').attr('disabled', false);
													value = 1;

													if (!$('#input-learningModeGracePeriod').val()) {
														var date = new Date();
														date.setDate(date.getDate() + 7);
														$('#input-learningModeGracePeriod').datetimepicker('setDate', date);
													}
												}

												if (originalValue == value) {
													delete WFAD.pendingChanges['learningModeGracePeriodEnabled'];
												}
												else {
													WFAD.pendingChanges['learningModeGracePeriodEnabled'] = value;
												}

												WFAD.updatePendingChanges();
											});

											$(window).on('wfOptionsReset', function() {
												$('#input-wafStatus').val($('#input-wafStatus').data('originalValue')).trigger('change');
												$('#waf-learning-mode-grace-period .wf-option-checkbox').each(function() {
													var originalValue = $(this).data('originalValue');
													$(this).toggleClass('wf-checked', !!originalValue);
													$('#waf-learning-mode-grace-period .wf-datetime').attr('disabled', !originalValue);
												});
												$('.wf-datetime').each(function() {
													var el = $(this);
													if (el.attr('data-value')) {
														el.datetimepicker('setDate', new Date(el.attr('data-value') * 1000));
													}
													else {
														el.val('');
													}
												})
											});
										});
									})(jQuery);
								</script>
							</p>
							<div id="waf-learning-mode-grace-period" class="wf-add-bottom" style="display: none;"><div class="waf-learning-mode wf-option-checkbox<?php echo $config->getConfig('learningModeGracePeriodEnabled') ? ' wf-checked' : ''; ?>" data-original-value="<?php echo $config->getConfig('learningModeGracePeriodEnabled') ? 1 : 0; ?>"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div><span> <?php _e('Automatically enable on', 'wordfence'); ?> </span><input type="text" name="learningModeGracePeriod" id="input-learningModeGracePeriod" class="wf-datetime wf-form-control" placeholder="Enabled until..." data-value="<?php echo esc_attr($config->getConfig('learningModeGracePeriod') ? (int) $config->getConfig('learningModeGracePeriod') : '') ?>" data-original-value="<?php echo esc_attr($config->getConfig('learningModeGracePeriod') ? (int) $config->getConfig('learningModeGracePeriod') : '') ?>"<?php echo $config->getConfig('learningModeGracePeriodEnabled') ? '' : ' disabled'; ?>></div>
						<?php endif; ?>
					</li>
					<li id="wf-option-protectionMode" class="wf-flex-vertical wf-flex-align-left">
						<h3><?php esc_html_e('Protection Level', 'wordfence'); ?></h3>
						<?php if ($firewall->protectionMode() == wfFirewall::PROTECTION_MODE_EXTENDED && !$firewall->isSubDirectoryInstallation()): ?>
							<p class="wf-no-top"><strong><?php _e('Extended Protection:', 'wordfence'); ?></strong> <?php _e('All PHP requests will be processed by the firewall prior to running.', 'wordfence'); ?></p>
							<p><?php printf(__('If you\'re moving to a new host or a new installation location, you may need to temporarily disable extended protection to avoid any file not found errors. Use this action to remove the configuration changes that enable extended protection mode or you can <a href="%s" target="_blank" rel="noopener noreferrer">remove them manually</a>.', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_WAF_REMOVE_MANUALLY)); ?></p>
							<p class="wf-no-top"><a class="wf-btn wf-btn-default" href="#" id="wf-waf-uninstall"><?php _e('Remove Extended Protection', 'wordfence'); ?></a></p>
						<?php elseif ($firewall->isSubDirectoryInstallation()): ?>
							<p class="wf-no-top"><strong><?php _e('Existing WAF Installation Detected:', 'wordfence'); ?></strong> <?php _e('You are currently running the Wordfence Web Application Firewall from another WordPress installation. Please configure the firewall to run correctly on this site.', 'wordfence'); ?></p>
							<p><a class="wf-btn wf-btn-primary" href="#" id="wf-waf-install"><?php _e('Optimize the Wordfence Firewall', 'wordfence'); ?></a></p>
						<?php else: ?>
							<p class="wf-no-top"><strong><?php _e('Basic WordPress Protection:', 'wordfence'); ?></strong> <?php _e('The plugin will load as a regular plugin after WordPress has been loaded, and while it can block many malicious requests, some vulnerable plugins or WordPress itself may run vulnerable code before all plugins are loaded.', 'wordfence'); ?></p>
							<p><a class="wf-btn wf-btn-primary" href="#" id="wf-waf-install"><?php _e('Optimize the Wordfence Firewall', 'wordfence'); ?></a></p>
						<?php endif; ?>
						<script type="application/javascript">

							(function($) {
								$(function() {
									var validateContinue = function() {
										var backupsAvailable = $('.wf-waf-backups:visible').data('backups');
										var backupsDownloaded = $('#wf-waf-server-config').data('backups');

										var matchCount = 0;
										backupsAvailable = backupsAvailable.sort();
										backupsDownloaded = backupsDownloaded.sort();
										for (var i = 0; i < backupsAvailable.length; i++) {
											for (var n = 0; n < backupsDownloaded.length; n++) {
												if (backupsAvailable[i] == backupsDownloaded[n]) {
													matchCount++;
												}
											}
										}

										$('#wf-waf-install-continue, #wf-waf-uninstall-continue').toggleClass('wf-disabled', matchCount != backupsAvailable.length);
										$('#wf-waf-install-continue, #wf-waf-uninstall-continue').text($('.wf-manual-waf-config').is(':visible') ? 'Close' : 'Continue');
									};

									var installUninstallResponseHandler = function(action, res) {
										var modal = $('.wf-modal-title').closest('.wf-modal');
										if (res.needsCredentials) {
											var replacement = $(res.html);
											modal.replaceWith(replacement);
											modal = replacement;

											var form = replacement.find('#request-filesystem-credentials-form').closest('form');
											form.find('input[type="submit"]').attr('type', 'hidden');
											form.on('submit', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.ajax(action, form.serialize(), function(res) {
													installUninstallResponseHandler(action, res);
												});
											});
											modal.find('#wf-waf-modal-continue').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												form.trigger('submit');
											});
											$.wfcolorbox.resize();
										}
										else if (res.credentialsFailed || res.installationFailed || res.uninstallationFailed) {
											var replacement = $(res.html);
											modal.replaceWith(replacement);
											modal = replacement;
											modal.find('#wf-waf-modal-continue').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.colorboxClose();
											});
											$.wfcolorbox.resize();
										}
										else if (res.uninstallationWaiting) {
											var replacement = $(res.html);
											modal.replaceWith(replacement);
											modal = replacement;
											modal.find('#wf-waf-modal-continue').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.colorboxClose();
											}).addClass('wf-disabled');

											var timeout = res.timeout; //In seconds
											setTimeout(function() {
												modal.find('#wf-waf-modal-continue').removeClass('wf-disabled');
												var payload = {serverConfiguration: res.serverConfiguration, iniModified: 1};
												if (res.credentials) {
													payload['credentials'] = res.credentials;
													payload['credentialsSignature'] = res.credentialsSignature;
												}
												WFAD.ajax(action, payload, function(res) {
													installUninstallResponseHandler(action, res);
												});
											}, (timeout + 10) * 1000);
											$.wfcolorbox.resize();
										}
										else if (res.ok) {
											var replacement = $(res.html);
											modal.replaceWith(replacement);
											modal = replacement;
											modal.find('#wf-waf-modal-continue').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												window.location.reload(true);
											});
											$.wfcolorbox.resize();
										}
										else {
											WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), 'Error During Setup', res.errorMsg);
										}
									};

									var installUninstallHandler = function(html) {
										WFAD.colorboxHTML('800px', html, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
											$('#wf-waf-server-config').data('backups', []);
											$('.wf-waf-backup-download').on('click', function(e) {
												var backupIndex = parseInt($(this).data('backupIndex'));
												var backupsAvailable = $(this).closest('.wf-waf-backups').data('backups');
												var backupsDownloaded = $('#wf-waf-server-config').data('backups');
												var found = false;
												for (var i = 0; i < backupsDownloaded.length; i++) {
													if (backupsDownloaded[i] == backupsAvailable[backupIndex]) {
														found = true;
														break;
													}
												}

												if (!found) {
													backupsDownloaded.push(backupsAvailable[backupIndex]);
													$('#wf-waf-server-config').data('backups', backupsDownloaded);
													validateContinue();
												}
											});

											$('#wf-waf-server-config').wfselect2({
												minimumResultsForSearch: -1,
												width: WFAD.isSmallScreen ? '300px' : '500px'
											});

											$('#wf-waf-include-prepend > li').each(function(index, element) {
												$(element).on('click', function(e) {
													e.preventDefault();
													e.stopPropagation();

													var control = $(this).closest('.wf-switch');
													var value = $(this).data('optionValue');

													control.find('li').each(function() {
														$(this).toggleClass('wf-active', value == $(this).data('optionValue'));
													});
												});
											});
											
											var nginxNotice = $('.wf-nginx-waf-config');
											var manualNotice = $('.wf-manual-waf-config');
											$('#wf-waf-server-config').on('change', function() {
												var el = $(this);
												if (manualNotice.length) {
													if (el.val() == 'manual') {
														$('.wf-waf-automatic-only').hide();
														manualNotice.fadeIn(400, function () {
															$.wfcolorbox.resize();
														});
													}
													else {
														$('.wf-waf-automatic-only').show();
														manualNotice.fadeOut(400, function () {
															$.wfcolorbox.resize();
														});
													}
												}
												else {
													$('.wf-waf-automatic-only').show();
												}
												
												$('.wf-waf-backups').hide();
												$('.wf-waf-backups-' + el.val().replace(/[^a-z0-9\-]/i, '')).show();

												if (nginxNotice.length) { //Install only
													if (el.val() == 'nginx') {
														nginxNotice.fadeIn(400, function () {
															$.wfcolorbox.resize();
														});
													}
													else {
														nginxNotice.fadeOut(400, function () {
															$.wfcolorbox.resize();
														});
													}

													validateContinue();
													return;
												}

												$.wfcolorbox.resize();
												validateContinue();
											}).triggerHandler('change');

											$('#wf-waf-install-continue').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												if ($('.wf-manual-waf-config').is(':visible')) {
													WFAD.colorboxClose();
													return;
												}

												var serverConfiguration = $('#wf-waf-server-config').val();
												var currentAutoPrepend = $('#wf-waf-include-prepend .wf-active').data('optionValue');

												WFAD.ajax('wordfence_installAutoPrepend', {serverConfiguration: serverConfiguration, currentAutoPrepend: currentAutoPrepend}, function(res) {
													installUninstallResponseHandler('wordfence_installAutoPrepend', res);
												});
											});

											$('#wf-waf-uninstall-continue').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												if ($('.wf-manual-waf-config').is(':visible')) {
													WFAD.colorboxClose();
													return;
												}

												var serverConfiguration = $('#wf-waf-server-config').val();

												WFAD.ajax('wordfence_uninstallAutoPrepend', {serverConfiguration: serverConfiguration}, function(res) {
													installUninstallResponseHandler('wordfence_uninstallAutoPrepend', res);
												});
											});
										}});
									};

									$('#wf-waf-install').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();

										var installer = $('#wafTmpl_install').tmpl();
										var installerHTML = $("<div />").append(installer).html();
										installUninstallHandler(installerHTML);
									});

									$('#wf-waf-uninstall').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();

										var uninstaller = $('#wafTmpl_uninstall').tmpl();
										var uninstallerHTML = $("<div />").append(uninstaller).html();
										installUninstallHandler(uninstallerHTML);
									});

									if (window.location.hash) {
										var hashes = window.location.hash.split('#');
										for (var i = 0; i < hashes.length; i++) {
											if (hashes[i] == 'configureAutoPrepend') {
												$('#wf-waf-install').trigger('click');
												history.replaceState('', document.title, window.location.pathname + window.location.search);
											}
											else if (hashes[i] == 'removeAutoPrepend') {
												$('#wf-waf-uninstall').trigger('click');
												history.replaceState('', document.title, window.location.pathname + window.location.search);
											}
										}
									}

									$(window).on('hashchange', function () {
										var hashes = window.location.hash.split('#');
										for (var i = 0; i < hashes.length; i++) {
											if (hashes[i] == 'configureAutoPrepend') {
												$('#wf-waf-install').trigger('click');
												history.replaceState('', document.title, window.location.pathname + window.location.search);
											}
											else if (hashes[i] == 'removeAutoPrepend') {
												$('#wf-waf-uninstall').trigger('click');
												history.replaceState('', document.title, window.location.pathname + window.location.search);
											}
										}
									});
								});
							})(jQuery);
						</script>
					</li>
					<li id="wf-option-disableWAFBlacklistBlocking" class="wf-flex-vertical wf-flex-align-left">
						<h3><?php esc_html_e('Real-Time IP Blacklist', 'wordfence'); ?></h3>
						<?php if ($firewall->ruleMode() == wfFirewall::RULE_MODE_COMMUNITY): ?>
							<p class="wf-no-top"><strong><?php _e('Premium Feature:', 'wordfence'); ?></strong> <?php _e('This feature blocks all traffic from IPs with a high volume of recent malicious activity using Wordfence\'s real-time blacklist.', 'wordfence'); ?></p>
							<p><a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="https://www.wordfence.com/gnl1blacklistUpgrade/wordfence-signup/#premium-order-form" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a>&nbsp;&nbsp;<a class="wf-btn wf-btn-callout-subtle wf-btn-default" href="https://www.wordfence.com/gnl1blacklistLearn/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></p>
						<?php elseif ($firewall->isSubDirectoryInstallation()): ?>
							<p class="wf-no-top"><?php printf(__('You are currently running the Wordfence Web Application Firewall from another WordPress installation. Please <a href="%s">click here</a> to configure the Firewall to run correctly on this site.', 'wordfence'), esc_attr(network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#configureAutoPrepend'))); ?></p>
						<?php else: ?>
							<p class="wf-no-top"><?php _e('This feature blocks all traffic from IPs with a high volume of recent malicious activity using Wordfence\'s real-time blacklist.', 'wordfence'); ?></p>
							<div class="wf-option wf-option-switch wf-padding-add-bottom" data-option-name="disableWAFBlacklistBlocking" data-original-value="<?php echo $config->getConfig('disableWAFBlacklistBlocking') ? '1': '0'; ?>">
								<ul class="wf-switch">
									<?php
									$states = array(
										array('value' => '1', 'label' => __('Disabled', 'wordfence')),
										array('value' => '0', 'label' => __('Enabled', 'wordfence')),
									);
									
									foreach ($states as $s):
										?>
										<li<?php if ($s['value'] == ($config->getConfig('disableWAFBlacklistBlocking') ? '1': '0')) { echo ' class="wf-active"'; } ?> data-option-value="<?php echo esc_attr($s['value']); ?>"><?php echo esc_html($s['label']); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end basic firewall options -->
<?php
if ($firewall->protectionMode() == wfFirewall::PROTECTION_MODE_BASIC || ($firewall->protectionMode() == wfFirewall::PROTECTION_MODE_EXTENDED && $firewall->isSubDirectoryInstallation())) {
	echo wfView::create('waf/waf-install', array(
	))->render();
}
else {
	echo wfView::create('waf/waf-uninstall', array(
	))->render();
}
?>