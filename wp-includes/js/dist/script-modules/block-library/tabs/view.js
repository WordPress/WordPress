// packages/block-library/build-module/tabs/view.mjs
import {
  store,
  getContext,
  getElement,
  withSyncEvent
} from "@wordpress/interactivity";
function createReadOnlyProxy(obj) {
  const arrayMutationMethods = /* @__PURE__ */ new Set([
    "push",
    "pop",
    "shift",
    "unshift",
    "splice",
    "sort",
    "reverse",
    "copyWithin",
    "fill"
  ]);
  return new Proxy(obj, {
    get(target, prop) {
      if (Array.isArray(target) && arrayMutationMethods.has(prop)) {
        return () => {
        };
      }
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
       * The value of the tabindex attribute for tab buttons.
       * Only the active tab should be in the tab sequence.
       *
       * @type {number}
       */
      get tabIndexAttribute() {
        return privateState.isActiveTab ? 0 : -1;
      }
    },
    actions: {
      /**
       * Handles the keydown events for the tab label and tabs controller.
       *
       * @param {KeyboardEvent} event The keydown event.
       */
      handleTabKeyDown: withSyncEvent((event) => {
        const context = getContext();
        const { isVertical } = context;
        const { tabIndex } = privateState;
        if (tabIndex === null) {
          return;
        }
        if (event.key === "ArrowRight" && !isVertical) {
          event.preventDefault();
          privateActions.moveFocus(tabIndex + 1);
        } else if (event.key === "ArrowLeft" && !isVertical) {
          event.preventDefault();
          privateActions.moveFocus(tabIndex - 1);
        } else if (event.key === "ArrowDown" && isVertical) {
          event.preventDefault();
          privateActions.moveFocus(tabIndex + 1);
        } else if (event.key === "ArrowUp" && isVertical) {
          event.preventDefault();
          privateActions.moveFocus(tabIndex - 1);
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
       * Moves focus to a specific tab without activating it.
       *
       * @param {number} tabIndex The index to move focus to.
       */
      moveFocus: (tabIndex) => {
        const { tabsList } = privateState;
        if (!tabsList || tabsList.length === 0) {
          return;
        }
        let newIndex = tabIndex;
        if (newIndex < 0) {
          newIndex = tabsList.length - 1;
        } else if (newIndex >= tabsList.length) {
          newIndex = 0;
        }
        const tabId = tabsList[newIndex].id;
        const tabElement = document.getElementById("tab__" + tabId);
        if (tabElement) {
          tabElement.focus();
        }
      },
      /**
       * Sets the active tab index (internal implementation).
       *
       * @param {number}  tabIndex    The index of the active tab.
       * @param {boolean} scrollToTab Whether to scroll to the tab element.
       */
      setActiveTab: (tabIndex, scrollToTab = false) => {
        const { tabsList } = privateState;
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
          const tabId = tabsList[newIndex].id;
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
     *
     * @type {number|null}
     */
    get tabIndex() {
      return privateState.tabIndex;
    },
    /**
     * Whether the tab panel or tab label is the active tab.
     *
     * @type {boolean}
     */
    get isActiveTab() {
      return privateState.isActiveTab;
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