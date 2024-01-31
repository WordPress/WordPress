import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ // The require scope
/******/ var __webpack_require__ = {};
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  N: () => (/* binding */ actions),
  S: () => (/* binding */ state)
});

;// CONCATENATED MODULE: external "@wordpress/interactivity"
var x = y => { var x = {}; __webpack_require__.d(x, y); return x; }
var y = x => () => x
const interactivity_namespaceObject = x({ ["directivePrefix"]: () => __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.directivePrefix, ["getRegionRootFragment"]: () => __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getRegionRootFragment, ["render"]: () => __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.render, ["store"]: () => __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store, ["toVdom"]: () => __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.toVdom });
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity-router/build-module/index.js
/**
 * WordPress dependencies
 */


// The cache of visited and prefetched pages.
const pages = new Map();

// Helper to remove domain and hash from the URL. We are only interesting in
// caching the path and the query.
const cleanUrl = url => {
  const u = new URL(url, window.location);
  return u.pathname + u.search;
};

// Fetch a new page and convert it to a static virtual DOM.
const fetchPage = async (url, {
  html
}) => {
  try {
    if (!html) {
      const res = await window.fetch(url);
      if (res.status !== 200) return false;
      html = await res.text();
    }
    const dom = new window.DOMParser().parseFromString(html, 'text/html');
    return regionsToVdom(dom);
  } catch (e) {
    return false;
  }
};

// Return an object with VDOM trees of those HTML regions marked with a
// `router-region` directive.
const regionsToVdom = dom => {
  const regions = {};
  const attrName = `data-${interactivity_namespaceObject.directivePrefix}-router-region`;
  dom.querySelectorAll(`[${attrName}]`).forEach(region => {
    const id = region.getAttribute(attrName);
    regions[id] = (0,interactivity_namespaceObject.toVdom)(region);
  });
  const title = dom.querySelector('title')?.innerText;
  return {
    regions,
    title
  };
};

// Render all interactive regions contained in the given page.
const renderRegions = page => {
  const attrName = `data-${interactivity_namespaceObject.directivePrefix}-router-region`;
  document.querySelectorAll(`[${attrName}]`).forEach(region => {
    const id = region.getAttribute(attrName);
    const fragment = (0,interactivity_namespaceObject.getRegionRootFragment)(region);
    (0,interactivity_namespaceObject.render)(page.regions[id], fragment);
  });
  if (page.title) {
    document.title = page.title;
  }
};

// Variable to store the current navigation.
let navigatingTo = '';

// Listen to the back and forward buttons and restore the page if it's in the
// cache.
window.addEventListener('popstate', async () => {
  const url = cleanUrl(window.location); // Remove hash.
  const page = pages.has(url) && (await pages.get(url));
  if (page) {
    renderRegions(page);
  } else {
    window.location.reload();
  }
});

// Cache the current regions.
pages.set(cleanUrl(window.location), Promise.resolve(regionsToVdom(document)));
const {
  state,
  actions
} = (0,interactivity_namespaceObject.store)('core/router', {
  actions: {
    /**
     * Navigates to the specified page.
     *
     * This function normalizes the passed href, fetchs the page HTML if
     * needed, and updates any interactive regions whose contents have
     * changed. It also creates a new entry in the browser session history.
     *
     * @param {string}  href              The page href.
     * @param {Object}  [options]         Options object.
     * @param {boolean} [options.force]   If true, it forces re-fetching the
     *                                    URL.
     * @param {string}  [options.html]    HTML string to be used instead of
     *                                    fetching the requested URL.
     * @param {boolean} [options.replace] If true, it replaces the current
     *                                    entry in the browser session
     *                                    history.
     * @param {number}  [options.timeout] Time until the navigation is
     *                                    aborted, in milliseconds. Default
     *                                    is 10000.
     *
     * @return {Promise} Promise that resolves once the navigation is
     *                   completed or aborted.
     */
    *navigate(href, options = {}) {
      const url = cleanUrl(href);
      navigatingTo = href;
      actions.prefetch(url, options);

      // Create a promise that resolves when the specified timeout ends.
      // The timeout value is 10 seconds by default.
      const timeoutPromise = new Promise(resolve => {
        var _options$timeout;
        return setTimeout(resolve, (_options$timeout = options.timeout) !== null && _options$timeout !== void 0 ? _options$timeout : 10000);
      });
      const page = yield Promise.race([pages.get(url), timeoutPromise]);

      // Once the page is fetched, the destination URL could have changed
      // (e.g., by clicking another link in the meantime). If so, bail
      // out, and let the newer execution to update the HTML.
      if (navigatingTo !== href) return;
      if (page) {
        renderRegions(page);
        window.history[options.replace ? 'replaceState' : 'pushState']({}, '', href);
      } else {
        window.location.assign(href);
        yield new Promise(() => {});
      }
    },
    /**
     * Prefetchs the page with the passed URL.
     *
     * The function normalizes the URL and stores internally the fetch
     * promise, to avoid triggering a second fetch for an ongoing request.
     *
     * @param {string}  url             The page URL.
     * @param {Object}  [options]       Options object.
     * @param {boolean} [options.force] Force fetching the URL again.
     * @param {string}  [options.html]  HTML string to be used instead of
     *                                  fetching the requested URL.
     */
    prefetch(url, options = {}) {
      url = cleanUrl(url);
      if (options.force || !pages.has(url)) {
        pages.set(url, fetchPage(url, options));
      }
    }
  }
});

var __webpack_exports__actions = __webpack_exports__.N;
var __webpack_exports__state = __webpack_exports__.S;
export { __webpack_exports__actions as actions, __webpack_exports__state as state };
