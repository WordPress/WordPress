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
  jb: () => (/* binding */ privateApis),
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
var n,l,preact_module_u,preact_module_t,i,preact_module_o,r,preact_module_f,preact_module_e,preact_module_c={},s=[],a=/acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i,h=Array.isArray;function v(n,l){for(var u in l)n[u]=l[u];return n}function p(n){var l=n.parentNode;l&&l.removeChild(n)}function y(l,u,t){var i,o,r,f={};for(r in u)"key"==r?i=u[r]:"ref"==r?o=u[r]:f[r]=u[r];if(arguments.length>2&&(f.children=arguments.length>3?n.call(arguments,2):t),"function"==typeof l&&null!=l.defaultProps)for(r in l.defaultProps)void 0===f[r]&&(f[r]=l.defaultProps[r]);return d(l,f,i,o,null)}function d(n,t,i,o,r){var f={type:n,props:t,key:i,ref:o,__k:null,__:null,__b:0,__e:null,__d:void 0,__c:null,constructor:void 0,__v:null==r?++preact_module_u:r,__i:-1,__u:0};return null==r&&null!=l.vnode&&l.vnode(f),f}function _(){return{current:null}}function g(n){return n.children}function b(n,l){this.props=n,this.context=l}function m(n,l){if(null==l)return n.__?m(n.__,n.__i+1):null;for(var u;l<n.__k.length;l++)if(null!=(u=n.__k[l])&&null!=u.__e)return u.__e;return"function"==typeof n.type?m(n):null}function w(n,u,t){var i,o=n.__v,r=o.__e,f=n.__P;if(f)return(i=v({},o)).__v=o.__v+1,l.vnode&&l.vnode(i),M(f,i,o,n.__n,void 0!==f.ownerSVGElement,32&o.__u?[r]:null,u,null==r?m(o):r,!!(32&o.__u),t),i.__v=o.__v,i.__.__k[i.__i]=i,i.__d=void 0,i.__e!=r&&k(i),i}function k(n){var l,u;if(null!=(n=n.__)&&null!=n.__c){for(n.__e=n.__c.base=null,l=0;l<n.__k.length;l++)if(null!=(u=n.__k[l])&&null!=u.__e){n.__e=n.__c.base=u.__e;break}return k(n)}}function x(n){(!n.__d&&(n.__d=!0)&&i.push(n)&&!C.__r++||preact_module_o!==l.debounceRendering)&&((preact_module_o=l.debounceRendering)||r)(C)}function C(){var n,u,t,o=[],r=[];for(i.sort(preact_module_f);n=i.shift();)n.__d&&(t=i.length,u=w(n,o,r)||u,0===t||i.length>t?(j(o,u,r),r.length=o.length=0,u=void 0,i.sort(preact_module_f)):u&&l.__c&&l.__c(u,s));u&&j(o,u,r),C.__r=0}function P(n,l,u,t,i,o,r,f,e,a,h){var v,p,y,d,_,g=t&&t.__k||s,b=l.length;for(u.__d=e,S(u,l,g),e=u.__d,v=0;v<b;v++)null!=(y=u.__k[v])&&"boolean"!=typeof y&&"function"!=typeof y&&(p=-1===y.__i?preact_module_c:g[y.__i]||preact_module_c,y.__i=v,M(n,y,p,i,o,r,f,e,a,h),d=y.__e,y.ref&&p.ref!=y.ref&&(p.ref&&N(p.ref,null,y),h.push(y.ref,y.__c||d,y)),null==_&&null!=d&&(_=d),65536&y.__u||p.__k===y.__k?e=$(y,e,n):"function"==typeof y.type&&void 0!==y.__d?e=y.__d:d&&(e=d.nextSibling),y.__d=void 0,y.__u&=-196609);u.__d=e,u.__e=_}function S(n,l,u){var t,i,o,r,f,e=l.length,c=u.length,s=c,a=0;for(n.__k=[],t=0;t<e;t++)r=t+a,null!=(i=n.__k[t]=null==(i=l[t])||"boolean"==typeof i||"function"==typeof i?null:"string"==typeof i||"number"==typeof i||"bigint"==typeof i||i.constructor==String?d(null,i,null,null,null):h(i)?d(g,{children:i},null,null,null):void 0===i.constructor&&i.__b>0?d(i.type,i.props,i.key,i.ref?i.ref:null,i.__v):i)?(i.__=n,i.__b=n.__b+1,f=I(i,u,r,s),i.__i=f,o=null,-1!==f&&(s--,(o=u[f])&&(o.__u|=131072)),null==o||null===o.__v?(-1==f&&a--,"function"!=typeof i.type&&(i.__u|=65536)):f!==r&&(f===r+1?a++:f>r?s>e-r?a+=f-r:a--:f<r?f==r-1&&(a=f-r):a=0,f!==t+a&&(i.__u|=65536))):(o=u[r])&&null==o.key&&o.__e&&0==(131072&o.__u)&&(o.__e==n.__d&&(n.__d=m(o)),O(o,o,!1),u[r]=null,s--);if(s)for(t=0;t<c;t++)null!=(o=u[t])&&0==(131072&o.__u)&&(o.__e==n.__d&&(n.__d=m(o)),O(o,o))}function $(n,l,u){var t,i;if("function"==typeof n.type){for(t=n.__k,i=0;t&&i<t.length;i++)t[i]&&(t[i].__=n,l=$(t[i],l,u));return l}n.__e!=l&&(u.insertBefore(n.__e,l||null),l=n.__e);do{l=l&&l.nextSibling}while(null!=l&&8===l.nodeType);return l}function H(n,l){return l=l||[],null==n||"boolean"==typeof n||(h(n)?n.some(function(n){H(n,l)}):l.push(n)),l}function I(n,l,u,t){var i=n.key,o=n.type,r=u-1,f=u+1,e=l[u];if(null===e||e&&i==e.key&&o===e.type&&0==(131072&e.__u))return u;if(t>(null!=e&&0==(131072&e.__u)?1:0))for(;r>=0||f<l.length;){if(r>=0){if((e=l[r])&&0==(131072&e.__u)&&i==e.key&&o===e.type)return r;r--}if(f<l.length){if((e=l[f])&&0==(131072&e.__u)&&i==e.key&&o===e.type)return f;f++}}return-1}function T(n,l,u){"-"===l[0]?n.setProperty(l,null==u?"":u):n[l]=null==u?"":"number"!=typeof u||a.test(l)?u:u+"px"}function A(n,l,u,t,i){var o;n:if("style"===l)if("string"==typeof u)n.style.cssText=u;else{if("string"==typeof t&&(n.style.cssText=t=""),t)for(l in t)u&&l in u||T(n.style,l,"");if(u)for(l in u)t&&u[l]===t[l]||T(n.style,l,u[l])}else if("o"===l[0]&&"n"===l[1])o=l!==(l=l.replace(/(PointerCapture)$|Capture$/i,"$1")),l=l.toLowerCase()in n?l.toLowerCase().slice(2):l.slice(2),n.l||(n.l={}),n.l[l+o]=u,u?t?u.u=t.u:(u.u=Date.now(),n.addEventListener(l,o?L:D,o)):n.removeEventListener(l,o?L:D,o);else{if(i)l=l.replace(/xlink(H|:h)/,"h").replace(/sName$/,"s");else if("width"!==l&&"height"!==l&&"href"!==l&&"list"!==l&&"form"!==l&&"tabIndex"!==l&&"download"!==l&&"rowSpan"!==l&&"colSpan"!==l&&"role"!==l&&l in n)try{n[l]=null==u?"":u;break n}catch(n){}"function"==typeof u||(null==u||!1===u&&"-"!==l[4]?n.removeAttribute(l):n.setAttribute(l,u))}}function D(n){if(this.l){var u=this.l[n.type+!1];if(n.t){if(n.t<=u.u)return}else n.t=Date.now();return u(l.event?l.event(n):n)}}function L(n){if(this.l)return this.l[n.type+!0](l.event?l.event(n):n)}function M(n,u,t,i,o,r,f,e,c,s){var a,p,y,d,_,m,w,k,x,C,S,$,H,I,T,A=u.type;if(void 0!==u.constructor)return null;128&t.__u&&(c=!!(32&t.__u),r=[e=u.__e=t.__e]),(a=l.__b)&&a(u);n:if("function"==typeof A)try{if(k=u.props,x=(a=A.contextType)&&i[a.__c],C=a?x?x.props.value:a.__:i,t.__c?w=(p=u.__c=t.__c).__=p.__E:("prototype"in A&&A.prototype.render?u.__c=p=new A(k,C):(u.__c=p=new b(k,C),p.constructor=A,p.render=q),x&&x.sub(p),p.props=k,p.state||(p.state={}),p.context=C,p.__n=i,y=p.__d=!0,p.__h=[],p._sb=[]),null==p.__s&&(p.__s=p.state),null!=A.getDerivedStateFromProps&&(p.__s==p.state&&(p.__s=v({},p.__s)),v(p.__s,A.getDerivedStateFromProps(k,p.__s))),d=p.props,_=p.state,p.__v=u,y)null==A.getDerivedStateFromProps&&null!=p.componentWillMount&&p.componentWillMount(),null!=p.componentDidMount&&p.__h.push(p.componentDidMount);else{if(null==A.getDerivedStateFromProps&&k!==d&&null!=p.componentWillReceiveProps&&p.componentWillReceiveProps(k,C),!p.__e&&(null!=p.shouldComponentUpdate&&!1===p.shouldComponentUpdate(k,p.__s,C)||u.__v===t.__v)){for(u.__v!==t.__v&&(p.props=k,p.state=p.__s,p.__d=!1),u.__e=t.__e,u.__k=t.__k,u.__k.forEach(function(n){n&&(n.__=u)}),S=0;S<p._sb.length;S++)p.__h.push(p._sb[S]);p._sb=[],p.__h.length&&f.push(p);break n}null!=p.componentWillUpdate&&p.componentWillUpdate(k,p.__s,C),null!=p.componentDidUpdate&&p.__h.push(function(){p.componentDidUpdate(d,_,m)})}if(p.context=C,p.props=k,p.__P=n,p.__e=!1,$=l.__r,H=0,"prototype"in A&&A.prototype.render){for(p.state=p.__s,p.__d=!1,$&&$(u),a=p.render(p.props,p.state,p.context),I=0;I<p._sb.length;I++)p.__h.push(p._sb[I]);p._sb=[]}else do{p.__d=!1,$&&$(u),a=p.render(p.props,p.state,p.context),p.state=p.__s}while(p.__d&&++H<25);p.state=p.__s,null!=p.getChildContext&&(i=v(v({},i),p.getChildContext())),y||null==p.getSnapshotBeforeUpdate||(m=p.getSnapshotBeforeUpdate(d,_)),P(n,h(T=null!=a&&a.type===g&&null==a.key?a.props.children:a)?T:[T],u,t,i,o,r,f,e,c,s),p.base=u.__e,u.__u&=-161,p.__h.length&&f.push(p),w&&(p.__E=p.__=null)}catch(n){u.__v=null,c||null!=r?(u.__e=e,u.__u|=c?160:32,r[r.indexOf(e)]=null):(u.__e=t.__e,u.__k=t.__k),l.__e(n,u,t)}else null==r&&u.__v===t.__v?(u.__k=t.__k,u.__e=t.__e):u.__e=z(t.__e,u,t,i,o,r,f,c,s);(a=l.diffed)&&a(u)}function j(n,u,t){for(var i=0;i<t.length;i++)N(t[i],t[++i],t[++i]);l.__c&&l.__c(u,n),n.some(function(u){try{n=u.__h,u.__h=[],n.some(function(n){n.call(u)})}catch(n){l.__e(n,u.__v)}})}function z(l,u,t,i,o,r,f,e,s){var a,v,y,d,_,g,b,w=t.props,k=u.props,x=u.type;if("svg"===x&&(o=!0),null!=r)for(a=0;a<r.length;a++)if((_=r[a])&&"setAttribute"in _==!!x&&(x?_.localName===x:3===_.nodeType)){l=_,r[a]=null;break}if(null==l){if(null===x)return document.createTextNode(k);l=o?document.createElementNS("http://www.w3.org/2000/svg",x):document.createElement(x,k.is&&k),r=null,e=!1}if(null===x)w===k||e&&l.data===k||(l.data=k);else{if(r=r&&n.call(l.childNodes),w=t.props||preact_module_c,!e&&null!=r)for(w={},a=0;a<l.attributes.length;a++)w[(_=l.attributes[a]).name]=_.value;for(a in w)_=w[a],"children"==a||("dangerouslySetInnerHTML"==a?y=_:"key"===a||a in k||A(l,a,null,_,o));for(a in k)_=k[a],"children"==a?d=_:"dangerouslySetInnerHTML"==a?v=_:"value"==a?g=_:"checked"==a?b=_:"key"===a||e&&"function"!=typeof _||w[a]===_||A(l,a,_,w[a],o);if(v)e||y&&(v.__html===y.__html||v.__html===l.innerHTML)||(l.innerHTML=v.__html),u.__k=[];else if(y&&(l.innerHTML=""),P(l,h(d)?d:[d],u,t,i,o&&"foreignObject"!==x,r,f,r?r[0]:t.__k&&m(t,0),e,s),null!=r)for(a=r.length;a--;)null!=r[a]&&p(r[a]);e||(a="value",void 0!==g&&(g!==l[a]||"progress"===x&&!g||"option"===x&&g!==w[a])&&A(l,a,g,w[a],!1),a="checked",void 0!==b&&b!==l[a]&&A(l,a,b,w[a],!1))}return l}function N(n,u,t){try{"function"==typeof n?n(u):n.current=u}catch(n){l.__e(n,t)}}function O(n,u,t){var i,o;if(l.unmount&&l.unmount(n),(i=n.ref)&&(i.current&&i.current!==n.__e||N(i,null,u)),null!=(i=n.__c)){if(i.componentWillUnmount)try{i.componentWillUnmount()}catch(n){l.__e(n,u)}i.base=i.__P=null,n.__c=void 0}if(i=n.__k)for(o=0;o<i.length;o++)i[o]&&O(i[o],u,t||"function"!=typeof n.type);t||null==n.__e||p(n.__e),n.__=n.__e=n.__d=void 0}function q(n,l,u){return this.constructor(n,u)}function B(u,t,i){var o,r,f,e;l.__&&l.__(u,t),r=(o="function"==typeof i)?null:i&&i.__k||t.__k,f=[],e=[],M(t,u=(!o&&i||t).__k=y(g,null,[u]),r||preact_module_c,preact_module_c,void 0!==t.ownerSVGElement,!o&&i?[i]:r?null:t.firstChild?n.call(t.childNodes):null,f,!o&&i?i:r?r.__e:t.firstChild,o,e),u.__d=void 0,j(f,u,e)}function E(n,l){B(n,l,E)}function F(l,u,t){var i,o,r,f,e=v({},l.props);for(r in l.type&&l.type.defaultProps&&(f=l.type.defaultProps),u)"key"==r?i=u[r]:"ref"==r?o=u[r]:e[r]=void 0===u[r]&&void 0!==f?f[r]:u[r];return arguments.length>2&&(e.children=arguments.length>3?n.call(arguments,2):t),d(l.type,e,i||l.key,o||l.ref,null)}function G(n,l){var u={__c:l="__cC"+preact_module_e++,__:n,Consumer:function(n,l){return n.children(l)},Provider:function(n){var u,t;return this.getChildContext||(u=[],(t={})[l]=this,this.getChildContext=function(){return t},this.shouldComponentUpdate=function(n){this.props.value!==n.value&&u.some(function(n){n.__e=!0,x(n)})},this.sub=function(n){u.push(n);var l=n.componentWillUnmount;n.componentWillUnmount=function(){u.splice(u.indexOf(n),1),l&&l.call(n)}}),n.children}};return u.Provider.__=u.Consumer.contextType=u}n=s.slice,l={__e:function(n,l,u,t){for(var i,o,r;l=l.__;)if((i=l.__c)&&!i.__)try{if((o=i.constructor)&&null!=o.getDerivedStateFromError&&(i.setState(o.getDerivedStateFromError(n)),r=i.__d),null!=i.componentDidCatch&&(i.componentDidCatch(n,t||{}),r=i.__d),r)return i.__E=i}catch(l){n=l}throw n}},preact_module_u=0,preact_module_t=function(n){return null!=n&&null==n.constructor},b.prototype.setState=function(n,l){var u;u=null!=this.__s&&this.__s!==this.state?this.__s:this.__s=v({},this.state),"function"==typeof n&&(n=n(v({},u),this.props)),n&&v(u,n),null!=n&&this.__v&&(l&&this._sb.push(l),x(this))},b.prototype.forceUpdate=function(n){this.__v&&(this.__e=!0,n&&this.__h.push(n),x(this))},b.prototype.render=g,i=[],r="function"==typeof Promise?Promise.prototype.then.bind(Promise.resolve()):setTimeout,preact_module_f=function(n,l){return n.__v.__b-l.__v.__b},C.__r=0,preact_module_e=0;

;// CONCATENATED MODULE: ./node_modules/preact/hooks/dist/hooks.module.js
var hooks_module_t,hooks_module_r,hooks_module_u,hooks_module_i,hooks_module_o=0,hooks_module_f=[],hooks_module_c=[],hooks_module_e=l,hooks_module_a=hooks_module_e.__b,hooks_module_v=hooks_module_e.__r,hooks_module_l=hooks_module_e.diffed,hooks_module_m=hooks_module_e.__c,hooks_module_s=hooks_module_e.unmount,hooks_module_d=hooks_module_e.__;function hooks_module_h(n,t){hooks_module_e.__h&&hooks_module_e.__h(hooks_module_r,n,hooks_module_o||t),hooks_module_o=0;var u=hooks_module_r.__H||(hooks_module_r.__H={__:[],__h:[]});return n>=u.__.length&&u.__.push({__V:hooks_module_c}),u.__[n]}function hooks_module_p(n){return hooks_module_o=1,hooks_module_y(hooks_module_D,n)}function hooks_module_y(n,u,i){var o=hooks_module_h(hooks_module_t++,2);if(o.t=n,!o.__c&&(o.__=[i?i(u):hooks_module_D(void 0,u),function(n){var t=o.__N?o.__N[0]:o.__[0],r=o.t(t,n);t!==r&&(o.__N=[r,o.__[1]],o.__c.setState({}))}],o.__c=hooks_module_r,!hooks_module_r.u)){var f=function(n,t,r){if(!o.__c.__H)return!0;var u=o.__c.__H.__.filter(function(n){return!!n.__c});if(u.every(function(n){return!n.__N}))return!c||c.call(this,n,t,r);var i=!1;return u.forEach(function(n){if(n.__N){var t=n.__[0];n.__=n.__N,n.__N=void 0,t!==n.__[0]&&(i=!0)}}),!(!i&&o.__c.props===n)&&(!c||c.call(this,n,t,r))};hooks_module_r.u=!0;var c=hooks_module_r.shouldComponentUpdate,e=hooks_module_r.componentWillUpdate;hooks_module_r.componentWillUpdate=function(n,t,r){if(this.__e){var u=c;c=void 0,f(n,t,r),c=u}e&&e.call(this,n,t,r)},hooks_module_r.shouldComponentUpdate=f}return o.__N||o.__}function hooks_module_(n,u){var i=hooks_module_h(hooks_module_t++,3);!hooks_module_e.__s&&hooks_module_C(i.__H,u)&&(i.__=n,i.i=u,hooks_module_r.__H.__h.push(i))}function hooks_module_A(n,u){var i=hooks_module_h(hooks_module_t++,4);!hooks_module_e.__s&&hooks_module_C(i.__H,u)&&(i.__=n,i.i=u,hooks_module_r.__h.push(i))}function hooks_module_F(n){return hooks_module_o=5,hooks_module_q(function(){return{current:n}},[])}function hooks_module_T(n,t,r){hooks_module_o=6,hooks_module_A(function(){return"function"==typeof n?(n(t()),function(){return n(null)}):n?(n.current=t(),function(){return n.current=null}):void 0},null==r?r:r.concat(n))}function hooks_module_q(n,r){var u=hooks_module_h(hooks_module_t++,7);return hooks_module_C(u.__H,r)?(u.__V=n(),u.i=r,u.__h=n,u.__V):u.__}function hooks_module_x(n,t){return hooks_module_o=8,hooks_module_q(function(){return n},t)}function hooks_module_P(n){var u=hooks_module_r.context[n.__c],i=hooks_module_h(hooks_module_t++,9);return i.c=n,u?(null==i.__&&(i.__=!0,u.sub(hooks_module_r)),u.props.value):n.__}function V(n,t){hooks_module_e.useDebugValue&&hooks_module_e.useDebugValue(t?t(n):n)}function hooks_module_b(n){var u=hooks_module_h(hooks_module_t++,10),i=hooks_module_p();return u.__=n,hooks_module_r.componentDidCatch||(hooks_module_r.componentDidCatch=function(n,t){u.__&&u.__(n,t),i[1](n)}),[i[0],function(){i[1](void 0)}]}function hooks_module_g(){var n=hooks_module_h(hooks_module_t++,11);if(!n.__){for(var u=hooks_module_r.__v;null!==u&&!u.__m&&null!==u.__;)u=u.__;var i=u.__m||(u.__m=[0,0]);n.__="P"+i[0]+"-"+i[1]++}return n.__}function hooks_module_j(){for(var n;n=hooks_module_f.shift();)if(n.__P&&n.__H)try{n.__H.__h.forEach(hooks_module_z),n.__H.__h.forEach(hooks_module_B),n.__H.__h=[]}catch(t){n.__H.__h=[],hooks_module_e.__e(t,n.__v)}}hooks_module_e.__b=function(n){hooks_module_r=null,hooks_module_a&&hooks_module_a(n)},hooks_module_e.__=function(n,t){n&&t.__k&&t.__k.__m&&(n.__m=t.__k.__m),hooks_module_d&&hooks_module_d(n,t)},hooks_module_e.__r=function(n){hooks_module_v&&hooks_module_v(n),hooks_module_t=0;var i=(hooks_module_r=n.__c).__H;i&&(hooks_module_u===hooks_module_r?(i.__h=[],hooks_module_r.__h=[],i.__.forEach(function(n){n.__N&&(n.__=n.__N),n.__V=hooks_module_c,n.__N=n.i=void 0})):(i.__h.forEach(hooks_module_z),i.__h.forEach(hooks_module_B),i.__h=[],hooks_module_t=0)),hooks_module_u=hooks_module_r},hooks_module_e.diffed=function(n){hooks_module_l&&hooks_module_l(n);var t=n.__c;t&&t.__H&&(t.__H.__h.length&&(1!==hooks_module_f.push(t)&&hooks_module_i===hooks_module_e.requestAnimationFrame||((hooks_module_i=hooks_module_e.requestAnimationFrame)||hooks_module_w)(hooks_module_j)),t.__H.__.forEach(function(n){n.i&&(n.__H=n.i),n.__V!==hooks_module_c&&(n.__=n.__V),n.i=void 0,n.__V=hooks_module_c})),hooks_module_u=hooks_module_r=null},hooks_module_e.__c=function(n,t){t.some(function(n){try{n.__h.forEach(hooks_module_z),n.__h=n.__h.filter(function(n){return!n.__||hooks_module_B(n)})}catch(r){t.some(function(n){n.__h&&(n.__h=[])}),t=[],hooks_module_e.__e(r,n.__v)}}),hooks_module_m&&hooks_module_m(n,t)},hooks_module_e.unmount=function(n){hooks_module_s&&hooks_module_s(n);var t,r=n.__c;r&&r.__H&&(r.__H.__.forEach(function(n){try{hooks_module_z(n)}catch(n){t=n}}),r.__H=void 0,t&&hooks_module_e.__e(t,r.__v))};var hooks_module_k="function"==typeof requestAnimationFrame;function hooks_module_w(n){var t,r=function(){clearTimeout(u),hooks_module_k&&cancelAnimationFrame(t),setTimeout(n)},u=setTimeout(r,100);hooks_module_k&&(t=requestAnimationFrame(r))}function hooks_module_z(n){var t=hooks_module_r,u=n.__c;"function"==typeof u&&(n.__c=void 0,u()),hooks_module_r=t}function hooks_module_B(n){var t=hooks_module_r;n.__c=n.__(),hooks_module_r=t}function hooks_module_C(n,t){return!n||n.length!==t.length||t.some(function(t,r){return t!==n[r]})}function hooks_module_D(n,t){return"function"==typeof t?t(n):t}

;// CONCATENATED MODULE: ./node_modules/@preact/signals-core/dist/signals-core.module.js
function signals_core_module_i(){throw new Error("Cycle detected")}var signals_core_module_t=Symbol.for("preact-signals");function signals_core_module_r(){if(!(signals_core_module_v>1)){var i,t=!1;while(void 0!==signals_core_module_f){var r=signals_core_module_f;signals_core_module_f=void 0;signals_core_module_e++;while(void 0!==r){var n=r.o;r.o=void 0;r.f&=-3;if(!(8&r.f)&&signals_core_module_l(r))try{r.c()}catch(r){if(!t){i=r;t=!0}}r=n}}signals_core_module_e=0;signals_core_module_v--;if(t)throw i}else signals_core_module_v--}function signals_core_module_n(i){if(signals_core_module_v>0)return i();signals_core_module_v++;try{return i()}finally{signals_core_module_r()}}var signals_core_module_o=void 0,signals_core_module_h=0;function signals_core_module_s(i){if(signals_core_module_h>0)return i();var t=signals_core_module_o;signals_core_module_o=void 0;signals_core_module_h++;try{return i()}finally{signals_core_module_h--;signals_core_module_o=t}}var signals_core_module_f=void 0,signals_core_module_v=0,signals_core_module_e=0,signals_core_module_u=0;function signals_core_module_c(i){if(void 0!==signals_core_module_o){var t=i.n;if(void 0===t||t.t!==signals_core_module_o){t={i:0,S:i,p:signals_core_module_o.s,n:void 0,t:signals_core_module_o,e:void 0,x:void 0,r:t};if(void 0!==signals_core_module_o.s)signals_core_module_o.s.n=t;signals_core_module_o.s=t;i.n=t;if(32&signals_core_module_o.f)i.S(t);return t}else if(-1===t.i){t.i=0;if(void 0!==t.n){t.n.p=t.p;if(void 0!==t.p)t.p.n=t.n;t.p=signals_core_module_o.s;t.n=void 0;signals_core_module_o.s.n=t;signals_core_module_o.s=t}return t}}}function signals_core_module_d(i){this.v=i;this.i=0;this.n=void 0;this.t=void 0}signals_core_module_d.prototype.brand=signals_core_module_t;signals_core_module_d.prototype.h=function(){return!0};signals_core_module_d.prototype.S=function(i){if(this.t!==i&&void 0===i.e){i.x=this.t;if(void 0!==this.t)this.t.e=i;this.t=i}};signals_core_module_d.prototype.U=function(i){if(void 0!==this.t){var t=i.e,r=i.x;if(void 0!==t){t.x=r;i.e=void 0}if(void 0!==r){r.e=t;i.x=void 0}if(i===this.t)this.t=r}};signals_core_module_d.prototype.subscribe=function(i){var t=this;return signals_core_module_O(function(){var r=t.value,n=32&this.f;this.f&=-33;try{i(r)}finally{this.f|=n}})};signals_core_module_d.prototype.valueOf=function(){return this.value};signals_core_module_d.prototype.toString=function(){return this.value+""};signals_core_module_d.prototype.toJSON=function(){return this.value};signals_core_module_d.prototype.peek=function(){return this.v};Object.defineProperty(signals_core_module_d.prototype,"value",{get:function(){var i=signals_core_module_c(this);if(void 0!==i)i.i=this.i;return this.v},set:function(t){if(signals_core_module_o instanceof signals_core_module_)!function(){throw new Error("Computed cannot have side-effects")}();if(t!==this.v){if(signals_core_module_e>100)signals_core_module_i();this.v=t;this.i++;signals_core_module_u++;signals_core_module_v++;try{for(var n=this.t;void 0!==n;n=n.x)n.t.N()}finally{signals_core_module_r()}}}});function signals_core_module_a(i){return new signals_core_module_d(i)}function signals_core_module_l(i){for(var t=i.s;void 0!==t;t=t.n)if(t.S.i!==t.i||!t.S.h()||t.S.i!==t.i)return!0;return!1}function signals_core_module_y(i){for(var t=i.s;void 0!==t;t=t.n){var r=t.S.n;if(void 0!==r)t.r=r;t.S.n=t;t.i=-1;if(void 0===t.n){i.s=t;break}}}function signals_core_module_w(i){var t=i.s,r=void 0;while(void 0!==t){var n=t.p;if(-1===t.i){t.S.U(t);if(void 0!==n)n.n=t.n;if(void 0!==t.n)t.n.p=n}else r=t;t.S.n=t.r;if(void 0!==t.r)t.r=void 0;t=n}i.s=r}function signals_core_module_(i){signals_core_module_d.call(this,void 0);this.x=i;this.s=void 0;this.g=signals_core_module_u-1;this.f=4}(signals_core_module_.prototype=new signals_core_module_d).h=function(){this.f&=-3;if(1&this.f)return!1;if(32==(36&this.f))return!0;this.f&=-5;if(this.g===signals_core_module_u)return!0;this.g=signals_core_module_u;this.f|=1;if(this.i>0&&!signals_core_module_l(this)){this.f&=-2;return!0}var i=signals_core_module_o;try{signals_core_module_y(this);signals_core_module_o=this;var t=this.x();if(16&this.f||this.v!==t||0===this.i){this.v=t;this.f&=-17;this.i++}}catch(i){this.v=i;this.f|=16;this.i++}signals_core_module_o=i;signals_core_module_w(this);this.f&=-2;return!0};signals_core_module_.prototype.S=function(i){if(void 0===this.t){this.f|=36;for(var t=this.s;void 0!==t;t=t.n)t.S.S(t)}signals_core_module_d.prototype.S.call(this,i)};signals_core_module_.prototype.U=function(i){if(void 0!==this.t){signals_core_module_d.prototype.U.call(this,i);if(void 0===this.t){this.f&=-33;for(var t=this.s;void 0!==t;t=t.n)t.S.U(t)}}};signals_core_module_.prototype.N=function(){if(!(2&this.f)){this.f|=6;for(var i=this.t;void 0!==i;i=i.x)i.t.N()}};signals_core_module_.prototype.peek=function(){if(!this.h())signals_core_module_i();if(16&this.f)throw this.v;return this.v};Object.defineProperty(signals_core_module_.prototype,"value",{get:function(){if(1&this.f)signals_core_module_i();var t=signals_core_module_c(this);this.h();if(void 0!==t)t.i=this.i;if(16&this.f)throw this.v;return this.v}});function signals_core_module_p(i){return new signals_core_module_(i)}function signals_core_module_g(i){var t=i.u;i.u=void 0;if("function"==typeof t){signals_core_module_v++;var n=signals_core_module_o;signals_core_module_o=void 0;try{t()}catch(t){i.f&=-2;i.f|=8;signals_core_module_b(i);throw t}finally{signals_core_module_o=n;signals_core_module_r()}}}function signals_core_module_b(i){for(var t=i.s;void 0!==t;t=t.n)t.S.U(t);i.x=void 0;i.s=void 0;signals_core_module_g(i)}function signals_core_module_x(i){if(signals_core_module_o!==this)throw new Error("Out-of-order effect");signals_core_module_w(this);signals_core_module_o=i;this.f&=-2;if(8&this.f)signals_core_module_b(this);signals_core_module_r()}function signals_core_module_E(i){this.x=i;this.u=void 0;this.s=void 0;this.o=void 0;this.f=32}signals_core_module_E.prototype.c=function(){var i=this.S();try{if(8&this.f)return;if(void 0===this.x)return;var t=this.x();if("function"==typeof t)this.u=t}finally{i()}};signals_core_module_E.prototype.S=function(){if(1&this.f)signals_core_module_i();this.f|=1;this.f&=-9;signals_core_module_g(this);signals_core_module_y(this);signals_core_module_v++;var t=signals_core_module_o;signals_core_module_o=this;return signals_core_module_x.bind(this,t)};signals_core_module_E.prototype.N=function(){if(!(2&this.f)){this.f|=2;this.o=signals_core_module_f;signals_core_module_f=this}};signals_core_module_E.prototype.d=function(){this.f|=8;if(!(1&this.f))signals_core_module_b(this)};function signals_core_module_O(i){var t=new signals_core_module_E(i);try{t.c()}catch(i){t.d();throw i}return t.d.bind(t)}
;// CONCATENATED MODULE: ./node_modules/@preact/signals/dist/signals.module.js
var signals_module_v,signals_module_s;function signals_module_l(n,i){l[n]=i.bind(null,l[n]||function(){})}function signals_module_d(n){if(signals_module_s)signals_module_s();signals_module_s=n&&n.S()}function signals_module_p(n){var r=this,f=n.data,o=useSignal(f);o.value=f;var e=hooks_module_q(function(){var n=r.__v;while(n=n.__)if(n.__c){n.__c.__$f|=4;break}r.__$u.c=function(){var n;if(!preact_module_t(e.peek())&&3===(null==(n=r.base)?void 0:n.nodeType))r.base.data=e.peek();else{r.__$f|=1;r.setState({})}};return signals_core_module_p(function(){var n=o.value.value;return 0===n?0:!0===n?"":n||""})},[]);return e.value}signals_module_p.displayName="_st";Object.defineProperties(signals_core_module_d.prototype,{constructor:{configurable:!0,value:void 0},type:{configurable:!0,value:signals_module_p},props:{configurable:!0,get:function(){return{data:this}}},__b:{configurable:!0,value:1}});signals_module_l("__b",function(n,r){if("string"==typeof r.type){var i,t=r.props;for(var f in t)if("children"!==f){var o=t[f];if(o instanceof signals_core_module_d){if(!i)r.__np=i={};i[f]=o;t[f]=o.peek()}}}n(r)});signals_module_l("__r",function(n,r){signals_module_d();var i,t=r.__c;if(t){t.__$f&=-2;if(void 0===(i=t.__$u))t.__$u=i=function(n){var r;signals_core_module_O(function(){r=this});r.c=function(){t.__$f|=1;t.setState({})};return r}()}signals_module_v=t;signals_module_d(i);n(r)});signals_module_l("__e",function(n,r,i,t){signals_module_d();signals_module_v=void 0;n(r,i,t)});signals_module_l("diffed",function(n,r){signals_module_d();signals_module_v=void 0;var i;if("string"==typeof r.type&&(i=r.__e)){var t=r.__np,f=r.props;if(t){var o=i.U;if(o)for(var e in o){var u=o[e];if(void 0!==u&&!(e in t)){u.d();o[e]=void 0}}else i.U=o={};for(var a in t){var c=o[a],s=t[a];if(void 0===c){c=signals_module_(i,a,s,f);o[a]=c}else c.o(s,f)}}}n(r)});function signals_module_(n,r,i,t){var f=r in n&&void 0===n.ownerSVGElement,o=signals_core_module_a(i);return{o:function(n,r){o.value=n;t=r},d:signals_core_module_O(function(){var i=o.value.value;if(t[r]!==i){t[r]=i;if(f)n[r]=i;else if(i)n.setAttribute(r,i);else n.removeAttribute(r)}})}}signals_module_l("unmount",function(n,r){if("string"==typeof r.type){var i=r.__e;if(i){var t=i.U;if(t){i.U=void 0;for(var f in t){var o=t[f];if(o)o.d()}}}}else{var e=r.__c;if(e){var u=e.__$u;if(u){e.__$u=void 0;u.d()}}}n(r)});signals_module_l("__h",function(n,r,i,t){if(t<3||9===t)r.__$f|=2;n(r,i,t)});b.prototype.shouldComponentUpdate=function(n,r){var i=this.__$u;if(!(i&&void 0!==i.s||4&this.__$f))return!0;if(3&this.__$f)return!0;for(var t in r)return!0;for(var f in n)if("__source"!==f&&n[f]!==this.props[f])return!0;for(var o in this.props)if(!(o in n))return!0;return!1};function useSignal(n){return hooks_module_q(function(){return signals_core_module_a(n)},[])}function useComputed(n){var r=f(n);r.current=n;signals_module_v.__$f|=4;return t(function(){return u(function(){return r.current()})},[])}function useSignalEffect(n){var r=f(n);r.current=n;o(function(){return c(function(){return r.current()})},[])}
;// CONCATENATED MODULE: ./node_modules/deepsignal/dist/deepsignal.module.js
var deepsignal_module_a=new WeakMap,deepsignal_module_o=new WeakMap,deepsignal_module_s=new WeakMap,deepsignal_module_u=new WeakSet,deepsignal_module_c=new WeakMap,deepsignal_module_f=/^\$/,deepsignal_module_i=Object.getOwnPropertyDescriptor,deepsignal_module_l=!1,deepsignal_module_g=function(e){if(!deepsignal_module_k(e))throw new Error("This object can't be observed.");return deepsignal_module_o.has(e)||deepsignal_module_o.set(e,deepsignal_module_v(e,deepsignal_module_d)),deepsignal_module_o.get(e)},deepsignal_module_p=function(e,t){deepsignal_module_l=!0;var r=e[t];try{deepsignal_module_l=!1}catch(e){}return r};function deepsignal_module_h(e){return deepsignal_module_u.add(e),e}var deepsignal_module_v=function(e,t){var r=new Proxy(e,t);return deepsignal_module_u.add(r),r},deepsignal_module_y=function(){throw new Error("Don't mutate the signals directly.")},deepsignal_module_w=function(e){return function(t,u,c){var g;if(deepsignal_module_l)return Reflect.get(t,u,c);var p=e||"$"===u[0];if(!e&&p&&Array.isArray(t)){if("$"===u)return deepsignal_module_s.has(t)||deepsignal_module_s.set(t,deepsignal_module_v(t,deepsignal_module_m)),deepsignal_module_s.get(t);p="$length"===u}deepsignal_module_a.has(c)||deepsignal_module_a.set(c,new Map);var h=deepsignal_module_a.get(c),y=p?u.replace(deepsignal_module_f,""):u;if(h.has(y)||"function"!=typeof(null==(g=deepsignal_module_i(t,y))?void 0:g.get)){var w=Reflect.get(t,y,c);if(p&&"function"==typeof w)return;if("symbol"==typeof y&&deepsignal_module_b.has(y))return w;h.has(y)||(deepsignal_module_k(w)&&(deepsignal_module_o.has(w)||deepsignal_module_o.set(w,deepsignal_module_v(w,deepsignal_module_d)),w=deepsignal_module_o.get(w)),h.set(y,signals_core_module_a(w)))}else h.set(y,signals_core_module_p(function(){return Reflect.get(t,y,c)}));return p?h.get(y):h.get(y).value}},deepsignal_module_d={get:deepsignal_module_w(!1),set:function(e,n,s,u){var l;if("function"==typeof(null==(l=deepsignal_module_i(e,n))?void 0:l.set))return Reflect.set(e,n,s,u);deepsignal_module_a.has(u)||deepsignal_module_a.set(u,new Map);var g=deepsignal_module_a.get(u);if("$"===n[0]){s instanceof signals_core_module_d||deepsignal_module_y();var p=n.replace(deepsignal_module_f,"");return g.set(p,s),Reflect.set(e,p,s.peek(),u)}var h=s;deepsignal_module_k(s)&&(deepsignal_module_o.has(s)||deepsignal_module_o.set(s,deepsignal_module_v(s,deepsignal_module_d)),h=deepsignal_module_o.get(s));var w=!(n in e),m=Reflect.set(e,n,s,u);return g.has(n)?g.get(n).value=h:g.set(n,signals_core_module_a(h)),w&&deepsignal_module_c.has(e)&&deepsignal_module_c.get(e).value++,Array.isArray(e)&&g.has("length")&&(g.get("length").value=e.length),m},deleteProperty:function(e,t){"$"===t[0]&&deepsignal_module_y();var r=deepsignal_module_a.get(deepsignal_module_o.get(e)),n=Reflect.deleteProperty(e,t);return r&&r.has(t)&&(r.get(t).value=void 0),deepsignal_module_c.has(e)&&deepsignal_module_c.get(e).value++,n},ownKeys:function(e){return deepsignal_module_c.has(e)||deepsignal_module_c.set(e,signals_core_module_a(0)),deepsignal_module_c._=deepsignal_module_c.get(e).value,Reflect.ownKeys(e)}},deepsignal_module_m={get:deepsignal_module_w(!0),set:deepsignal_module_y,deleteProperty:deepsignal_module_y},deepsignal_module_b=new Set(Object.getOwnPropertyNames(Symbol).map(function(e){return Symbol[e]}).filter(function(e){return"symbol"==typeof e})),R=new Set([Object,Array]),deepsignal_module_k=function(e){return"object"==typeof e&&null!==e&&R.has(e.constructor)&&!deepsignal_module_u.has(e)},deepsignal_module_M=function(t){return e(function(){return deepsignal_module_g(t)},[])};
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/store.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */

const isObject = item => item && typeof item === 'object' && item.constructor === Object;
const deepMerge = (target, source) => {
  if (isObject(target) && isObject(source)) {
    for (const key in source) {
      const getter = Object.getOwnPropertyDescriptor(source, key)?.get;
      if (typeof getter === 'function') {
        Object.defineProperty(target, key, {
          get: getter
        });
      } else if (isObject(source[key])) {
        if (!target[key]) target[key] = {};
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
          getters.set(getter, signals_core_module_p(() => {
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
          if (it.done) break;
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
    if (isObject(result)) return proxify(result, ns);
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
 * <div data-wp-interactive='{ "namespace": "counter" }'>
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
  const storeTag = dom.querySelector(`script[type="application/json"]#wp-interactivity-data`);
  if (storeTag?.textContent) {
    try {
      return JSON.parse(storeTag.textContent);
    } catch (e) {
      // Do nothing.
    }
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
/* @jsx createElement */

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
  if (!immutableMap.has(target)) immutableMap.set(target, new Proxy(target, immutableHandlers));
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
 * <div data-wp-interactive='{ "namespace": "messages" }'>
 *   <button data-wp-alert="state.alert">Click me!</button>
 * </div>
 * ```
 * Note that, in the previous example, the directive callback gets the path
 * value (`state.alert`) from the directive entry with suffix `default`. A
 * custom suffix can also be specified by appending `--` to the directive
 * attribute, followed by the suffix, like in the following HTML snippet:
 *
 * ```html
 * <div data-wp-interactive='{ "namespace": "myblock" }'>
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
  let resolvedStore = stores.get(namespace);
  if (typeof resolvedStore === 'undefined') {
    resolvedStore = store(namespace, undefined, {
      lock: universalUnlock
    });
  }
  let current = {
    ...resolvedStore,
    context: getScope().context[namespace]
  };
  path.split('.').forEach(p => current = current[p]);
  return current;
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
  element = F(element, {
    ref: scope.ref
  });
  scope.attributes = element.props;

  // Recursively render the wrapper for the next priority level.
  const children = nextPriorityLevels.length > 0 ? y(Directives, {
    directives: directives,
    priorityLevels: nextPriorityLevels,
    element: element,
    originalProps: originalProps,
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
    if (wrapper !== undefined) props.children = wrapper;
  }
  resetScope();
  return props.children;
};

// Preact Options Hook called each time a vnode is created.
const old = l.vnode;
l.vnode = vnode => {
  if (vnode.props.__directives) {
    const props = vnode.props;
    const directives = props.__directives;
    if (directives.key) vnode.key = directives.key.find(({
      suffix
    }) => suffix === 'default').value;
    delete props.__directives;
    const priorityLevels = getPriorityLevels(directives);
    if (priorityLevels.length > 0) {
      vnode.props = {
        directives,
        priorityLevels,
        originalProps: props,
        type: vnode.type,
        element: y(vnode.type, props),
        top: true
      };
      vnode.type = Directives;
    }
  }
  if (old) old(vnode);
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/utils.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
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

// Using the mangled properties:
// this.c: this._callback
// this.x: this._compute
// https://github.com/preactjs/signals/blob/main/mangle.json
function createFlusher(compute, notify) {
  let flush;
  const dispose = signals_core_module_O(function () {
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

// Version of `useSignalEffect` with a `useEffect`-like execution. This hook
// implementation comes from this PR, but we added short-cirtuiting to avoid
// infinite loops: https://github.com/preactjs/signals/pull/290
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
 * @param {Function} func The passed function.
 * @return {Function} The wrapped function.
 */
const withScope = func => {
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
        if (it.done) break;
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
};

/**
 * Accepts a function that contains imperative code which runs whenever any of
 * the accessed _reactive_ properties (e.g., values from the global state or the
 * context) is modified.
 *
 * This hook makes the element's scope available so functions like
 * `getElement()` and `getContext()` can be used inside the passed callback.
 *
 * @param {Function} callback The hook callback.
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
 * @param {Function} callback The hook callback.
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
 * @param {Function} callback Imperative function that can return a cleanup
 *                            function.
 * @param {any[]}    inputs   If present, effect will only activate if the
 *                            values in the list change (using `===`).
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
 * @param {Function} callback Imperative function that can return a cleanup
 *                            function.
 * @param {any[]}    inputs   If present, effect will only activate if the
 *                            values in the list change (using `===`).
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
 * @param {Function} callback Imperative function that can return a cleanup
 *                            function.
 * @param {any[]}    inputs   If present, effect will only activate if the
 *                            values in the list change (using `===`).
 */
function useCallback(callback, inputs) {
  hooks_module_x(withScope(callback), inputs);
}

/**
 * Pass a factory function and an array of inputs. `useMemo` will only recompute
 * the memoized value when one of the inputs has changed.
 *
 * This hook is equivalent to Preact's `useMemo` and makes the element's scope
 * available so functions like `getElement()` and `getContext()` can be used
 * inside the passed factory function.
 *
 * @param {Function} factory Imperative function that can return a cleanup
 *                           function.
 * @param {any[]}    inputs  If present, effect will only activate if the
 *                           values in the list change (using `===`).
 */
function useMemo(factory, inputs) {
  hooks_module_q(withScope(factory), inputs);
}

// For wrapperless hydration.
// See https://gist.github.com/developit/f4c67a2ede71dc2fab7f357f39cff28c
const createRootFragment = (parent, replaceNode) => {
  replaceNode = [].concat(replaceNode);
  const s = replaceNode[replaceNode.length - 1].nextSibling;
  function insert(c, r) {
    parent.insertBefore(c, r || s);
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

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/utils/kebab-to-camelcase.js
/**
 * Transforms a kebab-case string to camelCase.
 *
 * @param {string} str The kebab-case string to transform to camelCase.
 * @return {string} The transformed camelCase string.
 */
function kebabToCamelCase(str) {
  return str.replace(/^-+|-+$/g, '').toLowerCase().replace(/-([a-z])/g, function (match, group1) {
    return group1.toUpperCase();
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/build-module/directives.js
/* @jsx createElement */

/**
 * External dependencies
 */




/**
 * Internal dependencies
 */




// Assigned objects should be ignore during proxification.
const contextAssignedObjects = new WeakMap();

// Store the context proxy and fallback for each object in the context.
const contextObjectToProxy = new WeakMap();
const contextProxyToObject = new WeakMap();
const contextObjectToFallback = new WeakMap();
const isPlainObject = item => item && typeof item === 'object' && item.constructor === Object;
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
 * @param {Object} current   Current context.
 * @param {Object} inherited Inherited context, used as fallback.
 *
 * @return {Object} The wrapped context object.
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
 * @param {Object} target A deepSignal instance.
 * @param {Object} source Object with properties to update in `target`
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
 * @param {Object} source Source object.
 * @return {Object} Cloned object.
 */
const deepClone = source => {
  if (isPlainObject(source)) {
    return Object.fromEntries(Object.entries(source).map(([key, value]) => [key, deepClone(value)]));
  }
  if (Array.isArray(source)) {
    return source.map(i => deepClone(i));
  }
  return source;
};
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
 * @param {string} val CSS string.
 * @return {Object} CSS object.
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
 * @param {string} type 'window' or 'document'
 * @return {void}
 */
const getGlobalEventDirective = type => ({
  directives,
  evaluate
}) => {
  directives[`on-${type}`].filter(({
    suffix
  }) => suffix !== 'default').forEach(entry => {
    useInit(() => {
      const cb = event => evaluate(entry, event);
      const globalVar = type === 'window' ? window : document;
      globalVar.addEventListener(entry.suffix, cb);
      return () => globalVar.removeEventListener(entry.suffix, cb);
    }, []);
  });
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
        updateSignals(currentValue.current, {
          [namespace]: deepClone(value)
        });
      }
      return proxifyContext(currentValue.current, inheritedValue);
    }, [defaultEntry, inheritedValue]);
    return y(Provider, {
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
    on.filter(({
      suffix
    }) => suffix !== 'default').forEach(entry => {
      element.props[`on${entry.suffix}`] = event => {
        evaluate(entry, event);
      };
    });
  });

  // data-wp-on-window--[event]
  directive('on-window', getGlobalEventDirective('window'));
  // data-wp-on-document--[event]
  directive('on-document', getGlobalEventDirective('document'));

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
      if (!result) element.props.class = currentClass.replace(classFinder, ' ').trim();else if (!classFinder.test(currentClass)) element.props.class = currentClass ? `${currentClass} ${className}` : className;
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
      if (typeof element.props.style === 'string') element.props.style = cssStringToObject(element.props.style);
      if (!result) delete element.props.style[styleProp];else element.props.style[styleProp] = result;
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
          if (typeof result === 'string') el.style.cssText = result;
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
    return y(Type, {
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
    if (element.type !== 'template') return;
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
      return y(Provider, {
        value: mergedContext,
        key: key
      }, element.props.content);
    });
  }, {
    priority: 20
  });
  directive('each-child', () => null);
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
const nsPathRegExp = /^([\w-_\/]+)::(.+)$/;
const hydratedIslands = new WeakSet();

/**
 * Recursive function that transforms a DOM tree into vDOM.
 *
 * @param {Node} root The root element or node to start traversing on.
 * @return {import('preact').VNode[]} The resulting vDOM tree.
 */
function toVdom(root) {
  const treeWalker = document.createTreeWalker(root, 205 // ELEMENT + TEXT + COMMENT + CDATA_SECTION + PROCESSING_INSTRUCTION
  );
  function walk(node) {
    const {
      attributes,
      nodeType,
      localName
    } = node;
    if (nodeType === 3) return [node.data];
    if (nodeType === 4) {
      const next = treeWalker.nextSibling();
      node.replaceWith(new window.Text(node.nodeValue));
      return [node.nodeValue, next];
    }
    if (nodeType === 8 || nodeType === 7) {
      const next = treeWalker.nextSibling();
      node.remove();
      return [null, next];
    }
    const props = {};
    const children = [];
    const directives = [];
    let ignore = false;
    let island = false;
    for (let i = 0; i < attributes.length; i++) {
      const n = attributes[i].name;
      if (n[fullPrefix.length] && n.slice(0, fullPrefix.length) === fullPrefix) {
        if (n === ignoreAttr) {
          ignore = true;
        } else {
          var _nsPathRegExp$exec$sl;
          let [ns, value] = (_nsPathRegExp$exec$sl = nsPathRegExp.exec(attributes[i].value)?.slice(1)) !== null && _nsPathRegExp$exec$sl !== void 0 ? _nsPathRegExp$exec$sl : [null, attributes[i].value];
          try {
            value = JSON.parse(value);
          } catch (e) {}
          if (n === islandAttr) {
            var _value$namespace;
            island = true;
            namespaces.push(typeof value === 'string' ? value : (_value$namespace = value?.namespace) !== null && _value$namespace !== void 0 ? _value$namespace : null);
          } else {
            directives.push([n, ns, value]);
          }
        }
      } else if (n === 'ref') {
        continue;
      }
      props[n] = attributes[i].value;
    }
    if (ignore && !island) return [y(localName, {
      ...props,
      innerHTML: node.innerHTML,
      __directives: {
        ignore: true
      }
    })];
    if (island) hydratedIslands.add(node);
    if (directives.length) {
      props.__directives = directives.reduce((obj, [name, ns, value]) => {
        const [, prefix, suffix = 'default'] = directiveParser.exec(name);
        if (!obj[prefix]) obj[prefix] = [];
        obj[prefix].push({
          namespace: ns !== null && ns !== void 0 ? ns : currentNamespace(),
          value,
          suffix
        });
        return obj;
      }, {});
    }
    if (localName === 'template') {
      props.content = [...node.content.childNodes].map(childNode => toVdom(childNode));
    } else {
      let child = treeWalker.firstChild();
      if (child) {
        while (child) {
          const [vnode, nextChild] = walk(child);
          if (vnode) children.push(vnode);
          child = nextChild || treeWalker.nextSibling();
        }
        treeWalker.parentNode();
      }
    }

    // Restore previous namespace.
    if (island) namespaces.pop();
    return [y(localName, props, children)];
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
  if (!regionRootFragments.has(region)) {
    regionRootFragments.set(region, createRootFragment(region.parentElement, region));
  }
  return regionRootFragments.get(region);
};
function yieldToMain() {
  return new Promise(resolve => {
    // TODO: Use scheduler.yield() when available.
    setTimeout(resolve, 0);
  });
}

// Initial vDOM regions associated with its DOM element.
const initialVdom = new WeakMap();

// Initialize the router with the initial DOM.
const init = async () => {
  const nodes = document.querySelectorAll(`[data-${directivePrefix}-interactive]`);
  for (const node of nodes) {
    if (!hydratedIslands.has(node)) {
      await yieldToMain();
      const fragment = getRegionRootFragment(node);
      const vdom = toVdom(node);
      initialVdom.set(node, vdom);
      await yieldToMain();
      E(vdom, fragment);
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
      h: y,
      cloneElement: F,
      render: B,
      deepSignal: deepsignal_module_g,
      parseInitialData: parseInitialData,
      populateInitialData: populateInitialData,
      batch: signals_core_module_n
    };
  }
  throw new Error('Forbidden access.');
};
document.addEventListener('DOMContentLoaded', async () => {
  directives();
  await init();
});

var __webpack_exports__getConfig = __webpack_exports__.zj;
var __webpack_exports__getContext = __webpack_exports__.SD;
var __webpack_exports__getElement = __webpack_exports__.V6;
var __webpack_exports__privateApis = __webpack_exports__.jb;
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
export { __webpack_exports__getConfig as getConfig, __webpack_exports__getContext as getContext, __webpack_exports__getElement as getElement, __webpack_exports__privateApis as privateApis, __webpack_exports__store as store, __webpack_exports__useCallback as useCallback, __webpack_exports__useEffect as useEffect, __webpack_exports__useInit as useInit, __webpack_exports__useLayoutEffect as useLayoutEffect, __webpack_exports__useMemo as useMemo, __webpack_exports__useRef as useRef, __webpack_exports__useState as useState, __webpack_exports__useWatch as useWatch, __webpack_exports__withScope as withScope };
