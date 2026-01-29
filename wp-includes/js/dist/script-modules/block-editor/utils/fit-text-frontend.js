import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ // The require scope
/******/ var __webpack_require__ = {};
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};

;// external "@wordpress/interactivity"
var x = (y) => {
	var x = {}; __webpack_require__.d(x, y); return x
} 
var y = (x) => (() => (x))
const interactivity_namespaceObject = x({ ["getContext"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getContext), ["getElement"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getElement), ["store"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store) });
;// ./node_modules/@wordpress/block-editor/build-module/utils/fit-text-utils.js
function findOptimalFontSize(textElement, applyFontSize) {
  const alreadyHasScrollableHeight = textElement.scrollHeight > textElement.clientHeight;
  let minSize = 5;
  let maxSize = 2400;
  let bestSize = minSize;
  const computedStyle = window.getComputedStyle(textElement);
  let paddingLeft = parseFloat(computedStyle.paddingLeft) || 0;
  let paddingRight = parseFloat(computedStyle.paddingRight) || 0;
  const range = document.createRange();
  range.selectNodeContents(textElement);
  let referenceElement = textElement;
  const parentElement = textElement.parentElement;
  if (parentElement) {
    const parentElementComputedStyle = window.getComputedStyle(parentElement);
    if (parentElementComputedStyle?.display === "flex") {
      referenceElement = parentElement;
      paddingLeft += parseFloat(parentElementComputedStyle.paddingLeft) || 0;
      paddingRight += parseFloat(parentElementComputedStyle.paddingRight) || 0;
    }
  }
  let maxclientHeight = referenceElement.clientHeight;
  while (minSize <= maxSize) {
    const midSize = Math.floor((minSize + maxSize) / 2);
    applyFontSize(midSize);
    const rect = range.getBoundingClientRect();
    const textWidth = rect.width;
    const fitsWidth = textElement.scrollWidth <= referenceElement.clientWidth && textWidth <= referenceElement.clientWidth - paddingLeft - paddingRight;
    const fitsHeight = alreadyHasScrollableHeight || textElement.scrollHeight <= referenceElement.clientHeight || textElement.scrollHeight <= maxclientHeight;
    if (referenceElement.clientHeight > maxclientHeight) {
      maxclientHeight = referenceElement.clientHeight;
    }
    if (fitsWidth && fitsHeight) {
      bestSize = midSize;
      minSize = midSize + 1;
    } else {
      maxSize = midSize - 1;
    }
  }
  range.detach();
  return bestSize;
}
function optimizeFitText(textElement, applyFontSize) {
  if (!textElement) {
    return;
  }
  applyFontSize(0);
  const optimalSize = findOptimalFontSize(textElement, applyFontSize);
  applyFontSize(optimalSize);
  return optimalSize;
}


;// ./node_modules/@wordpress/block-editor/build-module/utils/fit-text-frontend.js


(0,interactivity_namespaceObject.store)("core/fit-text", {
  callbacks: {
    init() {
      const context = (0,interactivity_namespaceObject.getContext)();
      const { ref } = (0,interactivity_namespaceObject.getElement)();
      const applyFontSize = (fontSize) => {
        if (fontSize === 0) {
          ref.style.fontSize = "";
        } else {
          ref.style.fontSize = `${fontSize}px`;
        }
      };
      context.fontSize = optimizeFitText(ref, applyFontSize);
      if (window.ResizeObserver && ref.parentElement) {
        const resizeObserver = new window.ResizeObserver(() => {
          context.fontSize = optimizeFitText(ref, applyFontSize);
        });
        resizeObserver.observe(ref.parentElement);
        resizeObserver.observe(ref);
        return () => {
          if (resizeObserver) {
            resizeObserver.disconnect();
          }
        };
      }
    }
  }
});

