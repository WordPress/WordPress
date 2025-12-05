"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["accordion"],{

/***/ "../assets/dev/js/frontend/handlers/accordion.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/accordion.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _baseTabs = _interopRequireDefault(__webpack_require__(/*! ./base-tabs */ "../assets/dev/js/frontend/handlers/base-tabs.js"));
class Accordion extends _baseTabs.default {
  getDefaultSettings() {
    const defaultSettings = super.getDefaultSettings();
    return {
      ...defaultSettings,
      showTabFn: 'slideDown',
      hideTabFn: 'slideUp'
    };
  }
}
exports["default"] = Accordion;

/***/ }),

/***/ "../assets/dev/js/frontend/handlers/base-tabs.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/base-tabs.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "../node_modules/core-js/modules/esnext.iterator.filter.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
class baseTabs extends elementorModules.frontend.handlers.Base {
  getDefaultSettings() {
    return {
      selectors: {
        tablist: '[role="tablist"]',
        tabTitle: '.elementor-tab-title',
        tabContent: '.elementor-tab-content'
      },
      classes: {
        active: 'elementor-active'
      },
      showTabFn: 'show',
      hideTabFn: 'hide',
      toggleSelf: true,
      hidePrevious: true,
      autoExpand: true,
      keyDirection: {
        ArrowLeft: elementorFrontendConfig.is_rtl ? 1 : -1,
        ArrowUp: -1,
        ArrowRight: elementorFrontendConfig.is_rtl ? -1 : 1,
        ArrowDown: 1
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $tabTitles: this.findElement(selectors.tabTitle),
      $tabContents: this.findElement(selectors.tabContent)
    };
  }
  activateDefaultTab() {
    const settings = this.getSettings();
    if (!settings.autoExpand || 'editor' === settings.autoExpand && !this.isEdit) {
      return;
    }
    const defaultActiveTab = this.getEditSettings('activeItemIndex') || 1,
      originalToggleMethods = {
        showTabFn: settings.showTabFn,
        hideTabFn: settings.hideTabFn
      };

    // Toggle tabs without animation to avoid jumping
    this.setSettings({
      showTabFn: 'show',
      hideTabFn: 'hide'
    });
    this.changeActiveTab(defaultActiveTab);

    // Return back original toggle effects
    this.setSettings(originalToggleMethods);
  }
  handleKeyboardNavigation(event) {
    const tab = event.currentTarget,
      $tabList = jQuery(tab.closest(this.getSettings('selectors').tablist)),
      // eslint-disable-next-line @wordpress/no-unused-vars-before-return
      $tabs = $tabList.find(this.getSettings('selectors').tabTitle),
      isVertical = 'vertical' === $tabList.attr('aria-orientation');
    switch (event.key) {
      case 'ArrowLeft':
      case 'ArrowRight':
        if (isVertical) {
          return;
        }
        break;
      case 'ArrowUp':
      case 'ArrowDown':
        if (!isVertical) {
          return;
        }
        event.preventDefault();
        break;
      case 'Home':
        event.preventDefault();
        $tabs.first().trigger('focus');
        return;
      case 'End':
        event.preventDefault();
        $tabs.last().trigger('focus');
        return;
      default:
        return;
    }
    const tabIndex = tab.getAttribute('data-tab') - 1,
      direction = this.getSettings('keyDirection')[event.key],
      nextTab = $tabs[tabIndex + direction];
    if (nextTab) {
      nextTab.focus();
    } else if (-1 === tabIndex + direction) {
      $tabs.last().trigger('focus');
    } else {
      $tabs.first().trigger('focus');
    }
  }
  deactivateActiveTab(tabIndex) {
    const settings = this.getSettings(),
      activeClass = settings.classes.active,
      activeFilter = tabIndex ? '[data-tab="' + tabIndex + '"]' : '.' + activeClass,
      $activeTitle = this.elements.$tabTitles.filter(activeFilter),
      $activeContent = this.elements.$tabContents.filter(activeFilter);
    $activeTitle.add($activeContent).removeClass(activeClass);
    $activeTitle.attr({
      tabindex: '-1',
      'aria-selected': 'false',
      'aria-expanded': 'false'
    });
    $activeContent[settings.hideTabFn]();
    $activeContent.attr('hidden', 'hidden');
  }
  activateTab(tabIndex) {
    const settings = this.getSettings(),
      activeClass = settings.classes.active,
      $requestedTitle = this.elements.$tabTitles.filter('[data-tab="' + tabIndex + '"]'),
      $requestedContent = this.elements.$tabContents.filter('[data-tab="' + tabIndex + '"]'),
      animationDuration = 'show' === settings.showTabFn ? 0 : 400;
    $requestedTitle.add($requestedContent).addClass(activeClass);
    $requestedTitle.attr({
      tabindex: '0',
      'aria-selected': 'true',
      'aria-expanded': 'true'
    });
    $requestedContent[settings.showTabFn](animationDuration, () => elementorFrontend.elements.$window.trigger('elementor-pro/motion-fx/recalc'));
    $requestedContent.removeAttr('hidden');
  }
  isActiveTab(tabIndex) {
    return this.elements.$tabTitles.filter('[data-tab="' + tabIndex + '"]').hasClass(this.getSettings('classes.active'));
  }
  bindEvents() {
    this.elements.$tabTitles.on({
      keydown: event => {
        // Support for old markup that includes an `<a>` tag in the tab
        if (jQuery(event.target).is('a') && `Enter` === event.key) {
          event.preventDefault();
        }

        // We listen to keydowon event for these keys in order to prevent undesired page scrolling
        if (['End', 'Home', 'ArrowUp', 'ArrowDown'].includes(event.key)) {
          this.handleKeyboardNavigation(event);
        }
      },
      keyup: event => {
        switch (event.code) {
          case 'ArrowLeft':
          case 'ArrowRight':
            this.handleKeyboardNavigation(event);
            break;
          case 'Enter':
          case 'Space':
            event.preventDefault();
            this.changeActiveTab(event.currentTarget.getAttribute('data-tab'));
            break;
        }
      },
      click: event => {
        event.preventDefault();
        this.changeActiveTab(event.currentTarget.getAttribute('data-tab'));
      }
    });
  }
  onInit(...args) {
    super.onInit(...args);
    this.activateDefaultTab();
  }
  onEditSettingsChange(propertyName) {
    if ('activeItemIndex' === propertyName) {
      this.activateDefaultTab();
    }
  }
  changeActiveTab(tabIndex) {
    const isActiveTab = this.isActiveTab(tabIndex),
      settings = this.getSettings();
    if ((settings.toggleSelf || !isActiveTab) && settings.hidePrevious) {
      this.deactivateActiveTab();
    }
    if (!settings.hidePrevious && isActiveTab) {
      this.deactivateActiveTab(tabIndex);
    }
    if (!isActiveTab) {
      this.activateTab(tabIndex);
    }
  }
}
exports["default"] = baseTabs;

/***/ })

}]);
//# sourceMappingURL=accordion.30310568d938d38f4b75.bundle.js.map