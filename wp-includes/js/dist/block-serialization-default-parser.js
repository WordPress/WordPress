(function() {
"use strict";
var wp;
(wp ||= {}).blockSerializationDefaultParser = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // packages/block-serialization-default-parser/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    parse: () => parse
  });
  var document;
  var offset;
  var output;
  var stack;
  var delimiterPrefixPattern = /[ \t\f\r\n]+(\/)?wp:([a-z][a-z0-9_-]*\/)?([a-z][a-z0-9_-]*)[ \t\f\r\n]+/y;
  var attributeAreaClosingPattern = /}[ \t\f\r\n]+$/g;
  var commentStartPattern = /<!--/g;
  var onlyDashesCommentEndPattern = /-*>/y;
  var commentEndPattern = /--!?>/g;
  function Block(blockName, attrs, innerBlocks, innerHTML, innerContent) {
    return {
      blockName,
      attrs,
      innerBlocks,
      innerHTML,
      innerContent
    };
  }
  function Freeform(innerHTML) {
    return Block(null, {}, [], innerHTML, [innerHTML]);
  }
  function Frame(block, tokenStart, tokenLength, prevOffset, leadingHtmlStart) {
    return {
      block,
      tokenStart,
      tokenLength,
      prevOffset: prevOffset || tokenStart + tokenLength,
      leadingHtmlStart
    };
  }
  var parse = (doc) => {
    document = doc;
    offset = 0;
    output = [];
    stack = [];
    commentStartPattern.lastIndex = 0;
    do {
    } while (proceed());
    return output;
  };
  function proceed() {
    const stackDepth = stack.length;
    const next = nextToken();
    const [tokenType, blockName, attrs, startOffset, tokenLength] = next;
    const leadingHtmlStart = startOffset > offset ? offset : null;
    switch (tokenType) {
      case "no-more-tokens":
        if (0 === stackDepth) {
          addFreeform();
          return false;
        }
        implicitlyCloseAllOpenBlocks();
        return false;
      case "void-block":
        if (0 === stackDepth) {
          if (null !== leadingHtmlStart) {
            output.push(
              Freeform(
                document.substr(
                  leadingHtmlStart,
                  startOffset - leadingHtmlStart
                )
              )
            );
          }
          output.push(Block(blockName, attrs, [], "", []));
          offset = startOffset + tokenLength;
          return true;
        }
        addInnerBlock(
          Block(blockName, attrs, [], "", []),
          startOffset,
          tokenLength
        );
        offset = startOffset + tokenLength;
        return true;
      case "block-opener":
        stack.push(
          Frame(
            Block(blockName, attrs, [], "", []),
            startOffset,
            tokenLength,
            startOffset + tokenLength,
            leadingHtmlStart
          )
        );
        offset = startOffset + tokenLength;
        return true;
      case "block-closer":
        if (0 === stackDepth) {
          addFreeform();
          return false;
        }
        if (1 === stackDepth) {
          addBlockFromStack(startOffset);
          offset = startOffset + tokenLength;
          return true;
        }
        const stackTop = stack.pop();
        const html = document.substr(
          stackTop.prevOffset,
          startOffset - stackTop.prevOffset
        );
        if (html) {
          stackTop.block.innerHTML += html;
          stackTop.block.innerContent.push(html);
        }
        stackTop.prevOffset = startOffset + tokenLength;
        addInnerBlock(
          stackTop.block,
          stackTop.tokenStart,
          stackTop.tokenLength,
          startOffset + tokenLength
        );
        offset = startOffset + tokenLength;
        return true;
      default:
        addFreeform();
        return false;
    }
  }
  function parseJSON(input) {
    try {
      return JSON.parse(input);
    } catch {
      return null;
    }
  }
  function nextToken() {
    while (commentStartPattern.lastIndex < document.length) {
      const nextComment = commentStartPattern.exec(document);
      if (null === nextComment) {
        commentStartPattern.lastIndex = document.length;
        break;
      }
      onlyDashesCommentEndPattern.lastIndex = commentStartPattern.lastIndex;
      const abruptlyClosedEnding = onlyDashesCommentEndPattern.exec(document);
      if (null !== abruptlyClosedEnding) {
        commentStartPattern.lastIndex = onlyDashesCommentEndPattern.lastIndex;
        continue;
      }
      commentEndPattern.lastIndex = commentStartPattern.lastIndex;
      const commentEnd = commentEndPattern.exec(document);
      if (null === commentEnd) {
        commentStartPattern.lastIndex = document.length;
        break;
      }
      if ("!" === document[commentEndPattern.lastIndex - 2]) {
        commentStartPattern.lastIndex = commentEndPattern.lastIndex;
        continue;
      }
      if (commentEnd.index < commentStartPattern.lastIndex + 6) {
        commentStartPattern.lastIndex = commentEndPattern.lastIndex;
        continue;
      }
      delimiterPrefixPattern.lastIndex = commentStartPattern.lastIndex;
      const delimiterPrefix = delimiterPrefixPattern.exec(document);
      if (null === delimiterPrefix) {
        commentStartPattern.lastIndex = commentEndPattern.lastIndex;
        continue;
      }
      const isVoid = "/" === document[commentEnd.index - 1];
      const voidBackup = isVoid ? 1 : 0;
      const attributeAreaLength = commentEnd.index - voidBackup - delimiterPrefixPattern.lastIndex;
      const hasAttrs = attributeAreaLength > 0;
      if (hasAttrs && "{" !== document[delimiterPrefixPattern.lastIndex]) {
        commentStartPattern.lastIndex = commentEndPattern.lastIndex;
        continue;
      }
      const attributeArea = document.slice(
        delimiterPrefixPattern.lastIndex,
        commentEnd.index - voidBackup
      );
      attributeAreaClosingPattern.lastIndex = 1;
      const attributeClosing = hasAttrs ? attributeAreaClosingPattern.exec(attributeArea) : null;
      if (hasAttrs && null === attributeClosing) {
        commentStartPattern.lastIndex = commentEndPattern.lastIndex;
        continue;
      }
      const jsonAttributes = attributeClosing ? attributeArea.slice(0, attributeClosing.index + 1) : null;
      const startedAt = nextComment.index;
      commentStartPattern.lastIndex = commentEndPattern.lastIndex;
      const length = commentEndPattern.lastIndex - nextComment.index;
      const isCloser = "/" === delimiterPrefix[1];
      const namespace = delimiterPrefix[2] || "core/";
      const name = namespace + delimiterPrefix[3];
      const attrs = jsonAttributes ? parseJSON(jsonAttributes) : {};
      if (isCloser && (isVoid || hasAttrs)) {
      }
      if (isVoid) {
        return ["void-block", name, attrs, startedAt, length];
      }
      if (isCloser) {
        return ["block-closer", name, null, startedAt, length];
      }
      return ["block-opener", name, attrs, startedAt, length];
    }
    return ["no-more-tokens", "", null, 0, 0];
  }
  function addFreeform(rawLength) {
    const length = rawLength ? rawLength : document.length - offset;
    if (0 === length) {
      return;
    }
    output.push(Freeform(document.substr(offset, length)));
  }
  function addInnerBlock(block, tokenStart, tokenLength, lastOffset) {
    const parent = stack[stack.length - 1];
    parent.block.innerBlocks.push(block);
    const html = document.substr(
      parent.prevOffset,
      tokenStart - parent.prevOffset
    );
    if (html) {
      parent.block.innerHTML += html;
      parent.block.innerContent.push(html);
    }
    parent.block.innerContent.push(null);
    parent.prevOffset = lastOffset ? lastOffset : tokenStart + tokenLength;
  }
  function addBlockFromStack(endOffset) {
    const { block, leadingHtmlStart, prevOffset, tokenStart } = stack.pop();
    const html = endOffset ? document.substr(prevOffset, endOffset - prevOffset) : document.substr(prevOffset);
    if (html) {
      block.innerHTML += html;
      block.innerContent.push(html);
    }
    if (null !== leadingHtmlStart) {
      output.push(
        Freeform(
          document.substr(
            leadingHtmlStart,
            tokenStart - leadingHtmlStart
          )
        )
      );
    }
    output.push(block);
  }
  function implicitlyCloseAllOpenBlocks() {
    let lastClosedBlock = null;
    let lastOpenerAt = document.length;
    let lastLeadingHtml = null;
    while (stack.length > 0) {
      const stackTop = stack.pop();
      let html;
      const htmlLength = lastOpenerAt - stackTop.prevOffset;
      if (htmlLength > 0) {
        html = document.substr(stackTop.prevOffset, htmlLength);
        stackTop.block.innerHTML += html;
        stackTop.block.innerContent.push(html);
      }
      if (lastClosedBlock !== null) {
        stackTop.block.innerContent.push(null);
        stackTop.block.innerBlocks.push(lastClosedBlock);
      }
      lastClosedBlock = stackTop.block;
      lastOpenerAt = stackTop.tokenStart;
      lastLeadingHtml = stackTop.leadingHtmlStart;
    }
    if (null !== lastLeadingHtml && lastOpenerAt > lastLeadingHtml) {
      const html = document.substr(
        lastLeadingHtml,
        lastOpenerAt - lastLeadingHtml
      );
      output.push(Freeform(html));
    }
    if (lastClosedBlock !== null) {
      output.push(lastClosedBlock);
    }
    offset = document.length;
  }
  return __toCommonJS(index_exports);
})();
(window.wp ||= {}).blockSerializationDefaultParser = wp.blockSerializationDefaultParser;
})();
