<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents a modal prompt.
 *
 * Expects $title (or $titleHTML), $message (or $messageHTML), and $primaryButton to be defined. $secondaryButtons may also be defined.
 *
 * @var string $title The title for the prompt.
 * @var string $titleHTML The raw HTML title for the prompt. This supersedes $title.
 * @var string $message The message for the prompt.
 * @var string $messageHTML The raw HTML message for the prompt. This supersedes $message.
 * @var array $primaryButton The parameters for the primary button. The array is in the format array('id' => <element id>, 'label' => <button text>, 'link' => <href value>)
 * @var array $secondaryButtons The parameters for any secondary buttons. It is an array of arrays in the format array('id' => <element id>, 'label' => <button text>, 'link' => <href value>). The ordering of entries is the right-to-left order the buttons will be displayed.
 */

if (!isset($titleHTML)) {
	$titleHTML = esc_html($title);
}

if (!isset($messageHTML)) {
	$messageHTML = esc_html($message);
}

if (!isset($secondaryButtons)) {
	$secondaryButtons = array();
}
$secondaryButtons = array_reverse($secondaryButtons);
?>
<div class="wf-modal">
	<div class="wf-modal-header">
		<div class="wf-modal-header-content">
			<div class="wf-modal-title">
				<strong><?php echo $titleHTML; ?></strong>
			</div>
		</div>
		<div class="wf-modal-header-action">
			<div class="wf-padding-add-left-small wf-modal-header-action-close"><a href="#" onclick="WFAD.colorboxClose(); return false"><i class="wf-fa wf-fa-times-circle" aria-hidden="true"></i></a></div>
		</div>
	</div>
	<div class="wf-modal-content">
		<?php echo $messageHTML; ?>
	</div>
	<div class="wf-modal-footer">
		<ul class="wf-flex-horizontal wf-flex-align-right wf-full-width">
		<?php foreach ($secondaryButtons as $button): ?>
			<li class="wf-padding-add-left-small"><a href="<?php echo esc_attr($button['link']); ?>" class="wf-btn <?php echo isset($button['type']) ? $button['type'] : 'wf-btn-default'; ?> wf-btn-callout-subtle" id="<?php echo esc_attr($button['id']); ?>"><?php echo isset($button['labelHTML']) ? $button['labelHTML'] : esc_html($button['label']); ?></a></li>
		<?php endforeach; ?>
			<li class="wf-padding-add-left-small"><a href="<?php echo esc_attr($primaryButton['link']); ?>" class="wf-btn <?php echo isset($primaryButton['type']) ? $primaryButton['type'] : 'wf-btn-primary'; ?> wf-btn-callout-subtle" id="<?php echo esc_attr($primaryButton['id']); ?>"><?php echo isset($primaryButton['labelHTML']) ? $primaryButton['labelHTML'] : esc_html($primaryButton['label']); ?></a></li>
		</ul>
	</div>
</div>
