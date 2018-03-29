<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the firewall status.
 *
 * Expects $firewall and $dashboard to be defined.
 *
 * @var wfFirewall $firewall The firewall state.
 * @var wfDashboard $dashboard Dashboard statistics.
 */
?>
<ul class="wf-block-list wf-block-list-horizontal">
<?php if ($firewall->firewallMode() == 'enabled' && $firewall->ruleMode() == wfFirewall::RULE_MODE_PREMIUM): ?>
	<li>
		<div class="wf-block-labeled-value wf-waf-status wf-waf-status-full-enabled">
			<div class="wf-block-labeled-value-label"><?php _e('Wordfence Firewall &amp; Premium Enabled', 'wordfence'); ?></div>
		</div>
	</li>
<?php else: ?>
	<li>
	<?php if ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_DISABLED): ?>
		<div class="wf-waf-status-disabled">
			<p><h3><?php _e('Wordfence Firewall Deactivated', 'wordfence'); ?></h3></p>
			<p><?php _e('The Wordfence Web Application Firewall is a PHP-based, application-level firewall that filters out malicious requests to your site. It is designed to run at the beginning of WordPress\' initialization to filter any attacks before plugins or themes can run any potentially vulnerable code.', 'wordfence'); ?></p>
			<p>
				<a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="#" target="_blank" rel="noopener noreferrer" id="waf-top-enable-firewall"><?php _e('Enable Firewall', 'wordfence'); ?></a>
				<script type="application/javascript">
					(function($) {
						$(function() {
							$('#waf-top-enable-firewall').on('click', function(e) {
								e.preventDefault();
								e.stopPropagation();
	
								WFAD.setOption('wafStatus', 'enabled', function() {
									window.location.reload(true);
								});
							});
						});
					})(jQuery);
				</script>
			</p>
		</div>
	<?php else: ?>
		<?php if ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_ENABLED): ?>
		<div class="wf-block-labeled-value wf-waf-status wf-waf-status-<?php echo esc_attr($firewall->firewallMode()); ?>">
			<div class="wf-block-labeled-value-value"><i class="wf-fa wf-fa-check" aria-hidden="true"></i></div>
			<div class="wf-block-labeled-value-label"><?php _e('Wordfence Firewall Activated', 'wordfence'); ?></div>
		</div>
		<?php elseif ($firewall->firewallMode() == wfFirewall::FIREWALL_MODE_LEARNING): ?>
			<div>
				<?php
				$learningMode = $firewall->learningModeStatus();
				if (function_exists('network_admin_url') && is_multisite()) { $optionsURL = network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options'); }
				else { $optionsURL = admin_url('admin.php?page=WordfenceWAF&subpage=waf_options'); }
				?>
				<p><h3><?php echo ($learningMode === true ? __('Learning Mode Enabled', 'wordfence') : sprintf(__('Learning Mode Until %s', 'wordfence'), wfUtils::formatLocalTime(get_option('date_format'), $learningMode))); ?></h3></p>
				<p><?php _e('<i class="wf-fa wf-fa-lightbulb-o wf-tip" aria-hidden="true"></i> When you first install the Wordfence Web Application Firewall, it will be in learning mode. This allows Wordfence to learn about your site so that we can understand how to protect it and how to allow normal visitors through the firewall. We recommend you let Wordfence learn for a week before you enable the firewall.', 'wordfence'); ?></p>
				<p><a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="<?php echo esc_url($optionsURL); ?>"><?php _e('Manage Firewall', 'wordfence'); ?></a>&nbsp;&nbsp;<a class="wf-btn wf-btn-callout-subtle wf-btn-default" href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_WAF); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></p>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	</li>
	<li>
	<?php if ($firewall->ruleMode() == wfFirewall::RULE_MODE_COMMUNITY): ?>
		<div>
			<p><h3><?php _e('Premium Protection Disabled', 'wordfence'); ?></h3></p>
			<p><?php printf(__('As a free Wordfence user, you are currently using the Community version of the Threat Defense Feed. Premium users are protected by an additional %d firewall rules and malware signatures. Upgrade to Premium today to improve your protection.', 'wordfence'), ($dashboard->tdfPremium - $dashboard->tdfCommunity)); ?></p>
			<p><a class="wf-btn wf-btn-primary wf-btn-callout-subtle" href="https://www.wordfence.com/gnl1wafUpgrade/wordfence-signup/#premium-order-form" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence'); ?></a>&nbsp;&nbsp;<a class="wf-btn wf-btn-callout-subtle wf-btn-default" href="https://www.wordfence.com/gnl1wafLearn/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Learn More', 'wordfence'); ?></a></p>
		</div>
	<?php else: ?>
		<div class="wf-block-labeled-value wf-protection-status wf-protection-status-<?php echo esc_attr($firewall->ruleMode()); ?>">
			<div class="wf-block-labeled-value-value"><i class="wf-fa wf-fa-check" aria-hidden="true"></i></div>
			<div class="wf-block-labeled-value-label"><?php _e('Premium Protection Enabled', 'wordfence'); ?></div>
		</div>
	<?php endif; ?>
	</li>
<?php endif; ?>
</ul>
