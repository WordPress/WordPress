/**
 * UpSolution Theme Core JavaScript Code
 *
 * @requires jQuery
 */
if (window.$us === undefined) window.$us = {};

/**
 * Retrieve/set/erase dom modificator class <mod>_<value> for UpSolution CSS Framework
 * @param {String} mod Modificator namespace
 * @param {String} [value] Value
 * @returns {string|jQuery}
 */
jQuery.fn.usMod = function(mod, value){
	if (this.length == 0) return this;
	// Remove class modificator
	if (value === false) {
		this.get(0).className = this.get(0).className.replace(new RegExp('(^| )' + mod + '\_[a-z0-9]+( |$)'), '$2');
		return this;
	}
	var pcre = new RegExp('^.*?' + mod + '\_([a-z0-9]+).*?$'),
		arr;
	// Retrieve modificator
	if (value === undefined) {
		return (arr = pcre.exec(this.get(0).className)) ? arr[1] : false;
	}
	// Set modificator
	else {
		this.usMod(mod, false).get(0).className += ' ' + mod + '_' + value;
		return this;
	}
};

/**
 * Convert data from PHP to boolean the right way
 * @param {mixed} value
 * @returns {Boolean}
 */
$us.toBool = function(value){
	if (typeof value == 'string') return (value == 'true' || value == 'True' || value == 'TRUE' || value == '1');
	if (typeof value == 'boolean') return value;
	return !!parseInt(value);
};

// Detecting IE browser
$us.detectIE = function() {
	var ua = window.navigator.userAgent;

	var msie = ua.indexOf('MSIE ');
	if (msie > 0) {
		// IE 10 or older => return version number
		return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
	}

	var trident = ua.indexOf('Trident/');
	if (trident > 0) {
		// IE 11 => return version number
		var rv = ua.indexOf('rv:');
		return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
	}

	var edge = ua.indexOf('Edge/');
	if (edge > 0) {
		// Edge (IE 12+) => return version number
		return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
	}

	// other browser
	return false;
}

// Fixing hovers for devices with both mouse and touch screen
jQuery.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
jQuery('html').toggleClass('no-touch', !jQuery.isMobile);

/**
 * Commonly used jQuery objects
 */
!function($){
	$us.$window = $(window);
	$us.$document = $(document);
	$us.$html = $('html');
	$us.$body = $('.l-body:first');
	$us.$htmlBody = $us.$html.add($us.$body);
	$us.$canvas = $('.l-canvas:first');
}(jQuery);

/**
 * $us.canvas
 *
 * All the needed data and functions to work with overall canvas.
 */
!function($){
	"use strict";

	function USCanvas(options){

		// Setting options
		var defaults = {
			disableEffectsWidth: 900,
			responsive: true
		};
		this.options = $.extend({}, defaults, options || {});

		// Commonly used dom elements
		this.$header = $us.$canvas.find('.l-header');
		this.$main = $us.$canvas.find('.l-main');
		this.$titlebar = $us.$canvas.find('.l-titlebar');
		this.$sections = $us.$canvas.find('.l-section');
		this.$firstSection = this.$sections.first();
		this.$secondSection = this.$sections.eq(1);
		this.$fullscreenSections = this.$sections.filter('.height_full');
		this.$topLink = $('.w-toplink');

		// Canvas modificators
		this.sidebar = $us.$canvas.usMod('sidebar');
		this.type = $us.$canvas.usMod('type');
		// Initial header position
		this._headerPos = this.$header.usMod('pos');
		// Current header position
		this.headerPos = this._headerPos;
		this.headerInitialPos = $us.$body.usMod('header_inpos');
		this.headerBg = this.$header.usMod('bg');
		this.rtl = $us.$body.hasClass('rtl');

		// Will be used to count fullscreen sections heights and proper scroll positions
		this.scrolledOccupiedHeight = 0;

		// Used to prevent resize events on scroll for Android browsers
		this.isScrolling = false;
		this.scrollTimeout = false;
		this.isAndroid = /Android/i.test(navigator.userAgent);

		// Boundable events
		this._events = {
			scroll: this.scroll.bind(this),
			resize: this.resize.bind(this)
		};

		$us.$window.on('scroll', this._events.scroll);
		$us.$window.on('resize load', this._events.resize);
		// Complex logics requires two initial renders: before inner elements render and after
		setTimeout(this._events.resize, 25);
		setTimeout(this._events.resize, 75);
	}

	USCanvas.prototype = {

		/**
		 * Scroll-driven logics
		 */
		scroll: function(){
			var scrollTop = parseInt($us.$window.scrollTop());

			// Show/hide go to top link
			this.$topLink.toggleClass('active', (scrollTop >= this.winHeight));

			if (this.isAndroid) {
				this.isScrolling = true;
				if (this.scrollTimeout) clearTimeout(this.scrollTimeout);
				this.scrollTimeout = setTimeout(function () {
					this.isScrolling = false;
				}, 100);
			}
		},

		/**
		 * Resize-driven logics
		 */
		resize: function(){
			// Window dimensions
			this.winHeight = parseInt($us.$window.height());
			this.winWidth = parseInt($us.$window.width());

			// Disabling animation on mobile devices
			$us.$body.toggleClass('disable_effects', (this.winWidth <= this.options.disableEffectsWidth));

			// Vertical centering of fullscreen sections in IE 11
			var ieVersion = $us.detectIE();
			if ((ieVersion !== false && ieVersion == 11) && (this.$fullscreenSections.length > 0 && ! this.isScrolling)) {
				var adminBar = $('#wpadminbar'),
					adminBarHeight = (adminBar.length)?adminBar.height():0;
				this.$fullscreenSections.each(function(index, section){
					var $section = $(section),
						sectionHeight = this.winHeight,
						isFirstSection = (index == 0 && this.$titlebar.length == 0 && $section.is(this.$firstSection));
					// First section
					if (isFirstSection) {
						sectionHeight -= $section.offset().top;
					}
					// 2+ sections
					else {
						sectionHeight -= $us.header.scrolledOccupiedHeight + adminBarHeight;
					}
					if ($section.hasClass('valign_center')) {
						var $sectionH = $section.find('.l-section-h'),
							sectionTopPadding = parseInt($section.css('padding-top')),
							contentHeight = $sectionH.outerHeight(),
							topMargin;
						$sectionH.css('margin-top', '');
						// Section was extended by extra top padding that is overlapped by fixed solid header and not visible
						var sectionOverlapped = isFirstSection && $us.header.pos == 'fixed' && $us.header.bg != 'transparent' && $us.header.orientation != 'ver';
						if (sectionOverlapped) {
							// Part of first section is overlapped by header
							topMargin = Math.max(0, (sectionHeight - sectionTopPadding - contentHeight) / 2);
						} else {
							topMargin = Math.max(0, (sectionHeight - contentHeight) / 2 - sectionTopPadding);
						}
						$sectionH.css('margin-top', topMargin || '');
					}
				}.bind(this));
				$us.$canvas.trigger('contentChange');
			}

			// Fix scroll glitches that could occur after the resize
			this.scroll();
		}
	};

	$us.canvas = new USCanvas($us.canvasOptions || {});

}(jQuery);

/**
 * $us.header
 * Dev note: should be initialized after $us.canvas
 */
!function($){
	"use strict";
	function USHeader(settings){
		this.settings = settings || {};
		this.state = 'default'; // 'tablets' / 'mobiles'
		this.$container = $us.$canvas.find('.l-header');
		this.$topCell = this.$container.find('.l-subheader.at_top .l-subheader-cell:first');
		this.$middleCell = this.$container.find('.l-subheader.at_middle .l-subheader-cell:first');
		this.$bottomCell = this.$container.find('.l-subheader.at_bottom .l-subheader-cell:first');
		this.$showBtn = $('.w-header-show:first');
		this.orientation = $us.$body.usMod('header');
		this.pos = this.$container.usMod('pos'); // 'fixed' / 'static'
		this.bg = this.$container.usMod('bg'); // 'solid' / 'transparent'
		this.shadow = this.$container.usMod('shadow'); // 'none' / 'thin' / 'wide'

		// Will be used to count fullscreen sections heights and proper scroll positions
		this.scrolledOccupiedHeight = 0;

		this._events = {
			scroll: this.scroll.bind(this),
			resize: this.resize.bind(this),
			contentChange: function(){
				this._countScrollable();
			}.bind(this),
			hideMobileVerticalHeader: function(e){
				if ($.contains(this.$container[0], e.target)) return;
				$us.$body
					.off($.isMobile ? 'touchstart' : 'click', this._events.hideMobileVerticalHeader)
					.removeClass('header-show');
			}.bind(this)
		};
		this.$elms = {};
		this.$places = {
			hidden: this.$container.find('.l-subheader.for_hidden')
		};
		this.$container.find('.l-subheader-cell').each(function(index, cell){
			var $cell = $(cell);
			this.$places[$cell.parent().parent().usMod('at') + '_' + $cell.usMod('at')] = $cell;
		}.bind(this));
		var regexp = /(^| )ush_([a-z_]+)_([0-9]+)( |$)/;
		this.$container.find('[class*=ush_]').each(function(index, elm){
			var $elm = $(elm),
				matches = regexp.exec($elm.attr('class'));
			if (!matches) return;
			var id = matches[2] + ':' + matches[3];
			this.$elms[id] = $elm;
			if ($elm.is('.w-vwrapper, .w-hwrapper')) {
				this.$places[id] = $elm;
			}
		}.bind(this));
		// TODO Objects with the header elements
		$us.$window.on('scroll', this._events.scroll);
		$us.$window.on('resize load', this._events.resize);
		this.resize();

		$us.$canvas.on('contentChange', function(){
			if (this.orientation == 'ver') this.docHeight = $us.$document.height();
		}.bind(this));

		this.$container.on('contentChange', this._events.contentChange);

		this.$showBtn.on('click', function(e){
			if ($us.$body.hasClass('header-show')) return;
			e.stopPropagation();
			$us.$body
				.addClass('header-show')
				.on($.isMobile ? 'touchstart' : 'click', this._events.hideMobileVerticalHeader);
		}.bind(this));
	}

	$.extend(USHeader.prototype, {
		scroll: function(){
			var scrollTop = parseInt($us.$window.scrollTop());
			if (this.pos == 'fixed') {
				if (this.orientation == 'hor') {
					if (($us.canvas.headerInitialPos == 'bottom' || $us.canvas.headerInitialPos == 'below') && ($us.$body.usMod('state') == 'default')) {
						if (this.adminBarHeight) {
							scrollTop += this.adminBarHeight;
						}
						if (scrollTop >= this.headerTop && ( ! this.$container.hasClass('sticky'))) {
							this.$container.addClass('sticky');
							if (this.applyHeaderTop) {
								this.$container.css('top', '');
							}
						} else if (scrollTop < this.headerTop && this.$container.hasClass('sticky')) {
							this.$container.removeClass('sticky');
							if (this.applyHeaderTop) {
								this.$container.css('top', this.headerTop);
							}
						}

					} else {
						this.$container.toggleClass('sticky', scrollTop >= (this.settings[this.state].options.scroll_breakpoint || 100));
					}

				} else if ( ! jQuery.isMobile && this.$container.hasClass('scrollable') && this.docHeight > this.headerHeight + this.htmlTopMargin) {
					var scrollRangeDiff = this.headerHeight - $us.canvas.winHeight + this.htmlTopMargin;
					if (this._sidedHeaderScrollRange === undefined) {
						this._sidedHeaderScrollRange = [0, scrollRangeDiff];
					}
					if (scrollTop <= this._sidedHeaderScrollRange[0]) {
						this._sidedHeaderScrollRange[0] = Math.max(0, scrollTop);
						this._sidedHeaderScrollRange[1] = this._sidedHeaderScrollRange[0] + scrollRangeDiff;
						this.$container.css({
							position: 'fixed',
							top: this.htmlTopMargin
						});
					}
					else if (this._sidedHeaderScrollRange[0] < scrollTop && scrollTop < this._sidedHeaderScrollRange[1]) {
						this.$container.css({
							position: 'absolute',
							top: this._sidedHeaderScrollRange[0]
						});
					}
					else if (this._sidedHeaderScrollRange[1] <= scrollTop) {
						this._sidedHeaderScrollRange[1] = Math.min(this.docHeight - $us.canvas.winHeight, scrollTop);
						this._sidedHeaderScrollRange[0] = this._sidedHeaderScrollRange[1] - scrollRangeDiff;
						this.$container.css({
							position: 'fixed',
							top: $us.canvas.winHeight - this.headerHeight
						});
					}
				}
			}
		},
		resize: function(){
			var newState = 'default';
			if (window.innerWidth <= 900) newState = (window.innerWidth <= 600) ? 'mobiles' : 'tablets';
			this.setState(newState);
			if (this.pos == 'fixed' && this.orientation == 'hor') {
				var isSticky = this.$container.hasClass('sticky');
				this.$container.addClass('notransition');
				if (!isSticky) this.$container.addClass('sticky');
				this.scrolledOccupiedHeight = this.$container.height();
				if (!isSticky) this.$container.removeClass('sticky');
				// Removing with a small delay to prevent css glitch
				setTimeout(function(){
					this.$container.removeClass('notransition');
				}.bind(this), 50);
			} else /*if (this.orientation == 'ver' || this.pos == 'static')*/ {
				this.scrolledOccupiedHeight = 0;
			}

			if (this.orientation == 'hor') {
				if (this.pos == 'fixed' && ($us.canvas.headerInitialPos == 'bottom' || $us.canvas.headerInitialPos == 'below') && ($us.$body.usMod('state') == 'default')) {
					var adminBar = $('#wpadminbar');
					this.adminBarHeight = (adminBar.length)?adminBar.height():0;

					this.headerTop = $us.canvas.$firstSection.outerHeight() + this.adminBarHeight;
					if ($us.canvas.headerInitialPos == 'bottom' ) {
						this.$container.css('bottom', 'auto');
						this.headerTop = this.headerTop - this.$container.outerHeight();
						this.$container.css('bottom', '');
					}
					if ( ! $us.canvas.$firstSection.hasClass('height_full')) {
						this.applyHeaderTop = true;
						this.$container.css('top', this.headerTop);
					}

				} else {
					this.applyHeaderTop = false;
					this.$container.css('top', '');
				}
			} else {
				this.applyHeaderTop = false;
				this.$container.css('top', '');
			}

			this._countScrollable();
			this.scroll();
		},
		setState: function(newState){
			if (newState == this.state) return;
			var newOrientation = this.settings[newState].options.orientation || 'hor',
				newPos = $us.toBool(this.settings[newState].options.sticky) ? 'fixed' : 'static',
				newBg = $us.toBool(this.settings[newState].options.transparent) ? 'transparent' : 'solid',
				newShadow = this.settings[newState].options.shadow || 'thin';
			if (newOrientation == 'ver') {
				newPos = 'fixed';
				newBg = 'solid';
			}
			this.state = newState;
			// Don't change the order: orientation -> pos -> bg -> layout
			this._setOrientation(newOrientation);
			this._setPos(newPos);
			this._setBg(newBg);
			this._setShadow(newShadow);
			this._setLayout(this.settings[newState].layout || {});
			$us.$body.usMod('state', newState);
			if (newState == 'default') $us.$body.removeClass('header-show');
			// Updating the main menu because of dependencies
			if ($us.nav !== undefined) $us.nav.resize();
		},
		_setOrientation: function(newOrientation){
			if (newOrientation == this.orientation) return;
			$us.$body.usMod('header', newOrientation);
			this.orientation = newOrientation;
		},
		_countScrollable: function(){
			if (this.orientation == 'ver' && this.pos == 'fixed' && this.state == 'default') {
				this.docHeight = $us.$document.height();
				this.htmlTopMargin = parseInt($us.$html.css('margin-top'));
				this.headerHeight = this.$topCell.height() + this.$middleCell.height() + this.$bottomCell.height();
				if (this.headerHeight > $us.canvas.winHeight - this.htmlTopMargin) {
					this.$container.addClass('scrollable');
				} else if (this.$container.hasClass('scrollable')) {
					this.$container.removeClass('scrollable').resetInlineCSS('position', 'top', 'bottom');
					delete this._sidedHeaderScrollRange;
				}
				if (this.headerHeight + this.htmlTopMargin >= this.docHeight) {
					this.$container.css({
						position: 'absolute',
						top: 0
					});
				}
			} else if (this.$container.hasClass('scrollable')) {
				this.$container.removeClass('scrollable').resetInlineCSS('position', 'top', 'bottom');
				delete this._sidedHeaderScrollRange;
			}
		},
		_setPos: function(newPos){
			if (newPos == this.pos) return;
			this.$container.usMod('pos', newPos);
			if (newPos == 'static') {
				this.$container.removeClass('sticky');
			}
			this.pos = newPos;
			this._countScrollable();
		},
		_setBg: function(newBg){
			if (newBg == this.bg) return;
			this.$container.usMod('bg', newBg);
			this.bg = newBg;
		},
		_setShadow: function(newShadow){
			if (newShadow == this.shadow) return;
			this.$container.usMod('shadow', newShadow);
			this.shadow = newShadow;
		},
		/**
		 * Recursive function to place elements based on their ids
		 * @param {Array} elms
		 * @param {jQuery} $place
		 * @private
		 */
		_placeElements: function(elms, $place){
			for (var i = 0; i < elms.length; i++) {
				var elmId;
				if (typeof elms[i] == 'object') {
					// Wrapper
					elmId = elms[i][0];
					if (this.$places[elmId] === undefined || this.$elms[elmId] === undefined) continue;
					this.$elms[elmId].appendTo($place);
					this._placeElements(elms[i].shift(), this.$places[elmId]);
				} else {
					// Element
					elmId = elms[i];
					if (this.$elms[elmId] === undefined) continue;
					this.$elms[elmId].appendTo($place);
				}
			}
		},
		_setLayout: function(newLayout){
			// Retrieving the currently shown layout structure
			var curLayout = {};
			$.each(this.$places, function(place, $place){
			}.bind(this));
			for (var place in newLayout) {
				if (!newLayout.hasOwnProperty(place) || this.$places[place] === undefined) continue;
				this._placeElements(newLayout[place], this.$places[place]);
			}
		}
	});
	$us.header = new USHeader($us.headerSettings || {});
}(jQuery);

/**
 * $us.nav
 *
 * Header navigation will all the possible states
 *
 * @requires $us.canvas
 */
!function($){

	function USNav(){

		// Commonly used dom elements
		this.$nav = $('.l-header .w-nav:first');
		if (this.$nav.length == 0) return;
		this.$control = this.$nav.find('.w-nav-control');
		this.$items = this.$nav.find('.w-nav-item');
		this.$list = this.$nav.find('.w-nav-list.level_1');
		this.$subItems = this.$list.find('.w-nav-item.menu-item-has-children');
		this.$subAnchors = this.$list.find('.w-nav-item.menu-item-has-children > .w-nav-anchor');
		this.$subLists = this.$list.find('.w-nav-item.menu-item-has-children > .w-nav-list');
		this.$anchors = this.$nav.find('.w-nav-anchor');

		// Setting options
		this.options = this.$nav.find('.w-nav-options:first')[0].onclick() || {};

		// In case the nav doesn't exist, do nothing
		if (this.$nav.length == 0) return;

		this.type = this.$nav.usMod('type');

		this.mobileOpened = false;
		this.animationType = this.$nav.usMod('animation');
		var showFn = 'fadeInCSS',
			hideFn = 'fadeOutCSS';
		if (this.animationType == 'height') {
			showFn = 'slideDownCSS';
			hideFn = 'slideUpCSS';
		}
		else if (this.animationType == 'mdesign') {
			showFn = 'showMD';
			hideFn = 'hideMD';
		}

		// Mobile menu toggler
		this.$control.on('click', function(){
			this.mobileOpened = !this.mobileOpened;
			if (this.mobileOpened) {
				// Closing opened sublists
				this.$items.filter('.opened').removeClass('opened');
				this.$subLists.resetInlineCSS('display', 'opacity', 'height', 'padding-top', 'padding-bottom', 'margin-top');
				this.$list.slideDownCSS(250, this._events.contentChanged);
			} else {
				this.$list.slideUpCSS(250, this._events.contentChanged);
			}
		}.bind(this));

		// Boundable events
		this._events = {
			// Mobile submenu togglers
			toggle: function(e){
				if (this.type != 'mobile') return;
				e.stopPropagation();
				e.preventDefault();
				var $item = $(e.currentTarget).closest('.w-nav-item'),
					$sublist = $item.children('.w-nav-list');
				if ($item.hasClass('opened')) {
					$item.removeClass('opened');
					$sublist.slideUpCSS(250, this._events.contentChanged);
				} else {
					$item.addClass('opened');
					$sublist.slideDownCSS(250, this._events.contentChanged);
				}
			}.bind(this),
			resize: this.resize.bind(this),
			contentChanged: function(){
				if (this.type == 'mobile' && $us.header.orientation == 'hor' && $us.canvas.headerPos == 'fixed') {
					this.setFixedMobileMaxHeight();
				}
				$us.header.$container.trigger('contentChange');
			}.bind(this)
		};

		// Toggle on item clicks
		if (this.options.mobileBehavior) {
			this.$subAnchors.on('click', this._events.toggle);
		}
		// Toggle on arrows
		else {
			this.$list.find('.w-nav-item.menu-item-has-children > .w-nav-anchor > .w-nav-arrow').on('click', this._events.toggle);
		}
		// Mark all the togglable items
		this.$subItems.each(function(){
			var $this = $(this),
				$parentItem = $this.parent().closest('.w-nav-item');
			if ($parentItem.length == 0 || $parentItem.usMod('columns') === false) $this.addClass('togglable');
		});
		// Touch device handling in default (notouch) layout
		if (!$us.$html.hasClass('no-touch')) {
			this.$list.find('.w-nav-item.menu-item-has-children.togglable > .w-nav-anchor').on('click', function(e){
				if (this.type == 'mobile') return;
				e.preventDefault();
				var $this = $(e.currentTarget),
					$item = $this.parent(),
					$list = $item.children('.w-nav-list');

				// Second tap: going to the URL
				if ($item.hasClass('opened')) return location.assign($this.attr('href'));
				$list[showFn]();
				$item.addClass('opened');
				var outsideClickEvent = function(e){
					if ($.contains($item[0], e.target)) return;
					$item.removeClass('opened');
					$list[hideFn]();
					$us.$body.off('touchstart', outsideClickEvent);
				};

				$us.$body.on('touchstart', outsideClickEvent);
			}.bind(this));
		}
		// Desktop device hovers
		else {
			this.$subItems
				.filter('.togglable')
				.on('mouseenter', function(e){
					if (this.type == 'mobile') return;
					var $list = $(e.currentTarget).children('.w-nav-list');
					$list[showFn]();
				}.bind(this))
				.on('mouseleave', function(e){
					if (this.type == 'mobile') return;
					var $list = $(e.currentTarget).children('.w-nav-list');
					$list[hideFn]();
				}.bind(this));
		}
		// Close menu on anchor clicks
		this.$anchors.on('click', function(e){
			if (this.type != 'mobile' || $us.header.orientation != 'hor') return;
			// Toggled the item
			if (this.options.mobileBehavior && $(e.currentTarget).closest('.w-nav-item').hasClass('menu-item-has-children')) return;
			this.$list.slideUpCSS();
			this.mobileOpened = false;
		}.bind(this));

		$us.$window.on('resize', this._events.resize);
		setTimeout(function(){
			this.resize();
			$us.header.$container.trigger('contentChange');
		}.bind(this), 50);
	}

	USNav.prototype = {

		/**
		 * Count proper dimensions
		 */
		setFixedMobileMaxHeight: function(){
			var listTop = Math.min(this.$list.position().top, $us.header.scrolledOccupiedHeight);
			this.$list.css('max-height', $us.canvas.winHeight - listTop + 'px');
		},

		/**
		 * Resize handler
		 */
		resize: function(){
			if (this.$nav.length == 0) return;
			var nextType = (window.innerWidth <= this.options.mobileWidth) ? 'mobile' : 'desktop';
			if ($us.header.orientation != this.headerOrientation || nextType != this.type) {
				// Clearing the previous state
				this.$subLists.resetInlineCSS('display', 'opacity', 'height', 'padding-top', 'padding-bottom', 'margin-top');
				if (this.headerOrientation == 'hor' && this.type == 'mobile') {
					this.$list.resetInlineCSS('height', 'max-height', 'display', 'opacity', 'padding-top', 'padding-bottom');
				}
				// Closing opened sublists
				this.$items.removeClass('opened');
				// Applying the new state
				if ($us.header.orientation == 'hor') {
					if (nextType == 'desktop') {
						this.$items.filter('.togglable').children('.w-nav-list').css('display', 'none');
					} else if (nextType == 'mobile') {
						this.mobileOpened = false;
						this.$list.css('height', 0);
						this.$subLists.css('height', 0);
					}
				}
				this.headerOrientation = $us.header.orientation;
				this.type = nextType;
				this.$nav.usMod('type', nextType);
			}
			// Max-height limitation for fixed header layouts
			if ($us.header.orientation == 'hor' && this.type == 'mobile' && $us.canvas.headerPos == 'fixed') this.setFixedMobileMaxHeight();
			this.$list.removeClass('hidden');
		}
	};

	$us.nav = new USNav();

}(jQuery);


/**
 * $us.scroll
 *
 * ScrollSpy, Smooth scroll links and hash-based scrolling all-in-one
 *
 * @requires $us.canvas
 */
!function($){
	"use strict";

	function USScroll(options){

		// Setting options
		var defaults = {
			/**
			 * @param {String|jQuery} Selector or object of hash scroll anchors that should be attached on init
			 */
			attachOnInit: '.w-nav a[href*="#"], .w-menu a[href*="#"], a.w-btn[href*="#"], .w-iconbox a[href*="#"], .w-image a[href*="#"], .w-img a[href*="#"], .w-text a[href*="#"], ' +
			'.vc_icon_element a[href*="#"], .vc_custom_heading a[href*="#"], a.w-portfolio-item-anchor[href*="#"], .widget_nav_menu a[href*="#"], .w-toplink, ' +
			'.w-blog-post-meta-comments a[href*="#"], .w-comments-title a[href*="#"], .w-comments-item-date, a.smooth-scroll[href*="#"]',
			/**
			 * @param {String} Classname that will be toggled on relevant buttons
			 */
			buttonActiveClass: 'active',
			/**
			 * @param {String} Classname that will be toggled on relevant menu items
			 */
			menuItemActiveClass: 'current-menu-item',
			/**
			 * @param {String} Classname that will be toggled on relevant menu ancestors
			 */
			menuItemAncestorActiveClass: 'current-menu-ancestor',
			/**
			 * @param {Number} Duration of scroll animation
			 */
			animationDuration: 1200,
			/**
			 * @param {String} Easing for scroll animation
			 */
			animationEasing: 'easeInOutQuint'
		};
		this.options = $.extend({}, defaults, options || {});

		// Hash blocks with targets and activity indicators
		this.blocks = {};

		// Is scrolling to some specific block at the moment?
		this.isScrolling = false;

		// Waypoints that will be called at certain scroll position
		this.waypoints = [];

		// Boundable events
		this._events = {
			cancel: this.cancel.bind(this),
			scroll: this.scroll.bind(this),
			resize: this.resize.bind(this)
		};

		this._canvasTopOffset = 0;
		$us.$window.on('resize load', this._events.resize);
		setTimeout(this._events.resize, 75);

		$us.$window.on('scroll', this._events.scroll);
		setTimeout(this._events.scroll, 75);

		if (this.options.attachOnInit) {
			this.attach(this.options.attachOnInit);
		}

		// Recount scroll positions on any content changes
		$us.$canvas.on('contentChange', this._countAllPositions.bind(this));

		// Handling initial document hash
		if (document.location.hash && document.location.hash.indexOf('#!') == -1) {
			var hash = document.location.hash,
				scrollPlace = (this.blocks[hash] !== undefined) ? hash : undefined;
			if (scrollPlace === undefined) {
				try {
					var $target = $(hash);
					if ($target.length != 0) {
						scrollPlace = $target;
					}
				} catch (error) {
					//Do not have to do anything here since scrollPlace is already undefined
				}

			}
			if (scrollPlace !== undefined) {
				// While page loads, its content changes, and we'll keep the proper scroll on each sufficient content change
				// until the page finishes loading or user scrolls the page manually
				var keepScrollPositionTimer = setInterval(function(){
					this.scrollTo(scrollPlace);
				}.bind(this), 100);
				var clearHashEvents = function(){
					// Content size still may change via other script right after page load
					setTimeout(function(){
						clearInterval(keepScrollPositionTimer);
						$us.canvas.resize();
						this._countAllPositions();
						this.scrollTo(scrollPlace);
					}.bind(this), 100);
					$us.$window.off('load touchstart mousewheel DOMMouseScroll touchstart', clearHashEvents);
				}.bind(this);
				$us.$window.on('load touchstart mousewheel DOMMouseScroll touchstart', clearHashEvents);
			}
		}
	}

	USScroll.prototype = {

		/**
		 * Count hash's target position and store it properly
		 *
		 * @param {String} hash
		 * @private
		 */
		_countPosition: function(hash){
			this.blocks[hash].top = Math.ceil(this.blocks[hash].target.offset().top - this._canvasTopOffset);
			if ($us.header.headerTop === undefined || ($us.header.headerTop > 0 && this.blocks[hash].target.offset().top > $us.header.headerTop)) {
				this.blocks[hash].top = this.blocks[hash].top - $us.header.scrolledOccupiedHeight;
			}
			this.blocks[hash].bottom = this.blocks[hash].top + this.blocks[hash].target.outerHeight(false);
		},

		/**
		 * Count all targets' positions for proper scrolling
		 *
		 * @private
		 */
		_countAllPositions: function(){
			// Take into account #wpadminbar (and others possible) offset
			this._canvasTopOffset = $us.$canvas.offset().top;
			for (var hash in this.blocks) {
				if (!this.blocks.hasOwnProperty(hash)) continue;
				this._countPosition(hash);
			}
			// Counting waypoints
			for (var i = 0; i < this.waypoints.length; i++) {
				this._countWaypoint(this.waypoints[i]);
			}
		},

		/**
		 * Indicate scroll position by hash
		 *
		 * @param {String} activeHash
		 * @private
		 */
		_indicatePosition: function(activeHash){
			var activeMenuAncestors = [];
			for (var hash in this.blocks) {
				if (!this.blocks.hasOwnProperty(hash)) continue;
				if (this.blocks[hash].buttons !== undefined) {
					this.blocks[hash].buttons.toggleClass(this.options.buttonActiveClass, hash === activeHash);
				}
				if (this.blocks[hash].menuItems !== undefined) {
					this.blocks[hash].menuItems.toggleClass(this.options.menuItemActiveClass, hash === activeHash);
				}
				if (this.blocks[hash].menuAncestors !== undefined) {
					this.blocks[hash].menuAncestors.removeClass(this.options.menuItemAncestorActiveClass);
				}
			}
			if (this.blocks[activeHash] !== undefined && this.blocks[activeHash].menuAncestors !== undefined) {
				this.blocks[activeHash].menuAncestors.addClass(this.options.menuItemAncestorActiveClass);
			}
		},

		/**
		 * Attach anchors so their targets will be listened for possible scrolls
		 *
		 * @param {String|jQuery} anchors Selector or list of anchors to attach
		 */
		attach: function(anchors){
			// Location pattern to check absolute URLs for current location
			var locationPattern = new RegExp('^' + location.pathname.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&") + '#');

			var $anchors = $(anchors);
			if ($anchors.length == 0) return;
			$anchors.each(function(index, anchor){
				var $anchor = $(anchor),
					href = $anchor.attr('href'),
					hash = $anchor.prop('hash');
				// Ignoring ajax links
				if (hash.indexOf('#!') != -1) return;
				// Checking if the hash is connected with the current page
				if (!(
						// Link type: #something
						href.charAt(0) == '#' ||
							// Link type: /#something
						(href.charAt(0) == '/' && locationPattern.test(href)) ||
							// Link type: http://example.com/some/path/#something
						href.indexOf(location.host + location.pathname + '#') > -1
					)) return;
				// Do we have an actual target, for which we'll need to count geometry?
				if (hash != '' && hash != '#') {
					// Attach target
					if (this.blocks[hash] === undefined) {
						var $target = $(hash);
						// Don't attach anchors that actually have no target
						if ($target.length == 0) return;
						// If it's the only row in a section, than use section instead
						if ($target.hasClass('g-cols') && $target.parent().children().length == 1) {
							$target = $target.closest('.l-section');
						}
						// If it's tabs or tour item, then use tabs container
						if ($target.hasClass('w-tabs-section')) {
							var $newTarget = $target.closest('.w-tabs');
							if ( ! $newTarget.hasClass('accordion')) {
								$target = $newTarget;
							}
						}
						this.blocks[hash] = {
							target: $target
						};
						this._countPosition(hash);
					}
					// Attach activity indicator
					if ($anchor.hasClass('w-nav-anchor')) {
						var $menuIndicator = $anchor.closest('.w-nav-item');
						this.blocks[hash].menuItems = (this.blocks[hash].menuItems || $()).add($menuIndicator);
						var $menuAncestors = $menuIndicator.parents('.menu-item-has-children');
						if ($menuAncestors.length > 0) {
							this.blocks[hash].menuAncestors = (this.blocks[hash].menuAncestors || $()).add($menuAncestors);
						}
					}
					else {
						this.blocks[hash].buttons = (this.blocks[hash].buttons || $()).add($anchor);
					}
				}
				$anchor.on('click', function(event){
					event.preventDefault();
					this.scrollTo(hash, true);
				}.bind(this));
			}.bind(this));
		},

		/**
		 * Scroll page to a certain position or hash
		 *
		 * @param {Number|String|jQuery} place
		 * @param {Boolean} animate
		 */
		scrollTo: function(place, animate){
			var placeType,
				newY;
			// Scroll to top
			if (place == '' || place == '#') {
				newY = 0;
				placeType = 'top';
			}
			// Scroll by hash
			else if (this.blocks[place] !== undefined) {
				newY = this.blocks[place].top;
				placeType = 'hash';
			}
			else if (place instanceof $) {
				if (place.hasClass('w-tabs-section')) {
					var newPlace = place.closest('.w-tabs');
					if ( ! newPlace.hasClass('accordion')) {
						place = newPlace;
					}
				}
				newY = Math.floor(place.offset().top - this._canvasTopOffset);
				if ($us.header.headerTop === undefined || ($us.header.headerTop > 0 && place.offset().top > $us.header.headerTop)) {
					newY = newY - $us.header.scrolledOccupiedHeight;
				}
				placeType = 'element';
			}
			else {
				newY = Math.floor(place - this._canvasTopOffset);
				if ($us.header.headerTop === undefined || ($us.header.headerTop > 0 && place > $us.header.headerTop)) {
					newY = newY - $us.header.scrolledOccupiedHeight;
				}
			}
			var indicateActive = function(){
				if (placeType == 'hash') {
					this._indicatePosition(place);
				}
				else {
					this.scroll();
				}
			}.bind(this);
			if (animate) {
				this.isScrolling = true;
				$us.$htmlBody.stop(true, false).animate({
					scrollTop: newY + 'px'
				}, {
					duration: this.options.animationDuration,
					easing: this.options.animationEasing,
					always: function(){
						$us.$window.off('keydown mousewheel DOMMouseScroll touchstart', this._events.cancel);
						this.isScrolling = false;
						indicateActive();
					}.bind(this)
				});
				// Allow user to stop scrolling manually
				$us.$window.on('keydown mousewheel DOMMouseScroll touchstart', this._events.cancel);
			}
			else {
				$us.$htmlBody.stop(true, false).scrollTop(newY);
				indicateActive();
			}
		},

		/**
		 * Cancel scroll
		 */
		cancel: function(){
			$us.$htmlBody.stop(true, false);
		},

		/**
		 * Add new waypoint
		 *
		 * @param {jQuery} $elm object with the element
		 * @param {mixed} offset Offset from bottom of screen in pixels ('100') or percents ('20%')
		 * @param {Function} fn The function that will be called
		 */
		addWaypoint: function($elm, offset, fn){
			$elm = ($elm instanceof $) ? $elm : $($elm);
			if ($elm.length == 0) return;
			if (typeof offset != 'string' || offset.indexOf('%') == -1) {
				// Not percent: using pixels
				offset = parseInt(offset);
			}
			var waypoint = {
				$elm: $elm,
				offset: offset,
				fn: fn
			};
			this._countWaypoint(waypoint);
			this.waypoints.push(waypoint);
		},

		/**
		 *
		 * @param {Object} waypoint
		 * @private
		 */
		_countWaypoint: function(waypoint){
			var elmTop = waypoint.$elm.offset().top,
				winHeight = $us.$window.height();
			if (typeof waypoint.offset == 'number') {
				// Offset is defined in pixels
				waypoint.scrollPos = elmTop - winHeight + waypoint.offset;
			} else {
				// Offset is defined in percents
				waypoint.scrollPos = elmTop - winHeight + winHeight * parseInt(waypoint.offset) / 100;
			}
		},

		/**
		 * Scroll handler
		 */
		scroll: function(){
			var scrollTop = parseInt($us.$window.scrollTop());
			if (!this.isScrolling) {
				var activeHash;
				for (var hash in this.blocks) {
					if (!this.blocks.hasOwnProperty(hash)) continue;
					if (scrollTop >= this.blocks[hash].top && scrollTop < this.blocks[hash].bottom) {
						activeHash = hash;
						break;
					}
				}
				this._indicatePosition(activeHash);
			}
			// Handling waypoints
			for (var i = 0; i < this.waypoints.length; i++) {
				if (this.waypoints[i].scrollPos < scrollTop) {
					this.waypoints[i].fn(this.waypoints[i].$elm);
					this.waypoints.splice(i, 1);
					i--;
				}
			}
		},

		/**
		 * Resize handler
		 */
		resize: function(){
			// Delaying the resize event to prevent glitches
			setTimeout(function(){
				this._countAllPositions();
				this.scroll();
			}.bind(this), 150);
			this._countAllPositions();
			this.scroll();
		}
	};

	$(function(){
		$us.scroll = new USScroll($us.scrollOptions || {});
	});

}(jQuery);


jQuery(function($){
	"use strict";

	// TODO Move all of the below to us.widgets
	if ($.fn.magnificPopup) {

		$('.product .images').magnificPopup({
			type: 'image',
			delegate: 'a.with-lightbox',
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

		$('a[ref=magnificPopup][class!=direct-link]').magnificPopup({
			type: 'image',
			fixedContentPos: false
		});
	}

	if ($.fn.revolution) {
		$('.fullwidthbanner').revolution({
			delay: 9000,
			startwidth: 1140,
			startheight: 500,
			soloArrowLeftHOffset: 20,
			soloArrowLeftVOffset: 0,
			soloArrowRightHOffset: 20,
			soloArrowRightVOffset: 0,
			onHoverStop: "on", // Stop Banner Timet at Hover on Slide on/off
			fullWidth: "on",
			hideThumbs: false,
			shadow: 0 //0 = no Shadow, 1,2,3 = 3 Different Art of Shadows -  (No Shadow in Fullwidth Version !)
		});
		// Redrawing all the Revolution Sliders
		if (window.revapi3 !== undefined && window.revapi3.revredraw !== undefined) {
			$us.$window.on('resize', function(){
				window.revapi3.revredraw();
			});
		}
	}

	$('.animate_fade, .animate_afc, .animate_afl, .animate_afr, .animate_aft, .animate_afb, .animate_wfc, ' +
		'.animate_hfc, .animate_rfc, .animate_rfl, .animate_rfr').each(function(){
		$us.scroll.addWaypoint($(this), '15%', function($elm){
			if (!$elm.hasClass('animate_start')) {
				setTimeout(function(){
					$elm.addClass('animate_start');
				}, 20);
			}
		});
	});
	$('.wpb_animate_when_almost_visible').each(function(){
		$us.scroll.addWaypoint($(this), '15%', function($elm){
			if (!$elm.hasClass('wpb_start_animation')) {
				setTimeout(function(){
					$elm.addClass('wpb_start_animation');
				}, 20);
			}
		});
	});

	jQuery('input[type="text"], input[type="email"], textarea').each(function(index, input){
		var $input = $(input),
			$row = $input.closest('.w-form-row');
		if ($input.attr('type') == 'hidden') return;
		$row.toggleClass('not-empty', $input.val() != '');
		$input.on('input', function(){
			$row.toggleClass('not-empty', $input.val() != '');
		});
	});

	jQuery('.l-section-img, .l-titlebar-img').each(function(){
		var $this = $(this),
			img = new Image();

		img.onload = function(){
			if (!$this.hasClass('loaded')) {
				$this.addClass('loaded')
			}
		};

		img.src = ($this.css('background-image') || '').replace(/url\(['"]*(.*?)['"]*\)/g, '$1');
	});

	/* Ultimate Addons for Visual Composer integration */
	jQuery('.upb_bg_img, .upb_color, .upb_grad, .upb_content_iframe, .upb_content_video, .upb_no_bg').each(function(){
		var $bg = jQuery(this),
			$prev = $bg.prev();

		if ($prev.length == 0) {
			var $parent = $bg.parent(),
				$parentParent = $parent.parent(),
				$prevParentParent = $parentParent.prev();

			if ($prevParentParent.length) {
				$bg.insertAfter($prevParentParent);

				if ($parent.children().length == 0) {
					$parentParent.remove();
				}
			}
		}
	});
	$('.g-cols > .ult-item-wrap').each(function(index, elm){
		var $elm = jQuery(elm);
		$elm.replaceWith($elm.children());
	});
	jQuery('.overlay-show').click(function(){
		window.setTimeout(function(){
			$us.$canvas.trigger('contentChange');
		}, 1000);
	});

});

/**
 * CSS-analog of jQuery slideDown/slideUp/fadeIn/fadeOut functions (for better rendering)
 */
!function(){

	/**
	 * Remove the passed inline CSS attributes.
	 *
	 * Usage: $elm.resetInlineCSS('height', 'width');
	 */
	jQuery.fn.resetInlineCSS = function(){
		for (var index = 0; index < arguments.length; index++) {
			this.css(arguments[index], '');
		}
		return this;
	};

	jQuery.fn.clearPreviousTransitions = function(){
		// Stopping previous events, if there were any
		var prevTimers = (this.data('animation-timers') || '').split(',');
		if (prevTimers.length >= 2) {
			this.resetInlineCSS('transition', '-webkit-transition');
			prevTimers.map(clearTimeout);
			this.removeData('animation-timers');
		}
		return this;
	};
	/**
	 *
	 * @param {Object} css key-value pairs of animated css
	 * @param {Number} duration in milliseconds
	 * @param {Function} onFinish
	 * @param {String} easing CSS easing name
	 * @param {Number} delay in milliseconds
	 */
	jQuery.fn.performCSSTransition = function(css, duration, onFinish, easing, delay){
		duration = duration || 250;
		delay = delay || 25;
		easing = easing || 'ease-in-out';
		var $this = this,
			transition = [];

		this.clearPreviousTransitions();

		for (var attr in css) {
			if (!css.hasOwnProperty(attr)) continue;
			transition.push(attr + ' ' + (duration / 1000) + 's ' + easing);
		}
		transition = transition.join(', ');
		$this.css({
			transition: transition,
			'-webkit-transition': transition
		});

		// Starting the transition with a slight delay for the proper application of CSS transition properties
		var timer1 = setTimeout(function(){
			$this.css(css);
		}, delay);

		var timer2 = setTimeout(function(){
			$this.resetInlineCSS('transition', '-webkit-transition');
			if (typeof onFinish == 'function') onFinish();
		}, duration + delay);

		this.data('animation-timers', timer1 + ',' + timer2);
	};
	// Height animations
	jQuery.fn.slideDownCSS = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		var $this = this;
		this.clearPreviousTransitions();
		// Grabbing paddings
		this.resetInlineCSS('padding-top', 'padding-bottom');
		var timer1 = setTimeout(function(){
			var paddingTop = parseInt($this.css('padding-top')),
				paddingBottom = parseInt($this.css('padding-bottom'));
			// Grabbing the "auto" height in px
			$this.css({
				visibility: 'hidden',
				position: 'absolute',
				height: 'auto',
				'padding-top': 0,
				'padding-bottom': 0,
				display: 'block'
			});
			var height = $this.height();
			$this.css({
				overflow: 'hidden',
				height: '0px',
				visibility: '',
				position: '',
				opacity: 0
			});
			$this.performCSSTransition({
				height: height + paddingTop + paddingBottom,
				opacity: 1,
				'padding-top': paddingTop,
				'padding-bottom': paddingBottom
			}, duration, function(){
				$this.resetInlineCSS('overflow').css('height', 'auto');
				if (typeof onFinish == 'function') onFinish();
			}, easing, delay);
		}, 25);
		this.data('animation-timers', timer1 + ',null');
	};
	jQuery.fn.slideUpCSS = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		this.clearPreviousTransitions();
		this.css({
			height: this.outerHeight(),
			overflow: 'hidden',
			'padding-top': this.css('padding-top'),
			'padding-bottom': this.css('padding-bottom'),
			opacity: 1
		});
		var $this = this;
		this.performCSSTransition({
			height: 0,
			'padding-top': 0,
			'padding-bottom': 0,
			opacity: 0
		}, duration, function(){
			$this.resetInlineCSS('overflow', 'padding-top', 'padding-bottom', 'opacity').css({
				display: 'none'
			});
			if (typeof onFinish == 'function') onFinish();
		}, easing, delay);
	};
	// Opacity animations
	jQuery.fn.fadeInCSS = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		this.clearPreviousTransitions();
		this.css({
			opacity: 0,
			display: 'block'
		});
		this.performCSSTransition({
			opacity: 1
		}, duration, onFinish, easing, delay);
	};
	jQuery.fn.fadeOutCSS = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		var $this = this;
		this.performCSSTransition({
			opacity: 0
		}, duration, function(){
			$this.css('display', 'none');
			if (typeof onFinish == 'function') onFinish();
		}, easing, delay);
	};
	// Material design animations
	jQuery.fn.showMD = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		this.clearPreviousTransitions();
		// Grabbing paddings
		this.resetInlineCSS('padding-top', 'padding-bottom');
		var paddingTop = parseInt(this.css('padding-top')),
			paddingBottom = parseInt(this.css('padding-bottom'));
		// Grabbing the "auto" height in px
		this.css({
			visibility: 'hidden',
			position: 'absolute',
			height: 'auto',
			'padding-top': 0,
			'padding-bottom': 0,
			'margin-top': -20,
			opacity: '',
			display: 'block'
		});
		var height = this.height();
		this.css({
			overflow: 'hidden',
			height: '0px'
		}).resetInlineCSS('visibility', 'position');
		var $this = this;
		this.performCSSTransition({
			height: height + paddingTop + paddingBottom,
			'margin-top': 0,
			'padding-top': paddingTop,
			'padding-bottom': paddingBottom
		}, duration || 350, function(){
			$this.resetInlineCSS('overflow', 'margin-top', 'padding-top', 'padding-bottom').css('height', 'auto');
			if (typeof onFinish == 'function') onFinish();
		}, easing || 'cubic-bezier(.23,1,.32,1)', delay || 150);
	};
	jQuery.fn.hideMD = function(duration, onFinish, easing, delay){
		if (this.length == 0) return;
		this.clearPreviousTransitions();
		var $this = this;
		this.resetInlineCSS('margin-top');
		this.performCSSTransition({
			opacity: 0
		}, duration || 100, function(){
			$this.css({
				display: 'none'
			}).resetInlineCSS('opacity');
			if (typeof onFinish == 'function') onFinish();
		}, easing, delay);
	};
	// Slide element left / right
	var slideIn = function($this, from){
			if ($this.length == 0) return;
			$this.clearPreviousTransitions();
			$this.css({width: 'auto', height: 'auto'});
			var width = $this.width(),
				height = $this.height();
			$this.css({
				width: width,
				height: height,
				position: 'relative',
				left: (from == 'right') ? '100%' : '-100%',
				opacity: 0,
				display: 'block'
			});
			$this.performCSSTransition({
				left: '0%',
				opacity: 1
			}, arguments[0] || 250, function(){
				$this.resetInlineCSS('position', 'left', 'opacity', 'display').css({width: 'auto', height: 'auto'});
			});
		},
		slideOut = function($this, to){
			if ($this.length == 0) return;
			$this.clearPreviousTransitions();
			$this.css({
				position: 'relative',
				left: 0,
				opacity: 1
			});
			$this.performCSSTransition({
				left: (to == 'left') ? '-100%' : '100%',
				opacity: 0
			}, arguments[0] || 250, function(){
				$this.css({
					display: 'none'
				}).resetInlineCSS('position', 'left', 'opacity');
			});
		};
	jQuery.fn.slideOutLeft = function(){
		slideOut(this, 'left');
	};
	jQuery.fn.slideOutRight = function(){
		slideOut(this, 'right');
	};
	jQuery.fn.slideInLeft = function(){
		slideIn(this, 'left');
	};
	jQuery.fn.slideInRight = function(){
		slideIn(this, 'right');
	};
}();
