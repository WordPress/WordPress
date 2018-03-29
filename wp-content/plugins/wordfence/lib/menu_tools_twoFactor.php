<?php
if (!defined('WORDFENCE_VERSION')) { exit; }

$helpLink = wfSupportController::supportURL(wfSupportController::ITEM_TOOLS_TWO_FACTOR);

echo wfView::create('common/section-title', array(
	'title'     => __('Two Factor Authentication', 'wordfence'),
	'helpLink'  => $helpLink,
	'helpLabelHTML' => __('Learn more<span class="wf-hidden-xs"> about Two Factor Authentication</span>', 'wordfence'),
))->render();
?>

<script type="application/javascript">
	(function($) {
		$(function() {
			document.title = "<?php esc_attr_e('Two Factor Authentication', 'wordfence'); ?>" + " \u2039 " + WFAD.basePageName;
		});
	})(jQuery);
</script>

<div id="wordfenceMode_twoFactor"></div>

<div id="wf-tools-two-factor">
	<?php if (!wfConfig::get('isPaid')): ?>
		<div class="wf-premium-callout wf-add-bottom">
			<h3><?php _e("Take Login Security to the next level with Two Factor Authentication", 'wordfence') ?></h3>
			<p><?php _e('Used by banks, government agencies, and military worldwide, two factor authentication is one of the most secure forms of remote system authentication available. With it enabled, an attacker needs to know your username, password, <em>and</em> have control of your phone to log into your site. Upgrade to Premium now to enable this powerful feature.', 'wordfence') ?></p>

			<p class="wf-nowrap">
				<img id="wf-two-factor-img1" src="<?php echo wfUtils::getBaseURL() . 'images/2fa1.svg' ?>" alt="">
				<img id="wf-two-factor-img2" src="<?php echo wfUtils::getBaseURL() . 'images/2fa2.svg' ?>" alt="">
			</p>

			<p class="center">
				<a class="wf-btn wf-btn-primary wf-btn-callout" href="https://www.wordfence.com/gnl1twoFac1/wordfence-signup/" target="_blank" rel="noopener noreferrer"><?php _e('Upgrade to Premium', 'wordfence') ?></a>
			</p>
		</div>

	<?php else: ?>
		<div class="wf-row">
			<div class="wf-col-xs-12 wf-flex-row">
				<div class="wf-flex-row-1">
					<p><?php _e('With Two Factor Authentication enabled, an attacker needs to know your username, password <em>and</em> have control of your phone to log in to your site. We recommend you enable Two Factor Authentication for all Administrator level accounts.', 'wordfence') ?></p>
				</div>
				<div class="wf-flex-row-0 wf-padding-add-left">
					<?php
					echo wfView::create('options/block-controls', array(
						'suppressLogo' => true,
						'restoreDefaultsSection' => wfConfig::OPTIONS_TYPE_TWO_FACTOR,
						'restoreDefaultsMessage' => __('Are you sure you want to restore the default Two Factor Authentication settings? This will undo any custom changes you have made to the options on this page. If you have configured any users to use two factor authentication, they will not be changed.', 'wordfence'),
					))->render();
					?>
				</div>
			</div>
		</div>

		<div class="wf-row">
			<div class="wf-col-xs-12">
				<div class="wf-block wf-active">
					<?php if (!wfConfig::get('loginSecurityEnabled')): ?>
						<ul class="wf-block-banner">
							<li><?php _e('<strong>Note:</strong> Two Factor Authentication is disabled when the option "Enable Brute Force Protection" is off.', 'wordfence'); ?></li>
							<li><a href="#" class="wf-btn wf-btn-default" id="wf-2fa-enable"><?php _e('Turn On', 'wordfence'); ?></a></li>
						</ul>
					<?php endif; ?>
					<div class="wf-block-header">
						<div class="wf-block-header-content">
							<div class="wf-block-title">
								<strong><?php _e('Enable Two Factor Authentication', 'wordfence') ?></strong>
							</div>
						</div>
					</div>
					<div class="wf-block-content">
						<ul class="wf-block-list">
							<li>
								<ul class="wf-form-field">
									<li style="width: 450px;" class="wf-option-text">
										<input placeholder="<?php echo esc_attr(__('Enter username to enable Two Factor Authentication for', 'wordfence')) ?>" type="text" id="wfUsername" class="wf-form-control" value="">
									</li>
								</ul>
							</li>
							<li>
								<ul class="wf-form-field">
									<li>
										<input class="wf-option-radio" type="radio" name="wf2faMode" id="wf2faMode-authenticator" value="authenticator" checked>
										<label for="wf2faMode-authenticator">&nbsp;&nbsp;</label>
									</li>
									<li class="wf-option-title"><?php _e('Use authenticator app', 'wordfence') ?></li>
								</ul>
							</li>
							<li>
								<ul class="wf-form-field">
									<li>
										<input class="wf-option-radio" type="radio" name="wf2faMode" id="wf2faMode-phone" value="phone">
										<label for="wf2faMode-phone">&nbsp;&nbsp;</label>
									</li>
									<li class="wf-option-title"><?php _e('Send code to a phone number:', 'wordfence') ?>&nbsp;&nbsp;</li>
									<li class="wf-option-text">
										<input class="wf-form-control" type="text" value="" id="wfPhone" placeholder="<?php echo esc_attr(__('+1 (000) 000 0000', 'wordfence')) ?>">
									</li>
								</ul>

							</li>
							<li>
								<p>
									<input type="button" class="wf-btn wf-btn-primary pull-right" value="Enable User" onclick="WFAD.addTwoFactor(jQuery('#wfUsername').val(), jQuery('#wfPhone').val(), jQuery('input[name=wf2faMode]:checked').val());">
								</p>
							</li>

						</ul>

					</div>
				</div>
			</div>
		</div>
		<div class="wf-row">
			<div class="wf-col-xs-12">
				<h2><?php _e('Two Factor Authentication Users', 'wordfence') ?></h2>

				<div id="wfTwoFacUsers"></div>
			</div>
		</div>
		<?php
		echo wfView::create('tools/options-group-2fa', array(
			'stateKey' => 'wf-2fa-options',
		))->render();
		?>

		<script type="text/javascript">
			jQuery('.twoFactorOption').on('click', function() {
				WFAD.updateConfig(jQuery(this).attr('name'), jQuery(this).is(':checked') ? 1 : 0, function() {

				});
			});

			jQuery('input[name=wf2faMode]').on('change', function() {
				var selectedMode = jQuery('input[name=wf2faMode]:checked').val();
				jQuery('#wfPhone').prop('disabled', selectedMode != 'phone');
			}).triggerHandler('change');

			(function($) {
				$(function() {
					$('#wf-2fa-enable').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFAD.setOption('loginSecurityEnabled', 1, function() {
							window.location.reload(true);
						});
					});
				});
			})(jQuery);
		</script>

		<script type="text/x-jquery-template" id="wfTwoFacUserTmpl">
			<table class="wf-striped-table wf-table-twofactor">
				<thead>
				<tr>
					<th>User</th>
					<th>Mode</th>
					<th>Status</th>
					<th class="wf-center">Delete</th>
				</tr>
				</thead>
				<tbody>
				{{each(idx, user) users}}
				<tr id="twoFactorUser-${user.userID}">
					<td style="white-space: nowrap;">${user.username}</td>
					{{if user.mode == 'phone'}}
					<td style="white-space: nowrap;"><?php printf(__('Phone (%s)', 'wordfence'), '${user.phone}') ?></td>
					{{else}}
					<td style="white-space: nowrap;"><?php _e('Authenticator', 'wordfence') ?></td>
					{{/if}}
					<td style="white-space: nowrap;">
						{{if user.status == 'activated'}}
						<span style="color: #0A0;"><?php _e('Cellphone Sign-in Enabled', 'wordfence') ?></span>
						{{else}}
						<div class="wf-form-inline">
							<div class="wf-form-group">
								<label class="wf-plain wf-hidden-xs" style="margin: 0;" for="wfActivate-${user.userID}"><?php _e('Enter activation code:', 'wordfence') ?></label>
								<input class="wf-form-control" type="text" id="wfActivate-${user.userID}" size="6" placeholder="<?php esc_attr_e('Code', 'wordfence') ?>">
							</div>
							<input class="wf-btn wf-btn-default" type="button" value="<?php esc_attr_e('Activate', 'wordfence') ?>" onclick="WFAD.twoFacActivate('${user.userID}', jQuery('#wfActivate-${user.userID}').val());">
						</div>
						{{/if}}
					</td>
					<td style="white-space: nowrap; text-align: center;" class="wf-twofactor-delete">
						<a href="#" onclick="WFAD.delTwoFac('${user.userID}'); return false;"><i class="wf-ion-ios-trash-outline"></i></a>
					</td>
				</tr>
				{{/each}}
				{{if (users.length == 0)}}
				<tr id="twoFactorUser-none">
					<td colspan="4"><?php _e('No users currently have cellphone sign-in enabled.', 'wordfence') ?></td>
				</tr>
				{{/if}}
				</tbody>
			</table>
		</script>
	<?php endif ?>
</div>
