<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the top controls on an options page.
 *
 * Expects $backLink and $backLabel (or $backLabelHTML) or alternately just $backLinkBreadcrumbs. $suppressControls may be defined to hide the options-saving controls. If not truthy, $restoreDefaultsSection and $restoreDefaultsMessage must also be defined.
 *
 * @var string $backLink The link for the back button.
 * @var string $backLabel The label for the back button.
 * @var string $backLabelHTML The label for the back button as raw HTML.
 * @var wfPage[] $backLinkBreadcrumbs An array of wfPage instances of the page's breadcrumbs. The last entry in the array is expected to be a page and will not generate a link.
 * @var string $restoreDefaultsMessage The message shown in the restore defaults prompt
 */

if (isset($backLabel) && !isset($backLabelHTML)) {
	$backLabelHTML = esc_html($backLabel);
}
?>
<div class="wf-block wf-block-transparent wf-active">
	<div class="wf-block-content">
		<ul class="wf-block-left-right wf-hidden-xs">
			<li class="wf-left">
			<?php if (isset($backLinkBreadcrumbs)): ?>
				<?php foreach ($backLinkBreadcrumbs as $index => $page): ?>
					<a href="<?php echo esc_attr($page->url()); ?>" class="wf-back-link-chevron"><i class="wf-ion-chevron-left wf-back-icon" aria-hidden="true"></i></a>
					<?php if ($index < count($backLinkBreadcrumbs) - 1): ?><a href="<?php echo esc_attr($page->url()); ?>" class="wf-back-link"><?php endif; ?><?php echo esc_html($page->label()); ?><?php if ($index < count($backLinkBreadcrumbs) - 1): ?></a><?php endif; ?>
				<?php endforeach; ?>
			<?php else: ?>
				<?php if (!empty($backLink)): ?>
					<a href="<?php echo esc_attr($backLink); ?>" class="wf-back-link-chevron"><i class="wf-ion-chevron-left wf-back-icon" aria-hidden="true"></i></a>
					<a href="<?php echo esc_attr($backLink); ?>" class="wf-back-link"><?php echo $backLabelHTML; ?></a>
				<?php endif; ?>
			<?php endif; ?>
			</li>
			<?php if (!isset($suppressControls) || !$suppressControls): ?>
			<li class="wf-right">
				<a id="wf-restore-defaults" class="wf-btn wf-btn-default wf-btn-callout-subtle" href="#" data-restore-defaults-section="<?php echo esc_attr($restoreDefaultsSection); ?>"><?php _e('<span class="wf-hidden-xs">Restore </span>Defaults', 'wordfence'); ?></a>&nbsp;&nbsp;<a id="wf-cancel-changes" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-disabled" href="#"><?php _e('Cancel<span class="wf-hidden-xs wf-hidden-sm"> Changes</span>', 'wordfence'); ?></a>&nbsp;&nbsp;<a id="wf-save-changes" class="wf-btn wf-btn-primary wf-btn-callout-subtle wf-disabled" href="#"><?php _e('Save<span class="wf-hidden-xs wf-hidden-sm"> Changes</span>', 'wordfence'); ?></a>
			</li>
			<?php endif; ?>
		</ul>
		<ul class="wf-block-left-center-right wf-hidden-sm wf-hidden-md wf-hidden-lg">
			<li class="wf-left">
				<?php if (!empty($backLink)): ?>
					<a href="<?php echo esc_attr($backLink); ?>" class="wf-back-link-chevron"><i class="wf-ion-chevron-left wf-back-icon" aria-hidden="true"></i></a>
					<a href="<?php echo esc_attr($backLink); ?>" class="wf-back-link"><?php echo $backLabelHTML; ?></a>
				<?php endif ?>
			</li>
			<li class="wf-center">
				<?php if (!isset($suppressLogo) || !$suppressLogo): ?>
				<div class="wordfence-lock-icon wordfence-icon32"></div>
				<?php endif; ?>
			</li>
			<?php if (!isset($suppressControls) || !$suppressControls): ?>
			<li class="wf-right">
				<a id="wf-mobile-controls" href="#" data-restore-defaults-section="<?php echo esc_attr($restoreDefaultsSection); ?>">&bullet;&bullet;&bullet;</a>
			</li>
			<?php endif; ?>
		</ul>
	</div>
</div>
<?php if (!isset($suppressControls) || !$suppressControls): ?>
<script type="application/javascript">
	(function($) {
		$(function() {
			var initialTop = parseInt($('.wf-options-controls').css('top'));
			$(window).bind("scroll", function() {
				if (window.matchMedia("only screen and (max-width: 615px)").matches) {
					$(this).scrollTop() > initialTop ? $('.wf-options-controls').css('top', '0px').css('position', 'fixed').css('left', '0px') : $('.wf-options-controls').css('top', initialTop + 'px').css('position', 'absolute').css('left', '-10px');
				}
			});
		});
	})(jQuery);
</script>
<script type="text/x-jquery-template" id="wfTmpl_restoreDefaultsPrompt">
	<?php
	echo wfView::create('common/modal-prompt', array(
		'title' => __('Confirm Restore Defaults', 'wordfence'),
		'message' => $restoreDefaultsMessage,
		'primaryButton' => array('id' => 'wf-restore-defaults-prompt-cancel', 'label' => __('Cancel', 'wordfence'), 'link' => '#'),
		'secondaryButtons' => array(array('id' => 'wf-restore-defaults-prompt-confirm', 'labelHTML' => __('Restore<span class="wf-hidden-xs"> Defaults</span>', 'wordfence'), 'link' => '#')),
	))->render();
	?>
</script>
<?php endif; ?>
