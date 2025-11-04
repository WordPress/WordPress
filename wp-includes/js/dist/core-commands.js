/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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
  initializeCommandPalette: () => (/* binding */ initializeCommandPalette),
  privateApis: () => (/* reexport */ privateApis)
});

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","router"]
const external_wp_router_namespaceObject = window["wp"]["router"];
;// external ["wp","commands"]
const external_wp_commands_namespaceObject = window["wp"]["commands"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/@wordpress/icons/build-module/library/external.js


var external_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z" }) });


;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/core-commands/build-module/admin-navigation-commands.js






const getViewSiteCommand = () => function useViewSiteCommand() {
  const homeUrl = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    return select(external_wp_coreData_namespaceObject.store).getEntityRecord(
      "root",
      "__unstableBase"
    )?.home;
  }, []);
  const commands = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!homeUrl) {
      return [];
    }
    return [
      {
        name: "core/view-site",
        label: (0,external_wp_i18n_namespaceObject.__)("View site"),
        icon: external_default,
        callback: ({ close }) => {
          close();
          window.open(homeUrl, "_blank");
        }
      }
    ];
  }, [homeUrl]);
  return {
    isLoading: false,
    commands
  };
};
function useAdminNavigationCommands(menuCommands) {
  const commands = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return (menuCommands ?? []).map((menuCommand) => {
      const label = (0,external_wp_i18n_namespaceObject.sprintf)(
        /* translators: %s: menu label */
        (0,external_wp_i18n_namespaceObject.__)("Go to: %s"),
        menuCommand.label
      );
      return {
        name: menuCommand.name,
        label,
        searchLabel: label,
        callback: ({ close }) => {
          document.location = menuCommand.url;
          close();
        }
      };
    });
  }, [menuCommands]);
  (0,external_wp_commands_namespaceObject.useCommands)(commands);
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: "core/view-site",
    hook: getViewSiteCommand()
  });
}


;// ./node_modules/@wordpress/icons/build-module/library/post.js


var post_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "m7.3 9.7 1.4 1.4c.2-.2.3-.3.4-.5 0 0 0-.1.1-.1.3-.5.4-1.1.3-1.6L12 7 9 4 7.2 6.5c-.6-.1-1.1 0-1.6.3 0 0-.1 0-.1.1-.3.1-.4.2-.6.4l1.4 1.4L4 11v1h1l2.3-2.3zM4 20h9v-1.5H4V20zm0-5.5V16h16v-1.5H4z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/page.js


var page_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: [
  /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M15.5 7.5h-7V9h7V7.5Zm-7 3.5h7v1.5h-7V11Zm7 3.5h-7V16h7v-1.5Z" }),
  /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M17 4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2ZM7 5.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H7a.5.5 0 0 1-.5-.5V6a.5.5 0 0 1 .5-.5Z" })
] });


;// ./node_modules/@wordpress/icons/build-module/library/layout.js


var layout_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/symbol-filled.js


var symbol_filled_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-17.6 1L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/styles.js


var styles_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
  external_wp_primitives_namespaceObject.Path,
  {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-1.5 0a6.5 6.5 0 0 1-6.5 6.5v-13a6.5 6.5 0 0 1 6.5 6.5Z"
  }
) });


;// ./node_modules/@wordpress/icons/build-module/library/navigation.js


var navigation_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M12 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14.5c-3.6 0-6.5-2.9-6.5-6.5S8.4 5.5 12 5.5s6.5 2.9 6.5 6.5-2.9 6.5-6.5 6.5zM9 16l4.5-3L15 8.4l-4.5 3L9 16z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/symbol.js


var symbol_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });


;// ./node_modules/@wordpress/icons/build-module/library/brush.js


var brush_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M4 20h8v-1.5H4V20zM18.9 3.5c-.6-.6-1.5-.6-2.1 0l-7.2 7.2c-.4-.1-.7 0-1.1.1-.5.2-1.5.7-1.9 2.2-.4 1.7-.8 2.2-1.1 2.7-.1.1-.2.3-.3.4l-.6 1.1H6c2 0 3.4-.4 4.7-1.4.8-.6 1.2-1.4 1.3-2.3 0-.3 0-.5-.1-.7L19 5.7c.5-.6.5-1.6-.1-2.2zM9.7 14.7c-.7.5-1.5.8-2.4 1 .2-.5.5-1.2.8-2.3.2-.6.4-1 .8-1.1.5-.1 1 .1 1.3.3.2.2.3.5.2.8 0 .3-.1.9-.7 1.3z" }) });


;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/core-commands/build-module/lock-unlock.js

const { lock, unlock } = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/core-commands"
);


;// ./node_modules/@wordpress/core-commands/build-module/utils/order-entity-records-by-search.js
function orderEntityRecordsBySearch(records = [], search = "") {
  if (!Array.isArray(records) || !records.length) {
    return [];
  }
  if (!search) {
    return records;
  }
  const priority = [];
  const nonPriority = [];
  for (let i = 0; i < records.length; i++) {
    const record = records[i];
    if (record?.title?.raw?.toLowerCase()?.includes(search?.toLowerCase())) {
      priority.push(record);
    } else {
      nonPriority.push(record);
    }
  }
  return priority.concat(nonPriority);
}


;// ./node_modules/@wordpress/core-commands/build-module/site-editor-navigation-commands.js












const { useHistory } = unlock(external_wp_router_namespaceObject.privateApis);
const icons = {
  post: post_default,
  page: page_default,
  wp_template: layout_default,
  wp_template_part: symbol_filled_default
};
function useDebouncedValue(value) {
  const [debouncedValue, setDebouncedValue] = (0,external_wp_element_namespaceObject.useState)("");
  const debounced = (0,external_wp_compose_namespaceObject.useDebounce)(setDebouncedValue, 250);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    debounced(value);
    return () => debounced.cancel();
  }, [debounced, value]);
  return debouncedValue;
}
const getNavigationCommandLoaderPerPostType = (postType) => function useNavigationCommandLoader({ search }) {
  const history = useHistory();
  const { isBlockBasedTheme, canCreateTemplate } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      return {
        isBlockBasedTheme: select(external_wp_coreData_namespaceObject.store).getCurrentTheme()?.is_block_theme,
        canCreateTemplate: select(external_wp_coreData_namespaceObject.store).canUser("create", {
          kind: "postType",
          name: "wp_template"
        })
      };
    },
    []
  );
  const delayedSearch = useDebouncedValue(search);
  const { records, isLoading } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      if (!delayedSearch) {
        return {
          isLoading: false
        };
      }
      const query = {
        search: delayedSearch,
        per_page: 10,
        orderby: "relevance",
        status: [
          "publish",
          "future",
          "draft",
          "pending",
          "private"
        ]
      };
      return {
        records: select(external_wp_coreData_namespaceObject.store).getEntityRecords(
          "postType",
          postType,
          query
        ),
        isLoading: !select(external_wp_coreData_namespaceObject.store).hasFinishedResolution(
          "getEntityRecords",
          ["postType", postType, query]
        )
      };
    },
    [delayedSearch]
  );
  const commands = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return (records ?? []).map((record) => {
      const command = {
        name: postType + "-" + record.id,
        searchLabel: record.title?.rendered + " " + record.id,
        label: record.title?.rendered ? (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(record.title?.rendered) : (0,external_wp_i18n_namespaceObject.__)("(no title)"),
        icon: icons[postType]
      };
      if (!canCreateTemplate || postType === "post" || postType === "page" && !isBlockBasedTheme) {
        return {
          ...command,
          callback: ({ close }) => {
            const args = {
              post: record.id,
              action: "edit"
            };
            const targetUrl = (0,external_wp_url_namespaceObject.addQueryArgs)("post.php", args);
            document.location = targetUrl;
            close();
          }
        };
      }
      const isSiteEditor = (0,external_wp_url_namespaceObject.getPath)(window.location.href)?.includes(
        "site-editor.php"
      );
      return {
        ...command,
        callback: ({ close }) => {
          if (isSiteEditor) {
            history.navigate(
              `/${postType}/${record.id}?canvas=edit`
            );
          } else {
            document.location = (0,external_wp_url_namespaceObject.addQueryArgs)(
              "site-editor.php",
              {
                p: `/${postType}/${record.id}`,
                canvas: "edit"
              }
            );
          }
          close();
        }
      };
    });
  }, [canCreateTemplate, records, isBlockBasedTheme, history]);
  return {
    commands,
    isLoading
  };
};
const getNavigationCommandLoaderPerTemplate = (templateType) => function useNavigationCommandLoader({ search }) {
  const history = useHistory();
  const { isBlockBasedTheme, canCreateTemplate } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      return {
        isBlockBasedTheme: select(external_wp_coreData_namespaceObject.store).getCurrentTheme()?.is_block_theme,
        canCreateTemplate: select(external_wp_coreData_namespaceObject.store).canUser("create", {
          kind: "postType",
          name: templateType
        })
      };
    },
    []
  );
  const { records, isLoading } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getEntityRecords } = select(external_wp_coreData_namespaceObject.store);
    const query = { per_page: -1 };
    return {
      records: getEntityRecords("postType", templateType, query),
      isLoading: !select(external_wp_coreData_namespaceObject.store).hasFinishedResolution(
        "getEntityRecords",
        ["postType", templateType, query]
      )
    };
  }, []);
  const orderedRecords = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return orderEntityRecordsBySearch(records, search).slice(0, 10);
  }, [records, search]);
  const commands = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!canCreateTemplate || !isBlockBasedTheme && !templateType === "wp_template_part") {
      return [];
    }
    const isSiteEditor = (0,external_wp_url_namespaceObject.getPath)(window.location.href)?.includes(
      "site-editor.php"
    );
    const result = [];
    result.push(
      ...orderedRecords.map((record) => {
        return {
          name: templateType + "-" + record.id,
          searchLabel: record.title?.rendered + " " + record.id,
          label: record.title?.rendered ? record.title?.rendered : (0,external_wp_i18n_namespaceObject.__)("(no title)"),
          icon: icons[templateType],
          callback: ({ close }) => {
            if (isSiteEditor) {
              history.navigate(
                `/${templateType}/${record.id}?canvas=edit`
              );
            } else {
              document.location = (0,external_wp_url_namespaceObject.addQueryArgs)(
                "site-editor.php",
                {
                  p: `/${templateType}/${record.id}`,
                  canvas: "edit"
                }
              );
            }
            close();
          }
        };
      })
    );
    if (orderedRecords?.length > 0 && templateType === "wp_template_part") {
      result.push({
        name: "core/edit-site/open-template-parts",
        label: (0,external_wp_i18n_namespaceObject.__)("Go to: Template parts"),
        icon: symbol_filled_default,
        callback: ({ close }) => {
          if (isSiteEditor) {
            history.navigate(
              "/pattern?postType=wp_template_part&categoryId=all-parts"
            );
          } else {
            document.location = (0,external_wp_url_namespaceObject.addQueryArgs)(
              "site-editor.php",
              {
                p: "/pattern",
                postType: "wp_template_part",
                categoryId: "all-parts"
              }
            );
          }
          close();
        }
      });
    }
    return result;
  }, [canCreateTemplate, isBlockBasedTheme, orderedRecords, history]);
  return {
    commands,
    isLoading
  };
};
const getSiteEditorBasicNavigationCommands = () => function useSiteEditorBasicNavigationCommands() {
  const history = useHistory();
  const isSiteEditor = (0,external_wp_url_namespaceObject.getPath)(window.location.href)?.includes(
    "site-editor.php"
  );
  const { isBlockBasedTheme, canCreateTemplate, canCreatePatterns } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    return {
      isBlockBasedTheme: select(external_wp_coreData_namespaceObject.store).getCurrentTheme()?.is_block_theme,
      canCreateTemplate: select(external_wp_coreData_namespaceObject.store).canUser("create", {
        kind: "postType",
        name: "wp_template"
      }),
      canCreatePatterns: select(external_wp_coreData_namespaceObject.store).canUser("create", {
        kind: "postType",
        name: "wp_block"
      })
    };
  }, []);
  const commands = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const result = [];
    if (canCreateTemplate && isBlockBasedTheme) {
      result.push({
        name: "core/edit-site/open-styles",
        label: (0,external_wp_i18n_namespaceObject.__)("Go to: Styles"),
        icon: styles_default,
        callback: ({ close }) => {
          if (isSiteEditor) {
            history.navigate("/styles");
          } else {
            document.location = (0,external_wp_url_namespaceObject.addQueryArgs)(
              "site-editor.php",
              {
                p: "/styles"
              }
            );
          }
          close();
        }
      });
      result.push({
        name: "core/edit-site/open-navigation",
        label: (0,external_wp_i18n_namespaceObject.__)("Go to: Navigation"),
        icon: navigation_default,
        callback: ({ close }) => {
          if (isSiteEditor) {
            history.navigate("/navigation");
          } else {
            document.location = (0,external_wp_url_namespaceObject.addQueryArgs)(
              "site-editor.php",
              {
                p: "/navigation"
              }
            );
          }
          close();
        }
      });
      result.push({
        name: "core/edit-site/open-templates",
        label: (0,external_wp_i18n_namespaceObject.__)("Go to: Templates"),
        icon: layout_default,
        callback: ({ close }) => {
          if (isSiteEditor) {
            history.navigate("/template");
          } else {
            document.location = (0,external_wp_url_namespaceObject.addQueryArgs)(
              "site-editor.php",
              {
                p: "/template"
              }
            );
          }
          close();
        }
      });
    }
    if (canCreatePatterns) {
      result.push({
        name: "core/edit-site/open-patterns",
        label: (0,external_wp_i18n_namespaceObject.__)("Go to: Patterns"),
        icon: symbol_default,
        callback: ({ close }) => {
          if (canCreateTemplate) {
            if (isSiteEditor) {
              history.navigate("/pattern");
            } else {
              document.location = (0,external_wp_url_namespaceObject.addQueryArgs)(
                "site-editor.php",
                {
                  p: "/pattern"
                }
              );
            }
            close();
          } else {
            document.location.href = "edit.php?post_type=wp_block";
          }
        }
      });
    }
    return result;
  }, [
    history,
    isSiteEditor,
    canCreateTemplate,
    canCreatePatterns,
    isBlockBasedTheme
  ]);
  return {
    commands,
    isLoading: false
  };
};
const getGlobalStylesOpenCssCommands = () => function useGlobalStylesOpenCssCommands() {
  const history = useHistory();
  const isSiteEditor = (0,external_wp_url_namespaceObject.getPath)(window.location.href)?.includes(
    "site-editor.php"
  );
  const { canEditCSS } = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getEntityRecord, __experimentalGetCurrentGlobalStylesId } = select(external_wp_coreData_namespaceObject.store);
    const globalStylesId = __experimentalGetCurrentGlobalStylesId();
    const globalStyles = globalStylesId ? getEntityRecord("root", "globalStyles", globalStylesId) : void 0;
    return {
      canEditCSS: !!globalStyles?._links?.["wp:action-edit-css"]
    };
  }, []);
  const commands = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!canEditCSS) {
      return [];
    }
    return [
      {
        name: "core/open-styles-css",
        label: (0,external_wp_i18n_namespaceObject.__)("Open custom CSS"),
        icon: brush_default,
        callback: ({ close }) => {
          close();
          if (isSiteEditor) {
            history.navigate("/styles?section=/css");
          } else {
            document.location = (0,external_wp_url_namespaceObject.addQueryArgs)(
              "site-editor.php",
              {
                p: "/styles",
                section: "/css"
              }
            );
          }
        }
      }
    ];
  }, [history, canEditCSS, isSiteEditor]);
  return {
    isLoading: false,
    commands
  };
};
function useSiteEditorNavigationCommands(isNetworkAdmin) {
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: "core/edit-site/navigate-pages",
    hook: getNavigationCommandLoaderPerPostType("page"),
    disabled: isNetworkAdmin
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: "core/edit-site/navigate-posts",
    hook: getNavigationCommandLoaderPerPostType("post"),
    disabled: isNetworkAdmin
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: "core/edit-site/navigate-templates",
    hook: getNavigationCommandLoaderPerTemplate("wp_template"),
    disabled: isNetworkAdmin
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: "core/edit-site/navigate-template-parts",
    hook: getNavigationCommandLoaderPerTemplate("wp_template_part"),
    disabled: isNetworkAdmin
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: "core/edit-site/basic-navigation",
    hook: getSiteEditorBasicNavigationCommands(),
    context: "site-editor",
    disabled: isNetworkAdmin
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: "core/edit-site/global-styles-css",
    hook: getGlobalStylesOpenCssCommands(),
    disabled: isNetworkAdmin
  });
}


;// ./node_modules/@wordpress/core-commands/build-module/private-apis.js



function useCommands() {
  useAdminNavigationCommands();
  useSiteEditorNavigationCommands();
}
const privateApis = {};
lock(privateApis, {
  useCommands
});


;// ./node_modules/@wordpress/core-commands/build-module/index.js








const { RouterProvider } = unlock(external_wp_router_namespaceObject.privateApis);
function CommandPalette({ settings }) {
  const { menu_commands: menuCommands, is_network_admin: isNetworkAdmin } = settings;
  useAdminNavigationCommands(menuCommands);
  useSiteEditorNavigationCommands(isNetworkAdmin);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(RouterProvider, { pathArg: "p", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_commands_namespaceObject.CommandMenu, {}) });
}
function initializeCommandPalette(settings) {
  const root = document.createElement("div");
  document.body.appendChild(root);
  (0,external_wp_element_namespaceObject.createRoot)(root).render(
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.StrictMode, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(CommandPalette, { settings }) })
  );
}


(window.wp = window.wp || {}).coreCommands = __webpack_exports__;
/******/ })()
;