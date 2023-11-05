"use strict";
(self["__WordPressPrivateInteractivityAPI__"] = self["__WordPressPrivateInteractivityAPI__"] || []).push([[354],{

/***/ 699:
/***/ (function(__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) {

/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(754);
/**
 * WordPress dependencies
 */

const focusableSelectors = ['a[href]', 'area[href]', 'input:not([disabled]):not([type="hidden"]):not([aria-hidden])', 'select:not([disabled]):not([aria-hidden])', 'textarea:not([disabled]):not([aria-hidden])', 'button:not([disabled]):not([aria-hidden])', 'iframe', 'object', 'embed', '[contenteditable]', '[tabindex]:not([tabindex^="-"])'];

/*
 * Stores a context-bound scroll handler.
 *
 * This callback could be defined inline inside of the store
 * object but it's created externally to avoid confusion about
 * how its logic is called. This logic is not referenced directly
 * by the directives in the markup because the scroll event we
 * need to listen to is triggered on the window; so by defining it
 * outside of the store, we signal that the behavior here is different.
 * If we find a compelling reason to move it to the store, feel free.
 *
 * @type {Function}
 */
let scrollCallback;

/*
 * Tracks whether user is touching screen; used to
 * differentiate behavior for touch and mouse input.
 *
 * @type {boolean}
 */
let isTouching = false;

/*
 * Tracks the last time the screen was touched; used to
 * differentiate behavior for touch and mouse input.
 *
 * @type {number}
 */
let lastTouchTime = 0;

/*
 * Lightbox page-scroll handler: prevents scrolling.
 *
 * This handler is added to prevent scrolling behaviors that
 * trigger content shift while the lightbox is open.
 *
 * It would be better to accomplish this through CSS alone, but
 * using overflow: hidden is currently the only way to do so, and
 * that causes the layout to shift and prevents the zoom animation
 * from working in some cases because we're unable to account for
 * the layout shift when doing the animation calculations. Instead,
 * here we use JavaScript to prevent and reset the scrolling
 * behavior. In the future, we may be able to use CSS or overflow: hidden
 * instead to not rely on JavaScript, but this seems to be the best approach
 * for now that provides the best visual experience.
 *
 * @param {Object} context Interactivity page context?
 */
function handleScroll(context) {
  // We can't override the scroll behavior on mobile devices
  // because doing so breaks the pinch to zoom functionality, and we
  // want to allow users to zoom in further on the high-res image.
  if (!isTouching && Date.now() - lastTouchTime > 450) {
    // We are unable to use event.preventDefault() to prevent scrolling
    // because the scroll event can't be canceled, so we reset the position instead.
    window.scrollTo(context.core.image.scrollLeftReset, context.core.image.scrollTopReset);
  }
}
(0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__/* .store */ .h)({
  state: {
    core: {
      image: {
        windowWidth: window.innerWidth,
        windowHeight: window.innerHeight
      }
    }
  },
  actions: {
    core: {
      image: {
        showLightbox: ({
          context,
          event
        }) => {
          // We can't initialize the lightbox until the reference
          // image is loaded, otherwise the UX is broken.
          if (!context.core.image.imageLoaded) {
            return;
          }
          context.core.image.initialized = true;
          context.core.image.lastFocusedElement = window.document.activeElement;
          context.core.image.scrollDelta = 0;
          context.core.image.pointerType = event.pointerType;
          context.core.image.lightboxEnabled = true;
          setStyles(context, context.core.image.imageRef);
          context.core.image.scrollTopReset = window.pageYOffset || document.documentElement.scrollTop;

          // In most cases, this value will be 0, but this is included
          // in case a user has created a page with horizontal scrolling.
          context.core.image.scrollLeftReset = window.pageXOffset || document.documentElement.scrollLeft;

          // We define and bind the scroll callback here so
          // that we can pass the context and as an argument.
          // We may be able to change this in the future if we
          // define the scroll callback in the store instead, but
          // this approach seems to tbe clearest for now.
          scrollCallback = handleScroll.bind(null, context);

          // We need to add a scroll event listener to the window
          // here because we are unable to otherwise access it via
          // the Interactivity API directives. If we add a native way
          // to access the window, we can remove this.
          window.addEventListener('scroll', scrollCallback, false);
        },
        hideLightbox: async ({
          context
        }) => {
          context.core.image.hideAnimationEnabled = true;
          if (context.core.image.lightboxEnabled) {
            // We want to wait until the close animation is completed
            // before allowing a user to scroll again. The duration of this
            // animation is defined in the styles.scss and depends on if the
            // animation is 'zoom' or 'fade', but in any case we should wait
            // a few milliseconds longer than the duration, otherwise a user
            // may scroll too soon and cause the animation to look sloppy.
            setTimeout(function () {
              window.removeEventListener('scroll', scrollCallback);
              // If we don't delay before changing the focus,
              // the focus ring will appear on Firefox before
              // the image has finished animating, which looks broken.
              context.core.image.lightboxTriggerRef.focus({
                preventScroll: true
              });
            }, 450);
            context.core.image.lightboxEnabled = false;
          }
        },
        handleKeydown: ({
          context,
          actions,
          event
        }) => {
          if (context.core.image.lightboxEnabled) {
            if (event.key === 'Tab' || event.keyCode === 9) {
              // If shift + tab it change the direction
              if (event.shiftKey && window.document.activeElement === context.core.image.firstFocusableElement) {
                event.preventDefault();
                context.core.image.lastFocusableElement.focus();
              } else if (!event.shiftKey && window.document.activeElement === context.core.image.lastFocusableElement) {
                event.preventDefault();
                context.core.image.firstFocusableElement.focus();
              }
            }
            if (event.key === 'Escape' || event.keyCode === 27) {
              actions.core.image.hideLightbox({
                context,
                event
              });
            }
          }
        },
        // This is fired just by lazily loaded
        // images on the page, not all images.
        handleLoad: ({
          context,
          effects,
          ref
        }) => {
          context.core.image.imageLoaded = true;
          context.core.image.imageCurrentSrc = ref.currentSrc;
          effects.core.image.setButtonStyles({
            context,
            ref
          });
        },
        handleTouchStart: () => {
          isTouching = true;
        },
        handleTouchMove: ({
          context,
          event
        }) => {
          // On mobile devices, we want to prevent triggering the
          // scroll event because otherwise the page jumps around as
          // we reset the scroll position. This also means that closing
          // the lightbox requires that a user perform a simple tap. This
          // may be changed in the future if we find a better alternative
          // to override or reset the scroll position during swipe actions.
          if (context.core.image.lightboxEnabled) {
            event.preventDefault();
          }
        },
        handleTouchEnd: () => {
          // We need to wait a few milliseconds before resetting
          // to ensure that pinch to zoom works consistently
          // on mobile devices when the lightbox is open.
          lastTouchTime = Date.now();
          isTouching = false;
        }
      }
    }
  },
  selectors: {
    core: {
      image: {
        roleAttribute: ({
          context
        }) => {
          return context.core.image.lightboxEnabled ? 'dialog' : null;
        },
        ariaModal: ({
          context
        }) => {
          return context.core.image.lightboxEnabled ? 'true' : null;
        },
        dialogLabel: ({
          context
        }) => {
          return context.core.image.lightboxEnabled ? context.core.image.dialogLabel : null;
        },
        lightboxObjectFit: ({
          context
        }) => {
          if (context.core.image.initialized) {
            return 'cover';
          }
        },
        enlargedImgSrc: ({
          context
        }) => {
          return context.core.image.initialized ? context.core.image.imageUploadedSrc : 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';
        }
      }
    }
  },
  effects: {
    core: {
      image: {
        initOriginImage: ({
          context,
          ref
        }) => {
          context.core.image.imageRef = ref;
          context.core.image.lightboxTriggerRef = ref.parentElement.querySelector('.lightbox-trigger');
          if (ref.complete) {
            context.core.image.imageLoaded = true;
            context.core.image.imageCurrentSrc = ref.currentSrc;
          }
        },
        initLightbox: async ({
          context,
          ref
        }) => {
          if (context.core.image.lightboxEnabled) {
            const focusableElements = ref.querySelectorAll(focusableSelectors);
            context.core.image.firstFocusableElement = focusableElements[0];
            context.core.image.lastFocusableElement = focusableElements[focusableElements.length - 1];

            // Move focus to the dialog when opening it.
            ref.focus();
          }
        },
        setButtonStyles: ({
          context,
          ref
        }) => {
          const {
            naturalWidth,
            naturalHeight,
            offsetWidth,
            offsetHeight
          } = ref;

          // If the image isn't loaded yet, we can't
          // calculate where the button should be.
          if (naturalWidth === 0 || naturalHeight === 0) {
            return;
          }
          const figure = ref.parentElement;
          const figureWidth = ref.parentElement.clientWidth;

          // We need special handling for the height because
          // a caption will cause the figure to be taller than
          // the image, which means we need to account for that
          // when calculating the placement of the button in the
          // top right corner of the image.
          let figureHeight = ref.parentElement.clientHeight;
          const caption = figure.querySelector('figcaption');
          if (caption) {
            const captionComputedStyle = window.getComputedStyle(caption);
            figureHeight = figureHeight - caption.offsetHeight - parseFloat(captionComputedStyle.marginTop) - parseFloat(captionComputedStyle.marginBottom);
          }
          const buttonOffsetTop = figureHeight - offsetHeight;
          const buttonOffsetRight = figureWidth - offsetWidth;

          // In the case of an image with object-fit: contain, the
          // size of the <img> element can be larger than the image itself,
          // so we need to calculate where to place the button.
          if (context.core.image.scaleAttr === 'contain') {
            // Natural ratio of the image.
            const naturalRatio = naturalWidth / naturalHeight;
            // Offset ratio of the image.
            const offsetRatio = offsetWidth / offsetHeight;
            if (naturalRatio >= offsetRatio) {
              // If it reaches the width first, keep
              // the width and compute the height.
              const referenceHeight = offsetWidth / naturalRatio;
              context.core.image.imageButtonTop = (offsetHeight - referenceHeight) / 2 + buttonOffsetTop + 16;
              context.core.image.imageButtonRight = buttonOffsetRight + 16;
            } else {
              // If it reaches the height first, keep
              // the height and compute the width.
              const referenceWidth = offsetHeight * naturalRatio;
              context.core.image.imageButtonTop = buttonOffsetTop + 16;
              context.core.image.imageButtonRight = (offsetWidth - referenceWidth) / 2 + buttonOffsetRight + 16;
            }
          } else {
            context.core.image.imageButtonTop = buttonOffsetTop + 16;
            context.core.image.imageButtonRight = buttonOffsetRight + 16;
          }
        },
        setStylesOnResize: ({
          state,
          context,
          ref
        }) => {
          if (context.core.image.lightboxEnabled && (state.core.image.windowWidth || state.core.image.windowHeight)) {
            setStyles(context, ref);
          }
        }
      }
    }
  }
}, {
  afterLoad: ({
    state
  }) => {
    window.addEventListener('resize', debounce(() => {
      state.core.image.windowWidth = window.innerWidth;
      state.core.image.windowHeight = window.innerHeight;
    }));
  }
});

/*
 * Computes styles for the lightbox and adds them to the document.
 *
 * @function
 * @param {Object} context - An Interactivity API context
 * @param {Object} event - A triggering event
 */
function setStyles(context, ref) {
  // The reference img element lies adjacent
  // to the event target button in the DOM.
  let {
    naturalWidth,
    naturalHeight,
    offsetWidth: originalWidth,
    offsetHeight: originalHeight
  } = ref;
  let {
    x: screenPosX,
    y: screenPosY
  } = ref.getBoundingClientRect();

  // Natural ratio of the image clicked to open the lightbox.
  const naturalRatio = naturalWidth / naturalHeight;
  // Original ratio of the image clicked to open the lightbox.
  let originalRatio = originalWidth / originalHeight;

  // If it has object-fit: contain, recalculate the original sizes
  // and the screen position without the blank spaces.
  if (context.core.image.scaleAttr === 'contain') {
    if (naturalRatio > originalRatio) {
      const heightWithoutSpace = originalWidth / naturalRatio;
      // Recalculate screen position without the top space.
      screenPosY += (originalHeight - heightWithoutSpace) / 2;
      originalHeight = heightWithoutSpace;
    } else {
      const widthWithoutSpace = originalHeight * naturalRatio;
      // Recalculate screen position without the left space.
      screenPosX += (originalWidth - widthWithoutSpace) / 2;
      originalWidth = widthWithoutSpace;
    }
  }
  originalRatio = originalWidth / originalHeight;

  // Typically, we use the image's full-sized dimensions. If those
  // dimensions have not been set (i.e. an external image with only one size),
  // the image's dimensions in the lightbox are the same
  // as those of the image in the content.
  let imgMaxWidth = parseFloat(context.core.image.targetWidth !== 'none' ? context.core.image.targetWidth : naturalWidth);
  let imgMaxHeight = parseFloat(context.core.image.targetHeight !== 'none' ? context.core.image.targetHeight : naturalHeight);

  // Ratio of the biggest image stored in the database.
  let imgRatio = imgMaxWidth / imgMaxHeight;
  let containerMaxWidth = imgMaxWidth;
  let containerMaxHeight = imgMaxHeight;
  let containerWidth = imgMaxWidth;
  let containerHeight = imgMaxHeight;
  // Check if the target image has a different ratio than the original one (thumbnail).
  // Recalculate the width and height.
  if (naturalRatio.toFixed(2) !== imgRatio.toFixed(2)) {
    if (naturalRatio > imgRatio) {
      // If the width is reached before the height, we keep the maxWidth
      // and recalculate the height.
      // Unless the difference between the maxHeight and the reducedHeight
      // is higher than the maxWidth, where we keep the reducedHeight and
      // recalculate the width.
      const reducedHeight = imgMaxWidth / naturalRatio;
      if (imgMaxHeight - reducedHeight > imgMaxWidth) {
        imgMaxHeight = reducedHeight;
        imgMaxWidth = reducedHeight * naturalRatio;
      } else {
        imgMaxHeight = imgMaxWidth / naturalRatio;
      }
    } else {
      // If the height is reached before the width, we keep the maxHeight
      // and recalculate the width.
      // Unless the difference between the maxWidth and the reducedWidth
      // is higher than the maxHeight, where we keep the reducedWidth and
      // recalculate the height.
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

    // Calculate the max size of the container.
    if (originalRatio > imgRatio) {
      containerMaxWidth = imgMaxWidth;
      containerMaxHeight = containerMaxWidth / originalRatio;
    } else {
      containerMaxHeight = imgMaxHeight;
      containerMaxWidth = containerMaxHeight * originalRatio;
    }
  }

  // If the image has been pixelated on purpose, keep that size.
  if (originalWidth > containerWidth || originalHeight > containerHeight) {
    containerWidth = originalWidth;
    containerHeight = originalHeight;
  }

  // Calculate the final lightbox image size and the
  // scale factor. MaxWidth is either the window container
  // (accounting for padding) or the image resolution.
  let horizontalPadding = 0;
  if (window.innerWidth > 480) {
    horizontalPadding = 80;
  } else if (window.innerWidth > 1920) {
    horizontalPadding = 160;
  }
  const verticalPadding = 80;
  const targetMaxWidth = Math.min(window.innerWidth - horizontalPadding, containerWidth);
  const targetMaxHeight = Math.min(window.innerHeight - verticalPadding, containerHeight);
  const targetContainerRatio = targetMaxWidth / targetMaxHeight;
  if (originalRatio > targetContainerRatio) {
    // If targetMaxWidth is reached before targetMaxHeight
    containerWidth = targetMaxWidth;
    containerHeight = containerWidth / originalRatio;
  } else {
    // If targetMaxHeight is reached before targetMaxWidth
    containerHeight = targetMaxHeight;
    containerWidth = containerHeight * originalRatio;
  }
  const containerScale = originalWidth / containerWidth;
  const lightboxImgWidth = imgMaxWidth * (containerWidth / containerMaxWidth);
  const lightboxImgHeight = imgMaxHeight * (containerHeight / containerMaxHeight);

  // Add the CSS variables needed.
  let styleTag = document.getElementById('wp-lightbox-styles');
  if (!styleTag) {
    styleTag = document.createElement('style');
    styleTag.id = 'wp-lightbox-styles';
    document.head.appendChild(styleTag);
  }

  // As of this writing, using the calculations above will render the lightbox
  // with a small, erroneous whitespace on the left side of the image in iOS Safari,
  // perhaps due to an inconsistency in how browsers handle absolute positioning and CSS
  // transformation. In any case, adding 1 pixel to the container width and height solves
  // the problem, though this can be removed if the issue is fixed in the future.
  styleTag.innerHTML = `
		:root {
			--wp--lightbox-initial-top-position: ${screenPosY}px;
			--wp--lightbox-initial-left-position: ${screenPosX}px;
			--wp--lightbox-container-width: ${containerWidth + 1}px;
			--wp--lightbox-container-height: ${containerHeight + 1}px;
			--wp--lightbox-image-width: ${lightboxImgWidth}px;
			--wp--lightbox-image-height: ${lightboxImgHeight}px;
			--wp--lightbox-scale: ${containerScale};
		}
	`;
}

/*
 * Debounces a function call.
 *
 * @function
 * @param {Function} func - A function to be called
 * @param {number} wait - The time to wait before calling the function
 */
function debounce(func, wait = 50) {
  let timeout;
  return () => {
    const later = () => {
      timeout = null;
      func();
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/***/ })

},
/******/ function(__webpack_require__) { // webpackRuntimeModules
/******/ var __webpack_exec__ = function(moduleId) { return __webpack_require__(__webpack_require__.s = moduleId); }
/******/ var __webpack_exports__ = (__webpack_exec__(699));
/******/ }
]);