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
["tinymce.plugins.image.Plugin","tinymce.core.PluginManager","tinymce.core.util.Tools","tinymce.plugins.image.ui.Dialog","global!tinymce.util.Tools.resolve","global!document","global!Math","global!RegExp","tinymce.core.Env","tinymce.core.ui.Factory","tinymce.core.util.JSON","tinymce.core.util.XHR","tinymce.plugins.image.core.Uploader","tinymce.plugins.image.core.Utils","tinymce.core.util.Promise"]
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
  'tinymce.core.util.Tools',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Tools');
  }
);

defineGlobal("global!document", document);
defineGlobal("global!Math", Math);
defineGlobal("global!RegExp", RegExp);
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
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.ui.Factory',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.ui.Factory');
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
  'tinymce.core.util.JSON',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.JSON');
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
  'tinymce.core.util.Promise',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Promise');
  }
);

/**
 * Uploader.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This is basically cut down version of tinymce.core.file.Uploader, which we could use directly
 * if it wasn't marked as private.
 *
 * @class tinymce.image.core.Uploader
 * @private
 */
define(
  'tinymce.plugins.image.core.Uploader',
  [
    'tinymce.core.util.Promise',
    'tinymce.core.util.Tools',
    'global!document'
  ],
  function (Promise, Tools, document) {
    return function (settings) {
      var noop = function () {};

      function pathJoin(path1, path2) {
        if (path1) {
          return path1.replace(/\/$/, '') + '/' + path2.replace(/^\//, '');
        }

        return path2;
      }

      function defaultHandler(blobInfo, success, failure, progress) {
        var xhr, formData;

        xhr = new XMLHttpRequest();
        xhr.open('POST', settings.url);
        xhr.withCredentials = settings.credentials;

        xhr.upload.onprogress = function (e) {
          progress(e.loaded / e.total * 100);
        };

        xhr.onerror = function () {
          failure("Image upload failed due to a XHR Transport error. Code: " + xhr.status);
        };

        xhr.onload = function () {
          var json;

          if (xhr.status < 200 || xhr.status >= 300) {
            failure("HTTP Error: " + xhr.status);
            return;
          }

          json = JSON.parse(xhr.responseText);

          if (!json || typeof json.location != "string") {
            failure("Invalid JSON: " + xhr.responseText);
            return;
          }

          success(pathJoin(settings.basePath, json.location));
        };

        formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        xhr.send(formData);
      }

      function uploadBlob(blobInfo, handler) {
        return new Promise(function (resolve, reject) {
          try {
            handler(blobInfo, resolve, reject, noop);
          } catch (ex) {
            reject(ex.message);
          }
        });
      }

      function isDefaultHandler(handler) {
        return handler === defaultHandler;
      }

      function upload(blobInfo) {
        return (!settings.url && isDefaultHandler(settings.handler)) ? Promise.reject("Upload url missng from the settings.") : uploadBlob(blobInfo, settings.handler);
      }

      settings = Tools.extend({
        credentials: false,
        handler: defaultHandler
      }, settings);

      return {
        upload: upload
      };
    };
  }
);
/**
 * Utils.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * @class tinymce.image.core.Utils
 * @private
 */
define(
  'tinymce.plugins.image.core.Utils',
  [
    'tinymce.core.util.Tools',
    'global!Math',
    'global!document'
  ],
  function (Tools, Math, document) {

    var getImageSize = function (url, callback) {
      var img = document.createElement('img');

      function done(width, height) {
        if (img.parentNode) {
          img.parentNode.removeChild(img);
        }

        callback({ width: width, height: height });
      }

      img.onload = function () {
        done(Math.max(img.width, img.clientWidth), Math.max(img.height, img.clientHeight));
      };

      img.onerror = function () {
        done();
      };

      var style = img.style;
      style.visibility = 'hidden';
      style.position = 'fixed';
      style.bottom = style.left = 0;
      style.width = style.height = 'auto';

      document.body.appendChild(img);
      img.src = url;
    };


    var buildListItems = function (inputList, itemCallback, startItems) {
      function appendItems(values, output) {
        output = output || [];

        Tools.each(values, function (item) {
          var menuItem = { text: item.text || item.title };

          if (item.menu) {
            menuItem.menu = appendItems(item.menu);
          } else {
            menuItem.value = item.value;
            itemCallback(menuItem);
          }

          output.push(menuItem);
        });

        return output;
      }

      return appendItems(inputList, startItems || []);
    };

    var removePixelSuffix = function (value) {
      if (value) {
        value = value.replace(/px$/, '');
      }
      return value;
    };

    var addPixelSuffix = function (value) {
      if (value.length > 0 && /^[0-9]+$/.test(value)) {
        value += 'px';
      }
      return value;
    };

    var mergeMargins = function (css) {
      if (css.margin) {

        var splitMargin = css.margin.split(" ");

        switch (splitMargin.length) {
          case 1: //margin: toprightbottomleft;
            css['margin-top'] = css['margin-top'] || splitMargin[0];
            css['margin-right'] = css['margin-right'] || splitMargin[0];
            css['margin-bottom'] = css['margin-bottom'] || splitMargin[0];
            css['margin-left'] = css['margin-left'] || splitMargin[0];
            break;
          case 2: //margin: topbottom rightleft;
            css['margin-top'] = css['margin-top'] || splitMargin[0];
            css['margin-right'] = css['margin-right'] || splitMargin[1];
            css['margin-bottom'] = css['margin-bottom'] || splitMargin[0];
            css['margin-left'] = css['margin-left'] || splitMargin[1];
            break;
          case 3: //margin: top rightleft bottom;
            css['margin-top'] = css['margin-top'] || splitMargin[0];
            css['margin-right'] = css['margin-right'] || splitMargin[1];
            css['margin-bottom'] = css['margin-bottom'] || splitMargin[2];
            css['margin-left'] = css['margin-left'] || splitMargin[1];
            break;
          case 4: //margin: top right bottom left;
            css['margin-top'] = css['margin-top'] || splitMargin[0];
            css['margin-right'] = css['margin-right'] || splitMargin[1];
            css['margin-bottom'] = css['margin-bottom'] || splitMargin[2];
            css['margin-left'] = css['margin-left'] || splitMargin[3];
        }
        delete css.margin;
      }
      return css;
    };

    return {
      getImageSize: getImageSize,
      buildListItems: buildListItems,
      removePixelSuffix: removePixelSuffix,
      addPixelSuffix: addPixelSuffix,
      mergeMargins: mergeMargins
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

/**
 * @class tinymce.image.ui.Dialog
 * @private
 */
define(
  'tinymce.plugins.image.ui.Dialog',
  [
    'global!document',
    'global!Math',
    'global!RegExp',
    'tinymce.core.Env',
    'tinymce.core.ui.Factory',
    'tinymce.core.util.JSON',
    'tinymce.core.util.Tools',
    'tinymce.core.util.XHR',
    'tinymce.plugins.image.core.Uploader',
    'tinymce.plugins.image.core.Utils'
  ],
  function (document, Math, RegExp, Env, Factory, JSON, Tools, XHR, Uploader, Utils) {

    return function (editor) {
      function createImageList(callback) {
        var imageList = editor.settings.image_list;

        if (typeof imageList == "string") {
          XHR.send({
            url: imageList,
            success: function (text) {
              callback(JSON.parse(text));
            }
          });
        } else if (typeof imageList == "function") {
          imageList(callback);
        } else {
          callback(imageList);
        }
      }

      function showDialog(imageList) {
        var win, data = {}, imgElm, figureElm, dom = editor.dom, settings = editor.settings;
        var width, height, imageListCtrl, classListCtrl, imageDimensions = settings.image_dimensions !== false;


        function onFileInput() {
          var Throbber = Factory.get('Throbber');
          var throbber = new Throbber(win.getEl());
          var file = this.value();

          var uploader = new Uploader({
            url: settings.images_upload_url,
            basePath: settings.images_upload_base_path,
            credentials: settings.images_upload_credentials,
            handler: settings.images_upload_handler
          });

          // we do not need to add this to editors blobCache, so we fake bare minimum
          var blobInfo = editor.editorUpload.blobCache.create({
            blob: file,
            name: file.name ? file.name.replace(/\.[^\.]+$/, '') : null, // strip extension
            base64: 'data:image/fake;base64,=' // without this create() will throw exception
          });

          var finalize = function () {
            throbber.hide();
            URL.revokeObjectURL(blobInfo.blobUri()); // in theory we could fake blobUri too, but until it's legitimate, we have too revoke it manually
          };

          throbber.show();

          return uploader.upload(blobInfo).then(function (url) {
            var src = win.find('#src');
            src.value(url);
            win.find('tabpanel')[0].activateTab(0); // switch to General tab
            src.fire('change'); // this will invoke onSrcChange (and any other handlers, if any).
            finalize();
            return url;
          }, function (err) {
            editor.windowManager.alert(err);
            finalize();
          });
        }

        function isTextBlock(node) {
          return editor.schema.getTextBlockElements()[node.nodeName];
        }

        function recalcSize() {
          var widthCtrl, heightCtrl, newWidth, newHeight;

          widthCtrl = win.find('#width')[0];
          heightCtrl = win.find('#height')[0];

          if (!widthCtrl || !heightCtrl) {
            return;
          }

          newWidth = widthCtrl.value();
          newHeight = heightCtrl.value();

          if (win.find('#constrain')[0].checked() && width && height && newWidth && newHeight) {
            if (width != newWidth) {
              newHeight = Math.round((newWidth / width) * newHeight);

              if (!isNaN(newHeight)) {
                heightCtrl.value(newHeight);
              }
            } else {
              newWidth = Math.round((newHeight / height) * newWidth);

              if (!isNaN(newWidth)) {
                widthCtrl.value(newWidth);
              }
            }
          }

          width = newWidth;
          height = newHeight;
        }

        function updateStyle() {
          if (!editor.settings.image_advtab) {
            return;
          }

          var data = win.toJSON(),
            css = dom.parseStyle(data.style);

          css = Utils.mergeMargins(css);

          if (data.vspace) {
            css['margin-top'] = css['margin-bottom'] = Utils.addPixelSuffix(data.vspace);
          }
          if (data.hspace) {
            css['margin-left'] = css['margin-right'] = Utils.addPixelSuffix(data.hspace);
          }
          if (data.border) {
            css['border-width'] = Utils.addPixelSuffix(data.border);
          }

          win.find('#style').value(dom.serializeStyle(dom.parseStyle(dom.serializeStyle(css))));
        }

        function updateVSpaceHSpaceBorder() {
          if (!editor.settings.image_advtab) {
            return;
          }

          var data = win.toJSON(),
            css = dom.parseStyle(data.style);

          win.find('#vspace').value("");
          win.find('#hspace').value("");

          css = Utils.mergeMargins(css);

          //Move opposite equal margins to vspace/hspace field
          if ((css['margin-top'] && css['margin-bottom']) || (css['margin-right'] && css['margin-left'])) {
            if (css['margin-top'] === css['margin-bottom']) {
              win.find('#vspace').value(Utils.removePixelSuffix(css['margin-top']));
            } else {
              win.find('#vspace').value('');
            }
            if (css['margin-right'] === css['margin-left']) {
              win.find('#hspace').value(Utils.removePixelSuffix(css['margin-right']));
            } else {
              win.find('#hspace').value('');
            }
          }

          //Move border-width
          if (css['border-width']) {
            win.find('#border').value(Utils.removePixelSuffix(css['border-width']));
          }

          win.find('#style').value(dom.serializeStyle(dom.parseStyle(dom.serializeStyle(css))));
        }

        function waitLoad(imgElm) {
          function selectImage() {
            imgElm.onload = imgElm.onerror = null;

            if (editor.selection) {
              editor.selection.select(imgElm);
              editor.nodeChanged();
            }
          }

          imgElm.onload = function () {
            if (!data.width && !data.height && imageDimensions) {
              dom.setAttribs(imgElm, {
                width: imgElm.clientWidth,
                height: imgElm.clientHeight
              });
            }

            selectImage();
          };

          imgElm.onerror = selectImage;
        }

        function onSubmitForm() {
          var figureElm, oldImg;

          updateStyle();
          recalcSize();

          data = Tools.extend(data, win.toJSON());

          if (!data.alt) {
            data.alt = '';
          }

          if (!data.title) {
            data.title = '';
          }

          if (data.width === '') {
            data.width = null;
          }

          if (data.height === '') {
            data.height = null;
          }

          if (!data.style) {
            data.style = null;
          }

          // Setup new data excluding style properties
          /*eslint dot-notation: 0*/
          data = {
            src: data.src,
            alt: data.alt,
            title: data.title,
            width: data.width,
            height: data.height,
            style: data.style,
            caption: data.caption,
            "class": data["class"]
          };

          editor.undoManager.transact(function () {
            if (!data.src) {
              if (imgElm) {
                var elm = dom.is(imgElm.parentNode, 'figure.image') ? imgElm.parentNode : imgElm;
                dom.remove(elm);
                editor.focus();
                editor.nodeChanged();

                if (dom.isEmpty(editor.getBody())) {
                  editor.setContent('');
                  editor.selection.setCursorLocation();
                }
              }

              return;
            }

            if (data.title === "") {
              data.title = null;
            }

            if (!imgElm) {
              data.id = '__mcenew';
              editor.focus();
              editor.selection.setContent(dom.createHTML('img', data));
              imgElm = dom.get('__mcenew');
              dom.setAttrib(imgElm, 'id', null);
            } else {
              dom.setAttribs(imgElm, data);
            }

            editor.editorUpload.uploadImagesAuto();

            if (data.caption === false) {
              if (dom.is(imgElm.parentNode, 'figure.image')) {
                figureElm = imgElm.parentNode;
                dom.setAttrib(imgElm, 'contenteditable', null);
                dom.insertAfter(imgElm, figureElm);
                dom.remove(figureElm);
                editor.selection.select(imgElm);
                editor.nodeChanged();
              }
            }

            if (data.caption === true) {
              if (!dom.is(imgElm.parentNode, 'figure.image')) {
                oldImg = imgElm;
                imgElm = imgElm.cloneNode(true);
                imgElm.contentEditable = true;
                figureElm = dom.create('figure', { 'class': 'image' });
                figureElm.appendChild(imgElm);
                figureElm.appendChild(dom.create('figcaption', { contentEditable: true }, 'Caption'));
                figureElm.contentEditable = false;

                var textBlock = dom.getParent(oldImg, isTextBlock);
                if (textBlock) {
                  dom.split(textBlock, oldImg, figureElm);
                } else {
                  dom.replace(figureElm, oldImg);
                }

                editor.selection.select(figureElm);
              }

              return;
            }

            waitLoad(imgElm);
          });
        }

        function onSrcChange(e) {
          var srcURL, prependURL, absoluteURLPattern, meta = e.meta || {};

          if (imageListCtrl) {
            imageListCtrl.value(editor.convertURL(this.value(), 'src'));
          }

          Tools.each(meta, function (value, key) {
            win.find('#' + key).value(value);
          });

          if (!meta.width && !meta.height) {
            srcURL = editor.convertURL(this.value(), 'src');

            // Pattern test the src url and make sure we haven't already prepended the url
            prependURL = editor.settings.image_prepend_url;
            absoluteURLPattern = new RegExp('^(?:[a-z]+:)?//', 'i');
            if (prependURL && !absoluteURLPattern.test(srcURL) && srcURL.substring(0, prependURL.length) !== prependURL) {
              srcURL = prependURL + srcURL;
            }

            this.value(srcURL);

            Utils.getImageSize(editor.documentBaseURI.toAbsolute(this.value()), function (data) {
              if (data.width && data.height && imageDimensions) {
                width = data.width;
                height = data.height;

                win.find('#width').value(width);
                win.find('#height').value(height);
              }
            });
          }
        }

        function onBeforeCall(e) {
          e.meta = win.toJSON();
        }

        imgElm = editor.selection.getNode();
        figureElm = dom.getParent(imgElm, 'figure.image');
        if (figureElm) {
          imgElm = dom.select('img', figureElm)[0];
        }

        if (imgElm &&
          (imgElm.nodeName != 'IMG' ||
            imgElm.getAttribute('data-mce-object') ||
            imgElm.getAttribute('data-mce-placeholder'))) {
          imgElm = null;
        }

        if (imgElm) {
          width = dom.getAttrib(imgElm, 'width');
          height = dom.getAttrib(imgElm, 'height');

          data = {
            src: dom.getAttrib(imgElm, 'src'),
            alt: dom.getAttrib(imgElm, 'alt'),
            title: dom.getAttrib(imgElm, 'title'),
            "class": dom.getAttrib(imgElm, 'class'),
            width: width,
            height: height,
            caption: !!figureElm
          };
        }

        if (imageList) {
          imageListCtrl = {
            type: 'listbox',
            label: 'Image list',
            values: Utils.buildListItems(
              imageList,
              function (item) {
                item.value = editor.convertURL(item.value || item.url, 'src');
              },
              [{ text: 'None', value: '' }]
            ),
            value: data.src && editor.convertURL(data.src, 'src'),
            onselect: function (e) {
              var altCtrl = win.find('#alt');

              if (!altCtrl.value() || (e.lastControl && altCtrl.value() == e.lastControl.text())) {
                altCtrl.value(e.control.text());
              }

              win.find('#src').value(e.control.value()).fire('change');
            },
            onPostRender: function () {
              /*eslint consistent-this: 0*/
              imageListCtrl = this;
            }
          };
        }

        if (editor.settings.image_class_list) {
          classListCtrl = {
            name: 'class',
            type: 'listbox',
            label: 'Class',
            values: Utils.buildListItems(
              editor.settings.image_class_list,
              function (item) {
                if (item.value) {
                  item.textStyle = function () {
                    return editor.formatter.getCssText({ inline: 'img', classes: [item.value] });
                  };
                }
              }
            )
          };
        }

        // General settings shared between simple and advanced dialogs
        var generalFormItems = [
          {
            name: 'src',
            type: 'filepicker',
            filetype: 'image',
            label: 'Source',
            autofocus: true,
            onchange: onSrcChange,
            onbeforecall: onBeforeCall
          },
          imageListCtrl
        ];

        if (editor.settings.image_description !== false) {
          generalFormItems.push({ name: 'alt', type: 'textbox', label: 'Image description' });
        }

        if (editor.settings.image_title) {
          generalFormItems.push({ name: 'title', type: 'textbox', label: 'Image Title' });
        }

        if (imageDimensions) {
          generalFormItems.push({
            type: 'container',
            label: 'Dimensions',
            layout: 'flex',
            direction: 'row',
            align: 'center',
            spacing: 5,
            items: [
              { name: 'width', type: 'textbox', maxLength: 5, size: 3, onchange: recalcSize, ariaLabel: 'Width' },
              { type: 'label', text: 'x' },
              { name: 'height', type: 'textbox', maxLength: 5, size: 3, onchange: recalcSize, ariaLabel: 'Height' },
              { name: 'constrain', type: 'checkbox', checked: true, text: 'Constrain proportions' }
            ]
          });
        }

        generalFormItems.push(classListCtrl);

        if (editor.settings.image_caption && Env.ceFalse) {
          generalFormItems.push({ name: 'caption', type: 'checkbox', label: 'Caption' });
        }

        if (editor.settings.image_advtab || editor.settings.images_upload_url) {
          var body = [
            {
              title: 'General',
              type: 'form',
              items: generalFormItems
            }
          ];

          if (editor.settings.image_advtab) {
            // Parse styles from img
            if (imgElm) {
              if (imgElm.style.marginLeft && imgElm.style.marginRight && imgElm.style.marginLeft === imgElm.style.marginRight) {
                data.hspace = Utils.removePixelSuffix(imgElm.style.marginLeft);
              }
              if (imgElm.style.marginTop && imgElm.style.marginBottom && imgElm.style.marginTop === imgElm.style.marginBottom) {
                data.vspace = Utils.removePixelSuffix(imgElm.style.marginTop);
              }
              if (imgElm.style.borderWidth) {
                data.border = Utils.removePixelSuffix(imgElm.style.borderWidth);
              }

              data.style = editor.dom.serializeStyle(editor.dom.parseStyle(editor.dom.getAttrib(imgElm, 'style')));
            }

            body.push({
              title: 'Advanced',
              type: 'form',
              pack: 'start',
              items: [
                {
                  label: 'Style',
                  name: 'style',
                  type: 'textbox',
                  onchange: updateVSpaceHSpaceBorder
                },
                {
                  type: 'form',
                  layout: 'grid',
                  packV: 'start',
                  columns: 2,
                  padding: 0,
                  alignH: ['left', 'right'],
                  defaults: {
                    type: 'textbox',
                    maxWidth: 50,
                    onchange: updateStyle
                  },
                  items: [
                    { label: 'Vertical space', name: 'vspace' },
                    { label: 'Horizontal space', name: 'hspace' },
                    { label: 'Border', name: 'border' }
                  ]
                }
              ]
            });
          }

          if (editor.settings.images_upload_url) {
            var acceptExts = '.jpg,.jpeg,.png,.gif';

            var uploadTab = {
              title: 'Upload',
              type: 'form',
              layout: 'flex',
              direction: 'column',
              align: 'stretch',
              padding: '20 20 20 20',
              items: [
                {
                  type: 'container',
                  layout: 'flex',
                  direction: 'column',
                  align: 'center',
                  spacing: 10,
                  items: [
                    {
                      text: "Browse for an image",
                      type: 'browsebutton',
                      accept: acceptExts,
                      onchange: onFileInput
                    },
                    {
                      text: 'OR',
                      type: 'label'
                    }
                  ]
                },
                {
                  text: "Drop an image here",
                  type: 'dropzone',
                  accept: acceptExts,
                  height: 100,
                  onchange: onFileInput
                }
              ]
            };

            body.push(uploadTab);
          }

          // Advanced dialog shows general+advanced tabs
          win = editor.windowManager.open({
            title: 'Insert/edit image',
            data: data,
            bodyType: 'tabpanel',
            body: body,
            onSubmit: onSubmitForm
          });
        } else {
          // Simple default dialog
          win = editor.windowManager.open({
            title: 'Insert/edit image',
            data: data,
            body: generalFormItems,
            onSubmit: onSubmitForm
          });
        }
      }

      function open() {
        createImageList(showDialog);
      }

      return {
        open: open
      };
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

/**
 * This class contains all core logic for the image plugin.
 *
 * @class tinymce.image.Plugin
 * @private
 */
define(
  'tinymce.plugins.image.Plugin',
  [
    'tinymce.core.PluginManager',
    'tinymce.core.util.Tools',
    'tinymce.plugins.image.ui.Dialog'
  ],
  function (PluginManager, Tools, Dialog) {
    PluginManager.add('image', function (editor) {

      editor.on('preInit', function () {
        function hasImageClass(node) {
          var className = node.attr('class');
          return className && /\bimage\b/.test(className);
        }

        function toggleContentEditableState(state) {
          return function (nodes) {
            var i = nodes.length, node;

            function toggleContentEditable(node) {
              node.attr('contenteditable', state ? 'true' : null);
            }

            while (i--) {
              node = nodes[i];

              if (hasImageClass(node)) {
                node.attr('contenteditable', state ? 'false' : null);
                Tools.each(node.getAll('figcaption'), toggleContentEditable);
                Tools.each(node.getAll('img'), toggleContentEditable);
              }
            }
          };
        }

        editor.parser.addNodeFilter('figure', toggleContentEditableState(true));
        editor.serializer.addNodeFilter('figure', toggleContentEditableState(false));
      });

      editor.addButton('image', {
        icon: 'image',
        tooltip: 'Insert/edit image',
        onclick: Dialog(editor).open,
        stateSelector: 'img:not([data-mce-object],[data-mce-placeholder]),figure.image'
      });

      editor.addMenuItem('image', {
        icon: 'image',
        text: 'Image',
        onclick: Dialog(editor).open,
        context: 'insert',
        prependToContext: true
      });

      editor.addCommand('mceImage', Dialog(editor).open);
    });

    return function () { };
  }
);
dem('tinymce.plugins.image.Plugin')();
})();
