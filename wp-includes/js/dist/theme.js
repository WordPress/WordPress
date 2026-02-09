var wp;
(wp ||= {}).theme = (() => {
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

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/theme/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    privateApis: () => privateApis
  });

  // packages/theme/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/theme"
  );

  // packages/theme/build-module/theme-provider.mjs
  var import_element3 = __toESM(require_element(), 1);

  // packages/theme/build-module/context.mjs
  var import_element = __toESM(require_element(), 1);
  var ThemeContext = (0, import_element.createContext)({
    resolvedSettings: {
      color: {}
    }
  });

  // packages/theme/node_modules/colorjs.io/src/multiply-matrices.js
  function dot3(a, b2) {
    return a[0] * b2[0] + a[1] * b2[1] + a[2] * b2[2];
  }
  function multiply_v3_m3x3(input, matrix, out = [0, 0, 0]) {
    const x = dot3(input, matrix[0]);
    const y = dot3(input, matrix[1]);
    const z = dot3(input, matrix[2]);
    out[0] = x;
    out[1] = y;
    out[2] = z;
    return out;
  }

  // packages/theme/node_modules/colorjs.io/src/util.js
  function isString(str) {
    return type(str) === "string";
  }
  function type(o) {
    let str = Object.prototype.toString.call(o);
    return (str.match(/^\[object\s+(.*?)\]$/)[1] || "").toLowerCase();
  }
  function serializeNumber(n2, { precision = 16, unit }) {
    if (isNone(n2)) {
      return "none";
    }
    n2 = +toPrecision(n2, precision);
    return n2 + (unit ?? "");
  }
  function isNone(n2) {
    return n2 === null;
  }
  function toPrecision(n2, precision) {
    if (n2 === 0) {
      return 0;
    }
    let integer = ~~n2;
    let digits = 0;
    if (integer && precision) {
      digits = ~~Math.log10(Math.abs(integer)) + 1;
    }
    const multiplier = 10 ** (precision - digits);
    return Math.floor(n2 * multiplier + 0.5) / multiplier;
  }
  function interpolate(start, end, p2) {
    if (isNaN(start)) {
      return end;
    }
    if (isNaN(end)) {
      return start;
    }
    return start + (end - start) * p2;
  }
  function interpolateInv(start, end, value) {
    return (value - start) / (end - start);
  }
  function mapRange(from, to2, value) {
    if (!from || !to2 || from === to2 || from[0] === to2[0] && from[1] === to2[1] || isNaN(value) || value === null) {
      return value;
    }
    return interpolate(to2[0], to2[1], interpolateInv(from[0], from[1], value));
  }
  function clamp(min, val, max) {
    return Math.max(Math.min(max, val), min);
  }
  function copySign(to2, from) {
    return Math.sign(to2) === Math.sign(from) ? to2 : -to2;
  }
  function spow(base, exp) {
    return copySign(Math.abs(base) ** exp, base);
  }
  function zdiv(n2, d2) {
    return d2 === 0 ? 0 : n2 / d2;
  }
  function bisectLeft(arr, value, lo = 0, hi = arr.length) {
    while (lo < hi) {
      const mid = lo + hi >> 1;
      if (arr[mid] < value) {
        lo = mid + 1;
      } else {
        hi = mid;
      }
    }
    return lo;
  }
  function isInstance(arg, constructor) {
    if (arg instanceof constructor) {
      return true;
    }
    const targetName = constructor.name;
    while (arg) {
      const proto = Object.getPrototypeOf(arg);
      const constructorName = proto?.constructor?.name;
      if (constructorName === targetName) {
        return true;
      }
      if (!constructorName || constructorName === "Object") {
        return false;
      }
      arg = proto;
    }
    return false;
  }

  // packages/theme/node_modules/colorjs.io/src/Type.js
  var Type = class {
    // Class properties - declared here so that type inference works
    type;
    coordMeta;
    coordRange;
    /** @type {[number, number]} */
    range;
    /**
     * @param {any} type
     * @param {import("./types.js").CoordMeta} coordMeta
     */
    constructor(type2, coordMeta) {
      if (typeof type2 === "object") {
        this.coordMeta = type2;
      }
      if (coordMeta) {
        this.coordMeta = coordMeta;
        this.coordRange = coordMeta.range ?? coordMeta.refRange;
      }
      if (typeof type2 === "string") {
        let params = type2.trim().match(/^(?<type><[a-z]+>)(\[(?<min>-?[.\d]+),\s*(?<max>-?[.\d]+)\])?$/);
        if (!params) {
          throw new TypeError(`Cannot parse ${type2} as a type definition.`);
        }
        this.type = params.groups.type;
        let { min, max } = params.groups;
        if (min || max) {
          this.range = [+min, +max];
        }
      }
    }
    /** @returns {[number, number]} */
    get computedRange() {
      if (this.range) {
        return this.range;
      }
      if (this.type === "<percentage>") {
        return this.percentageRange();
      } else if (this.type === "<angle>") {
        return [0, 360];
      }
      return null;
    }
    get unit() {
      if (this.type === "<percentage>") {
        return "%";
      } else if (this.type === "<angle>") {
        return "deg";
      }
      return "";
    }
    /**
     * Map a number to the internal representation
     * @param {number} number
     */
    resolve(number) {
      if (this.type === "<angle>") {
        return number;
      }
      let fromRange = this.computedRange;
      let toRange = this.coordRange;
      if (this.type === "<percentage>") {
        toRange ??= this.percentageRange();
      }
      return mapRange(fromRange, toRange, number);
    }
    /**
     * Serialize a number from the internal representation to a string
     * @param {number} number
     * @param {number} [precision]
     */
    serialize(number, precision) {
      let toRange = this.type === "<percentage>" ? this.percentageRange(100) : this.computedRange;
      let unit = this.unit;
      number = mapRange(this.coordRange, toRange, number);
      return serializeNumber(number, { unit, precision });
    }
    toString() {
      let ret = this.type;
      if (this.range) {
        let [min = "", max = ""] = this.range;
        ret += `[${min},${max}]`;
      }
      return ret;
    }
    /**
     * Returns a percentage range for values of this type
     * @param {number} scale
     * @returns {[number, number]}
     */
    percentageRange(scale = 1) {
      let range;
      if (this.coordMeta && this.coordMeta.range || this.coordRange && this.coordRange[0] >= 0) {
        range = [0, 1];
      } else {
        range = [-1, 1];
      }
      return [range[0] * scale, range[1] * scale];
    }
    static get(type2, coordMeta) {
      if (isInstance(type2, this)) {
        return type2;
      }
      return new this(type2, coordMeta);
    }
  };

  // packages/theme/node_modules/colorjs.io/src/Format.js
  var instance = /* @__PURE__ */ Symbol("instance");
  var Format = class _Format {
    // Class properties - declared here so that type inference works
    type;
    name;
    spaceCoords;
    /** @type {Type[][]} */
    coords;
    /** @type {string | undefined} */
    id;
    /** @type {boolean | undefined} */
    alpha;
    /**
     * @param {FormatInterface} format
     * @param {ColorSpace} space
     */
    constructor(format, space = format.space) {
      format[instance] = this;
      this.type = "function";
      this.name = "color";
      Object.assign(this, format);
      this.space = space;
      if (this.type === "custom") {
        return;
      }
      this.spaceCoords = Object.values(space.coords);
      if (!this.coords) {
        this.coords = this.spaceCoords.map((coordMeta) => {
          let ret = ["<number>", "<percentage>"];
          if (coordMeta.type === "angle") {
            ret.push("<angle>");
          }
          return ret;
        });
      }
      this.coords = this.coords.map(
        /** @param {string | string[] | Type[]} types */
        (types, i) => {
          let coordMeta = this.spaceCoords[i];
          if (typeof types === "string") {
            types = types.trim().split(/\s*\|\s*/);
          }
          return types.map((type2) => Type.get(type2, coordMeta));
        }
      );
    }
    /**
     * @param {Coords} coords
     * @param {number} precision
     * @param {Type[]} types
     */
    serializeCoords(coords, precision, types) {
      types = coords.map((_, i) => Type.get(types?.[i] ?? this.coords[i][0], this.spaceCoords[i]));
      return coords.map((c, i) => types[i].serialize(c, precision));
    }
    /**
     * Validates the coordinates of a color against a format's coord grammar and
     * maps the coordinates to the range or refRange of the coordinates.
     * @param {Coords} coords
     * @param {[string, string, string]} types
     */
    coerceCoords(coords, types) {
      return Object.entries(this.space.coords).map(([id, coordMeta], i) => {
        let arg = coords[i];
        if (isNone(arg) || isNaN(arg)) {
          return arg;
        }
        let providedType = types[i];
        let type2 = this.coords[i].find((c) => c.type == providedType);
        if (!type2) {
          let coordName = coordMeta.name || id;
          throw new TypeError(
            `${providedType ?? /** @type {any} */
            arg?.raw ?? arg} not allowed for ${coordName} in ${this.name}()`
          );
        }
        arg = type2.resolve(arg);
        if (type2.range) {
          types[i] = type2.toString();
        }
        return arg;
      });
    }
    /**
     * @returns {boolean | Required<FormatInterface>["serialize"]}
     */
    canSerialize() {
      return this.type === "function" || /** @type {any} */
      this.serialize;
    }
    /**
     * @param {string} str
     * @returns {(import("./types.js").ColorConstructor) | undefined | null}
     */
    parse(str) {
      return null;
    }
    /**
     * @param {Format | FormatInterface} format
     * @param {RemoveFirstElement<ConstructorParameters<typeof Format>>} args
     * @returns {Format}
     */
    static get(format, ...args) {
      if (!format || isInstance(format, this)) {
        return (
          /** @type {Format} */
          format
        );
      }
      if (format[instance]) {
        return format[instance];
      }
      return new _Format(format, ...args);
    }
  };

  // packages/theme/node_modules/colorjs.io/src/hooks.js
  var Hooks = class {
    add(name, callback, first) {
      if (typeof arguments[0] != "string") {
        for (var name in arguments[0]) {
          this.add(name, arguments[0][name], arguments[1]);
        }
        return;
      }
      (Array.isArray(name) ? name : [name]).forEach(function(name2) {
        this[name2] = this[name2] || [];
        if (callback) {
          this[name2][first ? "unshift" : "push"](callback);
        }
      }, this);
    }
    run(name, env) {
      this[name] = this[name] || [];
      this[name].forEach(function(callback) {
        callback.call(env && env.context ? env.context : env, env);
      });
    }
  };
  var hooks = new Hooks();
  var hooks_default = hooks;

  // packages/theme/node_modules/colorjs.io/src/adapt.js
  var WHITES = {
    // for compatibility, the four-digit chromaticity-derived ones everyone else uses
    D50: [0.3457 / 0.3585, 1, (1 - 0.3457 - 0.3585) / 0.3585],
    D65: [0.3127 / 0.329, 1, (1 - 0.3127 - 0.329) / 0.329]
  };
  function getWhite(name) {
    if (Array.isArray(name)) {
      return name;
    }
    return WHITES[name];
  }
  function adapt(W1, W2, XYZ, options = {}) {
    W1 = getWhite(W1);
    W2 = getWhite(W2);
    if (!W1 || !W2) {
      throw new TypeError(
        `Missing white point to convert ${!W1 ? "from" : ""}${!W1 && !W2 ? "/" : ""}${!W2 ? "to" : ""}`
      );
    }
    if (W1 === W2) {
      return XYZ;
    }
    let env = { W1, W2, XYZ, options };
    hooks_default.run("chromatic-adaptation-start", env);
    if (!env.M) {
      if (env.W1 === WHITES.D65 && env.W2 === WHITES.D50) {
        env.M = [
          [1.0479297925449969, 0.022946870601609652, -0.05019226628920524],
          [0.02962780877005599, 0.9904344267538799, -0.017073799063418826],
          [-0.009243040646204504, 0.015055191490298152, 0.7518742814281371]
        ];
      } else if (env.W1 === WHITES.D50 && env.W2 === WHITES.D65) {
        env.M = [
          [0.955473421488075, -0.02309845494876471, 0.06325924320057072],
          [-0.0283697093338637, 1.0099953980813041, 0.021041441191917323],
          [0.012314014864481998, -0.020507649298898964, 1.330365926242124]
        ];
      }
    }
    hooks_default.run("chromatic-adaptation-end", env);
    if (env.M) {
      return multiply_v3_m3x3(env.XYZ, env.M);
    } else {
      throw new TypeError("Only Bradford CAT with white points D50 and D65 supported for now.");
    }
  }

  // packages/theme/node_modules/colorjs.io/src/defaults.js
  var defaults_default = {
    gamut_mapping: "css",
    precision: 5,
    deltaE: "76",
    // Default deltaE method
    verbose: globalThis?.process?.env?.NODE_ENV?.toLowerCase() !== "test",
    warn: function warn(msg) {
      if (this.verbose) {
        globalThis?.console?.warn?.(msg);
      }
    }
  };

  // packages/theme/node_modules/colorjs.io/src/parse.js
  function parse(str, options) {
    let env = {
      str: String(str)?.trim(),
      options
    };
    hooks_default.run("parse-start", env);
    if (env.color) {
      return env.color;
    }
    env.parsed = parseFunction(env.str);
    let ret;
    let meta = env.options ? env.options.parseMeta ?? env.options.meta : null;
    if (env.parsed) {
      let name = env.parsed.name;
      let format;
      let space;
      let coords = env.parsed.args;
      let types = coords.map((c, i) => env.parsed.argMeta[i]?.type);
      if (name === "color") {
        let id = coords.shift();
        types.shift();
        let alternateId = id.startsWith("--") ? id.substring(2) : `--${id}`;
        let ids = [id, alternateId];
        format = ColorSpace.findFormat({ name, id: ids, type: "function" });
        if (!format) {
          let didYouMean;
          let registryId = id in ColorSpace.registry ? id : alternateId;
          if (registryId in ColorSpace.registry) {
            let cssId = ColorSpace.registry[registryId].formats?.color?.id;
            if (cssId) {
              let altColor = str.replace("color(" + id, "color(" + cssId);
              didYouMean = `Did you mean ${altColor}?`;
            }
          }
          throw new TypeError(
            `Cannot parse ${env.str}. ` + (didYouMean ?? "Missing a plugin?")
          );
        }
        space = format.space;
        if (format.id.startsWith("--") && !id.startsWith("--")) {
          defaults_default.warn(
            `${space.name} is a non-standard space and not currently supported in the CSS spec. Use prefixed color(${format.id}) instead of color(${id}).`
          );
        }
        if (id.startsWith("--") && !format.id.startsWith("--")) {
          defaults_default.warn(
            `${space.name} is a standard space and supported in the CSS spec. Use color(${format.id}) instead of prefixed color(${id}).`
          );
        }
      } else {
        format = ColorSpace.findFormat({ name, type: "function" });
        space = format.space;
      }
      if (meta) {
        Object.assign(meta, {
          format,
          formatId: format.name,
          types,
          commas: env.parsed.commas
        });
      }
      let alpha = 1;
      if (env.parsed.lastAlpha) {
        alpha = env.parsed.args.pop();
        if (meta) {
          meta.alphaType = types.pop();
        }
      }
      let coordCount = format.coords.length;
      if (coords.length !== coordCount) {
        throw new TypeError(
          `Expected ${coordCount} coordinates for ${space.id} in ${env.str}), got ${coords.length}`
        );
      }
      coords = format.coerceCoords(coords, types);
      ret = { spaceId: space.id, coords, alpha };
    } else {
      spaceloop: for (let space of ColorSpace.all) {
        for (let formatId in space.formats) {
          let format = space.formats[formatId];
          if (format.type !== "custom") {
            continue;
          }
          if (format.test && !format.test(env.str)) {
            continue;
          }
          let formatObject = space.getFormat(format);
          let color = formatObject.parse(env.str);
          if (color) {
            if (meta) {
              Object.assign(meta, { format: formatObject, formatId });
            }
            ret = color;
            break spaceloop;
          }
        }
      }
    }
    if (!ret) {
      throw new TypeError(`Could not parse ${str} as a color. Missing a plugin?`);
    }
    ret.alpha = isNone(ret.alpha) ? ret.alpha : ret.alpha === void 0 ? 1 : clamp(0, ret.alpha, 1);
    return ret;
  }
  var units = {
    "%": 0.01,
    deg: 1,
    grad: 0.9,
    rad: 180 / Math.PI,
    turn: 360
  };
  var regex = {
    // Need to list calc(NaN) explicitly as otherwise its ending paren would terminate the function call
    function: /^([a-z]+)\(((?:calc\(NaN\)|.)+?)\)$/i,
    number: /^([-+]?(?:[0-9]*\.)?[0-9]+(e[-+]?[0-9]+)?)$/i,
    unitValue: RegExp(`(${Object.keys(units).join("|")})$`),
    // NOTE The -+ are not just for prefix, but also for idents, and e+N notation!
    singleArgument: /\/?\s*(none|NaN|calc\(NaN\)|[-+\w.]+(?:%|deg|g?rad|turn)?)/g
  };
  function parseArgument(rawArg) {
    let meta = {};
    let unit = rawArg.match(regex.unitValue)?.[0];
    let value = meta.raw = rawArg;
    if (unit) {
      meta.type = unit === "%" ? "<percentage>" : "<angle>";
      meta.unit = unit;
      meta.unitless = Number(value.slice(0, -unit.length));
      value = meta.unitless * units[unit];
    } else if (regex.number.test(value)) {
      value = Number(value);
      meta.type = "<number>";
    } else if (value === "none") {
      value = null;
    } else if (value === "NaN" || value === "calc(NaN)") {
      value = NaN;
      meta.type = "<number>";
    } else {
      meta.type = "<ident>";
    }
    return { value: (
      /** @type {number} */
      value
    ), meta: (
      /** @type {ArgumentMeta} */
      meta
    ) };
  }
  function parseFunction(str) {
    if (!str) {
      return;
    }
    str = str.trim();
    let parts = str.match(regex.function);
    if (parts) {
      let args = [];
      let argMeta = [];
      let lastAlpha = false;
      let name = parts[1].toLowerCase();
      let separators = parts[2].replace(regex.singleArgument, ($0, rawArg) => {
        let { value, meta } = parseArgument(rawArg);
        if (
          // If there's a slash here, it's modern syntax
          $0.startsWith("/") || // If there's still elements to process after there's already 3 in `args` (and the we're not dealing with "color()"), it's likely to be a legacy color like "hsl(0, 0%, 0%, 0.5)"
          name !== "color" && args.length === 3
        ) {
          lastAlpha = true;
        }
        args.push(value);
        argMeta.push(meta);
        return "";
      });
      return {
        name,
        args,
        argMeta,
        lastAlpha,
        commas: separators.includes(","),
        rawName: parts[1],
        rawArgs: parts[2]
      };
    }
  }

  // packages/theme/node_modules/colorjs.io/src/getColor.js
  function getColor(color, options) {
    if (Array.isArray(color)) {
      return color.map((c) => getColor(c, options));
    }
    if (!color) {
      throw new TypeError("Empty color reference");
    }
    if (isString(color)) {
      color = parse(color, options);
    }
    let space = color.space || color.spaceId;
    if (typeof space === "string") {
      color.space = ColorSpace.get(space);
    }
    if (color.alpha === void 0) {
      color.alpha = 1;
    }
    return color;
  }

  // packages/theme/node_modules/colorjs.io/src/ColorSpace.js
  var \u03B5 = 75e-6;
  var ColorSpace = class _ColorSpace {
    constructor(options) {
      this.id = options.id;
      this.name = options.name;
      this.base = options.base ? _ColorSpace.get(options.base) : null;
      this.aliases = options.aliases;
      if (this.base) {
        this.fromBase = options.fromBase;
        this.toBase = options.toBase;
      }
      let coords = options.coords ?? this.base.coords;
      for (let name in coords) {
        if (!("name" in coords[name])) {
          coords[name].name = name;
        }
      }
      this.coords = coords;
      let white4 = options.white ?? this.base.white ?? "D65";
      this.white = getWhite(white4);
      this.formats = options.formats ?? {};
      for (let name in this.formats) {
        let format = this.formats[name];
        format.type ||= "function";
        format.name ||= name;
      }
      if (!this.formats.color?.id) {
        this.formats.color = {
          ...this.formats.color ?? {},
          id: options.cssId || this.id
        };
      }
      if (options.gamutSpace) {
        this.gamutSpace = options.gamutSpace === "self" ? this : _ColorSpace.get(options.gamutSpace);
      } else {
        if (this.isPolar) {
          this.gamutSpace = this.base;
        } else {
          this.gamutSpace = this;
        }
      }
      if (this.gamutSpace.isUnbounded) {
        this.inGamut = (coords2, options2) => {
          return true;
        };
      }
      this.referred = options.referred;
      Object.defineProperty(this, "path", {
        value: getPath(this).reverse(),
        writable: false,
        enumerable: true,
        configurable: true
      });
      hooks_default.run("colorspace-init-end", this);
    }
    inGamut(coords, { epsilon = \u03B5 } = {}) {
      if (!this.equals(this.gamutSpace)) {
        coords = this.to(this.gamutSpace, coords);
        return this.gamutSpace.inGamut(coords, { epsilon });
      }
      let coordMeta = Object.values(this.coords);
      return coords.every((c, i) => {
        let meta = coordMeta[i];
        if (meta.type !== "angle" && meta.range) {
          if (isNone(c)) {
            return true;
          }
          let [min, max] = meta.range;
          return (min === void 0 || c >= min - epsilon) && (max === void 0 || c <= max + epsilon);
        }
        return true;
      });
    }
    get isUnbounded() {
      return Object.values(this.coords).every((coord) => !("range" in coord));
    }
    get cssId() {
      return this.formats?.color?.id || this.id;
    }
    get isPolar() {
      for (let id in this.coords) {
        if (this.coords[id].type === "angle") {
          return true;
        }
      }
      return false;
    }
    /**
     * Lookup a format in this color space
     * @param {string | object | Format} format - Format id if string. If object, it's converted to a `Format` object and returned.
     * @returns {Format}
     */
    getFormat(format) {
      if (!format) {
        return null;
      }
      if (format === "default") {
        format = Object.values(this.formats)[0];
      } else if (typeof format === "string") {
        format = this.formats[format];
      }
      let ret = Format.get(format, this);
      if (ret !== format && format.name in this.formats) {
        this.formats[format.name] = ret;
      }
      return ret;
    }
    /**
     * Check if this color space is the same as another color space reference.
     * Allows proxying color space objects and comparing color spaces with ids.
     * @param {string | ColorSpace} space ColorSpace object or id to compare to
     * @returns {boolean}
     */
    equals(space) {
      if (!space) {
        return false;
      }
      return this === space || this.id === space || this.id === space.id;
    }
    to(space, coords) {
      if (arguments.length === 1) {
        const color = getColor(space);
        [space, coords] = [color.space, color.coords];
      }
      space = _ColorSpace.get(space);
      if (this.equals(space)) {
        return coords;
      }
      coords = coords.map((c) => isNone(c) ? 0 : c);
      let myPath = this.path;
      let otherPath = space.path;
      let connectionSpace, connectionSpaceIndex;
      for (let i = 0; i < myPath.length; i++) {
        if (myPath[i].equals(otherPath[i])) {
          connectionSpace = myPath[i];
          connectionSpaceIndex = i;
        } else {
          break;
        }
      }
      if (!connectionSpace) {
        throw new Error(
          `Cannot convert between color spaces ${this} and ${space}: no connection space was found`
        );
      }
      for (let i = myPath.length - 1; i > connectionSpaceIndex; i--) {
        coords = myPath[i].toBase(coords);
      }
      for (let i = connectionSpaceIndex + 1; i < otherPath.length; i++) {
        coords = otherPath[i].fromBase(coords);
      }
      return coords;
    }
    from(space, coords) {
      if (arguments.length === 1) {
        const color = getColor(space);
        [space, coords] = [color.space, color.coords];
      }
      space = _ColorSpace.get(space);
      return space.to(this, coords);
    }
    toString() {
      return `${this.name} (${this.id})`;
    }
    getMinCoords() {
      let ret = [];
      for (let id in this.coords) {
        let meta = this.coords[id];
        let range = meta.range || meta.refRange;
        ret.push(range?.min ?? 0);
      }
      return ret;
    }
    static registry = {};
    // Returns array of unique color spaces
    static get all() {
      return [...new Set(Object.values(_ColorSpace.registry))];
    }
    static register(id, space) {
      if (arguments.length === 1) {
        space = arguments[0];
        id = space.id;
      }
      space = this.get(space);
      if (this.registry[id] && this.registry[id] !== space) {
        throw new Error(`Duplicate color space registration: '${id}'`);
      }
      this.registry[id] = space;
      if (arguments.length === 1 && space.aliases) {
        for (let alias of space.aliases) {
          this.register(alias, space);
        }
      }
      return space;
    }
    /**
     * Lookup ColorSpace object by name
     * @param {ColorSpace | string} name
     */
    static get(space, ...alternatives) {
      if (!space || isInstance(space, this)) {
        return space;
      }
      let argType = type(space);
      if (argType === "string") {
        let ret = _ColorSpace.registry[space.toLowerCase()];
        if (!ret) {
          throw new TypeError(`No color space found with id = "${space}"`);
        }
        return ret;
      }
      if (alternatives.length) {
        return _ColorSpace.get(...alternatives);
      }
      throw new TypeError(`${space} is not a valid color space`);
    }
    /**
     * Look up all color spaces for a format that matches certain criteria
     * @param {object | string} filters
     * @param {Array<ColorSpace>} [spaces=ColorSpace.all]
     * @returns {Format | null}
     */
    static findFormat(filters, spaces = _ColorSpace.all) {
      if (!filters) {
        return null;
      }
      if (typeof filters === "string") {
        filters = { name: filters };
      }
      for (let space of spaces) {
        for (let [name, format] of Object.entries(space.formats)) {
          format.name ??= name;
          format.type ??= "function";
          let matches = (!filters.name || format.name === filters.name) && (!filters.type || format.type === filters.type);
          if (filters.id) {
            let ids = format.ids || [format.id];
            let filterIds = Array.isArray(filters.id) ? filters.id : [filters.id];
            matches &&= filterIds.some((id) => ids.includes(id));
          }
          if (matches) {
            let ret = Format.get(format, space);
            if (ret !== format) {
              space.formats[format.name] = ret;
            }
            return ret;
          }
        }
      }
      return null;
    }
    /**
     * Get metadata about a coordinate of a color space
     *
     * @static
     * @param {Array | string} ref
     * @param {ColorSpace | string} [workingSpace]
     * @return {Object}
     */
    static resolveCoord(ref, workingSpace) {
      let coordType = type(ref);
      let space, coord;
      if (coordType === "string") {
        if (ref.includes(".")) {
          [space, coord] = ref.split(".");
        } else {
          [space, coord] = [, ref];
        }
      } else if (Array.isArray(ref)) {
        [space, coord] = ref;
      } else {
        space = ref.space;
        coord = ref.coordId;
      }
      space = _ColorSpace.get(space);
      if (!space) {
        space = workingSpace;
      }
      if (!space) {
        throw new TypeError(
          `Cannot resolve coordinate reference ${ref}: No color space specified and relative references are not allowed here`
        );
      }
      coordType = type(coord);
      if (coordType === "number" || coordType === "string" && coord >= 0) {
        let meta = Object.entries(space.coords)[coord];
        if (meta) {
          return { space, id: meta[0], index: coord, ...meta[1] };
        }
      }
      space = _ColorSpace.get(space);
      let normalizedCoord = coord.toLowerCase();
      let i = 0;
      for (let id in space.coords) {
        let meta = space.coords[id];
        if (id.toLowerCase() === normalizedCoord || meta.name?.toLowerCase() === normalizedCoord) {
          return { space, id, index: i, ...meta };
        }
        i++;
      }
      throw new TypeError(
        `No "${coord}" coordinate found in ${space.name}. Its coordinates are: ${Object.keys(space.coords).join(", ")}`
      );
    }
    static DEFAULT_FORMAT = {
      type: "functions",
      name: "color"
    };
  };
  function getPath(space) {
    let ret = [space];
    for (let s = space; s = s.base; ) {
      ret.push(s);
    }
    return ret;
  }

  // packages/theme/node_modules/colorjs.io/src/spaces/xyz-d65.js
  var xyz_d65_default = new ColorSpace({
    id: "xyz-d65",
    name: "XYZ D65",
    coords: {
      x: {
        refRange: [0, 1],
        name: "X"
      },
      y: {
        refRange: [0, 1],
        name: "Y"
      },
      z: {
        refRange: [0, 1],
        name: "Z"
      }
    },
    white: "D65",
    formats: {
      color: {
        ids: ["xyz-d65", "xyz"]
      }
    },
    aliases: ["xyz"]
  });

  // packages/theme/node_modules/colorjs.io/src/RGBColorSpace.js
  var RGBColorSpace = class extends ColorSpace {
    /**
     * Creates a new RGB ColorSpace.
     * If coords are not specified, they will use the default RGB coords.
     * Instead of `fromBase()` and `toBase()` functions,
     * you can specify to/from XYZ matrices and have `toBase()` and `fromBase()` automatically generated.
     * @param {RGBOptions} options
     */
    constructor(options) {
      if (!options.coords) {
        options.coords = {
          r: {
            range: [0, 1],
            name: "Red"
          },
          g: {
            range: [0, 1],
            name: "Green"
          },
          b: {
            range: [0, 1],
            name: "Blue"
          }
        };
      }
      if (!options.base) {
        options.base = xyz_d65_default;
      }
      if (options.toXYZ_M && options.fromXYZ_M) {
        options.toBase ??= (rgb) => {
          let xyz = multiply_v3_m3x3(rgb, options.toXYZ_M);
          if (this.white !== this.base.white) {
            xyz = adapt(this.white, this.base.white, xyz);
          }
          return xyz;
        };
        options.fromBase ??= (xyz) => {
          xyz = adapt(this.base.white, this.white, xyz);
          return multiply_v3_m3x3(xyz, options.fromXYZ_M);
        };
      }
      options.referred ??= "display";
      super(options);
    }
  };

  // packages/theme/node_modules/colorjs.io/src/getAll.js
  function getAll(color, options) {
    color = getColor(color);
    let space = ColorSpace.get(options, options?.space);
    let precision = options?.precision;
    let coords;
    if (!space || color.space.equals(space)) {
      coords = color.coords.slice();
    } else {
      coords = space.from(color);
    }
    return precision === void 0 ? coords : coords.map((coord) => toPrecision(coord, precision));
  }

  // packages/theme/node_modules/colorjs.io/src/get.js
  function get(color, prop) {
    color = getColor(color);
    if (prop === "alpha") {
      return color.alpha ?? 1;
    }
    let { space, index } = ColorSpace.resolveCoord(prop, color.space);
    let coords = getAll(color, space);
    return coords[index];
  }

  // packages/theme/node_modules/colorjs.io/src/setAll.js
  function setAll(color, space, coords, alpha) {
    color = getColor(color);
    if (Array.isArray(space)) {
      [space, coords, alpha] = [color.space, space, coords];
    }
    space = ColorSpace.get(space);
    color.coords = space === color.space ? coords.slice() : space.to(color.space, coords);
    if (alpha !== void 0) {
      color.alpha = alpha;
    }
    return color;
  }
  setAll.returns = "color";

  // packages/theme/node_modules/colorjs.io/src/set.js
  function set(color, prop, value) {
    color = getColor(color);
    if (arguments.length === 2 && type(arguments[1]) === "object") {
      let object = arguments[1];
      for (let p2 in object) {
        set(color, p2, object[p2]);
      }
    } else {
      if (typeof value === "function") {
        value = value(get(color, prop));
      }
      if (prop === "alpha") {
        color.alpha = value;
      } else {
        let { space, index } = ColorSpace.resolveCoord(prop, color.space);
        let coords = getAll(color, space);
        coords[index] = value;
        setAll(color, space, coords);
      }
    }
    return color;
  }
  set.returns = "color";

  // packages/theme/node_modules/colorjs.io/src/spaces/xyz-d50.js
  var xyz_d50_default = new ColorSpace({
    id: "xyz-d50",
    name: "XYZ D50",
    white: "D50",
    base: xyz_d65_default,
    fromBase: (coords) => adapt(xyz_d65_default.white, "D50", coords),
    toBase: (coords) => adapt("D50", xyz_d65_default.white, coords)
  });

  // packages/theme/node_modules/colorjs.io/src/spaces/lab.js
  var \u03B52 = 216 / 24389;
  var \u03B53 = 24 / 116;
  var \u03BA = 24389 / 27;
  var white = WHITES.D50;
  var lab_default = new ColorSpace({
    id: "lab",
    name: "Lab",
    coords: {
      l: {
        refRange: [0, 100],
        name: "Lightness"
      },
      a: {
        refRange: [-125, 125]
      },
      b: {
        refRange: [-125, 125]
      }
    },
    // Assuming XYZ is relative to D50, convert to CIE Lab
    // from CIE standard, which now defines these as a rational fraction
    white,
    base: xyz_d50_default,
    // Convert D50-adapted XYX to Lab
    // CIE 15.3:2004 section 8.2.1.1
    fromBase(XYZ) {
      let xyz = XYZ.map((value, i) => value / white[i]);
      let f = xyz.map((value) => value > \u03B52 ? Math.cbrt(value) : (\u03BA * value + 16) / 116);
      let L = 116 * f[1] - 16;
      let a = 500 * (f[0] - f[1]);
      let b2 = 200 * (f[1] - f[2]);
      return [L, a, b2];
    },
    // Convert Lab to D50-adapted XYZ
    // Same result as CIE 15.3:2004 Appendix D although the derivation is different
    // http://www.brucelindbloom.com/index.html?Eqn_RGB_XYZ_Matrix.html
    toBase(Lab) {
      let [L, a, b2] = Lab;
      let f = [];
      f[1] = (L + 16) / 116;
      f[0] = a / 500 + f[1];
      f[2] = f[1] - b2 / 200;
      let xyz = [
        f[0] > \u03B53 ? Math.pow(f[0], 3) : (116 * f[0] - 16) / \u03BA,
        Lab[0] > 8 ? Math.pow((Lab[0] + 16) / 116, 3) : Lab[0] / \u03BA,
        f[2] > \u03B53 ? Math.pow(f[2], 3) : (116 * f[2] - 16) / \u03BA
      ];
      return xyz.map((value, i) => value * white[i]);
    },
    formats: {
      lab: {
        coords: [
          "<percentage> | <number>",
          "<number> | <percentage>",
          "<number> | <percentage>"
        ]
      }
    }
  });

  // packages/theme/node_modules/colorjs.io/src/angles.js
  function constrain(angle) {
    if (typeof angle !== "number") {
      return angle;
    }
    return (angle % 360 + 360) % 360;
  }

  // packages/theme/node_modules/colorjs.io/src/spaces/lch.js
  var lch_default = new ColorSpace({
    id: "lch",
    name: "LCH",
    coords: {
      l: {
        refRange: [0, 100],
        name: "Lightness"
      },
      c: {
        refRange: [0, 150],
        name: "Chroma"
      },
      h: {
        refRange: [0, 360],
        type: "angle",
        name: "Hue"
      }
    },
    base: lab_default,
    fromBase(Lab) {
      if (this.\u03B5 === void 0) {
        let range = Object.values(this.base.coords)[1].refRange;
        let extent = range[1] - range[0];
        this.\u03B5 = extent / 1e5;
      }
      let [L, a, b2] = Lab;
      let isAchromatic = Math.abs(a) < this.\u03B5 && Math.abs(b2) < this.\u03B5;
      let h = isAchromatic ? null : constrain(Math.atan2(b2, a) * 180 / Math.PI);
      let C = isAchromatic ? 0 : Math.sqrt(a ** 2 + b2 ** 2);
      return [L, C, h];
    },
    toBase(lch) {
      let [L, C, h] = lch;
      let a = null, b2 = null;
      if (!isNone(h)) {
        C = C < 0 ? 0 : C;
        a = C * Math.cos(h * Math.PI / 180);
        b2 = C * Math.sin(h * Math.PI / 180);
      }
      return [L, a, b2];
    },
    formats: {
      lch: {
        coords: ["<percentage> | <number>", "<number> | <percentage>", "<number> | <angle>"]
      }
    }
  });

  // packages/theme/node_modules/colorjs.io/src/deltaE/deltaE2000.js
  var Gfactor = 25 ** 7;
  var \u03C0 = Math.PI;
  var r2d = 180 / \u03C0;
  var d2r = \u03C0 / 180;
  function pow7(x) {
    const x2 = x * x;
    const x7 = x2 * x2 * x2 * x;
    return x7;
  }
  function deltaE2000_default(color, sample, { kL = 1, kC = 1, kH = 1 } = {}) {
    [color, sample] = getColor([color, sample]);
    let [L1, a1, b1] = lab_default.from(color);
    let C1 = lch_default.from(lab_default, [L1, a1, b1])[1];
    let [L2, a2, b2] = lab_default.from(sample);
    let C2 = lch_default.from(lab_default, [L2, a2, b2])[1];
    if (C1 < 0) {
      C1 = 0;
    }
    if (C2 < 0) {
      C2 = 0;
    }
    let Cbar = (C1 + C2) / 2;
    let C7 = pow7(Cbar);
    let G = 0.5 * (1 - Math.sqrt(C7 / (C7 + Gfactor)));
    let adash1 = (1 + G) * a1;
    let adash2 = (1 + G) * a2;
    let Cdash1 = Math.sqrt(adash1 ** 2 + b1 ** 2);
    let Cdash2 = Math.sqrt(adash2 ** 2 + b2 ** 2);
    let h1 = adash1 === 0 && b1 === 0 ? 0 : Math.atan2(b1, adash1);
    let h2 = adash2 === 0 && b2 === 0 ? 0 : Math.atan2(b2, adash2);
    if (h1 < 0) {
      h1 += 2 * \u03C0;
    }
    if (h2 < 0) {
      h2 += 2 * \u03C0;
    }
    h1 *= r2d;
    h2 *= r2d;
    let \u0394L = L2 - L1;
    let \u0394C = Cdash2 - Cdash1;
    let hdiff = h2 - h1;
    let hsum = h1 + h2;
    let habs = Math.abs(hdiff);
    let \u0394h;
    if (Cdash1 * Cdash2 === 0) {
      \u0394h = 0;
    } else if (habs <= 180) {
      \u0394h = hdiff;
    } else if (hdiff > 180) {
      \u0394h = hdiff - 360;
    } else if (hdiff < -180) {
      \u0394h = hdiff + 360;
    } else {
      defaults_default.warn("the unthinkable has happened");
    }
    let \u0394H = 2 * Math.sqrt(Cdash2 * Cdash1) * Math.sin(\u0394h * d2r / 2);
    let Ldash = (L1 + L2) / 2;
    let Cdash = (Cdash1 + Cdash2) / 2;
    let Cdash7 = pow7(Cdash);
    let hdash;
    if (Cdash1 * Cdash2 === 0) {
      hdash = hsum;
    } else if (habs <= 180) {
      hdash = hsum / 2;
    } else if (hsum < 360) {
      hdash = (hsum + 360) / 2;
    } else {
      hdash = (hsum - 360) / 2;
    }
    let lsq = (Ldash - 50) ** 2;
    let SL = 1 + 0.015 * lsq / Math.sqrt(20 + lsq);
    let SC = 1 + 0.045 * Cdash;
    let T = 1;
    T -= 0.17 * Math.cos((hdash - 30) * d2r);
    T += 0.24 * Math.cos(2 * hdash * d2r);
    T += 0.32 * Math.cos((3 * hdash + 6) * d2r);
    T -= 0.2 * Math.cos((4 * hdash - 63) * d2r);
    let SH = 1 + 0.015 * Cdash * T;
    let \u0394\u03B8 = 30 * Math.exp(-1 * ((hdash - 275) / 25) ** 2);
    let RC = 2 * Math.sqrt(Cdash7 / (Cdash7 + Gfactor));
    let RT = -1 * Math.sin(2 * \u0394\u03B8 * d2r) * RC;
    let dE = (\u0394L / (kL * SL)) ** 2;
    dE += (\u0394C / (kC * SC)) ** 2;
    dE += (\u0394H / (kH * SH)) ** 2;
    dE += RT * (\u0394C / (kC * SC)) * (\u0394H / (kH * SH));
    return Math.sqrt(dE);
  }

  // packages/theme/node_modules/colorjs.io/src/spaces/oklab.js
  var XYZtoLMS_M = [
    [0.819022437996703, 0.3619062600528904, -0.1288737815209879],
    [0.0329836539323885, 0.9292868615863434, 0.0361446663506424],
    [0.0481771893596242, 0.2642395317527308, 0.6335478284694309]
  ];
  var LMStoXYZ_M = [
    [1.2268798758459243, -0.5578149944602171, 0.2813910456659647],
    [-0.0405757452148008, 1.112286803280317, -0.0717110580655164],
    [-0.0763729366746601, -0.4214933324022432, 1.5869240198367816]
  ];
  var LMStoLab_M = [
    [0.210454268309314, 0.7936177747023054, -0.0040720430116193],
    [1.9779985324311684, -2.42859224204858, 0.450593709617411],
    [0.0259040424655478, 0.7827717124575296, -0.8086757549230774]
  ];
  var LabtoLMS_M = [
    [1, 0.3963377773761749, 0.2158037573099136],
    [1, -0.1055613458156586, -0.0638541728258133],
    [1, -0.0894841775298119, -1.2914855480194092]
  ];
  var oklab_default = new ColorSpace({
    id: "oklab",
    name: "Oklab",
    coords: {
      l: {
        refRange: [0, 1],
        name: "Lightness"
      },
      a: {
        refRange: [-0.4, 0.4]
      },
      b: {
        refRange: [-0.4, 0.4]
      }
    },
    // Note that XYZ is relative to D65
    white: "D65",
    base: xyz_d65_default,
    fromBase(XYZ) {
      let LMS = multiply_v3_m3x3(XYZ, XYZtoLMS_M);
      LMS[0] = Math.cbrt(LMS[0]);
      LMS[1] = Math.cbrt(LMS[1]);
      LMS[2] = Math.cbrt(LMS[2]);
      return multiply_v3_m3x3(LMS, LMStoLab_M, LMS);
    },
    toBase(OKLab) {
      let LMSg = multiply_v3_m3x3(OKLab, LabtoLMS_M);
      LMSg[0] = LMSg[0] ** 3;
      LMSg[1] = LMSg[1] ** 3;
      LMSg[2] = LMSg[2] ** 3;
      return multiply_v3_m3x3(LMSg, LMStoXYZ_M, LMSg);
    },
    formats: {
      oklab: {
        coords: [
          "<percentage> | <number>",
          "<number> | <percentage>",
          "<number> | <percentage>"
        ]
      }
    }
  });

  // packages/theme/node_modules/colorjs.io/src/deltaE/deltaEOK.js
  function deltaEOK_default(color, sample) {
    [color, sample] = getColor([color, sample]);
    let [L1, a1, b1] = oklab_default.from(color);
    let [L2, a2, b2] = oklab_default.from(sample);
    let \u0394L = L1 - L2;
    let \u0394a = a1 - a2;
    let \u0394b = b1 - b2;
    return Math.sqrt(\u0394L ** 2 + \u0394a ** 2 + \u0394b ** 2);
  }

  // packages/theme/node_modules/colorjs.io/src/inGamut.js
  var \u03B54 = 75e-6;
  function inGamut(color, space, { epsilon = \u03B54 } = {}) {
    color = getColor(color);
    if (!space) {
      space = color.space;
    }
    space = ColorSpace.get(space);
    let coords = color.coords;
    if (space !== color.space) {
      coords = space.from(color);
    }
    return space.inGamut(coords, { epsilon });
  }

  // packages/theme/node_modules/colorjs.io/src/clone.js
  function clone(color) {
    return {
      space: color.space,
      coords: (
        /** @type {Coords} */
        color.coords.slice()
      ),
      alpha: color.alpha
    };
  }

  // packages/theme/node_modules/colorjs.io/src/distance.js
  function distance(color1, color2, space = "lab") {
    space = ColorSpace.get(space);
    let coords1 = space.from(color1);
    let coords2 = space.from(color2);
    return Math.sqrt(
      coords1.reduce((acc, c13, i) => {
        let c23 = coords2[i];
        if (isNone(c13) || isNone(c23)) {
          return acc;
        }
        return acc + (c23 - c13) ** 2;
      }, 0)
    );
  }

  // packages/theme/node_modules/colorjs.io/src/deltaE/deltaE76.js
  function deltaE76(color, sample) {
    return distance(color, sample, "lab");
  }

  // packages/theme/node_modules/colorjs.io/src/deltaE/deltaECMC.js
  var \u03C02 = Math.PI;
  var d2r2 = \u03C02 / 180;
  function deltaECMC_default(color, sample, { l = 2, c = 1 } = {}) {
    [color, sample] = getColor([color, sample]);
    let [L1, a1, b1] = lab_default.from(color);
    let [, C1, H1] = lch_default.from(lab_default, [L1, a1, b1]);
    let [L2, a2, b2] = lab_default.from(sample);
    let C2 = lch_default.from(lab_default, [L2, a2, b2])[1];
    if (C1 < 0) {
      C1 = 0;
    }
    if (C2 < 0) {
      C2 = 0;
    }
    let \u0394L = L1 - L2;
    let \u0394C = C1 - C2;
    let \u0394a = a1 - a2;
    let \u0394b = b1 - b2;
    let H2 = \u0394a ** 2 + \u0394b ** 2 - \u0394C ** 2;
    let SL = 0.511;
    if (L1 >= 16) {
      SL = 0.040975 * L1 / (1 + 0.01765 * L1);
    }
    let SC = 0.0638 * C1 / (1 + 0.0131 * C1) + 0.638;
    let T;
    if (isNone(H1)) {
      H1 = 0;
    }
    if (H1 >= 164 && H1 <= 345) {
      T = 0.56 + Math.abs(0.2 * Math.cos((H1 + 168) * d2r2));
    } else {
      T = 0.36 + Math.abs(0.4 * Math.cos((H1 + 35) * d2r2));
    }
    let C4 = Math.pow(C1, 4);
    let F = Math.sqrt(C4 / (C4 + 1900));
    let SH = SC * (F * T + 1 - F);
    let dE = (\u0394L / (l * SL)) ** 2;
    dE += (\u0394C / (c * SC)) ** 2;
    dE += H2 / SH ** 2;
    return Math.sqrt(dE);
  }

  // packages/theme/node_modules/colorjs.io/src/spaces/xyz-abs-d65.js
  var Yw = 203;
  var xyz_abs_d65_default = new ColorSpace({
    // Absolute CIE XYZ, with a D65 whitepoint,
    // as used in most HDR colorspaces as a starting point.
    // SDR spaces are converted per BT.2048
    // so that diffuse, media white is 203 cd/m²
    id: "xyz-abs-d65",
    cssId: "--xyz-abs-d65",
    name: "Absolute XYZ D65",
    coords: {
      x: {
        refRange: [0, 9504.7],
        name: "Xa"
      },
      y: {
        refRange: [0, 1e4],
        name: "Ya"
      },
      z: {
        refRange: [0, 10888.3],
        name: "Za"
      }
    },
    base: xyz_d65_default,
    fromBase(XYZ) {
      return XYZ.map((v) => v * Yw);
    },
    toBase(AbsXYZ) {
      return AbsXYZ.map((v) => v / Yw);
    }
  });

  // packages/theme/node_modules/colorjs.io/src/spaces/jzazbz.js
  var b = 1.15;
  var g = 0.66;
  var n = 2610 / 2 ** 14;
  var ninv = 2 ** 14 / 2610;
  var c1 = 3424 / 2 ** 12;
  var c2 = 2413 / 2 ** 7;
  var c3 = 2392 / 2 ** 7;
  var p = 1.7 * 2523 / 2 ** 5;
  var pinv = 2 ** 5 / (1.7 * 2523);
  var d = -0.56;
  var d0 = 16295499532821565e-27;
  var XYZtoCone_M = [
    [0.41478972, 0.579999, 0.014648],
    [-0.20151, 1.120649, 0.0531008],
    [-0.0166008, 0.2648, 0.6684799]
  ];
  var ConetoXYZ_M = [
    [1.9242264357876067, -1.0047923125953657, 0.037651404030618],
    [0.35031676209499907, 0.7264811939316552, -0.06538442294808501],
    [-0.09098281098284752, -0.3127282905230739, 1.5227665613052603]
  ];
  var ConetoIab_M = [
    [0.5, 0.5, 0],
    [3.524, -4.066708, 0.542708],
    [0.199076, 1.096799, -1.295875]
  ];
  var IabtoCone_M = [
    [1, 0.13860504327153927, 0.05804731615611883],
    [1, -0.1386050432715393, -0.058047316156118904],
    [1, -0.09601924202631895, -0.811891896056039]
  ];
  var jzazbz_default = new ColorSpace({
    id: "jzazbz",
    name: "Jzazbz",
    coords: {
      jz: {
        refRange: [0, 1],
        name: "Jz"
      },
      az: {
        refRange: [-0.21, 0.21]
      },
      bz: {
        refRange: [-0.21, 0.21]
      }
    },
    base: xyz_abs_d65_default,
    fromBase(XYZ) {
      let [Xa, Ya, Za] = XYZ;
      let Xm = b * Xa - (b - 1) * Za;
      let Ym = g * Ya - (g - 1) * Xa;
      let LMS = multiply_v3_m3x3([Xm, Ym, Za], XYZtoCone_M);
      let PQLMS = (
        /** @type {Vector3} } */
        LMS.map(function(val) {
          let num = c1 + c2 * spow(val / 1e4, n);
          let denom = 1 + c3 * spow(val / 1e4, n);
          return spow(num / denom, p);
        })
      );
      let [Iz, az, bz] = multiply_v3_m3x3(PQLMS, ConetoIab_M);
      let Jz = (1 + d) * Iz / (1 + d * Iz) - d0;
      return [Jz, az, bz];
    },
    toBase(Jzazbz) {
      let [Jz, az, bz] = Jzazbz;
      let Iz = (Jz + d0) / (1 + d - d * (Jz + d0));
      let PQLMS = multiply_v3_m3x3([Iz, az, bz], IabtoCone_M);
      let LMS = (
        /** @type {Vector3} } */
        PQLMS.map(function(val) {
          let num = c1 - spow(val, pinv);
          let denom = c3 * spow(val, pinv) - c2;
          let x = 1e4 * spow(num / denom, ninv);
          return x;
        })
      );
      let [Xm, Ym, Za] = multiply_v3_m3x3(LMS, ConetoXYZ_M);
      let Xa = (Xm + (b - 1) * Za) / b;
      let Ya = (Ym + (g - 1) * Xa) / g;
      return [Xa, Ya, Za];
    },
    formats: {
      // https://drafts.csswg.org/css-color-hdr/#Jzazbz
      jzazbz: {
        coords: [
          "<percentage> | <number>",
          "<number> | <percentage>",
          "<number> | <percentage>"
        ]
      }
    }
  });

  // packages/theme/node_modules/colorjs.io/src/spaces/jzczhz.js
  var jzczhz_default = new ColorSpace({
    id: "jzczhz",
    name: "JzCzHz",
    coords: {
      jz: {
        refRange: [0, 1],
        name: "Jz"
      },
      cz: {
        refRange: [0, 0.26],
        name: "Chroma"
      },
      hz: {
        refRange: [0, 360],
        type: "angle",
        name: "Hue"
      }
    },
    base: jzazbz_default,
    fromBase: lch_default.fromBase,
    toBase: lch_default.toBase,
    formats: {
      // https://drafts.csswg.org/css-color-hdr/#JzCzhz
      jzczhz: {
        coords: ["<percentage> | <number>", "<number> | <percentage>", "<number> | <angle>"]
      }
    }
  });

  // packages/theme/node_modules/colorjs.io/src/deltaE/deltaEJz.js
  function deltaEJz_default(color, sample) {
    [color, sample] = getColor([color, sample]);
    let [Jz1, Cz1, Hz1] = jzczhz_default.from(color);
    let [Jz2, Cz2, Hz2] = jzczhz_default.from(sample);
    let \u0394J = Jz1 - Jz2;
    let \u0394C = Cz1 - Cz2;
    if (isNone(Hz1) && isNone(Hz2)) {
      Hz1 = 0;
      Hz2 = 0;
    } else if (isNone(Hz1)) {
      Hz1 = Hz2;
    } else if (isNone(Hz2)) {
      Hz2 = Hz1;
    }
    let \u0394h = Hz1 - Hz2;
    let \u0394H = 2 * Math.sqrt(Cz1 * Cz2) * Math.sin(\u0394h / 2 * (Math.PI / 180));
    return Math.sqrt(\u0394J ** 2 + \u0394C ** 2 + \u0394H ** 2);
  }

  // packages/theme/node_modules/colorjs.io/src/spaces/ictcp.js
  var c12 = 3424 / 4096;
  var c22 = 2413 / 128;
  var c32 = 2392 / 128;
  var m1 = 2610 / 16384;
  var m2 = 2523 / 32;
  var im1 = 16384 / 2610;
  var im2 = 32 / 2523;
  var XYZtoLMS_M2 = [
    [0.3592832590121217, 0.6976051147779502, -0.035891593232029],
    [-0.1920808463704993, 1.100476797037432, 0.0753748658519118],
    [0.0070797844607479, 0.0748396662186362, 0.8433265453898765]
  ];
  var LMStoIPT_M = [
    [2048 / 4096, 2048 / 4096, 0],
    [6610 / 4096, -13613 / 4096, 7003 / 4096],
    [17933 / 4096, -17390 / 4096, -543 / 4096]
  ];
  var IPTtoLMS_M = [
    [0.9999999999999998, 0.0086090370379328, 0.111029625003026],
    [0.9999999999999998, -0.0086090370379328, -0.1110296250030259],
    [0.9999999999999998, 0.5600313357106791, -0.3206271749873188]
  ];
  var LMStoXYZ_M2 = [
    [2.0701522183894223, -1.3263473389671563, 0.2066510476294053],
    [0.3647385209748072, 0.6805660249472273, -0.0453045459220347],
    [-0.0497472075358123, -0.0492609666966131, 1.1880659249923042]
  ];
  var ictcp_default = new ColorSpace({
    id: "ictcp",
    name: "ICTCP",
    // From BT.2100-2 page 7:
    // During production, signal values are expected to exceed the
    // range E′ = [0.0 : 1.0]. This provides processing headroom and avoids
    // signal degradation during cascaded processing. Such values of E′,
    // below 0.0 or exceeding 1.0, should not be clipped during production
    // and exchange.
    // Values below 0.0 should not be clipped in reference displays (even
    // though they represent “negative” light) to allow the black level of
    // the signal (LB) to be properly set using test signals known as “PLUGE”
    coords: {
      i: {
        refRange: [0, 1],
        // Constant luminance,
        name: "I"
      },
      ct: {
        refRange: [-0.5, 0.5],
        // Full BT.2020 gamut in range [-0.5, 0.5]
        name: "CT"
      },
      cp: {
        refRange: [-0.5, 0.5],
        name: "CP"
      }
    },
    base: xyz_abs_d65_default,
    fromBase(XYZ) {
      let LMS = multiply_v3_m3x3(XYZ, XYZtoLMS_M2);
      return LMStoICtCp(LMS);
    },
    toBase(ICtCp) {
      let LMS = ICtCptoLMS(ICtCp);
      return multiply_v3_m3x3(LMS, LMStoXYZ_M2);
    },
    formats: {
      ictcp: {
        coords: [
          "<percentage> | <number>",
          "<number> | <percentage>",
          "<number> | <percentage>"
        ]
      }
    }
  });
  function LMStoICtCp(LMS) {
    let PQLMS = (
      /** @type {Vector3} */
      LMS.map(function(val) {
        let num = c12 + c22 * (val / 1e4) ** m1;
        let denom = 1 + c32 * (val / 1e4) ** m1;
        return (num / denom) ** m2;
      })
    );
    return multiply_v3_m3x3(PQLMS, LMStoIPT_M);
  }
  function ICtCptoLMS(ICtCp) {
    let PQLMS = multiply_v3_m3x3(ICtCp, IPTtoLMS_M);
    let LMS = (
      /** @type {Vector3} */
      PQLMS.map(function(val) {
        let num = Math.max(val ** im2 - c12, 0);
        let denom = c22 - c32 * val ** im2;
        return 1e4 * (num / denom) ** im1;
      })
    );
    return LMS;
  }

  // packages/theme/node_modules/colorjs.io/src/deltaE/deltaEITP.js
  function deltaEITP_default(color, sample) {
    [color, sample] = getColor([color, sample]);
    let [I1, T1, P1] = ictcp_default.from(color);
    let [I2, T2, P2] = ictcp_default.from(sample);
    return 720 * Math.sqrt((I1 - I2) ** 2 + 0.25 * (T1 - T2) ** 2 + (P1 - P2) ** 2);
  }

  // packages/theme/node_modules/colorjs.io/src/deltaE/deltaEOK2.js
  function deltaEOK2_default(color, sample) {
    [color, sample] = getColor([color, sample]);
    let abscale = 2;
    let [L1, a1, b1] = oklab_default.from(color);
    let [L2, a2, b2] = oklab_default.from(sample);
    let \u0394L = L1 - L2;
    let \u0394a = abscale * (a1 - a2);
    let \u0394b = abscale * (b1 - b2);
    return Math.sqrt(\u0394L ** 2 + \u0394a ** 2 + \u0394b ** 2);
  }

  // packages/theme/node_modules/colorjs.io/src/spaces/cam16.js
  var white2 = WHITES.D65;
  var adaptedCoef = 0.42;
  var adaptedCoefInv = 1 / adaptedCoef;
  var tau = 2 * Math.PI;
  var cat16 = [
    [0.401288, 0.650173, -0.051461],
    [-0.250268, 1.204414, 0.045854],
    [-2079e-6, 0.048952, 0.953127]
  ];
  var cat16Inv = [
    [1.8620678550872327, -1.0112546305316843, 0.14918677544445175],
    [0.38752654323613717, 0.6214474419314753, -0.008973985167612518],
    [-0.015841498849333856, -0.03412293802851557, 1.0499644368778496]
  ];
  var m12 = [
    [460, 451, 288],
    [460, -891, -261],
    [460, -220, -6300]
  ];
  var surroundMap = {
    dark: [0.8, 0.525, 0.8],
    dim: [0.9, 0.59, 0.9],
    average: [1, 0.69, 1]
  };
  var hueQuadMap = {
    // Red, Yellow, Green, Blue, Red
    h: [20.14, 90, 164.25, 237.53, 380.14],
    e: [0.8, 0.7, 1, 1.2, 0.8],
    H: [0, 100, 200, 300, 400]
  };
  var rad2deg = 180 / Math.PI;
  var deg2rad = Math.PI / 180;
  function adapt2(coords, fl) {
    const temp = (
      /** @type {[number, number, number]} */
      coords.map((c) => {
        const x = spow(fl * Math.abs(c) * 0.01, adaptedCoef);
        return 400 * copySign(x, c) / (x + 27.13);
      })
    );
    return temp;
  }
  function unadapt(adapted, fl) {
    const constant = 100 / fl * 27.13 ** adaptedCoefInv;
    return (
      /** @type {[number, number, number]} */
      adapted.map((c) => {
        const cabs = Math.abs(c);
        return copySign(constant * spow(cabs / (400 - cabs), adaptedCoefInv), c);
      })
    );
  }
  function hueQuadrature(h) {
    let hp = constrain(h);
    if (hp <= hueQuadMap.h[0]) {
      hp += 360;
    }
    const i = bisectLeft(hueQuadMap.h, hp) - 1;
    const [hi, hii] = hueQuadMap.h.slice(i, i + 2);
    const [ei, eii] = hueQuadMap.e.slice(i, i + 2);
    const Hi = hueQuadMap.H[i];
    const t = (hp - hi) / ei;
    return Hi + 100 * t / (t + (hii - hp) / eii);
  }
  function invHueQuadrature(H) {
    let Hp = (H % 400 + 400) % 400;
    const i = Math.floor(0.01 * Hp);
    Hp = Hp % 100;
    const [hi, hii] = hueQuadMap.h.slice(i, i + 2);
    const [ei, eii] = hueQuadMap.e.slice(i, i + 2);
    return constrain((Hp * (eii * hi - ei * hii) - 100 * hi * eii) / (Hp * (eii - ei) - 100 * eii));
  }
  function environment(refWhite, adaptingLuminance, backgroundLuminance, surround, discounting) {
    const env = {};
    env.discounting = discounting;
    env.refWhite = refWhite;
    env.surround = surround;
    const xyzW = (
      /** @type {Vector3} */
      refWhite.map((c) => {
        return c * 100;
      })
    );
    env.la = adaptingLuminance;
    env.yb = backgroundLuminance;
    const yw = xyzW[1];
    const rgbW = multiply_v3_m3x3(xyzW, cat16);
    let values = surroundMap[env.surround];
    const f = values[0];
    env.c = values[1];
    env.nc = values[2];
    const k = 1 / (5 * env.la + 1);
    const k4 = k ** 4;
    env.fl = k4 * env.la + 0.1 * (1 - k4) * (1 - k4) * Math.cbrt(5 * env.la);
    env.flRoot = env.fl ** 0.25;
    env.n = env.yb / yw;
    env.z = 1.48 + Math.sqrt(env.n);
    env.nbb = 0.725 * env.n ** -0.2;
    env.ncb = env.nbb;
    const d2 = discounting ? 1 : Math.max(Math.min(f * (1 - 1 / 3.6 * Math.exp((-env.la - 42) / 92)), 1), 0);
    env.dRgb = /** @type {[number, number, number]} */
    rgbW.map((c) => {
      return interpolate(1, yw / c, d2);
    });
    env.dRgbInv = /** @type {[number, number, number]} */
    env.dRgb.map((c) => {
      return 1 / c;
    });
    const rgbCW = (
      /** @type {[number, number, number]} */
      rgbW.map((c, i) => {
        return c * env.dRgb[i];
      })
    );
    const rgbAW = adapt2(rgbCW, env.fl);
    env.aW = env.nbb * (2 * rgbAW[0] + rgbAW[1] + 0.05 * rgbAW[2]);
    return env;
  }
  var viewingConditions = environment(white2, 64 / Math.PI * 0.2, 20, "average", false);
  function fromCam16(cam16, env) {
    if (!(cam16.J !== void 0 ^ cam16.Q !== void 0)) {
      throw new Error("Conversion requires one and only one: 'J' or 'Q'");
    }
    if (!(cam16.C !== void 0 ^ cam16.M !== void 0 ^ cam16.s !== void 0)) {
      throw new Error("Conversion requires one and only one: 'C', 'M' or 's'");
    }
    if (!(cam16.h !== void 0 ^ cam16.H !== void 0)) {
      throw new Error("Conversion requires one and only one: 'h' or 'H'");
    }
    if (cam16.J === 0 || cam16.Q === 0) {
      return [0, 0, 0];
    }
    let hRad = 0;
    if (cam16.h !== void 0) {
      hRad = constrain(cam16.h) * deg2rad;
    } else {
      hRad = invHueQuadrature(cam16.H) * deg2rad;
    }
    const cosh = Math.cos(hRad);
    const sinh = Math.sin(hRad);
    let Jroot = 0;
    if (cam16.J !== void 0) {
      Jroot = spow(cam16.J, 1 / 2) * 0.1;
    } else if (cam16.Q !== void 0) {
      Jroot = 0.25 * env.c * cam16.Q / ((env.aW + 4) * env.flRoot);
    }
    let alpha = 0;
    if (cam16.C !== void 0) {
      alpha = cam16.C / Jroot;
    } else if (cam16.M !== void 0) {
      alpha = cam16.M / env.flRoot / Jroot;
    } else if (cam16.s !== void 0) {
      alpha = 4e-4 * cam16.s ** 2 * (env.aW + 4) / env.c;
    }
    const t = spow(alpha * Math.pow(1.64 - Math.pow(0.29, env.n), -0.73), 10 / 9);
    const et = 0.25 * (Math.cos(hRad + 2) + 3.8);
    const A = env.aW * spow(Jroot, 2 / env.c / env.z);
    const p1 = 5e4 / 13 * env.nc * env.ncb * et;
    const p2 = A / env.nbb;
    const r = 23 * (p2 + 0.305) * zdiv(t, 23 * p1 + t * (11 * cosh + 108 * sinh));
    const a = r * cosh;
    const b2 = r * sinh;
    const rgb_c = unadapt(
      /** @type {Vector3} */
      multiply_v3_m3x3([p2, a, b2], m12).map((c) => {
        return c * 1 / 1403;
      }),
      env.fl
    );
    return (
      /** @type {Vector3} */
      multiply_v3_m3x3(
        /** @type {Vector3} */
        rgb_c.map((c, i) => {
          return c * env.dRgbInv[i];
        }),
        cat16Inv
      ).map((c) => {
        return c / 100;
      })
    );
  }
  function toCam16(xyzd65, env) {
    const xyz100 = (
      /** @type {Vector3} */
      xyzd65.map((c) => {
        return c * 100;
      })
    );
    const rgbA = adapt2(
      /** @type {[number, number, number]} */
      multiply_v3_m3x3(xyz100, cat16).map((c, i) => {
        return c * env.dRgb[i];
      }),
      env.fl
    );
    const a = rgbA[0] + (-12 * rgbA[1] + rgbA[2]) / 11;
    const b2 = (rgbA[0] + rgbA[1] - 2 * rgbA[2]) / 9;
    const hRad = (Math.atan2(b2, a) % tau + tau) % tau;
    const et = 0.25 * (Math.cos(hRad + 2) + 3.8);
    const t = 5e4 / 13 * env.nc * env.ncb * zdiv(et * Math.sqrt(a ** 2 + b2 ** 2), rgbA[0] + rgbA[1] + 1.05 * rgbA[2] + 0.305);
    const alpha = spow(t, 0.9) * Math.pow(1.64 - Math.pow(0.29, env.n), 0.73);
    const A = env.nbb * (2 * rgbA[0] + rgbA[1] + 0.05 * rgbA[2]);
    const Jroot = spow(A / env.aW, 0.5 * env.c * env.z);
    const J = 100 * spow(Jroot, 2);
    const Q = 4 / env.c * Jroot * (env.aW + 4) * env.flRoot;
    const C = alpha * Jroot;
    const M = C * env.flRoot;
    const h = constrain(hRad * rad2deg);
    const H = hueQuadrature(h);
    const s = 50 * spow(env.c * alpha / (env.aW + 4), 1 / 2);
    return { J, C, h, s, Q, M, H };
  }
  var cam16_default = new ColorSpace({
    id: "cam16-jmh",
    cssId: "--cam16-jmh",
    name: "CAM16-JMh",
    coords: {
      j: {
        refRange: [0, 100],
        name: "J"
      },
      m: {
        refRange: [0, 105],
        name: "Colorfulness"
      },
      h: {
        refRange: [0, 360],
        type: "angle",
        name: "Hue"
      }
    },
    base: xyz_d65_default,
    fromBase(xyz) {
      if (this.\u03B5 === void 0) {
        this.\u03B5 = Object.values(this.coords)[1].refRange[1] / 1e5;
      }
      const cam16 = toCam16(xyz, viewingConditions);
      const isAchromatic = Math.abs(cam16.M) < this.\u03B5;
      return [cam16.J, isAchromatic ? 0 : cam16.M, isAchromatic ? null : cam16.h];
    },
    toBase(cam16) {
      return fromCam16({ J: cam16[0], M: cam16[1], h: cam16[2] }, viewingConditions);
    }
  });

  // packages/theme/node_modules/colorjs.io/src/spaces/hct.js
  var white3 = WHITES.D65;
  var \u03B55 = 216 / 24389;
  var \u03BA2 = 24389 / 27;
  function toLstar(y) {
    const fy = y > \u03B55 ? Math.cbrt(y) : (\u03BA2 * y + 16) / 116;
    return 116 * fy - 16;
  }
  function fromLstar(lstar) {
    return lstar > 8 ? Math.pow((lstar + 16) / 116, 3) : lstar / \u03BA2;
  }
  function fromHct(coords, env) {
    let [h, c, t] = coords;
    let xyz = [];
    let j = 0;
    if (t === 0) {
      return [0, 0, 0];
    }
    let y = fromLstar(t);
    if (t > 0) {
      j = 0.00379058511492914 * t ** 2 + 0.608983189401032 * t + 0.9155088574762233;
    } else {
      j = 9514440756550361e-21 * t ** 2 + 0.08693057439788597 * t - 21.928975842194614;
    }
    const threshold = 2e-12;
    const max_attempts = 15;
    let attempt = 0;
    let last = Infinity;
    let best = j;
    while (attempt <= max_attempts) {
      xyz = fromCam16({ J: j, C: c, h }, env);
      const delta = Math.abs(xyz[1] - y);
      if (delta < last) {
        if (delta <= threshold) {
          return xyz;
        }
        best = j;
        last = delta;
      }
      j = j - (xyz[1] - y) * j / (2 * xyz[1]);
      attempt += 1;
    }
    return fromCam16({ J: j, C: c, h }, env);
  }
  function toHct(xyz, env) {
    const t = toLstar(xyz[1]);
    if (t === 0) {
      return [0, 0, 0];
    }
    const cam16 = toCam16(xyz, viewingConditions2);
    return [constrain(cam16.h), cam16.C, t];
  }
  var viewingConditions2 = environment(
    white3,
    200 / Math.PI * fromLstar(50),
    fromLstar(50) * 100,
    "average",
    false
  );
  var hct_default = new ColorSpace({
    id: "hct",
    name: "HCT",
    coords: {
      h: {
        refRange: [0, 360],
        type: "angle",
        name: "Hue"
      },
      c: {
        refRange: [0, 145],
        name: "Colorfulness"
      },
      t: {
        refRange: [0, 100],
        name: "Tone"
      }
    },
    base: xyz_d65_default,
    fromBase(xyz) {
      if (this.\u03B5 === void 0) {
        this.\u03B5 = Object.values(this.coords)[1].refRange[1] / 1e5;
      }
      let hct = toHct(xyz, viewingConditions2);
      if (hct[1] < this.\u03B5) {
        hct[1] = 0;
        hct[0] = null;
      }
      return hct;
    },
    toBase(hct) {
      return fromHct(hct, viewingConditions2);
    },
    formats: {
      color: {
        id: "--hct",
        coords: ["<number> | <angle>", "<percentage> | <number>", "<percentage> | <number>"]
      }
    }
  });

  // packages/theme/node_modules/colorjs.io/src/deltaE/deltaEHCT.js
  var rad2deg2 = 180 / Math.PI;
  var deg2rad2 = Math.PI / 180;
  var ucsCoeff = [1, 7e-3, 0.0228];
  function convertUcsAb(coords) {
    if (coords[1] < 0) {
      coords = hct_default.fromBase(hct_default.toBase(coords));
    }
    const M = Math.log(Math.max(1 + ucsCoeff[2] * coords[1] * viewingConditions2.flRoot, 1)) / ucsCoeff[2];
    const hrad = coords[0] * deg2rad2;
    const a = M * Math.cos(hrad);
    const b2 = M * Math.sin(hrad);
    return [coords[2], a, b2];
  }
  function deltaEHCT_default(color, sample) {
    [color, sample] = getColor([color, sample]);
    let [t1, a1, b1] = convertUcsAb(hct_default.from(color));
    let [t2, a2, b2] = convertUcsAb(hct_default.from(sample));
    return Math.sqrt((t1 - t2) ** 2 + (a1 - a2) ** 2 + (b1 - b2) ** 2);
  }

  // packages/theme/node_modules/colorjs.io/src/deltaE/index.js
  var deltaE_default = {
    deltaE76,
    deltaECMC: deltaECMC_default,
    deltaE2000: deltaE2000_default,
    deltaEJz: deltaEJz_default,
    deltaEITP: deltaEITP_default,
    deltaEOK: deltaEOK_default,
    deltaEOK2: deltaEOK2_default,
    deltaEHCT: deltaEHCT_default
  };

  // packages/theme/node_modules/colorjs.io/src/toGamut.js
  function calcEpsilon(jnd) {
    const order = !jnd ? 0 : Math.floor(Math.log10(Math.abs(jnd)));
    return Math.max(parseFloat(`1e${order - 2}`), 1e-6);
  }
  var GMAPPRESET = {
    hct: {
      method: "hct.c",
      jnd: 2,
      deltaEMethod: "hct",
      blackWhiteClamp: {}
    },
    "hct-tonal": {
      method: "hct.c",
      jnd: 0,
      deltaEMethod: "hct",
      blackWhiteClamp: { channel: "hct.t", min: 0, max: 100 }
    }
  };
  function toGamut(color, {
    method = defaults_default.gamut_mapping,
    space = void 0,
    deltaEMethod = "",
    jnd = 2,
    blackWhiteClamp = void 0
  } = {}) {
    color = getColor(color);
    if (isString(arguments[1])) {
      space = arguments[1];
    } else if (!space) {
      space = color.space;
    }
    space = ColorSpace.get(space);
    if (inGamut(color, space, { epsilon: 0 })) {
      return (
        /** @type {PlainColorObject} */
        color
      );
    }
    let spaceColor;
    if (method === "css") {
      spaceColor = toGamutCSS(color, { space });
    } else {
      if (method !== "clip" && !inGamut(color, space)) {
        if (Object.prototype.hasOwnProperty.call(GMAPPRESET, method)) {
          ({ method, jnd, deltaEMethod, blackWhiteClamp } = GMAPPRESET[method]);
        }
        let de = deltaE2000_default;
        if (deltaEMethod !== "") {
          for (let m in deltaE_default) {
            if ("deltae" + deltaEMethod.toLowerCase() === m.toLowerCase()) {
              de = deltaE_default[m];
              break;
            }
          }
        }
        if (jnd === 0) {
          jnd = 1e-16;
        }
        let clipped = toGamut(to(color, space), { method: "clip", space });
        if (de(color, clipped) > jnd) {
          if (blackWhiteClamp && Object.keys(blackWhiteClamp).length === 3) {
            let channelMeta = ColorSpace.resolveCoord(blackWhiteClamp.channel);
            let channel = get(to(color, channelMeta.space), channelMeta.id);
            if (isNone(channel)) {
              channel = 0;
            }
            if (channel >= blackWhiteClamp.max) {
              return to({ space: "xyz-d65", coords: WHITES["D65"] }, color.space);
            } else if (channel <= blackWhiteClamp.min) {
              return to({ space: "xyz-d65", coords: [0, 0, 0] }, color.space);
            }
          }
          let coordMeta = ColorSpace.resolveCoord(method);
          let mapSpace = coordMeta.space;
          let coordId = coordMeta.id;
          let mappedColor = to(color, mapSpace);
          mappedColor.coords.forEach((c, i) => {
            if (isNone(c)) {
              mappedColor.coords[i] = 0;
            }
          });
          let bounds = coordMeta.range || coordMeta.refRange;
          let min = bounds[0];
          let \u03B56 = calcEpsilon(jnd);
          let low = min;
          let high = get(mappedColor, coordId);
          while (high - low > \u03B56) {
            let clipped2 = clone(mappedColor);
            clipped2 = toGamut(clipped2, { space, method: "clip" });
            let deltaE = de(mappedColor, clipped2);
            if (deltaE - jnd < \u03B56) {
              low = get(mappedColor, coordId);
            } else {
              high = get(mappedColor, coordId);
            }
            set(mappedColor, coordId, (low + high) / 2);
          }
          spaceColor = to(mappedColor, space);
        } else {
          spaceColor = clipped;
        }
      } else {
        spaceColor = to(color, space);
      }
      if (method === "clip" || // Dumb coord clipping
      // finish off smarter gamut mapping with clip to get rid of ε, see #17
      !inGamut(spaceColor, space, { epsilon: 0 })) {
        let bounds = Object.values(space.coords).map((c) => c.range || []);
        spaceColor.coords = /** @type {[number, number, number]} */
        spaceColor.coords.map((c, i) => {
          let [min, max] = bounds[i];
          if (min !== void 0) {
            c = Math.max(min, c);
          }
          if (max !== void 0) {
            c = Math.min(c, max);
          }
          return c;
        });
      }
    }
    if (space !== color.space) {
      spaceColor = to(spaceColor, color.space);
    }
    color.coords = spaceColor.coords;
    return (
      /** @type {PlainColorObject} */
      color
    );
  }
  toGamut.returns = "color";
  var COLORS = {
    WHITE: { space: oklab_default, coords: [1, 0, 0], alpha: 1 },
    BLACK: { space: oklab_default, coords: [0, 0, 0], alpha: 1 }
  };
  function toGamutCSS(origin, { space } = {}) {
    const JND = 0.02;
    const \u03B56 = 1e-4;
    origin = getColor(origin);
    if (!space) {
      space = origin.space;
    }
    space = ColorSpace.get(space);
    const oklchSpace = ColorSpace.get("oklch");
    if (space.isUnbounded) {
      return to(origin, space);
    }
    const origin_OKLCH = to(origin, oklchSpace);
    let L = origin_OKLCH.coords[0];
    if (L >= 1) {
      const white4 = to(COLORS.WHITE, space);
      white4.alpha = origin.alpha;
      return to(white4, space);
    }
    if (L <= 0) {
      const black = to(COLORS.BLACK, space);
      black.alpha = origin.alpha;
      return to(black, space);
    }
    if (inGamut(origin_OKLCH, space, { epsilon: 0 })) {
      return to(origin_OKLCH, space);
    }
    function clip(_color) {
      const destColor = to(_color, space);
      const spaceCoords = Object.values(
        /** @type {ColorSpace} */
        space.coords
      );
      destColor.coords = /** @type {[number, number, number]} */
      destColor.coords.map((coord, index) => {
        if ("range" in spaceCoords[index]) {
          const [min2, max2] = spaceCoords[index].range;
          return clamp(min2, coord, max2);
        }
        return coord;
      });
      return destColor;
    }
    let min = 0;
    let max = origin_OKLCH.coords[1];
    let min_inGamut = true;
    let current = clone(origin_OKLCH);
    let clipped = clip(current);
    let E = deltaEOK_default(clipped, current);
    if (E < JND) {
      return clipped;
    }
    while (max - min > \u03B56) {
      const chroma = (min + max) / 2;
      current.coords[1] = chroma;
      if (min_inGamut && inGamut(current, space, { epsilon: 0 })) {
        min = chroma;
      } else {
        clipped = clip(current);
        E = deltaEOK_default(clipped, current);
        if (E < JND) {
          if (JND - E < \u03B56) {
            break;
          } else {
            min_inGamut = false;
            min = chroma;
          }
        } else {
          max = chroma;
        }
      }
    }
    return clipped;
  }

  // packages/theme/node_modules/colorjs.io/src/to.js
  function to(color, space, { inGamut: inGamut2 } = {}) {
    color = getColor(color);
    space = ColorSpace.get(space);
    let coords = space.from(color);
    let ret = { space, coords, alpha: color.alpha };
    if (inGamut2) {
      ret = toGamut(ret, inGamut2 === true ? void 0 : inGamut2);
    }
    return ret;
  }
  to.returns = "color";

  // packages/theme/node_modules/colorjs.io/src/serialize.js
  function serialize(color, options = {}) {
    let {
      precision = defaults_default.precision,
      format,
      inGamut: inGamut2 = true,
      coords: coordFormat,
      alpha: alphaFormat,
      commas
    } = options;
    let ret;
    let colorWithMeta = (
      /** @type {PlainColorObject & ParseOptions} */
      getColor(color)
    );
    let formatId = format;
    let parseMeta = colorWithMeta.parseMeta;
    if (parseMeta && !format) {
      if (parseMeta.format.canSerialize()) {
        format = parseMeta.format;
        formatId = parseMeta.formatId;
      }
      coordFormat ??= parseMeta.types;
      alphaFormat ??= parseMeta.alphaType;
      commas ??= parseMeta.commas;
    }
    if (formatId) {
      format = colorWithMeta.space.getFormat(format) ?? ColorSpace.findFormat(formatId);
    }
    if (!format) {
      format = colorWithMeta.space.getFormat("default") ?? ColorSpace.DEFAULT_FORMAT;
      formatId = format.name;
    }
    if (format && format.space && format.space !== colorWithMeta.space) {
      colorWithMeta = to(colorWithMeta, format.space);
    }
    let coords = colorWithMeta.coords.slice();
    inGamut2 ||= format.toGamut;
    if (inGamut2 && !inGamut(colorWithMeta)) {
      coords = toGamut(clone(colorWithMeta), inGamut2 === true ? void 0 : inGamut2).coords;
    }
    if (format.type === "custom") {
      if (format.serialize) {
        ret = format.serialize(coords, colorWithMeta.alpha, options);
      } else {
        throw new TypeError(
          `format ${formatId} can only be used to parse colors, not for serialization`
        );
      }
    } else {
      let name = format.name || "color";
      let args = format.serializeCoords(coords, precision, coordFormat);
      if (name === "color") {
        let cssId = format.id || format.ids?.[0] || colorWithMeta.space.cssId || colorWithMeta.space.id;
        args.unshift(cssId);
      }
      let alpha = colorWithMeta.alpha;
      if (alphaFormat !== void 0 && !(typeof alphaFormat === "object")) {
        alphaFormat = typeof alphaFormat === "string" ? { type: alphaFormat } : { include: alphaFormat };
      }
      let alphaType = alphaFormat?.type ?? "<number>";
      let serializeAlpha = alphaFormat?.include === true || format.alpha === true || alphaFormat?.include !== false && format.alpha !== false && alpha < 1;
      let strAlpha = "";
      commas ??= format.commas;
      if (serializeAlpha) {
        if (precision !== null) {
          let unit;
          if (alphaType === "<percentage>") {
            unit = "%";
            alpha *= 100;
          }
          alpha = serializeNumber(alpha, { precision, unit });
        }
        strAlpha = `${commas ? "," : " /"} ${alpha}`;
      }
      ret = `${name}(${args.join(commas ? ", " : " ")}${strAlpha})`;
    }
    return ret;
  }

  // packages/theme/node_modules/colorjs.io/src/spaces/p3-linear.js
  var toXYZ_M = [
    [0.4865709486482162, 0.26566769316909306, 0.1982172852343625],
    [0.2289745640697488, 0.6917385218365064, 0.079286914093745],
    [0, 0.04511338185890264, 1.043944368900976]
  ];
  var fromXYZ_M = [
    [2.493496911941425, -0.9313836179191239, -0.40271078445071684],
    [-0.8294889695615747, 1.7626640603183463, 0.023624685841943577],
    [0.03584583024378447, -0.07617238926804182, 0.9568845240076872]
  ];
  var p3_linear_default = new RGBColorSpace({
    id: "p3-linear",
    cssId: "display-p3-linear",
    name: "Linear P3",
    white: "D65",
    toXYZ_M,
    fromXYZ_M
  });

  // packages/theme/node_modules/colorjs.io/src/spaces/srgb-linear.js
  var toXYZ_M2 = [
    [0.41239079926595934, 0.357584339383878, 0.1804807884018343],
    [0.21263900587151027, 0.715168678767756, 0.07219231536073371],
    [0.01933081871559182, 0.11919477979462598, 0.9505321522496607]
  ];
  var fromXYZ_M2 = [
    [3.2409699419045226, -1.537383177570094, -0.4986107602930034],
    [-0.9692436362808796, 1.8759675015077202, 0.04155505740717559],
    [0.05563007969699366, -0.20397695888897652, 1.0569715142428786]
  ];
  var srgb_linear_default = new RGBColorSpace({
    id: "srgb-linear",
    name: "Linear sRGB",
    white: "D65",
    toXYZ_M: toXYZ_M2,
    fromXYZ_M: fromXYZ_M2
  });

  // packages/theme/node_modules/colorjs.io/src/keywords.js
  var keywords_default = {
    aliceblue: [240 / 255, 248 / 255, 1],
    antiquewhite: [250 / 255, 235 / 255, 215 / 255],
    aqua: [0, 1, 1],
    aquamarine: [127 / 255, 1, 212 / 255],
    azure: [240 / 255, 1, 1],
    beige: [245 / 255, 245 / 255, 220 / 255],
    bisque: [1, 228 / 255, 196 / 255],
    black: [0, 0, 0],
    blanchedalmond: [1, 235 / 255, 205 / 255],
    blue: [0, 0, 1],
    blueviolet: [138 / 255, 43 / 255, 226 / 255],
    brown: [165 / 255, 42 / 255, 42 / 255],
    burlywood: [222 / 255, 184 / 255, 135 / 255],
    cadetblue: [95 / 255, 158 / 255, 160 / 255],
    chartreuse: [127 / 255, 1, 0],
    chocolate: [210 / 255, 105 / 255, 30 / 255],
    coral: [1, 127 / 255, 80 / 255],
    cornflowerblue: [100 / 255, 149 / 255, 237 / 255],
    cornsilk: [1, 248 / 255, 220 / 255],
    crimson: [220 / 255, 20 / 255, 60 / 255],
    cyan: [0, 1, 1],
    darkblue: [0, 0, 139 / 255],
    darkcyan: [0, 139 / 255, 139 / 255],
    darkgoldenrod: [184 / 255, 134 / 255, 11 / 255],
    darkgray: [169 / 255, 169 / 255, 169 / 255],
    darkgreen: [0, 100 / 255, 0],
    darkgrey: [169 / 255, 169 / 255, 169 / 255],
    darkkhaki: [189 / 255, 183 / 255, 107 / 255],
    darkmagenta: [139 / 255, 0, 139 / 255],
    darkolivegreen: [85 / 255, 107 / 255, 47 / 255],
    darkorange: [1, 140 / 255, 0],
    darkorchid: [153 / 255, 50 / 255, 204 / 255],
    darkred: [139 / 255, 0, 0],
    darksalmon: [233 / 255, 150 / 255, 122 / 255],
    darkseagreen: [143 / 255, 188 / 255, 143 / 255],
    darkslateblue: [72 / 255, 61 / 255, 139 / 255],
    darkslategray: [47 / 255, 79 / 255, 79 / 255],
    darkslategrey: [47 / 255, 79 / 255, 79 / 255],
    darkturquoise: [0, 206 / 255, 209 / 255],
    darkviolet: [148 / 255, 0, 211 / 255],
    deeppink: [1, 20 / 255, 147 / 255],
    deepskyblue: [0, 191 / 255, 1],
    dimgray: [105 / 255, 105 / 255, 105 / 255],
    dimgrey: [105 / 255, 105 / 255, 105 / 255],
    dodgerblue: [30 / 255, 144 / 255, 1],
    firebrick: [178 / 255, 34 / 255, 34 / 255],
    floralwhite: [1, 250 / 255, 240 / 255],
    forestgreen: [34 / 255, 139 / 255, 34 / 255],
    fuchsia: [1, 0, 1],
    gainsboro: [220 / 255, 220 / 255, 220 / 255],
    ghostwhite: [248 / 255, 248 / 255, 1],
    gold: [1, 215 / 255, 0],
    goldenrod: [218 / 255, 165 / 255, 32 / 255],
    gray: [128 / 255, 128 / 255, 128 / 255],
    green: [0, 128 / 255, 0],
    greenyellow: [173 / 255, 1, 47 / 255],
    grey: [128 / 255, 128 / 255, 128 / 255],
    honeydew: [240 / 255, 1, 240 / 255],
    hotpink: [1, 105 / 255, 180 / 255],
    indianred: [205 / 255, 92 / 255, 92 / 255],
    indigo: [75 / 255, 0, 130 / 255],
    ivory: [1, 1, 240 / 255],
    khaki: [240 / 255, 230 / 255, 140 / 255],
    lavender: [230 / 255, 230 / 255, 250 / 255],
    lavenderblush: [1, 240 / 255, 245 / 255],
    lawngreen: [124 / 255, 252 / 255, 0],
    lemonchiffon: [1, 250 / 255, 205 / 255],
    lightblue: [173 / 255, 216 / 255, 230 / 255],
    lightcoral: [240 / 255, 128 / 255, 128 / 255],
    lightcyan: [224 / 255, 1, 1],
    lightgoldenrodyellow: [250 / 255, 250 / 255, 210 / 255],
    lightgray: [211 / 255, 211 / 255, 211 / 255],
    lightgreen: [144 / 255, 238 / 255, 144 / 255],
    lightgrey: [211 / 255, 211 / 255, 211 / 255],
    lightpink: [1, 182 / 255, 193 / 255],
    lightsalmon: [1, 160 / 255, 122 / 255],
    lightseagreen: [32 / 255, 178 / 255, 170 / 255],
    lightskyblue: [135 / 255, 206 / 255, 250 / 255],
    lightslategray: [119 / 255, 136 / 255, 153 / 255],
    lightslategrey: [119 / 255, 136 / 255, 153 / 255],
    lightsteelblue: [176 / 255, 196 / 255, 222 / 255],
    lightyellow: [1, 1, 224 / 255],
    lime: [0, 1, 0],
    limegreen: [50 / 255, 205 / 255, 50 / 255],
    linen: [250 / 255, 240 / 255, 230 / 255],
    magenta: [1, 0, 1],
    maroon: [128 / 255, 0, 0],
    mediumaquamarine: [102 / 255, 205 / 255, 170 / 255],
    mediumblue: [0, 0, 205 / 255],
    mediumorchid: [186 / 255, 85 / 255, 211 / 255],
    mediumpurple: [147 / 255, 112 / 255, 219 / 255],
    mediumseagreen: [60 / 255, 179 / 255, 113 / 255],
    mediumslateblue: [123 / 255, 104 / 255, 238 / 255],
    mediumspringgreen: [0, 250 / 255, 154 / 255],
    mediumturquoise: [72 / 255, 209 / 255, 204 / 255],
    mediumvioletred: [199 / 255, 21 / 255, 133 / 255],
    midnightblue: [25 / 255, 25 / 255, 112 / 255],
    mintcream: [245 / 255, 1, 250 / 255],
    mistyrose: [1, 228 / 255, 225 / 255],
    moccasin: [1, 228 / 255, 181 / 255],
    navajowhite: [1, 222 / 255, 173 / 255],
    navy: [0, 0, 128 / 255],
    oldlace: [253 / 255, 245 / 255, 230 / 255],
    olive: [128 / 255, 128 / 255, 0],
    olivedrab: [107 / 255, 142 / 255, 35 / 255],
    orange: [1, 165 / 255, 0],
    orangered: [1, 69 / 255, 0],
    orchid: [218 / 255, 112 / 255, 214 / 255],
    palegoldenrod: [238 / 255, 232 / 255, 170 / 255],
    palegreen: [152 / 255, 251 / 255, 152 / 255],
    paleturquoise: [175 / 255, 238 / 255, 238 / 255],
    palevioletred: [219 / 255, 112 / 255, 147 / 255],
    papayawhip: [1, 239 / 255, 213 / 255],
    peachpuff: [1, 218 / 255, 185 / 255],
    peru: [205 / 255, 133 / 255, 63 / 255],
    pink: [1, 192 / 255, 203 / 255],
    plum: [221 / 255, 160 / 255, 221 / 255],
    powderblue: [176 / 255, 224 / 255, 230 / 255],
    purple: [128 / 255, 0, 128 / 255],
    rebeccapurple: [102 / 255, 51 / 255, 153 / 255],
    red: [1, 0, 0],
    rosybrown: [188 / 255, 143 / 255, 143 / 255],
    royalblue: [65 / 255, 105 / 255, 225 / 255],
    saddlebrown: [139 / 255, 69 / 255, 19 / 255],
    salmon: [250 / 255, 128 / 255, 114 / 255],
    sandybrown: [244 / 255, 164 / 255, 96 / 255],
    seagreen: [46 / 255, 139 / 255, 87 / 255],
    seashell: [1, 245 / 255, 238 / 255],
    sienna: [160 / 255, 82 / 255, 45 / 255],
    silver: [192 / 255, 192 / 255, 192 / 255],
    skyblue: [135 / 255, 206 / 255, 235 / 255],
    slateblue: [106 / 255, 90 / 255, 205 / 255],
    slategray: [112 / 255, 128 / 255, 144 / 255],
    slategrey: [112 / 255, 128 / 255, 144 / 255],
    snow: [1, 250 / 255, 250 / 255],
    springgreen: [0, 1, 127 / 255],
    steelblue: [70 / 255, 130 / 255, 180 / 255],
    tan: [210 / 255, 180 / 255, 140 / 255],
    teal: [0, 128 / 255, 128 / 255],
    thistle: [216 / 255, 191 / 255, 216 / 255],
    tomato: [1, 99 / 255, 71 / 255],
    turquoise: [64 / 255, 224 / 255, 208 / 255],
    violet: [238 / 255, 130 / 255, 238 / 255],
    wheat: [245 / 255, 222 / 255, 179 / 255],
    white: [1, 1, 1],
    whitesmoke: [245 / 255, 245 / 255, 245 / 255],
    yellow: [1, 1, 0],
    yellowgreen: [154 / 255, 205 / 255, 50 / 255]
  };

  // packages/theme/node_modules/colorjs.io/src/spaces/srgb.js
  var coordGrammar = Array(3).fill("<percentage> | <number>[0, 255]");
  var coordGrammarNumber = Array(3).fill("<number>[0, 255]");
  var srgb_default = new RGBColorSpace({
    id: "srgb",
    name: "sRGB",
    base: srgb_linear_default,
    fromBase: (rgb) => {
      return rgb.map((val) => {
        let sign = val < 0 ? -1 : 1;
        let abs = val * sign;
        if (abs > 31308e-7) {
          return sign * (1.055 * abs ** (1 / 2.4) - 0.055);
        }
        return 12.92 * val;
      });
    },
    toBase: (rgb) => {
      return rgb.map((val) => {
        let sign = val < 0 ? -1 : 1;
        let abs = val * sign;
        if (abs <= 0.04045) {
          return val / 12.92;
        }
        return sign * ((abs + 0.055) / 1.055) ** 2.4;
      });
    },
    formats: {
      rgb: {
        coords: coordGrammar
      },
      rgb_number: {
        name: "rgb",
        commas: true,
        coords: coordGrammarNumber,
        alpha: false
      },
      color: {
        /* use defaults */
      },
      rgba: {
        coords: coordGrammar,
        commas: true,
        alpha: true
      },
      rgba_number: {
        name: "rgba",
        commas: true,
        coords: coordGrammarNumber
      },
      hex: {
        type: "custom",
        toGamut: true,
        test: (str) => /^#(([a-f0-9]{2}){3,4}|[a-f0-9]{3,4})$/i.test(str),
        parse(str) {
          if (str.length <= 5) {
            str = str.replace(/[a-f0-9]/gi, "$&$&");
          }
          let rgba = [];
          str.replace(/[a-f0-9]{2}/gi, (component) => {
            rgba.push(parseInt(component, 16) / 255);
          });
          return {
            spaceId: "srgb",
            coords: (
              /** @type {Coords} */
              rgba.slice(0, 3)
            ),
            alpha: (
              /** @type {number} */
              rgba.slice(3)[0]
            )
          };
        },
        serialize: (coords, alpha, {
          collapse = true,
          // collapse to 3-4 digit hex when possible?
          alpha: alphaFormat
        } = {}) => {
          if (alphaFormat !== false && alpha < 1 || alphaFormat === true) {
            coords.push(alpha);
          }
          coords = /** @type {[number, number, number]} */
          coords.map((c) => Math.round(c * 255));
          let collapsible = collapse && coords.every((c) => c % 17 === 0);
          let hex = coords.map((c) => {
            if (collapsible) {
              return (c / 17).toString(16);
            }
            return c.toString(16).padStart(2, "0");
          }).join("");
          return "#" + hex;
        }
      },
      keyword: {
        type: "custom",
        test: (str) => /^[a-z]+$/i.test(str),
        parse(str) {
          str = str.toLowerCase();
          let ret = { spaceId: "srgb", coords: null, alpha: 1 };
          if (str === "transparent") {
            ret.coords = keywords_default.black;
            ret.alpha = 0;
          } else {
            ret.coords = keywords_default[str];
          }
          if (ret.coords) {
            return ret;
          }
        }
      }
    }
  });

  // packages/theme/node_modules/colorjs.io/src/spaces/p3.js
  var p3_default = new RGBColorSpace({
    id: "p3",
    cssId: "display-p3",
    name: "P3",
    base: p3_linear_default,
    // Gamma encoding/decoding is the same as sRGB
    fromBase: srgb_default.fromBase,
    toBase: srgb_default.toBase
  });

  // packages/theme/node_modules/colorjs.io/src/luminance.js
  function getLuminance(color) {
    return get(color, [xyz_d65_default, "y"]);
  }

  // packages/theme/node_modules/colorjs.io/src/contrast/WCAG21.js
  function contrastWCAG21(color1, color2) {
    color1 = getColor(color1);
    color2 = getColor(color2);
    let Y1 = Math.max(getLuminance(color1), 0);
    let Y2 = Math.max(getLuminance(color2), 0);
    if (Y2 > Y1) {
      [Y1, Y2] = [Y2, Y1];
    }
    return (Y1 + 0.05) / (Y2 + 0.05);
  }

  // packages/theme/node_modules/colorjs.io/src/spaces/hsl.js
  var hsl_default = new ColorSpace({
    id: "hsl",
    name: "HSL",
    coords: {
      h: {
        refRange: [0, 360],
        type: "angle",
        name: "Hue"
      },
      s: {
        range: [0, 100],
        name: "Saturation"
      },
      l: {
        range: [0, 100],
        name: "Lightness"
      }
    },
    base: srgb_default,
    // Adapted from https://drafts.csswg.org/css-color-4/better-rgbToHsl.js
    fromBase: (rgb) => {
      let max = Math.max(...rgb);
      let min = Math.min(...rgb);
      let [r, g2, b2] = rgb;
      let [h, s, l] = [null, 0, (min + max) / 2];
      let d2 = max - min;
      if (d2 !== 0) {
        s = l === 0 || l === 1 ? 0 : (max - l) / Math.min(l, 1 - l);
        switch (max) {
          case r:
            h = (g2 - b2) / d2 + (g2 < b2 ? 6 : 0);
            break;
          case g2:
            h = (b2 - r) / d2 + 2;
            break;
          case b2:
            h = (r - g2) / d2 + 4;
        }
        h = h * 60;
      }
      if (s < 0) {
        h += 180;
        s = Math.abs(s);
      }
      if (h >= 360) {
        h -= 360;
      }
      return [h, s * 100, l * 100];
    },
    // Adapted from https://en.wikipedia.org/wiki/HSL_and_HSV#HSL_to_RGB_alternative
    toBase: (hsl) => {
      let [h, s, l] = hsl;
      h = h % 360;
      if (h < 0) {
        h += 360;
      }
      s /= 100;
      l /= 100;
      function f(n2) {
        let k = (n2 + h / 30) % 12;
        let a = s * Math.min(l, 1 - l);
        return l - a * Math.max(-1, Math.min(k - 3, 9 - k, 1));
      }
      return [f(0), f(8), f(4)];
    },
    formats: {
      hsl: {
        coords: ["<number> | <angle>", "<percentage> | <number>", "<percentage> | <number>"]
      },
      hsla: {
        coords: ["<number> | <angle>", "<percentage> | <number>", "<percentage> | <number>"],
        commas: true,
        alpha: true
      }
    }
  });

  // packages/theme/node_modules/colorjs.io/src/spaces/oklch.js
  var oklch_default = new ColorSpace({
    id: "oklch",
    name: "OkLCh",
    coords: {
      l: {
        refRange: [0, 1],
        name: "Lightness"
      },
      c: {
        refRange: [0, 0.4],
        name: "Chroma"
      },
      h: {
        refRange: [0, 360],
        type: "angle",
        name: "Hue"
      }
    },
    white: "D65",
    base: oklab_default,
    fromBase: lch_default.fromBase,
    toBase: lch_default.toBase,
    formats: {
      oklch: {
        coords: ["<percentage> | <number>", "<number> | <percentage>", "<number> | <angle>"]
      }
    }
  });

  // node_modules/memize/dist/index.js
  function memize(fn, options) {
    var size = 0;
    var head;
    var tail;
    options = options || {};
    function memoized() {
      var node = head, len = arguments.length, args, i;
      searchCache: while (node) {
        if (node.args.length !== arguments.length) {
          node = node.next;
          continue;
        }
        for (i = 0; i < len; i++) {
          if (node.args[i] !== arguments[i]) {
            node = node.next;
            continue searchCache;
          }
        }
        if (node !== head) {
          if (node === tail) {
            tail = node.prev;
          }
          node.prev.next = node.next;
          if (node.next) {
            node.next.prev = node.prev;
          }
          node.next = head;
          node.prev = null;
          head.prev = node;
          head = node;
        }
        return node.val;
      }
      args = new Array(len);
      for (i = 0; i < len; i++) {
        args[i] = arguments[i];
      }
      node = {
        args,
        // Generate the result from original function
        val: fn.apply(null, args)
      };
      if (head) {
        head.prev = node;
        node.next = head;
      } else {
        tail = node;
      }
      if (size === /** @type {MemizeOptions} */
      options.maxSize) {
        tail = /** @type {MemizeCacheNode} */
        tail.prev;
        tail.next = null;
      } else {
        size++;
      }
      head = node;
      return node.val;
    }
    memoized.clear = function() {
      head = null;
      tail = null;
      size = 0;
    };
    return memoized;
  }

  // packages/theme/build-module/use-theme-provider-styles.mjs
  var import_element2 = __toESM(require_element(), 1);

  // packages/theme/build-module/color-ramps/lib/register-color-spaces.mjs
  ColorSpace.register(srgb_default);
  ColorSpace.register(oklch_default);
  ColorSpace.register(p3_default);
  ColorSpace.register(hsl_default);

  // packages/theme/build-module/prebuilt/ts/color-tokens.mjs
  var color_tokens_default = {
    "primary-bgFill1": ["bg-interactive-brand-strong"],
    "primary-fgFill": [
      "fg-interactive-brand-strong",
      "fg-interactive-brand-strong-active"
    ],
    "primary-bgFill2": ["bg-interactive-brand-strong-active"],
    "primary-surface4": ["bg-interactive-brand-weak-active"],
    "primary-fgSurface3": [
      "fg-interactive-brand",
      "fg-interactive-brand-active"
    ],
    "primary-stroke3": [
      "bg-thumb-brand",
      "bg-thumb-brand-active",
      "stroke-focus-brand",
      "stroke-interactive-brand",
      "stroke-surface-brand-strong"
    ],
    "primary-stroke4": ["stroke-interactive-brand-active"],
    "primary-stroke1": ["stroke-surface-brand"],
    "primary-surface1": ["bg-surface-brand"],
    "info-surface2": ["bg-surface-info-weak"],
    "info-surface4": ["bg-surface-info"],
    "info-fgSurface4": ["fg-content-info"],
    "info-fgSurface3": ["fg-content-info-weak"],
    "info-stroke3": ["stroke-surface-info-strong"],
    "info-stroke1": ["stroke-surface-info"],
    "success-surface2": ["bg-surface-success-weak"],
    "success-surface4": ["bg-surface-success"],
    "success-fgSurface4": ["fg-content-success"],
    "success-fgSurface3": ["fg-content-success-weak"],
    "success-stroke3": ["stroke-surface-success-strong"],
    "success-stroke1": ["stroke-surface-success"],
    "warning-surface2": ["bg-surface-warning-weak"],
    "warning-surface4": ["bg-surface-warning"],
    "warning-fgSurface4": ["fg-content-warning"],
    "warning-fgSurface3": ["fg-content-warning-weak"],
    "warning-stroke3": ["stroke-surface-warning-strong"],
    "warning-stroke1": ["stroke-surface-warning"],
    "error-bgFill1": ["bg-interactive-error-strong"],
    "error-fgFill": [
      "fg-interactive-error-strong",
      "fg-interactive-error-strong-active"
    ],
    "error-bgFill2": ["bg-interactive-error-strong-active"],
    "error-surface2": [
      "bg-interactive-error-active",
      "bg-surface-error-weak"
    ],
    "error-surface4": [
      "bg-interactive-error-weak-active",
      "bg-surface-error"
    ],
    "error-fgSurface4": ["fg-content-error"],
    "error-fgSurface3": [
      "fg-content-error-weak",
      "fg-interactive-error",
      "fg-interactive-error-active"
    ],
    "error-stroke3": [
      "stroke-interactive-error",
      "stroke-interactive-error-strong",
      "stroke-surface-error-strong"
    ],
    "error-stroke4": ["stroke-interactive-error-active"],
    "error-stroke1": ["stroke-surface-error"],
    "bg-surface2": ["bg-surface-neutral"],
    "bg-surface5": ["bg-interactive-neutral-strong-disabled"],
    "bg-surface4": ["bg-interactive-neutral-weak-active"],
    "bg-surface3": ["bg-surface-neutral-strong"],
    "bg-fgSurface4": [
      "fg-content-neutral",
      "fg-interactive-neutral",
      "fg-interactive-neutral-active"
    ],
    "bg-fgSurface3": [
      "fg-content-neutral-weak",
      "fg-interactive-neutral-weak"
    ],
    "bg-fgSurface2": [
      "fg-interactive-neutral-disabled",
      "fg-interactive-neutral-strong-disabled",
      "fg-interactive-neutral-weak-disabled"
    ],
    "bg-stroke3": [
      "bg-thumb-neutral-weak",
      "stroke-interactive-neutral",
      "stroke-surface-neutral-strong"
    ],
    "bg-stroke4": [
      "bg-thumb-neutral-weak-active",
      "stroke-interactive-neutral-active",
      "stroke-interactive-neutral-strong"
    ],
    "bg-stroke2": [
      "bg-thumb-neutral-disabled",
      "bg-track-neutral",
      "stroke-interactive-neutral-disabled",
      "stroke-surface-neutral"
    ],
    "bg-stroke1": ["bg-track-neutral-weak", "stroke-surface-neutral-weak"],
    "bg-bgFillInverted2": ["bg-interactive-neutral-strong-active"],
    "bg-bgFillInverted1": ["bg-interactive-neutral-strong"],
    "bg-fgFillInverted": [
      "fg-interactive-neutral-strong",
      "fg-interactive-neutral-strong-active"
    ],
    "bg-surface1": ["bg-surface-neutral-weak"],
    "caution-surface2": ["bg-surface-caution-weak"],
    "caution-surface4": ["bg-surface-caution"],
    "caution-fgSurface4": ["fg-content-caution"],
    "caution-fgSurface3": ["fg-content-caution-weak"]
  };

  // packages/theme/build-module/color-ramps/lib/color-utils.mjs
  function getColorString(color) {
    const rgbRounded = serialize(to(color, srgb_default));
    return serialize(rgbRounded, { format: "hex" });
  }
  function getContrast(colorA, colorB) {
    return contrastWCAG21(colorA, colorB);
  }
  function clampToGamut(c) {
    return to(toGamut(c, { space: srgb_default, method: "css" }), oklch_default);
  }

  // packages/theme/build-module/color-ramps/lib/constants.mjs
  var WHITE = to("white", oklch_default);
  var BLACK = to("black", oklch_default);
  var UNIVERSAL_CONTRAST_TOPUP = 0.02;
  var WHITE_TEXT_CONTRAST_MARGIN = 3.1;
  var ACCENT_SCALE_BASE_LIGHTNESS_THRESHOLDS = {
    lighter: { min: 0.2, max: 0.4 },
    darker: { min: 0.75, max: 0.98 }
  };
  var CONTRAST_EPSILON = 4e-3;
  var MAX_BISECTION_ITERATIONS = 10;
  var DEFAULT_SEED_COLORS = {
    bg: "#f8f8f8",
    primary: "#3858e9",
    info: "#0090ff",
    success: "#4ab866",
    caution: "#f0d149",
    warning: "#f0b849",
    error: "#cc1818"
  };

  // packages/theme/build-module/color-ramps/lib/utils.mjs
  function buildDependencyGraph(config) {
    const dependencies = /* @__PURE__ */ new Map();
    const dependents = /* @__PURE__ */ new Map();
    Object.keys(config).forEach((step) => {
      dependencies.set(step, []);
    });
    dependents.set("seed", []);
    Object.keys(config).forEach((step) => {
      dependents.set(step, []);
    });
    Object.entries(config).forEach(([stepName, stepConfig]) => {
      const step = stepName;
      const reference = stepConfig.contrast.reference;
      dependencies.get(step).push(reference);
      dependents.get(reference).push(step);
      if (stepConfig.sameAsIfPossible) {
        dependencies.get(step).push(stepConfig.sameAsIfPossible);
        dependents.get(stepConfig.sameAsIfPossible).push(step);
      }
    });
    return { dependencies, dependents };
  }
  function sortByDependency(config) {
    const { dependents } = buildDependencyGraph(config);
    const result = [];
    const visited = /* @__PURE__ */ new Set();
    const visiting = /* @__PURE__ */ new Set();
    function visit(node) {
      if (visiting.has(node)) {
        throw new Error(
          `Circular dependency detected involving step: ${String(
            node
          )}`
        );
      }
      if (visited.has(node)) {
        return;
      }
      visiting.add(node);
      const nodeDependents = dependents.get(node) || [];
      nodeDependents.forEach((dependent) => {
        visit(dependent);
      });
      visiting.delete(node);
      visited.add(node);
      if (node !== "seed") {
        result.unshift(node);
      }
    }
    visit("seed");
    return result;
  }
  function stepsForStep(stepName, config) {
    const result = /* @__PURE__ */ new Set();
    function visit(step) {
      if (step === "seed" || result.has(step)) {
        return;
      }
      const stepConfig = config[step];
      if (!stepConfig) {
        return;
      }
      visit(stepConfig.contrast.reference);
      if (stepConfig.sameAsIfPossible) {
        visit(stepConfig.sameAsIfPossible);
      }
      result.add(step);
    }
    visit(stepName);
    return Array.from(result);
  }
  function computeBetterFgColorDirection(seed, preferLighter) {
    const contrastAgainstBlack = getContrast(seed, BLACK);
    const contrastAgainstWhite = getContrast(seed, WHITE);
    return contrastAgainstBlack > contrastAgainstWhite + (preferLighter ? WHITE_TEXT_CONTRAST_MARGIN : 0) ? { better: "darker", worse: "lighter" } : { better: "lighter", worse: "darker" };
  }
  function adjustContrastTarget(target) {
    if (target === 1) {
      return 1;
    }
    return target + UNIVERSAL_CONTRAST_TOPUP;
  }
  function clampAccentScaleReferenceLightness(rawLightness, direction) {
    const thresholds = ACCENT_SCALE_BASE_LIGHTNESS_THRESHOLDS[direction];
    return Math.max(thresholds.min, Math.min(thresholds.max, rawLightness));
  }
  function solveWithBisect(calculateC, calculateValue, initLowerL, initLowerValue, initUpperL, initUpperValue) {
    let lowerL = initLowerL;
    let lowerValue = initLowerValue;
    let lowerReplaced = false;
    let upperL = initUpperL;
    let upperValue = initUpperValue;
    let upperReplaced = false;
    let bestC;
    let bestValue;
    let iterations = 0;
    while (true) {
      iterations++;
      const newL = (lowerL * upperValue - upperL * lowerValue) / (upperValue - lowerValue);
      bestC = calculateC(newL);
      bestValue = calculateValue(bestC);
      if (Math.abs(bestValue) <= CONTRAST_EPSILON || iterations >= MAX_BISECTION_ITERATIONS) {
        break;
      }
      if (bestValue <= 0) {
        lowerL = newL;
        lowerValue = bestValue;
        if (lowerReplaced) {
          upperValue /= 2;
        }
        lowerReplaced = true;
        upperReplaced = false;
      } else {
        upperL = newL;
        upperValue = bestValue;
        if (upperReplaced) {
          lowerValue /= 2;
        }
        upperReplaced = true;
        lowerReplaced = false;
      }
    }
    return bestC;
  }

  // packages/theme/build-module/color-ramps/lib/taper-chroma.mjs
  function taperChroma(seed, lTarget, options = {}) {
    const gamut = options.gamut ?? srgb_default;
    const alpha = options.alpha ?? 0.65;
    const carry = options.carry ?? 0.5;
    const cUpperBound = options.cUpperBound ?? 0.45;
    const radiusLight = options.radiusLight ?? 0.2;
    const radiusDark = options.radiusDark ?? 0.2;
    const kLight = options.kLight ?? 0.85;
    const kDark = options.kDark ?? 0.85;
    const achromaEpsilon = options.achromaEpsilon ?? 5e-3;
    const cSeed = Math.max(0, get(seed, [oklch_default, "c"]));
    let hSeed = get(seed, [oklch_default, "h"]);
    const chromaIsTiny = cSeed < achromaEpsilon;
    const hueIsInvalid = hSeed === null || !Number.isFinite(hSeed);
    if (chromaIsTiny || hueIsInvalid) {
      if (typeof options.hueFallback === "number") {
        hSeed = normalizeHue(options.hueFallback);
      } else {
        return {
          space: oklch_default,
          coords: [clamp01(lTarget), 0, 0],
          alpha: 1
        };
      }
    }
    const lSeed = clamp01(get(seed, [oklch_default, "l"]));
    const cmaxSeed = getCachedMaxChromaAtLH(lSeed, hSeed, gamut, cUpperBound);
    const cmaxTarget = getCachedMaxChromaAtLH(
      clamp01(lTarget),
      hSeed,
      gamut,
      cUpperBound
    );
    let seedRelative = 0;
    const denom = cmaxSeed > 0 ? cmaxSeed : 1e-6;
    seedRelative = clamp01(cSeed / denom);
    const cIntendedBase = alpha * cmaxTarget;
    const cWithCarry = cIntendedBase * Math.pow(seedRelative, clamp01(carry));
    const t = continuousTaper(lSeed, lTarget, {
      radiusLight,
      radiusDark,
      kLight,
      kDark
    });
    const cPlanned = cWithCarry * t;
    const lOut = clamp01(lTarget);
    return { l: lOut, c: cPlanned };
  }
  function clamp01(x) {
    if (x < 0) {
      return 0;
    }
    if (x > 1) {
      return 1;
    }
    return x;
  }
  function normalizeHue(h) {
    let hue = h % 360;
    if (hue < 0) {
      hue += 360;
    }
    return hue;
  }
  function raisedCosine(u) {
    const x = clamp01(u);
    return 0.5 - 0.5 * Math.cos(Math.PI * x);
  }
  function continuousTaper(seedL, targetL, opts) {
    const d2 = targetL - seedL;
    if (d2 >= 0) {
      const u2 = opts.radiusLight > 0 ? Math.abs(d2) / opts.radiusLight : 1;
      const w2 = raisedCosine(u2 > 1 ? 1 : u2);
      return 1 - (1 - opts.kLight) * w2;
    }
    const u = opts.radiusDark > 0 ? Math.abs(d2) / opts.radiusDark : 1;
    const w = raisedCosine(u > 1 ? 1 : u);
    return 1 - (1 - opts.kDark) * w;
  }
  var maxChromaCache = /* @__PURE__ */ new Map();
  function keyMax(l, h, gamut, cap) {
    const lq = quantize(l, 0.05);
    const hq = quantize(normalizeHue(h), 10);
    const cq = quantize(cap, 0.05);
    return `${gamut}|L:${lq}|H:${hq}|cap:${cq}`;
  }
  function quantize(x, step) {
    const k = Math.round(x / step);
    return k * step;
  }
  function getCachedMaxChromaAtLH(l, h, gamutSpace, cap) {
    const gamut = gamutSpace.id;
    const key = keyMax(l, h, gamut, cap);
    const hit = maxChromaCache.get(key);
    if (typeof hit === "number") {
      return hit;
    }
    const computed = maxInGamutChromaAtLH(l, h, gamutSpace, cap);
    maxChromaCache.set(key, computed);
    return computed;
  }
  function maxInGamutChromaAtLH(l, h, gamutSpace, cap) {
    const probe = {
      space: oklch_default,
      coords: [l, cap, h],
      alpha: 1
    };
    const clamped = toGamut(probe, { space: gamutSpace, method: "css" });
    return get(clamped, [oklch_default, "c"]);
  }

  // packages/theme/build-module/color-ramps/lib/find-color-with-constraints.mjs
  function cdiff(c13, c23) {
    return Math.log(c13 / c23);
  }
  function findColorMeetingRequirements(reference, seed, target, direction, {
    lightnessConstraint,
    taperChromaOptions
  } = {}) {
    if (target <= 1) {
      return {
        color: reference,
        reached: true,
        achieved: 1
      };
    }
    function getColorForL(l) {
      let newL = l;
      let newC = get(seed, [oklch_default, "c"]);
      if (taperChromaOptions) {
        const tapered = taperChroma(seed, newL, taperChromaOptions);
        if ("l" in tapered && "c" in tapered) {
          newL = tapered.l;
          newC = tapered.c;
        } else {
          return tapered;
        }
      }
      return clampToGamut({
        spaceId: "oklch",
        coords: [newL, newC, get(seed, [oklch_default, "h"])]
      });
    }
    const mostContrastingL = direction === "lighter" ? 1 : 0;
    const mostContrastingColor = direction === "lighter" ? WHITE : BLACK;
    const highestContrast = getContrast(reference, mostContrastingColor);
    if (lightnessConstraint) {
      const colorWithExactL = getColorForL(lightnessConstraint.value);
      const exactLContrast = getContrast(reference, colorWithExactL);
      const exactLContrastMeetsTarget = cdiff(exactLContrast, target) >= -CONTRAST_EPSILON;
      if (exactLContrastMeetsTarget || lightnessConstraint.type === "force") {
        return {
          color: colorWithExactL,
          reached: exactLContrastMeetsTarget,
          achieved: exactLContrast,
          deficit: exactLContrastMeetsTarget ? cdiff(exactLContrast, highestContrast) : cdiff(target, exactLContrast)
        };
      }
    }
    if (cdiff(highestContrast, target) <= CONTRAST_EPSILON) {
      return {
        color: mostContrastingColor,
        reached: cdiff(highestContrast, target) >= -CONTRAST_EPSILON,
        achieved: highestContrast,
        deficit: cdiff(target, highestContrast)
      };
    }
    const lowerL = get(reference, [oklch_default, "l"]);
    const lowerContrast = cdiff(1, target);
    const upperL = mostContrastingL;
    const upperContrast = cdiff(highestContrast, target);
    const bestColor = solveWithBisect(
      getColorForL,
      (c) => cdiff(getContrast(reference, c), target),
      lowerL,
      lowerContrast,
      upperL,
      upperContrast
    );
    return {
      color: bestColor,
      reached: true,
      achieved: target,
      // Negative number that specifies how much room we have.
      deficit: cdiff(target, highestContrast)
    };
  }

  // packages/theme/build-module/color-ramps/lib/index.mjs
  function calculateRamp({
    seed,
    sortedSteps,
    config,
    mainDir,
    oppDir,
    pinLightness
  }) {
    const rampResults = {};
    let warnings;
    let maxDeficit = -Infinity;
    let maxDeficitDirection = "lighter";
    let maxDeficitStep;
    const calculatedColors = /* @__PURE__ */ new Map();
    calculatedColors.set("seed", seed);
    for (const stepName of sortedSteps) {
      let computeDirection = function(color, followDirection) {
        if (followDirection === "main") {
          return mainDir;
        }
        if (followDirection === "opposite") {
          return oppDir;
        }
        if (followDirection === "best") {
          return computeBetterFgColorDirection(
            color,
            contrast.preferLighter
          ).better;
        }
        return followDirection;
      };
      const {
        contrast,
        lightness: stepLightnessConstraint,
        taperChromaOptions,
        sameAsIfPossible
      } = config[stepName];
      const referenceColor = calculatedColors.get(contrast.reference);
      if (!referenceColor) {
        throw new Error(
          `Reference color for step ${stepName} not found: ${contrast.reference}`
        );
      }
      if (sameAsIfPossible) {
        const candidateColor = calculatedColors.get(sameAsIfPossible);
        if (!candidateColor) {
          throw new Error(
            `Same-as color for step ${stepName} not found: ${sameAsIfPossible}`
          );
        }
        const candidateContrast = getContrast(
          referenceColor,
          candidateColor
        );
        const adjustedTarget2 = adjustContrastTarget(contrast.target);
        if (candidateContrast >= adjustedTarget2) {
          calculatedColors.set(stepName, candidateColor);
          rampResults[stepName] = getColorString(candidateColor);
          continue;
        }
      }
      const computedDir = computeDirection(
        referenceColor,
        contrast.followDirection
      );
      const adjustedTarget = adjustContrastTarget(contrast.target);
      let lightnessConstraint;
      if (pinLightness?.stepName === stepName) {
        lightnessConstraint = {
          value: pinLightness.value,
          type: "force"
        };
      } else if (stepLightnessConstraint) {
        lightnessConstraint = {
          value: stepLightnessConstraint(computedDir),
          type: "onlyIfSucceeds"
        };
      }
      const searchResults = findColorMeetingRequirements(
        referenceColor,
        seed,
        adjustedTarget,
        computedDir,
        {
          lightnessConstraint,
          taperChromaOptions
        }
      );
      if (!contrast.ignoreWhenAdjustingSeed && searchResults.deficit && searchResults.deficit > maxDeficit) {
        maxDeficit = searchResults.deficit;
        maxDeficitDirection = computedDir;
        maxDeficitStep = stepName;
      }
      calculatedColors.set(stepName, searchResults.color);
      rampResults[stepName] = getColorString(searchResults.color);
      if (!searchResults.reached && !contrast.ignoreWhenAdjustingSeed) {
        warnings ??= [];
        warnings.push(stepName);
      }
    }
    return {
      rampResults,
      warnings,
      maxDeficit,
      maxDeficitDirection,
      maxDeficitStep
    };
  }
  function buildRamp(seedArg, config, {
    mainDirection,
    pinLightness,
    rescaleToFitContrastTargets = true
  } = {}) {
    let seed;
    try {
      seed = clampToGamut(seedArg);
    } catch (error) {
      throw new Error(
        `Invalid seed color "${seedArg}": ${error instanceof Error ? error.message : "Unknown error"}`
      );
    }
    let mainDir = "lighter";
    let oppDir = "darker";
    if (mainDirection) {
      mainDir = mainDirection;
      oppDir = mainDirection === "darker" ? "lighter" : "darker";
    } else {
      const { better, worse } = computeBetterFgColorDirection(seed);
      mainDir = better;
      oppDir = worse;
    }
    const sortedSteps = sortByDependency(config);
    const {
      rampResults,
      warnings,
      maxDeficit,
      maxDeficitDirection,
      maxDeficitStep
    } = calculateRamp({
      seed,
      sortedSteps,
      config,
      mainDir,
      oppDir,
      pinLightness
    });
    let bestRamp = rampResults;
    if (maxDeficit > CONTRAST_EPSILON && rescaleToFitContrastTargets) {
      let getSeedForL = function(l) {
        return clampToGamut(set(clone(seed), [oklch_default, "l"], l));
      }, getDeficitForSeed = function(s) {
        const iterationResults = calculateRamp({
          seed: s,
          sortedSteps: iterSteps,
          config,
          mainDir,
          oppDir,
          pinLightness
        });
        return iterationResults.maxDeficitDirection === maxDeficitDirection ? iterationResults.maxDeficit : -maxDeficit;
      };
      const iterSteps = stepsForStep(maxDeficitStep, config);
      const lowerSeedL = maxDeficitDirection === "lighter" ? 0 : 1;
      const lowerDeficit = -maxDeficit;
      const upperSeedL = get(seed, [oklch_default, "l"]);
      const upperDeficit = maxDeficit;
      const bestSeed = solveWithBisect(
        getSeedForL,
        getDeficitForSeed,
        lowerSeedL,
        lowerDeficit,
        upperSeedL,
        upperDeficit
      );
      bestRamp = calculateRamp({
        seed: bestSeed,
        sortedSteps,
        config,
        mainDir,
        oppDir,
        pinLightness
      }).rampResults;
    }
    if (mainDir === "darker") {
      const tmpSurface1 = bestRamp.surface1;
      bestRamp.surface1 = bestRamp.surface3;
      bestRamp.surface3 = tmpSurface1;
    }
    return {
      ramp: bestRamp,
      warnings,
      direction: mainDir
    };
  }

  // packages/theme/build-module/color-ramps/lib/ramp-configs.mjs
  var lightnessConstraintForegroundHighContrast = (direction) => direction === "lighter" ? 0.9551 : 0.235;
  var lightnessConstraintForegroundMediumContrast = (direction) => direction === "lighter" ? 0.77 : 0.56;
  var lightnessConstraintBgFill = (direction) => direction === "lighter" ? 0.67 : 0.45;
  var BG_SURFACE_TAPER_CHROMA = {
    alpha: 0.7
  };
  var FG_TAPER_CHROMA = {
    alpha: 0.6,
    kLight: 0.2,
    kDark: 0.2
  };
  var STROKE_TAPER_CHROMA = {
    alpha: 0.6,
    radiusDark: 0.01,
    radiusLight: 0.01,
    kLight: 0.8,
    kDark: 0.8
  };
  var ACCENT_SURFACE_TAPER_CHROMA = {
    alpha: 0.75,
    radiusDark: 0.01,
    radiusLight: 0.01
  };
  var fgSurface4Config = {
    contrast: {
      reference: "surface3",
      followDirection: "main",
      target: 7,
      preferLighter: true
    },
    lightness: lightnessConstraintForegroundHighContrast,
    taperChromaOptions: FG_TAPER_CHROMA
  };
  var BG_RAMP_CONFIG = {
    // Surface
    surface1: {
      contrast: {
        reference: "surface2",
        followDirection: "opposite",
        target: 1.06,
        ignoreWhenAdjustingSeed: true
      },
      taperChromaOptions: BG_SURFACE_TAPER_CHROMA
    },
    surface2: {
      contrast: {
        reference: "seed",
        followDirection: "main",
        target: 1
      }
    },
    surface3: {
      contrast: {
        reference: "surface2",
        followDirection: "main",
        target: 1.06
      },
      taperChromaOptions: BG_SURFACE_TAPER_CHROMA
    },
    surface4: {
      contrast: {
        reference: "surface2",
        followDirection: "main",
        target: 1.12
      },
      taperChromaOptions: BG_SURFACE_TAPER_CHROMA
    },
    surface5: {
      contrast: {
        reference: "surface2",
        followDirection: "main",
        target: 1.2
      },
      taperChromaOptions: BG_SURFACE_TAPER_CHROMA
    },
    surface6: {
      contrast: {
        reference: "surface2",
        followDirection: "main",
        target: 1.4
      },
      taperChromaOptions: BG_SURFACE_TAPER_CHROMA
    },
    // Bg fill
    bgFill1: {
      contrast: {
        reference: "surface2",
        followDirection: "main",
        target: 4
      },
      lightness: lightnessConstraintBgFill
    },
    bgFill2: {
      contrast: {
        reference: "bgFill1",
        followDirection: "main",
        target: 1.2
      }
    },
    bgFillInverted1: {
      contrast: {
        reference: "bgFillInverted2",
        followDirection: "opposite",
        target: 1.2
      }
    },
    bgFillInverted2: fgSurface4Config,
    bgFillDark: {
      contrast: {
        reference: "surface3",
        followDirection: "darker",
        // This is what causes the token to be always dark
        target: 7,
        ignoreWhenAdjustingSeed: true
      },
      lightness: lightnessConstraintForegroundHighContrast,
      taperChromaOptions: FG_TAPER_CHROMA
    },
    // Stroke
    stroke1: {
      contrast: {
        reference: "stroke3",
        followDirection: "opposite",
        target: 2.6
      },
      taperChromaOptions: STROKE_TAPER_CHROMA
    },
    stroke2: {
      contrast: {
        reference: "stroke3",
        followDirection: "opposite",
        target: 2.4
      },
      taperChromaOptions: STROKE_TAPER_CHROMA
    },
    stroke3: {
      contrast: {
        reference: "surface3",
        followDirection: "main",
        target: 3
      },
      taperChromaOptions: STROKE_TAPER_CHROMA
    },
    stroke4: {
      contrast: {
        reference: "stroke3",
        followDirection: "main",
        target: 1.5
      },
      taperChromaOptions: STROKE_TAPER_CHROMA
    },
    // fgSurface
    fgSurface1: {
      contrast: {
        reference: "surface3",
        followDirection: "main",
        target: 2,
        preferLighter: true
      },
      taperChromaOptions: FG_TAPER_CHROMA
    },
    fgSurface2: {
      contrast: {
        reference: "surface3",
        followDirection: "main",
        target: 3,
        preferLighter: true
      },
      taperChromaOptions: FG_TAPER_CHROMA
    },
    fgSurface3: {
      contrast: {
        reference: "surface3",
        followDirection: "main",
        target: 4.5,
        preferLighter: true
      },
      lightness: lightnessConstraintForegroundMediumContrast,
      taperChromaOptions: FG_TAPER_CHROMA
    },
    fgSurface4: fgSurface4Config,
    // fgFill
    fgFill: {
      contrast: {
        reference: "bgFill1",
        followDirection: "best",
        target: 4.5,
        preferLighter: true
      },
      lightness: lightnessConstraintForegroundHighContrast,
      taperChromaOptions: FG_TAPER_CHROMA
    },
    fgFillInverted: {
      contrast: {
        reference: "bgFillInverted1",
        followDirection: "best",
        target: 4.5,
        preferLighter: true
      },
      lightness: lightnessConstraintForegroundHighContrast,
      taperChromaOptions: FG_TAPER_CHROMA
    },
    fgFillDark: {
      contrast: {
        reference: "bgFillDark",
        followDirection: "best",
        target: 4.5,
        preferLighter: true
      },
      lightness: lightnessConstraintForegroundHighContrast,
      taperChromaOptions: FG_TAPER_CHROMA
    }
  };
  var ACCENT_RAMP_CONFIG = {
    ...BG_RAMP_CONFIG,
    surface1: {
      ...BG_RAMP_CONFIG.surface1,
      taperChromaOptions: ACCENT_SURFACE_TAPER_CHROMA
    },
    surface2: {
      contrast: {
        reference: "bgFill1",
        followDirection: "opposite",
        target: BG_RAMP_CONFIG.bgFill1.contrast.target,
        ignoreWhenAdjustingSeed: true
      },
      taperChromaOptions: ACCENT_SURFACE_TAPER_CHROMA
    },
    surface3: {
      ...BG_RAMP_CONFIG.surface3,
      taperChromaOptions: ACCENT_SURFACE_TAPER_CHROMA
    },
    surface4: {
      ...BG_RAMP_CONFIG.surface4,
      taperChromaOptions: ACCENT_SURFACE_TAPER_CHROMA
    },
    surface5: {
      ...BG_RAMP_CONFIG.surface5,
      taperChromaOptions: ACCENT_SURFACE_TAPER_CHROMA
    },
    surface6: {
      ...BG_RAMP_CONFIG.surface6,
      taperChromaOptions: ACCENT_SURFACE_TAPER_CHROMA
    },
    bgFill1: {
      contrast: {
        reference: "seed",
        followDirection: "main",
        target: 1
      }
    },
    stroke1: {
      ...BG_RAMP_CONFIG.stroke1
    },
    stroke2: {
      ...BG_RAMP_CONFIG.stroke2
    },
    stroke3: {
      ...BG_RAMP_CONFIG.stroke3,
      sameAsIfPossible: "fgSurface3",
      taperChromaOptions: void 0
    },
    stroke4: {
      ...BG_RAMP_CONFIG.stroke4,
      taperChromaOptions: void 0
    },
    // fgSurface: do not de-saturate
    fgSurface1: {
      ...BG_RAMP_CONFIG.fgSurface1,
      taperChromaOptions: void 0
    },
    fgSurface2: {
      ...BG_RAMP_CONFIG.fgSurface2,
      taperChromaOptions: void 0
    },
    fgSurface3: {
      ...BG_RAMP_CONFIG.fgSurface3,
      taperChromaOptions: void 0,
      sameAsIfPossible: "bgFill1"
    },
    fgSurface4: {
      ...BG_RAMP_CONFIG.fgSurface4,
      taperChromaOptions: void 0
    }
  };

  // packages/theme/build-module/color-ramps/index.mjs
  function buildBgRamp(seed) {
    if (typeof seed !== "string" || seed.trim() === "") {
      throw new Error("Seed color must be a non-empty string");
    }
    return buildRamp(seed, BG_RAMP_CONFIG);
  }
  var STEP_TO_PIN = "surface2";
  function getBgRampInfo(ramp) {
    return {
      mainDirection: ramp.direction,
      pinLightness: {
        stepName: STEP_TO_PIN,
        value: clampAccentScaleReferenceLightness(
          get(ramp.ramp[STEP_TO_PIN], [oklch_default, "l"]),
          ramp.direction
        )
      }
    };
  }
  function buildAccentRamp(seed, bgRamp) {
    if (typeof seed !== "string" || seed.trim() === "") {
      throw new Error("Seed color must be a non-empty string");
    }
    const bgRampInfo = bgRamp ? getBgRampInfo(bgRamp) : void 0;
    return buildRamp(seed, ACCENT_RAMP_CONFIG, bgRampInfo);
  }

  // packages/theme/build-module/use-theme-provider-styles.mjs
  var getCachedBgRamp = memize(buildBgRamp, { maxSize: 10 });
  var getCachedAccentRamp = memize(buildAccentRamp, { maxSize: 10 });
  var legacyWpComponentsOverridesCSS = [
    ["--wp-components-color-accent", "var(--wp-admin-theme-color)"],
    [
      "--wp-components-color-accent-darker-10",
      "var(--wp-admin-theme-color-darker-10)"
    ],
    [
      "--wp-components-color-accent-darker-20",
      "var(--wp-admin-theme-color-darker-20)"
    ],
    [
      "--wp-components-color-accent-inverted",
      "var(--wpds-color-fg-interactive-brand-strong)"
    ],
    [
      "--wp-components-color-background",
      "var(--wpds-color-bg-surface-neutral-strong)"
    ],
    [
      "--wp-components-color-foreground",
      "var(--wpds-color-fg-content-neutral)"
    ],
    [
      "--wp-components-color-foreground-inverted",
      "var(--wpds-color-bg-surface-neutral)"
    ],
    [
      "--wp-components-color-gray-100",
      "var(--wpds-color-bg-surface-neutral)"
    ],
    [
      "--wp-components-color-gray-200",
      "var(--wpds-color-stroke-surface-neutral)"
    ],
    [
      "--wp-components-color-gray-300",
      "var(--wpds-color-stroke-surface-neutral)"
    ],
    [
      "--wp-components-color-gray-400",
      "var(--wpds-color-stroke-interactive-neutral)"
    ],
    [
      "--wp-components-color-gray-600",
      "var(--wpds-color-stroke-interactive-neutral)"
    ],
    [
      "--wp-components-color-gray-700",
      "var(--wpds-color-fg-content-neutral-weak)"
    ],
    [
      "--wp-components-color-gray-800",
      "var(--wpds-color-fg-content-neutral)"
    ]
  ];
  function customRgbFormat(color) {
    const rgb = to(color, srgb_default);
    return rgb.coords.map((n2) => Math.round((n2 ?? 0) * 255)).join(", ");
  }
  function legacyWpAdminThemeOverridesCSS(accent) {
    const parsedAccent = to(accent, hsl_default);
    const parsedL = parsedAccent.coords[2] ?? 0;
    const darker10 = set(
      clone(parsedAccent),
      [hsl_default, "l"],
      Math.max(0, parsedL - 5)
      // L reduced by 5%
    );
    const darker20 = set(
      clone(parsedAccent),
      [hsl_default, "l"],
      Math.max(0, parsedL - 10)
      // L reduced by 10%
    );
    return [
      ["--wp-admin-theme-color", getColorString(parsedAccent)],
      ["--wp-admin-theme-color--rgb", customRgbFormat(parsedAccent)],
      ["--wp-admin-theme-color-darker-10", getColorString(darker10)],
      [
        "--wp-admin-theme-color-darker-10--rgb",
        customRgbFormat(darker10)
      ],
      ["--wp-admin-theme-color-darker-20", getColorString(darker20)],
      [
        "--wp-admin-theme-color-darker-20--rgb",
        customRgbFormat(darker20)
      ]
    ];
  }
  function colorTokensCSS(computedColorRamps) {
    const entries = [];
    for (const [rampName, { ramp }] of computedColorRamps) {
      for (const [tokenName, tokenValue] of Object.entries(ramp)) {
        const key = `${rampName}-${tokenName}`;
        const aliasedBy = color_tokens_default[key] ?? [];
        for (const aliasedId of aliasedBy) {
          entries.push([`--wpds-color-${aliasedId}`, tokenValue]);
        }
      }
    }
    return entries;
  }
  function generateStyles({
    primary,
    computedColorRamps
  }) {
    return Object.fromEntries(
      [
        // Semantic color tokens
        colorTokensCSS(computedColorRamps),
        // Legacy overrides
        legacyWpAdminThemeOverridesCSS(primary),
        legacyWpComponentsOverridesCSS
      ].flat()
    );
  }
  function useThemeProviderStyles({
    color = {}
  } = {}) {
    const { resolvedSettings: inheritedSettings } = (0, import_element2.useContext)(ThemeContext);
    const primary = color.primary ?? inheritedSettings.color?.primary ?? DEFAULT_SEED_COLORS.primary;
    const bg = color.bg ?? inheritedSettings.color?.bg ?? DEFAULT_SEED_COLORS.bg;
    const resolvedSettings = (0, import_element2.useMemo)(
      () => ({
        color: {
          primary,
          bg
        }
      }),
      [primary, bg]
    );
    const themeProviderStyles = (0, import_element2.useMemo)(() => {
      const seeds = {
        ...DEFAULT_SEED_COLORS,
        bg,
        primary
      };
      const computedColorRamps = /* @__PURE__ */ new Map();
      const bgRamp = getCachedBgRamp(seeds.bg);
      Object.entries(seeds).forEach(([rampName, seed]) => {
        if (rampName === "bg") {
          computedColorRamps.set(rampName, bgRamp);
        } else {
          computedColorRamps.set(
            rampName,
            getCachedAccentRamp(seed, bgRamp)
          );
        }
      });
      return generateStyles({
        primary: seeds.primary,
        computedColorRamps
      });
    }, [primary, bg]);
    return {
      resolvedSettings,
      themeProviderStyles
    };
  }

  // packages/theme/build-module/theme-provider.mjs
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='662a5161a8']")) {
    const style = document.createElement("style");
    style.setAttribute("data-wp-hash", "662a5161a8");
    style.appendChild(document.createTextNode(".dba930ea7a9438fd__root{display:contents}"));
    document.head.appendChild(style);
  }
  var style_default = { "root": "dba930ea7a9438fd__root" };
  function cssObjectToText(values) {
    return Object.entries(values).map(([key, value]) => `${key}: ${value};`).join("");
  }
  function generateCSSSelector({
    instanceId,
    isRoot
  }) {
    const rootSel = `[data-wpds-root-provider="true"]`;
    const instanceIdSel = `[data-wpds-theme-provider-id="${instanceId}"]`;
    const selectors = [];
    if (isRoot) {
      selectors.push(
        `:root:has(.${style_default.root}${rootSel}${instanceIdSel})`
      );
    }
    selectors.push(`.${style_default.root}.${style_default.root}${instanceIdSel}`);
    return selectors.join(",");
  }
  var ThemeProvider = ({
    children,
    color = {},
    isRoot = false,
    density
  }) => {
    const instanceId = (0, import_element3.useId)();
    const { themeProviderStyles, resolvedSettings } = useThemeProviderStyles({
      color
    });
    const contextValue = (0, import_element3.useMemo)(
      () => ({
        resolvedSettings
      }),
      [resolvedSettings]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_jsx_runtime.Fragment, { children: [
      themeProviderStyles ? /* @__PURE__ */ (0, import_jsx_runtime.jsx)("style", { children: `${generateCSSSelector({
        instanceId,
        isRoot
      })} {${cssObjectToText(themeProviderStyles)}}` }) : null,
      /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
        "div",
        {
          "data-wpds-theme-provider-id": instanceId,
          "data-wpds-root-provider": isRoot,
          "data-wpds-density": density,
          className: style_default.root,
          children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(ThemeContext.Provider, { value: contextValue, children })
        }
      )
    ] });
  };

  // packages/theme/build-module/private-apis.mjs
  var privateApis = {};
  lock(privateApis, {
    ThemeProvider,
    useThemeProviderStyles
  });
  return __toCommonJS(index_exports);
})();