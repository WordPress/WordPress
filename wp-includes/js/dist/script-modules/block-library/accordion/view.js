// packages/block-library/build-module/accordion/view.mjs
import { store, getContext } from "@wordpress/interactivity";
var hashHandled = false;
var { actions } = store(
  "core/accordion",
  {
    state: {
      get isOpen() {
        const { id, accordionItems } = getContext();
        const accordionItem = accordionItems.find(
          (item) => item.id === id
        );
        return accordionItem ? accordionItem.isOpen : false;
      },
      get isHidden() {
        const { id, accordionItems } = getContext();
        const accordionItem = accordionItems.find(
          (item) => item.id === id
        );
        return accordionItem?.isOpen ? null : "until-found";
      }
    },
    actions: {
      toggle: () => {
        const context = getContext();
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
      openPanelByHash: () => {
        if (hashHandled || !window.location?.hash?.length) {
          return;
        }
        const context = getContext();
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
      },
      handleBeforeMatch: () => {
        const context = getContext();
        const { id, autoclose, accordionItems } = context;
        const accordionItem = accordionItems.find(
          (item) => item.id === id
        );
        if (accordionItem) {
          if (autoclose) {
            accordionItems.forEach((item) => {
              item.isOpen = item.id === id;
            });
          } else {
            accordionItem.isOpen = true;
          }
        }
      }
    },
    callbacks: {
      initAccordionItems: () => {
        const context = getContext();
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
