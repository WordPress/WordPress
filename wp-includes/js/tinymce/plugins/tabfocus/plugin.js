(function () {
var tabfocus = (function () {
  'use strict';

  var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

  var global$1 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

  var global$2 = tinymce.util.Tools.resolve('tinymce.EditorManager');

  var global$3 = tinymce.util.Tools.resolve('tinymce.Env');

  var global$4 = tinymce.util.Tools.resolve('tinymce.util.Delay');

  var global$5 = tinymce.util.Tools.resolve('tinymce.util.Tools');

  var global$6 = tinymce.util.Tools.resolve('tinymce.util.VK');

  var getTabFocusElements = function (editor) {
    return editor.getParam('tabfocus_elements', ':prev,:next');
  };
  var getTabFocus = function (editor) {
    return editor.getParam('tab_focus', getTabFocusElements(editor));
  };
  var $_8rita4kwjjgwed4m = { getTabFocus: getTabFocus };

  var DOM = global$1.DOM;
  var tabCancel = function (e) {
    if (e.keyCode === global$6.TAB && !e.ctrlKey && !e.altKey && !e.metaKey) {
      e.preventDefault();
    }
  };
  var setup = function (editor) {
    function tabHandler(e) {
      var x, el, v, i;
      if (e.keyCode !== global$6.TAB || e.ctrlKey || e.altKey || e.metaKey || e.isDefaultPrevented()) {
        return;
      }
      function find(direction) {
        el = DOM.select(':input:enabled,*[tabindex]:not(iframe)');
        function canSelectRecursive(e) {
          return e.nodeName === 'BODY' || e.type !== 'hidden' && e.style.display !== 'none' && e.style.visibility !== 'hidden' && canSelectRecursive(e.parentNode);
        }
        function canSelect(el) {
          return /INPUT|TEXTAREA|BUTTON/.test(el.tagName) && global$2.get(e.id) && el.tabIndex !== -1 && canSelectRecursive(el);
        }
        global$5.each(el, function (e, i) {
          if (e.id === editor.id) {
            x = i;
            return false;
          }
        });
        if (direction > 0) {
          for (i = x + 1; i < el.length; i++) {
            if (canSelect(el[i])) {
              return el[i];
            }
          }
        } else {
          for (i = x - 1; i >= 0; i--) {
            if (canSelect(el[i])) {
              return el[i];
            }
          }
        }
        return null;
      }
      v = global$5.explode($_8rita4kwjjgwed4m.getTabFocus(editor));
      if (v.length === 1) {
        v[1] = v[0];
        v[0] = ':prev';
      }
      if (e.shiftKey) {
        if (v[0] === ':prev') {
          el = find(-1);
        } else {
          el = DOM.get(v[0]);
        }
      } else {
        if (v[1] === ':next') {
          el = find(1);
        } else {
          el = DOM.get(v[1]);
        }
      }
      if (el) {
        var focusEditor = global$2.get(el.id || el.name);
        if (el.id && focusEditor) {
          focusEditor.focus();
        } else {
          global$4.setTimeout(function () {
            if (!global$3.webkit) {
              window.focus();
            }
            el.focus();
          }, 10);
        }
        e.preventDefault();
      }
    }
    editor.on('init', function () {
      if (editor.inline) {
        DOM.setAttrib(editor.getBody(), 'tabIndex', null);
      }
      editor.on('keyup', tabCancel);
      if (global$3.gecko) {
        editor.on('keypress keydown', tabHandler);
      } else {
        editor.on('keydown', tabHandler);
      }
    });
  };
  var $_6zogdykpjjgwed4h = { setup: setup };

  global.add('tabfocus', function (editor) {
    $_6zogdykpjjgwed4h.setup(editor);
  });
  function Plugin () {
  }

  return Plugin;

}());
})();
