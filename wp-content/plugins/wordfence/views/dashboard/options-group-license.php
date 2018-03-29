<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the License group.
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
						<strong><?php _e('Wordfence License', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<li>
						<ul class="wf-flex-vertical wf-flex-full-width wf-add-top wf-add-bottom">
							<li><strong><?php _e('Your Wordfence License', 'wordfence'); ?></strong></li>
							<li>
								<ul id="wf-option-apiKey" class="wf-option wf-option-text" data-text-option="apiKey" data-original-text-value="<?php echo esc_attr(wfConfig::get('apiKey')); ?>">
									<li class="wf-option-title">
										<?php _e('License Key', 'wordfence'); ?> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_DASHBOARD_OPTION_API_KEY); ?>"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>
									</li>
									<li class="wf-option-text wf-option-full-width wf-no-right">
										<input type="text" value="<?php echo esc_attr(wfConfig::get('apiKey')); ?>" id="wf-license-input">
									</li>
								</ul>
							</li>
							<li>
								<ul class="wf-flex-horizontal wf-flex-vertical-xs wf-flex-full-width">
									<li><strong><?php _e('License Status:', 'wordfence'); ?></strong>
										<?php
										if (wfConfig::get('hasKeyConflict')) {
											_e('Premium License already in use', 'wordfence');
										}
										else if (wfConfig::get('isPaid')) {
											_e('Premium License Active', 'wordfence');
										}
										else if (wfConfig::get('keyType') == wfAPI::KEY_TYPE_PAID_EXPIRED) {
											_e('Premium License Expired', 'wordfence');
										}
										else {
											_e('Free License Active', 'wordfence');
										}
										?>
									</li>
									<li class="wf-right wf-flex-vertical-xs wf-flex-align-left wf-left-xs wf-padding-add-top-xs" id="wf-license-controls">
										<?php if (wfConfig::get('hasKeyConflict')): ?>
											<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-downgrade-license"><?php _e('Downgrade to a free license', 'wordfence'); ?></a>&nbsp;&nbsp;<a href="https://www.wordfence.com/gnl1optMngKysReset/manage-wordfence-api-keys/" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-primary wf-btn-callout-subtle"><?php _e('Reset Premium License', 'wordfence'); ?></a>
										<?php elseif (wfConfig::get('keyExpDays') < 30 && wfConfig::get('premiumAutoRenew', null) === '0'): ?>
											<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-downgrade-license"><?php _e('Downgrade to a free license', 'wordfence'); ?></a>&nbsp;&nbsp;<a href="https://www.wordfence.com/gnl1optMngKysExpiring/manage-wordfence-api-keys/" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-primary wf-btn-callout-subtle"><?php _e('Renew Premium License', 'wordfence'); ?></a>
										<?php elseif (wfConfig::get('keyExpDays') < 30 && (wfConfig::get('premiumPaymentExpiring') || wfConfig::get('premiumPaymentExpired') || wfConfig::get('premiumPaymentMissing') || wfConfig::get('premiumPaymentHold'))): ?>
											<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-downgrade-license"><?php _e('Downgrade to a free license', 'wordfence'); ?></a>&nbsp;&nbsp;<a href="https://www.wordfence.com/gnl1optMngKysExpiring/manage-wordfence-api-keys/" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-primary wf-btn-callout-subtle"><?php _e('Renew Premium License', 'wordfence'); ?></a>
										<?php elseif (wfConfig::get('isPaid')): ?>
											<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-downgrade-license"><?php _e('Downgrade to a free license', 'wordfence'); ?></a>&nbsp;&nbsp;<a href="https://www.wordfence.com/gnl1optMngKysReset/manage-wordfence-api-keys/" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-default wf-btn-callout-subtle"><?php _e('Renew Premium License', 'wordfence'); ?></a>
										<?php else: ?>
											<a href="https://www.wordfence.com/gnl1optUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-primary wf-btn-callout-subtle"><?php _e('Upgrade to Premium', 'wordfence'); ?></a>
										<?php endif ?>
										<a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle" style="display: none;" id="wf-install-license"><?php _e('Install License', 'wordfence'); ?></a>
									</li>
								</ul>
								
								<script type="application/javascript">
									(function($) {
										$(function() {
											$('#wf-install-license').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												WFAD.setOption('apiKey', $('#wf-license-input').val(), function() {
													delete WFAD.pendingChanges['apiKey'];
													WFAD.updatePendingChanges();
													window.location.reload(true);
												});
											});

											$('#wf-license-input').on('focus', function() {
												var field = $(this);
												setTimeout(function() {
													field.select();
												}, 100);
											}).on('change paste keyup', function() {
												setTimeout(function() {
													var originalKey = $('#wf-license-input').closest('.wf-option').data('originalTextValue');
													if (originalKey != $('#wf-license-input').val()) {
														$('#wf-license-controls a').hide();
														$('#wf-install-license').show();
													}
												}, 100);
											});

											$(window).on('wfOptionsReset', function() {
												$('#wf-license-controls a').show();
												$('#wf-install-license').hide();
											});

											$('.wf-downgrade-license').on('click', function(e) {
												e.preventDefault();
												e.stopPropagation();

												var prompt = $('#wfTmpl_downgradePrompt').tmpl();
												var promptHTML = $("<div />").append(prompt).html();
												WFAD.colorboxHTML('400px', promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
													$('#wf-downgrade-prompt-cancel').on('click', function(e) {
														e.preventDefault();
														e.stopPropagation();

														WFAD.colorboxClose();
													});

													$('#wf-downgrade-prompt-downgrade').on('click', function(e) {
														e.preventDefault();
														e.stopPropagation();

														WFAD.ajax('wordfence_downgradeLicense', {}, function(res) {
															window.location.reload(true);
														});
													});
												}});
											});
										});
									})(jQuery);
								</script>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end license options -->
<script type="text/x-jquery-template" id="wfTmpl_downgradePrompt">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Confirm Downgrade', 'wordfence'),
		'message' => __('Are you sure you want to downgrade your Wordfence Premium License? This will disable all Premium features and return you to the free version of Wordfence.', 'wordfence'),
		'primaryButton' => array('id' => 'wf-downgrade-prompt-cancel', 'label' => __('Cancel', 'wordfence'), 'link' => '#'),
		'secondaryButtons' => array(array('id' => 'wf-downgrade-prompt-downgrade', 'label' => __('Downgrade', 'wordfence'), 'link' => '#')),
	))->render();
	?>
</script>