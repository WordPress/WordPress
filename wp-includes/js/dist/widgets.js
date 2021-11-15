this["wp"] = this["wp"] || {}; this["wp"]["widgets"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "4g8P");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "4g8P":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "MoveToWidgetArea", function() { return /* reexport */ MoveToWidgetArea; });
__webpack_require__.d(__webpack_exports__, "getWidgetIdFromBlock", function() { return /* reexport */ getWidgetIdFromBlock; });
__webpack_require__.d(__webpack_exports__, "addWidgetIdToBlock", function() { return /* reexport */ addWidgetIdToBlock; });
__webpack_require__.d(__webpack_exports__, "registerLegacyWidgetBlock", function() { return /* binding */ registerLegacyWidgetBlock; });
__webpack_require__.d(__webpack_exports__, "registerWidgetGroupBlock", function() { return /* binding */ registerWidgetGroupBlock; });
__webpack_require__.d(__webpack_exports__, "registerLegacyWidgetVariations", function() { return /* reexport */ registerLegacyWidgetVariations; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/index.js
var legacy_widget_namespaceObject = {};
__webpack_require__.r(legacy_widget_namespaceObject);
__webpack_require__.d(legacy_widget_namespaceObject, "metadata", function() { return legacy_widget_metadata; });
__webpack_require__.d(legacy_widget_namespaceObject, "name", function() { return legacy_widget_name; });
__webpack_require__.d(legacy_widget_namespaceObject, "settings", function() { return legacy_widget_settings; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/index.js
var widget_group_namespaceObject = {};
__webpack_require__.r(widget_group_namespaceObject);
__webpack_require__.d(widget_group_namespaceObject, "metadata", function() { return widget_group_metadata; });
__webpack_require__.d(widget_group_namespaceObject, "name", function() { return widget_group_name; });
__webpack_require__.d(widget_group_namespaceObject, "settings", function() { return widget_group_settings; });

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","primitives"]
var external_wp_primitives_ = __webpack_require__("Tqx9");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/widget.js


/**
 * WordPress dependencies
 */

const widget_widget = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M6 3H8V5H16V3H18V5C19.1046 5 20 5.89543 20 7V19C20 20.1046 19.1046 21 18 21H6C4.89543 21 4 20.1046 4 19V7C4 5.89543 4.89543 5 6 5V3ZM18 6.5H6C5.72386 6.5 5.5 6.72386 5.5 7V8H18.5V7C18.5 6.72386 18.2761 6.5 18 6.5ZM18.5 9.5H5.5V19C5.5 19.2761 5.72386 19.5 6 19.5H18C18.2761 19.5 18.5 19.2761 18.5 19V9.5ZM11 11H13V13H11V11ZM7 11V13H9V11H7ZM15 13V11H17V13H15Z"
}));
/* harmony default export */ var library_widget = (widget_widget);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_ = __webpack_require__("axFQ");

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/brush.js


/**
 * WordPress dependencies
 */

const brush = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M4 20h8v-1.5H4V20zM18.9 3.5c-.6-.6-1.5-.6-2.1 0l-7.2 7.2c-.4-.1-.7 0-1.1.1-.5.2-1.5.7-1.9 2.2-.4 1.7-.8 2.2-1.1 2.7-.1.1-.2.3-.3.4l-.6 1.1H6c2 0 3.4-.4 4.7-1.4.8-.6 1.2-1.4 1.3-2.3 0-.3 0-.5-.1-.7L19 5.7c.5-.6.5-1.6-.1-2.2zM9.7 14.7c-.7.5-1.5.8-2.4 1 .2-.5.5-1.2.8-2.3.2-.6.4-1 .8-1.1.5-.1 1 .1 1.3.3.2.2.3.5.2.8 0 .3-.1.9-.7 1.3z"
}));
/* harmony default export */ var library_brush = (brush);

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","coreData"]
var external_wp_coreData_ = __webpack_require__("jZUy");

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/widget-type-selector.js


/**
 * WordPress dependencies
 */





function WidgetTypeSelector(_ref) {
  let {
    selectedId,
    onSelect
  } = _ref;
  const widgetTypes = Object(external_wp_data_["useSelect"])(select => {
    var _select$getSettings$w, _select$getSettings, _select$getWidgetType;

    const hiddenIds = (_select$getSettings$w = (_select$getSettings = select(external_wp_blockEditor_["store"]).getSettings()) === null || _select$getSettings === void 0 ? void 0 : _select$getSettings.widgetTypesToHideFromLegacyWidgetBlock) !== null && _select$getSettings$w !== void 0 ? _select$getSettings$w : [];
    return (_select$getWidgetType = select(external_wp_coreData_["store"]).getWidgetTypes({
      per_page: -1
    })) === null || _select$getWidgetType === void 0 ? void 0 : _select$getWidgetType.filter(widgetType => !hiddenIds.includes(widgetType.id));
  }, []);

  if (!widgetTypes) {
    return Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null);
  }

  if (widgetTypes.length === 0) {
    return Object(external_wp_i18n_["__"])('There are no widgets available.');
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["SelectControl"], {
    label: Object(external_wp_i18n_["__"])('Select a legacy widget to display:'),
    value: selectedId !== null && selectedId !== void 0 ? selectedId : '',
    options: [{
      value: '',
      label: Object(external_wp_i18n_["__"])('Select widget')
    }, ...widgetTypes.map(widgetType => ({
      value: widgetType.id,
      label: widgetType.name
    }))],
    onChange: value => {
      if (value) {
        const selected = widgetTypes.find(widgetType => widgetType.id === value);
        onSelect({
          selectedId: selected.id,
          isMulti: selected.is_multi
        });
      } else {
        onSelect({
          selectedId: null
        });
      }
    }
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/inspector-card.js

function InspectorCard(_ref) {
  let {
    name,
    description
  } = _ref;
  return Object(external_wp_element_["createElement"])("div", {
    className: "wp-block-legacy-widget-inspector-card"
  }, Object(external_wp_element_["createElement"])("h3", {
    className: "wp-block-legacy-widget-inspector-card__name"
  }, name), Object(external_wp_element_["createElement"])("span", null, description));
}

// EXTERNAL MODULE: external ["wp","notices"]
var external_wp_notices_ = __webpack_require__("onLe");

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__("ywyh");
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/control.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * An API for creating and loading a widget control (a <div class="widget">
 * element) that is compatible with most third party widget scripts. By not
 * using React for this, we ensure that we have complete contorl over the DOM
 * and do not accidentally remove any elements that a third party widget script
 * has attached an event listener to.
 *
 * @property {Element} element The control's DOM element.
 */

class control_Control {
  /**
   * Creates and loads a new control.
   *
   * @access public
   * @param {Object}   params
   * @param {string}   params.id
   * @param {string}   params.idBase
   * @param {Object}   params.instance
   * @param {Function} params.onChangeInstance
   * @param {Function} params.onChangeHasPreview
   * @param {Function} params.onError
   */
  constructor(_ref) {
    let {
      id,
      idBase,
      instance,
      onChangeInstance,
      onChangeHasPreview,
      onError
    } = _ref;
    this.id = id;
    this.idBase = idBase;
    this._instance = instance;
    this._hasPreview = null;
    this.onChangeInstance = onChangeInstance;
    this.onChangeHasPreview = onChangeHasPreview;
    this.onError = onError; // We can't use the real widget number as this is calculated by the
    // server and we may not ever *actually* save this widget. Instead, use
    // a fake but unique number.

    this.number = ++lastNumber;
    this.handleFormChange = Object(external_lodash_["debounce"])(this.handleFormChange.bind(this), 200);
    this.handleFormSubmit = this.handleFormSubmit.bind(this);
    this.initDOM();
    this.bindEvents();
    this.loadContent();
  }
  /**
   * Clean up the control so that it can be garabge collected.
   *
   * @access public
   */


  destroy() {
    this.unbindEvents();
    this.element.remove(); // TODO: How do we make third party widget scripts remove their event
    // listeners?
  }
  /**
   * Creates the control's DOM structure.
   *
   * @access private
   */


  initDOM() {
    var _this$id, _this$idBase;

    this.element = el('div', {
      class: 'widget open'
    }, [el('div', {
      class: 'widget-inside'
    }, [this.form = el('form', {
      class: 'form',
      method: 'post'
    }, [// These hidden form inputs are what most widgets' scripts
    // use to access data about the widget.
    el('input', {
      class: 'widget-id',
      type: 'hidden',
      name: 'widget-id',
      value: (_this$id = this.id) !== null && _this$id !== void 0 ? _this$id : `${this.idBase}-${this.number}`
    }), el('input', {
      class: 'id_base',
      type: 'hidden',
      name: 'id_base',
      value: (_this$idBase = this.idBase) !== null && _this$idBase !== void 0 ? _this$idBase : this.id
    }), el('input', {
      class: 'widget-width',
      type: 'hidden',
      name: 'widget-width',
      value: '250'
    }), el('input', {
      class: 'widget-height',
      type: 'hidden',
      name: 'widget-height',
      value: '200'
    }), el('input', {
      class: 'widget_number',
      type: 'hidden',
      name: 'widget_number',
      value: this.idBase ? this.number.toString() : ''
    }), this.content = el('div', {
      class: 'widget-content'
    }), // Non-multi widgets can be saved via a Save button.
    this.id && el('button', {
      class: 'button is-primary',
      type: 'submit'
    }, Object(external_wp_i18n_["__"])('Save'))])])]);
  }
  /**
   * Adds the control's event listeners.
   *
   * @access private
   */


  bindEvents() {
    // Prefer jQuery 'change' event instead of the native 'change' event
    // because many widgets use jQuery's event bus to trigger an update.
    if (window.jQuery) {
      const {
        jQuery: $
      } = window;
      $(this.form).on('change', null, this.handleFormChange);
      $(this.form).on('input', null, this.handleFormChange);
      $(this.form).on('submit', this.handleFormSubmit);
    } else {
      this.form.addEventListener('change', this.handleFormChange);
      this.form.addEventListener('input', this.handleFormChange);
      this.form.addEventListener('submit', this.handleFormSubmit);
    }
  }
  /**
   * Removes the control's event listeners.
   *
   * @access private
   */


  unbindEvents() {
    if (window.jQuery) {
      const {
        jQuery: $
      } = window;
      $(this.form).off('change', null, this.handleFormChange);
      $(this.form).off('input', null, this.handleFormChange);
      $(this.form).off('submit', this.handleFormSubmit);
    } else {
      this.form.removeEventListener('change', this.handleFormChange);
      this.form.removeEventListener('input', this.handleFormChange);
      this.form.removeEventListener('submit', this.handleFormSubmit);
    }
  }
  /**
   * Fetches the widget's form HTML from the REST API and loads it into the
   * control's form.
   *
   * @access private
   */


  async loadContent() {
    try {
      if (this.id) {
        const {
          form
        } = await saveWidget(this.id);
        this.content.innerHTML = form;
      } else if (this.idBase) {
        const {
          form,
          preview
        } = await encodeWidget({
          idBase: this.idBase,
          instance: this.instance,
          number: this.number
        });
        this.content.innerHTML = form;
        this.hasPreview = !isEmptyHTML(preview); // If we don't have an instance, perform a save right away. This
        // happens when creating a new Legacy Widget block.

        if (!this.instance.hash) {
          const {
            instance
          } = await encodeWidget({
            idBase: this.idBase,
            instance: this.instance,
            number: this.number,
            formData: serializeForm(this.form)
          });
          this.instance = instance;
        }
      } // Trigger 'widget-added' when widget is ready. This event is what
      // widgets' scripts use to initialize, attach events, etc. The event
      // must be fired using jQuery's event bus as this is what widget
      // scripts expect. If jQuery is not loaded, do nothing - some
      // widgets will still work regardless.


      if (window.jQuery) {
        const {
          jQuery: $
        } = window;
        $(document).trigger('widget-added', [$(this.element)]);
      }
    } catch (error) {
      this.onError(error);
    }
  }
  /**
   * Perform a save when a multi widget's form is changed. Non-multi widgets
   * are saved manually.
   *
   * @access private
   */


  handleFormChange() {
    if (this.idBase) {
      this.saveForm();
    }
  }
  /**
   * Perform a save when the control's form is manually submitted.
   *
   * @access private
   * @param {Event} event
   */


  handleFormSubmit(event) {
    event.preventDefault();
    this.saveForm();
  }
  /**
   * Serialize the control's form, send it to the REST API, and update the
   * instance with the encoded instance that the REST API returns.
   *
   * @access private
   */


  async saveForm() {
    const formData = serializeForm(this.form);

    try {
      if (this.id) {
        const {
          form
        } = await saveWidget(this.id, formData);
        this.content.innerHTML = form;

        if (window.jQuery) {
          const {
            jQuery: $
          } = window;
          $(document).trigger('widget-updated', [$(this.element)]);
        }
      } else if (this.idBase) {
        const {
          instance,
          preview
        } = await encodeWidget({
          idBase: this.idBase,
          instance: this.instance,
          number: this.number,
          formData
        });
        this.instance = instance;
        this.hasPreview = !isEmptyHTML(preview);
      }
    } catch (error) {
      this.onError(error);
    }
  }
  /**
   * The widget's instance object.
   *
   * @access private
   */


  get instance() {
    return this._instance;
  }
  /**
   * The widget's instance object.
   *
   * @access private
   */


  set instance(instance) {
    if (this._instance !== instance) {
      this._instance = instance;
      this.onChangeInstance(instance);
    }
  }
  /**
   * Whether or not the widget can be previewed.
   *
   * @access public
   */


  get hasPreview() {
    return this._hasPreview;
  }
  /**
   * Whether or not the widget can be previewed.
   *
   * @access private
   */


  set hasPreview(hasPreview) {
    if (this._hasPreview !== hasPreview) {
      this._hasPreview = hasPreview;
      this.onChangeHasPreview(hasPreview);
    }
  }

}
let lastNumber = 0;

function el(tagName) {
  let attributes = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  let content = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
  const element = document.createElement(tagName);

  for (const [attribute, value] of Object.entries(attributes)) {
    element.setAttribute(attribute, value);
  }

  if (Array.isArray(content)) {
    for (const child of content) {
      if (child) {
        element.appendChild(child);
      }
    }
  } else if (typeof content === 'string') {
    element.innerText = content;
  }

  return element;
}

async function saveWidget(id) {
  let formData = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  let widget;

  if (formData) {
    widget = await external_wp_apiFetch_default()({
      path: `/wp/v2/widgets/${id}?context=edit`,
      method: 'PUT',
      data: {
        form_data: formData
      }
    });
  } else {
    widget = await external_wp_apiFetch_default()({
      path: `/wp/v2/widgets/${id}?context=edit`,
      method: 'GET'
    });
  }

  return {
    form: widget.rendered_form
  };
}

async function encodeWidget(_ref2) {
  let {
    idBase,
    instance,
    number,
    formData = null
  } = _ref2;
  const response = await external_wp_apiFetch_default()({
    path: `/wp/v2/widget-types/${idBase}/encode`,
    method: 'POST',
    data: {
      instance,
      number,
      form_data: formData
    }
  });
  return {
    instance: response.instance,
    form: response.form,
    preview: response.preview
  };
}

function isEmptyHTML(html) {
  const element = document.createElement('div');
  element.innerHTML = html;
  return isEmptyNode(element);
}

function isEmptyNode(node) {
  switch (node.nodeType) {
    case node.TEXT_NODE:
      // Text nodes are empty if it's entirely whitespace.
      return node.nodeValue.trim() === '';

    case node.ELEMENT_NODE:
      // Elements that are "embedded content" are not empty.
      // https://dev.w3.org/html5/spec-LC/content-models.html#embedded-content-0
      if (['AUDIO', 'CANVAS', 'EMBED', 'IFRAME', 'IMG', 'MATH', 'OBJECT', 'SVG', 'VIDEO'].includes(node.tagName)) {
        return false;
      } // Elements with no children are empty.


      if (!node.hasChildNodes()) {
        return true;
      } // Elements with children are empty if all their children are empty.


      return Array.from(node.childNodes).every(isEmptyNode);

    default:
      return true;
  }
}

function serializeForm(form) {
  return new window.URLSearchParams(Array.from(new window.FormData(form))).toString();
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/form.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function Form(_ref) {
  let {
    title,
    isVisible,
    id,
    idBase,
    instance,
    isWide,
    onChangeInstance,
    onChangeHasPreview
  } = _ref;
  const ref = Object(external_wp_element_["useRef"])();
  const isMediumLargeViewport = Object(external_wp_compose_["useViewportMatch"])('small'); // We only want to remount the control when the instance changes
  // *externally*. For example, if the user performs an undo. To do this, we
  // keep track of changes made to instance by the control itself and then
  // ignore those.

  const outgoingInstances = Object(external_wp_element_["useRef"])(new Set());
  const incomingInstances = Object(external_wp_element_["useRef"])(new Set());
  const {
    createNotice
  } = Object(external_wp_data_["useDispatch"])(external_wp_notices_["store"]);
  Object(external_wp_element_["useEffect"])(() => {
    if (incomingInstances.current.has(instance)) {
      incomingInstances.current.delete(instance);
      return;
    }

    const control = new control_Control({
      id,
      idBase,
      instance,

      onChangeInstance(nextInstance) {
        outgoingInstances.current.add(instance);
        incomingInstances.current.add(nextInstance);
        onChangeInstance(nextInstance);
      },

      onChangeHasPreview,

      onError(error) {
        window.console.error(error);
        createNotice('error', Object(external_wp_i18n_["sprintf"])(
        /* translators: %s: the name of the affected block. */
        Object(external_wp_i18n_["__"])('The "%s" block was affected by errors and may not function properly. Check the developer tools for more details.'), idBase || id));
      }

    });
    ref.current.appendChild(control.element);
    return () => {
      if (outgoingInstances.current.has(instance)) {
        outgoingInstances.current.delete(instance);
        return;
      }

      control.destroy();
    };
  }, [id, idBase, instance, onChangeInstance, onChangeHasPreview, isMediumLargeViewport]);

  if (isWide && isMediumLargeViewport) {
    return Object(external_wp_element_["createElement"])("div", {
      className: classnames_default()({
        'wp-block-legacy-widget__container': isVisible
      })
    }, isVisible && Object(external_wp_element_["createElement"])("h3", {
      className: "wp-block-legacy-widget__edit-form-title"
    }, title), Object(external_wp_element_["createElement"])(external_wp_components_["Popover"], {
      focusOnMount: false,
      position: "middle right",
      __unstableForceXAlignment: true
    }, Object(external_wp_element_["createElement"])("div", {
      ref: ref,
      className: "wp-block-legacy-widget__edit-form",
      hidden: !isVisible
    })));
  }

  return Object(external_wp_element_["createElement"])("div", {
    ref: ref,
    className: "wp-block-legacy-widget__edit-form",
    hidden: !isVisible
  }, Object(external_wp_element_["createElement"])("h3", {
    className: "wp-block-legacy-widget__edit-form-title"
  }, title));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/preview.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






function Preview(_ref) {
  let {
    idBase,
    instance,
    isVisible
  } = _ref;
  const [isLoaded, setIsLoaded] = Object(external_wp_element_["useState"])(false);
  const [srcDoc, setSrcDoc] = Object(external_wp_element_["useState"])('');
  Object(external_wp_element_["useEffect"])(() => {
    const abortController = typeof window.AbortController === 'undefined' ? undefined : new window.AbortController();

    async function fetchPreviewHTML() {
      const restRoute = `/wp/v2/widget-types/${idBase}/render`;
      return await external_wp_apiFetch_default()({
        path: restRoute,
        method: 'POST',
        signal: abortController === null || abortController === void 0 ? void 0 : abortController.signal,
        data: instance ? {
          instance
        } : {}
      });
    }

    fetchPreviewHTML().then(response => {
      setSrcDoc(response.preview);
    }).catch(error => {
      if ('AbortError' === error.name) {
        // We don't want to log aborted requests.
        return;
      }

      throw error;
    });
    return () => abortController === null || abortController === void 0 ? void 0 : abortController.abort();
  }, [idBase, instance]); // Resize the iframe on either the load event, or when the iframe becomes visible.

  const ref = Object(external_wp_compose_["useRefEffect"])(iframe => {
    // Only set height if the iframe is loaded,
    // or it will grow to an unexpected large height in Safari if it's hidden initially.
    if (!isLoaded) {
      return;
    } // If the preview frame has another origin then this won't work.
    // One possible solution is to add custom script to call `postMessage` in the preview frame.
    // Or, better yet, we migrate away from iframe.


    function setHeight() {
      // Pick the maximum of these two values to account for margin collapsing.
      const height = Math.max(iframe.contentDocument.documentElement.offsetHeight, iframe.contentDocument.body.offsetHeight);
      iframe.style.height = `${height}px`;
    }

    const {
      IntersectionObserver
    } = iframe.ownerDocument.defaultView; // Observe for intersections that might cause a change in the height of
    // the iframe, e.g. a Widget Area becoming expanded.

    const intersectionObserver = new IntersectionObserver(_ref2 => {
      let [entry] = _ref2;

      if (entry.isIntersecting) {
        setHeight();
      }
    }, {
      threshold: 1
    });
    intersectionObserver.observe(iframe);
    iframe.addEventListener('load', setHeight);
    return () => {
      intersectionObserver.disconnect();
      iframe.removeEventListener('load', setHeight);
    };
  }, [isLoaded]);
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, isVisible && !isLoaded && Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null)), Object(external_wp_element_["createElement"])("div", {
    className: classnames_default()('wp-block-legacy-widget__edit-preview', {
      'is-offscreen': !isVisible || !isLoaded
    })
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Disabled"], null, Object(external_wp_element_["createElement"])("iframe", {
    ref: ref,
    className: "wp-block-legacy-widget__edit-preview-iframe",
    tabIndex: "-1",
    title: Object(external_wp_i18n_["__"])('Legacy Widget Preview'),
    srcDoc: srcDoc,
    onLoad: event => {
      // To hide the scrollbars of the preview frame for some edge cases,
      // such as negative margins in the Gallery Legacy Widget.
      // It can't be scrolled anyway.
      // TODO: Ideally, this should be fixed in core.
      event.target.contentDocument.body.style.overflow = 'hidden';
      setIsLoaded(true);
    },
    height: 100
  }))));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/no-preview.js


/**
 * WordPress dependencies
 */

function NoPreview(_ref) {
  let {
    name
  } = _ref;
  return Object(external_wp_element_["createElement"])("div", {
    className: "wp-block-legacy-widget__edit-no-preview"
  }, name && Object(external_wp_element_["createElement"])("h3", null, name), Object(external_wp_element_["createElement"])("p", null, Object(external_wp_i18n_["__"])('No preview available.')));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/convert-to-blocks-button.js


/**
 * WordPress dependencies
 */





function ConvertToBlocksButton(_ref) {
  let {
    clientId,
    rawInstance
  } = _ref;
  const {
    replaceBlocks
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  return Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarButton"], {
    onClick: () => {
      if (rawInstance.title) {
        replaceBlocks(clientId, [Object(external_wp_blocks_["createBlock"])('core/heading', {
          content: rawInstance.title
        }), ...Object(external_wp_blocks_["rawHandler"])({
          HTML: rawInstance.text
        })]);
      } else {
        replaceBlocks(clientId, Object(external_wp_blocks_["rawHandler"])({
          HTML: rawInstance.text
        }));
      }
    }
  }, Object(external_wp_i18n_["__"])('Convert to blocks'));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */







function Edit(props) {
  const {
    id,
    idBase
  } = props.attributes;
  const {
    isWide = false
  } = props;
  const blockProps = Object(external_wp_blockEditor_["useBlockProps"])({
    className: classnames_default()({
      'is-wide-widget': isWide
    })
  });
  return Object(external_wp_element_["createElement"])("div", blockProps, !id && !idBase ? Object(external_wp_element_["createElement"])(Empty, props) : Object(external_wp_element_["createElement"])(NotEmpty, props));
}

function Empty(_ref) {
  let {
    attributes: {
      id,
      idBase
    },
    setAttributes
  } = _ref;
  return Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], {
    icon: Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockIcon"], {
      icon: library_brush
    }),
    label: Object(external_wp_i18n_["__"])('Legacy Widget')
  }, Object(external_wp_element_["createElement"])(WidgetTypeSelector, {
    selectedId: id !== null && id !== void 0 ? id : idBase,
    onSelect: _ref2 => {
      let {
        selectedId,
        isMulti
      } = _ref2;

      if (!selectedId) {
        setAttributes({
          id: null,
          idBase: null,
          instance: null
        });
      } else if (isMulti) {
        setAttributes({
          id: null,
          idBase: selectedId,
          instance: {}
        });
      } else {
        setAttributes({
          id: selectedId,
          idBase: null,
          instance: null
        });
      }
    }
  }));
}

function NotEmpty(_ref3) {
  let {
    attributes: {
      id,
      idBase,
      instance
    },
    setAttributes,
    clientId,
    isSelected,
    isWide = false
  } = _ref3;
  const [hasPreview, setHasPreview] = Object(external_wp_element_["useState"])(null);
  const {
    widgetType,
    hasResolvedWidgetType,
    isNavigationMode
  } = Object(external_wp_data_["useSelect"])(select => {
    const widgetTypeId = id !== null && id !== void 0 ? id : idBase;
    return {
      widgetType: select(external_wp_coreData_["store"]).getWidgetType(widgetTypeId),
      hasResolvedWidgetType: select(external_wp_coreData_["store"]).hasFinishedResolution('getWidgetType', [widgetTypeId]),
      isNavigationMode: select(external_wp_blockEditor_["store"]).isNavigationMode()
    };
  }, [id, idBase]);
  const setInstance = Object(external_wp_element_["useCallback"])(nextInstance => {
    setAttributes({
      instance: nextInstance
    });
  }, []);

  if (!widgetType && hasResolvedWidgetType) {
    return Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], {
      icon: Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockIcon"], {
        icon: library_brush
      }),
      label: Object(external_wp_i18n_["__"])('Legacy Widget')
    }, Object(external_wp_i18n_["__"])('Widget is missing.'));
  }

  if (!hasResolvedWidgetType) {
    return Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null));
  }

  const mode = idBase && (isNavigationMode || !isSelected) ? 'preview' : 'edit';
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, idBase === 'text' && Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockControls"], {
    group: "other"
  }, Object(external_wp_element_["createElement"])(ConvertToBlocksButton, {
    clientId: clientId,
    rawInstance: instance.raw
  })), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["InspectorControls"], null, Object(external_wp_element_["createElement"])(InspectorCard, {
    name: widgetType.name,
    description: widgetType.description
  })), Object(external_wp_element_["createElement"])(Form, {
    title: widgetType.name,
    isVisible: mode === 'edit',
    id: id,
    idBase: idBase,
    instance: instance,
    isWide: isWide,
    onChangeInstance: setInstance,
    onChangeHasPreview: setHasPreview
  }), idBase && Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, hasPreview === null && mode === 'preview' && Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null)), hasPreview === true && Object(external_wp_element_["createElement"])(Preview, {
    idBase: idBase,
    instance: instance,
    isVisible: mode === 'preview'
  }), hasPreview === false && mode === 'preview' && Object(external_wp_element_["createElement"])(NoPreview, {
    name: widgetType.name
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/transforms.js
/**
 * WordPress dependencies
 */

const legacyWidgetTransforms = [{
  block: 'core/calendar',
  widget: 'calendar'
}, {
  block: 'core/search',
  widget: 'search'
}, {
  block: 'core/html',
  widget: 'custom_html',
  transform: _ref => {
    let {
      content
    } = _ref;
    return {
      content
    };
  }
}, {
  block: 'core/archives',
  widget: 'archives',
  transform: _ref2 => {
    let {
      count,
      dropdown
    } = _ref2;
    return {
      displayAsDropdown: !!dropdown,
      showPostCounts: !!count
    };
  }
}, {
  block: 'core/latest-posts',
  widget: 'recent-posts',
  transform: _ref3 => {
    let {
      show_date: displayPostDate,
      number
    } = _ref3;
    return {
      displayPostDate: !!displayPostDate,
      postsToShow: number
    };
  }
}, {
  block: 'core/latest-comments',
  widget: 'recent-comments',
  transform: _ref4 => {
    let {
      number
    } = _ref4;
    return {
      commentsToShow: number
    };
  }
}, {
  block: 'core/tag-cloud',
  widget: 'tag_cloud',
  transform: _ref5 => {
    let {
      taxonomy,
      count
    } = _ref5;
    return {
      showTagCounts: !!count,
      taxonomy
    };
  }
}, {
  block: 'core/categories',
  widget: 'categories',
  transform: _ref6 => {
    let {
      count,
      dropdown,
      hierarchical
    } = _ref6;
    return {
      displayAsDropdown: !!dropdown,
      showPostCounts: !!count,
      showHierarchy: !!hierarchical
    };
  }
}, {
  block: 'core/audio',
  widget: 'media_audio',
  transform: _ref7 => {
    let {
      url,
      preload,
      loop,
      attachment_id: id
    } = _ref7;
    return {
      src: url,
      id,
      preload,
      loop
    };
  }
}, {
  block: 'core/video',
  widget: 'media_video',
  transform: _ref8 => {
    let {
      url,
      preload,
      loop,
      attachment_id: id
    } = _ref8;
    return {
      src: url,
      id,
      preload,
      loop
    };
  }
}, {
  block: 'core/image',
  widget: 'media_image',
  transform: _ref9 => {
    let {
      alt,
      attachment_id: id,
      caption,
      height,
      link_classes: linkClass,
      link_rel: rel,
      link_target_blank: targetBlack,
      link_type: linkDestination,
      link_url: link,
      size: sizeSlug,
      url,
      width
    } = _ref9;
    return {
      alt,
      caption,
      height,
      id,
      link,
      linkClass,
      linkDestination,
      linkTarget: targetBlack ? '_blank' : undefined,
      rel,
      sizeSlug,
      url,
      width
    };
  }
}, {
  block: 'core/gallery',
  widget: 'media_gallery',
  transform: _ref10 => {
    let {
      ids,
      link_type: linkTo,
      size,
      number
    } = _ref10;
    return {
      ids,
      columns: number,
      linkTo,
      sizeSlug: size,
      images: ids.map(id => ({
        id
      }))
    };
  }
}, {
  block: 'core/rss',
  widget: 'rss',
  transform: _ref11 => {
    let {
      url,
      show_author: displayAuthor,
      show_date: displayDate,
      show_summary: displayExcerpt,
      items
    } = _ref11;
    return {
      feedURL: url,
      displayAuthor: !!displayAuthor,
      displayDate: !!displayDate,
      displayExcerpt: !!displayExcerpt,
      itemsToShow: items
    };
  }
}].map(_ref12 => {
  let {
    block,
    widget,
    transform
  } = _ref12;
  return {
    type: 'block',
    blocks: [block],
    isMatch: _ref13 => {
      let {
        idBase,
        instance
      } = _ref13;
      return idBase === widget && !!(instance !== null && instance !== void 0 && instance.raw);
    },
    transform: _ref14 => {
      var _instance$raw;

      let {
        instance
      } = _ref14;
      const transformedBlock = Object(external_wp_blocks_["createBlock"])(block, transform ? transform(instance.raw) : undefined);

      if (!((_instance$raw = instance.raw) !== null && _instance$raw !== void 0 && _instance$raw.title)) {
        return transformedBlock;
      }

      return [Object(external_wp_blocks_["createBlock"])('core/heading', {
        content: instance.raw.title
      }), transformedBlock];
    }
  };
});
const transforms = {
  to: legacyWidgetTransforms
};
/* harmony default export */ var legacy_widget_transforms = (transforms);

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

const legacy_widget_metadata = {
  apiVersion: 2,
  name: "core/legacy-widget",
  title: "Legacy Widget",
  category: "widgets",
  description: "Display a legacy widget.",
  textdomain: "default",
  attributes: {
    id: {
      type: "string",
      "default": null
    },
    idBase: {
      type: "string",
      "default": null
    },
    instance: {
      type: "object",
      "default": null
    }
  },
  supports: {
    html: false,
    customClassName: false,
    reusable: false
  },
  editorStyle: "wp-block-legacy-widget-editor"
};


const {
  name: legacy_widget_name
} = legacy_widget_metadata;

const legacy_widget_settings = {
  icon: library_widget,
  edit: Edit,
  transforms: legacy_widget_transforms
};

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/group.js
var group = __webpack_require__("u6za");

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/edit.js


/**
 * WordPress dependencies
 */





function edit_Edit(props) {
  const {
    clientId
  } = props;
  const {
    innerBlocks
  } = Object(external_wp_data_["useSelect"])(select => select(external_wp_blockEditor_["store"]).getBlock(clientId), [clientId]);
  return Object(external_wp_element_["createElement"])("div", Object(external_wp_blockEditor_["useBlockProps"])({
    className: 'widget'
  }), innerBlocks.length === 0 ? Object(external_wp_element_["createElement"])(PlaceholderContent, props) : Object(external_wp_element_["createElement"])(PreviewContent, props));
}

function PlaceholderContent(_ref) {
  let {
    clientId
  } = _ref;
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], {
    className: "wp-block-widget-group__placeholder",
    icon: Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockIcon"], {
      icon: group["a" /* default */]
    }),
    label: Object(external_wp_i18n_["__"])('Widget Group')
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["ButtonBlockAppender"], {
    rootClientId: clientId
  })), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["InnerBlocks"], {
    renderAppender: false
  }));
}

function PreviewContent(_ref2) {
  var _attributes$title;

  let {
    attributes,
    setAttributes
  } = _ref2;
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["RichText"], {
    tagName: "h2",
    className: "widget-title",
    allowedFormats: [],
    placeholder: Object(external_wp_i18n_["__"])('Title'),
    value: (_attributes$title = attributes.title) !== null && _attributes$title !== void 0 ? _attributes$title : '',
    onChange: title => setAttributes({
      title
    })
  }), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["InnerBlocks"], null));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/save.js


/**
 * WordPress dependencies
 */

function save(_ref) {
  let {
    attributes
  } = _ref;
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["RichText"].Content, {
    tagName: "h2",
    className: "widget-title",
    value: attributes.title
  }), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["InnerBlocks"].Content, null));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

const widget_group_metadata = {
  apiVersion: 2,
  name: "core/widget-group",
  category: "widgets",
  attributes: {
    title: {
      type: "string"
    }
  },
  supports: {
    html: false,
    inserter: true,
    customClassName: true,
    reusable: false
  },
  editorStyle: "wp-block-widget-group-editor",
  style: "wp-block-widget-group"
};


const {
  name: widget_group_name
} = widget_group_metadata;

const widget_group_settings = {
  title: Object(external_wp_i18n_["__"])('Widget Group'),
  description: Object(external_wp_i18n_["__"])('Create a classic widget layout with a title that’s styled by your theme for your widget areas.'),
  icon: group["a" /* default */],
  __experimentalLabel: _ref => {
    let {
      name: label
    } = _ref;
    return label;
  },
  edit: edit_Edit,
  save: save,
  transforms: {
    from: [{
      type: 'block',
      isMultiBlock: true,
      blocks: ['*'],

      isMatch(attributes, blocks) {
        // Avoid transforming existing `widget-group` blocks.
        return !blocks.some(block => block.name === 'core/widget-group');
      },

      __experimentalConvert(blocks) {
        // Put the selected blocks inside the new Widget Group's innerBlocks.
        let innerBlocks = [...blocks.map(block => {
          return Object(external_wp_blocks_["createBlock"])(block.name, block.attributes, block.innerBlocks);
        })]; // If the first block is a heading then assume this is intended
        // to be the Widget's "title".

        const firstHeadingBlock = innerBlocks[0].name === 'core/heading' ? innerBlocks[0] : null; // Remove the first heading block as we're copying
        // it's content into the Widget Group's title attribute.

        innerBlocks = innerBlocks.filter(block => block !== firstHeadingBlock);
        return Object(external_wp_blocks_["createBlock"])('core/widget-group', { ...(firstHeadingBlock && {
            title: firstHeadingBlock.attributes.content
          })
        }, innerBlocks);
      }

    }]
  }
};

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/move-to.js


/**
 * WordPress dependencies
 */

const moveTo = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  d: "M19.75 9c0-1.257-.565-2.197-1.39-2.858-.797-.64-1.827-1.017-2.815-1.247-1.802-.42-3.703-.403-4.383-.396L11 4.5V6l.177-.001c.696-.006 2.416-.02 4.028.356.887.207 1.67.518 2.216.957.52.416.829.945.829 1.688 0 .592-.167.966-.407 1.23-.255.281-.656.508-1.236.674-1.19.34-2.82.346-4.607.346h-.077c-1.692 0-3.527 0-4.942.404-.732.209-1.424.545-1.935 1.108-.526.579-.796 1.33-.796 2.238 0 1.257.565 2.197 1.39 2.858.797.64 1.827 1.017 2.815 1.247 1.802.42 3.703.403 4.383.396L13 19.5h.714V22L18 18.5 13.714 15v3H13l-.177.001c-.696.006-2.416.02-4.028-.356-.887-.207-1.67-.518-2.216-.957-.52-.416-.829-.945-.829-1.688 0-.592.167-.966.407-1.23.255-.281.656-.508 1.237-.674 1.189-.34 2.819-.346 4.606-.346h.077c1.692 0 3.527 0 4.941-.404.732-.209 1.425-.545 1.936-1.108.526-.579.796-1.33.796-2.238z"
}));
/* harmony default export */ var move_to = (moveTo);

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/components/move-to-widget-area/index.js


/**
 * WordPress dependencies
 */



function MoveToWidgetArea(_ref) {
  let {
    currentWidgetAreaId,
    widgetAreas,
    onSelect
  } = _ref;
  return Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarGroup"], null, Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarItem"], null, toggleProps => Object(external_wp_element_["createElement"])(external_wp_components_["DropdownMenu"], {
    icon: move_to,
    label: Object(external_wp_i18n_["__"])('Move to widget area'),
    toggleProps: toggleProps
  }, _ref2 => {
    let {
      onClose
    } = _ref2;
    return Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], {
      label: Object(external_wp_i18n_["__"])('Move to')
    }, Object(external_wp_element_["createElement"])(external_wp_components_["MenuItemsChoice"], {
      choices: widgetAreas.map(widgetArea => ({
        value: widgetArea.id,
        label: widgetArea.name,
        info: widgetArea.description
      })),
      value: currentWidgetAreaId,
      onSelect: value => {
        onSelect(value);
        onClose();
      }
    }));
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/components/index.js


// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/utils.js
// @ts-check

/**
 * Get the internal widget id from block.
 *
 * @typedef  {Object} Attributes
 * @property {string}     __internalWidgetId The internal widget id.
 * @typedef  {Object} Block
 * @property {Attributes} attributes         The attributes of the block.
 *
 * @param    {Block}      block              The block.
 * @return {string} The internal widget id.
 */
function getWidgetIdFromBlock(block) {
  return block.attributes.__internalWidgetId;
}
/**
 * Add internal widget id to block's attributes.
 *
 * @param {Block}  block    The block.
 * @param {string} widgetId The widget id.
 * @return {Block} The updated block.
 */

function addWidgetIdToBlock(block, widgetId) {
  return { ...block,
    attributes: { ...(block.attributes || {}),
      __internalWidgetId: widgetId
    }
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/register-legacy-widget-variations.js
/**
 * WordPress dependencies
 */



function registerLegacyWidgetVariations(settings) {
  const unsubscribe = Object(external_wp_data_["subscribe"])(() => {
    var _settings$widgetTypes, _select$getWidgetType;

    const hiddenIds = (_settings$widgetTypes = settings === null || settings === void 0 ? void 0 : settings.widgetTypesToHideFromLegacyWidgetBlock) !== null && _settings$widgetTypes !== void 0 ? _settings$widgetTypes : [];
    const widgetTypes = (_select$getWidgetType = Object(external_wp_data_["select"])(external_wp_coreData_["store"]).getWidgetTypes({
      per_page: -1
    })) === null || _select$getWidgetType === void 0 ? void 0 : _select$getWidgetType.filter(widgetType => !hiddenIds.includes(widgetType.id));

    if (widgetTypes) {
      unsubscribe();
      Object(external_wp_data_["dispatch"])(external_wp_blocks_["store"]).addBlockVariations('core/legacy-widget', widgetTypes.map(widgetType => ({
        name: widgetType.id,
        title: widgetType.name,
        description: widgetType.description,
        attributes: widgetType.is_multi ? {
          idBase: widgetType.id,
          instance: {}
        } : {
          id: widgetType.id
        }
      })));
    }
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Registers the Legacy Widget block.
 *
 * Note that for the block to be useful, any scripts required by a widget must
 * be loaded into the page.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/widgets/legacy-widget-block/
 */

function registerLegacyWidgetBlock() {
  const {
    metadata,
    settings,
    name
  } = legacy_widget_namespaceObject;
  Object(external_wp_blocks_["registerBlockType"])({
    name,
    ...metadata
  }, settings);
}
/**
 * Registers the Widget Group block.
 */

function registerWidgetGroupBlock() {
  const {
    metadata,
    settings,
    name
  } = widget_group_namespaceObject;
  Object(external_wp_blocks_["registerBlockType"])({
    name,
    ...metadata
  }, settings);
}



/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "TSYQ":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ "Tqx9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "axFQ":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockEditor"]; }());

/***/ }),

/***/ "jZUy":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["coreData"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "onLe":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["notices"]; }());

/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "u6za":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const group = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 4h-7c-1.1 0-2 .9-2 2v3H6c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2v-3h3c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4.5 14c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5v-7c0-.3.2-.5.5-.5h3V13c0 1.1.9 2 2 2h2.5v3zm0-4.5H11c-.3 0-.5-.2-.5-.5v-2.5H13c.3 0 .5.2.5.5v2.5zm5-.5c0 .3-.2.5-.5.5h-3V11c0-1.1-.9-2-2-2h-2.5V6c0-.3.2-.5.5-.5h7c.3 0 .5.2.5.5v7z"
}));
/* harmony default export */ __webpack_exports__["a"] = (group);


/***/ }),

/***/ "ywyh":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ })

/******/ });