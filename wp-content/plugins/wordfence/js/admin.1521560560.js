(function($) {
	if (!window['wordfenceAdmin']) { //To compile for checking: java -jar /usr/local/bin/closure.jar --js=admin.js --js_output_file=test.js
		window['wordfenceAdmin'] = {
			isSmallScreen: false,
			loading16: '<div class="wfLoading16"></div>',
			loadingCount: 0,
			dbCheckTables: [],
			dbCheckCount_ok: 0,
			dbCheckCount_skipped: 0,
			dbCheckCount_errors: 0,
			issues: [],
			ignoreData: false,
			iconErrorMsgs: [],
			scanIDLoaded: 0,
			colorboxQueue: [],
			mode: '',
			visibleIssuesPanel: 'new',
			preFirstScanMsgsLoaded: false,
			newestActivityTime: 0, //must be 0 to force loading of all initially
			elementGeneratorIter: 1,
			reloadConfigPage: false,
			nonce: false,
			tickerUpdatePending: false,
			activityLogUpdatePending: false,
			lastALogCtime: 0,
			lastIssueUpdateTime: 0,
			activityQueue: [],
			totalActAdded: 0,
			maxActivityLogItems: 1000,
			scanReqAnimation: false,
			debugOn: false,
			blockedCountriesPending: [],
			ownCountry: "",
			schedStartHour: false,
			currentPointer: false,
			countryMap: false,
			countryCodesToSave: "",
			performanceScale: 3,
			performanceMinWidth: 20,
			_windowHasFocus: true,
			serverTimestampOffset: 0,
			serverMicrotime: 0,
			wfLiveTraffic: null,
			loadingBlockedIPs: false,
			scanRunning: false,
			basePageName: '',
			pendingChanges: {},
			scanFailed: false,
			siteCleaningIssueTypes: ['file', 'checkGSB', 'checkSpamIP', 'commentBadURL', 'dnsChange', 'knownfile', 'optionBadURL', 'postBadTitle', 'postBadURL', 'spamvertizeCheck', 'suspiciousAdminUsers'],

			init: function() {
				this.isSmallScreen = window.matchMedia("only screen and (max-width: 500px)").matches;
				
				this.nonce = WordfenceAdminVars.firstNonce;
				this.debugOn = WordfenceAdminVars.debugOn == '1' ? true : false;
				this.scanRunning = WordfenceAdminVars.scanRunning == '1' ? true : false;
				this.basePageName = document.title;
				var startTicker = false;
				var self = this;

				$(window).on('blur', function() {
					self._windowHasFocus = false;
				}).on('focus', function() {
					self._windowHasFocus = true;
				}).focus();

				$('.do-show').click(function() {
					var $this = $(this);
					$this.hide();
					$($this.data('selector')).show();
					return false;
				});
				
				$('.downloadLogFile').each(function() {
					$(this).attr('href', WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadLogFile&nonce=' + WFAD.nonce + '&logfile=' + encodeURIComponent($(this).data('logfile')));
				});

				$('#doSendEmail').click(function() {
					var ticket = $('#_ticketnumber').val();
					if (ticket === null || typeof ticket === "undefined" || ticket.length == 0) {
						self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "Error", "Please include your support ticket number or forum username.");
						return;
					}
					WFAD.ajax('wordfence_sendDiagnostic', {email: $('#_email').val(), ticket: ticket}, function(res) {
						if (res.result) {
							self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "Email Diagnostic Report", "Diagnostic report has been sent successfully.");
						} else {
							self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "Error", "There was an error while sending the email.");
						}
					});
				});

				$('#sendByEmail').click(function() {
					$('#sendByEmailForm').removeClass('hidden');
					$(this).hide();
				});

				$('#expandAllDiagnostics').click(function() {
					$('#wf-diagnostics').find('.wf-block').each(function() {
						var el = $(this);
						if (!el.hasClass('wf-active')) {
							el.find('.wf-block-header').trigger('click');
						}
					})
				});

				$(window).bind("scroll", function() {
					$(this).scrollTop() > 200 ? $(".wf-scrollTop").fadeIn() : $(".wf-scrollTop").fadeOut()
				});
				$(".wf-scrollTop").click(function(e) {
					return e.stopPropagation(), $("body,html").animate({
						scrollTop: 0
					}, 800), !1;
				});

				var tabs = jQuery('.wf-page-tabs').find('.wf-tab a');
				if (tabs.length > 0) {
					tabs.click(function() {
						jQuery('.wf-page-tabs').find('.wf-tab').removeClass('wf-active');
						jQuery('.wf-tab-content').removeClass('wf-active');
						
						var tab = jQuery(this).closest('.wf-tab');
						tab.addClass('wf-active');
						var content = jQuery('#' + tab.data('target'));
						content.addClass('wf-active');
						document.title = tab.data('pageTitle') + " \u2039 " + self.basePageName;
						self.sectionInit();
						$(window).trigger('wfTabChange', [tab.data('target')]);
					});
					if (window.location.hash) {
						var hashes = window.location.hash.split('#');
						var hash = hashes[hashes.length - 1];
						for (var i = 0; i < tabs.length; i++) {
							if (hash == jQuery(tabs[i]).closest('.wf-tab').data('target')) {
								jQuery(tabs[i]).trigger('click');
							}
						}
					}
					else {
						jQuery(tabs[0]).trigger('click');
					}
					jQuery(window).on('hashchange', function () {
						var hashes = window.location.hash.split('#');
						var hash = hashes[hashes.length - 1];
						for (var i = 0; i < tabs.length; i++) {
							if (hash == jQuery(tabs[i]).closest('.wf-tab').data('target')) {
								jQuery(tabs[i]).trigger('click');
							}
						}
					});
				}
				else {
					this.sectionInit();
				}
				
				if (this.mode) {
					jQuery(document).bind('cbox_closed', function() {
						self.colorboxIsOpen = false;
						self.colorboxServiceQueue();
					});
				}
				
				if ($('.wf-options-controls-spacer').length) { //The WP code doesn't move update nags and we need to
					$('.update-nag, #update-nag').insertAfter($('.wf-options-controls-spacer'));
				}
				
				$('.wf-block-header-action-disclosure').each(function() {
					$(this).closest('.wf-block-header').css('cursor', 'pointer');
					$(this).closest('.wf-block-header').on('click', function(e) {
						// Let links in the header work.
						if (e.target && e.target.nodeName === 'A' && e.target.href) {
							return;
						}
						e.preventDefault();
						e.stopPropagation();
						
						if ($(this).closest('.wf-block').hasClass('wf-disabled')) {
							return;
						}
						
						var isActive = $(this).closest('.wf-block').hasClass('wf-active');
						if (isActive) {
							//$(this).closest('.wf-block').removeClass('wf-active');
							$(this).closest('.wf-block').find('.wf-block-content').slideUp({
								always: function() {
									$(this).closest('.wf-block').removeClass('wf-active');
								}
							});
						}
						else {
							//$(this).closest('.wf-block').addClass('wf-active');
							$(this).closest('.wf-block').find('.wf-block-content').slideDown({
								always: function() {
									$(this).closest('.wf-block').addClass('wf-active');
								}
							});
						}
						
						WFAD.ajax('wordfence_saveDisclosureState', {name: $(this).closest('.wf-block').data('persistenceKey'), state: !isActive}, function() {}, function() {}, true);
					});
				});
				
				//On/Off Option
				$('.wf-option.wf-option-toggled .wf-option-checkbox').each(function() {
					$(this).on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();
						
						var optionElement = $(this).closest('.wf-option');
						if (optionElement.hasClass('wf-option-premium') || optionElement.hasClass('wf-disabled')) {
							return;
						}
						
						var option = optionElement.data('option');
						var value = false;
						var isActive = $(this).hasClass('wf-checked');
						if (isActive) {
							$(this).removeClass('wf-checked');
							value = optionElement.data('disabledValue');
						}
						else {
							$(this).addClass('wf-checked');
							value = optionElement.data('enabledValue');
						}
						
						var originalValue = optionElement.data('originalValue');
						if (originalValue == value) {
							delete WFAD.pendingChanges[option];
						}
						else {
							WFAD.pendingChanges[option] = value;
						}

						$(optionElement).trigger('change', [false]);
						WFAD.updatePendingChanges();
					});
					
					$(this).parent().find('.wf-option-title').on('click', function(e) {
						var links = $(this).find('a');
						var buffer = 10;
						for (var i = 0; i < links.length; i++) {
							var t = $(links[i]).offset().top;
							var l = $(links[i]).offset().left;
							var b = t + $(links[i]).height();
							var r = l + $(links[i]).width();
							
							if (e.pageX > l - buffer && e.pageX < r + buffer && e.pageY > t - buffer && e.pageY < b + buffer) {
								return; 
							}
						}
						$(this).parent().find('.wf-option-checkbox').trigger('click');
					}).css('cursor', 'pointer');
				});

				//On/Off Boolean Switch Option
				$('.wf-option.wf-option-toggled-boolean-switch .wf-boolean-switch').each(function() {
					$(this).on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();
						
						$(this).find('.wf-boolean-switch-handle').trigger('click');
					});

					$(this).find('.wf-boolean-switch-handle').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var optionElement = $(this).closest('.wf-option');
						if (optionElement.hasClass('wf-option-premium') || optionElement.hasClass('wf-disabled')) {
							return;
						}

						var switchElement = $(this).closest('.wf-boolean-switch');
						var option = optionElement.data('option');
						var value = false;
						var isActive = switchElement.hasClass('wf-active');
						if (isActive) {
							switchElement.removeClass('wf-active');
							value = optionElement.data('disabledValue');
						}
						else {
							switchElement.addClass('wf-active');
							value = optionElement.data('enabledValue');
						}

						var originalValue = optionElement.data('originalValue');
						if (originalValue == value) {
							delete WFAD.pendingChanges[option];
						}
						else {
							WFAD.pendingChanges[option] = value;
						}

						$(optionElement).trigger('change', [false]);
						WFAD.updatePendingChanges();
					});

					$(this).parent().find('.wf-option-title').on('click', function(e) {
						var links = $(this).find('a');
						var buffer = 10;
						for (var i = 0; i < links.length; i++) {
							var t = $(links[i]).offset().top;
							var l = $(links[i]).offset().left;
							var b = t + $(links[i]).height();
							var r = l + $(links[i]).width();

							if (e.pageX > l - buffer && e.pageX < r + buffer && e.pageY > t - buffer && e.pageY < b + buffer) {
								return;
							}
						}
						$(this).parent().find('.wf-boolean-switch-handle').trigger('click');
					}).css('cursor', 'pointer');
				});

				//On/Off Segmented Option
				$('.wf-option.wf-option-toggled-segmented [type=radio]').each(function() {
					$(this).on('click', function(e) {
						var optionElement = $(this).closest('.wf-option');
						if (optionElement.hasClass('wf-option-premium') || optionElement.hasClass('wf-disabled')) {
							return;
						}

						var option = optionElement.data('option');
						var value = this.value;

						var originalValue = optionElement.data('originalValue');
						if (originalValue == value) {
							delete WFAD.pendingChanges[option];
						}
						else {
							WFAD.pendingChanges[option] = value;
						}

						$(optionElement).trigger('change', [false]);
						WFAD.updatePendingChanges();
					});
				});

				//On/Off Multiple Option
				$('.wf-option.wf-option-toggled-multiple .wf-option-checkbox').each(function() {
					$(this).on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var optionElement = $(this).closest('.wf-option');
						if (optionElement.hasClass('wf-option-premium') || optionElement.hasClass('wf-disabled')) {
							return;
						}

						var checkboxElement = $(this).closest('ul');
						var option = checkboxElement.data('option');
						var value = false;
						var isActive = $(this).hasClass('wf-checked');
						if (isActive) {
							$(this).removeClass('wf-checked');
							value = checkboxElement.data('disabledValue');
						}
						else {
							$(this).addClass('wf-checked');
							value = checkboxElement.data('enabledValue');
						}

						var originalValue = checkboxElement.data('originalValue');
						if (originalValue == value) {
							delete WFAD.pendingChanges[option];
						}
						else {
							WFAD.pendingChanges[option] = value;
						}

						$(optionElement).trigger('change', [false]);
						WFAD.updatePendingChanges();
					});
				});

				//On/Off Option with menu and Option with menu
				$('.wf-option.wf-option-toggled-select .wf-option-checkbox').each(function() {
					$(this).on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var optionElement = $(this).closest('.wf-option');
						if (optionElement.hasClass('wf-option-premium') || optionElement.hasClass('wf-disabled')) {
							return;
						}
						
						var selectElement = optionElement.find('.wf-option-select select');
						var option = optionElement.data('toggleOption');
						var value = false;
						var isActive = $(this).hasClass('wf-checked');
						if (isActive) {
							$(this).removeClass('wf-checked');
							selectElement.attr('disabled', true);
							value = optionElement.data('disabledToggleValue');
						}
						else {
							$(this).addClass('wf-checked');
							selectElement.attr('disabled', false);
							value = optionElement.data('enabledToggleValue');
						}

						var originalValue = optionElement.data('originalToggleValue');
						if (originalValue == value) {
							delete WFAD.pendingChanges[option];
						}
						else {
							WFAD.pendingChanges[option] = value;
						}

						$(optionElement).trigger('change', [false]);
						WFAD.updatePendingChanges();
					});
					
					$(this).parent().find('.wf-option-title').on('click', function(e) {
						var links = $(this).find('a');
						var buffer = 10;
						for (var i = 0; i < links.length; i++) {
							var t = $(links[i]).offset().top;
							var l = $(links[i]).offset().left;
							var b = t + $(links[i]).height();
							var r = l + $(links[i]).width();

							if (e.pageX > l - buffer && e.pageX < r + buffer && e.pageY > t - buffer && e.pageY < b + buffer) {
								return;
							}
						}
						$(this).closest('.wf-option').find('.wf-option-checkbox').trigger('click');
					}).css('cursor', 'pointer');
				});

				$('.wf-option.wf-option-toggled-select > .wf-option-content > ul > li.wf-option-select select, .wf-option.wf-option-select > .wf-option-content > ul > li.wf-option-select select, .wf-option.wf-option-select > li.wf-option-select select').each(function() {
					var width = WFAD.isSmallScreen ? '200px' : 'resolve';
					if ($(this).data('preferredWidth')) {
						width = $(this).data('preferredWidth');
					}
					
					$(this).wfselect2({
						minimumResultsForSearch: -1,
						width: width
					}).on('change', function () {
						var optionElement = $(this).closest('.wf-option');
						var option = optionElement.data('selectOption');
						var value = $(this).val();

						var originalValue = optionElement.data('originalSelectValue');
						if (originalValue == value) {
							delete WFAD.pendingChanges[option];
						}
						else {
							WFAD.pendingChanges[option] = value;
						}

						$(optionElement).trigger('change', [false]);
						WFAD.updatePendingChanges();
					});
				}).triggerHandler('change');
				
				//Text field option
				$('.wf-option.wf-option-text > .wf-option-content > ul > li.wf-option-text input').on('keyup', function() {
					var optionElement = $(this).closest('.wf-option');
					var option = optionElement.data('textOption');
					
					if (typeof option !== 'undefined') {
						var value = $(this).val();

						var originalValue = optionElement.data('originalTextValue');
						if (originalValue == value) {
							delete WFAD.pendingChanges[option];
						}
						else {
							WFAD.pendingChanges[option] = value;
						}

						$(optionElement).trigger('change', [false]);
						WFAD.updatePendingChanges();
					}
				});

				//Text area option
				$('.wf-option.wf-option-textarea > .wf-option-content > ul > li.wf-option-textarea textarea').on('keyup', function() {
					var optionElement = $(this).closest('.wf-option');
					var option = optionElement.data('textOption');
					var value = $(this).val();

					var originalValue = optionElement.data('originalTextValue');
					if (originalValue == value) {
						delete WFAD.pendingChanges[option];
					}
					else {
						WFAD.pendingChanges[option] = value;
					}

					$(optionElement).trigger('change', [false]);
					WFAD.updatePendingChanges();
				});

				//Value entry token option
				$('.wf-option.wf-option-token select').wfselect2({
					tags: true,
					tokenSeparators: [','],
					width: 'element',
					minimumResultsForSearch: -1,
					selectOnClose: true,
					matcher: function(params, data) {
						return null;
					}
				}).on('wfselect2:unselect', function(e){
					jQuery(e.params.data.element).remove();
				}).on('wfselect2:opening wfselect2:close', function(e){
					$('body').toggleClass('wf-select2-suppress-dropdown', e.type == 'wfselect2:opening');
				}).on('change', function () {
					var optionElement = $(this).closest('.wf-option');
					var option = optionElement.data('tokenOption');
					var value = $(this).val();
					if (!(value instanceof Array)) {
						value = [];
					}

					var selected = $(this).find('option:selected');
					var tagsElement = optionElement.find('.wf-option-token-tags');
					var list = $('<ul>');
					selected.each(function(index, value) {
						var li = $('<li class="wf-tag-selected"><a class="wf-destroy-tag-selected">×</a>' + $(value).text() + '</li>');
						li.children('a.wf-destroy-tag-selected')
							.off('click.wfselect2-copy')
							.on('click.wfselect2-copy', function(e) {
								var opt = $(this).data('wfselect2-opt');
								opt.attr('selected', false);
								opt.parents('select').trigger('change');
							}).data('wfselect2-opt', $(value));
						list.append(li);
					});
					tagsElement.html('').append(list);

					var originalValue = optionElement.data('originalTokenValue');
					var match = true;
					if (value.length != originalValue.length) {
						match = false;
					}
					else {
						value = value.sort();
						originalValue = originalValue.sort();
						for (var i = 0; i < value.length; i++) {
							if (value[i] !== originalValue[i]) {
								match = false;
							}
						}
					}
					if (match) {
						delete WFAD.pendingChanges[option];
					}
					else {
						WFAD.pendingChanges[option] = value;
					}

					$(optionElement).trigger('change', [false]);
					WFAD.updatePendingChanges();
				}).triggerHandler('change');

				$('.wf-option.wf-option-token select').each(function() { 
					$(this).data('wfselect2').$container.addClass('wf-select2-placeholder-fix wf-select2-hide-tags');
				});
				
				//Switch Option
				$('.wf-option.wf-option-switch .wf-switch > li').each(function(index, element) {
					$(element).on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var optionElement = $(this).closest('ul.wf-option-switch, div.wf-option-switch');
						var optionName = optionElement.data('optionName');
						var originalValue = optionElement.data('originalValue');
						var value = $(this).data('optionValue');

						var control = $(this).closest('.wf-switch');
						control.find('li').each(function() {
							$(this).toggleClass('wf-active', value == $(this).data('optionValue'));
						});

						if (originalValue == value) {
							delete WFAD.pendingChanges[optionName];
						}
						else {
							WFAD.pendingChanges[optionName] = value;
						}

						$(optionElement).trigger('change', [false]);
						WFAD.updatePendingChanges();
					});
				});

				$(document).focus();

				// (docs|support).wordfence.com GA links
				$(document).on('click', 'a', function() {
					if (this.href && this.href.indexOf('utm_source') > -1) {
						return;
					}
					var utm = '';
					if ((this.host == 'www.wordfence.com' || this.host == 'wordfence.com') && /^\/help(?:$|\/)/.test(this.pathname)) {
						utm = 'utm_source=plugin&utm_medium=pluginUI&utm_campaign=docsIcon';
					}
					if (utm) {
						utm = (this.search ? '&' : '?') + utm;
						this.href = this.protocol + '//' + this.host + this.pathname + this.search + utm + this.hash;
					}

					if (this.href == 'http://support.wordfence.com/') {
						this.href = 'https://support.wordfence.com/support/home?utm_source=plugin&utm_medium=pluginUI&utm_campaign=supportLink';
					}
				});
			},
			sectionInit: function() {
				var self = this;
				var startTicker = false;
				this.mode = false;
				if (jQuery('#wordfenceMode_dashboard:visible').length > 0) {
					this.mode = 'dashboard';
				} else if (jQuery('#wordfenceMode_scan:visible').length > 0) {
					this.mode = 'scan';
				} else if (jQuery('#wordfenceMode_waf:visible').length > 0) {
					this.mode = 'waf';
					startTicker = true;
				} else if (jQuery('#wordfenceMode_liveTraffic:visible').length > 0) {
					this.mode = 'liveTraffic';
					this.setupSwitches('wfLiveTrafficOnOff', 'liveTrafficEnabled', function() {
					});
					jQuery('#wfLiveTrafficOnOff').change(function() {
						self.updateSwitch('wfLiveTrafficOnOff', 'liveTrafficEnabled', function() {
							window.location.reload(true);
						});
					});

					startTicker = true;

				} else if (jQuery('#wordfenceMode_activity:visible').length > 0) {
					this.mode = 'activity';
					this.setupSwitches('wfLiveTrafficOnOff', 'liveTrafficEnabled', function() {
					});
					jQuery('#wfLiveTrafficOnOff').change(function() {
						self.updateSwitch('wfLiveTrafficOnOff', 'liveTrafficEnabled', function() {
							window.location.reload(true);
						});
					});

					if (WordfenceAdminVars.liveTrafficEnabled) {
						this.activityMode = 'hit';
					} else {
						this.activityMode = 'loginLogout';
						this.switchTab(jQuery('#wfLoginLogoutTab'), 'wfTab1', 'wfDataPanel', 'wfActivity_loginLogout', function() {
							WFAD.activityTabChanged();
						});
					}
					startTicker = true;
				} else if (jQuery('#wordfenceMode_options:visible').length > 0) {
					this.mode = 'options';
					this.updateTicker(true);
					startTicker = true;
				} else if (jQuery('#wordfenceMode_blockedIPs:visible').length > 0) {
					this.mode = 'blocked';
					this.staticTabChanged();
					this.updateTicker(true);
					startTicker = true;

					var self = this;
					var hasScrolled = false;
					$(window).on('scroll', function() {
						var win = $(this);
						var wrapper = $('#wfActivity_' + self.activityMode);
						// console.log(win.scrollTop() + window.innerHeight, liveTrafficWrapper.outerHeight() + liveTrafficWrapper.offset().top);
						var currentScrollBottom = win.scrollTop() + window.innerHeight;
						var scrollThreshold = wrapper.outerHeight() + wrapper.offset().top;
						if (hasScrolled && !self.loadingBlockedIPs && currentScrollBottom >= scrollThreshold) {
							// console.log('infinite scroll');
							hasScrolled = false;

							self.loadStaticPanelContent(true);
						} else if (currentScrollBottom < scrollThreshold) {
							hasScrolled = true;
							// console.log('no infinite scroll');
						}
					});
				} else if (jQuery('#wordfenceMode_twoFactor:visible').length > 0) {
					this.mode = 'twoFactor';
					startTicker = true;
					this.loadTwoFactor();

				} else if (jQuery('#wordfenceMode_countryBlocking:visible').length > 0) {
					this.mode = 'countryBlocking';
					startTicker = true;
				} else if (jQuery('#wordfenceMode_rangeBlocking:visible').length > 0) {
					this.mode = 'rangeBlocking';
					startTicker = true;
					this.calcRangeTotal();

				} else if (jQuery('#wordfenceMode_whois:visible').length > 0) {
					this.mode = 'whois';
					startTicker = true;
					this.calcRangeTotal();

				} else if (jQuery('#wordfenceMode_scanScheduling:visible').length > 0) {
					this.mode = 'scanScheduling';
					this.sched_modeChange();
				}
				
				if (this.mode) { //We are in a Wordfence page
					if (startTicker) {
						this.updateTicker();
						if (this.liveInt > 0) {
							clearInterval(this.liveInt);
							this.liveInt = 0;
						}
						this.liveInt = setInterval(function() {
							self.updateTicker();
						}, WordfenceAdminVars.actUpdateInterval);
					}
				}
			},
			sendTestEmail: function(email) {
				var self = this;
				this.ajax('wordfence_sendTestEmail', {email: email}, function(res) {
					if (res.result) {
						self.colorboxModalHTML((self.isSmallScreen ? '300px' : '400px'), "Test Email Sent", "Your test email was sent to the requested email address. The result we received from the WordPress wp_mail() function was: " +
							res.result + "<br /><br />A 'True' result means WordPress thinks the mail was sent without errors. A 'False' result means that WordPress encountered an error sending your mail. Note that it's possible to get a 'True' response with an error elsewhere in your mail system that may cause emails to not be delivered.");
					}
				});
			},
			updateSwitch: function(elemID, configItem, cb) {
				var setting = jQuery('#' + elemID).is(':checked');
				this.updateConfig(configItem, jQuery('#' + elemID).is(':checked') ? 1 : 0, cb);
			},
			setupSwitches: function(elemID, configItem, cb) {
				jQuery('.wfOnOffSwitch-checkbox').change(function() {
					jQuery.data(this, 'lastSwitchChange', (new Date()).getTime());
				});
				var self = this;
				jQuery('div.wfOnOffSwitch').mouseup(function() {
					var elem = jQuery(this);
					setTimeout(function() {
						var checkedElem = elem.find('.wfOnOffSwitch-checkbox');
						if ((new Date()).getTime() - jQuery.data(checkedElem[0], 'lastSwitchChange') > 300) {
							checkedElem.prop('checked', !checkedElem.is(':checked'));
							self.updateSwitch(elemID, configItem, cb);
						}
					}, 50);
				});
			},
			updateConfig: function(key, val, cb) {
				this.ajax('wordfence_updateConfig', {key: key, val: val}, function(ret) {
					if (cb) {
						cb(ret);
					}
				});
			},
			updateIPPreview: function(val, cb) {
				this.ajax('wordfence_updateIPPreview', val, function(ret) {
					if (cb) {
						cb(ret);
					}
				});
			},
			tourFinish: function(page) {
				if (WFAD.currentPointer) {
					WFAD.currentPointer.pointer('destroy');
					WFAD.currentPointer = false;
				}
				
				$('#wf-onboarding-tour-overlay').fadeOut();
				WFAD.ajax('wordfence_tourClosed', {page: page}, function(res) {});
			},
			tour: function(contentID, elemID, edge, align, previousCallback, nextCallback) {
				if (WFAD.currentPointer) {
					WFAD.currentPointer.pointer('destroy');
					WFAD.currentPointer = false;
				}
				
				var options = {
					pointerClass: 'wf-tour-pointer',
					buttons: function(event, t) {
						return null;
					},
					close: function() {
					},
					content: $('#' + contentID).tmpl().html(),
					pointerWidth: 700, 
					position: {
						edge: edge,
						align: align
					}
				};
				var element = $('#' + elemID);
				$('#wf-onboarding-tour-overlay').fadeIn();
				WFAD.currentPointer = element.pointer(options).pointer('open');
				
				if (previousCallback) {
					$('#wf-tour-previous a').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						previousCallback();
					});
				}
				
				if (nextCallback) {
					$('#wf-tour-continue a').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						nextCallback();
					});
				}
				
				$('#wf-tour-close').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					WFAD.tourComplete();
				});

				var wpPointer = $('.wf-tour-pointer');
				if (wpPointer.length > 0) {
					$('html, body').animate({
						scrollTop: wpPointer.offset().top - 100
					}, 1000);
				}
			},
			showLoading: function() {
				this.loadingCount++;
				if (this.loadingCount == 1) {
					$('<div id="wordfenceWorking">Wordfence is working...</div>').appendTo('body');
				}
			},
			removeLoading: function() {
				this.loadingCount--;
				if (this.loadingCount == 0) {
					jQuery('#wordfenceWorking').remove();
				}
			},
			startActivityLogUpdates: function() {
				var self = this;
				setInterval(function() {
					self.updateActivityLog();
				}, parseInt(WordfenceAdminVars.actUpdateInterval));
			},
			updateActivityLog: function() {
				if (this.activityLogUpdatePending || (!this.windowHasFocus() && WordfenceAdminVars.allowsPausing == '1')) {
					if (!jQuery('body').hasClass('wordfenceLiveActivityPaused') && !this.activityLogUpdatePending) {
						jQuery('body').addClass('wordfenceLiveActivityPaused');
					}
					return;
				}
				if (jQuery('body').hasClass('wordfenceLiveActivityPaused')) {
					jQuery('body').removeClass('wordfenceLiveActivityPaused');
				}
				WFAD.loadingIssues = true;
				this.activityLogUpdatePending = true;
				var self = this;
				this.ajax('wordfence_activityLogUpdate', {
					lastctime: this.lastALogCtime,
					lastissuetime: this.lastIssueUpdateTime,
				}, function(res) {
					self.doneUpdateActivityLog(res);
				}, function() {
					self.activityLogUpdatePending = false;
				}, true);

			},
			doneUpdateActivityLog: function(res) {
				this.actNextUpdateAt = (new Date()).getTime() + parseInt(WordfenceAdminVars.actUpdateInterval);
				if (res.ok) {
					if (res.items.length > 0) {
						this.activityQueue.push.apply(this.activityQueue, res.items);
						this.lastALogCtime = res.items[res.items.length - 1].ctime;
						this.processActQueue(res.currentScanID);
					}
					if (res.signatureUpdateTime) {
						this.updateSignaturesTimestamp(res.signatureUpdateTime);
					}

					WFAD.scanFailed = (res.scanFailed == '1' ? true : false);
					if (res.scanFailed) {
						jQuery('#wf-scan-failed-time-ago').text(res.scanFailedTiming);
						jQuery('#wf-scan-failed').show();
					}
					else {
						jQuery('#wf-scan-failed').hide();
					}
					
					if (res.lastMessage) {
						$('#wf-scan-last-status').html(res.lastMessage);
					}
					
					if (res.issues) {
						this.lastIssueUpdateTime = res.issueUpdateTimestamp;
						this.displayIssues(res);
					}
					
					if (res.issueCounts) {
						WFAD.updateIssueCounts(res.issueCounts);
					}
					
					WFAD.loadingIssues = false;

					if (res.scanStats) {
						var keys = Object.keys(res.scanStats);
						for (var i = 0; i < keys.length; i++) {
							$('.' + keys[i]).text(res.scanStats[keys[i]]); 
						}
					}

					if (res.scanStages) {
						var keys = Object.keys(res.scanStages);
						for (var i = 0; i < keys.length; i++) { 
							var element = $('#wf-scan-' + keys[i]);
							if (element) {
								var existingClasses = element.attr('class');
								if (existingClasses.match(/ /)) {
									existingClasses = existingClasses.split(' ');
								}
								else {
									existingClasses = [existingClasses];
								}
								
								var newClasses = res.scanStages[keys[i]];
								if (newClasses.match(/ /)) {
									newClasses = newClasses.split(' ');
								}
								else {
									newClasses = [newClasses];
								}
								
								var mismatch = false;
								if (existingClasses.length != newClasses.length) {
									mismatch = true;
								}
								else {
									var intersection = existingClasses.filter(function(value) {
										for (var n = 0; n < newClasses.length; n++) {
											if (newClasses[n] == value) {
												return true;
											}
										}
										return false;
									});
									mismatch = (intersection.length != newClasses.length);
								}
								
								if (mismatch) {
									element.removeClass();
									element.addClass(newClasses.join(' '));
								}

								var oldScanRunning = WFAD.scanRunning;
								WFAD.scanRunning = (res.scanRunning == '1' && !WFAD.scanFailed) ? true : false;
								if (oldScanRunning != WFAD.scanRunning) {
									if (WFAD.scanRunning) {
										$('#wf-scan-running-bar').show();
									}
									else {
										$('#wf-scan-running-bar').hide();
									}
									$(window).trigger('wfScanUpdateButtons');
								}
							}
						}
					}
				}
				this.activityLogUpdatePending = false;
			},

			updateSignaturesTimestamp: function(signatureUpdateTime) {
				var date = new Date(signatureUpdateTime * 1000);

				var dateString = date.toString();
				if (date.toLocaleString) {
					dateString = date.toLocaleString();
				}

				var sigTimestampEl = $('#wf-scan-sigs-last-update');
				var newText = 'Last Updated: ' + dateString;
				if (sigTimestampEl.text() !== newText) {
					sigTimestampEl.text(newText)
						.css({
							'opacity': 0
						})
						.animate({
							'opacity': 1
						}, 500);
				}
			},

			processActQueue: function(currentScanID) {
				if (this.activityQueue.length > 0) {
					this.addActItem(this.activityQueue.shift());
					this.totalActAdded++;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#wf-scan-activity-log > li:first').remove();
						this.totalActAdded--;
					}
					var timeTillNextUpdate = this.actNextUpdateAt - (new Date()).getTime();
					var maxRate = 50 / 1000; //Rate per millisecond
					var bulkTotal = 0;
					while (this.activityQueue.length > 0 && this.activityQueue.length / timeTillNextUpdate > maxRate) {
						var item = this.activityQueue.shift();
						if (item) {
							bulkTotal++;
							this.addActItem(item);
						}
					}
					this.totalActAdded += bulkTotal;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#wf-scan-activity-log > li:lt(' + bulkTotal + ')').remove();
						this.totalActAdded -= bulkTotal;
					}
					var minDelay = 100;
					var delay = minDelay;
					if (timeTillNextUpdate < 1) {
						delay = minDelay;
					} else {
						delay = Math.round(timeTillNextUpdate / this.activityQueue.length);
						if (delay < minDelay) {
							delay = minDelay;
						}
					}
					var self = this;
					setTimeout(function() {
						self.processActQueue();
					}, delay);
				}
				jQuery('#wf-scan-activity-log').scrollTop(jQuery('#wf-scan-activity-log').prop('scrollHeight'));
			},
			processActArray: function(arr) {
				for (var i = 0; i < arr.length; i++) {
					this.addActItem(arr[i]);
				}
			},
			addActItem: function(item) {
				if (!item) {
					return;
				}
				if (!item.msg) {
					return;
				}
				if (item.msg.indexOf('SUM_') == 0) {
					this.processSummaryLine(item);
				}
				else if (this.debugOn || item.level < 4) {

					var html = '<li class="wfActivityLine';
					if (this.debugOn) {
						html += ' wf' + item.type;
					}
					html += '">[' + item.date + ']&nbsp;' + item.msg + '</div>';
					jQuery('#wf-scan-activity-log').append(html);
					if (/Scan complete\./i.test(item.msg) || /Scan interrupted\./i.test(item.msg)) {
						this.loadIssues();
					}
				}
			},
			processSummaryLine: function(item) {
				var msg, summaryUpdated;
				if (item.msg.indexOf('SUM_START:') != -1) {
					msg = item.msg.replace('SUM_START:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult"><div class="wfSummaryLoading"></div></div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDBAD') != -1) {
					msg = item.msg.replace('SUM_ENDBAD:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryBad').html('Problems found.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDFAILED') != -1) {
					msg = item.msg.replace('SUM_ENDFAILED:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryBad').html('Failed.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDOK') != -1) {
					msg = item.msg.replace('SUM_ENDOK:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryOK').html('Secure.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDSUCCESS') != -1) {
					msg = item.msg.replace('SUM_ENDSUCCESS:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryOK').html('Success.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDERR') != -1) {
					msg = item.msg.replace('SUM_ENDERR:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryErr').html('An error occurred.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDSKIPPED') != -1) {
					msg = item.msg.replace('SUM_ENDSKIPPED:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryResult').html('Skipped.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_ENDIGNORED') != -1) {
					msg = item.msg.replace('SUM_ENDIGNORED:', '');
					jQuery('div.wfSummaryMsg:contains("' + msg + '")').next().addClass('wfSummaryIgnored').html('Ignored.');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_DISABLED:') != -1) {
					msg = item.msg.replace('SUM_DISABLED:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult">Disabled</div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_PAIDONLY:') != -1) {
					msg = item.msg.replace('SUM_PAIDONLY:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult"><a href="https://www.wordfence.com/wordfence-signup/" target="_blank"  rel="noopener noreferrer">Paid Members Only</a></div><div class="wfClear"></div>');
					summaryUpdated = true;
				} else if (item.msg.indexOf('SUM_FINAL:') != -1) {
					msg = item.msg.replace('SUM_FINAL:', '');
					jQuery('#consoleSummary').append('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg wfSummaryFinal">' + msg + '</div><div class="wfSummaryResult wfSummaryOK">Scan Complete.</div><div class="wfClear"></div>');
				} else if (item.msg.indexOf('SUM_PREP:') != -1) {
					msg = item.msg.replace('SUM_PREP:', '');
					jQuery('#consoleSummary').empty().html('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult" id="wfStartingScan"><div class="wfSummaryLoading"></div></div><div class="wfClear"></div>');
				} else if (item.msg.indexOf('SUM_KILLED:') != -1) {
					msg = item.msg.replace('SUM_KILLED:', '');
					jQuery('#consoleSummary').empty().html('<div class="wfSummaryLine"><div class="wfSummaryDate">[' + item.date + ']</div><div class="wfSummaryMsg">' + msg + '</div><div class="wfSummaryResult wfSummaryOK">Scan Complete.</div><div class="wfClear"></div>');
				}
			},
			processActQueueItem: function() {
				var item = this.activityQueue.shift();
				if (item) {
					jQuery('#consoleActivity').append('<div class="wfActivityLine wf' + item.type + '">[' + item.date + ']&nbsp;' + item.msg + '</div>');
					this.totalActAdded++;
					if (this.totalActAdded > this.maxActivityLogItems) {
						jQuery('#consoleActivity div:first').remove();
						this.totalActAdded--;
					}
					if (item.msg == 'Scan complete.') {
						this.loadIssues();
					}
				}
			},
			updateTicker: function(forceUpdate) {
				if ((!forceUpdate) && (this.tickerUpdatePending || (!this.windowHasFocus() && WordfenceAdminVars.allowsPausing == '1'))) {
					if (!jQuery('body').hasClass('wordfenceLiveActivityPaused') && !this.tickerUpdatePending) {
						jQuery('body').addClass('wordfenceLiveActivityPaused');
					}
					return;
				}
				if (jQuery('body').hasClass('wordfenceLiveActivityPaused')) {
					jQuery('body').removeClass('wordfenceLiveActivityPaused');
				}
				this.tickerUpdatePending = true;
				var self = this;
				var alsoGet = '';
				var otherParams = '';
				var data = '';
				if (this.mode == 'liveTraffic') {
					alsoGet = 'liveTraffic';
					otherParams = this.newestActivityTime;
					if (this.wfLiveTraffic) {
						data += this.wfLiveTraffic.getCurrentQueryString({
							since: this.newestActivityTime
						});
					}

				} else if (this.mode == 'activity' && /^(?:404|hit|human|ruser|gCrawler|crawler|loginLogout)$/.test(this.activityMode)) {
					alsoGet = 'logList_' + this.activityMode;
					otherParams = this.newestActivityTime;
				} else if (this.mode == 'perfStats') {
					alsoGet = 'perfStats';
					otherParams = this.newestActivityTime;
				}
				data += '&alsoGet=' + encodeURIComponent(alsoGet) + '&otherParams=' + encodeURIComponent(otherParams);
				this.ajax('wordfence_ticker', data, function(res) {
					self.handleTickerReturn(res);
				}, function() {
					self.tickerUpdatePending = false;
				}, true);
			},
			handleTickerReturn: function(res) {
				this.tickerUpdatePending = false;
				var newMsg = "";
				var oldMsg = jQuery('.wf-live-activity-message').text();
				if (res.msg) {
					newMsg = res.msg;
				} else {
					newMsg = "Idle";
				}
				if (newMsg && newMsg != oldMsg) {
					jQuery('.wf-live-activity-message').hide().html(newMsg).fadeIn(200);
				}
				var haveEvents, newElem;
				this.serverTimestampOffset = (new Date().getTime() / 1000) - res.serverTime;
				this.serverMicrotime = res.serverMicrotime;

				if (this.mode == 'liveTraffic') {
					if (res.events.length > 0) {
						this.newestActivityTime = res.events[0]['ctime'];
					}
					if (typeof WFAD.wfLiveTraffic !== undefined) {
						WFAD.wfLiveTraffic.prependListings(res.events, res);
						this.reverseLookupIPs();
						this.updateTimeAgo();
					}
				}
			},
			reverseLookupIPs: function() {
				var self = this;
				var ips = [];
				jQuery('.wfReverseLookup').each(function(idx, elem) {
					var txt = jQuery(elem).text().trim();
					if (/^\d+\.\d+\.\d+\.\d+$/.test(txt) && (!jQuery(elem).data('wfReverseDone'))) {
						jQuery(elem).data('wfReverseDone', true);
						ips.push(txt);
					}
				});
				if (ips.length < 1) {
					return;
				}
				var uni = {};
				var uniqueIPs = [];
				for (var i = 0; i < ips.length; i++) {
					if (!uni[ips[i]]) {
						uni[ips[i]] = true;
						uniqueIPs.push(ips[i]);
					}
				}
				this.ajax('wordfence_reverseLookup', {
						ips: uniqueIPs.join(',')
					},
					function(res) {
						if (res.ok) {
							jQuery('.wfReverseLookup').each(function(idx, elem) {
								var el = jQuery(elem);
								var txt = el.text().trim();
								for (var ip in res.ips) {
									if (txt == ip) {
										if (res.ips[ip]) {
											var hostnameTemplate = el.attr('data-reverse-lookup-template');
											if (hostnameTemplate) {
												jQuery(elem).html(jQuery.tmpl($('#' + hostnameTemplate), {
													ip: res.ips[ip]
												}));
											} else if (el.hasClass('wf-hostname-only')) {
												jQuery(elem).text(res.ips[ip]);
											} else {
												jQuery(elem).html('<strong>Hostname:</strong>&nbsp;' + self.htmlEscape(res.ips[ip]));
											}
										} else {
											jQuery(elem).html('');
										}
									}
								}
							});
						}
					}, false, false);
			},
			killScan: function(callback) {
				var self = this;
				this.ajax('wordfence_killScan', {}, function(res) {
					if (res.ok) {
						typeof callback === 'function' && callback(true);
						WFAD.scanRunning = false;
						WFAD.scanFailed = false;
						$(window).trigger('wfScanUpdateButtons');
					} else {
						typeof callback === 'function' && callback(false);
					}
				});
			},
			startScan: function() {
				this.ajax('wordfence_scan', {}, function(res) {
					if (res.ok) {
						WFAD.scanRunning = true;
						$('#wf-scan-results-new').empty();
						$('#wf-scan-bulk-buttons-delete, #wf-scan-bulk-buttons-repair').addClass('wf-disabled');
						WFAD.updateIssueCounts(res.issueCounts);
						$(window).trigger('wfScanUpdateButtons');
					}
				});
			},
			loadIssues: function(callback, offset, limit) {
				if (this.mode != 'scan') {
					return;
				}
				offset = offset || 0;
				limit = limit || WordfenceAdminVars.scanIssuesPerPage;
				var self = this;
				this.ajax('wordfence_loadIssues', {offset: offset, limit: limit}, function(res) {
					var newCount = parseInt(res.issueCounts.new) || 0;
					var ignoredCount = (parseInt(res.issueCounts.ignoreP) || 0) + (parseInt(res.issueCounts.ignoreC) || 0);
					jQuery('#wfNewIssuesTab .wfIssuesCount').text(' (' + newCount + ')');
					jQuery('#wfIgnoredIssuesTab .wfIssuesCount').text(' (' + ignoredCount + ')'); 
					self.displayIssues(res, callback);
				});
			},
			loadMoreIssues: function(callback, offset, limit, ignoredOffset, ignoredLimit) {
				offset = offset || 0;
				limit = limit || WordfenceAdminVars.scanIssuesPerPage;
				ignoredOffset = ignoredOffset || 0;
				ignoredLimit = ignoredLimit || WordfenceAdminVars.scanIssuesPerPage;
				
				if (offset >= WFAD.scanIssuesNewCount && ignoredOffset >= WFAD.scanIssuesIgnoredCount) {
					return;
				}
				
				WFAD.ajax('wordfence_loadIssues', {offset: offset, limit: limit, ignoredOffset: ignoredOffset, ignoredLimit: ignoredLimit}, function(res) {
					WFAD.updateIssueCounts(res.issueCounts);
					WFAD.appendIssues(res.issues, callback);
				});
			},
			sev2num: function(str) {
				if (/wfProbSev1/.test(str)) {
					return 1;
				} else if (/wfProbSev2/.test(str)) {
					return 2;
				} else {
					return 0;
				}
			},
			isIssueExpanded: function(issueID) {
				var key = 'wf-scan-issue-expanded-' + issueID;
				if (window.localStorage) {
					return !!parseInt(window.localStorage.getItem(key));
				}
				return false;
			},
			expandIssue: function(issueID, makeVisible) {
				var key = 'wf-scan-issue-expanded-' + issueID;
				if (window.localStorage) {
					window.localStorage.setItem(key, makeVisible ? 1 : 0);
				}
			},
			displayIssues: function(res, callback) {
				for (var issueStatus in res.issues) {
					var containerID = 'wf-scan-results-' + issueStatus;
					if ($('#' + containerID).length < 1) {
						continue;
					}
					
					if (res.issues[issueStatus].length < 1) {
						continue;
					}
					
					$('#' + containerID).empty();
				}
				
				WFAD.appendIssues(res.issues, callback);
				
				return true;
			},
			appendIssues: function(issues, callback) {
				for (var issueStatus in issues) {
					var containerID = 'wf-scan-results-' + issueStatus;
					if ($('#' + containerID).length < 1) {
						continue;
					}
					
					var container = $('#' + containerID);
					for (var i = 0; i < issues[issueStatus].length; i++) {
						var issueObject = issues[issueStatus][i];
						WFAD.appendIssue(issueObject, container);
					}
				}
				
				WFAD.sortIssues();
				WFAD.updateBulkButtons();
			},
			appendIssue: function(issueObject, container) {
				var issueType = issueObject.type;
				var tmplName = 'issueTmpl_' + issueType;
				var template = $('#' + tmplName);
				if (template.length) {
					var issue = template.tmpl(issueObject);
					issue.data('sourceData', issueObject);
					issue.data('templateName', tmplName);
					if (this.isIssueExpanded(issueObject.id)) {
						issue.addClass('wf-active');
					}

					//Hook up Details button
					issue.find('.wf-issue-control-show-details').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						var isActive = $(this).closest('.wf-issue').hasClass('wf-active');
						var issueID = $(this).closest('.wf-issue').data('issueId'); 
						WFAD.expandIssue(issueID, !isActive);
						$(this).closest('.wf-issue').toggleClass('wf-active', !isActive);
					});

					//Hook up Ignore button
					issue.find('.wf-issue-control-ignore').each(function() {
						var issueID = $(this).closest('.wf-issue').data('issueId');
						var menu = $(this).parent().find('.wf-issue-control-ignore-menu').menu().hide();

						$(this).on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							var ignoreAction = $(this).data('ignoreAction');
							if (ignoreAction == 'choice') {
								menu.show().position({
									my: "left top",
									at: "left bottom",
									of: this
								});

								$(document).on('click', function() {
									menu.hide();
								});
							}
							else {
								var self = this;
								WFAD.updateIssueStatus(issueID, ignoreAction, function(res) {
									if (res.ok) {
										var issueContainer = $(self).closest('.wf-scan-results-issues');
										var issueElement = $(self).closest('.wf-issue');
										var sourceData = issueElement.data('sourceData');
										sourceData['status'] = ignoreAction;

										var targetContainerID = 'wf-scan-results-' + (issueContainer.attr('id') == 'wf-scan-results-new' ? 'ignored' : 'new');
										var targetContainer = $('#' + targetContainerID);
										issueElement.remove();
										WFAD.appendIssue(sourceData, targetContainer);
										WFAD.sortIssues();
										WFAD.updateIssueCounts(res.issueCounts);
										WFAD.repositionSiteCleaningCallout();
										WFAD.updateBulkButtons();
									}
								});
							}
						});

						menu.find('.wf-issue-control-ignore-menu-ignorec').on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							var self = this;
							WFAD.updateIssueStatus(issueID, 'ignoreC', function(res) {
								if (res.ok) {
									var issueContainer = $(self).closest('.wf-scan-results-issues');
									var issueElement = $(self).closest('.wf-issue');
									var sourceData = issueElement.data('sourceData');
									sourceData['status'] = 'ignoreC';

									var targetContainerID = 'wf-scan-results-' + (issueContainer.attr('id') == 'wf-scan-results-new' ? 'ignored' : 'new');
									var targetContainer = $('#' + targetContainerID);
									issueElement.remove();
									WFAD.appendIssue(sourceData, targetContainer);
									WFAD.sortIssues();
									WFAD.updateIssueCounts(res.issueCounts);
									WFAD.repositionSiteCleaningCallout();
									WFAD.updateBulkButtons();
								}
							});
						});

						menu.find('.wf-issue-control-ignore-menu-ignorep').on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							var self = this;
							WFAD.updateIssueStatus(issueID, 'ignoreP', function(res) {
								if (res.ok) {
									var issueContainer = $(self).closest('.wf-scan-results-issues');
									var issueElement = $(self).closest('.wf-issue');
									var sourceData = issueElement.data('sourceData');
									sourceData['status'] = 'ignoreP';

									var targetContainerID = 'wf-scan-results-' + (issueContainer.attr('id') == 'wf-scan-results-new' ? 'ignored' : 'new');
									var targetContainer = $('#' + targetContainerID);
									issueElement.remove();
									WFAD.appendIssue(sourceData, targetContainer);
									WFAD.sortIssues();
									WFAD.updateIssueCounts(res.issueCounts);
									WFAD.repositionSiteCleaningCallout();
									WFAD.updateBulkButtons();
								}
							});
						});
					});
					
					//Hook up Mark as Fixed button
					issue.find('.wf-issue-control-mark-fixed').each(function() {
						var issueID = $(this).closest('.wf-issue').data('issueId');

						$(this).on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							var self = this;
							WFAD.updateIssueStatus(issueID, 'delete', function(res) {
								if (res.ok) {
									var issueElement = $(self).closest('.wf-issue');
									issueElement.remove();
									WFAD.updateIssueCounts(res.issueCounts);
									WFAD.repositionSiteCleaningCallout();
									WFAD.updateBulkButtons();
								}
							});
						});
					});
					
					//Hook up Delete File button
					issue.find('.wf-issue-control-delete-file').each(function() {
						var issueID = $(this).closest('.wf-issue').data('issueId');

						$(this).on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							var self = this;
							WFAD.deleteFile(issueID, false, function(res) {
								if (res.ok) {
									var issueElement = $(self).closest('.wf-issue');
									issueElement.remove();
									WFAD.updateIssueCounts(res.issueCounts);
									WFAD.repositionSiteCleaningCallout();
									WFAD.updateBulkButtons();
									WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), "Success deleting file", "The file " + res.file + " was successfully deleted.");
								}
								else if (res.errorMsg) {
									WFAD.colorboxError(res.errorMsg, res.tokenInvalid);
								}
							});
						});
					});

					//Hook up Repair button
					issue.find('.wf-issue-control-repair').each(function() {
						var issueID = $(this).closest('.wf-issue').data('issueId');

						$(this).on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							var self = this;
							WFAD.restoreFile(issueID, function(res) {
								if (res.ok) {
									var issueElement = $(self).closest('.wf-issue');
									issueElement.remove();
									WFAD.updateIssueCounts(res.issueCounts);
									WFAD.repositionSiteCleaningCallout();
									WFAD.updateBulkButtons();
									WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), "Success restoring file", "The file " + res.file + " was successfully restored.");
								}
								else if (res.errorMsg) {
									WFAD.colorboxError(res.errorMsg, res.tokenInvalid);
								}
							});
						});
					});
					
					//Hook up Hide File button
					issue.find('.wf-issue-control-hide-file').each(function() {
						var issueID = $(this).closest('.wf-issue').data('issueId');

						$(this).on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							var self = this;
							WFAD.hideFile(issueID, function(res) {
								if (res.ok) {
									var issueElement = $(self).closest('.wf-issue');
									issueElement.remove();
									WFAD.updateIssueCounts(res.issueCounts);
									WFAD.repositionSiteCleaningCallout();
									WFAD.updateBulkButtons();
									WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), "File hidden successfully", "The file " + res.file + " was successfully hidden from public view.");
								}
								else if (res.errorMsg) {
									WFAD.colorboxError(res.errorMsg, res.tokenInvalid);
								}
							});
						});
					});

					//Swap out if the row already exists
					var existing = $('.wf-issue[data-issue-id="' + issueObject.id + '"]');
					if (existing.length) {
						existing.replaceWith(issue);
					}
					else {
						container.append(issue);
					}

					//Make row tappable
					issue.find('.wf-issue-summary').on('mousedown', function(e) {
						$(this).data('clickTapX', e.pageX).data('clickTapY', e.pageY);
					}).on('click', function(e) {
						var buffer = 10;
						var clickTapX = $(this).data('clickTapX');
						var clickTapY = $(this).data('clickTapY');
						if (clickTapX > e.pageX - buffer && clickTapX < e.pageX + buffer && clickTapY > e.pageY - buffer && clickTapY < e.pageY + buffer) {
							var links = $(this).find('a');
							for (var i = 0; i < links.length; i++) {
								var t = $(links[i]).offset().top;
								var l = $(links[i]).offset().left;
								var b = t + $(links[i]).height();
								var r = l + $(links[i]).width();

								if (e.pageX > l - buffer && e.pageX < r + buffer && e.pageY > t - buffer && e.pageY < b + buffer) {
									return;
								}
							}
							
							$(this).closest('.wf-issue').find('li.wf-issue-controls .wf-issue-control-show-details').trigger('click');
						}
					}).css('cursor', 'pointer');
				}
			},
			sortIssues: function() {
				var issueTypes = ['new', 'ignored'];
				for (var i = 0; i < issueTypes.length; i++) {
					var containerID = 'wf-scan-results-' + issueTypes[i];
					if ($('#' + containerID).length < 1) {
						continue;
					}

					var container = $('#' + containerID);
					var issuesDOM = container.find('.wf-issue');
					issuesDOM.detach();
					issuesDOM.sort(function(a, b) {
						var severityA = $(a).data('issueSeverity');
						var severityB = $(b).data('issueSeverity');
						if (severityA < severityB) { return -1; }
						else if (severityA > severityB) { return 1; }

						var typeA = $(a).data('issueType');
						var typeB = $(b).data('issueType');
						
						var typeAIndex = WFAD.siteCleaningIssueTypes.indexOf(typeA);
						var typeBIndex = WFAD.siteCleaningIssueTypes.indexOf(typeB);
						if (typeAIndex > -1 && typeBIndex > -1) {
							if (typeAIndex < typeBIndex) { return -1; }
							else if (typeAIndex > typeBIndex) { return 1; }
							return 0;
						}
						else if (typeAIndex > -1) {
							return -1;
						}
						else if (typeBIndex > -1) {
							return 1;
						}
						
						if (typeA < typeB) { return -1; }
						else if (typeA > typeB) { return 1; }

						return 0;
					});
					container.append(issuesDOM);
				}

				WFAD.repositionSiteCleaningCallout();
				WFAD.scanIssuesOffset = $('#wf-scan-results-new .wf-issue').length;
				WFAD.scanIssuesIgnoredOffset = $('#wf-scan-results-ignored .wf-issue').length;
			},
			updateBulkButtons: function() {
				var containerID = 'wf-scan-results-new';
				if ($('#' + containerID).length < 1) {
					return;
				}

				var hasDeleteable = false;
				var hasRepairable = false;
				
				var container = $('#' + containerID);
				var issuesDOM = container.find('.wf-issue');
				for (var i = 0; i < issuesDOM.length; i++) {
					var sourceData = $(issuesDOM[i]).data('sourceData');
					if (sourceData.data.canDelete) {
						hasDeleteable = true;
					}
					
					if (sourceData.data.canFix) {
						hasRepairable = true;
					}
					
					if (hasDeleteable && hasRepairable) {
						break;
					}
				}

				$('#wf-scan-bulk-buttons-delete').toggleClass('wf-disabled', !hasDeleteable);
				$('#wf-scan-bulk-buttons-repair').toggleClass('wf-disabled', !hasRepairable);
			},
			updateIssueCounts: function(issueCounts) {
				var newCount = (typeof issueCounts['new'] === 'undefined' ? 0 : parseInt(issueCounts['new']));
				var ignoredCount = (typeof issueCounts['ignoreC'] === 'undefined' ? 0 : parseInt(issueCounts['ignoreC'])) + (typeof issueCounts['ignoreP'] === 'undefined' ? 0 : parseInt(issueCounts['ignoreP']));
				WFAD.scanIssuesNewCount = newCount;
				WFAD.scanIssuesIgnoredCount = ignoredCount;
				WFAD.scanIssuesTotalCount = newCount + ignoredCount;
				
				$('#wf-scan-tab-new a').html($('#wf-scan-tab-new').data('tabTitle') + ' (' + newCount + ')');
				$('#wf-scan-tab-ignored a').html($('#wf-scan-tab-ignored').data('tabTitle') + ' (' + ignoredCount + ')'); 

				if (newCount == 0) {
					var existing = $('.wf-issue[data-issue-id="no-issues-new"]');
					if (existing.length == 0) {
						var issue = $('#issueTmpl_noneFound').tmpl({shortMsg: 'No new issues have been found.', id: 'no-issues-new'});
						$('#wf-scan-results-new').append(issue);
					}
				}
				else {
					$('.wf-issue[data-issue-id="no-issues-new"]').remove();
				}
				
				if (ignoredCount == 0) {
					var existing = $('.wf-issue[data-issue-id="no-issues-ignored"]');
					if (existing.length == 0) {
						var issue = $('#issueTmpl_noneFound').tmpl({shortMsg: 'No issues have been ignored.', id: 'no-issues-ignored'});
						$('#wf-scan-results-ignored').append(issue);
					}
				}
				else {
					$('.wf-issue[data-issue-id="no-issues-ignored"]').remove();
				}
			},
			repositionSiteCleaningCallout: function() {
				$('.wf-issue-site-cleaning').remove();
				
				var issueTypes = WFAD.siteCleaningIssueTypes;
				for (var i = 0; i < issueTypes.length; i++) {
					if ($('#wf-scan-results-new .wf-issue-' + issueTypes[i]).length) {
						if (!!$('#wf-scan-results-new .wf-issue-' + issueTypes[i]).data('betaSignatures')) {
							$('#wf-scan-results-new .wf-issue').first().after($('#siteCleaningBetaSigsTmpl').tmpl());
						}
						else if (!!$('#wf-scan-results-new .wf-issue-' + issueTypes[i]).data('highSensitivity')) {
							$('#wf-scan-results-new .wf-issue').first().after($('#siteCleaningHighSenseTmpl').tmpl());
						}
						else {
							$('#wf-scan-results-new .wf-issue').first().after($('#siteCleaningTmpl').tmpl());
						}
						return;
					}
				}
			},
			ajax: function(action, data, cb, cbErr, noLoading) {
				if (typeof(data) == 'string') {
					if (data.length > 0) {
						data += '&';
					}
					data += 'action=' + action + '&nonce=' + this.nonce;
				} else if (typeof(data) == 'object' && data instanceof Array) {
					// jQuery serialized form data
					data.push({
						name: 'action',
						value: action
					});
					data.push({
						name: 'nonce',
						value: this.nonce
					});
				} else if (typeof(data) == 'object') {
					data['action'] = action;
					data['nonce'] = this.nonce;
				}
				if (!cbErr) {
					cbErr = function() {
					};
				}
				var self = this;
				if (!noLoading) {
					this.showLoading();
				}
				jQuery.ajax({
					type: 'POST',
					url: WordfenceAdminVars.ajaxURL,
					dataType: "json",
					data: data,
					success: function(json) {
						if (!noLoading) {
							self.removeLoading();
						}
						if (json && json.nonce) {
							self.nonce = json.nonce;
						}
						if (json && json.errorMsg) {
							WFAD.colorboxError(json.errorMsg, json.tokenInvalid);
						}
						cb(json);
					},
					error: function() {
						if (!noLoading) {
							self.removeLoading();
						}
						cbErr();
					}
				});
			},
			colorbox: function(width, heading, body, settings) {
				if (typeof settings === 'undefined') {
					settings = {};
				}
				this.colorboxQueue.push([width, "<h3>" + heading + "</h3><p>" + body + "</p>", settings]);
				this.colorboxServiceQueue();
			},
			colorboxModalHTML: function(width, heading, body, settings) {
				if (typeof settings === 'undefined') {
					settings = {};
				}
				
				var prompt = $.tmpl(WordfenceAdminVars.modalHTMLTemplate, {title: heading, message: body});
				var promptHTML = $("<div />").append(prompt).html();
				var callback = settings.onComplete;
				settings.overlayClose = false;
				settings.closeButton = false;
				settings.className = 'wf-modal';
				settings.onComplete = function() {
					$('#wf-generic-modal-close').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFAD.colorboxClose();
					});

					typeof callback === 'function' && callback();
				};
				this.colorboxHTML(width, promptHTML, settings)
			},
			colorboxModal: function(width, heading, body, settings) {
				if (typeof settings === 'undefined') {
					settings = {};
				}

				var prompt = $.tmpl(WordfenceAdminVars.modalTemplate, {title: heading, message: body});
				var promptHTML = $("<div />").append(prompt).html();
				var callback = settings.onComplete;
				settings.overlayClose = false;
				settings.closeButton = false;
				settings.className = 'wf-modal';
				settings.onComplete = function() {
					$('#wf-generic-modal-close').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						WFAD.colorboxClose();
					});

					typeof callback === 'function' && callback();
				};
				this.colorboxHTML(width, promptHTML, settings)
			},
			colorboxError: function(errorMsg, isTokenError) {
				var callback = false;
				if (isTokenError) {
					if (WFAD.tokenErrorShowing) {
						return;
					}
					
					callback = function() {
						setTimeout(function() {
							WFAD.tokenErrorShowing = false;
						}, 30000);
					};
					
					WFAD.tokenErrorShowing = true;
				}

				var prompt = $.tmpl(WordfenceAdminVars.tokenInvalidTemplate, {title: 'An error occurred', message: errorMsg});
				var promptHTML = $("<div />").append(prompt).html();
				var settings = {};
				settings.overlayClose = false;
				settings.closeButton = false;
				settings.className = 'wf-modal';
				settings.onComplete = function() {
					$('#wf-token-invalid-modal-reload').on('click', function(e) {
						e.preventDefault();
						e.stopPropagation();

						window.location.reload(true);
					});

					typeof callback === 'function' && callback();
				};
				WFAD.colorboxHTML((WFAD.isSmallScreen ? '300px' : '400px'), promptHTML, settings);
			},
			colorboxHTML: function(width, html, settings) {
				if (typeof settings === 'undefined') {
					settings = {};
				}
				this.colorboxQueue.push([width, html, settings]);
				this.colorboxServiceQueue();
			},
			colorboxServiceQueue: function() {
				if (this.colorboxIsOpen) {
					return;
				}
				if (this.colorboxQueue.length < 1) {
					return;
				}
				var elem = this.colorboxQueue.shift();
				this.colorboxOpen(elem[0], elem[1], elem[2]);
			},
			colorboxOpen: function(width, html, settings) {
				var self = this;
				this.colorboxIsOpen = true;
				jQuery.extend(settings, {
					width: width,
					html: html,
					onClosed: function() {
						self.colorboxClose();
					}
				});
				jQuery.wfcolorbox(settings);
			},
			colorboxClose: function() {
				this.colorboxIsOpen = false;
				jQuery.wfcolorbox.close();
			},
			bulkOperationConfirmed: function(op) {
				WFAD.colorboxClose();
				this.ajax('wordfence_bulkOperation', {
					op: op
				}, function(res) {
					if (res.ok) {
						for (var i = 0; i < res.idsRemoved.length; i++) {
							$('.wf-issue[data-issue-id="' + res.idsRemoved[i] + '"]').remove();
						}

						WFAD.updateIssueCounts(res.issueCounts);
						WFAD.repositionSiteCleaningCallout();
						WFAD.updateBulkButtons();
						setTimeout(function() {
							WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), res.bulkHeading, res.bulkBody);
						}, 500);
					}
				});
			},
			deleteFile: function(issueID, force, callback) {
				this.ajax('wordfence_deleteFile', {
					issueID: issueID,
					forceDelete: force
				}, function(res) {
					if (res.needsCredentials) {
						document.location.href = res.redirect;
					} else {
						typeof callback === 'function' && callback(res);
					}
				});
			},
			deleteDatabaseOption: function(issueID) {
				var self = this;
				this.ajax('wordfence_deleteDatabaseOption', {
					issueID: issueID
				}, function(res) {
					self.doneDeleteDatabaseOption(res);
				});
			},
			doneDeleteDatabaseOption: function(res) {
				var cb = false;
				var self = this;
				if (res.ok) {
					this.loadIssues(function() {
						self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "Success removing option", "The option " + res.option_name + " was successfully removed.");
					});
				} else if (res.cerrorMsg) {
					this.loadIssues(function() {
						WFAD.colorboxError(res.cerrorMsg, res.tokenInvalid);
					});
				}
			},
			useRecommendedHowGetIPs: function(issueID) {
				var self = this;
				this.ajax('wordfence_misconfiguredHowGetIPsChoice', {
					issueID: issueID,
					choice: 'yes'
				}, function(res) {
					if (res.ok) {
						jQuery('#wordfenceMisconfiguredHowGetIPsNotice').fadeOut();
						
						self.loadIssues(function() {
							self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "Success updating option", "The 'How does Wordfence get IPs' option was successfully updated to the recommended value.");
						});
					} else if (res.cerrorMsg) {
						self.loadIssues(function() {
							WFAD.colorboxError(res.cerrorMsg, res.tokenInvalid);
						}); 
					}
				});	
			},
			fixFPD: function(issueID) {
				var self = this;
				var title = "Full Path Disclosure";
				issueID = parseInt(issueID);

				this.ajax('wordfence_checkHtaccess', {}, function(res) {
					if (res.ok) {
						self.colorboxModalHTML((self.isSmallScreen ? '300px' : '400px'), title, 'We are about to change your <em>.htaccess</em> file. Please make a backup of this file proceeding'
							+ '<br/>'
							+ '<a href="' + WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadHtaccess&nonce=' + self.nonce + '" onclick="jQuery(\'#wfFPDNextBut\').prop(\'disabled\', false); return true;">Click here to download a backup copy of your .htaccess file now</a><br /><br /><input type="button" class="wf-btn wf-btn-default" name="but1" id="wfFPDNextBut" value="Click to fix .htaccess" disabled="disabled" onclick="WFAD.fixFPD_WriteHtAccess(' + issueID + ');" />');
					} else if (res.nginx) {
						self.colorboxModalHTML((self.isSmallScreen ? '300px' : '400px'), title, 'You are using an Nginx web server and using a FastCGI processor like PHP5-FPM. You will need to manually modify your php.ini to disable <em>display_error</em>');
					} else if (res.err) {
						self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "We encountered a problem", "We can't modify your .htaccess file for you because: " + res.err);
					}
				});
			},
			fixFPD_WriteHtAccess: function(issueID) {
				var self = this;
				self.colorboxClose();
				this.ajax('wordfence_fixFPD', {
					issueID: issueID
				}, function(res) {
					if (res.ok) {
						self.loadIssues(function() {
							self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "File restored OK", "The Full Path disclosure issue has been fixed");
						});
					} else {
						self.loadIssues(function() {
							WFAD.colorboxError(res.cerrorMsg, res.tokenInvalid);
						});
					}
				});
			},
			
			hideFile: function(issueID, callback) {
				WFAD.ajax('wordfence_checkHtaccess', {}, function(checkRes) {
					if (checkRes.ok) {
						WFAD.colorboxModalHTML((WFAD.isSmallScreen ? '300px' : '400px'), '.htaccess change', 'We are about to change your <em>.htaccess</em> file. Please make a backup of this file proceeding'
							+ '<br/>'
							+ '<a id="dlButton" href="' + WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadHtaccess&nonce=' + WFAD.nonce + '">Click here to download a backup copy of your .htaccess file now</a>'
							+ '<br /><br /><input type="button" class="wf-btn wf-btn-default" name="but1" id="wfFPDNextBut" value="Click to fix .htaccess" disabled="disabled" />'
						);
						$('#dlButton').on('click', function(e) {
							$('#wfFPDNextBut').prop('disabled', false);
						});
						$('#wfFPDNextBut').on('click', function(e) {
							e.preventDefault();
							e.stopPropagation();

							WFAD.ajax('wordfence_hideFileHtaccess', {
								issueID: issueID
							}, function(res) {
								WFAD.colorboxClose();
								typeof callback === 'function' && callback(res);
							});
						});
					}
					else if (checkRes.nginx) {
						WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), 'Unable to automatically hide file', 'You are using an Nginx web server and using a FastCGI processor like PHP5-FPM. You will need to manually delete or hide those files.');
					}
					else if (checkRes.err) {
						WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), "We encountered a problem", "We can't modify your .htaccess file for you because: " + res.err);
					}
				});
			},

			restoreFile: function(issueID, callback) {
				this.ajax('wordfence_restoreFile', {
					issueID: issueID
				}, function(res) {
					if (res.needsCredentials) {
						document.location.href = res.redirect;
					}
					else {
						typeof callback === 'function' && callback(res);
					}
				});
			},

			disableDirectoryListing: function(issueID) {
				var self = this;
				var title = "Disable Directory Listing";
				issueID = parseInt(issueID);

				this.ajax('wordfence_checkHtaccess', {}, function(res) {
					if (res.ok) {
						self.colorboxModalHTML((self.isSmallScreen ? '300px' : '400px'), title, 'We are about to change your <em>.htaccess</em> file. Please make a backup of this file proceeding'
							+ '<br/>'
							+ '<a href="' + WordfenceAdminVars.ajaxURL + '?action=wordfence_downloadHtaccess&nonce=' + self.nonce + '" onclick="jQuery(\'#wf-htaccess-confirm\').prop(\'disabled\', false); return true;">Click here to download a backup copy of your .htaccess file now</a>' +
							'<br /><br />' +
							'<button class="wf-btn wf-btn-default" type="button" id="wf-htaccess-confirm" disabled="disabled" onclick="WFAD.confirmDisableDirectoryListing(' + issueID + ');">Add code to .htaccess</button>');
					} else if (res.nginx) {
						self.colorboxModalHTML((self.isSmallScreen ? '300px' : '400px'), "You are using Nginx as your web server. " +
							"You'll need to disable autoindexing in your nginx.conf. " +
							"See the <a target='_blank'  rel='noopener noreferrer' href='http://nginx.org/en/docs/http/ngx_http_autoindex_module.html'>Nginx docs for more info</a> on how to do this.");
					} else if (res.err) {
						self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "We encountered a problem", "We can't modify your .htaccess file for you because: " + res.err);
					}
				});
			},
			confirmDisableDirectoryListing: function(issueID) {
				var self = this;
				this.colorboxClose();
				this.ajax('wordfence_disableDirectoryListing', {
					issueID: issueID
				}, function(res) {
					if (res.ok) {
						self.loadIssues(function() {
							self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "Directory Listing Disabled", "Directory listing has been disabled on your server.");
						});
					} else {
						//self.loadIssues(function() {
						//	self.colorbox('400px', 'An error occurred', res.errorMsg);
						//});
					}
				});
			},

			deleteIssue: function(id) {
				var self = this;
				this.ajax('wordfence_deleteIssue', {id: id}, function(res) {
					self.loadIssues();
				});
			},
			updateIssueStatus: function(id, st, callback) {
				this.ajax('wordfence_updateIssueStatus', {id: id, 'status': st}, function(res) {
					typeof callback === 'function' && callback(res);
				});
			},
			es: function(val) {
				if (val) {
					return val;
				} else {
					return "";
				}
			},
			noQuotes: function(str) {
				return str.replace(/"/g, '&#34;').replace(/\'/g, '&#145;');
			},
			commify: function(num) {
				return ("" + num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
			},
			switchToLiveTab: function(elem) {
				jQuery('.wfTab1').removeClass('selected');
				jQuery(elem).addClass('selected');
				jQuery('.wfDataPanel').hide();
				var self = this;
				jQuery('#wfActivity').fadeIn(function() {
					self.completeLiveTabSwitch();
				});
			},
			completeLiveTabSwitch: function() {
				this.ajax('wordfence_loadActivityLog', {}, function(res) {
					var html = '<a href="#" class="wfALogMailLink" onclick="WFAD.emailActivityLog(); return false;"></a><a href="#" class="wfALogReloadLink" onclick="WFAD.reloadActivityData(); return false;"></a>';
					if (res.events && res.events.length > 0) {
						jQuery('#wfActivity').empty();
						for (var i = 0; i < res.events.length; i++) {
							var timeTaken = '0.0000';
							if (res.events[i + 1]) {
								timeTaken = (res.events[i].ctime - res.events[i + 1].ctime).toFixed(4);
							}
							var red = "";
							if (res.events[i].type == 'error') {
								red = ' class="wfWarn" ';
							}
							html += '<div ' + red + 'class="wfALogEntry"><span ' + red + 'class="wfALogTime">[' + res.events[i].type + '&nbsp;:&nbsp;' + timeTaken + '&nbsp;:&nbsp;' + res.events[i].timeAgo + ' ago]</span>&nbsp;' + res.events[i].msg + "</div>";
						}
						jQuery('#wfActivity').html(html);
					} else {
						jQuery('#wfActivity').html("<p>&nbsp;&nbsp;No activity to report yet. Please complete your first scan.</p>");
					}
				});
			},
			emailActivityLog: function() {
				this.colorboxModalHTML((this.isSmallScreen ? '300px' : '400px'), 'Email Wordfence Activity Log', "Enter the email address you would like to send the Wordfence activity log to. Note that the activity log may contain thousands of lines of data. This log is usually only sent to a member of the Wordfence support team. It also contains your PHP configuration from the phpinfo() function for diagnostic data.<br /><br /><input type='text' value='wftest@wordfence.com' size='20' id='wfALogRecip' /><input class='wf-btn wf-btn-default' type='button' value='Send' onclick=\"WFAD.completeEmailActivityLog();\" />");
			},
			completeEmailActivityLog: function() {
				WFAD.colorboxClose();
				var email = jQuery('#wfALogRecip').val();
				if (!/^[^@]+@[^@]+$/.test(email)) {
					alert("Please enter a valid email address.");
					return;
				}
				var self = this;
				this.ajax('wordfence_sendActivityLog', {email: jQuery('#wfALogRecip').val()}, function(res) {
					if (res.ok) {
						self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), 'Activity Log Sent', "Your Wordfence activity log was sent to " + email);
					}
				});
			},
			reloadActivityData: function() {
				jQuery('#wfActivity').html('<div class="wfLoadingWhite32"></div>'); //&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
				this.completeLiveTabSwitch();
			},
			switchToSummaryTab: function(elem) {
				jQuery('.wfTab1').removeClass('selected');
				jQuery(elem).addClass('selected');
				jQuery('.wfDataPanel').hide();
				jQuery('#wfSummaryTables').fadeIn();
			},
			switchIssuesTab: function(elem, type) {
				jQuery('.wfTab2').removeClass('selected');
				jQuery('.wfIssuesContainer').hide();
				jQuery(elem).addClass('selected');
				this.visibleIssuesPanel = type;
				jQuery('#wfIssues_' + type).fadeIn();
			},
			switchTab: function(tabElement, tabClass, contentClass, selectedContentID, callback) {
				jQuery('.' + tabClass).removeClass('selected');
				jQuery(tabElement).addClass('selected');
				jQuery('.' + contentClass).hide().html('<div class="wfLoadingWhite32"></div>');
				var func = function() {
				};
				if (callback) {
					func = function() {
						callback();
					};
				}
				jQuery('#' + selectedContentID).fadeIn(func);
			},
			activityTabChanged: function() {
				var mode = jQuery('.wfDataPanel:visible')[0].id.replace('wfActivity_', '');
				if (!mode) {
					return;
				}
				this.activityMode = mode;
				this.reloadActivities();
			},
			reloadActivities: function() {
				jQuery('#wfActivity_' + this.activityMode).html('<div class="wfLoadingWhite32"></div>');
				this.newestActivityTime = 0;
				this.updateTicker(true);
			},
			ucfirst: function(str) {
				str = "" + str;
				return str.charAt(0).toUpperCase() + str.slice(1);
			},
			makeIPTrafLink: function(IP) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=IPTraf&nonce=' + this.nonce + '&IP=' + encodeURIComponent(IP);
			},
			makeDiffLink: function(dat) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=diff&nonce=' + this.nonce +
					'&file=' + encodeURIComponent(this.es(dat['file'])) +
					'&cType=' + encodeURIComponent(this.es(dat['cType'])) +
					'&cKey=' + encodeURIComponent(this.es(dat['cKey'])) +
					'&cName=' + encodeURIComponent(this.es(dat['cName'])) +
					'&cVersion=' + encodeURIComponent(this.es(dat['cVersion']));
			},
			makeViewFileLink: function(file) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=view&nonce=' + this.nonce + '&file=' + encodeURIComponent(file);
			},
			makeViewOptionLink: function(option, siteID) {
				return WordfenceAdminVars.siteBaseURL + '?_wfsf=viewOption&nonce=' + this.nonce + '&option=' + encodeURIComponent(option) + '&site_id=' + encodeURIComponent(siteID);
			},
			makeTimeAgo: function(t) {
				var months = Math.floor(t / (86400 * 30));
				var days = Math.floor(t / 86400);
				var hours = Math.floor(t / 3600);
				var minutes = Math.floor(t / 60);
				if (months > 0) {
					days -= months * 30;
					return this.pluralize(months, 'month', days, 'day');
				} else if (days > 0) {
					hours -= days * 24;
					return this.pluralize(days, 'day', hours, 'hour');
				} else if (hours > 0) {
					minutes -= hours * 60;
					return this.pluralize(hours, 'hour', minutes, 'min');
				} else if (minutes > 0) {
					//t -= minutes * 60;
					return this.pluralize(minutes, 'minute');
				} else {
					return Math.round(t) + " seconds";
				}
			},
			pluralize: function(m1, t1, m2, t2) {
				if (m1 != 1) {
					t1 = t1 + 's';
				}
				if (m2 != 1) {
					t2 = t2 + 's';
				}
				if (m1 && m2) {
					return m1 + ' ' + t1 + ' ' + m2 + ' ' + t2;
				} else {
					return m1 + ' ' + t1;
				}
			},
			isValidIP: function(ip) {
				if (!ip) {
					return false;
				}
				ip = ip.replace(/ /g, '');
				if (ip.match(/^(?:\d{1,3}(?:\.|$)){4}/)) { //IPv4
					var octets = ip.split('.');
					if (octets.length != 4) {
						return false;
					}
					
					for (var i = 0; i < octets.length; i++) {
						if (parseInt(octets[i]) > 255) {
							return false;
						}
					}

					return !!this.inet_pton(ip);
				}
				else if (ip.match(/^((?:[\da-f]{1,4}(?::|)){0,8})(::)?((?:[\da-f]{1,4}(?::|)){0,8})$/i)) { //IPv6
					if (ip == '::') {
						return true;
					}

					var colonCount = ip.split(':').length - 1;
					var doubleColonPos = ip.indexOf('::');
					if (doubleColonPos > -1) {
						var expansionLength = ((doubleColonPos == 0 || doubleColonPos == ip.length - 2) ? 9 : 8) - colonCount;
						if (expansionLength == 0) { //Double-colon in a full IPv6 address
							return false;
						}
						
						var expansion = '';
						for (i = 0; i < expansionLength; i++) {
							expansion += ':0000';
						}
						ip = ip.replace('::', expansion + ':');
						ip = ip.replace(/(?:^\:|\:$)/, '', ip);
					}

					var ipGroups = ip.split(':');
					var ipBin = '';
					for (i = 0; i < ipGroups.length; i++) {
						var group = ipGroups[i];
						if (group.length > 4 || group.length == 0) {
							return false;
						}
						group = ("0000" + group).slice(-4);
						var b1 = parseInt(group.slice(0, 2), 16);
						var b2 = parseInt(group.slice(-2), 16);
						if (isNaN(b1) || isNaN(b2)) {
							return false;
						}
						ipBin += String.fromCharCode(b1) + String.fromCharCode(b2);
					}

					return ipBin.length == 16 ? true : false;
				}
				
				return false;
			},
			parseIPRange: function(range) {
				if (!range) {
					return false;
				}
				range = range.replace(/ /g, '');
				range = range.replace(/[\u2013-\u2015]/g, '-'); //Non-hyphen dashes to hyphen
				if (range && /^[^\-]+\-[^\-]+$/.test(range)) {
					var count = 1;
					var countOverflow = false;

					var ips = range.split('-');
					var ip1 = this.inet_pton(ips[0]);
					var ip2 = this.inet_pton(ips[1]);

					if (ip1 === false || !this.isValidIP(ips[0]) || ip2 === false || !this.isValidIP(ips[1])) {
						return false;
					}
					
					//Both to 16-byte binary strings
					var binStart = ("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff" + ip1).slice(-16);
					var binEnd = ("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff" + ip2).slice(-16);

					for (var i = 0; i < binStart.length; i++) {
						var n0 = binStart.charCodeAt(i);
						var n1 = binEnd.charCodeAt(i);

						if (i < 11 && n1 - n0 > 0) { //Based on Number.MAX_SAFE_INTEGER, which equals 2 ^ 53 - 1. Any of the first 9 bytes and part of the 10th that add to the range will put us over that
							countOverflow = true;
							break;
						}
						else if (i < 11 && n1 - n0 < 0) {
							return false;
						}

						count += (n1 - n0) << (8 * (15 - i));
						if (count < 1) {
							return false;
						}
					}
					
					return {start: ip1, end: ip2, count: count, countOverflow: countOverflow};
				}
				else if (range && /^[^\/]+\/\d+$/.test(range)) {
					var count = 1;
					var countOverflow = false;

					var components = range.split('/');
					var ip = this.inet_pton(components[0]);
					var bits = parseInt(components[1]);
					
					if (ip === false || !this.isValidIP(components[0])) {
						return false;
					}
					
					var binIP = ("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff" + ip).slice(-16);
					if (binIP.slice(12) === "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff") {
						if (bits < 1 || bits > 32) {
							return false;
						}
					}
					else {
						if (bits < 1 || bits > 128) {
							return false;
						}
					}
					
					if (bits >= 53) {
						countOverflow = true;
						count = Math.pow(2, 53) - 1; /* Number.MAX_SAFE_INTEGER is unavailable in IE */
					}
					else {
						count = Math.pow(2, bits);
					}

					return {ip: ip, bits: bits, count: count, countOverflow: countOverflow};
				}
				
				return false;
			},
			calcRangeTotal: function() {
				var range = jQuery('#ipRange').val();
				if (!range) {
					return;
				}
				range = range.replace(/ /g, '');
				range = range.replace(/[\u2013-\u2015]/g, '-'); //Non-hyphen dashes to hyphen
				if (range && /^[^\-]+\-[^\-]+$/.test(range)) {
					var count = 1;
					var countOverflow = false;
					var badRange = false;
					var badIP = false;
					
					var ips = range.split('-');
					var ip1 = this.inet_pton(ips[0]);
					var ip2 = this.inet_pton(ips[1]);
					
					if (ip1 === false || ip2 === false) {
						badIP = true;
					}
					else {
						//Both to 16-byte binary strings
						var binStart = ("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff" + ip1).slice(-16);
						var binEnd = ("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff" + ip2).slice(-16);
						
						for (var i = 0; i < binStart.length; i++) {
							var n0 = binStart.charCodeAt(i);
							var n1 = binEnd.charCodeAt(i);
							
							if (i < 11 && n1 - n0 > 0) { //Based on Number.MAX_SAFE_INTEGER, which equals 2 ^ 53 - 1. Any of the first 9 bytes and part of the 10th that add to the range will put us over that
								countOverflow = true;
								break;
							}
							else if (i < 11 && n1 - n0 < 0) {
								badRange = true;
								break;
							}
							
							count += (n1 - n0) << (8 * (15 - i));
							if (count < 1) {
								badRange = true;
								break;
							}
						}
					}
					
					if (badIP) {
						jQuery('#wfShowRangeTotal').html("<span style=\"color: #F00;\">Invalid IP entered.</span>"); 
						return;
					}
					else if (badRange) {
						jQuery('#wfShowRangeTotal').html("<span style=\"color: #F00;\">Invalid. Starting IP is greater than ending IP.</span>");
						return;
					}
					else if (countOverflow) {
						jQuery('#wfShowRangeTotal').html("<span style=\"color: #0A0;\">Valid: &gt;281474976710656 addresses in range.</span>");
						return;
					}

					jQuery('#wfShowRangeTotal').html("<span style=\"color: #0A0;\">Valid: " + count + " addresses in range.</span>"); 
				}
				else {
					jQuery('#wfShowRangeTotal').empty();
				}
			},
			whois: function(val) {
				val = val.replace(' ', '');
				if (!/\w+/.test(val)) {
					this.colorboxModal('300px', "Enter a valid IP or domain", "Please enter a valid IP address or domain name for your whois lookup.");
					return;
				}
				var self = this;
				jQuery('#whoisbutton').attr('disabled', 'disabled');
				jQuery('#whoisbutton').attr('value', 'Loading...');
				this.ajax('wordfence_whois', {
					val: val
				}, function(res) {
					jQuery('#whoisbutton').removeAttr('disabled');
					jQuery('#whoisbutton').attr('value', 'Look up IP or Domain');
					if (res.ok) {
						self.completeWhois(res);
					}
				});
			},
			completeWhois: function(res, ret) {
				ret = ret === undefined ? false : !!ret;
				var self = this;
				var rawhtml = "";
				var ipRangeTmpl = jQuery("<div><div class='wf-flex-row'>" +
					"<a class=\"wf-btn wf-btn-default wf-flex-row-0\" href=\"${adminUrl}\">Block This Network</a>" +
					"<span class='wf-flex-row-1 wf-padding-add-left'>{{html totalStr}}{{if totalStr.indexOf(ipRange) == -1}} (${ipRange}){{/if}}" +
					'{{if (totalIPs)}}<br>[${totalIPs} addresses in this network]{{/if}}' +
					"</span></div></div>");
				if (res.ok && res.result && res.result.rawdata && res.result.rawdata.length > 0) {
					for (var i = 0; i < res.result.rawdata.length; i++) {
						res.result.rawdata[i] = jQuery('<div />').text(res.result.rawdata[i]).html();
						res.result.rawdata[i] = res.result.rawdata[i].replace(/([a-zA-Z0-9\-._+]+@[a-zA-Z0-9\-._]+)/, "<a href=\"mailto:$1\">$1<\/a>");
						res.result.rawdata[i] = res.result.rawdata[i].replace(/(https?:\/\/[a-zA-Z0-9\-._+\/?&=#%:@;]+)/, "<a target=\"_blank\" rel=\"noopener noreferrer\" href=\"$1\">$1<\/a>");

						function wfm21(str, startStr, ipRange, offset, totalStr) {
							var ips = ipRange.split(/\s*\-\s*/);
							var totalIPs = NaN;
							if (ips[0].indexOf(':') < 0) {
								var ip1num = self.inet_aton(ips[0]);
								var ip2num = self.inet_aton(ips[1]);
								totalIPs = ip2num - ip1num + 1;
							}
							var adminUrl = "admin.php?page=WordfenceWAF&wfBlockRange=" + encodeURIComponent(ipRange) + "#top#blocking";
							return jQuery(ipRangeTmpl).tmpl({
								adminUrl: adminUrl,
								totalStr: totalStr,
								ipRange: ipRange,
								totalIPs: totalIPs
							}).wrapAll('<div>').parent().html();
						}

						function buildRangeLink2(str, startStr, octet1, octet2, octet3, octet4, cidrRange, offset, totalStr) {

							octet3 = octet3.length > 0 ? octet3 : '0';
							octet4 = octet4.length > 0 ? octet4 : '0';

							var rangeStart = [octet1, octet2, octet3, octet4].join('.');
							var rangeStartNum = self.inet_aton(rangeStart);
							cidrRange = parseInt(cidrRange, 10);
							if (!isNaN(rangeStartNum) && cidrRange > 0 && cidrRange < 32) {
								var rangeEndNum = rangeStartNum;
								for (var i = 32, j = 1; i >= cidrRange; i--, j *= 2) {
									rangeEndNum |= j;
								}
								rangeEndNum = rangeEndNum >>> 0;
								var ipRange = self.inet_ntoa(rangeStartNum) + ' - ' + self.inet_ntoa(rangeEndNum);
								var totalIPs = rangeEndNum - rangeStartNum + 1;
								var adminUrl = "admin.php?page=WordfenceWAF&wfBlockRange=" + encodeURIComponent(ipRange) + "#top#blocking";
								return jQuery(ipRangeTmpl).tmpl({
									adminUrl: adminUrl,
									totalStr: totalStr,
									ipRange: ipRange,
									totalIPs: totalIPs
								}).wrapAll('<div>').parent().html();

							}
							return str;
						}

						var rangeRegex = /(.*?)(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3} - \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|[a-f0-9:.]{3,} - [a-f0-9:.]{3,}).*$/i;
						var cidrRegex = /(.*?)(\d{1,3})\.(\d{1,3})\.?(\d{0,3})\.?(\d{0,3})\/(\d{1,3}).*$/i;
						if (rangeRegex.test(res.result.rawdata[i])) {
							res.result.rawdata[i] = res.result.rawdata[i].replace(rangeRegex, wfm21);
							rawhtml += res.result.rawdata[i];
						} else if (cidrRegex.test(res.result.rawdata[i])) {
							res.result.rawdata[i] = res.result.rawdata[i].replace(cidrRegex, buildRangeLink2);
							rawhtml += res.result.rawdata[i];
						} else {
							rawhtml += res.result.rawdata[i] + "<br />";
						}
					}
					rawhtml = rawhtml.replace(/<\/div><br \/>/g, '</div>');
					if (ret) {
						return rawhtml;
					}
					jQuery('#wfrawhtml').html(rawhtml);
				} else {
					rawhtml = '<span style="color: #F00;">Sorry, but no data for that IP or domain was found.</span>';
					if (ret) {
						return rawhtml;
					}
					jQuery('#wfrawhtml').html(rawhtml);
				}
			},
			blockIPUARange: function(ipRange, hostname, uaRange, referer, reason) {
				if (!/\w+/.test(reason)) {
					this.colorboxModal('300px', "Please specify a reason", "You forgot to include a reason you're blocking this IP range. We ask you to include this for your own record keeping.");
					return;
				}
				ipRange = ipRange.replace(/ /g, '').toLowerCase();
				ipRange = ipRange.replace(/[\u2013-\u2015]/g, '-'); //Non-hyphen dashes to hyphen
				if (ipRange) {
					var range = ipRange.split('-'),
						validRange;
					if (range.length !== 2) {
						validRange = false;
					} else if (range[0].match(':')) {
						validRange = this.inet_pton(range[0]) !== false && this.inet_pton(range[1]) !== false;
					} else if (range[0].match('.')) {
						validRange = this.inet_aton(range[0]) !== false && this.inet_aton(range[1]) !== false;
					}
					if (!validRange) {
						this.colorboxModal('300px', 'Specify a valid IP range', "Please specify a valid IP address range in the form of \"1.2.3.4 - 1.2.3.5\" without quotes. Make sure the dash between the IP addresses in a normal dash (a minus sign on your keyboard) and not another character that looks like a dash.");
						return;
					}
				}
				if (hostname && !/^[a-z0-9\.\*\-]+$/i.test(hostname)) {
					this.colorboxModalHTML('300px', 'Specify a valid hostname', '<i>' + this.htmlEscape(hostname) + '</i> is not valid hostname');
					return;
				}
				if (!(/\w+/.test(ipRange) || /\w+/.test(uaRange) || /\w+/.test(referer) || /\w+/.test(hostname))) {
					this.colorboxModal('300px', 'Specify an IP range, Hostname or Browser pattern', "Please specify either an IP address range, Hostname or a web browser pattern to match.");
					return;
				}
				var self = this;
				this.ajax('wordfence_blockIPUARange', {
					ipRange: ipRange,
					hostname: hostname,
					uaRange: uaRange,
					referer: referer,
					reason: reason
				}, function(res) {
					if (res.ok) {
						self.loadBlockRanges();
						return;
					}
				});
			},
			blockIP: function(IP, reason, callback) {
				var self = this;
				this.ajax('wordfence_blockIP', {
					IP: IP,
					reason: reason
				}, function(res) {
					if (res.errorMsg) {
						return;
					} else {
						self.reloadActivities();
						typeof callback === 'function' && callback();
					}
				});
			},
			unlockOutIP: function(IP) {
				var self = this;
				this.ajax('wordfence_unlockOutIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			unblockIP: function(IP, callback) {
				var self = this;
				this.ajax('wordfence_unblockIP', {
					IP: IP
				}, function(res) {
					self.reloadActivities();
					typeof callback === 'function' && callback();
				});
			},
			unblockNetwork: function(id) {
				var self = this;
				this.ajax('wordfence_unblockRange', {
					id: id
				}, function(res) {
					self.reloadActivities();
				});
			},
			unblockIPTwo: function(IP) {
				var self = this;
				this.ajax('wordfence_unblockIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			permBlockIP: function(IP) {
				var self = this;
				this.ajax('wordfence_permBlockIP', {
					IP: IP
				}, function(res) {
					self.staticTabChanged();
				});
			},
			makeElemID: function() {
				return 'wfElemGen' + this.elementGeneratorIter++;
			},
			pulse: function(sel) {
				jQuery(sel).fadeIn(function() {
					setTimeout(function() {
						jQuery(sel).fadeOut();
					}, 2000);
				});
			},
			twoFacStatus: function(msg) {
				this.colorboxModal('300px', 'Two Factor Status', msg);
			},
			addTwoFactor: function(username, phone, mode) {
				var self = this;
				this.ajax('wordfence_addTwoFactor', {
					username: username,
					phone: phone,
					mode: mode
				}, function(res) {
					if (res.ok) {
						if (mode == 'authenticator') {
							var totpURL = "otpauth://totp/" + encodeURI(res.homeurl) + encodeURI(" (" + res.username + ")") + "?" + res.uriQueryString + "&issuer=Wordfence"; 							
							var message = "Scan the code below with your authenticator app to add this account. Some authenticator apps also allow you to type in the text version instead.<br><div id=\"wfTwoFactorQRCodeTable\"></div><br><strong>Key:</strong> <input type=\"text\"" + (self.isSmallScreen ? "" : " size=\"45\"") + " value=\"" + res.base32Secret + "\" onclick=\"this.select();\" readonly>";
							if (res.recoveryCodes.length > 0) {
								message = message + "<br><br><strong>Recovery Codes</strong><br><p>Use one of these " + res.recoveryCodes.length + " codes to log in if you lose access to your authenticator device. Codes are 16 characters long, plus optional spaces. Each one may be used only once.</p><ul id=\"wfTwoFactorRecoveryCodes\">";

								var recoveryCodeFileContents = "Cellphone Sign-In Recovery Codes - " + res.homeurl + " (" + res.username + ")\r\n";
								recoveryCodeFileContents = recoveryCodeFileContents + "\r\nEach line of 16 letters and numbers is a single recovery code, with optional spaces for readability. When typing your password, enter \"wf\" followed by the entire code like \"mypassword wf1234 5678 90AB CDEF\". If your site shows a separate prompt for entering a code after entering only your username and password, enter only the code like \"1234 5678 90AB CDEF\". Your recovery codes are:\r\n\r\n";
								var splitter = /.{4}/g;
								for (var i = 0; i < res.recoveryCodes.length; i++) { 
									var code = res.recoveryCodes[i];
									var chunks = code.match(splitter);
									message = message + "<li>" + chunks[0] + " " + chunks[1] + " " + chunks[2] + " " + chunks[3] + "</li>";
									recoveryCodeFileContents = recoveryCodeFileContents + chunks[0] + " " + chunks[1] + " " + chunks[2] + " " + chunks[3] + "\r\n"; 
								}
								
								message = message + "</ul>";
								
								message = message + "<p class=\"wf-center\"><a href=\"#\" class=\"wf-btn wf-btn-default\" id=\"wfTwoFactorDownload\" target=\"_blank\" rel=\"noopener noreferrer\"><i class=\"dashicons dashicons-download\"></i> Download</a></p>";
							}

							message = message + "<p><em>This will be shown only once. Keep these codes somewhere safe.</em></p>";
							
							self.colorboxModalHTML((self.isSmallScreen ? '300px' : '440px'), "Authentication Code", message, {onComplete: function() { 
								jQuery('#wfTwoFactorQRCodeTable').qrcode({text: totpURL, width: (self.isSmallScreen ? 175 : 256), height: (self.isSmallScreen ? 175 : 256)});
								jQuery('#wfTwoFactorDownload').on('click', function(e) {
									e.preventDefault();
									e.stopPropagation();
									saveAs(new Blob([recoveryCodeFileContents], {type: "text/plain;charset=" + document.characterSet}), self.htmlEscape(res.homeurl) + "_" + self.htmlEscape(res.username) + "_recoverycodes.txt");
								});
							}});
						}
						else {
							if (res.recoveryCodes.length > 0) {
								var message = "<p>Use one of these " + res.recoveryCodes.length + " codes to log in if you are unable to access your phone. Codes are 16 characters long, plus optional spaces. Each one may be used only once.</p><ul id=\"wfTwoFactorRecoveryCodes\">";

								var recoveryCodeFileContents = "Cellphone Sign-In Recovery Codes - " + res.homeurl + " (" + res.username + ")\r\n";
								recoveryCodeFileContents = recoveryCodeFileContents + "\r\nEach line of 16 letters and numbers is a single recovery code, with optional spaces for readability. When typing your password, enter \"wf\" followed by the entire code like \"mypassword wf1234 5678 90AB CDEF\". If your site shows a separate prompt for entering a code after entering only your username and password, enter only the code like \"1234 5678 90AB CDEF\". Your recovery codes are:\r\n\r\n";
								var splitter = /.{4}/g;
								for (var i = 0; i < res.recoveryCodes.length; i++) {
									var code = res.recoveryCodes[i];
									var chunks = code.match(splitter);
									message = message + "<li>" + chunks[0] + " " + chunks[1] + " " + chunks[2] + " " + chunks[3] + "</li>";
									recoveryCodeFileContents = recoveryCodeFileContents + chunks[0] + " " + chunks[1] + " " + chunks[2] + " " + chunks[3] + "\r\n";
								}

								message = message + "<p class=\"wf-center\"><a href=\"#\" class=\"wf-btn wf-btn-default\" id=\"wfTwoFactorDownload\" target=\"_blank\" rel=\"noopener noreferrer\"><i class=\"dashicons dashicons-download\"></i> Download</a></p>";

								message = message + "</ul><p><em>This will be shown only once. Keep these codes somewhere safe.</em></p>";

								self.colorboxModalHTML((self.isSmallScreen ? '300px' : '400px'), "Recovery Codes", message, {onComplete: function() {
									jQuery('#wfTwoFactorDownload').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();
										saveAs(new Blob([recoveryCodeFileContents], {type: "text/plain;charset=" + document.characterSet}), self.htmlEscape(res.homeurl) + "_" + self.htmlEscape(res.username) + "_recoverycodes.txt");
									});
								}});
							}
						}
						
						var updatedTwoFac = jQuery('#wfTwoFacUserTmpl').tmpl({users: [res]});
						jQuery('#twoFactorUser-none').remove();
						jQuery('#wfTwoFacUsers > table > tbody:last-child').append(updatedTwoFac.find('tbody > tr'));
					}
				});
			},
			twoFacActivate: function(userID, code) {
				var self = this;
				this.ajax('wordfence_twoFacActivate', {
					userID: userID,
					code: code
				}, function(res) {
					if (res.ok) {
						var updatedTwoFac = jQuery('#wfTwoFacUserTmpl').tmpl({users: [res]});
						updatedTwoFac.find('tbody > tr').each(function(index, element) {
							jQuery('#' + jQuery(element).attr('id')).replaceWith(element);
						});
						self.twoFacStatus('Cellphone Sign-in activated for user.');
					}
				});
			},
			delTwoFac: function(userID) {
				this.ajax('wordfence_twoFacDel', {
					userID: userID
				}, function(res) {
					if (res.ok) {
						jQuery('#twoFactorUser-' + res.userID).fadeOut(function() {
							jQuery(this).remove();
							
							if (jQuery('#wfTwoFacUsers > table > tbody:last-child').children().length == 0) {
								jQuery('#wfTwoFacUsers').html(jQuery('#wfTwoFacUserTmpl').tmpl({users: []}));
							}
						});
					}
				});
			},
			loadTwoFactor: function() {
				this.ajax('wordfence_loadTwoFactor', {}, function(res) {
					jQuery('#wfTwoFacUsers').html(jQuery('#wfTwoFacUserTmpl').tmpl(res));
				});
			},
			getQueryParam: function(name) {
				name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
				var regexS = "[\\?&]" + name + "=([^&#]*)";
				var regex = new RegExp(regexS);
				var results = regex.exec(window.location.search);
				if (results == null) {
					return "";
				} else {
					return decodeURIComponent(results[1].replace(/\+/g, " "));
				}
			},
			inet_aton: function(dot) {
				var d = dot.split('.');
				return ((((((+d[0]) * 256) + (+d[1])) * 256) + (+d[2])) * 256) + (+d[3]);
			},
			inet_ntoa: function(num) {
				var d = num % 256;
				for (var i = 3; i > 0; i--) {
					num = Math.floor(num / 256);
					d = num % 256 + '.' + d;
				}
				return d;
			},

			inet_pton: function(a) {
				//  discuss at: http://phpjs.org/functions/inet_pton/
				// original by: Theriault
				//   example 1: inet_pton('::');
				//   returns 1: '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0'
				//   example 2: inet_pton('127.0.0.1');
				//   returns 2: '\x7F\x00\x00\x01'

				var r, m, x, i, j, f = String.fromCharCode;
				m = a.match(/^(?:\d{1,3}(?:\.|$)){4}/); // IPv4
				if (m) {
					m = m[0].split('.');
					m = f(m[0]) + f(m[1]) + f(m[2]) + f(m[3]);
					// Return if 4 bytes, otherwise false.
					return m.length === 4 ? m : false;
				}
				r = /^((?:[\da-f]{1,4}(?::|)){0,8})(::)?((?:[\da-f]{1,4}(?::|)){0,8})$/i;
				m = a.match(r); // IPv6
				if (m) {
					if (a == '::') {
						return "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
					}

					var colonCount = a.split(':').length - 1;
					var doubleColonPos = a.indexOf('::');
					if (doubleColonPos > -1) {
						var expansionLength = ((doubleColonPos == 0 || doubleColonPos == a.length - 2) ? 9 : 8) - colonCount;
						var expansion = '';
						for (i = 0; i < expansionLength; i++) {
							expansion += ':0000';
						}
						a = a.replace('::', expansion + ':');
						a = a.replace(/(?:^\:|\:$)/, '', a);
					}
					
					var ipGroups = a.split(':');
					var ipBin = '';
					for (i = 0; i < ipGroups.length; i++) {
						var group = ipGroups[i];
						if (group.length > 4) {
							return false;
						}
						group = ("0000" + group).slice(-4);
						var b1 = parseInt(group.slice(0, 2), 16);
						var b2 = parseInt(group.slice(-2), 16);
						if (isNaN(b1) || isNaN(b2)) {
							return false;
						}
						ipBin += f(b1) + f(b2);
					}
					
					return ipBin.length == 16 ? ipBin : false;
				}
				return false; // Invalid IP.
			},
			inet_ntop: function(a) {
				//  discuss at: http://phpjs.org/functions/inet_ntop/
				// original by: Theriault
				//   example 1: inet_ntop('\x7F\x00\x00\x01');
				//   returns 1: '127.0.0.1'
				//   example 2: inet_ntop('\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\1');
				//   returns 2: '::1'

				var i = 0,
					m = '',
					c = [];
				a += '';
				if (a.length === 4) { // IPv4
					return [
						a.charCodeAt(0), a.charCodeAt(1), a.charCodeAt(2), a.charCodeAt(3)].join('.');
				} else if (a.length === 16) { // IPv6
					for (i = 0; i < 16; i++) {
						c.push(((a.charCodeAt(i++) << 8) + a.charCodeAt(i))
							.toString(16));
					}
					return c.join(':')
						.replace(/((^|:)0(?=:|$))+:?/g, function(t) {
							m = (t.length > m.length) ? t : m;
							return t;
						})
						.replace(m || ' ', '::');
				} else { // Invalid length
					return false;
				}
			},

			deleteAdminUser: function(issueID) {
				var self = this;
				this.ajax('wordfence_deleteAdminUser', {
					issueID: issueID
				}, function(res) {
					if (res.ok) {
						self.loadIssues(function() {
							self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "Successfully deleted admin", "The admin user " + res.user_login + " was successfully deleted.");
						});
					} else if (res.errorMsg) {
						self.loadIssues(function() {
							WFAD.colorboxError(res.errorMsg, res.tokenInvalid);
						});
					}
				});
			},

			revokeAdminUser: function(issueID) {
				var self = this;
				this.ajax('wordfence_revokeAdminUser', {
					issueID: issueID
				}, function(res) {
					if (res.ok) {
						self.loadIssues(function() {
							self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), "Successfully revoked admin", "All capabilties of admin user " + res.user_login + " were successfully revoked.");
						});
					} else if (res.errorMsg) {
						self.loadIssues(function() {
							WFAD.colorboxError(res.errorMsg, res.tokenInvalid);
						});
					}
				});
			},

			windowHasFocus: function() {
				if (typeof document.hasFocus === 'function') {
					return document.hasFocus();
				}
				// Older versions of Opera
				return this._windowHasFocus;
			},

			htmlEscape: function(html) {
				return String(html)
					.replace(/&/g, '&amp;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&#39;')
					.replace(/</g, '&lt;')
					.replace(/>/g, '&gt;');
			},

			permanentlyBlockAllIPs: function(type) {
				var self = this;
				this.ajax('wordfence_permanentlyBlockAllIPs', {
					type: type
				}, function(res) {
					$('#wfTabs').find('.wfTab1').eq(0).trigger('click');
				});
			},

			showTimestamp: function(timestamp, serverTime, format) {
				serverTime = serverTime === undefined ? new Date().getTime() / 1000 : serverTime;
				format = format === undefined ? '${dateTime} (${timeAgo} ago)' : format;
				var date = new Date(timestamp * 1000);

				return jQuery.tmpl(format, {
					dateTime: date.toLocaleDateString() + ' ' + date.toLocaleTimeString(),
					timeAgo: this.makeTimeAgo(serverTime - timestamp)
				});
			},

			updateTimeAgo: function() {
				var self = this;
				jQuery('.wfTimeAgo-timestamp').each(function(idx, elem) {
					var el = jQuery(elem);
					var testEl = el;
					if (typeof jQuery === "function" && testEl instanceof jQuery) {
						testEl = testEl[0];
					}

					var rect = testEl.getBoundingClientRect();
					if (!(rect.top >= 0 && rect.left >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && rect.right <= (window.innerWidth || document.documentElement.clientWidth))) {
						return;
					}
					
					var timestamp = el.data('wfctime');
					if (!timestamp) {
						timestamp = el.attr('data-timestamp');
					}
					var serverTime = self.serverMicrotime;
					var format = el.data('wfformat');
					if (!format) {
						format = el.attr('data-format');
					}
					el.html(self.showTimestamp(timestamp, serverTime, format));
				});
			},

			wafData: {
				whitelistedURLParams: []
			},
			restoreWAFData: {
				whitelistedURLParams: []
			},
			
			wafWhitelistedBulkChangeEnabled: function(enabled) {
				$('.wf-whitelist-table-bulk-checkbox.wf-option-checkbox.wf-checked').each(function() {
					$(this).closest('tr').find('.wf-whitelist-item-enabled.wf-option-checkbox').each(function() {
						if (($(this).hasClass('wf-checked') && !enabled) || (!$(this).hasClass('wf-checked') && enabled)) {
							var tr = $(this).closest('tr');
							if (tr.is(':visible')) {
								WFAD.wafWhitelistedChangeEnabled(tr.data('key'), enabled);
							}
						}
					});
				})
			},
			
			wafWhitelistedChangeEnabled: function(key, enabled) {
				$('#waf-whitelisted-urls-wrapper .whitelist-table > tbody > tr[data-key="' + key + '"]').each(function() {
					var adding = !!$(this).data('adding');
					if (adding) {
						WFAD.pendingChanges['whitelistedURLParams']['add'][key]['data']['disabled'] = !enabled ? 1 : 0;
					}
					else {
						if (!(WFAD.pendingChanges['whitelistedURLParams'] instanceof Object)) {
							WFAD.pendingChanges['whitelistedURLParams'] = {};
						}

						if (!(WFAD.pendingChanges['whitelistedURLParams']['enabled'] instanceof Object)) {
							WFAD.pendingChanges['whitelistedURLParams']['enabled'] = {};
						}

						WFAD.pendingChanges['whitelistedURLParams']['enabled'][key] = !!enabled ? 1 : 0;
					}
					$(this).find('.wf-whitelist-item-enabled.wf-option-checkbox').toggleClass('wf-checked', !!enabled);
				});
			},

			wafWhitelistedBulkDelete: function() {
				$('.wf-whitelist-table-bulk-checkbox.wf-option-checkbox.wf-checked').each(function() {
					$(this).closest('tr').find('.wf-whitelist-item-enabled.wf-option-checkbox').each(function() {
						var tr = $(this).closest('tr');
						if (tr.is(':visible')) {
							WFAD.wafWhitelistedDelete(tr.data('key'));
						}
					});
				});
			},

			wafWhitelistedDelete: function(key) {
				$('#waf-whitelisted-urls-wrapper .whitelist-table > tbody > tr[data-key="' + key + '"]').each(function() {
					var adding = !!$(this).data('adding');
					if (adding) {
						delete WFAD.pendingChanges['whitelistedURLParams']['add'][key];
					}
					else {
						if (!(WFAD.pendingChanges['whitelistedURLParams'] instanceof Object)) {
							WFAD.pendingChanges['whitelistedURLParams'] = {};
						}

						if (!(WFAD.pendingChanges['whitelistedURLParams']['delete'] instanceof Object)) {
							WFAD.pendingChanges['whitelistedURLParams']['delete'] = {};
						}

						WFAD.pendingChanges['whitelistedURLParams']['delete'][key] = 1;
					}

					for (var i = 0; i < WFAD.wafData.whitelistedURLParams.length; i++) {
						var testKey = WFAD.wafData.whitelistedURLParams[i].path + '|' + WFAD.wafData.whitelistedURLParams[i].paramKey;
						if (testKey == key) {
							WFAD.wafData.whitelistedURLParams.splice(i, 1);
							break;
						}
					}
				});
			},

			wafConfigPageRender: function() {
				WFAD.wafData.ruleCount = 0;
				if (WFAD.wafData.rules) {
					WFAD.wafData.ruleCount = Object.keys(WFAD.wafData.rules).length;
				}
				
				var whitelistedIPsEl = $('#waf-whitelisted-urls-tmpl').tmpl(WFAD.wafData);
				$('#waf-whitelisted-urls-wrapper').html(whitelistedIPsEl);

				var rulesEl = $('#waf-rules-tmpl').tmpl(WFAD.wafData);
				$('#waf-rules-wrapper').html(rulesEl);
				
				$('#waf-show-all-rules-button').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					
					$('#waf-rules-wrapper').addClass('wf-show-all');
				});

				if (WFAD.wafData['rulesLastUpdated']) {
					var date = new Date(WFAD.wafData['rulesLastUpdated'] * 1000);
					WFAD.renderWAFRulesLastUpdated(date);
				}
				$(window).trigger('wordfenceWAFConfigPageRender');
			},

			renderWAFRulesLastUpdated: function(date) {
				var dateString = date.toString();
				if (date.toLocaleString) {
					dateString = date.toLocaleString();
				}
				$('#waf-rules-last-updated').text('Last Updated: ' + dateString)
					.css({
						'opacity': 0
					})
					.animate({
						'opacity': 1
					}, 500);
			},

			renderWAFRulesNextUpdate: function(date) {
				var dateString = date.toString();
				if (date.toLocaleString) {
					dateString = date.toLocaleString();
				}
				$('#waf-rules-next-update').text('Next Update Check: ' + dateString)
					.css({
						'opacity': 0
					})
					.animate({
						'opacity': 1
					}, 500);
			},

			wafUpdateRules: function(onSuccess) {
				var self = this;
				this.ajax('wordfence_updateWAFRules', {}, function(res) {
					self.wafData = res;
					self.restoreWAFData.rules = res.rules;
					self.restoreWAFData.rulesLastUpdated = res.rulesLastUpdated;
					self.wafConfigPageRender();
					if (self.wafData['updated']) {
						if (!self.wafData['isPaid']) {
							self.colorboxModalHTML((self.isSmallScreen ? '300px' : '400px'), 'Rules Updated', 'Your rules have been updated successfully. You are ' +
								'currently using the free version of Wordfence. ' +
								'Upgrade to Wordfence premium to have your rules updated automatically as new threats emerge. ' +
								'<a href="https://www.wordfence.com/wafUpdateRules1/wordfence-signup/">Click here to purchase a premium API key</a>. ' +
								'<em>Note: Your rules will still update every 30 days as a free user.</em>');
						} else {
							self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), 'Rules Updated', 'Your rules have been updated successfully.');
						}
					}
					else {
						self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), 'Rule Update Failed', 'No rules were updated. Please verify you have permissions to write to the /wp-content/wflogs directory.');
					}
					if (typeof onSuccess === 'function') {
						return onSuccess.apply(this, arguments);
					}
				});
			},

			dateFormat: function(date) {
				if (date instanceof Date) {
					if (date.toLocaleString) {
						return date.toLocaleString();
					}
					return date.toString();
				}
				return date;
			},

			confirmWAFConfigureAutoPrepend: function() {
				var self = this;
				this.ajax('wordfence_wafConfigureAutoPrepend', {}, function(res) {
					self.colorboxModal((self.isSmallScreen ? '300px' : '400px'), '.htaccess Updated', "Your .htaccess has been updated successfully. Please " +
						"verify your site is functioning normally.");
				});
			},
			
			updatePendingChanges: function() {
				$(window).off('beforeunload', WFAD._unsavedOptionsHandler);
				if (Object.keys(WFAD.pendingChanges).length) {
					$('#wf-cancel-changes').removeClass('wf-disabled');
					$('#wf-save-changes').removeClass('wf-disabled');
					$(window).on('beforeunload', WFAD._unsavedOptionsHandler);
				}
				else {
					$('#wf-cancel-changes').addClass('wf-disabled');
					$('#wf-save-changes').addClass('wf-disabled');
				}
			},
			
			_unsavedOptionsHandler: function(e) {
				var message = "You have unsaved changes to your options. If you leave this page, those changes will be lost."; //Only shows on older browsers, newer browsers don't allow message customization 
				e = e || window.event;
				if (e) {
					e.returnValue = message; //IE and Firefox
				}
				return message; //Others
			},
			
			setOption: function(key, value, successCallback, failureCallback) {
				var changes = {};
				changes[key] = value;
				this.ajax('wordfence_saveOptions', {changes: JSON.stringify(changes), page: WFAD.getParameterByName('page')}, function(res) {
					if (res.success) {
						typeof successCallback == 'function' && successCallback(res);
					}
					else {
						WFAD.colorboxModal((self.isSmallScreen ? '300px' : '400px'), 'Error Saving Option', res.error);
						typeof failureCallback == 'function' && failureCallback(res);
					} 
				});
			},

			saveOptions: function(successCallback, failureCallback) {
				if (!Object.keys(WFAD.pendingChanges).length) {
					return;
				}
				var self = this;

				this.ajax('wordfence_saveOptions', {changes: JSON.stringify(WFAD.pendingChanges), page: WFAD.getParameterByName('page')}, function(res) {
					if (res.success) {
						typeof successCallback == 'function' && successCallback(res); 
					}
					else {
						WFAD.colorboxModal((self.isSmallScreen ? '300px' : '400px'), 'Error Saving Options', res.error);
						typeof failureCallback == 'function' && failureCallback
					}
				});
			},
			
			enableAllOptionsPage: function() {
				this.ajax('wordfence_enableAllOptionsPage', {}, function(res) {
					if (res.redirect) {
						window.location.href = res.redirect;
					}
					else {
						WFAD.colorboxModal((self.isSmallScreen ? '300px' : '400px'), 'Error Enabling All Options Page', res.error);
					}
				});
			},

			getParameterByName: function(name, url) {
				if (!url) url = window.location.href;
				name = name.replace(/[\[\]]/g, "\\$&");
				var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
					results = regex.exec(url);
				if (!results) return null;
				if (!results[2]) return '';
				return decodeURIComponent(results[2].replace(/\+/g, " "));
			},

			base64_decode: function(s) {
				var e = {}, i, b = 0, c, x, l = 0, a, r = '', w = String.fromCharCode, L = s.length;
				var A = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
				for (i = 0; i < 64; i++) {
					e[A.charAt(i)] = i;
				}
				for (x = 0; x < L; x++) {
					c = e[s.charAt(x)];
					b = (b << 6) + c;
					l += 6;
					while (l >= 8) {
						((a = (b >>> (l -= 8)) & 0xff) || (x < (L - 2))) && (r += w(a));
					}
				}
				return r;
			},
			
			base64_encode: function (input) {
				var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
				var output = "";
				var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
				var i = 0;

				while (i < input.length) {
					chr1 = input.charCodeAt(i++);
					chr2 = input.charCodeAt(i++);
					chr3 = input.charCodeAt(i++);

					enc1 = chr1 >> 2;
					enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
					enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
					enc4 = chr3 & 63;

					if (isNaN(chr2)) {
						enc3 = enc4 = 64;
					}
					else if (isNaN(chr3)) {
						enc4 = 64;
					}

					output = output + chars.charAt(enc1) + chars.charAt(enc2) + chars.charAt(enc3) + chars.charAt(enc4);
				}

				return output;
			}
		};

		window['WFAD'] = window['wordfenceAdmin'];
		setInterval(function() {
			WFAD.updateTimeAgo();
		}, 1000);
	}
	jQuery(function() {
		wordfenceAdmin.init();
		jQuery(window).on('focus', function() {
			if (jQuery('body').hasClass('wordfenceLiveActivityPaused')) {
				jQuery('body').removeClass('wordfenceLiveActivityPaused');
			}
		});
	});

	$(function() {
		$('#wf-mobile-controls').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();

			$.wfMobileMenu({
				menuItems: [
					{title: 'Save Changes', primary: true, disabled: $('#wf-save-changes').hasClass('wf-disabled'), action: function() { $('#wf-save-changes').trigger('click'); }},
					{title: 'Cancel Changes', primary: false, disabled: $('#wf-cancel-changes').hasClass('wf-disabled'), action: function() { $('#wf-cancel-changes').trigger('click'); }},
					{title: 'Restore Defaults', primary: false, disabled: $('#wf-restore-defaults').hasClass('wf-disabled'), action: function() { $('#wf-restore-defaults').trigger('click'); }}
				]
			});
		});
		
		$('#wf-restore-defaults').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var restoreDefaultsSection = $(this).data('restoreDefaultsSection');
			var prompt = $('#wfTmpl_restoreDefaultsPrompt').tmpl();
			var promptHTML = $("<div />").append(prompt).html();
			WFAD.colorboxHTML((WFAD.isSmallScreen ? '300px' : '400px'), promptHTML, {overlayClose: false, closeButton: false, className: 'wf-modal', onComplete: function() {
				$('#wf-restore-defaults-prompt-cancel').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					WFAD.colorboxClose();
				});

				$('#wf-restore-defaults-prompt-confirm').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();

					WFAD.ajax('wordfence_restoreDefaults', {section: restoreDefaultsSection}, function(res) {
						if (res.success) {
							window.location.reload(true);
						}
						else {
							WFAD.colorboxClose();
							WFAD.colorboxModal((WFAD.isSmallScreen ? '300px' : '400px'), 'Error Restoring Defaults', res.error);
						}
					});
				});
			}});
		});
		
		$('#wf-save-changes').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();

			WFAD.saveOptions(function(res) {
				WFAD.pendingChanges = {}; 
				WFAD.updatePendingChanges();

				if (res.redirect) {
					window.location.href = res.redirect;
				}
				else {
					window.location.reload(true);
				}
			});
		});

		$('#wf-cancel-changes').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();

			WFAD.pendingChanges = {};

			WFAD.updatePendingChanges();

			//On/Off options
			$('.wf-option.wf-option-toggled').each(function() {
				var enabledValue = $(this).data('enabledValue');
				var disabledValue = $(this).data('disabledValue');
				var originalValue = $(this).data('originalValue');
				if (enabledValue == originalValue) {
					$(this).find('.wf-option-checkbox').addClass('wf-checked');
				}
				else {
					$(this).find('.wf-option-checkbox').removeClass('wf-checked');
				}
				$(this).trigger('change', [true]);
			});
			
			$('.wf-option-toggled-boolean-switch').each(function() {
				var enabledValue = $(this).data('enabledValue');
				var disabledValue = $(this).data('disabledValue');
				var originalValue = $(this).data('originalValue');
				if (enabledValue == originalValue) {
					$(this).find('.wf-boolean-switch').addClass('wf-active');
				}
				else {
					$(this).find('.wf-boolean-switch').removeClass('wf-active');
				}
				$(this).trigger('change', [true]);
			});

			$('.wf-option.wf-option-toggled-segmented').each(function() {
				var originalValue = $(this).data('originalValue');
				$(this).find('[type=radio]').each(function() {
					if (this.value == originalValue) {
						this.checked = true;
						return false;
					}
				});
				$(this).trigger('change', [true]);
			});

			//On/Off multiple options
			$('.wf-option.wf-option-toggled-multiple').each(function() {
				$(this).find('.wf-option-checkboxes > ul').each(function() {
					var enabledValue = $(this).data('enabledValue');
					var disabledValue = $(this).data('disabledValue');
					var originalValue = $(this).data('originalValue');
					if (enabledValue == originalValue) {
						$(this).find('.wf-option-checkbox').addClass('wf-checked');
					}
					else {
						$(this).find('.wf-option-checkbox').removeClass('wf-checked');
					}
				});
				$(this).trigger('change', [true]);
			});

			//On/Off options with menu
			$('.wf-option.wf-option-toggled-select').each(function() {
				var selectElement = $(this).find('.wf-option-select select');
				var enabledToggleValue = $(this).data('enabledToggleValue');
				var disabledToggleValue = $(this).data('disabledToggleValue');
				var originalToggleValue = $(this).data('originalToggleValue');
				if (enabledToggleValue == originalToggleValue) {
					$(this).find('.wf-option-checkbox').addClass('wf-checked');
					selectElement.attr('disabled', false);
				}
				else {
					$(this).find('.wf-option-checkbox').removeClass('wf-checked');
					selectElement.attr('disabled', true);
				}

				var originalSelectValue = $(this).data('originalSelectValue');
				$(this).find('.wf-option-select select').val(originalSelectValue).trigger('change');
				$(this).trigger('change', [true]);
			});

			//Menu options
			$('.wf-option.wf-option-select').each(function() {
				var originalSelectValue = $(this).data('originalSelectValue');
				$(this).find('.wf-option-select select').val(originalSelectValue).trigger('change');
				$(this).trigger('change', [true]);
			});

			//Text options
			$('.wf-option.wf-option-text').each(function() {
				var originalTextValue = $(this).data('originalTextValue');
				if (typeof originalTextValue !== 'undefined') {
					$(this).find('.wf-option-text input').val(originalTextValue);
				}
				$(this).trigger('change', [true]);
			});

			//Text area options
			$('.wf-option.wf-option-textarea').each(function() {
				var originalTextValue = $(this).data('originalTextValue');
				$(this).find('.wf-option-textarea textarea').val(originalTextValue);
				$(this).trigger('change', [true]);
			});

			//Token options
			$('.wf-option.wf-option-token').each(function() {
				var originalTokenValue = $(this).data('originalTokenValue');
				$(this).find('select').val(originalTokenValue).trigger('change');
				$(this).trigger('change', [true]);
			});
			
			//Switch options
			$('.wf-option.wf-option-switch').each(function() {
				var originalValue = $(this).data('originalValue');
				$(this).find('.wf-switch > li').each(function() {
					$(this).toggleClass('wf-active', originalValue == $(this).data('optionValue'));
				});
				$(this).trigger('change', [true]);
			});

			//Other options
			$(window).trigger('wfOptionsReset');
		});

		var select2s = $('.wf-select2');
		if (select2s.length && $.fn.wfselect2) {
			select2s.wfselect2({
				minimumResultsForSearch: 5
			});
		}


		if ($.fn.tooltip) {
			$('.wf-status-circular').each(function() {
				var circle = $(this);
				var tmplID = 'tooltip-' + this.id + '-tmpl';
				var circleTmpl = $('#' + tmplID);
				if (circleTmpl.length) {
					circle.tooltip({
						tooltipClass: "wf-circle-tooltip",
						position: {
							my: "left-40 bottom",
							at: "center top",
							using: function(obj, info) {
								var el = $(this);
								el.removeClass('wf-tooltip-vertical-top wf-tooltip-vertical-bottom ' +
									'wf-tooltip-horizontal-left wf-tooltip-horizontal-right')
								.addClass('wf-tooltip-vertical-' + info.vertical)
								.addClass('wf-tooltip-horizontal-' + info.horizontal);

								$(this).css({
									left: obj.left + 'px',
									top: obj.top + 'px'
								});
							}
						},
						items: this,
						close: function (event, ui) {
							ui.tooltip.hover(
								function () {
									$(this).stop(true).fadeTo(400, 1);
								},
								function () {
									$(this).fadeOut("400", function () {
										$(this).remove();
									})
								});
						},
						content: function() {
							var circleClone = $(this).clone();
							circleClone.find('svg, .wf-status-circular-text').css('opacity', 1.0);
							var circleHTML = $(circleClone).html();
							return circleTmpl.tmpl({
								statusCircle: circleHTML
							});
						}
					})
					// .tooltip('open');
				}
			});
		}
	});
})(jQuery);

//wfCircularProgress
jQuery.fn.wfCircularProgress = function(options) {
	jQuery(this).each(function() {
		var creationOptions;
		try {
			creationOptions = JSON.parse(jQuery(this).data('wfCircularProgressOptions'));
		}
		catch (e) { /* Ignore */ }
		if (typeof creationOptions !== 'object') {
			creationOptions = {};
		}
		var opts = jQuery.extend({}, jQuery.fn.wfCircularProgress.defaults, creationOptions, options);

		var center = Math.floor(opts.diameter / 2);
		var insetRadius = center - opts.strokeWidth * 2;

		var circumference = 2 * insetRadius * Math.PI;
		var finalOffset = -(circumference * (1 - opts.endPercent));
		var initialOffset = -(circumference);

		var terminatorRadius = Math.floor(opts.strokeWidth * 1.5);
		var terminatorDiameter = 2 * terminatorRadius;
		var finalTerminatorX = center - insetRadius * Math.cos(Math.PI * 2 * (opts.endPercent - 0.25));
		var finalTerminatorY = center + insetRadius * Math.sin(Math.PI * 2 * (opts.endPercent - 0.25));
		var initialTerminatorX = center - insetRadius * Math.cos(Math.PI * 2 * (opts.startPercent - 0.25));
		var initialTerminatorY = center + insetRadius * Math.sin(Math.PI * 2 * (opts.startPercent - 0.25));

		var terminatorSVG = "m 0,-" + terminatorRadius + " a " + terminatorRadius + "," + terminatorRadius + " 0 1 1 0," + terminatorDiameter + " a " + terminatorRadius + "," + terminatorRadius + " 0 1 1 0,-" + terminatorDiameter;
		
		jQuery(this).data('wfCircularProgressOptions', JSON.stringify(opts));
		
		jQuery(this).css('width', opts.diameter + 'px');
		jQuery(this).css('height', opts.diameter + 'px');

		var svg = jQuery(this).find('svg');
		if (svg.length == 0) { svg = document.createElementNS("http://www.w3.org/2000/svg", "svg"); jQuery(this).append(svg); }
		var inactivePath = jQuery(this).find('.wf-status-circular-inactive-path');
		if (inactivePath.length == 0) { inactivePath = document.createElementNS("http://www.w3.org/2000/svg", "path"); jQuery(inactivePath).addClass('wf-status-circular-inactive-path'); jQuery(svg).append(inactivePath); }
		var activePath = jQuery(this).find('.wf-status-circular-active-path');
		if (activePath.length == 0) { activePath = document.createElementNS("http://www.w3.org/2000/svg", "path"); jQuery(activePath).addClass('wf-status-circular-active-path'); jQuery(svg).append(activePath); }
		var terminator = jQuery(this).find('.wf-status-circular-terminator');
		if (terminator.length == 0) { terminator = document.createElementNS("http://www.w3.org/2000/svg", "path"); jQuery(terminator).addClass('wf-status-circular-terminator'); jQuery(svg).append(terminator); }
		var text = jQuery(this).find('.wf-status-circular-text');
		if (text.length == 0) { text = jQuery('<div class="wf-status-circular-text"></div>'); jQuery(this).append(text); }
		var pendingOverlay = jQuery(this).find('.wf-status-overlay-text');
		if (pendingOverlay.length == 0) { pendingOverlay = jQuery('<div class="wf-status-overlay-text"></div>'); jQuery(this).append(pendingOverlay); }

		jQuery(svg).attr('viewBox', '0 0 ' + opts.diameter + ' ' + opts.diameter);
		jQuery(svg).css('display', 'block');
		jQuery(svg).css('width', opts.diameter + 'px');
		jQuery(svg).css('height', opts.diameter + 'px');
		jQuery(inactivePath).attr('d', 'M ' + center + ',' + center + ' m 0,-' + insetRadius + ' a ' + insetRadius + ',' +insetRadius + ' 0 1 1 0,' + (2 * insetRadius) + ' a ' + insetRadius + ',' + insetRadius + ' 0 1 1 0,-' + (2 * insetRadius));
		jQuery(inactivePath).attr('stroke', opts.inactiveColor);
		jQuery(inactivePath).attr('stroke-width', opts.strokeWidth);
		jQuery(inactivePath).attr('fill-opacity', 0);
		jQuery(activePath).attr('d', 'M ' + center + ',' + center + ' m 0,-' + insetRadius + ' a ' + insetRadius + ',' + insetRadius + ' 0 1 1 0,' + (2 * insetRadius) + ' a ' + insetRadius + ',' + insetRadius + ' 0 1 1 0,-' + (2 * insetRadius));
		jQuery(activePath).attr('stroke', opts.color);
		jQuery(activePath).attr('stroke-width', opts.strokeWidth);
		jQuery(activePath).attr('stroke-dasharray', circumference + ',' + circumference);
		jQuery(activePath).attr('stroke-dashoffset', initialOffset);
		jQuery(activePath).attr('fill-opacity', 0);
		jQuery(terminator).attr('d', 'M ' + initialTerminatorX + ',' + initialTerminatorY + ' ' + terminatorSVG);
		jQuery(terminator).attr('stroke', opts.color);
		jQuery(terminator).attr('stroke-width', opts.strokeWidth);
		jQuery(terminator).attr('fill', '#ffffff');
		jQuery(pendingOverlay).html(opts.pendingMessage);

		jQuery(pendingOverlay).animate({
			opacity: opts.pendingOverlay ? 1.0 : 0.0,
		}, {
			duration: 500,
			step: function(value) {
				var opacity = 1.0 - (value * 0.8);
				jQuery(svg).css('opacity', opacity);
				jQuery(text).css('opacity', opacity);
			},
			complete: function() {
				jQuery(svg).css('opacity', opts.pendingOverlay ? 0.2 : 1.0);
				jQuery(text).css('opacity', opts.pendingOverlay ? 0.2 : 1.0);
			}
		});
		
		jQuery(activePath).animate({
			"stroke-dashoffset": finalOffset + 'px'
		}, {
			duration: 500,
			step: function(value) {
				var percentage = 1 + value / circumference;
				var x = center - insetRadius * Math.cos(Math.PI * 2 * (percentage - 0.25));
				var y = center + insetRadius * Math.sin(Math.PI * 2 * (percentage - 0.25));
				jQuery(terminator).attr('d', 'M ' + x + ',' + y + ' ' + terminatorSVG);
				text.html(Math.round(percentage * 100) + '%');
			},
			complete: function() {
				text.html(Math.round(opts.endPercent * 100) + '%');
			}
		});
	});
};

jQuery.fn.wfCircularProgress.defaults = {
	startPercent: 0,
	endPercent: 1,
	color: '#16bc9b',
	inactiveColor: '#ececec',
	strokeWidth: 3,
	diameter: 100,
	pendingOverlay: false,
	pendingMessage: 'Note: Status will update when changes are saved',
};

//wfDrawer
(function ($, document, window) {
	var defaults = {
		width: '600px',
		clickOverlayDismiss: false,
		content: false,
		onComplete: false,
	};
	
	var publicMethod = $.fn['wfDrawer'] = $['wfDrawer'] = function (options) {
		var opts = $.extend({}, defaults, options);

		var overlay = $('<div class="wf-drawer-overlay"></div>').css('opacity', 0);
		if (opts.clickOverlayDismiss) {
			overlay.on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				$.wfDrawer.close();
			});
		}
		$('body').append(overlay);

		var drawer = $('<div class="wf-drawer"></div>').css('width', opts.width).css('right', '-' + opts.width);
		if (opts.content) {
			drawer.append(opts.content);
		}
		$('body').append(drawer);

		overlay.animate({
			"opacity": 1
		});
		drawer.animate({
				"right": '0px'
			},
			{
				complete: function() {
					typeof opts.onComplete === 'function' && opts.onComplete();
				}
			});
	};

	publicMethod.close = function() {
		var overlay = $('.wf-drawer-overlay');
		overlay.animate({
				"opacity": 0
			},
			{
				complete: function() {
					overlay.remove();
				}
			});
		
		var drawer = $('.wf-drawer');
		drawer.animate({
				"right": '-' + drawer.css('width')
			},
			{
				complete: function() {
					drawer.remove();
				}
			});
	};
}(jQuery, document, window));

//wfMobileMenu
(function ($, document, window) {
	var defaults = {
		width: '280px',
		clickOverlayDismiss: true,
		menuItems: [],
		onDismiss: false,
	};

	var publicMethod = $.fn['wfMobileMenu'] = $['wfMobileMenu'] = function (options) {
		var opts = $.extend({}, defaults, options);

		var overlay = $('<div class="wf-mobile-menu-overlay"></div>').css('opacity', 0);
		if (opts.clickOverlayDismiss) {
			overlay.on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				typeof opts.onDismiss === 'function' && opts.onDismiss(false);
				$.wfMobileMenu.close();
			});
		}
		$('body').append(overlay);

		var menu = $('<div class="wf-mobile-menu"><ul class="wf-mobile-menu-items"></ul></div>').css('width', opts.width).css('bottom', '-9999px');
		var itemsWrapper = menu.find('.wf-mobile-menu-items');
		for (var i = 0; i < opts.menuItems.length; i++) {
			var button = $('<li><a href="#" class="wf-btn wf-btn-callout-subtle"></a></li>');
			button.find('a').text(opts.menuItems[i].title).css('width', opts.width).on('click', null, {action: opts.menuItems[i].action}, function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				typeof opts.onDismiss === 'function' && opts.onDismiss(true);
				$.wfMobileMenu.close();
				e.data.action();
			});
			
			if (opts.menuItems[i].primary) {
				button.find('a').addClass('wf-btn-primary');
			}
			else {
				button.find('a').addClass('wf-btn-default');
			}
			
			if (opts.menuItems[i].disabled) {
				button.find('a').addClass('wf-disabled');
			}
			
			itemsWrapper.append(button);
		}

		var button = $('<li class="wf-padding-add-top-small"><a href="#" class="wf-btn wf-btn-callout-subtle wf-btn-default">Close</a></li>');
		button.find('a').css('width', opts.width).on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();

			typeof opts.onDismiss === 'function' && opts.onDismiss(false);
			$.wfMobileMenu.close();
		});
		itemsWrapper.append(button);
		
		$('body').append(menu);
		menu.css('bottom', '-' + menu.height() + 'px');

		overlay.animate({
			"opacity": 1
		});
		menu.animate({
				bottom: '0px'
			},
			{
				complete: function() {
					typeof opts.onComplete === 'function' && opts.onComplete();
				}
			});
	};

	publicMethod.close = function() {
		var overlay = $('.wf-mobile-menu-overlay');
		overlay.animate({
				"opacity": 0
			},
			{
				complete: function() {
					overlay.remove();
				}
			});

		var menu = $('.wf-mobile-menu');
		menu.animate({
			bottom: '-' + menu.height() + 'px'
			},
			{
				complete: function() {
					menu.remove();
				}
			});
	};
}(jQuery, document, window));

/*! @source http://purl.eligrey.com/github/FileSaver.js/blob/master/FileSaver.js */
var saveAs=saveAs||function(e){"use strict";if(typeof e==="undefined"||typeof navigator!=="undefined"&&/MSIE [1-9]\./.test(navigator.userAgent)){return}var t=e.document,n=function(){return e.URL||e.webkitURL||e},r=t.createElementNS("http://www.w3.org/1999/xhtml","a"),o="download"in r,i=function(e){var t=new MouseEvent("click");e.dispatchEvent(t)},a=/constructor/i.test(e.HTMLElement),f=/CriOS\/[\d]+/.test(navigator.userAgent),u=function(t){(e.setImmediate||e.setTimeout)(function(){throw t},0)},d="application/octet-stream",s=1e3*40,c=function(e){var t=function(){if(typeof e==="string"){n().revokeObjectURL(e)}else{e.remove()}};setTimeout(t,s)},l=function(e,t,n){t=[].concat(t);var r=t.length;while(r--){var o=e["on"+t[r]];if(typeof o==="function"){try{o.call(e,n||e)}catch(i){u(i)}}}},p=function(e){if(/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(e.type)){return new Blob([String.fromCharCode(65279),e],{type:e.type})}return e},v=function(t,u,s){if(!s){t=p(t)}var v=this,w=t.type,m=w===d,y,h=function(){l(v,"writestart progress write writeend".split(" "))},S=function(){if((f||m&&a)&&e.FileReader){var r=new FileReader;r.onloadend=function(){var t=f?r.result:r.result.replace(/^data:[^;]*;/,"data:attachment/file;");var n=e.open(t,"_blank");if(!n)e.location.href=t;t=undefined;v.readyState=v.DONE;h()};r.readAsDataURL(t);v.readyState=v.INIT;return}if(!y){y=n().createObjectURL(t)}if(m){e.location.href=y}else{var o=e.open(y,"_blank");if(!o){e.location.href=y}}v.readyState=v.DONE;h();c(y)};v.readyState=v.INIT;if(o){y=n().createObjectURL(t);setTimeout(function(){r.href=y;r.download=u;i(r);h();c(y);v.readyState=v.DONE});return}S()},w=v.prototype,m=function(e,t,n){return new v(e,t||e.name||"download",n)};if(typeof navigator!=="undefined"&&navigator.msSaveOrOpenBlob){return function(e,t,n){t=t||e.name||"download";if(!n){e=p(e)}return navigator.msSaveOrOpenBlob(e,t)}}w.abort=function(){};w.readyState=w.INIT=0;w.WRITING=1;w.DONE=2;w.error=w.onwritestart=w.onprogress=w.onwrite=w.onabort=w.onerror=w.onwriteend=null;return m}(typeof self!=="undefined"&&self||typeof window!=="undefined"&&window||this.content);if(typeof module!=="undefined"&&module.exports){module.exports.saveAs=saveAs}else if(typeof define!=="undefined"&&define!==null&&define.amd!==null){define([],function(){return saveAs})}

!function(t){"use strict";if(t.URL=t.URL||t.webkitURL,t.Blob&&t.URL)try{return void new Blob}catch(e){}var n=t.BlobBuilder||t.WebKitBlobBuilder||t.MozBlobBuilder||function(t){var e=function(t){return Object.prototype.toString.call(t).match(/^\[object\s(.*)\]$/)[1]},n=function(){this.data=[]},o=function(t,e,n){this.data=t,this.size=t.length,this.type=e,this.encoding=n},i=n.prototype,a=o.prototype,r=t.FileReaderSync,c=function(t){this.code=this[this.name=t]},l="NOT_FOUND_ERR SECURITY_ERR ABORT_ERR NOT_READABLE_ERR ENCODING_ERR NO_MODIFICATION_ALLOWED_ERR INVALID_STATE_ERR SYNTAX_ERR".split(" "),s=l.length,u=t.URL||t.webkitURL||t,d=u.createObjectURL,f=u.revokeObjectURL,R=u,p=t.btoa,h=t.atob,b=t.ArrayBuffer,g=t.Uint8Array,w=/^[\w-]+:\/*\[?[\w\.:-]+\]?(?::[0-9]+)?/;for(o.fake=a.fake=!0;s--;)c.prototype[l[s]]=s+1;return u.createObjectURL||(R=t.URL=function(t){var e,n=document.createElementNS("http://www.w3.org/1999/xhtml","a");return n.href=t,"origin"in n||("data:"===n.protocol.toLowerCase()?n.origin=null:(e=t.match(w),n.origin=e&&e[1])),n}),R.createObjectURL=function(t){var e,n=t.type;return null===n&&(n="application/octet-stream"),t instanceof o?(e="data:"+n,"base64"===t.encoding?e+";base64,"+t.data:"URI"===t.encoding?e+","+decodeURIComponent(t.data):p?e+";base64,"+p(t.data):e+","+encodeURIComponent(t.data)):d?d.call(u,t):void 0},R.revokeObjectURL=function(t){"data:"!==t.substring(0,5)&&f&&f.call(u,t)},i.append=function(t){var n=this.data;if(g&&(t instanceof b||t instanceof g)){for(var i="",a=new g(t),l=0,s=a.length;s>l;l++)i+=String.fromCharCode(a[l]);n.push(i)}else if("Blob"===e(t)||"File"===e(t)){if(!r)throw new c("NOT_READABLE_ERR");var u=new r;n.push(u.readAsBinaryString(t))}else t instanceof o?"base64"===t.encoding&&h?n.push(h(t.data)):"URI"===t.encoding?n.push(decodeURIComponent(t.data)):"raw"===t.encoding&&n.push(t.data):("string"!=typeof t&&(t+=""),n.push(unescape(encodeURIComponent(t))))},i.getBlob=function(t){return arguments.length||(t=null),new o(this.data.join(""),t,"raw")},i.toString=function(){return"[object BlobBuilder]"},a.slice=function(t,e,n){var i=arguments.length;return 3>i&&(n=null),new o(this.data.slice(t,i>1?e:this.data.length),n,this.encoding)},a.toString=function(){return"[object Blob]"},a.close=function(){this.size=0,delete this.data},n}(t);t.Blob=function(t,e){var o=e?e.type||"":"",i=new n;if(t)for(var a=0,r=t.length;r>a;a++)Uint8Array&&t[a]instanceof Uint8Array?i.append(t[a].buffer):i.append(t[a]);var c=i.getBlob(o);return!c.slice&&c.webkitSlice&&(c.slice=c.webkitSlice),c};var o=Object.getPrototypeOf||function(t){return t.__proto__};t.Blob.prototype=o(new t.Blob)}("undefined"!=typeof self&&self||"undefined"!=typeof window&&window||this.content||this);
