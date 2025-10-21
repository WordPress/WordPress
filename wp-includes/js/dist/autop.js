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
/* harmony export */   autop: () => (/* binding */ autop),
/* harmony export */   removep: () => (/* binding */ removep)
/* harmony export */ });
const htmlSplitRegex = (() => {
  const comments = "!(?:-(?!->)[^\\-]*)*(?:-->)?";
  const cdata = "!\\[CDATA\\[[^\\]]*(?:](?!]>)[^\\]]*)*?(?:]]>)?";
  const escaped = "(?=!--|!\\[CDATA\\[)((?=!-)" + // If yes, which type?
  comments + "|" + cdata + ")";
  const regex = "(<(" + // Conditional expression follows.
  escaped + // Find end of escaped element.
  "|[^>]*>?))";
  return new RegExp(regex);
})();
function htmlSplit(input) {
  const parts = [];
  let workingInput = input;
  let match;
  while (match = workingInput.match(htmlSplitRegex)) {
    const index = match.index;
    parts.push(workingInput.slice(0, index));
    parts.push(match[0]);
    workingInput = workingInput.slice(index + match[0].length);
  }
  if (workingInput.length) {
    parts.push(workingInput);
  }
  return parts;
}
function replaceInHtmlTags(haystack, replacePairs) {
  const textArr = htmlSplit(haystack);
  let changed = false;
  const needles = Object.keys(replacePairs);
  for (let i = 1; i < textArr.length; i += 2) {
    for (let j = 0; j < needles.length; j++) {
      const needle = needles[j];
      if (-1 !== textArr[i].indexOf(needle)) {
        textArr[i] = textArr[i].replace(
          new RegExp(needle, "g"),
          replacePairs[needle]
        );
        changed = true;
        break;
      }
    }
  }
  if (changed) {
    haystack = textArr.join("");
  }
  return haystack;
}
function autop(text, br = true) {
  const preTags = [];
  if (text.trim() === "") {
    return "";
  }
  text = text + "\n";
  if (text.indexOf("<pre") !== -1) {
    const textParts = text.split("</pre>");
    const lastText = textParts.pop();
    text = "";
    for (let i = 0; i < textParts.length; i++) {
      const textPart = textParts[i];
      const start = textPart.indexOf("<pre");
      if (start === -1) {
        text += textPart;
        continue;
      }
      const name = "<pre wp-pre-tag-" + i + "></pre>";
      preTags.push([name, textPart.substr(start) + "</pre>"]);
      text += textPart.substr(0, start) + name;
    }
    text += lastText;
  }
  text = text.replace(/<br\s*\/?>\s*<br\s*\/?>/g, "\n\n");
  const allBlocks = "(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)";
  text = text.replace(
    new RegExp("(<" + allBlocks + "[\\s/>])", "g"),
    "\n\n$1"
  );
  text = text.replace(
    new RegExp("(</" + allBlocks + ">)", "g"),
    "$1\n\n"
  );
  text = text.replace(/\r\n|\r/g, "\n");
  text = replaceInHtmlTags(text, { "\n": " <!-- wpnl --> " });
  if (text.indexOf("<option") !== -1) {
    text = text.replace(/\s*<option/g, "<option");
    text = text.replace(/<\/option>\s*/g, "</option>");
  }
  if (text.indexOf("</object>") !== -1) {
    text = text.replace(/(<object[^>]*>)\s*/g, "$1");
    text = text.replace(/\s*<\/object>/g, "</object>");
    text = text.replace(/\s*(<\/?(?:param|embed)[^>]*>)\s*/g, "$1");
  }
  if (text.indexOf("<source") !== -1 || text.indexOf("<track") !== -1) {
    text = text.replace(/([<\[](?:audio|video)[^>\]]*[>\]])\s*/g, "$1");
    text = text.replace(/\s*([<\[]\/(?:audio|video)[>\]])/g, "$1");
    text = text.replace(/\s*(<(?:source|track)[^>]*>)\s*/g, "$1");
  }
  if (text.indexOf("<figcaption") !== -1) {
    text = text.replace(/\s*(<figcaption[^>]*>)/, "$1");
    text = text.replace(/<\/figcaption>\s*/, "</figcaption>");
  }
  text = text.replace(/\n\n+/g, "\n\n");
  const texts = text.split(/\n\s*\n/).filter(Boolean);
  text = "";
  texts.forEach((textPiece) => {
    text += "<p>" + textPiece.replace(/^\n*|\n*$/g, "") + "</p>\n";
  });
  text = text.replace(/<p>\s*<\/p>/g, "");
  text = text.replace(
    /<p>([^<]+)<\/(div|address|form)>/g,
    "<p>$1</p></$2>"
  );
  text = text.replace(
    new RegExp("<p>\\s*(</?" + allBlocks + "[^>]*>)\\s*</p>", "g"),
    "$1"
  );
  text = text.replace(/<p>(<li.+?)<\/p>/g, "$1");
  text = text.replace(/<p><blockquote([^>]*)>/gi, "<blockquote$1><p>");
  text = text.replace(/<\/blockquote><\/p>/g, "</p></blockquote>");
  text = text.replace(
    new RegExp("<p>\\s*(</?" + allBlocks + "[^>]*>)", "g"),
    "$1"
  );
  text = text.replace(
    new RegExp("(</?" + allBlocks + "[^>]*>)\\s*</p>", "g"),
    "$1"
  );
  if (br) {
    text = text.replace(
      /<(script|style).*?<\/\\1>/g,
      (match) => match[0].replace(/\n/g, "<WPPreserveNewline />")
    );
    text = text.replace(/<br>|<br\/>/g, "<br />");
    text = text.replace(
      /(<br \/>)?\s*\n/g,
      (a, b) => b ? a : "<br />\n"
    );
    text = text.replace(/<WPPreserveNewline \/>/g, "\n");
  }
  text = text.replace(
    new RegExp("(</?" + allBlocks + "[^>]*>)\\s*<br />", "g"),
    "$1"
  );
  text = text.replace(
    /<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)/g,
    "$1"
  );
  text = text.replace(/\n<\/p>$/g, "</p>");
  preTags.forEach((preTag) => {
    const [name, original] = preTag;
    text = text.replace(name, original);
  });
  if (-1 !== text.indexOf("<!-- wpnl -->")) {
    text = text.replace(/\s?<!-- wpnl -->\s?/g, "\n");
  }
  return text;
}
function removep(html) {
  const blocklist = "blockquote|ul|ol|li|dl|dt|dd|table|thead|tbody|tfoot|tr|th|td|h[1-6]|fieldset|figure";
  const blocklist1 = blocklist + "|div|p";
  const blocklist2 = blocklist + "|pre";
  const preserve = [];
  let preserveLinebreaks = false;
  let preserveBr = false;
  if (!html) {
    return "";
  }
  if (html.indexOf("<script") !== -1 || html.indexOf("<style") !== -1) {
    html = html.replace(
      /<(script|style)[^>]*>[\s\S]*?<\/\1>/g,
      (match) => {
        preserve.push(match);
        return "<wp-preserve>";
      }
    );
  }
  if (html.indexOf("<pre") !== -1) {
    preserveLinebreaks = true;
    html = html.replace(/<pre[^>]*>[\s\S]+?<\/pre>/g, (a) => {
      a = a.replace(/<br ?\/?>(\r\n|\n)?/g, "<wp-line-break>");
      a = a.replace(/<\/?p( [^>]*)?>(\r\n|\n)?/g, "<wp-line-break>");
      return a.replace(/\r?\n/g, "<wp-line-break>");
    });
  }
  if (html.indexOf("[caption") !== -1) {
    preserveBr = true;
    html = html.replace(/\[caption[\s\S]+?\[\/caption\]/g, (a) => {
      return a.replace(/<br([^>]*)>/g, "<wp-temp-br$1>").replace(/[\r\n\t]+/, "");
    });
  }
  html = html.replace(
    new RegExp("\\s*</(" + blocklist1 + ")>\\s*", "g"),
    "</$1>\n"
  );
  html = html.replace(
    new RegExp("\\s*<((?:" + blocklist1 + ")(?: [^>]*)?)>", "g"),
    "\n<$1>"
  );
  html = html.replace(/(<p [^>]+>[\s\S]*?)<\/p>/g, "$1</p#>");
  html = html.replace(/<div( [^>]*)?>\s*<p>/gi, "<div$1>\n\n");
  html = html.replace(/\s*<p>/gi, "");
  html = html.replace(/\s*<\/p>\s*/gi, "\n\n");
  html = html.replace(/\n[\s\u00a0]+\n/g, "\n\n");
  html = html.replace(/(\s*)<br ?\/?>\s*/gi, (_, space) => {
    if (space && space.indexOf("\n") !== -1) {
      return "\n\n";
    }
    return "\n";
  });
  html = html.replace(/\s*<div/g, "\n<div");
  html = html.replace(/<\/div>\s*/g, "</div>\n");
  html = html.replace(
    /\s*\[caption([^\[]+)\[\/caption\]\s*/gi,
    "\n\n[caption$1[/caption]\n\n"
  );
  html = html.replace(/caption\]\n\n+\[caption/g, "caption]\n\n[caption");
  html = html.replace(
    new RegExp("\\s*<((?:" + blocklist2 + ")(?: [^>]*)?)\\s*>", "g"),
    "\n<$1>"
  );
  html = html.replace(
    new RegExp("\\s*</(" + blocklist2 + ")>\\s*", "g"),
    "</$1>\n"
  );
  html = html.replace(/<((li|dt|dd)[^>]*)>/g, " 	<$1>");
  if (html.indexOf("<option") !== -1) {
    html = html.replace(/\s*<option/g, "\n<option");
    html = html.replace(/\s*<\/select>/g, "\n</select>");
  }
  if (html.indexOf("<hr") !== -1) {
    html = html.replace(/\s*<hr( [^>]*)?>\s*/g, "\n\n<hr$1>\n\n");
  }
  if (html.indexOf("<object") !== -1) {
    html = html.replace(/<object[\s\S]+?<\/object>/g, (a) => {
      return a.replace(/[\r\n]+/g, "");
    });
  }
  html = html.replace(/<\/p#>/g, "</p>\n");
  html = html.replace(/\s*(<p [^>]+>[\s\S]*?<\/p>)/g, "\n$1");
  html = html.replace(/^\s+/, "");
  html = html.replace(/[\s\u00a0]+$/, "");
  if (preserveLinebreaks) {
    html = html.replace(/<wp-line-break>/g, "\n");
  }
  if (preserveBr) {
    html = html.replace(/<wp-temp-br([^>]*)>/g, "<br$1>");
  }
  if (preserve.length) {
    html = html.replace(/<wp-preserve>/g, () => {
      return preserve.shift();
    });
  }
  return html;
}


(window.wp = window.wp || {}).autop = __webpack_exports__;
/******/ })()
;