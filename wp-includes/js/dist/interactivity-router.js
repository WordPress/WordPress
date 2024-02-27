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
  o: () => (/* binding */ actions),
  w: () => (/* binding */ state)
});

;// CONCATENATED MODULE: external "@wordpress/interactivity"
var x = (y) => {
	var x = {}; __webpack_require__.d(x, y); return x
} 
var y = (x) => (() => (x))
const interactivity_namespaceObject = x({ ["getConfig"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getConfig), ["privateApis"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.privateApis), ["store"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store) });
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity-router/build-module/index.js
/**
 * WordPress dependencies
 */

const {
  directivePrefix,
  getRegionRootFragment,
  initialVdom,
  toVdom,
  render,
  parseInitialData,
  populateInitialData,
  batch
} = (0,interactivity_namespaceObject.privateApis)('I acknowledge that using private APIs means my theme or plugin will inevitably break in the next version of WordPress.');

// The cache of visited and prefetched pages.
const pages = new Map();

// Helper to remove domain and hash from the URL. We are only interesting in
// caching the path and the query.
const getPagePath = url => {
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
const regionsToVdom = (dom, {
  vdom
} = {}) => {
  const regions = {};
  const attrName = `data-${directivePrefix}-router-region`;
  dom.querySelectorAll(`[${attrName}]`).forEach(region => {
    const id = region.getAttribute(attrName);
    regions[id] = vdom?.has(region) ? vdom.get(region) : toVdom(region);
  });
  const title = dom.querySelector('title')?.innerText;
  const initialData = parseInitialData(dom);
  return {
    regions,
    title,
    initialData
  };
};

// Render all interactive regions contained in the given page.
const renderRegions = page => {
  batch(() => {
    populateInitialData(page.initialData);
    const attrName = `data-${directivePrefix}-router-region`;
    document.querySelectorAll(`[${attrName}]`).forEach(region => {
      const id = region.getAttribute(attrName);
      const fragment = getRegionRootFragment(region);
      render(page.regions[id], fragment);
    });
    if (page.title) {
      document.title = page.title;
    }
  });
};

/**
 * Load the given page forcing a full page reload.
 *
 * The function returns a promise that won't resolve, useful to prevent any
 * potential feedback indicating that the navigation has finished while the new
 * page is being loaded.
 *
 * @param {string} href The page href.
 * @return {Promise} Promise that never resolves.
 */
const forcePageReload = href => {
  window.location.assign(href);
  return new Promise(() => {});
};

// Listen to the back and forward buttons and restore the page if it's in the
// cache.
window.addEventListener('popstate', async () => {
  const pagePath = getPagePath(window.location); // Remove hash.
  const page = pages.has(pagePath) && (await pages.get(pagePath));
  if (page) {
    renderRegions(page);
    // Update the URL in the state.
    state.url = window.location.href;
  } else {
    window.location.reload();
  }
});

// Cache the initial page using the intially parsed vDOM.
pages.set(getPagePath(window.location), Promise.resolve(regionsToVdom(document, {
  vdom: initialVdom
})));

// Variable to store the current navigation.
let navigatingTo = '';
const {
  state,
  actions
} = (0,interactivity_namespaceObject.store)('core/router', {
  state: {
    url: window.location.href,
    navigation: {
      hasStarted: false,
      hasFinished: false,
      texts: {}
    }
  },
  actions: {
    /**
     * Navigates to the specified page.
     *
     * This function normalizes the passed href, fetchs the page HTML if
     * needed, and updates any interactive regions whose contents have
     * changed. It also creates a new entry in the browser session history.
     *
     * @param {string}  href                               The page href.
     * @param {Object}  [options]                          Options object.
     * @param {boolean} [options.force]                    If true, it forces re-fetching the URL.
     * @param {string}  [options.html]                     HTML string to be used instead of fetching the requested URL.
     * @param {boolean} [options.replace]                  If true, it replaces the current entry in the browser session history.
     * @param {number}  [options.timeout]                  Time until the navigation is aborted, in milliseconds. Default is 10000.
     * @param {boolean} [options.loadingAnimation]         Whether an animation should be shown while navigating. Default to `true`.
     * @param {boolean} [options.screenReaderAnnouncement] Whether a message for screen readers should be announced while navigating. Default to `true`.
     *
     * @return {Promise} Promise that resolves once the navigation is completed or aborted.
     */
    *navigate(href, options = {}) {
      const {
        clientNavigationDisabled
      } = (0,interactivity_namespaceObject.getConfig)();
      if (clientNavigationDisabled) {
        yield forcePageReload(href);
      }
      const pagePath = getPagePath(href);
      const {
        navigation
      } = state;
      const {
        loadingAnimation = true,
        screenReaderAnnouncement = true,
        timeout = 10000
      } = options;
      navigatingTo = href;
      actions.prefetch(pagePath, options);

      // Create a promise that resolves when the specified timeout ends.
      // The timeout value is 10 seconds by default.
      const timeoutPromise = new Promise(resolve => setTimeout(resolve, timeout));

      // Don't update the navigation status immediately, wait 400 ms.
      const loadingTimeout = setTimeout(() => {
        if (navigatingTo !== href) return;
        if (loadingAnimation) {
          navigation.hasStarted = true;
          navigation.hasFinished = false;
        }
        if (screenReaderAnnouncement) {
          navigation.message = navigation.texts.loading;
        }
      }, 400);
      const page = yield Promise.race([pages.get(pagePath), timeoutPromise]);

      // Dismiss loading message if it hasn't been added yet.
      clearTimeout(loadingTimeout);

      // Once the page is fetched, the destination URL could have changed
      // (e.g., by clicking another link in the meantime). If so, bail
      // out, and let the newer execution to update the HTML.
      if (navigatingTo !== href) return;
      if (page && !page.initialData?.config?.['core/router']?.clientNavigationDisabled) {
        renderRegions(page);
        window.history[options.replace ? 'replaceState' : 'pushState']({}, '', href);

        // Update the URL in the state.
        state.url = href;

        // Update the navigation status once the the new page rendering
        // has been completed.
        if (loadingAnimation) {
          navigation.hasStarted = false;
          navigation.hasFinished = true;
        }
        if (screenReaderAnnouncement) {
          // Announce that the page has been loaded. If the message is the
          // same, we use a no-break space similar to the @wordpress/a11y
          // package: https://github.com/WordPress/gutenberg/blob/c395242b8e6ee20f8b06c199e4fc2920d7018af1/packages/a11y/src/filter-message.js#L20-L26
          navigation.message = navigation.texts.loaded + (navigation.message === navigation.texts.loaded ? '\u00A0' : '');
        }
      } else {
        yield forcePageReload(href);
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
      const {
        clientNavigationDisabled
      } = (0,interactivity_namespaceObject.getConfig)();
      if (clientNavigationDisabled) return;
      const pagePath = getPagePath(url);
      if (options.force || !pages.has(pagePath)) {
        pages.set(pagePath, fetchPage(pagePath, options));
      }
    }
  }
});

var __webpack_exports__actions = __webpack_exports__.o;
var __webpack_exports__state = __webpack_exports__.w;
export { __webpack_exports__actions as actions, __webpack_exports__state as state };
