var __defProp = Object.defineProperty;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __esm = (fn, res) => function __init() {
  return fn && (res = (0, fn[__getOwnPropNames(fn)[0]])(fn = 0)), res;
};
var __export = (target, all) => {
  for (var name in all)
    __defProp(target, name, { get: all[name], enumerable: true });
};

// node_modules/preact/dist/preact.module.js
function d(n3, l6) {
  for (var u5 in l6) n3[u5] = l6[u5];
  return n3;
}
function w(n3) {
  n3 && n3.parentNode && n3.parentNode.removeChild(n3);
}
function _(l6, u5, t5) {
  var i6, o4, r5, f5 = {};
  for (r5 in u5) "key" == r5 ? i6 = u5[r5] : "ref" == r5 ? o4 = u5[r5] : f5[r5] = u5[r5];
  if (arguments.length > 2 && (f5.children = arguments.length > 3 ? n.call(arguments, 2) : t5), "function" == typeof l6 && null != l6.defaultProps) for (r5 in l6.defaultProps) void 0 === f5[r5] && (f5[r5] = l6.defaultProps[r5]);
  return g(l6, f5, i6, o4, null);
}
function g(n3, t5, i6, o4, r5) {
  var f5 = { type: n3, props: t5, key: i6, ref: o4, __k: null, __: null, __b: 0, __e: null, __d: void 0, __c: null, constructor: void 0, __v: null == r5 ? ++u : r5, __i: -1, __u: 0 };
  return null == r5 && null != l.vnode && l.vnode(f5), f5;
}
function b(n3) {
  return n3.children;
}
function k(n3, l6) {
  this.props = n3, this.context = l6;
}
function x(n3, l6) {
  if (null == l6) return n3.__ ? x(n3.__, n3.__i + 1) : null;
  for (var u5; l6 < n3.__k.length; l6++) if (null != (u5 = n3.__k[l6]) && null != u5.__e) return u5.__e;
  return "function" == typeof n3.type ? x(n3) : null;
}
function C(n3) {
  var l6, u5;
  if (null != (n3 = n3.__) && null != n3.__c) {
    for (n3.__e = n3.__c.base = null, l6 = 0; l6 < n3.__k.length; l6++) if (null != (u5 = n3.__k[l6]) && null != u5.__e) {
      n3.__e = n3.__c.base = u5.__e;
      break;
    }
    return C(n3);
  }
}
function M(n3) {
  (!n3.__d && (n3.__d = true) && i.push(n3) && !P.__r++ || o !== l.debounceRendering) && ((o = l.debounceRendering) || r)(P);
}
function P() {
  var n3, u5, t5, o4, r5, e4, c5, s6;
  for (i.sort(f); n3 = i.shift(); ) n3.__d && (u5 = i.length, o4 = void 0, e4 = (r5 = (t5 = n3).__v).__e, c5 = [], s6 = [], t5.__P && ((o4 = d({}, r5)).__v = r5.__v + 1, l.vnode && l.vnode(o4), O(t5.__P, o4, r5, t5.__n, t5.__P.namespaceURI, 32 & r5.__u ? [e4] : null, c5, null == e4 ? x(r5) : e4, !!(32 & r5.__u), s6), o4.__v = r5.__v, o4.__.__k[o4.__i] = o4, j(c5, o4, s6), o4.__e != e4 && C(o4)), i.length > u5 && i.sort(f));
  P.__r = 0;
}
function S(n3, l6, u5, t5, i6, o4, r5, f5, e4, c5, s6) {
  var a5, p6, y5, d6, w5, _5 = t5 && t5.__k || v, g3 = l6.length;
  for (u5.__d = e4, $(u5, l6, _5), e4 = u5.__d, a5 = 0; a5 < g3; a5++) null != (y5 = u5.__k[a5]) && (p6 = -1 === y5.__i ? h : _5[y5.__i] || h, y5.__i = a5, O(n3, y5, p6, i6, o4, r5, f5, e4, c5, s6), d6 = y5.__e, y5.ref && p6.ref != y5.ref && (p6.ref && N(p6.ref, null, y5), s6.push(y5.ref, y5.__c || d6, y5)), null == w5 && null != d6 && (w5 = d6), 65536 & y5.__u || p6.__k === y5.__k ? e4 = I(y5, e4, n3) : "function" == typeof y5.type && void 0 !== y5.__d ? e4 = y5.__d : d6 && (e4 = d6.nextSibling), y5.__d = void 0, y5.__u &= -196609);
  u5.__d = e4, u5.__e = w5;
}
function $(n3, l6, u5) {
  var t5, i6, o4, r5, f5, e4 = l6.length, c5 = u5.length, s6 = c5, a5 = 0;
  for (n3.__k = [], t5 = 0; t5 < e4; t5++) null != (i6 = l6[t5]) && "boolean" != typeof i6 && "function" != typeof i6 ? (r5 = t5 + a5, (i6 = n3.__k[t5] = "string" == typeof i6 || "number" == typeof i6 || "bigint" == typeof i6 || i6.constructor == String ? g(null, i6, null, null, null) : y(i6) ? g(b, { children: i6 }, null, null, null) : void 0 === i6.constructor && i6.__b > 0 ? g(i6.type, i6.props, i6.key, i6.ref ? i6.ref : null, i6.__v) : i6).__ = n3, i6.__b = n3.__b + 1, o4 = null, -1 !== (f5 = i6.__i = L(i6, u5, r5, s6)) && (s6--, (o4 = u5[f5]) && (o4.__u |= 131072)), null == o4 || null === o4.__v ? (-1 == f5 && a5--, "function" != typeof i6.type && (i6.__u |= 65536)) : f5 !== r5 && (f5 == r5 - 1 ? a5-- : f5 == r5 + 1 ? a5++ : (f5 > r5 ? a5-- : a5++, i6.__u |= 65536))) : i6 = n3.__k[t5] = null;
  if (s6) for (t5 = 0; t5 < c5; t5++) null != (o4 = u5[t5]) && 0 == (131072 & o4.__u) && (o4.__e == n3.__d && (n3.__d = x(o4)), V(o4, o4));
}
function I(n3, l6, u5) {
  var t5, i6;
  if ("function" == typeof n3.type) {
    for (t5 = n3.__k, i6 = 0; t5 && i6 < t5.length; i6++) t5[i6] && (t5[i6].__ = n3, l6 = I(t5[i6], l6, u5));
    return l6;
  }
  n3.__e != l6 && (l6 && n3.type && !u5.contains(l6) && (l6 = x(n3)), u5.insertBefore(n3.__e, l6 || null), l6 = n3.__e);
  do {
    l6 = l6 && l6.nextSibling;
  } while (null != l6 && 8 === l6.nodeType);
  return l6;
}
function L(n3, l6, u5, t5) {
  var i6 = n3.key, o4 = n3.type, r5 = u5 - 1, f5 = u5 + 1, e4 = l6[u5];
  if (null === e4 || e4 && i6 == e4.key && o4 === e4.type && 0 == (131072 & e4.__u)) return u5;
  if (t5 > (null != e4 && 0 == (131072 & e4.__u) ? 1 : 0)) for (; r5 >= 0 || f5 < l6.length; ) {
    if (r5 >= 0) {
      if ((e4 = l6[r5]) && 0 == (131072 & e4.__u) && i6 == e4.key && o4 === e4.type) return r5;
      r5--;
    }
    if (f5 < l6.length) {
      if ((e4 = l6[f5]) && 0 == (131072 & e4.__u) && i6 == e4.key && o4 === e4.type) return f5;
      f5++;
    }
  }
  return -1;
}
function T(n3, l6, u5) {
  "-" === l6[0] ? n3.setProperty(l6, null == u5 ? "" : u5) : n3[l6] = null == u5 ? "" : "number" != typeof u5 || p.test(l6) ? u5 : u5 + "px";
}
function A(n3, l6, u5, t5, i6) {
  var o4;
  n: if ("style" === l6) if ("string" == typeof u5) n3.style.cssText = u5;
  else {
    if ("string" == typeof t5 && (n3.style.cssText = t5 = ""), t5) for (l6 in t5) u5 && l6 in u5 || T(n3.style, l6, "");
    if (u5) for (l6 in u5) t5 && u5[l6] === t5[l6] || T(n3.style, l6, u5[l6]);
  }
  else if ("o" === l6[0] && "n" === l6[1]) o4 = l6 !== (l6 = l6.replace(/(PointerCapture)$|Capture$/i, "$1")), l6 = l6.toLowerCase() in n3 || "onFocusOut" === l6 || "onFocusIn" === l6 ? l6.toLowerCase().slice(2) : l6.slice(2), n3.l || (n3.l = {}), n3.l[l6 + o4] = u5, u5 ? t5 ? u5.u = t5.u : (u5.u = e, n3.addEventListener(l6, o4 ? s : c, o4)) : n3.removeEventListener(l6, o4 ? s : c, o4);
  else {
    if ("http://www.w3.org/2000/svg" == i6) l6 = l6.replace(/xlink(H|:h)/, "h").replace(/sName$/, "s");
    else if ("width" != l6 && "height" != l6 && "href" != l6 && "list" != l6 && "form" != l6 && "tabIndex" != l6 && "download" != l6 && "rowSpan" != l6 && "colSpan" != l6 && "role" != l6 && "popover" != l6 && l6 in n3) try {
      n3[l6] = null == u5 ? "" : u5;
      break n;
    } catch (n4) {
    }
    "function" == typeof u5 || (null == u5 || false === u5 && "-" !== l6[4] ? n3.removeAttribute(l6) : n3.setAttribute(l6, "popover" == l6 && 1 == u5 ? "" : u5));
  }
}
function F(n3) {
  return function(u5) {
    if (this.l) {
      var t5 = this.l[u5.type + n3];
      if (null == u5.t) u5.t = e++;
      else if (u5.t < t5.u) return;
      return t5(l.event ? l.event(u5) : u5);
    }
  };
}
function O(n3, u5, t5, i6, o4, r5, f5, e4, c5, s6) {
  var a5, h5, v6, p6, w5, _5, g3, m3, x3, C3, M2, P2, $2, I2, H, L2, T3 = u5.type;
  if (void 0 !== u5.constructor) return null;
  128 & t5.__u && (c5 = !!(32 & t5.__u), r5 = [e4 = u5.__e = t5.__e]), (a5 = l.__b) && a5(u5);
  n: if ("function" == typeof T3) try {
    if (m3 = u5.props, x3 = "prototype" in T3 && T3.prototype.render, C3 = (a5 = T3.contextType) && i6[a5.__c], M2 = a5 ? C3 ? C3.props.value : a5.__ : i6, t5.__c ? g3 = (h5 = u5.__c = t5.__c).__ = h5.__E : (x3 ? u5.__c = h5 = new T3(m3, M2) : (u5.__c = h5 = new k(m3, M2), h5.constructor = T3, h5.render = q), C3 && C3.sub(h5), h5.props = m3, h5.state || (h5.state = {}), h5.context = M2, h5.__n = i6, v6 = h5.__d = true, h5.__h = [], h5._sb = []), x3 && null == h5.__s && (h5.__s = h5.state), x3 && null != T3.getDerivedStateFromProps && (h5.__s == h5.state && (h5.__s = d({}, h5.__s)), d(h5.__s, T3.getDerivedStateFromProps(m3, h5.__s))), p6 = h5.props, w5 = h5.state, h5.__v = u5, v6) x3 && null == T3.getDerivedStateFromProps && null != h5.componentWillMount && h5.componentWillMount(), x3 && null != h5.componentDidMount && h5.__h.push(h5.componentDidMount);
    else {
      if (x3 && null == T3.getDerivedStateFromProps && m3 !== p6 && null != h5.componentWillReceiveProps && h5.componentWillReceiveProps(m3, M2), !h5.__e && (null != h5.shouldComponentUpdate && false === h5.shouldComponentUpdate(m3, h5.__s, M2) || u5.__v === t5.__v)) {
        for (u5.__v !== t5.__v && (h5.props = m3, h5.state = h5.__s, h5.__d = false), u5.__e = t5.__e, u5.__k = t5.__k, u5.__k.some(function(n4) {
          n4 && (n4.__ = u5);
        }), P2 = 0; P2 < h5._sb.length; P2++) h5.__h.push(h5._sb[P2]);
        h5._sb = [], h5.__h.length && f5.push(h5);
        break n;
      }
      null != h5.componentWillUpdate && h5.componentWillUpdate(m3, h5.__s, M2), x3 && null != h5.componentDidUpdate && h5.__h.push(function() {
        h5.componentDidUpdate(p6, w5, _5);
      });
    }
    if (h5.context = M2, h5.props = m3, h5.__P = n3, h5.__e = false, $2 = l.__r, I2 = 0, x3) {
      for (h5.state = h5.__s, h5.__d = false, $2 && $2(u5), a5 = h5.render(h5.props, h5.state, h5.context), H = 0; H < h5._sb.length; H++) h5.__h.push(h5._sb[H]);
      h5._sb = [];
    } else do {
      h5.__d = false, $2 && $2(u5), a5 = h5.render(h5.props, h5.state, h5.context), h5.state = h5.__s;
    } while (h5.__d && ++I2 < 25);
    h5.state = h5.__s, null != h5.getChildContext && (i6 = d(d({}, i6), h5.getChildContext())), x3 && !v6 && null != h5.getSnapshotBeforeUpdate && (_5 = h5.getSnapshotBeforeUpdate(p6, w5)), S(n3, y(L2 = null != a5 && a5.type === b && null == a5.key ? a5.props.children : a5) ? L2 : [L2], u5, t5, i6, o4, r5, f5, e4, c5, s6), h5.base = u5.__e, u5.__u &= -161, h5.__h.length && f5.push(h5), g3 && (h5.__E = h5.__ = null);
  } catch (n4) {
    if (u5.__v = null, c5 || null != r5) {
      for (u5.__u |= c5 ? 160 : 32; e4 && 8 === e4.nodeType && e4.nextSibling; ) e4 = e4.nextSibling;
      r5[r5.indexOf(e4)] = null, u5.__e = e4;
    } else u5.__e = t5.__e, u5.__k = t5.__k;
    l.__e(n4, u5, t5);
  }
  else null == r5 && u5.__v === t5.__v ? (u5.__k = t5.__k, u5.__e = t5.__e) : u5.__e = z(t5.__e, u5, t5, i6, o4, r5, f5, c5, s6);
  (a5 = l.diffed) && a5(u5);
}
function j(n3, u5, t5) {
  u5.__d = void 0;
  for (var i6 = 0; i6 < t5.length; i6++) N(t5[i6], t5[++i6], t5[++i6]);
  l.__c && l.__c(u5, n3), n3.some(function(u6) {
    try {
      n3 = u6.__h, u6.__h = [], n3.some(function(n4) {
        n4.call(u6);
      });
    } catch (n4) {
      l.__e(n4, u6.__v);
    }
  });
}
function z(u5, t5, i6, o4, r5, f5, e4, c5, s6) {
  var a5, v6, p6, d6, _5, g3, m3, b4 = i6.props, k3 = t5.props, C3 = t5.type;
  if ("svg" === C3 ? r5 = "http://www.w3.org/2000/svg" : "math" === C3 ? r5 = "http://www.w3.org/1998/Math/MathML" : r5 || (r5 = "http://www.w3.org/1999/xhtml"), null != f5) {
    for (a5 = 0; a5 < f5.length; a5++) if ((_5 = f5[a5]) && "setAttribute" in _5 == !!C3 && (C3 ? _5.localName === C3 : 3 === _5.nodeType)) {
      u5 = _5, f5[a5] = null;
      break;
    }
  }
  if (null == u5) {
    if (null === C3) return document.createTextNode(k3);
    u5 = document.createElementNS(r5, C3, k3.is && k3), c5 && (l.__m && l.__m(t5, f5), c5 = false), f5 = null;
  }
  if (null === C3) b4 === k3 || c5 && u5.data === k3 || (u5.data = k3);
  else {
    if (f5 = f5 && n.call(u5.childNodes), b4 = i6.props || h, !c5 && null != f5) for (b4 = {}, a5 = 0; a5 < u5.attributes.length; a5++) b4[(_5 = u5.attributes[a5]).name] = _5.value;
    for (a5 in b4) if (_5 = b4[a5], "children" == a5) ;
    else if ("dangerouslySetInnerHTML" == a5) p6 = _5;
    else if (!(a5 in k3)) {
      if ("value" == a5 && "defaultValue" in k3 || "checked" == a5 && "defaultChecked" in k3) continue;
      A(u5, a5, null, _5, r5);
    }
    for (a5 in k3) _5 = k3[a5], "children" == a5 ? d6 = _5 : "dangerouslySetInnerHTML" == a5 ? v6 = _5 : "value" == a5 ? g3 = _5 : "checked" == a5 ? m3 = _5 : c5 && "function" != typeof _5 || b4[a5] === _5 || A(u5, a5, _5, b4[a5], r5);
    if (v6) c5 || p6 && (v6.__html === p6.__html || v6.__html === u5.innerHTML) || (u5.innerHTML = v6.__html), t5.__k = [];
    else if (p6 && (u5.innerHTML = ""), S(u5, y(d6) ? d6 : [d6], t5, i6, o4, "foreignObject" === C3 ? "http://www.w3.org/1999/xhtml" : r5, f5, e4, f5 ? f5[0] : i6.__k && x(i6, 0), c5, s6), null != f5) for (a5 = f5.length; a5--; ) w(f5[a5]);
    c5 || (a5 = "value", "progress" === C3 && null == g3 ? u5.removeAttribute("value") : void 0 !== g3 && (g3 !== u5[a5] || "progress" === C3 && !g3 || "option" === C3 && g3 !== b4[a5]) && A(u5, a5, g3, b4[a5], r5), a5 = "checked", void 0 !== m3 && m3 !== u5[a5] && A(u5, a5, m3, b4[a5], r5));
  }
  return u5;
}
function N(n3, u5, t5) {
  try {
    if ("function" == typeof n3) {
      var i6 = "function" == typeof n3.__u;
      i6 && n3.__u(), i6 && null == u5 || (n3.__u = n3(u5));
    } else n3.current = u5;
  } catch (n4) {
    l.__e(n4, t5);
  }
}
function V(n3, u5, t5) {
  var i6, o4;
  if (l.unmount && l.unmount(n3), (i6 = n3.ref) && (i6.current && i6.current !== n3.__e || N(i6, null, u5)), null != (i6 = n3.__c)) {
    if (i6.componentWillUnmount) try {
      i6.componentWillUnmount();
    } catch (n4) {
      l.__e(n4, u5);
    }
    i6.base = i6.__P = null;
  }
  if (i6 = n3.__k) for (o4 = 0; o4 < i6.length; o4++) i6[o4] && V(i6[o4], u5, t5 || "function" != typeof n3.type);
  t5 || w(n3.__e), n3.__c = n3.__ = n3.__e = n3.__d = void 0;
}
function q(n3, l6, u5) {
  return this.constructor(n3, u5);
}
function B(u5, t5, i6) {
  var o4, r5, f5, e4;
  l.__ && l.__(u5, t5), r5 = (o4 = "function" == typeof i6) ? null : i6 && i6.__k || t5.__k, f5 = [], e4 = [], O(t5, u5 = (!o4 && i6 || t5).__k = _(b, null, [u5]), r5 || h, h, t5.namespaceURI, !o4 && i6 ? [i6] : r5 ? null : t5.firstChild ? n.call(t5.childNodes) : null, f5, !o4 && i6 ? i6 : r5 ? r5.__e : t5.firstChild, o4, e4), j(f5, u5, e4);
}
function D(n3, l6) {
  B(n3, l6, D);
}
function E(l6, u5, t5) {
  var i6, o4, r5, f5, e4 = d({}, l6.props);
  for (r5 in l6.type && l6.type.defaultProps && (f5 = l6.type.defaultProps), u5) "key" == r5 ? i6 = u5[r5] : "ref" == r5 ? o4 = u5[r5] : e4[r5] = void 0 === u5[r5] && void 0 !== f5 ? f5[r5] : u5[r5];
  return arguments.length > 2 && (e4.children = arguments.length > 3 ? n.call(arguments, 2) : t5), g(l6.type, e4, i6 || l6.key, o4 || l6.ref, null);
}
function G(n3, l6) {
  var u5 = { __c: l6 = "__cC" + a++, __: n3, Consumer: function(n4, l7) {
    return n4.children(l7);
  }, Provider: function(n4) {
    var u6, t5;
    return this.getChildContext || (u6 = [], (t5 = {})[l6] = this, this.getChildContext = function() {
      return t5;
    }, this.componentWillUnmount = function() {
      u6 = null;
    }, this.shouldComponentUpdate = function(n5) {
      this.props.value !== n5.value && u6.some(function(n6) {
        n6.__e = true, M(n6);
      });
    }, this.sub = function(n5) {
      u6.push(n5);
      var l7 = n5.componentWillUnmount;
      n5.componentWillUnmount = function() {
        u6 && u6.splice(u6.indexOf(n5), 1), l7 && l7.call(n5);
      };
    }), n4.children;
  } };
  return u5.Provider.__ = u5.Consumer.contextType = u5;
}
var n, l, u, t, i, o, r, f, e, c, s, a, h, v, p, y;
var init_preact_module = __esm({
  "node_modules/preact/dist/preact.module.js"() {
    h = {};
    v = [];
    p = /acit|ex(?:s|g|n|p|$)|rph|grid|ows|mnc|ntw|ine[ch]|zoo|^ord|itera/i;
    y = Array.isArray;
    n = v.slice, l = { __e: function(n3, l6, u5, t5) {
      for (var i6, o4, r5; l6 = l6.__; ) if ((i6 = l6.__c) && !i6.__) try {
        if ((o4 = i6.constructor) && null != o4.getDerivedStateFromError && (i6.setState(o4.getDerivedStateFromError(n3)), r5 = i6.__d), null != i6.componentDidCatch && (i6.componentDidCatch(n3, t5 || {}), r5 = i6.__d), r5) return i6.__E = i6;
      } catch (l7) {
        n3 = l7;
      }
      throw n3;
    } }, u = 0, t = function(n3) {
      return null != n3 && null == n3.constructor;
    }, k.prototype.setState = function(n3, l6) {
      var u5;
      u5 = null != this.__s && this.__s !== this.state ? this.__s : this.__s = d({}, this.state), "function" == typeof n3 && (n3 = n3(d({}, u5), this.props)), n3 && d(u5, n3), null != n3 && this.__v && (l6 && this._sb.push(l6), M(this));
    }, k.prototype.forceUpdate = function(n3) {
      this.__v && (this.__e = true, n3 && this.__h.push(n3), M(this));
    }, k.prototype.render = b, i = [], r = "function" == typeof Promise ? Promise.prototype.then.bind(Promise.resolve()) : setTimeout, f = function(n3, l6) {
      return n3.__v.__b - l6.__v.__b;
    }, P.__r = 0, e = 0, c = F(false), s = F(true), a = 0;
  }
});

// node_modules/preact/devtools/dist/devtools.module.js
var i4;
var init_devtools_module = __esm({
  "node_modules/preact/devtools/dist/devtools.module.js"() {
    init_preact_module();
    null != (i4 = "undefined" != typeof globalThis ? globalThis : "undefined" != typeof window ? window : void 0) && i4.__PREACT_DEVTOOLS__ && i4.__PREACT_DEVTOOLS__.attachPreact("10.24.2", l, { Fragment: b, Component: k });
  }
});

// node_modules/preact/debug/dist/debug.module.js
var debug_module_exports = {};
__export(debug_module_exports, {
  resetPropWarnings: () => r4
});
function r4() {
  t4 = {};
}
function a4(e4) {
  return e4.type === b ? "Fragment" : "function" == typeof e4.type ? e4.type.displayName || e4.type.name : "string" == typeof e4.type ? e4.type : "#text";
}
function c4() {
  return i5.length > 0 ? i5[i5.length - 1] : null;
}
function u4(e4) {
  return "function" == typeof e4.type && e4.type != b;
}
function f4(n3) {
  for (var e4 = [n3], o4 = n3; null != o4.__o; ) e4.push(o4.__o), o4 = o4.__o;
  return e4.reduce(function(n4, e5) {
    n4 += "  in " + a4(e5);
    var o5 = e5.__source;
    return o5 ? n4 += " (at " + o5.fileName + ":" + o5.lineNumber + ")" : l5 && console.warn("Add @babel/plugin-transform-react-jsx-source to get a more detailed component stack. Note that you should not add it to production builds of your App for bundle size reasons."), l5 = false, n4 + "\n";
  }, "");
}
function p5(n3) {
  var e4 = [];
  return n3.__k ? (n3.__k.forEach(function(n4) {
    n4 && "function" == typeof n4.type ? e4.push.apply(e4, p5(n4)) : n4 && "string" == typeof n4.type && e4.push(n4.type);
  }), e4) : e4;
}
function h4(n3) {
  return n3 ? "function" == typeof n3.type ? null == n3.__ ? null != n3.__e && null != n3.__e.parentNode ? n3.__e.parentNode.localName : "" : h4(n3.__) : n3.type : "";
}
function y4(n3) {
  return "table" === n3 || "tfoot" === n3 || "tbody" === n3 || "thead" === n3 || "td" === n3 || "tr" === n3 || "th" === n3;
}
function w4(n3) {
  var e4 = n3.props, o4 = a4(n3), t5 = "";
  for (var r5 in e4) if (e4.hasOwnProperty(r5) && "children" !== r5) {
    var i6 = e4[r5];
    "function" == typeof i6 && (i6 = "function " + (i6.displayName || i6.name) + "() {}"), i6 = Object(i6) !== i6 || i6.toString ? i6 + "" : Object.prototype.toString.call(i6), t5 += " " + r5 + "=" + JSON.stringify(i6);
  }
  var s6 = e4.children;
  return "<" + o4 + t5 + (s6 && s6.length ? ">..</" + o4 + ">" : " />");
}
var t4, i5, s5, l5, d5, v5, m2, b3;
var init_debug_module = __esm({
  "node_modules/preact/debug/dist/debug.module.js"() {
    init_preact_module();
    init_devtools_module();
    t4 = {};
    i5 = [];
    s5 = [];
    l5 = true;
    d5 = "function" == typeof WeakMap;
    v5 = k.prototype.setState;
    k.prototype.setState = function(n3, e4) {
      return null == this.__v && null == this.state && console.warn('Calling "this.setState" inside the constructor of a component is a no-op and might be a bug in your application. Instead, set "this.state = {}" directly.\n\n' + f4(c4())), v5.call(this, n3, e4);
    };
    m2 = /^(address|article|aside|blockquote|details|div|dl|fieldset|figcaption|figure|footer|form|h1|h2|h3|h4|h5|h6|header|hgroup|hr|main|menu|nav|ol|p|pre|search|section|table|ul)$/;
    b3 = k.prototype.forceUpdate;
    k.prototype.forceUpdate = function(n3) {
      return null == this.__v ? console.warn('Calling "this.forceUpdate" inside the constructor of a component is a no-op and might be a bug in your application.\n\n' + f4(c4())) : null == this.__P && console.warn(`Can't call "this.forceUpdate" on an unmounted component. This is a no-op, but it indicates a memory leak in your application. To fix, cancel all subscriptions and asynchronous tasks in the componentWillUnmount method.

` + f4(this.__v)), b3.call(this, n3);
    }, l.__m = function(n3, e4) {
      var o4 = n3.type, t5 = e4.map(function(n4) {
        return n4 && n4.localName;
      }).filter(Boolean);
      console.error("Expected a DOM node of type " + o4 + " but found " + t5.join(", ") + "as available DOM-node(s), this is caused by the SSR'd HTML containing different DOM-nodes compared to the hydrated one.\n\n" + f4(n3));
    }, (function() {
      !(function() {
        var n4 = l.__b, o5 = l.diffed, t5 = l.__, r6 = l.vnode, a5 = l.__r;
        l.diffed = function(n5) {
          u4(n5) && s5.pop(), i5.pop(), o5 && o5(n5);
        }, l.__b = function(e4) {
          u4(e4) && i5.push(e4), n4 && n4(e4);
        }, l.__ = function(n5, e4) {
          s5 = [], t5 && t5(n5, e4);
        }, l.vnode = function(n5) {
          n5.__o = s5.length > 0 ? s5[s5.length - 1] : null, r6 && r6(n5);
        }, l.__r = function(n5) {
          u4(n5) && s5.push(n5), a5 && a5(n5);
        };
      })();
      var n3 = false, o4 = l.__b, r5 = l.diffed, c5 = l.vnode, l6 = l.__r, v6 = l.__e, b4 = l.__, g3 = l.__h, E3 = d5 ? { useEffect: /* @__PURE__ */ new WeakMap(), useLayoutEffect: /* @__PURE__ */ new WeakMap(), lazyPropTypes: /* @__PURE__ */ new WeakMap() } : null, k3 = [];
      l.__e = function(n4, e4, o5, t5) {
        if (e4 && e4.__c && "function" == typeof n4.then) {
          var r6 = n4;
          n4 = new Error("Missing Suspense. The throwing component was: " + a4(e4));
          for (var i6 = e4; i6; i6 = i6.__) if (i6.__c && i6.__c.__c) {
            n4 = r6;
            break;
          }
          if (n4 instanceof Error) throw n4;
        }
        try {
          (t5 = t5 || {}).componentStack = f4(e4), v6(n4, e4, o5, t5), "function" != typeof n4.then && setTimeout(function() {
            throw n4;
          });
        } catch (n5) {
          throw n5;
        }
      }, l.__ = function(n4, e4) {
        if (!e4) throw new Error("Undefined parent passed to render(), this is the second argument.\nCheck if the element is available in the DOM/has the correct id.");
        var o5;
        switch (e4.nodeType) {
          case 1:
          case 11:
          case 9:
            o5 = true;
            break;
          default:
            o5 = false;
        }
        if (!o5) {
          var t5 = a4(n4);
          throw new Error("Expected a valid HTML node as a second argument to render.	Received " + e4 + " instead: render(<" + t5 + " />, " + e4 + ");");
        }
        b4 && b4(n4, e4);
      }, l.__b = function(e4) {
        var r6 = e4.type;
        if (n3 = true, void 0 === r6) throw new Error("Undefined component passed to createElement()\n\nYou likely forgot to export your component or might have mixed up default and named imports" + w4(e4) + "\n\n" + f4(e4));
        if (null != r6 && "object" == typeof r6) {
          if (void 0 !== r6.__k && void 0 !== r6.__e) throw new Error("Invalid type passed to createElement(): " + r6 + "\n\nDid you accidentally pass a JSX literal as JSX twice?\n\n  let My" + a4(e4) + " = " + w4(r6) + ";\n  let vnode = <My" + a4(e4) + " />;\n\nThis usually happens when you export a JSX literal and not the component.\n\n" + f4(e4));
          throw new Error("Invalid type passed to createElement(): " + (Array.isArray(r6) ? "array" : r6));
        }
        if (void 0 !== e4.ref && "function" != typeof e4.ref && "object" != typeof e4.ref && !("$$typeof" in e4)) throw new Error(`Component's "ref" property should be a function, or an object created by createRef(), but got [` + typeof e4.ref + "] instead\n" + w4(e4) + "\n\n" + f4(e4));
        if ("string" == typeof e4.type) {
          for (var i6 in e4.props) if ("o" === i6[0] && "n" === i6[1] && "function" != typeof e4.props[i6] && null != e4.props[i6]) throw new Error(`Component's "` + i6 + '" property should be a function, but got [' + typeof e4.props[i6] + "] instead\n" + w4(e4) + "\n\n" + f4(e4));
        }
        if ("function" == typeof e4.type && e4.type.propTypes) {
          if ("Lazy" === e4.type.displayName && E3 && !E3.lazyPropTypes.has(e4.type)) {
            var s6 = "PropTypes are not supported on lazy(). Use propTypes on the wrapped component itself. ";
            try {
              var c6 = e4.type();
              E3.lazyPropTypes.set(e4.type, true), console.warn(s6 + "Component wrapped in lazy() is " + a4(c6));
            } catch (n4) {
              console.warn(s6 + "We will log the wrapped component's name once it is loaded.");
            }
          }
          var l7 = e4.props;
          e4.type.__f && delete (l7 = (function(n4, e5) {
            for (var o5 in e5) n4[o5] = e5[o5];
            return n4;
          })({}, l7)).ref, (function(n4, e5, o5, r7, a5) {
            Object.keys(n4).forEach(function(o6) {
              var i7;
              try {
                i7 = n4[o6](e5, o6, r7, "prop", null, "SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED");
              } catch (n5) {
                i7 = n5;
              }
              i7 && !(i7.message in t4) && (t4[i7.message] = true, console.error("Failed prop type: " + i7.message + (a5 && "\n" + a5() || "")));
            });
          })(e4.type.propTypes, l7, 0, a4(e4), function() {
            return f4(e4);
          });
        }
        o4 && o4(e4);
      };
      var T3, _5 = 0;
      l.__r = function(e4) {
        l6 && l6(e4), n3 = true;
        var o5 = e4.__c;
        if (o5 === T3 ? _5++ : _5 = 1, _5 >= 25) throw new Error("Too many re-renders. This is limited to prevent an infinite loop which may lock up your browser. The component causing this is: " + a4(e4));
        T3 = o5;
      }, l.__h = function(e4, o5, t5) {
        if (!e4 || !n3) throw new Error("Hook can only be invoked from render methods.");
        g3 && g3(e4, o5, t5);
      };
      var O2 = function(n4, e4) {
        return { get: function() {
          var o5 = "get" + n4 + e4;
          k3 && k3.indexOf(o5) < 0 && (k3.push(o5), console.warn("getting vnode." + n4 + " is deprecated, " + e4));
        }, set: function() {
          var o5 = "set" + n4 + e4;
          k3 && k3.indexOf(o5) < 0 && (k3.push(o5), console.warn("setting vnode." + n4 + " is not allowed, " + e4));
        } };
      }, I2 = { nodeName: O2("nodeName", "use vnode.type"), attributes: O2("attributes", "use vnode.props"), children: O2("children", "use vnode.props.children") }, M2 = Object.create({}, I2);
      l.vnode = function(n4) {
        var e4 = n4.props;
        if (null !== n4.type && null != e4 && ("__source" in e4 || "__self" in e4)) {
          var o5 = n4.props = {};
          for (var t5 in e4) {
            var r6 = e4[t5];
            "__source" === t5 ? n4.__source = r6 : "__self" === t5 ? n4.__self = r6 : o5[t5] = r6;
          }
        }
        n4.__proto__ = M2, c5 && c5(n4);
      }, l.diffed = function(e4) {
        var o5, t5 = e4.type, i6 = e4.__;
        if (e4.__k && e4.__k.forEach(function(n4) {
          if ("object" == typeof n4 && n4 && void 0 === n4.type) {
            var o6 = Object.keys(n4).join(",");
            throw new Error("Objects are not valid as a child. Encountered an object with the keys {" + o6 + "}.\n\n" + f4(e4));
          }
        }), e4.__c === T3 && (_5 = 0), "string" == typeof t5 && (y4(t5) || "p" === t5 || "a" === t5 || "button" === t5)) {
          var s6 = h4(i6);
          if ("" !== s6 && y4(t5)) "table" === t5 && "td" !== s6 && y4(s6) ? (console.log(s6, i6.__e), console.error("Improper nesting of table. Your <table> should not have a table-node parent." + w4(e4) + "\n\n" + f4(e4))) : "thead" !== t5 && "tfoot" !== t5 && "tbody" !== t5 || "table" === s6 ? "tr" === t5 && "thead" !== s6 && "tfoot" !== s6 && "tbody" !== s6 ? console.error("Improper nesting of table. Your <tr> should have a <thead/tbody/tfoot> parent." + w4(e4) + "\n\n" + f4(e4)) : "td" === t5 && "tr" !== s6 ? console.error("Improper nesting of table. Your <td> should have a <tr> parent." + w4(e4) + "\n\n" + f4(e4)) : "th" === t5 && "tr" !== s6 && console.error("Improper nesting of table. Your <th> should have a <tr>." + w4(e4) + "\n\n" + f4(e4)) : console.error("Improper nesting of table. Your <thead/tbody/tfoot> should have a <table> parent." + w4(e4) + "\n\n" + f4(e4));
          else if ("p" === t5) {
            var c6 = p5(e4).filter(function(n4) {
              return m2.test(n4);
            });
            c6.length && console.error("Improper nesting of paragraph. Your <p> should not have " + c6.join(", ") + "as child-elements." + w4(e4) + "\n\n" + f4(e4));
          } else "a" !== t5 && "button" !== t5 || -1 !== p5(e4).indexOf(t5) && console.error("Improper nesting of interactive content. Your <" + t5 + "> should not have other " + ("a" === t5 ? "anchor" : "button") + " tags as child-elements." + w4(e4) + "\n\n" + f4(e4));
        }
        if (n3 = false, r5 && r5(e4), null != e4.__k) for (var l7 = [], u5 = 0; u5 < e4.__k.length; u5++) {
          var d6 = e4.__k[u5];
          if (d6 && null != d6.key) {
            var v7 = d6.key;
            if (-1 !== l7.indexOf(v7)) {
              console.error('Following component has two or more children with the same key attribute: "' + v7 + '". This may cause glitches and misbehavior in rendering process. Component: \n\n' + w4(e4) + "\n\n" + f4(e4));
              break;
            }
            l7.push(v7);
          }
        }
        if (null != e4.__c && null != e4.__c.__H) {
          var b5 = e4.__c.__H.__;
          if (b5) for (var g4 = 0; g4 < b5.length; g4 += 1) {
            var E4 = b5[g4];
            if (E4.__H) {
              for (var k4 = 0; k4 < E4.__H.length; k4++) if ((o5 = E4.__H[k4]) != o5) {
                var O3 = a4(e4);
                throw new Error("Invalid argument passed to hook. Hooks should not be called with NaN in the dependency array. Hook index " + g4 + " in component " + O3 + " was called with NaN.");
              }
            }
          }
        }
      };
    })();
  }
});

// packages/interactivity/build-module/index.js
init_preact_module();

// node_modules/@preact/signals/dist/signals.module.js
init_preact_module();

// node_modules/preact/hooks/dist/hooks.module.js
init_preact_module();
var t2;
var r2;
var u2;
var i2;
var o2 = 0;
var f2 = [];
var c2 = l;
var e2 = c2.__b;
var a2 = c2.__r;
var v2 = c2.diffed;
var l2 = c2.__c;
var m = c2.unmount;
var s2 = c2.__;
function d2(n3, t5) {
  c2.__h && c2.__h(r2, n3, o2 || t5), o2 = 0;
  var u5 = r2.__H || (r2.__H = { __: [], __h: [] });
  return n3 >= u5.__.length && u5.__.push({}), u5.__[n3];
}
function h2(n3) {
  return o2 = 1, p2(D2, n3);
}
function p2(n3, u5, i6) {
  var o4 = d2(t2++, 2);
  if (o4.t = n3, !o4.__c && (o4.__ = [i6 ? i6(u5) : D2(void 0, u5), function(n4) {
    var t5 = o4.__N ? o4.__N[0] : o4.__[0], r5 = o4.t(t5, n4);
    t5 !== r5 && (o4.__N = [r5, o4.__[1]], o4.__c.setState({}));
  }], o4.__c = r2, !r2.u)) {
    var f5 = function(n4, t5, r5) {
      if (!o4.__c.__H) return true;
      var u6 = o4.__c.__H.__.filter(function(n5) {
        return !!n5.__c;
      });
      if (u6.every(function(n5) {
        return !n5.__N;
      })) return !c5 || c5.call(this, n4, t5, r5);
      var i7 = false;
      return u6.forEach(function(n5) {
        if (n5.__N) {
          var t6 = n5.__[0];
          n5.__ = n5.__N, n5.__N = void 0, t6 !== n5.__[0] && (i7 = true);
        }
      }), !(!i7 && o4.__c.props === n4) && (!c5 || c5.call(this, n4, t5, r5));
    };
    r2.u = true;
    var c5 = r2.shouldComponentUpdate, e4 = r2.componentWillUpdate;
    r2.componentWillUpdate = function(n4, t5, r5) {
      if (this.__e) {
        var u6 = c5;
        c5 = void 0, f5(n4, t5, r5), c5 = u6;
      }
      e4 && e4.call(this, n4, t5, r5);
    }, r2.shouldComponentUpdate = f5;
  }
  return o4.__N || o4.__;
}
function y2(n3, u5) {
  var i6 = d2(t2++, 3);
  !c2.__s && C2(i6.__H, u5) && (i6.__ = n3, i6.i = u5, r2.__H.__h.push(i6));
}
function _2(n3, u5) {
  var i6 = d2(t2++, 4);
  !c2.__s && C2(i6.__H, u5) && (i6.__ = n3, i6.i = u5, r2.__h.push(i6));
}
function A2(n3) {
  return o2 = 5, T2(function() {
    return { current: n3 };
  }, []);
}
function T2(n3, r5) {
  var u5 = d2(t2++, 7);
  return C2(u5.__H, r5) && (u5.__ = n3(), u5.__H = r5, u5.__h = n3), u5.__;
}
function q2(n3, t5) {
  return o2 = 8, T2(function() {
    return n3;
  }, t5);
}
function x2(n3) {
  var u5 = r2.context[n3.__c], i6 = d2(t2++, 9);
  return i6.c = n3, u5 ? (null == i6.__ && (i6.__ = true, u5.sub(r2)), u5.props.value) : n3.__;
}
function j2() {
  for (var n3; n3 = f2.shift(); ) if (n3.__P && n3.__H) try {
    n3.__H.__h.forEach(z2), n3.__H.__h.forEach(B2), n3.__H.__h = [];
  } catch (t5) {
    n3.__H.__h = [], c2.__e(t5, n3.__v);
  }
}
c2.__b = function(n3) {
  r2 = null, e2 && e2(n3);
}, c2.__ = function(n3, t5) {
  n3 && t5.__k && t5.__k.__m && (n3.__m = t5.__k.__m), s2 && s2(n3, t5);
}, c2.__r = function(n3) {
  a2 && a2(n3), t2 = 0;
  var i6 = (r2 = n3.__c).__H;
  i6 && (u2 === r2 ? (i6.__h = [], r2.__h = [], i6.__.forEach(function(n4) {
    n4.__N && (n4.__ = n4.__N), n4.i = n4.__N = void 0;
  })) : (i6.__h.forEach(z2), i6.__h.forEach(B2), i6.__h = [], t2 = 0)), u2 = r2;
}, c2.diffed = function(n3) {
  v2 && v2(n3);
  var t5 = n3.__c;
  t5 && t5.__H && (t5.__H.__h.length && (1 !== f2.push(t5) && i2 === c2.requestAnimationFrame || ((i2 = c2.requestAnimationFrame) || w2)(j2)), t5.__H.__.forEach(function(n4) {
    n4.i && (n4.__H = n4.i), n4.i = void 0;
  })), u2 = r2 = null;
}, c2.__c = function(n3, t5) {
  t5.some(function(n4) {
    try {
      n4.__h.forEach(z2), n4.__h = n4.__h.filter(function(n5) {
        return !n5.__ || B2(n5);
      });
    } catch (r5) {
      t5.some(function(n5) {
        n5.__h && (n5.__h = []);
      }), t5 = [], c2.__e(r5, n4.__v);
    }
  }), l2 && l2(n3, t5);
}, c2.unmount = function(n3) {
  m && m(n3);
  var t5, r5 = n3.__c;
  r5 && r5.__H && (r5.__H.__.forEach(function(n4) {
    try {
      z2(n4);
    } catch (n5) {
      t5 = n5;
    }
  }), r5.__H = void 0, t5 && c2.__e(t5, r5.__v));
};
var k2 = "function" == typeof requestAnimationFrame;
function w2(n3) {
  var t5, r5 = function() {
    clearTimeout(u5), k2 && cancelAnimationFrame(t5), setTimeout(n3);
  }, u5 = setTimeout(r5, 100);
  k2 && (t5 = requestAnimationFrame(r5));
}
function z2(n3) {
  var t5 = r2, u5 = n3.__c;
  "function" == typeof u5 && (n3.__c = void 0, u5()), r2 = t5;
}
function B2(n3) {
  var t5 = r2;
  n3.__c = n3.__(), r2 = t5;
}
function C2(n3, t5) {
  return !n3 || n3.length !== t5.length || t5.some(function(t6, r5) {
    return t6 !== n3[r5];
  });
}
function D2(n3, t5) {
  return "function" == typeof t5 ? t5(n3) : t5;
}

// node_modules/@preact/signals-core/dist/signals-core.module.js
var i3 = Symbol.for("preact-signals");
function t3() {
  if (!(s3 > 1)) {
    var i6, t5 = false;
    while (void 0 !== h3) {
      var r5 = h3;
      h3 = void 0;
      f3++;
      while (void 0 !== r5) {
        var o4 = r5.o;
        r5.o = void 0;
        r5.f &= -3;
        if (!(8 & r5.f) && c3(r5)) try {
          r5.c();
        } catch (r6) {
          if (!t5) {
            i6 = r6;
            t5 = true;
          }
        }
        r5 = o4;
      }
    }
    f3 = 0;
    s3--;
    if (t5) throw i6;
  } else s3--;
}
function r3(i6) {
  if (s3 > 0) return i6();
  s3++;
  try {
    return i6();
  } finally {
    t3();
  }
}
var o3 = void 0;
var h3 = void 0;
var s3 = 0;
var f3 = 0;
var v3 = 0;
function e3(i6) {
  if (void 0 !== o3) {
    var t5 = i6.n;
    if (void 0 === t5 || t5.t !== o3) {
      t5 = { i: 0, S: i6, p: o3.s, n: void 0, t: o3, e: void 0, x: void 0, r: t5 };
      if (void 0 !== o3.s) o3.s.n = t5;
      o3.s = t5;
      i6.n = t5;
      if (32 & o3.f) i6.S(t5);
      return t5;
    } else if (-1 === t5.i) {
      t5.i = 0;
      if (void 0 !== t5.n) {
        t5.n.p = t5.p;
        if (void 0 !== t5.p) t5.p.n = t5.n;
        t5.p = o3.s;
        t5.n = void 0;
        o3.s.n = t5;
        o3.s = t5;
      }
      return t5;
    }
  }
}
function u3(i6) {
  this.v = i6;
  this.i = 0;
  this.n = void 0;
  this.t = void 0;
}
u3.prototype.brand = i3;
u3.prototype.h = function() {
  return true;
};
u3.prototype.S = function(i6) {
  if (this.t !== i6 && void 0 === i6.e) {
    i6.x = this.t;
    if (void 0 !== this.t) this.t.e = i6;
    this.t = i6;
  }
};
u3.prototype.U = function(i6) {
  if (void 0 !== this.t) {
    var t5 = i6.e, r5 = i6.x;
    if (void 0 !== t5) {
      t5.x = r5;
      i6.e = void 0;
    }
    if (void 0 !== r5) {
      r5.e = t5;
      i6.x = void 0;
    }
    if (i6 === this.t) this.t = r5;
  }
};
u3.prototype.subscribe = function(i6) {
  var t5 = this;
  return E2(function() {
    var r5 = t5.value, n3 = o3;
    o3 = void 0;
    try {
      i6(r5);
    } finally {
      o3 = n3;
    }
  });
};
u3.prototype.valueOf = function() {
  return this.value;
};
u3.prototype.toString = function() {
  return this.value + "";
};
u3.prototype.toJSON = function() {
  return this.value;
};
u3.prototype.peek = function() {
  var i6 = o3;
  o3 = void 0;
  try {
    return this.value;
  } finally {
    o3 = i6;
  }
};
Object.defineProperty(u3.prototype, "value", { get: function() {
  var i6 = e3(this);
  if (void 0 !== i6) i6.i = this.i;
  return this.v;
}, set: function(i6) {
  if (i6 !== this.v) {
    if (f3 > 100) throw new Error("Cycle detected");
    this.v = i6;
    this.i++;
    v3++;
    s3++;
    try {
      for (var r5 = this.t; void 0 !== r5; r5 = r5.x) r5.t.N();
    } finally {
      t3();
    }
  }
} });
function d3(i6) {
  return new u3(i6);
}
function c3(i6) {
  for (var t5 = i6.s; void 0 !== t5; t5 = t5.n) if (t5.S.i !== t5.i || !t5.S.h() || t5.S.i !== t5.i) return true;
  return false;
}
function a3(i6) {
  for (var t5 = i6.s; void 0 !== t5; t5 = t5.n) {
    var r5 = t5.S.n;
    if (void 0 !== r5) t5.r = r5;
    t5.S.n = t5;
    t5.i = -1;
    if (void 0 === t5.n) {
      i6.s = t5;
      break;
    }
  }
}
function l3(i6) {
  var t5 = i6.s, r5 = void 0;
  while (void 0 !== t5) {
    var o4 = t5.p;
    if (-1 === t5.i) {
      t5.S.U(t5);
      if (void 0 !== o4) o4.n = t5.n;
      if (void 0 !== t5.n) t5.n.p = o4;
    } else r5 = t5;
    t5.S.n = t5.r;
    if (void 0 !== t5.r) t5.r = void 0;
    t5 = o4;
  }
  i6.s = r5;
}
function y3(i6) {
  u3.call(this, void 0);
  this.x = i6;
  this.s = void 0;
  this.g = v3 - 1;
  this.f = 4;
}
(y3.prototype = new u3()).h = function() {
  this.f &= -3;
  if (1 & this.f) return false;
  if (32 == (36 & this.f)) return true;
  this.f &= -5;
  if (this.g === v3) return true;
  this.g = v3;
  this.f |= 1;
  if (this.i > 0 && !c3(this)) {
    this.f &= -2;
    return true;
  }
  var i6 = o3;
  try {
    a3(this);
    o3 = this;
    var t5 = this.x();
    if (16 & this.f || this.v !== t5 || 0 === this.i) {
      this.v = t5;
      this.f &= -17;
      this.i++;
    }
  } catch (i7) {
    this.v = i7;
    this.f |= 16;
    this.i++;
  }
  o3 = i6;
  l3(this);
  this.f &= -2;
  return true;
};
y3.prototype.S = function(i6) {
  if (void 0 === this.t) {
    this.f |= 36;
    for (var t5 = this.s; void 0 !== t5; t5 = t5.n) t5.S.S(t5);
  }
  u3.prototype.S.call(this, i6);
};
y3.prototype.U = function(i6) {
  if (void 0 !== this.t) {
    u3.prototype.U.call(this, i6);
    if (void 0 === this.t) {
      this.f &= -33;
      for (var t5 = this.s; void 0 !== t5; t5 = t5.n) t5.S.U(t5);
    }
  }
};
y3.prototype.N = function() {
  if (!(2 & this.f)) {
    this.f |= 6;
    for (var i6 = this.t; void 0 !== i6; i6 = i6.x) i6.t.N();
  }
};
Object.defineProperty(y3.prototype, "value", { get: function() {
  if (1 & this.f) throw new Error("Cycle detected");
  var i6 = e3(this);
  this.h();
  if (void 0 !== i6) i6.i = this.i;
  if (16 & this.f) throw this.v;
  return this.v;
} });
function w3(i6) {
  return new y3(i6);
}
function _3(i6) {
  var r5 = i6.u;
  i6.u = void 0;
  if ("function" == typeof r5) {
    s3++;
    var n3 = o3;
    o3 = void 0;
    try {
      r5();
    } catch (t5) {
      i6.f &= -2;
      i6.f |= 8;
      g2(i6);
      throw t5;
    } finally {
      o3 = n3;
      t3();
    }
  }
}
function g2(i6) {
  for (var t5 = i6.s; void 0 !== t5; t5 = t5.n) t5.S.U(t5);
  i6.x = void 0;
  i6.s = void 0;
  _3(i6);
}
function p3(i6) {
  if (o3 !== this) throw new Error("Out-of-order effect");
  l3(this);
  o3 = i6;
  this.f &= -2;
  if (8 & this.f) g2(this);
  t3();
}
function b2(i6) {
  this.x = i6;
  this.u = void 0;
  this.s = void 0;
  this.o = void 0;
  this.f = 32;
}
b2.prototype.c = function() {
  var i6 = this.S();
  try {
    if (8 & this.f) return;
    if (void 0 === this.x) return;
    var t5 = this.x();
    if ("function" == typeof t5) this.u = t5;
  } finally {
    i6();
  }
};
b2.prototype.S = function() {
  if (1 & this.f) throw new Error("Cycle detected");
  this.f |= 1;
  this.f &= -9;
  _3(this);
  a3(this);
  s3++;
  var i6 = o3;
  o3 = this;
  return p3.bind(this, i6);
};
b2.prototype.N = function() {
  if (!(2 & this.f)) {
    this.f |= 2;
    this.o = h3;
    h3 = this;
  }
};
b2.prototype.d = function() {
  this.f |= 8;
  if (!(1 & this.f)) g2(this);
};
function E2(i6) {
  var t5 = new b2(i6);
  try {
    t5.c();
  } catch (i7) {
    t5.d();
    throw i7;
  }
  return t5.d.bind(t5);
}

// node_modules/@preact/signals/dist/signals.module.js
var v4;
var s4;
function l4(n3, i6) {
  l[n3] = i6.bind(null, l[n3] || function() {
  });
}
function d4(n3) {
  if (s4) s4();
  s4 = n3 && n3.S();
}
function p4(n3) {
  var r5 = this, f5 = n3.data, o4 = useSignal(f5);
  o4.value = f5;
  var e4 = T2(function() {
    var n4 = r5.__v;
    while (n4 = n4.__) if (n4.__c) {
      n4.__c.__$f |= 4;
      break;
    }
    r5.__$u.c = function() {
      var n5;
      if (!t(e4.peek()) && 3 === (null == (n5 = r5.base) ? void 0 : n5.nodeType)) r5.base.data = e4.peek();
      else {
        r5.__$f |= 1;
        r5.setState({});
      }
    };
    return w3(function() {
      var n5 = o4.value.value;
      return 0 === n5 ? 0 : true === n5 ? "" : n5 || "";
    });
  }, []);
  return e4.value;
}
p4.displayName = "_st";
Object.defineProperties(u3.prototype, { constructor: { configurable: true, value: void 0 }, type: { configurable: true, value: p4 }, props: { configurable: true, get: function() {
  return { data: this };
} }, __b: { configurable: true, value: 1 } });
l4("__b", function(n3, r5) {
  if ("string" == typeof r5.type) {
    var i6, t5 = r5.props;
    for (var f5 in t5) if ("children" !== f5) {
      var o4 = t5[f5];
      if (o4 instanceof u3) {
        if (!i6) r5.__np = i6 = {};
        i6[f5] = o4;
        t5[f5] = o4.peek();
      }
    }
  }
  n3(r5);
});
l4("__r", function(n3, r5) {
  d4();
  var i6, t5 = r5.__c;
  if (t5) {
    t5.__$f &= -2;
    if (void 0 === (i6 = t5.__$u)) t5.__$u = i6 = (function(n4) {
      var r6;
      E2(function() {
        r6 = this;
      });
      r6.c = function() {
        t5.__$f |= 1;
        t5.setState({});
      };
      return r6;
    })();
  }
  v4 = t5;
  d4(i6);
  n3(r5);
});
l4("__e", function(n3, r5, i6, t5) {
  d4();
  v4 = void 0;
  n3(r5, i6, t5);
});
l4("diffed", function(n3, r5) {
  d4();
  v4 = void 0;
  var i6;
  if ("string" == typeof r5.type && (i6 = r5.__e)) {
    var t5 = r5.__np, f5 = r5.props;
    if (t5) {
      var o4 = i6.U;
      if (o4) for (var e4 in o4) {
        var u5 = o4[e4];
        if (void 0 !== u5 && !(e4 in t5)) {
          u5.d();
          o4[e4] = void 0;
        }
      }
      else i6.U = o4 = {};
      for (var a5 in t5) {
        var c5 = o4[a5], s6 = t5[a5];
        if (void 0 === c5) {
          c5 = _4(i6, a5, s6, f5);
          o4[a5] = c5;
        } else c5.o(s6, f5);
      }
    }
  }
  n3(r5);
});
function _4(n3, r5, i6, t5) {
  var f5 = r5 in n3 && void 0 === n3.ownerSVGElement, o4 = d3(i6);
  return { o: function(n4, r6) {
    o4.value = n4;
    t5 = r6;
  }, d: E2(function() {
    var i7 = o4.value.value;
    if (t5[r5] !== i7) {
      t5[r5] = i7;
      if (f5) n3[r5] = i7;
      else if (i7) n3.setAttribute(r5, i7);
      else n3.removeAttribute(r5);
    }
  }) };
}
l4("unmount", function(n3, r5) {
  if ("string" == typeof r5.type) {
    var i6 = r5.__e;
    if (i6) {
      var t5 = i6.U;
      if (t5) {
        i6.U = void 0;
        for (var f5 in t5) {
          var o4 = t5[f5];
          if (o4) o4.d();
        }
      }
    }
  } else {
    var e4 = r5.__c;
    if (e4) {
      var u5 = e4.__$u;
      if (u5) {
        e4.__$u = void 0;
        u5.d();
      }
    }
  }
  n3(r5);
});
l4("__h", function(n3, r5, i6, t5) {
  if (t5 < 3 || 9 === t5) r5.__$f |= 2;
  n3(r5, i6, t5);
});
k.prototype.shouldComponentUpdate = function(n3, r5) {
  var i6 = this.__$u;
  if (!(i6 && void 0 !== i6.s || 4 & this.__$f)) return true;
  if (3 & this.__$f) return true;
  for (var t5 in r5) return true;
  for (var f5 in n3) if ("__source" !== f5 && n3[f5] !== this.props[f5]) return true;
  for (var o4 in this.props) if (!(o4 in n3)) return true;
  return false;
};
function useSignal(n3) {
  return T2(function() {
    return d3(n3);
  }, []);
}

// packages/interactivity/build-module/directives.js
init_preact_module();

// packages/interactivity/build-module/namespaces.js
var namespaceStack = [];
var getNamespace = () => namespaceStack.slice(-1)[0];
var setNamespace = (namespace) => {
  namespaceStack.push(namespace);
};
var resetNamespace = () => {
  namespaceStack.pop();
};

// packages/interactivity/build-module/scopes.js
var scopeStack = [];
var getScope = () => scopeStack.slice(-1)[0];
var setScope = (scope) => {
  scopeStack.push(scope);
};
var resetScope = () => {
  scopeStack.pop();
};
var throwNotInScope = (method) => {
  throw Error(
    `Cannot call \`${method}()\` when there is no scope. If you are using an async function, please consider using a generator instead. If you are using some sort of async callbacks, like \`setTimeout\`, please wrap the callback with \`withScope(callback)\`.`
  );
};
var getContext = (namespace) => {
  const scope = getScope();
  if (true) {
    if (!scope) {
      throwNotInScope("getContext");
    }
  }
  return scope.context[namespace || getNamespace()];
};
var getElement = () => {
  const scope = getScope();
  let deepReadOnlyOptions = {};
  if (true) {
    if (!scope) {
      throwNotInScope("getElement");
    }
    deepReadOnlyOptions = {
      errorMessage: "Don't mutate the attributes from `getElement`, use `data-wp-bind` to modify the attributes of an element instead."
    };
  }
  const { ref, attributes } = scope;
  return Object.freeze({
    ref: ref.current,
    attributes: deepReadOnly(attributes, deepReadOnlyOptions)
  });
};
function getServerContext(namespace) {
  const scope = getScope();
  if (true) {
    if (!scope) {
      throwNotInScope("getServerContext");
    }
  }
  getServerContext.subscribe = navigationSignal.value;
  return scope.serverContext[namespace || getNamespace()];
}
getServerContext.subscribe = 0;

// packages/interactivity/build-module/utils.js
var afterNextFrame = (callback) => {
  return new Promise((resolve2) => {
    const done = () => {
      clearTimeout(timeout);
      window.cancelAnimationFrame(raf);
      setTimeout(() => {
        callback();
        resolve2();
      });
    };
    const timeout = setTimeout(done, 100);
    const raf = window.requestAnimationFrame(done);
  });
};
var splitTask = typeof window.scheduler?.yield === "function" ? window.scheduler.yield.bind(window.scheduler) : () => {
  return new Promise((resolve2) => {
    setTimeout(resolve2, 0);
  });
};
function createFlusher(compute, notify) {
  let flush = () => void 0;
  const dispose = E2(function() {
    flush = this.c.bind(this);
    this.x = compute;
    this.c = notify;
    return compute();
  });
  return { flush, dispose };
}
function useSignalEffect(callback) {
  y2(() => {
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
function withScope(func) {
  const scope = getScope();
  const ns = getNamespace();
  let wrapped;
  if (func?.constructor?.name === "GeneratorFunction") {
    wrapped = async (...args) => {
      const gen = func(...args);
      let value;
      let it;
      let error;
      while (true) {
        setNamespace(ns);
        setScope(scope);
        try {
          it = error ? gen.throw(error) : gen.next(value);
          error = void 0;
        } catch (e4) {
          throw e4;
        } finally {
          resetScope();
          resetNamespace();
        }
        try {
          value = await it.value;
        } catch (e4) {
          error = e4;
        }
        if (it.done) {
          if (error) {
            throw error;
          } else {
            break;
          }
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
  const syncAware = func;
  if (syncAware.sync) {
    const syncAwareWrapped = wrapped;
    syncAwareWrapped.sync = true;
    return syncAwareWrapped;
  }
  return wrapped;
}
function useWatch(callback) {
  useSignalEffect(withScope(callback));
}
function useInit(callback) {
  y2(withScope(callback), []);
}
function useEffect(callback, inputs) {
  y2(withScope(callback), inputs);
}
function useLayoutEffect(callback, inputs) {
  _2(withScope(callback), inputs);
}
function useCallback(callback, inputs) {
  return q2(withScope(callback), inputs);
}
function useMemo(factory, inputs) {
  return T2(withScope(factory), inputs);
}
var createRootFragment = (parent, replaceNode) => {
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
    removeChild(c5) {
      parent.removeChild(c5);
    },
    contains(c5) {
      parent.contains(c5);
    }
  };
};
function kebabToCamelCase(str) {
  return str.replace(/^-+|-+$/g, "").toLowerCase().replace(/-([a-z])/g, function(_match, group1) {
    return group1.toUpperCase();
  });
}
var logged = /* @__PURE__ */ new Set();
var warn = (message) => {
  if (true) {
    if (logged.has(message)) {
      return;
    }
    console.warn(message);
    try {
      throw Error(message);
    } catch (e4) {
    }
    logged.add(message);
  }
};
var isPlainObject = (candidate) => Boolean(
  candidate && typeof candidate === "object" && candidate.constructor === Object
);
function withSyncEvent(callback) {
  const syncAware = callback;
  syncAware.sync = true;
  return syncAware;
}
var readOnlyMap = /* @__PURE__ */ new WeakMap();
var createDeepReadOnlyHandlers = (errorMessage) => {
  const handleError = () => {
    if (true) {
      warn(errorMessage);
    }
    return false;
  };
  return {
    get(target, prop) {
      const value = target[prop];
      if (value && typeof value === "object") {
        return deepReadOnly(value, { errorMessage });
      }
      return value;
    },
    set: handleError,
    deleteProperty: handleError,
    defineProperty: handleError
  };
};
function deepReadOnly(obj, options) {
  const errorMessage = options?.errorMessage ?? "Cannot modify read-only object";
  if (!readOnlyMap.has(obj)) {
    const handlers = createDeepReadOnlyHandlers(errorMessage);
    readOnlyMap.set(obj, new Proxy(obj, handlers));
  }
  return readOnlyMap.get(obj);
}
var navigationSignal = d3(0);

// packages/interactivity/build-module/hooks.js
init_preact_module();

// packages/interactivity/build-module/proxies/registry.js
var objToProxy = /* @__PURE__ */ new WeakMap();
var proxyToObj = /* @__PURE__ */ new WeakMap();
var proxyToNs = /* @__PURE__ */ new WeakMap();
var supported = /* @__PURE__ */ new Set([Object, Array]);
var createProxy = (namespace, obj, handlers) => {
  if (!shouldProxy(obj)) {
    throw Error("This object cannot be proxified.");
  }
  if (!objToProxy.has(obj)) {
    const proxy = new Proxy(obj, handlers);
    objToProxy.set(obj, proxy);
    proxyToObj.set(proxy, obj);
    proxyToNs.set(proxy, namespace);
  }
  return objToProxy.get(obj);
};
var getProxyFromObject = (obj) => objToProxy.get(obj);
var getNamespaceFromProxy = (proxy) => proxyToNs.get(proxy);
var shouldProxy = (candidate) => {
  if (typeof candidate !== "object" || candidate === null) {
    return false;
  }
  return !proxyToNs.has(candidate) && supported.has(candidate.constructor);
};
var getObjectFromProxy = (proxy) => proxyToObj.get(proxy);

// packages/interactivity/build-module/proxies/signals.js
var NO_SCOPE = {};
var PropSignal = class {
  /**
   * Proxy that holds the property this PropSignal is associated with.
   */
  owner;
  /**
   * Relation of computeds by scope. These computeds are read-only signals
   * that depend on whether the property is a value or a getter and,
   * therefore, can return different values depending on the scope in which
   * the getter is accessed.
   */
  computedsByScope;
  /**
   * Signal with the value assigned to the related property.
   */
  valueSignal;
  /**
   * Signal with the getter assigned to the related property.
   */
  getterSignal;
  /**
   * Pending getter to be consolidated.
   */
  pendingGetter;
  /**
   * Structure that manages reactivity for a property in a state object, using
   * signals to keep track of property value or getter modifications.
   *
   * @param owner Proxy that holds the property this instance is associated
   *              with.
   */
  constructor(owner) {
    this.owner = owner;
    this.computedsByScope = /* @__PURE__ */ new WeakMap();
  }
  /**
   * Changes the internal value. If a getter was set before, it is set to
   * `undefined`.
   *
   * @param value New value.
   */
  setValue(value) {
    this.update({ value });
  }
  /**
   * Changes the internal getter. If a value was set before, it is set to
   * `undefined`.
   *
   * @param getter New getter.
   */
  setGetter(getter) {
    this.update({ get: getter });
  }
  /**
   * Changes the internal getter asynchronously.
   *
   * The update is made in a microtask, which prevents issues with getters
   * accessing the state, and ensures the update occurs before any render.
   *
   * @param getter New getter.
   */
  setPendingGetter(getter) {
    this.pendingGetter = getter;
    queueMicrotask(() => this.consolidateGetter());
  }
  /**
   * Consolidate the pending value of the getter.
   */
  consolidateGetter() {
    const getter = this.pendingGetter;
    if (getter) {
      this.pendingGetter = void 0;
      this.update({ get: getter });
    }
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
    if (this.pendingGetter) {
      this.consolidateGetter();
    }
    if (!this.computedsByScope.has(scope)) {
      const callback = () => {
        const getter = this.getterSignal?.value;
        return getter ? getter.call(this.owner) : this.valueSignal?.value;
      };
      setNamespace(getNamespaceFromProxy(this.owner));
      this.computedsByScope.set(
        scope,
        w3(withScope(callback))
      );
      resetNamespace();
    }
    return this.computedsByScope.get(scope);
  }
  /**
   *  Updates the internal signals for the value and the getter of the
   *  corresponding prop.
   *
   * @param param0
   * @param param0.get   New getter.
   * @param param0.value New value.
   */
  update({ get, value }) {
    if (!this.valueSignal) {
      this.valueSignal = d3(value);
      this.getterSignal = d3(get);
    } else if (value !== this.valueSignal.peek() || get !== this.getterSignal.peek()) {
      r3(() => {
        this.valueSignal.value = value;
        this.getterSignal.value = get;
      });
    }
  }
};

// packages/interactivity/build-module/proxies/state.js
var wellKnownSymbols = new Set(
  Object.getOwnPropertyNames(Symbol).map((key) => Symbol[key]).filter((value) => typeof value === "symbol")
);
var proxyToProps = /* @__PURE__ */ new WeakMap();
var hasPropSignal = (proxy, key) => proxyToProps.has(proxy) && proxyToProps.get(proxy).has(key);
var getPropSignal = (proxy, key, initial) => {
  if (!proxyToProps.has(proxy)) {
    proxyToProps.set(proxy, /* @__PURE__ */ new Map());
  }
  key = typeof key === "number" ? `${key}` : key;
  const props = proxyToProps.get(proxy);
  if (!props.has(key)) {
    const ns = getNamespaceFromProxy(proxy);
    const prop = new PropSignal(proxy);
    props.set(key, prop);
    if (initial) {
      const { get, value } = initial;
      if (get) {
        prop.setGetter(get);
      } else {
        prop.setValue(
          shouldProxy(value) ? proxifyState(ns, value) : value
        );
      }
    }
  }
  return props.get(key);
};
var objToIterable = /* @__PURE__ */ new WeakMap();
var peeking = false;
var PENDING_GETTER = Symbol("PENDING_GETTER");
var stateHandlers = {
  get(target, key, receiver) {
    if (peeking || !target.hasOwnProperty(key) && key in target || typeof key === "symbol" && wellKnownSymbols.has(key)) {
      return Reflect.get(target, key, receiver);
    }
    const desc = Object.getOwnPropertyDescriptor(target, key);
    const prop = getPropSignal(receiver, key, desc);
    const result = prop.getComputed().value;
    if (result === PENDING_GETTER) {
      throw PENDING_GETTER;
    }
    if (typeof result === "function") {
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
    setNamespace(getNamespaceFromProxy(receiver));
    try {
      return Reflect.set(target, key, value, receiver);
    } finally {
      resetNamespace();
    }
  },
  defineProperty(target, key, desc) {
    const isNew = !(key in target);
    const result = Reflect.defineProperty(target, key, desc);
    if (result) {
      const receiver = getProxyFromObject(target);
      const prop = getPropSignal(receiver, key);
      const { get, value } = desc;
      if (get) {
        prop.setGetter(get);
      } else {
        const ns = getNamespaceFromProxy(receiver);
        prop.setValue(
          shouldProxy(value) ? proxifyState(ns, value) : value
        );
      }
      if (isNew && objToIterable.has(target)) {
        objToIterable.get(target).value++;
      }
      if (Array.isArray(target) && proxyToProps.get(receiver)?.has("length")) {
        const length = getPropSignal(receiver, "length");
        length.setValue(target.length);
      }
    }
    return result;
  },
  deleteProperty(target, key) {
    const result = Reflect.deleteProperty(target, key);
    if (result) {
      const prop = getPropSignal(getProxyFromObject(target), key);
      prop.setValue(void 0);
      if (objToIterable.has(target)) {
        objToIterable.get(target).value++;
      }
    }
    return result;
  },
  ownKeys(target) {
    if (!objToIterable.has(target)) {
      objToIterable.set(target, d3(0));
    }
    objToIterable._ = objToIterable.get(target).value;
    return Reflect.ownKeys(target);
  }
};
var proxifyState = (namespace, obj) => {
  return createProxy(namespace, obj, stateHandlers);
};
var peek = (obj, key) => {
  peeking = true;
  try {
    return obj[key];
  } finally {
    peeking = false;
  }
};
var deepMergeRecursive = (target, source, override = true) => {
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
    if (typeof desc.get === "function" || typeof desc.set === "function") {
      if (override || isNew) {
        Object.defineProperty(target, key, {
          ...desc,
          configurable: true,
          enumerable: true
        });
        if (desc.get && propSignal) {
          propSignal.setPendingGetter(desc.get);
        }
      }
    } else if (isPlainObject(source[key])) {
      const targetValue = Object.getOwnPropertyDescriptor(target, key)?.value;
      if (isNew || override && !isPlainObject(targetValue)) {
        target[key] = {};
        if (propSignal) {
          const ns = getNamespaceFromProxy(proxy);
          propSignal.setValue(
            proxifyState(ns, target[key])
          );
        }
        deepMergeRecursive(target[key], source[key], override);
      } else if (isPlainObject(targetValue)) {
        deepMergeRecursive(target[key], source[key], override);
      }
    } else if (override || isNew) {
      Object.defineProperty(target, key, desc);
      if (propSignal) {
        const { value } = desc;
        const ns = getNamespaceFromProxy(proxy);
        propSignal.setValue(
          shouldProxy(value) ? proxifyState(ns, value) : value
        );
      }
    }
  }
  if (hasNewKeys && objToIterable.has(target)) {
    objToIterable.get(target).value++;
  }
};
var deepMerge = (target, source, override = true) => r3(
  () => deepMergeRecursive(
    getObjectFromProxy(target) || target,
    source,
    override
  )
);

// packages/interactivity/build-module/proxies/store.js
var storeRoots = /* @__PURE__ */ new WeakSet();
var storeHandlers = {
  get: (target, key, receiver) => {
    const result = Reflect.get(target, key);
    const ns = getNamespaceFromProxy(receiver);
    if (typeof result === "undefined" && storeRoots.has(receiver)) {
      const obj = {};
      Reflect.set(target, key, obj);
      return proxifyStore(ns, obj, false);
    }
    if (typeof result === "function") {
      setNamespace(ns);
      const scoped = withScope(result);
      resetNamespace();
      return scoped;
    }
    if (isPlainObject(result) && shouldProxy(result)) {
      return proxifyStore(ns, result, false);
    }
    return result;
  }
};
var proxifyStore = (namespace, obj, isRoot = true) => {
  const proxy = createProxy(namespace, obj, storeHandlers);
  if (proxy && isRoot) {
    storeRoots.add(proxy);
  }
  return proxy;
};

// packages/interactivity/build-module/proxies/context.js
var contextObjectToProxy = /* @__PURE__ */ new WeakMap();
var contextObjectToFallback = /* @__PURE__ */ new WeakMap();
var contextProxies = /* @__PURE__ */ new WeakSet();
var descriptor = Reflect.getOwnPropertyDescriptor;
var contextHandlers = {
  get: (target, key) => {
    const fallback = contextObjectToFallback.get(target);
    const currentProp = target[key];
    return key in target ? currentProp : fallback[key];
  },
  set: (target, key, value) => {
    const fallback = contextObjectToFallback.get(target);
    const obj = key in target || !(key in fallback) ? target : fallback;
    obj[key] = value;
    return true;
  },
  ownKeys: (target) => [
    .../* @__PURE__ */ new Set([
      ...Object.keys(contextObjectToFallback.get(target)),
      ...Object.keys(target)
    ])
  ],
  getOwnPropertyDescriptor: (target, key) => descriptor(target, key) || descriptor(contextObjectToFallback.get(target), key),
  has: (target, key) => Reflect.has(target, key) || Reflect.has(contextObjectToFallback.get(target), key)
};
var proxifyContext = (current, inherited = {}) => {
  if (contextProxies.has(current)) {
    throw Error("This object cannot be proxified.");
  }
  contextObjectToFallback.set(current, inherited);
  if (!contextObjectToProxy.has(current)) {
    const proxy = new Proxy(current, contextHandlers);
    contextObjectToProxy.set(current, proxy);
    contextProxies.add(proxy);
  }
  return contextObjectToProxy.get(current);
};

// packages/interactivity/build-module/store.js
var stores = /* @__PURE__ */ new Map();
var rawStores = /* @__PURE__ */ new Map();
var storeLocks = /* @__PURE__ */ new Map();
var storeConfigs = /* @__PURE__ */ new Map();
var serverStates = /* @__PURE__ */ new Map();
var getConfig = (namespace) => storeConfigs.get(namespace || getNamespace()) || {};
function getServerState(namespace) {
  const ns = namespace || getNamespace();
  if (!serverStates.has(ns)) {
    serverStates.set(ns, deepReadOnly({}));
  }
  getServerState.subscribe = navigationSignal.value;
  return serverStates.get(ns);
}
getServerState.subscribe = 0;
var universalUnlock = "I acknowledge that using a private store means my plugin will inevitably break on the next store release.";
function store(namespace, { state = {}, ...block } = {}, { lock = false } = {}) {
  if (!stores.has(namespace)) {
    if (lock !== universalUnlock) {
      storeLocks.set(namespace, lock);
    }
    const rawStore = {
      state: proxifyState(
        namespace,
        isPlainObject(state) ? state : {}
      ),
      ...block
    };
    const proxifiedStore = proxifyStore(namespace, rawStore);
    rawStores.set(namespace, rawStore);
    stores.set(namespace, proxifiedStore);
  } else {
    if (lock !== universalUnlock && !storeLocks.has(namespace)) {
      storeLocks.set(namespace, lock);
    } else {
      const storeLock = storeLocks.get(namespace);
      const isLockValid = lock === universalUnlock || lock !== true && lock === storeLock;
      if (!isLockValid) {
        if (!storeLock) {
          throw Error("Cannot lock a public store");
        } else {
          throw Error(
            "Cannot unlock a private store with an invalid lock code"
          );
        }
      }
    }
    const target = rawStores.get(namespace);
    deepMerge(target, block);
    deepMerge(target.state, state);
  }
  return stores.get(namespace);
}
var parseServerData = (dom = document) => {
  const jsonDataScriptTag = (
    // Preferred Script Module data passing form
    dom.getElementById(
      "wp-script-module-data-@wordpress/interactivity"
    ) ?? // Legacy form
    dom.getElementById("wp-interactivity-data")
  );
  if (jsonDataScriptTag?.textContent) {
    try {
      return JSON.parse(jsonDataScriptTag.textContent);
    } catch {
    }
  }
  return {};
};
var populateServerData = (data2) => {
  serverStates.clear();
  storeConfigs.clear();
  if (isPlainObject(data2?.state)) {
    Object.entries(data2.state).forEach(([namespace, state]) => {
      const st = store(namespace, {}, { lock: universalUnlock });
      deepMerge(st.state, state, false);
      serverStates.set(namespace, deepReadOnly(state));
    });
  }
  if (isPlainObject(data2?.config)) {
    Object.entries(data2.config).forEach(([namespace, config]) => {
      storeConfigs.set(namespace, config);
    });
  }
  if (isPlainObject(data2?.derivedStateClosures)) {
    Object.entries(data2.derivedStateClosures).forEach(
      ([namespace, paths]) => {
        const st = store(
          namespace,
          {},
          { lock: universalUnlock }
        );
        paths.forEach((path) => {
          const pathParts = path.split(".");
          const prop = pathParts.splice(-1, 1)[0];
          const parent = pathParts.reduce(
            (prev, key) => peek(prev, key),
            st
          );
          const desc = Object.getOwnPropertyDescriptor(
            parent,
            prop
          );
          if (isPlainObject(desc?.value)) {
            parent[prop] = PENDING_GETTER;
          }
        });
      }
    );
  }
  navigationSignal.value += 1;
};
var data = parseServerData();
populateServerData(data);

// packages/interactivity/build-module/hooks.js
function isNonDefaultDirectiveSuffix(entry) {
  return entry.suffix !== null;
}
function isDefaultDirectiveSuffix(entry) {
  return entry.suffix === null;
}
var context = G({ client: {}, server: {} });
var directiveCallbacks = {};
var directivePriorities = {};
var directive = (name, callback, { priority = 10 } = {}) => {
  directiveCallbacks[name] = callback;
  directivePriorities[name] = priority;
};
var resolve = (path, namespace) => {
  if (!namespace) {
    warn(
      `Namespace missing for "${path}". The value for that path won't be resolved.`
    );
    return;
  }
  let resolvedStore = stores.get(namespace);
  if (typeof resolvedStore === "undefined") {
    resolvedStore = store(
      namespace,
      {},
      {
        lock: universalUnlock
      }
    );
  }
  const current = {
    ...resolvedStore,
    context: getScope().context[namespace]
  };
  try {
    const pathParts = path.split(".");
    return pathParts.reduce((acc, key) => acc[key], current);
  } catch (e4) {
    if (e4 === PENDING_GETTER) {
      return PENDING_GETTER;
    }
  }
};
var getEvaluate = ({ scope }) => (
  // TODO: When removing the temporarily remaining `value( ...args )` call below, remove the `...args` parameter too.
  ((entry, ...args) => {
    let { value: path, namespace } = entry;
    if (typeof path !== "string") {
      throw new Error("The `value` prop should be a string path");
    }
    const hasNegationOperator = path[0] === "!" && !!(path = path.slice(1));
    setScope(scope);
    const value = resolve(path, namespace);
    if (typeof value === "function") {
      if (hasNegationOperator) {
        warn(
          "Using a function with a negation operator is deprecated and will stop working in WordPress 6.9. Please use derived state instead."
        );
        const functionResult = !value(...args);
        resetScope();
        return functionResult;
      }
      resetScope();
      const wrappedFunction = (...functionArgs) => {
        setScope(scope);
        const functionResult = value(...functionArgs);
        resetScope();
        return functionResult;
      };
      if (value.sync) {
        const syncAwareFunction = wrappedFunction;
        syncAwareFunction.sync = true;
      }
      return wrappedFunction;
    }
    const result = value;
    resetScope();
    return hasNegationOperator && value !== PENDING_GETTER ? !result : result;
  })
);
var getPriorityLevels = (directives) => {
  const byPriority = Object.keys(directives).reduce((obj, name) => {
    if (directiveCallbacks[name]) {
      const priority = directivePriorities[name];
      (obj[priority] = obj[priority] || []).push(name);
    }
    return obj;
  }, {});
  return Object.entries(byPriority).sort(([p1], [p22]) => parseInt(p1) - parseInt(p22)).map(([, arr]) => arr);
};
var Directives = ({
  directives,
  priorityLevels: [currentPriorityLevel, ...nextPriorityLevels],
  element,
  originalProps,
  previousScope
}) => {
  const scope = A2({}).current;
  scope.evaluate = q2(getEvaluate({ scope }), []);
  const { client, server } = x2(context);
  scope.context = client;
  scope.serverContext = server;
  scope.ref = previousScope?.ref || A2(null);
  element = E(element, { ref: scope.ref });
  scope.attributes = element.props;
  const children = nextPriorityLevels.length > 0 ? _(Directives, {
    directives,
    priorityLevels: nextPriorityLevels,
    element,
    originalProps,
    previousScope: scope
  }) : element;
  const props = { ...originalProps, children };
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
    if (wrapper !== void 0) {
      props.children = wrapper;
    }
  }
  resetScope();
  return props.children;
};
var old = l.vnode;
l.vnode = (vnode) => {
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

// packages/interactivity/build-module/directives.js
var warnUniqueIdWithTwoHyphens = (prefix, suffix, uniqueId) => {
  if (true) {
    warn(
      `The usage of data-wp-${prefix}--${suffix}${uniqueId ? `--${uniqueId}` : ""} (two hyphens for unique ID) is deprecated and will stop working in WordPress 7.0. Please use data-wp-${prefix}${uniqueId ? `--${suffix}---${uniqueId}` : `---${suffix}`} (three hyphens for unique ID) from now on.`
    );
  }
};
var warnUniqueIdNotSupported = (prefix, uniqueId) => {
  if (true) {
    warn(
      `Unique IDs are not supported for the data-wp-${prefix} directive. Ignoring the directive with unique ID "${uniqueId}".`
    );
  }
};
var warnWithSyncEvent = (wrongPrefix, rightPrefix) => {
  if (true) {
    warn(
      `The usage of data-wp-${wrongPrefix} is deprecated and will stop working in WordPress 7.0. Please, use data-wp-${rightPrefix} with the withSyncEvent() helper from now on.`
    );
  }
};
function deepClone(source) {
  if (isPlainObject(source)) {
    return Object.fromEntries(
      Object.entries(source).map(([key, value]) => [
        key,
        deepClone(value)
      ])
    );
  }
  if (Array.isArray(source)) {
    return source.map((i6) => deepClone(i6));
  }
  return source;
}
function wrapEventAsync(event) {
  const handler = {
    get(target, prop, receiver) {
      const value = target[prop];
      switch (prop) {
        case "currentTarget":
          if (true) {
            warn(
              `Accessing the synchronous event.${prop} property in a store action without wrapping it in withSyncEvent() is deprecated and will stop working in WordPress 7.0. Please wrap the store action in withSyncEvent().`
            );
          }
          break;
        case "preventDefault":
        case "stopImmediatePropagation":
        case "stopPropagation":
          if (true) {
            warn(
              `Using the synchronous event.${prop}() function in a store action without wrapping it in withSyncEvent() is deprecated and will stop working in WordPress 7.0. Please wrap the store action in withSyncEvent().`
            );
          }
          break;
      }
      if (value instanceof Function) {
        return function(...args) {
          return value.apply(
            this === receiver ? target : this,
            args
          );
        };
      }
      return value;
    }
  };
  return new Proxy(event, handler);
}
var newRule = /(?:([\u0080-\uFFFF\w-%@]+) *:? *([^{;]+?);|([^;}{]*?) *{)|(}\s*)/g;
var ruleClean = /\/\*[^]*?\*\/|  +/g;
var ruleNewline = /\n+/g;
var empty = " ";
var cssStringToObject = (val) => {
  const tree = [{}];
  let block, left;
  while (block = newRule.exec(val.replace(ruleClean, ""))) {
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
var getGlobalEventDirective = (type) => {
  return ({ directives, evaluate }) => {
    directives[`on-${type}`].filter(isNonDefaultDirectiveSuffix).forEach((entry) => {
      const suffixParts = entry.suffix.split("--", 2);
      const eventName = suffixParts[0];
      if (true) {
        if (suffixParts[1]) {
          warnUniqueIdWithTwoHyphens(
            `on-${type}`,
            suffixParts[0],
            suffixParts[1]
          );
        }
      }
      useInit(() => {
        const cb = (event) => {
          const result = evaluate(entry);
          if (typeof result === "function") {
            if (!result?.sync) {
              event = wrapEventAsync(event);
            }
            result(event);
          }
        };
        const globalVar = type === "window" ? window : document;
        globalVar.addEventListener(eventName, cb);
        return () => globalVar.removeEventListener(eventName, cb);
      });
    });
  };
};
var evaluateItemKey = (inheritedValue, namespace, item, itemProp, eachKey) => {
  const clientContextWithItem = {
    ...inheritedValue.client,
    [namespace]: {
      ...inheritedValue.client[namespace],
      [itemProp]: item
    }
  };
  const scope = {
    ...getScope(),
    context: clientContextWithItem,
    serverContext: inheritedValue.server
  };
  return eachKey ? getEvaluate({ scope })(eachKey) : item;
};
var useItemContexts = function* (inheritedValue, namespace, items, itemProp, eachKey) {
  const { current: itemContexts } = A2(/* @__PURE__ */ new Map());
  for (const item of items) {
    const key = evaluateItemKey(
      inheritedValue,
      namespace,
      item,
      itemProp,
      eachKey
    );
    if (!itemContexts.has(key)) {
      itemContexts.set(
        key,
        proxifyContext(
          proxifyState(namespace, {
            // Inits the item prop in the context to shadow it in case
            // it was inherited from the parent context. The actual
            // value is set in the `wp-each` directive later on.
            [itemProp]: void 0
          }),
          inheritedValue.client[namespace]
        )
      );
    }
    yield [item, itemContexts.get(key), key];
  }
};
var getGlobalAsyncEventDirective = (type) => {
  return ({ directives, evaluate }) => {
    directives[`on-async-${type}`].filter(isNonDefaultDirectiveSuffix).forEach((entry) => {
      if (true) {
        warnWithSyncEvent(`on-async-${type}`, `on-${type}`);
      }
      const eventName = entry.suffix.split("--", 1)[0];
      useInit(() => {
        const cb = async (event) => {
          await splitTask();
          const result = evaluate(entry);
          if (typeof result === "function") {
            result(event);
          }
        };
        const globalVar = type === "window" ? window : document;
        globalVar.addEventListener(eventName, cb, {
          passive: true
        });
        return () => globalVar.removeEventListener(eventName, cb);
      });
    });
  };
};
var routerRegions = /* @__PURE__ */ new Map();
var directives_default = () => {
  directive(
    "context",
    ({
      directives: { context: context2 },
      props: { children },
      context: inheritedContext
    }) => {
      const entries = context2.filter(isDefaultDirectiveSuffix).reverse();
      if (!entries.length) {
        if (true) {
          warn(
            "The usage of data-wp-context--unique-id (two hyphens) is not supported. To add a unique ID to the directive, please use data-wp-context---unique-id (three hyphens) instead."
          );
        }
        return;
      }
      const { Provider } = inheritedContext;
      const { client: inheritedClient, server: inheritedServer } = x2(inheritedContext);
      const client = A2({});
      const server = {};
      const result = {
        client: { ...inheritedClient },
        server: { ...inheritedServer }
      };
      const namespaces2 = /* @__PURE__ */ new Set();
      entries.forEach(({ value, namespace, uniqueId }) => {
        if (!isPlainObject(value)) {
          if (true) {
            warn(
              `The value of data-wp-context${uniqueId ? `---${uniqueId}` : ""} on the ${namespace} namespace must be a valid stringified JSON object.`
            );
          }
          return;
        }
        if (!client.current[namespace]) {
          client.current[namespace] = proxifyState(namespace, {});
        }
        deepMerge(
          client.current[namespace],
          deepClone(value),
          false
        );
        server[namespace] = deepReadOnly(value);
        namespaces2.add(namespace);
      });
      namespaces2.forEach((namespace) => {
        result.client[namespace] = proxifyContext(
          client.current[namespace],
          inheritedClient[namespace]
        );
        result.server[namespace] = proxifyContext(
          server[namespace],
          inheritedServer[namespace]
        );
      });
      return _(Provider, { value: result }, children);
    },
    { priority: 5 }
  );
  directive("watch", ({ directives: { watch }, evaluate }) => {
    watch.forEach((entry) => {
      if (true) {
        if (entry.suffix) {
          warnUniqueIdWithTwoHyphens("watch", entry.suffix);
        }
      }
      useWatch(() => {
        let start;
        if (true) {
          if (true) {
            start = performance.now();
          }
        }
        let result = evaluate(entry);
        if (typeof result === "function") {
          result = result();
        }
        if (true) {
          if (true) {
            performance.measure(
              `interactivity api watch ${entry.namespace}`,
              {
                start,
                end: performance.now(),
                detail: {
                  devtools: {
                    track: `IA: watch ${entry.namespace}`
                  }
                }
              }
            );
          }
        }
        return result;
      });
    });
  });
  directive("init", ({ directives: { init: init2 }, evaluate }) => {
    init2.forEach((entry) => {
      if (true) {
        if (entry.suffix) {
          warnUniqueIdWithTwoHyphens("init", entry.suffix);
        }
      }
      useInit(() => {
        let start;
        if (true) {
          if (true) {
            start = performance.now();
          }
        }
        let result = evaluate(entry);
        if (typeof result === "function") {
          result = result();
        }
        if (true) {
          if (true) {
            performance.measure(
              `interactivity api init ${entry.namespace}`,
              {
                start,
                end: performance.now(),
                detail: {
                  devtools: {
                    track: `IA: init ${entry.namespace}`
                  }
                }
              }
            );
          }
        }
        return result;
      });
    });
  });
  directive("on", ({ directives: { on }, element, evaluate }) => {
    const events = /* @__PURE__ */ new Map();
    on.filter(isNonDefaultDirectiveSuffix).forEach((entry) => {
      const suffixParts = entry.suffix.split("--", 2);
      if (true) {
        if (suffixParts[1]) {
          warnUniqueIdWithTwoHyphens(
            "on",
            suffixParts[0],
            suffixParts[1]
          );
        }
      }
      if (!events.has(suffixParts[0])) {
        events.set(suffixParts[0], /* @__PURE__ */ new Set());
      }
      events.get(suffixParts[0]).add(entry);
    });
    events.forEach((entries, eventType) => {
      const existingHandler = element.props[`on${eventType}`];
      element.props[`on${eventType}`] = (event) => {
        if (existingHandler) {
          existingHandler(event);
        }
        entries.forEach((entry) => {
          let start;
          if (true) {
            if (true) {
              start = performance.now();
            }
          }
          const result = evaluate(entry);
          if (typeof result === "function") {
            if (!result?.sync) {
              event = wrapEventAsync(event);
            }
            result(event);
          }
          if (true) {
            if (true) {
              performance.measure(
                `interactivity api on ${entry.namespace}`,
                {
                  start,
                  end: performance.now(),
                  detail: {
                    devtools: {
                      track: `IA: on ${entry.namespace}`
                    }
                  }
                }
              );
            }
          }
        });
      };
    });
  });
  directive(
    "on-async",
    ({ directives: { "on-async": onAsync }, element, evaluate }) => {
      if (true) {
        warnWithSyncEvent("on-async", "on");
      }
      const events = /* @__PURE__ */ new Map();
      onAsync.filter(isNonDefaultDirectiveSuffix).forEach((entry) => {
        const event = entry.suffix.split("--", 1)[0];
        if (!events.has(event)) {
          events.set(event, /* @__PURE__ */ new Set());
        }
        events.get(event).add(entry);
      });
      events.forEach((entries, eventType) => {
        const existingHandler = element.props[`on${eventType}`];
        element.props[`on${eventType}`] = (event) => {
          if (existingHandler) {
            existingHandler(event);
          }
          entries.forEach(async (entry) => {
            await splitTask();
            const result = evaluate(entry);
            if (typeof result === "function") {
              result(event);
            }
          });
        };
      });
    }
  );
  directive("on-window", getGlobalEventDirective("window"));
  directive("on-document", getGlobalEventDirective("document"));
  directive("on-async-window", getGlobalAsyncEventDirective("window"));
  directive(
    "on-async-document",
    getGlobalAsyncEventDirective("document")
  );
  directive(
    "class",
    ({ directives: { class: classNames }, element, evaluate }) => {
      classNames.filter(isNonDefaultDirectiveSuffix).forEach((entry) => {
        const className = entry.uniqueId ? `${entry.suffix}---${entry.uniqueId}` : entry.suffix;
        let result = evaluate(entry);
        if (result === PENDING_GETTER) {
          return;
        }
        if (typeof result === "function") {
          result = result();
        }
        const currentClass = element.props.class || "";
        const classFinder = new RegExp(
          `(^|\\s)${className}(\\s|$)`,
          "g"
        );
        if (!result) {
          element.props.class = currentClass.replace(classFinder, " ").trim();
        } else if (!classFinder.test(currentClass)) {
          element.props.class = currentClass ? `${currentClass} ${className}` : className;
        }
        useInit(() => {
          if (!result) {
            element.ref.current.classList.remove(className);
          } else {
            element.ref.current.classList.add(className);
          }
        });
      });
    }
  );
  directive("style", ({ directives: { style }, element, evaluate }) => {
    style.filter(isNonDefaultDirectiveSuffix).forEach((entry) => {
      if (entry.uniqueId) {
        if (true) {
          warnUniqueIdNotSupported("style", entry.uniqueId);
        }
        return;
      }
      const styleProp = entry.suffix;
      let result = evaluate(entry);
      if (result === PENDING_GETTER) {
        return;
      }
      if (typeof result === "function") {
        result = result();
      }
      element.props.style = element.props.style || {};
      if (typeof element.props.style === "string") {
        element.props.style = cssStringToObject(element.props.style);
      }
      if (!result) {
        delete element.props.style[styleProp];
      } else {
        element.props.style[styleProp] = result;
      }
      useInit(() => {
        if (!result) {
          element.ref.current.style.removeProperty(styleProp);
        } else {
          element.ref.current.style.setProperty(styleProp, result);
        }
      });
    });
  });
  directive("bind", ({ directives: { bind }, element, evaluate }) => {
    bind.filter(isNonDefaultDirectiveSuffix).forEach((entry) => {
      if (entry.uniqueId) {
        if (true) {
          warnUniqueIdNotSupported("bind", entry.uniqueId);
        }
        return;
      }
      const attribute = entry.suffix;
      let result = evaluate(entry);
      if (result === PENDING_GETTER) {
        return;
      }
      if (typeof result === "function") {
        result = result();
      }
      element.props[attribute] = result;
      useInit(() => {
        const el = element.ref.current;
        if (attribute === "style") {
          if (typeof result === "string") {
            el.style.cssText = result;
          }
          return;
        } else if (attribute !== "width" && attribute !== "height" && attribute !== "href" && attribute !== "list" && attribute !== "form" && /*
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
        attribute !== "tabIndex" && attribute !== "download" && attribute !== "rowSpan" && attribute !== "colSpan" && attribute !== "role" && attribute in el) {
          try {
            el[attribute] = result === null || result === void 0 ? "" : result;
            return;
          } catch (err) {
          }
        }
        if (result !== null && result !== void 0 && (result !== false || attribute[4] === "-")) {
          el.setAttribute(attribute, result);
        } else {
          el.removeAttribute(attribute);
        }
      });
    });
  });
  directive(
    "ignore",
    ({
      element: {
        type: Type,
        props: { innerHTML, ...rest }
      }
    }) => {
      if (true) {
        warn(
          "The data-wp-ignore directive is deprecated and will be removed in version 7.0."
        );
      }
      const cached = T2(() => innerHTML, []);
      return _(Type, {
        dangerouslySetInnerHTML: { __html: cached },
        ...rest
      });
    }
  );
  directive("text", ({ directives: { text }, element, evaluate }) => {
    const entries = text.filter(isDefaultDirectiveSuffix);
    if (!entries.length) {
      if (true) {
        warn(
          "The usage of data-wp-text--suffix is not supported. Please use data-wp-text instead."
        );
      }
      return;
    }
    entries.forEach((entry) => {
      if (entry.uniqueId) {
        if (true) {
          warnUniqueIdNotSupported("text", entry.uniqueId);
        }
        return;
      }
      try {
        let result = evaluate(entry);
        if (result === PENDING_GETTER) {
          return;
        }
        if (typeof result === "function") {
          result = result();
        }
        element.props.children = typeof result === "object" ? null : result.toString();
      } catch (e4) {
        element.props.children = null;
      }
    });
  });
  directive("run", ({ directives: { run }, evaluate }) => {
    run.forEach((entry) => {
      if (true) {
        if (entry.suffix) {
          warnUniqueIdWithTwoHyphens("run", entry.suffix);
        }
      }
      let result = evaluate(entry);
      if (typeof result === "function") {
        result = result();
      }
      return result;
    });
  });
  directive(
    "each",
    ({
      directives: { each, "each-key": eachKey },
      context: inheritedContext,
      element,
      evaluate
    }) => {
      if (element.type !== "template") {
        if (true) {
          warn(
            "The data-wp-each directive can only be used on <template> elements."
          );
        }
        return;
      }
      const { Provider } = inheritedContext;
      const inheritedValue = x2(inheritedContext);
      const [entry] = each;
      const { namespace, suffix, uniqueId } = entry;
      if (each.length > 1) {
        if (true) {
          warn(
            "The usage of multiple data-wp-each directives on the same element is not supported. Please pick only one."
          );
        }
        return;
      }
      if (uniqueId) {
        if (true) {
          warnUniqueIdNotSupported("each", uniqueId);
        }
        return;
      }
      let iterable = evaluate(entry);
      if (iterable === PENDING_GETTER) {
        return;
      }
      if (typeof iterable === "function") {
        iterable = iterable();
      }
      if (typeof iterable?.[Symbol.iterator] !== "function") {
        return;
      }
      const itemProp = suffix ? kebabToCamelCase(suffix) : "item";
      const result = [];
      const itemContexts = useItemContexts(
        inheritedValue,
        namespace,
        iterable,
        itemProp,
        eachKey?.[0]
      );
      for (const [item, itemContext, key] of itemContexts) {
        const mergedContext = {
          client: {
            ...inheritedValue.client,
            [namespace]: itemContext
          },
          server: { ...inheritedValue.server }
        };
        mergedContext.client[namespace][itemProp] = item;
        result.push(
          _(
            Provider,
            { value: mergedContext, key },
            element.props.content
          )
        );
      }
      return result;
    },
    { priority: 20 }
  );
  directive(
    "each-child",
    ({ directives: { "each-child": eachChild }, element, evaluate }) => {
      const entry = eachChild.find(isDefaultDirectiveSuffix);
      if (!entry) {
        return;
      }
      const iterable = evaluate(entry);
      return iterable === PENDING_GETTER ? element : null;
    },
    { priority: 1 }
  );
  directive(
    "router-region",
    ({ directives: { "router-region": routerRegion } }) => {
      const entry = routerRegion.find(isDefaultDirectiveSuffix);
      if (!entry) {
        return;
      }
      if (entry.suffix) {
        if (true) {
          warn(
            `Suffixes for the data-wp-router-region directive are not supported. Ignoring the directive with suffix "${entry.suffix}".`
          );
        }
        return;
      }
      if (entry.uniqueId) {
        if (true) {
          warnUniqueIdNotSupported("router-region", entry.uniqueId);
        }
        return;
      }
      const regionId = typeof entry.value === "string" ? entry.value : entry.value.id;
      if (!routerRegions.has(regionId)) {
        routerRegions.set(regionId, d3());
      }
      const vdom = routerRegions.get(regionId).value;
      if (vdom && typeof vdom.type !== "string") {
        const previousScope = getScope();
        return E(vdom, { previousScope });
      }
      return vdom;
    },
    { priority: 1 }
  );
};

// packages/interactivity/build-module/init.js
init_preact_module();

// packages/interactivity/build-module/vdom.js
init_preact_module();
var directivePrefix = `data-wp-`;
var namespaces = [];
var currentNamespace = () => namespaces[namespaces.length - 1] ?? null;
var isObject = (item) => Boolean(item && typeof item === "object" && item.constructor === Object);
var invalidCharsRegex = /[^a-z0-9-_]/i;
function parseDirectiveName(directiveName) {
  const name = directiveName.substring(8);
  if (invalidCharsRegex.test(name)) {
    return null;
  }
  const suffixIndex = name.indexOf("--");
  if (suffixIndex === -1) {
    return { prefix: name, suffix: null, uniqueId: null };
  }
  const prefix = name.substring(0, suffixIndex);
  const remaining = name.substring(suffixIndex);
  if (remaining.startsWith("---") && remaining[3] !== "-") {
    return {
      prefix,
      suffix: null,
      uniqueId: remaining.substring(3) || null
    };
  }
  let suffix = remaining.substring(2);
  const uniqueIdIndex = suffix.indexOf("---");
  if (uniqueIdIndex !== -1 && suffix.substring(uniqueIdIndex)[3] !== "-") {
    const uniqueId = suffix.substring(uniqueIdIndex + 3) || null;
    suffix = suffix.substring(0, uniqueIdIndex) || null;
    return { prefix, suffix, uniqueId };
  }
  return { prefix, suffix: suffix || null, uniqueId: null };
}
var nsPathRegExp = /^([\w_\/-]+)::(.+)$/;
var hydratedIslands = /* @__PURE__ */ new WeakSet();
function toVdom(root) {
  const nodesToRemove = /* @__PURE__ */ new Set();
  const nodesToReplace = /* @__PURE__ */ new Set();
  const treeWalker = document.createTreeWalker(
    root,
    205
    // TEXT + CDATA_SECTION + COMMENT + PROCESSING_INSTRUCTION + ELEMENT
  );
  function walk(node) {
    const { nodeType } = node;
    if (nodeType === 3) {
      return node.data;
    }
    if (nodeType === 4) {
      nodesToReplace.add(node);
      return node.nodeValue;
    }
    if (nodeType === 8 || nodeType === 7) {
      nodesToRemove.add(node);
      return null;
    }
    const elementNode = node;
    const { attributes } = elementNode;
    const localName = elementNode.localName;
    const props = {};
    const children = [];
    const directives = [];
    let ignore = false;
    let island = false;
    for (let i6 = 0; i6 < attributes.length; i6++) {
      const attributeName = attributes[i6].name;
      const attributeValue = attributes[i6].value;
      if (attributeName[directivePrefix.length] && attributeName.slice(0, directivePrefix.length) === directivePrefix) {
        if (attributeName === "data-wp-ignore") {
          ignore = true;
        } else {
          const regexResult = nsPathRegExp.exec(attributeValue);
          const namespace = regexResult?.[1] ?? null;
          let value = regexResult?.[2] ?? attributeValue;
          try {
            const parsedValue = JSON.parse(value);
            value = isObject(parsedValue) ? parsedValue : value;
          } catch {
          }
          if (attributeName === "data-wp-interactive") {
            island = true;
            const islandNamespace = (
              // eslint-disable-next-line no-nested-ternary
              typeof value === "string" ? value : typeof value?.namespace === "string" ? value.namespace : null
            );
            namespaces.push(islandNamespace);
          } else {
            directives.push([attributeName, namespace, value]);
          }
        }
      } else if (attributeName === "ref") {
        continue;
      }
      props[attributeName] = attributeValue;
    }
    if (ignore && !island) {
      return [
        _(localName, {
          ...props,
          innerHTML: elementNode.innerHTML,
          __directives: { ignore: true }
        })
      ];
    }
    if (island) {
      hydratedIslands.add(elementNode);
    }
    if (directives.length) {
      props.__directives = directives.reduce((obj, [name, ns, value]) => {
        const directiveParsed = parseDirectiveName(name);
        if (directiveParsed === null) {
          if (true) {
            warn(`Found malformed directive name: ${name}.`);
          }
          return obj;
        }
        const { prefix, suffix, uniqueId } = directiveParsed;
        obj[prefix] = obj[prefix] || [];
        obj[prefix].push({
          namespace: ns ?? currentNamespace(),
          value,
          suffix,
          uniqueId
        });
        return obj;
      }, {});
      for (const prefix in props.__directives) {
        props.__directives[prefix].sort(
          (a5, b4) => {
            const aSuffix = a5.suffix ?? "";
            const bSuffix = b4.suffix ?? "";
            if (aSuffix !== bSuffix) {
              return aSuffix < bSuffix ? -1 : 1;
            }
            const aId = a5.uniqueId ?? "";
            const bId = b4.uniqueId ?? "";
            return +(aId > bId) - +(aId < bId);
          }
        );
      }
    }
    if (props.__directives?.["each-child"]) {
      props.dangerouslySetInnerHTML = {
        __html: elementNode.innerHTML
      };
    } else if (localName === "template") {
      props.content = [
        ...elementNode.content.childNodes
      ].map((childNode) => toVdom(childNode));
    } else {
      let child = treeWalker.firstChild();
      if (child) {
        while (child) {
          const vnode = walk(child);
          if (vnode) {
            children.push(vnode);
          }
          child = treeWalker.nextSibling();
        }
        treeWalker.parentNode();
      }
    }
    if (island) {
      namespaces.pop();
    }
    return _(localName, props, children);
  }
  const vdom = walk(treeWalker.currentNode);
  nodesToRemove.forEach(
    (node) => node.remove()
  );
  nodesToReplace.forEach(
    (node) => node.replaceWith(
      new window.Text(node.nodeValue ?? "")
    )
  );
  return vdom;
}

// packages/interactivity/build-module/init.js
var regionRootFragments = /* @__PURE__ */ new WeakMap();
var getRegionRootFragment = (regions) => {
  const region = Array.isArray(regions) ? regions[0] : regions;
  if (!region.parentElement) {
    throw Error("The passed region should be an element with a parent.");
  }
  if (!regionRootFragments.has(region)) {
    regionRootFragments.set(
      region,
      createRootFragment(region.parentElement, regions)
    );
  }
  return regionRootFragments.get(region);
};
var initialVdom = /* @__PURE__ */ new WeakMap();
var init = async () => {
  const nodes = document.querySelectorAll(`[data-wp-interactive]`);
  await new Promise((resolve2) => {
    setTimeout(resolve2, 0);
  });
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

// packages/interactivity/build-module/index.js
if (true) {
  await Promise.resolve().then(() => (init_debug_module(), debug_module_exports));
}
var requiredConsent = "I acknowledge that using private APIs means my theme or plugin will inevitably break in the next version of WordPress.";
var privateApis = (lock) => {
  if (lock === requiredConsent) {
    return {
      getRegionRootFragment,
      initialVdom,
      toVdom,
      directive,
      getNamespace,
      h: _,
      cloneElement: E,
      render: B,
      proxifyState,
      parseServerData,
      populateServerData,
      batch: r3,
      routerRegions,
      deepReadOnly,
      navigationSignal
    };
  }
  throw new Error("Forbidden access.");
};
directives_default();
init();
export {
  getConfig,
  getContext,
  getElement,
  getServerContext,
  getServerState,
  privateApis,
  splitTask,
  store,
  useCallback,
  useEffect,
  useInit,
  useLayoutEffect,
  useMemo,
  A2 as useRef,
  h2 as useState,
  useWatch,
  withScope,
  withSyncEvent
};
//# sourceMappingURL=index.js.map
