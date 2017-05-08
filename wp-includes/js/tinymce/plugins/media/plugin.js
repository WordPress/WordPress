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
["tinymce.plugins.media.Plugin","tinymce.core.html.Node","tinymce.core.PluginManager","tinymce.core.util.Tools","tinymce.plugins.media.core.Nodes","tinymce.plugins.media.core.Sanitize","tinymce.plugins.media.core.UpdateHtml","tinymce.plugins.media.ui.Dialog","global!tinymce.util.Tools.resolve","tinymce.core.html.Writer","tinymce.core.html.SaxParser","tinymce.core.html.Schema","tinymce.plugins.media.core.VideoScript","tinymce.core.Env","tinymce.core.dom.DOMUtils","tinymce.plugins.media.core.Size","tinymce.core.util.Delay","tinymce.plugins.media.core.HtmlToData","tinymce.plugins.media.core.Service","tinymce.plugins.media.ui.SizeManager","tinymce.plugins.media.core.DataToHtml","tinymce.core.util.Promise","tinymce.plugins.media.core.Mime","tinymce.plugins.media.core.UrlPatterns"]
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
  'tinymce.core.html.Node',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.Node');
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
  'tinymce.core.html.Writer',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.Writer');
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
  'tinymce.core.html.SaxParser',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.SaxParser');
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
  'tinymce.core.html.Schema',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.Schema');
  }
);

/**
 * Sanitize.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.Sanitize',
  [
    'tinymce.core.util.Tools',
    'tinymce.core.html.Writer',
    'tinymce.core.html.SaxParser',
    'tinymce.core.html.Schema'
  ],
  function (Tools, Writer, SaxParser, Schema) {
    var sanitize = function (editor, html) {
      if (editor.settings.media_filter_html === false) {
        return html;
      }

      var writer = new Writer();
      var blocked;

      new SaxParser({
        validate: false,
        allow_conditional_comments: false,
        special: 'script,noscript',

        comment: function (text) {
          writer.comment(text);
        },

        cdata: function (text) {
          writer.cdata(text);
        },

        text: function (text, raw) {
          writer.text(text, raw);
        },

        start: function (name, attrs, empty) {
          blocked = true;

          if (name === 'script' || name === 'noscript') {
            return;
          }

          for (var i = 0; i < attrs.length; i++) {
            if (attrs[i].name.indexOf('on') === 0) {
              return;
            }

            if (attrs[i].name === 'style') {
              attrs[i].value = editor.dom.serializeStyle(editor.dom.parseStyle(attrs[i].value), name);
            }
          }

          writer.start(name, attrs, empty);
          blocked = false;
        },

        end: function (name) {
          if (blocked) {
            return;
          }

          writer.end(name);
        }
      }, new Schema({})).parse(html);

      return writer.getContent();
    };

    return {
      sanitize: sanitize
    };
  }
);
/**
 * VideoScript.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.VideoScript',
  [
  ],
  function () {
    var getVideoScriptMatch = function (prefixes, src) {
      // var prefixes = editor.settings.media_scripts;
      if (prefixes) {
        for (var i = 0; i < prefixes.length; i++) {
          if (src.indexOf(prefixes[i].filter) !== -1) {
            return prefixes[i];
          }
        }
      }
    };

    return {
      getVideoScriptMatch: getVideoScriptMatch
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
  'tinymce.core.Env',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.Env');
  }
);

/**
 * Nodes.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.Nodes',
  [
    'tinymce.plugins.media.core.Sanitize',
    'tinymce.plugins.media.core.VideoScript',
    'tinymce.core.html.Node',
    'tinymce.core.Env'
  ],
  function (Sanitize, VideoScript, Node, Env) {
    var createPlaceholderNode = function (editor, node) {
      var placeHolder;
      var name = node.name;

      placeHolder = new Node('img', 1);
      placeHolder.shortEnded = true;

      retainAttributesAndInnerHtml(editor, node, placeHolder);

      placeHolder.attr({
        width: node.attr('width') || "300",
        height: node.attr('height') || (name === "audio" ? "30" : "150"),
        style: node.attr('style'),
        src: Env.transparentSrc,
        "data-mce-object": name,
        "class": "mce-object mce-object-" + name
      });

      return placeHolder;
    };

    var createPreviewIframeNode = function (editor, node) {
      var previewWrapper;
      var previewNode;
      var shimNode;
      var name = node.name;

      previewWrapper = new Node('span', 1);
      previewWrapper.attr({
        contentEditable: 'false',
        style: node.attr('style'),
        "data-mce-object": name,
        "class": "mce-preview-object mce-object-" + name
      });

      retainAttributesAndInnerHtml(editor, node, previewWrapper);

      previewNode = new Node(name, 1);
      previewNode.attr({
        src: node.attr('src'),
        allowfullscreen: node.attr('allowfullscreen'),
        width: node.attr('width') || "300",
        height: node.attr('height') || (name === "audio" ? "30" : "150"),
        frameborder: '0'
      });

      shimNode = new Node('span', 1);
      shimNode.attr('class', 'mce-shim');

      previewWrapper.append(previewNode);
      previewWrapper.append(shimNode);

      return previewWrapper;
    };

    var retainAttributesAndInnerHtml = function (editor, sourceNode, targetNode) {
      var attrName;
      var attrValue;
      var attribs;
      var ai;
      var innerHtml;

      // Prefix all attributes except width, height and style since we
      // will add these to the placeholder
      attribs = sourceNode.attributes;
      ai = attribs.length;
      while (ai--) {
        attrName = attribs[ai].name;
        attrValue = attribs[ai].value;

        if (attrName !== "width" && attrName !== "height" && attrName !== "style") {
          if (attrName === "data" || attrName === "src") {
            attrValue = editor.convertURL(attrValue, attrName);
          }

          targetNode.attr('data-mce-p-' + attrName, attrValue);
        }
      }

      // Place the inner HTML contents inside an escaped attribute
      // This enables us to copy/paste the fake object
      innerHtml = sourceNode.firstChild && sourceNode.firstChild.value;
      if (innerHtml) {
        targetNode.attr("data-mce-html", escape(Sanitize.sanitize(editor, innerHtml)));
        targetNode.firstChild = null;
      }
    };

    var isWithinEphoxEmbed = function (node) {
      while ((node = node.parent)) {
        if (node.attr('data-ephox-embed-iri')) {
          return true;
        }
      }

      return false;
    };

    var placeHolderConverter = function (editor) {
      return function (nodes) {
        var i = nodes.length;
        var node;
        var videoScript;

        while (i--) {
          node = nodes[i];
          if (!node.parent) {
            continue;
          }

          if (node.parent.attr('data-mce-object')) {
            continue;
          }

          if (node.name === 'script') {
            videoScript = VideoScript.getVideoScriptMatch(editor.settings.media_scripts, node.attr('src'));
            if (!videoScript) {
              continue;
            }
          }

          if (videoScript) {
            if (videoScript.width) {
              node.attr('width', videoScript.width.toString());
            }

            if (videoScript.height) {
              node.attr('height', videoScript.height.toString());
            }
          }

          if (node.name === 'iframe' && editor.settings.media_live_embeds !== false && Env.ceFalse) {
            if (!isWithinEphoxEmbed(node)) {
              node.replace(createPreviewIframeNode(editor, node));
            }
          } else {
            if (!isWithinEphoxEmbed(node)) {
              node.replace(createPlaceholderNode(editor, node));
            }
          }
        }
      };
    };

    return {
      createPreviewIframeNode: createPreviewIframeNode,
      createPlaceholderNode: createPlaceholderNode,
      placeHolderConverter: placeHolderConverter
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
 * Size.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.Size',
  [
  ],
  function () {
    var trimPx = function (value) {
      return value.replace(/px$/, '');
    };

    var addPx = function (value) {
      return /^[0-9.]+$/.test(value) ? (value + 'px') : value;
    };

    var getSize = function (name) {
      return function (elm) {
        return elm ? trimPx(elm.style[name]) : '';
      };
    };

    var setSize = function (name) {
      return function (elm, value) {
        if (elm) {
          elm.style[name] = addPx(value);
        }
      };
    };

    return {
      getMaxWidth: getSize('maxWidth'),
      getMaxHeight: getSize('maxHeight'),
      setMaxWidth: setSize('maxWidth'),
      setMaxHeight: setSize('maxHeight')
    };
  }
);
/**
 * UpdateHtml.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.UpdateHtml',
  [
    'tinymce.core.html.Writer',
    'tinymce.core.html.SaxParser',
    'tinymce.core.html.Schema',
    'tinymce.core.dom.DOMUtils',
    'tinymce.plugins.media.core.Size'
  ],
  function (Writer, SaxParser, Schema, DOMUtils, Size) {
    var DOM = DOMUtils.DOM;

    var setAttributes = function (attrs, updatedAttrs) {
      var name;
      var i;
      var value;
      var attr;

      for (name in updatedAttrs) {
        value = "" + updatedAttrs[name];

        if (attrs.map[name]) {
          i = attrs.length;
          while (i--) {
            attr = attrs[i];

            if (attr.name === name) {
              if (value) {
                attrs.map[name] = value;
                attr.value = value;
              } else {
                delete attrs.map[name];
                attrs.splice(i, 1);
              }
            }
          }
        } else if (value) {
          attrs.push({
            name: name,
            value: value
          });

          attrs.map[name] = value;
        }
      }
    };

    var normalizeHtml = function (html) {
      var writer = new Writer();
      var parser = new SaxParser(writer);
      parser.parse(html);
      return writer.getContent();
    };

    var updateHtmlSax = function (html, data, updateAll) {
      var writer = new Writer();
      var sourceCount = 0;
      var hasImage;

      new SaxParser({
        validate: false,
        allow_conditional_comments: true,
        special: 'script,noscript',

        comment: function (text) {
          writer.comment(text);
        },

        cdata: function (text) {
          writer.cdata(text);
        },

        text: function (text, raw) {
          writer.text(text, raw);
        },

        start: function (name, attrs, empty) {
          switch (name) {
            case "video":
            case "object":
            case "embed":
            case "img":
            case "iframe":
              if (data.height !== undefined && data.width !== undefined) {
                setAttributes(attrs, {
                  width: data.width,
                  height: data.height
                });
              }
              break;
          }

          if (updateAll) {
            switch (name) {
              case "video":
                setAttributes(attrs, {
                  poster: data.poster,
                  src: ""
                });

                if (data.source2) {
                  setAttributes(attrs, {
                    src: ""
                  });
                }
                break;

              case "iframe":
                setAttributes(attrs, {
                  src: data.source1
                });
                break;

              case "source":
                sourceCount++;

                if (sourceCount <= 2) {
                  setAttributes(attrs, {
                    src: data["source" + sourceCount],
                    type: data["source" + sourceCount + "mime"]
                  });

                  if (!data["source" + sourceCount]) {
                    return;
                  }
                }
                break;

              case "img":
                if (!data.poster) {
                  return;
                }

                hasImage = true;
                break;
            }
          }

          writer.start(name, attrs, empty);
        },

        end: function (name) {
          if (name === "video" && updateAll) {
            for (var index = 1; index <= 2; index++) {
              if (data["source" + index]) {
                var attrs = [];
                attrs.map = {};

                if (sourceCount < index) {
                  setAttributes(attrs, {
                    src: data["source" + index],
                    type: data["source" + index + "mime"]
                  });

                  writer.start("source", attrs, true);
                }
              }
            }
          }

          if (data.poster && name === "object" && updateAll && !hasImage) {
            var imgAttrs = [];
            imgAttrs.map = {};

            setAttributes(imgAttrs, {
              src: data.poster,
              width: data.width,
              height: data.height
            });

            writer.start("img", imgAttrs, true);
          }

          writer.end(name);
        }
      }, new Schema({})).parse(html);

      return writer.getContent();
    };

    var isEphoxEmbed = function (html) {
      var fragment = DOM.createFragment(html);
      return DOM.getAttrib(fragment.firstChild, 'data-ephox-embed-iri') !== '';
    };

    var updateEphoxEmbed = function (html, data) {
      var fragment = DOM.createFragment(html);
      var div = fragment.firstChild;

      Size.setMaxWidth(div, data.width);
      Size.setMaxHeight(div, data.height);

      return normalizeHtml(div.outerHTML);
    };

    var updateHtml = function (html, data, updateAll) {
      return isEphoxEmbed(html) ? updateEphoxEmbed(html, data) : updateHtmlSax(html, data, updateAll);
    };

    return {
      updateHtml: updateHtml
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
  'tinymce.core.util.Delay',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Delay');
  }
);

/**
 * HtmlToData.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.HtmlToData',
  [
    'tinymce.core.util.Tools',
    'tinymce.core.html.SaxParser',
    'tinymce.core.html.Schema',
    'tinymce.core.dom.DOMUtils',
    'tinymce.plugins.media.core.VideoScript',
    'tinymce.plugins.media.core.Size'
  ],
  function (Tools, SaxParser, Schema, DOMUtils, VideoScript, Size) {
    var DOM = DOMUtils.DOM;

    var getEphoxEmbedIri = function (elm) {
      return DOM.getAttrib(elm, 'data-ephox-embed-iri');
    };

    var isEphoxEmbed = function (html) {
      var fragment = DOM.createFragment(html);
      return getEphoxEmbedIri(fragment.firstChild) !== '';
    };

    var htmlToDataSax = function (prefixes, html) {
      var data = {};

      new SaxParser({
        validate: false,
        allow_conditional_comments: true,
        special: 'script,noscript',
        start: function (name, attrs) {
          if (!data.source1 && name === "param") {
            data.source1 = attrs.map.movie;
          }

          if (name === "iframe" || name === "object" || name === "embed" || name === "video" || name === "audio") {
            if (!data.type) {
              data.type = name;
            }

            data = Tools.extend(attrs.map, data);
          }

          if (name === "script") {
            var videoScript = VideoScript.getVideoScriptMatch(prefixes, attrs.map.src);
            if (!videoScript) {
              return;
            }

            data = {
              type: "script",
              source1: attrs.map.src,
              width: videoScript.width,
              height: videoScript.height
            };
          }

          if (name === "source") {
            if (!data.source1) {
              data.source1 = attrs.map.src;
            } else if (!data.source2) {
              data.source2 = attrs.map.src;
            }
          }

          if (name === "img" && !data.poster) {
            data.poster = attrs.map.src;
          }
        }
      }).parse(html);

      data.source1 = data.source1 || data.src || data.data;
      data.source2 = data.source2 || '';
      data.poster = data.poster || '';

      return data;
    };

    var ephoxEmbedHtmlToData = function (html) {
      var fragment = DOM.createFragment(html);
      var div = fragment.firstChild;

      return {
        type: 'ephox-embed-iri',
        source1: getEphoxEmbedIri(div),
        source2: '',
        poster: '',
        width: Size.getMaxWidth(div),
        height: Size.getMaxHeight(div)
      };
    };

    var htmlToData = function (prefixes, html) {
      return isEphoxEmbed(html) ? ephoxEmbedHtmlToData(html) : htmlToDataSax(prefixes, html);
    };

    return {
      htmlToData: htmlToData
    };
  }
);
/**
 * Mime.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.Mime',
  [
  ],
  function () {
    var guess = function (url) {
      var mimes = {
        'mp3': 'audio/mpeg',
        'wav': 'audio/wav',
        'mp4': 'video/mp4',
        'webm': 'video/webm',
        'ogg': 'video/ogg',
        'swf': 'application/x-shockwave-flash'
      };
      var fileEnd = url.toLowerCase().split('.').pop();
      var mime = mimes[fileEnd];

      return mime ? mime : '';
    };

    return {
      guess: guess
    };
  }
);
/**
 * UrlPatterns.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.UrlPatterns',
  [
  ],
  function () {
    var urlPatterns = [
      {
        regex: /youtu\.be\/([\w\-.]+)/,
        type: 'iframe', w: 560, h: 314,
        url: '//www.youtube.com/embed/$1',
        allowFullscreen: true
      },
      {
        regex: /youtube\.com(.+)v=([^&]+)/,
        type: 'iframe', w: 560, h: 314,
        url: '//www.youtube.com/embed/$2',
        allowFullscreen: true
      },
      {
        regex: /youtube.com\/embed\/([a-z0-9\-_]+(?:\?.+)?)/i,
        type: 'iframe', w: 560, h: 314,
        url: '//www.youtube.com/embed/$1',
        allowFullscreen: true
      },
      {
        regex: /vimeo\.com\/([0-9]+)/,
        type: 'iframe', w: 425, h: 350,
        url: '//player.vimeo.com/video/$1?title=0&byline=0&portrait=0&color=8dc7dc',
        allowfullscreen: true
      },
      {
        regex: /vimeo\.com\/(.*)\/([0-9]+)/,
        type: "iframe", w: 425, h: 350,
        url: "//player.vimeo.com/video/$2?title=0&amp;byline=0",
        allowfullscreen: true
      },
      {
        regex: /maps\.google\.([a-z]{2,3})\/maps\/(.+)msid=(.+)/,
        type: 'iframe', w: 425, h: 350,
        url: '//maps.google.com/maps/ms?msid=$2&output=embed"',
        allowFullscreen: false
      },
      {
        regex: /dailymotion\.com\/video\/([^_]+)/,
        type: 'iframe', w: 480, h: 270,
        url: '//www.dailymotion.com/embed/video/$1',
        allowFullscreen: true
      }
    ];

    return {
      urlPatterns: urlPatterns
    };
  }
);
/**
 * DataToHtml.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.DataToHtml',
  [
    'tinymce.plugins.media.core.Mime',
    'tinymce.plugins.media.core.HtmlToData',
    'tinymce.plugins.media.core.UrlPatterns',
    'tinymce.plugins.media.core.VideoScript',
    'tinymce.plugins.media.core.UpdateHtml',
    'tinymce.core.util.Tools'
  ],
  function (Mime, HtmlToData, UrlPatterns, VideoScript, UpdateHtml, Tools) {
    var dataToHtml = function (editor, dataIn) {
      var html = '';
      var data = Tools.extend({}, dataIn);

      if (!data.source1) {
        Tools.extend(data, HtmlToData.htmlToData(editor.settings.media_scripts, data.embed));
        if (!data.source1) {
          return '';
        }
      }

      if (!data.source2) {
        data.source2 = '';
      }

      if (!data.poster) {
        data.poster = '';
      }

      data.source1 = editor.convertURL(data.source1, "source");
      data.source2 = editor.convertURL(data.source2, "source");
      data.source1mime = Mime.guess(data.source1);
      data.source2mime = Mime.guess(data.source2);
      data.poster = editor.convertURL(data.poster, "poster");

      Tools.each(UrlPatterns.urlPatterns, function (pattern) {
        var i;
        var url;

        var match = pattern.regex.exec(data.source1);

        if (match) {
          url = pattern.url;

          for (i = 0; match[i]; i++) {
            /*jshint loopfunc:true*/
            /*eslint no-loop-func:0 */
            url = url.replace('$' + i, function () {
              return match[i];
            });
          }

          data.source1 = url;
          data.type = pattern.type;
          data.allowFullscreen = pattern.allowFullscreen;
          data.width = data.width || pattern.w;
          data.height = data.height || pattern.h;
        }
      });

      if (data.embed) {
        html = UpdateHtml.updateHtml(data.embed, data, true);
      } else {
        var videoScript = VideoScript.getVideoScriptMatch(editor.settings.media_scripts, data.source1);
        if (videoScript) {
          data.type = 'script';
          data.width = videoScript.width;
          data.height = videoScript.height;
        }

        data.width = data.width || 300;
        data.height = data.height || 150;

        Tools.each(data, function (value, key) {
          data[key] = editor.dom.encode(value);
        });

        if (data.type === "iframe") {
          var allowFullscreen = data.allowFullscreen ? ' allowFullscreen="1"' : '';
          html +=
            '<iframe src="' + data.source1 +
            '" width="' + data.width +
            '" height="' + data.height +
            '"' + allowFullscreen + '></iframe>';
        } else if (data.source1mime === "application/x-shockwave-flash") {
          html +=
            '<object data="' + data.source1 +
            '" width="' + data.width +
            '" height="' + data.height +
            '" type="application/x-shockwave-flash">';

          if (data.poster) {
            html += '<img src="' + data.poster + '" width="' + data.width + '" height="' + data.height + '" />';
          }

          html += '</object>';
        } else if (data.source1mime.indexOf('audio') !== -1) {
          if (editor.settings.audio_template_callback) {
            html = editor.settings.audio_template_callback(data);
          } else {
            html += (
              '<audio controls="controls" src="' + data.source1 + '">' +
              (
                data.source2 ?
                  '\n<source src="' + data.source2 + '"' +
                  (data.source2mime ? ' type="' + data.source2mime + '"' : '') +
                  ' />\n' : '') +
              '</audio>'
            );
          }
        } else if (data.type === "script") {
          html += '<script src="' + data.source1 + '"></script>';
        } else {
          if (editor.settings.video_template_callback) {
            html = editor.settings.video_template_callback(data);
          } else {
            html = (
              '<video width="' + data.width +
              '" height="' + data.height + '"' +
              (data.poster ? ' poster="' + data.poster + '"' : '') + ' controls="controls">\n' +
              '<source src="' + data.source1 + '"' +
              (data.source1mime ? ' type="' + data.source1mime + '"' : '') + ' />\n' +
              (data.source2 ? '<source src="' + data.source2 + '"' +
                (data.source2mime ? ' type="' + data.source2mime + '"' : '') + ' />\n' : '') +
              '</video>'
            );
          }
        }
      }

      return html;
    };

    return {
      dataToHtml: dataToHtml
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
  'tinymce.core.util.Promise',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Promise');
  }
);

/**
 * Service.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.core.Service',
  [
    'tinymce.plugins.media.core.DataToHtml',
    'tinymce.core.util.Promise'
  ],
  function (DataToHtml, Promise) {
    var embedPromise = function (data, dataToHtml, handler) {
      var cache = {};
      return new Promise(function (res, rej) {
        var wrappedResolve = function (response) {
          if (response.html) {
            cache[data.source1] = response;
          }
          return res({
            url: data.source1,
            html: response.html ? response.html : dataToHtml(data)
          });
        };
        if (cache[data.source1]) {
          wrappedResolve(cache[data.source1]);
        } else {
          handler({ url: data.source1 }, wrappedResolve, rej);
        }
      });
    };

    var defaultPromise = function (data, dataToHtml) {
      return new Promise(function (res) {
        res({ html: dataToHtml(data), url: data.source1 });
      });
    };

    var loadedData = function (editor) {
      return function (data) {
        return DataToHtml.dataToHtml(editor, data);
      };
    };

    var getEmbedHtml = function (editor, data) {
      var embedHandler = editor.settings.media_url_resolver;

      return embedHandler ? embedPromise(data, loadedData(editor), embedHandler) : defaultPromise(data, loadedData(editor));
    };

    return {
      getEmbedHtml: getEmbedHtml
    };
  }
);
/**
 * SizeManager.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.media.ui.SizeManager',
  [
  ],
  function () {
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
          newHeight = Math.round((newWidth / oldWidth) * newHeight);

          if (!isNaN(newHeight)) {
            heightCtrl.value(newHeight);
          }
        } else {
          newWidth = Math.round((newHeight / oldHeight) * newWidth);

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

    var createUi = function (onChange) {
      var recalcSize = function () {
        onChange(function (win) {
          updateSize(win);
        });
      };

      return {
        type: 'container',
        label: 'Dimensions',
        layout: 'flex',
        align: 'center',
        spacing: 5,
        items: [
          {
            name: 'width', type: 'textbox', maxLength: 5, size: 5,
            onchange: recalcSize, ariaLabel: 'Width'
          },
          { type: 'label', text: 'x' },
          {
            name: 'height', type: 'textbox', maxLength: 5, size: 5,
            onchange: recalcSize, ariaLabel: 'Height'
          },
          { name: 'constrain', type: 'checkbox', checked: true, text: 'Constrain proportions' }
        ]
      };
    };

    return {
      createUi: createUi,
      syncSize: syncSize,
      updateSize: updateSize
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
  'tinymce.plugins.media.ui.Dialog',
  [
    'tinymce.core.util.Delay',
    'tinymce.plugins.media.core.HtmlToData',
    'tinymce.plugins.media.core.UpdateHtml',
    'tinymce.plugins.media.core.Service',
    'tinymce.plugins.media.core.Size',
    'tinymce.core.util.Tools',
    'tinymce.core.Env',
    'tinymce.plugins.media.ui.SizeManager'
  ],
  function (Delay, HtmlToData, UpdateHtml, Service, Size, Tools, Env, SizeManager) {
    var embedChange = (Env.ie && Env.ie <= 8) ? 'onChange' : 'onInput';

    var handleError = function (editor) {
      return function (error) {
        var errorMessage = error && error.msg ?
          'Media embed handler error: ' + error.msg :
          'Media embed handler threw unknown error.';
        editor.notificationManager.open({ type: 'error', text: errorMessage });
      };
    };

    var getData = function (editor) {
      var element = editor.selection.getNode();
      var dataEmbed = element.getAttribute('data-ephox-embed-iri');

      if (dataEmbed) {
        return {
          source1: dataEmbed,
          'data-ephox-embed-iri': dataEmbed,
          width: Size.getMaxWidth(element),
          height: Size.getMaxHeight(element)
        };
      }

      return element.getAttribute('data-mce-object') ?
        HtmlToData.htmlToData(editor.settings.media_scripts, editor.serializer.serialize(element, { selection: true })) :
        {};
    };

    var getSource = function (editor) {
      var elm = editor.selection.getNode();

      if (elm.getAttribute('data-mce-object') || elm.getAttribute('data-ephox-embed-iri')) {
        return editor.selection.getContent();
      }
    };

    var addEmbedHtml = function (win, editor) {
      return function (response) {
        var html = response.html;
        var embed = win.find('#embed')[0];
        var data = Tools.extend(HtmlToData.htmlToData(editor.settings.media_scripts, html), { source1: response.url });
        win.fromJSON(data);

        if (embed) {
          embed.value(html);
          SizeManager.updateSize(win);
        }
      };
    };

    var selectPlaceholder = function (editor, beforeObjects) {
      var i;
      var y;
      var afterObjects = editor.dom.select('img[data-mce-object]');

      // Find new image placeholder so we can select it
      for (i = 0; i < beforeObjects.length; i++) {
        for (y = afterObjects.length - 1; y >= 0; y--) {
          if (beforeObjects[i] === afterObjects[y]) {
            afterObjects.splice(y, 1);
          }
        }
      }

      editor.selection.select(afterObjects[0]);
    };

    var handleInsert = function (editor, html) {
      var beforeObjects = editor.dom.select('img[data-mce-object]');

      editor.insertContent(html);
      selectPlaceholder(editor, beforeObjects);
      editor.nodeChanged();
    };

    var submitForm = function (win, editor) {
      var data = win.toJSON();

      data.embed = UpdateHtml.updateHtml(data.embed, data);

      if (data.embed) {
        handleInsert(editor, data.embed);
      } else {
        Service.getEmbedHtml(editor, data)
          .then(function (response) {
            handleInsert(editor, response.html);
          })["catch"](handleError(editor));
      }
    };

    var populateMeta = function (win, meta) {
      Tools.each(meta, function (value, key) {
        win.find('#' + key).value(value);
      });
    };

    var showDialog = function (editor) {
      var win;
      var data;

      var generalFormItems = [
        {
          name: 'source1',
          type: 'filepicker',
          filetype: 'media',
          size: 40,
          autofocus: true,
          label: 'Source',
          onpaste: function () {
            setTimeout(function () {
              Service.getEmbedHtml(editor, win.toJSON())
                .then(
                addEmbedHtml(win, editor)
                )["catch"](handleError(editor));
            }, 1);
          },
          onchange: function (e) {
            Service.getEmbedHtml(editor, win.toJSON())
              .then(
              addEmbedHtml(win, editor)
              )["catch"](handleError(editor));

            populateMeta(win, e.meta);
          },
          onbeforecall: function (e) {
            e.meta = win.toJSON();
          }
        }
      ];

      var advancedFormItems = [];

      var reserialise = function (update) {
        update(win);
        data = win.toJSON();
        win.find('#embed').value(UpdateHtml.updateHtml(data.embed, data));
      };

      if (editor.settings.media_alt_source !== false) {
        advancedFormItems.push({ name: 'source2', type: 'filepicker', filetype: 'media', size: 40, label: 'Alternative source' });
      }

      if (editor.settings.media_poster !== false) {
        advancedFormItems.push({ name: 'poster', type: 'filepicker', filetype: 'image', size: 40, label: 'Poster' });
      }

      if (editor.settings.media_dimensions !== false) {
        var control = SizeManager.createUi(reserialise);
        generalFormItems.push(control);
      }

      data = getData(editor);

      var embedTextBox = {
        id: 'mcemediasource',
        type: 'textbox',
        flex: 1,
        name: 'embed',
        value: getSource(editor),
        multiline: true,
        rows: 5,
        label: 'Source'
      };

      var updateValueOnChange = function () {
        data = Tools.extend({}, HtmlToData.htmlToData(editor.settings.media_scripts, this.value()));
        this.parent().parent().fromJSON(data);
      };

      embedTextBox[embedChange] = updateValueOnChange;

      win = editor.windowManager.open({
        title: 'Insert/edit media',
        data: data,
        bodyType: 'tabpanel',
        body: [
          {
            title: 'General',
            type: "form",
            items: generalFormItems
          },

          {
            title: 'Embed',
            type: "container",
            layout: 'flex',
            direction: 'column',
            align: 'stretch',
            padding: 10,
            spacing: 10,
            items: [
              {
                type: 'label',
                text: 'Paste your embed code below:',
                forId: 'mcemediasource'
              },
              embedTextBox
            ]
          },

          {
            title: 'Advanced',
            type: "form",
            items: advancedFormItems
          }
        ],
        onSubmit: function () {
          SizeManager.updateSize(win);
          submitForm(win, editor);
        }
      });

      SizeManager.syncSize(win);
    };

    return {
      showDialog: showDialog
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
  'tinymce.plugins.media.Plugin',
  [
    'tinymce.core.html.Node',
    'tinymce.core.PluginManager',
    'tinymce.core.util.Tools',
    'tinymce.plugins.media.core.Nodes',
    'tinymce.plugins.media.core.Sanitize',
    'tinymce.plugins.media.core.UpdateHtml',
    'tinymce.plugins.media.ui.Dialog'
  ],
  function (Node, PluginManager, Tools, Nodes, Sanitize, UpdateHtml, Dialog) {
    var Plugin = function (editor) {
      editor.on('ResolveName', function (e) {
        var name;

        if (e.target.nodeType === 1 && (name = e.target.getAttribute("data-mce-object"))) {
          e.name = name;
        }
      });

      editor.on('preInit', function () {
        // Make sure that any messy HTML is retained inside these
        var specialElements = editor.schema.getSpecialElements();
        Tools.each('video audio iframe object'.split(' '), function (name) {
          specialElements[name] = new RegExp('<\/' + name + '[^>]*>', 'gi');
        });

        // Allow elements
        //editor.schema.addValidElements(
        //  'object[id|style|width|height|classid|codebase|*],embed[id|style|width|height|type|src|*],video[*],audio[*]'
        //);

        // Set allowFullscreen attribs as boolean
        var boolAttrs = editor.schema.getBoolAttrs();
        Tools.each('webkitallowfullscreen mozallowfullscreen allowfullscreen'.split(' '), function (name) {
          boolAttrs[name] = {};
        });

        // Converts iframe, video etc into placeholder images
        editor.parser.addNodeFilter('iframe,video,audio,object,embed,script',
          Nodes.placeHolderConverter(editor));

        // Replaces placeholder images with real elements for video, object, iframe etc
        editor.serializer.addAttributeFilter('data-mce-object', function (nodes, name) {
          var i = nodes.length;
          var node;
          var realElm;
          var ai;
          var attribs;
          var innerHtml;
          var innerNode;
          var realElmName;
          var className;

          while (i--) {
            node = nodes[i];
            if (!node.parent) {
              continue;
            }

            realElmName = node.attr(name);
            realElm = new Node(realElmName, 1);

            // Add width/height to everything but audio
            if (realElmName !== "audio" && realElmName !== "script") {
              className = node.attr('class');
              if (className && className.indexOf('mce-preview-object') !== -1) {
                realElm.attr({
                  width: node.firstChild.attr('width'),
                  height: node.firstChild.attr('height')
                });
              } else {
                realElm.attr({
                  width: node.attr('width'),
                  height: node.attr('height')
                });
              }
            }

            realElm.attr({
              style: node.attr('style')
            });

            // Unprefix all placeholder attributes
            attribs = node.attributes;
            ai = attribs.length;
            while (ai--) {
              var attrName = attribs[ai].name;

              if (attrName.indexOf('data-mce-p-') === 0) {
                realElm.attr(attrName.substr(11), attribs[ai].value);
              }
            }

            if (realElmName === "script") {
              realElm.attr('type', 'text/javascript');
            }

            // Inject innerhtml
            innerHtml = node.attr('data-mce-html');
            if (innerHtml) {
              innerNode = new Node('#text', 3);
              innerNode.raw = true;
              innerNode.value = Sanitize.sanitize(editor, unescape(innerHtml));
              realElm.append(innerNode);
            }

            node.replace(realElm);
          }
        });
      });

      editor.on('click keyup', function () {
        var selectedNode = editor.selection.getNode();

        if (selectedNode && editor.dom.hasClass(selectedNode, 'mce-preview-object')) {
          if (editor.dom.getAttrib(selectedNode, 'data-mce-selected')) {
            selectedNode.setAttribute('data-mce-selected', '2');
          }
        }
      });

      editor.on('ObjectSelected', function (e) {
        var objectType = e.target.getAttribute('data-mce-object');

        if (objectType === "audio" || objectType === "script") {
          e.preventDefault();
        }
      });

      editor.on('objectResized', function (e) {
        var target = e.target;
        var html;

        if (target.getAttribute('data-mce-object')) {
          html = target.getAttribute('data-mce-html');
          if (html) {
            html = unescape(html);
            target.setAttribute('data-mce-html', escape(
              UpdateHtml.updateHtml(html, {
                width: e.width,
                height: e.height
              })
            ));
          }
        }
      });

      this.showDialog = function () {
        Dialog.showDialog(editor);
      };

      editor.addButton('media', {
        tooltip: 'Insert/edit media',
        onclick: this.showDialog,
        stateSelector: ['img[data-mce-object]', 'span[data-mce-object]', 'div[data-ephox-embed-iri]']
      });

      editor.addMenuItem('media', {
        icon: 'media',
        text: 'Media',
        onclick: this.showDialog,
        context: 'insert',
        prependToContext: true
      });

      editor.on('setContent', function () {
        // TODO: This shouldn't be needed there should be a way to mark bogus
        // elements so they are never removed except external save
        editor.$('span.mce-preview-object').each(function (index, elm) {
          var $elm = editor.$(elm);

          if ($elm.find('span.mce-shim', elm).length === 0) {
            $elm.append('<span class="mce-shim"></span>');
          }
        });
      });

      editor.addCommand('mceMedia', this.showDialog);
    };

    PluginManager.add('media', Plugin);

    return function () { };
  }
);


dem('tinymce.plugins.media.Plugin')();
})();
