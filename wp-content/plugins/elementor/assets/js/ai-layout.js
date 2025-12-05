/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

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

/***/ "../modules/ai/assets/js/editor/ai-layout-behavior.js":
/*!************************************************************!*\
  !*** ../modules/ai/assets/js/editor/ai-layout-behavior.js ***!
  \************************************************************/
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
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _editorIntegration = __webpack_require__(/*! ./utils/editor-integration */ "../modules/ai/assets/js/editor/utils/editor-integration.js");
var _config = __webpack_require__(/*! ./pages/form-layout/context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var AiLayoutBehavior = exports["default"] = /*#__PURE__*/function (_Marionette$Behavior) {
  function AiLayoutBehavior() {
    var _this;
    (0, _classCallCheck2.default)(this, AiLayoutBehavior);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, AiLayoutBehavior, [].concat(args));
    (0, _defineProperty2.default)(_this, "previewContainer", null);
    return _this;
  }
  (0, _inherits2.default)(AiLayoutBehavior, _Marionette$Behavior);
  return (0, _createClass2.default)(AiLayoutBehavior, [{
    key: "ui",
    value: function ui() {
      return {
        aiButton: '.e-ai-layout-button',
        addTemplateButton: '.elementor-add-template-button'
      };
    }
  }, {
    key: "events",
    value: function events() {
      return {
        'click @ui.aiButton': 'onAiButtonClick'
      };
    }
  }, {
    key: "onAiButtonClick",
    value: function onAiButtonClick(e) {
      e.stopPropagation();
      window.elementorAiCurrentContext = this.getOption('context');
      (0, _editorIntegration.renderLayoutApp)({
        parentContainer: elementor.getPreviewContainer(),
        mode: _config.MODE_LAYOUT,
        at: this.view.getOption('at'),
        onInsert: this.onInsert.bind(this),
        onRenderApp: function onRenderApp(args) {
          args.previewContainer.init();
        },
        onGenerate: function onGenerate(args) {
          args.previewContainer.reset();
        }
      });
    }
  }, {
    key: "hideDropArea",
    value: function hideDropArea() {
      this.view.onCloseButtonClick();
    }
  }, {
    key: "onInsert",
    value: function onInsert(template) {
      this.hideDropArea();
      (0, _editorIntegration.importToEditor)({
        parentContainer: elementor.getPreviewContainer(),
        at: this.view.getOption('at'),
        template: template,
        historyTitle: (0, _i18n.__)('AI Layout', 'elementor')
      });
    }
  }, {
    key: "onRender",
    value: function onRender() {
      var $button = jQuery('<button>', {
        type: 'button',
        class: 'e-ai-layout-button elementor-add-section-area-button e-button-primary',
        title: (0, _i18n.__)('Build with AI', 'elementor'),
        'aria-label': (0, _i18n.__)('Build with AI', 'elementor')
      });
      $button.html("\n\t\t\t<div class=\"e-ai-layout-button--sparkle\"></div>\n\t\t\t<div class=\"e-ai-layout-button--sparkle\"></div>\n\t\t\t<div class=\"e-ai-layout-button--sparkle\"></div>\n\t\t\t<div class=\"e-ai-layout-button--sparkle\"></div>\n\t\t\t<div class=\"e-ai-layout-button--sparkle\"></div>\n\t\t\t<div class=\"e-ai-layout-button--sparkle\"></div>\n\t\t\t<div class=\"e-ai-layout-button--sparkle\"></div>\n\t\t\t<i class=\"eicon-ai\" aria-hidden=\"true\"></i>\n\t\t");
      this.ui.addTemplateButton.after($button);
    }
  }]);
}(Marionette.Behavior);

/***/ }),

/***/ "../modules/ai/assets/js/editor/api/index.js":
/*!***************************************************!*\
  !*** ../modules/ai/assets/js/editor/api/index.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.uploadImage = exports.toggleFavoriteHistoryItem = exports.setStatusFeedback = exports.setGetStarted = exports.getUserInformation = exports.getTextToImageGeneration = exports.getRemoteFrontendConfig = exports.getRemoteConfig = exports.getProductImageUnification = exports.getLayoutPromptEnhanced = exports.getImageToImageUpscale = exports.getImageToImageReplaceBackground = exports.getImageToImageRemoveText = exports.getImageToImageRemoveBackground = exports.getImageToImageOutPainting = exports.getImageToImageMaskGeneration = exports.getImageToImageMaskCleanup = exports.getImageToImageIsolateObjects = exports.getImageToImageGeneration = exports.getImagePromptEnhanced = exports.getHistory = exports.getFeaturedImage = exports.getExcerpt = exports.getEditText = exports.getCustomCode = exports.getCustomCSS = exports.getCompletionText = exports.getAnimation = exports.generateLayout = exports.deleteHistoryItem = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var request = function request(endpoint) {
  var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var immediately = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var signal = arguments.length > 3 ? arguments[3] : undefined;
  if (Object.keys(data).length) {
    if (window.elementorAiCurrentContext) {
      data.context = window.elementorAiCurrentContext;
    } else {
      data.context = window.elementorWpAiCurrentContext;
    }
  }
  return new Promise(function (resolve, reject) {
    var ajaxData = elementorCommon.ajax.addRequest(endpoint, {
      success: resolve,
      error: reject,
      data: data,
      unique_id: data.unique_id
    }, immediately);
    if (signal && ajaxData.jqXhr) {
      signal.addEventListener('abort', ajaxData.jqXhr.abort);
    }
  });
};
var getUserInformation = exports.getUserInformation = function getUserInformation(immediately) {
  return request('ai_get_user_information', undefined, immediately);
};
var getRemoteConfig = exports.getRemoteConfig = function getRemoteConfig() {
  return request('ai_get_remote_config');
};
var getRemoteFrontendConfig = exports.getRemoteFrontendConfig = function getRemoteFrontendConfig(payload, immediately) {
  return request('ai_get_remote_frontend_config', {
    payload: payload
  }, immediately);
};
var getCompletionText = exports.getCompletionText = function getCompletionText(payload) {
  return request('ai_get_completion_text', {
    payload: payload
  });
};
var getExcerpt = exports.getExcerpt = function getExcerpt(payload) {
  return request('ai_get_excerpt', {
    payload: payload
  });
};
var getFeaturedImage = exports.getFeaturedImage = function getFeaturedImage(payload) {
  return request('ai_get_featured_image', {
    payload: payload
  });
};
var getEditText = exports.getEditText = function getEditText(payload) {
  return request('ai_get_edit_text', {
    payload: payload
  });
};
var getCustomCode = exports.getCustomCode = function getCustomCode(payload) {
  return request('ai_get_custom_code', {
    payload: payload
  });
};
var getCustomCSS = exports.getCustomCSS = function getCustomCSS(payload) {
  return request('ai_get_custom_css', {
    payload: payload
  });
};
var setGetStarted = exports.setGetStarted = function setGetStarted() {
  return request('ai_set_get_started');
};
var setStatusFeedback = exports.setStatusFeedback = function setStatusFeedback(responseId) {
  return request('ai_set_status_feedback', {
    response_id: responseId
  }, true);
};
var getTextToImageGeneration = exports.getTextToImageGeneration = function getTextToImageGeneration(payload) {
  return request('ai_get_text_to_image', {
    payload: payload
  });
};
var getImageToImageGeneration = exports.getImageToImageGeneration = function getImageToImageGeneration(payload) {
  return request('ai_get_image_to_image', {
    payload: payload
  });
};
var getImageToImageMaskCleanup = exports.getImageToImageMaskCleanup = function getImageToImageMaskCleanup(payload) {
  return request('ai_get_image_to_image_mask_cleanup', {
    payload: payload
  });
};
var getImageToImageMaskGeneration = exports.getImageToImageMaskGeneration = function getImageToImageMaskGeneration(payload) {
  return request('ai_get_image_to_image_mask', {
    payload: payload
  });
};
var getImageToImageOutPainting = exports.getImageToImageOutPainting = function getImageToImageOutPainting(payload) {
  return request('ai_get_image_to_image_outpainting', {
    payload: payload
  });
};
var getImageToImageUpscale = exports.getImageToImageUpscale = function getImageToImageUpscale(payload) {
  return request('ai_get_image_to_image_upscale', {
    payload: payload
  });
};
var getImageToImageRemoveBackground = exports.getImageToImageRemoveBackground = function getImageToImageRemoveBackground(payload) {
  return request('ai_get_image_to_image_remove_background', {
    payload: payload
  });
};
var getImageToImageIsolateObjects = exports.getImageToImageIsolateObjects = function getImageToImageIsolateObjects(payload) {
  return request('ai_get_image_to_image_isolate_objects', {
    payload: payload
  });
};
var getImageToImageReplaceBackground = exports.getImageToImageReplaceBackground = function getImageToImageReplaceBackground(payload) {
  return request('ai_get_image_to_image_replace_background', {
    payload: payload
  });
};
var getImageToImageRemoveText = exports.getImageToImageRemoveText = function getImageToImageRemoveText(image) {
  return request('ai_get_image_to_image_remove_text', {
    image: image
  });
};
var getImagePromptEnhanced = exports.getImagePromptEnhanced = function getImagePromptEnhanced(prompt) {
  return request('ai_get_image_prompt_enhancer', {
    prompt: prompt
  });
};
var getProductImageUnification = exports.getProductImageUnification = function getProductImageUnification(payload, immediately) {
  return request('ai_get_product_image_unification', {
    payload: payload
  }, immediately);
};
var getAnimation = exports.getAnimation = function getAnimation(payload) {
  return request('ai_get_animation', {
    payload: payload
  });
};
var uploadImage = exports.uploadImage = function uploadImage(image) {
  return request('ai_upload_image', _objectSpread(_objectSpread({}, image), {}, {
    editor_post_id: image.image.editor_post_id,
    unique_id: image.image.unique_id
  }));
};

/**
 * @typedef {Object} AttachmentPropType - See ./types/attachment.js
 * @typedef {Object} requestBody
 * @property {string}               prompt             - Prompt to generate the layout from.
 * @property {0|1|2}                [variationType]    - Type of the layout to generate (actually it's a position).
 * @property {string[]}             [prevGeneratedIds] - Previously generated ids for exclusion on regeneration.
 * @property {AttachmentPropType[]} [attachments]      - Attachments to use for the generation. currently only `json` type is supported - a container JSON to generate variations from.
 */

/**
 * @param {requestBody} requestBody
 * @param {AbortSignal} [signal]
 */
var generateLayout = exports.generateLayout = function generateLayout(requestBody, signal) {
  return request('ai_generate_layout', requestBody, true, signal);
};
var getLayoutPromptEnhanced = exports.getLayoutPromptEnhanced = function getLayoutPromptEnhanced(prompt, enhanceType) {
  return request('ai_get_layout_prompt_enhancer', {
    prompt: prompt,
    enhance_type: enhanceType
  });
};
var getHistory = exports.getHistory = function getHistory(type, page, limit) {
  return request('ai_get_history', {
    type: type,
    page: page,
    limit: limit
  });
};
var deleteHistoryItem = exports.deleteHistoryItem = function deleteHistoryItem(id) {
  return request('ai_delete_history_item', {
    id: id
  });
};
var toggleFavoriteHistoryItem = exports.toggleFavoriteHistoryItem = function toggleFavoriteHistoryItem(id) {
  return request('ai_toggle_favorite_history_item', {
    id: id
  });
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/alert-dialog.js":
/*!*****************************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/alert-dialog.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.AlertDialog = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var AlertDialog = exports.AlertDialog = function AlertDialog(props) {
  var _useState = (0, _react.useState)(true),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isShown = _useState2[0],
    setIsShown = _useState2[1];
  if (!isShown) {
    return null;
  }
  return /*#__PURE__*/_react.default.createElement(_ui.Dialog, {
    open: true,
    maxWidth: "lg"
  }, /*#__PURE__*/_react.default.createElement(_ui.DialogContent, {
    sx: {
      padding: 0
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    sx: {
      textAlign: 'center',
      padding: 3
    }
  }, props.message), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    alignItems: "center",
    spacing: 2,
    marginBottom: 2
  }, /*#__PURE__*/_react.default.createElement(_ui.Button, {
    variant: "contained",
    type: "button",
    color: "primary",
    onClick: function onClick() {
      var _props$onClose;
      setIsShown(false);
      (_props$onClose = props.onClose) === null || _props$onClose === void 0 || _props$onClose.call(props);
    }
  }, (0, _i18n.__)('Close', 'elementor')))));
};
AlertDialog.propTypes = {
  message: _propTypes.default.string.isRequired,
  onClose: _propTypes.default.func
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/dialog-header.js":
/*!******************************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/dialog-header.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _icons = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
var ElementorLogo = function ElementorLogo(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 32 32"
  }, props), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M2.69648 24.8891C0.938383 22.2579 0 19.1645 0 16C0 11.7566 1.68571 7.68687 4.68629 4.68629C7.68687 1.68571 11.7566 0 16 0C19.1645 0 22.2579 0.938383 24.8891 2.69648C27.5203 4.45459 29.5711 6.95344 30.7821 9.87706C31.9931 12.8007 32.3099 16.0177 31.6926 19.1214C31.0752 22.2251 29.5514 25.0761 27.3137 27.3137C25.0761 29.5514 22.2251 31.0752 19.1214 31.6926C16.0177 32.3099 12.8007 31.9931 9.87706 30.7821C6.95344 29.5711 4.45459 27.5203 2.69648 24.8891ZM12.0006 9.33281H9.33437V22.6665H12.0006V9.33281ZM22.6657 9.33281H14.6669V11.9991H22.6657V9.33281ZM22.6657 14.6654H14.6669V17.3316H22.6657V14.6654ZM22.6657 20.0003H14.6669V22.6665H22.6657V20.0003Z"
  }));
};
var StyledElementorLogo = (0, _ui.styled)(ElementorLogo)(function (_ref) {
  var theme = _ref.theme;
  return {
    width: theme.spacing(3),
    height: theme.spacing(3),
    '& path': {
      fill: theme.palette.text.primary
    }
  };
});
var DialogHeader = function DialogHeader(props) {
  var hideAiBetaLogo = props.hideAiBetaLogo,
    onClose = props.onClose,
    children = props.children;
  return /*#__PURE__*/_react.default.createElement(_ui.AppBar, {
    sx: {
      fontWeight: 'normal'
    },
    color: "transparent",
    position: "relative"
  }, /*#__PURE__*/_react.default.createElement(_ui.Toolbar, {
    variant: "dense"
  }, !hideAiBetaLogo && /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(StyledElementorLogo, {
    sx: {
      mr: 1
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    component: "span",
    variant: "subtitle2",
    sx: {
      fontWeight: 'bold',
      textTransform: 'uppercase'
    }
  }, (0, _i18n.__)('AI', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Chip, {
    label: (0, _i18n.__)('Beta', 'elementor'),
    color: "default",
    size: "small",
    sx: {
      ml: 1
    }
  })), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    spacing: 1,
    alignItems: "center",
    sx: {
      ml: hideAiBetaLogo ? 0 : 'auto'
    }
  }, children, /*#__PURE__*/_react.default.createElement(_ui.IconButton, {
    size: "small",
    "aria-label": "close",
    onClick: onClose,
    sx: {
      '&.MuiButtonBase-root': {
        mr: -1
      }
    }
  }, /*#__PURE__*/_react.default.createElement(_icons.XIcon, null)))));
};
DialogHeader.propTypes = {
  onClose: _propTypes.default.func.isRequired,
  hideAiBetaLogo: _propTypes.default.bool,
  children: _propTypes.default.oneOfType([_propTypes.default.arrayOf(_propTypes.default.node), _propTypes.default.node])
};
var _default = exports["default"] = DialogHeader;

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/loader.js":
/*!***********************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/loader.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _excluded = ["sx", "BoxProps"];
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var Loader = function Loader(_ref) {
  var _ref$sx = _ref.sx,
    sx = _ref$sx === void 0 ? {} : _ref$sx,
    _ref$BoxProps = _ref.BoxProps,
    BoxProps = _ref$BoxProps === void 0 ? {} : _ref$BoxProps,
    props = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  return /*#__PURE__*/_react.default.createElement(_ui.Box, (0, _extends2.default)({
    width: "100%",
    display: "flex",
    alignItems: "center"
  }, BoxProps, {
    sx: _objectSpread({
      px: 1.5,
      minHeight: function minHeight(theme) {
        return theme.spacing(5);
      }
    }, BoxProps.sx || {})
  }), /*#__PURE__*/_react.default.createElement(_ui.LinearProgress, (0, _extends2.default)({
    color: "secondary"
  }, props, {
    sx: _objectSpread({
      width: '100%'
    }, sx)
  })));
};
Loader.propTypes = {
  sx: _propTypes.default.object,
  BoxProps: _propTypes.default.object
};
var _default = exports["default"] = Loader;

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/prompt-dialog.js":
/*!******************************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/prompt-dialog.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _reactDraggable = _interopRequireDefault(__webpack_require__(/*! react-draggable */ "../node_modules/react-draggable/build/cjs/cjs.js"));
var _dialogHeader = _interopRequireDefault(__webpack_require__(/*! ./dialog-header */ "../modules/ai/assets/js/editor/components/dialog-header.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var DraggablePaper = function DraggablePaper(props) {
  var _useState = (0, _react.useState)({
      x: 0,
      y: 0
    }),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    position = _useState2[0],
    setPosition = _useState2[1];
  var paperRef = (0, _react.useRef)(null);
  var timeout = (0, _react.useRef)(null);
  var onDrag = function onDrag(_e, _ref) {
    var x = _ref.x,
      y = _ref.y;
    return setPosition({
      x: x,
      y: y
    });
  };
  var handlePositionBoundaries = function handlePositionBoundaries() {
    clearTimeout(timeout.current);

    // Ensuring the dialog header, which is used as the dialog dragging handle, does not exceed the screen.
    timeout.current = setTimeout(function () {
      var _paperRef$current;
      var dialogTop = (_paperRef$current = paperRef.current) === null || _paperRef$current === void 0 ? void 0 : _paperRef$current.getBoundingClientRect().top;
      if (dialogTop < 0) {
        setPosition(function (prev) {
          return _objectSpread(_objectSpread({}, prev), {}, {
            y: prev.y - dialogTop
          });
        });
      }
    }, 50);
  };
  (0, _react.useEffect)(function () {
    var resizeObserver = new ResizeObserver(handlePositionBoundaries);
    resizeObserver.observe(paperRef.current);
    return function () {
      resizeObserver.disconnect();
    };
  }, []);
  return /*#__PURE__*/_react.default.createElement(_reactDraggable.default, {
    position: position,
    onDrag: onDrag,
    handle: ".MuiAppBar-root",
    cancel: '[class*="MuiDialogContent-root"]',
    bounds: "parent"
  }, /*#__PURE__*/_react.default.createElement(_ui.Paper, (0, _extends2.default)({}, props, {
    ref: paperRef
  })));
};
var PromptDialog = function PromptDialog(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.Dialog, (0, _extends2.default)({
    scroll: "paper",
    open: true,
    fullWidth: true,
    hideBackdrop: true,
    PaperComponent: DraggablePaper,
    disableScrollLock: true,
    sx: {
      '& .MuiDialog-container': {
        alignItems: 'flex-start',
        mt: '18vh'
      }
    },
    PaperProps: {
      sx: {
        m: 0,
        maxHeight: '76vh'
      }
    }
  }, props), props.children);
};
PromptDialog.propTypes = {
  onClose: _propTypes.default.func.isRequired,
  children: _propTypes.default.node,
  maxWidth: _propTypes.default.oneOf(['xs', 'sm', 'md', 'lg', 'xl', false])
};
PromptDialog.Header = _dialogHeader.default;
PromptDialog.Content = _ui.DialogContent;
var _default = exports["default"] = PromptDialog;

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/prompt-error-message.js":
/*!*************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/prompt-error-message.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var sprintf = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["sprintf"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _excluded = ["error", "onRetry", "actionPosition"];
var PromptErrorMessage = function PromptErrorMessage(_ref) {
  var error = _ref.error,
    _ref$onRetry = _ref.onRetry,
    onRetry = _ref$onRetry === void 0 ? function () {} : _ref$onRetry,
    _ref$actionPosition = _ref.actionPosition,
    actionPosition = _ref$actionPosition === void 0 ? 'default' : _ref$actionPosition,
    props = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  function getQuotaReachedTrailMessage(featureName) {
    if (!featureName) {
      return {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('It\'s time to upgrade.', 'elementor')),
        description: (0, _i18n.__)('Enjoy the free trial? Upgrade now for unlimited access to built-in image, text and custom code generators.', 'elementor'),
        buttonText: (0, _i18n.__)('Upgrade', 'elementor'),
        buttonAction: function buttonAction() {
          return window.open('https://go.elementor.com/ai-popup-purchase-limit-reached/', '_blank');
        }
      };
    }
    return {
      // Translators: %s is the feature name.
      text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, sprintf((0, _i18n.__)('You\'ve used all AI credits for %s.', 'elementor'), featureName.toLowerCase())),
      description: (0, _i18n.__)('Upgrade now to keep using this feature. You still have credits for other AI features (Text, Code, Images, Containers, etc.)', 'elementor'),
      buttonText: (0, _i18n.__)('Upgrade now', 'elementor'),
      buttonAction: function buttonAction() {
        return window.open('https://go.elementor.com/ai-popup-purchase-limit-reached/', '_blank');
      }
    };
  }
  function getErrorMessage() {
    var _error$extra_data;
    var errMsg = error.message || error;
    var featureName = (_error$extra_data = error.extra_data) === null || _error$extra_data === void 0 ? void 0 : _error$extra_data.featureName;
    var messages = {
      default: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('There was a glitch.', 'elementor')),
        description: (0, _i18n.__)('Wait a moment and give it another go, or try tweaking the prompt.', 'elementor'),
        buttonText: (0, _i18n.__)('Try again', 'elementor'),
        buttonAction: onRetry
      },
      service_outage_internal: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('There was a glitch.', 'elementor')),
        description: (0, _i18n.__)('Wait a moment and give it another go.', 'elementor'),
        buttonText: (0, _i18n.__)('Try again', 'elementor'),
        buttonAction: onRetry
      },
      invalid_connect_data: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('There was a glitch.', 'elementor')),
        description: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, (0, _i18n.__)('Try exiting Elementor and sign in again.', 'elementor'), ' ', /*#__PURE__*/_react.default.createElement("a", {
          href: "https://elementor.com/help/disconnecting-reconnecting-your-elementor-account/",
          target: "_blank",
          rel: "noreferrer"
        }, (0, _i18n.__)('Show me how', 'elementor'))),
        buttonText: (0, _i18n.__)('Reconnect', 'elementor'),
        buttonAction: function buttonAction() {
          return window.open(window.ElementorAiConfig.connect_url);
        }
      },
      not_connected: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('You aren\'t connected to Elementor AI.', 'elementor')),
        description: (0, _i18n.__)('Elementor AI is just a few clicks away. Connect your account to instantly create texts and custom code.', 'elementor'),
        buttonText: (0, _i18n.__)('Connect', 'elementor'),
        buttonAction: function buttonAction() {
          return window.open(window.ElementorAiConfig.connect_url);
        }
      },
      quota_reached_trail: getQuotaReachedTrailMessage(featureName),
      quota_reached_subscription: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('Looks like you\'re out of credits.', 'elementor')),
        description: (0, _i18n.__)('Ready to take it to the next level?', 'elementor'),
        buttonText: (0, _i18n.__)('Upgrade now', 'elementor'),
        buttonAction: function buttonAction() {
          return window.open('https://go.elementor.com/ai-popup-purchase-limit-reached/', '_blank');
        }
      },
      rate_limit_network: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('Whoa! Slow down there.', 'elementor')),
        description: (0, _i18n.__)('We can’t process that many requests so fast. Try again in 15 minutes.', 'elementor')
      },
      invalid_prompts: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('We were unable to generate that prompt.', 'elementor')),
        description: (0, _i18n.__)('Seems like the prompt contains words that could generate harmful content. Write a different prompt to continue.', 'elementor')
      },
      service_unavailable: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('There was a glitch.', 'elementor')),
        description: (0, _i18n.__)('Wait a moment and give it another go, or try tweaking the prompt.', 'elementor'),
        buttonText: (0, _i18n.__)('Try again', 'elementor'),
        buttonAction: onRetry
      },
      request_timeout_error: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('There was a glitch.', 'elementor')),
        description: (0, _i18n.__)('Wait a moment and give it another go, or try tweaking the prompt.', 'elementor'),
        buttonText: (0, _i18n.__)('Try again', 'elementor'),
        buttonAction: onRetry
      },
      invalid_token: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('Try again', 'elementor')),
        description: (0, _i18n.__)('Try exiting Elementor and sign in again.', 'elementor'),
        buttonText: (0, _i18n.__)('Reconnect', 'elementor'),
        buttonAction: onRetry
      },
      file_too_large: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('The file is too large.', 'elementor')),
        description: (0, _i18n.__)('Please upload a file that is less than 4MB.', 'elementor')
      },
      image_resolution_maximum_exceeded: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('The image resolution exceeds the maximum allowed size.', 'elementor')),
        description: (0, _i18n.__)('Please upload a file with dimensions less than 2048x2048 pixels.', 'elementor')
      },
      external_service_unavailable: {
        text: /*#__PURE__*/_react.default.createElement(_ui.AlertTitle, null, (0, _i18n.__)('Temporary external service issue', 'elementor')),
        description: (0, _i18n.__)('It seems that one of our partner services is temporarily unavailable. Please try again in a few minutes.', 'elementor'),
        buttonText: (0, _i18n.__)('Try Again', 'elementor'),
        buttonAction: onRetry
      }
    };
    return messages[errMsg] || messages.default;
  }
  var message = getErrorMessage();
  var action = (message === null || message === void 0 ? void 0 : message.buttonText) && /*#__PURE__*/_react.default.createElement(_ui.Button, {
    color: "inherit",
    size: "small",
    variant: "outlined",
    onClick: message.buttonAction
  }, message.buttonText);
  return /*#__PURE__*/_react.default.createElement(_ui.Alert, (0, _extends2.default)({
    severity: message.severity || 'error',
    action: 'default' === actionPosition && action
  }, props), message.text, message.description, 'bottom' === actionPosition && /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      mt: 1
    }
  }, action));
};
PromptErrorMessage.propTypes = {
  error: _propTypes.default.oneOfType([_propTypes.default.object, _propTypes.default.string]),
  onRetry: _propTypes.default.func,
  actionPosition: _propTypes.default.oneOf(['default', 'bottom'])
};
var _default = exports["default"] = PromptErrorMessage;

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/prompt-library-link.js":
/*!************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/prompt-library-link.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var PromptLibraryLink = function PromptLibraryLink(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, (0, _i18n.__)('For more suggestions, explore our'), ' ', /*#__PURE__*/_react.default.createElement(_ui.Link, {
    href: props.libraryLink,
    className: "elementor-clickable",
    target: "_blank"
  }, (0, _i18n.__)('prompt library')));
};
PromptLibraryLink.propTypes = {
  libraryLink: _propTypes.default.string
};
var _default = exports["default"] = PromptLibraryLink;

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/upgrade-chip.js":
/*!*****************************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/upgrade-chip.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _icons = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var popoverId = 'e-ai-upgrade-popover';
var StyledContent = (0, _ui.styled)(_ui.Paper)(function (_ref) {
  var theme = _ref.theme;
  return {
    position: 'relative',
    '[data-popper-placement="top"] &': {
      marginBottom: theme.spacing(2.5)
    },
    '[data-popper-placement="bottom"] &': {
      marginTop: theme.spacing(2.5)
    },
    padding: theme.spacing(3),
    boxShadow: theme.shadows[4],
    zIndex: '9999'
  };
});
var StyledArrow = (0, _ui.styled)(_ui.Box)(function (_ref2) {
  var theme = _ref2.theme;
  return {
    width: theme.spacing(5),
    height: theme.spacing(2.5),
    position: 'absolute',
    overflow: 'hidden',
    // Override Popper inline styles.
    left: '50% !important',
    transform: 'translateX(-50%) rotate(var(--rotate, 0deg)) !important',
    '[data-popper-placement="top"] &': {
      top: '100%'
    },
    '[data-popper-placement="bottom"] &': {
      '--rotate': '180deg',
      top: "calc(".concat(theme.spacing(2.5), " * -1)")
    },
    '&::after': {
      backgroundColor: theme.palette.background.paper,
      content: '""',
      display: 'block',
      position: 'absolute',
      width: theme.spacing(2.5),
      height: theme.spacing(2.5),
      top: 0,
      left: '50%',
      transform: 'translateX(-50%) translateY(-50%) rotate(45deg)',
      boxShadow: '1px 1px 5px 0px rgba(0, 0, 0, 0.2)',
      backgroundImage: 'linear-gradient(rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.05))'
    }
  };
});
var upgradeBullets = [(0, _i18n.__)('Get spot-on suggestions from AI Copilot and AI Context with appropriate designs, layouts, and content for your business.', 'elementor'), (0, _i18n.__)('Generate professional texts about any topic, in any tone.', 'elementor'), (0, _i18n.__)('Effortlessly create or enhance stunning images and bring your ideas to life.', 'elementor'), (0, _i18n.__)('Unleash infinite possibilities with the custom code generator.', 'elementor'), (0, _i18n.__)('Access 30-days of AI History with the AI Starter plan and 90-days with the Power plan.', 'elementor')];
var Chip = (0, _ui.styled)(_ui.Chip)(function () {
  return {
    '& .MuiChip-label': {
      lineHeight: 1.5
    },
    '& .MuiSvgIcon-root.MuiChip-icon': {
      fontSize: '1.25rem'
    }
  };
});
var UpgradeChip = function UpgradeChip(_ref3) {
  var _ref3$hasSubscription = _ref3.hasSubscription,
    hasSubscription = _ref3$hasSubscription === void 0 ? false : _ref3$hasSubscription,
    _ref3$usagePercentage = _ref3.usagePercentage,
    usagePercentage = _ref3$usagePercentage === void 0 ? 0 : _ref3$usagePercentage;
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isPopoverOpen = _useState2[0],
    setIsPopoverOpen = _useState2[1];
  var anchorEl = (0, _react.useRef)(null);
  var arrowEl = (0, _react.useRef)(null);
  var showPopover = function showPopover() {
    return setIsPopoverOpen(true);
  };
  var hidePopover = function hidePopover() {
    return setIsPopoverOpen(false);
  };
  var actionUrl = 'https://go.elementor.com/ai-popup-purchase-dropdown/';
  if (hasSubscription) {
    actionUrl = usagePercentage >= 100 ? 'https://go.elementor.com/ai-popup-upgrade-limit-reached/' : 'https://go.elementor.com/ai-popup-upgrade-limit-reached-80-percent/';
  }
  var actionLabel = hasSubscription ? (0, _i18n.__)('Upgrade Elementor AI', 'elementor') : (0, _i18n.__)('Get Elementor AI', 'elementor');
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    component: "span",
    "aria-owns": isPopoverOpen ? popoverId : undefined,
    "aria-haspopup": "true",
    onMouseEnter: showPopover,
    onMouseLeave: hidePopover,
    ref: anchorEl,
    display: "flex",
    alignItems: "center"
  }, /*#__PURE__*/_react.default.createElement(Chip, {
    color: "promotion",
    label: (0, _i18n.__)('Upgrade', 'elementor'),
    icon: /*#__PURE__*/_react.default.createElement(_icons.AIIcon, null),
    size: "small"
  }), /*#__PURE__*/_react.default.createElement(_ui.Popper, {
    open: isPopoverOpen,
    anchorEl: anchorEl.current,
    sx: {
      zIndex: '170001',
      maxWidth: 300
    },
    modifiers: [{
      name: 'arrow',
      enabled: true,
      options: {
        element: arrowEl.current
      }
    }]
  }, /*#__PURE__*/_react.default.createElement(StyledContent, null, /*#__PURE__*/_react.default.createElement(StyledArrow, {
    ref: arrowEl
  }), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "h5",
    color: "text.primary"
  }, (0, _i18n.__)('Unlimited access to Elementor AI', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.List, {
    sx: {
      mb: 1
    }
  }, upgradeBullets.map(function (bullet, index) {
    return /*#__PURE__*/_react.default.createElement(_ui.ListItem, {
      key: index,
      disableGutters: true,
      sx: {
        alignItems: 'flex-start'
      }
    }, /*#__PURE__*/_react.default.createElement(_ui.ListItemIcon, null, /*#__PURE__*/_react.default.createElement(_icons.CheckedCircleIcon, null)), /*#__PURE__*/_react.default.createElement(_ui.ListItemText, {
      sx: {
        m: 0
      }
    }, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
      variant: "body2"
    }, bullet)));
  })), /*#__PURE__*/_react.default.createElement(_ui.Button, {
    variant: "contained",
    color: "promotion",
    size: "small",
    href: actionUrl,
    target: "_blank",
    startIcon: /*#__PURE__*/_react.default.createElement(_icons.AIIcon, null),
    sx: {
      '&:hover': {
        color: 'promotion.contrastText'
      }
    }
  }, actionLabel))));
};
var _default = exports["default"] = UpgradeChip;
UpgradeChip.propTypes = {
  hasSubscription: _propTypes.default.bool,
  usagePercentage: _propTypes.default.number
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/voice-promotion-alert.js":
/*!**************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/voice-promotion-alert.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.VoicePromotionAlert = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _bulbIcon = _interopRequireDefault(__webpack_require__(/*! ../icons/bulb-icon */ "../modules/ai/assets/js/editor/icons/bulb-icon.js"));
var _useIntroduction2 = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-introduction */ "../modules/ai/assets/js/editor/hooks/use-introduction.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var VoicePromotionAlert = exports.VoicePromotionAlert = function VoicePromotionAlert(props) {
  var _useIntroduction = (0, _useIntroduction2.default)(props.introductionKey),
    isViewed = _useIntroduction.isViewed,
    markAsViewed = _useIntroduction.markAsViewed;
  if (isViewed) {
    return null;
  }
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: _objectSpread({
      mt: 2
    }, props.sx),
    alignItems: "top"
  }, /*#__PURE__*/_react.default.createElement(_ui.Alert, {
    severity: "info",
    variant: "standard",
    icon: /*#__PURE__*/_react.default.createElement(_bulbIcon.default, {
      sx: {
        alignSelf: 'flex-start'
      }
    }),
    onClose: markAsViewed
  }, (0, _i18n.__)('Get improved results from AI by adding personal context.', 'elementor'), /*#__PURE__*/_react.default.createElement(_ui.Link, {
    onClick: function onClick() {
      return $e.route('panel/global/menu');
    },
    className: "elementor-clickable",
    style: {
      textDecoration: 'none'
    },
    color: "info.main",
    href: "#"
  }, (0, _i18n.__)('Let’s do it', 'elementor'))));
};
VoicePromotionAlert.propTypes = {
  sx: _propTypes.default.object,
  introductionKey: _propTypes.default.string
};
var _default = exports["default"] = VoicePromotionAlert;

/***/ }),

/***/ "../modules/ai/assets/js/editor/components/wizard-dialog.js":
/*!******************************************************************!*\
  !*** ../modules/ai/assets/js/editor/components/wizard-dialog.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _dialogHeader = _interopRequireDefault(__webpack_require__(/*! ./dialog-header */ "../modules/ai/assets/js/editor/components/dialog-header.js"));
var _excluded = ["sx"];
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var WizardDialog = function WizardDialog(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.Dialog, {
    open: true,
    onClose: props.onClose,
    fullWidth: true,
    hideBackdrop: true,
    maxWidth: "lg",
    PaperProps: {
      sx: {
        height: '88vh'
      }
    },
    sx: {
      zIndex: 9999
    }
  }, props.children);
};
WizardDialog.propTypes = {
  onClose: _propTypes.default.func.isRequired,
  children: _propTypes.default.node.isRequired
};
var WizardDialogContent = function WizardDialogContent(_ref) {
  var _ref$sx = _ref.sx,
    sx = _ref$sx === void 0 ? {} : _ref$sx,
    props = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  return /*#__PURE__*/_react.default.createElement(_ui.DialogContent, (0, _extends2.default)({}, props, {
    sx: _objectSpread({
      display: 'flex',
      flexDirection: 'column',
      justifyContent: 'center'
    }, sx)
  }));
};
WizardDialogContent.propTypes = {
  sx: _propTypes.default.object
};
WizardDialog.Header = _dialogHeader.default;
WizardDialog.Content = WizardDialogContent;
var _default = exports["default"] = WizardDialog;

/***/ }),

/***/ "../modules/ai/assets/js/editor/context/requests-ids.js":
/*!**************************************************************!*\
  !*** ../modules/ai/assets/js/editor/context/requests-ids.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.RequestIdsProvider = void 0;
exports.generateIds = generateIds;
exports.useRequestIds = exports.getUniqueId = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var Context = (0, _react.createContext)({});
var useRequestIds = exports.useRequestIds = function useRequestIds() {
  var context = (0, _react.useContext)(Context);
  if (!context) {
    throw new Error('useRequestIds must be used within a RequestIdsProvider');
  }
  return context;
};
var getUniqueId = exports.getUniqueId = function getUniqueId(prefix) {
  return prefix + '-' + Math.random().toString(16).substr(2, 7);
};
window.EDITOR_SESSION_ID = window.EDITOR_SESSION_ID || getUniqueId('editor-session');
function generateIds(template) {
  var _template$elements;
  template.id = getUniqueId().toString();
  if ((_template$elements = template.elements) !== null && _template$elements !== void 0 && _template$elements.length) {
    template.elements.map(function (child) {
      return generateIds(child);
    });
  }
  return template;
}
var RequestIdsProvider = exports.RequestIdsProvider = function RequestIdsProvider(props) {
  var editorSessionId = (0, _react.useRef)(window.EDITOR_SESSION_ID);
  var sessionId = (0, _react.useRef)('');
  var generateId = (0, _react.useRef)('');
  var batchId = (0, _react.useRef)('');
  var requestId = (0, _react.useRef)('');
  sessionId.current = getUniqueId('session');
  var setGenerate = function setGenerate() {
    generateId.current = getUniqueId('generate');
    return generateId;
  };
  var setBatch = function setBatch() {
    batchId.current = getUniqueId('batch');
    return batchId;
  };
  var setRequest = function setRequest() {
    requestId.current = getUniqueId('request');
    return requestId;
  };
  var _useState = (0, _react.useState)(0),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    usagePercentage = _useState2[0],
    setUsagePercentage = _useState2[1];
  var updateUsagePercentage = function updateUsagePercentage(newPercentage) {
    setUsagePercentage(newPercentage);
  };
  return /*#__PURE__*/_react.default.createElement(Context.Provider, {
    value: {
      editorSessionId: editorSessionId,
      sessionId: sessionId,
      generateId: generateId,
      batchId: batchId,
      requestId: requestId,
      setGenerate: setGenerate,
      setBatch: setBatch,
      setRequest: setRequest,
      usagePercentage: usagePercentage,
      updateUsagePercentage: updateUsagePercentage
    }
  }, props.children);
};
RequestIdsProvider.propTypes = {
  children: _propTypes.default.node.isRequired
};
var _default = exports["default"] = Context;

/***/ }),

/***/ "../modules/ai/assets/js/editor/hooks/use-introduction.js":
/*!****************************************************************!*\
  !*** ../modules/ai/assets/js/editor/hooks/use-introduction.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useIntroduction;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
function useIntroduction(key) {
  var _window$elementor$con, _window$elementorAdmi, _globalConfig$introdu;
  var globalConfig = window.elementor ? (_window$elementor$con = window.elementor.config) === null || _window$elementor$con === void 0 ? void 0 : _window$elementor$con.user : (_window$elementorAdmi = window.elementorAdmin) === null || _window$elementorAdmi === void 0 || (_window$elementorAdmi = _window$elementorAdmi.config) === null || _window$elementorAdmi === void 0 ? void 0 : _window$elementorAdmi.user;
  var _useState = (0, _react.useState)(!!(globalConfig !== null && globalConfig !== void 0 && (_globalConfig$introdu = globalConfig.introduction) !== null && _globalConfig$introdu !== void 0 && _globalConfig$introdu[key])),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isViewed = _useState2[0],
    setIsViewed = _useState2[1];
  function markAsViewed() {
    if (!key) {
      return Promise.reject();
    }
    return new Promise(function (resolve, reject) {
      if (isViewed) {
        reject();
      }
      setIsViewed(true);
      elementorCommon.ajax.addRequest('introduction_viewed', {
        data: {
          introductionKey: key
        },
        error: function error() {
          setIsViewed(false);
          reject();
        },
        success: function success() {
          setIsViewed(true);
          if (globalConfig !== null && globalConfig !== void 0 && globalConfig.introduction) {
            globalConfig.introduction[key] = true;
          }
          resolve();
        }
      });
    });
  }
  return {
    isViewed: isViewed,
    markAsViewed: markAsViewed
  };
}

/***/ }),

/***/ "../modules/ai/assets/js/editor/hooks/use-prompt-enhancer.js":
/*!*******************************************************************!*\
  !*** ../modules/ai/assets/js/editor/hooks/use-prompt-enhancer.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _api = __webpack_require__(/*! ../api */ "../modules/ai/assets/js/editor/api/index.js");
var _usePrompt2 = _interopRequireDefault(__webpack_require__(/*! ./use-prompt */ "../modules/ai/assets/js/editor/hooks/use-prompt.js"));
var _config = __webpack_require__(/*! ../pages/form-layout/context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var enhancePromptMap = new Map([['media', _api.getImagePromptEnhanced], ['layout', _api.getLayoutPromptEnhanced]]);
var getResult = function getResult(prompt, type, enhanceType) {
  if (!enhancePromptMap.has(type)) {
    throw new Error("Invalid prompt type: ".concat(type));
  }
  return enhancePromptMap.get(type)(prompt, enhanceType);
};

/**
 * Hook to enhance a prompt.
 *
 * @param {string}             prompt
 * @param {'media' | 'layout'} type
 * @return {{enhancedPrompt: string | undefined, isEnhancing: boolean, enhance: (function(...[*]): Promise)}}
 */
var usePromptEnhancer = function usePromptEnhancer(prompt, type) {
  var _useConfig = (0, _config.useConfig)(),
    mode = _useConfig.mode;
  var _usePrompt = (0, _usePrompt2.default)(function () {
      return getResult(prompt, type, mode);
    }, prompt),
    enhancedData = _usePrompt.data,
    isEnhancing = _usePrompt.isLoading,
    enhance = _usePrompt.send;
  return {
    enhance: enhance,
    isEnhancing: isEnhancing,
    enhancedPrompt: enhancedData === null || enhancedData === void 0 ? void 0 : enhancedData.result
  };
};
var _default = exports["default"] = usePromptEnhancer;

/***/ }),

/***/ "../modules/ai/assets/js/editor/hooks/use-prompt.js":
/*!**********************************************************!*\
  !*** ../modules/ai/assets/js/editor/hooks/use-prompt.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _react = __webpack_require__(/*! react */ "react");
var _api = __webpack_require__(/*! ../api */ "../modules/ai/assets/js/editor/api/index.js");
var _requestsIds = __webpack_require__(/*! ../context/requests-ids */ "../modules/ai/assets/js/editor/context/requests-ids.js");
var _excluded = ["text", "response_id", "usage", "images"];
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var normalizeResponse = function normalizeResponse(_ref) {
  var text = _ref.text,
    responseId = _ref.response_id,
    usage = _ref.usage,
    images = _ref.images,
    optional = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  var creditsData = usage ? usage.quota - usage.usedQuota : 0;
  var credits = Math.max(creditsData, 0);
  var result = text || images;
  var normalized = {
    result: result,
    responseId: responseId,
    credits: credits,
    usagePercentage: usage === null || usage === void 0 ? void 0 : usage.usagePercentage
  };
  if (optional.base_template_id) {
    normalized.baseTemplateId = optional.base_template_id;
  }
  normalized.type = optional.template_type;
  return normalized;
};
var usePrompt = function usePrompt(fetchData, initialState) {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isLoading = _useState2[0],
    setIsLoading = _useState2[1];
  var _useState3 = (0, _react.useState)(''),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    error = _useState4[0],
    setError = _useState4[1];
  var _useState5 = (0, _react.useState)(initialState),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    data = _useState6[0],
    setData = _useState6[1];
  var _useRequestIds = (0, _requestsIds.useRequestIds)(),
    updateUsagePercentage = _useRequestIds.updateUsagePercentage,
    usagePercentage = _useRequestIds.usagePercentage;
  var send = (0, _react.useRef)(/*#__PURE__*/function () {
    var _ref2 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(payload) {
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            return _context.abrupt("return", payload);
          case 1:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function (_x) {
      return _ref2.apply(this, arguments);
    };
  }());
  var sendUsageData = (0, _react.useRef)(function () {});
  (0, _react.useEffect)(function () {
    var newUsageValue = data === null || data === void 0 ? void 0 : data.usagePercentage;
    if (newUsageValue && newUsageValue !== usagePercentage) {
      updateUsagePercentage(newUsageValue);
    }
  }, [data, usagePercentage, updateUsagePercentage]);
  var _useRequestIds2 = (0, _requestsIds.useRequestIds)(),
    setRequest = _useRequestIds2.setRequest,
    editorSessionId = _useRequestIds2.editorSessionId,
    sessionId = _useRequestIds2.sessionId,
    generateId = _useRequestIds2.generateId,
    batchId = _useRequestIds2.batchId;
  send.current = (0, _react.useCallback)(/*#__PURE__*/function () {
    var _ref3 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee2(payload) {
      return _regenerator.default.wrap(function (_context2) {
        while (1) switch (_context2.prev = _context2.next) {
          case 0:
            return _context2.abrupt("return", new Promise(function (resolve, reject) {
              setError('');
              setIsLoading(true);
              var requestId = setRequest();
              var requestIds = {
                editorSessionId: editorSessionId.current,
                sessionId: sessionId.current,
                generateId: generateId.current,
                batchId: batchId.current,
                requestId: requestId.current
              };
              payload = _objectSpread(_objectSpread({}, payload), {}, {
                requestIds: requestIds
              });
              fetchData(payload).then(function (result) {
                var normalizedData = normalizeResponse(result);
                setData(normalizedData);
                resolve(normalizedData);
              }).catch(function (err) {
                var finalError = (err === null || err === void 0 ? void 0 : err.responseText) || err;
                setError(finalError);
                reject(finalError);
              }).finally(function () {
                return setIsLoading(false);
              });
            }));
          case 1:
          case "end":
            return _context2.stop();
        }
      }, _callee2);
    }));
    return function (_x2) {
      return _ref3.apply(this, arguments);
    };
  }(), [batchId, editorSessionId, fetchData, generateId, sessionId, setRequest]);
  sendUsageData.current = (0, _react.useCallback)(function () {
    var usageData = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : data;
    return usageData.responseId && (0, _api.setStatusFeedback)(usageData.responseId);
  }, [data]);
  var reset = function reset() {
    setData(function (_ref4) {
      var credits = _ref4.credits;
      return {
        credits: credits,
        result: '',
        responseId: ''
      };
    });
    setError('');
    setIsLoading(false);
  };
  var setResult = function setResult(result) {
    var responseId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var updatedResult = _objectSpread({}, data);
    updatedResult.result = result;
    if (responseId) {
      updatedResult.responseId = responseId;
    }
    setData(updatedResult);
  };
  return {
    isLoading: isLoading,
    error: error,
    data: data,
    setResult: setResult,
    reset: reset,
    send: send.current,
    sendUsageData: sendUsageData.current
  };
};
var _default = exports["default"] = usePrompt;

/***/ }),

/***/ "../modules/ai/assets/js/editor/hooks/use-timeout.js":
/*!***********************************************************!*\
  !*** ../modules/ai/assets/js/editor/hooks/use-timeout.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.useTimeout = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
var useTimeout = exports.useTimeout = function useTimeout(delay) {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isTimeout = _useState2[0],
    setIsTimeout = _useState2[1];
  var timeoutIdRef = (0, _react.useRef)(null);
  var turnOffTimeout = function turnOffTimeout() {
    clearTimeout(timeoutIdRef.current);
    setIsTimeout(false);
  };
  (0, _react.useEffect)(function () {
    timeoutIdRef.current = setTimeout(function () {
      setIsTimeout(true);
    }, delay);
    return function () {
      clearTimeout(timeoutIdRef.current);
    };
  }, [delay]);
  return [isTimeout, turnOffTimeout];
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/hooks/use-user-info.js":
/*!*************************************************************!*\
  !*** ../modules/ai/assets/js/editor/hooks/use-user-info.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
var _api = __webpack_require__(/*! ../api */ "../modules/ai/assets/js/editor/api/index.js");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var useUserInfo = function useUserInfo() {
  var immediately = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isLoaded = _useState2[0],
    setIsLoaded = _useState2[1];
  var _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isLoading = _useState4[0],
    setIsLoading = _useState4[1];
  var _useState5 = (0, _react.useState)({
      is_connected: false,
      is_get_started: false,
      connect_url: '',
      usage: {
        hasAiSubscription: false,
        quota: 0,
        usedQuota: 0
      }
    }),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    userInfo = _useState6[0],
    setUserInfo = _useState6[1];
  var credits = userInfo.usage.quota - userInfo.usage.usedQuota;
  var usagePercentage = userInfo.usage.quota ? userInfo.usage.usedQuota / userInfo.usage.quota * 100 : 0;
  var fetchData = /*#__PURE__*/function () {
    var _ref = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
      var userInfoResult;
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            setIsLoading(true);
            _context.next = 1;
            return (0, _api.getUserInformation)(immediately);
          case 1:
            userInfoResult = _context.sent;
            setUserInfo(function (prevState) {
              return _objectSpread(_objectSpread({}, prevState), userInfoResult);
            });
            setIsLoaded(true);
            setIsLoading(false);
          case 2:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function fetchData() {
      return _ref.apply(this, arguments);
    };
  }();
  if (!isLoaded && !isLoading) {
    fetchData();
  }
  return {
    isLoading: isLoading,
    isLoaded: isLoaded,
    isConnected: userInfo.is_connected,
    isGetStarted: userInfo.is_get_started,
    connectUrl: userInfo.connect_url,
    builderUrl: userInfo.usage.builderUrl,
    hasSubscription: userInfo.usage.hasAiSubscription,
    credits: credits < 0 ? 0 : credits,
    usagePercentage: Math.round(usagePercentage),
    fetchData: fetchData
  };
};
useUserInfo.propTypes = {
  immediately: _propTypes.default.bool
};
var _default = exports["default"] = useUserInfo;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/arrow-left-icon.js":
/*!***************************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/arrow-left-icon.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var ArrowLeftIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M9.53033 7.46967C9.82322 7.76256 9.82322 8.23744 9.53033 8.53033L6.81066 11.25H19C19.4142 11.25 19.75 11.5858 19.75 12C19.75 12.4142 19.4142 12.75 19 12.75H6.81066L9.53033 15.4697C9.82322 15.7626 9.82322 16.2374 9.53033 16.5303C9.23744 16.8232 8.76256 16.8232 8.46967 16.5303L4.46967 12.5303C4.17678 12.2374 4.17678 11.7626 4.46967 11.4697L8.46967 7.46967C8.76256 7.17678 9.23744 7.17678 9.53033 7.46967Z"
  }));
});
var _default = exports["default"] = ArrowLeftIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/bulb-icon.js":
/*!*********************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/bulb-icon.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var BulbIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({}, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("svg", {
    width: "22",
    height: "22",
    viewBox: "0 0 22 22",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, /*#__PURE__*/_react.default.createElement("g", {
    clipPath: "url(#clip0_10743_8902)"
  }, /*#__PURE__*/_react.default.createElement("path", {
    d: "M2.75 10.0833H3.66667M11 2.75V3.66667M18.3333 10.0833H19.25M5.13333 5.13333L5.775 5.775M16.8667 5.13333L16.225 5.775",
    stroke: "#2563EB",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/_react.default.createElement("path", {
    d: "M9.16675 16.041C8.70841 15.1243 6.91205 13.2842 6.62523 12.366C6.3384 11.4477 6.34775 10.4626 6.65195 9.54997C6.95615 8.63738 7.53978 7.84362 8.32016 7.28116C9.10054 6.71869 10.0381 6.41602 11.0001 6.41602C11.962 6.41602 12.8996 6.71869 13.68 7.28116C14.4604 7.84362 15.044 8.63738 15.3482 9.54997C15.6524 10.4626 15.6618 11.4477 15.3749 12.366C15.0881 13.2842 13.2917 15.1243 12.8334 16.041C12.8334 16.041 12.7597 17.3762 12.8334 17.8743C12.8334 18.3606 12.6403 18.8269 12.2964 19.1707C11.9526 19.5145 11.4863 19.7077 11.0001 19.7077C10.5139 19.7077 10.0475 19.5145 9.70372 19.1707C9.3599 18.8269 9.16675 18.3606 9.16675 17.8743C9.2405 17.3762 9.16675 16.041 9.16675 16.041Z",
    stroke: "#2563EB",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  }), /*#__PURE__*/_react.default.createElement("path", {
    d: "M10.0833 16.5H11.9166",
    stroke: "#2563EB",
    strokeWidth: "1.5",
    strokeLinecap: "round",
    strokeLinejoin: "round"
  })), /*#__PURE__*/_react.default.createElement("defs", null, /*#__PURE__*/_react.default.createElement("clipPath", {
    id: "clip0_10743_8902"
  }, /*#__PURE__*/_react.default.createElement("rect", {
    width: "22",
    height: "22",
    fill: "white"
  })))));
});
var _default = exports["default"] = BulbIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/copy-page-icon.js":
/*!**************************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/copy-page-icon.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var CopyPageIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M16.6667 0.208496C17.0534 0.208496 17.4244 0.362142 17.6979 0.635632C17.9714 0.909123 18.125 1.28006 18.125 1.66683V11.6668C18.125 12.0536 17.9714 12.4245 17.6979 12.698C17.4244 12.9715 17.0534 13.1252 16.6667 13.1252H14.7917V16.6668C14.7917 17.0536 14.638 17.4245 14.3645 17.698C14.091 17.9715 13.7201 18.1252 13.3333 18.1252H3.33333C2.94656 18.1252 2.57563 17.9715 2.30214 17.698C2.02865 17.4245 1.875 17.0536 1.875 16.6668V6.66683C1.875 6.28005 2.02865 5.90912 2.30214 5.63563C2.57563 5.36214 2.94656 5.2085 3.33333 5.2085H5.20833V1.66683C5.20833 1.28005 5.36198 0.909122 5.63547 0.635632C5.90896 0.362142 6.27989 0.208496 6.66667 0.208496H16.6667ZM6.66667 1.4585C6.61141 1.4585 6.55842 1.48045 6.51935 1.51952C6.48028 1.55859 6.45833 1.61158 6.45833 1.66683V3.54183H8.54167V1.4585H6.66667ZM3.125 9.79183V16.6668C3.125 16.7221 3.14695 16.7751 3.18602 16.8141C3.22509 16.8532 3.27808 16.8752 3.33333 16.8752H13.3333C13.3886 16.8752 13.4416 16.8532 13.4806 16.8141C13.5197 16.7751 13.5417 16.7221 13.5417 16.6668V13.1252H6.66667C6.27989 13.1252 5.90896 12.9715 5.63547 12.698C5.36198 12.4245 5.20833 12.0536 5.20833 11.6668V9.79183H3.125ZM5.20833 8.54183H3.125V6.66683C3.125 6.61158 3.14695 6.55859 3.18602 6.51952C3.22509 6.48045 3.27808 6.4585 3.33333 6.4585H5.20833V8.54183ZM6.45833 11.6668C6.45833 11.7221 6.48028 11.7751 6.51935 11.8141C6.55842 11.8532 6.61141 11.8752 6.66667 11.8752H16.6667C16.7219 11.8752 16.7749 11.8532 16.814 11.8141C16.853 11.7751 16.875 11.7221 16.875 11.6668V4.79183H6.45833V11.6668ZM9.79167 1.4585V3.54183H16.875V1.66683C16.875 1.61157 16.853 1.55858 16.814 1.51952C16.7749 1.48045 16.7219 1.4585 16.6667 1.4585H9.79167Z"
  }));
});
var _default = exports["default"] = CopyPageIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/edit-icon.js":
/*!*********************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/edit-icon.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var EditIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M13.9697 4.96967C14.6408 4.29858 15.5509 3.92157 16.5 3.92157C17.4491 3.92157 18.3592 4.29858 19.0303 4.96967C19.7014 5.64075 20.0784 6.55094 20.0784 7.5C20.0784 8.44905 19.7014 9.35924 19.0303 10.0303L8.53033 20.5303C8.38968 20.671 8.19891 20.75 8 20.75H4C3.58579 20.75 3.25 20.4142 3.25 20V16C3.25 15.8011 3.32902 15.6103 3.46967 15.4697L13.9697 4.96967ZM16.5 5.42157C15.9488 5.42157 15.4201 5.64055 15.0303 6.03033L4.75 16.3107V19.25H7.68934L17.9697 8.96967C18.3595 8.57989 18.5784 8.05123 18.5784 7.5C18.5784 6.94876 18.3595 6.42011 17.9697 6.03033C17.5799 5.64055 17.0512 5.42157 16.5 5.42157Z"
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M12.9697 5.96967C13.2626 5.67677 13.7374 5.67677 14.0303 5.96967L18.0303 9.96967C18.3232 10.2626 18.3232 10.7374 18.0303 11.0303C17.7374 11.3232 17.2626 11.3232 16.9697 11.0303L12.9697 7.03033C12.6768 6.73743 12.6768 6.26256 12.9697 5.96967Z"
  }));
});
var _default = exports["default"] = EditIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/expand-diagonal-icon.js":
/*!********************************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/expand-diagonal-icon.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var ExpandDiagonalIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M4 3.25H8C8.41421 3.25 8.75 3.58579 8.75 4C8.75 4.41421 8.41421 4.75 8 4.75H5.81066L10.5303 9.46967C10.8232 9.76256 10.8232 10.2374 10.5303 10.5303C10.2374 10.8232 9.76256 10.8232 9.46967 10.5303L4.75 5.81066V8C4.75 8.41421 4.41421 8.75 4 8.75C3.58579 8.75 3.25 8.41421 3.25 8V4C3.25 3.58579 3.58579 3.25 4 3.25ZM13.4697 13.4697C13.7626 13.1768 14.2374 13.1768 14.5303 13.4697L19.25 18.1893V16C19.25 15.5858 19.5858 15.25 20 15.25C20.4142 15.25 20.75 15.5858 20.75 16V20C20.75 20.4142 20.4142 20.75 20 20.75H16C15.5858 20.75 15.25 20.4142 15.25 20C15.25 19.5858 15.5858 19.25 16 19.25H18.1893L13.4697 14.5303C13.1768 14.2374 13.1768 13.7626 13.4697 13.4697Z"
  }));
});
var _default = exports["default"] = ExpandDiagonalIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/lock-icon.js":
/*!*********************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/lock-icon.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var LockIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M7.8125 11.9996C7.29473 11.9996 6.875 12.4473 6.875 12.9996V18.9996C6.875 19.5519 7.29473 19.9996 7.8125 19.9996H17.1875C17.7053 19.9996 18.125 19.5519 18.125 18.9996V12.9996C18.125 12.4473 17.7053 11.9996 17.1875 11.9996H7.8125ZM5 12.9996C5 11.3428 6.2592 9.99963 7.8125 9.99963H17.1875C18.7408 9.99963 20 11.3428 20 12.9996V18.9996C20 20.6565 18.7408 21.9996 17.1875 21.9996H7.8125C6.2592 21.9996 5 20.6565 5 18.9996V12.9996Z"
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M12.5 3.90527C11.7044 3.90527 10.9413 4.22134 10.3787 4.78395C9.81607 5.34656 9.5 6.10962 9.5 6.90527V10.9053C9.5 11.4576 9.05228 11.9053 8.5 11.9053C7.94772 11.9053 7.5 11.4576 7.5 10.9053V6.90527C7.5 5.57919 8.02678 4.30742 8.96447 3.36974C9.90215 2.43206 11.1739 1.90527 12.5 1.90527C13.8261 1.90527 15.0979 2.43206 16.0355 3.36974C16.9732 4.30742 17.5 5.57919 17.5 6.90527V10.9053C17.5 11.4576 17.0523 11.9053 16.5 11.9053C15.9477 11.9053 15.5 11.4576 15.5 10.9053V6.90527C15.5 6.10962 15.1839 5.34656 14.6213 4.78395C14.0587 4.22134 13.2956 3.90527 12.5 3.90527Z"
  }), /*#__PURE__*/_react.default.createElement("path", {
    d: "M6 12H19V20H6V12Z"
  }));
});
var _default = exports["default"] = LockIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/minimize-diagonal-icon.js":
/*!**********************************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/minimize-diagonal-icon.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var MinimizeDiagonalIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M3.46967 3.46967C3.76256 3.17678 4.23744 3.17678 4.53033 3.46967L9.25 8.18934V6C9.25 5.58579 9.58579 5.25 10 5.25C10.4142 5.25 10.75 5.58579 10.75 6V10C10.75 10.4142 10.4142 10.75 10 10.75H6C5.58579 10.75 5.25 10.4142 5.25 10C5.25 9.58579 5.58579 9.25 6 9.25H8.18934L3.46967 4.53033C3.17678 4.23744 3.17678 3.76256 3.46967 3.46967ZM14 13.25H18C18.4142 13.25 18.75 13.5858 18.75 14C18.75 14.4142 18.4142 14.75 18 14.75H15.8107L20.5303 19.4697C20.8232 19.7626 20.8232 20.2374 20.5303 20.5303C20.2374 20.8232 19.7626 20.8232 19.4697 20.5303L14.75 15.8107V18C14.75 18.4142 14.4142 18.75 14 18.75C13.5858 18.75 13.25 18.4142 13.25 18V14C13.25 13.5858 13.5858 13.25 14 13.25Z"
  }));
});
var _default = exports["default"] = MinimizeDiagonalIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/plus-circle-icon.js":
/*!****************************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/plus-circle-icon.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var PlusCircleIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    d: "M12 2.69231C6.8595 2.69231 2.69231 6.8595 2.69231 12C2.69231 17.1405 6.8595 21.3077 12 21.3077C17.1405 21.3077 21.3077 17.1405 21.3077 12C21.3077 6.8595 17.1405 2.69231 12 2.69231ZM1 12C1 5.92487 5.92487 1 12 1C18.0751 1 23 5.92487 23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12ZM12 7.76923C12.4673 7.76923 12.8462 8.14807 12.8462 8.61538V11.1538H15.3846C15.8519 11.1538 16.2308 11.5327 16.2308 12C16.2308 12.4673 15.8519 12.8462 15.3846 12.8462H12.8462V15.3846C12.8462 15.8519 12.4673 16.2308 12 16.2308C11.5327 16.2308 11.1538 15.8519 11.1538 15.3846V12.8462H8.61538C8.14807 12.8462 7.76923 12.4673 7.76923 12C7.76923 11.5327 8.14807 11.1538 8.61538 11.1538H11.1538V8.61538C11.1538 8.14807 11.5327 7.76923 12 7.76923Z"
  }));
});
var _default = exports["default"] = PlusCircleIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/refresh-icon.js":
/*!************************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/refresh-icon.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var RefreshIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M7.55012 4.45178C9.23098 3.48072 11.1845 3.08925 13.1097 3.33767C15.035 3.58609 16.8251 4.46061 18.2045 5.82653C19.5838 7.19245 20.4757 8.97399 20.743 10.8967C20.8 11.307 20.5136 11.6858 20.1033 11.7428C19.6931 11.7998 19.3142 11.5135 19.2572 11.1032C19.0353 9.50635 18.2945 8.02677 17.149 6.89236C16.0035 5.75795 14.5167 5.03165 12.9178 4.82534C11.3189 4.61902 9.69644 4.94414 8.30047 5.75061C7.24361 6.36117 6.36093 7.22198 5.72541 8.24995H8.00009C8.41431 8.24995 8.75009 8.58574 8.75009 8.99995C8.75009 9.41417 8.41431 9.74995 8.00009 9.74995H4.51686C4.5055 9.75021 4.49412 9.75021 4.48272 9.74995H4.00009C3.58588 9.74995 3.25009 9.41417 3.25009 8.99995V4.99995C3.25009 4.58574 3.58588 4.24995 4.00009 4.24995C4.41431 4.24995 4.75009 4.58574 4.75009 4.99995V7.00691C5.48358 5.96916 6.43655 5.0951 7.55012 4.45178Z"
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M3.89686 12.2571C4.30713 12.2001 4.68594 12.4864 4.74295 12.8967C4.96487 14.4936 5.70565 15.9731 6.85119 17.1075C7.99673 18.242 9.48347 18.9683 11.0824 19.1746C12.6813 19.3809 14.3037 19.0558 15.6997 18.2493C16.7566 17.6387 17.6393 16.7779 18.2748 15.75H16.0001C15.5859 15.75 15.2501 15.4142 15.2501 15C15.2501 14.5857 15.5859 14.25 16.0001 14.25H19.4833C19.4947 14.2497 19.5061 14.2497 19.5175 14.25H20.0001C20.4143 14.25 20.7501 14.5857 20.7501 15V19C20.7501 19.4142 20.4143 19.75 20.0001 19.75C19.5859 19.75 19.2501 19.4142 19.2501 19V16.993C18.5166 18.0307 17.5636 18.9048 16.4501 19.5481C14.7692 20.5192 12.8157 20.9107 10.8904 20.6622C8.9652 20.4138 7.17504 19.5393 5.79572 18.1734C4.4164 16.8074 3.52443 15.0259 3.25723 13.1032C3.20022 12.6929 3.48658 12.3141 3.89686 12.2571Z"
  }));
});
var _default = exports["default"] = RefreshIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/wand-icon.js":
/*!*********************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/wand-icon.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var WandIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M9 2.25C9.41421 2.25 9.75 2.58579 9.75 3C9.75 3.33152 9.8817 3.64946 10.1161 3.88388C10.3505 4.1183 10.6685 4.25 11 4.25C11.4142 4.25 11.75 4.58579 11.75 5C11.75 5.41421 11.4142 5.75 11 5.75C10.6685 5.75 10.3505 5.8817 10.1161 6.11612C9.8817 6.35054 9.75 6.66848 9.75 7C9.75 7.41421 9.41421 7.75 9 7.75C8.58579 7.75 8.25 7.41421 8.25 7C8.25 6.66848 8.1183 6.35054 7.88388 6.11612C7.64946 5.8817 7.33152 5.75 7 5.75C6.58579 5.75 6.25 5.41421 6.25 5C6.25 4.58579 6.58579 4.25 7 4.25C7.33152 4.25 7.64946 4.1183 7.88388 3.88388C8.1183 3.64946 8.25 3.33152 8.25 3C8.25 2.58579 8.58579 2.25 9 2.25ZM9 4.88746C8.98182 4.90673 8.96333 4.92576 8.94454 4.94454C8.92576 4.96333 8.90673 4.98182 8.88746 5C8.90673 5.01818 8.92576 5.03667 8.94454 5.05546C8.96333 5.07424 8.98182 5.09327 9 5.11254C9.01818 5.09327 9.03667 5.07424 9.05546 5.05546C9.07424 5.03667 9.09327 5.01818 9.11254 5C9.09327 4.98182 9.07424 4.96333 9.05546 4.94454C9.03667 4.92576 9.01818 4.90673 9 4.88746Z"
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M18.5303 2.46967C18.2374 2.17678 17.7626 2.17678 17.4697 2.46967L2.46967 17.4697C2.17678 17.7626 2.17678 18.2374 2.46967 18.5303L5.46967 21.5303C5.76256 21.8232 6.23744 21.8232 6.53033 21.5303L21.5303 6.53033C21.8232 6.23744 21.8232 5.76256 21.5303 5.46967L18.5303 2.46967ZM18 7.93934L19.9393 6L18 4.06066L16.0607 6L18 7.93934ZM15 7.06066L16.9393 9L6 19.9393L4.06066 18L15 7.06066Z"
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M19.75 13C19.75 12.5858 19.4142 12.25 19 12.25C18.5858 12.25 18.25 12.5858 18.25 13C18.25 13.3315 18.1183 13.6495 17.8839 13.8839C17.6495 14.1183 17.3315 14.25 17 14.25C16.5858 14.25 16.25 14.5858 16.25 15C16.25 15.4142 16.5858 15.75 17 15.75C17.3315 15.75 17.6495 15.8817 17.8839 16.1161C18.1183 16.3505 18.25 16.6685 18.25 17C18.25 17.4142 18.5858 17.75 19 17.75C19.4142 17.75 19.75 17.4142 19.75 17C19.75 16.6685 19.8817 16.3505 20.1161 16.1161C20.3505 15.8817 20.6685 15.75 21 15.75C21.4142 15.75 21.75 15.4142 21.75 15C21.75 14.5858 21.4142 14.25 21 14.25C20.6685 14.25 20.3505 14.1183 20.1161 13.8839C19.8817 13.6495 19.75 13.3315 19.75 13ZM18.9445 14.9445C18.9633 14.9258 18.9818 14.9067 19 14.8875C19.0182 14.9067 19.0367 14.9258 19.0555 14.9445C19.0742 14.9633 19.0933 14.9818 19.1125 15C19.0933 15.0182 19.0742 15.0367 19.0555 15.0555C19.0367 15.0742 19.0182 15.0933 19 15.1125C18.9818 15.0933 18.9633 15.0742 18.9445 15.0555C18.9258 15.0367 18.9067 15.0182 18.8875 15C18.9067 14.9818 18.9258 14.9633 18.9445 14.9445Z"
  }));
});
var _default = exports["default"] = WandIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/website-icon.js":
/*!************************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/website-icon.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var WebsiteIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M4.16707 3.95837C4.11182 3.95837 4.05883 3.98032 4.01976 4.01939C3.98069 4.05846 3.95874 4.11145 3.95874 4.16671V6.04171H6.04207V3.95837H4.16707ZM4.16707 2.70837C3.7803 2.70837 3.40937 2.86202 3.13588 3.13551C2.86239 3.409 2.70874 3.77993 2.70874 4.16671V15.8334C2.70874 16.2201 2.86239 16.5911 3.13588 16.8646C3.40937 17.1381 3.7803 17.2917 4.16707 17.2917H15.8337C16.2205 17.2917 16.5914 17.1381 16.8649 16.8646C17.1384 16.5911 17.2921 16.2201 17.2921 15.8334V4.16671C17.2921 3.77993 17.1384 3.409 16.8649 3.13551C16.5914 2.86202 16.2205 2.70837 15.8337 2.70837H4.16707ZM7.29207 3.95837V6.04171H16.0421V4.16671C16.0421 4.11145 16.0201 4.05846 15.9811 4.01939C15.942 3.98032 15.889 3.95837 15.8337 3.95837H7.29207ZM16.0421 7.29171H3.95874V15.8334C3.95874 15.8886 3.98069 15.9416 4.01976 15.9807C4.05883 16.0198 4.11182 16.0417 4.16707 16.0417H15.8337C15.889 16.0417 15.942 16.0198 15.9811 15.9807C16.0201 15.9416 16.0421 15.8886 16.0421 15.8334V7.29171Z"
  }));
});
var _default = exports["default"] = WebsiteIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/icons/x-circle-icon.js":
/*!*************************************************************!*\
  !*** ../modules/ai/assets/js/editor/icons/x-circle-icon.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var XCircleIcon = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, (0, _extends2.default)({
    viewBox: "0 0 24 24"
  }, props, {
    ref: ref
  }), /*#__PURE__*/_react.default.createElement("path", {
    d: "M12 2.69231C6.8595 2.69231 2.69231 6.8595 2.69231 12C2.69231 17.1405 6.8595 21.3077 12 21.3077C17.1405 21.3077 21.3077 17.1405 21.3077 12C21.3077 6.8595 17.1405 2.69231 12 2.69231ZM1 12C1 5.92487 5.92487 1 12 1C18.0751 1 23 5.92487 23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12ZM9.14527 9.14527C9.47571 8.81483 10.0115 8.81483 10.3419 9.14527L12 10.8034L13.6581 9.14527C13.9885 8.81483 14.5243 8.81483 14.8547 9.14527C15.1852 9.47571 15.1852 10.0115 14.8547 10.3419L13.1966 12L14.8547 13.6581C15.1852 13.9885 15.1852 14.5243 14.8547 14.8547C14.5243 15.1852 13.9885 15.1852 13.6581 14.8547L12 13.1966L10.3419 14.8547C10.0115 15.1852 9.47571 15.1852 9.14527 14.8547C8.81483 14.5243 8.81483 13.9885 9.14527 13.6581L10.8034 12L9.14527 10.3419C8.81483 10.0115 8.81483 9.47571 9.14527 9.14527Z"
  }));
});
var _default = exports["default"] = XCircleIcon;

/***/ }),

/***/ "../modules/ai/assets/js/editor/integration/library/apply-template-for-ai-behavior.js":
/*!********************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/integration/library/apply-template-for-ai-behavior.js ***!
  \********************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _require = __webpack_require__(/*! ../../utils/editor-integration */ "../modules/ai/assets/js/editor/utils/editor-integration.js"),
  renderLayoutApp = _require.renderLayoutApp,
  importToEditor = _require.importToEditor;
var _require2 = __webpack_require__(/*! ../../pages/form-layout/context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js"),
  MODE_VARIATION = _require2.MODE_VARIATION;
var _require3 = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n"),
  __ = _require3.__;
var _require4 = __webpack_require__(/*! ../../pages/form-layout/components/attachments */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js"),
  ATTACHMENT_TYPE_JSON = _require4.ATTACHMENT_TYPE_JSON,
  ELEMENTOR_LIBRARY_SOURCE = _require4.ELEMENTOR_LIBRARY_SOURCE;
var ApplyTemplateForAiBehavior;
ApplyTemplateForAiBehavior = Marionette.Behavior.extend({
  ui: {
    applyButton: '.elementor-template-library-template-apply-ai',
    generateVariation: '.elementor-template-library-template-generate-variation'
  },
  events: {
    'click @ui.applyButton': 'onApplyButtonClick',
    'click @ui.generateVariation': 'onGenerateVariationClick'
  },
  onGenerateVariationClick: function onGenerateVariationClick() {
    var _libraryComponent$man;
    var args = {
      model: this.view.model
    };
    var libraryComponent = $e.components.get('library');
    var at = (_libraryComponent$man = libraryComponent.manager.modalConfig) === null || _libraryComponent$man === void 0 || (_libraryComponent$man = _libraryComponent$man.importOptions) === null || _libraryComponent$man === void 0 ? void 0 : _libraryComponent$man.at;
    libraryComponent.downloadTemplate(args, function (data) {
      var model = args.model;
      var attachment = {
        type: ATTACHMENT_TYPE_JSON,
        previewHTML: "<img src=\"".concat(model.get('thumbnail'), "\" />"),
        content: data.content[0],
        label: "".concat(model.get('template_id'), " - ").concat(model.get('title')),
        source: ELEMENTOR_LIBRARY_SOURCE
      };
      renderLayoutApp({
        parentContainer: elementor.getPreviewContainer(),
        mode: MODE_VARIATION,
        at: at,
        attachments: [attachment],
        onInsert: function onInsert(template) {
          importToEditor({
            parentContainer: elementor.getPreviewContainer(),
            at: at,
            template: template,
            historyTitle: __('AI Variation from library', 'elementor')
          });
        }
      });
      $e.run('library/close');
    });
  },
  onApplyButtonClick: function onApplyButtonClick() {
    var args = {
      model: this.view.model
    };
    this.ui.applyButton.addClass('elementor-disabled');
    var activeSource = args.model.get('source');

    /**
     * Filter template source.
     *
     * @param bool   isRemote     - If `true` the source is a remote source.
     * @param string activeSource - The current template source.
     */
    var isRemote = elementor.hooks.applyFilters('templates/source/is-remote', 'remote' === activeSource, activeSource);
    if (isRemote && !elementor.config.library_connect.is_connected) {
      $e.route('library/connect', args);
      return;
    }
    $e.run('library/generate-ai-variation', args);
  }
});
module.exports = ApplyTemplateForAiBehavior;

/***/ }),

/***/ "../modules/ai/assets/js/editor/layout-app-wrapper.js":
/*!************************************************************!*\
  !*** ../modules/ai/assets/js/editor/layout-app-wrapper.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var LayoutAppWrapper = function LayoutAppWrapper(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.DirectionProvider, {
    rtl: props.isRTL
  }, /*#__PURE__*/_react.default.createElement(_ui.ThemeProvider, {
    colorScheme: props.colorScheme
  }, props.children));
};
LayoutAppWrapper.propTypes = {
  children: _propTypes.default.node,
  isRTL: _propTypes.default.bool,
  colorScheme: _propTypes.default.oneOf(['auto', 'light', 'dark'])
};
var _default = exports["default"] = LayoutAppWrapper;

/***/ }),

/***/ "../modules/ai/assets/js/editor/layout-app.js":
/*!****************************************************!*\
  !*** ../modules/ai/assets/js/editor/layout-app.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _layoutContent = _interopRequireDefault(__webpack_require__(/*! ./layout-content */ "../modules/ai/assets/js/editor/layout-content.js"));
var _attachment = __webpack_require__(/*! ./types/attachment */ "../modules/ai/assets/js/editor/types/attachment.js");
var _config = __webpack_require__(/*! ./pages/form-layout/context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var _remoteConfig = __webpack_require__(/*! ./pages/form-layout/context/remote-config */ "../modules/ai/assets/js/editor/pages/form-layout/context/remote-config.js");
var _requestsIds = __webpack_require__(/*! ./context/requests-ids */ "../modules/ai/assets/js/editor/context/requests-ids.js");
var LayoutApp = function LayoutApp(props) {
  return /*#__PURE__*/_react.default.createElement(_remoteConfig.RemoteConfigProvider, {
    onError: props.onClose
  }, /*#__PURE__*/_react.default.createElement(_requestsIds.RequestIdsProvider, null, /*#__PURE__*/_react.default.createElement(_config.ConfigProvider, {
    mode: props.mode,
    attachmentsTypes: props.attachmentsTypes,
    onClose: props.onClose,
    onConnect: props.onConnect,
    onData: props.onData,
    onInsert: props.onInsert,
    onSelect: props.onSelect,
    onGenerate: props.onGenerate,
    currentContext: props.currentContext,
    hasPro: props.hasPro
  }, /*#__PURE__*/_react.default.createElement(_layoutContent.default, {
    attachments: props.attachments
  }))));
};
LayoutApp.propTypes = {
  mode: _propTypes.default.oneOf(_config.LAYOUT_APP_MODES).isRequired,
  attachmentsTypes: _attachment.AttachmentsTypesPropType,
  attachments: _propTypes.default.arrayOf(_attachment.AttachmentPropType),
  onClose: _propTypes.default.func.isRequired,
  onConnect: _propTypes.default.func.isRequired,
  onData: _propTypes.default.func.isRequired,
  onInsert: _propTypes.default.func.isRequired,
  onSelect: _propTypes.default.func.isRequired,
  onGenerate: _propTypes.default.func.isRequired,
  currentContext: _propTypes.default.object,
  hasPro: _propTypes.default.bool
};
var _default = exports["default"] = LayoutApp;

/***/ }),

/***/ "../modules/ai/assets/js/editor/layout-content.js":
/*!********************************************************!*\
  !*** ../modules/ai/assets/js/editor/layout-content.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _connect = _interopRequireDefault(__webpack_require__(/*! ./pages/connect */ "../modules/ai/assets/js/editor/pages/connect/index.js"));
var _formLayout = _interopRequireDefault(__webpack_require__(/*! ./pages/form-layout */ "../modules/ai/assets/js/editor/pages/form-layout/index.js"));
var _getStarted = _interopRequireDefault(__webpack_require__(/*! ./pages/get-started */ "../modules/ai/assets/js/editor/pages/get-started/index.js"));
var _loader = _interopRequireDefault(__webpack_require__(/*! ./components/loader */ "../modules/ai/assets/js/editor/components/loader.js"));
var _upgradeChip = _interopRequireDefault(__webpack_require__(/*! ./components/upgrade-chip */ "../modules/ai/assets/js/editor/components/upgrade-chip.js"));
var _useUserInfo2 = _interopRequireDefault(__webpack_require__(/*! ./hooks/use-user-info */ "../modules/ai/assets/js/editor/hooks/use-user-info.js"));
var _wizardDialog = _interopRequireDefault(__webpack_require__(/*! ./components/wizard-dialog */ "../modules/ai/assets/js/editor/components/wizard-dialog.js"));
var _layoutDialog = _interopRequireDefault(__webpack_require__(/*! ./pages/form-layout/components/layout-dialog */ "../modules/ai/assets/js/editor/pages/form-layout/components/layout-dialog.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _attachment = __webpack_require__(/*! ./types/attachment */ "../modules/ai/assets/js/editor/types/attachment.js");
var _config = __webpack_require__(/*! ./pages/form-layout/context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var _requestsIds = __webpack_require__(/*! ./context/requests-ids */ "../modules/ai/assets/js/editor/context/requests-ids.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var LayoutContent = function LayoutContent(props) {
  var _useUserInfo = (0, _useUserInfo2.default)(),
    isLoading = _useUserInfo.isLoading,
    isConnected = _useUserInfo.isConnected,
    isGetStarted = _useUserInfo.isGetStarted,
    connectUrl = _useUserInfo.connectUrl,
    fetchData = _useUserInfo.fetchData,
    hasSubscription = _useUserInfo.hasSubscription,
    initialUsagePercentage = _useUserInfo.usagePercentage;
  var _useConfig = (0, _config.useConfig)(),
    onClose = _useConfig.onClose,
    onConnect = _useConfig.onConnect;
  var _useRequestIds = (0, _requestsIds.useRequestIds)(),
    updateUsagePercentage = _useRequestIds.updateUsagePercentage,
    usagePercentage = _useRequestIds.usagePercentage;
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isInitUsageDone = _useState2[0],
    setIsInitUsageDone = _useState2[1];
  (0, _react.useEffect)(function () {
    if (!isInitUsageDone && !isLoading && (initialUsagePercentage || 0 === initialUsagePercentage)) {
      updateUsagePercentage(initialUsagePercentage);
      setIsInitUsageDone(true);
    }
  }, [isLoading, initialUsagePercentage, isInitUsageDone, updateUsagePercentage]);
  if (isLoading || !isInitUsageDone) {
    return /*#__PURE__*/_react.default.createElement(_layoutDialog.default, {
      onClose: onClose
    }, /*#__PURE__*/_react.default.createElement(_layoutDialog.default.Header, {
      onClose: onClose
    }), /*#__PURE__*/_react.default.createElement(_layoutDialog.default.Content, {
      dividers: true
    }, /*#__PURE__*/_react.default.createElement(_loader.default, {
      BoxProps: {
        sx: {
          px: 3
        }
      }
    })));
  }
  if (!isConnected) {
    return /*#__PURE__*/_react.default.createElement(_wizardDialog.default, {
      onClose: onClose
    }, /*#__PURE__*/_react.default.createElement(_layoutDialog.default, {
      onClose: onClose
    }), /*#__PURE__*/_react.default.createElement(_wizardDialog.default.Content, {
      dividers: true
    }, /*#__PURE__*/_react.default.createElement(_connect.default, {
      connectUrl: connectUrl,
      onSuccess: function onSuccess(data) {
        onConnect(data);
        fetchData();
      }
    })));
  }
  if (!isGetStarted) {
    return /*#__PURE__*/_react.default.createElement(_wizardDialog.default, {
      onClose: onClose
    }, /*#__PURE__*/_react.default.createElement(_layoutDialog.default, {
      onClose: onClose
    }), /*#__PURE__*/_react.default.createElement(_wizardDialog.default.Content, {
      dividers: true
    }, /*#__PURE__*/_react.default.createElement(_getStarted.default, {
      onSuccess: fetchData
    })));
  }
  var showUpgradeChip = !hasSubscription || 80 <= usagePercentage;
  return /*#__PURE__*/_react.default.createElement(_formLayout.default, {
    attachments: props.attachments,
    DialogHeaderProps: {
      children: showUpgradeChip && /*#__PURE__*/_react.default.createElement(_upgradeChip.default, {
        hasSubscription: hasSubscription,
        usagePercentage: usagePercentage
      })
    }
  });
};
LayoutContent.propTypes = {
  attachments: _propTypes.default.arrayOf(_attachment.AttachmentPropType)
};
var _default = exports["default"] = LayoutContent;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/connect/index.js":
/*!*************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/connect/index.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _icons = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var Connect = function Connect(_ref) {
  var connectUrl = _ref.connectUrl,
    onSuccess = _ref.onSuccess;
  var approveButtonRef = (0, _react.useRef)();
  (0, _react.useEffect)(function () {
    // On local dev (as a standalone app), the connect lib is not loaded.
    if (!jQuery.fn.elementorConnect) {
      return;
    }
    jQuery(approveButtonRef.current).elementorConnect({
      success: function success(_, data) {
        return onSuccess(data);
      },
      error: function error() {
        throw new Error('Elementor AI: Failed to connect.');
      }
    });
  }, []);
  return /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    alignItems: "center",
    gap: 2
  }, /*#__PURE__*/_react.default.createElement(_icons.AIIcon, {
    sx: {
      color: 'text.primary',
      fontSize: '60px',
      mb: 1
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "h4",
    sx: {
      color: 'text.primary'
    }
  }, (0, _i18n.__)('Step into the future with Elementor AI', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2"
  }, (0, _i18n.__)('Create smarter with AI text and code generators built right into the editor.', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "caption",
    sx: {
      maxWidth: 520,
      textAlign: 'center'
    }
  }, (0, _i18n.__)('By clicking "Connect", I approve the ', 'elementor'), /*#__PURE__*/_react.default.createElement(_ui.Link, {
    href: "https://go.elementor.com/ai-terms/",
    target: "_blank",
    color: "info.main"
  }, (0, _i18n.__)('Terms of Service', 'elementor')), ' & ', /*#__PURE__*/_react.default.createElement(_ui.Link, {
    href: "https://go.elementor.com/ai-privacy-policy/",
    target: "_blank",
    color: "info.main"
  }, (0, _i18n.__)('Privacy Policy', 'elementor')), (0, _i18n.__)(' of the Elementor AI service.', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Button, {
    ref: approveButtonRef,
    href: connectUrl,
    variant: "contained",
    sx: {
      mt: 1,
      '&:hover': {
        color: 'primary.contrastText'
      }
    }
  }, (0, _i18n.__)('Connect', 'elementor')));
};
Connect.propTypes = {
  connectUrl: _propTypes.default.string.isRequired,
  onSuccess: _propTypes.default.func.isRequired
};
var _default = exports["default"] = Connect;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js":
/*!**********************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.USER_VARIATION_SOURCE = exports.USER_URL_SOURCE = exports.MENU_TYPE_LIBRARY = exports.ELEMENTOR_LIBRARY_SOURCE = exports.ATTACHMENT_TYPE_URL = exports.ATTACHMENT_TYPE_JSON = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _menu = __webpack_require__(/*! ./attachments/menu */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/menu.js");
var _thumbnailJson = _interopRequireDefault(__webpack_require__(/*! ./attachments/thumbnail-json */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail-json.js"));
var _thumbnailUrl = _interopRequireDefault(__webpack_require__(/*! ./attachments/thumbnail-url */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail-url.js"));
var _websiteIcon = _interopRequireDefault(__webpack_require__(/*! ../../../icons/website-icon */ "../modules/ai/assets/js/editor/icons/website-icon.js"));
var _copyPageIcon = _interopRequireDefault(__webpack_require__(/*! ../../../icons/copy-page-icon */ "../modules/ai/assets/js/editor/icons/copy-page-icon.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _attachment = __webpack_require__(/*! ../../../types/attachment */ "../modules/ai/assets/js/editor/types/attachment.js");
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var ATTACHMENT_TYPE_JSON = exports.ATTACHMENT_TYPE_JSON = 'json';
var ATTACHMENT_TYPE_URL = exports.ATTACHMENT_TYPE_URL = 'url';
var MENU_TYPE_LIBRARY = exports.MENU_TYPE_LIBRARY = 'library';
var USER_VARIATION_SOURCE = exports.USER_VARIATION_SOURCE = 'user-variation';
var ELEMENTOR_LIBRARY_SOURCE = exports.ELEMENTOR_LIBRARY_SOURCE = 'elementor-library';
var USER_URL_SOURCE = exports.USER_URL_SOURCE = 'user-url';
var Attachments = function Attachments(props) {
  if (!props.attachments.length) {
    return /*#__PURE__*/_react.default.createElement(_menu.Menu, {
      disabled: props.disabled,
      onAttach: props.onAttach,
      items: [{
        title: (0, _i18n.__)('Reference a website', 'elementor'),
        icon: _websiteIcon.default,
        type: ATTACHMENT_TYPE_URL
      }, {
        title: (0, _i18n.__)('Create variations from Template Library', 'elementor'),
        icon: _copyPageIcon.default,
        type: MENU_TYPE_LIBRARY
      }]
    });
  }
  return /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    spacing: 1
  }, props.attachments.map(function (attachment, index) {
    switch (attachment.type) {
      case ATTACHMENT_TYPE_JSON:
        return /*#__PURE__*/_react.default.createElement(_thumbnailJson.default, (0, _extends2.default)({
          key: index
        }, props));
      case ATTACHMENT_TYPE_URL:
        return /*#__PURE__*/_react.default.createElement(_thumbnailUrl.default, (0, _extends2.default)({
          key: index
        }, props));
      default:
        return null;
    }
  }));
};
Attachments.propTypes = {
  attachments: _propTypes.default.arrayOf(_attachment.AttachmentPropType).isRequired,
  onAttach: _propTypes.default.func.isRequired,
  onDetach: _propTypes.default.func,
  disabled: _propTypes.default.bool
};
var _default = exports["default"] = Attachments;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/attach-dialog.js":
/*!************************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments/attach-dialog.js ***!
  \************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.AttachDialog = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _urlDialog = __webpack_require__(/*! ./url-dialog */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/url-dialog.js");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _libraryDialog = __webpack_require__(/*! ./library-dialog */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/library-dialog.js");
var _attachments = __webpack_require__(/*! ../attachments */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js");
var AttachDialog = exports.AttachDialog = function AttachDialog(props) {
  var type = props.type;
  var url = props.url;
  switch (type) {
    case _attachments.ATTACHMENT_TYPE_URL:
      return /*#__PURE__*/_react.default.createElement(_urlDialog.UrlDialog, {
        url: url,
        onAttach: props.onAttach,
        onClose: props.onClose
      });
    case _attachments.MENU_TYPE_LIBRARY:
      return /*#__PURE__*/_react.default.createElement(_libraryDialog.LibraryDialog, {
        onAttach: props.onAttach,
        onClose: props.onClose
      });
  }
  return null;
};
AttachDialog.propTypes = {
  type: _propTypes.default.string,
  onAttach: _propTypes.default.func,
  onClose: _propTypes.default.func,
  url: _propTypes.default.string
};
var _default = exports["default"] = AttachDialog;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/library-dialog.js":
/*!*************************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments/library-dialog.js ***!
  \*************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.LibraryDialog = void 0;
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _react = __webpack_require__(/*! react */ "react");
var _attachments = __webpack_require__(/*! ../attachments */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js");
var LibraryDialog = exports.LibraryDialog = function LibraryDialog(props) {
  var isApplyingTemplate = (0, _react.useRef)(false);
  (0, _react.useEffect)(function () {
    var onLibraryHide = function onLibraryHide() {
      if (isApplyingTemplate.current) {
        return;
      }
      props.onClose();
    };
    $e.components.get('library').layout.getModal().on('hide', onLibraryHide);
    return function () {
      $e.components.get('library').layout.getModal().off('hide', onLibraryHide);
    };
  }, [props]);
  (0, _react.useEffect)(function () {
    var onMessage = function onMessage(event) {
      var _event$data = event.data,
        type = _event$data.type,
        json = _event$data.json,
        html = _event$data.html,
        label = _event$data.label,
        source = _event$data.source;
      switch (type) {
        case 'library/attach:start':
          isApplyingTemplate.current = true;
          break;
        case 'library/attach':
          props.onAttach([{
            type: _attachments.ATTACHMENT_TYPE_JSON,
            previewHTML: html,
            content: json,
            label: label,
            source: source
          }]);
          isApplyingTemplate.current = false;
          props.onClose();
          break;
      }
    };
    window.addEventListener('message', onMessage);
    return function () {
      window.removeEventListener('message', onMessage);
    };
  });
  $e.run('library/open', {
    toDefault: true,
    mode: 'ai-attachment'
  });
  isApplyingTemplate.current = false;
  return null;
};
LibraryDialog.propTypes = {
  onAttach: _propTypes.default.func.isRequired,
  onClose: _propTypes.default.func.isRequired
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/menu.js":
/*!***************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments/menu.js ***!
  \***************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.Menu = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _xCircleIcon = _interopRequireDefault(__webpack_require__(/*! ../../../../icons/x-circle-icon */ "../modules/ai/assets/js/editor/icons/x-circle-icon.js"));
var _plusCircleIcon = _interopRequireDefault(__webpack_require__(/*! ../../../../icons/plus-circle-icon */ "../modules/ai/assets/js/editor/icons/plus-circle-icon.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _attachDialog = __webpack_require__(/*! ./attach-dialog */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/attach-dialog.js");
var _useIntroduction2 = _interopRequireDefault(__webpack_require__(/*! ../../../../hooks/use-introduction */ "../modules/ai/assets/js/editor/hooks/use-introduction.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var Menu = exports.Menu = function Menu(props) {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isOpen = _useState2[0],
    setIsOpen = _useState2[1];
  var _useState3 = (0, _react.useState)(null),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    selectedType = _useState4[0],
    setSelectedType = _useState4[1];
  var _useTheme = (0, _ui.useTheme)(),
    direction = _useTheme.direction;
  var anchorRef = (0, _react.useRef)(null);
  var _useIntroduction = (0, _useIntroduction2.default)('e-ai-attachment-badge'),
    isViewed = _useIntroduction.isViewed,
    markAsViewed = _useIntroduction.markAsViewed;
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(_ui.IconButton, {
    size: "small",
    ref: anchorRef,
    disabled: props.disabled,
    onClick: function onClick() {
      setIsOpen(true);
      if (!isViewed) {
        markAsViewed();
      }
    },
    color: "secondary"
  }, function () {
    if (isOpen) {
      return /*#__PURE__*/_react.default.createElement(_xCircleIcon.default, {
        fontSize: "small"
      });
    } else if (isViewed) {
      return /*#__PURE__*/_react.default.createElement(_plusCircleIcon.default, {
        fontSize: "small"
      });
    }
    return /*#__PURE__*/_react.default.createElement(_ui.Badge, {
      color: "primary",
      badgeContent: " ",
      variant: "dot"
    }, /*#__PURE__*/_react.default.createElement(_plusCircleIcon.default, {
      fontSize: "small"
    }));
  }()), /*#__PURE__*/_react.default.createElement(_ui.Popover, {
    open: isOpen,
    anchorEl: anchorRef.current,
    onClose: function onClose() {
      return setIsOpen(false);
    },
    anchorOrigin: {
      vertical: 'bottom',
      horizontal: 'rtl' === direction ? 'right' : 'left'
    },
    transformOrigin: {
      vertical: 'top',
      horizontal: 'rtl' === direction ? 'right' : 'left'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    sx: {
      width: 440
    }
  }, props.items.map(function (item) {
    var IconComponent = item.icon;
    return /*#__PURE__*/_react.default.createElement(_ui.MenuItem, {
      key: item.type,
      onClick: function onClick() {
        setSelectedType(item.type);
        setIsOpen(false);
      }
    }, /*#__PURE__*/_react.default.createElement(_ui.ListItemIcon, null, /*#__PURE__*/_react.default.createElement(IconComponent, null)), item.title);
  }))), /*#__PURE__*/_react.default.createElement(_attachDialog.AttachDialog, {
    type: selectedType,
    onAttach: props.onAttach,
    onClose: function onClose() {
      setIsOpen(false);
      setSelectedType(null);
    }
  }));
};
Menu.propTypes = {
  items: _propTypes.default.arrayOf(_propTypes.default.shape({
    title: _propTypes.default.string.isRequired,
    type: _propTypes.default.string.isRequired,
    icon: _propTypes.default.elementType
  })).isRequired,
  onAttach: _propTypes.default.func.isRequired,
  disabled: _propTypes.default.bool
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/prompt-power-notice.js":
/*!******************************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments/prompt-power-notice.js ***!
  \******************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.PromptPowerNotice = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _useIntroduction2 = _interopRequireDefault(__webpack_require__(/*! ../../../../hooks/use-introduction */ "../modules/ai/assets/js/editor/hooks/use-introduction.js"));
var PromptPowerNotice = exports.PromptPowerNotice = function PromptPowerNotice() {
  var _useIntroduction = (0, _useIntroduction2.default)('e-ai-builder-attachments-power'),
    isViewed = _useIntroduction.isViewed,
    markAsViewed = _useIntroduction.markAsViewed;
  if (isViewed) {
    return null;
  }
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      pt: 2,
      px: 2,
      pb: 0
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Alert, {
    severity: "info",
    onClose: function onClose() {
      return markAsViewed();
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    display: "inline-block",
    sx: {
      paddingInlineEnd: 1
    }
  }, (0, _i18n.__)('You’ve got the power.', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    display: "inline-block"
  }, (0, _i18n.__)('Craft your prompt to affect content, images and/or colors - whichever you decide.', 'elementor'))));
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail-json.js":
/*!*************************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail-json.js ***!
  \*************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.ThumbnailJson = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _thumbnail = __webpack_require__(/*! ./thumbnail */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail.js");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _attachment = __webpack_require__(/*! ../../../../types/attachment */ "../modules/ai/assets/js/editor/types/attachment.js");
var ThumbnailJson = exports.ThumbnailJson = function ThumbnailJson(props) {
  var _props$attachments;
  var attachment = (_props$attachments = props.attachments) === null || _props$attachments === void 0 ? void 0 : _props$attachments.find(function (item) {
    return 'json' === item.type;
  });
  if (!attachment) {
    return null;
  }
  if (!attachment.previewHTML) {
    return /*#__PURE__*/_react.default.createElement(_ui.Skeleton, {
      animation: "wave",
      variant: "rounded",
      width: 60,
      height: 60
    });
  }
  return /*#__PURE__*/_react.default.createElement(_thumbnail.Thumbnail, {
    html: attachment.previewHTML,
    disabled: props.disabled
  });
};
ThumbnailJson.propTypes = {
  attachments: _propTypes.default.arrayOf(_attachment.AttachmentPropType).isRequired,
  disabled: _propTypes.default.bool
};
var _default = exports["default"] = ThumbnailJson;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail-url.js":
/*!************************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail-url.js ***!
  \************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.ThumbnailUrl = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _thumbnail = __webpack_require__(/*! ./thumbnail */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail.js");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _icons = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
var _attachment = __webpack_require__(/*! ../../../../types/attachment */ "../modules/ai/assets/js/editor/types/attachment.js");
var ThumbnailUrl = exports.ThumbnailUrl = function ThumbnailUrl(props) {
  var _props$attachments;
  var attachment = (_props$attachments = props.attachments) === null || _props$attachments === void 0 ? void 0 : _props$attachments.find(function (item) {
    return 'url' === item.type;
  });
  if (!attachment) {
    return null;
  }
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      position: 'relative',
      '&:hover::before': {
        content: '""',
        position: 'absolute',
        userSelect: 'none',
        inset: 0,
        backgroundColor: 'rgba(0,0,0,0.6)',
        borderRadius: 1,
        zIndex: 1
      },
      '&:hover .remove-attachment': {
        display: 'flex'
      }
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.IconButton, {
    className: "remove-attachment",
    size: "small",
    "aria-label": (0, _i18n.__)('Remove', 'elementor'),
    disabled: props.disabled,
    onClick: function onClick(event) {
      event.stopPropagation();
      props.onDetach();
    },
    sx: {
      display: 'none',
      position: 'absolute',
      insetInlineEnd: 4,
      insetBlockStart: 4,
      backgroundColor: 'secondary.main',
      zIndex: 1,
      borderRadius: 1,
      p: '3px',
      '&:hover': {
        backgroundColor: 'secondary.dark'
      }
    }
  }, /*#__PURE__*/_react.default.createElement(_icons.TrashIcon, {
    sx: {
      fontSize: '1.125rem',
      color: 'common.white'
    }
  })), /*#__PURE__*/_react.default.createElement(_thumbnail.Thumbnail, {
    disabled: props.disabled,
    html: attachment.previewHTML
  }));
};
ThumbnailUrl.propTypes = {
  attachments: _propTypes.default.arrayOf(_attachment.AttachmentPropType).isRequired,
  disabled: _propTypes.default.bool,
  onDetach: _propTypes.default.func
};
var _default = exports["default"] = ThumbnailUrl;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail.js":
/*!********************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments/thumbnail.js ***!
  \********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.Thumbnail = exports.THUMBNAIL_SIZE = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _templateObject;
var THUMBNAIL_SIZE = exports.THUMBNAIL_SIZE = 64;
var StyledBody = (0, _ui.styled)('body')(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\thtml, body {\n\t\tmargin: 0;\n\t\tpadding: 0;\n\t\toverflow: hidden;\n\t}\n\n\tbody > * {\n\t\twidth: 100% !important;\n\t}\n\n\tbody > img {\n\t\theight: 100%;\n\t\tobject-fit: cover;\n\t}\n\n\tbody:has(> img) {\n\t\theight: ", "px\n\t}\n"])), THUMBNAIL_SIZE);
var Thumbnail = exports.Thumbnail = function Thumbnail(props) {
  var _props$html$match, _props$html$match2;
  var dataWidth = (_props$html$match = props.html.match('data-width="(?<width>\\d+)"')) === null || _props$html$match === void 0 || (_props$html$match = _props$html$match.groups) === null || _props$html$match === void 0 ? void 0 : _props$html$match.width;
  var dataHeight = (_props$html$match2 = props.html.match('data-height="(?<height>\\d+)"')) === null || _props$html$match2 === void 0 || (_props$html$match2 = _props$html$match2.groups) === null || _props$html$match2 === void 0 ? void 0 : _props$html$match2.height;
  var width = dataWidth ? parseInt(dataWidth) : THUMBNAIL_SIZE;
  var height = dataHeight ? parseInt(dataHeight) : THUMBNAIL_SIZE;
  var scaleFactor = Math.min(height, width);
  var scale = THUMBNAIL_SIZE / scaleFactor;

  // Center the preview
  var top = height > width ? (THUMBNAIL_SIZE - THUMBNAIL_SIZE * (height / width)) / 2 : 0;
  var left = width > height ? (THUMBNAIL_SIZE - THUMBNAIL_SIZE * (width / height)) / 2 : 0;
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    dir: "ltr",
    sx: {
      position: 'relative',
      cursor: 'default',
      overflow: 'hidden',
      border: '1px solid',
      borderColor: 'grey.300',
      borderRadius: 1,
      boxSizing: 'border-box',
      width: THUMBNAIL_SIZE,
      height: THUMBNAIL_SIZE,
      opacity: props.disabled ? 0.5 : 1
    }
  }, /*#__PURE__*/_react.default.createElement("iframe", {
    title: (0, _i18n.__)('Preview', 'elementor'),
    sandbox: "",
    srcDoc: "<style>" + StyledBody.componentStyle.rules.join('') + "</style>" + props.html,
    style: {
      border: 'none',
      overflow: 'hidden',
      width: width,
      height: height,
      transform: "scale(".concat(scale, ")"),
      transformOrigin: "".concat(left, "px ").concat(top, "px")
    }
  }));
};
Thumbnail.propTypes = {
  html: _propTypes.default.string.isRequired,
  disabled: _propTypes.default.bool
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/url-dialog.js":
/*!*********************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/attachments/url-dialog.js ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.UrlDialog = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _alertDialog = __webpack_require__(/*! ../../../../components/alert-dialog */ "../modules/ai/assets/js/editor/components/alert-dialog.js");
var _useTimeout3 = __webpack_require__(/*! ../../../../hooks/use-timeout */ "../modules/ai/assets/js/editor/hooks/use-timeout.js");
var _attachments = __webpack_require__(/*! ../attachments */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js");
var _remoteConfig = __webpack_require__(/*! ../../context/remote-config */ "../modules/ai/assets/js/editor/pages/form-layout/context/remote-config.js");
var _useUserInfo2 = _interopRequireDefault(__webpack_require__(/*! ../../../../hooks/use-user-info */ "../modules/ai/assets/js/editor/hooks/use-user-info.js"));
var _requestsIds = __webpack_require__(/*! ../../../../context/requests-ids */ "../modules/ai/assets/js/editor/context/requests-ids.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var UrlDialog = exports.UrlDialog = function UrlDialog(props) {
  var _useTimeout = (0, _useTimeout3.useTimeout)(10000),
    _useTimeout2 = (0, _slicedToArray2.default)(_useTimeout, 2),
    isTimeout = _useTimeout2[0],
    turnOffTimeout = _useTimeout2[1];
  var _useUserInfo = (0, _useUserInfo2.default)(),
    isLoading = _useUserInfo.isLoading,
    initialUsagePercentage = _useUserInfo.usagePercentage;
  var _useRequestIds = (0, _requestsIds.useRequestIds)(),
    updateUsagePercentage = _useRequestIds.updateUsagePercentage;
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isInitUsageDone = _useState2[0],
    setIsInitUsageDone = _useState2[1];
  var _useRemoteConfig = (0, _remoteConfig.useRemoteConfig)(),
    remoteConfig = _useRemoteConfig.remoteConfig;
  var builderUrl = remoteConfig[_remoteConfig.CONFIG_KEYS.WEB_BASED_BUILDER_URL];
  var urlObject = builderUrl ? new URL(builderUrl) : {};
  var iframeOrigin = urlObject.origin;
  var isOpen = (0, _react.useRef)(false);
  (0, _react.useEffect)(function () {
    if (!isInitUsageDone && !isLoading && (initialUsagePercentage || 0 === initialUsagePercentage)) {
      updateUsagePercentage(initialUsagePercentage);
      setIsInitUsageDone(true);
    }
  }, [isLoading, initialUsagePercentage, isInitUsageDone, updateUsagePercentage]);
  (0, _react.useEffect)(function () {
    if (!isOpen.current) {
      try {
        window.$e.run('ai-integration/open-choose-element', {
          url: props.url
        });
        isOpen.current = true;
      } catch (error) {
        // eslint-disable-next-line no-console
        console.error(error);
      }
    }
  }, [isOpen.current]);
  (0, _react.useEffect)(function () {
    var onMessage = function onMessage(event) {
      if (event.origin !== iframeOrigin) {
        return;
      }
      var _event$data = event.data,
        type = _event$data.type,
        html = _event$data.html,
        url = _event$data.url;
      switch (type) {
        case 'element-selector/close':
          isOpen.current = false;
          props.onClose();
          break;
        case 'element-selector/loaded':
          turnOffTimeout();
          isOpen.current = true;
          break;
        case 'element-selector/attach':
          props.onAttach([{
            type: 'url',
            previewHTML: html,
            content: html,
            label: url ? new URL(url).href : '',
            source: _attachments.USER_URL_SOURCE
          }]);
          break;
      }
    };
    window.addEventListener('message', onMessage);
    return function () {
      window.removeEventListener('message', onMessage);
    };
  }, [iframeOrigin, props, turnOffTimeout]);
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, !isOpen.current && !isTimeout && /*#__PURE__*/_react.default.createElement(_ui.Dialog, {
    open: true,
    maxWidth: "lg"
  }, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    sx: {
      textAlign: 'center',
      padding: 3
    }
  }, (0, _i18n.__)('Loading...', 'elementor'))), isTimeout && /*#__PURE__*/_react.default.createElement(_alertDialog.AlertDialog, {
    message: (0, _i18n.__)('The app is not responding. Please try again later. (#408)', 'elementor'),
    onClose: props.onClose
  }));
};
UrlDialog.propTypes = {
  onAttach: _propTypes.default.func.isRequired,
  onClose: _propTypes.default.func.isRequired,
  url: _propTypes.default.string
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/layout-dialog.js":
/*!************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/layout-dialog.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _promptDialog = _interopRequireDefault(__webpack_require__(/*! ../../../components/prompt-dialog */ "../modules/ai/assets/js/editor/components/prompt-dialog.js"));
var _icons = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
var _excluded = ["sx", "PaperProps"];
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var StyledDialog = (0, _ui.styled)(_promptDialog.default)(function () {
  return {
    '& .MuiDialog-container': {
      marginTop: 0,
      alignItems: 'flex-end',
      paddingBottom: '16vh'
    },
    '& .MuiPaper-root': {
      margin: 0,
      maxHeight: '80vh'
    }
  };
});
var DialogHeader = function DialogHeader(_ref) {
  var onClose = _ref.onClose,
    children = _ref.children;
  return /*#__PURE__*/_react.default.createElement(_ui.AppBar, {
    sx: {
      fontWeight: 'normal'
    },
    color: "transparent",
    position: "relative"
  }, /*#__PURE__*/_react.default.createElement(_ui.Toolbar, {
    variant: "dense"
  }, /*#__PURE__*/_react.default.createElement(_icons.AIIcon, {
    sx: {
      mr: 1
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    component: "span",
    variant: "subtitle2",
    sx: {
      fontWeight: 'bold',
      textTransform: 'uppercase'
    }
  }, (0, _i18n.__)('AI', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Chip, {
    label: (0, _i18n.__)('Beta', 'elementor'),
    color: "default",
    size: "small",
    sx: {
      ml: 1
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    spacing: 1,
    alignItems: "center",
    sx: {
      ml: 'auto'
    }
  }, children, /*#__PURE__*/_react.default.createElement(_ui.IconButton, {
    size: "small",
    "aria-label": "close",
    onClick: onClose,
    sx: {
      '&.MuiButtonBase-root': {
        mr: -1
      }
    }
  }, /*#__PURE__*/_react.default.createElement(_icons.XIcon, null)))));
};
DialogHeader.propTypes = {
  children: _propTypes.default.node,
  onClose: _propTypes.default.func.isRequired
};
var StyledDialogContent = (0, _ui.styled)(_promptDialog.default.Content)(function () {
  return {
    '&.MuiDialogContent-root': {
      padding: 0
    }
  };
});
var LayoutDialog = function LayoutDialog(_ref2) {
  var _ref2$sx = _ref2.sx,
    sx = _ref2$sx === void 0 ? {} : _ref2$sx,
    _ref2$PaperProps = _ref2.PaperProps,
    PaperProps = _ref2$PaperProps === void 0 ? {} : _ref2$PaperProps,
    props = (0, _objectWithoutProperties2.default)(_ref2, _excluded);
  var _useState = (0, _react.useState)({
      pointerEvents: 'none'
    }),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    sxStyle = _useState2[0],
    setSxStyle = _useState2[1];
  var timeoutRef = (0, _react.useRef)(null);

  /**
   * The PromptDialog is using disableScrollLock in order to allow scrolling the page when the Dialog is opened.
   * When using the react-draggable library inside the editor, the background page scroll is not working smoothly.
   * Therefore, we need to delay the pointerEvents: none, which allowing to scroll the page content.
   */
  return /*#__PURE__*/_react.default.createElement(StyledDialog, (0, _extends2.default)({
    maxWidth: "md",
    PaperProps: _objectSpread({
      sx: {
        pointerEvents: 'auto'
      },
      onMouseEnter: function onMouseEnter() {
        clearTimeout(timeoutRef.current);
        setSxStyle({
          pointerEvents: 'all'
        });
      },
      onMouseLeave: function onMouseLeave() {
        clearTimeout(timeoutRef.current);
        timeoutRef.current = setTimeout(function () {
          setSxStyle({
            pointerEvents: 'none'
          });
        }, 200);
      }
    }, PaperProps)
  }, props, {
    sx: _objectSpread(_objectSpread({}, sxStyle), sx)
  }));
};
LayoutDialog.propTypes = {
  sx: _propTypes.default.object,
  PaperProps: _propTypes.default.object
};
LayoutDialog.Header = DialogHeader;
LayoutDialog.Content = StyledDialogContent;
var _default = exports["default"] = LayoutDialog;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/pro-template-indicator.js":
/*!*********************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/pro-template-indicator.js ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.ProTemplateIndicator = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _lockIcon = _interopRequireDefault(__webpack_require__(/*! ../../../icons/lock-icon */ "../modules/ai/assets/js/editor/icons/lock-icon.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var popoverId = 'e-pro-upgrade-popover';
var StyledContent = (0, _ui.styled)(_ui.Paper)(function (_ref) {
  var theme = _ref.theme;
  return {
    position: 'relative',
    padding: theme.spacing(3),
    boxShadow: theme.shadows[4],
    zIndex: '9999'
  };
});
var StyledArrow = (0, _ui.styled)(_ui.Box)(function (_ref2) {
  var theme = _ref2.theme;
  return {
    position: 'absolute',
    width: theme.spacing(5),
    height: theme.spacing(5),
    overflow: 'hidden',
    // Override Popper inline styles.
    left: '100% !important',
    transform: 'translateX(-50%) translateY(-50%) rotate(var(--rotate, 0deg)) !important',
    '&::after': {
      backgroundColor: theme.palette.background.paper,
      content: '""',
      display: 'block',
      position: 'absolute',
      width: theme.spacing(2.5),
      height: theme.spacing(2.5),
      top: '50%',
      left: '50%',
      transform: 'translateX(-50%) translateY(-50%) rotate(45deg)',
      boxShadow: '5px -5px 5px 0px rgba(0, 0, 0, 0.2)',
      backgroundImage: 'linear-gradient(rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.05))'
    }
  };
});
var ProTemplateIndicator = exports.ProTemplateIndicator = function ProTemplateIndicator() {
  var actionUrl = 'https://go.elementor.com/go-pro-ai/';
  var actionLabel = (0, _i18n.__)('Go Pro', 'elementor');
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isPopoverOpen = _useState2[0],
    setIsPopoverOpen = _useState2[1];
  var anchorEl = (0, _react.useRef)(null);
  var arrowEl = (0, _react.useRef)(null);
  var showPopover = function showPopover() {
    return setIsPopoverOpen(true);
  };
  var hidePopover = function hidePopover() {
    return setIsPopoverOpen(false);
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    flexDirection: "row-reverse",
    component: "span",
    display: "flex",
    onMouseLeave: hidePopover,
    alignItems: "center"
  }, /*#__PURE__*/_react.default.createElement(_ui.IconButton, {
    ref: anchorEl,
    onMouseEnter: showPopover,
    onClick: function onClick(e) {
      return e.stopPropagation();
    } /* Do nothing */,
    "aria-owns": isPopoverOpen ? popoverId : undefined,
    "aria-haspopup": "true",
    sx: {
      m: 1,
      '&:hover': {
        backgroundColor: 'action.selected'
      }
    }
  }, /*#__PURE__*/_react.default.createElement(_lockIcon.default, {
    sx: {
      color: 'text.primary'
    }
  })), /*#__PURE__*/_react.default.createElement(_ui.Popper, {
    open: isPopoverOpen,
    popperOptions: {
      placement: 'left-start',
      modifiers: [{
        name: 'arrow',
        enabled: true,
        options: {
          element: arrowEl.current,
          padding: 5
        }
      }, {
        name: 'offset',
        options: {
          offset: [0, 10]
        }
      }]
    },
    anchorEl: anchorEl.current,
    sx: {
      zIndex: '9999',
      maxWidth: 300
    }
  }, /*#__PURE__*/_react.default.createElement(StyledContent, null, /*#__PURE__*/_react.default.createElement(StyledArrow, {
    ref: arrowEl
  }), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    alignItems: "start",
    spacing: 2
  }, /*#__PURE__*/_react.default.createElement(_ui.Chip, {
    color: "promotion",
    variant: "outlined",
    size: "small",
    label: (0, _i18n.__)('Pro', 'elementor'),
    icon: /*#__PURE__*/_react.default.createElement(_lockIcon.default, null)
  }), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2"
  }, (0, _i18n.__)("This result includes an Elementor Pro widget that's not available with your current plan. Upgrade to use all the widgets in this result.", 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Button, {
    variant: "contained",
    color: "promotion",
    size: "small",
    href: actionUrl,
    target: "_blank",
    sx: {
      alignSelf: 'flex-end'
    }
  }, actionLabel)))));
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/prompt-autocomplete.js":
/*!******************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/prompt-autocomplete.js ***!
  \******************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _promptLibraryLink = _interopRequireDefault(__webpack_require__(/*! ../../../components/prompt-library-link */ "../modules/ai/assets/js/editor/components/prompt-library-link.js"));
var _config = __webpack_require__(/*! ../context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var _excluded = ["onSubmit"];
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var TextInput = (0, _react.forwardRef)(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement(_ui.TextField
  // eslint-disable-next-line jsx-a11y/no-autofocus
  , (0, _extends2.default)({
    autoFocus: true,
    multiline: true,
    size: "small",
    maxRows: 3,
    color: "secondary",
    variant: "standard"
  }, props, {
    inputRef: ref,
    InputProps: _objectSpread(_objectSpread({}, props.InputProps), {}, {
      type: 'search',
      sx: {
        pt: 0
      }
    })
  }));
});
TextInput.propTypes = {
  InputProps: _propTypes.default.object
};
var PaperComponent = function PaperComponent(props) {
  var _useConfig = (0, _config.useConfig)(),
    mode = _useConfig.mode;
  var libraryLink = _config.MODE_VARIATION === mode ? 'https://go.elementor.com/ai-prompt-library-variations/' : 'https://go.elementor.com/ai-prompt-library-containers/';
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, (0, _extends2.default)({}, props, {
    elevation: 8,
    sx: {
      borderRadius: 2
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    component: _ui.Box,
    color: function color(theme) {
      return theme.palette.text.tertiary;
    },
    variant: "caption",
    paddingX: 2,
    paddingY: 1
  }, (0, _i18n.__)('Suggested Prompts', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Divider, null), props.children, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    sx: {
      m: 2
    }
  }, /*#__PURE__*/_react.default.createElement(_promptLibraryLink.default, {
    libraryLink: libraryLink
  })));
};
PaperComponent.propTypes = {
  children: _propTypes.default.node
};
var PromptAutocomplete = function PromptAutocomplete(_ref) {
  var onSubmit = _ref.onSubmit,
    props = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    showSuggestions = _useState2[0],
    setShowSuggestions = _useState2[1];
  var theme = (0, _ui.useTheme)();
  var itemHeight = parseInt(theme.spacing(4));
  var maxItems = 5;
  return /*#__PURE__*/_react.default.createElement(_ui.Autocomplete, (0, _extends2.default)({
    PaperComponent: PaperComponent,
    ListboxProps: {
      sx: {
        maxHeight: maxItems * itemHeight
      }
    },
    renderOption: function renderOption(optionProps, option) {
      return /*#__PURE__*/_react.default.createElement(_ui.Typography, (0, _extends2.default)({}, optionProps, {
        title: option.text,
        noWrap: true,
        variant: "body2",
        component: _ui.Box,
        sx: {
          '&.MuiAutocomplete-option': {
            display: 'block',
            minHeight: itemHeight
          }
        }
      }), option.text);
    },
    freeSolo: true,
    fullWidth: true,
    disableClearable: true,
    open: showSuggestions,
    onClose: function onClose(e) {
      var _e$relatedTarget;
      return setShowSuggestions('A' === ((_e$relatedTarget = e.relatedTarget) === null || _e$relatedTarget === void 0 ? void 0 : _e$relatedTarget.tagName));
    },
    onKeyDown: function onKeyDown(e) {
      if ('Enter' === e.key && !e.shiftKey && !showSuggestions) {
        onSubmit(e);
      } else if ('/' === e.key && '' === e.target.value) {
        e.preventDefault();
        setShowSuggestions(true);
      }
    }
  }, props));
};
PromptAutocomplete.propTypes = {
  onSubmit: _propTypes.default.func.isRequired
};
PromptAutocomplete.TextInput = TextInput;
var _default = exports["default"] = PromptAutocomplete;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/prompt-form.js":
/*!**********************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/prompt-form.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _promptAutocomplete = _interopRequireDefault(__webpack_require__(/*! ./prompt-autocomplete */ "../modules/ai/assets/js/editor/pages/form-layout/components/prompt-autocomplete.js"));
var _enhanceButton = _interopRequireDefault(__webpack_require__(/*! ../../form-media/components/enhance-button */ "../modules/ai/assets/js/editor/pages/form-media/components/enhance-button.js"));
var _generateSubmit = _interopRequireDefault(__webpack_require__(/*! ../../form-media/components/generate-submit */ "../modules/ai/assets/js/editor/pages/form-media/components/generate-submit.js"));
var _arrowLeftIcon = _interopRequireDefault(__webpack_require__(/*! ../../../icons/arrow-left-icon */ "../modules/ai/assets/js/editor/icons/arrow-left-icon.js"));
var _editIcon = _interopRequireDefault(__webpack_require__(/*! ../../../icons/edit-icon */ "../modules/ai/assets/js/editor/icons/edit-icon.js"));
var _usePromptEnhancer2 = _interopRequireDefault(__webpack_require__(/*! ../../../hooks/use-prompt-enhancer */ "../modules/ai/assets/js/editor/hooks/use-prompt-enhancer.js"));
var _attachments = _interopRequireDefault(__webpack_require__(/*! ./attachments */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js"));
var _config = __webpack_require__(/*! ../context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var _attachment = __webpack_require__(/*! ../../../types/attachment */ "../modules/ai/assets/js/editor/types/attachment.js");
var _excluded = ["tooltip"];
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var PROMPT_SUGGESTIONS = Object.freeze([
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('Hero section on [topic] with heading, text, buttons on the right, and an image on the left', 'elementor.com')
},
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('About Us section on [topic] with heading, text, and big image below', 'elementor.com')
}, {
  text: (0, _i18n.__)('Team section with four image boxes showcasing team members', 'elementor.com')
},
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('FAQ section with a toggle widget showcasing FAQs about [topic]', 'elementor.com')
}, {
  text: (0, _i18n.__)('Gallery section with a carousel displaying three images at once', 'elementor.com')
},
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('Contact section with a form for [topic]', 'elementor.com')
}, {
  text: (0, _i18n.__)('Client section featuring companies\' logos', 'elementor.com')
},
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('Testimonial section with testimonials, each featuring a star rating and an image', 'elementor.com')
},
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('Service section about [topic], showcasing four services with buttons', 'elementor.com')
},
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('Stats section with counters displaying data about [topic]', 'elementor.com')
}, {
  text: (0, _i18n.__)('Quote section with colored background, featuring a centered quote', 'elementor.com')
},
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('Pricing section for [topic] with a pricing list', 'elementor.com')
},
// Translators: [Topic] is a placeholder for the user - please translate it as well
{
  text: (0, _i18n.__)('Subscribe section featuring a simple email form, inviting users to stay informed on [topic]', 'elementor.com')
}]);
var IconButtonWithTooltip = function IconButtonWithTooltip(_ref) {
  var tooltip = _ref.tooltip,
    props = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  return /*#__PURE__*/_react.default.createElement(_ui.Tooltip, {
    title: tooltip
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    component: "span",
    sx: {
      cursor: props.disabled ? 'default' : 'pointer'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.IconButton, props)));
};
IconButtonWithTooltip.propTypes = {
  tooltip: _propTypes.default.string,
  disabled: _propTypes.default.bool
};
var BackButton = function BackButton(props) {
  return /*#__PURE__*/_react.default.createElement(IconButtonWithTooltip, (0, _extends2.default)({
    size: "small",
    color: "secondary",
    tooltip: (0, _i18n.__)('Back to results', 'elementor')
  }, props), /*#__PURE__*/_react.default.createElement(_arrowLeftIcon.default, null));
};
var EditButton = function EditButton(props) {
  return /*#__PURE__*/_react.default.createElement(IconButtonWithTooltip, (0, _extends2.default)({
    size: "small",
    color: "primary",
    tooltip: (0, _i18n.__)('Edit prompt', 'elementor')
  }, props), /*#__PURE__*/_react.default.createElement(_editIcon.default, null));
};
var GenerateButton = function GenerateButton(props) {
  return /*#__PURE__*/_react.default.createElement(_generateSubmit.default, (0, _extends2.default)({
    size: "small",
    fullWidth: false
  }, props), (0, _i18n.__)('Generate', 'elementor'));
};
var PromptForm = (0, _react.forwardRef)(function (_ref2, ref) {
  var _attachments$;
  var attachments = _ref2.attachments,
    isActive = _ref2.isActive,
    isLoading = _ref2.isLoading,
    _ref2$showActions = _ref2.showActions,
    showActions = _ref2$showActions === void 0 ? false : _ref2$showActions,
    onAttach = _ref2.onAttach,
    onDetach = _ref2.onDetach,
    _onSubmit = _ref2.onSubmit,
    onBack = _ref2.onBack,
    onEdit = _ref2.onEdit,
    _ref2$shouldResetProm = _ref2.shouldResetPrompt,
    shouldResetPrompt = _ref2$shouldResetProm === void 0 ? false : _ref2$shouldResetProm;
  var _useState = (0, _react.useState)(''),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    prompt = _useState2[0],
    setPrompt = _useState2[1];
  (0, _react.useEffect)(function () {
    if (shouldResetPrompt) {
      setPrompt('');
    }
  }, [shouldResetPrompt]);
  var _usePromptEnhancer = (0, _usePromptEnhancer2.default)(prompt, 'layout'),
    isEnhancing = _usePromptEnhancer.isEnhancing,
    enhance = _usePromptEnhancer.enhance;
  var previousPrompt = (0, _react.useRef)('');
  var _useConfig = (0, _config.useConfig)(),
    attachmentsTypes = _useConfig.attachmentsTypes;
  var isInputDisabled = isLoading || isEnhancing || !isActive;
  var isInputEmpty = '' === prompt && !attachments.length;
  var isGenerateDisabled = isInputDisabled || isInputEmpty;
  var attachmentsType = ((_attachments$ = attachments[0]) === null || _attachments$ === void 0 ? void 0 : _attachments$.type) || '';
  var attachmentsConfig = attachmentsTypes[attachmentsType];
  var promptSuggestions = (attachmentsConfig === null || attachmentsConfig === void 0 ? void 0 : attachmentsConfig.promptSuggestions) || PROMPT_SUGGESTIONS;
  var promptPlaceholder = (attachmentsConfig === null || attachmentsConfig === void 0 ? void 0 : attachmentsConfig.promptPlaceholder) || (0, _i18n.__)("Press '/' for suggested prompts or describe the layout you want to create", 'elementor');
  var handleBack = function handleBack() {
    setPrompt(previousPrompt.current);
    onBack();
  };
  var handleEdit = function handleEdit() {
    previousPrompt.current = prompt;
    onEdit();
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    component: "form",
    onSubmit: function onSubmit(e) {
      return _onSubmit(e, prompt);
    },
    direction: "row",
    sx: {
      p: 3
    },
    alignItems: "start",
    gap: 1
  }, /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    alignItems: "start",
    flexGrow: 1,
    spacing: 2
  }, showActions && (isActive ? /*#__PURE__*/_react.default.createElement(BackButton, {
    disabled: isLoading || isEnhancing,
    onClick: handleBack
  }) : /*#__PURE__*/_react.default.createElement(EditButton, {
    disabled: isLoading,
    onClick: handleEdit
  })), /*#__PURE__*/_react.default.createElement(_attachments.default, {
    attachments: attachments,
    onAttach: onAttach,
    onDetach: onDetach,
    disabled: isInputDisabled
  }), /*#__PURE__*/_react.default.createElement(_promptAutocomplete.default, {
    value: prompt,
    disabled: isInputDisabled,
    onSubmit: function onSubmit(e) {
      return _onSubmit(e, prompt);
    },
    options: promptSuggestions,
    onChange: function onChange(_, selectedValue) {
      return setPrompt(selectedValue.text + ' ');
    },
    renderInput: function renderInput(params) {
      return /*#__PURE__*/_react.default.createElement(_promptAutocomplete.default.TextInput, (0, _extends2.default)({}, params, {
        ref: ref,
        onChange: function onChange(e) {
          return setPrompt(e.target.value);
        },
        placeholder: promptPlaceholder
      }));
    }
  })), /*#__PURE__*/_react.default.createElement(_enhanceButton.default, {
    size: "small",
    disabled: isGenerateDisabled || '' === prompt,
    isLoading: isEnhancing,
    onClick: function onClick() {
      return enhance().then(function (_ref3) {
        var result = _ref3.result;
        return setPrompt(result);
      });
    }
  }), /*#__PURE__*/_react.default.createElement(GenerateButton, {
    disabled: isGenerateDisabled
  }));
});
PromptForm.propTypes = {
  isActive: _propTypes.default.bool,
  onAttach: _propTypes.default.func,
  onDetach: _propTypes.default.func,
  isLoading: _propTypes.default.bool,
  showActions: _propTypes.default.bool,
  onSubmit: _propTypes.default.func.isRequired,
  onBack: _propTypes.default.func.isRequired,
  onEdit: _propTypes.default.func.isRequired,
  attachments: _propTypes.default.arrayOf(_attachment.AttachmentPropType),
  shouldResetPrompt: _propTypes.default.bool
};
var _default = exports["default"] = PromptForm;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/screenshot-container.js":
/*!*******************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/screenshot-container.js ***!
  \*******************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var ScreenshotContainer = (0, _ui.styled)(_ui.Box, {
  shouldForwardProp: function shouldForwardProp(prop) {
    return prop !== 'outlineOffset';
  }
})(function (_ref) {
  var theme = _ref.theme,
    selected = _ref.selected,
    height = _ref.height,
    disabled = _ref.disabled,
    _ref$outlineOffset = _ref.outlineOffset,
    outlineOffset = _ref$outlineOffset === void 0 ? '0px' : _ref$outlineOffset;
  var outlineColor = selected ? theme.palette.text.primary : theme.palette.text.disabled;
  var outline = "2px solid ".concat(outlineColor);
  return {
    height: height,
    cursor: disabled ? 'default' : 'pointer',
    overflow: 'hidden',
    boxSizing: 'border-box',
    backgroundPosition: 'top center',
    backgroundSize: '100% auto',
    backgroundRepeat: 'no-repeat',
    backgroundColor: theme.palette.common.white,
    borderRadius: theme.shape.borderRadius * 0.5,
    outlineOffset: outlineOffset,
    outline: outline,
    opacity: disabled ? '0.4' : '1',
    transition: "all 50ms linear",
    '&:hover': disabled ? {} : {
      outlineColor: theme.palette.text.primary
    }
  };
});
var _default = exports["default"] = ScreenshotContainer;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/screenshot-unavailable.js":
/*!*********************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/screenshot-unavailable.js ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ScreenshotUnavailable;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _screenshotContainer = _interopRequireDefault(__webpack_require__(/*! ./screenshot-container */ "../modules/ai/assets/js/editor/pages/form-layout/components/screenshot-container.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function ScreenshotUnavailable(props) {
  return /*#__PURE__*/_react.default.createElement(_screenshotContainer.default, (0, _extends2.default)({}, props, {
    sx: _objectSpread(_objectSpread({}, props.sx || {}), {}, {
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      backgroundColor: 'background.paper',
      color: 'text.tertiary',
      fontStyle: 'italic',
      fontSize: '12px',
      paddingInline: 12,
      textAlign: 'center',
      lineHeight: 1.5
    })
  }), (0, _i18n.__)('Preview unavailable', 'elementor'));
}
ScreenshotUnavailable.propTypes = {
  sx: _propTypes.default.object
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/screenshot.js":
/*!*********************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/screenshot.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _screenshotContainer = _interopRequireDefault(__webpack_require__(/*! ./screenshot-container */ "../modules/ai/assets/js/editor/pages/form-layout/components/screenshot-container.js"));
var _screenshotUnavailable = _interopRequireDefault(__webpack_require__(/*! ./screenshot-unavailable */ "../modules/ai/assets/js/editor/pages/form-layout/components/screenshot-unavailable.js"));
var _templateBadge = _interopRequireDefault(__webpack_require__(/*! ./template-badge */ "../modules/ai/assets/js/editor/pages/form-layout/components/template-badge.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var SCREENSHOT_HEIGHT = '138px';
var Screenshot = function Screenshot(_ref) {
  var url = _ref.url,
    type = _ref.type,
    _ref$isLoading = _ref.isLoading,
    isLoading = _ref$isLoading === void 0 ? false : _ref$isLoading,
    _ref$isSelected = _ref.isSelected,
    isSelected = _ref$isSelected === void 0 ? false : _ref$isSelected,
    isPlaceholder = _ref.isPlaceholder,
    disabled = _ref.disabled,
    onClick = _ref.onClick,
    _ref$sx = _ref.sx,
    sx = _ref$sx === void 0 ? {} : _ref$sx,
    outlineOffset = _ref.outlineOffset;
  if (isPlaceholder) {
    return /*#__PURE__*/_react.default.createElement(_ui.Box, {
      sx: _objectSpread({
        height: SCREENSHOT_HEIGHT
      }, sx)
    });
  }
  if (isLoading) {
    return /*#__PURE__*/_react.default.createElement(_ui.Skeleton, {
      width: "100%",
      animation: "wave",
      variant: "rounded",
      height: SCREENSHOT_HEIGHT,
      sx: sx
    });
  }
  if (!url) {
    return /*#__PURE__*/_react.default.createElement(_screenshotUnavailable.default, {
      selected: isSelected,
      disabled: disabled,
      sx: sx,
      onClick: onClick,
      height: SCREENSHOT_HEIGHT,
      outlineOffset: outlineOffset
    });
  }
  return /*#__PURE__*/_react.default.createElement(_screenshotContainer.default, {
    selected: isSelected,
    disabled: disabled,
    sx: _objectSpread({
      backgroundImage: "url('".concat(url, "')")
    }, sx),
    onClick: onClick,
    height: SCREENSHOT_HEIGHT,
    outlineOffset: outlineOffset
  }, /*#__PURE__*/_react.default.createElement(_templateBadge.default, {
    type: type
  }));
};
Screenshot.propTypes = {
  isSelected: _propTypes.default.bool,
  isLoading: _propTypes.default.bool,
  isPlaceholder: _propTypes.default.bool,
  disabled: _propTypes.default.bool,
  onClick: _propTypes.default.func.isRequired,
  url: _propTypes.default.string,
  type: _propTypes.default.string,
  sx: _propTypes.default.object,
  outlineOffset: _propTypes.default.string
};
var _default = exports["default"] = Screenshot;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/template-badge.js":
/*!*************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/template-badge.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _config = __webpack_require__(/*! ../context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var _proTemplateIndicator = __webpack_require__(/*! ./pro-template-indicator */ "../modules/ai/assets/js/editor/pages/form-layout/components/pro-template-indicator.js");
var TemplateBadge = function TemplateBadge(props) {
  var _useConfig = (0, _config.useConfig)(),
    hasPro = _useConfig.hasPro;
  if ('Pro' === props.type && !hasPro) {
    return /*#__PURE__*/_react.default.createElement(_proTemplateIndicator.ProTemplateIndicator, null);
  }
  return null;
};
var _default = exports["default"] = TemplateBadge;
TemplateBadge.propTypes = {
  type: _propTypes.default.string
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/components/unsaved-changes-alert.js":
/*!********************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/components/unsaved-changes-alert.js ***!
  \********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _excluded = ["onClose", "onCancel", "title", "text"];
var UnsavedChangesAlert = function UnsavedChangesAlert(_ref) {
  var onClose = _ref.onClose,
    onCancel = _ref.onCancel,
    title = _ref.title,
    text = _ref.text,
    props = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  return /*#__PURE__*/_react.default.createElement(_ui.Dialog, (0, _extends2.default)({
    "aria-labelledby": "unsaved-changes-alert-title",
    "aria-describedby": "unsaved-changes-alert-description"
  }, props), /*#__PURE__*/_react.default.createElement(_ui.DialogTitle, {
    id: "unsaved-changes-alert-title"
  }, title), /*#__PURE__*/_react.default.createElement(_ui.DialogContent, null, /*#__PURE__*/_react.default.createElement(_ui.DialogContentText, {
    id: "unsaved-changes-alert-description"
  }, text)), /*#__PURE__*/_react.default.createElement(_ui.DialogActions, null, /*#__PURE__*/_react.default.createElement(_ui.Button, {
    onClick: onCancel,
    color: "secondary"
  }, (0, _i18n.__)('Cancel', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Button, {
    onClick: onClose,
    color: "error",
    variant: "contained"
  }, (0, _i18n.__)('Yes, leave', 'elementor'))));
};
UnsavedChangesAlert.propTypes = {
  title: _propTypes.default.string,
  text: _propTypes.default.string,
  onCancel: _propTypes.default.func,
  onClose: _propTypes.default.func
};
var _default = exports["default"] = UnsavedChangesAlert;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js":
/*!**************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/context/config.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.useConfig = exports["default"] = exports.MODE_VARIATION = exports.MODE_LAYOUT = exports.LAYOUT_APP_MODES = exports.ConfigProvider = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var MODE_LAYOUT = exports.MODE_LAYOUT = 'layout';
var MODE_VARIATION = exports.MODE_VARIATION = 'variation';
var LAYOUT_APP_MODES = exports.LAYOUT_APP_MODES = [MODE_LAYOUT, MODE_VARIATION];
var ConfigContext = _react.default.createContext({});
var useConfig = exports.useConfig = function useConfig() {
  return _react.default.useContext(ConfigContext);
};
var ConfigProvider = exports.ConfigProvider = function ConfigProvider(props) {
  return /*#__PURE__*/_react.default.createElement(ConfigContext.Provider, {
    value: {
      mode: props.mode,
      attachmentsTypes: props.attachmentsTypes,
      onClose: props.onClose,
      onConnect: props.onConnect,
      onData: props.onData,
      onInsert: props.onInsert,
      onSelect: props.onSelect,
      onGenerate: props.onGenerate,
      currentContext: props.currentContext,
      hasPro: props.hasPro
    }
  }, props.children);
};
ConfigProvider.propTypes = {
  mode: _propTypes.default.oneOf(LAYOUT_APP_MODES).isRequired,
  children: _propTypes.default.node.isRequired,
  attachmentsTypes: _propTypes.default.object.isRequired,
  onClose: _propTypes.default.func.isRequired,
  onConnect: _propTypes.default.func.isRequired,
  onData: _propTypes.default.func.isRequired,
  onInsert: _propTypes.default.func.isRequired,
  onSelect: _propTypes.default.func.isRequired,
  onGenerate: _propTypes.default.func.isRequired,
  currentContext: _propTypes.default.object,
  hasPro: _propTypes.default.bool
};
var _default = exports["default"] = ConfigContext;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/context/remote-config.js":
/*!*********************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/context/remote-config.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.useRemoteConfig = exports.RemoteConfigProvider = exports.CONFIG_KEYS = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _api = __webpack_require__(/*! ../../../api */ "../modules/ai/assets/js/editor/api/index.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t2 in e) "default" !== _t2 && {}.hasOwnProperty.call(e, _t2) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t2)) && (i.get || i.set) ? o(f, _t2, i) : f[_t2] = e[_t2]); return f; })(e, t); }
var RemoteConfigContext = _react.default.createContext({});
var useRemoteConfig = exports.useRemoteConfig = function useRemoteConfig() {
  return _react.default.useContext(RemoteConfigContext);
};
var CONFIG_KEYS = exports.CONFIG_KEYS = {
  WEB_BASED_BUILDER_URL: 'webBasedBuilderUrl',
  AUTH_TOKEN: 'jwt'
};
var RemoteConfigProvider = exports.RemoteConfigProvider = function RemoteConfigProvider(props) {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isLoading = _useState2[0],
    setIsLoading = _useState2[1];
  var _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isLoaded = _useState4[0],
    setIsLoaded = _useState4[1];
  var _useState5 = (0, _react.useState)(false),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    isError = _useState6[0],
    setIsError = _useState6[1];
  var _useState7 = (0, _react.useState)({}),
    _useState8 = (0, _slicedToArray2.default)(_useState7, 2),
    remoteConfig = _useState8[0],
    setRemoteConfig = _useState8[1];
  var fetchData = /*#__PURE__*/function () {
    var _ref = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
      var result, _t;
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            setIsLoading(true);
            setIsError(false);
            _context.prev = 1;
            _context.next = 2;
            return (0, _api.getRemoteConfig)().finally(function () {
              setIsLoaded(true);
              setIsLoading(false);
            });
          case 2:
            result = _context.sent;
            if (result.config) {
              _context.next = 3;
              break;
            }
            throw new Error('Invalid remote config');
          case 3:
            setRemoteConfig(result.config);
            _context.next = 5;
            break;
          case 4:
            _context.prev = 4;
            _t = _context["catch"](1);
            setIsError(true);
            setIsLoaded(true);
            setIsLoading(false);
          case 5:
          case "end":
            return _context.stop();
        }
      }, _callee, null, [[1, 4]]);
    }));
    return function fetchData() {
      return _ref.apply(this, arguments);
    };
  }();
  (0, _react.useEffect)(function () {
    window.addEventListener('elementor/connect/success', fetchData);
    return function () {
      window.removeEventListener('elementor/connect/success', fetchData);
    };
  }, []);
  if (!isLoaded && !isLoading) {
    fetchData();
  }
  return /*#__PURE__*/_react.default.createElement(RemoteConfigContext.Provider, {
    value: {
      isLoading: isLoading,
      isLoaded: isLoaded,
      isError: isError,
      remoteConfig: remoteConfig
    }
  }, props.children);
};
RemoteConfigProvider.propTypes = {
  children: _propTypes.default.node.isRequired,
  onError: _propTypes.default.func.isRequired
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/hooks/use-layout-prompt.js":
/*!***********************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/hooks/use-layout-prompt.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _api = __webpack_require__(/*! ../../../api */ "../modules/ai/assets/js/editor/api/index.js");
var _usePrompt = _interopRequireDefault(__webpack_require__(/*! ../../../hooks/use-prompt */ "../modules/ai/assets/js/editor/hooks/use-prompt.js"));
var useLayoutPrompt = function useLayoutPrompt(type, initialValue) {
  return (0, _usePrompt.default)(function (requestBody, signal) {
    requestBody.variationType = type;
    return (0, _api.generateLayout)(requestBody, signal);
  }, initialValue);
};
var _default = exports["default"] = useLayoutPrompt;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/hooks/use-screenshot.js":
/*!********************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/hooks/use-screenshot.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
var _useLayoutPrompt = _interopRequireDefault(__webpack_require__(/*! ./use-layout-prompt */ "../modules/ai/assets/js/editor/pages/form-layout/hooks/use-layout-prompt.js"));
var ERROR_INITIAL_VALUE = '';
var useScreenshot = function useScreenshot(type, onData) {
  var _useState = (0, _react.useState)(ERROR_INITIAL_VALUE),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    error = _useState2[0],
    setError = _useState2[1];
  var _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isLoading = _useState4[0],
    setIsLoading = _useState4[1];
  var layoutData = (0, _useLayoutPrompt.default)(type, null);
  var generate = function generate(requestBody, signal) {
    setIsLoading(true);
    setError(ERROR_INITIAL_VALUE);
    return layoutData.send(requestBody, signal).then(/*#__PURE__*/function () {
      var _ref = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(data) {
        var createdScreenshot;
        return _regenerator.default.wrap(function (_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              _context.next = 1;
              return onData(data.result);
            case 1:
              createdScreenshot = _context.sent;
              createdScreenshot.sendUsageData = function () {
                return layoutData.sendUsageData(data);
              };
              createdScreenshot.baseTemplateId = data.baseTemplateId;
              createdScreenshot.type = data.type;
              return _context.abrupt("return", createdScreenshot);
            case 2:
            case "end":
              return _context.stop();
          }
        }, _callee);
      }));
      return function (_x) {
        return _ref.apply(this, arguments);
      };
    }()).catch(function (err) {
      setError(err.extra_data ? err : err.message || err);
      throw err;
    }).finally(function () {
      return setIsLoading(false);
    });
  };
  return {
    generate: generate,
    error: error,
    isLoading: isLoading
  };
};
var _default = exports["default"] = useScreenshot;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/hooks/use-screenshots.js":
/*!*********************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/hooks/use-screenshots.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
var _useScreenshot = _interopRequireDefault(__webpack_require__(/*! ./use-screenshot */ "../modules/ai/assets/js/editor/pages/form-layout/hooks/use-screenshot.js"));
var _config = __webpack_require__(/*! ../context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var _requestsIds = __webpack_require__(/*! ../../../context/requests-ids */ "../modules/ai/assets/js/editor/context/requests-ids.js");
var PENDING_VALUE = {
  isPending: true
};
var useScreenshots = function useScreenshots(_ref) {
  var onData = _ref.onData;
  var _useState = (0, _react.useState)([]),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    screenshots = _useState2[0],
    setScreenshots = _useState2[1];

  /**
   * The ids for each request are:
   * - editorSessionId: a unique id for each editor opening
   * - sessionId: a unique id for each session. (open the AI builder)
   * - generateId: a unique id for each generate request. (prompt change)
   * - batchId: a unique id for each batch of generate requests. (generate, regenerate)
   * - requestId: a unique id for each generate request.
   */

  var _useConfig = (0, _config.useConfig)(),
    currentContext = _useConfig.currentContext;
  var _useRequestIds = (0, _requestsIds.useRequestIds)(),
    editorSessionId = _useRequestIds.editorSessionId,
    sessionId = _useRequestIds.sessionId,
    setRequest = _useRequestIds.setRequest,
    setBatch = _useRequestIds.setBatch,
    setGenerate = _useRequestIds.setGenerate;
  var generateIdRef = (0, _react.useRef)('');
  var batchId = setBatch();
  var screenshotsData = [(0, _useScreenshot.default)(0, onData), (0, _useScreenshot.default)(1, onData), (0, _useScreenshot.default)(2, onData)];
  var screenshotsGroupCount = screenshotsData.length;
  var error = screenshotsData.every(function (s) {
    return s === null || s === void 0 ? void 0 : s.error;
  }) ? screenshotsData[0].error : '';
  var isLoading = screenshotsData.some(function (s) {
    return s === null || s === void 0 ? void 0 : s.isLoading;
  });
  var abortController = (0, _react.useRef)(null);
  var abort = function abort() {
    var _abortController$curr;
    return (_abortController$curr = abortController.current) === null || _abortController$curr === void 0 ? void 0 : _abortController$curr.abort();
  };
  var createScreenshots = /*#__PURE__*/function () {
    var _ref2 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(prompt, attachments) {
      var onGenerate, onError, promises, results, isAllFailed;
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            abortController.current = new AbortController();
            onGenerate = function onGenerate(screenshot) {
              setScreenshots(function (prev) {
                var updatedData = (0, _toConsumableArray2.default)(prev);
                var pendingIndex = updatedData.indexOf(PENDING_VALUE);
                updatedData[pendingIndex] = screenshot;
                return updatedData;
              });
              return true;
            };
            onError = function onError() {
              setScreenshots(function (prev) {
                var updatedData = (0, _toConsumableArray2.default)(prev);
                var pendingIndex = updatedData.lastIndexOf(PENDING_VALUE);
                updatedData[pendingIndex] = {
                  isError: true
                };
                return updatedData;
              });
              return false;
            };
            promises = screenshotsData.map(function (_ref3) {
              var generate = _ref3.generate;
              var prevGeneratedIds = screenshots.map(function (screenshot) {
                return screenshot.baseTemplateId || '';
              });
              var requestBody = {
                prompt: prompt,
                prevGeneratedIds: prevGeneratedIds,
                currentContext: currentContext,
                ids: {
                  editorSessionId: editorSessionId.current,
                  sessionId: sessionId.current,
                  generateId: generateIdRef.current,
                  batchId: batchId.current,
                  requestId: setRequest().current
                },
                attachments: attachments.map(function (_ref4) {
                  var type = _ref4.type,
                    content = _ref4.content,
                    label = _ref4.label,
                    source = _ref4.source;
                  // Send only the data that is needed for the generation.
                  return {
                    type: type,
                    content: content,
                    label: label,
                    source: source
                  };
                })
              };
              return generate(requestBody, abortController.current.signal).then(onGenerate).catch(onError);
            });
            _context.next = 1;
            return Promise.all(promises);
          case 1:
            results = _context.sent;
            isAllFailed = results.every(function (value) {
              return false === value;
            });
            if (isAllFailed) {
              setScreenshots(function (prev) {
                var updatedData = (0, _toConsumableArray2.default)(prev);
                updatedData.splice(screenshotsGroupCount * -1);
                return updatedData;
              });
            }
          case 2:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function createScreenshots(_x, _x2) {
      return _ref2.apply(this, arguments);
    };
  }();
  var generate = function generate(prompt, attachments) {
    var placeholders = Array(screenshotsGroupCount).fill(PENDING_VALUE);
    generateIdRef.current = setGenerate().current;
    setScreenshots(placeholders);
    createScreenshots(prompt, attachments);
  };
  var regenerate = function regenerate(prompt, attachments) {
    var placeholders = Array(screenshotsGroupCount).fill(PENDING_VALUE);
    setScreenshots(function (prev) {
      return [].concat((0, _toConsumableArray2.default)(prev), (0, _toConsumableArray2.default)(placeholders));
    });
    createScreenshots(prompt, attachments);
  };
  return {
    generate: generate,
    regenerate: regenerate,
    screenshots: screenshots,
    isLoading: isLoading,
    error: error,
    abort: abort
  };
};
var _default = exports["default"] = useScreenshots;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/hooks/use-slider.js":
/*!****************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/hooks/use-slider.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.SCREENSHOTS_PER_PAGE = exports.MAX_PAGES = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
var SCREENSHOTS_PER_PAGE = exports.SCREENSHOTS_PER_PAGE = 3;
var MAX_PAGES = exports.MAX_PAGES = 5;
var useSlider = function useSlider() {
  var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
    _ref$slidesCount = _ref.slidesCount,
    slidesCount = _ref$slidesCount === void 0 ? 0 : _ref$slidesCount,
    _ref$slidesPerPage = _ref.slidesPerPage,
    slidesPerPage = _ref$slidesPerPage === void 0 ? SCREENSHOTS_PER_PAGE : _ref$slidesPerPage,
    _ref$gapPercentage = _ref.gapPercentage,
    gapPercentage = _ref$gapPercentage === void 0 ? 2 : _ref$gapPercentage;
  var _useState = (0, _react.useState)(1),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    currentPage = _useState2[0],
    setCurrentPage = _useState2[1];
  var gapsCount = slidesPerPage - 1;
  var slideWidthPercentage = (100 - gapPercentage * gapsCount) / slidesPerPage;
  var offsetXPercentage = (slideWidthPercentage + gapPercentage) * slidesPerPage * (currentPage - 1) * -1;
  var pagesCount = Math.ceil(slidesCount / slidesPerPage);
  (0, _react.useEffect)(function () {
    // In cases when the slidesCount value was reduced, we need to navigate to the last page.
    if (currentPage > 1 && currentPage > pagesCount) {
      setCurrentPage(pagesCount);
    }
  }, [pagesCount]);
  return {
    currentPage: currentPage,
    setCurrentPage: setCurrentPage,
    pagesCount: pagesCount,
    slidesPerPage: slidesPerPage,
    gapPercentage: gapPercentage,
    offsetXPercentage: offsetXPercentage,
    slideWidthPercentage: slideWidthPercentage
  };
};
var _default = exports["default"] = useSlider;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-layout/index.js":
/*!*****************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-layout/index.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _promptErrorMessage = _interopRequireDefault(__webpack_require__(/*! ../../components/prompt-error-message */ "../modules/ai/assets/js/editor/components/prompt-error-message.js"));
var _unsavedChangesAlert = _interopRequireDefault(__webpack_require__(/*! ./components/unsaved-changes-alert */ "../modules/ai/assets/js/editor/pages/form-layout/components/unsaved-changes-alert.js"));
var _layoutDialog = _interopRequireDefault(__webpack_require__(/*! ./components/layout-dialog */ "../modules/ai/assets/js/editor/pages/form-layout/components/layout-dialog.js"));
var _promptForm = _interopRequireDefault(__webpack_require__(/*! ./components/prompt-form */ "../modules/ai/assets/js/editor/pages/form-layout/components/prompt-form.js"));
var _refreshIcon = _interopRequireDefault(__webpack_require__(/*! ../../icons/refresh-icon */ "../modules/ai/assets/js/editor/icons/refresh-icon.js"));
var _screenshot = _interopRequireDefault(__webpack_require__(/*! ./components/screenshot */ "../modules/ai/assets/js/editor/pages/form-layout/components/screenshot.js"));
var _useScreenshots2 = _interopRequireDefault(__webpack_require__(/*! ./hooks/use-screenshots */ "../modules/ai/assets/js/editor/pages/form-layout/hooks/use-screenshots.js"));
var _useSlider2 = _interopRequireWildcard(__webpack_require__(/*! ./hooks/use-slider */ "../modules/ai/assets/js/editor/pages/form-layout/hooks/use-slider.js"));
var _minimizeDiagonalIcon = _interopRequireDefault(__webpack_require__(/*! ../../icons/minimize-diagonal-icon */ "../modules/ai/assets/js/editor/icons/minimize-diagonal-icon.js"));
var _expandDiagonalIcon = _interopRequireDefault(__webpack_require__(/*! ../../icons/expand-diagonal-icon */ "../modules/ai/assets/js/editor/icons/expand-diagonal-icon.js"));
var _config = __webpack_require__(/*! ./context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var _attachment = __webpack_require__(/*! ../../types/attachment */ "../modules/ai/assets/js/editor/types/attachment.js");
var _promptPowerNotice = __webpack_require__(/*! ./components/attachments/prompt-power-notice */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/prompt-power-notice.js");
var _attachments = __webpack_require__(/*! ./components/attachments */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js");
var _attachDialog = _interopRequireDefault(__webpack_require__(/*! ./components/attachments/attach-dialog */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments/attach-dialog.js"));
var _isURL = _interopRequireDefault(__webpack_require__(/*! validator/lib/isURL */ "../node_modules/validator/lib/isURL.js"));
var _voicePromotionAlert = __webpack_require__(/*! ../../components/voice-promotion-alert */ "../modules/ai/assets/js/editor/components/voice-promotion-alert.js");
var _excluded = ["children"];
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var DirectionalMinimizeDiagonalIcon = (0, _ui.withDirection)(_minimizeDiagonalIcon.default);
var DirectionalExpandDiagonalIcon = (0, _ui.withDirection)(_expandDiagonalIcon.default);

/**
 * @typedef {Object} Attachment
 * @property {('json')} type        - The type of the attachment, currently only `json` is supported.
 * @property {string}   previewHTML - HTML content as a string, representing a preview.
 * @property {string}   content     - Actual content of the attachment as a string.
 * @property {string}   label       - Label for the attachment.
 */

var RegenerateButton = function RegenerateButton(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.Button, (0, _extends2.default)({
    size: "small",
    color: "secondary",
    startIcon: /*#__PURE__*/_react.default.createElement(_refreshIcon.default, null)
  }, props), (0, _i18n.__)('Regenerate', 'elementor'));
};
var UseLayoutButton = function UseLayoutButton(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.Button, (0, _extends2.default)({
    size: "small",
    variant: "contained"
  }, props), (0, _i18n.__)('Use Layout', 'elementor'));
};
UseLayoutButton.propTypes = {
  sx: _propTypes.default.object
};
var isRegenerateButtonDisabled = function isRegenerateButtonDisabled(screenshots, isLoading, isPromptFormActive) {
  if (isLoading || isPromptFormActive) {
    return true;
  }
  return screenshots.length >= _useSlider2.SCREENSHOTS_PER_PAGE * _useSlider2.MAX_PAGES;
};
var FormLayout = function FormLayout(_ref) {
  var _screenshots$selected, _screenshots$2;
  var _ref$DialogHeaderProp = _ref.DialogHeaderProps,
    DialogHeaderProps = _ref$DialogHeaderProp === void 0 ? {} : _ref$DialogHeaderProp,
    _ref$DialogContentPro = _ref.DialogContentProps,
    DialogContentProps = _ref$DialogContentPro === void 0 ? {} : _ref$DialogContentPro,
    initialAttachments = _ref.attachments;
  var _useConfig = (0, _config.useConfig)(),
    attachmentsTypes = _useConfig.attachmentsTypes,
    onData = _useConfig.onData,
    onInsert = _useConfig.onInsert,
    onSelect = _useConfig.onSelect,
    onClose = _useConfig.onClose,
    onGenerate = _useConfig.onGenerate;
  var _useScreenshots = (0, _useScreenshots2.default)({
      onData: onData
    }),
    screenshots = _useScreenshots.screenshots,
    generate = _useScreenshots.generate,
    regenerate = _useScreenshots.regenerate,
    isLoading = _useScreenshots.isLoading,
    error = _useScreenshots.error,
    abort = _useScreenshots.abort;
  var screenshotOutlineOffset = '2px';
  var _useSlider = (0, _useSlider2.default)({
      slidesCount: screenshots.length
    }),
    currentPage = _useSlider.currentPage,
    setCurrentPage = _useSlider.setCurrentPage,
    pagesCount = _useSlider.pagesCount,
    gapPercentage = _useSlider.gapPercentage,
    slidesPerPage = _useSlider.slidesPerPage,
    offsetXPercentage = _useSlider.offsetXPercentage,
    slideWidthPercentage = _useSlider.slideWidthPercentage;
  var _useState = (0, _react.useState)(-1),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    selectedScreenshotIndex = _useState2[0],
    setSelectedScreenshotIndex = _useState2[1];
  var _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    showUnsavedChangesAlert = _useState4[0],
    setShowUnsavedChangesAlert = _useState4[1];
  var _useState5 = (0, _react.useState)(true),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    isPromptEditable = _useState6[0],
    setIsPromptEditable = _useState6[1];
  var _useState7 = (0, _react.useState)([]),
    _useState8 = (0, _slicedToArray2.default)(_useState7, 2),
    attachments = _useState8[0],
    setAttachments = _useState8[1];
  var _useState9 = (0, _react.useState)(false),
    _useState0 = (0, _slicedToArray2.default)(_useState9, 2),
    shouldRenderWebApp = _useState0[0],
    setShouldRenderWebApp = _useState0[1];
  var _useState1 = (0, _react.useState)(false),
    _useState10 = (0, _slicedToArray2.default)(_useState1, 2),
    isMinimized = _useState10[0],
    setIsMinimized = _useState10[1];
  var lastRun = (0, _react.useRef)(function () {});
  var promptInputRef = (0, _react.useRef)(null);
  var selectedTemplate = (_screenshots$selected = screenshots[selectedScreenshotIndex]) === null || _screenshots$selected === void 0 ? void 0 : _screenshots$selected.template;
  var dialogContentChildren = DialogContentProps.children,
    dialogContentProps = (0, _objectWithoutProperties2.default)(DialogContentProps, _excluded);

  // When there are no screenshots the prompt field should be editable.
  var shouldFallbackToEditPrompt = !!(error && 0 === screenshots.length);
  var isPromptFormActive = isPromptEditable || shouldFallbackToEditPrompt;
  var abortAndClose = function abortAndClose() {
    abort();
    onClose();
  };
  var onCloseIntent = function onCloseIntent() {
    var hasUnsavedChanges = promptInputRef.current.value.trim() !== '' || screenshots.length > 0;
    if (hasUnsavedChanges) {
      return setShowUnsavedChangesAlert(true);
    }
    abortAndClose();
  };
  var handleGenerate = function handleGenerate(event, prompt) {
    event.preventDefault();
    if ('' === prompt.trim() && 0 === attachments.length) {
      return;
    }
    if ((0, _isURL.default)(prompt)) {
      setShouldRenderWebApp(true);
      return;
    }
    onGenerate();
    lastRun.current = function () {
      setSelectedScreenshotIndex(-1);
      generate(prompt, attachments);
    };
    lastRun.current();
    setIsPromptEditable(false);
    setCurrentPage(1);
  };
  var handleRegenerate = function handleRegenerate() {
    lastRun.current = function () {
      regenerate(promptInputRef.current.value, attachments);
      // Changing the current page to the next page number.
      setCurrentPage(pagesCount + 1);
    };
    lastRun.current();
  };
  var applyTemplate = function applyTemplate() {
    onInsert(selectedTemplate);
    screenshots[selectedScreenshotIndex].sendUsageData();
    abortAndClose();
  };
  var handleScreenshotClick = function handleScreenshotClick(index, template) {
    return function () {
      if (isPromptFormActive) {
        return;
      }
      setSelectedScreenshotIndex(index);
      onSelect(template);
    };
  };

  /**
   * @param {Attachment[]} items
   */
  var onAttach = function onAttach(items) {
    items.forEach(function (item) {
      if (!attachmentsTypes[item.type]) {
        throw new Error("Invalid attachment type: ".concat(item.type));
      }
      var typeConfig = attachmentsTypes[item.type];
      if (!item.previewHTML && typeConfig.previewGenerator) {
        typeConfig.previewGenerator(item.content).then(function (html) {
          item.previewHTML = html;
          setAttachments(function (prev) {
            // Replace the attachment with the updated one.
            return prev.map(function (attachment) {
              if (attachment.content === item.content) {
                return item;
              }
              return attachment;
            });
          });
        });
      }
    });
    setAttachments(items);
    setShouldRenderWebApp(false);
    setIsPromptEditable(true);
  };
  (0, _react.useEffect)(function () {
    var _screenshots$;
    var isFirstTemplateExist = (_screenshots$ = screenshots[0]) === null || _screenshots$ === void 0 ? void 0 : _screenshots$.template;
    if (isFirstTemplateExist) {
      onSelect(screenshots[0].template);
      setSelectedScreenshotIndex(0);
    }
  }, [(_screenshots$2 = screenshots[0]) === null || _screenshots$2 === void 0 ? void 0 : _screenshots$2.template]);
  (0, _react.useEffect)(function () {
    if (initialAttachments !== null && initialAttachments !== void 0 && initialAttachments.length) {
      onAttach(initialAttachments);
    }
  }, []);
  return /*#__PURE__*/_react.default.createElement(_layoutDialog.default, {
    onClose: onCloseIntent
  }, /*#__PURE__*/_react.default.createElement(_layoutDialog.default.Header, (0, _extends2.default)({
    onClose: onCloseIntent
  }, DialogHeaderProps), DialogHeaderProps.children, /*#__PURE__*/_react.default.createElement(_ui.Tooltip, {
    title: isMinimized ? (0, _i18n.__)('Expand', 'elementor') : (0, _i18n.__)('Minimize', 'elementor')
  }, /*#__PURE__*/_react.default.createElement(_ui.IconButton, {
    size: "small",
    "aria-label": "minimize",
    onClick: function onClick() {
      return setIsMinimized(function (prev) {
        return !prev;
      });
    }
  }, isMinimized ? /*#__PURE__*/_react.default.createElement(DirectionalExpandDiagonalIcon, null) : /*#__PURE__*/_react.default.createElement(DirectionalMinimizeDiagonalIcon, null)))), /*#__PURE__*/_react.default.createElement(_layoutDialog.default.Content, (0, _extends2.default)({
    dividers: true
  }, dialogContentProps), /*#__PURE__*/_react.default.createElement(_ui.Collapse, {
    in: !isMinimized
  }, dialogContentChildren && /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      pt: 2,
      px: 2,
      pb: 0
    }
  }, dialogContentChildren), attachments.length > 0 && /*#__PURE__*/_react.default.createElement(_promptPowerNotice.PromptPowerNotice, null), error && /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      pt: 2,
      px: 2,
      pb: 0
    }
  }, /*#__PURE__*/_react.default.createElement(_promptErrorMessage.default, {
    error: error,
    onRetry: lastRun.current
  })), showUnsavedChangesAlert && /*#__PURE__*/_react.default.createElement(_unsavedChangesAlert.default, {
    open: showUnsavedChangesAlert,
    title: (0, _i18n.__)('Leave Elementor AI?', 'elementor'),
    text: (0, _i18n.__)("Your progress will be deleted, and can't be recovered.", 'elementor'),
    onClose: abortAndClose,
    onCancel: function onCancel() {
      return setShowUnsavedChangesAlert(false);
    }
  }), shouldRenderWebApp && /*#__PURE__*/_react.default.createElement(_attachDialog.default, {
    type: _attachments.ATTACHMENT_TYPE_URL,
    url: promptInputRef.current.value,
    onAttach: onAttach,
    onClose: function onClose() {
      setShouldRenderWebApp(false);
    }
  }), /*#__PURE__*/_react.default.createElement(_promptForm.default, {
    shouldResetPrompt: shouldRenderWebApp,
    ref: promptInputRef,
    isActive: isPromptFormActive,
    isLoading: isLoading,
    showActions: screenshots.length > 0 || isLoading,
    attachmentsTypes: attachmentsTypes,
    attachments: attachments,
    onAttach: onAttach,
    onDetach: function onDetach(index) {
      setAttachments(function (prev) {
        var newAttachments = (0, _toConsumableArray2.default)(prev);
        newAttachments.splice(index, 1);
        return newAttachments;
      });
      setIsPromptEditable(true);
    },
    onSubmit: handleGenerate,
    onBack: function onBack() {
      return setIsPromptEditable(false);
    },
    onEdit: function onEdit() {
      return setIsPromptEditable(true);
    }
  }), (screenshots.length > 0 || isLoading) && /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(_ui.Divider, null), /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      p: 1.5
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      overflow: 'hidden',
      p: 0.5
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      display: 'flex',
      transition: 'all 0.4s ease',
      gap: "".concat(gapPercentage, "%"),
      transform: "translateX(".concat(offsetXPercentage, "%)")
    }
  }, screenshots.map(function (_ref2, index) {
    var screenshot = _ref2.screenshot,
      type = _ref2.type,
      template = _ref2.template,
      isError = _ref2.isError,
      isPending = _ref2.isPending;
    return /*#__PURE__*/_react.default.createElement(_screenshot.default, {
      key: index,
      url: screenshot,
      type: type,
      disabled: isPromptFormActive,
      isPlaceholder: isError,
      isLoading: isPending,
      isSelected: selectedScreenshotIndex === index,
      onClick: handleScreenshotClick(index, template),
      outlineOffset: screenshotOutlineOffset,
      sx: {
        flex: "0 0 ".concat(slideWidthPercentage, "%")
      }
    });
  }))), /*#__PURE__*/_react.default.createElement(_voicePromotionAlert.VoicePromotionAlert, {
    introductionKey: "ai-context-layout-promotion"
  })), screenshots.length > 0 && /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      pt: 0,
      px: 2,
      pb: 2
    },
    display: "grid",
    gridTemplateColumns: "repeat(3, 1fr)",
    justifyItems: "center"
  }, /*#__PURE__*/_react.default.createElement(RegenerateButton, {
    onClick: handleRegenerate,
    disabled: isRegenerateButtonDisabled(screenshots, isLoading, isPromptFormActive),
    sx: {
      justifySelf: 'start'
    }
  }), screenshots.length > slidesPerPage && /*#__PURE__*/_react.default.createElement(_ui.Pagination, {
    page: currentPage,
    count: pagesCount,
    disabled: isPromptFormActive,
    onChange: function onChange(_, page) {
      return setCurrentPage(page);
    }
  }), /*#__PURE__*/_react.default.createElement(UseLayoutButton, {
    onClick: applyTemplate,
    disabled: isPromptFormActive || -1 === selectedScreenshotIndex,
    sx: {
      justifySelf: 'end',
      gridColumn: 3
    }
  }))))));
};
FormLayout.propTypes = {
  DialogHeaderProps: _propTypes.default.object,
  DialogContentProps: _propTypes.default.object,
  attachments: _propTypes.default.arrayOf(_attachment.AttachmentPropType)
};
var _default = exports["default"] = FormLayout;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-media/components/enhance-button.js":
/*!************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-media/components/enhance-button.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _wandIcon = _interopRequireDefault(__webpack_require__(/*! ../../../icons/wand-icon */ "../modules/ai/assets/js/editor/icons/wand-icon.js"));
var _excluded = ["isLoading"];
var StyledWandIcon = (0, _ui.withDirection)(_wandIcon.default);
var EnhanceButton = function EnhanceButton(_ref) {
  var isLoading = _ref.isLoading,
    props = (0, _objectWithoutProperties2.default)(_ref, _excluded);
  return /*#__PURE__*/_react.default.createElement(_ui.Tooltip, {
    title: (0, _i18n.__)('Enhance prompt', 'elementor')
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    component: "span",
    sx: {
      cursor: props.disabled ? 'default' : 'pointer'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.IconButton, (0, _extends2.default)({
    size: "small",
    color: "secondary"
  }, props), isLoading ? /*#__PURE__*/_react.default.createElement(_ui.CircularProgress, {
    color: "secondary",
    size: 20
  }) : /*#__PURE__*/_react.default.createElement(StyledWandIcon, {
    fontSize: "small"
  }))));
};
EnhanceButton.propTypes = {
  disabled: _propTypes.default.bool,
  isLoading: _propTypes.default.bool
};
var _default = exports["default"] = EnhanceButton;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/form-media/components/generate-submit.js":
/*!*************************************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/form-media/components/generate-submit.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var GenerateSubmit = function GenerateSubmit(props) {
  return /*#__PURE__*/_react.default.createElement(_ui.Button, (0, _extends2.default)({
    fullWidth: true,
    size: "medium",
    type: "submit",
    variant: "contained"
  }, props), props.children || (0, _i18n.__)('Generate', 'elementor'));
};
GenerateSubmit.propTypes = {
  children: _propTypes.default.node
};
var _default = exports["default"] = GenerateSubmit;

/***/ }),

/***/ "../modules/ai/assets/js/editor/pages/get-started/index.js":
/*!*****************************************************************!*\
  !*** ../modules/ai/assets/js/editor/pages/get-started/index.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _api = __webpack_require__(/*! ../../api */ "../modules/ai/assets/js/editor/api/index.js");
var _icons = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var GetStarted = function GetStarted(_ref) {
  var onSuccess = _ref.onSuccess;
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isTermsChecked = _useState2[0],
    setIsTermsChecked = _useState2[1];
  var onGetStartedClick = /*#__PURE__*/function () {
    var _ref2 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            _context.next = 1;
            return (0, _api.setGetStarted)();
          case 1:
            onSuccess();
          case 2:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function onGetStartedClick() {
      return _ref2.apply(this, arguments);
    };
  }();
  return /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    alignItems: "center",
    gap: 1.5
  }, /*#__PURE__*/_react.default.createElement(_icons.AIIcon, {
    sx: {
      color: 'text.primary',
      fontSize: '60px',
      mb: 1
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "h4",
    sx: {
      color: 'text.primary'
    }
  }, (0, _i18n.__)('Step into the future with Elementor AI', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2"
  }, (0, _i18n.__)('Create smarter with AI text and code generators built right into the editor.', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Stack, {
    direction: "row",
    gap: 1.5,
    alignItems: "flex-start"
  }, /*#__PURE__*/_react.default.createElement(_ui.Checkbox, {
    id: "e-ai-terms-approval",
    color: "secondary",
    checked: isTermsChecked,
    onClick: function onClick() {
      return setIsTermsChecked(function (prevState) {
        return !prevState;
      });
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.Stack, null, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "caption",
    sx: {
      maxWidth: 520
    },
    component: "label",
    htmlFor: "e-ai-terms-approval"
  }, (0, _i18n.__)('I approve the ', 'elementor'), /*#__PURE__*/_react.default.createElement(_ui.Link, {
    href: "https://go.elementor.com/ai-terms/",
    target: "_blank",
    color: "info.main"
  }, (0, _i18n.__)('Terms of Service', 'elementor')), ' & ', /*#__PURE__*/_react.default.createElement(_ui.Link, {
    href: "https://go.elementor.com/ai-privacy-policy/",
    target: "_blank",
    color: "info.main"
  }, (0, _i18n.__)('Privacy Policy', 'elementor')), (0, _i18n.__)(' of the Elementor AI service.', 'elementor'), /*#__PURE__*/_react.default.createElement("br", null), (0, _i18n.__)('This includes consenting to the collection and use of data to improve user experience.', 'elementor')))), /*#__PURE__*/_react.default.createElement(_ui.Button, {
    disabled: !isTermsChecked,
    variant: "contained",
    onClick: onGetStartedClick,
    sx: {
      mt: 1,
      '&:hover': {
        color: 'primary.contrastText'
      }
    }
  }, (0, _i18n.__)('Get Started', 'elementor')));
};
GetStarted.propTypes = {
  onSuccess: _propTypes.default.func.isRequired
};
var _default = exports["default"] = GetStarted;

/***/ }),

/***/ "../modules/ai/assets/js/editor/types/attachment.js":
/*!**********************************************************!*\
  !*** ../modules/ai/assets/js/editor/types/attachment.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.AttachmentsTypesPropType = exports.AttachmentPropType = void 0;
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var AttachmentPropType = exports.AttachmentPropType = _propTypes.default.shape({
  type: _propTypes.default.string,
  previewHTML: _propTypes.default.string,
  content: _propTypes.default.string,
  label: _propTypes.default.string,
  source: _propTypes.default.string
});
var AttachmentsTypesPropType = exports.AttachmentsTypesPropType = _propTypes.default.shape({
  type: _propTypes.default.shape({
    promptPlaceholder: _propTypes.default.string,
    promptSuggestions: _propTypes.default.arrayOf(_propTypes.default.shape({
      text: _propTypes.default.string.isRequired
    })),
    previewGenerator: _propTypes.default.func
  })
});

/***/ }),

/***/ "../modules/ai/assets/js/editor/utils/editor-integration.js":
/*!******************************************************************!*\
  !*** ../modules/ai/assets/js/editor/utils/editor-integration.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.renderLayoutApp = exports.openPanel = exports.onConnect = exports.importToEditor = exports.getUiConfig = exports.closePanel = exports.WEB_BASED_PROMPTS = exports.VARIATIONS_PROMPTS = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _react2 = _interopRequireDefault(__webpack_require__(/*! elementor-utils/react */ "../assets/dev/js/utils/react.js"));
var _previewContainer = __webpack_require__(/*! ./preview-container */ "../modules/ai/assets/js/editor/utils/preview-container.js");
var _layoutApp = _interopRequireDefault(__webpack_require__(/*! ../layout-app */ "../modules/ai/assets/js/editor/layout-app.js"));
var _screenshot = __webpack_require__(/*! ./screenshot */ "../modules/ai/assets/js/editor/utils/screenshot.js");
var _history = __webpack_require__(/*! ./history */ "../modules/ai/assets/js/editor/utils/history.js");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _layoutAppWrapper = _interopRequireDefault(__webpack_require__(/*! ../layout-app-wrapper */ "../modules/ai/assets/js/editor/layout-app-wrapper.js"));
var _requestsIds = __webpack_require__(/*! ../context/requests-ids */ "../modules/ai/assets/js/editor/context/requests-ids.js");
var closePanel = exports.closePanel = function closePanel() {
  $e.run('panel/close');
  $e.components.get('panel').blockUserInteractions();
};
var openPanel = exports.openPanel = function openPanel() {
  $e.run('panel/open');
  $e.components.get('panel').unblockUserInteractions();
};
var onConnect = exports.onConnect = function onConnect(data) {
  elementorCommon.config.library_connect.is_connected = true;
  elementorCommon.config.library_connect.current_access_level = data.kits_access_level || data.access_level || 0;
  elementorCommon.config.library_connect.current_access_tier = data.access_tier;
  elementorCommon.config.library_connect.plan_type = data.plan_type;
};
var getUiConfig = exports.getUiConfig = function getUiConfig() {
  var _elementor, _elementor$getPrefere;
  var colorScheme = ((_elementor = elementor) === null || _elementor === void 0 || (_elementor$getPrefere = _elementor.getPreferences) === null || _elementor$getPrefere === void 0 ? void 0 : _elementor$getPrefere.call(_elementor, 'ui_theme')) || 'auto';
  var isRTL = elementorCommon.config.isRTL;
  return {
    colorScheme: colorScheme,
    isRTL: isRTL
  };
};
var VARIATIONS_PROMPTS = exports.VARIATIONS_PROMPTS = [{
  text: (0, _i18n.__)('Minimalist design with bold typography about', 'elementor')
}, {
  text: (0, _i18n.__)('Elegant style with serif fonts discussing', 'elementor')
}, {
  text: (0, _i18n.__)('Retro vibe with muted colors and classic fonts about', 'elementor')
}, {
  text: (0, _i18n.__)('Futuristic design with neon accents about', 'elementor')
}, {
  text: (0, _i18n.__)('Professional look with clean lines for', 'elementor')
}, {
  text: (0, _i18n.__)('Earthy tones and organic shapes featuring', 'elementor')
}, {
  text: (0, _i18n.__)('Luxurious theme with rich colors discussing', 'elementor')
}, {
  text: (0, _i18n.__)('Tech-inspired style with modern fonts about', 'elementor')
}, {
  text: (0, _i18n.__)('Warm hues with comforting visuals about', 'elementor')
}];
var WEB_BASED_PROMPTS = exports.WEB_BASED_PROMPTS = [{
  text: (0, _i18n.__)('Change the content to be about [topic]', 'elementor')
}, {
  text: (0, _i18n.__)('Generate lorem ipsum placeholder text for all paragraphs', 'elementor')
}, {
  text: (0, _i18n.__)('Revise the content to focus on [topic] and then translate it into Spanish', 'elementor')
}, {
  text: (0, _i18n.__)('Shift the focus of the content to [topic] in order to showcase our company\'s mission and values', 'elementor')
}, {
  text: (0, _i18n.__)('Alter the content to provide helpful tips related to [topic]', 'elementor')
}, {
  text: (0, _i18n.__)('Adjust the content to include FAQs and answers for common inquiries about [topic]', 'elementor')
}];
var PROMPT_PLACEHOLDER = (0, _i18n.__)("Press '/' for suggestions or describe the changes you want to apply (optional)...", 'elementor');
var renderLayoutApp = exports.renderLayoutApp = function renderLayoutApp() {
  var _options$onRenderApp;
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
    parentContainer: null,
    mode: '',
    at: null,
    onClose: null,
    onGenerate: null,
    onInsert: null,
    onRenderApp: null,
    onSelect: null,
    attachments: []
  };
  closePanel();
  var previewContainer = (0, _previewContainer.createPreviewContainer)(options.parentContainer, {
    // Create the container at the "drag widget here" area position.
    at: options.at
  });
  var _getUiConfig = getUiConfig(),
    colorScheme = _getUiConfig.colorScheme,
    isRTL = _getUiConfig.isRTL;
  var rootElement = document.createElement('div');
  document.body.append(rootElement);
  var bodyStyle = window.elementorFrontend.elements.$window[0].getComputedStyle(window.elementorFrontend.elements.$body[0]);
  var _ReactUtils$render = _react2.default.render(/*#__PURE__*/_react.default.createElement(_layoutAppWrapper.default, {
      isRTL: isRTL,
      colorScheme: colorScheme
    }, /*#__PURE__*/_react.default.createElement(_layoutApp.default, {
      mode: options.mode,
      currentContext: {
        body: {
          backgroundColor: bodyStyle.backgroundColor,
          backgroundImage: bodyStyle.backgroundImage
        }
      },
      attachmentsTypes: {
        json: {
          promptSuggestions: VARIATIONS_PROMPTS,
          promptPlaceholder: PROMPT_PLACEHOLDER,
          previewGenerator: function () {
            var _previewGenerator = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(json) {
              var screenshot;
              return _regenerator.default.wrap(function (_context) {
                while (1) switch (_context.prev = _context.next) {
                  case 0:
                    _context.next = 1;
                    return (0, _screenshot.takeScreenshot)(json);
                  case 1:
                    screenshot = _context.sent;
                    return _context.abrupt("return", "<img src=\"".concat(screenshot, "\" />"));
                  case 2:
                  case "end":
                    return _context.stop();
                }
              }, _callee);
            }));
            function previewGenerator(_x) {
              return _previewGenerator.apply(this, arguments);
            }
            return previewGenerator;
          }()
        },
        url: {
          promptPlaceholder: PROMPT_PLACEHOLDER,
          promptSuggestions: WEB_BASED_PROMPTS
        }
      },
      attachments: options.attachments || [],
      onClose: function onClose() {
        var _options$onClose;
        previewContainer.destroy();
        (_options$onClose = options.onClose) === null || _options$onClose === void 0 || _options$onClose.call(options);
        unmount();
        rootElement.remove();
        openPanel();
      },
      onConnect: onConnect,
      onGenerate: function onGenerate() {
        var _options$onGenerate;
        (_options$onGenerate = options.onGenerate) === null || _options$onGenerate === void 0 || _options$onGenerate.call(options, {
          previewContainer: previewContainer
        });
      },
      onData: (/*#__PURE__*/function () {
        var _ref = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee2(template) {
          var screenshot;
          return _regenerator.default.wrap(function (_context2) {
            while (1) switch (_context2.prev = _context2.next) {
              case 0:
                _context2.next = 1;
                return (0, _screenshot.takeScreenshot)(template);
              case 1:
                screenshot = _context2.sent;
                return _context2.abrupt("return", {
                  screenshot: screenshot,
                  template: template
                });
              case 2:
              case "end":
                return _context2.stop();
            }
          }, _callee2);
        }));
        return function (_x2) {
          return _ref.apply(this, arguments);
        };
      }()),
      onSelect: function onSelect(template) {
        var _options$onSelect;
        (_options$onSelect = options.onSelect) === null || _options$onSelect === void 0 || _options$onSelect.call(options);
        previewContainer.setContent(template);
      },
      onInsert: options.onInsert,
      hasPro: elementor.helpers.hasPro()
    })), rootElement),
    unmount = _ReactUtils$render.unmount;
  (_options$onRenderApp = options.onRenderApp) === null || _options$onRenderApp === void 0 || _options$onRenderApp.call(options, {
    previewContainer: previewContainer
  });
};
var importToEditor = exports.importToEditor = function importToEditor(_ref2) {
  var parentContainer = _ref2.parentContainer,
    at = _ref2.at,
    template = _ref2.template,
    historyTitle = _ref2.historyTitle,
    _ref2$replace = _ref2.replace,
    replace = _ref2$replace === void 0 ? false : _ref2$replace;
  var endHistoryLog = (0, _history.startHistoryLog)({
    type: 'import',
    title: historyTitle
  });
  if (replace) {
    $e.run('document/elements/delete', {
      container: parentContainer.children.at(at)
    });
  }
  $e.run('document/elements/create', {
    container: parentContainer,
    model: (0, _requestsIds.generateIds)(template),
    options: {
      at: at,
      edit: true
    }
  });
  endHistoryLog();
};

/***/ }),

/***/ "../modules/ai/assets/js/editor/utils/history.js":
/*!*******************************************************!*\
  !*** ../modules/ai/assets/js/editor/utils/history.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.startHistoryLog = startHistoryLog;
exports.toggleHistory = toggleHistory;
function toggleHistory(isActive) {
  elementor.documents.getCurrent().history.setActive(isActive);
}

/**
 * @param {Object}                                                                                                                                                           options
 * @param { 'add' | 'change' | 'disable' | 'duplicate' | 'enable' | 'import' | 'move' | 'paste' | 'paste_style' | 'remove' | 'reset_settings' | 'reset_style' | 'selected' } options.type
 * @param { string }                                                                                                                                                         options.title
 *
 * @return {*}
 */
function startHistoryLog(_ref) {
  var type = _ref.type,
    title = _ref.title;
  var id = $e.internal('document/history/start-log', {
    type: type,
    title: title
  });
  return function () {
    return $e.internal('document/history/end-log', {
      id: id
    });
  };
}

/***/ }),

/***/ "../modules/ai/assets/js/editor/utils/preview-container.js":
/*!*****************************************************************!*\
  !*** ../modules/ai/assets/js/editor/utils/preview-container.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.createPreviewContainer = createPreviewContainer;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _history = __webpack_require__(/*! ./history */ "../modules/ai/assets/js/editor/utils/history.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * @typedef {import('elementor/assets/dev/js/editor/container/container')} Container
 */

var PREFIX = 'e-ai-preview-container';
var CLASS_HIDDEN = PREFIX + '--hidden';
var CLASS_IDLE = PREFIX + '--idle';

/**
 * @param {Container} parentContainer
 * @param {{}}        containerOptions
 * @return {{init, setContent, reset, destroy}}
 */

function createPreviewContainer(parentContainer) {
  var containerOptions = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var createdContainers = new Map();
  var idleContainer = createIdleContainer(parentContainer, containerOptions);
  function init() {
    showContainer(idleContainer);
  }
  function getAllContainers() {
    return [].concat((0, _toConsumableArray2.default)(createdContainers.values()), [idleContainer]);
  }
  function reset() {
    deleteContainers((0, _toConsumableArray2.default)(createdContainers.values()));
    createdContainers.clear();
    showContainer(idleContainer);
  }
  function setContent(template) {
    if (!template) {
      return;
    }
    hideContainers(getAllContainers());
    if (!createdContainers.has(template)) {
      var newContainer = createContainer(parentContainer, template, containerOptions);
      createdContainers.set(template, newContainer);
    }
    showContainer(createdContainers.get(template));
  }
  function destroy() {
    deleteContainers(getAllContainers());
    createdContainers.clear();
  }
  return {
    init: init,
    reset: reset,
    setContent: setContent,
    destroy: destroy
  };
}

/**
 * @param {Container} parentContainer
 * @param {{}}        model
 * @param {{}}        options
 * @return {*}
 */
function createContainer(parentContainer, model) {
  var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  (0, _history.toggleHistory)(false);
  var container = $e.run('document/elements/create', {
    container: parentContainer,
    model: _objectSpread(_objectSpread({}, model), {}, {
      id: "".concat(PREFIX, "-").concat(elementorCommon.helpers.getUniqueId().toString())
    }),
    options: _objectSpread(_objectSpread({}, options), {}, {
      edit: false
    })
  });
  (0, _history.toggleHistory)(true);
  container.view.$el.addClass(CLASS_HIDDEN);
  return container;
}

/**
 * @param {Container} parentContainer
 * @param {{}}        containerOptions
 * @return {*}
 */
function createIdleContainer(parentContainer) {
  var containerOptions = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  // Create an empty container that'll be used of UI purposes.
  var container = createContainer(parentContainer, {
    elType: 'container'
  }, containerOptions);
  container.view.$el.addClass(CLASS_IDLE);
  return container;
}
function hideContainers(containers) {
  containers.forEach(function (container) {
    container.view.$el.addClass(CLASS_HIDDEN);
  });
}
function showContainer(container) {
  container.view.$el.removeClass(CLASS_HIDDEN);

  // Delay the scroll to avoid UI jumps when toggling between containers.
  setTimeout(function () {
    container.view.$el[0].scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  });
}
function deleteContainers(containers) {
  (0, _history.toggleHistory)(false);
  $e.run('document/elements/delete', {
    containers: containers
  });
  (0, _history.toggleHistory)(true);
}

/***/ }),

/***/ "../modules/ai/assets/js/editor/utils/screenshot.js":
/*!**********************************************************!*\
  !*** ../modules/ai/assets/js/editor/utils/screenshot.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.takeScreenshot = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _htmlToImage = __webpack_require__(/*! html-to-image */ "../node_modules/html-to-image/es/index.js");
var _history = __webpack_require__(/*! ./history */ "../modules/ai/assets/js/editor/utils/history.js");
var _requestsIds = __webpack_require__(/*! ../context/requests-ids */ "../modules/ai/assets/js/editor/context/requests-ids.js");
var takeScreenshot = exports.takeScreenshot = /*#__PURE__*/function () {
  var _ref = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(template) {
    var hiddenWrapper, container, screenshot, _t;
    return _regenerator.default.wrap(function (_context) {
      while (1) switch (_context.prev = _context.next) {
        case 0:
          if (template) {
            _context.next = 1;
            break;
          }
          return _context.abrupt("return", '');
        case 1:
          // Disable history so the Editor won't show our hidden container as a user action.
          (0, _history.toggleHistory)(false);
          hiddenWrapper = createHiddenWrapper();
          container = createContainer(template);
          wrapContainer(container, hiddenWrapper);
          elementor.getPreviewView().$childViewContainer[0].appendChild(hiddenWrapper);

          // Wait for the container to render.
          _context.next = 2;
          return waitForContainer(container.id);
        case 2:
          if (!template.elements.length) {
            _context.next = 3;
            break;
          }
          _context.next = 3;
          return Promise.all(template.elements.map(function (child) {
            return waitForContainer(child.id);
          }));
        case 3:
          _context.prev = 3;
          _context.next = 4;
          return screenshotNode(container.view.$el[0]);
        case 4:
          screenshot = _context.sent;
          _context.next = 6;
          break;
        case 5:
          _context.prev = 5;
          _t = _context["catch"](3);
          // Return an empty image url if the screenshot failed.
          screenshot = '';
        case 6:
          deleteContainer(container);
          hiddenWrapper.remove();
          (0, _history.toggleHistory)(true);
          return _context.abrupt("return", screenshot);
        case 7:
        case "end":
          return _context.stop();
      }
    }, _callee, null, [[3, 5]]);
  }));
  return function takeScreenshot(_x) {
    return _ref.apply(this, arguments);
  };
}();
function screenshotNode(node) {
  return toWebp(node, {
    quality: 0.01,
    // Transparent 1x1 pixel.
    imagePlaceholder: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
  });
}
function toWebp(_x2) {
  return _toWebp.apply(this, arguments);
}
function _toWebp() {
  _toWebp = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee3(node) {
    var _options$quality;
    var options,
      canvas,
      _args3 = arguments;
    return _regenerator.default.wrap(function (_context3) {
      while (1) switch (_context3.prev = _context3.next) {
        case 0:
          options = _args3.length > 1 && _args3[1] !== undefined ? _args3[1] : {};
          _context3.next = 1;
          return (0, _htmlToImage.toCanvas)(node, options);
        case 1:
          canvas = _context3.sent;
          return _context3.abrupt("return", canvas.toDataURL('image/webp', (_options$quality = options.quality) !== null && _options$quality !== void 0 ? _options$quality : 1));
        case 2:
        case "end":
          return _context3.stop();
      }
    }, _callee3);
  }));
  return _toWebp.apply(this, arguments);
}
function createHiddenWrapper() {
  var wrapper = document.createElement('div');
  wrapper.style.position = 'fixed';
  wrapper.style.opacity = '0';
  wrapper.style.inset = '0';
  return wrapper;
}
function createContainer(template) {
  var model = (0, _requestsIds.generateIds)(template);

  // Set a custom ID, so it can be used later on in the backend.
  model.id = "e-ai-screenshot-container-".concat(model.id);
  return $e.run('document/elements/create', {
    container: elementor.getPreviewContainer(),
    model: model,
    options: {
      edit: false
    }
  });
}
function deleteContainer(container) {
  return $e.run('document/elements/delete', {
    container: container
  });
}
function waitForContainer(id) {
  var timeout = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 5000;
  var timeoutPromise = sleep(timeout);
  var waitPromise = new Promise(function (resolve) {
    elementorFrontend.hooks.addAction('frontend/element_ready/global', /*#__PURE__*/function () {
      var _ref2 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee2($element) {
        var images;
        return _regenerator.default.wrap(function (_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              if (!($element.data('id') === id)) {
                _context2.next = 2;
                break;
              }
              images = (0, _toConsumableArray2.default)($element[0].querySelectorAll('img')); // Wait for all images to load.
              _context2.next = 1;
              return Promise.all(images.map(waitForImage));
            case 1:
              resolve();
            case 2:
            case "end":
              return _context2.stop();
          }
        }, _callee2);
      }));
      return function (_x3) {
        return _ref2.apply(this, arguments);
      };
    }());
  });
  return Promise.any([timeoutPromise, waitPromise]);
}
function waitForImage(image) {
  if (image.complete) {
    return Promise.resolve();
  }
  return new Promise(function (resolve) {
    image.addEventListener('load', resolve);
    image.addEventListener('error', function () {
      // Remove the image to make sure it won't break the screenshot.
      image.remove();
      resolve();
    });
  });
}
function sleep(ms) {
  return new Promise(function (resolve) {
    return setTimeout(resolve, ms);
  });
}
function wrapContainer(container, wrapper) {
  var el = container.view.$el[0];
  el.parentNode.insertBefore(wrapper, el);
  wrapper.appendChild(el);
}

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/OverloadYield.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/OverloadYield.js ***!
  \***************************************************************/
/***/ ((module) => {

function _OverloadYield(e, d) {
  this.v = e, this.k = d;
}
module.exports = _OverloadYield, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/asyncToGenerator.js ***!
  \******************************************************************/
/***/ ((module) => {

function asyncGeneratorStep(n, t, e, r, o, a, c) {
  try {
    var i = n[a](c),
      u = i.value;
  } catch (n) {
    return void e(n);
  }
  i.done ? t(u) : Promise.resolve(u).then(r, o);
}
function _asyncToGenerator(n) {
  return function () {
    var t = this,
      e = arguments;
    return new Promise(function (r, o) {
      var a = n.apply(t, e);
      function _next(n) {
        asyncGeneratorStep(a, r, o, _next, _throw, "next", n);
      }
      function _throw(n) {
        asyncGeneratorStep(a, r, o, _next, _throw, "throw", n);
      }
      _next(void 0);
    });
  };
}
module.exports = _asyncToGenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/extends.js":
/*!*********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/extends.js ***!
  \*********************************************************/
/***/ ((module) => {

function _extends() {
  return module.exports = _extends = Object.assign ? Object.assign.bind() : function (n) {
    for (var e = 1; e < arguments.length; e++) {
      var t = arguments[e];
      for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]);
    }
    return n;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _extends.apply(null, arguments);
}
module.exports = _extends, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js":
/*!*************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/objectWithoutProperties.js ***!
  \*************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var objectWithoutPropertiesLoose = __webpack_require__(/*! ./objectWithoutPropertiesLoose.js */ "../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js");
function _objectWithoutProperties(e, t) {
  if (null == e) return {};
  var o,
    r,
    i = objectWithoutPropertiesLoose(e, t);
  if (Object.getOwnPropertySymbols) {
    var n = Object.getOwnPropertySymbols(e);
    for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]);
  }
  return i;
}
module.exports = _objectWithoutProperties, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js":
/*!******************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js ***!
  \******************************************************************************/
/***/ ((module) => {

function _objectWithoutPropertiesLoose(r, e) {
  if (null == r) return {};
  var t = {};
  for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
    if (-1 !== e.indexOf(n)) continue;
    t[n] = r[n];
  }
  return t;
}
module.exports = _objectWithoutPropertiesLoose, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/regenerator.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regenerator.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regeneratorDefine = __webpack_require__(/*! ./regeneratorDefine.js */ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js");
function _regenerator() {
  /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */
  var e,
    t,
    r = "function" == typeof Symbol ? Symbol : {},
    n = r.iterator || "@@iterator",
    o = r.toStringTag || "@@toStringTag";
  function i(r, n, o, i) {
    var c = n && n.prototype instanceof Generator ? n : Generator,
      u = Object.create(c.prototype);
    return regeneratorDefine(u, "_invoke", function (r, n, o) {
      var i,
        c,
        u,
        f = 0,
        p = o || [],
        y = !1,
        G = {
          p: 0,
          n: 0,
          v: e,
          a: d,
          f: d.bind(e, 4),
          d: function d(t, r) {
            return i = t, c = 0, u = e, G.n = r, a;
          }
        };
      function d(r, n) {
        for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) {
          var o,
            i = p[t],
            d = G.p,
            l = i[2];
          r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0));
        }
        if (o || r > 1) return a;
        throw y = !0, n;
      }
      return function (o, p, l) {
        if (f > 1) throw TypeError("Generator is already running");
        for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) {
          i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u);
          try {
            if (f = 2, i) {
              if (c || (o = "next"), t = i[o]) {
                if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object");
                if (!t.done) return t;
                u = t.value, c < 2 && (c = 0);
              } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1);
              i = e;
            } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break;
          } catch (t) {
            i = e, c = 1, u = t;
          } finally {
            f = 1;
          }
        }
        return {
          value: t,
          done: y
        };
      };
    }(r, o, i), !0), u;
  }
  var a = {};
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}
  t = Object.getPrototypeOf;
  var c = [][n] ? t(t([][n]())) : (regeneratorDefine(t = {}, n, function () {
      return this;
    }), t),
    u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c);
  function f(e) {
    return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, regeneratorDefine(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e;
  }
  return GeneratorFunction.prototype = GeneratorFunctionPrototype, regeneratorDefine(u, "constructor", GeneratorFunctionPrototype), regeneratorDefine(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", regeneratorDefine(GeneratorFunctionPrototype, o, "GeneratorFunction"), regeneratorDefine(u), regeneratorDefine(u, o, "Generator"), regeneratorDefine(u, n, function () {
    return this;
  }), regeneratorDefine(u, "toString", function () {
    return "[object Generator]";
  }), (module.exports = _regenerator = function _regenerator() {
    return {
      w: i,
      m: f
    };
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _regenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsync.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsync.js ***!
  \******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regeneratorAsyncGen = __webpack_require__(/*! ./regeneratorAsyncGen.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js");
function _regeneratorAsync(n, e, r, t, o) {
  var a = regeneratorAsyncGen(n, e, r, t, o);
  return a.next().then(function (n) {
    return n.done ? n.value : a.next();
  });
}
module.exports = _regeneratorAsync, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js":
/*!*********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regenerator = __webpack_require__(/*! ./regenerator.js */ "../node_modules/@babel/runtime/helpers/regenerator.js");
var regeneratorAsyncIterator = __webpack_require__(/*! ./regeneratorAsyncIterator.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js");
function _regeneratorAsyncGen(r, e, t, o, n) {
  return new regeneratorAsyncIterator(regenerator().w(r, e, t, o), n || Promise);
}
module.exports = _regeneratorAsyncGen, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js":
/*!**************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js ***!
  \**************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var OverloadYield = __webpack_require__(/*! ./OverloadYield.js */ "../node_modules/@babel/runtime/helpers/OverloadYield.js");
var regeneratorDefine = __webpack_require__(/*! ./regeneratorDefine.js */ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js");
function AsyncIterator(t, e) {
  function n(r, o, i, f) {
    try {
      var c = t[r](o),
        u = c.value;
      return u instanceof OverloadYield ? e.resolve(u.v).then(function (t) {
        n("next", t, i, f);
      }, function (t) {
        n("throw", t, i, f);
      }) : e.resolve(u).then(function (t) {
        c.value = t, i(c);
      }, function (t) {
        return n("throw", t, i, f);
      });
    } catch (t) {
      f(t);
    }
  }
  var r;
  this.next || (regeneratorDefine(AsyncIterator.prototype), regeneratorDefine(AsyncIterator.prototype, "function" == typeof Symbol && Symbol.asyncIterator || "@asyncIterator", function () {
    return this;
  })), regeneratorDefine(this, "_invoke", function (t, o, i) {
    function f() {
      return new e(function (e, r) {
        n(t, i, e, r);
      });
    }
    return r = r ? r.then(f, f) : f();
  }, !0);
}
module.exports = AsyncIterator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorDefine.js ***!
  \*******************************************************************/
/***/ ((module) => {

function _regeneratorDefine(e, r, n, t) {
  var i = Object.defineProperty;
  try {
    i({}, "", {});
  } catch (e) {
    i = 0;
  }
  module.exports = _regeneratorDefine = function regeneratorDefine(e, r, n, t) {
    if (r) i ? i(e, r, {
      value: n,
      enumerable: !t,
      configurable: !t,
      writable: !t
    }) : e[r] = n;else {
      var o = function o(r, n) {
        _regeneratorDefine(e, r, function (e) {
          return this._invoke(r, n, e);
        });
      };
      o("next", 0), o("throw", 1), o("return", 2);
    }
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _regeneratorDefine(e, r, n, t);
}
module.exports = _regeneratorDefine, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorKeys.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorKeys.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _regeneratorKeys(e) {
  var n = Object(e),
    r = [];
  for (var t in n) r.unshift(t);
  return function e() {
    for (; r.length;) if ((t = r.pop()) in n) return e.value = t, e.done = !1, e;
    return e.done = !0, e;
  };
}
module.exports = _regeneratorKeys, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorRuntime.js":
/*!********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorRuntime.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var OverloadYield = __webpack_require__(/*! ./OverloadYield.js */ "../node_modules/@babel/runtime/helpers/OverloadYield.js");
var regenerator = __webpack_require__(/*! ./regenerator.js */ "../node_modules/@babel/runtime/helpers/regenerator.js");
var regeneratorAsync = __webpack_require__(/*! ./regeneratorAsync.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsync.js");
var regeneratorAsyncGen = __webpack_require__(/*! ./regeneratorAsyncGen.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js");
var regeneratorAsyncIterator = __webpack_require__(/*! ./regeneratorAsyncIterator.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js");
var regeneratorKeys = __webpack_require__(/*! ./regeneratorKeys.js */ "../node_modules/@babel/runtime/helpers/regeneratorKeys.js");
var regeneratorValues = __webpack_require__(/*! ./regeneratorValues.js */ "../node_modules/@babel/runtime/helpers/regeneratorValues.js");
function _regeneratorRuntime() {
  "use strict";

  var r = regenerator(),
    e = r.m(_regeneratorRuntime),
    t = (Object.getPrototypeOf ? Object.getPrototypeOf(e) : e.__proto__).constructor;
  function n(r) {
    var e = "function" == typeof r && r.constructor;
    return !!e && (e === t || "GeneratorFunction" === (e.displayName || e.name));
  }
  var o = {
    "throw": 1,
    "return": 2,
    "break": 3,
    "continue": 3
  };
  function a(r) {
    var e, t;
    return function (n) {
      e || (e = {
        stop: function stop() {
          return t(n.a, 2);
        },
        "catch": function _catch() {
          return n.v;
        },
        abrupt: function abrupt(r, e) {
          return t(n.a, o[r], e);
        },
        delegateYield: function delegateYield(r, o, a) {
          return e.resultName = o, t(n.d, regeneratorValues(r), a);
        },
        finish: function finish(r) {
          return t(n.f, r);
        }
      }, t = function t(r, _t, o) {
        n.p = e.prev, n.n = e.next;
        try {
          return r(_t, o);
        } finally {
          e.next = n.n;
        }
      }), e.resultName && (e[e.resultName] = n.v, e.resultName = void 0), e.sent = n.v, e.next = n.n;
      try {
        return r.call(this, e);
      } finally {
        n.p = e.prev, n.n = e.next;
      }
    };
  }
  return (module.exports = _regeneratorRuntime = function _regeneratorRuntime() {
    return {
      wrap: function wrap(e, t, n, o) {
        return r.w(a(e), t, n, o && o.reverse());
      },
      isGeneratorFunction: n,
      mark: r.m,
      awrap: function awrap(r, e) {
        return new OverloadYield(r, e);
      },
      AsyncIterator: regeneratorAsyncIterator,
      async: function async(r, e, t, o, u) {
        return (n(e) ? regeneratorAsyncGen : regeneratorAsync)(a(r), e, t, o, u);
      },
      keys: regeneratorKeys,
      values: regeneratorValues
    };
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _regeneratorRuntime, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorValues.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorValues.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function _regeneratorValues(e) {
  if (null != e) {
    var t = e["function" == typeof Symbol && Symbol.iterator || "@@iterator"],
      r = 0;
    if (t) return t.call(e);
    if ("function" == typeof e.next) return e;
    if (!isNaN(e.length)) return {
      next: function next() {
        return e && r >= e.length && (e = void 0), {
          value: e && e[r++],
          done: !e
        };
      }
    };
  }
  throw new TypeError(_typeof(e) + " is not iterable");
}
module.exports = _regeneratorValues, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _taggedTemplateLiteral(e, t) {
  return t || (t = e.slice(0)), Object.freeze(Object.defineProperties(e, {
    raw: {
      value: Object.freeze(t)
    }
  }));
}
module.exports = _taggedTemplateLiteral, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/regenerator/index.js":
/*!***********************************************************!*\
  !*** ../node_modules/@babel/runtime/regenerator/index.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

// TODO(Babel 8): Remove this file.

var runtime = __webpack_require__(/*! ../helpers/regeneratorRuntime */ "../node_modules/@babel/runtime/helpers/regeneratorRuntime.js")();
module.exports = runtime;

// Copied from https://github.com/facebook/regenerator/blob/main/packages/runtime/runtime.js#L736=
try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  if (typeof globalThis === "object") {
    globalThis.regeneratorRuntime = runtime;
  } else {
    Function("r", "regeneratorRuntime = r")(runtime);
  }
}


/***/ }),

/***/ "../node_modules/html-to-image/es/apply-style.js":
/*!*******************************************************!*\
  !*** ../node_modules/html-to-image/es/apply-style.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   applyStyle: () => (/* binding */ applyStyle)
/* harmony export */ });
function applyStyle(node, options) {
    const { style } = node;
    if (options.backgroundColor) {
        style.backgroundColor = options.backgroundColor;
    }
    if (options.width) {
        style.width = `${options.width}px`;
    }
    if (options.height) {
        style.height = `${options.height}px`;
    }
    const manual = options.style;
    if (manual != null) {
        Object.keys(manual).forEach((key) => {
            style[key] = manual[key];
        });
    }
    return node;
}
//# sourceMappingURL=apply-style.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/clone-node.js":
/*!******************************************************!*\
  !*** ../node_modules/html-to-image/es/clone-node.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   cloneNode: () => (/* binding */ cloneNode)
/* harmony export */ });
/* harmony import */ var _clone_pseudos__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./clone-pseudos */ "../node_modules/html-to-image/es/clone-pseudos.js");
/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./util */ "../node_modules/html-to-image/es/util.js");
/* harmony import */ var _mimes__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./mimes */ "../node_modules/html-to-image/es/mimes.js");
/* harmony import */ var _dataurl__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./dataurl */ "../node_modules/html-to-image/es/dataurl.js");




async function cloneCanvasElement(canvas) {
    const dataURL = canvas.toDataURL();
    if (dataURL === 'data:,') {
        return canvas.cloneNode(false);
    }
    return (0,_util__WEBPACK_IMPORTED_MODULE_1__.createImage)(dataURL);
}
async function cloneVideoElement(video, options) {
    if (video.currentSrc) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = video.clientWidth;
        canvas.height = video.clientHeight;
        ctx === null || ctx === void 0 ? void 0 : ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const dataURL = canvas.toDataURL();
        return (0,_util__WEBPACK_IMPORTED_MODULE_1__.createImage)(dataURL);
    }
    const poster = video.poster;
    const contentType = (0,_mimes__WEBPACK_IMPORTED_MODULE_2__.getMimeType)(poster);
    const dataURL = await (0,_dataurl__WEBPACK_IMPORTED_MODULE_3__.resourceToDataURL)(poster, contentType, options);
    return (0,_util__WEBPACK_IMPORTED_MODULE_1__.createImage)(dataURL);
}
async function cloneIFrameElement(iframe, options) {
    var _a;
    try {
        if ((_a = iframe === null || iframe === void 0 ? void 0 : iframe.contentDocument) === null || _a === void 0 ? void 0 : _a.body) {
            return (await cloneNode(iframe.contentDocument.body, options, true));
        }
    }
    catch (_b) {
        // Failed to clone iframe
    }
    return iframe.cloneNode(false);
}
async function cloneSingleNode(node, options) {
    if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(node, HTMLCanvasElement)) {
        return cloneCanvasElement(node);
    }
    if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(node, HTMLVideoElement)) {
        return cloneVideoElement(node, options);
    }
    if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(node, HTMLIFrameElement)) {
        return cloneIFrameElement(node, options);
    }
    return node.cloneNode(isSVGElement(node));
}
const isSlotElement = (node) => node.tagName != null && node.tagName.toUpperCase() === 'SLOT';
const isSVGElement = (node) => node.tagName != null && node.tagName.toUpperCase() === 'SVG';
async function cloneChildren(nativeNode, clonedNode, options) {
    var _a, _b;
    if (isSVGElement(clonedNode)) {
        return clonedNode;
    }
    let children = [];
    if (isSlotElement(nativeNode) && nativeNode.assignedNodes) {
        children = (0,_util__WEBPACK_IMPORTED_MODULE_1__.toArray)(nativeNode.assignedNodes());
    }
    else if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(nativeNode, HTMLIFrameElement) &&
        ((_a = nativeNode.contentDocument) === null || _a === void 0 ? void 0 : _a.body)) {
        children = (0,_util__WEBPACK_IMPORTED_MODULE_1__.toArray)(nativeNode.contentDocument.body.childNodes);
    }
    else {
        children = (0,_util__WEBPACK_IMPORTED_MODULE_1__.toArray)(((_b = nativeNode.shadowRoot) !== null && _b !== void 0 ? _b : nativeNode).childNodes);
    }
    if (children.length === 0 ||
        (0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(nativeNode, HTMLVideoElement)) {
        return clonedNode;
    }
    await children.reduce((deferred, child) => deferred
        .then(() => cloneNode(child, options))
        .then((clonedChild) => {
        if (clonedChild) {
            clonedNode.appendChild(clonedChild);
        }
    }), Promise.resolve());
    return clonedNode;
}
function cloneCSSStyle(nativeNode, clonedNode, options) {
    const targetStyle = clonedNode.style;
    if (!targetStyle) {
        return;
    }
    const sourceStyle = window.getComputedStyle(nativeNode);
    if (sourceStyle.cssText) {
        targetStyle.cssText = sourceStyle.cssText;
        targetStyle.transformOrigin = sourceStyle.transformOrigin;
    }
    else {
        (0,_util__WEBPACK_IMPORTED_MODULE_1__.getStyleProperties)(options).forEach((name) => {
            let value = sourceStyle.getPropertyValue(name);
            if (name === 'font-size' && value.endsWith('px')) {
                const reducedFont = Math.floor(parseFloat(value.substring(0, value.length - 2))) - 0.1;
                value = `${reducedFont}px`;
            }
            if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(nativeNode, HTMLIFrameElement) &&
                name === 'display' &&
                value === 'inline') {
                value = 'block';
            }
            if (name === 'd' && clonedNode.getAttribute('d')) {
                value = `path(${clonedNode.getAttribute('d')})`;
            }
            targetStyle.setProperty(name, value, sourceStyle.getPropertyPriority(name));
        });
    }
}
function cloneInputValue(nativeNode, clonedNode) {
    if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(nativeNode, HTMLTextAreaElement)) {
        clonedNode.innerHTML = nativeNode.value;
    }
    if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(nativeNode, HTMLInputElement)) {
        clonedNode.setAttribute('value', nativeNode.value);
    }
}
function cloneSelectValue(nativeNode, clonedNode) {
    if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(nativeNode, HTMLSelectElement)) {
        const clonedSelect = clonedNode;
        const selectedOption = Array.from(clonedSelect.children).find((child) => nativeNode.value === child.getAttribute('value'));
        if (selectedOption) {
            selectedOption.setAttribute('selected', '');
        }
    }
}
function decorate(nativeNode, clonedNode, options) {
    if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(clonedNode, Element)) {
        cloneCSSStyle(nativeNode, clonedNode, options);
        (0,_clone_pseudos__WEBPACK_IMPORTED_MODULE_0__.clonePseudoElements)(nativeNode, clonedNode, options);
        cloneInputValue(nativeNode, clonedNode);
        cloneSelectValue(nativeNode, clonedNode);
    }
    return clonedNode;
}
async function ensureSVGSymbols(clone, options) {
    const uses = clone.querySelectorAll ? clone.querySelectorAll('use') : [];
    if (uses.length === 0) {
        return clone;
    }
    const processedDefs = {};
    for (let i = 0; i < uses.length; i++) {
        const use = uses[i];
        const id = use.getAttribute('xlink:href');
        if (id) {
            const exist = clone.querySelector(id);
            const definition = document.querySelector(id);
            if (!exist && definition && !processedDefs[id]) {
                // eslint-disable-next-line no-await-in-loop
                processedDefs[id] = (await cloneNode(definition, options, true));
            }
        }
    }
    const nodes = Object.values(processedDefs);
    if (nodes.length) {
        const ns = 'http://www.w3.org/1999/xhtml';
        const svg = document.createElementNS(ns, 'svg');
        svg.setAttribute('xmlns', ns);
        svg.style.position = 'absolute';
        svg.style.width = '0';
        svg.style.height = '0';
        svg.style.overflow = 'hidden';
        svg.style.display = 'none';
        const defs = document.createElementNS(ns, 'defs');
        svg.appendChild(defs);
        for (let i = 0; i < nodes.length; i++) {
            defs.appendChild(nodes[i]);
        }
        clone.appendChild(svg);
    }
    return clone;
}
async function cloneNode(node, options, isRoot) {
    if (!isRoot && options.filter && !options.filter(node)) {
        return null;
    }
    return Promise.resolve(node)
        .then((clonedNode) => cloneSingleNode(clonedNode, options))
        .then((clonedNode) => cloneChildren(node, clonedNode, options))
        .then((clonedNode) => decorate(node, clonedNode, options))
        .then((clonedNode) => ensureSVGSymbols(clonedNode, options));
}
//# sourceMappingURL=clone-node.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/clone-pseudos.js":
/*!*********************************************************!*\
  !*** ../node_modules/html-to-image/es/clone-pseudos.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clonePseudoElements: () => (/* binding */ clonePseudoElements)
/* harmony export */ });
/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./util */ "../node_modules/html-to-image/es/util.js");

function formatCSSText(style) {
    const content = style.getPropertyValue('content');
    return `${style.cssText} content: '${content.replace(/'|"/g, '')}';`;
}
function formatCSSProperties(style, options) {
    return (0,_util__WEBPACK_IMPORTED_MODULE_0__.getStyleProperties)(options)
        .map((name) => {
        const value = style.getPropertyValue(name);
        const priority = style.getPropertyPriority(name);
        return `${name}: ${value}${priority ? ' !important' : ''};`;
    })
        .join(' ');
}
function getPseudoElementStyle(className, pseudo, style, options) {
    const selector = `.${className}:${pseudo}`;
    const cssText = style.cssText
        ? formatCSSText(style)
        : formatCSSProperties(style, options);
    return document.createTextNode(`${selector}{${cssText}}`);
}
function clonePseudoElement(nativeNode, clonedNode, pseudo, options) {
    const style = window.getComputedStyle(nativeNode, pseudo);
    const content = style.getPropertyValue('content');
    if (content === '' || content === 'none') {
        return;
    }
    const className = (0,_util__WEBPACK_IMPORTED_MODULE_0__.uuid)();
    try {
        clonedNode.className = `${clonedNode.className} ${className}`;
    }
    catch (err) {
        return;
    }
    const styleElement = document.createElement('style');
    styleElement.appendChild(getPseudoElementStyle(className, pseudo, style, options));
    clonedNode.appendChild(styleElement);
}
function clonePseudoElements(nativeNode, clonedNode, options) {
    clonePseudoElement(nativeNode, clonedNode, ':before', options);
    clonePseudoElement(nativeNode, clonedNode, ':after', options);
}
//# sourceMappingURL=clone-pseudos.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/dataurl.js":
/*!***************************************************!*\
  !*** ../node_modules/html-to-image/es/dataurl.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fetchAsDataURL: () => (/* binding */ fetchAsDataURL),
/* harmony export */   isDataUrl: () => (/* binding */ isDataUrl),
/* harmony export */   makeDataUrl: () => (/* binding */ makeDataUrl),
/* harmony export */   resourceToDataURL: () => (/* binding */ resourceToDataURL)
/* harmony export */ });
function getContentFromDataUrl(dataURL) {
    return dataURL.split(/,/)[1];
}
function isDataUrl(url) {
    return url.search(/^(data:)/) !== -1;
}
function makeDataUrl(content, mimeType) {
    return `data:${mimeType};base64,${content}`;
}
async function fetchAsDataURL(url, init, process) {
    const res = await fetch(url, init);
    if (res.status === 404) {
        throw new Error(`Resource "${res.url}" not found`);
    }
    const blob = await res.blob();
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onerror = reject;
        reader.onloadend = () => {
            try {
                resolve(process({ res, result: reader.result }));
            }
            catch (error) {
                reject(error);
            }
        };
        reader.readAsDataURL(blob);
    });
}
const cache = {};
function getCacheKey(url, contentType, includeQueryParams) {
    let key = url.replace(/\?.*/, '');
    if (includeQueryParams) {
        key = url;
    }
    // font resource
    if (/ttf|otf|eot|woff2?/i.test(key)) {
        key = key.replace(/.*\//, '');
    }
    return contentType ? `[${contentType}]${key}` : key;
}
async function resourceToDataURL(resourceUrl, contentType, options) {
    const cacheKey = getCacheKey(resourceUrl, contentType, options.includeQueryParams);
    if (cache[cacheKey] != null) {
        return cache[cacheKey];
    }
    // ref: https://developer.mozilla.org/en/docs/Web/API/XMLHttpRequest/Using_XMLHttpRequest#Bypassing_the_cache
    if (options.cacheBust) {
        // eslint-disable-next-line no-param-reassign
        resourceUrl += (/\?/.test(resourceUrl) ? '&' : '?') + new Date().getTime();
    }
    let dataURL;
    try {
        const content = await fetchAsDataURL(resourceUrl, options.fetchRequestInit, ({ res, result }) => {
            if (!contentType) {
                // eslint-disable-next-line no-param-reassign
                contentType = res.headers.get('Content-Type') || '';
            }
            return getContentFromDataUrl(result);
        });
        dataURL = makeDataUrl(content, contentType);
    }
    catch (error) {
        dataURL = options.imagePlaceholder || '';
        let msg = `Failed to fetch resource: ${resourceUrl}`;
        if (error) {
            msg = typeof error === 'string' ? error : error.message;
        }
        if (msg) {
            console.warn(msg);
        }
    }
    cache[cacheKey] = dataURL;
    return dataURL;
}
//# sourceMappingURL=dataurl.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/embed-images.js":
/*!********************************************************!*\
  !*** ../node_modules/html-to-image/es/embed-images.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   embedImages: () => (/* binding */ embedImages)
/* harmony export */ });
/* harmony import */ var _embed_resources__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./embed-resources */ "../node_modules/html-to-image/es/embed-resources.js");
/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./util */ "../node_modules/html-to-image/es/util.js");
/* harmony import */ var _dataurl__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./dataurl */ "../node_modules/html-to-image/es/dataurl.js");
/* harmony import */ var _mimes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./mimes */ "../node_modules/html-to-image/es/mimes.js");




async function embedProp(propName, node, options) {
    var _a;
    const propValue = (_a = node.style) === null || _a === void 0 ? void 0 : _a.getPropertyValue(propName);
    if (propValue) {
        const cssString = await (0,_embed_resources__WEBPACK_IMPORTED_MODULE_0__.embedResources)(propValue, null, options);
        node.style.setProperty(propName, cssString, node.style.getPropertyPriority(propName));
        return true;
    }
    return false;
}
async function embedBackground(clonedNode, options) {
    ;
    (await embedProp('background', clonedNode, options)) ||
        (await embedProp('background-image', clonedNode, options));
    (await embedProp('mask', clonedNode, options)) ||
        (await embedProp('-webkit-mask', clonedNode, options)) ||
        (await embedProp('mask-image', clonedNode, options)) ||
        (await embedProp('-webkit-mask-image', clonedNode, options));
}
async function embedImageNode(clonedNode, options) {
    const isImageElement = (0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(clonedNode, HTMLImageElement);
    if (!(isImageElement && !(0,_dataurl__WEBPACK_IMPORTED_MODULE_2__.isDataUrl)(clonedNode.src)) &&
        !((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(clonedNode, SVGImageElement) &&
            !(0,_dataurl__WEBPACK_IMPORTED_MODULE_2__.isDataUrl)(clonedNode.href.baseVal))) {
        return;
    }
    const url = isImageElement ? clonedNode.src : clonedNode.href.baseVal;
    const dataURL = await (0,_dataurl__WEBPACK_IMPORTED_MODULE_2__.resourceToDataURL)(url, (0,_mimes__WEBPACK_IMPORTED_MODULE_3__.getMimeType)(url), options);
    await new Promise((resolve, reject) => {
        clonedNode.onload = resolve;
        clonedNode.onerror = options.onImageErrorHandler
            ? (...attributes) => {
                try {
                    resolve(options.onImageErrorHandler(...attributes));
                }
                catch (error) {
                    reject(error);
                }
            }
            : reject;
        const image = clonedNode;
        if (image.decode) {
            image.decode = resolve;
        }
        if (image.loading === 'lazy') {
            image.loading = 'eager';
        }
        if (isImageElement) {
            clonedNode.srcset = '';
            clonedNode.src = dataURL;
        }
        else {
            clonedNode.href.baseVal = dataURL;
        }
    });
}
async function embedChildren(clonedNode, options) {
    const children = (0,_util__WEBPACK_IMPORTED_MODULE_1__.toArray)(clonedNode.childNodes);
    const deferreds = children.map((child) => embedImages(child, options));
    await Promise.all(deferreds).then(() => clonedNode);
}
async function embedImages(clonedNode, options) {
    if ((0,_util__WEBPACK_IMPORTED_MODULE_1__.isInstanceOfElement)(clonedNode, Element)) {
        await embedBackground(clonedNode, options);
        await embedImageNode(clonedNode, options);
        await embedChildren(clonedNode, options);
    }
}
//# sourceMappingURL=embed-images.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/embed-resources.js":
/*!***********************************************************!*\
  !*** ../node_modules/html-to-image/es/embed-resources.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   embed: () => (/* binding */ embed),
/* harmony export */   embedResources: () => (/* binding */ embedResources),
/* harmony export */   parseURLs: () => (/* binding */ parseURLs),
/* harmony export */   shouldEmbed: () => (/* binding */ shouldEmbed)
/* harmony export */ });
/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./util */ "../node_modules/html-to-image/es/util.js");
/* harmony import */ var _mimes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./mimes */ "../node_modules/html-to-image/es/mimes.js");
/* harmony import */ var _dataurl__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./dataurl */ "../node_modules/html-to-image/es/dataurl.js");



const URL_REGEX = /url\((['"]?)([^'"]+?)\1\)/g;
const URL_WITH_FORMAT_REGEX = /url\([^)]+\)\s*format\((["']?)([^"']+)\1\)/g;
const FONT_SRC_REGEX = /src:\s*(?:url\([^)]+\)\s*format\([^)]+\)[,;]\s*)+/g;
function toRegex(url) {
    // eslint-disable-next-line no-useless-escape
    const escaped = url.replace(/([.*+?^${}()|\[\]\/\\])/g, '\\$1');
    return new RegExp(`(url\\(['"]?)(${escaped})(['"]?\\))`, 'g');
}
function parseURLs(cssText) {
    const urls = [];
    cssText.replace(URL_REGEX, (raw, quotation, url) => {
        urls.push(url);
        return raw;
    });
    return urls.filter((url) => !(0,_dataurl__WEBPACK_IMPORTED_MODULE_2__.isDataUrl)(url));
}
async function embed(cssText, resourceURL, baseURL, options, getContentFromUrl) {
    try {
        const resolvedURL = baseURL ? (0,_util__WEBPACK_IMPORTED_MODULE_0__.resolveUrl)(resourceURL, baseURL) : resourceURL;
        const contentType = (0,_mimes__WEBPACK_IMPORTED_MODULE_1__.getMimeType)(resourceURL);
        let dataURL;
        if (getContentFromUrl) {
            const content = await getContentFromUrl(resolvedURL);
            dataURL = (0,_dataurl__WEBPACK_IMPORTED_MODULE_2__.makeDataUrl)(content, contentType);
        }
        else {
            dataURL = await (0,_dataurl__WEBPACK_IMPORTED_MODULE_2__.resourceToDataURL)(resolvedURL, contentType, options);
        }
        return cssText.replace(toRegex(resourceURL), `$1${dataURL}$3`);
    }
    catch (error) {
        // pass
    }
    return cssText;
}
function filterPreferredFontFormat(str, { preferredFontFormat }) {
    return !preferredFontFormat
        ? str
        : str.replace(FONT_SRC_REGEX, (match) => {
            // eslint-disable-next-line no-constant-condition
            while (true) {
                const [src, , format] = URL_WITH_FORMAT_REGEX.exec(match) || [];
                if (!format) {
                    return '';
                }
                if (format === preferredFontFormat) {
                    return `src: ${src};`;
                }
            }
        });
}
function shouldEmbed(url) {
    return url.search(URL_REGEX) !== -1;
}
async function embedResources(cssText, baseUrl, options) {
    if (!shouldEmbed(cssText)) {
        return cssText;
    }
    const filteredCSSText = filterPreferredFontFormat(cssText, options);
    const urls = parseURLs(filteredCSSText);
    return urls.reduce((deferred, url) => deferred.then((css) => embed(css, url, baseUrl, options)), Promise.resolve(filteredCSSText));
}
//# sourceMappingURL=embed-resources.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/embed-webfonts.js":
/*!**********************************************************!*\
  !*** ../node_modules/html-to-image/es/embed-webfonts.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   embedWebFonts: () => (/* binding */ embedWebFonts),
/* harmony export */   getWebFontCSS: () => (/* binding */ getWebFontCSS)
/* harmony export */ });
/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./util */ "../node_modules/html-to-image/es/util.js");
/* harmony import */ var _dataurl__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./dataurl */ "../node_modules/html-to-image/es/dataurl.js");
/* harmony import */ var _embed_resources__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./embed-resources */ "../node_modules/html-to-image/es/embed-resources.js");



const cssFetchCache = {};
async function fetchCSS(url) {
    let cache = cssFetchCache[url];
    if (cache != null) {
        return cache;
    }
    const res = await fetch(url);
    const cssText = await res.text();
    cache = { url, cssText };
    cssFetchCache[url] = cache;
    return cache;
}
async function embedFonts(data, options) {
    let cssText = data.cssText;
    const regexUrl = /url\(["']?([^"')]+)["']?\)/g;
    const fontLocs = cssText.match(/url\([^)]+\)/g) || [];
    const loadFonts = fontLocs.map(async (loc) => {
        let url = loc.replace(regexUrl, '$1');
        if (!url.startsWith('https://')) {
            url = new URL(url, data.url).href;
        }
        return (0,_dataurl__WEBPACK_IMPORTED_MODULE_1__.fetchAsDataURL)(url, options.fetchRequestInit, ({ result }) => {
            cssText = cssText.replace(loc, `url(${result})`);
            return [loc, result];
        });
    });
    return Promise.all(loadFonts).then(() => cssText);
}
function parseCSS(source) {
    if (source == null) {
        return [];
    }
    const result = [];
    const commentsRegex = /(\/\*[\s\S]*?\*\/)/gi;
    // strip out comments
    let cssText = source.replace(commentsRegex, '');
    // eslint-disable-next-line prefer-regex-literals
    const keyframesRegex = new RegExp('((@.*?keyframes [\\s\\S]*?){([\\s\\S]*?}\\s*?)})', 'gi');
    // eslint-disable-next-line no-constant-condition
    while (true) {
        const matches = keyframesRegex.exec(cssText);
        if (matches === null) {
            break;
        }
        result.push(matches[0]);
    }
    cssText = cssText.replace(keyframesRegex, '');
    const importRegex = /@import[\s\S]*?url\([^)]*\)[\s\S]*?;/gi;
    // to match css & media queries together
    const combinedCSSRegex = '((\\s*?(?:\\/\\*[\\s\\S]*?\\*\\/)?\\s*?@media[\\s\\S]' +
        '*?){([\\s\\S]*?)}\\s*?})|(([\\s\\S]*?){([\\s\\S]*?)})';
    // unified regex
    const unifiedRegex = new RegExp(combinedCSSRegex, 'gi');
    // eslint-disable-next-line no-constant-condition
    while (true) {
        let matches = importRegex.exec(cssText);
        if (matches === null) {
            matches = unifiedRegex.exec(cssText);
            if (matches === null) {
                break;
            }
            else {
                importRegex.lastIndex = unifiedRegex.lastIndex;
            }
        }
        else {
            unifiedRegex.lastIndex = importRegex.lastIndex;
        }
        result.push(matches[0]);
    }
    return result;
}
async function getCSSRules(styleSheets, options) {
    const ret = [];
    const deferreds = [];
    // First loop inlines imports
    styleSheets.forEach((sheet) => {
        if ('cssRules' in sheet) {
            try {
                (0,_util__WEBPACK_IMPORTED_MODULE_0__.toArray)(sheet.cssRules || []).forEach((item, index) => {
                    if (item.type === CSSRule.IMPORT_RULE) {
                        let importIndex = index + 1;
                        const url = item.href;
                        const deferred = fetchCSS(url)
                            .then((metadata) => embedFonts(metadata, options))
                            .then((cssText) => parseCSS(cssText).forEach((rule) => {
                            try {
                                sheet.insertRule(rule, rule.startsWith('@import')
                                    ? (importIndex += 1)
                                    : sheet.cssRules.length);
                            }
                            catch (error) {
                                console.error('Error inserting rule from remote css', {
                                    rule,
                                    error,
                                });
                            }
                        }))
                            .catch((e) => {
                            console.error('Error loading remote css', e.toString());
                        });
                        deferreds.push(deferred);
                    }
                });
            }
            catch (e) {
                const inline = styleSheets.find((a) => a.href == null) || document.styleSheets[0];
                if (sheet.href != null) {
                    deferreds.push(fetchCSS(sheet.href)
                        .then((metadata) => embedFonts(metadata, options))
                        .then((cssText) => parseCSS(cssText).forEach((rule) => {
                        inline.insertRule(rule, inline.cssRules.length);
                    }))
                        .catch((err) => {
                        console.error('Error loading remote stylesheet', err);
                    }));
                }
                console.error('Error inlining remote css file', e);
            }
        }
    });
    return Promise.all(deferreds).then(() => {
        // Second loop parses rules
        styleSheets.forEach((sheet) => {
            if ('cssRules' in sheet) {
                try {
                    (0,_util__WEBPACK_IMPORTED_MODULE_0__.toArray)(sheet.cssRules || []).forEach((item) => {
                        ret.push(item);
                    });
                }
                catch (e) {
                    console.error(`Error while reading CSS rules from ${sheet.href}`, e);
                }
            }
        });
        return ret;
    });
}
function getWebFontRules(cssRules) {
    return cssRules
        .filter((rule) => rule.type === CSSRule.FONT_FACE_RULE)
        .filter((rule) => (0,_embed_resources__WEBPACK_IMPORTED_MODULE_2__.shouldEmbed)(rule.style.getPropertyValue('src')));
}
async function parseWebFontRules(node, options) {
    if (node.ownerDocument == null) {
        throw new Error('Provided element is not within a Document');
    }
    const styleSheets = (0,_util__WEBPACK_IMPORTED_MODULE_0__.toArray)(node.ownerDocument.styleSheets);
    const cssRules = await getCSSRules(styleSheets, options);
    return getWebFontRules(cssRules);
}
function normalizeFontFamily(font) {
    return font.trim().replace(/["']/g, '');
}
function getUsedFonts(node) {
    const fonts = new Set();
    function traverse(node) {
        const fontFamily = node.style.fontFamily || getComputedStyle(node).fontFamily;
        fontFamily.split(',').forEach((font) => {
            fonts.add(normalizeFontFamily(font));
        });
        Array.from(node.children).forEach((child) => {
            if (child instanceof HTMLElement) {
                traverse(child);
            }
        });
    }
    traverse(node);
    return fonts;
}
async function getWebFontCSS(node, options) {
    const rules = await parseWebFontRules(node, options);
    const usedFonts = getUsedFonts(node);
    const cssTexts = await Promise.all(rules
        .filter((rule) => usedFonts.has(normalizeFontFamily(rule.style.fontFamily)))
        .map((rule) => {
        const baseUrl = rule.parentStyleSheet
            ? rule.parentStyleSheet.href
            : null;
        return (0,_embed_resources__WEBPACK_IMPORTED_MODULE_2__.embedResources)(rule.cssText, baseUrl, options);
    }));
    return cssTexts.join('\n');
}
async function embedWebFonts(clonedNode, options) {
    const cssText = options.fontEmbedCSS != null
        ? options.fontEmbedCSS
        : options.skipFonts
            ? null
            : await getWebFontCSS(clonedNode, options);
    if (cssText) {
        const styleNode = document.createElement('style');
        const sytleContent = document.createTextNode(cssText);
        styleNode.appendChild(sytleContent);
        if (clonedNode.firstChild) {
            clonedNode.insertBefore(styleNode, clonedNode.firstChild);
        }
        else {
            clonedNode.appendChild(styleNode);
        }
    }
}
//# sourceMappingURL=embed-webfonts.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/index.js":
/*!*************************************************!*\
  !*** ../node_modules/html-to-image/es/index.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getFontEmbedCSS: () => (/* binding */ getFontEmbedCSS),
/* harmony export */   toBlob: () => (/* binding */ toBlob),
/* harmony export */   toCanvas: () => (/* binding */ toCanvas),
/* harmony export */   toJpeg: () => (/* binding */ toJpeg),
/* harmony export */   toPixelData: () => (/* binding */ toPixelData),
/* harmony export */   toPng: () => (/* binding */ toPng),
/* harmony export */   toSvg: () => (/* binding */ toSvg)
/* harmony export */ });
/* harmony import */ var _clone_node__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./clone-node */ "../node_modules/html-to-image/es/clone-node.js");
/* harmony import */ var _embed_images__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./embed-images */ "../node_modules/html-to-image/es/embed-images.js");
/* harmony import */ var _apply_style__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./apply-style */ "../node_modules/html-to-image/es/apply-style.js");
/* harmony import */ var _embed_webfonts__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./embed-webfonts */ "../node_modules/html-to-image/es/embed-webfonts.js");
/* harmony import */ var _util__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./util */ "../node_modules/html-to-image/es/util.js");





async function toSvg(node, options = {}) {
    const { width, height } = (0,_util__WEBPACK_IMPORTED_MODULE_4__.getImageSize)(node, options);
    const clonedNode = (await (0,_clone_node__WEBPACK_IMPORTED_MODULE_0__.cloneNode)(node, options, true));
    await (0,_embed_webfonts__WEBPACK_IMPORTED_MODULE_3__.embedWebFonts)(clonedNode, options);
    await (0,_embed_images__WEBPACK_IMPORTED_MODULE_1__.embedImages)(clonedNode, options);
    (0,_apply_style__WEBPACK_IMPORTED_MODULE_2__.applyStyle)(clonedNode, options);
    const datauri = await (0,_util__WEBPACK_IMPORTED_MODULE_4__.nodeToDataURL)(clonedNode, width, height);
    return datauri;
}
async function toCanvas(node, options = {}) {
    const { width, height } = (0,_util__WEBPACK_IMPORTED_MODULE_4__.getImageSize)(node, options);
    const svg = await toSvg(node, options);
    const img = await (0,_util__WEBPACK_IMPORTED_MODULE_4__.createImage)(svg);
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const ratio = options.pixelRatio || (0,_util__WEBPACK_IMPORTED_MODULE_4__.getPixelRatio)();
    const canvasWidth = options.canvasWidth || width;
    const canvasHeight = options.canvasHeight || height;
    canvas.width = canvasWidth * ratio;
    canvas.height = canvasHeight * ratio;
    if (!options.skipAutoScale) {
        (0,_util__WEBPACK_IMPORTED_MODULE_4__.checkCanvasDimensions)(canvas);
    }
    canvas.style.width = `${canvasWidth}`;
    canvas.style.height = `${canvasHeight}`;
    if (options.backgroundColor) {
        context.fillStyle = options.backgroundColor;
        context.fillRect(0, 0, canvas.width, canvas.height);
    }
    context.drawImage(img, 0, 0, canvas.width, canvas.height);
    return canvas;
}
async function toPixelData(node, options = {}) {
    const { width, height } = (0,_util__WEBPACK_IMPORTED_MODULE_4__.getImageSize)(node, options);
    const canvas = await toCanvas(node, options);
    const ctx = canvas.getContext('2d');
    return ctx.getImageData(0, 0, width, height).data;
}
async function toPng(node, options = {}) {
    const canvas = await toCanvas(node, options);
    return canvas.toDataURL();
}
async function toJpeg(node, options = {}) {
    const canvas = await toCanvas(node, options);
    return canvas.toDataURL('image/jpeg', options.quality || 1);
}
async function toBlob(node, options = {}) {
    const canvas = await toCanvas(node, options);
    const blob = await (0,_util__WEBPACK_IMPORTED_MODULE_4__.canvasToBlob)(canvas);
    return blob;
}
async function getFontEmbedCSS(node, options = {}) {
    return (0,_embed_webfonts__WEBPACK_IMPORTED_MODULE_3__.getWebFontCSS)(node, options);
}
//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/mimes.js":
/*!*************************************************!*\
  !*** ../node_modules/html-to-image/es/mimes.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getMimeType: () => (/* binding */ getMimeType)
/* harmony export */ });
const WOFF = 'application/font-woff';
const JPEG = 'image/jpeg';
const mimes = {
    woff: WOFF,
    woff2: WOFF,
    ttf: 'application/font-truetype',
    eot: 'application/vnd.ms-fontobject',
    png: 'image/png',
    jpg: JPEG,
    jpeg: JPEG,
    gif: 'image/gif',
    tiff: 'image/tiff',
    svg: 'image/svg+xml',
    webp: 'image/webp',
};
function getExtension(url) {
    const match = /\.([^./]*?)$/g.exec(url);
    return match ? match[1] : '';
}
function getMimeType(url) {
    const extension = getExtension(url).toLowerCase();
    return mimes[extension] || '';
}
//# sourceMappingURL=mimes.js.map

/***/ }),

/***/ "../node_modules/html-to-image/es/util.js":
/*!************************************************!*\
  !*** ../node_modules/html-to-image/es/util.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   canvasToBlob: () => (/* binding */ canvasToBlob),
/* harmony export */   checkCanvasDimensions: () => (/* binding */ checkCanvasDimensions),
/* harmony export */   createImage: () => (/* binding */ createImage),
/* harmony export */   delay: () => (/* binding */ delay),
/* harmony export */   getImageSize: () => (/* binding */ getImageSize),
/* harmony export */   getPixelRatio: () => (/* binding */ getPixelRatio),
/* harmony export */   getStyleProperties: () => (/* binding */ getStyleProperties),
/* harmony export */   isInstanceOfElement: () => (/* binding */ isInstanceOfElement),
/* harmony export */   nodeToDataURL: () => (/* binding */ nodeToDataURL),
/* harmony export */   resolveUrl: () => (/* binding */ resolveUrl),
/* harmony export */   svgToDataURL: () => (/* binding */ svgToDataURL),
/* harmony export */   toArray: () => (/* binding */ toArray),
/* harmony export */   uuid: () => (/* binding */ uuid)
/* harmony export */ });
function resolveUrl(url, baseUrl) {
    // url is absolute already
    if (url.match(/^[a-z]+:\/\//i)) {
        return url;
    }
    // url is absolute already, without protocol
    if (url.match(/^\/\//)) {
        return window.location.protocol + url;
    }
    // dataURI, mailto:, tel:, etc.
    if (url.match(/^[a-z]+:/i)) {
        return url;
    }
    const doc = document.implementation.createHTMLDocument();
    const base = doc.createElement('base');
    const a = doc.createElement('a');
    doc.head.appendChild(base);
    doc.body.appendChild(a);
    if (baseUrl) {
        base.href = baseUrl;
    }
    a.href = url;
    return a.href;
}
const uuid = (() => {
    // generate uuid for className of pseudo elements.
    // We should not use GUIDs, otherwise pseudo elements sometimes cannot be captured.
    let counter = 0;
    // ref: http://stackoverflow.com/a/6248722/2519373
    const random = () => 
    // eslint-disable-next-line no-bitwise
    `0000${((Math.random() * 36 ** 4) << 0).toString(36)}`.slice(-4);
    return () => {
        counter += 1;
        return `u${random()}${counter}`;
    };
})();
function delay(ms) {
    return (args) => new Promise((resolve) => {
        setTimeout(() => resolve(args), ms);
    });
}
function toArray(arrayLike) {
    const arr = [];
    for (let i = 0, l = arrayLike.length; i < l; i++) {
        arr.push(arrayLike[i]);
    }
    return arr;
}
let styleProps = null;
function getStyleProperties(options = {}) {
    if (styleProps) {
        return styleProps;
    }
    if (options.includeStyleProperties) {
        styleProps = options.includeStyleProperties;
        return styleProps;
    }
    styleProps = toArray(window.getComputedStyle(document.documentElement));
    return styleProps;
}
function px(node, styleProperty) {
    const win = node.ownerDocument.defaultView || window;
    const val = win.getComputedStyle(node).getPropertyValue(styleProperty);
    return val ? parseFloat(val.replace('px', '')) : 0;
}
function getNodeWidth(node) {
    const leftBorder = px(node, 'border-left-width');
    const rightBorder = px(node, 'border-right-width');
    return node.clientWidth + leftBorder + rightBorder;
}
function getNodeHeight(node) {
    const topBorder = px(node, 'border-top-width');
    const bottomBorder = px(node, 'border-bottom-width');
    return node.clientHeight + topBorder + bottomBorder;
}
function getImageSize(targetNode, options = {}) {
    const width = options.width || getNodeWidth(targetNode);
    const height = options.height || getNodeHeight(targetNode);
    return { width, height };
}
function getPixelRatio() {
    let ratio;
    let FINAL_PROCESS;
    try {
        FINAL_PROCESS = process;
    }
    catch (e) {
        // pass
    }
    const val = FINAL_PROCESS && FINAL_PROCESS.env
        ? FINAL_PROCESS.env.devicePixelRatio
        : null;
    if (val) {
        ratio = parseInt(val, 10);
        if (Number.isNaN(ratio)) {
            ratio = 1;
        }
    }
    return ratio || window.devicePixelRatio || 1;
}
// @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/canvas#maximum_canvas_size
const canvasDimensionLimit = 16384;
function checkCanvasDimensions(canvas) {
    if (canvas.width > canvasDimensionLimit ||
        canvas.height > canvasDimensionLimit) {
        if (canvas.width > canvasDimensionLimit &&
            canvas.height > canvasDimensionLimit) {
            if (canvas.width > canvas.height) {
                canvas.height *= canvasDimensionLimit / canvas.width;
                canvas.width = canvasDimensionLimit;
            }
            else {
                canvas.width *= canvasDimensionLimit / canvas.height;
                canvas.height = canvasDimensionLimit;
            }
        }
        else if (canvas.width > canvasDimensionLimit) {
            canvas.height *= canvasDimensionLimit / canvas.width;
            canvas.width = canvasDimensionLimit;
        }
        else {
            canvas.width *= canvasDimensionLimit / canvas.height;
            canvas.height = canvasDimensionLimit;
        }
    }
}
function canvasToBlob(canvas, options = {}) {
    if (canvas.toBlob) {
        return new Promise((resolve) => {
            canvas.toBlob(resolve, options.type ? options.type : 'image/png', options.quality ? options.quality : 1);
        });
    }
    return new Promise((resolve) => {
        const binaryString = window.atob(canvas
            .toDataURL(options.type ? options.type : undefined, options.quality ? options.quality : undefined)
            .split(',')[1]);
        const len = binaryString.length;
        const binaryArray = new Uint8Array(len);
        for (let i = 0; i < len; i += 1) {
            binaryArray[i] = binaryString.charCodeAt(i);
        }
        resolve(new Blob([binaryArray], {
            type: options.type ? options.type : 'image/png',
        }));
    });
}
function createImage(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            img.decode().then(() => {
                requestAnimationFrame(() => resolve(img));
            });
        };
        img.onerror = reject;
        img.crossOrigin = 'anonymous';
        img.decoding = 'async';
        img.src = url;
    });
}
async function svgToDataURL(svg) {
    return Promise.resolve()
        .then(() => new XMLSerializer().serializeToString(svg))
        .then(encodeURIComponent)
        .then((html) => `data:image/svg+xml;charset=utf-8,${html}`);
}
async function nodeToDataURL(node, width, height) {
    const xmlns = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(xmlns, 'svg');
    const foreignObject = document.createElementNS(xmlns, 'foreignObject');
    svg.setAttribute('width', `${width}`);
    svg.setAttribute('height', `${height}`);
    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    foreignObject.setAttribute('width', '100%');
    foreignObject.setAttribute('height', '100%');
    foreignObject.setAttribute('x', '0');
    foreignObject.setAttribute('y', '0');
    foreignObject.setAttribute('externalResourcesRequired', 'true');
    svg.appendChild(foreignObject);
    foreignObject.appendChild(node);
    return svgToDataURL(svg);
}
const isInstanceOfElement = (node, instance) => {
    if (node instanceof instance)
        return true;
    const nodePrototype = Object.getPrototypeOf(node);
    if (nodePrototype === null)
        return false;
    return (nodePrototype.constructor.name === instance.name ||
        isInstanceOfElement(nodePrototype, instance));
};
//# sourceMappingURL=util.js.map

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

/***/ "../node_modules/react-draggable/build/cjs/Draggable.js":
/*!**************************************************************!*\
  !*** ../node_modules/react-draggable/build/cjs/Draggable.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "DraggableCore", ({
  enumerable: true,
  get: function () {
    return _DraggableCore.default;
  }
}));
exports["default"] = void 0;
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _reactDom = _interopRequireDefault(__webpack_require__(/*! react-dom */ "react-dom"));
var _clsx = _interopRequireDefault(__webpack_require__(/*! clsx */ "../node_modules/react-draggable/node_modules/clsx/dist/clsx.m.js"));
var _domFns = __webpack_require__(/*! ./utils/domFns */ "../node_modules/react-draggable/build/cjs/utils/domFns.js");
var _positionFns = __webpack_require__(/*! ./utils/positionFns */ "../node_modules/react-draggable/build/cjs/utils/positionFns.js");
var _shims = __webpack_require__(/*! ./utils/shims */ "../node_modules/react-draggable/build/cjs/utils/shims.js");
var _DraggableCore = _interopRequireDefault(__webpack_require__(/*! ./DraggableCore */ "../node_modules/react-draggable/build/cjs/DraggableCore.js"));
var _log = _interopRequireDefault(__webpack_require__(/*! ./utils/log */ "../node_modules/react-draggable/build/cjs/utils/log.js"));
function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }
function _getRequireWildcardCache(nodeInterop) { if (typeof WeakMap !== "function") return null; var cacheBabelInterop = new WeakMap(); var cacheNodeInterop = new WeakMap(); return (_getRequireWildcardCache = function (nodeInterop) { return nodeInterop ? cacheNodeInterop : cacheBabelInterop; })(nodeInterop); }
function _interopRequireWildcard(obj, nodeInterop) { if (!nodeInterop && obj && obj.__esModule) { return obj; } if (obj === null || typeof obj !== "object" && typeof obj !== "function") { return { default: obj }; } var cache = _getRequireWildcardCache(nodeInterop); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj.default = obj; if (cache) { cache.set(obj, newObj); } return newObj; }
function _extends() { _extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return typeof key === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (typeof input !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (typeof res !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); } /*:: import type {ControlPosition, PositionOffsetControlPosition, DraggableCoreProps, DraggableCoreDefaultProps} from './DraggableCore';*/
/*:: import type {Bounds, DraggableEventHandler} from './utils/types';*/
/*:: import type {Element as ReactElement} from 'react';*/
/*:: type DraggableState = {
  dragging: boolean,
  dragged: boolean,
  x: number, y: number,
  slackX: number, slackY: number,
  isElementSVG: boolean,
  prevPropsPosition: ?ControlPosition,
};*/
/*:: export type DraggableDefaultProps = {
  ...DraggableCoreDefaultProps,
  axis: 'both' | 'x' | 'y' | 'none',
  bounds: Bounds | string | false,
  defaultClassName: string,
  defaultClassNameDragging: string,
  defaultClassNameDragged: string,
  defaultPosition: ControlPosition,
  scale: number,
};*/
/*:: export type DraggableProps = {
  ...DraggableCoreProps,
  ...DraggableDefaultProps,
  positionOffset: PositionOffsetControlPosition,
  position: ControlPosition,
};*/
//
// Define <Draggable>
//
class Draggable extends React.Component /*:: <DraggableProps, DraggableState>*/{
  // React 16.3+
  // Arity (props, state)
  static getDerivedStateFromProps(_ref /*:: */, _ref2 /*:: */) /*: ?Partial<DraggableState>*/{
    let {
      position
    } /*: DraggableProps*/ = _ref /*: DraggableProps*/;
    let {
      prevPropsPosition
    } /*: DraggableState*/ = _ref2 /*: DraggableState*/;
    // Set x/y if a new position is provided in props that is different than the previous.
    if (position && (!prevPropsPosition || position.x !== prevPropsPosition.x || position.y !== prevPropsPosition.y)) {
      (0, _log.default)('Draggable: getDerivedStateFromProps %j', {
        position,
        prevPropsPosition
      });
      return {
        x: position.x,
        y: position.y,
        prevPropsPosition: {
          ...position
        }
      };
    }
    return null;
  }
  constructor(props /*: DraggableProps*/) {
    super(props);
    _defineProperty(this, "onDragStart", (e, coreData) => {
      (0, _log.default)('Draggable: onDragStart: %j', coreData);

      // Short-circuit if user's callback killed it.
      const shouldStart = this.props.onStart(e, (0, _positionFns.createDraggableData)(this, coreData));
      // Kills start event on core as well, so move handlers are never bound.
      if (shouldStart === false) return false;
      this.setState({
        dragging: true,
        dragged: true
      });
    });
    _defineProperty(this, "onDrag", (e, coreData) => {
      if (!this.state.dragging) return false;
      (0, _log.default)('Draggable: onDrag: %j', coreData);
      const uiData = (0, _positionFns.createDraggableData)(this, coreData);
      const newState = {
        x: uiData.x,
        y: uiData.y,
        slackX: 0,
        slackY: 0
      };

      // Keep within bounds.
      if (this.props.bounds) {
        // Save original x and y.
        const {
          x,
          y
        } = newState;

        // Add slack to the values used to calculate bound position. This will ensure that if
        // we start removing slack, the element won't react to it right away until it's been
        // completely removed.
        newState.x += this.state.slackX;
        newState.y += this.state.slackY;

        // Get bound position. This will ceil/floor the x and y within the boundaries.
        const [newStateX, newStateY] = (0, _positionFns.getBoundPosition)(this, newState.x, newState.y);
        newState.x = newStateX;
        newState.y = newStateY;

        // Recalculate slack by noting how much was shaved by the boundPosition handler.
        newState.slackX = this.state.slackX + (x - newState.x);
        newState.slackY = this.state.slackY + (y - newState.y);

        // Update the event we fire to reflect what really happened after bounds took effect.
        uiData.x = newState.x;
        uiData.y = newState.y;
        uiData.deltaX = newState.x - this.state.x;
        uiData.deltaY = newState.y - this.state.y;
      }

      // Short-circuit if user's callback killed it.
      const shouldUpdate = this.props.onDrag(e, uiData);
      if (shouldUpdate === false) return false;
      this.setState(newState);
    });
    _defineProperty(this, "onDragStop", (e, coreData) => {
      if (!this.state.dragging) return false;

      // Short-circuit if user's callback killed it.
      const shouldContinue = this.props.onStop(e, (0, _positionFns.createDraggableData)(this, coreData));
      if (shouldContinue === false) return false;
      (0, _log.default)('Draggable: onDragStop: %j', coreData);
      const newState /*: Partial<DraggableState>*/ = {
        dragging: false,
        slackX: 0,
        slackY: 0
      };

      // If this is a controlled component, the result of this operation will be to
      // revert back to the old position. We expect a handler on `onDragStop`, at the least.
      const controlled = Boolean(this.props.position);
      if (controlled) {
        const {
          x,
          y
        } = this.props.position;
        newState.x = x;
        newState.y = y;
      }
      this.setState(newState);
    });
    this.state = {
      // Whether or not we are currently dragging.
      dragging: false,
      // Whether or not we have been dragged before.
      dragged: false,
      // Current transform x and y.
      x: props.position ? props.position.x : props.defaultPosition.x,
      y: props.position ? props.position.y : props.defaultPosition.y,
      prevPropsPosition: {
        ...props.position
      },
      // Used for compensating for out-of-bounds drags
      slackX: 0,
      slackY: 0,
      // Can only determine if SVG after mounting
      isElementSVG: false
    };
    if (props.position && !(props.onDrag || props.onStop)) {
      // eslint-disable-next-line no-console
      console.warn('A `position` was applied to this <Draggable>, without drag handlers. This will make this ' + 'component effectively undraggable. Please attach `onDrag` or `onStop` handlers so you can adjust the ' + '`position` of this element.');
    }
  }
  componentDidMount() {
    // Check to see if the element passed is an instanceof SVGElement
    if (typeof window.SVGElement !== 'undefined' && this.findDOMNode() instanceof window.SVGElement) {
      this.setState({
        isElementSVG: true
      });
    }
  }
  componentWillUnmount() {
    this.setState({
      dragging: false
    }); // prevents invariant if unmounted while dragging
  }

  // React Strict Mode compatibility: if `nodeRef` is passed, we will use it instead of trying to find
  // the underlying DOM node ourselves. See the README for more information.
  findDOMNode() /*: ?HTMLElement*/{
    var _this$props$nodeRef$c, _this$props;
    return (_this$props$nodeRef$c = (_this$props = this.props) === null || _this$props === void 0 || (_this$props = _this$props.nodeRef) === null || _this$props === void 0 ? void 0 : _this$props.current) !== null && _this$props$nodeRef$c !== void 0 ? _this$props$nodeRef$c : _reactDom.default.findDOMNode(this);
  }
  render() /*: ReactElement<any>*/{
    const {
      axis,
      bounds,
      children,
      defaultPosition,
      defaultClassName,
      defaultClassNameDragging,
      defaultClassNameDragged,
      position,
      positionOffset,
      scale,
      ...draggableCoreProps
    } = this.props;
    let style = {};
    let svgTransform = null;

    // If this is controlled, we don't want to move it - unless it's dragging.
    const controlled = Boolean(position);
    const draggable = !controlled || this.state.dragging;
    const validPosition = position || defaultPosition;
    const transformOpts = {
      // Set left if horizontal drag is enabled
      x: (0, _positionFns.canDragX)(this) && draggable ? this.state.x : validPosition.x,
      // Set top if vertical drag is enabled
      y: (0, _positionFns.canDragY)(this) && draggable ? this.state.y : validPosition.y
    };

    // If this element was SVG, we use the `transform` attribute.
    if (this.state.isElementSVG) {
      svgTransform = (0, _domFns.createSVGTransform)(transformOpts, positionOffset);
    } else {
      // Add a CSS transform to move the element around. This allows us to move the element around
      // without worrying about whether or not it is relatively or absolutely positioned.
      // If the item you are dragging already has a transform set, wrap it in a <span> so <Draggable>
      // has a clean slate.
      style = (0, _domFns.createCSSTransform)(transformOpts, positionOffset);
    }

    // Mark with class while dragging
    const className = (0, _clsx.default)(children.props.className || '', defaultClassName, {
      [defaultClassNameDragging]: this.state.dragging,
      [defaultClassNameDragged]: this.state.dragged
    });

    // Reuse the child provided
    // This makes it flexible to use whatever element is wanted (div, ul, etc)
    return /*#__PURE__*/React.createElement(_DraggableCore.default, _extends({}, draggableCoreProps, {
      onStart: this.onDragStart,
      onDrag: this.onDrag,
      onStop: this.onDragStop
    }), /*#__PURE__*/React.cloneElement(React.Children.only(children), {
      className: className,
      style: {
        ...children.props.style,
        ...style
      },
      transform: svgTransform
    }));
  }
}
exports["default"] = Draggable;
_defineProperty(Draggable, "displayName", 'Draggable');
_defineProperty(Draggable, "propTypes", {
  // Accepts all props <DraggableCore> accepts.
  ..._DraggableCore.default.propTypes,
  /**
   * `axis` determines which axis the draggable can move.
   *
   *  Note that all callbacks will still return data as normal. This only
   *  controls flushing to the DOM.
   *
   * 'both' allows movement horizontally and vertically.
   * 'x' limits movement to horizontal axis.
   * 'y' limits movement to vertical axis.
   * 'none' limits all movement.
   *
   * Defaults to 'both'.
   */
  axis: _propTypes.default.oneOf(['both', 'x', 'y', 'none']),
  /**
   * `bounds` determines the range of movement available to the element.
   * Available values are:
   *
   * 'parent' restricts movement within the Draggable's parent node.
   *
   * Alternatively, pass an object with the following properties, all of which are optional:
   *
   * {left: LEFT_BOUND, right: RIGHT_BOUND, bottom: BOTTOM_BOUND, top: TOP_BOUND}
   *
   * All values are in px.
   *
   * Example:
   *
   * ```jsx
   *   let App = React.createClass({
   *       render: function () {
   *         return (
   *            <Draggable bounds={{right: 300, bottom: 300}}>
   *              <div>Content</div>
   *           </Draggable>
   *         );
   *       }
   *   });
   * ```
   */
  bounds: _propTypes.default.oneOfType([_propTypes.default.shape({
    left: _propTypes.default.number,
    right: _propTypes.default.number,
    top: _propTypes.default.number,
    bottom: _propTypes.default.number
  }), _propTypes.default.string, _propTypes.default.oneOf([false])]),
  defaultClassName: _propTypes.default.string,
  defaultClassNameDragging: _propTypes.default.string,
  defaultClassNameDragged: _propTypes.default.string,
  /**
   * `defaultPosition` specifies the x and y that the dragged item should start at
   *
   * Example:
   *
   * ```jsx
   *      let App = React.createClass({
   *          render: function () {
   *              return (
   *                  <Draggable defaultPosition={{x: 25, y: 25}}>
   *                      <div>I start with transformX: 25px and transformY: 25px;</div>
   *                  </Draggable>
   *              );
   *          }
   *      });
   * ```
   */
  defaultPosition: _propTypes.default.shape({
    x: _propTypes.default.number,
    y: _propTypes.default.number
  }),
  positionOffset: _propTypes.default.shape({
    x: _propTypes.default.oneOfType([_propTypes.default.number, _propTypes.default.string]),
    y: _propTypes.default.oneOfType([_propTypes.default.number, _propTypes.default.string])
  }),
  /**
   * `position`, if present, defines the current position of the element.
   *
   *  This is similar to how form elements in React work - if no `position` is supplied, the component
   *  is uncontrolled.
   *
   * Example:
   *
   * ```jsx
   *      let App = React.createClass({
   *          render: function () {
   *              return (
   *                  <Draggable position={{x: 25, y: 25}}>
   *                      <div>I start with transformX: 25px and transformY: 25px;</div>
   *                  </Draggable>
   *              );
   *          }
   *      });
   * ```
   */
  position: _propTypes.default.shape({
    x: _propTypes.default.number,
    y: _propTypes.default.number
  }),
  /**
   * These properties should be defined on the child, not here.
   */
  className: _shims.dontSetMe,
  style: _shims.dontSetMe,
  transform: _shims.dontSetMe
});
_defineProperty(Draggable, "defaultProps", {
  ..._DraggableCore.default.defaultProps,
  axis: 'both',
  bounds: false,
  defaultClassName: 'react-draggable',
  defaultClassNameDragging: 'react-draggable-dragging',
  defaultClassNameDragged: 'react-draggable-dragged',
  defaultPosition: {
    x: 0,
    y: 0
  },
  scale: 1
});

/***/ }),

/***/ "../node_modules/react-draggable/build/cjs/DraggableCore.js":
/*!******************************************************************!*\
  !*** ../node_modules/react-draggable/build/cjs/DraggableCore.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _reactDom = _interopRequireDefault(__webpack_require__(/*! react-dom */ "react-dom"));
var _domFns = __webpack_require__(/*! ./utils/domFns */ "../node_modules/react-draggable/build/cjs/utils/domFns.js");
var _positionFns = __webpack_require__(/*! ./utils/positionFns */ "../node_modules/react-draggable/build/cjs/utils/positionFns.js");
var _shims = __webpack_require__(/*! ./utils/shims */ "../node_modules/react-draggable/build/cjs/utils/shims.js");
var _log = _interopRequireDefault(__webpack_require__(/*! ./utils/log */ "../node_modules/react-draggable/build/cjs/utils/log.js"));
function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }
function _getRequireWildcardCache(nodeInterop) { if (typeof WeakMap !== "function") return null; var cacheBabelInterop = new WeakMap(); var cacheNodeInterop = new WeakMap(); return (_getRequireWildcardCache = function (nodeInterop) { return nodeInterop ? cacheNodeInterop : cacheBabelInterop; })(nodeInterop); }
function _interopRequireWildcard(obj, nodeInterop) { if (!nodeInterop && obj && obj.__esModule) { return obj; } if (obj === null || typeof obj !== "object" && typeof obj !== "function") { return { default: obj }; } var cache = _getRequireWildcardCache(nodeInterop); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj.default = obj; if (cache) { cache.set(obj, newObj); } return newObj; }
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return typeof key === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (typeof input !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (typeof res !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
/*:: import type {EventHandler, MouseTouchEvent} from './utils/types';*/
/*:: import type {Element as ReactElement} from 'react';*/
// Simple abstraction for dragging events names.
const eventsFor = {
  touch: {
    start: 'touchstart',
    move: 'touchmove',
    stop: 'touchend'
  },
  mouse: {
    start: 'mousedown',
    move: 'mousemove',
    stop: 'mouseup'
  }
};

// Default to mouse events.
let dragEventFor = eventsFor.mouse;
/*:: export type DraggableData = {
  node: HTMLElement,
  x: number, y: number,
  deltaX: number, deltaY: number,
  lastX: number, lastY: number,
};*/
/*:: export type DraggableEventHandler = (e: MouseEvent, data: DraggableData) => void | false;*/
/*:: export type ControlPosition = {x: number, y: number};*/
/*:: export type PositionOffsetControlPosition = {x: number|string, y: number|string};*/
/*:: export type DraggableCoreDefaultProps = {
  allowAnyClick: boolean,
  disabled: boolean,
  enableUserSelectHack: boolean,
  onStart: DraggableEventHandler,
  onDrag: DraggableEventHandler,
  onStop: DraggableEventHandler,
  onMouseDown: (e: MouseEvent) => void,
  scale: number,
};*/
/*:: export type DraggableCoreProps = {
  ...DraggableCoreDefaultProps,
  cancel: string,
  children: ReactElement<any>,
  offsetParent: HTMLElement,
  grid: [number, number],
  handle: string,
  nodeRef?: ?React.ElementRef<any>,
};*/
//
// Define <DraggableCore>.
//
// <DraggableCore> is for advanced usage of <Draggable>. It maintains minimal internal state so it can
// work well with libraries that require more control over the element.
//

class DraggableCore extends React.Component /*:: <DraggableCoreProps>*/{
  constructor() {
    super(...arguments);
    _defineProperty(this, "dragging", false);
    // Used while dragging to determine deltas.
    _defineProperty(this, "lastX", NaN);
    _defineProperty(this, "lastY", NaN);
    _defineProperty(this, "touchIdentifier", null);
    _defineProperty(this, "mounted", false);
    _defineProperty(this, "handleDragStart", e => {
      // Make it possible to attach event handlers on top of this one.
      this.props.onMouseDown(e);

      // Only accept left-clicks.
      if (!this.props.allowAnyClick && typeof e.button === 'number' && e.button !== 0) return false;

      // Get nodes. Be sure to grab relative document (could be iframed)
      const thisNode = this.findDOMNode();
      if (!thisNode || !thisNode.ownerDocument || !thisNode.ownerDocument.body) {
        throw new Error('<DraggableCore> not mounted on DragStart!');
      }
      const {
        ownerDocument
      } = thisNode;

      // Short circuit if handle or cancel prop was provided and selector doesn't match.
      if (this.props.disabled || !(e.target instanceof ownerDocument.defaultView.Node) || this.props.handle && !(0, _domFns.matchesSelectorAndParentsTo)(e.target, this.props.handle, thisNode) || this.props.cancel && (0, _domFns.matchesSelectorAndParentsTo)(e.target, this.props.cancel, thisNode)) {
        return;
      }

      // Prevent scrolling on mobile devices, like ipad/iphone.
      // Important that this is after handle/cancel.
      if (e.type === 'touchstart') e.preventDefault();

      // Set touch identifier in component state if this is a touch event. This allows us to
      // distinguish between individual touches on multitouch screens by identifying which
      // touchpoint was set to this element.
      const touchIdentifier = (0, _domFns.getTouchIdentifier)(e);
      this.touchIdentifier = touchIdentifier;

      // Get the current drag point from the event. This is used as the offset.
      const position = (0, _positionFns.getControlPosition)(e, touchIdentifier, this);
      if (position == null) return; // not possible but satisfies flow
      const {
        x,
        y
      } = position;

      // Create an event object with all the data parents need to make a decision here.
      const coreEvent = (0, _positionFns.createCoreData)(this, x, y);
      (0, _log.default)('DraggableCore: handleDragStart: %j', coreEvent);

      // Call event handler. If it returns explicit false, cancel.
      (0, _log.default)('calling', this.props.onStart);
      const shouldUpdate = this.props.onStart(e, coreEvent);
      if (shouldUpdate === false || this.mounted === false) return;

      // Add a style to the body to disable user-select. This prevents text from
      // being selected all over the page.
      if (this.props.enableUserSelectHack) (0, _domFns.addUserSelectStyles)(ownerDocument);

      // Initiate dragging. Set the current x and y as offsets
      // so we know how much we've moved during the drag. This allows us
      // to drag elements around even if they have been moved, without issue.
      this.dragging = true;
      this.lastX = x;
      this.lastY = y;

      // Add events to the document directly so we catch when the user's mouse/touch moves outside of
      // this element. We use different events depending on whether or not we have detected that this
      // is a touch-capable device.
      (0, _domFns.addEvent)(ownerDocument, dragEventFor.move, this.handleDrag);
      (0, _domFns.addEvent)(ownerDocument, dragEventFor.stop, this.handleDragStop);
    });
    _defineProperty(this, "handleDrag", e => {
      // Get the current drag point from the event. This is used as the offset.
      const position = (0, _positionFns.getControlPosition)(e, this.touchIdentifier, this);
      if (position == null) return;
      let {
        x,
        y
      } = position;

      // Snap to grid if prop has been provided
      if (Array.isArray(this.props.grid)) {
        let deltaX = x - this.lastX,
          deltaY = y - this.lastY;
        [deltaX, deltaY] = (0, _positionFns.snapToGrid)(this.props.grid, deltaX, deltaY);
        if (!deltaX && !deltaY) return; // skip useless drag
        x = this.lastX + deltaX, y = this.lastY + deltaY;
      }
      const coreEvent = (0, _positionFns.createCoreData)(this, x, y);
      (0, _log.default)('DraggableCore: handleDrag: %j', coreEvent);

      // Call event handler. If it returns explicit false, trigger end.
      const shouldUpdate = this.props.onDrag(e, coreEvent);
      if (shouldUpdate === false || this.mounted === false) {
        try {
          // $FlowIgnore
          this.handleDragStop(new MouseEvent('mouseup'));
        } catch (err) {
          // Old browsers
          const event = ((document.createEvent('MouseEvents') /*: any*/) /*: MouseTouchEvent*/);
          // I see why this insanity was deprecated
          // $FlowIgnore
          event.initMouseEvent('mouseup', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
          this.handleDragStop(event);
        }
        return;
      }
      this.lastX = x;
      this.lastY = y;
    });
    _defineProperty(this, "handleDragStop", e => {
      if (!this.dragging) return;
      const position = (0, _positionFns.getControlPosition)(e, this.touchIdentifier, this);
      if (position == null) return;
      let {
        x,
        y
      } = position;

      // Snap to grid if prop has been provided
      if (Array.isArray(this.props.grid)) {
        let deltaX = x - this.lastX || 0;
        let deltaY = y - this.lastY || 0;
        [deltaX, deltaY] = (0, _positionFns.snapToGrid)(this.props.grid, deltaX, deltaY);
        x = this.lastX + deltaX, y = this.lastY + deltaY;
      }
      const coreEvent = (0, _positionFns.createCoreData)(this, x, y);

      // Call event handler
      const shouldContinue = this.props.onStop(e, coreEvent);
      if (shouldContinue === false || this.mounted === false) return false;
      const thisNode = this.findDOMNode();
      if (thisNode) {
        // Remove user-select hack
        if (this.props.enableUserSelectHack) (0, _domFns.removeUserSelectStyles)(thisNode.ownerDocument);
      }
      (0, _log.default)('DraggableCore: handleDragStop: %j', coreEvent);

      // Reset the el.
      this.dragging = false;
      this.lastX = NaN;
      this.lastY = NaN;
      if (thisNode) {
        // Remove event handlers
        (0, _log.default)('DraggableCore: Removing handlers');
        (0, _domFns.removeEvent)(thisNode.ownerDocument, dragEventFor.move, this.handleDrag);
        (0, _domFns.removeEvent)(thisNode.ownerDocument, dragEventFor.stop, this.handleDragStop);
      }
    });
    _defineProperty(this, "onMouseDown", e => {
      dragEventFor = eventsFor.mouse; // on touchscreen laptops we could switch back to mouse

      return this.handleDragStart(e);
    });
    _defineProperty(this, "onMouseUp", e => {
      dragEventFor = eventsFor.mouse;
      return this.handleDragStop(e);
    });
    // Same as onMouseDown (start drag), but now consider this a touch device.
    _defineProperty(this, "onTouchStart", e => {
      // We're on a touch device now, so change the event handlers
      dragEventFor = eventsFor.touch;
      return this.handleDragStart(e);
    });
    _defineProperty(this, "onTouchEnd", e => {
      // We're on a touch device now, so change the event handlers
      dragEventFor = eventsFor.touch;
      return this.handleDragStop(e);
    });
  }
  componentDidMount() {
    this.mounted = true;
    // Touch handlers must be added with {passive: false} to be cancelable.
    // https://developers.google.com/web/updates/2017/01/scrolling-intervention
    const thisNode = this.findDOMNode();
    if (thisNode) {
      (0, _domFns.addEvent)(thisNode, eventsFor.touch.start, this.onTouchStart, {
        passive: false
      });
    }
  }
  componentWillUnmount() {
    this.mounted = false;
    // Remove any leftover event handlers. Remove both touch and mouse handlers in case
    // some browser quirk caused a touch event to fire during a mouse move, or vice versa.
    const thisNode = this.findDOMNode();
    if (thisNode) {
      const {
        ownerDocument
      } = thisNode;
      (0, _domFns.removeEvent)(ownerDocument, eventsFor.mouse.move, this.handleDrag);
      (0, _domFns.removeEvent)(ownerDocument, eventsFor.touch.move, this.handleDrag);
      (0, _domFns.removeEvent)(ownerDocument, eventsFor.mouse.stop, this.handleDragStop);
      (0, _domFns.removeEvent)(ownerDocument, eventsFor.touch.stop, this.handleDragStop);
      (0, _domFns.removeEvent)(thisNode, eventsFor.touch.start, this.onTouchStart, {
        passive: false
      });
      if (this.props.enableUserSelectHack) (0, _domFns.removeUserSelectStyles)(ownerDocument);
    }
  }

  // React Strict Mode compatibility: if `nodeRef` is passed, we will use it instead of trying to find
  // the underlying DOM node ourselves. See the README for more information.
  findDOMNode() /*: ?HTMLElement*/{
    var _this$props, _this$props2;
    return (_this$props = this.props) !== null && _this$props !== void 0 && _this$props.nodeRef ? (_this$props2 = this.props) === null || _this$props2 === void 0 || (_this$props2 = _this$props2.nodeRef) === null || _this$props2 === void 0 ? void 0 : _this$props2.current : _reactDom.default.findDOMNode(this);
  }
  render() /*: React.Element<any>*/{
    // Reuse the child provided
    // This makes it flexible to use whatever element is wanted (div, ul, etc)
    return /*#__PURE__*/React.cloneElement(React.Children.only(this.props.children), {
      // Note: mouseMove handler is attached to document so it will still function
      // when the user drags quickly and leaves the bounds of the element.
      onMouseDown: this.onMouseDown,
      onMouseUp: this.onMouseUp,
      // onTouchStart is added on `componentDidMount` so they can be added with
      // {passive: false}, which allows it to cancel. See
      // https://developers.google.com/web/updates/2017/01/scrolling-intervention
      onTouchEnd: this.onTouchEnd
    });
  }
}
exports["default"] = DraggableCore;
_defineProperty(DraggableCore, "displayName", 'DraggableCore');
_defineProperty(DraggableCore, "propTypes", {
  /**
   * `allowAnyClick` allows dragging using any mouse button.
   * By default, we only accept the left button.
   *
   * Defaults to `false`.
   */
  allowAnyClick: _propTypes.default.bool,
  children: _propTypes.default.node.isRequired,
  /**
   * `disabled`, if true, stops the <Draggable> from dragging. All handlers,
   * with the exception of `onMouseDown`, will not fire.
   */
  disabled: _propTypes.default.bool,
  /**
   * By default, we add 'user-select:none' attributes to the document body
   * to prevent ugly text selection during drag. If this is causing problems
   * for your app, set this to `false`.
   */
  enableUserSelectHack: _propTypes.default.bool,
  /**
   * `offsetParent`, if set, uses the passed DOM node to compute drag offsets
   * instead of using the parent node.
   */
  offsetParent: function (props /*: DraggableCoreProps*/, propName /*: $Keys<DraggableCoreProps>*/) {
    if (props[propName] && props[propName].nodeType !== 1) {
      throw new Error('Draggable\'s offsetParent must be a DOM Node.');
    }
  },
  /**
   * `grid` specifies the x and y that dragging should snap to.
   */
  grid: _propTypes.default.arrayOf(_propTypes.default.number),
  /**
   * `handle` specifies a selector to be used as the handle that initiates drag.
   *
   * Example:
   *
   * ```jsx
   *   let App = React.createClass({
   *       render: function () {
   *         return (
   *            <Draggable handle=".handle">
   *              <div>
   *                  <div className="handle">Click me to drag</div>
   *                  <div>This is some other content</div>
   *              </div>
   *           </Draggable>
   *         );
   *       }
   *   });
   * ```
   */
  handle: _propTypes.default.string,
  /**
   * `cancel` specifies a selector to be used to prevent drag initialization.
   *
   * Example:
   *
   * ```jsx
   *   let App = React.createClass({
   *       render: function () {
   *           return(
   *               <Draggable cancel=".cancel">
   *                   <div>
   *                     <div className="cancel">You can't drag from here</div>
   *                     <div>Dragging here works fine</div>
   *                   </div>
   *               </Draggable>
   *           );
   *       }
   *   });
   * ```
   */
  cancel: _propTypes.default.string,
  /* If running in React Strict mode, ReactDOM.findDOMNode() is deprecated.
   * Unfortunately, in order for <Draggable> to work properly, we need raw access
   * to the underlying DOM node. If you want to avoid the warning, pass a `nodeRef`
   * as in this example:
   *
   * function MyComponent() {
   *   const nodeRef = React.useRef(null);
   *   return (
   *     <Draggable nodeRef={nodeRef}>
   *       <div ref={nodeRef}>Example Target</div>
   *     </Draggable>
   *   );
   * }
   *
   * This can be used for arbitrarily nested components, so long as the ref ends up
   * pointing to the actual child DOM node and not a custom component.
   */
  nodeRef: _propTypes.default.object,
  /**
   * Called when dragging starts.
   * If this function returns the boolean false, dragging will be canceled.
   */
  onStart: _propTypes.default.func,
  /**
   * Called while dragging.
   * If this function returns the boolean false, dragging will be canceled.
   */
  onDrag: _propTypes.default.func,
  /**
   * Called when dragging stops.
   * If this function returns the boolean false, the drag will remain active.
   */
  onStop: _propTypes.default.func,
  /**
   * A workaround option which can be passed if onMouseDown needs to be accessed,
   * since it'll always be blocked (as there is internal use of onMouseDown)
   */
  onMouseDown: _propTypes.default.func,
  /**
   * `scale`, if set, applies scaling while dragging an element
   */
  scale: _propTypes.default.number,
  /**
   * These properties should be defined on the child, not here.
   */
  className: _shims.dontSetMe,
  style: _shims.dontSetMe,
  transform: _shims.dontSetMe
});
_defineProperty(DraggableCore, "defaultProps", {
  allowAnyClick: false,
  // by default only accept left click
  disabled: false,
  enableUserSelectHack: true,
  onStart: function () {},
  onDrag: function () {},
  onStop: function () {},
  onMouseDown: function () {},
  scale: 1
});

/***/ }),

/***/ "../node_modules/react-draggable/build/cjs/cjs.js":
/*!********************************************************!*\
  !*** ../node_modules/react-draggable/build/cjs/cjs.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


const {
  default: Draggable,
  DraggableCore
} = __webpack_require__(/*! ./Draggable */ "../node_modules/react-draggable/build/cjs/Draggable.js");

// Previous versions of this lib exported <Draggable> as the root export. As to no-// them, or TypeScript, we export *both* as the root and as 'default'.
// See https://github.com/mzabriskie/react-draggable/pull/254
// and https://github.com/mzabriskie/react-draggable/issues/266
module.exports = Draggable;
module.exports["default"] = Draggable;
module.exports.DraggableCore = DraggableCore;

/***/ }),

/***/ "../node_modules/react-draggable/build/cjs/utils/domFns.js":
/*!*****************************************************************!*\
  !*** ../node_modules/react-draggable/build/cjs/utils/domFns.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.addClassName = addClassName;
exports.addEvent = addEvent;
exports.addUserSelectStyles = addUserSelectStyles;
exports.createCSSTransform = createCSSTransform;
exports.createSVGTransform = createSVGTransform;
exports.getTouch = getTouch;
exports.getTouchIdentifier = getTouchIdentifier;
exports.getTranslation = getTranslation;
exports.innerHeight = innerHeight;
exports.innerWidth = innerWidth;
exports.matchesSelector = matchesSelector;
exports.matchesSelectorAndParentsTo = matchesSelectorAndParentsTo;
exports.offsetXYFromParent = offsetXYFromParent;
exports.outerHeight = outerHeight;
exports.outerWidth = outerWidth;
exports.removeClassName = removeClassName;
exports.removeEvent = removeEvent;
exports.removeUserSelectStyles = removeUserSelectStyles;
var _shims = __webpack_require__(/*! ./shims */ "../node_modules/react-draggable/build/cjs/utils/shims.js");
var _getPrefix = _interopRequireWildcard(__webpack_require__(/*! ./getPrefix */ "../node_modules/react-draggable/build/cjs/utils/getPrefix.js"));
function _getRequireWildcardCache(nodeInterop) { if (typeof WeakMap !== "function") return null; var cacheBabelInterop = new WeakMap(); var cacheNodeInterop = new WeakMap(); return (_getRequireWildcardCache = function (nodeInterop) { return nodeInterop ? cacheNodeInterop : cacheBabelInterop; })(nodeInterop); }
function _interopRequireWildcard(obj, nodeInterop) { if (!nodeInterop && obj && obj.__esModule) { return obj; } if (obj === null || typeof obj !== "object" && typeof obj !== "function") { return { default: obj }; } var cache = _getRequireWildcardCache(nodeInterop); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj.default = obj; if (cache) { cache.set(obj, newObj); } return newObj; }
/*:: import type {ControlPosition, PositionOffsetControlPosition, MouseTouchEvent} from './types';*/
let matchesSelectorFunc = '';
function matchesSelector(el /*: Node*/, selector /*: string*/) /*: boolean*/{
  if (!matchesSelectorFunc) {
    matchesSelectorFunc = (0, _shims.findInArray)(['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'], function (method) {
      // $FlowIgnore: Doesn't think elements are indexable
      return (0, _shims.isFunction)(el[method]);
    });
  }

  // Might not be found entirely (not an Element?) - in that case, bail
  // $FlowIgnore: Doesn't think elements are indexable
  if (!(0, _shims.isFunction)(el[matchesSelectorFunc])) return false;

  // $FlowIgnore: Doesn't think elements are indexable
  return el[matchesSelectorFunc](selector);
}

// Works up the tree to the draggable itself attempting to match selector.
function matchesSelectorAndParentsTo(el /*: Node*/, selector /*: string*/, baseNode /*: Node*/) /*: boolean*/{
  let node = el;
  do {
    if (matchesSelector(node, selector)) return true;
    if (node === baseNode) return false;
    // $FlowIgnore[incompatible-type]
    node = node.parentNode;
  } while (node);
  return false;
}
function addEvent(el /*: ?Node*/, event /*: string*/, handler /*: Function*/, inputOptions /*: Object*/) /*: void*/{
  if (!el) return;
  const options = {
    capture: true,
    ...inputOptions
  };
  // $FlowIgnore[method-unbinding]
  if (el.addEventListener) {
    el.addEventListener(event, handler, options);
  } else if (el.attachEvent) {
    el.attachEvent('on' + event, handler);
  } else {
    // $FlowIgnore: Doesn't think elements are indexable
    el['on' + event] = handler;
  }
}
function removeEvent(el /*: ?Node*/, event /*: string*/, handler /*: Function*/, inputOptions /*: Object*/) /*: void*/{
  if (!el) return;
  const options = {
    capture: true,
    ...inputOptions
  };
  // $FlowIgnore[method-unbinding]
  if (el.removeEventListener) {
    el.removeEventListener(event, handler, options);
  } else if (el.detachEvent) {
    el.detachEvent('on' + event, handler);
  } else {
    // $FlowIgnore: Doesn't think elements are indexable
    el['on' + event] = null;
  }
}
function outerHeight(node /*: HTMLElement*/) /*: number*/{
  // This is deliberately excluding margin for our calculations, since we are using
  // offsetTop which is including margin. See getBoundPosition
  let height = node.clientHeight;
  const computedStyle = node.ownerDocument.defaultView.getComputedStyle(node);
  height += (0, _shims.int)(computedStyle.borderTopWidth);
  height += (0, _shims.int)(computedStyle.borderBottomWidth);
  return height;
}
function outerWidth(node /*: HTMLElement*/) /*: number*/{
  // This is deliberately excluding margin for our calculations, since we are using
  // offsetLeft which is including margin. See getBoundPosition
  let width = node.clientWidth;
  const computedStyle = node.ownerDocument.defaultView.getComputedStyle(node);
  width += (0, _shims.int)(computedStyle.borderLeftWidth);
  width += (0, _shims.int)(computedStyle.borderRightWidth);
  return width;
}
function innerHeight(node /*: HTMLElement*/) /*: number*/{
  let height = node.clientHeight;
  const computedStyle = node.ownerDocument.defaultView.getComputedStyle(node);
  height -= (0, _shims.int)(computedStyle.paddingTop);
  height -= (0, _shims.int)(computedStyle.paddingBottom);
  return height;
}
function innerWidth(node /*: HTMLElement*/) /*: number*/{
  let width = node.clientWidth;
  const computedStyle = node.ownerDocument.defaultView.getComputedStyle(node);
  width -= (0, _shims.int)(computedStyle.paddingLeft);
  width -= (0, _shims.int)(computedStyle.paddingRight);
  return width;
}
/*:: interface EventWithOffset {
  clientX: number, clientY: number
}*/
// Get from offsetParent
function offsetXYFromParent(evt /*: EventWithOffset*/, offsetParent /*: HTMLElement*/, scale /*: number*/) /*: ControlPosition*/{
  const isBody = offsetParent === offsetParent.ownerDocument.body;
  const offsetParentRect = isBody ? {
    left: 0,
    top: 0
  } : offsetParent.getBoundingClientRect();
  const x = (evt.clientX + offsetParent.scrollLeft - offsetParentRect.left) / scale;
  const y = (evt.clientY + offsetParent.scrollTop - offsetParentRect.top) / scale;
  return {
    x,
    y
  };
}
function createCSSTransform(controlPos /*: ControlPosition*/, positionOffset /*: PositionOffsetControlPosition*/) /*: Object*/{
  const translation = getTranslation(controlPos, positionOffset, 'px');
  return {
    [(0, _getPrefix.browserPrefixToKey)('transform', _getPrefix.default)]: translation
  };
}
function createSVGTransform(controlPos /*: ControlPosition*/, positionOffset /*: PositionOffsetControlPosition*/) /*: string*/{
  const translation = getTranslation(controlPos, positionOffset, '');
  return translation;
}
function getTranslation(_ref /*:: */, positionOffset /*: PositionOffsetControlPosition*/, unitSuffix /*: string*/) /*: string*/{
  let {
    x,
    y
  } /*: ControlPosition*/ = _ref /*: ControlPosition*/;
  let translation = "translate(".concat(x).concat(unitSuffix, ",").concat(y).concat(unitSuffix, ")");
  if (positionOffset) {
    const defaultX = "".concat(typeof positionOffset.x === 'string' ? positionOffset.x : positionOffset.x + unitSuffix);
    const defaultY = "".concat(typeof positionOffset.y === 'string' ? positionOffset.y : positionOffset.y + unitSuffix);
    translation = "translate(".concat(defaultX, ", ").concat(defaultY, ")") + translation;
  }
  return translation;
}
function getTouch(e /*: MouseTouchEvent*/, identifier /*: number*/) /*: ?{clientX: number, clientY: number}*/{
  return e.targetTouches && (0, _shims.findInArray)(e.targetTouches, t => identifier === t.identifier) || e.changedTouches && (0, _shims.findInArray)(e.changedTouches, t => identifier === t.identifier);
}
function getTouchIdentifier(e /*: MouseTouchEvent*/) /*: ?number*/{
  if (e.targetTouches && e.targetTouches[0]) return e.targetTouches[0].identifier;
  if (e.changedTouches && e.changedTouches[0]) return e.changedTouches[0].identifier;
}

// User-select Hacks:
//
// Useful for preventing blue highlights all over everything when dragging.

// Note we're passing `document` b/c we could be iframed
function addUserSelectStyles(doc /*: ?Document*/) {
  if (!doc) return;
  let styleEl = doc.getElementById('react-draggable-style-el');
  if (!styleEl) {
    styleEl = doc.createElement('style');
    styleEl.type = 'text/css';
    styleEl.id = 'react-draggable-style-el';
    styleEl.innerHTML = '.react-draggable-transparent-selection *::-moz-selection {all: inherit;}\n';
    styleEl.innerHTML += '.react-draggable-transparent-selection *::selection {all: inherit;}\n';
    doc.getElementsByTagName('head')[0].appendChild(styleEl);
  }
  if (doc.body) addClassName(doc.body, 'react-draggable-transparent-selection');
}
function removeUserSelectStyles(doc /*: ?Document*/) {
  if (!doc) return;
  try {
    if (doc.body) removeClassName(doc.body, 'react-draggable-transparent-selection');
    // $FlowIgnore: IE
    if (doc.selection) {
      // $FlowIgnore: IE
      doc.selection.empty();
    } else {
      // Remove selection caused by scroll, unless it's a focused input
      // (we use doc.defaultView in case we're in an iframe)
      const selection = (doc.defaultView || window).getSelection();
      if (selection && selection.type !== 'Caret') {
        selection.removeAllRanges();
      }
    }
  } catch (e) {
    // probably IE
  }
}
function addClassName(el /*: HTMLElement*/, className /*: string*/) {
  if (el.classList) {
    el.classList.add(className);
  } else {
    if (!el.className.match(new RegExp("(?:^|\\s)".concat(className, "(?!\\S)")))) {
      el.className += " ".concat(className);
    }
  }
}
function removeClassName(el /*: HTMLElement*/, className /*: string*/) {
  if (el.classList) {
    el.classList.remove(className);
  } else {
    el.className = el.className.replace(new RegExp("(?:^|\\s)".concat(className, "(?!\\S)"), 'g'), '');
  }
}

/***/ }),

/***/ "../node_modules/react-draggable/build/cjs/utils/getPrefix.js":
/*!********************************************************************!*\
  !*** ../node_modules/react-draggable/build/cjs/utils/getPrefix.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.browserPrefixToKey = browserPrefixToKey;
exports.browserPrefixToStyle = browserPrefixToStyle;
exports["default"] = void 0;
exports.getPrefix = getPrefix;
const prefixes = ['Moz', 'Webkit', 'O', 'ms'];
function getPrefix() /*: string*/{
  var _window$document;
  let prop /*: string*/ = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'transform';
  // Ensure we're running in an environment where there is actually a global
  // `window` obj
  if (typeof window === 'undefined') return '';

  // If we're in a pseudo-browser server-side environment, this access
  // path may not exist, so bail out if it doesn't.
  const style = (_window$document = window.document) === null || _window$document === void 0 || (_window$document = _window$document.documentElement) === null || _window$document === void 0 ? void 0 : _window$document.style;
  if (!style) return '';
  if (prop in style) return '';
  for (let i = 0; i < prefixes.length; i++) {
    if (browserPrefixToKey(prop, prefixes[i]) in style) return prefixes[i];
  }
  return '';
}
function browserPrefixToKey(prop /*: string*/, prefix /*: string*/) /*: string*/{
  return prefix ? "".concat(prefix).concat(kebabToTitleCase(prop)) : prop;
}
function browserPrefixToStyle(prop /*: string*/, prefix /*: string*/) /*: string*/{
  return prefix ? "-".concat(prefix.toLowerCase(), "-").concat(prop) : prop;
}
function kebabToTitleCase(str /*: string*/) /*: string*/{
  let out = '';
  let shouldCapitalize = true;
  for (let i = 0; i < str.length; i++) {
    if (shouldCapitalize) {
      out += str[i].toUpperCase();
      shouldCapitalize = false;
    } else if (str[i] === '-') {
      shouldCapitalize = true;
    } else {
      out += str[i];
    }
  }
  return out;
}

// Default export is the prefix itself, like 'Moz', 'Webkit', etc
// Note that you may have to re-test for certain things; for instance, Chrome 50
// can handle unprefixed `transform`, but not unprefixed `user-select`
var _default = exports["default"] = (getPrefix() /*: string*/);

/***/ }),

/***/ "../node_modules/react-draggable/build/cjs/utils/log.js":
/*!**************************************************************!*\
  !*** ../node_modules/react-draggable/build/cjs/utils/log.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = log;
/*eslint no-console:0*/
function log() {
  if (false) // removed by dead control flow
{}
}

/***/ }),

/***/ "../node_modules/react-draggable/build/cjs/utils/positionFns.js":
/*!**********************************************************************!*\
  !*** ../node_modules/react-draggable/build/cjs/utils/positionFns.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.canDragX = canDragX;
exports.canDragY = canDragY;
exports.createCoreData = createCoreData;
exports.createDraggableData = createDraggableData;
exports.getBoundPosition = getBoundPosition;
exports.getControlPosition = getControlPosition;
exports.snapToGrid = snapToGrid;
var _shims = __webpack_require__(/*! ./shims */ "../node_modules/react-draggable/build/cjs/utils/shims.js");
var _domFns = __webpack_require__(/*! ./domFns */ "../node_modules/react-draggable/build/cjs/utils/domFns.js");
/*:: import type Draggable from '../Draggable';*/
/*:: import type {Bounds, ControlPosition, DraggableData, MouseTouchEvent} from './types';*/
/*:: import type DraggableCore from '../DraggableCore';*/
function getBoundPosition(draggable /*: Draggable*/, x /*: number*/, y /*: number*/) /*: [number, number]*/{
  // If no bounds, short-circuit and move on
  if (!draggable.props.bounds) return [x, y];

  // Clone new bounds
  let {
    bounds
  } = draggable.props;
  bounds = typeof bounds === 'string' ? bounds : cloneBounds(bounds);
  const node = findDOMNode(draggable);
  if (typeof bounds === 'string') {
    const {
      ownerDocument
    } = node;
    const ownerWindow = ownerDocument.defaultView;
    let boundNode;
    if (bounds === 'parent') {
      boundNode = node.parentNode;
    } else {
      boundNode = ownerDocument.querySelector(bounds);
    }
    if (!(boundNode instanceof ownerWindow.HTMLElement)) {
      throw new Error('Bounds selector "' + bounds + '" could not find an element.');
    }
    const boundNodeEl /*: HTMLElement*/ = boundNode; // for Flow, can't seem to refine correctly
    const nodeStyle = ownerWindow.getComputedStyle(node);
    const boundNodeStyle = ownerWindow.getComputedStyle(boundNodeEl);
    // Compute bounds. This is a pain with padding and offsets but this gets it exactly right.
    bounds = {
      left: -node.offsetLeft + (0, _shims.int)(boundNodeStyle.paddingLeft) + (0, _shims.int)(nodeStyle.marginLeft),
      top: -node.offsetTop + (0, _shims.int)(boundNodeStyle.paddingTop) + (0, _shims.int)(nodeStyle.marginTop),
      right: (0, _domFns.innerWidth)(boundNodeEl) - (0, _domFns.outerWidth)(node) - node.offsetLeft + (0, _shims.int)(boundNodeStyle.paddingRight) - (0, _shims.int)(nodeStyle.marginRight),
      bottom: (0, _domFns.innerHeight)(boundNodeEl) - (0, _domFns.outerHeight)(node) - node.offsetTop + (0, _shims.int)(boundNodeStyle.paddingBottom) - (0, _shims.int)(nodeStyle.marginBottom)
    };
  }

  // Keep x and y below right and bottom limits...
  if ((0, _shims.isNum)(bounds.right)) x = Math.min(x, bounds.right);
  if ((0, _shims.isNum)(bounds.bottom)) y = Math.min(y, bounds.bottom);

  // But above left and top limits.
  if ((0, _shims.isNum)(bounds.left)) x = Math.max(x, bounds.left);
  if ((0, _shims.isNum)(bounds.top)) y = Math.max(y, bounds.top);
  return [x, y];
}
function snapToGrid(grid /*: [number, number]*/, pendingX /*: number*/, pendingY /*: number*/) /*: [number, number]*/{
  const x = Math.round(pendingX / grid[0]) * grid[0];
  const y = Math.round(pendingY / grid[1]) * grid[1];
  return [x, y];
}
function canDragX(draggable /*: Draggable*/) /*: boolean*/{
  return draggable.props.axis === 'both' || draggable.props.axis === 'x';
}
function canDragY(draggable /*: Draggable*/) /*: boolean*/{
  return draggable.props.axis === 'both' || draggable.props.axis === 'y';
}

// Get {x, y} positions from event.
function getControlPosition(e /*: MouseTouchEvent*/, touchIdentifier /*: ?number*/, draggableCore /*: DraggableCore*/) /*: ?ControlPosition*/{
  const touchObj = typeof touchIdentifier === 'number' ? (0, _domFns.getTouch)(e, touchIdentifier) : null;
  if (typeof touchIdentifier === 'number' && !touchObj) return null; // not the right touch
  const node = findDOMNode(draggableCore);
  // User can provide an offsetParent if desired.
  const offsetParent = draggableCore.props.offsetParent || node.offsetParent || node.ownerDocument.body;
  return (0, _domFns.offsetXYFromParent)(touchObj || e, offsetParent, draggableCore.props.scale);
}

// Create an data object exposed by <DraggableCore>'s events
function createCoreData(draggable /*: DraggableCore*/, x /*: number*/, y /*: number*/) /*: DraggableData*/{
  const isStart = !(0, _shims.isNum)(draggable.lastX);
  const node = findDOMNode(draggable);
  if (isStart) {
    // If this is our first move, use the x and y as last coords.
    return {
      node,
      deltaX: 0,
      deltaY: 0,
      lastX: x,
      lastY: y,
      x,
      y
    };
  } else {
    // Otherwise calculate proper values.
    return {
      node,
      deltaX: x - draggable.lastX,
      deltaY: y - draggable.lastY,
      lastX: draggable.lastX,
      lastY: draggable.lastY,
      x,
      y
    };
  }
}

// Create an data exposed by <Draggable>'s events
function createDraggableData(draggable /*: Draggable*/, coreData /*: DraggableData*/) /*: DraggableData*/{
  const scale = draggable.props.scale;
  return {
    node: coreData.node,
    x: draggable.state.x + coreData.deltaX / scale,
    y: draggable.state.y + coreData.deltaY / scale,
    deltaX: coreData.deltaX / scale,
    deltaY: coreData.deltaY / scale,
    lastX: draggable.state.x,
    lastY: draggable.state.y
  };
}

// A lot faster than stringify/parse
function cloneBounds(bounds /*: Bounds*/) /*: Bounds*/{
  return {
    left: bounds.left,
    top: bounds.top,
    right: bounds.right,
    bottom: bounds.bottom
  };
}
function findDOMNode(draggable /*: Draggable | DraggableCore*/) /*: HTMLElement*/{
  const node = draggable.findDOMNode();
  if (!node) {
    throw new Error('<DraggableCore>: Unmounted during event!');
  }
  // $FlowIgnore we can't assert on HTMLElement due to tests... FIXME
  return node;
}

/***/ }),

/***/ "../node_modules/react-draggable/build/cjs/utils/shims.js":
/*!****************************************************************!*\
  !*** ../node_modules/react-draggable/build/cjs/utils/shims.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.dontSetMe = dontSetMe;
exports.findInArray = findInArray;
exports.int = int;
exports.isFunction = isFunction;
exports.isNum = isNum;
// @credits https://gist.github.com/rogozhnikoff/a43cfed27c41e4e68cdc
function findInArray(array /*: Array<any> | TouchList*/, callback /*: Function*/) /*: any*/{
  for (let i = 0, length = array.length; i < length; i++) {
    if (callback.apply(callback, [array[i], i, array])) return array[i];
  }
}
function isFunction(func /*: any*/) /*: boolean %checks*/{
  // $FlowIgnore[method-unbinding]
  return typeof func === 'function' || Object.prototype.toString.call(func) === '[object Function]';
}
function isNum(num /*: any*/) /*: boolean %checks*/{
  return typeof num === 'number' && !isNaN(num);
}
function int(a /*: string*/) /*: number*/{
  return parseInt(a, 10);
}
function dontSetMe(props /*: Object*/, propName /*: string*/, componentName /*: string*/) /*: ?Error*/{
  if (props[propName]) {
    return new Error("Invalid prop ".concat(propName, " passed to ").concat(componentName, " - do not set this, set it on the child."));
  }
}

/***/ }),

/***/ "../node_modules/react-draggable/node_modules/clsx/dist/clsx.m.js":
/*!************************************************************************!*\
  !*** ../node_modules/react-draggable/node_modules/clsx/dist/clsx.m.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   clsx: () => (/* binding */ clsx),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e))for(t=0;t<e.length;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f);else for(t in e)e[t]&&(n&&(n+=" "),n+=t);return n}function clsx(){for(var e,t,f=0,n="";f<arguments.length;)(e=arguments[f++])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (clsx);

/***/ }),

/***/ "../node_modules/validator/lib/isFQDN.js":
/*!***********************************************!*\
  !*** ../node_modules/validator/lib/isFQDN.js ***!
  \***********************************************/
/***/ ((module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = isFQDN;
var _assertString = _interopRequireDefault(__webpack_require__(/*! ./util/assertString */ "../node_modules/validator/lib/util/assertString.js"));
var _merge = _interopRequireDefault(__webpack_require__(/*! ./util/merge */ "../node_modules/validator/lib/util/merge.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var default_fqdn_options = {
  require_tld: true,
  allow_underscores: false,
  allow_trailing_dot: false,
  allow_numeric_tld: false,
  allow_wildcard: false,
  ignore_max_length: false
};
function isFQDN(str, options) {
  (0, _assertString.default)(str);
  options = (0, _merge.default)(options, default_fqdn_options);

  /* Remove the optional trailing dot before checking validity */
  if (options.allow_trailing_dot && str[str.length - 1] === '.') {
    str = str.substring(0, str.length - 1);
  }

  /* Remove the optional wildcard before checking validity */
  if (options.allow_wildcard === true && str.indexOf('*.') === 0) {
    str = str.substring(2);
  }
  var parts = str.split('.');
  var tld = parts[parts.length - 1];
  if (options.require_tld) {
    // disallow fqdns without tld
    if (parts.length < 2) {
      return false;
    }
    if (!options.allow_numeric_tld && !/^([a-z\u00A1-\u00A8\u00AA-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]{2,}|xn[a-z0-9-]{2,})$/i.test(tld)) {
      return false;
    }

    // disallow spaces
    if (/\s/.test(tld)) {
      return false;
    }
  }

  // reject numeric TLDs
  if (!options.allow_numeric_tld && /^\d+$/.test(tld)) {
    return false;
  }
  return parts.every(function (part) {
    if (part.length > 63 && !options.ignore_max_length) {
      return false;
    }
    if (!/^[a-z_\u00a1-\uffff0-9-]+$/i.test(part)) {
      return false;
    }

    // disallow full-width chars
    if (/[\uff01-\uff5e]/.test(part)) {
      return false;
    }

    // disallow parts starting or ending with hyphen
    if (/^-|-$/.test(part)) {
      return false;
    }
    if (!options.allow_underscores && /_/.test(part)) {
      return false;
    }
    return true;
  });
}
module.exports = exports.default;
module.exports["default"] = exports.default;

/***/ }),

/***/ "../node_modules/validator/lib/isIP.js":
/*!*********************************************!*\
  !*** ../node_modules/validator/lib/isIP.js ***!
  \*********************************************/
/***/ ((module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = isIP;
var _assertString = _interopRequireDefault(__webpack_require__(/*! ./util/assertString */ "../node_modules/validator/lib/util/assertString.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
/**
11.3.  Examples

   The following addresses

             fe80::1234 (on the 1st link of the node)
             ff02::5678 (on the 5th link of the node)
             ff08::9abc (on the 10th organization of the node)

   would be represented as follows:

             fe80::1234%1
             ff02::5678%5
             ff08::9abc%10

   (Here we assume a natural translation from a zone index to the
   <zone_id> part, where the Nth zone of any scope is translated into
   "N".)

   If we use interface names as <zone_id>, those addresses could also be
   represented as follows:

            fe80::1234%ne0
            ff02::5678%pvc1.3
            ff08::9abc%interface10

   where the interface "ne0" belongs to the 1st link, "pvc1.3" belongs
   to the 5th link, and "interface10" belongs to the 10th organization.
 * * */
var IPv4SegmentFormat = '(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])';
var IPv4AddressFormat = "(".concat(IPv4SegmentFormat, "[.]){3}").concat(IPv4SegmentFormat);
var IPv4AddressRegExp = new RegExp("^".concat(IPv4AddressFormat, "$"));
var IPv6SegmentFormat = '(?:[0-9a-fA-F]{1,4})';
var IPv6AddressRegExp = new RegExp('^(' + "(?:".concat(IPv6SegmentFormat, ":){7}(?:").concat(IPv6SegmentFormat, "|:)|") + "(?:".concat(IPv6SegmentFormat, ":){6}(?:").concat(IPv4AddressFormat, "|:").concat(IPv6SegmentFormat, "|:)|") + "(?:".concat(IPv6SegmentFormat, ":){5}(?::").concat(IPv4AddressFormat, "|(:").concat(IPv6SegmentFormat, "){1,2}|:)|") + "(?:".concat(IPv6SegmentFormat, ":){4}(?:(:").concat(IPv6SegmentFormat, "){0,1}:").concat(IPv4AddressFormat, "|(:").concat(IPv6SegmentFormat, "){1,3}|:)|") + "(?:".concat(IPv6SegmentFormat, ":){3}(?:(:").concat(IPv6SegmentFormat, "){0,2}:").concat(IPv4AddressFormat, "|(:").concat(IPv6SegmentFormat, "){1,4}|:)|") + "(?:".concat(IPv6SegmentFormat, ":){2}(?:(:").concat(IPv6SegmentFormat, "){0,3}:").concat(IPv4AddressFormat, "|(:").concat(IPv6SegmentFormat, "){1,5}|:)|") + "(?:".concat(IPv6SegmentFormat, ":){1}(?:(:").concat(IPv6SegmentFormat, "){0,4}:").concat(IPv4AddressFormat, "|(:").concat(IPv6SegmentFormat, "){1,6}|:)|") + "(?::((?::".concat(IPv6SegmentFormat, "){0,5}:").concat(IPv4AddressFormat, "|(?::").concat(IPv6SegmentFormat, "){1,7}|:))") + ')(%[0-9a-zA-Z.]{1,})?$');
function isIP(ipAddress) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  (0, _assertString.default)(ipAddress);

  // accessing 'arguments' for backwards compatibility: isIP(ipAddress [, version])
  // eslint-disable-next-line prefer-rest-params
  var version = (_typeof(options) === 'object' ? options.version : arguments[1]) || '';
  if (!version) {
    return isIP(ipAddress, {
      version: 4
    }) || isIP(ipAddress, {
      version: 6
    });
  }
  if (version.toString() === '4') {
    return IPv4AddressRegExp.test(ipAddress);
  }
  if (version.toString() === '6') {
    return IPv6AddressRegExp.test(ipAddress);
  }
  return false;
}
module.exports = exports.default;
module.exports["default"] = exports.default;

/***/ }),

/***/ "../node_modules/validator/lib/isURL.js":
/*!**********************************************!*\
  !*** ../node_modules/validator/lib/isURL.js ***!
  \**********************************************/
/***/ ((module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = isURL;
var _assertString = _interopRequireDefault(__webpack_require__(/*! ./util/assertString */ "../node_modules/validator/lib/util/assertString.js"));
var _checkHost = _interopRequireDefault(__webpack_require__(/*! ./util/checkHost */ "../node_modules/validator/lib/util/checkHost.js"));
var _includesString = _interopRequireDefault(__webpack_require__(/*! ./util/includesString */ "../node_modules/validator/lib/util/includesString.js"));
var _isFQDN = _interopRequireDefault(__webpack_require__(/*! ./isFQDN */ "../node_modules/validator/lib/isFQDN.js"));
var _isIP = _interopRequireDefault(__webpack_require__(/*! ./isIP */ "../node_modules/validator/lib/isIP.js"));
var _merge = _interopRequireDefault(__webpack_require__(/*! ./util/merge */ "../node_modules/validator/lib/util/merge.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
/*
options for isURL method

protocols - valid protocols can be modified with this option.
require_tld - If set to false isURL will not check if the URL's host includes a top-level domain.
require_protocol - if set to true isURL will return false if protocol is not present in the URL.
require_host - if set to false isURL will not check if host is present in the URL.
require_port - if set to true isURL will check if port is present in the URL.
require_valid_protocol - isURL will check if the URL's protocol is present in the protocols option.
allow_underscores - if set to true, the validator will allow underscores in the URL.
host_whitelist - if set to an array of strings or regexp, and the domain matches none of the strings
                 defined in it, the validation fails.
host_blacklist - if set to an array of strings or regexp, and the domain matches any of the strings
                 defined in it, the validation fails.
allow_trailing_dot - if set to true, the validator will allow the domain to end with
                     a `.` character.
allow_protocol_relative_urls - if set to true protocol relative URLs will be allowed.
allow_fragments - if set to false isURL will return false if fragments are present.
allow_query_components - if set to false isURL will return false if query components are present.
disallow_auth - if set to true, the validator will fail if the URL contains an authentication
                component, e.g. `http://username:password@example.com`
validate_length - if set to false isURL will skip string length validation. `max_allowed_length`
                  will be ignored if this is set as `false`.
max_allowed_length - if set, isURL will not allow URLs longer than the specified value (default is
                     2084 that IE maximum URL length).

*/

var default_url_options = {
  protocols: ['http', 'https', 'ftp'],
  require_tld: true,
  require_protocol: false,
  require_host: true,
  require_port: false,
  require_valid_protocol: true,
  allow_underscores: false,
  allow_trailing_dot: false,
  allow_protocol_relative_urls: false,
  allow_fragments: true,
  allow_query_components: true,
  validate_length: true,
  max_allowed_length: 2084
};
var wrapped_ipv6 = /^\[([^\]]+)\](?::([0-9]+))?$/;
function isURL(url, options) {
  (0, _assertString.default)(url);
  if (!url || /[\s<>]/.test(url)) {
    return false;
  }
  if (url.indexOf('mailto:') === 0) {
    return false;
  }
  options = (0, _merge.default)(options, default_url_options);
  if (options.validate_length && url.length > options.max_allowed_length) {
    return false;
  }
  if (!options.allow_fragments && (0, _includesString.default)(url, '#')) {
    return false;
  }
  if (!options.allow_query_components && ((0, _includesString.default)(url, '?') || (0, _includesString.default)(url, '&'))) {
    return false;
  }
  var protocol, auth, host, hostname, port, port_str, split, ipv6;
  split = url.split('#');
  url = split.shift();
  split = url.split('?');
  url = split.shift();
  split = url.split('://');
  if (split.length > 1) {
    protocol = split.shift().toLowerCase();
    if (options.require_valid_protocol && options.protocols.indexOf(protocol) === -1) {
      return false;
    }
  } else if (options.require_protocol) {
    return false;
  } else if (url.slice(0, 2) === '//') {
    if (!options.allow_protocol_relative_urls) {
      return false;
    }
    split[0] = url.slice(2);
  }
  url = split.join('://');
  if (url === '') {
    return false;
  }
  split = url.split('/');
  url = split.shift();
  if (url === '' && !options.require_host) {
    return true;
  }
  split = url.split('@');
  if (split.length > 1) {
    if (options.disallow_auth) {
      return false;
    }
    if (split[0] === '') {
      return false;
    }
    auth = split.shift();
    if (auth.indexOf(':') >= 0 && auth.split(':').length > 2) {
      return false;
    }
    var _auth$split = auth.split(':'),
      _auth$split2 = _slicedToArray(_auth$split, 2),
      user = _auth$split2[0],
      password = _auth$split2[1];
    if (user === '' && password === '') {
      return false;
    }
  }
  hostname = split.join('@');
  port_str = null;
  ipv6 = null;
  var ipv6_match = hostname.match(wrapped_ipv6);
  if (ipv6_match) {
    host = '';
    ipv6 = ipv6_match[1];
    port_str = ipv6_match[2] || null;
  } else {
    split = hostname.split(':');
    host = split.shift();
    if (split.length) {
      port_str = split.join(':');
    }
  }
  if (port_str !== null && port_str.length > 0) {
    port = parseInt(port_str, 10);
    if (!/^[0-9]+$/.test(port_str) || port <= 0 || port > 65535) {
      return false;
    }
  } else if (options.require_port) {
    return false;
  }
  if (options.host_whitelist) {
    return (0, _checkHost.default)(host, options.host_whitelist);
  }
  if (host === '' && !options.require_host) {
    return true;
  }
  if (!(0, _isIP.default)(host) && !(0, _isFQDN.default)(host, options) && (!ipv6 || !(0, _isIP.default)(ipv6, 6))) {
    return false;
  }
  host = host || ipv6;
  if (options.host_blacklist && (0, _checkHost.default)(host, options.host_blacklist)) {
    return false;
  }
  return true;
}
module.exports = exports.default;
module.exports["default"] = exports.default;

/***/ }),

/***/ "../node_modules/validator/lib/util/assertString.js":
/*!**********************************************************!*\
  !*** ../node_modules/validator/lib/util/assertString.js ***!
  \**********************************************************/
/***/ ((module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = assertString;
function assertString(input) {
  if (input === undefined || input === null) throw new TypeError("Expected a string but received a ".concat(input));
  if (input.constructor.name !== 'String') throw new TypeError("Expected a string but received a ".concat(input.constructor.name));
}
module.exports = exports.default;
module.exports["default"] = exports.default;

/***/ }),

/***/ "../node_modules/validator/lib/util/checkHost.js":
/*!*******************************************************!*\
  !*** ../node_modules/validator/lib/util/checkHost.js ***!
  \*******************************************************/
/***/ ((module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = checkHost;
function isRegExp(obj) {
  return Object.prototype.toString.call(obj) === '[object RegExp]';
}
function checkHost(host, matches) {
  for (var i = 0; i < matches.length; i++) {
    var match = matches[i];
    if (host === match || isRegExp(match) && match.test(host)) {
      return true;
    }
  }
  return false;
}
module.exports = exports.default;
module.exports["default"] = exports.default;

/***/ }),

/***/ "../node_modules/validator/lib/util/includesString.js":
/*!************************************************************!*\
  !*** ../node_modules/validator/lib/util/includesString.js ***!
  \************************************************************/
/***/ ((module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var includes = function includes(str, val) {
  return str.indexOf(val) !== -1;
};
var _default = exports["default"] = includes;
module.exports = exports.default;
module.exports["default"] = exports.default;

/***/ }),

/***/ "../node_modules/validator/lib/util/merge.js":
/*!***************************************************!*\
  !*** ../node_modules/validator/lib/util/merge.js ***!
  \***************************************************/
/***/ ((module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = merge;
function merge() {
  var obj = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var defaults = arguments.length > 1 ? arguments[1] : undefined;
  for (var key in defaults) {
    if (typeof obj[key] === 'undefined') {
      obj[key] = defaults[key];
    }
  }
  return obj;
}
module.exports = exports.default;
module.exports["default"] = exports.default;

/***/ }),

/***/ "@elementor/icons":
/*!************************************!*\
  !*** external "elementorV2.icons" ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.icons;

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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
var exports = __webpack_exports__;
/*!*******************************************************!*\
  !*** ../modules/ai/assets/js/editor/layout-module.js ***!
  \*******************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.AI_ATTACHMENT = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _aiLayoutBehavior = _interopRequireDefault(__webpack_require__(/*! ./ai-layout-behavior */ "../modules/ai/assets/js/editor/ai-layout-behavior.js"));
var _editorIntegration = __webpack_require__(/*! ./utils/editor-integration */ "../modules/ai/assets/js/editor/utils/editor-integration.js");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _config = __webpack_require__(/*! ./pages/form-layout/context/config */ "../modules/ai/assets/js/editor/pages/form-layout/context/config.js");
var _applyTemplateForAiBehavior = _interopRequireDefault(__webpack_require__(/*! ./integration/library/apply-template-for-ai-behavior */ "../modules/ai/assets/js/editor/integration/library/apply-template-for-ai-behavior.js"));
var _attachments = __webpack_require__(/*! ./pages/form-layout/components/attachments */ "../modules/ai/assets/js/editor/pages/form-layout/components/attachments.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var AI_ATTACHMENT = exports.AI_ATTACHMENT = 'ai-attachment';
var Module = exports["default"] = /*#__PURE__*/function (_elementorModules$edi) {
  function Module() {
    var _this;
    (0, _classCallCheck2.default)(this, Module);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Module, [].concat(args));
    (0, _defineProperty2.default)(_this, "registerVariationsContextMenu", function (groups, currentElement) {
      var saveGroup = groups.find(function (group) {
        return 'save' === group.name;
      });
      if (!saveGroup) {
        return groups;
      }
      var contextMenu = {
        name: 'ai',
        icon: 'eicon-ai',
        isEnabled: function isEnabled() {
          return 0 !== currentElement.getContainer().children.length;
        },
        title: (0, _i18n.__)('Generate variations with AI', 'elementor'),
        callback: function () {
          var _callback = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
            var container, json, attachments;
            return _regenerator.default.wrap(function (_context) {
              while (1) switch (_context.prev = _context.next) {
                case 0:
                  container = currentElement.getContainer();
                  json = container.model.toJSON({
                    remove: ['default']
                  });
                  attachments = [{
                    type: 'json',
                    previewHTML: '',
                    content: json,
                    label: container.model.get('title'),
                    source: _attachments.USER_VARIATION_SOURCE
                  }];
                  (0, _editorIntegration.renderLayoutApp)({
                    parentContainer: container.parent,
                    mode: _config.MODE_VARIATION,
                    at: container.view._index,
                    attachments: attachments,
                    onSelect: function onSelect() {
                      container.view.$el.hide();
                    },
                    onClose: function onClose() {
                      container.view.$el.show();
                    },
                    onInsert: function onInsert(template) {
                      (0, _editorIntegration.importToEditor)({
                        parentContainer: container.parent,
                        at: container.view._index,
                        template: template,
                        historyTitle: (0, _i18n.__)('AI Variation', 'elementor'),
                        replace: true
                      });
                    }
                  });
                case 1:
                case "end":
                  return _context.stop();
              }
            }, _callee);
          }));
          function callback() {
            return _callback.apply(this, arguments);
          }
          return callback;
        }()
      };

      // Add on top of save group actions
      saveGroup.actions.unshift(contextMenu);
      return groups;
    });
    return _this;
  }
  (0, _inherits2.default)(Module, _elementorModules$edi);
  return (0, _createClass2.default)(Module, [{
    key: "onElementorInit",
    value: function onElementorInit() {
      var _this2 = this;
      elementor.hooks.addFilter('views/add-section/behaviors', this.registerAiLayoutBehavior);
      elementor.hooks.addFilter('elements/container/contextMenuGroups', this.registerVariationsContextMenu);
      elementor.hooks.addFilter('elementor/editor/template-library/template/behaviors', this.registerLibraryActionButtonBehavior);
      elementor.hooks.addFilter('elementor/editor/template-library/template/action-button', this.filterLibraryActionButtonTemplate, 11);
      $e.commands.register('library', 'generate-ai-variation', function (args) {
        return _this2.applyTemplate(args);
      });
    }
  }, {
    key: "applyTemplate",
    value: function applyTemplate(args) {
      window.postMessage({
        type: 'library/attach:start'
      });
      $e.components.get('library').downloadTemplate(args, function (data) {
        var model = args.model;
        window.postMessage({
          type: 'library/attach',
          json: data.content[0],
          html: "<img src=\"".concat(model.get('thumbnail'), "\" />"),
          label: "".concat(model.get('template_id'), " - ").concat(model.get('title')),
          source: _attachments.ELEMENTOR_LIBRARY_SOURCE
        }, window.location.origin);
      });
    }
  }, {
    key: "registerLibraryActionButtonBehavior",
    value: function registerLibraryActionButtonBehavior(behaviors) {
      behaviors.applyAiTemplate = {
        behaviorClass: _applyTemplateForAiBehavior.default
      };
      return behaviors;
    }
  }, {
    key: "registerAiLayoutBehavior",
    value: function registerAiLayoutBehavior(behaviors) {
      behaviors.ai = {
        behaviorClass: _aiLayoutBehavior.default,
        context: {
          documentType: window.elementor.documents.getCurrent().config.type
        }
      };
      return behaviors;
    }
  }, {
    key: "filterLibraryActionButtonTemplate",
    value: function filterLibraryActionButtonTemplate(viewId) {
      var modalConfig = $e.components.get('library').manager.modalConfig;
      var originalCoreViewId = '#tmpl-elementor-template-library-insert-button';
      if (originalCoreViewId !== viewId) {
        return viewId;
      }
      if ($e.routes.current.library !== 'library/templates/blocks') {
        return viewId;
      }
      if (AI_ATTACHMENT === modalConfig.mode) {
        viewId = '#tmpl-elementor-template-library-apply-ai-button';
      } else {
        viewId = '#tmpl-elementor-template-library-insert-and-ai-variations-buttons';
      }
      return viewId;
    }
  }]);
}(elementorModules.editor.utils.Module);
new Module();
})();

/******/ })()
;
//# sourceMappingURL=ai-layout.js.map