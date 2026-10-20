// packages/block-library/build-module/cover/view.mjs
import { getContext, store } from "@wordpress/interactivity";
import { prefersReducedMotion } from "@wordpress/a11y";
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
    }
  },
  { lock: true }
);
