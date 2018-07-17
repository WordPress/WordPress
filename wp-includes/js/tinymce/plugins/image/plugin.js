(function () {
var image = (function () {
  'use strict';

  var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

  var hasDimensions = function (editor) {
    return editor.settings.image_dimensions === false ? false : true;
  };
  var hasAdvTab = function (editor) {
    return editor.settings.image_advtab === true ? true : false;
  };
  var getPrependUrl = function (editor) {
    return editor.getParam('image_prepend_url', '');
  };
  var getClassList = function (editor) {
    return editor.getParam('image_class_list');
  };
  var hasDescription = function (editor) {
    return editor.settings.image_description === false ? false : true;
  };
  var hasImageTitle = function (editor) {
    return editor.settings.image_title === true ? true : false;
  };
  var hasImageCaption = function (editor) {
    return editor.settings.image_caption === true ? true : false;
  };
  var getImageList = function (editor) {
    return editor.getParam('image_list', false);
  };
  var hasUploadUrl = function (editor) {
    return editor.getParam('images_upload_url', false);
  };
  var hasUploadHandler = function (editor) {
    return editor.getParam('images_upload_handler', false);
  };
  var getUploadUrl = function (editor) {
    return editor.getParam('images_upload_url');
  };
  var getUploadHandler = function (editor) {
    return editor.getParam('images_upload_handler');
  };
  var getUploadBasePath = function (editor) {
    return editor.getParam('images_upload_base_path');
  };
  var getUploadCredentials = function (editor) {
    return editor.getParam('images_upload_credentials');
  };
  var $_1dn8wtctjjgwebvz = {
    hasDimensions: hasDimensions,
    hasAdvTab: hasAdvTab,
    getPrependUrl: getPrependUrl,
    getClassList: getClassList,
    hasDescription: hasDescription,
    hasImageTitle: hasImageTitle,
    hasImageCaption: hasImageCaption,
    getImageList: getImageList,
    hasUploadUrl: hasUploadUrl,
    hasUploadHandler: hasUploadHandler,
    getUploadUrl: getUploadUrl,
    getUploadHandler: getUploadHandler,
    getUploadBasePath: getUploadBasePath,
    getUploadCredentials: getUploadCredentials
  };

  var Global = typeof window !== 'undefined' ? window : Function('return this;')();

  var path = function (parts, scope) {
    var o = scope !== undefined && scope !== null ? scope : Global;
    for (var i = 0; i < parts.length && o !== undefined && o !== null; ++i)
      o = o[parts[i]];
    return o;
  };
  var resolve = function (p, scope) {
    var parts = p.split('.');
    return path(parts, scope);
  };

  var unsafe = function (name, scope) {
    return resolve(name, scope);
  };
  var getOrDie = function (name, scope) {
    var actual = unsafe(name, scope);
    if (actual === undefined || actual === null)
      throw name + ' not available on this browser';
    return actual;
  };
  var $_oab1bcwjjgwebwl = { getOrDie: getOrDie };

  function FileReader () {
    var f = $_oab1bcwjjgwebwl.getOrDie('FileReader');
    return new f();
  }

  var global$1 = tinymce.util.Tools.resolve('tinymce.util.Promise');

  var global$2 = tinymce.util.Tools.resolve('tinymce.util.Tools');

  var global$3 = tinymce.util.Tools.resolve('tinymce.util.XHR');

  var parseIntAndGetMax = function (val1, val2) {
    return Math.max(parseInt(val1, 10), parseInt(val2, 10));
  };
  var getImageSize = function (url, callback) {
    var img = document.createElement('img');
    function done(width, height) {
      if (img.parentNode) {
        img.parentNode.removeChild(img);
      }
      callback({
        width: width,
        height: height
      });
    }
    img.onload = function () {
      var width = parseIntAndGetMax(img.width, img.clientWidth);
      var height = parseIntAndGetMax(img.height, img.clientHeight);
      done(width, height);
    };
    img.onerror = function () {
      done(0, 0);
    };
    var style = img.style;
    style.visibility = 'hidden';
    style.position = 'fixed';
    style.bottom = style.left = '0px';
    style.width = style.height = 'auto';
    document.body.appendChild(img);
    img.src = url;
  };
  var buildListItems = function (inputList, itemCallback, startItems) {
    function appendItems(values, output) {
      output = output || [];
      global$2.each(values, function (item) {
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
      var splitMargin = css.margin.split(' ');
      switch (splitMargin.length) {
      case 1:
        css['margin-top'] = css['margin-top'] || splitMargin[0];
        css['margin-right'] = css['margin-right'] || splitMargin[0];
        css['margin-bottom'] = css['margin-bottom'] || splitMargin[0];
        css['margin-left'] = css['margin-left'] || splitMargin[0];
        break;
      case 2:
        css['margin-top'] = css['margin-top'] || splitMargin[0];
        css['margin-right'] = css['margin-right'] || splitMargin[1];
        css['margin-bottom'] = css['margin-bottom'] || splitMargin[0];
        css['margin-left'] = css['margin-left'] || splitMargin[1];
        break;
      case 3:
        css['margin-top'] = css['margin-top'] || splitMargin[0];
        css['margin-right'] = css['margin-right'] || splitMargin[1];
        css['margin-bottom'] = css['margin-bottom'] || splitMargin[2];
        css['margin-left'] = css['margin-left'] || splitMargin[1];
        break;
      case 4:
        css['margin-top'] = css['margin-top'] || splitMargin[0];
        css['margin-right'] = css['margin-right'] || splitMargin[1];
        css['margin-bottom'] = css['margin-bottom'] || splitMargin[2];
        css['margin-left'] = css['margin-left'] || splitMargin[3];
      }
      delete css.margin;
    }
    return css;
  };
  var createImageList = function (editor, callback) {
    var imageList = $_1dn8wtctjjgwebvz.getImageList(editor);
    if (typeof imageList === 'string') {
      global$3.send({
        url: imageList,
        success: function (text) {
          callback(JSON.parse(text));
        }
      });
    } else if (typeof imageList === 'function') {
      imageList(callback);
    } else {
      callback(imageList);
    }
  };
  var waitLoadImage = function (editor, data, imgElm) {
    function selectImage() {
      imgElm.onload = imgElm.onerror = null;
      if (editor.selection) {
        editor.selection.select(imgElm);
        editor.nodeChanged();
      }
    }
    imgElm.onload = function () {
      if (!data.width && !data.height && $_1dn8wtctjjgwebvz.hasDimensions(editor)) {
        editor.dom.setAttribs(imgElm, {
          width: imgElm.clientWidth,
          height: imgElm.clientHeight
        });
      }
      selectImage();
    };
    imgElm.onerror = selectImage;
  };
  var blobToDataUri = function (blob) {
    return new global$1(function (resolve, reject) {
      var reader = new FileReader();
      reader.onload = function () {
        resolve(reader.result);
      };
      reader.onerror = function () {
        reject(FileReader.error.message);
      };
      reader.readAsDataURL(blob);
    });
  };
  var $_1e8k4ncujjgwebw2 = {
    getImageSize: getImageSize,
    buildListItems: buildListItems,
    removePixelSuffix: removePixelSuffix,
    addPixelSuffix: addPixelSuffix,
    mergeMargins: mergeMargins,
    createImageList: createImageList,
    waitLoadImage: waitLoadImage,
    blobToDataUri: blobToDataUri
  };

  var global$4 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

  var hasOwnProperty = Object.prototype.hasOwnProperty;
  var shallow = function (old, nu) {
    return nu;
  };
  var baseMerge = function (merger) {
    return function () {
      var objects = new Array(arguments.length);
      for (var i = 0; i < objects.length; i++)
        objects[i] = arguments[i];
      if (objects.length === 0)
        throw new Error('Can\'t merge zero objects');
      var ret = {};
      for (var j = 0; j < objects.length; j++) {
        var curObject = objects[j];
        for (var key in curObject)
          if (hasOwnProperty.call(curObject, key)) {
            ret[key] = merger(ret[key], curObject[key]);
          }
      }
      return ret;
    };
  };

  var merge = baseMerge(shallow);

  var DOM = global$4.DOM;
  var getHspace = function (image) {
    if (image.style.marginLeft && image.style.marginRight && image.style.marginLeft === image.style.marginRight) {
      return $_1e8k4ncujjgwebw2.removePixelSuffix(image.style.marginLeft);
    } else {
      return '';
    }
  };
  var getVspace = function (image) {
    if (image.style.marginTop && image.style.marginBottom && image.style.marginTop === image.style.marginBottom) {
      return $_1e8k4ncujjgwebw2.removePixelSuffix(image.style.marginTop);
    } else {
      return '';
    }
  };
  var getBorder = function (image) {
    if (image.style.borderWidth) {
      return $_1e8k4ncujjgwebw2.removePixelSuffix(image.style.borderWidth);
    } else {
      return '';
    }
  };
  var getAttrib = function (image, name$$1) {
    if (image.hasAttribute(name$$1)) {
      return image.getAttribute(name$$1);
    } else {
      return '';
    }
  };
  var getStyle = function (image, name$$1) {
    return image.style[name$$1] ? image.style[name$$1] : '';
  };
  var hasCaption = function (image) {
    return image.parentNode !== null && image.parentNode.nodeName === 'FIGURE';
  };
  var setAttrib = function (image, name$$1, value) {
    image.setAttribute(name$$1, value);
  };
  var wrapInFigure = function (image) {
    var figureElm = DOM.create('figure', { class: 'image' });
    DOM.insertAfter(figureElm, image);
    figureElm.appendChild(image);
    figureElm.appendChild(DOM.create('figcaption', { contentEditable: true }, 'Caption'));
    figureElm.contentEditable = 'false';
  };
  var removeFigure = function (image) {
    var figureElm = image.parentNode;
    DOM.insertAfter(image, figureElm);
    DOM.remove(figureElm);
  };
  var toggleCaption = function (image) {
    if (hasCaption(image)) {
      removeFigure(image);
    } else {
      wrapInFigure(image);
    }
  };
  var normalizeStyle = function (image, normalizeCss) {
    var attrValue = image.getAttribute('style');
    var value = normalizeCss(attrValue !== null ? attrValue : '');
    if (value.length > 0) {
      image.setAttribute('style', value);
      image.setAttribute('data-mce-style', value);
    } else {
      image.removeAttribute('style');
    }
  };
  var setSize = function (name$$1, normalizeCss) {
    return function (image, name$$1, value) {
      if (image.style[name$$1]) {
        image.style[name$$1] = $_1e8k4ncujjgwebw2.addPixelSuffix(value);
        normalizeStyle(image, normalizeCss);
      } else {
        setAttrib(image, name$$1, value);
      }
    };
  };
  var getSize = function (image, name$$1) {
    if (image.style[name$$1]) {
      return $_1e8k4ncujjgwebw2.removePixelSuffix(image.style[name$$1]);
    } else {
      return getAttrib(image, name$$1);
    }
  };
  var setHspace = function (image, value) {
    var pxValue = $_1e8k4ncujjgwebw2.addPixelSuffix(value);
    image.style.marginLeft = pxValue;
    image.style.marginRight = pxValue;
  };
  var setVspace = function (image, value) {
    var pxValue = $_1e8k4ncujjgwebw2.addPixelSuffix(value);
    image.style.marginTop = pxValue;
    image.style.marginBottom = pxValue;
  };
  var setBorder = function (image, value) {
    var pxValue = $_1e8k4ncujjgwebw2.addPixelSuffix(value);
    image.style.borderWidth = pxValue;
  };
  var setBorderStyle = function (image, value) {
    image.style.borderStyle = value;
  };
  var getBorderStyle = function (image) {
    return getStyle(image, 'borderStyle');
  };
  var isFigure = function (elm) {
    return elm.nodeName === 'FIGURE';
  };
  var defaultData = function () {
    return {
      src: '',
      alt: '',
      title: '',
      width: '',
      height: '',
      class: '',
      style: '',
      caption: false,
      hspace: '',
      vspace: '',
      border: '',
      borderStyle: ''
    };
  };
  var getStyleValue = function (normalizeCss, data) {
    var image = document.createElement('img');
    setAttrib(image, 'style', data.style);
    if (getHspace(image) || data.hspace !== '') {
      setHspace(image, data.hspace);
    }
    if (getVspace(image) || data.vspace !== '') {
      setVspace(image, data.vspace);
    }
    if (getBorder(image) || data.border !== '') {
      setBorder(image, data.border);
    }
    if (getBorderStyle(image) || data.borderStyle !== '') {
      setBorderStyle(image, data.borderStyle);
    }
    return normalizeCss(image.getAttribute('style'));
  };
  var create = function (normalizeCss, data) {
    var image = document.createElement('img');
    write(normalizeCss, merge(data, { caption: false }), image);
    setAttrib(image, 'alt', data.alt);
    if (data.caption) {
      var figure = DOM.create('figure', { class: 'image' });
      figure.appendChild(image);
      figure.appendChild(DOM.create('figcaption', { contentEditable: true }, 'Caption'));
      figure.contentEditable = 'false';
      return figure;
    } else {
      return image;
    }
  };
  var read = function (normalizeCss, image) {
    return {
      src: getAttrib(image, 'src'),
      alt: getAttrib(image, 'alt'),
      title: getAttrib(image, 'title'),
      width: getSize(image, 'width'),
      height: getSize(image, 'height'),
      class: getAttrib(image, 'class'),
      style: normalizeCss(getAttrib(image, 'style')),
      caption: hasCaption(image),
      hspace: getHspace(image),
      vspace: getVspace(image),
      border: getBorder(image),
      borderStyle: getStyle(image, 'borderStyle')
    };
  };
  var updateProp = function (image, oldData, newData, name$$1, set) {
    if (newData[name$$1] !== oldData[name$$1]) {
      set(image, name$$1, newData[name$$1]);
    }
  };
  var normalized = function (set, normalizeCss) {
    return function (image, name$$1, value) {
      set(image, value);
      normalizeStyle(image, normalizeCss);
    };
  };
  var write = function (normalizeCss, newData, image) {
    var oldData = read(normalizeCss, image);
    updateProp(image, oldData, newData, 'caption', function (image, _name, _value) {
      return toggleCaption(image);
    });
    updateProp(image, oldData, newData, 'src', setAttrib);
    updateProp(image, oldData, newData, 'alt', setAttrib);
    updateProp(image, oldData, newData, 'title', setAttrib);
    updateProp(image, oldData, newData, 'width', setSize('width', normalizeCss));
    updateProp(image, oldData, newData, 'height', setSize('height', normalizeCss));
    updateProp(image, oldData, newData, 'class', setAttrib);
    updateProp(image, oldData, newData, 'style', normalized(function (image, value) {
      return setAttrib(image, 'style', value);
    }, normalizeCss));
    updateProp(image, oldData, newData, 'hspace', normalized(setHspace, normalizeCss));
    updateProp(image, oldData, newData, 'vspace', normalized(setVspace, normalizeCss));
    updateProp(image, oldData, newData, 'border', normalized(setBorder, normalizeCss));
    updateProp(image, oldData, newData, 'borderStyle', normalized(setBorderStyle, normalizeCss));
  };

  var normalizeCss = function (editor, cssText) {
    var css = editor.dom.styles.parse(cssText);
    var mergedCss = $_1e8k4ncujjgwebw2.mergeMargins(css);
    var compressed = editor.dom.styles.parse(editor.dom.styles.serialize(mergedCss));
    return editor.dom.styles.serialize(compressed);
  };
  var getSelectedImage = function (editor) {
    var imgElm = editor.selection.getNode();
    var figureElm = editor.dom.getParent(imgElm, 'figure.image');
    if (figureElm) {
      return editor.dom.select('img', figureElm)[0];
    }
    if (imgElm && (imgElm.nodeName !== 'IMG' || imgElm.getAttribute('data-mce-object') || imgElm.getAttribute('data-mce-placeholder'))) {
      return null;
    }
    return imgElm;
  };
  var splitTextBlock = function (editor, figure) {
    var dom = editor.dom;
    var textBlock = dom.getParent(figure.parentNode, function (node) {
      return editor.schema.getTextBlockElements()[node.nodeName];
    });
    if (textBlock) {
      return dom.split(textBlock, figure);
    } else {
      return figure;
    }
  };
  var readImageDataFromSelection = function (editor) {
    var image = getSelectedImage(editor);
    return image ? read(function (css) {
      return normalizeCss(editor, css);
    }, image) : defaultData();
  };
  var insertImageAtCaret = function (editor, data) {
    var elm = create(function (css) {
      return normalizeCss(editor, css);
    }, data);
    editor.dom.setAttrib(elm, 'data-mce-id', '__mcenew');
    editor.focus();
    editor.selection.setContent(elm.outerHTML);
    var insertedElm = editor.dom.select('*[data-mce-id="__mcenew"]')[0];
    editor.dom.setAttrib(insertedElm, 'data-mce-id', null);
    if (isFigure(insertedElm)) {
      var figure = splitTextBlock(editor, insertedElm);
      editor.selection.select(figure);
    } else {
      editor.selection.select(insertedElm);
    }
  };
  var syncSrcAttr = function (editor, image) {
    editor.dom.setAttrib(image, 'src', image.getAttribute('src'));
  };
  var deleteImage = function (editor, image) {
    if (image) {
      var elm = editor.dom.is(image.parentNode, 'figure.image') ? image.parentNode : image;
      editor.dom.remove(elm);
      editor.focus();
      editor.nodeChanged();
      if (editor.dom.isEmpty(editor.getBody())) {
        editor.setContent('');
        editor.selection.setCursorLocation();
      }
    }
  };
  var writeImageDataToSelection = function (editor, data) {
    var image = getSelectedImage(editor);
    write(function (css) {
      return normalizeCss(editor, css);
    }, data, image);
    syncSrcAttr(editor, image);
    if (isFigure(image.parentNode)) {
      var figure = image.parentNode;
      splitTextBlock(editor, figure);
      editor.selection.select(image.parentNode);
    } else {
      editor.selection.select(image);
      $_1e8k4ncujjgwebw2.waitLoadImage(editor, data, image);
    }
  };
  var insertOrUpdateImage = function (editor, data) {
    var image = getSelectedImage(editor);
    if (image) {
      if (data.src) {
        writeImageDataToSelection(editor, data);
      } else {
        deleteImage(editor, image);
      }
    } else if (data.src) {
      insertImageAtCaret(editor, data);
    }
  };

  var updateVSpaceHSpaceBorder = function (editor) {
    return function (evt) {
      var dom = editor.dom;
      var rootControl = evt.control.rootControl;
      if (!$_1dn8wtctjjgwebvz.hasAdvTab(editor)) {
        return;
      }
      var data = rootControl.toJSON();
      var css = dom.parseStyle(data.style);
      rootControl.find('#vspace').value('');
      rootControl.find('#hspace').value('');
      css = $_1e8k4ncujjgwebw2.mergeMargins(css);
      if (css['margin-top'] && css['margin-bottom'] || css['margin-right'] && css['margin-left']) {
        if (css['margin-top'] === css['margin-bottom']) {
          rootControl.find('#vspace').value($_1e8k4ncujjgwebw2.removePixelSuffix(css['margin-top']));
        } else {
          rootControl.find('#vspace').value('');
        }
        if (css['margin-right'] === css['margin-left']) {
          rootControl.find('#hspace').value($_1e8k4ncujjgwebw2.removePixelSuffix(css['margin-right']));
        } else {
          rootControl.find('#hspace').value('');
        }
      }
      if (css['border-width']) {
        rootControl.find('#border').value($_1e8k4ncujjgwebw2.removePixelSuffix(css['border-width']));
      } else {
        rootControl.find('#border').value('');
      }
      if (css['border-style']) {
        rootControl.find('#borderStyle').value(css['border-style']);
      } else {
        rootControl.find('#borderStyle').value('');
      }
      rootControl.find('#style').value(dom.serializeStyle(dom.parseStyle(dom.serializeStyle(css))));
    };
  };
  var updateStyle = function (editor, win) {
    win.find('#style').each(function (ctrl) {
      var value = getStyleValue(function (css) {
        return normalizeCss(editor, css);
      }, merge(defaultData(), win.toJSON()));
      ctrl.value(value);
    });
  };
  var makeTab = function (editor) {
    return {
      title: 'Advanced',
      type: 'form',
      pack: 'start',
      items: [
        {
          label: 'Style',
          name: 'style',
          type: 'textbox',
          onchange: updateVSpaceHSpaceBorder(editor)
        },
        {
          type: 'form',
          layout: 'grid',
          packV: 'start',
          columns: 2,
          padding: 0,
          defaults: {
            type: 'textbox',
            maxWidth: 50,
            onchange: function (evt) {
              updateStyle(editor, evt.control.rootControl);
            }
          },
          items: [
            {
              label: 'Vertical space',
              name: 'vspace'
            },
            {
              label: 'Border width',
              name: 'border'
            },
            {
              label: 'Horizontal space',
              name: 'hspace'
            },
            {
              label: 'Border style',
              type: 'listbox',
              name: 'borderStyle',
              width: 90,
              maxWidth: 90,
              onselect: function (evt) {
                updateStyle(editor, evt.control.rootControl);
              },
              values: [
                {
                  text: 'Select...',
                  value: ''
                },
                {
                  text: 'Solid',
                  value: 'solid'
                },
                {
                  text: 'Dotted',
                  value: 'dotted'
                },
                {
                  text: 'Dashed',
                  value: 'dashed'
                },
                {
                  text: 'Double',
                  value: 'double'
                },
                {
                  text: 'Groove',
                  value: 'groove'
                },
                {
                  text: 'Ridge',
                  value: 'ridge'
                },
                {
                  text: 'Inset',
                  value: 'inset'
                },
                {
                  text: 'Outset',
                  value: 'outset'
                },
                {
                  text: 'None',
                  value: 'none'
                },
                {
                  text: 'Hidden',
                  value: 'hidden'
                }
              ]
            }
          ]
        }
      ]
    };
  };
  var $_6dfy5vd3jjgwebxf = { makeTab: makeTab };

  var doSyncSize = function (widthCtrl, heightCtrl) {
    widthCtrl.state.set('oldVal', widthCtrl.value());
    heightCtrl.state.set('oldVal', heightCtrl.value());
  };
  var doSizeControls = function (win, f) {
    var widthCtrl = win.find('#width')[0];
    var heightCtrl = win.find('#height')[0];
    var constrained = win.find('#constrain')[0];
    if (widthCtrl && heightCtrl && constrained) {
      f(widthCtrl, heightCtrl, constrained.checked());
    }
  };
  var doUpdateSize = function (widthCtrl, heightCtrl, isContrained) {
    var oldWidth = widthCtrl.state.get('oldVal');
    var oldHeight = heightCtrl.state.get('oldVal');
    var newWidth = widthCtrl.value();
    var newHeight = heightCtrl.value();
    if (isContrained && oldWidth && oldHeight && newWidth && newHeight) {
      if (newWidth !== oldWidth) {
        newHeight = Math.round(newWidth / oldWidth * newHeight);
        if (!isNaN(newHeight)) {
          heightCtrl.value(newHeight);
        }
      } else {
        newWidth = Math.round(newHeight / oldHeight * newWidth);
        if (!isNaN(newWidth)) {
          widthCtrl.value(newWidth);
        }
      }
    }
    doSyncSize(widthCtrl, heightCtrl);
  };
  var syncSize = function (win) {
    doSizeControls(win, doSyncSize);
  };
  var updateSize = function (win) {
    doSizeControls(win, doUpdateSize);
  };
  var createUi = function () {
    var recalcSize = function (evt) {
      updateSize(evt.control.rootControl);
    };
    return {
      type: 'container',
      label: 'Dimensions',
      layout: 'flex',
      align: 'center',
      spacing: 5,
      items: [
        {
          name: 'width',
          type: 'textbox',
          maxLength: 5,
          size: 5,
          onchange: recalcSize,
          ariaLabel: 'Width'
        },
        {
          type: 'label',
          text: 'x'
        },
        {
          name: 'height',
          type: 'textbox',
          maxLength: 5,
          size: 5,
          onchange: recalcSize,
          ariaLabel: 'Height'
        },
        {
          name: 'constrain',
          type: 'checkbox',
          checked: true,
          text: 'Constrain proportions'
        }
      ]
    };
  };
  var $_ftlz5pdajjgweby4 = {
    createUi: createUi,
    syncSize: syncSize,
    updateSize: updateSize
  };

  var onSrcChange = function (evt, editor) {
    var srcURL, prependURL, absoluteURLPattern;
    var meta = evt.meta || {};
    var control = evt.control;
    var rootControl = control.rootControl;
    var imageListCtrl = rootControl.find('#image-list')[0];
    if (imageListCtrl) {
      imageListCtrl.value(editor.convertURL(control.value(), 'src'));
    }
    global$2.each(meta, function (value, key) {
      rootControl.find('#' + key).value(value);
    });
    if (!meta.width && !meta.height) {
      srcURL = editor.convertURL(control.value(), 'src');
      prependURL = $_1dn8wtctjjgwebvz.getPrependUrl(editor);
      absoluteURLPattern = new RegExp('^(?:[a-z]+:)?//', 'i');
      if (prependURL && !absoluteURLPattern.test(srcURL) && srcURL.substring(0, prependURL.length) !== prependURL) {
        srcURL = prependURL + srcURL;
      }
      control.value(srcURL);
      $_1e8k4ncujjgwebw2.getImageSize(editor.documentBaseURI.toAbsolute(control.value()), function (data) {
        if (data.width && data.height && $_1dn8wtctjjgwebvz.hasDimensions(editor)) {
          rootControl.find('#width').value(data.width);
          rootControl.find('#height').value(data.height);
          $_ftlz5pdajjgweby4.syncSize(rootControl);
        }
      });
    }
  };
  var onBeforeCall = function (evt) {
    evt.meta = evt.control.rootControl.toJSON();
  };
  var getGeneralItems = function (editor, imageListCtrl) {
    var generalFormItems = [
      {
        name: 'src',
        type: 'filepicker',
        filetype: 'image',
        label: 'Source',
        autofocus: true,
        onchange: function (evt) {
          onSrcChange(evt, editor);
        },
        onbeforecall: onBeforeCall
      },
      imageListCtrl
    ];
    if ($_1dn8wtctjjgwebvz.hasDescription(editor)) {
      generalFormItems.push({
        name: 'alt',
        type: 'textbox',
        label: 'Image description'
      });
    }
    if ($_1dn8wtctjjgwebvz.hasImageTitle(editor)) {
      generalFormItems.push({
        name: 'title',
        type: 'textbox',
        label: 'Image Title'
      });
    }
    if ($_1dn8wtctjjgwebvz.hasDimensions(editor)) {
      generalFormItems.push($_ftlz5pdajjgweby4.createUi());
    }
    if ($_1dn8wtctjjgwebvz.getClassList(editor)) {
      generalFormItems.push({
        name: 'class',
        type: 'listbox',
        label: 'Class',
        values: $_1e8k4ncujjgwebw2.buildListItems($_1dn8wtctjjgwebvz.getClassList(editor), function (item) {
          if (item.value) {
            item.textStyle = function () {
              return editor.formatter.getCssText({
                inline: 'img',
                classes: [item.value]
              });
            };
          }
        })
      });
    }
    if ($_1dn8wtctjjgwebvz.hasImageCaption(editor)) {
      generalFormItems.push({
        name: 'caption',
        type: 'checkbox',
        label: 'Caption'
      });
    }
    return generalFormItems;
  };
  var makeTab$1 = function (editor, imageListCtrl) {
    return {
      title: 'General',
      type: 'form',
      items: getGeneralItems(editor, imageListCtrl)
    };
  };
  var $_78zck5d9jjgweby1 = {
    makeTab: makeTab$1,
    getGeneralItems: getGeneralItems
  };

  var url = function () {
    return $_oab1bcwjjgwebwl.getOrDie('URL');
  };
  var createObjectURL = function (blob) {
    return url().createObjectURL(blob);
  };
  var revokeObjectURL = function (u) {
    url().revokeObjectURL(u);
  };
  var $_86i13edcjjgwebya = {
    createObjectURL: createObjectURL,
    revokeObjectURL: revokeObjectURL
  };

  var global$5 = tinymce.util.Tools.resolve('tinymce.ui.Factory');

  function XMLHttpRequest () {
    var f = $_oab1bcwjjgwebwl.getOrDie('XMLHttpRequest');
    return new f();
  }

  var noop = function () {
  };
  var pathJoin = function (path1, path2) {
    if (path1) {
      return path1.replace(/\/$/, '') + '/' + path2.replace(/^\//, '');
    }
    return path2;
  };
  function Uploader (settings) {
    var defaultHandler = function (blobInfo, success, failure, progress) {
      var xhr, formData;
      xhr = new XMLHttpRequest();
      xhr.open('POST', settings.url);
      xhr.withCredentials = settings.credentials;
      xhr.upload.onprogress = function (e) {
        progress(e.loaded / e.total * 100);
      };
      xhr.onerror = function () {
        failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
      };
      xhr.onload = function () {
        var json;
        if (xhr.status < 200 || xhr.status >= 300) {
          failure('HTTP Error: ' + xhr.status);
          return;
        }
        json = JSON.parse(xhr.responseText);
        if (!json || typeof json.location !== 'string') {
          failure('Invalid JSON: ' + xhr.responseText);
          return;
        }
        success(pathJoin(settings.basePath, json.location));
      };
      formData = new FormData();
      formData.append('file', blobInfo.blob(), blobInfo.filename());
      xhr.send(formData);
    };
    var uploadBlob = function (blobInfo, handler) {
      return new global$1(function (resolve, reject) {
        try {
          handler(blobInfo, resolve, reject, noop);
        } catch (ex) {
          reject(ex.message);
        }
      });
    };
    var isDefaultHandler = function (handler) {
      return handler === defaultHandler;
    };
    var upload = function (blobInfo) {
      return !settings.url && isDefaultHandler(settings.handler) ? global$1.reject('Upload url missing from the settings.') : uploadBlob(blobInfo, settings.handler);
    };
    settings = global$2.extend({
      credentials: false,
      handler: defaultHandler
    }, settings);
    return { upload: upload };
  }

  var onFileInput = function (editor) {
    return function (evt) {
      var Throbber = global$5.get('Throbber');
      var rootControl = evt.control.rootControl;
      var throbber = new Throbber(rootControl.getEl());
      var file = evt.control.value();
      var blobUri = $_86i13edcjjgwebya.createObjectURL(file);
      var uploader = Uploader({
        url: $_1dn8wtctjjgwebvz.getUploadUrl(editor),
        basePath: $_1dn8wtctjjgwebvz.getUploadBasePath(editor),
        credentials: $_1dn8wtctjjgwebvz.getUploadCredentials(editor),
        handler: $_1dn8wtctjjgwebvz.getUploadHandler(editor)
      });
      var finalize = function () {
        throbber.hide();
        $_86i13edcjjgwebya.revokeObjectURL(blobUri);
      };
      throbber.show();
      return $_1e8k4ncujjgwebw2.blobToDataUri(file).then(function (dataUrl) {
        var blobInfo = editor.editorUpload.blobCache.create({
          blob: file,
          blobUri: blobUri,
          name: file.name ? file.name.replace(/\.[^\.]+$/, '') : null,
          base64: dataUrl.split(',')[1]
        });
        return uploader.upload(blobInfo).then(function (url) {
          var src = rootControl.find('#src');
          src.value(url);
          rootControl.find('tabpanel')[0].activateTab(0);
          src.fire('change');
          finalize();
          return url;
        });
      }).catch(function (err) {
        editor.windowManager.alert(err);
        finalize();
      });
    };
  };
  var acceptExts = '.jpg,.jpeg,.png,.gif';
  var makeTab$2 = function (editor) {
    return {
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
              text: 'Browse for an image',
              type: 'browsebutton',
              accept: acceptExts,
              onchange: onFileInput(editor)
            },
            {
              text: 'OR',
              type: 'label'
            }
          ]
        },
        {
          text: 'Drop an image here',
          type: 'dropzone',
          accept: acceptExts,
          height: 100,
          onchange: onFileInput(editor)
        }
      ]
    };
  };
  var $_71qd7mdbjjgweby7 = { makeTab: makeTab$2 };

  var curry = function (f) {
    var x = [];
    for (var _i = 1; _i < arguments.length; _i++) {
      x[_i - 1] = arguments[_i];
    }
    var args = new Array(arguments.length - 1);
    for (var i = 1; i < arguments.length; i++)
      args[i - 1] = arguments[i];
    return function () {
      var x = [];
      for (var _i = 0; _i < arguments.length; _i++) {
        x[_i] = arguments[_i];
      }
      var newArgs = new Array(arguments.length);
      for (var j = 0; j < newArgs.length; j++)
        newArgs[j] = arguments[j];
      var all = args.concat(newArgs);
      return f.apply(null, all);
    };
  };

  var submitForm = function (editor, evt) {
    var win = evt.control.getRoot();
    $_ftlz5pdajjgweby4.updateSize(win);
    editor.undoManager.transact(function () {
      var data = merge(readImageDataFromSelection(editor), win.toJSON());
      insertOrUpdateImage(editor, data);
    });
    editor.editorUpload.uploadImagesAuto();
  };
  function Dialog (editor) {
    function showDialog(imageList) {
      var data = readImageDataFromSelection(editor);
      var win, imageListCtrl;
      if (imageList) {
        imageListCtrl = {
          type: 'listbox',
          label: 'Image list',
          name: 'image-list',
          values: $_1e8k4ncujjgwebw2.buildListItems(imageList, function (item) {
            item.value = editor.convertURL(item.value || item.url, 'src');
          }, [{
              text: 'None',
              value: ''
            }]),
          value: data.src && editor.convertURL(data.src, 'src'),
          onselect: function (e) {
            var altCtrl = win.find('#alt');
            if (!altCtrl.value() || e.lastControl && altCtrl.value() === e.lastControl.text()) {
              altCtrl.value(e.control.text());
            }
            win.find('#src').value(e.control.value()).fire('change');
          },
          onPostRender: function () {
            imageListCtrl = this;
          }
        };
      }
      if ($_1dn8wtctjjgwebvz.hasAdvTab(editor) || $_1dn8wtctjjgwebvz.hasUploadUrl(editor) || $_1dn8wtctjjgwebvz.hasUploadHandler(editor)) {
        var body = [$_78zck5d9jjgweby1.makeTab(editor, imageListCtrl)];
        if ($_1dn8wtctjjgwebvz.hasAdvTab(editor)) {
          body.push($_6dfy5vd3jjgwebxf.makeTab(editor));
        }
        if ($_1dn8wtctjjgwebvz.hasUploadUrl(editor) || $_1dn8wtctjjgwebvz.hasUploadHandler(editor)) {
          body.push($_71qd7mdbjjgweby7.makeTab(editor));
        }
        win = editor.windowManager.open({
          title: 'Insert/edit image',
          data: data,
          bodyType: 'tabpanel',
          body: body,
          onSubmit: curry(submitForm, editor)
        });
      } else {
        win = editor.windowManager.open({
          title: 'Insert/edit image',
          data: data,
          body: $_78zck5d9jjgweby1.getGeneralItems(editor, imageListCtrl),
          onSubmit: curry(submitForm, editor)
        });
      }
      $_ftlz5pdajjgweby4.syncSize(win);
    }
    function open() {
      $_1e8k4ncujjgwebw2.createImageList(editor, showDialog);
    }
    return { open: open };
  }

  var register = function (editor) {
    editor.addCommand('mceImage', Dialog(editor).open);
  };
  var $_3lypdlcrjjgwebvs = { register: register };

  var hasImageClass = function (node) {
    var className = node.attr('class');
    return className && /\bimage\b/.test(className);
  };
  var toggleContentEditableState = function (state) {
    return function (nodes) {
      var i = nodes.length, node;
      var toggleContentEditable = function (node) {
        node.attr('contenteditable', state ? 'true' : null);
      };
      while (i--) {
        node = nodes[i];
        if (hasImageClass(node)) {
          node.attr('contenteditable', state ? 'false' : null);
          global$2.each(node.getAll('figcaption'), toggleContentEditable);
        }
      }
    };
  };
  var setup = function (editor) {
    editor.on('preInit', function () {
      editor.parser.addNodeFilter('figure', toggleContentEditableState(true));
      editor.serializer.addNodeFilter('figure', toggleContentEditableState(false));
    });
  };
  var $_5op6l2dhjjgwebym = { setup: setup };

  var register$1 = function (editor) {
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
  };
  var $_dm869adijjgwebyn = { register: register$1 };

  global.add('image', function (editor) {
    $_5op6l2dhjjgwebym.setup(editor);
    $_dm869adijjgwebyn.register(editor);
    $_3lypdlcrjjgwebvs.register(editor);
  });
  function Plugin () {
  }

  return Plugin;

}());
})();
