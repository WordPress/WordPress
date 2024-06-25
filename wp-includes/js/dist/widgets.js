/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  MoveToWidgetArea: () => (/* reexport */ MoveToWidgetArea),
  addWidgetIdToBlock: () => (/* reexport */ addWidgetIdToBlock),
  getWidgetIdFromBlock: () => (/* reexport */ getWidgetIdFromBlock),
  registerLegacyWidgetBlock: () => (/* binding */ registerLegacyWidgetBlock),
  registerLegacyWidgetVariations: () => (/* reexport */ registerLegacyWidgetVariations),
  registerWidgetGroupBlock: () => (/* binding */ registerWidgetGroupBlock)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/index.js
var legacy_widget_namespaceObject = {};
__webpack_require__.r(legacy_widget_namespaceObject);
__webpack_require__.d(legacy_widget_namespaceObject, {
  metadata: () => (metadata),
  name: () => (legacy_widget_name),
  settings: () => (settings)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/index.js
var widget_group_namespaceObject = {};
__webpack_require__.r(widget_group_namespaceObject);
__webpack_require__.d(widget_group_namespaceObject, {
  metadata: () => (widget_group_metadata),
  name: () => (widget_group_name),
  settings: () => (widget_group_settings)
});

;// CONCATENATED MODULE: external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/widget.js
/**
 * WordPress dependencies
 */


const widget = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M6 3H8V5H16V3H18V5C19.1046 5 20 5.89543 20 7V19C20 20.1046 19.1046 21 18 21H6C4.89543 21 4 20.1046 4 19V7C4 5.89543 4.89543 5 6 5V3ZM18 6.5H6C5.72386 6.5 5.5 6.72386 5.5 7V8H18.5V7C18.5 6.72386 18.2761 6.5 18 6.5ZM18.5 9.5H5.5V19C5.5 19.2761 5.72386 19.5 6 19.5H18C18.2761 19.5 18.5 19.2761 18.5 19V9.5ZM11 11H13V13H11V11ZM7 11V13H9V11H7ZM15 13V11H17V13H15Z"
  })
});
/* harmony default export */ const library_widget = (widget);

;// CONCATENATED MODULE: ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
;// CONCATENATED MODULE: external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/brush.js
/**
 * WordPress dependencies
 */


const brush = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M4 20h8v-1.5H4V20zM18.9 3.5c-.6-.6-1.5-.6-2.1 0l-7.2 7.2c-.4-.1-.7 0-1.1.1-.5.2-1.5.7-1.9 2.2-.4 1.7-.8 2.2-1.1 2.7-.1.1-.2.3-.3.4l-.6 1.1H6c2 0 3.4-.4 4.7-1.4.8-.6 1.2-1.4 1.3-2.3 0-.3 0-.5-.1-.7L19 5.7c.5-.6.5-1.6-.1-2.2zM9.7 14.7c-.7.5-1.5.8-2.4 1 .2-.5.5-1.2.8-2.3.2-.6.4-1 .8-1.1.5-.1 1 .1 1.3.3.2.2.3.5.2.8 0 .3-.1.9-.7 1.3z"
  })
});
/* harmony default export */ const library_brush = (brush);

;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/widget-type-selector.js
/**
 * WordPress dependencies
 */






function WidgetTypeSelector({
  selectedId,
  onSelect
}) {
  const widgetTypes = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getSettings$w;
    const hiddenIds = (_select$getSettings$w = select(external_wp_blockEditor_namespaceObject.store).getSettings()?.widgetTypesToHideFromLegacyWidgetBlock) !== null && _select$getSettings$w !== void 0 ? _select$getSettings$w : [];
    return select(external_wp_coreData_namespaceObject.store).getWidgetTypes({
      per_page: -1
    })?.filter(widgetType => !hiddenIds.includes(widgetType.id));
  }, []);
  if (!widgetTypes) {
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {});
  }
  if (widgetTypes.length === 0) {
    return (0,external_wp_i18n_namespaceObject.__)('There are no widgets available.');
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.SelectControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Select a legacy widget to display:'),
    value: selectedId !== null && selectedId !== void 0 ? selectedId : '',
    options: [{
      value: '',
      label: (0,external_wp_i18n_namespaceObject.__)('Select widget')
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/inspector-card.js


function InspectorCard({
  name,
  description
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
    className: "wp-block-legacy-widget-inspector-card",
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h3", {
      className: "wp-block-legacy-widget-inspector-card__name",
      children: name
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("span", {
      children: description
    })]
  });
}

;// CONCATENATED MODULE: external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/control.js
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
class Control {
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
  constructor({
    id,
    idBase,
    instance,
    onChangeInstance,
    onChangeHasPreview,
    onError
  }) {
    this.id = id;
    this.idBase = idBase;
    this._instance = instance;
    this._hasPreview = null;
    this.onChangeInstance = onChangeInstance;
    this.onChangeHasPreview = onChangeHasPreview;
    this.onError = onError;

    // We can't use the real widget number as this is calculated by the
    // server and we may not ever *actually* save this widget. Instead, use
    // a fake but unique number.
    this.number = ++lastNumber;
    this.handleFormChange = (0,external_wp_compose_namespaceObject.debounce)(this.handleFormChange.bind(this), 200);
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
    this.element.remove();
    // TODO: How do we make third party widget scripts remove their event
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
    }, [
    // These hidden form inputs are what most widgets' scripts
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
    }),
    // Non-multi widgets can be saved via a Save button.
    this.id && el('button', {
      class: 'button is-primary',
      type: 'submit'
    }, (0,external_wp_i18n_namespaceObject.__)('Save'))])])]);
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
        this.hasPreview = !isEmptyHTML(preview);

        // If we don't have an instance, perform a save right away. This
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
      }

      // Trigger 'widget-added' when widget is ready. This event is what
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
function el(tagName, attributes = {}, content = null) {
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
async function saveWidget(id, formData = null) {
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
async function encodeWidget({
  idBase,
  instance,
  number,
  formData = null
}) {
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
      }
      // Elements with no children are empty.
      if (!node.hasChildNodes()) {
        return true;
      }
      // Elements with children are empty if all their children are empty.
      return Array.from(node.childNodes).every(isEmptyNode);
    default:
      return true;
  }
}
function serializeForm(form) {
  return new window.URLSearchParams(Array.from(new window.FormData(form))).toString();
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/form.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function Form({
  title,
  isVisible,
  id,
  idBase,
  instance,
  isWide,
  onChangeInstance,
  onChangeHasPreview
}) {
  const ref = (0,external_wp_element_namespaceObject.useRef)();
  const isMediumLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('small');

  // We only want to remount the control when the instance changes
  // *externally*. For example, if the user performs an undo. To do this, we
  // keep track of changes made to instance by the control itself and then
  // ignore those.
  const outgoingInstances = (0,external_wp_element_namespaceObject.useRef)(new Set());
  const incomingInstances = (0,external_wp_element_namespaceObject.useRef)(new Set());
  const {
    createNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (incomingInstances.current.has(instance)) {
      incomingInstances.current.delete(instance);
      return;
    }
    const control = new Control({
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
        createNotice('error', (0,external_wp_i18n_namespaceObject.sprintf)( /* translators: %s: the name of the affected block. */
        (0,external_wp_i18n_namespaceObject.__)('The "%s" block was affected by errors and may not function properly. Check the developer tools for more details.'), idBase || id));
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
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
      className: dist_clsx({
        'wp-block-legacy-widget__container': isVisible
      }),
      children: [isVisible && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h3", {
        className: "wp-block-legacy-widget__edit-form-title",
        children: title
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Popover, {
        focusOnMount: false,
        placement: "right",
        offset: 32,
        resize: false,
        flip: false,
        shift: true,
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
          ref: ref,
          className: "wp-block-legacy-widget__edit-form",
          hidden: !isVisible
        })
      })]
    });
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
    ref: ref,
    className: "wp-block-legacy-widget__edit-form",
    hidden: !isVisible,
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h3", {
      className: "wp-block-legacy-widget__edit-form-title",
      children: title
    })
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/preview.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */








function Preview({
  idBase,
  instance,
  isVisible
}) {
  const [isLoaded, setIsLoaded] = (0,external_wp_element_namespaceObject.useState)(false);
  const [srcDoc, setSrcDoc] = (0,external_wp_element_namespaceObject.useState)('');
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const abortController = typeof window.AbortController === 'undefined' ? undefined : new window.AbortController();
    async function fetchPreviewHTML() {
      const restRoute = `/wp/v2/widget-types/${idBase}/render`;
      return await external_wp_apiFetch_default()({
        path: restRoute,
        method: 'POST',
        signal: abortController?.signal,
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
    return () => abortController?.abort();
  }, [idBase, instance]);

  // Resize the iframe on either the load event, or when the iframe becomes visible.
  const ref = (0,external_wp_compose_namespaceObject.useRefEffect)(iframe => {
    // Only set height if the iframe is loaded,
    // or it will grow to an unexpected large height in Safari if it's hidden initially.
    if (!isLoaded) {
      return;
    }
    // If the preview frame has another origin then this won't work.
    // One possible solution is to add custom script to call `postMessage` in the preview frame.
    // Or, better yet, we migrate away from iframe.
    function setHeight() {
      var _iframe$contentDocume, _iframe$contentDocume2;
      // Pick the maximum of these two values to account for margin collapsing.
      const height = Math.max((_iframe$contentDocume = iframe.contentDocument.documentElement?.offsetHeight) !== null && _iframe$contentDocume !== void 0 ? _iframe$contentDocume : 0, (_iframe$contentDocume2 = iframe.contentDocument.body?.offsetHeight) !== null && _iframe$contentDocume2 !== void 0 ? _iframe$contentDocume2 : 0);

      // Fallback to a height of 100px if the height cannot be determined.
      // This ensures the block is still selectable. 100px should hopefully
      // be not so big that it's annoying, and not so small that nothing
      // can be seen.
      iframe.style.height = `${height !== 0 ? height : 100}px`;
    }
    const {
      IntersectionObserver
    } = iframe.ownerDocument.defaultView;

    // Observe for intersections that might cause a change in the height of
    // the iframe, e.g. a Widget Area becoming expanded.
    const intersectionObserver = new IntersectionObserver(([entry]) => {
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
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [isVisible && !isLoaded && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, {
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {})
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: dist_clsx('wp-block-legacy-widget__edit-preview', {
        'is-offscreen': !isVisible || !isLoaded
      }),
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Disabled, {
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("iframe", {
          ref: ref,
          className: "wp-block-legacy-widget__edit-preview-iframe",
          tabIndex: "-1",
          title: (0,external_wp_i18n_namespaceObject.__)('Legacy Widget Preview'),
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
        })
      })
    })]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/no-preview.js
/**
 * WordPress dependencies
 */



function NoPreview({
  name
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
    className: "wp-block-legacy-widget__edit-no-preview",
    children: [name && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h3", {
      children: name
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
      children: (0,external_wp_i18n_namespaceObject.__)('No preview available.')
    })]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/convert-to-blocks-button.js
/**
 * WordPress dependencies
 */






function ConvertToBlocksButton({
  clientId,
  rawInstance
}) {
  const {
    replaceBlocks
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarButton, {
    onClick: () => {
      if (rawInstance.title) {
        replaceBlocks(clientId, [(0,external_wp_blocks_namespaceObject.createBlock)('core/heading', {
          content: rawInstance.title
        }), ...(0,external_wp_blocks_namespaceObject.rawHandler)({
          HTML: rawInstance.text
        })]);
      } else {
        replaceBlocks(clientId, (0,external_wp_blocks_namespaceObject.rawHandler)({
          HTML: rawInstance.text
        }));
      }
    },
    children: (0,external_wp_i18n_namespaceObject.__)('Convert to blocks')
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/index.js
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
  const blockProps = (0,external_wp_blockEditor_namespaceObject.useBlockProps)({
    className: dist_clsx({
      'is-wide-widget': isWide
    })
  });
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
    ...blockProps,
    children: !id && !idBase ? /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Empty, {
      ...props
    }) : /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(NotEmpty, {
      ...props
    })
  });
}
function Empty({
  attributes: {
    id,
    idBase
  },
  setAttributes
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, {
    icon: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockIcon, {
      icon: library_brush
    }),
    label: (0,external_wp_i18n_namespaceObject.__)('Legacy Widget'),
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Flex, {
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.FlexBlock, {
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WidgetTypeSelector, {
          selectedId: id !== null && id !== void 0 ? id : idBase,
          onSelect: ({
            selectedId,
            isMulti
          }) => {
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
        })
      })
    })
  });
}
function NotEmpty({
  attributes: {
    id,
    idBase,
    instance
  },
  setAttributes,
  clientId,
  isSelected,
  isWide = false
}) {
  const [hasPreview, setHasPreview] = (0,external_wp_element_namespaceObject.useState)(null);
  const widgetTypeId = id !== null && id !== void 0 ? id : idBase;
  const {
    record: widgetType,
    hasResolved: hasResolvedWidgetType
  } = (0,external_wp_coreData_namespaceObject.useEntityRecord)('root', 'widgetType', widgetTypeId);
  const isNavigationMode = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).isNavigationMode(), []);
  const setInstance = (0,external_wp_element_namespaceObject.useCallback)(nextInstance => {
    setAttributes({
      instance: nextInstance
    });
  }, []);
  if (!widgetType && hasResolvedWidgetType) {
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, {
      icon: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockIcon, {
        icon: library_brush
      }),
      label: (0,external_wp_i18n_namespaceObject.__)('Legacy Widget'),
      children: (0,external_wp_i18n_namespaceObject.__)('Widget is missing.')
    });
  }
  if (!hasResolvedWidgetType) {
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, {
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {})
    });
  }
  const mode = idBase && (isNavigationMode || !isSelected) ? 'preview' : 'edit';
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [idBase === 'text' && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockControls, {
      group: "other",
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ConvertToBlocksButton, {
        clientId: clientId,
        rawInstance: instance.raw
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InspectorControls, {
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(InspectorCard, {
        name: widgetType.name,
        description: widgetType.description
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Form, {
      title: widgetType.name,
      isVisible: mode === 'edit',
      id: id,
      idBase: idBase,
      instance: instance,
      isWide: isWide,
      onChangeInstance: setInstance,
      onChangeHasPreview: setHasPreview
    }), idBase && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
      children: [hasPreview === null && mode === 'preview' && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, {
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {})
      }), hasPreview === true && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Preview, {
        idBase: idBase,
        instance: instance,
        isVisible: mode === 'preview'
      }), hasPreview === false && mode === 'preview' && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(NoPreview, {
        name: widgetType.name
      })]
    })]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/transforms.js
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
  transform: ({
    content
  }) => ({
    content
  })
}, {
  block: 'core/archives',
  widget: 'archives',
  transform: ({
    count,
    dropdown
  }) => {
    return {
      displayAsDropdown: !!dropdown,
      showPostCounts: !!count
    };
  }
}, {
  block: 'core/latest-posts',
  widget: 'recent-posts',
  transform: ({
    show_date: displayPostDate,
    number
  }) => {
    return {
      displayPostDate: !!displayPostDate,
      postsToShow: number
    };
  }
}, {
  block: 'core/latest-comments',
  widget: 'recent-comments',
  transform: ({
    number
  }) => {
    return {
      commentsToShow: number
    };
  }
}, {
  block: 'core/tag-cloud',
  widget: 'tag_cloud',
  transform: ({
    taxonomy,
    count
  }) => {
    return {
      showTagCounts: !!count,
      taxonomy
    };
  }
}, {
  block: 'core/categories',
  widget: 'categories',
  transform: ({
    count,
    dropdown,
    hierarchical
  }) => {
    return {
      displayAsDropdown: !!dropdown,
      showPostCounts: !!count,
      showHierarchy: !!hierarchical
    };
  }
}, {
  block: 'core/audio',
  widget: 'media_audio',
  transform: ({
    url,
    preload,
    loop,
    attachment_id: id
  }) => {
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
  transform: ({
    url,
    preload,
    loop,
    attachment_id: id
  }) => {
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
  transform: ({
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
  }) => {
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
  transform: ({
    ids,
    link_type: linkTo,
    size,
    number
  }) => {
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
  transform: ({
    url,
    show_author: displayAuthor,
    show_date: displayDate,
    show_summary: displayExcerpt,
    items
  }) => {
    return {
      feedURL: url,
      displayAuthor: !!displayAuthor,
      displayDate: !!displayDate,
      displayExcerpt: !!displayExcerpt,
      itemsToShow: items
    };
  }
}].map(({
  block,
  widget,
  transform
}) => {
  return {
    type: 'block',
    blocks: [block],
    isMatch: ({
      idBase,
      instance
    }) => {
      return idBase === widget && !!instance?.raw;
    },
    transform: ({
      instance
    }) => {
      const transformedBlock = (0,external_wp_blocks_namespaceObject.createBlock)(block, transform ? transform(instance.raw) : undefined);
      if (!instance.raw?.title) {
        return transformedBlock;
      }
      return [(0,external_wp_blocks_namespaceObject.createBlock)('core/heading', {
        content: instance.raw.title
      }), transformedBlock];
    }
  };
});
const transforms = {
  to: legacyWidgetTransforms
};
/* harmony default export */ const legacy_widget_transforms = (transforms);

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */
const metadata = {
  $schema: "https://schemas.wp.org/trunk/block.json",
  apiVersion: 3,
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
} = metadata;

const settings = {
  icon: library_widget,
  edit: Edit,
  transforms: legacy_widget_transforms
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/group.js
/**
 * WordPress dependencies
 */


const group = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M18 4h-7c-1.1 0-2 .9-2 2v3H6c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2v-3h3c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4.5 14c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5v-7c0-.3.2-.5.5-.5h3V13c0 1.1.9 2 2 2h2.5v3zm0-4.5H11c-.3 0-.5-.2-.5-.5v-2.5H13c.3 0 .5.2.5.5v2.5zm5-.5c0 .3-.2.5-.5.5h-3V11c0-1.1-.9-2-2-2h-2.5V6c0-.3.2-.5.5-.5h7c.3 0 .5.2.5.5v7z"
  })
});
/* harmony default export */ const library_group = (group);

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/edit.js
/**
 * WordPress dependencies
 */








function edit_Edit(props) {
  const {
    clientId
  } = props;
  const {
    innerBlocks
  } = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).getBlock(clientId), [clientId]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
    ...(0,external_wp_blockEditor_namespaceObject.useBlockProps)({
      className: 'widget'
    }),
    children: innerBlocks.length === 0 ? /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(PlaceholderContent, {
      ...props
    }) : /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(PreviewContent, {
      ...props
    })
  });
}
function PlaceholderContent({
  clientId
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, {
      className: "wp-block-widget-group__placeholder",
      icon: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockIcon, {
        icon: library_group
      }),
      label: (0,external_wp_i18n_namespaceObject.__)('Widget Group'),
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.ButtonBlockAppender, {
        rootClientId: clientId
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InnerBlocks, {
      renderAppender: false
    })]
  });
}
function PreviewContent({
  attributes,
  setAttributes
}) {
  var _attributes$title;
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.RichText, {
      tagName: "h2",
      identifier: "title",
      className: "widget-title",
      allowedFormats: [],
      placeholder: (0,external_wp_i18n_namespaceObject.__)('Title'),
      value: (_attributes$title = attributes.title) !== null && _attributes$title !== void 0 ? _attributes$title : '',
      onChange: title => setAttributes({
        title
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InnerBlocks, {})]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/save.js
/**
 * WordPress dependencies
 */




function save({
  attributes
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.RichText.Content, {
      tagName: "h2",
      className: "widget-title",
      value: attributes.title
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "wp-widget-group__inner-blocks",
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InnerBlocks.Content, {})
    })]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/deprecated.js
/**
 * WordPress dependencies
 */




const v1 = {
  attributes: {
    title: {
      type: 'string'
    }
  },
  supports: {
    html: false,
    inserter: true,
    customClassName: true,
    reusable: false
  },
  save({
    attributes
  }) {
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.RichText.Content, {
        tagName: "h2",
        className: "widget-title",
        value: attributes.title
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InnerBlocks.Content, {})]
    });
  }
};
/* harmony default export */ const deprecated = ([v1]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/index.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */
const widget_group_metadata = {
  $schema: "https://schemas.wp.org/trunk/block.json",
  apiVersion: 3,
  name: "core/widget-group",
  title: "Widget Group",
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
  title: (0,external_wp_i18n_namespaceObject.__)('Widget Group'),
  description: (0,external_wp_i18n_namespaceObject.__)('Create a classic widget layout with a title that’s styled by your theme for your widget areas.'),
  icon: library_group,
  __experimentalLabel: ({
    name: label
  }) => label,
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
          return (0,external_wp_blocks_namespaceObject.createBlock)(block.name, block.attributes, block.innerBlocks);
        })];

        // If the first block is a heading then assume this is intended
        // to be the Widget's "title".
        const firstHeadingBlock = innerBlocks[0].name === 'core/heading' ? innerBlocks[0] : null;

        // Remove the first heading block as we're copying
        // it's content into the Widget Group's title attribute.
        innerBlocks = innerBlocks.filter(block => block !== firstHeadingBlock);
        return (0,external_wp_blocks_namespaceObject.createBlock)('core/widget-group', {
          ...(firstHeadingBlock && {
            title: firstHeadingBlock.attributes.content
          })
        }, innerBlocks);
      }
    }]
  },
  deprecated: deprecated
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/move-to.js
/**
 * WordPress dependencies
 */


const moveTo = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M19.75 9c0-1.257-.565-2.197-1.39-2.858-.797-.64-1.827-1.017-2.815-1.247-1.802-.42-3.703-.403-4.383-.396L11 4.5V6l.177-.001c.696-.006 2.416-.02 4.028.356.887.207 1.67.518 2.216.957.52.416.829.945.829 1.688 0 .592-.167.966-.407 1.23-.255.281-.656.508-1.236.674-1.19.34-2.82.346-4.607.346h-.077c-1.692 0-3.527 0-4.942.404-.732.209-1.424.545-1.935 1.108-.526.579-.796 1.33-.796 2.238 0 1.257.565 2.197 1.39 2.858.797.64 1.827 1.017 2.815 1.247 1.802.42 3.703.403 4.383.396L13 19.5h.714V22L18 18.5 13.714 15v3H13l-.177.001c-.696.006-2.416.02-4.028-.356-.887-.207-1.67-.518-2.216-.957-.52-.416-.829-.945-.829-1.688 0-.592.167-.966.407-1.23.255-.281.656-.508 1.237-.674 1.189-.34 2.819-.346 4.606-.346h.077c1.692 0 3.527 0 4.941-.404.732-.209 1.425-.545 1.936-1.108.526-.579.796-1.33.796-2.238z"
  })
});
/* harmony default export */ const move_to = (moveTo);

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/components/move-to-widget-area/index.js
/**
 * WordPress dependencies
 */




function MoveToWidgetArea({
  currentWidgetAreaId,
  widgetAreas,
  onSelect
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarGroup, {
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarItem, {
      children: toggleProps => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.DropdownMenu, {
        icon: move_to,
        label: (0,external_wp_i18n_namespaceObject.__)('Move to widget area'),
        toggleProps: toggleProps,
        children: ({
          onClose
        }) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuGroup, {
          label: (0,external_wp_i18n_namespaceObject.__)('Move to'),
          children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItemsChoice, {
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
          })
        })
      })
    })
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/components/index.js


;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/utils.js
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
  return {
    ...block,
    attributes: {
      ...(block.attributes || {}),
      __internalWidgetId: widgetId
    }
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/register-legacy-widget-variations.js
/**
 * WordPress dependencies
 */



function registerLegacyWidgetVariations(settings) {
  const unsubscribe = (0,external_wp_data_namespaceObject.subscribe)(() => {
    var _settings$widgetTypes;
    const hiddenIds = (_settings$widgetTypes = settings?.widgetTypesToHideFromLegacyWidgetBlock) !== null && _settings$widgetTypes !== void 0 ? _settings$widgetTypes : [];
    const widgetTypes = (0,external_wp_data_namespaceObject.select)(external_wp_coreData_namespaceObject.store).getWidgetTypes({
      per_page: -1
    })?.filter(widgetType => !hiddenIds.includes(widgetType.id));
    if (widgetTypes) {
      unsubscribe();
      (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).addBlockVariations('core/legacy-widget', widgetTypes.map(widgetType => ({
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/widgets/build-module/index.js
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
 * @param {Object} supports Block support settings.
 * @see https://developer.wordpress.org/block-editor/how-to-guides/widgets/legacy-widget-block/
 */
function registerLegacyWidgetBlock(supports = {}) {
  const {
    metadata,
    settings,
    name
  } = legacy_widget_namespaceObject;
  (0,external_wp_blocks_namespaceObject.registerBlockType)({
    name,
    ...metadata
  }, {
    ...settings,
    supports: {
      ...settings.supports,
      ...supports
    }
  });
}

/**
 * Registers the Widget Group block.
 *
 * @param {Object} supports Block support settings.
 */
function registerWidgetGroupBlock(supports = {}) {
  const {
    metadata,
    settings,
    name
  } = widget_group_namespaceObject;
  (0,external_wp_blocks_namespaceObject.registerBlockType)({
    name,
    ...metadata
  }, {
    ...settings,
    supports: {
      ...settings.supports,
      ...supports
    }
  });
}


(window.wp = window.wp || {}).widgets = __webpack_exports__;
/******/ })()
;