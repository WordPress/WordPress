(function () {
var fullscreen = (function () {
  'use strict';

  var Cell = function (initial) {
    var value = initial;
    var get = function () {
      return value;
    };
    var set = function (v) {
      value = v;
    };
    var clone = function () {
      return Cell(get());
    };
    return {
      get: get,
      set: set,
      clone: clone
    };
  };

  var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

  var get = function (fullscreenState) {
    return {
      isFullscreen: function () {
        return fullscreenState.get() !== null;
      }
    };
  };
  var $_2d5qbdbsjfuw8ozh = { get: get };

  var global$1 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

  var fireFullscreenStateChanged = function (editor, state) {
    editor.fire('FullscreenStateChanged', { state: state });
  };
  var $_6m5hhtbwjfuw8ozm = { fireFullscreenStateChanged: fireFullscreenStateChanged };

  var DOM = global$1.DOM;
  var getWindowSize = function () {
    var w;
    var h;
    var win = window;
    var doc = document;
    var body = doc.body;
    if (body.offsetWidth) {
      w = body.offsetWidth;
      h = body.offsetHeight;
    }
    if (win.innerWidth && win.innerHeight) {
      w = win.innerWidth;
      h = win.innerHeight;
    }
    return {
      w: w,
      h: h
    };
  };
  var getScrollPos = function () {
    var vp = DOM.getViewPort();
    return {
      x: vp.x,
      y: vp.y
    };
  };
  var setScrollPos = function (pos) {
    window.scrollTo(pos.x, pos.y);
  };
  var toggleFullscreen = function (editor, fullscreenState) {
    var body = document.body;
    var documentElement = document.documentElement;
    var editorContainerStyle;
    var editorContainer, iframe, iframeStyle;
    var fullscreenInfo = fullscreenState.get();
    var resize = function () {
      DOM.setStyle(iframe, 'height', getWindowSize().h - (editorContainer.clientHeight - iframe.clientHeight));
    };
    var removeResize = function () {
      DOM.unbind(window, 'resize', resize);
    };
    editorContainer = editor.getContainer();
    editorContainerStyle = editorContainer.style;
    iframe = editor.getContentAreaContainer().firstChild;
    iframeStyle = iframe.style;
    if (!fullscreenInfo) {
      var newFullScreenInfo = {
        scrollPos: getScrollPos(),
        containerWidth: editorContainerStyle.width,
        containerHeight: editorContainerStyle.height,
        iframeWidth: iframeStyle.width,
        iframeHeight: iframeStyle.height,
        resizeHandler: resize,
        removeHandler: removeResize
      };
      iframeStyle.width = iframeStyle.height = '100%';
      editorContainerStyle.width = editorContainerStyle.height = '';
      DOM.addClass(body, 'mce-fullscreen');
      DOM.addClass(documentElement, 'mce-fullscreen');
      DOM.addClass(editorContainer, 'mce-fullscreen');
      DOM.bind(window, 'resize', resize);
      editor.on('remove', removeResize);
      resize();
      fullscreenState.set(newFullScreenInfo);
      $_6m5hhtbwjfuw8ozm.fireFullscreenStateChanged(editor, true);
    } else {
      iframeStyle.width = fullscreenInfo.iframeWidth;
      iframeStyle.height = fullscreenInfo.iframeHeight;
      if (fullscreenInfo.containerWidth) {
        editorContainerStyle.width = fullscreenInfo.containerWidth;
      }
      if (fullscreenInfo.containerHeight) {
        editorContainerStyle.height = fullscreenInfo.containerHeight;
      }
      DOM.removeClass(body, 'mce-fullscreen');
      DOM.removeClass(documentElement, 'mce-fullscreen');
      DOM.removeClass(editorContainer, 'mce-fullscreen');
      setScrollPos(fullscreenInfo.scrollPos);
      DOM.unbind(window, 'resize', fullscreenInfo.resizeHandler);
      editor.off('remove', fullscreenInfo.removeHandler);
      fullscreenState.set(null);
      $_6m5hhtbwjfuw8ozm.fireFullscreenStateChanged(editor, false);
    }
  };
  var $_e74zxhbujfuw8ozj = { toggleFullscreen: toggleFullscreen };

  var register = function (editor, fullscreenState) {
    editor.addCommand('mceFullScreen', function () {
      $_e74zxhbujfuw8ozj.toggleFullscreen(editor, fullscreenState);
    });
  };
  var $_j53v2btjfuw8ozi = { register: register };

  var postRender = function (editor) {
    return function (e) {
      var ctrl = e.control;
      editor.on('FullscreenStateChanged', function (e) {
        ctrl.active(e.state);
      });
    };
  };
  var register$1 = function (editor) {
    editor.addMenuItem('fullscreen', {
      text: 'Fullscreen',
      shortcut: 'Ctrl+Shift+F',
      selectable: true,
      cmd: 'mceFullScreen',
      onPostRender: postRender(editor),
      context: 'view'
    });
    editor.addButton('fullscreen', {
      active: false,
      tooltip: 'Fullscreen',
      cmd: 'mceFullScreen',
      onPostRender: postRender(editor)
    });
  };
  var $_3ge8trbxjfuw8ozn = { register: register$1 };

  global.add('fullscreen', function (editor) {
    var fullscreenState = Cell(null);
    if (editor.settings.inline) {
      return $_2d5qbdbsjfuw8ozh.get(fullscreenState);
    }
    $_j53v2btjfuw8ozi.register(editor, fullscreenState);
    $_3ge8trbxjfuw8ozn.register(editor);
    editor.addShortcut('Ctrl+Shift+F', '', 'mceFullScreen');
    return $_2d5qbdbsjfuw8ozh.get(fullscreenState);
  });
  function Plugin () {
  }

  return Plugin;

}());
})();
