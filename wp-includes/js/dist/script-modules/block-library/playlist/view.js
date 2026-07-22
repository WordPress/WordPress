// packages/block-library/build-module/playlist/view.mjs
import { store, getContext, getElement } from "@wordpress/interactivity";

// node_modules/colord/index.mjs
var r = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) };
var t = function(r2) {
  return "string" == typeof r2 ? r2.length > 0 : "number" == typeof r2;
};
var n = function(r2, t2, n2) {
  return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = Math.pow(10, t2)), Math.round(n2 * r2) / n2 + 0;
};
var e = function(r2, t2, n2) {
  return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = 1), r2 > n2 ? n2 : r2 > t2 ? r2 : t2;
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
  var t2 = r2.toString(16);
  return t2.length < 2 ? "0" + t2 : t2;
};
var h = function(r2) {
  var t2 = r2.r, n2 = r2.g, e3 = r2.b, u2 = r2.a, a2 = Math.max(t2, n2, e3), o2 = a2 - Math.min(t2, n2, e3), i2 = o2 ? a2 === t2 ? (n2 - e3) / o2 : a2 === n2 ? 2 + (e3 - t2) / o2 : 4 + (t2 - n2) / o2 : 0;
  return { h: 60 * (i2 < 0 ? i2 + 6 : i2), s: a2 ? o2 / a2 * 100 : 0, v: a2 / 255 * 100, a: u2 };
};
var b = function(r2) {
  var t2 = r2.h, n2 = r2.s, e3 = r2.v, u2 = r2.a;
  t2 = t2 / 360 * 6, n2 /= 100, e3 /= 100;
  var a2 = Math.floor(t2), o2 = e3 * (1 - n2), i2 = e3 * (1 - (t2 - a2) * n2), s2 = e3 * (1 - (1 - t2 + a2) * n2), h2 = a2 % 6;
  return { r: 255 * [e3, i2, o2, o2, s2, e3][h2], g: 255 * [s2, e3, e3, i2, o2, o2][h2], b: 255 * [o2, o2, s2, e3, e3, i2][h2], a: u2 };
};
var g = function(r2) {
  return { h: u(r2.h), s: e(r2.s, 0, 100), l: e(r2.l, 0, 100), a: e(r2.a) };
};
var d = function(r2) {
  return { h: n(r2.h), s: n(r2.s), l: n(r2.l), a: n(r2.a, 3) };
};
var f = function(r2) {
  return b((n2 = (t2 = r2).s, { h: t2.h, s: (n2 *= ((e3 = t2.l) < 50 ? e3 : 100 - e3) / 100) > 0 ? 2 * n2 / (e3 + n2) * 100 : 0, v: e3 + n2, a: t2.a }));
  var t2, n2, e3;
};
var c = function(r2) {
  return { h: (t2 = h(r2)).h, s: (u2 = (200 - (n2 = t2.s)) * (e3 = t2.v) / 100) > 0 && u2 < 200 ? n2 * e3 / 100 / (u2 <= 100 ? u2 : 200 - u2) * 100 : 0, l: u2 / 2, a: t2.a };
  var t2, n2, e3, u2;
};
var l = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var p = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var v = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var m = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var y = { string: [[function(r2) {
  var t2 = i.exec(r2);
  return t2 ? (r2 = t2[1]).length <= 4 ? { r: parseInt(r2[0] + r2[0], 16), g: parseInt(r2[1] + r2[1], 16), b: parseInt(r2[2] + r2[2], 16), a: 4 === r2.length ? n(parseInt(r2[3] + r2[3], 16) / 255, 2) : 1 } : 6 === r2.length || 8 === r2.length ? { r: parseInt(r2.substr(0, 2), 16), g: parseInt(r2.substr(2, 2), 16), b: parseInt(r2.substr(4, 2), 16), a: 8 === r2.length ? n(parseInt(r2.substr(6, 2), 16) / 255, 2) : 1 } : null : null;
}, "hex"], [function(r2) {
  var t2 = v.exec(r2) || m.exec(r2);
  return t2 ? t2[2] !== t2[4] || t2[4] !== t2[6] ? null : a({ r: Number(t2[1]) / (t2[2] ? 100 / 255 : 1), g: Number(t2[3]) / (t2[4] ? 100 / 255 : 1), b: Number(t2[5]) / (t2[6] ? 100 / 255 : 1), a: void 0 === t2[7] ? 1 : Number(t2[7]) / (t2[8] ? 100 : 1) }) : null;
}, "rgb"], [function(t2) {
  var n2 = l.exec(t2) || p.exec(t2);
  if (!n2) return null;
  var e3, u2, a2 = g({ h: (e3 = n2[1], u2 = n2[2], void 0 === u2 && (u2 = "deg"), Number(e3) * (r[u2] || 1)), s: Number(n2[3]), l: Number(n2[4]), a: void 0 === n2[5] ? 1 : Number(n2[5]) / (n2[6] ? 100 : 1) });
  return f(a2);
}, "hsl"]], object: [[function(r2) {
  var n2 = r2.r, e3 = r2.g, u2 = r2.b, o2 = r2.a, i2 = void 0 === o2 ? 1 : o2;
  return t(n2) && t(e3) && t(u2) ? a({ r: Number(n2), g: Number(e3), b: Number(u2), a: Number(i2) }) : null;
}, "rgb"], [function(r2) {
  var n2 = r2.h, e3 = r2.s, u2 = r2.l, a2 = r2.a, o2 = void 0 === a2 ? 1 : a2;
  if (!t(n2) || !t(e3) || !t(u2)) return null;
  var i2 = g({ h: Number(n2), s: Number(e3), l: Number(u2), a: Number(o2) });
  return f(i2);
}, "hsl"], [function(r2) {
  var n2 = r2.h, a2 = r2.s, o2 = r2.v, i2 = r2.a, s2 = void 0 === i2 ? 1 : i2;
  if (!t(n2) || !t(a2) || !t(o2)) return null;
  var h2 = (function(r3) {
    return { h: u(r3.h), s: e(r3.s, 0, 100), v: e(r3.v, 0, 100), a: e(r3.a) };
  })({ h: Number(n2), s: Number(a2), v: Number(o2), a: Number(s2) });
  return b(h2);
}, "hsv"]] };
var N = function(r2, t2) {
  for (var n2 = 0; n2 < t2.length; n2++) {
    var e3 = t2[n2][0](r2);
    if (e3) return [e3, t2[n2][1]];
  }
  return [null, void 0];
};
var x = function(r2) {
  return "string" == typeof r2 ? N(r2.trim(), y.string) : "object" == typeof r2 && null !== r2 ? N(r2, y.object) : [null, void 0];
};
var M = function(r2, t2) {
  var n2 = c(r2);
  return { h: n2.h, s: e(n2.s + 100 * t2, 0, 100), l: n2.l, a: n2.a };
};
var H = function(r2) {
  return (299 * r2.r + 587 * r2.g + 114 * r2.b) / 1e3 / 255;
};
var $ = function(r2, t2) {
  var n2 = c(r2);
  return { h: n2.h, s: n2.s, l: e(n2.l + 100 * t2, 0, 100), a: n2.a };
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
    return r3 = o(this.rgba), t2 = r3.r, e3 = r3.g, u2 = r3.b, i2 = (a2 = r3.a) < 1 ? s(n(255 * a2)) : "", "#" + s(t2) + s(e3) + s(u2) + i2;
    var r3, t2, e3, u2, a2, i2;
  }, r2.prototype.toRgb = function() {
    return o(this.rgba);
  }, r2.prototype.toRgbString = function() {
    return r3 = o(this.rgba), t2 = r3.r, n2 = r3.g, e3 = r3.b, (u2 = r3.a) < 1 ? "rgba(" + t2 + ", " + n2 + ", " + e3 + ", " + u2 + ")" : "rgb(" + t2 + ", " + n2 + ", " + e3 + ")";
    var r3, t2, n2, e3, u2;
  }, r2.prototype.toHsl = function() {
    return d(c(this.rgba));
  }, r2.prototype.toHslString = function() {
    return r3 = d(c(this.rgba)), t2 = r3.h, n2 = r3.s, e3 = r3.l, (u2 = r3.a) < 1 ? "hsla(" + t2 + ", " + n2 + "%, " + e3 + "%, " + u2 + ")" : "hsl(" + t2 + ", " + n2 + "%, " + e3 + "%)";
    var r3, t2, n2, e3, u2;
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
    return "number" == typeof r3 ? w({ r: (t2 = this.rgba).r, g: t2.g, b: t2.b, a: r3 }) : n(this.rgba.a, 3);
    var t2;
  }, r2.prototype.hue = function(r3) {
    var t2 = c(this.rgba);
    return "number" == typeof r3 ? w({ h: r3, s: t2.s, l: t2.l, a: t2.a }) : n(t2.h);
  }, r2.prototype.isEqual = function(r3) {
    return this.toHex() === w(r3).toHex();
  }, r2;
})();
var w = function(r2) {
  return r2 instanceof j ? r2 : new j(r2);
};

// node_modules/@arraypress/waveform-player/dist/waveform-player.esm.js
function B(e3) {
  let t2 = -1 / 0;
  for (let i2 = 0; i2 < e3.length; i2++) e3[i2] > t2 && (t2 = e3[i2]);
  return t2;
}
function S(e3) {
  return String(e3 ?? "").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;");
}
function W(e3) {
  return S(typeof e3 == "number" ? `${e3}px` : e3);
}
function V(e3) {
  if (typeof e3 != "string" || e3 === "") return false;
  try {
    let t2 = new URL(e3, "http://localhost/");
    return t2.protocol === "http:" || t2.protocol === "https:";
  } catch {
    return false;
  }
}
function m2(e3, t2 = 0, i2 = 1) {
  return Math.max(t2, Math.min(e3, i2));
}
function nt(e3) {
  return e3 === void 0 ? void 0 : e3 === "true";
}
function N2(e3) {
  if (typeof e3 == "string" && e3.trim().startsWith("[")) try {
    return JSON.parse(e3);
  } catch {
  }
  return e3;
}
function D(e3) {
  let t2 = {}, i2 = (o2, l2 = o2) => {
    let n2 = nt(e3.dataset[l2]);
    n2 !== void 0 && (t2[o2] = n2);
  }, s2 = (o2, l2 = o2, n2 = false) => {
    let h2 = e3.dataset[l2];
    h2 && (t2[o2] = n2 ? parseFloat(h2) : parseInt(h2, 10));
  }, r2 = (o2, l2 = o2) => {
    let n2 = e3.dataset[l2];
    n2 && (t2[o2] = /^\d+(\.\d+)?$/.test(n2.trim()) ? parseFloat(n2) : n2);
  }, a2 = (o2, l2 = o2) => {
    let n2 = e3.dataset[l2];
    if (n2) try {
      t2[o2] = JSON.parse(n2);
    } catch (h2) {
      console.warn(`[WaveformPlayer] Invalid ${l2} JSON:`, h2);
    }
  };
  return e3.dataset.src && (t2.url = e3.dataset.src), e3.dataset.url && (t2.url = e3.dataset.url), s2("height"), s2("samples"), e3.dataset.preload && (t2.preload = e3.dataset.preload), e3.dataset.crossOrigin && (t2.crossOrigin = e3.dataset.crossOrigin), e3.dataset.audioMode && (t2.audioMode = e3.dataset.audioMode), e3.dataset.style && (t2.waveformStyle = e3.dataset.style), e3.dataset.waveformStyle && (t2.waveformStyle = e3.dataset.waveformStyle), e3.dataset.waveformGradient && (t2.waveformGradient = e3.dataset.waveformGradient), s2("barWidth"), s2("barSpacing"), s2("barRadius"), e3.dataset.buttonAlign && (t2.buttonAlign = e3.dataset.buttonAlign), e3.dataset.layout && (t2.layout = e3.dataset.layout), e3.dataset.buttonStyle && (t2.buttonStyle = e3.dataset.buttonStyle), r2("buttonSize"), r2("buttonRadius"), e3.dataset.colorPreset && (t2.colorPreset = e3.dataset.colorPreset), e3.dataset.waveformColor && (t2.waveformColor = N2(e3.dataset.waveformColor)), e3.dataset.progressColor && (t2.progressColor = N2(e3.dataset.progressColor)), e3.dataset.color && (t2.waveformColor = e3.dataset.color), e3.dataset.theme && (t2.colorPreset = e3.dataset.theme), i2("autoplay"), i2("showControls"), i2("showInfo"), i2("showTime"), i2("showHoverTime"), i2("seekHandle"), i2("showBPM", "showBpm"), s2("bpm"), i2("singlePlay"), i2("playOnSeek"), e3.dataset.title && (t2.title = e3.dataset.title), e3.dataset.artist && (t2.artist = e3.dataset.artist), e3.dataset.album && (t2.album = e3.dataset.album), e3.dataset.artwork && (t2.artwork = e3.dataset.artwork), e3.dataset.artworkPosition && (t2.artworkPosition = e3.dataset.artworkPosition), e3.dataset.waveform && (t2.waveform = e3.dataset.waveform), a2("markers"), s2("playbackRate", "playbackRate", true), i2("showPlaybackSpeed"), a2("playbackRates"), i2("enableMediaSession"), i2("showMarkers"), i2("accessibleSeek"), e3.dataset.seekLabel && (t2.seekLabel = e3.dataset.seekLabel), e3.dataset.seekValueText && (t2.seekValueText = e3.dataset.seekValueText), e3.dataset.errorText && (t2.errorText = e3.dataset.errorText), e3.dataset.playPauseLabel && (t2.playPauseLabel = e3.dataset.playPauseLabel), e3.dataset.speedLabel && (t2.speedLabel = e3.dataset.speedLabel), e3.dataset.artworkAlt && (t2.artworkAlt = e3.dataset.artworkAlt), e3.dataset.unknownTrackText && (t2.unknownTrackText = e3.dataset.unknownTrackText), e3.dataset.playIcon && (t2.playIcon = e3.dataset.playIcon), e3.dataset.pauseIcon && (t2.pauseIcon = e3.dataset.pauseIcon), t2;
}
function j2(e3, ...t2) {
  let i2 = 0;
  return e3.replace(/%(?:(\d+)\$)?s/g, (s2, r2) => {
    let a2 = r2 ? Number(r2) - 1 : i2++;
    return t2[a2] ?? s2;
  });
}
function E(e3) {
  if (!e3 || isNaN(e3) || e3 < 0) return "0:00";
  let t2 = Math.floor(e3 / 3600), i2 = Math.floor(e3 % 3600 / 60), s2 = Math.floor(e3 % 60);
  return t2 > 0 ? `${t2}:${i2.toString().padStart(2, "0")}:${s2.toString().padStart(2, "0")}` : `${i2}:${s2.toString().padStart(2, "0")}`;
}
var lt = 0;
function G(e3) {
  let t2 = e3 || "audio", i2 = 5381;
  for (let s2 = 0; s2 < t2.length; s2++) i2 = (i2 << 5) + i2 + t2.charCodeAt(s2) | 0;
  return `wp_${(i2 >>> 0).toString(36)}_${(lt++).toString(36)}`;
}
function H2(e3) {
  if (!e3) return "Audio";
  let t2 = e3.split("/");
  return t2[t2.length - 1].split(".")[0].replace(/[-_]/g, " ").replace(/\b\w/g, (r2) => r2.toUpperCase());
}
function J(e3) {
  let t2 = typeof e3 == "string" ? e3.match(/\d+/g) : null;
  if (!t2 || t2.length < 3) return null;
  let [i2, s2, r2] = t2.map(Number);
  return (i2 * 299 + s2 * 587 + r2 * 114) / 1e3;
}
function O(...e3) {
  let t2 = {};
  for (let i2 of e3) for (let s2 in i2) i2[s2] !== null && i2[s2] !== void 0 && (t2[s2] = i2[s2]);
  return t2;
}
function K(e3, t2) {
  let i2;
  return function(...r2) {
    let a2 = () => {
      clearTimeout(i2), e3(...r2);
    };
    clearTimeout(i2), i2 = setTimeout(a2, t2);
  };
}
function x2(e3, t2) {
  if (e3.length === t2) return e3;
  if (e3.length === 0 || t2 === 0) return [];
  let i2 = [];
  if (t2 > e3.length) {
    let s2 = (e3.length - 1) / (t2 - 1);
    for (let r2 = 0; r2 < t2; r2++) {
      let a2 = r2 * s2, o2 = Math.floor(a2), l2 = Math.ceil(a2), n2 = a2 - o2;
      if (l2 >= e3.length) i2.push(e3[e3.length - 1]);
      else if (o2 === l2) i2.push(e3[o2]);
      else {
        let h2 = e3[o2] * (1 - n2) + e3[l2] * n2;
        i2.push(h2);
      }
    }
  } else {
    let s2 = e3.length / t2;
    for (let r2 = 0; r2 < t2; r2++) {
      let a2 = Math.floor(r2 * s2), o2 = Math.floor((r2 + 1) * s2), l2 = 0, n2 = 0;
      for (let h2 = a2; h2 <= o2 && h2 < e3.length; h2++) e3[h2] > l2 && (l2 = e3[h2]), n2++;
      if (n2 === 0) {
        let h2 = Math.min(Math.round(r2 * s2), e3.length - 1);
        l2 = e3[h2];
      }
      i2.push(l2);
    }
  }
  return i2;
}
function P(e3, t2, i2, s2) {
  if (!Array.isArray(t2)) return t2;
  if (t2.length < 2) return t2[0];
  let r2 = i2.width, a2 = i2.height, o2 = s2 && s2.waveformGradient, [l2, n2, h2, c2] = o2 === "horizontal" ? [0, 0, r2, 0] : o2 === "diagonal" ? [0, 0, r2, a2] : [0, 0, 0, a2];
  try {
    let u2 = e3.createLinearGradient(l2, n2, h2, c2);
    return t2.forEach((w2, g2) => u2.addColorStop(g2 / (t2.length - 1), w2)), u2;
  } catch {
    return t2[0];
  }
}
function L(e3, t2, i2, s2, r2, a2) {
  if ((Array.isArray(a2) ? a2.some((l2) => l2 > 0) : a2 > 0) && typeof e3.roundRect == "function") {
    let l2 = Math.min(s2 / 2, Math.abs(r2) / 2), n2 = (h2) => m2(h2, 0, l2);
    e3.beginPath(), e3.roundRect(t2, i2, s2, r2, Array.isArray(a2) ? a2.map(n2) : n2(a2)), e3.fill();
  } else e3.fillRect(t2, i2, s2, r2);
}
function Z(e3, t2) {
  return (e3.barRadius || 0) * t2;
}
function ht(e3, t2) {
  let i2 = Z(e3, t2);
  return [i2, i2, 0, 0];
}
function Y(e3, t2, i2, s2, r2) {
  let a2 = r2 / 2;
  e3.beginPath(), e3.moveTo(t2, s2 - a2), e3.lineTo(i2 - a2, s2 - a2), e3.arc(i2 - a2, s2, a2, -Math.PI / 2, Math.PI / 2), e3.lineTo(t2, s2 + a2), e3.arc(t2, s2, a2, Math.PI / 2, -Math.PI / 2), e3.closePath();
}
function I(e3, t2, i2, s2, r2) {
  let a2 = window.devicePixelRatio || 1, o2 = r2.barWidth * a2, l2 = r2.barSpacing * a2, n2 = Math.floor(t2.width / (o2 + l2)), h2 = x2(i2, n2), c2 = t2.height, u2 = s2 * t2.width, w2 = ht(r2, a2), g2 = P(e3, r2.color, t2, r2), k = P(e3, r2.progressColor, t2, r2);
  e3.clearRect(0, 0, t2.width, t2.height), e3.fillStyle = g2;
  for (let f2 = 0; f2 < h2.length; f2++) {
    let p2 = f2 * (o2 + l2);
    if (p2 + o2 > t2.width) break;
    let y2 = h2[f2] * c2 * 0.9, d2 = c2 - y2;
    L(e3, p2, d2, o2, y2, w2);
  }
  e3.save(), e3.beginPath(), e3.rect(0, 0, u2, c2), e3.clip(), e3.fillStyle = k;
  for (let f2 = 0; f2 < h2.length; f2++) {
    let p2 = f2 * (o2 + l2);
    if (p2 > u2) break;
    let y2 = h2[f2] * c2 * 0.9, d2 = c2 - y2;
    L(e3, p2, d2, o2, y2, w2);
  }
  e3.restore();
}
function ct(e3, t2, i2, s2, r2) {
  let a2 = window.devicePixelRatio || 1, o2 = r2.barWidth * a2, l2 = r2.barSpacing * a2, n2 = Math.floor(t2.width / (o2 + l2)), h2 = x2(i2, n2), c2 = t2.height, u2 = c2 / 2, w2 = s2 * t2.width, g2 = Z(r2, a2), k = [g2, g2, 0, 0], f2 = [0, 0, g2, g2], p2 = P(e3, r2.color, t2, r2), y2 = P(e3, r2.progressColor, t2, r2);
  e3.clearRect(0, 0, t2.width, t2.height), e3.fillStyle = p2;
  for (let d2 = 0; d2 < h2.length; d2++) {
    let b2 = d2 * (o2 + l2);
    if (b2 + o2 > t2.width) break;
    let v2 = h2[d2] * c2 * 0.45;
    L(e3, b2, u2 - v2, o2, v2, k), L(e3, b2, u2, o2, v2, f2);
  }
  e3.save(), e3.beginPath(), e3.rect(0, 0, w2, c2), e3.clip(), e3.fillStyle = y2;
  for (let d2 = 0; d2 < h2.length; d2++) {
    let b2 = d2 * (o2 + l2);
    if (b2 > w2) break;
    let v2 = h2[d2] * c2 * 0.45;
    L(e3, b2, u2 - v2, o2, v2, k), L(e3, b2, u2, o2, v2, f2);
  }
  e3.restore();
}
function dt(e3, t2, i2, s2, r2) {
  let a2 = t2.width, o2 = t2.height, l2 = o2 / 2, n2 = o2 * 0.35;
  e3.clearRect(0, 0, a2, o2);
  let h2 = (c2, u2, w2 = 1, g2 = false) => {
    let k = P(e3, c2, t2, r2), f2 = Array.isArray(c2) ? c2[c2.length - 1] : c2;
    g2 && (e3.shadowBlur = 12, e3.shadowColor = f2), e3.strokeStyle = k, e3.lineWidth = u2, e3.lineCap = "round", e3.lineJoin = "round", e3.beginPath(), e3.moveTo(0, l2);
    let p2 = [], y2 = Math.floor(i2.length * w2);
    for (let d2 = 0; d2 < y2; d2++) {
      let b2 = d2 / (i2.length - 1) * a2, v2 = i2[d2], T = Math.sin(d2 * 0.1) * v2, C = l2 + T * n2;
      p2.push({ x: b2, y: C });
    }
    for (let d2 = 0; d2 < p2.length - 1; d2++) {
      let b2 = p2[d2].x + (p2[d2 + 1].x - p2[d2].x) * 0.5, v2 = p2[d2].y, T = p2[d2 + 1].x - (p2[d2 + 1].x - p2[d2].x) * 0.5, C = p2[d2 + 1].y;
      e3.bezierCurveTo(b2, v2, T, C, p2[d2 + 1].x, p2[d2 + 1].y);
    }
    e3.stroke(), g2 && (e3.shadowBlur = 0);
  };
  e3.strokeStyle = "rgba(255, 255, 255, 0.03)", e3.lineWidth = 0.5, e3.beginPath(), e3.moveTo(0, l2), e3.lineTo(a2, l2), e3.stroke();
  for (let c2 = 0; c2 <= 10; c2++) {
    let u2 = a2 / 10 * c2;
    e3.beginPath(), e3.moveTo(u2, 0), e3.lineTo(u2, o2), e3.stroke();
  }
  h2(r2.color, 2, 1, false), s2 > 0 && h2(r2.progressColor, 3, s2, true);
}
function X(e3, t2, i2, s2, r2) {
  let a2 = window.devicePixelRatio || 1, o2 = (r2.barWidth || 3) * a2, l2 = (r2.barSpacing || 1) * a2, n2 = Math.floor(t2.width / (o2 + l2)), h2 = x2(i2, n2), c2 = t2.height, u2 = 4 * a2, w2 = 2 * a2, g2 = s2 * t2.width, k = c2 / 2, f2 = P(e3, r2.color, t2, r2), p2 = P(e3, r2.progressColor, t2, r2);
  e3.clearRect(0, 0, t2.width, t2.height);
  for (let y2 = 0; y2 < h2.length; y2++) {
    let d2 = y2 * (o2 + l2);
    if (d2 + o2 > t2.width) break;
    let b2 = h2[y2] * c2 * 0.9, v2 = Math.floor(b2 / (u2 + w2));
    e3.fillStyle = d2 < g2 ? p2 : f2;
    for (let T = 0; T < v2; T++) {
      let C = T * (u2 + w2);
      e3.fillRect(d2, k - C - u2, o2, u2), T > 0 && e3.fillRect(d2, k + C, o2, u2);
    }
  }
}
function Q(e3, t2, i2, s2, r2) {
  let a2 = window.devicePixelRatio || 1, o2 = (r2.barWidth || 2) * a2, l2 = (r2.barSpacing || 3) * a2, n2 = Math.floor(t2.width / (o2 + l2)), h2 = x2(i2, n2), c2 = t2.height, u2 = Math.max(1.5 * a2, o2 / 2), w2 = s2 * t2.width, g2 = c2 / 2, k = P(e3, r2.color, t2, r2), f2 = P(e3, r2.progressColor, t2, r2);
  e3.clearRect(0, 0, t2.width, t2.height);
  for (let p2 = 0; p2 < h2.length; p2++) {
    let y2 = p2 * (o2 + l2) + o2 / 2;
    if (y2 > t2.width) break;
    let d2 = h2[p2] * c2 * 0.9;
    e3.fillStyle = y2 < w2 ? f2 : k, e3.beginPath(), e3.arc(y2, g2 - d2 / 2, u2, 0, Math.PI * 2), e3.fill(), e3.beginPath(), e3.arc(y2, g2 + d2 / 2, u2, 0, Math.PI * 2), e3.fill();
  }
}
function ut(e3, t2, i2, s2, r2) {
  let a2 = t2.width, o2 = t2.height, l2 = o2 / 2, n2 = 4, h2 = n2 / 2, c2 = !!r2.seekActive;
  if (e3.clearRect(0, 0, a2, o2), e3.fillStyle = P(e3, r2.color, t2, r2) || "rgba(255, 255, 255, 0.2)", Y(e3, h2, a2, l2, n2), e3.fill(), s2 > 0) {
    let u2 = Math.max(h2 * 2, s2 * a2);
    e3.save(), e3.globalAlpha = r2.seekHandle && !c2 ? 0.7 : 1, e3.fillStyle = P(e3, r2.progressColor, t2, r2) || "rgba(255, 255, 255, 0.9)", Y(e3, h2, u2, l2, n2), e3.fill(), e3.restore();
  }
}
var pt = { bars: I, bar: I, mirror: ct, line: dt, blocks: X, block: X, dots: Q, dot: Q, seekbar: ut };
function tt(e3, t2, i2, s2, r2) {
  (pt[r2.waveformStyle] || I)(e3, t2, i2, s2, r2);
}
function et(e3) {
  try {
    let t2 = e3.getChannelData(0), i2 = e3.sampleRate, s2 = ft(t2, i2);
    if (s2.length < 2) return 120;
    let r2 = [];
    for (let n2 = 1; n2 < s2.length; n2++) r2.push((s2[n2] - s2[n2 - 1]) / i2);
    let a2 = {};
    r2.forEach((n2) => {
      let h2 = 60 / n2, c2 = Math.round(h2 / 3) * 3;
      c2 > 60 && c2 < 200 && (a2[c2] = (a2[c2] || 0) + 1);
    });
    let o2 = 0, l2 = 120;
    for (let [n2, h2] of Object.entries(a2)) h2 > o2 && (o2 = h2, l2 = parseInt(n2));
    return l2 < 70 && a2[l2 * 2] ? l2 *= 2 : l2 > 160 && a2[Math.round(l2 / 2)] && (l2 = Math.round(l2 / 2)), l2 - 1;
  } catch (t2) {
    return console.warn("[WaveformPlayer] BPM detection failed:", t2), null;
  }
}
function ft(e3, t2) {
  let r2 = [], a2 = 0;
  for (let o2 = 0; o2 < e3.length - 2048; o2 += 1024) {
    let l2 = 0;
    for (let c2 = o2; c2 < o2 + 2048; c2++) l2 += e3[c2] * e3[c2];
    l2 = l2 / 2048;
    let n2 = l2 - a2, h2 = a2 * 1.8 + 0.01;
    if (n2 > h2 && l2 > 0.01) {
      let c2 = r2[r2.length - 1] || 0, u2 = t2 * 0.15;
      o2 - c2 > u2 && r2.push(o2);
    }
    a2 = l2 * 0.8 + a2 * 0.2;
  }
  return r2;
}
function mt(e3, t2 = 1800) {
  let i2 = e3.length / t2, s2 = e3.numberOfChannels, r2 = [];
  for (let o2 = 0; o2 < s2; o2++) {
    let l2 = e3.getChannelData(o2);
    for (let n2 = 0; n2 < t2; n2++) {
      let h2 = ~~(n2 * i2), c2 = ~~(h2 + i2), u2 = 0, w2 = 0;
      for (let k = h2; k < c2; k++) {
        let f2 = l2[k];
        f2 > w2 && (w2 = f2), f2 < u2 && (u2 = f2);
      }
      let g2 = Math.max(Math.abs(w2), Math.abs(u2));
      (o2 === 0 || g2 > r2[n2]) && (r2[n2] = g2);
    }
  }
  let a2 = B(r2);
  return a2 > 0 ? r2.map((o2) => o2 / a2) : r2;
}
async function F(e3, t2 = 1800, i2 = false) {
  let s2;
  try {
    let r2 = window.AudioContext || window.webkitAudioContext;
    s2 = new r2();
    let o2 = await (await fetch(e3)).arrayBuffer(), l2 = await s2.decodeAudioData(o2), n2 = mt(l2, t2);
    n2 = gt(n2);
    let h2 = null;
    return i2 && (h2 = et(l2)), { peaks: n2, bpm: h2 };
  } finally {
    s2 && s2.close();
  }
}
function it(e3 = 1800) {
  let t2 = [];
  for (let i2 = 0; i2 < e3; i2++) {
    let s2 = Math.random() * 0.5 + 0.3, r2 = Math.sin(i2 / e3 * Math.PI * 4) * 0.2;
    t2.push(m2(s2 + r2, 0.1, 1));
  }
  return t2;
}
function gt(e3, t2 = 0.95) {
  let i2 = B(e3);
  if (i2 === 0 || i2 > t2) return e3;
  let s2 = t2 / i2;
  return e3.map((r2) => r2 * s2);
}
function st(e3) {
  let t2 = document.documentElement, i2 = document.body;
  return t2.classList.contains(e3) || t2.classList.contains(`${e3}-mode`) || t2.classList.contains(`theme-${e3}`) || t2.getAttribute("data-theme") === e3 || t2.getAttribute("data-color-scheme") === e3 || i2.classList.contains(e3) || i2.classList.contains(`${e3}-mode`) || i2.getAttribute("data-theme") === e3;
}
function R() {
  if (st("dark")) return "dark";
  if (st("light")) return "light";
  try {
    let e3 = getComputedStyle(document.body).backgroundColor, t2 = J(e3);
    if (t2 !== null) {
      if (t2 > 128) return "light";
      if (t2 < 128) return "dark";
    }
  } catch {
  }
  if (window.matchMedia) {
    if (window.matchMedia("(prefers-color-scheme: dark)").matches) return "dark";
    if (window.matchMedia("(prefers-color-scheme: light)").matches) return "light";
  }
  return "dark";
}
var _ = { dark: { waveformColor: "rgba(255, 255, 255, 0.3)", progressColor: "rgba(255, 255, 255, 0.9)" }, light: { waveformColor: "rgba(0, 0, 0, 0.2)", progressColor: "rgba(0, 0, 0, 0.8)" } };
function $2(e3) {
  if (e3 && _[e3]) return _[e3];
  let t2 = R();
  return _[t2];
}
var z = { url: "", height: 64, samples: 1800, preload: "metadata", crossOrigin: null, audioMode: "self", playbackRate: 1, showPlaybackSpeed: false, playbackRates: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2], buttonAlign: "auto", layout: "default", buttonStyle: "circle", buttonSize: null, buttonRadius: null, waveformStyle: "mirror", barWidth: 2, barSpacing: 0, barRadius: 1, waveformGradient: "vertical", colorPreset: null, waveformColor: null, progressColor: null, autoplay: false, showControls: true, showInfo: true, showTime: true, showHoverTime: false, seekHandle: false, showBPM: false, bpm: null, singlePlay: true, playOnSeek: true, enableMediaSession: true, markers: [], showMarkers: true, accessibleSeek: true, seekLabel: null, seekValueText: null, title: null, artist: null, artwork: null, artworkPosition: "info", album: "", errorText: "Unable to load audio", playPauseLabel: "Play/Pause", speedLabel: "Playback speed", artworkAlt: "Album artwork", unknownTrackText: "Unknown Track", playIcon: '<svg viewBox="0 0 24 24" width="16" height="16"><path d="M8 5v14l11-7z"/></svg>', pauseIcon: '<svg viewBox="0 0 24 24" width="16" height="16"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>', onLoad: null, onPlay: null, onPause: null, onEnd: null, onError: null, onTimeUpdate: null, onNextTrack: null, onPreviousTrack: null };
var rt = { bars: { barWidth: 3, barSpacing: 1 }, mirror: { barWidth: 2, barSpacing: 2 }, line: { barWidth: 2, barSpacing: 0 }, blocks: { barWidth: 4, barSpacing: 2 }, dots: { barWidth: 3, barSpacing: 3 }, seekbar: { barWidth: 1, barSpacing: 0 } };
var yt = "data:image/svg+xml," + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect width="24" height="24" rx="4" fill="#71717a" fill-opacity="0.15"/><g fill="none" stroke="#a1a1aa" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="17" r="2.2"/><circle cx="17" cy="15" r="2.2"/><path d="M10.2 17V7l9-1.6v9"/></g></svg>');
var at = 5;
var ot = 10;
var wt = 'button, a[href], input, [role="slider"]';
var A = class e2 {
  static instances = /* @__PURE__ */ new Map();
  static currentlyPlaying = null;
  constructor(t2, i2 = {}) {
    if (this.container = typeof t2 == "string" ? document.querySelector(t2) : t2, !this.container) throw new Error("[WaveformPlayer] Container element not found");
    let s2 = D(this.container), r2 = { ...i2 };
    r2.style && !r2.waveformStyle && (r2.waveformStyle = r2.style), r2.src && !r2.url && (r2.url = r2.src), this.options = O(z, s2, r2);
    let a2 = $2(this.options.colorPreset);
    this._autoTheme = this.options.colorPreset == null || !_[this.options.colorPreset], this._presetKeys = [], this._scheme = this.options.colorPreset && _[this.options.colorPreset] ? this.options.colorPreset : R();
    for (let [l2, n2] of Object.entries(a2)) (this.options[l2] === null || this.options[l2] === void 0) && (this.options[l2] = n2, this._presetKeys.push(l2));
    let o2 = rt[this.options.waveformStyle];
    o2 && (s2.barWidth === void 0 && i2.barWidth === void 0 && (this.options.barWidth = o2.barWidth), s2.barSpacing === void 0 && i2.barSpacing === void 0 && (this.options.barSpacing = o2.barSpacing)), this.audio = null, this.canvas = null, this.ctx = null, this.waveformData = [], this.progress = 0, this._activeMarkerIndex = -1, this._markerLabelTimer = null, this.isPlaying = false, this.isLoading = false, this.hasError = false, this.updateTimer = null, this.resizeObserver = null, this._ac = new AbortController(), this.id = this.container.id || G(this.options.url), e2.instances.set(this.id, this), e2._watchTheme(), this.init(), setTimeout(() => {
      this._emit("waveformplayer:ready", { player: this, url: this.options.url });
    }, 100);
  }
  _emit(t2, i2, s2 = false) {
    let r2 = new CustomEvent(t2, { bubbles: true, cancelable: s2, detail: i2 });
    return this.container.dispatchEvent(r2), r2;
  }
  _requestSeek(t2) {
    this._emit("waveformplayer:request-seek", { ...this._buildTrackDetail(), percent: t2 }, true).defaultPrevented || (this.progress = t2, this.drawWaveform?.());
  }
  init() {
    this.createDOM(), this.createAudio(), this.initPlaybackSpeed(), this.initKeyboardControls(), this.initSeekControl(), this.bindEvents(), this.setupResizeObserver(), requestAnimationFrame(() => {
      this.resizeCanvas(), this.options.url && this.load(this.options.url).then(() => {
        this.options.autoplay && this.play()?.catch(() => {
        });
      }).catch((t2) => {
        console.error("[WaveformPlayer] Failed to load audio:", t2);
      });
    });
  }
  createDOM() {
    this.container.innerHTML = "", this.container.className = "waveform-player";
    let t2 = this.options.buttonAlign;
    t2 === "auto" && (this.options.waveformStyle === "bars" ? t2 = "bottom" : t2 = "center"), this.options.layout === "preview" && this.container.classList.add("waveform-layout-preview"), this.container.classList.toggle("waveform-theme-light", this._scheme === "light");
    let s2 = [];
    this.options.buttonSize != null && s2.push(`--wfp-btn-size: ${W(this.options.buttonSize)}`), this.options.buttonRadius != null && s2.push(`--wfp-btn-radius: ${W(this.options.buttonRadius)}`);
    let r2 = s2.length ? ` style="${s2.join("; ")};"` : "", a2 = this.options.artworkPosition === "button" && this.options.artwork, o2 = a2 ? `<img class="waveform-btn-artwork" src="${S(this.options.artwork)}" alt="" aria-hidden="true">` : "", l2 = this.options.showControls ? `
        <button class="waveform-btn${this.options.buttonStyle === "minimal" ? " waveform-btn-minimal" : ""}${a2 ? " waveform-btn-has-artwork" : ""}" aria-label="${S(this.options.playPauseLabel)}"${r2}>
          ${o2}
          <span class="waveform-icon-play">${this.options.playIcon}</span>
          <span class="waveform-icon-pause" style="display:none;">${this.options.pauseIcon}</span>
        </button>
        ` : "", n2 = this.options.showInfo ? `
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
                ${this.options.playbackRates.map((h2) => `<button class="speed-option" role="menuitemradio" tabindex="-1" aria-checked="false" data-rate="${h2}">${h2}x</button>`).join("")}
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
      <div class="waveform-track waveform-align-${t2}">
        ${l2}
        
        <div class="waveform-container">
          <canvas></canvas>
          <div class="waveform-markers"></div>
          <div class="waveform-loading" style="display:none;"></div>
          <div class="waveform-error" style="display:none;" role="alert">
            <span class="waveform-error-text">${S(this.options.errorText)}</span>
          </div>
        </div>
      </div>
      
      ${n2}
    </div>
  </div>
`, this.playBtn = this.container.querySelector(".waveform-btn"), this.canvas = this.container.querySelector("canvas"), this.ctx = this.canvas.getContext("2d"), this.titleEl = this.container.querySelector(".waveform-title"), this.artistEl = this.container.querySelector(".waveform-artist"), this.artworkEl = this.container.querySelector(".waveform-artwork, .waveform-btn-artwork"), this.bindArtworkFallback(this.artworkEl), this.currentTimeEl = this.container.querySelector(".time-current"), this.totalTimeEl = this.container.querySelector(".time-total"), this.bpmEl = this.container.querySelector(".waveform-bpm"), this.bpmValueEl = this.container.querySelector(".bpm-value"), this.loadingEl = this.container.querySelector(".waveform-loading"), this.errorEl = this.container.querySelector(".waveform-error"), this.markersContainer = this.container.querySelector(".waveform-markers"), this.speedBtn = this.container.querySelector(".speed-btn"), this.speedMenu = this.container.querySelector(".speed-menu"), this.resizeCanvas(), this.updateBPMDisplay();
  }
  bindArtworkFallback(t2) {
    t2 && t2.addEventListener("error", () => {
      t2.src.startsWith("data:") || (t2.src = yt);
    }, { signal: this._ac.signal });
  }
  createArtworkElement() {
    let t2 = document.createElement("img");
    return t2.className = "waveform-artwork", t2.style.width = "40px", t2.style.height = "40px", t2.style.borderRadius = "4px", t2.style.objectFit = "cover", t2.style.flexShrink = "0", this.bindArtworkFallback(t2), t2;
  }
  createButtonArtworkElement() {
    let t2 = document.createElement("img");
    return t2.className = "waveform-btn-artwork", t2.alt = "", t2.setAttribute("aria-hidden", "true"), this.bindArtworkFallback(t2), t2;
  }
  createArtistElement() {
    let t2 = document.createElement("span");
    return t2.className = "waveform-artist", t2;
  }
  syncArtist(t2) {
    if (this.options.artist = t2 || null, !!this.options.showInfo) {
      if (!t2) {
        this.artistEl?.remove(), this.artistEl = null;
        return;
      }
      if (!this.artistEl) {
        let i2 = this.container.querySelector(".waveform-title");
        if (!i2) return;
        this.artistEl = this.createArtistElement(), i2.after(this.artistEl);
      }
      this.artistEl.textContent = t2, this.artistEl.style.display = "";
    }
  }
  syncButtonArtwork(t2) {
    if (this.playBtn) {
      if (!t2) {
        this.artworkEl?.remove(), this.artworkEl = null, this.playBtn.classList.remove("waveform-btn-has-artwork");
        return;
      }
      this.artworkEl || (this.artworkEl = this.createButtonArtworkElement(), this.playBtn.prepend(this.artworkEl)), this.artworkEl.src = t2, this.playBtn.classList.add("waveform-btn-has-artwork");
    }
  }
  syncArtwork(t2, i2 = "") {
    if (this.options.artwork = t2 || null, this.options.artworkAlt = i2 || "", this.options.artworkPosition === "button") {
      this.syncButtonArtwork(this.options.artwork);
      return;
    }
    if (this.options.showInfo) {
      if (!t2) {
        this.artworkEl?.remove(), this.artworkEl = null;
        return;
      }
      if (!this.artworkEl) {
        let s2 = this.container.querySelector(".waveform-text");
        if (!s2) return;
        this.artworkEl = this.createArtworkElement(), s2.before(this.artworkEl);
      }
      this.artworkEl.src = t2, this.artworkEl.alt = i2 || "";
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
    let t2 = this.container.querySelector(".speed-btn"), i2 = this.container.querySelector(".speed-menu");
    if (!t2 || !i2) return;
    let s2 = () => Array.from(i2.querySelectorAll(".speed-option")), r2 = () => i2.style.display !== "none", a2 = (n2) => {
      if (i2.style.display = n2 ? "block" : "none", t2.setAttribute("aria-expanded", n2 ? "true" : "false"), n2) {
        let h2 = s2();
        (h2.find((c2) => c2.getAttribute("aria-checked") === "true") || h2[0])?.focus();
      }
    }, o2 = (n2) => {
      let h2 = s2();
      h2.length && h2[(n2 + h2.length) % h2.length].focus();
    }, l2 = (n2) => {
      this.setPlaybackRate(parseFloat(n2.dataset.rate)), a2(false), t2.focus();
    };
    t2.addEventListener("click", (n2) => {
      n2.stopPropagation(), a2(!r2());
    }, { signal: this._ac.signal }), document.addEventListener("click", () => a2(false), { signal: this._ac.signal }), i2.addEventListener("click", (n2) => {
      n2.stopPropagation();
      let h2 = n2.target.closest(".speed-option");
      h2 && l2(h2);
    }, { signal: this._ac.signal }), t2.closest(".waveform-speed")?.addEventListener("keydown", (n2) => {
      let h2 = s2(), c2 = h2.indexOf(document.activeElement);
      if (!r2()) {
        (n2.key === "ArrowDown" || n2.key === "ArrowUp") && document.activeElement === t2 && (n2.preventDefault(), a2(true));
        return;
      }
      switch (n2.key) {
        case "ArrowDown":
          n2.preventDefault(), o2(c2 < 0 ? 0 : c2 + 1);
          break;
        case "ArrowUp":
          n2.preventDefault(), o2(c2 < 0 ? h2.length - 1 : c2 - 1);
          break;
        case "Home":
          n2.preventDefault(), o2(0);
          break;
        case "End":
          n2.preventDefault(), o2(h2.length - 1);
          break;
        case "Escape":
          n2.preventDefault(), a2(false), t2.focus();
          break;
        case "Tab":
          a2(false);
          break;
      }
    }, { signal: this._ac.signal }), this.updateSpeedUI();
  }
  initKeyboardControls() {
    this.container.setAttribute("tabindex", "-1"), this.container.addEventListener("click", (t2) => {
      t2.target.closest(wt) || (e2.getAllInstances().forEach((i2) => {
        i2 !== this && i2.container.setAttribute("tabindex", "-1");
      }), this.container.setAttribute("tabindex", "0"), this.container.focus());
    }, { signal: this._ac.signal }), this.container.addEventListener("keydown", (t2) => {
      if (document.activeElement !== this.container) return;
      let i2 = t2.key, s2 = !!this.audio, r2 = s2 ? this.audio.currentTime : 0;
      if (s2 && i2 >= "0" && i2 <= "9") {
        t2.preventDefault(), this.seekToPercent(parseInt(i2) / 10);
        return;
      }
      let a2 = { " ": () => this.togglePlay() };
      s2 && (a2.ArrowLeft = () => this.seekTo(m2(r2 - 5, 0, this.audio.duration)), a2.ArrowRight = () => this.seekTo(m2(r2 + 5, 0, this.audio.duration)), a2.ArrowUp = () => this.setVolume(m2(this.audio.volume + 0.1)), a2.ArrowDown = () => this.setVolume(m2(this.audio.volume - 0.1)), a2.m = a2.M = () => this.audio.muted = !this.audio.muted), a2[i2] && (t2.preventDefault(), a2[i2]());
    }, { signal: this._ac.signal });
  }
  initSeekControl() {
    this.options.accessibleSeek && (this.seekEl = this.container.querySelector(".waveform-container"), this.seekEl && (this.seekEl.setAttribute("role", "slider"), this.seekEl.setAttribute("tabindex", "0"), this.seekEl.setAttribute("aria-valuemin", "0"), this.applySeekLabel(), this.updateSeekAccessibility(), this.seekEl.addEventListener("keydown", (t2) => {
      if (t2.key === " " || t2.key === "Spacebar") {
        t2.preventDefault(), t2.stopPropagation(), this.togglePlay();
        return;
      }
      let i2 = this.getSeekDuration();
      if (!i2) return;
      let s2 = this.getSeekCurrentTime(), r2;
      switch (t2.key) {
        case "ArrowLeft":
        case "ArrowDown":
          r2 = s2 - at;
          break;
        case "ArrowRight":
        case "ArrowUp":
          r2 = s2 + at;
          break;
        case "PageDown":
          r2 = s2 - ot;
          break;
        case "PageUp":
          r2 = s2 + ot;
          break;
        case "Home":
          r2 = 0;
          break;
        case "End":
          r2 = i2;
          break;
        default:
          return;
      }
      t2.preventDefault(), t2.stopPropagation(), this.seekToSeconds(r2);
    }, { signal: this._ac.signal })));
  }
  getSeekDuration() {
    return this.options.audioMode === "external" ? this._extDuration || 0 : this.audio && Number.isFinite(this.audio.duration) ? this.audio.duration : 0;
  }
  getSeekCurrentTime() {
    return this.options.audioMode === "external" ? this.progress * (this._extDuration || 0) : this.audio && Number.isFinite(this.audio.currentTime) ? this.audio.currentTime : 0;
  }
  seekToSeconds(t2) {
    let i2 = this.getSeekDuration();
    if (!i2) return;
    let s2 = m2(t2, 0, i2);
    if (this.options.audioMode === "external") {
      this._requestSeek(s2 / i2), this.updateSeekAccessibility();
      return;
    }
    this.seekTo(s2);
  }
  applySeekLabel(t2 = this.options.title) {
    if (!this.seekEl) return;
    let i2 = this.options.seekLabel || t2 || "Seek";
    this.seekEl.setAttribute("aria-label", i2);
  }
  updateSeekAccessibility() {
    if (!this.seekEl) return;
    let t2 = this.getSeekDuration(), i2 = Math.min(this.getSeekCurrentTime(), t2);
    this.seekEl.setAttribute("aria-valuemax", String(Math.round(t2))), this.seekEl.setAttribute("aria-valuenow", String(Math.round(i2))), this.seekEl.setAttribute("aria-valuetext", j2(this.options.seekValueText || "%1$s of %2$s", E(i2), E(t2)));
  }
  initMediaSession() {
    if (!("mediaSession" in navigator) || !this.options.enableMediaSession || !this.audio) return;
    this._applyMediaMetadata(), navigator.mediaSession.setActionHandler("play", () => this.play()), navigator.mediaSession.setActionHandler("pause", () => this.pause()), navigator.mediaSession.setActionHandler("seekbackward", () => {
      this.seekTo(m2(this.audio.currentTime - 10, 0, this.audio.duration));
    }), navigator.mediaSession.setActionHandler("seekforward", () => {
      this.seekTo(m2(this.audio.currentTime + 10, 0, this.audio.duration));
    }), navigator.mediaSession.setActionHandler("seekto", (s2) => {
      s2.seekTime !== null && this.seekTo(s2.seekTime);
    });
    let t2 = this.options.onNextTrack, i2 = this.options.onPreviousTrack;
    try {
      navigator.mediaSession.setActionHandler("nexttrack", typeof t2 == "function" ? () => t2(this) : null);
    } catch {
    }
    try {
      navigator.mediaSession.setActionHandler("previoustrack", typeof i2 == "function" ? () => i2(this) : null);
    } catch {
    }
  }
  _applyMediaMetadata() {
    !("mediaSession" in navigator) || !this.options.enableMediaSession || (navigator.mediaSession.metadata = new MediaMetadata({ title: this.options.title || this.options.unknownTrackText, artist: this.options.artist || "", album: this.options.album || "", artwork: this.options.artwork ? [{ src: this.options.artwork, sizes: "512x512", type: "image/jpeg" }] : [] }));
  }
  _updateMediaSession(t2) {
    if (!(!("mediaSession" in navigator) || !this.options.enableMediaSession || !this.audio)) try {
      t2 === "playing" && this.initMediaSession(), navigator.mediaSession.playbackState = t2;
      let i2 = this.audio.duration;
      navigator.mediaSession.setPositionState && i2 && isFinite(i2) && navigator.mediaSession.setPositionState({ duration: i2, playbackRate: this.audio.playbackRate || 1, position: m2(this.audio.currentTime, 0, i2) });
    } catch {
    }
  }
  bindEvents() {
    this.playBtn && this.playBtn.addEventListener("click", () => this.togglePlay()), this.audio && (this.audio.addEventListener("loadstart", () => this.setLoading(true)), this.audio.addEventListener("loadedmetadata", () => this.onMetadataLoaded()), this.audio.addEventListener("canplay", () => this.setLoading(false)), this.audio.addEventListener("play", () => this.onPlay()), this.audio.addEventListener("pause", () => this.onPause()), this.audio.addEventListener("ended", () => this.onEnded()), this.audio.addEventListener("error", (i2) => this.onError(i2))), this.canvas.addEventListener("click", (i2) => this.handleCanvasClick(i2)), this._dragging = false, this._seekHover = false, this._handleNear = false, this.canvas.addEventListener("pointerenter", () => {
      this._seekHover = true, this.drawWaveform(), this._updateSeekHandle();
    }), this.canvas.addEventListener("pointerleave", () => {
      this._seekHover = false, this._handleNear = false, this._dragging || this._hideHoverTip(), this.drawWaveform(), this._updateSeekHandle();
    }), this.canvas.addEventListener("pointerdown", (i2) => {
      if (!(i2.pointerType === "mouse" && i2.button !== 0)) {
        this._dragging = true;
        try {
          this.canvas.setPointerCapture(i2.pointerId);
        } catch {
        }
        this._scrubTo(i2.clientX);
      }
    }), this.canvas.addEventListener("pointermove", (i2) => {
      if (this._dragging) {
        this._scrubTo(i2.clientX);
        return;
      }
      let s2 = this.canvas.getBoundingClientRect();
      s2.width && (this._handleNear = Math.abs(i2.clientX - s2.left - this.progress * s2.width) <= 10, this._updateSeekHandle());
    });
    let t2 = (i2) => {
      if (this._dragging) {
        this._dragging = false, this._suppressClick = true;
        try {
          this.canvas.releasePointerCapture(i2.pointerId);
        } catch {
        }
        this._seekFromPointer(i2.clientX), !this._seekHover && !this.options.showHoverTime && this._hideHoverTip(), this._updateSeekHandle();
      }
    };
    this.canvas.addEventListener("pointerup", t2), this.canvas.addEventListener("pointercancel", t2), this.setupHoverTime(), this.setupSeekHandle(), this.resizeHandler = K(() => this.resizeCanvas(), 100), window.addEventListener("resize", this.resizeHandler);
  }
  setupResizeObserver() {
    "ResizeObserver" in window && (this.resizeObserver = new ResizeObserver(() => {
      this.resizeCanvas();
    }), this.canvas?.parentElement && this.resizeObserver.observe(this.canvas.parentElement));
  }
  async load(t2) {
    try {
      this.setLoading(true), this.progress = 0, this.hasError = false, this.audio && (this.audio.src = t2, await new Promise((s2, r2) => {
        let a2 = () => {
          this.audio.removeEventListener("loadedmetadata", a2), this.audio.removeEventListener("error", o2), s2();
        }, o2 = (l2) => {
          this.audio.removeEventListener("loadedmetadata", a2), this.audio.removeEventListener("error", o2), r2(l2);
        };
        this.audio.addEventListener("loadedmetadata", a2), this.audio.addEventListener("error", o2);
      }));
      let i2 = this.options.title || H2(t2);
      if (this.titleEl && (this.titleEl.textContent = i2), this.applySeekLabel(i2), this.options.waveform) this.setWaveformData(this.options.waveform);
      else try {
        let s2 = await F(t2, this.options.samples, this.options.showBPM);
        this.waveformData = s2.peaks, s2.bpm && (this.detectedBPM = s2.bpm, this.updateBPMDisplay());
      } catch (s2) {
        console.warn("[WaveformPlayer] Using placeholder waveform:", s2), this.waveformData = it(this.options.samples);
      }
      this.drawWaveform(), this.renderMarkers(), this.options.onLoad && this.options.onLoad(this);
    } catch (i2) {
      this.onError(i2);
    } finally {
      this.setLoading(false);
    }
  }
  async loadTrack(t2, i2 = null, s2 = null, r2 = {}) {
    let a2 = Object.prototype.hasOwnProperty.call(r2, "artwork"), o2 = Object.prototype.hasOwnProperty.call(r2, "artworkAlt");
    this.isPlaying && this.pause(), this.audio && (this.audio.src = "", this.audio.load()), this.hasError = false, this.errorEl && (this.errorEl.style.display = "none"), this.canvas && (this.canvas.style.opacity = "1"), this.playBtn && (this.playBtn.disabled = false), this.progress = 0, this.waveformData = [], this.options = O(this.options, { url: t2, title: i2 === null ? this.options.title : i2, artist: s2 === null ? this.options.artist : s2, ...r2 }), a2 && (this.options.artwork = r2.artwork || null), o2 ? this.options.artworkAlt = r2.artworkAlt || "" : a2 && (this.options.artworkAlt = this.options.artwork ? z.artworkAlt : ""), r2.preload && this.audio && (this.audio.preload = r2.preload), r2.crossOrigin && this.audio && (this.audio.crossOrigin = r2.crossOrigin), s2 !== null && this.syncArtist(s2), (a2 || o2) && this.syncArtwork(a2 ? r2.artwork : this.options.artwork, o2 ? r2.artworkAlt : this.options.artworkAlt), this.options.markers = r2.markers || [], this.options.waveform = r2.waveform || null, await this.load(t2), r2.autoplay !== false && this.play()?.catch(() => {
    });
  }
  setWaveformData(t2) {
    if (typeof t2 == "string" && t2.trim().endsWith(".json")) {
      fetch(t2.trim()).then((i2) => i2.json()).then((i2) => {
        this.waveformData = Array.isArray(i2) ? i2 : i2.peaks || [], i2.markers && !this.options.markers?.length && (this.options.markers = i2.markers, this.renderMarkers()), this.drawWaveform();
      }).catch(() => {
      });
      return;
    }
    if (typeof t2 == "string") try {
      let i2 = JSON.parse(t2);
      this.waveformData = Array.isArray(i2) ? i2 : [];
    } catch {
      this.waveformData = t2.split(",").map(Number);
    }
    else this.waveformData = Array.isArray(t2) ? t2 : [];
    this.drawWaveform();
  }
  drawWaveform() {
    !this.ctx || this.waveformData.length === 0 || tt(this.ctx, this.canvas, this.waveformData, this.progress, { ...this.options, waveformStyle: this.options.waveformStyle || "bars", color: this.options.waveformColor, progressColor: this.options.progressColor, seekActive: this._seekHover || this._dragging });
  }
  resizeCanvas() {
    if (!this.canvas || this.isDestroying) return;
    let t2 = window.devicePixelRatio || 1, i2 = this.canvas.parentElement.getBoundingClientRect();
    this.canvas.width = i2.width * t2, this.canvas.height = this.options.height * t2, this.canvas.parentElement.style.height = this.options.height + "px", this.drawWaveform();
  }
  renderMarkers() {
    if (!this.markersContainer || (this.markersContainer.innerHTML = "", this._activeMarkerIndex = -1, clearTimeout(this._markerLabelTimer), !this.options.showMarkers || !this.options.markers?.length)) return;
    let t2 = this.getSeekDuration();
    t2 && this.options.markers.forEach((i2, s2) => {
      if (i2.time > t2) {
        console.warn(`[WaveformPlayer] Marker "${i2.label}" at ${i2.time}s exceeds audio duration of ${t2}s`);
        return;
      }
      let r2 = i2.time / t2 * 100, a2 = document.createElement("button");
      a2.className = "waveform-marker", a2.style.left = `${r2}%`, a2.style.backgroundColor = i2.color || "rgba(255, 255, 255, 0.5)", a2.setAttribute("aria-label", i2.label), a2.setAttribute("data-time", i2.time);
      let o2 = document.createElement("span");
      o2.className = "waveform-marker-tooltip", o2.textContent = i2.label, a2.appendChild(o2), a2.addEventListener("click", (l2) => {
        l2.stopPropagation(), this.seekTo(i2.time), this.options.playOnSeek && !this.isPlaying && this.play();
      }), this.markersContainer.appendChild(a2);
    });
  }
  setActiveMarker(t2) {
    if (!this.markersContainer) return;
    this.markersContainer.querySelectorAll(".waveform-marker").forEach((s2, r2) => s2.classList.toggle("active", r2 === t2));
  }
  updateActiveMarker() {
    if (!this.markersContainer) return;
    let t2 = this.markersContainer.querySelectorAll(".waveform-marker");
    if (!t2.length) return;
    let i2 = this.getSeekDuration(), s2 = i2 ? this.progress * i2 : 0, r2 = -1, a2 = -1 / 0;
    t2.forEach((o2, l2) => {
      let n2 = parseFloat(o2.getAttribute("data-time"));
      Number.isFinite(n2) && n2 <= s2 + 0.05 && n2 > a2 && (a2 = n2, r2 = l2);
    }), r2 !== this._activeMarkerIndex && (this._activeMarkerIndex = r2, this.setActiveMarker(r2), clearTimeout(this._markerLabelTimer), t2.forEach((o2, l2) => o2.classList.toggle("show-label", l2 === r2)), r2 >= 0 && (this._markerLabelTimer = setTimeout(() => {
      this.markersContainer?.querySelectorAll(".waveform-marker").forEach((o2) => o2.classList.remove("show-label"));
    }, 2500)));
  }
  setupHoverTime() {
    if (!this.seekEl) return;
    let t2 = document.createElement("div");
    t2.className = "waveform-hover-time", t2.setAttribute("aria-hidden", "true"), this.seekEl.appendChild(t2), this.hoverTimeEl = t2, this.options.showHoverTime && (this.seekEl.addEventListener("pointermove", (i2) => {
      this._dragging || this._updateHoverTip(i2.clientX);
    }), this.seekEl.addEventListener("pointerleave", () => {
      this._dragging || this._hideHoverTip();
    }));
  }
  _updateHoverTip(t2) {
    let i2 = this.hoverTimeEl;
    if (!i2) return;
    let s2 = this.getSeekDuration();
    if (!s2) {
      i2.style.opacity = "0";
      return;
    }
    let r2 = this.canvas.getBoundingClientRect(), a2 = m2((t2 - r2.left) / r2.width);
    i2.textContent = E(a2 * s2), i2.style.left = a2 * 100 + "%", i2.style.opacity = "1";
  }
  _hideHoverTip() {
    this.hoverTimeEl && (this.hoverTimeEl.style.opacity = "0");
  }
  _scrubTo(t2) {
    let i2 = this.canvas.getBoundingClientRect();
    if (!i2.width) return;
    this.progress = m2((t2 - i2.left) / i2.width), this.drawWaveform(), this._updateSeekHandle();
    let s2 = this.getSeekDuration();
    s2 && this.currentTimeEl ? (this.currentTimeEl.textContent = E(this.progress * s2), this._hideHoverTip()) : this._updateHoverTip(t2);
  }
  setupSeekHandle() {
    if (!this.options.seekHandle || this.options.waveformStyle !== "seekbar" || !this.seekEl) return;
    let t2 = document.createElement("div");
    t2.className = "waveform-seek-handle", t2.setAttribute("aria-hidden", "true"), this.seekEl.appendChild(t2), this.seekHandleEl = t2;
  }
  _updateSeekHandle() {
    let t2 = this.seekHandleEl;
    t2 && (t2.style.left = this.progress * 100 + "%", t2.classList.toggle("is-visible", this._seekHover || this._dragging), t2.classList.toggle("is-active", this._dragging || this._handleNear));
  }
  handleCanvasClick(t2) {
    if (this._suppressClick) {
      this._suppressClick = false;
      return;
    }
    this._seekFromPointer(t2.clientX);
  }
  _seekFromPointer(t2) {
    let i2 = this.canvas.getBoundingClientRect();
    if (!i2.width) return;
    let s2 = m2((t2 - i2.left) / i2.width);
    if (this.options.audioMode === "external") {
      this._requestSeek(s2);
      return;
    }
    !this.audio || !this.audio.duration || this.seekToPercent(s2);
  }
  setLoading(t2) {
    this.isLoading = t2, this.loadingEl && (this.loadingEl.style.display = t2 ? "block" : "none"), this.seekEl && this.seekEl.setAttribute("aria-busy", t2 ? "true" : "false");
  }
  onMetadataLoaded() {
    this.isDestroying || (this.totalTimeEl && (this.totalTimeEl.textContent = E(this.audio.duration)), this.renderMarkers(), this.updateSeekAccessibility());
  }
  setPlayButtonState(t2) {
    if (!this.playBtn) return;
    this.playBtn.classList.toggle("playing", t2);
    let i2 = this.playBtn.querySelector(".waveform-icon-play"), s2 = this.playBtn.querySelector(".waveform-icon-pause");
    i2 && (i2.style.display = t2 ? "none" : "flex"), s2 && (s2.style.display = t2 ? "flex" : "none");
  }
  onPlay() {
    this.isDestroying || (this.isPlaying = true, this.setPlayButtonState(true), this.startSmoothUpdate(), this._updateMediaSession("playing"), this._emit("waveformplayer:play", { player: this, url: this.options.url }), this.options.onPlay && this.options.onPlay(this));
  }
  onPause() {
    this.isDestroying || (this.isPlaying = false, this.setPlayButtonState(false), this.stopSmoothUpdate(), this._updateMediaSession("paused"), this._emit("waveformplayer:pause", { player: this, url: this.options.url }), this.options.onPause && this.options.onPause(this));
  }
  onEnded() {
    if (this.isDestroying) return;
    let t2 = this.audio.duration;
    this.progress = 0, this.audio.currentTime = 0, this.drawWaveform(), this.currentTimeEl && (this.currentTimeEl.textContent = "0:00"), this._emit("waveformplayer:ended", { player: this, url: this.options.url, currentTime: t2, duration: t2 }), this.onPause(), this.options.onEnd && this.options.onEnd(this);
  }
  onError(t2) {
    this.isDestroying || (console.error("[WaveformPlayer] Audio error:", t2), this.hasError = true, this.setLoading(false), this.errorEl && (this.errorEl.style.display = "flex"), this.canvas && (this.canvas.style.opacity = "0.2"), this.playBtn && (this.playBtn.disabled = true), this.options.onError && this.options.onError(t2, this));
  }
  startSmoothUpdate() {
    this.stopSmoothUpdate();
    let t2 = () => {
      this.isPlaying && this.audio && this.audio.duration && (this.updateProgress(), this.updateTimer = requestAnimationFrame(t2));
    };
    this.updateTimer = requestAnimationFrame(t2);
  }
  stopSmoothUpdate() {
    this.updateTimer && (cancelAnimationFrame(this.updateTimer), this.updateTimer = null);
  }
  updateProgress() {
    if (!this.audio || !this.audio.duration || this._dragging) return;
    let t2 = this.audio.currentTime / this.audio.duration;
    Math.abs(t2 - this.progress) > 1e-3 && (this.progress = t2, this.drawWaveform(), this._updateSeekHandle()), this.currentTimeEl && (this.currentTimeEl.textContent = E(this.audio.currentTime)), this._emit("waveformplayer:timeupdate", { player: this, currentTime: this.audio.currentTime, duration: this.audio.duration, progress: this.progress, url: this.options.url }), this.options.onTimeUpdate && this.options.onTimeUpdate(this.audio.currentTime, this.audio.duration, this), this.updateActiveMarker(), this.updateSeekAccessibility();
  }
  updateBPMDisplay() {
    let t2 = this.options.bpm || this.detectedBPM;
    this.bpmEl && this.bpmValueEl && t2 && (this.bpmValueEl.textContent = Math.round(t2), this.bpmEl.style.display = "inline-flex");
  }
  refreshTheme() {
    if (!this._autoTheme) return;
    this._scheme = R();
    let t2 = $2(this.options.colorPreset);
    for (let i2 of this._presetKeys || []) i2 in t2 && (this.options[i2] = t2[i2]);
    this._applyThemeColors();
  }
  _applyThemeColors() {
    this.container.classList.toggle("waveform-theme-light", this._scheme === "light"), this.canvas && this.drawWaveform();
  }
  static _watchTheme() {
    if (e2._themeWatch || typeof document > "u") return;
    let t2 = () => requestAnimationFrame(() => {
      e2.instances.forEach((a2) => {
        try {
          a2.refreshTheme();
        } catch {
        }
      });
    }), i2 = { attributes: true, attributeFilter: ["class", "data-theme", "data-color-scheme", "style"] }, s2 = new MutationObserver(t2);
    s2.observe(document.documentElement, i2), document.body && s2.observe(document.body, i2);
    let r2 = null;
    try {
      r2 = window.matchMedia("(prefers-color-scheme: dark)"), r2.addEventListener("change", t2);
    } catch {
    }
    e2._themeWatch = { obs: s2, mq: r2, refresh: t2 };
  }
  updateSpeedUI() {
    if (!this.audio) return;
    let t2 = this.container.querySelector(".speed-value");
    if (t2) {
      let i2 = this.audio.playbackRate;
      t2.textContent = i2 === 1 ? "1x" : `${i2}x`;
    }
    this.container.querySelectorAll(".speed-option").forEach((i2) => {
      let s2 = parseFloat(i2.dataset.rate) === this.audio.playbackRate;
      i2.classList.toggle("active", s2), i2.setAttribute("aria-checked", s2 ? "true" : "false");
    });
  }
  play() {
    if (this.options.singlePlay && e2.currentlyPlaying && e2.currentlyPlaying !== this && e2.currentlyPlaying.pause(), this.options.audioMode === "external") {
      this._emit("waveformplayer:request-play", this._buildTrackDetail(), true).defaultPrevented || (e2.currentlyPlaying = this);
      return;
    }
    return e2.currentlyPlaying = this, this.audio.play();
  }
  pause() {
    if (e2.currentlyPlaying === this && (e2.currentlyPlaying = null), this.options.audioMode === "external") {
      this._emit("waveformplayer:request-pause", this._buildTrackDetail(), true);
      return;
    }
    this.audio.pause();
  }
  _buildTrackDetail() {
    return { url: this.options.url, title: this.options.title, artist: this.options.artist, artwork: this.options.artwork, markers: this.options.markers, waveform: this.options.waveform, id: this.id, player: this };
  }
  setPlayingState(t2) {
    let i2 = this.isPlaying;
    this.isPlaying = !!t2, this.setPlayButtonState(this.isPlaying), this.isPlaying && !i2 ? (this.startSmoothUpdate?.(), this._emit("waveformplayer:play", { player: this, url: this.options.url }), this.options.onPlay && this.options.onPlay(this)) : !this.isPlaying && i2 && (this.stopSmoothUpdate?.(), this._emit("waveformplayer:pause", { player: this, url: this.options.url }), this.options.onPause && this.options.onPause(this));
  }
  setProgress(t2, i2) {
    !i2 || i2 <= 0 || (this.progress = m2(t2 / i2), this.currentTimeEl && (this.currentTimeEl.textContent = E(t2)), this._extDuration = i2, this.totalTimeEl && (!this.totalTimeEl.dataset._extSet || this.totalTimeEl.dataset._extDur !== String(i2)) && (this.totalTimeEl.textContent = E(i2), this.totalTimeEl.dataset._extSet = "1", this.totalTimeEl.dataset._extDur = String(i2)), this.drawWaveform?.(), this.updateActiveMarker(), this._emit("waveformplayer:timeupdate", { player: this, currentTime: t2, duration: i2, progress: this.progress, url: this.options.url }), this.options.onTimeUpdate && this.options.onTimeUpdate(t2, i2, this), this.progress >= 1 ? this._extEnded || (this._extEnded = true, this._emit("waveformplayer:ended", { player: this, url: this.options.url, currentTime: i2, duration: i2 }), this.options.onEnd && this.options.onEnd(this)) : this._extEnded = false, this.updateSeekAccessibility());
  }
  togglePlay() {
    this.isPlaying ? this.pause() : this.play();
  }
  seekTo(t2) {
    this.audio && this.audio.duration && (this.audio.currentTime = m2(t2, 0, this.audio.duration), this.updateProgress());
  }
  seekToPercent(t2) {
    this.audio && this.audio.duration && (this.audio.currentTime = this.audio.duration * m2(t2), this.updateProgress());
  }
  setVolume(t2) {
    let i2 = Number(t2);
    this.audio && Number.isFinite(i2) && (this.audio.volume = m2(i2));
  }
  setPlaybackRate(t2) {
    if (!this.audio) return;
    let i2 = m2(t2, 0.5, 2);
    this.audio.playbackRate = i2, this.options.playbackRate = i2, this.updateSpeedUI();
  }
  destroy() {
    this.isDestroying = true, this._emit("waveformplayer:destroy", { player: this, url: this.options.url }), this.pause(), this.stopSmoothUpdate(), clearTimeout(this._markerLabelTimer), this._ac?.abort(), this.resizeObserver && (this.resizeObserver.disconnect(), this.resizeObserver = null), this.resizeHandler && (window.removeEventListener("resize", this.resizeHandler), this.resizeHandler = null), e2.instances.delete(this.id), e2.currentlyPlaying === this && (e2.currentlyPlaying = null), this.audio && (this.audio.pause(), this.audio.src = "", this.audio.load(), this.audio = null), this.container.innerHTML = "", this.canvas = null, this.ctx = null, this.playBtn = null, this.waveformData = [];
  }
  static getInstance(t2) {
    if (typeof t2 == "string") {
      let i2 = this.instances.get(t2);
      if (i2) return i2;
      let s2 = document.getElementById(t2);
      if (s2) return Array.from(this.instances.values()).find((r2) => r2.container === s2);
    }
    if (t2 instanceof HTMLElement) return Array.from(this.instances.values()).find((i2) => i2.container === t2);
  }
  static getAllInstances() {
    return Array.from(this.instances.values());
  }
  static destroyAll() {
    this.instances.forEach((t2) => t2.destroy()), this.instances.clear();
  }
  static async generateWaveformData(t2, i2 = 1800) {
    try {
      return (await F(t2, i2)).peaks;
    } catch (s2) {
      throw console.error("[WaveformPlayer] Failed to generate waveform:", s2), s2;
    }
  }
  static getPeaksUrl(t2) {
    if (!t2) return;
    let i2 = t2.replace(/\.(mp3|wav|ogg|flac|m4a|aac)(\?[^#]*)?(#.*)?$/i, ".json$2$3");
    return i2 === t2 ? void 0 : i2;
  }
};
A.utils = { formatTime: E, extractTitleFromUrl: H2, escapeHtml: S, isSafeHref: V, parseDataAttributes: D };
var U = () => typeof window < "u" && typeof document < "u";
function q() {
  if (!U()) return;
  document.querySelectorAll("[data-waveform-player]").forEach((t2) => {
    if (t2.dataset.waveformInitialized !== "true") try {
      new A(t2), t2.dataset.waveformInitialized = "true";
    } catch (i2) {
      console.error("[WaveformPlayer] Failed to initialize:", i2, t2);
    }
  });
}
U() && (document.readyState === "loading" ? document.addEventListener("DOMContentLoaded", q) : q());
A.init = q;
U() && (window.WaveformPlayer = A);
var Bt = A;

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
  if (w(possibleColor).isValid()) {
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
  if (!colorValue || w(colorValue).isValid()) {
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
  return resolvedColor && w(resolvedColor).isValid() ? resolvedColor : colorValue;
}
function getResolvedGradientStops(element, gradientValue) {
  const stops = getWaveformGradientStops(gradientValue)?.map((colorValue) => resolveColorValue(element, colorValue)).filter((colorValue) => w(colorValue).isValid());
  return stops?.length > 1 ? stops : void 0;
}
function applyAlpha(colorValue, alpha) {
  if (Array.isArray(colorValue)) {
    return colorValue.map(
      (color) => w(color).alpha(alpha).toRgbString()
    );
  }
  return w(colorValue).alpha(alpha).toRgbString();
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
  const instance = new Bt(container);
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
