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

  // packages/block-serialization-default-parser/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    parse: () => parse
  });
  var document;
  var offset;
  var output;
  var stack;
  var tokenizer = /<!--\s+(\/)?wp:([a-z][a-z0-9_-]*\/)?([a-z][a-z0-9_-]*)\s+({(?:(?=([^}]+|}+(?=})|(?!}\s+\/?-->)[^])*)\5|[^]*?)}\s+)?(\/)?-->/g;
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
    tokenizer.lastIndex = 0;
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
        if (1 === stackDepth) {
          addBlockFromStack();
          return false;
        }
        while (0 < stack.length) {
          addBlockFromStack();
        }
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
        stackTop.block.innerHTML += html;
        stackTop.block.innerContent.push(html);
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
    } catch (e) {
      return null;
    }
  }
  function nextToken() {
    const matches = tokenizer.exec(document);
    if (null === matches) {
      return ["no-more-tokens", "", null, 0, 0];
    }
    const startedAt = matches.index;
    const [
      match,
      closerMatch,
      namespaceMatch,
      nameMatch,
      attrsMatch,
      ,
      voidMatch
    ] = matches;
    const length = match.length;
    const isCloser = !!closerMatch;
    const isVoid = !!voidMatch;
    const namespace = namespaceMatch || "core/";
    const name = namespace + nameMatch;
    const hasAttrs = !!attrsMatch;
    const attrs = hasAttrs ? parseJSON(attrsMatch) : {};
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
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
