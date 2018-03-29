<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents an option-styled text value.
 *
 * Expects $title (or $titleHTML) to be defined. $helpLink may also be defined.
 *
 * @var string $title The title shown for the option.
 * @var string $titleHTML The raw HTML title shown for the option. This supersedes $title.
 * @var string $helpLink If defined, the link to the corresponding external help page.
 */

if (!isset($titleHTML)) {
	$titleHTML = esc_html($title);
}
?>
<ul class="wf-option wf-option-label">
	<?php if (!isset($noSpacer) || !$noSpacer): ?>
		<li class="wf-option-spacer"></li>
	<?php endif; ?>
	<li class="wf-option-content">
		<ul>
			<li class="wf-option-title">
				<?php if (isset($subtitle)): ?>
				<ul class="wf-flex-vertical wf-flex-align-left">
					<li>
						<?php endif; ?>
						<?php echo $titleHTML; ?><?php if (isset($helpLink)) { echo ' <a href="' . esc_attr($helpLink) . '"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a>'; } ?>
						<?php if (isset($subtitle)): ?>
					</li>
					<li class="wf-option-subtitle"><?php echo esc_html($subtitle); ?></li>
				</ul>
			<?php endif; ?>
			</li>
		</ul>
	</li>
</ul>