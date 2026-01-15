var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJS = (cb, mod) => function __require() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
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

// package-external:@wordpress/api-fetch
var require_api_fetch = __commonJS({
  "package-external:@wordpress/api-fetch"(exports, module) {
    module.exports = window.wp.apiFetch;
  }
});

// package-external:@wordpress/url
var require_url = __commonJS({
  "package-external:@wordpress/url"(exports, module) {
    module.exports = window.wp.url;
  }
});

// packages/core-abilities/build-module/index.js
var import_api_fetch = __toESM(require_api_fetch());
var import_url = __toESM(require_url());
import { registerAbility, registerAbilityCategory } from "@wordpress/abilities";
var API_BASE = "/wp-abilities/v1";
var ABILITIES_ENDPOINT = `${API_BASE}/abilities`;
var CATEGORIES_ENDPOINT = `${API_BASE}/categories`;
function createServerCallback(ability) {
  return async (input) => {
    let method = "POST";
    if (!!ability.meta?.annotations?.readonly) {
      method = "GET";
    } else if (!!ability.meta?.annotations?.destructive && !!ability.meta?.annotations?.idempotent) {
      method = "DELETE";
    }
    let path = `${ABILITIES_ENDPOINT}/${ability.name}/run`;
    const options = {
      method
    };
    if (["GET", "DELETE"].includes(method) && input !== null && input !== void 0) {
      path = (0, import_url.addQueryArgs)(path, { input });
    } else if (method === "POST" && input !== null && input !== void 0) {
      options.data = { input };
    }
    return (0, import_api_fetch.default)({
      path,
      ...options
    });
  };
}
async function initializeCategories() {
  try {
    const categories = await (0, import_api_fetch.default)({
      path: (0, import_url.addQueryArgs)(CATEGORIES_ENDPOINT, {
        per_page: -1,
        context: "edit"
      })
    });
    if (categories && Array.isArray(categories)) {
      for (const category of categories) {
        registerAbilityCategory(category.slug, {
          label: category.label,
          description: category.description,
          meta: {
            annotations: { serverRegistered: true }
          }
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch ability categories:", error);
  }
}
async function initializeAbilities() {
  try {
    const abilities = await (0, import_api_fetch.default)({
      path: (0, import_url.addQueryArgs)(ABILITIES_ENDPOINT, {
        per_page: -1,
        context: "edit"
      })
    });
    if (abilities && Array.isArray(abilities)) {
      for (const ability of abilities) {
        registerAbility({
          ...ability,
          callback: createServerCallback(ability),
          meta: {
            annotations: {
              ...ability.meta?.annotations,
              serverRegistered: true
            }
          }
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch abilities:", error);
  }
}
async function initialize() {
  await initializeCategories();
  await initializeAbilities();
}
initialize();