<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Expects $title (or $titleHTML) to be defined. If $helpLink and $helpLabel (or $helpLabelHTML) are defined, the help link will be shown.
 * 
 * @var $title string The page title.
 * @var $titleHTML string The page title as raw HTML
 * @var $helpLink string The URL for the help link.
 * @var $helpLabel string The help link's text.
 * @var $helpLabelHTML string The help link's text as raw HTML.
 */

if (isset($title) && !isset($titleHTML)) {
	$titleHTML = esc_html($title);
}

if (isset($helpLabel) && !isset($helpLabelHTML)) {
	$helpLabelHTML = esc_html($helpLabel);
}
?>
<div class="wf-section-title">
<?php if (isset($showIcon) && $showIcon): ?>
	<div class="wordfence-lock-icon wordfence-icon32 wf-hidden-xs"></div>
<?php endif; ?>
	<h2 class="wf-center-xs"<?php echo (isset($headerID) ? ' id="' . $headerID . '"' : ''); ?>><?php echo $titleHTML; ?></h2>
<?php if (isset($helpLink) && isset($helpLabelHTML)): ?>
	<span class="wf-hidden-xs"><a href="<?php echo esc_attr($helpLink); ?>" target="_blank" rel="noopener noreferrer" class="wf-help-link"><?php echo $helpLabelHTML; ?></a> <i class="wf-fa wf-fa-external-link" aria-hidden="true"></i></span>
<?php endif; ?>
</div>