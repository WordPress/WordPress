"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["floating-bars"],{

/***/ "../modules/floating-buttons/assets/js/floating-bars/frontend/classes/floatin-bar-dom.js":
/*!***********************************************************************************************!*\
  !*** ../modules/floating-buttons/assets/js/floating-bars/frontend/classes/floatin-bar-dom.js ***!
  \***********************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
class FloatingBarDomHelper {
  constructor($element) {
    this.$element = $element;
  }
  maybeMoveToTop() {
    const el = this.$element[0];
    const widget = el.querySelector('.e-floating-bars');
    if (elementorFrontend.isEditMode()) {
      widget.classList.add('is-sticky');
      return;
    }
    if (el.dataset.widget_type.startsWith('floating-bars') && widget.classList.contains('has-vertical-position-top') && !widget.classList.contains('is-sticky')) {
      const wpAdminBar = document.getElementById('wpadminbar');
      const elementToInsert = el.closest('.elementor');
      if (wpAdminBar) {
        wpAdminBar.after(elementToInsert);
      } else {
        document.body.prepend(elementToInsert);
      }
    }
  }
}
exports["default"] = FloatingBarDomHelper;

/***/ }),

/***/ "../modules/floating-buttons/assets/js/floating-bars/frontend/handlers/floating-bars.js":
/*!**********************************************************************************************!*\
  !*** ../modules/floating-buttons/assets/js/floating-bars/frontend/handlers/floating-bars.js ***!
  \**********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
var _base = _interopRequireDefault(__webpack_require__(/*! elementor-frontend/handlers/base */ "../assets/dev/js/frontend/handlers/base.js"));
var _floatinBarDom = _interopRequireDefault(__webpack_require__(/*! ../classes/floatin-bar-dom */ "../modules/floating-buttons/assets/js/floating-bars/frontend/classes/floatin-bar-dom.js"));
var _clickTracking = _interopRequireDefault(__webpack_require__(/*! ../../../shared/frontend/handlers/click-tracking */ "../modules/floating-buttons/assets/js/shared/frontend/handlers/click-tracking.js"));
class FloatingBarsHandler extends _base.default {
  getDefaultSettings() {
    return {
      selectors: {
        main: '.e-floating-bars',
        closeButton: '.e-floating-bars__close-button',
        ctaButton: '.e-floating-bars__cta-button'
      },
      constants: {
        ctaEntranceAnimation: 'style_cta_button_animation',
        ctaEntranceAnimationDelay: 'style_cta_button_animation_delay',
        hasEntranceAnimation: 'has-entrance-animation',
        visible: 'visible',
        isSticky: 'is-sticky',
        hasVerticalPositionTop: 'has-vertical-position-top',
        hasVerticalPositionBottom: 'has-vertical-position-bottom',
        isHidden: 'is-hidden',
        animated: 'animated'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      main: this.$element[0].querySelector(selectors.main),
      mainAll: this.$element[0].querySelectorAll(selectors.main),
      closeButton: this.$element[0].querySelector(selectors.closeButton),
      ctaButton: this.$element[0].querySelector(selectors.ctaButton)
    };
  }
  onElementChange(property) {
    const changedProperties = ['advanced_vertical_position'];
    if (changedProperties.includes(property)) {
      this.initDefaultState();
    }
  }
  getResponsiveSetting(controlName) {
    const currentDevice = elementorFrontend.getCurrentDeviceMode();
    return elementorFrontend.utils.controls.getResponsiveControlValue(this.getElementSettings(), controlName, '', currentDevice);
  }
  bindEvents() {
    if (this.elements.closeButton) {
      this.elements.closeButton.addEventListener('click', this.closeFloatingBar.bind(this));
    }
    if (this.elements.ctaButton) {
      this.elements.ctaButton.addEventListener('animationend', this.handleAnimationEnd.bind(this));
    }
    if (this.elements.main) {
      window.addEventListener('keyup', this.onDocumentKeyup.bind(this));
    }
    if (this.hasStickyElements()) {
      window.addEventListener('resize', this.handleStickyElements.bind(this));
    }
  }
  isStickyTop() {
    const {
      isSticky,
      hasVerticalPositionTop
    } = this.getSettings('constants');
    return this.elements.main.classList.contains(isSticky) && this.elements.main.classList.contains(hasVerticalPositionTop);
  }
  isStickyBottom() {
    const {
      isSticky,
      hasVerticalPositionBottom
    } = this.getSettings('constants');
    return this.elements.main.classList.contains(isSticky) && this.elements.main.classList.contains(hasVerticalPositionBottom);
  }
  hasStickyElements() {
    const stickyElements = document.querySelectorAll('.elementor-sticky');
    return stickyElements.length > 0;
  }
  focusOnLoad() {
    this.elements.main.setAttribute('tabindex', '0');
    this.elements.main.focus({
      focusVisible: true
    });
  }
  applyBodyPadding() {
    const mainHeight = this.elements.main.offsetHeight;
    document.body.style.paddingTop = `${mainHeight}px`;
  }
  removeBodyPadding() {
    document.body.style.paddingTop = '0';
  }
  handleWPAdminBar() {
    const wpAdminBar = elementorFrontend.elements.$wpAdminBar;
    if (wpAdminBar.length) {
      this.elements.main.style.top = `${wpAdminBar.height()}px`;
    }
  }
  handleStickyElements() {
    const mainHeight = this.elements.main.offsetHeight;
    const wpAdminBar = elementorFrontend.elements.$wpAdminBar;
    const stickyElements = document.querySelectorAll('.elementor-sticky:not(.elementor-sticky__spacer)');
    if (0 === stickyElements.length) {
      return;
    }
    stickyElements.forEach(stickyElement => {
      const dataSettings = stickyElement.getAttribute('data-settings');
      const stickyPosition = JSON.parse(dataSettings)?.sticky;
      const isTop = '0px' === stickyElement.style.top || 'top' === stickyPosition;
      const isBottom = '0px' === stickyElement.style.bottom || 'bottom' === stickyPosition;
      if (this.isStickyTop() && isTop) {
        if (wpAdminBar.length) {
          stickyElement.style.top = `${mainHeight + wpAdminBar.height()}px`;
        } else {
          stickyElement.style.top = `${mainHeight}px`;
        }
      } else if (this.isStickyBottom() && isBottom) {
        stickyElement.style.bottom = `${mainHeight}px`;
      }
      if (elementorFrontend.isEditMode()) {
        if (isTop) {
          stickyElement.style.top = this.isStickyTop() ? `${mainHeight}px` : '0px';
        } else if (isBottom) {
          stickyElement.style.bottom = this.isStickyBottom() ? `${mainHeight}px` : '0px';
        }
      }
    });
    document.querySelectorAll('.elementor-sticky__spacer').forEach(stickySpacer => {
      const dataSettings = stickySpacer.getAttribute('data-settings');
      const stickyPosition = JSON.parse(dataSettings)?.sticky;
      const isTop = '0px' === stickySpacer.style.top || 'top' === stickyPosition;
      if (this.isStickyTop() && isTop) {
        stickySpacer.style.marginBottom = `${mainHeight}px`;
      }
    });
  }
  closeFloatingBar() {
    const {
      isHidden
    } = this.getSettings('constants');
    if (!elementorFrontend.isEditMode()) {
      this.elements.main.classList.add(isHidden);
      if (this.hasStickyElements()) {
        this.handleStickyElements();
      } else if (this.isStickyTop()) {
        this.removeBodyPadding();
      }
    }
  }
  initEntranceAnimation() {
    const {
      animated,
      ctaEntranceAnimation,
      ctaEntranceAnimationDelay,
      hasEntranceAnimation
    } = this.getSettings('constants');
    const entranceAnimationClass = this.getResponsiveSetting(ctaEntranceAnimation);
    const entranceAnimationDelay = this.getResponsiveSetting(ctaEntranceAnimationDelay) || 0;
    const setTimeoutDelay = entranceAnimationDelay + 500;
    this.elements.ctaButton.classList.add(animated);
    this.elements.ctaButton.classList.add(entranceAnimationClass);
    setTimeout(() => {
      this.elements.ctaButton.classList.remove(hasEntranceAnimation);
    }, setTimeoutDelay);
  }
  handleAnimationEnd() {
    this.removeEntranceAnimationClasses();
    this.focusOnLoad();
  }
  removeEntranceAnimationClasses() {
    if (!this.elements.ctaButton) {
      return;
    }
    const {
      animated,
      ctaEntranceAnimation,
      visible
    } = this.getSettings('constants');
    const entranceAnimationClass = this.getResponsiveSetting(ctaEntranceAnimation);
    this.elements.ctaButton.classList.remove(animated);
    this.elements.ctaButton.classList.remove(entranceAnimationClass);
    this.elements.ctaButton.classList.add(visible);
  }
  onDocumentKeyup(event) {
    // Bail if not ESC key
    if (event.keyCode !== 27 || !this.elements.main) {
      return;
    }

    /* eslint-disable @wordpress/no-global-active-element */
    if (this.elements.main.contains(document.activeElement)) {
      this.closeFloatingBar();
    }
    /* eslint-enable @wordpress/no-global-active-element */
  }
  initDefaultState() {
    const {
      hasEntranceAnimation
    } = this.getSettings('constants');
    if (this.isStickyTop()) {
      this.handleWPAdminBar();
    }
    if (this.hasStickyElements()) {
      this.handleStickyElements();
    } else if (this.isStickyTop()) {
      this.applyBodyPadding();
    }
    if (this.elements.main && !this.elements.ctaButton.classList.contains(hasEntranceAnimation) && !elementorFrontend.isEditMode()) {
      this.focusOnLoad();
    }
  }
  setupInnerContainer() {
    this.elements.main.closest('.e-con-inner').classList.add('e-con-inner--floating-bars');
    this.elements.main.closest('.e-con').classList.add('e-con--floating-bars');
  }
  onInit(...args) {
    const {
      hasEntranceAnimation
    } = this.getSettings('constants');
    super.onInit(...args);
    this.clickTrackingHandler = new _clickTracking.default({
      $element: this.$element
    });
    const domHelper = new _floatinBarDom.default(this.$element);
    domHelper.maybeMoveToTop();
    if (this.elements.ctaButton && this.elements.ctaButton.classList.contains(hasEntranceAnimation)) {
      this.initEntranceAnimation();
    }
    this.initDefaultState();
    this.setupInnerContainer();
  }
}
exports["default"] = FloatingBarsHandler;

/***/ }),

/***/ "../modules/floating-buttons/assets/js/shared/frontend/handlers/click-tracking.js":
/*!****************************************************************************************!*\
  !*** ../modules/floating-buttons/assets/js/shared/frontend/handlers/click-tracking.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
var _base = _interopRequireDefault(__webpack_require__(/*! elementor-frontend/handlers/base */ "../assets/dev/js/frontend/handlers/base.js"));
class ClickTrackingHandler extends _base.default {
  clicks = [];
  getDefaultSettings() {
    return {
      selectors: {
        contentWrapper: '.e-contact-buttons__content-wrapper',
        contactButtonCore: '.e-contact-buttons__send-button',
        contentWrapperFloatingBars: '.e-floating-bars',
        floatingBarCTAButton: '.e-floating-bars__cta-button',
        elementorWrapper: '[data-elementor-type="floating-buttons"]'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      contentWrapper: this.$element[0].querySelector(selectors.contentWrapper),
      contentWrapperFloatingBars: this.$element[0].querySelector(selectors.contentWrapperFloatingBars)
    };
  }
  bindEvents() {
    if (this.elements.contentWrapper) {
      this.elements.contentWrapper.addEventListener('click', this.onChatButtonTrackClick.bind(this));
    }
    if (this.elements.contentWrapperFloatingBars) {
      this.elements.contentWrapperFloatingBars.addEventListener('click', this.onChatButtonTrackClick.bind(this));
    }
    window.addEventListener('beforeunload', () => {
      if (this.clicks.length > 0) {
        this.sendClicks();
      }
    });
  }
  onChatButtonTrackClick(event) {
    const targetElement = event.target || event.srcElement;
    const selectors = this.getSettings('selectors');
    if (targetElement.matches(selectors.contactButtonCore) || targetElement.closest(selectors.contactButtonCore) || targetElement.matches(selectors.floatingBarCTAButton) || targetElement.closest(selectors.floatingBarCTAButton)) {
      this.getDocumentIdAndTrack(targetElement, selectors);
    }
  }
  getDocumentIdAndTrack(targetElement, selectors) {
    const documentId = targetElement.closest(selectors.elementorWrapper).dataset.elementorId;
    this.trackClick(documentId);
  }
  trackClick(documentId) {
    if (!documentId) {
      return;
    }
    this.clicks.push(documentId);
    if (this.clicks.length >= 10) {
      this.sendClicks();
    }
  }
  sendClicks() {
    const formData = new FormData();
    formData.append('action', 'elementor_send_clicks');
    formData.append('_nonce', elementorFrontendConfig?.nonces?.floatingButtonsClickTracking);
    this.clicks.forEach(documentId => formData.append('clicks[]', documentId));
    fetch(elementorFrontendConfig?.urls?.ajaxurl, {
      method: 'POST',
      body: formData
    }).then(() => {
      this.clicks = [];
    });
  }
}
exports["default"] = ClickTrackingHandler;

/***/ })

}]);
//# sourceMappingURL=floating-bars.8494af43b5d06b046fcd.bundle.js.map