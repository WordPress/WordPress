import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ var __webpack_modules__ = ({

/***/ 317:
/***/ ((module) => {

module.exports = import("@wordpress/a11y");;

/***/ })

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  o: () => (/* binding */ actions),
  w: () => (/* binding */ state)
});

;// external "@wordpress/interactivity"
var x = (y) => {
	var x = {}; __webpack_require__.d(x, y); return x
} 
var y = (x) => (() => (x))
const interactivity_namespaceObject = x({ ["getConfig"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.getConfig), ["privateApis"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.privateApis), ["store"]: () => (__WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__.store) });
;// ./node_modules/@wordpress/interactivity-router/build-module/assets/scs.js
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


;// ./node_modules/@wordpress/interactivity-router/build-module/assets/styles.js

const areNodesEqual = (a, b) => a.isEqualNode(b);
const normalizeMedia = (element) => {
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
const stylePromiseCache = /* @__PURE__ */ new WeakMap();
const prepareStylePromise = (element) => {
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
  const promise = new Promise((resolve, reject) => {
    element.addEventListener("load", () => resolve(element));
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
const styleSheetCache = /* @__PURE__ */ new Map();
const preloadStyles = (doc, url) => {
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
const applyStyles = (styles) => {
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


;// ./node_modules/@wordpress/interactivity-router/build-module/assets/dynamic-importmap/resolver.js
const backslashRegEx = /\\/g;
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
function resolveAndComposePackages(packages, outPackages, baseUrl2, parentMap) {
  for (const p in packages) {
    const resolvedLhs = resolveIfNotPlainOrUrl(p, baseUrl2) || p;
    const target = packages[p];
    if (typeof target !== "string") {
      continue;
    }
    const mapped = resolveImportMap(
      parentMap,
      resolveIfNotPlainOrUrl(target, baseUrl2) || target,
      baseUrl2
    );
    if (mapped) {
      outPackages[resolvedLhs] = mapped;
      continue;
    }
  }
}
function resolveAndComposeImportMap(json, baseUrl2, parentMap) {
  const outMap = {
    imports: Object.assign({}, parentMap.imports),
    scopes: Object.assign({}, parentMap.scopes)
  };
  if (json.imports) {
    resolveAndComposePackages(
      json.imports,
      outMap.imports,
      baseUrl2,
      parentMap
    );
  }
  if (json.scopes) {
    for (const s in json.scopes) {
      const resolvedScope = resolveUrl(s, baseUrl2);
      resolveAndComposePackages(
        json.scopes[s],
        outMap.scopes[resolvedScope] || (outMap.scopes[resolvedScope] = {}),
        baseUrl2,
        parentMap
      );
    }
  }
  return outMap;
}
let importMap = { imports: {}, scopes: {} };
const baseUrl = document.baseURI;
const pageBaseUrl = baseUrl;
function resolver_addImportMap(importMapIn) {
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


;// ./node_modules/es-module-lexer/dist/lexer.js
/* es-module-lexer 1.7.0 */
var ImportType;!function(A){A[A.Static=1]="Static",A[A.Dynamic=2]="Dynamic",A[A.ImportMeta=3]="ImportMeta",A[A.StaticSourcePhase=4]="StaticSourcePhase",A[A.DynamicSourcePhase=5]="DynamicSourcePhase",A[A.StaticDeferPhase=6]="StaticDeferPhase",A[A.DynamicDeferPhase=7]="DynamicDeferPhase"}(ImportType||(ImportType={}));const A=1===new Uint8Array(new Uint16Array([1]).buffer)[0];function parse(E,g="@"){if(!C)return init.then((()=>parse(E)));const I=E.length+1,w=(C.__heap_base.value||C.__heap_base)+4*I-C.memory.buffer.byteLength;w>0&&C.memory.grow(Math.ceil(w/65536));const K=C.sa(I-1);if((A?B:Q)(E,new Uint16Array(C.memory.buffer,K,I)),!C.parse())throw Object.assign(new Error(`Parse error ${g}:${E.slice(0,C.e()).split("\n").length}:${C.e()-E.lastIndexOf("\n",C.e()-1)}`),{idx:C.e()});const o=[],D=[];for(;C.ri();){const A=C.is(),Q=C.ie(),B=C.it(),g=C.ai(),I=C.id(),w=C.ss(),K=C.se();let D;C.ip()&&(D=k(E.slice(-1===I?A-1:A,-1===I?Q+1:Q))),o.push({n:D,t:B,s:A,e:Q,ss:w,se:K,d:I,a:g})}for(;C.re();){const A=C.es(),Q=C.ee(),B=C.els(),g=C.ele(),I=E.slice(A,Q),w=I[0],K=B<0?void 0:E.slice(B,g),o=K?K[0]:"";D.push({s:A,e:Q,ls:B,le:g,n:'"'===w||"'"===w?k(I):I,ln:'"'===o||"'"===o?k(K):K})}function k(A){try{return(0,eval)(A)}catch(A){}}return[o,D,!!C.f(),!!C.ms()]}function Q(A,Q){const B=A.length;let C=0;for(;C<B;){const B=A.charCodeAt(C);Q[C++]=(255&B)<<8|B>>>8}}function B(A,Q){const B=A.length;let C=0;for(;C<B;)Q[C]=A.charCodeAt(C++)}let C;const E=()=>{return A="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GzYCHAtBAEEAKAKwCkECajYCsAoCQAJAAkACQEEAECkiAUHhAEYNACABQfcARg0BQQAoArAKIQEMAgtBACgCsAoiAUECakHACEEKEC8NAUEGIQIMAgtBACgCsAoiAS8BAkHpAEcNACABLwEEQfQARw0AQQQhAiABLwEGQegARg0BC0EAIAFBfmo2ArAKDwtBACABIAJBAXRqNgKwCgJAQQEQKUH7AEYNAEEAIAE2ArAKDwtBACgCsAoiACECA0BBACACQQJqNgKwCgJAAkACQEEBECkiAkEiRg0AIAJBJ0cNAUEnEBpBAEEAKAKwCkECajYCsApBARApIQIMAgtBIhAaQQBBACgCsApBAmo2ArAKQQEQKSECDAELIAIQLCECCwJAIAJBOkYNAEEAIAE2ArAKDwtBAEEAKAKwCkECajYCsAoCQEEBECkiAkEiRg0AIAJBJ0YNAEEAIAE2ArAKDwsgAhAaQQBBACgCsApBAmo2ArAKAkACQEEBECkiAkEsRg0AIAJB/QBGDQFBACABNgKwCg8LQQBBACgCsApBAmo2ArAKQQEQKUH9AEYNAEEAKAKwCiECDAELC0EAKALwCSIBIAA2AhAgAUEAKAKwCkECajYCDAttAQJ/AkACQANAAkAgAEH//wNxIgFBd2oiAkEXSw0AQQEgAnRBn4CABHENAgsgAUGgAUYNASAAIQIgARAoDQJBACECQQBBACgCsAoiAEECajYCsAogAC8BAiIADQAMAgsLIAAhAgsgAkH//wNxC6sBAQR/AkACQEEAKAKwCiICLwEAIgNB4QBGDQAgASEEIAAhBQwBC0EAIAJBBGo2ArAKQQEQKSECQQAoArAKIQUCQAJAIAJBIkYNACACQSdGDQAgAhAsGkEAKAKwCiEEDAELIAIQGkEAQQAoArAKQQJqIgQ2ArAKC0EBECkhA0EAKAKwCiECCwJAIAIgBUYNACAFIARBACAAIAAgAUYiAhtBACABIAIbEAILIAMLcgEEf0EAKAKwCiEAQQAoArQKIQECQAJAA0AgAEECaiECIAAgAU8NAQJAAkAgAi8BACIDQaR/ag4CAQQACyACIQAgA0F2ag4EAgEBAgELIABBBGohAAwACwtBACACNgKwChAlQQAPC0EAIAI2ArAKQd0AC0kBA39BACEDAkAgAkUNAAJAA0AgAC0AACIEIAEtAAAiBUcNASABQQFqIQEgAEEBaiEAIAJBf2oiAg0ADAILCyAEIAVrIQMLIAMLC+wBAgBBgAgLzgEAAHgAcABvAHIAdABtAHAAbwByAHQAZgBvAHIAZQB0AGEAbwB1AHIAYwBlAHIAbwBtAHUAbgBjAHQAaQBvAG4AcwBzAGUAcgB0AHYAbwB5AGkAZQBkAGUAbABlAGMAbwBuAHQAaQBuAGkAbgBzAHQAYQBuAHQAeQBiAHIAZQBhAHIAZQB0AHUAcgBkAGUAYgB1AGcAZwBlAGEAdwBhAGkAdABoAHIAdwBoAGkAbABlAGkAZgBjAGEAdABjAGYAaQBuAGEAbABsAGUAbABzAABB0AkLEAEAAAACAAAAAAQAAEA5AAA=","undefined"!=typeof Buffer?Buffer.from(A,"base64"):Uint8Array.from(atob(A),(A=>A.charCodeAt(0)));var A};const init=WebAssembly.compile(E()).then(WebAssembly.instantiate).then((({exports:A})=>{C=A}));const initSync=()=>{if(C)return;const A=new WebAssembly.Module(E());C=new WebAssembly.Instance(A).exports};
;// ./node_modules/@wordpress/interactivity-router/build-module/assets/dynamic-importmap/fetch.js
const fetching = (url, parent) => {
  return ` fetching ${url}${parent ? ` from ${parent}` : ""}`;
};
const jsContentType = /^(text|application)\/(x-)?javascript(;|$)/;
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


;// ./node_modules/@wordpress/interactivity-router/build-module/assets/dynamic-importmap/loader.js



const initPromise = init;
const initialImportMapElement = window.document.querySelector(
  "script#wp-importmap[type=importmap]"
);
const initialImportMap = initialImportMapElement ? JSON.parse(initialImportMapElement.text) : { imports: {}, scopes: {} };
const skip = (id) => Object.keys(initialImportMap.imports).includes(id);
const fetchCache = {};
const registry = {};
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
const createBlob = (source, type = "text/javascript") => URL.createObjectURL(new Blob([source], { type }));
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
const sourceMapURLRegEx = /\n\/\/# source(Mapping)?URL=([^\n]+)\s*((;|\/\/[^#][^\n]*)\s*)*$/;
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
const dynamicImport = (u) => import(
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


;// ./node_modules/@wordpress/interactivity-router/build-module/assets/dynamic-importmap/index.js


const dynamic_importmap_baseUrl = document.baseURI;
const dynamic_importmap_pageBaseUrl = dynamic_importmap_baseUrl;
Object.defineProperty(self, "wpInteractivityRouterImport", {
  value: importShim,
  writable: false,
  enumerable: false,
  configurable: false
});
async function importShim(id) {
  await initPromise;
  return topLevelLoad(resolve(id, dynamic_importmap_pageBaseUrl), {
    credentials: "same-origin"
  });
}
async function importWithMap(id, importMapIn) {
  addImportMap(importMapIn);
  return importShim(id);
}
async function preloadWithMap(id, importMapIn) {
  resolver_addImportMap(importMapIn);
  await initPromise;
  return preloadModule(resolve(id, dynamic_importmap_pageBaseUrl), {
    credentials: "same-origin"
  });
}



;// ./node_modules/@wordpress/interactivity-router/build-module/assets/script-modules.js

const resolvedScriptModules = /* @__PURE__ */ new Set();
const markScriptModuleAsResolved = (url) => {
  resolvedScriptModules.add(url);
};
const preloadScriptModules = (doc) => {
  const importMapElement = doc.querySelector(
    "script#wp-importmap[type=importmap]"
  );
  const importMap = importMapElement ? JSON.parse(importMapElement.text) : { imports: {}, scopes: {} };
  for (const key in initialImportMap.imports) {
    delete importMap.imports[key];
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
  return moduleUrls.filter((url) => !resolvedScriptModules.has(url)).map((url) => preloadWithMap(url, importMap));
};
const importScriptModules = (modules) => Promise.all(modules.map((m) => importPreloadedModule(m)));


;// ./node_modules/@wordpress/interactivity-router/build-module/index.js



const {
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
} = (0,interactivity_namespaceObject.privateApis)(
  "I acknowledge that using private APIs means my theme or plugin will inevitably break in the next version of WordPress."
);
const regionAttr = `data-wp-router-region`;
const interactiveAttr = `data-wp-interactive`;
const regionsSelector = `[${interactiveAttr}][${regionAttr}], [${interactiveAttr}] [${interactiveAttr}][${regionAttr}]`;
const pages = /* @__PURE__ */ new Map();
const getPagePath = (url) => {
  const u = new URL(url, window.location.href);
  return u.pathname + u.search;
};
const parseRegionAttribute = (region) => {
  const value = region.getAttribute(regionAttr);
  try {
    const { id, attachTo } = JSON.parse(value);
    return { id, attachTo };
  } catch (e) {
    return { id: value };
  }
};
const cloneRouterRegionContent = (vdom) => {
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
const regionsToAttachByParent = /* @__PURE__ */ new WeakMap();
const rootFragmentsByParent = /* @__PURE__ */ new WeakMap();
const fetchPage = async (url, { html }) => {
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
const preparePage = async (url, dom, { vdom } = {}) => {
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
const renderPage = (page) => {
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
const forcePageReload = (href) => {
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
let navigatingTo = "";
let hasLoadedNavigationTextsData = false;
const navigationTexts = {
  loading: "Loading page, please wait.",
  loaded: "Page Loaded."
};
const { state, actions } = (0,interactivity_namespaceObject.store)("core/router", {
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
      const { clientNavigationDisabled } = (0,interactivity_namespaceObject.getConfig)();
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
        (resolve) => setTimeout(resolve, timeout)
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
      const { clientNavigationDisabled } = (0,interactivity_namespaceObject.getConfig)();
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
  Promise.resolve(/* import() */).then(__webpack_require__.bind(__webpack_require__, 317)).then(
    ({ speak }) => speak(message),
    // Ignore failures to load the a11y module.
    () => {
    }
  );
}


var __webpack_exports__actions = __webpack_exports__.o;
var __webpack_exports__state = __webpack_exports__.w;
export { __webpack_exports__actions as actions, __webpack_exports__state as state };
