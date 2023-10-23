"use strict";
(self["__WordPressPrivateInteractivityAPI__"] = self["__WordPressPrivateInteractivityAPI__"] || []).push([[3],{

/***/ 932:
/***/ (function(__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) {

/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(754);
/**
 * WordPress dependencies
 */

const focusableSelectors = ['a[href]', 'input:not([disabled]):not([type="hidden"]):not([aria-hidden])', 'select:not([disabled]):not([aria-hidden])', 'textarea:not([disabled]):not([aria-hidden])', 'button:not([disabled]):not([aria-hidden])', '[contenteditable]', '[tabindex]:not([tabindex^="-"])'];

// This is a fix for Safari in iOS/iPadOS. Without it, Safari doesn't focus out
// when the user taps in the body. It can be removed once we add an overlay to
// capture the clicks, instead of relying on the focusout event.
document.addEventListener('click', () => {});
const openMenu = (store, menuOpenedOn) => {
  const {
    context,
    selectors
  } = store;
  selectors.core.navigation.menuOpenedBy(store)[menuOpenedOn] = true;
  if (context.core.navigation.type === 'overlay') {
    // Add a `has-modal-open` class to the <html> root.
    document.documentElement.classList.add('has-modal-open');
  }
};
const closeMenu = (store, menuClosedOn) => {
  const {
    context,
    selectors
  } = store;
  selectors.core.navigation.menuOpenedBy(store)[menuClosedOn] = false;
  // Check if the menu is still open or not.
  if (!selectors.core.navigation.isMenuOpen(store)) {
    if (context.core.navigation.modal?.contains(window.document.activeElement)) {
      context.core.navigation.previousFocus?.focus();
    }
    context.core.navigation.modal = null;
    context.core.navigation.previousFocus = null;
    if (context.core.navigation.type === 'overlay') {
      document.documentElement.classList.remove('has-modal-open');
    }
  }
};
(0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__/* .store */ .h)({
  effects: {
    core: {
      navigation: {
        initMenu: store => {
          const {
            context,
            selectors,
            ref
          } = store;
          if (selectors.core.navigation.isMenuOpen(store)) {
            const focusableElements = ref.querySelectorAll(focusableSelectors);
            context.core.navigation.modal = ref;
            context.core.navigation.firstFocusableElement = focusableElements[0];
            context.core.navigation.lastFocusableElement = focusableElements[focusableElements.length - 1];
          }
        },
        focusFirstElement: store => {
          const {
            selectors,
            ref
          } = store;
          if (selectors.core.navigation.isMenuOpen(store)) {
            ref.querySelector('.wp-block-navigation-item > *:first-child').focus();
          }
        }
      }
    }
  },
  selectors: {
    core: {
      navigation: {
        roleAttribute: store => {
          const {
            context,
            selectors
          } = store;
          return context.core.navigation.type === 'overlay' && selectors.core.navigation.isMenuOpen(store) ? 'dialog' : null;
        },
        ariaModal: store => {
          const {
            context,
            selectors
          } = store;
          return context.core.navigation.type === 'overlay' && selectors.core.navigation.isMenuOpen(store) ? 'true' : null;
        },
        ariaLabel: store => {
          const {
            context,
            selectors
          } = store;
          return context.core.navigation.type === 'overlay' && selectors.core.navigation.isMenuOpen(store) ? context.core.navigation.ariaLabel : null;
        },
        isMenuOpen: ({
          context
        }) =>
        // The menu is opened if either `click`, `hover` or `focus` is true.
        Object.values(context.core.navigation[context.core.navigation.type === 'overlay' ? 'overlayOpenedBy' : 'submenuOpenedBy']).filter(Boolean).length > 0,
        menuOpenedBy: ({
          context
        }) => context.core.navigation[context.core.navigation.type === 'overlay' ? 'overlayOpenedBy' : 'submenuOpenedBy']
      }
    }
  },
  actions: {
    core: {
      navigation: {
        openMenuOnHover(store) {
          const {
            navigation
          } = store.context.core;
          if (navigation.type === 'submenu' &&
          // Only open on hover if the overlay is closed.
          Object.values(navigation.overlayOpenedBy || {}).filter(Boolean).length === 0) openMenu(store, 'hover');
        },
        closeMenuOnHover(store) {
          closeMenu(store, 'hover');
        },
        openMenuOnClick(store) {
          const {
            context,
            ref
          } = store;
          context.core.navigation.previousFocus = ref;
          openMenu(store, 'click');
        },
        closeMenuOnClick(store) {
          closeMenu(store, 'click');
          closeMenu(store, 'focus');
        },
        openMenuOnFocus(store) {
          openMenu(store, 'focus');
        },
        toggleMenuOnClick: store => {
          const {
            selectors,
            context,
            ref
          } = store;
          // Safari won't send focus to the clicked element, so we need to manually place it: https://bugs.webkit.org/show_bug.cgi?id=22261
          if (window.document.activeElement !== ref) ref.focus();
          const menuOpenedBy = selectors.core.navigation.menuOpenedBy(store);
          if (menuOpenedBy.click || menuOpenedBy.focus) {
            closeMenu(store, 'click');
            closeMenu(store, 'focus');
          } else {
            context.core.navigation.previousFocus = ref;
            openMenu(store, 'click');
          }
        },
        handleMenuKeydown: store => {
          const {
            context,
            selectors,
            event
          } = store;
          if (selectors.core.navigation.menuOpenedBy(store).click) {
            // If Escape close the menu.
            if (event?.key === 'Escape') {
              closeMenu(store, 'click');
              closeMenu(store, 'focus');
              return;
            }

            // Trap focus if it is an overlay (main menu).
            if (context.core.navigation.type === 'overlay' && event.key === 'Tab') {
              // If shift + tab it change the direction.
              if (event.shiftKey && window.document.activeElement === context.core.navigation.firstFocusableElement) {
                event.preventDefault();
                context.core.navigation.lastFocusableElement.focus();
              } else if (!event.shiftKey && window.document.activeElement === context.core.navigation.lastFocusableElement) {
                event.preventDefault();
                context.core.navigation.firstFocusableElement.focus();
              }
            }
          }
        },
        handleMenuFocusout: store => {
          const {
            context,
            event
          } = store;
          // If focus is outside modal, and in the document, close menu
          // event.target === The element losing focus
          // event.relatedTarget === The element receiving focus (if any)
          // When focusout is outsite the document,
          // `window.document.activeElement` doesn't change.

          // The event.relatedTarget is null when something outside the navigation menu is clicked. This is only necessary for Safari.
          if (event.relatedTarget === null || !context.core.navigation.modal?.contains(event.relatedTarget) && event.target !== window.document.activeElement) {
            closeMenu(store, 'click');
            closeMenu(store, 'focus');
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
/******/ var __webpack_exports__ = (__webpack_exec__(932));
/******/ }
]);