(function () {
var hr = (function () {
  'use strict';

  var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

  var register = function (editor) {
    editor.addCommand('InsertHorizontalRule', function () {
      editor.execCommand('mceInsertContent', false, '<hr />');
    });
  };
  var $_598wgdc0jfuw8p00 = { register: register };

  var register$1 = function (editor) {
    editor.addButton('hr', {
      icon: 'hr',
      tooltip: 'Horizontal line',
      cmd: 'InsertHorizontalRule'
    });
    editor.addMenuItem('hr', {
      icon: 'hr',
      text: 'Horizontal line',
      cmd: 'InsertHorizontalRule',
      context: 'insert'
    });
  };
  var $_7oq7jyc1jfuw8p02 = { register: register$1 };

  global.add('hr', function (editor) {
    $_598wgdc0jfuw8p00.register(editor);
    $_7oq7jyc1jfuw8p02.register(editor);
  });
  function Plugin () {
  }

  return Plugin;

}());
})();
