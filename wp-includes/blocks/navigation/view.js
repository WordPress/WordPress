/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
// Open on click functionality.
function closeSubmenus(element) {
  element.querySelectorAll('[aria-expanded="true"]').forEach(function (toggle) {
    toggle.setAttribute('aria-expanded', 'false');
  });
}

function toggleSubmenuOnClick(event) {
  const buttonToggle = event.target.closest('[aria-expanded]');
  const isSubmenuOpen = buttonToggle.getAttribute('aria-expanded');

  if (isSubmenuOpen === 'true') {
    closeSubmenus(buttonToggle.closest('.wp-block-navigation-item'));
  } else {
    // Close all sibling submenus.
    const parentElement = buttonToggle.closest('.wp-block-navigation-item');
    const navigationParent = buttonToggle.closest('.wp-block-navigation__submenu-container, .wp-block-navigation__container, .wp-block-page-list');
    navigationParent.querySelectorAll('.wp-block-navigation-item').forEach(function (child) {
      if (child !== parentElement) {
        closeSubmenus(child);
      }
    }); // Open submenu.

    buttonToggle.setAttribute('aria-expanded', 'true');
  }
} // Necessary for some themes such as TT1 Blocks, where
// scripts could be loaded before the body.


window.addEventListener('load', () => {
  const submenuButtons = document.querySelectorAll('.wp-block-navigation-submenu__toggle');
  submenuButtons.forEach(function (button) {
    button.addEventListener('click', toggleSubmenuOnClick);
  }); // Close on click outside.

  document.addEventListener('click', function (event) {
    const navigationBlocks = document.querySelectorAll('.wp-block-navigation');
    navigationBlocks.forEach(function (block) {
      if (!block.contains(event.target)) {
        closeSubmenus(block);
      }
    });
  }); // Close on focus outside or escape key.

  document.addEventListener('keyup', function (event) {
    const submenuBlocks = document.querySelectorAll('.wp-block-navigation-item.has-child');
    submenuBlocks.forEach(function (block) {
      if (!block.contains(event.target)) {
        closeSubmenus(block);
      } else if (event.key === 'Escape') {
        const toggle = block.querySelector('[aria-expanded="true"]');
        closeSubmenus(block); // Focus the submenu trigger so focus does not get trapped in the closed submenu.

        toggle === null || toggle === void 0 ? void 0 : toggle.focus();
      }
    });
  });
});

/******/ })()
;