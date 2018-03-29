<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
if (wfOnboardingController::shouldShowAttempt3()) {
	echo wfView::create('onboarding/banner')->render();
}

$support = @json_decode(wfConfig::get('supportContent'), true);
?>
	<div class="wrap wordfence">
		<div class="wf-container-fluid">
			<div class="wf-row">
				<div class="wf-col-xs-12">
					<div class="wp-header-end"></div>
					<?php
					echo wfView::create('common/section-title', array(
						'title' => __('Help', 'wordfence'),
						'showIcon' => true,
					))->render();
					?>
				</div>
				<div class="wf-col-xs-12">
					<div class="wf-block wf-active">
						<div class="wf-block-content">
							<ul class="wf-block-list">
								<li>
									<ul class="wf-block-list wf-block-list-horizontal">
										<li class="wf-flex-vertical">
											<h3><?php _e('Free Support', 'wordfence'); ?></h3>
											<p class="wf-center"><?php _e('Support for free customers is available via our forums page on wordpress.org. The majority of requests <strong>receive an answer within a few days.</strong>', 'wordfence'); ?></p>
											<p><a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_FREE); ?>" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-default wf-btn-callout-subtle"><?php _e('Go to Support Forums', 'wordfence'); ?></a></p>
										</li>
										<li class="wf-flex-vertical">
										<?php if (wfConfig::get('isPaid')): ?>
											<h3><?php _e('Premium Support', 'wordfence'); ?></h3>
											<p class="wf-center"><?php _e('Our senior support engineers <strong>respond to Premium tickets within a few hours</strong> on average and have a direct line to our QA and development teams.', 'wordfence'); ?></p>
											<p><a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_PREMIUM); ?>" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-primary wf-btn-callout-subtle"><?php _e('Go to Premium Support', 'wordfence'); ?></a></p>
										<?php else: ?>
											<h3><?php _e('Upgrade Now to Access Premium Support', 'wordfence'); ?></h3>
											<p class="wf-center"><?php _e('Our senior support engineers <strong>respond to Premium tickets within a few hours</strong> on average and have a direct line to our QA and development teams.', 'wordfence'); ?></p>
											<p><a href="https://www.wordfence.com/gnl1supportUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-primary wf-btn-callout-subtle"><?php _e('Upgrade to Premium', 'wordfence'); ?></a></p>
										<?php endif; ?>
										</li>
									</ul>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<?php if (isset($support['all'])): ?>
			<div class="wf-row">
				<div class="wf-col-xs-12 wf-col-sm-9 wf-col-sm-half-padding-right wf-add-top">
					<h3 class="wf-no-top"><?php _e('All Documentation', 'wordfence'); ?></h3>
				</div>
			</div>
			<div class="wf-row">
				<div class="wf-col-xs-12 wf-col-sm-3 wf-col-sm-push-9 wf-col-sm-half-padding-left"> 
					<div class="wf-block wf-active">
						<div class="wf-block-content">
							<div class="wf-support-top-block">
								<h4><?php _e('Top Topics and Questions', 'wordfence'); ?></h4> 
								<ol>
								<?php
								if (isset($support['top'])):
									foreach ($support['top'] as $entry):
								?>
									<li><a href="<?php echo esc_url($entry['permalink']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($entry['title']); ?></a></li>
								<?php
									endforeach;
								endif;
								?>
								</ol>
							</div>
						</div>
					</div>
				</div>
				<div class="wf-col-xs-12 wf-col-sm-9 wf-col-sm-pull-3 wf-col-sm-half-padding-right">
				<?php
				if (isset($support['all'])):
					foreach ($support['all'] as $entry):
				?>
					<div class="wf-block wf-active wf-add-bottom">
						<div class="wf-block-content">
							<div class="wf-support-block">
								<h4><a href="<?php echo esc_url($entry['permalink']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($entry['title']); ?></a></h4>
								<p><?php echo esc_html($entry['excerpt']); ?></p>
								<?php if (isset($entry['children'])): ?>
								<ul>
								<?php foreach ($entry['children'] as $child): ?>
									<li><a href="<?php echo esc_url($child['permalink']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($child['title']); ?></a></li>
								<?php endforeach; ?>
								</ul>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php
					endforeach;
				endif;
				?>
				</div>
			</div>
			<?php else: ?>
			<div class="wf-row">
				<div class="wf-col-xs-12">
					<div class="wf-block wf-active">
						<div class="wf-block-content">
							<div class="wf-support-missing-block">
								<h4><?php _e('Documentation', 'wordfence'); ?></h4>
								<p><?php _e('Documentation about Wordfence may be found on our website by clicking the button below or by clicking the <i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i> links on any of the plugin\'s pages.', 'wordfence'); ?></p>
								<p class="wf-no-bottom"><a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_INDEX); ?>" target="_blank" rel="noopener noreferrer" class="wf-btn wf-btn-default wf-btn-callout-subtle"><?php _e('View Documentation', 'wordfence'); ?></a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div> <!-- end container -->
	</div>
	