/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */
/*eslint consistent-this:0 */

tinymce.PluginManager.add('textcolor', function(editor) {
	var VK = tinymce.util.VK;

	function mapColors() {
		var i, colors = [], colorMap;

		colorMap = editor.settings.textcolor_map || [
			"000000", "Black",
			"993300", "Burnt orange",
			"333300", "Dark olive",
			"003300", "Dark green",
			"003366", "Dark azure",
			"000080", "Navy Blue",
			"333399", "Indigo",
			"333333", "Very dark gray",
			"800000", "Maroon",
			"FF6600", "Orange",
			"808000", "Olive",
			"008000", "Green",
			"008080", "Teal",
			"0000FF", "Blue",
			"666699", "Grayish blue",
			"808080", "Gray",
			"FF0000", "Red",
			"FF9900", "Amber",
			"99CC00", "Yellow green",
			"339966", "Sea green",
			"33CCCC", "Turquoise",
			"3366FF", "Royal blue",
			"800080", "Purple",
			"999999", "Medium gray",
			"FF00FF", "Magenta",
			"FFCC00", "Gold",
			"FFFF00", "Yellow",
			"00FF00", "Lime",
			"00FFFF", "Aqua",
			"00CCFF", "Sky blue",
			"993366", "Red violet",
			"C0C0C0", "Silver",
			"FF99CC", "Pink",
			"FFCC99", "Peach",
			"FFFF99", "Light yellow",
			"CCFFCC", "Pale green",
			"CCFFFF", "Pale cyan",
			"99CCFF", "Light sky blue",
			"CC99FF", "Plum",
			"FFFFFF", "White"
		];

		for (i = 0; i < colorMap.length; i += 2) {
			colors.push({
				text: colorMap[i + 1],
				color: colorMap[i]
			});
		}

		return colors;
	}

	function renderColorPicker() {
		var ctrl = this, colors, color, html, last, rows, cols, x, y, i;

		colors = mapColors();

		html = '<table class="mce-grid mce-grid-border mce-colorbutton-grid" role="list" cellspacing="0"><tbody>';
		last = colors.length - 1;
		rows = editor.settings.textcolor_rows || 5;
		cols = editor.settings.textcolor_cols || 8;

		for (y = 0; y < rows; y++) {
			html += '<tr>';

			for (x = 0; x < cols; x++) {
				i = y * cols + x;

				if (i > last) {
					html += '<td></td>';
				} else {
					color = colors[i];
					html += (
						'<td>' +
							'<div id="' + ctrl._id + '-' + i + '"' +
								' data-mce-color="' + color.color + '"' +
								' role="option"' +
								' tabIndex="-1"' +
								' style="' + (color ? 'background-color: #' + color.color : '') + '"' +
								' title="' + color.text + '">' +
							'</div>' +
						'</td>'
					);
				}
			}

			html += '</tr>';
		}

		if (editor.settings.textcolor_enable_hex) {
			var hexIdN = last + 1;
			var hexInputColSpan = cols - 1;
			html += (
				'<tr>' +
					'<td>' +
						'<div id="' + ctrl._id + '-' + hexIdN + '"' +
							'data-mce-color=""' +
							'style="background-color: #FFFFFF"' +
							'data-mce-hex-picker="true"' +
							'role="option" ' +
							'>' +
						'</div>' +
					'</td>' +
					'<td colspan="' + hexInputColSpan + '">' +
						'# <input type="text" class="mce-textcolor-hexpicker"' +
						'role="textbox" name="mce-hexcolorpicker"' +
						'id="' + ctrl._id + '-hexcolorpicker" maxlength="6" >' +
					'</td>' +
				'</tr>'
			);
		}

		html += '</tbody></table>';

		return html;
	}

	function onPanelClick(e) {
		var buttonCtrl = this.parent(), value;
		
		if (e.target.getAttribute('disabled')) {
			return;
		}
		if ((value = e.target.getAttribute('data-mce-color'))) {
			if (this.lastId) {
				document.getElementById(this.lastId).setAttribute('aria-selected', false);
			}

			e.target.setAttribute('aria-selected', true);
			this.lastId = e.target.id;

			buttonCtrl.hidePanel();
			value = '#' + value;
			buttonCtrl.color(value);
			editor.execCommand(buttonCtrl.settings.selectcmd, false, value);
		}
	}

	function onButtonClick() {
		var self = this;

		if (self._color) {
			editor.execCommand(self.settings.selectcmd, false, self._color);
		}
	}

	/**
	 * isValidHex checks if the provided string is valid hex color string
	 *
	 * @param  {string}   hexString 3 or 6 chars string representing a color.
	 * @return {Boolean}  [true]  the string is valid hex color
	 *                    [false] the string is not valid hex color        
	 */
	function isValidHex(hexString) {
		return /(^[0-9A-F]{3,6}$)/i.test(hexString);
	}

	/**
	 * isSpecialStroke checks if the keyCode is currently a special one:
	 *  backspace, delete, arrow keys (left/right)
	 *  or if it's a special ctrl+x/c/v
	 *
	 * @param  {string}  keyCode 
	 * @return {Boolean}  
	 */
	function isSpecialStroke(e) {
		var keyCode = e.keyCode;
		// Allow delete and backspace
		if (keyCode === VK.BACKSPACE || keyCode === VK.DELETE ) {
			return true;
		}

		// Allow arrow movements
		if (keyCode === VK.LEFT || keyCode === VK.RIGHT) {
			return true;
		}

		// Allow CTRL/CMD + C/V/X
		if ((tinymce.isMac ? e.metaKey : e.ctrlKey) && (keyCode == 67 || keyCode == 88 || keyCode == 86)) {
			return true;
		}

		return false;
	}

	function initHexPicker(e) {
		if (!editor.settings.textcolor_enable_hex) {
			return;
		}

		var wrapper = document.querySelector('#' + e.target._id);
		var input = wrapper.querySelector('[name="mce-hexcolorpicker"]');
		var hexcolorDiv = wrapper.querySelector('[data-mce-hex-picker]');
		var inputEvent = 'input';

		editor.dom.events.bind(input, 'keydown', function(e){
			var keyCode = e.keyCode;

			if (isSpecialStroke(e)) {
				return;
			}

			// Look for anything which is not A-Z or 0-9 and it is not a special char.
			if (!((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 70) || (keyCode >= 96 && keyCode <= 105)) ) {
				e.preventDefault();
			}

			// On Enter, take it like a click on the hexcolorDiv
			if ( (keyCode === VK.ENTER && isValidHex(input.value) ) ) {
				hexcolorDiv.click();
			}

		});

		// If IE8 we can't use the input event, so we have to
		// listen for keypress and paste events.
		// In IE9 the input implementation is buggy so
		// we use the same events as we'd like on IE8
		if (tinymce.Env.ie && tinymce.Env.ie <= 9) {
			inputEvent = 'keypress paste blur keydown keyup propertychange';
		}
		
		editor.dom.events.bind(input, inputEvent, function(){
			if (isValidHex(input.value)) {
				hexcolorDiv.setAttribute('data-mce-color', input.value);
				hexcolorDiv.setAttribute('style', 'background-color:#' + input.value);
				hexcolorDiv.removeAttribute('disabled');
			} else {
				hexcolorDiv.setAttribute('disabled', 'disabled');
			}
			
		});

	}

	editor.addButton('forecolor', {
		type: 'colorbutton',
		tooltip: 'Text color',
		selectcmd: 'ForeColor',
		panel: {
			role: 'application',
			ariaRemember: true,
			html: renderColorPicker,
			onclick: onPanelClick,
			onPostRender: initHexPicker
		},
		onclick: onButtonClick
	});

	editor.addButton('backcolor', {
		type: 'colorbutton',
		tooltip: 'Background color',
		selectcmd: 'HiliteColor',
		panel: {
			role: 'application',
			ariaRemember: true,
			html: renderColorPicker,
			onclick: onPanelClick,
			onPostRender: initHexPicker
		},
		onclick: onButtonClick
	});
});
