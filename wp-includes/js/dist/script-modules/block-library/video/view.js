// packages/block-library/build-module/video/view.mjs
import { getElement, store } from "@wordpress/interactivity";

// packages/block-library/build-module/utils/reduced-motion.mjs
function prefersReducedMotion() {
  return window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches === true;
}

// packages/block-library/build-module/video/view.mjs
store(
  "core/video",
  {
    callbacks: {
      stopAutoplay() {
        if (!prefersReducedMotion()) {
          return;
        }
        const { ref } = getElement();
        ref.autoplay = false;
        ref.pause();
      }
    }
  },
  { lock: true }
);
