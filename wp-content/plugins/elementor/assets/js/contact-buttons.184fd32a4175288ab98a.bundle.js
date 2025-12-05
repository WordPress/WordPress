"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["contact-buttons"],{

/***/ "../modules/floating-buttons/assets/js/floating-buttons/frontend/handlers/contact-buttons.js":
/*!***************************************************************************************************!*\
  !*** ../modules/floating-buttons/assets/js/floating-buttons/frontend/handlers/contact-buttons.js ***!
  \***************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _base = _interopRequireDefault(__webpack_require__(/*! elementor-frontend/handlers/base */ "../assets/dev/js/frontend/handlers/base.js"));
var _clickTracking = _interopRequireDefault(__webpack_require__(/*! ../../../shared/frontend/handlers/click-tracking */ "../modules/floating-buttons/assets/js/shared/frontend/handlers/click-tracking.js"));
class ContactButtonsHandler extends _base.default {
  getDefaultSettings() {
    return {
      selectors: {
        main: '.e-contact-buttons',
        content: '.e-contact-buttons__content',
        contentWrapper: '.e-contact-buttons__content-wrapper',
        chatButton: '.e-contact-buttons__chat-button',
        closeButton: '.e-contact-buttons__close-button',
        messageBubbleTime: '.e-contact-buttons__message-bubble-time'
      },
      constants: {
        entranceAnimation: 'style_chat_box_entrance_animation',
        exitAnimation: 'style_chat_box_exit_animation',
        chatButtonAnimation: 'style_chat_button_animation',
        animated: 'animated',
        animatedWrapper: 'animated-wrapper',
        visible: 'visible',
        reverse: 'reverse',
        hidden: 'hidden',
        hasAnimations: 'has-animations',
        hasEntranceAnimation: 'has-entrance-animation',
        none: 'none'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      main: this.$element[0].querySelector(selectors.main),
      content: this.$element[0].querySelector(selectors.content),
      contentWrapper: this.$element[0].querySelector(selectors.contentWrapper),
      chatButton: this.$element[0].querySelector(selectors.chatButton),
      closeButton: this.$element[0].querySelector(selectors.closeButton),
      messageBubbleTime: this.$element[0].querySelector(selectors.messageBubbleTime)
    };
  }
  getResponsiveSetting(controlName) {
    const currentDevice = elementorFrontend.getCurrentDeviceMode();
    return elementorFrontend.utils.controls.getResponsiveControlValue(this.getElementSettings(), controlName, '', currentDevice);
  }
  bindEvents() {
    if (this.elements.closeButton) {
      this.elements.closeButton.addEventListener('click', this.closeChatBox.bind(this));
    }
    if (this.elements.chatButton) {
      this.elements.chatButton.addEventListener('click', this.onChatButtonClick.bind(this));
      this.elements.chatButton.addEventListener('animationend', this.removeChatButtonAnimationClasses.bind(this));
    }
    if (this.elements.content) {
      this.elements.content.addEventListener('animationend', this.removeAnimationClasses.bind(this));
    }
    if (this.elements.contentWrapper) {
      window.addEventListener('keyup', this.onDocumentKeyup.bind(this));
    }
  }
  contentWrapperIsHidden(hide) {
    if (!this.elements.contentWrapper) {
      return false;
    }
    const {
      hidden
    } = this.getSettings('constants');

    // Set current state
    if (true === hide) {
      this.elements.contentWrapper.classList.add(hidden);
      this.elements.contentWrapper.setAttribute('aria-hidden', 'true');
      return;
    }
    if (false === hide) {
      this.elements.contentWrapper.classList.remove(hidden);
      this.elements.contentWrapper.setAttribute('aria-hidden', 'false');
      return;
    }

    // Get current state
    return this.elements.contentWrapper.classList.contains(hidden);
  }
  onDocumentKeyup(event) {
    // Bail if not ESC key
    if (event.keyCode !== 27 || !this.elements.main) {
      return;
    }

    /* eslint-disable @wordpress/no-global-active-element */
    if (!this.contentWrapperIsHidden() && this.elements.main.contains(document.activeElement)) {
      this.closeChatBox();
    }
    /* eslint-enable @wordpress/no-global-active-element */
  }
  removeAnimationClasses() {
    if (!this.elements.content) {
      return;
    }
    const {
      reverse,
      entranceAnimation,
      exitAnimation,
      animated,
      visible
    } = this.getSettings('constants');
    const isExitAnimation = this.elements.content.classList.contains(reverse),
      openAnimationClass = this.getResponsiveSetting(entranceAnimation),
      exitAnimationClass = this.getResponsiveSetting(exitAnimation);
    if (isExitAnimation) {
      this.elements.content.classList.remove(animated);
      this.elements.content.classList.remove(reverse);
      if (exitAnimationClass) {
        this.elements.content.classList.remove(exitAnimationClass);
      }
      this.elements.content.classList.remove(visible);
    } else {
      this.elements.content.classList.remove(animated);
      if (openAnimationClass) {
        this.elements.content.classList.remove(openAnimationClass);
      }
      this.elements.content.classList.add(visible);
    }
  }
  chatBoxEntranceAnimation() {
    const {
      entranceAnimation,
      animated,
      animatedWrapper,
      none
    } = this.getSettings('constants');
    const entranceAnimationControl = this.getResponsiveSetting(entranceAnimation);
    if (!entranceAnimationControl || none === entranceAnimationControl) {
      return;
    }
    if (this.elements.content) {
      this.elements.content.classList.add(animated);
      this.elements.content.classList.add(entranceAnimationControl);
    }
    if (this.elements.contentWrapper) {
      this.elements.contentWrapper.classList.remove(animatedWrapper);
    }
  }
  chatBoxExitAnimation() {
    const {
      reverse,
      exitAnimation,
      animated,
      animatedWrapper,
      none
    } = this.getSettings('constants');
    const exitAnimationControl = this.getResponsiveSetting(exitAnimation);
    if (!exitAnimationControl || none === exitAnimationControl) {
      return;
    }
    if (this.elements.content) {
      this.elements.content.classList.add(animated);
      this.elements.content.classList.add(reverse);
      this.elements.content.classList.add(exitAnimationControl);
    }
    if (this.elements.contentWrapper) {
      this.elements.contentWrapper.classList.add(animatedWrapper);
    }
  }
  openChatBox() {
    const {
      hasAnimations,
      visible
    } = this.getSettings('constants');
    if (this.elements.main && this.elements.main.classList.contains(hasAnimations)) {
      this.chatBoxEntranceAnimation();
    } else if (this.elements.content) {
      this.elements.content.classList.add(visible);
    }
    if (this.elements.contentWrapper) {
      this.contentWrapperIsHidden(false);
      if (!elementorFrontend.isEditMode()) {
        this.elements.contentWrapper.setAttribute('tabindex', '0');
        this.elements.contentWrapper.focus({
          focusVisible: true
        });
      }
    }
    if (this.elements.chatButton) {
      this.elements.chatButton.setAttribute('aria-expanded', 'true');
    }
    if (this.elements.closeButton) {
      this.elements.closeButton.setAttribute('aria-expanded', 'true');
    }
  }
  closeChatBox() {
    const {
      hasAnimations,
      visible
    } = this.getSettings('constants');
    if (this.elements.main && this.elements.main.classList.contains(hasAnimations)) {
      this.chatBoxExitAnimation();
    } else if (this.elements.content) {
      this.elements.content.classList.remove(visible);
    }
    if (this.elements.contentWrapper) {
      this.contentWrapperIsHidden(true);
    }
    if (this.elements.chatButton) {
      this.elements.chatButton.setAttribute('aria-expanded', 'false');
      this.elements.chatButton.focus({
        focusVisible: true
      });
    }
    if (this.elements.closeButton) {
      this.elements.closeButton.setAttribute('aria-expanded', 'false');
    }
  }
  onChatButtonClick() {
    if (this.elements.contentWrapper && this.contentWrapperIsHidden()) {
      this.openChatBox();
    } else {
      this.closeChatBox();
    }
  }
  initMessageBubbleTime() {
    if (!this.elements.messageBubbleTime) {
      return;
    }
    const messageBubbleTimeFormat = this.elements.messageBubbleTime.dataset.timeFormat;
    const is12hFormat = '12h' === messageBubbleTimeFormat;
    this.elements.messageBubbleTime.innerHTML = new Intl.DateTimeFormat('default', {
      hour12: is12hFormat,
      hour: 'numeric',
      minute: 'numeric'
    }).format(new Date());
  }
  removeChatButtonAnimationClasses() {
    if (!this.elements.chatButton) {
      return;
    }
    const {
      chatButtonAnimation,
      visible
    } = this.getSettings('constants');
    this.elements.chatButton.classList.remove(chatButtonAnimation);
    this.elements.chatButton.classList.add(visible);
  }
  initChatButtonEntranceAnimation() {
    const {
      none,
      chatButtonAnimation
    } = this.getSettings('constants');
    const entranceAnimationControl = this.getResponsiveSetting(chatButtonAnimation);
    if (!entranceAnimationControl || none === entranceAnimationControl) {
      return;
    }
    this.elements.chatButton.classList.add(entranceAnimationControl);
  }
  initDefaultState() {
    // Manage accessibility
    if (this.elements.contentWrapper) {
      const isHidden = this.contentWrapperIsHidden();
      if (this.elements.chatButton) {
        this.elements.chatButton.setAttribute('aria-expanded', !isHidden);
      }
      if (this.elements.closeButton) {
        this.elements.closeButton.setAttribute('aria-expanded', !isHidden);
      }
    }
    if (elementorFrontend.isEditMode() && 'floating-buttons' === elementor?.config?.document?.type) {
      this.openChatBox();
    }
  }
  setupInnerContainer() {
    this.elements.main.closest('.e-con-inner').classList.add('e-con-inner--floating-buttons');
  }
  onInit(...args) {
    const {
      hasEntranceAnimation
    } = this.getSettings('constants');
    super.onInit(...args);
    this.clickTrackingHandler = new _clickTracking.default({
      $element: this.$element
    });
    if (this.elements.messageBubbleTime) {
      this.initMessageBubbleTime();
    }
    this.initDefaultState();
    if (this.elements.chatButton) {
      if (this.elements.chatButton.classList.contains(hasEntranceAnimation)) {
        this.initChatButtonEntranceAnimation();
      }
    }
    this.setupInnerContainer();
  }
}
exports["default"] = ContactButtonsHandler;

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
//# sourceMappingURL=contact-buttons.184fd32a4175288ab98a.bundle.js.map