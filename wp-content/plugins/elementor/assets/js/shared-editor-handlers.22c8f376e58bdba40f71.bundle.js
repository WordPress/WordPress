"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["shared-editor-handlers"],{

/***/ "../assets/dev/js/frontend/handlers/handles-position.js":
/*!**************************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/handles-position.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
const handlesInsideClass = 'e-handles-inside';
const handlesHeight = 25;
class HandlesPosition extends elementorModules.frontend.handlers.Base {
  onInit() {
    this.$element.on('mouseenter', this.setHandlesPosition.bind(this));
  }
  isSectionScrollSnapEnabled() {
    return elementor.settings.page.model.attributes.scroll_snap;
  }
  isFirstElement() {
    return this.$element[0] === document.querySelector('.elementor-section-wrap > .elementor-element:first-child');
  }
  isOverflowHidden() {
    return 'hidden' === this.$element.css('overflow');
  }
  getOffset() {
    if ('body' === elementor.config.document.container) {
      return this.$element.offset().top;
    }
    const $container = jQuery(elementor.config.document.container);
    return this.$element.offset().top - $container.offset().top;
  }
  setHandlesPosition() {
    const document = elementor.documents.getCurrent();
    if (!document || !document.container.isEditable()) {
      return;
    }
    if (this.isSectionScrollSnapEnabled()) {
      this.$element.addClass(handlesInsideClass);
      return;
    }
    if (!this.isOverflowHidden() && !this.isFirstElement()) {
      this.$element.removeClass(handlesInsideClass);
      return;
    }
    const offset = this.getOffset(),
      $handlesElement = this.$element.find('> .elementor-element-overlay > .elementor-editor-section-settings');
    if (offset < handlesHeight) {
      this.$element.addClass(handlesInsideClass);
      $handlesElement.css('top', offset < -5 ? -offset : '');
    } else {
      this.$element.removeClass(handlesInsideClass);
    }
  }
}
exports["default"] = HandlesPosition;

/***/ })

}]);
//# sourceMappingURL=shared-editor-handlers.22c8f376e58bdba40f71.bundle.js.map