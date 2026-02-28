// packages/block-library/build-module/tabs/view.js
import {
  store,
  getContext,
  getElement,
  withSyncEvent
} from "@wordpress/interactivity";
function createReadOnlyProxy(obj) {
  return new Proxy(obj, {
    get(target, prop) {
      const value = target[prop];
      if (typeof value === "object" && value !== null) {
        return createReadOnlyProxy(value);
      }
      return value;
    },
    set() {
      return false;
    },
    deleteProperty() {
      return false;
    }
  });
}
var { actions: privateActions, state: privateState } = store(
  "core/tabs/private",
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
        const tabsList = privateState[tabsId];
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
        const { tabsList } = privateState;
        const tabIndex = tabsList.findIndex((t) => t.id === tabId);
        return tabIndex;
      },
      /**
       * Whether the tab panel or tab label is the active tab.
       *
       * @type {boolean}
       */
      get isActiveTab() {
        const { activeTabIndex } = getContext();
        const { tabIndex } = privateState;
        return activeTabIndex === tabIndex;
      },
      /**
       * The value of the tabindex attribute.
       *
       * @type {false|string}
       */
      get tabIndexAttribute() {
        return privateState.isActiveTab ? -1 : 0;
      }
    },
    actions: {
      /**
       * Handles the keydown events for the tab label and tabs controller.
       *
       * @param {KeyboardEvent} event The keydown event.
       */
      handleTabKeyDown: withSyncEvent((event) => {
        const { isVertical } = getContext();
        if (event.key === "Enter") {
          const { tabIndex } = privateState;
          if (tabIndex !== null) {
            privateActions.setActiveTab(tabIndex);
          }
        } else if (event.key === "ArrowRight" && !isVertical) {
          const { tabIndex } = privateState;
          if (tabIndex !== null) {
            privateActions.setActiveTab(tabIndex + 1);
          }
        } else if (event.key === "ArrowLeft" && !isVertical) {
          const { tabIndex } = privateState;
          if (tabIndex !== null) {
            privateActions.setActiveTab(tabIndex - 1);
          }
        } else if (event.key === "ArrowDown" && isVertical) {
          const { tabIndex } = privateState;
          if (tabIndex !== null) {
            privateActions.setActiveTab(tabIndex + 1);
          }
        } else if (event.key === "ArrowUp" && isVertical) {
          const { tabIndex } = privateState;
          if (tabIndex !== null) {
            privateActions.setActiveTab(tabIndex - 1);
          }
        }
      }),
      /**
       * Handles the click event for the tab label.
       *
       * @param {MouseEvent} event The click event.
       */
      handleTabClick: withSyncEvent((event) => {
        event.preventDefault();
        const { tabIndex } = privateState;
        if (tabIndex !== null) {
          privateActions.setActiveTab(tabIndex);
        }
      }),
      /**
       * Sets the active tab index (internal implementation).
       *
       * @param {number}  tabIndex    The index of the active tab.
       * @param {boolean} scrollToTab Whether to scroll to the tab element.
       */
      setActiveTab: (tabIndex, scrollToTab = false) => {
        const context = getContext();
        context.activeTabIndex = tabIndex;
        if (scrollToTab) {
          const tabId = privateState.tabsList[tabIndex].id;
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
        const { tabsList } = privateState;
        if (tabsList.length === 0) {
          return;
        }
        const { hash } = window.location;
        const tabId = hash.replace("#", "");
        const tabIndex = tabsList.findIndex((t) => t.id === tabId);
        if (tabIndex >= 0) {
          privateActions.setActiveTab(tabIndex, true);
        }
      }
    }
  },
  {
    lock: true
  }
);
store("core/tabs", {
  state: {
    /**
     * Gets a contextually aware list of tabs for the current tabs block.
     * Public API for third-party access.
     *
     * @type {Array}
     */
    get tabsList() {
      return createReadOnlyProxy(privateState.tabsList);
    },
    /**
     * Gets the index of the active tab element whether it
     * is a tab label or tab panel.
     * Public API for third-party access.
     *
     * @type {number|null}
     */
    get tabIndex() {
      return createReadOnlyProxy(privateState.tabIndex);
    },
    /**
     * Whether the tab panel or tab label is the active tab.
     * Public API for third-party access.
     *
     * @type {boolean}
     */
    get isActiveTab() {
      return createReadOnlyProxy(privateState.isActiveTab);
    }
  },
  actions: {
    /**
     * Sets the active tab index.
     * Public API for third-party programmatic tab activation.
     *
     * @param {number}  tabIndex    The index of the active tab.
     * @param {boolean} scrollToTab Whether to scroll to the tab element.
     */
    setActiveTab: (tabIndex, scrollToTab = false) => {
      privateActions.setActiveTab(tabIndex, scrollToTab);
    }
  }
});
//# sourceMappingURL=view.js.map
