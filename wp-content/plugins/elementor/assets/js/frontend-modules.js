(self["webpackChunkelementorFrontend"] = self["webpackChunkelementorFrontend"] || []).push([["frontend-modules"],{

/***/ "../app/assets/js/event-track/apps-event-tracking.js":
/*!***********************************************************!*\
  !*** ../app/assets/js/event-track/apps-event-tracking.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.appsEventTrackingDispatch = exports.AppsEventTracking = void 0;
var _eventsConfig = _interopRequireDefault(__webpack_require__(/*! ../../../../core/common/modules/events-manager/assets/js/events-config */ "../core/common/modules/events-manager/assets/js/events-config.js"));
const EVENTS_MAP = {
  PAGE_VIEWS_WEBSITE_TEMPLATES: 'page_views_website_templates',
  KITS_CLOUD_UPGRADE_CLICKED: 'kits_cloud_upgrade_clicked',
  EXPORT_KIT_CUSTOMIZATION: 'export_kit_customization',
  IMPORT_KIT_CUSTOMIZATION: 'import_kit_customization',
  KIT_IMPORT_STATUS: 'kit_import_status',
  KIT_CLOUD_LIBRARY_APPLY: 'kit_cloud_library_apply',
  KIT_CLOUD_LIBRARY_DELETE: 'kit_cloud_library_delete',
  IMPORT_EXPORT_ADMIN_ACTION: 'ie_admin_action',
  KIT_IMPORT_UPLOAD_FILE: 'kit_import_upload_file'
};
const appsEventTrackingDispatch = (command, eventParams) => {
  // Add existing eventParams key value pair to the data/details object.
  const objectCreator = (array, obj) => {
    for (const key of array) {
      if (eventParams.hasOwnProperty(key) && eventParams[key] !== null) {
        obj[key] = eventParams[key];
      }
    }
    return obj;
  };
  const dataKeys = [];
  const detailsKeys = ['layout', 'site_part', 'error', 'document_name', 'document_type', 'view_type_clicked', 'tag', 'sort_direction', 'sort_type', 'action', 'grid_location', 'kit_name', 'page_source', 'element_position', 'element', 'event_type', 'modal_type', 'method', 'status', 'step', 'item', 'category', 'element_location', 'search_term', 'section', 'site_area'];
  const data = {};
  const details = {};
  const init = () => {
    objectCreator(detailsKeys, details);
    objectCreator(dataKeys, data);
    const commandSplit = command.split('/');
    data.placement = commandSplit[0];
    data.event = commandSplit[1];

    // If 'details' is not empty, add the details object to the data object.
    if (Object.keys(details).length) {
      data.details = details;
    }
  };
  init();
  $e.run(command, data);
};
exports.appsEventTrackingDispatch = appsEventTrackingDispatch;
class AppsEventTracking {
  static dispatchEvent(eventName, payload) {
    return elementorCommon.eventsManager.dispatchEvent(eventName, payload);
  }
  static sendPageViewsWebsiteTemplates(page) {
    return this.dispatchEvent(EVENTS_MAP.PAGE_VIEWS_WEBSITE_TEMPLATES, {
      trigger: _eventsConfig.default.triggers.pageLoaded,
      page_loaded: page,
      secondary_location: page
    });
  }
  static sendKitsCloudUpgradeClicked(upgradeLocation) {
    return this.dispatchEvent(EVENTS_MAP.KITS_CLOUD_UPGRADE_CLICKED, {
      trigger: _eventsConfig.default.triggers.click,
      secondary_location: upgradeLocation,
      upgrade_location: upgradeLocation
    });
  }
  static sendExportKitCustomization(payload) {
    return this.dispatchEvent(EVENTS_MAP.EXPORT_KIT_CUSTOMIZATION, {
      trigger: _eventsConfig.default.triggers.click,
      ...payload
    });
  }
  static sendImportKitCustomization(payload) {
    return this.dispatchEvent(EVENTS_MAP.IMPORT_KIT_CUSTOMIZATION, {
      trigger: _eventsConfig.default.triggers.click,
      ...payload
    });
  }
  static sendKitImportStatus(error = null) {
    const isError = !!error;
    return this.dispatchEvent(EVENTS_MAP.KIT_IMPORT_STATUS, {
      kit_import_status: !isError,
      ...(isError && {
        kit_import_error: error.message
      })
    });
  }
  static sendKitCloudLibraryApply(kitId, kitApplyUrl) {
    return this.dispatchEvent(EVENTS_MAP.KIT_CLOUD_LIBRARY_APPLY, {
      trigger: _eventsConfig.default.triggers.click,
      kit_cloud_id: kitId,
      ...(kitApplyUrl && {
        kit_apply_url: kitApplyUrl
      })
    });
  }
  static sendKitCloudLibraryDelete() {
    return this.dispatchEvent(EVENTS_MAP.KIT_CLOUD_LIBRARY_DELETE, {
      trigger: _eventsConfig.default.triggers.click
    });
  }
  static sendImportExportAdminAction(actionType) {
    return this.dispatchEvent(EVENTS_MAP.IMPORT_EXPORT_ADMIN_ACTION, {
      trigger: _eventsConfig.default.triggers.click,
      action_type: actionType
    });
  }
  static sendKitImportUploadFile(status) {
    return this.dispatchEvent(EVENTS_MAP.KIT_IMPORT_UPLOAD_FILE, {
      kit_import_upload_file_status: status
    });
  }
}
exports.AppsEventTracking = AppsEventTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/action-controls.js":
/*!*****************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/action-controls.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
var _wpDashboardTracking = _interopRequireWildcard(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _utils = __webpack_require__(/*! ./utils */ "../app/assets/js/event-track/dashboard/utils.js");
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function (e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != typeof e && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (const t in e) "default" !== t && {}.hasOwnProperty.call(e, t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, t)) && (i.get || i.set) ? o(f, t, i) : f[t] = e[t]); return f; })(e, t); }
const EXCLUDED_SELECTORS = {
  ADMIN_MENU: '#adminmenu',
  TOP_BAR: '.e-admin-top-bar',
  WP_ADMIN_BAR: '#wpadminbar',
  SUBMENU: '.wp-submenu',
  PROMO_PAGE: '.e-feature-promotion',
  PROMO_BLANK_STATE: '.elementor-blank_state',
  APP: '.e-app'
};
class ActionControlTracking extends _baseTracking.default {
  static init() {
    if (!_utils.DashboardUtils.isElementorPage()) {
      return;
    }
    this.attachDelegatedHandlers();
    this.addTrackingAttributesToFilterButtons();
    this.initializeLinkDataIds();
  }
  static initializeLinkDataIds() {
    const initializeLinks = () => {
      const links = document.querySelectorAll('a[href]');
      links.forEach(link => {
        if (this.isExcludedElement(link) || this.isNavigationLink(link) || link.hasAttribute('data-id')) {
          return;
        }
        const href = link.getAttribute('href');
        if (!href) {
          return;
        }
        const cleanedHref = this.removeNonceFromUrl(href);
        if (cleanedHref) {
          link.setAttribute('data-id', cleanedHref);
        }
      });
    };
    if ('loading' === document.readyState) {
      document.addEventListener('DOMContentLoaded', initializeLinks);
    } else {
      initializeLinks();
    }
  }
  static addTrackingAttributesToFilterButtons() {
    const body = document.body;
    if (!body) {
      return;
    }
    let screenPrefix = '';
    switch (true) {
      case body.classList.contains('post-type-elementor_library'):
        screenPrefix = 'elementor_library-library';
        break;
      case body.classList.contains('post-type-e-floating-buttons'):
        screenPrefix = 'e-floating-buttons';
        break;
      default:
        return;
    }
    const addDataIdToListTableButtons = () => {
      const buttonConfigs = [{
        id: 'post-query-submit',
        suffix: 'filter'
      }, {
        id: 'search-submit',
        suffix: 'search'
      }, {
        id: 'doaction',
        suffix: 'apply'
      }, {
        id: 'doaction2',
        suffix: 'apply-bottom'
      }];
      buttonConfigs.forEach(config => {
        const button = document.getElementById(config.id);
        if (!button || button.hasAttribute('data-id')) {
          return;
        }
        button.setAttribute('data-id', `${screenPrefix}-button-${config.suffix}`);
      });
    };
    if ('loading' === document.readyState) {
      document.addEventListener('DOMContentLoaded', addDataIdToListTableButtons);
    } else {
      addDataIdToListTableButtons();
    }
  }
  static isExcludedElement(element) {
    for (const selector of Object.values(EXCLUDED_SELECTORS)) {
      if (element.closest(selector)) {
        return true;
      }
    }
    if (element.classList.contains('go-pro')) {
      return true;
    }
    return false;
  }
  static attachDelegatedHandlers() {
    const FILTER_BUTTON_IDS = ['search-submit', 'post-query-submit'];
    this.addEventListenerTracked(document, 'click', event => {
      const base = event.target && 1 === event.target.nodeType ? event.target : event.target?.parentElement;
      if (!base) {
        return;
      }
      const button = base.closest('button, input[type="submit"], input[type="button"], .button, .e-btn');
      if (button && !this.isExcludedElement(button)) {
        if (FILTER_BUTTON_IDS.includes(button.id)) {
          this.trackControl(button, _wpDashboardTracking.CONTROL_TYPES.FILTER);
          return;
        }
        this.trackControl(button, _wpDashboardTracking.CONTROL_TYPES.BUTTON);
        return;
      }
      const link = base.closest('a');
      if (link && !this.isExcludedElement(link) && !this.isNavigationLink(link)) {
        this.trackControl(link, _wpDashboardTracking.CONTROL_TYPES.LINK);
      }
    }, {
      capture: false
    });
    this.addEventListenerTracked(document, 'change', event => {
      const base = event.target && 1 === event.target.nodeType ? event.target : event.target?.parentElement;
      if (!base) {
        return;
      }
      const toggle = base.closest('.components-toggle-control');
      if (toggle && !this.isExcludedElement(toggle)) {
        this.trackControl(toggle, _wpDashboardTracking.CONTROL_TYPES.TOGGLE);
        return;
      }
      const checkbox = base.closest('input[type="checkbox"]');
      if (checkbox && !this.isExcludedElement(checkbox)) {
        this.trackControl(checkbox, _wpDashboardTracking.CONTROL_TYPES.CHECKBOX);
        return;
      }
      const radio = base.closest('input[type="radio"]');
      if (radio && !this.isExcludedElement(radio)) {
        this.trackControl(radio, _wpDashboardTracking.CONTROL_TYPES.RADIO);
        return;
      }
      const select = base.closest('select');
      if (select && !this.isExcludedElement(select)) {
        this.trackControl(select, _wpDashboardTracking.CONTROL_TYPES.SELECT);
      }
    });
  }
  static isNavigationLink(link) {
    const href = link.getAttribute('href');
    if (!href) {
      return false;
    }
    if (href.startsWith('#') && href.includes('tab')) {
      return true;
    }
    if (link.classList.contains('nav-tab')) {
      return true;
    }
    const isInNavigation = link.closest('.wp-submenu, #adminmenu, .e-admin-top-bar, #wpadminbar');
    return !!isInNavigation;
  }
  static trackControl(element, controlType) {
    const controlIdentifier = this.extractControlIdentifier(element, controlType);
    if (!controlIdentifier) {
      return;
    }
    _wpDashboardTracking.default.trackActionControl(controlIdentifier, controlType);
  }
  static extractControlIdentifier(element, controlType) {
    if (_wpDashboardTracking.CONTROL_TYPES.RADIO === controlType) {
      const name = element.getAttribute('name');
      const value = element.value || element.getAttribute('value');
      if (name && value) {
        return `${name}-${value}`;
      }
      if (name) {
        return name;
      }
    }
    if (_wpDashboardTracking.CONTROL_TYPES.SELECT === controlType) {
      const name = element.getAttribute('name');
      if (name) {
        return name;
      }
    }
    if (_wpDashboardTracking.CONTROL_TYPES.CHECKBOX === controlType) {
      const name = element.getAttribute('name');
      if (name) {
        const checkboxesWithSameName = document.querySelectorAll(`input[type="checkbox"][name="${CSS.escape(name)}"]`);
        if (checkboxesWithSameName.length > 1) {
          const value = element.value || element.getAttribute('value');
          if (value) {
            return `${name}-${value}`;
          }
        }
        return name;
      }
    }
    if (_wpDashboardTracking.CONTROL_TYPES.LINK === controlType) {
      const dataId = element.getAttribute('data-id');
      if (dataId) {
        return dataId;
      }
      const href = element.getAttribute('href');
      if (href) {
        return this.removeNonceFromUrl(href);
      }
    }
    if (_wpDashboardTracking.CONTROL_TYPES.BUTTON === controlType || _wpDashboardTracking.CONTROL_TYPES.TOGGLE === controlType || _wpDashboardTracking.CONTROL_TYPES.FILTER === controlType) {
      const dataId = element.getAttribute('data-id');
      if (dataId) {
        return dataId;
      }
      const classIdMatch = this.extractClassId(element);
      if (classIdMatch) {
        return classIdMatch;
      }
    }
    return '';
  }
  static extractClassId(element) {
    const classes = element.className;
    if (!classes || 'string' !== typeof classes) {
      return '';
    }
    const classList = classes.split(' ');
    for (const cls of classList) {
      if (cls.startsWith('e-id-')) {
        return cls.substring(5);
      }
    }
    return '';
  }
  static removeNonceFromUrl(url) {
    try {
      const urlObj = new URL(url, window.location.origin);
      urlObj.searchParams.delete('_wpnonce');
      const postParam = urlObj.searchParams.get('post');
      if (postParam !== null && /^[0-9]+$/.test(postParam)) {
        urlObj.searchParams.delete('post');
      }
      return urlObj.pathname + urlObj.search + urlObj.hash;
    } catch (e) {
      return url;
    }
  }
}
var _default = exports["default"] = ActionControlTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/base-tracking.js":
/*!***************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/base-tracking.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
class BaseTracking {
  static ensureOwnArrays() {
    if (!Object.prototype.hasOwnProperty.call(this, 'observers')) {
      this.observers = [];
    }
    if (!Object.prototype.hasOwnProperty.call(this, 'eventListeners')) {
      this.eventListeners = [];
    }
  }
  static destroy() {
    this.ensureOwnArrays();
    this.observers.forEach(observer => observer.disconnect());
    this.observers = [];
    this.eventListeners.forEach(({
      target,
      type,
      handler,
      options
    }) => {
      target.removeEventListener(type, handler, options);
    });
    this.eventListeners = [];
  }
  static addObserver(target, options, callback) {
    this.ensureOwnArrays();
    const observer = new MutationObserver(callback);
    observer.observe(target, options);
    this.observers.push(observer);
    return observer;
  }
  static addEventListenerTracked(target, type, handler, options = {}) {
    this.ensureOwnArrays();
    target.addEventListener(type, handler, options);
    this.eventListeners.push({
      target,
      type,
      handler,
      options
    });
  }
}
var _default = exports["default"] = BaseTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/menu-promotion.js":
/*!****************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/menu-promotion.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _wpDashboardTracking = _interopRequireDefault(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
const PROMO_MENU_ITEMS = {
  go_elementor_pro: 'Upgrade'
};
class MenuPromotionTracking extends _baseTracking.default {
  static init() {
    this.attachDelegatedTracking();
  }
  static attachDelegatedTracking() {
    this.addEventListenerTracked(document, 'click', event => {
      const target = event.target;
      if (!target) {
        return;
      }
      const link = target.closest('a');
      if (!link) {
        return;
      }
      const href = link.getAttribute('href');
      if (!href) {
        return;
      }
      const menuItemKey = this.extractPromoMenuKey(href);
      if (!menuItemKey) {
        return;
      }
      this.handleMenuPromoClick(link, menuItemKey);
    }, {
      capture: true
    });
  }
  static extractPromoMenuKey(href) {
    for (const menuItemKey of Object.keys(PROMO_MENU_ITEMS)) {
      if (href.includes(`page=${menuItemKey}`)) {
        return menuItemKey;
      }
    }
    return null;
  }
  static handleMenuPromoClick(menuItem, menuItemKey) {
    const destination = menuItem.getAttribute('href');
    const promoName = PROMO_MENU_ITEMS[menuItemKey];
    const path = menuItemKey.replace('elementor_', '').replace(/_/g, '/');
    _wpDashboardTracking.default.trackPromoClicked(promoName, destination, path);
  }
}
var _default = exports["default"] = MenuPromotionTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/navigation.js":
/*!************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/navigation.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _wpDashboardTracking = _interopRequireWildcard(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function (e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != typeof e && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (const t in e) "default" !== t && {}.hasOwnProperty.call(e, t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, t)) && (i.get || i.set) ? o(f, t, i) : f[t] = e[t]); return f; })(e, t); }
const ELEMENTOR_MENU_SELECTORS = {
  ELEMENTOR_TOP_LEVEL: 'li#toplevel_page_elementor',
  TEMPLATES_TOP_LEVEL: 'li#menu-posts-elementor_library',
  ADMIN_MENU: '#adminmenu',
  TOP_LEVEL_LINK: '.wp-menu-name',
  SUBMENU_CONTAINER: '.wp-submenu',
  SUBMENU_ITEM: '.wp-submenu li a',
  SUBMENU_ITEM_TOP_LEVEL: '.wp-has-submenu'
};
class NavigationTracking extends _baseTracking.default {
  static init() {
    this.attachElementorMenuTracking();
    this.attachTemplatesMenuTracking();
  }
  static attachElementorMenuTracking() {
    const elementorMenu = document.querySelector(ELEMENTOR_MENU_SELECTORS.ELEMENTOR_TOP_LEVEL);
    if (!elementorMenu) {
      return;
    }
    this.attachMenuTracking(elementorMenu, 'Elementor');
  }
  static attachTemplatesMenuTracking() {
    const templatesMenu = document.querySelector(ELEMENTOR_MENU_SELECTORS.TEMPLATES_TOP_LEVEL);
    if (!templatesMenu) {
      return;
    }
    this.attachMenuTracking(templatesMenu, 'Templates');
  }
  static attachMenuTracking(menuElement, menuName) {
    this.addEventListenerTracked(menuElement, 'click', event => {
      this.handleMenuClick(event, menuName);
    });
  }
  static handleMenuClick(event, menuName) {
    const link = event.target.closest('a');
    if (!link) {
      return;
    }
    const isTopLevel = link.classList.contains('menu-top');
    const itemId = this.extractItemId(link);
    const area = this.determineNavArea(link);
    _wpDashboardTracking.default.trackNavClicked(itemId, isTopLevel ? null : menuName, area);
  }
  static extractItemId(link) {
    const textContent = link.textContent.trim();
    if (textContent) {
      return textContent;
    }
    const href = link.getAttribute('href');
    if (href) {
      const urlParams = new URLSearchParams(href.split('?')[1] || '');
      const page = urlParams.get('page');
      const postType = urlParams.get('post_type');
      if (page) {
        return page;
      }
      if (postType) {
        return postType;
      }
    }
    const id = link.getAttribute('id');
    if (id) {
      return id;
    }
    return 'unknown';
  }
  static determineNavArea(link) {
    const parentMenu = link.closest('li.menu-top');
    if (parentMenu) {
      const isSubmenuItem = link.closest(ELEMENTOR_MENU_SELECTORS.SUBMENU_CONTAINER);
      if (isSubmenuItem) {
        const submenuElement = link.closest(ELEMENTOR_MENU_SELECTORS.SUBMENU_ITEM_TOP_LEVEL);
        if (submenuElement.classList.contains('wp-not-current-submenu')) {
          return _wpDashboardTracking.NAV_AREAS.HOVER_MENU;
        }
        return _wpDashboardTracking.NAV_AREAS.SUBMENU;
      }
      return _wpDashboardTracking.NAV_AREAS.LEFT_MENU;
    }
    return _wpDashboardTracking.NAV_AREAS.LEFT_MENU;
  }
}
var _default = exports["default"] = NavigationTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/plugin-actions.js":
/*!****************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/plugin-actions.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _wpDashboardTracking = _interopRequireDefault(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
const PLUGIN_TYPE = {
  ELEMENTOR: 'core',
  ELEMENTOR_PRO: 'pro'
};
class PluginActions extends _baseTracking.default {
  static selectedReason = null;
  static init() {
    this.attachCoreDeactivationTracking();
    this.attachProDeactivationTracking();
    this.attachProDeletionTracking();
  }
  static attachCoreDeactivationTracking() {
    const dialogForm = document.querySelector('#elementor-deactivate-feedback-dialog-form');
    if (!dialogForm) {
      return;
    }
    this.addEventListenerTracked(dialogForm, 'change', event => {
      const target = event.target;
      if (target.classList.contains('elementor-deactivate-feedback-dialog-input')) {
        this.selectedReason = target.value;
      }
    });
    this.observeModalButtons();
  }
  static attachProDeactivationTracking() {
    const pluginsTable = document.querySelector('.plugins');
    if (!pluginsTable) {
      return;
    }
    this.addEventListenerTracked(pluginsTable, 'click', event => {
      const link = event.target.closest('a');
      if (link && 'deactivate-elementor-pro' === link.id) {
        this.trackProDeactivation();
      }
    }, {
      capture: true
    });
  }
  static observeModalButtons() {
    const checkAndAttachDelegation = () => {
      const modal = document.querySelector('#elementor-deactivate-feedback-modal');
      if (!modal) {
        return false;
      }
      this.addEventListenerTracked(modal, 'click', event => {
        const submitButton = event.target.closest('.dialog-submit');
        const skipButton = event.target.closest('.dialog-skip');
        if (submitButton) {
          this.trackCoreDeactivation('submit&deactivate');
        } else if (skipButton) {
          this.trackCoreDeactivation('skip&deactivate');
        }
      }, {
        capture: true
      });
      return true;
    };
    if (checkAndAttachDelegation()) {
      return;
    }
    this.addObserver(document.body, {
      childList: true,
      subtree: true
    }, (mutations, observer) => {
      if (checkAndAttachDelegation()) {
        observer.disconnect();
      }
    });
  }
  static getUserInput() {
    const reasonsWithInput = ['found_a_better_plugin', 'other'];
    if (!this.selectedReason || !reasonsWithInput.includes(this.selectedReason)) {
      return null;
    }
    const inputField = document.querySelector(`input[name="reason_${this.selectedReason}"]`);
    if (inputField && inputField.value) {
      return inputField.value;
    }
    return null;
  }
  static trackCoreDeactivation(action) {
    const properties = {
      deactivate_form_submit: action,
      deactivate_plugin_type: PLUGIN_TYPE.ELEMENTOR
    };
    if (this.selectedReason) {
      properties.deactivate_feedback_reason = this.selectedReason;
    }
    const userInput = this.getUserInput();
    if (userInput) {
      properties.deactivate_feedback_reason += `/${userInput}`;
    }
    _wpDashboardTracking.default.dispatchEvent('wpdash_deactivate_plugin', properties, {
      send_immediately: true
    });
  }
  static trackProDeactivation() {
    this.trackProAction('deactivate');
  }
  static attachProDeletionTracking() {
    if ('undefined' === typeof jQuery) {
      return;
    }
    jQuery(document).on('wp-plugin-deleting', (event, args) => {
      if ('elementor-pro' === args?.slug) {
        this.trackProAction('delete');
      }
    });
  }
  static destroy() {
    if ('undefined' !== typeof jQuery) {
      jQuery(document).off('wp-plugin-deleting');
    }
    _baseTracking.default.destroy.call(this);
  }
  static trackProAction(action) {
    const eventMap = {
      deactivate: {
        eventName: 'wpdash_deactivate_plugin',
        propertyKey: 'deactivate_plugin_type'
      },
      delete: {
        eventName: 'wpdash_delete_plugin',
        propertyKey: 'plugin_delete'
      }
    };
    const config = eventMap[action];
    if (!config) {
      return;
    }
    const properties = {
      [config.propertyKey]: PLUGIN_TYPE.ELEMENTOR_PRO
    };
    _wpDashboardTracking.default.dispatchEvent(config.eventName, properties, {
      send_immediately: true
    });
  }
}
var _default = exports["default"] = PluginActions;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/promotion.js":
/*!***********************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/promotion.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _wpDashboardTracking = _interopRequireDefault(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
const PROMO_SELECTORS = {
  PROMO_PAGE: '.e-feature-promotion, .elementor-settings-form-page, #elementor-element-manager-wrap',
  PROMO_BLANK_STATE: '.elementor-blank_state',
  CTA_BUTTON: '.go-pro',
  TITLE: 'h3'
};
class PromotionTracking extends _baseTracking.default {
  static init() {
    this.attachDelegatedTracking();
  }
  static attachDelegatedTracking() {
    this.addEventListenerTracked(document, 'click', event => {
      const target = event.target;
      if (!target) {
        return;
      }
      const button = target.closest(`a${PROMO_SELECTORS.CTA_BUTTON}`);
      if (!button) {
        return;
      }
      const promoPage = button.closest(`${PROMO_SELECTORS.PROMO_PAGE}, ${PROMO_SELECTORS.PROMO_BLANK_STATE}`);
      if (!promoPage) {
        return;
      }
      this.handlePromoClick(button, promoPage);
    }, {
      capture: true
    });
  }
  static handlePromoClick(button, promoPage) {
    const promoTitle = this.extractPromoTitle(promoPage, button);
    const destination = button.getAttribute('href');
    const path = this.extractPromoPath();
    _wpDashboardTracking.default.trackPromoClicked(promoTitle, destination, path);
  }
  static extractPromoTitle(promoPage, button) {
    const titleElement = promoPage.querySelector(PROMO_SELECTORS.TITLE);
    return titleElement ? titleElement.textContent.trim() : button.textContent.trim();
  }
  static extractPromoPath() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    if (!page) {
      return 'elementor';
    }
    return page.replace('elementor_', '').replace(/_/g, '/');
  }
}
var _default = exports["default"] = PromotionTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/screen-view.js":
/*!*************************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/screen-view.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "../node_modules/core-js/modules/esnext.iterator.filter.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
var _wpDashboardTracking = _interopRequireWildcard(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _utils = __webpack_require__(/*! ./utils */ "../app/assets/js/event-track/dashboard/utils.js");
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function (e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != typeof e && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (const t in e) "default" !== t && {}.hasOwnProperty.call(e, t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, t)) && (i.get || i.set) ? o(f, t, i) : f[t] = e[t]); return f; })(e, t); }
const SCREEN_SELECTORS = {
  NAV_TAB_WRAPPER: '.nav-tab-wrapper',
  NAV_TAB: '.nav-tab',
  NAV_TAB_ACTIVE: '.nav-tab-active',
  SETTINGS_FORM_PAGE: '.elementor-settings-form-page',
  SETTINGS_FORM_PAGE_ACTIVE: '.elementor-settings-form-page.elementor-active',
  FLOATING_ELEMENTS_MODAL: '#elementor-new-floating-elements-modal',
  TEMPLATE_DIALOG_MODAL: '#elementor-new-template-dialog-content'
};
const TRACKED_MODALS = [SCREEN_SELECTORS.FLOATING_ELEMENTS_MODAL, SCREEN_SELECTORS.TEMPLATE_DIALOG_MODAL];
class ScreenViewTracking extends _baseTracking.default {
  static trackedScreens = new Set();
  static init() {
    if (!_utils.DashboardUtils.isElementorPage()) {
      return;
    }
    this.attachTabChangeTracking();
  }
  static destroy() {
    super.destroy();
    this.trackedScreens.clear();
  }
  static getScreenData() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    const postType = urlParams.get('post_type');
    const hash = window.location.hash;
    let screenId = '';
    let screenType = '';
    if (page) {
      screenId = page;
    } else if (postType) {
      screenId = postType;
    } else {
      screenId = this.getScreenIdFromBody();
    }
    if (this.isElementorAppPage()) {
      const appScreenData = this.getAppScreenData(hash);
      if (appScreenData) {
        return appScreenData;
      }
    }
    const hasNavTabs = document.querySelector(SCREEN_SELECTORS.NAV_TAB_WRAPPER);
    const hasSettingsTabs = document.querySelectorAll(SCREEN_SELECTORS.SETTINGS_FORM_PAGE).length > 1;
    if (hasNavTabs || hasSettingsTabs || hash && !this.isElementorAppPage()) {
      screenType = _wpDashboardTracking.SCREEN_TYPES.TAB;
      if (hash) {
        const tabId = hash.replace(/^#(tab-)?/, '');
        screenId = `${screenId}-${tabId}`;
      } else if (hasNavTabs) {
        const activeTab = document.querySelector(SCREEN_SELECTORS.NAV_TAB_ACTIVE);
        if (activeTab) {
          const tabText = activeTab.textContent.trim();
          const tabHref = activeTab.getAttribute('href');
          if (tabText) {
            screenId = `${screenId}-${this.sanitizeScreenId(tabText)}`;
          } else if (tabHref && tabHref.includes('#')) {
            const tabId = tabHref.split('#')[1];
            screenId = `${screenId}-${tabId}`;
          }
        }
      } else if (hasSettingsTabs) {
        const activeSettingsTab = document.querySelector(SCREEN_SELECTORS.SETTINGS_FORM_PAGE_ACTIVE);
        if (activeSettingsTab) {
          const tabId = activeSettingsTab.id;
          if (tabId) {
            screenId = `${screenId}-${tabId}`;
          }
        }
      }
    }
    return {
      screenId,
      screenType
    };
  }
  static isElementorAppPage() {
    const urlParams = new URLSearchParams(window.location.search);
    return 'elementor-app' === urlParams.get('page');
  }
  static getAppScreenData(hash) {
    if (!hash) {
      return null;
    }
    const cleanHash = hash.replace(/^#/, '');
    if (!cleanHash.startsWith('/')) {
      return null;
    }
    const pathParts = cleanHash.split('/').filter(Boolean);
    if (0 === pathParts.length) {
      return null;
    }
    const screenId = pathParts.join('/');
    const screenType = _wpDashboardTracking.SCREEN_TYPES.APP_SCREEN;
    return {
      screenId,
      screenType
    };
  }
  static getScreenIdFromBody() {
    const body = document.body;
    const bodyClasses = body.className.split(' ');
    for (const cls of bodyClasses) {
      if (cls.startsWith('elementor') && (cls.includes('page') || cls.includes('post-type'))) {
        return cls;
      }
    }
    return 'elementor-unknown';
  }
  static sanitizeScreenId(text) {
    return text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
  }
  static attachTabChangeTracking() {
    this.attachNavTabTracking();
    this.attachHashChangeTracking();
    this.attachSettingsTabTracking();
    this.attachModalTracking();
  }
  static attachNavTabTracking() {
    const wrapper = document.querySelector(SCREEN_SELECTORS.NAV_TAB_WRAPPER);
    if (!wrapper) {
      return;
    }
    this.addEventListenerTracked(wrapper, 'click', event => {
      const navTab = event.target.closest(SCREEN_SELECTORS.NAV_TAB);
      if (navTab && !navTab.classList.contains('nav-tab-active')) {
        const screenData = this.getScreenData();
        if (screenData) {
          this.trackScreen(screenData.screenId, screenData.screenType);
        }
      }
    });
  }
  static attachHashChangeTracking() {
    this.addEventListenerTracked(window, 'hashchange', () => {
      const screenData = this.getScreenData();
      if (screenData) {
        this.trackScreen(screenData.screenId, screenData.screenType);
      }
    });
  }
  static attachSettingsTabTracking() {
    const settingsPages = document.querySelectorAll(SCREEN_SELECTORS.SETTINGS_FORM_PAGE);
    if (0 === settingsPages.length) {
      return;
    }
    settingsPages.forEach(page => {
      this.addObserver(page, {
        attributes: true,
        attributeFilter: ['class']
      }, () => {
        const screenData = this.getScreenData();
        if (screenData) {
          this.trackScreen(screenData.screenId, screenData.screenType);
        }
      });
    });
  }
  static attachModalTracking() {
    this.addObserver(document.body, {
      childList: true,
      subtree: true
    }, mutations => {
      for (const mutation of mutations) {
        if ('childList' === mutation.type) {
          TRACKED_MODALS.forEach(modalSelector => {
            const modal = document.querySelector(modalSelector);
            if (modal && this.isModalVisible(modal)) {
              const modalId = modalSelector.replace('#', '');
              this.trackScreen(modalId, _wpDashboardTracking.SCREEN_TYPES.POPUP);
            }
          });
        }
      }
    });
  }
  static isModalVisible(element) {
    if (!element) {
      return false;
    }
    const style = window.getComputedStyle(element);
    return 'none' !== style.display && 0 !== parseFloat(style.opacity);
  }
  static trackScreen(screenId, screenType = _wpDashboardTracking.SCREEN_TYPES.TOP_LEVEL_PAGE) {
    const trackingKey = `${screenId}-${screenType}`;
    if (this.trackedScreens.has(trackingKey)) {
      return;
    }
    this.trackedScreens.add(trackingKey);
    _wpDashboardTracking.default.trackScreenViewed(screenId, screenType);
  }
}
var _default = exports["default"] = ScreenViewTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/top-bar.js":
/*!*********************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/top-bar.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "../node_modules/core-js/modules/esnext.iterator.filter.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
var _wpDashboardTracking = _interopRequireWildcard(__webpack_require__(/*! ../wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _baseTracking = _interopRequireDefault(__webpack_require__(/*! ./base-tracking */ "../app/assets/js/event-track/dashboard/base-tracking.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function (e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != typeof e && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (const t in e) "default" !== t && {}.hasOwnProperty.call(e, t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, t)) && (i.get || i.set) ? o(f, t, i) : f[t] = e[t]); return f; })(e, t); }
const TOP_BAR_SELECTORS = {
  TOP_BAR_ROOT: '.e-admin-top-bar',
  BAR_BUTTON: '.e-admin-top-bar__bar-button',
  BUTTON_TITLE: '.e-admin-top-bar__bar-button-title',
  MAIN_AREA: '.e-admin-top-bar__main-area',
  SECONDARY_AREA: '.e-admin-top-bar__secondary-area'
};
class TopBarTracking extends _baseTracking.default {
  static init() {
    this.waitForTopBar();
  }
  static waitForTopBar() {
    const topBar = document.querySelector(TOP_BAR_SELECTORS.TOP_BAR_ROOT);
    if (topBar) {
      this.attachTopBarTracking(topBar);
      return;
    }
    const observer = this.addObserver(document.body, {
      childList: true,
      subtree: true
    }, () => {
      const foundTopBar = document.querySelector(TOP_BAR_SELECTORS.TOP_BAR_ROOT);
      if (foundTopBar) {
        this.attachTopBarTracking(foundTopBar);
        observer.disconnect();
        clearTimeout(timeoutId);
      }
    });
    const timeoutId = setTimeout(() => {
      observer.disconnect();
    }, 10000);
  }
  static attachTopBarTracking(topBar) {
    const buttons = topBar.querySelectorAll(TOP_BAR_SELECTORS.BAR_BUTTON);
    buttons.forEach(button => {
      this.addEventListenerTracked(button, 'click', event => {
        this.handleTopBarClick(event);
      });
    });
    this.observeTopBarChanges(topBar);
  }
  static observeTopBarChanges(topBar) {
    this.addObserver(topBar, {
      childList: true,
      subtree: true
    }, mutations => {
      mutations.forEach(mutation => {
        if ('childList' === mutation.type) {
          mutation.addedNodes.forEach(node => {
            if (1 === node.nodeType) {
              if (node.matches && node.matches(TOP_BAR_SELECTORS.BAR_BUTTON)) {
                this.addEventListenerTracked(node, 'click', event => {
                  this.handleTopBarClick(event);
                });
              } else {
                const buttons = node.querySelectorAll ? node.querySelectorAll(TOP_BAR_SELECTORS.BAR_BUTTON) : [];
                buttons.forEach(button => {
                  this.addEventListenerTracked(button, 'click', event => {
                    this.handleTopBarClick(event);
                  });
                });
              }
            }
          });
        }
      });
    });
  }
  static handleTopBarClick(event) {
    const button = event.currentTarget;
    const itemId = this.extractItemId(button);
    _wpDashboardTracking.default.trackNavClicked(itemId, null, _wpDashboardTracking.NAV_AREAS.TOP_BAR);
  }
  static extractItemId(button) {
    const titleElement = button.querySelector(TOP_BAR_SELECTORS.BUTTON_TITLE);
    if (titleElement && titleElement.textContent.trim()) {
      return titleElement.textContent.trim();
    }
    const textContent = button.textContent.trim();
    if (textContent) {
      return textContent;
    }
    const href = button.getAttribute('href');
    if (href) {
      const urlParams = new URLSearchParams(href.split('?')[1] || '');
      const page = urlParams.get('page');
      if (page) {
        return page;
      }
      if (href.includes('/wp-admin/')) {
        const pathParts = href.split('/wp-admin/')[1];
        if (pathParts) {
          return pathParts.split('?')[0];
        }
      }
      try {
        const url = new URL(href, window.location.origin);
        return url.pathname.split('/').filter(Boolean).pop() || url.hostname;
      } catch (error) {
        return href;
      }
    }
    const dataInfo = button.getAttribute('data-info');
    if (dataInfo) {
      return dataInfo;
    }
    const classes = button.className.split(' ').filter(cls => cls && 'e-admin-top-bar__bar-button' !== cls);
    if (classes.length > 0) {
      return classes.join('-');
    }
    return 'unknown-top-bar-button';
  }
}
var _default = exports["default"] = TopBarTracking;

/***/ }),

/***/ "../app/assets/js/event-track/dashboard/utils.js":
/*!*******************************************************!*\
  !*** ../app/assets/js/event-track/dashboard/utils.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.DashboardUtils = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.some.js */ "../node_modules/core-js/modules/esnext.iterator.some.js");
const DashboardUtils = exports.DashboardUtils = {
  isElementorPage() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    if (page && (page.startsWith('elementor') || page.includes('elementor'))) {
      return true;
    }
    const postType = urlParams.get('post_type');
    if ('elementor_library' === postType || 'e-floating-buttons' === postType) {
      return true;
    }
    const body = document.body;
    const bodyClasses = body.className.split(' ');
    return bodyClasses.some(cls => cls.includes('elementor') && (cls.includes('page') || cls.includes('post-type')));
  }
};

/***/ }),

/***/ "../app/assets/js/event-track/wp-dashboard-tracking.js":
/*!*************************************************************!*\
  !*** ../app/assets/js/event-track/wp-dashboard-tracking.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.SCREEN_TYPES = exports.NAV_AREAS = exports.CONTROL_TYPES = void 0;
__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
var _navigation = _interopRequireDefault(__webpack_require__(/*! ./dashboard/navigation */ "../app/assets/js/event-track/dashboard/navigation.js"));
var _pluginActions = _interopRequireDefault(__webpack_require__(/*! ./dashboard/plugin-actions */ "../app/assets/js/event-track/dashboard/plugin-actions.js"));
var _promotion = _interopRequireDefault(__webpack_require__(/*! ./dashboard/promotion */ "../app/assets/js/event-track/dashboard/promotion.js"));
var _screenView = _interopRequireDefault(__webpack_require__(/*! ./dashboard/screen-view */ "../app/assets/js/event-track/dashboard/screen-view.js"));
var _topBar = _interopRequireDefault(__webpack_require__(/*! ./dashboard/top-bar */ "../app/assets/js/event-track/dashboard/top-bar.js"));
var _menuPromotion = _interopRequireDefault(__webpack_require__(/*! ./dashboard/menu-promotion */ "../app/assets/js/event-track/dashboard/menu-promotion.js"));
var _actionControls = _interopRequireDefault(__webpack_require__(/*! ./dashboard/action-controls */ "../app/assets/js/event-track/dashboard/action-controls.js"));
const SESSION_TIMEOUT_MINUTES = 30;
const MINUTE_MS = 60 * 1000;
const SESSION_TIMEOUT = SESSION_TIMEOUT_MINUTES * MINUTE_MS;
const ACTIVITY_CHECK_INTERVAL = 1 * MINUTE_MS;
const SESSION_STORAGE_KEY = 'elementor_wpdash_session';
const PENDING_NAV_CLICK_KEY = 'elementor_wpdash_pending_nav';
const CONTROL_TYPES = exports.CONTROL_TYPES = {
  BUTTON: 'button',
  CHECKBOX: 'checkbox',
  RADIO: 'radio',
  LINK: 'link',
  SELECT: 'select',
  TOGGLE: 'toggle',
  FILTER: 'filter'
};
const NAV_AREAS = exports.NAV_AREAS = {
  LEFT_MENU: 'left_menu',
  SUBMENU: 'submenu',
  HOVER_MENU: 'hover_menu',
  TOP_BAR: 'top_bar'
};
const SCREEN_TYPES = exports.SCREEN_TYPES = {
  TAB: 'tab',
  POPUP: 'popup',
  APP_SCREEN: 'app_screen'
};
class WpDashboardTracking {
  static sessionStartTime = Date.now();
  static lastActivityTime = Date.now();
  static sessionEnded = false;
  static navItemsVisited = new Set();
  static activityCheckInterval = null;
  static initialized = false;
  static navigationListeners = [];
  static isNavigatingToElementor = false;
  static init() {
    if (this.initialized) {
      return;
    }
    this.restoreOrCreateSession();
    if (this.isEventsManagerAvailable()) {
      this.startSessionMonitoring();
      this.attachActivityListeners();
      this.attachNavigationListener();
      this.initialized = true;
    }
  }
  static restoreOrCreateSession() {
    const storedSession = this.getStoredSession();
    if (storedSession) {
      this.sessionStartTime = storedSession.sessionStartTime;
      this.navItemsVisited = new Set(storedSession.navItemsVisited);
      this.lastActivityTime = Date.now();
      this.sessionEnded = false;
    } else {
      this.sessionStartTime = Date.now();
      this.lastActivityTime = Date.now();
      this.sessionEnded = false;
      this.navItemsVisited = new Set();
    }
    this.processPendingNavClick();
    this.saveSessionToStorage();
  }
  static processPendingNavClick() {
    try {
      const pendingNav = sessionStorage.getItem(PENDING_NAV_CLICK_KEY);
      if (pendingNav) {
        const {
          itemId,
          rootItem,
          area
        } = JSON.parse(pendingNav);
        this.navItemsVisited.add(itemId);
        const properties = {
          wpdash_nav_item_id: itemId,
          wpdash_nav_area: area
        };
        if (rootItem) {
          properties.wpdash_nav_item_root = rootItem;
        }
        this.dispatchEvent('wpdash_nav_clicked', properties, {
          send_immediately: true
        });
        sessionStorage.removeItem(PENDING_NAV_CLICK_KEY);
      }
    } catch (error) {
      sessionStorage.removeItem(PENDING_NAV_CLICK_KEY);
    }
  }
  static getStoredSession() {
    try {
      const stored = sessionStorage.getItem(SESSION_STORAGE_KEY);
      return stored ? JSON.parse(stored) : null;
    } catch (error) {
      return null;
    }
  }
  static saveSessionToStorage() {
    const sessionData = {
      sessionStartTime: this.sessionStartTime,
      navItemsVisited: Array.from(this.navItemsVisited)
    };
    sessionStorage.setItem(SESSION_STORAGE_KEY, JSON.stringify(sessionData));
  }
  static clearStoredSession() {
    sessionStorage.removeItem(SESSION_STORAGE_KEY);
  }
  static isEventsManagerAvailable() {
    return elementorCommon?.eventsManager && 'function' === typeof elementorCommon.eventsManager.dispatchEvent;
  }
  static canSendEvents() {
    return elementorCommon?.config?.editor_events?.can_send_events || false;
  }
  static dispatchEvent(eventName, properties = {}, options = {}) {
    if (!this.isEventsManagerAvailable() || !this.canSendEvents()) {
      return;
    }
    elementorCommon.eventsManager.dispatchEvent(eventName, properties, options);
  }
  static updateActivity() {
    this.lastActivityTime = Date.now();
  }
  static startSessionMonitoring() {
    this.activityCheckInterval = setInterval(() => {
      this.checkSessionTimeout();
    }, ACTIVITY_CHECK_INTERVAL);
    window.addEventListener('beforeunload', () => {
      if (!this.sessionEnded && !this.isNavigatingToElementor) {
        this.trackSessionEnd('tab_closed');
      }
    });
    document.addEventListener('visibilitychange', () => {
      if (!this.sessionEnded && document.hidden) {
        const timeSinceLastActivity = Date.now() - this.lastActivityTime;
        if (timeSinceLastActivity > SESSION_TIMEOUT) {
          this.trackSessionEnd('tab_inactive');
        }
      }
    });
  }
  static isElementorPage(url) {
    try {
      const urlObj = new URL(url, window.location.origin);
      const params = urlObj.searchParams;
      const page = params.get('page');
      const postType = params.get('post_type');
      const action = params.get('action');
      const elementorPages = ['elementor', 'go_knowledge_base_site', 'e-form-submissions'];
      const elementorPostTypes = ['elementor_library', 'e-floating-buttons'];
      return page && elementorPages.some(p => page.includes(p)) || postType && elementorPostTypes.includes(postType) || action && action.includes('elementor');
    } catch (error) {
      return false;
    }
  }
  static isPluginsPage(url) {
    try {
      const urlObj = new URL(url, window.location.origin);
      return urlObj.pathname.includes('plugins.php');
    } catch (error) {
      return false;
    }
  }
  static isNavigatingAwayFromElementor(targetUrl) {
    if (!targetUrl) {
      return false;
    }
    if (targetUrl.startsWith('#')) {
      return false;
    }
    return !this.isElementorPage(targetUrl);
  }
  static isLinkOpeningInNewTab(link) {
    const target = link.getAttribute('target');
    return '_blank' === target || '_new' === target;
  }
  static attachNavigationListener() {
    const handleLinkClick = event => {
      const link = event.target.closest('a');
      if (link && link.href) {
        if (this.isLinkOpeningInNewTab(link)) {
          return;
        }
        if (!this.sessionEnded && this.isNavigatingAwayFromElementor(link.href)) {
          this.trackSessionEnd('navigate_away');
        } else if (this.isElementorPage(link.href)) {
          this.isNavigatingToElementor = true;
        }
      }
    };
    const handleFormSubmit = event => {
      const form = event.target;
      if (form.action) {
        if (!this.sessionEnded && this.isNavigatingAwayFromElementor(form.action)) {
          this.trackSessionEnd('navigate_away');
        } else if (this.isElementorPage(form.action)) {
          this.isNavigatingToElementor = true;
        }
      }
    };
    document.addEventListener('click', handleLinkClick, true);
    document.addEventListener('submit', handleFormSubmit, true);
    this.navigationListeners.push({
      type: 'click',
      handler: handleLinkClick
    }, {
      type: 'submit',
      handler: handleFormSubmit
    });
  }
  static checkSessionTimeout() {
    const timeSinceLastActivity = Date.now() - this.lastActivityTime;
    if (timeSinceLastActivity > SESSION_TIMEOUT && !this.sessionEnded) {
      this.trackSessionEnd('timeout');
    }
  }
  static attachActivityListeners() {
    const events = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];
    events.forEach(event => {
      document.addEventListener(event, () => {
        this.updateActivity();
      }, {
        capture: true,
        passive: true
      });
    });
  }
  static formatDuration(milliseconds) {
    const totalSeconds = Math.floor(milliseconds / 1000);
    return Number(totalSeconds.toFixed(2));
  }
  static trackNavClicked(itemId, rootItem = null, area = NAV_AREAS.LEFT_MENU) {
    if (!this.initialized) {
      const pendingNav = {
        itemId,
        rootItem,
        area
      };
      sessionStorage.setItem(PENDING_NAV_CLICK_KEY, JSON.stringify(pendingNav));
      return;
    }
    this.updateActivity();
    this.navItemsVisited.add(itemId);
    this.saveSessionToStorage();
    const properties = {
      wpdash_nav_item_id: itemId,
      wpdash_nav_area: area
    };
    if (rootItem) {
      properties.wpdash_nav_item_root = rootItem;
    }
    this.dispatchEvent('wpdash_nav_clicked', properties);
  }
  static trackScreenViewed(screenId, screenType = SCREEN_TYPES.TAB) {
    this.updateActivity();
    const properties = {
      wpdash_screen_id: screenId,
      wpdash_screen_type: screenType
    };
    this.dispatchEvent('wpdash_screen_viewed', properties);
  }
  static trackActionControl(controlIdentifier, controlType) {
    this.updateActivity();
    const properties = {
      wpdash_action_control_interacted: controlIdentifier,
      wpdash_control_type: controlType
    };
    this.dispatchEvent('wpdash_action_control', properties);
  }
  static trackPromoClicked(promoName, destination, clickPath) {
    this.updateActivity();
    const properties = {
      wpdash_promo_name: promoName,
      wpdash_promo_destination: destination,
      wpdash_promo_clicked_path: clickPath
    };
    this.dispatchEvent('wpdash_promo_clicked', properties);
  }
  static trackSessionEnd(reason = 'timeout') {
    if (this.sessionEnded) {
      return;
    }
    this.sessionEnded = true;
    if (this.activityCheckInterval) {
      clearInterval(this.activityCheckInterval);
      this.activityCheckInterval = null;
    }
    const duration = Date.now() - this.sessionStartTime;
    const properties = {
      wpdash_endstate_nav_summary: Array.from(this.navItemsVisited),
      wpdash_endstate_nav_count: this.navItemsVisited.size,
      wpdash_endstate_duration: this.formatDuration(duration),
      reason
    };
    this.dispatchEvent('wpdash_session_end_state', properties);
    this.clearStoredSession();
  }
  static destroy() {
    if (this.activityCheckInterval) {
      clearInterval(this.activityCheckInterval);
    }
    this.navigationListeners.forEach(({
      type,
      handler
    }) => {
      document.removeEventListener(type, handler, true);
    });
    this.navigationListeners = [];
    _topBar.default.destroy();
    _screenView.default.destroy();
    _promotion.default.destroy();
    _menuPromotion.default.destroy();
    _actionControls.default.destroy();
    this.initialized = false;
  }
}
exports["default"] = WpDashboardTracking;
window.addEventListener('elementor/admin/init', () => {
  const currentUrl = window.location.href;
  const isPluginsPage = WpDashboardTracking.isPluginsPage(currentUrl);
  const isElementorPage = WpDashboardTracking.isElementorPage(currentUrl);
  if (isPluginsPage) {
    _pluginActions.default.init();
  }
  _navigation.default.init();
  if (isElementorPage) {
    WpDashboardTracking.init();
    _topBar.default.init();
    _screenView.default.init();
    _promotion.default.init();
    _menuPromotion.default.init();
    _actionControls.default.init();
  }
});
window.addEventListener('beforeunload', () => {
  _navigation.default.destroy();
  _pluginActions.default.destroy();
  WpDashboardTracking.destroy();
});

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/registry/base.js":
/*!************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/registry/base.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.BaseRegistry = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "../node_modules/core-js/modules/esnext.iterator.filter.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.map.js */ "../node_modules/core-js/modules/esnext.iterator.map.js");
class BaseRegistry {
  constructor() {
    this.sections = new Map();
  }
  register(section) {
    if (!section.key || !section.title) {
      throw new Error('Template type must have key and title');
    }
    const existingSection = this.get(section.key);
    const formattedSection = existingSection || this.formatSection(section);
    if (section.children) {
      // If existing section has children, merge them with new children
      if (formattedSection.children) {
        const existingChildrenMap = new Map(formattedSection.children.map(child => [child.key, child]));

        // Override existing children with new ones and add new children
        section.children.forEach(childSection => {
          const formattedChild = this.formatSection(childSection);
          existingChildrenMap.set(childSection.key, formattedChild);
        });
        formattedSection.children = Array.from(existingChildrenMap.values());
      } else {
        formattedSection.children = section.children.map(childSection => this.formatSection(childSection));
      }
    }
    this.sections.set(section.key, formattedSection);
  }
  formatSection({
    children,
    ...section
  }) {
    return {
      key: section.key,
      title: section.title,
      description: section.description || '',
      useParentDefault: section.useParentDefault !== false,
      getInitialState: section.getInitialState || null,
      component: section.component || null,
      order: section.order || 10,
      isAvailable: section.isAvailable || (() => true),
      ...section
    };
  }
  getAll() {
    return Array.from(this.sections.values()).filter(type => type.isAvailable()).map(type => {
      if (type.children) {
        return {
          ...type,
          children: [...type.children].sort((a, b) => a.order - b.order)
        };
      }
      return type;
    }).sort((a, b) => a.order - b.order);
  }
  get(key) {
    return this.sections.get(key);
  }
}
exports.BaseRegistry = BaseRegistry;

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/registry/customization-dialogs.js":
/*!*****************************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/registry/customization-dialogs.js ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.customizationDialogsRegistry = void 0;
var _base = __webpack_require__(/*! ./base */ "../app/modules/import-export-customization/assets/js/shared/registry/base.js");
const customizationDialogsRegistry = exports.customizationDialogsRegistry = new _base.BaseRegistry();

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/utils/template-registry-helpers.js":
/*!******************************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/utils/template-registry-helpers.js ***!
  \******************************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.createGetInitialState = createGetInitialState;
function createGetInitialState(exportGroup, additionalProps = {}) {
  return (data, parentInitialState) => {
    let isEnabled = parentInitialState;
    const isImport = data.hasOwnProperty('uploadedData');
    if (isImport) {
      isEnabled = false;
      const templates = data.uploadedData.manifest.templates;
      const exportGroups = elementorAppConfig?.['import-export-customization']?.exportGroups || {};
      for (const templateId in templates) {
        const template = templates[templateId];
        const templateExportGroup = exportGroups[template.doc_type];
        if (templateExportGroup === exportGroup) {
          isEnabled = true;
          break;
        }
      }
    }
    return {
      enabled: isEnabled,
      ...additionalProps
    };
  };
}

/***/ }),

/***/ "../assets/dev/js/editor/utils/is-instanceof.js":
/*!******************************************************!*\
  !*** ../assets/dev/js/editor/utils/is-instanceof.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
/**
 * Some FileAPI objects such as FileList, DataTransferItem and DataTransferItemList has inconsistency with the retrieved
 * object (from events, etc.) and the actual JavaScript object so a regular instanceof doesn't work. This function can
 * check whether it's instanceof by using the objects constructor and prototype names.
 *
 * @param  object
 * @param  constructors
 * @return {boolean}
 */
var _default = (object, constructors) => {
  constructors = Array.isArray(constructors) ? constructors : [constructors];
  for (const constructor of constructors) {
    if (object.constructor.name === constructor.prototype[Symbol.toStringTag]) {
      return true;
    }
  }
  return false;
};
exports["default"] = _default;

/***/ }),

/***/ "../assets/dev/js/frontend/document.js":
/*!*********************************************!*\
  !*** ../assets/dev/js/frontend/document.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
class _default extends elementorModules.ViewModule {
  getDefaultSettings() {
    return {
      selectors: {
        elements: '.elementor-element',
        nestedDocumentElements: '.elementor .elementor-element'
      },
      classes: {
        editMode: 'elementor-edit-mode'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $elements: this.$element.find(selectors.elements).not(this.$element.find(selectors.nestedDocumentElements))
    };
  }
  getDocumentSettings(setting) {
    let elementSettings;
    if (this.isEdit) {
      elementSettings = {};
      const settings = elementor.settings.page.model;
      jQuery.each(settings.getActiveControls(), controlKey => {
        elementSettings[controlKey] = settings.attributes[controlKey];
      });
    } else {
      elementSettings = this.$element.data('elementor-settings') || {};
    }
    return this.getItems(elementSettings, setting);
  }
  runElementsHandlers() {
    this.elements.$elements.each((index, element) => setTimeout(() => elementorFrontend.elementsHandler.runReadyTrigger(element)));
  }
  onInit() {
    this.$element = this.getSettings('$element');
    super.onInit();
    this.isEdit = this.$element.hasClass(this.getSettings('classes.editMode'));
    if (this.isEdit) {
      elementor.on('document:loaded', () => {
        elementor.settings.page.model.on('change', this.onSettingsChange.bind(this));
      });
    } else {
      this.runElementsHandlers();
    }
  }
  onSettingsChange() {}
}
exports["default"] = _default;

/***/ }),

/***/ "../assets/dev/js/frontend/handlers/base-carousel.js":
/*!***********************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/base-carousel.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
var _baseSwiper = _interopRequireDefault(__webpack_require__(/*! ./base-swiper */ "../assets/dev/js/frontend/handlers/base-swiper.js"));
class CarouselHandlerBase extends _baseSwiper.default {
  getDefaultSettings() {
    return {
      selectors: {
        carousel: '.swiper',
        swiperWrapper: '.swiper-wrapper',
        slideContent: '.swiper-slide',
        swiperArrow: '.elementor-swiper-button',
        paginationWrapper: '.swiper-pagination',
        paginationBullet: '.swiper-pagination-bullet',
        paginationBulletWrapper: '.swiper-pagination-bullets'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors'),
      elements = {
        $swiperContainer: this.$element.find(selectors.carousel),
        $swiperWrapper: this.$element.find(selectors.swiperWrapper),
        $swiperArrows: this.$element.find(selectors.swiperArrow),
        $paginationWrapper: this.$element.find(selectors.paginationWrapper),
        $paginationBullets: this.$element.find(selectors.paginationBullet),
        $paginationBulletWrapper: this.$element.find(selectors.paginationBulletWrapper)
      };
    elements.$slides = elements.$swiperContainer.find(selectors.slideContent);
    return elements;
  }
  getSwiperSettings() {
    const elementSettings = this.getElementSettings(),
      slidesToShow = +elementSettings.slides_to_show || 3,
      isSingleSlide = 1 === slidesToShow,
      elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints,
      defaultSlidesToShowMap = {
        mobile: 1,
        tablet: isSingleSlide ? 1 : 2
      };
    const swiperOptions = {
      slidesPerView: slidesToShow,
      loop: 'yes' === elementSettings.infinite,
      speed: elementSettings.speed,
      handleElementorBreakpoints: true
    };
    swiperOptions.breakpoints = {};
    let lastBreakpointSlidesToShowValue = slidesToShow;
    Object.keys(elementorBreakpoints).reverse().forEach(breakpointName => {
      // Tablet has a specific default `slides_to_show`.
      const defaultSlidesToShow = defaultSlidesToShowMap[breakpointName] ? defaultSlidesToShowMap[breakpointName] : lastBreakpointSlidesToShowValue;
      swiperOptions.breakpoints[elementorBreakpoints[breakpointName].value] = {
        slidesPerView: +elementSettings['slides_to_show_' + breakpointName] || defaultSlidesToShow,
        slidesPerGroup: +elementSettings['slides_to_scroll_' + breakpointName] || 1
      };
      if (elementSettings.image_spacing_custom) {
        swiperOptions.breakpoints[elementorBreakpoints[breakpointName].value].spaceBetween = this.getSpaceBetween(breakpointName);
      }
      lastBreakpointSlidesToShowValue = +elementSettings['slides_to_show_' + breakpointName] || defaultSlidesToShow;
    });
    if ('yes' === elementSettings.autoplay) {
      swiperOptions.autoplay = {
        delay: elementSettings.autoplay_speed,
        disableOnInteraction: 'yes' === elementSettings.pause_on_interaction
      };
    }
    if (isSingleSlide) {
      swiperOptions.effect = elementSettings.effect;
      if ('fade' === elementSettings.effect) {
        swiperOptions.fadeEffect = {
          crossFade: true
        };
      }
    } else {
      swiperOptions.slidesPerGroup = +elementSettings.slides_to_scroll || 1;
    }
    if (elementSettings.image_spacing_custom) {
      swiperOptions.spaceBetween = this.getSpaceBetween();
    }
    const showArrows = 'arrows' === elementSettings.navigation || 'both' === elementSettings.navigation,
      showPagination = 'dots' === elementSettings.navigation || 'both' === elementSettings.navigation || elementSettings.pagination;
    if (showArrows) {
      swiperOptions.navigation = {
        prevEl: '.elementor-swiper-button-prev',
        nextEl: '.elementor-swiper-button-next'
      };
    }
    if (showPagination) {
      swiperOptions.pagination = {
        el: `.elementor-element-${this.getID()} .swiper-pagination`,
        type: !!elementSettings.pagination ? elementSettings.pagination : 'bullets',
        clickable: true,
        renderBullet: (index, classname) => {
          return `<span class="${classname}" role="button" tabindex="0" data-bullet-index="${index}" aria-label="${elementorFrontend.config.i18n.a11yCarouselPaginationBulletMessage} ${index + 1}"></span>`;
        }
      };
    }
    if ('yes' === elementSettings.lazyload) {
      swiperOptions.lazy = {
        loadPrevNext: true,
        loadPrevNextAmount: 1
      };
    }
    swiperOptions.a11y = {
      enabled: true,
      prevSlideMessage: elementorFrontend.config.i18n.a11yCarouselPrevSlideMessage,
      nextSlideMessage: elementorFrontend.config.i18n.a11yCarouselNextSlideMessage,
      firstSlideMessage: elementorFrontend.config.i18n.a11yCarouselFirstSlideMessage,
      lastSlideMessage: elementorFrontend.config.i18n.a11yCarouselLastSlideMessage
    };
    swiperOptions.on = {
      slideChange: () => {
        this.a11ySetPaginationTabindex();
        this.handleElementHandlers();
        this.a11ySetSlideAriaHidden();
      },
      init: () => {
        this.a11ySetPaginationTabindex();
        this.a11ySetSlideAriaHidden('initialisation');
      }
    };
    this.applyOffsetSettings(elementSettings, swiperOptions, slidesToShow);
    return swiperOptions;
  }
  getOffsetWidth() {
    const currentDevice = elementorFrontend.getCurrentDeviceMode();
    return elementorFrontend.utils.controls.getResponsiveControlValue(this.getElementSettings(), 'offset_width', 'size', currentDevice) || 0;
  }
  applyOffsetSettings(elementSettings, swiperOptions, slidesToShow) {
    const offsetSide = elementSettings.offset_sides,
      isNestedCarouselInEditMode = elementorFrontend.isEditMode() && 'NestedCarousel' === this.constructor.name;
    if (isNestedCarouselInEditMode || !offsetSide || 'none' === offsetSide) {
      return;
    }
    switch (offsetSide) {
      case 'right':
        this.forceSliderToShowNextSlideWhenOnLast(swiperOptions, slidesToShow);
        this.addClassToSwiperContainer('offset-right');
        break;
      case 'left':
        this.addClassToSwiperContainer('offset-left');
        break;
      case 'both':
        this.forceSliderToShowNextSlideWhenOnLast(swiperOptions, slidesToShow);
        this.addClassToSwiperContainer('offset-both');
        break;
    }
  }
  forceSliderToShowNextSlideWhenOnLast(swiperOptions, slidesToShow) {
    swiperOptions.slidesPerView = slidesToShow + 0.001;
  }
  addClassToSwiperContainer(className) {
    this.getDefaultElements().$swiperContainer[0].classList.add(className);
  }
  async onInit(...args) {
    super.onInit(...args);
    if (!this.elements.$swiperContainer.length || 2 > this.elements.$slides.length) {
      return;
    }
    await this.initSwiper();
    const elementSettings = this.getElementSettings();
    if ('yes' === elementSettings.pause_on_hover) {
      this.togglePauseOnHover(true);
    }
  }
  async initSwiper() {
    const Swiper = elementorFrontend.utils.swiper;
    this.swiper = await new Swiper(this.elements.$swiperContainer, this.getSwiperSettings());

    // Expose the swiper instance in the frontend
    this.elements.$swiperContainer.data('swiper', this.swiper);
  }
  bindEvents() {
    this.elements.$swiperArrows.on('keydown', this.onDirectionArrowKeydown.bind(this));
    this.elements.$paginationWrapper.on('keydown', '.swiper-pagination-bullet', this.onDirectionArrowKeydown.bind(this));
    this.elements.$swiperContainer.on('keydown', '.swiper-slide', this.onDirectionArrowKeydown.bind(this));
    this.$element.find(':focusable').on('focus', this.onFocusDisableAutoplay.bind(this));
    elementorFrontend.elements.$window.on('resize', this.getSwiperSettings.bind(this));
  }
  unbindEvents() {
    this.elements.$swiperArrows.off();
    this.elements.$paginationWrapper.off();
    this.elements.$swiperContainer.off();
    this.$element.find(':focusable').off();
    elementorFrontend.elements.$window.off('resize');
  }
  onDirectionArrowKeydown(event) {
    const isRTL = elementorFrontend.config.is_rtl,
      inlineDirectionArrows = ['ArrowLeft', 'ArrowRight'],
      currentKeydown = event.originalEvent.code,
      isDirectionInlineKeydown = -1 !== inlineDirectionArrows.indexOf(currentKeydown),
      directionStart = isRTL ? 'ArrowRight' : 'ArrowLeft',
      directionEnd = isRTL ? 'ArrowLeft' : 'ArrowRight';
    if (!isDirectionInlineKeydown) {
      return true;
    } else if (directionStart === currentKeydown) {
      this.swiper.slidePrev();
    } else if (directionEnd === currentKeydown) {
      this.swiper.slideNext();
    }
  }
  onFocusDisableAutoplay() {
    this.swiper.autoplay.stop();
  }
  updateSwiperOption(propertyName) {
    const elementSettings = this.getElementSettings(),
      newSettingValue = elementSettings[propertyName],
      params = this.swiper.params;

    // Handle special cases where the value to update is not the value that the Swiper library accepts.
    switch (propertyName) {
      case 'autoplay_speed':
        params.autoplay.delay = newSettingValue;
        break;
      case 'speed':
        params.speed = newSettingValue;
        break;
    }
    this.swiper.update();
  }
  getChangeableProperties() {
    return {
      pause_on_hover: 'pauseOnHover',
      autoplay_speed: 'delay',
      speed: 'speed',
      arrows_position: 'arrows_position' // Not a Swiper setting.
    };
  }
  onElementChange(propertyName) {
    if (0 === propertyName.indexOf('image_spacing_custom')) {
      this.updateSpaceBetween(propertyName);
      return;
    }
    const changeableProperties = this.getChangeableProperties();
    if (changeableProperties[propertyName]) {
      // 'pause_on_hover' is implemented by the handler with event listeners, not the Swiper library.
      if ('pause_on_hover' === propertyName) {
        const newSettingValue = this.getElementSettings('pause_on_hover');
        this.togglePauseOnHover('yes' === newSettingValue);
      } else {
        this.updateSwiperOption(propertyName);
      }
    }
  }
  onEditSettingsChange(propertyName) {
    if ('activeItemIndex' === propertyName) {
      this.swiper.slideToLoop(this.getEditSettings('activeItemIndex') - 1);
    }
  }
  getSpaceBetween(device = null) {
    const responsiveControlValue = elementorFrontend.utils.controls.getResponsiveControlValue(this.getElementSettings(), 'image_spacing_custom', 'size', device);
    return Number(responsiveControlValue) || 0;
  }
  updateSpaceBetween(propertyName) {
    const deviceMatch = propertyName.match('image_spacing_custom_(.*)'),
      device = deviceMatch ? deviceMatch[1] : 'desktop',
      newSpaceBetween = this.getSpaceBetween(device);
    if ('desktop' !== device) {
      this.swiper.params.breakpoints[elementorFrontend.config.responsive.activeBreakpoints[device].value].spaceBetween = newSpaceBetween;
    }
    this.swiper.params.spaceBetween = newSpaceBetween;
    this.swiper.update();
  }
  getPaginationBullets(type = 'array') {
    const paginationBullets = this.$element.find(this.getSettings('selectors').paginationBullet);
    return 'array' === type ? Array.from(paginationBullets) : paginationBullets;
  }
  a11ySetPaginationTabindex() {
    const bulletClass = this.swiper?.params?.pagination.bulletClass,
      activeBulletClass = this.swiper?.params?.pagination.bulletActiveClass;
    this.getPaginationBullets().forEach(bullet => {
      if (!bullet.classList?.contains(activeBulletClass)) {
        bullet.removeAttribute('tabindex');
      }
    });
    const isDirectionInlineArrowKey = 'ArrowLeft' === event?.code || 'ArrowRight' === event?.code;
    if (event?.target?.classList?.contains(bulletClass) && isDirectionInlineArrowKey) {
      this.$element.find(`.${activeBulletClass}`).trigger('focus');
    }
  }
  getSwiperWrapperTranformXValue() {
    let transformValue = this.elements.$swiperWrapper[0]?.style.transform;
    transformValue = transformValue.replace('translate3d(', '');
    transformValue = transformValue.split(',');
    transformValue = parseInt(transformValue[0].replace('px', ''));
    return !!transformValue ? transformValue : 0;
  }
  a11ySetSlideAriaHidden(status = '') {
    const currentIndex = 'initialisation' === status ? 0 : this.swiper?.activeIndex;
    if ('number' !== typeof currentIndex) {
      return;
    }
    const swiperWrapperTransformXValue = this.getSwiperWrapperTranformXValue(),
      swiperWrapperWidth = this.elements.$swiperWrapper[0].clientWidth,
      $slides = this.elements.$swiperContainer.find(this.getSettings('selectors').slideContent);
    $slides.each((index, slide) => {
      const isSlideInsideWrapper = 0 <= slide.offsetLeft + swiperWrapperTransformXValue && swiperWrapperWidth > slide.offsetLeft + swiperWrapperTransformXValue;
      if (!isSlideInsideWrapper) {
        slide.setAttribute('aria-hidden', true);
        slide.setAttribute('inert', '');
      } else {
        slide.removeAttribute('aria-hidden');
        slide.removeAttribute('inert');
      }
    });
  }

  // Empty method which can be overwritten by child methods.
  handleElementHandlers() {}
}
exports["default"] = CarouselHandlerBase;

/***/ }),

/***/ "../assets/dev/js/frontend/handlers/base-swiper.js":
/*!*********************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/base-swiper.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../assets/dev/js/frontend/handlers/base.js"));
class SwiperHandlerBase extends _base.default {
  getInitialSlide() {
    const editSettings = this.getEditSettings();
    return editSettings.activeItemIndex ? editSettings.activeItemIndex - 1 : 0;
  }
  getSlidesCount() {
    return this.elements.$slides.length;
  }

  // This method live-handles the 'Pause On Hover' control's value being changed in the Editor Panel
  togglePauseOnHover(toggleOn) {
    if (toggleOn) {
      this.elements.$swiperContainer.on({
        mouseenter: () => {
          this.swiper.autoplay.stop();
        },
        mouseleave: () => {
          this.swiper.autoplay.start();
        }
      });
    } else {
      this.elements.$swiperContainer.off('mouseenter mouseleave');
    }
  }
  handleKenBurns() {
    const settings = this.getSettings();
    if (this.$activeImageBg) {
      this.$activeImageBg.removeClass(settings.classes.kenBurnsActive);
    }
    this.activeItemIndex = this.swiper ? this.swiper.activeIndex : this.getInitialSlide();
    if (this.swiper) {
      this.$activeImageBg = jQuery(this.swiper.slides[this.activeItemIndex]).children('.' + settings.classes.slideBackground);
    } else {
      this.$activeImageBg = jQuery(this.elements.$slides[0]).children('.' + settings.classes.slideBackground);
    }
    this.$activeImageBg.addClass(settings.classes.kenBurnsActive);
  }
}
exports["default"] = SwiperHandlerBase;

/***/ }),

/***/ "../assets/dev/js/frontend/handlers/base.js":
/*!**************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/base.js ***!
  \**************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "../node_modules/core-js/modules/esnext.iterator.filter.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
module.exports = elementorModules.ViewModule.extend({
  $element: null,
  editorListeners: null,
  onElementChange: null,
  onEditSettingsChange: null,
  onPageSettingsChange: null,
  isEdit: null,
  __construct(settings) {
    if (!this.isActive(settings)) {
      return;
    }
    this.$element = settings.$element;
    this.isEdit = this.$element.hasClass('elementor-element-edit-mode');
    if (this.isEdit) {
      this.addEditorListeners();
    }
  },
  isActive() {
    return true;
  },
  isElementInTheCurrentDocument() {
    if (!elementorFrontend.isEditMode()) {
      return false;
    }
    return elementor.documents.currentDocument.id.toString() === this.$element[0].closest('.elementor').dataset.elementorId;
  },
  findElement(selector) {
    var $mainElement = this.$element;
    return $mainElement.find(selector).filter(function () {
      // Start `closest` from parent since self can be `.elementor-element`.
      return jQuery(this).parent().closest('.elementor-element').is($mainElement);
    });
  },
  getUniqueHandlerID(cid, $element) {
    if (!cid) {
      cid = this.getModelCID();
    }
    if (!$element) {
      $element = this.$element;
    }
    return cid + $element.attr('data-element_type') + this.getConstructorID();
  },
  initEditorListeners() {
    var self = this;
    self.editorListeners = [{
      event: 'element:destroy',
      to: elementor.channels.data,
      callback(removedModel) {
        if (removedModel.cid !== self.getModelCID()) {
          return;
        }
        self.onDestroy();
      }
    }];
    if (self.onElementChange) {
      const elementType = self.getWidgetType() || self.getElementType();
      let eventName = 'change';
      if ('global' !== elementType) {
        eventName += ':' + elementType;
      }
      self.editorListeners.push({
        event: eventName,
        to: elementor.channels.editor,
        callback(controlView, elementView) {
          var elementViewHandlerID = self.getUniqueHandlerID(elementView.model.cid, elementView.$el);
          if (elementViewHandlerID !== self.getUniqueHandlerID()) {
            return;
          }
          self.onElementChange(controlView.model.get('name'), controlView, elementView);
        }
      });
    }
    if (self.onEditSettingsChange) {
      self.editorListeners.push({
        event: 'change:editSettings',
        to: elementor.channels.editor,
        callback(changedModel, view) {
          if (view.model.cid !== self.getModelCID()) {
            return;
          }
          const propName = Object.keys(changedModel.changed)[0];
          self.onEditSettingsChange(propName, changedModel.changed[propName]);
        }
      });
    }
    ['page'].forEach(function (settingsType) {
      var listenerMethodName = 'on' + settingsType[0].toUpperCase() + settingsType.slice(1) + 'SettingsChange';
      if (self[listenerMethodName]) {
        self.editorListeners.push({
          event: 'change',
          to: elementor.settings[settingsType].model,
          callback(model) {
            self[listenerMethodName](model.changed);
          }
        });
      }
    });
  },
  getEditorListeners() {
    if (!this.editorListeners) {
      this.initEditorListeners();
    }
    return this.editorListeners;
  },
  addEditorListeners() {
    var uniqueHandlerID = this.getUniqueHandlerID();
    this.getEditorListeners().forEach(function (listener) {
      elementorFrontend.addListenerOnce(uniqueHandlerID, listener.event, listener.callback, listener.to);
    });
  },
  removeEditorListeners() {
    var uniqueHandlerID = this.getUniqueHandlerID();
    this.getEditorListeners().forEach(function (listener) {
      elementorFrontend.removeListeners(uniqueHandlerID, listener.event, null, listener.to);
    });
  },
  getElementType() {
    return this.$element.data('element_type');
  },
  getWidgetType() {
    const widgetType = this.$element.data('widget_type');
    if (!widgetType) {
      return;
    }
    return widgetType.split('.')[0];
  },
  getID() {
    return this.$element.data('id');
  },
  getModelCID() {
    return this.$element.data('model-cid');
  },
  getElementSettings(setting) {
    let elementSettings = {};
    const modelCID = this.getModelCID();
    if (this.isEdit && modelCID) {
      const settings = elementorFrontend.config.elements.data[modelCID],
        attributes = settings.attributes;
      let type = attributes.widgetType || attributes.elType;
      if (attributes.isInner) {
        type = 'inner-' + type;
      }
      let settingsKeys = elementorFrontend.config.elements.keys[type];
      if (!settingsKeys) {
        settingsKeys = elementorFrontend.config.elements.keys[type] = [];
        jQuery.each(settings.controls, (name, control) => {
          if (control.frontend_available || control.editor_available) {
            settingsKeys.push(name);
          }
        });
      }
      jQuery.each(settings.getActiveControls(), function (controlKey) {
        if (-1 !== settingsKeys.indexOf(controlKey)) {
          let value = attributes[controlKey];
          if (value.toJSON) {
            value = value.toJSON();
          }
          elementSettings[controlKey] = value;
        }
      });
    } else {
      elementSettings = this.$element.data('settings') || {};
    }
    return this.getItems(elementSettings, setting);
  },
  getEditSettings(setting) {
    var attributes = {};
    if (this.isEdit) {
      attributes = elementorFrontend.config.elements.editSettings[this.getModelCID()].attributes;
    }
    return this.getItems(attributes, setting);
  },
  getCurrentDeviceSetting(settingKey) {
    return elementorFrontend.getCurrentDeviceSetting(this.getElementSettings(), settingKey);
  },
  onInit() {
    if (this.isActive(this.getSettings())) {
      elementorModules.ViewModule.prototype.onInit.apply(this, arguments);
    }
  },
  onDestroy() {
    if (this.isEdit) {
      this.removeEditorListeners();
    }
    if (this.unbindEvents) {
      this.unbindEvents();
    }
  }
});

/***/ }),

/***/ "../assets/dev/js/frontend/handlers/stretched-element.js":
/*!***************************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/stretched-element.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.find.js */ "../node_modules/core-js/modules/esnext.iterator.find.js");
var _base = _interopRequireDefault(__webpack_require__(/*! ./base */ "../assets/dev/js/frontend/handlers/base.js"));
class StretchedElement extends _base.default {
  getStretchedClass() {
    return 'e-stretched';
  }
  getStretchSettingName() {
    return 'stretch_element';
  }
  getStretchActiveValue() {
    return 'yes';
  }
  bindEvents() {
    const handlerID = this.getUniqueHandlerID();
    elementorFrontend.addListenerOnce(handlerID, 'resize', this.stretch);
    elementorFrontend.addListenerOnce(handlerID, 'sticky:stick', this.stretch, this.$element);
    elementorFrontend.addListenerOnce(handlerID, 'sticky:unstick', this.stretch, this.$element);
    if (elementorFrontend.isEditMode()) {
      this.onKitChangeStretchContainerChange = this.onKitChangeStretchContainerChange.bind(this);
      elementor.channels.editor.on('kit:change:stretchContainer', this.onKitChangeStretchContainerChange);
    }
  }
  unbindEvents() {
    elementorFrontend.removeListeners(this.getUniqueHandlerID(), 'resize', this.stretch);
    if (elementorFrontend.isEditMode()) {
      elementor.channels.editor.off('kit:change:stretchContainer', this.onKitChangeStretchContainerChange);
    }
  }
  isActive(settings) {
    return elementorFrontend.isEditMode() || settings.$element.hasClass(this.getStretchedClass());
  }
  getStretchElementForConfig(childSelector = null) {
    if (childSelector) {
      return this.$element.find(childSelector);
    }
    return this.$element;
  }
  getStretchElementConfig() {
    return {
      element: this.getStretchElementForConfig(),
      selectors: {
        container: this.getStretchContainer()
      },
      considerScrollbar: elementorFrontend.isEditMode() && elementorFrontend.config.is_rtl
    };
  }
  initStretch() {
    this.stretch = this.stretch.bind(this);
    this.stretchElement = new elementorModules.frontend.tools.StretchElement(this.getStretchElementConfig());
  }
  getStretchContainer() {
    return elementorFrontend.getKitSettings('stretched_section_container') || window;
  }
  isStretchSettingEnabled() {
    return this.getElementSettings(this.getStretchSettingName()) === this.getStretchActiveValue();
  }
  stretch() {
    if (!this.isStretchSettingEnabled()) {
      return;
    }
    this.stretchElement.stretch();
  }
  onInit(...args) {
    if (!this.isActive(this.getSettings())) {
      return;
    }
    this.initStretch();
    super.onInit(...args);
    this.stretch();
  }
  onElementChange(propertyName) {
    const stretchSettingName = this.getStretchSettingName();
    if (stretchSettingName === propertyName) {
      if (this.isStretchSettingEnabled()) {
        this.stretch();
      } else {
        this.stretchElement.reset();
      }
    }
  }
  onKitChangeStretchContainerChange() {
    this.stretchElement.setSettings('selectors.container', this.getStretchContainer());
    this.stretch();
  }
}
exports["default"] = StretchedElement;

/***/ }),

/***/ "../assets/dev/js/frontend/modules.js":
/*!********************************************!*\
  !*** ../assets/dev/js/frontend/modules.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _modules = _interopRequireDefault(__webpack_require__(/*! ../modules/modules */ "../assets/dev/js/modules/modules.js"));
var _document = _interopRequireDefault(__webpack_require__(/*! ./document */ "../assets/dev/js/frontend/document.js"));
var _stretchElement = _interopRequireDefault(__webpack_require__(/*! ./tools/stretch-element */ "../assets/dev/js/frontend/tools/stretch-element.js"));
var _stretchedElement = _interopRequireDefault(__webpack_require__(/*! ./handlers/stretched-element */ "../assets/dev/js/frontend/handlers/stretched-element.js"));
var _base = _interopRequireDefault(__webpack_require__(/*! ./handlers/base */ "../assets/dev/js/frontend/handlers/base.js"));
var _baseSwiper = _interopRequireDefault(__webpack_require__(/*! ./handlers/base-swiper */ "../assets/dev/js/frontend/handlers/base-swiper.js"));
var _baseCarousel = _interopRequireDefault(__webpack_require__(/*! ./handlers/base-carousel */ "../assets/dev/js/frontend/handlers/base-carousel.js"));
_modules.default.frontend = {
  Document: _document.default,
  tools: {
    StretchElement: _stretchElement.default
  },
  handlers: {
    Base: _base.default,
    StretchedElement: _stretchedElement.default,
    SwiperBase: _baseSwiper.default,
    CarouselBase: _baseCarousel.default
  }
};

/***/ }),

/***/ "../assets/dev/js/frontend/tools/stretch-element.js":
/*!**********************************************************!*\
  !*** ../assets/dev/js/frontend/tools/stretch-element.js ***!
  \**********************************************************/
/***/ ((module) => {

"use strict";


module.exports = elementorModules.ViewModule.extend({
  getDefaultSettings() {
    return {
      element: null,
      direction: elementorFrontend.config.is_rtl ? 'right' : 'left',
      selectors: {
        container: window
      },
      considerScrollbar: false,
      cssOutput: 'inline'
    };
  },
  getDefaultElements() {
    return {
      $element: jQuery(this.getSettings('element'))
    };
  },
  stretch() {
    const settings = this.getSettings();
    let $container;
    try {
      $container = jQuery(settings.selectors.container);
      // eslint-disable-next-line no-empty
    } catch (e) {}
    if (!$container || !$container.length) {
      $container = jQuery(this.getDefaultSettings().selectors.container);
    }
    this.reset();
    var $element = this.elements.$element,
      containerWidth = $container.innerWidth(),
      elementOffset = $element.offset().left,
      isFixed = 'fixed' === $element.css('position'),
      correctOffset = isFixed ? 0 : elementOffset,
      isContainerFullScreen = window === $container[0];
    if (!isContainerFullScreen) {
      var containerOffset = $container.offset().left;
      if (isFixed) {
        correctOffset = containerOffset;
      }
      if (elementOffset > containerOffset) {
        correctOffset = elementOffset - containerOffset;
      }
    }
    if (settings.considerScrollbar && isContainerFullScreen) {
      const scrollbarWidth = window.innerWidth - containerWidth;
      correctOffset -= scrollbarWidth;
    }
    if (!isFixed) {
      if (elementorFrontend.config.is_rtl) {
        correctOffset = containerWidth - ($element.outerWidth() + correctOffset);
      }
      correctOffset = -correctOffset;
    }

    // Consider margin
    if (settings.margin) {
      correctOffset += settings.margin;
    }
    var css = {};
    let width = containerWidth;
    if (settings.margin) {
      width -= settings.margin * 2;
    }
    css.width = width + 'px';
    css[settings.direction] = correctOffset + 'px';
    if ('variables' === settings.cssOutput) {
      this.applyCssVariables($element, css);
      return;
    }
    $element.css(css);
  },
  reset() {
    const css = {},
      settings = this.getSettings(),
      $element = this.elements.$element;
    if ('variables' === settings.cssOutput) {
      this.resetCssVariables($element);
      return;
    }
    css.width = '';
    css[settings.direction] = '';
    $element.css(css);
  },
  applyCssVariables($element, css) {
    $element.css('--stretch-width', css.width);
    if (!!css.left) {
      $element.css('--stretch-left', css.left);
    } else {
      $element.css('--stretch-right', css.right);
    }
  },
  resetCssVariables($element) {
    $element.css({
      '--stretch-width': '',
      '--stretch-left': '',
      '--stretch-right': ''
    });
  }
});

/***/ }),

/***/ "../assets/dev/js/modules/imports/args-object.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/modules/imports/args-object.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _instanceType = _interopRequireDefault(__webpack_require__(/*! ./instance-type */ "../assets/dev/js/modules/imports/instance-type.js"));
var _isInstanceof = _interopRequireDefault(__webpack_require__(/*! ../../editor/utils/is-instanceof */ "../assets/dev/js/editor/utils/is-instanceof.js"));
class ArgsObject extends _instanceType.default {
  static getInstanceType() {
    return 'ArgsObject';
  }

  /**
   * Function constructor().
   *
   * Create ArgsObject.
   *
   * @param {{}} args
   */
  constructor(args) {
    super();
    this.args = args;
  }

  /**
   * Function requireArgument().
   *
   * Validate property in args.
   *
   * @param {string} property
   * @param {{}}     args
   *
   * @throws {Error}
   */
  requireArgument(property, args = this.args) {
    if (!Object.prototype.hasOwnProperty.call(args, property)) {
      throw Error(`${property} is required.`);
    }
  }

  /**
   * Function requireArgumentType().
   *
   * Validate property in args using `type === typeof(args.whatever)`.
   *
   * @param {string} property
   * @param {string} type
   * @param {{}}     args
   *
   * @throws {Error}
   */
  requireArgumentType(property, type, args = this.args) {
    this.requireArgument(property, args);
    if (typeof args[property] !== type) {
      throw Error(`${property} invalid type: ${type}.`);
    }
  }

  /**
   * Function requireArgumentInstance().
   *
   * Validate property in args using `args.whatever instanceof instance`.
   *
   * @param {string} property
   * @param {*}      instance
   * @param {{}}     args
   *
   * @throws {Error}
   */
  requireArgumentInstance(property, instance, args = this.args) {
    this.requireArgument(property, args);
    if (!(args[property] instanceof instance) && !(0, _isInstanceof.default)(args[property], instance)) {
      throw Error(`${property} invalid instance.`);
    }
  }

  /**
   * Function requireArgumentConstructor().
   *
   * Validate property in args using `type === args.whatever.constructor`.
   *
   * @param {string} property
   * @param {*}      type
   * @param {{}}     args
   *
   * @throws {Error}
   */
  requireArgumentConstructor(property, type, args = this.args) {
    this.requireArgument(property, args);

    // Note: Converting the constructor to string in order to avoid equation issues
    // due to different memory addresses between iframes (window.Object !== window.top.Object).
    if (args[property].constructor.toString() !== type.prototype.constructor.toString()) {
      throw Error(`${property} invalid constructor type.`);
    }
  }
}
exports["default"] = ArgsObject;

/***/ }),

/***/ "../assets/dev/js/modules/imports/force-method-implementation.js":
/*!***********************************************************************!*\
  !*** ../assets/dev/js/modules/imports/force-method-implementation.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.ForceMethodImplementation = void 0;
// TODO: Wrong location used as `elementorModules.ForceMethodImplementation(); should be` `elementorUtils.forceMethodImplementation()`;

class ForceMethodImplementation extends Error {
  constructor(info = {}, args = {}) {
    super(`${info.isStatic ? 'static ' : ''}${info.fullName}() should be implemented, please provide '${info.functionName || info.fullName}' functionality.`, args);

    // Allow to pass custom properties to the error.
    if (Object.keys(args).length) {
      // eslint-disable-next-line no-console
      console.error(args);
    }
    Error.captureStackTrace(this, ForceMethodImplementation);
  }
}
exports.ForceMethodImplementation = ForceMethodImplementation;
var _default = args => {
  const stack = Error().stack,
    caller = stack.split('\n')[2].trim(),
    callerName = caller.startsWith('at new') ? 'constructor' : caller.split(' ')[1],
    info = {};
  info.functionName = callerName;
  info.fullName = callerName;
  if (info.functionName.includes('.')) {
    const parts = info.functionName.split('.');
    info.className = parts[0];
    info.functionName = parts[1];
  } else {
    info.isStatic = true;
  }
  throw new ForceMethodImplementation(info, args);
};
exports["default"] = _default;

/***/ }),

/***/ "../assets/dev/js/modules/imports/instance-type.js":
/*!*********************************************************!*\
  !*** ../assets/dev/js/modules/imports/instance-type.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
class InstanceType {
  static [Symbol.hasInstance](target) {
    /**
     * This is function extending being called each time JS uses instanceOf, since babel use it each time it create new class
     * its give's opportunity to mange capabilities of instanceOf operator.
     * saving current class each time will give option later to handle instanceOf manually.
     */
    let result = super[Symbol.hasInstance](target);

    // Act normal when validate a class, which does not have instance type.
    if (target && !target.constructor.getInstanceType) {
      return result;
    }
    if (target) {
      if (!target.instanceTypes) {
        target.instanceTypes = [];
      }
      if (!result) {
        if (this.getInstanceType() === target.constructor.getInstanceType()) {
          result = true;
        }
      }
      if (result) {
        const name = this.getInstanceType === InstanceType.getInstanceType ? 'BaseInstanceType' : this.getInstanceType();
        if (-1 === target.instanceTypes.indexOf(name)) {
          target.instanceTypes.push(name);
        }
      }
    }
    if (!result && target) {
      // Check if the given 'target', is instance of known types.
      result = target.instanceTypes && Array.isArray(target.instanceTypes) && -1 !== target.instanceTypes.indexOf(this.getInstanceType());
    }
    return result;
  }
  static getInstanceType() {
    elementorModules.ForceMethodImplementation();
  }
  constructor() {
    // Since anonymous classes sometimes do not get validated by babel, do it manually.
    let target = new.target;
    const prototypes = [];
    while (target.__proto__ && target.__proto__.name) {
      prototypes.push(target.__proto__);
      target = target.__proto__;
    }
    prototypes.reverse().forEach(proto => this instanceof proto);
  }
}
exports["default"] = InstanceType;

/***/ }),

/***/ "../assets/dev/js/modules/imports/module.js":
/*!**************************************************!*\
  !*** ../assets/dev/js/modules/imports/module.js ***!
  \**************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "../node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.filter.js */ "../node_modules/core-js/modules/esnext.iterator.filter.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "../node_modules/core-js/modules/esnext.iterator.for-each.js");
const Module = function () {
  const $ = jQuery,
    instanceParams = arguments,
    self = this,
    events = {};
  let settings;
  const ensureClosureMethods = function () {
    $.each(self, function (methodName) {
      const oldMethod = self[methodName];
      if ('function' !== typeof oldMethod) {
        return;
      }
      self[methodName] = function () {
        return oldMethod.apply(self, arguments);
      };
    });
  };
  const initSettings = function () {
    settings = self.getDefaultSettings();
    const instanceSettings = instanceParams[0];
    if (instanceSettings) {
      $.extend(true, settings, instanceSettings);
    }
  };
  const init = function () {
    self.__construct.apply(self, instanceParams);
    ensureClosureMethods();
    initSettings();
    self.trigger('init');
  };
  this.getItems = function (items, itemKey) {
    if (itemKey) {
      const keyStack = itemKey.split('.'),
        currentKey = keyStack.splice(0, 1);
      if (!keyStack.length) {
        return items[currentKey];
      }
      if (!items[currentKey]) {
        return;
      }
      return this.getItems(items[currentKey], keyStack.join('.'));
    }
    return items;
  };
  this.getSettings = function (setting) {
    return this.getItems(settings, setting);
  };
  this.setSettings = function (settingKey, value, settingsContainer) {
    if (!settingsContainer) {
      settingsContainer = settings;
    }
    if ('object' === typeof settingKey) {
      $.extend(settingsContainer, settingKey);
      return self;
    }
    const keyStack = settingKey.split('.'),
      currentKey = keyStack.splice(0, 1);
    if (!keyStack.length) {
      settingsContainer[currentKey] = value;
      return self;
    }
    if (!settingsContainer[currentKey]) {
      settingsContainer[currentKey] = {};
    }
    return self.setSettings(keyStack.join('.'), value, settingsContainer[currentKey]);
  };
  this.getErrorMessage = function (type, functionName) {
    let message;
    switch (type) {
      case 'forceMethodImplementation':
        message = `The method '${functionName}' must to be implemented in the inheritor child.`;
        break;
      default:
        message = 'An error occurs';
    }
    return message;
  };

  // TODO: This function should be deleted ?.
  this.forceMethodImplementation = function (functionName) {
    throw new Error(this.getErrorMessage('forceMethodImplementation', functionName));
  };
  this.on = function (eventName, callback) {
    if ('object' === typeof eventName) {
      $.each(eventName, function (singleEventName) {
        self.on(singleEventName, this);
      });
      return self;
    }
    const eventNames = eventName.split(' ');
    eventNames.forEach(function (singleEventName) {
      if (!events[singleEventName]) {
        events[singleEventName] = [];
      }
      events[singleEventName].push(callback);
    });
    return self;
  };
  this.off = function (eventName, callback) {
    if (!events[eventName]) {
      return self;
    }
    if (!callback) {
      delete events[eventName];
      return self;
    }
    const callbackIndex = events[eventName].indexOf(callback);
    if (-1 !== callbackIndex) {
      delete events[eventName][callbackIndex];

      // Reset array index (for next off on same event).
      events[eventName] = events[eventName].filter(val => val);
    }
    return self;
  };
  this.trigger = function (eventName) {
    const methodName = 'on' + eventName[0].toUpperCase() + eventName.slice(1),
      params = Array.prototype.slice.call(arguments, 1);
    if (self[methodName]) {
      self[methodName].apply(self, params);
    }
    const callbacks = events[eventName];
    if (!callbacks) {
      return self;
    }
    $.each(callbacks, function (index, callback) {
      callback.apply(self, params);
    });
    return self;
  };
  init();
};
Module.prototype.__construct = function () {};
Module.prototype.getDefaultSettings = function () {
  return {};
};
Module.prototype.getConstructorID = function () {
  return this.constructor.name;
};
Module.extend = function (properties) {
  const $ = jQuery,
    parent = this;
  const child = function () {
    return parent.apply(this, arguments);
  };
  $.extend(child, parent);
  child.prototype = Object.create($.extend({}, parent.prototype, properties));
  child.prototype.constructor = child;
  child.__super__ = parent.prototype;
  return child;
};
module.exports = Module;

/***/ }),

/***/ "../assets/dev/js/modules/imports/utils/masonry.js":
/*!*********************************************************!*\
  !*** ../assets/dev/js/modules/imports/utils/masonry.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
var _viewModule = _interopRequireDefault(__webpack_require__(/*! ../view-module */ "../assets/dev/js/modules/imports/view-module.js"));
var _default = exports["default"] = _viewModule.default.extend({
  getDefaultSettings() {
    return {
      container: null,
      items: null,
      columnsCount: 3,
      verticalSpaceBetween: 30
    };
  },
  getDefaultElements() {
    return {
      $container: jQuery(this.getSettings('container')),
      $items: jQuery(this.getSettings('items'))
    };
  },
  run() {
    var heights = [],
      distanceFromTop = this.elements.$container.position().top,
      settings = this.getSettings(),
      columnsCount = settings.columnsCount;
    distanceFromTop += parseInt(this.elements.$container.css('margin-top'), 10);
    this.elements.$items.each(function (index) {
      var row = Math.floor(index / columnsCount),
        $item = jQuery(this),
        itemHeight = $item[0].getBoundingClientRect().height + settings.verticalSpaceBetween;
      if (row) {
        var itemPosition = $item.position(),
          indexAtRow = index % columnsCount,
          pullHeight = itemPosition.top - distanceFromTop - heights[indexAtRow];
        pullHeight -= parseInt($item.css('margin-top'), 10);
        pullHeight *= -1;
        $item.css('margin-top', pullHeight + 'px');
        heights[indexAtRow] += itemHeight;
      } else {
        heights.push(itemHeight);
      }
    });
  }
});

/***/ }),

/***/ "../assets/dev/js/modules/imports/utils/scroll.js":
/*!********************************************************!*\
  !*** ../assets/dev/js/modules/imports/utils/scroll.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
__webpack_require__(/*! core-js/modules/es.array.push.js */ "../node_modules/core-js/modules/es.array.push.js");
// Moved from elementor pro: 'assets/dev/js/frontend/utils'
class Scroll {
  /**
   * @param {Object}      obj
   * @param {number}      obj.sensitivity - Value between 0-100 - Will determine the intersection trigger points on the element
   * @param {Function}    obj.callback    - Will be triggered on each intersection point between the element and the viewport top/bottom
   * @param {string}      obj.offset      - Offset between the element intersection points and the viewport, written like in CSS: '-50% 0 -25%'
   * @param {HTMLElement} obj.root        - The element that the events will be relative to, if 'null' will be relative to the viewport
   */
  static scrollObserver(obj) {
    let lastScrollY = 0;

    // Generating thresholds points along the animation height
    // More thresholds points = more trigger points of the callback
    const buildThresholds = (sensitivityPercentage = 0) => {
      const thresholds = [];
      if (sensitivityPercentage > 0 && sensitivityPercentage <= 100) {
        const increment = 100 / sensitivityPercentage;
        for (let i = 0; i <= 100; i += increment) {
          thresholds.push(i / 100);
        }
      } else {
        thresholds.push(0);
      }
      return thresholds;
    };
    const options = {
      root: obj.root || null,
      rootMargin: obj.offset || '0px',
      threshold: buildThresholds(obj.sensitivity)
    };
    function handleIntersect(entries) {
      const currentScrollY = entries[0].boundingClientRect.y,
        isInViewport = entries[0].isIntersecting,
        intersectionScrollDirection = currentScrollY < lastScrollY ? 'down' : 'up',
        scrollPercentage = Math.abs(parseFloat((entries[0].intersectionRatio * 100).toFixed(2)));
      obj.callback({
        sensitivity: obj.sensitivity,
        isInViewport,
        scrollPercentage,
        intersectionScrollDirection
      });
      lastScrollY = currentScrollY;
    }
    return new IntersectionObserver(handleIntersect, options);
  }

  /**
   * @param {jQuery.Element} $element
   * @param {Object}         offsetObj
   * @param {number}         offsetObj.start - Offset start value in percentages
   * @param {number}         offsetObj.end   - Offset end value in percentages
   */
  static getElementViewportPercentage($element, offsetObj = {}) {
    const elementOffset = $element[0].getBoundingClientRect(),
      offsetStart = offsetObj.start || 0,
      offsetEnd = offsetObj.end || 0,
      windowStartOffset = window.innerHeight * offsetStart / 100,
      windowEndOffset = window.innerHeight * offsetEnd / 100,
      y1 = elementOffset.top - window.innerHeight,
      y2 = elementOffset.top + windowStartOffset + $element.height(),
      startPosition = 0 - y1 + windowStartOffset,
      endPosition = y2 - y1 + windowEndOffset,
      percent = Math.max(0, Math.min(startPosition / endPosition, 1));
    return parseFloat((percent * 100).toFixed(2));
  }

  /**
   * @param {Object} offsetObj
   * @param {number} offsetObj.start - Offset start value in percentages
   * @param {number} offsetObj.end   - Offset end value in percentages
   * @param {number} limitPageHeight - Will limit the page height calculation
   */
  static getPageScrollPercentage(offsetObj = {}, limitPageHeight) {
    const offsetStart = offsetObj.start || 0,
      offsetEnd = offsetObj.end || 0,
      initialPageHeight = limitPageHeight || document.documentElement.scrollHeight - document.documentElement.clientHeight,
      heightOffset = initialPageHeight * offsetStart / 100,
      pageRange = initialPageHeight + heightOffset + initialPageHeight * offsetEnd / 100,
      scrollPos = document.documentElement.scrollTop + document.body.scrollTop + heightOffset;
    return scrollPos / pageRange * 100;
  }
}
exports["default"] = Scroll;

/***/ }),

/***/ "../assets/dev/js/modules/imports/view-module.js":
/*!*******************************************************!*\
  !*** ../assets/dev/js/modules/imports/view-module.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _module = _interopRequireDefault(__webpack_require__(/*! ./module */ "../assets/dev/js/modules/imports/module.js"));
var _default = exports["default"] = _module.default.extend({
  elements: null,
  getDefaultElements() {
    return {};
  },
  bindEvents() {},
  onInit() {
    this.initElements();
    this.bindEvents();
  },
  initElements() {
    this.elements = this.getDefaultElements();
  }
});

/***/ }),

/***/ "../assets/dev/js/modules/modules.js":
/*!*******************************************!*\
  !*** ../assets/dev/js/modules/modules.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _module = _interopRequireDefault(__webpack_require__(/*! ./imports/module */ "../assets/dev/js/modules/imports/module.js"));
var _viewModule = _interopRequireDefault(__webpack_require__(/*! ./imports/view-module */ "../assets/dev/js/modules/imports/view-module.js"));
var _argsObject = _interopRequireDefault(__webpack_require__(/*! ./imports/args-object */ "../assets/dev/js/modules/imports/args-object.js"));
var _masonry = _interopRequireDefault(__webpack_require__(/*! ./imports/utils/masonry */ "../assets/dev/js/modules/imports/utils/masonry.js"));
var _scroll = _interopRequireDefault(__webpack_require__(/*! ./imports/utils/scroll */ "../assets/dev/js/modules/imports/utils/scroll.js"));
var _forceMethodImplementation = _interopRequireDefault(__webpack_require__(/*! ./imports/force-method-implementation */ "../assets/dev/js/modules/imports/force-method-implementation.js"));
var _templateRegistryHelpers = __webpack_require__(/*! ../../../../app/modules/import-export-customization/assets/js/shared/utils/template-registry-helpers */ "../app/modules/import-export-customization/assets/js/shared/utils/template-registry-helpers.js");
var _customizationDialogs = __webpack_require__(/*! ../../../../app/modules/import-export-customization/assets/js/shared/registry/customization-dialogs */ "../app/modules/import-export-customization/assets/js/shared/registry/customization-dialogs.js");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _wpDashboardTracking = _interopRequireDefault(__webpack_require__(/*! elementor-app/event-track/wp-dashboard-tracking */ "../app/assets/js/event-track/wp-dashboard-tracking.js"));
var _default = exports["default"] = window.elementorModules = {
  Module: _module.default,
  ViewModule: _viewModule.default,
  ArgsObject: _argsObject.default,
  ForceMethodImplementation: _forceMethodImplementation.default,
  utils: {
    Masonry: _masonry.default,
    Scroll: _scroll.default
  },
  importExport: {
    createGetInitialState: _templateRegistryHelpers.createGetInitialState,
    customizationDialogsRegistry: _customizationDialogs.customizationDialogsRegistry
  },
  appsEventTracking: {
    AppsEventTracking: _appsEventTracking.AppsEventTracking
  },
  wpDashboardTracking: {
    WpDashboardTracking: _wpDashboardTracking.default
  }
};

/***/ }),

/***/ "../core/common/modules/events-manager/assets/js/events-config.js":
/*!************************************************************************!*\
  !*** ../core/common/modules/events-manager/assets/js/events-config.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
const eventsConfig = {
  triggers: {
    click: 'Click',
    accordionClick: 'Accordion Click',
    toggleClick: 'Toggle Click',
    dropdownClick: 'Click Dropdown',
    editorLoaded: 'Editor Loaded',
    visible: 'Visible',
    pageLoaded: 'Page Loaded'
  },
  locations: {
    widgetPanel: 'Widget Panel',
    topBar: 'Top Bar',
    elementorEditor: 'Elementor Editor',
    templatesLibrary: {
      library: 'Templates Library'
    },
    app: {
      import: 'Import Kit',
      export: 'Export Kit',
      kitLibrary: 'Kit Library',
      cloudKitLibrary: 'Cloud Kit Library'
    },
    variables: 'Variables Panel',
    admin: 'WP admin'
  },
  secondaryLocations: {
    layout: 'Layout Section',
    basic: 'Basic Section',
    'pro-elements': 'Pro Section',
    general: 'General Section',
    'theme-elements': 'Site Section',
    'theme-elements-single': 'Single Section',
    'woocommerce-elements': 'WooCommerce Section',
    wordpress: 'WordPress Section',
    categories: 'Widgets Tab',
    global: 'Globals Tab',
    'whats-new': 'What\'s New',
    'document-settings': 'Document Settings icon',
    'preview-page': 'Preview Page',
    'publish-button': 'Publish Button',
    'widget-panel': 'Widget Panel Icon',
    finder: 'Finder',
    help: 'Help',
    elementorLogoDropdown: 'top_bar_elementor_logo_dropdown',
    elementorLogo: 'Elementor Logo',
    eLogoMenu: 'E-logo Menu',
    notes: 'Notes',
    siteSettings: 'Site Settings',
    structure: 'Structure',
    documentNameDropdown: 'Document Name dropdown',
    responsiveControls: 'Responsive controls',
    launchpad: 'launchpad',
    checklistHeader: 'Checklist Header',
    checklistSteps: 'Checklist Steps',
    userPreferences: 'User Preferences',
    contextMenu: 'Context Menu',
    templateLibrary: {
      saveModal: 'Save to Modal',
      moveModal: 'Move to Modal',
      bulkMoveModal: 'Bulk Move to Modal',
      copyModal: 'Copy to Modal',
      bulkCopyModal: 'Bulk Copy to Modal',
      saveModalSelectFolder: 'Save to Modal - select folder',
      saveModalSelectConnect: 'Save to Modal - connect',
      saveModalSelectUpgrade: 'Save to Modal - upgrade',
      importModal: 'Import Modal',
      newFolderModal: 'New Folder Modal',
      deleteDialog: 'Delete Dialog',
      deleteFolderDialog: 'Delete Folder Dialog',
      renameDialog: 'Rename Dialog',
      createFolderDialog: 'Create Folder Dialog',
      applySettingsDialog: 'Apply Settings Dialog',
      cloudTab: 'Cloud Tab',
      siteTab: 'Site Tab',
      cloudTabFolder: 'Cloud Tab - Folder',
      cloudTabConnect: 'Cloud Tab - Connect',
      cloudTabUpgrade: 'Cloud Tab - Upgrade',
      morePopup: 'Context Menu',
      quotaBar: 'Quota Bar'
    },
    kitLibrary: {
      cloudKitLibrary: 'kits_cloud_library',
      cloudKitLibraryConnect: 'kits_cloud_library_connect',
      cloudKitLibraryUpgrade: 'kits_cloud_library_upgrade',
      kitExportCustomization: 'kit_export_customization',
      kitExport: 'kit_export',
      kitExportCustomizationEdit: 'kit_export_customization_edit',
      kitExportSummary: 'kit_export_summary',
      kitImportUploadBox: 'kit_import_upload_box',
      kitImportCustomization: 'kit_import_customization',
      kitImportSummary: 'kit_import_summary'
    },
    variablesPopover: 'Variables Popover',
    admin: {
      pluginToolsTab: 'plugin_tools_tab',
      pluginWebsiteTemplatesTab: 'plugin_website_templates_tab'
    }
  },
  elements: {
    accordionSection: 'Accordion section',
    buttonIcon: 'Button Icon',
    mainCta: 'Main CTA',
    button: 'Button',
    link: 'Link',
    dropdown: 'Dropdown',
    toggle: 'Toggle',
    launchpadChecklist: 'Checklist popup'
  },
  names: {
    v1: {
      layout: 'v1_widgets_tab_layout_section',
      basic: 'v1_widgets_tab_basic_section',
      'pro-elements': 'v1_widgets_tab_pro_section',
      general: 'v1_widgets_tab_general_section',
      'theme-elements': 'v1_widgets_tab_site_section',
      'theme-elements-single': 'v1_widgets_tab_single_section',
      'woocommerce-elements': 'v1_widgets_tab_woocommerce_section',
      wordpress: 'v1_widgets_tab_wordpress_section',
      categories: 'v1_widgets_tab',
      global: 'v1_globals_tab'
    },
    topBar: {
      whatsNew: 'top_bar_whats_new',
      documentSettings: 'top_bar_document_settings_icon',
      previewPage: 'top_bar_preview_page',
      publishButton: 'top_bar_publish_button',
      widgetPanel: 'top_bar_widget_panel_icon',
      finder: 'top_bar_finder',
      help: 'top_bar_help',
      history: 'top_bar_elementor_logo_dropdown_history',
      userPreferences: 'top_bar_elementor_logo_dropdown_user_preferences',
      keyboardShortcuts: 'top_bar_elementor_logo_dropdown_keyboard_shortcuts',
      exitToWordpress: 'top_bar_elementor_logo_dropdown_exit_to_wordpress',
      themeBuilder: 'top_bar_elementor_logo_dropdown_theme_builder',
      notes: 'top_bar_notes',
      siteSettings: 'top_bar_site_setting',
      structure: 'top_bar_structure',
      documentNameDropdown: 'top_bar_document_name_dropdown',
      responsiveControls: 'top_bar_responsive_controls',
      launchpadOn: 'top_bar_checklist_icon_show',
      launchpadOff: 'top_bar_checklist_icon_hide',
      elementorLogoDropdown: 'open_e_menu',
      connectAccount: 'connect_account',
      accountConnected: 'account_connected'
    },
    // ChecklistSteps event names are generated dynamically, based on stepId and action type taken: title, action, done, undone, upgrade
    elementorEditor: {
      checklist: {
        checklistHeaderClose: 'checklist_header_close_icon',
        checklistFirstPopup: 'checklist popup triggered'
      },
      userPreferences: {
        checklistShow: 'checklist_userpreferences_toggle_show',
        checklistHide: 'checklist_userpreferences_toggle_hide'
      }
    },
    variables: {
      open: 'open_variables_popover',
      add: 'add_new_variable',
      connect: 'connect_variable',
      save: 'save_new_variable'
    }
  }
};
var _default = exports["default"] = eventsConfig;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _interopRequireDefault(e) {
  return e && e.__esModule ? e : {
    "default": e
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/core-js/internals/a-callable.js":
/*!*******************************************************!*\
  !*** ../node_modules/core-js/internals/a-callable.js ***!
  \*******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var tryToString = __webpack_require__(/*! ../internals/try-to-string */ "../node_modules/core-js/internals/try-to-string.js");

var $TypeError = TypeError;

// `Assert: IsCallable(argument) is true`
module.exports = function (argument) {
  if (isCallable(argument)) return argument;
  throw new $TypeError(tryToString(argument) + ' is not a function');
};


/***/ }),

/***/ "../node_modules/core-js/internals/an-instance.js":
/*!********************************************************!*\
  !*** ../node_modules/core-js/internals/an-instance.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isPrototypeOf = __webpack_require__(/*! ../internals/object-is-prototype-of */ "../node_modules/core-js/internals/object-is-prototype-of.js");

var $TypeError = TypeError;

module.exports = function (it, Prototype) {
  if (isPrototypeOf(Prototype, it)) return it;
  throw new $TypeError('Incorrect invocation');
};


/***/ }),

/***/ "../node_modules/core-js/internals/an-object.js":
/*!******************************************************!*\
  !*** ../node_modules/core-js/internals/an-object.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isObject = __webpack_require__(/*! ../internals/is-object */ "../node_modules/core-js/internals/is-object.js");

var $String = String;
var $TypeError = TypeError;

// `Assert: Type(argument) is Object`
module.exports = function (argument) {
  if (isObject(argument)) return argument;
  throw new $TypeError($String(argument) + ' is not an object');
};


/***/ }),

/***/ "../node_modules/core-js/internals/array-includes.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/internals/array-includes.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ "../node_modules/core-js/internals/to-indexed-object.js");
var toAbsoluteIndex = __webpack_require__(/*! ../internals/to-absolute-index */ "../node_modules/core-js/internals/to-absolute-index.js");
var lengthOfArrayLike = __webpack_require__(/*! ../internals/length-of-array-like */ "../node_modules/core-js/internals/length-of-array-like.js");

// `Array.prototype.{ indexOf, includes }` methods implementation
var createMethod = function (IS_INCLUDES) {
  return function ($this, el, fromIndex) {
    var O = toIndexedObject($this);
    var length = lengthOfArrayLike(O);
    if (length === 0) return !IS_INCLUDES && -1;
    var index = toAbsoluteIndex(fromIndex, length);
    var value;
    // Array#includes uses SameValueZero equality algorithm
    // eslint-disable-next-line no-self-compare -- NaN check
    if (IS_INCLUDES && el !== el) while (length > index) {
      value = O[index++];
      // eslint-disable-next-line no-self-compare -- NaN check
      if (value !== value) return true;
    // Array#indexOf ignores holes, Array#includes - not
    } else for (;length > index; index++) {
      if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};

module.exports = {
  // `Array.prototype.includes` method
  // https://tc39.es/ecma262/#sec-array.prototype.includes
  includes: createMethod(true),
  // `Array.prototype.indexOf` method
  // https://tc39.es/ecma262/#sec-array.prototype.indexof
  indexOf: createMethod(false)
};


/***/ }),

/***/ "../node_modules/core-js/internals/array-set-length.js":
/*!*************************************************************!*\
  !*** ../node_modules/core-js/internals/array-set-length.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var isArray = __webpack_require__(/*! ../internals/is-array */ "../node_modules/core-js/internals/is-array.js");

var $TypeError = TypeError;
// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

// Safari < 13 does not throw an error in this case
var SILENT_ON_NON_WRITABLE_LENGTH_SET = DESCRIPTORS && !function () {
  // makes no sense without proper strict mode support
  if (this !== undefined) return true;
  try {
    // eslint-disable-next-line es/no-object-defineproperty -- safe
    Object.defineProperty([], 'length', { writable: false }).length = 1;
  } catch (error) {
    return error instanceof TypeError;
  }
}();

module.exports = SILENT_ON_NON_WRITABLE_LENGTH_SET ? function (O, length) {
  if (isArray(O) && !getOwnPropertyDescriptor(O, 'length').writable) {
    throw new $TypeError('Cannot set read only .length');
  } return O.length = length;
} : function (O, length) {
  return O.length = length;
};


/***/ }),

/***/ "../node_modules/core-js/internals/call-with-safe-iteration-closing.js":
/*!*****************************************************************************!*\
  !*** ../node_modules/core-js/internals/call-with-safe-iteration-closing.js ***!
  \*****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");

// call something on iterator step with safe closing on error
module.exports = function (iterator, fn, value, ENTRIES) {
  try {
    return ENTRIES ? fn(anObject(value)[0], value[1]) : fn(value);
  } catch (error) {
    iteratorClose(iterator, 'throw', error);
  }
};


/***/ }),

/***/ "../node_modules/core-js/internals/classof-raw.js":
/*!********************************************************!*\
  !*** ../node_modules/core-js/internals/classof-raw.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");

var toString = uncurryThis({}.toString);
var stringSlice = uncurryThis(''.slice);

module.exports = function (it) {
  return stringSlice(toString(it), 8, -1);
};


/***/ }),

/***/ "../node_modules/core-js/internals/classof.js":
/*!****************************************************!*\
  !*** ../node_modules/core-js/internals/classof.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var TO_STRING_TAG_SUPPORT = __webpack_require__(/*! ../internals/to-string-tag-support */ "../node_modules/core-js/internals/to-string-tag-support.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var classofRaw = __webpack_require__(/*! ../internals/classof-raw */ "../node_modules/core-js/internals/classof-raw.js");
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "../node_modules/core-js/internals/well-known-symbol.js");

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var $Object = Object;

// ES3 wrong here
var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) === 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (error) { /* empty */ }
};

// getting tag from ES6+ `Object.prototype.toString`
module.exports = TO_STRING_TAG_SUPPORT ? classofRaw : function (it) {
  var O, tag, result;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (tag = tryGet(O = $Object(it), TO_STRING_TAG)) == 'string' ? tag
    // builtinTag case
    : CORRECT_ARGUMENTS ? classofRaw(O)
    // ES3 arguments fallback
    : (result = classofRaw(O)) === 'Object' && isCallable(O.callee) ? 'Arguments' : result;
};


/***/ }),

/***/ "../node_modules/core-js/internals/copy-constructor-properties.js":
/*!************************************************************************!*\
  !*** ../node_modules/core-js/internals/copy-constructor-properties.js ***!
  \************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");
var ownKeys = __webpack_require__(/*! ../internals/own-keys */ "../node_modules/core-js/internals/own-keys.js");
var getOwnPropertyDescriptorModule = __webpack_require__(/*! ../internals/object-get-own-property-descriptor */ "../node_modules/core-js/internals/object-get-own-property-descriptor.js");
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ "../node_modules/core-js/internals/object-define-property.js");

module.exports = function (target, source, exceptions) {
  var keys = ownKeys(source);
  var defineProperty = definePropertyModule.f;
  var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
  for (var i = 0; i < keys.length; i++) {
    var key = keys[i];
    if (!hasOwn(target, key) && !(exceptions && hasOwn(exceptions, key))) {
      defineProperty(target, key, getOwnPropertyDescriptor(source, key));
    }
  }
};


/***/ }),

/***/ "../node_modules/core-js/internals/correct-prototype-getter.js":
/*!*********************************************************************!*\
  !*** ../node_modules/core-js/internals/correct-prototype-getter.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");

module.exports = !fails(function () {
  function F() { /* empty */ }
  F.prototype.constructor = null;
  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
  return Object.getPrototypeOf(new F()) !== F.prototype;
});


/***/ }),

/***/ "../node_modules/core-js/internals/create-iter-result-object.js":
/*!**********************************************************************!*\
  !*** ../node_modules/core-js/internals/create-iter-result-object.js ***!
  \**********************************************************************/
/***/ ((module) => {

"use strict";

// `CreateIterResultObject` abstract operation
// https://tc39.es/ecma262/#sec-createiterresultobject
module.exports = function (value, done) {
  return { value: value, done: done };
};


/***/ }),

/***/ "../node_modules/core-js/internals/create-non-enumerable-property.js":
/*!***************************************************************************!*\
  !*** ../node_modules/core-js/internals/create-non-enumerable-property.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ "../node_modules/core-js/internals/object-define-property.js");
var createPropertyDescriptor = __webpack_require__(/*! ../internals/create-property-descriptor */ "../node_modules/core-js/internals/create-property-descriptor.js");

module.exports = DESCRIPTORS ? function (object, key, value) {
  return definePropertyModule.f(object, key, createPropertyDescriptor(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ "../node_modules/core-js/internals/create-property-descriptor.js":
/*!***********************************************************************!*\
  !*** ../node_modules/core-js/internals/create-property-descriptor.js ***!
  \***********************************************************************/
/***/ ((module) => {

"use strict";

module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};


/***/ }),

/***/ "../node_modules/core-js/internals/create-property.js":
/*!************************************************************!*\
  !*** ../node_modules/core-js/internals/create-property.js ***!
  \************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ "../node_modules/core-js/internals/object-define-property.js");
var createPropertyDescriptor = __webpack_require__(/*! ../internals/create-property-descriptor */ "../node_modules/core-js/internals/create-property-descriptor.js");

module.exports = function (object, key, value) {
  if (DESCRIPTORS) definePropertyModule.f(object, key, createPropertyDescriptor(0, value));
  else object[key] = value;
};


/***/ }),

/***/ "../node_modules/core-js/internals/define-built-in-accessor.js":
/*!*********************************************************************!*\
  !*** ../node_modules/core-js/internals/define-built-in-accessor.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var makeBuiltIn = __webpack_require__(/*! ../internals/make-built-in */ "../node_modules/core-js/internals/make-built-in.js");
var defineProperty = __webpack_require__(/*! ../internals/object-define-property */ "../node_modules/core-js/internals/object-define-property.js");

module.exports = function (target, name, descriptor) {
  if (descriptor.get) makeBuiltIn(descriptor.get, name, { getter: true });
  if (descriptor.set) makeBuiltIn(descriptor.set, name, { setter: true });
  return defineProperty.f(target, name, descriptor);
};


/***/ }),

/***/ "../node_modules/core-js/internals/define-built-in.js":
/*!************************************************************!*\
  !*** ../node_modules/core-js/internals/define-built-in.js ***!
  \************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ "../node_modules/core-js/internals/object-define-property.js");
var makeBuiltIn = __webpack_require__(/*! ../internals/make-built-in */ "../node_modules/core-js/internals/make-built-in.js");
var defineGlobalProperty = __webpack_require__(/*! ../internals/define-global-property */ "../node_modules/core-js/internals/define-global-property.js");

module.exports = function (O, key, value, options) {
  if (!options) options = {};
  var simple = options.enumerable;
  var name = options.name !== undefined ? options.name : key;
  if (isCallable(value)) makeBuiltIn(value, name, options);
  if (options.global) {
    if (simple) O[key] = value;
    else defineGlobalProperty(key, value);
  } else {
    try {
      if (!options.unsafe) delete O[key];
      else if (O[key]) simple = true;
    } catch (error) { /* empty */ }
    if (simple) O[key] = value;
    else definePropertyModule.f(O, key, {
      value: value,
      enumerable: false,
      configurable: !options.nonConfigurable,
      writable: !options.nonWritable
    });
  } return O;
};


/***/ }),

/***/ "../node_modules/core-js/internals/define-built-ins.js":
/*!*************************************************************!*\
  !*** ../node_modules/core-js/internals/define-built-ins.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var defineBuiltIn = __webpack_require__(/*! ../internals/define-built-in */ "../node_modules/core-js/internals/define-built-in.js");

module.exports = function (target, src, options) {
  for (var key in src) defineBuiltIn(target, key, src[key], options);
  return target;
};


/***/ }),

/***/ "../node_modules/core-js/internals/define-global-property.js":
/*!*******************************************************************!*\
  !*** ../node_modules/core-js/internals/define-global-property.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");

// eslint-disable-next-line es/no-object-defineproperty -- safe
var defineProperty = Object.defineProperty;

module.exports = function (key, value) {
  try {
    defineProperty(globalThis, key, { value: value, configurable: true, writable: true });
  } catch (error) {
    globalThis[key] = value;
  } return value;
};


/***/ }),

/***/ "../node_modules/core-js/internals/descriptors.js":
/*!********************************************************!*\
  !*** ../node_modules/core-js/internals/descriptors.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");

// Detect IE8's incomplete defineProperty implementation
module.exports = !fails(function () {
  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] !== 7;
});


/***/ }),

/***/ "../node_modules/core-js/internals/document-create-element.js":
/*!********************************************************************!*\
  !*** ../node_modules/core-js/internals/document-create-element.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var isObject = __webpack_require__(/*! ../internals/is-object */ "../node_modules/core-js/internals/is-object.js");

var document = globalThis.document;
// typeof document.createElement is 'object' in old IE
var EXISTS = isObject(document) && isObject(document.createElement);

module.exports = function (it) {
  return EXISTS ? document.createElement(it) : {};
};


/***/ }),

/***/ "../node_modules/core-js/internals/does-not-exceed-safe-integer.js":
/*!*************************************************************************!*\
  !*** ../node_modules/core-js/internals/does-not-exceed-safe-integer.js ***!
  \*************************************************************************/
/***/ ((module) => {

"use strict";

var $TypeError = TypeError;
var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; // 2 ** 53 - 1 == 9007199254740991

module.exports = function (it) {
  if (it > MAX_SAFE_INTEGER) throw $TypeError('Maximum allowed index exceeded');
  return it;
};


/***/ }),

/***/ "../node_modules/core-js/internals/enum-bug-keys.js":
/*!**********************************************************!*\
  !*** ../node_modules/core-js/internals/enum-bug-keys.js ***!
  \**********************************************************/
/***/ ((module) => {

"use strict";

// IE8- don't enum bug keys
module.exports = [
  'constructor',
  'hasOwnProperty',
  'isPrototypeOf',
  'propertyIsEnumerable',
  'toLocaleString',
  'toString',
  'valueOf'
];


/***/ }),

/***/ "../node_modules/core-js/internals/environment-user-agent.js":
/*!*******************************************************************!*\
  !*** ../node_modules/core-js/internals/environment-user-agent.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");

var navigator = globalThis.navigator;
var userAgent = navigator && navigator.userAgent;

module.exports = userAgent ? String(userAgent) : '';


/***/ }),

/***/ "../node_modules/core-js/internals/environment-v8-version.js":
/*!*******************************************************************!*\
  !*** ../node_modules/core-js/internals/environment-v8-version.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var userAgent = __webpack_require__(/*! ../internals/environment-user-agent */ "../node_modules/core-js/internals/environment-user-agent.js");

var process = globalThis.process;
var Deno = globalThis.Deno;
var versions = process && process.versions || Deno && Deno.version;
var v8 = versions && versions.v8;
var match, version;

if (v8) {
  match = v8.split('.');
  // in old Chrome, versions of V8 isn't V8 = Chrome / 10
  // but their correct versions are not interesting for us
  version = match[0] > 0 && match[0] < 4 ? 1 : +(match[0] + match[1]);
}

// BrowserFS NodeJS `process` polyfill incorrectly set `.v8` to `0.0`
// so check `userAgent` even if `.v8` exists, but 0
if (!version && userAgent) {
  match = userAgent.match(/Edge\/(\d+)/);
  if (!match || match[1] >= 74) {
    match = userAgent.match(/Chrome\/(\d+)/);
    if (match) version = +match[1];
  }
}

module.exports = version;


/***/ }),

/***/ "../node_modules/core-js/internals/export.js":
/*!***************************************************!*\
  !*** ../node_modules/core-js/internals/export.js ***!
  \***************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var getOwnPropertyDescriptor = (__webpack_require__(/*! ../internals/object-get-own-property-descriptor */ "../node_modules/core-js/internals/object-get-own-property-descriptor.js").f);
var createNonEnumerableProperty = __webpack_require__(/*! ../internals/create-non-enumerable-property */ "../node_modules/core-js/internals/create-non-enumerable-property.js");
var defineBuiltIn = __webpack_require__(/*! ../internals/define-built-in */ "../node_modules/core-js/internals/define-built-in.js");
var defineGlobalProperty = __webpack_require__(/*! ../internals/define-global-property */ "../node_modules/core-js/internals/define-global-property.js");
var copyConstructorProperties = __webpack_require__(/*! ../internals/copy-constructor-properties */ "../node_modules/core-js/internals/copy-constructor-properties.js");
var isForced = __webpack_require__(/*! ../internals/is-forced */ "../node_modules/core-js/internals/is-forced.js");

/*
  options.target         - name of the target object
  options.global         - target is the global object
  options.stat           - export as static methods of target
  options.proto          - export as prototype methods of target
  options.real           - real prototype method for the `pure` version
  options.forced         - export even if the native feature is available
  options.bind           - bind methods to the target, required for the `pure` version
  options.wrap           - wrap constructors to preventing global pollution, required for the `pure` version
  options.unsafe         - use the simple assignment of property instead of delete + defineProperty
  options.sham           - add a flag to not completely full polyfills
  options.enumerable     - export as enumerable property
  options.dontCallGetSet - prevent calling a getter on target
  options.name           - the .name of the function if it does not match the key
*/
module.exports = function (options, source) {
  var TARGET = options.target;
  var GLOBAL = options.global;
  var STATIC = options.stat;
  var FORCED, target, key, targetProperty, sourceProperty, descriptor;
  if (GLOBAL) {
    target = globalThis;
  } else if (STATIC) {
    target = globalThis[TARGET] || defineGlobalProperty(TARGET, {});
  } else {
    target = globalThis[TARGET] && globalThis[TARGET].prototype;
  }
  if (target) for (key in source) {
    sourceProperty = source[key];
    if (options.dontCallGetSet) {
      descriptor = getOwnPropertyDescriptor(target, key);
      targetProperty = descriptor && descriptor.value;
    } else targetProperty = target[key];
    FORCED = isForced(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
    // contained in target
    if (!FORCED && targetProperty !== undefined) {
      if (typeof sourceProperty == typeof targetProperty) continue;
      copyConstructorProperties(sourceProperty, targetProperty);
    }
    // add a flag to not completely full polyfills
    if (options.sham || (targetProperty && targetProperty.sham)) {
      createNonEnumerableProperty(sourceProperty, 'sham', true);
    }
    defineBuiltIn(target, key, sourceProperty, options);
  }
};


/***/ }),

/***/ "../node_modules/core-js/internals/fails.js":
/*!**************************************************!*\
  !*** ../node_modules/core-js/internals/fails.js ***!
  \**************************************************/
/***/ ((module) => {

"use strict";

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (error) {
    return true;
  }
};


/***/ }),

/***/ "../node_modules/core-js/internals/function-bind-context.js":
/*!******************************************************************!*\
  !*** ../node_modules/core-js/internals/function-bind-context.js ***!
  \******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this-clause */ "../node_modules/core-js/internals/function-uncurry-this-clause.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "../node_modules/core-js/internals/a-callable.js");
var NATIVE_BIND = __webpack_require__(/*! ../internals/function-bind-native */ "../node_modules/core-js/internals/function-bind-native.js");

var bind = uncurryThis(uncurryThis.bind);

// optional / simple context binding
module.exports = function (fn, that) {
  aCallable(fn);
  return that === undefined ? fn : NATIVE_BIND ? bind(fn, that) : function (/* ...args */) {
    return fn.apply(that, arguments);
  };
};


/***/ }),

/***/ "../node_modules/core-js/internals/function-bind-native.js":
/*!*****************************************************************!*\
  !*** ../node_modules/core-js/internals/function-bind-native.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");

module.exports = !fails(function () {
  // eslint-disable-next-line es/no-function-prototype-bind -- safe
  var test = (function () { /* empty */ }).bind();
  // eslint-disable-next-line no-prototype-builtins -- safe
  return typeof test != 'function' || test.hasOwnProperty('prototype');
});


/***/ }),

/***/ "../node_modules/core-js/internals/function-call.js":
/*!**********************************************************!*\
  !*** ../node_modules/core-js/internals/function-call.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var NATIVE_BIND = __webpack_require__(/*! ../internals/function-bind-native */ "../node_modules/core-js/internals/function-bind-native.js");

var call = Function.prototype.call;
// eslint-disable-next-line es/no-function-prototype-bind -- safe
module.exports = NATIVE_BIND ? call.bind(call) : function () {
  return call.apply(call, arguments);
};


/***/ }),

/***/ "../node_modules/core-js/internals/function-name.js":
/*!**********************************************************!*\
  !*** ../node_modules/core-js/internals/function-name.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");

var FunctionPrototype = Function.prototype;
// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var getDescriptor = DESCRIPTORS && Object.getOwnPropertyDescriptor;

var EXISTS = hasOwn(FunctionPrototype, 'name');
// additional protection from minified / mangled / dropped function names
var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
var CONFIGURABLE = EXISTS && (!DESCRIPTORS || (DESCRIPTORS && getDescriptor(FunctionPrototype, 'name').configurable));

module.exports = {
  EXISTS: EXISTS,
  PROPER: PROPER,
  CONFIGURABLE: CONFIGURABLE
};


/***/ }),

/***/ "../node_modules/core-js/internals/function-uncurry-this-clause.js":
/*!*************************************************************************!*\
  !*** ../node_modules/core-js/internals/function-uncurry-this-clause.js ***!
  \*************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var classofRaw = __webpack_require__(/*! ../internals/classof-raw */ "../node_modules/core-js/internals/classof-raw.js");
var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");

module.exports = function (fn) {
  // Nashorn bug:
  //   https://github.com/zloirock/core-js/issues/1128
  //   https://github.com/zloirock/core-js/issues/1130
  if (classofRaw(fn) === 'Function') return uncurryThis(fn);
};


/***/ }),

/***/ "../node_modules/core-js/internals/function-uncurry-this.js":
/*!******************************************************************!*\
  !*** ../node_modules/core-js/internals/function-uncurry-this.js ***!
  \******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var NATIVE_BIND = __webpack_require__(/*! ../internals/function-bind-native */ "../node_modules/core-js/internals/function-bind-native.js");

var FunctionPrototype = Function.prototype;
var call = FunctionPrototype.call;
// eslint-disable-next-line es/no-function-prototype-bind -- safe
var uncurryThisWithBind = NATIVE_BIND && FunctionPrototype.bind.bind(call, call);

module.exports = NATIVE_BIND ? uncurryThisWithBind : function (fn) {
  return function () {
    return call.apply(fn, arguments);
  };
};


/***/ }),

/***/ "../node_modules/core-js/internals/get-built-in.js":
/*!*********************************************************!*\
  !*** ../node_modules/core-js/internals/get-built-in.js ***!
  \*********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");

var aFunction = function (argument) {
  return isCallable(argument) ? argument : undefined;
};

module.exports = function (namespace, method) {
  return arguments.length < 2 ? aFunction(globalThis[namespace]) : globalThis[namespace] && globalThis[namespace][method];
};


/***/ }),

/***/ "../node_modules/core-js/internals/get-iterator-direct.js":
/*!****************************************************************!*\
  !*** ../node_modules/core-js/internals/get-iterator-direct.js ***!
  \****************************************************************/
/***/ ((module) => {

"use strict";

// `GetIteratorDirect(obj)` abstract operation
// https://tc39.es/proposal-iterator-helpers/#sec-getiteratordirect
module.exports = function (obj) {
  return {
    iterator: obj,
    next: obj.next,
    done: false
  };
};


/***/ }),

/***/ "../node_modules/core-js/internals/get-iterator-method.js":
/*!****************************************************************!*\
  !*** ../node_modules/core-js/internals/get-iterator-method.js ***!
  \****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var classof = __webpack_require__(/*! ../internals/classof */ "../node_modules/core-js/internals/classof.js");
var getMethod = __webpack_require__(/*! ../internals/get-method */ "../node_modules/core-js/internals/get-method.js");
var isNullOrUndefined = __webpack_require__(/*! ../internals/is-null-or-undefined */ "../node_modules/core-js/internals/is-null-or-undefined.js");
var Iterators = __webpack_require__(/*! ../internals/iterators */ "../node_modules/core-js/internals/iterators.js");
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "../node_modules/core-js/internals/well-known-symbol.js");

var ITERATOR = wellKnownSymbol('iterator');

module.exports = function (it) {
  if (!isNullOrUndefined(it)) return getMethod(it, ITERATOR)
    || getMethod(it, '@@iterator')
    || Iterators[classof(it)];
};


/***/ }),

/***/ "../node_modules/core-js/internals/get-iterator.js":
/*!*********************************************************!*\
  !*** ../node_modules/core-js/internals/get-iterator.js ***!
  \*********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "../node_modules/core-js/internals/a-callable.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var tryToString = __webpack_require__(/*! ../internals/try-to-string */ "../node_modules/core-js/internals/try-to-string.js");
var getIteratorMethod = __webpack_require__(/*! ../internals/get-iterator-method */ "../node_modules/core-js/internals/get-iterator-method.js");

var $TypeError = TypeError;

module.exports = function (argument, usingIterator) {
  var iteratorMethod = arguments.length < 2 ? getIteratorMethod(argument) : usingIterator;
  if (aCallable(iteratorMethod)) return anObject(call(iteratorMethod, argument));
  throw new $TypeError(tryToString(argument) + ' is not iterable');
};


/***/ }),

/***/ "../node_modules/core-js/internals/get-method.js":
/*!*******************************************************!*\
  !*** ../node_modules/core-js/internals/get-method.js ***!
  \*******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var aCallable = __webpack_require__(/*! ../internals/a-callable */ "../node_modules/core-js/internals/a-callable.js");
var isNullOrUndefined = __webpack_require__(/*! ../internals/is-null-or-undefined */ "../node_modules/core-js/internals/is-null-or-undefined.js");

// `GetMethod` abstract operation
// https://tc39.es/ecma262/#sec-getmethod
module.exports = function (V, P) {
  var func = V[P];
  return isNullOrUndefined(func) ? undefined : aCallable(func);
};


/***/ }),

/***/ "../node_modules/core-js/internals/global-this.js":
/*!********************************************************!*\
  !*** ../node_modules/core-js/internals/global-this.js ***!
  \********************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var check = function (it) {
  return it && it.Math === Math && it;
};

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
module.exports =
  // eslint-disable-next-line es/no-global-this -- safe
  check(typeof globalThis == 'object' && globalThis) ||
  check(typeof window == 'object' && window) ||
  // eslint-disable-next-line no-restricted-globals -- safe
  check(typeof self == 'object' && self) ||
  check(typeof __webpack_require__.g == 'object' && __webpack_require__.g) ||
  check(typeof this == 'object' && this) ||
  // eslint-disable-next-line no-new-func -- fallback
  (function () { return this; })() || Function('return this')();


/***/ }),

/***/ "../node_modules/core-js/internals/has-own-property.js":
/*!*************************************************************!*\
  !*** ../node_modules/core-js/internals/has-own-property.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");
var toObject = __webpack_require__(/*! ../internals/to-object */ "../node_modules/core-js/internals/to-object.js");

var hasOwnProperty = uncurryThis({}.hasOwnProperty);

// `HasOwnProperty` abstract operation
// https://tc39.es/ecma262/#sec-hasownproperty
// eslint-disable-next-line es/no-object-hasown -- safe
module.exports = Object.hasOwn || function hasOwn(it, key) {
  return hasOwnProperty(toObject(it), key);
};


/***/ }),

/***/ "../node_modules/core-js/internals/hidden-keys.js":
/*!********************************************************!*\
  !*** ../node_modules/core-js/internals/hidden-keys.js ***!
  \********************************************************/
/***/ ((module) => {

"use strict";

module.exports = {};


/***/ }),

/***/ "../node_modules/core-js/internals/html.js":
/*!*************************************************!*\
  !*** ../node_modules/core-js/internals/html.js ***!
  \*************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var getBuiltIn = __webpack_require__(/*! ../internals/get-built-in */ "../node_modules/core-js/internals/get-built-in.js");

module.exports = getBuiltIn('document', 'documentElement');


/***/ }),

/***/ "../node_modules/core-js/internals/ie8-dom-define.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/internals/ie8-dom-define.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");
var createElement = __webpack_require__(/*! ../internals/document-create-element */ "../node_modules/core-js/internals/document-create-element.js");

// Thanks to IE8 for its funny defineProperty
module.exports = !DESCRIPTORS && !fails(function () {
  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
  return Object.defineProperty(createElement('div'), 'a', {
    get: function () { return 7; }
  }).a !== 7;
});


/***/ }),

/***/ "../node_modules/core-js/internals/indexed-object.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/internals/indexed-object.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");
var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");
var classof = __webpack_require__(/*! ../internals/classof-raw */ "../node_modules/core-js/internals/classof-raw.js");

var $Object = Object;
var split = uncurryThis(''.split);

// fallback for non-array-like ES3 and non-enumerable old V8 strings
module.exports = fails(function () {
  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
  // eslint-disable-next-line no-prototype-builtins -- safe
  return !$Object('z').propertyIsEnumerable(0);
}) ? function (it) {
  return classof(it) === 'String' ? split(it, '') : $Object(it);
} : $Object;


/***/ }),

/***/ "../node_modules/core-js/internals/inspect-source.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/internals/inspect-source.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var store = __webpack_require__(/*! ../internals/shared-store */ "../node_modules/core-js/internals/shared-store.js");

var functionToString = uncurryThis(Function.toString);

// this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
if (!isCallable(store.inspectSource)) {
  store.inspectSource = function (it) {
    return functionToString(it);
  };
}

module.exports = store.inspectSource;


/***/ }),

/***/ "../node_modules/core-js/internals/internal-state.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/internals/internal-state.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var NATIVE_WEAK_MAP = __webpack_require__(/*! ../internals/weak-map-basic-detection */ "../node_modules/core-js/internals/weak-map-basic-detection.js");
var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var isObject = __webpack_require__(/*! ../internals/is-object */ "../node_modules/core-js/internals/is-object.js");
var createNonEnumerableProperty = __webpack_require__(/*! ../internals/create-non-enumerable-property */ "../node_modules/core-js/internals/create-non-enumerable-property.js");
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");
var shared = __webpack_require__(/*! ../internals/shared-store */ "../node_modules/core-js/internals/shared-store.js");
var sharedKey = __webpack_require__(/*! ../internals/shared-key */ "../node_modules/core-js/internals/shared-key.js");
var hiddenKeys = __webpack_require__(/*! ../internals/hidden-keys */ "../node_modules/core-js/internals/hidden-keys.js");

var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
var TypeError = globalThis.TypeError;
var WeakMap = globalThis.WeakMap;
var set, get, has;

var enforce = function (it) {
  return has(it) ? get(it) : set(it, {});
};

var getterFor = function (TYPE) {
  return function (it) {
    var state;
    if (!isObject(it) || (state = get(it)).type !== TYPE) {
      throw new TypeError('Incompatible receiver, ' + TYPE + ' required');
    } return state;
  };
};

if (NATIVE_WEAK_MAP || shared.state) {
  var store = shared.state || (shared.state = new WeakMap());
  /* eslint-disable no-self-assign -- prototype methods protection */
  store.get = store.get;
  store.has = store.has;
  store.set = store.set;
  /* eslint-enable no-self-assign -- prototype methods protection */
  set = function (it, metadata) {
    if (store.has(it)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
    metadata.facade = it;
    store.set(it, metadata);
    return metadata;
  };
  get = function (it) {
    return store.get(it) || {};
  };
  has = function (it) {
    return store.has(it);
  };
} else {
  var STATE = sharedKey('state');
  hiddenKeys[STATE] = true;
  set = function (it, metadata) {
    if (hasOwn(it, STATE)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
    metadata.facade = it;
    createNonEnumerableProperty(it, STATE, metadata);
    return metadata;
  };
  get = function (it) {
    return hasOwn(it, STATE) ? it[STATE] : {};
  };
  has = function (it) {
    return hasOwn(it, STATE);
  };
}

module.exports = {
  set: set,
  get: get,
  has: has,
  enforce: enforce,
  getterFor: getterFor
};


/***/ }),

/***/ "../node_modules/core-js/internals/is-array-iterator-method.js":
/*!*********************************************************************!*\
  !*** ../node_modules/core-js/internals/is-array-iterator-method.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "../node_modules/core-js/internals/well-known-symbol.js");
var Iterators = __webpack_require__(/*! ../internals/iterators */ "../node_modules/core-js/internals/iterators.js");

var ITERATOR = wellKnownSymbol('iterator');
var ArrayPrototype = Array.prototype;

// check on default Array iterator
module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayPrototype[ITERATOR] === it);
};


/***/ }),

/***/ "../node_modules/core-js/internals/is-array.js":
/*!*****************************************************!*\
  !*** ../node_modules/core-js/internals/is-array.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var classof = __webpack_require__(/*! ../internals/classof-raw */ "../node_modules/core-js/internals/classof-raw.js");

// `IsArray` abstract operation
// https://tc39.es/ecma262/#sec-isarray
// eslint-disable-next-line es/no-array-isarray -- safe
module.exports = Array.isArray || function isArray(argument) {
  return classof(argument) === 'Array';
};


/***/ }),

/***/ "../node_modules/core-js/internals/is-callable.js":
/*!********************************************************!*\
  !*** ../node_modules/core-js/internals/is-callable.js ***!
  \********************************************************/
/***/ ((module) => {

"use strict";

// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
var documentAll = typeof document == 'object' && document.all;

// `IsCallable` abstract operation
// https://tc39.es/ecma262/#sec-iscallable
// eslint-disable-next-line unicorn/no-typeof-undefined -- required for testing
module.exports = typeof documentAll == 'undefined' && documentAll !== undefined ? function (argument) {
  return typeof argument == 'function' || argument === documentAll;
} : function (argument) {
  return typeof argument == 'function';
};


/***/ }),

/***/ "../node_modules/core-js/internals/is-forced.js":
/*!******************************************************!*\
  !*** ../node_modules/core-js/internals/is-forced.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");

var replacement = /#|\.prototype\./;

var isForced = function (feature, detection) {
  var value = data[normalize(feature)];
  return value === POLYFILL ? true
    : value === NATIVE ? false
    : isCallable(detection) ? fails(detection)
    : !!detection;
};

var normalize = isForced.normalize = function (string) {
  return String(string).replace(replacement, '.').toLowerCase();
};

var data = isForced.data = {};
var NATIVE = isForced.NATIVE = 'N';
var POLYFILL = isForced.POLYFILL = 'P';

module.exports = isForced;


/***/ }),

/***/ "../node_modules/core-js/internals/is-null-or-undefined.js":
/*!*****************************************************************!*\
  !*** ../node_modules/core-js/internals/is-null-or-undefined.js ***!
  \*****************************************************************/
/***/ ((module) => {

"use strict";

// we can't use just `it == null` since of `document.all` special case
// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
module.exports = function (it) {
  return it === null || it === undefined;
};


/***/ }),

/***/ "../node_modules/core-js/internals/is-object.js":
/*!******************************************************!*\
  !*** ../node_modules/core-js/internals/is-object.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");

module.exports = function (it) {
  return typeof it == 'object' ? it !== null : isCallable(it);
};


/***/ }),

/***/ "../node_modules/core-js/internals/is-pure.js":
/*!****************************************************!*\
  !*** ../node_modules/core-js/internals/is-pure.js ***!
  \****************************************************/
/***/ ((module) => {

"use strict";

module.exports = false;


/***/ }),

/***/ "../node_modules/core-js/internals/is-symbol.js":
/*!******************************************************!*\
  !*** ../node_modules/core-js/internals/is-symbol.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var getBuiltIn = __webpack_require__(/*! ../internals/get-built-in */ "../node_modules/core-js/internals/get-built-in.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var isPrototypeOf = __webpack_require__(/*! ../internals/object-is-prototype-of */ "../node_modules/core-js/internals/object-is-prototype-of.js");
var USE_SYMBOL_AS_UID = __webpack_require__(/*! ../internals/use-symbol-as-uid */ "../node_modules/core-js/internals/use-symbol-as-uid.js");

var $Object = Object;

module.exports = USE_SYMBOL_AS_UID ? function (it) {
  return typeof it == 'symbol';
} : function (it) {
  var $Symbol = getBuiltIn('Symbol');
  return isCallable($Symbol) && isPrototypeOf($Symbol.prototype, $Object(it));
};


/***/ }),

/***/ "../node_modules/core-js/internals/iterate.js":
/*!****************************************************!*\
  !*** ../node_modules/core-js/internals/iterate.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var bind = __webpack_require__(/*! ../internals/function-bind-context */ "../node_modules/core-js/internals/function-bind-context.js");
var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var tryToString = __webpack_require__(/*! ../internals/try-to-string */ "../node_modules/core-js/internals/try-to-string.js");
var isArrayIteratorMethod = __webpack_require__(/*! ../internals/is-array-iterator-method */ "../node_modules/core-js/internals/is-array-iterator-method.js");
var lengthOfArrayLike = __webpack_require__(/*! ../internals/length-of-array-like */ "../node_modules/core-js/internals/length-of-array-like.js");
var isPrototypeOf = __webpack_require__(/*! ../internals/object-is-prototype-of */ "../node_modules/core-js/internals/object-is-prototype-of.js");
var getIterator = __webpack_require__(/*! ../internals/get-iterator */ "../node_modules/core-js/internals/get-iterator.js");
var getIteratorMethod = __webpack_require__(/*! ../internals/get-iterator-method */ "../node_modules/core-js/internals/get-iterator-method.js");
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");

var $TypeError = TypeError;

var Result = function (stopped, result) {
  this.stopped = stopped;
  this.result = result;
};

var ResultPrototype = Result.prototype;

module.exports = function (iterable, unboundFunction, options) {
  var that = options && options.that;
  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
  var IS_RECORD = !!(options && options.IS_RECORD);
  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
  var INTERRUPTED = !!(options && options.INTERRUPTED);
  var fn = bind(unboundFunction, that);
  var iterator, iterFn, index, length, result, next, step;

  var stop = function (condition) {
    if (iterator) iteratorClose(iterator, 'normal');
    return new Result(true, condition);
  };

  var callFn = function (value) {
    if (AS_ENTRIES) {
      anObject(value);
      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
    } return INTERRUPTED ? fn(value, stop) : fn(value);
  };

  if (IS_RECORD) {
    iterator = iterable.iterator;
  } else if (IS_ITERATOR) {
    iterator = iterable;
  } else {
    iterFn = getIteratorMethod(iterable);
    if (!iterFn) throw new $TypeError(tryToString(iterable) + ' is not iterable');
    // optimisation for array iterators
    if (isArrayIteratorMethod(iterFn)) {
      for (index = 0, length = lengthOfArrayLike(iterable); length > index; index++) {
        result = callFn(iterable[index]);
        if (result && isPrototypeOf(ResultPrototype, result)) return result;
      } return new Result(false);
    }
    iterator = getIterator(iterable, iterFn);
  }

  next = IS_RECORD ? iterable.next : iterator.next;
  while (!(step = call(next, iterator)).done) {
    try {
      result = callFn(step.value);
    } catch (error) {
      iteratorClose(iterator, 'throw', error);
    }
    if (typeof result == 'object' && result && isPrototypeOf(ResultPrototype, result)) return result;
  } return new Result(false);
};


/***/ }),

/***/ "../node_modules/core-js/internals/iterator-close-all.js":
/*!***************************************************************!*\
  !*** ../node_modules/core-js/internals/iterator-close-all.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");

module.exports = function (iters, kind, value) {
  for (var i = iters.length - 1; i >= 0; i--) {
    if (iters[i] === undefined) continue;
    try {
      value = iteratorClose(iters[i].iterator, kind, value);
    } catch (error) {
      kind = 'throw';
      value = error;
    }
  }
  if (kind === 'throw') throw value;
  return value;
};


/***/ }),

/***/ "../node_modules/core-js/internals/iterator-close.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/internals/iterator-close.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var getMethod = __webpack_require__(/*! ../internals/get-method */ "../node_modules/core-js/internals/get-method.js");

module.exports = function (iterator, kind, value) {
  var innerResult, innerError;
  anObject(iterator);
  try {
    innerResult = getMethod(iterator, 'return');
    if (!innerResult) {
      if (kind === 'throw') throw value;
      return value;
    }
    innerResult = call(innerResult, iterator);
  } catch (error) {
    innerError = true;
    innerResult = error;
  }
  if (kind === 'throw') throw value;
  if (innerError) throw innerResult;
  anObject(innerResult);
  return value;
};


/***/ }),

/***/ "../node_modules/core-js/internals/iterator-create-proxy.js":
/*!******************************************************************!*\
  !*** ../node_modules/core-js/internals/iterator-create-proxy.js ***!
  \******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var create = __webpack_require__(/*! ../internals/object-create */ "../node_modules/core-js/internals/object-create.js");
var createNonEnumerableProperty = __webpack_require__(/*! ../internals/create-non-enumerable-property */ "../node_modules/core-js/internals/create-non-enumerable-property.js");
var defineBuiltIns = __webpack_require__(/*! ../internals/define-built-ins */ "../node_modules/core-js/internals/define-built-ins.js");
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "../node_modules/core-js/internals/well-known-symbol.js");
var InternalStateModule = __webpack_require__(/*! ../internals/internal-state */ "../node_modules/core-js/internals/internal-state.js");
var getMethod = __webpack_require__(/*! ../internals/get-method */ "../node_modules/core-js/internals/get-method.js");
var IteratorPrototype = (__webpack_require__(/*! ../internals/iterators-core */ "../node_modules/core-js/internals/iterators-core.js").IteratorPrototype);
var createIterResultObject = __webpack_require__(/*! ../internals/create-iter-result-object */ "../node_modules/core-js/internals/create-iter-result-object.js");
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");
var iteratorCloseAll = __webpack_require__(/*! ./iterator-close-all */ "../node_modules/core-js/internals/iterator-close-all.js");

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var ITERATOR_HELPER = 'IteratorHelper';
var WRAP_FOR_VALID_ITERATOR = 'WrapForValidIterator';
var NORMAL = 'normal';
var THROW = 'throw';
var setInternalState = InternalStateModule.set;

var createIteratorProxyPrototype = function (IS_ITERATOR) {
  var getInternalState = InternalStateModule.getterFor(IS_ITERATOR ? WRAP_FOR_VALID_ITERATOR : ITERATOR_HELPER);

  return defineBuiltIns(create(IteratorPrototype), {
    next: function next() {
      var state = getInternalState(this);
      // for simplification:
      //   for `%WrapForValidIteratorPrototype%.next` or with `state.returnHandlerResult` our `nextHandler` returns `IterResultObject`
      //   for `%IteratorHelperPrototype%.next` - just a value
      if (IS_ITERATOR) return state.nextHandler();
      if (state.done) return createIterResultObject(undefined, true);
      try {
        var result = state.nextHandler();
        return state.returnHandlerResult ? result : createIterResultObject(result, state.done);
      } catch (error) {
        state.done = true;
        throw error;
      }
    },
    'return': function () {
      var state = getInternalState(this);
      var iterator = state.iterator;
      state.done = true;
      if (IS_ITERATOR) {
        var returnMethod = getMethod(iterator, 'return');
        return returnMethod ? call(returnMethod, iterator) : createIterResultObject(undefined, true);
      }
      if (state.inner) try {
        iteratorClose(state.inner.iterator, NORMAL);
      } catch (error) {
        return iteratorClose(iterator, THROW, error);
      }
      if (state.openIters) try {
        iteratorCloseAll(state.openIters, NORMAL);
      } catch (error) {
        return iteratorClose(iterator, THROW, error);
      }
      if (iterator) iteratorClose(iterator, NORMAL);
      return createIterResultObject(undefined, true);
    }
  });
};

var WrapForValidIteratorPrototype = createIteratorProxyPrototype(true);
var IteratorHelperPrototype = createIteratorProxyPrototype(false);

createNonEnumerableProperty(IteratorHelperPrototype, TO_STRING_TAG, 'Iterator Helper');

module.exports = function (nextHandler, IS_ITERATOR, RETURN_HANDLER_RESULT) {
  var IteratorProxy = function Iterator(record, state) {
    if (state) {
      state.iterator = record.iterator;
      state.next = record.next;
    } else state = record;
    state.type = IS_ITERATOR ? WRAP_FOR_VALID_ITERATOR : ITERATOR_HELPER;
    state.returnHandlerResult = !!RETURN_HANDLER_RESULT;
    state.nextHandler = nextHandler;
    state.counter = 0;
    state.done = false;
    setInternalState(this, state);
  };

  IteratorProxy.prototype = IS_ITERATOR ? WrapForValidIteratorPrototype : IteratorHelperPrototype;

  return IteratorProxy;
};


/***/ }),

/***/ "../node_modules/core-js/internals/iterator-helper-throws-on-invalid-iterator.js":
/*!***************************************************************************************!*\
  !*** ../node_modules/core-js/internals/iterator-helper-throws-on-invalid-iterator.js ***!
  \***************************************************************************************/
/***/ ((module) => {

"use strict";

// Should throw an error on invalid iterator
// https://issues.chromium.org/issues/336839115
module.exports = function (methodName, argument) {
  // eslint-disable-next-line es/no-iterator -- required for testing
  var method = typeof Iterator == 'function' && Iterator.prototype[methodName];
  if (method) try {
    method.call({ next: null }, argument).next();
  } catch (error) {
    return true;
  }
};


/***/ }),

/***/ "../node_modules/core-js/internals/iterator-helper-without-closing-on-early-error.js":
/*!*******************************************************************************************!*\
  !*** ../node_modules/core-js/internals/iterator-helper-without-closing-on-early-error.js ***!
  \*******************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");

// https://github.com/tc39/ecma262/pull/3467
module.exports = function (METHOD_NAME, ExpectedError) {
  var Iterator = globalThis.Iterator;
  var IteratorPrototype = Iterator && Iterator.prototype;
  var method = IteratorPrototype && IteratorPrototype[METHOD_NAME];

  var CLOSED = false;

  if (method) try {
    method.call({
      next: function () { return { done: true }; },
      'return': function () { CLOSED = true; }
    }, -1);
  } catch (error) {
    // https://bugs.webkit.org/show_bug.cgi?id=291195
    if (!(error instanceof ExpectedError)) CLOSED = false;
  }

  if (!CLOSED) return method;
};


/***/ }),

/***/ "../node_modules/core-js/internals/iterators-core.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/internals/iterators-core.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var isObject = __webpack_require__(/*! ../internals/is-object */ "../node_modules/core-js/internals/is-object.js");
var create = __webpack_require__(/*! ../internals/object-create */ "../node_modules/core-js/internals/object-create.js");
var getPrototypeOf = __webpack_require__(/*! ../internals/object-get-prototype-of */ "../node_modules/core-js/internals/object-get-prototype-of.js");
var defineBuiltIn = __webpack_require__(/*! ../internals/define-built-in */ "../node_modules/core-js/internals/define-built-in.js");
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "../node_modules/core-js/internals/well-known-symbol.js");
var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ "../node_modules/core-js/internals/is-pure.js");

var ITERATOR = wellKnownSymbol('iterator');
var BUGGY_SAFARI_ITERATORS = false;

// `%IteratorPrototype%` object
// https://tc39.es/ecma262/#sec-%iteratorprototype%-object
var IteratorPrototype, PrototypeOfArrayIteratorPrototype, arrayIterator;

/* eslint-disable es/no-array-prototype-keys -- safe */
if ([].keys) {
  arrayIterator = [].keys();
  // Safari 8 has buggy iterators w/o `next`
  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS = true;
  else {
    PrototypeOfArrayIteratorPrototype = getPrototypeOf(getPrototypeOf(arrayIterator));
    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype = PrototypeOfArrayIteratorPrototype;
  }
}

var NEW_ITERATOR_PROTOTYPE = !isObject(IteratorPrototype) || fails(function () {
  var test = {};
  // FF44- legacy iterators case
  return IteratorPrototype[ITERATOR].call(test) !== test;
});

if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype = {};
else if (IS_PURE) IteratorPrototype = create(IteratorPrototype);

// `%IteratorPrototype%[@@iterator]()` method
// https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
if (!isCallable(IteratorPrototype[ITERATOR])) {
  defineBuiltIn(IteratorPrototype, ITERATOR, function () {
    return this;
  });
}

module.exports = {
  IteratorPrototype: IteratorPrototype,
  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS
};


/***/ }),

/***/ "../node_modules/core-js/internals/iterators.js":
/*!******************************************************!*\
  !*** ../node_modules/core-js/internals/iterators.js ***!
  \******************************************************/
/***/ ((module) => {

"use strict";

module.exports = {};


/***/ }),

/***/ "../node_modules/core-js/internals/length-of-array-like.js":
/*!*****************************************************************!*\
  !*** ../node_modules/core-js/internals/length-of-array-like.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toLength = __webpack_require__(/*! ../internals/to-length */ "../node_modules/core-js/internals/to-length.js");

// `LengthOfArrayLike` abstract operation
// https://tc39.es/ecma262/#sec-lengthofarraylike
module.exports = function (obj) {
  return toLength(obj.length);
};


/***/ }),

/***/ "../node_modules/core-js/internals/make-built-in.js":
/*!**********************************************************!*\
  !*** ../node_modules/core-js/internals/make-built-in.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");
var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");
var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var CONFIGURABLE_FUNCTION_NAME = (__webpack_require__(/*! ../internals/function-name */ "../node_modules/core-js/internals/function-name.js").CONFIGURABLE);
var inspectSource = __webpack_require__(/*! ../internals/inspect-source */ "../node_modules/core-js/internals/inspect-source.js");
var InternalStateModule = __webpack_require__(/*! ../internals/internal-state */ "../node_modules/core-js/internals/internal-state.js");

var enforceInternalState = InternalStateModule.enforce;
var getInternalState = InternalStateModule.get;
var $String = String;
// eslint-disable-next-line es/no-object-defineproperty -- safe
var defineProperty = Object.defineProperty;
var stringSlice = uncurryThis(''.slice);
var replace = uncurryThis(''.replace);
var join = uncurryThis([].join);

var CONFIGURABLE_LENGTH = DESCRIPTORS && !fails(function () {
  return defineProperty(function () { /* empty */ }, 'length', { value: 8 }).length !== 8;
});

var TEMPLATE = String(String).split('String');

var makeBuiltIn = module.exports = function (value, name, options) {
  if (stringSlice($String(name), 0, 7) === 'Symbol(') {
    name = '[' + replace($String(name), /^Symbol\(([^)]*)\).*$/, '$1') + ']';
  }
  if (options && options.getter) name = 'get ' + name;
  if (options && options.setter) name = 'set ' + name;
  if (!hasOwn(value, 'name') || (CONFIGURABLE_FUNCTION_NAME && value.name !== name)) {
    if (DESCRIPTORS) defineProperty(value, 'name', { value: name, configurable: true });
    else value.name = name;
  }
  if (CONFIGURABLE_LENGTH && options && hasOwn(options, 'arity') && value.length !== options.arity) {
    defineProperty(value, 'length', { value: options.arity });
  }
  try {
    if (options && hasOwn(options, 'constructor') && options.constructor) {
      if (DESCRIPTORS) defineProperty(value, 'prototype', { writable: false });
    // in V8 ~ Chrome 53, prototypes of some methods, like `Array.prototype.values`, are non-writable
    } else if (value.prototype) value.prototype = undefined;
  } catch (error) { /* empty */ }
  var state = enforceInternalState(value);
  if (!hasOwn(state, 'source')) {
    state.source = join(TEMPLATE, typeof name == 'string' ? name : '');
  } return value;
};

// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
// eslint-disable-next-line no-extend-native -- required
Function.prototype.toString = makeBuiltIn(function toString() {
  return isCallable(this) && getInternalState(this).source || inspectSource(this);
}, 'toString');


/***/ }),

/***/ "../node_modules/core-js/internals/math-trunc.js":
/*!*******************************************************!*\
  !*** ../node_modules/core-js/internals/math-trunc.js ***!
  \*******************************************************/
/***/ ((module) => {

"use strict";

var ceil = Math.ceil;
var floor = Math.floor;

// `Math.trunc` method
// https://tc39.es/ecma262/#sec-math.trunc
// eslint-disable-next-line es/no-math-trunc -- safe
module.exports = Math.trunc || function trunc(x) {
  var n = +x;
  return (n > 0 ? floor : ceil)(n);
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-create.js":
/*!**********************************************************!*\
  !*** ../node_modules/core-js/internals/object-create.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

/* global ActiveXObject -- old IE, WSH */
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var definePropertiesModule = __webpack_require__(/*! ../internals/object-define-properties */ "../node_modules/core-js/internals/object-define-properties.js");
var enumBugKeys = __webpack_require__(/*! ../internals/enum-bug-keys */ "../node_modules/core-js/internals/enum-bug-keys.js");
var hiddenKeys = __webpack_require__(/*! ../internals/hidden-keys */ "../node_modules/core-js/internals/hidden-keys.js");
var html = __webpack_require__(/*! ../internals/html */ "../node_modules/core-js/internals/html.js");
var documentCreateElement = __webpack_require__(/*! ../internals/document-create-element */ "../node_modules/core-js/internals/document-create-element.js");
var sharedKey = __webpack_require__(/*! ../internals/shared-key */ "../node_modules/core-js/internals/shared-key.js");

var GT = '>';
var LT = '<';
var PROTOTYPE = 'prototype';
var SCRIPT = 'script';
var IE_PROTO = sharedKey('IE_PROTO');

var EmptyConstructor = function () { /* empty */ };

var scriptTag = function (content) {
  return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
};

// Create object with fake `null` prototype: use ActiveX Object with cleared prototype
var NullProtoObjectViaActiveX = function (activeXDocument) {
  activeXDocument.write(scriptTag(''));
  activeXDocument.close();
  var temp = activeXDocument.parentWindow.Object;
  // eslint-disable-next-line no-useless-assignment -- avoid memory leak
  activeXDocument = null;
  return temp;
};

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var NullProtoObjectViaIFrame = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = documentCreateElement('iframe');
  var JS = 'java' + SCRIPT + ':';
  var iframeDocument;
  iframe.style.display = 'none';
  html.appendChild(iframe);
  // https://github.com/zloirock/core-js/issues/475
  iframe.src = String(JS);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(scriptTag('document.F=Object'));
  iframeDocument.close();
  return iframeDocument.F;
};

// Check for document.domain and active x support
// No need to use active x approach when document.domain is not set
// see https://github.com/es-shims/es5-shim/issues/150
// variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
// avoid IE GC bug
var activeXDocument;
var NullProtoObject = function () {
  try {
    activeXDocument = new ActiveXObject('htmlfile');
  } catch (error) { /* ignore */ }
  NullProtoObject = typeof document != 'undefined'
    ? document.domain && activeXDocument
      ? NullProtoObjectViaActiveX(activeXDocument) // old IE
      : NullProtoObjectViaIFrame()
    : NullProtoObjectViaActiveX(activeXDocument); // WSH
  var length = enumBugKeys.length;
  while (length--) delete NullProtoObject[PROTOTYPE][enumBugKeys[length]];
  return NullProtoObject();
};

hiddenKeys[IE_PROTO] = true;

// `Object.create` method
// https://tc39.es/ecma262/#sec-object.create
// eslint-disable-next-line es/no-object-create -- safe
module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    EmptyConstructor[PROTOTYPE] = anObject(O);
    result = new EmptyConstructor();
    EmptyConstructor[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = NullProtoObject();
  return Properties === undefined ? result : definePropertiesModule.f(result, Properties);
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-define-properties.js":
/*!*********************************************************************!*\
  !*** ../node_modules/core-js/internals/object-define-properties.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var V8_PROTOTYPE_DEFINE_BUG = __webpack_require__(/*! ../internals/v8-prototype-define-bug */ "../node_modules/core-js/internals/v8-prototype-define-bug.js");
var definePropertyModule = __webpack_require__(/*! ../internals/object-define-property */ "../node_modules/core-js/internals/object-define-property.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ "../node_modules/core-js/internals/to-indexed-object.js");
var objectKeys = __webpack_require__(/*! ../internals/object-keys */ "../node_modules/core-js/internals/object-keys.js");

// `Object.defineProperties` method
// https://tc39.es/ecma262/#sec-object.defineproperties
// eslint-disable-next-line es/no-object-defineproperties -- safe
exports.f = DESCRIPTORS && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var props = toIndexedObject(Properties);
  var keys = objectKeys(Properties);
  var length = keys.length;
  var index = 0;
  var key;
  while (length > index) definePropertyModule.f(O, key = keys[index++], props[key]);
  return O;
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-define-property.js":
/*!*******************************************************************!*\
  !*** ../node_modules/core-js/internals/object-define-property.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var IE8_DOM_DEFINE = __webpack_require__(/*! ../internals/ie8-dom-define */ "../node_modules/core-js/internals/ie8-dom-define.js");
var V8_PROTOTYPE_DEFINE_BUG = __webpack_require__(/*! ../internals/v8-prototype-define-bug */ "../node_modules/core-js/internals/v8-prototype-define-bug.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var toPropertyKey = __webpack_require__(/*! ../internals/to-property-key */ "../node_modules/core-js/internals/to-property-key.js");

var $TypeError = TypeError;
// eslint-disable-next-line es/no-object-defineproperty -- safe
var $defineProperty = Object.defineProperty;
// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
var ENUMERABLE = 'enumerable';
var CONFIGURABLE = 'configurable';
var WRITABLE = 'writable';

// `Object.defineProperty` method
// https://tc39.es/ecma262/#sec-object.defineproperty
exports.f = DESCRIPTORS ? V8_PROTOTYPE_DEFINE_BUG ? function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPropertyKey(P);
  anObject(Attributes);
  if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
    var current = $getOwnPropertyDescriptor(O, P);
    if (current && current[WRITABLE]) {
      O[P] = Attributes.value;
      Attributes = {
        configurable: CONFIGURABLE in Attributes ? Attributes[CONFIGURABLE] : current[CONFIGURABLE],
        enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
        writable: false
      };
    }
  } return $defineProperty(O, P, Attributes);
} : $defineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPropertyKey(P);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return $defineProperty(O, P, Attributes);
  } catch (error) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw new $TypeError('Accessors not supported');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-get-own-property-descriptor.js":
/*!*******************************************************************************!*\
  !*** ../node_modules/core-js/internals/object-get-own-property-descriptor.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var propertyIsEnumerableModule = __webpack_require__(/*! ../internals/object-property-is-enumerable */ "../node_modules/core-js/internals/object-property-is-enumerable.js");
var createPropertyDescriptor = __webpack_require__(/*! ../internals/create-property-descriptor */ "../node_modules/core-js/internals/create-property-descriptor.js");
var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ "../node_modules/core-js/internals/to-indexed-object.js");
var toPropertyKey = __webpack_require__(/*! ../internals/to-property-key */ "../node_modules/core-js/internals/to-property-key.js");
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");
var IE8_DOM_DEFINE = __webpack_require__(/*! ../internals/ie8-dom-define */ "../node_modules/core-js/internals/ie8-dom-define.js");

// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

// `Object.getOwnPropertyDescriptor` method
// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
exports.f = DESCRIPTORS ? $getOwnPropertyDescriptor : function getOwnPropertyDescriptor(O, P) {
  O = toIndexedObject(O);
  P = toPropertyKey(P);
  if (IE8_DOM_DEFINE) try {
    return $getOwnPropertyDescriptor(O, P);
  } catch (error) { /* empty */ }
  if (hasOwn(O, P)) return createPropertyDescriptor(!call(propertyIsEnumerableModule.f, O, P), O[P]);
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-get-own-property-names.js":
/*!**************************************************************************!*\
  !*** ../node_modules/core-js/internals/object-get-own-property-names.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";

var internalObjectKeys = __webpack_require__(/*! ../internals/object-keys-internal */ "../node_modules/core-js/internals/object-keys-internal.js");
var enumBugKeys = __webpack_require__(/*! ../internals/enum-bug-keys */ "../node_modules/core-js/internals/enum-bug-keys.js");

var hiddenKeys = enumBugKeys.concat('length', 'prototype');

// `Object.getOwnPropertyNames` method
// https://tc39.es/ecma262/#sec-object.getownpropertynames
// eslint-disable-next-line es/no-object-getownpropertynames -- safe
exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
  return internalObjectKeys(O, hiddenKeys);
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-get-own-property-symbols.js":
/*!****************************************************************************!*\
  !*** ../node_modules/core-js/internals/object-get-own-property-symbols.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";

// eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
exports.f = Object.getOwnPropertySymbols;


/***/ }),

/***/ "../node_modules/core-js/internals/object-get-prototype-of.js":
/*!********************************************************************!*\
  !*** ../node_modules/core-js/internals/object-get-prototype-of.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var toObject = __webpack_require__(/*! ../internals/to-object */ "../node_modules/core-js/internals/to-object.js");
var sharedKey = __webpack_require__(/*! ../internals/shared-key */ "../node_modules/core-js/internals/shared-key.js");
var CORRECT_PROTOTYPE_GETTER = __webpack_require__(/*! ../internals/correct-prototype-getter */ "../node_modules/core-js/internals/correct-prototype-getter.js");

var IE_PROTO = sharedKey('IE_PROTO');
var $Object = Object;
var ObjectPrototype = $Object.prototype;

// `Object.getPrototypeOf` method
// https://tc39.es/ecma262/#sec-object.getprototypeof
// eslint-disable-next-line es/no-object-getprototypeof -- safe
module.exports = CORRECT_PROTOTYPE_GETTER ? $Object.getPrototypeOf : function (O) {
  var object = toObject(O);
  if (hasOwn(object, IE_PROTO)) return object[IE_PROTO];
  var constructor = object.constructor;
  if (isCallable(constructor) && object instanceof constructor) {
    return constructor.prototype;
  } return object instanceof $Object ? ObjectPrototype : null;
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-is-prototype-of.js":
/*!*******************************************************************!*\
  !*** ../node_modules/core-js/internals/object-is-prototype-of.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");

module.exports = uncurryThis({}.isPrototypeOf);


/***/ }),

/***/ "../node_modules/core-js/internals/object-keys-internal.js":
/*!*****************************************************************!*\
  !*** ../node_modules/core-js/internals/object-keys-internal.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");
var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ "../node_modules/core-js/internals/to-indexed-object.js");
var indexOf = (__webpack_require__(/*! ../internals/array-includes */ "../node_modules/core-js/internals/array-includes.js").indexOf);
var hiddenKeys = __webpack_require__(/*! ../internals/hidden-keys */ "../node_modules/core-js/internals/hidden-keys.js");

var push = uncurryThis([].push);

module.exports = function (object, names) {
  var O = toIndexedObject(object);
  var i = 0;
  var result = [];
  var key;
  for (key in O) !hasOwn(hiddenKeys, key) && hasOwn(O, key) && push(result, key);
  // Don't enum bug & hidden keys
  while (names.length > i) if (hasOwn(O, key = names[i++])) {
    ~indexOf(result, key) || push(result, key);
  }
  return result;
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-keys.js":
/*!********************************************************!*\
  !*** ../node_modules/core-js/internals/object-keys.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var internalObjectKeys = __webpack_require__(/*! ../internals/object-keys-internal */ "../node_modules/core-js/internals/object-keys-internal.js");
var enumBugKeys = __webpack_require__(/*! ../internals/enum-bug-keys */ "../node_modules/core-js/internals/enum-bug-keys.js");

// `Object.keys` method
// https://tc39.es/ecma262/#sec-object.keys
// eslint-disable-next-line es/no-object-keys -- safe
module.exports = Object.keys || function keys(O) {
  return internalObjectKeys(O, enumBugKeys);
};


/***/ }),

/***/ "../node_modules/core-js/internals/object-property-is-enumerable.js":
/*!**************************************************************************!*\
  !*** ../node_modules/core-js/internals/object-property-is-enumerable.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";

var $propertyIsEnumerable = {}.propertyIsEnumerable;
// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

// Nashorn ~ JDK8 bug
var NASHORN_BUG = getOwnPropertyDescriptor && !$propertyIsEnumerable.call({ 1: 2 }, 1);

// `Object.prototype.propertyIsEnumerable` method implementation
// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
exports.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
  var descriptor = getOwnPropertyDescriptor(this, V);
  return !!descriptor && descriptor.enumerable;
} : $propertyIsEnumerable;


/***/ }),

/***/ "../node_modules/core-js/internals/ordinary-to-primitive.js":
/*!******************************************************************!*\
  !*** ../node_modules/core-js/internals/ordinary-to-primitive.js ***!
  \******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var isObject = __webpack_require__(/*! ../internals/is-object */ "../node_modules/core-js/internals/is-object.js");

var $TypeError = TypeError;

// `OrdinaryToPrimitive` abstract operation
// https://tc39.es/ecma262/#sec-ordinarytoprimitive
module.exports = function (input, pref) {
  var fn, val;
  if (pref === 'string' && isCallable(fn = input.toString) && !isObject(val = call(fn, input))) return val;
  if (isCallable(fn = input.valueOf) && !isObject(val = call(fn, input))) return val;
  if (pref !== 'string' && isCallable(fn = input.toString) && !isObject(val = call(fn, input))) return val;
  throw new $TypeError("Can't convert object to primitive value");
};


/***/ }),

/***/ "../node_modules/core-js/internals/own-keys.js":
/*!*****************************************************!*\
  !*** ../node_modules/core-js/internals/own-keys.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var getBuiltIn = __webpack_require__(/*! ../internals/get-built-in */ "../node_modules/core-js/internals/get-built-in.js");
var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");
var getOwnPropertyNamesModule = __webpack_require__(/*! ../internals/object-get-own-property-names */ "../node_modules/core-js/internals/object-get-own-property-names.js");
var getOwnPropertySymbolsModule = __webpack_require__(/*! ../internals/object-get-own-property-symbols */ "../node_modules/core-js/internals/object-get-own-property-symbols.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");

var concat = uncurryThis([].concat);

// all object keys, includes non-enumerable and symbols
module.exports = getBuiltIn('Reflect', 'ownKeys') || function ownKeys(it) {
  var keys = getOwnPropertyNamesModule.f(anObject(it));
  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
  return getOwnPropertySymbols ? concat(keys, getOwnPropertySymbols(it)) : keys;
};


/***/ }),

/***/ "../node_modules/core-js/internals/require-object-coercible.js":
/*!*********************************************************************!*\
  !*** ../node_modules/core-js/internals/require-object-coercible.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var isNullOrUndefined = __webpack_require__(/*! ../internals/is-null-or-undefined */ "../node_modules/core-js/internals/is-null-or-undefined.js");

var $TypeError = TypeError;

// `RequireObjectCoercible` abstract operation
// https://tc39.es/ecma262/#sec-requireobjectcoercible
module.exports = function (it) {
  if (isNullOrUndefined(it)) throw new $TypeError("Can't call method on " + it);
  return it;
};


/***/ }),

/***/ "../node_modules/core-js/internals/shared-key.js":
/*!*******************************************************!*\
  !*** ../node_modules/core-js/internals/shared-key.js ***!
  \*******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var shared = __webpack_require__(/*! ../internals/shared */ "../node_modules/core-js/internals/shared.js");
var uid = __webpack_require__(/*! ../internals/uid */ "../node_modules/core-js/internals/uid.js");

var keys = shared('keys');

module.exports = function (key) {
  return keys[key] || (keys[key] = uid(key));
};


/***/ }),

/***/ "../node_modules/core-js/internals/shared-store.js":
/*!*********************************************************!*\
  !*** ../node_modules/core-js/internals/shared-store.js ***!
  \*********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ "../node_modules/core-js/internals/is-pure.js");
var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var defineGlobalProperty = __webpack_require__(/*! ../internals/define-global-property */ "../node_modules/core-js/internals/define-global-property.js");

var SHARED = '__core-js_shared__';
var store = module.exports = globalThis[SHARED] || defineGlobalProperty(SHARED, {});

(store.versions || (store.versions = [])).push({
  version: '3.43.0',
  mode: IS_PURE ? 'pure' : 'global',
  copyright: '© 2014-2025 Denis Pushkarev (zloirock.ru)',
  license: 'https://github.com/zloirock/core-js/blob/v3.43.0/LICENSE',
  source: 'https://github.com/zloirock/core-js'
});


/***/ }),

/***/ "../node_modules/core-js/internals/shared.js":
/*!***************************************************!*\
  !*** ../node_modules/core-js/internals/shared.js ***!
  \***************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var store = __webpack_require__(/*! ../internals/shared-store */ "../node_modules/core-js/internals/shared-store.js");

module.exports = function (key, value) {
  return store[key] || (store[key] = value || {});
};


/***/ }),

/***/ "../node_modules/core-js/internals/symbol-constructor-detection.js":
/*!*************************************************************************!*\
  !*** ../node_modules/core-js/internals/symbol-constructor-detection.js ***!
  \*************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

/* eslint-disable es/no-symbol -- required for testing */
var V8_VERSION = __webpack_require__(/*! ../internals/environment-v8-version */ "../node_modules/core-js/internals/environment-v8-version.js");
var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");
var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");

var $String = globalThis.String;

// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
module.exports = !!Object.getOwnPropertySymbols && !fails(function () {
  var symbol = Symbol('symbol detection');
  // Chrome 38 Symbol has incorrect toString conversion
  // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
  // nb: Do not call `String` directly to avoid this being optimized out to `symbol+''` which will,
  // of course, fail.
  return !$String(symbol) || !(Object(symbol) instanceof Symbol) ||
    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
    !Symbol.sham && V8_VERSION && V8_VERSION < 41;
});


/***/ }),

/***/ "../node_modules/core-js/internals/to-absolute-index.js":
/*!**************************************************************!*\
  !*** ../node_modules/core-js/internals/to-absolute-index.js ***!
  \**************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toIntegerOrInfinity = __webpack_require__(/*! ../internals/to-integer-or-infinity */ "../node_modules/core-js/internals/to-integer-or-infinity.js");

var max = Math.max;
var min = Math.min;

// Helper for a popular repeating case of the spec:
// Let integer be ? ToInteger(index).
// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
module.exports = function (index, length) {
  var integer = toIntegerOrInfinity(index);
  return integer < 0 ? max(integer + length, 0) : min(integer, length);
};


/***/ }),

/***/ "../node_modules/core-js/internals/to-indexed-object.js":
/*!**************************************************************!*\
  !*** ../node_modules/core-js/internals/to-indexed-object.js ***!
  \**************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// toObject with fallback for non-array-like ES3 strings
var IndexedObject = __webpack_require__(/*! ../internals/indexed-object */ "../node_modules/core-js/internals/indexed-object.js");
var requireObjectCoercible = __webpack_require__(/*! ../internals/require-object-coercible */ "../node_modules/core-js/internals/require-object-coercible.js");

module.exports = function (it) {
  return IndexedObject(requireObjectCoercible(it));
};


/***/ }),

/***/ "../node_modules/core-js/internals/to-integer-or-infinity.js":
/*!*******************************************************************!*\
  !*** ../node_modules/core-js/internals/to-integer-or-infinity.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var trunc = __webpack_require__(/*! ../internals/math-trunc */ "../node_modules/core-js/internals/math-trunc.js");

// `ToIntegerOrInfinity` abstract operation
// https://tc39.es/ecma262/#sec-tointegerorinfinity
module.exports = function (argument) {
  var number = +argument;
  // eslint-disable-next-line no-self-compare -- NaN check
  return number !== number || number === 0 ? 0 : trunc(number);
};


/***/ }),

/***/ "../node_modules/core-js/internals/to-length.js":
/*!******************************************************!*\
  !*** ../node_modules/core-js/internals/to-length.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toIntegerOrInfinity = __webpack_require__(/*! ../internals/to-integer-or-infinity */ "../node_modules/core-js/internals/to-integer-or-infinity.js");

var min = Math.min;

// `ToLength` abstract operation
// https://tc39.es/ecma262/#sec-tolength
module.exports = function (argument) {
  var len = toIntegerOrInfinity(argument);
  return len > 0 ? min(len, 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
};


/***/ }),

/***/ "../node_modules/core-js/internals/to-object.js":
/*!******************************************************!*\
  !*** ../node_modules/core-js/internals/to-object.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var requireObjectCoercible = __webpack_require__(/*! ../internals/require-object-coercible */ "../node_modules/core-js/internals/require-object-coercible.js");

var $Object = Object;

// `ToObject` abstract operation
// https://tc39.es/ecma262/#sec-toobject
module.exports = function (argument) {
  return $Object(requireObjectCoercible(argument));
};


/***/ }),

/***/ "../node_modules/core-js/internals/to-primitive.js":
/*!*********************************************************!*\
  !*** ../node_modules/core-js/internals/to-primitive.js ***!
  \*********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var isObject = __webpack_require__(/*! ../internals/is-object */ "../node_modules/core-js/internals/is-object.js");
var isSymbol = __webpack_require__(/*! ../internals/is-symbol */ "../node_modules/core-js/internals/is-symbol.js");
var getMethod = __webpack_require__(/*! ../internals/get-method */ "../node_modules/core-js/internals/get-method.js");
var ordinaryToPrimitive = __webpack_require__(/*! ../internals/ordinary-to-primitive */ "../node_modules/core-js/internals/ordinary-to-primitive.js");
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "../node_modules/core-js/internals/well-known-symbol.js");

var $TypeError = TypeError;
var TO_PRIMITIVE = wellKnownSymbol('toPrimitive');

// `ToPrimitive` abstract operation
// https://tc39.es/ecma262/#sec-toprimitive
module.exports = function (input, pref) {
  if (!isObject(input) || isSymbol(input)) return input;
  var exoticToPrim = getMethod(input, TO_PRIMITIVE);
  var result;
  if (exoticToPrim) {
    if (pref === undefined) pref = 'default';
    result = call(exoticToPrim, input, pref);
    if (!isObject(result) || isSymbol(result)) return result;
    throw new $TypeError("Can't convert object to primitive value");
  }
  if (pref === undefined) pref = 'number';
  return ordinaryToPrimitive(input, pref);
};


/***/ }),

/***/ "../node_modules/core-js/internals/to-property-key.js":
/*!************************************************************!*\
  !*** ../node_modules/core-js/internals/to-property-key.js ***!
  \************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var toPrimitive = __webpack_require__(/*! ../internals/to-primitive */ "../node_modules/core-js/internals/to-primitive.js");
var isSymbol = __webpack_require__(/*! ../internals/is-symbol */ "../node_modules/core-js/internals/is-symbol.js");

// `ToPropertyKey` abstract operation
// https://tc39.es/ecma262/#sec-topropertykey
module.exports = function (argument) {
  var key = toPrimitive(argument, 'string');
  return isSymbol(key) ? key : key + '';
};


/***/ }),

/***/ "../node_modules/core-js/internals/to-string-tag-support.js":
/*!******************************************************************!*\
  !*** ../node_modules/core-js/internals/to-string-tag-support.js ***!
  \******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "../node_modules/core-js/internals/well-known-symbol.js");

var TO_STRING_TAG = wellKnownSymbol('toStringTag');
var test = {};

test[TO_STRING_TAG] = 'z';

module.exports = String(test) === '[object z]';


/***/ }),

/***/ "../node_modules/core-js/internals/try-to-string.js":
/*!**********************************************************!*\
  !*** ../node_modules/core-js/internals/try-to-string.js ***!
  \**********************************************************/
/***/ ((module) => {

"use strict";

var $String = String;

module.exports = function (argument) {
  try {
    return $String(argument);
  } catch (error) {
    return 'Object';
  }
};


/***/ }),

/***/ "../node_modules/core-js/internals/uid.js":
/*!************************************************!*\
  !*** ../node_modules/core-js/internals/uid.js ***!
  \************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "../node_modules/core-js/internals/function-uncurry-this.js");

var id = 0;
var postfix = Math.random();
var toString = uncurryThis(1.1.toString);

module.exports = function (key) {
  return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString(++id + postfix, 36);
};


/***/ }),

/***/ "../node_modules/core-js/internals/use-symbol-as-uid.js":
/*!**************************************************************!*\
  !*** ../node_modules/core-js/internals/use-symbol-as-uid.js ***!
  \**************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

/* eslint-disable es/no-symbol -- required for testing */
var NATIVE_SYMBOL = __webpack_require__(/*! ../internals/symbol-constructor-detection */ "../node_modules/core-js/internals/symbol-constructor-detection.js");

module.exports = NATIVE_SYMBOL &&
  !Symbol.sham &&
  typeof Symbol.iterator == 'symbol';


/***/ }),

/***/ "../node_modules/core-js/internals/v8-prototype-define-bug.js":
/*!********************************************************************!*\
  !*** ../node_modules/core-js/internals/v8-prototype-define-bug.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");

// V8 ~ Chrome 36-
// https://bugs.chromium.org/p/v8/issues/detail?id=3334
module.exports = DESCRIPTORS && fails(function () {
  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
  return Object.defineProperty(function () { /* empty */ }, 'prototype', {
    value: 42,
    writable: false
  }).prototype !== 42;
});


/***/ }),

/***/ "../node_modules/core-js/internals/weak-map-basic-detection.js":
/*!*********************************************************************!*\
  !*** ../node_modules/core-js/internals/weak-map-basic-detection.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");

var WeakMap = globalThis.WeakMap;

module.exports = isCallable(WeakMap) && /native code/.test(String(WeakMap));


/***/ }),

/***/ "../node_modules/core-js/internals/well-known-symbol.js":
/*!**************************************************************!*\
  !*** ../node_modules/core-js/internals/well-known-symbol.js ***!
  \**************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var shared = __webpack_require__(/*! ../internals/shared */ "../node_modules/core-js/internals/shared.js");
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");
var uid = __webpack_require__(/*! ../internals/uid */ "../node_modules/core-js/internals/uid.js");
var NATIVE_SYMBOL = __webpack_require__(/*! ../internals/symbol-constructor-detection */ "../node_modules/core-js/internals/symbol-constructor-detection.js");
var USE_SYMBOL_AS_UID = __webpack_require__(/*! ../internals/use-symbol-as-uid */ "../node_modules/core-js/internals/use-symbol-as-uid.js");

var Symbol = globalThis.Symbol;
var WellKnownSymbolsStore = shared('wks');
var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol['for'] || Symbol : Symbol && Symbol.withoutSetter || uid;

module.exports = function (name) {
  if (!hasOwn(WellKnownSymbolsStore, name)) {
    WellKnownSymbolsStore[name] = NATIVE_SYMBOL && hasOwn(Symbol, name)
      ? Symbol[name]
      : createWellKnownSymbol('Symbol.' + name);
  } return WellKnownSymbolsStore[name];
};


/***/ }),

/***/ "../node_modules/core-js/modules/es.array.push.js":
/*!********************************************************!*\
  !*** ../node_modules/core-js/modules/es.array.push.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "../node_modules/core-js/internals/export.js");
var toObject = __webpack_require__(/*! ../internals/to-object */ "../node_modules/core-js/internals/to-object.js");
var lengthOfArrayLike = __webpack_require__(/*! ../internals/length-of-array-like */ "../node_modules/core-js/internals/length-of-array-like.js");
var setArrayLength = __webpack_require__(/*! ../internals/array-set-length */ "../node_modules/core-js/internals/array-set-length.js");
var doesNotExceedSafeInteger = __webpack_require__(/*! ../internals/does-not-exceed-safe-integer */ "../node_modules/core-js/internals/does-not-exceed-safe-integer.js");
var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");

var INCORRECT_TO_LENGTH = fails(function () {
  return [].push.call({ length: 0x100000000 }, 1) !== 4294967297;
});

// V8 <= 121 and Safari <= 15.4; FF < 23 throws InternalError
// https://bugs.chromium.org/p/v8/issues/detail?id=12681
var properErrorOnNonWritableLength = function () {
  try {
    // eslint-disable-next-line es/no-object-defineproperty -- safe
    Object.defineProperty([], 'length', { writable: false }).push();
  } catch (error) {
    return error instanceof TypeError;
  }
};

var FORCED = INCORRECT_TO_LENGTH || !properErrorOnNonWritableLength();

// `Array.prototype.push` method
// https://tc39.es/ecma262/#sec-array.prototype.push
$({ target: 'Array', proto: true, arity: 1, forced: FORCED }, {
  // eslint-disable-next-line no-unused-vars -- required for `.length`
  push: function push(item) {
    var O = toObject(this);
    var len = lengthOfArrayLike(O);
    var argCount = arguments.length;
    doesNotExceedSafeInteger(len + argCount);
    for (var i = 0; i < argCount; i++) {
      O[len] = arguments[i];
      len++;
    }
    setArrayLength(O, len);
    return len;
  }
});


/***/ }),

/***/ "../node_modules/core-js/modules/es.iterator.constructor.js":
/*!******************************************************************!*\
  !*** ../node_modules/core-js/modules/es.iterator.constructor.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "../node_modules/core-js/internals/export.js");
var globalThis = __webpack_require__(/*! ../internals/global-this */ "../node_modules/core-js/internals/global-this.js");
var anInstance = __webpack_require__(/*! ../internals/an-instance */ "../node_modules/core-js/internals/an-instance.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "../node_modules/core-js/internals/is-callable.js");
var getPrototypeOf = __webpack_require__(/*! ../internals/object-get-prototype-of */ "../node_modules/core-js/internals/object-get-prototype-of.js");
var defineBuiltInAccessor = __webpack_require__(/*! ../internals/define-built-in-accessor */ "../node_modules/core-js/internals/define-built-in-accessor.js");
var createProperty = __webpack_require__(/*! ../internals/create-property */ "../node_modules/core-js/internals/create-property.js");
var fails = __webpack_require__(/*! ../internals/fails */ "../node_modules/core-js/internals/fails.js");
var hasOwn = __webpack_require__(/*! ../internals/has-own-property */ "../node_modules/core-js/internals/has-own-property.js");
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "../node_modules/core-js/internals/well-known-symbol.js");
var IteratorPrototype = (__webpack_require__(/*! ../internals/iterators-core */ "../node_modules/core-js/internals/iterators-core.js").IteratorPrototype);
var DESCRIPTORS = __webpack_require__(/*! ../internals/descriptors */ "../node_modules/core-js/internals/descriptors.js");
var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ "../node_modules/core-js/internals/is-pure.js");

var CONSTRUCTOR = 'constructor';
var ITERATOR = 'Iterator';
var TO_STRING_TAG = wellKnownSymbol('toStringTag');

var $TypeError = TypeError;
var NativeIterator = globalThis[ITERATOR];

// FF56- have non-standard global helper `Iterator`
var FORCED = IS_PURE
  || !isCallable(NativeIterator)
  || NativeIterator.prototype !== IteratorPrototype
  // FF44- non-standard `Iterator` passes previous tests
  || !fails(function () { NativeIterator({}); });

var IteratorConstructor = function Iterator() {
  anInstance(this, IteratorPrototype);
  if (getPrototypeOf(this) === IteratorPrototype) throw new $TypeError('Abstract class Iterator not directly constructable');
};

var defineIteratorPrototypeAccessor = function (key, value) {
  if (DESCRIPTORS) {
    defineBuiltInAccessor(IteratorPrototype, key, {
      configurable: true,
      get: function () {
        return value;
      },
      set: function (replacement) {
        anObject(this);
        if (this === IteratorPrototype) throw new $TypeError("You can't redefine this property");
        if (hasOwn(this, key)) this[key] = replacement;
        else createProperty(this, key, replacement);
      }
    });
  } else IteratorPrototype[key] = value;
};

if (!hasOwn(IteratorPrototype, TO_STRING_TAG)) defineIteratorPrototypeAccessor(TO_STRING_TAG, ITERATOR);

if (FORCED || !hasOwn(IteratorPrototype, CONSTRUCTOR) || IteratorPrototype[CONSTRUCTOR] === Object) {
  defineIteratorPrototypeAccessor(CONSTRUCTOR, IteratorConstructor);
}

IteratorConstructor.prototype = IteratorPrototype;

// `Iterator` constructor
// https://tc39.es/ecma262/#sec-iterator
$({ global: true, constructor: true, forced: FORCED }, {
  Iterator: IteratorConstructor
});


/***/ }),

/***/ "../node_modules/core-js/modules/es.iterator.filter.js":
/*!*************************************************************!*\
  !*** ../node_modules/core-js/modules/es.iterator.filter.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "../node_modules/core-js/internals/export.js");
var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "../node_modules/core-js/internals/a-callable.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ "../node_modules/core-js/internals/get-iterator-direct.js");
var createIteratorProxy = __webpack_require__(/*! ../internals/iterator-create-proxy */ "../node_modules/core-js/internals/iterator-create-proxy.js");
var callWithSafeIterationClosing = __webpack_require__(/*! ../internals/call-with-safe-iteration-closing */ "../node_modules/core-js/internals/call-with-safe-iteration-closing.js");
var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ "../node_modules/core-js/internals/is-pure.js");
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");
var iteratorHelperThrowsOnInvalidIterator = __webpack_require__(/*! ../internals/iterator-helper-throws-on-invalid-iterator */ "../node_modules/core-js/internals/iterator-helper-throws-on-invalid-iterator.js");
var iteratorHelperWithoutClosingOnEarlyError = __webpack_require__(/*! ../internals/iterator-helper-without-closing-on-early-error */ "../node_modules/core-js/internals/iterator-helper-without-closing-on-early-error.js");

var FILTER_WITHOUT_THROWING_ON_INVALID_ITERATOR = !IS_PURE && !iteratorHelperThrowsOnInvalidIterator('filter', function () { /* empty */ });
var filterWithoutClosingOnEarlyError = !IS_PURE && !FILTER_WITHOUT_THROWING_ON_INVALID_ITERATOR
  && iteratorHelperWithoutClosingOnEarlyError('filter', TypeError);

var FORCED = IS_PURE || FILTER_WITHOUT_THROWING_ON_INVALID_ITERATOR || filterWithoutClosingOnEarlyError;

var IteratorProxy = createIteratorProxy(function () {
  var iterator = this.iterator;
  var predicate = this.predicate;
  var next = this.next;
  var result, done, value;
  while (true) {
    result = anObject(call(next, iterator));
    done = this.done = !!result.done;
    if (done) return;
    value = result.value;
    if (callWithSafeIterationClosing(iterator, predicate, [value, this.counter++], true)) return value;
  }
});

// `Iterator.prototype.filter` method
// https://tc39.es/ecma262/#sec-iterator.prototype.filter
$({ target: 'Iterator', proto: true, real: true, forced: FORCED }, {
  filter: function filter(predicate) {
    anObject(this);
    try {
      aCallable(predicate);
    } catch (error) {
      iteratorClose(this, 'throw', error);
    }

    if (filterWithoutClosingOnEarlyError) return call(filterWithoutClosingOnEarlyError, this, predicate);

    return new IteratorProxy(getIteratorDirect(this), {
      predicate: predicate
    });
  }
});


/***/ }),

/***/ "../node_modules/core-js/modules/es.iterator.find.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/modules/es.iterator.find.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "../node_modules/core-js/internals/export.js");
var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var iterate = __webpack_require__(/*! ../internals/iterate */ "../node_modules/core-js/internals/iterate.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "../node_modules/core-js/internals/a-callable.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ "../node_modules/core-js/internals/get-iterator-direct.js");
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");
var iteratorHelperWithoutClosingOnEarlyError = __webpack_require__(/*! ../internals/iterator-helper-without-closing-on-early-error */ "../node_modules/core-js/internals/iterator-helper-without-closing-on-early-error.js");

var findWithoutClosingOnEarlyError = iteratorHelperWithoutClosingOnEarlyError('find', TypeError);

// `Iterator.prototype.find` method
// https://tc39.es/ecma262/#sec-iterator.prototype.find
$({ target: 'Iterator', proto: true, real: true, forced: findWithoutClosingOnEarlyError }, {
  find: function find(predicate) {
    anObject(this);
    try {
      aCallable(predicate);
    } catch (error) {
      iteratorClose(this, 'throw', error);
    }

    if (findWithoutClosingOnEarlyError) return call(findWithoutClosingOnEarlyError, this, predicate);

    var record = getIteratorDirect(this);
    var counter = 0;
    return iterate(record, function (value, stop) {
      if (predicate(value, counter++)) return stop(value);
    }, { IS_RECORD: true, INTERRUPTED: true }).result;
  }
});


/***/ }),

/***/ "../node_modules/core-js/modules/es.iterator.for-each.js":
/*!***************************************************************!*\
  !*** ../node_modules/core-js/modules/es.iterator.for-each.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "../node_modules/core-js/internals/export.js");
var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var iterate = __webpack_require__(/*! ../internals/iterate */ "../node_modules/core-js/internals/iterate.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "../node_modules/core-js/internals/a-callable.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ "../node_modules/core-js/internals/get-iterator-direct.js");
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");
var iteratorHelperWithoutClosingOnEarlyError = __webpack_require__(/*! ../internals/iterator-helper-without-closing-on-early-error */ "../node_modules/core-js/internals/iterator-helper-without-closing-on-early-error.js");

var forEachWithoutClosingOnEarlyError = iteratorHelperWithoutClosingOnEarlyError('forEach', TypeError);

// `Iterator.prototype.forEach` method
// https://tc39.es/ecma262/#sec-iterator.prototype.foreach
$({ target: 'Iterator', proto: true, real: true, forced: forEachWithoutClosingOnEarlyError }, {
  forEach: function forEach(fn) {
    anObject(this);
    try {
      aCallable(fn);
    } catch (error) {
      iteratorClose(this, 'throw', error);
    }

    if (forEachWithoutClosingOnEarlyError) return call(forEachWithoutClosingOnEarlyError, this, fn);

    var record = getIteratorDirect(this);
    var counter = 0;
    iterate(record, function (value) {
      fn(value, counter++);
    }, { IS_RECORD: true });
  }
});


/***/ }),

/***/ "../node_modules/core-js/modules/es.iterator.map.js":
/*!**********************************************************!*\
  !*** ../node_modules/core-js/modules/es.iterator.map.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "../node_modules/core-js/internals/export.js");
var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "../node_modules/core-js/internals/a-callable.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ "../node_modules/core-js/internals/get-iterator-direct.js");
var createIteratorProxy = __webpack_require__(/*! ../internals/iterator-create-proxy */ "../node_modules/core-js/internals/iterator-create-proxy.js");
var callWithSafeIterationClosing = __webpack_require__(/*! ../internals/call-with-safe-iteration-closing */ "../node_modules/core-js/internals/call-with-safe-iteration-closing.js");
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");
var iteratorHelperThrowsOnInvalidIterator = __webpack_require__(/*! ../internals/iterator-helper-throws-on-invalid-iterator */ "../node_modules/core-js/internals/iterator-helper-throws-on-invalid-iterator.js");
var iteratorHelperWithoutClosingOnEarlyError = __webpack_require__(/*! ../internals/iterator-helper-without-closing-on-early-error */ "../node_modules/core-js/internals/iterator-helper-without-closing-on-early-error.js");
var IS_PURE = __webpack_require__(/*! ../internals/is-pure */ "../node_modules/core-js/internals/is-pure.js");

var MAP_WITHOUT_THROWING_ON_INVALID_ITERATOR = !IS_PURE && !iteratorHelperThrowsOnInvalidIterator('map', function () { /* empty */ });
var mapWithoutClosingOnEarlyError = !IS_PURE && !MAP_WITHOUT_THROWING_ON_INVALID_ITERATOR
  && iteratorHelperWithoutClosingOnEarlyError('map', TypeError);

var FORCED = IS_PURE || MAP_WITHOUT_THROWING_ON_INVALID_ITERATOR || mapWithoutClosingOnEarlyError;

var IteratorProxy = createIteratorProxy(function () {
  var iterator = this.iterator;
  var result = anObject(call(this.next, iterator));
  var done = this.done = !!result.done;
  if (!done) return callWithSafeIterationClosing(iterator, this.mapper, [result.value, this.counter++], true);
});

// `Iterator.prototype.map` method
// https://tc39.es/ecma262/#sec-iterator.prototype.map
$({ target: 'Iterator', proto: true, real: true, forced: FORCED }, {
  map: function map(mapper) {
    anObject(this);
    try {
      aCallable(mapper);
    } catch (error) {
      iteratorClose(this, 'throw', error);
    }

    if (mapWithoutClosingOnEarlyError) return call(mapWithoutClosingOnEarlyError, this, mapper);

    return new IteratorProxy(getIteratorDirect(this), {
      mapper: mapper
    });
  }
});


/***/ }),

/***/ "../node_modules/core-js/modules/es.iterator.some.js":
/*!***********************************************************!*\
  !*** ../node_modules/core-js/modules/es.iterator.some.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "../node_modules/core-js/internals/export.js");
var call = __webpack_require__(/*! ../internals/function-call */ "../node_modules/core-js/internals/function-call.js");
var iterate = __webpack_require__(/*! ../internals/iterate */ "../node_modules/core-js/internals/iterate.js");
var aCallable = __webpack_require__(/*! ../internals/a-callable */ "../node_modules/core-js/internals/a-callable.js");
var anObject = __webpack_require__(/*! ../internals/an-object */ "../node_modules/core-js/internals/an-object.js");
var getIteratorDirect = __webpack_require__(/*! ../internals/get-iterator-direct */ "../node_modules/core-js/internals/get-iterator-direct.js");
var iteratorClose = __webpack_require__(/*! ../internals/iterator-close */ "../node_modules/core-js/internals/iterator-close.js");
var iteratorHelperWithoutClosingOnEarlyError = __webpack_require__(/*! ../internals/iterator-helper-without-closing-on-early-error */ "../node_modules/core-js/internals/iterator-helper-without-closing-on-early-error.js");

var someWithoutClosingOnEarlyError = iteratorHelperWithoutClosingOnEarlyError('some', TypeError);

// `Iterator.prototype.some` method
// https://tc39.es/ecma262/#sec-iterator.prototype.some
$({ target: 'Iterator', proto: true, real: true, forced: someWithoutClosingOnEarlyError }, {
  some: function some(predicate) {
    anObject(this);
    try {
      aCallable(predicate);
    } catch (error) {
      iteratorClose(this, 'throw', error);
    }

    if (someWithoutClosingOnEarlyError) return call(someWithoutClosingOnEarlyError, this, predicate);

    var record = getIteratorDirect(this);
    var counter = 0;
    return iterate(record, function (value, stop) {
      if (predicate(value, counter++)) return stop();
    }, { IS_RECORD: true, INTERRUPTED: true }).stopped;
  }
});


/***/ }),

/***/ "../node_modules/core-js/modules/esnext.iterator.constructor.js":
/*!**********************************************************************!*\
  !*** ../node_modules/core-js/modules/esnext.iterator.constructor.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// TODO: Remove from `core-js@4`
__webpack_require__(/*! ../modules/es.iterator.constructor */ "../node_modules/core-js/modules/es.iterator.constructor.js");


/***/ }),

/***/ "../node_modules/core-js/modules/esnext.iterator.filter.js":
/*!*****************************************************************!*\
  !*** ../node_modules/core-js/modules/esnext.iterator.filter.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// TODO: Remove from `core-js@4`
__webpack_require__(/*! ../modules/es.iterator.filter */ "../node_modules/core-js/modules/es.iterator.filter.js");


/***/ }),

/***/ "../node_modules/core-js/modules/esnext.iterator.find.js":
/*!***************************************************************!*\
  !*** ../node_modules/core-js/modules/esnext.iterator.find.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// TODO: Remove from `core-js@4`
__webpack_require__(/*! ../modules/es.iterator.find */ "../node_modules/core-js/modules/es.iterator.find.js");


/***/ }),

/***/ "../node_modules/core-js/modules/esnext.iterator.for-each.js":
/*!*******************************************************************!*\
  !*** ../node_modules/core-js/modules/esnext.iterator.for-each.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// TODO: Remove from `core-js@4`
__webpack_require__(/*! ../modules/es.iterator.for-each */ "../node_modules/core-js/modules/es.iterator.for-each.js");


/***/ }),

/***/ "../node_modules/core-js/modules/esnext.iterator.map.js":
/*!**************************************************************!*\
  !*** ../node_modules/core-js/modules/esnext.iterator.map.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// TODO: Remove from `core-js@4`
__webpack_require__(/*! ../modules/es.iterator.map */ "../node_modules/core-js/modules/es.iterator.map.js");


/***/ }),

/***/ "../node_modules/core-js/modules/esnext.iterator.some.js":
/*!***************************************************************!*\
  !*** ../node_modules/core-js/modules/esnext.iterator.some.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// TODO: Remove from `core-js@4`
__webpack_require__(/*! ../modules/es.iterator.some */ "../node_modules/core-js/modules/es.iterator.some.js");


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("../assets/dev/js/frontend/modules.js"));
/******/ }
]);
//# sourceMappingURL=frontend-modules.js.map