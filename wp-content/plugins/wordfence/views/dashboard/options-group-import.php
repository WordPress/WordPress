<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Import/Export Options group.
 *
 * Expects $stateKey.
 *
 * @var string $stateKey The key under which the collapse state is stored.
 * @var bool $collapseable If defined, specifies whether or not this grouping can be collapsed. Defaults to true.
 */

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
						<strong><?php _e('Import/Export Options', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<ul id="wf-option-exportOptions" class="wf-flex-horizontal wf-flex-vertical-xs wf-flex-full-width wf-add-top wf-add-bottom">
							<li><?php _e('Export this site\'s Wordfence options for import on another site', 'wordfence'); ?> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_EXPORT); ?>"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a></li>
							<li class="wf-right wf-left-xs wf-padding-add-top-xs-small">
								<a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle" id="wf-export-options"><?php _e('Export<span class="wf-hidden-xs"> Wordfence</span> Options', 'wordfence'); ?></a>
							</li>
						</ul>
					</li>
					<li>
						<ul id="wf-option-importOptions" class="wf-flex-vertical wf-flex-full-width wf-add-bottom">
							<li>
								<ul class="wf-option wf-option-text">
									<li class="wf-option-content">
										<ul>
											<li class="wf-option-title">
												<?php _e('Import Wordfence options from another site using a token', 'wordfence'); ?> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_IMPORT); ?>"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>
											</li>
											<li class="wf-option-text wf-option-full-width wf-no-right">
												<input type="text" value="" id="wf-import-token">
											</li>
										</ul>
									</li>
								</ul>
							</li>
							<li>
								<ul class="wf-flex-horizontal wf-flex-full-width">
									<li class="wf-right wf-left-xs" id="wf-license-controls">
										<a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle wf-disabled" id="wf-import-options"><?php _e('Import<span class="wf-hidden-xs"> Wordfence</span> Options', 'wordfence'); ?></a>
									</li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
				<script type="application/javascript">
					(function($) {
						$(function() {
							$('#wf-export-options').on('click', function(e) {
								e.preventDefault();
								e.stopPropagation();

								WFAD.ajax('wordfence_exportSettings', {}, function(res) {
									if (res.ok && res.token) {
										var prompt = $('#wfTmpl_exportPromptSuccess').tmpl(res);
										var promptHTML = $("<div />").append(prompt).html();
										WFAD.colorboxHTML((WFAD.isSmallScreen ? '300px' : '400px'), promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
											$('#wf-export-prompt-close').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.colorboxClose();
											});
										}});
									}
									else {
										var prompt = $('#wfTmpl_exportPromptError').tmpl({err: res.err || 'An unknown error occurred during the export. We received an undefined error from your web server.'});
										var promptHTML = $("<div />").append(prompt).html();
										WFAD.colorboxHTML((WFAD.isSmallScreen ? '300px' : '400px'), promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
											$('#wf-export-prompt-close').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.colorboxClose();
											});
										}});
									}
								});
							});

							$('#wf-import-token').on('keyup', function() {
								$('#wf-import-options').toggleClass('wf-disabled', $(this).val() == '');
							});

							$('#wf-import-options').on('click', function(e) {
								e.preventDefault();
								e.stopPropagation();

								WFAD.ajax('wordfence_importSettings', {token: $('#wf-import-token').val()}, function(res) {
									if (res.ok) {
										var prompt = $('#wfTmpl_importPromptSuccess').tmpl(res);
										var promptHTML = $("<div />").append(prompt).html();
										WFAD.colorboxHTML((WFAD.isSmallScreen ? '300px' : '400px'), promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
											$('#wf-import-prompt-reload').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												window.location.reload(true);
											});
										}});
									}
									else {
										var prompt = $('#wfTmpl_importPromptError').tmpl({err: res.err || 'An unknown error occurred during the import.'});
										var promptHTML = $("<div />").append(prompt).html();
										WFAD.colorboxHTML((WFAD.isSmallScreen ? '300px' : '400px'), promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
											$('#wf-import-prompt-close').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.colorboxClose();
											});
										}});
									}
								});
							});
						});
					})(jQuery);
				</script>
			</div>
		</div>
	</div>
</div> <!-- end import options -->
<script type="text/x-jquery-template" id="wfTmpl_exportPromptSuccess">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Export Successful', 'wordfence'),
		'messageHTML' => '<p>' . __('We successfully exported your site options. To import your site options on another site, copy and paste the token below into the import text box on the destination site. Keep this token secret &mdash; it is like a password. If anyone else discovers the token it will allow them to import your options excluding your license.', 'wordfence') . '</p><p><input type="text" class="wf-full-width" value="${token}" onclick="this.select();" /></p>',
		'primaryButton' => array('id' => 'wf-export-prompt-close', 'label' => __('Close', 'wordfence'), 'link' => '#'),
	))->render();
	?>
</script>

<script type="text/x-jquery-template" id="wfTmpl_exportPromptError">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Error during Export', 'wordfence'),
		'message' => '${err}',
		'primaryButton' => array('id' => 'wf-export-prompt-close', 'label' => __('Close', 'wordfence'), 'link' => '#'),
	))->render();
	?>
</script>

<script type="text/x-jquery-template" id="wfTmpl_importPromptSuccess">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Import Successful', 'wordfence'),
		'messageHTML' => __('We successfully imported the site options.', 'wordfence'),
		'primaryButton' => array('id' => 'wf-import-prompt-reload', 'label' => __('Reload', 'wordfence'), 'link' => '#'),
	))->render();
	?>
</script>

<script type="text/x-jquery-template" id="wfTmpl_importPromptError">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Error during Import', 'wordfence'),
		'message' => '${err}',
		'primaryButton' => array('id' => 'wf-import-prompt-close', 'label' => __('Close', 'wordfence'), 'link' => '#'),
	))->render();
	?>
</script>