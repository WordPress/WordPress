<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Expects $tabs to be defined as an array of wfTab instances
 */
?>
<div class="wf-row wf-tab-container">
	<div class="wf-col-xs-12">
		<div class="wp-header-end"></div>
		<ul class="wf-page-tabs">
			<li class="wordfence-lock-icon wordfence-icon32"></li>
		<?php foreach ($tabs as $t): ?>
			<?php
			$a = $t->a;
			if (!preg_match('/^https?:\/\//i', $a)) {
				$a = '#top#' . urlencode($a);
			}
			?>
			<li class="wf-tab" id="wf-tab-<?php echo esc_attr($t->id); ?>" data-target="<?php echo esc_attr($t->id); ?>" data-page-title="<?php echo esc_attr($t->pageTitle); ?>"><a href="<?php echo esc_attr($a); ?>"><?php echo esc_html($t->tabTitle); ?></a></li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>