var __webpack_exports__ = {};

;// ./node_modules/@wordpress/block-editor/build-module/utils/fit-text-utils.js
function generateCSSRule(elementSelector, fontSize) {
  return `${elementSelector} { font-size: ${fontSize}px !important; }`;
}
function findOptimalFontSize(textElement, elementSelector, applyStylesFn) {
  const alreadyHasScrollableHeight = textElement.scrollHeight > textElement.clientHeight;
  let minSize = 5;
  let maxSize = 600;
  let bestSize = minSize;
  while (minSize <= maxSize) {
    const midSize = Math.floor((minSize + maxSize) / 2);
    applyStylesFn(generateCSSRule(elementSelector, midSize));
    const fitsWidth = textElement.scrollWidth <= textElement.clientWidth;
    const fitsHeight = alreadyHasScrollableHeight || textElement.scrollHeight <= textElement.clientHeight;
    if (fitsWidth && fitsHeight) {
      bestSize = midSize;
      minSize = midSize + 1;
    } else {
      maxSize = midSize - 1;
    }
  }
  return bestSize;
}
function optimizeFitText(textElement, elementSelector, applyStylesFn) {
  if (!textElement) {
    return;
  }
  applyStylesFn("");
  const optimalSize = findOptimalFontSize(
    textElement,
    elementSelector,
    applyStylesFn
  );
  const cssRule = generateCSSRule(elementSelector, optimalSize);
  applyStylesFn(cssRule);
}


;// ./node_modules/@wordpress/block-editor/build-module/utils/fit-text-frontend.js

let idCounter = 0;
function getOrCreateStyleElement(elementId) {
  const styleId = `fit-text-${elementId}`;
  let styleElement = document.getElementById(styleId);
  if (!styleElement) {
    styleElement = document.createElement("style");
    styleElement.id = styleId;
    document.head.appendChild(styleElement);
  }
  return styleElement;
}
function getElementIdentifier(element) {
  if (!element.dataset.fitTextId) {
    element.dataset.fitTextId = `fit-text-${++idCounter}`;
  }
  return element.dataset.fitTextId;
}
function initializeFitText(element) {
  const elementId = getElementIdentifier(element);
  const applyFitText = () => {
    const styleElement = getOrCreateStyleElement(elementId);
    const elementSelector = `[data-fit-text-id="${elementId}"]`;
    const applyStylesFn = (css) => {
      styleElement.textContent = css;
    };
    optimizeFitText(element, elementSelector, applyStylesFn);
  };
  applyFitText();
  if (window.ResizeObserver && element.parentElement) {
    const resizeObserver = new window.ResizeObserver(applyFitText);
    resizeObserver.observe(element.parentElement);
  }
}
function initializeAllFitText() {
  const elements = document.querySelectorAll(".has-fit-text");
  elements.forEach(initializeFitText);
}
window.addEventListener("load", initializeAllFitText);

