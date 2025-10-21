/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   parse: () => (/* binding */ parse)
/* harmony export */ });
let document;
let offset;
let output;
let stack;
const tokenizer = /<!--\s+(\/)?wp:([a-z][a-z0-9_-]*\/)?([a-z][a-z0-9_-]*)\s+({(?:(?=([^}]+|}+(?=})|(?!}\s+\/?-->)[^])*)\5|[^]*?)}\s+)?(\/)?-->/g;
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
const parse = (doc) => {
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


(window.wp = window.wp || {}).blockSerializationDefaultParser = __webpack_exports__;
/******/ })()
;