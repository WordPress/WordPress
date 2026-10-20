(function() {
"use strict";
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

  // packages/preferences-persistence/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    __unstableCreatePersistenceLayer: () => __unstableCreatePersistenceLayer,
    create: () => create
  });

  // packages/preferences-persistence/build-module/create/index.mjs
  var import_api_fetch = __toESM(require_api_fetch(), 1);

  // packages/preferences-persistence/build-module/create/debounce-async.mjs
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

  // packages/preferences-persistence/build-module/is-preferences-data.mjs
  function isPreferencesData(data) {
    return !!data && typeof data === "object" && !Array.isArray(data);
  }

  // packages/preferences-persistence/build-module/local-storage.mjs
  function readStoredJSON(key) {
    try {
      const value = window.localStorage.getItem(key);
      return value === null ? null : JSON.parse(value);
    } catch {
      return null;
    }
  }
  function writeStoredJSON(key, value) {
    try {
      window.localStorage.setItem(key, JSON.stringify(value));
    } catch {
    }
  }

  // packages/preferences-persistence/build-module/migrations/preferences-package-data/convert-complementary-areas.mjs
  function convertComplementaryAreas(state) {
    return Object.keys(state).reduce(
      (stateAccumulator, scope) => {
        const scopeData = state[scope];
        if (scopeData?.complementaryArea) {
          const updatedScopeData = { ...scopeData };
          delete updatedScopeData.complementaryArea;
          updatedScopeData.isComplementaryAreaVisible = true;
          stateAccumulator[scope] = updatedScopeData;
          return stateAccumulator;
        }
        return stateAccumulator;
      },
      { ...state }
    );
  }

  // packages/preferences-persistence/build-module/migrations/preferences-package-data/convert-editor-settings.mjs
  var copyScope = (scope) => isPreferencesData(scope) ? { ...scope } : void 0;
  function convertEditorSettings(data) {
    const newData = { ...data };
    const editPost = copyScope(data["core/edit-post"]);
    const editSite = copyScope(data["core/edit-site"]);
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
      if (editPost?.[setting] !== void 0) {
        newData.core = {
          ...copyScope(newData.core),
          [setting]: editPost[setting]
        };
        delete editPost[setting];
      }
      if (editSite?.[setting] !== void 0) {
        delete editSite[setting];
      }
    });
    if (editPost && Object.keys(editPost).length > 0) {
      newData["core/edit-post"] = editPost;
    } else {
      delete newData["core/edit-post"];
    }
    if (editSite && Object.keys(editSite).length > 0) {
      newData["core/edit-site"] = editSite;
    } else {
      delete newData["core/edit-site"];
    }
    return newData;
  }

  // packages/preferences-persistence/build-module/migrations/preferences-package-data/index.mjs
  function convertPreferencesPackageData(data) {
    let newData = convertComplementaryAreas(data);
    newData = convertEditorSettings(newData);
    return newData;
  }

  // packages/preferences-persistence/build-module/get-newest-data.mjs
  function pickNewestData(serverData, localStorageRestoreKey) {
    const storedData = readStoredJSON(localStorageRestoreKey);
    const localData = isPreferencesData(storedData) ? storedData : null;
    if (!isPreferencesData(serverData)) {
      return localData;
    }
    const serverModified = Date.parse(serverData._modified ?? "") || 0;
    const localModified = Date.parse(localData?._modified ?? "") || 0;
    return localData && localModified > serverModified ? localData : serverData;
  }
  function getNewestData(serverData, localStorageRestoreKey) {
    const newestData = pickNewestData(serverData, localStorageRestoreKey);
    return newestData ? convertPreferencesPackageData(newestData) : void 0;
  }

  // packages/preferences-persistence/build-module/create/index.mjs
  function create({
    preloadedData,
    localStorageRestoreKey = "WP_PREFERENCES_RESTORE_DATA",
    requestDebounceMS = 2500
  } = {}) {
    let cache = isPreferencesData(preloadedData) ? preloadedData : void 0;
    const debouncedApiFetch = debounceAsync(import_api_fetch.default, requestDebounceMS);
    async function get() {
      if (cache) {
        return cache;
      }
      const user = await (0, import_api_fetch.default)({
        path: "/wp/v2/users/me?context=edit"
      });
      cache = getNewestData(
        user?.meta?.persisted_preferences,
        localStorageRestoreKey
      ) ?? {};
      return cache;
    }
    function set(newData) {
      const dataWithTimestamp = {
        ...newData,
        _modified: (/* @__PURE__ */ new Date()).toISOString()
      };
      cache = dataWithTimestamp;
      writeStoredJSON(localStorageRestoreKey, dataWithTimestamp);
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-feature-preferences.mjs
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-third-party-feature-preferences.mjs
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-individual-preference.mjs
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/move-interface-enable-items.mjs
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/convert-edit-post-panels.mjs
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

  // packages/preferences-persistence/build-module/migrations/legacy-local-storage-data/index.mjs
  function getLegacyData(userId) {
    return readStoredJSON(`WP_DATA_USER_${userId}`);
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
    const preferences = data?.["core/preferences"]?.preferences;
    return isPreferencesData(preferences) ? preferences : void 0;
  }
  function convertLegacyLocalStorageData(userId) {
    const data = getLegacyData(userId);
    return convertLegacyData(data);
  }

  // packages/preferences-persistence/build-module/index.mjs
  function __unstableCreatePersistenceLayer(serverData, userId) {
    const localStorageRestoreKey = `WP_PREFERENCES_USER_${userId}`;
    return create({
      /*
       * Fall back to data in the legacy format from the old persistence system.
       * The server data is already preloaded, so fetching it again is redundant.
       */
      preloadedData: getNewestData(serverData, localStorageRestoreKey) ?? convertLegacyLocalStorageData(userId) ?? {},
      localStorageRestoreKey
    });
  }
  return __toCommonJS(index_exports);
})();
(window.wp ||= {}).preferencesPersistence = wp.preferencesPersistence;
})();
