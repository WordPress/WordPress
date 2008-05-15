/*
 * jQuery UI @VERSION
 *
 * Copyright (c) 2008 Paul Bakaus (ui.jquery.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://docs.jquery.com/UI
 *
 * $Id: ui.core.js 5587 2008-05-13 19:56:42Z scott.gonzalez $
 */
;(function($) {
	
	$.ui = {
		plugin: {
			add: function(module, option, set) {
				var proto = $.ui[module].prototype;
				for(var i in set) {
					proto.plugins[i] = proto.plugins[i] || [];
					proto.plugins[i].push([option, set[i]]);
				}
			},
			call: function(instance, name, args) {
				var set = instance.plugins[name];
				if(!set) { return; }
				
				for (var i = 0; i < set.length; i++) {
					if (instance.options[set[i][0]]) {
						set[i][1].apply(instance.element, args);
					}
				}
			}	
		},
		cssCache: {},
		css: function(name) {
			if ($.ui.cssCache[name]) { return $.ui.cssCache[name]; }
			var tmp = $('<div class="ui-resizable-gen">').addClass(name).css({position:'absolute', top:'-5000px', left:'-5000px', display:'block'}).appendTo('body');
			
			//if (!$.browser.safari)
				//tmp.appendTo('body'); 
			
			//Opera and Safari set width and height to 0px instead of auto
			//Safari returns rgba(0,0,0,0) when bgcolor is not set
			$.ui.cssCache[name] = !!(
				(!(/auto|default/).test(tmp.css('cursor')) || (/^[1-9]/).test(tmp.css('height')) || (/^[1-9]/).test(tmp.css('width')) || 
				!(/none/).test(tmp.css('backgroundImage')) || !(/transparent|rgba\(0, 0, 0, 0\)/).test(tmp.css('backgroundColor')))
			);
			try { $('body').get(0).removeChild(tmp.get(0));	} catch(e){}
			return $.ui.cssCache[name];
		},
		disableSelection: function(e) {
			e.unselectable = "on";
			e.onselectstart = function() { return false; };
			if (e.style) { e.style.MozUserSelect = "none"; }
		},
		enableSelection: function(e) {
			e.unselectable = "off";
			e.onselectstart = function() { return true; };
			if (e.style) { e.style.MozUserSelect = ""; }
		},
		hasScroll: function(e, a) {
			var scroll = /top/.test(a||"top") ? 'scrollTop' : 'scrollLeft', has = false;
			if (e[scroll] > 0) return true; e[scroll] = 1;
			has = e[scroll] > 0 ? true : false; e[scroll] = 0;
			return has;
		}
	};
	
	
	/** jQuery core modifications and additions **/
	
	var _remove = $.fn.remove;
	$.fn.remove = function() {
		$("*", this).add(this).trigger("remove");
		return _remove.apply(this, arguments );
	};
	
	// $.widget is a factory to create jQuery plugins
	// taking some boilerplate code out of the plugin code
	// created by Scott González and Jörn Zaefferer
	function getter(namespace, plugin, method) {
		var methods = $[namespace][plugin].getter || [];
		methods = (typeof methods == "string" ? methods.split(/,?\s+/) : methods);
		return ($.inArray(method, methods) != -1);
	};
	
	var widgetPrototype = {
		init: function() {},
		destroy: function() {
			this.element.removeData(this.widgetName);
		},
		
		getData: function(key) {
			return this.options[key];
		},
		setData: function(key, value) {
			this.options[key] = value;
		},
		
		enable: function() {
			this.setData('disabled', false);
		},
		disable: function() {
			this.setData('disabled', true);
		}
	};
	
	$.widget = function(name, prototype) {
		var namespace = name.split(".")[0];
		name = name.split(".")[1];
		// create plugin method
		$.fn[name] = function(options, data) {
			var isMethodCall = (typeof options == 'string');
			[].shift.call(arguments);
			
			if (isMethodCall && getter(namespace, name, options)) {
				var instance = $.data(this[0], name);
				return (instance ? instance[options].apply(instance, arguments)
					: undefined);
			}
			
			return this.each(function() {
				var instance = $.data(this, name);
				if (!instance) {
					$.data(this, name, new $[namespace][name](this, options));
				} else if (isMethodCall) {
					instance[options].apply(instance, arguments);
				}
			});
		};
		
		// create widget constructor
		$[namespace][name] = function(element, options) {
			var self = this;
			
			this.widgetName = name;
			
			this.options = $.extend({}, $[namespace][name].defaults, options);
			this.element = $(element)
				.bind('setData.' + name, function(e, key, value) {
					return self.setData(key, value);
				})
				.bind('getData.' + name, function(e, key) {
					return self.getData(key);
				})
				.bind('remove', function() {
					return self.destroy();
				});
			this.init();
		};
		
		// add widget prototype
		$[namespace][name].prototype = $.extend({}, widgetPrototype, prototype);
	};
	
	
	/** Mouse Interaction Plugin **/
	
	$.widget("ui.mouse", {
		init: function() {
			var self = this;
			
			this.element
				.bind('mousedown.mouse', function() { return self.click.apply(self, arguments); })
				.bind('mouseup.mouse', function() { (self.timer && clearTimeout(self.timer)); })
				.bind('click.mouse', function() { if(self.initialized) { self.initialized = false; return false; } });
			//Prevent text selection in IE
			if ($.browser.msie) {
				this.unselectable = this.element.attr('unselectable');
				this.element.attr('unselectable', 'on');
			}
		},
		destroy: function() {
			this.element.unbind('.mouse').removeData("mouse");
			($.browser.msie && this.element.attr('unselectable', this.unselectable));
		},
		trigger: function() { return this.click.apply(this, arguments); },
		click: function(e) {
		
			if(    e.which != 1 //only left click starts dragging
				|| $.inArray(e.target.nodeName.toLowerCase(), this.options.dragPrevention || []) != -1 // Prevent execution on defined elements
				|| (this.options.condition && !this.options.condition.apply(this.options.executor || this, [e, this.element])) //Prevent execution on condition
			) { return true; }
		
			var self = this;
			this.initialized = false;
			var initialize = function() {
				self._MP = { left: e.pageX, top: e.pageY }; // Store the click mouse position
				$(document).bind('mouseup.mouse', function() { return self.stop.apply(self, arguments); });
				$(document).bind('mousemove.mouse', function() { return self.drag.apply(self, arguments); });
		
				if(!self.initalized && Math.abs(self._MP.left-e.pageX) >= self.options.distance || Math.abs(self._MP.top-e.pageY) >= self.options.distance) {
					(self.options.start && self.options.start.call(self.options.executor || self, e, self.element));
					(self.options.drag && self.options.drag.call(self.options.executor || self, e, this.element)); //This is actually not correct, but expected
					self.initialized = true;
				}
			};

			if(this.options.delay) {
				if(this.timer) { clearTimeout(this.timer); }
				this.timer = setTimeout(initialize, this.options.delay);
			} else {
				initialize();
			}
				
			return false;
			
		},
		stop: function(e) {
			
			if(!this.initialized) {
				return $(document).unbind('mouseup.mouse').unbind('mousemove.mouse');
			}

			(this.options.stop && this.options.stop.call(this.options.executor || this, e, this.element));
			
			$(document).unbind('mouseup.mouse').unbind('mousemove.mouse');
			return false;
			
		},
		drag: function(e) {

			var o = this.options;
			if ($.browser.msie && !e.button) {
				return this.stop.call(this, e); // IE mouseup check
			}
			
			if(!this.initialized && (Math.abs(this._MP.left-e.pageX) >= o.distance || Math.abs(this._MP.top-e.pageY) >= o.distance)) {
				(o.start && o.start.call(o.executor || this, e, this.element));
				this.initialized = true;
			} else {
				if(!this.initialized) { return false; }
			}

			(o.drag && o.drag.call(this.options.executor || this, e, this.element));
			return false;
			
		}
	});
	
})(jQuery);
