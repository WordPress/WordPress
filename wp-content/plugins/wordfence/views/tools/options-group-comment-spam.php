<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Comment Spam Filter Options group.
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
		<div class="wf-dashboard-item active">
			<div class="wf-dashboard-item-extra">
				<ul class="wf-dashboard-item-list">
					<li>
						<ul class="wf-dashboard-item-list wf-dashboard-item-list-horizontal">
							<li>
								<strong><?php _e('Comment Spam Filter Options', 'wordfence') ?></strong>
							</li>
							<li>
								<strong><?php _e('Advanced Comment Spam Filter Options', 'wordfence') ?>
									<?php if (!wfConfig::p()): ?>
										<a href="https://www.wordfence.com/gnl1optionUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-premium-link"><?php _e('Premium Feature', 'wordfence') ?></a>
									<?php endif ?>
								</strong>
							</li>
						</ul>
					</li>
				</ul>
				<ul class="wf-dashboard-item-list">
					<li>
						<ul class="wf-dashboard-item-list wf-dashboard-item-list-horizontal">
							<li>
								<div>
									<?php
									echo wfView::create('options/option-toggled-segmented', array(
										'optionName'    => 'other_noAnonMemberComments',
										'helpLink'      => wfSupportController::supportURL(wfSupportController::ITEM_TOOLS_COMMENT_SPAM_OPTION_HOLD_ANONYMOUS),
										'enabledValue'  => 1,
										'disabledValue' => 0,
										'value'         => wfConfig::get('other_noAnonMemberComments') ? 1 : 0,
										'htmlTitle'     => __(<<<HTML
<strong>Hold anonymous comments using member emails for moderation</strong><br>Blocks when the comment is posted without being logged in, but provides an email address for the registered user. 
HTML
											, 'wordfence'),
									))->render();
									?>
									<?php
									echo wfView::create('options/option-toggled-segmented', array(
										'optionName'    => 'other_scanComments',
										'helpLink'      => wfSupportController::supportURL(wfSupportController::ITEM_TOOLS_COMMENT_SPAM_OPTION_SCAN),
										'enabledValue'  => 1,
										'disabledValue' => 0,
										'value'         => wfConfig::get('other_scanComments') ? 1 : 0,
										'htmlTitle'     => __(<<<HTML
<strong>Filter comments for malware and phishing URLs</strong><br>Blocks when a comment contains a URL on a domain blacklist. 
HTML
											, 'wordfence'),
									))->render();
									?>
								</div>
							</li>
							<li>
								<div id="wfAdvancedCommentScanningOption" style="align-self:flex-start">
									<?php
									echo wfView::create('options/option-toggled-segmented', array(
										'optionName'    => 'advancedCommentScanning',
										'helpLink'      => wfSupportController::supportURL(wfSupportController::ITEM_TOOLS_COMMENT_SPAM_OPTION_ADVANCED),
										'premium'       => !wfConfig::p(),
										'enabledValue'  => 1,
										'disabledValue' => 0,
										'value'         => wfConfig::get('advancedCommentScanning') ? 1 : 0,
										'htmlTitle'     => __(<<<HTML
<strong>Advanced Comment Spam Filter</strong><br>In addition to free comment filtering, this option filters comments against several additional real-time lists of known spammers and infected hosts.  
HTML
											, 'wordfence'),
									
									))->render();
									?>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>