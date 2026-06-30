// packages/block-library/build-module/playlist/view.mjs
import { store, getContext, getElement } from "@wordpress/interactivity";

// node_modules/colord/index.mjs
var r = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) };
var t = function(r2) {
  return "string" == typeof r2 ? r2.length > 0 : "number" == typeof r2;
};
var n = function(r2, t3, n2) {
  return void 0 === t3 && (t3 = 0), void 0 === n2 && (n2 = Math.pow(10, t3)), Math.round(n2 * r2) / n2 + 0;
};
var e = function(r2, t3, n2) {
  return void 0 === t3 && (t3 = 0), void 0 === n2 && (n2 = 1), r2 > n2 ? n2 : r2 > t3 ? r2 : t3;
};
var u = function(r2) {
  return (r2 = isFinite(r2) ? r2 % 360 : 0) > 0 ? r2 : r2 + 360;
};
var a = function(r2) {
  return { r: e(r2.r, 0, 255), g: e(r2.g, 0, 255), b: e(r2.b, 0, 255), a: e(r2.a) };
};
var o = function(r2) {
  return { r: n(r2.r), g: n(r2.g), b: n(r2.b), a: n(r2.a, 3) };
};
var i = /^#([0-9a-f]{3,8})$/i;
var s = function(r2) {
  var t3 = r2.toString(16);
  return t3.length < 2 ? "0" + t3 : t3;
};
var h = function(r2) {
  var t3 = r2.r, n2 = r2.g, e2 = r2.b, u2 = r2.a, a2 = Math.max(t3, n2, e2), o2 = a2 - Math.min(t3, n2, e2), i2 = o2 ? a2 === t3 ? (n2 - e2) / o2 : a2 === n2 ? 2 + (e2 - t3) / o2 : 4 + (t3 - n2) / o2 : 0;
  return { h: 60 * (i2 < 0 ? i2 + 6 : i2), s: a2 ? o2 / a2 * 100 : 0, v: a2 / 255 * 100, a: u2 };
};
var b = function(r2) {
  var t3 = r2.h, n2 = r2.s, e2 = r2.v, u2 = r2.a;
  t3 = t3 / 360 * 6, n2 /= 100, e2 /= 100;
  var a2 = Math.floor(t3), o2 = e2 * (1 - n2), i2 = e2 * (1 - (t3 - a2) * n2), s2 = e2 * (1 - (1 - t3 + a2) * n2), h2 = a2 % 6;
  return { r: 255 * [e2, i2, o2, o2, s2, e2][h2], g: 255 * [s2, e2, e2, i2, o2, o2][h2], b: 255 * [o2, o2, s2, e2, e2, i2][h2], a: u2 };
};
var g = function(r2) {
  return { h: u(r2.h), s: e(r2.s, 0, 100), l: e(r2.l, 0, 100), a: e(r2.a) };
};
var d = function(r2) {
  return { h: n(r2.h), s: n(r2.s), l: n(r2.l), a: n(r2.a, 3) };
};
var f = function(r2) {
  return b((n2 = (t3 = r2).s, { h: t3.h, s: (n2 *= ((e2 = t3.l) < 50 ? e2 : 100 - e2) / 100) > 0 ? 2 * n2 / (e2 + n2) * 100 : 0, v: e2 + n2, a: t3.a }));
  var t3, n2, e2;
};
var c = function(r2) {
  return { h: (t3 = h(r2)).h, s: (u2 = (200 - (n2 = t3.s)) * (e2 = t3.v) / 100) > 0 && u2 < 200 ? n2 * e2 / 100 / (u2 <= 100 ? u2 : 200 - u2) * 100 : 0, l: u2 / 2, a: t3.a };
  var t3, n2, e2, u2;
};
var l = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var p = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var v = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var m = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var y = { string: [[function(r2) {
  var t3 = i.exec(r2);
  return t3 ? (r2 = t3[1]).length <= 4 ? { r: parseInt(r2[0] + r2[0], 16), g: parseInt(r2[1] + r2[1], 16), b: parseInt(r2[2] + r2[2], 16), a: 4 === r2.length ? n(parseInt(r2[3] + r2[3], 16) / 255, 2) : 1 } : 6 === r2.length || 8 === r2.length ? { r: parseInt(r2.substr(0, 2), 16), g: parseInt(r2.substr(2, 2), 16), b: parseInt(r2.substr(4, 2), 16), a: 8 === r2.length ? n(parseInt(r2.substr(6, 2), 16) / 255, 2) : 1 } : null : null;
}, "hex"], [function(r2) {
  var t3 = v.exec(r2) || m.exec(r2);
  return t3 ? t3[2] !== t3[4] || t3[4] !== t3[6] ? null : a({ r: Number(t3[1]) / (t3[2] ? 100 / 255 : 1), g: Number(t3[3]) / (t3[4] ? 100 / 255 : 1), b: Number(t3[5]) / (t3[6] ? 100 / 255 : 1), a: void 0 === t3[7] ? 1 : Number(t3[7]) / (t3[8] ? 100 : 1) }) : null;
}, "rgb"], [function(t3) {
  var n2 = l.exec(t3) || p.exec(t3);
  if (!n2) return null;
  var e2, u2, a2 = g({ h: (e2 = n2[1], u2 = n2[2], void 0 === u2 && (u2 = "deg"), Number(e2) * (r[u2] || 1)), s: Number(n2[3]), l: Number(n2[4]), a: void 0 === n2[5] ? 1 : Number(n2[5]) / (n2[6] ? 100 : 1) });
  return f(a2);
}, "hsl"]], object: [[function(r2) {
  var n2 = r2.r, e2 = r2.g, u2 = r2.b, o2 = r2.a, i2 = void 0 === o2 ? 1 : o2;
  return t(n2) && t(e2) && t(u2) ? a({ r: Number(n2), g: Number(e2), b: Number(u2), a: Number(i2) }) : null;
}, "rgb"], [function(r2) {
  var n2 = r2.h, e2 = r2.s, u2 = r2.l, a2 = r2.a, o2 = void 0 === a2 ? 1 : a2;
  if (!t(n2) || !t(e2) || !t(u2)) return null;
  var i2 = g({ h: Number(n2), s: Number(e2), l: Number(u2), a: Number(o2) });
  return f(i2);
}, "hsl"], [function(r2) {
  var n2 = r2.h, a2 = r2.s, o2 = r2.v, i2 = r2.a, s2 = void 0 === i2 ? 1 : i2;
  if (!t(n2) || !t(a2) || !t(o2)) return null;
  var h2 = (function(r3) {
    return { h: u(r3.h), s: e(r3.s, 0, 100), v: e(r3.v, 0, 100), a: e(r3.a) };
  })({ h: Number(n2), s: Number(a2), v: Number(o2), a: Number(s2) });
  return b(h2);
}, "hsv"]] };
var N = function(r2, t3) {
  for (var n2 = 0; n2 < t3.length; n2++) {
    var e2 = t3[n2][0](r2);
    if (e2) return [e2, t3[n2][1]];
  }
  return [null, void 0];
};
var x = function(r2) {
  return "string" == typeof r2 ? N(r2.trim(), y.string) : "object" == typeof r2 && null !== r2 ? N(r2, y.object) : [null, void 0];
};
var M = function(r2, t3) {
  var n2 = c(r2);
  return { h: n2.h, s: e(n2.s + 100 * t3, 0, 100), l: n2.l, a: n2.a };
};
var H = function(r2) {
  return (299 * r2.r + 587 * r2.g + 114 * r2.b) / 1e3 / 255;
};
var $ = function(r2, t3) {
  var n2 = c(r2);
  return { h: n2.h, s: n2.s, l: e(n2.l + 100 * t3, 0, 100), a: n2.a };
};
var j = (function() {
  function r2(r3) {
    this.parsed = x(r3)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
  }
  return r2.prototype.isValid = function() {
    return null !== this.parsed;
  }, r2.prototype.brightness = function() {
    return n(H(this.rgba), 2);
  }, r2.prototype.isDark = function() {
    return H(this.rgba) < 0.5;
  }, r2.prototype.isLight = function() {
    return H(this.rgba) >= 0.5;
  }, r2.prototype.toHex = function() {
    return r3 = o(this.rgba), t3 = r3.r, e2 = r3.g, u2 = r3.b, i2 = (a2 = r3.a) < 1 ? s(n(255 * a2)) : "", "#" + s(t3) + s(e2) + s(u2) + i2;
    var r3, t3, e2, u2, a2, i2;
  }, r2.prototype.toRgb = function() {
    return o(this.rgba);
  }, r2.prototype.toRgbString = function() {
    return r3 = o(this.rgba), t3 = r3.r, n2 = r3.g, e2 = r3.b, (u2 = r3.a) < 1 ? "rgba(" + t3 + ", " + n2 + ", " + e2 + ", " + u2 + ")" : "rgb(" + t3 + ", " + n2 + ", " + e2 + ")";
    var r3, t3, n2, e2, u2;
  }, r2.prototype.toHsl = function() {
    return d(c(this.rgba));
  }, r2.prototype.toHslString = function() {
    return r3 = d(c(this.rgba)), t3 = r3.h, n2 = r3.s, e2 = r3.l, (u2 = r3.a) < 1 ? "hsla(" + t3 + ", " + n2 + "%, " + e2 + "%, " + u2 + ")" : "hsl(" + t3 + ", " + n2 + "%, " + e2 + "%)";
    var r3, t3, n2, e2, u2;
  }, r2.prototype.toHsv = function() {
    return r3 = h(this.rgba), { h: n(r3.h), s: n(r3.s), v: n(r3.v), a: n(r3.a, 3) };
    var r3;
  }, r2.prototype.invert = function() {
    return w({ r: 255 - (r3 = this.rgba).r, g: 255 - r3.g, b: 255 - r3.b, a: r3.a });
    var r3;
  }, r2.prototype.saturate = function(r3) {
    return void 0 === r3 && (r3 = 0.1), w(M(this.rgba, r3));
  }, r2.prototype.desaturate = function(r3) {
    return void 0 === r3 && (r3 = 0.1), w(M(this.rgba, -r3));
  }, r2.prototype.grayscale = function() {
    return w(M(this.rgba, -1));
  }, r2.prototype.lighten = function(r3) {
    return void 0 === r3 && (r3 = 0.1), w($(this.rgba, r3));
  }, r2.prototype.darken = function(r3) {
    return void 0 === r3 && (r3 = 0.1), w($(this.rgba, -r3));
  }, r2.prototype.rotate = function(r3) {
    return void 0 === r3 && (r3 = 15), this.hue(this.hue() + r3);
  }, r2.prototype.alpha = function(r3) {
    return "number" == typeof r3 ? w({ r: (t3 = this.rgba).r, g: t3.g, b: t3.b, a: r3 }) : n(this.rgba.a, 3);
    var t3;
  }, r2.prototype.hue = function(r3) {
    var t3 = c(this.rgba);
    return "number" == typeof r3 ? w({ h: r3, s: t3.s, l: t3.l, a: t3.a }) : n(t3.h);
  }, r2.prototype.isEqual = function(r3) {
    return this.toHex() === w(r3).toHex();
  }, r2;
})();
var w = function(r2) {
  return r2 instanceof j ? r2 : new j(r2);
};

// packages/block-library/node_modules/@arraypress/waveform-player/dist/waveform-player.esm.js
function L(t3) {
  let e2 = {};
  if (t3.dataset.url && (e2.url = t3.dataset.url), t3.dataset.height && (e2.height = parseInt(t3.dataset.height)), t3.dataset.samples && (e2.samples = parseInt(t3.dataset.samples)), t3.dataset.preload && (e2.preload = t3.dataset.preload), t3.dataset.waveformStyle && (e2.waveformStyle = t3.dataset.waveformStyle), t3.dataset.barWidth && (e2.barWidth = parseInt(t3.dataset.barWidth)), t3.dataset.barSpacing && (e2.barSpacing = parseInt(t3.dataset.barSpacing)), t3.dataset.buttonAlign && (e2.buttonAlign = t3.dataset.buttonAlign), t3.dataset.colorPreset && (e2.colorPreset = t3.dataset.colorPreset), t3.dataset.waveformColor && (e2.waveformColor = t3.dataset.waveformColor), t3.dataset.progressColor && (e2.progressColor = t3.dataset.progressColor), t3.dataset.buttonColor && (e2.buttonColor = t3.dataset.buttonColor), t3.dataset.buttonHoverColor && (e2.buttonHoverColor = t3.dataset.buttonHoverColor), t3.dataset.textColor && (e2.textColor = t3.dataset.textColor), t3.dataset.textSecondaryColor && (e2.textSecondaryColor = t3.dataset.textSecondaryColor), t3.dataset.backgroundColor && (e2.backgroundColor = t3.dataset.backgroundColor), t3.dataset.borderColor && (e2.borderColor = t3.dataset.borderColor), t3.dataset.color && (e2.waveformColor = t3.dataset.color), t3.dataset.theme && (e2.colorPreset = t3.dataset.theme), t3.dataset.autoplay && (e2.autoplay = t3.dataset.autoplay === "true"), t3.dataset.showTime && (e2.showTime = t3.dataset.showTime === "true"), t3.dataset.showHoverTime && (e2.showHoverTime = t3.dataset.showHoverTime === "true"), t3.dataset.showBpm && (e2.showBPM = t3.dataset.showBpm === "true"), t3.dataset.singlePlay && (e2.singlePlay = t3.dataset.singlePlay === "true"), t3.dataset.playOnSeek && (e2.playOnSeek = t3.dataset.playOnSeek === "true"), t3.dataset.title && (e2.title = t3.dataset.title), t3.dataset.subtitle && (e2.subtitle = t3.dataset.subtitle), t3.dataset.album && (e2.album = t3.dataset.album), t3.dataset.artwork && (e2.artwork = t3.dataset.artwork), t3.dataset.waveform && (e2.waveform = t3.dataset.waveform), t3.dataset.markers) try {
    e2.markers = JSON.parse(t3.dataset.markers);
  } catch (i2) {
    console.warn("Invalid markers JSON:", i2);
  }
  if (t3.dataset.playbackRate && (e2.playbackRate = parseFloat(t3.dataset.playbackRate)), t3.dataset.showPlaybackSpeed !== void 0 && (e2.showPlaybackSpeed = t3.dataset.showPlaybackSpeed === "true"), t3.dataset.playbackRates) try {
    e2.playbackRates = JSON.parse(t3.dataset.playbackRates);
  } catch (i2) {
    console.warn("Invalid playbackRates JSON:", i2);
  }
  return t3.dataset.enableMediaSession !== void 0 && (e2.enableMediaSession = t3.dataset.enableMediaSession === "true"), e2;
}
function P(t3) {
  if (!t3 || isNaN(t3)) return "0:00";
  let e2 = Math.floor(t3 / 60), i2 = Math.floor(t3 % 60);
  return `${e2}:${i2.toString().padStart(2, "0")}`;
}
function A(t3) {
  let e2 = t3 || Math.random().toString();
  return btoa(e2.substring(0, 10)).replace(/[^a-zA-Z0-9]/g, "");
}
function R(t3) {
  if (!t3) return "Audio";
  let e2 = t3.split("/");
  return e2[e2.length - 1].split(".")[0].replace(/[-_]/g, " ").replace(/\b\w/g, (s2) => s2.toUpperCase());
}
function C(...t3) {
  let e2 = {};
  for (let i2 of t3) for (let a2 in i2) i2[a2] !== null && i2[a2] !== void 0 && (e2[a2] = i2[a2]);
  return e2;
}
function B(t3, e2) {
  let i2;
  return function(...s2) {
    let n2 = () => {
      clearTimeout(i2), t3(...s2);
    };
    clearTimeout(i2), i2 = setTimeout(n2, e2);
  };
}
function S(t3, e2) {
  if (t3.length === e2) return t3;
  if (t3.length === 0 || e2 === 0) return [];
  let i2 = [];
  if (e2 > t3.length) {
    let a2 = (t3.length - 1) / (e2 - 1);
    for (let s2 = 0; s2 < e2; s2++) {
      let n2 = s2 * a2, r2 = Math.floor(n2), o2 = Math.ceil(n2), h2 = n2 - r2;
      if (o2 >= t3.length) i2.push(t3[t3.length - 1]);
      else if (r2 === o2) i2.push(t3[r2]);
      else {
        let l2 = t3[r2] * (1 - h2) + t3[o2] * h2;
        i2.push(l2);
      }
    }
  } else {
    let a2 = t3.length / e2;
    for (let s2 = 0; s2 < e2; s2++) {
      let n2 = Math.floor(s2 * a2), r2 = Math.floor((s2 + 1) * a2), o2 = 0, h2 = 0;
      for (let l2 = n2; l2 <= r2 && l2 < t3.length; l2++) t3[l2] > o2 && (o2 = t3[l2]), h2++;
      if (h2 === 0) {
        let l2 = Math.min(Math.round(s2 * a2), t3.length - 1);
        o2 = t3[l2];
      }
      i2.push(o2);
    }
  }
  return i2;
}
function x2(t3, e2, i2, a2, s2) {
  let n2 = window.devicePixelRatio || 1, r2 = s2.barWidth * n2, o2 = s2.barSpacing * n2, h2 = Math.floor(e2.width / (r2 + o2)), l2 = S(i2, h2), d2 = e2.height, p2 = a2 * e2.width;
  t3.clearRect(0, 0, e2.width, e2.height);
  for (let y2 = 0; y2 < l2.length; y2++) {
    let f2 = y2 * (r2 + o2);
    if (f2 + r2 > e2.width) break;
    let c2 = l2[y2] * d2 * 0.9, m2 = d2 - c2;
    t3.fillStyle = s2.color, t3.fillRect(f2, m2, r2, c2);
  }
  t3.save(), t3.beginPath(), t3.rect(0, 0, p2, d2), t3.clip();
  for (let y2 = 0; y2 < l2.length; y2++) {
    let f2 = y2 * (r2 + o2);
    if (f2 > p2) break;
    let c2 = l2[y2] * d2 * 0.9, m2 = d2 - c2;
    t3.fillStyle = s2.progressColor, t3.fillRect(f2, m2, r2, c2);
  }
  t3.restore();
}
function q(t3, e2, i2, a2, s2) {
  let n2 = window.devicePixelRatio || 1, r2 = s2.barWidth * n2, o2 = s2.barSpacing * n2, h2 = Math.floor(e2.width / (r2 + o2)), l2 = S(i2, h2), d2 = e2.height, p2 = d2 / 2, y2 = a2 * e2.width;
  t3.clearRect(0, 0, e2.width, e2.height);
  for (let f2 = 0; f2 < l2.length; f2++) {
    let c2 = f2 * (r2 + o2);
    if (c2 + r2 > e2.width) break;
    let m2 = l2[f2] * d2 * 0.45;
    t3.fillStyle = s2.color, t3.fillRect(c2, p2 - m2, r2, m2), t3.fillRect(c2, p2, r2, m2);
  }
  t3.save(), t3.beginPath(), t3.rect(0, 0, y2, d2), t3.clip();
  for (let f2 = 0; f2 < l2.length; f2++) {
    let c2 = f2 * (r2 + o2);
    if (c2 > y2) break;
    let m2 = l2[f2] * d2 * 0.45;
    t3.fillStyle = s2.progressColor, t3.fillRect(c2, p2 - m2, r2, m2), t3.fillRect(c2, p2, r2, m2);
  }
  t3.restore();
}
function $2(t3, e2, i2, a2, s2) {
  let n2 = e2.width, r2 = e2.height, o2 = r2 / 2, h2 = r2 * 0.35;
  t3.clearRect(0, 0, n2, r2);
  let l2 = (d2, p2, y2 = 1, f2 = false) => {
    f2 && (t3.shadowBlur = 12, t3.shadowColor = d2), t3.strokeStyle = d2, t3.lineWidth = p2, t3.lineCap = "round", t3.lineJoin = "round", t3.beginPath(), t3.moveTo(0, o2);
    let c2 = [], m2 = Math.floor(i2.length * y2);
    for (let u2 = 0; u2 < m2; u2++) {
      let v2 = u2 / (i2.length - 1) * n2, k = i2[u2], b2 = Math.sin(u2 * 0.1) * k, w2 = o2 + b2 * h2;
      c2.push({ x: v2, y: w2 });
    }
    for (let u2 = 0; u2 < c2.length - 1; u2++) {
      let v2 = c2[u2].x + (c2[u2 + 1].x - c2[u2].x) * 0.5, k = c2[u2].y, b2 = c2[u2 + 1].x - (c2[u2 + 1].x - c2[u2].x) * 0.5, w2 = c2[u2 + 1].y;
      t3.bezierCurveTo(v2, k, b2, w2, c2[u2 + 1].x, c2[u2 + 1].y);
    }
    t3.stroke(), f2 && (t3.shadowBlur = 0);
  };
  t3.strokeStyle = "rgba(255, 255, 255, 0.03)", t3.lineWidth = 0.5, t3.beginPath(), t3.moveTo(0, o2), t3.lineTo(n2, o2), t3.stroke();
  for (let d2 = 0; d2 <= 10; d2++) {
    let p2 = n2 / 10 * d2;
    t3.beginPath(), t3.moveTo(p2, 0), t3.lineTo(p2, r2), t3.stroke();
  }
  l2(s2.color, 2, 1, false), a2 > 0 && l2(s2.progressColor, 3, a2, true);
}
function U(t3, e2, i2, a2, s2) {
  let n2 = window.devicePixelRatio || 1, r2 = (s2.barWidth || 3) * n2, o2 = (s2.barSpacing || 1) * n2, h2 = Math.floor(e2.width / (r2 + o2)), l2 = S(i2, h2), d2 = e2.height, p2 = 4 * n2, y2 = 2 * n2, f2 = a2 * e2.width, c2 = d2 / 2;
  t3.clearRect(0, 0, e2.width, e2.height);
  for (let m2 = 0; m2 < l2.length; m2++) {
    let u2 = m2 * (r2 + o2);
    if (u2 + r2 > e2.width) break;
    let v2 = l2[m2] * d2 * 0.9, k = Math.floor(v2 / (p2 + y2));
    t3.fillStyle = u2 < f2 ? s2.progressColor : s2.color;
    for (let b2 = 0; b2 < k; b2++) {
      let w2 = b2 * (p2 + y2);
      t3.fillRect(u2, c2 - w2 - p2, r2, p2), b2 > 0 && t3.fillRect(u2, c2 + w2, r2, p2);
    }
  }
}
function F(t3, e2, i2, a2, s2) {
  let n2 = window.devicePixelRatio || 1, r2 = (s2.barWidth || 2) * n2, o2 = (s2.barSpacing || 3) * n2, h2 = Math.floor(e2.width / (r2 + o2)), l2 = S(i2, h2), d2 = e2.height, p2 = Math.max(1.5 * n2, r2 / 2), y2 = a2 * e2.width, f2 = d2 / 2;
  t3.clearRect(0, 0, e2.width, e2.height);
  for (let c2 = 0; c2 < l2.length; c2++) {
    let m2 = c2 * (r2 + o2) + r2 / 2;
    if (m2 > e2.width) break;
    let u2 = l2[c2] * d2 * 0.9;
    t3.fillStyle = m2 < y2 ? s2.progressColor : s2.color, t3.beginPath(), t3.arc(m2, f2 - u2 / 2, p2, 0, Math.PI * 2), t3.fill(), t3.beginPath(), t3.arc(m2, f2 + u2 / 2, p2, 0, Math.PI * 2), t3.fill();
  }
}
function N2(t3, e2, i2, a2, s2) {
  let n2 = e2.width, r2 = e2.height, o2 = r2 / 2, h2 = 4, l2 = h2 / 2;
  if (t3.clearRect(0, 0, n2, r2), t3.fillStyle = s2.color || "rgba(255, 255, 255, 0.2)", t3.beginPath(), t3.moveTo(l2, o2 - h2 / 2), t3.lineTo(n2 - l2, o2 - h2 / 2), t3.arc(n2 - l2, o2, h2 / 2, -Math.PI / 2, Math.PI / 2), t3.lineTo(l2, o2 + h2 / 2), t3.arc(l2, o2, h2 / 2, Math.PI / 2, -Math.PI / 2), t3.closePath(), t3.fill(), a2 > 0) {
    let d2 = Math.max(l2 * 2, a2 * n2);
    t3.shadowBlur = 8, t3.shadowColor = s2.progressColor, t3.fillStyle = s2.progressColor || "rgba(255, 255, 255, 0.9)", t3.beginPath(), t3.moveTo(l2, o2 - h2 / 2), t3.lineTo(d2 - l2, o2 - h2 / 2), t3.arc(d2 - l2, o2, h2 / 2, -Math.PI / 2, Math.PI / 2), t3.lineTo(l2, o2 + h2 / 2), t3.arc(l2, o2, h2 / 2, Math.PI / 2, -Math.PI / 2), t3.closePath(), t3.fill(), t3.shadowBlur = 0;
    let p2 = 8, y2 = d2;
    t3.shadowBlur = 4, t3.shadowColor = "rgba(0, 0, 0, 0.3)", t3.shadowOffsetY = 2, t3.fillStyle = "#ffffff", t3.beginPath(), t3.arc(y2, o2, p2, 0, Math.PI * 2), t3.fill(), t3.shadowBlur = 0, t3.shadowOffsetY = 0, t3.fillStyle = s2.progressColor || "rgba(255, 255, 255, 0.9)", t3.beginPath(), t3.arc(y2, o2, p2 * 0.4, 0, Math.PI * 2), t3.fill();
  }
}
var Y = { bars: x2, mirror: q, line: $2, blocks: U, dots: F, seekbar: N2 };
function W(t3, e2, i2, a2, s2) {
  (Y[s2.waveformStyle] || x2)(t3, e2, i2, a2, s2);
}
function I(t3) {
  try {
    let e2 = t3.getChannelData(0), i2 = t3.sampleRate, a2 = j2(e2, i2);
    if (a2.length < 2) return 120;
    let s2 = [];
    for (let h2 = 1; h2 < a2.length; h2++) s2.push((a2[h2] - a2[h2 - 1]) / i2);
    let n2 = {};
    s2.forEach((h2) => {
      let l2 = 60 / h2, d2 = Math.round(l2 / 3) * 3;
      d2 > 60 && d2 < 200 && (n2[d2] = (n2[d2] || 0) + 1);
    });
    let r2 = 0, o2 = 120;
    for (let [h2, l2] of Object.entries(n2)) l2 > r2 && (r2 = l2, o2 = parseInt(h2));
    return o2 < 70 && n2[o2 * 2] ? o2 *= 2 : o2 > 160 && n2[Math.round(o2 / 2)] && (o2 = Math.round(o2 / 2)), o2 - 1;
  } catch (e2) {
    return console.warn("BPM detection failed:", e2), null;
  }
}
function j2(t3, e2) {
  let s2 = [], n2 = 0;
  for (let r2 = 0; r2 < t3.length - 2048; r2 += 1024) {
    let o2 = 0;
    for (let d2 = r2; d2 < r2 + 2048; d2++) o2 += t3[d2] * t3[d2];
    o2 = o2 / 2048;
    let h2 = o2 - n2, l2 = n2 * 1.8 + 0.01;
    if (h2 > l2 && o2 > 0.01) {
      let d2 = s2[s2.length - 1] || 0, p2 = e2 * 0.15;
      r2 - d2 > p2 && s2.push(r2);
    }
    n2 = o2 * 0.8 + n2 * 0.2;
  }
  return s2;
}
function V(t3, e2 = 200) {
  let i2 = t3.length / e2, a2 = ~~(i2 / 10) || 1, s2 = t3.numberOfChannels, n2 = [];
  for (let o2 = 0; o2 < s2; o2++) {
    let h2 = t3.getChannelData(o2);
    for (let l2 = 0; l2 < e2; l2++) {
      let d2 = ~~(l2 * i2), p2 = ~~(d2 + i2), y2 = 0, f2 = 0;
      for (let m2 = d2; m2 < p2; m2 += a2) {
        let u2 = h2[m2];
        u2 > f2 && (f2 = u2), u2 < y2 && (y2 = u2);
      }
      let c2 = Math.max(Math.abs(f2), Math.abs(y2));
      (o2 === 0 || c2 > n2[l2]) && (n2[l2] = c2);
    }
  }
  let r2 = Math.max(...n2);
  return r2 > 0 ? n2.map((o2) => o2 / r2) : n2;
}
async function M2(t3, e2 = 200, i2 = false) {
  try {
    let a2 = new (window.AudioContext || window.webkitAudioContext)(), n2 = await (await fetch(t3)).arrayBuffer(), r2 = await a2.decodeAudioData(n2), o2 = V(r2, e2);
    o2 = J(o2);
    let h2 = null;
    return i2 && (h2 = await I(r2)), a2.close(), { peaks: o2, bpm: h2 };
  } catch (a2) {
    throw console.error("Failed to generate waveform:", a2), a2;
  }
}
function D(t3 = 200) {
  let e2 = [];
  for (let i2 = 0; i2 < t3; i2++) {
    let a2 = Math.random() * 0.5 + 0.3, s2 = Math.sin(i2 / t3 * Math.PI * 4) * 0.2;
    e2.push(Math.max(0.1, Math.min(1, a2 + s2)));
  }
  return e2;
}
function J(t3, e2 = 0.95) {
  let i2 = Math.max(...t3);
  if (i2 === 0 || i2 > e2) return t3;
  let a2 = e2 / i2;
  return t3.map((s2) => s2 * a2);
}
function G() {
  let t3 = document.documentElement, e2 = document.body;
  if (t3.classList.contains("dark") || t3.classList.contains("dark-mode") || t3.classList.contains("theme-dark") || t3.getAttribute("data-theme") === "dark" || t3.getAttribute("data-color-scheme") === "dark" || e2.classList.contains("dark") || e2.classList.contains("dark-mode") || e2.getAttribute("data-theme") === "dark") return "dark";
  if (t3.classList.contains("light") || t3.classList.contains("light-mode") || t3.classList.contains("theme-light") || t3.getAttribute("data-theme") === "light" || t3.getAttribute("data-color-scheme") === "light" || e2.classList.contains("light") || e2.classList.contains("light-mode") || e2.getAttribute("data-theme") === "light") return "light";
  try {
    let a2 = getComputedStyle(document.body).backgroundColor.match(/\d+/g);
    if (a2 && a2.length >= 3) {
      let [s2, n2, r2] = a2.map(Number), o2 = (s2 * 299 + n2 * 587 + r2 * 114) / 1e3;
      if (o2 > 128) return "light";
      if (o2 < 128) return "dark";
    }
  } catch {
  }
  if (window.matchMedia) {
    if (window.matchMedia("(prefers-color-scheme: dark)").matches) return "dark";
    if (window.matchMedia("(prefers-color-scheme: light)").matches) return "light";
  }
  return "dark";
}
var E = { dark: { waveformColor: "rgba(255, 255, 255, 0.3)", progressColor: "rgba(255, 255, 255, 0.9)", buttonColor: "rgba(255, 255, 255, 0.9)", buttonHoverColor: "rgba(255, 255, 255, 1)", textColor: "#ffffff", textSecondaryColor: "rgba(255, 255, 255, 0.6)", backgroundColor: "rgba(255, 255, 255, 0.03)", borderColor: "rgba(255, 255, 255, 0.1)" }, light: { waveformColor: "rgba(0, 0, 0, 0.2)", progressColor: "rgba(0, 0, 0, 0.8)", buttonColor: "rgba(0, 0, 0, 0.8)", buttonHoverColor: "rgba(0, 0, 0, 0.9)", textColor: "#333333", textSecondaryColor: "rgba(0, 0, 0, 0.6)", backgroundColor: "rgba(0, 0, 0, 0.02)", borderColor: "rgba(0, 0, 0, 0.1)" } };
function z(t3) {
  if (t3 && E[t3]) return E[t3];
  let e2 = G();
  return E[e2];
}
var O = { url: "", height: 60, samples: 200, preload: "metadata", playbackRate: 1, showPlaybackSpeed: false, playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2], buttonAlign: "auto", waveformStyle: "mirror", barWidth: 2, barSpacing: 0, colorPreset: null, waveformColor: null, progressColor: null, buttonColor: null, buttonHoverColor: null, textColor: null, textSecondaryColor: null, backgroundColor: null, borderColor: null, autoplay: false, showTime: true, showHoverTime: false, showBPM: false, singlePlay: true, playOnSeek: true, enableMediaSession: true, markers: [], showMarkers: true, title: null, subtitle: null, artwork: null, album: "", playIcon: '<svg viewBox="0 0 24 24" width="16" height="16"><path d="M8 5v14l11-7z"/></svg>', pauseIcon: '<svg viewBox="0 0 24 24" width="16" height="16"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>', onLoad: null, onPlay: null, onPause: null, onEnd: null, onError: null, onTimeUpdate: null };
var H2 = { bars: { barWidth: 3, barSpacing: 1 }, mirror: { barWidth: 2, barSpacing: 0 }, line: { barWidth: 2, barSpacing: 0 }, blocks: { barWidth: 4, barSpacing: 2 }, dots: { barWidth: 3, barSpacing: 3 }, seekbar: { barWidth: 1, barSpacing: 0 } };
var g2 = class t2 {
  static instances = /* @__PURE__ */ new Map();
  static currentlyPlaying = null;
  constructor(e2, i2 = {}) {
    if (this.container = typeof e2 == "string" ? document.querySelector(e2) : e2, !this.container) throw new Error("WaveformPlayer: Container element not found");
    let a2 = L(this.container);
    this.options = C(O, a2, i2);
    let s2 = z(this.options.colorPreset);
    for (let [r2, o2] of Object.entries(s2)) (this.options[r2] === null || this.options[r2] === void 0) && (this.options[r2] = o2);
    let n2 = H2[this.options.waveformStyle];
    n2 && (a2.barWidth === void 0 && i2.barWidth === void 0 && (this.options.barWidth = n2.barWidth), a2.barSpacing === void 0 && i2.barSpacing === void 0 && (this.options.barSpacing = n2.barSpacing)), this.audio = null, this.canvas = null, this.ctx = null, this.waveformData = [], this.progress = 0, this.isPlaying = false, this.isLoading = false, this.hasError = false, this.updateTimer = null, this.resizeObserver = null, this.id = this.container.id || A(this.options.url), t2.instances.set(this.id, this), this.init(), setTimeout(() => {
      this.container.dispatchEvent(new CustomEvent("waveformplayer:ready", { bubbles: true, detail: { player: this, url: this.options.url } }));
    }, 100);
  }
  init() {
    this.createDOM(), this.createAudio(), this.initPlaybackSpeed(), this.initKeyboardControls(), this.bindEvents(), this.setupResizeObserver(), requestAnimationFrame(() => {
      this.resizeCanvas(), this.options.url && this.load(this.options.url).then(() => {
        this.options.autoplay && this.play();
      }).catch((e2) => {
        console.error("Failed to load audio:", e2);
      });
    });
  }
  createDOM() {
    this.container.innerHTML = "", this.container.className = "waveform-player";
    let e2 = this.options.buttonAlign;
    e2 === "auto" && (this.options.waveformStyle === "bars" ? e2 = "bottom" : e2 = "center"), this.container.innerHTML = `
  <div class="waveform-player-inner">
    <div class="waveform-body">
      <div class="waveform-track waveform-align-${e2}">
        <button class="waveform-btn" aria-label="Play/Pause" style="
            border-color: ${this.options.buttonColor};
            color: ${this.options.buttonColor};
        ">
          <span class="waveform-icon-play">${this.options.playIcon}</span>
          <span class="waveform-icon-pause" style="display:none;">${this.options.pauseIcon}</span>
        </button>
        
        <div class="waveform-container">
          <canvas></canvas>
          <div class="waveform-markers"></div>
          <div class="waveform-loading" style="display:none;"></div>
          <div class="waveform-error" style="display:none;">
            <span class="waveform-error-text">Unable to load audio</span>
          </div>
        </div>
      </div>
      
      <div class="waveform-info">
        ${this.options.artwork ? `
          <img class="waveform-artwork" src="${this.options.artwork}" alt="Album artwork" style="
            width: 40px;
            height: 40px;
            border-radius: 4px;
            object-fit: cover;
            flex-shrink: 0;
          ">
        ` : ""}
        <div class="waveform-text">
          <span class="waveform-title" style="color: ${this.options.textColor};"></span>
          ${this.options.subtitle ? `<span class="waveform-subtitle" style="color: ${this.options.textSecondaryColor};">${this.options.subtitle}</span>` : ""}
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
          ${this.options.showBPM ? `
            <span class="waveform-bpm" style="color: ${this.options.textSecondaryColor}; display: none;">
              <span class="bpm-value">--</span> BPM
            </span>
          ` : ""}
          ${this.options.showPlaybackSpeed ? `
            <div class="waveform-speed">
              <button class="speed-btn" aria-label="Playback speed">
                <span class="speed-value">1x</span>
              </button>
              <div class="speed-menu" style="display: none;">
                ${this.options.playbackRates.map((i2) => `<button class="speed-option" data-rate="${i2}">${i2}x</button>`).join("")}
              </div>
            </div>
          ` : ""}
          ${this.options.showTime ? `
            <span class="waveform-time" style="color: ${this.options.textSecondaryColor};">
              <span class="time-current">0:00</span> / <span class="time-total">0:00</span>
            </span>
          ` : ""}
        </div>
      </div>
    </div>
  </div>
`, this.playBtn = this.container.querySelector(".waveform-btn"), this.canvas = this.container.querySelector("canvas"), this.ctx = this.canvas.getContext("2d"), this.titleEl = this.container.querySelector(".waveform-title"), this.subtitleEl = this.container.querySelector(".waveform-subtitle"), this.artworkEl = this.container.querySelector(".waveform-artwork"), this.currentTimeEl = this.container.querySelector(".time-current"), this.totalTimeEl = this.container.querySelector(".time-total"), this.bpmEl = this.container.querySelector(".waveform-bpm"), this.bpmValueEl = this.container.querySelector(".bpm-value"), this.loadingEl = this.container.querySelector(".waveform-loading"), this.errorEl = this.container.querySelector(".waveform-error"), this.markersContainer = this.container.querySelector(".waveform-markers"), this.speedBtn = this.container.querySelector(".speed-btn"), this.speedMenu = this.container.querySelector(".speed-menu"), this.resizeCanvas();
  }
  createAudio() {
    this.audio = new Audio(), this.audio.preload = this.options.preload || "metadata", this.audio.crossOrigin = "anonymous";
  }
  initPlaybackSpeed() {
    this.options.playbackRate && this.options.playbackRate !== 1 && (this.audio.playbackRate = this.options.playbackRate), this.options.showPlaybackSpeed && this.initSpeedControls();
  }
  initSpeedControls() {
    let e2 = this.container.querySelector(".speed-btn"), i2 = this.container.querySelector(".speed-menu");
    !e2 || !i2 || (e2.addEventListener("click", (a2) => {
      a2.stopPropagation(), i2.style.display = i2.style.display === "none" ? "block" : "none";
    }), document.addEventListener("click", () => {
      i2.style.display = "none";
    }), i2.addEventListener("click", (a2) => {
      if (a2.stopPropagation(), a2.target.classList.contains("speed-option")) {
        let s2 = parseFloat(a2.target.dataset.rate);
        this.setPlaybackRate(s2), i2.style.display = "none";
      }
    }), this.updateSpeedUI());
  }
  initKeyboardControls() {
    this.container.setAttribute("tabindex", "-1"), this.container.addEventListener("click", () => {
      t2.getAllInstances().forEach((e2) => {
        e2 !== this && e2.container.setAttribute("tabindex", "-1");
      }), this.container.setAttribute("tabindex", "0"), this.container.focus();
    }), this.container.addEventListener("keydown", (e2) => {
      if (document.activeElement !== this.container) return;
      let i2 = e2.key, a2 = this.audio.currentTime;
      if (i2 >= "0" && i2 <= "9") {
        e2.preventDefault(), this.seekToPercent(parseInt(i2) / 10);
        return;
      }
      let s2 = { " ": () => this.togglePlay(), ArrowLeft: () => this.seekTo(Math.max(0, a2 - 5)), ArrowRight: () => this.seekTo(Math.min(this.audio.duration, a2 + 5)), ArrowUp: () => this.setVolume(Math.min(1, this.audio.volume + 0.1)), ArrowDown: () => this.setVolume(Math.max(0, this.audio.volume - 0.1)), m: () => this.audio.muted = !this.audio.muted, M: () => this.audio.muted = !this.audio.muted };
      s2[i2] && (e2.preventDefault(), s2[i2]());
    });
  }
  initMediaSession() {
    !("mediaSession" in navigator) || !this.options.enableMediaSession || (navigator.mediaSession.metadata = new MediaMetadata({ title: this.options.title || "Unknown Track", artist: this.options.subtitle || "", album: this.options.album || "", artwork: this.options.artwork ? [{ src: this.options.artwork, sizes: "512x512", type: "image/jpeg" }] : [] }), navigator.mediaSession.setActionHandler("play", () => this.play()), navigator.mediaSession.setActionHandler("pause", () => this.pause()), navigator.mediaSession.setActionHandler("seekbackward", () => {
      this.seekTo(Math.max(0, this.audio.currentTime - 10));
    }), navigator.mediaSession.setActionHandler("seekforward", () => {
      this.seekTo(Math.min(this.audio.duration, this.audio.currentTime + 10));
    }), navigator.mediaSession.setActionHandler("seekto", (e2) => {
      e2.seekTime !== null && this.seekTo(e2.seekTime);
    }));
  }
  bindEvents() {
    this.playBtn.addEventListener("click", () => this.togglePlay()), this.audio.addEventListener("loadstart", () => this.setLoading(true)), this.audio.addEventListener("loadedmetadata", () => this.onMetadataLoaded()), this.audio.addEventListener("canplay", () => this.setLoading(false)), this.audio.addEventListener("play", () => this.onPlay()), this.audio.addEventListener("pause", () => this.onPause()), this.audio.addEventListener("ended", () => this.onEnded()), this.audio.addEventListener("error", (e2) => this.onError(e2)), this.canvas.addEventListener("click", (e2) => this.handleCanvasClick(e2)), this.resizeHandler = B(() => this.resizeCanvas(), 100), window.addEventListener("resize", this.resizeHandler);
  }
  setupResizeObserver() {
    "ResizeObserver" in window && (this.resizeObserver = new ResizeObserver(() => {
      this.resizeCanvas();
    }), this.canvas?.parentElement && this.resizeObserver.observe(this.canvas.parentElement));
  }
  async load(e2) {
    try {
      this.setLoading(true), this.progress = 0, this.hasError = false, this.audio.src = e2, await new Promise((a2, s2) => {
        let n2 = () => {
          this.audio.removeEventListener("loadedmetadata", n2), this.audio.removeEventListener("error", r2), a2();
        }, r2 = (o2) => {
          this.audio.removeEventListener("loadedmetadata", n2), this.audio.removeEventListener("error", r2), s2(o2);
        };
        this.audio.addEventListener("loadedmetadata", n2), this.audio.addEventListener("error", r2);
      });
      let i2 = this.options.title || R(e2);
      if (this.titleEl && (this.titleEl.textContent = i2), this.options.waveform) this.setWaveformData(this.options.waveform);
      else try {
        let a2 = await M2(e2, this.options.samples, this.options.showBPM);
        this.waveformData = a2.peaks, a2.bpm && (this.detectedBPM = a2.bpm, this.updateBPMDisplay());
      } catch (a2) {
        console.warn("Using placeholder waveform:", a2), this.waveformData = D(this.options.samples);
      }
      this.drawWaveform(), this.renderMarkers(), this.initMediaSession(), this.options.onLoad && this.options.onLoad(this);
    } catch (i2) {
      console.error("Failed to load audio:", i2), this.onError(i2);
    } finally {
      this.setLoading(false);
    }
  }
  async loadTrack(e2, i2 = null, a2 = null, s2 = {}) {
    this.isPlaying && this.pause(), this.audio.src = "", this.audio.load(), this.hasError = false, this.errorEl && (this.errorEl.style.display = "none"), this.canvas && (this.canvas.style.opacity = "1"), this.playBtn && (this.playBtn.disabled = false), this.progress = 0, this.waveformData = [], this.options = C(this.options, { url: e2, title: i2 || this.options.title, subtitle: a2 || this.options.subtitle, ...s2 }), s2.preload && (this.audio.preload = s2.preload), this.subtitleEl && (a2 ? (this.subtitleEl.textContent = a2, this.subtitleEl.style.display = "") : a2 === "" && (this.subtitleEl.style.display = "none")), s2.artwork && this.artworkEl && (this.artworkEl.src = s2.artwork), s2.markers && (this.options.markers = s2.markers), await this.load(e2), this.play();
  }
  setWaveformData(e2) {
    if (typeof e2 == "string") try {
      let i2 = JSON.parse(e2);
      this.waveformData = Array.isArray(i2) ? i2 : [];
    } catch {
      this.waveformData = e2.split(",").map(Number);
    }
    else this.waveformData = Array.isArray(e2) ? e2 : [];
    this.drawWaveform();
  }
  drawWaveform() {
    !this.ctx || this.waveformData.length === 0 || W(this.ctx, this.canvas, this.waveformData, this.progress, { ...this.options, waveformStyle: this.options.waveformStyle || "bars", color: this.options.waveformColor, progressColor: this.options.progressColor });
  }
  resizeCanvas() {
    if (!this.canvas || this.isDestroying) return;
    let e2 = window.devicePixelRatio || 1, i2 = this.canvas.getBoundingClientRect();
    this.canvas.width = i2.width * e2, this.canvas.height = this.options.height * e2, this.canvas.style.height = this.options.height + "px", this.canvas.parentElement.style.height = this.options.height + "px", this.drawWaveform();
  }
  renderMarkers() {
    !this.options.showMarkers || !this.options.markers?.length || !this.markersContainer || (this.markersContainer.innerHTML = "", !(!this.audio || !this.audio.duration || this.audio.duration === 0) && this.options.markers.forEach((e2, i2) => {
      if (e2.time > this.audio.duration) {
        console.warn(`Marker "${e2.label}" at ${e2.time}s exceeds audio duration of ${this.audio.duration}s`);
        return;
      }
      let a2 = e2.time / this.audio.duration * 100, s2 = document.createElement("button");
      s2.className = "waveform-marker", s2.style.left = `${a2}%`, s2.style.backgroundColor = e2.color || "rgba(255, 255, 255, 0.5)", s2.setAttribute("aria-label", e2.label), s2.setAttribute("data-time", e2.time);
      let n2 = document.createElement("span");
      n2.className = "waveform-marker-tooltip", n2.textContent = e2.label, s2.appendChild(n2), s2.addEventListener("click", (r2) => {
        r2.stopPropagation(), this.seekTo(e2.time), this.options.playOnSeek && !this.isPlaying && this.play();
      }), this.markersContainer.appendChild(s2);
    }));
  }
  handleCanvasClick(e2) {
    if (!this.audio.duration) return;
    let i2 = this.canvas.getBoundingClientRect(), a2 = e2.clientX - i2.left, s2 = Math.max(0, Math.min(1, a2 / i2.width));
    this.seekToPercent(s2);
  }
  setLoading(e2) {
    this.isLoading = e2, this.loadingEl && (this.loadingEl.style.display = e2 ? "block" : "none");
  }
  onMetadataLoaded() {
    this.isDestroying || (this.totalTimeEl && (this.totalTimeEl.textContent = P(this.audio.duration)), this.renderMarkers());
  }
  onPlay() {
    if (this.isDestroying) return;
    this.isPlaying = true, this.playBtn.classList.add("playing");
    let e2 = this.playBtn.querySelector(".waveform-icon-play"), i2 = this.playBtn.querySelector(".waveform-icon-pause");
    e2 && (e2.style.display = "none"), i2 && (i2.style.display = "flex"), this.startSmoothUpdate(), this.container.dispatchEvent(new CustomEvent("waveformplayer:play", { bubbles: true, detail: { player: this, url: this.options.url } })), this.options.onPlay && this.options.onPlay(this);
  }
  onPause() {
    if (this.isDestroying) return;
    this.isPlaying = false, this.playBtn.classList.remove("playing");
    let e2 = this.playBtn.querySelector(".waveform-icon-play"), i2 = this.playBtn.querySelector(".waveform-icon-pause");
    e2 && (e2.style.display = "flex"), i2 && (i2.style.display = "none"), this.stopSmoothUpdate(), this.container.dispatchEvent(new CustomEvent("waveformplayer:pause", { bubbles: true, detail: { player: this, url: this.options.url } })), this.options.onPause && this.options.onPause(this);
  }
  onEnded() {
    this.isDestroying || (this.progress = 0, this.audio.currentTime = 0, this.drawWaveform(), this.currentTimeEl && (this.currentTimeEl.textContent = "0:00"), this.container.dispatchEvent(new CustomEvent("waveformplayer:ended", { bubbles: true, detail: { player: this, url: this.options.url } })), this.onPause(), this.options.onEnd && this.options.onEnd(this));
  }
  onError(e2) {
    this.isDestroying || (console.error("Audio error:", e2), this.hasError = true, this.setLoading(false), this.errorEl && (this.errorEl.style.display = "flex"), this.canvas && (this.canvas.style.opacity = "0.2"), this.playBtn && (this.playBtn.disabled = true), this.options.onError && this.options.onError(e2, this));
  }
  startSmoothUpdate() {
    this.stopSmoothUpdate();
    let e2 = () => {
      this.isPlaying && this.audio.duration && (this.updateProgress(), this.updateTimer = requestAnimationFrame(e2));
    };
    this.updateTimer = requestAnimationFrame(e2);
  }
  stopSmoothUpdate() {
    this.updateTimer && (cancelAnimationFrame(this.updateTimer), this.updateTimer = null);
  }
  updateProgress() {
    if (!this.audio.duration) return;
    let e2 = this.audio.currentTime / this.audio.duration;
    Math.abs(e2 - this.progress) > 1e-3 && (this.progress = e2, this.drawWaveform()), this.currentTimeEl && (this.currentTimeEl.textContent = P(this.audio.currentTime)), this.container.dispatchEvent(new CustomEvent("waveformplayer:timeupdate", { bubbles: true, detail: { player: this, currentTime: this.audio.currentTime, duration: this.audio.duration, url: this.options.url } })), this.options.onTimeUpdate && this.options.onTimeUpdate(this.audio.currentTime, this.audio.duration, this);
  }
  updateBPMDisplay() {
    this.bpmEl && this.bpmValueEl && this.detectedBPM && (this.bpmValueEl.textContent = Math.round(this.detectedBPM), this.bpmEl.style.display = "inline-flex");
  }
  updateSpeedUI() {
    let e2 = this.container.querySelector(".speed-value");
    if (e2) {
      let i2 = this.audio.playbackRate;
      e2.textContent = i2 === 1 ? "1x" : `${i2}x`;
    }
    this.container.querySelectorAll(".speed-option").forEach((i2) => {
      i2.classList.toggle("active", parseFloat(i2.dataset.rate) === this.audio.playbackRate);
    });
  }
  play() {
    this.options.singlePlay && t2.currentlyPlaying && t2.currentlyPlaying !== this && t2.currentlyPlaying.pause(), t2.currentlyPlaying = this, this.audio.play();
  }
  pause() {
    t2.currentlyPlaying === this && (t2.currentlyPlaying = null), this.audio.pause();
  }
  togglePlay() {
    this.isPlaying ? this.pause() : this.play();
  }
  seekTo(e2) {
    this.audio && this.audio.duration && (this.audio.currentTime = Math.max(0, Math.min(e2, this.audio.duration)), this.updateProgress());
  }
  seekToPercent(e2) {
    this.audio && this.audio.duration && (this.audio.currentTime = this.audio.duration * Math.max(0, Math.min(1, e2)), this.updateProgress());
  }
  setVolume(e2) {
    this.audio && (this.audio.volume = Math.max(0, Math.min(1, e2)));
  }
  setPlaybackRate(e2) {
    if (!this.audio) return;
    let i2 = Math.max(0.5, Math.min(2, e2));
    this.audio.playbackRate = i2, this.options.playbackRate = i2, this.updateSpeedUI();
  }
  destroy() {
    this.isDestroying = true, this.pause(), this.stopSmoothUpdate(), this.resizeObserver && (this.resizeObserver.disconnect(), this.resizeObserver = null), this.resizeHandler && (window.removeEventListener("resize", this.resizeHandler), this.resizeHandler = null), t2.instances.delete(this.id), t2.currentlyPlaying === this && (t2.currentlyPlaying = null), this.audio && (this.audio.pause(), this.audio.src = "", this.audio.load(), this.audio = null), this.container.innerHTML = "", this.canvas = null, this.ctx = null, this.playBtn = null, this.waveformData = [];
  }
  static getInstance(e2) {
    if (typeof e2 == "string") {
      let i2 = this.instances.get(e2);
      if (i2) return i2;
      let a2 = document.getElementById(e2);
      if (a2) return Array.from(this.instances.values()).find((s2) => s2.container === a2);
    }
    if (e2 instanceof HTMLElement) return Array.from(this.instances.values()).find((i2) => i2.container === e2);
  }
  static getAllInstances() {
    return Array.from(this.instances.values());
  }
  static destroyAll() {
    this.instances.forEach((e2) => e2.destroy()), this.instances.clear();
  }
  static async generateWaveformData(e2, i2 = 200) {
    try {
      return (await M2(e2, i2)).peaks;
    } catch (a2) {
      throw console.error("Failed to generate waveform:", a2), a2;
    }
  }
};
function T() {
  if (typeof document > "u") return;
  document.querySelectorAll("[data-waveform-player]").forEach((e2) => {
    if (e2.dataset.waveformInitialized !== "true") try {
      new g2(e2), e2.dataset.waveformInitialized = "true";
    } catch (i2) {
      console.error("Failed to initialize WaveformPlayer:", i2, e2);
    }
  });
}
typeof document < "u" && (document.readyState === "loading" ? document.addEventListener("DOMContentLoaded", T) : T());
g2.init = T;
typeof window < "u" && (window.WaveformPlayer = g2);
var lt = g2;

// packages/block-library/build-module/utils/waveform-utils.mjs
var DEFAULT_WAVEFORM_HEIGHT = 100;
function getComputedStyle2(element) {
  return element.ownerDocument.defaultView.getComputedStyle(element);
}
function getWaveformColors(element) {
  const textColor = getComputedStyle2(element).color;
  const waveformColor = w(textColor).alpha(0.3).toRgbString();
  const progressColor = w(textColor).alpha(0.6).toRgbString();
  return { textColor, waveformColor, progressColor };
}
function createWaveformContainer({
  url,
  title,
  artist,
  artwork,
  waveformColor,
  progressColor,
  buttonColor,
  height = DEFAULT_WAVEFORM_HEIGHT,
  waveformStyle = "bars"
}) {
  const container = document.createElement("div");
  container.setAttribute("data-waveform-player", "");
  container.setAttribute("data-url", url);
  container.setAttribute("data-height", String(height));
  container.setAttribute("data-waveform-style", waveformStyle);
  container.setAttribute("data-waveform-color", waveformColor);
  container.setAttribute("data-progress-color", progressColor);
  container.setAttribute("data-button-color", buttonColor);
  container.setAttribute("data-text-color", buttonColor);
  container.setAttribute("data-text-secondary-color", buttonColor);
  if (title) {
    container.setAttribute("data-title", title);
  }
  if (artist) {
    container.setAttribute("data-subtitle", artist);
  }
  if (artwork) {
    container.setAttribute("data-artwork", artwork);
  }
  return container;
}
function styleSvgIcons(container, buttonColor) {
  const isButtonDark = w(buttonColor).isDark();
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
function logPlayError(error) {
  if (error.name === "AbortError") {
    return;
  }
  console.error("Playlist play error:", error);
}
function initWaveformPlayer(element, { src, title, artist, image, autoPlay, onEnded, labels, waveformStyle }) {
  const { textColor, waveformColor, progressColor } = getWaveformColors(element);
  const container = createWaveformContainer({
    url: src,
    title,
    artist,
    artwork: image,
    waveformColor,
    progressColor,
    buttonColor: textColor,
    waveformStyle
  });
  element.appendChild(container);
  const instance = new lt(container);
  let cleanupAccessibility;
  const handlers = {
    ready: () => {
      styleSvgIcons(container, textColor);
      cleanupAccessibility = setupPlayButtonAccessibility(
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
      cleanupAccessibility?.();
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
var { state } = store(
  "core/playlist",
  {
    state: {
      playlists: {},
      get isCurrentTrack() {
        const { currentId, uniqueId } = getContext();
        return currentId === uniqueId;
      }
    },
    actions: {
      changeTrack() {
        const context = getContext();
        context.currentId = context.uniqueId;
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
  if (existing?.instance) {
    existing.instance.loadTrack(track.url, track.title, track.artist, {
      artwork: track.image
    }).then(() => {
      existing.url = track.url;
      if (shouldAutoPlay) {
        existing.instance.play()?.catch(logPlayError);
      }
    }).catch(logPlayError);
    return;
  }
  const labels = {
    play: ref.dataset.labelPlay,
    pause: ref.dataset.labelPause
  };
  const player = initWaveformPlayer(ref, {
    src: track.url,
    title: track.title,
    artist: track.artist,
    image: track.image,
    autoPlay: shouldAutoPlay,
    labels,
    waveformStyle: context.waveformStyle,
    onEnded: () => {
      const currentIndex = context.tracks.findIndex(
        (uniqueId) => uniqueId === context.currentId
      );
      const nextTrack = context.tracks[currentIndex + 1];
      if (nextTrack) {
        context.currentId = nextTrack;
      }
    }
  });
  playerState.set(ref, {
    url: track.url,
    instance: player.instance,
    destroy: player.destroy
  });
}
