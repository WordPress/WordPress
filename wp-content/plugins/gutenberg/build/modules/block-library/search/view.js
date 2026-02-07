// packages/block-library/build-module/search/view.js
import {
  store,
  getContext,
  getElement,
  withSyncEvent
} from "@wordpress/interactivity";
var { actions } = store(
  "core/search",
  {
    state: {
      get ariaLabel() {
        const {
          isSearchInputVisible,
          ariaLabelCollapsed,
          ariaLabelExpanded
        } = getContext();
        return isSearchInputVisible ? ariaLabelExpanded : ariaLabelCollapsed;
      },
      get ariaControls() {
        const { isSearchInputVisible, inputId } = getContext();
        return isSearchInputVisible ? null : inputId;
      },
      get type() {
        const { isSearchInputVisible } = getContext();
        return isSearchInputVisible ? "submit" : "button";
      },
      get tabindex() {
        const { isSearchInputVisible } = getContext();
        return isSearchInputVisible ? "0" : "-1";
      }
    },
    actions: {
      openSearchInput: withSyncEvent((event) => {
        const ctx = getContext();
        const { ref } = getElement();
        if (!ctx.isSearchInputVisible) {
          event.preventDefault();
          ctx.isSearchInputVisible = true;
          ref.parentElement.querySelector("input").focus();
        }
      }),
      closeSearchInput() {
        const ctx = getContext();
        ctx.isSearchInputVisible = false;
      },
      handleSearchKeydown: withSyncEvent((event) => {
        const { ref } = getElement();
        if (event?.key === "Escape") {
          actions.closeSearchInput();
          ref.querySelector("button").focus();
        }
      }),
      handleSearchFocusout: withSyncEvent((event) => {
        const { ref } = getElement();
        if (!ref.contains(event.relatedTarget) && event.target !== window.document.activeElement) {
          actions.closeSearchInput();
        }
      })
    }
  },
  { lock: true }
);
//# sourceMappingURL=view.js.map
