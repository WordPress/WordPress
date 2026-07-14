var ReactJSXRuntime=(()=>{var u=Object.defineProperty;var v=Object.getOwnPropertyDescriptor;var P=Object.getOwnPropertyNames;var w=Object.prototype.hasOwnProperty;var R=(r,e)=>()=>(r&&(e=r(r=0)),e);var a=(r,e)=>()=>(e||r((e={exports:{}}).exports,e),e.exports),T=(r,e)=>{for(var t in e)u(r,t,{get:e[t],enumerable:!0})},k=(r,e,t,n)=>{if(e&&typeof e=="object"||typeof e=="function")for(let s of P(e))!w.call(r,s)&&s!==t&&u(r,s,{get:()=>e[s],enumerable:!(n=v(e,s))||n.enumerable});return r};var _=r=>k(u({},"__esModule",{value:!0}),r);var d=a(o=>{"use strict";var q=Symbol.for("react.transitional.element"),C=Symbol.for("react.fragment");function i(r,e,t){var n=null;if(t!==void 0&&(n=""+t),e.key!==void 0&&(n=""+e.key),"key"in e){t={};for(var s in e)s!=="key"&&(t[s]=e[s])}else t=e;return e=t.ref,{$$typeof:q,type:r,key:n,ref:e!==void 0?e:null,props:t}}o.Fragment=C;o.jsx=i;o.jsxs=i});var f=a((h,p)=>{"use strict";p.exports=d()});var x={};T(x,{warnCompat:()=>A});function A(r,e){c.has(r)||(c.add(r),typeof console<"u"&&console.warn&&console.warn(`${$} ${e}`))}var $,c,j=R(()=>{$="[wordpress-react-19]",c=new Set});var D=a((G,m)=>{var l=f(),{warnCompat:F}=(j(),_(x));function E(r,e){if(!r||!r.defaultProps)return e;F("jsx-default-props","`defaultProps` on function components was removed in React 19 and is emulated by a compatibility polyfill. Use default parameters instead.");let t=r.defaultProps,n=e;for(let s in t)n[s]===void 0&&(n===e&&(n={...e}),n[s]=t[s]);return n}function N(r,e,t){return l.jsx(r,E(r,e),t)}function S(r,e,t){return l.jsxs(r,E(r,e),t)}m.exports={...l,jsx:N,jsxs:S}});return D();})();
/*! Bundled license information:

react/cjs/react-jsx-runtime.production.js:
  (**
   * @license React
   * react-jsx-runtime.production.js
   *
   * Copyright (c) Meta Platforms, Inc. and affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   *)
*/
