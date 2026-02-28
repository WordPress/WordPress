var wp;
(wp ||= {}).preferencesPersistence = (() => {
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
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
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

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // packages/preferences-persistence/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    __unstableCreatePersistenceLayer: () => __unstableCreatePersistenceLayer,
    create: () => create
  });

  // packages/preferences-persistence/build-module/create/index.js
  var import_api_fetch = __toESM(require_api_fetch());

  // packages/preferences-persistence/build-module/create/debounce-async.js
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

  // packages/preferences-persistence/build-module/create/index.js
  var EMPTY_OBJECT = {};
  var localStorage = window.localStorage;
  function create({
    preloadedData,
    localStorageRestoreKey = "WP_PREFERENCES_RESTORE_DATA",
    requestDebounceMS = 2500
  } = {}) {
    let cache = preloadedData;
    const debouncedApiFetch = debounceAsync(import_api_fetch.default, requestDebounceMS);
    async function get() {
      if (cache) {
        return cache;
      }
      const user = await (0, import_api_fetch.default)({
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-feature-preferences.js
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-third-party-feature-preferences.js
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-individual-preference.js
  var identity = (arg) => arg;
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-interface-enable-items.js
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/convert-edit-post-panels.js
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/index.js
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

  // packages/preferences-persistence/build-module/migrations/preferences-package-data/convert-complementary-areas.js
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

  // packages/preferences-persistence/build-module/migrations/preferences-package-data/convert-editor-settings.js
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

  // packages/preferences-persistence/build-module/migrations/preferences-package-data/index.js
  function convertPreferencesPackageData(data) {
    let newData = convertComplementaryAreas(data);
    newData = convertEditorSettings(newData);
    return newData;
  }

  // packages/preferences-persistence/build-module/index.js
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
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
