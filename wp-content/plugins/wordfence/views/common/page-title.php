<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-page-title">
			<div class="wordfence-lock-icon wordfence-icon32"></div><h2 id="wfHeading"><?php echo esc_html($title); ?></h2><?php if (isset($accessory)) { echo (string) $accessory; } ?><?php if (isset($help)) { echo (string) $help; } ?>
		</div>
		<div class="wp-header-end"></div>
	</div>
	<?php if (isset($wantsLiveActivity) && $wantsLiveActivity): ?><div class="wf-col-xs-12"><?php include('live_activity.php'); ?></div><?php endif; ?>
	<div class="wf-col-xs-12">
		<?php if (isset($options)): ?>
			<h2 class="wf-hidden-xs nav-tab-wrapper<?php if (count($options) <= 1 || (isset($hideBar) && $hideBar)) { echo ' wf-hidden'; } ?>" id="wordfenceTopTabs">
				<?php foreach ($options as $info): ?>
					<a class="nav-tab" id="<?php echo esc_html($info['a']); ?>-tab" href="#top#<?php echo esc_html($info['a']); ?>"><?php echo esc_html($info['t']); ?></a>
				<?php endforeach; ?>
			</h2>
			<ul class="wf-nav wf-nav-pills wf-visible-xs">
				<li class="wf-navbar-brand">Go:</li>
				<li class="wf-dropdown">
					<a href="#" id="wordfenceTopTabsMobile" class="wf-dropdown-toggle wf-mobile-dropdown" data-toggle="wf-dropdown"><span id="wordfenceTopTabsMobileTitle">Go to</span> <span class="wf-caret"></span></a>
					<ul class="wf-dropdown-menu">
						<?php foreach ($options as $info): ?>
							<li><a id="<?php echo esc_html($info['a']); ?>-tab-mobile" href="#top#<?php echo esc_html($info['a']); ?>"><?php echo esc_html($info['t']); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</li>
			</ul>
		<?php endif; ?>
		<?php if (isset($helpLink)): ?><div class="wordfenceHelpLink"><a href="<?php echo $helpLink; ?>" target="_blank" rel="noopener noreferrer" class="wfhelp"></a><a href="<?php echo $helpLink; ?>" target="_blank" rel="noopener noreferrer"><?php echo $helpLabel; ?></a></div><?php endif; ?>
	</div>
</div>