<?php
if (!defined('WORDFENCE_VERSION')) { exit; }

global $wpdb;

$table_wfBlockedCommentLog = wfDB::networkTable('wfBlockedCommentLog');
$blockedToday = (int) $wpdb->get_var("SELECT SUM(blockCount) 
FROM {$table_wfBlockedCommentLog}
WHERE unixday >= FLOOR(UNIX_TIMESTAMP() / 86400)");

$blockedThisWeek = (int) $wpdb->get_var("SELECT SUM(blockCount) 
FROM {$table_wfBlockedCommentLog}
WHERE unixday >= FLOOR(UNIX_TIMESTAMP() / 86400) - 7");

$blockedThisMonth = (int) $wpdb->get_var("SELECT SUM(blockCount)
FROM {$table_wfBlockedCommentLog}
WHERE unixday >= FLOOR(UNIX_TIMESTAMP() / 86400) - 31");

?>
<script type="application/javascript">
	(function($) {
		$(function() {
			document.title = "<?php esc_attr_e('Comment Spam Filter', 'wordfence'); ?>" + " \u2039 " + WFAD.basePageName;
		});
	})(jQuery);
</script>
<div id="wf-tools-commentspam">
	<div class="wf-section-title">
		<h2><?php _e('Comment Spam Filter', 'wordfence') ?></h2>
		<span><?php printf(__('<a href="%s" target="_blank" rel="noopener noreferrer" class="wf-help-link">Learn more<span class="wf-hidden-xs"> about the Comment Spam Filter</span></a>', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_TOOLS_COMMENT_SPAM)); ?>
			<i class="wf-fa wf-fa-external-link" aria-hidden="true"></i></span>
	</div>

	<p><?php _e("Wordfence reduces spam that is known to slip through traditional filters by using advanced heuristics to identify spam comments and aggregate data to identify spammers.", 'wordfence') ?></p>

	<div class="wf-row">
		<div class="wf-col-xs-12">
			<?php
			echo wfView::create('options/block-controls', array(
				'suppressLogo' => true,
				'restoreDefaultsSection' => wfConfig::OPTIONS_TYPE_COMMENT_SPAM,
				'restoreDefaultsMessage' => __('Are you sure you want to restore the default Comment Spam settings? This will undo any custom changes you have made to the options on this page.', 'wordfence'),
			))->render();
			?>
		</div>
	</div>
	<?php
	echo wfView::create('tools/options-group-comment-spam', array(
		'stateKey' => 'wf-comment-spam-options',
		'collapseable' => false,
	))->render();
	?>

	<div class="wf-row">

		<div class="wf-col-xs-12">
			<div class="wf-dashboard-item active">
				<div class="wf-dashboard-item-inner">
					<div class="wf-dashboard-item-content">
						<div class="wf-dashboard-item-title">
							<strong><?php _e('Spam Comments Blocked', 'wordfence') ?></strong>
						</div>
					</div>
				</div>
				<div class="wf-dashboard-item-extra">
					<ul class="wf-dashboard-item-list">
						<li>
							<ul class="wf-dashboard-item-list wf-dashboard-item-list-horizontal">
								<li>
									<div class="wf-dashboard-item-labeled-count">
										<div class="wf-dashboard-item-labeled-count-count"><?php echo $blockedToday ?></div>
										<div class="wf-dashboard-item-labeled-count-label">Blocked Today</div>
									</div>
								</li>
								<li>
									<div class="wf-dashboard-item-labeled-count">
										<div class="wf-dashboard-item-labeled-count-count"><?php echo $blockedThisWeek ?></div>
										<div class="wf-dashboard-item-labeled-count-label">Blocked This Week</div>
									</div>
								</li>
								<li>
									<div class="wf-dashboard-item-labeled-count">
										<div class="wf-dashboard-item-labeled-count-count"><?php echo $blockedThisMonth ?></div>
										<div class="wf-dashboard-item-labeled-count-label">Blocked This Month</div>
									</div>
								</li>
							</ul>
						</li>
					</ul>
					<ul class="wf-dashboard-item-list">
						<li>
							<div class="wf-center">
								<?php if (!wfConfig::p()): ?>
									<p>The Wordfence Advanced Comment Spam Filter is automatically enabled for Premium customers, providing an additional layer of filtering. The advanced filter does an additional check on the source IP of inbound comments and any URLs that are included.</p>
									<p><a class="wf-btn wf-btn-primary" href="https://www.wordfence.com/zz11/wordfence-signup/" target="_blank" rel="noopener noreferrer">Upgrade To Premium</a></p>
								<?php else: ?>
									<p><a class="wf-btn wf-btn-primary" href="https://www.wordfence.com/zz10/sign-in/" target="_blank" rel="noopener noreferrer">Protect More Sites</a></p>
								<?php endif ?>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
