<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the blocking status.
 *
 */
?>
<ul class="wf-block-list wf-block-list-horizontal wf-blocking-status<?php echo (wfConfig::get('isPaid') ? ' wf-blocking-status-premium' : ''); ?>">
	<li>
		<?php
		echo wfView::create('common/block-navigation-option', array(
			'id' => 'blocking-all-options',
			'img' => 'options.svg',
			'title' => __('Blocking Options', 'wordfence'),
			'subtitle' => __('Manage global blocking options.', 'wordfence'),
			'link' => network_admin_url('admin.php?page=WordfenceWAF&subpage=blocking_options'),
		))->render();
		?>
	</li>
<?php if (!wfConfig::get('isPaid')): ?>
	<li class="wf-flex-horizontal wf-flex-full-width">
		<div class="wf-flex-vertical wf-flex-align-left">
			<h4 class="wf-no-bottom">Upgrade to Premium</h4>
			<p class="wf-add-top-smaller">Enable country blocking by upgrading to Premium.</p>
		</div>
		<div class="wf-flex-horizontal wf-flex-full-width">
			<p class="wf-right"><a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="https://www.wordfence.com/gnl1blockingUpgrade/wordfence-signup/#premium-order-form" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a>&nbsp;&nbsp;<a class="wf-btn wf-btn-callout-subtle wf-btn-default" href="https://www.wordfence.com/gnl1blockingLearn/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></p>
		</div>
	</li>
<?php endif; ?>
</ul>
