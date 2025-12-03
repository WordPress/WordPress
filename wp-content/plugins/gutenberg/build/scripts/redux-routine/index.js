"use strict";
var wp;
(wp ||= {}).reduxRoutine = (() => {
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

  // node_modules/rungen/dist/utils/keys.js
  var require_keys = __commonJS({
    "node_modules/rungen/dist/utils/keys.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      var keys = {
        all: Symbol("all"),
        error: Symbol("error"),
        fork: Symbol("fork"),
        join: Symbol("join"),
        race: Symbol("race"),
        call: Symbol("call"),
        cps: Symbol("cps"),
        subscribe: Symbol("subscribe")
      };
      exports.default = keys;
    }
  });

  // node_modules/rungen/dist/utils/helpers.js
  var require_helpers = __commonJS({
    "node_modules/rungen/dist/utils/helpers.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.createChannel = exports.subscribe = exports.cps = exports.apply = exports.call = exports.invoke = exports.delay = exports.race = exports.join = exports.fork = exports.error = exports.all = void 0;
      var _keys = require_keys();
      var _keys2 = _interopRequireDefault(_keys);
      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : { default: obj };
      }
      var all = exports.all = function all2(value) {
        return {
          type: _keys2.default.all,
          value
        };
      };
      var error = exports.error = function error2(err) {
        return {
          type: _keys2.default.error,
          error: err
        };
      };
      var fork = exports.fork = function fork2(iterator) {
        for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }
        return {
          type: _keys2.default.fork,
          iterator,
          args
        };
      };
      var join = exports.join = function join2(task) {
        return {
          type: _keys2.default.join,
          task
        };
      };
      var race = exports.race = function race2(competitors) {
        return {
          type: _keys2.default.race,
          competitors
        };
      };
      var delay = exports.delay = function delay2(timeout) {
        return new Promise(function(resolve) {
          setTimeout(function() {
            return resolve(true);
          }, timeout);
        });
      };
      var invoke = exports.invoke = function invoke2(func) {
        for (var _len2 = arguments.length, args = Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
          args[_key2 - 1] = arguments[_key2];
        }
        return {
          type: _keys2.default.call,
          func,
          context: null,
          args
        };
      };
      var call = exports.call = function call2(func, context) {
        for (var _len3 = arguments.length, args = Array(_len3 > 2 ? _len3 - 2 : 0), _key3 = 2; _key3 < _len3; _key3++) {
          args[_key3 - 2] = arguments[_key3];
        }
        return {
          type: _keys2.default.call,
          func,
          context,
          args
        };
      };
      var apply = exports.apply = function apply2(func, context, args) {
        return {
          type: _keys2.default.call,
          func,
          context,
          args
        };
      };
      var cps = exports.cps = function cps2(func) {
        for (var _len4 = arguments.length, args = Array(_len4 > 1 ? _len4 - 1 : 0), _key4 = 1; _key4 < _len4; _key4++) {
          args[_key4 - 1] = arguments[_key4];
        }
        return {
          type: _keys2.default.cps,
          func,
          args
        };
      };
      var subscribe = exports.subscribe = function subscribe2(channel) {
        return {
          type: _keys2.default.subscribe,
          channel
        };
      };
      var createChannel = exports.createChannel = function createChannel2(callback) {
        var listeners = [];
        var subscribe2 = function subscribe3(l) {
          listeners.push(l);
          return function() {
            return listeners.splice(listeners.indexOf(l), 1);
          };
        };
        var next = function next2(val) {
          return listeners.forEach(function(l) {
            return l(val);
          });
        };
        callback(next);
        return {
          subscribe: subscribe2
        };
      };
    }
  });

  // node_modules/rungen/dist/utils/is.js
  var require_is = __commonJS({
    "node_modules/rungen/dist/utils/is.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function(obj) {
        return typeof obj;
      } : function(obj) {
        return obj && typeof Symbol === "function" && obj.constructor === Symbol ? "symbol" : typeof obj;
      };
      var _keys = require_keys();
      var _keys2 = _interopRequireDefault(_keys);
      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : { default: obj };
      }
      var is = {
        obj: function obj(value) {
          return (typeof value === "undefined" ? "undefined" : _typeof(value)) === "object" && !!value;
        },
        all: function all(value) {
          return is.obj(value) && value.type === _keys2.default.all;
        },
        error: function error(value) {
          return is.obj(value) && value.type === _keys2.default.error;
        },
        array: Array.isArray,
        func: function func(value) {
          return typeof value === "function";
        },
        promise: function promise(value) {
          return value && is.func(value.then);
        },
        iterator: function iterator(value) {
          return value && is.func(value.next) && is.func(value.throw);
        },
        fork: function fork(value) {
          return is.obj(value) && value.type === _keys2.default.fork;
        },
        join: function join(value) {
          return is.obj(value) && value.type === _keys2.default.join;
        },
        race: function race(value) {
          return is.obj(value) && value.type === _keys2.default.race;
        },
        call: function call(value) {
          return is.obj(value) && value.type === _keys2.default.call;
        },
        cps: function cps(value) {
          return is.obj(value) && value.type === _keys2.default.cps;
        },
        subscribe: function subscribe(value) {
          return is.obj(value) && value.type === _keys2.default.subscribe;
        },
        channel: function channel(value) {
          return is.obj(value) && is.func(value.subscribe);
        }
      };
      exports.default = is;
    }
  });

  // node_modules/rungen/dist/controls/builtin.js
  var require_builtin = __commonJS({
    "node_modules/rungen/dist/controls/builtin.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.iterator = exports.array = exports.object = exports.error = exports.any = void 0;
      var _is = require_is();
      var _is2 = _interopRequireDefault(_is);
      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : { default: obj };
      }
      var any = exports.any = function any2(value, next, rungen, yieldNext) {
        yieldNext(value);
        return true;
      };
      var error = exports.error = function error2(value, next, rungen, yieldNext, raiseNext) {
        if (!_is2.default.error(value)) return false;
        raiseNext(value.error);
        return true;
      };
      var object = exports.object = function object2(value, next, rungen, yieldNext, raiseNext) {
        if (!_is2.default.all(value) || !_is2.default.obj(value.value)) return false;
        var result = {};
        var keys = Object.keys(value.value);
        var count = 0;
        var hasError = false;
        var gotResultSuccess = function gotResultSuccess2(key, ret) {
          if (hasError) return;
          result[key] = ret;
          count++;
          if (count === keys.length) {
            yieldNext(result);
          }
        };
        var gotResultError = function gotResultError2(key, error2) {
          if (hasError) return;
          hasError = true;
          raiseNext(error2);
        };
        keys.map(function(key) {
          rungen(value.value[key], function(ret) {
            return gotResultSuccess(key, ret);
          }, function(err) {
            return gotResultError(key, err);
          });
        });
        return true;
      };
      var array = exports.array = function array2(value, next, rungen, yieldNext, raiseNext) {
        if (!_is2.default.all(value) || !_is2.default.array(value.value)) return false;
        var result = [];
        var count = 0;
        var hasError = false;
        var gotResultSuccess = function gotResultSuccess2(key, ret) {
          if (hasError) return;
          result[key] = ret;
          count++;
          if (count === value.value.length) {
            yieldNext(result);
          }
        };
        var gotResultError = function gotResultError2(key, error2) {
          if (hasError) return;
          hasError = true;
          raiseNext(error2);
        };
        value.value.map(function(v, key) {
          rungen(v, function(ret) {
            return gotResultSuccess(key, ret);
          }, function(err) {
            return gotResultError(key, err);
          });
        });
        return true;
      };
      var iterator = exports.iterator = function iterator2(value, next, rungen, yieldNext, raiseNext) {
        if (!_is2.default.iterator(value)) return false;
        rungen(value, next, raiseNext);
        return true;
      };
      exports.default = [error, iterator, array, object, any];
    }
  });

  // node_modules/rungen/dist/create.js
  var require_create = __commonJS({
    "node_modules/rungen/dist/create.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      var _builtin = require_builtin();
      var _builtin2 = _interopRequireDefault(_builtin);
      var _is = require_is();
      var _is2 = _interopRequireDefault(_is);
      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : { default: obj };
      }
      function _toConsumableArray(arr) {
        if (Array.isArray(arr)) {
          for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) {
            arr2[i] = arr[i];
          }
          return arr2;
        } else {
          return Array.from(arr);
        }
      }
      var create2 = function create3() {
        var userControls = arguments.length <= 0 || arguments[0] === void 0 ? [] : arguments[0];
        var controls = [].concat(_toConsumableArray(userControls), _toConsumableArray(_builtin2.default));
        var runtime = function runtime2(input) {
          var success = arguments.length <= 1 || arguments[1] === void 0 ? function() {
          } : arguments[1];
          var error = arguments.length <= 2 || arguments[2] === void 0 ? function() {
          } : arguments[2];
          var iterate = function iterate2(gen) {
            var yieldValue = function yieldValue2(isError) {
              return function(ret) {
                try {
                  var _ref = isError ? gen.throw(ret) : gen.next(ret);
                  var value = _ref.value;
                  var done = _ref.done;
                  if (done) return success(value);
                  next(value);
                } catch (e) {
                  return error(e);
                }
              };
            };
            var next = function next2(ret) {
              controls.some(function(control) {
                return control(ret, next2, runtime2, yieldValue(false), yieldValue(true));
              });
            };
            yieldValue(false)();
          };
          var iterator = _is2.default.iterator(input) ? input : regeneratorRuntime.mark(function _callee() {
            return regeneratorRuntime.wrap(function _callee$(_context) {
              while (1) {
                switch (_context.prev = _context.next) {
                  case 0:
                    _context.next = 2;
                    return input;
                  case 2:
                    return _context.abrupt("return", _context.sent);
                  case 3:
                  case "end":
                    return _context.stop();
                }
              }
            }, _callee, this);
          })();
          iterate(iterator, success, error);
        };
        return runtime;
      };
      exports.default = create2;
    }
  });

  // node_modules/rungen/dist/utils/dispatcher.js
  var require_dispatcher = __commonJS({
    "node_modules/rungen/dist/utils/dispatcher.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      var createDispatcher = function createDispatcher2() {
        var listeners = [];
        return {
          subscribe: function subscribe(listener) {
            listeners.push(listener);
            return function() {
              listeners = listeners.filter(function(l) {
                return l !== listener;
              });
            };
          },
          dispatch: function dispatch(action) {
            listeners.slice().forEach(function(listener) {
              return listener(action);
            });
          }
        };
      };
      exports.default = createDispatcher;
    }
  });

  // node_modules/rungen/dist/controls/async.js
  var require_async = __commonJS({
    "node_modules/rungen/dist/controls/async.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.race = exports.join = exports.fork = exports.promise = void 0;
      var _is = require_is();
      var _is2 = _interopRequireDefault(_is);
      var _helpers = require_helpers();
      var _dispatcher = require_dispatcher();
      var _dispatcher2 = _interopRequireDefault(_dispatcher);
      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : { default: obj };
      }
      var promise = exports.promise = function promise2(value, next, rungen, yieldNext, raiseNext) {
        if (!_is2.default.promise(value)) return false;
        value.then(next, raiseNext);
        return true;
      };
      var forkedTasks = /* @__PURE__ */ new Map();
      var fork = exports.fork = function fork2(value, next, rungen) {
        if (!_is2.default.fork(value)) return false;
        var task = Symbol("fork");
        var dispatcher = (0, _dispatcher2.default)();
        forkedTasks.set(task, dispatcher);
        rungen(value.iterator.apply(null, value.args), function(result) {
          return dispatcher.dispatch(result);
        }, function(err) {
          return dispatcher.dispatch((0, _helpers.error)(err));
        });
        var unsubscribe = dispatcher.subscribe(function() {
          unsubscribe();
          forkedTasks.delete(task);
        });
        next(task);
        return true;
      };
      var join = exports.join = function join2(value, next, rungen, yieldNext, raiseNext) {
        if (!_is2.default.join(value)) return false;
        var dispatcher = forkedTasks.get(value.task);
        if (!dispatcher) {
          raiseNext("join error : task not found");
        } else {
          (function() {
            var unsubscribe = dispatcher.subscribe(function(result) {
              unsubscribe();
              next(result);
            });
          })();
        }
        return true;
      };
      var race = exports.race = function race2(value, next, rungen, yieldNext, raiseNext) {
        if (!_is2.default.race(value)) return false;
        var finished = false;
        var success = function success2(result, k, v) {
          if (finished) return;
          finished = true;
          result[k] = v;
          next(result);
        };
        var fail = function fail2(err) {
          if (finished) return;
          raiseNext(err);
        };
        if (_is2.default.array(value.competitors)) {
          (function() {
            var result = value.competitors.map(function() {
              return false;
            });
            value.competitors.forEach(function(competitor, index) {
              rungen(competitor, function(output) {
                return success(result, index, output);
              }, fail);
            });
          })();
        } else {
          (function() {
            var result = Object.keys(value.competitors).reduce(function(p, c) {
              p[c] = false;
              return p;
            }, {});
            Object.keys(value.competitors).forEach(function(index) {
              rungen(value.competitors[index], function(output) {
                return success(result, index, output);
              }, fail);
            });
          })();
        }
        return true;
      };
      var subscribe = function subscribe2(value, next) {
        if (!_is2.default.subscribe(value)) return false;
        if (!_is2.default.channel(value.channel)) {
          throw new Error('the first argument of "subscribe" must be a valid channel');
        }
        var unsubscribe = value.channel.subscribe(function(ret) {
          unsubscribe && unsubscribe();
          next(ret);
        });
        return true;
      };
      exports.default = [promise, fork, join, race, subscribe];
    }
  });

  // node_modules/rungen/dist/controls/wrap.js
  var require_wrap = __commonJS({
    "node_modules/rungen/dist/controls/wrap.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.cps = exports.call = void 0;
      var _is = require_is();
      var _is2 = _interopRequireDefault(_is);
      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : { default: obj };
      }
      function _toConsumableArray(arr) {
        if (Array.isArray(arr)) {
          for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) {
            arr2[i] = arr[i];
          }
          return arr2;
        } else {
          return Array.from(arr);
        }
      }
      var call = exports.call = function call2(value, next, rungen, yieldNext, raiseNext) {
        if (!_is2.default.call(value)) return false;
        try {
          next(value.func.apply(value.context, value.args));
        } catch (err) {
          raiseNext(err);
        }
        return true;
      };
      var cps = exports.cps = function cps2(value, next, rungen, yieldNext, raiseNext) {
        var _value$func;
        if (!_is2.default.cps(value)) return false;
        (_value$func = value.func).call.apply(_value$func, [null].concat(_toConsumableArray(value.args), [function(err, result) {
          if (err) raiseNext(err);
          else next(result);
        }]));
        return true;
      };
      exports.default = [call, cps];
    }
  });

  // node_modules/rungen/dist/index.js
  var require_dist = __commonJS({
    "node_modules/rungen/dist/index.js"(exports) {
      "use strict";
      Object.defineProperty(exports, "__esModule", {
        value: true
      });
      exports.wrapControls = exports.asyncControls = exports.create = void 0;
      var _helpers = require_helpers();
      Object.keys(_helpers).forEach(function(key) {
        if (key === "default") return;
        Object.defineProperty(exports, key, {
          enumerable: true,
          get: function get() {
            return _helpers[key];
          }
        });
      });
      var _create = require_create();
      var _create2 = _interopRequireDefault(_create);
      var _async = require_async();
      var _async2 = _interopRequireDefault(_async);
      var _wrap = require_wrap();
      var _wrap2 = _interopRequireDefault(_wrap);
      function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : { default: obj };
      }
      exports.create = _create2.default;
      exports.asyncControls = _async2.default;
      exports.wrapControls = _wrap2.default;
    }
  });

  // packages/redux-routine/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    default: () => createMiddleware
  });

  // packages/redux-routine/build-module/is-generator.js
  function isGenerator(object) {
    return !!object && typeof object[Symbol.iterator] === "function" && typeof object.next === "function";
  }

  // packages/redux-routine/build-module/runtime.js
  var import_rungen = __toESM(require_dist());

  // node_modules/is-promise/index.mjs
  function isPromise(obj) {
    return !!obj && (typeof obj === "object" || typeof obj === "function") && typeof obj.then === "function";
  }

  // node_modules/is-plain-object/dist/is-plain-object.mjs
  function isObject(o) {
    return Object.prototype.toString.call(o) === "[object Object]";
  }
  function isPlainObject(o) {
    var ctor, prot;
    if (isObject(o) === false) return false;
    ctor = o.constructor;
    if (ctor === void 0) return true;
    prot = ctor.prototype;
    if (isObject(prot) === false) return false;
    if (prot.hasOwnProperty("isPrototypeOf") === false) {
      return false;
    }
    return true;
  }

  // packages/redux-routine/build-module/is-action.js
  function isAction(object) {
    return isPlainObject(object) && typeof object.type === "string";
  }
  function isActionOfType(object, expectedType) {
    return isAction(object) && object.type === expectedType;
  }

  // packages/redux-routine/build-module/runtime.js
  function createRuntime(controls = {}, dispatch) {
    const rungenControls = Object.entries(controls).map(
      ([actionType, control]) => (value, next, iterate, yieldNext, yieldError) => {
        if (!isActionOfType(value, actionType)) {
          return false;
        }
        const routine = control(value);
        if (isPromise(routine)) {
          routine.then(yieldNext, yieldError);
        } else {
          yieldNext(routine);
        }
        return true;
      }
    );
    const unhandledActionControl = (value, next) => {
      if (!isAction(value)) {
        return false;
      }
      dispatch(value);
      next();
      return true;
    };
    rungenControls.push(unhandledActionControl);
    const rungenRuntime = (0, import_rungen.create)(rungenControls);
    return (action) => new Promise(
      (resolve, reject) => rungenRuntime(
        action,
        (result) => {
          if (isAction(result)) {
            dispatch(result);
          }
          resolve(result);
        },
        reject
      )
    );
  }

  // packages/redux-routine/build-module/index.js
  function createMiddleware(controls = {}) {
    return (store) => {
      const runtime = createRuntime(controls, store.dispatch);
      return (next) => (action) => {
        if (!isGenerator(action)) {
          return next(action);
        }
        return runtime(action);
      };
    };
  }
  return __toCommonJS(index_exports);
})();
/*! Bundled license information:

is-plain-object/dist/is-plain-object.mjs:
  (*!
   * is-plain-object <https://github.com/jonschlinkert/is-plain-object>
   *
   * Copyright (c) 2014-2017, Jon Schlinkert.
   * Released under the MIT License.
   *)
*/
if (typeof wp.reduxRoutine === 'object' && wp.reduxRoutine.default) { wp.reduxRoutine = wp.reduxRoutine.default; }
//# sourceMappingURL=index.js.map
