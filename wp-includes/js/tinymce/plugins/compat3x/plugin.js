/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true, console:true */
/*eslint no-console:0, new-cap:0 */

/**
 * This plugin adds missing events form the 4.x API back. Not every event is
 * properly supported but most things should work.
 *
 * Unsupported things:
 *  - No editor.onEvent
 *  - Can't cancel execCommands with beforeExecCommand
 */
(function (tinymce) {
  var reported;

  function noop() {
  }

  function log(apiCall) {
    if (!reported && window && window.console) {
      reported = true;
      console.log("Deprecated TinyMCE API call: " + apiCall);
    }
  }

  function Dispatcher(target, newEventName, argsMap, defaultScope) {
    target = target || this;
    var cbs = [];

    if (!newEventName) {
      this.add = this.addToTop = this.remove = this.dispatch = noop;
      return;
    }

    this.add = function (callback, scope, prepend) {
      log('<target>.on' + newEventName + ".add(..)");

      // Convert callback({arg1:x, arg2:x}) -> callback(arg1, arg2).
      function patchedEventCallback(e) {
        var callbackArgs = [];

        if (typeof argsMap == "string") {
          argsMap = argsMap.split(" ");
        }

        if (argsMap && typeof argsMap !== "function") {
          for (var i = 0; i < argsMap.length; i++) {
            callbackArgs.push(e[argsMap[i]]);
          }
        }

        if (typeof argsMap == "function") {
          callbackArgs = argsMap(newEventName, e, target);
          if (!callbackArgs) {
            return;
          }
        }

        if (!argsMap) {
          callbackArgs = [e];
        }

        callbackArgs.unshift(defaultScope || target);

        if (callback.apply(scope || defaultScope || target, callbackArgs) === false) {
          e.stopImmediatePropagation();
        }
      }

      target.on(newEventName, patchedEventCallback, prepend);

      var handlers = {
        original: callback,
        patched: patchedEventCallback
      };

      cbs.push(handlers);
      return patchedEventCallback;
    };

    this.addToTop = function (callback, scope) {
      this.add(callback, scope, true);
    };

    this.remove = function (callback) {
      cbs.forEach(function (item, i) {
        if (item.original === callback) {
          cbs.splice(i, 1);
          return target.off(newEventName, item.patched);
        }
      });

      return target.off(newEventName, callback);
    };

    this.dispatch = function () {
      target.fire(newEventName);
      return true;
    };
  }

  tinymce.util.Dispatcher = Dispatcher;
  tinymce.onBeforeUnload = new Dispatcher(tinymce, "BeforeUnload");
  tinymce.onAddEditor = new Dispatcher(tinymce, "AddEditor", "editor");
  tinymce.onRemoveEditor = new Dispatcher(tinymce, "RemoveEditor", "editor");

  tinymce.util.Cookie = {
    get: noop, getHash: noop, remove: noop, set: noop, setHash: noop
  };

  function patchEditor(editor) {

    function translate(str) {
      var prefix = editor.settings.language || "en";
      var prefixedStr = [prefix, str].join('.');
      var translatedStr = tinymce.i18n.translate(prefixedStr);

      return prefixedStr !== translatedStr ? translatedStr : tinymce.i18n.translate(str);
    }

    function patchEditorEvents(oldEventNames, argsMap) {
      tinymce.each(oldEventNames.split(" "), function (oldName) {
        editor["on" + oldName] = new Dispatcher(editor, oldName, argsMap);
      });
    }

    function convertUndoEventArgs(type, event, target) {
      return [
        event.level,
        target
      ];
    }

    function filterSelectionEvents(needsSelection) {
      return function (type, e) {
        if ((!e.selection && !needsSelection) || e.selection == needsSelection) {
          return [e];
        }
      };
    }

    if (editor.controlManager) {
      return;
    }

    function cmNoop() {
      var obj = {}, methods = 'add addMenu addSeparator collapse createMenu destroy displayColor expand focus ' +
        'getLength hasMenus hideMenu isActive isCollapsed isDisabled isRendered isSelected mark ' +
        'postRender remove removeAll renderHTML renderMenu renderNode renderTo select selectByIndex ' +
        'setActive setAriaProperty setColor setDisabled setSelected setState showMenu update';

      log('editor.controlManager.*');

      function _noop() {
        return cmNoop();
      }

      tinymce.each(methods.split(' '), function (method) {
        obj[method] = _noop;
      });

      return obj;
    }

    editor.controlManager = {
      buttons: {},

      setDisabled: function (name, state) {
        log("controlManager.setDisabled(..)");

        if (this.buttons[name]) {
          this.buttons[name].disabled(state);
        }
      },

      setActive: function (name, state) {
        log("controlManager.setActive(..)");

        if (this.buttons[name]) {
          this.buttons[name].active(state);
        }
      },

      onAdd: new Dispatcher(),
      onPostRender: new Dispatcher(),

      add: function (obj) {
        return obj;
      },
      createButton: cmNoop,
      createColorSplitButton: cmNoop,
      createControl: cmNoop,
      createDropMenu: cmNoop,
      createListBox: cmNoop,
      createMenuButton: cmNoop,
      createSeparator: cmNoop,
      createSplitButton: cmNoop,
      createToolbar: cmNoop,
      createToolbarGroup: cmNoop,
      destroy: noop,
      get: noop,
      setControlType: cmNoop
    };

    patchEditorEvents("PreInit BeforeRenderUI PostRender Load Init Remove Activate Deactivate", "editor");
    patchEditorEvents("Click MouseUp MouseDown DblClick KeyDown KeyUp KeyPress ContextMenu Paste Submit Reset");
    patchEditorEvents("BeforeExecCommand ExecCommand", "command ui value args"); // args.terminate not supported
    patchEditorEvents("PreProcess PostProcess LoadContent SaveContent Change");
    patchEditorEvents("BeforeSetContent BeforeGetContent SetContent GetContent", filterSelectionEvents(false));
    patchEditorEvents("SetProgressState", "state time");
    patchEditorEvents("VisualAid", "element hasVisual");
    patchEditorEvents("Undo Redo", convertUndoEventArgs);

    patchEditorEvents("NodeChange", function (type, e) {
      return [
        editor.controlManager,
        e.element,
        editor.selection.isCollapsed(),
        e
      ];
    });

    var originalAddButton = editor.addButton;
    editor.addButton = function (name, settings) {
      var originalOnPostRender;

      function patchedPostRender() {
        editor.controlManager.buttons[name] = this;

        if (originalOnPostRender) {
          return originalOnPostRender.apply(this, arguments);
        }
      }

      for (var key in settings) {
        if (key.toLowerCase() === "onpostrender") {
          originalOnPostRender = settings[key];
          settings.onPostRender = patchedPostRender;
        }
      }

      if (!originalOnPostRender) {
        settings.onPostRender = patchedPostRender;
      }

      if (settings.title) {
        settings.title = translate(settings.title);
      }

      return originalAddButton.call(this, name, settings);
    };

    editor.on('init', function () {
      var undoManager = editor.undoManager, selection = editor.selection;

      undoManager.onUndo = new Dispatcher(editor, "Undo", convertUndoEventArgs, null, undoManager);
      undoManager.onRedo = new Dispatcher(editor, "Redo", convertUndoEventArgs, null, undoManager);
      undoManager.onBeforeAdd = new Dispatcher(editor, "BeforeAddUndo", null, undoManager);
      undoManager.onAdd = new Dispatcher(editor, "AddUndo", null, undoManager);

      selection.onBeforeGetContent = new Dispatcher(editor, "BeforeGetContent", filterSelectionEvents(true), selection);
      selection.onGetContent = new Dispatcher(editor, "GetContent", filterSelectionEvents(true), selection);
      selection.onBeforeSetContent = new Dispatcher(editor, "BeforeSetContent", filterSelectionEvents(true), selection);
      selection.onSetContent = new Dispatcher(editor, "SetContent", filterSelectionEvents(true), selection);
    });

    editor.on('BeforeRenderUI', function () {
      var windowManager = editor.windowManager;

      windowManager.onOpen = new Dispatcher();
      windowManager.onClose = new Dispatcher();
      windowManager.createInstance = function (className, a, b, c, d, e) {
        log("windowManager.createInstance(..)");

        var constr = tinymce.resolve(className);
        return new constr(a, b, c, d, e);
      };
    });
  }

  tinymce.on('SetupEditor', function (e) {
    patchEditor(e.editor);
  });

  tinymce.PluginManager.add("compat3x", patchEditor);

  tinymce.addI18n = function (prefix, o) {
    var I18n = tinymce.util.I18n, each = tinymce.each;

    if (typeof prefix == "string" && prefix.indexOf('.') === -1) {
      I18n.add(prefix, o);
      return;
    }

    if (!tinymce.is(prefix, 'string')) {
      each(prefix, function (o, lc) {
        each(o, function (o, g) {
          each(o, function (o, k) {
            if (g === 'common') {
              I18n.data[lc + '.' + k] = o;
            } else {
              I18n.data[lc + '.' + g + '.' + k] = o;
            }
          });
        });
      });
    } else {
      each(o, function (o, k) {
        I18n.data[prefix + '.' + k] = o;
      });
    }
  };
})(tinymce);
