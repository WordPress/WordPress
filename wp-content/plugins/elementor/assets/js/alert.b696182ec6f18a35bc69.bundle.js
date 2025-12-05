"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["alert"],{

/***/ "../assets/dev/js/frontend/handlers/alert.js":
/*!***************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/alert.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
class Alert extends elementorModules.frontend.handlers.Base {
  getDefaultSettings() {
    return {
      selectors: {
        dismissButton: '.elementor-alert-dismiss'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $dismissButton: this.$element.find(selectors.dismissButton)
    };
  }
  bindEvents() {
    this.elements.$dismissButton.on('click', this.onDismissButtonClick.bind(this));
  }
  onDismissButtonClick() {
    this.$element.fadeOut();
  }
}
exports["default"] = Alert;

/***/ })

}]);
//# sourceMappingURL=alert.b696182ec6f18a35bc69.bundle.js.map