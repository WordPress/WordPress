"use strict";
(self["__WordPressPrivateInteractivityAPI__"] = self["__WordPressPrivateInteractivityAPI__"] || []).push([[155],{

/***/ 890:
/***/ (function(__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) {

/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(754);
/**
 * WordPress dependencies
 */

const isValidLink = ref => ref && ref instanceof window.HTMLAnchorElement && ref.href && (!ref.target || ref.target === '_self') && ref.origin === window.location.origin;
const isValidEvent = event => event.button === 0 &&
// left clicks only
!event.metaKey &&
// open in new tab (mac)
!event.ctrlKey &&
// open in new tab (windows)
!event.altKey &&
// download
!event.shiftKey && !event.defaultPrevented;
(0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__/* .store */ .h)({
  selectors: {
    core: {
      query: {
        startAnimation: ({
          context
        }) => context.core.query.animation === 'start',
        finishAnimation: ({
          context
        }) => context.core.query.animation === 'finish'
      }
    }
  },
  actions: {
    core: {
      query: {
        navigate: async ({
          event,
          ref,
          context
        }) => {
          const isDisabled = ref.closest('[data-wp-navigation-id]')?.dataset.wpNavigationDisabled;
          if (isValidLink(ref) && isValidEvent(event) && !isDisabled) {
            event.preventDefault();
            const id = ref.closest('[data-wp-navigation-id]').dataset.wpNavigationId;

            // Don't announce the navigation immediately, wait 300 ms.
            const timeout = setTimeout(() => {
              context.core.query.message = context.core.query.loadingText;
              context.core.query.animation = 'start';
            }, 400);
            await (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__/* .navigate */ .c4)(ref.href);

            // Dismiss loading message if it hasn't been added yet.
            clearTimeout(timeout);

            // Announce that the page has been loaded. If the message is the
            // same, we use a no-break space similar to the @wordpress/a11y
            // package: https://github.com/WordPress/gutenberg/blob/c395242b8e6ee20f8b06c199e4fc2920d7018af1/packages/a11y/src/filter-message.js#L20-L26
            context.core.query.message = context.core.query.loadedText + (context.core.query.message === context.core.query.loadedText ? '\u00A0' : '');
            context.core.query.animation = 'finish';
            context.core.query.url = ref.href;

            // Focus the first anchor of the Query block.
            const firstAnchor = `[data-wp-navigation-id=${id}] .wp-block-post-template a[href]`;
            document.querySelector(firstAnchor)?.focus();
          }
        },
        prefetch: async ({
          ref
        }) => {
          const isDisabled = ref.closest('[data-wp-navigation-id]')?.dataset.wpNavigationDisabled;
          if (isValidLink(ref) && !isDisabled) {
            await (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__/* .prefetch */ .tL)(ref.href);
          }
        }
      }
    }
  },
  effects: {
    core: {
      query: {
        prefetch: async ({
          ref,
          context
        }) => {
          if (context.core.query.url && isValidLink(ref)) {
            await (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__/* .prefetch */ .tL)(ref.href);
          }
        }
      }
    }
  }
});

/***/ })

},
/******/ function(__webpack_require__) { // webpackRuntimeModules
/******/ var __webpack_exec__ = function(moduleId) { return __webpack_require__(__webpack_require__.s = moduleId); }
/******/ var __webpack_exports__ = (__webpack_exec__(890));
/******/ }
]);