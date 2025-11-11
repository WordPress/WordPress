/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 1233:
/***/ ((module) => {

module.exports = window["wp"]["preferences"];

/***/ }),

/***/ 6087:
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ 7143:
/***/ ((module) => {

module.exports = window["wp"]["data"];

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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  loadView: () => (/* reexport */ loadView),
  useView: () => (/* reexport */ useView)
});

;// ./node_modules/dequal/dist/index.mjs
var has = Object.prototype.hasOwnProperty;

function find(iter, tar, key) {
	for (key of iter.keys()) {
		if (dequal(key, tar)) return key;
	}
}

function dequal(foo, bar) {
	var ctor, len, tmp;
	if (foo === bar) return true;

	if (foo && bar && (ctor=foo.constructor) === bar.constructor) {
		if (ctor === Date) return foo.getTime() === bar.getTime();
		if (ctor === RegExp) return foo.toString() === bar.toString();

		if (ctor === Array) {
			if ((len=foo.length) === bar.length) {
				while (len-- && dequal(foo[len], bar[len]));
			}
			return len === -1;
		}

		if (ctor === Set) {
			if (foo.size !== bar.size) {
				return false;
			}
			for (len of foo) {
				tmp = len;
				if (tmp && typeof tmp === 'object') {
					tmp = find(bar, tmp);
					if (!tmp) return false;
				}
				if (!bar.has(tmp)) return false;
			}
			return true;
		}

		if (ctor === Map) {
			if (foo.size !== bar.size) {
				return false;
			}
			for (len of foo) {
				tmp = len[0];
				if (tmp && typeof tmp === 'object') {
					tmp = find(bar, tmp);
					if (!tmp) return false;
				}
				if (!dequal(len[1], bar.get(tmp))) {
					return false;
				}
			}
			return true;
		}

		if (ctor === ArrayBuffer) {
			foo = new Uint8Array(foo);
			bar = new Uint8Array(bar);
		} else if (ctor === DataView) {
			if ((len=foo.byteLength) === bar.byteLength) {
				while (len-- && foo.getInt8(len) === bar.getInt8(len));
			}
			return len === -1;
		}

		if (ArrayBuffer.isView(foo)) {
			if ((len=foo.byteLength) === bar.byteLength) {
				while (len-- && foo[len] === bar[len]);
			}
			return len === -1;
		}

		if (!ctor || typeof foo === 'object') {
			len = 0;
			for (ctor in foo) {
				if (has.call(foo, ctor) && ++len && !has.call(bar, ctor)) return false;
				if (!(ctor in bar) || !dequal(foo[ctor], bar[ctor])) return false;
			}
			return Object.keys(bar).length === len;
		}
	}

	return foo !== foo && bar !== bar;
}

;// ./node_modules/@wordpress/views/build-module/preference-keys.js
function generatePreferenceKey(kind, name, slug) {
  return `dataviews-${kind}-${name}-${slug}`;
}


// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(6087);
// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
// EXTERNAL MODULE: external ["wp","preferences"]
var external_wp_preferences_ = __webpack_require__(1233);
;// ./node_modules/@wordpress/views/build-module/use-view.js





function omit(obj, keys) {
  const result = { ...obj };
  for (const key of keys) {
    delete result[key];
  }
  return result;
}
function useView(config) {
  const { kind, name, slug, defaultView, queryParams, onChangeQueryParams } = config;
  const preferenceKey = generatePreferenceKey(kind, name, slug);
  const persistedView = (0,external_wp_data_.useSelect)(
    (select) => {
      return select(external_wp_preferences_.store).get(
        "core/views",
        preferenceKey
      );
    },
    [preferenceKey]
  );
  const { set } = (0,external_wp_data_.useDispatch)(external_wp_preferences_.store);
  const baseView = persistedView ?? defaultView;
  const page = Number(queryParams?.page ?? baseView.page ?? 1);
  const search = queryParams?.search ?? baseView.search ?? "";
  const view = (0,external_wp_element_.useMemo)(() => {
    return {
      ...baseView,
      page,
      search
    };
  }, [baseView, page, search]);
  const isModified = !!persistedView;
  const updateView = (0,external_wp_element_.useCallback)(
    (newView) => {
      const urlParams = {
        page: newView?.page,
        search: newView?.search
      };
      const preferenceView = omit(newView, ["page", "search"]);
      if (onChangeQueryParams && !dequal(urlParams, { page, search })) {
        onChangeQueryParams(urlParams);
      }
      if (!dequal(baseView, preferenceView)) {
        if (dequal(preferenceView, defaultView)) {
          set("core/views", preferenceKey, void 0);
        } else {
          set("core/views", preferenceKey, preferenceView);
        }
      }
    },
    [
      onChangeQueryParams,
      page,
      search,
      baseView,
      defaultView,
      set,
      preferenceKey
    ]
  );
  const resetToDefault = (0,external_wp_element_.useCallback)(() => {
    set("core/views", preferenceKey, void 0);
  }, [preferenceKey, set]);
  return {
    view,
    isModified,
    updateView,
    resetToDefault
  };
}


;// ./node_modules/@wordpress/views/build-module/load-view.js



async function loadView(config) {
  const { kind, name, slug, defaultView, queryParams } = config;
  const preferenceKey = generatePreferenceKey(kind, name, slug);
  const persistedView = (0,external_wp_data_.select)(external_wp_preferences_.store).get(
    "core/views",
    preferenceKey
  );
  const baseView = persistedView ?? defaultView;
  const page = queryParams?.page ?? 1;
  const search = queryParams?.search ?? "";
  return {
    ...baseView,
    page,
    search
  };
}


;// ./node_modules/@wordpress/views/build-module/index.js




(window.wp = window.wp || {}).views = __webpack_exports__;
/******/ })()
;