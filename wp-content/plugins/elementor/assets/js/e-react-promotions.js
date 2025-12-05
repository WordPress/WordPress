/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/editor/components/dynamic-tags/control-behavior.js":
/*!***************************************************************************!*\
  !*** ../assets/dev/js/editor/components/dynamic-tags/control-behavior.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var TagPanelView = __webpack_require__(/*! elementor-dynamic-tags/tag-panel-view */ "../assets/dev/js/editor/components/dynamic-tags/tag-panel-view.js");
module.exports = Marionette.Behavior.extend({
  tagView: null,
  listenerAttached: false,
  initialize: function initialize() {
    if (!this.listenerAttached) {
      this.listenTo(this.view.options.container.settings, 'change:external:__dynamic__', this.onAfterExternalChange);
      this.listenerAttached = true;
    }
  },
  shouldRenderTools: function shouldRenderTools() {
    var hasDefault = this.getOption('dynamicSettings').default;
    if (hasDefault) {
      return false;
    }
    var isFeatureAvailableToUser = elementor.helpers.hasPro() && !elementor.helpers.hasProAndNotConnected(),
      hasTags = this.getOption('tags').length > 0;
    return !isFeatureAvailableToUser || hasTags;
  },
  renderTools: function renderTools() {
    var _this = this;
    if (!this.shouldRenderTools()) {
      return;
    }
    var $dynamicSwitcher = jQuery(Marionette.Renderer.render('#tmpl-elementor-control-dynamic-switcher'));
    $dynamicSwitcher.on('click', function (event) {
      return _this.onDynamicSwitcherClick(event);
    });
    this.$el.find('.elementor-control-dynamic-switcher-wrapper').append($dynamicSwitcher);
    this.ui.dynamicSwitcher = $dynamicSwitcher;
    if ('color' === this.view.model.get('type')) {
      if (this.view.colorPicker) {
        this.moveDynamicSwitcherToColorPicker();
      } else {
        setTimeout(function () {
          return _this.moveDynamicSwitcherToColorPicker();
        });
      }
    }

    // Add a Tipsy Tooltip to the Dynamic Switcher
    this.ui.dynamicSwitcher.tipsy({
      title: function title() {
        return this.getAttribute('data-tooltip');
      },
      gravity: 's'
    });
  },
  moveDynamicSwitcherToColorPicker: function moveDynamicSwitcherToColorPicker() {
    var $colorPickerToolsContainer = this.view.colorPicker.$pickerToolsContainer;
    this.ui.dynamicSwitcher.removeClass('elementor-control-unit-1').addClass('e-control-tool');
    var $eyedropper = $colorPickerToolsContainer.find('.elementor-control-element-color-picker');
    if ($eyedropper.length) {
      this.ui.dynamicSwitcher.insertBefore($eyedropper);
    } else {
      $colorPickerToolsContainer.append(this.ui.dynamicSwitcher);
    }
  },
  toggleDynamicClass: function toggleDynamicClass() {
    this.$el.toggleClass('elementor-control-dynamic-value', this.isDynamicMode());
  },
  isDynamicMode: function isDynamicMode() {
    var dynamicSettings = this.view.container.settings.get('__dynamic__');
    return !!(dynamicSettings && dynamicSettings[this.view.model.get('name')]);
  },
  createTagsList: function createTagsList() {
    var tags = _.groupBy(this.getOption('tags'), 'group'),
      groups = elementor.dynamicTags.getConfig('groups'),
      $tagsList = this.ui.tagsList = jQuery('<div>', {
        class: 'elementor-tags-list'
      }),
      $tagsListInner = jQuery('<div>', {
        class: 'elementor-tags-list__inner'
      });
    $tagsList.append($tagsListInner);
    jQuery.each(groups, function (groupName) {
      var groupTags = tags[groupName];
      if (!groupTags) {
        return;
      }
      var group = this,
        $groupTitle = jQuery('<div>', {
          class: 'elementor-tags-list__group-title'
        }).text(group.title);
      $tagsListInner.append($groupTitle);
      groupTags.forEach(function (tag) {
        var $tag = jQuery('<div>', {
          class: 'elementor-tags-list__item'
        });
        $tag.text(tag.title).attr('data-tag-name', tag.name);
        $tagsListInner.append($tag);
      });
    });

    // Create and inject pro dynamic teaser template if Pro is not installed
    if (!elementor.helpers.hasPro() && Object.keys(tags).length) {
      var proTeaser = Marionette.Renderer.render('#tmpl-elementor-dynamic-tags-promo', {
        promotionUrl: elementor.config.dynamicPromotionURL.replace('%s', this.view.model.get('name'))
      });
      $tagsListInner.append(proTeaser);
    }
    $tagsListInner.on('click', '.elementor-tags-list__item', this.onTagsListItemClick.bind(this));
    elementorCommon.elements.$body.append($tagsList);
  },
  getTagsList: function getTagsList() {
    if (!this.ui.tagsList) {
      this.createTagsList();
    }
    return this.ui.tagsList;
  },
  toggleTagsList: function toggleTagsList() {
    var $tagsList = this.getTagsList();
    if ($tagsList.is(':visible')) {
      $tagsList.hide();
      return;
    }
    var direction = elementorCommon.config.isRTL ? 'left' : 'right';
    $tagsList.show().position({
      my: "".concat(direction, " top"),
      at: "".concat(direction, " bottom+5"),
      of: this.ui.dynamicSwitcher
    });
  },
  setTagView: function setTagView(id, name, settings) {
    if (this.tagView) {
      this.tagView.destroy();
    }
    var tagView = this.tagView = new TagPanelView({
        id: id,
        name: name,
        settings: settings,
        controlName: this.view.model.get('name'),
        dynamicSettings: this.getOption('dynamicSettings')
      }),
      elementContainer = this.view.options.container,
      tagViewLabel = elementContainer.controls[tagView.options.controlName].label;
    tagView.options.container = new elementorModules.editor.Container({
      type: 'dynamic',
      id: id,
      model: tagView.model,
      settings: tagView.model,
      view: tagView,
      parent: elementContainer,
      label: elementContainer.label + ' ' + tagViewLabel,
      controls: tagView.model.options.controls,
      renderer: elementContainer
    });
    tagView.render();
    this.$el.find('.elementor-control-tag-area').after(tagView.el);
    this.listenTo(tagView, 'remove', this.onTagViewRemove.bind(this));
  },
  setDefaultTagView: function setDefaultTagView() {
    var tagData = elementor.dynamicTags.tagTextToTagData(this.getDynamicValue());
    this.setTagView(tagData.id, tagData.name, tagData.settings);
  },
  tagViewToTagText: function tagViewToTagText() {
    var tagView = this.tagView;
    return elementor.dynamicTags.tagDataToTagText(tagView.getOption('id'), tagView.getOption('name'), tagView.model);
  },
  getDynamicValue: function getDynamicValue() {
    return this.view.container.dynamic.get(this.view.model.get('name'));
  },
  destroyTagView: function destroyTagView() {
    if (this.tagView) {
      this.tagView.destroy();
      this.tagView = null;
    }
  },
  showPromotion: function showPromotion() {
    var hasProAndNotConnected = elementor.helpers.hasProAndNotConnected(),
      dialogOptions = {
        title: __('Dynamic Content', 'elementor'),
        content: __('Create more personalized and dynamic sites by populating data from various sources with dozens of dynamic tags to choose from.', 'elementor'),
        targetElement: this.ui.dynamicSwitcher,
        position: {
          blockStart: '-10'
        },
        actionButton: {
          url: hasProAndNotConnected ? elementorProEditorConfig.urls.connect : elementor.config.dynamicPromotionURL.replace('%s', this.view.model.get('name')),
          text: hasProAndNotConnected ? __('Connect & Activate', 'elementor') : __('Upgrade', 'elementor')
        }
      };
    elementor.promotion.showDialog(dialogOptions);
  },
  onRender: function onRender() {
    this.$el.addClass('elementor-control-dynamic');
    this.renderTools();
    this.toggleDynamicClass();
    if (this.isDynamicMode()) {
      this.setDefaultTagView();
    }
  },
  onDynamicSwitcherClick: function onDynamicSwitcherClick(event) {
    event.stopPropagation();
    if (this.getOption('tags').length) {
      this.toggleTagsList();
    } else {
      this.showPromotion();
    }
  },
  onTagsListItemClick: function onTagsListItemClick(event) {
    var $tag = jQuery(event.currentTarget);
    this.setTagView(elementorCommon.helpers.getUniqueId(), $tag.data('tagName'), {});

    // If an element has an active global value, disable it before applying the dynamic value.
    if (this.view.getGlobalKey()) {
      this.view.triggerMethod('unset:global:value');
    }
    if (this.isDynamicMode()) {
      $e.run('document/dynamic/settings', {
        container: this.view.options.container,
        settings: (0, _defineProperty2.default)({}, this.view.model.get('name'), this.tagViewToTagText())
      });
    } else {
      $e.run('document/dynamic/enable', {
        container: this.view.options.container,
        settings: (0, _defineProperty2.default)({}, this.view.model.get('name'), this.tagViewToTagText())
      });
    }
    this.toggleDynamicClass();
    this.toggleTagsList();
    if (this.tagView.getTagConfig().settings_required) {
      this.tagView.showSettingsPopup();
    }
  },
  onTagViewRemove: function onTagViewRemove() {
    $e.run('document/dynamic/disable', {
      container: this.view.options.container,
      settings: (0, _defineProperty2.default)({}, this.view.model.get('name'), this.tagViewToTagText())
    });
    this.toggleDynamicClass();
  },
  onAfterExternalChange: function onAfterExternalChange() {
    this.destroyTagView();
    if (this.isDynamicMode()) {
      this.setDefaultTagView();
    }
    this.toggleDynamicClass();
  },
  onDestroy: function onDestroy() {
    this.destroyTagView();
    if (this.ui.tagsList) {
      this.ui.tagsList.remove();
    }
  }
});

/***/ }),

/***/ "../assets/dev/js/editor/components/dynamic-tags/tag-controls-stack-empty.js":
/*!***********************************************************************************!*\
  !*** ../assets/dev/js/editor/components/dynamic-tags/tag-controls-stack-empty.js ***!
  \***********************************************************************************/
/***/ ((module) => {

"use strict";


module.exports = Marionette.ItemView.extend({
  className: 'elementor-tag-controls-stack-empty',
  template: '#tmpl-elementor-tag-controls-stack-empty'
});

/***/ }),

/***/ "../assets/dev/js/editor/components/dynamic-tags/tag-controls-stack.js":
/*!*****************************************************************************!*\
  !*** ../assets/dev/js/editor/components/dynamic-tags/tag-controls-stack.js ***!
  \*****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var EmptyView = __webpack_require__(/*! elementor-dynamic-tags/tag-controls-stack-empty */ "../assets/dev/js/editor/components/dynamic-tags/tag-controls-stack-empty.js");
module.exports = elementorModules.editor.views.ControlsStack.extend({
  activeTab: 'content',
  template: _.noop,
  emptyView: EmptyView,
  isEmpty: function isEmpty() {
    // Ignore the section control
    return this.collection.length < 2;
  },
  childViewOptions: function childViewOptions() {
    return {
      container: this.options.container
    };
  },
  getNamespaceArray: function getNamespaceArray() {
    var currentPageView = elementor.getPanelView().getCurrentPageView(),
      eventNamespace = currentPageView.getNamespaceArray();
    eventNamespace.push(currentPageView.activeSection);
    eventNamespace.push(this.getOption('controlName'));
    eventNamespace.push(this.getOption('name'));
    return eventNamespace;
  },
  onRenderTemplate: function onRenderTemplate() {
    this.activateFirstSection();
  }
});

/***/ }),

/***/ "../assets/dev/js/editor/components/dynamic-tags/tag-panel-view.js":
/*!*************************************************************************!*\
  !*** ../assets/dev/js/editor/components/dynamic-tags/tag-panel-view.js ***!
  \*************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var TagControlsStack = __webpack_require__(/*! elementor-dynamic-tags/tag-controls-stack */ "../assets/dev/js/editor/components/dynamic-tags/tag-controls-stack.js");
module.exports = Marionette.ItemView.extend({
  className: 'elementor-dynamic-cover e-input-style',
  tagControlsStack: null,
  templateHelpers: function templateHelpers() {
    var helpers = {};
    if (this.model) {
      helpers.controls = this.model.options.controls;
    }
    return helpers;
  },
  ui: {
    remove: '.elementor-dynamic-cover__remove'
  },
  events: function events() {
    var events = {
      'click @ui.remove': 'onRemoveClick'
    };
    if (this.hasSettings()) {
      events.click = 'onClick';
    }
    return events;
  },
  getTemplate: function getTemplate() {
    var config = this.getTagConfig(),
      templateFunction = Marionette.TemplateCache.get('#tmpl-elementor-control-dynamic-cover'),
      renderedTemplate = Marionette.Renderer.render(templateFunction, {
        hasSettings: this.hasSettings(),
        isRemovable: !this.getOption('dynamicSettings').default,
        title: config.title,
        content: config.panel_template
      });
    return Marionette.TemplateCache.prototype.compileTemplate(renderedTemplate.trim());
  },
  getTagConfig: function getTagConfig() {
    return elementor.dynamicTags.getConfig('tags.' + this.getOption('name'));
  },
  initSettingsPopup: function initSettingsPopup() {
    var settingsPopupOptions = {
      className: 'elementor-tag-settings-popup',
      position: {
        my: 'left top+5',
        at: 'left bottom',
        of: this.$el,
        autoRefresh: true
      },
      hide: {
        ignore: '.select2-container'
      }
    };
    var settingsPopup = elementorCommon.dialogsManager.createWidget('buttons', settingsPopupOptions);
    this.getSettingsPopup = function () {
      return settingsPopup;
    };
  },
  hasSettings: function hasSettings() {
    return !!Object.values(this.getTagConfig().controls).length;
  },
  showSettingsPopup: function showSettingsPopup() {
    if (!this.tagControlsStack) {
      this.initTagControlsStack();
    }
    var settingsPopup = this.getSettingsPopup();
    if (settingsPopup.isVisible()) {
      return;
    }
    settingsPopup.show();
  },
  initTagControlsStack: function initTagControlsStack() {
    this.tagControlsStack = new TagControlsStack({
      model: this.model,
      controls: this.model.controls,
      name: this.options.name,
      controlName: this.options.controlName,
      container: this.options.container,
      el: this.getSettingsPopup().getElements('message')[0]
    });
    this.tagControlsStack.render();
  },
  initModel: function initModel() {
    this.model = new elementorModules.editor.elements.models.BaseSettings(this.getOption('settings'), {
      controls: this.getTagConfig().controls
    });
  },
  initialize: function initialize() {
    // The `model` should always be available.
    this.initModel();
    if (!this.hasSettings()) {
      return;
    }
    this.initSettingsPopup();
    this.listenTo(this.model, 'change', this.render);
  },
  onClick: function onClick() {
    this.showSettingsPopup();
  },
  onRemoveClick: function onRemoveClick(event) {
    event.stopPropagation();
    this.destroy();
    this.trigger('remove');
  },
  onDestroy: function onDestroy() {
    if (this.hasSettings()) {
      this.getSettingsPopup().destroy();
    }
    if (this.tagControlsStack) {
      this.tagControlsStack.destroy();
    }
  }
});

/***/ }),

/***/ "../assets/dev/js/editor/components/validator/base.js":
/*!************************************************************!*\
  !*** ../assets/dev/js/editor/components/validator/base.js ***!
  \************************************************************/
/***/ ((module) => {

"use strict";


module.exports = elementorModules.Module.extend({
  errors: [],
  __construct: function __construct(settings) {
    var customValidationMethod = settings.customValidationMethod;
    if (customValidationMethod) {
      this.validationMethod = customValidationMethod;
    }
  },
  getDefaultSettings: function getDefaultSettings() {
    return {
      validationTerms: {}
    };
  },
  isValid: function isValid() {
    var validationErrors = this.validationMethod.apply(this, arguments);
    if (validationErrors.length) {
      this.errors = validationErrors;
      return false;
    }
    return true;
  },
  validationMethod: function validationMethod(newValue) {
    var validationTerms = this.getSettings('validationTerms'),
      errors = [];
    if (validationTerms.required) {
      if (!('' + newValue).length) {
        errors.push('Required value is empty');
      }
    }
    return errors;
  }
});

/***/ }),

/***/ "../assets/dev/js/editor/components/validator/breakpoint.js":
/*!******************************************************************!*\
  !*** ../assets/dev/js/editor/components/validator/breakpoint.js ***!
  \******************************************************************/
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
var NumberValidator = __webpack_require__(/*! elementor-validator/number */ "../assets/dev/js/editor/components/validator/number.js");
var BreakpointValidator = exports["default"] = /*#__PURE__*/function (_NumberValidator) {
  function BreakpointValidator() {
    (0, _classCallCheck2.default)(this, BreakpointValidator);
    return _callSuper(this, BreakpointValidator, arguments);
  }
  (0, _inherits2.default)(BreakpointValidator, _NumberValidator);
  return (0, _createClass2.default)(BreakpointValidator, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        validationTerms: {
          // Max width we allow in general
          max: 5120
        }
      };
    }

    /**
     * Get Panel Active Breakpoints
     *
     * Since the active kit used in the Site Settings panel could be a draft, we need to use the panel's active
     * breakpoints settings and not the elementorFrontend.config values (which come from the DB).
     *
     * @return {*} Object
     */
  }, {
    key: "getPanelActiveBreakpoints",
    value: function getPanelActiveBreakpoints() {
      var panelBreakpoints = elementor.documents.currentDocument.config.settings.settings.active_breakpoints.map(function (breakpointName) {
          return breakpointName.replace('viewport_', '');
        }),
        panelActiveBreakpoints = {};
      panelBreakpoints.forEach(function (breakpointName) {
        panelActiveBreakpoints[breakpointName] = elementorFrontend.config.responsive.breakpoints[breakpointName];
      });
      return panelActiveBreakpoints;
    }
  }, {
    key: "initBreakpointProperties",
    value: function initBreakpointProperties() {
      var _activeBreakpoints$br, _activeBreakpoints$br2;
      var validationTerms = this.getSettings('validationTerms'),
        activeBreakpoints = this.getPanelActiveBreakpoints(),
        breakpointKeys = Object.keys(activeBreakpoints);
      this.breakpointIndex = breakpointKeys.indexOf(validationTerms.breakpointName);
      this.topBreakpoint = (_activeBreakpoints$br = activeBreakpoints[breakpointKeys[this.breakpointIndex + 1]]) === null || _activeBreakpoints$br === void 0 ? void 0 : _activeBreakpoints$br.value;
      this.bottomBreakpoint = (_activeBreakpoints$br2 = activeBreakpoints[breakpointKeys[this.breakpointIndex - 1]]) === null || _activeBreakpoints$br2 === void 0 ? void 0 : _activeBreakpoints$br2.value;
    }
  }, {
    key: "validationMethod",
    value: function validationMethod(newValue) {
      var validationTerms = this.getSettings('validationTerms'),
        errors = NumberValidator.prototype.validationMethod.call(this, newValue);

      // Validate both numeric and empty values, since breakpoints utilize default values when empty.
      if (_.isFinite(newValue) || '' === newValue) {
        if (!this.validateMinMaxForBreakpoint(newValue, validationTerms)) {
          errors.push('Value is not between the breakpoints above or under the edited breakpoint');
        }
      }
      return errors;
    }
  }, {
    key: "validateMinMaxForBreakpoint",
    value: function validateMinMaxForBreakpoint(newValue, validationTerms) {
      var breakpointDefaultValue = elementorFrontend.config.responsive.breakpoints[validationTerms.breakpointName].default_value;
      var isValid = true;
      this.initBreakpointProperties();

      // Since the following comparison is <=, allow usage of the 320px value for the mobile breakpoint.
      if ('mobile' === validationTerms.breakpointName && 320 === this.bottomBreakpoint) {
        this.bottomBreakpoint -= 1;
      }

      // If there is a breakpoint below the currently edited breakpoint
      if (this.bottomBreakpoint) {
        // Check that the new value is not under the bottom breakpoint's value.
        if ('' !== newValue && newValue <= this.bottomBreakpoint) {
          isValid = false;
        }

        // If the new value is empty, check that the default breakpoint value is not below the bottom breakpoint.
        if ('' === newValue && breakpointDefaultValue <= this.bottomBreakpoint) {
          isValid = false;
        }
      }

      // If there is a breakpoint above the currently edited breakpoint.
      if (this.topBreakpoint) {
        // Check that the value is not above the top breakpoint's value.
        if ('' !== newValue && newValue >= this.topBreakpoint) {
          isValid = false;
        }

        // If the new value is empty, check that the default breakpoint value is not above the top breakpoint.
        if ('' === newValue && breakpointDefaultValue >= this.topBreakpoint) {
          isValid = false;
        }
      }
      return isValid;
    }
  }]);
}(NumberValidator);

/***/ }),

/***/ "../assets/dev/js/editor/components/validator/number.js":
/*!**************************************************************!*\
  !*** ../assets/dev/js/editor/components/validator/number.js ***!
  \**************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var Validator = __webpack_require__(/*! elementor-validator/base */ "../assets/dev/js/editor/components/validator/base.js");
module.exports = Validator.extend({
  validationMethod: function validationMethod(newValue) {
    var validationTerms = this.getSettings('validationTerms'),
      errors = [];
    if (_.isFinite(newValue)) {
      if (undefined !== validationTerms.min && newValue < validationTerms.min) {
        errors.push('Value is less than minimum');
      }
      if (undefined !== validationTerms.max && newValue > validationTerms.max) {
        errors.push('Value is greater than maximum');
      }
    }
    return errors;
  }
});

/***/ }),

/***/ "../assets/dev/js/editor/controls/base-data.js":
/*!*****************************************************!*\
  !*** ../assets/dev/js/editor/controls/base-data.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _breakpoint = _interopRequireDefault(__webpack_require__(/*! elementor-validator/breakpoint */ "../assets/dev/js/editor/components/validator/breakpoint.js"));
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var ControlBaseView = __webpack_require__(/*! elementor-controls/base */ "../assets/dev/js/editor/controls/base.js"),
  TagsBehavior = __webpack_require__(/*! elementor-dynamic-tags/control-behavior */ "../assets/dev/js/editor/components/dynamic-tags/control-behavior.js"),
  Validator = __webpack_require__(/*! elementor-validator/base */ "../assets/dev/js/editor/components/validator/base.js"),
  NumberValidator = __webpack_require__(/*! elementor-validator/number */ "../assets/dev/js/editor/components/validator/number.js"),
  ControlBaseDataView;
ControlBaseDataView = ControlBaseView.extend({
  validatorTypes: {
    Base: Validator,
    Number: NumberValidator,
    Breakpoint: _breakpoint.default
  },
  ui: function ui() {
    var ui = ControlBaseView.prototype.ui.apply(this, arguments);
    _.extend(ui, {
      input: 'input[data-setting][type!="checkbox"][type!="radio"]',
      checkbox: 'input[data-setting][type="checkbox"]',
      radio: 'input[data-setting][type="radio"]',
      select: 'select[data-setting]',
      textarea: 'textarea[data-setting]',
      responsiveSwitchersSibling: "".concat(ui.controlTitle, "[data-e-responsive-switcher-sibling!=\"false\"]"),
      responsiveSwitchers: '.elementor-responsive-switcher',
      contentEditable: '[contenteditable="true"]'
    });
    return ui;
  },
  templateHelpers: function templateHelpers() {
    var controlData = ControlBaseView.prototype.templateHelpers.apply(this, arguments);
    controlData.data.controlValue = this.getControlValue();
    return controlData;
  },
  events: function events() {
    return {
      'input @ui.input': 'onBaseInputTextChange',
      'change @ui.checkbox': 'onBaseInputChange',
      'change @ui.radio': 'onBaseInputChange',
      'input @ui.textarea': 'onBaseInputTextChange',
      'change @ui.select': 'onBaseInputChange',
      'input @ui.contentEditable': 'onBaseInputTextChange',
      'click @ui.responsiveSwitchers': 'onResponsiveSwitchersClick'
    };
  },
  behaviors: function behaviors() {
    var behaviors = ControlBaseView.prototype.behaviors.apply(this, arguments),
      dynamicSettings = this.options.model.get('dynamic');
    if (dynamicSettings && dynamicSettings.active) {
      var tags = _.filter(elementor.dynamicTags.getConfig('tags'), function (tag) {
        return tag.editable && _.intersection(tag.categories, dynamicSettings.categories).length;
      });
      if (tags.length || elementor.config.user.is_administrator) {
        behaviors.tags = {
          behaviorClass: TagsBehavior,
          tags: tags,
          dynamicSettings: dynamicSettings
        };
      }
    }
    return behaviors;
  },
  initialize: function initialize() {
    ControlBaseView.prototype.initialize.apply(this, arguments);
    this.registerValidators();
    if (this.model.get('responsive')) {
      this.setPlaceholderFromParent();
    }
    if (undefined === this.model.get('inherit_placeholders')) {
      this.model.set('inherit_placeholders', true);
    }

    // TODO: this.elementSettingsModel is deprecated since 2.8.0.
    var settings = this.container ? this.container.settings : this.elementSettingsModel;
    this.listenTo(settings, 'change:external:' + this.model.get('name'), this.onAfterExternalChange);
  },
  getControlValue: function getControlValue() {
    return this.container.settings.get(this.model.get('name'));
  },
  getGlobalKey: function getGlobalKey() {
    return this.container.globals.get(this.model.get('name'));
  },
  getGlobalValue: function getGlobalValue() {
    return this.globalValue;
  },
  getGlobalDefault: function getGlobalDefault() {
    var controlGlobalArgs = this.model.get('global');
    if (controlGlobalArgs !== null && controlGlobalArgs !== void 0 && controlGlobalArgs.default) {
      // If the control is a color/typography control and default colors/typography are disabled, don't return the global value.
      if (!elementor.config.globals.defaults_enabled[this.getGlobalMeta().controlType]) {
        return '';
      }
      var _$e$data$commandExtra = $e.data.commandExtractArgs(controlGlobalArgs.default),
        command = _$e$data$commandExtra.command,
        args = _$e$data$commandExtra.args,
        result = $e.data.getCache($e.components.get('globals'), command, args.query);
      return result === null || result === void 0 ? void 0 : result.value;
    }

    // No global default.
    return '';
  },
  getCurrentValue: function getCurrentValue() {
    if (this.getGlobalKey() && !this.globalValue) {
      return '';
    }
    if (this.globalValue) {
      return this.globalValue;
    }
    var controlValue = this.getControlValue();
    if (controlValue) {
      return controlValue;
    }
    return this.getGlobalDefault();
  },
  isGlobalActive: function isGlobalActive() {
    var _this$options$model$g;
    return (_this$options$model$g = this.options.model.get('global')) === null || _this$options$model$g === void 0 ? void 0 : _this$options$model$g.active;
  },
  setValue: function setValue(value) {
    this.setSettingsModel(value);
  },
  setSettingsModel: function setSettingsModel(value) {
    var key = this.model.get('name');
    $e.run('document/elements/settings', {
      container: this.options.container,
      settings: (0, _defineProperty2.default)({}, key, value)
    });
    this.triggerMethod('settings:change');
  },
  applySavedValue: function applySavedValue() {
    this.setInputValue('[data-setting="' + this.model.get('name') + '"]', this.getControlValue());
  },
  getEditSettings: function getEditSettings(setting) {
    var settings = this.getOption('elementEditSettings').toJSON();
    if (setting) {
      return settings[setting];
    }
    return settings;
  },
  setEditSetting: function setEditSetting(settingKey, settingValue) {
    var settings = this.getOption('elementEditSettings') || this.getOption('container').settings;
    settings.set(settingKey, settingValue);
  },
  /**
   * Get the placeholder for the current control.
   *
   * @return {*} placeholder
   */
  getControlPlaceholder: function getControlPlaceholder() {
    var placeholder = this.model.get('placeholder');
    if (this.model.get('responsive') && this.model.get('inherit_placeholders')) {
      placeholder = placeholder || this.container.placeholders[this.model.get('name')];
    }
    return placeholder;
  },
  /**
   * Get the responsive parent view if exists.
   *
   * @return {ControlBaseDataView|undefined} responsive parent view if exists
   */
  getResponsiveParentView: function getResponsiveParentView() {
    var parent = this.model.get('parent');
    try {
      return parent && this.container.panel.getControlView(parent);
      // eslint-disable-next-line no-empty
    } catch (e) {}
  },
  /**
   * Get the responsive children views if exists.
   *
   * @return {ControlBaseDataView|null} responsive children views if exists
   */
  getResponsiveChildrenViews: function getResponsiveChildrenViews() {
    var children = this.model.get('inheritors'),
      views = [];
    try {
      var _iterator = _createForOfIteratorHelper(children),
        _step;
      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var child = _step.value;
          views.push(this.container.panel.getControlView(child));
        }
        // eslint-disable-next-line no-empty
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
    } catch (e) {}
    return views;
  },
  /**
   * Get prepared placeholder from the responsive parent, and put it into current
   * control model as placeholder.
   */
  setPlaceholderFromParent: function setPlaceholderFromParent() {
    var parent = this.getResponsiveParentView();
    if (parent) {
      this.container.placeholders[this.model.get('name')] = parent.preparePlaceholderForChildren();
    }
  },
  /**
   * Returns the value of the current control if exists, or the parent value if not,
   * so responsive children can set it as their placeholder. When there are multiple
   * inputs, the inputs which are empty on this control will inherit their values
   * from the responsive parent.
   * For example, if on desktop the padding of all edges is 10, and on tablet only
   * padding right and left is set to 15, the mobile control placeholder will
   * eventually be: { top: 10, right: 15, left: 15, bottom: 10 }, because of the
   * inheritance of multiple values.
   *
   * @return {*} value of the current control if exists, or the parent value if not
   */
  preparePlaceholderForChildren: function preparePlaceholderForChildren() {
    var _this$getResponsivePa;
    var cleanValue = this.getCleanControlValue(),
      parentValue = (_this$getResponsivePa = this.getResponsiveParentView()) === null || _this$getResponsivePa === void 0 ? void 0 : _this$getResponsivePa.preparePlaceholderForChildren();
    if (cleanValue instanceof Object) {
      return Object.assign({}, parentValue, cleanValue);
    }
    return cleanValue || parentValue;
  },
  /**
   * Start the re-rendering recursive chain from the responsive child of this
   * control. It's useful when the current control value is changed and we want
   * to update all responsive children. In this case, the re-rendering is supposed
   * to be applied only from the responsive child of this control and on.
   */
  propagatePlaceholder: function propagatePlaceholder() {
    var children = this.getResponsiveChildrenViews();
    var _iterator2 = _createForOfIteratorHelper(children),
      _step2;
    try {
      for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
        var child = _step2.value;
        child.renderWithChildren();
      }
    } catch (err) {
      _iterator2.e(err);
    } finally {
      _iterator2.f();
    }
  },
  /**
   * Re-render current control and trigger this method on the responsive child.
   * The purpose of those actions is to recursively re-render all responsive
   * children.
   */
  renderWithChildren: function renderWithChildren() {
    this.render();
    this.propagatePlaceholder();
  },
  /**
   * Get control value without empty properties, and without default values.
   *
   * @return {{}} control value without empty properties, and without default values
   */
  getCleanControlValue: function getCleanControlValue() {
    var value = this.getControlValue();
    return value && value !== this.model.get('default') ? value : undefined;
  },
  onAfterChange: function onAfterChange(control) {
    if (Object.keys(control.changed).includes(this.model.get('name'))) {
      this.propagatePlaceholder();
    }
    ControlBaseView.prototype.onAfterChange.apply(this, arguments);
  },
  getInputValue: function getInputValue(input) {
    var $input = this.$(input);
    if ($input.is('[contenteditable="true"]')) {
      return $input.html();
    }
    var inputValue = $input.val(),
      inputType = $input.attr('type');
    if (-1 !== ['radio', 'checkbox'].indexOf(inputType)) {
      return $input.prop('checked') ? inputValue : '';
    }
    if ('number' === inputType && _.isFinite(inputValue)) {
      return +inputValue;
    }

    // Temp fix for jQuery (< 3.0) that return null instead of empty array
    if ('SELECT' === input.tagName && $input.prop('multiple') && null === inputValue) {
      inputValue = [];
    }
    return inputValue;
  },
  setInputValue: function setInputValue(input, value) {
    var $input = this.$(input),
      inputType = $input.attr('type');
    if ('checkbox' === inputType) {
      $input.prop('checked', !!value);
    } else if ('radio' === inputType) {
      $input.filter('[value="' + value + '"]').prop('checked', true);
    } else {
      $input.val(value);
    }
  },
  addValidator: function addValidator(validator) {
    this.validators.push(validator);
  },
  registerValidators: function registerValidators() {
    var _this = this;
    this.validators = [];
    var validationTerms = {};
    if (this.model.get('required')) {
      validationTerms.required = true;
    }
    if (!jQuery.isEmptyObject(validationTerms)) {
      this.addValidator(new this.validatorTypes.Base({
        validationTerms: validationTerms
      }));
    }
    var validators = this.model.get('validators');
    if (validators) {
      Object.entries(validators).forEach(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          key = _ref2[0],
          args = _ref2[1];
        _this.addValidator(new _this.validatorTypes[key]({
          validationTerms: args
        }));
      });
    }
  },
  onBeforeRender: function onBeforeRender() {
    this.setPlaceholderFromParent();
  },
  onRender: function onRender() {
    ControlBaseView.prototype.onRender.apply(this, arguments);
    if (this.model.get('responsive')) {
      this.renderResponsiveSwitchers();
    }
    this.applySavedValue();
    this.triggerMethod('ready');
    this.toggleControlVisibility();
    this.addTooltip();
  },
  onBaseInputTextChange: function onBaseInputTextChange(event) {
    this.onBaseInputChange(event);
  },
  onBaseInputChange: function onBaseInputChange(event) {
    clearTimeout(this.correctionTimeout);
    var input = event.currentTarget,
      value = this.getInputValue(input),
      validators = this.validators.slice(0),
      settingsValidators = this.container.settings.validators[this.model.get('name')];
    if (settingsValidators) {
      validators = validators.concat(settingsValidators);
    }
    if (validators) {
      var oldValue = this.getControlValue(input.dataset.setting);
      var isValidValue = validators.every(function (validator) {
        return validator.isValid(value, oldValue);
      });
      if (!isValidValue) {
        this.correctionTimeout = setTimeout(this.setInputValue.bind(this, input, oldValue), 1200);
        return;
      }
    }
    this.updateElementModel(value, input);
    this.triggerMethod('input:change', event);
  },
  onResponsiveSwitchersClick: function onResponsiveSwitchersClick(event) {
    var $switcher = jQuery(event.currentTarget),
      device = $switcher.data('device'),
      $switchersWrapper = this.ui.responsiveSwitchersWrapper,
      selectedOption = $switcher.index();
    $switchersWrapper.toggleClass('elementor-responsive-switchers-open');
    $switchersWrapper[0].style.setProperty('--selected-option', selectedOption);
    this.triggerMethod('responsive:switcher:click', device);
    elementor.changeDeviceMode(device);
  },
  renderResponsiveSwitchers: function renderResponsiveSwitchers() {
    var templateHtml = Marionette.Renderer.render('#tmpl-elementor-control-responsive-switchers', this.model.attributes);
    this.ui.responsiveSwitchersSibling.after(templateHtml);
    this.ui.responsiveSwitchersWrapper = this.$el.find('.elementor-control-responsive-switchers');
  },
  onAfterExternalChange: function onAfterExternalChange() {
    this.hideTooltip();
    this.applySavedValue();
  },
  addTooltip: function addTooltip() {
    this.ui.tooltipTargets = this.$el.find('.tooltip-target');
    if (!this.ui.tooltipTargets.length) {
      return;
    }

    // Create tooltip on controls
    this.ui.tooltipTargets.tipsy({
      gravity: function gravity() {
        // `n` for down, `s` for up
        var gravity = jQuery(this).data('tooltip-pos');
        if (undefined !== gravity) {
          return gravity;
        }
        return 's';
      },
      title: function title() {
        return this.getAttribute('data-tooltip');
      }
    });
  },
  hideTooltip: function hideTooltip() {
    if (this.ui.tooltipTargets.length) {
      this.ui.tooltipTargets.tipsy('hide');
    }
  },
  updateElementModel: function updateElementModel(value) {
    this.setValue(value);
  }
}, {
  // Static methods
  getStyleValue: function getStyleValue(placeholder, controlValue, controlData) {
    if ('DEFAULT' === placeholder) {
      return controlData.default;
    }
    return controlValue;
  },
  onPasteStyle: function onPasteStyle() {
    return true;
  }
});
module.exports = ControlBaseDataView;

/***/ }),

/***/ "../assets/dev/js/editor/controls/base.js":
/*!************************************************!*\
  !*** ../assets/dev/js/editor/controls/base.js ***!
  \************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var ControlBaseView;
ControlBaseView = Marionette.CompositeView.extend({
  ui: function ui() {
    return {
      controlTitle: '.elementor-control-title'
    };
  },
  behaviors: function behaviors() {
    var behaviors = {};
    return elementor.hooks.applyFilters('controls/base/behaviors', behaviors, this);
  },
  getBehavior: function getBehavior(name) {
    return this._behaviors[Object.keys(this.behaviors()).indexOf(name)];
  },
  className: function className() {
    // TODO: Any better classes for that?
    var classes = 'elementor-control elementor-control-' + this.model.get('name') + ' elementor-control-type-' + this.model.get('type'),
      modelClasses = this.model.get('classes'),
      responsive = this.model.get('responsive');
    if (!_.isEmpty(modelClasses)) {
      classes += ' ' + modelClasses;
    }
    if (!_.isEmpty(responsive)) {
      var responsiveControlName = responsive.max || responsive.min;
      classes += ' elementor-control-responsive-' + responsiveControlName;
    }
    return classes;
  },
  templateHelpers: function templateHelpers() {
    var controlData = {
      _cid: this.model.cid
    };
    return {
      view: this,
      data: _.extend({}, this.model.toJSON(), controlData)
    };
  },
  getTemplate: function getTemplate() {
    return Marionette.TemplateCache.get('#tmpl-elementor-control-' + this.model.get('type') + '-content');
  },
  initialize: function initialize(options) {
    var label = this.model.get('label');

    // TODO: Temp backwards compatibility. since 2.8.0.
    Object.defineProperty(this, 'container', {
      get: function get() {
        if (!options.container) {
          var settingsModel = options.elementSettingsModel,
            view = $e.components.get('document').utils.findViewById(settingsModel.id);

          // Element control.
          if (view && view.getContainer) {
            options.container = view.getContainer();
          } else {
            if (!settingsModel.id) {
              settingsModel.id = 'bc-' + elementorCommon.helpers.getUniqueId();
            }

            // Document/General/Other control.
            options.container = new elementorModules.editor.Container({
              type: 'bc-container',
              id: settingsModel.id,
              model: settingsModel,
              settings: settingsModel,
              label: label,
              view: false,
              parent: false,
              renderer: false,
              controls: settingsModel.options.controls
            });
          }
        }
        return options.container;
      }
    });

    // Use `defineProperty` because `get elementSettingsModel()` fails during the `Marionette.CompositeView.extend`.
    Object.defineProperty(this, 'elementSettingsModel', {
      get: function get() {
        elementorDevTools.deprecation.deprecated('elementSettingsModel', '2.8.0', 'container.settings');
        return options.container ? options.container.settings : options.elementSettingsModel;
      }
    });
    var controlType = this.model.get('type'),
      controlSettings = jQuery.extend(true, {}, elementor.config.controls[controlType], this.model.attributes);
    this.model.set(controlSettings);

    // TODO: this.elementSettingsModel is deprecated since 2.8.0.
    var settings = this.container ? this.container.settings : this.elementSettingsModel;
    this.listenTo(settings, 'change', this.onAfterChange);
    if (this.model.attributes.responsive) {
      this.onDeviceModeChange = this.onDeviceModeChange.bind(this);
      elementor.listenTo(elementor.channels.deviceMode, 'change', this.onDeviceModeChange);
    }
  },
  onDestroy: function onDestroy() {
    elementor.stopListening(elementor.channels.deviceMode, 'change', this.onDeviceModeChange);
  },
  onDeviceModeChange: function onDeviceModeChange() {
    this.toggleControlVisibility();
  },
  onAfterChange: function onAfterChange() {
    this.toggleControlVisibility();
  },
  toggleControlVisibility: function toggleControlVisibility() {
    // TODO: this.elementSettingsModel is deprecated since 2.8.0.
    var settings = this.container ? this.container.settings : this.elementSettingsModel;
    var isVisible = elementor.helpers.isActiveControl(this.model, settings.attributes, settings.controls);
    this.$el.toggleClass('elementor-hidden-control', !isVisible);
    elementor.getPanelView().updateScrollbar();
  },
  onRender: function onRender() {
    var layoutType = this.model.get('label_block') ? 'block' : 'inline',
      showLabel = this.model.get('show_label'),
      elClasses = 'elementor-label-' + layoutType;
    elClasses += ' elementor-control-separator-' + this.model.get('separator');
    if (!showLabel) {
      elClasses += ' elementor-control-hidden-label';
    }
    this.$el.addClass(elClasses);
    this.toggleControlVisibility();
  },
  reRoute: function reRoute(controlActive) {
    $e.route($e.routes.getCurrent('panel'), this.getControlInRouteArgs(controlActive ? this.getControlPath() : ''), {
      history: false
    });
  },
  getControlInRouteArgs: function getControlInRouteArgs(path) {
    return _objectSpread(_objectSpread({}, $e.routes.getCurrentArgs('panel')), {}, {
      activeControl: path
    });
  },
  getControlPath: function getControlPath() {
    var controlPath = this.model.get('name'),
      parent = this._parent;
    while (!parent.$el.hasClass('elementor-controls-stack')) {
      var parentName = parent.model.get('name') || parent.model.get('_id');
      controlPath = parentName + '/' + controlPath;
      parent = parent._parent;
    }
    return controlPath;
  }
});
module.exports = ControlBaseView;

/***/ }),

/***/ "../modules/promotions/assets/js/react/app-manager.js":
/*!************************************************************!*\
  !*** ../modules/promotions/assets/js/react/app-manager.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.AppManager = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _app = _interopRequireDefault(__webpack_require__(/*! ./app */ "../modules/promotions/assets/js/react/app.js"));
var _client = __webpack_require__(/*! react-dom/client */ "../node_modules/react-dom/client.js");
var AppManager = exports.AppManager = /*#__PURE__*/function () {
  function AppManager() {
    (0, _classCallCheck2.default)(this, AppManager);
    this.promotionInfoTip = null;
    this.onRoute = function () {};
  }
  return (0, _createClass2.default)(AppManager, [{
    key: "getPromotionData",
    value: function getPromotionData(promotionType) {
      return elementorPromotionsData[promotionType] || {};
    }
  }, {
    key: "mount",
    value: function mount(targetNode, selectors) {
      var _elementor,
        _elementor$getPrefere,
        _rootElement$getAttri,
        _this = this;
      if (this.promotionInfoTip) {
        return;
      }
      var wrapperElement = targetNode === null || targetNode === void 0 ? void 0 : targetNode.closest(selectors.wrapperElement);
      var rootElement = wrapperElement === null || wrapperElement === void 0 ? void 0 : wrapperElement.querySelector(selectors.reactAnchor);
      if (!rootElement) {
        return;
      }
      this.attachEditorEventListeners();
      this.promotionInfoTip = (0, _client.createRoot)(rootElement);
      var colorScheme = ((_elementor = elementor) === null || _elementor === void 0 || (_elementor$getPrefere = _elementor.getPreferences) === null || _elementor$getPrefere === void 0 ? void 0 : _elementor$getPrefere.call(_elementor, 'ui_theme')) || 'auto';
      var isRTL = elementorCommon.config.isRTL;
      var promotionType = (_rootElement$getAttri = rootElement.getAttribute('data-promotion')) === null || _rootElement$getAttri === void 0 ? void 0 : _rootElement$getAttri.replace('_promotion', '');
      this.promotionInfoTip.render(/*#__PURE__*/_react.default.createElement(_app.default, {
        colorScheme: colorScheme,
        isRTL: isRTL,
        promotionsData: this.getPromotionData(promotionType),
        onClose: function onClose() {
          return _this.unmount();
        }
      }));
    }
  }, {
    key: "unmount",
    value: function unmount() {
      if (this.promotionInfoTip) {
        this.detachEditorEventListeners();
        this.promotionInfoTip.unmount();
      }
      this.promotionInfoTip = null;
    }
  }, {
    key: "attachEditorEventListeners",
    value: function attachEditorEventListeners() {
      var _this2 = this;
      this.onRoute = function (component, route) {
        if (route !== 'panel/elements/categories' && route !== 'panel/editor/content') {
          return;
        }
        _this2.unmount();
      };
      $e.routes.on('run:after', this.onRoute);
    }
  }, {
    key: "detachEditorEventListeners",
    value: function detachEditorEventListeners() {
      $e.routes.off('run:after', this.onRoute);
    }
  }]);
}();

/***/ }),

/***/ "../modules/promotions/assets/js/react/app.js":
/*!****************************************************!*\
  !*** ../modules/promotions/assets/js/react/app.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _promotionCard = _interopRequireDefault(__webpack_require__(/*! ./components/promotion-card */ "../modules/promotions/assets/js/react/components/promotion-card.js"));
var App = function App(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.DirectionProvider, {
    rtl: props.isRTL
  }, /*#__PURE__*/_react.default.createElement(_ui.LocalizationProvider, null, /*#__PURE__*/_react.default.createElement(_ui.ThemeProvider, {
    colorScheme: props.colorScheme
  }, /*#__PURE__*/_react.default.createElement(_ui.Infotip, {
    content: /*#__PURE__*/_react.default.createElement(_promotionCard.default, {
      doClose: props.onClose,
      promotionsData: props.promotionsData
    }),
    placement: "right",
    arrow: true,
    open: true,
    disableHoverListener: true,
    PopperProps: {
      modifiers: [{
        name: 'offset',
        options: {
          offset: [-24, 8]
        }
      }]
    }
  }, /*#__PURE__*/_react.default.createElement("span", null)))));
};
App.propTypes = {
  colorScheme: PropTypes.oneOf(['auto', 'light', 'dark']),
  isRTL: PropTypes.bool,
  promotionsData: PropTypes.object,
  onClose: PropTypes.func.isRequired
};
var _default = exports["default"] = App;

/***/ }),

/***/ "../modules/promotions/assets/js/react/components/promotion-card.js":
/*!**************************************************************************!*\
  !*** ../modules/promotions/assets/js/react/components/promotion-card.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var PromotionCard = function PromotionCard(_ref) {
  var doClose = _ref.doClose,
    promotionsData = _ref.promotionsData;
  var title = promotionsData === null || promotionsData === void 0 ? void 0 : promotionsData.title,
    description = promotionsData === null || promotionsData === void 0 ? void 0 : promotionsData.description,
    imgSrc = promotionsData === null || promotionsData === void 0 ? void 0 : promotionsData.image,
    imgAlt = promotionsData === null || promotionsData === void 0 ? void 0 : promotionsData.image_alt,
    ctaText = promotionsData === null || promotionsData === void 0 ? void 0 : promotionsData.upgrade_text,
    ctaUrl = promotionsData === null || promotionsData === void 0 ? void 0 : promotionsData.upgrade_url;
  var redirectHandler = function redirectHandler() {
    window.open(ctaUrl, '_blank');
    return doClose();
  };
  return /*#__PURE__*/_react.default.createElement(_ui.ClickAwayListener, {
    disableReactTree: true,
    mouseEvent: "onMouseDown",
    touchEvent: "onTouchStart",
    onClickAway: doClose
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      width: 296
    },
    "data-testid": "e-promotion-card"
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    alignItems: "center",
    py: 1,
    px: 2
  }, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "subtitle2"
  }, title), /*#__PURE__*/_react.default.createElement(_ui.Chip, {
    label: (0, _i18n.__)('PRO', 'elementor'),
    size: "small",
    variant: "outlined",
    color: "promotion",
    sx: {
      ml: 1
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.CloseButton, {
    edge: "end",
    sx: {
      ml: 'auto'
    },
    slotProps: {
      icon: {
        fontSize: 'small'
      }
    },
    onClick: doClose
  })), /*#__PURE__*/_react.default.createElement(_ui.Image, {
    src: imgSrc,
    alt: imgAlt,
    sx: {
      height: 150,
      width: '100%'
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    px: 2
  }, 1 === description.length ? /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    color: "secondary",
    sx: {
      pt: 1.5,
      pb: 1
    }
  }, description[0]) : /*#__PURE__*/_react.default.createElement(_ui.List, {
    sx: {
      pl: 2
    }
  }, description.map(function (text, index) {
    return /*#__PURE__*/_react.default.createElement(_ui.ListItem, {
      key: index,
      sx: {
        listStyle: 'disc',
        display: 'list-item',
        color: 'text.secondary',
        p: 0
      }
    }, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
      variant: "body2",
      color: "secondary"
    }, text));
  }))), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    pt: 1,
    pb: 1.5,
    px: 2
  }, /*#__PURE__*/_react.default.createElement(_ui.Button, {
    variant: "contained",
    size: "small",
    color: "promotion",
    onClick: redirectHandler,
    sx: {
      ml: 'auto'
    }
  }, ctaText))));
};
PromotionCard.propTypes = {
  doClose: PropTypes.func,
  promotionsData: PropTypes.object
};
var _default = exports["default"] = PromotionCard;

/***/ }),

/***/ "../modules/promotions/assets/js/react/controls/promotion.js":
/*!*******************************************************************!*\
  !*** ../modules/promotions/assets/js/react/controls/promotion.js ***!
  \*******************************************************************/
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
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _baseData = _interopRequireDefault(__webpack_require__(/*! elementor-controls/base-data */ "../assets/dev/js/editor/controls/base-data.js"));
var _appManager = __webpack_require__(/*! ../app-manager */ "../modules/promotions/assets/js/react/app-manager.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_ControlBaseDataView) {
  function _default(options) {
    var _this;
    (0, _classCallCheck2.default)(this, _default);
    _this = _callSuper(this, _default, [options]);
    (0, _defineProperty2.default)(_this, "promotionInfoTip", null);
    (0, _defineProperty2.default)(_this, "selectors", {
      wrapperElement: '.elementor-control-type-switcher',
      reactAnchor: '.e-promotion-react-wrapper'
    });
    _this.AppManager = new _appManager.AppManager();
    return _this;
  }
  (0, _inherits2.default)(_default, _ControlBaseDataView);
  return (0, _createClass2.default)(_default, [{
    key: "ui",
    value: function ui() {
      return {
        switcher: '[data-promotion].elementor-control-type-switcher'
      };
    }
  }, {
    key: "events",
    value: function events() {
      return {
        'click @ui.switcher': 'onClickControlSwitcher'
      };
    }
  }, {
    key: "promotionData",
    value: function promotionData(promotionType) {
      return elementorPromotionsData[promotionType] || {};
    }
  }, {
    key: "onClickControlSwitcher",
    value: function onClickControlSwitcher(event) {
      event.stopPropagation();
      this.AppManager.mount(event.target, this.selectors);
    }
  }]);
}(_baseData.default);

/***/ }),

/***/ "../modules/promotions/assets/js/react/module.js":
/*!*******************************************************!*\
  !*** ../modules/promotions/assets/js/react/module.js ***!
  \*******************************************************/
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
var _promotion = _interopRequireDefault(__webpack_require__(/*! ./controls/promotion */ "../modules/promotions/assets/js/react/controls/promotion.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Module = exports["default"] = /*#__PURE__*/function (_elementorModules$edi) {
  function Module() {
    (0, _classCallCheck2.default)(this, Module);
    return _callSuper(this, Module, arguments);
  }
  (0, _inherits2.default)(Module, _elementorModules$edi);
  return (0, _createClass2.default)(Module, [{
    key: "onElementorInit",
    value: function onElementorInit() {
      elementor.addControlView('promotion_control', _promotion.default);
    }
  }]);
}(elementorModules.editor.utils.Module);

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

/***/ "../node_modules/object-assign/index.js":
/*!**********************************************!*\
  !*** ../node_modules/object-assign/index.js ***!
  \**********************************************/
/***/ ((module) => {

"use strict";
/*
object-assign
(c) Sindre Sorhus
@license MIT
*/


/* eslint-disable no-unused-vars */
var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var propIsEnumerable = Object.prototype.propertyIsEnumerable;

function toObject(val) {
	if (val === null || val === undefined) {
		throw new TypeError('Object.assign cannot be called with null or undefined');
	}

	return Object(val);
}

function shouldUseNative() {
	try {
		if (!Object.assign) {
			return false;
		}

		// Detect buggy property enumeration order in older V8 versions.

		// https://bugs.chromium.org/p/v8/issues/detail?id=4118
		var test1 = new String('abc');  // eslint-disable-line no-new-wrappers
		test1[5] = 'de';
		if (Object.getOwnPropertyNames(test1)[0] === '5') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test2 = {};
		for (var i = 0; i < 10; i++) {
			test2['_' + String.fromCharCode(i)] = i;
		}
		var order2 = Object.getOwnPropertyNames(test2).map(function (n) {
			return test2[n];
		});
		if (order2.join('') !== '0123456789') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test3 = {};
		'abcdefghijklmnopqrst'.split('').forEach(function (letter) {
			test3[letter] = letter;
		});
		if (Object.keys(Object.assign({}, test3)).join('') !==
				'abcdefghijklmnopqrst') {
			return false;
		}

		return true;
	} catch (err) {
		// We don't expect any of the above to throw, but better to be safe.
		return false;
	}
}

module.exports = shouldUseNative() ? Object.assign : function (target, source) {
	var from;
	var to = toObject(target);
	var symbols;

	for (var s = 1; s < arguments.length; s++) {
		from = Object(arguments[s]);

		for (var key in from) {
			if (hasOwnProperty.call(from, key)) {
				to[key] = from[key];
			}
		}

		if (getOwnPropertySymbols) {
			symbols = getOwnPropertySymbols(from);
			for (var i = 0; i < symbols.length; i++) {
				if (propIsEnumerable.call(from, symbols[i])) {
					to[symbols[i]] = from[symbols[i]];
				}
			}
		}
	}

	return to;
};


/***/ }),

/***/ "../node_modules/prop-types/checkPropTypes.js":
/*!****************************************************!*\
  !*** ../node_modules/prop-types/checkPropTypes.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var printWarning = function() {};

if (true) {
  var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "../node_modules/prop-types/lib/ReactPropTypesSecret.js");
  var loggedTypeFailures = {};
  var has = __webpack_require__(/*! ./lib/has */ "../node_modules/prop-types/lib/has.js");

  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) { /**/ }
  };
}

/**
 * Assert that the values match with the type specs.
 * Error messages are memorized and will only be shown once.
 *
 * @param {object} typeSpecs Map of name to a ReactPropType
 * @param {object} values Runtime values that need to be type-checked
 * @param {string} location e.g. "prop", "context", "child context"
 * @param {string} componentName Name of the component for error messages.
 * @param {?Function} getStack Returns the component stack.
 * @private
 */
function checkPropTypes(typeSpecs, values, location, componentName, getStack) {
  if (true) {
    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error;
        // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.
        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            var err = Error(
              (componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' +
              'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' +
              'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.'
            );
            err.name = 'Invariant Violation';
            throw err;
          }
          error = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, ReactPropTypesSecret);
        } catch (ex) {
          error = ex;
        }
        if (error && !(error instanceof Error)) {
          printWarning(
            (componentName || 'React class') + ': type specification of ' +
            location + ' `' + typeSpecName + '` is invalid; the type checker ' +
            'function must return `null` or an `Error` but returned a ' + typeof error + '. ' +
            'You may have forgotten to pass an argument to the type checker ' +
            'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' +
            'shape all require an argument).'
          );
        }
        if (error instanceof Error && !(error.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error.message] = true;

          var stack = getStack ? getStack() : '';

          printWarning(
            'Failed ' + location + ' type: ' + error.message + (stack != null ? stack : '')
          );
        }
      }
    }
  }
}

/**
 * Resets warning cache when testing.
 *
 * @private
 */
checkPropTypes.resetWarningCache = function() {
  if (true) {
    loggedTypeFailures = {};
  }
}

module.exports = checkPropTypes;


/***/ }),

/***/ "../node_modules/prop-types/factoryWithTypeCheckers.js":
/*!*************************************************************!*\
  !*** ../node_modules/prop-types/factoryWithTypeCheckers.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactIs = __webpack_require__(/*! react-is */ "../node_modules/prop-types/node_modules/react-is/index.js");
var assign = __webpack_require__(/*! object-assign */ "../node_modules/object-assign/index.js");

var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "../node_modules/prop-types/lib/ReactPropTypesSecret.js");
var has = __webpack_require__(/*! ./lib/has */ "../node_modules/prop-types/lib/has.js");
var checkPropTypes = __webpack_require__(/*! ./checkPropTypes */ "../node_modules/prop-types/checkPropTypes.js");

var printWarning = function() {};

if (true) {
  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  };
}

function emptyFunctionThatReturnsNull() {
  return null;
}

module.exports = function(isValidElement, throwOnDirectAccess) {
  /* global Symbol */
  var ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
  var FAUX_ITERATOR_SYMBOL = '@@iterator'; // Before Symbol spec.

  /**
   * Returns the iterator method function contained on the iterable object.
   *
   * Be sure to invoke the function with the iterable as context:
   *
   *     var iteratorFn = getIteratorFn(myIterable);
   *     if (iteratorFn) {
   *       var iterator = iteratorFn.call(myIterable);
   *       ...
   *     }
   *
   * @param {?object} maybeIterable
   * @return {?function}
   */
  function getIteratorFn(maybeIterable) {
    var iteratorFn = maybeIterable && (ITERATOR_SYMBOL && maybeIterable[ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL]);
    if (typeof iteratorFn === 'function') {
      return iteratorFn;
    }
  }

  /**
   * Collection of methods that allow declaration and validation of props that are
   * supplied to React components. Example usage:
   *
   *   var Props = require('ReactPropTypes');
   *   var MyArticle = React.createClass({
   *     propTypes: {
   *       // An optional string prop named "description".
   *       description: Props.string,
   *
   *       // A required enum prop named "category".
   *       category: Props.oneOf(['News','Photos']).isRequired,
   *
   *       // A prop named "dialog" that requires an instance of Dialog.
   *       dialog: Props.instanceOf(Dialog).isRequired
   *     },
   *     render: function() { ... }
   *   });
   *
   * A more formal specification of how these methods are used:
   *
   *   type := array|bool|func|object|number|string|oneOf([...])|instanceOf(...)
   *   decl := ReactPropTypes.{type}(.isRequired)?
   *
   * Each and every declaration produces a function with the same signature. This
   * allows the creation of custom validation functions. For example:
   *
   *  var MyLink = React.createClass({
   *    propTypes: {
   *      // An optional string or URI prop named "href".
   *      href: function(props, propName, componentName) {
   *        var propValue = props[propName];
   *        if (propValue != null && typeof propValue !== 'string' &&
   *            !(propValue instanceof URI)) {
   *          return new Error(
   *            'Expected a string or an URI for ' + propName + ' in ' +
   *            componentName
   *          );
   *        }
   *      }
   *    },
   *    render: function() {...}
   *  });
   *
   * @internal
   */

  var ANONYMOUS = '<<anonymous>>';

  // Important!
  // Keep this list in sync with production version in `./factoryWithThrowingShims.js`.
  var ReactPropTypes = {
    array: createPrimitiveTypeChecker('array'),
    bigint: createPrimitiveTypeChecker('bigint'),
    bool: createPrimitiveTypeChecker('boolean'),
    func: createPrimitiveTypeChecker('function'),
    number: createPrimitiveTypeChecker('number'),
    object: createPrimitiveTypeChecker('object'),
    string: createPrimitiveTypeChecker('string'),
    symbol: createPrimitiveTypeChecker('symbol'),

    any: createAnyTypeChecker(),
    arrayOf: createArrayOfTypeChecker,
    element: createElementTypeChecker(),
    elementType: createElementTypeTypeChecker(),
    instanceOf: createInstanceTypeChecker,
    node: createNodeChecker(),
    objectOf: createObjectOfTypeChecker,
    oneOf: createEnumTypeChecker,
    oneOfType: createUnionTypeChecker,
    shape: createShapeTypeChecker,
    exact: createStrictShapeTypeChecker,
  };

  /**
   * inlined Object.is polyfill to avoid requiring consumers ship their own
   * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
   */
  /*eslint-disable no-self-compare*/
  function is(x, y) {
    // SameValue algorithm
    if (x === y) {
      // Steps 1-5, 7-10
      // Steps 6.b-6.e: +0 != -0
      return x !== 0 || 1 / x === 1 / y;
    } else {
      // Step 6.a: NaN == NaN
      return x !== x && y !== y;
    }
  }
  /*eslint-enable no-self-compare*/

  /**
   * We use an Error-like object for backward compatibility as people may call
   * PropTypes directly and inspect their output. However, we don't use real
   * Errors anymore. We don't inspect their stack anyway, and creating them
   * is prohibitively expensive if they are created too often, such as what
   * happens in oneOfType() for any type before the one that matched.
   */
  function PropTypeError(message, data) {
    this.message = message;
    this.data = data && typeof data === 'object' ? data: {};
    this.stack = '';
  }
  // Make `instanceof Error` still work for returned errors.
  PropTypeError.prototype = Error.prototype;

  function createChainableTypeChecker(validate) {
    if (true) {
      var manualPropTypeCallCache = {};
      var manualPropTypeWarningCount = 0;
    }
    function checkType(isRequired, props, propName, componentName, location, propFullName, secret) {
      componentName = componentName || ANONYMOUS;
      propFullName = propFullName || propName;

      if (secret !== ReactPropTypesSecret) {
        if (throwOnDirectAccess) {
          // New behavior only for users of `prop-types` package
          var err = new Error(
            'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
            'Use `PropTypes.checkPropTypes()` to call them. ' +
            'Read more at http://fb.me/use-check-prop-types'
          );
          err.name = 'Invariant Violation';
          throw err;
        } else if ( true && typeof console !== 'undefined') {
          // Old behavior for people using React.PropTypes
          var cacheKey = componentName + ':' + propName;
          if (
            !manualPropTypeCallCache[cacheKey] &&
            // Avoid spamming the console because they are often not actionable except for lib authors
            manualPropTypeWarningCount < 3
          ) {
            printWarning(
              'You are manually calling a React.PropTypes validation ' +
              'function for the `' + propFullName + '` prop on `' + componentName + '`. This is deprecated ' +
              'and will throw in the standalone `prop-types` package. ' +
              'You may be seeing this warning due to a third-party PropTypes ' +
              'library. See https://fb.me/react-warning-dont-call-proptypes ' + 'for details.'
            );
            manualPropTypeCallCache[cacheKey] = true;
            manualPropTypeWarningCount++;
          }
        }
      }
      if (props[propName] == null) {
        if (isRequired) {
          if (props[propName] === null) {
            return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required ' + ('in `' + componentName + '`, but its value is `null`.'));
          }
          return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required in ' + ('`' + componentName + '`, but its value is `undefined`.'));
        }
        return null;
      } else {
        return validate(props, propName, componentName, location, propFullName);
      }
    }

    var chainedCheckType = checkType.bind(null, false);
    chainedCheckType.isRequired = checkType.bind(null, true);

    return chainedCheckType;
  }

  function createPrimitiveTypeChecker(expectedType) {
    function validate(props, propName, componentName, location, propFullName, secret) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== expectedType) {
        // `propValue` being instance of, say, date/regexp, pass the 'object'
        // check, but we can offer a more precise error message here rather than
        // 'of type `object`'.
        var preciseType = getPreciseType(propValue);

        return new PropTypeError(
          'Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + preciseType + '` supplied to `' + componentName + '`, expected ') + ('`' + expectedType + '`.'),
          {expectedType: expectedType}
        );
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createAnyTypeChecker() {
    return createChainableTypeChecker(emptyFunctionThatReturnsNull);
  }

  function createArrayOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside arrayOf.');
      }
      var propValue = props[propName];
      if (!Array.isArray(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an array.'));
      }
      for (var i = 0; i < propValue.length; i++) {
        var error = typeChecker(propValue, i, componentName, location, propFullName + '[' + i + ']', ReactPropTypesSecret);
        if (error instanceof Error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!isValidElement(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!ReactIs.isValidElementType(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement type.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createInstanceTypeChecker(expectedClass) {
    function validate(props, propName, componentName, location, propFullName) {
      if (!(props[propName] instanceof expectedClass)) {
        var expectedClassName = expectedClass.name || ANONYMOUS;
        var actualClassName = getClassName(props[propName]);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + actualClassName + '` supplied to `' + componentName + '`, expected ') + ('instance of `' + expectedClassName + '`.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createEnumTypeChecker(expectedValues) {
    if (!Array.isArray(expectedValues)) {
      if (true) {
        if (arguments.length > 1) {
          printWarning(
            'Invalid arguments supplied to oneOf, expected an array, got ' + arguments.length + ' arguments. ' +
            'A common mistake is to write oneOf(x, y, z) instead of oneOf([x, y, z]).'
          );
        } else {
          printWarning('Invalid argument supplied to oneOf, expected an array.');
        }
      }
      return emptyFunctionThatReturnsNull;
    }

    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      for (var i = 0; i < expectedValues.length; i++) {
        if (is(propValue, expectedValues[i])) {
          return null;
        }
      }

      var valuesString = JSON.stringify(expectedValues, function replacer(key, value) {
        var type = getPreciseType(value);
        if (type === 'symbol') {
          return String(value);
        }
        return value;
      });
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of value `' + String(propValue) + '` ' + ('supplied to `' + componentName + '`, expected one of ' + valuesString + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createObjectOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside objectOf.');
      }
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an object.'));
      }
      for (var key in propValue) {
        if (has(propValue, key)) {
          var error = typeChecker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
          if (error instanceof Error) {
            return error;
          }
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createUnionTypeChecker(arrayOfTypeCheckers) {
    if (!Array.isArray(arrayOfTypeCheckers)) {
       true ? printWarning('Invalid argument supplied to oneOfType, expected an instance of array.') : 0;
      return emptyFunctionThatReturnsNull;
    }

    for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
      var checker = arrayOfTypeCheckers[i];
      if (typeof checker !== 'function') {
        printWarning(
          'Invalid argument supplied to oneOfType. Expected an array of check functions, but ' +
          'received ' + getPostfixForTypeWarning(checker) + ' at index ' + i + '.'
        );
        return emptyFunctionThatReturnsNull;
      }
    }

    function validate(props, propName, componentName, location, propFullName) {
      var expectedTypes = [];
      for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
        var checker = arrayOfTypeCheckers[i];
        var checkerResult = checker(props, propName, componentName, location, propFullName, ReactPropTypesSecret);
        if (checkerResult == null) {
          return null;
        }
        if (checkerResult.data && has(checkerResult.data, 'expectedType')) {
          expectedTypes.push(checkerResult.data.expectedType);
        }
      }
      var expectedTypesMessage = (expectedTypes.length > 0) ? ', expected one of type [' + expectedTypes.join(', ') + ']': '';
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`' + expectedTypesMessage + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createNodeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      if (!isNode(props[propName])) {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`, expected a ReactNode.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function invalidValidatorError(componentName, location, propFullName, key, type) {
    return new PropTypeError(
      (componentName || 'React class') + ': ' + location + ' type `' + propFullName + '.' + key + '` is invalid; ' +
      'it must be a function, usually from the `prop-types` package, but received `' + type + '`.'
    );
  }

  function createShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      for (var key in shapeTypes) {
        var checker = shapeTypes[key];
        if (typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createStrictShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      // We need to check all keys in case some are required but missing from props.
      var allKeys = assign({}, props[propName], shapeTypes);
      for (var key in allKeys) {
        var checker = shapeTypes[key];
        if (has(shapeTypes, key) && typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        if (!checker) {
          return new PropTypeError(
            'Invalid ' + location + ' `' + propFullName + '` key `' + key + '` supplied to `' + componentName + '`.' +
            '\nBad object: ' + JSON.stringify(props[propName], null, '  ') +
            '\nValid keys: ' + JSON.stringify(Object.keys(shapeTypes), null, '  ')
          );
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }

    return createChainableTypeChecker(validate);
  }

  function isNode(propValue) {
    switch (typeof propValue) {
      case 'number':
      case 'string':
      case 'undefined':
        return true;
      case 'boolean':
        return !propValue;
      case 'object':
        if (Array.isArray(propValue)) {
          return propValue.every(isNode);
        }
        if (propValue === null || isValidElement(propValue)) {
          return true;
        }

        var iteratorFn = getIteratorFn(propValue);
        if (iteratorFn) {
          var iterator = iteratorFn.call(propValue);
          var step;
          if (iteratorFn !== propValue.entries) {
            while (!(step = iterator.next()).done) {
              if (!isNode(step.value)) {
                return false;
              }
            }
          } else {
            // Iterator will provide entry [k,v] tuples rather than values.
            while (!(step = iterator.next()).done) {
              var entry = step.value;
              if (entry) {
                if (!isNode(entry[1])) {
                  return false;
                }
              }
            }
          }
        } else {
          return false;
        }

        return true;
      default:
        return false;
    }
  }

  function isSymbol(propType, propValue) {
    // Native Symbol.
    if (propType === 'symbol') {
      return true;
    }

    // falsy value can't be a Symbol
    if (!propValue) {
      return false;
    }

    // 19.4.3.5 Symbol.prototype[@@toStringTag] === 'Symbol'
    if (propValue['@@toStringTag'] === 'Symbol') {
      return true;
    }

    // Fallback for non-spec compliant Symbols which are polyfilled.
    if (typeof Symbol === 'function' && propValue instanceof Symbol) {
      return true;
    }

    return false;
  }

  // Equivalent of `typeof` but with special handling for array and regexp.
  function getPropType(propValue) {
    var propType = typeof propValue;
    if (Array.isArray(propValue)) {
      return 'array';
    }
    if (propValue instanceof RegExp) {
      // Old webkits (at least until Android 4.0) return 'function' rather than
      // 'object' for typeof a RegExp. We'll normalize this here so that /bla/
      // passes PropTypes.object.
      return 'object';
    }
    if (isSymbol(propType, propValue)) {
      return 'symbol';
    }
    return propType;
  }

  // This handles more types than `getPropType`. Only used for error messages.
  // See `createPrimitiveTypeChecker`.
  function getPreciseType(propValue) {
    if (typeof propValue === 'undefined' || propValue === null) {
      return '' + propValue;
    }
    var propType = getPropType(propValue);
    if (propType === 'object') {
      if (propValue instanceof Date) {
        return 'date';
      } else if (propValue instanceof RegExp) {
        return 'regexp';
      }
    }
    return propType;
  }

  // Returns a string that is postfixed to a warning about an invalid type.
  // For example, "undefined" or "of type array"
  function getPostfixForTypeWarning(value) {
    var type = getPreciseType(value);
    switch (type) {
      case 'array':
      case 'object':
        return 'an ' + type;
      case 'boolean':
      case 'date':
      case 'regexp':
        return 'a ' + type;
      default:
        return type;
    }
  }

  // Returns class name of the object, if any.
  function getClassName(propValue) {
    if (!propValue.constructor || !propValue.constructor.name) {
      return ANONYMOUS;
    }
    return propValue.constructor.name;
  }

  ReactPropTypes.checkPropTypes = checkPropTypes;
  ReactPropTypes.resetWarningCache = checkPropTypes.resetWarningCache;
  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ "../node_modules/prop-types/index.js":
/*!*******************************************!*\
  !*** ../node_modules/prop-types/index.js ***!
  \*******************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (true) {
  var ReactIs = __webpack_require__(/*! react-is */ "../node_modules/prop-types/node_modules/react-is/index.js");

  // By explicitly using `prop-types` you are opting into new development behavior.
  // http://fb.me/prop-types-in-prod
  var throwOnDirectAccess = true;
  module.exports = __webpack_require__(/*! ./factoryWithTypeCheckers */ "../node_modules/prop-types/factoryWithTypeCheckers.js")(ReactIs.isElement, throwOnDirectAccess);
} else // removed by dead control flow
{}


/***/ }),

/***/ "../node_modules/prop-types/lib/ReactPropTypesSecret.js":
/*!**************************************************************!*\
  !*** ../node_modules/prop-types/lib/ReactPropTypesSecret.js ***!
  \**************************************************************/
/***/ ((module) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ }),

/***/ "../node_modules/prop-types/lib/has.js":
/*!*********************************************!*\
  !*** ../node_modules/prop-types/lib/has.js ***!
  \*********************************************/
/***/ ((module) => {

module.exports = Function.call.bind(Object.prototype.hasOwnProperty);


/***/ }),

/***/ "../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js":
/*!************************************************************************************!*\
  !*** ../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";
/** @license React v16.13.1
 * react-is.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */





if (true) {
  (function() {
'use strict';

// The Symbol used to tag the ReactElement-like types. If there is no native Symbol
// nor polyfill, then a plain number is used for performance.
var hasSymbol = typeof Symbol === 'function' && Symbol.for;
var REACT_ELEMENT_TYPE = hasSymbol ? Symbol.for('react.element') : 0xeac7;
var REACT_PORTAL_TYPE = hasSymbol ? Symbol.for('react.portal') : 0xeaca;
var REACT_FRAGMENT_TYPE = hasSymbol ? Symbol.for('react.fragment') : 0xeacb;
var REACT_STRICT_MODE_TYPE = hasSymbol ? Symbol.for('react.strict_mode') : 0xeacc;
var REACT_PROFILER_TYPE = hasSymbol ? Symbol.for('react.profiler') : 0xead2;
var REACT_PROVIDER_TYPE = hasSymbol ? Symbol.for('react.provider') : 0xeacd;
var REACT_CONTEXT_TYPE = hasSymbol ? Symbol.for('react.context') : 0xeace; // TODO: We don't use AsyncMode or ConcurrentMode anymore. They were temporary
// (unstable) APIs that have been removed. Can we remove the symbols?

var REACT_ASYNC_MODE_TYPE = hasSymbol ? Symbol.for('react.async_mode') : 0xeacf;
var REACT_CONCURRENT_MODE_TYPE = hasSymbol ? Symbol.for('react.concurrent_mode') : 0xeacf;
var REACT_FORWARD_REF_TYPE = hasSymbol ? Symbol.for('react.forward_ref') : 0xead0;
var REACT_SUSPENSE_TYPE = hasSymbol ? Symbol.for('react.suspense') : 0xead1;
var REACT_SUSPENSE_LIST_TYPE = hasSymbol ? Symbol.for('react.suspense_list') : 0xead8;
var REACT_MEMO_TYPE = hasSymbol ? Symbol.for('react.memo') : 0xead3;
var REACT_LAZY_TYPE = hasSymbol ? Symbol.for('react.lazy') : 0xead4;
var REACT_BLOCK_TYPE = hasSymbol ? Symbol.for('react.block') : 0xead9;
var REACT_FUNDAMENTAL_TYPE = hasSymbol ? Symbol.for('react.fundamental') : 0xead5;
var REACT_RESPONDER_TYPE = hasSymbol ? Symbol.for('react.responder') : 0xead6;
var REACT_SCOPE_TYPE = hasSymbol ? Symbol.for('react.scope') : 0xead7;

function isValidElementType(type) {
  return typeof type === 'string' || typeof type === 'function' || // Note: its typeof might be other than 'symbol' or 'number' if it's a polyfill.
  type === REACT_FRAGMENT_TYPE || type === REACT_CONCURRENT_MODE_TYPE || type === REACT_PROFILER_TYPE || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || typeof type === 'object' && type !== null && (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || type.$$typeof === REACT_FUNDAMENTAL_TYPE || type.$$typeof === REACT_RESPONDER_TYPE || type.$$typeof === REACT_SCOPE_TYPE || type.$$typeof === REACT_BLOCK_TYPE);
}

function typeOf(object) {
  if (typeof object === 'object' && object !== null) {
    var $$typeof = object.$$typeof;

    switch ($$typeof) {
      case REACT_ELEMENT_TYPE:
        var type = object.type;

        switch (type) {
          case REACT_ASYNC_MODE_TYPE:
          case REACT_CONCURRENT_MODE_TYPE:
          case REACT_FRAGMENT_TYPE:
          case REACT_PROFILER_TYPE:
          case REACT_STRICT_MODE_TYPE:
          case REACT_SUSPENSE_TYPE:
            return type;

          default:
            var $$typeofType = type && type.$$typeof;

            switch ($$typeofType) {
              case REACT_CONTEXT_TYPE:
              case REACT_FORWARD_REF_TYPE:
              case REACT_LAZY_TYPE:
              case REACT_MEMO_TYPE:
              case REACT_PROVIDER_TYPE:
                return $$typeofType;

              default:
                return $$typeof;
            }

        }

      case REACT_PORTAL_TYPE:
        return $$typeof;
    }
  }

  return undefined;
} // AsyncMode is deprecated along with isAsyncMode

var AsyncMode = REACT_ASYNC_MODE_TYPE;
var ConcurrentMode = REACT_CONCURRENT_MODE_TYPE;
var ContextConsumer = REACT_CONTEXT_TYPE;
var ContextProvider = REACT_PROVIDER_TYPE;
var Element = REACT_ELEMENT_TYPE;
var ForwardRef = REACT_FORWARD_REF_TYPE;
var Fragment = REACT_FRAGMENT_TYPE;
var Lazy = REACT_LAZY_TYPE;
var Memo = REACT_MEMO_TYPE;
var Portal = REACT_PORTAL_TYPE;
var Profiler = REACT_PROFILER_TYPE;
var StrictMode = REACT_STRICT_MODE_TYPE;
var Suspense = REACT_SUSPENSE_TYPE;
var hasWarnedAboutDeprecatedIsAsyncMode = false; // AsyncMode should be deprecated

function isAsyncMode(object) {
  {
    if (!hasWarnedAboutDeprecatedIsAsyncMode) {
      hasWarnedAboutDeprecatedIsAsyncMode = true; // Using console['warn'] to evade Babel and ESLint

      console['warn']('The ReactIs.isAsyncMode() alias has been deprecated, ' + 'and will be removed in React 17+. Update your code to use ' + 'ReactIs.isConcurrentMode() instead. It has the exact same API.');
    }
  }

  return isConcurrentMode(object) || typeOf(object) === REACT_ASYNC_MODE_TYPE;
}
function isConcurrentMode(object) {
  return typeOf(object) === REACT_CONCURRENT_MODE_TYPE;
}
function isContextConsumer(object) {
  return typeOf(object) === REACT_CONTEXT_TYPE;
}
function isContextProvider(object) {
  return typeOf(object) === REACT_PROVIDER_TYPE;
}
function isElement(object) {
  return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
}
function isForwardRef(object) {
  return typeOf(object) === REACT_FORWARD_REF_TYPE;
}
function isFragment(object) {
  return typeOf(object) === REACT_FRAGMENT_TYPE;
}
function isLazy(object) {
  return typeOf(object) === REACT_LAZY_TYPE;
}
function isMemo(object) {
  return typeOf(object) === REACT_MEMO_TYPE;
}
function isPortal(object) {
  return typeOf(object) === REACT_PORTAL_TYPE;
}
function isProfiler(object) {
  return typeOf(object) === REACT_PROFILER_TYPE;
}
function isStrictMode(object) {
  return typeOf(object) === REACT_STRICT_MODE_TYPE;
}
function isSuspense(object) {
  return typeOf(object) === REACT_SUSPENSE_TYPE;
}

exports.AsyncMode = AsyncMode;
exports.ConcurrentMode = ConcurrentMode;
exports.ContextConsumer = ContextConsumer;
exports.ContextProvider = ContextProvider;
exports.Element = Element;
exports.ForwardRef = ForwardRef;
exports.Fragment = Fragment;
exports.Lazy = Lazy;
exports.Memo = Memo;
exports.Portal = Portal;
exports.Profiler = Profiler;
exports.StrictMode = StrictMode;
exports.Suspense = Suspense;
exports.isAsyncMode = isAsyncMode;
exports.isConcurrentMode = isConcurrentMode;
exports.isContextConsumer = isContextConsumer;
exports.isContextProvider = isContextProvider;
exports.isElement = isElement;
exports.isForwardRef = isForwardRef;
exports.isFragment = isFragment;
exports.isLazy = isLazy;
exports.isMemo = isMemo;
exports.isPortal = isPortal;
exports.isProfiler = isProfiler;
exports.isStrictMode = isStrictMode;
exports.isSuspense = isSuspense;
exports.isValidElementType = isValidElementType;
exports.typeOf = typeOf;
  })();
}


/***/ }),

/***/ "../node_modules/prop-types/node_modules/react-is/index.js":
/*!*****************************************************************!*\
  !*** ../node_modules/prop-types/node_modules/react-is/index.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


if (false) // removed by dead control flow
{} else {
  module.exports = __webpack_require__(/*! ./cjs/react-is.development.js */ "../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js");
}


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

/***/ "@elementor/ui":
/*!*********************************!*\
  !*** external "elementorV2.ui" ***!
  \*********************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.ui;

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
/*!******************************************************!*\
  !*** ../modules/promotions/assets/js/react/index.js ***!
  \******************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _module = _interopRequireDefault(__webpack_require__(/*! ./module.js */ "../modules/promotions/assets/js/react/module.js"));
new _module.default();
})();

/******/ })()
;
//# sourceMappingURL=e-react-promotions.js.map