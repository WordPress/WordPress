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
		return this.each(function(){
			this.className = this.className.replace(new RegExp('(^| )' + mod + '\_[a-zA-Z0-9\_\-]+( |$)'), '$2');
		});
	}
	var pcre = new RegExp('^.*?' + mod + '\_([a-zA-Z0-9\_\-]+).*?$'),
		arr;
	// Retrieve modificator
	if (value === undefined) {
		return (arr = pcre.exec(this.get(0).className)) ? arr[1] : false;
	}
	// Set modificator
	else {
		var regexp = new RegExp('(^| )' + mod + '\_[a-zA-Z0-9\_\-]+( |$)');
		return this.each(function(){
			if (this.className.match(regexp)) {
				this.className = this.className.replace(regexp, '$1' + mod + '_' + value + '$2');
			} else {
				this.className += ' ' + mod + '_' + value;
			}
		});
	}
};

/**
 * USOF Fields
 */
!function($){

	if (window.$usof === undefined) window.$usof = {};
	if ($usof.mixins === undefined) $usof.mixins = {};

	// Prototype mixin for all classes working with events
	$usof.mixins.Events = {
		/**
		 * Attach a handler to an event for the class instance
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Function} handler A function to execute each time the event is triggered
		 */
		on: function(eventType, handler){
			if (this.$$events === undefined) this.$$events = {};
			if (this.$$events[eventType] === undefined) this.$$events[eventType] = [];
			this.$$events[eventType].push(handler);
			return this;
		},
		/**
		 * Remove a previously-attached event handler from the class instance
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Function} [handler] The function that is to be no longer executed.
		 * @chainable
		 */
		off: function(eventType, handler){
			if (this.$$events === undefined || this.$$events[eventType] === undefined) return this;
			if (handler !== undefined) {
				var handlerPos = $.inArray(handler, this.$$events[eventType]);
				if (handlerPos != -1) {
					this.$$events[eventType].splice(handlerPos, 1);
				}
			} else {
				this.$$events[eventType] = [];
			}
			return this;
		},
		/**
		 * Execute all handlers and behaviours attached to the class instance for the given event type
		 * @param {String} eventType A string containing event type, such as 'beforeShow' or 'change'
		 * @param {Array} extraParameters Additional parameters to pass along to the event handler
		 * @chainable
		 */
		trigger: function(eventType, extraParameters){
			if (this.$$events === undefined || this.$$events[eventType] === undefined || this.$$events[eventType].length == 0) return this;
			var params = (arguments.length > 2 || !$.isArray(extraParameters)) ? Array.prototype.slice.call(arguments, 1) : extraParameters;
			// First argument is the current class instance
			params.unshift(this);
			for (var index = 0; index < this.$$events[eventType].length; index++) {
				this.$$events[eventType][index].apply(this.$$events[eventType][index], params);
			}
			return this;
		}
	};

	$usof.field = function(row, options){
		this.$row = $(row);
		this.type = this.$row.usMod('type');
		this.name = this.$row.data('name');
		this.id = this.$row.data('id');
		this.$input = this.$row.find('[name="' + this.name + '"]');
		this.inited = false;

		/**
		 * Boundable field events
		 */
		this.$$events = {
			beforeShow: [],
			afterShow: [],
			change: [],
			beforeHide: [],
			afterHide: []
		};

		// Overloading selected functions, moving parent functions to "parent" namespace: init => parentInit
		if ($usof.field[this.type] !== undefined) {
			for (var fn in $usof.field[this.type]) {
				if (!$usof.field[this.type].hasOwnProperty(fn)) continue;
				if (this[fn] !== undefined) {
					var parentFn = 'parent' + fn.charAt(0).toUpperCase() + fn.slice(1);
					this[parentFn] = this[fn];
				}
				this[fn] = $usof.field[this.type][fn];
			}
		}

		this.$row.data('usofField', this);

		// Init on first show
		var initEvent = function(){
			this.init(options);
			this.inited = true;
			this.off('beforeShow', initEvent);
		}.bind(this);
		this.on('beforeShow', initEvent);
	};
	$.extend($usof.field.prototype, $usof.mixins.Events, {
		init: function(){
			if (this._events === undefined) this._events = {};
			this._events.change = function(){
				this.trigger('change', [this.getValue()]);
			}.bind(this);
			this.$input.on('change', this._events.change);
		},
		getValue: function(){
			return this.$input.val();
		},
		setValue: function(value, quiet){
			this.$input.val(value);
			if (!quiet) this.trigger('change', [value]);
		}
	});

	/**
	 * USOF Field: Backup
	 */
	$usof.field['backup'] = {

		init: function(){
			this.$backupStatus = this.$row.find('.usof-backup-status');
			this.$btnBackup = this.$row.find('.usof-button.type_backup').on('click', this.backup.bind(this));
			this.$btnRestore = this.$row.find('.usof-button.type_restore').on('click', this.restore.bind(this));

			// JS Translations
			var $i18n = this.$row.find('.usof-backup-i18n');
			this.i18n = {};
			if ($i18n.length > 0) {
				this.i18n = $i18n[0].onclick() || {};
			}
		},

		backup: function(){
			$.ajax({
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_backup',
					_wpnonce: this.$row.closest('.usof-form').find('[name="_wpnonce"]').val(),
					_wp_http_referer: this.$row.closest('.usof-form').find('[name="_wp_http_referer"]').val()
				},
				success: function(result){
					this.$backupStatus.html(result.data.status);
					this.$btnRestore.show();
					alert(result.data.message);
				}.bind(this)
			});
		},

		restore: function(){
			if (!confirm(this.i18n.restore_confirm)) return;
			$.ajax({
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_restore_backup',
					_wpnonce: this.$row.closest('.usof-form').find('[name="_wpnonce"]').val(),
					_wp_http_referer: this.$row.closest('.usof-form').find('[name="_wp_http_referer"]').val()
				},
				success: function(result){
					$usof.instance.setValues(result.data.usof_options);
					$usof.instance.save();
					alert(result.data.message);
				}.bind(this)
			});
		}

	};

	/**
	 * USOF Field: Checkbox
	 */
	$usof.field['checkboxes'] = {

		getValue: function(){
			var value = [];
			$.each(this.$input, function(){
				if (this.checked) value.push(this.value);
			});
			return value;
		},

		setValue: function(value, quiet){
			$.each(this.$input, function(){
				$(this).attr('checked', ($.inArray(this.value, value) != -1) ? 'checked' : false);
			});
		}

	};

	/**
	 * USOF Field: Color
	 */
	$usof.field['color'] = {

		init: function(options){
			this.$color = this.$row.find('.usof-color');
			this.$preview = this.$row.find('.usof-color-preview');
			this.$clear = this.$row.find('.usof-color-clear');
			this.$input.colpick({
				layout: 'hex',
				color: (this.$input.val() || ''),
				submit: false,
				showEvent: 'focus',
				onChange: function(hsb, hex, rgb, el, bySetColor){
					this.$preview.css('background', hex);
					this.$input.toggleClass('with_alpha', hex.substr(0, 5) == 'rgba(')
					if (!bySetColor) this.$input.val(hex);
				}.bind(this),
				onShow: function(){
					this.$color.addClass('active');
				}.bind(this),
				onHide: function(){
					this.$color.removeClass('active');
					this.trigger('change', this.$input.val());
				}.bind(this)
			});
			this.$input.on('keyup', function(){
				var value = this.$input.val() || '';
				if (value == '') {
					this.$preview.removeAttr('style');
					return;
				}
				if ((value.length == 3 || value.length == 4) && (m = /^\#?([0-9a-fA-F]{3})$/.exec(value))){
					value = '#' + m[1][0] + m[1][0] + m[1][1] + m[1][1] + m[1][2] + m[1][2];
				}
				if ((value.length == 6) && (m = /^([0-9a-fA-F]{6})$/.exec(value))){
					value = '#' + m[1];
				}
				this.$input.colpickSetColor(value);
			}.bind(this));
			this.$input.on('change', function(){
				this.setValue(this.$input.val());
			}.bind(this));
			this.$preview.on('click', function(){
				this.$input.colpickShow();
			}.bind(this));
			this.$clear.on('click', function(){
				this.setValue('');
			}.bind(this));
		},

		setValue: function(value, quiet){
			if ((value.length == 3 || value.length == 4) && (m = /^\#?([0-9a-fA-F]{3})$/.exec(value))){
				value = '#' + m[1][0] + m[1][0] + m[1][1] + m[1][1] + m[1][2] + m[1][2];
			}
			if ((value.length == 6) && (m = /^([0-9a-fA-F]{6})$/.exec(value))){
				value = '#' + m[1];
			}
			if (value == '') {
				this.$preview.removeAttr('style');
				this.$input.removeClass('with_alpha');
			} else {
				this.$input.colpickSetColor(value);
			}
			this.parentSetValue(value, quiet);
		}

	};

	/**
	 * USOF Field: Css / Html
	 */
	$usof.field['css'] = $usof.field['html'] = {

		init: function(){
			this._events = {};
			this._events.editorChange = function(e){
				var value = this.editor.getSession().getValue();
				this.parentSetValue(value);
			}.bind(this);
			this.$editor = this.$row.find('.usof-form-row-control-ace').text(this.getValue());
			// Loading ACE dynamically
			if (window.ace === undefined) {
				var data = this.$row.find('.usof-form-row-control-param')[0].onclick() || {},
					script = document.createElement('script');
				script.onload = this._init.bind(this);
				script.type = 'text/javascript';
				script.src = data.ace_path;
				document.getElementsByTagName('head')[0].appendChild(script);
				return;
			}
			this._init();
		},

		_init: function(){
			this.$input.hide();
			this.editor = ace.edit(this.$editor[0]);
			this.editor.setTheme("ace/theme/dawn");
			this.editor.getSession().setMode("ace/mode/" + this.type);
			this.editor.setShowFoldWidgets(false);
			this.editor.setFontSize(13);
			this.editor.getSession().setUseWorker(false);
			this.editor.getSession().setValue(this.getValue());
			this.editor.getSession().on('change', this._events.editorChange);
			// Resize handler
			this.$body = $(document.body);
			this.$window = $(window);
			this.$control = this.$row.find('.usof-form-row-control');
			this.$resize = this.$row.find('.usof-form-row-resize').insertAfter(this.$control);
			this.$resizeKnob = this.$row.find('.usof-form-row-resize-knob');
			var startPageY, startHeight, draggedValue;
			$.extend(this._events, {
				dragstart: function(e){
					e.stopPropagation();
					this.$resize.addClass('dragged');
					startPageY = e.pageY;
					startHeight = this.$control.height();
					this.$body.on('mousemove', this._events.dragmove);
					this.$window.on('mouseup', this._events.dragstop);
					this._events.dragmove(e);
				}.bind(this),
				dragmove: function(e){
					e.stopPropagation();
					draggedValue = Math.max(startPageY - startHeight + 400, Math.round(e.pageY));
					this.$resizeKnob.css('top', draggedValue - startPageY);
				}.bind(this),
				dragstop: function(e){
					e.stopPropagation();
					this.$body.off('mousemove', this._events.dragmove);
					this.$window.off('mouseup', this._events.dragstop);
					this.$control.height(startHeight + draggedValue - startPageY);
					this.$resizeKnob.css('top', 0);
					this.editor.resize();
					this.$resize.removeClass('dragged');
				}.bind(this)
			});
			this.$resizeKnob.on('mousedown', this._events.dragstart);
		},

		setValue: function(value){
			if (this.editor !== undefined) {
				this.editor.getSession().off('change', this._events.editorChange);
				this.editor.setValue(value);
				this.editor.getSession().on('change', this._events.editorChange);
			} else {
				this.parentSetValue(value);
			}
		}

	};

	/**
	 * USOF Field: Font
	 */
	$usof.field['font'] = {

		init: function(options){
			this.parentInit(options);
			this.$select = this.$row.find('select');
			this.$preview = this.$row.find('.usof-font-preview');
			this.$weightsContainer = this.$row.find('.usof-checkbox-list');
			this.$weightCheckboxes = this.$weightsContainer.find('.usof-checkbox');
			this.$weights = this.$weightsContainer.find('input');
			this.fonts = $('.usof-fonts-json')[0].onclick() || {};
			this.curFont = this.$select.find(':selected').val();

			this.$select.on('change', function(){
				this.setValue(this._getValue());
			}.bind(this));
			this.$weights.on('change', function(){
				this.setValue(this._getValue());
			}.bind(this));
			if (this.curFont != 'none' && this.curFont.indexOf(',') == -1) {
				$('head').append('<link href="//fonts.googleapis.com/css?family=' + this.curFont.replace(/\s+/g, '+') + '" rel="stylesheet" type="text/css" class="usof_font_' + this.id + '" />');
				this.$preview.css('font-family', this.curFont + '');
			}
			this.$select.select2();
		},

		setValue: function(value, quiet){
			var parts = value.split('|'),
				fontName = parts[0] || 'none',
				fontWeights = parts[1] || '400,700';
			fontWeights = fontWeights.split(',');
			if (fontName != this.curFont) {
				$('.usof_font_' + this.id).remove();
				if (fontName == 'none') {
					// Selected no-font
					this.$preview.css('font-family', '');
				}
				else if (fontName.indexOf(',') != -1) {
					// Web-safe font combination
					this.$preview.css('font-family', fontName);
				}
				else {
					// Selected some google font: show preview
					$('head').append('<link href="//fonts.googleapis.com/css?family=' + fontName.replace(/\s+/g, '+') + '" rel="stylesheet" type="text/css" class="usof_font_' + this.id + '" />');
					this.$preview.css('font-family', fontName + ', sans-serif');
				}
				if (this.$select.select2('val') != fontName) {
					// setValue may be called both from inside and outside, so checking to avoid recursion
					this.$select.select2('val', fontName);
				}
				this.curFont = fontName;
			}
			// Show the available weights
			if (this.fonts[fontName] === undefined) {
				this.$weightCheckboxes.addClass('hidden');
			} else {
				this.$weightCheckboxes.each(function(index, elm){
					var $elm = $(elm),
						weightValue = $elm.data('value') + '';
					$elm.toggleClass('hidden', $.inArray(weightValue, this.fonts[fontName].variants) == -1);
					$elm.attr('checked', ($.inArray(weightValue, fontWeights) == -1) ? 'checked' : false);
				}.bind(this));
			}
			this.parentSetValue(value, quiet);
		},

		_getValue: function(){
			var fontName = this.$select.val(),
				fontWeights = [];
			if (this.fonts[fontName] !== undefined && this.fonts[fontName].variants !== undefined) {
				this.$weights.filter(':checked').each(function(index, elm){
					var weightValue = $(elm).val() + '';
					if ($.inArray(weightValue, this.fonts[fontName].variants) != -1) {
						fontWeights.push(weightValue);
					}
				}.bind(this));
			}
			return fontName + '|' + fontWeights.join(',');
		}

	};

	/**
	 * USOF Field: Imgradio / Radio
	 */
	$usof.field['imgradio'] = $usof.field['radio'] = {

		getValue: function(){
			return this.$input.filter(':checked').val();
		},

		setValue: function(value, quiet){
			if (quiet) this.$input.off('change', this._events.change);
			this.$input.filter('[value="' + value + '"]').attr('checked', 'checked');
			if (quiet) this.$input.on('change', this._events.change);
		}

	};

	/**
	 * USOF Field: Link
	 */
	$usof.field['link'] = {

		init: function(options){
			this.parentInit(options);
			this.$mainField = this.$row.find('input[type="hidden"]:first');
			this.$url = this.$row.find('input[type="text"]:first');
			this.$target = this.$row.find('input[type="checkbox"]:first');

			this.$url.on('change', function(){
				this.$mainField.val(JSON.stringify(this.getValue()));
			}.bind(this));

			this.$target.on('change', function(){
				this.$mainField.val(JSON.stringify(this.getValue()));
			}.bind(this));
		},

		getValue: function(){
			if (!this.inited) return {};
			return {
				url: this.$url.val(),
				target: this.$target.is(':checked') ? '_blank' : ''
			};
		},

		setValue: function(value, quiet){
			if (!this.inited) return;
			if (typeof value != 'object' || value.url === undefined) {
				value = {
					url: (typeof value == 'string') ? value : ''
				}
			}
			this.$url.val(value.url);
			this.$target.attr('checked', (value.target == '_blank') ? 'checked' : false);
		},

	};

	/**
	 * USOF Field: Reset
	 */
	$usof.field['reset'] = {

		init: function(){
			this.$resetControl = this.$row.find('.usof-control.for_reset');
			this.$btnReset = this.$row.find('.usof-button.type_reset').on('click', this.reset.bind(this));
			this.$resetMessage = this.$row.find('.usof-control-message');
			this.resetStateTimer = null;
			this.i18n = (this.$row.find('.usof-form-row-control-i18n')[0].onclick() || {});
		},

		reset: function(){
			if (!confirm(this.i18n.reset_confirm)) return;
			clearTimeout(this.resetStateTimer);
			this.$resetMessage.html('');
			this.$resetControl.usMod('status', 'loading');
			$.ajax({
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_reset',
					_wpnonce: $usof.instance.$container.find('[name="_wpnonce"]').val(),
					_wp_http_referer: $usof.instance.$container.find('[name="_wp_http_referer"]').val()
				},
				success: function(result){
					this.$resetMessage.html(result.data.message);
					this.$resetControl.usMod('status', 'success');
					this.resetStateTimer = setTimeout(function(){
						this.$resetMessage.html('');
						this.$resetControl.usMod('status', 'clear');
					}.bind(this), 4000);
					$usof.instance.setValues(result.data.usof_options);
					$usof.instance.valuesChanged = {};
					$usof.instance.$saveControl.usMod('status', 'clear');
				}.bind(this)
			});
		}

	};

	/**
	 * USOF Field: Slider
	 */
	$usof.field['slider'] = {

		init: function(options){
			this.$slider = this.$row.find('.usof-slider');
			// Params
			this.min = parseFloat(this.$slider.data('min'));
			this.max = parseFloat(this.$slider.data('max'));
			this.step = parseFloat(this.$slider.data('step')) || 1;
			this.prefix = this.$slider.data('prefix') || '';
			this.postfix = this.$slider.data('postfix') || '';
			this.$textfield = this.$row.find('input[type="text"]');
			this.$box = this.$row.find('.usof-slider-box');
			this.$range = this.$row.find('.usof-slider-range');
			this.$body = $(document.body);
			this.$window = $(window);
			// Needed box dimensions
			this.sz = {};
			var draggedValue;
			this._events = {
				dragstart: function(e){
					e.stopPropagation();
					this.sz = {left: this.$box.offset().left, width: this.$box.width()};
					this.$body.on('mousemove', this._events.dragmove);
					this.$window.on('mouseup', this._events.dragstop);
					this._events.dragmove(e);
				}.bind(this),
				dragmove: function(e){
					e.stopPropagation();
					var x = Math.max(0, Math.min(1, (this.sz == 0) ? 0 : ((e.pageX - this.sz.left) / this.sz.width))),
						value = parseFloat(this.min + x * (this.max - this.min));
					value = Math.round(value / this.step) * this.step;
					this.renderValue(value);
					draggedValue = value;
				}.bind(this),
				dragstop: function(e){
					e.stopPropagation();
					this.$body.off('mousemove', this._events.dragmove);
					this.$window.off('mouseup', this._events.dragstop);
					this.setValue(draggedValue);
				}.bind(this)
			};
			this.$textfield.on('focus', function(){
				this.$textfield.val(this.getValue());
			}.bind(this));
			this.$textfield.on('blur', function(){
				var value = parseFloat(this.$textfield.val().replace('[^0-9.]+', ''));
				this.setValue(value);
			}.bind(this));
			this.$box.on('mousedown', this._events.dragstart);
		},

		renderValue: function(value){
			var x = Math.max(0, Math.min(1, (value - this.min) / (this.max - this.min))),
				valueDecimalPart;
			this.$range.css('left', x * 100 + '%');

			if ($.isNumeric(value)) {
				value = parseFloat(value);
				valueDecimalPart = value % 1 + '';
				if (valueDecimalPart.charAt(3) !== '' && valueDecimalPart.charAt(3) !== '0') { // Decimal part has 1/100 part
					value = value.toFixed(2);
				} else if (valueDecimalPart.charAt(2) !== '' && valueDecimalPart.charAt(2) !== '0') { // Decimal part has 1/10 part
					value = value.toFixed(1);
				} else { // Decimal part is less than 1/100 or it is just 0
					value = value.toFixed(0);
				}
			}

			this.$textfield.val(this.prefix + value + this.postfix);
		},

		setValue: function(value, quiet){
			this.renderValue(value);
			this.parentSetValue(value, quiet);
		}

	};

	/**
	 * USOF Field: Switch
	 */
	$usof.field['switch'] = {

		getValue: function(){
			return (this.$input.is(':checked') ? this.$input.get(1).value : this.$input.get(0).value);
		},

		setValue: function(value, quiet){
			if (typeof value == 'string') value = (value == 'true' || value == 'True' || value == 'TRUE' || value == '1');
			else if (typeof value != 'boolean') value = !!parseInt(value);
			this.$input.filter('[type="checkbox"]').prop('checked', value);
		}

	};

	/**
	 * USOF Field: Text / Textarea
	 */
	$usof.field['text'] = $usof.field['textarea'] = {

		init: function(){
			this.$input.on('change keyup', function(){
				this.trigger('change', this.getValue());
			}.bind(this));
		}

	};

	/**
	 * USOF Field: Button Preview
	 */
	$usof.field['button_preview'] = {

		init: function(){

			this.dependsOn = ['color_content_primary','button_fontsize','button_height','button_width','button_border_radius','button_letterspacing','button_text_style','button_font','heading_font_family','body_font_family','menu_font_family'];
			this.$buttons = this.$row.find('.usof-button-example');

			for (var fieldId in $usof.instance.fields) {
				if (!$usof.instance.fields.hasOwnProperty(fieldId)) continue;
				if ($.inArray($usof.instance.fields[fieldId].name, this.dependsOn) === -1) continue;
				$usof.instance.fields[fieldId].on('change', function(field, value){
					console.log(field.name);
					console.log(value);
					if (field.name == 'color_content_primary') {
						this.$buttons.css('box-shadow', '0 0 0 2px '+value+' inset');
						this.$buttons.css('background-color', value);
						this.$buttons.css('color', value);
					} else if (field.name == 'button_fontsize') {
						this.$buttons.css('font-size', value);
					} else if (field.name == 'button_height') {
						this.$buttons.css('line-height', value);
					} else if (field.name == 'button_width') {
						this.$buttons.css('padding', '0 '+value+'em');
					} else if (field.name == 'button_border_radius') {
						this.$buttons.css('border-radius', value+'em');
					} else if (field.name == 'button_letterspacing') {
						this.$buttons.css('letter-spacing', value+'px');
					} else if (field.name == 'button_font' || field.name == 'heading_font_family' || field.name == 'body_font_family' || field.name == 'menu_font_family') {
						var fontFamily = $usof.instance.getValue($usof.instance.getValue('button_font')+'_font_family').split('|')[0];
						if (fontFamily == 'none') {
							fontFamily = '';
						}
						this.$buttons.css('font-family', fontFamily);
					} else if (field.name == 'button_text_style') {
						if ($.inArray('bold', value) !== -1) {
							this.$buttons.css('font-weight', 'bold');
						} else {
							this.$buttons.css('font-weight', 'normal');
						}
						if ($.inArray('uppercase', value) !== -1) {
							this.$buttons.css('text-transform', 'uppercase');
						} else {
							this.$buttons.css('text-transform', 'none');
						}

					}
				}.bind(this));
			}

			// Apply possible changes for values outside of Buttons Options tab (if they were changed before tab was first shown)
			var fontFamily = $usof.instance.getValue($usof.instance.getValue('button_font')+'_font_family').split('|')[0];
			if (fontFamily == 'none') {
				fontFamily = '';
			}
			this.$buttons.css('font-family', fontFamily);

			this.$buttons.css('box-shadow', '0 0 0 2px '+$usof.instance.getValue('color_content_primary')+' inset');
			this.$buttons.css('background-color', $usof.instance.getValue('color_content_primary'));
			this.$buttons.css('color', $usof.instance.getValue('color_content_primary'));
		},

	};

	/**
	 * USOF Field: Transfer
	 */
	$usof.field['transfer'] = {

		init: function(){
			this.$textarea = this.$row.find('textarea');
			this.translations = (this.$row.find('.usof-transfer-translations')[0].onclick() || {});
			this.$btnImport = this.$row.find('.usof-button.type_import').on('click', this.importValues.bind(this));

			this.hiddenFieldsValues = $('.usof-hidden-fields')[0].onclick() || {};

			this.exportValues();
			this.on('beforeShow', this.exportValues.bind(this));
		},

		exportValues: function(){
			var values = $.extend(this.hiddenFieldsValues, $usof.instance.getValues());
			this.$textarea.val(JSON.stringify(values));
		},

		importValues: function(){
			var encoded = this.$textarea.val(),
				values;
			try {
				if (encoded.charAt(0) == '{') {
					// New USOF export: json-encoded
					values = JSON.parse(encoded);
				} else {
					// Old SMOF export: base64-encoded
					var serialized = window.atob(encoded),
						matches = serialized.match(/(s\:[0-9]+\:\"(.*?)\"\;)|(i\:[0-9]+\;)/g),
						_key = null,
						_value;
					values = {};
					for (var i = 0; i < matches.length; i++) {
						_value = matches[i].replace((matches[i].charAt(0) == 's') ? /^s\:[0-9]+\:\"(.*?)\"\;$/ : /^i\:([0-9]+)\;$/, '$1');
						if (_key === null) {
							_key = _value;
						} else {
							values[_key] = _value;
							_key = null;
						}
					}
				}
			} catch (error) {
				return alert(this.translations.importError);
			}
			$usof.instance.setValues(values);
			this.valuesChanged = values;
			$usof.instance.save();
		}

	};

	/**
	 * USOF Field: Style Scheme
	 */
	$usof.field['style_scheme'] = {

		init: function(options){
			this.$input = this.$row.find('input[name="'+this.name+'"]');
			this.$schemesContainer = this.$row.find('.usof-schemes-list');
			this.$schemeItems = this.$row.find('.usof-schemes-list > li');
			this.$controls = this.$row.find('.usof-schemes-controls');
			this.$nameInput = this.$row.find('#style_scheme_name');
			this.$saveBtn = this.$row.find('#save_style_scheme').on('click', this.saveScheme.bind(this));

			this.schemes = (this.$row.find('.usof-form-row-control-schemes-json')[0].onclick() || {});
			this.customSchemes = (this.$row.find('.usof-form-row-control-custom-schemes-json')[0].onclick() || {});
			this.colors = (this.$row.find('.usof-form-row-control-colors-json')[0].onclick() || {});
			this.i18n = (this.$row.find('.usof-form-row-control-i18n')[0].onclick() || {});

			this.initSchemes();
		},
		initSchemes: function() {
			this.$schemeItems.each(function(index, item){
				var $item = $(item),
					schemeId = $item.data('id'),
					$deleteBtn = $item.find('.usof-schemes-item-delete'),
					isCustom = $item.hasClass('type_custom'),
					colors;
				$item.on('click', function(){
					if (window.$usof !== undefined && $usof.instance !== undefined) {
						if (this.schemes[schemeId] === undefined || ( ! isCustom && this.schemes[schemeId].values === undefined) || (isCustom && this.customSchemes[schemeId].values === undefined)) return;
						this.$schemeItems.removeClass('active');
						$item.addClass('active');
						if (isCustom) {
							colors = this.customSchemes[schemeId].values;
							this.$input.val('custom-'+schemeId);
							this.trigger('change', 'custom-'+schemeId);
						} else {
							colors = this.schemes[schemeId].values;
							this.$input.val(schemeId);
							this.trigger('change', schemeId);
						}
						$.each(colors, function(id, value){
							$usof.instance.setValue(id, value);
						});

					}
				}.bind(this));
				if ($deleteBtn.length) {
					$deleteBtn.on('click', function(event){
						this.deleteScheme(schemeId, event);
					}.bind(this));
				}
			}.bind(this));
		},
		getColorValues: function(){
			var colors = {};
			if (window.$usof === undefined || $usof.instance == undefined) {
				return undefined;
			}
			if (this.colors == undefined) {
				return undefined;
			}
			$.each(this.colors, function(id, color){
				colors[color] = $usof.instance.getValue(color);
			});

			return colors;
		},
		saveScheme: function(){
			var colors = this.getColorValues(),
				name = this.$nameInput.val(),
				scheme = {name: name, colors: colors},
				$activeScheme = this.$schemeItems.filter('.active');
			if (name == '') {
				if ($activeScheme.hasClass('type_custom')) {
					if (!confirm(this.i18n.create_confirm)) return false;
					scheme.name = $activeScheme.find('.usof-schemes-item-title').html();
					scheme.id = $activeScheme.data('id');
				} else {
					alert(this.i18n.create_error_alert);
					return false;
				}

			}
			this.$controls.addClass('loading');
			$.ajax({
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_save_style_scheme',
					scheme: JSON.stringify(scheme),
					_wpnonce: this.$row.closest('.usof-form').find('[name="_wpnonce"]').val(),
					_wp_http_referer: this.$row.closest('.usof-form').find('[name="_wp_http_referer"]').val()
				},
				success: function(result){
					this.setSchemes(result.data.schemes, result.data.customSchemes, result.data.schemesHtml);
					this.$nameInput.val('');
					this.$controls.removeClass('loading');
				}.bind(this)
			});
			return false;
		},
		deleteScheme: function(schemeId, event){
			event.stopPropagation();
			if (!confirm(this.i18n.delete_confirm)) return false;

			$.ajax({
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_delete_style_scheme',
					scheme: schemeId,
					_wpnonce: this.$row.closest('.usof-form').find('[name="_wpnonce"]').val(),
					_wp_http_referer: this.$row.closest('.usof-form').find('[name="_wp_http_referer"]').val()
				},
				success: function(result){
					this.setSchemes(result.data.schemes, result.data.customSchemes, result.data.schemesHtml);
				}.bind(this)
			});
			return false;
		},
		setSchemes: function(schemes, customSchemes, schemesHtml){
			var $activeScheme = this.$schemeItems.filter('.active'),
				activeSchemeId, isActiveCustom;
			if ($activeScheme.length) {
				activeSchemeId = $activeScheme.data('id');
				isActiveCustom = $activeScheme.hasClass('type_custom');
			}
			this.schemes = schemes;
			this.customSchemes = customSchemes;
			this.$schemesContainer.html(schemesHtml);
			this.$schemeItems = this.$row.find('.usof-schemes-list > li');
			this.initSchemes();

			if (activeSchemeId !== undefined) {
				this.$schemeItems.filter('[data-id="'+activeSchemeId+'"]').each(function(index, item) {
					var $item = $(item);
					if ((isActiveCustom && $item.hasClass('type_custom')) || ( ! isActiveCustom && ! $item.hasClass('type_custom'))) {
						$item.addClass('active');
					}
				});
			}
			// TODO: replace this with actual save of options
			//if (schemeId !== false) {
			//	this.$schemeItems.filter('.type_custom[data-id="'+schemeId+'"]').addClass('active');
			//	this.$input.val('custom-'+schemeId);
			//	this.trigger('change', 'custom-'+schemeId);
			//}
		}

	};

	/**
	 * USOF Field: Upload
	 */
	$usof.field['upload'] = {

		init: function(options){
			this.parentInit(options);
			// Cached URLs for certain values (images IDs and sizes)
			this.$btnSet = this.$row.find('.usof-button.type_set');
			this.$btnRemove = this.$row.find('.usof-button.type_remove');
			this.$previewContainer = this.$row.find('.usof-upload-container');
			this.$previewImg = this.$previewContainer.find('img');
			this.$btnSet.add(this.$row.find('.usof-button.type_change')).on('click', this.openMediaUploader.bind(this));
			this.$btnRemove.on('click', function(){
				this.setValue('');
			}.bind(this));
		},

		setValue: function(value, quiet){
			if (value == '') {
				// Removed value
				this.$previewContainer.hide();
				this.$btnSet.show();
				this.$previewImg.attr('src', '');
			} else {
				if (value.match(/^[0-9]+(\|[a-z_\-0-9]+)?$/)) {
					var attachment = wp.media.attachment(parseInt(value)),
						renderAttachmentImage = function(){
							var src = attachment.attributes.url;
							if (attachment.attributes.sizes !== undefined) {
								var size = (attachment.attributes.sizes.medium !== undefined) ? 'medium' : 'full';
								src = attachment.attributes.sizes[size].url;
							}
							this.$previewImg.attr('src', src);
						}.bind(this);
					if (attachment.attributes.url !== undefined) {
						renderAttachmentImage();
					} else {
						// Loading missing data via ajax
						attachment.fetch({success: renderAttachmentImage});
					}
				} else {
					// Direct image URL (for old SMOF framework compatibility)
					this.$previewImg.attr('src', value);
				}
				this.$previewContainer.show();
				this.$btnSet.hide();
			}
			this.parentSetValue(value, quiet);
		},

		openMediaUploader: function(){
			if (this.frame === undefined) {
				this.frame = wp.media({
					title: this.$btnSet.text(),
					multiple: false,
					library: {type: 'image'},
					button: {text: this.$btnSet.text()}
				});
				this.frame.on('open', function(){
					var value = parseInt(this.getValue());
					if (value) this.frame.state().get('selection').add(wp.media.attachment(value));
				}.bind(this));
				this.frame.on('select', function(){
					var attachment = this.frame.state().get('selection').first();
					this.setValue(attachment.id + '|full');
				}.bind(this));
			}
			this.frame.open();
		}
	};

	/**
	 * Field initialization
	 *
	 * @param options object
	 * @returns {$usof.field}
	 */
	$.fn.usofField = function(options){
		return new $usof.field(this, options);
	};

}(jQuery);


/**
 * USOF Core
 */
!function($){

	$usof.ajaxUrl = $('.usof-container').data('ajaxurl');

	// Prototype mixin for all classes working with fields
	if ($usof.mixins === undefined) $usof.mixins = {};
	$usof.mixins.Fieldset = {
		/**
		 * Initialize fields inside of a container
		 * @param {jQuery} $container
		 */
		initFields: function($container){
			if (this.$fields === undefined) this.$fields = {};
			if (this.fields === undefined) this.fields = {};
			// Showing conditions (fieldId => condition)
			if (this.showIf === undefined) this.showIf = {};
			// Showing dependencies (fieldId => affected field ids)
			if (this.showIfDeps === undefined) this.showIfDeps = {};
			$.each($container.find('.usof-form-row, .usof-form-wrapper'), function(index, elm){
				var $field = $(elm),
					name = $field.data('name'),
					isRow = $field.hasClass('usof-form-row'),
					isToggle = $field.hasClass('type_toggle'),
					$showIf = $field.find(isRow ? '> .usof-form-row-showif' : '> .usof-form-wrapper-cont > .usof-form-wrapper-showif');
				this.$fields[name] = $field;
				if ($showIf.length > 0) {
					this.showIf[name] = $showIf[0].onclick() || [];
					$showIf.remove();
					// Writing dependencies
					var showIfVars = this.getShowIfVariables(this.showIf[name]);
					for (var i = 0; i < showIfVars.length; i++) {
						if (this.showIfDeps[showIfVars[i]] === undefined) this.showIfDeps[showIfVars[i]] = [];
						this.showIfDeps[showIfVars[i]].push(name);
					}
				}
				if (isRow) {
					this.fields[name] = $field.usofField(elm);
				} else if (isToggle) {
					var $title = $field.find('.usof-form-wrapper-title'),
						$content = $field.find('.usof-form-wrapper-cont');
					$content.hide();
					$title.on('click', function(){
						if ($field.hasClass('active')) {
							$content.slideUp();
							$field.removeClass('active');
						} else {
							$content.slideDown();
							$field.addClass('active');
						}
					});
				}
			}.bind(this));
			for (var fieldName in this.showIfDeps) {
				if (!this.showIfDeps.hasOwnProperty(fieldName) || this.fields[fieldName] === undefined) continue;
				this.fields[fieldName].on('change', function(field){
					this.updateVisibility(field.name);
				}.bind(this));
			}
		},
		/**
		 * Show / hide the field based on its showIf condition
		 */
		updateVisibility: function(fieldName){
			$.each(this.showIfDeps[fieldName], function(index, depFieldId){
				// Getting stored value to take animations into account as well
				var isShown = this.$fields[depFieldId].data('isShown'),
					shouldBeShown = this.executeShowIf(this.showIf[depFieldId], this.getValue.bind(this));
				if (isShown === undefined) {
					isShown = (this.$fields[depFieldId].css('display') != 'none');
				}
				if (shouldBeShown && !isShown) {
					this.fireFieldEvent(this.$fields[depFieldId], 'beforeShow');
					this.$fields[depFieldId].stop(true, false).slideDown(function(){
						this.fireFieldEvent(this.$fields[depFieldId], 'afterShow');
					}.bind(this));
					this.$fields[depFieldId].data('isShown', true);
				} else if (!shouldBeShown && isShown) {
					this.fireFieldEvent(this.$fields[depFieldId], 'beforeHide');
					this.$fields[depFieldId].stop(true, false).slideUp(function(){
						this.fireFieldEvent(this.$fields[depFieldId], 'afterHide');
					}.bind(this));
					this.$fields[depFieldId].data('isShown', false);
				}
			}.bind(this));
		},
		/**
		 * Get all field names that affect the given 'show_if' condition
		 * @param {Array} condition
		 * @returns {Array}
		 */
		getShowIfVariables: function(condition){
			if (!$.isArray(condition) || condition.length < 3) {
				return [];
			} else if ($.inArray(condition[1].toLowerCase(), ['and', 'or']) != -1) {
				// Complex or / and statement
				var vars = this.getShowIfVariables(condition[0]),
					index = 2;
				while (condition[index] !== undefined) {
					vars = vars.concat(this.getShowIfVariables(condition[index]));
					index = index + 2;
				}
				return vars;
			} else {
				return [condition[0]];
			}
		},
		/**
		 * Execute 'show_if' condition
		 * @param {Array} condition
		 * @param {Function} getValue Function to get the needed value
		 * @returns {Boolean} Should be shown?
		 */
		executeShowIf: function(condition, getValue){
			var result = true;
			if (!$.isArray(condition) || condition.length < 3) {
				return result;
			} else if ($.inArray(condition[1].toLowerCase(), ['and', 'or']) != -1) {
				// Complex or / and statement
				result = this.executeShowIf(condition[0], getValue);
				var index = 2;
				while (condition[index] !== undefined) {
					condition[index - 1] = condition[index - 1].toLowerCase();
					if (condition[index - 1] == 'and') {
						result = (result && this.executeShowIf(condition[index], getValue));
					} else if (condition[index - 1] == 'or') {
						result = (result || this.executeShowIf(condition[index], getValue));
					}
					index = index + 2;
				}
			} else {
				var value = getValue(condition[0]);
				if (value === undefined) return true;
				if (condition[1] == '=') {
					result = ( value == condition[2] );
				} else if (condition[1] == '!=' || condition[1] == '<>') {
					result = ( value != condition[2] );
				} else if (condition[1] == 'in') {
					result = ( !$.isArray(condition[2]) || $.inArray(value, condition[2]) != -1 );
				} else if (condition[1] == 'not in') {
					result = ( !$.isArray(condition[2]) || $.inArray(value, condition[2]) == -1 );
				} else if (condition[1] == 'has') {
					result = ( !$.isArray(value) || $.inArray(condition[2], value) != -1 );
				} else if (condition[1] == '<=') {
					result = ( value <= condition[2] );
				} else if (condition[1] == '<') {
					result = ( value < condition[2] );
				} else if (condition[1] == '>') {
					result = ( value > condition[2] );
				} else if (condition[1] == '>=') {
					result = ( value >= condition[2] );
				} else {
					result = true;
				}
			}
			return result;
		},
		/**
		 * Find all the fields within $container and fire a certain event there
		 * @param $container jQuery
		 * @param trigger string
		 */
		fireFieldEvent: function($container, trigger){
			var isRow = $container.hasClass('usof-form-row'),
				hideShowEvent = (trigger == 'beforeShow' || trigger == 'afterShow' || trigger == 'beforeHide' || trigger == 'afterHide');
			if (!isRow) {
				$container.find('.usof-form-row').each(function(index, block){
					var $block = $(block),
						isShown = $block.data('isShown');
					if (isShown === undefined) {
						isShown = ($block.css('display') != 'none');
					}
					// The block is not actually shown or hidden in this case
					if (hideShowEvent && !isShown) return;
					$block.data('usofField').trigger(trigger);
				}.bind(this));
			} else {
				$container.data('usofField').trigger(trigger);
			}
		},

		getValue: function(id){
			if (this.fields[id] === undefined) return undefined;
			return this.fields[id].getValue();
		},

		/**
		 * Set some particular field value
		 * @param {String} id
		 * @param {String} value
		 * @param {Boolean} quiet Don't fire onchange events
		 */
		setValue: function(id, value, quiet){
			if (this.fields[id] === undefined) return;
			var shouldFireShow = !this.fields[id].inited;
			if (shouldFireShow) {
				this.fields[id].trigger('beforeShow');
				this.fields[id].trigger('afterShow');
			}
			this.fields[id].setValue(value, quiet);
			if (shouldFireShow) {
				this.fields[id].trigger('beforeHide');
				this.fields[id].trigger('afterHide');
			}
		},

		getValues: function(id){
			var values = {};
			for (var fieldId in this.fields) {
				if (!this.fields.hasOwnProperty(fieldId)) continue;
				values[fieldId] = this.getValue(fieldId);
			}
			return values;
		},

		/**
		 * Set the values
		 * @param {Object} values
		 * @param {Boolean} quiet Don't fire onchange events, just change the interface
		 */
		setValues: function(values, quiet){
			for (var fieldId in values) {
				if (!values.hasOwnProperty(fieldId) || this.fields[fieldId] == undefined) continue;
				this.setValue(fieldId, values[fieldId], quiet);
				if (!quiet) {
					this.fields[fieldId].trigger('change', [values[fieldId]]);
				}
			}
			if (quiet) {
				// Update fields visibility anyway
				for (var fieldName in this.showIfDeps) {
					if (!this.showIfDeps.hasOwnProperty(fieldName) || this.fields[fieldName] === undefined) continue;
					this.updateVisibility(fieldName);
				}
			}
		},
		/**
		 * JavaScript representation of us_prepare_icon_class helper function + removal of wrong symbols
		 * @param {String} iconClass
		 * @returns {String}
		 */
		prepareIconClass: function(iconClass){
			iconClass = iconClass.trim();
			if (iconClass.substr(0, 4) == 'mdfi') {
				// mdfi-toggle-check-box => mdfi_toggle_check_box
				iconClass = iconClass.replace(/\-/g, '_');
			} else if (iconClass.substr(0, 3) == 'fa-') {
				// fa-check => fa fa-check
				iconClass = 'fa ' + iconClass;
			} else {
				// check => fa fa-check
				iconClass = 'fa fa-' + iconClass;
			}
			return iconClass.replace(/[^a-zA-Z0-9\-\_ ]+/g, '');
		}
	};

	var USOF_Meta = function(container){
		this.$container = $(container);
		this.initFields(this.$container);

		this.fireFieldEvent(this.$container, 'beforeShow');
		this.fireFieldEvent(this.$container, 'afterShow');


	};
	$.extend(USOF_Meta.prototype, $usof.mixins.Fieldset, {});

	var USOF = function(container){
		if (window.$usof === undefined) window.$usof = {};
		$usof.instance = this;
		this.$container = $(container);
		this.$title = this.$container.find('.usof-header-title h2');

		this.initFields(this.$container);

		this.active = null;
		this.$sections = {};
		this.$sectionContents = {};
		this.sectionFields = {};
		$.each(this.$container.find('.usof-section'), function(index, section){
			var $section = $(section),
				sectionId = $section.data('id');
			this.$sections[sectionId] = $section;
			this.$sectionContents[sectionId] = $section.find('.usof-section-content');
			if ($section.hasClass('current')) {
				this.active = sectionId;
			}
			this.sectionFields[sectionId] = [];
			$.each($section.find('.usof-form-row'), function(index, row){
				var $row = $(row),
					fieldName = $row.data('name');
				if (fieldName) {
					this.sectionFields[sectionId].push(fieldName);
				}
			}.bind(this));
		}.bind(this));

		this.sectionTitles = {};
		$.each(this.$container.find('.usof-nav-item.level_1'), function(index, item){
			var $item = $(item),
				sectionId = $item.data('id');
			this.sectionTitles[sectionId] = $item.find('.usof-nav-title').html();
		}.bind(this));

		this.navItems = this.$container.find('.usof-nav-item.level_1, .usof-section-header');
		this.navItems.each(function(index, item){
			var $item = $(item),
				sectionId = $item.data('id');
			$item.on('click', function(){
				this.openSection(sectionId);
			}.bind(this));
		}.bind(this));

		// Handling initial document hash
		if (document.location.hash && document.location.hash.indexOf('#!') == -1) {
			this.openSection(document.location.hash.substring(1));
		}

		// Initializing fields at the shown section
		if (this.$sections[this.active] !== undefined) {
			this.fireFieldEvent(this.$sections[this.active], 'beforeShow');
			this.fireFieldEvent(this.$sections[this.active], 'afterShow');
		}

		// Save action
		this.$saveControl = this.$container.find('.usof-control.for_save');
		this.$saveBtn = this.$saveControl.find('.usof-button').on('click', this.save.bind(this));
		this.$saveMessage = this.$saveControl.find('.usof-control-message');
		this.valuesChanged = {};
		this.saveStateTimer = null;
		for (var fieldId in this.fields) {
			if (!this.fields.hasOwnProperty(fieldId)) continue;
			this.fields[fieldId].on('change', function(field, value){
				if ($.isEmptyObject(this.valuesChanged)) {
					clearTimeout(this.saveStateTimer);
					this.$saveControl.usMod('status', 'notsaved');
				}
				this.valuesChanged[field.name] = value;
			}.bind(this));
		}

		this.$window = $(window);
		this.$header = this.$container.find('.usof-header');

		this._events = {
			scroll: this.scroll.bind(this),
			resize: this.resize.bind(this)
		};

		this.resize();
		this.$window.on('resize load', this._events.resize);
		this.$window.on('scroll', this._events.scroll);
	};
	$.extend(USOF.prototype, $usof.mixins.Fieldset, {
		scroll: function(){
			this.$container.toggleClass('footer_fixed', this.$window.scrollTop() > this.headerAreaSize);
		},

		resize: function(){
			if ( ! this.$header.length) return;
			this.headerAreaSize = this.$header.offset().top + this.$header.outerHeight();
			this.scroll();
		},

		openSection: function(sectionId){
			if (sectionId == this.active || this.$sections[sectionId] === undefined) return;
			if (this.$sections[this.active] !== undefined) {
				this.hideSection();
			}
			this.showSection(sectionId);
		},

		showSection: function(sectionId){
			var curItem = this.navItems.filter('[data-id="' + sectionId + '"]');
			curItem.addClass('current');
			this.fireFieldEvent(this.$sectionContents[sectionId], 'beforeShow');
			this.$sectionContents[sectionId].stop(true, false).fadeIn();
			this.$title.html(this.sectionTitles[sectionId]);
			this.fireFieldEvent(this.$sectionContents[sectionId], 'afterShow');
			// Item popup
			var itemPopup = curItem.find('.usof-nav-popup');
			if (itemPopup.length > 0) {
				// Current usof_visited_new_sections cookie
				var matches = document.cookie.match(/(?:^|; )usof_visited_new_sections=([^;]*)/),
					cookieValue = matches ? decodeURIComponent(matches[1]) : '',
					visitedNewSections = (cookieValue == '') ? [] : cookieValue.split(',');
				if (visitedNewSections.indexOf(sectionId) == -1) {
					visitedNewSections.push(sectionId);
					document.cookie = 'usof_visited_new_sections=' + visitedNewSections.join(',')
				}
				itemPopup.remove();
			}
			this.active = sectionId;
		},

		hideSection: function(){
			this.navItems.filter('[data-id="' + this.active + '"]').removeClass('current');
			this.fireFieldEvent(this.$sectionContents[this.active], 'beforeHide');
			this.$sectionContents[this.active].stop(true, false).hide();
			this.$title.html('');
			this.fireFieldEvent(this.$sectionContents[this.active], 'afterHide');
			this.active = null;
		},

		/**
		 * Save the new values
		 */
		save: function(){
			if ($.isEmptyObject(this.valuesChanged)) return;
			clearTimeout(this.saveStateTimer);
			this.$saveMessage.html('');
			this.$saveControl.usMod('status', 'loading');
			$.ajax({
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_save',
					usof_options: JSON.stringify(this.valuesChanged),
					_wpnonce: this.$container.find('[name="_wpnonce"]').val(),
					_wp_http_referer: this.$container.find('[name="_wp_http_referer"]').val()
				},
				success: function(result){
					if (result.success) {
						this.valuesChanged = {};
						this.$saveMessage.html(result.data.message);
						this.$saveControl.usMod('status', 'success');
						this.saveStateTimer = setTimeout(function(){
							this.$saveMessage.html('');
							this.$saveControl.usMod('status', 'clear');
						}.bind(this), 4000);
					} else {
						this.$saveMessage.html(result.data.message);
						this.$saveControl.usMod('status', 'error');
						this.saveStateTimer = setTimeout(function(){
							this.$saveMessage.html('');
							this.$saveControl.usMod('status', 'notsaved');
						}.bind(this), 4000);
					}
				}.bind(this)
			});
		}
	});

	$(function(){
		new USOF('.usof-container');

		$.each($('.usof-metabox'), function(index, item){
			new USOF_Meta(item);
		});
	});
}(jQuery);
