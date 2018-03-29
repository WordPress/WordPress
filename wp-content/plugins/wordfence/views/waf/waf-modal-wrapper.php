<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the content given in a modal wrapper for the WAF install/uninstall flow.
 *
 * Expects $title and $html to be defined. $helpHTML, $footerHTML, and $footerButtonTitle may also be defined.
 *
 * @var string $title The title for the panel.
 * @var string $html The main HTML content for the panel.
 * @var string $helpHTML HTML content for the help area next to the close button.
 * @var string $footerHTML HTML content for the footer area next to the footer button.
 * @var string $footerButtonTitle Title for the footer button, defaults to "Continue".
 * @var bool $noX Optional, hides the top right x button if truthy.
 */

if (!isset($footerButtonTitle)) {
	$footerButtonTitle = __('Continue', 'wordfence');
}

$showX = !isset($noX) || !$noX;
?>
<div class="wf-modal">
	<div class="wf-modal-header">
		<div class="wf-modal-header-content">
			<div class="wf-modal-title">
				<strong><?php echo $title; ?></strong>
			</div>
		</div>
		<div class="wf-modal-header-action">
			<div><?php if (isset($helpHTML)) { echo $helpHTML; } ?></div>
			<?php if ($showX) { ?><div class="wf-padding-add-left-small wf-modal-header-action-close"><a href="#" onclick="WFAD.colorboxClose(); return false"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div><?php } ?>
		</div>
	</div>
	<div class="wf-modal-content">
		<?php echo $html; ?>
	</div>
	<div class="wf-modal-footer">
		<ul class="wf-flex-horizontal wf-flex-full-width">
			<li><?php if (isset($footerHTML)) { echo $footerHTML; } ?></li>
			<li class="wf-right"><a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle" id="wf-waf-modal-continue"><?php echo $footerButtonTitle; ?></a></li>
		</ul>
	</div>
</div>
