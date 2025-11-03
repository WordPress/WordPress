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
  __unstableCreatePersistenceLayer: () => (/* binding */ __unstableCreatePersistenceLayer),
  create: () => (/* reexport */ create)
});

;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// ./node_modules/@wordpress/preferences-persistence/build-module/create/debounce-async.js
function debounceAsync(func, delayMS) {
  let timeoutId;
  let activePromise;
  return async function debounced(...args) {
    if (!activePromise && !timeoutId) {
      return new Promise((resolve, reject) => {
        activePromise = func(...args).then((...thenArgs) => {
          resolve(...thenArgs);
        }).catch((error) => {
          reject(error);
        }).finally(() => {
          activePromise = null;
        });
      });
    }
    if (activePromise) {
      await activePromise;
    }
    if (timeoutId) {
      clearTimeout(timeoutId);
      timeoutId = null;
    }
    return new Promise((resolve, reject) => {
      timeoutId = setTimeout(() => {
        activePromise = func(...args).then((...thenArgs) => {
          resolve(...thenArgs);
        }).catch((error) => {
          reject(error);
        }).finally(() => {
          activePromise = null;
          timeoutId = null;
        });
      }, delayMS);
    });
  };
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/create/index.js


const EMPTY_OBJECT = {};
const localStorage = window.localStorage;
function create({
  preloadedData,
  localStorageRestoreKey = "WP_PREFERENCES_RESTORE_DATA",
  requestDebounceMS = 2500
} = {}) {
  let cache = preloadedData;
  const debouncedApiFetch = debounceAsync((external_wp_apiFetch_default()), requestDebounceMS);
  async function get() {
    if (cache) {
      return cache;
    }
    const user = await external_wp_apiFetch_default()({
      path: "/wp/v2/users/me?context=edit"
    });
    const serverData = user?.meta?.persisted_preferences;
    const localData = JSON.parse(
      localStorage.getItem(localStorageRestoreKey)
    );
    const serverTimestamp = Date.parse(serverData?._modified) || 0;
    const localTimestamp = Date.parse(localData?._modified) || 0;
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
    const dataWithTimestamp = {
      ...newData,
      _modified: (/* @__PURE__ */ new Date()).toISOString()
    };
    cache = dataWithTimestamp;
    localStorage.setItem(
      localStorageRestoreKey,
      JSON.stringify(dataWithTimestamp)
    );
    debouncedApiFetch({
      path: "/wp/v2/users/me",
      method: "PUT",
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
    }).catch(() => {
    });
  }
  return {
    get,
    set
  };
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-feature-preferences.js
function moveFeaturePreferences(state, sourceStoreName) {
  const preferencesStoreName = "core/preferences";
  const interfaceStoreName = "core/interface";
  const interfaceFeatures = state?.[interfaceStoreName]?.preferences?.features?.[sourceStoreName];
  const sourceFeatures = state?.[sourceStoreName]?.preferences?.features;
  const featuresToMigrate = interfaceFeatures ? interfaceFeatures : sourceFeatures;
  if (!featuresToMigrate) {
    return state;
  }
  const existingPreferences = state?.[preferencesStoreName]?.preferences;
  if (existingPreferences?.[sourceStoreName]) {
    return state;
  }
  let updatedInterfaceState;
  if (interfaceFeatures) {
    const otherInterfaceState = state?.[interfaceStoreName];
    const otherInterfaceScopes = state?.[interfaceStoreName]?.preferences?.features;
    updatedInterfaceState = {
      [interfaceStoreName]: {
        ...otherInterfaceState,
        preferences: {
          features: {
            ...otherInterfaceScopes,
            [sourceStoreName]: void 0
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
      [sourceStoreName]: {
        ...otherSourceState,
        preferences: {
          ...sourcePreferences,
          features: void 0
        }
      }
    };
  }
  return {
    ...state,
    [preferencesStoreName]: {
      preferences: {
        ...existingPreferences,
        [sourceStoreName]: featuresToMigrate
      }
    },
    ...updatedInterfaceState,
    ...updatedSourceState
  };
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-third-party-feature-preferences.js
function moveThirdPartyFeaturePreferencesToPreferences(state) {
  const interfaceStoreName = "core/interface";
  const preferencesStoreName = "core/preferences";
  const interfaceScopes = state?.[interfaceStoreName]?.preferences?.features;
  const interfaceScopeKeys = interfaceScopes ? Object.keys(interfaceScopes) : [];
  if (!interfaceScopeKeys?.length) {
    return state;
  }
  return interfaceScopeKeys.reduce(function(convertedState, scope) {
    if (scope.startsWith("core")) {
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
    return {
      ...convertedState,
      [preferencesStoreName]: {
        preferences: {
          ...otherPreferencesScopes,
          [scope]: featuresToMigrate
        }
      },
      [interfaceStoreName]: {
        ...otherInterfaceState,
        preferences: {
          features: {
            ...otherInterfaceScopes,
            [scope]: void 0
          }
        }
      }
    };
  }, state);
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-individual-preference.js
const identity = (arg) => arg;
function moveIndividualPreferenceToPreferences(state, { from: sourceStoreName, to: scope }, key, convert = identity) {
  const preferencesStoreName = "core/preferences";
  const sourcePreference = state?.[sourceStoreName]?.preferences?.[key];
  if (sourcePreference === void 0) {
    return state;
  }
  const targetPreference = state?.[preferencesStoreName]?.preferences?.[scope]?.[key];
  if (targetPreference) {
    return state;
  }
  const otherScopes = state?.[preferencesStoreName]?.preferences;
  const otherPreferences = state?.[preferencesStoreName]?.preferences?.[scope];
  const otherSourceState = state?.[sourceStoreName];
  const allSourcePreferences = state?.[sourceStoreName]?.preferences;
  const convertedPreferences = convert({ [key]: sourcePreference });
  return {
    ...state,
    [preferencesStoreName]: {
      preferences: {
        ...otherScopes,
        [scope]: {
          ...otherPreferences,
          ...convertedPreferences
        }
      }
    },
    [sourceStoreName]: {
      ...otherSourceState,
      preferences: {
        ...allSourcePreferences,
        [key]: void 0
      }
    }
  };
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-interface-enable-items.js
function moveInterfaceEnableItems(state) {
  const interfaceStoreName = "core/interface";
  const preferencesStoreName = "core/preferences";
  const sourceEnableItems = state?.[interfaceStoreName]?.enableItems;
  if (!sourceEnableItems) {
    return state;
  }
  const allPreferences = state?.[preferencesStoreName]?.preferences ?? {};
  const sourceComplementaryAreas = sourceEnableItems?.singleEnableItems?.complementaryArea ?? {};
  const preferencesWithConvertedComplementaryAreas = Object.keys(
    sourceComplementaryAreas
  ).reduce((accumulator, scope) => {
    const data = sourceComplementaryAreas[scope];
    if (accumulator?.[scope]?.complementaryArea) {
      return accumulator;
    }
    return {
      ...accumulator,
      [scope]: {
        ...accumulator[scope],
        complementaryArea: data
      }
    };
  }, allPreferences);
  const sourcePinnedItems = sourceEnableItems?.multipleEnableItems?.pinnedItems ?? {};
  const allConvertedData = Object.keys(sourcePinnedItems).reduce(
    (accumulator, scope) => {
      const data = sourcePinnedItems[scope];
      if (accumulator?.[scope]?.pinnedItems) {
        return accumulator;
      }
      return {
        ...accumulator,
        [scope]: {
          ...accumulator[scope],
          pinnedItems: data
        }
      };
    },
    preferencesWithConvertedComplementaryAreas
  );
  const otherInterfaceItems = state[interfaceStoreName];
  return {
    ...state,
    [preferencesStoreName]: {
      preferences: allConvertedData
    },
    [interfaceStoreName]: {
      ...otherInterfaceItems,
      enableItems: void 0
    }
  };
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/convert-edit-post-panels.js
function convertEditPostPanels(preferences) {
  const panels = preferences?.panels ?? {};
  return Object.keys(panels).reduce(
    (convertedData, panelName) => {
      const panel = panels[panelName];
      if (panel?.enabled === false) {
        convertedData.inactivePanels.push(panelName);
      }
      if (panel?.opened === true) {
        convertedData.openPanels.push(panelName);
      }
      return convertedData;
    },
    { inactivePanels: [], openPanels: [] }
  );
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/legacy-local-storage-data/index.js





function getLegacyData(userId) {
  const key = `WP_DATA_USER_${userId}`;
  const unparsedData = window.localStorage.getItem(key);
  return JSON.parse(unparsedData);
}
function convertLegacyData(data) {
  if (!data) {
    return;
  }
  data = moveFeaturePreferences(data, "core/edit-widgets");
  data = moveFeaturePreferences(data, "core/customize-widgets");
  data = moveFeaturePreferences(data, "core/edit-post");
  data = moveFeaturePreferences(data, "core/edit-site");
  data = moveThirdPartyFeaturePreferencesToPreferences(data);
  data = moveInterfaceEnableItems(data);
  data = moveIndividualPreferenceToPreferences(
    data,
    { from: "core/edit-post", to: "core/edit-post" },
    "hiddenBlockTypes"
  );
  data = moveIndividualPreferenceToPreferences(
    data,
    { from: "core/edit-post", to: "core/edit-post" },
    "editorMode"
  );
  data = moveIndividualPreferenceToPreferences(
    data,
    { from: "core/edit-post", to: "core/edit-post" },
    "panels",
    convertEditPostPanels
  );
  data = moveIndividualPreferenceToPreferences(
    data,
    { from: "core/editor", to: "core" },
    "isPublishSidebarEnabled"
  );
  data = moveIndividualPreferenceToPreferences(
    data,
    { from: "core/edit-post", to: "core" },
    "isPublishSidebarEnabled"
  );
  data = moveIndividualPreferenceToPreferences(
    data,
    { from: "core/edit-site", to: "core/edit-site" },
    "editorMode"
  );
  return data?.["core/preferences"]?.preferences;
}
function convertLegacyLocalStorageData(userId) {
  const data = getLegacyData(userId);
  return convertLegacyData(data);
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/preferences-package-data/convert-complementary-areas.js
function convertComplementaryAreas(state) {
  return Object.keys(state).reduce((stateAccumulator, scope) => {
    const scopeData = state[scope];
    if (scopeData?.complementaryArea) {
      const updatedScopeData = { ...scopeData };
      delete updatedScopeData.complementaryArea;
      updatedScopeData.isComplementaryAreaVisible = true;
      stateAccumulator[scope] = updatedScopeData;
      return stateAccumulator;
    }
    return stateAccumulator;
  }, state);
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/preferences-package-data/convert-editor-settings.js
function convertEditorSettings(data) {
  let newData = data;
  const settingsToMoveToCore = [
    "allowRightClickOverrides",
    "distractionFree",
    "editorMode",
    "fixedToolbar",
    "focusMode",
    "hiddenBlockTypes",
    "inactivePanels",
    "keepCaretInsideBlock",
    "mostUsedBlocks",
    "openPanels",
    "showBlockBreadcrumbs",
    "showIconLabels",
    "showListViewByDefault",
    "isPublishSidebarEnabled",
    "isComplementaryAreaVisible",
    "pinnedItems"
  ];
  settingsToMoveToCore.forEach((setting) => {
    if (data?.["core/edit-post"]?.[setting] !== void 0) {
      newData = {
        ...newData,
        core: {
          ...newData?.core,
          [setting]: data["core/edit-post"][setting]
        }
      };
      delete newData["core/edit-post"][setting];
    }
    if (data?.["core/edit-site"]?.[setting] !== void 0) {
      delete newData["core/edit-site"][setting];
    }
  });
  if (Object.keys(newData?.["core/edit-post"] ?? {})?.length === 0) {
    delete newData["core/edit-post"];
  }
  if (Object.keys(newData?.["core/edit-site"] ?? {})?.length === 0) {
    delete newData["core/edit-site"];
  }
  return newData;
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/migrations/preferences-package-data/index.js


function convertPreferencesPackageData(data) {
  let newData = convertComplementaryAreas(data);
  newData = convertEditorSettings(newData);
  return newData;
}


;// ./node_modules/@wordpress/preferences-persistence/build-module/index.js



function __unstableCreatePersistenceLayer(serverData, userId) {
  const localStorageRestoreKey = `WP_PREFERENCES_USER_${userId}`;
  const localData = JSON.parse(
    window.localStorage.getItem(localStorageRestoreKey)
  );
  const serverModified = Date.parse(serverData && serverData._modified) || 0;
  const localModified = Date.parse(localData && localData._modified) || 0;
  let preloadedData;
  if (serverData && serverModified >= localModified) {
    preloadedData = convertPreferencesPackageData(serverData);
  } else if (localData) {
    preloadedData = convertPreferencesPackageData(localData);
  } else {
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