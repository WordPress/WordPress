/* ========================================================================
 * Bootstrap: dropdown.js v3.3.7 (adapted to WF prefix)
 * http://getbootstrap.com/javascript/#dropdowns
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
	'use strict';

	// DROPDOWN CLASS DEFINITION
	// =========================

	var backdrop = '.wf-dropdown-backdrop'
	var toggle   = '[data-toggle="wf-dropdown"]'
	var WFDropdown = function (element) {
		$(element).on('click.bs.wf-dropdown', this.toggle)
	}

	WFDropdown.VERSION = '3.3.7'

	function getParent($this) {
		var selector = $this.attr('data-target')

		if (!selector) {
			selector = $this.attr('href')
			selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
		}

		var $parent = selector && $(selector)

		return $parent && $parent.length ? $parent : $this.parent()
	}

	function clearMenus(e) {
		if (e && e.which === 3) return
		$(backdrop).remove()
		$(toggle).each(function () {
			var $this         = $(this)
			var $parent       = getParent($this)
			var relatedTarget = { relatedTarget: this }

			if (!$parent.hasClass('wf-open')) return

			if (e && e.type == 'click' && /input|textarea/i.test(e.target.tagName) && $.contains($parent[0], e.target)) return

			$parent.trigger(e = $.Event('hide.bs.wf-dropdown', relatedTarget))

			if (e.isDefaultPrevented()) return

			$this.attr('aria-expanded', 'false')
			$parent.removeClass('wf-open').trigger($.Event('hidden.bs.wf-dropdown', relatedTarget))
		})
	}

	WFDropdown.prototype.toggle = function (e) {
		var $this = $(this)

		if ($this.is('.wf-disabled, :disabled')) return

		var $parent  = getParent($this)
		var isActive = $parent.hasClass('wf-open')

		clearMenus()

		if (!isActive) {
			if ('ontouchstart' in document.documentElement && !$parent.closest('.wf-navbar-nav').length) {
				// if mobile we use a backdrop because click events don't delegate
				$(document.createElement('div'))
					.addClass('wf-dropdown-backdrop')
					.insertAfter($(this))
					.on('click', clearMenus)
			}

			var relatedTarget = { relatedTarget: this }
			$parent.trigger(e = $.Event('show.bs.wf-dropdown', relatedTarget))

			if (e.isDefaultPrevented()) return

			$this
				.trigger('focus')
				.attr('aria-expanded', 'true')

			$parent
				.toggleClass('wf-open')
				.trigger($.Event('shown.bs.wf-dropdown', relatedTarget))
		}

		return false
	}

	WFDropdown.prototype.keydown = function (e) {
		if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return

		var $this = $(this)

		e.preventDefault()
		e.stopPropagation()

		if ($this.is('.wf-disabled, :disabled')) return

		var $parent  = getParent($this)
		var isActive = $parent.hasClass('wf-open')

		if (!isActive && e.which != 27 || isActive && e.which == 27) {
			if (e.which == 27) $parent.find(toggle).trigger('focus')
			return $this.trigger('click')
		}

		var desc = ' li:not(.wf-disabled):visible a'
		var $items = $parent.find('.wf-dropdown-menu' + desc)

		if (!$items.length) return

		var index = $items.index(e.target)

		if (e.which == 38 && index > 0)                 index--         // up
		if (e.which == 40 && index < $items.length - 1) index++         // down
		if (!~index)                                    index = 0

		$items.eq(index).trigger('focus')
	}


	// DROPDOWN PLUGIN DEFINITION
	// ==========================

	function Plugin(option) {
		return this.each(function () {
			var $this = $(this)
			var data  = $this.data('bs.wf-dropdown')

			if (!data) $this.data('bs.wf-dropdown', (data = new WFDropdown(this)))
			if (typeof option == 'string') data[option].call($this)
		})
	}

	var old = $.fn.wfdropdown

	$.fn.wfdropdown             = Plugin
	$.fn.wfdropdown.Constructor = WFDropdown


	// DROPDOWN NO CONFLICT
	// ====================

	$.fn.wfdropdown.noConflict = function () {
		$.fn.wfdropdown = old
		return this
	}


	// APPLY TO STANDARD DROPDOWN ELEMENTS
	// ===================================

	$(document)
		.on('click.bs.wf-dropdown.data-api', clearMenus)
		.on('click.bs.wf-dropdown.data-api', '.wf-dropdown form', function (e) { e.stopPropagation() })
		.on('click.bs.wf-dropdown.data-api', toggle, WFDropdown.prototype.toggle)
		.on('keydown.bs.wf-dropdown.data-api', toggle, WFDropdown.prototype.keydown)
		.on('keydown.bs.wf-dropdown.data-api', '.wf-dropdown-menu', WFDropdown.prototype.keydown)

}(jQuery);
