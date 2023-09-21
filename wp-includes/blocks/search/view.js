/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
window.addEventListener('DOMContentLoaded', () => {
  const hiddenClass = 'wp-block-search__searchfield-hidden';
  Array.from(document.getElementsByClassName('wp-block-search__button-behavior-expand')).forEach(block => {
    const searchField = block.querySelector('.wp-block-search__input');
    const searchButton = block.querySelector('.wp-block-search__button');
    const searchLabel = block.querySelector('.wp-block-search__label');
    const ariaLabel = searchButton.getAttribute('aria-label');
    const id = searchField.getAttribute('id');

    const toggleSearchField = showSearchField => {
      if (showSearchField) {
        searchField.removeAttribute('aria-hidden');
        searchField.removeAttribute('tabindex');
        searchButton.removeAttribute('aria-expanded');
        searchButton.removeAttribute('aria-controls');
        searchButton.setAttribute('type', 'submit');
        searchButton.setAttribute('aria-label', 'Submit Search');
        return block.classList.remove(hiddenClass);
      }

      searchButton.removeAttribute('type');
      searchField.setAttribute('aria-hidden', 'true');
      searchField.setAttribute('tabindex', '-1');
      searchButton.setAttribute('aria-expanded', 'false');
      searchButton.setAttribute('aria-controls', id);
      searchButton.setAttribute('aria-label', ariaLabel);
      return block.classList.add(hiddenClass);
    };

    const hideSearchField = e => {
      if (!e.target.closest('.wp-block-search')) {
        return toggleSearchField(false);
      }

      if (e.key === 'Escape') {
        searchButton.focus();
        return toggleSearchField(false);
      }
    };

    const handleButtonClick = e => {
      if (block.classList.contains(hiddenClass)) {
        e.preventDefault();
        searchField.focus();
        toggleSearchField(true);
      }
    };

    searchButton.removeAttribute('type');
    searchField.addEventListener('keydown', e => {
      hideSearchField(e);
    });
    searchButton.addEventListener('click', handleButtonClick);
    searchButton.addEventListener('keydown', e => {
      hideSearchField(e);
    });

    if (searchLabel) {
      searchLabel.addEventListener('click', handleButtonClick);
    }

    document.body.addEventListener('click', hideSearchField);
  });
});

/******/ })()
;