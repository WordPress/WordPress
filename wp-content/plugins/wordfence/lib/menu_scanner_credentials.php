<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
$scanner = wfScanner::shared();
$scanURL = network_admin_url('admin.php?page=WordfenceScan');

$action = @$_GET['action'];
if (!in_array($action, array('restoreFile', 'deleteFile'))) { $action = ''; }
$filesystemCredentialsAdminURL = network_admin_url('admin.php?' . http_build_query(array(
		'page'               => 'WordfenceScan',
		'subpage'       	 => 'scan_credentials',
		'action' 			 => $action,
		'issueID'            => (int) @$_GET['issueID'],
		'nonce'              => wp_create_nonce('wp-ajax'),
	)));

switch ($action) {
	case 'restoreFile':
		$callback = array('wordfence', 'fsActionRestoreFileCallback');
		break;
	case 'deleteFile':
		$callback = array('wordfence', 'fsActionDeleteFileCallback');
		break;
}
?>
<div class="wf-options-controls">
	<div class="wf-row">
		<div class="wf-col-xs-12">
			<?php
			echo wfView::create('options/block-controls', array(
				'backLink' => $scanURL,
				'backLabel' => __('Back to Scan', 'wordfence'),
				'suppressControls' => true,
			))->render();
			?>
		</div>
	</div>
</div>
<div class="wf-options-controls-spacer"></div>
<div class="wrap wordfence">
	<div class="wf-container-fluid">
		<div class="wf-row">
			<div class="wf-col-xs-12">
				<div class="wp-header-end"></div>
			</div>
		</div>
		<div class="wf-row">
			<div class="<?php echo wfStyle::contentClasses(); ?>">
				<div id="wf-scan-permissions-prompt" class="wf-fixed-tab-content">
					<?php
					echo wfView::create('common/section-title', array(
						'title' => __('File System Credentials Required', 'wordfence'),
					))->render();
					?>
					<div class="wf-row">
						<div class="wf-col-xs-12">
							<div class="wf-block wf-active">
								<div class="wf-block-content wf-padding-add-top wf-padding-add-bottom">
									<?php
									if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'wp-ajax')) {
										if (wordfence::requestFilesystemCredentials($filesystemCredentialsAdminURL, get_home_path(), true, true)) {
											call_user_func_array($callback, isset($callbackArgs) && is_array($callbackArgs) ? $callbackArgs : array());
										}
										//else - outputs credentials form
									}
									else {
										echo '<p>' . sprintf(__('Security token has expired. Click <a href="%s">here</a> to return to the scan page.', 'wordfence'), esc_url($scanURL)) . '</p>';
									}
									?>
								</div>
							</div>
						</div>
					</div> <!-- end permissions -->
				</div> <!-- end wf-scan-permissions-prompt block -->
			</div> <!-- end content block -->
		</div> <!-- end row -->
	</div> <!-- end container -->
</div>
