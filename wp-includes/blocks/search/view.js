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

;// CONCATENATED MODULE: external "@wordpress/interactivity"
var x = (y) => {
	var x = {}; __webpack_require__.d(x, y); return x
} 
var y = (x) => (() => (x))
const interactivity_namespaceObject = x({ ["getContext"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getContext), ["getElement"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getElement), ["store"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store) });
;// CONCATENATED MODULE: ./node_modules/@wordpress/block-library/build-module/search/view.js
/**
 * WordPress dependencies
 */

const {
  actions
} = (0,interactivity_namespaceObject.store)('core/search', {
  state: {
    get ariaLabel() {
      const {
        isSearchInputVisible,
        ariaLabelCollapsed,
        ariaLabelExpanded
      } = (0,interactivity_namespaceObject.getContext)();
      return isSearchInputVisible ? ariaLabelExpanded : ariaLabelCollapsed;
    },
    get ariaControls() {
      const {
        isSearchInputVisible,
        inputId
      } = (0,interactivity_namespaceObject.getContext)();
      return isSearchInputVisible ? null : inputId;
    },
    get type() {
      const {
        isSearchInputVisible
      } = (0,interactivity_namespaceObject.getContext)();
      return isSearchInputVisible ? 'submit' : 'button';
    },
    get tabindex() {
      const {
        isSearchInputVisible
      } = (0,interactivity_namespaceObject.getContext)();
      return isSearchInputVisible ? '0' : '-1';
    }
  },
  actions: {
    openSearchInput(event) {
      const ctx = (0,interactivity_namespaceObject.getContext)();
      const {
        ref
      } = (0,interactivity_namespaceObject.getElement)();
      if (!ctx.isSearchInputVisible) {
        event.preventDefault();
        ctx.isSearchInputVisible = true;
        ref.parentElement.querySelector('input').focus();
      }
    },
    closeSearchInput() {
      const ctx = (0,interactivity_namespaceObject.getContext)();
      ctx.isSearchInputVisible = false;
    },
    handleSearchKeydown(event) {
      const {
        ref
      } = (0,interactivity_namespaceObject.getElement)();
      // If Escape close the menu.
      if (event?.key === 'Escape') {
        actions.closeSearchInput();
        ref.querySelector('button').focus();
      }
    },
    handleSearchFocusout(event) {
      const {
        ref
      } = (0,interactivity_namespaceObject.getElement)();
      // If focus is outside search form, and in the document, close menu
      // event.target === The element losing focus
      // event.relatedTarget === The element receiving focus (if any)
      // When focusout is outside the document,
      // `window.document.activeElement` doesn't change.
      if (!ref.contains(event.relatedTarget) && event.target !== window.document.activeElement) {
        actions.closeSearchInput();
      }
    }
  }
}, {
  lock: true
});

