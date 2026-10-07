(function() {
var wp;
(wp ||= {}).widgets = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name3 in all)
      __defProp(target, name3, { get: all[name3], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/core-data
  var require_core_data = __commonJS({
    "package-external:@wordpress/core-data"(exports, module) {
      module.exports = window.wp.coreData;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/notices
  var require_notices = __commonJS({
    "package-external:@wordpress/notices"(exports, module) {
      module.exports = window.wp.notices;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // packages/widgets/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    MoveToWidgetArea: () => MoveToWidgetArea,
    addWidgetIdToBlock: () => addWidgetIdToBlock,
    getWidgetIdFromBlock: () => getWidgetIdFromBlock,
    registerLegacyWidgetBlock: () => registerLegacyWidgetBlock,
    registerLegacyWidgetVariations: () => registerLegacyWidgetVariations,
    registerWidgetGroupBlock: () => registerWidgetGroupBlock
  });
  var import_blocks5 = __toESM(require_blocks(), 1);

  // packages/widgets/build-module/blocks/legacy-widget/index.mjs
  var legacy_widget_exports = {};
  __export(legacy_widget_exports, {
    metadata: () => block_default,
    name: () => name,
    settings: () => settings
  });

  // packages/icons/build-module/library/brush.mjs
  var import_primitives = __toESM(require_primitives(), 1);
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  var brush_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M17 15.25L17.5858 14.6642C18.3668 13.8832 18.3668 12.6168 17.5858 11.8358L16.7071 10.9571C16.3166 10.5666 16.3166 9.93342 16.7071 9.54289L20 6.25C20.5523 5.69772 20.5523 4.80228 20 4.25C19.4477 3.69772 18.5523 3.69771 18 4.25L14.7071 7.54289C14.3166 7.93342 13.6834 7.93342 13.2929 7.54289L12.4142 6.66421C11.6332 5.88316 10.3668 5.88316 9.58579 6.66421L9 7.25M17 15.25L14.1213 18.1287C12.9497 19.3003 11.0503 19.3003 9.87868 18.1287L8 16.25M17 15.25L9 7.25M9 7.25L4 12.25L6 14.25M6 14.25L8 12.25M6 14.25L8 16.25M8 16.25L10 14.25", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/group.mjs
  var import_primitives2 = __toESM(require_primitives(), 1);
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var group_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M4.75 11C4.75 10.3096 5.30964 9.75 6 9.75H13C13.6904 9.75 14.25 10.3096 14.25 11V18C14.25 18.6904 13.6904 19.25 13 19.25H6C5.30964 19.25 4.75 18.6904 4.75 18V11Z", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M9.75 6C9.75 5.30964 10.3096 4.75 11 4.75H18C18.6904 4.75 19.25 5.30964 19.25 6V13C19.25 13.6904 18.6904 14.25 18 14.25H11C10.3096 14.25 9.75 13.6904 9.75 13V6Z", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/icons/build-module/library/move-to.mjs
  var import_primitives3 = __toESM(require_primitives(), 1);
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  var move_to_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M11 4H15C16.933 4 18.5 5.567 18.5 7.5C18.5 9.433 16.933 11 15 11H8.5C6.567 11 5 12.567 5 14.5C5 16.433 6.567 18 8.5 18H11H17M14 21L17 18L14 15", vectorEffect: "non-scaling-stroke" }) });

  // packages/icons/build-module/library/widget.mjs
  var import_primitives4 = __toESM(require_primitives(), 1);
  var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
  var widget_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M19.25 8.25V7C19.25 6.30964 18.6904 5.75 18 5.75H17M7 5.75H6C5.30964 5.75 4.75 6.30964 4.75 7V8.25L4.75 19C4.75 19.6904 5.30964 20.25 6 20.25H18C18.6904 20.25 19.25 19.6904 19.25 19V8.25M19.25 8.25H4.75M7.25 11.75H9.25M11 11.75H13M14.75 11.75H16.75M7 5.75V3M7 5.75H17M17 5.75V3", vectorEffect: "non-scaling-stroke" }),
    /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M18 5.75H6C5.30964 5.75 4.75 6.30964 4.75 7V8.25H19.25V7C19.25 6.30964 18.6904 5.75 18 5.75Z", fill: "currentColor", vectorEffect: "non-scaling-stroke" })
  ] });

  // packages/widgets/build-module/blocks/legacy-widget/block.json
  var block_default = {
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
        default: null
      },
      idBase: {
        type: "string",
        default: null
      },
      instance: {
        type: "object",
        default: null
      }
    },
    supports: {
      html: false,
      customClassName: false,
      reusable: false
    },
    editorStyle: "wp-block-legacy-widget-editor"
  };

  // node_modules/clsx/dist/clsx.mjs
  function r(e) {
    var t, f, n = "";
    if ("string" == typeof e || "number" == typeof e) n += e;
    else if ("object" == typeof e) if (Array.isArray(e)) {
      var o = e.length;
      for (t = 0; t < o; t++) e[t] && (f = r(e[t])) && (n && (n += " "), n += f);
    } else for (f in e) e[f] && (n && (n += " "), n += f);
    return n;
  }
  function clsx() {
    for (var e, t, f = 0, n = "", o = arguments.length; f < o; f++) (e = arguments[f]) && (t = r(e)) && (n && (n += " "), n += t);
    return n;
  }
  var clsx_default = clsx;

  // packages/widgets/build-module/blocks/legacy-widget/edit/index.mjs
  var import_block_editor3 = __toESM(require_block_editor(), 1);
  var import_components5 = __toESM(require_components(), 1);
  var import_i18n7 = __toESM(require_i18n(), 1);
  var import_element3 = __toESM(require_element(), 1);
  var import_core_data2 = __toESM(require_core_data(), 1);

  // packages/widgets/build-module/blocks/legacy-widget/edit/widget-type-selector.mjs
  var import_components = __toESM(require_components(), 1);
  var import_i18n = __toESM(require_i18n(), 1);
  var import_data = __toESM(require_data(), 1);
  var import_core_data = __toESM(require_core_data(), 1);
  var import_block_editor = __toESM(require_block_editor(), 1);
  var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
  function WidgetTypeSelector({ selectedId, onSelect }) {
    const widgetTypes = (0, import_data.useSelect)((select2) => {
      const hiddenIds = select2(import_block_editor.store).getSettings()?.widgetTypesToHideFromLegacyWidgetBlock ?? [];
      return select2(import_core_data.store).getWidgetTypes({ per_page: -1 })?.filter((widgetType) => !hiddenIds.includes(widgetType.id));
    }, []);
    if (!widgetTypes) {
      return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_components.Spinner, {});
    }
    if (widgetTypes.length === 0) {
      return (0, import_i18n.__)("There are no widgets available.");
    }
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
      import_components.SelectControl,
      {
        label: (0, import_i18n.__)("Legacy widget"),
        value: selectedId ?? "",
        options: [
          { value: "", label: (0, import_i18n.__)("Select widget") },
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

  // packages/widgets/build-module/blocks/legacy-widget/edit/inspector-card.mjs
  var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
  function InspectorCard({ name: name3, description }) {
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)("div", { className: "wp-block-legacy-widget-inspector-card", children: [
      /* @__PURE__ */ (0, import_jsx_runtime6.jsx)("h3", { className: "wp-block-legacy-widget-inspector-card__name", children: name3 }),
      /* @__PURE__ */ (0, import_jsx_runtime6.jsx)("span", { children: description })
    ] });
  }

  // packages/widgets/build-module/blocks/legacy-widget/edit/form.mjs
  var import_element = __toESM(require_element(), 1);
  var import_data2 = __toESM(require_data(), 1);
  var import_notices = __toESM(require_notices(), 1);
  var import_i18n3 = __toESM(require_i18n(), 1);
  var import_components2 = __toESM(require_components(), 1);
  var import_compose2 = __toESM(require_compose(), 1);

  // packages/widgets/build-module/blocks/legacy-widget/edit/control.mjs
  var import_api_fetch = __toESM(require_api_fetch(), 1);
  var import_compose = __toESM(require_compose(), 1);
  var import_i18n2 = __toESM(require_i18n(), 1);
  var Control = class {
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
      this.handleFormChange = (0, import_compose.debounce)(
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
              (0, import_i18n2.__)("Save")
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
  };
  var lastNumber = 0;
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
      widget = await (0, import_api_fetch.default)({
        path: `/wp/v2/widgets/${id}?context=edit`,
        method: "PUT",
        data: {
          form_data: formData
        }
      });
    } else {
      widget = await (0, import_api_fetch.default)({
        path: `/wp/v2/widgets/${id}?context=edit`,
        method: "GET"
      });
    }
    return { form: widget.rendered_form };
  }
  async function encodeWidget({ idBase, instance, number, formData = null }) {
    const response = await (0, import_api_fetch.default)({
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

  // packages/widgets/build-module/blocks/legacy-widget/edit/form.mjs
  var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
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
    const ref = (0, import_element.useRef)();
    const isMediumLargeViewport = (0, import_compose2.useViewportMatch)("small");
    const outgoingInstances = (0, import_element.useRef)(/* @__PURE__ */ new Set());
    const incomingInstances = (0, import_element.useRef)(/* @__PURE__ */ new Set());
    const { createNotice } = (0, import_data2.useDispatch)(import_notices.store);
    (0, import_element.useEffect)(() => {
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
            (0, import_i18n3.sprintf)(
              /* translators: %s: the name of the affected block. */
              (0, import_i18n3.__)(
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
      return /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(
        "div",
        {
          className: clsx_default({
            "wp-block-legacy-widget__container": isVisible
          }),
          children: [
            isVisible && /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("h3", { className: "wp-block-legacy-widget__edit-form-title", children: title }),
            /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
              import_components2.Popover,
              {
                focusOnMount: false,
                placement: "right",
                offset: 32,
                resize: false,
                flip: false,
                shift: true,
                children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
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
    return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
      "div",
      {
        ref,
        className: "wp-block-legacy-widget__edit-form",
        hidden: !isVisible,
        children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("h3", { className: "wp-block-legacy-widget__edit-form-title", children: title })
      }
    );
  }

  // packages/widgets/build-module/blocks/legacy-widget/edit/preview.mjs
  var import_compose3 = __toESM(require_compose(), 1);
  var import_element2 = __toESM(require_element(), 1);
  var import_components3 = __toESM(require_components(), 1);
  var import_i18n4 = __toESM(require_i18n(), 1);
  var import_api_fetch2 = __toESM(require_api_fetch(), 1);
  var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
  function Preview({ idBase, instance, isVisible }) {
    const [isLoaded, setIsLoaded] = (0, import_element2.useState)(false);
    const [srcDoc, setSrcDoc] = (0, import_element2.useState)("");
    (0, import_element2.useEffect)(() => {
      const abortController = typeof window.AbortController === "undefined" ? void 0 : new window.AbortController();
      async function fetchPreviewHTML() {
        const restRoute = `/wp/v2/widget-types/${idBase}/render`;
        return await (0, import_api_fetch2.default)({
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
    const ref = (0, import_compose3.useRefEffect)(
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
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(import_jsx_runtime8.Fragment, { children: [
      isVisible && !isLoaded && /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components3.Placeholder, { children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components3.Spinner, {}) }),
      /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
        "div",
        {
          className: clsx_default("wp-block-legacy-widget__edit-preview", {
            "is-offscreen": !isVisible || !isLoaded
          }),
          children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components3.Disabled, { children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
            "iframe",
            {
              ref,
              className: "wp-block-legacy-widget__edit-preview-iframe",
              tabIndex: "-1",
              title: (0, import_i18n4.__)("Legacy Widget Preview"),
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

  // packages/widgets/build-module/blocks/legacy-widget/edit/no-preview.mjs
  var import_i18n5 = __toESM(require_i18n(), 1);
  var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
  function NoPreview({ name: name3 }) {
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)("div", { className: "wp-block-legacy-widget__edit-no-preview", children: [
      name3 && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("h3", { children: name3 }),
      /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("p", { children: (0, import_i18n5.__)("No preview available.") })
    ] });
  }

  // packages/widgets/build-module/blocks/legacy-widget/edit/convert-to-blocks-button.mjs
  var import_data3 = __toESM(require_data(), 1);
  var import_block_editor2 = __toESM(require_block_editor(), 1);
  var import_components4 = __toESM(require_components(), 1);
  var import_blocks = __toESM(require_blocks(), 1);
  var import_i18n6 = __toESM(require_i18n(), 1);
  var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
  function ConvertToBlocksButton({ clientId, rawInstance }) {
    const { replaceBlocks } = (0, import_data3.useDispatch)(import_block_editor2.store);
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
      import_components4.ToolbarButton,
      {
        onClick: () => {
          if (rawInstance.title) {
            replaceBlocks(clientId, [
              (0, import_blocks.createBlock)("core/heading", {
                content: rawInstance.title
              }),
              ...(0, import_blocks.rawHandler)({ HTML: rawInstance.text })
            ]);
          } else {
            replaceBlocks(
              clientId,
              (0, import_blocks.rawHandler)({ HTML: rawInstance.text })
            );
          }
        },
        children: (0, import_i18n6.__)("Convert to blocks")
      }
    );
  }

  // packages/widgets/build-module/blocks/legacy-widget/edit/index.mjs
  var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
  function Edit(props) {
    const { id, idBase } = props.attributes;
    const { isWide = false } = props;
    const blockProps = (0, import_block_editor3.useBlockProps)({
      className: clsx_default({
        "is-wide-widget": isWide
      })
    });
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)("div", { ...blockProps, children: !id && !idBase ? /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(Empty, { ...props }) : /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(NotEmpty, { ...props }) });
  }
  function Empty({ attributes: { id, idBase }, setAttributes }) {
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
      import_components5.Placeholder,
      {
        icon: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_block_editor3.BlockIcon, { icon: brush_default }),
        label: (0, import_i18n7.__)("Legacy Widget"),
        children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components5.Flex, { children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components5.FlexBlock, { children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
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
    const [hasPreview, setHasPreview] = (0, import_element3.useState)(null);
    const widgetTypeId = id ?? idBase;
    const { record: widgetType, hasResolved: hasResolvedWidgetType } = (0, import_core_data2.useEntityRecord)("root", "widgetType", widgetTypeId);
    const setInstance = (0, import_element3.useCallback)((nextInstance) => {
      setAttributes({ instance: nextInstance });
    }, []);
    if (!widgetType && hasResolvedWidgetType) {
      return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
        import_components5.Placeholder,
        {
          icon: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_block_editor3.BlockIcon, { icon: brush_default }),
          label: (0, import_i18n7.__)("Legacy Widget"),
          children: (0, import_i18n7.__)("Widget is missing.")
        }
      );
    }
    if (!hasResolvedWidgetType) {
      return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components5.Placeholder, { children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components5.Spinner, {}) });
    }
    const mode = idBase && !isSelected ? "preview" : "edit";
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_jsx_runtime11.Fragment, { children: [
      idBase === "text" && /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_block_editor3.BlockControls, { group: "other", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
        ConvertToBlocksButton,
        {
          clientId,
          rawInstance: instance.raw
        }
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_block_editor3.InspectorControls, { children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
        InspectorCard,
        {
          name: widgetType.name,
          description: widgetType.description
        }
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
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
      idBase && /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_jsx_runtime11.Fragment, { children: [
        hasPreview === null && mode === "preview" && /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components5.Placeholder, { children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components5.Spinner, {}) }),
        hasPreview === true && /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
          Preview,
          {
            idBase,
            instance,
            isVisible: mode === "preview"
          }
        ),
        hasPreview === false && mode === "preview" && /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(NoPreview, { name: widgetType.name })
      ] })
    ] });
  }

  // packages/widgets/build-module/blocks/legacy-widget/transforms.mjs
  var import_blocks2 = __toESM(require_blocks(), 1);
  var legacyWidgetTransforms = [
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
      transformBlock: ({ content }) => (0, import_blocks2.createBlock)("core/html", {}, [], [content])
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
  ].map(({ block, widget, transform, transformBlock }) => {
    return {
      type: "block",
      blocks: [block],
      isMatch: ({ idBase, instance }) => {
        return idBase === widget && !!instance?.raw;
      },
      transform: ({ instance }) => {
        const transformedBlock = transformBlock ? transformBlock(instance.raw) : (0, import_blocks2.createBlock)(
          block,
          transform ? transform(instance.raw) : void 0
        );
        if (!instance.raw?.title) {
          return transformedBlock;
        }
        return [
          (0, import_blocks2.createBlock)("core/heading", {
            content: instance.raw.title
          }),
          transformedBlock
        ];
      }
    };
  });
  var transforms = {
    to: legacyWidgetTransforms
  };
  var transforms_default = transforms;

  // packages/widgets/build-module/blocks/legacy-widget/index.mjs
  var { name } = block_default;
  var settings = {
    icon: widget_default,
    edit: Edit,
    transforms: transforms_default
  };

  // packages/widgets/build-module/blocks/widget-group/index.mjs
  var widget_group_exports = {};
  __export(widget_group_exports, {
    metadata: () => block_default2,
    name: () => name2,
    settings: () => settings2
  });
  var import_i18n9 = __toESM(require_i18n(), 1);
  var import_blocks3 = __toESM(require_blocks(), 1);

  // packages/widgets/build-module/blocks/widget-group/block.json
  var block_default2 = {
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

  // packages/widgets/build-module/blocks/widget-group/edit.mjs
  var import_block_editor4 = __toESM(require_block_editor(), 1);
  var import_components6 = __toESM(require_components(), 1);
  var import_i18n8 = __toESM(require_i18n(), 1);
  var import_data4 = __toESM(require_data(), 1);
  var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
  function Edit2(props) {
    const { clientId } = props;
    const hasInnerBlocks = (0, import_data4.useSelect)(
      (select2) => select2(import_block_editor4.store).getBlockCount(clientId) > 0,
      [clientId]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)("div", { ...(0, import_block_editor4.useBlockProps)({ className: "widget" }), children: !hasInnerBlocks ? /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(PlaceholderContent, { ...props }) : /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(PreviewContent, { ...props }) });
  }
  function PlaceholderContent({ clientId }) {
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsxs)(import_jsx_runtime12.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
        import_components6.Placeholder,
        {
          className: "wp-block-widget-group__placeholder",
          icon: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_block_editor4.BlockIcon, { icon: group_default }),
          label: (0, import_i18n8.__)("Widget Group"),
          children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_block_editor4.ButtonBlockAppender, { rootClientId: clientId })
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_block_editor4.InnerBlocks, { renderAppender: false })
    ] });
  }
  function PreviewContent({ attributes, setAttributes }) {
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsxs)(import_jsx_runtime12.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
        import_block_editor4.RichText,
        {
          tagName: "h2",
          identifier: "title",
          className: "widget-title",
          allowedFormats: [],
          placeholder: (0, import_i18n8.__)("Title"),
          value: attributes.title ?? "",
          onChange: (title) => setAttributes({ title })
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_block_editor4.InnerBlocks, {})
    ] });
  }

  // packages/widgets/build-module/blocks/widget-group/save.mjs
  var import_block_editor5 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
  function save({ attributes }) {
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(import_jsx_runtime13.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
        import_block_editor5.RichText.Content,
        {
          tagName: "h2",
          className: "widget-title",
          value: attributes.title
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime13.jsx)("div", { className: "wp-widget-group__inner-blocks", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_block_editor5.InnerBlocks.Content, {}) })
    ] });
  }

  // packages/widgets/build-module/blocks/widget-group/deprecated.mjs
  var import_block_editor6 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
  var v1 = {
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
      return /* @__PURE__ */ (0, import_jsx_runtime14.jsxs)(import_jsx_runtime14.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
          import_block_editor6.RichText.Content,
          {
            tagName: "h2",
            className: "widget-title",
            value: attributes.title
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(import_block_editor6.InnerBlocks.Content, {})
      ] });
    }
  };
  var deprecated_default = [v1];

  // packages/widgets/build-module/blocks/widget-group/index.mjs
  var { name: name2 } = block_default2;
  var settings2 = {
    title: (0, import_i18n9.__)("Widget Group"),
    description: (0, import_i18n9.__)(
      "Create a classic widget layout with a title that\u2019s styled by your theme for your widget areas."
    ),
    icon: group_default,
    __experimentalLabel: ({ name: label }) => label,
    edit: Edit2,
    save,
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
            let innerBlocks = blocks.map(
              (block) => (0, import_blocks3.cloneSanitizedBlock)(block)
            );
            const firstHeadingBlock = innerBlocks[0].name === "core/heading" ? innerBlocks[0] : null;
            innerBlocks = innerBlocks.filter(
              (block) => block !== firstHeadingBlock
            );
            return (0, import_blocks3.createBlock)(
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

  // packages/widgets/build-module/components/move-to-widget-area/index.mjs
  var import_components7 = __toESM(require_components(), 1);
  var import_i18n10 = __toESM(require_i18n(), 1);
  var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
  function MoveToWidgetArea({
    currentWidgetAreaId,
    widgetAreas,
    onSelect
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_components7.ToolbarGroup, { children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_components7.ToolbarItem, { children: (toggleProps) => /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
      import_components7.DropdownMenu,
      {
        icon: move_to_default,
        label: (0, import_i18n10.__)("Move to widget area"),
        toggleProps,
        children: ({ onClose }) => /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(import_components7.MenuGroup, { label: (0, import_i18n10.__)("Move to"), children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
          import_components7.MenuItemsChoice,
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

  // packages/widgets/build-module/utils.mjs
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

  // packages/widgets/build-module/register-legacy-widget-variations.mjs
  var import_data5 = __toESM(require_data(), 1);
  var import_core_data3 = __toESM(require_core_data(), 1);
  var import_blocks4 = __toESM(require_blocks(), 1);
  function registerLegacyWidgetVariations(settings3) {
    const unsubscribe = (0, import_data5.subscribe)(() => {
      const hiddenIds = settings3?.widgetTypesToHideFromLegacyWidgetBlock ?? [];
      const widgetTypes = (0, import_data5.select)(import_core_data3.store).getWidgetTypes({ per_page: -1 })?.filter((widgetType) => !hiddenIds.includes(widgetType.id));
      if (widgetTypes) {
        unsubscribe();
        (0, import_data5.dispatch)(import_blocks4.store).addBlockVariations(
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

  // packages/widgets/build-module/index.mjs
  function registerLegacyWidgetBlock(supports = {}) {
    const { metadata, settings: settings3, name: name3 } = legacy_widget_exports;
    (0, import_blocks5.registerBlockType)(
      { name: name3, ...metadata },
      {
        ...settings3,
        supports: {
          ...settings3.supports,
          ...supports
        }
      }
    );
  }
  function registerWidgetGroupBlock(supports = {}) {
    const { metadata, settings: settings3, name: name3 } = widget_group_exports;
    (0, import_blocks5.registerBlockType)(
      { name: name3, ...metadata },
      {
        ...settings3,
        supports: {
          ...settings3.supports,
          ...supports
        }
      }
    );
  }
  return __toCommonJS(index_exports);
})();
(window.wp ||= {}).widgets = wp.widgets;
})();
