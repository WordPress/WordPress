<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the scan results.
 *
 * Expects $scanner and $issues.
 *
 * @var wfScanner $scanner The scanner state.
 * @var wfIssues $issues The issues state.
 */

$hasDeleteableIssue = !!$issues->getDeleteableIssueCount();
$hasRepairableIssue = !!$issues->getFixableIssueCount();

$tabs = array(
  new wfTab('new', 'new', __('Results<span class="wf-hidden-xs"> Found</span>', 'wordfence'), ''),
  new wfTab('ignored', 'ignored', __('Ignored<span class="wf-hidden-xs"> Results</span>', 'wordfence'), ''),
)
?>
<ul class="wf-scan-tabs">
  <?php foreach ($tabs as $index => $t): ?> 
	<?php
	$a = $t->a;
	if (!preg_match('/^https?:\/\//i', $a)) {
	  $a = '#' . urlencode($a);
	}
	?>
	<li class="wf-tab<?php if ($index == 0) { echo ' wf-active'; } ?>" id="wf-scan-tab-<?php echo esc_attr($t->id); ?>" data-target="<?php echo esc_attr($t->id); ?>" data-tab-title="<?php echo esc_attr($t->tabTitle); ?>"><a href="<?php echo esc_attr($a); ?>"><?php echo $t->tabTitle; ?></a></li>
  <?php endforeach; ?>
	<li id="wf-scan-bulk-buttons"><span class="wf-hidden-xs"><a href="#" id="wf-scan-bulk-buttons-delete" class="wf-btn wf-btn-default wf-btn-callout-subtle<?php echo ($hasDeleteableIssue ? '' : ' wf-disabled'); ?>"><?php _e('Delete All Deletable Files', 'wordfence'); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" id="wf-scan-bulk-buttons-repair" class="wf-btn wf-btn-default wf-btn-callout-subtle<?php echo ($hasRepairableIssue ? '' : ' wf-disabled'); ?>"><?php _e('Repair All Repairable Files', 'wordfence'); ?></a></span></li>
</ul>
<ul class="wf-scan-results">
	<li class="wf-scan-results-stats">
		<div class="wf-block wf-active">
			<div class="wf-block-content">
				<ul class="wf-block-list wf-block-list-horizontal wf-block-list-horizontal-5 wf-block-list-equal wf-hidden-xs">
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('Posts, Comments, &amp; Files', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-postscommentsfiles"><?php echo $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_POSTS, 0) + $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_COMMENTS, 0) + $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_FILES, 0); ?></li>
						</ul>
					</li>
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('Themes &amp; Plugins', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-themesplugins"><?php echo $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_PLUGINS, 0) + $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_THEMES, 0); ?></li>
						</ul>
					</li>
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('Users Checked', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-users"><?php echo $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_USERS, 0); ?></li>
						</ul>
					</li>
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('URLs Checked', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-urls"><?php echo $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_URLS, 0); ?></li>
						</ul>
					</li>
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('Results Found', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-issues"><?php echo $issues->getIssueCount(); ?></li>
						</ul>
					</li>
				</ul>
				<ul class="wf-block-list wf-hidden-sm wf-hidden-md wf-hidden-lg">
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('Posts, Comments, &amp; Files', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-postscommentsfiles"><?php echo $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_POSTS, 0) + $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_COMMENTS, 0) + $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_FILES, 0); ?></li>
						</ul>
					</li>
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('Themes &amp; Plugins', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-themesplugins"><?php echo $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_PLUGINS, 0) + $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_THEMES, 0); ?></li>
						</ul>
					</li>
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('Users Checked', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-users"><?php echo $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_USERS, 0); ?></li>
						</ul>
					</li>
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('URLs Checked', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-urls"><?php echo $scanner->getSummaryItem(wfScanner::SUMMARY_SCANNED_URLS, 0); ?></li>
						</ul>
					</li>
					<li>
						<ul class="wf-flex-horizontal wf-flex-full-width">
							<li><?php _e('Results Found', 'wordfence'); ?></li>
							<li class="wf-scan-results-stats-issues"><?php echo $issues->getIssueCount(); ?></li>
						</ul>
					</li>
				</ul>
			</div>
	</li>
	<li class="wf-scan-results-issues wf-active" id="wf-scan-results-new" data-issue-type="new"></li>
	<li class="wf-scan-results-issues" id="wf-scan-results-ignored" data-issue-type="ignored"></li>
</ul>
<script type="application/javascript">
	(function($) {
		WFAD.scanIssuesOffset = 0;
		
		var issuesWrapper = $('.wf-scan-results');
		var hasScrolled = false;
		$(window).on('scroll', function() {
			var win = $(this);
			// console.log(win.scrollTop() + window.innerHeight, liveTrafficWrapper.outerHeight() + liveTrafficWrapper.offset().top);
			var currentScrollBottom = win.scrollTop() + window.innerHeight;
			var scrollThreshold = issuesWrapper.outerHeight() + issuesWrapper.offset().top;
			if (hasScrolled && !WFAD.loadingIssues && currentScrollBottom >= scrollThreshold) {
				// console.log('infinite scroll');

				WFAD.loadingIssues = true;
				hasScrolled = false;
				WFAD.loadMoreIssues(function() {
					WFAD.loadingIssues = false;
				}, WFAD.scanIssuesOffset, null, WFAD.scanIssuesIgnoredOffset);
			}
			else if (currentScrollBottom < scrollThreshold) {
				hasScrolled = true;
				// console.log('no infinite scroll');
			}
		});
		
		$(function() {
			$('.wf-scan-tabs .wf-tab a').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				$('.wf-scan-tabs').find('.wf-tab').removeClass('wf-active');
				$('.wf-scan-results-issues').removeClass('wf-active');

				var tab = $(this).closest('.wf-tab');
				tab.addClass('wf-active');
				var content = $('#wf-scan-results-' + tab.data('target'));
				content.addClass('wf-active');
			});
			
			$('#wf-scan-bulk-buttons-delete').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var prompt = $('#wfTmpl_scannerDelete').tmpl();
				var promptHTML = $("<div />").append(prompt).html();
				WFAD.colorboxHTML((WFAD.isSmallScreen ? '300px' : '700px'), promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
					$('#wf-scanner-prompt-cancel').on('click', function(e) { 
						e.preventDefault();
						e.stopPropagation();

						WFAD.colorboxClose();
					});

					$('#wf-scanner-prompt-confirm').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFAD.bulkOperationConfirmed('del');
					});
				}});
			});

			$('#wf-scan-bulk-buttons-repair').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var prompt = $('#wfTmpl_scannerRepair').tmpl();
				var promptHTML = $("<div />").append(prompt).html();
				WFAD.colorboxHTML((WFAD.isSmallScreen ? '300px' : '700px'), promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
					$('#wf-scanner-prompt-cancel').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFAD.colorboxClose();
					});

					$('#wf-scanner-prompt-confirm').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFAD.bulkOperationConfirmed('repair');
					});
				}});
			});
		});
	})(jQuery);
</script>