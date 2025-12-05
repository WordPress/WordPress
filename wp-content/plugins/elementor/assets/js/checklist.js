/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../app/assets/js/hooks/use-ajax.js":
/*!******************************************!*\
  !*** ../app/assets/js/hooks/use-ajax.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useAjax;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function useAjax() {
  var _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    ajax = _useState2[0],
    setAjax = _useState2[1],
    initialStatusKey = 'initial',
    uploadInitialState = {
      status: initialStatusKey,
      isComplete: false,
      response: null
    },
    _useState3 = (0, _react.useState)(uploadInitialState),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    ajaxState = _useState4[0],
    setAjaxState = _useState4[1],
    ajaxActions = {
      reset: function reset() {
        return setAjaxState(initialStatusKey);
      }
    };
  var runRequest = /*#__PURE__*/function () {
    var _ref = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(config) {
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            return _context.abrupt("return", new Promise(function (resolve, reject) {
              var formData = new FormData();
              if (config.data) {
                for (var key in config.data) {
                  formData.append(key, config.data[key]);
                }
                if (!config.data.nonce) {
                  formData.append('_nonce', elementorCommon.config.ajax.nonce);
                }
              }
              var options = _objectSpread(_objectSpread({
                type: 'post',
                url: elementorCommon.config.ajax.url,
                headers: {},
                cache: false,
                contentType: false,
                processData: false
              }, config), {}, {
                data: formData,
                success: function success(response) {
                  resolve(response);
                },
                error: function error(_error) {
                  reject(_error);
                }
              });
              jQuery.ajax(options);
            }));
          case 1:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function runRequest(_x) {
      return _ref.apply(this, arguments);
    };
  }();
  (0, _react.useEffect)(function () {
    if (ajax) {
      runRequest(ajax).then(function (response) {
        var status = response.success ? 'success' : 'error';
        setAjaxState(function (prevState) {
          return _objectSpread(_objectSpread({}, prevState), {}, {
            status: status,
            response: response === null || response === void 0 ? void 0 : response.data
          });
        });
      }).catch(function (error) {
        var _error$responseJSON;
        var response = 408 === error.status ? 'timeout' : (_error$responseJSON = error.responseJSON) === null || _error$responseJSON === void 0 ? void 0 : _error$responseJSON.data;
        setAjaxState(function (prevState) {
          return _objectSpread(_objectSpread({}, prevState), {}, {
            status: 'error',
            response: response
          });
        });
      }).finally(function () {
        setAjaxState(function (prevState) {
          return _objectSpread(_objectSpread({}, prevState), {}, {
            isComplete: true
          });
        });
      });
    }
  }, [ajax]);
  return {
    ajax: ajax,
    setAjax: setAjax,
    ajaxState: ajaxState,
    ajaxActions: ajaxActions,
    runRequest: runRequest
  };
}

/***/ }),

/***/ "../modules/checklist/assets/js/app/app.js":
/*!*************************************************!*\
  !*** ../modules/checklist/assets/js/app/app.js ***!
  \*************************************************/
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
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _query = __webpack_require__(/*! @elementor/query */ "@elementor/query");
var _editorV1Adapters = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
var _checklist = _interopRequireDefault(__webpack_require__(/*! ./components/checklist */ "../modules/checklist/assets/js/app/components/checklist.js"));
var _functions = __webpack_require__(/*! ../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var App = function App() {
  var isRTL = elementorCommon.config.isRTL,
    _useQuery = (0, _query.useQuery)({
      queryKey: ['steps'],
      queryFn: _functions.fetchSteps,
      gcTime: 0,
      enabled: false
    }),
    stepsError = _useQuery.error,
    steps = _useQuery.data,
    refetchSteps = _useQuery.refetch,
    _useQuery2 = (0, _query.useQuery)({
      queryKey: ['statusData'],
      queryFn: _functions.fetchUserProgress,
      gcTime: 0,
      enabled: false
    }),
    userProgressError = _useQuery2.error,
    userProgress = _useQuery2.data,
    refetchUserProgress = _useQuery2.refetch;
  var fetchData = function fetchData() {
    refetchSteps();
    refetchUserProgress();
  };
  (0, _react.useEffect)(function () {
    fetchData();
    return (0, _editorV1Adapters.__privateListenTo)((0, _editorV1Adapters.commandEndEvent)('document/save/save'), function (_ref) {
      var _args$document;
      var args = _ref.args;
      if ('kit' === (args === null || args === void 0 || (_args$document = args.document) === null || _args$document === void 0 || (_args$document = _args$document.config) === null || _args$document === void 0 ? void 0 : _args$document.type)) {
        fetchData();
      }
    });
  }, []);
  if (userProgressError || !userProgress || stepsError || !(steps !== null && steps !== void 0 && steps.length)) {
    return null;
  }
  return /*#__PURE__*/_react.default.createElement(_ui.DirectionProvider, {
    rtl: isRTL
  }, /*#__PURE__*/_react.default.createElement(_ui.ThemeProvider, {
    colorScheme: "light"
  }, /*#__PURE__*/_react.default.createElement(_checklist.default, {
    steps: (0, _toConsumableArray2.default)(steps),
    userProgress: userProgress
  })));
};
var _default = exports["default"] = App;

/***/ }),

/***/ "../modules/checklist/assets/js/app/components/checklist-card-content.js":
/*!*******************************************************************************!*\
  !*** ../modules/checklist/assets/js/app/components/checklist-card-content.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _functions = __webpack_require__(/*! ../../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
var _consts = __webpack_require__(/*! ../../utils/consts */ "../modules/checklist/assets/js/utils/consts.js");
var IS_MARKED_COMPLETED = _consts.STEP.IS_MARKED_COMPLETED,
  IS_ABSOLUTE_COMPLETED = _consts.STEP.IS_ABSOLUTE_COMPLETED,
  IS_IMMUTABLE_COMPLETED = _consts.STEP.IS_IMMUTABLE_COMPLETED;
var DONE = _consts.MIXPANEL_CHECKLIST_STEPS.DONE,
  UNDONE = _consts.MIXPANEL_CHECKLIST_STEPS.UNDONE,
  ACTION = _consts.MIXPANEL_CHECKLIST_STEPS.ACTION,
  UPGRADE = _consts.MIXPANEL_CHECKLIST_STEPS.UPGRADE;
var ChecklistCardContent = function ChecklistCardContent(_ref) {
  var step = _ref.step,
    setSteps = _ref.setSteps;
  var _step$config = step.config,
    id = _step$config.id,
    description = _step$config.description,
    learnMoreUrl = _step$config.learn_more_url,
    learnMoreText = _step$config.learn_more_text,
    imageSrc = _step$config.image_src,
    promotionData = _step$config.promotion_data;
  var ctaText = promotionData ? (promotionData === null || promotionData === void 0 ? void 0 : promotionData.text) || (0, _i18n.__)('Upgrade Now', 'elementor') : step.config.cta_text,
    ctaUrl = promotionData ? promotionData.url : step.config.cta_url,
    isAbsoluteCompleted = step[IS_ABSOLUTE_COMPLETED],
    isImmutableCompleted = step[IS_IMMUTABLE_COMPLETED],
    isMarkedCompleted = step[IS_MARKED_COMPLETED],
    shouldShowMarkAsDone = !isAbsoluteCompleted && !isImmutableCompleted && !promotionData;
  var redirectHandler = /*#__PURE__*/function () {
    var _ref2 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            if (promotionData) {
              (0, _functions.addMixpanelTrackingChecklistSteps)(step.config.id, UPGRADE);
            } else {
              (0, _functions.addMixpanelTrackingChecklistSteps)(step.config.id, ACTION);
            }
            if (!(!elementor || !_consts.STEP_IDS_TO_COMPLETE_IN_EDITOR.includes(id) || !_consts.PANEL_ROUTES[id])) {
              _context.next = 1;
              break;
            }
            return _context.abrupt("return", window.open(ctaUrl, '_blank'));
          case 1:
            _context.next = 2;
            return $e.run('panel/global/open');
          case 2:
            $e.route(_consts.PANEL_ROUTES[id]);
          case 3:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function redirectHandler() {
      return _ref2.apply(this, arguments);
    };
  }();
  var toggleMarkAsDone = /*#__PURE__*/function () {
    var _ref3 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee2() {
      var currState, _t;
      return _regenerator.default.wrap(function (_context2) {
        while (1) switch (_context2.prev = _context2.next) {
          case 0:
            currState = isMarkedCompleted;
            if (isMarkedCompleted) {
              (0, _functions.addMixpanelTrackingChecklistSteps)(step.config.id, UNDONE);
            } else {
              (0, _functions.addMixpanelTrackingChecklistSteps)(step.config.id, DONE);
            }
            _context2.prev = 1;
            updateStepsState(IS_MARKED_COMPLETED, !currState);
            _context2.next = 2;
            return (0, _functions.updateStep)(id, (0, _defineProperty2.default)({}, IS_MARKED_COMPLETED, !currState));
          case 2:
            _context2.next = 4;
            break;
          case 3:
            _context2.prev = 3;
            _t = _context2["catch"](1);
            updateStepsState(IS_MARKED_COMPLETED, currState);
          case 4:
          case "end":
            return _context2.stop();
        }
      }, _callee2, null, [[1, 3]]);
    }));
    return function toggleMarkAsDone() {
      return _ref3.apply(this, arguments);
    };
  }();
  var updateStepsState = function updateStepsState(key, value) {
    setSteps(function (steps) {
      return steps.map(function (iteratedStep) {
        return (0, _functions.getAndUpdateStep)(step.config.id, iteratedStep, key, value);
      });
    });
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Card, {
    elevation: 0,
    square: true,
    "data-step-id": id
  }, /*#__PURE__*/_react.default.createElement(_ui.CardMedia, {
    image: imageSrc,
    sx: {
      height: 180
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.CardContent, null, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    color: "text.secondary",
    component: "p"
  }, description + ' ', /*#__PURE__*/_react.default.createElement(_ui.Link, {
    href: learnMoreUrl,
    target: "_blank",
    rel: "noreferrer",
    underline: "hover",
    color: "info.main",
    noWrap: true
  }, learnMoreText))), /*#__PURE__*/_react.default.createElement(_ui.CardActions, null, shouldShowMarkAsDone ? /*#__PURE__*/_react.default.createElement(_ui.Button, {
    size: "small",
    color: "secondary",
    variant: "text",
    onClick: toggleMarkAsDone
  }, isMarkedCompleted ? (0, _i18n.__)('Unmark as done', 'elementor') : (0, _i18n.__)('Mark as done', 'elementor')) : null, /*#__PURE__*/_react.default.createElement(_ui.Button, {
    color: promotionData ? 'promotion' : 'primary',
    size: "small",
    variant: "contained",
    onClick: redirectHandler
  }, ctaText)));
};
var _default = exports["default"] = ChecklistCardContent;
ChecklistCardContent.propTypes = {
  step: _propTypes.default.object.isRequired,
  setSteps: _propTypes.default.func.isRequired
};

/***/ }),

/***/ "../modules/checklist/assets/js/app/components/checklist-item.js":
/*!***********************************************************************!*\
  !*** ../modules/checklist/assets/js/app/components/checklist-item.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _checklistCardContent = _interopRequireDefault(__webpack_require__(/*! ./checklist-card-content */ "../modules/checklist/assets/js/app/components/checklist-card-content.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _icons = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
var _functions = __webpack_require__(/*! ../../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
var _consts = __webpack_require__(/*! ../../utils/consts */ "../modules/checklist/assets/js/utils/consts.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var PROMOTION_DATA = _consts.STEP.PROMOTION_DATA;
var TITLE = _consts.MIXPANEL_CHECKLIST_STEPS.TITLE,
  ACCORDION_SECTION = _consts.MIXPANEL_CHECKLIST_STEPS.ACCORDION_SECTION;
function CheckListItem(props) {
  var expandedIndex = props.expandedIndex,
    setExpandedIndex = props.setExpandedIndex,
    setSteps = props.setSteps,
    index = props.index,
    step = props.step,
    chevronStyle = index === expandedIndex ? {
      transform: 'rotate(180deg)'
    } : {},
    isChecked = (0, _functions.isStepChecked)(step),
    promotionData = step.config[PROMOTION_DATA];
  var handleExpandClick = function handleExpandClick() {
    (0, _functions.addMixpanelTrackingChecklistSteps)(step.config.id, TITLE, ACCORDION_SECTION);
    setExpandedIndex(index === expandedIndex ? -1 : index);
  };
  var getUpgradeIcon = function getUpgradeIcon() {
    return 'default' === (promotionData === null || promotionData === void 0 ? void 0 : promotionData.icon) ? /*#__PURE__*/_react.default.createElement(_icons.UpgradeIcon, {
      color: "promotion",
      sx: {
        mr: 1
      }
    }) : /*#__PURE__*/_react.default.createElement(_ui.SvgIcon, {
      color: "promotion",
      sx: {
        mr: 1
      }
    }, /*#__PURE__*/_react.default.createElement("img", {
      src: promotionData === null || promotionData === void 0 ? void 0 : promotionData.icon,
      alt: promotionData.iconAlt || ''
    }));
  };
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(_ui.ListItemButton, {
    onClick: handleExpandClick,
    "data-step-id": step.config.id,
    dense: true
  }, /*#__PURE__*/_react.default.createElement(_ui.ListItemIcon, null, /*#__PURE__*/_react.default.createElement(_ui.Checkbox, {
    "data-is-checked": isChecked,
    icon: /*#__PURE__*/_react.default.createElement(_icons.RadioButtonUncheckedIcon, null),
    checkedIcon: /*#__PURE__*/_react.default.createElement(_icons.CircleCheckFilledIcon, {
      color: "primary"
    }),
    edge: "start",
    checked: isChecked,
    tabIndex: -1,
    inputProps: {
      'aria-labelledby': step.config.title
    }
  })), /*#__PURE__*/_react.default.createElement(_ui.ListItemText, {
    primary: step.config.title,
    primaryTypographyProps: {
      variant: 'body2'
    }
  }), promotionData ? getUpgradeIcon() : null, /*#__PURE__*/_react.default.createElement(_icons.ChevronDownIcon, {
    sx: _objectSpread(_objectSpread({}, chevronStyle), {}, {
      transition: '300ms'
    })
  })), /*#__PURE__*/_react.default.createElement(_ui.Collapse, {
    in: index === expandedIndex
  }, /*#__PURE__*/_react.default.createElement(_checklistCardContent.default, {
    step: step,
    setSteps: setSteps
  })));
}
var _default = exports["default"] = CheckListItem;
CheckListItem.propTypes = {
  step: _propTypes.default.object.isRequired,
  expandedIndex: _propTypes.default.number,
  setExpandedIndex: _propTypes.default.func.isRequired,
  setSteps: _propTypes.default.func.isRequired,
  index: _propTypes.default.number.isRequired
};

/***/ }),

/***/ "../modules/checklist/assets/js/app/components/checklist-wrapper.js":
/*!**************************************************************************!*\
  !*** ../modules/checklist/assets/js/app/components/checklist-wrapper.js ***!
  \**************************************************************************/
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
var _checklistItem = _interopRequireDefault(__webpack_require__(/*! ./checklist-item */ "../modules/checklist/assets/js/app/components/checklist-item.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _successMessage = _interopRequireDefault(__webpack_require__(/*! ./success-message */ "../modules/checklist/assets/js/app/components/success-message.js"));
var _functions = __webpack_require__(/*! ../../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var ChecklistWrapper = function ChecklistWrapper(_ref) {
  var steps = _ref.steps,
    setSteps = _ref.setSteps,
    isMinimized = _ref.isMinimized;
  var _useState = (0, _react.useState)(-1),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    expandedIndex = _useState2[0],
    setExpandedIndex = _useState2[1];
  var isChecklistCompleted = steps.filter(_functions.isStepChecked).length === steps.length;
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      transition: '400ms',
      maxHeight: isMinimized ? 0 : '645px'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.List, {
    component: "div",
    sx: {
      py: 0
    }
  }, steps.map(function (step, index) {
    return /*#__PURE__*/_react.default.createElement(_react.Fragment, {
      key: index
    }, index ? /*#__PURE__*/_react.default.createElement(_ui.Divider, null) : null, /*#__PURE__*/_react.default.createElement(_checklistItem.default, {
      step: step,
      setSteps: setSteps,
      setExpandedIndex: setExpandedIndex,
      expandedIndex: expandedIndex,
      index: index
    }));
  })), isChecklistCompleted ? /*#__PURE__*/_react.default.createElement(_successMessage.default, null) : null);
};
var _default = exports["default"] = ChecklistWrapper;
ChecklistWrapper.propTypes = {
  steps: _propTypes.default.array.isRequired,
  setSteps: _propTypes.default.func.isRequired,
  isMinimized: _propTypes.default.bool.isRequired
};

/***/ }),

/***/ "../modules/checklist/assets/js/app/components/checklist.js":
/*!******************************************************************!*\
  !*** ../modules/checklist/assets/js/app/components/checklist.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _header = _interopRequireDefault(__webpack_require__(/*! ./header */ "../modules/checklist/assets/js/app/components/header.js"));
var _checklistWrapper = _interopRequireDefault(__webpack_require__(/*! ./checklist-wrapper */ "../modules/checklist/assets/js/app/components/checklist-wrapper.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _consts = __webpack_require__(/*! ../../utils/consts */ "../modules/checklist/assets/js/utils/consts.js");
var _functions = __webpack_require__(/*! ../../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t2 in e) "default" !== _t2 && {}.hasOwnProperty.call(e, _t2) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t2)) && (i.get || i.set) ? o(f, _t2, i) : f[_t2] = e[_t2]); return f; })(e, t); }
var IS_POPUP_MINIMIZED = _consts.USER_PROGRESS.IS_POPUP_MINIMIZED;
var Checklist = function Checklist(props) {
  var _useState = (0, _react.useState)(props.steps),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    steps = _useState2[0],
    setSteps = _useState2[1],
    _useState3 = (0, _react.useState)(!!props.userProgress[IS_POPUP_MINIMIZED]),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isMinimized = _useState4[0],
    setIsMinimized = _useState4[1];
  var toggleIsMinimized = /*#__PURE__*/function () {
    var _ref = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
      var currState, _t;
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            currState = isMinimized;
            _context.prev = 1;
            setIsMinimized(!currState);
            _context.next = 2;
            return (0, _functions.updateUserProgress)((0, _defineProperty2.default)({}, IS_POPUP_MINIMIZED, !currState));
          case 2:
            _context.next = 4;
            break;
          case 3:
            _context.prev = 3;
            _t = _context["catch"](1);
            setIsMinimized(currState);
          case 4:
          case "end":
            return _context.stop();
        }
      }, _callee, null, [[1, 3]]);
    }));
    return function toggleIsMinimized() {
      return _ref.apply(this, arguments);
    };
  }();
  (0, _react.useEffect)(function () {
    setSteps(props.steps);
  }, [props.steps]);
  return /*#__PURE__*/_react.default.createElement(_ui.Paper, {
    elevation: 5,
    sx: {
      position: 'fixed',
      width: '360px',
      bottom: '40px',
      insetInlineEnd: '40px',
      zIndex: '99999',
      hidden: true,
      maxHeight: '645px',
      overflowY: 'auto'
    }
  }, /*#__PURE__*/_react.default.createElement(_header.default, {
    steps: steps,
    isMinimized: isMinimized,
    toggleIsMinimized: toggleIsMinimized
  }), /*#__PURE__*/_react.default.createElement(_checklistWrapper.default, {
    steps: steps,
    setSteps: setSteps,
    isMinimized: isMinimized
  }));
};
Checklist.propTypes = {
  steps: PropTypes.array.isRequired,
  userProgress: PropTypes.object.isRequired
};
var _default = exports["default"] = Checklist;

/***/ }),

/***/ "../modules/checklist/assets/js/app/components/header.js":
/*!***************************************************************!*\
  !*** ../modules/checklist/assets/js/app/components/header.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _progress = _interopRequireDefault(__webpack_require__(/*! ./progress */ "../modules/checklist/assets/js/app/components/progress.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _query = __webpack_require__(/*! @elementor/query */ "@elementor/query");
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _functions = __webpack_require__(/*! ../../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
var _consts = __webpack_require__(/*! ../../utils/consts */ "../modules/checklist/assets/js/utils/consts.js");
var _icons = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var CHECKLIST_CLOSED_IN_THE_EDITOR_FOR_FIRST_TIME = _consts.USER_PROGRESS.CHECKLIST_CLOSED_IN_THE_EDITOR_FOR_FIRST_TIME;
var CHECKLIST_HEADER_CLOSE = _consts.MIXPANEL_CHECKLIST_STEPS.CHECKLIST_HEADER_CLOSE;
var Header = function Header(_ref) {
  var steps = _ref.steps,
    isMinimized = _ref.isMinimized,
    toggleIsMinimized = _ref.toggleIsMinimized;
  var _useQuery = (0, _query.useQuery)({
      queryKey: ['closedForFirstTime'],
      queryFn: _functions.fetchUserProgress
    }),
    userProgress = _useQuery.data,
    closedForFirstTime = (userProgress === null || userProgress === void 0 ? void 0 : userProgress[CHECKLIST_CLOSED_IN_THE_EDITOR_FOR_FIRST_TIME]) || false;
  var closeChecklist = /*#__PURE__*/function () {
    var _ref2 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            (0, _functions.addMixpanelTrackingChecklistHeader)(CHECKLIST_HEADER_CLOSE);
            if (closedForFirstTime) {
              _context.next = 2;
              break;
            }
            _context.next = 1;
            return (0, _functions.updateUserProgress)((0, _defineProperty2.default)({}, CHECKLIST_CLOSED_IN_THE_EDITOR_FOR_FIRST_TIME, true));
          case 1:
            window.dispatchEvent(new CustomEvent('elementor/checklist/first_close', {
              detail: {
                message: 'firstClose'
              }
            }));
          case 2:
            (0, _functions.toggleChecklistPopup)();
          case 3:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function closeChecklist() {
      return _ref2.apply(this, arguments);
    };
  }();
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_ui.AppBar, {
    elevation: 0,
    position: "sticky",
    sx: {
      p: 2,
      backgroundColor: 'background.default'
    }
  }, /*#__PURE__*/React.createElement(_ui.Toolbar, {
    variant: "dense",
    disableGutters: true
  }, /*#__PURE__*/React.createElement(_ui.Typography, {
    variant: "subtitle1",
    sx: {
      flexGrow: 1
    }
  }, (0, _i18n.__)('Let\'s make a productivity boost', 'elementor')), /*#__PURE__*/React.createElement(_ui.IconButton, {
    size: "small",
    onClick: toggleIsMinimized,
    "aria-expanded": !isMinimized
  }, isMinimized ? /*#__PURE__*/React.createElement(_icons.ExpandDiagonalIcon, null) : /*#__PURE__*/React.createElement(_icons.MinimizeDiagonalIcon, null)), /*#__PURE__*/React.createElement(_ui.CloseButton, {
    sx: {
      mr: -0.5
    },
    size: "small",
    onClick: closeChecklist
  })), /*#__PURE__*/React.createElement(_progress.default, {
    steps: steps
  })), /*#__PURE__*/React.createElement(_ui.Divider, null));
};
Header.propTypes = {
  steps: _propTypes.default.array.isRequired,
  isMinimized: _propTypes.default.bool.isRequired,
  toggleIsMinimized: _propTypes.default.func.isRequired
};
var _default = exports["default"] = Header;

/***/ }),

/***/ "../modules/checklist/assets/js/app/components/progress.js":
/*!*****************************************************************!*\
  !*** ../modules/checklist/assets/js/app/components/progress.js ***!
  \*****************************************************************/
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
var _functions = __webpack_require__(/*! ../../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
var Progress = function Progress(_ref) {
  var steps = _ref.steps;
  var progress = steps.filter(_functions.isStepChecked).length * 100 / steps.length;
  return /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      display: 'flex',
      alignItems: 'center',
      gap: 1
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      width: '100%'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.LinearProgress, {
    variant: "determinate",
    value: progress
  })), /*#__PURE__*/_react.default.createElement(_ui.Box, {
    sx: {
      width: 'fit-content'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    color: "text.secondary"
  }, "".concat(Math.round(progress), "%"))));
};
var _default = exports["default"] = Progress;
Progress.propTypes = {
  steps: _propTypes.default.array.isRequired
};

/***/ }),

/***/ "../modules/checklist/assets/js/app/components/reminder-modal.js":
/*!***********************************************************************!*\
  !*** ../modules/checklist/assets/js/app/components/reminder-modal.js ***!
  \***********************************************************************/
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
var ReminderModal = function ReminderModal(_ref) {
  var setOpen = _ref.setOpen;
  var closeChecklist = function closeChecklist(e) {
    e.stopPropagation();
    setOpen(false);
  };
  return /*#__PURE__*/_react.default.createElement(_ui.Card, {
    elevation: 0,
    sx: {
      maxWidth: 336
    },
    className: "e-checklist-infotip-first-time-closed"
  }, /*#__PURE__*/_react.default.createElement(_ui.CardContent, null, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "subtitle2",
    sx: {
      mb: 2
    }
  }, (0, _i18n.__)('Looking for your Launchpad Checklist?', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2"
  }, (0, _i18n.__)('Click the launch icon to continue setting up your site.', 'elementor'))), /*#__PURE__*/_react.default.createElement(_ui.CardActions, null, /*#__PURE__*/_react.default.createElement(_ui.Button, {
    size: "small",
    variant: "contained",
    className: "infotip-first-time-closed-button",
    onClick: closeChecklist
  }, (0, _i18n.__)('Got it', 'elementor'))));
};
var _default = exports["default"] = ReminderModal;
ReminderModal.propTypes = {
  setOpen: _propTypes.default.func.isRequired
};

/***/ }),

/***/ "../modules/checklist/assets/js/app/components/success-message.js":
/*!************************************************************************!*\
  !*** ../modules/checklist/assets/js/app/components/success-message.js ***!
  \************************************************************************/
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
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _functions = __webpack_require__(/*! ../../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
var _consts = __webpack_require__(/*! ../../utils/consts */ "../modules/checklist/assets/js/utils/consts.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var ACTION = _consts.MIXPANEL_CHECKLIST_STEPS.ACTION,
  WELL_DONE = _consts.MIXPANEL_CHECKLIST_STEPS.WELL_DONE;
var SuccessMessage = function SuccessMessage() {
  var _useAjax = (0, _useAjax2.default)(),
    ajaxState = _useAjax.ajaxState,
    setAjax = _useAjax.setAjax;
  var hideChecklist = function hideChecklist() {
    (0, _functions.addMixpanelTrackingChecklistSteps)(WELL_DONE, ACTION);
    setAjax({
      data: {
        action: 'elementor_ajax',
        actions: JSON.stringify({
          save_editorPreferences_settings: {
            action: 'save_editorPreferences_settings',
            data: {
              data: {
                show_launchpad_checklist: ''
              }
            }
          }
        })
      }
    });
  };
  (0, _react.useEffect)(function () {
    switch (ajaxState.status) {
      case 'success':
        setTimeout(function () {
          $e.commands.run('checklist/toggle-icon', false);
        }, 0);
        break;
      case 'error':
        break;
    }
  }, [ajaxState]);
  return /*#__PURE__*/_react.default.createElement(_ui.Card, {
    elevation: 0,
    square: true,
    className: "e-checklist-done"
  }, /*#__PURE__*/_react.default.createElement(_ui.CardMedia, {
    image: "https://assets.elementor.com/checklist/v1/images/checklist-step-7.jpg",
    sx: {
      height: 180
    }
  }), /*#__PURE__*/_react.default.createElement(_ui.CardContent, {
    sx: {
      textAlign: 'center'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "h6",
    color: "text.primary"
  }, (0, _i18n.__)('You\'re on your way!', 'elementor')), /*#__PURE__*/_react.default.createElement(_ui.Typography, {
    variant: "body2",
    color: "text.secondary",
    component: "p"
  }, (0, _i18n.__)('With these steps, you\'ve got a great base for a robust website. Enjoy your web creation journey!', 'elementor'))), /*#__PURE__*/_react.default.createElement(_ui.CardActions, {
    sx: {
      justifyContent: 'center'
    }
  }, /*#__PURE__*/_react.default.createElement(_ui.Button, {
    color: "primary",
    size: "small",
    variant: "contained",
    onClick: hideChecklist
  }, (0, _i18n.__)('Got it', 'elementor'))));
};
var _default = exports["default"] = SuccessMessage;

/***/ }),

/***/ "../modules/checklist/assets/js/commands-data/index.js":
/*!*************************************************************!*\
  !*** ../modules/checklist/assets/js/commands-data/index.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "Steps", ({
  enumerable: true,
  get: function get() {
    return _steps.Steps;
  }
}));
Object.defineProperty(exports, "UserProgress", ({
  enumerable: true,
  get: function get() {
    return _userProgress.UserProgress;
  }
}));
var _steps = __webpack_require__(/*! ./steps */ "../modules/checklist/assets/js/commands-data/steps.js");
var _userProgress = __webpack_require__(/*! ./user-progress */ "../modules/checklist/assets/js/commands-data/user-progress.js");

/***/ }),

/***/ "../modules/checklist/assets/js/commands-data/steps.js":
/*!*************************************************************!*\
  !*** ../modules/checklist/assets/js/commands-data/steps.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Steps = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Steps = exports.Steps = /*#__PURE__*/function (_$e$modules$CommandDa) {
  function Steps() {
    (0, _classCallCheck2.default)(this, Steps);
    return _callSuper(this, Steps, arguments);
  }
  (0, _inherits2.default)(Steps, _$e$modules$CommandDa);
  return (0, _createClass2.default)(Steps, null, [{
    key: "getEndpointFormat",
    value: function getEndpointFormat() {
      return 'checklist/steps/{id}';
    }
  }]);
}($e.modules.CommandData);
var _default = exports["default"] = Steps;

/***/ }),

/***/ "../modules/checklist/assets/js/commands-data/user-progress.js":
/*!*********************************************************************!*\
  !*** ../modules/checklist/assets/js/commands-data/user-progress.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.UserProgress = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var UserProgress = exports.UserProgress = /*#__PURE__*/function (_$e$modules$CommandDa) {
  function UserProgress() {
    (0, _classCallCheck2.default)(this, UserProgress);
    return _callSuper(this, UserProgress, arguments);
  }
  (0, _inherits2.default)(UserProgress, _$e$modules$CommandDa);
  return (0, _createClass2.default)(UserProgress, null, [{
    key: "getEndpointFormat",
    value: function getEndpointFormat() {
      return 'checklist/user-progress';
    }
  }]);
}($e.modules.CommandData);
var _default = exports["default"] = UserProgress;

/***/ }),

/***/ "../modules/checklist/assets/js/commands/index.js":
/*!********************************************************!*\
  !*** ../modules/checklist/assets/js/commands/index.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "ToggleIcon", ({
  enumerable: true,
  get: function get() {
    return _toggleIcon.ToggleIcon;
  }
}));
Object.defineProperty(exports, "TogglePopup", ({
  enumerable: true,
  get: function get() {
    return _togglePopup.TogglePopup;
  }
}));
var _togglePopup = __webpack_require__(/*! ./toggle-popup */ "../modules/checklist/assets/js/commands/toggle-popup.js");
var _toggleIcon = __webpack_require__(/*! ./toggle-icon */ "../modules/checklist/assets/js/commands/toggle-icon.js");

/***/ }),

/***/ "../modules/checklist/assets/js/commands/toggle-icon.js":
/*!**************************************************************!*\
  !*** ../modules/checklist/assets/js/commands/toggle-icon.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.ToggleIcon = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _togglePopup = _interopRequireDefault(__webpack_require__(/*! ./toggle-popup */ "../modules/checklist/assets/js/commands/toggle-popup.js"));
var _functions = __webpack_require__(/*! ../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var ToggleIcon = exports.ToggleIcon = /*#__PURE__*/function (_$e$modules$CommandBa) {
  function ToggleIcon() {
    (0, _classCallCheck2.default)(this, ToggleIcon);
    return _callSuper(this, ToggleIcon, arguments);
  }
  (0, _inherits2.default)(ToggleIcon, _$e$modules$CommandBa);
  return (0, _createClass2.default)(ToggleIcon, [{
    key: "apply",
    value: function apply(shouldShow) {
      document.body.querySelector('[aria-label="Checklist"]').parentElement.style.display = shouldShow ? 'block' : 'none';
      if (!shouldShow && _togglePopup.default.isOpen) {
        (0, _functions.toggleChecklistPopup)();
      }
    }
  }]);
}($e.modules.CommandBase);
(0, _defineProperty2.default)(ToggleIcon, "isSettingsOn", true);
var _default = exports["default"] = ToggleIcon;

/***/ }),

/***/ "../modules/checklist/assets/js/commands/toggle-popup.js":
/*!***************************************************************!*\
  !*** ../modules/checklist/assets/js/commands/toggle-popup.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.TogglePopup = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _app = _interopRequireDefault(__webpack_require__(/*! ../app/app */ "../modules/checklist/assets/js/app/app.js"));
var _query = __webpack_require__(/*! @elementor/query */ "@elementor/query");
var _client = _interopRequireDefault(__webpack_require__(/*! react-dom/client */ "../node_modules/react-dom/client.js"));
var _functions = __webpack_require__(/*! ../utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
var _consts = __webpack_require__(/*! ../utils/consts */ "../modules/checklist/assets/js/utils/consts.js");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var queryClient = new _query.QueryClient();
var TogglePopup = exports.TogglePopup = /*#__PURE__*/function (_$e$modules$CommandBa) {
  function TogglePopup() {
    (0, _classCallCheck2.default)(this, TogglePopup);
    return _callSuper(this, TogglePopup, arguments);
  }
  (0, _inherits2.default)(TogglePopup, _$e$modules$CommandBa);
  return (0, _createClass2.default)(TogglePopup, [{
    key: "apply",
    value: function apply(args) {
      if (!TogglePopup.isOpen) {
        this.mount();
      } else {
        this.unmount();
      }
      TogglePopup.isOpen = !TogglePopup.isOpen;
      args.isOpen = TogglePopup.isOpen;
      (0, _functions.updateUserProgress)((0, _defineProperty2.default)({}, _consts.USER_PROGRESS.LAST_OPENED_TIMESTAMP, TogglePopup.isOpen));
    }
  }, {
    key: "mount",
    value: function mount() {
      this.setRootElement();
      TogglePopup.rootElement.render(/*#__PURE__*/_react.default.createElement(_query.QueryClientProvider, {
        client: queryClient
      }, /*#__PURE__*/_react.default.createElement(_app.default, null)));
    }
  }, {
    key: "unmount",
    value: function unmount() {
      TogglePopup.rootElement.unmount();
      document.body.removeChild(document.body.querySelector('#e-checklist'));
    }
  }, {
    key: "setRootElement",
    value: function setRootElement() {
      var root = document.body.querySelector('#e-checklist');
      if (!root) {
        root = document.createElement('div');
        root.id = 'e-checklist';
        document.body.appendChild(root);
      }
      TogglePopup.rootElement = _client.default.createRoot(root);
    }
  }]);
}($e.modules.CommandBase);
(0, _defineProperty2.default)(TogglePopup, "rootElement", null);
(0, _defineProperty2.default)(TogglePopup, "isOpen", false);
var _default = exports["default"] = TogglePopup;

/***/ }),

/***/ "../modules/checklist/assets/js/component.js":
/*!***************************************************!*\
  !*** ../modules/checklist/assets/js/component.js ***!
  \***************************************************/
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
var commands = _interopRequireWildcard(__webpack_require__(/*! ./commands/ */ "../modules/checklist/assets/js/commands/index.js"));
var commandsData = _interopRequireWildcard(__webpack_require__(/*! ./commands-data/ */ "../modules/checklist/assets/js/commands-data/index.js"));
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
      return 'checklist';
    }
  }, {
    key: "defaultCommands",
    value: function defaultCommands() {
      return this.importCommands(commands);
    }
  }, {
    key: "defaultData",
    value: function defaultData() {
      return this.importCommands(commandsData);
    }
  }], [{
    key: "getEndpointFormat",
    value: function getEndpointFormat() {
      return 'checklist';
    }
  }]);
}($e.modules.ComponentBase);

/***/ }),

/***/ "../modules/checklist/assets/js/editor-app-bar-link.js":
/*!*************************************************************!*\
  !*** ../modules/checklist/assets/js/editor-app-bar-link.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.editorAppBarLink = void 0;
var EditorAppBar = _interopRequireWildcard(__webpack_require__(/*! @elementor/editor-app-bar */ "@elementor/editor-app-bar"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _topbarIcon = _interopRequireDefault(__webpack_require__(/*! ./topbar-icon */ "../modules/checklist/assets/js/topbar-icon.js"));
var _functions = __webpack_require__(/*! ./utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
var _query = __webpack_require__(/*! @elementor/query */ "@elementor/query");
var _commands = __webpack_require__(/*! ./commands */ "../modules/checklist/assets/js/commands/index.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var queryClient = new _query.QueryClient();
var editorAppBarLink = exports.editorAppBarLink = function editorAppBarLink() {
  var utilitiesMenu = EditorAppBar.utilitiesMenu;
  utilitiesMenu.registerLink({
    id: 'app-bar-menu-item-checklist',
    priority: 5,
    useProps: function useProps() {
      return {
        title: (0, _i18n.__)('Checklist', 'elementor'),
        icon: function icon() {
          return /*#__PURE__*/React.createElement(_query.QueryClientProvider, {
            client: queryClient
          }, /*#__PURE__*/React.createElement(_topbarIcon.default, null));
        },
        onClick: function onClick() {
          (0, _functions.addMixpanelTrackingChecklistTopBar)(_commands.TogglePopup.isOpen);
          (0, _functions.toggleChecklistPopup)();
        }
      };
    }
  });
};

/***/ }),

/***/ "../modules/checklist/assets/js/topbar-icon.js":
/*!*****************************************************!*\
  !*** ../modules/checklist/assets/js/topbar-icon.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var React = _react;
var _query = __webpack_require__(/*! @elementor/query */ "@elementor/query");
var _editorV1Adapters = __webpack_require__(/*! @elementor/editor-v1-adapters */ "@elementor/editor-v1-adapters");
var _RocketIcon = _interopRequireDefault(__webpack_require__(/*! @elementor/icons/RocketIcon */ "@elementor/icons/RocketIcon"));
var _ui = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
var _reminderModal = _interopRequireDefault(__webpack_require__(/*! ./app/components/reminder-modal */ "../modules/checklist/assets/js/app/components/reminder-modal.js"));
var _consts = __webpack_require__(/*! ./utils/consts */ "../modules/checklist/assets/js/utils/consts.js");
var _functions = __webpack_require__(/*! ./utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var CHECKLIST_CLOSED_IN_THE_EDITOR_FOR_FIRST_TIME = _consts.USER_PROGRESS.CHECKLIST_CLOSED_IN_THE_EDITOR_FOR_FIRST_TIME;
var TopBarIcon = function TopBarIcon() {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    hasRoot = _useState2[0],
    setHasRoot = _useState2[1],
    _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    open = _useState4[0],
    setOpen = _useState4[1],
    _useQuery = (0, _query.useQuery)({
      queryKey: ['closedForFirstTime'],
      queryFn: _functions.fetchUserProgress
    }),
    error = _useQuery.error,
    userProgress = _useQuery.data,
    closedForFirstTime = userProgress === null || userProgress === void 0 ? void 0 : userProgress[CHECKLIST_CLOSED_IN_THE_EDITOR_FOR_FIRST_TIME];
  (0, _react.useEffect)(function () {
    return (0, _editorV1Adapters.__privateListenTo)((0, _editorV1Adapters.commandEndEvent)('checklist/toggle-popup'), function (e) {
      setHasRoot(e.args.isOpen);
    });
  }, [hasRoot]);
  (0, _react.useEffect)(function () {
    var handleFirstClosed = function handleFirstClosed() {
      setOpen(true);
    };
    window.addEventListener('elementor/checklist/first_close', handleFirstClosed);
    return function () {
      window.removeEventListener('elementor/checklist/first_close', handleFirstClosed);
    };
  }, []);
  if (error) {
    return null;
  }
  return hasRoot && !closedForFirstTime ? /*#__PURE__*/React.createElement(_RocketIcon.default, null) : /*#__PURE__*/React.createElement(_ui.Infotip, {
    placement: "bottom-start",
    content: /*#__PURE__*/React.createElement(_reminderModal.default, {
      setHasRoot: setHasRoot,
      setOpen: setOpen
    }),
    open: open,
    PopperProps: {
      modifiers: [{
        name: 'offset',
        options: {
          offset: [-16, 12]
        }
      }]
    }
  }, /*#__PURE__*/React.createElement(_RocketIcon.default, null));
};
var _default = exports["default"] = TopBarIcon;

/***/ }),

/***/ "../modules/checklist/assets/js/utils/consts.js":
/*!******************************************************!*\
  !*** ../modules/checklist/assets/js/utils/consts.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.USER_PROGRESS_ROUTE = exports.USER_PROGRESS = exports.STEP_IDS_TO_COMPLETE_IN_EDITOR = exports.STEPS_ROUTE = exports.STEP = exports.PANEL_ROUTES = exports.MIXPANEL_CHECKLIST_STEPS = void 0;
var STEPS_ROUTE = exports.STEPS_ROUTE = 'checklist/steps',
  USER_PROGRESS_ROUTE = exports.USER_PROGRESS_ROUTE = 'checklist/user-progress';
var STEP = exports.STEP = {
  IS_MARKED_COMPLETED: 'is_marked_completed',
  IS_IMMUTABLE_COMPLETED: 'is_immutable_completed',
  IS_ABSOLUTE_COMPLETED: 'is_absolute_completed',
  PROMOTION_DATA: 'promotion_data'
};
var USER_PROGRESS = exports.USER_PROGRESS = {
  LAST_OPENED_TIMESTAMP: 'last_opened_timestamp',
  SHOULD_OPEN_IN_EDITOR: 'should_open_in_editor',
  CHECKLIST_CLOSED_IN_THE_EDITOR_FOR_FIRST_TIME: 'first_closed_checklist_in_editor',
  IS_POPUP_MINIMIZED: 'is_popup_minimized',
  EDITOR_VISIT_COUNT: 'e_editor_counter'
};
var STEP_IDS_TO_COMPLETE_IN_EDITOR = exports.STEP_IDS_TO_COMPLETE_IN_EDITOR = ['add_logo', 'set_fonts_and_colors'];
var PANEL_ROUTES = exports.PANEL_ROUTES = {
  add_logo: 'panel/global/settings-site-identity',
  set_fonts_and_colors: 'panel/global/global-typography'
};
var MIXPANEL_CHECKLIST_STEPS = exports.MIXPANEL_CHECKLIST_STEPS = {
  UPGRADE: 'upgrade',
  ACTION: 'action',
  DONE: 'done',
  UNDONE: 'undone',
  TITLE: 'title',
  WELL_DONE: 'well_done',
  CHECKLIST_HEADER_CLOSE: 'checklistHeaderClose',
  ACCORDION_SECTION: 'accordionSection'
};

/***/ }),

/***/ "../modules/checklist/assets/js/utils/functions.js":
/*!*********************************************************!*\
  !*** ../modules/checklist/assets/js/utils/functions.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.addMixpanelTrackingChecklistHeader = addMixpanelTrackingChecklistHeader;
exports.addMixpanelTrackingChecklistSteps = addMixpanelTrackingChecklistSteps;
exports.addMixpanelTrackingChecklistTopBar = addMixpanelTrackingChecklistTopBar;
exports.dispatchChecklistOpenEvent = dispatchChecklistOpenEvent;
exports.fetchSteps = fetchSteps;
exports.fetchUserProgress = fetchUserProgress;
exports.getAndUpdateStep = getAndUpdateStep;
exports.getDocumentMetaDataMixpanel = getDocumentMetaDataMixpanel;
exports.isStepChecked = isStepChecked;
exports.toggleChecklistPopup = toggleChecklistPopup;
exports.updateStep = updateStep;
exports.updateUserProgress = updateUserProgress;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _consts = __webpack_require__(/*! ./consts */ "../modules/checklist/assets/js/utils/consts.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var IS_MARKED_COMPLETED = _consts.STEP.IS_MARKED_COMPLETED,
  IS_ABSOLUTE_COMPLETED = _consts.STEP.IS_ABSOLUTE_COMPLETED,
  IS_IMMUTABLE_COMPLETED = _consts.STEP.IS_IMMUTABLE_COMPLETED,
  PROMOTION_DATA = _consts.STEP.PROMOTION_DATA;
function isStepChecked(step) {
  return !step[PROMOTION_DATA] && (step[IS_MARKED_COMPLETED] || step[IS_ABSOLUTE_COMPLETED] || step[IS_IMMUTABLE_COMPLETED]);
}
function toggleChecklistPopup() {
  $e.run('checklist/toggle-popup');
}
function fetchSteps() {
  return _fetchSteps.apply(this, arguments);
}
function _fetchSteps() {
  _fetchSteps = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
    var _response$data;
    var response;
    return _regenerator.default.wrap(function (_context) {
      while (1) switch (_context.prev = _context.next) {
        case 0:
          _context.next = 1;
          return $e.data.get(_consts.STEPS_ROUTE, {}, {
            refresh: true
          });
        case 1:
          response = _context.sent;
          return _context.abrupt("return", (response === null || response === void 0 || (_response$data = response.data) === null || _response$data === void 0 ? void 0 : _response$data.data) || null);
        case 2:
        case "end":
          return _context.stop();
      }
    }, _callee);
  }));
  return _fetchSteps.apply(this, arguments);
}
function fetchUserProgress() {
  return _fetchUserProgress.apply(this, arguments);
}
function _fetchUserProgress() {
  _fetchUserProgress = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee2() {
    var _response$data2;
    var response;
    return _regenerator.default.wrap(function (_context2) {
      while (1) switch (_context2.prev = _context2.next) {
        case 0:
          _context2.next = 1;
          return $e.data.get(_consts.USER_PROGRESS_ROUTE, {}, {
            refresh: true
          });
        case 1:
          response = _context2.sent;
          return _context2.abrupt("return", (response === null || response === void 0 || (_response$data2 = response.data) === null || _response$data2 === void 0 ? void 0 : _response$data2.data) || null);
        case 2:
        case "end":
          return _context2.stop();
      }
    }, _callee2);
  }));
  return _fetchUserProgress.apply(this, arguments);
}
function updateStep(_x, _x2) {
  return _updateStep.apply(this, arguments);
}
function _updateStep() {
  _updateStep = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee3(id, data) {
    return _regenerator.default.wrap(function (_context3) {
      while (1) switch (_context3.prev = _context3.next) {
        case 0:
          _context3.next = 1;
          return $e.data.update(_consts.STEPS_ROUTE, _objectSpread({
            id: id
          }, data), {
            id: id
          });
        case 1:
          return _context3.abrupt("return", _context3.sent);
        case 2:
        case "end":
          return _context3.stop();
      }
    }, _callee3);
  }));
  return _updateStep.apply(this, arguments);
}
function updateUserProgress(_x3) {
  return _updateUserProgress.apply(this, arguments);
}
function _updateUserProgress() {
  _updateUserProgress = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee4(data) {
    return _regenerator.default.wrap(function (_context4) {
      while (1) switch (_context4.prev = _context4.next) {
        case 0:
          _context4.next = 1;
          return $e.data.update(_consts.USER_PROGRESS_ROUTE, data);
        case 1:
          return _context4.abrupt("return", _context4.sent);
        case 2:
        case "end":
          return _context4.stop();
      }
    }, _callee4);
  }));
  return _updateUserProgress.apply(this, arguments);
}
function getAndUpdateStep(id, step, key, value) {
  if (step.config.id !== id) {
    return step;
  }
  return _objectSpread(_objectSpread({}, step), {}, (0, _defineProperty2.default)({}, key, value));
}
function addMixpanelTrackingChecklistSteps(name, action) {
  var element = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'button';
  var documentMetaData = getDocumentMetaDataMixpanel();
  name = name.replace(/_/g, '');
  var eventName = "checklist_steps_".concat(action, "_").concat(name);
  return elementorCommon.eventsManager.dispatchEvent(eventName, _objectSpread({
    location: elementorCommon.eventsManager.config.locations.elementorEditor,
    secondaryLocation: elementorCommon.eventsManager.config.secondaryLocations.checklistSteps,
    trigger: elementorCommon.eventsManager.config.triggers.click,
    element: elementorCommon.eventsManager.config.elements[element]
  }, documentMetaData));
}
function addMixpanelTrackingChecklistHeader(name) {
  var documentMetaData = getDocumentMetaDataMixpanel();
  return elementorCommon.eventsManager.dispatchEvent(elementorCommon.eventsManager.config.names.elementorEditor.checklist[name], _objectSpread({
    location: elementorCommon.eventsManager.config.locations.elementorEditor,
    secondaryLocation: elementorCommon.eventsManager.config.secondaryLocations.checklistHeader,
    trigger: elementorCommon.eventsManager.config.triggers.click,
    element: elementorCommon.eventsManager.config.elements.buttonIcon
  }, documentMetaData));
}
function addMixpanelTrackingChecklistTopBar(togglePopupState) {
  var documentMetaData = getDocumentMetaDataMixpanel();
  var name = !togglePopupState ? 'launchpadOn' : 'launchpadOff';
  return elementorCommon.eventsManager.dispatchEvent(elementorCommon.eventsManager.config.names.topBar[name], _objectSpread({
    location: elementorCommon.eventsManager.config.locations.topBar,
    secondaryLocation: elementorCommon.eventsManager.config.secondaryLocations.launchpad,
    trigger: elementorCommon.eventsManager.config.triggers.toggleClick,
    element: elementorCommon.eventsManager.config.elements.buttonIcon
  }, documentMetaData));
}
function dispatchChecklistOpenEvent() {
  var documentMetaData = getDocumentMetaDataMixpanel();
  return elementorCommon.eventsManager.dispatchEvent(elementorCommon.eventsManager.config.names.elementorEditor.checklist.checklistFirstPopup, _objectSpread({
    location: elementorCommon.eventsManager.config.locations.elementorEditor,
    secondaryLocation: elementorCommon.eventsManager.config.secondaryLocations.launchpad,
    trigger: elementorCommon.eventsManager.config.triggers.editorLoaded,
    element: elementorCommon.eventsManager.config.elements.launchpadChecklist
  }, documentMetaData));
}
function getDocumentMetaDataMixpanel() {
  var postId = elementor.getPreviewContainer().document.config.id;
  var postTitle = elementor.getPreviewContainer().model.attributes.settings.attributes.post_title;
  var postTypeTitle = elementor.getPreviewContainer().document.config.post_type_title;
  var documentType = elementor.getPreviewContainer().document.config.type;
  return {
    postId: postId,
    postTitle: postTitle,
    postTypeTitle: postTypeTitle,
    documentType: documentType
  };
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

/***/ "@elementor/editor-app-bar":
/*!*******************************************!*\
  !*** external "elementorV2.editorAppBar" ***!
  \*******************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.editorAppBar;

/***/ }),

/***/ "@elementor/editor-v1-adapters":
/*!***********************************************!*\
  !*** external "elementorV2.editorV1Adapters" ***!
  \***********************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.editorV1Adapters;

/***/ }),

/***/ "@elementor/icons":
/*!************************************!*\
  !*** external "elementorV2.icons" ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.icons;

/***/ }),

/***/ "@elementor/icons/RocketIcon":
/*!**************************************************!*\
  !*** external "elementorV2.icons['RocketIcon']" ***!
  \**************************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.icons['RocketIcon'];

/***/ }),

/***/ "@elementor/query":
/*!************************************!*\
  !*** external "elementorV2.query" ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorV2.query;

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
/*!************************************************!*\
  !*** ../modules/checklist/assets/js/editor.js ***!
  \************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _editorAppBarLink = __webpack_require__(/*! ./editor-app-bar-link */ "../modules/checklist/assets/js/editor-app-bar-link.js");
var _component = _interopRequireDefault(__webpack_require__(/*! ./component */ "../modules/checklist/assets/js/component.js"));
var _consts = __webpack_require__(/*! ./utils/consts */ "../modules/checklist/assets/js/utils/consts.js");
var _functions = __webpack_require__(/*! ./utils/functions */ "../modules/checklist/assets/js/utils/functions.js");
$e.components.register(new _component.default());
(0, _editorAppBarLink.editorAppBarLink)();
elementorCommon.elements.$window.on('elementor:loaded', elementorLoaded);
function elementorLoaded() {
  elementor.on('document:loaded', checklistStartup);
  elementorCommon.elements.$window.off('elementor:loaded', elementorLoaded);
}
function checklistStartup() {
  return _checklistStartup.apply(this, arguments);
}
function _checklistStartup() {
  _checklistStartup = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
    var shouldHide, userProgress;
    return _regenerator.default.wrap(function (_context) {
      while (1) switch (_context.prev = _context.next) {
        case 0:
          shouldHide = 'yes' !== elementor.getPreferences('show_launchpad_checklist');
          if (!shouldHide) {
            _context.next = 1;
            break;
          }
          $e.commands.run('checklist/toggle-icon', false);
          _context.next = 3;
          break;
        case 1:
          _context.next = 2;
          return (0, _functions.fetchUserProgress)();
        case 2:
          userProgress = _context.sent;
          if (userProgress !== null && userProgress !== void 0 && userProgress[_consts.USER_PROGRESS.SHOULD_OPEN_IN_EDITOR]) {
            (0, _functions.toggleChecklistPopup)();
            (0, _functions.dispatchChecklistOpenEvent)();
          }
        case 3:
          elementor.off('document:loaded', checklistStartup);
        case 4:
        case "end":
          return _context.stop();
      }
    }, _callee);
  }));
  return _checklistStartup.apply(this, arguments);
}
})();

/******/ })()
;
//# sourceMappingURL=checklist.js.map