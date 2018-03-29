<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Expects $title (or $titleHTML) and $stateKeys to be defined.
 *
 * @var string $title The page title.
 * @var string $titleHTML The page title as raw HTML
 * @var string[] $stateKeys An array of state keys that will be controlled by the expand/collapse all button
 */

if (isset($title) && !isset($titleHTML)) {
	$titleHTML = esc_html($title);
}

if (isset($helpLabel) && !isset($helpLabelHTML)) {
	$helpLabelHTML = esc_html($helpLabel);
}

$expanded = true;
foreach ($stateKeys as $k) {
	if (!wfPersistenceController::shared()->isActive($k)) {
		$expanded = false;
		break;
	}
}
?>
<div class="wf-section-title">
	<?php if (isset($showIcon) && $showIcon): ?>
		<div class="wordfence-lock-icon wordfence-icon32 wf-hidden-xs"></div>
	<?php endif; ?>
	<h2<?php echo (isset($headerID) ? ' id="' . $headerID . '"' : ''); ?>><?php echo $titleHTML; ?></h2>
	<div><a href="#" class="wf-toggle-all-sections wf-btn wf-btn-callout-subtle wf-btn-default" data-collapsed-title="<?php esc_attr_e('Expand All', 'wordfence'); ?>" data-expanded-title="<?php esc_attr_e('Collapse All', 'wordfence'); ?>" data-expanded="<?php echo wfUtils::truthyToInt($expanded); ?>"></a></div>
</div>
<script type="application/javascript">
	(function($) {
		$('.wf-toggle-all-sections').text($('.wf-toggle-all-sections').data('expanded') == 1 ? $('.wf-toggle-all-sections').data('expandedTitle') : $('.wf-toggle-all-sections').data('collapsedTitle'));
		
		$(function() {
			$('.wf-toggle-all-sections').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				if ($(this).data('expanded') == 1) {
					$('.wf-block[data-persistence-key]').each(function() {
						var block = $(this);
						if (block.hasClass('wf-active')) {
							block.find('.wf-block-content').slideUp({
								always: function() {
									block.removeClass('wf-active');
								}
							});
						}
					});

					WFAD.ajax('wordfence_saveDisclosureState', {names: <?php echo json_encode($stateKeys) ?>, state: false}, function() {}, function() {}, true);
					
					$(this).data('expanded', 0);
					$('.wf-toggle-all-sections').text($('.wf-toggle-all-sections').data('collapsedTitle'));
				}
				else {
					$('.wf-block[data-persistence-key]').each(function() {
						var block = $(this);
						if (!block.hasClass('wf-active')) {
							block.find('.wf-block-content').slideDown({
								always: function() {
									block.addClass('wf-active');
								}
							});
						}
					});

					WFAD.ajax('wordfence_saveDisclosureState', {names: <?php echo json_encode($stateKeys) ?>, state: true}, function() {}, function() {}, true);
					
					$(this).data('expanded', 1);
					$('.wf-toggle-all-sections').text($('.wf-toggle-all-sections').data('expandedTitle'));
				}
			});
		});
	})(jQuery);
</script> 