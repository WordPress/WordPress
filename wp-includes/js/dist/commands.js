/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 4403:
/***/ (function(module, exports) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;
	var nativeCodeString = '[native code]';

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
				if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
					classes.push(arg.toString());
					continue;
				}

				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
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

/***/ 8670:
/***/ (function(module) {

// The scores are arranged so that a continuous match of characters will
// result in a total score of 1.
//
// The best case, this character is a match, and either this is the start
// of the string, or the previous character was also a match.
var SCORE_CONTINUE_MATCH = 1,

    // A new match at the start of a word scores better than a new match
    // elsewhere as it's more likely that the user will type the starts
    // of fragments.
    // (Our notion of word includes CamelCase and hypen-separated, etc.)
    SCORE_WORD_JUMP = 0.9,

    // Any other match isn't ideal, but we include it for completeness.
    SCORE_CHARACTER_JUMP = 0.3,

    // If the user transposed two letters, it should be signficantly penalized.
    //
    // i.e. "ouch" is more likely than "curtain" when "uc" is typed.
    SCORE_TRANSPOSITION = 0.1,

    // If the user jumped to half-way through a subsequent word, it should be
    // very significantly penalized.
    //
    // i.e. "loes" is very unlikely to match "loch ness".
    // NOTE: this is set to 0 for superhuman right now, but we may want to revisit.
    SCORE_LONG_JUMP = 0,

    // The goodness of a match should decay slightly with each missing
    // character.
    //
    // i.e. "bad" is more likely than "bard" when "bd" is typed.
    //
    // This will not change the order of suggestions based on SCORE_* until
    // 100 characters are inserted between matches.
    PENALTY_SKIPPED = 0.999,

    // The goodness of an exact-case match should be higher than a
    // case-insensitive match by a small amount.
    //
    // i.e. "HTML" is more likely than "haml" when "HM" is typed.
    //
    // This will not change the order of suggestions based on SCORE_* until
    // 1000 characters are inserted between matches.
    PENALTY_CASE_MISMATCH = 0.9999,

    // If the word has more characters than the user typed, it should
    // be penalised slightly.
    //
    // i.e. "html" is more likely than "html5" if I type "html".
    //
    // However, it may well be the case that there's a sensible secondary
    // ordering (like alphabetical) that it makes sense to rely on when
    // there are many prefix matches, so we don't make the penalty increase
    // with the number of tokens.
    PENALTY_NOT_COMPLETE = 0.99;

var IS_GAP_REGEXP = /[\\\/\-_+.# \t"@\[\(\{&]/,
    COUNT_GAPS_REGEXP = /[\\\/\-_+.# \t"@\[\(\{&]/g;

function commandScoreInner(string, abbreviation, lowerString, lowerAbbreviation, stringIndex, abbreviationIndex) {

    if (abbreviationIndex === abbreviation.length) {
        if (stringIndex === string.length) {
            return SCORE_CONTINUE_MATCH;

        }
        return PENALTY_NOT_COMPLETE;
    }

    var abbreviationChar = lowerAbbreviation.charAt(abbreviationIndex);
    var index = lowerString.indexOf(abbreviationChar, stringIndex);
    var highScore = 0;

    var score, transposedScore, wordBreaks;

    while (index >= 0) {

        score = commandScoreInner(string, abbreviation, lowerString, lowerAbbreviation, index + 1, abbreviationIndex + 1);
        if (score > highScore) {
            if (index === stringIndex) {
                score *= SCORE_CONTINUE_MATCH;
            } else if (IS_GAP_REGEXP.test(string.charAt(index - 1))) {
                score *= SCORE_WORD_JUMP;
                wordBreaks = string.slice(stringIndex, index - 1).match(COUNT_GAPS_REGEXP);
                if (wordBreaks && stringIndex > 0) {
                    score *= Math.pow(PENALTY_SKIPPED, wordBreaks.length);
                }
            } else if (IS_GAP_REGEXP.test(string.slice(stringIndex, index - 1))) {
                score *= SCORE_LONG_JUMP;
                if (stringIndex > 0) {
                    score *= Math.pow(PENALTY_SKIPPED, index - stringIndex);
                }
            } else {
                score *= SCORE_CHARACTER_JUMP;
                if (stringIndex > 0) {
                    score *= Math.pow(PENALTY_SKIPPED, index - stringIndex);
                }
            }

            if (string.charAt(index) !== abbreviation.charAt(abbreviationIndex)) {
                score *= PENALTY_CASE_MISMATCH;
            }

        }

        if (score < SCORE_TRANSPOSITION &&
                lowerString.charAt(index - 1) === lowerAbbreviation.charAt(abbreviationIndex + 1) &&
                lowerString.charAt(index - 1) !== lowerAbbreviation.charAt(abbreviationIndex)) {
            transposedScore = commandScoreInner(string, abbreviation, lowerString, lowerAbbreviation, index + 1, abbreviationIndex + 2);

            if (transposedScore * SCORE_TRANSPOSITION > score) {
                score = transposedScore * SCORE_TRANSPOSITION;
            }
        }

        if (score > highScore) {
            highScore = score;
        }

        index = lowerString.indexOf(abbreviationChar, index + 1);
    }

    return highScore;
}

function commandScore(string, abbreviation) {
    /* NOTE:
     * in the original, we used to do the lower-casing on each recursive call, but this meant that toLowerCase()
     * was the dominating cost in the algorithm, passing both is a little ugly, but considerably faster.
     */
    return commandScoreInner(string, abbreviation, string.toLowerCase(), abbreviation.toLowerCase(), 0, 0);
}

module.exports = commandScore;


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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/nonce */
/******/ 	!function() {
/******/ 		__webpack_require__.nc = undefined;
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  CommandMenu: function() { return /* reexport */ CommandMenu; },
  privateApis: function() { return /* reexport */ privateApis; },
  store: function() { return /* reexport */ store; },
  useCommand: function() { return /* reexport */ useCommand; },
  useCommandLoader: function() { return /* reexport */ useCommandLoader; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/commands/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  close: function() { return actions_close; },
  open: function() { return actions_open; },
  registerCommand: function() { return registerCommand; },
  registerCommandLoader: function() { return registerCommandLoader; },
  unregisterCommand: function() { return unregisterCommand; },
  unregisterCommandLoader: function() { return unregisterCommandLoader; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/commands/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  getCommandLoaders: function() { return getCommandLoaders; },
  getCommands: function() { return getCommands; },
  getContext: function() { return getContext; },
  isOpen: function() { return selectors_isOpen; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/commands/build-module/store/private-actions.js
var private_actions_namespaceObject = {};
__webpack_require__.r(private_actions_namespaceObject);
__webpack_require__.d(private_actions_namespaceObject, {
  setContext: function() { return setContext; }
});

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
function _extends() {
  _extends = Object.assign ? Object.assign.bind() : function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return _extends.apply(this, arguments);
}
;// CONCATENATED MODULE: external "React"
var external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: ./node_modules/@radix-ui/primitive/dist/index.module.js
function $e42e1063c40fb3ef$export$b9ecd428b558ff10(originalEventHandler, ourEventHandler, { checkForDefaultPrevented: checkForDefaultPrevented = true  } = {}) {
    return function handleEvent(event) {
        originalEventHandler === null || originalEventHandler === void 0 || originalEventHandler(event);
        if (checkForDefaultPrevented === false || !event.defaultPrevented) return ourEventHandler === null || ourEventHandler === void 0 ? void 0 : ourEventHandler(event);
    };
}






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-compose-refs/dist/index.module.js



/**
 * Set a given ref to a given value
 * This utility takes care of different types of refs: callback refs and RefObject(s)
 */ function $6ed0406888f73fc4$var$setRef(ref, value) {
    if (typeof ref === 'function') ref(value);
    else if (ref !== null && ref !== undefined) ref.current = value;
}
/**
 * A utility to compose multiple refs together
 * Accepts callback refs and RefObject(s)
 */ function $6ed0406888f73fc4$export$43e446d32b3d21af(...refs) {
    return (node)=>refs.forEach((ref)=>$6ed0406888f73fc4$var$setRef(ref, node)
        )
    ;
}
/**
 * A custom hook that composes multiple refs
 * Accepts callback refs and RefObject(s)
 */ function $6ed0406888f73fc4$export$c7b2cbe3552a0d05(...refs) {
    // eslint-disable-next-line react-hooks/exhaustive-deps
    return (0,external_React_namespaceObject.useCallback)($6ed0406888f73fc4$export$43e446d32b3d21af(...refs), refs);
}






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-context/dist/index.module.js



function $c512c27ab02ef895$export$fd42f52fd3ae1109(rootComponentName, defaultContext) {
    const Context = /*#__PURE__*/ (0,external_React_namespaceObject.createContext)(defaultContext);
    function Provider(props) {
        const { children: children , ...context } = props; // Only re-memoize when prop values change
        // eslint-disable-next-line react-hooks/exhaustive-deps
        const value = (0,external_React_namespaceObject.useMemo)(()=>context
        , Object.values(context));
        return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)(Context.Provider, {
            value: value
        }, children);
    }
    function useContext(consumerName) {
        const context = (0,external_React_namespaceObject.useContext)(Context);
        if (context) return context;
        if (defaultContext !== undefined) return defaultContext; // if a defaultContext wasn't specified, it's a required context.
        throw new Error(`\`${consumerName}\` must be used within \`${rootComponentName}\``);
    }
    Provider.displayName = rootComponentName + 'Provider';
    return [
        Provider,
        useContext
    ];
}
/* -------------------------------------------------------------------------------------------------
 * createContextScope
 * -----------------------------------------------------------------------------------------------*/ function $c512c27ab02ef895$export$50c7b4e9d9f19c1(scopeName, createContextScopeDeps = []) {
    let defaultContexts = [];
    /* -----------------------------------------------------------------------------------------------
   * createContext
   * ---------------------------------------------------------------------------------------------*/ function $c512c27ab02ef895$export$fd42f52fd3ae1109(rootComponentName, defaultContext) {
        const BaseContext = /*#__PURE__*/ (0,external_React_namespaceObject.createContext)(defaultContext);
        const index = defaultContexts.length;
        defaultContexts = [
            ...defaultContexts,
            defaultContext
        ];
        function Provider(props) {
            const { scope: scope , children: children , ...context } = props;
            const Context = (scope === null || scope === void 0 ? void 0 : scope[scopeName][index]) || BaseContext; // Only re-memoize when prop values change
            // eslint-disable-next-line react-hooks/exhaustive-deps
            const value = (0,external_React_namespaceObject.useMemo)(()=>context
            , Object.values(context));
            return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)(Context.Provider, {
                value: value
            }, children);
        }
        function useContext(consumerName, scope) {
            const Context = (scope === null || scope === void 0 ? void 0 : scope[scopeName][index]) || BaseContext;
            const context = (0,external_React_namespaceObject.useContext)(Context);
            if (context) return context;
            if (defaultContext !== undefined) return defaultContext; // if a defaultContext wasn't specified, it's a required context.
            throw new Error(`\`${consumerName}\` must be used within \`${rootComponentName}\``);
        }
        Provider.displayName = rootComponentName + 'Provider';
        return [
            Provider,
            useContext
        ];
    }
    /* -----------------------------------------------------------------------------------------------
   * createScope
   * ---------------------------------------------------------------------------------------------*/ const createScope = ()=>{
        const scopeContexts = defaultContexts.map((defaultContext)=>{
            return /*#__PURE__*/ (0,external_React_namespaceObject.createContext)(defaultContext);
        });
        return function useScope(scope) {
            const contexts = (scope === null || scope === void 0 ? void 0 : scope[scopeName]) || scopeContexts;
            return (0,external_React_namespaceObject.useMemo)(()=>({
                    [`__scope${scopeName}`]: {
                        ...scope,
                        [scopeName]: contexts
                    }
                })
            , [
                scope,
                contexts
            ]);
        };
    };
    createScope.scopeName = scopeName;
    return [
        $c512c27ab02ef895$export$fd42f52fd3ae1109,
        $c512c27ab02ef895$var$composeContextScopes(createScope, ...createContextScopeDeps)
    ];
}
/* -------------------------------------------------------------------------------------------------
 * composeContextScopes
 * -----------------------------------------------------------------------------------------------*/ function $c512c27ab02ef895$var$composeContextScopes(...scopes) {
    const baseScope = scopes[0];
    if (scopes.length === 1) return baseScope;
    const createScope1 = ()=>{
        const scopeHooks = scopes.map((createScope)=>({
                useScope: createScope(),
                scopeName: createScope.scopeName
            })
        );
        return function useComposedScopes(overrideScopes) {
            const nextScopes1 = scopeHooks.reduce((nextScopes, { useScope: useScope , scopeName: scopeName  })=>{
                // We are calling a hook inside a callback which React warns against to avoid inconsistent
                // renders, however, scoping doesn't have render side effects so we ignore the rule.
                // eslint-disable-next-line react-hooks/rules-of-hooks
                const scopeProps = useScope(overrideScopes);
                const currentScope = scopeProps[`__scope${scopeName}`];
                return {
                    ...nextScopes,
                    ...currentScope
                };
            }, {});
            return (0,external_React_namespaceObject.useMemo)(()=>({
                    [`__scope${baseScope.scopeName}`]: nextScopes1
                })
            , [
                nextScopes1
            ]);
        };
    };
    createScope1.scopeName = baseScope.scopeName;
    return createScope1;
}






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-use-layout-effect/dist/index.module.js



/**
 * On the server, React emits a warning when calling `useLayoutEffect`.
 * This is because neither `useLayoutEffect` nor `useEffect` run on the server.
 * We use this safe version which suppresses the warning by replacing it with a noop on the server.
 *
 * See: https://reactjs.org/docs/hooks-reference.html#uselayouteffect
 */ const $9f79659886946c16$export$e5c5a5f917a5871c = Boolean(globalThis === null || globalThis === void 0 ? void 0 : globalThis.document) ? external_React_namespaceObject.useLayoutEffect : ()=>{};






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-id/dist/index.module.js





const $1746a345f3d73bb7$var$useReactId = external_React_namespaceObject['useId'.toString()] || (()=>undefined
);
let $1746a345f3d73bb7$var$count = 0;
function $1746a345f3d73bb7$export$f680877a34711e37(deterministicId) {
    const [id, setId] = external_React_namespaceObject.useState($1746a345f3d73bb7$var$useReactId()); // React versions older than 18 will have client-side ids only.
    $9f79659886946c16$export$e5c5a5f917a5871c(()=>{
        if (!deterministicId) setId((reactId)=>reactId !== null && reactId !== void 0 ? reactId : String($1746a345f3d73bb7$var$count++)
        );
    }, [
        deterministicId
    ]);
    return deterministicId || (id ? `radix-${id}` : '');
}






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-use-callback-ref/dist/index.module.js



/**
 * A custom hook that converts a callback to a ref to avoid triggering re-renders when passed as a
 * prop or avoid re-executing effects when passed as a dependency
 */ function $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(callback) {
    const callbackRef = (0,external_React_namespaceObject.useRef)(callback);
    (0,external_React_namespaceObject.useEffect)(()=>{
        callbackRef.current = callback;
    }); // https://github.com/facebook/react/issues/19240
    return (0,external_React_namespaceObject.useMemo)(()=>(...args)=>{
            var _callbackRef$current;
            return (_callbackRef$current = callbackRef.current) === null || _callbackRef$current === void 0 ? void 0 : _callbackRef$current.call(callbackRef, ...args);
        }
    , []);
}






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-use-controllable-state/dist/index.module.js





function $71cd76cc60e0454e$export$6f32135080cb4c3({ prop: prop , defaultProp: defaultProp , onChange: onChange = ()=>{}  }) {
    const [uncontrolledProp, setUncontrolledProp] = $71cd76cc60e0454e$var$useUncontrolledState({
        defaultProp: defaultProp,
        onChange: onChange
    });
    const isControlled = prop !== undefined;
    const value1 = isControlled ? prop : uncontrolledProp;
    const handleChange = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onChange);
    const setValue = (0,external_React_namespaceObject.useCallback)((nextValue)=>{
        if (isControlled) {
            const setter = nextValue;
            const value = typeof nextValue === 'function' ? setter(prop) : nextValue;
            if (value !== prop) handleChange(value);
        } else setUncontrolledProp(nextValue);
    }, [
        isControlled,
        prop,
        setUncontrolledProp,
        handleChange
    ]);
    return [
        value1,
        setValue
    ];
}
function $71cd76cc60e0454e$var$useUncontrolledState({ defaultProp: defaultProp , onChange: onChange  }) {
    const uncontrolledState = (0,external_React_namespaceObject.useState)(defaultProp);
    const [value] = uncontrolledState;
    const prevValueRef = (0,external_React_namespaceObject.useRef)(value);
    const handleChange = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onChange);
    (0,external_React_namespaceObject.useEffect)(()=>{
        if (prevValueRef.current !== value) {
            handleChange(value);
            prevValueRef.current = value;
        }
    }, [
        value,
        prevValueRef,
        handleChange
    ]);
    return uncontrolledState;
}






;// CONCATENATED MODULE: external "ReactDOM"
var external_ReactDOM_namespaceObject = window["ReactDOM"];
var external_ReactDOM_default = /*#__PURE__*/__webpack_require__.n(external_ReactDOM_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-slot/dist/index.module.js







/* -------------------------------------------------------------------------------------------------
 * Slot
 * -----------------------------------------------------------------------------------------------*/ const $5e63c961fc1ce211$export$8c6ed5c666ac1360 = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { children: children , ...slotProps } = props;
    const childrenArray = external_React_namespaceObject.Children.toArray(children);
    const slottable = childrenArray.find($5e63c961fc1ce211$var$isSlottable);
    if (slottable) {
        // the new element to render is the one passed as a child of `Slottable`
        const newElement = slottable.props.children;
        const newChildren = childrenArray.map((child)=>{
            if (child === slottable) {
                // because the new element will be the one rendered, we are only interested
                // in grabbing its children (`newElement.props.children`)
                if (external_React_namespaceObject.Children.count(newElement) > 1) return external_React_namespaceObject.Children.only(null);
                return /*#__PURE__*/ (0,external_React_namespaceObject.isValidElement)(newElement) ? newElement.props.children : null;
            } else return child;
        });
        return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5e63c961fc1ce211$var$SlotClone, _extends({}, slotProps, {
            ref: forwardedRef
        }), /*#__PURE__*/ (0,external_React_namespaceObject.isValidElement)(newElement) ? /*#__PURE__*/ (0,external_React_namespaceObject.cloneElement)(newElement, undefined, newChildren) : null);
    }
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5e63c961fc1ce211$var$SlotClone, _extends({}, slotProps, {
        ref: forwardedRef
    }), children);
});
$5e63c961fc1ce211$export$8c6ed5c666ac1360.displayName = 'Slot';
/* -------------------------------------------------------------------------------------------------
 * SlotClone
 * -----------------------------------------------------------------------------------------------*/ const $5e63c961fc1ce211$var$SlotClone = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { children: children , ...slotProps } = props;
    if (/*#__PURE__*/ (0,external_React_namespaceObject.isValidElement)(children)) return /*#__PURE__*/ (0,external_React_namespaceObject.cloneElement)(children, {
        ...$5e63c961fc1ce211$var$mergeProps(slotProps, children.props),
        ref: $6ed0406888f73fc4$export$43e446d32b3d21af(forwardedRef, children.ref)
    });
    return external_React_namespaceObject.Children.count(children) > 1 ? external_React_namespaceObject.Children.only(null) : null;
});
$5e63c961fc1ce211$var$SlotClone.displayName = 'SlotClone';
/* -------------------------------------------------------------------------------------------------
 * Slottable
 * -----------------------------------------------------------------------------------------------*/ const $5e63c961fc1ce211$export$d9f1ccf0bdb05d45 = ({ children: children  })=>{
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)(external_React_namespaceObject.Fragment, null, children);
};
/* ---------------------------------------------------------------------------------------------- */ function $5e63c961fc1ce211$var$isSlottable(child) {
    return /*#__PURE__*/ (0,external_React_namespaceObject.isValidElement)(child) && child.type === $5e63c961fc1ce211$export$d9f1ccf0bdb05d45;
}
function $5e63c961fc1ce211$var$mergeProps(slotProps, childProps) {
    // all child props should override
    const overrideProps = {
        ...childProps
    };
    for(const propName in childProps){
        const slotPropValue = slotProps[propName];
        const childPropValue = childProps[propName];
        const isHandler = /^on[A-Z]/.test(propName); // if it's a handler, modify the override by composing the base handler
        if (isHandler) overrideProps[propName] = (...args)=>{
            childPropValue === null || childPropValue === void 0 || childPropValue(...args);
            slotPropValue === null || slotPropValue === void 0 || slotPropValue(...args);
        };
        else if (propName === 'style') overrideProps[propName] = {
            ...slotPropValue,
            ...childPropValue
        };
        else if (propName === 'className') overrideProps[propName] = [
            slotPropValue,
            childPropValue
        ].filter(Boolean).join(' ');
    }
    return {
        ...slotProps,
        ...overrideProps
    };
}
const $5e63c961fc1ce211$export$be92b6f5f03c0fe9 = (/* unused pure expression or super */ null && ($5e63c961fc1ce211$export$8c6ed5c666ac1360));






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-primitive/dist/index.module.js









const $8927f6f2acc4f386$var$NODES = [
    'a',
    'button',
    'div',
    'h2',
    'h3',
    'img',
    'li',
    'nav',
    'ol',
    'p',
    'span',
    'svg',
    'ul'
]; // Temporary while we await merge of this fix:
// https://github.com/DefinitelyTyped/DefinitelyTyped/pull/55396
// prettier-ignore
/* -------------------------------------------------------------------------------------------------
 * Primitive
 * -----------------------------------------------------------------------------------------------*/ const $8927f6f2acc4f386$export$250ffa63cdc0d034 = $8927f6f2acc4f386$var$NODES.reduce((primitive, node)=>{
    const Node = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
        const { asChild: asChild , ...primitiveProps } = props;
        const Comp = asChild ? $5e63c961fc1ce211$export$8c6ed5c666ac1360 : node;
        (0,external_React_namespaceObject.useEffect)(()=>{
            window[Symbol.for('radix-ui')] = true;
        }, []);
        return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)(Comp, _extends({}, primitiveProps, {
            ref: forwardedRef
        }));
    });
    Node.displayName = `Primitive.${node}`;
    return {
        ...primitive,
        [node]: Node
    };
}, {});
/* -------------------------------------------------------------------------------------------------
 * Utils
 * -----------------------------------------------------------------------------------------------*/ /**
 * Flush custom event dispatch
 * https://github.com/radix-ui/primitives/pull/1378
 *
 * React batches *all* event handlers since version 18, this introduces certain considerations when using custom event types.
 *
 * Internally, React prioritises events in the following order:
 *  - discrete
 *  - continuous
 *  - default
 *
 * https://github.com/facebook/react/blob/a8a4742f1c54493df00da648a3f9d26e3db9c8b5/packages/react-dom/src/events/ReactDOMEventListener.js#L294-L350
 *
 * `discrete` is an  important distinction as updates within these events are applied immediately.
 * React however, is not able to infer the priority of custom event types due to how they are detected internally.
 * Because of this, it's possible for updates from custom events to be unexpectedly batched when
 * dispatched by another `discrete` event.
 *
 * In order to ensure that updates from custom events are applied predictably, we need to manually flush the batch.
 * This utility should be used when dispatching a custom event from within another `discrete` event, this utility
 * is not nessesary when dispatching known event types, or if dispatching a custom type inside a non-discrete event.
 * For example:
 *
 * dispatching a known click 👎
 * target.dispatchEvent(new Event(‘click’))
 *
 * dispatching a custom type within a non-discrete event 👎
 * onScroll={(event) => event.target.dispatchEvent(new CustomEvent(‘customType’))}
 *
 * dispatching a custom type within a `discrete` event 👍
 * onPointerDown={(event) => dispatchDiscreteCustomEvent(event.target, new CustomEvent(‘customType’))}
 *
 * Note: though React classifies `focus`, `focusin` and `focusout` events as `discrete`, it's  not recommended to use
 * this utility with them. This is because it's possible for those handlers to be called implicitly during render
 * e.g. when focus is within a component as it is unmounted, or when managing focus on mount.
 */ function $8927f6f2acc4f386$export$6d1a0317bde7de7f(target, event) {
    if (target) (0,external_ReactDOM_namespaceObject.flushSync)(()=>target.dispatchEvent(event)
    );
}
/* -----------------------------------------------------------------------------------------------*/ const $8927f6f2acc4f386$export$be92b6f5f03c0fe9 = (/* unused pure expression or super */ null && ($8927f6f2acc4f386$export$250ffa63cdc0d034));






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-use-escape-keydown/dist/index.module.js





/**
 * Listens for when the escape key is down
 */ function $addc16e1bbe58fd0$export$3a72a57244d6e765(onEscapeKeyDownProp) {
    const onEscapeKeyDown = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onEscapeKeyDownProp);
    (0,external_React_namespaceObject.useEffect)(()=>{
        const handleKeyDown = (event)=>{
            if (event.key === 'Escape') onEscapeKeyDown(event);
        };
        document.addEventListener('keydown', handleKeyDown);
        return ()=>document.removeEventListener('keydown', handleKeyDown)
        ;
    }, [
        onEscapeKeyDown
    ]);
}






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-dismissable-layer/dist/index.module.js















/* -------------------------------------------------------------------------------------------------
 * DismissableLayer
 * -----------------------------------------------------------------------------------------------*/ const $5cb92bef7577960e$var$DISMISSABLE_LAYER_NAME = 'DismissableLayer';
const $5cb92bef7577960e$var$CONTEXT_UPDATE = 'dismissableLayer.update';
const $5cb92bef7577960e$var$POINTER_DOWN_OUTSIDE = 'dismissableLayer.pointerDownOutside';
const $5cb92bef7577960e$var$FOCUS_OUTSIDE = 'dismissableLayer.focusOutside';
let $5cb92bef7577960e$var$originalBodyPointerEvents;
const $5cb92bef7577960e$var$DismissableLayerContext = /*#__PURE__*/ (0,external_React_namespaceObject.createContext)({
    layers: new Set(),
    layersWithOutsidePointerEventsDisabled: new Set(),
    branches: new Set()
});
const $5cb92bef7577960e$export$177fb62ff3ec1f22 = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { disableOutsidePointerEvents: disableOutsidePointerEvents = false , onEscapeKeyDown: onEscapeKeyDown , onPointerDownOutside: onPointerDownOutside , onFocusOutside: onFocusOutside , onInteractOutside: onInteractOutside , onDismiss: onDismiss , ...layerProps } = props;
    const context = (0,external_React_namespaceObject.useContext)($5cb92bef7577960e$var$DismissableLayerContext);
    const [node1, setNode] = (0,external_React_namespaceObject.useState)(null);
    const [, force] = (0,external_React_namespaceObject.useState)({});
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(forwardedRef, (node)=>setNode(node)
    );
    const layers = Array.from(context.layers);
    const [highestLayerWithOutsidePointerEventsDisabled] = [
        ...context.layersWithOutsidePointerEventsDisabled
    ].slice(-1); // prettier-ignore
    const highestLayerWithOutsidePointerEventsDisabledIndex = layers.indexOf(highestLayerWithOutsidePointerEventsDisabled); // prettier-ignore
    const index = node1 ? layers.indexOf(node1) : -1;
    const isBodyPointerEventsDisabled = context.layersWithOutsidePointerEventsDisabled.size > 0;
    const isPointerEventsEnabled = index >= highestLayerWithOutsidePointerEventsDisabledIndex;
    const pointerDownOutside = $5cb92bef7577960e$var$usePointerDownOutside((event)=>{
        const target = event.target;
        const isPointerDownOnBranch = [
            ...context.branches
        ].some((branch)=>branch.contains(target)
        );
        if (!isPointerEventsEnabled || isPointerDownOnBranch) return;
        onPointerDownOutside === null || onPointerDownOutside === void 0 || onPointerDownOutside(event);
        onInteractOutside === null || onInteractOutside === void 0 || onInteractOutside(event);
        if (!event.defaultPrevented) onDismiss === null || onDismiss === void 0 || onDismiss();
    });
    const focusOutside = $5cb92bef7577960e$var$useFocusOutside((event)=>{
        const target = event.target;
        const isFocusInBranch = [
            ...context.branches
        ].some((branch)=>branch.contains(target)
        );
        if (isFocusInBranch) return;
        onFocusOutside === null || onFocusOutside === void 0 || onFocusOutside(event);
        onInteractOutside === null || onInteractOutside === void 0 || onInteractOutside(event);
        if (!event.defaultPrevented) onDismiss === null || onDismiss === void 0 || onDismiss();
    });
    $addc16e1bbe58fd0$export$3a72a57244d6e765((event)=>{
        const isHighestLayer = index === context.layers.size - 1;
        if (!isHighestLayer) return;
        onEscapeKeyDown === null || onEscapeKeyDown === void 0 || onEscapeKeyDown(event);
        if (!event.defaultPrevented && onDismiss) {
            event.preventDefault();
            onDismiss();
        }
    });
    (0,external_React_namespaceObject.useEffect)(()=>{
        if (!node1) return;
        if (disableOutsidePointerEvents) {
            if (context.layersWithOutsidePointerEventsDisabled.size === 0) {
                $5cb92bef7577960e$var$originalBodyPointerEvents = document.body.style.pointerEvents;
                document.body.style.pointerEvents = 'none';
            }
            context.layersWithOutsidePointerEventsDisabled.add(node1);
        }
        context.layers.add(node1);
        $5cb92bef7577960e$var$dispatchUpdate();
        return ()=>{
            if (disableOutsidePointerEvents && context.layersWithOutsidePointerEventsDisabled.size === 1) document.body.style.pointerEvents = $5cb92bef7577960e$var$originalBodyPointerEvents;
        };
    }, [
        node1,
        disableOutsidePointerEvents,
        context
    ]);
    /**
   * We purposefully prevent combining this effect with the `disableOutsidePointerEvents` effect
   * because a change to `disableOutsidePointerEvents` would remove this layer from the stack
   * and add it to the end again so the layering order wouldn't be _creation order_.
   * We only want them to be removed from context stacks when unmounted.
   */ (0,external_React_namespaceObject.useEffect)(()=>{
        return ()=>{
            if (!node1) return;
            context.layers.delete(node1);
            context.layersWithOutsidePointerEventsDisabled.delete(node1);
            $5cb92bef7577960e$var$dispatchUpdate();
        };
    }, [
        node1,
        context
    ]);
    (0,external_React_namespaceObject.useEffect)(()=>{
        const handleUpdate = ()=>force({})
        ;
        document.addEventListener($5cb92bef7577960e$var$CONTEXT_UPDATE, handleUpdate);
        return ()=>document.removeEventListener($5cb92bef7577960e$var$CONTEXT_UPDATE, handleUpdate)
        ;
    }, []);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends({}, layerProps, {
        ref: composedRefs,
        style: {
            pointerEvents: isBodyPointerEventsDisabled ? isPointerEventsEnabled ? 'auto' : 'none' : undefined,
            ...props.style
        },
        onFocusCapture: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onFocusCapture, focusOutside.onFocusCapture),
        onBlurCapture: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onBlurCapture, focusOutside.onBlurCapture),
        onPointerDownCapture: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onPointerDownCapture, pointerDownOutside.onPointerDownCapture)
    }));
});
/*#__PURE__*/ Object.assign($5cb92bef7577960e$export$177fb62ff3ec1f22, {
    displayName: $5cb92bef7577960e$var$DISMISSABLE_LAYER_NAME
});
/* -------------------------------------------------------------------------------------------------
 * DismissableLayerBranch
 * -----------------------------------------------------------------------------------------------*/ const $5cb92bef7577960e$var$BRANCH_NAME = 'DismissableLayerBranch';
const $5cb92bef7577960e$export$4d5eb2109db14228 = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const context = (0,external_React_namespaceObject.useContext)($5cb92bef7577960e$var$DismissableLayerContext);
    const ref = (0,external_React_namespaceObject.useRef)(null);
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(forwardedRef, ref);
    (0,external_React_namespaceObject.useEffect)(()=>{
        const node = ref.current;
        if (node) {
            context.branches.add(node);
            return ()=>{
                context.branches.delete(node);
            };
        }
    }, [
        context.branches
    ]);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends({}, props, {
        ref: composedRefs
    }));
});
/*#__PURE__*/ Object.assign($5cb92bef7577960e$export$4d5eb2109db14228, {
    displayName: $5cb92bef7577960e$var$BRANCH_NAME
});
/* -----------------------------------------------------------------------------------------------*/ /**
 * Listens for `pointerdown` outside a react subtree. We use `pointerdown` rather than `pointerup`
 * to mimic layer dismissing behaviour present in OS.
 * Returns props to pass to the node we want to check for outside events.
 */ function $5cb92bef7577960e$var$usePointerDownOutside(onPointerDownOutside) {
    const handlePointerDownOutside = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onPointerDownOutside);
    const isPointerInsideReactTreeRef = (0,external_React_namespaceObject.useRef)(false);
    const handleClickRef = (0,external_React_namespaceObject.useRef)(()=>{});
    (0,external_React_namespaceObject.useEffect)(()=>{
        const handlePointerDown = (event)=>{
            if (event.target && !isPointerInsideReactTreeRef.current) {
                const eventDetail = {
                    originalEvent: event
                };
                function handleAndDispatchPointerDownOutsideEvent() {
                    $5cb92bef7577960e$var$handleAndDispatchCustomEvent($5cb92bef7577960e$var$POINTER_DOWN_OUTSIDE, handlePointerDownOutside, eventDetail, {
                        discrete: true
                    });
                }
                /**
         * On touch devices, we need to wait for a click event because browsers implement
         * a ~350ms delay between the time the user stops touching the display and when the
         * browser executres events. We need to ensure we don't reactivate pointer-events within
         * this timeframe otherwise the browser may execute events that should have been prevented.
         *
         * Additionally, this also lets us deal automatically with cancellations when a click event
         * isn't raised because the page was considered scrolled/drag-scrolled, long-pressed, etc.
         *
         * This is why we also continuously remove the previous listener, because we cannot be
         * certain that it was raised, and therefore cleaned-up.
         */ if (event.pointerType === 'touch') {
                    document.removeEventListener('click', handleClickRef.current);
                    handleClickRef.current = handleAndDispatchPointerDownOutsideEvent;
                    document.addEventListener('click', handleClickRef.current, {
                        once: true
                    });
                } else handleAndDispatchPointerDownOutsideEvent();
            }
            isPointerInsideReactTreeRef.current = false;
        };
        /**
     * if this hook executes in a component that mounts via a `pointerdown` event, the event
     * would bubble up to the document and trigger a `pointerDownOutside` event. We avoid
     * this by delaying the event listener registration on the document.
     * This is not React specific, but rather how the DOM works, ie:
     * ```
     * button.addEventListener('pointerdown', () => {
     *   console.log('I will log');
     *   document.addEventListener('pointerdown', () => {
     *     console.log('I will also log');
     *   })
     * });
     */ const timerId = window.setTimeout(()=>{
            document.addEventListener('pointerdown', handlePointerDown);
        }, 0);
        return ()=>{
            window.clearTimeout(timerId);
            document.removeEventListener('pointerdown', handlePointerDown);
            document.removeEventListener('click', handleClickRef.current);
        };
    }, [
        handlePointerDownOutside
    ]);
    return {
        // ensures we check React component tree (not just DOM tree)
        onPointerDownCapture: ()=>isPointerInsideReactTreeRef.current = true
    };
}
/**
 * Listens for when focus happens outside a react subtree.
 * Returns props to pass to the root (node) of the subtree we want to check.
 */ function $5cb92bef7577960e$var$useFocusOutside(onFocusOutside) {
    const handleFocusOutside = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onFocusOutside);
    const isFocusInsideReactTreeRef = (0,external_React_namespaceObject.useRef)(false);
    (0,external_React_namespaceObject.useEffect)(()=>{
        const handleFocus = (event)=>{
            if (event.target && !isFocusInsideReactTreeRef.current) {
                const eventDetail = {
                    originalEvent: event
                };
                $5cb92bef7577960e$var$handleAndDispatchCustomEvent($5cb92bef7577960e$var$FOCUS_OUTSIDE, handleFocusOutside, eventDetail, {
                    discrete: false
                });
            }
        };
        document.addEventListener('focusin', handleFocus);
        return ()=>document.removeEventListener('focusin', handleFocus)
        ;
    }, [
        handleFocusOutside
    ]);
    return {
        onFocusCapture: ()=>isFocusInsideReactTreeRef.current = true
        ,
        onBlurCapture: ()=>isFocusInsideReactTreeRef.current = false
    };
}
function $5cb92bef7577960e$var$dispatchUpdate() {
    const event = new CustomEvent($5cb92bef7577960e$var$CONTEXT_UPDATE);
    document.dispatchEvent(event);
}
function $5cb92bef7577960e$var$handleAndDispatchCustomEvent(name, handler, detail, { discrete: discrete  }) {
    const target = detail.originalEvent.target;
    const event = new CustomEvent(name, {
        bubbles: false,
        cancelable: true,
        detail: detail
    });
    if (handler) target.addEventListener(name, handler, {
        once: true
    });
    if (discrete) $8927f6f2acc4f386$export$6d1a0317bde7de7f(target, event);
    else target.dispatchEvent(event);
}
const $5cb92bef7577960e$export$be92b6f5f03c0fe9 = (/* unused pure expression or super */ null && ($5cb92bef7577960e$export$177fb62ff3ec1f22));
const $5cb92bef7577960e$export$aecb2ddcb55c95be = (/* unused pure expression or super */ null && ($5cb92bef7577960e$export$4d5eb2109db14228));






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-focus-scope/dist/index.module.js











const $d3863c46a17e8a28$var$AUTOFOCUS_ON_MOUNT = 'focusScope.autoFocusOnMount';
const $d3863c46a17e8a28$var$AUTOFOCUS_ON_UNMOUNT = 'focusScope.autoFocusOnUnmount';
const $d3863c46a17e8a28$var$EVENT_OPTIONS = {
    bubbles: false,
    cancelable: true
};
/* -------------------------------------------------------------------------------------------------
 * FocusScope
 * -----------------------------------------------------------------------------------------------*/ const $d3863c46a17e8a28$var$FOCUS_SCOPE_NAME = 'FocusScope';
const $d3863c46a17e8a28$export$20e40289641fbbb6 = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { loop: loop = false , trapped: trapped = false , onMountAutoFocus: onMountAutoFocusProp , onUnmountAutoFocus: onUnmountAutoFocusProp , ...scopeProps } = props;
    const [container1, setContainer] = (0,external_React_namespaceObject.useState)(null);
    const onMountAutoFocus = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onMountAutoFocusProp);
    const onUnmountAutoFocus = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onUnmountAutoFocusProp);
    const lastFocusedElementRef = (0,external_React_namespaceObject.useRef)(null);
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(forwardedRef, (node)=>setContainer(node)
    );
    const focusScope = (0,external_React_namespaceObject.useRef)({
        paused: false,
        pause () {
            this.paused = true;
        },
        resume () {
            this.paused = false;
        }
    }).current; // Takes care of trapping focus if focus is moved outside programmatically for example
    (0,external_React_namespaceObject.useEffect)(()=>{
        if (trapped) {
            function handleFocusIn(event) {
                if (focusScope.paused || !container1) return;
                const target = event.target;
                if (container1.contains(target)) lastFocusedElementRef.current = target;
                else $d3863c46a17e8a28$var$focus(lastFocusedElementRef.current, {
                    select: true
                });
            }
            function handleFocusOut(event) {
                if (focusScope.paused || !container1) return;
                if (!container1.contains(event.relatedTarget)) $d3863c46a17e8a28$var$focus(lastFocusedElementRef.current, {
                    select: true
                });
            }
            document.addEventListener('focusin', handleFocusIn);
            document.addEventListener('focusout', handleFocusOut);
            return ()=>{
                document.removeEventListener('focusin', handleFocusIn);
                document.removeEventListener('focusout', handleFocusOut);
            };
        }
    }, [
        trapped,
        container1,
        focusScope.paused
    ]);
    (0,external_React_namespaceObject.useEffect)(()=>{
        if (container1) {
            $d3863c46a17e8a28$var$focusScopesStack.add(focusScope);
            const previouslyFocusedElement = document.activeElement;
            const hasFocusedCandidate = container1.contains(previouslyFocusedElement);
            if (!hasFocusedCandidate) {
                const mountEvent = new CustomEvent($d3863c46a17e8a28$var$AUTOFOCUS_ON_MOUNT, $d3863c46a17e8a28$var$EVENT_OPTIONS);
                container1.addEventListener($d3863c46a17e8a28$var$AUTOFOCUS_ON_MOUNT, onMountAutoFocus);
                container1.dispatchEvent(mountEvent);
                if (!mountEvent.defaultPrevented) {
                    $d3863c46a17e8a28$var$focusFirst($d3863c46a17e8a28$var$removeLinks($d3863c46a17e8a28$var$getTabbableCandidates(container1)), {
                        select: true
                    });
                    if (document.activeElement === previouslyFocusedElement) $d3863c46a17e8a28$var$focus(container1);
                }
            }
            return ()=>{
                container1.removeEventListener($d3863c46a17e8a28$var$AUTOFOCUS_ON_MOUNT, onMountAutoFocus); // We hit a react bug (fixed in v17) with focusing in unmount.
                // We need to delay the focus a little to get around it for now.
                // See: https://github.com/facebook/react/issues/17894
                setTimeout(()=>{
                    const unmountEvent = new CustomEvent($d3863c46a17e8a28$var$AUTOFOCUS_ON_UNMOUNT, $d3863c46a17e8a28$var$EVENT_OPTIONS);
                    container1.addEventListener($d3863c46a17e8a28$var$AUTOFOCUS_ON_UNMOUNT, onUnmountAutoFocus);
                    container1.dispatchEvent(unmountEvent);
                    if (!unmountEvent.defaultPrevented) $d3863c46a17e8a28$var$focus(previouslyFocusedElement !== null && previouslyFocusedElement !== void 0 ? previouslyFocusedElement : document.body, {
                        select: true
                    });
                     // we need to remove the listener after we `dispatchEvent`
                    container1.removeEventListener($d3863c46a17e8a28$var$AUTOFOCUS_ON_UNMOUNT, onUnmountAutoFocus);
                    $d3863c46a17e8a28$var$focusScopesStack.remove(focusScope);
                }, 0);
            };
        }
    }, [
        container1,
        onMountAutoFocus,
        onUnmountAutoFocus,
        focusScope
    ]); // Takes care of looping focus (when tabbing whilst at the edges)
    const handleKeyDown = (0,external_React_namespaceObject.useCallback)((event)=>{
        if (!loop && !trapped) return;
        if (focusScope.paused) return;
        const isTabKey = event.key === 'Tab' && !event.altKey && !event.ctrlKey && !event.metaKey;
        const focusedElement = document.activeElement;
        if (isTabKey && focusedElement) {
            const container = event.currentTarget;
            const [first, last] = $d3863c46a17e8a28$var$getTabbableEdges(container);
            const hasTabbableElementsInside = first && last; // we can only wrap focus if we have tabbable edges
            if (!hasTabbableElementsInside) {
                if (focusedElement === container) event.preventDefault();
            } else {
                if (!event.shiftKey && focusedElement === last) {
                    event.preventDefault();
                    if (loop) $d3863c46a17e8a28$var$focus(first, {
                        select: true
                    });
                } else if (event.shiftKey && focusedElement === first) {
                    event.preventDefault();
                    if (loop) $d3863c46a17e8a28$var$focus(last, {
                        select: true
                    });
                }
            }
        }
    }, [
        loop,
        trapped,
        focusScope.paused
    ]);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends({
        tabIndex: -1
    }, scopeProps, {
        ref: composedRefs,
        onKeyDown: handleKeyDown
    }));
});
/*#__PURE__*/ Object.assign($d3863c46a17e8a28$export$20e40289641fbbb6, {
    displayName: $d3863c46a17e8a28$var$FOCUS_SCOPE_NAME
});
/* -------------------------------------------------------------------------------------------------
 * Utils
 * -----------------------------------------------------------------------------------------------*/ /**
 * Attempts focusing the first element in a list of candidates.
 * Stops when focus has actually moved.
 */ function $d3863c46a17e8a28$var$focusFirst(candidates, { select: select = false  } = {}) {
    const previouslyFocusedElement = document.activeElement;
    for (const candidate of candidates){
        $d3863c46a17e8a28$var$focus(candidate, {
            select: select
        });
        if (document.activeElement !== previouslyFocusedElement) return;
    }
}
/**
 * Returns the first and last tabbable elements inside a container.
 */ function $d3863c46a17e8a28$var$getTabbableEdges(container) {
    const candidates = $d3863c46a17e8a28$var$getTabbableCandidates(container);
    const first = $d3863c46a17e8a28$var$findVisible(candidates, container);
    const last = $d3863c46a17e8a28$var$findVisible(candidates.reverse(), container);
    return [
        first,
        last
    ];
}
/**
 * Returns a list of potential tabbable candidates.
 *
 * NOTE: This is only a close approximation. For example it doesn't take into account cases like when
 * elements are not visible. This cannot be worked out easily by just reading a property, but rather
 * necessitate runtime knowledge (computed styles, etc). We deal with these cases separately.
 *
 * See: https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker
 * Credit: https://github.com/discord/focus-layers/blob/master/src/util/wrapFocus.tsx#L1
 */ function $d3863c46a17e8a28$var$getTabbableCandidates(container) {
    const nodes = [];
    const walker = document.createTreeWalker(container, NodeFilter.SHOW_ELEMENT, {
        acceptNode: (node)=>{
            const isHiddenInput = node.tagName === 'INPUT' && node.type === 'hidden';
            if (node.disabled || node.hidden || isHiddenInput) return NodeFilter.FILTER_SKIP; // `.tabIndex` is not the same as the `tabindex` attribute. It works on the
            // runtime's understanding of tabbability, so this automatically accounts
            // for any kind of element that could be tabbed to.
            return node.tabIndex >= 0 ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
        }
    });
    while(walker.nextNode())nodes.push(walker.currentNode); // we do not take into account the order of nodes with positive `tabIndex` as it
    // hinders accessibility to have tab order different from visual order.
    return nodes;
}
/**
 * Returns the first visible element in a list.
 * NOTE: Only checks visibility up to the `container`.
 */ function $d3863c46a17e8a28$var$findVisible(elements, container) {
    for (const element of elements){
        // we stop checking if it's hidden at the `container` level (excluding)
        if (!$d3863c46a17e8a28$var$isHidden(element, {
            upTo: container
        })) return element;
    }
}
function $d3863c46a17e8a28$var$isHidden(node, { upTo: upTo  }) {
    if (getComputedStyle(node).visibility === 'hidden') return true;
    while(node){
        // we stop at `upTo` (excluding it)
        if (upTo !== undefined && node === upTo) return false;
        if (getComputedStyle(node).display === 'none') return true;
        node = node.parentElement;
    }
    return false;
}
function $d3863c46a17e8a28$var$isSelectableInput(element) {
    return element instanceof HTMLInputElement && 'select' in element;
}
function $d3863c46a17e8a28$var$focus(element, { select: select = false  } = {}) {
    // only focus if that element is focusable
    if (element && element.focus) {
        const previouslyFocusedElement = document.activeElement; // NOTE: we prevent scrolling on focus, to minimize jarring transitions for users
        element.focus({
            preventScroll: true
        }); // only select if its not the same element, it supports selection and we need to select
        if (element !== previouslyFocusedElement && $d3863c46a17e8a28$var$isSelectableInput(element) && select) element.select();
    }
}
/* -------------------------------------------------------------------------------------------------
 * FocusScope stack
 * -----------------------------------------------------------------------------------------------*/ const $d3863c46a17e8a28$var$focusScopesStack = $d3863c46a17e8a28$var$createFocusScopesStack();
function $d3863c46a17e8a28$var$createFocusScopesStack() {
    /** A stack of focus scopes, with the active one at the top */ let stack = [];
    return {
        add (focusScope) {
            // pause the currently active focus scope (at the top of the stack)
            const activeFocusScope = stack[0];
            if (focusScope !== activeFocusScope) activeFocusScope === null || activeFocusScope === void 0 || activeFocusScope.pause();
             // remove in case it already exists (because we'll re-add it at the top of the stack)
            stack = $d3863c46a17e8a28$var$arrayRemove(stack, focusScope);
            stack.unshift(focusScope);
        },
        remove (focusScope) {
            var _stack$;
            stack = $d3863c46a17e8a28$var$arrayRemove(stack, focusScope);
            (_stack$ = stack[0]) === null || _stack$ === void 0 || _stack$.resume();
        }
    };
}
function $d3863c46a17e8a28$var$arrayRemove(array, item) {
    const updatedArray = [
        ...array
    ];
    const index = updatedArray.indexOf(item);
    if (index !== -1) updatedArray.splice(index, 1);
    return updatedArray;
}
function $d3863c46a17e8a28$var$removeLinks(items) {
    return items.filter((item)=>item.tagName !== 'A'
    );
}
const $d3863c46a17e8a28$export$be92b6f5f03c0fe9 = (/* unused pure expression or super */ null && ($d3863c46a17e8a28$export$20e40289641fbbb6));






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-portal/dist/index.module.js









/* -------------------------------------------------------------------------------------------------
 * Portal
 * -----------------------------------------------------------------------------------------------*/ const $f1701beae083dbae$var$PORTAL_NAME = 'Portal';
const $f1701beae083dbae$export$602eac185826482c = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    var _globalThis$document;
    const { container: container = globalThis === null || globalThis === void 0 ? void 0 : (_globalThis$document = globalThis.document) === null || _globalThis$document === void 0 ? void 0 : _globalThis$document.body , ...portalProps } = props;
    return container ? /*#__PURE__*/ external_ReactDOM_default().createPortal(/*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends({}, portalProps, {
        ref: forwardedRef
    })), container) : null;
});
/*#__PURE__*/ Object.assign($f1701beae083dbae$export$602eac185826482c, {
    displayName: $f1701beae083dbae$var$PORTAL_NAME
});
/* -----------------------------------------------------------------------------------------------*/ const $f1701beae083dbae$export$be92b6f5f03c0fe9 = (/* unused pure expression or super */ null && ($f1701beae083dbae$export$602eac185826482c));






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-presence/dist/index.module.js










function $fe963b355347cc68$export$3e6543de14f8614f(initialState, machine) {
    return (0,external_React_namespaceObject.useReducer)((state, event)=>{
        const nextState = machine[state][event];
        return nextState !== null && nextState !== void 0 ? nextState : state;
    }, initialState);
}


const $921a889cee6df7e8$export$99c2b779aa4e8b8b = (props)=>{
    const { present: present , children: children  } = props;
    const presence = $921a889cee6df7e8$var$usePresence(present);
    const child = typeof children === 'function' ? children({
        present: presence.isPresent
    }) : external_React_namespaceObject.Children.only(children);
    const ref = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(presence.ref, child.ref);
    const forceMount = typeof children === 'function';
    return forceMount || presence.isPresent ? /*#__PURE__*/ (0,external_React_namespaceObject.cloneElement)(child, {
        ref: ref
    }) : null;
};
$921a889cee6df7e8$export$99c2b779aa4e8b8b.displayName = 'Presence';
/* -------------------------------------------------------------------------------------------------
 * usePresence
 * -----------------------------------------------------------------------------------------------*/ function $921a889cee6df7e8$var$usePresence(present) {
    const [node1, setNode] = (0,external_React_namespaceObject.useState)();
    const stylesRef = (0,external_React_namespaceObject.useRef)({});
    const prevPresentRef = (0,external_React_namespaceObject.useRef)(present);
    const prevAnimationNameRef = (0,external_React_namespaceObject.useRef)('none');
    const initialState = present ? 'mounted' : 'unmounted';
    const [state, send] = $fe963b355347cc68$export$3e6543de14f8614f(initialState, {
        mounted: {
            UNMOUNT: 'unmounted',
            ANIMATION_OUT: 'unmountSuspended'
        },
        unmountSuspended: {
            MOUNT: 'mounted',
            ANIMATION_END: 'unmounted'
        },
        unmounted: {
            MOUNT: 'mounted'
        }
    });
    (0,external_React_namespaceObject.useEffect)(()=>{
        const currentAnimationName = $921a889cee6df7e8$var$getAnimationName(stylesRef.current);
        prevAnimationNameRef.current = state === 'mounted' ? currentAnimationName : 'none';
    }, [
        state
    ]);
    $9f79659886946c16$export$e5c5a5f917a5871c(()=>{
        const styles = stylesRef.current;
        const wasPresent = prevPresentRef.current;
        const hasPresentChanged = wasPresent !== present;
        if (hasPresentChanged) {
            const prevAnimationName = prevAnimationNameRef.current;
            const currentAnimationName = $921a889cee6df7e8$var$getAnimationName(styles);
            if (present) send('MOUNT');
            else if (currentAnimationName === 'none' || (styles === null || styles === void 0 ? void 0 : styles.display) === 'none') // If there is no exit animation or the element is hidden, animations won't run
            // so we unmount instantly
            send('UNMOUNT');
            else {
                /**
         * When `present` changes to `false`, we check changes to animation-name to
         * determine whether an animation has started. We chose this approach (reading
         * computed styles) because there is no `animationrun` event and `animationstart`
         * fires after `animation-delay` has expired which would be too late.
         */ const isAnimating = prevAnimationName !== currentAnimationName;
                if (wasPresent && isAnimating) send('ANIMATION_OUT');
                else send('UNMOUNT');
            }
            prevPresentRef.current = present;
        }
    }, [
        present,
        send
    ]);
    $9f79659886946c16$export$e5c5a5f917a5871c(()=>{
        if (node1) {
            /**
       * Triggering an ANIMATION_OUT during an ANIMATION_IN will fire an `animationcancel`
       * event for ANIMATION_IN after we have entered `unmountSuspended` state. So, we
       * make sure we only trigger ANIMATION_END for the currently active animation.
       */ const handleAnimationEnd = (event)=>{
                const currentAnimationName = $921a889cee6df7e8$var$getAnimationName(stylesRef.current);
                const isCurrentAnimation = currentAnimationName.includes(event.animationName);
                if (event.target === node1 && isCurrentAnimation) // With React 18 concurrency this update is applied
                // a frame after the animation ends, creating a flash of visible content.
                // By manually flushing we ensure they sync within a frame, removing the flash.
                (0,external_ReactDOM_namespaceObject.flushSync)(()=>send('ANIMATION_END')
                );
            };
            const handleAnimationStart = (event)=>{
                if (event.target === node1) // if animation occurred, store its name as the previous animation.
                prevAnimationNameRef.current = $921a889cee6df7e8$var$getAnimationName(stylesRef.current);
            };
            node1.addEventListener('animationstart', handleAnimationStart);
            node1.addEventListener('animationcancel', handleAnimationEnd);
            node1.addEventListener('animationend', handleAnimationEnd);
            return ()=>{
                node1.removeEventListener('animationstart', handleAnimationStart);
                node1.removeEventListener('animationcancel', handleAnimationEnd);
                node1.removeEventListener('animationend', handleAnimationEnd);
            };
        } else // Transition to the unmounted state if the node is removed prematurely.
        // We avoid doing so during cleanup as the node may change but still exist.
        send('ANIMATION_END');
    }, [
        node1,
        send
    ]);
    return {
        isPresent: [
            'mounted',
            'unmountSuspended'
        ].includes(state),
        ref: (0,external_React_namespaceObject.useCallback)((node)=>{
            if (node) stylesRef.current = getComputedStyle(node);
            setNode(node);
        }, [])
    };
}
/* -----------------------------------------------------------------------------------------------*/ function $921a889cee6df7e8$var$getAnimationName(styles) {
    return (styles === null || styles === void 0 ? void 0 : styles.animationName) || 'none';
}






;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-focus-guards/dist/index.module.js



/** Number of components which have requested interest to have focus guards */ let $3db38b7d1fb3fe6a$var$count = 0;
function $3db38b7d1fb3fe6a$export$ac5b58043b79449b(props) {
    $3db38b7d1fb3fe6a$export$b7ece24a22aeda8c();
    return props.children;
}
/**
 * Injects a pair of focus guards at the edges of the whole DOM tree
 * to ensure `focusin` & `focusout` events can be caught consistently.
 */ function $3db38b7d1fb3fe6a$export$b7ece24a22aeda8c() {
    (0,external_React_namespaceObject.useEffect)(()=>{
        var _edgeGuards$, _edgeGuards$2;
        const edgeGuards = document.querySelectorAll('[data-radix-focus-guard]');
        document.body.insertAdjacentElement('afterbegin', (_edgeGuards$ = edgeGuards[0]) !== null && _edgeGuards$ !== void 0 ? _edgeGuards$ : $3db38b7d1fb3fe6a$var$createFocusGuard());
        document.body.insertAdjacentElement('beforeend', (_edgeGuards$2 = edgeGuards[1]) !== null && _edgeGuards$2 !== void 0 ? _edgeGuards$2 : $3db38b7d1fb3fe6a$var$createFocusGuard());
        $3db38b7d1fb3fe6a$var$count++;
        return ()=>{
            if ($3db38b7d1fb3fe6a$var$count === 1) document.querySelectorAll('[data-radix-focus-guard]').forEach((node)=>node.remove()
            );
            $3db38b7d1fb3fe6a$var$count--;
        };
    }, []);
}
function $3db38b7d1fb3fe6a$var$createFocusGuard() {
    const element = document.createElement('span');
    element.setAttribute('data-radix-focus-guard', '');
    element.tabIndex = 0;
    element.style.cssText = 'outline: none; opacity: 0; position: fixed; pointer-events: none';
    return element;
}
const $3db38b7d1fb3fe6a$export$be92b6f5f03c0fe9 = (/* unused pure expression or super */ null && ($3db38b7d1fb3fe6a$export$ac5b58043b79449b));






;// CONCATENATED MODULE: ./node_modules/tslib/tslib.es6.mjs
/******************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
/* global Reflect, Promise, SuppressedError, Symbol */

var extendStatics = function(d, b) {
  extendStatics = Object.setPrototypeOf ||
      ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
      function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
  return extendStatics(d, b);
};

function __extends(d, b) {
  if (typeof b !== "function" && b !== null)
      throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
  extendStatics(d, b);
  function __() { this.constructor = d; }
  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

var __assign = function() {
  __assign = Object.assign || function __assign(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
  }
  return __assign.apply(this, arguments);
}

function __rest(s, e) {
  var t = {};
  for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
      t[p] = s[p];
  if (s != null && typeof Object.getOwnPropertySymbols === "function")
      for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
          if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i]))
              t[p[i]] = s[p[i]];
      }
  return t;
}

function __decorate(decorators, target, key, desc) {
  var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
  if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
  else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  return c > 3 && r && Object.defineProperty(target, key, r), r;
}

function __param(paramIndex, decorator) {
  return function (target, key) { decorator(target, key, paramIndex); }
}

function __esDecorate(ctor, descriptorIn, decorators, contextIn, initializers, extraInitializers) {
  function accept(f) { if (f !== void 0 && typeof f !== "function") throw new TypeError("Function expected"); return f; }
  var kind = contextIn.kind, key = kind === "getter" ? "get" : kind === "setter" ? "set" : "value";
  var target = !descriptorIn && ctor ? contextIn["static"] ? ctor : ctor.prototype : null;
  var descriptor = descriptorIn || (target ? Object.getOwnPropertyDescriptor(target, contextIn.name) : {});
  var _, done = false;
  for (var i = decorators.length - 1; i >= 0; i--) {
      var context = {};
      for (var p in contextIn) context[p] = p === "access" ? {} : contextIn[p];
      for (var p in contextIn.access) context.access[p] = contextIn.access[p];
      context.addInitializer = function (f) { if (done) throw new TypeError("Cannot add initializers after decoration has completed"); extraInitializers.push(accept(f || null)); };
      var result = (0, decorators[i])(kind === "accessor" ? { get: descriptor.get, set: descriptor.set } : descriptor[key], context);
      if (kind === "accessor") {
          if (result === void 0) continue;
          if (result === null || typeof result !== "object") throw new TypeError("Object expected");
          if (_ = accept(result.get)) descriptor.get = _;
          if (_ = accept(result.set)) descriptor.set = _;
          if (_ = accept(result.init)) initializers.unshift(_);
      }
      else if (_ = accept(result)) {
          if (kind === "field") initializers.unshift(_);
          else descriptor[key] = _;
      }
  }
  if (target) Object.defineProperty(target, contextIn.name, descriptor);
  done = true;
};

function __runInitializers(thisArg, initializers, value) {
  var useValue = arguments.length > 2;
  for (var i = 0; i < initializers.length; i++) {
      value = useValue ? initializers[i].call(thisArg, value) : initializers[i].call(thisArg);
  }
  return useValue ? value : void 0;
};

function __propKey(x) {
  return typeof x === "symbol" ? x : "".concat(x);
};

function __setFunctionName(f, name, prefix) {
  if (typeof name === "symbol") name = name.description ? "[".concat(name.description, "]") : "";
  return Object.defineProperty(f, "name", { configurable: true, value: prefix ? "".concat(prefix, " ", name) : name });
};

function __metadata(metadataKey, metadataValue) {
  if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(metadataKey, metadataValue);
}

function __awaiter(thisArg, _arguments, P, generator) {
  function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
  return new (P || (P = Promise))(function (resolve, reject) {
      function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
      function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
      function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
      step((generator = generator.apply(thisArg, _arguments || [])).next());
  });
}

function __generator(thisArg, body) {
  var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
  return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
  function verb(n) { return function (v) { return step([n, v]); }; }
  function step(op) {
      if (f) throw new TypeError("Generator is already executing.");
      while (g && (g = 0, op[0] && (_ = 0)), _) try {
          if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
          if (y = 0, t) op = [op[0] & 2, t.value];
          switch (op[0]) {
              case 0: case 1: t = op; break;
              case 4: _.label++; return { value: op[1], done: false };
              case 5: _.label++; y = op[1]; op = [0]; continue;
              case 7: op = _.ops.pop(); _.trys.pop(); continue;
              default:
                  if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                  if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                  if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                  if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                  if (t[2]) _.ops.pop();
                  _.trys.pop(); continue;
          }
          op = body.call(thisArg, _);
      } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
      if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
  }
}

var __createBinding = Object.create ? (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  var desc = Object.getOwnPropertyDescriptor(m, k);
  if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
  }
  Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  o[k2] = m[k];
});

function __exportStar(m, o) {
  for (var p in m) if (p !== "default" && !Object.prototype.hasOwnProperty.call(o, p)) __createBinding(o, m, p);
}

function __values(o) {
  var s = typeof Symbol === "function" && Symbol.iterator, m = s && o[s], i = 0;
  if (m) return m.call(o);
  if (o && typeof o.length === "number") return {
      next: function () {
          if (o && i >= o.length) o = void 0;
          return { value: o && o[i++], done: !o };
      }
  };
  throw new TypeError(s ? "Object is not iterable." : "Symbol.iterator is not defined.");
}

function __read(o, n) {
  var m = typeof Symbol === "function" && o[Symbol.iterator];
  if (!m) return o;
  var i = m.call(o), r, ar = [], e;
  try {
      while ((n === void 0 || n-- > 0) && !(r = i.next()).done) ar.push(r.value);
  }
  catch (error) { e = { error: error }; }
  finally {
      try {
          if (r && !r.done && (m = i["return"])) m.call(i);
      }
      finally { if (e) throw e.error; }
  }
  return ar;
}

/** @deprecated */
function __spread() {
  for (var ar = [], i = 0; i < arguments.length; i++)
      ar = ar.concat(__read(arguments[i]));
  return ar;
}

/** @deprecated */
function __spreadArrays() {
  for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
  for (var r = Array(s), k = 0, i = 0; i < il; i++)
      for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
          r[k] = a[j];
  return r;
}

function __spreadArray(to, from, pack) {
  if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
      if (ar || !(i in from)) {
          if (!ar) ar = Array.prototype.slice.call(from, 0, i);
          ar[i] = from[i];
      }
  }
  return to.concat(ar || Array.prototype.slice.call(from));
}

function __await(v) {
  return this instanceof __await ? (this.v = v, this) : new __await(v);
}

function __asyncGenerator(thisArg, _arguments, generator) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var g = generator.apply(thisArg, _arguments || []), i, q = [];
  return i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i;
  function verb(n) { if (g[n]) i[n] = function (v) { return new Promise(function (a, b) { q.push([n, v, a, b]) > 1 || resume(n, v); }); }; }
  function resume(n, v) { try { step(g[n](v)); } catch (e) { settle(q[0][3], e); } }
  function step(r) { r.value instanceof __await ? Promise.resolve(r.value.v).then(fulfill, reject) : settle(q[0][2], r); }
  function fulfill(value) { resume("next", value); }
  function reject(value) { resume("throw", value); }
  function settle(f, v) { if (f(v), q.shift(), q.length) resume(q[0][0], q[0][1]); }
}

function __asyncDelegator(o) {
  var i, p;
  return i = {}, verb("next"), verb("throw", function (e) { throw e; }), verb("return"), i[Symbol.iterator] = function () { return this; }, i;
  function verb(n, f) { i[n] = o[n] ? function (v) { return (p = !p) ? { value: __await(o[n](v)), done: false } : f ? f(v) : v; } : f; }
}

function __asyncValues(o) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var m = o[Symbol.asyncIterator], i;
  return m ? m.call(o) : (o = typeof __values === "function" ? __values(o) : o[Symbol.iterator](), i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i);
  function verb(n) { i[n] = o[n] && function (v) { return new Promise(function (resolve, reject) { v = o[n](v), settle(resolve, reject, v.done, v.value); }); }; }
  function settle(resolve, reject, d, v) { Promise.resolve(v).then(function(v) { resolve({ value: v, done: d }); }, reject); }
}

function __makeTemplateObject(cooked, raw) {
  if (Object.defineProperty) { Object.defineProperty(cooked, "raw", { value: raw }); } else { cooked.raw = raw; }
  return cooked;
};

var __setModuleDefault = Object.create ? (function(o, v) {
  Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
  o["default"] = v;
};

function __importStar(mod) {
  if (mod && mod.__esModule) return mod;
  var result = {};
  if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
  __setModuleDefault(result, mod);
  return result;
}

function __importDefault(mod) {
  return (mod && mod.__esModule) ? mod : { default: mod };
}

function __classPrivateFieldGet(receiver, state, kind, f) {
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a getter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot read private member from an object whose class did not declare it");
  return kind === "m" ? f : kind === "a" ? f.call(receiver) : f ? f.value : state.get(receiver);
}

function __classPrivateFieldSet(receiver, state, value, kind, f) {
  if (kind === "m") throw new TypeError("Private method is not writable");
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a setter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot write private member to an object whose class did not declare it");
  return (kind === "a" ? f.call(receiver, value) : f ? f.value = value : state.set(receiver, value)), value;
}

function __classPrivateFieldIn(state, receiver) {
  if (receiver === null || (typeof receiver !== "object" && typeof receiver !== "function")) throw new TypeError("Cannot use 'in' operator on non-object");
  return typeof state === "function" ? receiver === state : state.has(receiver);
}

function __addDisposableResource(env, value, async) {
  if (value !== null && value !== void 0) {
    if (typeof value !== "object" && typeof value !== "function") throw new TypeError("Object expected.");
    var dispose;
    if (async) {
        if (!Symbol.asyncDispose) throw new TypeError("Symbol.asyncDispose is not defined.");
        dispose = value[Symbol.asyncDispose];
    }
    if (dispose === void 0) {
        if (!Symbol.dispose) throw new TypeError("Symbol.dispose is not defined.");
        dispose = value[Symbol.dispose];
    }
    if (typeof dispose !== "function") throw new TypeError("Object not disposable.");
    env.stack.push({ value: value, dispose: dispose, async: async });
  }
  else if (async) {
    env.stack.push({ async: true });
  }
  return value;
}

var _SuppressedError = typeof SuppressedError === "function" ? SuppressedError : function (error, suppressed, message) {
  var e = new Error(message);
  return e.name = "SuppressedError", e.error = error, e.suppressed = suppressed, e;
};

function __disposeResources(env) {
  function fail(e) {
    env.error = env.hasError ? new _SuppressedError(e, env.error, "An error was suppressed during disposal.") : e;
    env.hasError = true;
  }
  function next() {
    while (env.stack.length) {
      var rec = env.stack.pop();
      try {
        var result = rec.dispose && rec.dispose.call(rec.value);
        if (rec.async) return Promise.resolve(result).then(next, function(e) { fail(e); return next(); });
      }
      catch (e) {
          fail(e);
      }
    }
    if (env.hasError) throw env.error;
  }
  return next();
}

/* harmony default export */ var tslib_es6 = ({
  __extends,
  __assign,
  __rest,
  __decorate,
  __param,
  __metadata,
  __awaiter,
  __generator,
  __createBinding,
  __exportStar,
  __values,
  __read,
  __spread,
  __spreadArrays,
  __spreadArray,
  __await,
  __asyncGenerator,
  __asyncDelegator,
  __asyncValues,
  __makeTemplateObject,
  __importStar,
  __importDefault,
  __classPrivateFieldGet,
  __classPrivateFieldSet,
  __classPrivateFieldIn,
  __addDisposableResource,
  __disposeResources,
});

;// CONCATENATED MODULE: ./node_modules/react-remove-scroll-bar/dist/es2015/constants.js
var zeroRightClassName = 'right-scroll-bar-position';
var fullWidthClassName = 'width-before-scroll-bar';
var noScrollbarsClassName = 'with-scroll-bars-hidden';
/**
 * Name of a CSS variable containing the amount of "hidden" scrollbar
 * ! might be undefined ! use will fallback!
 */
var removedBarSizeVariable = '--removed-body-scroll-bar-size';

;// CONCATENATED MODULE: ./node_modules/use-callback-ref/dist/es2015/assignRef.js
/**
 * Assigns a value for a given ref, no matter of the ref format
 * @param {RefObject} ref - a callback function or ref object
 * @param value - a new value
 *
 * @see https://github.com/theKashey/use-callback-ref#assignref
 * @example
 * const refObject = useRef();
 * const refFn = (ref) => {....}
 *
 * assignRef(refObject, "refValue");
 * assignRef(refFn, "refValue");
 */
function assignRef(ref, value) {
    if (typeof ref === 'function') {
        ref(value);
    }
    else if (ref) {
        ref.current = value;
    }
    return ref;
}

;// CONCATENATED MODULE: ./node_modules/use-callback-ref/dist/es2015/useRef.js

/**
 * creates a MutableRef with ref change callback
 * @param initialValue - initial ref value
 * @param {Function} callback - a callback to run when value changes
 *
 * @example
 * const ref = useCallbackRef(0, (newValue, oldValue) => console.log(oldValue, '->', newValue);
 * ref.current = 1;
 * // prints 0 -> 1
 *
 * @see https://reactjs.org/docs/hooks-reference.html#useref
 * @see https://github.com/theKashey/use-callback-ref#usecallbackref---to-replace-reactuseref
 * @returns {MutableRefObject}
 */
function useCallbackRef(initialValue, callback) {
    var ref = (0,external_React_namespaceObject.useState)(function () { return ({
        // value
        value: initialValue,
        // last callback
        callback: callback,
        // "memoized" public interface
        facade: {
            get current() {
                return ref.value;
            },
            set current(value) {
                var last = ref.value;
                if (last !== value) {
                    ref.value = value;
                    ref.callback(value, last);
                }
            },
        },
    }); })[0];
    // update callback
    ref.callback = callback;
    return ref.facade;
}

;// CONCATENATED MODULE: ./node_modules/use-callback-ref/dist/es2015/useMergeRef.js


/**
 * Merges two or more refs together providing a single interface to set their value
 * @param {RefObject|Ref} refs
 * @returns {MutableRefObject} - a new ref, which translates all changes to {refs}
 *
 * @see {@link mergeRefs} a version without buit-in memoization
 * @see https://github.com/theKashey/use-callback-ref#usemergerefs
 * @example
 * const Component = React.forwardRef((props, ref) => {
 *   const ownRef = useRef();
 *   const domRef = useMergeRefs([ref, ownRef]); // 👈 merge together
 *   return <div ref={domRef}>...</div>
 * }
 */
function useMergeRefs(refs, defaultValue) {
    return useCallbackRef(defaultValue || null, function (newValue) { return refs.forEach(function (ref) { return assignRef(ref, newValue); }); });
}

;// CONCATENATED MODULE: ./node_modules/use-sidecar/dist/es2015/medium.js

function ItoI(a) {
    return a;
}
function innerCreateMedium(defaults, middleware) {
    if (middleware === void 0) { middleware = ItoI; }
    var buffer = [];
    var assigned = false;
    var medium = {
        read: function () {
            if (assigned) {
                throw new Error('Sidecar: could not `read` from an `assigned` medium. `read` could be used only with `useMedium`.');
            }
            if (buffer.length) {
                return buffer[buffer.length - 1];
            }
            return defaults;
        },
        useMedium: function (data) {
            var item = middleware(data, assigned);
            buffer.push(item);
            return function () {
                buffer = buffer.filter(function (x) { return x !== item; });
            };
        },
        assignSyncMedium: function (cb) {
            assigned = true;
            while (buffer.length) {
                var cbs = buffer;
                buffer = [];
                cbs.forEach(cb);
            }
            buffer = {
                push: function (x) { return cb(x); },
                filter: function () { return buffer; },
            };
        },
        assignMedium: function (cb) {
            assigned = true;
            var pendingQueue = [];
            if (buffer.length) {
                var cbs = buffer;
                buffer = [];
                cbs.forEach(cb);
                pendingQueue = buffer;
            }
            var executeQueue = function () {
                var cbs = pendingQueue;
                pendingQueue = [];
                cbs.forEach(cb);
            };
            var cycle = function () { return Promise.resolve().then(executeQueue); };
            cycle();
            buffer = {
                push: function (x) {
                    pendingQueue.push(x);
                    cycle();
                },
                filter: function (filter) {
                    pendingQueue = pendingQueue.filter(filter);
                    return buffer;
                },
            };
        },
    };
    return medium;
}
function createMedium(defaults, middleware) {
    if (middleware === void 0) { middleware = ItoI; }
    return innerCreateMedium(defaults, middleware);
}
// eslint-disable-next-line @typescript-eslint/ban-types
function createSidecarMedium(options) {
    if (options === void 0) { options = {}; }
    var medium = innerCreateMedium(null);
    medium.options = __assign({ async: true, ssr: false }, options);
    return medium;
}

;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/medium.js

var effectCar = createSidecarMedium();

;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/UI.js





var nothing = function () {
    return;
};
/**
 * Removes scrollbar from the page and contain the scroll within the Lock
 */
var RemoveScroll = external_React_namespaceObject.forwardRef(function (props, parentRef) {
    var ref = external_React_namespaceObject.useRef(null);
    var _a = external_React_namespaceObject.useState({
        onScrollCapture: nothing,
        onWheelCapture: nothing,
        onTouchMoveCapture: nothing,
    }), callbacks = _a[0], setCallbacks = _a[1];
    var forwardProps = props.forwardProps, children = props.children, className = props.className, removeScrollBar = props.removeScrollBar, enabled = props.enabled, shards = props.shards, sideCar = props.sideCar, noIsolation = props.noIsolation, inert = props.inert, allowPinchZoom = props.allowPinchZoom, _b = props.as, Container = _b === void 0 ? 'div' : _b, rest = __rest(props, ["forwardProps", "children", "className", "removeScrollBar", "enabled", "shards", "sideCar", "noIsolation", "inert", "allowPinchZoom", "as"]);
    var SideCar = sideCar;
    var containerRef = useMergeRefs([ref, parentRef]);
    var containerProps = __assign(__assign({}, rest), callbacks);
    return (external_React_namespaceObject.createElement(external_React_namespaceObject.Fragment, null,
        enabled && (external_React_namespaceObject.createElement(SideCar, { sideCar: effectCar, removeScrollBar: removeScrollBar, shards: shards, noIsolation: noIsolation, inert: inert, setCallbacks: setCallbacks, allowPinchZoom: !!allowPinchZoom, lockRef: ref })),
        forwardProps ? (external_React_namespaceObject.cloneElement(external_React_namespaceObject.Children.only(children), __assign(__assign({}, containerProps), { ref: containerRef }))) : (external_React_namespaceObject.createElement(Container, __assign({}, containerProps, { className: className, ref: containerRef }), children))));
});
RemoveScroll.defaultProps = {
    enabled: true,
    removeScrollBar: true,
    inert: false,
};
RemoveScroll.classNames = {
    fullWidth: fullWidthClassName,
    zeroRight: zeroRightClassName,
};


;// CONCATENATED MODULE: ./node_modules/use-sidecar/dist/es2015/exports.js


var SideCar = function (_a) {
    var sideCar = _a.sideCar, rest = __rest(_a, ["sideCar"]);
    if (!sideCar) {
        throw new Error('Sidecar: please provide `sideCar` property to import the right car');
    }
    var Target = sideCar.read();
    if (!Target) {
        throw new Error('Sidecar medium not found');
    }
    return external_React_namespaceObject.createElement(Target, __assign({}, rest));
};
SideCar.isSideCarExport = true;
function exportSidecar(medium, exported) {
    medium.useMedium(exported);
    return SideCar;
}

;// CONCATENATED MODULE: ./node_modules/get-nonce/dist/es2015/index.js
var currentNonce;
var setNonce = function (nonce) {
    currentNonce = nonce;
};
var getNonce = function () {
    if (currentNonce) {
        return currentNonce;
    }
    if (true) {
        return __webpack_require__.nc;
    }
    return undefined;
};

;// CONCATENATED MODULE: ./node_modules/react-style-singleton/dist/es2015/singleton.js

function makeStyleTag() {
    if (!document)
        return null;
    var tag = document.createElement('style');
    tag.type = 'text/css';
    var nonce = getNonce();
    if (nonce) {
        tag.setAttribute('nonce', nonce);
    }
    return tag;
}
function injectStyles(tag, css) {
    // @ts-ignore
    if (tag.styleSheet) {
        // @ts-ignore
        tag.styleSheet.cssText = css;
    }
    else {
        tag.appendChild(document.createTextNode(css));
    }
}
function insertStyleTag(tag) {
    var head = document.head || document.getElementsByTagName('head')[0];
    head.appendChild(tag);
}
var stylesheetSingleton = function () {
    var counter = 0;
    var stylesheet = null;
    return {
        add: function (style) {
            if (counter == 0) {
                if ((stylesheet = makeStyleTag())) {
                    injectStyles(stylesheet, style);
                    insertStyleTag(stylesheet);
                }
            }
            counter++;
        },
        remove: function () {
            counter--;
            if (!counter && stylesheet) {
                stylesheet.parentNode && stylesheet.parentNode.removeChild(stylesheet);
                stylesheet = null;
            }
        },
    };
};

;// CONCATENATED MODULE: ./node_modules/react-style-singleton/dist/es2015/hook.js


/**
 * creates a hook to control style singleton
 * @see {@link styleSingleton} for a safer component version
 * @example
 * ```tsx
 * const useStyle = styleHookSingleton();
 * ///
 * useStyle('body { overflow: hidden}');
 */
var styleHookSingleton = function () {
    var sheet = stylesheetSingleton();
    return function (styles, isDynamic) {
        external_React_namespaceObject.useEffect(function () {
            sheet.add(styles);
            return function () {
                sheet.remove();
            };
        }, [styles && isDynamic]);
    };
};

;// CONCATENATED MODULE: ./node_modules/react-style-singleton/dist/es2015/component.js

/**
 * create a Component to add styles on demand
 * - styles are added when first instance is mounted
 * - styles are removed when the last instance is unmounted
 * - changing styles in runtime does nothing unless dynamic is set. But with multiple components that can lead to the undefined behavior
 */
var styleSingleton = function () {
    var useStyle = styleHookSingleton();
    var Sheet = function (_a) {
        var styles = _a.styles, dynamic = _a.dynamic;
        useStyle(styles, dynamic);
        return null;
    };
    return Sheet;
};

;// CONCATENATED MODULE: ./node_modules/react-style-singleton/dist/es2015/index.js




;// CONCATENATED MODULE: ./node_modules/react-remove-scroll-bar/dist/es2015/utils.js
var zeroGap = {
    left: 0,
    top: 0,
    right: 0,
    gap: 0,
};
var parse = function (x) { return parseInt(x || '', 10) || 0; };
var getOffset = function (gapMode) {
    var cs = window.getComputedStyle(document.body);
    var left = cs[gapMode === 'padding' ? 'paddingLeft' : 'marginLeft'];
    var top = cs[gapMode === 'padding' ? 'paddingTop' : 'marginTop'];
    var right = cs[gapMode === 'padding' ? 'paddingRight' : 'marginRight'];
    return [parse(left), parse(top), parse(right)];
};
var getGapWidth = function (gapMode) {
    if (gapMode === void 0) { gapMode = 'margin'; }
    if (typeof window === 'undefined') {
        return zeroGap;
    }
    var offsets = getOffset(gapMode);
    var documentWidth = document.documentElement.clientWidth;
    var windowWidth = window.innerWidth;
    return {
        left: offsets[0],
        top: offsets[1],
        right: offsets[2],
        gap: Math.max(0, windowWidth - documentWidth + offsets[2] - offsets[0]),
    };
};

;// CONCATENATED MODULE: ./node_modules/react-remove-scroll-bar/dist/es2015/component.js




var Style = styleSingleton();
// important tip - once we measure scrollBar width and remove them
// we could not repeat this operation
// thus we are using style-singleton - only the first "yet correct" style will be applied.
var getStyles = function (_a, allowRelative, gapMode, important) {
    var left = _a.left, top = _a.top, right = _a.right, gap = _a.gap;
    if (gapMode === void 0) { gapMode = 'margin'; }
    return "\n  .".concat(noScrollbarsClassName, " {\n   overflow: hidden ").concat(important, ";\n   padding-right: ").concat(gap, "px ").concat(important, ";\n  }\n  body {\n    overflow: hidden ").concat(important, ";\n    overscroll-behavior: contain;\n    ").concat([
        allowRelative && "position: relative ".concat(important, ";"),
        gapMode === 'margin' &&
            "\n    padding-left: ".concat(left, "px;\n    padding-top: ").concat(top, "px;\n    padding-right: ").concat(right, "px;\n    margin-left:0;\n    margin-top:0;\n    margin-right: ").concat(gap, "px ").concat(important, ";\n    "),
        gapMode === 'padding' && "padding-right: ".concat(gap, "px ").concat(important, ";"),
    ]
        .filter(Boolean)
        .join(''), "\n  }\n  \n  .").concat(zeroRightClassName, " {\n    right: ").concat(gap, "px ").concat(important, ";\n  }\n  \n  .").concat(fullWidthClassName, " {\n    margin-right: ").concat(gap, "px ").concat(important, ";\n  }\n  \n  .").concat(zeroRightClassName, " .").concat(zeroRightClassName, " {\n    right: 0 ").concat(important, ";\n  }\n  \n  .").concat(fullWidthClassName, " .").concat(fullWidthClassName, " {\n    margin-right: 0 ").concat(important, ";\n  }\n  \n  body {\n    ").concat(removedBarSizeVariable, ": ").concat(gap, "px;\n  }\n");
};
/**
 * Removes page scrollbar and blocks page scroll when mounted
 */
var RemoveScrollBar = function (props) {
    var noRelative = props.noRelative, noImportant = props.noImportant, _a = props.gapMode, gapMode = _a === void 0 ? 'margin' : _a;
    /*
     gap will be measured on every component mount
     however it will be used only by the "first" invocation
     due to singleton nature of <Style
     */
    var gap = external_React_namespaceObject.useMemo(function () { return getGapWidth(gapMode); }, [gapMode]);
    return external_React_namespaceObject.createElement(Style, { styles: getStyles(gap, !noRelative, gapMode, !noImportant ? '!important' : '') });
};

;// CONCATENATED MODULE: ./node_modules/react-remove-scroll-bar/dist/es2015/index.js





;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/aggresiveCapture.js
var passiveSupported = false;
if (typeof window !== 'undefined') {
    try {
        var options = Object.defineProperty({}, 'passive', {
            get: function () {
                passiveSupported = true;
                return true;
            },
        });
        // @ts-ignore
        window.addEventListener('test', options, options);
        // @ts-ignore
        window.removeEventListener('test', options, options);
    }
    catch (err) {
        passiveSupported = false;
    }
}
var nonPassive = passiveSupported ? { passive: false } : false;

;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/handleScroll.js
var elementCouldBeVScrolled = function (node) {
    var styles = window.getComputedStyle(node);
    return (styles.overflowY !== 'hidden' && // not-not-scrollable
        !(styles.overflowY === styles.overflowX && styles.overflowY === 'visible') // scrollable
    );
};
var elementCouldBeHScrolled = function (node) {
    var styles = window.getComputedStyle(node);
    return (styles.overflowX !== 'hidden' && // not-not-scrollable
        !(styles.overflowY === styles.overflowX && styles.overflowX === 'visible') // scrollable
    );
};
var locationCouldBeScrolled = function (axis, node) {
    var current = node;
    do {
        // Skip over shadow root
        if (typeof ShadowRoot !== 'undefined' && current instanceof ShadowRoot) {
            current = current.host;
        }
        var isScrollable = elementCouldBeScrolled(axis, current);
        if (isScrollable) {
            var _a = getScrollVariables(axis, current), s = _a[1], d = _a[2];
            if (s > d) {
                return true;
            }
        }
        current = current.parentNode;
    } while (current && current !== document.body);
    return false;
};
var getVScrollVariables = function (_a) {
    var scrollTop = _a.scrollTop, scrollHeight = _a.scrollHeight, clientHeight = _a.clientHeight;
    return [
        scrollTop,
        scrollHeight,
        clientHeight,
    ];
};
var getHScrollVariables = function (_a) {
    var scrollLeft = _a.scrollLeft, scrollWidth = _a.scrollWidth, clientWidth = _a.clientWidth;
    return [
        scrollLeft,
        scrollWidth,
        clientWidth,
    ];
};
var elementCouldBeScrolled = function (axis, node) {
    return axis === 'v' ? elementCouldBeVScrolled(node) : elementCouldBeHScrolled(node);
};
var getScrollVariables = function (axis, node) {
    return axis === 'v' ? getVScrollVariables(node) : getHScrollVariables(node);
};
var getDirectionFactor = function (axis, direction) {
    /**
     * If the element's direction is rtl (right-to-left), then scrollLeft is 0 when the scrollbar is at its rightmost position,
     * and then increasingly negative as you scroll towards the end of the content.
     * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/scrollLeft
     */
    return axis === 'h' && direction === 'rtl' ? -1 : 1;
};
var handleScroll = function (axis, endTarget, event, sourceDelta, noOverscroll) {
    var directionFactor = getDirectionFactor(axis, window.getComputedStyle(endTarget).direction);
    var delta = directionFactor * sourceDelta;
    // find scrollable target
    var target = event.target;
    var targetInLock = endTarget.contains(target);
    var shouldCancelScroll = false;
    var isDeltaPositive = delta > 0;
    var availableScroll = 0;
    var availableScrollTop = 0;
    do {
        var _a = getScrollVariables(axis, target), position = _a[0], scroll_1 = _a[1], capacity = _a[2];
        var elementScroll = scroll_1 - capacity - directionFactor * position;
        if (position || elementScroll) {
            if (elementCouldBeScrolled(axis, target)) {
                availableScroll += elementScroll;
                availableScrollTop += position;
            }
        }
        target = target.parentNode;
    } while (
    // portaled content
    (!targetInLock && target !== document.body) ||
        // self content
        (targetInLock && (endTarget.contains(target) || endTarget === target)));
    if (isDeltaPositive && ((noOverscroll && availableScroll === 0) || (!noOverscroll && delta > availableScroll))) {
        shouldCancelScroll = true;
    }
    else if (!isDeltaPositive &&
        ((noOverscroll && availableScrollTop === 0) || (!noOverscroll && -delta > availableScrollTop))) {
        shouldCancelScroll = true;
    }
    return shouldCancelScroll;
};

;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/SideEffect.js






var getTouchXY = function (event) {
    return 'changedTouches' in event ? [event.changedTouches[0].clientX, event.changedTouches[0].clientY] : [0, 0];
};
var getDeltaXY = function (event) { return [event.deltaX, event.deltaY]; };
var extractRef = function (ref) {
    return ref && 'current' in ref ? ref.current : ref;
};
var deltaCompare = function (x, y) { return x[0] === y[0] && x[1] === y[1]; };
var generateStyle = function (id) { return "\n  .block-interactivity-".concat(id, " {pointer-events: none;}\n  .allow-interactivity-").concat(id, " {pointer-events: all;}\n"); };
var idCounter = 0;
var lockStack = [];
function RemoveScrollSideCar(props) {
    var shouldPreventQueue = external_React_namespaceObject.useRef([]);
    var touchStartRef = external_React_namespaceObject.useRef([0, 0]);
    var activeAxis = external_React_namespaceObject.useRef();
    var id = external_React_namespaceObject.useState(idCounter++)[0];
    var Style = external_React_namespaceObject.useState(function () { return styleSingleton(); })[0];
    var lastProps = external_React_namespaceObject.useRef(props);
    external_React_namespaceObject.useEffect(function () {
        lastProps.current = props;
    }, [props]);
    external_React_namespaceObject.useEffect(function () {
        if (props.inert) {
            document.body.classList.add("block-interactivity-".concat(id));
            var allow_1 = __spreadArray([props.lockRef.current], (props.shards || []).map(extractRef), true).filter(Boolean);
            allow_1.forEach(function (el) { return el.classList.add("allow-interactivity-".concat(id)); });
            return function () {
                document.body.classList.remove("block-interactivity-".concat(id));
                allow_1.forEach(function (el) { return el.classList.remove("allow-interactivity-".concat(id)); });
            };
        }
        return;
    }, [props.inert, props.lockRef.current, props.shards]);
    var shouldCancelEvent = external_React_namespaceObject.useCallback(function (event, parent) {
        if ('touches' in event && event.touches.length === 2) {
            return !lastProps.current.allowPinchZoom;
        }
        var touch = getTouchXY(event);
        var touchStart = touchStartRef.current;
        var deltaX = 'deltaX' in event ? event.deltaX : touchStart[0] - touch[0];
        var deltaY = 'deltaY' in event ? event.deltaY : touchStart[1] - touch[1];
        var currentAxis;
        var target = event.target;
        var moveDirection = Math.abs(deltaX) > Math.abs(deltaY) ? 'h' : 'v';
        // allow horizontal touch move on Range inputs. They will not cause any scroll
        if ('touches' in event && moveDirection === 'h' && target.type === 'range') {
            return false;
        }
        var canBeScrolledInMainDirection = locationCouldBeScrolled(moveDirection, target);
        if (!canBeScrolledInMainDirection) {
            return true;
        }
        if (canBeScrolledInMainDirection) {
            currentAxis = moveDirection;
        }
        else {
            currentAxis = moveDirection === 'v' ? 'h' : 'v';
            canBeScrolledInMainDirection = locationCouldBeScrolled(moveDirection, target);
            // other axis might be not scrollable
        }
        if (!canBeScrolledInMainDirection) {
            return false;
        }
        if (!activeAxis.current && 'changedTouches' in event && (deltaX || deltaY)) {
            activeAxis.current = currentAxis;
        }
        if (!currentAxis) {
            return true;
        }
        var cancelingAxis = activeAxis.current || currentAxis;
        return handleScroll(cancelingAxis, parent, event, cancelingAxis === 'h' ? deltaX : deltaY, true);
    }, []);
    var shouldPrevent = external_React_namespaceObject.useCallback(function (_event) {
        var event = _event;
        if (!lockStack.length || lockStack[lockStack.length - 1] !== Style) {
            // not the last active
            return;
        }
        var delta = 'deltaY' in event ? getDeltaXY(event) : getTouchXY(event);
        var sourceEvent = shouldPreventQueue.current.filter(function (e) { return e.name === event.type && e.target === event.target && deltaCompare(e.delta, delta); })[0];
        // self event, and should be canceled
        if (sourceEvent && sourceEvent.should) {
            event.preventDefault();
            return;
        }
        // outside or shard event
        if (!sourceEvent) {
            var shardNodes = (lastProps.current.shards || [])
                .map(extractRef)
                .filter(Boolean)
                .filter(function (node) { return node.contains(event.target); });
            var shouldStop = shardNodes.length > 0 ? shouldCancelEvent(event, shardNodes[0]) : !lastProps.current.noIsolation;
            if (shouldStop) {
                event.preventDefault();
            }
        }
    }, []);
    var shouldCancel = external_React_namespaceObject.useCallback(function (name, delta, target, should) {
        var event = { name: name, delta: delta, target: target, should: should };
        shouldPreventQueue.current.push(event);
        setTimeout(function () {
            shouldPreventQueue.current = shouldPreventQueue.current.filter(function (e) { return e !== event; });
        }, 1);
    }, []);
    var scrollTouchStart = external_React_namespaceObject.useCallback(function (event) {
        touchStartRef.current = getTouchXY(event);
        activeAxis.current = undefined;
    }, []);
    var scrollWheel = external_React_namespaceObject.useCallback(function (event) {
        shouldCancel(event.type, getDeltaXY(event), event.target, shouldCancelEvent(event, props.lockRef.current));
    }, []);
    var scrollTouchMove = external_React_namespaceObject.useCallback(function (event) {
        shouldCancel(event.type, getTouchXY(event), event.target, shouldCancelEvent(event, props.lockRef.current));
    }, []);
    external_React_namespaceObject.useEffect(function () {
        lockStack.push(Style);
        props.setCallbacks({
            onScrollCapture: scrollWheel,
            onWheelCapture: scrollWheel,
            onTouchMoveCapture: scrollTouchMove,
        });
        document.addEventListener('wheel', shouldPrevent, nonPassive);
        document.addEventListener('touchmove', shouldPrevent, nonPassive);
        document.addEventListener('touchstart', scrollTouchStart, nonPassive);
        return function () {
            lockStack = lockStack.filter(function (inst) { return inst !== Style; });
            document.removeEventListener('wheel', shouldPrevent, nonPassive);
            document.removeEventListener('touchmove', shouldPrevent, nonPassive);
            document.removeEventListener('touchstart', scrollTouchStart, nonPassive);
        };
    }, []);
    var removeScrollBar = props.removeScrollBar, inert = props.inert;
    return (external_React_namespaceObject.createElement(external_React_namespaceObject.Fragment, null,
        inert ? external_React_namespaceObject.createElement(Style, { styles: generateStyle(id) }) : null,
        removeScrollBar ? external_React_namespaceObject.createElement(RemoveScrollBar, { gapMode: "margin" }) : null));
}

;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/sidecar.js



/* harmony default export */ var sidecar = (exportSidecar(effectCar, RemoveScrollSideCar));

;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/Combination.js




var ReactRemoveScroll = external_React_namespaceObject.forwardRef(function (props, ref) { return (external_React_namespaceObject.createElement(RemoveScroll, __assign({}, props, { ref: ref, sideCar: sidecar }))); });
ReactRemoveScroll.classNames = RemoveScroll.classNames;
/* harmony default export */ var Combination = (ReactRemoveScroll);

;// CONCATENATED MODULE: ./node_modules/aria-hidden/dist/es2015/index.js
var getDefaultParent = function (originalTarget) {
    if (typeof document === 'undefined') {
        return null;
    }
    var sampleTarget = Array.isArray(originalTarget) ? originalTarget[0] : originalTarget;
    return sampleTarget.ownerDocument.body;
};
var counterMap = new WeakMap();
var uncontrolledNodes = new WeakMap();
var markerMap = {};
var lockCount = 0;
var unwrapHost = function (node) {
    return node && (node.host || unwrapHost(node.parentNode));
};
var correctTargets = function (parent, targets) {
    return targets
        .map(function (target) {
        if (parent.contains(target)) {
            return target;
        }
        var correctedTarget = unwrapHost(target);
        if (correctedTarget && parent.contains(correctedTarget)) {
            return correctedTarget;
        }
        console.error('aria-hidden', target, 'in not contained inside', parent, '. Doing nothing');
        return null;
    })
        .filter(function (x) { return Boolean(x); });
};
/**
 * Marks everything except given node(or nodes) as aria-hidden
 * @param {Element | Element[]} originalTarget - elements to keep on the page
 * @param [parentNode] - top element, defaults to document.body
 * @param {String} [markerName] - a special attribute to mark every node
 * @param {String} [controlAttribute] - html Attribute to control
 * @return {Undo} undo command
 */
var applyAttributeToOthers = function (originalTarget, parentNode, markerName, controlAttribute) {
    var targets = correctTargets(parentNode, Array.isArray(originalTarget) ? originalTarget : [originalTarget]);
    if (!markerMap[markerName]) {
        markerMap[markerName] = new WeakMap();
    }
    var markerCounter = markerMap[markerName];
    var hiddenNodes = [];
    var elementsToKeep = new Set();
    var elementsToStop = new Set(targets);
    var keep = function (el) {
        if (!el || elementsToKeep.has(el)) {
            return;
        }
        elementsToKeep.add(el);
        keep(el.parentNode);
    };
    targets.forEach(keep);
    var deep = function (parent) {
        if (!parent || elementsToStop.has(parent)) {
            return;
        }
        Array.prototype.forEach.call(parent.children, function (node) {
            if (elementsToKeep.has(node)) {
                deep(node);
            }
            else {
                var attr = node.getAttribute(controlAttribute);
                var alreadyHidden = attr !== null && attr !== 'false';
                var counterValue = (counterMap.get(node) || 0) + 1;
                var markerValue = (markerCounter.get(node) || 0) + 1;
                counterMap.set(node, counterValue);
                markerCounter.set(node, markerValue);
                hiddenNodes.push(node);
                if (counterValue === 1 && alreadyHidden) {
                    uncontrolledNodes.set(node, true);
                }
                if (markerValue === 1) {
                    node.setAttribute(markerName, 'true');
                }
                if (!alreadyHidden) {
                    node.setAttribute(controlAttribute, 'true');
                }
            }
        });
    };
    deep(parentNode);
    elementsToKeep.clear();
    lockCount++;
    return function () {
        hiddenNodes.forEach(function (node) {
            var counterValue = counterMap.get(node) - 1;
            var markerValue = markerCounter.get(node) - 1;
            counterMap.set(node, counterValue);
            markerCounter.set(node, markerValue);
            if (!counterValue) {
                if (!uncontrolledNodes.has(node)) {
                    node.removeAttribute(controlAttribute);
                }
                uncontrolledNodes.delete(node);
            }
            if (!markerValue) {
                node.removeAttribute(markerName);
            }
        });
        lockCount--;
        if (!lockCount) {
            // clear
            counterMap = new WeakMap();
            counterMap = new WeakMap();
            uncontrolledNodes = new WeakMap();
            markerMap = {};
        }
    };
};
/**
 * Marks everything except given node(or nodes) as aria-hidden
 * @param {Element | Element[]} originalTarget - elements to keep on the page
 * @param [parentNode] - top element, defaults to document.body
 * @param {String} [markerName] - a special attribute to mark every node
 * @return {Undo} undo command
 */
var hideOthers = function (originalTarget, parentNode, markerName) {
    if (markerName === void 0) { markerName = 'data-aria-hidden'; }
    var targets = Array.from(Array.isArray(originalTarget) ? originalTarget : [originalTarget]);
    var activeParentNode = parentNode || getDefaultParent(originalTarget);
    if (!activeParentNode) {
        return function () { return null; };
    }
    // we should not hide ariaLive elements - https://github.com/theKashey/aria-hidden/issues/10
    targets.push.apply(targets, Array.from(activeParentNode.querySelectorAll('[aria-live]')));
    return applyAttributeToOthers(targets, activeParentNode, markerName, 'aria-hidden');
};
/**
 * Marks everything except given node(or nodes) as inert
 * @param {Element | Element[]} originalTarget - elements to keep on the page
 * @param [parentNode] - top element, defaults to document.body
 * @param {String} [markerName] - a special attribute to mark every node
 * @return {Undo} undo command
 */
var inertOthers = function (originalTarget, parentNode, markerName) {
    if (markerName === void 0) { markerName = 'data-inert-ed'; }
    var activeParentNode = parentNode || getDefaultParent(originalTarget);
    if (!activeParentNode) {
        return function () { return null; };
    }
    return applyAttributeToOthers(originalTarget, activeParentNode, markerName, 'inert');
};
/**
 * @returns if current browser supports inert
 */
var supportsInert = function () {
    return typeof HTMLElement !== 'undefined' && HTMLElement.prototype.hasOwnProperty('inert');
};
/**
 * Automatic function to "suppress" DOM elements - _hide_ or _inert_ in the best possible way
 * @param {Element | Element[]} originalTarget - elements to keep on the page
 * @param [parentNode] - top element, defaults to document.body
 * @param {String} [markerName] - a special attribute to mark every node
 * @return {Undo} undo command
 */
var suppressOthers = function (originalTarget, parentNode, markerName) {
    if (markerName === void 0) { markerName = 'data-suppressed'; }
    return (supportsInert() ? inertOthers : hideOthers)(originalTarget, parentNode, markerName);
};

;// CONCATENATED MODULE: ./node_modules/@radix-ui/react-dialog/dist/index.module.js

































/* -------------------------------------------------------------------------------------------------
 * Dialog
 * -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$DIALOG_NAME = 'Dialog';
const [$5d3850c4d0b4e6c7$var$createDialogContext, $5d3850c4d0b4e6c7$export$cc702773b8ea3e41] = $c512c27ab02ef895$export$50c7b4e9d9f19c1($5d3850c4d0b4e6c7$var$DIALOG_NAME);
const [$5d3850c4d0b4e6c7$var$DialogProvider, $5d3850c4d0b4e6c7$var$useDialogContext] = $5d3850c4d0b4e6c7$var$createDialogContext($5d3850c4d0b4e6c7$var$DIALOG_NAME);
const $5d3850c4d0b4e6c7$export$3ddf2d174ce01153 = (props)=>{
    const { __scopeDialog: __scopeDialog , children: children , open: openProp , defaultOpen: defaultOpen , onOpenChange: onOpenChange , modal: modal = true  } = props;
    const triggerRef = (0,external_React_namespaceObject.useRef)(null);
    const contentRef = (0,external_React_namespaceObject.useRef)(null);
    const [open = false, setOpen] = $71cd76cc60e0454e$export$6f32135080cb4c3({
        prop: openProp,
        defaultProp: defaultOpen,
        onChange: onOpenChange
    });
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5d3850c4d0b4e6c7$var$DialogProvider, {
        scope: __scopeDialog,
        triggerRef: triggerRef,
        contentRef: contentRef,
        contentId: $1746a345f3d73bb7$export$f680877a34711e37(),
        titleId: $1746a345f3d73bb7$export$f680877a34711e37(),
        descriptionId: $1746a345f3d73bb7$export$f680877a34711e37(),
        open: open,
        onOpenChange: setOpen,
        onOpenToggle: (0,external_React_namespaceObject.useCallback)(()=>setOpen((prevOpen)=>!prevOpen
            )
        , [
            setOpen
        ]),
        modal: modal
    }, children);
};
/*#__PURE__*/ Object.assign($5d3850c4d0b4e6c7$export$3ddf2d174ce01153, {
    displayName: $5d3850c4d0b4e6c7$var$DIALOG_NAME
});
/* -------------------------------------------------------------------------------------------------
 * DialogTrigger
 * -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$TRIGGER_NAME = 'DialogTrigger';
const $5d3850c4d0b4e6c7$export$2e1e1122cf0cba88 = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { __scopeDialog: __scopeDialog , ...triggerProps } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$TRIGGER_NAME, __scopeDialog);
    const composedTriggerRef = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(forwardedRef, context.triggerRef);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.button, _extends({
        type: "button",
        "aria-haspopup": "dialog",
        "aria-expanded": context.open,
        "aria-controls": context.contentId,
        "data-state": $5d3850c4d0b4e6c7$var$getState(context.open)
    }, triggerProps, {
        ref: composedTriggerRef,
        onClick: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onClick, context.onOpenToggle)
    }));
});
/*#__PURE__*/ Object.assign($5d3850c4d0b4e6c7$export$2e1e1122cf0cba88, {
    displayName: $5d3850c4d0b4e6c7$var$TRIGGER_NAME
});
/* -------------------------------------------------------------------------------------------------
 * DialogPortal
 * -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$PORTAL_NAME = 'DialogPortal';
const [$5d3850c4d0b4e6c7$var$PortalProvider, $5d3850c4d0b4e6c7$var$usePortalContext] = $5d3850c4d0b4e6c7$var$createDialogContext($5d3850c4d0b4e6c7$var$PORTAL_NAME, {
    forceMount: undefined
});
const $5d3850c4d0b4e6c7$export$dad7c95542bacce0 = (props)=>{
    const { __scopeDialog: __scopeDialog , forceMount: forceMount , children: children , container: container  } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$PORTAL_NAME, __scopeDialog);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5d3850c4d0b4e6c7$var$PortalProvider, {
        scope: __scopeDialog,
        forceMount: forceMount
    }, external_React_namespaceObject.Children.map(children, (child)=>/*#__PURE__*/ (0,external_React_namespaceObject.createElement)($921a889cee6df7e8$export$99c2b779aa4e8b8b, {
            present: forceMount || context.open
        }, /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($f1701beae083dbae$export$602eac185826482c, {
            asChild: true,
            container: container
        }, child))
    ));
};
/*#__PURE__*/ Object.assign($5d3850c4d0b4e6c7$export$dad7c95542bacce0, {
    displayName: $5d3850c4d0b4e6c7$var$PORTAL_NAME
});
/* -------------------------------------------------------------------------------------------------
 * DialogOverlay
 * -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$OVERLAY_NAME = 'DialogOverlay';
const $5d3850c4d0b4e6c7$export$bd1d06c79be19e17 = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const portalContext = $5d3850c4d0b4e6c7$var$usePortalContext($5d3850c4d0b4e6c7$var$OVERLAY_NAME, props.__scopeDialog);
    const { forceMount: forceMount = portalContext.forceMount , ...overlayProps } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$OVERLAY_NAME, props.__scopeDialog);
    return context.modal ? /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($921a889cee6df7e8$export$99c2b779aa4e8b8b, {
        present: forceMount || context.open
    }, /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5d3850c4d0b4e6c7$var$DialogOverlayImpl, _extends({}, overlayProps, {
        ref: forwardedRef
    }))) : null;
});
/*#__PURE__*/ Object.assign($5d3850c4d0b4e6c7$export$bd1d06c79be19e17, {
    displayName: $5d3850c4d0b4e6c7$var$OVERLAY_NAME
});
const $5d3850c4d0b4e6c7$var$DialogOverlayImpl = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { __scopeDialog: __scopeDialog , ...overlayProps } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$OVERLAY_NAME, __scopeDialog);
    return(/*#__PURE__*/ // Make sure `Content` is scrollable even when it doesn't live inside `RemoveScroll`
    // ie. when `Overlay` and `Content` are siblings
    (0,external_React_namespaceObject.createElement)(Combination, {
        as: $5e63c961fc1ce211$export$8c6ed5c666ac1360,
        allowPinchZoom: true,
        shards: [
            context.contentRef
        ]
    }, /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends({
        "data-state": $5d3850c4d0b4e6c7$var$getState(context.open)
    }, overlayProps, {
        ref: forwardedRef // We re-enable pointer-events prevented by `Dialog.Content` to allow scrolling the overlay.
        ,
        style: {
            pointerEvents: 'auto',
            ...overlayProps.style
        }
    }))));
});
/* -------------------------------------------------------------------------------------------------
 * DialogContent
 * -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$CONTENT_NAME = 'DialogContent';
const $5d3850c4d0b4e6c7$export$b6d9565de1e068cf = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const portalContext = $5d3850c4d0b4e6c7$var$usePortalContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, props.__scopeDialog);
    const { forceMount: forceMount = portalContext.forceMount , ...contentProps } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, props.__scopeDialog);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($921a889cee6df7e8$export$99c2b779aa4e8b8b, {
        present: forceMount || context.open
    }, context.modal ? /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5d3850c4d0b4e6c7$var$DialogContentModal, _extends({}, contentProps, {
        ref: forwardedRef
    })) : /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5d3850c4d0b4e6c7$var$DialogContentNonModal, _extends({}, contentProps, {
        ref: forwardedRef
    })));
});
/*#__PURE__*/ Object.assign($5d3850c4d0b4e6c7$export$b6d9565de1e068cf, {
    displayName: $5d3850c4d0b4e6c7$var$CONTENT_NAME
});
/* -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$DialogContentModal = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, props.__scopeDialog);
    const contentRef = (0,external_React_namespaceObject.useRef)(null);
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(forwardedRef, context.contentRef, contentRef); // aria-hide everything except the content (better supported equivalent to setting aria-modal)
    (0,external_React_namespaceObject.useEffect)(()=>{
        const content = contentRef.current;
        if (content) return hideOthers(content);
    }, []);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5d3850c4d0b4e6c7$var$DialogContentImpl, _extends({}, props, {
        ref: composedRefs // we make sure focus isn't trapped once `DialogContent` has been closed
        ,
        trapFocus: context.open,
        disableOutsidePointerEvents: true,
        onCloseAutoFocus: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onCloseAutoFocus, (event)=>{
            var _context$triggerRef$c;
            event.preventDefault();
            (_context$triggerRef$c = context.triggerRef.current) === null || _context$triggerRef$c === void 0 || _context$triggerRef$c.focus();
        }),
        onPointerDownOutside: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onPointerDownOutside, (event)=>{
            const originalEvent = event.detail.originalEvent;
            const ctrlLeftClick = originalEvent.button === 0 && originalEvent.ctrlKey === true;
            const isRightClick = originalEvent.button === 2 || ctrlLeftClick; // If the event is a right-click, we shouldn't close because
            // it is effectively as if we right-clicked the `Overlay`.
            if (isRightClick) event.preventDefault();
        }) // When focus is trapped, a `focusout` event may still happen.
        ,
        onFocusOutside: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onFocusOutside, (event)=>event.preventDefault()
        )
    }));
});
/* -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$DialogContentNonModal = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, props.__scopeDialog);
    const hasInteractedOutsideRef = (0,external_React_namespaceObject.useRef)(false);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5d3850c4d0b4e6c7$var$DialogContentImpl, _extends({}, props, {
        ref: forwardedRef,
        trapFocus: false,
        disableOutsidePointerEvents: false,
        onCloseAutoFocus: (event)=>{
            var _props$onCloseAutoFoc;
            (_props$onCloseAutoFoc = props.onCloseAutoFocus) === null || _props$onCloseAutoFoc === void 0 || _props$onCloseAutoFoc.call(props, event);
            if (!event.defaultPrevented) {
                var _context$triggerRef$c2;
                if (!hasInteractedOutsideRef.current) (_context$triggerRef$c2 = context.triggerRef.current) === null || _context$triggerRef$c2 === void 0 || _context$triggerRef$c2.focus(); // Always prevent auto focus because we either focus manually or want user agent focus
                event.preventDefault();
            }
            hasInteractedOutsideRef.current = false;
        },
        onInteractOutside: (event)=>{
            var _props$onInteractOuts, _context$triggerRef$c3;
            (_props$onInteractOuts = props.onInteractOutside) === null || _props$onInteractOuts === void 0 || _props$onInteractOuts.call(props, event);
            if (!event.defaultPrevented) hasInteractedOutsideRef.current = true; // Prevent dismissing when clicking the trigger.
            // As the trigger is already setup to close, without doing so would
            // cause it to close and immediately open.
            //
            // We use `onInteractOutside` as some browsers also
            // focus on pointer down, creating the same issue.
            const target = event.target;
            const targetIsTrigger = (_context$triggerRef$c3 = context.triggerRef.current) === null || _context$triggerRef$c3 === void 0 ? void 0 : _context$triggerRef$c3.contains(target);
            if (targetIsTrigger) event.preventDefault();
        }
    }));
});
/* -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$DialogContentImpl = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { __scopeDialog: __scopeDialog , trapFocus: trapFocus , onOpenAutoFocus: onOpenAutoFocus , onCloseAutoFocus: onCloseAutoFocus , ...contentProps } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, __scopeDialog);
    const contentRef = (0,external_React_namespaceObject.useRef)(null);
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(forwardedRef, contentRef); // Make sure the whole tree has focus guards as our `Dialog` will be
    // the last element in the DOM (beacuse of the `Portal`)
    $3db38b7d1fb3fe6a$export$b7ece24a22aeda8c();
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)(external_React_namespaceObject.Fragment, null, /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($d3863c46a17e8a28$export$20e40289641fbbb6, {
        asChild: true,
        loop: true,
        trapped: trapFocus,
        onMountAutoFocus: onOpenAutoFocus,
        onUnmountAutoFocus: onCloseAutoFocus
    }, /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($5cb92bef7577960e$export$177fb62ff3ec1f22, _extends({
        role: "dialog",
        id: context.contentId,
        "aria-describedby": context.descriptionId,
        "aria-labelledby": context.titleId,
        "data-state": $5d3850c4d0b4e6c7$var$getState(context.open)
    }, contentProps, {
        ref: composedRefs,
        onDismiss: ()=>context.onOpenChange(false)
    }))), false);
});
/* -------------------------------------------------------------------------------------------------
 * DialogTitle
 * -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$TITLE_NAME = 'DialogTitle';
const $5d3850c4d0b4e6c7$export$16f7638e4a34b909 = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { __scopeDialog: __scopeDialog , ...titleProps } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$TITLE_NAME, __scopeDialog);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.h2, _extends({
        id: context.titleId
    }, titleProps, {
        ref: forwardedRef
    }));
});
/*#__PURE__*/ Object.assign($5d3850c4d0b4e6c7$export$16f7638e4a34b909, {
    displayName: $5d3850c4d0b4e6c7$var$TITLE_NAME
});
/* -------------------------------------------------------------------------------------------------
 * DialogDescription
 * -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$DESCRIPTION_NAME = 'DialogDescription';
const $5d3850c4d0b4e6c7$export$94e94c2ec2c954d5 = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { __scopeDialog: __scopeDialog , ...descriptionProps } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$DESCRIPTION_NAME, __scopeDialog);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.p, _extends({
        id: context.descriptionId
    }, descriptionProps, {
        ref: forwardedRef
    }));
});
/*#__PURE__*/ Object.assign($5d3850c4d0b4e6c7$export$94e94c2ec2c954d5, {
    displayName: $5d3850c4d0b4e6c7$var$DESCRIPTION_NAME
});
/* -------------------------------------------------------------------------------------------------
 * DialogClose
 * -----------------------------------------------------------------------------------------------*/ const $5d3850c4d0b4e6c7$var$CLOSE_NAME = 'DialogClose';
const $5d3850c4d0b4e6c7$export$fba2fb7cd781b7ac = /*#__PURE__*/ (0,external_React_namespaceObject.forwardRef)((props, forwardedRef)=>{
    const { __scopeDialog: __scopeDialog , ...closeProps } = props;
    const context = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CLOSE_NAME, __scopeDialog);
    return /*#__PURE__*/ (0,external_React_namespaceObject.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.button, _extends({
        type: "button"
    }, closeProps, {
        ref: forwardedRef,
        onClick: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onClick, ()=>context.onOpenChange(false)
        )
    }));
});
/*#__PURE__*/ Object.assign($5d3850c4d0b4e6c7$export$fba2fb7cd781b7ac, {
    displayName: $5d3850c4d0b4e6c7$var$CLOSE_NAME
});
/* -----------------------------------------------------------------------------------------------*/ function $5d3850c4d0b4e6c7$var$getState(open) {
    return open ? 'open' : 'closed';
}
const $5d3850c4d0b4e6c7$var$TITLE_WARNING_NAME = 'DialogTitleWarning';
const [$5d3850c4d0b4e6c7$export$69b62a49393917d6, $5d3850c4d0b4e6c7$var$useWarningContext] = $c512c27ab02ef895$export$fd42f52fd3ae1109($5d3850c4d0b4e6c7$var$TITLE_WARNING_NAME, {
    contentName: $5d3850c4d0b4e6c7$var$CONTENT_NAME,
    titleName: $5d3850c4d0b4e6c7$var$TITLE_NAME,
    docsSlug: 'dialog'
});
const $5d3850c4d0b4e6c7$var$TitleWarning = ({ titleId: titleId  })=>{
    const titleWarningContext = $5d3850c4d0b4e6c7$var$useWarningContext($5d3850c4d0b4e6c7$var$TITLE_WARNING_NAME);
    const MESSAGE = `\`${titleWarningContext.contentName}\` requires a \`${titleWarningContext.titleName}\` for the component to be accessible for screen reader users.

If you want to hide the \`${titleWarningContext.titleName}\`, you can wrap it with our VisuallyHidden component.

For more information, see https://radix-ui.com/primitives/docs/components/${titleWarningContext.docsSlug}`;
    $67UHm$useEffect(()=>{
        if (titleId) {
            const hasTitle = document.getElementById(titleId);
            if (!hasTitle) throw new Error(MESSAGE);
        }
    }, [
        MESSAGE,
        titleId
    ]);
    return null;
};
const $5d3850c4d0b4e6c7$var$DESCRIPTION_WARNING_NAME = 'DialogDescriptionWarning';
const $5d3850c4d0b4e6c7$var$DescriptionWarning = ({ contentRef: contentRef , descriptionId: descriptionId  })=>{
    const descriptionWarningContext = $5d3850c4d0b4e6c7$var$useWarningContext($5d3850c4d0b4e6c7$var$DESCRIPTION_WARNING_NAME);
    const MESSAGE = `Warning: Missing \`Description\` or \`aria-describedby={undefined}\` for {${descriptionWarningContext.contentName}}.`;
    $67UHm$useEffect(()=>{
        var _contentRef$current;
        const describedById = (_contentRef$current = contentRef.current) === null || _contentRef$current === void 0 ? void 0 : _contentRef$current.getAttribute('aria-describedby'); // if we have an id and the user hasn't set aria-describedby={undefined}
        if (descriptionId && describedById) {
            const hasDescription = document.getElementById(descriptionId);
            if (!hasDescription) console.warn(MESSAGE);
        }
    }, [
        MESSAGE,
        contentRef,
        descriptionId
    ]);
    return null;
};
const $5d3850c4d0b4e6c7$export$be92b6f5f03c0fe9 = $5d3850c4d0b4e6c7$export$3ddf2d174ce01153;
const $5d3850c4d0b4e6c7$export$41fb9f06171c75f4 = (/* unused pure expression or super */ null && ($5d3850c4d0b4e6c7$export$2e1e1122cf0cba88));
const $5d3850c4d0b4e6c7$export$602eac185826482c = $5d3850c4d0b4e6c7$export$dad7c95542bacce0;
const $5d3850c4d0b4e6c7$export$c6fdb837b070b4ff = $5d3850c4d0b4e6c7$export$bd1d06c79be19e17;
const $5d3850c4d0b4e6c7$export$7c6e2c02157bb7d2 = $5d3850c4d0b4e6c7$export$b6d9565de1e068cf;
const $5d3850c4d0b4e6c7$export$f99233281efd08a0 = (/* unused pure expression or super */ null && ($5d3850c4d0b4e6c7$export$16f7638e4a34b909));
const $5d3850c4d0b4e6c7$export$393edc798c47379d = (/* unused pure expression or super */ null && ($5d3850c4d0b4e6c7$export$94e94c2ec2c954d5));
const $5d3850c4d0b4e6c7$export$f39c2d165cd861fe = (/* unused pure expression or super */ null && ($5d3850c4d0b4e6c7$export$fba2fb7cd781b7ac));






// EXTERNAL MODULE: ./node_modules/command-score/index.js
var command_score = __webpack_require__(8670);
;// CONCATENATED MODULE: ./node_modules/cmdk/dist/index.mjs
var ue='[cmdk-list-sizer=""]',M='[cmdk-group=""]',N='[cmdk-group-items=""]',de='[cmdk-group-heading=""]',ee='[cmdk-item=""]',Z=`${ee}:not([aria-disabled="true"])`,z="cmdk-item-select",S="data-value",fe=(n,a)=>command_score(n,a),te=external_React_namespaceObject.createContext(void 0),k=()=>external_React_namespaceObject.useContext(te),re=external_React_namespaceObject.createContext(void 0),U=()=>external_React_namespaceObject.useContext(re),ne=external_React_namespaceObject.createContext(void 0),oe=external_React_namespaceObject.forwardRef((n,a)=>{let r=external_React_namespaceObject.useRef(null),o=x(()=>({search:"",value:"",filtered:{count:0,items:new Map,groups:new Set}})),u=x(()=>new Set),l=x(()=>new Map),p=x(()=>new Map),f=x(()=>new Set),d=ae(n),{label:v,children:E,value:R,onValueChange:w,filter:O,shouldFilter:ie,...D}=n,F=external_React_namespaceObject.useId(),g=external_React_namespaceObject.useId(),A=external_React_namespaceObject.useId(),y=ye();L(()=>{if(R!==void 0){let e=R.trim().toLowerCase();o.current.value=e,y(6,W),h.emit()}},[R]);let h=external_React_namespaceObject.useMemo(()=>({subscribe:e=>(f.current.add(e),()=>f.current.delete(e)),snapshot:()=>o.current,setState:(e,c,i)=>{var s,m,b;if(!Object.is(o.current[e],c)){if(o.current[e]=c,e==="search")j(),G(),y(1,V);else if(e==="value")if(((s=d.current)==null?void 0:s.value)!==void 0){(b=(m=d.current).onValueChange)==null||b.call(m,c);return}else i||y(5,W);h.emit()}},emit:()=>{f.current.forEach(e=>e())}}),[]),K=external_React_namespaceObject.useMemo(()=>({value:(e,c)=>{c!==p.current.get(e)&&(p.current.set(e,c),o.current.filtered.items.set(e,B(c)),y(2,()=>{G(),h.emit()}))},item:(e,c)=>(u.current.add(e),c&&(l.current.has(c)?l.current.get(c).add(e):l.current.set(c,new Set([e]))),y(3,()=>{j(),G(),o.current.value||V(),h.emit()}),()=>{p.current.delete(e),u.current.delete(e),o.current.filtered.items.delete(e),y(4,()=>{j(),V(),h.emit()})}),group:e=>(l.current.has(e)||l.current.set(e,new Set),()=>{p.current.delete(e),l.current.delete(e)}),filter:()=>d.current.shouldFilter,label:v||n["aria-label"],listId:F,inputId:A,labelId:g}),[]);function B(e){var i;let c=((i=d.current)==null?void 0:i.filter)??fe;return e?c(e,o.current.search):0}function G(){if(!r.current||!o.current.search||d.current.shouldFilter===!1)return;let e=o.current.filtered.items,c=[];o.current.filtered.groups.forEach(s=>{let m=l.current.get(s),b=0;m.forEach(P=>{let ce=e.get(P);b=Math.max(ce,b)}),c.push([s,b])});let i=r.current.querySelector(ue);I().sort((s,m)=>{let b=s.getAttribute(S),P=m.getAttribute(S);return(e.get(P)??0)-(e.get(b)??0)}).forEach(s=>{let m=s.closest(N);m?m.appendChild(s.parentElement===m?s:s.closest(`${N} > *`)):i.appendChild(s.parentElement===i?s:s.closest(`${N} > *`))}),c.sort((s,m)=>m[1]-s[1]).forEach(s=>{let m=r.current.querySelector(`${M}[${S}="${s[0]}"]`);m==null||m.parentElement.appendChild(m)})}function V(){let e=I().find(i=>!i.ariaDisabled),c=e==null?void 0:e.getAttribute(S);h.setState("value",c||void 0)}function j(){if(!o.current.search||d.current.shouldFilter===!1){o.current.filtered.count=u.current.size;return}o.current.filtered.groups=new Set;let e=0;for(let c of u.current){let i=p.current.get(c),s=B(i);o.current.filtered.items.set(c,s),s>0&&e++}for(let[c,i]of l.current)for(let s of i)if(o.current.filtered.items.get(s)>0){o.current.filtered.groups.add(c);break}o.current.filtered.count=e}function W(){var c,i,s;let e=_();e&&(((c=e.parentElement)==null?void 0:c.firstChild)===e&&((s=(i=e.closest(M))==null?void 0:i.querySelector(de))==null||s.scrollIntoView({block:"nearest"})),e.scrollIntoView({block:"nearest"}))}function _(){return r.current.querySelector(`${ee}[aria-selected="true"]`)}function I(){return Array.from(r.current.querySelectorAll(Z))}function q(e){let i=I()[e];i&&h.setState("value",i.getAttribute(S))}function $(e){var b;let c=_(),i=I(),s=i.findIndex(P=>P===c),m=i[s+e];(b=d.current)!=null&&b.loop&&(m=s+e<0?i[i.length-1]:s+e===i.length?i[0]:i[s+e]),m&&h.setState("value",m.getAttribute(S))}function J(e){let c=_(),i=c==null?void 0:c.closest(M),s;for(;i&&!s;)i=e>0?Se(i,M):Ce(i,M),s=i==null?void 0:i.querySelector(Z);s?h.setState("value",s.getAttribute(S)):$(e)}let Q=()=>q(I().length-1),X=e=>{e.preventDefault(),e.metaKey?Q():e.altKey?J(1):$(1)},Y=e=>{e.preventDefault(),e.metaKey?q(0):e.altKey?J(-1):$(-1)};return external_React_namespaceObject.createElement("div",{ref:H([r,a]),...D,"cmdk-root":"",onKeyDown:e=>{var c;if((c=D.onKeyDown)==null||c.call(D,e),!e.defaultPrevented)switch(e.key){case"n":case"j":{e.ctrlKey&&X(e);break}case"ArrowDown":{X(e);break}case"p":case"k":{e.ctrlKey&&Y(e);break}case"ArrowUp":{Y(e);break}case"Home":{e.preventDefault(),q(0);break}case"End":{e.preventDefault(),Q();break}case"Enter":{e.preventDefault();let i=_();if(i){let s=new Event(z);i.dispatchEvent(s)}}}}},external_React_namespaceObject.createElement("label",{"cmdk-label":"",htmlFor:K.inputId,id:K.labelId,style:xe},v),external_React_namespaceObject.createElement(re.Provider,{value:h},external_React_namespaceObject.createElement(te.Provider,{value:K},E)))}),me=external_React_namespaceObject.forwardRef((n,a)=>{let r=external_React_namespaceObject.useId(),o=external_React_namespaceObject.useRef(null),u=external_React_namespaceObject.useContext(ne),l=k(),p=ae(n);L(()=>l.item(r,u),[]);let f=se(r,o,[n.value,n.children,o]),d=U(),v=T(g=>g.value&&g.value===f.current),E=T(g=>l.filter()===!1?!0:g.search?g.filtered.items.get(r)>0:!0);external_React_namespaceObject.useEffect(()=>{let g=o.current;if(!(!g||n.disabled))return g.addEventListener(z,R),()=>g.removeEventListener(z,R)},[E,n.onSelect,n.disabled]);function R(){var g,A;(A=(g=p.current).onSelect)==null||A.call(g,f.current)}function w(){d.setState("value",f.current,!0)}if(!E)return null;let{disabled:O,value:ie,onSelect:D,...F}=n;return external_React_namespaceObject.createElement("div",{ref:H([o,a]),...F,"cmdk-item":"",role:"option","aria-disabled":O||void 0,"aria-selected":v||void 0,"data-selected":v||void 0,onPointerMove:O?void 0:w,onClick:O?void 0:R},n.children)}),pe=external_React_namespaceObject.forwardRef((n,a)=>{let{heading:r,children:o,...u}=n,l=external_React_namespaceObject.useId(),p=external_React_namespaceObject.useRef(null),f=external_React_namespaceObject.useRef(null),d=external_React_namespaceObject.useId(),v=k(),E=T(w=>v.filter()===!1?!0:w.search?w.filtered.groups.has(l):!0);L(()=>v.group(l),[]),se(l,p,[n.value,n.heading,f]);let R=external_React_namespaceObject.createElement(ne.Provider,{value:l},o);return external_React_namespaceObject.createElement("div",{ref:H([p,a]),...u,"cmdk-group":"",role:"presentation",hidden:E?void 0:!0},r&&external_React_namespaceObject.createElement("div",{ref:f,"cmdk-group-heading":"","aria-hidden":!0,id:d},r),external_React_namespaceObject.createElement("div",{"cmdk-group-items":"",role:"group","aria-labelledby":r?d:void 0},R))}),ge=external_React_namespaceObject.forwardRef((n,a)=>{let{alwaysRender:r,...o}=n,u=external_React_namespaceObject.useRef(null),l=T(p=>!p.search);return!r&&!l?null:external_React_namespaceObject.createElement("div",{ref:H([u,a]),...o,"cmdk-separator":"",role:"separator"})}),ve=external_React_namespaceObject.forwardRef((n,a)=>{let{onValueChange:r,...o}=n,u=n.value!=null,l=U(),p=T(d=>d.search),f=k();return external_React_namespaceObject.useEffect(()=>{n.value!=null&&l.setState("search",n.value)},[n.value]),external_React_namespaceObject.createElement("input",{ref:a,...o,"cmdk-input":"",autoComplete:"off",autoCorrect:"off",spellCheck:!1,"aria-autocomplete":"list",role:"combobox","aria-expanded":!0,"aria-controls":f.listId,"aria-labelledby":f.labelId,id:f.inputId,type:"text",value:u?n.value:p,onChange:d=>{u||l.setState("search",d.target.value),r==null||r(d.target.value)}})}),Re=external_React_namespaceObject.forwardRef((n,a)=>{let{children:r,...o}=n,u=external_React_namespaceObject.useRef(null),l=external_React_namespaceObject.useRef(null),p=k();return external_React_namespaceObject.useEffect(()=>{if(l.current&&u.current){let f=l.current,d=u.current,v,E=new ResizeObserver(()=>{v=requestAnimationFrame(()=>{let R=f.getBoundingClientRect().height;d.style.setProperty("--cmdk-list-height",R.toFixed(1)+"px")})});return E.observe(f),()=>{cancelAnimationFrame(v),E.unobserve(f)}}},[]),external_React_namespaceObject.createElement("div",{ref:H([u,a]),...o,"cmdk-list":"",role:"listbox","aria-label":"Suggestions",id:p.listId,"aria-labelledby":p.inputId},external_React_namespaceObject.createElement("div",{ref:l,"cmdk-list-sizer":""},r))}),be=external_React_namespaceObject.forwardRef((n,a)=>{let{open:r,onOpenChange:o,container:u,...l}=n;return external_React_namespaceObject.createElement($5d3850c4d0b4e6c7$export$be92b6f5f03c0fe9,{open:r,onOpenChange:o},external_React_namespaceObject.createElement($5d3850c4d0b4e6c7$export$602eac185826482c,{container:u},external_React_namespaceObject.createElement($5d3850c4d0b4e6c7$export$c6fdb837b070b4ff,{"cmdk-overlay":""}),external_React_namespaceObject.createElement($5d3850c4d0b4e6c7$export$7c6e2c02157bb7d2,{"aria-label":n.label,"cmdk-dialog":""},external_React_namespaceObject.createElement(oe,{ref:a,...l}))))}),he=external_React_namespaceObject.forwardRef((n,a)=>{let r=external_React_namespaceObject.useRef(!0),o=T(u=>u.filtered.count===0);return external_React_namespaceObject.useEffect(()=>{r.current=!1},[]),r.current||!o?null:external_React_namespaceObject.createElement("div",{ref:a,...n,"cmdk-empty":"",role:"presentation"})}),Ee=external_React_namespaceObject.forwardRef((n,a)=>{let{progress:r,children:o,...u}=n;return external_React_namespaceObject.createElement("div",{ref:a,...u,"cmdk-loading":"",role:"progressbar","aria-valuenow":r,"aria-valuemin":0,"aria-valuemax":100,"aria-label":"Loading..."},external_React_namespaceObject.createElement("div",{"aria-hidden":!0},o))}),Le=Object.assign(oe,{List:Re,Item:me,Input:ve,Group:pe,Separator:ge,Dialog:be,Empty:he,Loading:Ee});function Se(n,a){let r=n.nextElementSibling;for(;r;){if(r.matches(a))return r;r=r.nextElementSibling}}function Ce(n,a){let r=n.previousElementSibling;for(;r;){if(r.matches(a))return r;r=r.previousElementSibling}}function ae(n){let a=external_React_namespaceObject.useRef(n);return L(()=>{a.current=n}),a}var L=typeof window>"u"?external_React_namespaceObject.useEffect:external_React_namespaceObject.useLayoutEffect;function x(n){let a=external_React_namespaceObject.useRef();return a.current===void 0&&(a.current=n()),a}function H(n){return a=>{n.forEach(r=>{typeof r=="function"?r(a):r!=null&&(r.current=a)})}}function T(n){let a=U(),r=()=>n(a.snapshot());return external_React_namespaceObject.useSyncExternalStore(a.subscribe,r,r)}function se(n,a,r){let o=external_React_namespaceObject.useRef(),u=k();return L(()=>{var p;let l=(()=>{var f;for(let d of r){if(typeof d=="string")return d.trim().toLowerCase();if(typeof d=="object"&&"current"in d&&d.current)return(f=d.current.textContent)==null?void 0:f.trim().toLowerCase()}})();u.value(n,l),(p=a.current)==null||p.setAttribute(S,l),o.current=l}),o}var ye=()=>{let[n,a]=external_React_namespaceObject.useState(),r=x(()=>new Map);return L(()=>{r.current.forEach(o=>o()),r.current=new Map},[n]),(o,u)=>{r.current.set(o,u),a({})}},xe={position:"absolute",width:"1px",height:"1px",padding:"0",margin:"-1px",overflow:"hidden",clip:"rect(0, 0, 0, 0)",whiteSpace:"nowrap",borderWidth:"0"};

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(4403);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","keyboardShortcuts"]
var external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */


/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps}                                 props icon is the SVG component to render
 *                                                          size is a number specifiying the icon size in pixels
 *                                                          Other props will be passed to wrapped SVG component
 * @param {import('react').ForwardedRef<HTMLElement>} ref   The forwarded ref to the SVG element.
 *
 * @return {JSX.Element}  Icon component
 */
function Icon({
  icon,
  size = 24,
  ...props
}, ref) {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props,
    ref
  });
}
/* harmony default export */ var icon = ((0,external_wp_element_namespaceObject.forwardRef)(Icon));

;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/search.js

/**
 * WordPress dependencies
 */

const search = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 5c-3.3 0-6 2.7-6 6 0 1.4.5 2.7 1.3 3.7l-3.8 3.8 1.1 1.1 3.8-3.8c1 .8 2.3 1.3 3.7 1.3 3.3 0 6-2.7 6-6S16.3 5 13 5zm0 10.5c-2.5 0-4.5-2-4.5-4.5s2-4.5 4.5-4.5 4.5 2 4.5 4.5-2 4.5-4.5 4.5z"
}));
/* harmony default export */ var library_search = (search);

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/store/reducer.js
/**
 * WordPress dependencies
 */


/**
 * Reducer returning the registered commands
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
function commands(state = {}, action) {
  switch (action.type) {
    case 'REGISTER_COMMAND':
      return {
        ...state,
        [action.name]: {
          name: action.name,
          label: action.label,
          searchLabel: action.searchLabel,
          context: action.context,
          callback: action.callback,
          icon: action.icon
        }
      };
    case 'UNREGISTER_COMMAND':
      {
        const {
          [action.name]: _,
          ...remainingState
        } = state;
        return remainingState;
      }
  }
  return state;
}

/**
 * Reducer returning the command loaders
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
function commandLoaders(state = {}, action) {
  switch (action.type) {
    case 'REGISTER_COMMAND_LOADER':
      return {
        ...state,
        [action.name]: {
          name: action.name,
          context: action.context,
          hook: action.hook
        }
      };
    case 'UNREGISTER_COMMAND_LOADER':
      {
        const {
          [action.name]: _,
          ...remainingState
        } = state;
        return remainingState;
      }
  }
  return state;
}

/**
 * Reducer returning the command palette open state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {boolean} Updated state.
 */
function isOpen(state = false, action) {
  switch (action.type) {
    case 'OPEN':
      return true;
    case 'CLOSE':
      return false;
  }
  return state;
}

/**
 * Reducer returning the command palette's active context.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {boolean} Updated state.
 */
function context(state = 'root', action) {
  switch (action.type) {
    case 'SET_CONTEXT':
      return action.context;
  }
  return state;
}
const reducer = (0,external_wp_data_namespaceObject.combineReducers)({
  commands,
  commandLoaders,
  isOpen,
  context
});
/* harmony default export */ var store_reducer = (reducer);

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/store/actions.js
/** @typedef {import('@wordpress/keycodes').WPKeycodeModifier} WPKeycodeModifier */

/**
 * Configuration of a registered keyboard shortcut.
 *
 * @typedef {Object} WPCommandConfig
 *
 * @property {string}      name        Command name.
 * @property {string}      label       Command label.
 * @property {string=}     searchLabel Command search label.
 * @property {string=}     context     Command context.
 * @property {JSX.Element} icon        Command icon.
 * @property {Function}    callback    Command callback.
 */

/**
 * @typedef {(search: string) => WPCommandConfig[]} WPCommandLoaderHook hoo
 */

/**
 * Command loader config.
 *
 * @typedef {Object} WPCommandLoaderConfig
 *
 * @property {string}              name    Command loader name.
 * @property {string=}             context Command loader context.
 * @property {WPCommandLoaderHook} hook    Command loader hook.
 */

/**
 * Returns an action object used to register a new command.
 *
 * @param {WPCommandConfig} config Command config.
 *
 * @return {Object} action.
 */
function registerCommand(config) {
  return {
    type: 'REGISTER_COMMAND',
    ...config
  };
}

/**
 * Returns an action object used to unregister a command.
 *
 * @param {string} name Command name.
 *
 * @return {Object} action.
 */
function unregisterCommand(name) {
  return {
    type: 'UNREGISTER_COMMAND',
    name
  };
}

/**
 * Register command loader.
 *
 * @param {WPCommandLoaderConfig} config Command loader config.
 *
 * @return {Object} action.
 */
function registerCommandLoader(config) {
  return {
    type: 'REGISTER_COMMAND_LOADER',
    ...config
  };
}

/**
 * Unregister command loader hook.
 *
 * @param {string} name Command loader name.
 *
 * @return {Object} action.
 */
function unregisterCommandLoader(name) {
  return {
    type: 'UNREGISTER_COMMAND_LOADER',
    name
  };
}

/**
 * Opens the command palette.
 *
 * @return {Object} action.
 */
function actions_open() {
  return {
    type: 'OPEN'
  };
}

/**
 * Closes the command palette.
 *
 * @return {Object} action.
 */
function actions_close() {
  return {
    type: 'CLOSE'
  };
}

;// CONCATENATED MODULE: ./node_modules/rememo/rememo.js


/** @typedef {(...args: any[]) => *[]} GetDependants */

/** @typedef {() => void} Clear */

/**
 * @typedef {{
 *   getDependants: GetDependants,
 *   clear: Clear
 * }} EnhancedSelector
 */

/**
 * Internal cache entry.
 *
 * @typedef CacheNode
 *
 * @property {?CacheNode|undefined} [prev] Previous node.
 * @property {?CacheNode|undefined} [next] Next node.
 * @property {*[]} args Function arguments for cache entry.
 * @property {*} val Function result.
 */

/**
 * @typedef Cache
 *
 * @property {Clear} clear Function to clear cache.
 * @property {boolean} [isUniqueByDependants] Whether dependants are valid in
 * considering cache uniqueness. A cache is unique if dependents are all arrays
 * or objects.
 * @property {CacheNode?} [head] Cache head.
 * @property {*[]} [lastDependants] Dependants from previous invocation.
 */

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {{}}
 */
var LEAF_KEY = {};

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @template T
 *
 * @param {T} value Value to return.
 *
 * @return {[T]} Value returned as entry in array.
 */
function arrayOf(value) {
	return [value];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
			return false;
		}
	}

	return true;
}

/**
 * Returns a memoized selector function. The getDependants function argument is
 * called before the memoized selector and is expected to return an immutable
 * reference or array of references on which the selector depends for computing
 * its own return value. The memoize cache is preserved only as long as those
 * dependant references remain the same. If getDependants returns a different
 * reference(s), the cache is cleared and the selector value regenerated.
 *
 * @template {(...args: *[]) => *} S
 *
 * @param {S} selector Selector function.
 * @param {GetDependants=} getDependants Dependant getter returning an array of
 * references used in cache bust consideration.
 */
/* harmony default export */ function rememo(selector, getDependants) {
	/** @type {WeakMap<*,*>} */
	var rootCache;

	/** @type {GetDependants} */
	var normalizedGetDependants = getDependants ? getDependants : arrayOf;

	/**
	 * Returns the cache for a given dependants array. When possible, a WeakMap
	 * will be used to create a unique cache for each set of dependants. This
	 * is feasible due to the nature of WeakMap in allowing garbage collection
	 * to occur on entries where the key object is no longer referenced. Since
	 * WeakMap requires the key to be an object, this is only possible when the
	 * dependant is object-like. The root cache is created as a hierarchy where
	 * each top-level key is the first entry in a dependants set, the value a
	 * WeakMap where each key is the next dependant, and so on. This continues
	 * so long as the dependants are object-like. If no dependants are object-
	 * like, then the cache is shared across all invocations.
	 *
	 * @see isObjectLike
	 *
	 * @param {*[]} dependants Selector dependants.
	 *
	 * @return {Cache} Cache object.
	 */
	function getCache(dependants) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i,
			dependant,
			map,
			cache;

		for (i = 0; i < dependants.length; i++) {
			dependant = dependants[i];

			// Can only compose WeakMap from object-like key.
			if (!isObjectLike(dependant)) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {*}    source    Source object for derivation.
	 * @param {...*} extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	/* eslint-enable jsdoc/check-param-names */
	function callSelector(/* source, ...extraArgs */) {
		var len = arguments.length,
			cache,
			node,
			i,
			args,
			dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		dependants = normalizedGetDependants.apply(null, args);
		cache = getCache(dependants);

		// If not guaranteed uniqueness by dependants (primitive type), shallow
		// compare against last dependants and, if references have changed,
		// destroy cache to recalculate result.
		if (!cache.isUniqueByDependants) {
			if (
				cache.lastDependants &&
				!isShallowEqual(dependants, cache.lastDependants, 0)
			) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				/** @type {CacheNode} */ (cache.head).prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (cache.head) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * Returns the registered static commands.
 *
 * @param {Object}  state      State tree.
 * @param {boolean} contextual Whether to return only contextual commands.
 *
 * @return {import('./actions').WPCommandConfig[]} The list of registered commands.
 */
const getCommands = rememo((state, contextual = false) => Object.values(state.commands).filter(command => {
  const isContextual = command.context && command.context === state.context;
  return contextual ? isContextual : !isContextual;
}), state => [state.commands, state.context]);

/**
 * Returns the registered command loaders.
 *
 * @param {Object}  state      State tree.
 * @param {boolean} contextual Whether to return only contextual command loaders.
 *
 * @return {import('./actions').WPCommandLoaderConfig[]} The list of registered command loaders.
 */
const getCommandLoaders = rememo((state, contextual = false) => Object.values(state.commandLoaders).filter(loader => {
  const isContextual = loader.context && loader.context === state.context;
  return contextual ? isContextual : !isContextual;
}), state => [state.commandLoaders, state.context]);

/**
 * Returns whether the command palette is open.
 *
 * @param {Object} state State tree.
 *
 * @return {boolean} Returns whether the command palette is open.
 */
function selectors_isOpen(state) {
  return state.isOpen;
}

/**
 * Returns whether the active context.
 *
 * @param {Object} state State tree.
 *
 * @return {string} Context.
 */
function getContext(state) {
  return state.context;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/store/private-actions.js
/**
 * Sets the active context.
 *
 * @param {string} context Context.
 *
 * @return {Object} action.
 */
function setContext(context) {
  return {
    type: 'SET_CONTEXT',
    context
  };
}

;// CONCATENATED MODULE: external ["wp","privateApis"]
var external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my theme or plugin will inevitably break in the next version of WordPress.', '@wordpress/commands');

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





const STORE_NAME = 'core/commands';

/**
 * Store definition for the commands namespace.
 *
 * See how the Commands Store is being used in components like [site-hub](https://github.com/WordPress/gutenberg/blob/HEAD/packages/edit-site/src/components/site-hub/index.js#L23) and [document-actions](https://github.com/WordPress/gutenberg/blob/HEAD/packages/edit-post/src/components/header/document-actions/index.js#L14).
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 *
 * @example
 * ```js
 * import { store as commandsStore } from '@wordpress/commands';
 * import { useDispatch } from '@wordpress/data';
 * ...
 * const { open: openCommandCenter } = useDispatch( commandsStore );
 * ```
 */
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);
unlock(store).registerPrivateActions(private_actions_namespaceObject);

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/components/command-menu.js

/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */

function CommandMenuLoader({
  name,
  search,
  hook,
  setLoader,
  close
}) {
  var _hook;
  const {
    isLoading,
    commands = []
  } = (_hook = hook({
    search
  })) !== null && _hook !== void 0 ? _hook : {};
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    setLoader(name, isLoading);
  }, [setLoader, name, isLoading]);
  if (!commands.length) {
    return null;
  }
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(Le.List, null, commands.map(command => {
    var _command$searchLabel;
    return (0,external_wp_element_namespaceObject.createElement)(Le.Item, {
      key: command.name,
      value: (_command$searchLabel = command.searchLabel) !== null && _command$searchLabel !== void 0 ? _command$searchLabel : command.label,
      onSelect: () => command.callback({
        close
      }),
      id: command.name
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
      alignment: "left",
      className: classnames_default()('commands-command-menu__item', {
        'has-icon': command.icon
      })
    }, command.icon && (0,external_wp_element_namespaceObject.createElement)(icon, {
      icon: command.icon
    }), (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextHighlight, {
      text: command.label,
      highlight: search
    }))));
  })));
}
function CommandMenuLoaderWrapper({
  hook,
  search,
  setLoader,
  close
}) {
  // The "hook" prop is actually a custom React hook
  // so to avoid breaking the rules of hooks
  // the CommandMenuLoaderWrapper component need to be
  // remounted on each hook prop change
  // We use the key state to make sure we do that properly.
  const currentLoader = (0,external_wp_element_namespaceObject.useRef)(hook);
  const [key, setKey] = (0,external_wp_element_namespaceObject.useState)(0);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (currentLoader.current !== hook) {
      currentLoader.current = hook;
      setKey(prevKey => prevKey + 1);
    }
  }, [hook]);
  return (0,external_wp_element_namespaceObject.createElement)(CommandMenuLoader, {
    key: key,
    hook: currentLoader.current,
    search: search,
    setLoader: setLoader,
    close: close
  });
}
function CommandMenuGroup({
  isContextual,
  search,
  setLoader,
  close
}) {
  const {
    commands,
    loaders
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getCommands,
      getCommandLoaders
    } = select(store);
    return {
      commands: getCommands(isContextual),
      loaders: getCommandLoaders(isContextual)
    };
  }, [isContextual]);
  if (!commands.length && !loaders.length) {
    return null;
  }
  return (0,external_wp_element_namespaceObject.createElement)(Le.Group, null, commands.map(command => {
    var _command$searchLabel2;
    return (0,external_wp_element_namespaceObject.createElement)(Le.Item, {
      key: command.name,
      value: (_command$searchLabel2 = command.searchLabel) !== null && _command$searchLabel2 !== void 0 ? _command$searchLabel2 : command.label,
      onSelect: () => command.callback({
        close
      }),
      id: command.name
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
      alignment: "left",
      className: classnames_default()('commands-command-menu__item', {
        'has-icon': command.icon
      })
    }, command.icon && (0,external_wp_element_namespaceObject.createElement)(icon, {
      icon: command.icon
    }), (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextHighlight, {
      text: command.label,
      highlight: search
    }))));
  }), loaders.map(loader => (0,external_wp_element_namespaceObject.createElement)(CommandMenuLoaderWrapper, {
    key: loader.name,
    hook: loader.hook,
    search: search,
    setLoader: setLoader,
    close: close
  })));
}
function CommandInput({
  isOpen,
  search,
  setSearch
}) {
  const commandMenuInput = (0,external_wp_element_namespaceObject.useRef)();
  const _value = T(state => state.value);
  const selectedItemId = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const item = document.querySelector(`[cmdk-item=""][data-value="${_value}"]`);
    return item?.getAttribute('id');
  }, [_value]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // Focus the command palette input when mounting the modal.
    if (isOpen) {
      commandMenuInput.current.focus();
    }
  }, [isOpen]);
  return (0,external_wp_element_namespaceObject.createElement)(Le.Input, {
    ref: commandMenuInput,
    value: search,
    onValueChange: setSearch,
    placeholder: (0,external_wp_i18n_namespaceObject.__)('Search for commands'),
    "aria-activedescendant": selectedItemId,
    icon: search
  });
}

/**
 * @ignore
 */
function CommandMenu() {
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  const [search, setSearch] = (0,external_wp_element_namespaceObject.useState)('');
  const isOpen = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).isOpen(), []);
  const {
    open,
    close
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const [loaders, setLoaders] = (0,external_wp_element_namespaceObject.useState)({});
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/commands',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Open the command palette.'),
      keyCombination: {
        modifier: 'primary',
        character: 'k'
      }
    });
  }, [registerShortcut]);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/commands', /** @type {import('react').KeyboardEventHandler} */
  event => {
    // Bails to avoid obscuring the effect of the preceding handler(s).
    if (event.defaultPrevented) return;
    event.preventDefault();
    if (isOpen) {
      close();
    } else {
      open();
    }
  }, {
    bindGlobal: true
  });
  const setLoader = (0,external_wp_element_namespaceObject.useCallback)((name, value) => setLoaders(current => ({
    ...current,
    [name]: value
  })), []);
  const closeAndReset = () => {
    setSearch('');
    close();
  };
  if (!isOpen) {
    return false;
  }
  const onKeyDown = event => {
    if (
    // Ignore keydowns from IMEs
    event.nativeEvent.isComposing ||
    // Workaround for Mac Safari where the final Enter/Backspace of an IME composition
    // is `isComposing=false`, even though it's technically still part of the composition.
    // These can only be detected by keyCode.
    event.keyCode === 229) {
      event.preventDefault();
    }
  };
  const isLoading = Object.values(loaders).some(Boolean);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "commands-command-menu",
    overlayClassName: "commands-command-menu__overlay",
    onRequestClose: closeAndReset,
    __experimentalHideHeader: true
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "commands-command-menu__container"
  }, (0,external_wp_element_namespaceObject.createElement)(Le, {
    label: (0,external_wp_i18n_namespaceObject.__)('Command palette'),
    onKeyDown: onKeyDown
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "commands-command-menu__header"
  }, (0,external_wp_element_namespaceObject.createElement)(icon, {
    icon: library_search
  }), (0,external_wp_element_namespaceObject.createElement)(CommandInput, {
    search: search,
    setSearch: setSearch,
    isOpen: isOpen
  })), (0,external_wp_element_namespaceObject.createElement)(Le.List, null, search && !isLoading && (0,external_wp_element_namespaceObject.createElement)(Le.Empty, null, (0,external_wp_i18n_namespaceObject.__)('No results found.')), (0,external_wp_element_namespaceObject.createElement)(CommandMenuGroup, {
    search: search,
    setLoader: setLoader,
    close: closeAndReset,
    isContextual: true
  }), search && (0,external_wp_element_namespaceObject.createElement)(CommandMenuGroup, {
    search: search,
    setLoader: setLoader,
    close: closeAndReset
  })))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/hooks/use-command-context.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



/**
 * Sets the active context of the command palette
 *
 * @param {string} context Context to set.
 */
function useCommandContext(context) {
  const {
    getContext
  } = (0,external_wp_data_namespaceObject.useSelect)(store);
  const initialContext = (0,external_wp_element_namespaceObject.useRef)(getContext());
  const {
    setContext
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store));
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    setContext(context);
  }, [context, setContext]);

  // This effects ensures that on unmount, we restore the context
  // that was set before the component actually mounts.
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const initialContextRef = initialContext.current;
    return () => setContext(initialContextRef);
  }, [setContext]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/private-apis.js
/**
 * Internal dependencies
 */



/**
 * @private
 */
const privateApis = {};
lock(privateApis, {
  useCommandContext: useCommandContext
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/hooks/use-command.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Attach a command to the command palette. Used for static commands.
 *
 * @param {import('../store/actions').WPCommandConfig} command command config.
 *
 * @example
 * ```js
 * import { useCommand } from '@wordpress/commands';
 * import { plus } from '@wordpress/icons';
 *
 * useCommand( {
 *     name: 'myplugin/my-command-name',
 *     label: __( 'Add new post' ),
 *	   icon: plus,
 *     callback: ({ close }) => {
 *         document.location.href = 'post-new.php';
 *         close();
 *     },
 * } );
 * ```
 */
function useCommand(command) {
  const {
    registerCommand,
    unregisterCommand
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const currentCallback = (0,external_wp_element_namespaceObject.useRef)(command.callback);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    currentCallback.current = command.callback;
  }, [command.callback]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerCommand({
      name: command.name,
      context: command.context,
      label: command.label,
      searchLabel: command.searchLabel,
      icon: command.icon,
      callback: (...args) => currentCallback.current(...args)
    });
    return () => {
      unregisterCommand(command.name);
    };
  }, [command.name, command.label, command.searchLabel, command.icon, command.context, registerCommand, unregisterCommand]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/hooks/use-command-loader.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Attach a command loader to the command palette. Used for dynamic commands.
 *
 * @param {import('../store/actions').WPCommandLoaderConfig} loader command loader config.
 *
 * @example
 * ```js
 * import { useCommandLoader } from '@wordpress/commands';
 * import { post, page, layout, symbolFilled } from '@wordpress/icons';
 *
 * const icons = {
 *     post,
 *     page,
 *     wp_template: layout,
 *     wp_template_part: symbolFilled,
 * };
 *
 * function usePageSearchCommandLoader( { search } ) {
 *     // Retrieve the pages for the "search" term.
 *     const { records, isLoading } = useSelect( ( select ) => {
 *         const { getEntityRecords } = select( coreStore );
 *         const query = {
 *             search: !! search ? search : undefined,
 *             per_page: 10,
 *             orderby: search ? 'relevance' : 'date',
 *         };
 *         return {
 *             records: getEntityRecords( 'postType', 'page', query ),
 *             isLoading: ! select( coreStore ).hasFinishedResolution(
 *                 'getEntityRecords',
 *                 'postType', 'page', query ]
 *             ),
 *         };
 *     }, [ search ] );
 *
 *     // Create the commands.
 *     const commands = useMemo( () => {
 *         return ( records ?? [] ).slice( 0, 10 ).map( ( record ) => {
 *             return {
 *                 name: record.title?.rendered + ' ' + record.id,
 *                 label: record.title?.rendered
 *                     ? record.title?.rendered
 *                     : __( '(no title)' ),
 *                 icon: icons[ postType ],
 *                 callback: ( { close } ) => {
 *                     const args = {
 *                         postType,
 *                         postId: record.id,
 *                         ...extraArgs,
 *                     };
 *                     document.location = addQueryArgs( 'site-editor.php', args );
 *                     close();
 *                 },
 *             };
 *         } );
 *     }, [ records, history ] );
 *
 *     return {
 *         commands,
 *         isLoading,
 *     };
 * }
 *
 * useCommandLoader( {
 *     name: 'myplugin/page-search',
 *     hook: usePageSearchCommandLoader,
 * } );
 * ```
 */
function useCommandLoader(loader) {
  const {
    registerCommandLoader,
    unregisterCommandLoader
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerCommandLoader({
      name: loader.name,
      hook: loader.hook,
      context: loader.context
    });
    return () => {
      unregisterCommandLoader(loader.name);
    };
  }, [loader.name, loader.hook, loader.context, registerCommandLoader, unregisterCommandLoader]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/commands/build-module/index.js






}();
(window.wp = window.wp || {}).commands = __webpack_exports__;
/******/ })()
;