<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the top controls on the All Options page.
 *
 * Expects $indexOptions. $suppressControls may be defined to hide the options-saving controls. If not truthy, $restoreDefaultsSection and $restoreDefaultsMessage must also be defined.
 *
 * @var array $indexOptions An array of the index options to allow searching. The key should be the element ID to scroll to and the value is the name of the option.
 * @var string $restoreDefaultsMessage The message shown in the restore defaults prompt
 */

if (isset($backLabel) && !isset($backLabelHTML)) {
	$backLabelHTML = esc_html($backLabel);
}
?>
<div class="wf-block wf-block-transparent wf-active">
	<div class="wf-block-content">
		<ul class="wf-block-left-right wf-block-left-right-nowrap wf-hidden-xs">
			<li class="wf-left">
				<ul class="wf-flex-horizontal">
					<?php if (isset($showIcon) && $showIcon): ?>
					<li><div class="wordfence-lock-icon wordfence-icon32 wf-no-top wf-no-right wf-hidden-xs"></div></li>
					<?php endif; ?>
					<li id="wf-all-options-search"<?php if (isset($showIcon) && $showIcon): ?> class="wf-padding-add-left"<?php endif; ?>>
						<select class="wf-options-searcher" multiple>
						<?php
						foreach ($indexOptions as $element => $label):
						?>
							<option value="<?php echo esc_attr($element); ?>"><?php echo esc_html($label); ?></option>
						<?php
						endforeach;
						?>
						</select>
					</li>
				</ul>
			</li>
			<?php if (!isset($suppressControls) || !$suppressControls): ?>
				<li class="wf-right">
					<a id="wf-restore-defaults" class="wf-btn wf-btn-default wf-btn-callout-subtle" href="#" data-restore-defaults-section="<?php echo esc_attr($restoreDefaultsSection); ?>"><?php _e('<span class="wf-hidden-xs">Restore </span>Defaults', 'wordfence'); ?></a>&nbsp;&nbsp;<a id="wf-cancel-changes" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-disabled" href="#"><?php _e('Cancel<span class="wf-hidden-xs wf-hidden-sm"> Changes</span>', 'wordfence'); ?></a>&nbsp;&nbsp;<a id="wf-save-changes" class="wf-btn wf-btn-primary wf-btn-callout-subtle wf-disabled" href="#"><?php _e('Save<span class="wf-hidden-xs wf-hidden-sm"> Changes</span>', 'wordfence'); ?></a>
				</li>
			<?php endif; ?>
		</ul>
		<ul class="wf-block-left-center-right wf-hidden-sm wf-hidden-md wf-hidden-lg">
			<li class="wf-left">
				<?php if (!isset($suppressLogo) || !$suppressLogo): ?>
					<div class="wordfence-lock-icon wordfence-icon32"></div>
				<?php endif; ?>
			</li>
			<li class="wf-center">
				<select class="wf-options-searcher" multiple>
					<?php
					foreach ($indexOptions as $element => $label):
						?>
						<option value="<?php echo esc_attr($element); ?>"><?php echo esc_html($label); ?></option>
						<?php
					endforeach;
					?>
				</select>
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
			$('.wf-options-searcher').css('display', 'none');
			
			$(function() {
				var initialTop = parseInt($('.wf-options-controls').css('top'));
				$(window).bind("scroll", function() {
					if (window.matchMedia("only screen and (max-width: 615px)").matches) {
						$(this).scrollTop() > initialTop ? $('.wf-options-controls').css('top', '0px').css('position', 'fixed').css('left', '0px') : $('.wf-options-controls').css('top', initialTop + 'px').css('position', 'absolute').css('left', '-10px');
					}
				});

				$('.wf-options-searcher').wfselect2({
					tags: true,
					tokenSeparators: [','],
					placeholder: "Search All Options",
					width: 'element',
					minimumResultsForSearch: -1,
					minimumInputLength: 2,
					selectOnClose: false,
					width: (WFAD.isSmallScreen ? '300px' : '500px'),
					createTag: function (params) {
						return null; //No custom tags
					}
				}).on('change', function () {
					var selection = $(this).val();
					if (Array.isArray(selection)) {
						if (selection.length > 0) {
							selection = selection[0];
						}
						else {
							selection = false;
						}
					}
					else if (typeof selection !== 'string') {
						selection = false;
					}
					
					if (selection !== false) {
						var el = $('#' + selection);
						if (el.is(':visible')) {
							$('html, body').animate({
								scrollTop: el.offset().top - 100
							}, 750);
						}
						else {
							var block = el.closest('.wf-block[data-persistence-key]');
							if (!block.hasClass('wf-active')) {
								block.find('.wf-block-content').slideDown({
									always: function() {
										block.addClass('wf-active');
										$('html, body').animate({
											scrollTop: el.offset().top - 100
										}, 750);
									}
								});

								WFAD.ajax('wordfence_saveDisclosureState', {name: block.data('persistenceKey'), state: true}, function() {}, function() {}, true);
							}
						}

						$('.wf-options-searcher').val('').change();
					}
				});

				if ($('.wf-options-searcher').length > 0) {
					$('.wf-options-searcher').data('wfselect2').$container.addClass('wf-select2-placeholder-fix wf-select2-hide-tags');
				}
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
