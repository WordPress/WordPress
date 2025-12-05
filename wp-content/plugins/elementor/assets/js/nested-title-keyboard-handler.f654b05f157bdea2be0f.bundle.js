"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["nested-title-keyboard-handler"],{

/***/ "../assets/dev/js/frontend/handlers/accessibility/nested-title-keyboard-handler.js":
/*!*****************************************************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/accessibility/nested-title-keyboard-handler.js ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "../node_modules/core-js/modules/esnext.iterator.filter.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
var _base = _interopRequireDefault(__webpack_require__(/*! ../base */ "../assets/dev/js/frontend/handlers/base.js"));
class NestedTitleKeyboardHandler extends _base.default {
  __construct(settings) {
    super.__construct(settings);
    this.directionNext = 'next';
    this.directionPrevious = 'previous';
    this.focusableElementSelector = 'audio, button, canvas, details, iframe, input, select, summary, textarea, video, [accesskey], [contenteditable], [href], [tabindex]:not([tabindex="-1"])';
  }
  getWidgetNumber() {
    return this.$element.find('> .elementor-widget-container > .e-n-tabs, > .e-n-tabs').attr('data-widget-number');
  }
  getDefaultSettings() {
    return {
      selectors: {
        itemTitle: `[data-tab-title-id*="e-n-tab-title-${this.getWidgetNumber()}"]`,
        itemContainer: `[id*="e-n-tab-content-${this.getWidgetNumber()}"]`
      },
      ariaAttributes: {
        titleStateAttribute: 'aria-selected',
        activeTitleSelector: '[aria-selected="true"]'
      },
      datasets: {
        titleIndex: 'data-tab-index'
      },
      keyDirection: {
        ArrowLeft: elementorFrontendConfig.is_rtl ? this.directionNext : this.directionPrevious,
        ArrowUp: this.directionPrevious,
        ArrowRight: elementorFrontendConfig.is_rtl ? this.directionPrevious : this.directionNext,
        ArrowDown: this.directionNext
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $itemTitles: this.findElement(selectors.itemTitle),
      $itemContainers: this.findElement(selectors.itemContainer),
      $focusableContainerElements: this.getFocusableElements(this.findElement(selectors.itemContainer))
    };
  }
  getFocusableElements($elements) {
    return $elements.find(this.focusableElementSelector).not('[disabled], [inert]');
  }
  getKeyDirectionValue(event) {
    const direction = this.getSettings('keyDirection')[event.key];
    return this.directionNext === direction ? 1 : -1;
  }

  /**
   * @param {HTMLElement} itemTitleElement
   *
   * @return {string}
   */
  getTitleIndex(itemTitleElement) {
    const {
      titleIndex: indexAttribute
    } = this.getSettings('datasets');
    return itemTitleElement.getAttribute(indexAttribute);
  }

  /**
   * @param {string|number} titleIndex
   *
   * @return {string}
   */
  getTitleFilterSelector(titleIndex) {
    const {
      titleIndex: indexAttribute
    } = this.getSettings('datasets');
    return `[${indexAttribute}="${titleIndex}"]`;
  }
  getActiveTitleElement() {
    const activeTitleFilter = this.getSettings('ariaAttributes').activeTitleSelector;
    return this.elements.$itemTitles.filter(activeTitleFilter);
  }
  onInit(...args) {
    super.onInit(...args);
  }
  bindEvents() {
    this.elements.$itemTitles.on(this.getTitleEvents());
    this.elements.$focusableContainerElements.on(this.getContentElementEvents());
  }
  unbindEvents() {
    this.elements.$itemTitles.off(this.getTitleEvents());
    this.elements.$focusableContainerElements.children().off(this.getContentElementEvents());
  }
  getTitleEvents() {
    return {
      keydown: this.handleTitleKeyboardNavigation.bind(this)
    };
  }
  getContentElementEvents() {
    return {
      keydown: this.handleContentElementKeyboardNavigation.bind(this)
    };
  }
  isDirectionKey(event) {
    const directionKeys = ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
    return directionKeys.includes(event.key);
  }
  isActivationKey(event) {
    const activationKeys = ['Enter', ' '];
    return activationKeys.includes(event.key);
  }
  handleTitleKeyboardNavigation(event) {
    if (this.isDirectionKey(event)) {
      event.preventDefault();
      const currentTitleIndex = parseInt(this.getTitleIndex(event.currentTarget)) || 1,
        numberOfTitles = this.elements.$itemTitles.length,
        titleIndexUpdated = this.getTitleIndexFocusUpdated(event, currentTitleIndex, numberOfTitles);
      this.changeTitleFocus(titleIndexUpdated);
      event.stopPropagation();
    } else if (this.isActivationKey(event)) {
      event.preventDefault();
      if (this.handeTitleLinkEnterOrSpaceEvent(event)) {
        return;
      }
      const titleIndex = this.getTitleIndex(event.currentTarget);
      elementorFrontend.elements.$window.trigger('elementor/nested-elements/activate-by-keyboard', {
        widgetId: this.getID(),
        titleIndex
      });
    } else if ('Escape' === event.key) {
      this.handleTitleEscapeKeyEvents(event);
    }
  }
  handeTitleLinkEnterOrSpaceEvent(event) {
    const isLinkElement = 'a' === event?.currentTarget?.tagName?.toLowerCase();
    if (!elementorFrontend.isEditMode() && isLinkElement) {
      event?.currentTarget?.click();
      event.stopPropagation();
    }
    return isLinkElement;
  }
  getTitleIndexFocusUpdated(event, currentTitleIndex, numberOfTitles) {
    let titleIndexUpdated = 0;
    switch (event.key) {
      case 'Home':
        titleIndexUpdated = 1;
        break;
      case 'End':
        titleIndexUpdated = numberOfTitles;
        break;
      default:
        const directionValue = this.getKeyDirectionValue(event),
          isEndReached = numberOfTitles < currentTitleIndex + directionValue,
          isStartReached = 0 === currentTitleIndex + directionValue;
        if (isEndReached) {
          titleIndexUpdated = 1;
        } else if (isStartReached) {
          titleIndexUpdated = numberOfTitles;
        } else {
          titleIndexUpdated = currentTitleIndex + directionValue;
        }
    }
    return titleIndexUpdated;
  }
  changeTitleFocus(titleIndexUpdated) {
    const $newTitle = this.elements.$itemTitles.filter(this.getTitleFilterSelector(titleIndexUpdated));
    this.setTitleTabindex(titleIndexUpdated);
    $newTitle.trigger('focus');
  }
  setTitleTabindex(titleIndex) {
    this.elements.$itemTitles.attr('tabindex', '-1');
    const $newTitle = this.elements.$itemTitles.filter(this.getTitleFilterSelector(titleIndex));
    $newTitle.attr('tabindex', '0');
  }
  handleTitleEscapeKeyEvents() {}
  handleContentElementKeyboardNavigation(event) {
    if ('Tab' === event.key && !event.shiftKey) {
      this.handleContentElementTabEvents(event);
    } else if ('Escape' === event.key) {
      event.preventDefault();
      event.stopPropagation();
      this.handleContentElementEscapeEvents(event);
    }
  }
  handleContentElementEscapeEvents() {
    this.getActiveTitleElement().trigger('focus');
  }
  handleContentElementTabEvents() {}
}
exports["default"] = NestedTitleKeyboardHandler;

/***/ })

}]);
//# sourceMappingURL=nested-title-keyboard-handler.f654b05f157bdea2be0f.bundle.js.map