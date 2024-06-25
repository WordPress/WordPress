/******/ // The require scope
/******/ var __webpack_require__ = {};
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
  jb: () => (/* reexport */ privateApis),
  yT: () => (/* reexport */ splitTask),
  M_: () => (/* reexport */ store),
  hb: () => (/* reexport */ useCallback),
  vJ: () => (/* reexport */ useEffect),
  ip: () => (/* reexport */ useInit),
  Nf: () => (/* reexport */ useLayoutEffect),
  Kr: () => (/* reexport */ useMemo),
  li: () => (/* reexport */ hooks_module_F),
  J0: () => (/* reexport */ hooks_module_p),
  FH: () => (/* reexport */ useWatch),
  v4: () => (/* reexport */ withScope)
});

;// CONCATENATED MODULE: ./node_modules/preact/dist/preact.module.js
var preact_module_n,preact_module_l,preact_module_u,preact_module_t,i,preact_module_o,r,preact_module_f,preact_module_e,preact_module_c,s,a,h={},p=[],v=/acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i,y=Array.isArray;function d(n,l){for(var u in l)n[u]=l[u];return n}function w(n){var l=n.parentNode;l&&l.removeChild(n)}function _(l,u,t){var i,o,r,f={};for(r in u)"key"==r?i=u[r]:"ref"==r?o=u[r]:f[r]=u[r];if(arguments.length>2&&(f.children=arguments.length>3?preact_module_n.call(arguments,2):t),"function"==typeof l&&null!=l.defaultProps)for(r in l.defaultProps)void 0===f[r]&&(f[r]=l.defaultProps[r]);return g(l,f,i,o,null)}function g(n,t,i,o,r){var f={type:n,props:t,key:i,ref:o,__k:null,__:null,__b:0,__e:null,__d:void 0,__c:null,constructor:void 0,__v:null==r?++preact_module_u:r,__i:-1,__u:0};return null==r&&null!=preact_module_l.vnode&&preact_module_l.vnode(f),f}function m(){return{current:null}}function k(n){return n.children}function b(n,l){this.props=n,this.context=l}function x(n,l){if(null==l)return n.__?x(n.__,n.__i+1):null;for(var u;l<n.__k.length;l++)if(null!=(u=n.__k[l])&&null!=u.__e)return u.__e;return"function"==typeof n.type?x(n):null}function C(n){var l,u;if(null!=(n=n.__)&&null!=n.__c){for(n.__e=n.__c.base=null,l=0;l<n.__k.length;l++)if(null!=(u=n.__k[l])&&null!=u.__e){n.__e=n.__c.base=u.__e;break}return C(n)}}function M(n){(!n.__d&&(n.__d=!0)&&i.push(n)&&!P.__r++||preact_module_o!==preact_module_l.debounceRendering)&&((preact_module_o=preact_module_l.debounceRendering)||r)(P)}function P(){var n,u,t,o,r,e,c,s;for(i.sort(preact_module_f);n=i.shift();)n.__d&&(u=i.length,o=void 0,e=(r=(t=n).__v).__e,c=[],s=[],t.__P&&((o=d({},r)).__v=r.__v+1,preact_module_l.vnode&&preact_module_l.vnode(o),O(t.__P,o,r,t.__n,t.__P.namespaceURI,32&r.__u?[e]:null,c,null==e?x(r):e,!!(32&r.__u),s),o.__v=r.__v,o.__.__k[o.__i]=o,j(c,o,s),o.__e!=e&&C(o)),i.length>u&&i.sort(preact_module_f));P.__r=0}function S(n,l,u,t,i,o,r,f,e,c,s){var a,v,y,d,w,_=t&&t.__k||p,g=l.length;for(u.__d=e,$(u,l,_),e=u.__d,a=0;a<g;a++)null!=(y=u.__k[a])&&"boolean"!=typeof y&&"function"!=typeof y&&(v=-1===y.__i?h:_[y.__i]||h,y.__i=a,O(n,y,v,i,o,r,f,e,c,s),d=y.__e,y.ref&&v.ref!=y.ref&&(v.ref&&N(v.ref,null,y),s.push(y.ref,y.__c||d,y)),null==w&&null!=d&&(w=d),65536&y.__u||v.__k===y.__k?(e&&!e.isConnected&&(e=x(v)),e=I(y,e,n)):"function"==typeof y.type&&void 0!==y.__d?e=y.__d:d&&(e=d.nextSibling),y.__d=void 0,y.__u&=-196609);u.__d=e,u.__e=w}function $(n,l,u){var t,i,o,r,f,e=l.length,c=u.length,s=c,a=0;for(n.__k=[],t=0;t<e;t++)r=t+a,null!=(i=n.__k[t]=null==(i=l[t])||"boolean"==typeof i||"function"==typeof i?null:"string"==typeof i||"number"==typeof i||"bigint"==typeof i||i.constructor==String?g(null,i,null,null,null):y(i)?g(k,{children:i},null,null,null):void 0===i.constructor&&i.__b>0?g(i.type,i.props,i.key,i.ref?i.ref:null,i.__v):i)?(i.__=n,i.__b=n.__b+1,f=L(i,u,r,s),i.__i=f,o=null,-1!==f&&(s--,(o=u[f])&&(o.__u|=131072)),null==o||null===o.__v?(-1==f&&a--,"function"!=typeof i.type&&(i.__u|=65536)):f!==r&&(f===r+1?a++:f>r?s>e-r?a+=f-r:a--:f<r?f==r-1&&(a=f-r):a=0,f!==t+a&&(i.__u|=65536))):(o=u[r])&&null==o.key&&o.__e&&0==(131072&o.__u)&&(o.__e==n.__d&&(n.__d=x(o)),V(o,o,!1),u[r]=null,s--);if(s)for(t=0;t<c;t++)null!=(o=u[t])&&0==(131072&o.__u)&&(o.__e==n.__d&&(n.__d=x(o)),V(o,o))}function I(n,l,u){var t,i;if("function"==typeof n.type){for(t=n.__k,i=0;t&&i<t.length;i++)t[i]&&(t[i].__=n,l=I(t[i],l,u));return l}n.__e!=l&&(u.insertBefore(n.__e,l||null),l=n.__e);do{l=l&&l.nextSibling}while(null!=l&&8===l.nodeType);return l}function H(n,l){return l=l||[],null==n||"boolean"==typeof n||(y(n)?n.some(function(n){H(n,l)}):l.push(n)),l}function L(n,l,u,t){var i=n.key,o=n.type,r=u-1,f=u+1,e=l[u];if(null===e||e&&i==e.key&&o===e.type&&0==(131072&e.__u))return u;if(t>(null!=e&&0==(131072&e.__u)?1:0))for(;r>=0||f<l.length;){if(r>=0){if((e=l[r])&&0==(131072&e.__u)&&i==e.key&&o===e.type)return r;r--}if(f<l.length){if((e=l[f])&&0==(131072&e.__u)&&i==e.key&&o===e.type)return f;f++}}return-1}function T(n,l,u){"-"===l[0]?n.setProperty(l,null==u?"":u):n[l]=null==u?"":"number"!=typeof u||v.test(l)?u:u+"px"}function A(n,l,u,t,i){var o;n:if("style"===l)if("string"==typeof u)n.style.cssText=u;else{if("string"==typeof t&&(n.style.cssText=t=""),t)for(l in t)u&&l in u||T(n.style,l,"");if(u)for(l in u)t&&u[l]===t[l]||T(n.style,l,u[l])}else if("o"===l[0]&&"n"===l[1])o=l!==(l=l.replace(/(PointerCapture)$|Capture$/i,"$1")),l=l.toLowerCase()in n||"onFocusOut"===l||"onFocusIn"===l?l.toLowerCase().slice(2):l.slice(2),n.l||(n.l={}),n.l[l+o]=u,u?t?u.u=t.u:(u.u=preact_module_e,n.addEventListener(l,o?s:preact_module_c,o)):n.removeEventListener(l,o?s:preact_module_c,o);else{if("http://www.w3.org/2000/svg"==i)l=l.replace(/xlink(H|:h)/,"h").replace(/sName$/,"s");else if("width"!=l&&"height"!=l&&"href"!=l&&"list"!=l&&"form"!=l&&"tabIndex"!=l&&"download"!=l&&"rowSpan"!=l&&"colSpan"!=l&&"role"!=l&&l in n)try{n[l]=null==u?"":u;break n}catch(n){}"function"==typeof u||(null==u||!1===u&&"-"!==l[4]?n.removeAttribute(l):n.setAttribute(l,u))}}function F(n){return function(u){if(this.l){var t=this.l[u.type+n];if(null==u.t)u.t=preact_module_e++;else if(u.t<t.u)return;return t(preact_module_l.event?preact_module_l.event(u):u)}}}function O(n,u,t,i,o,r,f,e,c,s){var a,h,p,v,w,_,g,m,x,C,M,P,$,I,H,L=u.type;if(void 0!==u.constructor)return null;128&t.__u&&(c=!!(32&t.__u),r=[e=u.__e=t.__e]),(a=preact_module_l.__b)&&a(u);n:if("function"==typeof L)try{if(m=u.props,x=(a=L.contextType)&&i[a.__c],C=a?x?x.props.value:a.__:i,t.__c?g=(h=u.__c=t.__c).__=h.__E:("prototype"in L&&L.prototype.render?u.__c=h=new L(m,C):(u.__c=h=new b(m,C),h.constructor=L,h.render=q),x&&x.sub(h),h.props=m,h.state||(h.state={}),h.context=C,h.__n=i,p=h.__d=!0,h.__h=[],h._sb=[]),null==h.__s&&(h.__s=h.state),null!=L.getDerivedStateFromProps&&(h.__s==h.state&&(h.__s=d({},h.__s)),d(h.__s,L.getDerivedStateFromProps(m,h.__s))),v=h.props,w=h.state,h.__v=u,p)null==L.getDerivedStateFromProps&&null!=h.componentWillMount&&h.componentWillMount(),null!=h.componentDidMount&&h.__h.push(h.componentDidMount);else{if(null==L.getDerivedStateFromProps&&m!==v&&null!=h.componentWillReceiveProps&&h.componentWillReceiveProps(m,C),!h.__e&&(null!=h.shouldComponentUpdate&&!1===h.shouldComponentUpdate(m,h.__s,C)||u.__v===t.__v)){for(u.__v!==t.__v&&(h.props=m,h.state=h.__s,h.__d=!1),u.__e=t.__e,u.__k=t.__k,u.__k.forEach(function(n){n&&(n.__=u)}),M=0;M<h._sb.length;M++)h.__h.push(h._sb[M]);h._sb=[],h.__h.length&&f.push(h);break n}null!=h.componentWillUpdate&&h.componentWillUpdate(m,h.__s,C),null!=h.componentDidUpdate&&h.__h.push(function(){h.componentDidUpdate(v,w,_)})}if(h.context=C,h.props=m,h.__P=n,h.__e=!1,P=preact_module_l.__r,$=0,"prototype"in L&&L.prototype.render){for(h.state=h.__s,h.__d=!1,P&&P(u),a=h.render(h.props,h.state,h.context),I=0;I<h._sb.length;I++)h.__h.push(h._sb[I]);h._sb=[]}else do{h.__d=!1,P&&P(u),a=h.render(h.props,h.state,h.context),h.state=h.__s}while(h.__d&&++$<25);h.state=h.__s,null!=h.getChildContext&&(i=d(d({},i),h.getChildContext())),p||null==h.getSnapshotBeforeUpdate||(_=h.getSnapshotBeforeUpdate(v,w)),S(n,y(H=null!=a&&a.type===k&&null==a.key?a.props.children:a)?H:[H],u,t,i,o,r,f,e,c,s),h.base=u.__e,u.__u&=-161,h.__h.length&&f.push(h),g&&(h.__E=h.__=null)}catch(n){u.__v=null,c||null!=r?(u.__e=e,u.__u|=c?160:32,r[r.indexOf(e)]=null):(u.__e=t.__e,u.__k=t.__k),preact_module_l.__e(n,u,t)}else null==r&&u.__v===t.__v?(u.__k=t.__k,u.__e=t.__e):u.__e=z(t.__e,u,t,i,o,r,f,c,s);(a=preact_module_l.diffed)&&a(u)}function j(n,u,t){u.__d=void 0;for(var i=0;i<t.length;i++)N(t[i],t[++i],t[++i]);preact_module_l.__c&&preact_module_l.__c(u,n),n.some(function(u){try{n=u.__h,u.__h=[],n.some(function(n){n.call(u)})}catch(n){preact_module_l.__e(n,u.__v)}})}function z(l,u,t,i,o,r,f,e,c){var s,a,p,v,d,_,g,m=t.props,k=u.props,b=u.type;if("svg"===b?o="http://www.w3.org/2000/svg":"math"===b?o="http://www.w3.org/1998/Math/MathML":o||(o="http://www.w3.org/1999/xhtml"),null!=r)for(s=0;s<r.length;s++)if((d=r[s])&&"setAttribute"in d==!!b&&(b?d.localName===b:3===d.nodeType)){l=d,r[s]=null;break}if(null==l){if(null===b)return document.createTextNode(k);l=document.createElementNS(o,b,k.is&&k),r=null,e=!1}if(null===b)m===k||e&&l.data===k||(l.data=k);else{if(r=r&&preact_module_n.call(l.childNodes),m=t.props||h,!e&&null!=r)for(m={},s=0;s<l.attributes.length;s++)m[(d=l.attributes[s]).name]=d.value;for(s in m)if(d=m[s],"children"==s);else if("dangerouslySetInnerHTML"==s)p=d;else if("key"!==s&&!(s in k)){if("value"==s&&"defaultValue"in k||"checked"==s&&"defaultChecked"in k)continue;A(l,s,null,d,o)}for(s in k)d=k[s],"children"==s?v=d:"dangerouslySetInnerHTML"==s?a=d:"value"==s?_=d:"checked"==s?g=d:"key"===s||e&&"function"!=typeof d||m[s]===d||A(l,s,d,m[s],o);if(a)e||p&&(a.__html===p.__html||a.__html===l.innerHTML)||(l.innerHTML=a.__html),u.__k=[];else if(p&&(l.innerHTML=""),S(l,y(v)?v:[v],u,t,i,"foreignObject"===b?"http://www.w3.org/1999/xhtml":o,r,f,r?r[0]:t.__k&&x(t,0),e,c),null!=r)for(s=r.length;s--;)null!=r[s]&&w(r[s]);e||(s="value",void 0!==_&&(_!==l[s]||"progress"===b&&!_||"option"===b&&_!==m[s])&&A(l,s,_,m[s],o),s="checked",void 0!==g&&g!==l[s]&&A(l,s,g,m[s],o))}return l}function N(n,u,t){try{"function"==typeof n?n(u):n.current=u}catch(n){preact_module_l.__e(n,t)}}function V(n,u,t){var i,o;if(preact_module_l.unmount&&preact_module_l.unmount(n),(i=n.ref)&&(i.current&&i.current!==n.__e||N(i,null,u)),null!=(i=n.__c)){if(i.componentWillUnmount)try{i.componentWillUnmount()}catch(n){preact_module_l.__e(n,u)}i.base=i.__P=null}if(i=n.__k)for(o=0;o<i.length;o++)i[o]&&V(i[o],u,t||"function"!=typeof n.type);t||null==n.__e||w(n.__e),n.__c=n.__=n.__e=n.__d=void 0}function q(n,l,u){return this.constructor(n,u)}function B(u,t,i){var o,r,f,e;preact_module_l.__&&preact_module_l.__(u,t),r=(o="function"==typeof i)?null:i&&i.__k||t.__k,f=[],e=[],O(t,u=(!o&&i||t).__k=_(k,null,[u]),r||h,h,t.namespaceURI,!o&&i?[i]:r?null:t.firstChild?preact_module_n.call(t.childNodes):null,f,!o&&i?i:r?r.__e:t.firstChild,o,e),j(f,u,e)}function D(n,l){B(n,l,D)}function E(l,u,t){var i,o,r,f,e=d({},l.props);for(r in l.type&&l.type.defaultProps&&(f=l.type.defaultProps),u)"key"==r?i=u[r]:"ref"==r?o=u[r]:e[r]=void 0===u[r]&&void 0!==f?f[r]:u[r];return arguments.length>2&&(e.children=arguments.length>3?preact_module_n.call(arguments,2):t),g(l.type,e,i||l.key,o||l.ref,null)}function G(n,l){var u={__c:l="__cC"+a++,__:n,Consumer:function(n,l){return n.children(l)},Provider:function(n){var u,t;return this.getChildContext||(u=[],(t={})[l]=this,this.getChildContext=function(){return t},this.shouldComponentUpdate=function(n){this.props.value!==n.value&&u.some(function(n){n.__e=!0,M(n)})},this.sub=function(n){u.push(n);var l=n.componentWillUnmount;n.componentWillUnmount=function(){u.splice(u.indexOf(n),1),l&&l.call(n)}}),n.children}};return u.Provider.__=u.Consumer.contextType=u}preact_module_n=p.slice,preact_module_l={__e:function(n,l,u,t){for(var i,o,r;l=l.__;)if((i=l.__c)&&!i.__)try{if((o=i.constructor)&&null!=o.getDerivedStateFromError&&(i.setState(o.getDerivedStateFromError(n)),r=i.__d),null!=i.componentDidCatch&&(i.componentDidCatch(n,t||{}),r=i.__d),r)return i.__E=i}catch(l){n=l}throw n}},preact_module_u=0,preact_module_t=function(n){return null!=n&&null==n.constructor},b.prototype.setState=function(n,l){var u;u=null!=this.__s&&this.__s!==this.state?this.__s:this.__s=d({},this.state),"function"==typeof n&&(n=n(d({},u),this.props)),n&&d(u,n),null!=n&&this.__v&&(l&&this._sb.push(l),M(this))},b.prototype.forceUpdate=function(n){this.__v&&(this.__e=!0,n&&this.__h.push(n),M(this))},b.prototype.render=k,i=[],r="function"==typeof Promise?Promise.prototype.then.bind(Promise.resolve()):setTimeout,preact_module_f=function(n,l){return n.__v.__b-l.__v.__b},P.__r=0,preact_module_e=0,preact_module_c=F(!1),s=F(!0),a=0;

;// CONCATENATED MODULE: ./node_modules/preact/devtools/dist/devtools.module.js
function devtools_module_t(o,e){return n.__a&&n.__a(e),o}"undefined"!=typeof window&&window.__PREACT_DEVTOOLS__&&window.__PREACT_DEVTOOLS__.attachPreact("10.22.0",preact_module_l,{Fragment:k,Component:b});

;// CONCATENATED MODULE: ./node_modules/preact/debug/dist/debug.module.js
var debug_module_o={};function debug_module_r(){debug_module_o={}}function debug_module_a(e){return e.type===k?"Fragment":"function"==typeof e.type?e.type.displayName||e.type.name:"string"==typeof e.type?e.type:"#text"}var debug_module_i=[],debug_module_s=[];function debug_module_c(){return debug_module_i.length>0?debug_module_i[debug_module_i.length-1]:null}var l=!0;function debug_module_u(e){return"function"==typeof e.type&&e.type!=k}function debug_module_f(n){for(var e=[n],t=n;null!=t.__o;)e.push(t.__o),t=t.__o;return e.reduce(function(n,e){n+="  in "+debug_module_a(e);var t=e.__source;return t?n+=" (at "+t.fileName+":"+t.lineNumber+")":l&&console.warn("Add @babel/plugin-transform-react-jsx-source to get a more detailed component stack. Note that you should not add it to production builds of your App for bundle size reasons."),l=!1,n+"\n"},"")}var debug_module_p="function"==typeof WeakMap;function debug_module_d(n){var e=[];return n.__k?(n.__k.forEach(function(n){n&&"function"==typeof n.type?e.push.apply(e,debug_module_d(n)):n&&"string"==typeof n.type&&e.push(n.type)}),e):e}function debug_module_h(n){return n?"function"==typeof n.type?null===n.__?null!==n.__e&&null!==n.__e.parentNode?n.__e.parentNode.localName:"":debug_module_h(n.__):n.type:""}var debug_module_v=b.prototype.setState;function debug_module_y(n){return"table"===n||"tfoot"===n||"tbody"===n||"thead"===n||"td"===n||"tr"===n||"th"===n}b.prototype.setState=function(n,e){return null==this.__v&&null==this.state&&console.warn('Calling "this.setState" inside the constructor of a component is a no-op and might be a bug in your application. Instead, set "this.state = {}" directly.\n\n'+debug_module_f(debug_module_c())),debug_module_v.call(this,n,e)};var debug_module_m=/^(address|article|aside|blockquote|details|div|dl|fieldset|figcaption|figure|footer|form|h1|h2|h3|h4|h5|h6|header|hgroup|hr|main|menu|nav|ol|p|pre|search|section|table|ul)$/,debug_module_b=b.prototype.forceUpdate;function debug_module_w(n){var e=n.props,t=debug_module_a(n),o="";for(var r in e)if(e.hasOwnProperty(r)&&"children"!==r){var i=e[r];"function"==typeof i&&(i="function "+(i.displayName||i.name)+"() {}"),i=Object(i)!==i||i.toString?i+"":Object.prototype.toString.call(i),o+=" "+r+"="+JSON.stringify(i)}var s=e.children;return"<"+t+o+(s&&s.length?">..</"+t+">":" />")}b.prototype.forceUpdate=function(n){return null==this.__v?console.warn('Calling "this.forceUpdate" inside the constructor of a component is a no-op and might be a bug in your application.\n\n'+debug_module_f(debug_module_c())):null==this.__P&&console.warn('Can\'t call "this.forceUpdate" on an unmounted component. This is a no-op, but it indicates a memory leak in your application. To fix, cancel all subscriptions and asynchronous tasks in the componentWillUnmount method.\n\n'+debug_module_f(this.__v)),debug_module_b.call(this,n)},function(){!function(){var n=preact_module_l.__b,t=preact_module_l.diffed,o=preact_module_l.__,r=preact_module_l.vnode,a=preact_module_l.__r;preact_module_l.diffed=function(n){debug_module_u(n)&&debug_module_s.pop(),debug_module_i.pop(),t&&t(n)},preact_module_l.__b=function(e){debug_module_u(e)&&debug_module_i.push(e),n&&n(e)},preact_module_l.__=function(n,e){debug_module_s=[],o&&o(n,e)},preact_module_l.vnode=function(n){n.__o=debug_module_s.length>0?debug_module_s[debug_module_s.length-1]:null,r&&r(n)},preact_module_l.__r=function(n){debug_module_u(n)&&debug_module_s.push(n),a&&a(n)}}();var n=!1,t=preact_module_l.__b,r=preact_module_l.diffed,c=preact_module_l.vnode,l=preact_module_l.__r,v=preact_module_l.__e,b=preact_module_l.__,g=preact_module_l.__h,E=debug_module_p?{useEffect:new WeakMap,useLayoutEffect:new WeakMap,lazyPropTypes:new WeakMap}:null,k=[];preact_module_l.__e=function(n,e,t,o){if(e&&e.__c&&"function"==typeof n.then){var r=n;n=new Error("Missing Suspense. The throwing component was: "+debug_module_a(e));for(var i=e;i;i=i.__)if(i.__c&&i.__c.__c){n=r;break}if(n instanceof Error)throw n}try{(o=o||{}).componentStack=debug_module_f(e),v(n,e,t,o),"function"!=typeof n.then&&setTimeout(function(){throw n})}catch(n){throw n}},preact_module_l.__=function(n,e){if(!e)throw new Error("Undefined parent passed to render(), this is the second argument.\nCheck if the element is available in the DOM/has the correct id.");var t;switch(e.nodeType){case 1:case 11:case 9:t=!0;break;default:t=!1}if(!t){var o=debug_module_a(n);throw new Error("Expected a valid HTML node as a second argument to render.\tReceived "+e+" instead: render(<"+o+" />, "+e+");")}b&&b(n,e)},preact_module_l.__b=function(e){var r=e.type;if(n=!0,void 0===r)throw new Error("Undefined component passed to createElement()\n\nYou likely forgot to export your component or might have mixed up default and named imports"+debug_module_w(e)+"\n\n"+debug_module_f(e));if(null!=r&&"object"==typeof r){if(void 0!==r.__k&&void 0!==r.__e)throw new Error("Invalid type passed to createElement(): "+r+"\n\nDid you accidentally pass a JSX literal as JSX twice?\n\n  let My"+debug_module_a(e)+" = "+debug_module_w(r)+";\n  let vnode = <My"+debug_module_a(e)+" />;\n\nThis usually happens when you export a JSX literal and not the component.\n\n"+debug_module_f(e));throw new Error("Invalid type passed to createElement(): "+(Array.isArray(r)?"array":r))}if(void 0!==e.ref&&"function"!=typeof e.ref&&"object"!=typeof e.ref&&!("$$typeof"in e))throw new Error('Component\'s "ref" property should be a function, or an object created by createRef(), but got ['+typeof e.ref+"] instead\n"+debug_module_w(e)+"\n\n"+debug_module_f(e));if("string"==typeof e.type)for(var i in e.props)if("o"===i[0]&&"n"===i[1]&&"function"!=typeof e.props[i]&&null!=e.props[i])throw new Error("Component's \""+i+'" property should be a function, but got ['+typeof e.props[i]+"] instead\n"+debug_module_w(e)+"\n\n"+debug_module_f(e));if("function"==typeof e.type&&e.type.propTypes){if("Lazy"===e.type.displayName&&E&&!E.lazyPropTypes.has(e.type)){var s="PropTypes are not supported on lazy(). Use propTypes on the wrapped component itself. ";try{var c=e.type();E.lazyPropTypes.set(e.type,!0),console.warn(s+"Component wrapped in lazy() is "+debug_module_a(c))}catch(n){console.warn(s+"We will log the wrapped component's name once it is loaded.")}}var l=e.props;e.type.__f&&delete(l=function(n,e){for(var t in e)n[t]=e[t];return n}({},l)).ref,function(n,e,t,r,a){Object.keys(n).forEach(function(t){var i;try{i=n[t](e,t,r,"prop",null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(n){i=n}i&&!(i.message in debug_module_o)&&(debug_module_o[i.message]=!0,console.error("Failed prop type: "+i.message+(a&&"\n"+a()||"")))})}(e.type.propTypes,l,0,debug_module_a(e),function(){return debug_module_f(e)})}t&&t(e)};var _,T=0;preact_module_l.__r=function(e){l&&l(e),n=!0;var t=e.__c;if(t===_?T++:T=1,T>=25)throw new Error("Too many re-renders. This is limited to prevent an infinite loop which may lock up your browser. The component causing this is: "+debug_module_a(e));_=t},preact_module_l.__h=function(e,t,o){if(!e||!n)throw new Error("Hook can only be invoked from render methods.");g&&g(e,t,o)};var I=function(n,e){return{get:function(){var t="get"+n+e;k&&k.indexOf(t)<0&&(k.push(t),console.warn("getting vnode."+n+" is deprecated, "+e))},set:function(){var t="set"+n+e;k&&k.indexOf(t)<0&&(k.push(t),console.warn("setting vnode."+n+" is not allowed, "+e))}}},j={nodeName:I("nodeName","use vnode.type"),attributes:I("attributes","use vnode.props"),children:I("children","use vnode.props.children")},O=Object.create({},j);preact_module_l.vnode=function(n){var e=n.props;if(null!==n.type&&null!=e&&("__source"in e||"__self"in e)){var t=n.props={};for(var o in e){var r=e[o];"__source"===o?n.__source=r:"__self"===o?n.__self=r:t[o]=r}}n.__proto__=O,c&&c(n)},preact_module_l.diffed=function(e){var t,o=e.type,i=e.__;if(e.__k&&e.__k.forEach(function(n){if("object"==typeof n&&n&&void 0===n.type){var t=Object.keys(n).join(",");throw new Error("Objects are not valid as a child. Encountered an object with the keys {"+t+"}.\n\n"+debug_module_f(e))}}),e.__c===_&&(T=0),"string"==typeof o&&(debug_module_y(o)||"p"===o||"a"===o||"button"===o)){var s=debug_module_h(i);if(""!==s)"table"===o&&"td"!==s&&debug_module_y(s)?(console.log(s,i.__e),console.error("Improper nesting of table. Your <table> should not have a table-node parent."+debug_module_w(e)+"\n\n"+debug_module_f(e))):"thead"!==o&&"tfoot"!==o&&"tbody"!==o||"table"===s?"tr"===o&&"thead"!==s&&"tfoot"!==s&&"tbody"!==s?console.error("Improper nesting of table. Your <tr> should have a <thead/tbody/tfoot> parent."+debug_module_w(e)+"\n\n"+debug_module_f(e)):"td"===o&&"tr"!==s?console.error("Improper nesting of table. Your <td> should have a <tr> parent."+debug_module_w(e)+"\n\n"+debug_module_f(e)):"th"===o&&"tr"!==s&&console.error("Improper nesting of table. Your <th> should have a <tr>."+debug_module_w(e)+"\n\n"+debug_module_f(e)):console.error("Improper nesting of table. Your <thead/tbody/tfoot> should have a <table> parent."+debug_module_w(e)+"\n\n"+debug_module_f(e));else if("p"===o){var c=debug_module_d(e).filter(function(n){return debug_module_m.test(n)});c.length&&console.error("Improper nesting of paragraph. Your <p> should not have "+c.join(", ")+"as child-elements."+debug_module_w(e)+"\n\n"+debug_module_f(e))}else"a"!==o&&"button"!==o||-1!==debug_module_d(e).indexOf(o)&&console.error("Improper nesting of interactive content. Your <"+o+"> should not have other "+("a"===o?"anchor":"button")+" tags as child-elements."+debug_module_w(e)+"\n\n"+debug_module_f(e))}if(n=!1,r&&r(e),null!=e.__k)for(var l=[],u=0;u<e.__k.length;u++){var p=e.__k[u];if(p&&null!=p.key){var v=p.key;if(-1!==l.indexOf(v)){console.error('Following component has two or more children with the same key attribute: "'+v+'". This may cause glitches and misbehavior in rendering process. Component: \n\n'+debug_module_w(e)+"\n\n"+debug_module_f(e));break}l.push(v)}}if(null!=e.__c&&null!=e.__c.__H){var b=e.__c.__H.__;if(b)for(var g=0;g<b.length;g+=1){var E=b[g];if(E.__H)for(var k=0;k<E.__H.length;k++)if((t=E.__H[k])!=t){var I=debug_module_a(e);throw new Error("Invalid argument passed to hook. Hooks should not be called with NaN in the dependency array. Hook index "+g+" in component "+I+" was called with NaN.")}}}}}();

;// CONCATENATED MODULE: ./node_modules/preact/hooks/dist/hooks.module.js
var hooks_module_t,hooks_module_r,hooks_module_u,hooks_module_i,hooks_module_o=0,hooks_module_f=[],hooks_module_c=[],hooks_module_e=preact_module_l,hooks_module_a=hooks_module_e.__b,hooks_module_v=hooks_module_e.__r,hooks_module_l=hooks_module_e.diffed,hooks_module_m=hooks_module_e.__c,hooks_module_s=hooks_module_e.unmount,hooks_module_d=hooks_module_e.__;function hooks_module_h(n,t){hooks_module_e.__h&&hooks_module_e.__h(hooks_module_r,n,hooks_module_o||t),hooks_module_o=0;var u=hooks_module_r.__H||(hooks_module_r.__H={__:[],__h:[]});return n>=u.__.length&&u.__.push({__V:hooks_module_c}),u.__[n]}function hooks_module_p(n){return hooks_module_o=1,hooks_module_y(hooks_module_D,n)}function hooks_module_y(n,u,i){var o=hooks_module_h(hooks_module_t++,2);if(o.t=n,!o.__c&&(o.__=[i?i(u):hooks_module_D(void 0,u),function(n){var t=o.__N?o.__N[0]:o.__[0],r=o.t(t,n);t!==r&&(o.__N=[r,o.__[1]],o.__c.setState({}))}],o.__c=hooks_module_r,!hooks_module_r.u)){var f=function(n,t,r){if(!o.__c.__H)return!0;var u=o.__c.__H.__.filter(function(n){return!!n.__c});if(u.every(function(n){return!n.__N}))return!c||c.call(this,n,t,r);var i=!1;return u.forEach(function(n){if(n.__N){var t=n.__[0];n.__=n.__N,n.__N=void 0,t!==n.__[0]&&(i=!0)}}),!(!i&&o.__c.props===n)&&(!c||c.call(this,n,t,r))};hooks_module_r.u=!0;var c=hooks_module_r.shouldComponentUpdate,e=hooks_module_r.componentWillUpdate;hooks_module_r.componentWillUpdate=function(n,t,r){if(this.__e){var u=c;c=void 0,f(n,t,r),c=u}e&&e.call(this,n,t,r)},hooks_module_r.shouldComponentUpdate=f}return o.__N||o.__}function hooks_module_(n,u){var i=hooks_module_h(hooks_module_t++,3);!hooks_module_e.__s&&hooks_module_C(i.__H,u)&&(i.__=n,i.i=u,hooks_module_r.__H.__h.push(i))}function hooks_module_A(n,u){var i=hooks_module_h(hooks_module_t++,4);!hooks_module_e.__s&&hooks_module_C(i.__H,u)&&(i.__=n,i.i=u,hooks_module_r.__h.push(i))}function hooks_module_F(n){return hooks_module_o=5,hooks_module_q(function(){return{current:n}},[])}function hooks_module_T(n,t,r){hooks_module_o=6,hooks_module_A(function(){return"function"==typeof n?(n(t()),function(){return n(null)}):n?(n.current=t(),function(){return n.current=null}):void 0},null==r?r:r.concat(n))}function hooks_module_q(n,r){var u=hooks_module_h(hooks_module_t++,7);return hooks_module_C(u.__H,r)?(u.__V=n(),u.i=r,u.__h=n,u.__V):u.__}function hooks_module_x(n,t){return hooks_module_o=8,hooks_module_q(function(){return n},t)}function hooks_module_P(n){var u=hooks_module_r.context[n.__c],i=hooks_module_h(hooks_module_t++,9);return i.c=n,u?(null==i.__&&(i.__=!0,u.sub(hooks_module_r)),u.props.value):n.__}function hooks_module_V(n,t){hooks_module_e.useDebugValue&&hooks_module_e.useDebugValue(t?t(n):n)}function hooks_module_b(n){var u=hooks_module_h(hooks_module_t++,10),i=hooks_module_p();return u.__=n,hooks_module_r.componentDidCatch||(hooks_module_r.componentDidCatch=function(n,t){u.__&&u.__(n,t),i[1](n)}),[i[0],function(){i[1](void 0)}]}function hooks_module_g(){var n=hooks_module_h(hooks_module_t++,11);if(!n.__){for(var u=hooks_module_r.__v;null!==u&&!u.__m&&null!==u.__;)u=u.__;var i=u.__m||(u.__m=[0,0]);n.__="P"+i[0]+"-"+i[1]++}return n.__}function hooks_module_j(){for(var n;n=hooks_module_f.shift();)if(n.__P&&n.__H)try{n.__H.__h.forEach(hooks_module_z),n.__H.__h.forEach(hooks_module_B),n.__H.__h=[]}catch(t){n.__H.__h=[],hooks_module_e.__e(t,n.__v)}}hooks_module_e.__b=function(n){hooks_module_r=null,hooks_module_a&&hooks_module_a(n)},hooks_module_e.__=function(n,t){n&&t.__k&&t.__k.__m&&(n.__m=t.__k.__m),hooks_module_d&&hooks_module_d(n,t)},hooks_module_e.__r=function(n){hooks_module_v&&hooks_module_v(n),hooks_module_t=0;var i=(hooks_module_r=n.__c).__H;i&&(hooks_module_u===hooks_module_r?(i.__h=[],hooks_module_r.__h=[],i.__.forEach(function(n){n.__N&&(n.__=n.__N),n.__V=hooks_module_c,n.__N=n.i=void 0})):(i.__h.forEach(hooks_module_z),i.__h.forEach(hooks_module_B),i.__h=[],hooks_module_t=0)),hooks_module_u=hooks_module_r},hooks_module_e.diffed=function(n){hooks_module_l&&hooks_module_l(n);var t=n.__c;t&&t.__H&&(t.__H.__h.length&&(1!==hooks_module_f.push(t)&&hooks_module_i===hooks_module_e.requestAnimationFrame||((hooks_module_i=hooks_module_e.requestAnimationFrame)||hooks_module_w)(hooks_module_j)),t.__H.__.forEach(function(n){n.i&&(n.__H=n.i),n.__V!==hooks_module_c&&(n.__=n.__V),n.i=void 0,n.__V=hooks_module_c})),hooks_module_u=hooks_module_r=null},hooks_module_e.__c=function(n,t){t.some(function(n){try{n.__h.forEach(hooks_module_z),n.__h=n.__h.filter(function(n){return!n.__||hooks_module_B(n)})}catch(r){t.some(function(n){n.__h&&(n.__h=[])}),t=[],hooks_module_e.__e(r,n.__v)}}),hooks_module_m&&hooks_module_m(n,t)},hooks_module_e.unmount=function(n){hooks_module_s&&hooks_module_s(n);var t,r=n.__c;r&&r.__H&&(r.__H.__.forEach(function(n){try{hooks_module_z(n)}catch(n){t=n}}),r.__H=void 0,t&&hooks_module_e.__e(t,r.__v))};var hooks_module_k="function"==typeof requestAnimationFrame;function hooks_module_w(n){var t,r=function(){clearTimeout(u),hooks_module_k&&cancelAnimationFrame(t),setTimeout(n)},u=setTimeout(r,100);hooks_module_k&&(t=requestAnimationFrame(r))}function hooks_module_z(n){var t=hooks_module_r,u=n.__c;"function"==typeof u&&(n.__c=void 0,u()),hooks_module_r=t}function hooks_module_B(n){var t=hooks_module_r;n.__c=n.__(),hooks_module_r=t}function hooks_module_C(n,t){return!n||n.length!==t.length||t.some(function(t,r){return t!==n[r]})}function hooks_module_D(n,t){return"function"==typeof t?t(n):t}

;// CONCATENATED MODULE: ./node_modules/@preact/signals-core/dist/signals-core.module.js
var signals_core_module_i=Symbol.for("preact-signals");function signals_core_module_t(){if(!(signals_core_module_s>1)){var i,t=!1;while(void 0!==signals_core_module_h){var r=signals_core_module_h;signals_core_module_h=void 0;signals_core_module_f++;while(void 0!==r){var o=r.o;r.o=void 0;r.f&=-3;if(!(8&r.f)&&signals_core_module_c(r))try{r.c()}catch(r){if(!t){i=r;t=!0}}r=o}}signals_core_module_f=0;signals_core_module_s--;if(t)throw i}else signals_core_module_s--}function signals_core_module_r(i){if(signals_core_module_s>0)return i();signals_core_module_s++;try{return i()}finally{signals_core_module_t()}}var signals_core_module_o=void 0;function signals_core_module_n(i){var t=signals_core_module_o;signals_core_module_o=void 0;try{return i()}finally{signals_core_module_o=t}}var signals_core_module_h=void 0,signals_core_module_s=0,signals_core_module_f=0,signals_core_module_v=0;function signals_core_module_e(i){if(void 0!==signals_core_module_o){var t=i.n;if(void 0===t||t.t!==signals_core_module_o){t={i:0,S:i,p:signals_core_module_o.s,n:void 0,t:signals_core_module_o,e:void 0,x:void 0,r:t};if(void 0!==signals_core_module_o.s)signals_core_module_o.s.n=t;signals_core_module_o.s=t;i.n=t;if(32&signals_core_module_o.f)i.S(t);return t}else if(-1===t.i){t.i=0;if(void 0!==t.n){t.n.p=t.p;if(void 0!==t.p)t.p.n=t.n;t.p=signals_core_module_o.s;t.n=void 0;signals_core_module_o.s.n=t;signals_core_module_o.s=t}return t}}}function signals_core_module_u(i){this.v=i;this.i=0;this.n=void 0;this.t=void 0}signals_core_module_u.prototype.brand=signals_core_module_i;signals_core_module_u.prototype.h=function(){return!0};signals_core_module_u.prototype.S=function(i){if(this.t!==i&&void 0===i.e){i.x=this.t;if(void 0!==this.t)this.t.e=i;this.t=i}};signals_core_module_u.prototype.U=function(i){if(void 0!==this.t){var t=i.e,r=i.x;if(void 0!==t){t.x=r;i.e=void 0}if(void 0!==r){r.e=t;i.x=void 0}if(i===this.t)this.t=r}};signals_core_module_u.prototype.subscribe=function(i){var t=this;return signals_core_module_E(function(){var r=t.value,n=signals_core_module_o;signals_core_module_o=void 0;try{i(r)}finally{signals_core_module_o=n}})};signals_core_module_u.prototype.valueOf=function(){return this.value};signals_core_module_u.prototype.toString=function(){return this.value+""};signals_core_module_u.prototype.toJSON=function(){return this.value};signals_core_module_u.prototype.peek=function(){var i=signals_core_module_o;signals_core_module_o=void 0;try{return this.value}finally{signals_core_module_o=i}};Object.defineProperty(signals_core_module_u.prototype,"value",{get:function(){var i=signals_core_module_e(this);if(void 0!==i)i.i=this.i;return this.v},set:function(i){if(i!==this.v){if(signals_core_module_f>100)throw new Error("Cycle detected");this.v=i;this.i++;signals_core_module_v++;signals_core_module_s++;try{for(var r=this.t;void 0!==r;r=r.x)r.t.N()}finally{signals_core_module_t()}}}});function signals_core_module_d(i){return new signals_core_module_u(i)}function signals_core_module_c(i){for(var t=i.s;void 0!==t;t=t.n)if(t.S.i!==t.i||!t.S.h()||t.S.i!==t.i)return!0;return!1}function signals_core_module_a(i){for(var t=i.s;void 0!==t;t=t.n){var r=t.S.n;if(void 0!==r)t.r=r;t.S.n=t;t.i=-1;if(void 0===t.n){i.s=t;break}}}function signals_core_module_l(i){var t=i.s,r=void 0;while(void 0!==t){var o=t.p;if(-1===t.i){t.S.U(t);if(void 0!==o)o.n=t.n;if(void 0!==t.n)t.n.p=o}else r=t;t.S.n=t.r;if(void 0!==t.r)t.r=void 0;t=o}i.s=r}function signals_core_module_y(i){signals_core_module_u.call(this,void 0);this.x=i;this.s=void 0;this.g=signals_core_module_v-1;this.f=4}(signals_core_module_y.prototype=new signals_core_module_u).h=function(){this.f&=-3;if(1&this.f)return!1;if(32==(36&this.f))return!0;this.f&=-5;if(this.g===signals_core_module_v)return!0;this.g=signals_core_module_v;this.f|=1;if(this.i>0&&!signals_core_module_c(this)){this.f&=-2;return!0}var i=signals_core_module_o;try{signals_core_module_a(this);signals_core_module_o=this;var t=this.x();if(16&this.f||this.v!==t||0===this.i){this.v=t;this.f&=-17;this.i++}}catch(i){this.v=i;this.f|=16;this.i++}signals_core_module_o=i;signals_core_module_l(this);this.f&=-2;return!0};signals_core_module_y.prototype.S=function(i){if(void 0===this.t){this.f|=36;for(var t=this.s;void 0!==t;t=t.n)t.S.S(t)}signals_core_module_u.prototype.S.call(this,i)};signals_core_module_y.prototype.U=function(i){if(void 0!==this.t){signals_core_module_u.prototype.U.call(this,i);if(void 0===this.t){this.f&=-33;for(var t=this.s;void 0!==t;t=t.n)t.S.U(t)}}};signals_core_module_y.prototype.N=function(){if(!(2&this.f)){this.f|=6;for(var i=this.t;void 0!==i;i=i.x)i.t.N()}};Object.defineProperty(signals_core_module_y.prototype,"value",{get:function(){if(1&this.f)throw new Error("Cycle detected");var i=signals_core_module_e(this);this.h();if(void 0!==i)i.i=this.i;if(16&this.f)throw this.v;return this.v}});function signals_core_module_w(i){return new signals_core_module_y(i)}function signals_core_module_(i){var r=i.u;i.u=void 0;if("function"==typeof r){signals_core_module_s++;var n=signals_core_module_o;signals_core_module_o=void 0;try{r()}catch(t){i.f&=-2;i.f|=8;signals_core_module_g(i);throw t}finally{signals_core_module_o=n;signals_core_module_t()}}}function signals_core_module_g(i){for(var t=i.s;void 0!==t;t=t.n)t.S.U(t);i.x=void 0;i.s=void 0;signals_core_module_(i)}function signals_core_module_p(i){if(signals_core_module_o!==this)throw new Error("Out-of-order effect");signals_core_module_l(this);signals_core_module_o=i;this.f&=-2;if(8&this.f)signals_core_module_g(this);signals_core_module_t()}function signals_core_module_b(i){this.x=i;this.u=void 0;this.s=void 0;this.o=void 0;this.f=32}signals_core_module_b.prototype.c=function(){var i=this.S();try{if(8&this.f)return;if(void 0===this.x)return;var t=this.x();if("function"==typeof t)this.u=t}finally{i()}};signals_core_module_b.prototype.S=function(){if(1&this.f)throw new Error("Cycle detected");this.f|=1;this.f&=-9;signals_core_module_(this);signals_core_module_a(this);signals_core_module_s++;var i=signals_core_module_o;signals_core_module_o=this;return signals_core_module_p.bind(this,i)};signals_core_module_b.prototype.N=function(){if(!(2&this.f)){this.f|=2;this.o=signals_core_module_h;signals_core_module_h=this}};signals_core_module_b.prototype.d=function(){this.f|=8;if(!(1&this.f))signals_core_module_g(this)};function signals_core_module_E(i){var t=new signals_core_module_b(i);try{t.c()}catch(i){t.d();throw i}return t.d.bind(t)}
;// CONCATENATED MODULE: ./node_modules/@preact/signals/dist/signals.module.js
var signals_module_v,signals_module_s;function signals_module_l(n,i){preact_module_l[n]=i.bind(null,preact_module_l[n]||function(){})}function signals_module_d(n){if(signals_module_s)signals_module_s();signals_module_s=n&&n.S()}function signals_module_p(n){var r=this,f=n.data,o=useSignal(f);o.value=f;var e=hooks_module_q(function(){var n=r.__v;while(n=n.__)if(n.__c){n.__c.__$f|=4;break}r.__$u.c=function(){var n;if(!preact_module_t(e.peek())&&3===(null==(n=r.base)?void 0:n.nodeType))r.base.data=e.peek();else{r.__$f|=1;r.setState({})}};return signals_core_module_w(function(){var n=o.value.value;return 0===n?0:!0===n?"":n||""})},[]);return e.value}signals_module_p.displayName="_st";Object.defineProperties(signals_core_module_u.prototype,{constructor:{configurable:!0,value:void 0},type:{configurable:!0,value:signals_module_p},props:{configurable:!0,get:function(){return{data:this}}},__b:{configurable:!0,value:1}});signals_module_l("__b",function(n,r){if("string"==typeof r.type){var i,t=r.props;for(var f in t)if("children"!==f){var o=t[f];if(o instanceof signals_core_module_u){if(!i)r.__np=i={};i[f]=o;t[f]=o.peek()}}}n(r)});signals_module_l("__r",function(n,r){signals_module_d();var i,t=r.__c;if(t){t.__$f&=-2;if(void 0===(i=t.__$u))t.__$u=i=function(n){var r;signals_core_module_E(function(){r=this});r.c=function(){t.__$f|=1;t.setState({})};return r}()}signals_module_v=t;signals_module_d(i);n(r)});signals_module_l("__e",function(n,r,i,t){signals_module_d();signals_module_v=void 0;n(r,i,t)});signals_module_l("diffed",function(n,r){signals_module_d();signals_module_v=void 0;var i;if("string"==typeof r.type&&(i=r.__e)){var t=r.__np,f=r.props;if(t){var o=i.U;if(o)for(var e in o){var u=o[e];if(void 0!==u&&!(e in t)){u.d();o[e]=void 0}}else i.U=o={};for(var a in t){var c=o[a],s=t[a];if(void 0===c){c=signals_module_(i,a,s,f);o[a]=c}else c.o(s,f)}}}n(r)});function signals_module_(n,r,i,t){var f=r in n&&void 0===n.ownerSVGElement,o=signals_core_module_d(i);return{o:function(n,r){o.value=n;t=r},d:signals_core_module_E(function(){var i=o.value.value;if(t[r]!==i){t[r]=i;if(f)n[r]=i;else if(i)n.setAttribute(r,i);else n.removeAttribute(r)}})}}signals_module_l("unmount",function(n,r){if("string"==typeof r.type){var i=r.__e;if(i){var t=i.U;if(t){i.U=void 0;for(var f in t){var o=t[f];if(o)o.d()}}}}else{var e=r.__c;if(e){var u=e.__$u;if(u){e.__$u=void 0;u.d()}}}n(r)});signals_module_l("__h",function(n,r,i,t){if(t<3||9===t)r.__$f|=2;n(r,i,t)});b.prototype.shouldComponentUpdate=function(n,r){var i=this.__$u;if(!(i&&void 0!==i.s||4&this.__$f))return!0;if(3&this.__$f)return!0;for(var t in r)return!0;for(var f in n)if("__source"!==f&&n[f]!==this.props[f])return!0;for(var o in this.props)if(!(o in n))return!0;return!1};function useSignal(n){return hooks_module_q(function(){return signals_core_module_d(n)},[])}function useComputed(n){var r=f(n);r.current=n;signals_module_v.__$f|=4;return t(function(){return u(function(){return r.current()})},[])}function useSignalEffect(n){var r=f(n);r.current=n;o(function(){return c(function(){return r.current()})},[])}
;// CONCATENATED MODULE: ./node_modules/deepsignal/dist/deepsignal.module.js
var deepsignal_module_a=new WeakMap,deepsignal_module_o=new WeakMap,deepsignal_module_s=new WeakMap,deepsignal_module_u=new WeakSet,deepsignal_module_c=new WeakMap,deepsignal_module_f=/^\$/,deepsignal_module_i=Object.getOwnPropertyDescriptor,deepsignal_module_l=!1,deepsignal_module_g=function(e){if(!deepsignal_module_k(e))throw new Error("This object can't be observed.");return deepsignal_module_o.has(e)||deepsignal_module_o.set(e,deepsignal_module_v(e,deepsignal_module_d)),deepsignal_module_o.get(e)},deepsignal_module_p=function(e,t){deepsignal_module_l=!0;var r=e[t];try{deepsignal_module_l=!1}catch(e){}return r};function deepsignal_module_h(e){return deepsignal_module_u.add(e),e}var deepsignal_module_v=function(e,t){var r=new Proxy(e,t);return deepsignal_module_u.add(r),r},deepsignal_module_y=function(){throw new Error("Don't mutate the signals directly.")},deepsignal_module_w=function(e){return function(t,u,c){var g;if(deepsignal_module_l)return Reflect.get(t,u,c);var p=e||"$"===u[0];if(!e&&p&&Array.isArray(t)){if("$"===u)return deepsignal_module_s.has(t)||deepsignal_module_s.set(t,deepsignal_module_v(t,deepsignal_module_m)),deepsignal_module_s.get(t);p="$length"===u}deepsignal_module_a.has(c)||deepsignal_module_a.set(c,new Map);var h=deepsignal_module_a.get(c),y=p?u.replace(deepsignal_module_f,""):u;if(h.has(y)||"function"!=typeof(null==(g=deepsignal_module_i(t,y))?void 0:g.get)){var w=Reflect.get(t,y,c);if(p&&"function"==typeof w)return;if("symbol"==typeof y&&deepsignal_module_b.has(y))return w;h.has(y)||(deepsignal_module_k(w)&&(deepsignal_module_o.has(w)||deepsignal_module_o.set(w,deepsignal_module_v(w,deepsignal_module_d)),w=deepsignal_module_o.get(w)),h.set(y,signals_core_module_d(w)))}else h.set(y,signals_core_module_w(function(){return Reflect.get(t,y,c)}));return p?h.get(y):h.get(y).value}},deepsignal_module_d={get:deepsignal_module_w(!1),set:function(e,n,s,u){var l;if("function"==typeof(null==(l=deepsignal_module_i(e,n))?void 0:l.set))return Reflect.set(e,n,s,u);deepsignal_module_a.has(u)||deepsignal_module_a.set(u,new Map);var g=deepsignal_module_a.get(u);if("$"===n[0]){s instanceof signals_core_module_u||deepsignal_module_y();var p=n.replace(deepsignal_module_f,"");return g.set(p,s),Reflect.set(e,p,s.peek(),u)}var h=s;deepsignal_module_k(s)&&(deepsignal_module_o.has(s)||deepsignal_module_o.set(s,deepsignal_module_v(s,deepsignal_module_d)),h=deepsignal_module_o.get(s));var w=!(n in e),m=Reflect.set(e,n,s,u);return g.has(n)?g.get(n).value=h:g.set(n,signals_core_module_d(h)),w&&deepsignal_module_c.has(e)&&deepsignal_module_c.get(e).value++,Array.isArray(e)&&g.has("length")&&(g.get("length").value=e.length),m},deleteProperty:function(e,t){"$"===t[0]&&deepsignal_module_y();var r=deepsignal_module_a.get(deepsignal_module_o.get(e)),n=Reflect.deleteProperty(e,t);return r&&r.has(t)&&(r.get(t).value=void 0),deepsignal_module_c.has(e)&&deepsignal_module_c.get(e).value++,n},ownKeys:function(e){return deepsignal_module_c.has(e)||deepsignal_module_c.set(e,signals_core_module_d(0)),deepsignal_module_c._=deepsignal_module_c.get(e).value,Reflect.ownKeys(e)}},deepsignal_module_m={get:deepsignal_module_w(!0),set:deepsignal_module_y,deleteProperty:deepsignal_module_y},deepsignal_module_b=new Set(Object.getOwnPropertyNames(Symbol).map(function(e){return Symbol[e]}).filter(function(e){return"symbol"==typeof e})),R=new Set([Object,Array]),deepsignal_module_k=function(e){return"object"==typeof e&&null!==e&&R.has(e.constructor)&&!deepsignal_module_u.has(e)},deepsignal_module_M=function(t){return e(function(){return deepsignal_module_g(t)},[])};
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/store.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */

const isObject = item => Boolean(item && typeof item === 'object' && item.constructor === Object);
const deepMerge = (target, source) => {
  if (isObject(target) && isObject(source)) {
    for (const key in source) {
      const getter = Object.getOwnPropertyDescriptor(source, key)?.get;
      if (typeof getter === 'function') {
        Object.defineProperty(target, key, {
          get: getter
        });
      } else if (isObject(source[key])) {
        if (!target[key]) {
          target[key] = {};
        }
        deepMerge(target[key], source[key]);
      } else {
        try {
          target[key] = source[key];
        } catch (e) {
          // Assignemnts fail for properties that are only getters.
          // When that's the case, the assignment is simply ignored.
        }
      }
    }
  }
};
const stores = new Map();
const rawStores = new Map();
const storeLocks = new Map();
const storeConfigs = new Map();
const objToProxy = new WeakMap();
const proxyToNs = new WeakMap();
const scopeToGetters = new WeakMap();
const proxify = (obj, ns) => {
  if (!objToProxy.has(obj)) {
    const proxy = new Proxy(obj, handlers);
    objToProxy.set(obj, proxy);
    proxyToNs.set(proxy, ns);
  }
  return objToProxy.get(obj);
};
const handlers = {
  get: (target, key, receiver) => {
    const ns = proxyToNs.get(receiver);

    // Check if the property is a getter and we are inside an scope. If that is
    // the case, we clone the getter to avoid overwriting the scoped
    // dependencies of the computed each time that getter runs.
    const getter = Object.getOwnPropertyDescriptor(target, key)?.get;
    if (getter) {
      const scope = getScope();
      if (scope) {
        const getters = scopeToGetters.get(scope) || scopeToGetters.set(scope, new Map()).get(scope);
        if (!getters.has(getter)) {
          getters.set(getter, signals_core_module_w(() => {
            setNamespace(ns);
            setScope(scope);
            try {
              return getter.call(target);
            } finally {
              resetScope();
              resetNamespace();
            }
          }));
        }
        return getters.get(getter).value;
      }
    }
    const result = Reflect.get(target, key);

    // Check if the proxy is the store root and no key with that name exist. In
    // that case, return an empty object for the requested key.
    if (typeof result === 'undefined' && receiver === stores.get(ns)) {
      const obj = {};
      Reflect.set(target, key, obj);
      return proxify(obj, ns);
    }

    // Check if the property is a generator. If it is, we turn it into an
    // asynchronous function where we restore the default namespace and scope
    // each time it awaits/yields.
    if (result?.constructor?.name === 'GeneratorFunction') {
      return async (...args) => {
        const scope = getScope();
        const gen = result(...args);
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
    }

    // Check if the property is a synchronous function. If it is, set the
    // default namespace. Synchronous functions always run in the proper scope,
    // which is set by the Directives component.
    if (typeof result === 'function') {
      return (...args) => {
        setNamespace(ns);
        try {
          return result(...args);
        } finally {
          resetNamespace();
        }
      };
    }

    // Check if the property is an object. If it is, proxyify it.
    if (isObject(result)) {
      return proxify(result, ns);
    }
    return result;
  },
  // Prevents passing the current proxy as the receiver to the deepSignal.
  set(target, key, value) {
    return Reflect.set(target, key, value);
  }
};

/**
 * Get the defined config for the store with the passed namespace.
 *
 * @param namespace Store's namespace from which to retrieve the config.
 * @return Defined config for the given namespace.
 */
const getConfig = namespace => storeConfigs.get(namespace || getNamespace()) || {};
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
      state: deepsignal_module_g(isObject(state) ? state : {}),
      ...block
    };
    const proxiedStore = new Proxy(rawStore, handlers);
    rawStores.set(namespace, rawStore);
    stores.set(namespace, proxiedStore);
    proxyToNs.set(proxiedStore, namespace);
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
const parseInitialData = (dom = document) => {
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
const populateInitialData = data => {
  if (isObject(data?.state)) {
    Object.entries(data.state).forEach(([namespace, state]) => {
      store(namespace, {
        state
      }, {
        lock: universalUnlock
      });
    });
  }
  if (isObject(data?.config)) {
    Object.entries(data.config).forEach(([namespace, config]) => {
      storeConfigs.set(namespace, config);
    });
  }
};

// Parse and populate the initial state and config.
const data = parseInitialData();
populateInitialData(data);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/hooks.js
// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react-hooks/exhaustive-deps */

/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


// Main context.
const context = G({});

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

// Store stacks for the current scope and the default namespaces and export APIs
// to interact with them.
const scopeStack = [];
const namespaceStack = [];

/**
 * Retrieves the context inherited by the element evaluating a function from the
 * store. The returned value depends on the element and the namespace where the
 * function calling `getContext()` exists.
 *
 * @param namespace Store namespace. By default, the namespace where the calling
 *                  function exists is used.
 * @return The context content.
 */
const getContext = namespace => getScope()?.context[namespace || getNamespace()];

/**
 * Retrieves a representation of the element where a function from the store
 * is being evalutated. Such representation is read-only, and contains a
 * reference to the DOM element, its props and a local reactive state.
 *
 * @return Element representation.
 */
const getElement = () => {
  if (!getScope()) {
    throw Error('Cannot call `getElement()` outside getters and actions used by directives.');
  }
  const {
    ref,
    attributes
  } = getScope();
  return Object.freeze({
    ref: ref.current,
    attributes: deepImmutable(attributes)
  });
};
const getScope = () => scopeStack.slice(-1)[0];
const setScope = scope => {
  scopeStack.push(scope);
};
const resetScope = () => {
  scopeStack.pop();
};
const getNamespace = () => namespaceStack.slice(-1)[0];
const setNamespace = namespace => {
  namespaceStack.push(namespace);
};
const resetNamespace = () => {
  namespaceStack.pop();
};

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
 *     const defaultEntry = alert.find( entry => entry.suffix === 'default' );
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
 * value (`state.alert`) from the directive entry with suffix `default`. A
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
 *   ( { directives: { color }, ref, evaluate } ) =>
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
    resolvedStore = store(namespace, undefined, {
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
}) => (entry, ...args) => {
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
  const result = typeof value === 'function' ? value(...args) : value;
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
  const scope = hooks_module_F({}).current;
  scope.evaluate = hooks_module_x(getEvaluate({
    scope
  }), []);
  scope.context = hooks_module_P(context);
  /* eslint-disable react-hooks/rules-of-hooks */
  scope.ref = previousScope?.ref || hooks_module_F(null);
  /* eslint-enable react-hooks/rules-of-hooks */

  // Create a fresh copy of the vnode element and add the props to the scope,
  // named as attributes (HTML Attributes).
  element = E(element, {
    ref: scope.ref
  });
  scope.attributes = element.props;

  // Recursively render the wrapper for the next priority level.
  const children = nextPriorityLevels.length > 0 ? _(Directives, {
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
const old = preact_module_l.vnode;
preact_module_l.vnode = vnode => {
  if (vnode.props.__directives) {
    const props = vnode.props;
    const directives = props.__directives;
    if (directives.key) {
      vnode.key = directives.key.find(({
        suffix
      }) => suffix === 'default').value;
    }
    delete props.__directives;
    const priorityLevels = getPriorityLevels(directives);
    if (priorityLevels.length > 0) {
      vnode.props = {
        directives,
        priorityLevels,
        originalProps: props,
        type: vnode.type,
        element: _(vnode.type, props),
        top: true
      };
      vnode.type = Directives;
    }
  }
  if (old) {
    old(vnode);
  }
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/utils.js
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
 * @return Promise
 */
const splitTask = () => {
  return new Promise(resolve => {
    // TODO: Use scheduler.yield() when available.
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
  const dispose = signals_core_module_E(function () {
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
  hooks_module_(() => {
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
 * Asyncronous functions should use generators that yield promises instead of awaiting them.
 * See the documentation for details: https://developer.wordpress.org/block-editor/reference-guides/packages/packages-interactivity/packages-interactivity-api-reference/#the-store
 *
 * @param func The passed function.
 * @return The wrapped function.
 */

function withScope(func) {
  const scope = getScope();
  const ns = getNamespace();
  if (func?.constructor?.name === 'GeneratorFunction') {
    return async (...args) => {
      const gen = func(...args);
      let value;
      let it;
      while (true) {
        setNamespace(ns);
        setScope(scope);
        try {
          it = gen.next(value);
        } finally {
          resetNamespace();
          resetScope();
        }
        try {
          value = await it.value;
        } catch (e) {
          gen.throw(e);
        }
        if (it.done) {
          break;
        }
      }
      return value;
    };
  }
  return (...args) => {
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
 * element's first render, mainly useful for intialization logic.
 *
 * This hook makes the element's scope available so functions like
 * `getElement()` and `getContext()` can be used inside the passed callback.
 *
 * @param callback The hook callback.
 */
function useInit(callback) {
  hooks_module_(withScope(callback), []);
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
  hooks_module_(withScope(callback), inputs);
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
  hooks_module_A(withScope(callback), inputs);
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
  return hooks_module_x(withScope(callback), inputs);
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
  return hooks_module_q(withScope(factory), inputs);
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/directives.js
// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react-hooks/exhaustive-deps */

/**
 * External dependencies
 */




/**
 * Internal dependencies
 */



// Assigned objects should be ignored during proxification.
const contextAssignedObjects = new WeakMap();

// Store the context proxy and fallback for each object in the context.
const contextObjectToProxy = new WeakMap();
const contextProxyToObject = new WeakMap();
const contextObjectToFallback = new WeakMap();
const isPlainObject = item => Boolean(item && typeof item === 'object' && item.constructor === Object);
const descriptor = Reflect.getOwnPropertyDescriptor;

/**
 * Wrap a context object with a proxy to reproduce the context stack. The proxy
 * uses the passed `inherited` context as a fallback to look up for properties
 * that don't exist in the given context. Also, updated properties are modified
 * where they are defined, or added to the main context when they don't exist.
 *
 * By default, all plain objects inside the context are wrapped, unless it is
 * listed in the `ignore` option.
 *
 * @param current   Current context.
 * @param inherited Inherited context, used as fallback.
 *
 * @return The wrapped context object.
 */
const proxifyContext = (current, inherited = {}) => {
  // Update the fallback object reference when it changes.
  contextObjectToFallback.set(current, inherited);
  if (!contextObjectToProxy.has(current)) {
    const proxy = new Proxy(current, {
      get: (target, k) => {
        const fallback = contextObjectToFallback.get(current);
        // Always subscribe to prop changes in the current context.
        const currentProp = target[k];

        // Return the inherited prop when missing in target.
        if (!(k in target) && k in fallback) {
          return fallback[k];
        }

        // Proxify plain objects that were not directly assigned.
        if (k in target && !contextAssignedObjects.get(target)?.has(k) && isPlainObject(deepsignal_module_p(target, k))) {
          return proxifyContext(currentProp, fallback[k]);
        }

        // Return the stored proxy for `currentProp` when it exists.
        if (contextObjectToProxy.has(currentProp)) {
          return contextObjectToProxy.get(currentProp);
        }

        /*
         * For other cases, return the value from target, also
         * subscribing to changes in the parent context when the current
         * prop is not defined.
         */
        return k in target ? currentProp : fallback[k];
      },
      set: (target, k, value) => {
        const fallback = contextObjectToFallback.get(current);
        const obj = k in target || !(k in fallback) ? target : fallback;

        /*
         * Assigned object values should not be proxified so they point
         * to the original object and don't inherit unexpected
         * properties.
         */
        if (value && typeof value === 'object') {
          if (!contextAssignedObjects.has(obj)) {
            contextAssignedObjects.set(obj, new Set());
          }
          contextAssignedObjects.get(obj).add(k);
        }

        /*
         * When the value is a proxy, it's because it comes from the
         * context, so the inner value is assigned instead.
         */
        if (contextProxyToObject.has(value)) {
          const innerValue = contextProxyToObject.get(value);
          obj[k] = innerValue;
        } else {
          obj[k] = value;
        }
        return true;
      },
      ownKeys: target => [...new Set([...Object.keys(contextObjectToFallback.get(current)), ...Object.keys(target)])],
      getOwnPropertyDescriptor: (target, k) => descriptor(target, k) || descriptor(contextObjectToFallback.get(current), k)
    });
    contextObjectToProxy.set(current, proxy);
    contextProxyToObject.set(proxy, current);
  }
  return contextObjectToProxy.get(current);
};

/**
 * Recursively update values within a deepSignal object.
 *
 * @param target A deepSignal instance.
 * @param source Object with properties to update in `target`.
 */
const updateSignals = (target, source) => {
  for (const k in source) {
    if (isPlainObject(deepsignal_module_p(target, k)) && isPlainObject(deepsignal_module_p(source, k))) {
      updateSignals(target[`$${k}`].peek(), source[k]);
    } else {
      target[k] = source[k];
    }
  }
};

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
    directives[`on-${type}`].filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      const eventName = entry.suffix.split('--', 1)[0];
      useInit(() => {
        const cb = event => evaluate(entry, event);
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
    directives[`on-async-${type}`].filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      const eventName = entry.suffix.split('--', 1)[0];
      useInit(() => {
        const cb = async event => {
          await splitTask();
          evaluate(entry, event);
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
  directive('context',
  // @ts-ignore-next-line
  ({
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
    const inheritedValue = hooks_module_P(inheritedContext);
    const currentValue = hooks_module_F(deepsignal_module_g({}));
    const defaultEntry = context.find(({
      suffix
    }) => suffix === 'default');

    // No change should be made if `defaultEntry` does not exist.
    const contextStack = hooks_module_q(() => {
      if (defaultEntry) {
        const {
          namespace,
          value
        } = defaultEntry;
        // Check that the value is a JSON object. Send a console warning if not.
        if (!isPlainObject(value)) {
          warn(`The value of data-wp-context in "${namespace}" store must be a valid stringified JSON object.`);
        }
        updateSignals(currentValue.current, {
          [namespace]: deepClone(value)
        });
      }
      return proxifyContext(currentValue.current, inheritedValue);
    }, [defaultEntry, inheritedValue]);
    return _(Provider, {
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
      useWatch(() => evaluate(entry));
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
      useInit(() => evaluate(entry));
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
    on.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
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
          evaluate(entry, event);
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
    onAsync.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
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
          evaluate(entry, event);
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
    classNames.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      const className = entry.suffix;
      const result = evaluate(entry);
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
    style.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      const styleProp = entry.suffix;
      const result = evaluate(entry);
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
    bind.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      const attribute = entry.suffix;
      const result = evaluate(entry);
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
    const cached = hooks_module_q(() => innerHTML, []);
    return _(Type, {
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
    const entry = text.find(({
      suffix
    }) => suffix === 'default');
    if (!entry) {
      element.props.children = null;
      return;
    }
    try {
      const result = evaluate(entry);
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
    run.forEach(entry => evaluate(entry));
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
    const inheritedValue = hooks_module_P(inheritedContext);
    const [entry] = each;
    const {
      namespace,
      suffix
    } = entry;
    const list = evaluate(entry);
    return list.map(item => {
      const itemProp = suffix === 'default' ? 'item' : kebabToCamelCase(suffix);
      const itemContext = deepsignal_module_g({
        [namespace]: {}
      });
      const mergedContext = proxifyContext(itemContext, inheritedValue);

      // Set the item after proxifying the context.
      mergedContext[namespace][itemProp] = item;
      const scope = {
        ...getScope(),
        context: mergedContext
      };
      const key = eachKey ? getEvaluate({
        scope
      })(eachKey[0]) : item;
      return _(Provider, {
        value: mergedContext,
        key
      }, element.props.content);
    });
  }, {
    priority: 20
  });
  directive('each-child', () => null, {
    priority: 1
  });
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/constants.js
const directivePrefix = 'wp';

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/vdom.js
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
const vdom_isObject = item => Boolean(item && typeof item === 'object' && item.constructor === Object);

// Regular expression for directive parsing.
const directiveParser = new RegExp(`^data-${directivePrefix}-` +
// ${p} must be a prefix string, like 'wp'.
// Match alphanumeric characters including hyphen-separated
// segments. It excludes underscore intentionally to prevent confusion.
// E.g., "custom-directive".
'([a-z0-9]+(?:-[a-z0-9]+)*)' +
// (Optional) Match '--' followed by any alphanumeric charachters. It
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
            value = vdom_isObject(parsedValue) ? parsedValue : value;
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
      return [_(localName, {
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
        const suffix = directiveMatch[2] || 'default';
        obj[prefix] = obj[prefix] || [];
        obj[prefix].push({
          namespace: ns !== null && ns !== void 0 ? ns : currentNamespace(),
          value,
          suffix
        });
        return obj;
      }, {});
    }

    // @ts-expect-error Fixed in upcoming preact release https://github.com/preactjs/preact/pull/4334
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
    return [_(localName, props, children)];
  }
  return walk(treeWalker.currentNode);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/init.js
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
  for (const node of nodes) {
    if (!hydratedIslands.has(node)) {
      await splitTask();
      const fragment = getRegionRootFragment(node);
      const vdom = toVdom(node);
      initialVdom.set(node, vdom);
      await splitTask();
      D(vdom, fragment);
    }
  }
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/index.js
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
      h: _,
      cloneElement: E,
      render: B,
      deepSignal: deepsignal_module_g,
      parseInitialData: parseInitialData,
      populateInitialData: populateInitialData,
      batch: signals_core_module_r
    };
  }
  throw new Error('Forbidden access.');
};
document.addEventListener('DOMContentLoaded', async () => {
  directives();
  await init();
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/debug.js
/**
 * External dependencies
 */



var __webpack_exports__getConfig = __webpack_exports__.zj;
var __webpack_exports__getContext = __webpack_exports__.SD;
var __webpack_exports__getElement = __webpack_exports__.V6;
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
export { __webpack_exports__getConfig as getConfig, __webpack_exports__getContext as getContext, __webpack_exports__getElement as getElement, __webpack_exports__privateApis as privateApis, __webpack_exports__splitTask as splitTask, __webpack_exports__store as store, __webpack_exports__useCallback as useCallback, __webpack_exports__useEffect as useEffect, __webpack_exports__useInit as useInit, __webpack_exports__useLayoutEffect as useLayoutEffect, __webpack_exports__useMemo as useMemo, __webpack_exports__useRef as useRef, __webpack_exports__useState as useState, __webpack_exports__useWatch as useWatch, __webpack_exports__withScope as withScope };
