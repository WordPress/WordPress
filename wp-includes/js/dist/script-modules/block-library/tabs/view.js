// packages/block-library/build-module/tabs/view.mjs
import {
  store,
  getContext,
  getElement,
  withSyncEvent
} from "@wordpress/interactivity";
var { actions, state } = store(
  "core/tabs",
  {
    state: {
      /**
       * Gets a contextually aware list of tabs for the current tabs block.
       *
       * @type {Array}
       */
      get tabsList() {
        const context = getContext();
        const tabsId = context?.tabsId;
        const tabsList = state[tabsId];
        return tabsList;
      },
      /**
       * Gets the index of the active tab element whether it
       * is a tab label or tab panel.
       *
       * @type {number|null}
       */
      get tabIndex() {
        const { attributes } = getElement();
        const tabId = attributes?.id?.replace("tab__", "") || null;
        if (!tabId) {
          return null;
        }
        const { tabsList } = state;
        const tabIndex = tabsList.findIndex((t) => t === tabId);
        return tabIndex;
      },
      /**
       * Whether the tab panel or tab label is the active tab.
       *
       * @type {boolean}
       */
      get isActiveTab() {
        const { activeTabIndex } = getContext();
        const { tabIndex } = state;
        return activeTabIndex === tabIndex;
      },
      /**
       * The value of the tabindex attribute for tab buttons.
       * Only the active tab should be in the tab sequence.
       *
       * @type {number}
       */
      get tabIndexAttribute() {
        return state.isActiveTab ? 0 : -1;
      }
    },
    actions: {
      /**
       * Handles the keydown events for the tab label and tabs controller.
       *
       * @param {KeyboardEvent} event The keydown event.
       */
      handleTabKeyDown: withSyncEvent((event) => {
        const { tabIndex } = state;
        if (tabIndex === null) {
          return;
        }
        if (event.key === "ArrowRight") {
          event.preventDefault();
          actions.moveFocus(tabIndex + 1);
        } else if (event.key === "ArrowLeft") {
          event.preventDefault();
          actions.moveFocus(tabIndex - 1);
        }
      }),
      /**
       * Handles the click event for the tab label.
       *
       * @param {MouseEvent} event The click event.
       */
      handleTabClick: withSyncEvent((event) => {
        event.preventDefault();
        const { tabIndex } = state;
        if (tabIndex !== null) {
          actions.setActiveTab(tabIndex);
        }
      }),
      /**
       * Moves focus to a specific tab without activating it.
       *
       * @param {number} tabIndex The index to move focus to.
       */
      moveFocus: (tabIndex) => {
        const { tabsList } = state;
        if (!tabsList || tabsList.length === 0) {
          return;
        }
        let newIndex = tabIndex;
        if (newIndex < 0) {
          newIndex = tabsList.length - 1;
        } else if (newIndex >= tabsList.length) {
          newIndex = 0;
        }
        const tabId = tabsList[newIndex];
        const tabElement = document.getElementById("tab__" + tabId);
        if (tabElement) {
          tabElement.focus();
        }
      },
      /**
       * Sets the active tab index.
       *
       * @param {number}  tabIndex    The index of the active tab.
       * @param {boolean} scrollToTab Whether to scroll to the tab element.
       */
      setActiveTab: (tabIndex, scrollToTab = false) => {
        const { tabsList } = state;
        if (!tabsList || tabsList.length === 0) {
          return;
        }
        let newIndex = tabIndex;
        if (newIndex < 0) {
          newIndex = 0;
        } else if (newIndex >= tabsList.length) {
          newIndex = tabsList.length - 1;
        }
        const context = getContext();
        context.activeTabIndex = newIndex;
        if (scrollToTab) {
          const tabId = tabsList[newIndex];
          const tabElement = document.getElementById(tabId);
          if (tabElement) {
            setTimeout(() => {
              tabElement.scrollIntoView({ behavior: "smooth" });
            }, 100);
          }
        }
      }
    },
    callbacks: {
      /**
       * When the tabs are initialized, we need to check if there is a hash in the url and if so if it exists in the current tabsList, set the active tab to that index.
       *
       */
      onTabsInit: () => {
        const { tabsList } = state;
        if (tabsList.length === 0) {
          return;
        }
        const { hash } = window.location;
        const tabId = hash.replace("#", "");
        const tabIndex = tabsList.findIndex((t) => t === tabId);
        if (tabIndex >= 0) {
          actions.setActiveTab(tabIndex, true);
        }
      }
    }
  },
  {
    lock: true
  }
);
