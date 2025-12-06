// packages/block-library/build-module/navigation/view.js
import {
  store,
  getContext,
  getElement,
  withSyncEvent
} from "@wordpress/interactivity";
var focusableSelectors = [
  "a[href]",
  'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',
  "select:not([disabled]):not([aria-hidden])",
  "textarea:not([disabled]):not([aria-hidden])",
  "button:not([disabled]):not([aria-hidden])",
  "[contenteditable]",
  '[tabindex]:not([tabindex^="-"])'
];
document.addEventListener("click", () => {
});
var { state, actions } = store(
  "core/navigation",
  {
    state: {
      get roleAttribute() {
        const ctx = getContext();
        return ctx.type === "overlay" && state.isMenuOpen ? "dialog" : null;
      },
      get ariaModal() {
        const ctx = getContext();
        return ctx.type === "overlay" && state.isMenuOpen ? "true" : null;
      },
      get ariaLabel() {
        const ctx = getContext();
        return ctx.type === "overlay" && state.isMenuOpen ? ctx.ariaLabel : null;
      },
      get isMenuOpen() {
        return Object.values(state.menuOpenedBy).filter(Boolean).length > 0;
      },
      get menuOpenedBy() {
        const ctx = getContext();
        return ctx.type === "overlay" ? ctx.overlayOpenedBy : ctx.submenuOpenedBy;
      }
    },
    actions: {
      openMenuOnHover() {
        const { type, overlayOpenedBy } = getContext();
        if (type === "submenu" && // Only open on hover if the overlay is closed.
        Object.values(overlayOpenedBy || {}).filter(Boolean).length === 0) {
          actions.openMenu("hover");
        }
      },
      closeMenuOnHover() {
        const { type, overlayOpenedBy } = getContext();
        if (type === "submenu" && // Only close on hover if the overlay is closed.
        Object.values(overlayOpenedBy || {}).filter(Boolean).length === 0) {
          actions.closeMenu("hover");
        }
      },
      openMenuOnClick() {
        const ctx = getContext();
        const { ref } = getElement();
        ctx.previousFocus = ref;
        actions.openMenu("click");
      },
      closeMenuOnClick() {
        actions.closeMenu("click");
        actions.closeMenu("focus");
      },
      openMenuOnFocus() {
        actions.openMenu("focus");
      },
      toggleMenuOnClick() {
        const ctx = getContext();
        const { ref } = getElement();
        if (window.document.activeElement !== ref) {
          ref.focus();
        }
        const { menuOpenedBy } = state;
        if (menuOpenedBy.click || menuOpenedBy.focus) {
          actions.closeMenu("click");
          actions.closeMenu("focus");
        } else {
          ctx.previousFocus = ref;
          actions.openMenu("click");
        }
      },
      handleMenuKeydown: withSyncEvent((event) => {
        const { type, firstFocusableElement, lastFocusableElement } = getContext();
        if (state.menuOpenedBy.click) {
          if (event.key === "Escape") {
            event.stopPropagation();
            actions.closeMenu("click");
            actions.closeMenu("focus");
            return;
          }
          if (type === "overlay" && event.key === "Tab") {
            if (event.shiftKey && window.document.activeElement === firstFocusableElement) {
              event.preventDefault();
              lastFocusableElement.focus();
            } else if (!event.shiftKey && window.document.activeElement === lastFocusableElement) {
              event.preventDefault();
              firstFocusableElement.focus();
            }
          }
        }
      }),
      handleMenuFocusout: withSyncEvent((event) => {
        const { modal, type } = getContext();
        if (event.relatedTarget === null || !modal?.contains(event.relatedTarget) && event.target !== window.document.activeElement && type === "submenu") {
          actions.closeMenu("click");
          actions.closeMenu("focus");
        }
      }),
      openMenu(menuOpenedOn = "click") {
        const { type } = getContext();
        state.menuOpenedBy[menuOpenedOn] = true;
        if (type === "overlay") {
          document.documentElement.classList.add("has-modal-open");
        }
      },
      closeMenu(menuClosedOn = "click") {
        const ctx = getContext();
        state.menuOpenedBy[menuClosedOn] = false;
        if (!state.isMenuOpen) {
          if (ctx.modal?.contains(window.document.activeElement)) {
            ctx.previousFocus?.focus();
          }
          ctx.modal = null;
          ctx.previousFocus = null;
          if (ctx.type === "overlay") {
            document.documentElement.classList.remove(
              "has-modal-open"
            );
          }
        }
      }
    },
    callbacks: {
      initMenu() {
        const ctx = getContext();
        const { ref } = getElement();
        if (state.isMenuOpen) {
          const focusableElements = ref.querySelectorAll(focusableSelectors);
          ctx.modal = ref;
          ctx.firstFocusableElement = focusableElements[0];
          ctx.lastFocusableElement = focusableElements[focusableElements.length - 1];
        }
      },
      focusFirstElement() {
        const { ref } = getElement();
        if (state.isMenuOpen) {
          const focusableElements = ref.querySelectorAll(focusableSelectors);
          focusableElements?.[0]?.focus();
        }
      }
    }
  },
  { lock: true }
);
//# sourceMappingURL=view.js.map
