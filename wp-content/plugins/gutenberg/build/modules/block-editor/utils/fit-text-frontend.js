// packages/block-editor/build-module/utils/fit-text-frontend.js
import { store, getElement, getContext } from "@wordpress/interactivity";

// packages/block-editor/build-module/utils/fit-text-utils.js
function findOptimalFontSize(textElement, applyFontSize) {
  const alreadyHasScrollableHeight = textElement.scrollHeight > textElement.clientHeight;
  let minSize = 5;
  let maxSize = 2400;
  let bestSize = minSize;
  while (minSize <= maxSize) {
    const midSize = Math.floor((minSize + maxSize) / 2);
    applyFontSize(midSize);
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
//# sourceMappingURL=fit-text-frontend.js.map
