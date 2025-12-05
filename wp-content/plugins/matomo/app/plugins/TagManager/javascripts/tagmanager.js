/*!
 * Matomo - free/libre analytics platform
 *
 * Matomo Tag Manager
 *
 * @link https://matomo.org
 * @source https://github.com/matomo-org/tag-manager/blob/master/js/piwik.js
 * @license https://matomo.org/free-software/bsd/ BSD-3 Clause (also in js/LICENSE.txt)
 */

/**
 * To minify execute
 * cat javascripts/tagmanager.js | java -jar ../../js/yuicompressor-2.4.8.jar --type js --line-break 1000 | sed 's/^[/][*]/\/*!/' > javascripts/tagmanager.min.js
 */


/* startjslint */
/*jslint browser:true, plusplus:true, vars:true, nomen:true, evil:true, regexp: false, bitwise: true, white: true */
/*global window */
/*global unescape */
/*global ActiveXObject */
(function () {
    var documentAlias = document;
    var windowAlias = window;

    /*!! windowLevelSettingsHook */

    /*!! previewModeHook */

    if (typeof window.MatomoTagManager !== 'object') {

        if (typeof window._mtm !== 'object') {
            window._mtm = [];
        }

        window.MatomoTagManager = (function () {
            var timeScriptLoaded = new Date().getTime();

            function pushDebugLogMessage()
            {
                if (window.mtmPreviewWindow && 'object' === typeof window.mtmPreviewWindow.mtmLogs) {
                    var now = new Date();
                    var messages = [];
                    for (var i = 0; i < arguments.length; i++) {
                        messages.push(JSON.stringify(arguments[i], function( key, value) {
                            if (typeof value === 'object' && value instanceof Node) {
                                return value.nodeName;
                            } else {
                                return value;
                            };
                        }));
                    }
                    window.mtmPreviewWindow.mtmLogs.push({time: now.toLocaleTimeString() + '.' + now.getMilliseconds(), messages: messages});
                }
            }

            function pushDebugEvent(event)
            {
                if (window.mtmPreviewWindow && 'object' === typeof window.mtmPreviewWindow.mtmEvents && event) {
                    var now = new Date();
                    event.time = now.toLocaleTimeString() + '.' + now.getMilliseconds();
                    window.mtmPreviewWindow.mtmEvents.push(event);
                }
            }

            var Debug = {
                enabled: !!window.mtmPreviewWindow,
                log: function () {
                    pushDebugLogMessage.apply(windowAlias, arguments);

                    if (this.enabled && 'undefined' !== typeof console && console && console.debug) {
                        console.debug.apply(console, arguments);
                    }
                },
                error: function () {
                    pushDebugLogMessage.apply(windowAlias, arguments);

                    // cannot be disabled
                    if ('undefined' !== typeof console && console && console.error) {
                        console.error.apply(console, arguments);
                    }
                },
            };

            function throwError(message) {
                Debug.error(message);

                if (typeof TagManager !== 'object' || TagManager.THROW_ERRORS) {
                    throw new Error(message);
                }
            }

            function resolveNestedDotVar(key, obj)
            {
                if (utils.isString(key) && key.indexOf('.') !== -1) {
                    var parts = key.split('.');
                    var i;
                    for (i = 0; i < parts.length; i++) {
                        if (parts[i] in obj) {
                            obj = obj[parts[i]];
                        } else {
                            // value does not exist
                            return;
                        }
                    }
                    return obj;
                }
            }

            function Storage(storageInterface) {
                var namespace = 'mtm:';
                var values = {};

                function hasStorage (method) {
                    return storageInterface in windowAlias && utils.isObject(windowAlias[storageInterface]);
                }

                function hasFeature (method) {
                    return hasStorage() && utils.isFunction(windowAlias[storageInterface][method]);
                }

                function set(group, value) {
                    if (hasFeature('setItem')) {
                        try {
                            windowAlias[storageInterface].setItem(namespace + group, JSON.stringify(value));
                        } catch (e) {}
                    } else {
                        values[group] = value;
                    }
                }

                function get(group) {
                    if (hasFeature('getItem')) {
                        try {
                            var value = windowAlias[storageInterface].getItem(namespace + group);
                            if (value) {
                                value = JSON.parse(value);
                                if (utils.isObject(value)) {
                                    return value;
                                }
                            }
                        } catch(e) {}
                        return {};
                    } else {
                        if (group in values) {
                            return values[group];
                        }
                    }
                }

                function remove(group) {
                    if (hasFeature('removeItem')) {
                        try {
                            windowAlias[storageInterface].removeItem(namespace + group);
                        } catch(e) {}
                    } else {
                        if (group in values) {
                            delete values[group];
                        }
                    }
                }

                this.set = function (group, key, val, ttl) {
                    var expireTime = null;
                    if (ttl) {
                        expireTime = (new Date().getTime()) + (parseInt(ttl,10) * 1000);
                    }
                    var value = get(group);
                    value[key] = {value: val, expire: expireTime};
                    set(group, value);
                };
                this.get = function (group, key) {
                    var value = get(group);
                    if (value && key in value && 'value' in value[key]) {
                        if (value[key].expire && value[key].expire < (new Date().getTime())) {
                            delete value[key];
                            set(group)
                            return;
                        }
                        return value[key].value;
                    }
                };
                this.clearAll = function () {
                    values = {};
                    if (hasStorage() && utils.isFunction(Object.keys)) {
                        var items = Object.keys(windowAlias[storageInterface]);
                        if (items) {
                            for (var i = 0; i < items.length; i++) {
                                if (String(items[i]).substr(0, namespace.length) === namespace) {
                                    remove(String(items[i]).substr(namespace.length));
                                }
                            }
                        }
                    }
                };
            }
            var localStorage = new Storage('localStorage');
            var sessionStorage = new Storage('sessionStorage');

            var utils = {
                _compare: function (actualValue, expectedValue, comparison) {
                    var comparisonsToTreatLowerCase = ['equals', 'starts_with', 'contains', 'ends_with'];

                    if (this.indexOfArray(comparisonsToTreatLowerCase, comparison) !== -1) {
                        actualValue = String(actualValue).toLowerCase();
                        expectedValue = String(expectedValue).toLowerCase();
                    }

                    switch (comparison) {
                        case 'equals':
                            return String(actualValue) === String(expectedValue);
                        case 'equals_exactly':
                            return String(actualValue) === String(expectedValue);
                        case 'regexp':
                            return null !== (String(actualValue).match(new RegExp(expectedValue)));
                        case 'regexp_ignore_case':
                            return null !== (String(actualValue).match(new RegExp(expectedValue, 'i')));
                        case 'lower_than':
                            return actualValue < expectedValue;
                        case 'lower_than_or_equals':
                            return actualValue <= expectedValue;
                        case 'greater_than':
                            return actualValue > expectedValue;
                        case 'greater_than_or_equals':
                            return actualValue >= expectedValue;
                        case 'contains':
                            return String(actualValue).indexOf(expectedValue) !== -1;
                        case 'match_css_selector':
                            if (!expectedValue || !actualValue) {
                                return false;
                            }
                            var nodes = DOM.bySelector(expectedValue)
                            return utils.indexOfArray(nodes, actualValue) !== -1;
                        case 'starts_with':
                            return String(actualValue).indexOf(expectedValue) === 0;
                        case 'ends_with':
                            return String(actualValue).substring(actualValue.length - expectedValue.length, actualValue.length) === expectedValue;
                    }

                    return false;
                },
                compare: function (actualValue, expectedValue, comparison) {
                    var isInverted = String(comparison).indexOf('not_') === 0;
                    if (isInverted) {
                        comparison = String(comparison).substr('not_'.length);
                    }
                    var result = this._compare(actualValue, expectedValue, comparison);

                    if (isInverted) {
                        return !result;
                    }

                    return result;
                },
                trim: function (text)
                {
                    if (text && String(text) === text) {
                        return text.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                    }

                    return text;
                },
                isDefined: function (property) {
                    var propertyType = typeof property;

                    return propertyType !== 'undefined';
                },
                isFunction: function (property) {
                    return typeof property === 'function';
                },
                isObject: function (property) {
                    return typeof property === 'object' && property !== null;
                },
                isString: function (property) {
                    return typeof property === 'string';
                },
                isNumber: function (property) {
                    return typeof property === 'number';
                },
                isArray: function (property) {
                    if (!utils.isObject(property)) {
                        return false;
                    }

                    if ('function' === typeof Array.isArray && Array.isArray) {
                        return Array.isArray(property);
                    }

                    var toString = Object.prototype.toString;
                    var arrayString = toString.call([]);

                    return toString.call(property) === arrayString;
                },
                hasProperty: function (object, key) {
                    return Object.prototype.hasOwnProperty.call(object, key);
                },
                indexOfArray: function (anArray, element) {
                    if (!anArray) {
                        return -1;
                    }

                    if ('function' === typeof anArray.indexOf && anArray.indexOf) {
                        return anArray.indexOf(element);
                    }

                    if (!this.isArray(anArray)) {
                        return -1;
                    }

                    for (var i = 0; i < anArray.length; i++) {
                        if (anArray[i] === element) {
                            return i;
                        }
                    }

                    return -1;
                },
                setMethodWrapIfNeeded: function (contextObject, methodNameToReplace, callback)
                {
                    if (!(methodNameToReplace in contextObject)) {
                        contextObject[methodNameToReplace] = callback;
                        return;
                    }

                    var oldMethodBackup = contextObject[methodNameToReplace];

                    if (!TagManager.utils.isFunction(oldMethodBackup)) {
                        contextObject[methodNameToReplace] = callback;
                        return;
                    }

                    try {
                        contextObject[methodNameToReplace] = function() {
                            try {
                                var value = oldMethodBackup.apply(contextObject, [].slice.call(arguments, 0));
                            } catch (e) {
                                callback.apply(contextObject, [].slice.call(arguments, 0));
                                throw e;
                            }
                            callback.apply(contextObject, [].slice.call(arguments, 0));
                            return value;
                        };
                    } catch (error) {

                    }
                }
            };

            var DataLayer = function () {
                this.values = {};
                this.events = [];
                this.callbacks = [];
                this.reset = function () {
                    this.values = {};
                    this.events = [];
                    this.callbacks = [];
                };
                this.push = function (value) {
                    if (!utils.isObject(value)) {
                        Debug.log('pushed dataLayer value is not an object', value);
                        return;
                    }

                    this.events.push(value);

                    var i;

                    for (i in value) {
                        if (utils.hasProperty(value, i)) {
                            this.set(i, value[i]);
                        }
                    }

                    for (i = 0; i < this.callbacks.length; i++) {
                        if (this.callbacks[i]) {
                            this.callbacks[i](value);
                        }
                    }
                };
                this.on = function (callback) {
                    this.callbacks.push(callback);
                    return this.callbacks.length - 1;
                };
                this.off = function (callbackIndex) {
                    if (callbackIndex in this.callbacks) {
                        // we do not call .splice() as it would change the order of all indexes of other callbacks
                        this.callbacks[callbackIndex] = null;
                    }
                };
                this.set = function (key, value) {
                    this.values[key] = value;
                };
                this.getAllEvents = function (container) {
                    return this.events;
                };
                this.get = function (name) {
                    if (name in this.values) {
                        if (utils.isFunction(this.values[name])) {
                            return this.values[name]();
                        } else if (utils.isObject(this.values[name]) && utils.isFunction(this.values[name].get)) {
                            return this.values[name].get();
                        }

                        return this.values[name];
                    }

                    var val = resolveNestedDotVar(name, this.values);
                    if (utils.isDefined(val)) {
                        return val;
                    }
                };
            };

            var dataLayer = new DataLayer();

            var dateHelper = {
                matchesDateRange: function(now, startDateTime, endDateTime) {
                    var currentTimestampUTC = Date.UTC(now.getUTCFullYear(), now.getUTCMonth(),
                      now.getUTCDate(), now.getUTCHours(),
                      now.getUTCMinutes(), now.getUTCSeconds());

                    if (startDateTime) {
                        // needed eg for safari: 2014/01/02 instead of 2014-01-02
                        startDateTime = String(startDateTime).replace(/-/g, '/');
                    }
                    if (endDateTime) {
                        endDateTime = String(endDateTime).replace(/-/g, '/');
                    }

                    var start, end;
                    try {
                        start = this.convertStringToDate(startDateTime);
                    } catch (e) {
                        if (startDateTime) {
                            throwError('Invalid startDateTime given');
                        }
                    }

                    try {
                        end = this.convertStringToDate(endDateTime);
                    } catch (e) {
                        if (endDateTime) {
                            throwError('Invalid endDateTime given');
                        }
                    }

                    if (startDateTime && isNaN && isNaN(start.getTime())) {
                        throwError('Invalid startDateTime given');
                    }

                    if (endDateTime && isNaN && isNaN(end.getTime())) {
                        throwError('Invalid endDateTime given');
                    }

                    if (startDateTime && currentTimestampUTC < start.getTime()) {
                        return false;
                    }

                    if (endDateTime && currentTimestampUTC > end.getTime()) {
                        return false;
                    }

                    return true;
                },
                convertStringToDate: function (dateString) {
                    var timezonePresent = (dateString && dateString.split(' ').length > 2);
                    dateString = dateString + (dateString && dateString.toLowerCase() !== 'invalid date' && !timezonePresent ? ' UTC' : '');

                    return new Date(dateString);
                }
            };

            var urlHelper = {
                parseUrl: function (urlToParse, urlPart) {
                    try {
                        var loc = document.createElement("a");
                        loc.href = urlToParse;
                        var absUrl = loc.href;

                        // needed to make tests work in IE10... we first need to convert URL to abs url
                        loc = document.createElement("a");
                        loc.href = absUrl;

                        if (urlPart && urlPart in loc) {
                            if ('hash' === urlPart) {
                                return String(loc[urlPart]).replace('#', '');
                            } else if ('protocol' === urlPart) {
                                return String(loc[urlPart]).replace(':', '');
                            } else if ('search' === urlPart) {
                                return String(loc[urlPart]).replace('?', '');
                            } else if ('port' === urlPart && !loc[urlPart]) {
                                if (loc.protocol === 'https:') {
                                    return '443';
                                } else if (loc.protocol === 'http:') {
                                    return '80';
                                }
                            }

                            if ('pathname' === urlPart && loc[urlPart] && String(loc[urlPart]).substr(0,1) !== '/') {
                                return '/' + loc[urlPart]; // ie 10 doesn't return leading slash when not added to the dom
                            }

                            if ('port' === urlPart && loc[urlPart]) {
                                return String(loc[urlPart]); // ie 10 returns int
                            }

                            return loc[urlPart];
                        }

                        if ('origin' === urlPart && 'protocol' in loc && loc.protocol) {
                            // fix for ie10
                            return loc.protocol + "//" + loc.hostname + (loc.port ? ':' + loc.port : '');
                        }
                        return;
                    } catch (e) {
                        if ('function' === typeof URL) {
                            var theUrl = new URL(urlToParse);
                            if (urlPart && urlPart in theUrl) {
                                if ('hash' === urlPart) {
                                    return String(theUrl[urlPart]).replace('#', '');
                                } else if ('protocol' === urlPart) {
                                    return String(theUrl[urlPart]).replace(':', '');
                                } else if ('search' === urlPart) {
                                    return String(theUrl[urlPart]).replace('?', '');
                                } else if ('port' === urlPart && !theUrl[urlPart]) {
                                    if (theUrl.protocol === 'https:') {
                                        return '443';
                                    } else if (theUrl.protocol === 'http:') {
                                        return '80';
                                    }
                                }
                                return theUrl[urlPart];
                            }
                            return;
                        }
                    }
                },
                decodeSafe: function (text) {
                    try {
                        return windowAlias.decodeURIComponent(text);
                    } catch (e) {
                        return windowAlias.unescape(text);
                    }
                },
                getQueryParameter: function (parameter, locationSearch) {
                    if (!utils.isDefined(locationSearch)) {
                        locationSearch = windowAlias.location.search;
                    }
                    if (!locationSearch || !utils.isDefined(parameter) || parameter === null || parameter === false || parameter === '') {
                        return null;
                    }
                    var locationStart = locationSearch.substr(0,1);
                    if (locationSearch !== '?' && locationSearch !== '&'){
                        locationSearch = '?' + locationSearch;
                    }

                    parameter = parameter.replace('[', '\\[');
                    parameter = parameter.replace(']', '\\]');

                    var regexp = new RegExp('[?&]' + parameter + '(=([^&#]*)|&|#|$)');
                    var matches = regexp.exec(locationSearch);

                    if (!matches) {
                        return null;
                    }

                    if (!matches[2]) {
                        return '';
                    }

                    var value = matches[2].replace(/\+/g, " ");

                    return this.decodeSafe(value);
                }
            };

            var windowHelperScrollTimeout;
            var windowHelper = {
                hasSetupScroll: false,
                scrollCallbacks: [],
                scrollListenEvents: ['scroll', 'resize'],
                offScroll: function (scrollIndex) {
                    if (scrollIndex in this.scrollCallbacks) {
                        this.scrollCallbacks[scrollIndex] = null;
                    }

                    // when there are no longer any callbacks, we should remove all event listeners for better performance
                    var i = 0, numCallbacks = 0;
                    for (i in this.scrollCallbacks) {
                        if (this.scrollCallbacks[i]) {
                            numCallbacks++;
                        }
                    }

                    if (!numCallbacks) {
                        for (i = 0; i < this.scrollListenEvents.length; i++) {
                            if (documentAlias.removeEventListener) {
                                windowAlias.removeEventListener(this.scrollListenEvents[i], this.didScroll, true);
                            } else {
                                windowAlias.detachEvent('on' + this.scrollListenEvents[i], this.didScroll);
                            }
                        }

                        this.hasSetupScroll = false;
                    }
                },
                didScroll: function (event) {
                    if (windowHelperScrollTimeout) {
                        return;
                    }
                    if (event && event.type && event.type === 'scroll' && event.target && event.target !== documentAlias && event.target !== windowAlias) {
                        // we only listen to scrolls on whole page by default as this is WINDOW listener, not element listener
                        return;
                    }

                    // scroll event is executed after each pixel, so we make sure not to
                    // execute event too often. otherwise FPS goes down a lot!

                    windowHelperScrollTimeout = setTimeout(function () {
                        windowHelperScrollTimeout = null;
                        var i;
                        for (i = 0; i < windowHelper.scrollCallbacks.length; i++) {
                            if (windowHelper.scrollCallbacks[i]) {
                                windowHelper.scrollCallbacks[i](event);
                            }
                        }
                    }, 120);
                },
                onScroll: function (callback) {
                    this.scrollCallbacks.push(callback);

                    if (!this.hasSetupScroll) {
                        this.hasSetupScroll = true;

                        var index = 0;
                        for (index = 0; index < this.scrollListenEvents.length; index++) {
                            if (documentAlias.addEventListener) {
                                windowAlias.addEventListener(this.scrollListenEvents[index], this.didScroll, true);
                            } else {
                                windowAlias.attachEvent('on' + this.scrollListenEvents[index], this.didScroll);
                            }
                        }
                    }

                    return this.scrollCallbacks.length - 1;
                },
                getScreenHeight: function () {
                    return windowAlias.screen.height;
                },
                getScreenWidth: function () {
                    return windowAlias.screen.width;
                },
                getViewportWidth: function () {
                    var width = windowAlias.innerWidth || documentAlias.documentElement.clientWidth || documentAlias.body.clientWidth;

                    if (!width) {
                        return 0;
                    }

                    return width;
                },
                getViewportHeight: function () {
                    var height = windowAlias.innerHeight || documentAlias.documentElement.clientHeight || documentAlias.body.clientHeight;

                    if (!height) {
                        return 0;
                    }

                    return height;
                },
                getPerformanceTiming: function (keyword) {
                    if ('performance' in windowAlias && utils.isObject(windowAlias.performance) && utils.isObject(windowAlias.performance.timing) && keyword in windowAlias.performance.timing) {
                        return windowAlias.performance.timing[keyword];
                    }
                    return 0;
                }
            };

            var DOM = {
                loadScriptUrl: function (url, options) {
                    if (!options) {
                        options = {};
                    }
                    if (!utils.isDefined(options.defer)) {
                        options.defer = true;
                    }
                    if (!utils.isDefined(options.async)) {
                        options.async = true;
                    }
                    if (!utils.isDefined(options.type)) {
                        options.type = 'text/javascript';
                    }

                    var script = document.createElement('script');
                    script.src = url;
                    script.type = options.type;
                    script.defer = !!options.defer;
                    script.async = !!options.async;

                    if (utils.isFunction(options.onload)) {
                        script.onload = options.onload;
                    }

                    if (utils.isFunction(options.onerror)) {
                        script.onerror = options.onerror;
                    }

                    if (utils.isDefined(options.charset)) {
                        script.charset = options.charset;
                    }

                    if (utils.isDefined(options.id)) {
                        script.id = options.id;
                    }

                    documentAlias.head.appendChild(script);
                },
                getScrollLeft: function () {
                    return windowAlias.document.body.scrollLeft || windowAlias.document.documentElement.scrollLeft;
                },
                getScrollTop: function () {
                    return windowAlias.document.body.scrollTop || windowAlias.document.documentElement.scrollTop;
                },
                getDocumentHeight: function () {
                    // we use at least one px to prevent divisions by zero etc
                    return Math.max(documentAlias.body.offsetHeight, documentAlias.body.scrollHeight, documentAlias.documentElement.offsetHeight, documentAlias.documentElement.clientHeight, documentAlias.documentElement.scrollHeight, 1);
                },
                getDocumentWidth: function () {
                    // we use at least one px to prevent divisions by zero etc
                    return Math.max(documentAlias.body.offsetWidth, documentAlias.body.scrollWidth, documentAlias.documentElement.offsetWidth, documentAlias.documentElement.clientWidth, documentAlias.documentElement.scrollWidth, 1);
                },
                addEventListener: function (element, eventType, eventHandler, useCapture) {
                    if (!element) {
                        Debug.log('element not found, cannot add event listener', element, this);

                        return;
                    }
                    if (element.addEventListener) {
                        useCapture = useCapture || false;
                        element.addEventListener(eventType, eventHandler, useCapture);

                        return true;
                    }

                    if (element.attachEvent) {
                        return element.attachEvent('on' + eventType, eventHandler);
                    }

                    element['on' + eventType] = eventHandler;
                },
                getElementText: function (node) {
                    if (!node) {
                        return;
                    }

                    // If the content belongs to a masked element and it doesn't have any children, return a masked string.
                    if (TagManager.dom.shouldElementBeMasked(node) && node.children.length === 0) {
                        return '*******';
                    }

                    // If the element has children that should be masked, deal with that.
                    if (TagManager.dom.elementHasMaskedChild(node)) {
                        return TagManager.dom.getElementTextWithMaskedChildren(node);
                    }

                    var content = node.innerText || node.textContent || '';
                    content = content.replace(/([\s\uFEFF\xA0])+/g, " ");
                    content = content.replace(/(\s)+/g, " ");
                    return utils.trim(content);
                },
                getElementClassNames: function (node) {
                    if (node && node.className) {
                        return utils.trim(String(node.className).replace(/\s{2,}/g, ' '));
                    }
                    return '';
                },
                getElementAttribute: function (node, attributeName) {
                    if (!node || !attributeName) {
                        return;
                    }

                    // If the attribute is one of the restricted attributes and belongs to a masked element, return a masked string.
                    var attr = attributeName.toLowerCase();
                    if ((attr === 'value' || attr === 'title' || attr === 'alt' || attr === 'label' || attr === 'placeholder') && TagManager.dom.shouldElementBeMasked(node)) {
                        return '*******';
                    }

                    if (node && node.getAttribute) {
                        return node.getAttribute(attributeName);
                    }

                    if (!node || !node.attributes) {
                        return;
                    }

                    var typeOfAttr = (typeof node.attributes[attributeName]);
                    if ('undefined' === typeOfAttr) {
                        return null;
                    }

                    if (node.attributes[attributeName].value) {
                        return node.attributes[attributeName].value; // nodeValue is deprecated ie Chrome
                    }

                    if (node.attributes[attributeName].nodeValue) {
                        return node.attributes[attributeName].nodeValue;
                    }
                    return null;
                },
                _htmlCollectionToArray: function (foundNodes)
                {
                    var nodes = [];

                    if (!foundNodes || !foundNodes.length) {
                        return nodes;
                    }
                    var index;
                    for (index = 0; index < foundNodes.length; index++) {
                        nodes.push(foundNodes[index]);
                    }

                    return nodes;
                },
                byId: function (id) {
                    if (utils.isString(id) && id.substr(0,1) === '#') {
                        id = id.substr(1);
                    }
                    return documentAlias.getElementById(id);
                },
                byClassName: function (className) {
                    if (className && 'getElementsByClassName' in documentAlias) {
                        return this._htmlCollectionToArray(documentAlias.getElementsByClassName(className));
                    }
                    return [];
                },
                byTagName: function (tagName) {
                    if (tagName && 'getElementsByTagName' in documentAlias) {
                        return this._htmlCollectionToArray(documentAlias.getElementsByTagName(tagName));
                    }
                    return [];
                },
                bySelector: function (selector) {
                    if (selector && 'querySelectorAll' in documentAlias) {
                        return this._htmlCollectionToArray(documentAlias.querySelectorAll(selector));
                    }
                    return [];
                },
                /** @ignore **/
                isElementContext: function (htmlString, tag) {
                    //  not part of API at this moment
                    if (!htmlString || !tag) {
                        return false;
                    }
                    htmlString = String(htmlString).toLowerCase();
                    tag = String(tag).toLowerCase();
                    var lastScriptPos = htmlString.lastIndexOf('<' + tag);
                    if (lastScriptPos === -1) {
                        return false;
                    }
                    var lastPiece = htmlString.substring(lastScriptPos);

                    return !lastPiece.match(new RegExp('<\\s*/\\s*' + tag + '>'));
                },
                /** @ignore **/
                isAttributeContext: function (htmlString, attr) {
                    //  not part of API at this moment
                    if (!htmlString || !attr) {
                        return false;
                    }

                    // we remove all whitespace around equal signs in case there is an attribute like this " href = 'fff'"
                    // for easier matching
                    htmlString = String(htmlString).replace(/([\s\uFEFF\xA0]*=[\s\uFEFF\xA0]*)/g, '=');

                    // htmlString = eg "sdsds <div foo='test' mytest style='color:"
                    var lastScriptPos = htmlString.lastIndexOf('<');
                    if (lastScriptPos === -1) {
                        return false; // no opening tag
                    }
                    // lastPiece = eg "<div foo='test' mytest style='color:"
                    var lastPiece = htmlString.substring(lastScriptPos);
                    var endingElementPos = lastPiece.indexOf('>');

                    if (endingElementPos !== -1) {
                        return false; // the tag was closed so we cannot be within an attribute
                    }

                    var posAttrEnd = lastPiece.lastIndexOf('=');
                    if (posAttrEnd === -1) {
                        return false; // no attribute with value within the tag
                    }
                    var posAttrStart = lastPiece.lastIndexOf(' ', posAttrEnd);
                    // attrName = eg " style"
                    var attrName = lastPiece.substring(posAttrStart, posAttrEnd);
                    attrName = utils.trim(attrName);

                    if (attrName.toLowerCase() !== attr.toLowerCase()) {
                        return false;
                    }

                    // attrValue = eg "'color:"
                    var attrValue = lastPiece.substring(posAttrEnd).replace('=', '');

                    var quote = attrValue.substring(0, 1);
                    if ('"' === quote) {
                        return -1 === attrValue.substring(1).indexOf('"');
                    } else if ("'" === quote) {
                        return -1 === attrValue.substring(1).indexOf("'");
                    }

                    // seems like user did not put quotes around the attribute! we check for a space for attr separation
                    return -1 === attrValue.indexOf(' ');
                },
                onLoad: function (callback) {
                    if (documentAlias.readyState === 'complete') {
                        callback();
                    } else if (windowAlias.addEventListener) {
                        windowAlias.addEventListener('load', callback);
                    } else if (windowAlias.attachEvent) {
                        windowAlias.attachEvent('onload', callback);
                    }
                },
                onReady: function (callback) {
                    var loaded = false;

                    if (documentAlias.attachEvent) {
                        loaded = documentAlias.readyState === 'complete';
                    } else {
                        loaded = documentAlias.readyState !== 'loading';
                    }

                    if (loaded) {
                        callback();
                        return;
                    }

                    if (documentAlias.addEventListener) {
                        this.addEventListener(documentAlias, 'DOMContentLoaded', function ready() {
                            documentAlias.removeEventListener('DOMContentLoaded', ready, false);
                            if (!loaded) {
                                loaded = true;
                                callback();
                            }
                        });
                    } else if (documentAlias.attachEvent) {
                        documentAlias.attachEvent('onreadystatechange', function ready() {
                            if (documentAlias.readyState === 'complete') {
                                documentAlias.detachEvent('onreadystatechange', ready);
                                if (!loaded) {
                                    loaded = true;
                                    callback();
                                }
                            }
                        });
                    }

                    // fallback
                    this.onLoad(function () {
                        if (!loaded) {
                            loaded = true;
                            callback();
                        }
                    });
                },
                onClick: function (callback, element) {
                    if (typeof element === 'undefined') {
                        element = documentAlias.body;
                    }
                    TagManager.dom.addEventListener(element, 'click', function (event) {
                        var clickKey = (event.which ? event.which : 1);
                        if (clickKey === 1) {
                          callback(event, 'left');
                        }
                    }, true)
                    TagManager.dom.addEventListener(element, 'auxclick', function (event) {
                        var clickKey = (event.which ? event.which : 2);
                        if (clickKey === 2) {
                          callback(event, 'middle');
                        }
                    }, true)
                    TagManager.dom.addEventListener(element, 'contextmenu', function (event) {
                        var clickKey = (event.which ? event.which : 3);
                        if (clickKey === 3) {
                          callback(event, 'right');
                        }
                    }, true)
                },
                shouldElementBeMasked: function (element) {
                    if (typeof element === 'undefined') {
                        return false;
                    }

                    // If the element has the attribute indicating that it should be masked, return true.
                    if (element.hasAttribute('data-matomo-mask') || element.hasAttribute('data-piwik-mask')) {
                        return true;
                    }

                    // If the element has the attribute indicating that it shouldn't be masked, return false.
                    if (element.hasAttribute('data-matomo-unmask') || element.hasAttribute('data-piwik-unmask')) {
                        return false;
                    }

                    // Find the closest parent with the mask or unmask attribute. If it's the mask, return true. I originally used the closest function, but it appears that some browsers don't support it.
                    var parentElement = element.parentElement;
                    while (parentElement) {
                        if (parentElement.hasAttribute('data-matomo-mask') || parentElement.hasAttribute('data-piwik-mask')) {
                            return true;
                        }

                        if (parentElement.hasAttribute('data-matomo-unmask') || parentElement.hasAttribute('data-piwik-unmask')) {
                            return false;
                        }

                        parentElement = parentElement.parentElement;
                    }

                    return false;
                },
                elementHasMaskedChild: function (element) {
                    if (typeof element === 'undefined') {
                        return false;
                    }

                    // Does the element even have any children?
                    if (element.children.length === 0) {
                        return false;
                    }

                    // Does the current node have a mask attribute or a parent that does?
                    if (element.hasAttribute('data-matomo-mask') || element.hasAttribute('data-piwik-mask') || TagManager.dom.shouldElementBeMasked(element)) {
                        return true;
                    }

                    return element.querySelector('[data-matomo-mask],[data-piwik-mask]') !== null;
                },
                getElementTextWithMaskedChildren: function (element) {
                    var text = '';
                    var descendents = element.children;
                    for (var i = 0; i < descendents.length; i++) {
                        var item = descendents[i];
                        text += TagManager.dom.getElementText(item) + ' ';
                    }

                    return utils.trim(text);
                }
            };

            function TemplateParameters(params)
            {
                this.window = windowAlias;
                this.document = documentAlias;

                this.set = function (index, value) {
                    this[index] = value;
                };

                this.get = function (key, defaultValue) {
                    if (key === null || key === false || !utils.isDefined(key)) {
                        return defaultValue;
                    }

                    if (key in this) {
                        if (utils.isObject(this[key]) && 'get' in this[key] && utils.isFunction(this[key].get)) {
                            return this[key].get();
                        }

                        return this[key];
                    }

                    var value = resolveNestedDotVar(key, this);
                    if (utils.isDefined(value)) {
                        return value;
                    }

                    return defaultValue;
                };

                this.buildVariable = function (variable) {
                    return buildVariable(variable, this.get('container'));
                };

                if (utils.isObject(params)) {
                    for (var i in params) {
                        if (utils.hasProperty(params, i)) {
                            this.set(i, params[i]);
                        }
                    }
                }
            }

            function Condition(condition, container) {
                this.isValid = function () {
                    var actualValue = buildVariable(condition.actual, container).get();
                    var expectedValue = buildVariable(condition.expected, container).get();

                    return utils.compare(actualValue, expectedValue, condition.comparison);
                };
            }

            function buildVariable(variable, container)
            {
                if (utils.isObject(variable) && variable.joinedVariable && utils.isArray(variable.joinedVariable)) {
                    return new JoinedVariable(variable.joinedVariable, container);
                } else if (utils.isObject(variable) && variable.type) {
                    return new Variable(variable, container);
                }

                return new ConstantVariable(variable, container);
            }

            function JoinedVariable(variables, container)
            {
                this.name = '';
                this.type = 'JoinedVariable';

                this.getDefinition = function () {
                    return variables;
                };
                this.get = function () {
                    var value = '', varReturn;
                    for (var i = 0; i < variables.length; i++) {
                        varReturn = buildVariable(variables[i], container).toString();
                        if (varReturn !== false && varReturn !== null && utils.isDefined(varReturn)) {
                            value += varReturn;
                        }
                    }
                    return value;
                };
                this.toString = function () {
                    return this.get();
                };

                this.addDebugValues = function (variables) {
                    variables.push({
                        name: null,
                        type: '_joined',
                        value: this.get()
                    });
                };
            }

            function ConstantVariable(value, container)
            {
                this.name = '';
                this.type = 'ConstantVariable';

                this.getDefinition = function () {
                    return value;
                };

                function isVariableDefinition(value) {
                    return value && utils.isObject(value) && !utils.isArray(value) && (utils.hasProperty(value, 'type') || utils.hasProperty(value, 'joinedVariable'));
                }

                function deepClone(value)
                {
                    if (value == null || typeof value !== 'object') {
                        return value;
                    }

                    var newVal = new value.constructor();

                    var key;
                    for (key in value) {
                        if (utils.hasProperty(value, key)) {
                            newVal[key] = deepClone(value[key]);
                        }
                    }

                    return newVal;
                }

                function convertVariableTemplateIfNeeded(value)
                {
                    var i;
                    if (isVariableDefinition(value)) {
                        value = buildVariable(value, container).get();
                    } else if (value && utils.isArray(value)) {
                        for (i = 0; i < value.length; i++) {
                            value[i] = convertVariableTemplateIfNeeded(value[i]);
                        }
                    } else if (value && utils.isObject(value)) {
                        for (i in value) {
                            if (utils.hasProperty(value, i)) {
                                value[i] = convertVariableTemplateIfNeeded(value[i]);
                            }
                        }
                    }
                    return value;
                }

                this.get = function () {
                    var result = value;
                    if (utils.isObject(result)) {
                        // we potentially make modifications to the initial parameter and therefore need to
                        // clone it to ensure we always resolve all variables. cannot use json.parse(json.stringify) as
                        // there will be functions and objects
                        result = deepClone(result);
                        result= convertVariableTemplateIfNeeded(result);
                    }
                    return result;
                };

                this.toString = function () {
                    return value;
                };
                this.addDebugValues = function (variables) {
                    variables.push({
                        name: null,
                        type: '_constant',
                        value: this.get()
                    });
                };
            }

            function Variable(variable, container) {
                this.type = variable.type;
                this.name = variable.name;
                this.lookUpTable = variable.lookUpTable || [];
                this.defaultValue = undefined;
                this.parameters = variable.parameters || {};

                this.getDefinition = function () {
                    return variable;
                };
                this.get = function () {
                    var value;
                    try {
                        value = this.theVariable.get();
                    } catch (e) {
                        Debug.error('Failed to get value of variable', e, this);
                        value = undefined;
                    }

                    if ((!utils.isDefined(value) || value === null || value === false) && utils.isDefined(this.defaultValue)) {
                        value = this.defaultValue;
                    }

                    var i;
                    for (i = 0; i < this.lookUpTable.length; i++) {
                        var lookUp = this.lookUpTable[i];
                        if (utils.compare(value, lookUp.matchValue, lookUp.comparison)) {
                            return lookUp.outValue;
                        }
                    }

                    return value;
                };
                this.toString = function () {
                    if (this.theVariable && utils.hasProperty(this.theVariable, 'toString') && utils.isFunction(this.theVariable.toString)) {
                        try {
                            return this.theVariable.toString();
                        } catch (e) {
                            Debug.error('Failed to get toString of variable', e, this);
                            return;
                        }
                    }
                    return this.get();
                };

                this.addDebugValues = function (variables) {
                    variables.push({
                        name: this.name,
                        type: this.type,
                        value: this.get()
                    });
                };

                if ('undefined' !== typeof variable.defaultValue) {
                    this.defaultValue = variable.defaultValue;
                }

                if (!utils.isDefined(variable.Variable) || !variable.Variable) {
                    Debug.log('no template defined for variable ', variable);
                    return;
                }

                var i, parameters = new TemplateParameters({variable: this, container: container});
                if (utils.isObject(variable.parameters)) {
                    for (i in variable.parameters) {
                        if (utils.hasProperty(variable.parameters, i)) {
                            parameters.set(i, buildVariable(variable.parameters[i], container));
                        }
                    }
                }

                if (utils.isFunction(variable.Variable)) {
                    this.theVariable = new variable.Variable(parameters, TagManager);
                } else if (utils.isObject(variable.Variable)) {
                    this.theVariable = variable.Variable;
                } else if (variable.Variable in container.templates) {
                    this.theVariable = new container.templates[variable.Variable](parameters, TagManager);
                } else {
                    throwError('No matching variable template found');
                }
            }
            function Trigger(trigger, container) {
                this.referencedTags = [];
                this.id = trigger.id;
                this.type = trigger.type;
                this.name = trigger.name;
                this.conditions = [];
                this.parameters = trigger.parameters || {};

                var self = this;

                this.getId = function () {
                    return this.id;
                };

                this.setUp = function () {
                    if (this.theTrigger && this.theTrigger.setUp && utils.isFunction(this.theTrigger.setUp)) {
                        this.theTrigger.setUp(function (event) {
                            dataLayer.push(event);

                            if (!('event' in event)) {
                                return;
                            }

                            var result = {
                                tags: [],
                                variables: [],
                                metTrigger: null,
                                name: event.event,
                                eventData: event,
                                container: {}
                            };

                            var i, j;
                            if (self.meetsConditions()) {
                                Debug.log('The condition is met for trigger ' + self.name, self);

                                result.metTrigger = {name: self.name, type: self.type};

                                var tags = self.getReferencedTags();

                                for (j = 0; j < tags.length; j++) {
                                    if (tags[j].hasBlockTrigger(self)) {
                                        tags[j].block();
                                        tags[j].addDebugValues(result.tags, 'Block');
                                    } else if (tags[j].hasFireTrigger(self)) {
                                        // todo we could further optimize this that when a trigger is no longer needed because
                                        // eg the tag can be only triggered once, then we remove the trigger from this.triggers
                                        // and ideally even call a teardown method to remove possible event listeners etc!
                                        tags[j].fire();
                                        tags[j].addDebugValues(result.tags, 'Fire');
                                    }
                                }
                            }

                            if (window.mtmPreviewWindow || Debug.enabled) {
                                container.addDebugValues(result.container);
                                pushDebugEvent(result);
                                if (Debug.enabled) {
                                    Debug.log('event: ', result);
                                }
                            }
                        });
                    }
                };

                this.addReferencedTag = function (tag) {
                    this.referencedTags.push(tag);
                };
                this.getReferencedTags = function () {
                    return this.referencedTags;
                };

                this.meetsConditions = function () {
                    var i,condition;
                    for (i = 0; i < this.conditions.length; i++) {
                        condition = new Condition(this.conditions[i], container);
                        if (!condition.isValid()) {
                            return false;
                        }
                    }
                    return true;
                };

                if (trigger.conditions && utils.isArray(trigger.conditions)) {
                    this.conditions = trigger.conditions;
                }

                var i, parameters = new TemplateParameters({trigger: this, container: container});

                if (utils.isObject(trigger.parameters)) {
                    for (i in trigger.parameters) {
                        if (utils.hasProperty(trigger.parameters, i)) {
                            parameters.set(i, buildVariable(trigger.parameters[i], container));
                        }
                    }
                }

                if (!utils.isDefined(trigger.Trigger) || !trigger.Trigger) {
                    Debug.error('no template defined for trigger ', trigger);
                    return;
                }

                if (utils.isFunction(trigger.Trigger)) {
                    this.theTrigger = new trigger.Trigger(parameters, TagManager);
                } else if (utils.isObject(trigger.Trigger)) {
                    this.theTrigger = trigger.Trigger;
                } else if (trigger.Trigger in container.templates) {
                    this.theTrigger = new container.templates[trigger.Trigger](parameters, TagManager);
                } else {
                    throwError('No matching trigger template found');
                }

                parameters = null;

            }
            function Tag (tag, container) {
                this.type = tag.type;
                this.name = tag.name;
                this.fireTriggerIds = tag.fireTriggerIds ? tag.fireTriggerIds : [];
                this.blockTriggerIds = tag.blockTriggerIds ? tag.blockTriggerIds : [];
                this.fireLimit = tag.fireLimit ? tag.fireLimit : Tag.FIRE_LIMIT_UNLIMITED;
                this.fireDelay = tag.fireDelay ? parseInt(tag.fireDelay, 10) : 0;
                this.startDate = tag.startDate ? tag.startDate : null;
                this.endDate = tag.endDate ? tag.endDate : null;
                this.numExecuted = 0;
                this.blocked = false;
                this.parameters = tag.parameters || {};
                this.isTagFireLimitAllowedInPreviewMode = container.isTagFireLimitAllowedInPreviewMode || false;
                var self = this;

                this.addDebugValues = function (tags, action) {
                    tags.push({
                        action: action,
                        type: this.type,
                        name: this.name,
                        numExecuted: this.numExecuted,
                    });
                };

                this._doFire = function () {
                    if (this.blocked) {
                        Debug.log('not firing as this tag is blocked', this);
                        return 'tag is blocked';
                    }

                    if (this.fireLimit !== Tag.FIRE_LIMIT_UNLIMITED && this.numExecuted) {
                        Debug.log('not firing as this tag has limit reached', this);
                        return 'fire limit is restricted';
                    }

                    var storageKey = 'tag';
                    if (container.id) {
                        // the same name may be used in different containers therefore need to save it per container
                        storageKey += '_' + container.id;
                    }

                    if (this.fireLimit === Tag.FIRE_LIMIT_ONCE_24HOURS && (!window.mtmPreviewWindow || this.isTagFireLimitAllowedInPreviewMode)) {
                        // in preview/debug mode we make sure to execute it
                        if (localStorage.get(storageKey, this.name)) {
                            Debug.log('not firing as this tag has 24hours limit reached', this);
                            return 'fire limit 24hours is restricted';
                        }
                    }

                    if (this.fireLimit === Tag.FIRE_LIMIT_ONCE_LIFETIME && (!window.mtmPreviewWindow || this.isTagFireLimitAllowedInPreviewMode)) {
                        // in preview/debug mode we make sure to execute it
                        if (localStorage.get(storageKey, this.name)) {
                            Debug.log('not firing as this tag has limit reached', this);
                            return 'fire limit lifetime is restricted';
                        }
                    }

                    if (!dateHelper.matchesDateRange(new Date(), this.startDate, this.endDate)) {
                        Debug.log('not firing as this tag does not match date', this);
                        return 'date range does not match';
                    }

                    if (!this.theTag || !this.theTag.fire) {
                        Debug.log('not firing as tag does not exist anymore', this);
                        return 'tag not found';
                    }

                    Debug.log('firing this tag', this);

                    this.numExecuted++;

                    if (this.fireLimit === Tag.FIRE_LIMIT_ONCE_24HOURS) {
                        var ttl24Hours = 24 * 60 * 60;
                        localStorage.set(storageKey, this.name, '1', ttl24Hours);
                    }

                    if (this.fireLimit === Tag.FIRE_LIMIT_ONCE_LIFETIME) {
                        localStorage.set(storageKey, this.name, '1');
                    }

                    this.theTag.fire();

                    Debug.log('fired this tag', this);
                };

                this.fire = function () {
                    if (this.fireDelay) {
                        setTimeout(function () {
                            self._doFire();
                        }, this.fireDelay);
                    } else {
                        return this._doFire();
                    }
                };

                this.block = function () {
                    this.blocked = true;
                };

                this.hasFireTrigger = function (trigger) {
                    if (!this.fireTriggerIds || !this.fireTriggerIds.length) {
                        return false;
                    }
                    if (!trigger) {
                        return false;
                    }
                    var id = trigger.getId();
                    return utils.indexOfArray(this.fireTriggerIds, id) !== -1;
                };

                this.hasBlockTrigger = function (trigger) {
                    if (!this.blockTriggerIds || !this.blockTriggerIds.length) {
                        return false;
                    }
                    if (!trigger) {
                        return false;
                    }
                    var id = trigger.getId();
                    return utils.indexOfArray(this.blockTriggerIds, id) !== -1;
                };

                if (!utils.isDefined(tag.Tag) || !tag.Tag) {
                    Debug.error('no template defined for tag ', tag);
                    return;
                }

                var i, parameters = new TemplateParameters({tag: this, container: container});

                if (utils.isObject(tag.parameters)) {
                    for (i in tag.parameters) {
                        if (utils.hasProperty(tag.parameters, i)) {
                            parameters.set(i, buildVariable(tag.parameters[i], container));
                        }
                    }
                }

                if (utils.isFunction(tag.Tag)) {
                    this.theTag = new tag.Tag(parameters, TagManager);
                } else if (utils.isObject(tag.Tag)) {
                    this.theTag = tag.Tag;
                } else if (tag.Tag in container.templates) {
                    this.theTag = new container.templates[tag.Tag](parameters, TagManager);
                } else {
                    throwError('No matching tag template found');
                }

            }
            Tag.FIRE_LIMIT_ONCE_PAGE = 'once_page';
            Tag.FIRE_LIMIT_ONCE_24HOURS = 'once_24hours';
            Tag.FIRE_LIMIT_ONCE_LIFETIME = 'once_lifetime';
            Tag.FIRE_LIMIT_UNLIMITED = 'unlimited';

            function Container(container, templates) {
                var self = this;

                this.id = container.id;
                this.idsite = container.idsite || null;
                this.isTagFireLimitAllowedInPreviewMode = container.isTagFireLimitAllowedInPreviewMode || false;
                this.versionName = container.versionName || null;
                this.revision = container.revision || null;
                this.environment = container.environment || null;
                this.templates = templates || {};
                this.dataLayer = new DataLayer();
                // this.variables currently only used for debug mode actually!
                this.variables = [];
                this.triggers = [];
                this.tags = []; // only there for debugging

                this.onNewGlobalDataLayerValue = function (value) {
                    this.dataLayer.push(value);
                };
                dataLayer.on(function (value) {
                    self.onNewGlobalDataLayerValue(value);
                });

                this.addDebugValues = function (container) {
                    container.variables = [];
                    var i;

                    for (i = 0; i < this.variables.length; i++) {
                        this.variables[i].addDebugValues(container.variables);
                    }

                    container.tags = [];
                    for (i = 0; i < this.tags.length; i++) {
                        this.tags[i].addDebugValues(container.tags, 'Not Fired Yet');
                    }

                    container.id = this.id;
                    container.versionName = this.versionName;
                    container.dataLayer = JSON.parse(JSON.stringify(this.dataLayer.values, function( key, value) {
                        if (typeof value === 'object' && value instanceof Node) {
                            return value.nodeName;
                        } else {
                            return value;
                        };
                    }));
                };

                this.getTriggerById = function (idTrigger){
                    if (!idTrigger) {
                        return;
                    }
                    var i;
                    for (i = 0; i < this.triggers.length; i++) {
                        if (this.triggers[i].getId() === idTrigger) {
                            return this.triggers[i];
                        }
                    }
                };
                this.addTrigger = function (triggerObj) {
                    if (!triggerObj) {
                        return;
                    }
                    var triggerInstance = this.getTriggerById(triggerObj.id);
                    if (!triggerInstance) {
                        triggerInstance = new Trigger(triggerObj, this);
                        this.triggers.push(triggerInstance);
                    }
                    return triggerInstance;
                };

                var i, j, tag, tagDefinition, trigger;
                if (container.variables && utils.isArray(container.variables)) {
                    for (i = 0; i < container.variables.length; i++) {
                        this.variables.push(buildVariable(container.variables[i], this));
                    }
                }

                if (container.triggers && utils.isArray(container.triggers)) {

                    if (container.tags && utils.isArray(container.tags)) {
                        // we need to try and add triggers first that are block triggers. This way block triggers will be
                        // executed before fire triggers (unless a trigger is a block and a fire trigger in which case it
                        // may still cause issues and then a tag delay needs to be used.
                        // this is interesting when you have say 2 page view triggers. The first page view trigger is triggered
                        // and then would immediately fire a tag. Next the second page view trigger will be triggered
                        // immediately afterwards and would then block the previously fired tag. The tag should not have been fired
                        // basically. By sorting them to add triggers that block tags first, these triggers would be executed
                        // first and then the scenario is that basically the 2nd trigger would be executed first and correctly block
                        // the tag. Then the first trigger will be triggered and it won't fire the tag because it was blocked.
                        container.triggers.sort(function (a, b) {
                            var isABlockTrigger = false, isBBlockTrigger = false, tag, z;
                            for (z= 0; z < container.tags.length; z++) {
                                tag = container.tags[z];
                                if (tag && tag.blockTriggerIds && utils.isArray(tag.blockTriggerIds)) {
                                    isABlockTrigger = isABlockTrigger || utils.indexOfArray(tag.blockTriggerIds, a.id) !== -1;
                                    isBBlockTrigger = isBBlockTrigger || utils.indexOfArray(tag.blockTriggerIds, b.id) !== -1;
                                }
                            }
                            if (isABlockTrigger && !isBBlockTrigger) {
                                return -1;
                            } else if (isBBlockTrigger && !isABlockTrigger) {
                                return 1;
                            }

                            if (a.id < b.id) {
                                return -1;
                            }
                            return 1;
                        });
                    }
                    for (i = 0; i < container.triggers.length; i++) {
                        this.addTrigger(container.triggers[i]);
                    }
                }

                if (container.tags && utils.isArray(container.tags)) {
                    for (i = 0; i < container.tags.length; i++) {
                        tagDefinition = container.tags[i];
                        tag = new Tag(tagDefinition, this);
                        this.tags.push(tag);

                        if (tagDefinition.blockTriggerIds && utils.isArray(tagDefinition.blockTriggerIds)) {
                            for (j = 0; j < tagDefinition.blockTriggerIds.length; j++) {
                                trigger = this.getTriggerById(tagDefinition.blockTriggerIds[j]);
                                if (trigger) {
                                    trigger.addReferencedTag(tag);
                                }
                            }
                        }

                        if (tagDefinition.fireTriggerIds && utils.isArray(tagDefinition.fireTriggerIds)) {
                            for (j = 0; j < tagDefinition.fireTriggerIds.length; j++) {
                                trigger = this.getTriggerById(tagDefinition.fireTriggerIds[j]);
                                if (trigger) {
                                    trigger.addReferencedTag(tag);
                                }
                            }
                        }
                    }
                }

                this.run = function () {
                    var missedEvents = dataLayer.getAllEvents();
                    var i;
                    for (i = 0; i < missedEvents.length; i++) {
                        this.onNewGlobalDataLayerValue(missedEvents[i]);
                    }

                    for (i = 0; i < this.triggers.length; i++) {
                        this.triggers[i].setUp();
                    }
                };
            }

            var TagManager = {
                THROW_ERRORS: true,
                dataLayer: dataLayer,
                containers: [],
                url: urlHelper,
                date: dateHelper,
                utils: utils,
                debug: Debug,
                dom: DOM,
                window: windowHelper,
                Variable: Variable,
                storage: {local: localStorage, session: sessionStorage},
                _buildVariable: buildVariable,
                Condition: Condition,
                TemplateParameters: TemplateParameters,
                Trigger: Trigger,
                Tag: Tag,
                throwError: throwError,
                Container: Container,
                addContainer: function (containerConfig, templates) {
                    var mtmSetDebugFlag = urlHelper.getQueryParameter('mtmSetDebugFlag');
                    if (mtmSetDebugFlag) {
                        var idSite = encodeURIComponent(containerConfig.idsite);
                        var containerID = encodeURIComponent(containerConfig.id);
                        if (mtmSetDebugFlag == 1) {
                            var date = new Date();
                            date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
                            document.cookie = 'mtmPreviewMode=mtmPreview' + idSite + '_' + containerID + '%3D1;expires=' + date.toUTCString() + ';SameSite=Lax';
                        } else {
                            document.cookie = 'mtmPreviewMode=mtmPreview' + idSite + '_' + containerID + '%3D1;expires=Thu, 01 Jan 1970 00:00:00 UTC;SameSite=Lax';
                            window.close();
                        }
                    }
                    if (!window.mtmPreviewWindow) {
                        // interesting when multiple containers are registered... we check if meanwhile a
                        // debug container has been added
                        var previewFrame = documentAlias.getElementById('mtmDebugFrame');

                        if (previewFrame && previewFrame.contentWindow) {
                            window.mtmPreviewWindow = previewFrame.contentWindow;
                        }
                    }

                    Debug.log('creating container');
                    var container = new Container(containerConfig, templates);
                    this.containers.push(container);
                    container.dataLayer.push({'mtm.containerId': container.id});
                    Debug.log('running container');

                    container.run();
                    return container;
                },
                enableDebugMode: function () { Debug.enabled = true; }
            };

            if ('matomoTagManagerAsyncInit' in windowAlias && utils.isFunction(windowAlias.matomoTagManagerAsyncInit)) {
                windowAlias.matomoTagManagerAsyncInit(TagManager);
            }
            function processMtmPush() {
                var i, j, methodName, parameterArray, theCall;

                for (i = 0; i < arguments.length; i += 1) {
                    theCall = null;
                    if (arguments[i] && arguments[i].slice) {
                        theCall = arguments[i].slice();
                    }
                    parameterArray = arguments[i];

                    if (utils.isObject(parameterArray) && !utils.isArray(parameterArray)) {
                        dataLayer.push(parameterArray); // we assume dataLayer push
                        continue;
                    }

                    methodName = parameterArray.shift();

                    var isStaticPluginCall = utils.isString(methodName) && methodName.indexOf('::') > 0;
                    if (isStaticPluginCall) {
                        var fParts, context;

                        // a static method will not be called on a tracker and is not dependent on the existence of a
                        // tracker etc
                        fParts = methodName.split('::');
                        context = fParts[0];
                        methodName = fParts[1];

                        if ('object' === typeof TagManager[context] && utils.isFunction(TagManager[context][methodName])) {
                            TagManager[context][methodName].apply(TagManager[context], parameterArray);
                        }
                    } else {
                        if (methodName && methodName in TagManager && utils.isFunction(TagManager[methodName])) {
                            TagManager[methodName].apply(TagManager, parameterArray);
                        } else {
                            Debug.error('method ' + methodName + ' is not valid');
                        }
                    }
                }
            }

            utils.setMethodWrapIfNeeded(windowAlias._mtm, 'push', processMtmPush);
            var i;
            for (i = 0; i < windowAlias._mtm.length; i++) {
                processMtmPush(windowAlias._mtm[i]);
            }

            dataLayer.push({'mtm.mtmScriptLoadedTime': timeScriptLoaded});

            if (('undefined' === typeof ignoreGtmDataLayer || !ignoreGtmDataLayer) && 'undefined' !== typeof windowAlias.dataLayer && utils.isArray(windowAlias.dataLayer)) {
                // compatibility for GTM
                for ( i = 0; i < windowAlias.dataLayer.length; i++) {
                    if (utils.isObject(windowAlias.dataLayer[i])) {
                        dataLayer.push(windowAlias.dataLayer[i]);
                    }
                }
            }

            // Only sync the dataLayer changes from GTM if the config has been set
            if (!('undefined' === typeof activelySyncGtmDataLayer) && activelySyncGtmDataLayer) {
                windowAlias.dataLayer = windowAlias.dataLayer || [];

                const syncDataLayer = function(array, callback) {
                    array.push = function(e) {
                        Array.prototype.push.call(array, e);
                        callback(array);
                    };
                }

                syncDataLayer(windowAlias.dataLayer, function (e) {
                    dataLayer.push(windowAlias.dataLayer[windowAlias.dataLayer.length - 1]);
                });
            }

            return TagManager;

        })();
    }

    // we initialize container outside regular code so multiple containers can be embedded on the same site

    /*!! initContainerHook */

})();
