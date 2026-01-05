// packages/block-editor/build-module/utils/fit-text-frontend.js
import { store, getElement, getContext } from "@wordpress/interactivity";

// packages/block-editor/build-module/utils/fit-text-utils.js
function findOptimalFontSize(textElement, applyFontSize) {
  const alreadyHasScrollableHeight = textElement.scrollHeight > textElement.clientHeight;
  let minSize = 0;
  let maxSize = 2400;
  let bestSize = minSize;
  const computedStyle = window.getComputedStyle(textElement);
  const paddingLeft = parseFloat(computedStyle.paddingLeft) || 0;
  const paddingRight = parseFloat(computedStyle.paddingRight) || 0;
  const range = document.createRange();
  range.selectNodeContents(textElement);
  while (minSize <= maxSize) {
    const midSize = Math.floor((minSize + maxSize) / 2);
    applyFontSize(midSize);
    const rect = range.getBoundingClientRect();
    const textWidth = rect.width;
    const fitsWidth = textElement.scrollWidth <= textElement.clientWidth && textWidth <= textElement.clientWidth - paddingLeft - paddingRight;
    const fitsHeight = alreadyHasScrollableHeight || textElement.scrollHeight <= textElement.clientHeight;
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

// packages/block-editor/build-module/utils/fit-text-frontend.js
store("core/fit-text", {
  callbacks: {
    init() {
      const context = getContext();
      const { ref } = getElement();
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