import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ // The require scope
/******/ var __webpack_require__ = {};
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};

;// external "@wordpress/interactivity"
var x = (y) => {
	var x = {}; __webpack_require__.d(x, y); return x
} 
var y = (x) => (() => (x))
const interactivity_namespaceObject = x({ ["getContext"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getContext), ["getElement"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getElement), ["store"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store), ["withSyncEvent"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.withSyncEvent) });
;// ./node_modules/@wordpress/block-library/build-module/navigation/view.js

const focusableSelectors = [
  "a[href]",
  'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',
  "select:not([disabled]):not([aria-hidden])",
  "textarea:not([disabled]):not([aria-hidden])",
  "button:not([disabled]):not([aria-hidden])",
  "[contenteditable]",
  '[tabindex]:not([tabindex^="-"])'
];
document.addEventListener("click", () => {
});
const { state, actions } = (0,interactivity_namespaceObject.store)(
  "core/navigation",
  {
    state: {
      get roleAttribute() {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        return ctx.type === "overlay" && state.isMenuOpen ? "dialog" : null;
      },
      get ariaModal() {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        return ctx.type === "overlay" && state.isMenuOpen ? "true" : null;
      },
      get ariaLabel() {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        return ctx.type === "overlay" && state.isMenuOpen ? ctx.ariaLabel : null;
      },
      get isMenuOpen() {
        return Object.values(state.menuOpenedBy).filter(Boolean).length > 0;
      },
      get menuOpenedBy() {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        return ctx.type === "overlay" ? ctx.overlayOpenedBy : ctx.submenuOpenedBy;
      }
    },
    actions: {
      openMenuOnHover() {
        const { type, overlayOpenedBy } = (0,interactivity_namespaceObject.getContext)();
        if (type === "submenu" && // Only open on hover if the overlay is closed.
        Object.values(overlayOpenedBy || {}).filter(Boolean).length === 0) {
          actions.openMenu("hover");
        }
      },
      closeMenuOnHover() {
        const { type, overlayOpenedBy } = (0,interactivity_namespaceObject.getContext)();
        if (type === "submenu" && // Only close on hover if the overlay is closed.
        Object.values(overlayOpenedBy || {}).filter(Boolean).length === 0) {
          actions.closeMenu("hover");
        }
      },
      openMenuOnClick() {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        const { ref } = (0,interactivity_namespaceObject.getElement)();
        ctx.previousFocus = ref;
        actions.openMenu("click");
      },
      closeMenuOnClick() {
        actions.closeMenu("click");
        actions.closeMenu("focus");
      },
      openMenuOnFocus() {
        actions.openMenu("focus");
      },
      toggleMenuOnClick() {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        const { ref } = (0,interactivity_namespaceObject.getElement)();
        if (window.document.activeElement !== ref) {
          ref.focus();
        }
        const { menuOpenedBy } = state;
        if (menuOpenedBy.click || menuOpenedBy.focus) {
          actions.closeMenu("click");
          actions.closeMenu("focus");
        } else {
          ctx.previousFocus = ref;
          actions.openMenu("click");
        }
      },
      handleMenuKeydown: (0,interactivity_namespaceObject.withSyncEvent)((event) => {
        const { type, firstFocusableElement, lastFocusableElement } = (0,interactivity_namespaceObject.getContext)();
        if (state.menuOpenedBy.click) {
          if (event.key === "Escape") {
            event.stopPropagation();
            actions.closeMenu("click");
            actions.closeMenu("focus");
            return;
          }
          if (type === "overlay" && event.key === "Tab") {
            if (event.shiftKey && window.document.activeElement === firstFocusableElement) {
              event.preventDefault();
              lastFocusableElement.focus();
            } else if (!event.shiftKey && window.document.activeElement === lastFocusableElement) {
              event.preventDefault();
              firstFocusableElement.focus();
            }
          }
        }
      }),
      handleMenuFocusout: (0,interactivity_namespaceObject.withSyncEvent)((event) => {
        const { modal, type } = (0,interactivity_namespaceObject.getContext)();
        if (event.relatedTarget === null || !modal?.contains(event.relatedTarget) && event.target !== window.document.activeElement && type === "submenu") {
          actions.closeMenu("click");
          actions.closeMenu("focus");
        }
      }),
      openMenu(menuOpenedOn = "click") {
        const { type } = (0,interactivity_namespaceObject.getContext)();
        state.menuOpenedBy[menuOpenedOn] = true;
        if (type === "overlay") {
          document.documentElement.classList.add("has-modal-open");
        }
      },
      closeMenu(menuClosedOn = "click") {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        state.menuOpenedBy[menuClosedOn] = false;
        if (!state.isMenuOpen) {
          if (ctx.modal?.contains(window.document.activeElement)) {
            ctx.previousFocus?.focus();
          }
          ctx.modal = null;
          ctx.previousFocus = null;
          if (ctx.type === "overlay") {
            document.documentElement.classList.remove(
              "has-modal-open"
            );
          }
        }
      }
    },
    callbacks: {
      initMenu() {
        const ctx = (0,interactivity_namespaceObject.getContext)();
        const { ref } = (0,interactivity_namespaceObject.getElement)();
        if (state.isMenuOpen) {
          const focusableElements = ref.querySelectorAll(focusableSelectors);
          ctx.modal = ref;
          ctx.firstFocusableElement = focusableElements[0];
          ctx.lastFocusableElement = focusableElements[focusableElements.length - 1];
        }
      },
      focusFirstElement() {
        const { ref } = (0,interactivity_namespaceObject.getElement)();
        if (state.isMenuOpen) {
          const focusableElements = ref.querySelectorAll(focusableSelectors);
          focusableElements?.[0]?.focus();
        }
      }
    }
  },
  { lock: true }
);

