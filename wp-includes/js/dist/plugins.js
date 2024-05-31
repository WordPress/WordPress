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
  PluginArea: () => (/* reexport */ plugin_area),
  getPlugin: () => (/* reexport */ getPlugin),
  getPlugins: () => (/* reexport */ getPlugins),
  registerPlugin: () => (/* reexport */ registerPlugin),
  unregisterPlugin: () => (/* reexport */ unregisterPlugin),
  usePluginContext: () => (/* reexport */ usePluginContext),
  withPluginContext: () => (/* reexport */ withPluginContext)
});

;// CONCATENATED MODULE: ./node_modules/memize/dist/index.js
/**
 * Memize options object.
 *
 * @typedef MemizeOptions
 *
 * @property {number} [maxSize] Maximum size of the cache.
 */

/**
 * Internal cache entry.
 *
 * @typedef MemizeCacheNode
 *
 * @property {?MemizeCacheNode|undefined} [prev] Previous node.
 * @property {?MemizeCacheNode|undefined} [next] Next node.
 * @property {Array<*>}                   args   Function arguments for cache
 *                                               entry.
 * @property {*}                          val    Function result.
 */

/**
 * Properties of the enhanced function for controlling cache.
 *
 * @typedef MemizeMemoizedFunction
 *
 * @property {()=>void} clear Clear the cache.
 */

/**
 * Accepts a function to be memoized, and returns a new memoized function, with
 * optional options.
 *
 * @template {(...args: any[]) => any} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {((...args: Parameters<F>) => ReturnType<F>) & MemizeMemoizedFunction} Memoized function.
 */
function memize(fn, options) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

	function memoized(/* ...args */) {
		var node = head,
			len = arguments.length,
			args,
			i;

		searchCache: while (node) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if (node.args.length !== arguments.length) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for (i = 0; i < len; i++) {
				if (node.args[i] !== arguments[i]) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== head) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if (node === tail) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				/** @type {MemizeCacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ (head).prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply(null, args),
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (head) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if (size === /** @type {MemizeOptions} */ (options).maxSize) {
			tail = /** @type {MemizeCacheNode} */ (tail).prev;
			/** @type {MemizeCacheNode} */ (tail).next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function () {
		head = null;
		tail = null;
		size = 0;
	};

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}



;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: external ["wp","isShallowEqual"]
const external_wp_isShallowEqual_namespaceObject = window["wp"]["isShallowEqual"];
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_namespaceObject);
;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/plugins/build-module/components/plugin-context/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

const Context = (0,external_wp_element_namespaceObject.createContext)({
  name: null,
  icon: null
});
const PluginContextProvider = Context.Provider;

/**
 * A hook that returns the plugin context.
 *
 * @return {PluginContext} Plugin context
 */
function usePluginContext() {
  return (0,external_wp_element_namespaceObject.useContext)(Context);
}

/**
 * A Higher Order Component used to inject Plugin context to the
 * wrapped component.
 *
 * @param  mapContextToProps Function called on every context change,
 *                           expected to return object of props to
 *                           merge with the component's own props.
 *
 * @return {Component} Enhanced component with injected context as props.
 */
const withPluginContext = mapContextToProps => (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(OriginalComponent => {
  return props => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Context.Consumer, {
    children: context => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(OriginalComponent, {
      ...props,
      ...mapContextToProps(context, props)
    })
  });
}, 'withPluginContext');

;// CONCATENATED MODULE: ./node_modules/@wordpress/plugins/build-module/components/plugin-error-boundary/index.js
/**
 * WordPress dependencies
 */

class PluginErrorBoundary extends external_wp_element_namespaceObject.Component {
  /**
   * @param {Object} props
   */
  constructor(props) {
    super(props);
    this.state = {
      hasError: false
    };
  }
  static getDerivedStateFromError() {
    return {
      hasError: true
    };
  }

  /**
   * @param {Error} error Error object passed by React.
   */
  componentDidCatch(error) {
    const {
      name,
      onError
    } = this.props;
    if (onError) {
      onError(name, error);
    }
  }
  render() {
    if (!this.state.hasError) {
      return this.props.children;
    }
    return null;
  }
}

;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plugins.js
/**
 * WordPress dependencies
 */


const plugins = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M10.5 4v4h3V4H15v4h1.5a1 1 0 011 1v4l-3 4v2a1 1 0 01-1 1h-3a1 1 0 01-1-1v-2l-3-4V9a1 1 0 011-1H9V4h1.5zm.5 12.5v2h2v-2l3-4v-3H8v3l3 4z"
  })
});
/* harmony default export */ const library_plugins = (plugins);

;// CONCATENATED MODULE: ./node_modules/@wordpress/plugins/build-module/api/index.js
/* eslint no-console: [ 'error', { allow: [ 'error' ] } ] */
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Defined behavior of a plugin type.
 */

/**
 * Plugin definitions keyed by plugin name.
 */
const api_plugins = {};

/**
 * Registers a plugin to the editor.
 *
 * @param name     A string identifying the plugin. Must be
 *                 unique across all registered plugins.
 * @param settings The settings for this plugin.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var el = React.createElement;
 * var Fragment = wp.element.Fragment;
 * var PluginSidebar = wp.editor.PluginSidebar;
 * var PluginSidebarMoreMenuItem = wp.editor.PluginSidebarMoreMenuItem;
 * var registerPlugin = wp.plugins.registerPlugin;
 * var moreIcon = React.createElement( 'svg' ); //... svg element.
 *
 * function Component() {
 * 	return el(
 * 		Fragment,
 * 		{},
 * 		el(
 * 			PluginSidebarMoreMenuItem,
 * 			{
 * 				target: 'sidebar-name',
 * 			},
 * 			'My Sidebar'
 * 		),
 * 		el(
 * 			PluginSidebar,
 * 			{
 * 				name: 'sidebar-name',
 * 				title: 'My Sidebar',
 * 			},
 * 			'Content of the sidebar'
 * 		)
 * 	);
 * }
 * registerPlugin( 'plugin-name', {
 * 	icon: moreIcon,
 * 	render: Component,
 * 	scope: 'my-page',
 * } );
 * ```
 *
 * @example
 * ```js
 * // Using ESNext syntax
 * import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/editor';
 * import { registerPlugin } from '@wordpress/plugins';
 * import { more } from '@wordpress/icons';
 *
 * const Component = () => (
 * 	<>
 * 		<PluginSidebarMoreMenuItem
 * 			target="sidebar-name"
 * 		>
 * 			My Sidebar
 * 		</PluginSidebarMoreMenuItem>
 * 		<PluginSidebar
 * 			name="sidebar-name"
 * 			title="My Sidebar"
 * 		>
 * 			Content of the sidebar
 * 		</PluginSidebar>
 * 	</>
 * );
 *
 * registerPlugin( 'plugin-name', {
 * 	icon: more,
 * 	render: Component,
 * 	scope: 'my-page',
 * } );
 * ```
 *
 * @return The final plugin settings object.
 */
function registerPlugin(name, settings) {
  if (typeof settings !== 'object') {
    console.error('No settings object provided!');
    return null;
  }
  if (typeof name !== 'string') {
    console.error('Plugin name must be string.');
    return null;
  }
  if (!/^[a-z][a-z0-9-]*$/.test(name)) {
    console.error('Plugin name must include only lowercase alphanumeric characters or dashes, and start with a letter. Example: "my-plugin".');
    return null;
  }
  if (api_plugins[name]) {
    console.error(`Plugin "${name}" is already registered.`);
  }
  settings = (0,external_wp_hooks_namespaceObject.applyFilters)('plugins.registerPlugin', settings, name);
  const {
    render,
    scope
  } = settings;
  if (typeof render !== 'function') {
    console.error('The "render" property must be specified and must be a valid function.');
    return null;
  }
  if (scope) {
    if (typeof scope !== 'string') {
      console.error('Plugin scope must be string.');
      return null;
    }
    if (!/^[a-z][a-z0-9-]*$/.test(scope)) {
      console.error('Plugin scope must include only lowercase alphanumeric characters or dashes, and start with a letter. Example: "my-page".');
      return null;
    }
  }
  api_plugins[name] = {
    name,
    icon: library_plugins,
    ...settings
  };
  (0,external_wp_hooks_namespaceObject.doAction)('plugins.pluginRegistered', settings, name);
  return settings;
}

/**
 * Unregisters a plugin by name.
 *
 * @param name Plugin name.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var unregisterPlugin = wp.plugins.unregisterPlugin;
 *
 * unregisterPlugin( 'plugin-name' );
 * ```
 *
 * @example
 * ```js
 * // Using ESNext syntax
 * import { unregisterPlugin } from '@wordpress/plugins';
 *
 * unregisterPlugin( 'plugin-name' );
 * ```
 *
 * @return The previous plugin settings object, if it has been
 *         successfully unregistered; otherwise `undefined`.
 */
function unregisterPlugin(name) {
  if (!api_plugins[name]) {
    console.error('Plugin "' + name + '" is not registered.');
    return;
  }
  const oldPlugin = api_plugins[name];
  delete api_plugins[name];
  (0,external_wp_hooks_namespaceObject.doAction)('plugins.pluginUnregistered', oldPlugin, name);
  return oldPlugin;
}

/**
 * Returns a registered plugin settings.
 *
 * @param name Plugin name.
 *
 * @return Plugin setting.
 */
function getPlugin(name) {
  return api_plugins[name];
}

/**
 * Returns all registered plugins without a scope or for a given scope.
 *
 * @param scope The scope to be used when rendering inside
 *              a plugin area. No scope by default.
 *
 * @return The list of plugins without a scope or for a given scope.
 */
function getPlugins(scope) {
  return Object.values(api_plugins).filter(plugin => plugin.scope === scope);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/plugins/build-module/components/plugin-area/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




const getPluginContext = memize((icon, name) => ({
  icon,
  name
}));

/**
 * A component that renders all plugin fills in a hidden div.
 *
 * @param  props
 * @param  props.scope
 * @param  props.onError
 * @example
 * ```js
 * // Using ES5 syntax
 * var el = React.createElement;
 * var PluginArea = wp.plugins.PluginArea;
 *
 * function Layout() {
 * 	return el(
 * 		'div',
 * 		{ scope: 'my-page' },
 * 		'Content of the page',
 * 		PluginArea
 * 	);
 * }
 * ```
 *
 * @example
 * ```js
 * // Using ESNext syntax
 * import { PluginArea } from '@wordpress/plugins';
 *
 * const Layout = () => (
 * 	<div>
 * 		Content of the page
 * 		<PluginArea scope="my-page" />
 * 	</div>
 * );
 * ```
 *
 * @return {Component} The component to be rendered.
 */
function PluginArea({
  scope,
  onError
}) {
  const store = (0,external_wp_element_namespaceObject.useMemo)(() => {
    let lastValue = [];
    return {
      subscribe(listener) {
        (0,external_wp_hooks_namespaceObject.addAction)('plugins.pluginRegistered', 'core/plugins/plugin-area/plugins-registered', listener);
        (0,external_wp_hooks_namespaceObject.addAction)('plugins.pluginUnregistered', 'core/plugins/plugin-area/plugins-unregistered', listener);
        return () => {
          (0,external_wp_hooks_namespaceObject.removeAction)('plugins.pluginRegistered', 'core/plugins/plugin-area/plugins-registered');
          (0,external_wp_hooks_namespaceObject.removeAction)('plugins.pluginUnregistered', 'core/plugins/plugin-area/plugins-unregistered');
        };
      },
      getValue() {
        const nextValue = getPlugins(scope);
        if (!external_wp_isShallowEqual_default()(lastValue, nextValue)) {
          lastValue = nextValue;
        }
        return lastValue;
      }
    };
  }, [scope]);
  const plugins = (0,external_wp_element_namespaceObject.useSyncExternalStore)(store.subscribe, store.getValue, store.getValue);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
    style: {
      display: 'none'
    },
    children: plugins.map(({
      icon,
      name,
      render: Plugin
    }) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(PluginContextProvider, {
      value: getPluginContext(icon, name),
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(PluginErrorBoundary, {
        name: name,
        onError: onError,
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Plugin, {})
      })
    }, name))
  });
}
/* harmony default export */ const plugin_area = (PluginArea);

;// CONCATENATED MODULE: ./node_modules/@wordpress/plugins/build-module/components/index.js



;// CONCATENATED MODULE: ./node_modules/@wordpress/plugins/build-module/index.js



(window.wp = window.wp || {}).plugins = __webpack_exports__;
/******/ })()
;