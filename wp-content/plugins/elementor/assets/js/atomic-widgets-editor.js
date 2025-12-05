/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/editor/elements/views/behaviors/sortable.js":
/*!********************************************************************!*\
  !*** ../assets/dev/js/editor/elements/views/behaviors/sortable.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var SortableBehavior;

/**
 * @typedef {import('../../../container/container')} Container
 */
SortableBehavior = Marionette.Behavior.extend({
  defaults: {
    elChildType: 'widget'
  },
  events: {
    sortstart: 'onSortStart',
    sortreceive: 'onSortReceive',
    sortupdate: 'onSortUpdate',
    sortover: 'onSortOver',
    sortout: 'onSortOut'
  },
  initialize: function initialize() {
    this.listenTo(elementor.channels.dataEditMode, 'switch', this.onEditModeSwitched).listenTo(this.view.options.model, 'request:sort:start', this.startSort).listenTo(this.view.options.model, 'request:sort:update', this.updateSort).listenTo(this.view.options.model, 'request:sort:receive', this.receiveSort);
  },
  onEditModeSwitched: function onEditModeSwitched(activeMode) {
    this.onToggleSortMode('edit' === activeMode);
  },
  refresh: function refresh() {
    this.onEditModeSwitched(elementor.channels.dataEditMode.request('activeMode'));
  },
  onRender: function onRender() {
    var _this = this;
    this.view.collection.on('update', function () {
      return _this.refresh();
    });
    _.defer(function () {
      return _this.refresh();
    });
  },
  onDestroy: function onDestroy() {
    this.deactivate();
  },
  /**
   * Create an item placeholder in order to avoid UI jumps due to flex.
   *
   * @param {Object}  $element  - jQuery element instance to create placeholder for.
   * @param {string}  className - Placeholder class.
   * @param {boolean} hide      - Whether to hide the original element.
   *
   * @return {void}
   */
  createPlaceholder: function createPlaceholder($element) {
    var className = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
    var hide = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
    // Get the actual item size.
    $element.css('display', '');
    var _$element$ = $element[0],
      width = _$element$.clientWidth,
      height = _$element$.clientHeight;
    if (hide) {
      $element.css('display', 'none');
    }
    jQuery('<div />').css(_objectSpread(_objectSpread({}, $element.css(['flex-basis', 'flex-grow', 'flex-shrink', 'position'])), {}, {
      width: width,
      height: height
    })).addClass(className).insertAfter($element);
  },
  /**
   * Return a settings object for jQuery UI sortable to make it swappable.
   *
   * @return {{stop: Function, start: Function}} options
   */
  getSwappableOptions: function getSwappableOptions() {
    var _this2 = this;
    var $childViewContainer = this.getChildViewContainer(),
      placeholderClass = 'e-swappable--item-placeholder';
    return {
      start: function start(event, ui) {
        $childViewContainer.sortable('refreshPositions');

        // TODO: Find a better solution than this hack.
        // Used in order to prevent dragging a container into itself.
        _this2.createPlaceholder(ui.item, placeholderClass);
      },
      stop: function stop() {
        // Cleanup.
        $childViewContainer.find(".".concat(placeholderClass)).remove();
      }
    };
  },
  onToggleSortMode: function onToggleSortMode(isActive) {
    if (isActive) {
      this.activate();
    } else {
      this.deactivate();
    }
  },
  applySortable: function applySortable() {
    if (!elementor.userCan('design')) {
      return;
    }
    var $childViewContainer = this.getChildViewContainer(),
      defaultSortableOptions = {
        placeholder: 'elementor-sortable-placeholder elementor-' + this.getOption('elChildType') + '-placeholder',
        cursorAt: {
          top: 20,
          left: 25
        },
        helper: this._getSortableHelper.bind(this),
        cancel: 'input, textarea, button, select, option, .elementor-inline-editing, .elementor-tab-title',
        // Fix: Sortable - Unable to drag and drop sections with huge height.
        start: function start() {
          $childViewContainer.sortable('refreshPositions');
        }
      };
    var sortableOptions = _.extend(defaultSortableOptions, this.view.getSortableOptions());

    // Add a swappable behavior (used for flex containers).
    if (this.isSwappable()) {
      $childViewContainer.addClass('e-swappable');
      sortableOptions = _.extend(sortableOptions, this.getSwappableOptions());
    }

    // TODO: Temporary hack for Container.
    //  Will be removed in the future when the Navigator will use React.
    if (sortableOptions.preventInit) {
      return;
    }
    $childViewContainer.sortable(sortableOptions);
  },
  /**
   * Enable sorting for this element, and generate sortable instance for it unless already generated.
   */
  activate: function activate() {
    if (!this.getChildViewContainer().sortable('instance')) {
      // Generate sortable instance for this element. Since fresh instances of sortable already allowing sorting,
      // we can return.
      this.applySortable();
      return;
    }
    this.getChildViewContainer().sortable('enable');
  },
  _getSortableHelper: function _getSortableHelper(event, $item) {
    var model = this.view.collection.get({
      cid: $item.data('model-cid')
    });
    return '<div style="height: 84px; width: 125px;" class="elementor-sortable-helper elementor-sortable-helper-' + model.get('elType') + '"><div class="icon"><i class="' + model.getIcon() + '"></i></div><div class="title-wrapper"><div class="title">' + model.getTitle() + '</div></div></div>';
  },
  getChildViewContainer: function getChildViewContainer() {
    return this.view.getChildViewContainer(this.view);
  },
  // The natural widget index in the column is wrong, since there are other elements
  // at the beginning of the column (background-overlay, element-overlay, resizeable-handle)
  getSortedElementNewIndex: function getSortedElementNewIndex($element) {
    var widgets = Object.values($element.parent().find('> .elementor-element'));
    return widgets.indexOf($element[0]);
  },
  /**
   * Disable sorting of the element unless no sortable instance exists, in which case there is already no option to
   * sort.
   */
  deactivate: function deactivate() {
    var childViewContainer = this.getChildViewContainer();
    if (childViewContainer.sortable('instance')) {
      childViewContainer.sortable('disable');
    }
  },
  /**
   * Determine if the current instance of Sortable is swappable.
   *
   * @return {boolean} is swappable
   */
  isSwappable: function isSwappable() {
    return !!this.view.getSortableOptions().swappable;
  },
  startSort: function startSort(event, ui) {
    event.stopPropagation();
    var container = elementor.getContainer(ui.item.attr('data-id'));
    elementor.channels.data.reply('dragging:model', container.model).reply('dragging:view', container.view).reply('dragging:parent:view', this.view).trigger('drag:start', container.model).trigger(container.model.get('elType') + ':drag:start');
  },
  // On sorting element
  updateSort: function updateSort(ui, newIndex) {
    if (undefined === newIndex) {
      newIndex = ui.item.index();
    }
    var child = elementor.channels.data.request('dragging:view').getContainer();
    var result = this.moveChild(child, newIndex);
    if (!result) {
      jQuery(ui.sender).sortable('cancel');
    }
  },
  // On receiving element from another container
  receiveSort: function receiveSort(event, ui, newIndex) {
    event.stopPropagation();
    if (this.view.isCollectionFilled()) {
      jQuery(ui.sender).sortable('cancel');
      return;
    }
    var model = elementor.channels.data.request('dragging:model'),
      draggedElType = model.get('elType'),
      draggedIsInnerSection = 'section' === draggedElType && model.get('isInner'),
      targetIsInnerColumn = 'column' === this.view.getElementType() && this.view.isInner();
    if (draggedIsInnerSection && targetIsInnerColumn) {
      jQuery(ui.sender).sortable('cancel');
      return;
    }
    if (undefined === newIndex) {
      newIndex = ui.item.index();
    }
    var child = elementor.channels.data.request('dragging:view').getContainer();
    var result = this.moveChild(child, newIndex);
    if (!result) {
      jQuery(ui.sender).sortable('cancel');
    }
  },
  onSortStart: function onSortStart(event, ui) {
    if ('column' === this.options.elChildType) {
      var uiData = ui.item.data('sortableItem'),
        uiItems = uiData.items,
        itemHeight = 0;
      uiItems.forEach(function (item) {
        if (item.item[0] === ui.item[0]) {
          itemHeight = item.height;
          return false;
        }
      });
      ui.placeholder.height(itemHeight);
    }
    this.startSort(event, ui);
  },
  onSortOver: function onSortOver(event) {
    event.stopPropagation();
    var model = elementor.channels.data.request('dragging:model');
    jQuery(event.target).addClass('elementor-draggable-over').attr({
      'data-dragged-element': model.get('elType'),
      'data-dragged-is-inner': model.get('isInner')
    });
    this.$el.addClass('elementor-dragging-on-child');
  },
  onSortOut: function onSortOut(event) {
    event.stopPropagation();
    jQuery(event.target).removeClass('elementor-draggable-over').removeAttr('data-dragged-element data-dragged-is-inner');
    this.$el.removeClass('elementor-dragging-on-child');
  },
  onSortReceive: function onSortReceive(event, ui) {
    this.receiveSort(event, ui, this.getSortedElementNewIndex(ui.item));
  },
  onSortUpdate: function onSortUpdate(event, ui) {
    event.stopPropagation();
    if (this.getChildViewContainer()[0] !== ui.item.parent()[0]) {
      return;
    }
    this.updateSort(ui, this.getSortedElementNewIndex(ui.item));
  },
  onAddChild: function onAddChild(view) {
    view.$el.attr('data-model-cid', view.model.cid);
  },
  /**
   * Move a child container to another position.
   *
   * @param {Container}     child - The child container to move.
   * @param {number|string} index - New index.
   *
   * @return {Container|boolean}
   */
  moveChild: function moveChild(child, index) {
    return $e.run('document/elements/move', {
      container: child,
      target: this.view.getContainer(),
      options: {
        at: index
      }
    });
  }
});
module.exports = SortableBehavior;

/***/ }),

/***/ "../assets/dev/js/editor/elements/views/container/empty-component.js":
/*!***************************************************************************!*\
  !*** ../assets/dev/js/editor/elements/views/container/empty-component.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = EmptyComponent;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */
function EmptyComponent() {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "elementor-first-add"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "elementor-icon eicon-plus",
    onClick: function onClick() {
      return $e.route('panel/elements/categories');
    }
  }));
}

/***/ }),

/***/ "../assets/dev/js/editor/utils/element-types.js":
/*!******************************************************!*\
  !*** ../assets/dev/js/editor/utils/element-types.js ***!
  \******************************************************/
/***/ ((module) => {

"use strict";


/**
 * Returns an array of all available element types.
 *
 * @return {string[]} Array of element type strings.
 */
var getAllElementTypes = function getAllElementTypes() {
  return Object.keys(elementor.getConfig().elements);
};
module.exports = {
  getAllElementTypes: getAllElementTypes
};

/***/ }),

/***/ "../assets/dev/js/utils/react.js":
/*!***************************************!*\
  !*** ../assets/dev/js/utils/react.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var ReactDOM = _interopRequireWildcard(__webpack_require__(/*! react-dom */ "react-dom"));
var _client = __webpack_require__(/*! react-dom/client */ "../node_modules/react-dom/client.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/**
 * Support conditional rendering of a React App to the DOM, based on the React version.
 * We use `createRoot` when available, but fallback to `ReactDOM.render` for older versions.
 *
 * @param { React.ReactElement } app        The app to render.
 * @param { HTMLElement }        domElement The DOM element to render the app into.
 *
 * @return {{ unmount: () => void }} The unmount function.
 */
function render(app, domElement) {
  var unmountFunction;
  try {
    var root = (0, _client.createRoot)(domElement);
    root.render(app);
    unmountFunction = function unmountFunction() {
      root.unmount();
    };
  } catch (e) {
    // eslint-disable-next-line react/no-deprecated
    ReactDOM.render(app, domElement);
    unmountFunction = function unmountFunction() {
      // eslint-disable-next-line react/no-deprecated
      ReactDOM.unmountComponentAtNode(domElement);
    };
  }
  return {
    unmount: unmountFunction
  };
}
var _default = exports["default"] = {
  render: render
};

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-base-model.js":
/*!*******************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-base-model.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var AtomicElementBaseModel = exports["default"] = /*#__PURE__*/function (_elementor$modules$el) {
  function AtomicElementBaseModel() {
    (0, _classCallCheck2.default)(this, AtomicElementBaseModel);
    return _callSuper(this, AtomicElementBaseModel, arguments);
  }
  (0, _inherits2.default)(AtomicElementBaseModel, _elementor$modules$el);
  return (0, _createClass2.default)(AtomicElementBaseModel, [{
    key: "isValidChild",
    value:
    /**
     * Do not allow section, column or container be placed in the Atomic container.
     *
     * @param {*} childModel
     */
    function isValidChild(childModel) {
      var elType = childModel.get('elType');
      return 'section' !== elType && 'column' !== elType;
    }
  }, {
    key: "initialize",
    value: function initialize(attributes, options) {
      var elementType = this.get('elType');
      this.config = elementor.config.elements[elementType];
      var isNewElementCreate = 0 === this.get('elements').length && $e.commands.currentTrace.includes('document/elements/create');
      if (isNewElementCreate) {
        this.onElementCreate();
      }
      _superPropGet(AtomicElementBaseModel, "initialize", this, 3)([attributes, options]);
    }
  }, {
    key: "getDefaultChildren",
    value: function getDefaultChildren() {
      var defaultChildren = this.config.default_children;
      return defaultChildren;
    }
  }, {
    key: "onElementCreate",
    value: function onElementCreate() {
      var _this = this;
      this.set('elements', this.getDefaultChildren().map(function (element) {
        return _this.buildElement(element);
      }));
    }
  }, {
    key: "buildElement",
    value: function buildElement(element) {
      var _this2 = this;
      var id = elementorCommon.helpers.getUniqueId();
      var elements = (element.elements || []).map(function (el) {
        return _this2.buildElement(el);
      });
      return {
        elType: element.elType,
        widgetType: element.widgetType,
        id: id,
        settings: element.settings || {},
        elements: elements,
        isLocked: element.isLocked || false,
        editor_settings: element.editor_settings || {}
      };
    }
  }]);
}(elementor.modules.elements.models.Element);

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-base-type.js":
/*!******************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-base-type.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var AtomicElementBaseType = exports["default"] = /*#__PURE__*/function (_elementor$modules$el) {
  function AtomicElementBaseType(elementType, viewClass) {
    var _this;
    var modelClass = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var emptyViewClass = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    (0, _classCallCheck2.default)(this, AtomicElementBaseType);
    _this = _callSuper(this, AtomicElementBaseType);
    _this.elementType = elementType;
    _this.viewClass = viewClass;
    _this.modelClass = modelClass;
    _this.emptyViewClass = emptyViewClass;
    return _this;
  }
  (0, _inherits2.default)(AtomicElementBaseType, _elementor$modules$el);
  return (0, _createClass2.default)(AtomicElementBaseType, [{
    key: "getType",
    value: function getType() {
      return this.elementType;
    }
  }, {
    key: "getView",
    value: function getView() {
      return this.viewClass;
    }
  }, {
    key: "getEmptyView",
    value: function getEmptyView() {
      return this.emptyViewClass || elementor.modules.elements.views.EmptyComponent;
    }
  }, {
    key: "getModel",
    value: function getModel() {
      return this.modelClass || elementor.modules.elements.models.AtomicElementBase;
    }
  }]);
}(elementor.modules.elements.types.Base);

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab-panel/create-atomic-tab-panel-type.js":
/*!************************************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab-panel/create-atomic-tab-panel-type.js ***!
  \************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createAtomicTabPanelView = _interopRequireDefault(__webpack_require__(/*! ./create-atomic-tab-panel-view */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab-panel/create-atomic-tab-panel-view.js"));
var createAtomicTabPanelType = function createAtomicTabPanelType() {
  return new elementor.modules.elements.types.AtomicElementBase('e-tab-panel', (0, _createAtomicTabPanelView.default)());
};
var _default = exports["default"] = createAtomicTabPanelType;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab-panel/create-atomic-tab-panel-view.js":
/*!************************************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab-panel/create-atomic-tab-panel-view.js ***!
  \************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var createAtomicTabPanelView = function createAtomicTabPanelView() {
  var AtomicElementBaseView = elementor.modules.elements.views.createAtomicElementBase('e-tab-panel');
  return /*#__PURE__*/function (_AtomicElementBaseVie) {
    function AtomicTabPanelView() {
      (0, _classCallCheck2.default)(this, AtomicTabPanelView);
      return _callSuper(this, AtomicTabPanelView, arguments);
    }
    (0, _inherits2.default)(AtomicTabPanelView, _AtomicElementBaseVie);
    return (0, _createClass2.default)(AtomicTabPanelView, [{
      key: "attributes",
      value: function attributes() {
        var tabId = this.model.getSetting('tab-id');
        return tabId !== null && tabId !== void 0 && tabId.value ? _objectSpread({
          'data-tab-id': tabId.value,
          'aria-labelledby': tabId.value
        }, _superPropGet(AtomicTabPanelView, "attributes", this, 3)([])) : _superPropGet(AtomicTabPanelView, "attributes", this, 3)([]);
      }
    }]);
  }(AtomicElementBaseView);
};
var _default = exports["default"] = createAtomicTabPanelView;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab/create-atomic-tab-type.js":
/*!************************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab/create-atomic-tab-type.js ***!
  \************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createAtomicTabView = _interopRequireDefault(__webpack_require__(/*! ./create-atomic-tab-view */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab/create-atomic-tab-view.js"));
var createAtomicTabType = function createAtomicTabType() {
  return new elementor.modules.elements.types.AtomicElementBase('e-tab', (0, _createAtomicTabView.default)());
};
var _default = exports["default"] = createAtomicTabType;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab/create-atomic-tab-view.js":
/*!************************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab/create-atomic-tab-view.js ***!
  \************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var createAtomicTabView = function createAtomicTabView() {
  var atomicElementBaseView = elementor.modules.elements.views.createAtomicElementBase('e-tab');
  return /*#__PURE__*/function (_atomicElementBaseVie) {
    function AtomicTabView() {
      (0, _classCallCheck2.default)(this, AtomicTabView);
      return _callSuper(this, AtomicTabView, arguments);
    }
    (0, _inherits2.default)(AtomicTabView, _atomicElementBaseVie);
    return (0, _createClass2.default)(AtomicTabView, [{
      key: "attributes",
      value: function attributes() {
        var tabPanelId = this.model.getSetting('tab-panel-id');
        return tabPanelId !== null && tabPanelId !== void 0 && tabPanelId.value ? _objectSpread({
          'aria-controls': tabPanelId.value
        }, _superPropGet(AtomicTabView, "attributes", this, 3)([])) : _superPropGet(AtomicTabView, "attributes", this, 3)([]);
      }
    }]);
  }(atomicElementBaseView);
};
var _default = exports["default"] = createAtomicTabView;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-model.js":
/*!***************************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-model.js ***!
  \***************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var createAtomicTabsModel = function createAtomicTabsModel() {
  var AtomicElementBaseModel = elementor.modules.elements.models.AtomicElementBase;
  return /*#__PURE__*/function (_AtomicElementBaseMod) {
    function AtomicTabsModel() {
      (0, _classCallCheck2.default)(this, AtomicTabsModel);
      return _callSuper(this, AtomicTabsModel, arguments);
    }
    (0, _inherits2.default)(AtomicTabsModel, _AtomicElementBaseMod);
    return (0, _createClass2.default)(AtomicTabsModel, [{
      key: "onElementCreate",
      value: function onElementCreate() {
        _superPropGet(AtomicTabsModel, "onElementCreate", this, 3)([]);
        var tabs = this.getChildrenByType(this.get('elements'), 'e-tab');
        var tabPanels = this.getChildrenByType(this.get('elements'), 'e-tab-panel');
        var currentSettings = this.get('settings') || {};
        currentSettings['default-active-tab'] = {
          $$type: 'string',
          value: tabs[0].id
        };
        this.set('settings', currentSettings);

        // TODO: maybe move this part to a dedicated "afterDefaultChildrenSet" method
        tabs.forEach(function (tab, index) {
          tab.settings._cssid = {
            $$type: 'string',
            value: tab.id
          };
          tab.settings['tab-panel-id'] = {
            $$type: 'string',
            value: tabPanels[index].id
          };
          var tabPanel = tabPanels[index];
          tabPanel.settings._cssid = {
            $$type: 'string',
            value: tabPanels[index].id
          };
          tabPanel.settings['tab-id'] = {
            $$type: 'string',
            value: tab.id
          };
        });
      }
    }, {
      key: "getChildrenByType",
      value: function getChildrenByType(elements, type) {
        var foundElements = [];
        var _searchRecursively = function searchRecursively(collection) {
          collection.forEach(function (element) {
            if (type === element.elType) {
              foundElements.push(element);
            }
            var childElements = element.elements;
            if (childElements && childElements.length > 0) {
              _searchRecursively(childElements);
            }
          });
        };
        _searchRecursively(elements);
        return foundElements;
      }
    }]);
  }(AtomicElementBaseModel);
};
var _default = exports["default"] = createAtomicTabsModel;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-type.js":
/*!**************************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-type.js ***!
  \**************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _createAtomicTabsView = _interopRequireDefault(__webpack_require__(/*! ./create-atomic-tabs-view */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-view.js"));
var _createAtomicTabsModel = _interopRequireDefault(__webpack_require__(/*! ./create-atomic-tabs-model */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-model.js"));
var createAtomicTabsType = function createAtomicTabsType() {
  return new elementor.modules.elements.types.AtomicElementBase('e-tabs', (0, _createAtomicTabsView.default)(), (0, _createAtomicTabsModel.default)());
};
var _default = exports["default"] = createAtomicTabsType;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-view.js":
/*!**************************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-view.js ***!
  \**************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var createAtomicTabsView = function createAtomicTabsView() {
  var AtomicElementBaseView = elementor.modules.elements.views.createAtomicElementBase('e-tabs');
  return /*#__PURE__*/function (_AtomicElementBaseVie) {
    function AtomicTabsView() {
      (0, _classCallCheck2.default)(this, AtomicTabsView);
      return _callSuper(this, AtomicTabsView, arguments);
    }
    (0, _inherits2.default)(AtomicTabsView, _AtomicElementBaseVie);
    return (0, _createClass2.default)(AtomicTabsView, [{
      key: "attributes",
      value: function attributes() {
        var defaultActiveTab = this.model.getSetting('default-active-tab');
        return defaultActiveTab !== null && defaultActiveTab !== void 0 && defaultActiveTab.value ? _objectSpread({
          'data-active-tab': defaultActiveTab.value
        }, _superPropGet(AtomicTabsView, "attributes", this, 3)([])) : _superPropGet(AtomicTabsView, "attributes", this, 3)([]);
      }
    }]);
  }(AtomicElementBaseView);
};
var _default = exports["default"] = createAtomicTabsView;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-atomic-tabs-content-type.js":
/*!**********************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-atomic-tabs-content-type.js ***!
  \**********************************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var createAtomicTabsContentType = function createAtomicTabsContentType() {
  var AtomicTabsContentView = elementor.modules.elements.views.createAtomicElementBase('e-tabs-content');
  return new elementor.modules.elements.types.AtomicElementBase('e-tabs-content', AtomicTabsContentView);
};
var _default = exports["default"] = createAtomicTabsContentType;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-atomic-tabs-list-type.js":
/*!*******************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-atomic-tabs-list-type.js ***!
  \*******************************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var createAtomicTabsListType = function createAtomicTabsListType() {
  var AtomicTabsListView = elementor.modules.elements.views.createAtomicElementBase('e-tabs-list');
  return new elementor.modules.elements.types.AtomicElementBase('e-tabs-list', AtomicTabsListView);
};
var _default = exports["default"] = createAtomicTabsListType;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-div-block-type.js":
/*!************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-div-block-type.js ***!
  \************************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var createDivBlockType = function createDivBlockType() {
  var DivBlockView = elementor.modules.elements.views.createAtomicElementBase('e-div-block');
  return new elementor.modules.elements.types.AtomicElementBase('e-div-block', DivBlockView);
};
var _default = exports["default"] = createDivBlockType;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-flexbox-type.js":
/*!**********************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-flexbox-type.js ***!
  \**********************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var createFlexboxType = function createFlexboxType() {
  var FlexboxView = elementor.modules.elements.views.createAtomicElementBase('e-flexbox');
  return new elementor.modules.elements.types.AtomicElementBase('e-flexbox', FlexboxView);
};
var _default = exports["default"] = createFlexboxType;

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/component.js":
/*!***************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/component.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var hooks = _interopRequireWildcard(__webpack_require__(/*! ./hooks */ "../modules/atomic-widgets/assets/js/editor/hooks/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Component = exports["default"] = /*#__PURE__*/function (_$e$modules$Component) {
  function Component() {
    (0, _classCallCheck2.default)(this, Component);
    return _callSuper(this, Component, arguments);
  }
  (0, _inherits2.default)(Component, _$e$modules$Component);
  return (0, _createClass2.default)(Component, [{
    key: "getNamespace",
    value: function getNamespace() {
      return 'document/atomic-widgets';
    }
  }, {
    key: "defaultHooks",
    value: function defaultHooks() {
      return this.importHooks(hooks);
    }
  }]);
}($e.modules.ComponentBase);

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/container/atomic-element-empty-view.js":
/*!*****************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/container/atomic-element-empty-view.js ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _react2 = _interopRequireDefault(__webpack_require__(/*! elementor-utils/react */ "../assets/dev/js/utils/react.js"));
var _emptyComponent = _interopRequireDefault(__webpack_require__(/*! elementor-elements/views/container/empty-component */ "../assets/dev/js/editor/elements/views/container/empty-component.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var AtomicElementEmptyView = exports["default"] = /*#__PURE__*/function (_Marionette$ItemView) {
  function AtomicElementEmptyView() {
    var _this;
    (0, _classCallCheck2.default)(this, AtomicElementEmptyView);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, AtomicElementEmptyView, [].concat(args));
    (0, _defineProperty2.default)(_this, "template", '<div></div>');
    (0, _defineProperty2.default)(_this, "className", 'elementor-empty-view');
    return _this;
  }
  (0, _inherits2.default)(AtomicElementEmptyView, _Marionette$ItemView);
  return (0, _createClass2.default)(AtomicElementEmptyView, [{
    key: "renderReactDefaultElement",
    value: function renderReactDefaultElement(container) {
      var _ReactUtils$render = _react2.default.render(/*#__PURE__*/_react.default.createElement(_emptyComponent.default, {
          container: container
        }), this.el),
        unmount = _ReactUtils$render.unmount;
      this.unmount = unmount;
    }
  }, {
    key: "onRender",
    value: function onRender() {
      this.$el.addClass(this.className);
      this.renderReactDefaultElement();
    }
  }, {
    key: "onDestroy",
    value: function onDestroy() {
      this.unmount();
    }
  }]);
}(Marionette.ItemView);

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/create-atomic-element-base-view.js":
/*!*************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/create-atomic-element-base-view.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var sprintf = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["sprintf"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = createAtomicElementBaseView;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _atomicElementEmptyView = _interopRequireDefault(__webpack_require__(/*! ./container/atomic-element-empty-view */ "../modules/atomic-widgets/assets/js/editor/container/atomic-element-empty-view.js"));
var _elementTypes = __webpack_require__(/*! elementor-editor/utils/element-types */ "../assets/dev/js/editor/utils/element-types.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var BaseElementView = elementor.modules.elements.views.BaseElement;
function createAtomicElementBaseView(type) {
  var AtomicElementView = BaseElementView.extend({
    template: Marionette.TemplateCache.get("#tmpl-elementor-".concat(type, "-content")),
    emptyView: _atomicElementEmptyView.default,
    tagName: function tagName() {
      if (this.haveLink()) {
        return 'a';
      }
      var tagControl = this.model.getSetting('tag');
      var tagControlValue = (tagControl === null || tagControl === void 0 ? void 0 : tagControl.value) || tagControl;
      var defaultTag = this.model.config.default_html_tag;
      return tagControlValue || defaultTag;
    },
    getChildViewContainer: function getChildViewContainer() {
      this.childViewContainer = '';
      return Marionette.CompositeView.prototype.getChildViewContainer.apply(this, arguments);
    },
    getChildType: function getChildType() {
      var atomicElements = Object.entries(elementor.config.elements).filter(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          element = _ref2[1];
        return !!(element !== null && element !== void 0 && element.atomic_props_schema);
      }).map(function (_ref3) {
        var _ref4 = (0, _slicedToArray2.default)(_ref3, 1),
          elType = _ref4[0];
        return elType;
      });
      return ['widget', 'container'].concat((0, _toConsumableArray2.default)(atomicElements));
    },
    className: function className() {
      return "".concat(BaseElementView.prototype.className.apply(this), " e-con e-atomic-element ").concat(this.getClassString());
    },
    // TODO: Copied from `views/column.js`.
    ui: function ui() {
      var ui = BaseElementView.prototype.ui.apply(this, arguments);
      ui.percentsTooltip = '> .elementor-element-overlay .elementor-column-percents-tooltip';
      return ui;
    },
    attributes: function attributes() {
      var _this$model$getSettin, _this$model$getSettin2, _this$model$config$in, _this$model;
      var attr = BaseElementView.prototype.attributes.apply(this);
      var local = {};
      var cssId = this.model.getSetting('_cssid');
      var customAttributes = (_this$model$getSettin = (_this$model$getSettin2 = this.model.getSetting('attributes')) === null || _this$model$getSettin2 === void 0 ? void 0 : _this$model$getSettin2.value) !== null && _this$model$getSettin !== void 0 ? _this$model$getSettin : [];
      var initialAttributes = (_this$model$config$in = this === null || this === void 0 || (_this$model = this.model) === null || _this$model === void 0 || (_this$model = _this$model.config) === null || _this$model === void 0 ? void 0 : _this$model.initial_attributes) !== null && _this$model$config$in !== void 0 ? _this$model$config$in : {};
      if (cssId) {
        local.id = cssId.value;
      }
      var href = this.getHref();
      if (href) {
        local.href = href;
      }
      customAttributes.forEach(function (attribute) {
        var _attribute$value, _attribute$value2;
        var key = (_attribute$value = attribute.value) === null || _attribute$value === void 0 || (_attribute$value = _attribute$value.key) === null || _attribute$value === void 0 ? void 0 : _attribute$value.value;
        var value = (_attribute$value2 = attribute.value) === null || _attribute$value2 === void 0 || (_attribute$value2 = _attribute$value2.value) === null || _attribute$value2 === void 0 ? void 0 : _attribute$value2.value;
        if (key && value) {
          local[key] = value;
        }
      });
      return _objectSpread(_objectSpread(_objectSpread({}, attr), initialAttributes), local);
    },
    // TODO: Copied from `views/column.js`.
    attachElContent: function attachElContent() {
      BaseElementView.prototype.attachElContent.apply(this, arguments);
      var $tooltip = jQuery('<div>', {
        class: 'elementor-column-percents-tooltip',
        'data-side': elementorCommon.config.isRTL ? 'right' : 'left'
      });
      this.$el.children('.elementor-element-overlay').append($tooltip);
    },
    // TODO: Copied from `views/column.js`.
    getPercentSize: function getPercentSize(size) {
      if (!size) {
        size = this.el.getBoundingClientRect().width;
      }
      return +(size / this.$el.parent().width() * 100).toFixed(3);
    },
    // TODO: Copied from `views/column.js`.
    getPercentsForDisplay: function getPercentsForDisplay() {
      var width = +this.model.getSetting('width') || this.getPercentSize();
      return width.toFixed(1) + '%';
    },
    renderOnChange: function renderOnChange(settings) {
      var _this = this;
      var changed = settings.changedAttributes();
      setTimeout(function () {
        _this.updateHandlesPosition();
      });
      if (!changed) {
        return;
      }
      BaseElementView.prototype.renderOnChange.apply(this, settings);
      if (changed.attributes) {
        var _this$model$getSettin3;
        var preserveAttrs = ['id', 'class', 'href'];
        var $elAttrs = this.$el[0].attributes;
        for (var i = $elAttrs.length - 1; i >= 0; i--) {
          var attrName = $elAttrs[i].name;
          if (!preserveAttrs.includes(attrName)) {
            this.$el.removeAttr(attrName);
          }
        }
        var attrs = ((_this$model$getSettin3 = this.model.getSetting('attributes')) === null || _this$model$getSettin3 === void 0 ? void 0 : _this$model$getSettin3.value) || [];
        attrs.forEach(function (attribute) {
          var _attribute$value3, _attribute$value4;
          var key = attribute === null || attribute === void 0 || (_attribute$value3 = attribute.value) === null || _attribute$value3 === void 0 || (_attribute$value3 = _attribute$value3.key) === null || _attribute$value3 === void 0 ? void 0 : _attribute$value3.value;
          var value = attribute === null || attribute === void 0 || (_attribute$value4 = attribute.value) === null || _attribute$value4 === void 0 || (_attribute$value4 = _attribute$value4.value) === null || _attribute$value4 === void 0 ? void 0 : _attribute$value4.value;
          if (key && value) {
            _this.$el.attr(key, value);
          }
        });
        return;
      }
      if (changed.classes) {
        this.$el.attr('class', this.className());
        return;
      }
      if (changed._cssid) {
        if (changed._cssid.value) {
          this.$el.attr('id', changed._cssid.value);
        } else {
          this.$el.removeAttr('id');
        }
        return;
      }
      this.$el.addClass(this.getClasses());
      if (this.isTagChanged(changed)) {
        this.rerenderEntireView();
      }
    },
    isTagChanged: function isTagChanged(changed) {
      return ((changed === null || changed === void 0 ? void 0 : changed.tag) !== undefined || (changed === null || changed === void 0 ? void 0 : changed.link) !== undefined) && this._parent && this.tagName() !== this.el.tagName;
    },
    rerenderEntireView: function rerenderEntireView() {
      var parent = this._parent;
      this._parent.removeChildView(this);
      parent.addChild(this.model, AtomicElementView, this._index);
    },
    onRender: function onRender() {
      var _this2 = this;
      this.dispatchPreviewEvent('elementor/element/render');
      BaseElementView.prototype.onRender.apply(this, arguments);

      // Defer to wait for everything to render.
      setTimeout(function () {
        _this2.droppableInitialize();
        _this2.updateHandlesPosition();
      });
    },
    onDestroy: function onDestroy() {
      BaseElementView.prototype.onDestroy.apply(this, arguments);
      this.dispatchPreviewEvent('elementor/element/destroy');
    },
    dispatchPreviewEvent: function dispatchPreviewEvent(eventType) {
      var _elementor;
      (_elementor = elementor) === null || _elementor === void 0 || (_elementor = _elementor.$preview) === null || _elementor === void 0 || (_elementor = _elementor[0]) === null || _elementor === void 0 || _elementor.contentWindow.dispatchEvent(new CustomEvent(eventType, {
        detail: {
          id: this.model.get('id'),
          type: this.model.get('elType'),
          element: this.getDomElement().get(0)
        }
      }));
    },
    haveLink: function haveLink() {
      var _this$model$getSettin4;
      return !!((_this$model$getSettin4 = this.model.getSetting('link')) !== null && _this$model$getSettin4 !== void 0 && (_this$model$getSettin4 = _this$model$getSettin4.value) !== null && _this$model$getSettin4 !== void 0 && (_this$model$getSettin4 = _this$model$getSettin4.destination) !== null && _this$model$getSettin4 !== void 0 && _this$model$getSettin4.value);
    },
    getHref: function getHref() {
      if (!this.haveLink()) {
        return;
      }
      var _this$model$getSettin5 = this.model.getSetting('link').value.destination,
        $$type = _this$model$getSettin5.$$type,
        value = _this$model$getSettin5.value;
      var isPostId = 'number' === $$type;
      var hrefPrefix = isPostId ? elementor.config.home_url + '/?p=' : '';
      return hrefPrefix + value;
    },
    droppableInitialize: function droppableInitialize() {
      this.$el.html5Droppable(this.getDroppableOptions());
    },
    /**
     * Add a `Save as a Template` button to the context menu.
     *
     * @return {Object} groups
     */
    getContextMenuGroups: function getContextMenuGroups() {
      var _this3 = this,
        _elementorCommon$conf;
      var saveActions = [{
        name: 'save',
        title: __('Save as a template', 'elementor'),
        shortcut: "<span class=\"elementor-context-menu-list__item__shortcut__new-badge\">".concat(__('New', 'elementor'), "</span>"),
        callback: this.saveAsTemplate.bind(this),
        isEnabled: function isEnabled() {
          return !_this3.getContainer().isLocked();
        }
      }];
      if ((_elementorCommon$conf = elementorCommon.config.experimentalFeatures) !== null && _elementorCommon$conf !== void 0 && _elementorCommon$conf.e_components) {
        saveActions.unshift({
          name: 'save-component',
          title: __('Save as a component', 'elementor'),
          shortcut: "<span class=\"elementor-context-menu-list__item__shortcut__new-badge\">".concat(__('New', 'elementor'), "</span>"),
          callback: this.saveAsComponent.bind(this),
          isEnabled: function isEnabled() {
            return !_this3.getContainer().isLocked();
          }
        });
      }
      var groups = BaseElementView.prototype.getContextMenuGroups.apply(this, arguments),
        transferGroupClipboardIndex = groups.indexOf(_.findWhere(groups, {
          name: 'clipboard'
        }));
      groups.splice(transferGroupClipboardIndex + 1, 0, {
        name: 'save',
        actions: saveActions
      });
      return groups;
    },
    saveAsTemplate: function saveAsTemplate() {
      elementor.templates.eventManager.sendNewSaveTemplateClickedEvent();
      $e.route('library/save-template', {
        model: this.model
      });
    },
    saveAsComponent: function saveAsComponent(openContextMenuEvent) {
      // Calculate the absolute position where the context menu was opened.
      var openMenuOriginalEvent = openContextMenuEvent.originalEvent;
      var iframeRect = elementor.$preview[0].getBoundingClientRect();
      var anchorPosition = {
        left: openMenuOriginalEvent.clientX + iframeRect.left,
        top: openMenuOriginalEvent.clientY + iframeRect.top
      };
      window.dispatchEvent(new CustomEvent('elementor/editor/open-save-as-component-form', {
        detail: {
          element: elementor.getContainer(this.model.id),
          anchorPosition: anchorPosition
        }
      }));
    },
    isDroppingAllowed: function isDroppingAllowed() {
      return true;
    },
    behaviors: function behaviors() {
      var behaviors = BaseElementView.prototype.behaviors.apply(this, arguments);
      _.extend(behaviors, {
        Sortable: {
          behaviorClass: __webpack_require__(/*! elementor-behaviors/sortable */ "../assets/dev/js/editor/elements/views/behaviors/sortable.js"),
          elChildType: 'widget'
        }
      });
      return elementor.hooks.applyFilters("elements/".concat(type, "/behaviors"), behaviors, this);
    },
    /**
     * @return {{}} options
     */
    getSortableOptions: function getSortableOptions() {
      return {
        preventInit: true
      };
    },
    getDroppableOptions: function getDroppableOptions() {
      var _this4 = this;
      var items = '> .elementor-element, > .elementor-empty-view .elementor-first-add';
      return {
        axis: null,
        items: items,
        groups: ['elementor-element'],
        horizontalThreshold: 0,
        isDroppingAllowed: this.isDroppingAllowed.bind(this),
        currentElementClass: 'elementor-html5dnd-current-element',
        placeholderClass: 'elementor-sortable-placeholder elementor-widget-placeholder',
        hasDraggingOnChildClass: 'e-dragging-over',
        getDropContainer: function getDropContainer() {
          return _this4.getContainer();
        },
        onDropping: function onDropping(side, event) {
          event.stopPropagation();

          // Triggering the drag end manually, since it won't fire above the iframe
          elementor.getPreviewView().onPanelElementDragEnd();
          var draggedView = elementor.channels.editor.request('element:dragged'),
            draggedElement = draggedView === null || draggedView === void 0 ? void 0 : draggedView.getContainer().view.el,
            containerElement = event.currentTarget.parentElement,
            elements = Array.from((containerElement === null || containerElement === void 0 ? void 0 : containerElement.querySelectorAll(':scope > .elementor-element')) || []);
          var targetIndex = elements.indexOf(event.currentTarget);
          if (_this4.isPanelElement(draggedView, draggedElement)) {
            var _elementorCommon;
            if (_this4.draggingOnBottomOrRightSide(side) && !_this4.emptyViewIsCurrentlyBeingDraggedOver()) {
              targetIndex++;
            }
            _this4.onDrop(event, {
              at: targetIndex
            });
            if ((_elementorCommon = elementorCommon) !== null && _elementorCommon !== void 0 && (_elementorCommon = _elementorCommon.eventsManager) !== null && _elementorCommon !== void 0 && _elementorCommon.dispatchEvent) {
              var selectedElement = elementor.channels.panelElements.request('element:selected');
              if (selectedElement) {
                var _selectedElement$mode, _selectedElement$mode2, _selectedElement$mode3, _selectedElement$mode4;
                var elType = (_selectedElement$mode = (_selectedElement$mode2 = selectedElement.model) === null || _selectedElement$mode2 === void 0 ? void 0 : _selectedElement$mode2.get('elType')) !== null && _selectedElement$mode !== void 0 ? _selectedElement$mode : '';
                var widgetType = (_selectedElement$mode3 = (_selectedElement$mode4 = selectedElement.model) === null || _selectedElement$mode4 === void 0 ? void 0 : _selectedElement$mode4.get('widgetType')) !== null && _selectedElement$mode3 !== void 0 ? _selectedElement$mode3 : '';
                var elementName = 'widget' === elType ? widgetType : elType;
                elementorCommon.eventsManager.dispatchEvent('add_element', {
                  location: 'editor_panel',
                  element_name: elementName,
                  element_type: elType,
                  widget_type: widgetType
                });
              }
            }
            return;
          }
          if (_this4.isParentElement(draggedView.getContainer().id)) {
            return;
          }
          if (_this4.emptyViewIsCurrentlyBeingDraggedOver()) {
            _this4.moveDroppedItem(draggedView, 0);
            return;
          }
          _this4.moveExistingElement(side, draggedView, containerElement, elements, targetIndex, draggedElement);
        }
      };
    },
    moveExistingElement: function moveExistingElement(side, draggedView, containerElement, elements, targetIndex, draggedElement) {
      var selfIndex = elements.indexOf(draggedElement);
      if (targetIndex === selfIndex) {
        return;
      }
      var dropIndex = this.getDropIndex(containerElement, side, targetIndex, selfIndex);
      this.moveDroppedItem(draggedView, dropIndex);
    },
    isPanelElement: function isPanelElement(draggedView, draggedElement) {
      return !draggedView || !draggedElement;
    },
    isParentElement: function isParentElement(draggedId) {
      var current = this.container;
      while (current) {
        if (current.id === draggedId) {
          return true;
        }
        current = current.parent;
      }
      return false;
    },
    getDropIndex: function getDropIndex(container, side, index, selfIndex) {
      var styles = window.getComputedStyle(container);
      var isFlex = ['flex', 'inline-flex'].includes(styles.display);
      var isFlexReverse = isFlex && ['column-reverse', 'row-reverse'].includes(styles.flexDirection);
      var isRow = isFlex && ['row-reverse', 'row'].includes(styles.flexDirection);
      var isRtl = elementorCommon.config.isRTL;
      var isReverse = isRow ? isFlexReverse !== isRtl : isFlexReverse;

      // The element should be placed BEFORE the current target
      // if is reversed + side is bottom/right OR not is reversed + side is top/left
      if (isReverse === this.draggingOnBottomOrRightSide(side)) {
        if (-1 === selfIndex || selfIndex >= index - 1) {
          return index;
        }
        return index > 0 ? index - 1 : 0;
      }
      if (0 <= selfIndex && selfIndex < index) {
        return index;
      }
      return index + 1;
    },
    moveDroppedItem: function moveDroppedItem(draggedView, dropIndex) {
      // Reset the dragged element cache.
      elementor.channels.editor.reply('element:dragged', null);
      $e.run('document/elements/move', {
        container: draggedView.getContainer(),
        target: this.getContainer(),
        options: {
          at: dropIndex
        }
      });
    },
    getEditButtons: function getEditButtons() {
      var elementData = elementor.getElementData(this.model),
        editTools = {};
      if ($e.components.get('document/elements').utils.allowAddingWidgets()) {
        editTools.add = {
          /* Translators: %s: Element Name. */
          title: sprintf(__('Add %s', 'elementor'), elementData.title),
          icon: 'plus'
        };
        editTools.edit = {
          /* Translators: %s: Element Name. */
          title: sprintf(__('Edit %s', 'elementor'), elementData.title),
          icon: 'handle'
        };
      }
      if (!this.getContainer().isLocked()) {
        if (elementor.getPreferences('edit_buttons') && $e.components.get('document/elements').utils.allowAddingWidgets()) {
          editTools.duplicate = {
            /* Translators: %s: Element Name. */
            title: sprintf(__('Duplicate %s', 'elementor'), elementData.title),
            icon: 'clone'
          };
        }
        editTools.remove = {
          /* Translators: %s: Element Name. */
          title: sprintf(__('Delete %s', 'elementor'), elementData.title),
          icon: 'close'
        };
      }
      return editTools;
    },
    draggingOnBottomOrRightSide: function draggingOnBottomOrRightSide(side) {
      return ['bottom', 'right'].includes(side);
    },
    emptyViewIsCurrentlyBeingDraggedOver: function emptyViewIsCurrentlyBeingDraggedOver() {
      return this.$el.find('> .elementor-empty-view > .elementor-first-add.elementor-html5dnd-current-element').length > 0;
    },
    /**
     * Toggle the `New Section` view when clicking the `add` button in the edit tools.
     *
     * @return {void}
     */
    onAddButtonClick: function onAddButtonClick() {
      if (this.addSectionView && !this.addSectionView.isDestroyed) {
        this.addSectionView.fadeToDeath();
        return;
      }
      var addSectionView = new elementor.modules.elements.components.AddSectionView({
        at: this.model.collection.indexOf(this.model)
      });
      addSectionView.render();
      this.$el.before(addSectionView.$el);
      addSectionView.$el.hide();

      // Delaying the slide down for slow-render browsers (such as FF)
      setTimeout(function () {
        addSectionView.$el.slideDown(null, function () {
          // Remove inline style, for preview mode.
          jQuery(this).css('display', '');
        });
      });
      this.addSectionView = addSectionView;
    },
    getClasses: function getClasses() {
      var _window, _window$get, _this$options;
      var transformer = (_window = window) === null || _window === void 0 || (_window = _window.elementorV2) === null || _window === void 0 || (_window = _window.editorCanvas) === null || _window === void 0 || (_window = _window.settingsTransformersRegistry) === null || _window === void 0 || (_window$get = _window.get) === null || _window$get === void 0 ? void 0 : _window$get.call(_window, 'classes');
      if (!transformer) {
        return [];
      }
      return transformer(((_this$options = this.options) === null || _this$options === void 0 || (_this$options = _this$options.model) === null || _this$options === void 0 || (_this$options = _this$options.getSetting('classes')) === null || _this$options === void 0 ? void 0 : _this$options.value) || []);
    },
    getClassString: function getClassString() {
      var classes = this.getClasses();
      var base = this.getBaseClass();
      return [base].concat((0, _toConsumableArray2.default)(classes)).join(' ');
    },
    getBaseClass: function getBaseClass() {
      var _this$options2, _Object$keys$;
      var baseStyles = elementor.helpers.getAtomicWidgetBaseStyles((_this$options2 = this.options) === null || _this$options2 === void 0 ? void 0 : _this$options2.model);
      return (_Object$keys$ = Object.keys(baseStyles !== null && baseStyles !== void 0 ? baseStyles : {})[0]) !== null && _Object$keys$ !== void 0 ? _Object$keys$ : '';
    },
    isOverflowHidden: function isOverflowHidden() {
      var elementStyles = window.getComputedStyle(this.el);
      var overflowStyles = [elementStyles.overflowX, elementStyles.overflowY, elementStyles.overflow];
      return overflowStyles.includes('hidden') || overflowStyles.includes('auto');
    },
    updateHandlesPosition: function updateHandlesPosition() {
      var elementType = this.$el.data('element_type');
      var isElement = (0, _elementTypes.getAllElementTypes)().includes(elementType);
      if (!isElement) {
        return;
      }
      var shouldPlaceInside = this.isOverflowHidden();
      if (!shouldPlaceInside && this.isTopLevelElement() && this.isFirstElementInStructure()) {
        shouldPlaceInside = true;
      }
      this.$el.toggleClass('e-handles-inside', shouldPlaceInside);
    },
    isTopLevelElement: function isTopLevelElement() {
      return this.container.parent && 'document' === this.container.parent.id;
    },
    isFirstElementInStructure: function isFirstElementInStructure() {
      return 0 === this.model.collection.indexOf(this.model);
    }
  });
  return AtomicElementView;
}

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/duplicate-element.js":
/*!*************************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/duplicate-element.js ***!
  \*************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.DuplicateElement = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _regenerateLocalStyleIds = __webpack_require__(/*! ../../../utils/regenerate-local-style-ids */ "../modules/atomic-widgets/assets/js/editor/utils/regenerate-local-style-ids.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var DuplicateElement = exports.DuplicateElement = /*#__PURE__*/function (_$e$modules$hookData$) {
  function DuplicateElement() {
    (0, _classCallCheck2.default)(this, DuplicateElement);
    return _callSuper(this, DuplicateElement, arguments);
  }
  (0, _inherits2.default)(DuplicateElement, _$e$modules$hookData$);
  return (0, _createClass2.default)(DuplicateElement, [{
    key: "getCommand",
    value: function getCommand() {
      return 'document/elements/duplicate';
    }
  }, {
    key: "getId",
    value: function getId() {
      return 'regenerate-local-style-ids--document/elements/duplicate';
    }
  }, {
    key: "apply",
    value: function apply(args, result) {
      var containers = Array.isArray(result) ? result : [result];
      containers.forEach(_regenerateLocalStyleIds.regenerateLocalStyleIds);
    }
  }]);
}($e.modules.hookData.After);

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/import-element.js":
/*!**********************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/import-element.js ***!
  \**********************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.ImportElement = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _regenerateLocalStyleIds = __webpack_require__(/*! ../../../utils/regenerate-local-style-ids */ "../modules/atomic-widgets/assets/js/editor/utils/regenerate-local-style-ids.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var ImportElement = exports.ImportElement = /*#__PURE__*/function (_$e$modules$hookData$) {
  function ImportElement() {
    (0, _classCallCheck2.default)(this, ImportElement);
    return _callSuper(this, ImportElement, arguments);
  }
  (0, _inherits2.default)(ImportElement, _$e$modules$hookData$);
  return (0, _createClass2.default)(ImportElement, [{
    key: "getCommand",
    value: function getCommand() {
      return 'document/elements/import';
    }
  }, {
    key: "getId",
    value: function getId() {
      return 'regenerate-local-style-ids--document/elements/import';
    }
  }, {
    key: "apply",
    value: function apply(args, result) {
      var containers = Array.isArray(result) ? result : [result];
      containers.forEach(_regenerateLocalStyleIds.regenerateLocalStyleIds);
    }
  }]);
}($e.modules.hookData.After);

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/paste-element.js":
/*!*********************************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/paste-element.js ***!
  \*********************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.PasteElement = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _regenerateLocalStyleIds = __webpack_require__(/*! ../../../utils/regenerate-local-style-ids */ "../modules/atomic-widgets/assets/js/editor/utils/regenerate-local-style-ids.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var PasteElement = exports.PasteElement = /*#__PURE__*/function (_$e$modules$hookData$) {
  function PasteElement() {
    (0, _classCallCheck2.default)(this, PasteElement);
    return _callSuper(this, PasteElement, arguments);
  }
  (0, _inherits2.default)(PasteElement, _$e$modules$hookData$);
  return (0, _createClass2.default)(PasteElement, [{
    key: "getCommand",
    value: function getCommand() {
      return 'document/elements/paste';
    }
  }, {
    key: "getId",
    value: function getId() {
      return 'regenerate-local-style-ids--document/elements/paste';
    }
  }, {
    key: "apply",
    value: function apply(args, result) {
      var containers = Array.isArray(result) ? result : [result];
      containers.forEach(_regenerateLocalStyleIds.regenerateLocalStyleIds);
    }
  }]);
}($e.modules.hookData.After);

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/hooks/index.js":
/*!*****************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/hooks/index.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "DuplicateElement", ({
  enumerable: true,
  get: function get() {
    return _duplicateElement.DuplicateElement;
  }
}));
Object.defineProperty(exports, "ImportElement", ({
  enumerable: true,
  get: function get() {
    return _importElement.ImportElement;
  }
}));
Object.defineProperty(exports, "PasteElement", ({
  enumerable: true,
  get: function get() {
    return _pasteElement.PasteElement;
  }
}));
var _duplicateElement = __webpack_require__(/*! ./data/regenerate-local-style-ids/duplicate-element */ "../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/duplicate-element.js");
var _pasteElement = __webpack_require__(/*! ./data/regenerate-local-style-ids/paste-element */ "../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/paste-element.js");
var _importElement = __webpack_require__(/*! ./data/regenerate-local-style-ids/import-element */ "../modules/atomic-widgets/assets/js/editor/hooks/data/regenerate-local-style-ids/import-element.js");

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/utils/get-element-children.js":
/*!********************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/utils/get-element-children.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.getElementChildren = getElementChildren;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
/**
 * @typedef {import('assets/dev/js/editor/container/container')} Container
 */

/**
 * return all recursively nested elements in a flat array
 *
 * @param {Container} model
 * @return {Container[]}
 */
function getElementChildren(model) {
  var _flatMap, _container$model$get$, _container$model;
  var container = window.elementor.getContainer(model.id);
  var children = (_flatMap = ((_container$model$get$ = (_container$model = container.model) === null || _container$model === void 0 || (_container$model = _container$model.get('elements')) === null || _container$model === void 0 ? void 0 : _container$model.models) !== null && _container$model$get$ !== void 0 ? _container$model$get$ : []).flatMap(function (child) {
    return getElementChildren(child);
  })) !== null && _flatMap !== void 0 ? _flatMap : [];
  return [container].concat((0, _toConsumableArray2.default)(children));
}

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/utils/get-random-style-id.js":
/*!*******************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/utils/get-random-style-id.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.getRandomStyleId = getRandomStyleId;
/**
 * @typedef {import('elementor/assets/dev/js/editor/container/container')} Container
 */

/**
 * @param {Container} container
 * @param {Object}    existingStyleIds
 * @return {string}
 */
function getRandomStyleId(container) {
  var existingStyleIds = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var id;
  do {
    id = "e-".concat(container.id, "-").concat(elementorCommon.helpers.getUniqueId());
  } while (existingStyleIds.hasOwnProperty(id));
  return id;
}

/***/ }),

/***/ "../modules/atomic-widgets/assets/js/editor/utils/regenerate-local-style-ids.js":
/*!**************************************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/utils/regenerate-local-style-ids.js ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.regenerateLocalStyleIds = regenerateLocalStyleIds;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _getElementChildren = __webpack_require__(/*! ./get-element-children */ "../modules/atomic-widgets/assets/js/editor/utils/get-element-children.js");
var _getRandomStyleId = __webpack_require__(/*! ./get-random-style-id */ "../modules/atomic-widgets/assets/js/editor/utils/get-random-style-id.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * @typedef {import('assets/dev/js/editor/container/container')} Container
 */

function isClassesProp(prop) {
  return prop.$$type && 'classes' === prop.$$type && Array.isArray(prop.value) && prop.value.length > 0;
}

/**
 * Update the style id of the container.
 *
 * @param {Container} container
 */
function updateStyleId(container) {
  var _container$settings$t, _container$settings;
  var originalStyles = container.model.get('styles');
  var settings = (_container$settings$t = (_container$settings = container.settings) === null || _container$settings === void 0 ? void 0 : _container$settings.toJSON()) !== null && _container$settings$t !== void 0 ? _container$settings$t : {};
  var classesProps = Object.entries(settings).filter(function (_ref) {
    var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
      propValue = _ref2[1];
    return isClassesProp(propValue);
  });
  var newStyles = {};
  var changedIds = {}; // Conversion map - {[originalId: string]: newId: string}

  Object.entries(originalStyles).forEach(function (_ref3) {
    var _ref4 = (0, _slicedToArray2.default)(_ref3, 2),
      originalStyleId = _ref4[0],
      style = _ref4[1];
    var newStyleId = (0, _getRandomStyleId.getRandomStyleId)(container, newStyles);
    newStyles[newStyleId] = structuredClone(_objectSpread(_objectSpread({}, style), {}, {
      id: newStyleId
    }));
    changedIds[originalStyleId] = newStyleId;
  });
  var newClassesProps = classesProps.map(function (_ref5) {
    var _ref6 = (0, _slicedToArray2.default)(_ref5, 2),
      key = _ref6[0],
      value = _ref6[1];
    return [key, _objectSpread(_objectSpread({}, value), {}, {
      value: value.value.map(function (className) {
        var _changedIds$className;
        return (_changedIds$className = changedIds[className]) !== null && _changedIds$className !== void 0 ? _changedIds$className : className;
      })
    })];
  }, {});

  // Update classes array
  $e.internal('document/elements/set-settings', {
    container: container,
    settings: Object.fromEntries(newClassesProps)
  });

  // Update local styles
  container.model.set('styles', newStyles);
}
function updateElementsStyleIdsInsideOut(styledElements) {
  styledElements === null || styledElements === void 0 || styledElements.reverse().forEach(updateStyleId);
}

/**
 * Get a container - iterate over its children, find all styled atomic widgets and update their style ids
 *
 * @param {Container} container
 */
function regenerateLocalStyleIds(container) {
  var allElements = (0, _getElementChildren.getElementChildren)(container);
  var styledElements = allElements.filter(function (element) {
    var _element$model$get;
    return Object.keys((_element$model$get = element.model.get('styles')) !== null && _element$model$get !== void 0 ? _element$model$get : {}).length > 0;
  });
  updateElementsStyleIdsInsideOut(styledElements);
}

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \******************************************************************/
/***/ ((module) => {

function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \****************************************************************/
/***/ ((module) => {

function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _arrayWithoutHoles(r) {
  if (Array.isArray(r)) return arrayLikeToArray(r);
}
module.exports = _arrayWithoutHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _assertThisInitialized(e) {
  if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  return e;
}
module.exports = _assertThisInitialized, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \****************************************************************/
/***/ ((module) => {

function _classCallCheck(a, n) {
  if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function");
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/createClass.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/createClass.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperties(e, r) {
  for (var t = 0; t < r.length; t++) {
    var o = r[t];
    o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, toPropertyKey(o.key), o);
  }
}
function _createClass(e, r, t) {
  return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", {
    writable: !1
  }), e;
}
module.exports = _createClass, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/defineProperty.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperty(e, r, t) {
  return (r = toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : e[r] = t, e;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/get.js":
/*!*****************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/get.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var superPropBase = __webpack_require__(/*! ./superPropBase.js */ "../node_modules/@babel/runtime/helpers/superPropBase.js");
function _get() {
  return module.exports = _get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) {
    var p = superPropBase(e, t);
    if (p) {
      var n = Object.getOwnPropertyDescriptor(p, t);
      return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value;
    }
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _get.apply(null, arguments);
}
module.exports = _get, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _getPrototypeOf(t) {
  return module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) {
    return t.__proto__ || Object.getPrototypeOf(t);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _getPrototypeOf(t);
}
module.exports = _getPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/inherits.js":
/*!**********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/inherits.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _inherits(t, e) {
  if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
  t.prototype = Object.create(e && e.prototype, {
    constructor: {
      value: t,
      writable: !0,
      configurable: !0
    }
  }), Object.defineProperty(t, "prototype", {
    writable: !1
  }), e && setPrototypeOf(t, e);
}
module.exports = _inherits, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _interopRequireDefault(e) {
  return e && e.__esModule ? e : {
    "default": e
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _iterableToArray(r) {
  if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r);
}
module.exports = _iterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!**********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \**********************************************************************/
/***/ ((module) => {

function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \*******************************************************************/
/***/ ((module) => {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableSpread, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!***************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized.js */ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js");
function _possibleConstructorReturn(t, e) {
  if (e && ("object" == _typeof(e) || "function" == typeof e)) return e;
  if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined");
  return assertThisInitialized(t);
}
module.exports = _possibleConstructorReturn, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _setPrototypeOf(t, e) {
  return module.exports = _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) {
    return t.__proto__ = e, t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _setPrototypeOf(t, e);
}
module.exports = _setPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js");
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ "../node_modules/@babel/runtime/helpers/nonIterableRest.js");
function _slicedToArray(r, e) {
  return arrayWithHoles(r) || iterableToArrayLimit(r, e) || unsupportedIterableToArray(r, e) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/superPropBase.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/superPropBase.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
function _superPropBase(t, o) {
  for (; !{}.hasOwnProperty.call(t, o) && null !== (t = getPrototypeOf(t)););
  return t;
}
module.exports = _superPropBase, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");
var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ "../node_modules/@babel/runtime/helpers/iterableToArray.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread.js */ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js");
function _toConsumableArray(r) {
  return arrayWithoutHoles(r) || iterableToArray(r) || unsupportedIterableToArray(r) || nonIterableSpread();
}
module.exports = _toConsumableArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPrimitive.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPrimitive.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function toPrimitive(t, r) {
  if ("object" != _typeof(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != _typeof(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}
module.exports = toPrimitive, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPropertyKey.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPropertyKey.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var toPrimitive = __webpack_require__(/*! ./toPrimitive.js */ "../node_modules/@babel/runtime/helpers/toPrimitive.js");
function toPropertyKey(t) {
  var i = toPrimitive(t, "string");
  return "symbol" == _typeof(i) ? i : i + "";
}
module.exports = toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/typeof.js":
/*!********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/typeof.js ***!
  \********************************************************/
/***/ ((module) => {

function _typeof(o) {
  "@babel/helpers - typeof";

  return module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _typeof(o);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!****************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return arrayLikeToArray(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? arrayLikeToArray(r, a) : void 0;
  }
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/react-dom/client.js":
/*!*******************************************!*\
  !*** ../node_modules/react-dom/client.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var m = __webpack_require__(/*! react-dom */ "react-dom");
if (false) // removed by dead control flow
{} else {
  var i = m.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
  exports.createRoot = function(c, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.createRoot(c, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
  exports.hydrateRoot = function(c, h, o) {
    i.usingClientEntryPoint = true;
    try {
      return m.hydrateRoot(c, h, o);
    } finally {
      i.usingClientEntryPoint = false;
    }
  };
}


/***/ }),

/***/ "@wordpress/i18n":
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/***/ ((module) => {

"use strict";
module.exports = wp.i18n;

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = React;

/***/ }),

/***/ "react-dom":
/*!***************************!*\
  !*** external "ReactDOM" ***!
  \***************************/
/***/ ((module) => {

"use strict";
module.exports = ReactDOM;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!************************************************************!*\
  !*** ../modules/atomic-widgets/assets/js/editor/module.js ***!
  \************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _component = _interopRequireDefault(__webpack_require__(/*! ./component */ "../modules/atomic-widgets/assets/js/editor/component.js"));
var _atomicElementBaseType = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-base-type */ "../modules/atomic-widgets/assets/js/editor/atomic-element-base-type.js"));
var _createAtomicElementBaseView = _interopRequireDefault(__webpack_require__(/*! ./create-atomic-element-base-view */ "../modules/atomic-widgets/assets/js/editor/create-atomic-element-base-view.js"));
var _atomicElementBaseModel = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-base-model */ "../modules/atomic-widgets/assets/js/editor/atomic-element-base-model.js"));
var _createDivBlockType = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-types/create-div-block-type */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-div-block-type.js"));
var _createFlexboxType = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-types/create-flexbox-type */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-flexbox-type.js"));
var _createAtomicTabsType = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-types/atomic-tabs/create-atomic-tabs-type */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tabs/create-atomic-tabs-type.js"));
var _createAtomicTabPanelType = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-types/atomic-tab-panel/create-atomic-tab-panel-type */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab-panel/create-atomic-tab-panel-type.js"));
var _createAtomicTabType = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-types/atomic-tab/create-atomic-tab-type */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/atomic-tab/create-atomic-tab-type.js"));
var _createAtomicTabsListType = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-types/create-atomic-tabs-list-type */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-atomic-tabs-list-type.js"));
var _createAtomicTabsContentType = _interopRequireDefault(__webpack_require__(/*! ./atomic-element-types/create-atomic-tabs-content-type */ "../modules/atomic-widgets/assets/js/editor/atomic-element-types/create-atomic-tabs-content-type.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Module = /*#__PURE__*/function (_elementorModules$edi) {
  function Module() {
    (0, _classCallCheck2.default)(this, Module);
    return _callSuper(this, Module, arguments);
  }
  (0, _inherits2.default)(Module, _elementorModules$edi);
  return (0, _createClass2.default)(Module, [{
    key: "onInit",
    value: function onInit() {
      $e.components.register(new _component.default());
      this.exposeAtomicElementClasses();
      this.registerAtomicElements();
    }
  }, {
    key: "exposeAtomicElementClasses",
    value: function exposeAtomicElementClasses() {
      elementor.modules.elements.types.AtomicElementBase = _atomicElementBaseType.default;
      elementor.modules.elements.views.createAtomicElementBase = _createAtomicElementBaseView.default;
      elementor.modules.elements.models.AtomicElementBase = _atomicElementBaseModel.default;
    }
  }, {
    key: "registerAtomicElements",
    value: function registerAtomicElements() {
      var nestedElementsExperiment = 'e_nested_elements';
      elementor.elementsManager.registerElementType((0, _createDivBlockType.default)());
      elementor.elementsManager.registerElementType((0, _createFlexboxType.default)());
      if (elementorCommon.config.experimentalFeatures[nestedElementsExperiment]) {
        elementor.elementsManager.registerElementType((0, _createAtomicTabsType.default)());
        elementor.elementsManager.registerElementType((0, _createAtomicTabPanelType.default)());
        elementor.elementsManager.registerElementType((0, _createAtomicTabType.default)());
        elementor.elementsManager.registerElementType((0, _createAtomicTabsListType.default)());
        elementor.elementsManager.registerElementType((0, _createAtomicTabsContentType.default)());
      }
    }
  }]);
}(elementorModules.editor.utils.Module);
new Module();
})();

/******/ })()
;
//# sourceMappingURL=atomic-widgets-editor.js.map