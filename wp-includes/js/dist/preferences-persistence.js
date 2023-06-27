/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "__unstableCreatePersistenceLayer": function() { return /* binding */ __unstableCreatePersistenceLayer; },
  "create": function() { return /* reexport */ create; }
});

;// CONCATENATED MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/create/debounce-async.js
/**
 * Performs a leading edge debounce of async functions.
 *
 * If three functions are throttled at the same time:
 * - The first happens immediately.
 * - The second is never called.
 * - The third happens `delayMS` milliseconds after the first has resolved.
 *
 * This is distinct from `{ debounce } from @wordpress/compose` in that it
 * waits for promise resolution.
 *
 * @param {Function} func    A function that returns a promise.
 * @param {number}   delayMS A delay in milliseconds.
 *
 * @return {Function} A function that debounce whatever function is passed
 *                    to it.
 */
function debounceAsync(func, delayMS) {
  let timeoutId;
  let activePromise;
  return async function debounced(...args) {
    // This is a leading edge debounce. If there's no promise or timeout
    // in progress, call the debounced function immediately.
    if (!activePromise && !timeoutId) {
      return new Promise((resolve, reject) => {
        // Keep a reference to the promise.
        activePromise = func(...args).then((...thenArgs) => {
          resolve(...thenArgs);
        }).catch(error => {
          reject(error);
        }).finally(() => {
          // As soon this promise is complete, clear the way for the
          // next one to happen immediately.
          activePromise = null;
        });
      });
    }

    if (activePromise) {
      // Let any active promises finish before queuing the next request.
      await activePromise;
    } // Clear any active timeouts, abandoning any requests that have
    // been queued but not been made.


    if (timeoutId) {
      clearTimeout(timeoutId);
      timeoutId = null;
    } // Trigger any trailing edge calls to the function.


    return new Promise((resolve, reject) => {
      // Schedule the next request but with a delay.
      timeoutId = setTimeout(() => {
        activePromise = func(...args).then((...thenArgs) => {
          resolve(...thenArgs);
        }).catch(error => {
          reject(error);
        }).finally(() => {
          // As soon this promise is complete, clear the way for the
          // next one to happen immediately.
          activePromise = null;
          timeoutId = null;
        });
      }, delayMS);
    });
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/create/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const EMPTY_OBJECT = {};
const localStorage = window.localStorage;
/**
 * Creates a persistence layer that stores data in WordPress user meta via the
 * REST API.
 *
 * @param {Object}  options
 * @param {?Object} options.preloadedData          Any persisted preferences data that should be preloaded.
 *                                                 When set, the persistence layer will avoid fetching data
 *                                                 from the REST API.
 * @param {?string} options.localStorageRestoreKey The key to use for restoring the localStorage backup, used
 *                                                 when the persistence layer calls `localStorage.getItem` or
 *                                                 `localStorage.setItem`.
 * @param {?number} options.requestDebounceMS      Debounce requests to the API so that they only occur at
 *                                                 minimum every `requestDebounceMS` milliseconds, and don't
 *                                                 swamp the server. Defaults to 2500ms.
 *
 * @return {Object} A persistence layer for WordPress user meta.
 */

function create({
  preloadedData,
  localStorageRestoreKey = 'WP_PREFERENCES_RESTORE_DATA',
  requestDebounceMS = 2500
} = {}) {
  let cache = preloadedData;
  const debouncedApiFetch = debounceAsync((external_wp_apiFetch_default()), requestDebounceMS);

  async function get() {
    if (cache) {
      return cache;
    }

    const user = await external_wp_apiFetch_default()({
      path: '/wp/v2/users/me?context=edit'
    });
    const serverData = user?.meta?.persisted_preferences;
    const localData = JSON.parse(localStorage.getItem(localStorageRestoreKey)); // Date parse returns NaN for invalid input. Coerce anything invalid
    // into a conveniently comparable zero.

    const serverTimestamp = Date.parse(serverData?._modified) || 0;
    const localTimestamp = Date.parse(localData?._modified) || 0; // Prefer server data if it exists and is more recent.
    // Otherwise fallback to localStorage data.

    if (serverData && serverTimestamp >= localTimestamp) {
      cache = serverData;
    } else if (localData) {
      cache = localData;
    } else {
      cache = EMPTY_OBJECT;
    }

    return cache;
  }

  function set(newData) {
    const dataWithTimestamp = { ...newData,
      _modified: new Date().toISOString()
    };
    cache = dataWithTimestamp; // Store data in local storage as a fallback. If for some reason the
    // api request does not complete or becomes unavailable, this data
    // can be used to restore preferences.

    localStorage.setItem(localStorageRestoreKey, JSON.stringify(dataWithTimestamp)); // The user meta endpoint seems susceptible to errors when consecutive
    // requests are made in quick succession. Ensure there's a gap between
    // any consecutive requests.
    //
    // Catch and do nothing with errors from the REST API.

    debouncedApiFetch({
      path: '/wp/v2/users/me',
      method: 'PUT',
      // `keepalive` will still send the request in the background,
      // even when a browser unload event might interrupt it.
      // This should hopefully make things more resilient.
      // This does have a size limit of 64kb, but the data is usually
      // much less.
      keepalive: true,
      data: {
        meta: {
          persisted_preferences: dataWithTimestamp
        }
      }
    }).catch(() => {});
  }

  return {
    get,
    set
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-feature-preferences.js
/**
 * Move the 'features' object in local storage from the sourceStoreName to the
 * preferences store data structure.
 *
 * Previously, editors used a data structure like this for feature preferences:
 * ```js
 * {
 *     'core/edit-post': {
 *         preferences: {
 *             features; {
 *                 topToolbar: true,
 *                 // ... other boolean 'feature' preferences
 *             },
 *         },
 *     },
 * }
 * ```
 *
 * And for a while these feature preferences lived in the interface package:
 * ```js
 * {
 *     'core/interface': {
 *         preferences: {
 *             features: {
 *                 'core/edit-post': {
 *                     topToolbar: true
 *                 }
 *             }
 *         }
 *     }
 * }
 * ```
 *
 * In the preferences store, 'features' aren't considered special, they're
 * merged to the root level of the scope along with other preferences:
 * ```js
 * {
 *     'core/preferences': {
 *         preferences: {
 *             'core/edit-post': {
 *                 topToolbar: true,
 *                 // ... any other preferences.
 *             }
 *         }
 *     }
 * }
 * ```
 *
 * This function handles moving from either the source store or the interface
 * store to the preferences data structure.
 *
 * @param {Object} state           The state before migration.
 * @param {string} sourceStoreName The name of the store that has persisted
 *                                 preferences to migrate to the preferences
 *                                 package.
 * @return {Object} The migrated state
 */
function moveFeaturePreferences(state, sourceStoreName) {
  const preferencesStoreName = 'core/preferences';
  const interfaceStoreName = 'core/interface'; // Features most recently (and briefly) lived in the interface package.
  // If data exists there, prioritize using that for the migration. If not
  // also check the original package as the user may have updated from an
  // older block editor version.

  const interfaceFeatures = state?.[interfaceStoreName]?.preferences?.features?.[sourceStoreName];
  const sourceFeatures = state?.[sourceStoreName]?.preferences?.features;
  const featuresToMigrate = interfaceFeatures ? interfaceFeatures : sourceFeatures;

  if (!featuresToMigrate) {
    return state;
  }

  const existingPreferences = state?.[preferencesStoreName]?.preferences; // Avoid migrating features again if they've previously been migrated.

  if (existingPreferences?.[sourceStoreName]) {
    return state;
  }

  let updatedInterfaceState;

  if (interfaceFeatures) {
    const otherInterfaceState = state?.[interfaceStoreName];
    const otherInterfaceScopes = state?.[interfaceStoreName]?.preferences?.features;
    updatedInterfaceState = {
      [interfaceStoreName]: { ...otherInterfaceState,
        preferences: {
          features: { ...otherInterfaceScopes,
            [sourceStoreName]: undefined
          }
        }
      }
    };
  }

  let updatedSourceState;

  if (sourceFeatures) {
    const otherSourceState = state?.[sourceStoreName];
    const sourcePreferences = state?.[sourceStoreName]?.preferences;
    updatedSourceState = {
      [sourceStoreName]: { ...otherSourceState,
        preferences: { ...sourcePreferences,
          features: undefined
        }
      }
    };
  } // Set the feature values in the interface store, the features
  // object is keyed by 'scope', which matches the store name for
  // the source.


  return { ...state,
    [preferencesStoreName]: {
      preferences: { ...existingPreferences,
        [sourceStoreName]: featuresToMigrate
      }
    },
    ...updatedInterfaceState,
    ...updatedSourceState
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-third-party-feature-preferences.js
/**
 * The interface package previously had a public API that could be used by
 * plugins to set persisted boolean 'feature' preferences.
 *
 * While usage was likely non-existent or very small, this function ensures
 * those are migrated to the preferences data structure. The interface
 * package's APIs have now been deprecated and use the preferences store.
 *
 * This will convert data that looks like this:
 * ```js
 * {
 *     'core/interface': {
 *         preferences: {
 *             features: {
 *                 'my-plugin': {
 *                     myPluginFeature: true
 *                 }
 *             }
 *         }
 *     }
 * }
 * ```
 *
 * To this:
 * ```js
 *  * {
 *     'core/preferences': {
 *         preferences: {
 *             'my-plugin': {
 *                 myPluginFeature: true
 *             }
 *         }
 *     }
 * }
 * ```
 *
 * @param {Object} state The local storage state
 *
 * @return {Object} The state with third party preferences moved to the
 *                  preferences data structure.
 */
function moveThirdPartyFeaturePreferencesToPreferences(state) {
  const interfaceStoreName = 'core/interface';
  const preferencesStoreName = 'core/preferences';
  const interfaceScopes = state?.[interfaceStoreName]?.preferences?.features;
  const interfaceScopeKeys = interfaceScopes ? Object.keys(interfaceScopes) : [];

  if (!interfaceScopeKeys?.length) {
    return state;
  }

  return interfaceScopeKeys.reduce(function (convertedState, scope) {
    if (scope.startsWith('core')) {
      return convertedState;
    }

    const featuresToMigrate = interfaceScopes?.[scope];

    if (!featuresToMigrate) {
      return convertedState;
    }

    const existingMigratedData = convertedState?.[preferencesStoreName]?.preferences?.[scope];

    if (existingMigratedData) {
      return convertedState;
    }

    const otherPreferencesScopes = convertedState?.[preferencesStoreName]?.preferences;
    const otherInterfaceState = convertedState?.[interfaceStoreName];
    const otherInterfaceScopes = convertedState?.[interfaceStoreName]?.preferences?.features;
    return { ...convertedState,
      [preferencesStoreName]: {
        preferences: { ...otherPreferencesScopes,
          [scope]: featuresToMigrate
        }
      },
      [interfaceStoreName]: { ...otherInterfaceState,
        preferences: {
          features: { ...otherInterfaceScopes,
            [scope]: undefined
          }
        }
      }
    };
  }, state);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-individual-preference.js
const identity = arg => arg;
/**
 * Migrates an individual item inside the `preferences` object for a package's store.
 *
 * Previously, some packages had individual 'preferences' of any data type, and many used
 * complex nested data structures. For example:
 * ```js
 * {
 *     'core/edit-post': {
 *         preferences: {
 *             panels: {
 *                 publish: {
 *                     opened: true,
 *                     enabled: true,
 *                 }
 *             },
 *             // ...other preferences.
 *         },
 *     },
 * }
 *
 * This function supports moving an individual preference like 'panels' above into the
 * preferences package data structure.
 *
 * It supports moving a preference to a particular scope in the preferences store and
 * optionally converting the data using a `convert` function.
 *
 * ```
 *
 * @param {Object}    state        The original state.
 * @param {Object}    migrate      An options object that contains details of the migration.
 * @param {string}    migrate.from The name of the store to migrate from.
 * @param {string}    migrate.to   The scope in the preferences store to migrate to.
 * @param {string}    key          The key in the preferences object to migrate.
 * @param {?Function} convert      A function that converts preferences from one format to another.
 */


function moveIndividualPreferenceToPreferences(state, {
  from: sourceStoreName,
  to: scope
}, key, convert = identity) {
  const preferencesStoreName = 'core/preferences';
  const sourcePreference = state?.[sourceStoreName]?.preferences?.[key]; // There's nothing to migrate, exit early.

  if (sourcePreference === undefined) {
    return state;
  }

  const targetPreference = state?.[preferencesStoreName]?.preferences?.[scope]?.[key]; // There's existing data at the target, so don't overwrite it, exit early.

  if (targetPreference) {
    return state;
  }

  const otherScopes = state?.[preferencesStoreName]?.preferences;
  const otherPreferences = state?.[preferencesStoreName]?.preferences?.[scope];
  const otherSourceState = state?.[sourceStoreName];
  const allSourcePreferences = state?.[sourceStoreName]?.preferences; // Pass an object with the key and value as this allows the convert
  // function to convert to a data structure that has different keys.

  const convertedPreferences = convert({
    [key]: sourcePreference
  });
  return { ...state,
    [preferencesStoreName]: {
      preferences: { ...otherScopes,
        [scope]: { ...otherPreferences,
          ...convertedPreferences
        }
      }
    },
    [sourceStoreName]: { ...otherSourceState,
      preferences: { ...allSourcePreferences,
        [key]: undefined
      }
    }
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-interface-enable-items.js
/**
 * Migrates interface 'enableItems' data to the preferences store.
 *
 * The interface package stores this data in this format:
 * ```js
 * {
 *     enableItems: {
 *         singleEnableItems: {
 * 	           complementaryArea: {
 *                 'core/edit-post': 'edit-post/document',
 *                 'core/edit-site': 'edit-site/global-styles',
 *             }
 *         },
 *         multipleEnableItems: {
 *             pinnedItems: {
 *                 'core/edit-post': {
 *                     'plugin-1': true,
 *                 },
 *                 'core/edit-site': {
 *                     'plugin-2': true,
 *                 },
 *             },
 *         }
 *     }
 * }
 * ```
 *
 * and it should be converted it to:
 * ```js
 * {
 *     'core/edit-post': {
 *         complementaryArea: 'edit-post/document',
 *         pinnedItems: {
 *             'plugin-1': true,
 *         },
 *     },
 *     'core/edit-site': {
 *         complementaryArea: 'edit-site/global-styles',
 *         pinnedItems: {
 *             'plugin-2': true,
 *         },
 *     },
 * }
 * ```
 *
 * @param {Object} state The local storage state.
 */
function moveInterfaceEnableItems(state) {
  var _state$preferencesSto, _sourceEnableItems$si, _sourceEnableItems$mu;

  const interfaceStoreName = 'core/interface';
  const preferencesStoreName = 'core/preferences';
  const sourceEnableItems = state?.[interfaceStoreName]?.enableItems; // There's nothing to migrate, exit early.

  if (!sourceEnableItems) {
    return state;
  }

  const allPreferences = (_state$preferencesSto = state?.[preferencesStoreName]?.preferences) !== null && _state$preferencesSto !== void 0 ? _state$preferencesSto : {}; // First convert complementaryAreas into the right format.
  // Use the existing preferences as the accumulator so that the data is
  // merged.

  const sourceComplementaryAreas = (_sourceEnableItems$si = sourceEnableItems?.singleEnableItems?.complementaryArea) !== null && _sourceEnableItems$si !== void 0 ? _sourceEnableItems$si : {};
  const preferencesWithConvertedComplementaryAreas = Object.keys(sourceComplementaryAreas).reduce((accumulator, scope) => {
    const data = sourceComplementaryAreas[scope]; // Don't overwrite any existing data in the preferences store.

    if (accumulator?.[scope]?.complementaryArea) {
      return accumulator;
    }

    return { ...accumulator,
      [scope]: { ...accumulator[scope],
        complementaryArea: data
      }
    };
  }, allPreferences); // Next feed the converted complementary areas back into a reducer that
  // converts the pinned items, resulting in the fully migrated data.

  const sourcePinnedItems = (_sourceEnableItems$mu = sourceEnableItems?.multipleEnableItems?.pinnedItems) !== null && _sourceEnableItems$mu !== void 0 ? _sourceEnableItems$mu : {};
  const allConvertedData = Object.keys(sourcePinnedItems).reduce((accumulator, scope) => {
    const data = sourcePinnedItems[scope]; // Don't overwrite any existing data in the preferences store.

    if (accumulator?.[scope]?.pinnedItems) {
      return accumulator;
    }

    return { ...accumulator,
      [scope]: { ...accumulator[scope],
        pinnedItems: data
      }
    };
  }, preferencesWithConvertedComplementaryAreas);
  const otherInterfaceItems = state[interfaceStoreName];
  return { ...state,
    [preferencesStoreName]: {
      preferences: allConvertedData
    },
    [interfaceStoreName]: { ...otherInterfaceItems,
      enableItems: undefined
    }
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/convert-edit-post-panels.js
/**
 * Convert the post editor's panels state from:
 * ```
 * {
 *     panels: {
 *         tags: {
 *             enabled: true,
 *             opened: true,
 *         },
 *         permalinks: {
 *             enabled: false,
 *             opened: false,
 *         },
 *     },
 * }
 * ```
 *
 * to a new, more concise data structure:
 * {
 *     inactivePanels: [
 *         'permalinks',
 *     ],
 *     openPanels: [
 *         'tags',
 *     ],
 * }
 *
 * @param {Object} preferences A preferences object.
 *
 * @return {Object} The converted data.
 */
function convertEditPostPanels(preferences) {
  var _preferences$panels;

  const panels = (_preferences$panels = preferences?.panels) !== null && _preferences$panels !== void 0 ? _preferences$panels : {};
  return Object.keys(panels).reduce((convertedData, panelName) => {
    const panel = panels[panelName];

    if (panel?.enabled === false) {
      convertedData.inactivePanels.push(panelName);
    }

    if (panel?.opened === true) {
      convertedData.openPanels.push(panelName);
    }

    return convertedData;
  }, {
    inactivePanels: [],
    openPanels: []
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/index.js
/**
 * Internal dependencies
 */





/**
 * Gets the legacy local storage data for a given user.
 *
 * @param {string | number} userId The user id.
 *
 * @return {Object | null} The local storage data.
 */

function getLegacyData(userId) {
  const key = `WP_DATA_USER_${userId}`;
  const unparsedData = window.localStorage.getItem(key);
  return JSON.parse(unparsedData);
}
/**
 * Converts data from the old `@wordpress/data` package format.
 *
 * @param {Object | null | undefined} data The legacy data in its original format.
 *
 * @return {Object | undefined} The converted data or `undefined` if there was
 *                              nothing to convert.
 */


function convertLegacyData(data) {
  if (!data) {
    return;
  } // Move boolean feature preferences from each editor into the
  // preferences store data structure.


  data = moveFeaturePreferences(data, 'core/edit-widgets');
  data = moveFeaturePreferences(data, 'core/customize-widgets');
  data = moveFeaturePreferences(data, 'core/edit-post');
  data = moveFeaturePreferences(data, 'core/edit-site'); // Move third party boolean feature preferences from the interface package
  // to the preferences store data structure.

  data = moveThirdPartyFeaturePreferencesToPreferences(data); // Move and convert the interface store's `enableItems` data into the
  // preferences data structure.

  data = moveInterfaceEnableItems(data); // Move individual ad-hoc preferences from various packages into the
  // preferences store data structure.

  data = moveIndividualPreferenceToPreferences(data, {
    from: 'core/edit-post',
    to: 'core/edit-post'
  }, 'hiddenBlockTypes');
  data = moveIndividualPreferenceToPreferences(data, {
    from: 'core/edit-post',
    to: 'core/edit-post'
  }, 'editorMode');
  data = moveIndividualPreferenceToPreferences(data, {
    from: 'core/edit-post',
    to: 'core/edit-post'
  }, 'preferredStyleVariations');
  data = moveIndividualPreferenceToPreferences(data, {
    from: 'core/edit-post',
    to: 'core/edit-post'
  }, 'panels', convertEditPostPanels);
  data = moveIndividualPreferenceToPreferences(data, {
    from: 'core/editor',
    to: 'core/edit-post'
  }, 'isPublishSidebarEnabled');
  data = moveIndividualPreferenceToPreferences(data, {
    from: 'core/edit-site',
    to: 'core/edit-site'
  }, 'editorMode'); // The new system is only concerned with persisting
  // 'core/preferences' preferences reducer, so only return that.

  return data?.['core/preferences']?.preferences;
}
/**
 * Gets the legacy local storage data for the given user and returns the
 * data converted to the new format.
 *
 * @param {string | number} userId The user id.
 *
 * @return {Object | undefined} The converted data or undefined if no local
 *                              storage data could be found.
 */

function convertLegacyLocalStorageData(userId) {
  const data = getLegacyData(userId);
  return convertLegacyData(data);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/migrations/preferences-package-data/convert-complementary-areas.js
function convertComplementaryAreas(state) {
  return Object.keys(state).reduce((stateAccumulator, scope) => {
    const scopeData = state[scope]; // If a complementary area is truthy, convert it to the `isComplementaryAreaVisible` boolean.

    if (scopeData?.complementaryArea) {
      const updatedScopeData = { ...scopeData
      };
      delete updatedScopeData.complementaryArea;
      updatedScopeData.isComplementaryAreaVisible = true;
      stateAccumulator[scope] = updatedScopeData;
      return stateAccumulator;
    }

    return stateAccumulator;
  }, state);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/migrations/preferences-package-data/index.js
/**
 * Internal dependencies
 */

function convertPreferencesPackageData(data) {
  return convertComplementaryAreas(data);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/preferences-persistence/build-module/index.js
/**
 * Internal dependencies
 */




/**
 * Creates the persistence layer with preloaded data.
 *
 * It prioritizes any data from the server, but falls back first to localStorage
 * restore data, and then to any legacy data.
 *
 * This function is used internally by WordPress in an inline script, so
 * prefixed with `__unstable`.
 *
 * @param {Object} serverData Preferences data preloaded from the server.
 * @param {string} userId     The user id.
 *
 * @return {Object} The persistence layer initialized with the preloaded data.
 */

function __unstableCreatePersistenceLayer(serverData, userId) {
  const localStorageRestoreKey = `WP_PREFERENCES_USER_${userId}`;
  const localData = JSON.parse(window.localStorage.getItem(localStorageRestoreKey)); // Date parse returns NaN for invalid input. Coerce anything invalid
  // into a conveniently comparable zero.

  const serverModified = Date.parse(serverData && serverData._modified) || 0;
  const localModified = Date.parse(localData && localData._modified) || 0;
  let preloadedData;

  if (serverData && serverModified >= localModified) {
    preloadedData = convertPreferencesPackageData(serverData);
  } else if (localData) {
    preloadedData = convertPreferencesPackageData(localData);
  } else {
    // Check if there is data in the legacy format from the old persistence system.
    preloadedData = convertLegacyLocalStorageData(userId);
  }

  return create({
    preloadedData,
    localStorageRestoreKey
  });
}

(window.wp = window.wp || {}).preferencesPersistence = __webpack_exports__;
/******/ })()
;