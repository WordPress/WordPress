/******/ var __webpack_modules__ = ({

/***/ 380:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {


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
  J0: () => (/* reexport */ h),
  FH: () => (/* reexport */ useWatch),
  v4: () => (/* reexport */ withScope)
});

// EXTERNAL MODULE: ./node_modules/preact/dist/preact.module.js
var preact_module = __webpack_require__(622);
;// ./node_modules/preact/hooks/dist/hooks.module.js
var hooks_module_t,r,hooks_module_u,i,hooks_module_o=0,hooks_module_f=[],hooks_module_c=preact_module/* options */.fF,e=hooks_module_c.__b,a=hooks_module_c.__r,v=hooks_module_c.diffed,l=hooks_module_c.__c,m=hooks_module_c.unmount,s=hooks_module_c.__;function d(n,t){hooks_module_c.__h&&hooks_module_c.__h(r,n,hooks_module_o||t),hooks_module_o=0;var u=r.__H||(r.__H={__:[],__h:[]});return n>=u.__.length&&u.__.push({}),u.__[n]}function h(n){return hooks_module_o=1,p(D,n)}function p(n,u,i){var o=d(hooks_module_t++,2);if(o.t=n,!o.__c&&(o.__=[i?i(u):D(void 0,u),function(n){var t=o.__N?o.__N[0]:o.__[0],r=o.t(t,n);t!==r&&(o.__N=[r,o.__[1]],o.__c.setState({}))}],o.__c=r,!r.u)){var f=function(n,t,r){if(!o.__c.__H)return!0;var u=o.__c.__H.__.filter(function(n){return!!n.__c});if(u.every(function(n){return!n.__N}))return!c||c.call(this,n,t,r);var i=!1;return u.forEach(function(n){if(n.__N){var t=n.__[0];n.__=n.__N,n.__N=void 0,t!==n.__[0]&&(i=!0)}}),!(!i&&o.__c.props===n)&&(!c||c.call(this,n,t,r))};r.u=!0;var c=r.shouldComponentUpdate,e=r.componentWillUpdate;r.componentWillUpdate=function(n,t,r){if(this.__e){var u=c;c=void 0,f(n,t,r),c=u}e&&e.call(this,n,t,r)},r.shouldComponentUpdate=f}return o.__N||o.__}function y(n,u){var i=d(hooks_module_t++,3);!hooks_module_c.__s&&C(i.__H,u)&&(i.__=n,i.i=u,r.__H.__h.push(i))}function _(n,u){var i=d(hooks_module_t++,4);!hooks_module_c.__s&&C(i.__H,u)&&(i.__=n,i.i=u,r.__h.push(i))}function A(n){return hooks_module_o=5,T(function(){return{current:n}},[])}function F(n,t,r){hooks_module_o=6,_(function(){return"function"==typeof n?(n(t()),function(){return n(null)}):n?(n.current=t(),function(){return n.current=null}):void 0},null==r?r:r.concat(n))}function T(n,r){var u=d(hooks_module_t++,7);return C(u.__H,r)&&(u.__=n(),u.__H=r,u.__h=n),u.__}function q(n,t){return hooks_module_o=8,T(function(){return n},t)}function x(n){var u=r.context[n.__c],i=d(hooks_module_t++,9);return i.c=n,u?(null==i.__&&(i.__=!0,u.sub(r)),u.props.value):n.__}function P(n,t){hooks_module_c.useDebugValue&&hooks_module_c.useDebugValue(t?t(n):n)}function b(n){var u=d(hooks_module_t++,10),i=h();return u.__=n,r.componentDidCatch||(r.componentDidCatch=function(n,t){u.__&&u.__(n,t),i[1](n)}),[i[0],function(){i[1](void 0)}]}function g(){var n=d(hooks_module_t++,11);if(!n.__){for(var u=r.__v;null!==u&&!u.__m&&null!==u.__;)u=u.__;var i=u.__m||(u.__m=[0,0]);n.__="P"+i[0]+"-"+i[1]++}return n.__}function j(){for(var n;n=hooks_module_f.shift();)if(n.__P&&n.__H)try{n.__H.__h.forEach(z),n.__H.__h.forEach(B),n.__H.__h=[]}catch(t){n.__H.__h=[],hooks_module_c.__e(t,n.__v)}}hooks_module_c.__b=function(n){r=null,e&&e(n)},hooks_module_c.__=function(n,t){n&&t.__k&&t.__k.__m&&(n.__m=t.__k.__m),s&&s(n,t)},hooks_module_c.__r=function(n){a&&a(n),hooks_module_t=0;var i=(r=n.__c).__H;i&&(hooks_module_u===r?(i.__h=[],r.__h=[],i.__.forEach(function(n){n.__N&&(n.__=n.__N),n.i=n.__N=void 0})):(i.__h.forEach(z),i.__h.forEach(B),i.__h=[],hooks_module_t=0)),hooks_module_u=r},hooks_module_c.diffed=function(n){v&&v(n);var t=n.__c;t&&t.__H&&(t.__H.__h.length&&(1!==hooks_module_f.push(t)&&i===hooks_module_c.requestAnimationFrame||((i=hooks_module_c.requestAnimationFrame)||w)(j)),t.__H.__.forEach(function(n){n.i&&(n.__H=n.i),n.i=void 0})),hooks_module_u=r=null},hooks_module_c.__c=function(n,t){t.some(function(n){try{n.__h.forEach(z),n.__h=n.__h.filter(function(n){return!n.__||B(n)})}catch(r){t.some(function(n){n.__h&&(n.__h=[])}),t=[],hooks_module_c.__e(r,n.__v)}}),l&&l(n,t)},hooks_module_c.unmount=function(n){m&&m(n);var t,r=n.__c;r&&r.__H&&(r.__H.__.forEach(function(n){try{z(n)}catch(n){t=n}}),r.__H=void 0,t&&hooks_module_c.__e(t,r.__v))};var k="function"==typeof requestAnimationFrame;function w(n){var t,r=function(){clearTimeout(u),k&&cancelAnimationFrame(t),setTimeout(n)},u=setTimeout(r,100);k&&(t=requestAnimationFrame(r))}function z(n){var t=r,u=n.__c;"function"==typeof u&&(n.__c=void 0,u()),r=t}function B(n){var t=r;n.__c=n.__(),r=t}function C(n,t){return!n||n.length!==t.length||t.some(function(t,r){return t!==n[r]})}function D(n,t){return"function"==typeof t?t(n):t}

;// ./node_modules/@preact/signals-core/dist/signals-core.module.js
var signals_core_module_i=Symbol.for("preact-signals");function signals_core_module_t(){if(!(signals_core_module_s>1)){var i,t=!1;while(void 0!==signals_core_module_h){var r=signals_core_module_h;signals_core_module_h=void 0;signals_core_module_f++;while(void 0!==r){var o=r.o;r.o=void 0;r.f&=-3;if(!(8&r.f)&&signals_core_module_c(r))try{r.c()}catch(r){if(!t){i=r;t=!0}}r=o}}signals_core_module_f=0;signals_core_module_s--;if(t)throw i}else signals_core_module_s--}function signals_core_module_r(i){if(signals_core_module_s>0)return i();signals_core_module_s++;try{return i()}finally{signals_core_module_t()}}var signals_core_module_o=void 0;function n(i){var t=signals_core_module_o;signals_core_module_o=void 0;try{return i()}finally{signals_core_module_o=t}}var signals_core_module_h=void 0,signals_core_module_s=0,signals_core_module_f=0,signals_core_module_v=0;function signals_core_module_e(i){if(void 0!==signals_core_module_o){var t=i.n;if(void 0===t||t.t!==signals_core_module_o){t={i:0,S:i,p:signals_core_module_o.s,n:void 0,t:signals_core_module_o,e:void 0,x:void 0,r:t};if(void 0!==signals_core_module_o.s)signals_core_module_o.s.n=t;signals_core_module_o.s=t;i.n=t;if(32&signals_core_module_o.f)i.S(t);return t}else if(-1===t.i){t.i=0;if(void 0!==t.n){t.n.p=t.p;if(void 0!==t.p)t.p.n=t.n;t.p=signals_core_module_o.s;t.n=void 0;signals_core_module_o.s.n=t;signals_core_module_o.s=t}return t}}}function signals_core_module_u(i){this.v=i;this.i=0;this.n=void 0;this.t=void 0}signals_core_module_u.prototype.brand=signals_core_module_i;signals_core_module_u.prototype.h=function(){return!0};signals_core_module_u.prototype.S=function(i){if(this.t!==i&&void 0===i.e){i.x=this.t;if(void 0!==this.t)this.t.e=i;this.t=i}};signals_core_module_u.prototype.U=function(i){if(void 0!==this.t){var t=i.e,r=i.x;if(void 0!==t){t.x=r;i.e=void 0}if(void 0!==r){r.e=t;i.x=void 0}if(i===this.t)this.t=r}};signals_core_module_u.prototype.subscribe=function(i){var t=this;return E(function(){var r=t.value,n=signals_core_module_o;signals_core_module_o=void 0;try{i(r)}finally{signals_core_module_o=n}})};signals_core_module_u.prototype.valueOf=function(){return this.value};signals_core_module_u.prototype.toString=function(){return this.value+""};signals_core_module_u.prototype.toJSON=function(){return this.value};signals_core_module_u.prototype.peek=function(){var i=signals_core_module_o;signals_core_module_o=void 0;try{return this.value}finally{signals_core_module_o=i}};Object.defineProperty(signals_core_module_u.prototype,"value",{get:function(){var i=signals_core_module_e(this);if(void 0!==i)i.i=this.i;return this.v},set:function(i){if(i!==this.v){if(signals_core_module_f>100)throw new Error("Cycle detected");this.v=i;this.i++;signals_core_module_v++;signals_core_module_s++;try{for(var r=this.t;void 0!==r;r=r.x)r.t.N()}finally{signals_core_module_t()}}}});function signals_core_module_d(i){return new signals_core_module_u(i)}function signals_core_module_c(i){for(var t=i.s;void 0!==t;t=t.n)if(t.S.i!==t.i||!t.S.h()||t.S.i!==t.i)return!0;return!1}function signals_core_module_a(i){for(var t=i.s;void 0!==t;t=t.n){var r=t.S.n;if(void 0!==r)t.r=r;t.S.n=t;t.i=-1;if(void 0===t.n){i.s=t;break}}}function signals_core_module_l(i){var t=i.s,r=void 0;while(void 0!==t){var o=t.p;if(-1===t.i){t.S.U(t);if(void 0!==o)o.n=t.n;if(void 0!==t.n)t.n.p=o}else r=t;t.S.n=t.r;if(void 0!==t.r)t.r=void 0;t=o}i.s=r}function signals_core_module_y(i){signals_core_module_u.call(this,void 0);this.x=i;this.s=void 0;this.g=signals_core_module_v-1;this.f=4}(signals_core_module_y.prototype=new signals_core_module_u).h=function(){this.f&=-3;if(1&this.f)return!1;if(32==(36&this.f))return!0;this.f&=-5;if(this.g===signals_core_module_v)return!0;this.g=signals_core_module_v;this.f|=1;if(this.i>0&&!signals_core_module_c(this)){this.f&=-2;return!0}var i=signals_core_module_o;try{signals_core_module_a(this);signals_core_module_o=this;var t=this.x();if(16&this.f||this.v!==t||0===this.i){this.v=t;this.f&=-17;this.i++}}catch(i){this.v=i;this.f|=16;this.i++}signals_core_module_o=i;signals_core_module_l(this);this.f&=-2;return!0};signals_core_module_y.prototype.S=function(i){if(void 0===this.t){this.f|=36;for(var t=this.s;void 0!==t;t=t.n)t.S.S(t)}signals_core_module_u.prototype.S.call(this,i)};signals_core_module_y.prototype.U=function(i){if(void 0!==this.t){signals_core_module_u.prototype.U.call(this,i);if(void 0===this.t){this.f&=-33;for(var t=this.s;void 0!==t;t=t.n)t.S.U(t)}}};signals_core_module_y.prototype.N=function(){if(!(2&this.f)){this.f|=6;for(var i=this.t;void 0!==i;i=i.x)i.t.N()}};Object.defineProperty(signals_core_module_y.prototype,"value",{get:function(){if(1&this.f)throw new Error("Cycle detected");var i=signals_core_module_e(this);this.h();if(void 0!==i)i.i=this.i;if(16&this.f)throw this.v;return this.v}});function signals_core_module_w(i){return new signals_core_module_y(i)}function signals_core_module_(i){var r=i.u;i.u=void 0;if("function"==typeof r){signals_core_module_s++;var n=signals_core_module_o;signals_core_module_o=void 0;try{r()}catch(t){i.f&=-2;i.f|=8;signals_core_module_g(i);throw t}finally{signals_core_module_o=n;signals_core_module_t()}}}function signals_core_module_g(i){for(var t=i.s;void 0!==t;t=t.n)t.S.U(t);i.x=void 0;i.s=void 0;signals_core_module_(i)}function signals_core_module_p(i){if(signals_core_module_o!==this)throw new Error("Out-of-order effect");signals_core_module_l(this);signals_core_module_o=i;this.f&=-2;if(8&this.f)signals_core_module_g(this);signals_core_module_t()}function signals_core_module_b(i){this.x=i;this.u=void 0;this.s=void 0;this.o=void 0;this.f=32}signals_core_module_b.prototype.c=function(){var i=this.S();try{if(8&this.f)return;if(void 0===this.x)return;var t=this.x();if("function"==typeof t)this.u=t}finally{i()}};signals_core_module_b.prototype.S=function(){if(1&this.f)throw new Error("Cycle detected");this.f|=1;this.f&=-9;signals_core_module_(this);signals_core_module_a(this);signals_core_module_s++;var i=signals_core_module_o;signals_core_module_o=this;return signals_core_module_p.bind(this,i)};signals_core_module_b.prototype.N=function(){if(!(2&this.f)){this.f|=2;this.o=signals_core_module_h;signals_core_module_h=this}};signals_core_module_b.prototype.d=function(){this.f|=8;if(!(1&this.f))signals_core_module_g(this)};function E(i){var t=new signals_core_module_b(i);try{t.c()}catch(i){t.d();throw i}return t.d.bind(t)}
;// ./node_modules/@preact/signals/dist/signals.module.js
var signals_module_v,signals_module_s;function signals_module_l(n,i){preact_module/* options */.fF[n]=i.bind(null,preact_module/* options */.fF[n]||function(){})}function signals_module_d(n){if(signals_module_s)signals_module_s();signals_module_s=n&&n.S()}function signals_module_p(n){var r=this,f=n.data,o=useSignal(f);o.value=f;var e=T(function(){var n=r.__v;while(n=n.__)if(n.__c){n.__c.__$f|=4;break}r.__$u.c=function(){var n;if(!(0,preact_module/* isValidElement */.zO)(e.peek())&&3===(null==(n=r.base)?void 0:n.nodeType))r.base.data=e.peek();else{r.__$f|=1;r.setState({})}};return signals_core_module_w(function(){var n=o.value.value;return 0===n?0:!0===n?"":n||""})},[]);return e.value}signals_module_p.displayName="_st";Object.defineProperties(signals_core_module_u.prototype,{constructor:{configurable:!0,value:void 0},type:{configurable:!0,value:signals_module_p},props:{configurable:!0,get:function(){return{data:this}}},__b:{configurable:!0,value:1}});signals_module_l("__b",function(n,r){if("string"==typeof r.type){var i,t=r.props;for(var f in t)if("children"!==f){var o=t[f];if(o instanceof signals_core_module_u){if(!i)r.__np=i={};i[f]=o;t[f]=o.peek()}}}n(r)});signals_module_l("__r",function(n,r){signals_module_d();var i,t=r.__c;if(t){t.__$f&=-2;if(void 0===(i=t.__$u))t.__$u=i=function(n){var r;E(function(){r=this});r.c=function(){t.__$f|=1;t.setState({})};return r}()}signals_module_v=t;signals_module_d(i);n(r)});signals_module_l("__e",function(n,r,i,t){signals_module_d();signals_module_v=void 0;n(r,i,t)});signals_module_l("diffed",function(n,r){signals_module_d();signals_module_v=void 0;var i;if("string"==typeof r.type&&(i=r.__e)){var t=r.__np,f=r.props;if(t){var o=i.U;if(o)for(var e in o){var u=o[e];if(void 0!==u&&!(e in t)){u.d();o[e]=void 0}}else i.U=o={};for(var a in t){var c=o[a],s=t[a];if(void 0===c){c=signals_module_(i,a,s,f);o[a]=c}else c.o(s,f)}}}n(r)});function signals_module_(n,r,i,t){var f=r in n&&void 0===n.ownerSVGElement,o=signals_core_module_d(i);return{o:function(n,r){o.value=n;t=r},d:E(function(){var i=o.value.value;if(t[r]!==i){t[r]=i;if(f)n[r]=i;else if(i)n.setAttribute(r,i);else n.removeAttribute(r)}})}}signals_module_l("unmount",function(n,r){if("string"==typeof r.type){var i=r.__e;if(i){var t=i.U;if(t){i.U=void 0;for(var f in t){var o=t[f];if(o)o.d()}}}}else{var e=r.__c;if(e){var u=e.__$u;if(u){e.__$u=void 0;u.d()}}}n(r)});signals_module_l("__h",function(n,r,i,t){if(t<3||9===t)r.__$f|=2;n(r,i,t)});preact_module/* Component */.uA.prototype.shouldComponentUpdate=function(n,r){var i=this.__$u;if(!(i&&void 0!==i.s||4&this.__$f))return!0;if(3&this.__$f)return!0;for(var t in r)return!0;for(var f in n)if("__source"!==f&&n[f]!==this.props[f])return!0;for(var o in this.props)if(!(o in n))return!0;return!1};function useSignal(n){return T(function(){return signals_core_module_d(n)},[])}function useComputed(n){var r=f(n);r.current=n;signals_module_v.__$f|=4;return t(function(){return u(function(){return r.current()})},[])}function useSignalEffect(n){var r=f(n);r.current=n;o(function(){return c(function(){return r.current()})},[])}
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
 * is being evalutated. Such representation is read-only, and contains a
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
 *  Checks wether a {@link PropSignal | `PropSignal`} instance exists for the
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
    if (typeof desc.get === 'function' || typeof desc.set === 'function') {
      if (override || isNew) {
        Object.defineProperty(target, key, {
          ...desc,
          configurable: true,
          enumerable: true
        });
        if (desc.get && propSignal) {
          propSignal.setGetter(desc.get);
        }
      }
    } else if (isPlainObject(source[key])) {
      if (isNew || override && !isPlainObject(target[key])) {
        target[key] = {};
        if (propSignal) {
          const ns = getNamespaceFromProxy(proxy);
          propSignal.setValue(proxifyState(ns, target[key]));
        }
      }
      if (isPlainObject(target[key])) {
        deepMergeRecursive(target[key], source[key], override);
      }
    } else if (override || isNew) {
      Object.defineProperty(target, key, desc);
      if (propSignal) {
        const {
          value
        } = desc;
        const ns = getNamespaceFromProxy(proxy);
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
  getOwnPropertyDescriptor: (target, key) => descriptor(target, key) || descriptor(contextObjectToFallback.get(target), key)
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
 * updated to reflect the changes in its properites, without affecting the state
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
    directives[`on-async-${type}`].filter(isNonDefaultDirectiveSuffix).forEach(entry => {
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
        const result = evaluate(entry);
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
        const result = evaluate(entry);
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
          evaluate(entry, event);
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
    classNames.filter(isNonDefaultDirectiveSuffix).forEach(entry => {
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
    style.filter(isNonDefaultDirectiveSuffix).forEach(entry => {
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
    bind.filter(isNonDefaultDirectiveSuffix).forEach(entry => {
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
    const inheritedValue = x(inheritedContext);
    const [entry] = each;
    const {
      namespace
    } = entry;
    const list = evaluate(entry);
    const itemProp = isNonDefaultDirectiveSuffix(entry) ? kebabToCamelCase(entry.suffix) : 'item';
    return list.map(item => {
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
      return (0,preact_module.h)(Provider, {
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


/***/ }),

/***/ 622:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FK: () => (/* binding */ b),
/* harmony export */   Ob: () => (/* binding */ E),
/* harmony export */   Qv: () => (/* binding */ D),
/* harmony export */   XX: () => (/* binding */ B),
/* harmony export */   fF: () => (/* binding */ l),
/* harmony export */   h: () => (/* binding */ _),
/* harmony export */   q6: () => (/* binding */ G),
/* harmony export */   uA: () => (/* binding */ k),
/* harmony export */   zO: () => (/* binding */ t)
/* harmony export */ });
/* unused harmony exports createElement, createRef, toChildArray */
var n,l,u,t,i,o,r,f,e,c,s,a,h={},v=[],p=/acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i,y=Array.isArray;function d(n,l){for(var u in l)n[u]=l[u];return n}function w(n){n&&n.parentNode&&n.parentNode.removeChild(n)}function _(l,u,t){var i,o,r,f={};for(r in u)"key"==r?i=u[r]:"ref"==r?o=u[r]:f[r]=u[r];if(arguments.length>2&&(f.children=arguments.length>3?n.call(arguments,2):t),"function"==typeof l&&null!=l.defaultProps)for(r in l.defaultProps)void 0===f[r]&&(f[r]=l.defaultProps[r]);return g(l,f,i,o,null)}function g(n,t,i,o,r){var f={type:n,props:t,key:i,ref:o,__k:null,__:null,__b:0,__e:null,__d:void 0,__c:null,constructor:void 0,__v:null==r?++u:r,__i:-1,__u:0};return null==r&&null!=l.vnode&&l.vnode(f),f}function m(){return{current:null}}function b(n){return n.children}function k(n,l){this.props=n,this.context=l}function x(n,l){if(null==l)return n.__?x(n.__,n.__i+1):null;for(var u;l<n.__k.length;l++)if(null!=(u=n.__k[l])&&null!=u.__e)return u.__e;return"function"==typeof n.type?x(n):null}function C(n){var l,u;if(null!=(n=n.__)&&null!=n.__c){for(n.__e=n.__c.base=null,l=0;l<n.__k.length;l++)if(null!=(u=n.__k[l])&&null!=u.__e){n.__e=n.__c.base=u.__e;break}return C(n)}}function S(n){(!n.__d&&(n.__d=!0)&&i.push(n)&&!M.__r++||o!==l.debounceRendering)&&((o=l.debounceRendering)||r)(M)}function M(){var n,u,t,o,r,e,c,s;for(i.sort(f);n=i.shift();)n.__d&&(u=i.length,o=void 0,e=(r=(t=n).__v).__e,c=[],s=[],t.__P&&((o=d({},r)).__v=r.__v+1,l.vnode&&l.vnode(o),O(t.__P,o,r,t.__n,t.__P.namespaceURI,32&r.__u?[e]:null,c,null==e?x(r):e,!!(32&r.__u),s),o.__v=r.__v,o.__.__k[o.__i]=o,j(c,o,s),o.__e!=e&&C(o)),i.length>u&&i.sort(f));M.__r=0}function P(n,l,u,t,i,o,r,f,e,c,s){var a,p,y,d,w,_=t&&t.__k||v,g=l.length;for(u.__d=e,$(u,l,_),e=u.__d,a=0;a<g;a++)null!=(y=u.__k[a])&&(p=-1===y.__i?h:_[y.__i]||h,y.__i=a,O(n,y,p,i,o,r,f,e,c,s),d=y.__e,y.ref&&p.ref!=y.ref&&(p.ref&&N(p.ref,null,y),s.push(y.ref,y.__c||d,y)),null==w&&null!=d&&(w=d),65536&y.__u||p.__k===y.__k?e=I(y,e,n):"function"==typeof y.type&&void 0!==y.__d?e=y.__d:d&&(e=d.nextSibling),y.__d=void 0,y.__u&=-196609);u.__d=e,u.__e=w}function $(n,l,u){var t,i,o,r,f,e=l.length,c=u.length,s=c,a=0;for(n.__k=[],t=0;t<e;t++)null!=(i=l[t])&&"boolean"!=typeof i&&"function"!=typeof i?(r=t+a,(i=n.__k[t]="string"==typeof i||"number"==typeof i||"bigint"==typeof i||i.constructor==String?g(null,i,null,null,null):y(i)?g(b,{children:i},null,null,null):void 0===i.constructor&&i.__b>0?g(i.type,i.props,i.key,i.ref?i.ref:null,i.__v):i).__=n,i.__b=n.__b+1,o=null,-1!==(f=i.__i=L(i,u,r,s))&&(s--,(o=u[f])&&(o.__u|=131072)),null==o||null===o.__v?(-1==f&&a--,"function"!=typeof i.type&&(i.__u|=65536)):f!==r&&(f==r-1?a--:f==r+1?a++:(f>r?a--:a++,i.__u|=65536))):i=n.__k[t]=null;if(s)for(t=0;t<c;t++)null!=(o=u[t])&&0==(131072&o.__u)&&(o.__e==n.__d&&(n.__d=x(o)),V(o,o))}function I(n,l,u){var t,i;if("function"==typeof n.type){for(t=n.__k,i=0;t&&i<t.length;i++)t[i]&&(t[i].__=n,l=I(t[i],l,u));return l}n.__e!=l&&(l&&n.type&&!u.contains(l)&&(l=x(n)),u.insertBefore(n.__e,l||null),l=n.__e);do{l=l&&l.nextSibling}while(null!=l&&8===l.nodeType);return l}function H(n,l){return l=l||[],null==n||"boolean"==typeof n||(y(n)?n.some(function(n){H(n,l)}):l.push(n)),l}function L(n,l,u,t){var i=n.key,o=n.type,r=u-1,f=u+1,e=l[u];if(null===e||e&&i==e.key&&o===e.type&&0==(131072&e.__u))return u;if(t>(null!=e&&0==(131072&e.__u)?1:0))for(;r>=0||f<l.length;){if(r>=0){if((e=l[r])&&0==(131072&e.__u)&&i==e.key&&o===e.type)return r;r--}if(f<l.length){if((e=l[f])&&0==(131072&e.__u)&&i==e.key&&o===e.type)return f;f++}}return-1}function T(n,l,u){"-"===l[0]?n.setProperty(l,null==u?"":u):n[l]=null==u?"":"number"!=typeof u||p.test(l)?u:u+"px"}function A(n,l,u,t,i){var o;n:if("style"===l)if("string"==typeof u)n.style.cssText=u;else{if("string"==typeof t&&(n.style.cssText=t=""),t)for(l in t)u&&l in u||T(n.style,l,"");if(u)for(l in u)t&&u[l]===t[l]||T(n.style,l,u[l])}else if("o"===l[0]&&"n"===l[1])o=l!==(l=l.replace(/(PointerCapture)$|Capture$/i,"$1")),l=l.toLowerCase()in n||"onFocusOut"===l||"onFocusIn"===l?l.toLowerCase().slice(2):l.slice(2),n.l||(n.l={}),n.l[l+o]=u,u?t?u.u=t.u:(u.u=e,n.addEventListener(l,o?s:c,o)):n.removeEventListener(l,o?s:c,o);else{if("http://www.w3.org/2000/svg"==i)l=l.replace(/xlink(H|:h)/,"h").replace(/sName$/,"s");else if("width"!=l&&"height"!=l&&"href"!=l&&"list"!=l&&"form"!=l&&"tabIndex"!=l&&"download"!=l&&"rowSpan"!=l&&"colSpan"!=l&&"role"!=l&&"popover"!=l&&l in n)try{n[l]=null==u?"":u;break n}catch(n){}"function"==typeof u||(null==u||!1===u&&"-"!==l[4]?n.removeAttribute(l):n.setAttribute(l,"popover"==l&&1==u?"":u))}}function F(n){return function(u){if(this.l){var t=this.l[u.type+n];if(null==u.t)u.t=e++;else if(u.t<t.u)return;return t(l.event?l.event(u):u)}}}function O(n,u,t,i,o,r,f,e,c,s){var a,h,v,p,w,_,g,m,x,C,S,M,$,I,H,L,T=u.type;if(void 0!==u.constructor)return null;128&t.__u&&(c=!!(32&t.__u),r=[e=u.__e=t.__e]),(a=l.__b)&&a(u);n:if("function"==typeof T)try{if(m=u.props,x="prototype"in T&&T.prototype.render,C=(a=T.contextType)&&i[a.__c],S=a?C?C.props.value:a.__:i,t.__c?g=(h=u.__c=t.__c).__=h.__E:(x?u.__c=h=new T(m,S):(u.__c=h=new k(m,S),h.constructor=T,h.render=q),C&&C.sub(h),h.props=m,h.state||(h.state={}),h.context=S,h.__n=i,v=h.__d=!0,h.__h=[],h._sb=[]),x&&null==h.__s&&(h.__s=h.state),x&&null!=T.getDerivedStateFromProps&&(h.__s==h.state&&(h.__s=d({},h.__s)),d(h.__s,T.getDerivedStateFromProps(m,h.__s))),p=h.props,w=h.state,h.__v=u,v)x&&null==T.getDerivedStateFromProps&&null!=h.componentWillMount&&h.componentWillMount(),x&&null!=h.componentDidMount&&h.__h.push(h.componentDidMount);else{if(x&&null==T.getDerivedStateFromProps&&m!==p&&null!=h.componentWillReceiveProps&&h.componentWillReceiveProps(m,S),!h.__e&&(null!=h.shouldComponentUpdate&&!1===h.shouldComponentUpdate(m,h.__s,S)||u.__v===t.__v)){for(u.__v!==t.__v&&(h.props=m,h.state=h.__s,h.__d=!1),u.__e=t.__e,u.__k=t.__k,u.__k.some(function(n){n&&(n.__=u)}),M=0;M<h._sb.length;M++)h.__h.push(h._sb[M]);h._sb=[],h.__h.length&&f.push(h);break n}null!=h.componentWillUpdate&&h.componentWillUpdate(m,h.__s,S),x&&null!=h.componentDidUpdate&&h.__h.push(function(){h.componentDidUpdate(p,w,_)})}if(h.context=S,h.props=m,h.__P=n,h.__e=!1,$=l.__r,I=0,x){for(h.state=h.__s,h.__d=!1,$&&$(u),a=h.render(h.props,h.state,h.context),H=0;H<h._sb.length;H++)h.__h.push(h._sb[H]);h._sb=[]}else do{h.__d=!1,$&&$(u),a=h.render(h.props,h.state,h.context),h.state=h.__s}while(h.__d&&++I<25);h.state=h.__s,null!=h.getChildContext&&(i=d(d({},i),h.getChildContext())),x&&!v&&null!=h.getSnapshotBeforeUpdate&&(_=h.getSnapshotBeforeUpdate(p,w)),P(n,y(L=null!=a&&a.type===b&&null==a.key?a.props.children:a)?L:[L],u,t,i,o,r,f,e,c,s),h.base=u.__e,u.__u&=-161,h.__h.length&&f.push(h),g&&(h.__E=h.__=null)}catch(n){if(u.__v=null,c||null!=r){for(u.__u|=c?160:128;e&&8===e.nodeType&&e.nextSibling;)e=e.nextSibling;r[r.indexOf(e)]=null,u.__e=e}else u.__e=t.__e,u.__k=t.__k;l.__e(n,u,t)}else null==r&&u.__v===t.__v?(u.__k=t.__k,u.__e=t.__e):u.__e=z(t.__e,u,t,i,o,r,f,c,s);(a=l.diffed)&&a(u)}function j(n,u,t){u.__d=void 0;for(var i=0;i<t.length;i++)N(t[i],t[++i],t[++i]);l.__c&&l.__c(u,n),n.some(function(u){try{n=u.__h,u.__h=[],n.some(function(n){n.call(u)})}catch(n){l.__e(n,u.__v)}})}function z(u,t,i,o,r,f,e,c,s){var a,v,p,d,_,g,m,b=i.props,k=t.props,C=t.type;if("svg"===C?r="http://www.w3.org/2000/svg":"math"===C?r="http://www.w3.org/1998/Math/MathML":r||(r="http://www.w3.org/1999/xhtml"),null!=f)for(a=0;a<f.length;a++)if((_=f[a])&&"setAttribute"in _==!!C&&(C?_.localName===C:3===_.nodeType)){u=_,f[a]=null;break}if(null==u){if(null===C)return document.createTextNode(k);u=document.createElementNS(r,C,k.is&&k),c&&(l.__m&&l.__m(t,f),c=!1),f=null}if(null===C)b===k||c&&u.data===k||(u.data=k);else{if(f=f&&n.call(u.childNodes),b=i.props||h,!c&&null!=f)for(b={},a=0;a<u.attributes.length;a++)b[(_=u.attributes[a]).name]=_.value;for(a in b)if(_=b[a],"children"==a);else if("dangerouslySetInnerHTML"==a)p=_;else if(!(a in k)){if("value"==a&&"defaultValue"in k||"checked"==a&&"defaultChecked"in k)continue;A(u,a,null,_,r)}for(a in k)_=k[a],"children"==a?d=_:"dangerouslySetInnerHTML"==a?v=_:"value"==a?g=_:"checked"==a?m=_:c&&"function"!=typeof _||b[a]===_||A(u,a,_,b[a],r);if(v)c||p&&(v.__html===p.__html||v.__html===u.innerHTML)||(u.innerHTML=v.__html),t.__k=[];else if(p&&(u.innerHTML=""),P(u,y(d)?d:[d],t,i,o,"foreignObject"===C?"http://www.w3.org/1999/xhtml":r,f,e,f?f[0]:i.__k&&x(i,0),c,s),null!=f)for(a=f.length;a--;)w(f[a]);c||(a="value","progress"===C&&null==g?u.removeAttribute("value"):void 0!==g&&(g!==u[a]||"progress"===C&&!g||"option"===C&&g!==b[a])&&A(u,a,g,b[a],r),a="checked",void 0!==m&&m!==u[a]&&A(u,a,m,b[a],r))}return u}function N(n,u,t){try{if("function"==typeof n){var i="function"==typeof n.__u;i&&n.__u(),i&&null==u||(n.__u=n(u))}else n.current=u}catch(n){l.__e(n,t)}}function V(n,u,t){var i,o;if(l.unmount&&l.unmount(n),(i=n.ref)&&(i.current&&i.current!==n.__e||N(i,null,u)),null!=(i=n.__c)){if(i.componentWillUnmount)try{i.componentWillUnmount()}catch(n){l.__e(n,u)}i.base=i.__P=null}if(i=n.__k)for(o=0;o<i.length;o++)i[o]&&V(i[o],u,t||"function"!=typeof n.type);t||w(n.__e),n.__c=n.__=n.__e=n.__d=void 0}function q(n,l,u){return this.constructor(n,u)}function B(u,t,i){var o,r,f,e;l.__&&l.__(u,t),r=(o="function"==typeof i)?null:i&&i.__k||t.__k,f=[],e=[],O(t,u=(!o&&i||t).__k=_(b,null,[u]),r||h,h,t.namespaceURI,!o&&i?[i]:r?null:t.firstChild?n.call(t.childNodes):null,f,!o&&i?i:r?r.__e:t.firstChild,o,e),j(f,u,e)}function D(n,l){B(n,l,D)}function E(l,u,t){var i,o,r,f,e=d({},l.props);for(r in l.type&&l.type.defaultProps&&(f=l.type.defaultProps),u)"key"==r?i=u[r]:"ref"==r?o=u[r]:e[r]=void 0===u[r]&&void 0!==f?f[r]:u[r];return arguments.length>2&&(e.children=arguments.length>3?n.call(arguments,2):t),g(l.type,e,i||l.key,o||l.ref,null)}function G(n,l){var u={__c:l="__cC"+a++,__:n,Consumer:function(n,l){return n.children(l)},Provider:function(n){var u,t;return this.getChildContext||(u=new Set,(t={})[l]=this,this.getChildContext=function(){return t},this.componentWillUnmount=function(){u=null},this.shouldComponentUpdate=function(n){this.props.value!==n.value&&u.forEach(function(n){n.__e=!0,S(n)})},this.sub=function(n){u.add(n);var l=n.componentWillUnmount;n.componentWillUnmount=function(){u&&u.delete(n),l&&l.call(n)}}),n.children}};return u.Provider.__=u.Consumer.contextType=u}n=v.slice,l={__e:function(n,l,u,t){for(var i,o,r;l=l.__;)if((i=l.__c)&&!i.__)try{if((o=i.constructor)&&null!=o.getDerivedStateFromError&&(i.setState(o.getDerivedStateFromError(n)),r=i.__d),null!=i.componentDidCatch&&(i.componentDidCatch(n,t||{}),r=i.__d),r)return i.__E=i}catch(l){n=l}throw n}},u=0,t=function(n){return null!=n&&null==n.constructor},k.prototype.setState=function(n,l){var u;u=null!=this.__s&&this.__s!==this.state?this.__s:this.__s=d({},this.state),"function"==typeof n&&(n=n(d({},u),this.props)),n&&d(u,n),null!=n&&this.__v&&(l&&this._sb.push(l),S(this))},k.prototype.forceUpdate=function(n){this.__v&&(this.__e=!0,n&&this.__h.push(n),S(this))},k.prototype.render=b,i=[],r="function"==typeof Promise?Promise.prototype.then.bind(Promise.resolve()):setTimeout,f=function(n,l){return n.__v.__b-l.__v.__b},M.__r=0,e=0,c=F(!1),s=F(!0),a=0;


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
  zj: () => (/* reexport */ debug_build_module/* getConfig */.zj),
  SD: () => (/* reexport */ debug_build_module/* getContext */.SD),
  V6: () => (/* reexport */ debug_build_module/* getElement */.V6),
  $K: () => (/* reexport */ debug_build_module/* getServerContext */.$K),
  vT: () => (/* reexport */ debug_build_module/* getServerState */.vT),
  jb: () => (/* reexport */ debug_build_module/* privateApis */.jb),
  yT: () => (/* reexport */ debug_build_module/* splitTask */.yT),
  M_: () => (/* reexport */ debug_build_module/* store */.M_),
  hb: () => (/* reexport */ debug_build_module/* useCallback */.hb),
  vJ: () => (/* reexport */ debug_build_module/* useEffect */.vJ),
  ip: () => (/* reexport */ debug_build_module/* useInit */.ip),
  Nf: () => (/* reexport */ debug_build_module/* useLayoutEffect */.Nf),
  Kr: () => (/* reexport */ debug_build_module/* useMemo */.Kr),
  li: () => (/* reexport */ debug_build_module/* useRef */.li),
  J0: () => (/* reexport */ debug_build_module/* useState */.J0),
  FH: () => (/* reexport */ debug_build_module/* useWatch */.FH),
  v4: () => (/* reexport */ debug_build_module/* withScope */.v4)
});

// EXTERNAL MODULE: ./node_modules/preact/dist/preact.module.js
var debug_preact_module = __webpack_require__(622);
;// ./node_modules/preact/devtools/dist/devtools.module.js
var debug_i;function debug_t(o,e){return n.__a&&n.__a(e),o}null!=(debug_i="undefined"!=typeof globalThis?globalThis:"undefined"!=typeof window?window:void 0)&&debug_i.__PREACT_DEVTOOLS__&&debug_i.__PREACT_DEVTOOLS__.attachPreact("10.24.3",debug_preact_module/* options */.fF,{Fragment:debug_preact_module/* Fragment */.FK,Component:debug_preact_module/* Component */.uA});

;// ./node_modules/preact/debug/dist/debug.module.js
var debug_debug_module_t={};function debug_r(){debug_debug_module_t={}}function debug_a(e){return e.type===debug_preact_module/* Fragment */.FK?"Fragment":"function"==typeof e.type?e.type.displayName||e.type.name:"string"==typeof e.type?e.type:"#text"}var debug_debug_module_i=[],debug_s=[];function debug_c(){return debug_debug_module_i.length>0?debug_debug_module_i[debug_debug_module_i.length-1]:null}var debug_l=!0;function debug_u(e){return"function"==typeof e.type&&e.type!=debug_preact_module/* Fragment */.FK}function debug_f(n){for(var e=[n],o=n;null!=o.__o;)e.push(o.__o),o=o.__o;return e.reduce(function(n,e){n+="  in "+debug_a(e);var o=e.__source;return o?n+=" (at "+o.fileName+":"+o.lineNumber+")":debug_l&&console.warn("Add @babel/plugin-transform-react-jsx-source to get a more detailed component stack. Note that you should not add it to production builds of your App for bundle size reasons."),debug_l=!1,n+"\n"},"")}var debug_d="function"==typeof WeakMap;function debug_p(n){var e=[];return n.__k?(n.__k.forEach(function(n){n&&"function"==typeof n.type?e.push.apply(e,debug_p(n)):n&&"string"==typeof n.type&&e.push(n.type)}),e):e}function debug_h(n){return n?"function"==typeof n.type?null==n.__?null!=n.__e&&null!=n.__e.parentNode?n.__e.parentNode.localName:"":debug_h(n.__):n.type:""}var debug_v=debug_preact_module/* Component */.uA.prototype.setState;function debug_y(n){return"table"===n||"tfoot"===n||"tbody"===n||"thead"===n||"td"===n||"tr"===n||"th"===n}debug_preact_module/* Component */.uA.prototype.setState=function(n,e){return null==this.__v&&null==this.state&&console.warn('Calling "this.setState" inside the constructor of a component is a no-op and might be a bug in your application. Instead, set "this.state = {}" directly.\n\n'+debug_f(debug_c())),debug_v.call(this,n,e)};var debug_m=/^(address|article|aside|blockquote|details|div|dl|fieldset|figcaption|figure|footer|form|h1|h2|h3|h4|h5|h6|header|hgroup|hr|main|menu|nav|ol|p|pre|search|section|table|ul)$/,debug_b=debug_preact_module/* Component */.uA.prototype.forceUpdate;function debug_w(n){var e=n.props,o=debug_a(n),t="";for(var r in e)if(e.hasOwnProperty(r)&&"children"!==r){var i=e[r];"function"==typeof i&&(i="function "+(i.displayName||i.name)+"() {}"),i=Object(i)!==i||i.toString?i+"":Object.prototype.toString.call(i),t+=" "+r+"="+JSON.stringify(i)}var s=e.children;return"<"+o+t+(s&&s.length?">..</"+o+">":" />")}debug_preact_module/* Component */.uA.prototype.forceUpdate=function(n){return null==this.__v?console.warn('Calling "this.forceUpdate" inside the constructor of a component is a no-op and might be a bug in your application.\n\n'+debug_f(debug_c())):null==this.__P&&console.warn('Can\'t call "this.forceUpdate" on an unmounted component. This is a no-op, but it indicates a memory leak in your application. To fix, cancel all subscriptions and asynchronous tasks in the componentWillUnmount method.\n\n'+debug_f(this.__v)),debug_b.call(this,n)},debug_preact_module/* options */.fF.__m=function(n,e){var o=n.type,t=e.map(function(n){return n&&n.localName}).filter(Boolean);console.error("Expected a DOM node of type "+o+" but found "+t.join(", ")+"as available DOM-node(s), this is caused by the SSR'd HTML containing different DOM-nodes compared to the hydrated one.\n\n"+debug_f(n))},function(){!function(){var n=debug_preact_module/* options */.fF.__b,o=debug_preact_module/* options */.fF.diffed,t=debug_preact_module/* options */.fF.__,r=debug_preact_module/* options */.fF.vnode,a=debug_preact_module/* options */.fF.__r;debug_preact_module/* options */.fF.diffed=function(n){debug_u(n)&&debug_s.pop(),debug_debug_module_i.pop(),o&&o(n)},debug_preact_module/* options */.fF.__b=function(e){debug_u(e)&&debug_debug_module_i.push(e),n&&n(e)},debug_preact_module/* options */.fF.__=function(n,e){debug_s=[],t&&t(n,e)},debug_preact_module/* options */.fF.vnode=function(n){n.__o=debug_s.length>0?debug_s[debug_s.length-1]:null,r&&r(n)},debug_preact_module/* options */.fF.__r=function(n){debug_u(n)&&debug_s.push(n),a&&a(n)}}();var n=!1,o=debug_preact_module/* options */.fF.__b,r=debug_preact_module/* options */.fF.diffed,c=debug_preact_module/* options */.fF.vnode,l=debug_preact_module/* options */.fF.__r,v=debug_preact_module/* options */.fF.__e,b=debug_preact_module/* options */.fF.__,g=debug_preact_module/* options */.fF.__h,E=debug_d?{useEffect:new WeakMap,useLayoutEffect:new WeakMap,lazyPropTypes:new WeakMap}:null,k=[];debug_preact_module/* options */.fF.__e=function(n,e,o,t){if(e&&e.__c&&"function"==typeof n.then){var r=n;n=new Error("Missing Suspense. The throwing component was: "+debug_a(e));for(var i=e;i;i=i.__)if(i.__c&&i.__c.__c){n=r;break}if(n instanceof Error)throw n}try{(t=t||{}).componentStack=debug_f(e),v(n,e,o,t),"function"!=typeof n.then&&setTimeout(function(){throw n})}catch(n){throw n}},debug_preact_module/* options */.fF.__=function(n,e){if(!e)throw new Error("Undefined parent passed to render(), this is the second argument.\nCheck if the element is available in the DOM/has the correct id.");var o;switch(e.nodeType){case 1:case 11:case 9:o=!0;break;default:o=!1}if(!o){var t=debug_a(n);throw new Error("Expected a valid HTML node as a second argument to render.\tReceived "+e+" instead: render(<"+t+" />, "+e+");")}b&&b(n,e)},debug_preact_module/* options */.fF.__b=function(e){var r=e.type;if(n=!0,void 0===r)throw new Error("Undefined component passed to createElement()\n\nYou likely forgot to export your component or might have mixed up default and named imports"+debug_w(e)+"\n\n"+debug_f(e));if(null!=r&&"object"==typeof r){if(void 0!==r.__k&&void 0!==r.__e)throw new Error("Invalid type passed to createElement(): "+r+"\n\nDid you accidentally pass a JSX literal as JSX twice?\n\n  let My"+debug_a(e)+" = "+debug_w(r)+";\n  let vnode = <My"+debug_a(e)+" />;\n\nThis usually happens when you export a JSX literal and not the component.\n\n"+debug_f(e));throw new Error("Invalid type passed to createElement(): "+(Array.isArray(r)?"array":r))}if(void 0!==e.ref&&"function"!=typeof e.ref&&"object"!=typeof e.ref&&!("$$typeof"in e))throw new Error('Component\'s "ref" property should be a function, or an object created by createRef(), but got ['+typeof e.ref+"] instead\n"+debug_w(e)+"\n\n"+debug_f(e));if("string"==typeof e.type)for(var i in e.props)if("o"===i[0]&&"n"===i[1]&&"function"!=typeof e.props[i]&&null!=e.props[i])throw new Error("Component's \""+i+'" property should be a function, but got ['+typeof e.props[i]+"] instead\n"+debug_w(e)+"\n\n"+debug_f(e));if("function"==typeof e.type&&e.type.propTypes){if("Lazy"===e.type.displayName&&E&&!E.lazyPropTypes.has(e.type)){var s="PropTypes are not supported on lazy(). Use propTypes on the wrapped component itself. ";try{var c=e.type();E.lazyPropTypes.set(e.type,!0),console.warn(s+"Component wrapped in lazy() is "+debug_a(c))}catch(n){console.warn(s+"We will log the wrapped component's name once it is loaded.")}}var l=e.props;e.type.__f&&delete(l=function(n,e){for(var o in e)n[o]=e[o];return n}({},l)).ref,function(n,e,o,r,a){Object.keys(n).forEach(function(o){var i;try{i=n[o](e,o,r,"prop",null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(n){i=n}i&&!(i.message in debug_debug_module_t)&&(debug_debug_module_t[i.message]=!0,console.error("Failed prop type: "+i.message+(a&&"\n"+a()||"")))})}(e.type.propTypes,l,0,debug_a(e),function(){return debug_f(e)})}o&&o(e)};var T,_=0;debug_preact_module/* options */.fF.__r=function(e){l&&l(e),n=!0;var o=e.__c;if(o===T?_++:_=1,_>=25)throw new Error("Too many re-renders. This is limited to prevent an infinite loop which may lock up your browser. The component causing this is: "+debug_a(e));T=o},debug_preact_module/* options */.fF.__h=function(e,o,t){if(!e||!n)throw new Error("Hook can only be invoked from render methods.");g&&g(e,o,t)};var O=function(n,e){return{get:function(){var o="get"+n+e;k&&k.indexOf(o)<0&&(k.push(o),console.warn("getting vnode."+n+" is deprecated, "+e))},set:function(){var o="set"+n+e;k&&k.indexOf(o)<0&&(k.push(o),console.warn("setting vnode."+n+" is not allowed, "+e))}}},I={nodeName:O("nodeName","use vnode.type"),attributes:O("attributes","use vnode.props"),children:O("children","use vnode.props.children")},M=Object.create({},I);debug_preact_module/* options */.fF.vnode=function(n){var e=n.props;if(null!==n.type&&null!=e&&("__source"in e||"__self"in e)){var o=n.props={};for(var t in e){var r=e[t];"__source"===t?n.__source=r:"__self"===t?n.__self=r:o[t]=r}}n.__proto__=M,c&&c(n)},debug_preact_module/* options */.fF.diffed=function(e){var o,t=e.type,i=e.__;if(e.__k&&e.__k.forEach(function(n){if("object"==typeof n&&n&&void 0===n.type){var o=Object.keys(n).join(",");throw new Error("Objects are not valid as a child. Encountered an object with the keys {"+o+"}.\n\n"+debug_f(e))}}),e.__c===T&&(_=0),"string"==typeof t&&(debug_y(t)||"p"===t||"a"===t||"button"===t)){var s=debug_h(i);if(""!==s&&debug_y(t))"table"===t&&"td"!==s&&debug_y(s)?(console.log(s,i.__e),console.error("Improper nesting of table. Your <table> should not have a table-node parent."+debug_w(e)+"\n\n"+debug_f(e))):"thead"!==t&&"tfoot"!==t&&"tbody"!==t||"table"===s?"tr"===t&&"thead"!==s&&"tfoot"!==s&&"tbody"!==s?console.error("Improper nesting of table. Your <tr> should have a <thead/tbody/tfoot> parent."+debug_w(e)+"\n\n"+debug_f(e)):"td"===t&&"tr"!==s?console.error("Improper nesting of table. Your <td> should have a <tr> parent."+debug_w(e)+"\n\n"+debug_f(e)):"th"===t&&"tr"!==s&&console.error("Improper nesting of table. Your <th> should have a <tr>."+debug_w(e)+"\n\n"+debug_f(e)):console.error("Improper nesting of table. Your <thead/tbody/tfoot> should have a <table> parent."+debug_w(e)+"\n\n"+debug_f(e));else if("p"===t){var c=debug_p(e).filter(function(n){return debug_m.test(n)});c.length&&console.error("Improper nesting of paragraph. Your <p> should not have "+c.join(", ")+"as child-elements."+debug_w(e)+"\n\n"+debug_f(e))}else"a"!==t&&"button"!==t||-1!==debug_p(e).indexOf(t)&&console.error("Improper nesting of interactive content. Your <"+t+"> should not have other "+("a"===t?"anchor":"button")+" tags as child-elements."+debug_w(e)+"\n\n"+debug_f(e))}if(n=!1,r&&r(e),null!=e.__k)for(var l=[],u=0;u<e.__k.length;u++){var d=e.__k[u];if(d&&null!=d.key){var v=d.key;if(-1!==l.indexOf(v)){console.error('Following component has two or more children with the same key attribute: "'+v+'". This may cause glitches and misbehavior in rendering process. Component: \n\n'+debug_w(e)+"\n\n"+debug_f(e));break}l.push(v)}}if(null!=e.__c&&null!=e.__c.__H){var b=e.__c.__H.__;if(b)for(var g=0;g<b.length;g+=1){var E=b[g];if(E.__H)for(var k=0;k<E.__H.length;k++)if((o=E.__H[k])!=o){var O=debug_a(e);console.warn("Invalid argument passed to hook. Hooks should not be called with NaN in the dependency array. Hook index "+g+" in component "+O+" was called with NaN.")}}}}}();

// EXTERNAL MODULE: ./node_modules/@wordpress/interactivity/build-module/index.js + 18 modules
var debug_build_module = __webpack_require__(380);
;// ./node_modules/@wordpress/interactivity/build-module/debug.js
/**
 * External dependencies
 */



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
export { __webpack_exports__getConfig as getConfig, __webpack_exports__getContext as getContext, __webpack_exports__getElement as getElement, __webpack_exports__getServerContext as getServerContext, __webpack_exports__getServerState as getServerState, __webpack_exports__privateApis as privateApis, __webpack_exports__splitTask as splitTask, __webpack_exports__store as store, __webpack_exports__useCallback as useCallback, __webpack_exports__useEffect as useEffect, __webpack_exports__useInit as useInit, __webpack_exports__useLayoutEffect as useLayoutEffect, __webpack_exports__useMemo as useMemo, __webpack_exports__useRef as useRef, __webpack_exports__useState as useState, __webpack_exports__useWatch as useWatch, __webpack_exports__withScope as withScope };
