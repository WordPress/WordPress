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
  privateApis: () => (/* reexport */ privateApis)
});

;// ./node_modules/route-recognizer/dist/route-recognizer.es.js
var createObject = Object.create;
function createMap() {
    var map = createObject(null);
    map["__"] = undefined;
    delete map["__"];
    return map;
}

var Target = function Target(path, matcher, delegate) {
    this.path = path;
    this.matcher = matcher;
    this.delegate = delegate;
};
Target.prototype.to = function to (target, callback) {
    var delegate = this.delegate;
    if (delegate && delegate.willAddRoute) {
        target = delegate.willAddRoute(this.matcher.target, target);
    }
    this.matcher.add(this.path, target);
    if (callback) {
        if (callback.length === 0) {
            throw new Error("You must have an argument in the function passed to `to`");
        }
        this.matcher.addChild(this.path, target, callback, this.delegate);
    }
};
var Matcher = function Matcher(target) {
    this.routes = createMap();
    this.children = createMap();
    this.target = target;
};
Matcher.prototype.add = function add (path, target) {
    this.routes[path] = target;
};
Matcher.prototype.addChild = function addChild (path, target, callback, delegate) {
    var matcher = new Matcher(target);
    this.children[path] = matcher;
    var match = generateMatch(path, matcher, delegate);
    if (delegate && delegate.contextEntered) {
        delegate.contextEntered(target, match);
    }
    callback(match);
};
function generateMatch(startingPath, matcher, delegate) {
    function match(path, callback) {
        var fullPath = startingPath + path;
        if (callback) {
            callback(generateMatch(fullPath, matcher, delegate));
        }
        else {
            return new Target(fullPath, matcher, delegate);
        }
    }
    
    return match;
}
function addRoute(routeArray, path, handler) {
    var len = 0;
    for (var i = 0; i < routeArray.length; i++) {
        len += routeArray[i].path.length;
    }
    path = path.substr(len);
    var route = { path: path, handler: handler };
    routeArray.push(route);
}
function eachRoute(baseRoute, matcher, callback, binding) {
    var routes = matcher.routes;
    var paths = Object.keys(routes);
    for (var i = 0; i < paths.length; i++) {
        var path = paths[i];
        var routeArray = baseRoute.slice();
        addRoute(routeArray, path, routes[path]);
        var nested = matcher.children[path];
        if (nested) {
            eachRoute(routeArray, nested, callback, binding);
        }
        else {
            callback.call(binding, routeArray);
        }
    }
}
var map = function (callback, addRouteCallback) {
    var matcher = new Matcher();
    callback(generateMatch("", matcher, this.delegate));
    eachRoute([], matcher, function (routes) {
        if (addRouteCallback) {
            addRouteCallback(this, routes);
        }
        else {
            this.add(routes);
        }
    }, this);
};

// Normalizes percent-encoded values in `path` to upper-case and decodes percent-encoded
// values that are not reserved (i.e., unicode characters, emoji, etc). The reserved
// chars are "/" and "%".
// Safe to call multiple times on the same path.
// Normalizes percent-encoded values in `path` to upper-case and decodes percent-encoded
function normalizePath(path) {
    return path.split("/")
        .map(normalizeSegment)
        .join("/");
}
// We want to ensure the characters "%" and "/" remain in percent-encoded
// form when normalizing paths, so replace them with their encoded form after
// decoding the rest of the path
var SEGMENT_RESERVED_CHARS = /%|\//g;
function normalizeSegment(segment) {
    if (segment.length < 3 || segment.indexOf("%") === -1)
        { return segment; }
    return decodeURIComponent(segment).replace(SEGMENT_RESERVED_CHARS, encodeURIComponent);
}
// We do not want to encode these characters when generating dynamic path segments
// See https://tools.ietf.org/html/rfc3986#section-3.3
// sub-delims: "!", "$", "&", "'", "(", ")", "*", "+", ",", ";", "="
// others allowed by RFC 3986: ":", "@"
//
// First encode the entire path segment, then decode any of the encoded special chars.
//
// The chars "!", "'", "(", ")", "*" do not get changed by `encodeURIComponent`,
// so the possible encoded chars are:
// ['%24', '%26', '%2B', '%2C', '%3B', '%3D', '%3A', '%40'].
var PATH_SEGMENT_ENCODINGS = /%(?:2(?:4|6|B|C)|3(?:B|D|A)|40)/g;
function encodePathSegment(str) {
    return encodeURIComponent(str).replace(PATH_SEGMENT_ENCODINGS, decodeURIComponent);
}

var escapeRegex = /(\/|\.|\*|\+|\?|\||\(|\)|\[|\]|\{|\}|\\)/g;
var isArray = Array.isArray;
var route_recognizer_es_hasOwnProperty = Object.prototype.hasOwnProperty;
function getParam(params, key) {
    if (typeof params !== "object" || params === null) {
        throw new Error("You must pass an object as the second argument to `generate`.");
    }
    if (!route_recognizer_es_hasOwnProperty.call(params, key)) {
        throw new Error("You must provide param `" + key + "` to `generate`.");
    }
    var value = params[key];
    var str = typeof value === "string" ? value : "" + value;
    if (str.length === 0) {
        throw new Error("You must provide a param `" + key + "`.");
    }
    return str;
}
var eachChar = [];
eachChar[0 /* Static */] = function (segment, currentState) {
    var state = currentState;
    var value = segment.value;
    for (var i = 0; i < value.length; i++) {
        var ch = value.charCodeAt(i);
        state = state.put(ch, false, false);
    }
    return state;
};
eachChar[1 /* Dynamic */] = function (_, currentState) {
    return currentState.put(47 /* SLASH */, true, true);
};
eachChar[2 /* Star */] = function (_, currentState) {
    return currentState.put(-1 /* ANY */, false, true);
};
eachChar[4 /* Epsilon */] = function (_, currentState) {
    return currentState;
};
var regex = [];
regex[0 /* Static */] = function (segment) {
    return segment.value.replace(escapeRegex, "\\$1");
};
regex[1 /* Dynamic */] = function () {
    return "([^/]+)";
};
regex[2 /* Star */] = function () {
    return "(.+)";
};
regex[4 /* Epsilon */] = function () {
    return "";
};
var generate = [];
generate[0 /* Static */] = function (segment) {
    return segment.value;
};
generate[1 /* Dynamic */] = function (segment, params) {
    var value = getParam(params, segment.value);
    if (RouteRecognizer.ENCODE_AND_DECODE_PATH_SEGMENTS) {
        return encodePathSegment(value);
    }
    else {
        return value;
    }
};
generate[2 /* Star */] = function (segment, params) {
    return getParam(params, segment.value);
};
generate[4 /* Epsilon */] = function () {
    return "";
};
var EmptyObject = Object.freeze({});
var EmptyArray = Object.freeze([]);
// The `names` will be populated with the paramter name for each dynamic/star
// segment. `shouldDecodes` will be populated with a boolean for each dyanamic/star
// segment, indicating whether it should be decoded during recognition.
function parse(segments, route, types) {
    // normalize route as not starting with a "/". Recognition will
    // also normalize.
    if (route.length > 0 && route.charCodeAt(0) === 47 /* SLASH */) {
        route = route.substr(1);
    }
    var parts = route.split("/");
    var names = undefined;
    var shouldDecodes = undefined;
    for (var i = 0; i < parts.length; i++) {
        var part = parts[i];
        var flags = 0;
        var type = 0;
        if (part === "") {
            type = 4 /* Epsilon */;
        }
        else if (part.charCodeAt(0) === 58 /* COLON */) {
            type = 1 /* Dynamic */;
        }
        else if (part.charCodeAt(0) === 42 /* STAR */) {
            type = 2 /* Star */;
        }
        else {
            type = 0 /* Static */;
        }
        flags = 2 << type;
        if (flags & 12 /* Named */) {
            part = part.slice(1);
            names = names || [];
            names.push(part);
            shouldDecodes = shouldDecodes || [];
            shouldDecodes.push((flags & 4 /* Decoded */) !== 0);
        }
        if (flags & 14 /* Counted */) {
            types[type]++;
        }
        segments.push({
            type: type,
            value: normalizeSegment(part)
        });
    }
    return {
        names: names || EmptyArray,
        shouldDecodes: shouldDecodes || EmptyArray,
    };
}
function isEqualCharSpec(spec, char, negate) {
    return spec.char === char && spec.negate === negate;
}
// A State has a character specification and (`charSpec`) and a list of possible
// subsequent states (`nextStates`).
//
// If a State is an accepting state, it will also have several additional
// properties:
//
// * `regex`: A regular expression that is used to extract parameters from paths
//   that reached this accepting state.
// * `handlers`: Information on how to convert the list of captures into calls
//   to registered handlers with the specified parameters
// * `types`: How many static, dynamic or star segments in this route. Used to
//   decide which route to use if multiple registered routes match a path.
//
// Currently, State is implemented naively by looping over `nextStates` and
// comparing a character specification against a character. A more efficient
// implementation would use a hash of keys pointing at one or more next states.
var State = function State(states, id, char, negate, repeat) {
    this.states = states;
    this.id = id;
    this.char = char;
    this.negate = negate;
    this.nextStates = repeat ? id : null;
    this.pattern = "";
    this._regex = undefined;
    this.handlers = undefined;
    this.types = undefined;
};
State.prototype.regex = function regex$1 () {
    if (!this._regex) {
        this._regex = new RegExp(this.pattern);
    }
    return this._regex;
};
State.prototype.get = function get (char, negate) {
        var this$1 = this;

    var nextStates = this.nextStates;
    if (nextStates === null)
        { return; }
    if (isArray(nextStates)) {
        for (var i = 0; i < nextStates.length; i++) {
            var child = this$1.states[nextStates[i]];
            if (isEqualCharSpec(child, char, negate)) {
                return child;
            }
        }
    }
    else {
        var child$1 = this.states[nextStates];
        if (isEqualCharSpec(child$1, char, negate)) {
            return child$1;
        }
    }
};
State.prototype.put = function put (char, negate, repeat) {
    var state;
    // If the character specification already exists in a child of the current
    // state, just return that state.
    if (state = this.get(char, negate)) {
        return state;
    }
    // Make a new state for the character spec
    var states = this.states;
    state = new State(states, states.length, char, negate, repeat);
    states[states.length] = state;
    // Insert the new state as a child of the current state
    if (this.nextStates == null) {
        this.nextStates = state.id;
    }
    else if (isArray(this.nextStates)) {
        this.nextStates.push(state.id);
    }
    else {
        this.nextStates = [this.nextStates, state.id];
    }
    // Return the new state
    return state;
};
// Find a list of child states matching the next character
State.prototype.match = function match (ch) {
        var this$1 = this;

    var nextStates = this.nextStates;
    if (!nextStates)
        { return []; }
    var returned = [];
    if (isArray(nextStates)) {
        for (var i = 0; i < nextStates.length; i++) {
            var child = this$1.states[nextStates[i]];
            if (isMatch(child, ch)) {
                returned.push(child);
            }
        }
    }
    else {
        var child$1 = this.states[nextStates];
        if (isMatch(child$1, ch)) {
            returned.push(child$1);
        }
    }
    return returned;
};
function isMatch(spec, char) {
    return spec.negate ? spec.char !== char && spec.char !== -1 /* ANY */ : spec.char === char || spec.char === -1 /* ANY */;
}
// This is a somewhat naive strategy, but should work in a lot of cases
// A better strategy would properly resolve /posts/:id/new and /posts/edit/:id.
//
// This strategy generally prefers more static and less dynamic matching.
// Specifically, it
//
//  * prefers fewer stars to more, then
//  * prefers using stars for less of the match to more, then
//  * prefers fewer dynamic segments to more, then
//  * prefers more static segments to more
function sortSolutions(states) {
    return states.sort(function (a, b) {
        var ref = a.types || [0, 0, 0];
        var astatics = ref[0];
        var adynamics = ref[1];
        var astars = ref[2];
        var ref$1 = b.types || [0, 0, 0];
        var bstatics = ref$1[0];
        var bdynamics = ref$1[1];
        var bstars = ref$1[2];
        if (astars !== bstars) {
            return astars - bstars;
        }
        if (astars) {
            if (astatics !== bstatics) {
                return bstatics - astatics;
            }
            if (adynamics !== bdynamics) {
                return bdynamics - adynamics;
            }
        }
        if (adynamics !== bdynamics) {
            return adynamics - bdynamics;
        }
        if (astatics !== bstatics) {
            return bstatics - astatics;
        }
        return 0;
    });
}
function recognizeChar(states, ch) {
    var nextStates = [];
    for (var i = 0, l = states.length; i < l; i++) {
        var state = states[i];
        nextStates = nextStates.concat(state.match(ch));
    }
    return nextStates;
}
var RecognizeResults = function RecognizeResults(queryParams) {
    this.length = 0;
    this.queryParams = queryParams || {};
};

RecognizeResults.prototype.splice = Array.prototype.splice;
RecognizeResults.prototype.slice = Array.prototype.slice;
RecognizeResults.prototype.push = Array.prototype.push;
function findHandler(state, originalPath, queryParams) {
    var handlers = state.handlers;
    var regex = state.regex();
    if (!regex || !handlers)
        { throw new Error("state not initialized"); }
    var captures = originalPath.match(regex);
    var currentCapture = 1;
    var result = new RecognizeResults(queryParams);
    result.length = handlers.length;
    for (var i = 0; i < handlers.length; i++) {
        var handler = handlers[i];
        var names = handler.names;
        var shouldDecodes = handler.shouldDecodes;
        var params = EmptyObject;
        var isDynamic = false;
        if (names !== EmptyArray && shouldDecodes !== EmptyArray) {
            for (var j = 0; j < names.length; j++) {
                isDynamic = true;
                var name = names[j];
                var capture = captures && captures[currentCapture++];
                if (params === EmptyObject) {
                    params = {};
                }
                if (RouteRecognizer.ENCODE_AND_DECODE_PATH_SEGMENTS && shouldDecodes[j]) {
                    params[name] = capture && decodeURIComponent(capture);
                }
                else {
                    params[name] = capture;
                }
            }
        }
        result[i] = {
            handler: handler.handler,
            params: params,
            isDynamic: isDynamic
        };
    }
    return result;
}
function decodeQueryParamPart(part) {
    // http://www.w3.org/TR/html401/interact/forms.html#h-17.13.4.1
    part = part.replace(/\+/gm, "%20");
    var result;
    try {
        result = decodeURIComponent(part);
    }
    catch (error) {
        result = "";
    }
    return result;
}
var RouteRecognizer = function RouteRecognizer() {
    this.names = createMap();
    var states = [];
    var state = new State(states, 0, -1 /* ANY */, true, false);
    states[0] = state;
    this.states = states;
    this.rootState = state;
};
RouteRecognizer.prototype.add = function add (routes, options) {
    var currentState = this.rootState;
    var pattern = "^";
    var types = [0, 0, 0];
    var handlers = new Array(routes.length);
    var allSegments = [];
    var isEmpty = true;
    var j = 0;
    for (var i = 0; i < routes.length; i++) {
        var route = routes[i];
        var ref = parse(allSegments, route.path, types);
            var names = ref.names;
            var shouldDecodes = ref.shouldDecodes;
        // preserve j so it points to the start of newly added segments
        for (; j < allSegments.length; j++) {
            var segment = allSegments[j];
            if (segment.type === 4 /* Epsilon */) {
                continue;
            }
            isEmpty = false;
            // Add a "/" for the new segment
            currentState = currentState.put(47 /* SLASH */, false, false);
            pattern += "/";
            // Add a representation of the segment to the NFA and regex
            currentState = eachChar[segment.type](segment, currentState);
            pattern += regex[segment.type](segment);
        }
        handlers[i] = {
            handler: route.handler,
            names: names,
            shouldDecodes: shouldDecodes
        };
    }
    if (isEmpty) {
        currentState = currentState.put(47 /* SLASH */, false, false);
        pattern += "/";
    }
    currentState.handlers = handlers;
    currentState.pattern = pattern + "$";
    currentState.types = types;
    var name;
    if (typeof options === "object" && options !== null && options.as) {
        name = options.as;
    }
    if (name) {
        // if (this.names[name]) {
        //   throw new Error("You may not add a duplicate route named `" + name + "`.");
        // }
        this.names[name] = {
            segments: allSegments,
            handlers: handlers
        };
    }
};
RouteRecognizer.prototype.handlersFor = function handlersFor (name) {
    var route = this.names[name];
    if (!route) {
        throw new Error("There is no route named " + name);
    }
    var result = new Array(route.handlers.length);
    for (var i = 0; i < route.handlers.length; i++) {
        var handler = route.handlers[i];
        result[i] = handler;
    }
    return result;
};
RouteRecognizer.prototype.hasRoute = function hasRoute (name) {
    return !!this.names[name];
};
RouteRecognizer.prototype.generate = function generate$1 (name, params) {
    var route = this.names[name];
    var output = "";
    if (!route) {
        throw new Error("There is no route named " + name);
    }
    var segments = route.segments;
    for (var i = 0; i < segments.length; i++) {
        var segment = segments[i];
        if (segment.type === 4 /* Epsilon */) {
            continue;
        }
        output += "/";
        output += generate[segment.type](segment, params);
    }
    if (output.charAt(0) !== "/") {
        output = "/" + output;
    }
    if (params && params.queryParams) {
        output += this.generateQueryString(params.queryParams);
    }
    return output;
};
RouteRecognizer.prototype.generateQueryString = function generateQueryString (params) {
    var pairs = [];
    var keys = Object.keys(params);
    keys.sort();
    for (var i = 0; i < keys.length; i++) {
        var key = keys[i];
        var value = params[key];
        if (value == null) {
            continue;
        }
        var pair = encodeURIComponent(key);
        if (isArray(value)) {
            for (var j = 0; j < value.length; j++) {
                var arrayPair = key + "[]" + "=" + encodeURIComponent(value[j]);
                pairs.push(arrayPair);
            }
        }
        else {
            pair += "=" + encodeURIComponent(value);
            pairs.push(pair);
        }
    }
    if (pairs.length === 0) {
        return "";
    }
    return "?" + pairs.join("&");
};
RouteRecognizer.prototype.parseQueryString = function parseQueryString (queryString) {
    var pairs = queryString.split("&");
    var queryParams = {};
    for (var i = 0; i < pairs.length; i++) {
        var pair = pairs[i].split("="), key = decodeQueryParamPart(pair[0]), keyLength = key.length, isArray = false, value = (void 0);
        if (pair.length === 1) {
            value = "true";
        }
        else {
            // Handle arrays
            if (keyLength > 2 && key.slice(keyLength - 2) === "[]") {
                isArray = true;
                key = key.slice(0, keyLength - 2);
                if (!queryParams[key]) {
                    queryParams[key] = [];
                }
            }
            value = pair[1] ? decodeQueryParamPart(pair[1]) : "";
        }
        if (isArray) {
            queryParams[key].push(value);
        }
        else {
            queryParams[key] = value;
        }
    }
    return queryParams;
};
RouteRecognizer.prototype.recognize = function recognize (path) {
    var results;
    var states = [this.rootState];
    var queryParams = {};
    var isSlashDropped = false;
    var hashStart = path.indexOf("#");
    if (hashStart !== -1) {
        path = path.substr(0, hashStart);
    }
    var queryStart = path.indexOf("?");
    if (queryStart !== -1) {
        var queryString = path.substr(queryStart + 1, path.length);
        path = path.substr(0, queryStart);
        queryParams = this.parseQueryString(queryString);
    }
    if (path.charAt(0) !== "/") {
        path = "/" + path;
    }
    var originalPath = path;
    if (RouteRecognizer.ENCODE_AND_DECODE_PATH_SEGMENTS) {
        path = normalizePath(path);
    }
    else {
        path = decodeURI(path);
        originalPath = decodeURI(originalPath);
    }
    var pathLen = path.length;
    if (pathLen > 1 && path.charAt(pathLen - 1) === "/") {
        path = path.substr(0, pathLen - 1);
        originalPath = originalPath.substr(0, originalPath.length - 1);
        isSlashDropped = true;
    }
    for (var i = 0; i < path.length; i++) {
        states = recognizeChar(states, path.charCodeAt(i));
        if (!states.length) {
            break;
        }
    }
    var solutions = [];
    for (var i$1 = 0; i$1 < states.length; i$1++) {
        if (states[i$1].handlers) {
            solutions.push(states[i$1]);
        }
    }
    states = sortSolutions(solutions);
    var state = solutions[0];
    if (state && state.handlers) {
        // if a trailing slash was dropped and a star segment is the last segment
        // specified, put the trailing slash back
        if (isSlashDropped && state.pattern && state.pattern.slice(-5) === "(.+)$") {
            originalPath = originalPath + "/";
        }
        results = findHandler(state, originalPath, queryParams);
    }
    return results;
};
RouteRecognizer.VERSION = "0.3.4";
// Set to false to opt-out of encoding and decoding path segments.
// See https://github.com/tildeio/route-recognizer/pull/55
RouteRecognizer.ENCODE_AND_DECODE_PATH_SEGMENTS = true;
RouteRecognizer.Normalizer = {
    normalizeSegment: normalizeSegment, normalizePath: normalizePath, encodePathSegment: encodePathSegment
};
RouteRecognizer.prototype.map = map;

/* harmony default export */ const route_recognizer_es = (RouteRecognizer);


;// ./node_modules/@babel/runtime/helpers/esm/extends.js
function extends_extends() {
  return extends_extends = Object.assign ? Object.assign.bind() : function (n) {
    for (var e = 1; e < arguments.length; e++) {
      var t = arguments[e];
      for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]);
    }
    return n;
  }, extends_extends.apply(null, arguments);
}

;// ./node_modules/history/index.js


/**
 * Actions represent the type of change to a location value.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#action
 */
var Action;

(function (Action) {
  /**
   * A POP indicates a change to an arbitrary index in the history stack, such
   * as a back or forward navigation. It does not describe the direction of the
   * navigation, only that the current index changed.
   *
   * Note: This is the default action for newly created history objects.
   */
  Action["Pop"] = "POP";
  /**
   * A PUSH indicates a new entry being added to the history stack, such as when
   * a link is clicked and a new page loads. When this happens, all subsequent
   * entries in the stack are lost.
   */

  Action["Push"] = "PUSH";
  /**
   * A REPLACE indicates the entry at the current index in the history stack
   * being replaced by a new one.
   */

  Action["Replace"] = "REPLACE";
})(Action || (Action = {}));

var readOnly =  false ? 0 : function (obj) {
  return obj;
};

function warning(cond, message) {
  if (!cond) {
    // eslint-disable-next-line no-console
    if (typeof console !== 'undefined') console.warn(message);

    try {
      // Welcome to debugging history!
      //
      // This error is thrown as a convenience so you can more easily
      // find the source for a warning that appears in the console by
      // enabling "pause on exceptions" in your JavaScript debugger.
      throw new Error(message); // eslint-disable-next-line no-empty
    } catch (e) {}
  }
}

var BeforeUnloadEventType = 'beforeunload';
var HashChangeEventType = 'hashchange';
var PopStateEventType = 'popstate';
/**
 * Browser history stores the location in regular URLs. This is the standard for
 * most web apps, but it requires some configuration on the server to ensure you
 * serve the same app at multiple URLs.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createbrowserhistory
 */

function createBrowserHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options = options,
      _options$window = _options.window,
      window = _options$window === void 0 ? document.defaultView : _options$window;
  var globalHistory = window.history;

  function getIndexAndLocation() {
    var _window$location = window.location,
        pathname = _window$location.pathname,
        search = _window$location.search,
        hash = _window$location.hash;
    var state = globalHistory.state || {};
    return [state.idx, readOnly({
      pathname: pathname,
      search: search,
      hash: hash,
      state: state.usr || null,
      key: state.key || 'default'
    })];
  }

  var blockedPopTx = null;

  function handlePop() {
    if (blockedPopTx) {
      blockers.call(blockedPopTx);
      blockedPopTx = null;
    } else {
      var nextAction = Action.Pop;

      var _getIndexAndLocation = getIndexAndLocation(),
          nextIndex = _getIndexAndLocation[0],
          nextLocation = _getIndexAndLocation[1];

      if (blockers.length) {
        if (nextIndex != null) {
          var delta = index - nextIndex;

          if (delta) {
            // Revert the POP
            blockedPopTx = {
              action: nextAction,
              location: nextLocation,
              retry: function retry() {
                go(delta * -1);
              }
            };
            go(delta);
          }
        } else {
          // Trying to POP to a location with no index. We did not create
          // this location, so we can't effectively block the navigation.
           false ? 0 : void 0;
        }
      } else {
        applyTx(nextAction);
      }
    }
  }

  window.addEventListener(PopStateEventType, handlePop);
  var action = Action.Pop;

  var _getIndexAndLocation2 = getIndexAndLocation(),
      index = _getIndexAndLocation2[0],
      location = _getIndexAndLocation2[1];

  var listeners = createEvents();
  var blockers = createEvents();

  if (index == null) {
    index = 0;
    globalHistory.replaceState(extends_extends({}, globalHistory.state, {
      idx: index
    }), '');
  }

  function createHref(to) {
    return typeof to === 'string' ? to : createPath(to);
  } // state defaults to `null` because `window.history.state` does


  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly(extends_extends({
      pathname: location.pathname,
      hash: '',
      search: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function getHistoryStateAndUrl(nextLocation, index) {
    return [{
      usr: nextLocation.state,
      key: nextLocation.key,
      idx: index
    }, createHref(nextLocation)];
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction) {
    action = nextAction;

    var _getIndexAndLocation3 = getIndexAndLocation();

    index = _getIndexAndLocation3[0];
    location = _getIndexAndLocation3[1];
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr = getHistoryStateAndUrl(nextLocation, index + 1),
          historyState = _getHistoryStateAndUr[0],
          url = _getHistoryStateAndUr[1]; // TODO: Support forced reloading
      // try...catch because iOS limits us to 100 pushState calls :/


      try {
        globalHistory.pushState(historyState, '', url);
      } catch (error) {
        // They are going to lose state here, but there is no real
        // way to warn them about it since the page will refresh...
        window.location.assign(url);
      }

      applyTx(nextAction);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr2 = getHistoryStateAndUrl(nextLocation, index),
          historyState = _getHistoryStateAndUr2[0],
          url = _getHistoryStateAndUr2[1]; // TODO: Support forced reloading


      globalHistory.replaceState(historyState, '', url);
      applyTx(nextAction);
    }
  }

  function go(delta) {
    globalHistory.go(delta);
  }

  var history = {
    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      var unblock = blockers.push(blocker);

      if (blockers.length === 1) {
        window.addEventListener(BeforeUnloadEventType, promptBeforeUnload);
      }

      return function () {
        unblock(); // Remove the beforeunload listener so the document may
        // still be salvageable in the pagehide event.
        // See https://html.spec.whatwg.org/#unloading-documents

        if (!blockers.length) {
          window.removeEventListener(BeforeUnloadEventType, promptBeforeUnload);
        }
      };
    }
  };
  return history;
}
/**
 * Hash history stores the location in window.location.hash. This makes it ideal
 * for situations where you don't want to send the location to the server for
 * some reason, either because you do cannot configure it or the URL space is
 * reserved for something else.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createhashhistory
 */

function createHashHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options2 = options,
      _options2$window = _options2.window,
      window = _options2$window === void 0 ? document.defaultView : _options2$window;
  var globalHistory = window.history;

  function getIndexAndLocation() {
    var _parsePath = parsePath(window.location.hash.substr(1)),
        _parsePath$pathname = _parsePath.pathname,
        pathname = _parsePath$pathname === void 0 ? '/' : _parsePath$pathname,
        _parsePath$search = _parsePath.search,
        search = _parsePath$search === void 0 ? '' : _parsePath$search,
        _parsePath$hash = _parsePath.hash,
        hash = _parsePath$hash === void 0 ? '' : _parsePath$hash;

    var state = globalHistory.state || {};
    return [state.idx, readOnly({
      pathname: pathname,
      search: search,
      hash: hash,
      state: state.usr || null,
      key: state.key || 'default'
    })];
  }

  var blockedPopTx = null;

  function handlePop() {
    if (blockedPopTx) {
      blockers.call(blockedPopTx);
      blockedPopTx = null;
    } else {
      var nextAction = Action.Pop;

      var _getIndexAndLocation4 = getIndexAndLocation(),
          nextIndex = _getIndexAndLocation4[0],
          nextLocation = _getIndexAndLocation4[1];

      if (blockers.length) {
        if (nextIndex != null) {
          var delta = index - nextIndex;

          if (delta) {
            // Revert the POP
            blockedPopTx = {
              action: nextAction,
              location: nextLocation,
              retry: function retry() {
                go(delta * -1);
              }
            };
            go(delta);
          }
        } else {
          // Trying to POP to a location with no index. We did not create
          // this location, so we can't effectively block the navigation.
           false ? 0 : void 0;
        }
      } else {
        applyTx(nextAction);
      }
    }
  }

  window.addEventListener(PopStateEventType, handlePop); // popstate does not fire on hashchange in IE 11 and old (trident) Edge
  // https://developer.mozilla.org/de/docs/Web/API/Window/popstate_event

  window.addEventListener(HashChangeEventType, function () {
    var _getIndexAndLocation5 = getIndexAndLocation(),
        nextLocation = _getIndexAndLocation5[1]; // Ignore extraneous hashchange events.


    if (createPath(nextLocation) !== createPath(location)) {
      handlePop();
    }
  });
  var action = Action.Pop;

  var _getIndexAndLocation6 = getIndexAndLocation(),
      index = _getIndexAndLocation6[0],
      location = _getIndexAndLocation6[1];

  var listeners = createEvents();
  var blockers = createEvents();

  if (index == null) {
    index = 0;
    globalHistory.replaceState(_extends({}, globalHistory.state, {
      idx: index
    }), '');
  }

  function getBaseHref() {
    var base = document.querySelector('base');
    var href = '';

    if (base && base.getAttribute('href')) {
      var url = window.location.href;
      var hashIndex = url.indexOf('#');
      href = hashIndex === -1 ? url : url.slice(0, hashIndex);
    }

    return href;
  }

  function createHref(to) {
    return getBaseHref() + '#' + (typeof to === 'string' ? to : createPath(to));
  }

  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly(_extends({
      pathname: location.pathname,
      hash: '',
      search: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function getHistoryStateAndUrl(nextLocation, index) {
    return [{
      usr: nextLocation.state,
      key: nextLocation.key,
      idx: index
    }, createHref(nextLocation)];
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction) {
    action = nextAction;

    var _getIndexAndLocation7 = getIndexAndLocation();

    index = _getIndexAndLocation7[0];
    location = _getIndexAndLocation7[1];
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

     false ? 0 : void 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr3 = getHistoryStateAndUrl(nextLocation, index + 1),
          historyState = _getHistoryStateAndUr3[0],
          url = _getHistoryStateAndUr3[1]; // TODO: Support forced reloading
      // try...catch because iOS limits us to 100 pushState calls :/


      try {
        globalHistory.pushState(historyState, '', url);
      } catch (error) {
        // They are going to lose state here, but there is no real
        // way to warn them about it since the page will refresh...
        window.location.assign(url);
      }

      applyTx(nextAction);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

     false ? 0 : void 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr4 = getHistoryStateAndUrl(nextLocation, index),
          historyState = _getHistoryStateAndUr4[0],
          url = _getHistoryStateAndUr4[1]; // TODO: Support forced reloading


      globalHistory.replaceState(historyState, '', url);
      applyTx(nextAction);
    }
  }

  function go(delta) {
    globalHistory.go(delta);
  }

  var history = {
    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      var unblock = blockers.push(blocker);

      if (blockers.length === 1) {
        window.addEventListener(BeforeUnloadEventType, promptBeforeUnload);
      }

      return function () {
        unblock(); // Remove the beforeunload listener so the document may
        // still be salvageable in the pagehide event.
        // See https://html.spec.whatwg.org/#unloading-documents

        if (!blockers.length) {
          window.removeEventListener(BeforeUnloadEventType, promptBeforeUnload);
        }
      };
    }
  };
  return history;
}
/**
 * Memory history stores the current location in memory. It is designed for use
 * in stateful non-browser environments like tests and React Native.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#creatememoryhistory
 */

function createMemoryHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options3 = options,
      _options3$initialEntr = _options3.initialEntries,
      initialEntries = _options3$initialEntr === void 0 ? ['/'] : _options3$initialEntr,
      initialIndex = _options3.initialIndex;
  var entries = initialEntries.map(function (entry) {
    var location = readOnly(_extends({
      pathname: '/',
      search: '',
      hash: '',
      state: null,
      key: createKey()
    }, typeof entry === 'string' ? parsePath(entry) : entry));
     false ? 0 : void 0;
    return location;
  });
  var index = clamp(initialIndex == null ? entries.length - 1 : initialIndex, 0, entries.length - 1);
  var action = Action.Pop;
  var location = entries[index];
  var listeners = createEvents();
  var blockers = createEvents();

  function createHref(to) {
    return typeof to === 'string' ? to : createPath(to);
  }

  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly(_extends({
      pathname: location.pathname,
      search: '',
      hash: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction, nextLocation) {
    action = nextAction;
    location = nextLocation;
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

     false ? 0 : void 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      index += 1;
      entries.splice(index, entries.length, nextLocation);
      applyTx(nextAction, nextLocation);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

     false ? 0 : void 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      entries[index] = nextLocation;
      applyTx(nextAction, nextLocation);
    }
  }

  function go(delta) {
    var nextIndex = clamp(index + delta, 0, entries.length - 1);
    var nextAction = Action.Pop;
    var nextLocation = entries[nextIndex];

    function retry() {
      go(delta);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      index = nextIndex;
      applyTx(nextAction, nextLocation);
    }
  }

  var history = {
    get index() {
      return index;
    },

    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      return blockers.push(blocker);
    }
  };
  return history;
} ////////////////////////////////////////////////////////////////////////////////
// UTILS
////////////////////////////////////////////////////////////////////////////////

function clamp(n, lowerBound, upperBound) {
  return Math.min(Math.max(n, lowerBound), upperBound);
}

function promptBeforeUnload(event) {
  // Cancel the event.
  event.preventDefault(); // Chrome (and legacy IE) requires returnValue to be set.

  event.returnValue = '';
}

function createEvents() {
  var handlers = [];
  return {
    get length() {
      return handlers.length;
    },

    push: function push(fn) {
      handlers.push(fn);
      return function () {
        handlers = handlers.filter(function (handler) {
          return handler !== fn;
        });
      };
    },
    call: function call(arg) {
      handlers.forEach(function (fn) {
        return fn && fn(arg);
      });
    }
  };
}

function createKey() {
  return Math.random().toString(36).substr(2, 8);
}
/**
 * Creates a string URL path from the given pathname, search, and hash components.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createpath
 */


function createPath(_ref) {
  var _ref$pathname = _ref.pathname,
      pathname = _ref$pathname === void 0 ? '/' : _ref$pathname,
      _ref$search = _ref.search,
      search = _ref$search === void 0 ? '' : _ref$search,
      _ref$hash = _ref.hash,
      hash = _ref$hash === void 0 ? '' : _ref$hash;
  if (search && search !== '?') pathname += search.charAt(0) === '?' ? search : '?' + search;
  if (hash && hash !== '#') pathname += hash.charAt(0) === '#' ? hash : '#' + hash;
  return pathname;
}
/**
 * Parses a string URL path into its separate pathname, search, and hash components.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#parsepath
 */

function parsePath(path) {
  var parsedPath = {};

  if (path) {
    var hashIndex = path.indexOf('#');

    if (hashIndex >= 0) {
      parsedPath.hash = path.substr(hashIndex);
      path = path.substr(0, hashIndex);
    }

    var searchIndex = path.indexOf('?');

    if (searchIndex >= 0) {
      parsedPath.search = path.substr(searchIndex);
      path = path.substr(0, searchIndex);
    }

    if (path) {
      parsedPath.pathname = path;
    }
  }

  return parsedPath;
}



;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./node_modules/@wordpress/router/build-module/router.js
/* wp:polyfill */
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

const router_history = createBrowserHistory();
const RoutesContext = (0,external_wp_element_namespaceObject.createContext)(null);
const ConfigContext = (0,external_wp_element_namespaceObject.createContext)({
  pathArg: 'p'
});
const locationMemo = new WeakMap();
function getLocationWithQuery() {
  const location = router_history.location;
  let locationWithQuery = locationMemo.get(location);
  if (!locationWithQuery) {
    locationWithQuery = {
      ...location,
      query: Object.fromEntries(new URLSearchParams(location.search))
    };
    locationMemo.set(location, locationWithQuery);
  }
  return locationWithQuery;
}
function useLocation() {
  const context = (0,external_wp_element_namespaceObject.useContext)(RoutesContext);
  if (!context) {
    throw new Error('useLocation must be used within a RouterProvider');
  }
  return context;
}
function useHistory() {
  const {
    pathArg,
    beforeNavigate
  } = (0,external_wp_element_namespaceObject.useContext)(ConfigContext);
  const navigate = (0,external_wp_compose_namespaceObject.useEvent)(async (rawPath, options = {}) => {
    var _getPath;
    const query = (0,external_wp_url_namespaceObject.getQueryArgs)(rawPath);
    const path = (_getPath = (0,external_wp_url_namespaceObject.getPath)('http://domain.com/' + rawPath)) !== null && _getPath !== void 0 ? _getPath : '';
    const performPush = () => {
      const result = beforeNavigate ? beforeNavigate({
        path,
        query
      }) : {
        path,
        query
      };
      return router_history.push({
        search: (0,external_wp_url_namespaceObject.buildQueryString)({
          [pathArg]: result.path,
          ...result.query
        })
      }, options.state);
    };

    /*
     * Skip transition in mobile, otherwise it crashes the browser.
     * See: https://github.com/WordPress/gutenberg/pull/63002.
     */
    const isMediumOrBigger = window.matchMedia('(min-width: 782px)').matches;
    if (!isMediumOrBigger || !document.startViewTransition || !options.transition) {
      performPush();
      return;
    }
    await new Promise(resolve => {
      var _options$transition;
      const classname = (_options$transition = options.transition) !== null && _options$transition !== void 0 ? _options$transition : '';
      document.documentElement.classList.add(classname);
      const transition = document.startViewTransition(() => performPush());
      transition.finished.finally(() => {
        document.documentElement.classList.remove(classname);
        resolve();
      });
    });
  });
  return (0,external_wp_element_namespaceObject.useMemo)(() => ({
    navigate,
    back: router_history.back
  }), [navigate]);
}
function useMatch(location, matcher, pathArg, matchResolverArgs) {
  const {
    query: rawQuery = {}
  } = location;
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    const {
      [pathArg]: path = '/',
      ...query
    } = rawQuery;
    const result = matcher.recognize(path)?.[0];
    if (!result) {
      return {
        name: '404',
        path: (0,external_wp_url_namespaceObject.addQueryArgs)(path, query),
        areas: {},
        widths: {},
        query,
        params: {}
      };
    }
    const matchedRoute = result.handler;
    const resolveFunctions = (record = {}) => {
      return Object.fromEntries(Object.entries(record).map(([key, value]) => {
        if (typeof value === 'function') {
          return [key, value({
            query,
            params: result.params,
            ...matchResolverArgs
          })];
        }
        return [key, value];
      }));
    };
    return {
      name: matchedRoute.name,
      areas: resolveFunctions(matchedRoute.areas),
      widths: resolveFunctions(matchedRoute.widths),
      params: result.params,
      query,
      path: (0,external_wp_url_namespaceObject.addQueryArgs)(path, query)
    };
  }, [matcher, rawQuery, pathArg, matchResolverArgs]);
}
function RouterProvider({
  routes,
  pathArg,
  beforeNavigate,
  children,
  matchResolverArgs
}) {
  const location = (0,external_wp_element_namespaceObject.useSyncExternalStore)(router_history.listen, getLocationWithQuery, getLocationWithQuery);
  const matcher = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const ret = new route_recognizer_es();
    routes.forEach(route => {
      ret.add([{
        path: route.path,
        handler: route
      }], {
        as: route.name
      });
    });
    return ret;
  }, [routes]);
  const match = useMatch(location, matcher, pathArg, matchResolverArgs);
  const config = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    beforeNavigate,
    pathArg
  }), [beforeNavigate, pathArg]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ConfigContext.Provider, {
    value: config,
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(RoutesContext.Provider, {
      value: match,
      children: children
    })
  });
}

;// ./node_modules/@wordpress/router/build-module/link.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function useLink(to, options = {}) {
  var _getPath;
  const history = useHistory();
  const {
    pathArg,
    beforeNavigate
  } = (0,external_wp_element_namespaceObject.useContext)(ConfigContext);
  function onClick(event) {
    event?.preventDefault();
    history.navigate(to, options);
  }
  const query = (0,external_wp_url_namespaceObject.getQueryArgs)(to);
  const path = (_getPath = (0,external_wp_url_namespaceObject.getPath)('http://domain.com/' + to)) !== null && _getPath !== void 0 ? _getPath : '';
  const link = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return beforeNavigate ? beforeNavigate({
      path,
      query
    }) : {
      path,
      query
    };
  }, [path, query, beforeNavigate]);
  const [before] = window.location.href.split('?');
  return {
    href: `${before}?${(0,external_wp_url_namespaceObject.buildQueryString)({
      [pathArg]: link.path,
      ...link.query
    })}`,
    onClick
  };
}
function Link({
  to,
  options,
  children,
  ...props
}) {
  const {
    href,
    onClick
  } = useLink(to, options);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("a", {
    href: href,
    onClick: onClick,
    ...props,
    children: children
  });
}

;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/router/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.', '@wordpress/router');

;// ./node_modules/@wordpress/router/build-module/private-apis.js
/**
 * Internal dependencies
 */



const privateApis = {};
lock(privateApis, {
  useHistory: useHistory,
  useLocation: useLocation,
  RouterProvider: RouterProvider,
  useLink: useLink,
  Link: Link
});

;// ./node_modules/@wordpress/router/build-module/index.js


(window.wp = window.wp || {}).router = __webpack_exports__;
/******/ })()
;