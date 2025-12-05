"use strict";
(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["container-editor-handlers"],{

/***/ "../assets/dev/js/frontend/handlers/container/grid-container.js":
/*!**********************************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/container/grid-container.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
class GridContainer extends elementorModules.frontend.handlers.Base {
  __construct(settings) {
    super.__construct(settings);
    this.onDeviceModeChange = this.onDeviceModeChange.bind(this);
    this.updateEmptyViewHeight = this.updateEmptyViewHeight.bind(this);
  }
  isActive() {
    return elementorFrontend.isEditMode();
  }
  getDefaultSettings() {
    const gridItemSuffixes = ['_heading_grid_item', '_grid_column', '_grid_column_custom', '_grid_row', '_grid_row_custom', 'heading_grid_item', 'grid_column', 'grid_column_custom', 'grid_row', 'grid_row_custom'];
    const gridItemControls = gridItemSuffixes.map(suffix => `[class*="elementor-control-${suffix}"]`).join(', ');
    return {
      selectors: {
        gridOutline: '.e-grid-outline',
        directGridOverlay: ':scope > .e-grid-outline',
        boxedContainer: ':scope > .e-con-inner',
        emptyView: '.elementor-empty-view'
      },
      classes: {
        outline: 'e-grid-outline',
        outlineItem: 'e-grid-outline-item',
        gridItemControls
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      outlineParentContainer: null,
      gridOutline: this.findElement(selectors.gridOutline),
      directChildGridOverlay: this.findElement(selectors.directGridOverlay),
      emptyView: this.findElement(selectors.emptyView)[0],
      container: this.$element[0]
    };
  }
  onInit() {
    super.onInit();
    this.initLayoutOverlay();
    this.updateEmptyViewHeight();
    elementor.hooks.addAction('panel/open_editor/container', this.onPanelShow);
  }
  handleGridControls(sectionName, editor) {
    const advancedSections = ['_section_style',
    // Widgets
    'section_layout' // Containers
    ];
    if (!advancedSections.includes(sectionName)) {
      return;
    }
    if (!this.isItemInGridCell(editor)) {
      this.hideGridControls(editor);
    }
  }
  isItemInGridCell(editor) {
    const container = editor?.getOption('editedElementView')?.getContainer();
    if ('function' !== typeof container?.parent?.model?.getSetting) {
      return false;
    }
    return 'grid' === container?.parent?.model?.getSetting('container_type');
  }
  hideGridControls(editor) {
    const classes = this.getSettings('classes');
    const gridControls = editor?.el.querySelectorAll(classes.gridItemControls);
    gridControls.forEach(element => {
      element.style.display = 'none';
    });
  }
  onPanelShow(panel, model) {
    const settingsModel = model.get('settings'),
      containerType = settingsModel.get('container_type'),
      $linkElement = panel.$el.find('#elementor-panel__editor__help__link'),
      href = 'grid' === containerType ? 'https://go.elementor.com/widget-container-grid' : 'https://go.elementor.com/widget-container';
    if ($linkElement) {
      $linkElement.attr('href', href);
    }
  }
  bindEvents() {
    elementorFrontend.elements.$window.on('resize', this.onDeviceModeChange);
    elementorFrontend.elements.$window.on('resize', this.updateEmptyViewHeight);
    this.addChildLifeCycleEventListeners();
    elementor.channels.editor.on('section:activated', this.handleGridControls.bind(this));
  }
  unbindEvents() {
    this.removeChildLifeCycleEventListeners();
    elementorFrontend.elements.$window.off('resize', this.onDeviceModeChange);
    elementorFrontend.elements.$window.off('resize', this.updateEmptyViewHeight);
    elementor.channels.editor.off('section:activated', this.handleGridControls.bind(this));
  }
  initLayoutOverlay() {
    this.getCorrectContainer();
    // Re-init empty view element after container layout change
    const selectors = this.getSettings('selectors'),
      isGridContainer = 'grid' === this.getElementSettings('container_type');
    this.elements.emptyView = this.findElement(selectors.emptyView)[0];
    if (isGridContainer && this.elements?.emptyView) {
      this.elements.emptyView.style.display = this.shouldRemoveEmptyView() ? 'none' : 'block';
    }
    if (!this.shouldDrawOutline()) {
      return;
    }
    this.removeExistingOverlay();
    this.createOverlayContainer();
    this.createOverlayItems();
  }
  shouldDrawOutline() {
    const {
      grid_outline: gridOutline
    } = this.getElementSettings();
    return gridOutline;
  }
  getCorrectContainer() {
    const container = this.elements.container,
      getDefaultSettings = this.getDefaultSettings(),
      {
        selectors: {
          boxedContainer
        }
      } = getDefaultSettings;
    this.elements.outlineParentContainer = container.querySelector(boxedContainer) || container;
  }
  removeExistingOverlay() {
    this.elements.gridOutline?.remove();
  }
  createOverlayContainer() {
    const {
        outlineParentContainer
      } = this.elements,
      {
        classes: {
          outline
        }
      } = this.getDefaultSettings(),
      gridOutline = document.createElement('div');
    gridOutline.classList.add(outline);
    outlineParentContainer.appendChild(gridOutline);
    this.elements.gridOutline = gridOutline;
    this.setGridOutlineDimensions();
  }
  createOverlayItems() {
    const {
        gridOutline
      } = this.elements,
      {
        classes: {
          outlineItem
        }
      } = this.getDefaultSettings(),
      numberOfItems = this.getMaxOutlineElementsNumber();
    for (let i = 0; i < numberOfItems; i++) {
      const gridOutlineItem = document.createElement('div');
      gridOutlineItem.classList.add(outlineItem);
      gridOutline.appendChild(gridOutlineItem);
    }
  }

  /**
   * Get the grid dimensions for the current device.
   *
   * @return { { columns: { value, length }, rows: { value, length } } }
   */
  getDeviceGridDimensions() {
    const currentDevice = elementor.channels.deviceMode.request('currentMode');
    return {
      rows: this.getControlValues('grid_rows_grid', currentDevice, 'grid-template-rows') || 1,
      columns: this.getControlValues('grid_columns_grid', currentDevice, 'grid-template-columns') || 1
    };
  }
  setGridOutlineDimensions() {
    const {
        gridOutline
      } = this.elements,
      {
        rows,
        columns
      } = this.getDeviceGridDimensions();
    gridOutline.style.gridTemplateColumns = columns.value;
    gridOutline.style.gridTemplateRows = rows.value;
  }

  /**
   * Set the control value for the current device.
   * Distinguish between grid custom values and slider controls.
   *
   * @param {string} control  - The control name.
   * @param {string} device   - The device mode.
   * @param {string} property - The CSS property name we need to copy from the parent container.
   *
   * @return {Object} - E,g. {value: repeat(2, 1fr), length: 2}.
   */
  getControlValues(control, device, property) {
    const elementSettings = this.getElementSettings(),
      {
        unit,
        size
      } = elementSettings[control],
      {
        outlineParentContainer
      } = this.elements,
      controlValueForCurrentDevice = elementorFrontend.utils.controls.getResponsiveControlValue(elementSettings, control, 'size', device),
      controlValue = this.getComputedStyle(outlineParentContainer, property),
      computedStyleLength = controlValue.split(' ').length;
    let controlData;
    if ('custom' === unit && 'string' === typeof controlValueForCurrentDevice || size < computedStyleLength) {
      controlData = {
        value: controlValue
      };
    } else {
      // In this case the data is taken from the getComputedStyle and not from the control, in order to handle cases when the user has more elements than grid cells.
      controlData = {
        value: `repeat(${computedStyleLength}, 1fr)`
      };
    }
    controlData = {
      ...controlData,
      length: computedStyleLength
    };
    return controlData;
  }
  getComputedStyle(container, property) {
    return window?.getComputedStyle(container, null).getPropertyValue(property);
  }
  onElementChange(propertyName) {
    if (this.isControlThatMayAffectEmptyViewHeight(propertyName)) {
      this.updateEmptyViewHeight();
    }
    let propsThatTriggerGridLayoutRender = ['grid_rows_grid', 'grid_columns_grid', 'grid_gaps', 'container_type', 'boxed_width', 'content_width', 'width', 'height', 'min_height', 'padding', 'grid_auto_flow'];

    // Add responsive control names to the list of controls that trigger re-rendering.
    propsThatTriggerGridLayoutRender = this.getResponsiveControlNames(propsThatTriggerGridLayoutRender);
    if (propsThatTriggerGridLayoutRender.includes(propertyName)) {
      this.initLayoutOverlay();
    }
  }
  isControlThatMayAffectEmptyViewHeight(propertyName) {
    return 0 === propertyName.indexOf('grid_rows_grid') || 0 === propertyName.indexOf('grid_columns_grid') || 0 === propertyName.indexOf('grid_auto_flow');
  }

  /**
   * GetResponsiveControlNames
   * Add responsive control names to the list of controls that trigger re-rendering.
   *
   * @param {Array} propsThatTriggerGridLayoutRender - array of control names.
   *
   * @return {Array}
   */
  getResponsiveControlNames(propsThatTriggerGridLayoutRender) {
    const activeBreakpoints = elementorFrontend.breakpoints.getActiveBreakpointsList();
    const responsiveControlNames = [];
    for (const prop of propsThatTriggerGridLayoutRender) {
      for (const breakpoint of activeBreakpoints) {
        responsiveControlNames.push(`${prop}_${breakpoint}`);
      }
    }
    responsiveControlNames.push(...propsThatTriggerGridLayoutRender);
    return responsiveControlNames;
  }
  onDeviceModeChange() {
    this.initLayoutOverlay();
  }

  /**
   * Rerender Grid Overlay when child element is added or removed from its parent.
   *
   * @return {void}
   */
  addChildLifeCycleEventListeners() {
    this.lifecycleChangeListener = this.initLayoutOverlay.bind(this);
    window.addEventListener('elementor/editor/element-rendered', this.lifecycleChangeListener);
    window.addEventListener('elementor/editor/element-destroyed', this.lifecycleChangeListener);
  }
  removeChildLifeCycleEventListeners() {
    window.removeEventListener('elementor/editor/element-rendered', this.lifecycleChangeListener);
    window.removeEventListener('elementor/editor/element-destroyed', this.lifecycleChangeListener);
  }
  updateEmptyViewHeight() {
    if (this.shouldUpdateEmptyViewHeight()) {
      const {
          emptyView
        } = this.elements,
        currentDevice = elementor.channels.deviceMode.request('currentMode'),
        elementSettings = this.getElementSettings(),
        gridRows = 'desktop' === currentDevice ? elementSettings.grid_rows_grid : elementSettings.grid_rows_grid + '_' + currentDevice;
      emptyView?.style.removeProperty('min-height');
      if (this.hasCustomUnit(gridRows) && this.isNotOnlyANumber(gridRows) && this.sizeNotEmpty(gridRows)) {
        emptyView.style.minHeight = 'auto';
      }

      // This is to handle cases where `minHeight: auto` computes to `0`.
      if (emptyView?.offsetHeight <= 0) {
        emptyView.style.minHeight = '100px';
      }
    }
  }
  shouldUpdateEmptyViewHeight() {
    return !!this.elements.container.querySelector('.elementor-empty-view');
  }
  hasCustomUnit(gridRows) {
    return 'custom' === gridRows?.unit;
  }
  sizeNotEmpty(gridRows) {
    return '' !== gridRows?.size?.trim();
  }
  isNotOnlyANumber(gridRows) {
    const numberPattern = /^\d+$/;
    return !numberPattern.test(gridRows?.size);
  }
  shouldRemoveEmptyView() {
    const childrenLength = this.elements.outlineParentContainer.querySelectorAll(':scope > .elementor-element').length;
    if (0 === childrenLength) {
      return false;
    }
    const maxElements = this.getMaxElementsNumber();
    return maxElements <= childrenLength && this.isFullFilled(childrenLength);
  }
  isFullFilled(numberOfElements) {
    const gridDimensions = this.getDeviceGridDimensions(),
      {
        grid_auto_flow: gridAutoFlow
      } = this.getElementSettings();
    const flowTypeField = 'row' === gridAutoFlow ? 'columns' : 'rows';
    return 0 === numberOfElements % gridDimensions[flowTypeField].length;
  }
  getMaxOutlineElementsNumber() {
    const childrenLength = this.elements.outlineParentContainer.querySelectorAll(':scope > .elementor-element').length,
      gridDimensions = this.getDeviceGridDimensions(),
      maxElementsBySettings = this.getMaxElementsNumber(),
      {
        grid_auto_flow: gridAutoFlow
      } = this.getElementSettings();
    const flowTypeField = 'row' === gridAutoFlow ? 'columns' : 'rows';
    const maxElementsByItems = Math.ceil(childrenLength / gridDimensions[flowTypeField].length) * gridDimensions[flowTypeField].length;
    return maxElementsBySettings > maxElementsByItems ? maxElementsBySettings : maxElementsByItems;
  }
  getMaxElementsNumber() {
    const elementSettings = this.getElementSettings(),
      device = elementor.channels.deviceMode.request('currentMode'),
      {
        grid_auto_flow: gridAutoFlow
      } = this.getElementSettings(),
      gridDimensions = this.getDeviceGridDimensions();
    if ('row' === gridAutoFlow) {
      const rows = elementorFrontend.utils.controls.getResponsiveControlValue(elementSettings, 'grid_rows_grid', 'size', device);
      const rowsLength = isNaN(rows) ? rows.split(' ').length : rows;
      return gridDimensions.columns.length * rowsLength;
    }
    const columns = elementorFrontend.utils.controls.getResponsiveControlValue(elementSettings, 'grid_columns_grid', 'size', device);
    const columnsLength = isNaN(columns) ? rows.split(' ').length : columns;
    return gridDimensions.rows.length * columnsLength;
  }
}
exports["default"] = GridContainer;

/***/ }),

/***/ "../assets/dev/js/frontend/handlers/container/shapes.js":
/*!**************************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/container/shapes.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
// TODO: Copied from `section/shapes.js`.
class Shapes extends elementorModules.frontend.handlers.Base {
  getDefaultSettings() {
    const contentWidth = this.getElementSettings('content_width'),
      container = 'boxed' === contentWidth ? '> .e-con-inner > .elementor-shape-%s' : '> .elementor-shape-%s';
    return {
      selectors: {
        container
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
//# sourceMappingURL=container-editor-handlers.4366bb0d455036506f1e.bundle.js.map