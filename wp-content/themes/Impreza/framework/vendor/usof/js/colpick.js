/*
 colpick Color Picker
 Copyright 2013 Jose Vargas. Licensed under GPL license. Based on Stefan Petre's Color Picker www.eyecon.ro, dual licensed under the MIT and GPL licenses

 For usage and examples: colpick.com/plugin
 */

(function ($) {
	var colpick = function () {
		var
			tpl = '<div class="colpick">' +
				'<div class="colpick_color">' +
				'<div class="colpick_color_overlay1"><div class="colpick_color_overlay2">' +
				'<div class="colpick_selector_outer"><div class="colpick_selector_inner"></div></div>' +
				'</div></div>' +
				'</div>' +
				'<div class="colpick_hue"><div class="colpick_hue_arrs"></div></div>' +
				'<div class="colpick_alpha"><div class="colpick_alpha_arrs"></div></div>' +
				'<div class="colpick_new_color"></div>' +
				'<div class="colpick_current_color"></div>' +
				'<div class="colpick_hex_field"><div class="colpick_field_letter">#</div><input type="text" maxlength="6" size="6" /></div>' +
				// Rgb
				'<div class="colpick_rgb_r colpick_field">' +
				'<div class="colpick_field_letter">R</div><input type="text" maxlength="3" size="3" />' +
				'<div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div>' +
				'</div>' +
				// rGb
				'<div class="colpick_rgb_g colpick_field">' +
				'<div class="colpick_field_letter">G</div><input type="text" maxlength="3" size="3" />' +
				'<div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div>' +
				'</div>' +
				// rgB
				'<div class="colpick_rgb_b colpick_field">' +
				'<div class="colpick_field_letter">B</div><input type="text" maxlength="3" size="3" />' +
				'<div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div>' +
				'</div>' +
				// Hsb
				'<div class="colpick_hsb_h colpick_field">' +
				'<div class="colpick_field_letter">H</div><input type="text" maxlength="3" size="3" />' +
				'<div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div>' +
				'</div>' +
				// hSb
				'<div class="colpick_hsb_s colpick_field">' +
				'<div class="colpick_field_letter">S</div><input type="text" maxlength="3" size="3" />' +
				'<div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div>' +
				'</div>' +
				// hsB
				'<div class="colpick_hsb_b colpick_field">' +
				'<div class="colpick_field_letter">B</div><input type="text" maxlength="3" size="3" />' +
				'<div class="colpick_field_arrs"><div class="colpick_field_uarr"></div><div class="colpick_field_darr"></div></div>' +
				'</div>' +
				// Alpha
				'<div class="colpick_field"><input type="text" maxlength="3" size="3" /></div>' +
				'<div class="colpick_submit"></div></div>',
			defaults = {
				showEvent: 'click',
				onShow: function () {
				},
				onBeforeShow: function () {
				},
				onHide: function () {
				},
				onChange: function () {
				},
				onSubmit: function () {
				},
				colorScheme: 'light',
				color: '3289c7',
				livePreview: true,
				flat: false,
				layout: 'full',
				submit: 1,
				submitText: 'OK',
				height: 156
			},
			transparentImage = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAAHnlligAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHJJREFUeNpi+P///4EDBxiAGMgCCCAGFB5AADGCRBgYDh48CCRZIJS9vT2QBAggFBkmBiSAogxFBiCAoHogAKIKAlBUYTELAiAmEtABEECk20G6BOmuIl0CIMBQ/IEMkO0myiSSraaaBhZcbkUOs0HuBwDplz5uFJ3Z4gAAAABJRU5ErkJggg==",
		//Fill the inputs of the plugin
			fillRGBAFields = function (hsba, cal) {
				var rgb = hsbaToRgba(hsba);
				$(cal).data('colpick').fields
					.eq(1).val(rgb.r).end()
					.eq(2).val(rgb.g).end()
					.eq(3).val(rgb.b).end()
					.eq(7).val(rgb.a).end();
			},
			fillHSBAFields = function (hsba, cal) {
				$(cal).data('colpick').fields
					.eq(4).val(Math.round(hsba.h)).end()
					.eq(5).val(Math.round(hsba.s)).end()
					.eq(6).val(Math.round(hsba.b)).end()
					.eq(7).val(hsba.a).end();
			},
			fillHexFields = function (hsba, cal) {
				$(cal).data('colpick').fields.eq(0).val(hsbaToHex(hsba));
			},
		//Set the round selector position
			setSelector = function (hsba, cal) {
				$(cal).data('colpick').selector.css('backgroundColor', hsbaToHex({h: hsba.h, s: 100, b: 100}));
				$(cal).data('colpick').selectorIndic.css({
					left: parseInt($(cal).data('colpick').height * hsba.s / 100, 10),
					top: parseInt($(cal).data('colpick').height * (100 - hsba.b) / 100, 10)
				});
			},
		//Set the hue selector position
			setHue = function (hsba, cal) {
				$(cal).data('colpick').hue.css('top', parseInt($(cal).data('colpick').height - $(cal).data('colpick').height * hsba.h / 360, 10));
			},
			// Set the alpha selector position
			setAlpha = function (hsba, cal) {
				var colpick = $(cal).data('colpick'),
					rgba = hsbaToRgba(hsba);
				if (hsba.a === undefined) hsba.a = 1.;
				colpick.alpha.css('top', parseInt(156 * (1. - hsba.a)));
				var alphaStyle = 'background: linear-gradient(to bottom, rgb(' + rgba.r + ', ' + rgba.g + ', ' + rgba.b + ') 0%, ';
				alphaStyle += 'rgba(' + rgba.r + ', ' + rgba.g + ', ' + rgba.b + ', 0) 100%) repeat scroll 0% 0%, transparent url("';
				alphaStyle += transparentImage + '") repeat scroll 0% 0%;';
				colpick.alphaContainer.attr('style', alphaStyle)
			},
		//Set current and new colors
			setCurrentColor = function (hsba, cal) {
				$(cal).data('colpick').currentColor.css('backgroundColor', hsbaToHex(hsba));
			},
			setNewColor = function (hsba, cal) {
				$(cal).data('colpick').newColor.css('backgroundColor', hsbaToHex(hsba));
			},
		//Called when the new color is changed
			change = function (ev) {
				var cal = $(this).parent().parent(), col;
				if (this.parentNode.className.indexOf('_hex') > 0) {
					cal.data('colpick').color = col = hexToHsba(fixHex(this.value));
					fillRGBAFields(col, cal.get(0));
					fillHSBAFields(col, cal.get(0));
				} else if (this.parentNode.className.indexOf('_hsb') > 0) {
					col = fixHSBA({
						h: parseInt(cal.data('colpick').fields.eq(4).val(), 10),
						s: parseInt(cal.data('colpick').fields.eq(5).val(), 10),
						b: parseInt(cal.data('colpick').fields.eq(6).val(), 10),
						a: parseFloat(cal.data('colpick').fields.eq(7).val())
					});
					cal.data('colpick').color = col;
					fillRGBAFields(col, cal.get(0));
					fillHexFields(col, cal.get(0));
				} else {
					cal.data('colpick').color = col = rgbaToHsba(fixRGBA({
						r: parseInt(cal.data('colpick').fields.eq(1).val(), 10),
						g: parseInt(cal.data('colpick').fields.eq(2).val(), 10),
						b: parseInt(cal.data('colpick').fields.eq(3).val(), 10),
						a: parseFloat(cal.data('colpick').fields.eq(7).val())
					}));
					fillHexFields(col, cal.get(0));
					fillHSBAFields(col, cal.get(0));
				}
				setSelector(col, cal.get(0));
				setHue(col, cal.get(0));
				setAlpha(col, cal.get(0));
				setNewColor(col, cal.get(0));
				cal.data('colpick').onChange.apply(cal.parent(), [col, hsbaToHex(col), hsbaToRgba(col), cal.data('colpick').el, 0]);
			},
		//Change style on blur and on focus of inputs
			blur = function (ev) {
				$(this).parent().removeClass('colpick_focus');
			},
			focus = function () {
				$(this).parent().parent().data('colpick').fields.parent().removeClass('colpick_focus');
				$(this).parent().addClass('colpick_focus');
			},
		//Increment/decrement arrows functions
			downIncrement = function (ev) {
				ev.preventDefault ? ev.preventDefault() : ev.returnValue = false;
				var field = $(this).parent().find('input').focus();
				var current = {
					el: $(this).parent().addClass('colpick_slider'),
					max: this.parentNode.className.indexOf('_hsb_h') > 0 ? 360 : (this.parentNode.className.indexOf('_hsb') > 0 ? 100 : 255),
					y: ev.pageY,
					field: field,
					val: parseInt(field.val(), 10),
					preview: $(this).parent().parent().data('colpick').livePreview
				};
				$(document).mouseup(current, upIncrement);
				$(document).mousemove(current, moveIncrement);
			},
			moveIncrement = function (ev) {
				ev.data.field.val(Math.max(0, Math.min(ev.data.max, parseInt(ev.data.val - ev.pageY + ev.data.y, 10))));
				if (ev.data.preview) {
					change.apply(ev.data.field.get(0), [true]);
				}
				return false;
			},
			upIncrement = function (ev) {
				change.apply(ev.data.field.get(0), [true]);
				ev.data.el.removeClass('colpick_slider').find('input').focus();
				$(document).off('mouseup', upIncrement);
				$(document).off('mousemove', moveIncrement);
				return false;
			},
		//Hue slider functions
			downHue = function (ev) {
				ev.preventDefault ? ev.preventDefault() : ev.returnValue = false;
				var current = {
					cal: $(this).parent(),
					y: $(this).offset().top
				};
				$(document).on('mouseup touchend', current, upHue);
				$(document).on('mousemove touchmove', current, moveHue);

				var pageY = ((ev.type == 'touchstart') ? ev.originalEvent.changedTouches[0].pageY : ev.pageY );
				change.apply(
					current.cal.data('colpick')
						.fields.eq(4).val(parseInt(360 * (current.cal.data('colpick').height - (pageY - current.y)) / current.cal.data('colpick').height, 10))
						.get(0),
					[current.cal.data('colpick').livePreview]
				);
				return false;
			},
			moveHue = function (ev) {
				var pageY = ((ev.type == 'touchmove') ? ev.originalEvent.changedTouches[0].pageY : ev.pageY );
				change.apply(
					ev.data.cal.data('colpick')
						.fields.eq(4).val(parseInt(360 * (ev.data.cal.data('colpick').height - Math.max(0, Math.min(ev.data.cal.data('colpick').height, (pageY - ev.data.y)))) / ev.data.cal.data('colpick').height, 10))
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upHue = function (ev) {
				fillRGBAFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
				$(document).off('mouseup touchend', upHue);
				$(document).off('mousemove touchmove', moveHue);
				return false;
			},
		//Alpha slider function
			downAlpha = function (ev) {
				ev.preventDefault ? ev.preventDefault() : ev.returnValue = false;
				var current = {
					cal: $(this).parent(),
					y: $(this).offset().top
				};
				$(document).on('mouseup touchend', current, upAlpha);
				$(document).on('mousemove touchmove', current, moveAlpha);

				var pageY = ((ev.type == 'touchstart') ? ev.originalEvent.changedTouches[0].pageY : ev.pageY );
				change.apply(
					current.cal.data('colpick')
						.fields.eq(7).val((current.cal.data('colpick').height - (pageY - current.y)) / current.cal.data('colpick').height)
						.get(0),
					[current.cal.data('colpick').livePreview]
				);
				return false;
			},
			moveAlpha = function (ev) {
				var pageY = ((ev.type == 'touchmove') ? ev.originalEvent.changedTouches[0].pageY : ev.pageY );
				change.apply(
					ev.data.cal.data('colpick')
						.fields.eq(7).val((ev.data.cal.data('colpick').height - (pageY - ev.data.y)) / ev.data.cal.data('colpick').height)
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upAlpha = function (ev) {
				fillRGBAFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
				$(document).off('mouseup touchend', upAlpha);
				$(document).off('mousemove touchmove', moveAlpha);
				return false;
			},
		//Color selector functions
			downSelector = function (ev) {
				ev.preventDefault ? ev.preventDefault() : ev.returnValue = false;
				var current = {
					cal: $(this).parent(),
					pos: $(this).offset()
				};
				current.preview = current.cal.data('colpick').livePreview;

				$(document).on('mouseup touchend', current, upSelector);
				$(document).on('mousemove touchmove', current, moveSelector);

				var payeX, pageY;
				if (ev.type == 'touchstart') {
					pageX = ev.originalEvent.changedTouches[0].pageX,
						pageY = ev.originalEvent.changedTouches[0].pageY;
				} else {
					pageX = ev.pageX;
					pageY = ev.pageY;
				}

				change.apply(
					current.cal.data('colpick').fields
						.eq(6).val(parseInt(100 * (current.cal.data('colpick').height - (pageY - current.pos.top)) / current.cal.data('colpick').height, 10)).end()
						.eq(5).val(parseInt(100 * (pageX - current.pos.left) / current.cal.data('colpick').height, 10))
						.get(0),
					[current.preview]
				);
				return false;
			},
			moveSelector = function (ev) {
				var payeX, pageY;
				if (ev.type == 'touchmove') {
					pageX = ev.originalEvent.changedTouches[0].pageX,
						pageY = ev.originalEvent.changedTouches[0].pageY;
				} else {
					pageX = ev.pageX;
					pageY = ev.pageY;
				}

				change.apply(
					ev.data.cal.data('colpick').fields
						.eq(6).val(parseInt(100 * (ev.data.cal.data('colpick').height - Math.max(0, Math.min(ev.data.cal.data('colpick').height, (pageY - ev.data.pos.top)))) / ev.data.cal.data('colpick').height, 10)).end()
						.eq(5).val(parseInt(100 * (Math.max(0, Math.min(ev.data.cal.data('colpick').height, (pageX - ev.data.pos.left)))) / ev.data.cal.data('colpick').height, 10))
						.get(0),
					[ev.data.preview]
				);
				return false;
			},
			upSelector = function (ev) {
				fillRGBAFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
				fillHexFields(ev.data.cal.data('colpick').color, ev.data.cal.get(0));
				$(document).off('mouseup touchend', upSelector);
				$(document).off('mousemove touchmove', moveSelector);
				return false;
			},
		//Submit button
			clickSubmit = function (ev) {
				var cal = $(this).parent();
				var col = cal.data('colpick').color;
				cal.data('colpick').origColor = col;
				setCurrentColor(col, cal.get(0));
				cal.data('colpick').onSubmit(col, hsbaToHex(col), hsbaToRgba(col), cal.data('colpick').el);
			},
		//Show/hide the color picker
			show = function (ev) {
				// Prevent the trigger of any direct parent
				if (ev !== undefined) ev.stopPropagation();
				var cal = $('#' + $(this).data('colpickId'));
				cal.data('colpick').onBeforeShow.apply(this, [cal.get(0)]);
				var pos = $(this).offset();
				var top = pos.top + this.offsetHeight;
				var left = pos.left;
				var viewPort = getViewport();
				var calW = cal.width();
				if (left + calW > viewPort.l + viewPort.w) {
					left -= calW;
				}
				cal.css({left: left + 'px', top: top + 'px'});
				if (cal.data('colpick').onShow.apply(this, [cal.get(0)]) != false) {
					cal.show();
				}
				//Hide when user clicks outside
				$('html').mousedown({cal: cal}, hide);
				cal.mousedown(function (ev) {
					ev.stopPropagation();
				})
			},
			hide = function (ev) {
				if (ev.data.cal.data('colpick').onHide.apply(this, [ev.data.cal.get(0)]) != false) {
					ev.data.cal.hide();
				}
				$('html').off('mousedown', hide);
			},
			getViewport = function () {
				var m = document.compatMode == 'CSS1Compat';
				return {
					l: window.pageXOffset || (m ? document.documentElement.scrollLeft : document.body.scrollLeft),
					w: window.innerWidth || (m ? document.documentElement.clientWidth : document.body.clientWidth)
				};
			},
		//Fix the values if the user enters a negative or high value
			fixHSBA = function (hsba) {
				if (hsba.a === undefined) hsba.a = 1.;
				return {
					h: Math.min(360, Math.max(0, hsba.h)),
					s: Math.min(100, Math.max(0, hsba.s)),
					b: Math.min(100, Math.max(0, hsba.b)),
					a: Math.min(1., Math.max(0., hsba.a))
				};
			},
			fixRGBA = function (rgba) {
				if (rgba.a === undefined) rgba.a = 1.;
				return {
					r: Math.min(255, Math.max(0, rgba.r)),
					g: Math.min(255, Math.max(0, rgba.g)),
					b: Math.min(255, Math.max(0, rgba.b)),
					a: Math.min(1., Math.max(0., rgba.a))
				};
			},
			fixHex = function (hex) {
				if (hex.substr(0, 5) == 'rgba(') {
					var parts = hex.substring(5, s.length - 1).split(',').map(parseFloat);
					if (parts.length == 4) return hex;
				}
				var len = 6 - hex.length;
				if (len > 0) {
					var o = [];
					for (var i = 0; i < len; i++) {
						o.push('0');
					}
					o.push(hex);
					hex = o.join('');
				}
				return hex;
			},
			restoreOriginal = function () {
				var cal = $(this).parent();
				var col = cal.data('colpick').origColor;
				cal.data('colpick').color = col;
				fillRGBAFields(col, cal.get(0));
				fillHexFields(col, cal.get(0));
				fillHSBAFields(col, cal.get(0));
				setSelector(col, cal.get(0));
				setHue(col, cal.get(0));
				setAlpha(col, cal.get(0));
				setNewColor(col, cal.get(0));
			};
		return {
			init: function (opt) {
				opt = $.extend({}, defaults, opt || {});
				//Set color
				if (typeof opt.color == 'string') {
					opt.color = hexToHsba(opt.color);
				} else if (opt.color.r != undefined && opt.color.g != undefined && opt.color.b != undefined) {
					opt.color = rgbaToHsba(opt.color);
				} else if (opt.color.h != undefined && opt.color.s != undefined && opt.color.b != undefined) {
					opt.color = fixHSBA(opt.color);
				} else {
					return this;
				}

				//For each selected DOM element
				return this.each(function () {
					//If the element does not have an ID
					if (!$(this).data('colpickId')) {
						var options = $.extend({}, opt);
						options.origColor = opt.color;
						//Generate and assign a random ID
						var id = 'collorpicker_' + parseInt(Math.random() * 100000);
						$(this).data('colpickId', id);
						//Set the tpl's ID and get the HTML
						var cal = $(tpl).attr('id', id);
						//Add class according to layout
						cal.addClass('colpick_' + options.layout + (options.submit ? '' : ' colpick_' + options.layout + '_ns'));
						//Add class if the color scheme is not default
						if (options.colorScheme != 'light') {
							cal.addClass('colpick_' + options.colorScheme);
						}
						//Setup submit button
						cal.find('div.colpick_submit').html(options.submitText).click(clickSubmit);
						//Setup input fields
						options.fields = cal.find('input').change(change).blur(blur).focus(focus);
						cal.find('div.colpick_field_arrs').mousedown(downIncrement).end().find('div.colpick_current_color').click(restoreOriginal);
						//Setup hue selector
						options.selector = cal.find('div.colpick_color').on('mousedown touchstart', downSelector);
						options.selectorIndic = options.selector.find('div.colpick_selector_outer');
						//Store parts of the plugin
						options.el = this;
						options.hue = cal.find('div.colpick_hue_arrs');
						huebar = options.hue.parent();
						options.alphaContainer = cal.find('div.colpick_alpha');
						options.alpha = cal.find('div.colpick_alpha_arrs');
						alphabar = options.alpha.parent();
						//Paint the hue bar
						var UA = navigator.userAgent.toLowerCase();
						var isIE = navigator.appName === 'Microsoft Internet Explorer';
						var IEver = isIE ? parseFloat(UA.match(/msie ([0-9]{1,}[\.0-9]{0,})/)[1]) : 0;
						var ngIE = ( isIE && IEver < 10 );
						var stops = ['#ff0000', '#ff0080', '#ff00ff', '#8000ff', '#0000ff', '#0080ff', '#00ffff', '#00ff80', '#00ff00', '#80ff00', '#ffff00', '#ff8000', '#ff0000'];
						if (ngIE) {
							var i, div;
							for (i = 0; i <= 11; i++) {
								div = $('<div></div>').attr('style', 'height:8.333333%; filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=' + stops[i] + ', endColorstr=' + stops[i + 1] + '); -ms-filter: "progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=' + stops[i] + ', endColorstr=' + stops[i + 1] + ')";');
								huebar.append(div);
							}
						} else {
							stopList = stops.join(',');
							huebar.attr('style', 'background:-webkit-linear-gradient(top,' + stopList + '); background: -o-linear-gradient(top,' + stopList + '); background: -ms-linear-gradient(top,' + stopList + '); background:-moz-linear-gradient(top,' + stopList + '); -webkit-linear-gradient(top,' + stopList + '); background:linear-gradient(to bottom,' + stopList + '); ');
						}
						cal.find('div.colpick_hue').on('mousedown touchstart', downHue);
						cal.find('div.colpick_alpha').on('mousedown touchstart', downAlpha);
						options.newColor = cal.find('div.colpick_new_color');
						options.currentColor = cal.find('div.colpick_current_color');
						//Store options and fill with default color
						cal.data('colpick', options);
						fillRGBAFields(options.color, cal.get(0));
						fillHSBAFields(options.color, cal.get(0));
						fillHexFields(options.color, cal.get(0));
						setHue(options.color, cal.get(0));
						setAlpha(options.color, cal.get(0));
						setSelector(options.color, cal.get(0));
						setCurrentColor(options.color, cal.get(0));
						setNewColor(options.color, cal.get(0));
						//Append to body if flat=false, else show in place
						if (options.flat) {
							cal.appendTo(this).show();
							cal.css({
								position: 'relative',
								display: 'block'
							});
						} else {
							cal.appendTo(document.body);
							$(this).on(options.showEvent, show);
							cal.css({
								position: 'absolute'
							});
						}
					}
				});
			},
			//Shows the picker
			showPicker: function () {
				return this.each(function () {
					if ($(this).data('colpickId')) {
						show.apply(this);
					}
				});
			},
			//Hides the picker
			hidePicker: function () {
				return this.each(function () {
					if ($(this).data('colpickId')) {
						$('#' + $(this).data('colpickId')).hide();
					}
				});
			},
			//Sets a color as new and current (default)
			setColor: function (col, setCurrent) {
				setCurrent = (typeof setCurrent === "undefined") ? 1 : setCurrent;
				if (typeof col == 'string') {
					col = hexToHsba(col);
				} else if (col.r != undefined && col.g != undefined && col.b != undefined) {
					col = rgbaToHsba(col);
				} else if (col.h != undefined && col.s != undefined && col.b != undefined) {
					col = fixHSBA(col);
				} else {
					return this;
				}
				return this.each(function () {
					if ($(this).data('colpickId')) {
						var cal = $('#' + $(this).data('colpickId'));
						cal.data('colpick').color = col;
						cal.data('colpick').origColor = col;
						fillRGBAFields(col, cal.get(0));
						fillHSBAFields(col, cal.get(0));
						fillHexFields(col, cal.get(0));
						setHue(col, cal.get(0));
						setAlpha(col, cal.get(0));
						setSelector(col, cal.get(0));

						setNewColor(col, cal.get(0));
						cal.data('colpick').onChange.apply(cal.parent(), [col, hsbaToHex(col), hsbaToRgba(col), cal.data('colpick').el, 1]);
						if (setCurrent) {
							setCurrentColor(col, cal.get(0));
						}
					}
				});
			}
		};
	}();
	//Color space convertions
	var hexToRgba = function (hex) {
		if (hex.substr(0, 5) == 'rgba(') {
			var parts = hex.substring(5, hex.length - 1).split(',').map(parseFloat);
			if (parts.length == 4) return {r: parts[0], g: parts[1], b: parts[2], a: parts[3]};
		}
		if (hex.length == 3) {
			hex = hex.charAt(0) + hex.charAt(0) + hex.charAt(1) + hex.charAt(0) + hex.charAt(2) + hex.charAt(2);
		}
		hex = parseInt(((hex.indexOf('#') > -1) ? hex.substring(1) : hex), 16);
		return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF), a: 1.};
	};
	var hexToHsba = function (hex) {
		return rgbaToHsba(hexToRgba(hex));
	};
	var rgbaToHsba = function (rgba) {
		var hsba = {h: 0, s: 0, b: 0};
		var min = Math.min(rgba.r, rgba.g, rgba.b);
		var max = Math.max(rgba.r, rgba.g, rgba.b);
		var delta = max - min;
		hsba.b = max;
		hsba.s = max != 0 ? 255 * delta / max : 0;
		if (hsba.s != 0) {
			if (rgba.r == max) hsba.h = (rgba.g - rgba.b) / delta;
			else if (rgba.g == max) hsba.h = 2 + (rgba.b - rgba.r) / delta;
			else hsba.h = 4 + (rgba.r - rgba.g) / delta;
		} else hsba.h = -1;
		hsba.h *= 60;
		if (hsba.h < 0) hsba.h += 360;
		hsba.s *= 100 / 255;
		hsba.b *= 100 / 255;
		hsba.a = rgba.a;
		return hsba;
	};
	var hsbaToRgba = function (hsba) {
		var rgb = {};
		var h = hsba.h;
		var s = hsba.s * 255 / 100;
		var v = hsba.b * 255 / 100;
		if (s == 0) {
			rgb.r = rgb.g = rgb.b = v;
		} else {
			var t1 = v;
			var t2 = (255 - s) * v / 255;
			var t3 = (t1 - t2) * (h % 60) / 60;
			if (h == 360) h = 0;
			if (h < 60) {
				rgb.r = t1;
				rgb.b = t2;
				rgb.g = t2 + t3
			}
			else if (h < 120) {
				rgb.g = t1;
				rgb.b = t2;
				rgb.r = t1 - t3
			}
			else if (h < 180) {
				rgb.g = t1;
				rgb.r = t2;
				rgb.b = t2 + t3
			}
			else if (h < 240) {
				rgb.b = t1;
				rgb.r = t2;
				rgb.g = t1 - t3
			}
			else if (h < 300) {
				rgb.b = t1;
				rgb.g = t2;
				rgb.r = t2 + t3
			}
			else if (h < 360) {
				rgb.r = t1;
				rgb.g = t2;
				rgb.b = t1 - t3
			}
			else {
				rgb.r = 0;
				rgb.g = 0;
				rgb.b = 0
			}
		}
		return {r: Math.round(rgb.r), g: Math.round(rgb.g), b: Math.round(rgb.b), a: hsba.a};
	};
	var rgbaToHex = function (rgba) {
		if (rgba.a !== undefined && rgba.a < 1.) {
			return 'rgba(' + rgba.r + ',' + rgba.g + ',' + rgba.b + ',' + Math.round(rgba.a * 100) / 100 + ')';
		}
		var hex = [
			rgba.r.toString(16),
			rgba.g.toString(16),
			rgba.b.toString(16)
		];
		$.each(hex, function (nr, val) {
			if (val.length == 1) {
				hex[nr] = '0' + val;
			}
		});
		return '#' + hex.join('');
	};
	var hsbaToHex = function (hsba) {
		return rgbaToHex(hsbaToRgba(hsba));
	};
	$.fn.extend({
		colpick: colpick.init,
		colpickHide: colpick.hidePicker,
		colpickShow: colpick.showPicker,
		colpickSetColor: colpick.setColor
	});
	$.extend({
		colpick: {
			rgbaToHex: rgbaToHex,
			rgbaToHsb: rgbaToHsba,
			hsbaToHex: hsbaToHex,
			hsbaToRgba: hsbaToRgba,
			hexToHsba: hexToHsba,
			hexToRgba: hexToRgba
		}
	});
})(jQuery);
