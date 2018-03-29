<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-dashboard-item active">
			<div class="wf-dashboard-item-inner">
				<div class="wf-dashboard-item-content">
					<div class="wf-dashboard-item-title">
						<strong>Threat Defense Feed - Total Firewall Rules and Malware Signatures</strong>
					</div>
					<div class="wf-dashboard-item-action"><div class="wf-dashboard-item-action-disclosure"></div></div>
				</div>
			</div>
			<div class="wf-dashboard-item-extra">
				<ul class="wf-dashboard-item-list">
					<li>
						<?php if ($d->tdfCommunity === null): ?>
							<div class="wf-dashboard-item-list-text"><em>Threat Defense Feed statistics will be updated soon.</em></div>
						<?php else: ?>
							<ul class="wf-dashboard-item-list wf-dashboard-item-list-horizontal">
								<li>
									<div class="wf-dashboard-item-labeled-count">
										<div class="wf-dashboard-item-labeled-count-count"><?php echo $d->tdfCommunity; ?></div>
										<div class="wf-dashboard-item-labeled-count-label">Free Count</div>
									</div>
								</li>
								<li>
									<div class="wf-dashboard-item-labeled-count">
										<div class="wf-dashboard-item-labeled-count-count"><?php echo $d->tdfPremium; ?></div>
										<div class="wf-dashboard-item-labeled-count-label">Premium Count</div>
									</div>
								</li>
							</ul>
						<?php endif; ?>
					</li>
					<?php if (!wfConfig::get('isPaid')): ?>
						<li>
							<div class="wf-dashboard-item-list-text">
								<p>As a free Wordfence user, you are currently using the Community version of the Threat Defense Feed. Premium users are protected by an additional <?php echo ($d->tdfPremium - $d->tdfCommunity); ?> firewall rules and malware signatures. Upgrade to Premium today to improve your protection.</p>
								<p><a class="wf-btn wf-btn-primary wf-btn-callout" href="https://www.wordfence.com/gnl1scanUpgrade/wordfence-signup/" target="_blank" rel="noopener noreferrer">Upgrade to Premium</a></p>
							</div>
						</li>
					<?php else: ?>
						<li>
							<div class="wf-dashboard-item-list-text">
								<p>As a Premium user you receive updates to the Threat Defense Feed in real-time. You are currently protected by an additional <?php echo ($d->tdfPremium - $d->tdfCommunity); ?> firewall rules and malware signatures.</p>
							</div>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>