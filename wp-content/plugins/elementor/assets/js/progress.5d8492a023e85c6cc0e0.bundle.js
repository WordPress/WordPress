"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["progress"],{

/***/ "../assets/dev/js/frontend/handlers/progress.js":
/*!******************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/progress.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
class Progress extends elementorModules.frontend.handlers.Base {
  getDefaultSettings() {
    return {
      selectors: {
        progressNumber: '.elementor-progress-bar'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $progressNumber: this.$element.find(selectors.progressNumber)
    };
  }
  onInit() {
    super.onInit();
    const observer = this.createObserver();
    observer.observe(this.elements.$progressNumber[0]);
  }
  createObserver() {
    const options = {
      root: null,
      threshold: 0,
      rootMargin: '0px'
    };
    return new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const $progressbar = this.elements.$progressNumber;
          $progressbar.css('width', $progressbar.data('max') + '%');
        }
      });
    }, options);
  }
}
exports["default"] = Progress;

/***/ })

}]);
//# sourceMappingURL=progress.5d8492a023e85c6cc0e0.bundle.js.map