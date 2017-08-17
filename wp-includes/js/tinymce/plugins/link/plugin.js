(function () {

var defs = {}; // id -> {dependencies, definition, instance (possibly undefined)}

// Used when there is no 'main' module.
// The name is probably (hopefully) unique so minification removes for releases.
var register_3795 = function (id) {
  var module = dem(id);
  var fragments = id.split('.');
  var target = Function('return this;')();
  for (var i = 0; i < fragments.length - 1; ++i) {
    if (target[fragments[i]] === undefined)
      target[fragments[i]] = {};
    target = target[fragments[i]];
  }
  target[fragments[fragments.length - 1]] = module;
};

var instantiate = function (id) {
  var actual = defs[id];
  var dependencies = actual.deps;
  var definition = actual.defn;
  var len = dependencies.length;
  var instances = new Array(len);
  for (var i = 0; i < len; ++i)
    instances[i] = dem(dependencies[i]);
  var defResult = definition.apply(null, instances);
  if (defResult === undefined)
     throw 'module [' + id + '] returned undefined';
  actual.instance = defResult;
};

var def = function (id, dependencies, definition) {
  if (typeof id !== 'string')
    throw 'module id must be a string';
  else if (dependencies === undefined)
    throw 'no dependencies for ' + id;
  else if (definition === undefined)
    throw 'no definition function for ' + id;
  defs[id] = {
    deps: dependencies,
    defn: definition,
    instance: undefined
  };
};

var dem = function (id) {
  var actual = defs[id];
  if (actual === undefined)
    throw 'module [' + id + '] was undefined';
  else if (actual.instance === undefined)
    instantiate(id);
  return actual.instance;
};

var req = function (ids, callback) {
  var len = ids.length;
  var instances = new Array(len);
  for (var i = 0; i < len; ++i)
    instances.push(dem(ids[i]));
  callback.apply(null, callback);
};

var ephox = {};

ephox.bolt = {
  module: {
    api: {
      define: def,
      require: req,
      demand: dem
    }
  }
};

var define = def;
var require = req;
var demand = dem;
// this helps with minificiation when using a lot of global references
var defineGlobal = function (id, ref) {
  define(id, [], function () { return ref; });
};
/*jsc
["tinymce.plugins.link.Plugin","tinymce.core.PluginManager","tinymce.plugins.link.core.Actions","tinymce.plugins.link.ui.Controls","global!tinymce.util.Tools.resolve","tinymce.core.util.VK","tinymce.plugins.link.ui.Dialog","tinymce.plugins.link.core.OpenUrl","tinymce.plugins.link.core.Utils","tinymce.plugins.link.core.Settings","tinymce.core.util.Delay","tinymce.core.util.Tools","tinymce.core.util.XHR","global!RegExp","tinymce.core.dom.DOMUtils","tinymce.core.Env"]
jsc*/
defineGlobal("global!tinymce.util.Tools.resolve", tinymce.util.Tools.resolve);
/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.PluginManager',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.PluginManager');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.util.VK',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.VK');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.util.Delay',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Delay');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.util.Tools',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Tools');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.util.XHR',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.XHR');
  }
);

define(
  'tinymce.plugins.link.core.Settings',
  [

  ],
  function () {
    var assumeExternalTargets = function (editorSettings) {
      return typeof editorSettings.link_assume_external_targets === 'boolean' ? editorSettings.link_assume_external_targets : false;
    };

    var hasContextToolbar = function (editorSettings) {
      return typeof editorSettings.link_context_toolbar === 'boolean' ? editorSettings.link_context_toolbar : false;
    };

    var getLinkList = function (editorSettings) {
      return editorSettings.link_list;
    };

    var hasDefaultLinkTarget = function (editorSettings) {
      return typeof editorSettings.default_link_target === 'string';
    };

    var getDefaultLinkTarget = function (editorSettings) {
      return editorSettings.default_link_target;
    };

    var getTargetList = function (editorSettings) {
      return editorSettings.target_list;
    };

    var setTargetList = function (editor, list) {
      editor.settings.target_list = list;
    };

    var shouldShowTargetList = function (editorSettings) {
      return getTargetList(editorSettings) !== false;
    };

    var getRelList = function (editorSettings) {
      return editorSettings.rel_list;
    };

    var hasRelList = function (editorSettings) {
      return getRelList(editorSettings) !== undefined;
    };

    var getLinkClassList = function (editorSettings) {
      return editorSettings.link_class_list;
    };

    var hasLinkClassList = function (editorSettings) {
      return getLinkClassList(editorSettings) !== undefined;
    };

    var shouldShowLinkTitle = function (editorSettings) {
      return editorSettings.link_title !== false;
    };

    var allowUnsafeLinkTarget = function (editorSettings) {
      return typeof editorSettings.allow_unsafe_link_target === 'boolean' ? editorSettings.allow_unsafe_link_target : false;
    };

    return {
      assumeExternalTargets: assumeExternalTargets,
      hasContextToolbar: hasContextToolbar,
      getLinkList: getLinkList,
      hasDefaultLinkTarget: hasDefaultLinkTarget,
      getDefaultLinkTarget: getDefaultLinkTarget,
      getTargetList: getTargetList,
      setTargetList: setTargetList,
      shouldShowTargetList: shouldShowTargetList,
      getRelList: getRelList,
      hasRelList: hasRelList,
      getLinkClassList: getLinkClassList,
      hasLinkClassList: hasLinkClassList,
      shouldShowLinkTitle: shouldShowLinkTitle,
      allowUnsafeLinkTarget: allowUnsafeLinkTarget
    };
  }
);

defineGlobal("global!RegExp", RegExp);
/**
 * Utils.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.link.core.Utils',
  [
    'tinymce.core.util.Tools',
    'tinymce.plugins.link.core.Settings',
    'global!RegExp'
  ],
  function (Tools, Settings, RegExp) {

    var toggleTargetRules = function (rel, isUnsafe) {
      var rules = 'noopener';

      var addTargetRules = function (rel) {
        rel = removeTargetRules(rel);
        return rel ? [rel, rules].join(' ') : rules;
      };

      var removeTargetRules = function (rel) {
        var regExp = new RegExp('(' + rules.replace(' ', '|') + ')', 'g');
        if (rel) {
          rel = Tools.trim(rel.replace(regExp, ''));
        }
        return rel ? rel : null;
      };

      return isUnsafe ? addTargetRules(rel) : removeTargetRules(rel);
    };


    var trimCaretContainers = function (text) {
      return text.replace(/\uFEFF/g, '');
    };


    var getAnchorElement = function (editor, selectedElm) {
      selectedElm = selectedElm || editor.selection.getStart();
      if (isImageFigure(selectedElm)) {
        // for an image conained in a figure we look for a link inside the selected element
        return editor.dom.select('a[href]', selectedElm)[0];
      } else {
        return editor.dom.getParent(selectedElm, 'a[href]');
      }
    };


    var getAnchorText = function (selection, anchorElm) {
      var text = anchorElm ? (anchorElm.innerText || anchorElm.textContent) : selection.getContent({ format: 'text' });
      return trimCaretContainers(text);
    };


    var isLink = function (elm) {
      return elm && elm.nodeName === 'A' && elm.href;
    };

    var hasLinks = function (elements) {
      return Tools.grep(elements, isLink).length > 0;
    };


    var isOnlyTextSelected = function (html) {
      // Partial html and not a fully selected anchor element
      if (/</.test(html) && (!/^<a [^>]+>[^<]+<\/a>$/.test(html) || html.indexOf('href=') == -1)) {
        return false;
      }

      return true;
    };


    var isImageFigure = function (node) {
      return node && node.nodeName === 'FIGURE' && /\bimage\b/i.test(node.className);
    };


    var link = function (editor, attachState) {
      return function (data) {
        editor.undoManager.transact(function () {
          var selectedElm = editor.selection.getNode();
          var anchorElm = getAnchorElement(editor, selectedElm);

          var linkAttrs = {
            href: data.href,
            target: data.target ? data.target : null,
            rel: data.rel ? data.rel : null,
            "class": data["class"] ? data["class"] : null,
            title: data.title ? data.title : null
          };

          if (Settings.allowUnsafeLinkTarget(editor.settings) === false) {
            linkAttrs.rel = toggleTargetRules(linkAttrs.rel, linkAttrs.target == '_blank');
          }

          if (data.href === attachState.href) {
            attachState.attach();
            attachState = {};
          }

          if (anchorElm) {
            editor.focus();

            if (data.hasOwnProperty('text')) {
              if ("innerText" in anchorElm) {
                anchorElm.innerText = data.text;
              } else {
                anchorElm.textContent = data.text;
              }
            }

            editor.dom.setAttribs(anchorElm, linkAttrs);

            editor.selection.select(anchorElm);
            editor.undoManager.add();
          } else {
            if (isImageFigure(selectedElm)) {
              linkImageFigure(editor, selectedElm, linkAttrs);
            } else if (data.hasOwnProperty('text')) {
              editor.insertContent(editor.dom.createHTML('a', linkAttrs, editor.dom.encode(data.text)));
            } else {
              editor.execCommand('mceInsertLink', false, linkAttrs);
            }
          }
        });
      };
    };


    var unlink = function (editor) {
      return function () {
        editor.undoManager.transact(function () {
          var node = editor.selection.getNode();
          if (isImageFigure(node)) {
            unlinkImageFigure(editor, node);
          } else {
            editor.execCommand('unlink');
          }
        });
      };
    };


    var unlinkImageFigure = function (editor, fig) {
      var a, img;
      img = editor.dom.select('img', fig)[0];
      if (img) {
        a = editor.dom.getParents(img, 'a[href]', fig)[0];
        if (a) {
          a.parentNode.insertBefore(img, a);
          editor.dom.remove(a);
        }
      }
    };


    var linkImageFigure = function (editor, fig, attrs) {
      var a, img;
      img = editor.dom.select('img', fig)[0];
      if (img) {
        a = editor.dom.create('a', attrs);
        img.parentNode.insertBefore(a, img);
        a.appendChild(img);
      }
    };

    return {
      link: link,
      unlink: unlink,
      isLink: isLink,
      hasLinks: hasLinks,
      isOnlyTextSelected: isOnlyTextSelected,
      getAnchorElement: getAnchorElement,
      getAnchorText: getAnchorText
    };
  }
);
/**
 * Dialog.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.link.ui.Dialog',
  [
    'tinymce.core.util.Delay',
    'tinymce.core.util.Tools',
    'tinymce.core.util.XHR',
    'tinymce.plugins.link.core.Utils',
    'tinymce.plugins.link.core.Settings'
  ],
  function (Delay, Tools, XHR, Utils, Settings) {
    var attachState = {};

    var createLinkList = function (editor, callback) {
      var linkList = Settings.getLinkList(editor.settings);

      if (typeof linkList == "string") {
        XHR.send({
          url: linkList,
          success: function (text) {
            callback(editor, JSON.parse(text));
          }
        });
      } else if (typeof linkList == "function") {
        linkList(function (list) {
          callback(editor, list);
        });
      } else {
        callback(editor, linkList);
      }
    };

    var buildListItems = function (inputList, itemCallback, startItems) {
      var appendItems = function (values, output) {
        output = output || [];

        Tools.each(values, function (item) {
          var menuItem = { text: item.text || item.title };

          if (item.menu) {
            menuItem.menu = appendItems(item.menu);
          } else {
            menuItem.value = item.value;

            if (itemCallback) {
              itemCallback(menuItem);
            }
          }

          output.push(menuItem);
        });

        return output;
      };

      return appendItems(inputList, startItems || []);
    };

    // Delay confirm since onSubmit will move focus
    var delayedConfirm = function (editor, message, callback) {
      var rng = editor.selection.getRng();

      Delay.setEditorTimeout(editor, function () {
        editor.windowManager.confirm(message, function (state) {
          editor.selection.setRng(rng);
          callback(state);
        });
      });
    };

    var showDialog = function (editor, linkList) {
      var data = {}, selection = editor.selection, dom = editor.dom, anchorElm, initialText;
      var win, onlyText, textListCtrl, linkListCtrl, relListCtrl, targetListCtrl, classListCtrl, linkTitleCtrl, value;

      var linkListChangeHandler = function (e) {
        var textCtrl = win.find('#text');

        if (!textCtrl.value() || (e.lastControl && textCtrl.value() == e.lastControl.text())) {
          textCtrl.value(e.control.text());
        }

        win.find('#href').value(e.control.value());
      };

      var buildAnchorListControl = function (url) {
        var anchorList = [];

        Tools.each(editor.dom.select('a:not([href])'), function (anchor) {
          var id = anchor.name || anchor.id;

          if (id) {
            anchorList.push({
              text: id,
              value: '#' + id,
              selected: url.indexOf('#' + id) != -1
            });
          }
        });

        if (anchorList.length) {
          anchorList.unshift({ text: 'None', value: '' });

          return {
            name: 'anchor',
            type: 'listbox',
            label: 'Anchors',
            values: anchorList,
            onselect: linkListChangeHandler
          };
        }
      };

      var updateText = function () {
        if (!initialText && onlyText && !data.text) {
          this.parent().parent().find('#text')[0].value(this.value());
        }
      };

      var urlChange = function (e) {
        var meta = e.meta || {};

        if (linkListCtrl) {
          linkListCtrl.value(editor.convertURL(this.value(), 'href'));
        }

        Tools.each(e.meta, function (value, key) {
          var inp = win.find('#' + key);

          if (key === 'text') {
            if (initialText.length === 0) {
              inp.value(value);
              data.text = value;
            }
          } else {
            inp.value(value);
          }
        });

        if (meta.attach) {
          attachState = {
            href: this.value(),
            attach: meta.attach
          };
        }

        if (!meta.text) {
          updateText.call(this);
        }
      };

      var onBeforeCall = function (e) {
        e.meta = win.toJSON();
      };

      onlyText = Utils.isOnlyTextSelected(selection.getContent());
      anchorElm = Utils.getAnchorElement(editor);

      data.text = initialText = Utils.getAnchorText(editor.selection, anchorElm);
      data.href = anchorElm ? dom.getAttrib(anchorElm, 'href') : '';

      if (anchorElm) {
        data.target = dom.getAttrib(anchorElm, 'target');
      } else if (Settings.hasDefaultLinkTarget(editor.settings)) {
        data.target = Settings.getDefaultLinkTarget(editor.settings);
      }

      if ((value = dom.getAttrib(anchorElm, 'rel'))) {
        data.rel = value;
      }

      if ((value = dom.getAttrib(anchorElm, 'class'))) {
        data['class'] = value;
      }

      if ((value = dom.getAttrib(anchorElm, 'title'))) {
        data.title = value;
      }

      if (onlyText) {
        textListCtrl = {
          name: 'text',
          type: 'textbox',
          size: 40,
          label: 'Text to display',
          onchange: function () {
            data.text = this.value();
          }
        };
      }

      if (linkList) {
        linkListCtrl = {
          type: 'listbox',
          label: 'Link list',
          values: buildListItems(
            linkList,
            function (item) {
              item.value = editor.convertURL(item.value || item.url, 'href');
            },
            [{ text: 'None', value: '' }]
          ),
          onselect: linkListChangeHandler,
          value: editor.convertURL(data.href, 'href'),
          onPostRender: function () {
            /*eslint consistent-this:0*/
            linkListCtrl = this;
          }
        };
      }

      if (Settings.shouldShowTargetList(editor.settings)) {
        if (Settings.getTargetList(editor.settings) === undefined) {
          Settings.setTargetList(editor, [
            { text: 'None', value: '' },
            { text: 'New window', value: '_blank' }
          ]);
        }

        targetListCtrl = {
          name: 'target',
          type: 'listbox',
          label: 'Target',
          values: buildListItems(Settings.getTargetList(editor.settings))
        };
      }

      if (Settings.hasRelList(editor.settings)) {
        relListCtrl = {
          name: 'rel',
          type: 'listbox',
          label: 'Rel',
          values: buildListItems(Settings.getRelList(editor.settings))
        };
      }

      if (Settings.hasLinkClassList(editor.settings)) {
        classListCtrl = {
          name: 'class',
          type: 'listbox',
          label: 'Class',
          values: buildListItems(
            Settings.getLinkClassList(editor.settings),
            function (item) {
              if (item.value) {
                item.textStyle = function () {
                  return editor.formatter.getCssText({ inline: 'a', classes: [item.value] });
                };
              }
            }
          )
        };
      }

      if (Settings.shouldShowLinkTitle(editor.settings)) {
        linkTitleCtrl = {
          name: 'title',
          type: 'textbox',
          label: 'Title',
          value: data.title
        };
      }

      win = editor.windowManager.open({
        title: 'Insert link',
        data: data,
        body: [
          {
            name: 'href',
            type: 'filepicker',
            filetype: 'file',
            size: 40,
            autofocus: true,
            label: 'Url',
            onchange: urlChange,
            onkeyup: updateText,
            onbeforecall: onBeforeCall
          },
          textListCtrl,
          linkTitleCtrl,
          buildAnchorListControl(data.href),
          linkListCtrl,
          relListCtrl,
          targetListCtrl,
          classListCtrl
        ],
        onSubmit: function (e) {
          var assumeExternalTargets = Settings.assumeExternalTargets(editor.settings);
          var insertLink = Utils.link(editor, attachState);
          var removeLink = Utils.unlink(editor);

          var resultData = Tools.extend({}, data, e.data);
          /*eslint dot-notation: 0*/
          var href = resultData.href;

          if (!href) {
            removeLink();
            return;
          }

          if (!onlyText || resultData.text === initialText) {
            delete resultData.text;
          }

          // Is email and not //user@domain.com
          if (href.indexOf('@') > 0 && href.indexOf('//') == -1 && href.indexOf('mailto:') == -1) {
            delayedConfirm(
              editor,
              'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?',
              function (state) {
                if (state) {
                  resultData.href = 'mailto:' + href;
                }
                insertLink(resultData);
              }
            );
            return;
          }

          // Is not protocol prefixed
          if ((assumeExternalTargets === true && !/^\w+:/i.test(href)) ||
            (assumeExternalTargets === false && /^\s*www[\.|\d\.]/i.test(href))) {
            delayedConfirm(
              editor,
              'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?',
              function (state) {
                if (state) {
                  resultData.href = 'http://' + href;
                }
                insertLink(resultData);
              }
            );
            return;
          }

          insertLink(resultData);
        }
      });
    };

    var open = function (editor) {
      createLinkList(editor, showDialog);
    };

    return {
      open: open
    };
  }
);
/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.dom.DOMUtils',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.dom.DOMUtils');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.Env',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.Env');
  }
);

/**
 * OpenUrl.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.link.core.OpenUrl',
  [
    'tinymce.core.dom.DOMUtils',
    'tinymce.core.Env'
  ],
  function (DOMUtils, Env) {
    var appendClickRemove = function (link, evt) {
      document.body.appendChild(link);
      link.dispatchEvent(evt);
      document.body.removeChild(link);
    };

    var open = function (url) {
      // Chrome and Webkit has implemented noopener and works correctly with/without popup blocker
      // Firefox has it implemented noopener but when the popup blocker is activated it doesn't work
      // Edge has only implemented noreferrer and it seems to remove opener as well
      // Older IE versions pre IE 11 falls back to a window.open approach
      if (!Env.ie || Env.ie > 10) {
        var link = document.createElement('a');
        link.target = '_blank';
        link.href = url;
        link.rel = 'noreferrer noopener';

        var evt = document.createEvent('MouseEvents');
        evt.initMouseEvent('click', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);

        appendClickRemove(link, evt);
      } else {
        var win = window.open('', '_blank');
        if (win) {
          win.opener = null;
          var doc = win.document;
          doc.open();
          doc.write('<meta http-equiv="refresh" content="0; url=' + DOMUtils.DOM.encode(url) + '">');
          doc.close();
        }
      }
    };

    return {
      open: open
    };
  }
);
/**
 * Actions.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.link.core.Actions',
  [
    'tinymce.core.util.VK',
    'tinymce.plugins.link.ui.Dialog',
    'tinymce.plugins.link.core.OpenUrl',
    'tinymce.plugins.link.core.Utils',
    'tinymce.plugins.link.core.Settings'
  ],
  function (VK, Dialog, OpenUrl, Utils, Settings) {
    var getLink = function (editor, elm) {
      return editor.dom.getParent(elm, 'a[href]');
    };

    var getSelectedLink = function (editor) {
      return getLink(editor, editor.selection.getStart());
    };

    var getHref = function (elm) {
      // Returns the real href value not the resolved a.href value
      var href = elm.getAttribute('data-mce-href');
      return href ? href : elm.getAttribute('href');
    };

    var isContextMenuVisible = function (editor) {
      var contextmenu = editor.plugins.contextmenu;
      return contextmenu ? contextmenu.isContextMenuVisible() : false;
    };

    var hasOnlyAltModifier = function (e) {
      return e.altKey === true && e.shiftKey === false && e.ctrlKey === false && e.metaKey === false;
    };

    var gotoLink = function (editor, a) {
      if (a) {
        var href = getHref(a);
        if (/^#/.test(href)) {
          var targetEl = editor.$(href);
          if (targetEl.length) {
            editor.selection.scrollIntoView(targetEl[0], true);
          }
        } else {
          OpenUrl.open(a.href);
        }
      }
    };

    var openDialog = function (editor) {
      return function () {
        Dialog.open(editor);
      };
    };

    var gotoSelectedLink = function (editor) {
      return function () {
        gotoLink(editor, getSelectedLink(editor));
      };
    };

    var leftClickedOnAHref = function (editor) {
      return function (elm) {
        var sel, rng, node;
        if (Settings.hasContextToolbar(editor.settings) && !isContextMenuVisible(editor) && Utils.isLink(elm)) {
          sel = editor.selection;
          rng = sel.getRng();
          node = rng.startContainer;
          // ignore cursor positions at the beginning/end (to make context toolbar less noisy)
          if (node.nodeType == 3 && sel.isCollapsed() && rng.startOffset > 0 && rng.startOffset < node.data.length) {
            return true;
          }
        }
        return false;
      };
    };

    var setupGotoLinks = function (editor) {
      editor.on('click', function (e) {
        var link = getLink(editor, e.target);
        if (link && VK.metaKeyPressed(e)) {
          e.preventDefault();
          gotoLink(editor, link);
        }
      });

      editor.on('keydown', function (e) {
        var link = getSelectedLink(editor);
        if (link && e.keyCode === 13 && hasOnlyAltModifier(e)) {
          e.preventDefault();
          gotoLink(editor, link);
        }
      });
    };

    var toggleActiveState = function (editor) {
      return function () {
        var self = this;
        editor.on('nodechange', function (e) {
          self.active(!editor.readonly && !!Utils.getAnchorElement(editor, e.element));
        });
      };
    };

    var toggleViewLinkState = function (editor) {
      return function () {
        var self = this;

        var toggleVisibility = function (e) {
          if (Utils.hasLinks(e.parents)) {
            self.show();
          } else {
            self.hide();
          }
        };

        if (!Utils.hasLinks(editor.dom.getParents(editor.selection.getStart()))) {
          self.hide();
        }

        editor.on('nodechange', toggleVisibility);

        self.on('remove', function () {
          editor.off('nodechange', toggleVisibility);
        });
      };
    };

    return {
      openDialog: openDialog,
      gotoSelectedLink: gotoSelectedLink,
      leftClickedOnAHref: leftClickedOnAHref,
      setupGotoLinks: setupGotoLinks,
      toggleActiveState: toggleActiveState,
      toggleViewLinkState: toggleViewLinkState
    };
  }
);
/**
 * Controls.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.link.ui.Controls',
  [
    'tinymce.plugins.link.core.Actions',
    'tinymce.plugins.link.core.Utils'
  ],
  function (Actions, Utils) {

    var setupButtons = function (editor) {
      editor.addButton('link', {
        icon: 'link',
        tooltip: 'Insert/edit link',
        shortcut: 'Meta+K',
        onclick: Actions.openDialog(editor),
        onpostrender: Actions.toggleActiveState(editor)
      });

      editor.addButton('unlink', {
        icon: 'unlink',
        tooltip: 'Remove link',
        onclick: Utils.unlink(editor),
        onpostrender: Actions.toggleActiveState(editor)
      });

      if (editor.addContextToolbar) {
        editor.addButton('openlink', {
          icon: 'newtab',
          tooltip: 'Open link',
          onclick: Actions.gotoSelectedLink(editor)
        });
      }
    };

    var setupMenuItems = function (editor) {
      editor.addMenuItem('openlink', {
        text: 'Open link',
        icon: 'newtab',
        onclick: Actions.gotoSelectedLink(editor),
        onPostRender: Actions.toggleViewLinkState(editor),
        prependToContext: true
      });

      editor.addMenuItem('link', {
        icon: 'link',
        text: 'Link',
        shortcut: 'Meta+K',
        onclick: Actions.openDialog(editor),
        stateSelector: 'a[href]',
        context: 'insert',
        prependToContext: true
      });
    };

    var setupContextToolbars = function (editor) {
      if (editor.addContextToolbar) {
        editor.addContextToolbar(
          Actions.leftClickedOnAHref(editor),
          'openlink | link unlink'
        );
      }
    };

    return {
      setupButtons: setupButtons,
      setupMenuItems: setupMenuItems,
      setupContextToolbars: setupContextToolbars
    };
  }
);
/**
 * Plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.link.Plugin',
  [
    'tinymce.core.PluginManager',
    'tinymce.plugins.link.core.Actions',
    'tinymce.plugins.link.ui.Controls'
  ],
  function (PluginManager, Actions, Controls) {
    PluginManager.add('link', function (editor) {
      Controls.setupButtons(editor);
      Controls.setupMenuItems(editor);
      Controls.setupContextToolbars(editor);
      Actions.setupGotoLinks(editor);
      editor.addShortcut('Meta+K', '', Actions.openDialog(editor));
      editor.addCommand('mceLink', Actions.openDialog(editor));
    });

    return function () { };
  }
);
dem('tinymce.plugins.link.Plugin')();
})();
