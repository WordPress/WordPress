// packages/block-library/build-module/cover/view.mjs
import { getContext, getElement, store } from "@wordpress/interactivity";

// packages/block-library/build-module/utils/reduced-motion.mjs
function prefersReducedMotion() {
  return window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches === true;
}

// packages/block-library/build-module/cover/view.mjs
store(
  "core/cover",
  {
    state: {
      get videoSrc() {
        const { src, reducedMotionSrc } = getContext();
        if (reducedMotionSrc && prefersReducedMotion()) {
          return reducedMotionSrc;
        }
        return src;
      }
    },
    callbacks: {
      stopBackgroundVideo() {
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
