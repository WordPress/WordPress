// packages/block-library/build-module/image/view.mjs
import {
  store,
  getContext,
  getElement,
  getConfig,
  withSyncEvent,
  withScope
} from "@wordpress/interactivity";

// packages/block-library/build-module/image/constants.mjs
var IMAGE_PRELOAD_DELAY = 200;

// packages/block-library/build-module/image/view.mjs
var isTouching = false;
var lastTouchTime = 0;
var touchStartEvent = {
  startX: 0,
  startY: 0,
  startTime: 0
};
var focusableSelectors = [
  ".wp-lightbox-close-button",
  ".wp-lightbox-navigation-button"
];
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
      selectedImageId: null,
      selectedGalleryId: null,
      preloadTimers: /* @__PURE__ */ new Map(),
      preloadedImageIds: /* @__PURE__ */ new Set(),
      get galleryImages() {
        if (!state.selectedGalleryId) {
          return [state.selectedImageId];
        }
        return Object.entries(state.metadata).filter(
          ([, value]) => value.galleryId === state.selectedGalleryId
        ).sort(([, a], [, b]) => {
          const orderA = a.order ?? 0;
          const orderB = b.order ?? 0;
          return orderA - orderB;
        }).map(([key]) => key);
      },
      get selectedImageIndex() {
        return state.galleryImages.findIndex(
          (id) => id === state.selectedImageId
        );
      },
      get selectedImage() {
        return state.metadata[state.selectedImageId];
      },
      get hasNavigationIcon() {
        const { navigationButtonType } = state.selectedImage;
        return navigationButtonType === "icon" || navigationButtonType === "both";
      },
      get hasNavigationText() {
        const { navigationButtonType } = state.selectedImage;
        return navigationButtonType === "text" || navigationButtonType === "both";
      },
      get thisImage() {
        const { imageId } = getContext();
        return state.metadata[imageId];
      },
      get hasNavigation() {
        return state.galleryImages.length > 1;
      },
      get hasNextImage() {
        return state.selectedImageIndex + 1 < state.galleryImages.length;
      },
      get hasPreviousImage() {
        return state.selectedImageIndex - 1 >= 0;
      },
      get overlayOpened() {
        return state.selectedImageId !== null;
      },
      get roleAttribute() {
        return state.overlayOpened ? "dialog" : null;
      },
      get ariaModal() {
        return state.overlayOpened ? "true" : null;
      },
      get ariaLabel() {
        return state.selectedImage.customAriaLabel || getConfig().defaultAriaLabel;
      },
      get closeButtonAriaLabel() {
        return state.hasNavigationText ? void 0 : getConfig().closeButtonText;
      },
      get prevButtonAriaLabel() {
        return state.hasNavigationText ? void 0 : getConfig().prevButtonText;
      },
      get nextButtonAriaLabel() {
        return state.hasNavigationText ? void 0 : getConfig().nextButtonText;
      },
      get enlargedSrc() {
        return getImageSrc(state.selectedImage);
      },
      get enlargedSrcset() {
        return getImageSrcset(state.selectedImage);
      },
      get figureStyles() {
        return state.overlayOpened && `${state.selectedImage.figureStyles?.replace(
          /margin[^;]*;?/g,
          ""
        )};`;
      },
      get imgStyles() {
        return state.overlayOpened && `${state.selectedImage.imgStyles?.replace(
          /;$/,
          ""
        )}; object-fit:cover;`;
      },
      get isContentHidden() {
        const ctx = getContext();
        return state.overlayEnabled && state.selectedImageId === ctx.imageId;
      },
      get isContentVisible() {
        const ctx = getContext();
        return !state.overlayEnabled && state.selectedImageId === ctx.imageId;
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
        state.selectedImageId = imageId;
        const { galleryId } = getContext("core/gallery") || {};
        state.selectedGalleryId = galleryId || null;
        state.overlayEnabled = true;
        callbacks.setOverlayStyles();
      },
      hideLightbox() {
        if (state.overlayEnabled) {
          state.overlayEnabled = false;
          setTimeout(function() {
            state.selectedImage.buttonRef.focus({
              preventScroll: true
            });
            state.selectedImageId = null;
            state.selectedGalleryId = null;
          }, 450);
        }
      },
      showPreviousImage: withSyncEvent((event) => {
        event.stopPropagation();
        const nextIndex = state.hasPreviousImage ? state.selectedImageIndex - 1 : state.galleryImages.length - 1;
        state.selectedImageId = state.galleryImages[nextIndex];
        callbacks.setOverlayStyles();
      }),
      showNextImage: withSyncEvent((event) => {
        event.stopPropagation();
        const nextIndex = state.hasNextImage ? state.selectedImageIndex + 1 : 0;
        state.selectedImageId = state.galleryImages[nextIndex];
        callbacks.setOverlayStyles();
      }),
      handleKeydown: withSyncEvent((event) => {
        if (state.overlayEnabled) {
          if (event.key === "Escape") {
            actions.hideLightbox();
          } else if (event.key === "ArrowLeft") {
            actions.showPreviousImage(event);
          } else if (event.key === "ArrowRight") {
            actions.showNextImage(event);
          } else if (event.key === "Tab") {
            const focusableElements = Array.from(
              document.querySelectorAll(focusableSelectors)
            );
            const firstFocusableElement = focusableElements[0];
            const lastFocusableElement = focusableElements[focusableElements.length - 1];
            if (event.shiftKey && event.target === firstFocusableElement) {
              event.preventDefault();
              lastFocusableElement.focus();
            } else if (!event.shiftKey && event.target === lastFocusableElement) {
              event.preventDefault();
              firstFocusableElement.focus();
            }
          }
        }
      }),
      handleTouchMove: withSyncEvent((event) => {
        if (state.overlayEnabled) {
          event.preventDefault();
        }
      }),
      handleTouchStart(event) {
        isTouching = true;
        const t = event.touches && event.touches[0];
        if (t) {
          touchStartEvent.startX = t.clientX;
          touchStartEvent.startY = t.clientY;
          touchStartEvent.startTime = Date.now();
        }
      },
      handleTouchEnd: withSyncEvent((event) => {
        const touchEndEvent = event.changedTouches && event.changedTouches[0] || event.touches && event.touches[0];
        const now = Date.now();
        if (touchEndEvent && state.overlayEnabled) {
          const deltaX = touchEndEvent.clientX - touchStartEvent.startX;
          const deltaY = touchEndEvent.clientY - touchStartEvent.startY;
          const absDeltaX = Math.abs(deltaX);
          const absDeltaY = Math.abs(deltaY);
          const elapsedMs = now - touchStartEvent.startTime;
          const isHorizontalSwipe = (
            // Swipe distance is greater than 50px
            absDeltaX > 50 && // Horizontal movement is much larger than the vertical movement
            absDeltaX > absDeltaY * 1.5 && // Fast action of less than 800ms
            elapsedMs < 800
          );
          if (isHorizontalSwipe) {
            event.preventDefault();
            if (deltaX < 0) {
              actions.showNextImage(event);
            } else {
              actions.showPreviousImage(event);
            }
          }
        }
        lastTouchTime = now;
        isTouching = false;
      }),
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
        } = state.selectedImage.imageRef;
        let { x: screenPosX, y: screenPosY } = state.selectedImage.imageRef.getBoundingClientRect();
        const naturalRatio = naturalWidth / naturalHeight;
        let originalRatio = originalWidth / originalHeight;
        if (state.selectedImage.scaleAttr === "contain") {
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
          state.selectedImage.targetWidth && state.selectedImage.targetWidth !== "none" ? state.selectedImage.targetWidth : naturalWidth
        );
        let imgMaxHeight = parseFloat(
          state.selectedImage.targetHeight && state.selectedImage.targetHeight !== "none" ? state.selectedImage.targetHeight : naturalHeight
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
        let verticalPadding = 160;
        if (480 < window.innerWidth) {
          horizontalPadding = 80;
          verticalPadding = 160;
        }
        if (960 < window.innerWidth) {
          horizontalPadding = state.hasNavigation ? 320 : 80;
          verticalPadding = 80;
        }
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
        let buttonTop = buttonOffsetTop + 16;
        let buttonRight = buttonOffsetRight + 16;
        if (state.metadata[imageId].scaleAttr === "contain") {
          const naturalRatio = naturalWidth / naturalHeight;
          const offsetRatio = offsetWidth / offsetHeight;
          if (naturalRatio >= offsetRatio) {
            const referenceHeight = offsetWidth / naturalRatio;
            buttonTop = (offsetHeight - referenceHeight) / 2 + buttonOffsetTop + 16;
            buttonRight = buttonOffsetRight + 16;
          } else {
            const referenceWidth = offsetHeight * naturalRatio;
            buttonTop = buttonOffsetTop + 16;
            buttonRight = (offsetWidth - referenceWidth) / 2 + buttonOffsetRight + 16;
          }
        }
        state.metadata[imageId].buttonTop = buttonTop;
        state.metadata[imageId].buttonRight = buttonRight;
      },
      setOverlayFocus() {
        if (state.overlayEnabled) {
          const { ref } = getElement();
          ref.focus();
        }
      },
      setInertElements() {
        document.querySelectorAll("body > :not(.wp-lightbox-overlay)").forEach((el) => {
          if (state.overlayEnabled) {
            el.setAttribute("inert", "");
          } else {
            el.removeAttribute("inert");
          }
        });
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