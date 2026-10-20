// packages/block-library/build-module/image/view.mjs
import {
  store,
  getContext,
  getElement,
  getConfig,
  withSyncEvent,
  withScope,
  splitTask
} from "@wordpress/interactivity";

// packages/block-library/build-module/utils/reduced-motion.mjs
function prefersReducedMotion() {
  return window.matchMedia?.("(prefers-reduced-motion: reduce)")?.matches === true;
}

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
var inertElements = [];
var touchDrag = {
  isDragging: false,
  direction: null,
  // The elements the gesture moves. The drag offset is written on these
  // rather than on the overlay, whose `style` attribute is bound to
  // `state.overlayStyles`: the framework rewrites that attribute whole
  // whenever the bound value changes, so a resize part way through a gesture
  // used to wipe the drag out from under the finger. Nothing is bound to the
  // containers, so what is written here stays written.
  slideEls: [],
  // Animations currently running on `slideEls`, so an interrupted gesture can
  // stop them.
  animations: [],
  // Finishes a committed swipe once its slide is over. Held so that a new
  // gesture can settle a swipe still in flight rather than stranding it.
  settle: null
};
var SLIDE_DURATION_MS = 220;
var FADE_DURATION_MS = 300;
var SLIDE_EASING = "cubic-bezier(0.2, 0.7, 0.2, 1)";
var DIRECTION_LATCH_THRESHOLD_PX = 10;
var HORIZONTAL_DOMINANCE_RATIO = 1.5;
var COMMIT_DISTANCE_RATIO = 0.2;
var FLICK_DISTANCE_PX = 50;
var FLICK_MAX_DURATION_MS = 300;
function getSlidePitch() {
  return window.innerWidth;
}
function slideTransform(offset) {
  return `translate(calc(-50% + ${offset}px), -50%)`;
}
function setDragOffset(els, offset) {
  els.forEach((el) => {
    if (offset) {
      el.style.setProperty(
        "--wp--lightbox-drag-offset",
        `${offset}px`
      );
    } else {
      el.style.removeProperty("--wp--lightbox-drag-offset");
    }
  });
}
function stopDragAnimations() {
  touchDrag.animations.forEach((animation) => animation.cancel());
  touchDrag.animations = [];
}
function resetTouchDrag() {
  touchDrag.isDragging = false;
  touchDrag.direction = null;
  touchDrag.slideEls = [];
}
async function waitForImage(img) {
  await splitTask();
  try {
    await img?.decode();
  } catch {
  }
}
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
        if (!state.overlayEnabled) {
          return;
        }
        event.preventDefault();
        const t = event.touches && event.touches[0];
        if (!t || !state.hasNavigation) {
          return;
        }
        const deltaX = t.clientX - touchStartEvent.startX;
        const deltaY = t.clientY - touchStartEvent.startY;
        const absDeltaX = Math.abs(deltaX);
        const absDeltaY = Math.abs(deltaY);
        if (!touchDrag.direction && (absDeltaX > DIRECTION_LATCH_THRESHOLD_PX || absDeltaY > DIRECTION_LATCH_THRESHOLD_PX)) {
          touchDrag.direction = absDeltaX > absDeltaY ? "horizontal" : "vertical";
          touchDrag.slideEls = Array.from(
            event.currentTarget.querySelectorAll(
              ".lightbox-image-container"
            )
          );
        }
        if (touchDrag.direction === "horizontal" && touchDrag.slideEls.length) {
          touchDrag.isDragging = true;
          setDragOffset(touchDrag.slideEls, deltaX);
        }
      }),
      handleTouchStart(event) {
        isTouching = true;
        if (event.touches?.length > 1) {
          return;
        }
        const t = event.touches && event.touches[0];
        if (t) {
          touchStartEvent.startX = t.clientX;
          touchStartEvent.startY = t.clientY;
          touchStartEvent.startTime = Date.now();
        }
        touchDrag.settle?.();
        stopDragAnimations();
        resetTouchDrag();
      },
      handleTouchCancel() {
        stopDragAnimations();
        setDragOffset(touchDrag.slideEls, 0);
        resetTouchDrag();
        lastTouchTime = Date.now();
        isTouching = false;
      },
      handleTouchEnd: withSyncEvent((event) => {
        if (event.touches?.length) {
          return;
        }
        const touchEndEvent = event.changedTouches && event.changedTouches[0] || event.touches && event.touches[0];
        const now = Date.now();
        if (touchEndEvent && state.overlayEnabled && touchDrag.isDragging) {
          event.preventDefault();
          const deltaX = touchEndEvent.clientX - touchStartEvent.startX;
          const deltaY = touchEndEvent.clientY - touchStartEvent.startY;
          const absDeltaX = Math.abs(deltaX);
          const absDeltaY = Math.abs(deltaY);
          const elapsedMs = now - touchStartEvent.startTime;
          const slidePitch = getSlidePitch();
          const isHorizontalDominant = absDeltaX > absDeltaY * HORIZONTAL_DOMINANCE_RATIO;
          const pastDistanceThreshold = absDeltaX > slidePitch * COMMIT_DISTANCE_RATIO;
          const isFastFlick = absDeltaX > FLICK_DISTANCE_PX && elapsedMs < FLICK_MAX_DURATION_MS;
          const shouldCommit = isHorizontalDominant && (pastDistanceThreshold || isFastFlick);
          const showsNextImage = deltaX < 0;
          const slideEls = touchDrag.slideEls;
          const reducedMotion = prefersReducedMotion();
          const committedOffset = showsNextImage ? -slidePitch : slidePitch;
          const endOffset = shouldCommit ? committedOffset : 0;
          const settle = () => {
            if (touchDrag.settle !== settle) {
              return;
            }
            touchDrag.settle = null;
            stopDragAnimations();
            setDragOffset(slideEls, 0);
            if (!shouldCommit) {
              return;
            }
            if (showsNextImage) {
              actions.showNextImage(event);
            } else {
              actions.showPreviousImage(event);
            }
            if (reducedMotion) {
              return;
            }
            const hold = slideEls.map(
              (el) => el.animate([{ opacity: 0 }, { opacity: 0 }], {
                fill: "forwards"
              })
            );
            touchDrag.animations = hold;
            const enlargedEl = slideEls[slideEls.length - 1];
            waitForImage(enlargedEl.querySelector("img")).then(
              () => {
                if (touchDrag.animations !== hold) {
                  return;
                }
                stopDragAnimations();
                touchDrag.animations = slideEls.map(
                  (el) => el.animate(
                    [
                      { opacity: 0 },
                      {
                        opacity: el === enlargedEl ? 1 : 0
                      }
                    ],
                    {
                      duration: FADE_DURATION_MS,
                      easing: "ease-out"
                    }
                  )
                );
              }
            );
          };
          touchDrag.settle = settle;
          if (reducedMotion) {
            settle();
          } else {
            touchDrag.animations = slideEls.map(
              (el) => el.animate(
                [
                  { transform: slideTransform(deltaX) },
                  {
                    transform: slideTransform(endOffset)
                  }
                ],
                {
                  duration: SLIDE_DURATION_MS,
                  easing: SLIDE_EASING,
                  // Holds the end position until `settle()` has
                  // moved the slides there itself.
                  fill: "forwards"
                }
              )
            );
            touchDrag.animations[0].onfinish = withScope(settle);
          }
        }
        lastTouchTime = now;
        isTouching = false;
        resetTouchDrag();
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
        const originalRatio = originalWidth / originalHeight;
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
        const imgMaxWidth = parseFloat(
          state.selectedImage.targetWidth && state.selectedImage.targetWidth !== "none" ? state.selectedImage.targetWidth : naturalWidth
        );
        const imgMaxHeight = parseFloat(
          state.selectedImage.targetHeight && state.selectedImage.targetHeight !== "none" ? state.selectedImage.targetHeight : naturalHeight
        );
        const fullSizeRatio = imgMaxWidth / imgMaxHeight;
        let containerWidth = imgMaxWidth;
        let containerHeight = imgMaxHeight;
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
        if (fullSizeRatio > targetContainerRatio) {
          containerWidth = targetMaxWidth;
          containerHeight = containerWidth / fullSizeRatio;
        } else {
          containerHeight = targetMaxHeight;
          containerWidth = containerHeight * fullSizeRatio;
        }
        const hasCroppedSource = naturalRatio.toFixed(2) !== fullSizeRatio.toFixed(2);
        const sourceRatio = hasCroppedSource ? naturalRatio : fullSizeRatio;
        const containerScale = Math.max(
          originalWidth / containerWidth,
          originalHeight / containerHeight,
          originalWidth / (containerHeight * sourceRatio),
          originalHeight * sourceRatio / containerWidth
        );
        const thumbnailWidth = originalWidth / containerScale;
        const thumbnailHeight = originalHeight / containerScale;
        const cropX = (containerWidth - thumbnailWidth) / 2;
        const cropY = (containerHeight - thumbnailHeight) / 2;
        screenPosX -= cropX * containerScale;
        screenPosY -= cropY * containerScale;
        state.overlayStyles = `
					--wp--lightbox-initial-top-position: ${screenPosY}px;
					--wp--lightbox-initial-left-position: ${screenPosX}px;
					--wp--lightbox-container-width: ${containerWidth + 1}px;
					--wp--lightbox-container-height: ${containerHeight + 1}px;
					--wp--lightbox-image-width: ${containerWidth}px;
					--wp--lightbox-image-height: ${containerHeight}px;
					--wp--lightbox-initial-clip: inset(${cropY}px ${cropX}px);
					--wp--lightbox-thumbnail-width: ${hasCroppedSource ? thumbnailWidth : containerWidth}px;
					--wp--lightbox-thumbnail-height: ${hasCroppedSource ? thumbnailHeight : containerHeight}px;
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
        if (!state.overlayEnabled) {
          inertElements.forEach(
            (el) => el.removeAttribute("inert")
          );
          inertElements = [];
          return;
        }
        const { ref } = getElement();
        let node = ref;
        while (node && node !== document.body && node.parentElement) {
          for (const sibling of node.parentElement.children) {
            if (sibling !== node && !sibling.hasAttribute("inert")) {
              sibling.setAttribute("inert", "");
              inertElements.push(sibling);
            }
          }
          node = node.parentElement;
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
