<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<ul id="wf-option-wafWhitelist" class="wf-option wf-flex-vertical wf-flex-full-width">
	<li><strong>Add Whitelisted URL/Param</strong> <a href="<?php echo wfSupportController::esc_supportURL(wfSupportController::ITEM_FIREWALL_WAF_WHITELIST); ?>"  target="_blank" rel="noopener noreferrer" class="wf-inline-help"><i class="wf-fa wf-fa-question-circle-o" aria-hidden="true"></i></a> The URL/parameters in this table will not be tested by the firewall. They are typically added while the firewall is in Learning Mode or by an admin who identifies a particular action/request is a false positive.</li>
	<li id="whitelist-form"> 
		<div class="wf-form-inline">
			<div class="wf-form-group">
				<input class="wf-form-control" type="text" name="whitelistURL" id="whitelistURL" placeholder="URL">
			</div>
			<div class="wf-form-group">
				<select class="wf-form-control" name="whitelistParam" id="whitelistParam">
					<option value="request.body">POST Body</option>
					<option value="request.cookies">Cookie</option>
					<option value="request.fileNames">File Name</option>
					<option value="request.headers">Header</option>
					<option value="request.queryString">Query String</option>
				</select>
			</div>
			<div class="wf-form-group">
				<input class="wf-form-control" type="text" name="whitelistParamName" id="whitelistParamName" placeholder="Param Name">
			</div>
			<a href="#" class="wf-btn wf-btn-callout wf-btn-primary wf-disabled" id="waf-whitelisted-urls-add">Add</a>
		</div>
		<script type="application/javascript">
			(function($) {
				$(function() {
					$('#whitelistURL, #whitelistParamName').on('change paste keyup', function() {
						setTimeout(function() {
							$('#waf-whitelisted-urls-add').toggleClass('wf-disabled', $('#whitelistURL').val().length == 0 || $('#whitelistParamName').val().length == 0);
						}, 100);
					});
					
					$('#waf-whitelisted-urls-add').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var form = $('#whitelist-form');
						var inputURL = form.find('[name=whitelistURL]');
						var inputParam = form.find('[name=whitelistParam]');
						var inputParamName = form.find('[name=whitelistParamName]');

						var url = inputURL.val();
						var param = inputParam.val();
						var paramName = inputParamName.val();
						if (url && param) {
							<?php $user = wp_get_current_user(); ?>
							var paramKey = WFAD.base64_encode(param + '[' + paramName + ']');
							var pathKey = WFAD.base64_encode(url);
							var key = pathKey + '|' + paramKey;
							var matches = $('#waf-whitelisted-urls-wrapper .whitelist-table > tbody > tr[data-key="' + key + '"]');
							if (matches.length > 0) {
								WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), '<?php esc_attr_e('Whitelist Entry Exists', 'wordfence'); ?>', '<?php esc_attr_e('A whitelist entry for this URL and parameter already exists.', 'wordfence'); ?>');
								return;
							}
							
							//Generate entry and add to display data set
							var entry = {
								data: {
									description: "<?php esc_attr_e('Whitelisted via Firewall Options page', 'wordfence'); ?>",
									disabled: false,
									ip: "<?php echo esc_attr(wfUtils::getIP()); ?>",
									timestamp: Math.round(Date.now() / 1000),
									userID: <?php echo (int) $user->ID; ?>,
									username: "<?php echo esc_attr($user->user_login); ?>"
								},
								paramKey: paramKey,
								path: pathKey,
								ruleID: ['all'],
								adding: true
							};
							WFAD.wafData.whitelistedURLParams.push(entry);

							//Add to change list
							if (!(WFAD.pendingChanges['whitelistedURLParams'] instanceof Object)) {
								WFAD.pendingChanges['whitelistedURLParams'] = {};
							}

							if (!(WFAD.pendingChanges['whitelistedURLParams']['add'] instanceof Object)) {
								WFAD.pendingChanges['whitelistedURLParams']['add'] = {};
							}

							WFAD.pendingChanges['whitelistedURLParams']['add'][key] = entry;
							WFAD.updatePendingChanges();
							
							//Reload and reset add form
							var whitelistedIPsEl = $('#waf-whitelisted-urls-tmpl').tmpl(WFAD.wafData);
							$('#waf-whitelisted-urls-wrapper').html(whitelistedIPsEl);
							$(window).trigger('wordfenceWAFInstallWhitelistEventHandlers');

							inputURL.val('');
							inputParamName.val('');
						}
					});
				});
			})(jQuery);
		</script>
	</li>
	<li><hr id="whitelist-form-separator"></li>
	<li id="whitelist-table-controls" class="wf-flex-horizontal wf-flex-vertical-xs wf-flex-full-width">
		<div><a href="#" id="whitelist-bulk-delete" class="wf-btn wf-btn-callout wf-btn-default">Delete</a>&nbsp;&nbsp;<a href="#" id="whitelist-bulk-enable" class="wf-btn wf-btn-callout wf-btn-default">Enable</a>&nbsp;&nbsp;<a href="#" id="whitelist-bulk-disable" class="wf-btn wf-btn-callout wf-btn-default">Disable</a></div>
		<div class="wf-right wf-left-xs wf-padding-add-top-xs-small">
			<div class="wf-select-group wf-flex-vertical-xs wf-flex-full-width">
				<select name="filterColumn">
					<option value="url">URL</option>
					<option value="param">Param</option>
					<option value="source">Source</option>
					<option value="user">User</option>
					<option value="ip">IP</option>
				</select>
				<input type="text" class="wf-form-control" placeholder="Filter Value" name="filterValue">
				<div><span class="wf-hidden-xs">&nbsp;&nbsp;</span><a href="#" id="whitelist-apply-filter" class="wf-btn wf-btn-callout wf-btn-default">Filter</a></div>
			</div>
			<script type="application/javascript">
				(function($) {
					$(function() {
						$('#whitelist-apply-filter').on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							$(window).trigger('wordfenceWAFApplyWhitelistFilter');
						});
					});
				})(jQuery);
			</script>
		</div>
	</li>
	<li>
		<div id="waf-whitelisted-urls-wrapper"></div>
	</li>
</ul>
<script type="application/javascript">
	(function($) {
		$(function() {
			$('#whitelistParam').wfselect2({
				minimumResultsForSearch: -1,
				templateSelection: function(item) {
					return 'Param Type: ' + item.text;
				}
			});
			
			$('#whitelist-table-controls select').wfselect2({
				minimumResultsForSearch: -1,
				placeholder: "Filter By",
				width: '200px',
				templateSelection: function(item) {
					return 'Filter By: ' + item.text;
				}
			});
			
			$('#whitelist-bulk-delete').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				WFAD.wafWhitelistedBulkDelete();
				WFAD.updatePendingChanges();
				var whitelistedIPsEl = $('#waf-whitelisted-urls-tmpl').tmpl(WFAD.wafData);
				$('#waf-whitelisted-urls-wrapper').html(whitelistedIPsEl);
				$(window).trigger('wordfenceWAFInstallWhitelistEventHandlers');
			});

			$('#whitelist-bulk-enable').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				WFAD.wafWhitelistedBulkChangeEnabled(true);
				WFAD.updatePendingChanges();
			});

			$('#whitelist-bulk-disable').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				WFAD.wafWhitelistedBulkChangeEnabled(false);
				WFAD.updatePendingChanges();
			});
		});
	})(jQuery);
</script> 