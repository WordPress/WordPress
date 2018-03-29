<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Expects $tabs to be defined as an array of wfTab instances
 */
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wp-header-end"></div>
		<ul class="wf-hidden-xs wf-page-fixed-tabs">
			<li class="wordfence-lock-icon wordfence-icon32"></li>
			<?php foreach ($tabs as $t): ?>
				<?php
				$a = $t->a;
				if (!preg_match('/^https?:\/\//i', $a)) {
					$a = '#top#' . urlencode($a);
				}
				?>
				<li class="wf-tab<?php if ($t->active) { echo ' wf-active'; } ?>" id="wf-tab-<?php echo esc_attr($t->id); ?>" data-target="<?php echo esc_attr($t->id); ?>" data-page-title="<?php echo esc_attr($t->pageTitle); ?>"><a href="<?php echo esc_attr($a); ?>"><?php echo esc_html($t->tabTitle); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<ul class="wf-nav wf-nav-pills wf-visible-xs">
			<li class="wf-navbar-brand wordfence-lock-icon wordfence-icon32"></li>
			<li class="wf-dropdown">
				<a href="#" id="wordfenceTopTabsMobile" class="wf-dropdown-toggle wf-mobile-dropdown" data-toggle="wf-dropdown"><span id="wordfenceTopTabsMobileTitle">Go to</span> <span class="wf-caret"></span></a>
				<ul class="wf-dropdown-menu">
					<?php foreach ($tabs as $t): ?>
						<?php
						$a = $t->a;
						if (!preg_match('/^https?:\/\//i', $a)) {
							$a = '#top#' . urlencode($a);
						}
						?>
						<li id="wf-tab-mobile-<?php echo esc_attr($t->id); ?>"><a href="<?php echo esc_attr($a); ?>"><?php echo esc_html($t->tabTitle); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</li>
		</ul>
	</div>
</div>