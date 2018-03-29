<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the list of blocks.
 *
 */
?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-flex-horizontal wf-flex-full-width wf-add-bottom-small">
			<h3 class="wf-no-top wf-no-bottom"><?php printf(__('All blocks<span class="wf-hidden-xs"> for %s</span>', 'wordfence'), preg_replace('/^https?:\/\//i', '', wfUtils::wpSiteURL())); ?></h3>
			<div class="wf-right">
				<div class="wf-inline-block">
				<ul class="wf-option wf-option-toggled-boolean-switch wf-option-no-spacing" data-option="displayAutomaticBlocks" data-enabled-value="1" data-disabled-value="0" data-original-value="<?php echo wfConfig::get('displayAutomaticBlocks') ? 1 : 0; ?>">
					<li class="wf-boolean-switch<?php echo wfConfig::get('displayAutomaticBlocks') ? ' wf-active' : ''; ?>"><a href="#" class="wf-boolean-switch-handle"></a></li>
					<li class="wf-option-title wf-padding-add-left wf-no-right wf-padding-no-right">
						<?php echo __('Show<span class="wf-hidden-xs"> Wordfence</span> Automatic<span class="wf-hidden-xs"> Blocks</span>', 'wordfence'); ?> 
					</li>
				</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-row">
			<div class="wf-col-xs-12">
				<div class="wf-block wf-block-no-header wf-active">
					<div class="wf-block-content wf-padding-add-top-large wf-padding-add-bottom-large">
						<ul class="wf-flex-horizontal wf-flex-vertical-xs wf-flex-full-width wf-no-top">
							<li class="wf-padding-add-bottom-xs">
								<ul class="wf-flex-horizontal wf-flex-full-width wf-no-top" style="display: none !important;">
									<li><input type="text" placeholder="<?php esc_attr_e('Filter', 'wordfence'); ?>" id="wf-blocks-filter-field" class="wf-input-text"></li>
									<li class="wf-padding-add-left-medium"><a href="#" id="wf-blocks-apply-filter" class="wf-btn wf-btn-callout wf-btn-default"><?php _e('Filter', 'wordfence'); ?></a></li>
								</ul>
							</li>
							<li class="wf-right wf-flex-vertical-xs">
								<a href="#" id="blocks-bulk-unblock" class="wf-btn wf-btn-callout wf-btn-default"><?php _e('Unblock', 'wordfence'); ?></a>&nbsp;&nbsp;<a href="#" id="blocks-bulk-make-permanent" class="wf-btn wf-btn-callout wf-btn-default"><?php _e('Make Permanent', 'wordfence'); ?></a>&nbsp;&nbsp;<a href="<?php echo wfUtils::siteURLRelative(); ?>?_wfsf=blockedIPs&amp;nonce=<?php echo wp_create_nonce('wp-ajax'); ?>" id="blocks-export-ips" class="wf-btn wf-btn-callout wf-btn-default"><?php _e('Export<span class="wf-hidden-xs"> All IPs</span>', 'wordfence'); ?></a>
							</li>
						</ul>
						<div class="wf-block wf-block-no-padding wf-block-no-header wf-active wf-no-bottom wf-overflow-y-auto-xs">
							<div class="wf-block-content">
								<div id="wf-blocks-wrapper"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> <!-- end block list -->
<script type="text/x-jquery-template" id="wf-blocks-tmpl">
	<div class="wf-blocks-table-container">
		<table class="wf-striped-table wf-blocks-table">
			<thead>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</script>
<script type="text/x-jquery-template" id="wf-blocks-columns-tmpl">
	<tr class="wf-blocks-columns">
		<th style="width: 2%;text-align: center"><div class="wf-blocks-bulk-select wf-option-checkbox"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div></th>
		<th data-column="type" class="wf-sortable wf-unsorted"><?php _e('Block Type', 'wordfence'); ?> <i class="wf-sorted-ascending wf-ion-android-arrow-dropup" aria-hidden="true"></i><i class="wf-sorted-descending wf-ion-android-arrow-dropdown" aria-hidden="true"></i></th>
		<th data-column="detail" class="wf-sortable wf-unsorted"><?php _e('Detail', 'wordfence'); ?> <i class="wf-sorted-ascending wf-ion-android-arrow-dropup" aria-hidden="true"></i><i class="wf-sorted-descending wf-ion-android-arrow-dropdown" aria-hidden="true"></i></th>
		<th data-column="ruleAdded" class="wf-sortable wf-unsorted"><?php _e('Rule Added', 'wordfence'); ?> <i class="wf-sorted-ascending wf-ion-android-arrow-dropup" aria-hidden="true"></i><i class="wf-sorted-descending wf-ion-android-arrow-dropdown" aria-hidden="true"></i></th>
		<th data-column="reason" class="wf-sortable wf-unsorted"><?php _e('Reason', 'wordfence'); ?> <i class="wf-sorted-ascending wf-ion-android-arrow-dropup" aria-hidden="true"></i><i class="wf-sorted-descending wf-ion-android-arrow-dropdown" aria-hidden="true"></i></th>
		<th data-column="expiration" class="wf-sortable wf-unsorted"><?php _e('Expiration', 'wordfence'); ?> <i class="wf-sorted-ascending wf-ion-android-arrow-dropup" aria-hidden="true"></i><i class="wf-sorted-descending wf-ion-android-arrow-dropdown" aria-hidden="true"></i></th>
		<th data-column="blockCount" class="wf-sortable wf-unsorted"><?php _e('Block Count', 'wordfence'); ?> <i class="wf-sorted-ascending wf-ion-android-arrow-dropup" aria-hidden="true"></i><i class="wf-sorted-descending wf-ion-android-arrow-dropdown" aria-hidden="true"></i></th>
		<th data-column="lastAttempt" class="wf-sortable wf-unsorted"><?php _e('Last Attempt', 'wordfence'); ?> <i class="wf-sorted-ascending wf-ion-android-arrow-dropup" aria-hidden="true"></i><i class="wf-sorted-descending wf-ion-android-arrow-dropdown" aria-hidden="true"></i></th>
	</tr>
</script>
<script type="text/x-jquery-template" id="wf-no-blocks-tmpl">
	<tr id="wf-no-blocks">
		<td colspan="8"><?php _e('No blocks are currently active.', 'wordfence'); ?></td>
	</tr>
</script>
<script type="text/x-jquery-template" id="wf-no-filtered-blocks-tmpl">
	<tr id="wf-no-blocks">
		<td colspan="8"><?php _e('No blocks match the current filter.', 'wordfence'); ?></td>
	</tr>
</script>
<script type="text/x-jquery-template" id="wf-blocks-loading-tmpl">
	<tr id="wf-blocks-loading">
		<td colspan="8" class="wf-center wf-padding-add-top wf-padding-add-bottom">
			<?php
			echo wfView::create('common/indeterminate-progress', array(
				'size' => 50,
			))->render();
			?>
		</td>
	</tr>
</script>
<script type="text/x-jquery-template" id="wf-block-row-tmpl">
	<tr class="wf-block-record" data-id="${id}" data-expiration="${expiration}">
		<td style="text-align: center;"><div class="wf-blocks-table-bulk-checkbox wf-option-checkbox"><i class="wf-ion-ios-checkmark-empty" aria-hidden="true"></i></div></td>
		<td data-column="type" data-sort="${typeSort}">${typeDisplay}</td>
		<td data-column="detail" data-sort="${detailSort}">${detailDisplay}{{if (editable)}}&nbsp;<a href="#" class="wf-block-edit" data-edit-type="${editType}" data-edit-values="${editValues}"><i class="wf-ion-edit" aria-hidden="true"></i></a>{{/if}}</td>
		<td data-column="ruleAdded" data-sort="${ruleAddedSort}">${ruleAddedDisplay}</td>
		<td data-column="reason" data-sort="${reasonSort}" class="wf-split-word">${reasonDisplay}</td>
		<td data-column="expiration" data-sort="${expirationSort}">${expirationDisplay}</td>
		<td data-column="blockCount" data-sort="${blockCountSort}">${blockCountDisplay}</td>
		<td data-column="lastAttempt" data-sort="${lastAttemptSort}">${lastAttemptDisplay}</td>
	</tr>
</script>
<script type="application/javascript">
	(function($) {
		WFAD.blockHeaderCheckboxAction = function(checkbox) { //Top-level checkboxes
			$('.wf-blocks-bulk-select.wf-option-checkbox').toggleClass('wf-checked');
			var checked = $(checkbox).hasClass('wf-checked');
			$('.wf-blocks-table-bulk-checkbox.wf-option-checkbox').toggleClass('wf-checked', checked);
			$(window).trigger('wordfenceUpdateBlockButtons');
		};
		
		
		$(window).on('wordfenceRefreshBlockList', function(e, payload, append) {
			if (!payload.hasOwnProperty('loading')) {
				payload['loading'] = false;
			}
			
			//Create table if needed
			var table = $(".wf-blocks-table-container");
			if (table.length == 0) {
				var wrapperTemplate = $('#wf-blocks-tmpl').tmpl();
				$('#wf-blocks-wrapper').append(wrapperTemplate);
				table = $(".wf-blocks-table-container");
			}
			
			if (!append) {
				table.find('.wf-block-record').remove();
			}
			
			//Create header if needed
			if (table.find('thead > .wf-blocks-columns').length == 0) {
				table.find('thead').append($('#wf-blocks-columns-tmpl').tmpl());
				table.find('thead .wf-blocks-bulk-select.wf-option-checkbox').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					WFAD.blockHeaderCheckboxAction($(this));
				});
				table.find('thead > .wf-blocks-columns > .wf-sortable').on('click', function(e, initialState) {
					e.preventDefault();
					e.stopPropagation();

					var column = $(this).data('column');
					if ($(this).hasClass('wf-sorted-ascending')) {
						table.find('.wf-blocks-columns > .wf-sortable.wf-sorted-ascending').each(function() {
							$(this).removeClass('wf-sorted-ascending');
							$(this).addClass('wf-sorted-descending');
						});
						if (!initialState) {
							WFAD.sortColumn = column;
							WFAD.sortDirection = 'descending';
							$(window).trigger('wordfenceLoadBlocks', [true]);
						}
					}
					else if ($(this).hasClass('wf-sorted-descending')) {
						table.find('.wf-blocks-columns > .wf-sortable.wf-sorted-descending').each(function() {
							$(this).removeClass('wf-sorted-descending');
							$(this).addClass('wf-sorted-ascending');
						});
						if (!initialState) {
							WFAD.sortColumn = column;
							WFAD.sortDirection = 'ascending';
							$(window).trigger('wordfenceLoadBlocks', [true]);
						}
					}
					else {
						table.find('.wf-blocks-columns > .wf-sortable').removeClass('wf-sorted-descending').removeClass('wf-sorted-ascending').addClass('wf-unsorted');
						var column = $(this).data('column');
						$(this).removeClass('wf-unsorted').addClass('wf-sorted-ascending');
						table.find('tfoot > .wf-blocks-columns > .wf-sortable[data-column="' + column + '"]').removeClass('wf-unsorted').addClass('wf-sorted-ascending');
						if (!initialState) {
							WFAD.sortColumn = column;
							WFAD.sortDirection = 'ascending';
							$(window).trigger('wordfenceLoadBlocks', [true]);
						}
					}
				});
			}
			
			//Create or remove footer if needed
			var loadedBlockCount = $('.wf-block-record').length + payload['blocks'].length;
			if (loadedBlockCount > 5 && table.find('tfoot > .wf-blocks-columns').length == 0) {
				table.find('tfoot').append($('#wf-blocks-columns-tmpl').tmpl());
				table.find('tfoot .wf-blocks-bulk-select.wf-option-checkbox').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					WFAD.blockHeaderCheckboxAction($(this));
				});
				table.find('tfoot > .wf-blocks-columns > .wf-sortable').on('click', function(e, initialState) {
					e.preventDefault();
					e.stopPropagation();

					var column = $(this).data('column');
					if ($(this).hasClass('wf-sorted-ascending')) {
						table.find('.wf-blocks-columns > .wf-sortable.wf-sorted-ascending').each(function() {
							$(this).removeClass('wf-sorted-ascending');
							$(this).addClass('wf-sorted-descending');
						});
						if (!initialState) {
							WFAD.sortColumn = column;
							WFAD.sortDirection = 'descending';
							$(window).trigger('wordfenceLoadBlocks', [true]);
						}
					}
					else if ($(this).hasClass('wf-sorted-descending')) {
						table.find('.wf-blocks-columns > .wf-sortable.wf-sorted-descending').each(function() {
							$(this).removeClass('wf-sorted-descending');
							$(this).addClass('wf-sorted-ascending');
						});
						if (!initialState) {
							WFAD.sortColumn = column;
							WFAD.sortDirection = 'ascending';
							$(window).trigger('wordfenceLoadBlocks', [true]);
						}
					}
					else {
						table.find('.wf-blocks-columns > .wf-sortable').removeClass('wf-sorted-descending').removeClass('wf-sorted-ascending').addClass('wf-unsorted');
						$(this).removeClass('wf-unsorted').addClass('wf-sorted-ascending');
						table.find('thead > .wf-blocks-columns > .wf-sortable[data-column="' + column + '"]').removeClass('wf-unsorted').addClass('wf-sorted-ascending');
						if (!initialState) {
							WFAD.sortColumn = column;
							WFAD.sortDirection = 'ascending';
							$(window).trigger('wordfenceLoadBlocks', [true]);
						}
					}
				});
			}
			else if (loadedBlockCount > 5) {
				//Do nothing
			}
			else {
				table.find('tfoot > .wf-blocks-columns').remove();
			}
			
			//Add row(s)
			$('#wf-blocks-loading').remove();
			if (!append && payload['blocks'].length == 0) {
				if (!payload['loading'] && $('#wf-no-blocks').length == 0) {
					if (!!WFAD.blocksFilter) {
						table.find('tbody').append($('#wf-no-filtered-blocks-tmpl').tmpl());
					}
					else {
						table.find('tbody').append($('#wf-no-blocks-tmpl').tmpl());
					}
				}
			}
			else {
				$('#wf-no-blocks').remove();
				for (var i = 0; i < payload['blocks'].length; i++) {
					var row = $('#wf-block-row-tmpl').tmpl(payload['blocks'][i]);

					row.find('.wf-blocks-table-bulk-checkbox.wf-option-checkbox').on('click', function() { //Individual checkboxes
						e.preventDefault();
						e.stopPropagation();

						$(this).toggleClass('wf-checked');
						$(window).trigger('wordfenceUpdateBulkSelect');
						$(window).trigger('wordfenceUpdateBlockButtons');
					});

					row.find('.wf-block-edit').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var editType = $(this).data('editType');
						$('#wf-block-type > li > a[data-value="' + editType + '"]').trigger('click');
						if ($('#wf-block-parameters-title').offset().top < $(window).scrollTop()) {
							$("body,html").animate({
								scrollTop: $('#wf-block-parameters-title').offset().top
							}, 800);
						}
					});
					
					var existing = table.find('tbody tr[data-id="' + payload['blocks'][i]['id'] + '"]');
					if (existing.length > 0) {
						existing.replaceWith(row);
					}
					else {
						table.find('tbody').append(row);
					}
				}
			}
			
			try {
				$('#wf-blocks-wrapper').data('hasCountryBlock', JSON.parse(payload.hasCountryBlock));
			}
			catch (e) {
				$('#wf-blocks-wrapper').data('hasCountryBlock', '');
			}
			
			if (table.find('.wf-blocks-columns > .wf-sortable.wf-sorted-ascending, .wf-blocks-columns > .wf-sortable.wf-sorted-descending').length == 0) {
				table.find('thead > .wf-blocks-columns > .wf-sortable[data-column="type"]').trigger('click', [true]);
			}

			$(window).trigger('wordfenceUpdateBlockButtons');
		});

		$(window).on('wordfenceUpdateBlockButtons', function() {
			var totalCount = $('.wf-blocks-table-bulk-checkbox.wf-option-checkbox').length;
			var checked = $('.wf-blocks-table-bulk-checkbox.wf-option-checkbox.wf-checked');
			var allowUnblock = false;
			var allowMakeForever = false;
			for (var i = 0; i < checked.length; i++) {
				var tr = $(checked[i]).closest('tr');
				if (tr.is(':visible')) {
					allowUnblock = true;
					if (tr.data('expiration') > 0) {
						allowMakeForever = true;
					}
				}
			}
			
			$('#blocks-bulk-unblock').toggleClass('wf-disabled', !allowUnblock);
			$('#blocks-bulk-make-permanent').toggleClass('wf-disabled', !allowMakeForever);
			$('#blocks-export-ips').toggleClass('wf-disabled', (totalCount == 0));
		});

		$(window).on('wordfenceUpdateBulkSelect', function() {
			var totalCount = $('.wf-blocks-table-bulk-checkbox.wf-option-checkbox:visible').length;
			var checkedCount = $('.wf-blocks-table-bulk-checkbox.wf-option-checkbox.wf-checked:visible').length;
			$('.wf-blocks-bulk-select.wf-option-checkbox:visible').toggleClass('wf-checked', (totalCount > 0 && checkedCount == totalCount));
		});

		$(window).on('wordfenceLoadBlocks', function(e, reload) {
			var offset = reload ? 0 : $('.wf-block-record').length;
			
			WFAD.loadingBlocks = true;
			WFAD.ajax('wordfence_getBlocks', {offset: offset, sortColumn: WFAD.sortColumn, sortDirection: WFAD.sortDirection, blocksFilter: WFAD.blocksFilter}, function(res) {
				$(window).trigger('wordfenceRefreshBlockList', [res, !reload]);
				WFAD.loadingBlocks = false;
			});
		});
		
		$(function() {
			WFAD.sortColumn = 'type';
			WFAD.sortDirection = 'ascending';
			$(window).trigger('wordfenceRefreshBlockList', [{blocks: [], loading: true}, false]);
			$(window).trigger('wordfenceLoadBlocks', [true]);

			var issuesWrapper = $('#wf-blocks-wrapper');
			var hasScrolled = false;
			$(window).on('scroll', function() {
				var win = $(this);
				var currentScrollBottom = win.scrollTop() + window.innerHeight;
				var scrollThreshold = issuesWrapper.outerHeight() + issuesWrapper.offset().top;
				if (hasScrolled && !WFAD.loadingBlocks && currentScrollBottom >= scrollThreshold) {
					hasScrolled = false;
					$(window).trigger('wordfenceLoadBlocks', [false]);
				}
				else if (currentScrollBottom < scrollThreshold) {
					hasScrolled = true;
				}
			});

			$('#wf-blocks-filter-field').on('keypress', function(e) {
				if (e.which == 13) {
					$('#wf-blocks-apply-filter').trigger('click');
					return false;
				}
			}).on('keyup', function(e) {
				var currentValue = $('#wf-blocks-filter-field').val() || '';
				if (!WFAD.blocksFilter) {
					$('#wf-blocks-apply-filter').text('<?php _e('Filter', 'wordfence'); ?>').data('filterMode', '');
				}
				else if (currentValue == '' || currentValue == WFAD.blocksFilter) {
					$('#wf-blocks-apply-filter').text('<?php _e('Clear Filter', 'wordfence'); ?>').data('filterMode', 'filtered');
				}
				else {
					$('#wf-blocks-apply-filter').text('<?php _e('Change Filter', 'wordfence'); ?>').data('filterMode', 'pendingChange');
				}
			});
			
			$('#wf-blocks-apply-filter').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				var mode = $('#wf-blocks-apply-filter').data('filterMode') || '';
				if (mode != 'filtered') {
					WFAD.blocksFilter = $('#wf-blocks-filter-field').val() || '';
				}
				else {
					WFAD.blocksFilter = '';
					$('#wf-blocks-filter-field').val('')
				}

				$('#wf-blocks-filter-field').trigger('keyup');
				$(window).trigger('wordfenceLoadBlocks', [true]);
			});
			
			$('#blocks-bulk-unblock').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var totalCount = $('.wf-blocks-table-bulk-checkbox.wf-option-checkbox:visible').length;
				var checked = $('.wf-blocks-table-bulk-checkbox.wf-option-checkbox.wf-checked:visible');
				var checkedCount = checked.length;
				var removingCountryBlock = false;
				var blockIDs = [];
				var rows = [];
				for (var i = 0; i < checked.length; i++) {
					var tr = $(checked[i]).closest('tr');
					rows.push(tr);
					blockIDs.push(tr.data('id'));
					
					if (tr.find('td[data-column="type"]').data('sort') == <?php echo (int) wfBlock::TYPE_COUNTRY; ?>) {
						removingCountryBlock = true;
					}
				}

				var prompt = $('#wfTmpl_unblockPrompt').tmpl({count: checkedCount});
				var promptHTML = $("<div />").append(prompt).html();
				WFAD.colorboxHTML('400px', promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
					$('#wf-blocking-prompt-cancel').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();
						
						WFAD.colorboxClose();
					});

					$('#wf-blocking-prompt-unblock').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFAD.loadingBlocks = true;
						WFAD.ajax('wordfence_deleteBlocks', {blocks: JSON.stringify(blockIDs), sortColumn: WFAD.sortColumn, sortDirection: WFAD.sortDirection, blocksFilter: WFAD.blocksFilter}, function(res) {
							WFAD.loadingBlocks = false;
							if (totalCount == checkedCount) {
								$(window).trigger('wordfenceRefreshBlockList', [res, false]); //Everything deleted, just reload it
							}
							else {
								for (var i = 0; i < rows.length; i++) {
									$(rows[i]).remove();
								}
								
								if (removingCountryBlock) {
									$('#wf-blocks-wrapper').data('hasCountryBlock', '');
								}
								
								$(window).trigger('wordfenceUpdateBulkSelect');
								$(window).trigger('wordfenceUpdateBlockButtons');
							}

							WFAD.colorboxClose();
						});
					});
				}});
			});
			
			$('#blocks-bulk-make-permanent').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var checked = $('.wf-blocks-table-bulk-checkbox.wf-option-checkbox.wf-checked:visible');
				var updateIDs = [];
				for (var i = 0; i < checked.length; i++) {
					var tr = $(checked[i]).closest('tr');
					if (tr.is(':visible')) {
						updateIDs.push(tr.data('id'));
					}
				}

				WFAD.loadingBlocks = true;
				WFAD.ajax('wordfence_makePermanentBlocks', {updates: JSON.stringify(updateIDs), sortColumn: WFAD.sortColumn, sortDirection: WFAD.sortDirection, blocksFilter: WFAD.blocksFilter}, function(res) {
					WFAD.loadingBlocks = false;
					$(window).trigger('wordfenceRefreshBlockList', [res, false]);
				});
			});

			$('.wf-option.wf-option-toggled-boolean-switch[data-option="displayAutomaticBlocks"]').on('change', function() {
				delete WFAD.pendingChanges['displayAutomaticBlocks'];
				var isOn = $(this).find('.wf-boolean-switch').hasClass('wf-active');
				WFAD.setOption($(this).data('option'), (isOn ? $(this).data('enabledValue') : $(this).data('disabledValue')), function() {
					$(window).trigger('wordfenceLoadBlocks', [true]);
				});
			});
		});
	})(jQuery);
</script>
<script type="text/x-jquery-template" id="wfTmpl_unblockPrompt">
<?php
echo wfView::create('common/modal-prompt', array(
	'title' => __('Unblocking', 'wordfence'),
	'message' => '{{if count == 1}}' . __('Are you sure you want to stop blocking the selected IP, range, or country?') . ' {{else}}' . __('Are you sure you want to stop blocking the ${count} selected IPs, ranges, and countries?') . '{{/if}}',
	'primaryButton' => array('id' => 'wf-blocking-prompt-cancel', 'label' => __('Cancel', 'wordfence'), 'link' => '#'),
	'secondaryButtons' => array(array('id' => 'wf-blocking-prompt-unblock', 'label' => __('Unblock', 'wordfence'), 'link' => '#')),
))->render();
?>
</script>