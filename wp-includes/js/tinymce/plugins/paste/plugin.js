(function () {
var paste = (function () {
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

  var hasProPlugin = function (editor) {
    if (/(^|[ ,])powerpaste([, ]|$)/.test(editor.settings.plugins) && global.get('powerpaste')) {
      if (typeof window.console !== 'undefined' && window.console.log) {
        window.console.log('PowerPaste is incompatible with Paste plugin! Remove \'paste\' from the \'plugins\' option.');
      }
      return true;
    } else {
      return false;
    }
  };
  var $_15bf6siejjgwect1 = { hasProPlugin: hasProPlugin };

  var get = function (clipboard, quirks) {
    return {
      clipboard: clipboard,
      quirks: quirks
    };
  };
  var $_6gtliyigjjgwecte = { get: get };

  var firePastePreProcess = function (editor, html, internal, isWordHtml) {
    return editor.fire('PastePreProcess', {
      content: html,
      internal: internal,
      wordContent: isWordHtml
    });
  };
  var firePastePostProcess = function (editor, node, internal, isWordHtml) {
    return editor.fire('PastePostProcess', {
      node: node,
      internal: internal,
      wordContent: isWordHtml
    });
  };
  var firePastePlainTextToggle = function (editor, state) {
    return editor.fire('PastePlainTextToggle', { state: state });
  };
  var firePaste = function (editor, ieFake) {
    return editor.fire('paste', { ieFake: ieFake });
  };
  var $_8tki3zijjjgwectj = {
    firePastePreProcess: firePastePreProcess,
    firePastePostProcess: firePastePostProcess,
    firePastePlainTextToggle: firePastePlainTextToggle,
    firePaste: firePaste
  };

  var shouldPlainTextInform = function (editor) {
    return editor.getParam('paste_plaintext_inform', true);
  };
  var shouldBlockDrop = function (editor) {
    return editor.getParam('paste_block_drop', false);
  };
  var shouldPasteDataImages = function (editor) {
    return editor.getParam('paste_data_images', false);
  };
  var shouldFilterDrop = function (editor) {
    return editor.getParam('paste_filter_drop', true);
  };
  var getPreProcess = function (editor) {
    return editor.getParam('paste_preprocess');
  };
  var getPostProcess = function (editor) {
    return editor.getParam('paste_postprocess');
  };
  var getWebkitStyles = function (editor) {
    return editor.getParam('paste_webkit_styles');
  };
  var shouldRemoveWebKitStyles = function (editor) {
    return editor.getParam('paste_remove_styles_if_webkit', true);
  };
  var shouldMergeFormats = function (editor) {
    return editor.getParam('paste_merge_formats', true);
  };
  var isSmartPasteEnabled = function (editor) {
    return editor.getParam('smart_paste', true);
  };
  var isPasteAsTextEnabled = function (editor) {
    return editor.getParam('paste_as_text', false);
  };
  var getRetainStyleProps = function (editor) {
    return editor.getParam('paste_retain_style_properties');
  };
  var getWordValidElements = function (editor) {
    var defaultValidElements = '-strong/b,-em/i,-u,-span,-p,-ol,-ul,-li,-h1,-h2,-h3,-h4,-h5,-h6,' + '-p/div,-a[href|name],sub,sup,strike,br,del,table[width],tr,' + 'td[colspan|rowspan|width],th[colspan|rowspan|width],thead,tfoot,tbody';
    return editor.getParam('paste_word_valid_elements', defaultValidElements);
  };
  var shouldConvertWordFakeLists = function (editor) {
    return editor.getParam('paste_convert_word_fake_lists', true);
  };
  var shouldUseDefaultFilters = function (editor) {
    return editor.getParam('paste_enable_default_filters', true);
  };
  var $_xr8b0ikjjgwectl = {
    shouldPlainTextInform: shouldPlainTextInform,
    shouldBlockDrop: shouldBlockDrop,
    shouldPasteDataImages: shouldPasteDataImages,
    shouldFilterDrop: shouldFilterDrop,
    getPreProcess: getPreProcess,
    getPostProcess: getPostProcess,
    getWebkitStyles: getWebkitStyles,
    shouldRemoveWebKitStyles: shouldRemoveWebKitStyles,
    shouldMergeFormats: shouldMergeFormats,
    isSmartPasteEnabled: isSmartPasteEnabled,
    isPasteAsTextEnabled: isPasteAsTextEnabled,
    getRetainStyleProps: getRetainStyleProps,
    getWordValidElements: getWordValidElements,
    shouldConvertWordFakeLists: shouldConvertWordFakeLists,
    shouldUseDefaultFilters: shouldUseDefaultFilters
  };

  var shouldInformUserAboutPlainText = function (editor, userIsInformedState) {
    return userIsInformedState.get() === false && $_xr8b0ikjjgwectl.shouldPlainTextInform(editor);
  };
  var displayNotification = function (editor, message) {
    editor.notificationManager.open({
      text: editor.translate(message),
      type: 'info'
    });
  };
  var togglePlainTextPaste = function (editor, clipboard, userIsInformedState) {
    if (clipboard.pasteFormat.get() === 'text') {
      clipboard.pasteFormat.set('html');
      $_8tki3zijjjgwectj.firePastePlainTextToggle(editor, false);
    } else {
      clipboard.pasteFormat.set('text');
      $_8tki3zijjjgwectj.firePastePlainTextToggle(editor, true);
      if (shouldInformUserAboutPlainText(editor, userIsInformedState)) {
        displayNotification(editor, 'Paste is now in plain text mode. Contents will now be pasted as plain text until you toggle this option off.');
        userIsInformedState.set(true);
      }
    }
    editor.focus();
  };
  var $_2j7vw7iijjgwecti = { togglePlainTextPaste: togglePlainTextPaste };

  var register = function (editor, clipboard, userIsInformedState) {
    editor.addCommand('mceTogglePlainTextPaste', function () {
      $_2j7vw7iijjgwecti.togglePlainTextPaste(editor, clipboard, userIsInformedState);
    });
    editor.addCommand('mceInsertClipboardContent', function (ui, value) {
      if (value.content) {
        clipboard.pasteHtml(value.content, value.internal);
      }
      if (value.text) {
        clipboard.pasteText(value.text);
      }
    });
  };
  var $_fldd1mihjjgwecth = { register: register };

  var global$1 = tinymce.util.Tools.resolve('tinymce.Env');

  var global$2 = tinymce.util.Tools.resolve('tinymce.util.Delay');

  var global$3 = tinymce.util.Tools.resolve('tinymce.util.Tools');

  var global$4 = tinymce.util.Tools.resolve('tinymce.util.VK');

  var internalMimeType = 'x-tinymce/html';
  var internalMark = '<!-- ' + internalMimeType + ' -->';
  var mark = function (html) {
    return internalMark + html;
  };
  var unmark = function (html) {
    return html.replace(internalMark, '');
  };
  var isMarked = function (html) {
    return html.indexOf(internalMark) !== -1;
  };
  var $_4x13hjirjjgwecu1 = {
    mark: mark,
    unmark: unmark,
    isMarked: isMarked,
    internalHtmlMime: function () {
      return internalMimeType;
    }
  };

  var global$5 = tinymce.util.Tools.resolve('tinymce.html.Entities');

  var isPlainText = function (text) {
    return !/<(?:\/?(?!(?:div|p|br|span)>)\w+|(?:(?!(?:span style="white-space:\s?pre;?">)|br\s?\/>))\w+\s[^>]+)>/i.test(text);
  };
  var toBRs = function (text) {
    return text.replace(/\r?\n/g, '<br>');
  };
  var openContainer = function (rootTag, rootAttrs) {
    var key;
    var attrs = [];
    var tag = '<' + rootTag;
    if (typeof rootAttrs === 'object') {
      for (key in rootAttrs) {
        if (rootAttrs.hasOwnProperty(key)) {
          attrs.push(key + '="' + global$5.encodeAllRaw(rootAttrs[key]) + '"');
        }
      }
      if (attrs.length) {
        tag += ' ' + attrs.join(' ');
      }
    }
    return tag + '>';
  };
  var toBlockElements = function (text, rootTag, rootAttrs) {
    var blocks = text.split(/\n\n/);
    var tagOpen = openContainer(rootTag, rootAttrs);
    var tagClose = '</' + rootTag + '>';
    var paragraphs = global$3.map(blocks, function (p) {
      return p.split(/\n/).join('<br />');
    });
    var stitch = function (p) {
      return tagOpen + p + tagClose;
    };
    return paragraphs.length === 1 ? paragraphs[0] : global$3.map(paragraphs, stitch).join('');
  };
  var convert = function (text, rootTag, rootAttrs) {
    return rootTag ? toBlockElements(text, rootTag, rootAttrs) : toBRs(text);
  };
  var $_4h3hnrisjjgwecu2 = {
    isPlainText: isPlainText,
    convert: convert,
    toBRs: toBRs,
    toBlockElements: toBlockElements
  };

  var global$6 = tinymce.util.Tools.resolve('tinymce.html.DomParser');

  var global$7 = tinymce.util.Tools.resolve('tinymce.html.Node');

  var global$8 = tinymce.util.Tools.resolve('tinymce.html.Schema');

  var global$9 = tinymce.util.Tools.resolve('tinymce.html.Serializer');

  function filter(content, items) {
    global$3.each(items, function (v) {
      if (v.constructor === RegExp) {
        content = content.replace(v, '');
      } else {
        content = content.replace(v[0], v[1]);
      }
    });
    return content;
  }
  function innerText(html) {
    var schema = global$8();
    var domParser = global$6({}, schema);
    var text = '';
    var shortEndedElements = schema.getShortEndedElements();
    var ignoreElements = global$3.makeMap('script noscript style textarea video audio iframe object', ' ');
    var blockElements = schema.getBlockElements();
    function walk(node) {
      var name$$1 = node.name, currentNode = node;
      if (name$$1 === 'br') {
        text += '\n';
        return;
      }
      if (shortEndedElements[name$$1]) {
        text += ' ';
      }
      if (ignoreElements[name$$1]) {
        text += ' ';
        return;
      }
      if (node.type === 3) {
        text += node.value;
      }
      if (!node.shortEnded) {
        if (node = node.firstChild) {
          do {
            walk(node);
          } while (node = node.next);
        }
      }
      if (blockElements[name$$1] && currentNode.next) {
        text += '\n';
        if (name$$1 === 'p') {
          text += '\n';
        }
      }
    }
    html = filter(html, [/<!\[[^\]]+\]>/g]);
    walk(domParser.parse(html));
    return text;
  }
  function trimHtml(html) {
    function trimSpaces(all, s1, s2) {
      if (!s1 && !s2) {
        return ' ';
      }
      return '\xA0';
    }
    html = filter(html, [
      /^[\s\S]*<body[^>]*>\s*|\s*<\/body[^>]*>[\s\S]*$/ig,
      /<!--StartFragment-->|<!--EndFragment-->/g,
      [
        /( ?)<span class="Apple-converted-space">\u00a0<\/span>( ?)/g,
        trimSpaces
      ],
      /<br class="Apple-interchange-newline">/g,
      /<br>$/i
    ]);
    return html;
  }
  function createIdGenerator(prefix) {
    var count = 0;
    return function () {
      return prefix + count++;
    };
  }
  var isMsEdge = function () {
    return navigator.userAgent.indexOf(' Edge/') !== -1;
  };
  var $_4bi2o9j0jjgwecui = {
    filter: filter,
    innerText: innerText,
    trimHtml: trimHtml,
    createIdGenerator: createIdGenerator,
    isMsEdge: isMsEdge
  };

  function isWordContent(content) {
    return /<font face="Times New Roman"|class="?Mso|style="[^"]*\bmso-|style='[^'']*\bmso-|w:WordDocument/i.test(content) || /class="OutlineElement/.test(content) || /id="?docs\-internal\-guid\-/.test(content);
  }
  function isNumericList(text) {
    var found, patterns;
    patterns = [
      /^[IVXLMCD]{1,2}\.[ \u00a0]/,
      /^[ivxlmcd]{1,2}\.[ \u00a0]/,
      /^[a-z]{1,2}[\.\)][ \u00a0]/,
      /^[A-Z]{1,2}[\.\)][ \u00a0]/,
      /^[0-9]+\.[ \u00a0]/,
      /^[\u3007\u4e00\u4e8c\u4e09\u56db\u4e94\u516d\u4e03\u516b\u4e5d]+\.[ \u00a0]/,
      /^[\u58f1\u5f10\u53c2\u56db\u4f0d\u516d\u4e03\u516b\u4e5d\u62fe]+\.[ \u00a0]/
    ];
    text = text.replace(/^[\u00a0 ]+/, '');
    global$3.each(patterns, function (pattern) {
      if (pattern.test(text)) {
        found = true;
        return false;
      }
    });
    return found;
  }
  function isBulletList(text) {
    return /^[\s\u00a0]*[\u2022\u00b7\u00a7\u25CF]\s*/.test(text);
  }
  function convertFakeListsToProperLists(node) {
    var currentListNode, prevListNode, lastLevel = 1;
    function getText(node) {
      var txt = '';
      if (node.type === 3) {
        return node.value;
      }
      if (node = node.firstChild) {
        do {
          txt += getText(node);
        } while (node = node.next);
      }
      return txt;
    }
    function trimListStart(node, regExp) {
      if (node.type === 3) {
        if (regExp.test(node.value)) {
          node.value = node.value.replace(regExp, '');
          return false;
        }
      }
      if (node = node.firstChild) {
        do {
          if (!trimListStart(node, regExp)) {
            return false;
          }
        } while (node = node.next);
      }
      return true;
    }
    function removeIgnoredNodes(node) {
      if (node._listIgnore) {
        node.remove();
        return;
      }
      if (node = node.firstChild) {
        do {
          removeIgnoredNodes(node);
        } while (node = node.next);
      }
    }
    function convertParagraphToLi(paragraphNode, listName, start) {
      var level = paragraphNode._listLevel || lastLevel;
      if (level !== lastLevel) {
        if (level < lastLevel) {
          if (currentListNode) {
            currentListNode = currentListNode.parent.parent;
          }
        } else {
          prevListNode = currentListNode;
          currentListNode = null;
        }
      }
      if (!currentListNode || currentListNode.name !== listName) {
        prevListNode = prevListNode || currentListNode;
        currentListNode = new global$7(listName, 1);
        if (start > 1) {
          currentListNode.attr('start', '' + start);
        }
        paragraphNode.wrap(currentListNode);
      } else {
        currentListNode.append(paragraphNode);
      }
      paragraphNode.name = 'li';
      if (level > lastLevel && prevListNode) {
        prevListNode.lastChild.append(currentListNode);
      }
      lastLevel = level;
      removeIgnoredNodes(paragraphNode);
      trimListStart(paragraphNode, /^\u00a0+/);
      trimListStart(paragraphNode, /^\s*([\u2022\u00b7\u00a7\u25CF]|\w+\.)/);
      trimListStart(paragraphNode, /^\u00a0+/);
    }
    var elements = [];
    var child = node.firstChild;
    while (typeof child !== 'undefined' && child !== null) {
      elements.push(child);
      child = child.walk();
      if (child !== null) {
        while (typeof child !== 'undefined' && child.parent !== node) {
          child = child.walk();
        }
      }
    }
    for (var i = 0; i < elements.length; i++) {
      node = elements[i];
      if (node.name === 'p' && node.firstChild) {
        var nodeText = getText(node);
        if (isBulletList(nodeText)) {
          convertParagraphToLi(node, 'ul');
          continue;
        }
        if (isNumericList(nodeText)) {
          var matches = /([0-9]+)\./.exec(nodeText);
          var start = 1;
          if (matches) {
            start = parseInt(matches[1], 10);
          }
          convertParagraphToLi(node, 'ol', start);
          continue;
        }
        if (node._listLevel) {
          convertParagraphToLi(node, 'ul', 1);
          continue;
        }
        currentListNode = null;
      } else {
        prevListNode = currentListNode;
        currentListNode = null;
      }
    }
  }
  function filterStyles(editor, validStyles, node, styleValue) {
    var outputStyles = {}, matches;
    var styles = editor.dom.parseStyle(styleValue);
    global$3.each(styles, function (value, name) {
      switch (name) {
      case 'mso-list':
        matches = /\w+ \w+([0-9]+)/i.exec(styleValue);
        if (matches) {
          node._listLevel = parseInt(matches[1], 10);
        }
        if (/Ignore/i.test(value) && node.firstChild) {
          node._listIgnore = true;
          node.firstChild._listIgnore = true;
        }
        break;
      case 'horiz-align':
        name = 'text-align';
        break;
      case 'vert-align':
        name = 'vertical-align';
        break;
      case 'font-color':
      case 'mso-foreground':
        name = 'color';
        break;
      case 'mso-background':
      case 'mso-highlight':
        name = 'background';
        break;
      case 'font-weight':
      case 'font-style':
        if (value !== 'normal') {
          outputStyles[name] = value;
        }
        return;
      case 'mso-element':
        if (/^(comment|comment-list)$/i.test(value)) {
          node.remove();
          return;
        }
        break;
      }
      if (name.indexOf('mso-comment') === 0) {
        node.remove();
        return;
      }
      if (name.indexOf('mso-') === 0) {
        return;
      }
      if ($_xr8b0ikjjgwectl.getRetainStyleProps(editor) === 'all' || validStyles && validStyles[name]) {
        outputStyles[name] = value;
      }
    });
    if (/(bold)/i.test(outputStyles['font-weight'])) {
      delete outputStyles['font-weight'];
      node.wrap(new global$7('b', 1));
    }
    if (/(italic)/i.test(outputStyles['font-style'])) {
      delete outputStyles['font-style'];
      node.wrap(new global$7('i', 1));
    }
    outputStyles = editor.dom.serializeStyle(outputStyles, node.name);
    if (outputStyles) {
      return outputStyles;
    }
    return null;
  }
  var filterWordContent = function (editor, content) {
    var retainStyleProperties, validStyles;
    retainStyleProperties = $_xr8b0ikjjgwectl.getRetainStyleProps(editor);
    if (retainStyleProperties) {
      validStyles = global$3.makeMap(retainStyleProperties.split(/[, ]/));
    }
    content = $_4bi2o9j0jjgwecui.filter(content, [
      /<br class="?Apple-interchange-newline"?>/gi,
      /<b[^>]+id="?docs-internal-[^>]*>/gi,
      /<!--[\s\S]+?-->/gi,
      /<(!|script[^>]*>.*?<\/script(?=[>\s])|\/?(\?xml(:\w+)?|img|meta|link|style|\w:\w+)(?=[\s\/>]))[^>]*>/gi,
      [
        /<(\/?)s>/gi,
        '<$1strike>'
      ],
      [
        /&nbsp;/gi,
        '\xA0'
      ],
      [
        /<span\s+style\s*=\s*"\s*mso-spacerun\s*:\s*yes\s*;?\s*"\s*>([\s\u00a0]*)<\/span>/gi,
        function (str, spaces) {
          return spaces.length > 0 ? spaces.replace(/./, ' ').slice(Math.floor(spaces.length / 2)).split('').join('\xA0') : '';
        }
      ]
    ]);
    var validElements = $_xr8b0ikjjgwectl.getWordValidElements(editor);
    var schema = global$8({
      valid_elements: validElements,
      valid_children: '-li[p]'
    });
    global$3.each(schema.elements, function (rule) {
      if (!rule.attributes.class) {
        rule.attributes.class = {};
        rule.attributesOrder.push('class');
      }
      if (!rule.attributes.style) {
        rule.attributes.style = {};
        rule.attributesOrder.push('style');
      }
    });
    var domParser = global$6({}, schema);
    domParser.addAttributeFilter('style', function (nodes) {
      var i = nodes.length, node;
      while (i--) {
        node = nodes[i];
        node.attr('style', filterStyles(editor, validStyles, node, node.attr('style')));
        if (node.name === 'span' && node.parent && !node.attributes.length) {
          node.unwrap();
        }
      }
    });
    domParser.addAttributeFilter('class', function (nodes) {
      var i = nodes.length, node, className;
      while (i--) {
        node = nodes[i];
        className = node.attr('class');
        if (/^(MsoCommentReference|MsoCommentText|msoDel)$/i.test(className)) {
          node.remove();
        }
        node.attr('class', null);
      }
    });
    domParser.addNodeFilter('del', function (nodes) {
      var i = nodes.length;
      while (i--) {
        nodes[i].remove();
      }
    });
    domParser.addNodeFilter('a', function (nodes) {
      var i = nodes.length, node, href, name;
      while (i--) {
        node = nodes[i];
        href = node.attr('href');
        name = node.attr('name');
        if (href && href.indexOf('#_msocom_') !== -1) {
          node.remove();
          continue;
        }
        if (href && href.indexOf('file://') === 0) {
          href = href.split('#')[1];
          if (href) {
            href = '#' + href;
          }
        }
        if (!href && !name) {
          node.unwrap();
        } else {
          if (name && !/^_?(?:toc|edn|ftn)/i.test(name)) {
            node.unwrap();
            continue;
          }
          node.attr({
            href: href,
            name: name
          });
        }
      }
    });
    var rootNode = domParser.parse(content);
    if ($_xr8b0ikjjgwectl.shouldConvertWordFakeLists(editor)) {
      convertFakeListsToProperLists(rootNode);
    }
    content = global$9({ validate: editor.settings.validate }, schema).serialize(rootNode);
    return content;
  };
  var preProcess = function (editor, content) {
    return $_xr8b0ikjjgwectl.shouldUseDefaultFilters(editor) ? filterWordContent(editor, content) : content;
  };
  var $_dfatuiivjjgwecu8 = {
    preProcess: preProcess,
    isWordContent: isWordContent
  };

  var processResult = function (content, cancelled) {
    return {
      content: content,
      cancelled: cancelled
    };
  };
  var postProcessFilter = function (editor, html, internal, isWordHtml) {
    var tempBody = editor.dom.create('div', { style: 'display:none' }, html);
    var postProcessArgs = $_8tki3zijjjgwectj.firePastePostProcess(editor, tempBody, internal, isWordHtml);
    return processResult(postProcessArgs.node.innerHTML, postProcessArgs.isDefaultPrevented());
  };
  var filterContent = function (editor, content, internal, isWordHtml) {
    var preProcessArgs = $_8tki3zijjjgwectj.firePastePreProcess(editor, content, internal, isWordHtml);
    if (editor.hasEventListeners('PastePostProcess') && !preProcessArgs.isDefaultPrevented()) {
      return postProcessFilter(editor, preProcessArgs.content, internal, isWordHtml);
    } else {
      return processResult(preProcessArgs.content, preProcessArgs.isDefaultPrevented());
    }
  };
  var process = function (editor, html, internal) {
    var isWordHtml = $_dfatuiivjjgwecu8.isWordContent(html);
    var content = isWordHtml ? $_dfatuiivjjgwecu8.preProcess(editor, html) : html;
    return filterContent(editor, content, internal, isWordHtml);
  };
  var $_3scw66iujjgwecu4 = { process: process };

  var pasteHtml = function (editor, html) {
    editor.insertContent(html, {
      merge: $_xr8b0ikjjgwectl.shouldMergeFormats(editor),
      paste: true
    });
    return true;
  };
  var isAbsoluteUrl = function (url) {
    return /^https?:\/\/[\w\?\-\/+=.&%@~#]+$/i.test(url);
  };
  var isImageUrl = function (url) {
    return isAbsoluteUrl(url) && /.(gif|jpe?g|png)$/.test(url);
  };
  var createImage = function (editor, url, pasteHtmlFn) {
    editor.undoManager.extra(function () {
      pasteHtmlFn(editor, url);
    }, function () {
      editor.insertContent('<img src="' + url + '">');
    });
    return true;
  };
  var createLink = function (editor, url, pasteHtmlFn) {
    editor.undoManager.extra(function () {
      pasteHtmlFn(editor, url);
    }, function () {
      editor.execCommand('mceInsertLink', false, url);
    });
    return true;
  };
  var linkSelection = function (editor, html, pasteHtmlFn) {
    return editor.selection.isCollapsed() === false && isAbsoluteUrl(html) ? createLink(editor, html, pasteHtmlFn) : false;
  };
  var insertImage = function (editor, html, pasteHtmlFn) {
    return isImageUrl(html) ? createImage(editor, html, pasteHtmlFn) : false;
  };
  var smartInsertContent = function (editor, html) {
    global$3.each([
      linkSelection,
      insertImage,
      pasteHtml
    ], function (action) {
      return action(editor, html, pasteHtml) !== true;
    });
  };
  var insertContent = function (editor, html) {
    if ($_xr8b0ikjjgwectl.isSmartPasteEnabled(editor) === false) {
      pasteHtml(editor, html);
    } else {
      smartInsertContent(editor, html);
    }
  };
  var $_d8pzpej1jjgwecum = {
    isImageUrl: isImageUrl,
    isAbsoluteUrl: isAbsoluteUrl,
    insertContent: insertContent
  };

  var pasteHtml$1 = function (editor, html, internalFlag) {
    var internal = internalFlag ? internalFlag : $_4x13hjirjjgwecu1.isMarked(html);
    var args = $_3scw66iujjgwecu4.process(editor, $_4x13hjirjjgwecu1.unmark(html), internal);
    if (args.cancelled === false) {
      $_d8pzpej1jjgwecum.insertContent(editor, args.content);
    }
  };
  var pasteText = function (editor, text) {
    text = editor.dom.encode(text).replace(/\r\n/g, '\n');
    text = $_4h3hnrisjjgwecu2.convert(text, editor.settings.forced_root_block, editor.settings.forced_root_block_attrs);
    pasteHtml$1(editor, text, false);
  };
  var getDataTransferItems = function (dataTransfer) {
    var items = {};
    var mceInternalUrlPrefix = 'data:text/mce-internal,';
    if (dataTransfer) {
      if (dataTransfer.getData) {
        var legacyText = dataTransfer.getData('Text');
        if (legacyText && legacyText.length > 0) {
          if (legacyText.indexOf(mceInternalUrlPrefix) === -1) {
            items['text/plain'] = legacyText;
          }
        }
      }
      if (dataTransfer.types) {
        for (var i = 0; i < dataTransfer.types.length; i++) {
          var contentType = dataTransfer.types[i];
          try {
            items[contentType] = dataTransfer.getData(contentType);
          } catch (ex) {
            items[contentType] = '';
          }
        }
      }
    }
    return items;
  };
  var getClipboardContent = function (editor, clipboardEvent) {
    var content = getDataTransferItems(clipboardEvent.clipboardData || editor.getDoc().dataTransfer);
    return $_4bi2o9j0jjgwecui.isMsEdge() ? global$3.extend(content, { 'text/html': '' }) : content;
  };
  var hasContentType = function (clipboardContent, mimeType) {
    return mimeType in clipboardContent && clipboardContent[mimeType].length > 0;
  };
  var hasHtmlOrText = function (content) {
    return hasContentType(content, 'text/html') || hasContentType(content, 'text/plain');
  };
  var getBase64FromUri = function (uri) {
    var idx;
    idx = uri.indexOf(',');
    if (idx !== -1) {
      return uri.substr(idx + 1);
    }
    return null;
  };
  var isValidDataUriImage = function (settings, imgElm) {
    return settings.images_dataimg_filter ? settings.images_dataimg_filter(imgElm) : true;
  };
  var extractFilename = function (editor, str) {
    var m = str.match(/([\s\S]+?)\.(?:jpeg|jpg|png|gif)$/i);
    return m ? editor.dom.encode(m[1]) : null;
  };
  var uniqueId = $_4bi2o9j0jjgwecui.createIdGenerator('mceclip');
  var pasteImage = function (editor, rng, reader, blob) {
    if (rng) {
      editor.selection.setRng(rng);
      rng = null;
    }
    var dataUri = reader.result;
    var base64 = getBase64FromUri(dataUri);
    var id = uniqueId();
    var name$$1 = editor.settings.images_reuse_filename && blob.name ? extractFilename(editor, blob.name) : id;
    var img = new Image();
    img.src = dataUri;
    if (isValidDataUriImage(editor.settings, img)) {
      var blobCache = editor.editorUpload.blobCache;
      var blobInfo = void 0, existingBlobInfo = void 0;
      existingBlobInfo = blobCache.findFirst(function (cachedBlobInfo) {
        return cachedBlobInfo.base64() === base64;
      });
      if (!existingBlobInfo) {
        blobInfo = blobCache.create(id, blob, base64, name$$1);
        blobCache.add(blobInfo);
      } else {
        blobInfo = existingBlobInfo;
      }
      pasteHtml$1(editor, '<img src="' + blobInfo.blobUri() + '">', false);
    } else {
      pasteHtml$1(editor, '<img src="' + dataUri + '">', false);
    }
  };
  var isClipboardEvent = function (event$$1) {
    return event$$1.type === 'paste';
  };
  var pasteImageData = function (editor, e, rng) {
    var dataTransfer = isClipboardEvent(e) ? e.clipboardData : e.dataTransfer;
    function processItems(items) {
      var i, item, reader, hadImage = false;
      if (items) {
        for (i = 0; i < items.length; i++) {
          item = items[i];
          if (/^image\/(jpeg|png|gif|bmp)$/.test(item.type)) {
            var blob = item.getAsFile ? item.getAsFile() : item;
            reader = new window.FileReader();
            reader.onload = pasteImage.bind(null, editor, rng, reader, blob);
            reader.readAsDataURL(blob);
            e.preventDefault();
            hadImage = true;
          }
        }
      }
      return hadImage;
    }
    if (editor.settings.paste_data_images && dataTransfer) {
      return processItems(dataTransfer.items) || processItems(dataTransfer.files);
    }
  };
  var isBrokenAndroidClipboardEvent = function (e) {
    var clipboardData = e.clipboardData;
    return navigator.userAgent.indexOf('Android') !== -1 && clipboardData && clipboardData.items && clipboardData.items.length === 0;
  };
  var isKeyboardPasteEvent = function (e) {
    return global$4.metaKeyPressed(e) && e.keyCode === 86 || e.shiftKey && e.keyCode === 45;
  };
  var registerEventHandlers = function (editor, pasteBin, pasteFormat) {
    var keyboardPasteTimeStamp = 0;
    var keyboardPastePlainTextState;
    editor.on('keydown', function (e) {
      function removePasteBinOnKeyUp(e) {
        if (isKeyboardPasteEvent(e) && !e.isDefaultPrevented()) {
          pasteBin.remove();
        }
      }
      if (isKeyboardPasteEvent(e) && !e.isDefaultPrevented()) {
        keyboardPastePlainTextState = e.shiftKey && e.keyCode === 86;
        if (keyboardPastePlainTextState && global$1.webkit && navigator.userAgent.indexOf('Version/') !== -1) {
          return;
        }
        e.stopImmediatePropagation();
        keyboardPasteTimeStamp = new Date().getTime();
        if (global$1.ie && keyboardPastePlainTextState) {
          e.preventDefault();
          $_8tki3zijjjgwectj.firePaste(editor, true);
          return;
        }
        pasteBin.remove();
        pasteBin.create();
        editor.once('keyup', removePasteBinOnKeyUp);
        editor.once('paste', function () {
          editor.off('keyup', removePasteBinOnKeyUp);
        });
      }
    });
    function insertClipboardContent(clipboardContent, isKeyBoardPaste, plainTextMode, internal) {
      var content, isPlainTextHtml;
      if (hasContentType(clipboardContent, 'text/html')) {
        content = clipboardContent['text/html'];
      } else {
        content = pasteBin.getHtml();
        internal = internal ? internal : $_4x13hjirjjgwecu1.isMarked(content);
        if (pasteBin.isDefaultContent(content)) {
          plainTextMode = true;
        }
      }
      content = $_4bi2o9j0jjgwecui.trimHtml(content);
      pasteBin.remove();
      isPlainTextHtml = internal === false && $_4h3hnrisjjgwecu2.isPlainText(content);
      if (!content.length || isPlainTextHtml) {
        plainTextMode = true;
      }
      if (plainTextMode) {
        if (hasContentType(clipboardContent, 'text/plain') && isPlainTextHtml) {
          content = clipboardContent['text/plain'];
        } else {
          content = $_4bi2o9j0jjgwecui.innerText(content);
        }
      }
      if (pasteBin.isDefaultContent(content)) {
        if (!isKeyBoardPaste) {
          editor.windowManager.alert('Please use Ctrl+V/Cmd+V keyboard shortcuts to paste contents.');
        }
        return;
      }
      if (plainTextMode) {
        pasteText(editor, content);
      } else {
        pasteHtml$1(editor, content, internal);
      }
    }
    var getLastRng = function () {
      return pasteBin.getLastRng() || editor.selection.getRng();
    };
    editor.on('paste', function (e) {
      var clipboardTimer = new Date().getTime();
      var clipboardContent = getClipboardContent(editor, e);
      var clipboardDelay = new Date().getTime() - clipboardTimer;
      var isKeyBoardPaste = new Date().getTime() - keyboardPasteTimeStamp - clipboardDelay < 1000;
      var plainTextMode = pasteFormat.get() === 'text' || keyboardPastePlainTextState;
      var internal = hasContentType(clipboardContent, $_4x13hjirjjgwecu1.internalHtmlMime());
      keyboardPastePlainTextState = false;
      if (e.isDefaultPrevented() || isBrokenAndroidClipboardEvent(e)) {
        pasteBin.remove();
        return;
      }
      if (!hasHtmlOrText(clipboardContent) && pasteImageData(editor, e, getLastRng())) {
        pasteBin.remove();
        return;
      }
      if (!isKeyBoardPaste) {
        e.preventDefault();
      }
      if (global$1.ie && (!isKeyBoardPaste || e.ieFake) && !hasContentType(clipboardContent, 'text/html')) {
        pasteBin.create();
        editor.dom.bind(pasteBin.getEl(), 'paste', function (e) {
          e.stopPropagation();
        });
        editor.getDoc().execCommand('Paste', false, null);
        clipboardContent['text/html'] = pasteBin.getHtml();
      }
      if (hasContentType(clipboardContent, 'text/html')) {
        e.preventDefault();
        if (!internal) {
          internal = $_4x13hjirjjgwecu1.isMarked(clipboardContent['text/html']);
        }
        insertClipboardContent(clipboardContent, isKeyBoardPaste, plainTextMode, internal);
      } else {
        global$2.setEditorTimeout(editor, function () {
          insertClipboardContent(clipboardContent, isKeyBoardPaste, plainTextMode, internal);
        }, 0);
      }
    });
  };
  var registerEventsAndFilters = function (editor, pasteBin, pasteFormat) {
    registerEventHandlers(editor, pasteBin, pasteFormat);
    var src;
    editor.parser.addNodeFilter('img', function (nodes, name$$1, args) {
      var isPasteInsert = function (args) {
        return args.data && args.data.paste === true;
      };
      var remove = function (node) {
        if (!node.attr('data-mce-object') && src !== global$1.transparentSrc) {
          node.remove();
        }
      };
      var isWebKitFakeUrl = function (src) {
        return src.indexOf('webkit-fake-url') === 0;
      };
      var isDataUri = function (src) {
        return src.indexOf('data:') === 0;
      };
      if (!editor.settings.paste_data_images && isPasteInsert(args)) {
        var i = nodes.length;
        while (i--) {
          src = nodes[i].attributes.map.src;
          if (!src) {
            continue;
          }
          if (isWebKitFakeUrl(src)) {
            remove(nodes[i]);
          } else if (!editor.settings.allow_html_data_urls && isDataUri(src)) {
            remove(nodes[i]);
          }
        }
      }
    });
  };

  var getPasteBinParent = function (editor) {
    return global$1.ie && editor.inline ? document.body : editor.getBody();
  };
  var isExternalPasteBin = function (editor) {
    return getPasteBinParent(editor) !== editor.getBody();
  };
  var delegatePasteEvents = function (editor, pasteBinElm) {
    if (isExternalPasteBin(editor)) {
      editor.dom.bind(pasteBinElm, 'paste keyup', function (e) {
        setTimeout(function () {
          editor.fire('paste');
        }, 0);
      });
    }
  };
  var create = function (editor, lastRngCell, pasteBinDefaultContent) {
    var dom = editor.dom, body = editor.getBody();
    var pasteBinElm;
    lastRngCell.set(editor.selection.getRng());
    pasteBinElm = editor.dom.add(getPasteBinParent(editor), 'div', {
      'id': 'mcepastebin',
      'class': 'mce-pastebin',
      'contentEditable': true,
      'data-mce-bogus': 'all',
      'style': 'position: fixed; top: 50%; width: 10px; height: 10px; overflow: hidden; opacity: 0'
    }, pasteBinDefaultContent);
    if (global$1.ie || global$1.gecko) {
      dom.setStyle(pasteBinElm, 'left', dom.getStyle(body, 'direction', true) === 'rtl' ? 65535 : -65535);
    }
    dom.bind(pasteBinElm, 'beforedeactivate focusin focusout', function (e) {
      e.stopPropagation();
    });
    delegatePasteEvents(editor, pasteBinElm);
    pasteBinElm.focus();
    editor.selection.select(pasteBinElm, true);
  };
  var remove = function (editor, lastRngCell) {
    if (getEl(editor)) {
      var pasteBinClone = void 0;
      var lastRng = lastRngCell.get();
      while (pasteBinClone = editor.dom.get('mcepastebin')) {
        editor.dom.remove(pasteBinClone);
        editor.dom.unbind(pasteBinClone);
      }
      if (lastRng) {
        editor.selection.setRng(lastRng);
      }
    }
    lastRngCell.set(null);
  };
  var getEl = function (editor) {
    return editor.dom.get('mcepastebin');
  };
  var getHtml = function (editor) {
    var pasteBinElm, pasteBinClones, i, dirtyWrappers, cleanWrapper;
    var copyAndRemove = function (toElm, fromElm) {
      toElm.appendChild(fromElm);
      editor.dom.remove(fromElm, true);
    };
    pasteBinClones = global$3.grep(getPasteBinParent(editor).childNodes, function (elm) {
      return elm.id === 'mcepastebin';
    });
    pasteBinElm = pasteBinClones.shift();
    global$3.each(pasteBinClones, function (pasteBinClone) {
      copyAndRemove(pasteBinElm, pasteBinClone);
    });
    dirtyWrappers = editor.dom.select('div[id=mcepastebin]', pasteBinElm);
    for (i = dirtyWrappers.length - 1; i >= 0; i--) {
      cleanWrapper = editor.dom.create('div');
      pasteBinElm.insertBefore(cleanWrapper, dirtyWrappers[i]);
      copyAndRemove(cleanWrapper, dirtyWrappers[i]);
    }
    return pasteBinElm ? pasteBinElm.innerHTML : '';
  };
  var getLastRng = function (lastRng) {
    return lastRng.get();
  };
  var isDefaultContent = function (pasteBinDefaultContent, content) {
    return content === pasteBinDefaultContent;
  };
  var isPasteBin = function (elm) {
    return elm && elm.id === 'mcepastebin';
  };
  var isDefault = function (editor, pasteBinDefaultContent) {
    var pasteBinElm = getEl(editor);
    return isPasteBin(pasteBinElm) && isDefaultContent(pasteBinDefaultContent, pasteBinElm.innerHTML);
  };
  var PasteBin = function (editor) {
    var lastRng = Cell(null);
    var pasteBinDefaultContent = '%MCEPASTEBIN%';
    return {
      create: function () {
        return create(editor, lastRng, pasteBinDefaultContent);
      },
      remove: function () {
        return remove(editor, lastRng);
      },
      getEl: function () {
        return getEl(editor);
      },
      getHtml: function () {
        return getHtml(editor);
      },
      getLastRng: function () {
        return getLastRng(lastRng);
      },
      isDefault: function () {
        return isDefault(editor, pasteBinDefaultContent);
      },
      isDefaultContent: function (content) {
        return isDefaultContent(pasteBinDefaultContent, content);
      }
    };
  };

  var Clipboard = function (editor, pasteFormat) {
    var pasteBin = PasteBin(editor);
    editor.on('preInit', function () {
      return registerEventsAndFilters(editor, pasteBin, pasteFormat);
    });
    return {
      pasteFormat: pasteFormat,
      pasteHtml: function (html, internalFlag) {
        return pasteHtml$1(editor, html, internalFlag);
      },
      pasteText: function (text) {
        return pasteText(editor, text);
      },
      pasteImageData: function (e, rng) {
        return pasteImageData(editor, e, rng);
      },
      getDataTransferItems: getDataTransferItems,
      hasHtmlOrText: hasHtmlOrText,
      hasContentType: hasContentType
    };
  };

  var noop = function () {
  };
  var hasWorkingClipboardApi = function (clipboardData) {
    return global$1.iOS === false && clipboardData !== undefined && typeof clipboardData.setData === 'function' && $_4bi2o9j0jjgwecui.isMsEdge() !== true;
  };
  var setHtml5Clipboard = function (clipboardData, html, text) {
    if (hasWorkingClipboardApi(clipboardData)) {
      try {
        clipboardData.clearData();
        clipboardData.setData('text/html', html);
        clipboardData.setData('text/plain', text);
        clipboardData.setData($_4x13hjirjjgwecu1.internalHtmlMime(), html);
        return true;
      } catch (e) {
        return false;
      }
    } else {
      return false;
    }
  };
  var setClipboardData = function (evt, data, fallback, done) {
    if (setHtml5Clipboard(evt.clipboardData, data.html, data.text)) {
      evt.preventDefault();
      done();
    } else {
      fallback(data.html, done);
    }
  };
  var fallback = function (editor) {
    return function (html, done) {
      var markedHtml = $_4x13hjirjjgwecu1.mark(html);
      var outer = editor.dom.create('div', {
        'contenteditable': 'false',
        'data-mce-bogus': 'all'
      });
      var inner = editor.dom.create('div', { contenteditable: 'true' }, markedHtml);
      editor.dom.setStyles(outer, {
        position: 'fixed',
        top: '0',
        left: '-3000px',
        width: '1000px',
        overflow: 'hidden'
      });
      outer.appendChild(inner);
      editor.dom.add(editor.getBody(), outer);
      var range = editor.selection.getRng();
      inner.focus();
      var offscreenRange = editor.dom.createRng();
      offscreenRange.selectNodeContents(inner);
      editor.selection.setRng(offscreenRange);
      setTimeout(function () {
        editor.selection.setRng(range);
        outer.parentNode.removeChild(outer);
        done();
      }, 0);
    };
  };
  var getData = function (editor) {
    return {
      html: editor.selection.getContent({ contextual: true }),
      text: editor.selection.getContent({ format: 'text' })
    };
  };
  var cut = function (editor) {
    return function (evt) {
      if (editor.selection.isCollapsed() === false) {
        setClipboardData(evt, getData(editor), fallback(editor), function () {
          setTimeout(function () {
            editor.execCommand('Delete');
          }, 0);
        });
      }
    };
  };
  var copy = function (editor) {
    return function (evt) {
      if (editor.selection.isCollapsed() === false) {
        setClipboardData(evt, getData(editor), fallback(editor), noop);
      }
    };
  };
  var register$1 = function (editor) {
    editor.on('cut', cut(editor));
    editor.on('copy', copy(editor));
  };
  var $_32blojj3jjgwecv4 = { register: register$1 };

  var global$10 = tinymce.util.Tools.resolve('tinymce.dom.RangeUtils');

  var getCaretRangeFromEvent = function (editor, e) {
    return global$10.getCaretRangeFromPoint(e.clientX, e.clientY, editor.getDoc());
  };
  var isPlainTextFileUrl = function (content) {
    var plainTextContent = content['text/plain'];
    return plainTextContent ? plainTextContent.indexOf('file://') === 0 : false;
  };
  var setFocusedRange = function (editor, rng) {
    editor.focus();
    editor.selection.setRng(rng);
  };
  var setup = function (editor, clipboard, draggingInternallyState) {
    if ($_xr8b0ikjjgwectl.shouldBlockDrop(editor)) {
      editor.on('dragend dragover draggesture dragdrop drop drag', function (e) {
        e.preventDefault();
        e.stopPropagation();
      });
    }
    if (!$_xr8b0ikjjgwectl.shouldPasteDataImages(editor)) {
      editor.on('drop', function (e) {
        var dataTransfer = e.dataTransfer;
        if (dataTransfer && dataTransfer.files && dataTransfer.files.length > 0) {
          e.preventDefault();
        }
      });
    }
    editor.on('drop', function (e) {
      var dropContent, rng;
      rng = getCaretRangeFromEvent(editor, e);
      if (e.isDefaultPrevented() || draggingInternallyState.get()) {
        return;
      }
      dropContent = clipboard.getDataTransferItems(e.dataTransfer);
      var internal = clipboard.hasContentType(dropContent, $_4x13hjirjjgwecu1.internalHtmlMime());
      if ((!clipboard.hasHtmlOrText(dropContent) || isPlainTextFileUrl(dropContent)) && clipboard.pasteImageData(e, rng)) {
        return;
      }
      if (rng && $_xr8b0ikjjgwectl.shouldFilterDrop(editor)) {
        var content_1 = dropContent['mce-internal'] || dropContent['text/html'] || dropContent['text/plain'];
        if (content_1) {
          e.preventDefault();
          global$2.setEditorTimeout(editor, function () {
            editor.undoManager.transact(function () {
              if (dropContent['mce-internal']) {
                editor.execCommand('Delete');
              }
              setFocusedRange(editor, rng);
              content_1 = $_4bi2o9j0jjgwecui.trimHtml(content_1);
              if (!dropContent['text/html']) {
                clipboard.pasteText(content_1);
              } else {
                clipboard.pasteHtml(content_1, internal);
              }
            });
          });
        }
      }
    });
    editor.on('dragstart', function (e) {
      draggingInternallyState.set(true);
    });
    editor.on('dragover dragend', function (e) {
      if ($_xr8b0ikjjgwectl.shouldPasteDataImages(editor) && draggingInternallyState.get() === false) {
        e.preventDefault();
        setFocusedRange(editor, getCaretRangeFromEvent(editor, e));
      }
      if (e.type === 'dragend') {
        draggingInternallyState.set(false);
      }
    });
  };
  var $_b4etj0j4jjgwecv7 = { setup: setup };

  var setup$1 = function (editor) {
    var plugin = editor.plugins.paste;
    var preProcess = $_xr8b0ikjjgwectl.getPreProcess(editor);
    if (preProcess) {
      editor.on('PastePreProcess', function (e) {
        preProcess.call(plugin, plugin, e);
      });
    }
    var postProcess = $_xr8b0ikjjgwectl.getPostProcess(editor);
    if (postProcess) {
      editor.on('PastePostProcess', function (e) {
        postProcess.call(plugin, plugin, e);
      });
    }
  };
  var $_c5bihmj6jjgwecva = { setup: setup$1 };

  function addPreProcessFilter(editor, filterFunc) {
    editor.on('PastePreProcess', function (e) {
      e.content = filterFunc(editor, e.content, e.internal, e.wordContent);
    });
  }
  function addPostProcessFilter(editor, filterFunc) {
    editor.on('PastePostProcess', function (e) {
      filterFunc(editor, e.node);
    });
  }
  function removeExplorerBrElementsAfterBlocks(editor, html) {
    if (!$_dfatuiivjjgwecu8.isWordContent(html)) {
      return html;
    }
    var blockElements = [];
    global$3.each(editor.schema.getBlockElements(), function (block, blockName) {
      blockElements.push(blockName);
    });
    var explorerBlocksRegExp = new RegExp('(?:<br>&nbsp;[\\s\\r\\n]+|<br>)*(<\\/?(' + blockElements.join('|') + ')[^>]*>)(?:<br>&nbsp;[\\s\\r\\n]+|<br>)*', 'g');
    html = $_4bi2o9j0jjgwecui.filter(html, [[
        explorerBlocksRegExp,
        '$1'
      ]]);
    html = $_4bi2o9j0jjgwecui.filter(html, [
      [
        /<br><br>/g,
        '<BR><BR>'
      ],
      [
        /<br>/g,
        ' '
      ],
      [
        /<BR><BR>/g,
        '<br>'
      ]
    ]);
    return html;
  }
  function removeWebKitStyles(editor, content, internal, isWordHtml) {
    if (isWordHtml || internal) {
      return content;
    }
    var webKitStylesSetting = $_xr8b0ikjjgwectl.getWebkitStyles(editor);
    var webKitStyles;
    if ($_xr8b0ikjjgwectl.shouldRemoveWebKitStyles(editor) === false || webKitStylesSetting === 'all') {
      return content;
    }
    if (webKitStylesSetting) {
      webKitStyles = webKitStylesSetting.split(/[, ]/);
    }
    if (webKitStyles) {
      var dom_1 = editor.dom, node_1 = editor.selection.getNode();
      content = content.replace(/(<[^>]+) style="([^"]*)"([^>]*>)/gi, function (all, before, value, after) {
        var inputStyles = dom_1.parseStyle(dom_1.decode(value));
        var outputStyles = {};
        if (webKitStyles === 'none') {
          return before + after;
        }
        for (var i = 0; i < webKitStyles.length; i++) {
          var inputValue = inputStyles[webKitStyles[i]], currentValue = dom_1.getStyle(node_1, webKitStyles[i], true);
          if (/color/.test(webKitStyles[i])) {
            inputValue = dom_1.toHex(inputValue);
            currentValue = dom_1.toHex(currentValue);
          }
          if (currentValue !== inputValue) {
            outputStyles[webKitStyles[i]] = inputValue;
          }
        }
        outputStyles = dom_1.serializeStyle(outputStyles, 'span');
        if (outputStyles) {
          return before + ' style="' + outputStyles + '"' + after;
        }
        return before + after;
      });
    } else {
      content = content.replace(/(<[^>]+) style="([^"]*)"([^>]*>)/gi, '$1$3');
    }
    content = content.replace(/(<[^>]+) data-mce-style="([^"]+)"([^>]*>)/gi, function (all, before, value, after) {
      return before + ' style="' + value + '"' + after;
    });
    return content;
  }
  function removeUnderlineAndFontInAnchor(editor, root) {
    editor.$('a', root).find('font,u').each(function (i, node) {
      editor.dom.remove(node, true);
    });
  }
  var setup$2 = function (editor) {
    if (global$1.webkit) {
      addPreProcessFilter(editor, removeWebKitStyles);
    }
    if (global$1.ie) {
      addPreProcessFilter(editor, removeExplorerBrElementsAfterBlocks);
      addPostProcessFilter(editor, removeUnderlineAndFontInAnchor);
    }
  };
  var $_36tmgyj7jjgwecvc = { setup: setup$2 };

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

  var stateChange = function (editor, clipboard, e) {
    var ctrl = e.control;
    ctrl.active(clipboard.pasteFormat.get() === 'text');
    editor.on('PastePlainTextToggle', function (e) {
      ctrl.active(e.state);
    });
  };
  var register$2 = function (editor, clipboard) {
    var postRender = curry(stateChange, editor, clipboard);
    editor.addButton('pastetext', {
      active: false,
      icon: 'pastetext',
      tooltip: 'Paste as text',
      cmd: 'mceTogglePlainTextPaste',
      onPostRender: postRender
    });
    editor.addMenuItem('pastetext', {
      text: 'Paste as text',
      selectable: true,
      active: clipboard.pasteFormat,
      cmd: 'mceTogglePlainTextPaste',
      onPostRender: postRender
    });
  };
  var $_g9yhwdj8jjgwecvf = { register: register$2 };

  global.add('paste', function (editor) {
    if ($_15bf6siejjgwect1.hasProPlugin(editor) === false) {
      var userIsInformedState = Cell(false);
      var draggingInternallyState = Cell(false);
      var pasteFormat = Cell($_xr8b0ikjjgwectl.isPasteAsTextEnabled(editor) ? 'text' : 'html');
      var clipboard = Clipboard(editor, pasteFormat);
      var quirks = $_36tmgyj7jjgwecvc.setup(editor);
      $_g9yhwdj8jjgwecvf.register(editor, clipboard);
      $_fldd1mihjjgwecth.register(editor, clipboard, userIsInformedState);
      $_c5bihmj6jjgwecva.setup(editor);
      $_32blojj3jjgwecv4.register(editor);
      $_b4etj0j4jjgwecv7.setup(editor, clipboard, draggingInternallyState);
      return $_6gtliyigjjgwecte.get(clipboard, quirks);
    }
  });
  function Plugin () {
  }

  return Plugin;

}());
})();
