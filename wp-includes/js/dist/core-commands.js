/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
  "privateApis": function() { return /* reexport */ privateApis; }
});

;// CONCATENATED MODULE: external ["wp","commands"]
var external_wp_commands_namespaceObject = window["wp"]["commands"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plus.js


/**
 * WordPress dependencies
 */

const plus = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ var library_plus = (plus);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js


/**
 * WordPress dependencies
 */

const external = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"
}));
/* harmony default export */ var library_external = (external);

;// CONCATENATED MODULE: ./node_modules/@wordpress/core-commands/build-module/admin-navigation-commands.js
/**
 * WordPress dependencies
 */



function useAdminNavigationCommands() {
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/add-new-post',
    label: (0,external_wp_i18n_namespaceObject.__)('Add new post'),
    icon: library_plus,
    callback: () => {
      document.location.href = 'post-new.php';
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/add-new-page',
    label: (0,external_wp_i18n_namespaceObject.__)('Add new page'),
    icon: library_plus,
    callback: () => {
      document.location.href = 'post-new.php?post_type=page';
    }
  });
  (0,external_wp_commands_namespaceObject.useCommand)({
    name: 'core/manage-reusable-blocks',
    label: (0,external_wp_i18n_namespaceObject.__)('Manage all of my patterns'),
    callback: () => {
      document.location.href = 'edit.php?post_type=wp_block';
    },
    icon: library_external
  });
}

;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","coreData"]
var external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/post.js


/**
 * WordPress dependencies
 */

const post = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "m7.3 9.7 1.4 1.4c.2-.2.3-.3.4-.5 0 0 0-.1.1-.1.3-.5.4-1.1.3-1.6L12 7 9 4 7.2 6.5c-.6-.1-1.1 0-1.6.3 0 0-.1 0-.1.1-.3.1-.4.2-.6.4l1.4 1.4L4 11v1h1l2.3-2.3zM4 20h9v-1.5H4V20zm0-5.5V16h16v-1.5H4z"
}));
/* harmony default export */ var library_post = (post);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/page.js


/**
 * WordPress dependencies
 */

const page = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M7 5.5h10a.5.5 0 01.5.5v12a.5.5 0 01-.5.5H7a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM17 4H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2zm-1 3.75H8v1.5h8v-1.5zM8 11h8v1.5H8V11zm6 3.25H8v1.5h6v-1.5z"
}));
/* harmony default export */ var library_page = (page);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/layout.js


/**
 * WordPress dependencies
 */

const layout = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ var library_layout = (layout);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/symbol-filled.js


/**
 * WordPress dependencies
 */

const symbolFilled = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-17.6 1L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z"
}));
/* harmony default export */ var symbol_filled = (symbolFilled);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/navigation.js


/**
 * WordPress dependencies
 */

const navigation = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14.5c-3.6 0-6.5-2.9-6.5-6.5S8.4 5.5 12 5.5s6.5 2.9 6.5 6.5-2.9 6.5-6.5 6.5zM9 16l4.5-3L15 8.4l-4.5 3L9 16z"
}));
/* harmony default export */ var library_navigation = (navigation);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/styles.js


/**
 * WordPress dependencies
 */

const styles = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 4c-4.4 0-8 3.6-8 8v.1c0 4.1 3.2 7.5 7.2 7.9h.8c4.4 0 8-3.6 8-8s-3.6-8-8-8zm0 15V5c3.9 0 7 3.1 7 7s-3.1 7-7 7z"
}));
/* harmony default export */ var library_styles = (styles);

;// CONCATENATED MODULE: external ["wp","router"]
var external_wp_router_namespaceObject = window["wp"]["router"];
;// CONCATENATED MODULE: external ["wp","url"]
var external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","privateApis"]
var external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/core-commands/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my plugin or theme will inevitably break on the next WordPress release.', '@wordpress/core-commands');

;// CONCATENATED MODULE: ./node_modules/@wordpress/core-commands/build-module/site-editor-navigation-commands.js
/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


const {
  useHistory
} = unlock(external_wp_router_namespaceObject.privateApis);
const icons = {
  post: library_post,
  page: library_page,
  wp_template: library_layout,
  wp_template_part: symbol_filled
};

const getNavigationCommandLoaderPerPostType = postType => function useNavigationCommandLoader({
  search
}) {
  const history = useHistory();
  const supportsSearch = !['wp_template', 'wp_template_part'].includes(postType);
  const {
    records,
    isLoading
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEntityRecords
    } = select(external_wp_coreData_namespaceObject.store);
    const query = supportsSearch ? {
      search: !!search ? search : undefined,
      per_page: 10,
      orderby: search ? 'relevance' : 'date',
      status: ['publish', 'future', 'draft', 'pending', 'private']
    } : {
      per_page: -1
    };
    return {
      records: getEntityRecords('postType', postType, query),
      isLoading: !select(external_wp_coreData_namespaceObject.store).hasFinishedResolution('getEntityRecords', ['postType', postType, query])
    };
  }, [supportsSearch, search]);
  const commands = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return (records !== null && records !== void 0 ? records : []).slice(0, 10).map(record => {
      const isSiteEditor = (0,external_wp_url_namespaceObject.getPath)(window.location.href)?.includes('site-editor.php');
      const extraArgs = isSiteEditor ? {
        canvas: (0,external_wp_url_namespaceObject.getQueryArg)(window.location.href, 'canvas')
      } : {};
      return {
        name: postType + '-' + record.id,
        searchLabel: record.title?.rendered + ' ' + record.id,
        label: record.title?.rendered ? record.title?.rendered : (0,external_wp_i18n_namespaceObject.__)('(no title)'),
        icon: icons[postType],
        callback: ({
          close
        }) => {
          const args = {
            postType,
            postId: record.id,
            ...extraArgs
          };
          const targetUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('site-editor.php', args);

          if (isSiteEditor) {
            history.push(args);
          } else {
            document.location = targetUrl;
          }

          close();
        }
      };
    });
  }, [records, history]);
  return {
    commands,
    isLoading
  };
};

const usePageNavigationCommandLoader = getNavigationCommandLoaderPerPostType('page');
const usePostNavigationCommandLoader = getNavigationCommandLoaderPerPostType('post');
const useTemplateNavigationCommandLoader = getNavigationCommandLoaderPerPostType('wp_template');
const useTemplatePartNavigationCommandLoader = getNavigationCommandLoaderPerPostType('wp_template_part');

function useSiteEditorBasicNavigationCommands() {
  const history = useHistory();
  const isSiteEditor = (0,external_wp_url_namespaceObject.getPath)(window.location.href)?.includes('site-editor.php');
  const commands = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const result = [];
    result.push({
      name: 'core/edit-site/open-navigation',
      label: (0,external_wp_i18n_namespaceObject.__)('Open navigation'),
      icon: library_navigation,
      callback: ({
        close
      }) => {
        const args = {
          path: '/navigation'
        };
        const targetUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('site-editor.php', args);

        if (isSiteEditor) {
          history.push(args);
        } else {
          document.location = targetUrl;
        }

        close();
      }
    });
    result.push({
      name: 'core/edit-site/open-pages',
      label: (0,external_wp_i18n_namespaceObject.__)('Open pages'),
      icon: library_page,
      callback: ({
        close
      }) => {
        const args = {
          path: '/page'
        };
        const targetUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('site-editor.php', args);

        if (isSiteEditor) {
          history.push(args);
        } else {
          document.location = targetUrl;
        }

        close();
      }
    });
    result.push({
      name: 'core/edit-site/open-style-variations',
      label: (0,external_wp_i18n_namespaceObject.__)('Open style variations'),
      icon: library_styles,
      callback: ({
        close
      }) => {
        const args = {
          path: '/wp_global_styles'
        };
        const targetUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('site-editor.php', args);

        if (isSiteEditor) {
          history.push(args);
        } else {
          document.location = targetUrl;
        }

        close();
      }
    });
    result.push({
      name: 'core/edit-site/open-templates',
      label: (0,external_wp_i18n_namespaceObject.__)('Open templates'),
      icon: library_layout,
      callback: ({
        close
      }) => {
        const args = {
          path: '/wp_template'
        };
        const targetUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('site-editor.php', args);

        if (isSiteEditor) {
          history.push(args);
        } else {
          document.location = targetUrl;
        }

        close();
      }
    });
    result.push({
      name: 'core/edit-site/open-template-parts',
      label: (0,external_wp_i18n_namespaceObject.__)('Open library'),
      icon: symbol_filled,
      callback: ({
        close
      }) => {
        const args = {
          path: '/patterns'
        };
        const targetUrl = (0,external_wp_url_namespaceObject.addQueryArgs)('site-editor.php', args);

        if (isSiteEditor) {
          history.push(args);
        } else {
          document.location = targetUrl;
        }

        close();
      }
    });
    return result;
  }, [history, isSiteEditor]);
  return {
    commands,
    isLoading: false
  };
}

function useSiteEditorNavigationCommands() {
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: 'core/edit-site/navigate-pages',
    hook: usePageNavigationCommandLoader
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: 'core/edit-site/navigate-posts',
    hook: usePostNavigationCommandLoader
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: 'core/edit-site/navigate-templates',
    hook: useTemplateNavigationCommandLoader
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: 'core/edit-site/navigate-template-parts',
    hook: useTemplatePartNavigationCommandLoader
  });
  (0,external_wp_commands_namespaceObject.useCommandLoader)({
    name: 'core/edit-site/basic-navigation',
    hook: useSiteEditorBasicNavigationCommands,
    context: 'site-editor'
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/core-commands/build-module/private-apis.js
/**
 * Internal dependencies
 */




function useCommands() {
  useAdminNavigationCommands();
  useSiteEditorNavigationCommands();
}

const privateApis = {};
lock(privateApis, {
  useCommands
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/core-commands/build-module/index.js


(window.wp = window.wp || {}).coreCommands = __webpack_exports__;
/******/ })()
;