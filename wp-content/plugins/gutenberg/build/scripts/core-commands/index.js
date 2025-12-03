var wp;
(wp ||= {}).coreCommands = (() => {
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

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/router
  var require_router = __commonJS({
    "package-external:@wordpress/router"(exports, module) {
      module.exports = window.wp.router;
    }
  });

  // package-external:@wordpress/commands
  var require_commands = __commonJS({
    "package-external:@wordpress/commands"(exports, module) {
      module.exports = window.wp.commands;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/core-data
  var require_core_data = __commonJS({
    "package-external:@wordpress/core-data"(exports, module) {
      module.exports = window.wp.coreData;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/html-entities
  var require_html_entities = __commonJS({
    "package-external:@wordpress/html-entities"(exports, module) {
      module.exports = window.wp.htmlEntities;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // packages/core-commands/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    initializeCommandPalette: () => initializeCommandPalette,
    privateApis: () => privateApis
  });
  var import_element3 = __toESM(require_element());
  var import_router2 = __toESM(require_router());
  var import_commands3 = __toESM(require_commands());

  // packages/core-commands/build-module/admin-navigation-commands.js
  var import_commands = __toESM(require_commands());
  var import_i18n = __toESM(require_i18n());

  // packages/icons/build-module/library/brush.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var brush_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M4 20h8v-1.5H4V20zM18.9 3.5c-.6-.6-1.5-.6-2.1 0l-7.2 7.2c-.4-.1-.7 0-1.1.1-.5.2-1.5.7-1.9 2.2-.4 1.7-.8 2.2-1.1 2.7-.1.1-.2.3-.3.4l-.6 1.1H6c2 0 3.4-.4 4.7-1.4.8-.6 1.2-1.4 1.3-2.3 0-.3 0-.5-.1-.7L19 5.7c.5-.6.5-1.6-.1-2.2zM9.7 14.7c-.7.5-1.5.8-2.4 1 .2-.5.5-1.2.8-2.3.2-.6.4-1 .8-1.1.5-.1 1 .1 1.3.3.2.2.3.5.2.8 0 .3-.1.9-.7 1.3z" }) });

  // packages/icons/build-module/library/external.js
  var import_primitives2 = __toESM(require_primitives());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var external_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z" }) });

  // packages/icons/build-module/library/layout.js
  var import_primitives3 = __toESM(require_primitives());
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  var layout_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z" }) });

  // packages/icons/build-module/library/navigation.js
  var import_primitives4 = __toESM(require_primitives());
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
  var navigation_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M12 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14.5c-3.6 0-6.5-2.9-6.5-6.5S8.4 5.5 12 5.5s6.5 2.9 6.5 6.5-2.9 6.5-6.5 6.5zM9 16l4.5-3L15 8.4l-4.5 3L9 16z" }) });

  // packages/icons/build-module/library/page.js
  var import_primitives5 = __toESM(require_primitives());
  var import_jsx_runtime5 = __toESM(require_jsx_runtime());
  var page_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsxs)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: [
    /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.Path, { d: "M15.5 7.5h-7V9h7V7.5Zm-7 3.5h7v1.5h-7V11Zm7 3.5h-7V16h7v-1.5Z" }),
    /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.Path, { d: "M17 4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2ZM7 5.5h10a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5H7a.5.5 0 0 1-.5-.5V6a.5.5 0 0 1 .5-.5Z" })
  ] });

  // packages/icons/build-module/library/post.js
  var import_primitives6 = __toESM(require_primitives());
  var import_jsx_runtime6 = __toESM(require_jsx_runtime());
  var post_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "m7.3 9.7 1.4 1.4c.2-.2.3-.3.4-.5 0 0 0-.1.1-.1.3-.5.4-1.1.3-1.6L12 7 9 4 7.2 6.5c-.6-.1-1.1 0-1.6.3 0 0-.1 0-.1.1-.3.1-.4.2-.6.4l1.4 1.4L4 11v1h1l2.3-2.3zM4 20h9v-1.5H4V20zm0-5.5V16h16v-1.5H4z" }) });

  // packages/icons/build-module/library/styles.js
  var import_primitives7 = __toESM(require_primitives());
  var import_jsx_runtime7 = __toESM(require_jsx_runtime());
  var styles_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
    import_primitives7.Path,
    {
      fillRule: "evenodd",
      clipRule: "evenodd",
      d: "M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-1.5 0a6.5 6.5 0 0 1-6.5 6.5v-13a6.5 6.5 0 0 1 6.5 6.5Z"
    }
  ) });

  // packages/icons/build-module/library/symbol-filled.js
  var import_primitives8 = __toESM(require_primitives());
  var import_jsx_runtime8 = __toESM(require_jsx_runtime());
  var symbol_filled_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-17.6 1L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

  // packages/icons/build-module/library/symbol.js
  var import_primitives9 = __toESM(require_primitives());
  var import_jsx_runtime9 = __toESM(require_jsx_runtime());
  var symbol_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

  // packages/core-commands/build-module/admin-navigation-commands.js
  var import_element = __toESM(require_element());
  var import_core_data = __toESM(require_core_data());
  var import_data = __toESM(require_data());
  var getViewSiteCommand = () => function useViewSiteCommand() {
    const homeUrl = (0, import_data.useSelect)((select) => {
      return select(import_core_data.store).getEntityRecord(
        "root",
        "__unstableBase"
      )?.home;
    }, []);
    const commands = (0, import_element.useMemo)(() => {
      if (!homeUrl) {
        return [];
      }
      return [
        {
          name: "core/view-site",
          label: (0, import_i18n.__)("View site"),
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
    const commands = (0, import_element.useMemo)(() => {
      return (menuCommands ?? []).map((menuCommand) => {
        const label = (0, import_i18n.sprintf)(
          /* translators: %s: menu label */
          (0, import_i18n.__)("Go to: %s"),
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
    (0, import_commands.useCommands)(commands);
    (0, import_commands.useCommandLoader)({
      name: "core/view-site",
      hook: getViewSiteCommand()
    });
  }

  // packages/core-commands/build-module/site-editor-navigation-commands.js
  var import_commands2 = __toESM(require_commands());
  var import_i18n2 = __toESM(require_i18n());
  var import_element2 = __toESM(require_element());
  var import_data2 = __toESM(require_data());
  var import_core_data2 = __toESM(require_core_data());
  var import_router = __toESM(require_router());
  var import_url = __toESM(require_url());
  var import_compose = __toESM(require_compose());
  var import_html_entities = __toESM(require_html_entities());

  // packages/core-commands/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/core-commands"
  );

  // packages/core-commands/build-module/utils/order-entity-records-by-search.js
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

  // packages/core-commands/build-module/site-editor-navigation-commands.js
  var { useHistory } = unlock(import_router.privateApis);
  var icons = {
    post: post_default,
    page: page_default,
    wp_template: layout_default,
    wp_template_part: symbol_filled_default
  };
  function useDebouncedValue(value) {
    const [debouncedValue, setDebouncedValue] = (0, import_element2.useState)("");
    const debounced = (0, import_compose.useDebounce)(setDebouncedValue, 250);
    (0, import_element2.useEffect)(() => {
      debounced(value);
      return () => debounced.cancel();
    }, [debounced, value]);
    return debouncedValue;
  }
  var getNavigationCommandLoaderPerPostType = (postType) => function useNavigationCommandLoader({ search }) {
    const history = useHistory();
    const { isBlockBasedTheme, canCreateTemplate } = (0, import_data2.useSelect)(
      (select) => {
        return {
          isBlockBasedTheme: select(import_core_data2.store).getCurrentTheme()?.is_block_theme,
          canCreateTemplate: select(import_core_data2.store).canUser("create", {
            kind: "postType",
            name: "wp_template"
          })
        };
      },
      []
    );
    const delayedSearch = useDebouncedValue(search);
    const { records, isLoading } = (0, import_data2.useSelect)(
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
          records: select(import_core_data2.store).getEntityRecords(
            "postType",
            postType,
            query
          ),
          isLoading: !select(import_core_data2.store).hasFinishedResolution(
            "getEntityRecords",
            ["postType", postType, query]
          )
        };
      },
      [delayedSearch]
    );
    const commands = (0, import_element2.useMemo)(() => {
      return (records ?? []).map((record) => {
        const command = {
          name: postType + "-" + record.id,
          searchLabel: record.title?.rendered + " " + record.id,
          label: record.title?.rendered ? (0, import_html_entities.decodeEntities)(record.title?.rendered) : (0, import_i18n2.__)("(no title)"),
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
              const targetUrl = (0, import_url.addQueryArgs)("post.php", args);
              document.location = targetUrl;
              close();
            }
          };
        }
        const isSiteEditor = (0, import_url.getPath)(window.location.href)?.includes(
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
              document.location = (0, import_url.addQueryArgs)(
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
  var getNavigationCommandLoaderPerTemplate = (templateType) => function useNavigationCommandLoader({ search }) {
    const history = useHistory();
    const { isBlockBasedTheme, canCreateTemplate } = (0, import_data2.useSelect)(
      (select) => {
        return {
          isBlockBasedTheme: select(import_core_data2.store).getCurrentTheme()?.is_block_theme,
          canCreateTemplate: select(import_core_data2.store).canUser("create", {
            kind: "postType",
            name: templateType
          })
        };
      },
      []
    );
    const { records, isLoading } = (0, import_data2.useSelect)((select) => {
      const { getEntityRecords } = select(import_core_data2.store);
      const query = { per_page: -1 };
      return {
        records: getEntityRecords("postType", templateType, query),
        isLoading: !select(import_core_data2.store).hasFinishedResolution(
          "getEntityRecords",
          ["postType", templateType, query]
        )
      };
    }, []);
    const orderedRecords = (0, import_element2.useMemo)(() => {
      return orderEntityRecordsBySearch(records, search).slice(0, 10);
    }, [records, search]);
    const commands = (0, import_element2.useMemo)(() => {
      if (!canCreateTemplate || !isBlockBasedTheme && !templateType === "wp_template_part") {
        return [];
      }
      const isSiteEditor = (0, import_url.getPath)(window.location.href)?.includes(
        "site-editor.php"
      );
      const result = [];
      result.push(
        ...orderedRecords.map((record) => {
          return {
            name: templateType + "-" + record.id,
            searchLabel: record.title?.rendered + " " + record.id,
            label: record.title?.rendered ? record.title?.rendered : (0, import_i18n2.__)("(no title)"),
            icon: icons[templateType],
            callback: ({ close }) => {
              if (isSiteEditor) {
                history.navigate(
                  `/${templateType}/${record.id}?canvas=edit`
                );
              } else {
                document.location = (0, import_url.addQueryArgs)(
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
          label: (0, import_i18n2.__)("Go to: Template parts"),
          icon: symbol_filled_default,
          callback: ({ close }) => {
            if (isSiteEditor) {
              history.navigate(
                "/pattern?postType=wp_template_part&categoryId=all-parts"
              );
            } else {
              document.location = (0, import_url.addQueryArgs)(
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
  var getSiteEditorBasicNavigationCommands = () => function useSiteEditorBasicNavigationCommands() {
    const history = useHistory();
    const isSiteEditor = (0, import_url.getPath)(window.location.href)?.includes(
      "site-editor.php"
    );
    const { isBlockBasedTheme, canCreateTemplate, canCreatePatterns } = (0, import_data2.useSelect)((select) => {
      return {
        isBlockBasedTheme: select(import_core_data2.store).getCurrentTheme()?.is_block_theme,
        canCreateTemplate: select(import_core_data2.store).canUser("create", {
          kind: "postType",
          name: "wp_template"
        }),
        canCreatePatterns: select(import_core_data2.store).canUser("create", {
          kind: "postType",
          name: "wp_block"
        })
      };
    }, []);
    const commands = (0, import_element2.useMemo)(() => {
      const result = [];
      if (canCreateTemplate && isBlockBasedTheme) {
        result.push({
          name: "core/edit-site/open-styles",
          label: (0, import_i18n2.__)("Go to: Styles"),
          icon: styles_default,
          callback: ({ close }) => {
            if (isSiteEditor) {
              history.navigate("/styles");
            } else {
              document.location = (0, import_url.addQueryArgs)(
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
          label: (0, import_i18n2.__)("Go to: Navigation"),
          icon: navigation_default,
          callback: ({ close }) => {
            if (isSiteEditor) {
              history.navigate("/navigation");
            } else {
              document.location = (0, import_url.addQueryArgs)(
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
          label: (0, import_i18n2.__)("Go to: Templates"),
          icon: layout_default,
          callback: ({ close }) => {
            if (isSiteEditor) {
              history.navigate("/template");
            } else {
              document.location = (0, import_url.addQueryArgs)(
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
          label: (0, import_i18n2.__)("Go to: Patterns"),
          icon: symbol_default,
          callback: ({ close }) => {
            if (canCreateTemplate) {
              if (isSiteEditor) {
                history.navigate("/pattern");
              } else {
                document.location = (0, import_url.addQueryArgs)(
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
  var getGlobalStylesOpenCssCommands = () => function useGlobalStylesOpenCssCommands() {
    const history = useHistory();
    const isSiteEditor = (0, import_url.getPath)(window.location.href)?.includes(
      "site-editor.php"
    );
    const { canEditCSS } = (0, import_data2.useSelect)((select) => {
      const { getEntityRecord, __experimentalGetCurrentGlobalStylesId } = select(import_core_data2.store);
      const globalStylesId = __experimentalGetCurrentGlobalStylesId();
      const globalStyles = globalStylesId ? getEntityRecord("root", "globalStyles", globalStylesId) : void 0;
      return {
        canEditCSS: !!globalStyles?._links?.["wp:action-edit-css"]
      };
    }, []);
    const commands = (0, import_element2.useMemo)(() => {
      if (!canEditCSS) {
        return [];
      }
      return [
        {
          name: "core/open-styles-css",
          label: (0, import_i18n2.__)("Open custom CSS"),
          icon: brush_default,
          callback: ({ close }) => {
            close();
            if (isSiteEditor) {
              history.navigate("/styles?section=/css");
            } else {
              document.location = (0, import_url.addQueryArgs)(
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
    (0, import_commands2.useCommandLoader)({
      name: "core/edit-site/navigate-pages",
      hook: getNavigationCommandLoaderPerPostType("page"),
      disabled: isNetworkAdmin
    });
    (0, import_commands2.useCommandLoader)({
      name: "core/edit-site/navigate-posts",
      hook: getNavigationCommandLoaderPerPostType("post"),
      disabled: isNetworkAdmin
    });
    (0, import_commands2.useCommandLoader)({
      name: "core/edit-site/navigate-templates",
      hook: getNavigationCommandLoaderPerTemplate("wp_template"),
      disabled: isNetworkAdmin
    });
    (0, import_commands2.useCommandLoader)({
      name: "core/edit-site/navigate-template-parts",
      hook: getNavigationCommandLoaderPerTemplate("wp_template_part"),
      disabled: isNetworkAdmin
    });
    (0, import_commands2.useCommandLoader)({
      name: "core/edit-site/basic-navigation",
      hook: getSiteEditorBasicNavigationCommands(),
      context: "site-editor",
      disabled: isNetworkAdmin
    });
    (0, import_commands2.useCommandLoader)({
      name: "core/edit-site/global-styles-css",
      hook: getGlobalStylesOpenCssCommands(),
      disabled: isNetworkAdmin
    });
  }

  // packages/core-commands/build-module/private-apis.js
  function useCommands2() {
    useAdminNavigationCommands();
    useSiteEditorNavigationCommands();
  }
  var privateApis = {};
  lock(privateApis, {
    useCommands: useCommands2
  });

  // packages/core-commands/build-module/index.js
  var import_jsx_runtime10 = __toESM(require_jsx_runtime());
  var { RouterProvider } = unlock(import_router2.privateApis);
  function CommandPalette({ settings }) {
    const { menu_commands: menuCommands, is_network_admin: isNetworkAdmin } = settings;
    useAdminNavigationCommands(menuCommands);
    useSiteEditorNavigationCommands(isNetworkAdmin);
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(RouterProvider, { pathArg: "p", children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_commands3.CommandMenu, {}) });
  }
  function initializeCommandPalette(settings) {
    const root = document.createElement("div");
    document.body.appendChild(root);
    (0, import_element3.createRoot)(root).render(
      /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_element3.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(CommandPalette, { settings }) })
    );
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
