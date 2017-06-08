/**
 * UpSolution Shortcode: us_message
 */
(function($){
	"use strict";

	$.fn.usMessage = function(){
		return this.each(function(){
			var $this = $(this),
				$closer = $this.find('.w-message-close');
			$closer.click(function(){
				$this.wrap('<div></div>');
				var $wrapper = $this.parent();
				$wrapper.css({overflow: 'hidden', height: $this.outerHeight(true)});
				$wrapper.performCSSTransition({
					height: 0
				}, 400, function(){
					$wrapper.remove();
					$us.$canvas.trigger('contentChange');
				}, 'cubic-bezier(.4,0,.2,1)');
			});
		});
	};

	$(function(){
		$('.w-message').usMessage();
	});
})(jQuery);


/**
 * Focus for different kind of forms
 */
jQuery(function($){
	$(document).on('focus', '.w-form-row-field input, .w-form-row-field textarea', function(){
		$(this).closest('.w-form-row').addClass('focused');
	});
	$(document).on('blur', '.w-form-row-field input, .w-form-row-field textarea', function(){
		$(this).closest('.w-form-row').removeClass('focused');
	});
});


/**
 * UpSolution Widget: w-dropdown
 */
(function($){
	"use strict";
	$.fn.wDropdown = function(){
		return this.each(function(){
			var $this = $(this),
				$list = $this.find('.w-dropdown-list'),
				$current = $this.find('.w-dropdown-current');
			var closeList = function(){
				$list.slideUpCSS(250, function(){
					$this.removeClass('active');
				});
				$us.$window.off('mouseup touchstart mousewheel DOMMouseScroll touchstart', closeListEvent);
			};
			var closeListEvent = function(e){
				if ($this.has(e.target).length !== 0) return;
				e.stopPropagation();
				e.preventDefault();
				closeList();
			};
			$list.hide();
			$current.click(function(){
				if ($this.hasClass('active')) {
					closeList();
					return;
				}
				$this.addClass('active');
				$list.slideDownCSS();
				$us.$window.on('mouseup touchstart mousewheel DOMMouseScroll touchstart', closeListEvent);
			});
		});
	};
	$(function(){
		$('.w-dropdown').wDropdown();
	});
})(jQuery);


/**
 * UpSolution Widget: w-blog
 */
(function($){
	"use strict";

	$us.WBlog = function(container, options){
		this.init(container, options);
	};

	$us.WBlog.prototype = {

		init: function(container, options){
			// Commonly used dom elements
			this.$container = $(container);
			this.$filters = this.$container.find('.g-filters-item');
			this.$list = this.$container.find('.w-blog-list');
			this.$items = this.$container.find('.w-blog-post');
			this.$pagination = this.$container.find('.g-pagination');
			this.$loadmore = this.$container.find('.g-loadmore');
			this.$preloader = this.$container.find('.w-blog-preloader');
			this.curCategory = '';
			this.paginationType = this.$pagination.length ? 'regular' : (this.$loadmore.length ? 'ajax' : 'none');
			this.items = [];
			this.loading = false;

			if (this.paginationType != 'none') {
				var $jsonContainer = this.$container.find('.w-blog-json');
				if ($jsonContainer.length == 0) return;
				this.ajaxData = $jsonContainer[0].onclick() || {};
				this.ajaxUrl = this.ajaxData.ajax_url || '';
				this.permalinkUrl = this.ajaxData.permalink_url || '';
				this.templateVars = this.ajaxData.template_vars || {};
				this.category = this.templateVars.query_args.category_name || '';
				this.curCategory = this.category;
				this.curPage = this.ajaxData.current_page || 1;
				this.perpage = this.ajaxData.perpage || this.$items.length;
				this.infiniteScroll = this.ajaxData.infinite_scroll || 0;
				$jsonContainer.remove();

			}

			if (this.paginationType == 'ajax') {
				if (this.templateVars.query_args.orderby == 'rand') {
					this.$items.each(function(index, item){
						this.items.push(parseInt(item.getAttribute('data-id')));
					}.bind(this));
				}
				this.$loadmore.on('click', function(){
					if (this.curPage < this.ajaxData.max_num_pages) {
						this.setState(this.curPage + 1);
					}
				}.bind(this));

				if (this.infiniteScroll) {
					$us.scroll.addWaypoint(this.$loadmore, '-70%', function(){
						this.$loadmore.click();
					}.bind(this));
				}
			}
			else if (this.paginationType == 'regular' && this.$filters.length) {
				this.paginationPcre = new RegExp('/page/([0-9]+)/$');
				this.location = location.href.replace(this.paginationPcre, '/');
				this.$navLinks = this.$container.find('.nav-links');
				var self = this;
				this.$navLinks.on('click', 'a', function(e){
					e.preventDefault();
					var arr,
						pageNum = (arr = self.paginationPcre.exec(this.href)) ? parseInt(arr[1]) : 1;
					self.setState(pageNum);
				});
			}

			if (this.$container.hasClass('with_isotope') && $.fn.isotope) {
				this.$list.imagesLoaded(function(){
					this.$list.isotope({
						itemSelector: '.w-blog-post',
						layoutMode: (this.$container.hasClass('isotope_fit_rows')) ? 'fitRows' : 'masonry',
						isOriginLeft: !$('.l-body').hasClass('rtl')
					});
					this.$list.isotope();
				}.bind(this));

				$us.$canvas.on('contentChange', function(){
					this.$list.imagesLoaded(function(){
						this.$list.isotope('layout');
					}.bind(this));
				}.bind(this));
			}

			this.$filters.each(function(index, filter){
				var $filter = $(filter),
					category = $filter.data('category');
				$filter.on('click', function(){
					if (category != this.curCategory) {
						this.setState(1, category);
						this.$filters.removeClass('active');
						$filter.addClass('active');
					}
				}.bind(this))
			}.bind(this));
		},

		setState: function(page, category){
			if (this.paginationType == 'none') {
				// Simple state changer
				this.$list.isotope({filter: (category == '*') ? '*' : ('.' + category)});
				this.curCategory = category;
				return;
			}

			if (this.loading) return;

			this.loading = true;

			category = category || this.curCategory;
			if (category == '*') {
				category = this.category;
			}

			this.templateVars.query_args.paged = page;
			this.templateVars.query_args.category_name = category;

			if (this.paginationType == 'ajax') {
				if (page == 1) {
					this.items = [];
					this.templateVars.query_args.post__not_in = this.items;
					this.$loadmore.addClass('done');
				} else {
					if (this.templateVars.query_args.orderby == 'rand') {
						this.templateVars.query_args.paged = 1;
						this.templateVars.query_args.post__not_in = this.items;
					}
					this.$loadmore.addClass('loading');
				}
			}

			if (this.paginationType != 'ajax' || page == 1) {
				this.$preloader.addClass('active');
				if (this.$list.data('isotope')) {
					this.$list.isotope('remove', this.$container.find('.w-blog-post'));
					this.$list.isotope('layout');
				} else {
					this.$container.find('.w-blog-post').remove();
				}
			}

			this.ajaxData.template_vars = JSON.stringify(this.templateVars);

			// In case we set paged to 1 for rand order - setting it back
			this.templateVars.query_args.paged = page;

			$.ajax({
				type: 'post',
				url: this.ajaxData.ajax_url,
				data: this.ajaxData,
				success: function(html){
					var $result = $(html),
						$container = $result.find('.w-blog-list'),
						$items = $container.children(),
						isotope = this.$list.data('isotope');
					$container.imagesLoaded(function(){
						this.beforeAppendItems($items);
						$items.appendTo(this.$list);
						$container.remove();
						var $sliders = $items.find('.w-slider');
						this.afterAppendItems($items);
						if (isotope) {
							isotope.appended($items);
						}
						$sliders.each(function(index, slider){
							$(slider).wSlider().find('.royalSlider').data('royalSlider').ev.on('rsAfterInit', function(){
								if (isotope) {
									this.$list.isotope('layout');
								}
							});
						}.bind(this));
						if (isotope) {
							this.$list.isotope('layout');
						}
						if (this.paginationType == 'regular') {
							this.$pagination.remove();

							var $pagination = $result.find('.g-pagination');

							this.$container.append($pagination);
							this.$pagination = this.$container.find('.g-pagination');

							var self = this;
							this.$pagination.find('.nav-links a').each(function(){
								var $link = $(this),
									linkURL = $link.attr('href');
								linkURL = linkURL.replace(self.ajaxUrl, self.permalinkUrl);
								$link.attr('href', linkURL);
							});

							this.paginationPcre = new RegExp('/page/([0-9]+)/$');
							this.location = location.href.replace(this.paginationPcre, '/');
							this.$navLinks = this.$container.find('.nav-links');

							this.$navLinks.on('click', 'a', function(e){
								e.preventDefault();
								var arr,
									pageNum = (arr = self.paginationPcre.exec(this.href)) ? parseInt(arr[1]) : 1;
								self.setState(pageNum);
							});

						}
						if (this.paginationType == 'ajax') {
							if (page == 1) {
								var $jsonContainer = $result.find('.w-blog-json');
								if ($jsonContainer.length) {
									var ajaxData = $jsonContainer[0].onclick() || {};
									this.ajaxData.max_num_pages = ajaxData.max_num_pages || this.ajaxData.max_num_pages;
								} else {
									this.ajaxData.max_num_pages = 1;
								}
							}

							if (this.templateVars.query_args.orderby == 'rand') {
								$items.each(function(index, item){
									this.items.push(parseInt(item.getAttribute('data-id')));
								}.bind(this));
							}

							if (this.templateVars.query_args.paged >= this.ajaxData.max_num_pages) {
								this.$loadmore.addClass('done');
							} else {
								this.$loadmore.removeClass('done');
								this.$loadmore.removeClass('loading');
							}

							if (this.infiniteScroll) {
								$us.scroll.addWaypoint(this.$loadmore, '-70%', function(){
									this.$loadmore.click();
								}.bind(this));
							}
						}
						this.$preloader.removeClass('active');
					}.bind(this));

					this.loading = false;

				}.bind(this),
				error: function(){
					this.$loadmore.removeClass('loading');
				}.bind(this)
			});


			this.curPage = page;
			this.curCategory = category;

		},
		/**
		 * Overloadable function for themes
		 * @param $items
		 */
		beforeAppendItems: function($items){
		},

		afterAppendItems: function($items){
		}

	};

	$.fn.wBlog = function(options){
		return this.each(function(){
			$(this).data('wBlog', new $us.WBlog(this, options));
		});
	};

})(jQuery);


/**
 * UpSolution Widget: w-tabs
 *
 * @requires $us.canvas
 */
!function($){
	"use strict";

	$us.WTabs = function(container, options){
		this.init(container, options);
	};

	$us.WTabs.prototype = {

		init: function(container, options){
			// Setting options
			var defaults = {
				duration: 300,
				easing: 'cubic-bezier(.78,.13,.15,.86)'
			};
			this.options = $.extend({}, defaults, options);
			this.isRtl = $('.l-body').hasClass('rtl');

			// Commonly used dom elements
			this.$container = $(container);
			this.$tabsList = this.$container.find('.w-tabs-list:first');
			this.$tabs = this.$tabsList.find('.w-tabs-item');
			this.$tabsH = this.$tabsList.find('.w-tabs-item-h');
			this.$sectionsWrapper = this.$container.find('.w-tabs-sections:first');
			this.$sectionsHelper = this.$sectionsWrapper.children();
			this.$sections = this.$sectionsHelper.find('.w-tabs-section');
			this.$headers = this.$sections.children('.w-tabs-section-header');
			this.$contents = this.$sections.children('.w-tabs-section-content');
			this.$line_charts = this.$container.find(".vc_line-chart");

			// Class variables
			this.width = 0;
			this.tabWidths = [];
			this.isTogglable = (this.$container.usMod('type') == 'togglable');
			// Basic layout
			this.basicLayout = this.$container.hasClass('accordion') ? 'accordion' : (this.$container.usMod('layout') || 'default');
			// Current active layout (may be switched to 'accordion')
			this.curLayout = this.basicLayout;
			this.responsive = $us.canvas.options.responsive;
			// Array of active tabs indexes
			this.active = [];
			this.count = this.$tabs.length;
			// Container width at which we should switch to accordion layout
			this.minWidth = 0;

			if (this.count == 0) return;

			// Preparing arrays of jQuery objects for easier manipulating in future
			this.tabs = $.map(this.$tabs.toArray(), $);
			this.sections = $.map(this.$sections.toArray(), $);
			this.headers = $.map(this.$headers.toArray(), $);
			this.contents = $.map(this.$contents.toArray(), $);

			$.each(this.tabs, function(index){
				if (this.tabs[index].hasClass('active')) {
					this.active.push(index);
				}
				this.tabs[index].add(this.headers[index]).on('click', function(e){
					e.preventDefault();
					// Toggling accordion sections
					if (this.curLayout == 'accordion' && this.isTogglable) {
						// Cannot toggle the only active item
						this.toggleSection(index);
					}
					// Setting tabs active item
					else if (index != this.active[0]) {
						this.openSection(index);
					}
				}.bind(this));
			}.bind(this));

			this.$tabsH.on('click', function(e){
				e.preventDefault();
			});

			// Boundable events
			this._events = {
				resize: this.resize.bind(this),
				contentChanged: function(){
					$us.$canvas.trigger('contentChange');
					this.$line_charts.length&&jQuery.fn.vcLineChart&&this.$line_charts.vcLineChart({reload:!1});// TODO: check if we can do this without hardcoding line charts init here;
				}.bind(this)
			};

			// Starting everything
			this.switchLayout(this.curLayout);
			if (this.curLayout != 'accordion' || !this.isTogglable) {
				this.openSection(this.active[0]);
			}

			setTimeout(this._events.resize, 50);
			$us.$window.on('resize load', this._events.resize);

			// Open tab on page load by hash
			if (window.location.hash) {
				var hash = window.location.hash.substr(1),
					$linkedSection = this.$container.find('.w-tabs-section[id="' + hash + '"]');
				if ($linkedSection.length && ( !$linkedSection.hasClass('active'))) {
					var $header = $linkedSection.find('.w-tabs-section-header');
					$header.click();
				}
			}
			// Support for external links to tabs
			$.each(this.tabs, function(index){
				if (this.headers[index].attr('href') != undefined) {
					var tabHref = this.headers[index].attr('href'),
						tabHeader = this.headers[index];
					$('a[href=' + tabHref + ']').on('click', function(e){
						e.preventDefault();
						if ($(this).hasClass('w-tabs-section-header', 'w-tabs-item-h')) {
							return;
						}
						tabHeader.click();
					});
				}
			}.bind(this));
		},

		switchLayout: function(to){
			this.cleanUpLayout(this.curLayout);
			this.prepareLayout(to);
			this.curLayout = to;
		},

		/**
		 * Clean up layout's special inline styles and/or dom elements
		 * @param from
		 */
		cleanUpLayout: function(from){
			if (from == 'default' || from == 'timeline' || from == 'modern' || from == 'trendy') {
				this.$sectionsWrapper.clearPreviousTransitions().resetInlineCSS('width', 'height');
				this.$sectionsHelper.clearPreviousTransitions().resetInlineCSS('position', 'width', 'left');
				this.$sections.resetInlineCSS('width');
				this.$container.removeClass('autoresize');
			}
			else if (from == 'accordion') {
				this.$container.removeClass('accordion');
				this.$contents.resetInlineCSS('height', 'padding-top', 'padding-bottom', 'display', 'opacity');
			}
			else if (from == 'ver') {
				this.$contents.resetInlineCSS('height', 'padding-top', 'padding-bottom', 'display', 'opacity');
			}
		},

		/**
		 * Apply layout's special inline styles and/or dom elements
		 * @param to
		 */
		prepareLayout: function(to){
			if (to == 'default' || to == 'timeline' || to == 'modern' || to == 'trendy') {
				this.$container.addClass('autoresize');
				this.$sectionsHelper.css('position', 'absolute');
			}
			else if (to == 'accordion') {
				this.$container.addClass('accordion');
				this.$contents.hide();
				for (var i = 0; i < this.active.length; i++) {
					if (this.contents[this.active[i]] !== undefined) {
						this.contents[this.active[i]].show();
					}
				}
			}
			else if (to == 'ver') {
				this.$contents.hide();
				this.contents[this.active[0]].show();
			}
		},

		/**
		 * Measure needed sizes and store them to this.tabWidths variable
		 *
		 * TODO Count minWidth here as well
		 */
		measure: function(){
			if (this.basicLayout == 'ver') {
				// Measuring minimum tabs width
				this.$tabsList.css('width', 0);
				var minTabWidth = this.$tabsList.outerWidth(true);
				this.$tabsList.css('width', '');
				// Measuring the mininum content width
				this.$container.addClass('measure');
				var minContentWidth = this.$sectionsWrapper.outerWidth(true);
				this.$container.removeClass('measure');
				// Measuring minimum tabs width for percent-based sizes
				var navWidth = this.$container.usMod('navwidth');
				if (navWidth != 'auto') {
					// Percent-based measure
					minTabWidth = Math.max(minTabWidth, minContentWidth * parseInt(navWidth) / (100 - parseInt(navWidth)));
				}
				this.minWidth = Math.max(480, minContentWidth + minTabWidth + 1);
			} else {
				this.tabWidths = [];
				// We hide active line temporarily to count tab sizes properly
				this.$container.addClass('measure');
				for (var index = 0; index < this.tabs.length; index++) {
					this.tabWidths.push(this.tabs[index].outerWidth(true));
				}
				this.$container.removeClass('measure');
				if (this.basicLayout == 'default' || this.basicLayout == 'timeline' || this.basicLayout == 'modern' || this.basicLayout == 'trendy') {
					// Array sum
					this.minWidth = this.tabWidths.reduce(function(pv, cv){
						return pv + cv;
					}, 0);
				}
			}
		},

		/**
		 * Open tab section
		 *
		 * @param index int
		 */
		openSection: function(index){
			if (this.sections[index] === undefined) return;
			if (this.curLayout == 'default' || this.curLayout == 'timeline' || this.curLayout == 'modern' || this.curLayout == 'trendy') {
				this.$container.removeClass('autoresize');
				var height = this.sections[index].height();
				this.$sectionsHelper.performCSSTransition({
					left: -this.width * (this.isRtl ? (this.count - index - 1 ) : index)
				}, this.options.duration, this._events.contentChanged, this.options.easing);
				this.$sectionsWrapper.performCSSTransition({
					height: height
				}, this.options.duration, function(){
					this.$container.addClass('autoresize');
				}.bind(this), this.options.easing);
			}
			else if (this.curLayout == 'accordion' || this.curLayout == 'ver') {
				if (this.contents[this.active[0]] !== undefined) {
					this.contents[this.active[0]].css('display', 'block').slideUp(this.options.duration);
				}
				this.contents[index].css('display', 'none').slideDown(this.options.duration, this._events.contentChanged);
				// Scrolling to the opened section at small window dimensions
				if (this.curLayout == 'accordion' && $us.canvas.winWidth < 768) {
					var newTop = this.headers[0].offset().top;
					for (var i = 0; i < index; i++) {
						newTop += this.headers[i].outerHeight();
					}
					$us.scroll.scrollTo(newTop, true);
				}
			}
			this._events.contentChanged();
			this.$tabs.removeClass('active');
			this.tabs[index].addClass('active');
			this.$sections.removeClass('active');
			this.sections[index].addClass('active');
			this.active[0] = index;
		},

		/**
		 * Toggle some togglable accordion section
		 *
		 * @param index
		 */
		toggleSection: function(index){
			// (!) Can only be used within accordion state
			var indexPos = $.inArray(index, this.active);
			if (indexPos != -1) {
				this.contents[index].css('display', 'block').slideUp(this.options.duration, this._events.contentChanged);
				this.tabs[index].removeClass('active');
				this.sections[index].removeClass('active');
				this.active.splice(indexPos, 1);
			}
			else {
				this.contents[index].css('display', 'none').slideDown(this.options.duration, this._events.contentChanged);
				this.tabs[index].addClass('active');
				this.sections[index].addClass('active');
				this.active.push(index);
			}
		},

		/**
		 * Resize-driven logics
		 */
		resize: function(){
			this.width = this.$container.width();
			this.$tabsList.removeClass('hidden');

			// Basic layout may be overriden
			if (this.responsive) {
				if (this.basicLayout == 'ver' && this.curLayout != 'ver') this.switchLayout('ver');
				if (this.curLayout != 'accordion') this.measure();
				var nextLayout = (this.width < this.minWidth) ? 'accordion' : this.basicLayout;
				if (nextLayout !== this.curLayout) this.switchLayout(nextLayout);
			}

			// Fixing tabs display
			if (this.curLayout == 'default' || this.curLayout == 'timeline' || this.curLayout == 'modern' || this.curLayout == 'trendy') {
				this.$container.addClass('autoresize');
				this.$sectionsWrapper.css('width', this.width);
				this.$sectionsHelper.css('width', this.count * this.width);
				this.$sections.css('width', this.width);
				if (this.contents[this.active[0]] !== undefined) {
					this.$sectionsHelper.css('left', -this.width * (this.isRtl ? (this.count - this.active[0] - 1) : this.active[0]));
					var height = this.sections[this.active[0]].height();
					this.$sectionsWrapper.css('height', height);
				}
			} else if (this.curLayout == 'ver') {
				var sectionsWrapperWidth = this.$sectionsWrapper.width();
			}
			this._events.contentChanged();
		}

	};

	$.fn.wTabs = function(options){
		return this.each(function(){
			$(this).data('wTabs', new $us.WTabs(this, options));
		});
	};

}(jQuery);


/**
 * UpSolution Shortcode: us_logos
 */
jQuery(function($){
	$(".w-logos.type_carousel .w-logos-list").each(function(){
		var $list = $(this),
			items = parseInt($list.data('items'));
		$list.owlCarousel({
			items: items,
			center: (items == 1),
			loop: true,
			rtl: $('.l-body').hasClass('rtl'),
			nav: $list.data('nav'),
			autoplay: $list.data('autoplay'),
			autoplayTimeout: $list.data('timeout'),
			autoplayHoverPause: true,
			responsive: {
				0: {items: 1, center: true},
				480: {items: Math.min(items, 2)},
				768: {items: Math.min(items, 3)},
				900: {items: Math.min(items, 4)},
				1200: {items: items}
			}
		});
	});
});


/**
 * UpSolution Shortcode: us_feedback
 */
jQuery(function($){

	$('.w-form.for_cform').each(function(){
		var $container = $(this),
			$form = $container.find('form:first'),
			$submitBtn = $form.find('.w-btn'),
			$resultField = $form.find('.w-form-message'),
			options = $container.find('.w-form-json')[0].onclick();

		$form.submit(function(event){
			event.preventDefault();

			// Prevent double-sending
			if ($submitBtn.hasClass('loading')) return;

			$resultField.usMod('type', false).html('');
			// Validation
			var errors = 0;
			$form.find('[data-required="true"]').each(function(){
				var $input = $(this),
					isEmpty = ($input.val() == ''),
					$row = $input.closest('.w-form-row'),
					errorText = options.errors[$input.attr('name')] || '';
				$row.toggleClass('check_wrong', isEmpty);
				$row.find('.w-form-row-state').html(isEmpty ? errorText : '');
				if (isEmpty) {
					errors++;
				}
			});

			if (errors != 0) return;

			$submitBtn.addClass('loading');
			$.ajax({
				type: 'POST',
				url: options.ajaxurl,
				dataType: 'json',
				data: $form.serialize(),
				success: function(result){
					if (result.success) {
						$resultField.usMod('type', 'success').html(result.data);
						$form.find('.w-form-row.check_wrong').removeClass('check_wrong');
						$form.find('.w-form-row.not-empty').removeClass('not-empty');
						$form.find('.w-form-state').html('');
						$form.find('input[type="text"], input[type="email"], textarea').val('');
					} else {
						$form.find('.w-form-row.check_wrong').removeClass('check_wrong');
						$form.find('.w-form-state').html('');
						if (result.data && typeof result.data == 'object') {
							for (var fieldName in result.data) {
								if (fieldName == 'empty_message') {
									var errorText = result.data[fieldName];
									$resultField.usMod('type', 'error').html(errorText);
									continue;
								}
								if (!result.data.hasOwnProperty(fieldName)) continue;
								var $input = $form.find('[name="' + fieldName + '"]'),
									errorText = result.data[fieldName];
								$input.closest('.w-form-row').addClass('check_wrong')
									.find('.w-form-row-state').html(errorText);
							}
						} else {
							$resultField.usMod('type', 'error').html(result.data);
						}
					}
				},
				complete: function(){
					$submitBtn.removeClass('loading');
				}
			});
		});

	});
});


/**
 * UpSolution Shortcode: us_counter
 */
jQuery(function($){
	$('.w-counter').each(function(index, elm){
		var $container = $(this),
			$number = $container.find('.w-counter-number'),
			initial = ($container.data('initial') || '0') + '',
			target = ($container.data('target') || '10') + '',
			prefix = $container.data('prefix') || '',
			suffix = $container.data('suffix') || '',
		// 0 for integers, 1+ for floats (number of digits after the decimal)
			precision = 0,
			usingComma = false;
		if (target.indexOf('.') != -1) {
			precision = target.length - 1 - target.indexOf('.');
		} else if (target.indexOf(',') != -1) {
			precision = target.length - 1 - target.indexOf(',');
			usingComma = true;
			target = target.replace(',', '.');
		}
		initial = window[precision ? 'parseFloat' : 'parseInt'](initial, 10);
		target = window[precision ? 'parseFloat' : 'parseInt'](target, 10);

		if ( /bot|googlebot|crawler|spider|robot|crawling/i.test(navigator.userAgent) ) {
			if (usingComma) {
				$number.html(prefix + target.toFixed(precision).replace('\.', ',') + suffix);
			} else {
				$number.html(prefix + target.toFixed(precision) + suffix);
			}

			return;
		}

		if (usingComma) {
			$number.html(prefix + initial.toFixed(precision).replace('\.', ',') + suffix);
		} else {
			$number.html(prefix + initial.toFixed(precision) + suffix);
		}
		$us.scroll.addWaypoint(this, '15%', function(){
			var current = initial,
				step = 25,
				stepValue = (target - initial) / 25,
				interval = setInterval(function(){
					current += stepValue;
					step--;
					if (usingComma) {
						$number.html(prefix + current.toFixed(precision).replace('\.', ',') + suffix);
					} else {
						$number.html(prefix + current.toFixed(precision) + suffix);
					}
					if (step <= 0) {
						if (usingComma) {
							$number.html(prefix + target.toFixed(precision).replace('\.', ',') + suffix);
						} else {
							$number.html(prefix + target.toFixed(precision) + suffix);
						}
						window.clearInterval(interval);
					}
				}, 40);
		});
	});
});


/**
 * UpSolution Shortcode: us_progbar
 */
jQuery(function($){
	$('.w-progbar').each(function(index, elm){
		var $container = $(this),
			$bar = $container.find('.w-progbar-bar-h'),
			count = $container.data('count') + '',
			$titleCount = $container.find('.w-progbar-title-count'),
			$barCount = $container.find('.w-progbar-bar-count');

		if (count === null) {
			count = 50;
		}

		if ( /bot|googlebot|crawler|spider|robot|crawling/i.test(navigator.userAgent) ) {
			$container.removeClass('initial');
			$titleCount.html(count + '%');
			$barCount.html(count + '%');
			return;
		}

		$titleCount.html('0%');
		$barCount.html('0%');

		$us.scroll.addWaypoint(this, '15%', function(){
			var current = 0,
				step = 40,
				stepValue = count / 40,
				interval = setInterval(function(){
					current += stepValue;
					step--;
					$titleCount.html(current.toFixed(0) + '%');
					$barCount.html(current.toFixed(0) + '%');
					if (step <= 0) {
						$titleCount.html(count + '%');
						$barCount.html(count + '%');
						window.clearInterval(interval);
					}
				}, 20);

			$container.removeClass('initial');
		});
	});
});


/**
 * UpSolution Shortcode: us_gallery
 */
jQuery(function($){
	if ($.fn.magnificPopup) {
		$('.w-gallery.link_media .w-gallery-list').each(function(){
			$(this).magnificPopup({
				type: 'image',
				delegate: 'a.w-gallery-item',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [0, 1],
					tPrev: $us.langOptions.magnificPopup.tPrev, // Alt text on left arrow
					tNext: $us.langOptions.magnificPopup.tNext, // Alt text on right arrow
					tCounter: $us.langOptions.magnificPopup.tCounter // Markup for "1 of 7" counter
				},
				removalDelay: 300,
				mainClass: 'mfp-fade',
				fixedContentPos: false
			});
		});
	}
	if ($.fn.isotope) {
		// Applying isotope to gallery
		$('.w-gallery.layout_masonry').each(function(index, gallery){
			if ($(gallery).hasClass('cols_1')) return; // TODO: check if we can replace this condition with CSS :not(.cols_1) selector

			var $container = $($(gallery).find('.w-gallery-list')),
				isotopeOptions = {
					layoutMode: 'masonry',
					isOriginLeft: !$('body').hasClass('rtl')
				};
			if ($container.parents('.w-tabs-section-content-h').length) {
				isotopeOptions.transitionDuration = 0;
			}
			$container.imagesLoaded(function(){
				$container.isotope(isotopeOptions);
				$container.isotope();
			});
			$us.$canvas.on('contentChange', function(){
				$container.imagesLoaded(function(){
					$container.isotope();
				});
			});
		});
	}
});


/**
 * UpSolution Shortcode: us_slider
 */
(function($){
	$.fn.wSlider = function(){
		return this.each(function(){
			var $this = $(this),
				$slider = $this.find('.royalSlider'),
				$options = $this.find('.w-slider-json'),
				options = $options[0].onclick() || {};
			$options.remove();
			if (!$.fn.royalSlider) {
				return;
			}
			// Always apply certain fit options for blog listing slider
			if ($this.parent().hasClass('w-blog-post-preview')) {
				options['imageScaleMode'] = 'fill';
			}
			$slider.royalSlider(options);
			var slider = $slider.data('royalSlider');
			if (options.fullscreen && options.fullscreen.enabled) {
				// Moving royal slider to the very end of body element to allow a proper fullscreen
				var rsEnterFullscreen = function(){
					$slider.appendTo($('body'));
					slider.ev.off('rsEnterFullscreen', rsEnterFullscreen);
					slider.exitFullscreen();
					slider.enterFullscreen();
					slider.ev.on('rsEnterFullscreen', rsEnterFullscreen);
					slider.ev.on('rsExitFullscreen', rsExitFullscreen);
				};
				slider.ev.on('rsEnterFullscreen', rsEnterFullscreen);
				var rsExitFullscreen = function(){
					$slider.prependTo($this);
					slider.ev.off('rsExitFullscreen', rsExitFullscreen);
					slider.exitFullscreen();
				};
			}
			$us.$canvas.on('contentChange', function(){
				$slider.parent().imagesLoaded(function(){
					slider.updateSliderSize();
				});
			});
		});
	};
	$(function(){
		jQuery('.w-slider').wSlider();
	});
})(jQuery);


/**
 * UpSolution Widget: w-portfolio
 */
!function($){
	"use strict";

	$us.WPortfolio = function(container, options){
		this.init(container, options);
	};

	$us.WPortfolio.prototype = {

		init: function(container, options){
			// Commonly used dom elements
			this.$container = $(container);

			if (this.$container.usMod('position') != 'isotope' || !$.fn.isotope) {
				// No scripts needed
				return;
			}

			this.$filters = this.$container.find('.g-filters-item');
			this.$list = this.$container.find('.w-portfolio-list');
			this.$items = this.$container.find('.w-portfolio-item');
			this.$pagination = this.$container.find('.g-pagination');
			this.$loadmore = this.$container.find('.g-loadmore');
			this.paginationType = this.$pagination.length ? 'regular' : (this.$loadmore.length ? 'ajax' : 'none');
			this.items = {};
			this.curCategory = '*';
			this.loading = false;

			this.$items.each(function(index, item){
				this.items[parseInt(item.getAttribute('data-id'))] = $(item);
			}.bind(this));

			this.isotopeOptions = {
				itemSelector: '.w-portfolio-item',
				layoutMode: 'masonry',
				masonry: {},
				isOriginLeft: !$('.l-body').hasClass('rtl')
			};

			if (this.$container.find('.w-portfolio-item.size_1x1').length) {
				this.itemWidth = 1;
				this.isotopeOptions.masonry.columnWidth = '.size_1x1';
			} else if (this.$container.find('.w-portfolio-item.size_1x2').length) {
				this.itemWidth = 1;
				this.isotopeOptions.masonry.columnWidth = '.size_1x2';
			} else {
				this.itemWidth = 2;
				this.isotopeOptions.masonry.columnWidth = '.w-portfolio-item';
			}

			if (this.paginationType != 'none') {
				var $jsonContainer = this.$container.find('.w-portfolio-json');
				if ($jsonContainer.length == 0) return;
				this.jsonData = $jsonContainer[0].onclick() || {};
				this.ajaxUrl = this.jsonData.ajax_url || '';
				this.templateVars = JSON.stringify(this.jsonData.template_vars || {});
				this.perpage = this.jsonData.perpage || this.$items.length;
				this.order = this.jsonData.order || {};
				this.sizes = this.jsonData.sizes || {};
				this.curPage = this.jsonData.page || 1;
				this.infiniteScroll = this.jsonData.infinite_scroll || 0;
				$jsonContainer.remove();
				this.isotopeOptions.sortBy = 'number';
				this.isotopeOptions.getSortData = {
					number: function(elm){
						return this.order['*'].indexOf(parseInt(elm.getAttribute('data-id')));
					}.bind(this)
				};
			}

			if (this.paginationType == 'ajax') {
				this.$loadmore.on('click', function(){
					var maxPage = Math.ceil(this.order[this.curCategory].length / this.perpage);
					if (this.curPage < maxPage) {
						this.setState(this.curPage + 1);
					}
				}.bind(this));
			}
			else if (this.paginationType == 'regular') {
				this.paginationPcre = new RegExp('/page/([0-9]+)/$');
				this.location = location.href.replace(this.paginationPcre, '/');
				this.$navLinks = this.$container.find('.nav-links');
				var self = this;
				this.$navLinks.on('click', 'a', function(e){
					e.preventDefault();
					var arr,
						pageNum = (arr = self.paginationPcre.exec(this.href)) ? parseInt(arr[1]) : 1;
					self.setState(pageNum);
				});
				this.renderPagination(this.curPage);
			}

			this.$filters.each(function(index, filter){
				var $filter = $(filter),
					category = $filter.data('category');
				$filter.on('click', function(){
					if (category != this.curCategory) {
						this.setState((this.paginationType == 'regular') ? 1 : this.curPage, category);
						this.$filters.removeClass('active');
						$filter.addClass('active');
					}
				}.bind(this))
			}.bind(this));

			// Applying isotope
			this.loading = true;
			this.$list.imagesLoaded(function(){
				this.$list.isotope(this.isotopeOptions);
				this.$list.isotope();
				this.loading = false;
				$us.$canvas.on('contentChange', function(){
					this.$list.isotope('layout');
				}.bind(this));
				$(window).on('resize', function(){
					this.$list.isotope('layout');
				}.bind(this));


				if (this.paginationType == 'ajax' && this.infiniteScroll) {
					$us.scroll.addWaypoint(this.$loadmore, '-70%', function(){
						this.$loadmore.click();
					}.bind(this));
				}
			}.bind(this));
		},

		setState: function(page, category){
			if (this.paginationType == 'none') {
				// Simple state changer
				this.$list.isotope({filter: (category == '*') ? '*' : ('.' + category)});
				this.curCategory = category;
				return;
			}

			if (this.loading) return;
			category = category || this.curCategory;
			var start = (this.paginationType == 'ajax') ? 0 : ((page - 1) * this.perpage),
				length = page * this.perpage,
				showIds = (this.order[category] || []).slice(start, length),
				loadIds = [],
				$newItems = [];
			$.each(showIds, function(i, id){
				// Determining which items we need to load via ajax and creating temporary stubs for them
				if (this.items[id] !== undefined) return;
				var itemSize = (this.sizes[id] || '1x1'),
					itemHtml = '<div class="w-portfolio-item size_' + itemSize + ' loading" data-id="' + id + '">' +
						'<div class="w-portfolio-item-anchor"><div class="g-preloader type_1"></div></div></div>';
				this.items[id] = $(itemHtml).appendTo(this.$list);
				$newItems.push(this.items[id][0]);
				loadIds.push(showIds[i]);
			}.bind(this));
			if (loadIds.length > 0) {
				// Loading new items
				var $insertedItems = $();
				$.ajax({
					type: 'post',
					url: this.ajaxUrl,
					data: {
						action: 'us_ajax_portfolio',
						ids: loadIds.join(','),
						template_vars: this.templateVars
					},
					success: function(html){
						var $container = $('<div>', {html: html}),
							$items = $container.children(),
							isotope = this.$list.data('isotope');
						$items.each(function(index, item){
							var $item = $(item),
								itemID = parseInt($item.data('id'));
							$item.imagesLoaded(function(){
								this.items[itemID].attr('class', $item.attr('class')).attr('style', $item.attr('style'));
								this.itemLoaded(itemID, $item);
								this.items[itemID].html($item.html());
								$insertedItems = $insertedItems.add(this.items[itemID]);
								if ($insertedItems.length >= loadIds.length) {
									$container.remove();
									this.itemsLoaded($insertedItems);
								}
								if (isotope) {
									if (this.itemWidth != 1) {
										if (this.$container.find('.w-portfolio-item.size_1x1').length) {
											this.itemWidth = 1;
											this.isotopeOptions.masonry.columnWidth = '.size_1x1';
										} else if (this.$container.find('.w-portfolio-item.size_1x2').length) {
											this.itemWidth = 1;
											this.isotopeOptions.masonry.columnWidth = '.size_1x2';
										} else {
											this.itemWidth = 2;
											this.isotopeOptions.masonry.columnWidth = '.w-portfolio-item';
										}
										if (this.itemWidth == 1) {
											this.$list.isotope(this.isotopeOptions);
										}
									}

									this.$list.isotope('layout');
								}
							}.bind(this));
						}.bind(this));

					}.bind(this)
				});
			}
			this.$list.isotope({
				filter: function(){
					return (showIds.indexOf(parseInt(this.getAttribute('data-id'))) != -1);
				}
			});
			if (loadIds.length > 0) {
				this.$list.isotope('insert', $newItems);
			}
			if (this.infiniteScroll) {
				$us.scroll.addWaypoint(this.$loadmore, '-70%', function(){
					this.$loadmore.click();
				}.bind(this));
			}
			this.curPage = page;
			this.curCategory = category;
			this.renderPagination();
		},

		renderPagination: function(){
			if (this.paginationType == 'ajax') {
				var maxPage = Math.ceil(this.order[this.curCategory].length / this.perpage);
				this.$loadmore[(this.curPage < maxPage) ? 'removeClass' : 'addClass']('done');
			}
			else if (this.paginationType == 'regular') {
				var maxPage = Math.ceil(this.order[this.curCategory].length / this.perpage),
					html = '';
				if (maxPage > 1) {
					if (this.curPage > 1) {
						html += '<a href="' + this.pageUrl(this.curPage - 1) + '" class="prev page-numbers"><span>&lt;</span></a>';
					} else {
						html += '<span class="prev page-numbers">&lt;</span>';
					}
					for (var i = 1; i <= maxPage; i++) {
						if (i != this.curPage) {
							html += '<a href="' + this.pageUrl(i) + '" class="page-numbers"><span>' + i + '</span></a>';
						} else {
							html += '<span class="page-numbers current"><span>' + i + '</span></span>';
						}
					}
					if (this.curPage < maxPage) {
						html += '<a href="' + this.pageUrl(this.curPage + 1) + '" class="next page-numbers"><span>&gt;</span></a>';
					} else {
						html += '<span class="next page-numbers">&gt;</span>';
					}
				}
				this.$navLinks.html(html);
			}
		},

		pageUrl: function(page){
			return (page == 1) ? this.location : (this.location + 'page/' + page + '/');
		},

		/**
		 * Overloadable function for themes
		 * @param $item
		 */
		itemLoaded: function($item){
		},

		/**
		 * Overloadable function for themes
		 * @param $item
		 */
		itemsLoaded: function($items){
		}

	};

	$.fn.wPortfolio = function(options){
		return this.each(function(){
			$(this).data('wPortfolio', new $us.WPortfolio(this, options));
		});
	};

	if ($.fn.magnificPopup) {
		$('.w-portfolio-list').each(function(){
			$(this).magnificPopup({
				type: 'image',
				delegate: 'a[ref=magnificPopupPortfolio]:visible',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [0, 1],
					tPrev: $us.langOptions.magnificPopup.tPrev, // Alt text on left arrow
					tNext: $us.langOptions.magnificPopup.tNext, // Alt text on right arrow
					tCounter: $us.langOptions.magnificPopup.tCounter // Markup for "1 of 7" counter
				},
				removalDelay: 300,
				mainClass: 'mfp-fade',
				fixedContentPos: false
			});
		});
	}

}(jQuery);

/**
 * UpSolution Widget: w-cart
 *
 * @requires $us.canvas
 * @requires $us.nav
 */
jQuery(function($){
	var $cart = $('.w-cart');
	if ($cart.length == 0) return;
	var $quantity = $cart.find('.w-cart-quantity');

	var updateCart = function(){
		var $mini_cart_amount = $cart.find('.us_mini_cart_amount'),
			mini_cart_amount = $mini_cart_amount.text();

		if (mini_cart_amount !== undefined) {
			mini_cart_amount = mini_cart_amount + '';
			mini_cart_amount = mini_cart_amount.match(/\d+/g);

			if (mini_cart_amount > 0) {
				$quantity.html(mini_cart_amount);
				$cart.removeClass('empty');
			} else {
				$quantity.html('0');
				$cart.addClass('empty');
			}

		} else {
			// fallback in case our action wasn't fired somehow
			var $quantities = $cart.find('.quantity'),
				total = 0;
			$quantities.each(function(){
				var quantity,
					text = $(this).text() + '',
					matches = text.match(/\d+/g);

				if (matches) {
					quantity = parseInt(matches[0], 10);
					total += quantity;
				}

			});

			if (total > 0) {
				$quantity.html(total);
				$cart.removeClass('empty');
			} else {
				$quantity.html('0');
				$cart.addClass('empty');
			}

		}

	};

	updateCart();

	$(document.body).bind('wc_fragments_loaded', function(){
		updateCart();
	});

	$(document.body).bind('wc_fragments_refreshed', function(){
		updateCart();
	});

	var $notification = $cart.find('.w-cart-notification'),
		$productName = $notification.find('.product-name'),
		$cartLink = $cart.find('.w-cart-link'),
		$dropdown = $cart.find('.w-cart-dropdown'),
		$quantity = $cart.find('.w-cart-quantity'),
		productName = $productName.text(),
		animationType = (window.$us !== undefined && window.$us.nav !== undefined) ? $us.nav.animationType : 'opacity',
		showFn = 'fadeInCSS',
		hideFn = 'fadeOutCSS',
		opened = false;

	if (animationType == 'height') {
		showFn = 'slideDownCSS';
		hideFn = 'slideUpCSS';
	}
	else if (animationType == 'mdesign') {
		showFn = 'showMD';
		hideFn = 'hideMD';
	}

	$notification.on('click', function(){
		$notification[hideFn]();
	});

	jQuery('body').bind('added_to_cart', function(event, fragments, cart_hash, $button){
		if (event === undefined) return;

		updateCart();

		productName = $button.closest('.product').find('.product-meta h3:first').text();
		$productName.html(productName);

		$notification[showFn](undefined, function(){
			var newTimerId = setTimeout(function(){
				$notification[hideFn]();
			}, 3000);
			$notification.data('animation-timers', $notification.data('animation-timers') + ',' + newTimerId);
		});
	});

	if ($.isMobile) {
		var outsideClickEvent = function(e){
			if (jQuery.contains($cart[0], e.target)) return;
			$dropdown[hideFn]();
			$us.$body.off('touchstart', outsideClickEvent);
			opened = false;
		};
		$cartLink.on('click', function(e){
			if (!opened) {
				e.preventDefault();
				$dropdown[showFn]();
				$us.$body.on('touchstart', outsideClickEvent);
			} else {
				$dropdown[hideFn]();
				$us.$body.off('touchstart', outsideClickEvent);
			}
			opened = !opened;
		});
	} else {
		var hideTimer = null;
		$cartLink.on('hover', function(){
			if (opened) return;
			$dropdown[showFn]();
			opened = true;
		});
		$cart.hover(function(){
			clearTimeout(hideTimer);
		}, function(){
			clearTimeout(hideTimer);
			hideTimer = setTimeout(function(){
				if (!opened) return;
				$dropdown[hideFn]();
				opened = false;
			}, 250);
		});
	}
});


/**
 * UpSolution Login Widget: widget_us_login
 *
 */
!function($){
	"use strict";

	$us.wUsLogin = function(container, options){
		this.$container = $(container);
		this.$form = this.$container.find('.w-form');
		this.$profile = this.$container.find('.w-profile');

		var $jsonContainer = this.$container.find('.w-profile-json');

		this.jsonData = $jsonContainer[0].onclick() || {};
		$jsonContainer.remove();

		this.ajaxUrl = this.jsonData.ajax_url || '';
		this.logoutRedirect = this.jsonData.logout_redirect || '';

		$.ajax({
			type: 'post',
			url: this.ajaxUrl,
			data: {
				action: 'us_ajax_user_info',
				logout_redirect: this.logoutRedirect
			},
			success: function(result){
				if (result.success) {
					var $avatar = this.$profile.find('.w-profile-avatar'),
						$name = this.$profile.find('.w-profile-name'),
						$logoutLink = this.$profile.find('.w-profile-link.for_logout')

					$avatar.html(result.data.avatar);
					$name.html(result.data.name);
					$logoutLink.attr('href', result.data.logout_url);

					this.$profile.removeClass('hidden');
				} else {
					this.$form.removeClass('hidden');
				}
			}.bind(this)
		});
	};

	$.fn.wUsLogin = function(options){
		return this.each(function(){
			$(this).data('wUsLogin', new $us.wUsLogin(this, options));
		});
	};

	$(function(){
		$('.widget_us_login').wUsLogin();
	});
}(jQuery);


/**
 * UpSolution Widget: w-maps
 *
 * Used for [us_gmaps] shortcode
 */
!function($){
	"use strict";

	$us.WMapsGeocodesCounter = 0; // counter of total geocode requests number
	$us.WMapsGeocodesRunning = false;
	$us.WMapsCurrentGeocode = 0; // current processing geocode
	$us.WMapsGeocodesMax = 5; // max number of simultaneous geocode requests allowed
	$us.WMapsGeocodesStack = {};

	$us.WMapsRunGeoCode = function(){
		if ($us.WMapsCurrentGeocode <= $us.WMapsGeocodesCounter) {
			$us.WMapsGeocodesRunning = true;
			if ($us.WMapsGeocodesStack[$us.WMapsCurrentGeocode] != null)
				$us.WMapsGeocodesStack[$us.WMapsCurrentGeocode]();
		} else {
			$us.WMapsGeocodesRunning = false;
		}
	};

	$us.WMaps = function(container, options){

		this.$container = $(container);

		var $jsonContainer = this.$container.find('.w-map-json'),
			jsonOptions = $jsonContainer[0].onclick() || {},
			$jsonStyleContainer = this.$container.find('.w-map-style-json'),
			jsonStyleOptions,
			markerOptions,
			shouldRunGeoCode = false;
		$jsonContainer.remove();
		if ($jsonStyleContainer.length) {
			jsonStyleOptions = $jsonStyleContainer[0].onclick() || {};
			$jsonStyleContainer.remove();
		}


		// Setting options
		var defaults = {};
		this.options = $.extend({}, defaults, jsonOptions, options);

		this._events = {
			redraw: this.redraw.bind(this)
		};

		var gmapsOptions = {
			el: '#' + this.$container.attr('id'),
			lat: 0,
			lng: 0,
			zoom: this.options.zoom,
			type: this.options.type,
			height: this.options.height + 'px',
			width: '100%',
			mapTypeId: google.maps.MapTypeId[this.options.maptype]
		};

		if (this.options.hideControls) {
			gmapsOptions.disableDefaultUI = true;
		}
		if (this.options.disableZoom) {
			gmapsOptions.scrollwheel = false;
		}
		if (this.options.disableDragging && ( !$us.$html.hasClass('no-touch'))) {
			gmapsOptions.draggable = false;
		}
		if (this.options.mapBgColor) {
			gmapsOptions.backgroundColor = this.options.mapBgColor;
		}

		this.GMapsObj = new GMaps(gmapsOptions);
		if (jsonStyleOptions != null && jsonStyleOptions != {}) {
			this.GMapsObj.map.setOptions({styles: jsonStyleOptions});
		}

		var that = this;

		if (this.options.latitude != null && this.options.longitude != null) {
			this.GMapsObj.setCenter(this.options.latitude, this.options.longitude);
		} else {
			var mapGeoCode = function(geocodeNum){
				GMaps.geocode({
					address: that.options.address,
					callback: function(results, status){
						if (status == 'OK') {
							var latlng = results[0].geometry.location;
							that.options.latitude = latlng.lat();
							that.options.longitude = latlng.lng();
							that.GMapsObj.setCenter(that.options.latitude, that.options.longitude);
							$us.WMapsCurrentGeocode++;
							$us.WMapsRunGeoCode();
						} else if (status == "OVER_QUERY_LIMIT") {
							setTimeout(function(){
								$us.WMapsRunGeoCode()
							}, 2000);
						}
					}
				});
			};
			shouldRunGeoCode = true;
			$us.WMapsGeocodesStack[$us.WMapsGeocodesCounter] = mapGeoCode;
			$us.WMapsGeocodesCounter++;
		}

		$.each(this.options.markers, function(i, val){
			markerOptions = {};
			if (that.options.icon != null) {
				markerOptions.icon = {
					url: that.options.icon.url,
					size: new google.maps.Size(that.options.icon.size[0], that.options.icon.size[1]),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(that.options.icon.anchor[0], that.options.icon.anchor[1])
				};
			}

			if (that.options.markers[i] != null) {

				if (that.options.markers[i].latitude != null && that.options.markers[i].longitude != null) {
					markerOptions.lat = that.options.markers[i].latitude;
					markerOptions.lng = that.options.markers[i].longitude;
					markerOptions.infoWindow = {content: that.options.markers[i].html};
					var marker = that.GMapsObj.addMarker(markerOptions);
					if (that.options.markers[i].infowindow) {
						marker.infoWindow.open(that.GMapsObj.map, marker);
					}
				} else {
					var markerGeoCode = function(geocodeNum){
						GMaps.geocode({
							address: that.options.markers[i].address,
							callback: function(results, status){
								if (status == 'OK') {
									var latlng = results[0].geometry.location;
									markerOptions.lat = latlng.lat();
									markerOptions.lng = latlng.lng();
									markerOptions.infoWindow = {content: that.options.markers[i].html};
									var marker = that.GMapsObj.addMarker(markerOptions);
									if (that.options.markers[i].infowindow) {
										marker.infoWindow.open(that.GMapsObj.map, marker);
									}
									$us.WMapsCurrentGeocode++;
									$us.WMapsRunGeoCode();
								} else if (status == "OVER_QUERY_LIMIT") {
									setTimeout(function(){
										$us.WMapsRunGeoCode()
									}, 2000);
								}
							}
						});
					};
					shouldRunGeoCode = true;
					$us.WMapsGeocodesStack[$us.WMapsGeocodesCounter] = markerGeoCode;
					$us.WMapsGeocodesCounter++;
				}
			}
		});

		if (shouldRunGeoCode && ( !$us.WMapsGeocodesRunning)) {
			$us.WMapsRunGeoCode();
		}

		$us.$canvas.on('contentChange', this._events.redraw);

		// In case some toggler was opened before the actual page load
		$us.$window.load(this._events.redraw);
	};

	$us.WMaps.prototype = {
		/**
		 * Fixing hidden and other breaking-cases maps
		 */
		redraw: function(){
			if (this.$container.is(':hidden')) return;
			this.GMapsObj.refresh();
			if (this.options.latitude != null && this.options.longitude != null) {
				this.GMapsObj.setCenter(this.options.latitude, this.options.longitude);
			}

		}
	};

	$.fn.wMaps = function(options){
		return this.each(function(){
			$(this).data('wMaps', new $us.WMaps(this, options));
		});
	};

	$(function(){
		$('.w-map').wMaps();
	});
}(jQuery);


/**
 * UpSolution Widget: w-sharing
 */
!function($){
	"use strict";

	$('.w-sharing.type_fixed.align_left, .w-sharing.type_fixed.align_right').each(function(){
		var $this = $(this);
		$this.css('margin-top', -.5 * $this.height());
	});

	$('.w-sharing.type_fixed.align_center').each(function(){
		var $this = $(this);
		$this.css('margin-left', -.5 * $this.width());
	});

	$('.w-sharing-item').on('click', function(){
		var $this = $(this);
		var opt = {
			url: window.location,
			text: document.title,
			lang: document.documentElement.lang,
			image: $('meta[name="og:image"]').attr('content') || ''
		};
		if ($this.attr('data-sharing-url') !== undefined && $this.attr('data-sharing-url') != '') {
			opt.url = $this.attr('data-sharing-url');
		}
		if ($this.attr('data-sharing-image') !== undefined && $this.attr('data-sharing-image') != '') {
			opt.image = $this.attr('data-sharing-image');
		}
		if (opt.image == '' || opt.image === undefined) {
			var first_image_src = $('img').first().attr('src');
			if (first_image_src != undefined && first_image_src != '') {
				opt.image = first_image_src;
			}
		}
		if ($this.hasClass('facebook')) {
			window.open("http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(opt.url) + "&t=" + encodeURIComponent(opt.text) + "", "", "toolbar=0, status=0, width=900, height=500");
		} else if ($this.hasClass('twitter')) {
			window.open("https://twitter.com/intent/tweet?text=" + encodeURIComponent(opt.text) + "&url=" + encodeURIComponent(opt.url), "", "toolbar=0, status=0, width=650, height=360");
		} else if ($this.hasClass('linkedin')) {
			window.open('https://www.linkedin.com/cws/share?url=' + encodeURIComponent(opt.url) + '&token=&isFramed=true', 'linkedin', 'toolbar=no,width=550,height=550');
		} else if ($this.hasClass('gplus')) {
			window.open("https://plus.google.com/share?hl=" + encodeURIComponent(opt.lang) + "&url=" + encodeURIComponent(opt.url), "", "toolbar=0, status=0, width=900, height=500");
		} else if ($this.hasClass('pinterest')) {
			window.open('http://pinterest.com/pin/create/button/?url=' + encodeURIComponent(opt.url) + '&media=' + encodeURIComponent(opt.image) + '&description=' + encodeURIComponent(opt.text), 'pinterest', 'toolbar=no,width=700,height=300');
		} else if ($this.hasClass('vk')) {
			window.open('http://vk.com/share.php?url=' + encodeURIComponent(opt.url) + '&title=' + encodeURIComponent(opt.text), '&description=&image=' + encodeURIComponent(opt.image), 'toolbar=no,width=700,height=300');
		} else if ($this.hasClass('email')) {
			window.location = 'mailto:?subject=' + opt.text + '&body=' + opt.url;
		}
	});
}(jQuery);


/**
 * UpSolution Widget: l-preloader
 */
!function($){
	"use strict";

	if ($('.l-preloader').length) {
		$('document').ready(function(){
			setTimeout(function(){
				$('.l-preloader').addClass('done');
			}, 500);
			setTimeout(function(){
				$('.l-preloader').addClass('hidden');
			}, 1000); // 500 ms after 'done' class is added
		});
	}
}(jQuery);
