/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 754:
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {


// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  c4: function() { return /* reexport */ router_navigate; },
  tL: function() { return /* reexport */ prefetch; },
  h: function() { return /* reexport */ store; }
});

// UNUSED EXPORTS: createElement, deepSignal, directive, useContext, useEffect, useMemo

;// CONCATENATED MODULE: ./node_modules/preact/dist/preact.module.js
var preact_module_n,
  preact_module_l,
  preact_module_u,
  preact_module_t,
  i,
  preact_module_o,
  preact_module_r,
  preact_module_f,
  preact_module_e,
  preact_module_c = {},
  s = [],
  a = /acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i,
  h = Array.isArray;
function v(n, l) {
  for (var u in l) n[u] = l[u];
  return n;
}
function p(n) {
  var l = n.parentNode;
  l && l.removeChild(n);
}
function y(l, u, t) {
  var i,
    o,
    r,
    f = {};
  for (r in u) "key" == r ? i = u[r] : "ref" == r ? o = u[r] : f[r] = u[r];
  if (arguments.length > 2 && (f.children = arguments.length > 3 ? preact_module_n.call(arguments, 2) : t), "function" == typeof l && null != l.defaultProps) for (r in l.defaultProps) void 0 === f[r] && (f[r] = l.defaultProps[r]);
  return d(l, f, i, o, null);
}
function d(n, t, i, o, r) {
  var f = {
    type: n,
    props: t,
    key: i,
    ref: o,
    __k: null,
    __: null,
    __b: 0,
    __e: null,
    __d: void 0,
    __c: null,
    constructor: void 0,
    __v: null == r ? ++preact_module_u : r,
    __i: -1,
    __u: 0
  };
  return null == r && null != preact_module_l.vnode && preact_module_l.vnode(f), f;
}
function _() {
  return {
    current: null
  };
}
function g(n) {
  return n.children;
}
function b(n, l) {
  this.props = n, this.context = l;
}
function m(n, l) {
  if (null == l) return n.__ ? m(n.__, n.__i + 1) : null;
  for (var u; l < n.__k.length; l++) if (null != (u = n.__k[l]) && null != u.__e) return u.__e;
  return "function" == typeof n.type ? m(n) : null;
}
function k(n) {
  var l, u;
  if (null != (n = n.__) && null != n.__c) {
    for (n.__e = n.__c.base = null, l = 0; l < n.__k.length; l++) if (null != (u = n.__k[l]) && null != u.__e) {
      n.__e = n.__c.base = u.__e;
      break;
    }
    return k(n);
  }
}
function w(n) {
  (!n.__d && (n.__d = !0) && i.push(n) && !x.__r++ || preact_module_o !== preact_module_l.debounceRendering) && ((preact_module_o = preact_module_l.debounceRendering) || preact_module_r)(x);
}
function x() {
  var n, u, t, o, r, e, c, s, a;
  for (i.sort(preact_module_f); n = i.shift();) n.__d && (u = i.length, o = void 0, e = (r = (t = n).__v).__e, s = [], a = [], (c = t.__P) && ((o = v({}, r)).__v = r.__v + 1, preact_module_l.vnode && preact_module_l.vnode(o), L(c, o, r, t.__n, void 0 !== c.ownerSVGElement, 32 & r.__u ? [e] : null, s, null == e ? m(r) : e, !!(32 & r.__u), a), o.__.__k[o.__i] = o, M(s, o, a), o.__e != e && k(o)), i.length > u && i.sort(preact_module_f));
  x.__r = 0;
}
function C(n, l, u, t, i, o, r, f, e, a, h) {
  var v,
    p,
    y,
    d,
    _,
    g = t && t.__k || s,
    b = l.length;
  for (u.__d = e, P(u, l, g), e = u.__d, v = 0; v < b; v++) null != (y = u.__k[v]) && "boolean" != typeof y && "function" != typeof y && (p = -1 === y.__i ? preact_module_c : g[y.__i] || preact_module_c, y.__i = v, L(n, y, p, i, o, r, f, e, a, h), d = y.__e, y.ref && p.ref != y.ref && (p.ref && z(p.ref, null, y), h.push(y.ref, y.__c || d, y)), null == _ && null != d && (_ = d), 65536 & y.__u || p.__k === y.__k ? e = S(y, e, n) : "function" == typeof y.type && void 0 !== y.__d ? e = y.__d : d && (e = d.nextSibling), y.__d = void 0, y.__u &= -196609);
  u.__d = e, u.__e = _;
}
function P(n, l, u) {
  var t,
    i,
    o,
    r,
    f,
    e = l.length,
    c = u.length,
    s = c,
    a = 0;
  for (n.__k = [], t = 0; t < e; t++) null != (i = n.__k[t] = null == (i = l[t]) || "boolean" == typeof i || "function" == typeof i ? null : "string" == typeof i || "number" == typeof i || "bigint" == typeof i || i.constructor == String ? d(null, i, null, null, i) : h(i) ? d(g, {
    children: i
  }, null, null, null) : i.__b > 0 ? d(i.type, i.props, i.key, i.ref ? i.ref : null, i.__v) : i) ? (i.__ = n, i.__b = n.__b + 1, f = H(i, u, r = t + a, s), i.__i = f, o = null, -1 !== f && (s--, (o = u[f]) && (o.__u |= 131072)), null == o || null === o.__v ? (-1 == f && a--, "function" != typeof i.type && (i.__u |= 65536)) : f !== r && (f === r + 1 ? a++ : f > r ? s > e - r ? a += f - r : a-- : a = f < r && f == r - 1 ? f - r : 0, f !== t + a && (i.__u |= 65536))) : (o = u[t]) && null == o.key && o.__e && (o.__e == n.__d && (n.__d = m(o)), N(o, o, !1), u[t] = null, s--);
  if (s) for (t = 0; t < c; t++) null != (o = u[t]) && 0 == (131072 & o.__u) && (o.__e == n.__d && (n.__d = m(o)), N(o, o));
}
function S(n, l, u) {
  var t, i;
  if ("function" == typeof n.type) {
    for (t = n.__k, i = 0; t && i < t.length; i++) t[i] && (t[i].__ = n, l = S(t[i], l, u));
    return l;
  }
  return n.__e != l && (u.insertBefore(n.__e, l || null), l = n.__e), l && l.nextSibling;
}
function $(n, l) {
  return l = l || [], null == n || "boolean" == typeof n || (h(n) ? n.some(function (n) {
    $(n, l);
  }) : l.push(n)), l;
}
function H(n, l, u, t) {
  var i = n.key,
    o = n.type,
    r = u - 1,
    f = u + 1,
    e = l[u];
  if (null === e || e && i == e.key && o === e.type) return u;
  if (t > (null != e && 0 == (131072 & e.__u) ? 1 : 0)) for (; r >= 0 || f < l.length;) {
    if (r >= 0) {
      if ((e = l[r]) && 0 == (131072 & e.__u) && i == e.key && o === e.type) return r;
      r--;
    }
    if (f < l.length) {
      if ((e = l[f]) && 0 == (131072 & e.__u) && i == e.key && o === e.type) return f;
      f++;
    }
  }
  return -1;
}
function I(n, l, u) {
  "-" === l[0] ? n.setProperty(l, null == u ? "" : u) : n[l] = null == u ? "" : "number" != typeof u || a.test(l) ? u : u + "px";
}
function T(n, l, u, t, i) {
  var o;
  n: if ("style" === l) {
    if ("string" == typeof u) n.style.cssText = u;else {
      if ("string" == typeof t && (n.style.cssText = t = ""), t) for (l in t) u && l in u || I(n.style, l, "");
      if (u) for (l in u) t && u[l] === t[l] || I(n.style, l, u[l]);
    }
  } else if ("o" === l[0] && "n" === l[1]) o = l !== (l = l.replace(/(PointerCapture)$|Capture$/, "$1")), l = l.toLowerCase() in n ? l.toLowerCase().slice(2) : l.slice(2), n.l || (n.l = {}), n.l[l + o] = u, u ? t ? u.u = t.u : (u.u = Date.now(), n.addEventListener(l, o ? D : A, o)) : n.removeEventListener(l, o ? D : A, o);else {
    if (i) l = l.replace(/xlink(H|:h)/, "h").replace(/sName$/, "s");else if ("width" !== l && "height" !== l && "href" !== l && "list" !== l && "form" !== l && "tabIndex" !== l && "download" !== l && "rowSpan" !== l && "colSpan" !== l && "role" !== l && l in n) try {
      n[l] = null == u ? "" : u;
      break n;
    } catch (n) {}
    "function" == typeof u || (null == u || !1 === u && "-" !== l[4] ? n.removeAttribute(l) : n.setAttribute(l, u));
  }
}
function A(n) {
  var u = this.l[n.type + !1];
  if (n.t) {
    if (n.t <= u.u) return;
  } else n.t = Date.now();
  return u(preact_module_l.event ? preact_module_l.event(n) : n);
}
function D(n) {
  return this.l[n.type + !0](preact_module_l.event ? preact_module_l.event(n) : n);
}
function L(n, u, t, i, o, r, f, e, c, s) {
  var a,
    p,
    y,
    d,
    _,
    m,
    k,
    w,
    x,
    P,
    S,
    $,
    H,
    I,
    T,
    A = u.type;
  if (void 0 !== u.constructor) return null;
  128 & t.__u && (c = !!(32 & t.__u), r = [e = u.__e = t.__e]), (a = preact_module_l.__b) && a(u);
  n: if ("function" == typeof A) try {
    if (w = u.props, x = (a = A.contextType) && i[a.__c], P = a ? x ? x.props.value : a.__ : i, t.__c ? k = (p = u.__c = t.__c).__ = p.__E : ("prototype" in A && A.prototype.render ? u.__c = p = new A(w, P) : (u.__c = p = new b(w, P), p.constructor = A, p.render = O), x && x.sub(p), p.props = w, p.state || (p.state = {}), p.context = P, p.__n = i, y = p.__d = !0, p.__h = [], p._sb = []), null == p.__s && (p.__s = p.state), null != A.getDerivedStateFromProps && (p.__s == p.state && (p.__s = v({}, p.__s)), v(p.__s, A.getDerivedStateFromProps(w, p.__s))), d = p.props, _ = p.state, p.__v = u, y) null == A.getDerivedStateFromProps && null != p.componentWillMount && p.componentWillMount(), null != p.componentDidMount && p.__h.push(p.componentDidMount);else {
      if (null == A.getDerivedStateFromProps && w !== d && null != p.componentWillReceiveProps && p.componentWillReceiveProps(w, P), !p.__e && (null != p.shouldComponentUpdate && !1 === p.shouldComponentUpdate(w, p.__s, P) || u.__v === t.__v)) {
        for (u.__v !== t.__v && (p.props = w, p.state = p.__s, p.__d = !1), u.__e = t.__e, u.__k = t.__k, u.__k.forEach(function (n) {
          n && (n.__ = u);
        }), S = 0; S < p._sb.length; S++) p.__h.push(p._sb[S]);
        p._sb = [], p.__h.length && f.push(p);
        break n;
      }
      null != p.componentWillUpdate && p.componentWillUpdate(w, p.__s, P), null != p.componentDidUpdate && p.__h.push(function () {
        p.componentDidUpdate(d, _, m);
      });
    }
    if (p.context = P, p.props = w, p.__P = n, p.__e = !1, $ = preact_module_l.__r, H = 0, "prototype" in A && A.prototype.render) {
      for (p.state = p.__s, p.__d = !1, $ && $(u), a = p.render(p.props, p.state, p.context), I = 0; I < p._sb.length; I++) p.__h.push(p._sb[I]);
      p._sb = [];
    } else do {
      p.__d = !1, $ && $(u), a = p.render(p.props, p.state, p.context), p.state = p.__s;
    } while (p.__d && ++H < 25);
    p.state = p.__s, null != p.getChildContext && (i = v(v({}, i), p.getChildContext())), y || null == p.getSnapshotBeforeUpdate || (m = p.getSnapshotBeforeUpdate(d, _)), C(n, h(T = null != a && a.type === g && null == a.key ? a.props.children : a) ? T : [T], u, t, i, o, r, f, e, c, s), p.base = u.__e, u.__u &= -161, p.__h.length && f.push(p), k && (p.__E = p.__ = null);
  } catch (n) {
    u.__v = null, c || null != r ? (u.__e = e, u.__u |= c ? 160 : 32, r[r.indexOf(e)] = null) : (u.__e = t.__e, u.__k = t.__k), preact_module_l.__e(n, u, t);
  } else null == r && u.__v === t.__v ? (u.__k = t.__k, u.__e = t.__e) : u.__e = j(t.__e, u, t, i, o, r, f, c, s);
  (a = preact_module_l.diffed) && a(u);
}
function M(n, u, t) {
  u.__d = void 0;
  for (var i = 0; i < t.length; i++) z(t[i], t[++i], t[++i]);
  preact_module_l.__c && preact_module_l.__c(u, n), n.some(function (u) {
    try {
      n = u.__h, u.__h = [], n.some(function (n) {
        n.call(u);
      });
    } catch (n) {
      preact_module_l.__e(n, u.__v);
    }
  });
}
function j(l, u, t, i, o, r, f, e, s) {
  var a,
    v,
    y,
    d,
    _,
    g,
    b,
    k = t.props,
    w = u.props,
    x = u.type;
  if ("svg" === x && (o = !0), null != r) for (a = 0; a < r.length; a++) if ((_ = r[a]) && "setAttribute" in _ == !!x && (x ? _.localName === x : 3 === _.nodeType)) {
    l = _, r[a] = null;
    break;
  }
  if (null == l) {
    if (null === x) return document.createTextNode(w);
    l = o ? document.createElementNS("http://www.w3.org/2000/svg", x) : document.createElement(x, w.is && w), r = null, e = !1;
  }
  if (null === x) k === w || e && l.data === w || (l.data = w);else {
    if (r = r && preact_module_n.call(l.childNodes), k = t.props || preact_module_c, !e && null != r) for (k = {}, a = 0; a < l.attributes.length; a++) k[(_ = l.attributes[a]).name] = _.value;
    for (a in k) _ = k[a], "children" == a || ("dangerouslySetInnerHTML" == a ? y = _ : "key" === a || a in w || T(l, a, null, _, o));
    for (a in w) _ = w[a], "children" == a ? d = _ : "dangerouslySetInnerHTML" == a ? v = _ : "value" == a ? g = _ : "checked" == a ? b = _ : "key" === a || e && "function" != typeof _ || k[a] === _ || T(l, a, _, k[a], o);
    if (v) e || y && (v.__html === y.__html || v.__html === l.innerHTML) || (l.innerHTML = v.__html), u.__k = [];else if (y && (l.innerHTML = ""), C(l, h(d) ? d : [d], u, t, i, o && "foreignObject" !== x, r, f, r ? r[0] : t.__k && m(t, 0), e, s), null != r) for (a = r.length; a--;) null != r[a] && p(r[a]);
    e || (a = "value", void 0 !== g && (g !== l[a] || "progress" === x && !g || "option" === x && g !== k[a]) && T(l, a, g, k[a], !1), a = "checked", void 0 !== b && b !== l[a] && T(l, a, b, k[a], !1));
  }
  return l;
}
function z(n, u, t) {
  try {
    "function" == typeof n ? n(u) : n.current = u;
  } catch (n) {
    preact_module_l.__e(n, t);
  }
}
function N(n, u, t) {
  var i, o;
  if (preact_module_l.unmount && preact_module_l.unmount(n), (i = n.ref) && (i.current && i.current !== n.__e || z(i, null, u)), null != (i = n.__c)) {
    if (i.componentWillUnmount) try {
      i.componentWillUnmount();
    } catch (n) {
      preact_module_l.__e(n, u);
    }
    i.base = i.__P = null, n.__c = void 0;
  }
  if (i = n.__k) for (o = 0; o < i.length; o++) i[o] && N(i[o], u, t || "function" != typeof n.type);
  t || null == n.__e || p(n.__e), n.__ = n.__e = n.__d = void 0;
}
function O(n, l, u) {
  return this.constructor(n, u);
}
function q(u, t, i) {
  var o, r, f, e;
  preact_module_l.__ && preact_module_l.__(u, t), r = (o = "function" == typeof i) ? null : i && i.__k || t.__k, f = [], e = [], L(t, u = (!o && i || t).__k = y(g, null, [u]), r || preact_module_c, preact_module_c, void 0 !== t.ownerSVGElement, !o && i ? [i] : r ? null : t.firstChild ? preact_module_n.call(t.childNodes) : null, f, !o && i ? i : r ? r.__e : t.firstChild, o, e), M(f, u, e);
}
function B(n, l) {
  q(n, l, B);
}
function E(l, u, t) {
  var i,
    o,
    r,
    f,
    e = v({}, l.props);
  for (r in l.type && l.type.defaultProps && (f = l.type.defaultProps), u) "key" == r ? i = u[r] : "ref" == r ? o = u[r] : e[r] = void 0 === u[r] && void 0 !== f ? f[r] : u[r];
  return arguments.length > 2 && (e.children = arguments.length > 3 ? preact_module_n.call(arguments, 2) : t), d(l.type, e, i || l.key, o || l.ref, null);
}
function F(n, l) {
  var u = {
    __c: l = "__cC" + preact_module_e++,
    __: n,
    Consumer: function (n, l) {
      return n.children(l);
    },
    Provider: function (n) {
      var u, t;
      return this.getChildContext || (u = [], (t = {})[l] = this, this.getChildContext = function () {
        return t;
      }, this.shouldComponentUpdate = function (n) {
        this.props.value !== n.value && u.some(function (n) {
          n.__e = !0, w(n);
        });
      }, this.sub = function (n) {
        u.push(n);
        var l = n.componentWillUnmount;
        n.componentWillUnmount = function () {
          u.splice(u.indexOf(n), 1), l && l.call(n);
        };
      }), n.children;
    }
  };
  return u.Provider.__ = u.Consumer.contextType = u;
}
preact_module_n = s.slice, preact_module_l = {
  __e: function (n, l, u, t) {
    for (var i, o, r; l = l.__;) if ((i = l.__c) && !i.__) try {
      if ((o = i.constructor) && null != o.getDerivedStateFromError && (i.setState(o.getDerivedStateFromError(n)), r = i.__d), null != i.componentDidCatch && (i.componentDidCatch(n, t || {}), r = i.__d), r) return i.__E = i;
    } catch (l) {
      n = l;
    }
    throw n;
  }
}, preact_module_u = 0, preact_module_t = function (n) {
  return null != n && null == n.constructor;
}, b.prototype.setState = function (n, l) {
  var u;
  u = null != this.__s && this.__s !== this.state ? this.__s : this.__s = v({}, this.state), "function" == typeof n && (n = n(v({}, u), this.props)), n && v(u, n), null != n && this.__v && (l && this._sb.push(l), w(this));
}, b.prototype.forceUpdate = function (n) {
  this.__v && (this.__e = !0, n && this.__h.push(n), w(this));
}, b.prototype.render = g, i = [], preact_module_r = "function" == typeof Promise ? Promise.prototype.then.bind(Promise.resolve()) : setTimeout, preact_module_f = function (n, l) {
  return n.__v.__b - l.__v.__b;
}, x.__r = 0, preact_module_e = 0;

;// CONCATENATED MODULE: ./node_modules/preact/hooks/dist/hooks.module.js

var hooks_module_t,
  hooks_module_r,
  hooks_module_u,
  hooks_module_i,
  hooks_module_o = 0,
  hooks_module_f = [],
  hooks_module_c = [],
  hooks_module_e = preact_module_l.__b,
  hooks_module_a = preact_module_l.__r,
  hooks_module_v = preact_module_l.diffed,
  l = preact_module_l.__c,
  hooks_module_m = preact_module_l.unmount;
function hooks_module_d(t, u) {
  preact_module_l.__h && preact_module_l.__h(hooks_module_r, t, hooks_module_o || u), hooks_module_o = 0;
  var i = hooks_module_r.__H || (hooks_module_r.__H = {
    __: [],
    __h: []
  });
  return t >= i.__.length && i.__.push({
    __V: hooks_module_c
  }), i.__[t];
}
function hooks_module_h(n) {
  return hooks_module_o = 1, hooks_module_s(hooks_module_B, n);
}
function hooks_module_s(n, u, i) {
  var o = hooks_module_d(hooks_module_t++, 2);
  if (o.t = n, !o.__c && (o.__ = [i ? i(u) : hooks_module_B(void 0, u), function (n) {
    var t = o.__N ? o.__N[0] : o.__[0],
      r = o.t(t, n);
    t !== r && (o.__N = [r, o.__[1]], o.__c.setState({}));
  }], o.__c = hooks_module_r, !hooks_module_r.u)) {
    var f = function (n, t, r) {
      if (!o.__c.__H) return !0;
      var u = o.__c.__H.__.filter(function (n) {
        return n.__c;
      });
      if (u.every(function (n) {
        return !n.__N;
      })) return !c || c.call(this, n, t, r);
      var i = !1;
      return u.forEach(function (n) {
        if (n.__N) {
          var t = n.__[0];
          n.__ = n.__N, n.__N = void 0, t !== n.__[0] && (i = !0);
        }
      }), !(!i && o.__c.props === n) && (!c || c.call(this, n, t, r));
    };
    hooks_module_r.u = !0;
    var c = hooks_module_r.shouldComponentUpdate,
      e = hooks_module_r.componentWillUpdate;
    hooks_module_r.componentWillUpdate = function (n, t, r) {
      if (this.__e) {
        var u = c;
        c = void 0, f(n, t, r), c = u;
      }
      e && e.call(this, n, t, r);
    }, hooks_module_r.shouldComponentUpdate = f;
  }
  return o.__N || o.__;
}
function hooks_module_p(u, i) {
  var o = hooks_module_d(hooks_module_t++, 3);
  !preact_module_l.__s && hooks_module_z(o.__H, i) && (o.__ = u, o.i = i, hooks_module_r.__H.__h.push(o));
}
function hooks_module_y(u, i) {
  var o = hooks_module_d(hooks_module_t++, 4);
  !preact_module_l.__s && hooks_module_z(o.__H, i) && (o.__ = u, o.i = i, hooks_module_r.__h.push(o));
}
function hooks_module_(n) {
  return hooks_module_o = 5, hooks_module_F(function () {
    return {
      current: n
    };
  }, []);
}
function hooks_module_A(n, t, r) {
  hooks_module_o = 6, hooks_module_y(function () {
    return "function" == typeof n ? (n(t()), function () {
      return n(null);
    }) : n ? (n.current = t(), function () {
      return n.current = null;
    }) : void 0;
  }, null == r ? r : r.concat(n));
}
function hooks_module_F(n, r) {
  var u = hooks_module_d(hooks_module_t++, 7);
  return hooks_module_z(u.__H, r) ? (u.__V = n(), u.i = r, u.__h = n, u.__V) : u.__;
}
function hooks_module_T(n, t) {
  return hooks_module_o = 8, hooks_module_F(function () {
    return n;
  }, t);
}
function hooks_module_q(n) {
  var u = hooks_module_r.context[n.__c],
    i = hooks_module_d(hooks_module_t++, 9);
  return i.c = n, u ? (null == i.__ && (i.__ = !0, u.sub(hooks_module_r)), u.props.value) : n.__;
}
function hooks_module_x(t, r) {
  n.useDebugValue && n.useDebugValue(r ? r(t) : t);
}
function hooks_module_P(n) {
  var u = hooks_module_d(hooks_module_t++, 10),
    i = hooks_module_h();
  return u.__ = n, hooks_module_r.componentDidCatch || (hooks_module_r.componentDidCatch = function (n, t) {
    u.__ && u.__(n, t), i[1](n);
  }), [i[0], function () {
    i[1](void 0);
  }];
}
function V() {
  var n = hooks_module_d(hooks_module_t++, 11);
  if (!n.__) {
    for (var u = hooks_module_r.__v; null !== u && !u.__m && null !== u.__;) u = u.__;
    var i = u.__m || (u.__m = [0, 0]);
    n.__ = "P" + i[0] + "-" + i[1]++;
  }
  return n.__;
}
function hooks_module_b() {
  for (var t; t = hooks_module_f.shift();) if (t.__P && t.__H) try {
    t.__H.__h.forEach(hooks_module_k), t.__H.__h.forEach(hooks_module_w), t.__H.__h = [];
  } catch (r) {
    t.__H.__h = [], preact_module_l.__e(r, t.__v);
  }
}
preact_module_l.__b = function (n) {
  hooks_module_r = null, hooks_module_e && hooks_module_e(n);
}, preact_module_l.__r = function (n) {
  hooks_module_a && hooks_module_a(n), hooks_module_t = 0;
  var i = (hooks_module_r = n.__c).__H;
  i && (hooks_module_u === hooks_module_r ? (i.__h = [], hooks_module_r.__h = [], i.__.forEach(function (n) {
    n.__N && (n.__ = n.__N), n.__V = hooks_module_c, n.__N = n.i = void 0;
  })) : (i.__h.forEach(hooks_module_k), i.__h.forEach(hooks_module_w), i.__h = [], hooks_module_t = 0)), hooks_module_u = hooks_module_r;
}, preact_module_l.diffed = function (t) {
  hooks_module_v && hooks_module_v(t);
  var o = t.__c;
  o && o.__H && (o.__H.__h.length && (1 !== hooks_module_f.push(o) && hooks_module_i === preact_module_l.requestAnimationFrame || ((hooks_module_i = preact_module_l.requestAnimationFrame) || hooks_module_j)(hooks_module_b)), o.__H.__.forEach(function (n) {
    n.i && (n.__H = n.i), n.__V !== hooks_module_c && (n.__ = n.__V), n.i = void 0, n.__V = hooks_module_c;
  })), hooks_module_u = hooks_module_r = null;
}, preact_module_l.__c = function (t, r) {
  r.some(function (t) {
    try {
      t.__h.forEach(hooks_module_k), t.__h = t.__h.filter(function (n) {
        return !n.__ || hooks_module_w(n);
      });
    } catch (u) {
      r.some(function (n) {
        n.__h && (n.__h = []);
      }), r = [], preact_module_l.__e(u, t.__v);
    }
  }), l && l(t, r);
}, preact_module_l.unmount = function (t) {
  hooks_module_m && hooks_module_m(t);
  var r,
    u = t.__c;
  u && u.__H && (u.__H.__.forEach(function (n) {
    try {
      hooks_module_k(n);
    } catch (n) {
      r = n;
    }
  }), u.__H = void 0, r && preact_module_l.__e(r, u.__v));
};
var hooks_module_g = "function" == typeof requestAnimationFrame;
function hooks_module_j(n) {
  var t,
    r = function () {
      clearTimeout(u), hooks_module_g && cancelAnimationFrame(t), setTimeout(n);
    },
    u = setTimeout(r, 100);
  hooks_module_g && (t = requestAnimationFrame(r));
}
function hooks_module_k(n) {
  var t = hooks_module_r,
    u = n.__c;
  "function" == typeof u && (n.__c = void 0, u()), hooks_module_r = t;
}
function hooks_module_w(n) {
  var t = hooks_module_r;
  n.__c = n.__(), hooks_module_r = t;
}
function hooks_module_z(n, t) {
  return !n || n.length !== t.length || t.some(function (t, r) {
    return t !== n[r];
  });
}
function hooks_module_B(n, t) {
  return "function" == typeof t ? t(n) : t;
}

;// CONCATENATED MODULE: ./node_modules/@preact/signals-core/dist/signals-core.module.js
function signals_core_module_i() {
  throw new Error("Cycle detected");
}
var signals_core_module_t = Symbol.for("preact-signals");
function signals_core_module_r() {
  if (!(signals_core_module_v > 1)) {
    var i,
      t = !1;
    while (void 0 !== signals_core_module_f) {
      var r = signals_core_module_f;
      signals_core_module_f = void 0;
      signals_core_module_e++;
      while (void 0 !== r) {
        var n = r.o;
        r.o = void 0;
        r.f &= -3;
        if (!(8 & r.f) && signals_core_module_l(r)) try {
          r.c();
        } catch (r) {
          if (!t) {
            i = r;
            t = !0;
          }
        }
        r = n;
      }
    }
    signals_core_module_e = 0;
    signals_core_module_v--;
    if (t) throw i;
  } else signals_core_module_v--;
}
function signals_core_module_n(i) {
  if (signals_core_module_v > 0) return i();
  signals_core_module_v++;
  try {
    return i();
  } finally {
    signals_core_module_r();
  }
}
var signals_core_module_o = void 0,
  signals_core_module_h = 0;
function signals_core_module_s(i) {
  if (signals_core_module_h > 0) return i();
  var t = signals_core_module_o;
  signals_core_module_o = void 0;
  signals_core_module_h++;
  try {
    return i();
  } finally {
    signals_core_module_h--;
    signals_core_module_o = t;
  }
}
var signals_core_module_f = void 0,
  signals_core_module_v = 0,
  signals_core_module_e = 0,
  signals_core_module_u = 0;
function signals_core_module_c(i) {
  if (void 0 !== signals_core_module_o) {
    var t = i.n;
    if (void 0 === t || t.t !== signals_core_module_o) {
      t = {
        i: 0,
        S: i,
        p: signals_core_module_o.s,
        n: void 0,
        t: signals_core_module_o,
        e: void 0,
        x: void 0,
        r: t
      };
      if (void 0 !== signals_core_module_o.s) signals_core_module_o.s.n = t;
      signals_core_module_o.s = t;
      i.n = t;
      if (32 & signals_core_module_o.f) i.S(t);
      return t;
    } else if (-1 === t.i) {
      t.i = 0;
      if (void 0 !== t.n) {
        t.n.p = t.p;
        if (void 0 !== t.p) t.p.n = t.n;
        t.p = signals_core_module_o.s;
        t.n = void 0;
        signals_core_module_o.s.n = t;
        signals_core_module_o.s = t;
      }
      return t;
    }
  }
}
function signals_core_module_d(i) {
  this.v = i;
  this.i = 0;
  this.n = void 0;
  this.t = void 0;
}
signals_core_module_d.prototype.brand = signals_core_module_t;
signals_core_module_d.prototype.h = function () {
  return !0;
};
signals_core_module_d.prototype.S = function (i) {
  if (this.t !== i && void 0 === i.e) {
    i.x = this.t;
    if (void 0 !== this.t) this.t.e = i;
    this.t = i;
  }
};
signals_core_module_d.prototype.U = function (i) {
  if (void 0 !== this.t) {
    var t = i.e,
      r = i.x;
    if (void 0 !== t) {
      t.x = r;
      i.e = void 0;
    }
    if (void 0 !== r) {
      r.e = t;
      i.x = void 0;
    }
    if (i === this.t) this.t = r;
  }
};
signals_core_module_d.prototype.subscribe = function (i) {
  var t = this;
  return signals_core_module_O(function () {
    var r = t.value,
      n = 32 & this.f;
    this.f &= -33;
    try {
      i(r);
    } finally {
      this.f |= n;
    }
  });
};
signals_core_module_d.prototype.valueOf = function () {
  return this.value;
};
signals_core_module_d.prototype.toString = function () {
  return this.value + "";
};
signals_core_module_d.prototype.toJSON = function () {
  return this.value;
};
signals_core_module_d.prototype.peek = function () {
  return this.v;
};
Object.defineProperty(signals_core_module_d.prototype, "value", {
  get: function () {
    var i = signals_core_module_c(this);
    if (void 0 !== i) i.i = this.i;
    return this.v;
  },
  set: function (t) {
    if (signals_core_module_o instanceof signals_core_module_) !function () {
      throw new Error("Computed cannot have side-effects");
    }();
    if (t !== this.v) {
      if (signals_core_module_e > 100) signals_core_module_i();
      this.v = t;
      this.i++;
      signals_core_module_u++;
      signals_core_module_v++;
      try {
        for (var n = this.t; void 0 !== n; n = n.x) n.t.N();
      } finally {
        signals_core_module_r();
      }
    }
  }
});
function signals_core_module_a(i) {
  return new signals_core_module_d(i);
}
function signals_core_module_l(i) {
  for (var t = i.s; void 0 !== t; t = t.n) if (t.S.i !== t.i || !t.S.h() || t.S.i !== t.i) return !0;
  return !1;
}
function signals_core_module_y(i) {
  for (var t = i.s; void 0 !== t; t = t.n) {
    var r = t.S.n;
    if (void 0 !== r) t.r = r;
    t.S.n = t;
    t.i = -1;
    if (void 0 === t.n) {
      i.s = t;
      break;
    }
  }
}
function signals_core_module_w(i) {
  var t = i.s,
    r = void 0;
  while (void 0 !== t) {
    var n = t.p;
    if (-1 === t.i) {
      t.S.U(t);
      if (void 0 !== n) n.n = t.n;
      if (void 0 !== t.n) t.n.p = n;
    } else r = t;
    t.S.n = t.r;
    if (void 0 !== t.r) t.r = void 0;
    t = n;
  }
  i.s = r;
}
function signals_core_module_(i) {
  signals_core_module_d.call(this, void 0);
  this.x = i;
  this.s = void 0;
  this.g = signals_core_module_u - 1;
  this.f = 4;
}
(signals_core_module_.prototype = new signals_core_module_d()).h = function () {
  this.f &= -3;
  if (1 & this.f) return !1;
  if (32 == (36 & this.f)) return !0;
  this.f &= -5;
  if (this.g === signals_core_module_u) return !0;
  this.g = signals_core_module_u;
  this.f |= 1;
  if (this.i > 0 && !signals_core_module_l(this)) {
    this.f &= -2;
    return !0;
  }
  var i = signals_core_module_o;
  try {
    signals_core_module_y(this);
    signals_core_module_o = this;
    var t = this.x();
    if (16 & this.f || this.v !== t || 0 === this.i) {
      this.v = t;
      this.f &= -17;
      this.i++;
    }
  } catch (i) {
    this.v = i;
    this.f |= 16;
    this.i++;
  }
  signals_core_module_o = i;
  signals_core_module_w(this);
  this.f &= -2;
  return !0;
};
signals_core_module_.prototype.S = function (i) {
  if (void 0 === this.t) {
    this.f |= 36;
    for (var t = this.s; void 0 !== t; t = t.n) t.S.S(t);
  }
  signals_core_module_d.prototype.S.call(this, i);
};
signals_core_module_.prototype.U = function (i) {
  if (void 0 !== this.t) {
    signals_core_module_d.prototype.U.call(this, i);
    if (void 0 === this.t) {
      this.f &= -33;
      for (var t = this.s; void 0 !== t; t = t.n) t.S.U(t);
    }
  }
};
signals_core_module_.prototype.N = function () {
  if (!(2 & this.f)) {
    this.f |= 6;
    for (var i = this.t; void 0 !== i; i = i.x) i.t.N();
  }
};
signals_core_module_.prototype.peek = function () {
  if (!this.h()) signals_core_module_i();
  if (16 & this.f) throw this.v;
  return this.v;
};
Object.defineProperty(signals_core_module_.prototype, "value", {
  get: function () {
    if (1 & this.f) signals_core_module_i();
    var t = signals_core_module_c(this);
    this.h();
    if (void 0 !== t) t.i = this.i;
    if (16 & this.f) throw this.v;
    return this.v;
  }
});
function signals_core_module_p(i) {
  return new signals_core_module_(i);
}
function signals_core_module_g(i) {
  var t = i.u;
  i.u = void 0;
  if ("function" == typeof t) {
    signals_core_module_v++;
    var n = signals_core_module_o;
    signals_core_module_o = void 0;
    try {
      t();
    } catch (t) {
      i.f &= -2;
      i.f |= 8;
      signals_core_module_b(i);
      throw t;
    } finally {
      signals_core_module_o = n;
      signals_core_module_r();
    }
  }
}
function signals_core_module_b(i) {
  for (var t = i.s; void 0 !== t; t = t.n) t.S.U(t);
  i.x = void 0;
  i.s = void 0;
  signals_core_module_g(i);
}
function signals_core_module_x(i) {
  if (signals_core_module_o !== this) throw new Error("Out-of-order effect");
  signals_core_module_w(this);
  signals_core_module_o = i;
  this.f &= -2;
  if (8 & this.f) signals_core_module_b(this);
  signals_core_module_r();
}
function signals_core_module_E(i) {
  this.x = i;
  this.u = void 0;
  this.s = void 0;
  this.o = void 0;
  this.f = 32;
}
signals_core_module_E.prototype.c = function () {
  var i = this.S();
  try {
    if (8 & this.f) return;
    if (void 0 === this.x) return;
    var t = this.x();
    if ("function" == typeof t) this.u = t;
  } finally {
    i();
  }
};
signals_core_module_E.prototype.S = function () {
  if (1 & this.f) signals_core_module_i();
  this.f |= 1;
  this.f &= -9;
  signals_core_module_g(this);
  signals_core_module_y(this);
  signals_core_module_v++;
  var t = signals_core_module_o;
  signals_core_module_o = this;
  return signals_core_module_x.bind(this, t);
};
signals_core_module_E.prototype.N = function () {
  if (!(2 & this.f)) {
    this.f |= 2;
    this.o = signals_core_module_f;
    signals_core_module_f = this;
  }
};
signals_core_module_E.prototype.d = function () {
  this.f |= 8;
  if (!(1 & this.f)) signals_core_module_b(this);
};
function signals_core_module_O(i) {
  var t = new signals_core_module_E(i);
  try {
    t.c();
  } catch (i) {
    t.d();
    throw i;
  }
  return t.d.bind(t);
}

;// CONCATENATED MODULE: ./node_modules/@preact/signals/dist/signals.module.js




var signals_module_v, signals_module_s;
function signals_module_l(n, i) {
  preact_module_l[n] = i.bind(null, preact_module_l[n] || function () {});
}
function signals_module_d(n) {
  if (signals_module_s) signals_module_s();
  signals_module_s = n && n.S();
}
function signals_module_p(n) {
  var r = this,
    f = n.data,
    o = useSignal(f);
  o.value = f;
  var e = hooks_module_F(function () {
    var n = r.__v;
    while (n = n.__) if (n.__c) {
      n.__c.__$f |= 4;
      break;
    }
    r.__$u.c = function () {
      var n;
      if (!preact_module_t(e.peek()) && 3 === (null == (n = r.base) ? void 0 : n.nodeType)) r.base.data = e.peek();else {
        r.__$f |= 1;
        r.setState({});
      }
    };
    return signals_core_module_p(function () {
      var n = o.value.value;
      return 0 === n ? 0 : !0 === n ? "" : n || "";
    });
  }, []);
  return e.value;
}
signals_module_p.displayName = "_st";
Object.defineProperties(signals_core_module_d.prototype, {
  constructor: {
    configurable: !0,
    value: void 0
  },
  type: {
    configurable: !0,
    value: signals_module_p
  },
  props: {
    configurable: !0,
    get: function () {
      return {
        data: this
      };
    }
  },
  __b: {
    configurable: !0,
    value: 1
  }
});
signals_module_l("__b", function (n, r) {
  if ("string" == typeof r.type) {
    var i,
      t = r.props;
    for (var f in t) if ("children" !== f) {
      var o = t[f];
      if (o instanceof signals_core_module_d) {
        if (!i) r.__np = i = {};
        i[f] = o;
        t[f] = o.peek();
      }
    }
  }
  n(r);
});
signals_module_l("__r", function (n, r) {
  signals_module_d();
  var i,
    t = r.__c;
  if (t) {
    t.__$f &= -2;
    if (void 0 === (i = t.__$u)) t.__$u = i = function (n) {
      var r;
      signals_core_module_O(function () {
        r = this;
      });
      r.c = function () {
        t.__$f |= 1;
        t.setState({});
      };
      return r;
    }();
  }
  signals_module_v = t;
  signals_module_d(i);
  n(r);
});
signals_module_l("__e", function (n, r, i, t) {
  signals_module_d();
  signals_module_v = void 0;
  n(r, i, t);
});
signals_module_l("diffed", function (n, r) {
  signals_module_d();
  signals_module_v = void 0;
  var i;
  if ("string" == typeof r.type && (i = r.__e)) {
    var t = r.__np,
      f = r.props;
    if (t) {
      var o = i.U;
      if (o) for (var e in o) {
        var u = o[e];
        if (void 0 !== u && !(e in t)) {
          u.d();
          o[e] = void 0;
        }
      } else i.U = o = {};
      for (var a in t) {
        var c = o[a],
          s = t[a];
        if (void 0 === c) {
          c = signals_module_(i, a, s, f);
          o[a] = c;
        } else c.o(s, f);
      }
    }
  }
  n(r);
});
function signals_module_(n, r, i, t) {
  var f = r in n && void 0 === n.ownerSVGElement,
    o = signals_core_module_a(i);
  return {
    o: function (n, r) {
      o.value = n;
      t = r;
    },
    d: signals_core_module_O(function () {
      var i = o.value.value;
      if (t[r] !== i) {
        t[r] = i;
        if (f) n[r] = i;else if (i) n.setAttribute(r, i);else n.removeAttribute(r);
      }
    })
  };
}
signals_module_l("unmount", function (n, r) {
  if ("string" == typeof r.type) {
    var i = r.__e;
    if (i) {
      var t = i.U;
      if (t) {
        i.U = void 0;
        for (var f in t) {
          var o = t[f];
          if (o) o.d();
        }
      }
    }
  } else {
    var e = r.__c;
    if (e) {
      var u = e.__$u;
      if (u) {
        e.__$u = void 0;
        u.d();
      }
    }
  }
  n(r);
});
signals_module_l("__h", function (n, r, i, t) {
  if (t < 3 || 9 === t) r.__$f |= 2;
  n(r, i, t);
});
b.prototype.shouldComponentUpdate = function (n, r) {
  var i = this.__$u;
  if (!(i && void 0 !== i.s || 4 & this.__$f)) return !0;
  if (3 & this.__$f) return !0;
  for (var t in r) return !0;
  for (var f in n) if ("__source" !== f && n[f] !== this.props[f]) return !0;
  for (var o in this.props) if (!(o in n)) return !0;
  return !1;
};
function useSignal(n) {
  return hooks_module_F(function () {
    return signals_core_module_a(n);
  }, []);
}
function useComputed(n) {
  var r = f(n);
  r.current = n;
  signals_module_v.__$f |= 4;
  return t(function () {
    return u(function () {
      return r.current();
    });
  }, []);
}
function useSignalEffect(n) {
  var r = f(n);
  r.current = n;
  o(function () {
    return c(function () {
      return r.current();
    });
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/deepsignal/dist/deepsignal.module.js



var deepsignal_module_a = new WeakMap(),
  deepsignal_module_o = new WeakMap(),
  deepsignal_module_s = new WeakMap(),
  deepsignal_module_c = new WeakSet(),
  deepsignal_module_u = new WeakMap(),
  deepsignal_module_i = /^\$/,
  deepsignal_module_f = !1,
  deepsignal_module_l = function (e) {
    if (!deepsignal_module_d(e)) throw new Error("This object can't be observed.");
    return deepsignal_module_o.has(e) || deepsignal_module_o.set(e, deepsignal_module_h(e, deepsignal_module_v)), deepsignal_module_o.get(e);
  },
  deepsignal_module_g = function (e, t) {
    deepsignal_module_f = !0;
    var r = e[t];
    try {
      deepsignal_module_f = !1;
    } catch (e) {}
    return r;
  },
  deepsignal_module_h = function (e, t) {
    var r = new Proxy(e, t);
    return deepsignal_module_c.add(r), r;
  },
  deepsignal_module_p = function () {
    throw new Error("Don't mutate the signals directly.");
  },
  deepsignal_module_y = function (e) {
    return function (t, c, u) {
      var l;
      if (deepsignal_module_f) return Reflect.get(t, c, u);
      var g = e || "$" === c[0];
      if (!e && g && Array.isArray(t)) {
        if ("$" === c) return deepsignal_module_s.has(t) || deepsignal_module_s.set(t, deepsignal_module_h(t, deepsignal_module_w)), deepsignal_module_s.get(t);
        g = "$length" === c;
      }
      deepsignal_module_a.has(u) || deepsignal_module_a.set(u, new Map());
      var p = deepsignal_module_a.get(u),
        y = g ? c.replace(deepsignal_module_i, "") : c;
      if (p.has(y) || "function" != typeof (null == (l = Object.getOwnPropertyDescriptor(t, y)) ? void 0 : l.get)) {
        var b = Reflect.get(t, y, u);
        if (g && "function" == typeof b) return;
        if ("symbol" == typeof y && deepsignal_module_m.has(y)) return b;
        p.has(y) || (deepsignal_module_d(b) && (deepsignal_module_o.has(b) || deepsignal_module_o.set(b, deepsignal_module_h(b, deepsignal_module_v)), b = deepsignal_module_o.get(b)), p.set(y, signals_core_module_a(b)));
      } else p.set(y, signals_core_module_p(function () {
        return Reflect.get(t, y, u);
      }));
      return g ? p.get(y) : p.get(y).value;
    };
  },
  deepsignal_module_v = {
    get: deepsignal_module_y(!1),
    set: function (e, n, s, c) {
      deepsignal_module_a.has(c) || deepsignal_module_a.set(c, new Map());
      var f = deepsignal_module_a.get(c);
      if ("$" === n[0]) {
        s instanceof signals_core_module_d || deepsignal_module_p();
        var l = n.replace(deepsignal_module_i, "");
        return f.set(l, s), Reflect.set(e, l, s.peek(), c);
      }
      var g = s;
      deepsignal_module_d(s) && (deepsignal_module_o.has(s) || deepsignal_module_o.set(s, deepsignal_module_h(s, deepsignal_module_v)), g = deepsignal_module_o.get(s));
      var y = !(n in e),
        w = Reflect.set(e, n, s, c);
      return f.has(n) ? f.get(n).value = g : f.set(n, signals_core_module_a(g)), y && deepsignal_module_u.has(e) && deepsignal_module_u.get(e).value++, Array.isArray(e) && f.has("length") && (f.get("length").value = e.length), w;
    },
    deleteProperty: function (e, t) {
      "$" === t[0] && deepsignal_module_p();
      var r = deepsignal_module_a.get(deepsignal_module_o.get(e)),
        n = Reflect.deleteProperty(e, t);
      return r && r.has(t) && (r.get(t).value = void 0), deepsignal_module_u.has(e) && deepsignal_module_u.get(e).value++, n;
    },
    ownKeys: function (e) {
      return deepsignal_module_u.has(e) || deepsignal_module_u.set(e, signals_core_module_a(0)), deepsignal_module_u._ = deepsignal_module_u.get(e).value, Reflect.ownKeys(e);
    }
  },
  deepsignal_module_w = {
    get: deepsignal_module_y(!0),
    set: deepsignal_module_p,
    deleteProperty: deepsignal_module_p
  },
  deepsignal_module_m = new Set(Object.getOwnPropertyNames(Symbol).map(function (e) {
    return Symbol[e];
  }).filter(function (e) {
    return "symbol" == typeof e;
  })),
  deepsignal_module_b = new Set([Object, Array]),
  deepsignal_module_d = function (e) {
    return "object" == typeof e && null !== e && (!("function" == typeof e.constructor && e.constructor.name in globalThis && globalThis[e.constructor.name] === e.constructor) || deepsignal_module_b.has(e.constructor)) && !deepsignal_module_c.has(e);
  },
  deepsignal_module_k = function (t) {
    return e(function () {
      return deepsignal_module_l(t);
    }, []);
  };

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/portals.js
/**
 * External dependencies
 */


/**
 * @param {import('../../src/index').RenderableProps<{ context: any }>} props
 */
function ContextProvider(props) {
  this.getChildContext = () => props.context;
  return props.children;
}

/**
 * Portal component
 *
 * @this {import('./internal').Component}
 * @param {object | null | undefined} props
 *
 *                                          TODO: use createRoot() instead of fake root
 */
function Portal(props) {
  const _this = this;
  const container = props._container;
  _this.componentWillUnmount = function () {
    q(null, _this._temp);
    _this._temp = null;
    _this._container = null;
  };

  // When we change container we should clear our old container and
  // indicate a new mount.
  if (_this._container && _this._container !== container) {
    _this.componentWillUnmount();
  }

  // When props.vnode is undefined/false/null we are dealing with some kind of
  // conditional vnode. This should not trigger a render.
  if (props._vnode) {
    if (!_this._temp) {
      _this._container = container;

      // Create a fake DOM parent node that manages a subset of `container`'s children:
      _this._temp = {
        nodeType: 1,
        parentNode: container,
        childNodes: [],
        appendChild(child) {
          this.childNodes.push(child);
          _this._container.appendChild(child);
        },
        insertBefore(child) {
          this.childNodes.push(child);
          _this._container.appendChild(child);
        },
        removeChild(child) {
          this.childNodes.splice(
          // eslint-disable-next-line no-bitwise
          this.childNodes.indexOf(child) >>> 1, 1);
          _this._container.removeChild(child);
        }
      };
    }

    // Render our wrapping element into temp.
    q(y(ContextProvider, {
      context: _this.context
    }, props._vnode), _this._temp);
  }
  // When we come from a conditional render, on a mounted
  // portal we should clear the DOM.
  else if (_this._temp) {
    _this.componentWillUnmount();
  }
}

/**
 * Create a `Portal` to continue rendering the vnode tree at a different DOM node
 *
 * @param {import('./internal').VNode}         vnode     The vnode to render
 * @param {import('./internal').PreactElement} container The DOM node to continue rendering in to.
 */
function createPortal(vnode, container) {
  const el = y(Portal, {
    _vnode: vnode,
    _container: container
  });
  el.containerInfo = container;
  return el;
}
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/utils.js
/**
 * External dependencies
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
  hooks_module_p(() => {
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
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/store.js
/**
 * External dependencies
 */

const isObject = item => item && typeof item === 'object' && !Array.isArray(item);
const deepMerge = (target, source) => {
  if (isObject(target) && isObject(source)) {
    for (const key in source) {
      if (isObject(source[key])) {
        if (!target[key]) Object.assign(target, {
          [key]: {}
        });
        deepMerge(target[key], source[key]);
      } else {
        Object.assign(target, {
          [key]: source[key]
        });
      }
    }
  }
};
const getSerializedState = () => {
  const storeTag = document.querySelector(`script[type="application/json"]#wp-interactivity-store-data`);
  if (!storeTag) return {};
  try {
    const {
      state
    } = JSON.parse(storeTag.textContent);
    if (isObject(state)) return state;
    throw Error('Parsed state is not an object');
  } catch (e) {
    // eslint-disable-next-line no-console
    console.log(e);
  }
  return {};
};
const afterLoads = new Set();
const rawState = getSerializedState();
const rawStore = {
  state: deepsignal_module_l(rawState)
};

/**
 * @typedef StoreProps Properties object passed to `store`.
 * @property {Object} state State to be added to the global store. All the
 *                          properties included here become reactive.
 */

/**
 * @typedef StoreOptions Options object.
 * @property {(store:any) => void} [afterLoad] Callback to be executed after the
 *                                             Interactivity API has been set up
 *                                             and the store is ready. It
 *                                             receives the store as argument.
 */

/**
 * Extends the Interactivity API global store with the passed properties.
 *
 * These props typically consist of `state`, which is reactive, and other
 * properties like `selectors`, `actions`, `effects`, etc. which can store
 * callbacks and derived state. These props can then be referenced by any
 * directive to make the HTML interactive.
 *
 * @example
 * ```js
 *  store({
 *    state: {
 *      counter: { value: 0 },
 *    },
 *    actions: {
 *      counter: {
 *        increment: ({ state }) => {
 *          state.counter.value += 1;
 *        },
 *      },
 *    },
 *  });
 * ```
 *
 * The code from the example above allows blocks to subscribe and interact with
 * the store by using directives in the HTML, e.g.:
 *
 * ```html
 * <div data-wp-interactive>
 *   <button
 *     data-wp-text="state.counter.value"
 *     data-wp-on--click="actions.counter.increment"
 *   >
 *     0
 *   </button>
 * </div>
 * ```
 *
 * @param {StoreProps}   properties Properties to be added to the global store.
 * @param {StoreOptions} [options]  Options passed to the `store` call.
 */
const store = ({
  state,
  ...block
}, {
  afterLoad
} = {}) => {
  deepMerge(rawStore, block);
  deepMerge(rawState, state);
  if (afterLoad) afterLoads.add(afterLoad);
};
;// CONCATENATED MODULE: ./node_modules/preact/jsx-runtime/dist/jsxRuntime.module.js


var jsxRuntime_module_t = /["&<]/;
function jsxRuntime_module_n(r) {
  if (0 === r.length || !1 === jsxRuntime_module_t.test(r)) return r;
  for (var e = 0, n = 0, o = "", f = ""; n < r.length; n++) {
    switch (r.charCodeAt(n)) {
      case 34:
        f = "&quot;";
        break;
      case 38:
        f = "&amp;";
        break;
      case 60:
        f = "&lt;";
        break;
      default:
        continue;
    }
    n !== e && (o += r.slice(e, n)), o += f, e = n + 1;
  }
  return n !== e && (o += r.slice(e, n)), o;
}
var jsxRuntime_module_o = /acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i,
  jsxRuntime_module_f = 0,
  jsxRuntime_module_i = Array.isArray;
function jsxRuntime_module_u(e, t, n, o, i, u) {
  var a,
    c,
    p = {};
  for (c in t) "ref" == c ? a = t[c] : p[c] = t[c];
  var l = {
    type: e,
    props: p,
    key: n,
    ref: a,
    __k: null,
    __: null,
    __b: 0,
    __e: null,
    __d: void 0,
    __c: null,
    constructor: void 0,
    __v: --jsxRuntime_module_f,
    __i: -1,
    __u: 0,
    __source: i,
    __self: u
  };
  if ("function" == typeof e && (a = e.defaultProps)) for (c in a) void 0 === p[c] && (p[c] = a[c]);
  return preact_module_l.vnode && preact_module_l.vnode(l), l;
}
function jsxRuntime_module_a(r) {
  var t = jsxRuntime_module_u(e, {
    tpl: r,
    exprs: [].slice.call(arguments, 1)
  });
  return t.key = t.__v, t;
}
var jsxRuntime_module_c = {},
  jsxRuntime_module_p = /[A-Z]/g;
function jsxRuntime_module_l(e, t) {
  if (r.attr) {
    var f = r.attr(e, t);
    if ("string" == typeof f) return f;
  }
  if ("ref" === e || "key" === e) return "";
  if ("style" === e && "object" == typeof t) {
    var i = "";
    for (var u in t) {
      var a = t[u];
      if (null != a && "" !== a) {
        var l = "-" == u[0] ? u : jsxRuntime_module_c[u] || (jsxRuntime_module_c[u] = u.replace(jsxRuntime_module_p, "-$&").toLowerCase()),
          _ = ";";
        "number" != typeof a || l.startsWith("--") || jsxRuntime_module_o.test(l) || (_ = "px;"), i = i + l + ":" + a + _;
      }
    }
    return e + '="' + i + '"';
  }
  return null == t || !1 === t || "function" == typeof t || "object" == typeof t ? "" : !0 === t ? e : e + '="' + jsxRuntime_module_n(t) + '"';
}
function jsxRuntime_module_(r) {
  if (null == r || "boolean" == typeof r || "function" == typeof r) return null;
  if ("object" == typeof r) {
    if (void 0 === r.constructor) return r;
    if (jsxRuntime_module_i(r)) {
      for (var e = 0; e < r.length; e++) r[e] = jsxRuntime_module_(r[e]);
      return r;
    }
  }
  return jsxRuntime_module_n("" + r);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/hooks.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/** @typedef {import('preact').VNode} VNode */
/** @typedef {typeof context} Context */
/** @typedef {ReturnType<typeof getEvaluate>} Evaluate */

/**
 * @typedef {Object} DirectiveCallbackParams Callback parameters.
 * @property {Object}   directives Object map with the defined directives of the element being evaluated.
 * @property {Object}   props      Props present in the current element.
 * @property {VNode}    element    Virtual node representing the original element.
 * @property {Context}  context    The inherited context.
 * @property {Evaluate} evaluate   Function that resolves a given path to a value either in the store or the context.
 */

/**
 * @callback DirectiveCallback Callback that runs the directive logic.
 * @param {DirectiveCallbackParams} params Callback parameters.
 */

/**
 * @typedef DirectiveOptions Options object.
 * @property {number} [priority=10] Value that specifies the priority to
 *                                  evaluate directives of this type. Lower
 *                                  numbers correspond with earlier execution.
 *                                  Default is `10`.
 */

// Main context.

const context = F({});

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
 *   ( { directives: { alert }, element, evaluate }) => {
 *     element.props.onclick = () => {
 *       alert( evaluate( alert.default ) );
 *     }
 *   }
 * )
 * ```
 *
 * The previous code registers a custom directive type for displaying an alert
 * message whenever an element using it is clicked. The message text is obtained
 * from the store using `evaluate`.
 *
 * When the HTML is processed by the Interactivity API, any element containing
 * the `data-wp-alert` directive will have the `onclick` event handler, e.g.,
 *
 * ```html
 * <button data-wp-alert="state.messages.alert">Click me!</button>
 * ```
 * Note that, in the previous example, you access `alert.default` in order to
 * retrieve the `state.messages.alert` value passed to the directive. You can
 * also define custom names by appending `--` to the directive attribute,
 * followed by a suffix, like in the following HTML snippet:
 *
 * ```html
 * <button
 *   data-wp-color--text="state.theme.text"
 *   data-wp-color--background="state.theme.background"
 * >Click me!</button>
 * ```
 *
 * This could be an hypothetical implementation of the custom directive used in
 * the snippet above.
 *
 * @example
 * ```js
 * directive(
 *   'color', // Name without prefix and suffix.
 *   ( { directives: { color }, ref, evaluate }) => {
 *     if ( color.text ) {
 * 	     ref.style.setProperty(
 *         'color',
 *         evaluate( color.text )
 *       );
 *     }
 *     if ( color.background ) {
 *       ref.style.setProperty(
 *         'background-color',
 *         evaluate( color.background )
 *       );
 *     }
 *   }
 * )
 * ```
 *
 * @param {string}            name     Directive name, without the `data-wp-` prefix.
 * @param {DirectiveCallback} callback Function that runs the directive logic.
 * @param {DirectiveOptions=} options  Options object.
 */
const directive = (name, callback, {
  priority = 10
} = {}) => {
  directiveCallbacks[name] = callback;
  directivePriorities[name] = priority;
};

// Resolve the path to some property of the store object.
const resolve = (path, ctx) => {
  let current = {
    ...rawStore,
    context: ctx
  };
  path.split('.').forEach(p => current = current[p]);
  return current;
};

// Generate the evaluate function.
const getEvaluate = ({
  ref
} = {}) => (path, extraArgs = {}) => {
  // If path starts with !, remove it and save a flag.
  const hasNegationOperator = path[0] === '!' && !!(path = path.slice(1));
  const value = resolve(path, extraArgs.context);
  const returnValue = typeof value === 'function' ? value({
    ref: ref.current,
    ...rawStore,
    ...extraArgs
  }) : value;
  return hasNegationOperator ? !returnValue : returnValue;
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
  return Object.entries(byPriority).sort(([p1], [p2]) => p1 - p2).map(([, arr]) => arr);
};

// Priority level wrapper.
const Directives = ({
  directives,
  priorityLevels: [currentPriorityLevel, ...nextPriorityLevels],
  element,
  evaluate,
  originalProps,
  elemRef
}) => {
  // Initialize the DOM reference.
  // eslint-disable-next-line react-hooks/rules-of-hooks
  elemRef = elemRef || hooks_module_(null);

  // Create a reference to the evaluate function using the DOM reference.
  // eslint-disable-next-line react-hooks/rules-of-hooks, react-hooks/exhaustive-deps
  evaluate = evaluate || hooks_module_T(getEvaluate({
    ref: elemRef
  }), []);

  // Create a fresh copy of the vnode element.
  element = E(element, {
    ref: elemRef
  });

  // Recursively render the wrapper for the next priority level.
  const children = nextPriorityLevels.length > 0 ? jsxRuntime_module_u(Directives, {
    directives: directives,
    priorityLevels: nextPriorityLevels,
    element: element,
    evaluate: evaluate,
    originalProps: originalProps,
    elemRef: elemRef
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
    evaluate
  };
  for (const directiveName of currentPriorityLevel) {
    const wrapper = directiveCallbacks[directiveName]?.(directiveArgs);
    if (wrapper !== undefined) props.children = wrapper;
  }
  return props.children;
};

// Preact Options Hook called each time a vnode is created.
const old = preact_module_l.vnode;
preact_module_l.vnode = vnode => {
  if (vnode.props.__directives) {
    const props = vnode.props;
    const directives = props.__directives;
    if (directives.key) vnode.key = directives.key.default;
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
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/slots.js
/**
 * External dependencies
 */




const slotsContext = F();
const Fill = ({
  slot,
  children
}) => {
  const slots = hooks_module_q(slotsContext);
  hooks_module_p(() => {
    if (slot) {
      slots.value = {
        ...slots.value,
        [slot]: children
      };
      return () => {
        slots.value = {
          ...slots.value,
          [slot]: null
        };
      };
    }
  }, [slots, slot, children]);
  return !!slot ? null : children;
};
const SlotProvider = ({
  children
}) => {
  return (
    // TODO: We can change this to use deepsignal once this PR is merged.
    // https://github.com/luisherranz/deepsignal/pull/38
    jsxRuntime_module_u(slotsContext.Provider, {
      value: signals_core_module_a({}),
      children: children
    })
  );
};
const Slot = ({
  name,
  children
}) => {
  const slots = hooks_module_q(slotsContext);
  return slots.value[name] || children;
};
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/directives.js
/**
 * External dependencies
 */



/**
 * Internal dependencies
 */







const directives_isObject = item => item && typeof item === 'object' && !Array.isArray(item);
const mergeDeepSignals = (target, source, overwrite) => {
  for (const k in source) {
    if (directives_isObject(deepsignal_module_g(target, k)) && directives_isObject(deepsignal_module_g(source, k))) {
      mergeDeepSignals(target[`$${k}`].peek(), source[`$${k}`].peek(), overwrite);
    } else if (overwrite || typeof deepsignal_module_g(target, k) === 'undefined') {
      target[`$${k}`] = source[`$${k}`];
    }
  }
};
/* harmony default export */ var directives = (() => {
  // data-wp-context
  directive('context', ({
    directives: {
      context: {
        default: newContext
      }
    },
    props: {
      children
    },
    context: inheritedContext
  }) => {
    const {
      Provider
    } = inheritedContext;
    const inheritedValue = hooks_module_q(inheritedContext);
    const currentValue = hooks_module_(deepsignal_module_l({}));
    currentValue.current = hooks_module_F(() => {
      const newValue = deepsignal_module_l(newContext);
      mergeDeepSignals(newValue, inheritedValue);
      mergeDeepSignals(currentValue.current, newValue, true);
      return currentValue.current;
    }, [newContext, inheritedValue]);
    return jsxRuntime_module_u(Provider, {
      value: currentValue.current,
      children: children
    });
  }, {
    priority: 5
  });

  // data-wp-body
  directive('body', ({
    props: {
      children
    }
  }) => {
    return createPortal(children, document.body);
  });

  // data-wp-effect--[name]
  directive('effect', ({
    directives: {
      effect
    },
    context,
    evaluate
  }) => {
    const contextValue = hooks_module_q(context);
    Object.values(effect).forEach(path => {
      utils_useSignalEffect(() => {
        return evaluate(path, {
          context: contextValue
        });
      });
    });
  });

  // data-wp-init--[name]
  directive('init', ({
    directives: {
      init
    },
    context,
    evaluate
  }) => {
    const contextValue = hooks_module_q(context);
    Object.values(init).forEach(path => {
      hooks_module_p(() => {
        return evaluate(path, {
          context: contextValue
        });
      }, []);
    });
  });

  // data-wp-on--[event]
  directive('on', ({
    directives: {
      on
    },
    element,
    evaluate,
    context
  }) => {
    const contextValue = hooks_module_q(context);
    Object.entries(on).forEach(([name, path]) => {
      element.props[`on${name}`] = event => {
        evaluate(path, {
          event,
          context: contextValue
        });
      };
    });
  });

  // data-wp-class--[classname]
  directive('class', ({
    directives: {
      class: className
    },
    element,
    evaluate,
    context
  }) => {
    const contextValue = hooks_module_q(context);
    Object.keys(className).filter(n => n !== 'default').forEach(name => {
      const result = evaluate(className[name], {
        className: name,
        context: contextValue
      });
      const currentClass = element.props.class || '';
      const classFinder = new RegExp(`(^|\\s)${name}(\\s|$)`, 'g');
      if (!result) element.props.class = currentClass.replace(classFinder, ' ').trim();else if (!classFinder.test(currentClass)) element.props.class = currentClass ? `${currentClass} ${name}` : name;
      hooks_module_p(() => {
        // This seems necessary because Preact doesn't change the class
        // names on the hydration, so we have to do it manually. It doesn't
        // need deps because it only needs to do it the first time.
        if (!result) {
          element.ref.current.classList.remove(name);
        } else {
          element.ref.current.classList.add(name);
        }
      }, []);
    });
  });
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

  // data-wp-style--[style-key]
  directive('style', ({
    directives: {
      style
    },
    element,
    evaluate,
    context
  }) => {
    const contextValue = hooks_module_q(context);
    Object.keys(style).filter(n => n !== 'default').forEach(key => {
      const result = evaluate(style[key], {
        key,
        context: contextValue
      });
      element.props.style = element.props.style || {};
      if (typeof element.props.style === 'string') element.props.style = cssStringToObject(element.props.style);
      if (!result) delete element.props.style[key];else element.props.style[key] = result;
      hooks_module_p(() => {
        // This seems necessary because Preact doesn't change the styles on
        // the hydration, so we have to do it manually. It doesn't need deps
        // because it only needs to do it the first time.
        if (!result) {
          element.ref.current.style.removeProperty(key);
        } else {
          element.ref.current.style[key] = result;
        }
      }, []);
    });
  });

  // data-wp-bind--[attribute]
  directive('bind', ({
    directives: {
      bind
    },
    element,
    context,
    evaluate
  }) => {
    const contextValue = hooks_module_q(context);
    Object.entries(bind).filter(n => n !== 'default').forEach(([attribute, path]) => {
      const result = evaluate(path, {
        context: contextValue
      });
      element.props[attribute] = result;
      // Preact doesn't handle the `role` attribute properly, as it doesn't remove it when `null`.
      // We need this workaround until the following issue is solved:
      // https://github.com/preactjs/preact/issues/4136
      hooks_module_y(() => {
        if (attribute === 'role' && (result === null || result === undefined)) {
          element.ref.current.removeAttribute(attribute);
        }
      }, [attribute, result]);

      // This seems necessary because Preact doesn't change the attributes
      // on the hydration, so we have to do it manually. It doesn't need
      // deps because it only needs to do it the first time.
      hooks_module_p(() => {
        const el = element.ref.current;

        // We set the value directly to the corresponding
        // HTMLElement instance property excluding the following
        // special cases.
        // We follow Preact's logic: https://github.com/preactjs/preact/blob/ea49f7a0f9d1ff2c98c0bdd66aa0cbc583055246/src/diff/props.js#L110-L129
        if (attribute !== 'width' && attribute !== 'height' && attribute !== 'href' && attribute !== 'list' && attribute !== 'form' &&
        // Default value in browsers is `-1` and an empty string is
        // cast to `0` instead
        attribute !== 'tabIndex' && attribute !== 'download' && attribute !== 'rowSpan' && attribute !== 'colSpan' && attribute !== 'role' && attribute in el) {
          try {
            el[attribute] = result === null || result === undefined ? '' : result;
            return;
          } catch (err) {}
        }
        // aria- and data- attributes have no boolean representation.
        // A `false` value is different from the attribute not being
        // present, so we can't remove it.
        // We follow Preact's logic: https://github.com/preactjs/preact/blob/ea49f7a0f9d1ff2c98c0bdd66aa0cbc583055246/src/diff/props.js#L131C24-L136
        if (result !== null && result !== undefined && (result !== false || attribute[4] === '-')) {
          el.setAttribute(attribute, result);
        } else {
          el.removeAttribute(attribute);
        }
      }, []);
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
    const cached = hooks_module_F(() => innerHTML, []);
    return jsxRuntime_module_u(Type, {
      dangerouslySetInnerHTML: {
        __html: cached
      },
      ...rest
    });
  });

  // data-wp-text
  directive('text', ({
    directives: {
      text: {
        default: text
      }
    },
    element,
    evaluate,
    context
  }) => {
    const contextValue = hooks_module_q(context);
    element.props.children = evaluate(text, {
      context: contextValue
    });
  });

  // data-wp-slot
  directive('slot', ({
    directives: {
      slot: {
        default: slot
      }
    },
    props: {
      children
    },
    element
  }) => {
    const name = typeof slot === 'string' ? slot : slot.name;
    const position = slot.position || 'children';
    if (position === 'before') {
      return jsxRuntime_module_u(g, {
        children: [jsxRuntime_module_u(Slot, {
          name: name
        }), children]
      });
    }
    if (position === 'after') {
      return jsxRuntime_module_u(g, {
        children: [children, jsxRuntime_module_u(Slot, {
          name: name
        })]
      });
    }
    if (position === 'replace') {
      return jsxRuntime_module_u(Slot, {
        name: name,
        children: children
      });
    }
    if (position === 'children') {
      element.props.children = jsxRuntime_module_u(Slot, {
        name: name,
        children: element.props.children
      });
    }
  }, {
    priority: 4
  });

  // data-wp-fill
  directive('fill', ({
    directives: {
      fill: {
        default: fill
      }
    },
    props: {
      children
    },
    evaluate,
    context
  }) => {
    const contextValue = hooks_module_q(context);
    const slot = evaluate(fill, {
      context: contextValue
    });
    return jsxRuntime_module_u(Fill, {
      slot: slot,
      children: children
    });
  }, {
    priority: 4
  });

  // data-wp-slot-provider
  directive('slot-provider', ({
    props: {
      children
    }
  }) => jsxRuntime_module_u(SlotProvider, {
    children: children
  }), {
    priority: 4
  });
});
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/constants.js
const directivePrefix = 'wp';
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/vdom.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */

const ignoreAttr = `data-${directivePrefix}-ignore`;
const islandAttr = `data-${directivePrefix}-interactive`;
const fullPrefix = `data-${directivePrefix}-`;

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

const hydratedIslands = new WeakSet();

// Recursive function that transforms a DOM tree into vDOM.
function toVdom(root) {
  const treeWalker = document.createTreeWalker(root, 205 // ELEMENT + TEXT + COMMENT + CDATA_SECTION + PROCESSING_INSTRUCTION
  );

  function walk(node) {
    const {
      attributes,
      nodeType
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
    const directives = {};
    let hasDirectives = false;
    let ignore = false;
    let island = false;
    for (let i = 0; i < attributes.length; i++) {
      const n = attributes[i].name;
      if (n[fullPrefix.length] && n.slice(0, fullPrefix.length) === fullPrefix) {
        if (n === ignoreAttr) {
          ignore = true;
        } else if (n === islandAttr) {
          island = true;
        } else {
          hasDirectives = true;
          let val = attributes[i].value;
          try {
            val = JSON.parse(val);
          } catch (e) {}
          const [, prefix, suffix] = directiveParser.exec(n);
          directives[prefix] = directives[prefix] || {};
          directives[prefix][suffix || 'default'] = val;
        }
      } else if (n === 'ref') {
        continue;
      }
      props[n] = attributes[i].value;
    }
    if (ignore && !island) return [y(node.localName, {
      ...props,
      innerHTML: node.innerHTML,
      __directives: {
        ignore: true
      }
    })];
    if (island) hydratedIslands.add(node);
    if (hasDirectives) props.__directives = directives;
    let child = treeWalker.firstChild();
    if (child) {
      while (child) {
        const [vnode, nextChild] = walk(child);
        if (vnode) children.push(vnode);
        child = nextChild || treeWalker.nextSibling();
      }
      treeWalker.parentNode();
    }
    return [y(node.localName, props, children)];
  }
  return walk(treeWalker.currentNode);
}
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/router.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */




// The cache of visited and prefetched pages.
const pages = new Map();

// Keep the same root fragment for each interactive region node.
const regionRootFragments = new WeakMap();
const getRegionRootFragment = region => {
  if (!regionRootFragments.has(region)) {
    regionRootFragments.set(region, createRootFragment(region.parentElement, region));
  }
  return regionRootFragments.get(region);
};

// Helper to remove domain and hash from the URL. We are only interesting in
// caching the path and the query.
const cleanUrl = url => {
  const u = new URL(url, window.location);
  return u.pathname + u.search;
};

// Fetch a new page and convert it to a static virtual DOM.
const fetchPage = async (url, {
  html
}) => {
  try {
    if (!html) {
      const res = await window.fetch(url);
      if (res.status !== 200) return false;
      html = await res.text();
    }
    const dom = new window.DOMParser().parseFromString(html, 'text/html');
    return regionsToVdom(dom);
  } catch (e) {
    return false;
  }
};

// Return an object with VDOM trees of those HTML regions marked with a
// `navigation-id` directive.
const regionsToVdom = dom => {
  const regions = {};
  const attrName = `data-${directivePrefix}-navigation-id`;
  dom.querySelectorAll(`[${attrName}]`).forEach(region => {
    const id = region.getAttribute(attrName);
    regions[id] = toVdom(region);
  });
  const title = dom.querySelector('title')?.innerText;
  return {
    regions,
    title
  };
};

// Prefetch a page. We store the promise to avoid triggering a second fetch for
// a page if a fetching has already started.
const prefetch = (url, options = {}) => {
  url = cleanUrl(url);
  if (options.force || !pages.has(url)) {
    pages.set(url, fetchPage(url, options));
  }
};

// Render all interactive regions contained in the given page.
const renderRegions = page => {
  const attrName = `data-${directivePrefix}-navigation-id`;
  document.querySelectorAll(`[${attrName}]`).forEach(region => {
    const id = region.getAttribute(attrName);
    const fragment = getRegionRootFragment(region);
    q(page.regions[id], fragment);
  });
  if (page.title) {
    document.title = page.title;
  }
};

// Variable to store the current navigation.
let navigatingTo = '';

// Navigate to a new page.
const router_navigate = async (href, options = {}) => {
  const url = cleanUrl(href);
  navigatingTo = href;
  prefetch(url, options);

  // Create a promise that resolves when the specified timeout ends. The
  // timeout value is 10 seconds by default.
  const timeoutPromise = new Promise(resolve => setTimeout(resolve, options.timeout ?? 10000));
  const page = await Promise.race([pages.get(url), timeoutPromise]);

  // Once the page is fetched, the destination URL could have changed (e.g.,
  // by clicking another link in the meantime). If so, bail out, and let the
  // newer execution to update the HTML.
  if (navigatingTo !== href) return;
  if (page) {
    renderRegions(page);
    window.history[options.replace ? 'replaceState' : 'pushState']({}, '', href);
  } else {
    window.location.assign(href);
    await new Promise(() => {});
  }
};

// Listen to the back and forward buttons and restore the page if it's in the
// cache.
window.addEventListener('popstate', async () => {
  const url = cleanUrl(window.location); // Remove hash.
  const page = pages.has(url) && (await pages.get(url));
  if (page) {
    renderRegions(page);
  } else {
    window.location.reload();
  }
});

// Initialize the router with the initial DOM.
const init = async () => {
  document.querySelectorAll(`[data-${directivePrefix}-interactive]`).forEach(node => {
    if (!hydratedIslands.has(node)) {
      const fragment = getRegionRootFragment(node);
      const vdom = toVdom(node);
      B(vdom, fragment);
    }
  });

  // Cache the current regions.
  pages.set(cleanUrl(window.location), Promise.resolve(regionsToVdom(document)));
};
;// CONCATENATED MODULE: ./node_modules/@wordpress/interactivity/src/index.js
/**
 * Internal dependencies
 */









document.addEventListener('DOMContentLoaded', async () => {
  directives();
  await init();
  afterLoads.forEach(afterLoad => afterLoad(rawStore));
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	!function() {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = function(result, chunkIds, fn, priority) {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every(function(key) { return __webpack_require__.O[key](chunkIds[j]); })) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			440: 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = function(chunkId) { return installedChunks[chunkId] === 0; };
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some(function(id) { return installedChunks[id] !== 0; })) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["__WordPressPrivateInteractivityAPI__"] = self["__WordPressPrivateInteractivityAPI__"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	
/******/ })()
;