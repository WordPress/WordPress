"use strict";
var wp;
(wp ||= {}).router = (() => {
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
  var __copyProps = (to2, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to2, key) && key !== except)
          __defProp(to2, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to2;
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

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // packages/router/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    privateApis: () => privateApis
  });

  // node_modules/route-recognizer/dist/route-recognizer.es.js
  var createObject = Object.create;
  function createMap() {
    var map2 = createObject(null);
    map2["__"] = void 0;
    delete map2["__"];
    return map2;
  }
  var Target = function Target2(path, matcher, delegate) {
    this.path = path;
    this.matcher = matcher;
    this.delegate = delegate;
  };
  Target.prototype.to = function to(target, callback) {
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
  var Matcher = function Matcher2(target) {
    this.routes = createMap();
    this.children = createMap();
    this.target = target;
  };
  Matcher.prototype.add = function add(path, target) {
    this.routes[path] = target;
  };
  Matcher.prototype.addChild = function addChild(path, target, callback, delegate) {
    var matcher = new Matcher(target);
    this.children[path] = matcher;
    var match2 = generateMatch(path, matcher, delegate);
    if (delegate && delegate.contextEntered) {
      delegate.contextEntered(target, match2);
    }
    callback(match2);
  };
  function generateMatch(startingPath, matcher, delegate) {
    function match2(path, callback) {
      var fullPath = startingPath + path;
      if (callback) {
        callback(generateMatch(fullPath, matcher, delegate));
      } else {
        return new Target(fullPath, matcher, delegate);
      }
    }
    return match2;
  }
  function addRoute(routeArray, path, handler) {
    var len = 0;
    for (var i = 0; i < routeArray.length; i++) {
      len += routeArray[i].path.length;
    }
    path = path.substr(len);
    var route = { path, handler };
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
      } else {
        callback.call(binding, routeArray);
      }
    }
  }
  var map = function(callback, addRouteCallback) {
    var matcher = new Matcher();
    callback(generateMatch("", matcher, this.delegate));
    eachRoute([], matcher, function(routes) {
      if (addRouteCallback) {
        addRouteCallback(this, routes);
      } else {
        this.add(routes);
      }
    }, this);
  };
  function normalizePath(path) {
    return path.split("/").map(normalizeSegment).join("/");
  }
  var SEGMENT_RESERVED_CHARS = /%|\//g;
  function normalizeSegment(segment) {
    if (segment.length < 3 || segment.indexOf("%") === -1) {
      return segment;
    }
    return decodeURIComponent(segment).replace(SEGMENT_RESERVED_CHARS, encodeURIComponent);
  }
  var PATH_SEGMENT_ENCODINGS = /%(?:2(?:4|6|B|C)|3(?:B|D|A)|40)/g;
  function encodePathSegment(str) {
    return encodeURIComponent(str).replace(PATH_SEGMENT_ENCODINGS, decodeURIComponent);
  }
  var escapeRegex = /(\/|\.|\*|\+|\?|\||\(|\)|\[|\]|\{|\}|\\)/g;
  var isArray = Array.isArray;
  var hasOwnProperty = Object.prototype.hasOwnProperty;
  function getParam(params, key) {
    if (typeof params !== "object" || params === null) {
      throw new Error("You must pass an object as the second argument to `generate`.");
    }
    if (!hasOwnProperty.call(params, key)) {
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
  eachChar[
    0
    /* Static */
  ] = function(segment, currentState) {
    var state = currentState;
    var value = segment.value;
    for (var i = 0; i < value.length; i++) {
      var ch = value.charCodeAt(i);
      state = state.put(ch, false, false);
    }
    return state;
  };
  eachChar[
    1
    /* Dynamic */
  ] = function(_, currentState) {
    return currentState.put(47, true, true);
  };
  eachChar[
    2
    /* Star */
  ] = function(_, currentState) {
    return currentState.put(-1, false, true);
  };
  eachChar[
    4
    /* Epsilon */
  ] = function(_, currentState) {
    return currentState;
  };
  var regex = [];
  regex[
    0
    /* Static */
  ] = function(segment) {
    return segment.value.replace(escapeRegex, "\\$1");
  };
  regex[
    1
    /* Dynamic */
  ] = function() {
    return "([^/]+)";
  };
  regex[
    2
    /* Star */
  ] = function() {
    return "(.+)";
  };
  regex[
    4
    /* Epsilon */
  ] = function() {
    return "";
  };
  var generate = [];
  generate[
    0
    /* Static */
  ] = function(segment) {
    return segment.value;
  };
  generate[
    1
    /* Dynamic */
  ] = function(segment, params) {
    var value = getParam(params, segment.value);
    if (RouteRecognizer.ENCODE_AND_DECODE_PATH_SEGMENTS) {
      return encodePathSegment(value);
    } else {
      return value;
    }
  };
  generate[
    2
    /* Star */
  ] = function(segment, params) {
    return getParam(params, segment.value);
  };
  generate[
    4
    /* Epsilon */
  ] = function() {
    return "";
  };
  var EmptyObject = Object.freeze({});
  var EmptyArray = Object.freeze([]);
  function parse(segments, route, types) {
    if (route.length > 0 && route.charCodeAt(0) === 47) {
      route = route.substr(1);
    }
    var parts = route.split("/");
    var names = void 0;
    var shouldDecodes = void 0;
    for (var i = 0; i < parts.length; i++) {
      var part = parts[i];
      var flags = 0;
      var type = 0;
      if (part === "") {
        type = 4;
      } else if (part.charCodeAt(0) === 58) {
        type = 1;
      } else if (part.charCodeAt(0) === 42) {
        type = 2;
      } else {
        type = 0;
      }
      flags = 2 << type;
      if (flags & 12) {
        part = part.slice(1);
        names = names || [];
        names.push(part);
        shouldDecodes = shouldDecodes || [];
        shouldDecodes.push((flags & 4) !== 0);
      }
      if (flags & 14) {
        types[type]++;
      }
      segments.push({
        type,
        value: normalizeSegment(part)
      });
    }
    return {
      names: names || EmptyArray,
      shouldDecodes: shouldDecodes || EmptyArray
    };
  }
  function isEqualCharSpec(spec, char, negate) {
    return spec.char === char && spec.negate === negate;
  }
  var State = function State2(states, id, char, negate, repeat) {
    this.states = states;
    this.id = id;
    this.char = char;
    this.negate = negate;
    this.nextStates = repeat ? id : null;
    this.pattern = "";
    this._regex = void 0;
    this.handlers = void 0;
    this.types = void 0;
  };
  State.prototype.regex = function regex$1() {
    if (!this._regex) {
      this._regex = new RegExp(this.pattern);
    }
    return this._regex;
  };
  State.prototype.get = function get(char, negate) {
    var this$1 = this;
    var nextStates = this.nextStates;
    if (nextStates === null) {
      return;
    }
    if (isArray(nextStates)) {
      for (var i = 0; i < nextStates.length; i++) {
        var child = this$1.states[nextStates[i]];
        if (isEqualCharSpec(child, char, negate)) {
          return child;
        }
      }
    } else {
      var child$1 = this.states[nextStates];
      if (isEqualCharSpec(child$1, char, negate)) {
        return child$1;
      }
    }
  };
  State.prototype.put = function put(char, negate, repeat) {
    var state;
    if (state = this.get(char, negate)) {
      return state;
    }
    var states = this.states;
    state = new State(states, states.length, char, negate, repeat);
    states[states.length] = state;
    if (this.nextStates == null) {
      this.nextStates = state.id;
    } else if (isArray(this.nextStates)) {
      this.nextStates.push(state.id);
    } else {
      this.nextStates = [this.nextStates, state.id];
    }
    return state;
  };
  State.prototype.match = function match(ch) {
    var this$1 = this;
    var nextStates = this.nextStates;
    if (!nextStates) {
      return [];
    }
    var returned = [];
    if (isArray(nextStates)) {
      for (var i = 0; i < nextStates.length; i++) {
        var child = this$1.states[nextStates[i]];
        if (isMatch(child, ch)) {
          returned.push(child);
        }
      }
    } else {
      var child$1 = this.states[nextStates];
      if (isMatch(child$1, ch)) {
        returned.push(child$1);
      }
    }
    return returned;
  };
  function isMatch(spec, char) {
    return spec.negate ? spec.char !== char && spec.char !== -1 : spec.char === char || spec.char === -1;
  }
  function sortSolutions(states) {
    return states.sort(function(a, b) {
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
  var RecognizeResults = function RecognizeResults2(queryParams) {
    this.length = 0;
    this.queryParams = queryParams || {};
  };
  RecognizeResults.prototype.splice = Array.prototype.splice;
  RecognizeResults.prototype.slice = Array.prototype.slice;
  RecognizeResults.prototype.push = Array.prototype.push;
  function findHandler(state, originalPath, queryParams) {
    var handlers = state.handlers;
    var regex2 = state.regex();
    if (!regex2 || !handlers) {
      throw new Error("state not initialized");
    }
    var captures = originalPath.match(regex2);
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
          } else {
            params[name] = capture;
          }
        }
      }
      result[i] = {
        handler: handler.handler,
        params,
        isDynamic
      };
    }
    return result;
  }
  function decodeQueryParamPart(part) {
    part = part.replace(/\+/gm, "%20");
    var result;
    try {
      result = decodeURIComponent(part);
    } catch (error) {
      result = "";
    }
    return result;
  }
  var RouteRecognizer = function RouteRecognizer2() {
    this.names = createMap();
    var states = [];
    var state = new State(states, 0, -1, true, false);
    states[0] = state;
    this.states = states;
    this.rootState = state;
  };
  RouteRecognizer.prototype.add = function add2(routes, options) {
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
      for (; j < allSegments.length; j++) {
        var segment = allSegments[j];
        if (segment.type === 4) {
          continue;
        }
        isEmpty = false;
        currentState = currentState.put(47, false, false);
        pattern += "/";
        currentState = eachChar[segment.type](segment, currentState);
        pattern += regex[segment.type](segment);
      }
      handlers[i] = {
        handler: route.handler,
        names,
        shouldDecodes
      };
    }
    if (isEmpty) {
      currentState = currentState.put(47, false, false);
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
      this.names[name] = {
        segments: allSegments,
        handlers
      };
    }
  };
  RouteRecognizer.prototype.handlersFor = function handlersFor(name) {
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
  RouteRecognizer.prototype.hasRoute = function hasRoute(name) {
    return !!this.names[name];
  };
  RouteRecognizer.prototype.generate = function generate$1(name, params) {
    var route = this.names[name];
    var output = "";
    if (!route) {
      throw new Error("There is no route named " + name);
    }
    var segments = route.segments;
    for (var i = 0; i < segments.length; i++) {
      var segment = segments[i];
      if (segment.type === 4) {
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
  RouteRecognizer.prototype.generateQueryString = function generateQueryString(params) {
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
          var arrayPair = key + "[]=" + encodeURIComponent(value[j]);
          pairs.push(arrayPair);
        }
      } else {
        pair += "=" + encodeURIComponent(value);
        pairs.push(pair);
      }
    }
    if (pairs.length === 0) {
      return "";
    }
    return "?" + pairs.join("&");
  };
  RouteRecognizer.prototype.parseQueryString = function parseQueryString(queryString) {
    var pairs = queryString.split("&");
    var queryParams = {};
    for (var i = 0; i < pairs.length; i++) {
      var pair = pairs[i].split("="), key = decodeQueryParamPart(pair[0]), keyLength = key.length, isArray2 = false, value = void 0;
      if (pair.length === 1) {
        value = "true";
      } else {
        if (keyLength > 2 && key.slice(keyLength - 2) === "[]") {
          isArray2 = true;
          key = key.slice(0, keyLength - 2);
          if (!queryParams[key]) {
            queryParams[key] = [];
          }
        }
        value = pair[1] ? decodeQueryParamPart(pair[1]) : "";
      }
      if (isArray2) {
        queryParams[key].push(value);
      } else {
        queryParams[key] = value;
      }
    }
    return queryParams;
  };
  RouteRecognizer.prototype.recognize = function recognize(path) {
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
    } else {
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
      if (isSlashDropped && state.pattern && state.pattern.slice(-5) === "(.+)$") {
        originalPath = originalPath + "/";
      }
      results = findHandler(state, originalPath, queryParams);
    }
    return results;
  };
  RouteRecognizer.VERSION = "0.3.4";
  RouteRecognizer.ENCODE_AND_DECODE_PATH_SEGMENTS = true;
  RouteRecognizer.Normalizer = {
    normalizeSegment,
    normalizePath,
    encodePathSegment
  };
  RouteRecognizer.prototype.map = map;
  var route_recognizer_es_default = RouteRecognizer;

  // node_modules/@babel/runtime/helpers/esm/extends.js
  function _extends() {
    return _extends = Object.assign ? Object.assign.bind() : function(n) {
      for (var e = 1; e < arguments.length; e++) {
        var t = arguments[e];
        for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]);
      }
      return n;
    }, _extends.apply(null, arguments);
  }

  // node_modules/history/index.js
  var Action;
  (function(Action2) {
    Action2["Pop"] = "POP";
    Action2["Push"] = "PUSH";
    Action2["Replace"] = "REPLACE";
  })(Action || (Action = {}));
  var readOnly = true ? function(obj) {
    return Object.freeze(obj);
  } : function(obj) {
    return obj;
  };
  function warning(cond, message) {
    if (!cond) {
      if (typeof console !== "undefined") console.warn(message);
      try {
        throw new Error(message);
      } catch (e) {
      }
    }
  }
  var BeforeUnloadEventType = "beforeunload";
  var PopStateEventType = "popstate";
  function createBrowserHistory(options) {
    if (options === void 0) {
      options = {};
    }
    var _options = options, _options$window = _options.window, window2 = _options$window === void 0 ? document.defaultView : _options$window;
    var globalHistory = window2.history;
    function getIndexAndLocation() {
      var _window$location = window2.location, pathname = _window$location.pathname, search = _window$location.search, hash = _window$location.hash;
      var state = globalHistory.state || {};
      return [state.idx, readOnly({
        pathname,
        search,
        hash,
        state: state.usr || null,
        key: state.key || "default"
      })];
    }
    var blockedPopTx = null;
    function handlePop() {
      if (blockedPopTx) {
        blockers.call(blockedPopTx);
        blockedPopTx = null;
      } else {
        var nextAction = Action.Pop;
        var _getIndexAndLocation = getIndexAndLocation(), nextIndex = _getIndexAndLocation[0], nextLocation = _getIndexAndLocation[1];
        if (blockers.length) {
          if (nextIndex != null) {
            var delta = index - nextIndex;
            if (delta) {
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
            true ? warning(
              false,
              // TODO: Write up a doc that explains our blocking strategy in
              // detail and link to it here so people can understand better what
              // is going on and how to avoid it.
              "You are trying to block a POP navigation to a location that was not created by the history library. The block will fail silently in production, but in general you should do all navigation with the history library (instead of using window.history.pushState directly) to avoid this situation."
            ) : void 0;
          }
        } else {
          applyTx(nextAction);
        }
      }
    }
    window2.addEventListener(PopStateEventType, handlePop);
    var action = Action.Pop;
    var _getIndexAndLocation2 = getIndexAndLocation(), index = _getIndexAndLocation2[0], location = _getIndexAndLocation2[1];
    var listeners = createEvents();
    var blockers = createEvents();
    if (index == null) {
      index = 0;
      globalHistory.replaceState(_extends({}, globalHistory.state, {
        idx: index
      }), "");
    }
    function createHref(to2) {
      return typeof to2 === "string" ? to2 : createPath(to2);
    }
    function getNextLocation(to2, state) {
      if (state === void 0) {
        state = null;
      }
      return readOnly(_extends({
        pathname: location.pathname,
        hash: "",
        search: ""
      }, typeof to2 === "string" ? parsePath(to2) : to2, {
        state,
        key: createKey()
      }));
    }
    function getHistoryStateAndUrl(nextLocation, index2) {
      return [{
        usr: nextLocation.state,
        key: nextLocation.key,
        idx: index2
      }, createHref(nextLocation)];
    }
    function allowTx(action2, location2, retry) {
      return !blockers.length || (blockers.call({
        action: action2,
        location: location2,
        retry
      }), false);
    }
    function applyTx(nextAction) {
      action = nextAction;
      var _getIndexAndLocation3 = getIndexAndLocation();
      index = _getIndexAndLocation3[0];
      location = _getIndexAndLocation3[1];
      listeners.call({
        action,
        location
      });
    }
    function push(to2, state) {
      var nextAction = Action.Push;
      var nextLocation = getNextLocation(to2, state);
      function retry() {
        push(to2, state);
      }
      if (allowTx(nextAction, nextLocation, retry)) {
        var _getHistoryStateAndUr = getHistoryStateAndUrl(nextLocation, index + 1), historyState = _getHistoryStateAndUr[0], url = _getHistoryStateAndUr[1];
        try {
          globalHistory.pushState(historyState, "", url);
        } catch (error) {
          window2.location.assign(url);
        }
        applyTx(nextAction);
      }
    }
    function replace(to2, state) {
      var nextAction = Action.Replace;
      var nextLocation = getNextLocation(to2, state);
      function retry() {
        replace(to2, state);
      }
      if (allowTx(nextAction, nextLocation, retry)) {
        var _getHistoryStateAndUr2 = getHistoryStateAndUrl(nextLocation, index), historyState = _getHistoryStateAndUr2[0], url = _getHistoryStateAndUr2[1];
        globalHistory.replaceState(historyState, "", url);
        applyTx(nextAction);
      }
    }
    function go(delta) {
      globalHistory.go(delta);
    }
    var history2 = {
      get action() {
        return action;
      },
      get location() {
        return location;
      },
      createHref,
      push,
      replace,
      go,
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
          window2.addEventListener(BeforeUnloadEventType, promptBeforeUnload);
        }
        return function() {
          unblock();
          if (!blockers.length) {
            window2.removeEventListener(BeforeUnloadEventType, promptBeforeUnload);
          }
        };
      }
    };
    return history2;
  }
  function promptBeforeUnload(event) {
    event.preventDefault();
    event.returnValue = "";
  }
  function createEvents() {
    var handlers = [];
    return {
      get length() {
        return handlers.length;
      },
      push: function push(fn) {
        handlers.push(fn);
        return function() {
          handlers = handlers.filter(function(handler) {
            return handler !== fn;
          });
        };
      },
      call: function call(arg) {
        handlers.forEach(function(fn) {
          return fn && fn(arg);
        });
      }
    };
  }
  function createKey() {
    return Math.random().toString(36).substr(2, 8);
  }
  function createPath(_ref) {
    var _ref$pathname = _ref.pathname, pathname = _ref$pathname === void 0 ? "/" : _ref$pathname, _ref$search = _ref.search, search = _ref$search === void 0 ? "" : _ref$search, _ref$hash = _ref.hash, hash = _ref$hash === void 0 ? "" : _ref$hash;
    if (search && search !== "?") pathname += search.charAt(0) === "?" ? search : "?" + search;
    if (hash && hash !== "#") pathname += hash.charAt(0) === "#" ? hash : "#" + hash;
    return pathname;
  }
  function parsePath(path) {
    var parsedPath = {};
    if (path) {
      var hashIndex = path.indexOf("#");
      if (hashIndex >= 0) {
        parsedPath.hash = path.substr(hashIndex);
        path = path.substr(0, hashIndex);
      }
      var searchIndex = path.indexOf("?");
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

  // packages/router/build-module/router.js
  var import_element = __toESM(require_element());
  var import_url = __toESM(require_url());
  var import_compose = __toESM(require_compose());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var history = createBrowserHistory();
  var RoutesContext = (0, import_element.createContext)(null);
  RoutesContext.displayName = "RoutesContext";
  var ConfigContext = (0, import_element.createContext)({ pathArg: "p" });
  ConfigContext.displayName = "ConfigContext";
  var locationMemo = /* @__PURE__ */ new WeakMap();
  function getLocationWithQuery() {
    const location = history.location;
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
    const context = (0, import_element.useContext)(RoutesContext);
    if (!context) {
      throw new Error("useLocation must be used within a RouterProvider");
    }
    return context;
  }
  function useHistory() {
    const { pathArg, beforeNavigate } = (0, import_element.useContext)(ConfigContext);
    const navigate = (0, import_compose.useEvent)(
      async (rawPath, options = {}) => {
        const query = (0, import_url.getQueryArgs)(rawPath);
        const path = (0, import_url.getPath)("http://domain.com/" + rawPath) ?? "";
        const performPush = () => {
          const result = beforeNavigate ? beforeNavigate({ path, query }) : { path, query };
          return history.push(
            {
              search: (0, import_url.buildQueryString)({
                [pathArg]: result.path,
                ...result.query
              })
            },
            options.state
          );
        };
        const isMediumOrBigger = window.matchMedia("(min-width: 782px)").matches;
        if (!isMediumOrBigger || !document.startViewTransition || !options.transition) {
          performPush();
          return;
        }
        await new Promise((resolve) => {
          const classname = options.transition ?? "";
          document.documentElement.classList.add(classname);
          const transition = document.startViewTransition(
            () => performPush()
          );
          transition.finished.finally(() => {
            document.documentElement.classList.remove(classname);
            resolve();
          });
        });
      }
    );
    return (0, import_element.useMemo)(
      () => ({
        navigate,
        back: history.back,
        invalidate: () => {
          history.replace({
            search: history.location.search
          });
        }
      }),
      [navigate]
    );
  }
  function useMatch(location, matcher, pathArg, matchResolverArgs) {
    const { query: rawQuery = {} } = location;
    const [resolvedMatch, setMatch] = (0, import_element.useState)();
    (0, import_element.useEffect)(() => {
      const { [pathArg]: path = "/", ...query } = rawQuery;
      const ret = matcher.recognize(path)?.[0];
      async function resolveMatch(result) {
        const matchedRoute = result.handler;
        const resolveFunctions = async (record = {}) => {
          const entries = await Promise.all(
            Object.entries(record).map(async ([key, value]) => {
              if (typeof value === "function") {
                return [
                  key,
                  await value({
                    query,
                    params: result.params,
                    ...matchResolverArgs
                  })
                ];
              }
              return [key, value];
            })
          );
          return Object.fromEntries(entries);
        };
        const [resolvedAreas, resolvedWidths] = await Promise.all([
          resolveFunctions(matchedRoute.areas),
          resolveFunctions(matchedRoute.widths)
        ]);
        setMatch({
          name: matchedRoute.name,
          areas: resolvedAreas,
          widths: resolvedWidths,
          params: result.params,
          query,
          path: (0, import_url.addQueryArgs)(path, query)
        });
      }
      if (!ret) {
        setMatch({
          name: "404",
          path: (0, import_url.addQueryArgs)(path, query),
          areas: {},
          widths: {},
          query,
          params: {}
        });
      } else {
        resolveMatch(ret);
      }
      return () => setMatch(void 0);
    }, [matcher, rawQuery, pathArg, matchResolverArgs]);
    return resolvedMatch;
  }
  function RouterProvider({
    routes,
    pathArg,
    beforeNavigate,
    children,
    matchResolverArgs
  }) {
    const location = (0, import_element.useSyncExternalStore)(
      history.listen,
      getLocationWithQuery,
      getLocationWithQuery
    );
    const matcher = (0, import_element.useMemo)(() => {
      const ret = new route_recognizer_es_default();
      (routes ?? []).forEach((route) => {
        ret.add([{ path: route.path, handler: route }], {
          as: route.name
        });
      });
      return ret;
    }, [routes]);
    const match2 = useMatch(location, matcher, pathArg, matchResolverArgs);
    const previousMatch = (0, import_compose.usePrevious)(match2);
    const config = (0, import_element.useMemo)(
      () => ({ beforeNavigate, pathArg }),
      [beforeNavigate, pathArg]
    );
    const renderedMatch = match2 || previousMatch;
    if (!renderedMatch) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(ConfigContext.Provider, { value: config, children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(RoutesContext.Provider, { value: renderedMatch, children }) });
  }

  // packages/router/build-module/link.js
  var import_element2 = __toESM(require_element());
  var import_url2 = __toESM(require_url());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  function useLink(to2, options = {}) {
    const history2 = useHistory();
    const { pathArg, beforeNavigate } = (0, import_element2.useContext)(ConfigContext);
    function onClick(event) {
      event?.preventDefault();
      history2.navigate(to2, options);
    }
    const query = (0, import_url2.getQueryArgs)(to2);
    const path = (0, import_url2.getPath)("http://domain.com/" + to2) ?? "";
    const link = (0, import_element2.useMemo)(() => {
      return beforeNavigate ? beforeNavigate({ path, query }) : { path, query };
    }, [path, query, beforeNavigate]);
    const [before] = window.location.href.split("?");
    return {
      href: `${before}?${(0, import_url2.buildQueryString)({
        [pathArg]: link.path,
        ...link.query
      })}`,
      onClick
    };
  }
  function Link({
    to: to2,
    options,
    children,
    ...props
  }) {
    const { href, onClick } = useLink(to2, options);
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("a", { href, onClick, ...props, children });
  }

  // packages/router/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/router"
  );

  // packages/router/build-module/private-apis.js
  var privateApis = {};
  lock(privateApis, {
    useHistory,
    useLocation,
    RouterProvider,
    useLink,
    Link
  });
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
