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
  yu: () => (block_namespaceObject),
  UU: () => (legacy_widget_name),
  W0: () => (settings)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/index.js
var widget_group_namespaceObject = {};
__webpack_require__.r(widget_group_namespaceObject);
__webpack_require__.d(widget_group_namespaceObject, {
  yu: () => (widget_group_block_namespaceObject),
  UU: () => (widget_group_name),
  W0: () => (widget_group_settings)
});

;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/@wordpress/icons/build-module/library/widget.js


var widget_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M6 3H8V5H16V3H18V5C19.1046 5 20 5.89543 20 7V19C20 20.1046 19.1046 21 18 21H6C4.89543 21 4 20.1046 4 19V7C4 5.89543 4.89543 5 6 5V3ZM18 6.5H6C5.72386 6.5 5.5 6.72386 5.5 7V8H18.5V7C18.5 6.72386 18.2761 6.5 18 6.5ZM18.5 9.5H5.5V19C5.5 19.2761 5.72386 19.5 6 19.5H18C18.2761 19.5 18.5 19.2761 18.5 19V9.5ZM11 11H13V13H11V11ZM7 11V13H9V11H7ZM15 13V11H17V13H15Z" }) });


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/block.json
const block_namespaceObject = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"core/legacy-widget","title":"Legacy Widget","category":"widgets","description":"Display a legacy widget.","textdomain":"default","attributes":{"id":{"type":"string","default":null},"idBase":{"type":"string","default":null},"instance":{"type":"object","default":null}},"supports":{"html":false,"customClassName":false,"reusable":false},"editorStyle":"wp-block-legacy-widget-editor"}');
;// ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// ./node_modules/@wordpress/icons/build-module/library/brush.js


var brush_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M4 20h8v-1.5H4V20zM18.9 3.5c-.6-.6-1.5-.6-2.1 0l-7.2 7.2c-.4-.1-.7 0-1.1.1-.5.2-1.5.7-1.9 2.2-.4 1.7-.8 2.2-1.1 2.7-.1.1-.2.3-.3.4l-.6 1.1H6c2 0 3.4-.4 4.7-1.4.8-.6 1.2-1.4 1.3-2.3 0-.3 0-.5-.1-.7L19 5.7c.5-.6.5-1.6-.1-2.2zM9.7 14.7c-.7.5-1.5.8-2.4 1 .2-.5.5-1.2.8-2.3.2-.6.4-1 .8-1.1.5-.1 1 .1 1.3.3.2.2.3.5.2.8 0 .3-.1.9-.7 1.3z" }) });


;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/widget-type-selector.js






function WidgetTypeSelector({ selectedId, onSelect }) {
  const widgetTypes = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const hiddenIds = select(external_wp_blockEditor_namespaceObject.store).getSettings()?.widgetTypesToHideFromLegacyWidgetBlock ?? [];
    return select(external_wp_coreData_namespaceObject.store).getWidgetTypes({ per_page: -1 })?.filter((widgetType) => !hiddenIds.includes(widgetType.id));
  }, []);
  if (!widgetTypes) {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {});
  }
  if (widgetTypes.length === 0) {
    return (0,external_wp_i18n_namespaceObject.__)("There are no widgets available.");
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.SelectControl,
    {
      __next40pxDefaultSize: true,
      __nextHasNoMarginBottom: true,
      label: (0,external_wp_i18n_namespaceObject.__)("Legacy widget"),
      value: selectedId ?? "",
      options: [
        { value: "", label: (0,external_wp_i18n_namespaceObject.__)("Select widget") },
        ...widgetTypes.map((widgetType) => ({
          value: widgetType.id,
          label: widgetType.name
        }))
      ],
      onChange: (value) => {
        if (value) {
          const selected = widgetTypes.find(
            (widgetType) => widgetType.id === value
          );
          onSelect({
            selectedId: selected.id,
            isMulti: selected.is_multi
          });
        } else {
          onSelect({ selectedId: null });
        }
      }
    }
  );
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/inspector-card.js

function InspectorCard({ name, description }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "wp-block-legacy-widget-inspector-card", children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h3", { className: "wp-block-legacy-widget-inspector-card__name", children: name }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("span", { children: description })
  ] });
}


;// external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/control.js



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
    this.number = ++lastNumber;
    this.handleFormChange = (0,external_wp_compose_namespaceObject.debounce)(
      this.handleFormChange.bind(this),
      200
    );
    this.handleFormSubmit = this.handleFormSubmit.bind(this);
    this.initDOM();
    this.bindEvents();
    this.loadContent();
  }
  /**
   * Clean up the control so that it can be garbage collected.
   *
   * @access public
   */
  destroy() {
    this.unbindEvents();
    this.element.remove();
  }
  /**
   * Creates the control's DOM structure.
   *
   * @access private
   */
  initDOM() {
    this.element = el("div", { class: "widget open" }, [
      el("div", { class: "widget-inside" }, [
        this.form = el("form", { class: "form", method: "post" }, [
          // These hidden form inputs are what most widgets' scripts
          // use to access data about the widget.
          el("input", {
            class: "widget-id",
            type: "hidden",
            name: "widget-id",
            value: this.id ?? `${this.idBase}-${this.number}`
          }),
          el("input", {
            class: "id_base",
            type: "hidden",
            name: "id_base",
            value: this.idBase ?? this.id
          }),
          el("input", {
            class: "widget-width",
            type: "hidden",
            name: "widget-width",
            value: "250"
          }),
          el("input", {
            class: "widget-height",
            type: "hidden",
            name: "widget-height",
            value: "200"
          }),
          el("input", {
            class: "widget_number",
            type: "hidden",
            name: "widget_number",
            value: this.idBase ? this.number.toString() : ""
          }),
          this.content = el("div", { class: "widget-content" }),
          // Non-multi widgets can be saved via a Save button.
          this.id && el(
            "button",
            {
              class: "button is-primary",
              type: "submit"
            },
            (0,external_wp_i18n_namespaceObject.__)("Save")
          )
        ])
      ])
    ]);
  }
  /**
   * Adds the control's event listeners.
   *
   * @access private
   */
  bindEvents() {
    if (window.jQuery) {
      const { jQuery: $ } = window;
      $(this.form).on("change", null, this.handleFormChange);
      $(this.form).on("input", null, this.handleFormChange);
      $(this.form).on("submit", this.handleFormSubmit);
    } else {
      this.form.addEventListener("change", this.handleFormChange);
      this.form.addEventListener("input", this.handleFormChange);
      this.form.addEventListener("submit", this.handleFormSubmit);
    }
  }
  /**
   * Removes the control's event listeners.
   *
   * @access private
   */
  unbindEvents() {
    if (window.jQuery) {
      const { jQuery: $ } = window;
      $(this.form).off("change", null, this.handleFormChange);
      $(this.form).off("input", null, this.handleFormChange);
      $(this.form).off("submit", this.handleFormSubmit);
    } else {
      this.form.removeEventListener("change", this.handleFormChange);
      this.form.removeEventListener("input", this.handleFormChange);
      this.form.removeEventListener("submit", this.handleFormSubmit);
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
        const { form } = await saveWidget(this.id);
        this.content.innerHTML = form;
      } else if (this.idBase) {
        const { form, preview } = await encodeWidget({
          idBase: this.idBase,
          instance: this.instance,
          number: this.number
        });
        this.content.innerHTML = form;
        this.hasPreview = !isEmptyHTML(preview);
        if (!this.instance.hash) {
          const { instance } = await encodeWidget({
            idBase: this.idBase,
            instance: this.instance,
            number: this.number,
            formData: serializeForm(this.form)
          });
          this.instance = instance;
        }
      }
      if (window.jQuery) {
        const { jQuery: $ } = window;
        $(document).trigger("widget-added", [$(this.element)]);
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
        const { form } = await saveWidget(this.id, formData);
        this.content.innerHTML = form;
        if (window.jQuery) {
          const { jQuery: $ } = window;
          $(document).trigger("widget-updated", [
            $(this.element)
          ]);
        }
      } else if (this.idBase) {
        const { instance, preview } = await encodeWidget({
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
  } else if (typeof content === "string") {
    element.innerText = content;
  }
  return element;
}
async function saveWidget(id, formData = null) {
  let widget;
  if (formData) {
    widget = await external_wp_apiFetch_default()({
      path: `/wp/v2/widgets/${id}?context=edit`,
      method: "PUT",
      data: {
        form_data: formData
      }
    });
  } else {
    widget = await external_wp_apiFetch_default()({
      path: `/wp/v2/widgets/${id}?context=edit`,
      method: "GET"
    });
  }
  return { form: widget.rendered_form };
}
async function encodeWidget({ idBase, instance, number, formData = null }) {
  const response = await external_wp_apiFetch_default()({
    path: `/wp/v2/widget-types/${idBase}/encode`,
    method: "POST",
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
  const element = document.createElement("div");
  element.innerHTML = html;
  return isEmptyNode(element);
}
function isEmptyNode(node) {
  switch (node.nodeType) {
    case node.TEXT_NODE:
      return node.nodeValue.trim() === "";
    case node.ELEMENT_NODE:
      if ([
        "AUDIO",
        "CANVAS",
        "EMBED",
        "IFRAME",
        "IMG",
        "MATH",
        "OBJECT",
        "SVG",
        "VIDEO"
      ].includes(node.tagName)) {
        return false;
      }
      if (!node.hasChildNodes()) {
        return true;
      }
      return Array.from(node.childNodes).every(isEmptyNode);
    default:
      return true;
  }
}
function serializeForm(form) {
  return new window.URLSearchParams(
    Array.from(new window.FormData(form))
  ).toString();
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/form.js









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
  const isMediumLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)("small");
  const outgoingInstances = (0,external_wp_element_namespaceObject.useRef)(/* @__PURE__ */ new Set());
  const incomingInstances = (0,external_wp_element_namespaceObject.useRef)(/* @__PURE__ */ new Set());
  const { createNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
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
        createNotice(
          "error",
          (0,external_wp_i18n_namespaceObject.sprintf)(
            /* translators: %s: the name of the affected block. */
            (0,external_wp_i18n_namespaceObject.__)(
              'The "%s" block was affected by errors and may not function properly. Check the developer tools for more details.'
            ),
            idBase || id
          )
        );
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
  }, [
    id,
    idBase,
    instance,
    onChangeInstance,
    onChangeHasPreview,
    isMediumLargeViewport
  ]);
  if (isWide && isMediumLargeViewport) {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(
      "div",
      {
        className: dist_clsx({
          "wp-block-legacy-widget__container": isVisible
        }),
        children: [
          isVisible && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h3", { className: "wp-block-legacy-widget__edit-form-title", children: title }),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.Popover,
            {
              focusOnMount: false,
              placement: "right",
              offset: 32,
              resize: false,
              flip: false,
              shift: true,
              children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                "div",
                {
                  ref,
                  className: "wp-block-legacy-widget__edit-form",
                  hidden: !isVisible
                }
              )
            }
          )
        ]
      }
    );
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    "div",
    {
      ref,
      className: "wp-block-legacy-widget__edit-form",
      hidden: !isVisible,
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h3", { className: "wp-block-legacy-widget__edit-form-title", children: title })
    }
  );
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/preview.js







function Preview({ idBase, instance, isVisible }) {
  const [isLoaded, setIsLoaded] = (0,external_wp_element_namespaceObject.useState)(false);
  const [srcDoc, setSrcDoc] = (0,external_wp_element_namespaceObject.useState)("");
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const abortController = typeof window.AbortController === "undefined" ? void 0 : new window.AbortController();
    async function fetchPreviewHTML() {
      const restRoute = `/wp/v2/widget-types/${idBase}/render`;
      return await external_wp_apiFetch_default()({
        path: restRoute,
        method: "POST",
        signal: abortController?.signal,
        data: instance ? { instance } : {}
      });
    }
    fetchPreviewHTML().then((response) => {
      setSrcDoc(response.preview);
    }).catch((error) => {
      if ("AbortError" === error.name) {
        return;
      }
      throw error;
    });
    return () => abortController?.abort();
  }, [idBase, instance]);
  const ref = (0,external_wp_compose_namespaceObject.useRefEffect)(
    (iframe) => {
      if (!isLoaded) {
        return;
      }
      function setHeight() {
        const height = Math.max(
          iframe.contentDocument.documentElement?.offsetHeight ?? 0,
          iframe.contentDocument.body?.offsetHeight ?? 0
        );
        iframe.style.height = `${height !== 0 ? height : 100}px`;
      }
      const { IntersectionObserver } = iframe.ownerDocument.defaultView;
      const intersectionObserver = new IntersectionObserver(
        ([entry]) => {
          if (entry.isIntersecting) {
            setHeight();
          }
        },
        {
          threshold: 1
        }
      );
      intersectionObserver.observe(iframe);
      iframe.addEventListener("load", setHeight);
      return () => {
        intersectionObserver.disconnect();
        iframe.removeEventListener("load", setHeight);
      };
    },
    [isLoaded]
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    isVisible && !isLoaded && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {}) }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      "div",
      {
        className: dist_clsx("wp-block-legacy-widget__edit-preview", {
          "is-offscreen": !isVisible || !isLoaded
        }),
        children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Disabled, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          "iframe",
          {
            ref,
            className: "wp-block-legacy-widget__edit-preview-iframe",
            tabIndex: "-1",
            title: (0,external_wp_i18n_namespaceObject.__)("Legacy Widget Preview"),
            srcDoc,
            onLoad: (event) => {
              event.target.contentDocument.body.style.overflow = "hidden";
              setIsLoaded(true);
            },
            height: 100
          }
        ) })
      }
    )
  ] });
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/no-preview.js


function NoPreview({ name }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { className: "wp-block-legacy-widget__edit-no-preview", children: [
    name && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("h3", { children: name }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("p", { children: (0,external_wp_i18n_namespaceObject.__)("No preview available.") })
  ] });
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/convert-to-blocks-button.js






function ConvertToBlocksButton({ clientId, rawInstance }) {
  const { replaceBlocks } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.ToolbarButton,
    {
      onClick: () => {
        if (rawInstance.title) {
          replaceBlocks(clientId, [
            (0,external_wp_blocks_namespaceObject.createBlock)("core/heading", {
              content: rawInstance.title
            }),
            ...(0,external_wp_blocks_namespaceObject.rawHandler)({ HTML: rawInstance.text })
          ]);
        } else {
          replaceBlocks(
            clientId,
            (0,external_wp_blocks_namespaceObject.rawHandler)({ HTML: rawInstance.text })
          );
        }
      },
      children: (0,external_wp_i18n_namespaceObject.__)("Convert to blocks")
    }
  );
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/edit/index.js














function Edit(props) {
  const { id, idBase } = props.attributes;
  const { isWide = false } = props;
  const blockProps = (0,external_wp_blockEditor_namespaceObject.useBlockProps)({
    className: dist_clsx({
      "is-wide-widget": isWide
    })
  });
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { ...blockProps, children: !id && !idBase ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Empty, { ...props }) : /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(NotEmpty, { ...props }) });
}
function Empty({ attributes: { id, idBase }, setAttributes }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Placeholder,
    {
      icon: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockIcon, { icon: brush_default }),
      label: (0,external_wp_i18n_namespaceObject.__)("Legacy Widget"),
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Flex, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.FlexBlock, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        WidgetTypeSelector,
        {
          selectedId: id ?? idBase,
          onSelect: ({ selectedId, isMulti }) => {
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
        }
      ) }) })
    }
  );
}
function NotEmpty({
  attributes: { id, idBase, instance },
  setAttributes,
  clientId,
  isSelected,
  isWide = false
}) {
  const [hasPreview, setHasPreview] = (0,external_wp_element_namespaceObject.useState)(null);
  const widgetTypeId = id ?? idBase;
  const { record: widgetType, hasResolved: hasResolvedWidgetType } = (0,external_wp_coreData_namespaceObject.useEntityRecord)("root", "widgetType", widgetTypeId);
  const setInstance = (0,external_wp_element_namespaceObject.useCallback)((nextInstance) => {
    setAttributes({ instance: nextInstance });
  }, []);
  if (!widgetType && hasResolvedWidgetType) {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.Placeholder,
      {
        icon: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockIcon, { icon: brush_default }),
        label: (0,external_wp_i18n_namespaceObject.__)("Legacy Widget"),
        children: (0,external_wp_i18n_namespaceObject.__)("Widget is missing.")
      }
    );
  }
  if (!hasResolvedWidgetType) {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {}) });
  }
  const mode = idBase && !isSelected ? "preview" : "edit";
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    idBase === "text" && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockControls, { group: "other", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      ConvertToBlocksButton,
      {
        clientId,
        rawInstance: instance.raw
      }
    ) }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InspectorControls, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      InspectorCard,
      {
        name: widgetType.name,
        description: widgetType.description
      }
    ) }),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      Form,
      {
        title: widgetType.name,
        isVisible: mode === "edit",
        id,
        idBase,
        instance,
        isWide,
        onChangeInstance: setInstance,
        onChangeHasPreview: setHasPreview
      }
    ),
    idBase && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
      hasPreview === null && mode === "preview" && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {}) }),
      hasPreview === true && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        Preview,
        {
          idBase,
          instance,
          isVisible: mode === "preview"
        }
      ),
      hasPreview === false && mode === "preview" && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(NoPreview, { name: widgetType.name })
    ] })
  ] });
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/transforms.js

const legacyWidgetTransforms = [
  {
    block: "core/calendar",
    widget: "calendar"
  },
  {
    block: "core/search",
    widget: "search"
  },
  {
    block: "core/html",
    widget: "custom_html",
    transform: ({ content }) => ({
      content
    })
  },
  {
    block: "core/archives",
    widget: "archives",
    transform: ({ count, dropdown }) => {
      return {
        displayAsDropdown: !!dropdown,
        showPostCounts: !!count
      };
    }
  },
  {
    block: "core/latest-posts",
    widget: "recent-posts",
    transform: ({ show_date: displayPostDate, number }) => {
      return {
        displayPostDate: !!displayPostDate,
        postsToShow: number
      };
    }
  },
  {
    block: "core/latest-comments",
    widget: "recent-comments",
    transform: ({ number }) => {
      return {
        commentsToShow: number
      };
    }
  },
  {
    block: "core/tag-cloud",
    widget: "tag_cloud",
    transform: ({ taxonomy, count }) => {
      return {
        showTagCounts: !!count,
        taxonomy
      };
    }
  },
  {
    block: "core/categories",
    widget: "categories",
    transform: ({ count, dropdown, hierarchical }) => {
      return {
        displayAsDropdown: !!dropdown,
        showPostCounts: !!count,
        showHierarchy: !!hierarchical
      };
    }
  },
  {
    block: "core/audio",
    widget: "media_audio",
    transform: ({ url, preload, loop, attachment_id: id }) => {
      return {
        src: url,
        id,
        preload,
        loop
      };
    }
  },
  {
    block: "core/video",
    widget: "media_video",
    transform: ({ url, preload, loop, attachment_id: id }) => {
      return {
        src: url,
        id,
        preload,
        loop
      };
    }
  },
  {
    block: "core/image",
    widget: "media_image",
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
        linkTarget: targetBlack ? "_blank" : void 0,
        rel,
        sizeSlug,
        url,
        width
      };
    }
  },
  {
    block: "core/gallery",
    widget: "media_gallery",
    transform: ({ ids, link_type: linkTo, size, number }) => {
      return {
        ids,
        columns: number,
        linkTo,
        sizeSlug: size,
        images: ids.map((id) => ({
          id
        }))
      };
    }
  },
  {
    block: "core/rss",
    widget: "rss",
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
  }
].map(({ block, widget, transform }) => {
  return {
    type: "block",
    blocks: [block],
    isMatch: ({ idBase, instance }) => {
      return idBase === widget && !!instance?.raw;
    },
    transform: ({ instance }) => {
      const transformedBlock = (0,external_wp_blocks_namespaceObject.createBlock)(
        block,
        transform ? transform(instance.raw) : void 0
      );
      if (!instance.raw?.title) {
        return transformedBlock;
      }
      return [
        (0,external_wp_blocks_namespaceObject.createBlock)("core/heading", {
          content: instance.raw.title
        }),
        transformedBlock
      ];
    }
  };
});
const transforms = {
  to: legacyWidgetTransforms
};
var transforms_default = transforms;


;// ./node_modules/@wordpress/widgets/build-module/blocks/legacy-widget/index.js




const { name: legacy_widget_name } = block_namespaceObject;
const settings = {
  icon: widget_default,
  edit: Edit,
  transforms: transforms_default
};


;// ./node_modules/@wordpress/icons/build-module/library/group.js


var group_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M18 4h-7c-1.1 0-2 .9-2 2v3H6c-1.1 0-2 .9-2 2v7c0 1.1.9 2 2 2h7c1.1 0 2-.9 2-2v-3h3c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4.5 14c0 .3-.2.5-.5.5H6c-.3 0-.5-.2-.5-.5v-7c0-.3.2-.5.5-.5h3V13c0 1.1.9 2 2 2h2.5v3zm0-4.5H11c-.3 0-.5-.2-.5-.5v-2.5H13c.3 0 .5.2.5.5v2.5zm5-.5c0 .3-.2.5-.5.5h-3V11c0-1.1-.9-2-2-2h-2.5V6c0-.3.2-.5.5-.5h7c.3 0 .5.2.5.5v7z" }) });


;// ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/block.json
const widget_group_block_namespaceObject = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"core/widget-group","title":"Widget Group","category":"widgets","attributes":{"title":{"type":"string"}},"supports":{"html":false,"inserter":true,"customClassName":true,"reusable":false},"editorStyle":"wp-block-widget-group-editor","style":"wp-block-widget-group"}');
;// ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/edit.js






function edit_Edit(props) {
  const { clientId } = props;
  const { innerBlocks } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(external_wp_blockEditor_namespaceObject.store).getBlock(clientId),
    [clientId]
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { ...(0,external_wp_blockEditor_namespaceObject.useBlockProps)({ className: "widget" }), children: innerBlocks.length === 0 ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(PlaceholderContent, { ...props }) : /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(PreviewContent, { ...props }) });
}
function PlaceholderContent({ clientId }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.Placeholder,
      {
        className: "wp-block-widget-group__placeholder",
        icon: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockIcon, { icon: group_default }),
        label: (0,external_wp_i18n_namespaceObject.__)("Widget Group"),
        children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.ButtonBlockAppender, { rootClientId: clientId })
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InnerBlocks, { renderAppender: false })
  ] });
}
function PreviewContent({ attributes, setAttributes }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_blockEditor_namespaceObject.RichText,
      {
        tagName: "h2",
        identifier: "title",
        className: "widget-title",
        allowedFormats: [],
        placeholder: (0,external_wp_i18n_namespaceObject.__)("Title"),
        value: attributes.title ?? "",
        onChange: (title) => setAttributes({ title })
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InnerBlocks, {})
  ] });
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/save.js


function save({ attributes }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_blockEditor_namespaceObject.RichText.Content,
      {
        tagName: "h2",
        className: "widget-title",
        value: attributes.title
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { className: "wp-widget-group__inner-blocks", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InnerBlocks.Content, {}) })
  ] });
}


;// ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/deprecated.js


const v1 = {
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
  save({ attributes }) {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        external_wp_blockEditor_namespaceObject.RichText.Content,
        {
          tagName: "h2",
          className: "widget-title",
          value: attributes.title
        }
      ),
      /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InnerBlocks.Content, {})
    ] });
  }
};
var deprecated_default = [v1];


;// ./node_modules/@wordpress/widgets/build-module/blocks/widget-group/index.js







const { name: widget_group_name } = widget_group_block_namespaceObject;
const widget_group_settings = {
  title: (0,external_wp_i18n_namespaceObject.__)("Widget Group"),
  description: (0,external_wp_i18n_namespaceObject.__)(
    "Create a classic widget layout with a title that\u2019s styled by your theme for your widget areas."
  ),
  icon: group_default,
  __experimentalLabel: ({ name: label }) => label,
  edit: edit_Edit,
  save: save,
  transforms: {
    from: [
      {
        type: "block",
        isMultiBlock: true,
        blocks: ["*"],
        isMatch(attributes, blocks) {
          return !blocks.some(
            (block) => block.name === "core/widget-group"
          );
        },
        __experimentalConvert(blocks) {
          let innerBlocks = [
            ...blocks.map((block) => {
              return (0,external_wp_blocks_namespaceObject.createBlock)(
                block.name,
                block.attributes,
                block.innerBlocks
              );
            })
          ];
          const firstHeadingBlock = innerBlocks[0].name === "core/heading" ? innerBlocks[0] : null;
          innerBlocks = innerBlocks.filter(
            (block) => block !== firstHeadingBlock
          );
          return (0,external_wp_blocks_namespaceObject.createBlock)(
            "core/widget-group",
            {
              ...firstHeadingBlock && {
                title: firstHeadingBlock.attributes.content
              }
            },
            innerBlocks
          );
        }
      }
    ]
  },
  deprecated: deprecated_default
};


;// ./node_modules/@wordpress/icons/build-module/library/move-to.js


var move_to_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M19.75 9c0-1.257-.565-2.197-1.39-2.858-.797-.64-1.827-1.017-2.815-1.247-1.802-.42-3.703-.403-4.383-.396L11 4.5V6l.177-.001c.696-.006 2.416-.02 4.028.356.887.207 1.67.518 2.216.957.52.416.829.945.829 1.688 0 .592-.167.966-.407 1.23-.255.281-.656.508-1.236.674-1.19.34-2.82.346-4.607.346h-.077c-1.692 0-3.527 0-4.942.404-.732.209-1.424.545-1.935 1.108-.526.579-.796 1.33-.796 2.238 0 1.257.565 2.197 1.39 2.858.797.64 1.827 1.017 2.815 1.247 1.802.42 3.703.403 4.383.396L13 19.5h.714V22L18 18.5 13.714 15v3H13l-.177.001c-.696.006-2.416.02-4.028-.356-.887-.207-1.67-.518-2.216-.957-.52-.416-.829-.945-.829-1.688 0-.592.167-.966.407-1.23.255-.281.656-.508 1.237-.674 1.189-.34 2.819-.346 4.606-.346h.077c1.692 0 3.527 0 4.941-.404.732-.209 1.425-.545 1.936-1.108.526-.579.796-1.33.796-2.238z" }) });


;// ./node_modules/@wordpress/widgets/build-module/components/move-to-widget-area/index.js




function MoveToWidgetArea({
  currentWidgetAreaId,
  widgetAreas,
  onSelect
}) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarGroup, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarItem, { children: (toggleProps) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.DropdownMenu,
    {
      icon: move_to_default,
      label: (0,external_wp_i18n_namespaceObject.__)("Move to widget area"),
      toggleProps,
      children: ({ onClose }) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuGroup, { label: (0,external_wp_i18n_namespaceObject.__)("Move to"), children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        external_wp_components_namespaceObject.MenuItemsChoice,
        {
          choices: widgetAreas.map(
            (widgetArea) => ({
              value: widgetArea.id,
              label: widgetArea.name,
              info: widgetArea.description
            })
          ),
          value: currentWidgetAreaId,
          onSelect: (value) => {
            onSelect(value);
            onClose();
          }
        }
      ) })
    }
  ) }) });
}


;// ./node_modules/@wordpress/widgets/build-module/components/index.js



;// ./node_modules/@wordpress/widgets/build-module/utils.js
function getWidgetIdFromBlock(block) {
  return block.attributes.__internalWidgetId;
}
function addWidgetIdToBlock(block, widgetId) {
  return {
    ...block,
    attributes: {
      ...block.attributes || {},
      __internalWidgetId: widgetId
    }
  };
}


;// ./node_modules/@wordpress/widgets/build-module/register-legacy-widget-variations.js



function registerLegacyWidgetVariations(settings) {
  const unsubscribe = (0,external_wp_data_namespaceObject.subscribe)(() => {
    const hiddenIds = settings?.widgetTypesToHideFromLegacyWidgetBlock ?? [];
    const widgetTypes = (0,external_wp_data_namespaceObject.select)(external_wp_coreData_namespaceObject.store).getWidgetTypes({ per_page: -1 })?.filter((widgetType) => !hiddenIds.includes(widgetType.id));
    if (widgetTypes) {
      unsubscribe();
      (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).addBlockVariations(
        "core/legacy-widget",
        widgetTypes.map((widgetType) => ({
          name: widgetType.id,
          title: widgetType.name,
          description: widgetType.description,
          attributes: widgetType.is_multi ? {
            idBase: widgetType.id,
            instance: {}
          } : {
            id: widgetType.id
          }
        }))
      );
    }
  });
}


;// ./node_modules/@wordpress/widgets/build-module/index.js





function registerLegacyWidgetBlock(supports = {}) {
  const { /* metadata */ "yu": metadata, /* settings */ "W0": settings, /* name */ "UU": name } = legacy_widget_namespaceObject;
  (0,external_wp_blocks_namespaceObject.registerBlockType)(
    { name, ...metadata },
    {
      ...settings,
      supports: {
        ...settings.supports,
        ...supports
      }
    }
  );
}
function registerWidgetGroupBlock(supports = {}) {
  const { /* metadata */ "yu": metadata, /* settings */ "W0": settings, /* name */ "UU": name } = widget_group_namespaceObject;
  (0,external_wp_blocks_namespaceObject.registerBlockType)(
    { name, ...metadata },
    {
      ...settings,
      supports: {
        ...settings.supports,
        ...supports
      }
    }
  );
}



(window.wp = window.wp || {}).widgets = __webpack_exports__;
/******/ })()
;