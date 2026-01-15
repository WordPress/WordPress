(function () {
var colorpicker = (function () {
    'use strict';

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.util.Color');

    var showPreview = function (win, hexColor) {
      win.find('#preview')[0].getEl().style.background = hexColor;
    };
    var setColor = function (win, value) {
      var color = global$1(value), rgb = color.toRgb();
      win.fromJSON({
        r: rgb.r,
        g: rgb.g,
        b: rgb.b,
        hex: color.toHex().substr(1)
      });
      showPreview(win, color.toHex());
    };
    var open = function (editor, callback, value) {
      var win = editor.windowManager.open({
        title: 'Color',
        items: {
          type: 'container',
          layout: 'flex',
          direction: 'row',
          align: 'stretch',
          padding: 5,
          spacing: 10,
          items: [
            {
              type: 'colorpicker',
              value: value,
              onchange: function () {
                var rgb = this.rgb();
                if (win) {
                  win.find('#r').value(rgb.r);
                  win.find('#g').value(rgb.g);
                  win.find('#b').value(rgb.b);
                  win.find('#hex').value(this.value().substr(1));
                  showPreview(win, this.value());
                }
              }
            },
            {
              type: 'form',
              padding: 0,
              labelGap: 5,
              defaults: {
                type: 'textbox',
                size: 7,
                value: '0',
                flex: 1,
                spellcheck: false,
                onchange: function () {
                  var colorPickerCtrl = win.find('colorpicker')[0];
                  var name, value;
                  name = this.name();
                  value = this.value();
                  if (name === 'hex') {
                    value = '#' + value;
                    setColor(win, value);
                    colorPickerCtrl.value(value);
                    return;
                  }
                  value = {
                    r: win.find('#r').value(),
                    g: win.find('#g').value(),
                    b: win.find('#b').value()
                  };
                  colorPickerCtrl.value(value);
                  setColor(win, value);
                }
              },
              items: [
                {
                  name: 'r',
                  label: 'R',
                  autofocus: 1
                },
                {
                  name: 'g',
                  label: 'G'
                },
                {
                  name: 'b',
                  label: 'B'
                },
                {
                  name: 'hex',
                  label: '#',
                  value: '000000'
                },
                {
                  name: 'preview',
                  type: 'container',
                  border: 1
                }
              ]
            }
          ]
        },
        onSubmit: function () {
          callback('#' + win.toJSON().hex);
        }
      });
      setColor(win, value);
    };
    var Dialog = { open: open };

    global.add('colorpicker', function (editor) {
      if (!editor.settings.color_picker_callback) {
        editor.settings.color_picker_callback = function (callback, value) {
          Dialog.open(editor, callback, value);
        };
      }
    });
    function Plugin () {
    }

    return Plugin;

}());
})();
