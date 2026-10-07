// packages/block-library/build-module/playlist/view.mjs
import { store, getContext, getElement } from "@wordpress/interactivity";

// node_modules/colord/index.mjs
for (r = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) }, t = function(r2) {
  return "string" == typeof r2 ? r2.length > 0 : "number" == typeof r2;
}, n = function(r2, t2, n2) {
  return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = Math.pow(10, t2)), Math.round(n2 * r2) / n2 + 0;
}, u = function(r2, t2, n2) {
  return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = 1), r2 > n2 ? n2 : r2 > t2 ? r2 : t2;
}, e2 = function(r2) {
  return (r2 = isFinite(r2) ? r2 % 360 : 0) < 0 ? r2 + 360 : r2;
}, o = function(r2, t2) {
  return void 0 === t2 && (t2 = 0), n(r2, t2) % 360;
}, a = function(r2) {
  return { r: u(r2.r, 0, 255), g: u(r2.g, 0, 255), b: u(r2.b, 0, 255), a: u(r2.a) };
}, i = function(r2) {
  return { r: n(r2.r), g: n(r2.g), b: n(r2.b), a: n(r2.a, 3) };
}, s = /^#([0-9a-f]{3,8})$/i, d = function(r2, t2) {
  var n2 = r2.charCodeAt(t2);
  return (15 & n2) + 9 * (n2 >> 6);
}, h = function(r2, t2) {
  return d(r2, t2) << 4 | d(r2, t2 + 1);
}, b = [], f = 0; f < 256; f++) b.push((f < 16 ? "0" : "") + f.toString(16));
var r;
var t;
var n;
var u;
var e2;
var o;
var a;
var i;
var s;
var d;
var h;
var b;
var f;
var g = function(r) {
  return b[u(r, 0, 255)];
};
var c = function(r) {
  var t = r.r, n = r.g, u = r.b, e2 = r.a, o = Math.max(t, n, u), a = o - Math.min(t, n, u), i = a ? o === t ? (n - u) / a : o === n ? 2 + (u - t) / a : 4 + (t - n) / a : 0;
  return { h: 60 * (i < 0 ? i + 6 : i), s: o ? a / o * 100 : 0, v: o / 255 * 100, a: e2 };
};
var v = function(r) {
  var t = r.h, n = r.s, u = r.v, e2 = r.a;
  t = t / 360 * 6, n /= 100, u /= 100;
  var o = Math.floor(t), a = u * (1 - n), i = u * (1 - (t - o) * n), s = u * (1 - (1 - t + o) * n), d = o % 6;
  return { r: 255 * [u, i, a, a, s, u][d], g: 255 * [s, u, u, i, a, a][d], b: 255 * [a, a, s, u, u, i][d], a: e2 };
};
var l = function(r) {
  return { h: e2(r.h), s: u(r.s, 0, 100), l: u(r.l, 0, 100), a: u(r.a) };
};
var p = function(r) {
  return { h: o(r.h), s: n(r.s), l: n(r.l), a: n(r.a, 3) };
};
var m = function(r) {
  return v((n = (t = r).s, { h: t.h, s: (n *= ((u = t.l) < 50 ? u : 100 - u) / 100) > 0 ? 2 * n / (u + n) * 100 : 0, v: u + n, a: t.a }));
  var t, n, u;
};
var y = function(r) {
  return { h: (t = c(r)).h, s: (e2 = (200 - (n = t.s)) * (u = t.v) / 100) > 0 && e2 < 200 ? n * u / 100 / (e2 <= 100 ? e2 : 200 - e2) * 100 : 0, l: e2 / 2, a: t.a };
  var t, n, u, e2;
};
var N = /^hsla?\(\s*([+-]?(?:\d*\.\d+|\d+))(deg|rad|grad|turn)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))%\s*,\s*([+-]?(?:\d*\.\d+|\d+))%\s*(?:,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
var x = /^hsla?\(\s*([+-]?(?:\d*\.\d+|\d+))(deg|rad|grad|turn)?\s+([+-]?(?:\d*\.\d+|\d+))%\s+([+-]?(?:\d*\.\d+|\d+))%\s*(?:\/\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
var M = /^rgba?\(\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*(?:,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
var H = /^rgba?\(\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s+([+-]?(?:\d*\.\d+|\d+))(%)?\s+([+-]?(?:\d*\.\d+|\d+))(%)?\s*(?:\/\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
var $ = { string: [[function(r) {
  if (!s.test(r)) return null;
  var t = r.length;
  return t <= 5 ? { r: 17 * d(r, 1), g: 17 * d(r, 2), b: 17 * d(r, 3), a: 5 === t ? n(17 * d(r, 4) / 255, 2) : 1 } : 7 === t || 9 === t ? { r: h(r, 1), g: h(r, 3), b: h(r, 5), a: 9 === t ? n(h(r, 7) / 255, 2) : 1 } : null;
}, "hex"], [function(r) {
  var t = M.exec(r) || H.exec(r);
  return t ? t[2] !== t[4] || t[4] !== t[6] ? null : a({ r: Number(t[1]) / (t[2] ? 100 / 255 : 1), g: Number(t[3]) / (t[4] ? 100 / 255 : 1), b: Number(t[5]) / (t[6] ? 100 / 255 : 1), a: void 0 === t[7] ? 1 : Number(t[7]) / (t[8] ? 100 : 1) }) : null;
}, "rgb"], [function(t) {
  var n = N.exec(t) || x.exec(t);
  if (!n) return null;
  var u, e2, o = l({ h: (u = n[1], e2 = n[2], void 0 === e2 && (e2 = "deg"), Number(u) * (r[e2] || 1)), s: Number(n[3]), l: Number(n[4]), a: void 0 === n[5] ? 1 : Number(n[5]) / (n[6] ? 100 : 1) });
  return m(o);
}, "hsl"]], object: [[function(r) {
  var n = r.r, u = r.g, e2 = r.b, o = r.a, i = void 0 === o ? 1 : o;
  return t(n) && t(u) && t(e2) ? a({ r: Number(n), g: Number(u), b: Number(e2), a: Number(i) }) : null;
}, "rgb"], [function(r) {
  var n = r.h, u = r.s, e2 = r.l, o = r.a, a = void 0 === o ? 1 : o;
  if (!t(n) || !t(u) || !t(e2)) return null;
  var i = l({ h: Number(n), s: Number(u), l: Number(e2), a: Number(a) });
  return m(i);
}, "hsl"], [function(r) {
  var n = r.h, o = r.s, a = r.v, i = r.a, s = void 0 === i ? 1 : i;
  if (!t(n) || !t(o) || !t(a)) return null;
  var d = (function(r2) {
    return { h: e2(r2.h), s: u(r2.s, 0, 100), v: u(r2.v, 0, 100), a: u(r2.a) };
  })({ h: Number(n), s: Number(o), v: Number(a), a: Number(s) });
  return v(d);
}, "hsv"]] };
var j = function(r, t) {
  for (var n = 0; n < t.length; n++) {
    var u = t[n][0](r);
    if (u) return [u, t[n][1]];
  }
  return [null, void 0];
};
var w = function(r) {
  return "string" == typeof r ? j(r.trim(), $.string) : "object" == typeof r && null !== r ? j(r, $.object) : [null, void 0];
};
var k = function(r, t) {
  var n = y(r);
  return { h: n.h, s: u(n.s + 100 * t, 0, 100), l: n.l, a: n.a };
};
var E = function(r) {
  return (299 * r.r + 587 * r.g + 114 * r.b) / 1e3 / 255;
};
var R = function(r, t) {
  var n = y(r);
  return { h: n.h, s: n.s, l: u(n.l + 100 * t, 0, 100), a: n.a };
};
var q = (function() {
  function r(r2) {
    this.parsed = w(r2)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
  }
  return r.prototype.isValid = function() {
    return null !== this.parsed;
  }, r.prototype.brightness = function() {
    return n(E(this.rgba), 2);
  }, r.prototype.isDark = function() {
    return E(this.rgba) < 0.5;
  }, r.prototype.isLight = function() {
    return E(this.rgba) >= 0.5;
  }, r.prototype.toHex = function() {
    return r2 = i(this.rgba), t = r2.r, u = r2.g, e2 = r2.b, a = (o = r2.a) < 1 ? g(n(255 * o)) : "", "#" + g(t) + g(u) + g(e2) + a;
    var r2, t, u, e2, o, a;
  }, r.prototype.toRgb = function() {
    return i(this.rgba);
  }, r.prototype.toRgbString = function() {
    return r2 = i(this.rgba), t = r2.r, n = r2.g, u = r2.b, (e2 = r2.a) < 1 ? "rgba(" + t + ", " + n + ", " + u + ", " + e2 + ")" : "rgb(" + t + ", " + n + ", " + u + ")";
    var r2, t, n, u, e2;
  }, r.prototype.toHsl = function() {
    return p(y(this.rgba));
  }, r.prototype.toHslString = function() {
    return r2 = p(y(this.rgba)), t = r2.h, n = r2.s, u = r2.l, (e2 = r2.a) < 1 ? "hsla(" + t + ", " + n + "%, " + u + "%, " + e2 + ")" : "hsl(" + t + ", " + n + "%, " + u + "%)";
    var r2, t, n, u, e2;
  }, r.prototype.toHsv = function() {
    return r2 = c(this.rgba), { h: o(r2.h), s: n(r2.s), v: n(r2.v), a: n(r2.a, 3) };
    var r2;
  }, r.prototype.invert = function() {
    return A({ r: 255 - (r2 = this.rgba).r, g: 255 - r2.g, b: 255 - r2.b, a: r2.a });
    var r2;
  }, r.prototype.saturate = function(r2) {
    return void 0 === r2 && (r2 = 0.1), A(k(this.rgba, r2));
  }, r.prototype.desaturate = function(r2) {
    return void 0 === r2 && (r2 = 0.1), A(k(this.rgba, -r2));
  }, r.prototype.grayscale = function() {
    return A(k(this.rgba, -1));
  }, r.prototype.lighten = function(r2) {
    return void 0 === r2 && (r2 = 0.1), A(R(this.rgba, r2));
  }, r.prototype.darken = function(r2) {
    return void 0 === r2 && (r2 = 0.1), A(R(this.rgba, -r2));
  }, r.prototype.rotate = function(r2) {
    return void 0 === r2 && (r2 = 15), this.hue(y(this.rgba).h + r2);
  }, r.prototype.alpha = function(r2) {
    return "number" == typeof r2 ? A({ r: (t = this.rgba).r, g: t.g, b: t.b, a: r2 }) : n(this.rgba.a, 3);
    var t;
  }, r.prototype.hue = function(r2) {
    var t = y(this.rgba);
    return "number" == typeof r2 ? A({ h: r2, s: t.s, l: t.l, a: t.a }) : o(t.h);
  }, r.prototype.isEqual = function(r2) {
    return this.toHex() === A(r2).toHex();
  }, r;
})();
var A = function(r) {
  return r instanceof q ? r : new q(r);
};

// node_modules/@arraypress/waveform-player/dist/waveform-player.esm.js
function $2(e2) {
  let t = -1 / 0;
  for (let i = 0; i < e2.length; i++) e2[i] > t && (t = e2[i]);
  return t;
}
function S(e2) {
  return String(e2 ?? "").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
}
function q2(e2) {
  return S(typeof e2 == "number" ? `${e2}px` : e2);
}
function st(e2) {
  if (typeof e2 != "string" || e2 === "") return false;
  try {
    let t = new URL(e2, "http://localhost/");
    return t.protocol === "http:" || t.protocol === "https:";
  } catch {
    return false;
  }
}
function m2(e2, t = 0, i = 1) {
  return Math.max(t, Math.min(e2, i));
}
function _(e2, t = null, i = {}) {
  let { min: s = -1 / 0, max: r = 1 / 0, integer: a = false } = i, o = typeof e2 == "number" ? e2 : typeof e2 == "string" && e2.trim() !== "" ? Number(e2) : NaN;
  if (!Number.isFinite(o)) return t;
  let n = m2(o, s, r);
  return a ? Math.round(n) : n;
}
function H2(e2, t = null) {
  if (Array.isArray(e2)) return e2;
  if (typeof e2 == "string" && e2.trim().startsWith("[")) try {
    let i = JSON.parse(e2);
    if (Array.isArray(i)) return i;
  } catch {
  }
  return t;
}
function D(e2, t = {}) {
  let { min: i = -1 / 0, max: s = 1 / 0, fallback: r = null } = t, a = H2(e2);
  if (!a && typeof e2 == "string" && e2.trim() !== "" && (a = e2.split(/[,\s]+/)), !a) return r;
  let o = a.map((n) => _(n)).filter((n) => n !== null && n >= i && n <= s);
  return o.length ? o : r;
}
function rt(e2, t, i = null) {
  return t.includes(e2) ? e2 : i;
}
function at(e2) {
  if (typeof e2 == "string") {
    let t = e2.trim().toLowerCase();
    return t !== "" && t !== "false" && t !== "0";
  }
  return !!e2;
}
function vt(e2) {
  return e2 === void 0 ? void 0 : e2 === "true";
}
function it(e2) {
  if (typeof e2 == "string" && e2.trim().startsWith("[")) try {
    return JSON.parse(e2);
  } catch {
  }
  return e2;
}
function O(e2) {
  let t = {}, i = (o, n = o) => {
    let l2 = vt(e2.dataset[n]);
    l2 !== void 0 && (t[o] = l2);
  }, s = (o, n = o, l2 = false) => {
    let h = e2.dataset[n];
    h && (t[o] = l2 ? parseFloat(h) : parseInt(h, 10));
  }, r = (o, n = o) => {
    let l2 = e2.dataset[n];
    l2 && (t[o] = /^\d+(\.\d+)?$/.test(l2.trim()) ? parseFloat(l2) : l2);
  }, a = (o, n = o) => {
    let l2 = e2.dataset[n];
    if (!l2) return;
    let h = H2(l2);
    h ? t[o] = h : console.warn(`[WaveformPlayer] Invalid ${n} attribute, expected a JSON array:`, l2);
  };
  if (e2.dataset.src && (t.url = e2.dataset.src), e2.dataset.url && (t.url = e2.dataset.url), s("height"), s("samples"), e2.dataset.preload && (t.preload = e2.dataset.preload), e2.dataset.crossOrigin && (t.crossOrigin = e2.dataset.crossOrigin), e2.dataset.audioMode && (t.audioMode = e2.dataset.audioMode), e2.dataset.style && (t.waveformStyle = e2.dataset.style), e2.dataset.waveformStyle && (t.waveformStyle = e2.dataset.waveformStyle), e2.dataset.waveformGradient && (t.waveformGradient = e2.dataset.waveformGradient), s("barWidth"), s("barSpacing"), s("barRadius"), e2.dataset.buttonAlign && (t.buttonAlign = e2.dataset.buttonAlign), e2.dataset.layout && (t.layout = e2.dataset.layout), e2.dataset.buttonStyle && (t.buttonStyle = e2.dataset.buttonStyle), r("buttonSize"), r("buttonRadius"), e2.dataset.colorPreset && (t.colorPreset = e2.dataset.colorPreset), e2.dataset.waveformColor && (t.waveformColor = it(e2.dataset.waveformColor)), e2.dataset.progressColor && (t.progressColor = it(e2.dataset.progressColor)), e2.dataset.color && (t.waveformColor = e2.dataset.color), e2.dataset.theme && (t.colorPreset = e2.dataset.theme), i("autoplay"), i("showControls"), i("showInfo"), i("showTime"), i("showHoverTime"), i("seekHandle"), i("showBPM", "showBpm"), s("bpm"), i("singlePlay"), i("playOnSeek"), e2.dataset.title && (t.title = e2.dataset.title), e2.dataset.artist && (t.artist = e2.dataset.artist), e2.dataset.album && (t.album = e2.dataset.album), e2.dataset.artwork && (t.artwork = e2.dataset.artwork), e2.dataset.artworkPosition && (t.artworkPosition = e2.dataset.artworkPosition), e2.dataset.waveform && (t.waveform = e2.dataset.waveform), a("markers"), s("playbackRate", "playbackRate", true), i("showPlaybackSpeed"), e2.dataset.playbackRates) {
    let o = D(e2.dataset.playbackRates);
    o ? t.playbackRates = o : console.warn("[WaveformPlayer] Invalid playbackRates attribute:", e2.dataset.playbackRates);
  }
  return i("enableMediaSession"), i("showMarkers"), i("accessibleSeek"), e2.dataset.seekLabel && (t.seekLabel = e2.dataset.seekLabel), e2.dataset.seekValueText && (t.seekValueText = e2.dataset.seekValueText), e2.dataset.errorText && (t.errorText = e2.dataset.errorText), e2.dataset.playPauseLabel && (t.playPauseLabel = e2.dataset.playPauseLabel), e2.dataset.speedLabel && (t.speedLabel = e2.dataset.speedLabel), e2.dataset.artworkAlt && (t.artworkAlt = e2.dataset.artworkAlt), e2.dataset.unknownTrackText && (t.unknownTrackText = e2.dataset.unknownTrackText), t;
}
function ot(e2, ...t) {
  let i = 0;
  return e2.replace(/%(?:(\d+)\$)?s/g, (s, r) => {
    let a = r ? Number(r) - 1 : i++;
    return t[a] ?? s;
  });
}
function E2(e2) {
  let t = Number(e2);
  if (!t || !Number.isFinite(t) || t < 0) return "0:00";
  let i = Math.floor(t / 3600), s = Math.floor(t % 3600 / 60), r = Math.floor(t % 60);
  return i > 0 ? `${i}:${s.toString().padStart(2, "0")}:${r.toString().padStart(2, "0")}` : `${s}:${r.toString().padStart(2, "0")}`;
}
var St = 0;
function nt(e2) {
  let t = e2 || "audio", i = 5381;
  for (let s = 0; s < t.length; s++) i = (i << 5) + i + t.charCodeAt(s) | 0;
  return `wp_${(i >>> 0).toString(36)}_${(St++).toString(36)}`;
}
function I(e2) {
  if (!e2) return "Audio";
  let t = e2.split("/");
  return t[t.length - 1].split(".")[0].replace(/[-_]/g, " ").replace(/\b\w/g, (r) => r.toUpperCase());
}
function U(e2) {
  if (typeof e2 != "string") return null;
  let t = e2.match(/rgba?\(\s*([\d.]+)\s*[,\s]\s*([\d.]+)\s*[,\s]\s*([\d.]+)\s*(?:[,/]\s*([\d.]+)(%?))?/i);
  if (!t) return null;
  let i = Number(t[1]), s = Number(t[2]), r = Number(t[3]);
  if (!Number.isFinite(i) || !Number.isFinite(s) || !Number.isFinite(r)) return null;
  let a = t[4] === void 0 ? 1 : Number(t[4]);
  return Number.isFinite(a) ? (t[5] === "%" && (a /= 100), { r: i, g: s, b: r, a: m2(a, 0, 1) }) : null;
}
function lt(e2) {
  let t = U(e2);
  return !t || t.a <= 0 ? null : (t.r * 299 + t.g * 587 + t.b * 114) / 1e3;
}
function j2(...e2) {
  let t = {};
  for (let i of e2) for (let s in i) i[s] !== null && i[s] !== void 0 && (t[s] = i[s]);
  return t;
}
function ht(e2, t) {
  let i;
  return function(...r) {
    let a = () => {
      clearTimeout(i), e2(...r);
    };
    clearTimeout(i), i = setTimeout(a, t);
  };
}
function B(e2, t) {
  if (e2.length === t) return e2;
  if (e2.length === 0 || t === 0) return [];
  let i = [];
  if (t > e2.length) {
    let s = (e2.length - 1) / (t - 1);
    for (let r = 0; r < t; r++) {
      let a = r * s, o = Math.floor(a), n = Math.ceil(a), l2 = a - o;
      if (n >= e2.length) i.push(e2[e2.length - 1]);
      else if (o === n) i.push(e2[o]);
      else {
        let h = e2[o] * (1 - l2) + e2[n] * l2;
        i.push(h);
      }
    }
  } else {
    let s = e2.length / t;
    for (let r = 0; r < t; r++) {
      let a = Math.floor(r * s), o = Math.floor((r + 1) * s), n = 0, l2 = 0;
      for (let h = a; h <= o && h < e2.length; h++) e2[h] > n && (n = e2[h]), l2++;
      if (l2 === 0) {
        let h = Math.min(Math.round(r * s), e2.length - 1);
        n = e2[h];
      }
      i.push(n);
    }
  }
  return i;
}
function P(e2, t, i, s) {
  if (!Array.isArray(t)) return t;
  if (t.length < 2) return t[0];
  let r = i.width, a = i.height, o = s && s.waveformGradient, [n, l2, h, c2] = o === "horizontal" ? [0, 0, r, 0] : o === "diagonal" ? [0, 0, r, a] : [0, 0, 0, a];
  try {
    let d = e2.createLinearGradient(n, l2, h, c2);
    return t.forEach((b, y2) => d.addColorStop(y2 / (t.length - 1), b)), d;
  } catch {
    return t[0];
  }
}
function x2(e2, t, i, s, r, a) {
  if ((Array.isArray(a) ? a.some((n) => n > 0) : a > 0) && typeof e2.roundRect == "function") {
    let n = Math.min(s / 2, Math.abs(r) / 2), l2 = (h) => m2(h, 0, n);
    e2.beginPath(), e2.roundRect(t, i, s, r, Array.isArray(a) ? a.map(l2) : l2(a)), e2.fill();
  } else e2.fillRect(t, i, s, r);
}
function pt(e2, t) {
  return (e2.barRadius || 0) * t;
}
function Et(e2, t) {
  let i = pt(e2, t);
  return [i, i, 0, 0];
}
function ct(e2, t, i, s, r) {
  let a = r / 2;
  e2.beginPath(), e2.moveTo(t, s - a), e2.lineTo(i - a, s - a), e2.arc(i - a, s, a, -Math.PI / 2, Math.PI / 2), e2.lineTo(t, s + a), e2.arc(t, s, a, Math.PI / 2, -Math.PI / 2), e2.closePath();
}
function V(e2, t, i, s, r) {
  let a = window.devicePixelRatio || 1, o = r.barWidth * a, n = r.barSpacing * a, l2 = Math.floor(t.width / (o + n)), h = B(i, l2), c2 = t.height, d = s * t.width, b = Et(r, a), y2 = P(e2, r.color, t, r), w2 = P(e2, r.progressColor, t, r);
  e2.clearRect(0, 0, t.width, t.height), e2.fillStyle = y2;
  for (let f = 0; f < h.length; f++) {
    let p2 = f * (o + n);
    if (p2 + o > t.width) break;
    let g2 = h[f] * c2 * 0.9, u = c2 - g2;
    x2(e2, p2, u, o, g2, b);
  }
  e2.save(), e2.beginPath(), e2.rect(0, 0, d, c2), e2.clip(), e2.fillStyle = w2;
  for (let f = 0; f < h.length; f++) {
    let p2 = f * (o + n);
    if (p2 > d) break;
    let g2 = h[f] * c2 * 0.9, u = c2 - g2;
    x2(e2, p2, u, o, g2, b);
  }
  e2.restore();
}
function Pt(e2, t, i, s, r) {
  let a = window.devicePixelRatio || 1, o = r.barWidth * a, n = r.barSpacing * a, l2 = Math.floor(t.width / (o + n)), h = B(i, l2), c2 = t.height, d = c2 / 2, b = s * t.width, y2 = pt(r, a), w2 = [y2, y2, 0, 0], f = [0, 0, y2, y2], p2 = P(e2, r.color, t, r), g2 = P(e2, r.progressColor, t, r);
  e2.clearRect(0, 0, t.width, t.height), e2.fillStyle = p2;
  for (let u = 0; u < h.length; u++) {
    let k2 = u * (o + n);
    if (k2 + o > t.width) break;
    let v2 = h[u] * c2 * 0.45;
    x2(e2, k2, d - v2, o, v2, w2), x2(e2, k2, d, o, v2, f);
  }
  e2.save(), e2.beginPath(), e2.rect(0, 0, b, c2), e2.clip(), e2.fillStyle = g2;
  for (let u = 0; u < h.length; u++) {
    let k2 = u * (o + n);
    if (k2 > b) break;
    let v2 = h[u] * c2 * 0.45;
    x2(e2, k2, d - v2, o, v2, w2), x2(e2, k2, d, o, v2, f);
  }
  e2.restore();
}
function Tt(e2, t, i, s, r) {
  let a = t.width, o = t.height, n = o / 2, l2 = o * 0.35;
  e2.clearRect(0, 0, a, o);
  let h = (c2, d, b = 1, y2 = false) => {
    let w2 = P(e2, c2, t, r), f = Array.isArray(c2) ? c2[c2.length - 1] : c2;
    y2 && (e2.shadowBlur = 12, e2.shadowColor = f), e2.strokeStyle = w2, e2.lineWidth = d, e2.lineCap = "round", e2.lineJoin = "round", e2.beginPath(), e2.moveTo(0, n);
    let p2 = [], g2 = Math.floor(i.length * b);
    for (let u = 0; u < g2; u++) {
      let k2 = u / (i.length - 1) * a, v2 = i[u], A2 = Math.sin(u * 0.1) * v2, L = n + A2 * l2;
      p2.push({ x: k2, y: L });
    }
    for (let u = 0; u < p2.length - 1; u++) {
      let k2 = p2[u].x + (p2[u + 1].x - p2[u].x) * 0.5, v2 = p2[u].y, A2 = p2[u + 1].x - (p2[u + 1].x - p2[u].x) * 0.5, L = p2[u + 1].y;
      e2.bezierCurveTo(k2, v2, A2, L, p2[u + 1].x, p2[u + 1].y);
    }
    e2.stroke(), y2 && (e2.shadowBlur = 0);
  };
  e2.strokeStyle = "rgba(255, 255, 255, 0.03)", e2.lineWidth = 0.5, e2.beginPath(), e2.moveTo(0, n), e2.lineTo(a, n), e2.stroke();
  for (let c2 = 0; c2 <= 10; c2++) {
    let d = a / 10 * c2;
    e2.beginPath(), e2.moveTo(d, 0), e2.lineTo(d, o), e2.stroke();
  }
  h(r.color, 2, 1, false), s > 0 && h(r.progressColor, 3, s, true);
}
function ut(e2, t, i, s, r) {
  let a = window.devicePixelRatio || 1, o = (r.barWidth || 3) * a, n = (r.barSpacing || 1) * a, l2 = Math.floor(t.width / (o + n)), h = B(i, l2), c2 = t.height, d = 4 * a, b = 2 * a, y2 = s * t.width, w2 = c2 / 2, f = P(e2, r.color, t, r), p2 = P(e2, r.progressColor, t, r);
  e2.clearRect(0, 0, t.width, t.height);
  for (let g2 = 0; g2 < h.length; g2++) {
    let u = g2 * (o + n);
    if (u + o > t.width) break;
    let k2 = h[g2] * c2 * 0.9, v2 = Math.floor(k2 / (d + b));
    e2.fillStyle = u < y2 ? p2 : f;
    for (let A2 = 0; A2 < v2; A2++) {
      let L = A2 * (d + b);
      e2.fillRect(u, w2 - L - d, o, d), A2 > 0 && e2.fillRect(u, w2 + L, o, d);
    }
  }
}
function dt(e2, t, i, s, r) {
  let a = window.devicePixelRatio || 1, o = (r.barWidth || 2) * a, n = (r.barSpacing || 3) * a, l2 = Math.floor(t.width / (o + n)), h = B(i, l2), c2 = t.height, d = Math.max(1.5 * a, o / 2), b = s * t.width, y2 = c2 / 2, w2 = P(e2, r.color, t, r), f = P(e2, r.progressColor, t, r);
  e2.clearRect(0, 0, t.width, t.height);
  for (let p2 = 0; p2 < h.length; p2++) {
    let g2 = p2 * (o + n) + o / 2;
    if (g2 > t.width) break;
    let u = h[p2] * c2 * 0.9;
    e2.fillStyle = g2 < b ? f : w2, e2.beginPath(), e2.arc(g2, y2 - u / 2, d, 0, Math.PI * 2), e2.fill(), e2.beginPath(), e2.arc(g2, y2 + u / 2, d, 0, Math.PI * 2), e2.fill();
  }
}
function At(e2, t, i, s, r) {
  let a = t.width, o = t.height, n = o / 2, l2 = 4, h = l2 / 2, c2 = !!r.seekActive;
  if (e2.clearRect(0, 0, a, o), e2.fillStyle = P(e2, r.color, t, r) || "rgba(255, 255, 255, 0.2)", ct(e2, h, a, n, l2), e2.fill(), s > 0) {
    let d = Math.max(h * 2, s * a);
    e2.save(), e2.globalAlpha = r.seekHandle && !c2 ? 0.7 : 1, e2.fillStyle = P(e2, r.progressColor, t, r) || "rgba(255, 255, 255, 0.9)", ct(e2, h, d, n, l2), e2.fill(), e2.restore();
  }
}
var Mt = { bars: V, bar: V, mirror: Pt, line: Tt, blocks: ut, block: ut, dots: dt, dot: dt, seekbar: At };
function ft(e2, t, i, s, r) {
  (Mt[r.waveformStyle] || V)(e2, t, i, s, r);
}
function mt(e2) {
  try {
    let t = e2.getChannelData(0), i = e2.sampleRate, s = Ct(t, i);
    if (s.length < 2) return 120;
    let r = [];
    for (let l2 = 1; l2 < s.length; l2++) r.push((s[l2] - s[l2 - 1]) / i);
    let a = {};
    r.forEach((l2) => {
      let h = 60 / l2, c2 = Math.round(h / 3) * 3;
      c2 > 60 && c2 < 200 && (a[c2] = (a[c2] || 0) + 1);
    });
    let o = 0, n = 120;
    for (let [l2, h] of Object.entries(a)) h > o && (o = h, n = parseInt(l2));
    return n < 70 && a[n * 2] ? n *= 2 : n > 160 && a[Math.round(n / 2)] && (n = Math.round(n / 2)), n - 1;
  } catch (t) {
    return console.warn("[WaveformPlayer] BPM detection failed:", t), null;
  }
}
function Ct(e2, t) {
  let r = [], a = 0;
  for (let o = 0; o < e2.length - 2048; o += 1024) {
    let n = 0;
    for (let c2 = o; c2 < o + 2048; c2++) n += e2[c2] * e2[c2];
    n = n / 2048;
    let l2 = n - a, h = a * 1.8 + 0.01;
    if (l2 > h && n > 0.01) {
      let c2 = r[r.length - 1] || 0, d = t * 0.15;
      o - c2 > d && r.push(o);
    }
    a = n * 0.8 + a * 0.2;
  }
  return r;
}
function Lt(e2, t = 1800) {
  let i = e2.length / t, s = e2.numberOfChannels, r = [];
  for (let o = 0; o < s; o++) {
    let n = e2.getChannelData(o);
    for (let l2 = 0; l2 < t; l2++) {
      let h = ~~(l2 * i), c2 = ~~(h + i), d = 0, b = 0;
      for (let w2 = h; w2 < c2; w2++) {
        let f = n[w2];
        f > b && (b = f), f < d && (d = f);
      }
      let y2 = Math.max(Math.abs(b), Math.abs(d));
      (o === 0 || y2 > r[l2]) && (r[l2] = y2);
    }
  }
  let a = $2(r);
  return a > 0 ? r.map((o) => o / a) : r;
}
async function G(e2, t = 1800, i = false) {
  let s;
  try {
    let r = window.AudioContext || window.webkitAudioContext;
    s = new r();
    let o = await (await fetch(e2)).arrayBuffer(), n = await s.decodeAudioData(o), l2 = Lt(n, t);
    l2 = _t(l2);
    let h = null;
    return i && (h = mt(n)), { peaks: l2, bpm: h };
  } finally {
    s && s.close();
  }
}
function yt(e2 = 1800) {
  let t = [];
  for (let i = 0; i < e2; i++) {
    let s = Math.random() * 0.5 + 0.3, r = Math.sin(i / e2 * Math.PI * 4) * 0.2;
    t.push(m2(s + r, 0.1, 1));
  }
  return t;
}
function _t(e2, t = 0.95) {
  let i = $2(e2);
  if (i === 0 || i > t) return e2;
  let s = t / i;
  return e2.map((r) => r * s);
}
var K = 128;
function gt(e2) {
  let t = document.documentElement, i = document.body;
  return t.classList.contains(e2) || t.classList.contains(`${e2}-mode`) || t.classList.contains(`theme-${e2}`) || t.getAttribute("data-theme") === e2 || t.getAttribute("data-color-scheme") === e2 || i.classList.contains(e2) || i.classList.contains(`${e2}-mode`) || i.getAttribute("data-theme") === e2;
}
function xt(e2) {
  let t = 0, i = 0;
  for (let s = e2; s && s.nodeType === 1 && i < 0.995; s = s.parentElement) {
    let r = U(getComputedStyle(s).backgroundColor);
    if (!r || r.a <= 0) continue;
    let a = r.a * (1 - i);
    t += (r.r * 299 + r.g * 587 + r.b * 114) / 1e3 * a, i += a;
  }
  return { sum: t, alpha: i };
}
function Rt() {
  let e2 = lt(getComputedStyle(document.body).color);
  if (e2 !== null) return e2 > K ? "dark" : "light";
  if (window.matchMedia) {
    if (window.matchMedia("(prefers-color-scheme: dark)").matches) return "dark";
    if (window.matchMedia("(prefers-color-scheme: light)").matches) return "light";
  }
  return "dark";
}
function R2(e2) {
  if (gt("dark")) return "dark";
  if (gt("light")) return "light";
  try {
    let t = e2 && e2.nodeType === 1 ? e2 : document.body, { sum: i, alpha: s } = xt(t), r = Rt(), a = i + (r === "dark" ? 0 : 255) * (1 - s);
    return a > K ? "light" : a < K ? "dark" : r;
  } catch {
    return "dark";
  }
}
var C = { dark: { waveformColor: "rgba(255, 255, 255, 0.3)", progressColor: "rgba(255, 255, 255, 0.9)" }, light: { waveformColor: "rgba(0, 0, 0, 0.2)", progressColor: "rgba(0, 0, 0, 0.8)" } };
function J(e2, t) {
  if (e2 && C[e2]) return C[e2];
  let i = R2(t);
  return C[i];
}
var W = { url: "", height: 64, samples: 1800, preload: "metadata", crossOrigin: null, audioMode: "self", playbackRate: 1, showPlaybackSpeed: false, playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2], buttonAlign: "auto", layout: "default", buttonStyle: "circle", buttonSize: null, buttonRadius: null, waveformStyle: "mirror", barWidth: 2, barSpacing: 0, barRadius: 1, waveformGradient: "vertical", colorPreset: null, waveformColor: null, progressColor: null, autoplay: false, showControls: true, showInfo: true, showTime: true, showHoverTime: false, seekHandle: false, showBPM: false, bpm: null, singlePlay: true, playOnSeek: true, enableMediaSession: true, markers: [], showMarkers: true, accessibleSeek: true, seekLabel: null, seekValueText: null, title: null, artist: null, artwork: null, artworkPosition: "info", album: "", errorText: "Unable to load audio", playPauseLabel: "Play/Pause", speedLabel: "Playback speed", artworkAlt: "Album artwork", unknownTrackText: "Unknown Track", playIcon: '<svg viewBox="0 0 24 24" width="16" height="16"><path d="M8 5v14l11-7z"/></svg>', pauseIcon: '<svg viewBox="0 0 24 24" width="16" height="16"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>', onLoad: null, onPlay: null, onPause: null, onEnd: null, onError: null, onTimeUpdate: null, onNextTrack: null, onPreviousTrack: null };
var X = { bars: { barWidth: 3, barSpacing: 1 }, mirror: { barWidth: 2, barSpacing: 2 }, line: { barWidth: 2, barSpacing: 0 }, blocks: { barWidth: 4, barSpacing: 2 }, dots: { barWidth: 3, barSpacing: 3 }, seekbar: { barWidth: 1, barSpacing: 0 } };
var Q = ["auto", "top", "center", "bottom"];
var N2 = 0.25;
var F = 4;
var Dt = { buttonAlign: Q, layout: ["default", "preview"], buttonStyle: ["circle", "minimal"], artworkPosition: ["info", "button"], waveformStyle: Object.keys(X), waveformGradient: ["vertical", "horizontal", "diagonal"], audioMode: ["self", "external"], preload: ["none", "metadata", "auto"], colorPreset: Object.keys(C), crossOrigin: ["anonymous", "use-credentials"] };
var Bt = { height: { min: 1, integer: true }, samples: { min: 1, integer: true }, barWidth: { min: 0 }, barSpacing: { min: 0 }, barRadius: { min: 0 }, bpm: { min: 1 }, playbackRate: { min: N2, max: F } };
var Ht = ["autoplay", "showControls", "showInfo", "showTime", "showHoverTime", "seekHandle", "showBPM", "singlePlay", "playOnSeek", "enableMediaSession", "showMarkers", "accessibleSeek", "showPlaybackSpeed"];
var Ot = ["onLoad", "onPlay", "onPause", "onEnd", "onError", "onTimeUpdate", "onNextTrack", "onPreviousTrack"];
function Y(e2, t) {
  console.warn(`[WaveformPlayer] Invalid ${e2} option, using default:`, t);
}
function z(e2) {
  let t = H2(e2);
  return t ? t.reduce((i, s) => {
    let r = s && typeof s == "object" ? _(s.time, null, { min: 0 }) : null;
    return r === null ? (Y("marker", s), i) : (i.push({ ...s, time: r, label: s.label == null ? "" : s.label }), i);
  }, []) : (e2 != null && Y("markers", e2), []);
}
function Z(e2) {
  let t = (s) => e2[s] != null, i = (s) => {
    Y(s, e2[s]), e2[s] = W[s];
  };
  for (let [s, r] of Object.entries(Bt)) {
    if (!t(s)) continue;
    let a = _(e2[s], null, r);
    a === null ? i(s) : e2[s] = a;
  }
  for (let [s, r] of Object.entries(Dt)) t(s) && rt(e2[s], r) === null && i(s);
  for (let s of Ht) e2[s] = at(e2[s]);
  for (let s of Ot) t(s) && typeof e2[s] != "function" && i(s);
  if (t("playbackRates")) {
    let s = D(e2.playbackRates, { min: N2, max: F, fallback: null });
    s === null ? i("playbackRates") : e2.playbackRates = s;
  }
  e2.markers = z(e2.markers);
  for (let s of ["buttonSize", "buttonRadius"]) {
    if (!t(s)) continue;
    let r = e2[s];
    (typeof r == "number" ? Number.isFinite(r) : typeof r == "string" && r.trim() !== "") || i(s);
  }
  for (let s of ["waveformColor", "progressColor"]) {
    if (!t(s)) continue;
    let r = e2[s];
    !(typeof r == "string" && r.trim() !== "") && !Array.isArray(r) && i(s);
  }
  return e2;
}
var It = "data:image/svg+xml," + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect width="24" height="24" rx="4" fill="#71717a" fill-opacity="0.15"/><g fill="none" stroke="#a1a1aa" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="17" r="2.2"/><circle cx="17" cy="15" r="2.2"/><path d="M10.2 17V7l9-1.6v9"/></g></svg>');
var bt = 5;
var wt = 10;
var Wt = 'button, a[href], input, [role="slider"]';
var T = class e {
  static instances = /* @__PURE__ */ new Map();
  static currentlyPlaying = null;
  constructor(t, i = {}) {
    if (this.container = typeof t == "string" ? document.querySelector(t) : t, !this.container) throw new Error("[WaveformPlayer] Container element not found");
    let s = O(this.container), r = { ...i };
    r.style && !r.waveformStyle && (r.waveformStyle = r.style), r.src && !r.url && (r.url = r.src), this.options = Z(j2(W, s, r));
    let a = J(this.options.colorPreset, this.container);
    this._autoTheme = this.options.colorPreset == null || !C[this.options.colorPreset], this._presetKeys = [], this._scheme = this.options.colorPreset && C[this.options.colorPreset] ? this.options.colorPreset : R2(this.container);
    for (let [n, l2] of Object.entries(a)) (this.options[n] === null || this.options[n] === void 0) && (this.options[n] = l2, this._presetKeys.push(n));
    let o = X[this.options.waveformStyle];
    o && (s.barWidth === void 0 && i.barWidth === void 0 && (this.options.barWidth = o.barWidth), s.barSpacing === void 0 && i.barSpacing === void 0 && (this.options.barSpacing = o.barSpacing)), this.audio = null, this.canvas = null, this.ctx = null, this.waveformData = [], this.progress = 0, this._activeMarkerIndex = -1, this._markerLabelTimer = null, this.isPlaying = false, this.isLoading = false, this.hasError = false, this.updateTimer = null, this.resizeObserver = null, this._ac = new AbortController(), this.id = this.container.id || nt(this.options.url), e.instances.set(this.id, this), e._watchTheme();
    try {
      this.init();
    } catch (n) {
      throw e.instances.delete(this.id), this._ac.abort(), n;
    }
    setTimeout(() => {
      this._emit("waveformplayer:ready", { player: this, url: this.options.url });
    }, 100);
  }
  _emit(t, i, s = false) {
    let r = new CustomEvent(t, { bubbles: true, cancelable: s, detail: i });
    return this.container.dispatchEvent(r), r;
  }
  _requestSeek(t) {
    this._emit("waveformplayer:request-seek", { ...this._buildTrackDetail(), percent: t }, true).defaultPrevented || (this.progress = t, this.drawWaveform?.());
  }
  init() {
    this.createDOM(), this.createAudio(), this.initPlaybackSpeed(), this.initKeyboardControls(), this.initSeekControl(), this.bindEvents(), this.setupResizeObserver(), requestAnimationFrame(() => {
      this.resizeCanvas(), this.options.url && this.load(this.options.url).then(() => {
        this.options.autoplay && this.play()?.catch(() => {
        });
      }).catch((t) => {
        console.error("[WaveformPlayer] Failed to load audio:", t);
      });
    });
  }
  createDOM() {
    this.container.innerHTML = "", this.container.className = "waveform-player";
    let t = Q.includes(this.options.buttonAlign) ? this.options.buttonAlign : "auto";
    t === "auto" && (this.options.waveformStyle === "bars" ? t = "bottom" : t = "center"), this.options.layout === "preview" && this.container.classList.add("waveform-layout-preview"), this.container.classList.toggle("waveform-theme-light", this._scheme === "light");
    let s = [];
    this.options.buttonSize != null && s.push(`--wfp-btn-size: ${q2(this.options.buttonSize)}`), this.options.buttonRadius != null && s.push(`--wfp-btn-radius: ${q2(this.options.buttonRadius)}`);
    let r = s.length ? ` style="${s.join("; ")};"` : "", a = this.options.artworkPosition === "button" && this.options.artwork, o = a ? `<img class="waveform-btn-artwork" src="${S(this.options.artwork)}" alt="" aria-hidden="true">` : "", n = this.options.showControls ? `
        <button class="waveform-btn${this.options.buttonStyle === "minimal" ? " waveform-btn-minimal" : ""}${a ? " waveform-btn-has-artwork" : ""}" aria-label="${S(this.options.playPauseLabel)}"${r}>
          ${o}
          <span class="waveform-icon-play">${this.options.playIcon}</span>
          <span class="waveform-icon-pause" style="display:none;">${this.options.pauseIcon}</span>
        </button>
        ` : "", l2 = this.options.showInfo ? `
      <div class="waveform-info">
        ${this.options.artworkPosition !== "button" && this.options.artwork ? `
          <img class="waveform-artwork" src="${S(this.options.artwork)}" alt="${S(this.options.artworkAlt)}" style="
            width: 40px;
            height: 40px;
            border-radius: 4px;
            object-fit: cover;
            flex-shrink: 0;
          ">
        ` : ""}
        <div class="waveform-text">
          <span class="waveform-title"></span>
          ${this.options.artist ? `<span class="waveform-artist">${S(this.options.artist)}</span>` : ""}
        </div>
        <div class="waveform-meta" style="display: flex; align-items: center; gap: 1rem;">
          ${this.options.showBPM ? `
            <span class="waveform-bpm" style="display: none;">
              <span class="bpm-value">--</span> BPM
            </span>
          ` : ""}
          ${this.options.showPlaybackSpeed ? `
            <div class="waveform-speed">
              <button class="speed-btn" aria-label="${S(this.options.speedLabel)}" aria-haspopup="menu" aria-expanded="false">
                <span class="speed-value">1x</span>
              </button>
              <div class="speed-menu" role="menu" aria-label="${S(this.options.speedLabel)}" style="display: none;">
                ${this.options.playbackRates.map((h) => `<button class="speed-option" role="menuitemradio" tabindex="-1" aria-checked="false" data-rate="${h}">${h}x</button>`).join("")}
              </div>
            </div>
          ` : ""}
          ${this.options.showTime ? `
            <span class="waveform-time">
              <span class="time-current">0:00</span> / <span class="time-total">0:00</span>
            </span>
          ` : ""}
        </div>
      </div>
        ` : "";
    this.container.innerHTML = `
  <div class="waveform-player-inner">
    <div class="waveform-body">
      <div class="waveform-track waveform-align-${t}">
        ${n}
        
        <div class="waveform-container">
          <canvas></canvas>
          <div class="waveform-markers"></div>
          <div class="waveform-loading" style="display:none;"></div>
          <div class="waveform-error" style="display:none;" role="alert">
            <span class="waveform-error-text">${S(this.options.errorText)}</span>
          </div>
        </div>
      </div>
      
      ${l2}
    </div>
  </div>
`, this.playBtn = this.container.querySelector(".waveform-btn"), this.canvas = this.container.querySelector("canvas"), this.ctx = this.canvas.getContext("2d"), this.titleEl = this.container.querySelector(".waveform-title"), this.artistEl = this.container.querySelector(".waveform-artist"), this.artworkEl = this.container.querySelector(".waveform-artwork, .waveform-btn-artwork"), this.bindArtworkFallback(this.artworkEl), this.currentTimeEl = this.container.querySelector(".time-current"), this.totalTimeEl = this.container.querySelector(".time-total"), this.bpmEl = this.container.querySelector(".waveform-bpm"), this.bpmValueEl = this.container.querySelector(".bpm-value"), this.loadingEl = this.container.querySelector(".waveform-loading"), this.errorEl = this.container.querySelector(".waveform-error"), this.markersContainer = this.container.querySelector(".waveform-markers"), this.speedBtn = this.container.querySelector(".speed-btn"), this.speedMenu = this.container.querySelector(".speed-menu"), this.resizeCanvas(), this.updateBPMDisplay();
  }
  bindArtworkFallback(t) {
    t && t.addEventListener("error", () => {
      t.src.startsWith("data:") || (t.src = It);
    }, { signal: this._ac.signal });
  }
  createArtworkElement() {
    let t = document.createElement("img");
    return t.className = "waveform-artwork", t.style.width = "40px", t.style.height = "40px", t.style.borderRadius = "4px", t.style.objectFit = "cover", t.style.flexShrink = "0", this.bindArtworkFallback(t), t;
  }
  createButtonArtworkElement() {
    let t = document.createElement("img");
    return t.className = "waveform-btn-artwork", t.alt = "", t.setAttribute("aria-hidden", "true"), this.bindArtworkFallback(t), t;
  }
  createArtistElement() {
    let t = document.createElement("span");
    return t.className = "waveform-artist", t;
  }
  syncArtist(t) {
    if (this.options.artist = t || null, !!this.options.showInfo) {
      if (!t) {
        this.artistEl?.remove(), this.artistEl = null;
        return;
      }
      if (!this.artistEl) {
        let i = this.container.querySelector(".waveform-title");
        if (!i) return;
        this.artistEl = this.createArtistElement(), i.after(this.artistEl);
      }
      this.artistEl.textContent = t, this.artistEl.style.display = "";
    }
  }
  syncButtonArtwork(t) {
    if (this.playBtn) {
      if (!t) {
        this.artworkEl?.remove(), this.artworkEl = null, this.playBtn.classList.remove("waveform-btn-has-artwork");
        return;
      }
      this.artworkEl || (this.artworkEl = this.createButtonArtworkElement(), this.playBtn.prepend(this.artworkEl)), this.artworkEl.src = t, this.playBtn.classList.add("waveform-btn-has-artwork");
    }
  }
  syncArtwork(t, i = "") {
    if (this.options.artwork = t || null, this.options.artworkAlt = i || "", this.options.artworkPosition === "button") {
      this.syncButtonArtwork(this.options.artwork);
      return;
    }
    if (this.options.showInfo) {
      if (!t) {
        this.artworkEl?.remove(), this.artworkEl = null;
        return;
      }
      if (!this.artworkEl) {
        let s = this.container.querySelector(".waveform-text");
        if (!s) return;
        this.artworkEl = this.createArtworkElement(), s.before(this.artworkEl);
      }
      this.artworkEl.src = t, this.artworkEl.alt = i || "";
    }
  }
  createAudio() {
    if (this.options.audioMode === "external") {
      this.audio = null;
      return;
    }
    this.audio = new Audio(), this.audio.preload = this.options.preload || "metadata", this.options.crossOrigin && (this.audio.crossOrigin = this.options.crossOrigin);
  }
  initPlaybackSpeed() {
    this.audio && this.options.playbackRate && this.options.playbackRate !== 1 && (this.audio.playbackRate = this.options.playbackRate), this.options.showPlaybackSpeed && this.initSpeedControls();
  }
  initSpeedControls() {
    let t = this.container.querySelector(".speed-btn"), i = this.container.querySelector(".speed-menu");
    if (!t || !i) return;
    let s = () => Array.from(i.querySelectorAll(".speed-option")), r = () => i.style.display !== "none", a = (l2) => {
      if (i.style.display = l2 ? "block" : "none", t.setAttribute("aria-expanded", l2 ? "true" : "false"), l2) {
        let h = s();
        (h.find((c2) => c2.getAttribute("aria-checked") === "true") || h[0])?.focus();
      }
    }, o = (l2) => {
      let h = s();
      h.length && h[(l2 + h.length) % h.length].focus();
    }, n = (l2) => {
      this.setPlaybackRate(parseFloat(l2.dataset.rate)), a(false), t.focus();
    };
    t.addEventListener("click", (l2) => {
      l2.stopPropagation(), a(!r());
    }, { signal: this._ac.signal }), document.addEventListener("click", () => a(false), { signal: this._ac.signal }), i.addEventListener("click", (l2) => {
      l2.stopPropagation();
      let h = l2.target.closest(".speed-option");
      h && n(h);
    }, { signal: this._ac.signal }), t.closest(".waveform-speed")?.addEventListener("keydown", (l2) => {
      let h = s(), c2 = h.indexOf(document.activeElement);
      if (!r()) {
        (l2.key === "ArrowDown" || l2.key === "ArrowUp") && document.activeElement === t && (l2.preventDefault(), a(true));
        return;
      }
      switch (l2.key) {
        case "ArrowDown":
          l2.preventDefault(), o(c2 < 0 ? 0 : c2 + 1);
          break;
        case "ArrowUp":
          l2.preventDefault(), o(c2 < 0 ? h.length - 1 : c2 - 1);
          break;
        case "Home":
          l2.preventDefault(), o(0);
          break;
        case "End":
          l2.preventDefault(), o(h.length - 1);
          break;
        case "Escape":
          l2.preventDefault(), a(false), t.focus();
          break;
        case "Tab":
          a(false);
          break;
      }
    }, { signal: this._ac.signal }), this.updateSpeedUI();
  }
  initKeyboardControls() {
    this.container.setAttribute("tabindex", "-1"), this.container.addEventListener("click", (t) => {
      t.target.closest(Wt) || (e.getAllInstances().forEach((i) => {
        i !== this && i.container.setAttribute("tabindex", "-1");
      }), this.container.setAttribute("tabindex", "0"), this.container.focus());
    }, { signal: this._ac.signal }), this.container.addEventListener("keydown", (t) => {
      if (document.activeElement !== this.container) return;
      let i = t.key, s = !!this.audio, r = s ? this.audio.currentTime : 0;
      if (s && i >= "0" && i <= "9") {
        t.preventDefault(), this.seekToPercent(parseInt(i) / 10);
        return;
      }
      let a = { " ": () => this.togglePlay() };
      s && (a.ArrowLeft = () => this.seekTo(m2(r - 5, 0, this.audio.duration)), a.ArrowRight = () => this.seekTo(m2(r + 5, 0, this.audio.duration)), a.ArrowUp = () => this.setVolume(m2(this.audio.volume + 0.1)), a.ArrowDown = () => this.setVolume(m2(this.audio.volume - 0.1)), a.m = a.M = () => this.audio.muted = !this.audio.muted), a[i] && (t.preventDefault(), a[i]());
    }, { signal: this._ac.signal });
  }
  initSeekControl() {
    this.options.accessibleSeek && (this.seekEl = this.container.querySelector(".waveform-container"), this.seekEl && (this.seekEl.setAttribute("role", "slider"), this.seekEl.setAttribute("tabindex", "0"), this.seekEl.setAttribute("aria-valuemin", "0"), this.applySeekLabel(), this.updateSeekAccessibility(), this.seekEl.addEventListener("keydown", (t) => {
      if (t.key === " " || t.key === "Spacebar") {
        t.preventDefault(), t.stopPropagation(), this.togglePlay();
        return;
      }
      let i = this.getSeekDuration();
      if (!i) return;
      let s = this.getSeekCurrentTime(), r;
      switch (t.key) {
        case "ArrowLeft":
        case "ArrowDown":
          r = s - bt;
          break;
        case "ArrowRight":
        case "ArrowUp":
          r = s + bt;
          break;
        case "PageDown":
          r = s - wt;
          break;
        case "PageUp":
          r = s + wt;
          break;
        case "Home":
          r = 0;
          break;
        case "End":
          r = i;
          break;
        default:
          return;
      }
      t.preventDefault(), t.stopPropagation(), this.seekToSeconds(r);
    }, { signal: this._ac.signal })));
  }
  getSeekDuration() {
    return this.options.audioMode === "external" ? this._extDuration || 0 : this.audio && Number.isFinite(this.audio.duration) ? this.audio.duration : 0;
  }
  getSeekCurrentTime() {
    return this.options.audioMode === "external" ? this.progress * (this._extDuration || 0) : this.audio && Number.isFinite(this.audio.currentTime) ? this.audio.currentTime : 0;
  }
  seekToSeconds(t) {
    let i = this.getSeekDuration();
    if (!i) return;
    let s = m2(t, 0, i);
    if (this.options.audioMode === "external") {
      this._requestSeek(s / i), this.updateSeekAccessibility();
      return;
    }
    this.seekTo(s);
  }
  applySeekLabel(t = this.options.title) {
    if (!this.seekEl) return;
    let i = this.options.seekLabel || t || "Seek";
    this.seekEl.setAttribute("aria-label", i);
  }
  updateSeekAccessibility() {
    if (!this.seekEl) return;
    let t = this.getSeekDuration(), i = Math.min(this.getSeekCurrentTime(), t);
    this.seekEl.setAttribute("aria-valuemax", String(Math.round(t))), this.seekEl.setAttribute("aria-valuenow", String(Math.round(i))), this.seekEl.setAttribute("aria-valuetext", ot(this.options.seekValueText || "%1$s of %2$s", E2(i), E2(t)));
  }
  initMediaSession() {
    if (!("mediaSession" in navigator) || !this.options.enableMediaSession || !this.audio) return;
    this._applyMediaMetadata(), navigator.mediaSession.setActionHandler("play", () => this.play()), navigator.mediaSession.setActionHandler("pause", () => this.pause()), navigator.mediaSession.setActionHandler("seekbackward", () => {
      this.seekTo(m2(this.audio.currentTime - 10, 0, this.audio.duration));
    }), navigator.mediaSession.setActionHandler("seekforward", () => {
      this.seekTo(m2(this.audio.currentTime + 10, 0, this.audio.duration));
    }), navigator.mediaSession.setActionHandler("seekto", (s) => {
      s.seekTime !== null && this.seekTo(s.seekTime);
    });
    let t = this.options.onNextTrack, i = this.options.onPreviousTrack;
    try {
      navigator.mediaSession.setActionHandler("nexttrack", typeof t == "function" ? () => t(this) : null);
    } catch {
    }
    try {
      navigator.mediaSession.setActionHandler("previoustrack", typeof i == "function" ? () => i(this) : null);
    } catch {
    }
  }
  _applyMediaMetadata() {
    !("mediaSession" in navigator) || !this.options.enableMediaSession || (navigator.mediaSession.metadata = new MediaMetadata({ title: this.options.title || this.options.unknownTrackText, artist: this.options.artist || "", album: this.options.album || "", artwork: this.options.artwork ? [{ src: this.options.artwork, sizes: "512x512", type: "image/jpeg" }] : [] }));
  }
  _updateMediaSession(t) {
    if (!(!("mediaSession" in navigator) || !this.options.enableMediaSession || !this.audio)) try {
      t === "playing" && this.initMediaSession(), navigator.mediaSession.playbackState = t;
      let i = this.audio.duration;
      navigator.mediaSession.setPositionState && i && isFinite(i) && navigator.mediaSession.setPositionState({ duration: i, playbackRate: this.audio.playbackRate || 1, position: m2(this.audio.currentTime, 0, i) });
    } catch {
    }
  }
  bindEvents() {
    this.playBtn && this.playBtn.addEventListener("click", () => this.togglePlay()), this.audio && (this.audio.addEventListener("loadstart", () => this.setLoading(true)), this.audio.addEventListener("loadedmetadata", () => this.onMetadataLoaded()), this.audio.addEventListener("canplay", () => this.setLoading(false)), this.audio.addEventListener("play", () => this.onPlay()), this.audio.addEventListener("pause", () => this.onPause()), this.audio.addEventListener("ended", () => this.onEnded()), this.audio.addEventListener("error", (i) => this.onError(i))), this.canvas.addEventListener("click", (i) => this.handleCanvasClick(i)), this._dragging = false, this._seekHover = false, this._handleNear = false, this.canvas.addEventListener("pointerenter", () => {
      this._seekHover = true, this.drawWaveform(), this._updateSeekHandle();
    }), this.canvas.addEventListener("pointerleave", () => {
      this._seekHover = false, this._handleNear = false, this._dragging || this._hideHoverTip(), this.drawWaveform(), this._updateSeekHandle();
    }), this.canvas.addEventListener("pointerdown", (i) => {
      if (!(i.pointerType === "mouse" && i.button !== 0)) {
        this._dragging = true;
        try {
          this.canvas.setPointerCapture(i.pointerId);
        } catch {
        }
        this._scrubTo(i.clientX);
      }
    }), this.canvas.addEventListener("pointermove", (i) => {
      if (this._dragging) {
        this._scrubTo(i.clientX);
        return;
      }
      let s = this.canvas.getBoundingClientRect();
      s.width && (this._handleNear = Math.abs(i.clientX - s.left - this.progress * s.width) <= 10, this._updateSeekHandle());
    });
    let t = (i) => {
      if (this._dragging) {
        this._dragging = false, this._suppressClick = true;
        try {
          this.canvas.releasePointerCapture(i.pointerId);
        } catch {
        }
        this._seekFromPointer(i.clientX), !this._seekHover && !this.options.showHoverTime && this._hideHoverTip(), this._updateSeekHandle();
      }
    };
    this.canvas.addEventListener("pointerup", t), this.canvas.addEventListener("pointercancel", t), this.setupHoverTime(), this.setupSeekHandle(), this.resizeHandler = ht(() => this.resizeCanvas(), 100), window.addEventListener("resize", this.resizeHandler);
  }
  setupResizeObserver() {
    "ResizeObserver" in window && (this.resizeObserver = new ResizeObserver(() => {
      this.resizeCanvas();
    }), this.canvas?.parentElement && this.resizeObserver.observe(this.canvas.parentElement));
  }
  async load(t) {
    try {
      this.setLoading(true), this.progress = 0, this.hasError = false, this.container.classList.remove("waveform-is-placeholder");
      let i = !!this.options.waveform;
      i && this.setWaveformData(this.options.waveform);
      let s = this.options.title || I(t);
      if (this.titleEl && (this.titleEl.textContent = s), this.applySeekLabel(s), this.audio && (this.audio.src = t, this.audio.preload !== "none" && await new Promise((r, a) => {
        let o = () => {
          this.audio.removeEventListener("loadedmetadata", o), this.audio.removeEventListener("error", n), r();
        }, n = (l2) => {
          this.audio.removeEventListener("loadedmetadata", o), this.audio.removeEventListener("error", n), a(l2);
        };
        this.audio.addEventListener("loadedmetadata", o), this.audio.addEventListener("error", n);
      })), !i) try {
        let r = await G(t, this.options.samples, this.options.showBPM);
        this.waveformData = r.peaks, r.bpm && (this.detectedBPM = r.bpm, this.updateBPMDisplay());
      } catch (r) {
        console.warn("[WaveformPlayer] Using placeholder waveform:", r), this.waveformData = yt(this.options.samples), this.container.classList.add("waveform-is-placeholder");
      }
      this.drawWaveform(), this.renderMarkers(), this.options.onLoad && this.options.onLoad(this);
    } catch (i) {
      this.onError(i);
    } finally {
      this.setLoading(false);
    }
  }
  async loadTrack(t, i = null, s = null, r = {}) {
    let a = Object.prototype.hasOwnProperty.call(r, "artwork"), o = Object.prototype.hasOwnProperty.call(r, "artworkAlt");
    this.isPlaying && this.pause(), this.audio && (this.audio.src = "", this.audio.load()), this.hasError = false, this.errorEl && (this.errorEl.style.display = "none"), this.canvas && (this.canvas.style.opacity = "1"), this.playBtn && (this.playBtn.disabled = false), this.progress = 0, this.waveformData = [], this.options = Z(j2(this.options, { url: t, title: i === null ? this.options.title : i, artist: s === null ? this.options.artist : s, ...r })), a && (this.options.artwork = r.artwork || null), o ? this.options.artworkAlt = r.artworkAlt || "" : a && (this.options.artworkAlt = this.options.artwork ? W.artworkAlt : ""), r.preload && this.audio && (this.audio.preload = this.options.preload), r.crossOrigin && this.audio && (this.audio.crossOrigin = this.options.crossOrigin), s !== null && this.syncArtist(s), (a || o) && this.syncArtwork(a ? r.artwork : this.options.artwork, o ? r.artworkAlt : this.options.artworkAlt), this.options.markers = r.markers ? z(r.markers) : [], this.options.waveform = r.waveform || null, await this.load(t), r.autoplay !== false && this.play()?.catch(() => {
    });
  }
  setWaveformData(t) {
    if (typeof t == "string" && t.trim().endsWith(".json")) {
      fetch(t.trim()).then((i) => i.json()).then((i) => {
        this.waveformData = Array.isArray(i) ? i : i.peaks || [], i.markers && !this.options.markers?.length && (this.options.markers = z(i.markers), this.renderMarkers()), this.drawWaveform();
      }).catch(() => {
      });
      return;
    }
    this.waveformData = D(t, { fallback: [] }), this.drawWaveform();
  }
  drawWaveform() {
    !this.ctx || this.waveformData.length === 0 || ft(this.ctx, this.canvas, this.waveformData, this.progress, { ...this.options, waveformStyle: this.options.waveformStyle || "bars", color: this.options.waveformColor, progressColor: this.options.progressColor, seekActive: this._seekHover || this._dragging });
  }
  resizeCanvas() {
    if (!this.canvas || this.isDestroying) return;
    let t = window.devicePixelRatio || 1, i = this.canvas.parentElement.getBoundingClientRect();
    this.canvas.width = i.width * t, this.canvas.height = this.options.height * t, this.canvas.parentElement.style.height = this.options.height + "px", this.drawWaveform();
  }
  renderMarkers() {
    if (!this.markersContainer || (this.markersContainer.innerHTML = "", this._activeMarkerIndex = -1, clearTimeout(this._markerLabelTimer), !this.options.showMarkers || !this.options.markers?.length)) return;
    let t = this.getSeekDuration();
    t && this.options.markers.forEach((i, s) => {
      if (i.time > t) {
        console.warn(`[WaveformPlayer] Marker "${i.label}" at ${i.time}s exceeds audio duration of ${t}s`);
        return;
      }
      let r = i.time / t * 100, a = document.createElement("button");
      a.className = "waveform-marker", a.style.left = `${r}%`, a.style.backgroundColor = i.color || "rgba(255, 255, 255, 0.5)", a.setAttribute("aria-label", i.label), a.setAttribute("data-time", i.time);
      let o = document.createElement("span");
      o.className = "waveform-marker-tooltip", o.textContent = i.label, a.appendChild(o), a.addEventListener("click", (n) => {
        n.stopPropagation(), this.seekTo(i.time), this.options.playOnSeek && !this.isPlaying && this.play();
      }), this.markersContainer.appendChild(a);
    });
  }
  setActiveMarker(t) {
    if (!this.markersContainer) return;
    this.markersContainer.querySelectorAll(".waveform-marker").forEach((s, r) => s.classList.toggle("active", r === t));
  }
  updateActiveMarker() {
    if (!this.markersContainer) return;
    let t = this.markersContainer.querySelectorAll(".waveform-marker");
    if (!t.length) return;
    let i = this.getSeekDuration(), s = i ? this.progress * i : 0, r = -1, a = -1 / 0;
    t.forEach((o, n) => {
      let l2 = parseFloat(o.getAttribute("data-time"));
      Number.isFinite(l2) && l2 <= s + 0.05 && l2 > a && (a = l2, r = n);
    }), r !== this._activeMarkerIndex && (this._activeMarkerIndex = r, this.setActiveMarker(r), clearTimeout(this._markerLabelTimer), t.forEach((o, n) => o.classList.toggle("show-label", n === r)), r >= 0 && (this._markerLabelTimer = setTimeout(() => {
      this.markersContainer?.querySelectorAll(".waveform-marker").forEach((o) => o.classList.remove("show-label"));
    }, 2500)));
  }
  setupHoverTime() {
    if (!this.seekEl) return;
    let t = document.createElement("div");
    t.className = "waveform-hover-time", t.setAttribute("aria-hidden", "true"), this.seekEl.appendChild(t), this.hoverTimeEl = t, this.options.showHoverTime && (this.seekEl.addEventListener("pointermove", (i) => {
      this._dragging || this._updateHoverTip(i.clientX);
    }), this.seekEl.addEventListener("pointerleave", () => {
      this._dragging || this._hideHoverTip();
    }));
  }
  _updateHoverTip(t) {
    let i = this.hoverTimeEl;
    if (!i) return;
    let s = this.getSeekDuration();
    if (!s) {
      i.style.opacity = "0";
      return;
    }
    let r = this.canvas.getBoundingClientRect(), a = m2((t - r.left) / r.width);
    i.textContent = E2(a * s), i.style.left = a * 100 + "%", i.style.opacity = "1";
  }
  _hideHoverTip() {
    this.hoverTimeEl && (this.hoverTimeEl.style.opacity = "0");
  }
  _scrubTo(t) {
    let i = this.canvas.getBoundingClientRect();
    if (!i.width) return;
    this.progress = m2((t - i.left) / i.width), this.drawWaveform(), this._updateSeekHandle();
    let s = this.getSeekDuration();
    s && this.currentTimeEl ? (this.currentTimeEl.textContent = E2(this.progress * s), this._hideHoverTip()) : this._updateHoverTip(t);
  }
  setupSeekHandle() {
    if (!this.options.seekHandle || this.options.waveformStyle !== "seekbar" || !this.seekEl) return;
    let t = document.createElement("div");
    t.className = "waveform-seek-handle", t.setAttribute("aria-hidden", "true"), this.seekEl.appendChild(t), this.seekHandleEl = t;
  }
  _updateSeekHandle() {
    let t = this.seekHandleEl;
    t && (t.style.left = this.progress * 100 + "%", t.classList.toggle("is-visible", this._seekHover || this._dragging), t.classList.toggle("is-active", this._dragging || this._handleNear));
  }
  handleCanvasClick(t) {
    if (this._suppressClick) {
      this._suppressClick = false;
      return;
    }
    this._seekFromPointer(t.clientX);
  }
  _seekFromPointer(t) {
    let i = this.canvas.getBoundingClientRect();
    if (!i.width) return;
    let s = m2((t - i.left) / i.width);
    if (this.options.audioMode === "external") {
      this._requestSeek(s);
      return;
    }
    !this.audio || !this.audio.duration || this.seekToPercent(s);
  }
  setLoading(t) {
    if (this.isLoading = t, this.loadingEl) {
      let i = t && this.waveformData.length === 0;
      this.loadingEl.style.display = i ? "block" : "none";
    }
    this.seekEl && this.seekEl.setAttribute("aria-busy", t ? "true" : "false");
  }
  onMetadataLoaded() {
    this.isDestroying || (this.totalTimeEl && (this.totalTimeEl.textContent = E2(this.audio.duration)), this.renderMarkers(), this.updateSeekAccessibility());
  }
  setPlayButtonState(t) {
    if (!this.playBtn) return;
    this.playBtn.classList.toggle("playing", t);
    let i = this.playBtn.querySelector(".waveform-icon-play"), s = this.playBtn.querySelector(".waveform-icon-pause");
    i && (i.style.display = t ? "none" : "flex"), s && (s.style.display = t ? "flex" : "none");
  }
  onPlay() {
    this.isDestroying || (this.isPlaying = true, this.setPlayButtonState(true), this.startSmoothUpdate(), this._updateMediaSession("playing"), this._emit("waveformplayer:play", { player: this, url: this.options.url }), this.options.onPlay && this.options.onPlay(this));
  }
  onPause() {
    this.isDestroying || (this.isPlaying = false, this.setPlayButtonState(false), this.stopSmoothUpdate(), this._updateMediaSession("paused"), this._emit("waveformplayer:pause", { player: this, url: this.options.url }), this.options.onPause && this.options.onPause(this));
  }
  onEnded() {
    if (this.isDestroying) return;
    let t = this.audio.duration;
    this.progress = 0, this.audio.currentTime = 0, this.drawWaveform(), this.currentTimeEl && (this.currentTimeEl.textContent = "0:00"), this._emit("waveformplayer:ended", { player: this, url: this.options.url, currentTime: t, duration: t }), this.onPause(), this.options.onEnd && this.options.onEnd(this);
  }
  onError(t) {
    this.isDestroying || (console.error("[WaveformPlayer] Audio error:", t), this.hasError = true, this.setLoading(false), this.errorEl && (this.errorEl.style.display = "flex"), this.canvas && (this.canvas.style.opacity = "0.2"), this.playBtn && (this.playBtn.disabled = true), this.options.onError && this.options.onError(t, this));
  }
  startSmoothUpdate() {
    this.stopSmoothUpdate();
    let t = () => {
      this.isPlaying && this.audio && this.audio.duration && (this.updateProgress(), this.updateTimer = requestAnimationFrame(t));
    };
    this.updateTimer = requestAnimationFrame(t);
  }
  stopSmoothUpdate() {
    this.updateTimer && (cancelAnimationFrame(this.updateTimer), this.updateTimer = null);
  }
  updateProgress() {
    if (!this.audio || !this.audio.duration || this._dragging) return;
    let t = this.audio.currentTime / this.audio.duration;
    Math.abs(t - this.progress) > 1e-3 && (this.progress = t, this.drawWaveform(), this._updateSeekHandle()), this.currentTimeEl && (this.currentTimeEl.textContent = E2(this.audio.currentTime)), this._emit("waveformplayer:timeupdate", { player: this, currentTime: this.audio.currentTime, duration: this.audio.duration, progress: this.progress, url: this.options.url }), this.options.onTimeUpdate && this.options.onTimeUpdate(this.audio.currentTime, this.audio.duration, this), this.updateActiveMarker(), this.updateSeekAccessibility();
  }
  updateBPMDisplay() {
    let t = this.options.bpm || this.detectedBPM;
    this.bpmEl && this.bpmValueEl && t && (this.bpmValueEl.textContent = Math.round(t), this.bpmEl.style.display = "inline-flex");
  }
  refreshTheme() {
    if (!this._autoTheme) return;
    this._scheme = R2(this.container);
    let t = J(this.options.colorPreset, this.container);
    for (let i of this._presetKeys || []) i in t && (this.options[i] = t[i]);
    this._applyThemeColors();
  }
  _applyThemeColors() {
    this.container.classList.toggle("waveform-theme-light", this._scheme === "light"), this.canvas && this.drawWaveform();
  }
  static _watchTheme() {
    if (e._themeWatch || typeof document > "u") return;
    let t = () => requestAnimationFrame(() => {
      e.instances.forEach((a) => {
        try {
          a.refreshTheme();
        } catch {
        }
      });
    }), i = { attributes: true, attributeFilter: ["class", "data-theme", "data-color-scheme", "style"] }, s = new MutationObserver(t);
    s.observe(document.documentElement, i), document.body && s.observe(document.body, i);
    let r = null;
    try {
      r = window.matchMedia("(prefers-color-scheme: dark)"), r.addEventListener("change", t);
    } catch {
    }
    e._themeWatch = { obs: s, mq: r, refresh: t };
  }
  updateSpeedUI() {
    if (!this.audio) return;
    let t = this.container.querySelector(".speed-value");
    if (t) {
      let i = this.audio.playbackRate;
      t.textContent = i === 1 ? "1x" : `${i}x`;
    }
    this.container.querySelectorAll(".speed-option").forEach((i) => {
      let s = parseFloat(i.dataset.rate) === this.audio.playbackRate;
      i.classList.toggle("active", s), i.setAttribute("aria-checked", s ? "true" : "false");
    });
  }
  play() {
    if (this.options.singlePlay && e.currentlyPlaying && e.currentlyPlaying !== this && e.currentlyPlaying.pause(), this.options.audioMode === "external") {
      this._emit("waveformplayer:request-play", this._buildTrackDetail(), true).defaultPrevented || (e.currentlyPlaying = this);
      return;
    }
    return e.currentlyPlaying = this, this.audio.play();
  }
  pause() {
    if (e.currentlyPlaying === this && (e.currentlyPlaying = null), this.options.audioMode === "external") {
      this._emit("waveformplayer:request-pause", this._buildTrackDetail(), true);
      return;
    }
    this.audio.pause();
  }
  _buildTrackDetail() {
    return { url: this.options.url, title: this.options.title, artist: this.options.artist, artwork: this.options.artwork, markers: this.options.markers, waveform: this.options.waveform, id: this.id, player: this };
  }
  setPlayingState(t) {
    let i = this.isPlaying;
    this.isPlaying = !!t, this.setPlayButtonState(this.isPlaying), this.isPlaying && !i ? (this.startSmoothUpdate?.(), this._emit("waveformplayer:play", { player: this, url: this.options.url }), this.options.onPlay && this.options.onPlay(this)) : !this.isPlaying && i && (this.stopSmoothUpdate?.(), this._emit("waveformplayer:pause", { player: this, url: this.options.url }), this.options.onPause && this.options.onPause(this));
  }
  setProgress(t, i) {
    !i || i <= 0 || (this.progress = m2(t / i), this.currentTimeEl && (this.currentTimeEl.textContent = E2(t)), this._extDuration = i, this.totalTimeEl && (!this.totalTimeEl.dataset._extSet || this.totalTimeEl.dataset._extDur !== String(i)) && (this.totalTimeEl.textContent = E2(i), this.totalTimeEl.dataset._extSet = "1", this.totalTimeEl.dataset._extDur = String(i)), this.drawWaveform?.(), this.updateActiveMarker(), this._emit("waveformplayer:timeupdate", { player: this, currentTime: t, duration: i, progress: this.progress, url: this.options.url }), this.options.onTimeUpdate && this.options.onTimeUpdate(t, i, this), this.progress >= 1 ? this._extEnded || (this._extEnded = true, this._emit("waveformplayer:ended", { player: this, url: this.options.url, currentTime: i, duration: i }), this.options.onEnd && this.options.onEnd(this)) : this._extEnded = false, this.updateSeekAccessibility());
  }
  togglePlay() {
    this.isPlaying ? this.pause() : this.play();
  }
  seekTo(t) {
    this.audio && this.audio.duration && (this.audio.currentTime = m2(t, 0, this.audio.duration), this.updateProgress());
  }
  seekToPercent(t) {
    this.audio && this.audio.duration && (this.audio.currentTime = this.audio.duration * m2(t), this.updateProgress());
  }
  setVolume(t) {
    let i = Number(t);
    this.audio && Number.isFinite(i) && (this.audio.volume = m2(i));
  }
  setPlaybackRate(t) {
    if (!this.audio) return;
    let i = _(t, null, { min: N2, max: F });
    i !== null && (this.audio.playbackRate = i, this.options.playbackRate = i, this.updateSpeedUI());
  }
  destroy() {
    this.isDestroying = true, this._emit("waveformplayer:destroy", { player: this, url: this.options.url }), this.pause(), this.stopSmoothUpdate(), clearTimeout(this._markerLabelTimer), this._ac?.abort(), this.resizeObserver && (this.resizeObserver.disconnect(), this.resizeObserver = null), this.resizeHandler && (window.removeEventListener("resize", this.resizeHandler), this.resizeHandler = null), e.instances.delete(this.id), e.currentlyPlaying === this && (e.currentlyPlaying = null), this.audio && (this.audio.pause(), this.audio.src = "", this.audio.load(), this.audio = null), this.container.innerHTML = "", delete this.container.dataset.waveformInitialized, this.canvas = null, this.ctx = null, this.playBtn = null, this.waveformData = [];
  }
  static getInstance(t) {
    if (typeof t == "string") {
      let i = this.instances.get(t);
      if (i) return i;
      let s = document.getElementById(t);
      if (s) return Array.from(this.instances.values()).find((r) => r.container === s);
    }
    if (t instanceof HTMLElement) return Array.from(this.instances.values()).find((i) => i.container === t);
  }
  static getAllInstances() {
    return Array.from(this.instances.values());
  }
  static destroyAll() {
    this.instances.forEach((t) => t.destroy()), this.instances.clear();
  }
  static async generateWaveformData(t, i = 1800) {
    try {
      return (await G(t, i)).peaks;
    } catch (s) {
      throw console.error("[WaveformPlayer] Failed to generate waveform:", s), s;
    }
  }
  static getPeaksUrl(t) {
    if (!t) return;
    let i = t.replace(/\.(mp3|wav|ogg|flac|m4a|aac)(\?[^#]*)?(#.*)?$/i, ".json$2$3");
    return i === t ? void 0 : i;
  }
};
T.utils = { formatTime: E2, extractTitleFromUrl: I, escapeHtml: S, isSafeHref: st, parseDataAttributes: O, detectColorScheme: R2 };
var et = () => typeof window < "u" && typeof document < "u";
var Nt = () => true;
function kt(e2) {
  if (!(e2.dataset.waveformInitialized === "true" || T.getInstance(e2))) try {
    new T(e2), e2.dataset.waveformInitialized = "true";
  } catch (t) {
    console.error("[WaveformPlayer] Failed to initialize:", t, e2);
  }
}
function tt(e2 = document) {
  if (!et()) return;
  let t = e2 || document;
  t.matches?.("[data-waveform-player]") && kt(t), t.querySelectorAll("[data-waveform-player]").forEach(kt);
}
et() && !Nt() && (document.readyState === "loading" ? document.addEventListener("DOMContentLoaded", () => tt()) : tt());
T.init = tt;
et() && (window.WaveformPlayer = T);
var se = T;

// packages/block-library/build-module/utils/waveform-utils.mjs
var DEFAULT_WAVEFORM_HEIGHT = 100;
var DEFAULT_SEEK_LABEL = "Seek";
function getComputedStyle2(element) {
  return element.ownerDocument.defaultView.getComputedStyle(element);
}
function getTopLevelGradientParts(gradientValue) {
  const match = gradientValue?.trim().match(/^[\w-]+-gradient\((.*)\)$/i);
  if (!match) {
    return [];
  }
  const parts = [];
  let depth = 0;
  let current = "";
  for (const character of match[1]) {
    if (character === "(") {
      ++depth;
    } else if (character === ")") {
      --depth;
    }
    if (character === "," && depth === 0) {
      parts.push(current.trim());
      current = "";
      continue;
    }
    current += character;
  }
  if (current.trim()) {
    parts.push(current.trim());
  }
  return parts;
}
function getLeadingColorFunction(value) {
  const match = value.match(/^([\w-]+)\(/);
  if (!match) {
    return;
  }
  const supportedFunctions = [
    "color",
    "color-mix",
    "hsl",
    "hsla",
    "hwb",
    "lab",
    "lch",
    "oklab",
    "oklch",
    "rgb",
    "rgba",
    "var"
  ];
  if (!supportedFunctions.includes(match[1].toLowerCase())) {
    return;
  }
  let depth = 0;
  let foundOpeningParenthesis = false;
  for (let index = 0; index < value.length; index++) {
    const character = value[index];
    if (character === "(") {
      foundOpeningParenthesis = true;
      ++depth;
    } else if (character === ")") {
      --depth;
    }
    if (foundOpeningParenthesis && depth === 0) {
      return value.slice(0, index + 1);
    }
  }
}
function getColorStopValue(gradientPart) {
  const colorFunction = getLeadingColorFunction(gradientPart);
  if (colorFunction) {
    return colorFunction;
  }
  const [possibleColor] = gradientPart.split(/\s+/);
  if (A(possibleColor).isValid()) {
    return possibleColor;
  }
}
function getWaveformGradientDirection(gradientValue) {
  const parts = getTopLevelGradientParts(gradientValue);
  const direction = parts[0];
  const angleMatch = direction?.match(/^(-?\d+(?:\.\d+)?)deg$/i);
  if (angleMatch) {
    const angle = (Number(angleMatch[1]) % 360 + 360) % 360;
    if (angle === 90 || angle === 270) {
      return "horizontal";
    }
    if (angle === 0 || angle === 180) {
      return "vertical";
    }
    return "diagonal";
  }
  if (!direction?.startsWith("to ")) {
    return void 0;
  }
  const sideOrCorner = direction.toLowerCase().replace(/^to\s+/, "");
  const hasHorizontalSide = sideOrCorner.includes("left") || sideOrCorner.includes("right");
  const hasVerticalSide = sideOrCorner.includes("top") || sideOrCorner.includes("bottom");
  if (hasHorizontalSide && hasVerticalSide) {
    return "diagonal";
  }
  if (hasHorizontalSide) {
    return "horizontal";
  }
  if (hasVerticalSide) {
    return "vertical";
  }
}
function resolveColorValue(element, colorValue) {
  if (!colorValue || A(colorValue).isValid()) {
    return colorValue;
  }
  const colorResolver = element.ownerDocument.createElement("span");
  colorResolver.style.color = colorValue;
  if (!colorResolver.style.color) {
    return colorValue;
  }
  element.appendChild(colorResolver);
  const resolvedColor = getComputedStyle2(colorResolver).color;
  colorResolver.remove();
  return resolvedColor && A(resolvedColor).isValid() ? resolvedColor : colorValue;
}
function getResolvedGradientStops(element, gradientValue) {
  const stops = getWaveformGradientStops(gradientValue)?.map((colorValue) => resolveColorValue(element, colorValue)).filter((colorValue) => A(colorValue).isValid());
  return stops?.length > 1 ? stops : void 0;
}
function applyAlpha(colorValue, alpha) {
  if (Array.isArray(colorValue)) {
    return colorValue.map(
      (color) => A(color).alpha(alpha).toRgbString()
    );
  }
  return A(colorValue).alpha(alpha).toRgbString();
}
function getRepresentativeColor(colorValue) {
  if (Array.isArray(colorValue)) {
    return colorValue[colorValue.length - 1];
  }
  return colorValue;
}
function getWaveformGradientStops(gradientValue) {
  const stops = getTopLevelGradientParts(gradientValue).map(getColorStopValue).filter(Boolean);
  return stops.length > 1 ? stops : void 0;
}
function serializeColorValue(colorValue) {
  return Array.isArray(colorValue) ? JSON.stringify(colorValue) : colorValue;
}
function getWaveformColors(element, waveformColorValue, textColorValue, waveformGradientValue) {
  const textColor = textColorValue || getComputedStyle2(element).color;
  const waveformGradientStops = getResolvedGradientStops(
    element,
    waveformGradientValue
  );
  const waveformBaseColor = waveformGradientStops || waveformColorValue || textColor;
  const waveformColor = applyAlpha(waveformBaseColor, 0.3);
  const progressColor = applyAlpha(waveformBaseColor, 0.6);
  const waveformGradient = waveformGradientStops ? getWaveformGradientDirection(waveformGradientValue) : void 0;
  return {
    textColor,
    waveformColor,
    progressColor,
    ...waveformGradient && { waveformGradient }
  };
}
function createWaveformContainer({
  url,
  title,
  artist,
  artwork,
  waveformColor,
  progressColor,
  waveformGradient,
  buttonColor,
  seekLabel,
  seekValueText,
  height = DEFAULT_WAVEFORM_HEIGHT,
  waveformStyle = "bars"
}) {
  const container = document.createElement("div");
  container.setAttribute("data-waveform-player", "");
  container.setAttribute("data-url", url);
  container.setAttribute("data-height", String(height));
  container.setAttribute("data-waveform-style", waveformStyle);
  container.setAttribute(
    "data-waveform-color",
    serializeColorValue(waveformColor)
  );
  container.setAttribute(
    "data-progress-color",
    serializeColorValue(progressColor)
  );
  if (waveformGradient) {
    container.setAttribute("data-waveform-gradient", waveformGradient);
  }
  container.setAttribute("data-button-color", buttonColor);
  container.setAttribute(
    "data-seek-label",
    getSeekControlLabel(seekLabel)
  );
  if (seekValueText) {
    container.setAttribute("data-seek-value-text", seekValueText);
  }
  container.setAttribute("data-text-color", buttonColor);
  container.setAttribute("data-text-secondary-color", buttonColor);
  if (title) {
    container.setAttribute("data-title", title);
  }
  if (artist) {
    container.setAttribute("data-artist", artist);
  }
  if (artwork) {
    container.setAttribute("data-artwork", artwork);
  }
  return container;
}
function applyWaveformPlayerStyles(container, {
  backgroundColor,
  backgroundGradient,
  textColor,
  playButtonColor,
  playButtonGradient
} = {}) {
  const waveformContainer = container.querySelector(".waveform-container");
  const playButton = container.querySelector(".waveform-btn");
  const playButtonBaseColor = getRepresentativeColor(
    getResolvedGradientStops(container, playButtonGradient) || playButtonColor
  );
  if (playButtonBaseColor) {
    container.style.setProperty(
      "--wfp-button-color",
      playButtonBaseColor
    );
  } else {
    container.style.removeProperty("--wfp-button-color");
  }
  if (textColor) {
    container.style.setProperty("--wfp-text-color", textColor);
    container.style.setProperty("--wfp-text-secondary-color", textColor);
  } else {
    container.style.removeProperty("--wfp-text-color");
    container.style.removeProperty("--wfp-text-secondary-color");
  }
  if (playButton) {
    if (playButtonGradient) {
      playButton.style.background = playButtonGradient;
    } else {
      playButton.style.removeProperty("background");
    }
  }
  if (waveformContainer) {
    if (backgroundGradient) {
      waveformContainer.style.background = backgroundGradient;
    } else if (backgroundColor) {
      waveformContainer.style.removeProperty("background");
      waveformContainer.style.backgroundColor = backgroundColor;
    } else {
      waveformContainer.style.removeProperty("background");
      waveformContainer.style.removeProperty("background-color");
    }
  }
}
function styleSvgIcons(container, buttonColor) {
  const isButtonDark = A(buttonColor).isDark();
  const iconColor = isButtonDark ? "#ffffff" : "#000000";
  const svgPaths = container.querySelectorAll("svg path");
  svgPaths.forEach((path) => {
    path.style.fill = iconColor;
  });
}
function setupPlayButtonAccessibility(container, { play: playLabel = "Play", pause: pauseLabel = "Pause" } = {}) {
  const playBtn = container.querySelector(".waveform-btn");
  if (!playBtn) {
    return;
  }
  playBtn.setAttribute("aria-label", playLabel);
  const onPlay = () => playBtn.setAttribute("aria-label", pauseLabel);
  const onPause = () => playBtn.setAttribute("aria-label", playLabel);
  container.addEventListener("waveformplayer:play", onPlay);
  container.addEventListener("waveformplayer:pause", onPause);
  container.addEventListener("waveformplayer:ended", onPause);
  return () => {
    container.removeEventListener("waveformplayer:play", onPlay);
    container.removeEventListener("waveformplayer:pause", onPause);
    container.removeEventListener("waveformplayer:ended", onPause);
  };
}
function getSeekControlLabel(label) {
  return label || DEFAULT_SEEK_LABEL;
}
function updateSeekControlLabel(instance, label) {
  const seekLabel = getSeekControlLabel(label);
  instance.options.seekLabel = seekLabel;
  instance.applySeekLabel?.(seekLabel);
  const seekControl = instance?.container?.querySelector(
    ".waveform-container"
  );
  if (seekControl) {
    seekControl.setAttribute("aria-label", seekLabel);
  }
}
function setupPlayButtonArtwork(container, artworkUrl) {
  if (!artworkUrl) {
    container.classList.remove("has-play-button-artwork");
    container.style.removeProperty("--wp--playlist--play-button-artwork");
    return;
  }
  container.classList.add("has-play-button-artwork");
  container.style.setProperty(
    "--wp--playlist--play-button-artwork",
    `url(${JSON.stringify(artworkUrl)})`
  );
}
function logPlayError(error) {
  if (error.name === "AbortError") {
    return;
  }
  console.error("Playlist play error:", error);
}
function initWaveformPlayer(element, {
  src,
  title,
  artist,
  image,
  imageAlt,
  waveformColor: waveformColorValue,
  waveformGradient: waveformGradientValue,
  textColor: textColorValue,
  backgroundColor,
  backgroundGradient,
  autoPlay,
  onEnded,
  labels,
  waveformStyle,
  showPlayButtonArtwork = false
}) {
  const playerArtwork = showPlayButtonArtwork ? void 0 : image;
  const { textColor, waveformColor, progressColor, waveformGradient } = getWaveformColors(
    element,
    waveformColorValue,
    textColorValue,
    waveformGradientValue
  );
  const waveformGradientStops = getResolvedGradientStops(
    element,
    waveformGradientValue
  );
  const waveformButtonColor = getRepresentativeColor(
    waveformGradientStops || waveformColorValue
  );
  const container = createWaveformContainer({
    url: src,
    title,
    artist,
    artwork: playerArtwork,
    waveformColor,
    progressColor,
    waveformGradient,
    buttonColor: textColor,
    seekLabel: title || labels?.seek,
    seekValueText: labels?.seekValueText,
    waveformStyle
  });
  element.appendChild(container);
  const instance = new se(container);
  if (instance.artworkEl) {
    instance.artworkEl.alt = imageAlt || "";
  }
  applyWaveformPlayerStyles(container, {
    backgroundColor,
    backgroundGradient,
    textColor,
    playButtonColor: showPlayButtonArtwork ? void 0 : waveformButtonColor,
    playButtonGradient: showPlayButtonArtwork ? void 0 : waveformGradientValue
  });
  let cleanupPlayButtonAccessibility;
  const handlers = {
    ready: () => {
      styleSvgIcons(container, waveformButtonColor || textColor);
      if (showPlayButtonArtwork) {
        setupPlayButtonArtwork(container, image);
      }
      cleanupPlayButtonAccessibility = setupPlayButtonAccessibility(
        container,
        labels
      );
      if (autoPlay) {
        instance.play()?.catch(logPlayError);
      }
    },
    ended: () => onEnded?.()
  };
  container.addEventListener("waveformplayer:ready", handlers.ready);
  container.addEventListener("waveformplayer:ended", handlers.ended);
  return {
    instance,
    container,
    destroy: () => {
      cleanupPlayButtonAccessibility?.();
      container.removeEventListener(
        "waveformplayer:ready",
        handlers.ready
      );
      container.removeEventListener(
        "waveformplayer:ended",
        handlers.ended
      );
      instance.destroy();
      container.remove();
    }
  };
}

// packages/block-library/build-module/playlist/view.mjs
var playerState = /* @__PURE__ */ new WeakMap();
var playlistPlayerState = /* @__PURE__ */ new Map();
var { state } = store(
  "core/playlist",
  {
    state: {
      playlists: {},
      get isCurrentTrack() {
        const { currentId, trackId } = getContext();
        return currentId === trackId;
      },
      get isCurrentTrackPlaying() {
        const { currentId, isPlaying, trackId } = getContext();
        return currentId === trackId && !!isPlaying;
      },
      get trackButtonActionLabel() {
        const { labelPauseTrack, labelSelectTrack } = getContext();
        return state.isCurrentTrackPlaying ? labelPauseTrack : labelSelectTrack;
      }
    },
    actions: {
      changeTrack() {
        const context = getContext();
        if (context.currentId === context.trackId) {
          const player = playlistPlayerState.get(
            context.playlistId
          )?.instance;
          if (player?.isPlaying) {
            context.isPlaying = false;
            player.pause();
          } else {
            player?.play()?.catch(logPlayError);
          }
          return;
        }
        context.isPlaying = false;
        context.currentId = context.trackId;
      }
    },
    callbacks: {
      initWaveformPlayer() {
        const context = getContext();
        const { ref } = getElement();
        if (!context.currentId || !ref) {
          return;
        }
        const track = state.playlists[context.playlistId]?.tracks[context.currentId];
        if (!track?.url) {
          return;
        }
        const existing = playerState.get(ref);
        if (existing?.url === track.url) {
          return;
        }
        const shouldAutoPlay = !!existing?.url;
        initPlayer(ref, track, shouldAutoPlay, context);
      }
    }
  },
  { lock: true }
);
function initPlayer(ref, track, shouldAutoPlay, context) {
  const existing = playerState.get(ref);
  const showPlayButtonArtwork = context.showPlayButtonArtwork === true;
  const playerArtwork = showPlayButtonArtwork ? "" : track.image;
  if (existing?.instance) {
    const shouldRecreatePlayer = !!existing.instance.artworkEl !== !!playerArtwork;
    if (shouldRecreatePlayer) {
      existing.destroy?.();
      playerState.delete(ref);
    } else {
      playlistPlayerState.set(context.playlistId, existing);
      existing.instance.loadTrack(track.url, track.title, track.artist, {
        artwork: playerArtwork,
        artworkAlt: playerArtwork ? track.imageAlt : ""
      }).then(() => {
        existing.url = track.url;
        if (existing.instance.artworkEl) {
          existing.instance.artworkEl.alt = track.imageAlt || "";
        }
        updateSeekControlLabel(
          existing.instance,
          track.title || ref.dataset.labelSeek
        );
        if (showPlayButtonArtwork) {
          setupPlayButtonArtwork(
            existing.container,
            track.image
          );
        }
        if (shouldAutoPlay) {
          existing.instance.play()?.catch(logPlayError);
        }
      }).catch(logPlayError);
      return;
    }
  }
  const labels = {
    play: ref.dataset.labelPlay,
    pause: ref.dataset.labelPause,
    seek: ref.dataset.labelSeek,
    seekValueText: ref.dataset.labelSeekValue
  };
  const player = initWaveformPlayer(ref, {
    src: track.url,
    title: track.title,
    artist: track.artist,
    image: track.image,
    imageAlt: track.imageAlt,
    waveformColor: ref.dataset.waveformPlayerColor,
    waveformGradient: ref.dataset.waveformPlayerGradient,
    backgroundColor: ref.dataset.waveformPlayerBackgroundColor,
    backgroundGradient: ref.dataset.waveformPlayerBackgroundGradient,
    autoPlay: shouldAutoPlay,
    labels,
    waveformStyle: context.waveformStyle,
    showPlayButtonArtwork,
    onEnded: () => {
      const currentIndex = context.tracks.findIndex(
        (trackId) => trackId === context.currentId
      );
      const nextTrack = context.tracks[currentIndex + 1];
      if (nextTrack) {
        context.currentId = nextTrack;
      }
    }
  });
  const setIsPlaying = (isPlaying) => {
    context.isPlaying = isPlaying;
  };
  const onPlay = () => setIsPlaying(true);
  const onPause = () => setIsPlaying(false);
  player.container.addEventListener("waveformplayer:play", onPlay);
  player.container.addEventListener("waveformplayer:pause", onPause);
  player.container.addEventListener("waveformplayer:ended", onPause);
  const destroy = () => {
    player.container.removeEventListener("waveformplayer:play", onPlay);
    player.container.removeEventListener("waveformplayer:pause", onPause);
    player.container.removeEventListener("waveformplayer:ended", onPause);
    player.destroy();
  };
  const nextState = {
    url: track.url,
    instance: player.instance,
    container: player.container,
    destroy
  };
  playerState.set(ref, nextState);
  playlistPlayerState.set(context.playlistId, nextState);
}
