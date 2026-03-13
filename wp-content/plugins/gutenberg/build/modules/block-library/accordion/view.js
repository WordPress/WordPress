// packages/block-library/build-module/accordion/view.js
import { store, getContext, withSyncEvent } from "@wordpress/interactivity";
store(
  "core/accordion",
  {
    state: {
      get isOpen() {
        const { id, accordionItems } = getContext();
        const accordionItem = accordionItems.find(
          (item) => item.id === id
        );
        return accordionItem ? accordionItem.isOpen : false;
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
      handleKeyDown: withSyncEvent((event) => {
        if (event.key !== "ArrowUp" && event.key !== "ArrowDown" && event.key !== "Home" && event.key !== "End") {
          return;
        }
        event.preventDefault();
        const context = getContext();
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
      })
    },
    callbacks: {
      initAccordionItems: () => {
        const context = getContext();
        const { id, openByDefault } = context;
        context.accordionItems.push({
          id,
          isOpen: openByDefault
        });
      }
    }
  },
  { lock: true }
);
//# sourceMappingURL=view.js.map
