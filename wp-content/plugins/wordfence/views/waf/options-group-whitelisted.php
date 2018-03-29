<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the Whitelisted URLs group.
 *
 * Expects $firewall, $waf, and $stateKey.
 *
 * @var wfFirewall $firewall
 * @var wfWAF $waf
 * @var string $stateKey The key under which the collapse state is stored.
 * @var bool $collapseable If defined, specifies whether or not this grouping can be collapsed. Defaults to true.
 */

$config = $waf->getStorageEngine();

if (!isset($collapseable)) {
	$collapseable = true;
}
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-block<?php if (!$collapseable) { echo ' wf-always-active'; } else { echo (wfPersistenceController::shared()->isActive($stateKey) ? ' wf-active' : ''); } ?>" data-persistence-key="<?php echo esc_attr($stateKey); ?>">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Whitelisted URLs', 'wordfence'); ?></strong>
					</div>
					<?php if ($collapseable): ?><div class="wf-block-header-action"><div class="wf-block-header-action-disclosure"></div></div><?php endif; ?>
				</div>
			</div>
			<div class="wf-block-content">
				<ul class="wf-block-list">
					<?php if ($firewall->isSubDirectoryInstallation()): ?>
						<li>
							<p><?php printf(__('You are currently running the Wordfence Web Application Firewall from another WordPress installation. Please <a href="%s">click here</a> to configure the Firewall to run correctly on this site.', 'wordfence'), esc_attr(network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#configureAutoPrepend'))); ?></p>
						</li>
					<?php else: ?>
						<li>
							<?php
							echo wfView::create('waf/option-whitelist', array(
							))->render();
							?>
						</li>
						<li>
							<?php
							echo wfView::create('options/option-toggled-multiple', array(
								'options' => array(
									array(
										'name' => 'ajaxWatcherDisabled_front',
										'enabledValue' => 0,
										'disabledValue' => 1,
										'value' => wfConfig::get('ajaxWatcherDisabled_front') ? 1 : 0,
										'title' => __('Front-end Website', 'wordfence'),
									),
									array(
										'name' => 'ajaxWatcherDisabled_admin',
										'enabledValue' => 0,
										'disabledValue' => 1,
										'value' => wfConfig::get('ajaxWatcherDisabled_admin') ? 1 : 0,
										'title' => __('Admin Panel', 'wordfence'),
									),
								),
								'title' => __('Monitor background requests from an administrator\'s web browser for false positives', 'wordfence'),
								'helpLink' => wfSupportController::supportURL(wfSupportController::ITEM_FIREWALL_WAF_OPTION_MONITOR_AJAX),
							))->render();
							?>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end whitelisted urls -->
<script type="text/x-jquery-template" id="waf-whitelisted-urls-tmpl">
	<div class="whitelist-table-container">
		<table class="wf-striped-table whitelist-table">
			<thead>
			<tr>
				<th style="width: 2%;text-align: center"><div class="wf-whitelist-bulk-select wf-option-checkbox"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div></th>
				<th style="width: 5%;"><?php _e('Enabled', 'wordfence'); ?></th>
				<th><?php _e('URL', 'wordfence'); ?></th>
				<th><?php _e('Param', 'wordfence'); ?></th>
				<th><?php _e('Created', 'wordfence'); ?></th>
				<th><?php _e('Source', 'wordfence'); ?></th>
				<th><?php _e('User', 'wordfence'); ?></th>
				<th><?php _e('IP', 'wordfence'); ?></th>
			</tr>
			</thead>
			{{if whitelistedURLParams.length > 5}}
			<tfoot>
			<tr>
				<th style="width: 2%;text-align: center"><div class="wf-whitelist-bulk-select wf-option-checkbox"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div></th>
				<th style="width: 5%;"><?php _e('Enabled', 'wordfence'); ?></th>
				<th><?php _e('URL', 'wordfence'); ?></th>
				<th><?php _e('Param', 'wordfence'); ?></th>
				<th><?php _e('Created', 'wordfence'); ?></th>
				<th><?php _e('Source', 'wordfence'); ?></th>
				<th><?php _e('User', 'wordfence'); ?></th>
				<th><?php _e('IP', 'wordfence'); ?></th>
			</tr>
			{{/if}}
			</tfoot>
			<tbody>
			{{each(idx, whitelistedURLParam) whitelistedURLParams}}
			<tr data-index="${idx}" data-adding="{{if (whitelistedURLParam.adding)}}1{{else}}0{{/if}}" data-key="${whitelistedURLParam.path}|${whitelistedURLParam.paramKey}">
				<td style="text-align: center;"><div class="wf-whitelist-table-bulk-checkbox wf-option-checkbox"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div></td>
				<td style="text-align: center;"><div class="wf-whitelist-item-enabled wf-option-checkbox{{if (!whitelistedURLParam.data.disabled)}} wf-checked{{/if}}" data-original-value="{{if (!whitelistedURLParam.data.disabled)}}1{{else}}0{{/if}}"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div></td>
				<td data-column="url">
					<input name="replaceWhitelistedPath" type="hidden" value="${whitelistedURLParam.path}">
					<span class="whitelist-display">${WFAD.htmlEscape(WFAD.base64_decode(whitelistedURLParam.path))}</span>
					<input name="whitelistedPath" class="whitelist-edit whitelist-path" type="text"
						   value="${WFAD.htmlEscape(WFAD.base64_decode(whitelistedURLParam.path))}">
				</td>
				<td data-column="param">
					<input name="replaceWhitelistedParam" type="hidden" value="${whitelistedURLParam.paramKey}">
					<span class="whitelist-display">${WFAD.htmlEscape(WFAD.base64_decode(whitelistedURLParam.paramKey))}</span>
					<input name="whitelistedParam" class="whitelist-edit whitelist-param-key"
						   type="text" value="${WFAD.htmlEscape(WFAD.base64_decode(whitelistedURLParam.paramKey))}">
				</td>
				<td>
					{{if (whitelistedURLParam.data.timestamp)}}
					${WFAD.dateFormat((new Date(whitelistedURLParam.data.timestamp * 1000)))}
					{{else}}
					-
					{{/if}}
				</td>
				<td data-column="source">
					{{if (whitelistedURLParam.data.description)}}
					${whitelistedURLParam.data.description}
					{{else}}
					-
					{{/if}}
				</td>
				<td data-column="user">
					{{if (whitelistedURLParam.data.userID)}}
					{{if (whitelistedURLParam.data.username)}}
					${whitelistedURLParam.data.username}
					{{else}}
					${whitelistedURLParam.data.userID}
					{{/if}}
					{{else}}
					-
					{{/if}}
				</td>
				<td data-column="ip">
					{{if (whitelistedURLParam.data.ip)}}
					${whitelistedURLParam.data.ip}
					{{else}}
					-
					{{/if}}
				</td>
			</tr>
			{{/each}}
			{{if (whitelistedURLParams.length == 0)}}
			<tr>
				<td colspan="8"><?php _e('No whitelisted URLs currently set.', 'wordfence'); ?></td>
			</tr>
			{{/if}}
			</tbody>
		</table>
	</div>
</script>
<script type="application/javascript">
	(function($) {
		function whitelistCheckAllVisible() {
			$('.wf-whitelist-bulk-select.wf-option-checkbox').toggleClass('wf-checked', true);
			$('.wf-whitelist-table-bulk-checkbox.wf-option-checkbox').each(function() {
				$(this).toggleClass('wf-checked', $(this).closest('tr').is(':visible'));
			});
		}

		function whitelistUncheckAll() {
			$('.wf-whitelist-bulk-select.wf-option-checkbox').toggleClass('wf-checked', false);
			$('.wf-whitelist-table-bulk-checkbox.wf-option-checkbox').toggleClass('wf-checked', false);
		}

		$(window).on('wordfenceWAFInstallWhitelistEventHandlers', function() {
			//Enabled/Disabled
			$('.wf-whitelist-item-enabled.wf-option-checkbox').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					var row = $(this).closest('tr');
					var key = row.data('key');
					var value = $(this).hasClass('wf-checked') ? 1 : 0;
					if (value) {
						$(this).removeClass('wf-checked');
						value = 0;
					}
					else {
						$(this).addClass('wf-checked');
						value = 1;
					}

					WFAD.wafWhitelistedChangeEnabled(key, value);
					WFAD.updatePendingChanges();
				});
			});

			//Header/Footer Bulk Action
			$('.wf-whitelist-bulk-select.wf-option-checkbox').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					if ($(this).hasClass('wf-checked')) {
						$(this).removeClass('wf-checked');
						whitelistUncheckAll();
					}
					else {
						$(this).addClass('wf-checked');
						whitelistCheckAllVisible();
					}
				});
			});

			//Row Bulk Action
			$('.wf-whitelist-table-bulk-checkbox.wf-option-checkbox').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					var row = $(this).closest('tr');
					var key = row.data('key');
					var value = $(this).hasClass('wf-checked') ? 1 : 0;
					if (value) {
						$(this).removeClass('wf-checked');
					}
					else {
						$(this).addClass('wf-checked');
					}

					var totalCount = $('.wf-whitelist-table-bulk-checkbox.wf-option-checkbox:visible').length;
					var checkedCount = $('.wf-whitelist-table-bulk-checkbox.wf-option-checkbox.wf-checked:visible').length;
					if (totalCount == 0 || (checkedCount != totalCount)) {
						$('.wf-whitelist-bulk-select.wf-option-checkbox').removeClass('wf-checked');
					}
					else {
						$('.wf-whitelist-bulk-select.wf-option-checkbox').addClass('wf-checked');
					}
				});
			});

			$(window).trigger('wordfenceWAFApplyWhitelistFilter');
		});

		$(window).on('wordfenceWAFApplyWhitelistFilter', function() {
			if (WFAD.wafData.whitelistedURLParams.length == 0) {
				return;
			}

			var filterColumn = $('#whitelist-table-controls select').val();
			var filterValue = $('input[name="filterValue"]').val();
			if (typeof filterValue != 'string' || filterValue.length == 0) {
				$('#waf-whitelisted-urls-wrapper .whitelist-table > tbody > tr[data-index]').show();
			}
			else {
				$('#waf-whitelisted-urls-wrapper .whitelist-table > tbody > tr[data-index]').each(function() {
					var text = $(this).find('td[data-column="' + filterColumn + '"]').text();
					if (text.indexOf(filterValue) > -1) {
						$(this).show();
					}
					else {
						$(this).hide();
					}
				});
			}
		});

		$(window).on('wordfenceWAFConfigPageRender', function() {
			//Add event handler to whitelist checkboxes
			$(window).trigger('wordfenceWAFInstallWhitelistEventHandlers');
		});
	})(jQuery);
</script>