// packages/block-library/build-module/image/view.js
import {
  store,
  getContext,
  getElement,
  withSyncEvent,
  withScope
} from "@wordpress/interactivity";

// packages/block-library/build-module/image/constants.js
var IMAGE_PRELOAD_DELAY = 200;

// packages/block-library/build-module/image/view.js
var isTouching = false;
var lastTouchTime = 0;
function getImageSrc({ uploadedSrc }) {
  return uploadedSrc || "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
}
function getImageSrcset({ lightboxSrcset }) {
  return lightboxSrcset || "";
}
var { state, actions, callbacks } = store(
  "core/image",
  {
    state: {
      currentImageId: null,
      preloadTimers: /* @__PURE__ */ new Map(),
      preloadedImageIds: /* @__PURE__ */ new Set(),
      get currentImage() {
        return state.metadata[state.currentImageId];
      },
      get overlayOpened() {
        return state.currentImageId !== null;
      },
      get roleAttribute() {
        return state.overlayOpened ? "dialog" : null;
      },
      get ariaModal() {
        return state.overlayOpened ? "true" : null;
      },
      get enlargedSrc() {
        return getImageSrc(state.currentImage);
      },
      get enlargedSrcset() {
        return getImageSrcset(state.currentImage);
      },
      get figureStyles() {
        return state.overlayOpened && `${state.currentImage.figureStyles?.replace(
          /margin[^;]*;?/g,
          ""
        )};`;
      },
      get imgStyles() {
        return state.overlayOpened && `${state.currentImage.imgStyles?.replace(
          /;$/,
          ""
        )}; object-fit:cover;`;
      },
      get imageButtonRight() {
        const { imageId } = getContext();
        return state.metadata[imageId].imageButtonRight;
      },
      get imageButtonTop() {
        const { imageId } = getContext();
        return state.metadata[imageId].imageButtonTop;
      },
      get isContentHidden() {
        const ctx = getContext();
        return state.overlayEnabled && state.currentImageId === ctx.imageId;
      },
      get isContentVisible() {
        const ctx = getContext();
        return !state.overlayEnabled && state.currentImageId === ctx.imageId;
      }
    },
    actions: {
      showLightbox() {
        const { imageId } = getContext();
        if (!state.metadata[imageId].imageRef?.complete) {
          return;
        }
        state.scrollTopReset = document.documentElement.scrollTop;
        state.scrollLeftReset = document.documentElement.scrollLeft;
        state.overlayEnabled = true;
        state.currentImageId = imageId;
        callbacks.setOverlayStyles();
      },
      hideLightbox() {
        if (state.overlayEnabled) {
          state.overlayEnabled = false;
          setTimeout(function() {
            state.currentImage.buttonRef.focus({
              preventScroll: true
            });
            state.currentImageId = null;
          }, 450);
        }
      },
      handleKeydown: withSyncEvent((event) => {
        if (state.overlayEnabled) {
          if (event.key === "Tab") {
            event.preventDefault();
            const { ref } = getElement();
            ref.querySelector("button").focus();
          }
          if (event.key === "Escape") {
            actions.hideLightbox();
          }
        }
      }),
      handleTouchMove: withSyncEvent((event) => {
        if (state.overlayEnabled) {
          event.preventDefault();
        }
      }),
      handleTouchStart() {
        isTouching = true;
      },
      handleTouchEnd() {
        lastTouchTime = Date.now();
        isTouching = false;
      },
      handleScroll() {
        if (state.overlayOpened) {
          if (!isTouching && Date.now() - lastTouchTime > 450) {
            window.scrollTo(
              state.scrollLeftReset,
              state.scrollTopReset
            );
          }
        }
      },
      preloadImage() {
        const { imageId } = getContext();
        if (state.preloadedImageIds.has(imageId)) {
          return;
        }
        const imageMetadata = state.metadata[imageId];
        const imageLink = document.createElement("link");
        imageLink.rel = "preload";
        imageLink.as = "image";
        imageLink.href = getImageSrc(imageMetadata);
        const srcset = getImageSrcset(imageMetadata);
        if (srcset) {
          imageLink.setAttribute("imagesrcset", srcset);
          imageLink.setAttribute("imagesizes", "100vw");
        }
        document.head.appendChild(imageLink);
        state.preloadedImageIds.add(imageId);
      },
      preloadImageWithDelay() {
        const { imageId } = getContext();
        actions.cancelPreload();
        const timerId = setTimeout(
          withScope(() => {
            actions.preloadImage();
            state.preloadTimers.delete(imageId);
          }),
          IMAGE_PRELOAD_DELAY
        );
        state.preloadTimers.set(imageId, timerId);
      },
      cancelPreload() {
        const { imageId } = getContext();
        if (state.preloadTimers.has(imageId)) {
          clearTimeout(state.preloadTimers.get(imageId));
          state.preloadTimers.delete(imageId);
        }
      }
    },
    callbacks: {
      setOverlayStyles() {
        if (!state.overlayEnabled) {
          return;
        }
        let {
          naturalWidth,
          naturalHeight,
          offsetWidth: originalWidth,
          offsetHeight: originalHeight
        } = state.currentImage.imageRef;
        let { x: screenPosX, y: screenPosY } = state.currentImage.imageRef.getBoundingClientRect();
        const naturalRatio = naturalWidth / naturalHeight;
        let originalRatio = originalWidth / originalHeight;
        if (state.currentImage.scaleAttr === "contain") {
          if (naturalRatio > originalRatio) {
            const heightWithoutSpace = originalWidth / naturalRatio;
            screenPosY += (originalHeight - heightWithoutSpace) / 2;
            originalHeight = heightWithoutSpace;
          } else {
            const widthWithoutSpace = originalHeight * naturalRatio;
            screenPosX += (originalWidth - widthWithoutSpace) / 2;
            originalWidth = widthWithoutSpace;
          }
        }
        originalRatio = originalWidth / originalHeight;
        let imgMaxWidth = parseFloat(
          state.currentImage.targetWidth !== "none" ? state.currentImage.targetWidth : naturalWidth
        );
        let imgMaxHeight = parseFloat(
          state.currentImage.targetHeight !== "none" ? state.currentImage.targetHeight : naturalHeight
        );
        let imgRatio = imgMaxWidth / imgMaxHeight;
        let containerMaxWidth = imgMaxWidth;
        let containerMaxHeight = imgMaxHeight;
        let containerWidth = imgMaxWidth;
        let containerHeight = imgMaxHeight;
        if (naturalRatio.toFixed(2) !== imgRatio.toFixed(2)) {
          if (naturalRatio > imgRatio) {
            const reducedHeight = imgMaxWidth / naturalRatio;
            if (imgMaxHeight - reducedHeight > imgMaxWidth) {
              imgMaxHeight = reducedHeight;
              imgMaxWidth = reducedHeight * naturalRatio;
            } else {
              imgMaxHeight = imgMaxWidth / naturalRatio;
            }
          } else {
            const reducedWidth = imgMaxHeight * naturalRatio;
            if (imgMaxWidth - reducedWidth > imgMaxHeight) {
              imgMaxWidth = reducedWidth;
              imgMaxHeight = reducedWidth / naturalRatio;
            } else {
              imgMaxWidth = imgMaxHeight * naturalRatio;
            }
          }
          containerWidth = imgMaxWidth;
          containerHeight = imgMaxHeight;
          imgRatio = imgMaxWidth / imgMaxHeight;
          if (originalRatio > imgRatio) {
            containerMaxWidth = imgMaxWidth;
            containerMaxHeight = containerMaxWidth / originalRatio;
          } else {
            containerMaxHeight = imgMaxHeight;
            containerMaxWidth = containerMaxHeight * originalRatio;
          }
        }
        if (originalWidth > containerWidth || originalHeight > containerHeight) {
          containerWidth = originalWidth;
          containerHeight = originalHeight;
        }
        let horizontalPadding = 0;
        if (window.innerWidth > 480) {
          horizontalPadding = 80;
        } else if (window.innerWidth > 1920) {
          horizontalPadding = 160;
        }
        const verticalPadding = 80;
        const targetMaxWidth = Math.min(
          window.innerWidth - horizontalPadding,
          containerWidth
        );
        const targetMaxHeight = Math.min(
          window.innerHeight - verticalPadding,
          containerHeight
        );
        const targetContainerRatio = targetMaxWidth / targetMaxHeight;
        if (originalRatio > targetContainerRatio) {
          containerWidth = targetMaxWidth;
          containerHeight = containerWidth / originalRatio;
        } else {
          containerHeight = targetMaxHeight;
          containerWidth = containerHeight * originalRatio;
        }
        const containerScale = originalWidth / containerWidth;
        const lightboxImgWidth = imgMaxWidth * (containerWidth / containerMaxWidth);
        const lightboxImgHeight = imgMaxHeight * (containerHeight / containerMaxHeight);
        state.overlayStyles = `
					--wp--lightbox-initial-top-position: ${screenPosY}px;
					--wp--lightbox-initial-left-position: ${screenPosX}px;
					--wp--lightbox-container-width: ${containerWidth + 1}px;
					--wp--lightbox-container-height: ${containerHeight + 1}px;
					--wp--lightbox-image-width: ${lightboxImgWidth}px;
					--wp--lightbox-image-height: ${lightboxImgHeight}px;
					--wp--lightbox-scale: ${containerScale};
					--wp--lightbox-scrollbar-width: ${window.innerWidth - document.documentElement.clientWidth}px;
				`;
      },
      setButtonStyles() {
        const { ref } = getElement();
        if (!ref) {
          return;
        }
        const { imageId } = getContext();
        state.metadata[imageId].imageRef = ref;
        state.metadata[imageId].currentSrc = ref.currentSrc;
        const {
          naturalWidth,
          naturalHeight,
          offsetWidth,
          offsetHeight
        } = ref;
        if (naturalWidth === 0 || naturalHeight === 0) {
          return;
        }
        const figure = ref.parentElement;
        const figureWidth = ref.parentElement.clientWidth;
        let figureHeight = ref.parentElement.clientHeight;
        const caption = figure.querySelector("figcaption");
        if (caption) {
          const captionComputedStyle = window.getComputedStyle(caption);
          if (!["absolute", "fixed"].includes(
            captionComputedStyle.position
          )) {
            figureHeight = figureHeight - caption.offsetHeight - parseFloat(captionComputedStyle.marginTop) - parseFloat(captionComputedStyle.marginBottom);
          }
        }
        const buttonOffsetTop = figureHeight - offsetHeight;
        const buttonOffsetRight = figureWidth - offsetWidth;
        let imageButtonTop = buttonOffsetTop + 16;
        let imageButtonRight = buttonOffsetRight + 16;
        if (state.metadata[imageId].scaleAttr === "contain") {
          const naturalRatio = naturalWidth / naturalHeight;
          const offsetRatio = offsetWidth / offsetHeight;
          if (naturalRatio >= offsetRatio) {
            const referenceHeight = offsetWidth / naturalRatio;
            imageButtonTop = (offsetHeight - referenceHeight) / 2 + buttonOffsetTop + 16;
            imageButtonRight = buttonOffsetRight + 16;
          } else {
            const referenceWidth = offsetHeight * naturalRatio;
            imageButtonTop = buttonOffsetTop + 16;
            imageButtonRight = (offsetWidth - referenceWidth) / 2 + buttonOffsetRight + 16;
          }
        }
        state.metadata[imageId].imageButtonTop = imageButtonTop;
        state.metadata[imageId].imageButtonRight = imageButtonRight;
      },
      setOverlayFocus() {
        if (state.overlayEnabled) {
          const { ref } = getElement();
          ref.focus();
        }
      },
      initTriggerButton() {
        const { imageId } = getContext();
        const { ref } = getElement();
        state.metadata[imageId].buttonRef = ref;
      }
    }
  },
  { lock: true }
);
//# sourceMappingURL=view.js.map
