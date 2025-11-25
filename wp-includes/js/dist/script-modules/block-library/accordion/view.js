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
const interactivity_namespaceObject = x({ ["getContext"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getContext), ["store"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store), ["withSyncEvent"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.withSyncEvent) });
;// ./node_modules/@wordpress/block-library/build-module/accordion/view.js

let hashHandled = false;
const { actions } = (0,interactivity_namespaceObject.store)(
  "core/accordion",
  {
    state: {
      get isOpen() {
        const { id, accordionItems } = (0,interactivity_namespaceObject.getContext)();
        const accordionItem = accordionItems.find(
          (item) => item.id === id
        );
        return accordionItem ? accordionItem.isOpen : false;
      }
    },
    actions: {
      toggle: () => {
        const context = (0,interactivity_namespaceObject.getContext)();
        const { id, autoclose, accordionItems } = context;
        const accordionItem = accordionItems.find(
          (item) => item.id === id
        );
        if (autoclose) {
          accordionItems.forEach((item) => {
            item.isOpen = item.id === id ? !accordionItem.isOpen : false;
          });
        } else {
          accordionItem.isOpen = !accordionItem.isOpen;
        }
      },
      handleKeyDown: (0,interactivity_namespaceObject.withSyncEvent)((event) => {
        if (event.key !== "ArrowUp" && event.key !== "ArrowDown" && event.key !== "Home" && event.key !== "End") {
          return;
        }
        event.preventDefault();
        const context = (0,interactivity_namespaceObject.getContext)();
        const { id, accordionItems } = context;
        const currentIndex = accordionItems.findIndex(
          (item) => item.id === id
        );
        let nextIndex;
        switch (event.key) {
          case "ArrowUp":
            nextIndex = Math.max(0, currentIndex - 1);
            break;
          case "ArrowDown":
            nextIndex = Math.min(
              currentIndex + 1,
              accordionItems.length - 1
            );
            break;
          case "Home":
            nextIndex = 0;
            break;
          case "End":
            nextIndex = accordionItems.length - 1;
            break;
        }
        const nextId = accordionItems[nextIndex].id;
        const nextButton = document.getElementById(nextId);
        if (nextButton) {
          nextButton.focus();
        }
      }),
      openPanelByHash: () => {
        if (hashHandled || !window.location?.hash?.length) {
          return;
        }
        const context = (0,interactivity_namespaceObject.getContext)();
        const { id, accordionItems, autoclose } = context;
        const hash = decodeURIComponent(
          window.location.hash.slice(1)
        );
        const targetElement = window.document.getElementById(hash);
        if (!targetElement) {
          return;
        }
        const panelElement = window.document.querySelector(
          '.wp-block-accordion-panel[aria-labelledby="' + id + '"]'
        );
        if (!panelElement || !panelElement.contains(targetElement)) {
          return;
        }
        hashHandled = true;
        if (autoclose) {
          accordionItems.forEach((item) => {
            item.isOpen = item.id === id;
          });
        } else {
          const targetItem = accordionItems.find(
            (item) => item.id === id
          );
          if (targetItem) {
            targetItem.isOpen = true;
          }
        }
        window.setTimeout(() => {
          targetElement.scrollIntoView();
        }, 0);
      }
    },
    callbacks: {
      initAccordionItems: () => {
        const context = (0,interactivity_namespaceObject.getContext)();
        const { id, openByDefault, accordionItems } = context;
        accordionItems.push({
          id,
          isOpen: openByDefault
        });
        actions.openPanelByHash();
      },
      hashChange: () => {
        hashHandled = false;
        actions.openPanelByHash();
      }
    }
  },
  { lock: true }
);

