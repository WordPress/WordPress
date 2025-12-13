"use strict";
var wp;
(wp ||= {}).priorityQueue = (() => {
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

  // node_modules/requestidlecallback/index.js
  var require_requestidlecallback = __commonJS({
    "node_modules/requestidlecallback/index.js"(exports, module) {
      (function(factory) {
        if (typeof define === "function" && define.amd) {
          define([], factory);
        } else if (typeof module === "object" && module.exports) {
          module.exports = factory();
        } else {
          window.idleCallbackShim = factory();
        }
      })(function() {
        "use strict";
        var scheduleStart, throttleDelay, lazytimer, lazyraf;
        var root = typeof window != "undefined" ? window : typeof global != void 0 ? global : this || {};
        var requestAnimationFrame = root.cancelRequestAnimationFrame && root.requestAnimationFrame || setTimeout;
        var cancelRequestAnimationFrame = root.cancelRequestAnimationFrame || clearTimeout;
        var tasks = [];
        var runAttempts = 0;
        var isRunning = false;
        var remainingTime = 7;
        var minThrottle = 35;
        var throttle = 125;
        var index = 0;
        var taskStart = 0;
        var tasklength = 0;
        var IdleDeadline = {
          get didTimeout() {
            return false;
          },
          timeRemaining: function() {
            var timeRemaining = remainingTime - (Date.now() - taskStart);
            return timeRemaining < 0 ? 0 : timeRemaining;
          }
        };
        var setInactive = debounce(function() {
          remainingTime = 22;
          throttle = 66;
          minThrottle = 0;
        });
        function debounce(fn) {
          var id, timestamp;
          var wait = 99;
          var check = function() {
            var last = Date.now() - timestamp;
            if (last < wait) {
              id = setTimeout(check, wait - last);
            } else {
              id = null;
              fn();
            }
          };
          return function() {
            timestamp = Date.now();
            if (!id) {
              id = setTimeout(check, wait);
            }
          };
        }
        function abortRunning() {
          if (isRunning) {
            if (lazyraf) {
              cancelRequestAnimationFrame(lazyraf);
            }
            if (lazytimer) {
              clearTimeout(lazytimer);
            }
            isRunning = false;
          }
        }
        function onInputorMutation() {
          if (throttle != 125) {
            remainingTime = 7;
            throttle = 125;
            minThrottle = 35;
            if (isRunning) {
              abortRunning();
              scheduleLazy();
            }
          }
          setInactive();
        }
        function scheduleAfterRaf() {
          lazyraf = null;
          lazytimer = setTimeout(runTasks, 0);
        }
        function scheduleRaf() {
          lazytimer = null;
          requestAnimationFrame(scheduleAfterRaf);
        }
        function scheduleLazy() {
          if (isRunning) {
            return;
          }
          throttleDelay = throttle - (Date.now() - taskStart);
          scheduleStart = Date.now();
          isRunning = true;
          if (minThrottle && throttleDelay < minThrottle) {
            throttleDelay = minThrottle;
          }
          if (throttleDelay > 9) {
            lazytimer = setTimeout(scheduleRaf, throttleDelay);
          } else {
            throttleDelay = 0;
            scheduleRaf();
          }
        }
        function runTasks() {
          var task, i, len;
          var timeThreshold = remainingTime > 9 ? 9 : 1;
          taskStart = Date.now();
          isRunning = false;
          lazytimer = null;
          if (runAttempts > 2 || taskStart - throttleDelay - 50 < scheduleStart) {
            for (i = 0, len = tasks.length; i < len && IdleDeadline.timeRemaining() > timeThreshold; i++) {
              task = tasks.shift();
              tasklength++;
              if (task) {
                task(IdleDeadline);
              }
            }
          }
          if (tasks.length) {
            scheduleLazy();
          } else {
            runAttempts = 0;
          }
        }
        function requestIdleCallbackShim(task) {
          index++;
          tasks.push(task);
          scheduleLazy();
          return index;
        }
        function cancelIdleCallbackShim(id) {
          var index2 = id - 1 - tasklength;
          if (tasks[index2]) {
            tasks[index2] = null;
          }
        }
        if (!root.requestIdleCallback || !root.cancelIdleCallback) {
          root.requestIdleCallback = requestIdleCallbackShim;
          root.cancelIdleCallback = cancelIdleCallbackShim;
          if (root.document && document.addEventListener) {
            root.addEventListener("scroll", onInputorMutation, true);
            root.addEventListener("resize", onInputorMutation);
            document.addEventListener("focus", onInputorMutation, true);
            document.addEventListener("mouseover", onInputorMutation, true);
            ["click", "keypress", "touchstart", "mousedown"].forEach(function(name) {
              document.addEventListener(name, onInputorMutation, { capture: true, passive: true });
            });
            if (root.MutationObserver) {
              new MutationObserver(onInputorMutation).observe(document.documentElement, { childList: true, subtree: true, attributes: true });
            }
          }
        } else {
          try {
            root.requestIdleCallback(function() {
            }, { timeout: 0 });
          } catch (e) {
            (function(rIC) {
              var timeRemainingProto, timeRemaining;
              root.requestIdleCallback = function(fn, timeout) {
                if (timeout && typeof timeout.timeout == "number") {
                  return rIC(fn, timeout.timeout);
                }
                return rIC(fn);
              };
              if (root.IdleCallbackDeadline && (timeRemainingProto = IdleCallbackDeadline.prototype)) {
                timeRemaining = Object.getOwnPropertyDescriptor(timeRemainingProto, "timeRemaining");
                if (!timeRemaining || !timeRemaining.configurable || !timeRemaining.get) {
                  return;
                }
                Object.defineProperty(timeRemainingProto, "timeRemaining", {
                  value: function() {
                    return timeRemaining.get.call(this);
                  },
                  enumerable: true,
                  configurable: true
                });
              }
            })(root.requestIdleCallback);
          }
        }
        return {
          request: requestIdleCallbackShim,
          cancel: cancelIdleCallbackShim
        };
      });
    }
  });

  // packages/priority-queue/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    createQueue: () => createQueue
  });

  // packages/priority-queue/build-module/request-idle-callback.js
  var import_requestidlecallback = __toESM(require_requestidlecallback());
  function createRequestIdleCallback() {
    if (typeof window === "undefined") {
      return (callback) => {
        setTimeout(() => callback(Date.now()), 0);
      };
    }
    return window.requestIdleCallback;
  }
  var request_idle_callback_default = createRequestIdleCallback();

  // packages/priority-queue/build-module/index.js
  var createQueue = () => {
    const waitingList = /* @__PURE__ */ new Map();
    let isRunning = false;
    const runWaitingList = (deadline) => {
      for (const [nextElement, callback] of waitingList) {
        waitingList.delete(nextElement);
        callback();
        if ("number" === typeof deadline || deadline.timeRemaining() <= 0) {
          break;
        }
      }
      if (waitingList.size === 0) {
        isRunning = false;
        return;
      }
      request_idle_callback_default(runWaitingList);
    };
    const add = (element, item) => {
      waitingList.set(element, item);
      if (!isRunning) {
        isRunning = true;
        request_idle_callback_default(runWaitingList);
      }
    };
    const flush = (element) => {
      const callback = waitingList.get(element);
      if (void 0 === callback) {
        return false;
      }
      waitingList.delete(element);
      callback();
      return true;
    };
    const cancel = (element) => {
      return waitingList.delete(element);
    };
    const reset = () => {
      waitingList.clear();
      isRunning = false;
    };
    return {
      add,
      flush,
      cancel,
      reset
    };
  };
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
