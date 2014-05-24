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

		html += '</tbody></table>';

		return html;
	}

	function onPanelClick(e) {
		var buttonCtrl = this.parent(), value;

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

	editor.addButton('forecolor', {
		type: 'colorbutton',
		tooltip: 'Text color',
		selectcmd: 'ForeColor',
		panel: {
			role: 'application',
			ariaRemember: true,
			html: renderColorPicker,
			onclick: onPanelClick
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
			onclick: onPanelClick
		},
		onclick: onButtonClick
	});
});
