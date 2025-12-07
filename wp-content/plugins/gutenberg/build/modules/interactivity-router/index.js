// packages/interactivity-router/build-module/index.js
import { store, privateApis, getConfig } from "@wordpress/interactivity";

// packages/interactivity-router/build-module/assets/scs.js
function shortestCommonSupersequence(X, Y, isEqual = (a, b) => a === b) {
  const m = X.length;
  const n = Y.length;
  const dp = Array.from(
    { length: m + 1 },
    () => Array(n + 1).fill(null)
  );
  for (let i = 0; i <= m; i++) {
    dp[i][0] = X.slice(0, i);
  }
  for (let j = 0; j <= n; j++) {
    dp[0][j] = Y.slice(0, j);
  }
  for (let i = 1; i <= m; i++) {
    for (let j = 1; j <= n; j++) {
      if (isEqual(X[i - 1], Y[j - 1])) {
        dp[i][j] = dp[i - 1][j - 1].concat(X[i - 1]);
      } else {
        const option1 = dp[i - 1][j].concat(X[i - 1]);
        const option2 = dp[i][j - 1].concat(Y[j - 1]);
        dp[i][j] = option1.length <= option2.length ? option1 : option2;
      }
    }
  }
  return dp[m][n];
}

// packages/interactivity-router/build-module/assets/styles.js
var areNodesEqual = (a, b) => a.isEqualNode(b);
var normalizeMedia = (element) => {
  element = element.cloneNode(true);
  const media = element.media;
  const { originalMedia } = element.dataset;
  if (media === "preload") {
    element.media = originalMedia || "all";
    element.removeAttribute("data-original-media");
  } else if (!element.media) {
    element.media = "all";
  }
  return element;
};
function updateStylesWithSCS(X, Y, parent = window.document.head) {
  if (X.length === 0) {
    return Y.map((element) => {
      const promise = prepareStylePromise(element);
      parent.appendChild(element);
      return promise;
    });
  }
  const xNormalized = X.map(normalizeMedia);
  const yNormalized = Y.map(normalizeMedia);
  const scs = shortestCommonSupersequence(
    xNormalized,
    yNormalized,
    areNodesEqual
  );
  const xLength = X.length;
  const yLength = Y.length;
  const promises = [];
  let last = X[xLength - 1];
  let xIndex = 0;
  let yIndex = 0;
  for (const scsElement of scs) {
    const xElement = X[xIndex];
    const yElement = Y[yIndex];
    const xNormEl = xNormalized[xIndex];
    const yNormEl = yNormalized[yIndex];
    if (xIndex < xLength && areNodesEqual(xNormEl, scsElement)) {
      if (yIndex < yLength && areNodesEqual(yNormEl, scsElement)) {
        promises.push(prepareStylePromise(xElement));
        yIndex++;
      }
      xIndex++;
    } else {
      promises.push(prepareStylePromise(yElement));
      if (xIndex < xLength) {
        xElement.before(yElement);
      } else {
        last.after(yElement);
        last = yElement;
      }
      yIndex++;
    }
  }
  return promises;
}
var stylePromiseCache = /* @__PURE__ */ new WeakMap();
var prepareStylePromise = (element) => {
  if (stylePromiseCache.has(element)) {
    return stylePromiseCache.get(element);
  }
  if (window.document.contains(element) && element.media !== "preload") {
    const promise2 = Promise.resolve(element);
    stylePromiseCache.set(element, promise2);
    return promise2;
  }
  if (element.hasAttribute("media") && element.media !== "all") {
    element.dataset.originalMedia = element.media;
  }
  element.media = "preload";
  if (element instanceof HTMLStyleElement) {
    const promise2 = Promise.resolve(element);
    stylePromiseCache.set(element, promise2);
    return promise2;
  }
  const promise = new Promise((resolve2, reject) => {
    element.addEventListener("load", () => resolve2(element));
    element.addEventListener("error", (event) => {
      const { href } = event.target;
      reject(
        Error(
          `The style sheet with the following URL failed to load: ${href}`
        )
      );
    });
  });
  stylePromiseCache.set(element, promise);
  return promise;
};
var styleSheetCache = /* @__PURE__ */ new Map();
var preloadStyles = (doc, url) => {
  if (!styleSheetCache.has(url)) {
    const currentStyleElements = Array.from(
      window.document.querySelectorAll(
        "style,link[rel=stylesheet]"
      )
    );
    const newStyleElements = Array.from(
      doc.querySelectorAll("style,link[rel=stylesheet]")
    );
    const stylePromises = updateStylesWithSCS(
      currentStyleElements,
      newStyleElements
    );
    styleSheetCache.set(url, stylePromises);
  }
  return styleSheetCache.get(url);
};
var applyStyles = (styles) => {
  window.document.querySelectorAll("style,link[rel=stylesheet]").forEach((el) => {
    if (el.sheet) {
      if (styles.includes(el)) {
        if (el.sheet.media.mediaText === "preload") {
          const { originalMedia = "all" } = el.dataset;
          el.sheet.media.mediaText = originalMedia;
        }
        el.sheet.disabled = false;
      } else {
        el.sheet.disabled = true;
      }
    }
  });
};

// packages/interactivity-router/build-module/assets/dynamic-importmap/resolver.js
var backslashRegEx = /\\/g;
function isURL(url) {
  if (url.indexOf(":") === -1) {
    return false;
  }
  try {
    new URL(url);
    return true;
  } catch (_) {
    return false;
  }
}
function resolveIfNotPlainOrUrl(relUrl, parentUrl) {
  const hIdx = parentUrl.indexOf("#"), qIdx = parentUrl.indexOf("?");
  if (hIdx + qIdx > -2) {
    parentUrl = parentUrl.slice(
      0,
      // eslint-disable-next-line no-nested-ternary
      hIdx === -1 ? qIdx : qIdx === -1 || qIdx > hIdx ? hIdx : qIdx
    );
  }
  if (relUrl.indexOf("\\") !== -1) {
    relUrl = relUrl.replace(backslashRegEx, "/");
  }
  if (relUrl[0] === "/" && relUrl[1] === "/") {
    return parentUrl.slice(0, parentUrl.indexOf(":") + 1) + relUrl;
  } else if (relUrl[0] === "." && (relUrl[1] === "/" || relUrl[1] === "." && (relUrl[2] === "/" || relUrl.length === 2 && (relUrl += "/")) || relUrl.length === 1 && (relUrl += "/")) || relUrl[0] === "/") {
    const parentProtocol = parentUrl.slice(
      0,
      parentUrl.indexOf(":") + 1
    );
    let pathname;
    if (parentUrl[parentProtocol.length + 1] === "/") {
      if (parentProtocol !== "file:") {
        pathname = parentUrl.slice(parentProtocol.length + 2);
        pathname = pathname.slice(pathname.indexOf("/") + 1);
      } else {
        pathname = parentUrl.slice(8);
      }
    } else {
      pathname = parentUrl.slice(
        parentProtocol.length + (parentUrl[parentProtocol.length] === "/")
      );
    }
    if (relUrl[0] === "/") {
      return parentUrl.slice(0, parentUrl.length - pathname.length - 1) + relUrl;
    }
    const segmented = pathname.slice(0, pathname.lastIndexOf("/") + 1) + relUrl;
    const output = [];
    let segmentIndex = -1;
    for (let i = 0; i < segmented.length; i++) {
      if (segmentIndex !== -1) {
        if (segmented[i] === "/") {
          output.push(segmented.slice(segmentIndex, i + 1));
          segmentIndex = -1;
        }
        continue;
      } else if (segmented[i] === ".") {
        if (segmented[i + 1] === "." && (segmented[i + 2] === "/" || i + 2 === segmented.length)) {
          output.pop();
          i += 2;
          continue;
        } else if (segmented[i + 1] === "/" || i + 1 === segmented.length) {
          i += 1;
          continue;
        }
      }
      while (segmented[i] === "/") {
        i++;
      }
      segmentIndex = i;
    }
    if (segmentIndex !== -1) {
      output.push(segmented.slice(segmentIndex));
    }
    return parentUrl.slice(0, parentUrl.length - pathname.length) + output.join("");
  }
}
function resolveUrl(relUrl, parentUrl) {
  return resolveIfNotPlainOrUrl(relUrl, parentUrl) || (isURL(relUrl) ? relUrl : resolveIfNotPlainOrUrl("./" + relUrl, parentUrl));
}
function getMatch(path, matchObj) {
  if (matchObj[path]) {
    return path;
  }
  let sepIndex = path.length;
  do {
    const segment = path.slice(0, sepIndex + 1);
    if (segment in matchObj) {
      return segment;
    }
  } while ((sepIndex = path.lastIndexOf("/", sepIndex - 1)) !== -1);
}
function applyPackages(id, packages) {
  const pkgName = getMatch(id, packages);
  if (pkgName) {
    const pkg = packages[pkgName];
    if (pkg === null) {
      return;
    }
    return pkg + id.slice(pkgName.length);
  }
}
function resolveImportMap(importMap2, resolvedOrPlain, parentUrl) {
  let scopeUrl = parentUrl && getMatch(parentUrl, importMap2.scopes);
  while (scopeUrl) {
    const packageResolution = applyPackages(
      resolvedOrPlain,
      importMap2.scopes[scopeUrl]
    );
    if (packageResolution) {
      return packageResolution;
    }
    scopeUrl = getMatch(
      scopeUrl.slice(0, scopeUrl.lastIndexOf("/")),
      importMap2.scopes
    );
  }
  return applyPackages(resolvedOrPlain, importMap2.imports) || resolvedOrPlain.indexOf(":") !== -1 && resolvedOrPlain;
}
function resolveAndComposePackages(packages, outPackages, baseUrl22, parentMap) {
  for (const p in packages) {
    const resolvedLhs = resolveIfNotPlainOrUrl(p, baseUrl22) || p;
    const target = packages[p];
    if (typeof target !== "string") {
      continue;
    }
    const mapped = resolveImportMap(
      parentMap,
      resolveIfNotPlainOrUrl(target, baseUrl22) || target,
      baseUrl22
    );
    if (mapped) {
      outPackages[resolvedLhs] = mapped;
      continue;
    }
  }
}
function resolveAndComposeImportMap(json, baseUrl22, parentMap) {
  const outMap = {
    imports: Object.assign({}, parentMap.imports),
    scopes: Object.assign({}, parentMap.scopes)
  };
  if (json.imports) {
    resolveAndComposePackages(
      json.imports,
      outMap.imports,
      baseUrl22,
      parentMap
    );
  }
  if (json.scopes) {
    for (const s in json.scopes) {
      const resolvedScope = resolveUrl(s, baseUrl22);
      resolveAndComposePackages(
        json.scopes[s],
        outMap.scopes[resolvedScope] || (outMap.scopes[resolvedScope] = {}),
        baseUrl22,
        parentMap
      );
    }
  }
  return outMap;
}
var importMap = { imports: {}, scopes: {} };
var baseUrl = document.baseURI;
var pageBaseUrl = baseUrl;
function addImportMap(importMapIn) {
  importMap = resolveAndComposeImportMap(
    importMapIn,
    pageBaseUrl,
    importMap
  );
}
function resolve(id, parentUrl) {
  const urlResolved = resolveIfNotPlainOrUrl(id, parentUrl);
  return resolveImportMap(importMap, urlResolved || id, parentUrl) || id;
}

// node_modules/es-module-lexer/dist/lexer.js
var ImportType;
!(function(A2) {
  A2[A2.Static = 1] = "Static", A2[A2.Dynamic = 2] = "Dynamic", A2[A2.ImportMeta = 3] = "ImportMeta", A2[A2.StaticSourcePhase = 4] = "StaticSourcePhase", A2[A2.DynamicSourcePhase = 5] = "DynamicSourcePhase";
})(ImportType || (ImportType = {}));
var A = 1 === new Uint8Array(new Uint16Array([1]).buffer)[0];
function parse(E2, g = "@") {
  if (!C) return init.then((() => parse(E2)));
  const I = E2.length + 1, w = (C.__heap_base.value || C.__heap_base) + 4 * I - C.memory.buffer.byteLength;
  w > 0 && C.memory.grow(Math.ceil(w / 65536));
  const K = C.sa(I - 1);
  if ((A ? B : Q)(E2, new Uint16Array(C.memory.buffer, K, I)), !C.parse()) throw Object.assign(new Error(`Parse error ${g}:${E2.slice(0, C.e()).split("\n").length}:${C.e() - E2.lastIndexOf("\n", C.e() - 1)}`), { idx: C.e() });
  const D = [], o = [];
  for (; C.ri(); ) {
    const A2 = C.is(), Q2 = C.ie(), B2 = C.it(), g2 = C.ai(), I2 = C.id(), w2 = C.ss(), K2 = C.se();
    let o2;
    C.ip() && (o2 = k(E2.slice(-1 === I2 ? A2 - 1 : A2, -1 === I2 ? Q2 + 1 : Q2))), D.push({ n: o2, t: B2, s: A2, e: Q2, ss: w2, se: K2, d: I2, a: g2 });
  }
  for (; C.re(); ) {
    const A2 = C.es(), Q2 = C.ee(), B2 = C.els(), g2 = C.ele(), I2 = E2.slice(A2, Q2), w2 = I2[0], K2 = B2 < 0 ? void 0 : E2.slice(B2, g2), D2 = K2 ? K2[0] : "";
    o.push({ s: A2, e: Q2, ls: B2, le: g2, n: '"' === w2 || "'" === w2 ? k(I2) : I2, ln: '"' === D2 || "'" === D2 ? k(K2) : K2 });
  }
  function k(A2) {
    try {
      return (0, eval)(A2);
    } catch (A3) {
    }
  }
  return [D, o, !!C.f(), !!C.ms()];
}
function Q(A2, Q2) {
  const B2 = A2.length;
  let C2 = 0;
  for (; C2 < B2; ) {
    const B3 = A2.charCodeAt(C2);
    Q2[C2++] = (255 & B3) << 8 | B3 >>> 8;
  }
}
function B(A2, Q2) {
  const B2 = A2.length;
  let C2 = 0;
  for (; C2 < B2; ) Q2[C2] = A2.charCodeAt(C2++);
}
var C;
var init = WebAssembly.compile((E = "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", "undefined" != typeof Buffer ? Buffer.from(E, "base64") : Uint8Array.from(atob(E), ((A2) => A2.charCodeAt(0))))).then(WebAssembly.instantiate).then((({ exports: A2 }) => {
  C = A2;
}));
var E;

// packages/interactivity-router/build-module/assets/dynamic-importmap/fetch.js
var fetching = (url, parent) => {
  return ` fetching ${url}${parent ? ` from ${parent}` : ""}`;
};
var jsContentType = /^(text|application)\/(x-)?javascript(;|$)/;
async function fetchModule(url, fetchOpts, parent) {
  let res;
  try {
    res = await fetch(url, fetchOpts);
  } catch (e) {
    throw Error(`Network error${fetching(url, parent)}.`);
  }
  if (!res.ok) {
    throw Error(`Error ${res.status}${fetching(url, parent)}.`);
  }
  const contentType = res.headers.get("content-type");
  if (!jsContentType.test(contentType)) {
    throw Error(
      `Bad Content-Type "${contentType}"${fetching(url, parent)}.`
    );
  }
  return { responseUrl: res.url, source: await res.text() };
}

// packages/interactivity-router/build-module/assets/dynamic-importmap/loader.js
var initPromise = init;
var initialImportMapElement = window.document.querySelector(
  "script#wp-importmap[type=importmap]"
);
var initialImportMap = initialImportMapElement ? JSON.parse(initialImportMapElement.text) : { imports: {}, scopes: {} };
var skip = (id) => Object.keys(initialImportMap.imports).includes(id);
var fetchCache = {};
var registry = {};
Object.keys(initialImportMap.imports).forEach((id) => {
  registry[id] = {
    blobUrl: id
  };
});
async function loadAll(load, seen) {
  if (load.blobUrl || seen[load.url]) {
    return;
  }
  seen[load.url] = 1;
  await load.linkPromise;
  await Promise.all(load.deps.map((dep) => loadAll(dep, seen)));
}
function urlJsString(url) {
  return `'${url.replace(/'/g, "\\'")}'`;
}
var createBlob = (source, type = "text/javascript") => URL.createObjectURL(new Blob([source], { type }));
function resolveDeps(load, seen) {
  if (load.blobUrl || !seen[load.url]) {
    return;
  }
  seen[load.url] = 0;
  for (const dep of load.deps) {
    resolveDeps(dep, seen);
  }
  const [imports, exports] = load.analysis;
  const source = load.source;
  let resolvedSource = "";
  if (!imports.length) {
    resolvedSource += source;
  } else {
    let pushStringTo = function(originalIndex) {
      while (dynamicImportEndStack.length && dynamicImportEndStack[dynamicImportEndStack.length - 1] < originalIndex) {
        const dynamicImportEnd = dynamicImportEndStack.pop();
        resolvedSource += `${source.slice(
          lastIndex,
          dynamicImportEnd
        )}, ${urlJsString(load.responseUrl)}`;
        lastIndex = dynamicImportEnd;
      }
      resolvedSource += source.slice(lastIndex, originalIndex);
      lastIndex = originalIndex;
    };
    let lastIndex = 0;
    let depIndex = 0;
    const dynamicImportEndStack = [];
    for (const {
      s: start,
      ss: statementStart,
      se: statementEnd,
      d: dynamicImportIndex
    } of imports) {
      if (dynamicImportIndex === -1) {
        const depLoad = load.deps[depIndex++];
        let blobUrl = depLoad.blobUrl;
        const cycleShell = !blobUrl;
        if (cycleShell) {
          if (!(blobUrl = depLoad.shellUrl)) {
            blobUrl = depLoad.shellUrl = createBlob(
              `export function u$_(m){${depLoad.analysis[1].map(({ s, e }, i) => {
                const q = depLoad.source[s] === '"' || depLoad.source[s] === "'";
                return `e$_${i}=m${q ? `[` : "."}${depLoad.source.slice(s, e)}${q ? `]` : ""}`;
              }).join(",")}}${depLoad.analysis[1].length ? `let ${depLoad.analysis[1].map((_, i) => `e$_${i}`).join(",")};` : ""}export {${depLoad.analysis[1].map(
                ({ s, e }, i) => `e$_${i} as ${depLoad.source.slice(
                  s,
                  e
                )}`
              ).join(",")}}
//# sourceURL=${depLoad.responseUrl}?cycle`
            );
          }
        }
        pushStringTo(start - 1);
        resolvedSource += `/*${source.slice(
          start - 1,
          statementEnd
        )}*/${urlJsString(blobUrl)}`;
        if (!cycleShell && depLoad.shellUrl) {
          resolvedSource += `;import*as m$_${depIndex} from'${depLoad.blobUrl}';import{u$_ as u$_${depIndex}}from'${depLoad.shellUrl}';u$_${depIndex}(m$_${depIndex})`;
          depLoad.shellUrl = void 0;
        }
        lastIndex = statementEnd;
      } else if (dynamicImportIndex === -2) {
        throw Error("The import.meta property is not supported.");
      } else {
        pushStringTo(statementStart);
        resolvedSource += `wpInteractivityRouterImport(`;
        dynamicImportEndStack.push(statementEnd - 1);
        lastIndex = start;
      }
    }
    if (load.shellUrl) {
      resolvedSource += `
;import{u$_}from'${load.shellUrl}';try{u$_({${exports.filter((e) => e.ln).map(({ s, e, ln }) => `${source.slice(s, e)}:${ln}`).join(",")}})}catch(_){};
`;
    }
    pushStringTo(source.length);
  }
  let hasSourceURL = false;
  resolvedSource = resolvedSource.replace(
    sourceMapURLRegEx,
    (match, isMapping, url) => {
      hasSourceURL = !isMapping;
      return match.replace(
        url,
        () => new URL(url, load.responseUrl).toString()
      );
    }
  );
  if (!hasSourceURL) {
    resolvedSource += "\n//# sourceURL=" + load.responseUrl;
  }
  load.blobUrl = createBlob(resolvedSource);
  load.source = void 0;
}
var sourceMapURLRegEx = /\n\/\/# source(Mapping)?URL=([^\n]+)\s*((;|\/\/[^#][^\n]*)\s*)*$/;
function getOrCreateLoad(url, fetchOpts, parent) {
  let load = registry[url];
  if (load) {
    return load;
  }
  load = { url };
  if (registry[url]) {
    let i = 0;
    while (registry[load.url + ++i]) {
    }
    load.url += i;
  }
  registry[load.url] = load;
  load.fetchPromise = (async () => {
    let source;
    ({ responseUrl: load.responseUrl, source } = await (fetchCache[url] || fetchModule(url, fetchOpts, parent)));
    try {
      load.analysis = parse(source, load.url);
    } catch (e) {
      console.error(e);
      load.analysis = [[], [], false, false];
    }
    load.source = source;
    return load;
  })();
  load.linkPromise = load.fetchPromise.then(async () => {
    let childFetchOpts = fetchOpts;
    load.deps = (await Promise.all(
      load.analysis[0].map(async ({ n, d }) => {
        if (d !== -1 || !n) {
          return void 0;
        }
        const responseUrl = resolve(
          n,
          load.responseUrl || load.url
        );
        if (skip && skip(responseUrl)) {
          return { blobUrl: responseUrl };
        }
        if (childFetchOpts.integrity) {
          childFetchOpts = {
            ...childFetchOpts,
            integrity: void 0
          };
        }
        return getOrCreateLoad(
          responseUrl,
          childFetchOpts,
          load.responseUrl
        ).fetchPromise;
      })
    )).filter((l) => l);
  });
  return load;
}
var dynamicImport = (u) => import(
  /* webpackIgnore: true */
  u
);
async function preloadModule(url, fetchOpts) {
  await initPromise;
  const load = getOrCreateLoad(url, fetchOpts, null);
  const seen = {};
  await loadAll(load, seen);
  resolveDeps(load, seen);
  await Promise.resolve();
  return load;
}
async function importPreloadedModule(load) {
  const module = await dynamicImport(load.blobUrl);
  if (load.shellUrl) {
    (await dynamicImport(load.shellUrl)).u$_(module);
  }
  return module;
}
async function topLevelLoad(url, fetchOpts) {
  const load = await preloadModule(url, fetchOpts);
  return importPreloadedModule(load);
}

// packages/interactivity-router/build-module/assets/dynamic-importmap/index.js
var baseUrl2 = document.baseURI;
var pageBaseUrl2 = baseUrl2;
Object.defineProperty(self, "wpInteractivityRouterImport", {
  value: importShim,
  writable: false,
  enumerable: false,
  configurable: false
});
async function importShim(id) {
  await initPromise;
  return topLevelLoad(resolve(id, pageBaseUrl2), {
    credentials: "same-origin"
  });
}
async function preloadWithMap(id, importMapIn) {
  addImportMap(importMapIn);
  await initPromise;
  return preloadModule(resolve(id, pageBaseUrl2), {
    credentials: "same-origin"
  });
}

// packages/interactivity-router/build-module/assets/script-modules.js
var resolvedScriptModules = /* @__PURE__ */ new Set();
var markScriptModuleAsResolved = (url) => {
  resolvedScriptModules.add(url);
};
var preloadScriptModules = (doc) => {
  const importMapElement = doc.querySelector(
    "script#wp-importmap[type=importmap]"
  );
  const importMap2 = importMapElement ? JSON.parse(importMapElement.text) : { imports: {}, scopes: {} };
  for (const key in initialImportMap.imports) {
    delete importMap2.imports[key];
  }
  const moduleUrls = [
    ...doc.querySelectorAll(
      "script[type=module][src][data-wp-router-options]"
    )
  ].filter((script) => {
    try {
      const parsed = JSON.parse(
        script.getAttribute("data-wp-router-options")
      );
      return parsed?.loadOnClientNavigation === true;
    } catch {
      return false;
    }
  }).map((script) => script.src);
  return moduleUrls.filter((url) => !resolvedScriptModules.has(url)).map((url) => preloadWithMap(url, importMap2));
};
var importScriptModules = (modules) => Promise.all(modules.map((m) => importPreloadedModule(m)));

// packages/interactivity-router/build-module/index.js
var {
  getRegionRootFragment,
  initialVdom,
  toVdom,
  render,
  parseServerData,
  populateServerData,
  batch,
  routerRegions,
  cloneElement,
  navigationSignal
} = privateApis(
  "I acknowledge that using private APIs means my theme or plugin will inevitably break in the next version of WordPress."
);
var regionAttr = `data-wp-router-region`;
var interactiveAttr = `data-wp-interactive`;
var regionsSelector = `[${interactiveAttr}][${regionAttr}], [${interactiveAttr}] [${interactiveAttr}][${regionAttr}]`;
var pages = /* @__PURE__ */ new Map();
var getPagePath = (url) => {
  const u = new URL(url, window.location.href);
  return u.pathname + u.search;
};
var parseRegionAttribute = (region) => {
  const value = region.getAttribute(regionAttr);
  try {
    const { id, attachTo } = JSON.parse(value);
    return { id, attachTo };
  } catch (e) {
    return { id: value };
  }
};
var cloneRouterRegionContent = (vdom) => {
  if (!vdom) {
    return vdom;
  }
  const allPriorityLevels = vdom.props.priorityLevels;
  const routerRegionLevel = allPriorityLevels.findIndex(
    (level) => level.includes("router-region")
  );
  const priorityLevels = routerRegionLevel !== -1 ? allPriorityLevels.slice(routerRegionLevel + 1) : allPriorityLevels;
  return priorityLevels.length > 0 ? cloneElement(vdom, {
    ...vdom.props,
    priorityLevels
  }) : vdom.props.element;
};
var regionsToAttachByParent = /* @__PURE__ */ new WeakMap();
var rootFragmentsByParent = /* @__PURE__ */ new WeakMap();
var fetchPage = async (url, { html }) => {
  try {
    if (!html) {
      const res = await window.fetch(url);
      if (res.status !== 200) {
        return false;
      }
      html = await res.text();
    }
    const dom = new window.DOMParser().parseFromString(html, "text/html");
    return await preparePage(url, dom);
  } catch (e) {
    return false;
  }
};
var preparePage = async (url, dom, { vdom } = {}) => {
  dom.querySelectorAll("noscript").forEach((el) => el.remove());
  const regions = {};
  const regionsToAttach = {};
  dom.querySelectorAll(regionsSelector).forEach((region) => {
    const { id, attachTo } = parseRegionAttribute(region);
    if (region.parentElement.closest(`[${regionAttr}]`)) {
      regions[id] = void 0;
    } else {
      regions[id] = vdom?.has(region) ? vdom.get(region) : toVdom(region);
    }
    if (attachTo) {
      regionsToAttach[id] = attachTo;
    }
  });
  const title = dom.querySelector("title")?.innerText;
  const initialData = parseServerData(dom);
  const [styles, scriptModules] = await Promise.all([
    Promise.all(preloadStyles(dom, url)),
    Promise.all(preloadScriptModules(dom))
  ]);
  return {
    regions,
    regionsToAttach,
    styles,
    scriptModules,
    title,
    initialData,
    url
  };
};
var renderPage = (page) => {
  applyStyles(page.styles);
  const regionsToAttach = { ...page.regionsToAttach };
  batch(() => {
    populateServerData(page.initialData);
    navigationSignal.value += 1;
    routerRegions.forEach((signal) => {
      signal.value = null;
    });
    const parentsToUpdate = /* @__PURE__ */ new Set();
    for (const id in regionsToAttach) {
      const parent = document.querySelector(regionsToAttach[id]);
      if (!regionsToAttachByParent.has(parent)) {
        regionsToAttachByParent.set(parent, []);
      }
      const regions = regionsToAttachByParent.get(parent);
      if (!regions.includes(id)) {
        regions.push(id);
        parentsToUpdate.add(parent);
      }
    }
    for (const id in page.regions) {
      if (routerRegions.has(id)) {
        routerRegions.get(id).value = cloneRouterRegionContent(
          page.regions[id]
        );
      }
    }
    parentsToUpdate.forEach((parent) => {
      const ids = regionsToAttachByParent.get(parent);
      const vdoms = ids.map((id) => page.regions[id]);
      if (!rootFragmentsByParent.has(parent)) {
        const regions = vdoms.map(({ props, type }) => {
          const elementType = typeof type === "function" ? props.type : type;
          const region = document.createElement(elementType);
          parent.appendChild(region);
          return region;
        });
        rootFragmentsByParent.set(
          parent,
          getRegionRootFragment(regions)
        );
      }
      const fragment = rootFragmentsByParent.get(parent);
      render(vdoms, fragment);
    });
  });
  if (page.title) {
    document.title = page.title;
  }
};
var forcePageReload = (href) => {
  window.location.assign(href);
  return new Promise(() => {
  });
};
window.addEventListener("popstate", async () => {
  const pagePath = getPagePath(window.location.href);
  const page = pages.has(pagePath) && await pages.get(pagePath);
  if (page) {
    batch(() => {
      state.url = window.location.href;
      renderPage(page);
    });
  } else {
    window.location.reload();
  }
});
window.document.querySelectorAll("script[type=module][src]").forEach(({ src }) => markScriptModuleAsResolved(src));
pages.set(
  getPagePath(window.location.href),
  Promise.resolve(
    preparePage(getPagePath(window.location.href), document, {
      vdom: initialVdom
    })
  )
);
var navigatingTo = "";
var hasLoadedNavigationTextsData = false;
var navigationTexts = {
  loading: "Loading page, please wait.",
  loaded: "Page Loaded."
};
var { state, actions } = store("core/router", {
  state: {
    url: window.location.href,
    navigation: {
      hasStarted: false,
      hasFinished: false
    }
  },
  actions: {
    /**
     * Navigates to the specified page.
     *
     * This function normalizes the passed href, fetches the page HTML if
     * needed, and updates any interactive regions whose contents have
     * changed. It also creates a new entry in the browser session history.
     *
     * @param href                               The page href.
     * @param [options]                          Options object.
     * @param [options.force]                    If true, it forces re-fetching the URL.
     * @param [options.html]                     HTML string to be used instead of fetching the requested URL.
     * @param [options.replace]                  If true, it replaces the current entry in the browser session history.
     * @param [options.timeout]                  Time until the navigation is aborted, in milliseconds. Default is 10000.
     * @param [options.loadingAnimation]         Whether an animation should be shown while navigating. Default to `true`.
     * @param [options.screenReaderAnnouncement] Whether a message for screen readers should be announced while navigating. Default to `true`.
     *
     * @return  Promise that resolves once the navigation is completed or aborted.
     */
    *navigate(href, options = {}) {
      const { clientNavigationDisabled } = getConfig();
      if (clientNavigationDisabled) {
        yield forcePageReload(href);
      }
      const pagePath = getPagePath(href);
      const { navigation } = state;
      const {
        loadingAnimation = true,
        screenReaderAnnouncement = true,
        timeout = 1e4
      } = options;
      navigatingTo = href;
      actions.prefetch(pagePath, options);
      const timeoutPromise = new Promise(
        (resolve2) => setTimeout(resolve2, timeout)
      );
      const loadingTimeout = setTimeout(() => {
        if (navigatingTo !== href) {
          return;
        }
        if (loadingAnimation) {
          navigation.hasStarted = true;
          navigation.hasFinished = false;
        }
        if (screenReaderAnnouncement) {
          a11ySpeak("loading");
        }
      }, 400);
      const page = yield Promise.race([
        pages.get(pagePath),
        timeoutPromise
      ]);
      clearTimeout(loadingTimeout);
      if (navigatingTo !== href) {
        return;
      }
      if (page && !page.initialData?.config?.["core/router"]?.clientNavigationDisabled) {
        yield importScriptModules(page.scriptModules);
        batch(() => {
          state.url = href;
          if (loadingAnimation) {
            navigation.hasStarted = false;
            navigation.hasFinished = true;
          }
          renderPage(page);
        });
        window.history[options.replace ? "replaceState" : "pushState"]({}, "", href);
        if (screenReaderAnnouncement) {
          a11ySpeak("loaded");
        }
        const { hash } = new URL(href, window.location.href);
        if (hash) {
          document.querySelector(hash)?.scrollIntoView();
        }
      } else {
        yield forcePageReload(href);
      }
    },
    /**
     * Prefetches the page with the passed URL.
     *
     * The function normalizes the URL and stores internally the fetch
     * promise, to avoid triggering a second fetch for an ongoing request.
     *
     * @param url             The page URL.
     * @param [options]       Options object.
     * @param [options.force] Force fetching the URL again.
     * @param [options.html]  HTML string to be used instead of fetching the requested URL.
     *
     * @return  Promise that resolves once the page has been fetched.
     */
    *prefetch(url, options = {}) {
      const { clientNavigationDisabled } = getConfig();
      if (clientNavigationDisabled) {
        return;
      }
      const pagePath = getPagePath(url);
      if (options.force || !pages.has(pagePath)) {
        pages.set(
          pagePath,
          fetchPage(pagePath, { html: options.html })
        );
      }
      yield pages.get(pagePath);
    }
  }
});
function a11ySpeak(messageKey) {
  if (!hasLoadedNavigationTextsData) {
    hasLoadedNavigationTextsData = true;
    const content = document.getElementById(
      "wp-script-module-data-@wordpress/interactivity-router"
    )?.textContent;
    if (content) {
      try {
        const parsed = JSON.parse(content);
        if (typeof parsed?.i18n?.loading === "string") {
          navigationTexts.loading = parsed.i18n.loading;
        }
        if (typeof parsed?.i18n?.loaded === "string") {
          navigationTexts.loaded = parsed.i18n.loaded;
        }
      } catch {
      }
    } else {
      if (state.navigation.texts?.loading) {
        navigationTexts.loading = state.navigation.texts.loading;
      }
      if (state.navigation.texts?.loaded) {
        navigationTexts.loaded = state.navigation.texts.loaded;
      }
    }
  }
  const message = navigationTexts[messageKey];
  import("@wordpress/a11y").then(
    ({ speak }) => speak(message),
    // Ignore failures to load the a11y module.
    () => {
    }
  );
}
export {
  actions,
  state
};
//# sourceMappingURL=index.js.map
