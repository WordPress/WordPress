"use strict";
(self["__WordPressPrivateInteractivityAPI__"] = self["__WordPressPrivateInteractivityAPI__"] || []).push([[222],{

/***/ 534:
/***/ (function(__unused_webpack_module, __unused_webpack___webpack_exports__, __webpack_require__) {

/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(754);
/**
 * WordPress dependencies
 */

(0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__/* .store */ .h)({
  selectors: {
    core: {
      search: {
        ariaLabel: ({
          context
        }) => {
          const {
            ariaLabelCollapsed,
            ariaLabelExpanded
          } = context.core.search;
          return context.core.search.isSearchInputVisible ? ariaLabelExpanded : ariaLabelCollapsed;
        },
        ariaControls: ({
          context
        }) => {
          return context.core.search.isSearchInputVisible ? null : context.core.search.inputId;
        },
        type: ({
          context
        }) => {
          return context.core.search.isSearchInputVisible ? 'submit' : 'button';
        },
        tabindex: ({
          context
        }) => {
          return context.core.search.isSearchInputVisible ? '0' : '-1';
        }
      }
    }
  },
  actions: {
    core: {
      search: {
        openSearchInput: ({
          context,
          event,
          ref
        }) => {
          if (!context.core.search.isSearchInputVisible) {
            event.preventDefault();
            context.core.search.isSearchInputVisible = true;
            ref.parentElement.querySelector('input').focus();
          }
        },
        closeSearchInput: ({
          context
        }) => {
          context.core.search.isSearchInputVisible = false;
        },
        handleSearchKeydown: store => {
          const {
            actions,
            event,
            ref
          } = store;
          // If Escape close the menu.
          if (event?.key === 'Escape') {
            actions.core.search.closeSearchInput(store);
            ref.querySelector('button').focus();
          }
        },
        handleSearchFocusout: store => {
          const {
            actions,
            event,
            ref
          } = store;
          // If focus is outside search form, and in the document, close menu
          // event.target === The element losing focus
          // event.relatedTarget === The element receiving focus (if any)
          // When focusout is outside the document,
          // `window.document.activeElement` doesn't change.
          if (!ref.contains(event.relatedTarget) && event.target !== window.document.activeElement) {
            actions.core.search.closeSearchInput(store);
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
/******/ var __webpack_exports__ = (__webpack_exec__(534));
/******/ }
]);