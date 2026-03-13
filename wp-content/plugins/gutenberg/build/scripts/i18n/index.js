"use strict";
var wp;
(wp ||= {}).i18n = (() => {
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

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // packages/i18n/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    __: () => __,
    _n: () => _n,
    _nx: () => _nx,
    _x: () => _x,
    createI18n: () => createI18n,
    defaultI18n: () => default_i18n_default,
    getLocaleData: () => getLocaleData,
    hasTranslation: () => hasTranslation,
    isRTL: () => isRTL,
    resetLocaleData: () => resetLocaleData,
    setLocaleData: () => setLocaleData,
    sprintf: () => sprintf2,
    subscribe: () => subscribe
  });

  // node_modules/@tannin/sprintf/src/index.js
  var PATTERN = /%(((\d+)\$)|(\(([$_a-zA-Z][$_a-zA-Z0-9]*)\)))?[ +0#-]*\d*(\.(\d+|\*))?(ll|[lhqL])?([cduxXefgsp%])/g;
  function sprintf(string, ...args) {
    var i = 0;
    if (Array.isArray(args[0])) {
      args = /** @type {import('../types').SprintfArgs<T>[]} */
      /** @type {unknown} */
      args[0];
    }
    return string.replace(PATTERN, function() {
      var index, name, precision, type, value;
      index = arguments[3];
      name = arguments[5];
      precision = arguments[7];
      type = arguments[9];
      if (type === "%") {
        return "%";
      }
      if (precision === "*") {
        precision = args[i];
        i++;
      }
      if (name === void 0) {
        if (index === void 0) {
          index = i + 1;
        }
        i++;
        value = args[index - 1];
      } else if (args[0] && typeof args[0] === "object" && args[0].hasOwnProperty(name)) {
        value = args[0][name];
      }
      if (type === "f") {
        value = parseFloat(value) || 0;
      } else if (type === "d") {
        value = parseInt(value) || 0;
      }
      if (precision !== void 0) {
        if (type === "f") {
          value = value.toFixed(precision);
        } else if (type === "s") {
          value = value.substr(0, precision);
        }
      }
      return value !== void 0 && value !== null ? value : "";
    });
  }

  // packages/i18n/build-module/sprintf.js
  function sprintf2(format, ...args) {
    return sprintf(format, ...args);
  }

  // node_modules/@tannin/postfix/index.js
  var PRECEDENCE;
  var OPENERS;
  var TERMINATORS;
  var PATTERN2;
  PRECEDENCE = {
    "(": 9,
    "!": 8,
    "*": 7,
    "/": 7,
    "%": 7,
    "+": 6,
    "-": 6,
    "<": 5,
    "<=": 5,
    ">": 5,
    ">=": 5,
    "==": 4,
    "!=": 4,
    "&&": 3,
    "||": 2,
    "?": 1,
    "?:": 1
  };
  OPENERS = ["(", "?"];
  TERMINATORS = {
    ")": ["("],
    ":": ["?", "?:"]
  };
  PATTERN2 = /<=|>=|==|!=|&&|\|\||\?:|\(|!|\*|\/|%|\+|-|<|>|\?|\)|:/;
  function postfix(expression) {
    var terms = [], stack = [], match, operator, term, element;
    while (match = expression.match(PATTERN2)) {
      operator = match[0];
      term = expression.substr(0, match.index).trim();
      if (term) {
        terms.push(term);
      }
      while (element = stack.pop()) {
        if (TERMINATORS[operator]) {
          if (TERMINATORS[operator][0] === element) {
            operator = TERMINATORS[operator][1] || operator;
            break;
          }
        } else if (OPENERS.indexOf(element) >= 0 || PRECEDENCE[element] < PRECEDENCE[operator]) {
          stack.push(element);
          break;
        }
        terms.push(element);
      }
      if (!TERMINATORS[operator]) {
        stack.push(operator);
      }
      expression = expression.substr(match.index + operator.length);
    }
    expression = expression.trim();
    if (expression) {
      terms.push(expression);
    }
    return terms.concat(stack.reverse());
  }

  // node_modules/@tannin/evaluate/index.js
  var OPERATORS = {
    "!": function(a) {
      return !a;
    },
    "*": function(a, b) {
      return a * b;
    },
    "/": function(a, b) {
      return a / b;
    },
    "%": function(a, b) {
      return a % b;
    },
    "+": function(a, b) {
      return a + b;
    },
    "-": function(a, b) {
      return a - b;
    },
    "<": function(a, b) {
      return a < b;
    },
    "<=": function(a, b) {
      return a <= b;
    },
    ">": function(a, b) {
      return a > b;
    },
    ">=": function(a, b) {
      return a >= b;
    },
    "==": function(a, b) {
      return a === b;
    },
    "!=": function(a, b) {
      return a !== b;
    },
    "&&": function(a, b) {
      return a && b;
    },
    "||": function(a, b) {
      return a || b;
    },
    "?:": function(a, b, c) {
      if (a) {
        throw b;
      }
      return c;
    }
  };
  function evaluate(postfix2, variables) {
    var stack = [], i, j, args, getOperatorResult, term, value;
    for (i = 0; i < postfix2.length; i++) {
      term = postfix2[i];
      getOperatorResult = OPERATORS[term];
      if (getOperatorResult) {
        j = getOperatorResult.length;
        args = Array(j);
        while (j--) {
          args[j] = stack.pop();
        }
        try {
          value = getOperatorResult.apply(null, args);
        } catch (earlyReturn) {
          return earlyReturn;
        }
      } else if (variables.hasOwnProperty(term)) {
        value = variables[term];
      } else {
        value = +term;
      }
      stack.push(value);
    }
    return stack[0];
  }

  // node_modules/@tannin/compile/index.js
  function compile(expression) {
    var terms = postfix(expression);
    return function(variables) {
      return evaluate(terms, variables);
    };
  }

  // node_modules/@tannin/plural-forms/index.js
  function pluralForms(expression) {
    var evaluate2 = compile(expression);
    return function(n) {
      return +evaluate2({ n });
    };
  }

  // node_modules/tannin/index.js
  var DEFAULT_OPTIONS = {
    contextDelimiter: "",
    onMissingKey: null
  };
  function getPluralExpression(pf) {
    var parts, i, part;
    parts = pf.split(";");
    for (i = 0; i < parts.length; i++) {
      part = parts[i].trim();
      if (part.indexOf("plural=") === 0) {
        return part.substr(7);
      }
    }
  }
  function Tannin(data, options) {
    var key;
    this.data = data;
    this.pluralForms = {};
    this.options = {};
    for (key in DEFAULT_OPTIONS) {
      this.options[key] = options !== void 0 && key in options ? options[key] : DEFAULT_OPTIONS[key];
    }
  }
  Tannin.prototype.getPluralForm = function(domain, n) {
    var getPluralForm = this.pluralForms[domain], config, plural, pf;
    if (!getPluralForm) {
      config = this.data[domain][""];
      pf = config["Plural-Forms"] || config["plural-forms"] || // Ignore reason: As known, there's no way to document the empty
      // string property on a key to guarantee this as metadata.
      // @ts-ignore
      config.plural_forms;
      if (typeof pf !== "function") {
        plural = getPluralExpression(
          config["Plural-Forms"] || config["plural-forms"] || // Ignore reason: As known, there's no way to document the empty
          // string property on a key to guarantee this as metadata.
          // @ts-ignore
          config.plural_forms
        );
        pf = pluralForms(plural);
      }
      getPluralForm = this.pluralForms[domain] = pf;
    }
    return getPluralForm(n);
  };
  Tannin.prototype.dcnpgettext = function(domain, context, singular, plural, n) {
    var index, key, entry;
    if (n === void 0) {
      index = 0;
    } else {
      index = this.getPluralForm(domain, n);
    }
    key = singular;
    if (context) {
      key = context + this.options.contextDelimiter + singular;
    }
    entry = this.data[domain][key];
    if (entry && entry[index]) {
      return entry[index];
    }
    if (this.options.onMissingKey) {
      this.options.onMissingKey(singular, domain);
    }
    return index === 0 ? singular : plural;
  };

  // packages/i18n/build-module/create-i18n.js
  var DEFAULT_LOCALE_DATA = {
    "": {
      plural_forms(n) {
        return n === 1 ? 0 : 1;
      }
    }
  };
  var I18N_HOOK_REGEXP = /^i18n\.(n?gettext|has_translation)(_|$)/;
  var createI18n = (initialData, initialDomain, hooks) => {
    const tannin = new Tannin({});
    const listeners = /* @__PURE__ */ new Set();
    const notifyListeners = () => {
      listeners.forEach((listener) => listener());
    };
    const subscribe2 = (callback) => {
      listeners.add(callback);
      return () => listeners.delete(callback);
    };
    const getLocaleData2 = (domain = "default") => tannin.data[domain];
    const doSetLocaleData = (data, domain = "default") => {
      tannin.data[domain] = {
        ...tannin.data[domain],
        ...data
      };
      tannin.data[domain][""] = {
        ...DEFAULT_LOCALE_DATA[""],
        ...tannin.data[domain]?.[""]
      };
      delete tannin.pluralForms[domain];
    };
    const setLocaleData2 = (data, domain) => {
      doSetLocaleData(data, domain);
      notifyListeners();
    };
    const addLocaleData = (data, domain = "default") => {
      tannin.data[domain] = {
        ...tannin.data[domain],
        ...data,
        // Populate default domain configuration (supported locale date which omits
        // a plural forms expression).
        "": {
          ...DEFAULT_LOCALE_DATA[""],
          ...tannin.data[domain]?.[""],
          ...data?.[""]
        }
      };
      delete tannin.pluralForms[domain];
      notifyListeners();
    };
    const resetLocaleData2 = (data, domain) => {
      tannin.data = {};
      tannin.pluralForms = {};
      setLocaleData2(data, domain);
    };
    const dcnpgettext = (domain = "default", context, single, plural, number) => {
      if (!tannin.data[domain]) {
        doSetLocaleData(void 0, domain);
      }
      return tannin.dcnpgettext(domain, context, single, plural, number);
    };
    const getFilterDomain = (domain) => domain || "default";
    const __2 = (text, domain) => {
      let translation = dcnpgettext(domain, void 0, text);
      if (!hooks) {
        return translation;
      }
      translation = hooks.applyFilters(
        "i18n.gettext",
        translation,
        text,
        domain
      );
      return hooks.applyFilters(
        "i18n.gettext_" + getFilterDomain(domain),
        translation,
        text,
        domain
      );
    };
    const _x2 = (text, context, domain) => {
      let translation = dcnpgettext(domain, context, text);
      if (!hooks) {
        return translation;
      }
      translation = hooks.applyFilters(
        "i18n.gettext_with_context",
        translation,
        text,
        context,
        domain
      );
      return hooks.applyFilters(
        "i18n.gettext_with_context_" + getFilterDomain(domain),
        translation,
        text,
        context,
        domain
      );
    };
    const _n2 = (single, plural, number, domain) => {
      let translation = dcnpgettext(
        domain,
        void 0,
        single,
        plural,
        number
      );
      if (!hooks) {
        return translation;
      }
      translation = hooks.applyFilters(
        "i18n.ngettext",
        translation,
        single,
        plural,
        number,
        domain
      );
      return hooks.applyFilters(
        "i18n.ngettext_" + getFilterDomain(domain),
        translation,
        single,
        plural,
        number,
        domain
      );
    };
    const _nx2 = (single, plural, number, context, domain) => {
      let translation = dcnpgettext(
        domain,
        context,
        single,
        plural,
        number
      );
      if (!hooks) {
        return translation;
      }
      translation = hooks.applyFilters(
        "i18n.ngettext_with_context",
        translation,
        single,
        plural,
        number,
        context,
        domain
      );
      return hooks.applyFilters(
        "i18n.ngettext_with_context_" + getFilterDomain(domain),
        translation,
        single,
        plural,
        number,
        context,
        domain
      );
    };
    const isRTL2 = () => {
      return "rtl" === _x2("ltr", "text direction");
    };
    const hasTranslation2 = (single, context, domain) => {
      const key = context ? context + "" + single : single;
      let result = !!tannin.data?.[domain ?? "default"]?.[key];
      if (hooks) {
        result = hooks.applyFilters(
          "i18n.has_translation",
          result,
          single,
          context,
          domain
        );
        result = hooks.applyFilters(
          "i18n.has_translation_" + getFilterDomain(domain),
          result,
          single,
          context,
          domain
        );
      }
      return result;
    };
    if (initialData) {
      setLocaleData2(initialData, initialDomain);
    }
    if (hooks) {
      const onHookAddedOrRemoved = (hookName) => {
        if (I18N_HOOK_REGEXP.test(hookName)) {
          notifyListeners();
        }
      };
      hooks.addAction("hookAdded", "core/i18n", onHookAddedOrRemoved);
      hooks.addAction("hookRemoved", "core/i18n", onHookAddedOrRemoved);
    }
    return {
      getLocaleData: getLocaleData2,
      setLocaleData: setLocaleData2,
      addLocaleData,
      resetLocaleData: resetLocaleData2,
      subscribe: subscribe2,
      __: __2,
      _x: _x2,
      _n: _n2,
      _nx: _nx2,
      isRTL: isRTL2,
      hasTranslation: hasTranslation2
    };
  };

  // packages/i18n/build-module/default-i18n.js
  var import_hooks = __toESM(require_hooks());
  var i18n = createI18n(void 0, void 0, import_hooks.defaultHooks);
  var default_i18n_default = i18n;
  var getLocaleData = i18n.getLocaleData.bind(i18n);
  var setLocaleData = i18n.setLocaleData.bind(i18n);
  var resetLocaleData = i18n.resetLocaleData.bind(i18n);
  var subscribe = i18n.subscribe.bind(i18n);
  var __ = i18n.__.bind(i18n);
  var _x = i18n._x.bind(i18n);
  var _n = i18n._n.bind(i18n);
  var _nx = i18n._nx.bind(i18n);
  var isRTL = i18n.isRTL.bind(i18n);
  var hasTranslation = i18n.hasTranslation.bind(i18n);
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
