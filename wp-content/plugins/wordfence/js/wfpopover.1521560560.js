/* ========================================================================
 * Bootstrap: wftooltip.js v3.3.7 and wfpopover.js v3.3.7 (adapted to wf prefix)
 * http://getbootstrap.com/javascript/#wfpopovers
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
	'use strict';

	// TOOLTIP PUBLIC CLASS DEFINITION
	// ===============================

	var WFTooltip = function (element, options) {
		this.type       = null
		this.options    = null
		this.enabled    = null
		this.timeout    = null
		this.hoverState = null
		this.$element   = null
		this.inState    = null

		this.init('wftooltip', element, options)
	}

	WFTooltip.VERSION  = '3.3.7'

	WFTooltip.TRANSITION_DURATION = 150

	WFTooltip.DEFAULTS = {
		animation: true,
		placement: 'top',
		selector: false,
		template: '<div class="wftooltip" role="wftooltip"><div class="wftooltip-arrow"></div><div class="wftooltip-inner"></div></div>',
		trigger: 'hover focus',
		title: '',
		delay: 0,
		html: false,
		container: false,
		viewport: {
			selector: 'body',
			padding: 0
		}
	}

	WFTooltip.prototype.init = function (type, element, options) {
		this.enabled   = true
		this.type      = type
		this.$element  = $(element)
		this.options   = this.getOptions(options)
		this.$viewport = this.options.viewport && $($.isFunction(this.options.viewport) ? this.options.viewport.call(this, this.$element) : (this.options.viewport.selector || this.options.viewport))
		this.inState   = { click: false, hover: false, focus: false }

		if (this.$element[0] instanceof document.constructor && !this.options.selector) {
			throw new Error('`selector` option must be specified when initializing ' + this.type + ' on the window.document object!')
		}

		var triggers = this.options.trigger.split(' ')

		for (var i = triggers.length; i--;) {
			var trigger = triggers[i]

			if (trigger == 'click') {
				this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
			} else if (trigger != 'manual') {
				var eventIn  = trigger == 'hover' ? 'mouseenter' : 'focusin'
				var eventOut = trigger == 'hover' ? 'mouseleave' : 'focusout'

				this.$element.on(eventIn  + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
				this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
			}
		}

		this.options.selector ?
			(this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
			this.fixTitle()
	}

	WFTooltip.prototype.getDefaults = function () {
		return WFTooltip.DEFAULTS
	}

	WFTooltip.prototype.getOptions = function (options) {
		options = $.extend({}, this.getDefaults(), this.$element.data(), options)

		if (options.delay && typeof options.delay == 'number') {
			options.delay = {
				show: options.delay,
				hide: options.delay
			}
		}

		return options
	}

	WFTooltip.prototype.getDelegateOptions = function () {
		var options  = {}
		var defaults = this.getDefaults()

		this._options && $.each(this._options, function (key, value) {
			if (defaults[key] != value) options[key] = value
		})

		return options
	}

	WFTooltip.prototype.enter = function (obj) {
		var self = obj instanceof this.constructor ?
			obj : $(obj.currentTarget).data('bs.' + this.type)

		if (!self) {
			self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
			$(obj.currentTarget).data('bs.' + this.type, self)
		}

		if (obj instanceof $.Event) {
			self.inState[obj.type == 'focusin' ? 'focus' : 'hover'] = true
		}

		if (self.tip().hasClass('wf-in') || self.hoverState == 'wf-in') {
			self.hoverState = 'in'
			return
		}

		clearTimeout(self.timeout)

		self.hoverState = 'wf-in'

		if (!self.options.delay || !self.options.delay.show) return self.show()

		self.timeout = setTimeout(function () {
			if (self.hoverState == 'wf-in') self.show()
		}, self.options.delay.show)
	}

	WFTooltip.prototype.isInStateTrue = function () {
		for (var key in this.inState) {
			if (this.inState[key]) return true
		}

		return false
	}

	WFTooltip.prototype.leave = function (obj) {
		var self = obj instanceof this.constructor ?
			obj : $(obj.currentTarget).data('bs.' + this.type)

		if (!self) {
			self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
			$(obj.currentTarget).data('bs.' + this.type, self)
		}

		if (obj instanceof $.Event) {
			self.inState[obj.type == 'focusout' ? 'focus' : 'hover'] = false
		}

		if (self.isInStateTrue()) return

		clearTimeout(self.timeout)

		self.hoverState = 'wf-out'

		if (!self.options.delay || !self.options.delay.hide) return self.hide()

		self.timeout = setTimeout(function () {
			if (self.hoverState == 'wf-out') self.hide()
		}, self.options.delay.hide)
	}

	WFTooltip.prototype.show = function () {
		var e = $.Event('show.bs.' + this.type)

		if (this.hasContent() && this.enabled) {
			this.$element.trigger(e)

			var inDom = $.contains(this.$element[0].ownerDocument.documentElement, this.$element[0])
			if (e.isDefaultPrevented() || !inDom) return
			var that = this

			var $tip = this.tip()

			var tipId = this.getUID(this.type)

			this.setContent()
			$tip.attr('id', tipId)
			this.$element.attr('aria-describedby', tipId)

			if (this.options.animation) $tip.addClass('wf-fade')

			var placement = typeof this.options.placement == 'function' ?
				this.options.placement.call(this, $tip[0], this.$element[0]) :
				this.options.placement

			var autoToken = /\s?auto?\s?/i
			var autoPlace = autoToken.test(placement)
			if (autoPlace) placement = placement.replace(autoToken, '') || 'wf-top'

			$tip
				.detach()
				.css({ top: 0, left: 0, display: 'block' })
				.addClass(placement)
				.data('bs.' + this.type, this)

			this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)
			this.$element.trigger('inserted.bs.' + this.type)

			var pos          = this.getPosition()
			var actualWidth  = $tip[0].offsetWidth
			var actualHeight = $tip[0].offsetHeight

			if (autoPlace) {
				var orgPlacement = placement
				var viewportDim = this.getPosition(this.$viewport)

				placement = placement == 'wf-bottom' && pos.bottom + actualHeight > viewportDim.bottom ? 'wf-top'    :
							placement == 'wf-top'    && pos.top    - actualHeight < viewportDim.top    ? 'wf-bottom' :
							placement == 'wf-right'  && pos.right  + actualWidth  > viewportDim.width  ? 'wf-left'   :
							placement == 'wf-left'   && pos.left   - actualWidth  < viewportDim.left   ? 'wf-right'  :
							placement

				$tip
					.removeClass(orgPlacement)
					.addClass(placement)
			}

			var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

			this.applyPlacement(calculatedOffset, placement)

			var complete = function () {
				var prevHoverState = that.hoverState
				that.$element.trigger('shown.bs.' + that.type)
				that.hoverState = null

				if (prevHoverState == 'wf-out') that.leave(that)
			}

			$.support.transition && this.$tip.hasClass('wf-fade') ?
				$tip
					.one('bsTransitionEnd', complete)
					.emulateTransitionEnd(WFTooltip.TRANSITION_DURATION) :
				complete()
		}
	}

	WFTooltip.prototype.applyPlacement = function (offset, placement) {
		var $tip   = this.tip()
		var width  = $tip[0].offsetWidth
		var height = $tip[0].offsetHeight

		// manually read margins because getBoundingClientRect includes difference
		var marginTop = parseInt($tip.css('margin-top'), 10)
		var marginLeft = parseInt($tip.css('margin-left'), 10)

		// we must check for NaN for ie 8/9
		if (isNaN(marginTop))  marginTop  = 0
		if (isNaN(marginLeft)) marginLeft = 0

		offset.top  += marginTop
		offset.left += marginLeft

		// $.fn.offset doesn't round pixel values
		// so we use setOffset directly with our own function B-0
		$.offset.setOffset($tip[0], $.extend({
			using: function (props) {
				$tip.css({
					top: Math.round(props.top),
					left: Math.round(props.left)
				})
			}
		}, offset), 0)

		$tip.addClass('wf-in')

		// check to see if placing tip in new offset caused the tip to resize itself
		var actualWidth  = $tip[0].offsetWidth
		var actualHeight = $tip[0].offsetHeight

		if (placement == 'wf-top' && actualHeight != height) {
			offset.top = offset.top + height - actualHeight
		}

		var delta = this.getViewportAdjustedDelta(placement, offset, actualWidth, actualHeight)

		if (delta.left) offset.left += delta.left
		else offset.top += delta.top

		var isVertical          = /top|bottom/.test(placement)
		var arrowDelta          = isVertical ? delta.left * 2 - width + actualWidth : delta.top * 2 - height + actualHeight
		var arrowOffsetPosition = isVertical ? 'offsetWidth' : 'offsetHeight'

		$tip.offset(offset)
		this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], isVertical)
	}

	WFTooltip.prototype.replaceArrow = function (delta, dimension, isVertical) {
		this.arrow()
			.css(isVertical ? 'left' : 'top', 50 * (1 - delta / dimension) + '%')
			.css(isVertical ? 'top' : 'left', '')
	}

	WFTooltip.prototype.setContent = function () {
		var $tip  = this.tip()
		var title = this.getTitle()

		$tip.find('.wftooltip-inner')[this.options.html ? 'html' : 'text'](title)
		$tip.removeClass('wf-fade wf-in wf-top wf-bottom wf-left wf-right')
	}

	WFTooltip.prototype.hide = function (callback) {
		var that = this
		var $tip = $(this.$tip)
		var e    = $.Event('hide.bs.' + this.type)

		function complete() {
			if (that.hoverState != 'in') $tip.detach()
			if (that.$element) { // TODO: Check whether guarding this code with this `if` is really necessary.
				that.$element
					.removeAttr('aria-describedby')
					.trigger('hidden.bs.' + that.type)
			}
			callback && callback()
		}

		this.$element.trigger(e)

		if (e.isDefaultPrevented()) return

		$tip.removeClass('in')

		$.support.transition && $tip.hasClass('wf-fade') ?
			$tip
				.one('bsTransitionEnd', complete)
				.emulateTransitionEnd(WFTooltip.TRANSITION_DURATION) :
			complete()

		this.hoverState = null

		return this
	}

	WFTooltip.prototype.fixTitle = function () {
		var $e = this.$element
		if ($e.attr('title') || typeof $e.attr('data-original-title') != 'string') {
			$e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
		}
	}

	WFTooltip.prototype.hasContent = function () {
		return this.getTitle()
	}

	WFTooltip.prototype.getPosition = function ($element) {
		$element   = $element || this.$element

		var el     = $element[0]
		var isBody = el.tagName == 'BODY'

		var elRect    = el.getBoundingClientRect()
		if (elRect.width == null) {
			// width and height are missing in IE8, so compute them manually; see https://github.com/twbs/bootstrap/issues/14093
			elRect = $.extend({}, elRect, { width: elRect.right - elRect.left, height: elRect.bottom - elRect.top })
		}
		var isSvg = window.SVGElement && el instanceof window.SVGElement
		// Avoid using $.offset() on SVGs since it gives incorrect results in jQuery 3.
		// See https://github.com/twbs/bootstrap/issues/20280
		var elOffset  = isBody ? { top: 0, left: 0 } : (isSvg ? null : $element.offset())
		var scroll    = { scroll: isBody ? document.documentElement.scrollTop || document.body.scrollTop : $element.scrollTop() }
		var outerDims = isBody ? { width: $(window).width(), height: $(window).height() } : null

		return $.extend({}, elRect, scroll, outerDims, elOffset)
	}

	WFTooltip.prototype.getCalculatedOffset = function (placement, pos, actualWidth, actualHeight) {
		return placement == 'wf-bottom' ? { top: pos.top + pos.height,   left: pos.left + pos.width / 2 - actualWidth / 2 } :
				placement == 'wf-top'    ? { top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2 } :
				placement == 'wf-left'   ? { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth } :
					/* placement == 'wf-right' */ { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width }

	}

	WFTooltip.prototype.getViewportAdjustedDelta = function (placement, pos, actualWidth, actualHeight) {
		var delta = { top: 0, left: 0 }
		if (!this.$viewport) return delta

		var viewportPadding = this.options.viewport && this.options.viewport.padding || 0
		var viewportDimensions = this.getPosition(this.$viewport)

		if (/right|left/.test(placement)) {
			var topEdgeOffset    = pos.top - viewportPadding - viewportDimensions.scroll
			var bottomEdgeOffset = pos.top + viewportPadding - viewportDimensions.scroll + actualHeight
			if (topEdgeOffset < viewportDimensions.top) { // top overflow
				delta.top = viewportDimensions.top - topEdgeOffset
			} else if (bottomEdgeOffset > viewportDimensions.top + viewportDimensions.height) { // bottom overflow
				delta.top = viewportDimensions.top + viewportDimensions.height - bottomEdgeOffset
			}
		} else {
			var leftEdgeOffset  = pos.left - viewportPadding
			var rightEdgeOffset = pos.left + viewportPadding + actualWidth
			if (leftEdgeOffset < viewportDimensions.left) { // left overflow
				delta.left = viewportDimensions.left - leftEdgeOffset
			} else if (rightEdgeOffset > viewportDimensions.right) { // right overflow
				delta.left = viewportDimensions.left + viewportDimensions.width - rightEdgeOffset
			}
		}

		return delta
	}

	WFTooltip.prototype.getTitle = function () {
		var title
		var $e = this.$element
		var o  = this.options

		title = $e.attr('data-original-title')
			|| (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

		return title
	}

	WFTooltip.prototype.getUID = function (prefix) {
		do prefix += ~~(Math.random() * 1000000)
		while (document.getElementById(prefix))
		return prefix
	}

	WFTooltip.prototype.tip = function () {
		if (!this.$tip) {
			this.$tip = $(this.options.template)
			if (this.$tip.length != 1) {
				throw new Error(this.type + ' `template` option must consist of exactly 1 top-level element!')
			}
		}
		return this.$tip
	}

	WFTooltip.prototype.arrow = function () {
		return (this.$arrow = this.$arrow || this.tip().find('.wftooltip-arrow'))
	}

	WFTooltip.prototype.enable = function () {
		this.enabled = true
	}

	WFTooltip.prototype.disable = function () {
		this.enabled = false
	}

	WFTooltip.prototype.toggleEnabled = function () {
		this.enabled = !this.enabled
	}

	WFTooltip.prototype.toggle = function (e) {
		var self = this
		if (e) {
			self = $(e.currentTarget).data('bs.' + this.type)
			if (!self) {
				self = new this.constructor(e.currentTarget, this.getDelegateOptions())
				$(e.currentTarget).data('bs.' + this.type, self)
			}
		}

		if (e) {
			self.inState.click = !self.inState.click
			if (self.isInStateTrue()) self.enter(self)
			else self.leave(self)
		} else {
			self.tip().hasClass('wf-in') ? self.leave(self) : self.enter(self)
		}
	}

	WFTooltip.prototype.destroy = function () {
		var that = this
		clearTimeout(this.timeout)
		this.hide(function () {
			that.$element.off('.' + that.type).removeData('bs.' + that.type)
			if (that.$tip) {
				that.$tip.detach()
			}
			that.$tip = null
			that.$arrow = null
			that.$viewport = null
			that.$element = null
		})
	}


	// TOOLTIP PLUGIN DEFINITION
	// =========================

	function Plugin(option) {
		return this.each(function () {
			var $this   = $(this)
			var data    = $this.data('bs.wftooltip')
			var options = typeof option == 'object' && option

			if (!data && /destroy|hide/.test(option)) return
			if (!data) $this.data('bs.wftooltip', (data = new WFTooltip(this, options)))
			if (typeof option == 'string') data[option]()
		})
	}

	var old = $.fn.wftooltip

	$.fn.wftooltip             = Plugin
	$.fn.wftooltip.Constructor = WFTooltip


	// TOOLTIP NO CONFLICT
	// ===================

	$.fn.wftooltip.noConflict = function () {
		$.fn.wftooltip = old
		return this
	}

	// POPOVER PUBLIC CLASS DEFINITION
	// ===============================

	var WFPopover = function (element, options) {
		this.init('wfpopover', element, options)
	}

	WFPopover.VERSION  = '3.3.7'

	WFPopover.DEFAULTS = $.extend({}, $.fn.wftooltip.Constructor.DEFAULTS, {
		placement: 'wf-right',
		trigger: 'click',
		content: '',
		template: '<div class="wfpopover" role="wftooltip"><div class="wf-arrow"></div><h3 class="wfpopover-title"></h3><div class="wfpopover-content"></div></div>'
	})


	// NOTE: POPOVER EXTENDS wftooltip.js
	// ================================

	WFPopover.prototype = $.extend({}, $.fn.wftooltip.Constructor.prototype)

	WFPopover.prototype.constructor = WFPopover

	WFPopover.prototype.getDefaults = function () {
		return WFPopover.DEFAULTS
	}

	WFPopover.prototype.setContent = function () {
		var $tip    = this.tip()
		var title   = this.getTitle()
		var content = this.getContent()

		$tip.find('.wfpopover-title')[this.options.html ? 'html' : 'text'](title)
		$tip.find('.wfpopover-content').children().detach().end()[ // we use append for html objects to maintain js events
			this.options.html ? (typeof content == 'string' ? 'html' : 'append') : 'text'
			](content)

		$tip.removeClass('wf-fade wf-top wf-bottom wf-left wf-right wf-in')

		// IE8 doesn't accept hiding via the `:empty` pseudo selector, we have to do
		// this manually by checking the contents.
		if (!$tip.find('.wfpopover-title').html()) $tip.find('.wfpopover-title').hide()
	}

	WFPopover.prototype.hasContent = function () {
		return this.getTitle() || this.getContent()
	}

	WFPopover.prototype.getContent = function () {
		var $e = this.$element
		var o  = this.options

		return $e.attr('data-content')
			|| (typeof o.content == 'function' ?
				o.content.call($e[0]) :
				o.content)
	}

	WFPopover.prototype.arrow = function () {
		return (this.$arrow = this.$arrow || this.tip().find('.wf-arrow'))
	}


	// POPOVER PLUGIN DEFINITION
	// =========================

	function Plugin(option) {
		return this.each(function () {
			var $this   = $(this)
			var data    = $this.data('bs.wfpopover')
			var options = typeof option == 'object' && option

			if (!data && /destroy|hide/.test(option)) return
			if (!data) $this.data('bs.wfpopover', (data = new WFPopover(this, options)))
			if (typeof option == 'string') data[option]()
		})
	}

	var old = $.fn.wfpopover

	$.fn.wfpopover             = Plugin
	$.fn.wfpopover.Constructor = WFPopover


	// POPOVER NO CONFLICT
	// ===================

	$.fn.wfpopover.noConflict = function () {
		$.fn.wfpopover = old
		return this
	}

}(jQuery);
