"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["section-editor-handlers"],{

/***/ "../assets/dev/js/frontend/handlers/section/shapes.js":
/*!************************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/section/shapes.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
class Shapes extends elementorModules.frontend.handlers.Base {
  getDefaultSettings() {
    return {
      selectors: {
        container: '> .elementor-shape-%s'
      },
      svgURL: elementorFrontend.config.urls.assets + 'shapes/'
    };
  }
  getDefaultElements() {
    const elements = {},
      selectors = this.getSettings('selectors');
    elements.$topContainer = this.$element.find(selectors.container.replace('%s', 'top'));
    elements.$bottomContainer = this.$element.find(selectors.container.replace('%s', 'bottom'));
    return elements;
  }
  isActive() {
    return elementorFrontend.isEditMode();
  }
  getSvgURL(shapeType, fileName) {
    let svgURL = this.getSettings('svgURL') + fileName + '.svg';
    if (elementor.config.additional_shapes && shapeType in elementor.config.additional_shapes) {
      svgURL = elementor.config.additional_shapes[shapeType];
      if (-1 < fileName.indexOf('-negative')) {
        svgURL = svgURL.replace('.svg', '-negative.svg');
      }
    }
    return svgURL;
  }
  buildSVG(side) {
    const baseSettingKey = 'shape_divider_' + side,
      shapeType = this.getElementSettings(baseSettingKey),
      $svgContainer = this.elements['$' + side + 'Container'];
    $svgContainer.attr('data-shape', shapeType);
    if (!shapeType) {
      $svgContainer.empty(); // Shape-divider set to 'none'
      return;
    }
    let fileName = shapeType;
    if (this.getElementSettings(baseSettingKey + '_negative')) {
      fileName += '-negative';
    }
    const svgURL = this.getSvgURL(shapeType, fileName);
    jQuery.get(svgURL, data => {
      $svgContainer.empty().append(data.childNodes[0]);
    });
    this.setNegative(side);
  }
  setNegative(side) {
    this.elements['$' + side + 'Container'].attr('data-negative', !!this.getElementSettings('shape_divider_' + side + '_negative'));
  }
  onInit(...args) {
    if (!this.isActive(this.getSettings())) {
      return;
    }
    super.onInit(...args);
    ['top', 'bottom'].forEach(side => {
      if (this.getElementSettings('shape_divider_' + side)) {
        this.buildSVG(side);
      }
    });
  }
  onElementChange(propertyName) {
    const shapeChange = propertyName.match(/^shape_divider_(top|bottom)$/);
    if (shapeChange) {
      this.buildSVG(shapeChange[1]);
      return;
    }
    const negativeChange = propertyName.match(/^shape_divider_(top|bottom)_negative$/);
    if (negativeChange) {
      this.buildSVG(negativeChange[1]);
      this.setNegative(negativeChange[1]);
    }
  }
}
exports["default"] = Shapes;

/***/ })

}]);
//# sourceMappingURL=section-editor-handlers.79e6ddb8decf79f20369.bundle.js.map