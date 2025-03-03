/******/ var __webpack_modules__ = ({

/***/ 622:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Ob: () => (/* binding */ J),
/* harmony export */   Qv: () => (/* binding */ G),
/* harmony export */   XX: () => (/* binding */ E),
/* harmony export */   fF: () => (/* binding */ l),
/* harmony export */   h: () => (/* binding */ _),
/* harmony export */   q6: () => (/* binding */ K),
/* harmony export */   uA: () => (/* binding */ x),
/* harmony export */   zO: () => (/* binding */ u)
/* harmony export */ });
/* unused harmony exports Fragment, createElement, createRef, toChildArray */
var n,l,t,u,i,r,o,e,f,c,s,a,h,p={},v=[],y=/acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i,d=Array.isArray;function w(n,l){for(var t in l)n[t]=l[t];return n}function g(n){n&&n.parentNode&&n.parentNode.removeChild(n)}function _(l,t,u){var i,r,o,e={};for(o in t)"key"==o?i=t[o]:"ref"==o?r=t[o]:e[o]=t[o];if(arguments.length>2&&(e.children=arguments.length>3?n.call(arguments,2):u),"function"==typeof l&&null!=l.defaultProps)for(o in l.defaultProps)void 0===e[o]&&(e[o]=l.defaultProps[o]);return m(l,e,i,r,null)}function m(n,u,i,r,o){var e={type:n,props:u,key:i,ref:r,__k:null,__:null,__b:0,__e:null,__c:null,constructor:void 0,__v:null==o?++t:o,__i:-1,__u:0};return null==o&&null!=l.vnode&&l.vnode(e),e}function b(){return{current:null}}function k(n){return n.children}function x(n,l){this.props=n,this.context=l}function S(n,l){if(null==l)return n.__?S(n.__,n.__i+1):null;for(var t;l<n.__k.length;l++)if(null!=(t=n.__k[l])&&null!=t.__e)return t.__e;return"function"==typeof n.type?S(n):null}function C(n){var l,t;if(null!=(n=n.__)&&null!=n.__c){for(n.__e=n.__c.base=null,l=0;l<n.__k.length;l++)if(null!=(t=n.__k[l])&&null!=t.__e){n.__e=n.__c.base=t.__e;break}return C(n)}}function M(n){(!n.__d&&(n.__d=!0)&&i.push(n)&&!$.__r++||r!==l.debounceRendering)&&((r=l.debounceRendering)||o)($)}function $(){for(var n,t,u,r,o,f,c,s=1;i.length;)i.length>s&&i.sort(e),n=i.shift(),s=i.length,n.__d&&(u=void 0,o=(r=(t=n).__v).__e,f=[],c=[],t.__P&&((u=w({},r)).__v=r.__v+1,l.vnode&&l.vnode(u),O(t.__P,u,r,t.__n,t.__P.namespaceURI,32&r.__u?[o]:null,f,null==o?S(r):o,!!(32&r.__u),c),u.__v=r.__v,u.__.__k[u.__i]=u,z(f,u,c),u.__e!=o&&C(u)));$.__r=0}function I(n,l,t,u,i,r,o,e,f,c,s){var a,h,y,d,w,g,_=u&&u.__k||v,m=l.length;for(f=P(t,l,_,f,m),a=0;a<m;a++)null!=(y=t.__k[a])&&(h=-1===y.__i?p:_[y.__i]||p,y.__i=a,g=O(n,y,h,i,r,o,e,f,c,s),d=y.__e,y.ref&&h.ref!=y.ref&&(h.ref&&q(h.ref,null,y),s.push(y.ref,y.__c||d,y)),null==w&&null!=d&&(w=d),4&y.__u||h.__k===y.__k?f=A(y,f,n):"function"==typeof y.type&&void 0!==g?f=g:d&&(f=d.nextSibling),y.__u&=-7);return t.__e=w,f}function P(n,l,t,u,i){var r,o,e,f,c,s=t.length,a=s,h=0;for(n.__k=new Array(i),r=0;r<i;r++)null!=(o=l[r])&&"boolean"!=typeof o&&"function"!=typeof o?(f=r+h,(o=n.__k[r]="string"==typeof o||"number"==typeof o||"bigint"==typeof o||o.constructor==String?m(null,o,null,null,null):d(o)?m(k,{children:o},null,null,null):void 0===o.constructor&&o.__b>0?m(o.type,o.props,o.key,o.ref?o.ref:null,o.__v):o).__=n,o.__b=n.__b+1,e=null,-1!==(c=o.__i=L(o,t,f,a))&&(a--,(e=t[c])&&(e.__u|=2)),null==e||null===e.__v?(-1==c&&(i>s?h--:i<s&&h++),"function"!=typeof o.type&&(o.__u|=4)):c!=f&&(c==f-1?h--:c==f+1?h++:(c>f?h--:h++,o.__u|=4))):n.__k[r]=null;if(a)for(r=0;r<s;r++)null!=(e=t[r])&&0==(2&e.__u)&&(e.__e==u&&(u=S(e)),B(e,e));return u}function A(n,l,t){var u,i;if("function"==typeof n.type){for(u=n.__k,i=0;u&&i<u.length;i++)u[i]&&(u[i].__=n,l=A(u[i],l,t));return l}n.__e!=l&&(l&&n.type&&!t.contains(l)&&(l=S(n)),t.insertBefore(n.__e,l||null),l=n.__e);do{l=l&&l.nextSibling}while(null!=l&&8==l.nodeType);return l}function H(n,l){return l=l||[],null==n||"boolean"==typeof n||(d(n)?n.some(function(n){H(n,l)}):l.push(n)),l}function L(n,l,t,u){var i,r,o=n.key,e=n.type,f=l[t];if(null===f&&null==n.key||f&&o==f.key&&e===f.type&&0==(2&f.__u))return t;if(u>(null!=f&&0==(2&f.__u)?1:0))for(i=t-1,r=t+1;i>=0||r<l.length;){if(i>=0){if((f=l[i])&&0==(2&f.__u)&&o==f.key&&e===f.type)return i;i--}if(r<l.length){if((f=l[r])&&0==(2&f.__u)&&o==f.key&&e===f.type)return r;r++}}return-1}function T(n,l,t){"-"==l[0]?n.setProperty(l,null==t?"":t):n[l]=null==t?"":"number"!=typeof t||y.test(l)?t:t+"px"}function j(n,l,t,u,i){var r;n:if("style"==l)if("string"==typeof t)n.style.cssText=t;else{if("string"==typeof u&&(n.style.cssText=u=""),u)for(l in u)t&&l in t||T(n.style,l,"");if(t)for(l in t)u&&t[l]===u[l]||T(n.style,l,t[l])}else if("o"==l[0]&&"n"==l[1])r=l!=(l=l.replace(f,"$1")),l=l.toLowerCase()in n||"onFocusOut"==l||"onFocusIn"==l?l.toLowerCase().slice(2):l.slice(2),n.l||(n.l={}),n.l[l+r]=t,t?u?t.t=u.t:(t.t=c,n.addEventListener(l,r?a:s,r)):n.removeEventListener(l,r?a:s,r);else{if("http://www.w3.org/2000/svg"==i)l=l.replace(/xlink(H|:h)/,"h").replace(/sName$/,"s");else if("width"!=l&&"height"!=l&&"href"!=l&&"list"!=l&&"form"!=l&&"tabIndex"!=l&&"download"!=l&&"rowSpan"!=l&&"colSpan"!=l&&"role"!=l&&"popover"!=l&&l in n)try{n[l]=null==t?"":t;break n}catch(n){}"function"==typeof t||(null==t||!1===t&&"-"!=l[4]?n.removeAttribute(l):n.setAttribute(l,"popover"==l&&1==t?"":t))}}function F(n){return function(t){if(this.l){var u=this.l[t.type+n];if(null==t.u)t.u=c++;else if(t.u<u.t)return;return u(l.event?l.event(t):t)}}}function O(n,t,u,i,r,o,e,f,c,s){var a,h,p,v,y,_,m,b,S,C,M,$,P,A,H,L,T,j=t.type;if(void 0!==t.constructor)return null;128&u.__u&&(c=!!(32&u.__u),o=[f=t.__e=u.__e]),(a=l.__b)&&a(t);n:if("function"==typeof j)try{if(b=t.props,S="prototype"in j&&j.prototype.render,C=(a=j.contextType)&&i[a.__c],M=a?C?C.props.value:a.__:i,u.__c?m=(h=t.__c=u.__c).__=h.__E:(S?t.__c=h=new j(b,M):(t.__c=h=new x(b,M),h.constructor=j,h.render=D),C&&C.sub(h),h.props=b,h.state||(h.state={}),h.context=M,h.__n=i,p=h.__d=!0,h.__h=[],h._sb=[]),S&&null==h.__s&&(h.__s=h.state),S&&null!=j.getDerivedStateFromProps&&(h.__s==h.state&&(h.__s=w({},h.__s)),w(h.__s,j.getDerivedStateFromProps(b,h.__s))),v=h.props,y=h.state,h.__v=t,p)S&&null==j.getDerivedStateFromProps&&null!=h.componentWillMount&&h.componentWillMount(),S&&null!=h.componentDidMount&&h.__h.push(h.componentDidMount);else{if(S&&null==j.getDerivedStateFromProps&&b!==v&&null!=h.componentWillReceiveProps&&h.componentWillReceiveProps(b,M),!h.__e&&(null!=h.shouldComponentUpdate&&!1===h.shouldComponentUpdate(b,h.__s,M)||t.__v==u.__v)){for(t.__v!=u.__v&&(h.props=b,h.state=h.__s,h.__d=!1),t.__e=u.__e,t.__k=u.__k,t.__k.some(function(n){n&&(n.__=t)}),$=0;$<h._sb.length;$++)h.__h.push(h._sb[$]);h._sb=[],h.__h.length&&e.push(h);break n}null!=h.componentWillUpdate&&h.componentWillUpdate(b,h.__s,M),S&&null!=h.componentDidUpdate&&h.__h.push(function(){h.componentDidUpdate(v,y,_)})}if(h.context=M,h.props=b,h.__P=n,h.__e=!1,P=l.__r,A=0,S){for(h.state=h.__s,h.__d=!1,P&&P(t),a=h.render(h.props,h.state,h.context),H=0;H<h._sb.length;H++)h.__h.push(h._sb[H]);h._sb=[]}else do{h.__d=!1,P&&P(t),a=h.render(h.props,h.state,h.context),h.state=h.__s}while(h.__d&&++A<25);h.state=h.__s,null!=h.getChildContext&&(i=w(w({},i),h.getChildContext())),S&&!p&&null!=h.getSnapshotBeforeUpdate&&(_=h.getSnapshotBeforeUpdate(v,y)),L=a,null!=a&&a.type===k&&null==a.key&&(L=N(a.props.children)),f=I(n,d(L)?L:[L],t,u,i,r,o,e,f,c,s),h.base=t.__e,t.__u&=-161,h.__h.length&&e.push(h),m&&(h.__E=h.__=null)}catch(n){if(t.__v=null,c||null!=o)if(n.then){for(t.__u|=c?160:128;f&&8==f.nodeType&&f.nextSibling;)f=f.nextSibling;o[o.indexOf(f)]=null,t.__e=f}else for(T=o.length;T--;)g(o[T]);else t.__e=u.__e,t.__k=u.__k;l.__e(n,t,u)}else null==o&&t.__v==u.__v?(t.__k=u.__k,t.__e=u.__e):f=t.__e=V(u.__e,t,u,i,r,o,e,c,s);return(a=l.diffed)&&a(t),128&t.__u?void 0:f}function z(n,t,u){for(var i=0;i<u.length;i++)q(u[i],u[++i],u[++i]);l.__c&&l.__c(t,n),n.some(function(t){try{n=t.__h,t.__h=[],n.some(function(n){n.call(t)})}catch(n){l.__e(n,t.__v)}})}function N(n){return"object"!=typeof n||null==n?n:d(n)?n.map(N):w({},n)}function V(t,u,i,r,o,e,f,c,s){var a,h,v,y,w,_,m,b=i.props,k=u.props,x=u.type;if("svg"==x?o="http://www.w3.org/2000/svg":"math"==x?o="http://www.w3.org/1998/Math/MathML":o||(o="http://www.w3.org/1999/xhtml"),null!=e)for(a=0;a<e.length;a++)if((w=e[a])&&"setAttribute"in w==!!x&&(x?w.localName==x:3==w.nodeType)){t=w,e[a]=null;break}if(null==t){if(null==x)return document.createTextNode(k);t=document.createElementNS(o,x,k.is&&k),c&&(l.__m&&l.__m(u,e),c=!1),e=null}if(null===x)b===k||c&&t.data===k||(t.data=k);else{if(e=e&&n.call(t.childNodes),b=i.props||p,!c&&null!=e)for(b={},a=0;a<t.attributes.length;a++)b[(w=t.attributes[a]).name]=w.value;for(a in b)if(w=b[a],"children"==a);else if("dangerouslySetInnerHTML"==a)v=w;else if(!(a in k)){if("value"==a&&"defaultValue"in k||"checked"==a&&"defaultChecked"in k)continue;j(t,a,null,w,o)}for(a in k)w=k[a],"children"==a?y=w:"dangerouslySetInnerHTML"==a?h=w:"value"==a?_=w:"checked"==a?m=w:c&&"function"!=typeof w||b[a]===w||j(t,a,w,b[a],o);if(h)c||v&&(h.__html===v.__html||h.__html===t.innerHTML)||(t.innerHTML=h.__html),u.__k=[];else if(v&&(t.innerHTML=""),I("template"===u.type?t.content:t,d(y)?y:[y],u,i,r,"foreignObject"==x?"http://www.w3.org/1999/xhtml":o,e,f,e?e[0]:i.__k&&S(i,0),c,s),null!=e)for(a=e.length;a--;)g(e[a]);c||(a="value","progress"==x&&null==_?t.removeAttribute("value"):void 0!==_&&(_!==t[a]||"progress"==x&&!_||"option"==x&&_!==b[a])&&j(t,a,_,b[a],o),a="checked",void 0!==m&&m!==t[a]&&j(t,a,m,b[a],o))}return t}function q(n,t,u){try{if("function"==typeof n){var i="function"==typeof n.__u;i&&n.__u(),i&&null==t||(n.__u=n(t))}else n.current=t}catch(n){l.__e(n,u)}}function B(n,t,u){var i,r;if(l.unmount&&l.unmount(n),(i=n.ref)&&(i.current&&i.current!==n.__e||q(i,null,t)),null!=(i=n.__c)){if(i.componentWillUnmount)try{i.componentWillUnmount()}catch(n){l.__e(n,t)}i.base=i.__P=null}if(i=n.__k)for(r=0;r<i.length;r++)i[r]&&B(i[r],t,u||"function"!=typeof n.type);u||g(n.__e),n.__c=n.__=n.__e=void 0}function D(n,l,t){return this.constructor(n,t)}function E(t,u,i){var r,o,e,f;u==document&&(u=document.documentElement),l.__&&l.__(t,u),o=(r="function"==typeof i)?null:i&&i.__k||u.__k,e=[],f=[],O(u,t=(!r&&i||u).__k=_(k,null,[t]),o||p,p,u.namespaceURI,!r&&i?[i]:o?null:u.firstChild?n.call(u.childNodes):null,e,!r&&i?i:o?o.__e:u.firstChild,r,f),z(e,t,f)}function G(n,l){E(n,l,G)}function J(l,t,u){var i,r,o,e,f=w({},l.props);for(o in l.type&&l.type.defaultProps&&(e=l.type.defaultProps),t)"key"==o?i=t[o]:"ref"==o?r=t[o]:f[o]=void 0===t[o]&&void 0!==e?e[o]:t[o];return arguments.length>2&&(f.children=arguments.length>3?n.call(arguments,2):u),m(l.type,f,i||l.key,r||l.ref,null)}function K(n){function l(n){var t,u;return this.getChildContext||(t=new Set,(u={})[l.__c]=this,this.getChildContext=function(){return u},this.componentWillUnmount=function(){t=null},this.shouldComponentUpdate=function(n){this.props.value!==n.value&&t.forEach(function(n){n.__e=!0,M(n)})},this.sub=function(n){t.add(n);var l=n.componentWillUnmount;n.componentWillUnmount=function(){t&&t.delete(n),l&&l.call(n)}}),n.children}return l.__c="__cC"+h++,l.__=n,l.Provider=l.__l=(l.Consumer=function(n,l){return n.children(l)}).contextType=l,l}n=v.slice,l={__e:function(n,l,t,u){for(var i,r,o;l=l.__;)if((i=l.__c)&&!i.__)try{if((r=i.constructor)&&null!=r.getDerivedStateFromError&&(i.setState(r.getDerivedStateFromError(n)),o=i.__d),null!=i.componentDidCatch&&(i.componentDidCatch(n,u||{}),o=i.__d),o)return i.__E=i}catch(l){n=l}throw n}},t=0,u=function(n){return null!=n&&null==n.constructor},x.prototype.setState=function(n,l){var t;t=null!=this.__s&&this.__s!==this.state?this.__s:this.__s=w({},this.state),"function"==typeof n&&(n=n(w({},t),this.props)),n&&w(t,n),null!=n&&this.__v&&(l&&this._sb.push(l),M(this))},x.prototype.forceUpdate=function(n){this.__v&&(this.__e=!0,n&&this.__h.push(n),M(this))},x.prototype.render=k,i=[],o="function"==typeof Promise?Promise.prototype.then.bind(Promise.resolve()):setTimeout,e=function(n,l){return n.__v.__b-l.__v.__b},$.__r=0,f=/(PointerCapture)$|Capture$/i,c=0,s=F(!1),a=F(!0),h=0;


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
  zj: () => (/* reexport */ getConfig),
  SD: () => (/* reexport */ getContext),
  V6: () => (/* reexport */ getElement),
  $K: () => (/* reexport */ getServerContext),
  vT: () => (/* reexport */ getServerState),
  jb: () => (/* binding */ privateApis),
  yT: () => (/* reexport */ splitTask),
  M_: () => (/* reexport */ store),
  hb: () => (/* reexport */ useCallback),
  vJ: () => (/* reexport */ useEffect),
  ip: () => (/* reexport */ useInit),
  Nf: () => (/* reexport */ useLayoutEffect),
  Kr: () => (/* reexport */ useMemo),
  li: () => (/* reexport */ A),
  J0: () => (/* reexport */ d),
  FH: () => (/* reexport */ useWatch),
  v4: () => (/* reexport */ withScope),
  mh: () => (/* reexport */ withSyncEvent)
});

// EXTERNAL MODULE: ./node_modules/preact/dist/preact.module.js
var preact_module = __webpack_require__(622);
;// ./node_modules/preact/hooks/dist/hooks.module.js
var hooks_module_t,r,hooks_module_u,i,hooks_module_o=0,hooks_module_f=[],hooks_module_c=preact_module/* options */.fF,e=hooks_module_c.__b,a=hooks_module_c.__r,v=hooks_module_c.diffed,l=hooks_module_c.__c,m=hooks_module_c.unmount,s=hooks_module_c.__;function p(n,t){hooks_module_c.__h&&hooks_module_c.__h(r,n,hooks_module_o||t),hooks_module_o=0;var u=r.__H||(r.__H={__:[],__h:[]});return n>=u.__.length&&u.__.push({}),u.__[n]}function d(n){return hooks_module_o=1,h(D,n)}function h(n,u,i){var o=p(hooks_module_t++,2);if(o.t=n,!o.__c&&(o.__=[i?i(u):D(void 0,u),function(n){var t=o.__N?o.__N[0]:o.__[0],r=o.t(t,n);t!==r&&(o.__N=[r,o.__[1]],o.__c.setState({}))}],o.__c=r,!r.__f)){var f=function(n,t,r){if(!o.__c.__H)return!0;var u=o.__c.__H.__.filter(function(n){return!!n.__c});if(u.every(function(n){return!n.__N}))return!c||c.call(this,n,t,r);var i=o.__c.props!==n;return u.forEach(function(n){if(n.__N){var t=n.__[0];n.__=n.__N,n.__N=void 0,t!==n.__[0]&&(i=!0)}}),c&&c.call(this,n,t,r)||i};r.__f=!0;var c=r.shouldComponentUpdate,e=r.componentWillUpdate;r.componentWillUpdate=function(n,t,r){if(this.__e){var u=c;c=void 0,f(n,t,r),c=u}e&&e.call(this,n,t,r)},r.shouldComponentUpdate=f}return o.__N||o.__}function y(n,u){var i=p(hooks_module_t++,3);!hooks_module_c.__s&&C(i.__H,u)&&(i.__=n,i.u=u,r.__H.__h.push(i))}function _(n,u){var i=p(hooks_module_t++,4);!hooks_module_c.__s&&C(i.__H,u)&&(i.__=n,i.u=u,r.__h.push(i))}function A(n){return hooks_module_o=5,T(function(){return{current:n}},[])}function F(n,t,r){hooks_module_o=6,_(function(){if("function"==typeof n){var r=n(t());return function(){n(null),r&&"function"==typeof r&&r()}}if(n)return n.current=t(),function(){return n.current=null}},null==r?r:r.concat(n))}function T(n,r){var u=p(hooks_module_t++,7);return C(u.__H,r)&&(u.__=n(),u.__H=r,u.__h=n),u.__}function q(n,t){return hooks_module_o=8,T(function(){return n},t)}function x(n){var u=r.context[n.__c],i=p(hooks_module_t++,9);return i.c=n,u?(null==i.__&&(i.__=!0,u.sub(r)),u.props.value):n.__}function P(n,t){hooks_module_c.useDebugValue&&hooks_module_c.useDebugValue(t?t(n):n)}function b(n){var u=p(hooks_module_t++,10),i=d();return u.__=n,r.componentDidCatch||(r.componentDidCatch=function(n,t){u.__&&u.__(n,t),i[1](n)}),[i[0],function(){i[1](void 0)}]}function g(){var n=p(hooks_module_t++,11);if(!n.__){for(var u=r.__v;null!==u&&!u.__m&&null!==u.__;)u=u.__;var i=u.__m||(u.__m=[0,0]);n.__="P"+i[0]+"-"+i[1]++}return n.__}function j(){for(var n;n=hooks_module_f.shift();)if(n.__P&&n.__H)try{n.__H.__h.forEach(z),n.__H.__h.forEach(B),n.__H.__h=[]}catch(t){n.__H.__h=[],hooks_module_c.__e(t,n.__v)}}hooks_module_c.__b=function(n){r=null,e&&e(n)},hooks_module_c.__=function(n,t){n&&t.__k&&t.__k.__m&&(n.__m=t.__k.__m),s&&s(n,t)},hooks_module_c.__r=function(n){a&&a(n),hooks_module_t=0;var i=(r=n.__c).__H;i&&(hooks_module_u===r?(i.__h=[],r.__h=[],i.__.forEach(function(n){n.__N&&(n.__=n.__N),n.u=n.__N=void 0})):(i.__h.forEach(z),i.__h.forEach(B),i.__h=[],hooks_module_t=0)),hooks_module_u=r},hooks_module_c.diffed=function(n){v&&v(n);var t=n.__c;t&&t.__H&&(t.__H.__h.length&&(1!==hooks_module_f.push(t)&&i===hooks_module_c.requestAnimationFrame||((i=hooks_module_c.requestAnimationFrame)||w)(j)),t.__H.__.forEach(function(n){n.u&&(n.__H=n.u),n.u=void 0})),hooks_module_u=r=null},hooks_module_c.__c=function(n,t){t.some(function(n){try{n.__h.forEach(z),n.__h=n.__h.filter(function(n){return!n.__||B(n)})}catch(r){t.some(function(n){n.__h&&(n.__h=[])}),t=[],hooks_module_c.__e(r,n.__v)}}),l&&l(n,t)},hooks_module_c.unmount=function(n){m&&m(n);var t,r=n.__c;r&&r.__H&&(r.__H.__.forEach(function(n){try{z(n)}catch(n){t=n}}),r.__H=void 0,t&&hooks_module_c.__e(t,r.__v))};var k="function"==typeof requestAnimationFrame;function w(n){var t,r=function(){clearTimeout(u),k&&cancelAnimationFrame(t),setTimeout(n)},u=setTimeout(r,100);k&&(t=requestAnimationFrame(r))}function z(n){var t=r,u=n.__c;"function"==typeof u&&(n.__c=void 0,u()),r=t}function B(n){var t=r;n.__c=n.__(),r=t}function C(n,t){return!n||n.length!==t.length||t.some(function(t,r){return t!==n[r]})}function D(n,t){return"function"==typeof t?t(n):t}

;// ./node_modules/@preact/signals-core/dist/signals-core.module.js
var signals_core_module_i=Symbol.for("preact-signals");function signals_core_module_t(){if(!(signals_core_module_s>1)){var i,t=!1;while(void 0!==signals_core_module_h){var r=signals_core_module_h;signals_core_module_h=void 0;signals_core_module_f++;while(void 0!==r){var o=r.o;r.o=void 0;r.f&=-3;if(!(8&r.f)&&signals_core_module_c(r))try{r.c()}catch(r){if(!t){i=r;t=!0}}r=o}}signals_core_module_f=0;signals_core_module_s--;if(t)throw i}else signals_core_module_s--}function signals_core_module_r(i){if(signals_core_module_s>0)return i();signals_core_module_s++;try{return i()}finally{signals_core_module_t()}}var signals_core_module_o=void 0;function n(i){var t=signals_core_module_o;signals_core_module_o=void 0;try{return i()}finally{signals_core_module_o=t}}var signals_core_module_h=void 0,signals_core_module_s=0,signals_core_module_f=0,signals_core_module_v=0;function signals_core_module_e(i){if(void 0!==signals_core_module_o){var t=i.n;if(void 0===t||t.t!==signals_core_module_o){t={i:0,S:i,p:signals_core_module_o.s,n:void 0,t:signals_core_module_o,e:void 0,x:void 0,r:t};if(void 0!==signals_core_module_o.s)signals_core_module_o.s.n=t;signals_core_module_o.s=t;i.n=t;if(32&signals_core_module_o.f)i.S(t);return t}else if(-1===t.i){t.i=0;if(void 0!==t.n){t.n.p=t.p;if(void 0!==t.p)t.p.n=t.n;t.p=signals_core_module_o.s;t.n=void 0;signals_core_module_o.s.n=t;signals_core_module_o.s=t}return t}}}function signals_core_module_u(i){this.v=i;this.i=0;this.n=void 0;this.t=void 0}signals_core_module_u.prototype.brand=signals_core_module_i;signals_core_module_u.prototype.h=function(){return!0};signals_core_module_u.prototype.S=function(i){if(this.t!==i&&void 0===i.e){i.x=this.t;if(void 0!==this.t)this.t.e=i;this.t=i}};signals_core_module_u.prototype.U=function(i){if(void 0!==this.t){var t=i.e,r=i.x;if(void 0!==t){t.x=r;i.e=void 0}if(void 0!==r){r.e=t;i.x=void 0}if(i===this.t)this.t=r}};signals_core_module_u.prototype.subscribe=function(i){var t=this;return E(function(){var r=t.value,n=signals_core_module_o;signals_core_module_o=void 0;try{i(r)}finally{signals_core_module_o=n}})};signals_core_module_u.prototype.valueOf=function(){return this.value};signals_core_module_u.prototype.toString=function(){return this.value+""};signals_core_module_u.prototype.toJSON=function(){return this.value};signals_core_module_u.prototype.peek=function(){var i=signals_core_module_o;signals_core_module_o=void 0;try{return this.value}finally{signals_core_module_o=i}};Object.defineProperty(signals_core_module_u.prototype,"value",{get:function(){var i=signals_core_module_e(this);if(void 0!==i)i.i=this.i;return this.v},set:function(i){if(i!==this.v){if(signals_core_module_f>100)throw new Error("Cycle detected");this.v=i;this.i++;signals_core_module_v++;signals_core_module_s++;try{for(var r=this.t;void 0!==r;r=r.x)r.t.N()}finally{signals_core_module_t()}}}});function signals_core_module_d(i){return new signals_core_module_u(i)}function signals_core_module_c(i){for(var t=i.s;void 0!==t;t=t.n)if(t.S.i!==t.i||!t.S.h()||t.S.i!==t.i)return!0;return!1}function signals_core_module_a(i){for(var t=i.s;void 0!==t;t=t.n){var r=t.S.n;if(void 0!==r)t.r=r;t.S.n=t;t.i=-1;if(void 0===t.n){i.s=t;break}}}function signals_core_module_l(i){var t=i.s,r=void 0;while(void 0!==t){var o=t.p;if(-1===t.i){t.S.U(t);if(void 0!==o)o.n=t.n;if(void 0!==t.n)t.n.p=o}else r=t;t.S.n=t.r;if(void 0!==t.r)t.r=void 0;t=o}i.s=r}function signals_core_module_y(i){signals_core_module_u.call(this,void 0);this.x=i;this.s=void 0;this.g=signals_core_module_v-1;this.f=4}(signals_core_module_y.prototype=new signals_core_module_u).h=function(){this.f&=-3;if(1&this.f)return!1;if(32==(36&this.f))return!0;this.f&=-5;if(this.g===signals_core_module_v)return!0;this.g=signals_core_module_v;this.f|=1;if(this.i>0&&!signals_core_module_c(this)){this.f&=-2;return!0}var i=signals_core_module_o;try{signals_core_module_a(this);signals_core_module_o=this;var t=this.x();if(16&this.f||this.v!==t||0===this.i){this.v=t;this.f&=-17;this.i++}}catch(i){this.v=i;this.f|=16;this.i++}signals_core_module_o=i;signals_core_module_l(this);this.f&=-2;return!0};signals_core_module_y.prototype.S=function(i){if(void 0===this.t){this.f|=36;for(var t=this.s;void 0!==t;t=t.n)t.S.S(t)}signals_core_module_u.prototype.S.call(this,i)};signals_core_module_y.prototype.U=function(i){if(void 0!==this.t){signals_core_module_u.prototype.U.call(this,i);if(void 0===this.t){this.f&=-33;for(var t=this.s;void 0!==t;t=t.n)t.S.U(t)}}};signals_core_module_y.prototype.N=function(){if(!(2&this.f)){this.f|=6;for(var i=this.t;void 0!==i;i=i.x)i.t.N()}};Object.defineProperty(signals_core_module_y.prototype,"value",{get:function(){if(1&this.f)throw new Error("Cycle detected");var i=signals_core_module_e(this);this.h();if(void 0!==i)i.i=this.i;if(16&this.f)throw this.v;return this.v}});function signals_core_module_w(i){return new signals_core_module_y(i)}function signals_core_module_(i){var r=i.u;i.u=void 0;if("function"==typeof r){signals_core_module_s++;var n=signals_core_module_o;signals_core_module_o=void 0;try{r()}catch(t){i.f&=-2;i.f|=8;signals_core_module_g(i);throw t}finally{signals_core_module_o=n;signals_core_module_t()}}}function signals_core_module_g(i){for(var t=i.s;void 0!==t;t=t.n)t.S.U(t);i.x=void 0;i.s=void 0;signals_core_module_(i)}function signals_core_module_p(i){if(signals_core_module_o!==this)throw new Error("Out-of-order effect");signals_core_module_l(this);signals_core_module_o=i;this.f&=-2;if(8&this.f)signals_core_module_g(this);signals_core_module_t()}function signals_core_module_b(i){this.x=i;this.u=void 0;this.s=void 0;this.o=void 0;this.f=32}signals_core_module_b.prototype.c=function(){var i=this.S();try{if(8&this.f)return;if(void 0===this.x)return;var t=this.x();if("function"==typeof t)this.u=t}finally{i()}};signals_core_module_b.prototype.S=function(){if(1&this.f)throw new Error("Cycle detected");this.f|=1;this.f&=-9;signals_core_module_(this);signals_core_module_a(this);signals_core_module_s++;var i=signals_core_module_o;signals_core_module_o=this;return signals_core_module_p.bind(this,i)};signals_core_module_b.prototype.N=function(){if(!(2&this.f)){this.f|=2;this.o=signals_core_module_h;signals_core_module_h=this}};signals_core_module_b.prototype.d=function(){this.f|=8;if(!(1&this.f))signals_core_module_g(this)};function E(i){var t=new signals_core_module_b(i);try{t.c()}catch(i){t.d();throw i}return t.d.bind(t)}
;// ./node_modules/@preact/signals/dist/signals.module.js
var signals_module_v,signals_module_s;function signals_module_l(i,n){preact_module/* options */.fF[i]=n.bind(null,preact_module/* options */.fF[i]||function(){})}function signals_module_d(i){if(signals_module_s)signals_module_s();signals_module_s=i&&i.S()}function signals_module_h(i){var r=this,f=i.data,o=useSignal(f);o.value=f;var e=T(function(){var i=r.__v;while(i=i.__)if(i.__c){i.__c.__$f|=4;break}r.__$u.c=function(){var i,t=r.__$u.S(),f=e.value;t();if((0,preact_module/* isValidElement */.zO)(f)||3!==(null==(i=r.base)?void 0:i.nodeType)){r.__$f|=1;r.setState({})}else r.base.data=f};return signals_core_module_w(function(){var i=o.value.value;return 0===i?0:!0===i?"":i||""})},[]);return e.value}signals_module_h.displayName="_st";Object.defineProperties(signals_core_module_u.prototype,{constructor:{configurable:!0,value:void 0},type:{configurable:!0,value:signals_module_h},props:{configurable:!0,get:function(){return{data:this}}},__b:{configurable:!0,value:1}});signals_module_l("__b",function(i,r){if("string"==typeof r.type){var n,t=r.props;for(var f in t)if("children"!==f){var o=t[f];if(o instanceof signals_core_module_u){if(!n)r.__np=n={};n[f]=o;t[f]=o.peek()}}}i(r)});signals_module_l("__r",function(i,r){signals_module_d();var n,t=r.__c;if(t){t.__$f&=-2;if(void 0===(n=t.__$u))t.__$u=n=function(i){var r;E(function(){r=this});r.c=function(){t.__$f|=1;t.setState({})};return r}()}signals_module_v=t;signals_module_d(n);i(r)});signals_module_l("__e",function(i,r,n,t){signals_module_d();signals_module_v=void 0;i(r,n,t)});signals_module_l("diffed",function(i,r){signals_module_d();signals_module_v=void 0;var n;if("string"==typeof r.type&&(n=r.__e)){var t=r.__np,f=r.props;if(t){var o=n.U;if(o)for(var e in o){var u=o[e];if(void 0!==u&&!(e in t)){u.d();o[e]=void 0}}else n.U=o={};for(var a in t){var c=o[a],s=t[a];if(void 0===c){c=signals_module_p(n,a,s,f);o[a]=c}else c.o(s,f)}}}i(r)});function signals_module_p(i,r,n,t){var f=r in i&&void 0===i.ownerSVGElement,o=signals_core_module_d(n);return{o:function(i,r){o.value=i;t=r},d:E(function(){var n=o.value.value;if(t[r]!==n){t[r]=n;if(f)i[r]=n;else if(n)i.setAttribute(r,n);else i.removeAttribute(r)}})}}signals_module_l("unmount",function(i,r){if("string"==typeof r.type){var n=r.__e;if(n){var t=n.U;if(t){n.U=void 0;for(var f in t){var o=t[f];if(o)o.d()}}}}else{var e=r.__c;if(e){var u=e.__$u;if(u){e.__$u=void 0;u.d()}}}i(r)});signals_module_l("__h",function(i,r,n,t){if(t<3||9===t)r.__$f|=2;i(r,n,t)});preact_module/* Component */.uA.prototype.shouldComponentUpdate=function(i,r){var n=this.__$u,t=n&&void 0!==n.s;for(var f in r)return!0;if(this.__f||"boolean"==typeof this.u&&!0===this.u){if(!(t||2&this.__$f||4&this.__$f))return!0;if(1&this.__$f)return!0}else{if(!(t||4&this.__$f))return!0;if(3&this.__$f)return!0}for(var o in i)if("__source"!==o&&i[o]!==this.props[o])return!0;for(var e in this.props)if(!(e in i))return!0;return!1};function useSignal(i){return T(function(){return signals_core_module_d(i)},[])}function useComputed(i){var r=f(i);r.current=i;signals_module_v.__$f|=4;return t(function(){return u(function(){return r.current()})},[])}function useSignalEffect(i){var r=f(i);r.current=i;o(function(){return c(function(){return r.current()})},[])}
;// ./node_modules/@wordpress/interactivity/build-module/namespaces.js
const namespaceStack = [];
const getNamespace = () => namespaceStack.slice(-1)[0];
const setNamespace = namespace => {
  namespaceStack.push(namespace);
};
const resetNamespace = () => {
  namespaceStack.pop();
};

;// ./node_modules/@wordpress/interactivity/build-module/scopes.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */

// Store stacks for the current scope and the default namespaces and export APIs
// to interact with them.
const scopeStack = [];
const getScope = () => scopeStack.slice(-1)[0];
const setScope = scope => {
  scopeStack.push(scope);
};
const resetScope = () => {
  scopeStack.pop();
};

// Wrap the element props to prevent modifications.
const immutableMap = new WeakMap();
const immutableError = () => {
  throw new Error('Please use `data-wp-bind` to modify the attributes of an element.');
};
const immutableHandlers = {
  get(target, key, receiver) {
    const value = Reflect.get(target, key, receiver);
    return !!value && typeof value === 'object' ? deepImmutable(value) : value;
  },
  set: immutableError,
  deleteProperty: immutableError
};
const deepImmutable = target => {
  if (!immutableMap.has(target)) {
    immutableMap.set(target, new Proxy(target, immutableHandlers));
  }
  return immutableMap.get(target);
};

/**
 * Retrieves the context inherited by the element evaluating a function from the
 * store. The returned value depends on the element and the namespace where the
 * function calling `getContext()` exists.
 *
 * @param namespace Store namespace. By default, the namespace where the calling
 *                  function exists is used.
 * @return The context content.
 */
const getContext = namespace => {
  const scope = getScope();
  if (true) {
    if (!scope) {
      throw Error('Cannot call `getContext()` when there is no scope. If you are using an async function, please consider using a generator instead. If you are using some sort of async callbacks, like `setTimeout`, please wrap the callback with `withScope(callback)`.');
    }
  }
  return scope.context[namespace || getNamespace()];
};

/**
 * Retrieves a representation of the element where a function from the store
 * is being evaluated. Such representation is read-only, and contains a
 * reference to the DOM element, its props and a local reactive state.
 *
 * @return Element representation.
 */
const getElement = () => {
  const scope = getScope();
  if (true) {
    if (!scope) {
      throw Error('Cannot call `getElement()` when there is no scope. If you are using an async function, please consider using a generator instead. If you are using some sort of async callbacks, like `setTimeout`, please wrap the callback with `withScope(callback)`.');
    }
  }
  const {
    ref,
    attributes
  } = scope;
  return Object.freeze({
    ref: ref.current,
    attributes: deepImmutable(attributes)
  });
};

/**
 * Retrieves the part of the inherited context defined and updated from the
 * server.
 *
 * The object returned is read-only, and includes the context defined in PHP
 * with `wp_interactivity_data_wp_context()`, including the corresponding
 * inherited properties. When `actions.navigate()` is called, this object is
 * updated to reflect the changes in the new visited page, without affecting the
 * context returned by `getContext()`. Directives can subscribe to those changes
 * to update the context if needed.
 *
 * @example
 * ```js
 *  store('...', {
 *    callbacks: {
 *      updateServerContext() {
 *        const context = getContext();
 *        const serverContext = getServerContext();
 *        // Override some property with the new value that came from the server.
 *        context.overridableProp = serverContext.overridableProp;
 *      },
 *    },
 *  });
 * ```
 *
 * @param namespace Store namespace. By default, the namespace where the calling
 *                  function exists is used.
 * @return The server context content.
 */
const getServerContext = namespace => {
  const scope = getScope();
  if (true) {
    if (!scope) {
      throw Error('Cannot call `getServerContext()` when there is no scope. If you are using an async function, please consider using a generator instead. If you are using some sort of async callbacks, like `setTimeout`, please wrap the callback with `withScope(callback)`.');
    }
  }
  return scope.serverContext[namespace || getNamespace()];
};

;// ./node_modules/@wordpress/interactivity/build-module/utils.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Executes a callback function after the next frame is rendered.
 *
 * @param callback The callback function to be executed.
 * @return A promise that resolves after the callback function is executed.
 */
const afterNextFrame = callback => {
  return new Promise(resolve => {
    const done = () => {
      clearTimeout(timeout);
      window.cancelAnimationFrame(raf);
      setTimeout(() => {
        callback();
        resolve();
      });
    };
    const timeout = setTimeout(done, 100);
    const raf = window.requestAnimationFrame(done);
  });
};

/**
 * Returns a promise that resolves after yielding to main.
 *
 * @return Promise<void>
 */
const splitTask = typeof window.scheduler?.yield === 'function' ? window.scheduler.yield.bind(window.scheduler) : () => {
  return new Promise(resolve => {
    setTimeout(resolve, 0);
  });
};

/**
 * Creates a Flusher object that can be used to flush computed values and notify listeners.
 *
 * Using the mangled properties:
 * this.c: this._callback
 * this.x: this._compute
 * https://github.com/preactjs/signals/blob/main/mangle.json
 *
 * @param compute The function that computes the value to be flushed.
 * @param notify  The function that notifies listeners when the value is flushed.
 * @return The Flusher object with `flush` and `dispose` properties.
 */
function createFlusher(compute, notify) {
  let flush = () => undefined;
  const dispose = E(function () {
    flush = this.c.bind(this);
    this.x = compute;
    this.c = notify;
    return compute();
  });
  return {
    flush,
    dispose
  };
}

/**
 * Custom hook that executes a callback function whenever a signal is triggered.
 * Version of `useSignalEffect` with a `useEffect`-like execution. This hook
 * implementation comes from this PR, but we added short-cirtuiting to avoid
 * infinite loops: https://github.com/preactjs/signals/pull/290
 *
 * @param callback The callback function to be executed.
 */
function utils_useSignalEffect(callback) {
  y(() => {
    let eff = null;
    let isExecuting = false;
    const notify = async () => {
      if (eff && !isExecuting) {
        isExecuting = true;
        await afterNextFrame(eff.flush);
        isExecuting = false;
      }
    };
    eff = createFlusher(callback, notify);
    return eff.dispose;
  }, []);
}

/**
 * Returns the passed function wrapped with the current scope so it is
 * accessible whenever the function runs. This is primarily to make the scope
 * available inside hook callbacks.
 *
 * Asynchronous functions should use generators that yield promises instead of awaiting them.
 * See the documentation for details: https://developer.wordpress.org/block-editor/reference-guides/packages/packages-interactivity/packages-interactivity-api-reference/#the-store
 *
 * @param func The passed function.
 * @return The wrapped function.
 */

function withScope(func) {
  const scope = getScope();
  const ns = getNamespace();
  let wrapped;
  if (func?.constructor?.name === 'GeneratorFunction') {
    wrapped = async (...args) => {
      const gen = func(...args);
      let value;
      let it;
      while (true) {
        setNamespace(ns);
        setScope(scope);
        try {
          it = gen.next(value);
        } finally {
          resetScope();
          resetNamespace();
        }
        try {
          value = await it.value;
        } catch (e) {
          setNamespace(ns);
          setScope(scope);
          gen.throw(e);
        } finally {
          resetScope();
          resetNamespace();
        }
        if (it.done) {
          break;
        }
      }
      return value;
    };
  } else {
    wrapped = (...args) => {
      setNamespace(ns);
      setScope(scope);
      try {
        return func(...args);
      } finally {
        resetNamespace();
        resetScope();
      }
    };
  }

  // If function was annotated via `withSyncEvent()`, maintain the annotation.
  const syncAware = func;
  if (syncAware.sync) {
    const syncAwareWrapped = wrapped;
    syncAwareWrapped.sync = true;
    return syncAwareWrapped;
  }
  return wrapped;
}

/**
 * Accepts a function that contains imperative code which runs whenever any of
 * the accessed _reactive_ properties (e.g., values from the global state or the
 * context) is modified.
 *
 * This hook makes the element's scope available so functions like
 * `getElement()` and `getContext()` can be used inside the passed callback.
 *
 * @param callback The hook callback.
 */
function useWatch(callback) {
  utils_useSignalEffect(withScope(callback));
}

/**
 * Accepts a function that contains imperative code which runs only after the
 * element's first render, mainly useful for initialization logic.
 *
 * This hook makes the element's scope available so functions like
 * `getElement()` and `getContext()` can be used inside the passed callback.
 *
 * @param callback The hook callback.
 */
function useInit(callback) {
  y(withScope(callback), []);
}

/**
 * Accepts a function that contains imperative, possibly effectful code. The
 * effects run after browser paint, without blocking it.
 *
 * This hook is equivalent to Preact's `useEffect` and makes the element's scope
 * available so functions like `getElement()` and `getContext()` can be used
 * inside the passed callback.
 *
 * @param callback Imperative function that can return a cleanup
 *                 function.
 * @param inputs   If present, effect will only activate if the
 *                 values in the list change (using `===`).
 */
function useEffect(callback, inputs) {
  y(withScope(callback), inputs);
}

/**
 * Accepts a function that contains imperative, possibly effectful code. Use
 * this to read layout from the DOM and synchronously re-render.
 *
 * This hook is equivalent to Preact's `useLayoutEffect` and makes the element's
 * scope available so functions like `getElement()` and `getContext()` can be
 * used inside the passed callback.
 *
 * @param callback Imperative function that can return a cleanup
 *                 function.
 * @param inputs   If present, effect will only activate if the
 *                 values in the list change (using `===`).
 */
function useLayoutEffect(callback, inputs) {
  _(withScope(callback), inputs);
}

/**
 * Returns a memoized version of the callback that only changes if one of the
 * inputs has changed (using `===`).
 *
 * This hook is equivalent to Preact's `useCallback` and makes the element's
 * scope available so functions like `getElement()` and `getContext()` can be
 * used inside the passed callback.
 *
 * @param callback Callback function.
 * @param inputs   If present, the callback will only be updated if the
 *                 values in the list change (using `===`).
 *
 * @return The callback function.
 */
function useCallback(callback, inputs) {
  return q(withScope(callback), inputs);
}

/**
 * Pass a factory function and an array of inputs. `useMemo` will only recompute
 * the memoized value when one of the inputs has changed.
 *
 * This hook is equivalent to Preact's `useMemo` and makes the element's scope
 * available so functions like `getElement()` and `getContext()` can be used
 * inside the passed factory function.
 *
 * @param factory Factory function that returns that value for memoization.
 * @param inputs  If present, the factory will only be run to recompute if
 *                the values in the list change (using `===`).
 *
 * @return The memoized value.
 */
function useMemo(factory, inputs) {
  return T(withScope(factory), inputs);
}

/**
 * Creates a root fragment by replacing a node or an array of nodes in a parent element.
 * For wrapperless hydration.
 * See https://gist.github.com/developit/f4c67a2ede71dc2fab7f357f39cff28c
 *
 * @param parent      The parent element where the nodes will be replaced.
 * @param replaceNode The node or array of nodes to replace in the parent element.
 * @return The created root fragment.
 */
const createRootFragment = (parent, replaceNode) => {
  replaceNode = [].concat(replaceNode);
  const sibling = replaceNode[replaceNode.length - 1].nextSibling;
  function insert(child, root) {
    parent.insertBefore(child, root || sibling);
  }
  return parent.__k = {
    nodeType: 1,
    parentNode: parent,
    firstChild: replaceNode[0],
    childNodes: replaceNode,
    insertBefore: insert,
    appendChild: insert,
    removeChild(c) {
      parent.removeChild(c);
    }
  };
};

/**
 * Transforms a kebab-case string to camelCase.
 *
 * @param str The kebab-case string to transform to camelCase.
 * @return The transformed camelCase string.
 */
function kebabToCamelCase(str) {
  return str.replace(/^-+|-+$/g, '').toLowerCase().replace(/-([a-z])/g, function (_match, group1) {
    return group1.toUpperCase();
  });
}
const logged = new Set();

/**
 * Shows a warning with `message` if environment is not `production`.
 *
 * Based on the `@wordpress/warning` package.
 *
 * @param message Message to show in the warning.
 */
const warn = message => {
  if (true) {
    if (logged.has(message)) {
      return;
    }

    // eslint-disable-next-line no-console
    console.warn(message);

    // Throwing an error and catching it immediately to improve debugging
    // A consumer can use 'pause on caught exceptions'
    try {
      throw Error(message);
    } catch (e) {
      // Do nothing.
    }
    logged.add(message);
  }
};

/**
 * Checks if the passed `candidate` is a plain object with just the `Object`
 * prototype.
 *
 * @param candidate The item to check.
 * @return Whether `candidate` is a plain object.
 */
const isPlainObject = candidate => Boolean(candidate && typeof candidate === 'object' && candidate.constructor === Object);

/**
 * Indicates that the passed `callback` requires synchronous access to the event object.
 *
 * @param callback The event callback.
 * @return Altered event callback.
 */
function withSyncEvent(callback) {
  const syncAware = callback;
  syncAware.sync = true;
  return syncAware;
}

;// ./node_modules/@wordpress/interactivity/build-module/proxies/registry.js
/**
 * Proxies for each object.
 */
const objToProxy = new WeakMap();
const proxyToObj = new WeakMap();

/**
 * Namespaces for each created proxy.
 */
const proxyToNs = new WeakMap();

/**
 * Object types that can be proxied.
 */
const supported = new Set([Object, Array]);

/**
 * Returns a proxy to the passed object with the given handlers, assigning the
 * specified namespace to it. If a proxy for the passed object was created
 * before, that proxy is returned.
 *
 * @param namespace The namespace that will be associated to this proxy.
 * @param obj       The object to proxify.
 * @param handlers  Handlers that the proxy will use.
 *
 * @throws Error if the object cannot be proxified. Use {@link shouldProxy} to
 *         check if a proxy can be created for a specific object.
 *
 * @return The created proxy.
 */
const createProxy = (namespace, obj, handlers) => {
  if (!shouldProxy(obj)) {
    throw Error('This object cannot be proxified.');
  }
  if (!objToProxy.has(obj)) {
    const proxy = new Proxy(obj, handlers);
    objToProxy.set(obj, proxy);
    proxyToObj.set(proxy, obj);
    proxyToNs.set(proxy, namespace);
  }
  return objToProxy.get(obj);
};

/**
 * Returns the proxy for the given object. If there is no associated proxy, the
 * function returns `undefined`.
 *
 * @param obj Object from which to know the proxy.
 * @return Associated proxy or `undefined`.
 */
const getProxyFromObject = obj => objToProxy.get(obj);

/**
 * Gets the namespace associated with the given proxy.
 *
 * Proxies have a namespace assigned upon creation. See {@link createProxy}.
 *
 * @param proxy Proxy.
 * @return Namespace.
 */
const getNamespaceFromProxy = proxy => proxyToNs.get(proxy);

/**
 * Checks if a given object can be proxied.
 *
 * @param candidate Object to know whether it can be proxied.
 * @return True if the passed instance can be proxied.
 */
const shouldProxy = candidate => {
  if (typeof candidate !== 'object' || candidate === null) {
    return false;
  }
  return !proxyToNs.has(candidate) && supported.has(candidate.constructor);
};

/**
 * Returns the target object for the passed proxy. If the passed object is not a registered proxy, the
 * function returns `undefined`.
 *
 * @param proxy Proxy from which to know the target.
 * @return The target object or `undefined`.
 */
const getObjectFromProxy = proxy => proxyToObj.get(proxy);

;// ./node_modules/@wordpress/interactivity/build-module/proxies/signals.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */





/**
 * Identifier for property computeds not associated to any scope.
 */
const NO_SCOPE = {};

/**
 * Structure that manages reactivity for a property in a state object. It uses
 * signals to keep track of property value or getter modifications.
 */
class PropSignal {
  /**
   * Proxy that holds the property this PropSignal is associated with.
   */

  /**
   * Relation of computeds by scope. These computeds are read-only signals
   * that depend on whether the property is a value or a getter and,
   * therefore, can return different values depending on the scope in which
   * the getter is accessed.
   */

  /**
   * Signal with the value assigned to the related property.
   */

  /**
   * Signal with the getter assigned to the related property.
   */

  /**
   * Structure that manages reactivity for a property in a state object, using
   * signals to keep track of property value or getter modifications.
   *
   * @param owner Proxy that holds the property this instance is associated
   *              with.
   */
  constructor(owner) {
    this.owner = owner;
    this.computedsByScope = new WeakMap();
  }

  /**
   * Changes the internal value. If a getter was set before, it is set to
   * `undefined`.
   *
   * @param value New value.
   */
  setValue(value) {
    this.update({
      value
    });
  }

  /**
   * Changes the internal getter. If a value was set before, it is set to
   * `undefined`.
   *
   * @param getter New getter.
   */
  setGetter(getter) {
    this.update({
      get: getter
    });
  }

  /**
   * Returns the computed that holds the result of evaluating the prop in the
   * current scope.
   *
   * These computeds are read-only signals that depend on whether the property
   * is a value or a getter and, therefore, can return different values
   * depending on the scope in which the getter is accessed.
   *
   * @return Computed that depends on the scope.
   */
  getComputed() {
    const scope = getScope() || NO_SCOPE;
    if (!this.valueSignal && !this.getterSignal) {
      this.update({});
    }
    if (!this.computedsByScope.has(scope)) {
      const callback = () => {
        const getter = this.getterSignal?.value;
        return getter ? getter.call(this.owner) : this.valueSignal?.value;
      };
      setNamespace(getNamespaceFromProxy(this.owner));
      this.computedsByScope.set(scope, signals_core_module_w(withScope(callback)));
      resetNamespace();
    }
    return this.computedsByScope.get(scope);
  }

  /**
   *  Update the internal signals for the value and the getter of the
   *  corresponding prop.
   *
   * @param param0
   * @param param0.get   New getter.
   * @param param0.value New value.
   */
  update({
    get,
    value
  }) {
    if (!this.valueSignal) {
      this.valueSignal = signals_core_module_d(value);
      this.getterSignal = signals_core_module_d(get);
    } else if (value !== this.valueSignal.peek() || get !== this.getterSignal.peek()) {
      signals_core_module_r(() => {
        this.valueSignal.value = value;
        this.getterSignal.value = get;
      });
    }
  }
}

;// ./node_modules/@wordpress/interactivity/build-module/proxies/state.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */





/**
 * Set of built-in symbols.
 */
const wellKnownSymbols = new Set(Object.getOwnPropertyNames(Symbol).map(key => Symbol[key]).filter(value => typeof value === 'symbol'));

/**
 * Relates each proxy with a map of {@link PropSignal} instances, representing
 * the proxy's accessed properties.
 */
const proxyToProps = new WeakMap();

/**
 *  Checks whether a {@link PropSignal | `PropSignal`} instance exists for the
 *  given property in the passed proxy.
 *
 * @param proxy Proxy of a state object or array.
 * @param key   The property key.
 * @return `true` when it exists; false otherwise.
 */
const hasPropSignal = (proxy, key) => proxyToProps.has(proxy) && proxyToProps.get(proxy).has(key);
const readOnlyProxies = new WeakSet();

/**
 * Returns the {@link PropSignal | `PropSignal`} instance associated with the
 * specified prop in the passed proxy.
 *
 * The `PropSignal` instance is generated if it doesn't exist yet, using the
 * `initial` parameter to initialize the internal signals.
 *
 * @param proxy   Proxy of a state object or array.
 * @param key     The property key.
 * @param initial Initial data for the `PropSignal` instance.
 * @return The `PropSignal` instance.
 */
const getPropSignal = (proxy, key, initial) => {
  if (!proxyToProps.has(proxy)) {
    proxyToProps.set(proxy, new Map());
  }
  key = typeof key === 'number' ? `${key}` : key;
  const props = proxyToProps.get(proxy);
  if (!props.has(key)) {
    const ns = getNamespaceFromProxy(proxy);
    const prop = new PropSignal(proxy);
    props.set(key, prop);
    if (initial) {
      const {
        get,
        value
      } = initial;
      if (get) {
        prop.setGetter(get);
      } else {
        const readOnly = readOnlyProxies.has(proxy);
        prop.setValue(shouldProxy(value) ? proxifyState(ns, value, {
          readOnly
        }) : value);
      }
    }
  }
  return props.get(key);
};

/**
 * Relates each proxied object (i.e., the original object) with a signal that
 * tracks changes in the number of properties.
 */
const objToIterable = new WeakMap();

/**
 * When this flag is `true`, it avoids any signal subscription, overriding state
 * props' "reactive" behavior.
 */
let peeking = false;

/**
 * Handlers for reactive objects and arrays in the state.
 */
const stateHandlers = {
  get(target, key, receiver) {
    /*
     * The property should not be reactive for the following cases:
     * 1. While using the `peek` function to read the property.
     * 2. The property exists but comes from the Object or Array prototypes.
     * 3. The property key is a known symbol.
     */
    if (peeking || !target.hasOwnProperty(key) && key in target || typeof key === 'symbol' && wellKnownSymbols.has(key)) {
      return Reflect.get(target, key, receiver);
    }

    // At this point, the property should be reactive.
    const desc = Object.getOwnPropertyDescriptor(target, key);
    const prop = getPropSignal(receiver, key, desc);
    const result = prop.getComputed().value;

    /*
     * Check if the property is a synchronous function. If it is, set the
     * default namespace. Synchronous functions always run in the proper scope,
     * which is set by the Directives component.
     */
    if (typeof result === 'function') {
      const ns = getNamespaceFromProxy(receiver);
      return (...args) => {
        setNamespace(ns);
        try {
          return result.call(receiver, ...args);
        } finally {
          resetNamespace();
        }
      };
    }
    return result;
  },
  set(target, key, value, receiver) {
    if (readOnlyProxies.has(receiver)) {
      return false;
    }
    setNamespace(getNamespaceFromProxy(receiver));
    try {
      return Reflect.set(target, key, value, receiver);
    } finally {
      resetNamespace();
    }
  },
  defineProperty(target, key, desc) {
    if (readOnlyProxies.has(getProxyFromObject(target))) {
      return false;
    }
    const isNew = !(key in target);
    const result = Reflect.defineProperty(target, key, desc);
    if (result) {
      const receiver = getProxyFromObject(target);
      const prop = getPropSignal(receiver, key);
      const {
        get,
        value
      } = desc;
      if (get) {
        prop.setGetter(get);
      } else {
        const ns = getNamespaceFromProxy(receiver);
        prop.setValue(shouldProxy(value) ? proxifyState(ns, value) : value);
      }
      if (isNew && objToIterable.has(target)) {
        objToIterable.get(target).value++;
      }

      /*
       * Modify the `length` property value only if the related
       * `PropSignal` exists, which means that there are subscriptions to
       * this property.
       */
      if (Array.isArray(target) && proxyToProps.get(receiver)?.has('length')) {
        const length = getPropSignal(receiver, 'length');
        length.setValue(target.length);
      }
    }
    return result;
  },
  deleteProperty(target, key) {
    if (readOnlyProxies.has(getProxyFromObject(target))) {
      return false;
    }
    const result = Reflect.deleteProperty(target, key);
    if (result) {
      const prop = getPropSignal(getProxyFromObject(target), key);
      prop.setValue(undefined);
      if (objToIterable.has(target)) {
        objToIterable.get(target).value++;
      }
    }
    return result;
  },
  ownKeys(target) {
    if (!objToIterable.has(target)) {
      objToIterable.set(target, signals_core_module_d(0));
    }
    /*
     *This subscribes to the signal while preventing the minifier from
     * deleting this line in production.
     */
    objToIterable._ = objToIterable.get(target).value;
    return Reflect.ownKeys(target);
  }
};

/**
 * Returns the proxy associated with the given state object, creating it if it
 * does not exist.
 *
 * @param namespace        The namespace that will be associated to this proxy.
 * @param obj              The object to proxify.
 * @param options          Options.
 * @param options.readOnly Read-only.
 *
 * @throws Error if the object cannot be proxified. Use {@link shouldProxy} to
 *         check if a proxy can be created for a specific object.
 *
 * @return The associated proxy.
 */
const proxifyState = (namespace, obj, options) => {
  const proxy = createProxy(namespace, obj, stateHandlers);
  if (options?.readOnly) {
    readOnlyProxies.add(proxy);
  }
  return proxy;
};

/**
 * Reads the value of the specified property without subscribing to it.
 *
 * @param obj The object to read the property from.
 * @param key The property key.
 * @return The property value.
 */
const peek = (obj, key) => {
  peeking = true;
  try {
    return obj[key];
  } finally {
    peeking = false;
  }
};

/**
 * Internal recursive implementation for {@link deepMerge | `deepMerge`}.
 *
 * @param target   The target object.
 * @param source   The source object containing new values and props.
 * @param override Whether existing props should be overwritten or not (`true`
 *                 by default).
 */
const deepMergeRecursive = (target, source, override = true) => {
  // If target is not a plain object and the source is, we don't need to merge
  // them because the source will be used as the new value of the target.
  if (!(isPlainObject(target) && isPlainObject(source))) {
    return;
  }
  let hasNewKeys = false;
  for (const key in source) {
    const isNew = !(key in target);
    hasNewKeys = hasNewKeys || isNew;
    const desc = Object.getOwnPropertyDescriptor(source, key);
    const proxy = getProxyFromObject(target);
    const propSignal = !!proxy && hasPropSignal(proxy, key) && getPropSignal(proxy, key);

    // Handle getters and setters
    if (typeof desc.get === 'function' || typeof desc.set === 'function') {
      if (override || isNew) {
        // Because we are setting a getter or setter, we need to use
        // Object.defineProperty to define the property on the target object.
        Object.defineProperty(target, key, {
          ...desc,
          configurable: true,
          enumerable: true
        });
        // Update the getter in the property signal if it exists
        if (desc.get && propSignal) {
          propSignal.setGetter(desc.get);
        }
      }

      // Handle nested objects
    } else if (isPlainObject(source[key])) {
      const targetValue = Object.getOwnPropertyDescriptor(target, key)?.value;
      if (isNew || override && !isPlainObject(targetValue)) {
        // Create a new object if the property is new or needs to be overridden
        target[key] = {};
        if (propSignal) {
          // Create a new proxified state for the nested object
          const ns = getNamespaceFromProxy(proxy);
          propSignal.setValue(proxifyState(ns, target[key]));
        }
        deepMergeRecursive(target[key], source[key], override);
      }
      // Both target and source are plain objects, merge them recursively
      else if (isPlainObject(targetValue)) {
        deepMergeRecursive(target[key], source[key], override);
      }

      // Handle primitive values and non-plain objects
    } else if (override || isNew) {
      Object.defineProperty(target, key, desc);
      if (propSignal) {
        const {
          value
        } = desc;
        const ns = getNamespaceFromProxy(proxy);
        // Proxify the value if necessary before setting it in the signal
        propSignal.setValue(shouldProxy(value) ? proxifyState(ns, value) : value);
      }
    }
  }
  if (hasNewKeys && objToIterable.has(target)) {
    objToIterable.get(target).value++;
  }
};

/**
 * Recursively update prop values inside the passed `target` and nested plain
 * objects, using the values present in `source`. References to plain objects
 * are kept, only updating props containing primitives or arrays. Arrays are
 * replaced instead of merged or concatenated.
 *
 * If the `override` parameter is set to `false`, then all values in `target`
 * are preserved, and only new properties from `source` are added.
 *
 * @param target   The target object.
 * @param source   The source object containing new values and props.
 * @param override Whether existing props should be overwritten or not (`true`
 *                 by default).
 */
const deepMerge = (target, source, override = true) => signals_core_module_r(() => deepMergeRecursive(getObjectFromProxy(target) || target, source, override));

;// ./node_modules/@wordpress/interactivity/build-module/proxies/store.js
/**
 * Internal dependencies
 */

/**
 * External dependencies
 */



/**
 * Identifies the store proxies handling the root objects of each store.
 */
const storeRoots = new WeakSet();

/**
 * Handlers for store proxies.
 */
const storeHandlers = {
  get: (target, key, receiver) => {
    const result = Reflect.get(target, key);
    const ns = getNamespaceFromProxy(receiver);

    /*
     * Check if the proxy is the store root and no key with that name exist. In
     * that case, return an empty object for the requested key.
     */
    if (typeof result === 'undefined' && storeRoots.has(receiver)) {
      const obj = {};
      Reflect.set(target, key, obj);
      return proxifyStore(ns, obj, false);
    }

    /*
     * Check if the property is a function. If it is, add the store
     * namespace to the stack and wrap the function with the current scope.
     * The `withScope` util handles both synchronous functions and generator
     * functions.
     */
    if (typeof result === 'function') {
      setNamespace(ns);
      const scoped = withScope(result);
      resetNamespace();
      return scoped;
    }

    // Check if the property is an object. If it is, proxyify it.
    if (isPlainObject(result) && shouldProxy(result)) {
      return proxifyStore(ns, result, false);
    }
    return result;
  }
};

/**
 * Returns the proxy associated with the given store object, creating it if it
 * does not exist.
 *
 * @param namespace The namespace that will be associated to this proxy.
 * @param obj       The object to proxify.
 *
 * @param isRoot    Whether the passed object is the store root object.
 * @throws Error if the object cannot be proxified. Use {@link shouldProxy} to
 *         check if a proxy can be created for a specific object.
 *
 * @return The associated proxy.
 */
const proxifyStore = (namespace, obj, isRoot = true) => {
  const proxy = createProxy(namespace, obj, storeHandlers);
  if (proxy && isRoot) {
    storeRoots.add(proxy);
  }
  return proxy;
};

;// ./node_modules/@wordpress/interactivity/build-module/proxies/context.js
const contextObjectToProxy = new WeakMap();
const contextObjectToFallback = new WeakMap();
const contextProxies = new WeakSet();
const descriptor = Reflect.getOwnPropertyDescriptor;

// TODO: Use the proxy registry to avoid multiple proxies on the same object.
const contextHandlers = {
  get: (target, key) => {
    const fallback = contextObjectToFallback.get(target);
    // Always subscribe to prop changes in the current context.
    const currentProp = target[key];

    /*
     * Return the value from `target` if it exists, or from `fallback`
     * otherwise. This way, in the case the property doesn't exist either in
     * `target` or `fallback`, it also subscribes to changes in the parent
     * context.
     */
    return key in target ? currentProp : fallback[key];
  },
  set: (target, key, value) => {
    const fallback = contextObjectToFallback.get(target);

    // If the property exists in the current context, modify it. Otherwise,
    // add it to the current context.
    const obj = key in target || !(key in fallback) ? target : fallback;
    obj[key] = value;
    return true;
  },
  ownKeys: target => [...new Set([...Object.keys(contextObjectToFallback.get(target)), ...Object.keys(target)])],
  getOwnPropertyDescriptor: (target, key) => descriptor(target, key) || descriptor(contextObjectToFallback.get(target), key),
  has: (target, key) => Reflect.has(target, key) || Reflect.has(contextObjectToFallback.get(target), key)
};

/**
 * Wrap a context object with a proxy to reproduce the context stack. The proxy
 * uses the passed `inherited` context as a fallback to look up for properties
 * that don't exist in the given context. Also, updated properties are modified
 * where they are defined, or added to the main context when they don't exist.
 *
 * @param current   Current context.
 * @param inherited Inherited context, used as fallback.
 *
 * @return The wrapped context object.
 */
const proxifyContext = (current, inherited = {}) => {
  if (contextProxies.has(current)) {
    throw Error('This object cannot be proxified.');
  }
  // Update the fallback object reference when it changes.
  contextObjectToFallback.set(current, inherited);
  if (!contextObjectToProxy.has(current)) {
    const proxy = new Proxy(current, contextHandlers);
    contextObjectToProxy.set(current, proxy);
    contextProxies.add(proxy);
  }
  return contextObjectToProxy.get(current);
};

;// ./node_modules/@wordpress/interactivity/build-module/proxies/index.js
/**
 * Internal dependencies
 */




;// ./node_modules/@wordpress/interactivity/build-module/store.js
/**
 * Internal dependencies
 */

/**
 * External dependencies
 */


const stores = new Map();
const rawStores = new Map();
const storeLocks = new Map();
const storeConfigs = new Map();
const serverStates = new Map();

/**
 * Get the defined config for the store with the passed namespace.
 *
 * @param namespace Store's namespace from which to retrieve the config.
 * @return Defined config for the given namespace.
 */
const getConfig = namespace => storeConfigs.get(namespace || getNamespace()) || {};

/**
 * Get the part of the state defined and updated from the server.
 *
 * The object returned is read-only, and includes the state defined in PHP with
 * `wp_interactivity_state()`. When using `actions.navigate()`, this object is
 * updated to reflect the changes in its properties, without affecting the state
 * returned by `store()`. Directives can subscribe to those changes to update
 * the state if needed.
 *
 * @example
 * ```js
 *  const { state } = store('myStore', {
 *    callbacks: {
 *      updateServerState() {
 *        const serverState = getServerState();
 *        // Override some property with the new value that came from the server.
 *        state.overridableProp = serverState.overridableProp;
 *      },
 *    },
 *  });
 * ```
 *
 * @param namespace Store's namespace from which to retrieve the server state.
 * @return The server state for the given namespace.
 */
const getServerState = namespace => {
  const ns = namespace || getNamespace();
  if (!serverStates.has(ns)) {
    serverStates.set(ns, proxifyState(ns, {}, {
      readOnly: true
    }));
  }
  return serverStates.get(ns);
};
const universalUnlock = 'I acknowledge that using a private store means my plugin will inevitably break on the next store release.';

/**
 * Extends the Interactivity API global store adding the passed properties to
 * the given namespace. It also returns stable references to the namespace
 * content.
 *
 * These props typically consist of `state`, which is the reactive part of the
 * store ― which means that any directive referencing a state property will be
 * re-rendered anytime it changes ― and function properties like `actions` and
 * `callbacks`, mostly used for event handlers. These props can then be
 * referenced by any directive to make the HTML interactive.
 *
 * @example
 * ```js
 *  const { state } = store( 'counter', {
 *    state: {
 *      value: 0,
 *      get double() { return state.value * 2; },
 *    },
 *    actions: {
 *      increment() {
 *        state.value += 1;
 *      },
 *    },
 *  } );
 * ```
 *
 * The code from the example above allows blocks to subscribe and interact with
 * the store by using directives in the HTML, e.g.:
 *
 * ```html
 * <div data-wp-interactive="counter">
 *   <button
 *     data-wp-text="state.double"
 *     data-wp-on--click="actions.increment"
 *   >
 *     0
 *   </button>
 * </div>
 * ```
 * @param namespace The store namespace to interact with.
 * @param storePart Properties to add to the store namespace.
 * @param options   Options for the given namespace.
 *
 * @return A reference to the namespace content.
 */

// Overload for when the types are inferred.

// Overload for when types are passed via generics and they contain state.

// Overload for when types are passed via generics and they don't contain state.

// Overload for when types are divided into multiple parts.

function store(namespace, {
  state = {},
  ...block
} = {}, {
  lock = false
} = {}) {
  if (!stores.has(namespace)) {
    // Lock the store if the passed lock is different from the universal
    // unlock. Once the lock is set (either false, true, or a given string),
    // it cannot change.
    if (lock !== universalUnlock) {
      storeLocks.set(namespace, lock);
    }
    const rawStore = {
      state: proxifyState(namespace, isPlainObject(state) ? state : {}),
      ...block
    };
    const proxifiedStore = proxifyStore(namespace, rawStore);
    rawStores.set(namespace, rawStore);
    stores.set(namespace, proxifiedStore);
  } else {
    // Lock the store if it wasn't locked yet and the passed lock is
    // different from the universal unlock. If no lock is given, the store
    // will be public and won't accept any lock from now on.
    if (lock !== universalUnlock && !storeLocks.has(namespace)) {
      storeLocks.set(namespace, lock);
    } else {
      const storeLock = storeLocks.get(namespace);
      const isLockValid = lock === universalUnlock || lock !== true && lock === storeLock;
      if (!isLockValid) {
        if (!storeLock) {
          throw Error('Cannot lock a public store');
        } else {
          throw Error('Cannot unlock a private store with an invalid lock code');
        }
      }
    }
    const target = rawStores.get(namespace);
    deepMerge(target, block);
    deepMerge(target.state, state);
  }
  return stores.get(namespace);
}
const parseServerData = (dom = document) => {
  var _dom$getElementById;
  const jsonDataScriptTag = // Preferred Script Module data passing form
  (_dom$getElementById = dom.getElementById('wp-script-module-data-@wordpress/interactivity')) !== null && _dom$getElementById !== void 0 ? _dom$getElementById :
  // Legacy form
  dom.getElementById('wp-interactivity-data');
  if (jsonDataScriptTag?.textContent) {
    try {
      return JSON.parse(jsonDataScriptTag.textContent);
    } catch {}
  }
  return {};
};
const populateServerData = data => {
  if (isPlainObject(data?.state)) {
    Object.entries(data.state).forEach(([namespace, state]) => {
      const st = store(namespace, {}, {
        lock: universalUnlock
      });
      deepMerge(st.state, state, false);
      deepMerge(getServerState(namespace), state);
    });
  }
  if (isPlainObject(data?.config)) {
    Object.entries(data.config).forEach(([namespace, config]) => {
      storeConfigs.set(namespace, config);
    });
  }
};

// Parse and populate the initial state and config.
const data = parseServerData();
populateServerData(data);

;// ./node_modules/@wordpress/interactivity/build-module/hooks.js
// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react-hooks/exhaustive-deps */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



function isNonDefaultDirectiveSuffix(entry) {
  return entry.suffix !== null;
}
function isDefaultDirectiveSuffix(entry) {
  return entry.suffix === null;
}
// Main context.
const context = (0,preact_module/* createContext */.q6)({
  client: {},
  server: {}
});

// WordPress Directives.
const directiveCallbacks = {};
const directivePriorities = {};

/**
 * Register a new directive type in the Interactivity API runtime.
 *
 * @example
 * ```js
 * directive(
 *   'alert', // Name without the `data-wp-` prefix.
 *   ( { directives: { alert }, element, evaluate } ) => {
 *     const defaultEntry = alert.find( isDefaultDirectiveSuffix );
 *     element.props.onclick = () => { alert( evaluate( defaultEntry ) ); }
 *   }
 * )
 * ```
 *
 * The previous code registers a custom directive type for displaying an alert
 * message whenever an element using it is clicked. The message text is obtained
 * from the store under the inherited namespace, using `evaluate`.
 *
 * When the HTML is processed by the Interactivity API, any element containing
 * the `data-wp-alert` directive will have the `onclick` event handler, e.g.,
 *
 * ```html
 * <div data-wp-interactive="messages">
 *   <button data-wp-alert="state.alert">Click me!</button>
 * </div>
 * ```
 * Note that, in the previous example, the directive callback gets the path
 * value (`state.alert`) from the directive entry with suffix `null`. A
 * custom suffix can also be specified by appending `--` to the directive
 * attribute, followed by the suffix, like in the following HTML snippet:
 *
 * ```html
 * <div data-wp-interactive="myblock">
 *   <button
 *     data-wp-color--text="state.text"
 *     data-wp-color--background="state.background"
 *   >Click me!</button>
 * </div>
 * ```
 *
 * This could be an hypothetical implementation of the custom directive used in
 * the snippet above.
 *
 * @example
 * ```js
 * directive(
 *   'color', // Name without prefix and suffix.
 *   ( { directives: { color: colors }, ref, evaluate } ) =>
 *     colors.forEach( ( color ) => {
 *       if ( color.suffix = 'text' ) {
 *         ref.style.setProperty(
 *           'color',
 *           evaluate( color.text )
 *         );
 *       }
 *       if ( color.suffix = 'background' ) {
 *         ref.style.setProperty(
 *           'background-color',
 *           evaluate( color.background )
 *         );
 *       }
 *     } );
 *   }
 * )
 * ```
 *
 * @param name             Directive name, without the `data-wp-` prefix.
 * @param callback         Function that runs the directive logic.
 * @param options          Options object.
 * @param options.priority Option to control the directive execution order. The
 *                         lesser, the highest priority. Default is `10`.
 */
const directive = (name, callback, {
  priority = 10
} = {}) => {
  directiveCallbacks[name] = callback;
  directivePriorities[name] = priority;
};

// Resolve the path to some property of the store object.
const resolve = (path, namespace) => {
  if (!namespace) {
    warn(`Namespace missing for "${path}". The value for that path won't be resolved.`);
    return;
  }
  let resolvedStore = stores.get(namespace);
  if (typeof resolvedStore === 'undefined') {
    resolvedStore = store(namespace, {}, {
      lock: universalUnlock
    });
  }
  const current = {
    ...resolvedStore,
    context: getScope().context[namespace]
  };
  try {
    // TODO: Support lazy/dynamically initialized stores
    return path.split('.').reduce((acc, key) => acc[key], current);
  } catch (e) {}
};

// Generate the evaluate function.
const getEvaluate = ({
  scope
}) =>
// TODO: When removing the temporarily remaining `value( ...args )` call below, remove the `...args` parameter too.
(entry, ...args) => {
  let {
    value: path,
    namespace
  } = entry;
  if (typeof path !== 'string') {
    throw new Error('The `value` prop should be a string path');
  }
  // If path starts with !, remove it and save a flag.
  const hasNegationOperator = path[0] === '!' && !!(path = path.slice(1));
  setScope(scope);
  const value = resolve(path, namespace);
  // Functions are returned without invoking them.
  if (typeof value === 'function') {
    // Except if they have a negation operator present, for backward compatibility.
    // This pattern is strongly discouraged and deprecated, and it will be removed in a near future release.
    // TODO: Remove this condition to effectively ignore negation operator when provided with a function.
    if (hasNegationOperator) {
      warn('Using a function with a negation operator is deprecated and will stop working in WordPress 6.9. Please use derived state instead.');
      const functionResult = !value(...args);
      resetScope();
      return functionResult;
    }
    // Reset scope before return and wrap the function so it will still run within the correct scope.
    resetScope();
    return (...functionArgs) => {
      setScope(scope);
      const functionResult = value(...functionArgs);
      resetScope();
      return functionResult;
    };
  }
  const result = value;
  resetScope();
  return hasNegationOperator ? !result : result;
};

// Separate directives by priority. The resulting array contains objects
// of directives grouped by same priority, and sorted in ascending order.
const getPriorityLevels = directives => {
  const byPriority = Object.keys(directives).reduce((obj, name) => {
    if (directiveCallbacks[name]) {
      const priority = directivePriorities[name];
      (obj[priority] = obj[priority] || []).push(name);
    }
    return obj;
  }, {});
  return Object.entries(byPriority).sort(([p1], [p2]) => parseInt(p1) - parseInt(p2)).map(([, arr]) => arr);
};

// Component that wraps each priority level of directives of an element.
const Directives = ({
  directives,
  priorityLevels: [currentPriorityLevel, ...nextPriorityLevels],
  element,
  originalProps,
  previousScope
}) => {
  // Initialize the scope of this element. These scopes are different per each
  // level because each level has a different context, but they share the same
  // element ref, state and props.
  const scope = A({}).current;
  scope.evaluate = q(getEvaluate({
    scope
  }), []);
  const {
    client,
    server
  } = x(context);
  scope.context = client;
  scope.serverContext = server;
  /* eslint-disable react-hooks/rules-of-hooks */
  scope.ref = previousScope?.ref || A(null);
  /* eslint-enable react-hooks/rules-of-hooks */

  // Create a fresh copy of the vnode element and add the props to the scope,
  // named as attributes (HTML Attributes).
  element = (0,preact_module/* cloneElement */.Ob)(element, {
    ref: scope.ref
  });
  scope.attributes = element.props;

  // Recursively render the wrapper for the next priority level.
  const children = nextPriorityLevels.length > 0 ? (0,preact_module.h)(Directives, {
    directives,
    priorityLevels: nextPriorityLevels,
    element,
    originalProps,
    previousScope: scope
  }) : element;
  const props = {
    ...originalProps,
    children
  };
  const directiveArgs = {
    directives,
    props,
    element,
    context,
    evaluate: scope.evaluate
  };
  setScope(scope);
  for (const directiveName of currentPriorityLevel) {
    const wrapper = directiveCallbacks[directiveName]?.(directiveArgs);
    if (wrapper !== undefined) {
      props.children = wrapper;
    }
  }
  resetScope();
  return props.children;
};

// Preact Options Hook called each time a vnode is created.
const old = preact_module/* options */.fF.vnode;
preact_module/* options */.fF.vnode = vnode => {
  if (vnode.props.__directives) {
    const props = vnode.props;
    const directives = props.__directives;
    if (directives.key) {
      vnode.key = directives.key.find(isDefaultDirectiveSuffix).value;
    }
    delete props.__directives;
    const priorityLevels = getPriorityLevels(directives);
    if (priorityLevels.length > 0) {
      vnode.props = {
        directives,
        priorityLevels,
        originalProps: props,
        type: vnode.type,
        element: (0,preact_module.h)(vnode.type, props),
        top: true
      };
      vnode.type = Directives;
    }
  }
  if (old) {
    old(vnode);
  }
};

;// ./node_modules/@wordpress/interactivity/build-module/directives.js
// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react-hooks/exhaustive-deps */

/**
 * External dependencies
 */



/**
 * Internal dependencies
 */





/**
 * Recursively clone the passed object.
 *
 * @param source Source object.
 * @return Cloned object.
 */
function deepClone(source) {
  if (isPlainObject(source)) {
    return Object.fromEntries(Object.entries(source).map(([key, value]) => [key, deepClone(value)]));
  }
  if (Array.isArray(source)) {
    return source.map(i => deepClone(i));
  }
  return source;
}

/**
 * Wraps event object to warn about access of synchronous properties and methods.
 *
 * For all store actions attached to an event listener the event object is proxied via this function, unless the action
 * uses the `withSyncEvent()` utility to indicate that it requires synchronous access to the event object.
 *
 * At the moment, the proxied event only emits warnings when synchronous properties or methods are being accessed. In
 * the future this will be changed and result in an error. The current temporary behavior allows implementers to update
 * their relevant actions to use `withSyncEvent()`.
 *
 * For additional context, see https://github.com/WordPress/gutenberg/issues/64944.
 *
 * @param event Event object.
 * @return Proxied event object.
 */
function wrapEventAsync(event) {
  const handler = {
    get(target, prop, receiver) {
      const value = target[prop];
      switch (prop) {
        case 'currentTarget':
          warn(`Accessing the synchronous event.${prop} property in a store action without wrapping it in withSyncEvent() is deprecated and will stop working in WordPress 6.9. Please wrap the store action in withSyncEvent().`);
          break;
        case 'preventDefault':
        case 'stopImmediatePropagation':
        case 'stopPropagation':
          warn(`Using the synchronous event.${prop}() function in a store action without wrapping it in withSyncEvent() is deprecated and will stop working in WordPress 6.9. Please wrap the store action in withSyncEvent().`);
          break;
      }
      if (value instanceof Function) {
        return function (...args) {
          return value.apply(this === receiver ? target : this, args);
        };
      }
      return value;
    }
  };
  return new Proxy(event, handler);
}
const newRule = /(?:([\u0080-\uFFFF\w-%@]+) *:? *([^{;]+?);|([^;}{]*?) *{)|(}\s*)/g;
const ruleClean = /\/\*[^]*?\*\/|  +/g;
const ruleNewline = /\n+/g;
const empty = ' ';

/**
 * Convert a css style string into a object.
 *
 * Made by Cristian Bote (@cristianbote) for Goober.
 * https://unpkg.com/browse/goober@2.1.13/src/core/astish.js
 *
 * @param val CSS string.
 * @return CSS object.
 */
const cssStringToObject = val => {
  const tree = [{}];
  let block, left;
  while (block = newRule.exec(val.replace(ruleClean, ''))) {
    if (block[4]) {
      tree.shift();
    } else if (block[3]) {
      left = block[3].replace(ruleNewline, empty).trim();
      tree.unshift(tree[0][left] = tree[0][left] || {});
    } else {
      tree[0][block[1]] = block[2].replace(ruleNewline, empty).trim();
    }
  }
  return tree[0];
};

/**
 * Creates a directive that adds an event listener to the global window or
 * document object.
 *
 * @param type 'window' or 'document'
 */
const getGlobalEventDirective = type => {
  return ({
    directives,
    evaluate
  }) => {
    directives[`on-${type}`].filter(isNonDefaultDirectiveSuffix).forEach(entry => {
      const eventName = entry.suffix.split('--', 1)[0];
      useInit(() => {
        const cb = event => {
          const result = evaluate(entry);
          if (typeof result === 'function') {
            if (!result?.sync) {
              event = wrapEventAsync(event);
            }
            result(event);
          }
        };
        const globalVar = type === 'window' ? window : document;
        globalVar.addEventListener(eventName, cb);
        return () => globalVar.removeEventListener(eventName, cb);
      });
    });
  };
};

/**
 * Creates a directive that adds an async event listener to the global window or
 * document object.
 *
 * @param type 'window' or 'document'
 */
const getGlobalAsyncEventDirective = type => {
  return ({
    directives,
    evaluate
  }) => {
    directives[`on-async-${type}`].filter(isNonDefaultDirectiveSuffix).forEach(entry => {
      const eventName = entry.suffix.split('--', 1)[0];
      useInit(() => {
        const cb = async event => {
          await splitTask();
          const result = evaluate(entry);
          if (typeof result === 'function') {
            result(event);
          }
        };
        const globalVar = type === 'window' ? window : document;
        globalVar.addEventListener(eventName, cb, {
          passive: true
        });
        return () => globalVar.removeEventListener(eventName, cb);
      });
    });
  };
};
/* harmony default export */ const directives = (() => {
  // data-wp-context
  directive('context', ({
    directives: {
      context
    },
    props: {
      children
    },
    context: inheritedContext
  }) => {
    const {
      Provider
    } = inheritedContext;
    const defaultEntry = context.find(isDefaultDirectiveSuffix);
    const {
      client: inheritedClient,
      server: inheritedServer
    } = x(inheritedContext);
    const ns = defaultEntry.namespace;
    const client = A(proxifyState(ns, {}));
    const server = A(proxifyState(ns, {}, {
      readOnly: true
    }));

    // No change should be made if `defaultEntry` does not exist.
    const contextStack = T(() => {
      const result = {
        client: {
          ...inheritedClient
        },
        server: {
          ...inheritedServer
        }
      };
      if (defaultEntry) {
        const {
          namespace,
          value
        } = defaultEntry;
        // Check that the value is a JSON object. Send a console warning if not.
        if (!isPlainObject(value)) {
          warn(`The value of data-wp-context in "${namespace}" store must be a valid stringified JSON object.`);
        }
        deepMerge(client.current, deepClone(value), false);
        deepMerge(server.current, deepClone(value));
        result.client[namespace] = proxifyContext(client.current, inheritedClient[namespace]);
        result.server[namespace] = proxifyContext(server.current, inheritedServer[namespace]);
      }
      return result;
    }, [defaultEntry, inheritedClient, inheritedServer]);
    return (0,preact_module.h)(Provider, {
      value: contextStack
    }, children);
  }, {
    priority: 5
  });

  // data-wp-watch--[name]
  directive('watch', ({
    directives: {
      watch
    },
    evaluate
  }) => {
    watch.forEach(entry => {
      useWatch(() => {
        let start;
        if (false) {}
        let result = evaluate(entry);
        if (typeof result === 'function') {
          result = result();
        }
        if (false) {}
        return result;
      });
    });
  });

  // data-wp-init--[name]
  directive('init', ({
    directives: {
      init
    },
    evaluate
  }) => {
    init.forEach(entry => {
      // TODO: Replace with useEffect to prevent unneeded scopes.
      useInit(() => {
        let start;
        if (false) {}
        let result = evaluate(entry);
        if (typeof result === 'function') {
          result = result();
        }
        if (false) {}
        return result;
      });
    });
  });

  // data-wp-on--[event]
  directive('on', ({
    directives: {
      on
    },
    element,
    evaluate
  }) => {
    const events = new Map();
    on.filter(isNonDefaultDirectiveSuffix).forEach(entry => {
      const event = entry.suffix.split('--')[0];
      if (!events.has(event)) {
        events.set(event, new Set());
      }
      events.get(event).add(entry);
    });
    events.forEach((entries, eventType) => {
      const existingHandler = element.props[`on${eventType}`];
      element.props[`on${eventType}`] = event => {
        entries.forEach(entry => {
          if (existingHandler) {
            existingHandler(event);
          }
          let start;
          if (false) {}
          const result = evaluate(entry);
          if (typeof result === 'function') {
            if (!result?.sync) {
              event = wrapEventAsync(event);
            }
            result(event);
          }
          if (false) {}
        });
      };
    });
  });

  // data-wp-on-async--[event]
  directive('on-async', ({
    directives: {
      'on-async': onAsync
    },
    element,
    evaluate
  }) => {
    const events = new Map();
    onAsync.filter(isNonDefaultDirectiveSuffix).forEach(entry => {
      const event = entry.suffix.split('--')[0];
      if (!events.has(event)) {
        events.set(event, new Set());
      }
      events.get(event).add(entry);
    });
    events.forEach((entries, eventType) => {
      const existingHandler = element.props[`on${eventType}`];
      element.props[`on${eventType}`] = event => {
        if (existingHandler) {
          existingHandler(event);
        }
        entries.forEach(async entry => {
          await splitTask();
          const result = evaluate(entry);
          if (typeof result === 'function') {
            result(event);
          }
        });
      };
    });
  });

  // data-wp-on-window--[event]
  directive('on-window', getGlobalEventDirective('window'));
  // data-wp-on-document--[event]
  directive('on-document', getGlobalEventDirective('document'));

  // data-wp-on-async-window--[event]
  directive('on-async-window', getGlobalAsyncEventDirective('window'));
  // data-wp-on-async-document--[event]
  directive('on-async-document', getGlobalAsyncEventDirective('document'));

  // data-wp-class--[classname]
  directive('class', ({
    directives: {
      class: classNames
    },
    element,
    evaluate
  }) => {
    classNames.filter(isNonDefaultDirectiveSuffix).forEach(entry => {
      const className = entry.suffix;
      let result = evaluate(entry);
      if (typeof result === 'function') {
        result = result();
      }
      const currentClass = element.props.class || '';
      const classFinder = new RegExp(`(^|\\s)${className}(\\s|$)`, 'g');
      if (!result) {
        element.props.class = currentClass.replace(classFinder, ' ').trim();
      } else if (!classFinder.test(currentClass)) {
        element.props.class = currentClass ? `${currentClass} ${className}` : className;
      }
      useInit(() => {
        /*
         * This seems necessary because Preact doesn't change the class
         * names on the hydration, so we have to do it manually. It doesn't
         * need deps because it only needs to do it the first time.
         */
        if (!result) {
          element.ref.current.classList.remove(className);
        } else {
          element.ref.current.classList.add(className);
        }
      });
    });
  });

  // data-wp-style--[style-prop]
  directive('style', ({
    directives: {
      style
    },
    element,
    evaluate
  }) => {
    style.filter(isNonDefaultDirectiveSuffix).forEach(entry => {
      const styleProp = entry.suffix;
      let result = evaluate(entry);
      if (typeof result === 'function') {
        result = result();
      }
      element.props.style = element.props.style || {};
      if (typeof element.props.style === 'string') {
        element.props.style = cssStringToObject(element.props.style);
      }
      if (!result) {
        delete element.props.style[styleProp];
      } else {
        element.props.style[styleProp] = result;
      }
      useInit(() => {
        /*
         * This seems necessary because Preact doesn't change the styles on
         * the hydration, so we have to do it manually. It doesn't need deps
         * because it only needs to do it the first time.
         */
        if (!result) {
          element.ref.current.style.removeProperty(styleProp);
        } else {
          element.ref.current.style[styleProp] = result;
        }
      });
    });
  });

  // data-wp-bind--[attribute]
  directive('bind', ({
    directives: {
      bind
    },
    element,
    evaluate
  }) => {
    bind.filter(isNonDefaultDirectiveSuffix).forEach(entry => {
      const attribute = entry.suffix;
      let result = evaluate(entry);
      if (typeof result === 'function') {
        result = result();
      }
      element.props[attribute] = result;

      /*
       * This is necessary because Preact doesn't change the attributes on the
       * hydration, so we have to do it manually. It only needs to do it the
       * first time. After that, Preact will handle the changes.
       */
      useInit(() => {
        const el = element.ref.current;

        /*
         * We set the value directly to the corresponding HTMLElement instance
         * property excluding the following special cases. We follow Preact's
         * logic: https://github.com/preactjs/preact/blob/ea49f7a0f9d1ff2c98c0bdd66aa0cbc583055246/src/diff/props.js#L110-L129
         */
        if (attribute === 'style') {
          if (typeof result === 'string') {
            el.style.cssText = result;
          }
          return;
        } else if (attribute !== 'width' && attribute !== 'height' && attribute !== 'href' && attribute !== 'list' && attribute !== 'form' &&
        /*
         * The value for `tabindex` follows the parsing rules for an
         * integer. If that fails, or if the attribute isn't present, then
         * the browsers should "follow platform conventions to determine if
         * the element should be considered as a focusable area",
         * practically meaning that most elements get a default of `-1` (not
         * focusable), but several also get a default of `0` (focusable in
         * order after all elements with a positive `tabindex` value).
         *
         * @see https://html.spec.whatwg.org/#tabindex-value
         */
        attribute !== 'tabIndex' && attribute !== 'download' && attribute !== 'rowSpan' && attribute !== 'colSpan' && attribute !== 'role' && attribute in el) {
          try {
            el[attribute] = result === null || result === undefined ? '' : result;
            return;
          } catch (err) {}
        }
        /*
         * aria- and data- attributes have no boolean representation.
         * A `false` value is different from the attribute not being
         * present, so we can't remove it.
         * We follow Preact's logic: https://github.com/preactjs/preact/blob/ea49f7a0f9d1ff2c98c0bdd66aa0cbc583055246/src/diff/props.js#L131C24-L136
         */
        if (result !== null && result !== undefined && (result !== false || attribute[4] === '-')) {
          el.setAttribute(attribute, result);
        } else {
          el.removeAttribute(attribute);
        }
      });
    });
  });

  // data-wp-ignore
  directive('ignore', ({
    element: {
      type: Type,
      props: {
        innerHTML,
        ...rest
      }
    }
  }) => {
    // Preserve the initial inner HTML.
    const cached = T(() => innerHTML, []);
    return (0,preact_module.h)(Type, {
      dangerouslySetInnerHTML: {
        __html: cached
      },
      ...rest
    });
  });

  // data-wp-text
  directive('text', ({
    directives: {
      text
    },
    element,
    evaluate
  }) => {
    const entry = text.find(isDefaultDirectiveSuffix);
    if (!entry) {
      element.props.children = null;
      return;
    }
    try {
      let result = evaluate(entry);
      if (typeof result === 'function') {
        result = result();
      }
      element.props.children = typeof result === 'object' ? null : result.toString();
    } catch (e) {
      element.props.children = null;
    }
  });

  // data-wp-run
  directive('run', ({
    directives: {
      run
    },
    evaluate
  }) => {
    run.forEach(entry => {
      let result = evaluate(entry);
      if (typeof result === 'function') {
        result = result();
      }
      return result;
    });
  });

  // data-wp-each--[item]
  directive('each', ({
    directives: {
      each,
      'each-key': eachKey
    },
    context: inheritedContext,
    element,
    evaluate
  }) => {
    if (element.type !== 'template') {
      return;
    }
    const {
      Provider
    } = inheritedContext;
    const inheritedValue = x(inheritedContext);
    const [entry] = each;
    const {
      namespace
    } = entry;
    let iterable = evaluate(entry);
    if (typeof iterable === 'function') {
      iterable = iterable();
    }
    if (typeof iterable?.[Symbol.iterator] !== 'function') {
      return;
    }
    const itemProp = isNonDefaultDirectiveSuffix(entry) ? kebabToCamelCase(entry.suffix) : 'item';
    const result = [];
    for (const item of iterable) {
      const itemContext = proxifyContext(proxifyState(namespace, {}), inheritedValue.client[namespace]);
      const mergedContext = {
        client: {
          ...inheritedValue.client,
          [namespace]: itemContext
        },
        server: {
          ...inheritedValue.server
        }
      };

      // Set the item after proxifying the context.
      mergedContext.client[namespace][itemProp] = item;
      const scope = {
        ...getScope(),
        context: mergedContext.client,
        serverContext: mergedContext.server
      };
      const key = eachKey ? getEvaluate({
        scope
      })(eachKey[0]) : item;
      result.push((0,preact_module.h)(Provider, {
        value: mergedContext,
        key
      }, element.props.content));
    }
    return result;
  }, {
    priority: 20
  });
  directive('each-child', () => null, {
    priority: 1
  });
});

;// ./node_modules/@wordpress/interactivity/build-module/constants.js
const directivePrefix = 'wp';

;// ./node_modules/@wordpress/interactivity/build-module/vdom.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


const ignoreAttr = `data-${directivePrefix}-ignore`;
const islandAttr = `data-${directivePrefix}-interactive`;
const fullPrefix = `data-${directivePrefix}-`;
const namespaces = [];
const currentNamespace = () => {
  var _namespaces;
  return (_namespaces = namespaces[namespaces.length - 1]) !== null && _namespaces !== void 0 ? _namespaces : null;
};
const isObject = item => Boolean(item && typeof item === 'object' && item.constructor === Object);

// Regular expression for directive parsing.
const directiveParser = new RegExp(`^data-${directivePrefix}-` +
// ${p} must be a prefix string, like 'wp'.
// Match alphanumeric characters including hyphen-separated
// segments. It excludes underscore intentionally to prevent confusion.
// E.g., "custom-directive".
'([a-z0-9]+(?:-[a-z0-9]+)*)' +
// (Optional) Match '--' followed by any alphanumeric characters. It
// excludes underscore intentionally to prevent confusion, but it can
// contain multiple hyphens. E.g., "--custom-prefix--with-more-info".
'(?:--([a-z0-9_-]+))?$', 'i' // Case insensitive.
);

// Regular expression for reference parsing. It can contain a namespace before
// the reference, separated by `::`, like `some-namespace::state.somePath`.
// Namespaces can contain any alphanumeric characters, hyphens, underscores or
// forward slashes. References don't have any restrictions.
const nsPathRegExp = /^([\w_\/-]+)::(.+)$/;
const hydratedIslands = new WeakSet();

/**
 * Recursive function that transforms a DOM tree into vDOM.
 *
 * @param root The root element or node to start traversing on.
 * @return The resulting vDOM tree.
 */
function toVdom(root) {
  const treeWalker = document.createTreeWalker(root, 205 // TEXT + CDATA_SECTION + COMMENT + PROCESSING_INSTRUCTION + ELEMENT
  );
  function walk(node) {
    const {
      nodeType
    } = node;

    // TEXT_NODE (3)
    if (nodeType === 3) {
      return [node.data];
    }

    // CDATA_SECTION_NODE (4)
    if (nodeType === 4) {
      var _nodeValue;
      const next = treeWalker.nextSibling();
      node.replaceWith(new window.Text((_nodeValue = node.nodeValue) !== null && _nodeValue !== void 0 ? _nodeValue : ''));
      return [node.nodeValue, next];
    }

    // COMMENT_NODE (8) || PROCESSING_INSTRUCTION_NODE (7)
    if (nodeType === 8 || nodeType === 7) {
      const next = treeWalker.nextSibling();
      node.remove();
      return [null, next];
    }
    const elementNode = node;
    const {
      attributes
    } = elementNode;
    const localName = elementNode.localName;
    const props = {};
    const children = [];
    const directives = [];
    let ignore = false;
    let island = false;
    for (let i = 0; i < attributes.length; i++) {
      const attributeName = attributes[i].name;
      const attributeValue = attributes[i].value;
      if (attributeName[fullPrefix.length] && attributeName.slice(0, fullPrefix.length) === fullPrefix) {
        if (attributeName === ignoreAttr) {
          ignore = true;
        } else {
          var _regexResult$, _regexResult$2;
          const regexResult = nsPathRegExp.exec(attributeValue);
          const namespace = (_regexResult$ = regexResult?.[1]) !== null && _regexResult$ !== void 0 ? _regexResult$ : null;
          let value = (_regexResult$2 = regexResult?.[2]) !== null && _regexResult$2 !== void 0 ? _regexResult$2 : attributeValue;
          try {
            const parsedValue = JSON.parse(value);
            value = isObject(parsedValue) ? parsedValue : value;
          } catch {}
          if (attributeName === islandAttr) {
            island = true;
            const islandNamespace =
            // eslint-disable-next-line no-nested-ternary
            typeof value === 'string' ? value : typeof value?.namespace === 'string' ? value.namespace : null;
            namespaces.push(islandNamespace);
          } else {
            directives.push([attributeName, namespace, value]);
          }
        }
      } else if (attributeName === 'ref') {
        continue;
      }
      props[attributeName] = attributeValue;
    }
    if (ignore && !island) {
      return [(0,preact_module.h)(localName, {
        ...props,
        innerHTML: elementNode.innerHTML,
        __directives: {
          ignore: true
        }
      })];
    }
    if (island) {
      hydratedIslands.add(elementNode);
    }
    if (directives.length) {
      props.__directives = directives.reduce((obj, [name, ns, value]) => {
        const directiveMatch = directiveParser.exec(name);
        if (directiveMatch === null) {
          warn(`Found malformed directive name: ${name}.`);
          return obj;
        }
        const prefix = directiveMatch[1] || '';
        const suffix = directiveMatch[2] || null;
        obj[prefix] = obj[prefix] || [];
        obj[prefix].push({
          namespace: ns !== null && ns !== void 0 ? ns : currentNamespace(),
          value: value,
          suffix
        });
        return obj;
      }, {});
    }
    if (localName === 'template') {
      props.content = [...elementNode.content.childNodes].map(childNode => toVdom(childNode));
    } else {
      let child = treeWalker.firstChild();
      if (child) {
        while (child) {
          const [vnode, nextChild] = walk(child);
          if (vnode) {
            children.push(vnode);
          }
          child = nextChild || treeWalker.nextSibling();
        }
        treeWalker.parentNode();
      }
    }

    // Restore previous namespace.
    if (island) {
      namespaces.pop();
    }
    return [(0,preact_module.h)(localName, props, children)];
  }
  return walk(treeWalker.currentNode);
}

;// ./node_modules/@wordpress/interactivity/build-module/init.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */




// Keep the same root fragment for each interactive region node.
const regionRootFragments = new WeakMap();
const getRegionRootFragment = region => {
  if (!region.parentElement) {
    throw Error('The passed region should be an element with a parent.');
  }
  if (!regionRootFragments.has(region)) {
    regionRootFragments.set(region, createRootFragment(region.parentElement, region));
  }
  return regionRootFragments.get(region);
};

// Initial vDOM regions associated with its DOM element.
const initialVdom = new WeakMap();

// Initialize the router with the initial DOM.
const init = async () => {
  const nodes = document.querySelectorAll(`[data-${directivePrefix}-interactive]`);

  /*
   * This `await` with setTimeout is required to apparently ensure that the interactive blocks have their stores
   * fully initialized prior to hydrating the blocks. If this is not present, then an error occurs, for example:
   * > view.js:46 Uncaught (in promise) ReferenceError: Cannot access 'state' before initialization
   * This occurs when splitTask() is implemented with scheduler.yield() as opposed to setTimeout(), as with the former
   * split tasks are added to the front of the task queue whereas with the latter they are added to the end of the queue.
   */
  await new Promise(resolve => {
    setTimeout(resolve, 0);
  });
  for (const node of nodes) {
    if (!hydratedIslands.has(node)) {
      await splitTask();
      const fragment = getRegionRootFragment(node);
      const vdom = toVdom(node);
      initialVdom.set(node, vdom);
      await splitTask();
      (0,preact_module/* hydrate */.Qv)(vdom, fragment);
    }
  }
};

;// ./node_modules/@wordpress/interactivity/build-module/index.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */












const requiredConsent = 'I acknowledge that using private APIs means my theme or plugin will inevitably break in the next version of WordPress.';
const privateApis = lock => {
  if (lock === requiredConsent) {
    return {
      directivePrefix: directivePrefix,
      getRegionRootFragment: getRegionRootFragment,
      initialVdom: initialVdom,
      toVdom: toVdom,
      directive: directive,
      getNamespace: getNamespace,
      h: preact_module.h,
      cloneElement: preact_module/* cloneElement */.Ob,
      render: preact_module/* render */.XX,
      proxifyState: proxifyState,
      parseServerData: parseServerData,
      populateServerData: populateServerData,
      batch: signals_core_module_r
    };
  }
  throw new Error('Forbidden access.');
};
directives();
init();

var __webpack_exports__getConfig = __webpack_exports__.zj;
var __webpack_exports__getContext = __webpack_exports__.SD;
var __webpack_exports__getElement = __webpack_exports__.V6;
var __webpack_exports__getServerContext = __webpack_exports__.$K;
var __webpack_exports__getServerState = __webpack_exports__.vT;
var __webpack_exports__privateApis = __webpack_exports__.jb;
var __webpack_exports__splitTask = __webpack_exports__.yT;
var __webpack_exports__store = __webpack_exports__.M_;
var __webpack_exports__useCallback = __webpack_exports__.hb;
var __webpack_exports__useEffect = __webpack_exports__.vJ;
var __webpack_exports__useInit = __webpack_exports__.ip;
var __webpack_exports__useLayoutEffect = __webpack_exports__.Nf;
var __webpack_exports__useMemo = __webpack_exports__.Kr;
var __webpack_exports__useRef = __webpack_exports__.li;
var __webpack_exports__useState = __webpack_exports__.J0;
var __webpack_exports__useWatch = __webpack_exports__.FH;
var __webpack_exports__withScope = __webpack_exports__.v4;
var __webpack_exports__withSyncEvent = __webpack_exports__.mh;
export { __webpack_exports__getConfig as getConfig, __webpack_exports__getContext as getContext, __webpack_exports__getElement as getElement, __webpack_exports__getServerContext as getServerContext, __webpack_exports__getServerState as getServerState, __webpack_exports__privateApis as privateApis, __webpack_exports__splitTask as splitTask, __webpack_exports__store as store, __webpack_exports__useCallback as useCallback, __webpack_exports__useEffect as useEffect, __webpack_exports__useInit as useInit, __webpack_exports__useLayoutEffect as useLayoutEffect, __webpack_exports__useMemo as useMemo, __webpack_exports__useRef as useRef, __webpack_exports__useState as useState, __webpack_exports__useWatch as useWatch, __webpack_exports__withScope as withScope, __webpack_exports__withSyncEvent as withSyncEvent };
