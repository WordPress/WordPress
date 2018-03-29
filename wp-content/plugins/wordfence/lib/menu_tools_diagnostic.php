<?php
if (!defined('WORDFENCE_VERSION')) { exit; }

/** @var boolean $inEmail */

$diagnostic = new wfDiagnostic;
$plugins = get_plugins();
$activePlugins = array_flip(get_option('active_plugins'));
$activeNetworkPlugins = is_multisite() ? array_flip(wp_get_active_network_plugins()) : array();
$muPlugins = get_mu_plugins();
$themes = wp_get_themes();
$currentTheme = wp_get_theme();
$cols = 3;

$w = new wfConfig();
if (!isset($sendingDiagnosticEmail)) {
	$sendingDiagnosticEmail = false;
}
?>
<script type="application/javascript">
	(function($) {
		$(function() {
			document.title = "<?php esc_attr_e('Diagnostics', 'wordfence'); ?>" + " \u2039 " + WFAD.basePageName;
		});
	})(jQuery);
</script>
<div id="wf-diagnostics">
	<?php if (!$sendingDiagnosticEmail): ?>
		<div class="wf-diagnostics-wrapper">
			<div class="wf-flex-row">
				<div class="wf-flex-row-1">
					<?php _e('This page shows information that can be used for troubleshooting conflicts, configuration issues, or compatibility with other plugins, themes, or a host\'s environment.', 'wordfence') ?>
				</div>
				<div class="wf-flex-row-0 wf-padding-add-left">
					<div id="sendByEmailThanks" class="hidden">
						<h3>Thanks for sending your diagnostic page over email</h3>
					</div>
					<div id="sendByEmailDiv" class="wf-add-bottom">
						<span class="wf-nowrap">
							<input class="wf-btn wf-btn-primary" type="submit" id="sendByEmail" value="Send Report by Email"/>
							<input class="wf-btn wf-btn-default" type="button" id="expandAllDiagnostics" value="Expand All Diagnostics"/>
						</span>
					</div>
				</div>
			</div>
			<div id="sendByEmailForm" class="wf-block wf-active hidden">
				<div class="wf-block-header">
					<div class="wf-block-header-content">
						<div class="wf-block-title">
							<strong><?php echo esc_html(__('Send Report by Email', 'wordfence')) ?></strong>
						</div>
					</div>
				</div>
				<div class="wf-block-content wf-clearfix">
					<ul class="wf-block-list">
						<li>
							<div>Email address:</div>
							<div style="width: 40%">
								<p><input class="wf-input-text" type="email" id="_email" value="wftest@wordfence.com"/>
								</p>
							</div>
						</li>
						<li>
							<div>Ticket Number/Forum Username:</div>
							<div style="width: 40%">
								<p><input class="wf-input-text" type="text" id="_ticketnumber" required/></p>
							</div>
						</li>
						<li>
							<p>
								<input class="wf-btn wf-btn-primary" type="button" id="doSendEmail" value="Send"/>
							</p>
						</li>
					</ul>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<form id="wfConfigForm" style="overflow-x: auto;">
		<?php foreach ($diagnostic->getResults() as $title => $tests):
			$key = sanitize_key('wf-diagnostics-' . $title);
			$hasFailingTest = false;
			foreach ($tests['results'] as $result) {
				if (!$result['test']) {
					$hasFailingTest = true;
					break;
				}
			}

			if ($inEmail): ?>
				<table>
					<thead>
					<tr>
						<th colspan="<?php echo $cols ?>"><?php echo esc_html(__($title, 'wordfence')) ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($tests['results'] as $result): ?>
						<tr>
							<td style="width: 75%;"
									colspan="<?php echo $cols - 1 ?>"><?php echo wp_kses($result['label'], array(
									'code'   => array(),
									'strong' => array(),
									'em'     => array(),
									'a'      => array('href' => true),
								)) ?></td>
							<td>
								<?php if ($result['test']): ?>
									<div class="wf-result-success"><?php echo esc_html($result['message']) ?></div>
								<?php else: ?>
									<div class="wf-result-error"><?php echo esc_html($result['message']) ?></div>
								<?php endif ?>
							</td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			<?php else: ?>
				<div class="wf-block<?php echo (wfPersistenceController::shared()->isActive($key) ? ' wf-active' : '') .
					($hasFailingTest ? ' wf-diagnostic-fail' : '') ?>" data-persistence-key="<?php echo esc_attr($key) ?>">
					<div class="wf-block-header">
						<div class="wf-block-header-content">
							<div class="wf-block-title">
								<strong><?php echo esc_html(__($title, 'wordfence')) ?></strong>
								<span class="wf-text-small"><?php echo esc_html(__($tests['description'], 'wordfence')) ?></span>
							</div>
							<div class="wf-block-header-action">
								<div class="wf-block-header-action-disclosure"></div>
							</div>
						</div>
					</div>
					<div class="wf-block-content wf-clearfix">
						<ul class="wf-block-list">
							<?php foreach ($tests['results'] as $result): ?>
								<li>
									<div style="width: 75%;"
											colspan="<?php echo $cols - 1 ?>"><?php echo wp_kses($result['label'], array(
											'code'   => array(),
											'strong' => array(),
											'em'     => array(),
											'a'      => array('href' => true),
										)) ?></div>
									<?php if ($result['test']): ?>
										<div class="wf-result-success"><?php echo esc_html($result['message']) ?></div>
									<?php else: ?>
										<div class="wf-result-error"><?php echo esc_html($result['message']) ?></div>
									<?php endif ?>
								</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			<?php endif ?>

		<?php endforeach ?>
		<?php
		$howGet = wfConfig::get('howGetIPs', false);
		list($currentIP, $currentServerVarForIP) = wfUtils::getIPAndServerVariable();
		$howGetHasErrors = false;
		foreach (array(
			         'REMOTE_ADDR'           => 'REMOTE_ADDR',
			         'HTTP_CF_CONNECTING_IP' => 'CF-Connecting-IP',
			         'HTTP_X_REAL_IP'        => 'X-Real-IP',
			         'HTTP_X_FORWARDED_FOR'  => 'X-Forwarded-For',
		         ) as $variable => $label) {
			if (!($currentServerVarForIP && $currentServerVarForIP === $variable) && $howGet === $variable) {
				$howGetHasErrors = true;
				break;
			}
		}
		?>
		<div class="wf-block<?php echo ($howGetHasErrors ? ' wf-diagnostic-fail' : '') . (wfPersistenceController::shared()->isActive('wf-diagnostics-client-ip') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-client-ip') ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('IP Detection', 'wordfence') ?></strong>
						<span class="wf-text-small"><?php _e('Methods of detecting a visitor\'s IP address.', 'wordfence') ?></span>
					</div>
					<div class="wf-block-header-action">
						<div class="wf-block-header-action-disclosure"></div>
					</div>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix wf-padding-no-left wf-padding-no-right">

				<table class="wf-striped-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
					<tbody class="thead">
					<tr>
						<th>IPs</th>
						<th>Value</th>
						<th>Used</th>
					</tr>
					</tbody>
					<tbody>
					<?php
					$howGet = wfConfig::get('howGetIPs', false);
					list($currentIP, $currentServerVarForIP) = wfUtils::getIPAndServerVariable();
					foreach (array(
						         'REMOTE_ADDR'           => 'REMOTE_ADDR',
						         'HTTP_CF_CONNECTING_IP' => 'CF-Connecting-IP',
						         'HTTP_X_REAL_IP'        => 'X-Real-IP',
						         'HTTP_X_FORWARDED_FOR'  => 'X-Forwarded-For',
					         ) as $variable => $label): ?>
						<tr>
							<td><?php echo $label ?></td>
							<td><?php
								if (!array_key_exists($variable, $_SERVER)) {
									echo '(not set)';
								} else {
									if (strpos($_SERVER[$variable], ',') !== false) {
										$trustedProxies = explode("\n", wfConfig::get('howGetIPs_trusted_proxies', ''));
										$items = preg_replace('/[\s,]/', '', explode(',', $_SERVER[$variable]));
										$items = array_reverse($items);
										$output = '';
										$markedSelectedAddress = false;
										foreach ($items as $index => $i) {
											foreach ($trustedProxies as $proxy) {
												if (!empty($proxy)) {
													if (wfUtils::subnetContainsIP($proxy, $i) && $index < count($items) - 1) {
														$output = esc_html($i) . ', ' . $output;
														continue 2;
													}
												}
											}

											if (!$markedSelectedAddress) {
												$output = '<strong>' . esc_html($i) . '</strong>, ' . $output;
												$markedSelectedAddress = true;
											} else {
												$output = esc_html($i) . ', ' . $output;
											}
										}

										echo substr($output, 0, -2);
									} else {
										echo esc_html($_SERVER[$variable]);
									}
								}
								?></td>
							<?php if ($currentServerVarForIP && $currentServerVarForIP === $variable): ?>
								<td class="wf-result-success">In use</td>
							<?php elseif ($howGet === $variable): ?>
								<td class="wf-result-error">Configured, but not valid</td>
							<?php else: ?>
								<td></td>
							<?php endif ?>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>

			</div>
		</div>

		<div class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-wordpress-constants') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-wordpress-constants') ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('WordPress Settings', 'wordfence') ?></strong>
						<span class="wf-text-small"><?php _e('WordPress version and internal settings/constants.', 'wordfence') ?></span>
					</div>
					<div class="wf-block-header-action">
						<div class="wf-block-header-action-disclosure"></div>
					</div>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix wf-padding-no-left wf-padding-no-right">
				<table class="wf-striped-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
					<tbody>
					<?php
					require(ABSPATH . 'wp-includes/version.php');
					$postRevisions = (defined('WP_POST_REVISIONS') ? WP_POST_REVISIONS : true);
					$wordPressValues = array(
						'WordPress Version'            => array('description' => '', 'value' => $wp_version),
						'WP_DEBUG'                     => array('description' => 'WordPress debug mode', 'value' => (defined('WP_DEBUG') && WP_DEBUG ? 'On' : 'Off')),
						'WP_DEBUG_LOG'                 => array('description' => 'WordPress error logging override', 'value' => defined('WP_DEBUG_LOG') ? (WP_DEBUG_LOG ? 'Enabled' : 'Disabled') : '(not set)'),
						'WP_DEBUG_DISPLAY'             => array('description' => 'WordPress error display override', 'value' => defined('WP_DEBUG_DISPLAY') ? (WP_DEBUG_LOG ? 'Enabled' : 'Disabled') : '(not set)'),
						'SCRIPT_DEBUG'                 => array('description' => 'WordPress script debug mode', 'value' => (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? 'On' : 'Off')),
						'SAVEQUERIES'                  => array('description' => 'WordPress query debug mode', 'value' => (defined('SAVEQUERIES') && SAVEQUERIES ? 'On' : 'Off')),
						'DB_CHARSET'                   => 'Database character set',
						'DB_COLLATE'                   => 'Database collation',
						'WP_SITEURL'                   => 'Explicitly set site URL',
						'WP_HOME'                      => 'Explicitly set blog URL',
						'WP_CONTENT_DIR'               => array('description' => '"wp-content" folder is in default location', 'value' => (realpath(WP_CONTENT_DIR) === realpath(ABSPATH . 'wp-content') ? 'Yes' : 'No')),
						'WP_CONTENT_URL'               => 'URL to the "wp-content" folder',
						'WP_PLUGIN_DIR'                => array('description' => '"plugins" folder is in default location', 'value' => (realpath(WP_PLUGIN_DIR) === realpath(ABSPATH . 'wp-content/plugins') ? 'Yes' : 'No')),
						'WP_LANG_DIR'                  => array('description' => '"languages" folder is in default location', 'value' => (realpath(WP_LANG_DIR) === realpath(ABSPATH . 'wp-content/languages') ? 'Yes' : 'No')),
						'WPLANG'                       => 'Language choice',
						'UPLOADS'                      => 'Custom upload folder location',
						'TEMPLATEPATH'                 => array('description' => 'Theme template folder override', 'value' => (defined('TEMPLATEPATH') && realpath(get_template_directory()) !== realpath(TEMPLATEPATH) ? 'Overridden' : '(not set)')),
						'STYLESHEETPATH'               => array('description' => 'Theme stylesheet folder override', 'value' => (defined('STYLESHEETPATH') && realpath(get_stylesheet_directory()) !== realpath(STYLESHEETPATH) ? 'Overridden' : '(not set)')),
						'AUTOSAVE_INTERVAL'            => 'Post editing automatic saving interval',
						'WP_POST_REVISIONS'            => array('description' => 'Post revisions saved by WordPress', 'value' => is_numeric($postRevisions) ? $postRevisions : ($postRevisions ? 'Unlimited' : 'None')),
						'COOKIE_DOMAIN'                => 'WordPress cookie domain',
						'COOKIEPATH'                   => 'WordPress cookie path',
						'SITECOOKIEPATH'               => 'WordPress site cookie path',
						'ADMIN_COOKIE_PATH'            => 'WordPress admin cookie path',
						'PLUGINS_COOKIE_PATH'          => 'WordPress plugins cookie path',
						'WP_ALLOW_MULTISITE'           => array('description' => 'Multisite/network ability enabled', 'value' => (defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE ? 'Yes' : 'No')),
						'NOBLOGREDIRECT'               => 'URL redirected to if the visitor tries to access a nonexistent blog',
						'CONCATENATE_SCRIPTS'          => array('description' => 'Concatenate JavaScript files', 'value' => (defined('CONCATENATE_SCRIPTS') && CONCATENATE_SCRIPTS ? 'Yes' : 'No')),
						'WP_MEMORY_LIMIT'              => 'WordPress memory limit',
						'WP_MAX_MEMORY_LIMIT'          => 'Administrative memory limit',
						'WP_CACHE'                     => array('description' => 'Built-in caching', 'value' => (defined('WP_CACHE') && WP_CACHE ? 'Enabled' : 'Disabled')),
						'CUSTOM_USER_TABLE'            => array('description' => 'Custom "users" table', 'value' => (defined('CUSTOM_USER_TABLE') ? 'Set' : '(not set)')),
						'CUSTOM_USER_META_TABLE'       => array('description' => 'Custom "usermeta" table', 'value' => (defined('CUSTOM_USER_META_TABLE') ? 'Set' : '(not set)')),
						'FS_CHMOD_DIR'                 => array('description' => 'Overridden permissions for a new folder', 'value' => defined('FS_CHMOD_DIR') ? decoct(FS_CHMOD_DIR) : '(not set)'),
						'FS_CHMOD_FILE'                => array('description' => 'Overridden permissions for a new file', 'value' => defined('FS_CHMOD_FILE') ? decoct(FS_CHMOD_FILE) : '(not set)'),
						'ALTERNATE_WP_CRON'            => array('description' => 'Alternate WP cron', 'value' => (defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON ? 'Enabled' : 'Disabled')),
						'DISABLE_WP_CRON'              => array('description' => 'WP cron status', 'value' => (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ? 'Disabled' : 'Enabled')),
						'WP_CRON_LOCK_TIMEOUT'         => 'Cron running frequency lock',
						'EMPTY_TRASH_DAYS'             => array('description' => 'Interval the trash is automatically emptied at in days', 'value' => (EMPTY_TRASH_DAYS > 0 ? EMPTY_TRASH_DAYS : 'Never')),
						'WP_ALLOW_REPAIR'              => array('description' => 'Automatic database repair', 'value' => (defined('WP_ALLOW_REPAIR') && WP_ALLOW_REPAIR ? 'Enabled' : 'Disabled')),
						'DO_NOT_UPGRADE_GLOBAL_TABLES' => array('description' => 'Do not upgrade global tables', 'value' => (defined('DO_NOT_UPGRADE_GLOBAL_TABLES') && DO_NOT_UPGRADE_GLOBAL_TABLES ? 'Yes' : 'No')),
						'DISALLOW_FILE_EDIT'           => array('description' => 'Disallow plugin/theme editing', 'value' => (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT ? 'Yes' : 'No')),
						'DISALLOW_FILE_MODS'           => array('description' => 'Disallow plugin/theme update and installation', 'value' => (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS ? 'Yes' : 'No')),
						'IMAGE_EDIT_OVERWRITE'         => array('description' => 'Overwrite image edits when restoring the original', 'value' => (defined('IMAGE_EDIT_OVERWRITE') && IMAGE_EDIT_OVERWRITE ? 'Yes' : 'No')),
						'FORCE_SSL_ADMIN'              => array('description' => 'Force SSL for administrative logins', 'value' => (defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ? 'Yes' : 'No')),
						'WP_HTTP_BLOCK_EXTERNAL'       => array('description' => 'Block external URL requests', 'value' => (defined('WP_HTTP_BLOCK_EXTERNAL') && WP_HTTP_BLOCK_EXTERNAL ? 'Yes' : 'No')),
						'WP_ACCESSIBLE_HOSTS'          => 'Whitelisted hosts',
						'WP_AUTO_UPDATE_CORE'          => array('description' => 'Automatic WP Core updates', 'value' => defined('WP_AUTO_UPDATE_CORE') ? (is_bool(WP_AUTO_UPDATE_CORE) ? (WP_AUTO_UPDATE_CORE ? 'Everything' : 'None') : WP_AUTO_UPDATE_CORE) : 'Default'),
						'WP_PROXY_HOST'                => array('description' => 'Hostname for a proxy server', 'value' => defined('WP_PROXY_HOST') ? WP_PROXY_HOST : '(not set)'),
						'WP_PROXY_PORT'                => array('description' => 'Port for a proxy server', 'value' => defined('WP_PROXY_PORT') ? WP_PROXY_PORT : '(not set)'),
					);

					foreach ($wordPressValues as $settingName => $settingData):
						$escapedName = esc_html($settingName);
						$escapedDescription = '';
						$escapedValue = '(not set)';
						if (is_array($settingData)) {
							$escapedDescription = esc_html($settingData['description']);
							if (isset($settingData['value'])) {
								$escapedValue = esc_html($settingData['value']);
							}
						} else {
							$escapedDescription = esc_html($settingData);
							if (defined($settingName)) {
								$escapedValue = esc_html(constant($settingName));
							}
						}
						?>
						<tr>
							<td><strong><?php echo $escapedName ?></strong></td>
							<td><?php echo $escapedDescription ?></td>
							<td><?php echo $escapedValue ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-wordpress-plugins') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-wordpress-plugins') ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('WordPress Plugins', 'wordfence') ?></strong>
						<span class="wf-text-small"><?php _e('Status of installed plugins.', 'wordfence') ?></span>
					</div>
					<div class="wf-block-header-action">
						<div class="wf-block-header-action-disclosure"></div>
					</div>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix wf-padding-no-left wf-padding-no-right">
				<table class="wf-striped-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
					<tbody>
					<?php foreach ($plugins as $plugin => $pluginData): ?>
						<tr>
							<td colspan="<?php echo $cols - 1 ?>">
								<strong><?php echo esc_html($pluginData['Name']) ?></strong>
								<?php if (!empty($pluginData['Version'])): ?>
									- Version <?php echo esc_html($pluginData['Version']) ?>
								<?php endif ?>
							</td>
							<?php if (array_key_exists(trailingslashit(WP_PLUGIN_DIR) . $plugin, $activeNetworkPlugins)): ?>
								<td class="wf-result-success">Network Activated</td>
							<?php elseif (array_key_exists($plugin, $activePlugins)): ?>
								<td class="wf-result-success">Active</td>
							<?php else: ?>
								<td class="wf-result-inactive">Inactive</td>
							<?php endif ?>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-mu-wordpress-plugins') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-mu-wordpress-plugins') ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Must-Use WordPress Plugins', 'wordfence') ?></strong>
						<span class="wf-text-small"><?php _e('WordPress "mu-plugins" that are always active, incluing those provided by hosts.', 'wordfence') ?></span>
					</div>
					<div class="wf-block-header-action">
						<div class="wf-block-header-action-disclosure"></div>
					</div>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix wf-padding-no-left wf-padding-no-right">
				<table class="wf-striped-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
					<?php if (!empty($muPlugins)): ?>
						<tbody>
						<?php foreach ($muPlugins as $plugin => $pluginData): ?>
							<tr>
								<td colspan="<?php echo $cols - 1 ?>">
									<strong><?php echo esc_html($pluginData['Name']) ?></strong>
									<?php if (!empty($pluginData['Version'])): ?>
										- Version <?php echo esc_html($pluginData['Version']) ?>
									<?php endif ?>
								</td>
								<td class="wf-result-success">Active</td>
							</tr>
						<?php endforeach ?>
						</tbody>
					<?php else: ?>
						<tbody>
						<tr>
							<td colspan="<?php echo $cols ?>">No MU-Plugins</td>
						</tr>
						</tbody>

					<?php endif ?>
				</table>
			</div>
		</div>
		<div class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-wordpress-themes') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-wordpress-themes') ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Themes', 'wordfence') ?></strong>
						<span class="wf-text-small"><?php _e('Status of installed themes.', 'wordfence') ?></span>
					</div>
					<div class="wf-block-header-action">
						<div class="wf-block-header-action-disclosure"></div>
					</div>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix wf-padding-no-left wf-padding-no-right">
				<table class="wf-striped-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
					<?php if (!empty($themes)): ?>
						<tbody>
						<?php foreach ($themes as $theme => $themeData): ?>
							<tr>
								<td colspan="<?php echo $cols - 1 ?>">
									<strong><?php echo esc_html($themeData['Name']) ?></strong>
									Version <?php echo esc_html($themeData['Version']) ?></td>
								<?php if ($currentTheme instanceof WP_Theme && $theme === $currentTheme->get_stylesheet()): ?>
									<td class="wf-result-success">Active</td>
								<?php else: ?>
									<td class="wf-result-inactive">Inactive</td>
								<?php endif ?>
							</tr>
						<?php endforeach ?>
						</tbody>
					<?php else: ?>
						<tbody>
						<tr>
							<td colspan="<?php echo $cols ?>">No Themes</td>
						</tr>
						</tbody>

					<?php endif ?>
				</table>
			</div>
		</div>
		<div class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-wordpress-cron-jobs') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-wordpress-cron-jobs') ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Cron Jobs', 'wordfence') ?></strong>
						<span class="wf-text-small"><?php _e('List of WordPress cron jobs scheduled by WordPress, plugins, or themes.', 'wordfence') ?></span>
					</div>
					<div class="wf-block-header-action">
						<div class="wf-block-header-action-disclosure"></div>
					</div>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix wf-padding-no-left wf-padding-no-right">
				<table class="wf-striped-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
					<tbody>
					<?php
					$cron = _get_cron_array();

					foreach ($cron as $timestamp => $values) {
						if (is_array($values)) {
							foreach ($values as $cron_job => $v) {
								if (is_numeric($timestamp)) {
									?>
									<tr>
										<td colspan="<?php echo $cols - 1 ?>"><?php echo esc_html(date('r', $timestamp)) ?></td>
										<td><?php echo esc_html($cron_job) ?></td>
									</tr>
									<?php
								}
							}
						}
					}
					?>
					</tbody>
				</table>
			</div>
		</div>

		<?php
		global $wpdb;
		$wfdb = new wfDB();
		//This must be done this way because MySQL with InnoDB tables does a full regeneration of all metadata if we don't. That takes a long time with a large table count.
		$tables = $wfdb->querySelect('SELECT SQL_CALC_FOUND_ROWS TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() ORDER BY TABLE_NAME ASC LIMIT 250');
		$total = $wfdb->querySingle('SELECT FOUND_ROWS()');
		foreach ($tables as &$t) {
			$t = "'" . esc_sql($t['TABLE_NAME']) . "'";
		}
		unset($t);
		$q = $wfdb->querySelect("SHOW TABLE STATUS WHERE Name IN (" . implode(',', $tables) . ')');
		if ($q):
			$databaseCols = count($q[0]);
			?>
			<div class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-database-tables') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-database-tables') ?>">
				<div class="wf-block-header">
					<div class="wf-block-header-content">
						<div class="wf-block-title">
							<strong><?php _e('Database Tables', 'wordfence') ?></strong>
							<span class="wf-text-small"><?php _e('Database table names, sizes, timestamps, and other metadata.', 'wordfence') ?></span>
						</div>
						<div class="wf-block-header-action">
							<div class="wf-block-header-action-disclosure"></div>
						</div>
					</div>
				</div>
				<div class="wf-block-content wf-clearfix wf-padding-no-left wf-padding-no-right">
					<div style="max-width: 100%; overflow: auto; padding: 1px;">
						<table class="wf-striped-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
							<tbody class="thead thead-subhead" style="font-size: 85%">
							<?php
							$val = wfUtils::array_first($q);
							?>
							<tr>
								<?php foreach ($val as $tkey => $tval): ?>
									<th><?php echo esc_html($tkey) ?></th>
								<?php endforeach; ?>
							</tr>
							</tbody>
							<tbody style="font-size: 85%">
							<?php
							$count = 0;
							foreach ($q as $val) {
								?>
								<tr>
									<?php foreach ($val as $tkey => $tval): ?>
										<td><?php echo esc_html($tval) ?></td>
									<?php endforeach; ?>
								</tr>
								<?php
								$count++;
								if ($count >= 250) {
									?>
									<tr>
										<td colspan="<?php echo $databaseCols; ?>">and <?php echo $total - $count; ?> more</td>
									</tr>
									<?php
									break;
								}
							}
							?>
							</tbody>

						</table>
					</div>

				</div>
			</div>
		<?php endif ?>
		<div class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-log-files') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-log-files') ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Log Files', 'wordfence') ?></strong>
						<span class="wf-text-small"><?php _e('PHP error logs generated by your site, if enabled by your host.', 'wordfence') ?></span>
					</div>
					<div class="wf-block-header-action">
						<div class="wf-block-header-action-disclosure"></div>
					</div>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix wf-padding-no-left wf-padding-no-right">
				<div style="max-width: 100%; overflow: auto; padding: 1px;">
					<table class="wf-striped-table"<?php echo !empty($inEmail) ? ' border=1' : '' ?>>
						<tbody class="thead thead-subhead" style="font-size: 85%">
						<tr>
							<th>File</th>
							<th>Download</th>
						</tr>
						</tbody>
						<tbody style="font-size: 85%">
						<?php
						$errorLogs = wfErrorLogHandler::getErrorLogs();
						if (count($errorLogs) < 1): ?>
							<tr>
								<td colspan="2"><em>No log files found.</em></td>
							</tr>
						<?php else:
							foreach ($errorLogs as $log => $readable): ?>
								<tr>
									<td style="width: 100%"><?php echo esc_html($log) . ' (' . wfUtils::formatBytes(filesize($log)) . ')'; ?></td>
									<td style="white-space: nowrap; text-align: right;"><?php echo($readable ? '<a href="#" data-logfile="' . esc_html($log) . '" class="downloadLogFile" target="_blank" rel="noopener noreferrer">Download</a>' : '<em>Requires downloading from the server directly</em>'); ?></td>
								</tr>
							<?php endforeach;
						endif; ?>
						</tbody>

					</table>
				</div>
			</div>
		</div>
	</form>

	<?php if (!empty($inEmail)): ?>
		<?php phpinfo(); ?>
	<?php endif ?>

	<?php if (!empty($emailForm)): ?>
		<div class="wf-diagnostics-wrapper">
			<div id="wf-diagnostics-other-tests" class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-other-tests') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-other-tests') ?>">
				<div class="wf-block-header">
					<div class="wf-block-header-content">
						<div class="wf-block-title">
							<strong><?php _e('Other Tests', 'wordfence') ?></strong>
							<span class="wf-text-small"><?php _e('System configuration, memory test, send test email from this server.', 'wordfence') ?></span>
						</div>
						<div class="wf-block-header-action">
							<div class="wf-block-header-action-disclosure"></div>
						</div>
					</div>
				</div>
				<div class="wf-block-content wf-clearfix">
					<ul class="wf-block-list">
						<li>
							<span>
								<a href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=sysinfo&nonce=<?php echo wp_create_nonce('wp-ajax'); ?>" target="_blank" rel="noopener noreferrer">Click to view your system's configuration in a new window</a>
								<a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DIAGNOSTICS_SYSTEM_CONFIGURATION); ?>" target="_blank" rel="noopener noreferrer" class="wfhelp wf-inline-help"></a>
							</span>
						</li>
						<li>
							<span>
								<a href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=testmem&nonce=<?php echo wp_create_nonce('wp-ajax'); ?>" target="_blank" rel="noopener noreferrer">Test your WordPress host's available memory</a>
							<a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DIAGNOSTICS_TEST_MEMORY); ?>" target="_blank" rel="noopener noreferrer" class="wfhelp wf-inline-help"></a>
							</span>
						</li>
						<li>
							<span>
								Send a test email from this WordPress server to an email address:<a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DIAGNOSTICS_TEST_EMAIL); ?>" target="_blank" rel="noopener noreferrer" class="wfhelp wf-inline-help"></a>
								<input type="text" id="testEmailDest" value="" size="20" maxlength="255" class="wfConfigElem"/>
								<input class="wf-btn wf-btn-default wf-btn-sm" type="button" value="Send Test Email" onclick="WFAD.sendTestEmail(jQuery('#testEmailDest').val());"/>
							</span>
						</li>
						<li>
							<span>
								Send a test activity report email: <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DIAGNOSTICS_TEST_ACTIVITY_REPORT); ?>" target="_blank" rel="noopener noreferrer" class="wfhelp wf-inline-help"></a>
								<input type="email" id="email_summary_email_address_debug" value="" size="20" maxlength="255" class="wfConfigElem"/>
								<input class="wf-btn wf-btn-default wf-btn-sm" type="button" value="Send Test Activity Report" onclick="WFAD.ajax('wordfence_email_summary_email_address_debug', {email: jQuery('#email_summary_email_address_debug').val()});"/>
							</span>
						</li>
					</ul>

				</div>
			</div>

			<div class="wf-block<?php echo(wfPersistenceController::shared()->isActive('wf-diagnostics-debugging-options') ? ' wf-active' : '') ?>" data-persistence-key="<?php echo esc_attr('wf-diagnostics-debugging-options') ?>">
				<div class="wf-block-header">
					<div class="wf-block-header-content">
						<div class="wf-block-title">
							<strong><?php _e('Debugging Options', 'wordfence') ?></strong>
						</div>
						<div class="wf-block-header-action">
							<div class="wf-block-header-action-disclosure"></div>
						</div>
					</div>
				</div>
				<div class="wf-block-content wf-clearfix">
					<form action="#" id="wfDebuggingConfigForm">
						<ul class="wf-block-list">
							<li>
								<?php
								echo wfView::create('options/option-toggled', array(
									'optionName'    => 'debugOn',
									'enabledValue'  => 1,
									'disabledValue' => 0,
									'value'         => $w->get('debugOn') ? 1 : 0,
									'title'         => __('Enable debugging mode (increases database load)', 'wordfence'),
									'helpLink'      => wfSupportController::supportURL(wfSupportController::ITEM_DIAGNOSTICS_OPTION_DEBUGGING_MODE),
								))->render();
								?>
							</li>
							<li>
								<?php
								echo wfView::create('options/option-toggled', array(
									'optionName'    => 'startScansRemotely',
									'enabledValue'  => 1,
									'disabledValue' => 0,
									'value'         => $w->get('startScansRemotely') ? 1 : 0,
									'title'         => __('Start all scans remotely (Try this if your scans aren\'t starting and your site is publicly accessible)', 'wordfence'),
									'helpLink'      => wfSupportController::supportURL(wfSupportController::ITEM_DIAGNOSTICS_OPTION_REMOTE_SCANS),
								))->render();
								?>
							</li>
							<li>
								<?php
								echo wfView::create('options/option-toggled', array(
									'optionName'    => 'ssl_verify',
									'enabledValue'  => 1,
									'disabledValue' => 0,
									'value'         => $w->get('ssl_verify') ? 1 : 0,
									'title'         => __('Enable SSL Verification (Disable this if you are consistently unable to connect to the Wordfence servers.)', 'wordfence'),
									'helpLink'      => wfSupportController::supportURL(wfSupportController::ITEM_DIAGNOSTICS_OPTION_SSL_VERIFICATION),
								))->render();
								?>
							</li>
							<li>
								<?php
								echo wfView::create('options/option-toggled', array(
									'optionName'    => 'betaThreatDefenseFeed',
									'enabledValue'  => 1,
									'disabledValue' => 0,
									'value'         => $w->get('betaThreatDefenseFeed') ? 1 : 0,
									'title'         => __('Enable beta threat defense feed', 'wordfence'),
									'helpLink'      => wfSupportController::supportURL(wfSupportController::ITEM_DIAGNOSTICS_OPTION_BETA_TDF),
								))->render();
								?>
							</li>
							<li>
								<p>
									<a id="wf-restore-defaults" class="wf-btn wf-btn-default wf-btn-callout-subtle" href="#" data-restore-defaults-section="<?php echo esc_attr(wfConfig::OPTIONS_TYPE_DIAGNOSTICS); ?>"><?php esc_html_e('Restore Defaults', 'wordfence'); ?></a>
									<a id="wf-cancel-changes" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-disabled" href="#"><?php esc_html_e('Cancel Changes', 'wordfence'); ?></a>
									<a id="wf-save-changes" class="wf-btn wf-btn-primary wf-btn-callout-subtle wf-disabled" href="#"><?php esc_html_e('Save Changes', 'wordfence'); ?></a>
								</p>
							</li>
						</ul>

					</form>
				</div>
			</div>
		</div>

	<?php endif ?>
</div>
<div class="wf-scrollTop">
	<a href="javascript:void(0);"><i class="wf-ionicons wf-ion-chevron-up"></i></a>
</div>
<script type="text/x-jquery-template" id="wfTmpl_restoreDefaultsPrompt">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Confirm Restore Defaults', 'wordfence'),
		'message' => __('Are you sure you want to restore the default Diagnostics settings? This will undo any custom changes you have made to the options on this page.', 'wordfence'),
		'primaryButton' => array('id' => 'wf-restore-defaults-prompt-cancel', 'label' => __('Cancel', 'wordfence'), 'link' => '#'),
		'secondaryButtons' => array(array('id' => 'wf-restore-defaults-prompt-confirm', 'labelHTML' => __('Restore<span class="wf-hidden-xs"> Defaults</span>', 'wordfence'), 'link' => '#')),
	))->render();
	?>
</script>