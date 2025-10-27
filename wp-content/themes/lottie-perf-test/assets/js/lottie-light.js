!function(t) {
    "use strict";
    let e, s, i, r, a = (s = 0,
    () => (s++,
    `__lottie_element_${s}`));
    var n, o, h, l, p, m, d, c, u, f, g, y, b, v, _ = ((n = {}).Bounce = "bounce",
    n.Normal = "normal",
    n), w = ((o = {}).Complete = "complete",
    o.Destroyed = "destroyed",
    o.Error = "error",
    o.Frame = "frame",
    o.Freeze = "freeze",
    o.Load = "load",
    o.Loop = "loop",
    o.Next = "next",
    o.Pause = "pause",
    o.Play = "play",
    o.Previous = "previous",
    o.Ready = "ready",
    o.Rendered = "rendered",
    o.Stop = "stop",
    o), S = ((h = {}).Contain = "xMidYMid meet",
    h.Cover = "xMidYMid slice",
    h.Initial = "none",
    h.None = "xMinYMin slice",
    h), E = ((l = {}).Canvas = "canvas",
    l.HTML = "html",
    l.SVG = "svg",
    l);
    let k = !("undefined" != typeof window && document);
    class M {
        constructor(t, e, s, i) {
            this.type = t,
            this.currentTime = e,
            this.totalTime = s,
            this.direction = i < 0 ? -1 : 1
        }
    }
    class x {
        constructor(t, e) {
            this.type = t,
            this.direction = e < 0 ? -1 : 1
        }
    }
    class P {
        constructor(t, e, s, i) {
            this.type = t,
            this.direction = s,
            this.currentTime = e,
            this.totalTime = i
        }
    }
    class C {
        constructor(t, e, s, i) {
            this.type = t,
            this.currentLoop = s,
            this.totalLoops = e,
            this.direction = i < 0 ? -1 : 1
        }
    }
    class A {
        constructor(t, e, s) {
            this.type = t,
            this.firstFrame = e,
            this.totalFrames = s
        }
    }
    class T {
        constructor(t, e) {
            this.type = t,
            this.target = e
        }
    }
    class D {
        constructor(t, e) {
            this.type = "renderFrameError",
            this.nativeError = t,
            this.currentTime = e
        }
    }
    class I {
        constructor(t, e) {
            this.type = "configError",
            this.nativeError = t
        }
    }
    class L {
        addEventListener(t, e) {
            return this._cbs[t] = this._cbs[t] ?? [],
            this._cbs[t].push(e),
            () => {
                this.removeEventListener(t, e)
            }
        }
        removeEventListener(t, e) {
            if (!e) {
                this._cbs[t] = null;
                return
            }
            if (this._cbs[t]) {
                let s = 0
                  , {length: i} = this._cbs[t];
                for (; s < i; )
                    this._cbs[t][s] === e && (this._cbs[t].splice(s, 1),
                    s--,
                    i--),
                    s++;
                0 === this._cbs[t].length && (this._cbs[t] = null)
            }
        }
        triggerEvent(t, e) {
            if (!this._cbs[t])
                return;
            let {length: s} = this._cbs[t];
            for (let i = 0; i < s; i++)
                this._cbs[t][i]?.(e)
        }
        constructor() {
            this._cbs = {}
        }
    }
    var F = ((p = {}).Float32 = "float32",
    p.Int16 = "int16",
    p.Int32 = "int32",
    p.Uint8 = "uint8",
    p.Uint8c = "uint8c",
    p)
      , $ = ((m = {}).MouseModifier = "ms",
    m.OffsetPathModifier = "op",
    m.PuckerAndBloatModifier = "pb",
    m.RepeaterModifier = "rp",
    m.RoundCornersModifier = "rd",
    m.TrimModifier = "tm",
    m.ZigZagModifier = "zz",
    m)
      , N = ((d = {}).Canvas = "canvas",
    d.HTML = "html",
    d.SVG = "svg",
    d)
      , O = ((c = {}).Ellipse = "el",
    c.Fill = "fl",
    c.GradientFill = "gf",
    c.GradientStroke = "gs",
    c.Group = "gr",
    c.Merge = "mm",
    c.NoStyle = "no",
    c.OffsetPath = "op",
    c.Path = "sh",
    c.PolygonStar = "sr",
    c.PuckerBloat = "pb",
    c.Rectangle = "rc",
    c.Repeater = "rp",
    c.RoundedCorners = "rd",
    c.Stroke = "st",
    c.Transform = "tr",
    c.Trim = "tm",
    c.Twist = "tw",
    c.Unknown = "ms",
    c.ZigZag = "zz",
    c)
      , V = ((u = {}).TransformEffect = "transformEffect",
    u)
      , z = ((f = {}).MultiDimensional = "multidimensiional",
    f.Shape = "shape",
    f.TextSelector = "textSelector",
    f.Transform = "transform",
    f.UniDimensional = "unidimensional",
    f);
    let R = {
        1: "butt",
        2: "round",
        3: "square"
    }
      , B = {
        1: "miter",
        2: "round",
        3: "bevel"
    }
      , q = {}
      , j = (t, e) => 1e5 * Math.abs(t - e) <= Math.min(Math.abs(t), Math.abs(e))
      , H = t => 1e-5 >= Math.abs(t)
      , G = (t, e) => j(t[0], e[0]) && j(t[1], e[1])
      , W = (i = 0,
    () => (i++,
    `__lottie_element_${i}`))
      , U = t => Symbol.iterator in Object(t) && t.length > 0
      , Y = t => U(t) && "number" == typeof t[0]
      , X = t => !(!t || U(t)) && "_type"in t && "ShapePath" === t._type
      , J = Math.PI / 180
      , Z = "http://www.w3.org/1999/xlink"
      , K = "http://www.w3.org/2000/svg"
      , Q = !("undefined" != typeof window && document)
      , tt = "undefined" != typeof navigator
      , te = !!tt && /^(?:(?!chrome|android).)*safari/i.test(navigator.userAgent);
    class ts {
        constructor(t) {
            this.audios = [],
            this.audioFactory = t,
            this._volume = 1,
            this._isMuted = !1
        }
        addAudio(t) {
            this.audios.push(t)
        }
        createAudio(t) {
            return this.audioFactory ? this.audioFactory(t) : !Q && "Howl"in window ? new window.Howl({
                src: [t]
            }) : {
                isPlaying: !1,
                play: () => {
                    this.isPlaying = !0
                }
                ,
                playing: () => {
                    throw Error(`${this.constructor.name}: Method playing is not implemented`)
                }
                ,
                rate: () => {
                    throw Error(`${this.constructor.name}: Method rate is not implemented`)
                }
                ,
                seek: () => {
                    this.isPlaying = !1
                }
                ,
                setVolume: () => {
                    throw Error(`${this.constructor.name}: Method setVolume is not implemented`)
                }
            }
        }
        getVolume() {
            return this._volume
        }
        mute() {
            this._isMuted = !0,
            this._updateVolume()
        }
        pause() {
            for (let t of this.audios)
                t.pause()
        }
        resume() {
            for (let t of this.audios)
                t.resume()
        }
        setAudioFactory(t) {
            this.audioFactory = t
        }
        setRate(t) {
            for (let e of this.audios)
                e.setRate(t)
        }
        setVolume(t) {
            this._volume = t,
            this._updateVolume()
        }
        unmute() {
            this._isMuted = !1,
            this._updateVolume()
        }
        _updateVolume() {
            let {length: t} = this.audios;
            for (let e = 0; e < t; e++)
                this.audios[e]?.volume(!this._isMuted * this._volume)
        }
    }
    function ti(t, e, s, i) {
        let r, a = new XMLHttpRequest;
        try {
            a.responseType = "json"
        } catch (t) {}
        a.onreadystatechange = () => {
            if (4 === a.readyState) {
                if (200 === a.status)
                    return void s(r = tr(a));
                try {
                    r = tr(a),
                    s(r)
                } catch (t) {
                    i?.(t)
                }
            }
        }
        ;
        try {
            a.open("GET", t, !0)
        } catch (s) {
            a.open("GET", `${e}/${t}`, !0)
        }
        a.send()
    }
    function tr(t) {
        let e = t.getResponseHeader("content-type");
        return e && "json" === t.responseType && e.includes("json") || t.response && "object" == typeof t.response ? t.response : t.response && "string" == typeof t.response ? JSON.parse(t.response) : t.responseText ? JSON.parse(t.responseText) : null
    }
    function ta(t, e) {
        let s, {length: i} = t;
        for (let a = 0; a < i; a++)
            if ("ks"in (s = t[a]) && !s.completed) {
                if (s.completed = !0,
                s.hasMask) {
                    let t = s.masksProperties
                      , {length: e} = t ?? [];
                    for (let s = 0; s < e; s++) {
                        let e = t?.[s]?.pt?.k;
                        if (!e)
                            continue;
                        if (!U(e)) {
                            th(e);
                            continue
                        }
                        let {length: i} = e;
                        for (let t = 0; t < i; t++) {
                            let s = e[t]?.e ?? []
                              , i = e[t]?.s ?? [];
                            s.length > 0 && th(s[0]),
                            i.length > 0 && th(i[0])
                        }
                    }
                }
                switch (s.ty) {
                case 0:
                    s.layers = tn(s.refId, e),
                    ta(s.layers, e);
                    break;
                case 4:
                    to(s.shapes);
                    break;
                case 5:
                    var r;
                    r = s,
                    r.t?.a?.length === 0 && r.t.p
                }
            }
    }
    function tn(t, e) {
        if (!t || !e)
            return;
        let s = function(t, e) {
            let s = 0
              , {length: i} = e;
            for (; s < i; ) {
                if (e[s]?.id === t)
                    return e[s];
                s++
            }
            return null
        }(t, e);
        return s?.layers ? s.layers.__used ? JSON.parse(JSON.stringify(s.layers)) : (s.layers.__used = !0,
        s.layers) : null
    }
    function to(t=[]) {
        let {length: e} = t;
        for (let s = e - 1; s >= 0; s--) {
            if (t[s]?.ty === O.Group) {
                to(t[s]?.it);
                continue
            }
            if (t[s]?.ty === O.Path) {
                let e = t[s]?.ks?.k;
                if (!U(e)) {
                    th(e);
                    continue
                }
                let {length: i} = e;
                for (let t = 0; t < i; t++) {
                    let s = e[t]?.e
                      , i = e[t]?.s;
                    s && th(s[0]),
                    i && th(i[0])
                }
            }
        }
    }
    function th(t) {
        if (!t)
            return;
        let {length: e} = t.i;
        for (let s = 0; s < e; s++)
            t.i[s][0] += t.v[s]?.[0] ?? 0,
            t.i[s][1] += t.v[s]?.[1] ?? 0,
            t.o[s][0] += t.v[s]?.[0] ?? 0,
            t.o[s][1] += t.v[s]?.[1] ?? 0
    }
    function tl(t, e) {
        let s = e ? e.split(".").map(Number) : [100, 100, 100];
        return t[0] > s[0] || !(s[0] > t[0]) && (t[1] > s[1] || !(s[1] > t[1]) && (t[2] > s[2] || !(s[2] > t[2]) && null))
    }
    let tp = (t, e, s) => {
        let {length: i} = t;
        for (let r = 0; r < i; r++)
            t[r]?.ty === s && e(t[r])
    }
    , tm = ( () => {
        let t = [4, 4, 14]
          , e = t => {
            let e = t.t?.d;
            t.t && e && (t.t.d = {
                k: [{
                    s: e,
                    t: 0
                }]
            })
        }
        ;
        return s => {
            if (!tl(t, s.v))
                return;
            tp(s.layers, e, 5);
            let {length: i} = s.assets;
            for (let t = 0; t < i; t++)
                s.assets[t]?.layers && tp(s.assets[t]?.layers, e, 5)
        }
    }
    )(), td = ( () => {
        let t = [4, 7, 99];
        return e => {
            if (!e.chars || tl(t, e.v))
                return;
            let {length: s} = e.chars;
            for (let t = 0; t < s; t++) {
                let s = e.chars[t];
                s?.data?.shapes && (to(s.data.shapes),
                s.data.ip = 0,
                s.data.op = 99999,
                s.data.st = 0,
                s.data.sr = 1,
                s.data.ks = {
                    a: {
                        a: 0,
                        k: [0, 0]
                    },
                    o: {
                        a: 0,
                        k: 100
                    },
                    p: {
                        a: 0,
                        k: [0, 0]
                    },
                    r: {
                        a: 0,
                        k: 0
                    },
                    s: {
                        a: 0,
                        k: [100, 100]
                    }
                },
                e.chars[t]?.t || (s.data.shapes.push({
                    ty: O.NoStyle
                }),
                s.data.shapes[0]?.it?.push({
                    a: {
                        a: 0,
                        k: [0, 0]
                    },
                    o: {
                        a: 0,
                        k: 100
                    },
                    p: {
                        a: 0,
                        k: [0, 0]
                    },
                    r: {
                        a: 0,
                        k: 0
                    },
                    s: {
                        a: 0,
                        k: [100, 100]
                    },
                    sa: {
                        a: 0,
                        k: 0
                    },
                    sk: {
                        a: 0,
                        k: 0
                    },
                    ty: O.Transform
                })))
            }
        }
    }
    )(), tc = ( () => {
        let t = [5, 7, 15]
          , e = t => {
            let e = t.t?.p;
            e && ("number" == typeof e.a && (e.a = {
                a: 0,
                k: e.a
            }),
            "number" == typeof e.p && (e.p = {
                a: 0,
                k: e.p
            }),
            "number" == typeof e.r && (e.r = {
                a: 0,
                k: e.r
            }))
        }
        ;
        return s => {
            if (!tl(t, s.v))
                return;
            tp(s.layers, e, 5);
            let {length: i} = s.assets;
            for (let t = 0; t < i; t++)
                s.assets[t]?.layers && tp(s.assets[t]?.layers, e, 5)
        }
    }
    )(), tu = ( () => {
        let t = [4, 1, 9]
          , e = t => {
            let {length: s} = t;
            for (let i = 0; i < s; i++) {
                if (t[i]?.ty === O.Group) {
                    e(t[i]?.it ?? []);
                    continue
                }
                if (t[i]?.ty !== O.Fill && t[i]?.ty !== O.Stroke)
                    continue;
                let s = t[i]?.c?.k;
                if (!s || "number" == typeof s)
                    continue;
                if (Y(s) && s.length >= 4) {
                    s[0] /= 255,
                    s[1] /= 255,
                    s[2] /= 255,
                    s[3] /= 255;
                    continue
                }
                let {length: r} = s;
                for (let t = 0; t < r; t++) {
                    let e = s[t];
                    e.s[0] /= 255,
                    e.s[1] /= 255,
                    e.s[2] /= 255,
                    e.s[3] /= 255,
                    e.e[0] /= 255,
                    e.e[1] /= 255,
                    e.e[2] /= 255,
                    e.e[3] /= 255
                }
            }
        }
          , s = t => {
            let {length: s} = t;
            for (let i = 0; i < s; i++)
                t[i]?.ty === 4 && e(t[i]?.shapes ?? [])
        }
        ;
        return e => {
            if (!tl(t, e.v))
                return;
            s(e.layers);
            let {length: i} = e.assets;
            for (let t = 0; t < i; t++)
                e.assets[t]?.layers && s(e.assets[t]?.layers)
        }
    }
    )(), tf = ( () => {
        let t = [4, 4, 18]
          , e = t => {
            let {length: s} = t;
            for (let i = s - 1; i >= 0; i--) {
                if (t[i]?.ty === O.Group) {
                    e(t[i]?.it ?? []);
                    continue
                }
                if (t[i]?.ty !== O.Path)
                    continue;
                let s = t[i]?.ks?.k;
                if (!s)
                    continue;
                let r = !!t[i]?.closed;
                if (!U(s)) {
                    s.c = r;
                    continue
                }
                let {length: a} = s;
                for (let t = 0; t < a; t += 1) {
                    let e = s[t]?.e ?? []
                      , i = s[t]?.s ?? [];
                    e.length > 0 && (e[0].c = r),
                    i.length > 0 && (i[0].c = r)
                }
            }
        }
          , s = t => {
            let s, {length: i} = t;
            for (let r = 0; r < i; r++) {
                if (s = t[r],
                s?.hasMask) {
                    let t = s.masksProperties
                      , {length: e} = t ?? [];
                    for (let s = 0; s < e; s++) {
                        let e = t?.[s]?.pt?.k;
                        if (!e)
                            continue;
                        let i = !!t[s]?.cl;
                        if (!U(e)) {
                            e.c = i;
                            continue
                        }
                        let {length: r} = e;
                        for (let t = 0; t < r; t++) {
                            let s = e[t]?.e ?? []
                              , r = e[t]?.s ?? [];
                            s.length > 0 && (s[0].c = i),
                            r.length > 0 && (r[0].c = i)
                        }
                    }
                }
                s?.ty === 4 && e(s.shapes)
            }
        }
        ;
        return e => {
            if (!tl(t, e.v))
                return;
            s(e.layers);
            let {length: i} = e.assets;
            for (let t = 0; t < i; t++)
                e.assets[t]?.layers && s(e.assets[t]?.layers)
        }
    }
    )(), tg = {
        checkChars: td,
        checkColors: tu,
        checkPathProperties: tc,
        checkShapes: tf,
        completeData: function(t) {
            t.__complete || (tu(t),
            tm(t),
            td(t),
            tc(t),
            tf(t),
            ta(t.layers, t.assets),
            function(t, e) {
                if (!t)
                    return;
                let {length: s} = t;
                for (let i = 0; i < s; i++) {
                    if (t[i]?.t !== 1)
                        continue;
                    let {data: s} = t[i] ?? {};
                    s && (s.layers = tn(s.refId, e)),
                    ta(t[i]?.data?.layers ?? [], e)
                }
            }(t.chars, t.assets),
            t.__complete = !0)
        },
        completeLayers: ta
    }, ty = 1, tb, tv = "Function not implemented.", t_ = {
        addEventListener: (t, e, s) => {
            throw Error(tv)
        }
        ,
        dispatchEvent: t => {
            throw Error(tv)
        }
        ,
        onerror: null,
        onmessage: t => {
            throw Error("workerProxy: Method onmessage not implemented")
        }
        ,
        onmessageerror: null,
        postMessage: t => {
            tb({
                data: t
            })
        }
        ,
        removeEventListener: (t, e, s) => {
            throw Error(tv)
        }
        ,
        terminate: () => {
            throw Error(tv)
        }
    }, tw = {
        postMessage: t => {
            t_.onmessage && t_.onmessage({
                AT_TARGET: 2,
                bubbles: !1,
                BUBBLING_PHASE: 3,
                cancelable: !1,
                cancelBubble: !1,
                CAPTURING_PHASE: 1,
                composed: !1,
                composedPath: () => {
                    throw Error(tv)
                }
                ,
                currentTarget: null,
                data: t,
                defaultPrevented: !1,
                eventPhase: 0,
                initEvent: (t, e, s) => {
                    throw Error(tv)
                }
                ,
                initMessageEvent: (t, e, s, i, r, a, n, o) => {
                    throw Error(tv)
                }
                ,
                isTrusted: !1,
                lastEventId: "",
                NONE: 0,
                origin: "",
                ports: [],
                preventDefault: () => {
                    throw Error(tv)
                }
                ,
                returnValue: !1,
                source: null,
                srcElement: null,
                stopImmediatePropagation: () => {
                    throw Error(tv)
                }
                ,
                stopPropagation: () => {
                    throw Error(tv)
                }
                ,
                target: null,
                timeStamp: 0,
                type: ""
            })
        }
    }, tS = {};
    function tE() {
        e || (tb = t => {
            if (tw.dataManager = tw.dataManager ?? tg,
            "loadAnimation" === t.data.type)
                return void ti(t.data.path, t.data.fullPath, e => {
                    e && tg.completeData(e),
                    tw.postMessage({
                        id: t.data.id,
                        payload: e,
                        status: "success"
                    })
                }
                , () => {
                    tw.postMessage({
                        id: t.data.id,
                        status: "error"
                    })
                }
                );
            if ("complete" === t.data.type) {
                let {animation: e, id: s} = t.data;
                tg.completeData(e),
                tw.postMessage({
                    id: s,
                    payload: e,
                    status: "success"
                });
                return
            }
            "loadData" === t.data.type && ti(t.data.path, t.data.fullPath, e => {
                tw.postMessage({
                    id: t.data.id,
                    payload: e,
                    status: "success"
                })
            }
            , () => {
                tw.postMessage({
                    id: t.data.id,
                    status: "error"
                })
            }
            )
        }
        ,
        (e = t_).onmessage = ({data: t}) => {
            let {id: e, payload: s, status: i} = t
              , r = tS[e];
            if (tS[e] = null,
            "success" === i)
                return void r?.onComplete(s);
            r?.onError && r.onError()
        }
        )
    }
    function tk(t, e) {
        ty++;
        let s = `processId_${ty}`;
        try {
            return tS[s] = {
                onComplete: t,
                onError: e
            },
            s
        } catch (t) {
            throw console.error("DataManager}:\n", t),
            Error("Could not create animation proccess")
        }
    }
    function tM(t, s, i) {
        tE();
        let r = tk(s, i);
        e?.postMessage({
            fullPath: Q ? t : window.location.origin + window.location.pathname,
            id: r,
            path: t,
            type: "loadData"
        })
    }
    function tx(t, s, i) {
        tE();
        let r = tk(s, i);
        e?.postMessage({
            animation: t,
            id: r,
            type: "complete"
        })
    }
    class tP {
        getComposition(t) {
            let e = 0
              , {length: s} = this.compositions;
            for (; e < s; ) {
                if (this.compositions[e]?.data && this.compositions[e]?.data?.nm === t)
                    return this.compositions[e]?.data?.xt && this.compositions[e]?.prepareFrame(this.currentFrame),
                    this.compositions[e]?.compInterface;
                e++
            }
            return null
        }
        registerComposition(t) {
            this.compositions.push(t)
        }
        constructor() {
            this.compositions = [],
            this.currentFrame = 0
        }
    }
    function tC(t) {
        return Q ? null : document.createElement(t)
    }
    function tA(t) {
        return Q ? null : document.createElementNS(K, t)
    }
    class tT {
        constructor() {
            this._imageLoaded = this.imageLoaded.bind(this),
            this._footageLoaded = this.footageLoaded.bind(this),
            this.testImageLoaded = this.testImageLoaded.bind(this),
            this.createFootageData = this.createFootageData.bind(this),
            this.assetsPath = "",
            this.path = "",
            this.totalImages = 0,
            this.totalFootages = 0,
            this.loadedAssets = 0,
            this.loadedFootagesCount = 0,
            this.imagesLoadedCb = null,
            this.images = [],
            this.proxyImage = this._createProxyImage()
        }
        createFootageData(t) {
            let e = {
                assetData: t,
                img: null
            };
            return tM(this.getAssetsPath(t, this.assetsPath, this.path), t => {
                t && (e.img = t),
                this._footageLoaded()
            }
            , () => {
                this._footageLoaded()
            }
            ),
            e
        }
        createImageData(t) {
            let e = this.getAssetsPath(t, this.assetsPath, this.path)
              , s = tA("image")
              , i = {
                assetData: t,
                img: s
            };
            return te ? this.testImageLoaded(s) : s.addEventListener("load", this._imageLoaded, !1),
            s.addEventListener("error", () => {
                this.proxyImage && (i.img = this.proxyImage),
                this._imageLoaded()
            }
            , !1),
            s.setAttributeNS(Z, "href", e),
            this._elementHelper?.append ? this._elementHelper.append(s) : this._elementHelper?.appendChild(s),
            i
        }
        destroy() {
            this.imagesLoadedCb = null,
            this.images.length = 0
        }
        footageLoaded() {
            this.loadedFootagesCount++,
            this.loadedAssets === this.totalImages && this.loadedFootagesCount === this.totalFootages && this.imagesLoadedCb && this.imagesLoadedCb(null)
        }
        getAsset(t) {
            let e = 0
              , {length: s} = this.images;
            for (; e < s; ) {
                if (this.images[e]?.assetData === t)
                    return this.images[e]?.img ?? null;
                e++
            }
            return null
        }
        imageLoaded() {
            this.loadedAssets++,
            this.loadedAssets === this.totalImages && this.loadedFootagesCount === this.totalFootages && this.imagesLoadedCb && this.imagesLoadedCb(null)
        }
        loadAssets(t, e) {
            this.imagesLoadedCb = e;
            let {length: s} = t;
            for (let e = 0; e < s; e++)
                if (!t[e]?.layers) {
                    if ((!t[e]?.t || t[e]?.t === "seq") && this._createImageData) {
                        this.totalImages++;
                        let s = this._createImageData(t[e]);
                        s && this.images.push(s);
                        continue
                    }
                    3 === Number(t[e]?.t) && (this.totalFootages++,
                    this.images.push(this.createFootageData(t[e])))
                }
        }
        loadedFootages() {
            return this.totalFootages === this.loadedFootagesCount
        }
        loadedImages() {
            return this.totalImages === this.loadedAssets
        }
        setAssetsPath(t) {
            this.assetsPath = t || ""
        }
        setCacheType(t, e) {
            t === N.SVG ? (this._elementHelper = e,
            this._createImageData = this.createImageData.bind(this)) : this._createImageData = this.createImgData.bind(this)
        }
        setPath(t) {
            this.path = t || ""
        }
        _createProxyImage() {
            if (Q)
                return null;
            let t = tC(N.Canvas);
            t.width = 1,
            t.height = 1;
            let e = t.getContext("2d");
            return e && (e.fillStyle = "rgba(0,0,0,0)",
            e.fillRect(0, 0, 1, 1)),
            t
        }
        createImgData(t) {
            let e = this.getAssetsPath(t, this.assetsPath, this.path)
              , s = tC("img")
              , i = {
                assetData: t,
                img: s
            };
            return s.crossOrigin = "anonymous",
            s.addEventListener("load", this._imageLoaded, !1),
            s.addEventListener("error", () => {
                this.proxyImage && (i.img = this.proxyImage),
                this._imageLoaded()
            }
            , !1),
            s.src = e,
            i
        }
        getAssetsPath(t, e, s) {
            if (t.e)
                return t.p || "";
            if (e) {
                let s = t.p;
                return s?.indexOf("images/") !== -1 && (s = s?.split("/")[1]),
                `${e}${s || ""}`
            }
            let i = s;
            return i += t.u ?? "",
            i += t.p ?? ""
        }
        testImageLoaded(t) {
            if (Q)
                return;
            let e = 0
              , s = setInterval( () => {
                (t.getBBox().width || e > 500) && (this._imageLoaded(),
                clearInterval(s)),
                e++
            }
            , 50)
        }
    }
    let tD = t => {
        let e = t.split("\r\n")
          , s = {}
          , {length: i} = e
          , r = 0;
        for (let t = 0; t < i; t++) {
            let i = e[t]?.split(":") ?? [];
            2 === i.length && (s[i[0]] = i[1]?.trim(),
            r++)
        }
        if (0 === r)
            throw Error("Could not parse markers");
        return s
    }
    ;
    class tI extends L {
        constructor() {
            super(),
            this.wrapper = null,
            this.autoloadSegments = !1,
            this.onComplete = null,
            this.onDestroy = null,
            this.onEnterFrame = null,
            this.onLoopComplete = null,
            this.onSegmentStart = null,
            this._cbs = {},
            this.name = "",
            this.path = "",
            this.isLoaded = !1,
            this.currentFrame = 0,
            this.currentRawFrame = 0,
            this.firstFrame = 0,
            this.totalFrames = 0,
            this.frameRate = 60,
            this.frameMult = 0,
            this.playSpeed = 1,
            this.playDirection = 1,
            this.frameModifier = 1,
            this.playCount = 0,
            this.animationData = {},
            this.assets = [],
            this.isPaused = !0,
            this.autoplay = !1,
            this.loop = !0,
            this.renderer = null,
            this.animationID = W(),
            this.assetsPath = "",
            this.timeCompleted = 0,
            this.segmentPos = 0,
            this.isSubframeEnabled = !0,
            this.segments = [],
            this._idle = !0,
            this._completedLoop = !1,
            this.projectInterface = new tP,
            this.imagePreloader = new tT,
            this.audioController = new ts(void 0),
            this.markers = [],
            this.configAnimation = this.configAnimation.bind(this),
            this.onSetupError = this.onSetupError.bind(this),
            this.onSegmentComplete = this.onSegmentComplete.bind(this),
            this.drawnFrameEvent = new M("drawnFrame",0,0,0),
            this.expressionsPlugin = null
        }
        adjustSegment(t, e) {
            this.playCount = 0,
            t[1] < t[0] ? (this.frameModifier > 0 && (this.playSpeed < 0 ? this.setSpeed(-this.playSpeed) : this.setDirection(-1)),
            this.totalFrames = t[0] - t[1],
            this.timeCompleted = this.totalFrames,
            this.firstFrame = t[1],
            this.setCurrentRawFrameValue(this.totalFrames - .001 - e)) : t[1] > t[0] && (this.frameModifier < 0 && (this.playSpeed < 0 ? this.setSpeed(-this.playSpeed) : this.setDirection(1)),
            this.totalFrames = t[1] - t[0],
            this.timeCompleted = this.totalFrames,
            this.firstFrame = t[0],
            this.setCurrentRawFrameValue(.001 + e)),
            this.trigger("segmentStart")
        }
        advanceTime(t) {
            if (this.isPaused || !this.isLoaded)
                return;
            let e = this.currentRawFrame + t * this.frameModifier
              , s = !1;
            e >= this.totalFrames - 1 && this.frameModifier > 0 ? this.loop && this.playCount !== this.loop ? e >= this.totalFrames ? (this.playCount++,
            this.checkSegments(e % this.totalFrames) || (this.setCurrentRawFrameValue(e % this.totalFrames),
            this._completedLoop = !0,
            this.trigger("loopComplete"))) : this.setCurrentRawFrameValue(e) : this.checkSegments(e > this.totalFrames ? e % this.totalFrames : 0) || (s = !0,
            e = this.totalFrames - 1) : e < 0 ? this.checkSegments(e % this.totalFrames) || (this.loop && !(this.playCount-- <= 0 && !0 !== this.loop) ? (this.setCurrentRawFrameValue(this.totalFrames + e % this.totalFrames),
            this._completedLoop ? this.trigger("loopComplete") : this._completedLoop = !0) : (s = !0,
            e = 0)) : this.setCurrentRawFrameValue(e),
            s && (this.setCurrentRawFrameValue(e),
            this.pause(),
            this.trigger("complete"))
        }
        checkLoaded() {
            !this.isLoaded && this.renderer.globalData?.fontManager?.isLoaded && (this.imagePreloader?.loadedImages() || this.renderer.rendererType !== N.Canvas) && this.imagePreloader?.loadedFootages() && (this.isLoaded = !0,
            this.renderer.initItems(),
            setTimeout( () => {
                this.trigger("DOMLoaded")
            }
            , 0),
            this.gotoFrame(),
            this.autoplay && this.play())
        }
        checkSegments(t) {
            return this.segments.length > 0 && (this.adjustSegment(this.segments.shift() ?? [0, 0], t),
            !0)
        }
        configAnimation(t) {
            try {
                this.animationData = t,
                this.initialSegment ? (this.totalFrames = Math.floor(this.initialSegment[1] - this.initialSegment[0]),
                this.firstFrame = Math.round(this.initialSegment[0])) : (this.totalFrames = Math.floor((this.animationData.op || 1) - (this.animationData.ip || 0)),
                this.firstFrame = Math.round(this.animationData.ip || 0)),
                this.renderer.configAnimation(t),
                this.assets = this.animationData.assets,
                this.frameRate = this.animationData.fr,
                void 0 !== this.animationData.fr && (this.frameMult = this.animationData.fr / 1e3),
                this.renderer.searchExtraCompositions(t.assets),
                this.markers = function(t) {
                    let e = []
                      , {length: s} = t;
                    for (let i = 0; i < s; i++) {
                        if ("duration"in (t[i] ?? {})) {
                            e.push(t[i]);
                            continue
                        }
                        let s = {
                            duration: t[i].dr,
                            time: t[i].tm
                        };
                        try {
                            s.payload = JSON.parse(t[i].cm)
                        } catch (e) {
                            try {
                                s.payload = tD(t[i].cm)
                            } catch (e) {
                                s.payload = {
                                    name: t[i].cm
                                }
                            }
                        }
                        e.push(s)
                    }
                    return e
                }(t.markers ?? []),
                this.trigger("config_ready"),
                this.preloadImages(),
                this.loadSegments(),
                this.updaFrameModifier(),
                this.waitForFontsLoaded(),
                this.isPaused && this.audioController.pause()
            } catch (t) {
                this.triggerConfigError(t)
            }
        }
        destroy(t) {
            t && this.name !== t || (this.renderer.destroy(),
            this.imagePreloader?.destroy(),
            this.trigger("destroy"),
            this._cbs = {},
            this.onEnterFrame = null,
            this.onLoopComplete = null,
            this.onComplete = null,
            this.onSegmentStart = null,
            this.onDestroy = null,
            this.renderer = null,
            this.expressionsPlugin = null,
            this.imagePreloader = null,
            this.projectInterface = null)
        }
        getAssetData(t) {
            let e = 0
              , {length: s} = this.assets;
            for (; e < s; ) {
                if (t === this.assets[e]?.id)
                    return this.assets[e];
                e++
            }
            return null
        }
        getAssetsPath(t) {
            let e;
            if (!t)
                return "";
            if (t.e)
                e = t.p || "";
            else if (this.assetsPath) {
                let s = t.p;
                s?.indexOf("images/") !== -1 && (s = s?.split("/")[1]),
                e = this.assetsPath + (s || "")
            } else
                e = this.path + (t.u ?? "") + (t.p ?? "");
            return e
        }
        getDuration(t) {
            return t ? this.totalFrames : this.totalFrames / this.frameRate
        }
        getMarkerData(t) {
            for (let e = 0; e < this.markers.length; e++)
                if (this.markers[e]?.payload?.name === t)
                    return this.markers[e];
            return null
        }
        getPath() {
            return this.path
        }
        getVolume() {
            return this.audioController.getVolume()
        }
        goToAndPlay(t, e, s) {
            if (!s || this.name === s) {
                if (isNaN(t)) {
                    let e = this.getMarkerData(t);
                    e && (e.duration ? this.playSegments([e.time, e.time + e.duration], !0) : this.goToAndStop(e.time, !0))
                } else
                    this.goToAndStop(t, e, s);
                this.play()
            }
        }
        goToAndStop(t, e, s) {
            if (!s || this.name === s) {
                if (isNaN(t)) {
                    let e = this.getMarkerData(t);
                    e && this.goToAndStop(e.time, !0)
                } else
                    e ? this.setCurrentRawFrameValue(t) : this.setCurrentRawFrameValue(t * this.frameModifier);
                this.pause()
            }
        }
        gotoFrame() {
            this.currentFrame = this.isSubframeEnabled ? this.currentRawFrame : ~~this.currentRawFrame,
            this.timeCompleted !== this.totalFrames && this.currentFrame > this.timeCompleted && (this.currentFrame = this.timeCompleted),
            this.trigger("enterFrame"),
            this.renderFrame(),
            this.trigger("drawnFrame")
        }
        hide() {
            this.renderer.hide()
        }
        imagesLoaded() {
            this.trigger("loaded_images"),
            this.checkLoaded()
        }
        includeLayers(t) {
            this.animationData.op && t.op > this.animationData.op && (this.animationData.op = t.op,
            this.totalFrames = Math.floor(t.op - (this.animationData.ip || 0)));
            let {assets: e, layers: s} = this.animationData, i, {length: r} = s, a = t.layers, {length: n} = a;
            for (let t = 0; t < n; t++)
                for (i = 0; i < r; ) {
                    if (s[i]?.id === a[t]?.id) {
                        s[i] = a[t];
                        break
                    }
                    i++
                }
            for ((t.chars || t.fonts) && (this.renderer.globalData?.fontManager?.addChars(t.chars),
            this.renderer.globalData?.fontManager?.addFonts(t.fonts, this.renderer.globalData.defs)),
            r = t.assets.length,
            i = 0; i < r; i++)
                e.push(t.assets[i]);
            this.animationData.__complete = !1,
            tx(this.animationData, this.onSegmentComplete)
        }
        loadNextSegment() {
            let {segments: t} = this.animationData;
            if (!t || 0 === t.length || !this.autoloadSegments) {
                this.trigger("data_ready"),
                this.timeCompleted = this.totalFrames;
                return
            }
            let e = t.shift();
            this.timeCompleted = Number(e?.time) * this.frameRate;
            let s = `${this.path + (this.fileName || "")}_${this.segmentPos}.json`;
            this.segmentPos++,
            tM(s, this.includeLayers.bind(this), () => {
                this.trigger("data_failed")
            }
            )
        }
        loadSegments() {
            let {segments: t} = this.animationData;
            t || (this.timeCompleted = this.totalFrames),
            this.loadNextSegment()
        }
        mute(t) {
            t && this.name !== t || this.audioController.mute()
        }
        onSegmentComplete(t) {
            this.animationData = t,
            this.loadNextSegment()
        }
        onSetupError() {
            this.trigger("data_failed")
        }
        pause(t) {
            (!t || this.name === t) && (this.isPaused || (this.isPaused = !0,
            this.trigger("_pause"),
            this._idle = !0,
            this.trigger("_idle"),
            this.audioController.pause()))
        }
        play(t) {
            (!t || this.name === t) && this.isPaused && (this.isPaused = !1,
            this.trigger("_play"),
            this.audioController.resume(),
            this._idle && (this._idle = !1,
            this.trigger("_active")))
        }
        playSegments(t, e) {
            if (e && (this.segments.length = 0),
            U(t[0])) {
                let {length: e} = t;
                for (let s = 0; s < e; s++)
                    this.segments.push(t[s])
            } else
                this.segments.push(t);
            this.segments.length > 0 && e && this.adjustSegment(this.segments.shift() ?? [0, 0], 0),
            this.isPaused && this.play()
        }
        preloadImages() {
            this.imagePreloader && (this.imagePreloader.setAssetsPath(this.assetsPath),
            this.imagePreloader.setPath(this.path),
            this.imagePreloader.loadAssets(this.animationData.assets, this.imagesLoaded.bind(this)))
        }
        renderFrame(t) {
            if (this.isLoaded)
                try {
                    this.expressionsPlugin?.resetFrame(),
                    this.renderer.renderFrame(this.currentFrame + this.firstFrame)
                } catch (t) {
                    this.triggerRenderFrameError(t)
                }
        }
        resetSegments(t) {
            this.segments.length = 0,
            this.segments.push([this.animationData.ip, this.animationData.op]),
            t && this.checkSegments(0)
        }
        resize(t, e) {
            this.renderer.updateContainerSize("number" == typeof t ? t : void 0, "number" == typeof e ? e : void 0)
        }
        setCurrentRawFrameValue(t) {
            this.currentRawFrame = t,
            this.gotoFrame()
        }
        setData(t, e) {
            try {
                let s = e;
                s && "object" != typeof s && (s = JSON.parse(s));
                let i = {
                    animationData: s,
                    wrapper: t
                }
                  , r = t.attributes;
                i.path = r.getNamedItem("data-animation-path")?.value ?? r.getNamedItem("data-bm-path")?.value ?? r.getNamedItem("bm-path")?.value ?? "";
                let a = r.getNamedItem("data-anim-type")?.value ?? r.getNamedItem("data-bm-type")?.value ?? r.getNamedItem("bm-type")?.value ?? r.getNamedItem("data-bm-renderer")?.value ?? r.getNamedItem("bm-renderer")?.value ?? ( () => {
                    if (q.canvas)
                        return N.Canvas;
                    let t = Object.keys(q)
                      , {length: e} = t;
                    for (let s = 0; s < e; s++)
                        if (q[t[s]])
                            return t[s];
                    return N.SVG
                }
                )();
                Object.values(N).includes(a) ? i.animType = a : i.animType = N.Canvas;
                let n = r.getNamedItem("data-anim-loop")?.value ?? r.getNamedItem("data-bm-loop")?.value ?? r.getNamedItem("bm-loop")?.value ?? "";
                "false" === n ? i.loop = !1 : "true" === n ? i.loop = !0 : "" !== n && (i.loop = parseInt(n, 10));
                let o = r.getNamedItem("data-anim-autoplay")?.value ?? r.getNamedItem("data-bm-autoplay")?.value ?? r.getNamedItem("bm-autoplay")?.value ?? !0;
                i.autoplay = "false" !== o,
                i.name = r.getNamedItem("data-name")?.value ?? r.getNamedItem("data-bm-name")?.value ?? r.getNamedItem("bm-name")?.value ?? "";
                let h = r.getNamedItem("data-anim-prerender")?.value ?? r.getNamedItem("data-bm-prerender")?.value ?? r.getNamedItem("bm-prerender")?.value ?? "";
                "false" === h && (i.prerender = !1),
                i.path ? this.setParams(i) : this.trigger("destroy")
            } catch (t) {
                throw console.error(`${this.constructor.name}:
`, t),
                Error(`${this.constructor.name}: Could not set data`)
            }
        }
        setDirection(t, e) {
            e && this.name !== e || (this.playDirection = t < 0 ? -1 : 1,
            this.updaFrameModifier())
        }
        setLoop(t) {
            this.loop = t
        }
        setParams(t) {
            try {
                (t.wrapper || t.container) && (this.wrapper = t.wrapper ?? t.container ?? null);
                let s = N.SVG;
                t.animType ? s = t.animType : t.renderer && (s = t.renderer);
                let i = (t => {
                    if (!q[t])
                        throw Error("Could not get renderer");
                    return q[t]
                }
                )(s);
                this.renderer = new i(this,t.rendererSettings),
                this.imagePreloader?.setCacheType(s, this.renderer.globalData?.defs),
                this.renderer.setProjectInterface(this.projectInterface),
                this.animType = s,
                "" === t.loop || null === t.loop || void 0 === t.loop || !0 === t.loop ? this.loop = !0 : !1 === t.loop ? this.loop = !1 : this.loop = parseInt(`${t.loop}`, 10),
                this.autoplay = !!(!("autoplay"in t) || t.autoplay),
                this.name = t.name ?? "",
                this.autoloadSegments = !!(!Object.hasOwn(t, "autoloadSegments") || t.autoloadSegments),
                this.assetsPath = t.assetsPath ?? this.assetsPath,
                this.initialSegment = t.initialSegment,
                t.audioFactory && this.audioController.setAudioFactory(t.audioFactory),
                t.animationData ? this.setupAnimation(t.animationData) : t.path && (t.path.includes("\\") ? this.path = t.path.slice(0, Math.max(0, t.path.lastIndexOf("\\") + 1)) : this.path = t.path.slice(0, Math.max(0, t.path.lastIndexOf("/") + 1)),
                this.fileName = t.path.slice(Math.max(0, t.path.lastIndexOf("/") + 1)),
                this.fileName = this.fileName.slice(0, Math.max(0, this.fileName.lastIndexOf(".json"))),
                function(t, s, i) {
                    tE();
                    let r = tk(s, i);
                    e?.postMessage({
                        fullPath: Q ? t : window.location.origin + window.location.pathname,
                        id: r,
                        path: t,
                        type: "loadAnimation"
                    })
                }(t.path, this.configAnimation, this.onSetupError))
            } catch (t) {
                throw console.error(`${this.constructor.name}:
`, t),
                Error(`${this.constructor.name}: Could not set params`)
            }
        }
        setSegment(t, e) {
            let s = -1;
            this.isPaused && (this.currentRawFrame + this.firstFrame < t ? s = t : this.currentRawFrame + this.firstFrame > e && (s = e - t)),
            this.firstFrame = t,
            this.totalFrames = e - t,
            this.timeCompleted = this.totalFrames,
            -1 !== s && this.goToAndStop(s, !0)
        }
        setSpeed(t, e) {
            e && this.name !== e || (this.playSpeed = t,
            this.updaFrameModifier())
        }
        setSubframe(t=!1) {
            this.isSubframeEnabled = t
        }
        setupAnimation(t) {
            tx(t, this.configAnimation)
        }
        setVolume(t, e) {
            e && this.name !== e || this.audioController.setVolume(t)
        }
        show() {
            this.renderer.show()
        }
        stop(t) {
            t && this.name !== t || (this.pause(),
            this.playCount = 0,
            this._completedLoop = !1,
            this.setCurrentRawFrameValue(0))
        }
        togglePause(t) {
            t && this.name !== t || (this.isPaused ? this.play() : this.pause())
        }
        trigger(t) {
            try {
                if (!this._cbs[t])
                    return;
                switch (t) {
                case "enterFrame":
                    this.triggerEvent(t, new M(t,this.currentFrame,this.totalFrames,this.frameModifier)),
                    this.onEnterFrame?.(new M(t,this.currentFrame,this.totalFrames,this.frameMult));
                    break;
                case "drawnFrame":
                    this.triggerEvent(t, new P(t,this.currentFrame,this.frameModifier,this.totalFrames));
                    break;
                case "loopComplete":
                    this.triggerEvent(t, new C(t,Number(this.loop),this.playCount,this.frameMult)),
                    this.onLoopComplete?.(new C(t,this.loop,this.playCount,this.frameMult));
                    break;
                case "complete":
                    this.triggerEvent(t, new x(t,this.frameMult)),
                    this.onComplete?.(new x(t,this.frameMult));
                    break;
                case "segmentStart":
                    this.triggerEvent(t, new A(t,this.firstFrame,this.totalFrames)),
                    this.onSegmentStart?.(new A(t,this.firstFrame,this.totalFrames));
                    break;
                case "destroy":
                    this.triggerEvent(t, new T(t,this)),
                    this.onDestroy?.(new T(t,this));
                    break;
                default:
                    this.triggerEvent(t)
                }
            } catch (t) {
                console.error(`${this.constructor.name}:
`, t)
            }
        }
        triggerConfigError(t) {
            let e = new I(t,this.currentFrame);
            this.triggerEvent("error", e),
            this.onError?.(e)
        }
        triggerRenderFrameError(t) {
            let e = new D(t,this.currentFrame);
            this.triggerEvent("error", e),
            this.onError?.(e)
        }
        unmute(t) {
            t && this.name !== t || this.audioController.unmute()
        }
        updaFrameModifier() {
            this.frameModifier = this.frameMult * this.playSpeed * this.playDirection,
            this.audioController.setRate(this.playSpeed * this.playDirection)
        }
        updateDocumentData(t, e, s) {
            try {
                let i = this.renderer.getElementByPath(t);
                i?.updateDocumentData([], e, s)
            } catch (t) {
                console.error(this.constructor.name, t)
            }
        }
        waitForFontsLoaded() {
            if (this.renderer.globalData?.fontManager?.isLoaded)
                return void this.checkLoaded();
            setTimeout(this.waitForFontsLoaded.bind(this), 20)
        }
    }
    let tL = !0
      , tF = 0
      , t$ = 0
      , tN = 0
      , tO = [];
    function tV() {
        ++tN && tL && !Q && (window.requestAnimationFrame(tz),
        tL = !1)
    }
    function tz(t) {
        tF = t,
        Q || window.requestAnimationFrame(tB)
    }
    function tR({target: t}) {
        let e = 0;
        if (!t)
            throw Error("No animation to remove");
        for (; e < t$; )
            tO[e]?.animation === t && (tO.splice(e, 1),
            e--,
            t$ -= 1,
            t.isPaused || tj()),
            e++
    }
    function tB(t) {
        let e = t - tF;
        for (let t = 0; t < t$; t++)
            tO[t]?.animation.advanceTime(e);
        tF = t,
        tN && 1 ? Q || window.requestAnimationFrame(tB) : tL = !0
    }
    function tq(t, e) {
        t.addEventListener("destroy", tR),
        t.addEventListener("_active", tV),
        t.addEventListener("_idle", tj),
        tO.push({
            animation: t,
            elem: e
        }),
        t$++
    }
    function tj() {
        tN--
    }
    let tH = ( () => {
        function t(t, e) {
            let s, i = [];
            switch (t) {
            case F.Int16:
            case F.Uint8c:
                s = 1;
                break;
            case F.Float32:
            case F.Int32:
            case F.Uint8:
            default:
                s = 1.1
            }
            for (let t = 0; t < e; t++)
                i.push(s);
            return i
        }
        return "function" == typeof Uint8ClampedArray && "function" == typeof Float32Array ? function(e, s) {
            return e === F.Float32 ? new Float32Array(s) : e === F.Int16 ? new Int16Array(s) : e === F.Uint8c ? new Uint8ClampedArray(s) : t(e, s)
        }
        : t
    }
    )();
    function tG(t) {
        return Array.from({
            length: t
        })
    }
    class tW {
        constructor(t, e, s) {
            this._length = 0,
            this._maxLength = t,
            this._create = e,
            this._release = s,
            this.pool = tG(this._maxLength),
            this.newElement = this.newElement.bind(this),
            this.release = this.release.bind(this)
        }
        newElement() {
            let t;
            return this._length ? (this._length -= 1,
            t = this.pool[this._length]) : t = this._create(),
            t
        }
        release(t) {
            if (this._length === this._maxLength) {
                var e;
                this.pool = [...e = this.pool, ...tG(e.length)],
                this._maxLength *= 2
            }
            this._release && this._release(t),
            this.pool[this._length] = t,
            this._length++
        }
    }
    let tU = new tW(8, () => ({
        addedLength: 0,
        lengths: tH(F.Float32, 150),
        percents: tH(F.Float32, 150)
    }))
      , tY = new tW(8, () => ({
        lengths: [],
        totalLength: 0
    }),t => {
        if (!X(t))
            return;
        let {length: e} = t.lengths;
        for (let s = 0; s < e; s++)
            tU.release(t.lengths[s]);
        t.lengths.length = 0
    }
    )
      , tX = tH(F.Float32, 8);
    function tJ(t, e, s, i) {
        let r = {}
          , a = `${t[0]}_${t[1]}_${e[0]}_${e[1]}_${s[0]}_${s[1]}_${i[0]}_${i[1]}`.replaceAll(".", "p");
        if (!r[a]) {
            let n = 150, o, h, l = 0, p, m, d = null;
            2 === t.length && (t[0] !== e[0] || t[1] !== e[1]) && tK(t[0], t[1], e[0], e[1], t[0] + s[0], t[1] + s[1]) && tK(t[0], t[1], e[0], e[1], e[0] + i[0], e[1] + i[1]) && (n = 2);
            let c = new t2(n)
              , {length: u} = s;
            for (let r = 0; r < n; r++) {
                m = tG(u),
                h = r / (n - 1),
                p = 0;
                for (let r = 0; r < u; r++)
                    o = Math.pow(1 - h, 3) * (t[r] ?? 0) + 3 * Math.pow(1 - h, 2) * h * ((t[r] ?? 0) + (s[r] ?? 0)) + 3 * (1 - h) * Math.pow(h, 2) * ((e[r] ?? 0) + (i[r] ?? 0)) + Math.pow(h, 3) * (e[r] ?? 0),
                    m[r] = o,
                    null !== d && (p += Math.pow(Number(m[r]) - Number(d[r]), 2));
                l += p = Math.sqrt(p),
                c.points[r] = new t3(p,m),
                d = m
            }
            c.segmentLength = l,
            r[a] = c
        }
        return r[a]
    }
    function tZ(t, e, s, i, r, a, n) {
        let o = r
          , h = a;
        o < 0 ? o = 0 : o > 1 && (o = 1);
        let l = t1(o, n) ?? 0
          , p = t1(h = h > 1 ? 1 : h, n) ?? 0
          , m = 1 - l
          , d = 1 - p
          , c = m * m * m
          , u = l * m * m * 3
          , f = l * l * m * 3
          , g = l * l * l
          , y = m * m * d
          , b = l * m * d + m * l * d + m * m * p
          , v = l * l * d + m * l * p + l * m * p
          , _ = l * l * p
          , w = m * d * d
          , S = l * d * d + m * p * d + m * d * p
          , E = l * p * d + m * p * p + l * d * p
          , k = l * p * p
          , M = d * d * d
          , x = p * d * d + d * p * d + d * d * p
          , P = p * p * d + d * p * p + p * d * p
          , C = p * p * p
          , {length: A} = t;
        for (let r = 0; r < A; r++)
            tX[4 * r] = Math.round((c * (t[r] ?? 0) + u * (s[r] ?? 0) + f * (i[r] ?? 0) + g * (e[r] ?? 0)) * 1e3) / 1e3,
            tX[4 * r + 1] = Math.round((y * (t[r] ?? 0) + b * (s[r] ?? 0) + v * (i[r] ?? 0) + _ * (e[r] ?? 0)) * 1e3) / 1e3,
            tX[4 * r + 2] = Math.round((w * (t[r] ?? 0) + S * (s[r] ?? 0) + E * (i[r] ?? 0) + k * (e[r] ?? 0)) * 1e3) / 1e3,
            tX[4 * r + 3] = Math.round((M * (t[r] ?? 0) + x * (s[r] ?? 0) + P * (i[r] ?? 0) + C * (e[r] ?? 0)) * 1e3) / 1e3;
        return tX
    }
    function tK(t, e, s, i, r, a) {
        let n = t * i + e * r + s * a - r * i - a * t - s * e;
        return n > -.001 && n < .001
    }
    function tQ(t, e, s, i, r, a, n, o, h) {
        let l;
        if (0 === s && 0 === a && 0 === h)
            return tK(t, e, i, r, n, o);
        let p = Math.sqrt(Math.pow(i - t, 2) + Math.pow(r - e, 2) + Math.pow(a - s, 2))
          , m = Math.sqrt(Math.pow(n - t, 2) + Math.pow(o - e, 2) + Math.pow(h - s, 2))
          , d = Math.sqrt(Math.pow(n - i, 2) + Math.pow(o - r, 2) + Math.pow(h - a, 2));
        return (l = p > m ? p > d ? p - m - d : d - m - p : d > m ? d - m - p : m - p - d) > -1e-4 && l < 1e-4
    }
    function t0(t, e, s, i) {
        let r, a, n = 0, o, h = [], l = [], p = tU.newElement(), m = s.length;
        for (let d = 0; d < 150; d++) {
            a = d / 149,
            o = 0;
            for (let n = 0; n < m; n++)
                r = Math.pow(1 - a, 3) * (t[n] ?? 0) + 3 * Math.pow(1 - a, 2) * a * (s[n] ?? 0) + 3 * (1 - a) * Math.pow(a, 2) * (i[n] ?? 0) + Math.pow(a, 3) * (e[n] ?? 0),
                h[n] = r,
                "number" == typeof l[n] && (o += Math.pow((h[n] ?? 0) - (l[n] ?? 0), 2)),
                l[n] = h[n] ?? 0;
            o && (n += o = Math.sqrt(o)),
            p.percents[d] = a,
            p.lengths[d] = n
        }
        return p.addedLength = n,
        p
    }
    function t1(t, {addedLength: e, lengths: s, percents: i}) {
        let {length: r} = i
          , a = Math.floor((r - 1) * t)
          , n = t * e
          , o = 0;
        if (a === r - 1 || 0 === a || n === s[a])
            return i[a];
        let h = (s[a] ?? 0) > n ? -1 : 1
          , l = !0;
        for (; l; )
            if ((s[a] ?? 0) <= n && (s[a + 1] ?? 0) > n ? (o = (n - (s[a] ?? 0)) / ((s[a + 1] ?? 0) - (s[a] ?? 0)),
            l = !1) : a += h,
            a < 0 || a >= r - 1) {
                if (a === r - 1)
                    return i[a];
                l = !1
            }
        return (i[a] ?? 0) + ((i[a + 1] ?? 0) - (i[a] ?? 0)) * o
    }
    class t2 {
        constructor(t) {
            this.segmentLength = 0,
            this.points = Array.from({
                length: t
            })
        }
    }
    class t3 {
        constructor(t, e) {
            this.partialLength = t,
            this.point = e
        }
    }
    let t5 = {};
    function t4(t, e, s, i, r) {
        let a = r || `bez_${t}_${e}_${s}_${i}`.replaceAll(".", "p");
        if (t5[a])
            return t5[a];
        let n = new t6([t, e, s, i]);
        return t5[a] = n,
        n
    }
    class t6 {
        constructor(t) {
            this.float32ArraySupported = "function" == typeof Float32Array,
            this.kSplineTableSize = 11,
            this.kSampleStepSize = 1 / (this.kSplineTableSize - 1),
            this.NEWTON_ITERATIONS = 4,
            this.NEWTON_MIN_SLOPE = .001,
            this.SUBDIVISION_MAX_ITERATIONS = 10,
            this.SUBDIVISION_PRECISION = 1e-7,
            this._p = t,
            this._mSampleValues = this.float32ArraySupported ? new Float32Array(this.kSplineTableSize) : Array.from({
                length: this.kSplineTableSize
            }),
            this._precomputed = !1,
            this.get = this.get.bind(this)
        }
        _calcSampleValues() {
            let t = this._p[0]
              , e = this._p[2];
            for (let s = 0; s < this.kSplineTableSize; ++s)
                this._mSampleValues[s] = this.calcBezier(s * this.kSampleStepSize, t ?? 0, e ?? 0)
        }
        _getTForX(t) {
            let e = this._p[0]
              , s = this._p[2]
              , i = this._mSampleValues
              , r = 0
              , a = 1
              , n = this.kSplineTableSize - 1;
            for (; a !== n && Number(i[a]) <= t; ++a)
                r += this.kSampleStepSize;
            let o = r + (t - Number(i[--a])) / (Number(i[a + 1]) - Number(i[a])) * this.kSampleStepSize
              , h = this.getSlope(o, e ?? 0, s ?? 0);
            return h >= this.NEWTON_MIN_SLOPE ? this.newtonRaphsonIterate(t, o, e ?? 0, s ?? 0) : 0 === h ? o : this.binarySubdivide(t, r, r + this.kSampleStepSize, e ?? 0, s ?? 0)
        }
        _precompute() {
            let t = this._p[0]
              , e = this._p[1]
              , s = this._p[2]
              , i = this._p[3];
            this._precomputed = !0,
            (t !== e || s !== i) && this._calcSampleValues()
        }
        get(t) {
            let e = this._p[0]
              , s = this._p[1]
              , i = this._p[2]
              , r = this._p[3];
            return (this._precomputed || this._precompute(),
            e === s && i === r) ? t : 0 === t ? 0 : 1 === t ? 1 : this.calcBezier(this._getTForX(t), s ?? 0, r ?? 0)
        }
        A(t, e) {
            return 1 - 3 * e + 3 * t
        }
        B(t, e) {
            return 3 * e - 6 * t
        }
        binarySubdivide(t, e, s, i, r) {
            let a = e, n = s, o, h, l = 0;
            do
                h = a + (n - a) / 2,
                (o = this.calcBezier(h, i, r) - t) > 0 ? n = h : a = h;
            while (Math.abs(o) > this.SUBDIVISION_PRECISION && ++l < this.SUBDIVISION_MAX_ITERATIONS);
            return h
        }
        C(t) {
            return 3 * t
        }
        calcBezier(t, e, s) {
            return ((this.A(e, s) * t + this.B(e, s)) * t + this.C(e)) * t
        }
        getSlope(t, e, s) {
            return 3 * this.A(e, s) * t * t + 2 * this.B(e, s) * t + this.C(e)
        }
        newtonRaphsonIterate(t, e, s, i) {
            let r = e;
            for (let e = 0; e < this.NEWTON_ITERATIONS; ++e) {
                let e = this.getSlope(r, s, i);
                if (0 === e)
                    break;
                let a = this.calcBezier(r, s, i) - t;
                r -= a / e
            }
            return r
        }
    }
    class t8 {
        addDynamicProperty(t) {
            this.dynamicProperties.includes(t) || (this.dynamicProperties.push(t),
            this.container?.addDynamicProperty(this),
            this._isAnimated = !0)
        }
        getValue(t) {
            throw Error(`${this.constructor.name}: Method getValue is not implemented`)
        }
        initDynamicPropertyContainer(t) {
            this.container = t,
            this.dynamicProperties = [],
            this._mdf = !1,
            this._isAnimated = !1
        }
        iterateDynamicProperties() {
            this._mdf = !1;
            let {length: t} = this.dynamicProperties;
            for (let e = 0; e < t; e++)
                this.dynamicProperties[e]?.getValue(),
                this.dynamicProperties[e]?._mdf && (this._mdf = !0);
            return 0
        }
        setGroupProperty(t) {}
        constructor() {
            this.dynamicProperties = []
        }
    }
    let t9 = t => {
        let e = t[0] * J
          , s = t[1] * J
          , i = t[2] * J
          , r = Math.cos(e / 2)
          , a = Math.cos(s / 2)
          , n = Math.cos(i / 2)
          , o = Math.sin(e / 2)
          , h = Math.sin(s / 2)
          , l = Math.sin(i / 2);
        return [o * h * n + r * a * l, o * a * n + r * h * l, r * h * n - o * a * l, r * a * n - o * h * l]
    }
    ;
    class t7 extends t8 {
        addEffect(t) {
            this.effectsSequence.push(t),
            this.container?.addDynamicProperty(this)
        }
        getSpeedAtTime(t) {
            throw Error(`${this.constructor.name}: Method getSpeedAtTime is not implemented`)
        }
        getValueAtCurrentTime() {
            if (this._caching = this._caching ?? {},
            !this.keyframes)
                return;
            let {_caching: t, comp: e, keyframes: s, offsetTime: i} = this
              , r = Number(e?.renderedFrame) - i
              , a = (s[0]?.t ?? 0) - i
              , n = s.length - 1
              , o = (s[n]?.t ?? 0) - i
              , {lastFrame: h} = t;
            return r === h || -999999 !== h && (h >= o && r >= o || h < a && r < a) || (h >= r && (t._lastKeyframeIndex = -1,
            t.lastIndex = 0),
            this.pv = this.interpolateValue(r, t)),
            t.lastFrame = r,
            this.pv
        }
        getValueAtTime(t, e) {
            throw Error(`${this.constructor.name}: Method getValueAtTime is not implemented`)
        }
        getVelocityAtTime(t) {
            throw Error(`${this.constructor.name}: Method getVelocityAtTime is not implemented`)
        }
        initiateExpression(t, e, s) {
            throw Error("Method not implemented")
        }
        interpolateValue(t, e={}) {
            let s, {offsetTime: i, pv: r} = this, a = [0, 0, 0];
            this.propType === z.MultiDimensional && r && (a = tH(F.Float32, r.length));
            let {keyframes: n=[], keyframesMetadata: o, propType: h} = this
              , l = e.lastIndex || 0
              , p = l
              , m = n.length - 1
              , d = !0
              , c = n[0] ?? {}
              , u = n[1] ?? {};
            for (; d; ) {
                if (c = n[p] ?? {},
                u = n[p + 1] ?? {},
                p === m - 1 && t >= u.t - i) {
                    c.h && (c = u),
                    l = 0;
                    break
                }
                if (u.t - i > t) {
                    l = p;
                    break
                }
                p < m - 1 ? p++ : (l = 0,
                d = !1)
            }
            let f = o[p] ?? {}, g, y, b, v, _ = null, w = u.t - i, S = c.t - i;
            if (c.to && c.s) {
                f.bezierData = f.bezierData ?? tJ(c.s, u.s ?? c.e, c.to, c.ti);
                let {__fnct: s, bezierData: i} = f;
                if (t >= w || t < S) {
                    let e = t >= w ? i.points.length - 1 : 0;
                    g = i.points[e]?.point.length ?? 0;
                    for (let t = 0; t < g; t++)
                        a[t] = i.points[e]?.point[t] ?? 0
                } else {
                    s ? _ = s : f.__fnct = _ = t4(c.o.x, c.o.y, c.i.x, c.i.y, c.n).get,
                    y = _((t - S) / (w - S));
                    let r = i.segmentLength * y, n, o = e.lastFrame < t && e._lastKeyframeIndex === p ? e._lastAddedLength : 0;
                    for (v = e.lastFrame < t && e._lastKeyframeIndex === p ? e._lastPoint : 0,
                    d = !0,
                    b = i.points.length; d; ) {
                        if (o += i.points[v]?.partialLength ?? 0,
                        0 === r || 0 === y || v === i.points.length - 1) {
                            g = i.points[v]?.point.length ?? 0;
                            for (let t = 0; t < g; t++)
                                a[t] = i.points[v]?.point[t] ?? 0;
                            break
                        }
                        if (r >= o && r < o + (i.points[v + 1]?.partialLength ?? 0)) {
                            n = (r - o) / (i.points[v + 1]?.partialLength ?? 0),
                            g = i.points[v]?.point.length ?? 0;
                            for (let t = 0; t < g; t++)
                                a[t] = (i.points[v]?.point[t] ?? 0) + ((i.points[v + 1]?.point[t] ?? 0) - (i.points[v]?.point[t] ?? 0)) * n;
                            break
                        }
                        v < b - 1 ? v++ : d = !1
                    }
                    e._lastPoint = v,
                    e._lastAddedLength = o - (i.points[v]?.partialLength ?? 0),
                    e._lastKeyframeIndex = p
                }
            } else {
                let e, i, r, n;
                if (m = c.s?.length || 0,
                s = u.s ?? c.e,
                this.sh && 1 !== c.h)
                    t >= w ? (a[0] = s[0],
                    a[1] = s[1],
                    a[2] = s[2]) : t <= S && c.s ? (a[0] = c.s[0],
                    a[1] = c.s[1],
                    a[2] = c.s[2]) : ( (t, e) => {
                        let s = e[0]
                          , i = e[1]
                          , r = e[2]
                          , a = e[3]
                          , n = Math.atan2(2 * i * a - 2 * s * r, 1 - 2 * i * i - 2 * r * r)
                          , o = Math.asin(2 * s * i + 2 * r * a)
                          , h = Math.atan2(2 * s * a - 2 * i * r, 1 - 2 * s * s - 2 * r * r);
                        t[0] = n / J,
                        t[1] = o / J,
                        t[2] = h / J
                    }
                    )(a, ( (t, e, s) => {
                        let i = [0, 0, 0, 0], r = t[0], a = t[1], n = t[2], o = t[3], h = e[0], l = e[1], p = e[2], m = e[3], d, c, u, f, g;
                        return (c = r * h + a * l + n * p + o * m) < 0 && (c = -c,
                        h = -h,
                        l = -l,
                        p = -p,
                        m = -m),
                        1 - c > 1e-6 ? (u = Math.sin(d = Math.acos(c)),
                        f = Math.sin((1 - s) * d) / u,
                        g = Math.sin(s * d) / u) : (f = 1 - s,
                        g = s),
                        i[0] = f * r + g * h,
                        i[1] = f * a + g * l,
                        i[2] = f * n + g * p,
                        i[3] = f * o + g * m,
                        i
                    }
                    )(t9(c.s), t9(s), (t - S) / (w - S)));
                else
                    for (p = 0; p < m; p++)
                        1 !== c.h && (t >= w ? y = 1 : t < S ? y = 0 : (c.o.x.constructor === Array ? (f.__fnct = f.__fnct ?? [],
                        f.__fnct[p] ? _ = f.__fnct[p] : U(c.o.y) && U(c.i.y) && U(c.i.x) && (e = c.o.x[p] ?? c.o.x[0],
                        i = c.o.y[p] ?? c.o.y[0],
                        _ = t4(e ?? 0, i ?? 0, (r = c.i.x[p] ?? c.i.x[0]) ?? 0, c.i.y[p] ?? c.i.y[0] ?? 0).get,
                        f.__fnct[p] = _)) : f.__fnct ? _ = f.__fnct : (e = c.o.x,
                        i = c.o.y,
                        _ = t4(e, i, r = c.i.x, c.i.y).get,
                        c.keyframeMetadata = _),
                        y = _?.((t - S) / (w - S)))),
                        s = u.s ?? c.e,
                        void 0 !== (n = 1 === c.h ? c.s?.[p] : Number(c.s?.[p]) + ((s[p] ?? 0) - Number(c.s?.[p])) * Number(y)) && (h === z.MultiDimensional ? a[p] = n : a = n)
            }
            return e.lastIndex = l,
            a
        }
        processEffectsSequence() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (!this.elem)
                throw Error(`${this.constructor.name}: elem (ElementInterface) is not implemented`);
            if (this.elem.globalData?.frameId === this.frameId)
                return 0;
            if (0 === this.effectsSequence.length)
                return this._mdf = !1,
                0;
            if (this.lock && this.pv)
                return this.setVValue(this.pv),
                0;
            this.lock = !0,
            this._mdf = !!this._isFirstFrame;
            let t = this.effectsSequence.length
              , e = this.kf ? this.pv : this.data.k;
            for (let s = 0; s < t; s++)
                e = this.effectsSequence[s]?.(e);
            return this.setVValue(e),
            this._isFirstFrame = !1,
            this.lock = !1,
            this.frameId = this.elem.globalData?.frameId,
            0
        }
        setVValue(t) {
            let e;
            if ("number" == typeof t && this.propType === z.UniDimensional) {
                e = t * Number(this.mult),
                Math.abs(this.v - e) > 1e-5 && (this.v = e,
                this._mdf = !0);
                return
            }
            let s = 0
              , {length: i} = this.v;
            for (; s < i; )
                e = (t[s] ?? 0) * Number(this.mult),
                Math.abs((this.v[s] ?? 0) - e) > 1e-5 && (this.v[s] = e,
                this._mdf = !0),
                s++
        }
        speedAtTime(t) {
            throw Error("Method is not implemented")
        }
        valueAtTime(t, e) {
            throw Error(`${this.constructor.name}: Method valueAtTime is not implemented`)
        }
        velocityAtTime(t) {
            throw Error("Method is not implemented")
        }
        constructor(...t) {
            super(...t),
            this.effectsSequence = [],
            this.initFrame = -999999,
            this.keyframesMetadata = [],
            this.offsetTime = 0
        }
    }
    class et extends t7 {
        constructor(t, e, s=null, i=null) {
            let r, a, n, o;
            super(),
            this.propType = z.MultiDimensional;
            let {length: h} = e.k;
            for (let t = 0; t < h - 1; t++)
                e.k[t]?.to && e.k[t]?.s && e.k[t + 1]?.s && (r = e.k[t]?.s,
                a = e.k[t + 1]?.s,
                n = e.k[t]?.to,
                o = e.k[t]?.ti,
                (2 === r.length && (r[0] !== a[0] || r[1] !== a[1]) && tK(r[0] ?? 0, r[1] ?? 0, a[0] ?? 0, a[1] ?? 0, r[0] ?? 0 + (n[0] ?? 0), r[1] ?? 0 + (n[1] ?? 0)) && tK(r[0] ?? 0, r[1] ?? 0, a[0] ?? 0, a[1] ?? 0, a[0] ?? 0 + (o[0] ?? 0), a[1] ?? 0 + (o[1] ?? 0)) || 3 === r.length && (r[0] !== a[0] || r[1] !== a[1] || r[2] !== a[2]) && tQ(r[0] ?? 0, r[1] ?? 0, r[2] ?? 0, a[0] ?? 0, a[1] ?? 0, a[2] ?? 0, r[0] ?? 0 + (n[0] ?? 0), r[1] ?? 0 + (n[1] ?? 0), r[2] ?? 0 + (n[2] ?? 0)) && tQ(r[0] ?? 0, r[1] ?? 0, r[2] ?? 0, a[0] ?? 0, a[1] ?? 0, a[2] ?? 0, a[0] ?? 0 + (o[0] ?? 0), a[1] ?? 0 + (o[1] ?? 0), a[2] ?? 0 + (o[2] ?? 0))) && (e.k[t].to = null,
                e.k[t].ti = null),
                (r[0] === a[0] && r[1] === a[1] && 0 === n[0] && 0 === n[1] && 0 === o[0] && 0 === o[1] && 2 === r.length || r[2] === a[2] && 0 === n[2] && 0 === o[2]) && (e.k[t].to = null,
                e.k[t].ti = null));
            this.effectsSequence = [this.getValueAtCurrentTime.bind(this)],
            this.data = e,
            this.keyframes = e.k,
            this.keyframesMetadata = [],
            this.offsetTime = t.data.st,
            this.k = !0,
            this.kf = !0,
            this._isFirstFrame = !0,
            this.mult = s || 1,
            this.elem = t,
            this.container = i,
            this.comp = t.comp,
            this.getValue = this.processEffectsSequence,
            this.frameId = -1;
            let l = e.k[0]?.s?.length || 0;
            this.v = tH(F.Float32, l),
            this.pv = tH(F.Float32, l);
            for (let t = 0; t < l; t++)
                this.v[t] = this.initFrame,
                this.pv[t] = this.initFrame;
            this._caching = {
                lastFrame: this.initFrame,
                lastIndex: 0,
                value: tH(F.Float32, l)
            }
        }
    }
    class ee extends t7 {
        constructor(t, e, s=null, i=null) {
            super(),
            this.propType = z.UniDimensional,
            this.keyframes = e.k,
            this.keyframesMetadata = [],
            this.offsetTime = t.data.st,
            this.frameId = -1,
            this._caching = {
                _lastKeyframeIndex: -1,
                lastFrame: this.initFrame,
                lastIndex: 0,
                value: 0
            },
            this.k = !0,
            this.kf = !0,
            this.data = e,
            this.mult = s || 1,
            this.elem = t,
            this.container = i,
            this.comp = t.comp,
            this.v = this.initFrame,
            this.pv = this.initFrame,
            this._isFirstFrame = !0,
            this.getValue = this.processEffectsSequence,
            this.effectsSequence = [this.getValueAtCurrentTime.bind(this)]
        }
    }
    class es extends t7 {
        constructor(t, e, s=null, i=null) {
            super(),
            this.propType = z.MultiDimensional,
            this.mult = s || 1,
            this.data = e,
            this._mdf = !1,
            this.elem = t,
            this.container = i,
            this.comp = t.comp,
            this.k = !1,
            this.kf = !1,
            this.frameId = -1;
            let {length: r} = e.k;
            this.v = tH(F.Float32, r),
            this.pv = tH(F.Float32, r),
            this.vel = tH(F.Float32, r);
            for (let t = 0; t < r; t++)
                this.v[t] = (e.k[t] ?? 0) * this.mult,
                this.pv[t] = e.k[t] ?? 0;
            this._isFirstFrame = !0,
            this.effectsSequence = [],
            this.getValue = this.processEffectsSequence
        }
    }
    class ei extends t7 {
        constructor() {
            super(),
            this.propType = !1
        }
    }
    class er extends t7 {
        constructor(t, e, s=null, i=null) {
            super(),
            this.propType = z.UniDimensional,
            this.mult = s || 1,
            this.data = e,
            this.v = e.k * (s || 1),
            this.pv = e.k,
            this._mdf = !1,
            this.elem = t,
            this.container = i,
            this.comp = t.comp,
            this.k = !1,
            this.kf = !1,
            this.vel = 0,
            this.effectsSequence = [],
            this._isFirstFrame = !0,
            this.getValue = this.processEffectsSequence
        }
    }
    let ea = {
        getProp: function(t, e, s, i, r) {
            let a, n = e;
            if (n && "sid"in n && n.sid && (n = t.globalData?.slotManager?.getProp(n)),
            n?.k?.length)
                if ("number" == typeof (n?.k)[0])
                    a = new es(t,n,i,r);
                else
                    switch (s) {
                    case 0:
                        a = new ee(t,n,i,r);
                        break;
                    case 1:
                        a = new et(t,n,i,r)
                    }
            else
                a = new er(t,n,i,r);
            return (a = a ?? new ei).effectsSequence.length > 0 && r?.addDynamicProperty(a),
            a
        }
    };
    class en {
        constructor(t, e, s) {
            this.p = ea.getProp(e, t.v, 0, 0, s)
        }
    }
    class eo {
        constructor(t, e, s) {
            this.p = ea.getProp(e, t.v, 1, 0, s)
        }
    }
    class eh extends en {
    }
    class el extends en {
    }
    class ep extends eo {
    }
    class em extends eo {
    }
    class ed extends en {
    }
    class ec extends en {
    }
    class eu extends en {
    }
    class ef {
        constructor() {
            this.p = {}
        }
    }
    class eg extends t8 {
        constructor(t, e, s) {
            super(),
            this.effectElements = [],
            this.getValue = this.iterateDynamicProperties,
            this.init(t, e, s)
        }
        init(t, e, s) {
            let i;
            this.data = t,
            this.effectElements = [],
            this.initDynamicPropertyContainer(e);
            let r = this.data.ef
              , {length: a} = r;
            for (let t = 0; t < a; t++) {
                switch (r[t]?.ty) {
                case 0:
                    i = new eh(r[t],e,this);
                    break;
                case 1:
                    i = new el(r[t],e,this);
                    break;
                case 2:
                    i = new ep(r[t],e,this);
                    break;
                case 3:
                    i = new em(r[t],e,this);
                    break;
                case 4:
                case 7:
                    i = new eu(r[t],e,this);
                    break;
                case 10:
                    i = new ed(r[t],e,this);
                    break;
                case 11:
                    i = new ec(r[t],e,this);
                    break;
                case 5:
                    i = new ey(s,e);
                    break;
                default:
                    i = new ef
                }
                this.effectElements.push(i)
            }
        }
        renderFrame(t) {
            throw Error(`${this.constructor.name}: Method renderFrame is not implemented yet`)
        }
    }
    class ey {
        constructor(t, e, s) {
            let i = t.ef ?? [];
            this.effectElements = [];
            let {length: r} = i;
            for (let s = 0; s < r; s++) {
                let r = new eg(i[s],e,t);
                this.effectElements.push(r)
            }
        }
    }
    function eb(t=16) {
        return ({
            0: "source-over",
            1: "multiply",
            10: "difference",
            11: "exclusion",
            12: "hue",
            13: "saturation",
            14: "color",
            15: "luminosity",
            2: "screen",
            3: "overlay",
            4: "darken",
            5: "lighten",
            6: "color-dodge",
            7: "color-burn",
            8: "hard-light",
            9: "soft-light"
        })[t] || ""
    }
    class ev {
        buildAllItems() {
            throw Error(`${this.constructor.name}: Method buildAllItems is not implemented`)
        }
        checkLayers(t) {
            throw Error(`${this.constructor.name}: Method checkLayers is not implemented`)
        }
        checkMasks() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not initialized`);
            if (!this.data.hasMask)
                return !1;
            let t = 0
              , {length: e} = this.data.masksProperties ?? [];
            for (; t < e; ) {
                if (this.data.masksProperties?.[t]?.mode !== "n" && this.data.masksProperties?.[t]?.cl !== !1)
                    return !0;
                t++
            }
            return !1
        }
        destroy() {}
        destroyBaseElement() {
            throw Error(`${this.constructor.name}: Method destroyBaseElement is not implemented`)
        }
        getBaseElement() {
            throw Error(`${this.constructor.name}: Method getBaseElement is not implemented`)
        }
        getType() {
            return this.type
        }
        initBaseData(t, e, s) {
            this.globalData = e,
            this.comp = s,
            this.data = t,
            this.layerId = W(),
            this.data.sr || (this.data.sr = 1),
            this.effectsManager = new ey(this.data,this,this.dynamicProperties)
        }
        initExpressions() {
            try {
                if (!this.data)
                    throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
                !0;
                return
            } catch (t) {
                console.error(this.constructor.name, t)
            }
        }
        setBlendMode() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            let t = eb(this.data.bm)
              , e = this.baseElement ?? this.layerElement;
            if (!e)
                throw Error(`${this.constructor.name}: Both baseElement and layerElement are not implemented`);
            e.style.mixBlendMode = t
        }
        sourceRectAtTime() {
            throw Error(`${this.constructor.name}: Method sourceRectAtTime is not implemented`)
        }
        constructor() {
            this.dynamicProperties = [],
            this.frameDuration = 1,
            this.itemsData = [],
            this.layerInterface = null,
            this.shapesData = []
        }
    }
    class e_ {
        constructor() {
            this.props = tH(F.Float32, 16),
            this._identity = !0,
            this._identityCalculated = !1,
            this.reset()
        }
        applyToPoint(t, e, s) {
            return {
                x: t * (this.props[0] ?? 0) + e * (this.props[4] ?? 0) + s * (this.props[8] ?? 0) + (this.props[12] ?? 0),
                y: t * (this.props[1] ?? 0) + e * (this.props[5] ?? 0) + s * (this.props[9] ?? 0) + (this.props[13] ?? 0),
                z: t * (this.props[2] ?? 0) + e * (this.props[6] ?? 0) + s * (this.props[10] ?? 0) + (this.props[14] ?? 0)
            }
        }
        applyToPointArray(t, e, s) {
            return this.isIdentity() ? [t, e, s] : [t * (this.props[0] ?? 0) + e * (this.props[4] ?? 0) + s * (this.props[8] ?? 0) + (this.props[12] ?? 0), t * (this.props[1] ?? 0) + e * (this.props[5] ?? 0) + s * (this.props[9] ?? 0) + (this.props[13] ?? 0), t * (this.props[2] ?? 0) + e * (this.props[6] ?? 0) + s * (this.props[10] ?? 0) + (this.props[14] ?? 0)]
        }
        applyToPointStringified(t, e) {
            if (this.isIdentity())
                return `${t},${e}`;
            let s = this.props;
            return `${Math.round((t * (s[0] ?? 0) + e * (s[4] ?? 0) + (s[12] ?? 0)) * 100) / 100},${Math.round((t * (s[1] ?? 0) + e * (s[5] ?? 0) + (s[13] ?? 0)) * 100) / 100}`
        }
        applyToTriplePoints(t, e, s) {
            let i = tH(F.Float32, 6);
            if (this.isIdentity())
                i.set([t[0] ?? 0, t[1] ?? 0, e[0] ?? 0, e[1] ?? 0, s[0] ?? 0, s[1] ?? 0]);
            else {
                let r = this.props[0] ?? 0
                  , a = this.props[1] ?? 0
                  , n = this.props[4] ?? 0
                  , o = this.props[5] ?? 0
                  , h = this.props[12] ?? 0
                  , l = this.props[13] ?? 0;
                i.set([(t[0] ?? 0) * r + (t[1] ?? 0) * n + h, (t[0] ?? 0) * a + (t[1] ?? 0) * o + l, (e[0] ?? 0) * r + (e[1] ?? 0) * n + h, (e[0] ?? 0) * a + (e[1] ?? 0) * o + l, (s[0] ?? 0) * r + (s[1] ?? 0) * n + h, (s[0] ?? 0) * a + (s[1] ?? 0) * o + l])
            }
            return i
        }
        applyToX(t, e, s) {
            return t * (this.props[0] ?? 0) + e * (this.props[4] ?? 0) + s * (this.props[8] ?? 0) + (this.props[12] ?? 0)
        }
        applyToY(t, e, s) {
            return t * (this.props[1] ?? 0) + e * (this.props[5] ?? 0) + s * (this.props[9] ?? 0) + (this.props[13] ?? 0)
        }
        applyToZ(t, e, s) {
            return t * this.props[2] + e * this.props[6] + s * this.props[10] + this.props[14]
        }
        clone(t) {
            return t.props.set(this.props),
            t
        }
        cloneFromProps(t) {
            return this.props.set(t),
            this
        }
        equals(t) {
            return this.props.every( (e, s) => e === t?.props[s])
        }
        getInverseMatrix() {
            let t = (this.props[0] ?? 0) * (this.props[5] ?? 0) - (this.props[1] ?? 0) * (this.props[4] ?? 0)
              , e = this.props[5] / t
              , s = -this.props[1] / t
              , i = -this.props[4] / t
              , r = this.props[0] / t
              , a = (this.props[4] * this.props[13] - this.props[5] * this.props[12]) / t
              , n = -(this.props[0] * this.props[13] - this.props[1] * this.props[12]) / t
              , o = new e_;
            return o.setTransform(e, s, 0, 0, i, r, 0, 0, 0, 0, 1, 0, a, n, 0, 1),
            o
        }
        inversePoint(t) {
            return this.getInverseMatrix().applyToPoint(t[0] ?? 0, t[1] ?? 0, t[2] ?? 0)
        }
        inversePoints(t) {
            return t.map(t => this.inversePoint(t))
        }
        isIdentity() {
            return this._identityCalculated || (this._identity = 1 === this.props[0] && 0 === this.props[1] && 0 === this.props[2] && 0 === this.props[3] && 0 === this.props[4] && 1 === this.props[5] && 0 === this.props[6] && 0 === this.props[7] && 0 === this.props[8] && 0 === this.props[9] && 1 === this.props[10] && 0 === this.props[11] && 0 === this.props[12] && 0 === this.props[13] && 0 === this.props[14] && 1 === this.props[15],
            this._identityCalculated = !0),
            this._identity
        }
        multiply(t) {
            return this.transform(...t.props)
        }
        reset() {
            return this.props.set([1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1]),
            this
        }
        rotate(t) {
            if (!t)
                return this;
            let e = Math.cos(t)
              , s = Math.sin(t);
            return this._t(e, -s, 0, 0, s, e, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1)
        }
        rotateX(t) {
            if (!t)
                return this;
            let e = Math.cos(t)
              , s = Math.sin(t);
            return this._t(1, 0, 0, 0, 0, e, -s, 0, 0, s, e, 0, 0, 0, 0, 1)
        }
        rotateY(t) {
            if (!t)
                return this;
            let e = Math.cos(t)
              , s = Math.sin(t);
            return this._t(e, 0, s, 0, 0, 1, 0, 0, -s, 0, e, 0, 0, 0, 0, 1)
        }
        rotateZ(t) {
            return this.rotate(t)
        }
        scale(t, e, s=1) {
            return 1 === t && 1 === e && 1 === s ? this : this._t(t, 0, 0, 0, 0, e, 0, 0, 0, 0, s, 0, 0, 0, 0, 1)
        }
        setTransform(t, e, s, i, r, a, n, o, h, l, p, m, d, c, u, f) {
            return this.props.set([t, e, s, i, r, a, n, o, h, l, p, m, d, c, u, f]),
            this
        }
        shear(t, e) {
            return this._t(1, e, t, 1, 0, 0)
        }
        skew(t, e) {
            return this.shear(Math.tan(t), Math.tan(e))
        }
        skewFromAxis(t, e) {
            let s = Math.cos(e)
              , i = Math.sin(e);
            return this._t(s, i, 0, 0, -i, s, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1)._t(1, 0, 0, 0, Math.tan(t), 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1)._t(s, -i, 0, 0, i, s, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1)
        }
        to2dCSS() {
            let t = this.roundMatrixProperty(this.props[0] ?? 0)
              , e = this.roundMatrixProperty(this.props[1] ?? 0)
              , s = this.roundMatrixProperty(this.props[4] ?? 0)
              , i = this.roundMatrixProperty(this.props[5] ?? 0)
              , r = this.roundMatrixProperty(this.props[12] ?? 0)
              , a = this.roundMatrixProperty(this.props[13] ?? 0);
            return `matrix(${t},${e},${s},${i},${r},${a})`
        }
        toCSS() {
            let t = "matrix3d(";
            for (let e = 0; e < 16; e++)
                t += `${Math.round((this.props[e] ?? 0) * 1e4) / 1e4}` + (15 === e ? ")" : ",");
            return t
        }
        transform(t, e, s, i, r, a, n, o, h, l, p, m, d, c, u, f) {
            let g = this.props;
            if (1 === t && 0 === e && 0 === s && 0 === i && 0 === r && 1 === a && 0 === n && 0 === o && 0 === h && 0 === l && 1 === p && 0 === m)
                return g[12] = (g[12] ?? 0) * t + (g[15] ?? 0) * d,
                g[13] = (g[13] ?? 0) * a + (g[15] ?? 0) * c,
                g[14] = (g[14] ?? 0) * p + (g[15] ?? 0) * u,
                g[15] *= f,
                this._identityCalculated = !1,
                this;
            let y = g[0] ?? 0
              , b = g[1] ?? 0
              , v = g[2] ?? 0
              , _ = g[3] ?? 0
              , w = g[4] ?? 0
              , S = g[5] ?? 0
              , E = g[6] ?? 0
              , k = g[7] ?? 0
              , M = g[8] ?? 0
              , x = g[9] ?? 0
              , P = g[10] ?? 0
              , C = g[11] ?? 0
              , A = g[12] ?? 0
              , T = g[13] ?? 0
              , D = g[14] ?? 0
              , I = g[15] ?? 0;
            return g[0] = y * t + b * r + v * h + _ * d,
            g[1] = y * e + b * a + v * l + _ * c,
            g[2] = y * s + b * n + v * p + _ * u,
            g[3] = y * i + b * o + v * m + _ * f,
            g[4] = w * t + S * r + E * h + k * d,
            g[5] = w * e + S * a + E * l + k * c,
            g[6] = w * s + S * n + E * p + k * u,
            g[7] = w * i + S * o + E * m + k * f,
            g[8] = M * t + x * r + P * h + C * d,
            g[9] = M * e + x * a + P * l + C * c,
            g[10] = M * s + x * n + P * p + C * u,
            g[11] = M * i + x * o + P * m + C * f,
            g[12] = A * t + T * r + D * h + I * d,
            g[13] = A * e + T * a + D * l + I * c,
            g[14] = A * s + T * n + D * p + I * u,
            g[15] = A * i + T * o + D * m + I * f,
            this._identityCalculated = !1,
            this
        }
        translate(t, e, s) {
            let i = s || 0;
            return 0 !== t || 0 !== e || 0 !== i ? this._t(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, t, e, i, 1) : this
        }
        _t(...t) {
            return this.transform(...t)
        }
        roundMatrixProperty(t) {
            return t < 1e-6 && t > 0 || t > -1e-6 && t < 0 ? Math.round(1e4 * t) / 1e4 : t
        }
    }
    class ew extends t7 {
        constructor(t, e, s) {
            if (super(),
            this.defaultVector = [0, 0],
            this.elem = t,
            this.frameId = -1,
            this.propType = z.Transform,
            this.data = e,
            this.v = new e_,
            this.pre = new e_,
            this.appliedTransformations = 0,
            this.initDynamicPropertyContainer(s ?? t),
            e.p && "s"in e.p ? (this.px = ea.getProp(t, e.p.x, 0, 0, this),
            this.py = ea.getProp(t, e.p.y, 0, 0, this),
            "z"in e.p && (this.pz = ea.getProp(t, e.p.z, 0, 0, this))) : this.p = ea.getProp(t, e.p ?? {
                k: [0, 0, 0]
            }, 1, 0, this),
            "rx"in e) {
                if (this.rx = ea.getProp(t, e.rx, 0, J, this),
                this.ry = ea.getProp(t, e.ry, 0, J, this),
                this.rz = ea.getProp(t, e.rz, 0, J, this),
                e.or?.k[0]?.ti) {
                    let {length: t} = e.or.k;
                    for (let s = 0; s < t; s++) {
                        let t = e.or.k[s];
                        t && (t.to = null,
                        t.ti = null)
                    }
                }
                this.or = ea.getProp(t, e.or, 1, J, this),
                this.or.sh = !0
            } else
                this.r = ea.getProp(t, e.r ?? {
                    k: 0
                }, 0, J, this);
            e.sk && (this.sk = ea.getProp(t, e.sk, 0, J, this),
            this.sa = ea.getProp(t, e.sa, 0, J, this)),
            this.a = ea.getProp(t, e.a ?? {
                k: [0, 0, 0]
            }, 1, 0, this),
            this.s = ea.getProp(t, e.s ?? {
                k: [100, 100, 100]
            }, 1, .01, this),
            e.o ? this.o = ea.getProp(t, e.o, 0, .01, t) : this.o = {
                _mdf: !1,
                v: 1
            },
            this._isDirty = !0,
            0 === this.dynamicProperties.length && this.getValue(!0)
        }
        addDynamicProperty(t) {
            super.addDynamicProperty(t),
            this.elem.addDynamicProperty(t),
            this._isDirty = !0
        }
        applyToMatrix(t) {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (Shape) is not implemented`);
            this.iterateDynamicProperties(),
            this._mdf = !!this._mdf,
            this.a && t.translate(-this.a.v[0], -this.a.v[1], this.a.v[2]),
            this.s && t.scale(this.s.v[0], this.s.v[1], this.s.v[2]),
            this.sk && t.skewFromAxis(-this.sk.v, Number(this.sa?.v)),
            this.r ? t.rotate(-this.r.v) : t.rotateZ(-Number(this.rz?.v)).rotateY(Number(this.ry?.v)).rotateX(Number(this.rx?.v)).rotateZ(-Number(this.or?.v[2])).rotateY(Number(this.or?.v[1])).rotateX(Number(this.or?.v[0])),
            this.data.p && "s"in this.data.p ? "z"in this.data.p ? t.translate(Number(this.px?.v), Number(this.py?.v), -Number(this.pz?.v)) : t.translate(Number(this.px?.v), Number(this.py?.v), 0) : t.translate(Number(this.p?.v[0]), Number(this.p?.v[1]), -Number(this.p?.v[2]))
        }
        autoOrient() {
            throw Error(`${this.constructor.name}: Method autoOrient not implemented`)
        }
        getValue(t) {
            if (this.elem.globalData?.frameId === this.frameId)
                return 0;
            if (this._isDirty && (this.precalculateMatrix(),
            this._isDirty = !1),
            this.iterateDynamicProperties(),
            this._mdf || t) {
                if (this.v.cloneFromProps(this.pre.props),
                this.appliedTransformations < 1 && this.a && this.v.translate(-this.a.v[0], -this.a.v[1], this.a.v[2]),
                this.appliedTransformations < 2 && this.s && this.v.scale(this.s.v[0], this.s.v[1], this.s.v[2]),
                this.sk && this.appliedTransformations < 3 && this.v.skewFromAxis(-this.sk.v, Number(this.sa?.v)),
                this.r && this.appliedTransformations < 4 ? this.v.rotate(-this.r.v) : !this.r && this.appliedTransformations < 4 && this.v.rotateZ(-Number(this.rz?.v)).rotateY(Number(this.ry?.v)).rotateX(Number(this.rx?.v)).rotateZ(-Number(this.or?.v[2])).rotateY(Number(this.or?.v[1])).rotateX(Number(this.or?.v[0])),
                this.autoOriented) {
                    let t, e, {frameRate: s} = this.elem.globalData ?? {
                        frameRate: 60
                    };
                    if (this.p?.keyframes)
                        Number(this.p._caching?.lastFrame) + this.p.offsetTime <= (this.p.keyframes[0]?.t ?? 0) ? (t = this.p.getValueAtTime(((this.p.keyframes[0]?.t ?? 0) + .01) / s, 0),
                        e = this.p.getValueAtTime(Number(this.p.keyframes[0]?.t) / s, 0)) : Number(this.p._caching?.lastFrame) + this.p.offsetTime >= (this.p.keyframes[this.p.keyframes.length - 1]?.t ?? 0) ? (t = this.p.getValueAtTime((this.p.keyframes[this.p.keyframes.length - 1]?.t ?? 0) / s, 0),
                        e = this.p.getValueAtTime(((this.p.keyframes[this.p.keyframes.length - 1]?.t ?? 0) - .05) / s, 0)) : (t = this.p.pv,
                        e = this.p.getValueAtTime((Number(this.p._caching?.lastFrame) + this.p.offsetTime - .01) / s, this.p.offsetTime));
                    else if (this.px?.keyframes && this.py?.keyframes) {
                        t = [0, 0],
                        e = [0, 0];
                        let {px: i, py: r} = this
                          , {keyframes: a=[]} = i
                          , {keyframes: n=[]} = r;
                        Number(i._caching?.lastFrame) + i.offsetTime <= (a[0]?.t ?? 0) ? (t[0] = i.getValueAtTime(((a[0]?.t ?? 0) + .01) / s, 0),
                        t[1] = r.getValueAtTime(((n[0]?.t ?? 0) + .01) / s, 0),
                        e[0] = i.getValueAtTime((a[0]?.t ?? 0) / s, 0),
                        e[1] = r.getValueAtTime(Number(n[0]?.t) / s, 0)) : Number(i._caching?.lastFrame) + i.offsetTime >= (a[a.length - 1]?.t ?? 0) ? (t[0] = i.getValueAtTime((a[a.length - 1]?.t ?? 0) / s, 0),
                        t[1] = r.getValueAtTime((n[n.length - 1]?.t ?? 0) / s, 0),
                        e[0] = i.getValueAtTime(((a[a.length - 1]?.t ?? 0) - .01) / s, 0),
                        e[1] = r.getValueAtTime(((n[n.length - 1]?.t ?? 0) - .01) / s, 0)) : (t = [i.pv, r.pv],
                        e[0] = i.getValueAtTime((Number(i._caching?.lastFrame) + i.offsetTime - .01) / s, i.offsetTime),
                        e[1] = r.getValueAtTime((Number(r._caching?.lastFrame) + r.offsetTime - .01) / s, r.offsetTime))
                    } else
                        t = e = this.defaultVector;
                    this.v.rotate(-Math.atan2(t[1] - e[1], t[0] - e[0]))
                }
                this.data.p && "s"in this.data.p ? "z"in this.data.p ? this.v.translate(Number(this.px?.v), Number(this.py?.v), -Number(this.pz?.v)) : this.v.translate(Number(this.px?.v), Number(this.py?.v), 0) : this.p && this.v.translate(this.p.v[0], this.p.v[1], -this.p.v[2])
            }
            return this.frameId = this.elem.globalData?.frameId,
            0
        }
        precalculateMatrix() {
            if (this.appliedTransformations = 0,
            this.pre.reset(),
            !this.a?.effectsSequence.length) {
                if (!this.a)
                    throw Error(`${this.constructor.name}: Cannot read 'a' value`);
                if (this.pre.translate(-this.a.v[0], -this.a.v[1], this.a.v[2]),
                this.appliedTransformations = 1,
                !this.s?.effectsSequence.length) {
                    if (!this.s)
                        throw Error(`${this.constructor.name}: Cannot read 's' value`);
                    if (this.pre.scale(this.s.v[0], this.s.v[1], this.s.v[2]),
                    this.appliedTransformations = 2,
                    this.sk) {
                        if (this.sk.effectsSequence.length > 0 || this.sa?.effectsSequence.length)
                            return;
                        this.pre.skewFromAxis(-this.sk.v, Number(this.sa?.v)),
                        this.appliedTransformations = 3
                    }
                    if (this.r) {
                        0 === this.r.effectsSequence.length && (this.pre.rotate(-this.r.v),
                        this.appliedTransformations = 4);
                        return
                    }
                    this.rz?.effectsSequence.length || this.ry?.effectsSequence.length || this.rx?.effectsSequence.length || this.or?.effectsSequence.length || (this.pre.rotateZ(-Number(this.rz?.v)).rotateY(Number(this.ry?.v)).rotateX(Number(this.rx?.v)).rotateZ(-Number(this.or?.v[2])).rotateY(this.or?.v[1]).rotateX(this.or?.v[0]),
                    this.appliedTransformations = 4)
                }
            }
        }
    }
    let eS = (t, e, s) => new ew(t,e,s);
    class eE extends ev {
        globalToLocal(t) {
            let e, s = t, i = [];
            i.push(this.finalTransform);
            let r = !0
              , {comp: a} = this;
            for (; r; )
                a?.finalTransform ? (a.data?.hasMask && i.splice(0, 0, a.finalTransform),
                a = a.comp) : r = !1;
            let {length: n} = i;
            for (let t = 0; t < n; t++)
                e = i[t]?.mat.applyToPointArray(0, 0, 0),
                s = [s[0] - Number(e?.[0]), s[1] - Number(e?.[1]), 0];
            return s
        }
        initTransform() {
            if (!this.data)
                throw Error(`${this.constructor.name}: LottiePlayer is not initialized`);
            let t = new e_;
            this.finalTransform = {
                _localMatMdf: !1,
                _matMdf: !1,
                _opMdf: !1,
                localMat: t,
                localOpacity: 1,
                mat: t,
                mProp: this.data.ks ? eS(this, this.data.ks, this) : {
                    o: 0
                }
            },
            this.data.ao && (this.finalTransform.mProp.autoOriented = !0)
        }
        renderLocalTransform() {
            if (!this.localTransforms)
                return;
            if (!this.finalTransform)
                throw Error(`${this.constructor.name}: finalTransform is not initialized`);
            let t = 0
              , {length: e} = this.localTransforms;
            if (this.finalTransform._localMatMdf = this.finalTransform._matMdf,
            !this.finalTransform._localMatMdf || !this.finalTransform._opMdf)
                for (; t < e; )
                    this.localTransforms[t]?._mdf && (this.finalTransform._localMatMdf = !0),
                    this.localTransforms[t]?._opMdf && !this.finalTransform._opMdf && (this.finalTransform.localOpacity = Number(this.finalTransform.mProp.o?.v),
                    this.finalTransform._opMdf = !0),
                    t++;
            if (this.finalTransform._localMatMdf) {
                let {localMat: s, mat: i} = this.finalTransform;
                for (this.localTransforms[0]?.matrix?.clone(s),
                t = 1; t < e; t++) {
                    let e = this.localTransforms[t]?.matrix;
                    e && s.multiply(e)
                }
                s.multiply(i)
            }
            if (this.finalTransform._opMdf) {
                let s = this.finalTransform.localOpacity;
                for (t = 0; t < e; t++)
                    s *= this.localTransforms[t]?.opacity ?? .01;
                this.finalTransform.localOpacity = s
            }
        }
        renderTransform() {
            if (!this.finalTransform)
                throw Error(`${this.constructor.name}: finalTransform is not initialized`);
            if (this.finalTransform._opMdf = !!(this.finalTransform.mProp.o?._mdf || this._isFirstFrame),
            this.finalTransform._matMdf = !!(this.finalTransform.mProp._mdf || this._isFirstFrame),
            this.hierarchy) {
                let t = this.finalTransform.mat
                  , e = 0
                  , {length: s} = this.hierarchy;
                if (!this.finalTransform._matMdf)
                    for (; e < s; ) {
                        if (this.hierarchy[e]?.finalTransform?.mProp._mdf) {
                            this.finalTransform._matMdf = !0;
                            break
                        }
                        e++
                    }
                if (this.finalTransform._matMdf) {
                    let i = this.finalTransform.mProp.v.props;
                    for (t.cloneFromProps(i),
                    e = 0; e < s; e++)
                        if (this.hierarchy[e]?.finalTransform?.mProp.v) {
                            let {v: s} = this.hierarchy[e]?.finalTransform?.mProp ?? {
                                v: null
                            };
                            s && t.multiply(s)
                        }
                }
            }
            this.finalTransform._matMdf && (this.finalTransform._localMatMdf = this.finalTransform._matMdf),
            this.finalTransform._opMdf && (this.finalTransform.localOpacity = Number(this.finalTransform.mProp.o?.v))
        }
        searchEffectTransforms() {
            if (!this.renderableEffectsManager)
                return;
            let t = this.renderableEffectsManager.getEffects(V.TransformEffect);
            if (0 === t.length)
                return;
            this.localTransforms = [],
            this.finalTransform && (this.finalTransform.localMat = new e_);
            let {length: e} = t;
            for (let s = 0; s < e; s++)
                this.localTransforms.push(t[s])
        }
        constructor(...t) {
            super(...t),
            this.mHelper = new e_
        }
    }
    class ek extends eE {
        checkParenting() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (!this.comp)
                throw Error(`${this.constructor.name}: comp (ElementInterface) is not implemented`);
            void 0 !== this.data.parent && this.comp.buildElementParenting(this, this.data.parent, [])
        }
        initHierarchy() {
            this.hierarchy = [],
            this._isParent = !1,
            this.checkParenting()
        }
        setAsParent() {
            this._isParent = !0
        }
        setHierarchy(t) {
            this.hierarchy = t
        }
    }
    class eM extends ek {
        addDynamicProperty(t) {
            this.dynamicProperties.includes(t) || this.dynamicProperties.push(t)
        }
        initFrame() {
            this._isFirstFrame = !1,
            this.dynamicProperties = [],
            this._mdf = !1
        }
        prepareProperties(t, e) {
            let {length: s} = this.dynamicProperties;
            for (let t = 0; t < s; t++)
                (e || this._isParent && this.dynamicProperties[t]?.propType === z.Transform) && (this.dynamicProperties[t]?.getValue(),
                this.globalData && this.dynamicProperties[t]?._mdf && (this.globalData._mdf = !0,
                this._mdf = !0))
        }
        constructor(...t) {
            super(...t),
            this.displayStartTime = 0
        }
    }
    class ex extends eM {
        addRenderableComponent(t) {
            this.renderableComponents.includes(t) || this.renderableComponents.push(t)
        }
        checkLayerLimits(t) {
            this.data && this.globalData && (this.data.ip - this.data.st <= t && this.data.op - this.data.st > t ? !0 !== this.isInRange && (this.globalData._mdf = !0,
            this._mdf = !0,
            this.isInRange = !0,
            this.show()) : !1 !== this.isInRange && (this.globalData._mdf = !0,
            this.isInRange = !1,
            this.hide()))
        }
        checkTransparency() {
            if (!this.finalTransform)
                throw Error(`${this.constructor.name}: finalTransform is not implemented`);
            if (0 >= Number(this.finalTransform.mProp.o?.v)) {
                !this.isTransparent && (this.globalData?.renderConfig).hideOnTransparent && (this.isTransparent = !0,
                this.hide());
                return
            }
            this.isTransparent && (this.isTransparent = !1,
            this.show())
        }
        getLayerSize() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            return 5 === this.data.ty ? {
                h: Number(this.data.textData?.height),
                w: Number(this.data.textData?.width)
            } : {
                h: Number(this.data.height),
                w: Number(this.data.width)
            }
        }
        hide() {}
        initRenderable() {
            this.isInRange = !1,
            this.hidden = !1,
            this.isTransparent = !1,
            this.renderableComponents = []
        }
        prepareRenderableFrame(t, e) {
            this.checkLayerLimits(t)
        }
        removeRenderableComponent(t) {
            this.renderableComponents.includes(t) && this.renderableComponents.splice(this.renderableComponents.indexOf(t), 1)
        }
        renderRenderable() {
            let {length: t} = this.renderableComponents;
            for (let e = 0; e < t; e++)
                this.renderableComponents[e]?.renderFrame(Number(this._isFirstFrame))
        }
        show() {
            throw Error(`${this.constructor.name}: Method show is not implemented`)
        }
        sourceRectAtTime() {
            return {
                height: 100,
                left: 0,
                top: 0,
                width: 100
            }
        }
        constructor(...t) {
            super(...t),
            this.renderableComponents = []
        }
    }
    class eP extends ex {
        createContainerElements() {
            throw Error(`${this.constructor.name}: Method createContainerElements is not implemented`)
        }
        createContent() {
            throw Error(`${this.constructor.name}: Method createContent is not implemented`)
        }
        createRenderableComponents() {
            throw Error(`${this.constructor.name}: Method createRenderableComponents is not implemented`)
        }
        destroy() {
            this.innerElem = null,
            this.destroyBaseElement()
        }
        hide() {
            if (this.hidden || this.isInRange && !this.isTransparent)
                return;
            let t = this.baseElement ?? this.layerElement;
            t && (t.style.display = "none"),
            this.hidden = !0
        }
        initElement(t, e, s) {
            this.initFrame(),
            this.initBaseData(t, e, s),
            this.initTransform(),
            this.initHierarchy(),
            this.initRenderable(),
            this.initRendererElement(),
            this.createContainerElements(),
            this.createRenderableComponents(),
            this.createContent(),
            this.hide()
        }
        initRendererElement() {
            throw Error(`${this.constructor.name}: Method initRendererElement is not implemented`)
        }
        prepareFrame(t) {
            this._mdf = !1,
            this.prepareRenderableFrame(t),
            this.prepareProperties(t, this.isInRange),
            this.checkTransparency()
        }
        renderElement() {
            throw Error(`${this.constructor.name}: Method renderElement is not implemented`)
        }
        renderFrame(t) {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            this.data.hd || this.hidden || (this.renderTransform(),
            this.renderRenderable(),
            this.renderLocalTransform(),
            this.renderElement(),
            this.renderInnerContent(),
            this._isFirstFrame && (this._isFirstFrame = !1))
        }
        renderInnerContent() {
            throw Error(`${this.constructor.name}: Method renderInnerContent is not implemented`)
        }
        show() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (this.isInRange || !this.isTransparent) {
                if (!this.data.hd) {
                    let t = this.baseElement ?? this.layerElement;
                    if (!t)
                        throw Error(`${this.constructor.name}: Neither baseElement or layerElement is implemented`);
                    t.style.display = "block"
                }
                this.hidden = !1,
                this._isFirstFrame = !0
            }
        }
    }
    class eC extends eP {
        destroy() {
            this.destroyElements(),
            this.destroyBaseElement()
        }
        destroyElements() {
            let {length: t} = this.layers;
            for (let e = 0; e < t; e++)
                this.elements[e]?.destroy()
        }
        getElements() {
            return this.elements
        }
        initElement(t, e, s) {
            if (this.initFrame(),
            this.initBaseData(t, e, s),
            this.initTransform(),
            this.initRenderable(),
            this.initHierarchy(),
            this.initRendererElement(),
            this.createContainerElements(),
            this.createRenderableComponents(),
            !this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            (this.data.xt || !e.progressiveLoad) && this.buildAllItems(),
            this.hide()
        }
        prepareFrame(t) {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (this._mdf = !1,
            this.prepareRenderableFrame(t),
            this.prepareProperties(t, this.isInRange),
            !this.isInRange && !this.data.xt)
                return;
            if (this.tm?._placeholder)
                this.renderedFrame = t / Number(this.data.sr);
            else {
                let t = this.tm?.v || 0;
                t === this.data.op && (t = this.data.op - 1),
                this.renderedFrame = t
            }
            let {length: e} = this.elements;
            this.completeLayers || this.checkLayers(this.renderedFrame);
            for (let t = e - 1; t >= 0; t--)
                (this.completeLayers || this.elements[t]) && (this.elements[t]?.prepareFrame(this.renderedFrame - (this.layers[t]?.st ?? 0)),
                this.elements[t]?._mdf && (this._mdf = !0))
        }
        renderInnerContent() {
            let {length: t} = this.layers;
            for (let e = 0; e < t; e++)
                (this.completeLayers || this.elements[e]) && this.elements[e]?.renderFrame()
        }
        setElements(t) {
            this.elements = t
        }
        constructor(...t) {
            super(...t),
            this.currentFrame = 0,
            this.elements = [],
            this.layers = []
        }
    }
    let eA = () => ""
      , eT = new tW(8, () => tH(F.Float32, 2));
    class eD {
        get _type() {
            return "ShapePath"
        }
        constructor() {
            this.lengths = [],
            this.c = !1,
            this._length = 0,
            this._maxLength = 8,
            this.v = tG(this._maxLength),
            this.o = tG(this._maxLength),
            this.i = tG(this._maxLength)
        }
        doubleArrayLength() {
            this.v = [...this.v, ...tG(this._maxLength)],
            this.i = [...this.i, ...tG(this._maxLength)],
            this.o = [...this.o, ...tG(this._maxLength)],
            this._maxLength *= 2
        }
        length() {
            return this._length
        }
        reverse() {
            let t = new eD;
            t.setPathData(this.c, this._length);
            let e = this.v
              , s = this.o
              , i = this.i
              , r = 0;
            this.c && (t.setTripleAt(e[0]?.[0] ?? 0, e[0]?.[1] ?? 0, i[0]?.[0] ?? 0, i[0]?.[1] ?? 0, s[0]?.[0] ?? 0, s[0]?.[1] ?? 0, 0, !1),
            r = 1);
            let {_length: a} = this
              , n = a - 1;
            for (let o = r; o < a; o++)
                t.setTripleAt(e[n]?.[0] ?? 0, e[n]?.[1] ?? 0, i[n]?.[0] ?? 0, i[n]?.[1] ?? 0, s[n]?.[0] ?? 0, s[n]?.[1] ?? 0, o, !1),
                n -= 1;
            return t
        }
        setLength(t) {
            for (; this._maxLength < t; )
                this.doubleArrayLength();
            this._length = t
        }
        setPathData(t, e) {
            this.c = t,
            this.setLength(e);
            let s = 0;
            for (; s < e; )
                this.v[s] = eT.newElement(),
                this.o[s] = eT.newElement(),
                this.i[s] = eT.newElement(),
                s++
        }
        setTripleAt(t, e, s, i, r, a, n, o) {
            this.setXYAt(t, e, "v", n, o),
            this.setXYAt(s, i, "o", n, o),
            this.setXYAt(r, a, "i", n, o)
        }
        setXYAt(t, e, s, i, r) {
            let a;
            switch (this._length = Math.max(this._length, i + 1),
            this._length >= this._maxLength && this.doubleArrayLength(),
            s) {
            case "v":
                a = this.v;
                break;
            case "i":
                a = this.i;
                break;
            case "o":
                a = this.o;
                break;
            default:
                a = []
            }
            a[i] && r || (a[i] = eT.newElement()),
            a[i][0] = t,
            a[i][1] = e
        }
    }
    let eI = new tW(4,function() {
        return new eD
    }
    ,function(t) {
        if (!X(t))
            return;
        let e = t._length;
        for (let s = 0; s < e; s++)
            eT.release(t.v[s]),
            eT.release(t.i[s]),
            eT.release(t.o[s]),
            t.v[s] = null,
            t.i[s] = null,
            t.o[s] = null;
        t._length = 0,
        t.c = !1
    }
    )
      , {newElement: eL} = eI
      , {release: eF} = eI;
    function e$(t) {
        let e = eL()
          , s = t._length ?? t.v.length;
        e.setLength(s),
        e.c = t.c;
        for (let i = 0; i < s; i++)
            e.setTripleAt(t.v[i]?.[0] ?? 0, t.v[i]?.[1] ?? 0, t.o[i]?.[0] ?? 0, t.o[i]?.[1] ?? 0, t.i[i]?.[0] ?? 0, t.i[i]?.[1] ?? 0, i);
        return e
    }
    class eN {
        constructor() {
            this._length = 0,
            this._maxLength = 4,
            this.shapes = tG(this._maxLength)
        }
        addShape(t) {
            this._length === this._maxLength && (this.shapes = [...this.shapes, ...tG(this._maxLength)],
            this._maxLength *= 2),
            this.shapes[this._length] = t,
            this._length++
        }
        releaseShapes() {
            for (let t = 0; t < this._length; t++)
                eF(this.shapes[t]);
            this._length = 0
        }
    }
    function eO() {
        return new eN
    }
    tG(4);
    class eV extends t8 {
        getValueAtTime(t, e) {
            throw Error(`${this.constructor.name}: Method getShapeValueAtTime is not implemented`)
        }
        initiateExpression(t, e, s) {
            throw Error(`${this.constructor.name}: Method initiateExpression is not implemented`)
        }
        interpolateShape(t, e, s={}) {
            let i = s.lastIndex || 0, r, a, n, o = 0, h, l = this.keyframes ?? [];
            if (t < (l[0]?.t ?? 0) - this.offsetTime)
                r = l[0]?.s?.[0] ?? 0,
                n = !0,
                i = 0;
            else if (t >= (l[l.length - 1]?.t ?? 0) - this.offsetTime)
                r = l[l.length - 1]?.s ? l[l.length - 1]?.s?.[0] : l[l.length - 2]?.e[0],
                n = !0;
            else {
                let e = i, s = l.length - 1, h = !0, p, m;
                for (; h && (p = l[e],
                m = l[e + 1],
                !((m?.t ?? 0) - this.offsetTime > t)); )
                    e < s - 1 ? e++ : h = !1;
                if (!p || !m)
                    throw Error(`${this.constructor.name}: Could not set keyframe data`);
                let d = this.keyframesMetadata[e] ?? {};
                if (n = 1 === p.h,
                i = e,
                !n) {
                    if (t >= m.t - this.offsetTime)
                        o = 1;
                    else if (t < p.t - this.offsetTime)
                        o = 0;
                    else {
                        let e;
                        d.__fnct ? e = d.__fnct : "number" == typeof p.o.x && "number" == typeof p.o.y && "number" == typeof p.i.x && "number" == typeof p.i.y && (d.__fnct = e = t4(p.o.x, p.o.y, p.i.x, p.i.y).get),
                        o = e?.((t - (p.t - this.offsetTime)) / (m.t - this.offsetTime - (p.t - this.offsetTime))) || 0
                    }
                    a = m.s ? m.s[0] : p.e[0]
                }
                r = p.s?.[0]
            }
            if (!r || "number" == typeof r || U(r) || "number" == typeof a)
                return;
            let p = e._length
              , m = r.i[0]?.length ?? 0;
            s.lastIndex = i;
            for (let t = 0; t < p; t++)
                for (let s = 0; s < m; s++)
                    h = (n ? r.i[t]?.[s] : (r.i[t]?.[s] ?? 0) + ((a?.i[t]?.[s] ?? 0) - (r.i[t]?.[s] ?? 0)) * o) ?? 0,
                    e.i[t][s] = h,
                    h = (n ? r.o[t]?.[s] : (r.o[t]?.[s] ?? 0) + ((a?.o[t]?.[s] ?? 0) - (r.o[t]?.[s] ?? 0)) * o) ?? 0,
                    e.o[t][s] = h,
                    h = (n ? r.v[t]?.[s] : (r.v[t]?.[s] ?? 0) + ((a?.v[t]?.[s] ?? 0) - (r.v[t]?.[s] ?? 0)) * o) ?? 0,
                    e.v[t][s] = h
        }
        interpolateShapeCurrentTime() {
            if (!this.pv)
                throw Error(`${this.constructor.name}: Cannot parse ShapePath v value`);
            if (!this.keyframes)
                return;
            this._caching = this._caching ?? {};
            let t = Number(this.comp?.renderedFrame) - this.offsetTime
              , e = (this.keyframes[0]?.t ?? 0) - this.offsetTime
              , s = (this.keyframes[this.keyframes.length - 1]?.t ?? 0) - this.offsetTime
              , {lastFrame: i, lastIndex: r} = this._caching;
            return -999999 !== i && (i < e && t < e || i > s && t > s) || (this._caching.lastIndex = i < t ? r : 0,
            this.interpolateShape(t, this.pv, this._caching)),
            this._caching.lastFrame = t,
            this.pv
        }
        processEffectsSequence(t) {
            let e, s;
            if (!this.data)
                throw Error(`${this.constructor.name}: data (Shape) is not implemented`);
            if (this.elem?.globalData?.frameId === this.frameId)
                return 0;
            if (0 === this.effectsSequence.length)
                return this._mdf = !1,
                0;
            if (this.lock && this.pv)
                return this.setVValue(this.pv),
                0;
            this.lock = !0,
            this._mdf = !1,
            e = this.kf ? this.pv : this.data.ks ? this.data.ks.k : this.data.pt?.k;
            let i = this.effectsSequence.length;
            for (s = 0; s < i; s++)
                e = this.effectsSequence[s]?.(e);
            return this.setVValue(e),
            this.lock = !1,
            this.frameId = this.elem?.globalData?.frameId || 0,
            0
        }
        reset() {
            this.paths = this.localShapeCollection
        }
        setVValue(t) {
            if (!this.v || !t)
                throw Error(`${this.constructor.name}: ShapePath is not set`);
            if (!this.localShapeCollection)
                throw Error(`${this.constructor.name}: localShapeCollection is not set`);
            this.shapesEqual(this.v, t) || (this.v = e$(t),
            this.localShapeCollection.releaseShapes(),
            this.localShapeCollection.addShape(this.v),
            this._mdf = !0,
            this.paths = this.localShapeCollection)
        }
        shapesEqual(t, e) {
            if (t._length !== e._length || t.c !== e.c)
                return !1;
            let s = t._length || 0;
            for (let i = 0; i < s; i++)
                if (t.v[i]?.[0] !== e.v[i]?.[0] || t.v[i]?.[1] !== e.v[i]?.[1] || t.o[i]?.[0] !== e.o[i]?.[0] || t.o[i]?.[1] !== e.o[i]?.[1] || t.i[i]?.[0] !== e.i[i]?.[0] || t.i[i]?.[1] !== e.i[i]?.[1])
                    return !1;
            return !0
        }
        constructor(...t) {
            super(...t),
            this.effectsSequence = [],
            this.keyframesMetadata = [],
            this.offsetTime = 0
        }
    }
    class ez extends eV {
        constructor(t, e) {
            super(),
            this._cPoint = .5519,
            this.v = eL(),
            this.v.setPathData(!0, 4),
            this.localShapeCollection = eO(),
            this.paths = this.localShapeCollection,
            this.localShapeCollection.addShape(this.v),
            this.d = e.d,
            this.elem = t,
            this.comp = t.comp,
            this.frameId = -1,
            this.initDynamicPropertyContainer(t),
            this.p = ea.getProp(t, e.p, 1, 0, this),
            this.s = ea.getProp(t, e.s, 1, 0, this),
            this.dynamicProperties.length > 0 ? this.k = !0 : (this.k = !1,
            this.convertEllToPath())
        }
        convertEllToPath() {
            if (!this.p)
                return;
            let t = this.p.v[0]
              , e = this.p.v[1]
              , s = this.s.v[0] / 2
              , i = this.s.v[1] / 2
              , r = 3 !== this.d
              , a = this.v;
            if (!a?.v[0] || !a.v[1] || !a.v[2] || !a.v[3] || !a.i[0] || !a.i[1] || !a.i[2] || !a.i[3] || !a.o[0] || !a.o[1] || !a.o[2] || !a.o[3])
                throw Error(`${this.constructor.name}: Could not get value of ellipse`);
            a.v[0][0] = t,
            a.v[0][1] = e - i,
            a.v[1][0] = r ? t + s : t - s,
            a.v[1][1] = e,
            a.v[2][0] = t,
            a.v[2][1] = e + i,
            a.v[3][0] = r ? t - s : t + s,
            a.v[3][1] = e,
            a.i[0][0] = r ? t - s * this._cPoint : t + s * this._cPoint,
            a.i[0][1] = e - i,
            a.i[1][0] = r ? t + s : t - s,
            a.i[1][1] = e - i * this._cPoint,
            a.i[2][0] = r ? t + s * this._cPoint : t - s * this._cPoint,
            a.i[2][1] = e + i,
            a.i[3][0] = r ? t - s : t + s,
            a.i[3][1] = e + i * this._cPoint,
            a.o[0][0] = r ? t + s * this._cPoint : t - s * this._cPoint,
            a.o[0][1] = e - i,
            a.o[1][0] = r ? t + s : t - s,
            a.o[1][1] = e + i * this._cPoint,
            a.o[2][0] = r ? t - s * this._cPoint : t + s * this._cPoint,
            a.o[2][1] = e + i,
            a.o[3][0] = r ? t - s : t + s,
            a.o[3][1] = e - i * this._cPoint
        }
        getValue(t) {
            return this.elem?.globalData?.frameId === this.frameId || (this.frameId = this.elem?.globalData?.frameId || 0,
            this.iterateDynamicProperties(),
            this._mdf && this.convertEllToPath()),
            0
        }
    }
    class eR extends eV {
        constructor(t, e) {
            super(),
            this.v = eL(),
            this.v.c = !0,
            this.localShapeCollection = eO(),
            this.localShapeCollection.addShape(this.v),
            this.paths = this.localShapeCollection,
            this.elem = t,
            this.comp = t.comp,
            this.frameId = -1,
            this.d = e.d,
            this.initDynamicPropertyContainer(t),
            this.p = ea.getProp(t, e.p, 1, 0, this),
            this.s = ea.getProp(t, e.s, 1, 0, this),
            this.r = ea.getProp(t, e.r, 0, 0, this),
            this.dynamicProperties.length > 0 ? this.k = !0 : (this.k = !1,
            this.convertRectToPath())
        }
        convertRectToPath() {
            if (!this.p)
                throw Error(`${this.constructor.name}: p value is not implemented`);
            let t = this.p.v[0]
              , e = this.p.v[1]
              , s = this.s.v[0] / 2
              , i = this.s.v[1] / 2
              , r = Math.min(s, i, this.r.v)
              , a = .44810000000000005 * r;
            this.v && (this.v._length = 0),
            2 === this.d || 1 === this.d ? (this.v?.setTripleAt(t + s, e - i + r, t + s, e - i + r, t + s, e - i + a, 0, !0),
            this.v?.setTripleAt(t + s, e + i - r, t + s, e + i - a, t + s, e + i - r, 1, !0),
            0 === r ? (this.v?.setTripleAt(t - s, e + i, t - s + a, e + i, t - s, e + i, 2),
            this.v?.setTripleAt(t - s, e - i, t - s, e - i + a, t - s, e - i, 3)) : (this.v?.setTripleAt(t + s - r, e + i, t + s - r, e + i, t + s - a, e + i, 2, !0),
            this.v?.setTripleAt(t - s + r, e + i, t - s + a, e + i, t - s + r, e + i, 3, !0),
            this.v?.setTripleAt(t - s, e + i - r, t - s, e + i - r, t - s, e + i - a, 4, !0),
            this.v?.setTripleAt(t - s, e - i + r, t - s, e - i + a, t - s, e - i + r, 5, !0),
            this.v?.setTripleAt(t - s + r, e - i, t - s + r, e - i, t - s + a, e - i, 6, !0),
            this.v?.setTripleAt(t + s - r, e - i, t + s - a, e - i, t + s - r, e - i, 7, !0))) : (this.v?.setTripleAt(t + s, e - i + r, t + s, e - i + a, t + s, e - i + r, 0, !0),
            0 === r ? (this.v?.setTripleAt(t - s, e - i, t - s + a, e - i, t - s, e - i, 1, !0),
            this.v?.setTripleAt(t - s, e + i, t - s, e + i - a, t - s, e + i, 2, !0),
            this.v?.setTripleAt(t + s, e + i, t + s - a, e + i, t + s, e + i, 3, !0)) : (this.v?.setTripleAt(t + s - r, e - i, t + s - r, e - i, t + s - a, e - i, 1, !0),
            this.v?.setTripleAt(t - s + r, e - i, t - s + a, e - i, t - s + r, e - i, 2, !0),
            this.v?.setTripleAt(t - s, e - i + r, t - s, e - i + r, t - s, e - i + a, 3, !0),
            this.v?.setTripleAt(t - s, e + i - r, t - s, e + i - a, t - s, e + i - r, 4, !0),
            this.v?.setTripleAt(t - s + r, e + i, t - s + r, e + i, t - s + a, e + i, 5, !0),
            this.v?.setTripleAt(t + s - r, e + i, t + s - a, e + i, t + s - r, e + i, 6, !0),
            this.v?.setTripleAt(t + s, e + i - r, t + s, e + i - r, t + s, e + i - a, 7, !0)))
        }
        getValue() {
            return this.elem?.globalData?.frameId === this.frameId || (this.elem?.globalData?.frameId && (this.frameId = this.elem.globalData.frameId),
            this.iterateDynamicProperties(),
            this._mdf && this.convertRectToPath()),
            0
        }
    }
    class eB extends eV {
        constructor(t, e, s) {
            super(),
            this.propType = z.Shape,
            this.comp = t.comp,
            this.container = t,
            this.elem = t,
            this.data = e,
            this.k = !1,
            this.kf = !1,
            this._mdf = !1;
            let i = 3 === s ? e.pt?.k : e.ks?.k;
            if (!i)
                throw Error(`${this.constructor.name}: Could now get Path Data`);
            this.v = e$(i),
            this.pv = e$(this.v),
            this.localShapeCollection = eO(),
            this.paths = this.localShapeCollection,
            this.paths.addShape(this.v),
            this.effectsSequence = [],
            this.getValue = this.processEffectsSequence
        }
    }
    class eq extends eV {
        constructor(t, e, s) {
            super(),
            this.data = e,
            this.propType = z.Shape,
            this.comp = t.comp,
            this.elem = t,
            this.container = t,
            this.offsetTime = t.data?.st || 0,
            this.keyframes = 3 === s ? e.pt?.k : e.ks?.k ?? [],
            this.keyframesMetadata = [],
            this.k = !0,
            this.kf = !0;
            let i = this.keyframes[0]?.s
              , {length: r} = i?.[0]?.i ?? [];
            this.v = eL(),
            this.v.setPathData(!!i?.[0]?.c, r),
            this.pv = e$(this.v),
            this.localShapeCollection = eO(),
            this.paths = this.localShapeCollection,
            this.paths.addShape(this.v),
            this.lastFrame = -999999,
            this._caching = {
                lastFrame: -999999,
                lastIndex: 0
            },
            this.effectsSequence = [this.interpolateShapeCurrentTime.bind(this)],
            this.getValue = this.processEffectsSequence
        }
    }
    class ej extends eV {
        constructor(t, e) {
            super(),
            this.v = eL(),
            this.v.setPathData(!0, 0),
            this.elem = t,
            this.comp = t.comp,
            this.data = e,
            this.frameId = -1,
            this.d = e.d,
            this.initDynamicPropertyContainer(t),
            1 === e.sy ? (this.ir = ea.getProp(t, e.ir, 0, 0, this),
            this.is = ea.getProp(t, e.is, 0, .01, this),
            this.convertToPath = this.convertStarToPath) : this.convertToPath = this.convertPolygonToPath,
            this.pt = ea.getProp(t, e.pt, 0, 0, this),
            this.p = ea.getProp(t, e.p, 1, 0, this),
            this.r = ea.getProp(t, e.r, 0, J, this),
            this.or = ea.getProp(t, e.or, 0, 0, this),
            this.os = ea.getProp(t, e.os, 0, .01, this),
            this.localShapeCollection = eO(),
            this.localShapeCollection.addShape(this.v),
            this.paths = this.localShapeCollection,
            this.dynamicProperties.length > 0 ? this.k = !0 : (this.k = !1,
            this.convertToPath())
        }
        convertPolygonToPath() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (Shape) is not implemented`);
            if (!this.p)
                throw Error(`${this.constructor.name}: p value is not implemented`);
            let t = Math.floor(this.pt.v)
              , e = 2 * Math.PI / t
              , s = this.or.v
              , i = this.os.v
              , r = 2 * Math.PI * s / (4 * t)
              , a = -(.5 * Math.PI)
              , n = 3 === this.data.d ? -1 : 1;
            a += this.r.v,
            this.v && (this.v._length = 0);
            for (let o = 0; o < t; o++) {
                let t = s * Math.cos(a)
                  , h = s * Math.sin(a)
                  , l = 0 === t && 0 === h ? 0 : h / Math.sqrt(t * t + h * h)
                  , p = 0 === t && 0 === h ? 0 : -t / Math.sqrt(t * t + h * h);
                t += this.p.v[0],
                h += this.p.v[1],
                this.v?.setTripleAt(t, h, t - l * r * i * n, h - p * r * i * n, t + l * r * i * n, h + p * r * i * n, o, !0),
                a += e * n
            }
            this.paths.length = 0,
            this.paths[0] = this.v
        }
        convertStarToPath() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (Shape) is not implemented`);
            if (!this.v)
                throw Error(`${this.constructor.name}: v (ShapePath) is not implemented`);
            let t = 2 * Math.floor(this.pt.v), e = 2 * Math.PI / t, s = !0, i = this.or.v, r = Number(this.ir?.v), a = this.os.v, n = Number(this.is?.v), o = 2 * Math.PI * i / (2 * t), h = 2 * Math.PI * r / (2 * t), l, p, m, d = -Math.PI / 2;
            d += this.r.v;
            let c = 3 === this.data.d ? -1 : 1;
            this.v._length = 0;
            for (let u = 0; u < t; u++) {
                l = s ? i : r,
                p = s ? a : n,
                m = s ? o : h;
                let t = l * Math.cos(d)
                  , f = l * Math.sin(d)
                  , g = 0 === t && 0 === f ? 0 : f / Math.sqrt(t * t + f * f)
                  , y = 0 === t && 0 === f ? 0 : -t / Math.sqrt(t * t + f * f);
                t += Number(this.p?.v[0]),
                f += Number(this.p?.v[1]),
                this.v.setTripleAt(t, f, t - g * m * p * c, f - y * m * p * c, t + g * m * p * c, f + y * m * p * c, u, !0),
                s = !s,
                d += e * c
            }
        }
        convertToPath() {
            throw Error(`${this.constructor.name}: Method convertToPath is not implemented`)
        }
        getValue(t) {
            return this.elem?.globalData?.frameId === this.frameId || (this.frameId = this.elem?.globalData?.frameId || 0,
            this.iterateDynamicProperties(),
            this._mdf && this.convertToPath()),
            0
        }
    }
    let eH = function(t, e, s, i, r) {
        let a = null;
        switch (s) {
        case 3:
        case 4:
            {
                let i = 3 === s ? e.pt : e.ks
                  , r = i?.k;
                if (r?.length) {
                    a = new eq(t,e,s);
                    break
                }
                a = new eB(t,e,s);
                break
            }
        case 5:
            a = new eR(t,e);
            break;
        case 6:
            a = new ez(t,e);
            break;
        case 7:
            a = new ej(t,e)
        }
        return a?.k && t.addDynamicProperty(a),
        a
    };
    class eG {
        constructor(t, e, s) {
            this.masksProperties = [],
            this.data = t,
            this.element = e,
            this.globalData = s,
            this.storedData = [],
            this.masksProperties = this.data.masksProperties ?? [],
            this.maskElement = null;
            let {defs: i} = this.globalData
              , {length: r} = this.masksProperties;
            this.viewData = tG(r),
            this.solidPath = "";
            let a = this.masksProperties, n = 0, o = [], h = W(), l, p, m, d = "clipPath", c = "clip-path";
            for (let t = 0; t < r; t++) {
                let e;
                (a[t]?.mode !== "a" && a[t]?.mode !== "n" || a[t]?.inv || a[t]?.o?.k !== 100 || a[t]?.o?.x) && (d = "mask",
                c = "mask"),
                (a[t]?.mode === "s" || a[t]?.mode === "i") && 0 === n ? ((l = tA("rect")).setAttribute("fill", "#ffffff"),
                l.setAttribute("width", `${Number(this.element.comp?.data?.w)}`),
                l.setAttribute("height", `${Number(this.element.comp?.data?.h)}`),
                o.push(l)) : l = null;
                let s = tA("path");
                if (a[t]?.mode === "n") {
                    this.viewData[t] = {
                        elem: s,
                        lastPath: "",
                        op: ea.getProp(this.element, a[t]?.o, 0, .01, this.element),
                        prop: eH(this.element, a[t], 3)
                    },
                    i.appendChild(s);
                    continue
                }
                if (n++,
                s.setAttribute("fill", a[t]?.mode === "s" ? "#000000" : "#ffffff"),
                s.setAttribute("clip-rule", "nonzero"),
                a[t]?.x?.k === 0)
                    p = null,
                    m = null;
                else {
                    d = "mask",
                    c = "mask",
                    m = ea.getProp(this.element, a[t]?.x, 0, null, this.element),
                    e = W();
                    let r = tA("filter");
                    r.id = e,
                    (p = tA("feMorphology")).setAttribute("operator", "erode"),
                    p.setAttribute("in", "SourceGraphic"),
                    p.setAttribute("radius", "0"),
                    r.appendChild(p),
                    i.appendChild(r),
                    s.setAttribute("stroke", a[t]?.mode === "s" ? "#000000" : "#ffffff")
                }
                if (this.storedData[t] = {
                    elem: s,
                    expan: p,
                    filterId: e,
                    lastOperator: "",
                    lastPath: "",
                    lastRadius: 0,
                    x: m
                },
                a[t]?.mode === "i") {
                    let {length: t} = o
                      , e = tA("g");
                    for (let s = 0; s < t; s++)
                        e.appendChild(o[s]);
                    let r = tA("mask");
                    r.setAttribute("mask-type", "alpha"),
                    r.id = `${h}_${n}`,
                    r.appendChild(s),
                    i.appendChild(r),
                    e.setAttribute("mask", `url(${eA()}#${h}_${n})`),
                    o.length = 0,
                    o.push(e)
                } else
                    o.push(s);
                a[t]?.inv && !this.solidPath && (this.solidPath = this.createLayerSolidPath()),
                this.viewData[t] = {
                    elem: s,
                    invRect: l,
                    lastPath: "",
                    op: ea.getProp(this.element, a[t]?.o, 0, .01, this.element),
                    prop: eH(this.element, a[t], 3)
                };
                let r = this.viewData[t]?.prop?.v;
                !this.viewData[t]?.prop?.k && r && this.drawPath(a[t] ?? null, r, this.viewData[t])
            }
            this.maskElement = tA(d);
            let {length: u} = o;
            for (let t = 0; t < u; t++)
                this.maskElement.appendChild(o[t]);
            n > 0 && (this.maskElement.id = h,
            this.element.maskedElement?.setAttribute(c, `url(${eA()}#${h})`),
            i.appendChild(this.maskElement)),
            this.viewData.length > 0 && this.element.addRenderableComponent(this)
        }
        createLayerSolidPath() {
            let t = "M0,0 ";
            return t + ` h${this.globalData.compSize?.w || 0} v${this.globalData.compSize?.h || 0} h-${this.globalData.compSize?.w || 0} v-${this.globalData.compSize?.h || 0} `
        }
        destroy() {
            this.element = null,
            this.globalData = null,
            this.maskElement = null,
            this.data = null,
            this.masksProperties = null
        }
        drawPath(t, e, s) {
            let i, r = ` M${e.v[0]?.[0]},${e.v[0]?.[1]}`, a = e._length || 0;
            for (i = 1; i < a; i++)
                r += ` C${e.o[i - 1]?.[0]},${e.o[i - 1]?.[1]} ${e.i[i]?.[0]},${e.i[i]?.[1]} ${e.v[i]?.[0]},${e.v[i]?.[1]}`;
            if (e.c && a > 1 && (r += ` C${e.o[i - 1]?.[0]},${e.o[i - 1]?.[1]} ${e.i[0]?.[0]},${e.i[0]?.[1]} ${e.v[0]?.[0]},${e.v[0]?.[1]}`),
            s.lastPath !== r) {
                let i = "";
                s.elem && (e.c && (i = t?.inv ? this.solidPath + r : r),
                s.elem.setAttribute("d", i)),
                s.lastPath = r
            }
        }
        getMaskelement() {
            return this.maskElement
        }
        getMaskProperty(t) {
            return this.viewData[t]?.prop ?? null
        }
        renderFrame(t) {
            let e = this.element.finalTransform?.mat
              , {length: s} = this.masksProperties;
            for (let i = 0; i < s; i++) {
                let s = this.viewData[i]?.prop?.v;
                if (s && (this.viewData[i]?.prop?._mdf || t) && this.drawPath(this.masksProperties[i] ?? null, s, this.viewData[i]),
                (this.viewData[i]?.op._mdf || t) && this.viewData[i]?.elem.setAttribute("fill-opacity", `${this.viewData[i]?.op.v ?? 1}`),
                this.masksProperties[i]?.mode === "n")
                    continue;
                this.viewData[i]?.invRect && (this.element.finalTransform?.mProp._mdf || t) && this.viewData[i]?.invRect?.setAttribute("transform", `${e?.getInverseMatrix().to2dCSS()}`);
                let r = this.storedData[i];
                if (!r?.x || !(r.x._mdf || t))
                    continue;
                let a = r.expan;
                if (r.x.v < 0) {
                    "erode" !== r.lastOperator && (r.lastOperator = "erode",
                    r.elem.setAttribute("filter", `url(${eA()}#${r.filterId})`)),
                    a?.setAttribute("radius", `${-r.x.v}`);
                    continue
                }
                "dilate" !== r.lastOperator && (r.lastOperator = "dilate",
                r.elem.removeAttribute("filter")),
                r.elem.setAttribute("stroke-width", `${2 * r.x.v}`)
            }
        }
    }
    let eW = function() {
        let t = tA("feColorMatrix");
        return t.setAttribute("type", "matrix"),
        t.setAttribute("color-interpolation-filters", "sRGB"),
        t.setAttribute("values", "0 0 0 1 0  0 0 0 1 0  0 0 0 1 0  0 0 0 1 1"),
        t
    }
      , eU = function(t, e) {
        let s = tA("filter");
        return s.id = t,
        e || (s.setAttribute("filterUnits", "objectBoundingBox"),
        s.setAttribute("x", "0%"),
        s.setAttribute("y", "0%"),
        s.setAttribute("width", "100%"),
        s.setAttribute("height", "100%")),
        s
    }
      , eY = "filter_result_"
      , eX = {};
    class eJ {
        constructor(t) {
            let e, s = "SourceGraphic", i = W(), r = eU(i, !0), a = 0;
            this.filters = [];
            let {length: n} = t.data.ef ?? [];
            for (let i = 0; i < n; i++) {
                e = null;
                let {ty: n} = t.data.ef?.[i] ?? {
                    ty: null
                }
                  , o = null !== n && eX[n] ? eX[n].effect : null;
                o && n && t.effectsManager && (e = new o(r,t.effectsManager.effectElements[i],t,`${eY}${a}`,s),
                s = `${eY}${a}`,
                eX[n]?.countsAsEffect && a++),
                e && this.filters.push(e)
            }
            a && (t.globalData?.defs.appendChild(r),
            t.layerElement?.setAttribute("filter", `url(${eA()}#${i})`)),
            this.filters.length > 0 && t.addRenderableComponent(this)
        }
        getEffects(t) {
            let {length: e} = this.filters
              , s = [];
            for (let i = 0; i < e; i++)
                this.filters[i]?.type === t && s.push(this.filters[i]);
            return s
        }
        renderFrame(t) {
            let {length: e} = this.filters;
            for (let s = 0; s < e; s++)
                this.filters[s]?.renderFrame(t)
        }
    }
    let eZ = new class {
        constructor() {
            if (this.maskType = !0,
            this.offscreenCanvas = "undefined" != typeof OffscreenCanvas,
            this.svgLumaHidden = !0,
            !tt)
                return;
            this.maskType = !/MSIE 10/i.test(navigator.userAgent) && !/MSIE 9/i.test(navigator.userAgent) && !/rv:11.0/i.test(navigator.userAgent) && !/Edge\/\d./i.test(navigator.userAgent),
            this.svgLumaHidden = !/firefox/i.test(navigator.userAgent)
        }
    }
    ;
    class eK extends eP {
        createContainerElements() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (!this.globalData)
                throw Error(`${this.constructor.name}: globalData is not implemented`);
            if (!this.layerElement)
                throw Error(`${this.constructor.name}: layerElement is not implemented`);
            this.matteElement = tA("g"),
            this.transformedElement = this.layerElement,
            this.maskedElement = this.layerElement,
            this._sizeChanged = !1;
            let t = null;
            if (this.data.td) {
                this.matteMasks = {};
                let e = tA("g");
                this.layerId && (e.id = this.layerId),
                e.appendChild(this.layerElement),
                t = e,
                this.globalData.defs.appendChild(e)
            } else
                this.data.tt ? (this.matteElement.appendChild(this.layerElement),
                t = this.matteElement,
                this.baseElement = this.matteElement) : this.baseElement = this.layerElement;
            if (this.data.ln && (this.layerElement.id = this.data.ln),
            this.data.cl && this.layerElement.classList.add(this.data.cl),
            0 === this.data.ty && !this.data.hd) {
                let e = tA("clipPath")
                  , s = tA("path");
                s.setAttribute("d", `M0,0 L${this.data.w},0 L${this.data.w},${this.data.h} L0,${this.data.h}z`);
                let i = W();
                if (e.id = i,
                e.appendChild(s),
                this.globalData.defs.appendChild(e),
                this.checkMasks()) {
                    let e = tA("g");
                    e.setAttribute("clip-path", `url(${eA()}#${i})`),
                    e.appendChild(this.layerElement),
                    this.transformedElement = e,
                    t ? t.appendChild(this.transformedElement) : this.baseElement = this.transformedElement
                } else
                    this.layerElement.setAttribute("clip-path", `url(${eA()}#${i})`)
            }
            0 !== this.data.bm && this.setBlendMode()
        }
        createRenderableComponents() {
            if (!this.data)
                throw Error(`${this.constructor.name}: layerData is not initialized`);
            if (!this.globalData)
                throw Error(`${this.constructor.name}: globalData is not initialized`);
            this.maskManager = new eG(this.data,this,this.globalData),
            this.renderableEffectsManager = new eJ(this),
            this.searchEffectTransforms()
        }
        destroyBaseElement() {
            this.layerElement = null,
            this.matteElement = null,
            this.maskManager?.destroy()
        }
        getBaseElement() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            return this.data.hd ? null : this.baseElement ?? null
        }
        getMatte(t=1) {
            let e, s, i, r;
            if (this.matteMasks = this.matteMasks ?? {},
            this.matteMasks[t])
                return this.matteMasks[t];
            let a = `${this.layerId}_${t}`;
            switch (t) {
            case 1:
            case 3:
                {
                    let n = tA("mask");
                    if (n.id = a,
                    n.setAttribute("mask-type", 3 === t ? "luminance" : "alpha"),
                    (i = tA("use")).setAttributeNS(Z, "href", `#${this.layerId}`),
                    n.appendChild(i),
                    this.globalData?.defs.appendChild(n),
                    eZ.maskType || 1 !== t)
                        break;
                    n.setAttribute("mask-type", "luminance"),
                    s = eU(e = W()),
                    this.globalData?.defs.appendChild(s),
                    s.appendChild(eW()),
                    (r = tA("g")).appendChild(i),
                    n.appendChild(r),
                    r.setAttribute("filter", `url(${eA()}#${e})`)
                }
                break;
            case 2:
                {
                    let t = tA("mask");
                    t.id = a,
                    t.setAttribute("mask-type", "alpha");
                    let n = tA("g");
                    t.appendChild(n),
                    s = eU(e = W());
                    let o = tA("feComponentTransfer");
                    o.setAttribute("in", "SourceGraphic"),
                    s.appendChild(o);
                    let h = tA("feFuncA");
                    h.setAttribute("type", "table"),
                    h.setAttribute("tableValues", "1.0 0.0"),
                    o.appendChild(h),
                    this.globalData?.defs.appendChild(s);
                    let l = tA("rect");
                    l.width.baseVal.value = this.comp?.data?.w || 0,
                    l.height.baseVal.value = this.comp?.data?.h || 0,
                    l.x.baseVal.value = 0,
                    l.y.baseVal.value = 0,
                    l.setAttribute("fill", "#ffffff"),
                    l.setAttribute("opacity", "0"),
                    n.setAttribute("filter", `url(${eA()}#${e})`),
                    n.appendChild(l),
                    (i = tA("use")).setAttributeNS(Z, "href", `#${this.layerId}`),
                    n.appendChild(i),
                    eZ.maskType || (t.setAttribute("mask-type", "luminance"),
                    s.appendChild(eW()),
                    r = tA("g"),
                    n.appendChild(l),
                    this.layerElement && r.appendChild(this.layerElement),
                    n.appendChild(r)),
                    this.globalData?.defs.appendChild(t)
                }
            }
            return this.matteMasks[t] = a,
            this.matteMasks[t]
        }
        initRendererElement() {
            this.layerElement = tA("g")
        }
        renderElement() {
            if (!this.finalTransform)
                throw Error(`${this.constructor.name}: finalTransform is not implemented`);
            this.finalTransform._localMatMdf && this.transformedElement?.setAttribute("transform", this.finalTransform.localMat.to2dCSS()),
            this.finalTransform._opMdf && this.transformedElement?.setAttribute("opacity", `${this.finalTransform.localOpacity}`)
        }
        setMatte(t) {
            this.matteElement && this.matteElement.setAttribute("mask", `url(${eA()}#${t})`)
        }
    }
    class eQ extends eK {
        constructor(t, e, s) {
            super(),
            this.layers = [],
            t.refId && (this.assetData = e.getAssetData(t.refId)),
            this.assetData?.sid && (this.assetData = e.slotManager?.getProp(this.assetData) || null),
            this.initElement(t, e, s),
            this.sourceRect = {
                height: Number(this.assetData?.h),
                left: 0,
                top: 0,
                width: Number(this.assetData?.w)
            }
        }
        createContent() {
            let t = "";
            this.assetData && this.globalData?.getAssetsPath && (t = this.globalData.getAssetsPath(this.assetData)),
            this.assetData && (this.innerElem = tA("image"),
            this.innerElem.setAttribute("width", `${this.assetData.w}px`),
            this.innerElem.setAttribute("height", `${this.assetData.h}px`),
            this.innerElem.setAttribute("preserveAspectRatio", this.assetData.pr || this.globalData?.renderConfig?.imagePreserveAspectRatio || ""),
            this.innerElem.setAttributeNS(Z, "href", t),
            this.layerElement?.appendChild(this.innerElem))
        }
        renderInnerContent() {}
        sourceRectAtTime() {
            return this.sourceRect
        }
    }
    class e0 extends eM {
        constructor(t, e, s) {
            super(),
            this.initFrame(),
            this.initBaseData(t, e, s),
            this.initTransform(),
            this.initHierarchy()
        }
        getBaseElement() {
            return null
        }
        prepareFrame(t) {
            this.prepareProperties(t, !0)
        }
        renderFrame(t) {
            return null
        }
    }
    class e1 extends eQ {
        constructor(t, e, s) {
            super(t, e, s),
            this.initElement(t, e, s)
        }
        createContent() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            let t = tA("rect");
            t.width.baseVal.value = this.data.sw || 0,
            t.height.baseVal.value = this.data.sh || 0,
            this.data.sc && t.setAttribute("fill", this.data.sc),
            this.layerElement?.appendChild(t)
        }
    }
    class e2 {
        constructor() {
            this.it = [],
            this.prevViewData = [],
            this.gr = tA("g")
        }
    }
    function e3(t, e, s, i) {
        if (0 === e)
            return "";
        let r = t.o, a = t.i, n = t.v, o, h = ` M${i.applyToPointStringified(n[0]?.[0] ?? 0, n[0]?.[1] ?? 0)}`;
        for (o = 1; o < e; o++)
            h += ` C${i.applyToPointStringified(r[o - 1]?.[0] ?? 0, r[o - 1]?.[1] ?? 0)} ${i.applyToPointStringified(a[o]?.[0] ?? 0, a[o]?.[1] ?? 0)} ${i.applyToPointStringified(n[o]?.[0] ?? 0, n[o]?.[1] ?? 0)}`;
        return s && e && (h += ` C${i.applyToPointStringified(r[o - 1]?.[0] ?? 0, r[o - 1]?.[1] ?? 0)} ${i.applyToPointStringified(a[0]?.[0] ?? 0, a[0]?.[1] ?? 0)} ${i.applyToPointStringified(n[0]?.[0] ?? 0, n[0]?.[1] ?? 0)}z`),
        h
    }
    let e5 = new e_
      , e4 = new e_;
    function e6(t, e, s) {
        if (!e)
            throw Error("SVGElementsRenderer: Method renderContetTransform is missing data");
        (s || e.transform.op._mdf) && e.transform.container.setAttribute("opacity", `${e.transform.op.v}`),
        (s || e.transform.mProps._mdf) && e.transform.container.setAttribute("transform", e.transform.mProps.v.to2dCSS())
    }
    function e8(t, e, s) {
        if (!e)
            throw Error("SVGElementsRenderer: Method renderFill is missing data");
        let i = e.style;
        e.c?.v && (e.c._mdf || s) && i.pElem.setAttribute("fill", `rgb(${Math.floor(e.c.v[0])},${Math.floor(e.c.v[1])},${e.c.v[2]})`),
        (e.o?._mdf || s) && i.pElem.setAttribute("fill-opacity", `${e.o?.v}`)
    }
    function e9(t, e, s) {
        let i, r;
        if (!e)
            throw Error("SVGElementsRenderer: Method renderGradient is missing data");
        let a = e.gf
          , n = e.g?._hasOpacity
          , o = e.s?.v ?? [0, 0]
          , h = e.e?.v ?? [0, 0];
        if (e.o?._mdf || s) {
            let s = t.ty === O.GradientFill ? "fill-opacity" : "stroke-opacity";
            e.style?.pElem.setAttribute(s, `${e.o?.v}`)
        }
        if (e.s?._mdf || s) {
            let s = 1 === t.t ? "x1" : "cx"
              , i = "x1" === s ? "y1" : "cy";
            a?.setAttribute(s, `${o[0]}`),
            a?.setAttribute(i, `${o[1]}`),
            n && !e.g?._collapsable && (e.of?.setAttribute(s, `${o[0]}`),
            e.of?.setAttribute(i, `${o[1]}`))
        }
        if (e.g && (e.g._cmdf || s)) {
            i = e.cst;
            let t = e.g.c
              , {length: s} = i;
            for (let e = 0; e < s; e++)
                (r = i[e]).setAttribute("offset", `${t[4 * e]}%`),
                r.setAttribute("stop-color", `rgb(${t[4 * e + 1]},${t[4 * e + 2]},${t[4 * e + 3]})`)
        }
        if (n && e.g && (e.g._omdf || s)) {
            let t = e.g.o
              , {length: s} = i = e.g._collapsable ? e.cst : e.ost;
            for (let a = 0; a < s; a++)
                r = i[a],
                e.g._collapsable || r.setAttribute("offset", `${t[2 * a]}%`),
                r.setAttribute("stop-opacity", `${t[2 * a + 1]}`)
        }
        if (1 === t.t)
            (e.e?._mdf || s) && (a?.setAttribute("x2", `${h[0]}`),
            a?.setAttribute("y2", `${h[1]}`),
            n && !e.g?._collapsable && (e.of?.setAttribute("x2", `${h[0]}`),
            e.of?.setAttribute("y2", `${h[1]}`)));
        else {
            let t = 0;
            if ((e.s?._mdf || e.e?._mdf || s) && (t = Math.sqrt(Math.pow(o[0] - h[0], 2) + Math.pow(o[1] - h[1], 2)),
            a?.setAttribute("r", `${t}`),
            n && !e.g?._collapsable && e.of?.setAttribute("r", `${t}`)),
            e.e?._mdf || e.h?._mdf || e.a?._mdf || s) {
                t || (t = Math.sqrt(Math.pow(o[0] - h[0], 2) + Math.pow(o[1] - h[1], 2)));
                let s = Math.atan2(h[1] - o[1], h[0] - o[0])
                  , i = Number(e.h?.v);
                i >= 1 ? i = .99 : i <= -1 && (i = -.99);
                let r = t * i
                  , l = Math.cos(s + Number(e.a?.v)) * r + o[0]
                  , p = Math.sin(s + Number(e.a?.v)) * r + o[1];
                a?.setAttribute("fx", `${l}`),
                a?.setAttribute("fy", `${p}`),
                n && !e.g?._collapsable && (e.of?.setAttribute("fx", `${l}`),
                e.of?.setAttribute("fy", `${p}`))
            }
        }
    }
    function e7(t, e, s) {
        e9(t, e, s),
        ss(t, e, s)
    }
    function st(t) {}
    function se(t, e, s) {
        let i, r, a, n, o, h, l;
        if (!e)
            throw Error("SVGElementsRenderer: Method renderPath is missing data");
        let {caches: p, lvl: m, sh: d, styles: c, transformers: u} = e
          , {length: f} = c;
        for (let e = 0; e < f; e++) {
            r = d?._mdf || !!s;
            let f = c[e]
              , {lvl: g} = f ?? {
                lvl: 0
            };
            if (g < m) {
                for (o = e4.reset(),
                h = m - g,
                l = u.length - 1; !r && h > 0; )
                    r = u[l]?.mProps._mdf || r,
                    h--,
                    l--;
                if (r)
                    for (h = m - g,
                    l = u.length - 1; h > 0; )
                        o.multiply(u[l]?.mProps.v),
                        h--,
                        l--
            } else
                o = e5;
            n = d?.paths;
            let y = n?._length || 0;
            if (r) {
                i = "";
                for (let t = 0; t < y; t++)
                    a = n?.shapes[t],
                    a?._length && (i += e3(a, a._length, a.c, o));
                p[e] = i
            } else
                i = p[e];
            f && (f.d += !0 === t.hd ? "" : i ?? "",
            f._mdf = r || f._mdf)
        }
    }
    function ss(t, e, s) {
        if (!e?.style)
            throw Error("SVGElementsRenderer: Method renderStroke is missing data");
        let {c: i, d: r, o: a, style: {msElem: n, pElem: o}, w: h} = e;
        r && (r._mdf || s) && r.dashStr && (o.setAttribute("stroke-dasharray", r.dashStr),
        o.setAttribute("stroke-dashoffset", `${r.dashoffset[0]}`)),
        i && (i._mdf || s) && o.setAttribute("stroke", `rgb(${Math.floor(i.v[0])},${Math.floor(i.v[1])},${Math.floor(i.v[2])})`),
        (a?._mdf || s) && o.setAttribute("stroke-opacity", `${a?.v ?? 1}`),
        (h?._mdf || s) && (o.setAttribute("stroke-width", `${h?.v || 0}`),
        n?.setAttribute("stroke-width", `${h?.v || 0}`))
    }
    class si extends t8 {
        constructor(t, e, s) {
            super(),
            this.it = [],
            this.prevViewData = [],
            this.initDynamicPropertyContainer(t),
            this.getValue = this.iterateDynamicProperties,
            this.o = ea.getProp(t, e.o, 0, .01, this),
            this.c = ea.getProp(t, e.c, 1, 255, this),
            this.style = s
        }
    }
    class sr extends t8 {
        constructor(t, e, s) {
            super(),
            this.data = e,
            this.c = tH(F.Uint8c, 4 * e.p);
            let i = e.k.k?.[0]?.s ? (e.k.k[0]?.s.length ?? 0) - 4 * e.p : e.k.k.length - 4 * e.p;
            this.o = tH(F.Float32, i),
            this._cmdf = !1,
            this._omdf = !1,
            this._collapsable = this.checkCollapsable(),
            this._hasOpacity = i,
            this.initDynamicPropertyContainer(s),
            this.prop = ea.getProp(t, e.k, 1, null, this),
            this.k = this.prop.k,
            this.getValue(!0)
        }
        checkCollapsable() {
            if (this.o.length / 2 != this.c.length / 4)
                return !1;
            if (this.data.k.k?.[0]?.s) {
                let t = 0
                  , e = this.data.k.k.length;
                for (; t < e; ) {
                    if (!this.comparePoints(this.data.k.k[t]?.s ?? [], this.data.p))
                        return !1;
                    t++
                }
            } else if (!this.comparePoints(this.data.k.k, this.data.p))
                return !1;
            return !0
        }
        comparePoints(t, e) {
            let s = 0
              , i = this.o.length / 2;
            for (; s < i; ) {
                if (Math.abs((t[4 * s] ?? 0) - (t[4 * e + 2 * s] ?? 0)) > .01)
                    return !1;
                s++
            }
            return !0
        }
        getValue(t) {
            let e, s;
            if (this.prop.getValue(),
            this._mdf = !1,
            this._cmdf = !1,
            this._omdf = !1,
            !this.prop._mdf && !t || !Y(this.prop.v))
                return 0;
            let i = 4 * this.data.p;
            for (let r = 0; r < i; r++)
                e = r % 4 == 0 ? 100 : 255,
                s = Math.round((this.prop.v[r] ?? 0) * e),
                this.c[r] !== s && (this.c[r] = s,
                this._cmdf = !t);
            if (this.o.length > 0) {
                let {length: e} = this.prop.v;
                for (let i = 4 * this.data.p; i < e; i++)
                    s = (i % 2 == 0 ? Math.round((this.prop.v[i] ?? 0) * 100) : this.prop.v[i]) ?? 0,
                    this.o[i - 4 * this.data.p] !== s && (this.o[i - 4 * this.data.p] = s,
                    this._omdf = !t)
            }
            return this._mdf = !t,
            0
        }
    }
    class sa extends t8 {
        constructor(t, e, s) {
            super(),
            this.cst = [],
            this.it = [],
            this.ost = [],
            this.prevViewData = [],
            this.stops = [],
            this.initDynamicPropertyContainer(t),
            this.getValue = this.iterateDynamicProperties,
            this.initGradientData(t, e, s)
        }
        initGradientData(t, e, s) {
            this.o = ea.getProp(t, e.o, 0, .01, this),
            this.s = ea.getProp(t, e.s, 1, null, this),
            this.e = ea.getProp(t, e.e, 1, null, this),
            this.h = ea.getProp(t, e.h ?? {
                k: 0
            }, 0, .01, this),
            this.a = ea.getProp(t, e.a ?? {
                k: 0
            }, 0, J, this),
            this.g = new sr(t,e.g,this),
            this.style = s,
            this.stops = [],
            this.setGradientData(s.pElem, e),
            this.setGradientOpacity(e, s),
            this._isAnimated = !!this._isAnimated
        }
        setGradientData(t, e) {
            let s, i = W(), r = tA(1 === e.t ? "linearGradient" : "radialGradient");
            r.id = i,
            r.setAttribute("spreadMethod", "pad"),
            r.setAttribute("gradientUnits", "userSpaceOnUse");
            let a = []
              , n = 4 * (e.g?.p || 1);
            for (let t = 0; t < n; t += 4)
                s = tA("stop"),
                r.appendChild(s),
                a.push(s);
            t.setAttribute(e.ty === O.GradientFill ? "fill" : "stroke", `url(${eA()}#${i})`),
            this.gf = r,
            this.cst = a
        }
        setGradientOpacity(t, e) {
            let s;
            if (!this.g?._hasOpacity || this.g._collapsable)
                return;
            let i = tA("mask")
              , r = tA("path");
            i.appendChild(r);
            let a = W()
              , n = W();
            i.id = n;
            let o = tA(1 === t.t ? "linearGradient" : "radialGradient");
            o.id = a,
            o.setAttribute("spreadMethod", "pad"),
            o.setAttribute("gradientUnits", "userSpaceOnUse");
            let h = (t.g?.k.k?.[0]?.s ? t.g?.k.k[0]?.s.length : t.g?.k.k.length) || 0
              , {stops: l} = this;
            for (let e = 4 * (t.g?.p || 1); e < h; e += 2)
                (s = tA("stop")).setAttribute("stop-color", "rgb(255,255,255)"),
                o.appendChild(s),
                l.push(s);
            r.setAttribute(t.ty === O.GradientFill ? "fill" : "stroke", `url(${eA()}#${a})`),
            t.ty === O.GradientStroke && (r.setAttribute("stroke-linecap", R[t.lc || 2]),
            r.setAttribute("stroke-linejoin", B[t.lj || 2]),
            1 === t.lj && r.setAttribute("stroke-miterlimit", `${Number(t.ml)}`)),
            this.of = o,
            this.ms = i,
            this.ost = l,
            this.maskId = n,
            e.msElem = r
        }
    }
    class sn extends t8 {
        constructor(t, e, s, i) {
            let r;
            super(),
            this.elem = t,
            this.frameId = -1,
            this.dataProps = tG(e.length),
            this.renderer = s,
            this.k = !1,
            this.dashStr = "",
            this.dashArray = tH(F.Float32, e.length > 0 ? e.length - 1 : 0),
            this.dashoffset = tH(F.Float32, 1),
            this.initDynamicPropertyContainer(i);
            let a = e.length || 0;
            for (let s = 0; s < a; s++)
                r = ea.getProp(t, e[s]?.v, 0, 0, this),
                this.k = r.k || this.k,
                this.dataProps[s] = {
                    n: e[s]?.n ?? "d",
                    p: r
                };
            this.k || this.getValue(!0),
            this._isAnimated = this.k
        }
        getValue(t) {
            if (this.elem.globalData?.frameId === this.frameId && !t || (this.elem.globalData?.frameId && (this.frameId = this.elem.globalData.frameId),
            this.iterateDynamicProperties(),
            this._mdf = this._mdf || !!t,
            !this._mdf))
                return 0;
            let e = this.dataProps.length;
            this.renderer === N.SVG && (this.dashStr = "");
            for (let t = 0; t < e; t++) {
                if (this.dataProps[t]?.n === "o") {
                    this.dashoffset[0] = this.dataProps[t]?.p.v;
                    continue
                }
                if (this.renderer === N.SVG) {
                    this.dashStr += ` ${this.dataProps[t]?.p.v}`;
                    continue
                }
                this.dashArray[t] = this.dataProps[t]?.p.v
            }
            return 0
        }
    }
    class so extends sa {
        constructor(t, e, s) {
            super(t, e, s),
            this.initDynamicPropertyContainer(t),
            this.getValue = this.iterateDynamicProperties,
            this.w = ea.getProp(t, e.w, 0, null, this),
            this.d = new sn(t,e.d || [],N.SVG,this),
            this.initGradientData(t, e, s),
            this._isAnimated = !!this._isAnimated
        }
    }
    class sh extends t8 {
        constructor(t, e, s) {
            super(),
            this.it = [],
            this.prevViewData = [],
            this.initDynamicPropertyContainer(t),
            this.getValue = this.iterateDynamicProperties,
            this.style = s
        }
    }
    class sl {
        setAsAnimated() {
            this._isAnimated = !0
        }
        constructor() {
            this.caches = [],
            this.it = [],
            this.lStr = "",
            this.lvl = 0,
            this.pathsData = [],
            this.prevViewData = [],
            this.sh = null,
            this.styledShapes = [],
            this.styles = [],
            this.tr = [],
            this.transformers = [],
            this.trNodes = []
        }
    }
    class sp extends sl {
        constructor(t, e, s) {
            super(),
            this.caches = [],
            this.styles = [],
            this.transformers = t,
            this.lStr = "",
            this.sh = s,
            this.lvl = e,
            this._isAnimated = !!s.k;
            let i = 0
              , {length: r} = t;
            for (; i < r; ) {
                if ((t[i]?.mProps.dynamicProperties.length ?? 0) > 0) {
                    this._isAnimated = !0;
                    break
                }
                i++
            }
        }
    }
    class sm extends si {
        constructor(t, e, s) {
            super(t, e, s),
            this.initDynamicPropertyContainer(t),
            this.getValue = this.iterateDynamicProperties,
            this.o = ea.getProp(t, e.o, 0, .01, this),
            this.w = ea.getProp(t, e.w, 0, null, this),
            this.d = new sn(t,e.d || [],N.SVG,this),
            this.c = ea.getProp(t, e.c, 1, 255, this),
            this.style = s,
            this._isAnimated = !!this._isAnimated
        }
    }
    class sd {
        constructor(t, e) {
            this.data = t,
            this.type = t.ty,
            this.d = "",
            this.lvl = e,
            this._mdf = !1,
            this.closed = !0 === t.hd,
            this.pElem = tA("path"),
            this.msElem = null
        }
        reset() {
            this.d = "",
            this._mdf = !1
        }
    }
    class sc {
        constructor(t, e, s) {
            this.transform = {
                container: s,
                mProps: t,
                op: e
            },
            this.elements = [],
            this._isAnimated = this.transform.mProps.dynamicProperties.length > 0 || this.transform.op.effectsSequence.length > 0
        }
    }
    class su {
        constructor(t, e) {
            this.elem = t,
            this.pos = e
        }
    }
    class sf extends eP {
        addProcessedElement(t, e) {
            let {processedElements: s} = this
              , i = s.length;
            for (; i; ) {
                let r = s[--i];
                if (r?.elem === t) {
                    r.pos = e;
                    return
                }
            }
            s.push(new su(t,e))
        }
        addShapeToModifiers(t) {
            let {length: e} = this.shapeModifiers;
            for (let s = 0; s < e; s++)
                this.shapeModifiers[s]?.addShape(t)
        }
        isShapeInAnimatedModifiers(t) {
            let e = 0
              , {length: s} = this.shapeModifiers;
            for (; e < s; ) {
                if (this.shapeModifiers[e]?.isAnimatedWithShape(t))
                    return !0;
                e++
            }
            return !1
        }
        prepareFrame(t) {
            this.prepareRenderableFrame(t),
            this.prepareProperties(t, this.isInRange)
        }
        renderModifiers() {
            if (0 === this.shapeModifiers.length)
                return;
            let {length: t} = this.shapes;
            for (let e = 0; e < t; e++)
                this.shapes[e]?.sh?.reset();
            let {length: e} = this.shapeModifiers;
            for (let t = e - 1; t >= 0 && !this.shapeModifiers[t]?.processShapes(!!this._isFirstFrame); t--)
                ;
        }
        searchProcessedElement(t) {
            let {processedElements: e} = this
              , {length: s} = e
              , i = 0;
            for (; i < s; ) {
                if (e[i]?.elem === t)
                    return e[i]?.pos ?? 0;
                i++
            }
            return 0
        }
        constructor(...t) {
            super(...t),
            this.processedElements = [],
            this.shapeModifiers = [],
            this.shapes = []
        }
    }
    let sg = {};
    function sy(t, e, s) {
        if (!sg[t])
            throw Error("Invalid modifier");
        return new sg[t]
    }
    function sb(t, e) {
        sg[t] = sg[t] ?? e
    }
    class sv extends sf {
        constructor(t, e, s) {
            super(),
            this.createContainerElements = eK.prototype.createContainerElements,
            this.createRenderableComponents = eK.prototype.createRenderableComponents,
            this.destroyBaseElement = eK.prototype.destroyBaseElement,
            this.getBaseElement = eK.prototype.getBaseElement,
            this.getMatte = eK.prototype.getMatte,
            this.identityMatrix = new e_,
            this.initRendererElement = eK.prototype.initRendererElement,
            this.renderElement = eK.prototype.renderElement,
            this.setMatte = eK.prototype.setMatte,
            this.shapes = [],
            this.shapesData = t.shapes,
            this.stylesList = [],
            this.shapeModifiers = [],
            this.itemsData = [],
            this.processedElements = [],
            this.animatedContents = [],
            this.initElement(t, e, s),
            this.prevViewData = []
        }
        addToAnimatedContents(t, e) {
            let s = 0
              , {length: i} = this.animatedContents;
            for (; s < i; ) {
                if (this.animatedContents[s]?.element === e)
                    return;
                s++
            }
            this.animatedContents.push({
                data: t,
                element: e,
                fn: function(t) {
                    switch (t.ty) {
                    case O.Fill:
                        return e8;
                    case O.GradientFill:
                        return e9;
                    case O.GradientStroke:
                        return e7;
                    case O.Stroke:
                        return ss;
                    case O.Path:
                    case O.Ellipse:
                    case O.Rectangle:
                    case O.PolygonStar:
                        return se;
                    case O.Transform:
                        return e6;
                    case O.NoStyle:
                        return st;
                    default:
                        return null
                    }
                }(t)
            })
        }
        buildExpressionInterface() {}
        createContent() {
            if (!this.layerElement)
                throw Error(`${this.constructor.name}: Could not access Layer`);
            this.searchShapes(this.shapesData, this.itemsData, this.prevViewData, this.layerElement, 0, [], !0),
            this.filterUniqueShapes()
        }
        createGroupElement(t) {
            let e = new e2;
            return t.ln && (e.gr.id = t.ln),
            t.cl && e.gr.classList.add(t.cl),
            t.bm && (e.gr.style.mixBlendMode = eb(t.bm)),
            e
        }
        createShapeElement(t, e, s) {
            let i = 4;
            switch (t.ty) {
            case O.Rectangle:
                i = 5;
                break;
            case O.Ellipse:
                i = 6;
                break;
            case O.PolygonStar:
                i = 7
            }
            let r = new sp(e,s,eH(this, t, i));
            return this.shapes.push(r),
            this.addShapeToModifiers(r),
            this.addToAnimatedContents(t, r),
            r
        }
        createStyleElement(t, e) {
            let s = null
              , i = new sd(t,e)
              , r = i.pElem;
            switch (t.ty) {
            case O.Stroke:
                s = new sm(this,t,i);
                break;
            case O.Fill:
                s = new si(this,t,i);
                break;
            case O.GradientFill:
            case O.GradientStroke:
                (s = new (t.ty === O.GradientFill ? sa : so)(this,t,i)).gf && this.globalData?.defs.appendChild(s.gf),
                s.maskId && s.ms && s.of && (this.globalData?.defs.appendChild(s.ms),
                this.globalData?.defs.appendChild(s.of),
                r.setAttribute("mask", `url(${eA()}#${s.maskId})`));
                break;
            case O.NoStyle:
                s = new sh(this,t,i)
            }
            return (t.ty === O.Stroke || t.ty === O.GradientStroke) && (r.setAttribute("stroke-linecap", R[t.lc || 2]),
            r.setAttribute("stroke-linejoin", B[t.lj || 2]),
            r.setAttribute("fill-opacity", "0"),
            1 === t.lj && t.ml && r.setAttribute("stroke-miterlimit", `${t.ml}`)),
            2 === t.r && r.setAttribute("fill-rule", "evenodd"),
            t.ln && (r.id = t.ln),
            t.cl && r.classList.add(t.cl),
            t.bm && (r.style.mixBlendMode = eb(t.bm)),
            this.stylesList.push(i),
            s && this.addToAnimatedContents(t, s),
            s
        }
        createTransformElement(t, e) {
            let s = eS(this, t, this);
            if (!s.o)
                throw Error(`${this.constructor.name}: Missing required data in TransformProperty`);
            let i = new sc(s,s.o,e);
            return this.addToAnimatedContents(t, i),
            i
        }
        destroy() {
            this.destroyBaseElement(),
            this.shapesData = null,
            this.itemsData = null
        }
        filterUniqueShapes() {
            let t, e, {length: s} = this.shapes, i = this.stylesList.length, r = [];
            for (let a = 0; a < i; a++) {
                t = this.stylesList[a],
                e = !1,
                r.length = 0;
                for (let i = 0; i < s; i++)
                    this.shapes[i].styles.includes(t) && (r.push(this.shapes[i]),
                    e = this.shapes[i]?._isAnimated || e);
                r.length > 1 && e && this.setShapesAsAnimated(r)
            }
        }
        initSecondaryElement() {}
        reloadShapes() {
            if (!this.layerElement)
                throw Error(`${this.constructor.name}: Could not access layerElement`);
            this._isFirstFrame = !0;
            let {length: t} = this.itemsData;
            for (let e = 0; e < t; e++)
                this.prevViewData[e] = this.itemsData[e];
            this.searchShapes(this.shapesData, this.itemsData, this.prevViewData, this.layerElement, 0, [], !0),
            this.filterUniqueShapes();
            let {length: e} = this.dynamicProperties;
            for (let t = 0; t < e; t++)
                this.dynamicProperties[t]?.getValue();
            this.renderModifiers()
        }
        renderInnerContent() {
            this.renderModifiers();
            let {length: t} = this.stylesList;
            for (let e = 0; e < t; e++)
                this.stylesList[e]?.reset();
            this.renderShape();
            for (let e = 0; e < t; e++)
                (this.stylesList[e]?._mdf || this._isFirstFrame) && (this.stylesList[e]?.msElem && (this.stylesList[e]?.msElem?.setAttribute("d", this.stylesList[e]?.d ?? ""),
                this.stylesList[e].d = `M0 0${this.stylesList[e]?.d ?? ""}`),
                this.stylesList[e]?.pElem.setAttribute("d", this.stylesList[e]?.d || "M0 0"))
        }
        renderShape() {
            let {length: t} = this.animatedContents;
            for (let e = 0; e < t; e++)
                (this._isFirstFrame || this.animatedContents[e]?.element._isAnimated) && this.animatedContents[e]?.data !== !0 && this.animatedContents[e]?.fn && this.animatedContents[e]?.fn?.(this.animatedContents[e]?.data, this.animatedContents[e]?.element, this._isFirstFrame)
        }
        searchShapes(t, e, s, i, r, a, n) {
            let o, h, l = n, p = [...a], m = [], d = [], {length: c} = t;
            for (let a = c - 1; a >= 0; a--) {
                let n = this.searchProcessedElement(t[a]);
                switch (n ? e[a] = s[n - 1] : t[a]._shouldRender = l,
                t[a]?.ty) {
                case O.Fill:
                case O.Stroke:
                case O.GradientFill:
                case O.GradientStroke:
                case O.NoStyle:
                    if (n) {
                        let {style: t} = e[a] ?? {
                            style: null
                        };
                        t && (t.closed = !1)
                    } else
                        e[a] = this.createStyleElement(t[a], r);
                    if (t[a]?._shouldRender && e[a]?.style?.pElem.parentNode !== i) {
                        let {pElem: t} = e[a]?.style ?? {
                            pElem: null
                        };
                        t && i.appendChild(t)
                    }
                    m.push(e[a]?.style);
                    break;
                case O.Group:
                    if (n) {
                        let {length: t} = e[a]?.it ?? [];
                        for (let s = 0; s < t; s++) {
                            let {it: t, prevViewData: i} = e[a] ?? {
                                it: null,
                                prevViewData: null
                            };
                            i && t && (i[s] = t[s])
                        }
                    } else
                        e[a] = this.createGroupElement(t[a]);
                    if (this.searchShapes(t[a]?.it, e[a]?.it ?? [], e[a]?.prevViewData ?? [], e[a]?.gr, r + 1, p, l),
                    t[a]?._shouldRender && e[a]?.gr?.parentNode !== i) {
                        let {gr: t} = e[a] ?? {};
                        t && i.appendChild(t)
                    }
                    break;
                case O.Transform:
                    n || (e[a] = this.createTransformElement(t[a], i)),
                    (o = e[a]?.transform) && p.push(o);
                    break;
                case O.Path:
                case O.Rectangle:
                case O.Ellipse:
                case O.PolygonStar:
                    n || (e[a] = this.createShapeElement(t[a], p, r)),
                    this.setElementStyles(e[a]);
                    break;
                case O.Trim:
                case O.RoundedCorners:
                case O.Unknown:
                case O.PuckerBloat:
                case O.ZigZag:
                case O.OffsetPath:
                    n ? (h = e[a]).closed = !1 : ((h = sy(t[a]?.ty ?? O.Unknown)).init(this, t[a]),
                    e[a] = h,
                    this.shapeModifiers.push(h)),
                    d.push(h);
                    break;
                case O.Repeater:
                    if (n) {
                        (h = e[a]).closed = !0,
                        d.push(h);
                        break
                    }
                    h = sy(t[a]?.ty ?? O.Unknown),
                    e[a] = h,
                    h.init(this, t, a, e),
                    this.shapeModifiers.push(h),
                    l = !1,
                    d.push(h)
                }
                this.addProcessedElement(t[a], a + 1)
            }
            let {length: u} = m;
            for (let t = 0; t < u; t++) {
                let e = m[t];
                e && (e.closed = !0)
            }
            let {length: f} = d;
            for (let t = 0; t < f; t++)
                d[t].closed = !0
        }
        setElementStyles(t) {
            let {length: e} = this.stylesList;
            for (let s = 0; s < e; s++)
                this.stylesList[s]?.closed || t.styles.push(this.stylesList[s])
        }
        setShapesAsAnimated(t) {
            let {length: e} = t;
            for (let s = 0; s < e; s++)
                t[s]?.setAsAnimated()
        }
    }
    class s_ {
        constructor(t, e, s, i, r, a) {
            this.o = t,
            this.sw = e,
            this.sc = s,
            this.fc = i,
            this.m = r,
            this.p = a,
            this._mdf = {
                fc: !!i,
                m: !0,
                o: !0,
                p: !0,
                sc: !!s,
                sw: !!e
            }
        }
        update(t, e, s, i, r, a) {
            this._mdf.o = !1,
            this._mdf.sw = !1,
            this._mdf.sc = !1,
            this._mdf.fc = !1,
            this._mdf.m = !1,
            this._mdf.p = !1;
            let n = !1;
            return this.o !== t && (this.o = t,
            this._mdf.o = !0,
            n = !0),
            this.sw !== e && (this.sw = e,
            this._mdf.sw = !0,
            n = !0),
            this.sc !== s && (this.sc = s,
            this._mdf.sc = !0,
            n = !0),
            this.fc !== i && (this.fc = i,
            this._mdf.fc = !0,
            n = !0),
            this.m !== r && (this.m = r,
            this._mdf.m = !0,
            n = !0),
            a && a.length > 0 && (this.p[0] !== a[0] || this.p[1] !== a[1] || this.p[4] !== a[4] || this.p[5] !== a[5] || this.p[12] !== a[12] || this.p[13] !== a[13]) && (this.p = a,
            this._mdf.p = !0,
            n = !0),
            n
        }
    }
    class sw extends t7 {
        constructor(t, e) {
            super(),
            this._currentTextLength = -1,
            this.k = !1,
            this.data = e,
            this.elem = t,
            this.comp = t.comp,
            this.finalS = 0,
            this.finalE = 0,
            this.initDynamicPropertyContainer(t),
            this.s = ea.getProp(t, e.s ?? {
                a: 0,
                k: 0
            }, 0, 0, this),
            "e"in e ? this.e = ea.getProp(t, e.e, 0, 0, this) : this.e = {
                v: 100
            },
            this.o = ea.getProp(t, e.o ?? {
                a: 0,
                k: 0
            }, 0, 0, this),
            this.xe = ea.getProp(t, e.xe ?? {
                a: 0,
                k: 0
            }, 0, 0, this),
            this.ne = ea.getProp(t, e.ne ?? {
                a: 0,
                k: 0
            }, 0, 0, this),
            this.sm = ea.getProp(t, e.sm ?? {
                a: 0,
                k: 100
            }, 0, 0, this),
            this.a = ea.getProp(t, e.a, 0, .01, this),
            0 === this.dynamicProperties.length && this.getValue()
        }
        getMult(t, e) {
            let s = t;
            this._currentTextLength !== this.elem.textProperty?.currentData.l.length && this.getValue();
            let i = 0
              , r = 0
              , a = 1
              , n = 1;
            this.ne.v > 0 ? i = this.ne.v / 100 : r = -this.ne.v / 100,
            this.xe.v > 0 ? a = 1 - this.xe.v / 100 : n = 1 + this.xe.v / 100;
            let o = t4(i, r, a, n).get
              , h = 0
              , l = this.finalS
              , p = this.finalE;
            switch (this.data.sh) {
            case 2:
                h = o(h = p === l ? +(s >= p) : Math.max(0, Math.min(.5 / (p - l) + (s - l) / (p - l), 1)));
                break;
            case 3:
                h = o(h = p === l ? s >= p ? 0 : 1 : 1 - Math.max(0, Math.min(.5 / (p - l) + (s - l) / (p - l), 1)));
                break;
            case 4:
                p === l ? h = 0 : (h = Math.max(0, Math.min(.5 / (p - l) + (s - l) / (p - l), 1))) < .5 ? h *= 2 : h = 1 - 2 * (h - .5),
                h = o(h);
                break;
            case 5:
                if (p === l)
                    h = 0;
                else {
                    let t = p - l
                      , e = -t / 2 + (s = Math.min(Math.max(0, s + .5 - l), p - l))
                      , i = t / 2;
                    h = Math.sqrt(1 - e * e / (i * i))
                }
                h = o(h);
                break;
            case 6:
                h = o(h = p === l ? 0 : (1 + Math.cos(Math.PI + 2 * Math.PI * (s = Math.min(Math.max(0, s + .5 - l), p - l)) / (p - l))) / 2);
                break;
            default:
                s >= Math.floor(l) && (h = s - l < 0 ? Math.max(0, Math.min(Math.min(p, 1) - (l - s), 1)) : Math.max(0, Math.min(p - s, 1))),
                h = o(h)
            }
            if (100 !== this.sm.v) {
                let t = .01 * this.sm.v;
                0 === t && (t = 1e-8);
                let e = .5 - .5 * t;
                h < e ? h = 0 : (h = (h - e) / t) > 1 && (h = 1)
            }
            return h * this.a.v
        }
        getTextSelectorProp(t, e, s) {
            throw Error("Method not implemented")
        }
        getValue(t) {
            this.iterateDynamicProperties(),
            this._mdf = t || this._mdf,
            this._currentTextLength = this.elem.textProperty?.currentData.l.length || 0,
            t && 2 === this.data.r && this.e.v && (this.e.v = this._currentTextLength);
            let e = 2 === this.data.r ? 1 : 100 / this.data.totalChars
              , s = this.o.v / e
              , i = Number(this.s?.v) / e + s
              , r = this.e.v / e + s;
            if (i > r) {
                let t = i;
                i = r,
                r = t
            }
            return this.finalS = i,
            this.finalE = r,
            0
        }
    }
    class sS {
        constructor(t, e, s) {
            let i = {
                propType: !1
            }
              , r = e?.a;
            this.a = {
                a: r?.a ? ea.getProp(t, r.a, 1, 0, s) : i,
                fb: r?.fb ? ea.getProp(t, r.fb, 0, .01, s) : i,
                fc: r?.fc ? ea.getProp(t, r.fc, 1, 0, s) : i,
                fh: r?.fh ? ea.getProp(t, r.fh, 0, 0, s) : i,
                fs: r?.fs ? ea.getProp(t, r.fs, 0, .01, s) : i,
                o: r?.o ? ea.getProp(t, r.o, 0, .01, s) : i,
                p: r?.p ? ea.getProp(t, r.p, 1, 0, s) : i,
                r: r?.r ? ea.getProp(t, r.r, 0, J, s) : i,
                rx: r?.rx ? ea.getProp(t, r.rx, 0, J, s) : i,
                ry: r?.ry ? ea.getProp(t, r.ry, 0, J, s) : i,
                s: r?.s ? ea.getProp(t, r.s, 1, .01, s) : i,
                sa: r?.sa ? ea.getProp(t, r.sa, 0, J, s) : i,
                sc: r?.sc ? ea.getProp(t, r.sc, 1, 0, s) : i,
                sk: r?.sk ? ea.getProp(t, r.sk, 0, J, s) : i,
                sw: r?.sw ? ea.getProp(t, r.sw, 0, 0, s) : i,
                t: r?.t ? ea.getProp(t, r.t, 0, 0, s) : i
            },
            this.s = new sw(t,e?.s),
            this.s.t = e?.s?.t
        }
    }
    let sE = (t, e, s) => {
        let i = Math.max(t, e, s)
          , r = Math.min(t, e, s)
          , a = i - r
          , n = 0;
        switch (i) {
        case r:
            n = 0;
            break;
        case t:
            n = (e - s + 6 * (e < s) * a) / (6 * a);
            break;
        case e:
            n = (s - t + 2 * a) / (6 * a);
            break;
        case s:
            n = (t - e + 4 * a) / (6 * a)
        }
        return [n, 0 === i ? 0 : a / i, i / 255]
    }
      , sk = (t, e, s) => {
        let i = 0
          , r = 0
          , a = 0
          , n = Math.floor(6 * t)
          , o = 6 * t - n
          , h = s * (1 - e)
          , l = s * (1 - o * e)
          , p = s * (1 - (1 - o) * e);
        switch (n % 6) {
        case 0:
            i = s,
            r = p,
            a = h;
            break;
        case 1:
            i = l,
            r = s,
            a = h;
            break;
        case 2:
            i = h,
            r = s,
            a = p;
            break;
        case 3:
            i = h,
            r = l,
            a = s;
            break;
        case 4:
            i = p,
            r = h,
            a = s;
            break;
        case 5:
            i = s,
            r = h,
            a = l
        }
        return [i, r, a]
    }
      , sM = (t, e) => {
        let s = sE(255 * t[0], 255 * t[1], 255 * t[2]);
        return s[2] += e,
        s[2] > 1 ? s[2] = 1 : s[2] < 0 && (s[2] = 0),
        sk(s[0], s[1], s[2])
    }
      , sx = (t, e) => {
        let s = sE(255 * t[0], 255 * t[1], 255 * t[2]);
        return s[0] += e / 360,
        s[0] > 1 ? s[0] -= 1 : s[0] < 0 && s[0]++,
        sk(s[0], s[1], s[2])
    }
      , sP = (t, e) => {
        let s = sE(255 * t[0], 255 * t[1], 255 * t[2]);
        return s[1] += e,
        s[1] > 1 ? s[1] = 1 : s[1] <= 0 && (s[1] = 0),
        sk(s[0], s[1], s[2])
    }
    ;
    class sC extends t8 {
        constructor(t, e, s) {
            super(),
            this.defaultPropsArray = [],
            this.mHelper = new e_,
            this._isFirstFrame = !0,
            this._hasMaskedPath = !1,
            this._frameId = -1,
            this._textData = t,
            this._renderType = e,
            this._elem = s,
            this._animatorsData = tG(Number(this._textData.a?.length)),
            this._pathData = {},
            this._moreOptions = {
                alignment: {}
            },
            this.renderedLetters = [],
            this.lettersChangedFlag = !1,
            this.initDynamicPropertyContainer(s)
        }
        getMeasures(t, e) {
            try {
                if (this.lettersChangedFlag = !!e,
                !this._mdf && !this._isFirstFrame && !e && (!this._hasMaskedPath || !this._pathData.m?._mdf))
                    return;
                this._isFirstFrame = !1;
                let s = this._moreOptions.alignment.v, i = this._animatorsData, r = this._textData, a = this.mHelper, n = this._renderType, o = this.renderedLetters.length, h, l, p, m, d = t.l, c, u = 0, f, g = 0, y, b = 0, v = 0, _, w = null, S = [], E = 0, k = 0, M, x, P = null;
                if (this._hasMaskedPath && this._pathData.m) {
                    if (P = this._pathData.m,
                    !this._pathData.n || this._pathData._mdf) {
                        let t = P.v;
                        if (t) {
                            let e;
                            for (this._pathData.r?.v && (t = t.reverse()),
                            c = {
                                segments: [],
                                tLength: 0
                            },
                            m = t._length - 1,
                            k = 0,
                            p = 0; p < m; p++)
                                U(t) || (e = tJ(t.v[p], t.v[p + 1], [(t.o[p]?.[0] ?? 0) - (t.v[p]?.[0] ?? 0), (t.o[p]?.[1] ?? 0) - (t.v[p]?.[1] ?? 0)], [(t.i[p + 1]?.[0] ?? 0) - (t.v[p + 1]?.[0] ?? 0), (t.i[p + 1]?.[1] ?? 0) - (t.v[p + 1]?.[1] ?? 0)]),
                                c.tLength += e.segmentLength,
                                c.segments.push(e),
                                k += e.segmentLength);
                            p = m,
                            P.v?.c && !U(t) && (e = tJ(t.v[p], t.v[0], [(t.o[p]?.[0] ?? 0) - (t.v[p]?.[0] ?? 0), (t.o[p]?.[1] ?? 0) - (t.v[p]?.[1] ?? 0)], [(t.i[0]?.[0] ?? 0) - (t.v[0]?.[0] ?? 0), (t.i[0]?.[1] ?? 0) - (t.v[0]?.[1] ?? 0)]),
                            c.tLength += e.segmentLength,
                            c.segments.push(e),
                            k += e.segmentLength),
                            this._pathData.pi = c
                        }
                    }
                    if (c = this._pathData.pi,
                    u = this._pathData.f?.v ?? 0,
                    v = 0,
                    b = 1,
                    g = 0,
                    S = c?.segments ?? [],
                    u > 0 && P.v?.c)
                        for ((c?.tLength ?? 0 < Math.abs(u)) && (u = -Math.abs(u) % (c?.tLength ?? 0)),
                        v = S.length - 1,
                        b = (w = S[v]?.points ?? []).length - 1; u < 0; )
                            u += w[b]?.partialLength ?? 0,
                            (b -= 1) < 0 && (v -= 1,
                            b = (w = S[v]?.points ?? []).length - 1);
                    _ = (w = S[v]?.points ?? [])[b - 1],
                    f = w[b],
                    E = f?.partialLength ?? 0
                }
                m = d.length || 0,
                h = 0,
                l = 0;
                let C = 1.2 * (t.finalSize || 0) * .714, A = !0, T, D, I, L, F = i.length, $, O = -1, V, z = 0, R = 0, B = u, q = v, j = b, H = -1, G, W = [0, 0, 0], Y = 0, X = [0, 0, 0], J, Z, K, Q, tt = "", te = this.defaultPropsArray, ts;
                if (2 === t.j || 1 === t.j) {
                    let e = 0
                      , s = 0
                      , a = 2 === t.j ? -.5 : -1
                      , n = 0
                      , o = !0;
                    for (p = 0; p < m; p++) {
                        if (d[p]?.n) {
                            for (e && (e += s); n < p; )
                                (d[n] ?? {}).animatorJustifyOffset = e,
                                n++;
                            e = 0,
                            o = !0;
                            continue
                        }
                        for (I = 0; I < F; I++) {
                            if (T = i[I]?.a ?? {},
                            !T.t?.propType)
                                continue;
                            let {v: n} = T.t;
                            "number" == typeof n && (o && 2 === t.j && (s += n * a),
                            D = i[I]?.s,
                            $ = D?.getMult(d[p]?.anIndexes[I] ?? 0, r.a?.[I]?.s?.totalChars),
                            void 0 !== $ && (U($) ? e += n * ($[0] ?? 1) * a : e += n * $ * a))
                        }
                        o = !1
                    }
                    for (e && (e += s); n < p; )
                        d[n].animatorJustifyOffset = e,
                        n++
                }
                for (p = 0; p < m; p++) {
                    if (a.reset(),
                    G = 1,
                    d[p]?.n)
                        h = 0,
                        l += t.yOffset || 0,
                        l += +!!A,
                        u = B,
                        A = !1,
                        this._hasMaskedPath && (v = q || 0,
                        b = j || 0,
                        _ = (w = S[v]?.points ?? [])[b - 1],
                        f = w[b],
                        E = f?.partialLength ?? 0,
                        g = 0),
                        tt = "",
                        Q = "",
                        Z = "",
                        ts = "",
                        te = this.defaultPropsArray;
                    else {
                        if (this._hasMaskedPath) {
                            if (H !== d[p]?.line) {
                                switch (t.j) {
                                case 1:
                                    u += k - (t.lineWidths[d[p]?.line ?? 0] ?? 0);
                                    break;
                                case 2:
                                    u += (k - (t.lineWidths[d[p]?.line ?? 0] ?? 0)) / 2
                                }
                                H = d[p]?.line || 0
                            }
                            O !== d[p]?.ind && (d[O] && (u += Number(d[O]?.extra)),
                            u += (d[p]?.an || 0) / 2,
                            O = d[p]?.ind || 0),
                            u += s[0] * (d[p]?.an || 0) * .005;
                            let e = 0;
                            for (I = 0; I < F; I++) {
                                if (T = i[I]?.a,
                                T?.p.propType) {
                                    if (D = i[I]?.s,
                                    !($ = D?.getMult(d[p]?.anIndexes[I] ?? 0, r.a?.[I]?.s?.totalChars)))
                                        continue;
                                    U(T.p.v) && (U($) ? e += T.p.v[0] * ($[0] ?? 1) : e += T.p.v[0] * $)
                                }
                                if (T?.a.propType) {
                                    if ((D = i[I]?.s,
                                    $ = D?.getMult(Number(d[p]?.anIndexes[I]), r.a?.[I]?.s?.totalChars)) && U(T.a.v)) {
                                        if (U($)) {
                                            e += (T.a.v[0] ?? 0) * ($[0] ?? 1);
                                            continue
                                        }
                                        e += (T.a.v[0] ?? 0) * $
                                    }
                                }
                            }
                            for (y = !0,
                            this._pathData.a?.v && (u = .5 * (d[0]?.an || 0) + (k - Number(this._pathData.f?.v) - .5 * (d[0]?.an || 0) - (d[d.length - 1]?.an ?? 0) * .5) * O / (m - 1) + Number(this._pathData.f?.v)); y; )
                                g + E >= u + e || !w ? (M = (u + e - g) / (f?.partialLength || 0),
                                z = Number(_?.point[0]) + (Number(f?.point[0]) - Number(_?.point[0])) * M,
                                R = Number(_?.point[1]) + (Number(f?.point[1]) - Number(_?.point[1])) * M,
                                a.translate(-s[0] * (d[p]?.an ?? 0) * .005, -(.01 * (s[1] * C))),
                                y = !1) : w.length > 0 && (g += Number(f?.partialLength),
                                ++b >= w.length && (b = 0,
                                S[++v] ? w = S[v]?.points ?? [] : P?.v?.c ? (b = 0,
                                v = 0,
                                w = S[v]?.points ?? []) : (g -= Number(f?.partialLength),
                                w = null)),
                                w && (_ = f,
                                f = w[b],
                                E = f?.partialLength ?? 0));
                            V = (d[p]?.an ?? 0) / 2 - (d[p]?.add ?? 0),
                            a.translate(-V, 0, 0)
                        } else
                            V = (d[p]?.an ?? 0) / 2 - (d[p]?.add ?? 0),
                            a.translate(-V, 0, 0),
                            a.translate(-s[0] * (d[p]?.an ?? 0) * .005, -s[1] * C * .01, 0);
                        for (I = 0; I < F; I++)
                            if (T = i[I]?.a ?? {},
                            T.t?.propType && (D = i[I]?.s,
                            $ = D?.getMult(Number(d[p]?.anIndexes[I]), r.a?.[I]?.s?.totalChars),
                            0 !== h || 0 !== t.j)) {
                                if (this._hasMaskedPath) {
                                    U($) ? u += Number(T.t.v) * ($[0] ?? 1) : u += Number(T.t.v) * Number($);
                                    continue
                                }
                                if (U($)) {
                                    h += Number(T.t.v) * ($[0] ?? 1);
                                    continue
                                }
                                h += Number(T.t.v) * Number($)
                            }
                        for (t.strokeWidthAnim && (Y = t.sw || 0),
                        t.strokeColorAnim && (W = t.sc ? [t.sc[0], t.sc[1], t.sc[2]] : [0, 0, 0]),
                        t.fillColorAnim && t.fc && (X = [Number(t.fc[0]), Number(t.fc[1]), Number(t.fc[2])]),
                        I = 0; I < F; I++)
                            if (T = i[I]?.a ?? {},
                            T.a?.propType) {
                                if (D = i[I]?.s,
                                !($ = D?.getMult(d[p]?.anIndexes[I] ?? 0, r.a?.[I]?.s?.totalChars)) || !U(T.a.v))
                                    continue;
                                if (U($)) {
                                    a.translate(-(T.a.v[0] ?? 0) * ($[0] ?? 1), -(T.a.v[1] ?? 0) * ($[1] ?? 1), (T.a.v[2] ?? 0) * ($[2] ?? 1));
                                    continue
                                }
                                a.translate(-(T.a.v[0] ?? 0) * $, -(T.a.v[1] ?? 0) * $, (T.a.v[2] ?? 0) * $)
                            }
                        for (I = 0; I < F; I++)
                            if (T = i[I]?.a ?? {},
                            T.s?.propType) {
                                if (D = i[I]?.s,
                                !($ = D?.getMult(d[p]?.anIndexes[I] ?? 0, r.a?.[I]?.s?.totalChars)) || !U(T.s.v))
                                    continue;
                                if (U($)) {
                                    a.scale(1 + ((T.s.v[0] ?? 0) - 1) * ($[0] ?? 1), 1 + ((T.s.v[1] ?? 0) - 1) * ($[1] ?? 1), 1);
                                    continue
                                }
                                a.scale(1 + ((T.s.v[0] ?? 0) - 1) * $, 1 + ((T.s.v[1] ?? 0) - 1) * $, 1)
                            }
                        for (I = 0; I < F; I++)
                            if (T = i[I]?.a ?? {},
                            D = i[I]?.s,
                            $ = D?.getMult(d[p]?.anIndexes[I] ?? 0, r.a?.[I]?.s?.totalChars)) {
                                if (T.sk?.propType && (U($) ? a.skewFromAxis(-Number(T.sk.v) * ($[0] ?? 1), Number(T.sa?.v) * ($[1] ?? 1)) : a.skewFromAxis(-Number(T.sk.v) * $, Number(T.sa?.v) * $)),
                                T.r?.propType && (U($) ? a.rotateZ(-Number(T.r.v) * ($[2] ?? 1)) : a.rotateZ(-Number(T.r.v) * $)),
                                T.ry?.propType && (U($) ? a.rotateY(Number(T.ry.v) * ($[1] ?? 1)) : a.rotateY(Number(T.ry.v) * $)),
                                T.rx?.propType && (U($) ? a.rotateX(Number(T.rx.v) * ($[0] ?? 1)) : a.rotateX(Number(T.rx.v) * $)),
                                T.o?.propType && (U($) ? G += (Number(T.o.v) * ($[0] ?? 1) - G) * ($[0] ?? 1) : G += (Number(T.o.v) * $ - G) * $),
                                t.strokeWidthAnim && T.sw?.propType && (U($) ? Y += Number(T.sw.v) * ($[0] ?? 1) : Y += Number(T.sw.v) * $),
                                t.strokeColorAnim && T.sc?.propType && U(T.sc.v))
                                    for (J = 0; J < 3; J++)
                                        U($) ? W[J] += ((T.sc.v[J] ?? 0) - (W[J] ?? 0)) * ($[0] ?? 1) : W[J] += ((T.sc.v[J] ?? 0) - (W[J] ?? 0)) * $;
                                if (t.fillColorAnim && t.fc) {
                                    if (T.fc?.propType)
                                        for (J = 0; J < 3; J++)
                                            U(T.fc.v) && (U($) ? X[J] += ((T.fc.v[J] ?? 0) - (X[J] ?? 0)) * ($[0] ?? 1) : X[J] += ((T.fc.v[J] ?? 0) - (X[J] ?? 0)) * $);
                                    T.fh?.propType && (X = U($) ? sx(X, Number(T.fh.v) * ($[0] ?? 1)) : sx(X, Number(T.fh.v) * $)),
                                    T.fs?.propType && (X = U($) ? sP(X, Number(T.fs.v) * ($[0] ?? 1)) : sP(X, Number(T.fs.v) * $)),
                                    T.fb?.propType && (X = U($) ? sM(X, Number(T.fb.v) * ($[0] ?? 1)) : sM(X, Number(T.fb.v) * $))
                                }
                            }
                        for (I = 0; I < F; I++)
                            if (T = i[I]?.a ?? {},
                            T.p?.propType) {
                                if (D = i[I]?.s,
                                !($ = D?.getMult(d[p]?.anIndexes[I] ?? 0, r.a?.[I]?.s?.totalChars)) || !U(T.p.v))
                                    continue;
                                this._hasMaskedPath ? U($) ? a.translate(0, T.p.v[1] * $[0], -T.p.v[2] * $[1]) : a.translate(0, T.p.v[1] * $, -T.p.v[2] * $) : U($) ? a.translate(T.p.v[0] * $[0], T.p.v[1] * $[1], -T.p.v[2] * $[2]) : a.translate(T.p.v[0] * $, T.p.v[1] * $, -T.p.v[2] * $)
                            }
                        if (t.strokeWidthAnim && (Z = Y < 0 ? 0 : Y),
                        t.strokeColorAnim && (K = `rgb(${Math.round(255 * W[0])},${Math.round(255 * W[1])},${Math.round(255 * W[2])})`),
                        t.fillColorAnim && t.fc && (Q = `rgb(${Math.round(255 * X[0])},${Math.round(255 * X[1])},${Math.round(255 * X[2])})`),
                        this._hasMaskedPath) {
                            if (a.translate(0, -Number(t.ls)),
                            a.translate(0, s[1] * C * .01 + l, 0),
                            this._pathData.p?.v) {
                                x = (Number(f?.point[1]) - Number(_?.point[1])) / (Number(f?.point[0]) - Number(_?.point[0]));
                                let t = 180 * Math.atan(x) / Math.PI;
                                Number(f?.point[0]) < Number(_?.point[0]) && (t += 180),
                                a.rotate(-t * Math.PI / 180)
                            }
                            a.translate(z, R, 0),
                            u -= s[0] * (d[p]?.an ?? 0) * .005,
                            d[p + 1] && O !== d[p + 1]?.ind && (u += (d[p]?.an ?? 0) / 2,
                            u += .001 * t.tr * Number(t.finalSize))
                        } else {
                            switch (a.translate(h, l, 0),
                            t.ps && a.translate(t.ps[0], t.ps[1] + Number(t.ascent), 0),
                            t.j) {
                            case 1:
                                a.translate((d[p]?.animatorJustifyOffset ?? 0) + Number(t.justifyOffset) + (Number(t.boxWidth) - Number(t.lineWidths[d[p]?.line ?? 0])), 0, 0);
                                break;
                            case 2:
                                a.translate((d[p]?.animatorJustifyOffset ?? 0) + Number(t.justifyOffset) + (Number(t.boxWidth) - Number(t.lineWidths[d[p]?.line ?? 0])) / 2, 0, 0)
                            }
                            a.translate(0, -Number(t.ls)),
                            a.translate(V, 0, 0),
                            a.translate(s[0] * Number(d[p]?.an) * .005, s[1] * C * .01, 0),
                            h += (d[p]?.l ?? 0) + .001 * t.tr * Number(t.finalSize)
                        }
                        n === N.HTML ? tt = a.toCSS() : n === N.SVG ? tt = a.to2dCSS() : te = [a.props[0] ?? 0, a.props[1] ?? 0, a.props[2] ?? 0, a.props[3] ?? 0, a.props[4] ?? 0, a.props[5] ?? 0, a.props[6] ?? 0, a.props[7] ?? 0, a.props[8] ?? 0, a.props[9] ?? 0, a.props[10] ?? 0, a.props[11] ?? 0, a.props[12] ?? 0, a.props[13] ?? 0, a.props[14] ?? 0, a.props[15] ?? 0],
                        ts = G
                    }
                    if (o <= p) {
                        L = new s_(Number(ts),Number(Z),K,Q,tt,te),
                        this.renderedLetters.push(L),
                        o++,
                        this.lettersChangedFlag = !0;
                        continue
                    }
                    L = this.renderedLetters[p],
                    this.lettersChangedFlag = L.update(Number(ts), Number(Z), K, Q, tt, te) || this.lettersChangedFlag
                }
            } catch (t) {
                console.error(this.constructor.name, t)
            }
        }
        getValue() {
            return this._elem.globalData?.frameId === this._frameId || (this._frameId = this._elem.globalData?.frameId ?? 0,
            this.iterateDynamicProperties()),
            0
        }
        searchProperties(t) {
            let {length: e} = this._textData.a ?? []
              , {getProp: s} = ea;
            for (let t = 0; t < e; t++)
                this._animatorsData[t] = new sS(this._elem,this._textData.a?.[t],this);
            this._textData.p && "m"in this._textData.p && this._elem.maskManager ? (this._pathData = {
                a: s(this._elem, this._textData.p.a, 0, 0, this),
                f: s(this._elem, this._textData.p.f, 0, 0, this),
                l: s(this._elem, this._textData.p.l, 0, 0, this),
                m: this._elem.maskManager.getMaskProperty(this._textData.p.m),
                p: s(this._elem, this._textData.p.p, 0, 0, this),
                r: s(this._elem, this._textData.p.r, 0, 0, this)
            },
            this._hasMaskedPath = !0) : this._hasMaskedPath = !1,
            this._moreOptions.alignment = s(this._elem, this._textData.m?.a, 1, 0, this)
        }
    }
    function sA(t) {
        let e = t.fStyle ? t.fStyle.split(" ") : []
          , s = "normal"
          , i = "normal"
          , {length: r} = e;
        for (let t = 0; t < r; t++)
            switch (e[t]?.toLowerCase()) {
            case "italic":
                i = "italic";
                break;
            case "bold":
                s = "700";
                break;
            case "black":
                s = "900";
                break;
            case "medium":
                s = "500";
                break;
            case "regular":
            case "normal":
                s = "400";
                break;
            case "light":
            case "thin":
                s = "200"
            }
        return {
            style: i,
            weight: t.fWeight || s
        }
    }
    let sT = [2304, 2305, 2306, 2307, 2362, 2363, 2364, 2364, 2366, 2367, 2368, 2369, 2370, 2371, 2372, 2373, 2374, 2375, 2376, 2377, 2378, 2379, 2380, 2381, 2382, 2383, 2387, 2388, 2389, 2390, 2391, 2402, 2403]
      , sD = {
        data: {
            shapes: []
        },
        shapes: [],
        size: 0,
        w: 0
    }
      , sI = ["d83cdffb", "d83cdffc", "d83cdffd", "d83cdffe", "d83cdfff"];
    function sL(t) {
        let e = sF(t);
        return e >= 127462 && e <= 127487
    }
    function sF(t) {
        let e = 0
          , s = t.charCodeAt(0);
        if (s >= 55296 && s <= 56319) {
            let i = t.charCodeAt(1);
            i >= 56320 && i <= 57343 && (e = (s - 55296) * 1024 + i - 56320 + 65536)
        }
        return e
    }
    function s$(t, e) {
        if (Q)
            return;
        let s = tC("span");
        s.setAttribute("aria-hidden", "true"),
        s.style.fontFamily = e;
        let i = tC("span");
        i.innerText = "giItT1WQy@!-/#",
        s.style.position = "absolute",
        s.style.left = "-10000px",
        s.style.top = "-10000px",
        s.style.fontSize = "300px",
        s.style.fontVariant = "normal",
        s.style.fontStyle = "normal",
        s.style.fontWeight = "normal",
        s.style.letterSpacing = "0",
        s.appendChild(i),
        document.body.appendChild(s);
        let r = i.offsetWidth;
        return i.style.fontFamily = `${function(t) {
            let e = t.split(",")
              , {length: s} = e
              , i = [];
            for (let t = 0; t < s; t++)
                "sans-serif" !== e[t] && "monospace" !== e[t] && i.push(e[t]);
            return i.join(",")
        }(t)}, ${e}`,
        {
            node: i,
            parent: s,
            w: r
        }
    }
    class sN {
        constructor() {
            this.chars = null,
            this.fonts = [],
            this.isLoaded = !1,
            this.typekitLoaded = 0,
            this._warned = !1,
            this.initTime = Date.now(),
            this.setIsLoadedBinded = this.setIsLoaded.bind(this),
            this.checkLoadedFontsBinded = this.checkLoadedFonts.bind(this)
        }
        addChars(t) {
            if (!t)
                return;
            this.chars = this.chars ?? [];
            let {length: e} = t, s, {length: i} = this.chars, r;
            for (let a = 0; a < e; a++) {
                for (s = 0,
                r = !1; s < i; )
                    this.chars[s]?.style === t[a]?.style && this.chars[s]?.fFamily === t[a]?.fFamily && this.chars[s]?.ch === t[a]?.ch && (r = !0),
                    s++;
                !r && (this.chars.push(t[a]),
                i++)
            }
        }
        addFonts(t, e) {
            if (!t) {
                this.isLoaded = !0;
                return
            }
            if (this.chars) {
                this.isLoaded = !0,
                this.fonts = t.list;
                return
            }
            let {length: s} = t.list;
            if (!Q) {
                this.isLoaded = !0;
                for (let e = 0; e < s; e++) {
                    let s = t.list[e];
                    s && (s.helper = this.createHelper(s),
                    s.cache = {})
                }
                this.fonts = t.list;
                return
            }
            let i = s;
            for (let s = 0; s < i; s++) {
                let r = !0, a, n = t.list[s];
                if (n) {
                    if (n.loaded = !1,
                    n.monoCase = s$(n.fFamily, "monospace"),
                    n.sansCase = s$(n.fFamily, "sans-serif"),
                    n.fPath) {
                        if ("p" === n.fOrigin || 3 === n.origin) {
                            if ((a = document.querySelectorAll(`style[f-forigin="p"][f-family="${n.fFamily}"], style[f-origin="3"][f-family="${n.fFamily}"]`)).length > 0 && (r = !1),
                            r) {
                                let t = tC("style");
                                t.setAttribute("f-forigin", n.fOrigin),
                                t.setAttribute("f-origin", `${n.origin}`),
                                t.setAttribute("f-family", n.fFamily),
                                t.innerText = `@font-face {font-family: ${n.fFamily}; font-style: normal; src: url('${n.fPath}');}`,
                                e?.appendChild(t)
                            }
                        } else if ("g" === n.fOrigin || 1 === n.origin) {
                            let {length: t} = a = document.querySelectorAll('link[f-forigin="g"], link[f-origin="1"]');
                            for (s = 0; s < t; s++)
                                a[s]?.href.includes(n.fPath) && (r = !1);
                            if (r) {
                                let t = tC("link");
                                t.setAttribute("f-forigin", n.fOrigin),
                                t.setAttribute("f-origin", `${n.origin}`),
                                t.type = "text/css",
                                t.rel = "stylesheet",
                                t.href = n.fPath,
                                document.body.appendChild(t)
                            }
                        } else if ("t" === n.fOrigin || 2 === n.origin) {
                            let {length: t} = a = document.querySelectorAll('script[f-forigin="t"], script[f-origin="2"]');
                            for (s = 0; s < t; s++)
                                n.fPath === a[s]?.src && (r = !1);
                            if (r) {
                                let t = tC("link");
                                t.setAttribute("f-forigin", n.fOrigin),
                                t.setAttribute("f-origin", `${n.origin}`),
                                t.rel = "stylesheet",
                                t.href = n.fPath,
                                e?.appendChild(t)
                            }
                        }
                    } else
                        n.loaded = !0,
                        i -= 1;
                    n.helper = this.createHelper(n, e),
                    n.cache = {},
                    this.fonts.push(n)
                }
            }
            0 === i ? this.isLoaded = !0 : setTimeout(this.checkLoadedFonts.bind(this), 100)
        }
        getCharData(t, e, s) {
            let i = 0
              , {length: r} = this.chars ?? [];
            for (; i < r; ) {
                if (this.chars?.[i]?.ch === t && this.chars[i]?.style === e && this.chars[i]?.fFamily === s)
                    return this.chars[i];
                i++
            }
            return ("string" != typeof t || 13 === t.charCodeAt(0)) && t || this._warned || (this._warned = !0,
            console.warn("Missing character from exported characters list: ", t, e, s)),
            sD
        }
        getFontByName(t) {
            let e = 0
              , {length: s} = this.fonts;
            for (; e < s; ) {
                if (this.fonts[e]?.fName === t)
                    return this.fonts[e];
                e++
            }
            return this.fonts[0]
        }
        measureText(t, e, s) {
            let i = this.getFontByName(e);
            if (i.cache && !i.cache[t]) {
                let e = i.helper;
                if (" " === t) {
                    let s = Number(e?.measureText(`|${t}|`))
                      , r = Number(e?.measureText("||"));
                    i.cache[t] = (s - r) / 100
                } else
                    i.cache[t] = Number(e?.measureText(t)) / 100
            }
            return Number(i.cache?.[t]) * Number(s)
        }
        checkLoadedFonts() {
            let t, e, s = this.fonts.length;
            for (let i = 0; i < s; i++) {
                let r = this.fonts[i];
                if (!r)
                    continue;
                if (r.loaded) {
                    s -= 1;
                    continue
                }
                if ("n" === r.fOrigin || 0 === r.origin) {
                    r.loaded = !0;
                    continue
                }
                if (t = r.monoCase?.node,
                e = r.monoCase?.w || 0,
                t?.offsetWidth === e ? (t = r.sansCase?.node,
                e = r.sansCase?.w || 0,
                t?.offsetWidth !== e && (s -= 1,
                r.loaded = !0)) : (s -= 1,
                r.loaded = !0),
                !r.loaded)
                    continue;
                let {monoCase: a, sansCase: n} = r;
                n && n.parent.parentNode?.removeChild(n.parent),
                a && a.parent.parentNode?.removeChild(a.parent)
            }
            if (0 !== s && Date.now() - this.initTime < 5e3)
                return void setTimeout(this.checkLoadedFontsBinded, 20);
            setTimeout(this.setIsLoadedBinded, 10)
        }
        createHelper(t, e) {
            let s;
            if (Q)
                return;
            let i = e ? N.SVG : N.Canvas
              , r = sA(t);
            if (i === N.SVG) {
                let i = tA("text");
                i.style.fontSize = "100px",
                i.setAttribute("font-family", t.fFamily),
                i.setAttribute("font-style", r.style),
                i.setAttribute("font-weight", r.weight),
                i.textContent = "1",
                t.fClass ? (i.style.fontFamily = "inherit",
                i.classList.add(t.fClass)) : i.style.fontFamily = t.fFamily,
                e?.appendChild(i),
                s = i
            } else {
                let e = new OffscreenCanvas(500,500).getContext("2d");
                e && (e.font = `${r.style} ${r.weight} 100px ${t.fFamily}`,
                s = e)
            }
            return {
                measureText: t => i === N.SVG ? (s.textContent = t,
                s.getComputedTextLength()) : s.measureText(t).width
            }
        }
        setIsLoaded() {
            this.isLoaded = !0
        }
    }
    class sO extends t7 {
        constructor(t, e) {
            super(),
            this.defaultBoxWidth = [0, 0],
            this._frameId = -999999,
            this.pv = "",
            this.v = "",
            this.kf = !1,
            this._isFirstFrame = !0,
            this._mdf = !1,
            e.d?.sid && (e.d = t.globalData?.slotManager?.getProp(e.d)),
            this.data = e,
            this.elem = t,
            this.comp = this.elem.comp,
            this.keysIndex = 0,
            this.canResize = !1,
            this.minimumFontSize = 1,
            this.effectsSequence = [],
            this.currentData = {
                __complete: !1,
                ascent: 0,
                boxWidth: this.defaultBoxWidth,
                f: "",
                fc: "",
                fillColorAnim: !1,
                finalLineHeight: 0,
                finalSize: 0,
                finalText: [],
                fStyle: "",
                fWeight: "",
                justifyOffset: 0,
                l: [],
                lh: 0,
                lineWidths: [],
                ls: 0,
                of: "",
                ps: null,
                s: 0,
                sc: "",
                strokeColorAnim: !1,
                strokeWidthAnim: !1,
                sw: 0,
                t: 0,
                tr: 0,
                yOffset: 0
            };
            let s = this.data.d?.k[0]?.s;
            s && this.copyData(this.currentData, s),
            this.searchProperty() || this.completeTextData(this.currentData)
        }
        addEffect(t) {
            this.effectsSequence.push(t),
            this.elem.addDynamicProperty(this)
        }
        buildFinalText(t) {
            let e = [], s = 0, i = t.length, r, a, n, o = !1, h;
            for (; s < i; ) {
                var l;
                (n = o,
                o = !1,
                r = t.charCodeAt(s),
                h = t.charAt(s),
                sT.includes(r)) ? n = !0 : r >= 55296 && r <= 56319 ? function(t, e) {
                    let s = e
                      , i = sF(t.slice(s, 2));
                    if (127988 !== i)
                        return !1;
                    let r = 0;
                    for (s += 2; r < 5; ) {
                        if ((i = sF(t.slice(s, 2))) < 917601 || i > 917626)
                            return !1;
                        r++,
                        s += 2
                    }
                    return 917631 === sF(t.slice(s, 2))
                }(t, s) ? h = t.slice(s, 14) : (a = t.charCodeAt(s + 1)) >= 56320 && a <= 57343 && (function(t, e) {
                    let s = t.toString(16) + e.toString(16);
                    return sI.includes(s)
                }(r, a) ? (h = t.slice(s, 2),
                n = !0) : h = sL((l = t.slice(s, 4)).slice(0, 2)) && sL(l.slice(2, 2)) ? t.slice(s, 4) : t.slice(s, 2)) : r > 56319 ? 65039 === r && (n = !0) : 8205 === r && (n = !0,
                o = !0),
                n ? e[e.length - 1] += h : e.push(h),
                s += h.length
            }
            return e
        }
        calculateExpression(t) {
            throw Error(`${this.constructor.name}: Method calculateExpression is not implemented`)
        }
        canResizeFont(t) {
            this.canResize = t,
            this.recalculate(this.keysIndex),
            this.elem.addDynamicProperty(this)
        }
        completeTextData(t) {
            let e, s, i, r, a, n, o;
            t.__complete = !0;
            let {fontManager: h} = this.elem.globalData ?? {};
            if (!h)
                throw Error(`${this.constructor.name}: FontManager not loaded to globalData`);
            let {canResize: l, data: p, minimumFontSize: m} = this, d = [], c, u, f = 0, g, y = p.m?.g, b = 0, v = 0, _ = 0, w = [], S, E = 0, k = h.getFontByName(t.f), M = sA(k);
            t.fWeight = M.weight,
            t.fStyle = M.style,
            t.finalSize = t.s,
            t.finalText = this.buildFinalText(`${t.t}`),
            c = t.finalText.length || 0,
            t.finalLineHeight = t.lh;
            let x = t.tr / 1e3 * t.finalSize;
            if (t.sz) {
                let r, a, n = !0, [o,p] = t.sz;
                for (; n; ) {
                    a = this.buildFinalText(`${t.t}`),
                    r = 0,
                    S = 0,
                    c = a.length,
                    x = t.tr / 1e3 * t.finalSize;
                    let d = -1;
                    for (let n = 0; n < c; n++)
                        i = a[n]?.charCodeAt(0) ?? 0,
                        u = !1,
                        " " === a[n] ? d = n : (13 === i || 3 === i) && (S = 0,
                        u = !0,
                        r += t.finalLineHeight || 1.2 * t.finalSize),
                        h.chars ? (e = h.getCharData(a[n] ?? "", k.fStyle, k.fFamily),
                        s = u ? 0 : e.w * t.finalSize / 100) : s = h.measureText(a[n] ?? "", t.f, t.finalSize) || 0,
                        S + s > o && " " !== a[n] ? (-1 === d ? c++ : n = d,
                        r += t.finalLineHeight || 1.2 * t.finalSize,
                        a.splice(n, +(d === n), "\r"),
                        d = -1,
                        S = 0) : (S += s,
                        S += x);
                    r += Number(k.ascent) * t.finalSize / 100,
                    l && t.finalSize > m && p < r ? (t.finalSize -= 1,
                    t.finalLineHeight = t.finalSize * t.lh / t.s) : (t.finalText = a,
                    c = t.finalText.length,
                    n = !1)
                }
            }
            S = -x;
            let P = 0, C;
            for (let r = 0; r < c; r++)
                if (u = !1,
                13 === (i = (C = t.finalText[r] ?? "").charCodeAt(0)) || 3 === i ? (P = 0,
                w.push(S),
                E = S > E ? S : E,
                S = -2 * x,
                g = "",
                u = !0,
                _++) : g = C,
                h.chars ? (e = h.getCharData(C, k.fStyle, h.getFontByName(t.f).fFamily),
                s = u ? 0 : e.w * t.finalSize / 100) : s = h.measureText(g, t.f, t.finalSize),
                " " === C ? P += s + x : (S += s + x + P,
                P = 0),
                d.push({
                    add: b,
                    an: s,
                    animatorJustifyOffset: 0,
                    anIndexes: [],
                    l: s,
                    line: _,
                    n: u,
                    val: g
                }),
                2 === y) {
                    if (b += s,
                    "" === g || " " === g || r === c - 1) {
                        for (("" === g || " " === g) && (b -= s); v <= r; ) {
                            let t = d[v];
                            t && (t.an = b,
                            t.ind = f,
                            t.extra = s),
                            v++
                        }
                        f++,
                        b = 0
                    }
                } else if (3 === y) {
                    if (b += s,
                    "" === g || r === c - 1) {
                        "" === g && (b -= s);
                        let t = d[v];
                        for (; v <= r; )
                            t && (t.an = b,
                            t.ind = f,
                            t.extra = s),
                            v++;
                        b = 0,
                        f++
                    }
                } else {
                    let t = d[f];
                    t && (t.ind = f,
                    t.extra = 0),
                    f++
                }
            if (t.l = d,
            E = S > E ? S : E,
            w.push(S),
            t.sz)
                t.boxWidth = t.sz[0],
                t.justifyOffset = 0;
            else
                switch (t.boxWidth = E,
                t.j) {
                case 1:
                    t.justifyOffset = -t.boxWidth;
                    break;
                case 2:
                    t.justifyOffset = -t.boxWidth / 2;
                    break;
                default:
                    t.justifyOffset = 0
                }
            t.lineWidths = w;
            let A = p.a
              , {length: T} = A ?? []
              , D = [];
            for (let e = 0; e < T; e++) {
                if (!(r = A?.[e]))
                    continue;
                r.a?.sc && (t.strokeColorAnim = !0),
                r.a?.sw && (t.strokeWidthAnim = !0),
                (r.a?.fc || r.a?.fh || r.a?.fs || r.a?.fb) && (t.fillColorAnim = !0),
                o = 0,
                n = Number(r.s?.b);
                for (let t = 0; t < c; t++)
                    (a = d[t] ?? {}).anIndexes[e] = o,
                    (1 === n && "" !== a.val || 2 === n && "" !== a.val && " " !== a.val || 3 === n && (a.n || " " === a.val || t === c - 1) || 4 === n && (a.n || t === c - 1)) && (1 === Number(r.s?.rn) && D.push(o),
                    o++);
                let s = p.a?.[e]?.s;
                s && (s.totalChars = o);
                let i = -1, h;
                if (r.s?.rn === 1)
                    for (let t = 0; t < c; t++)
                        i !== Number((a = d[t] ?? {}).anIndexes[e]) && (i = a.anIndexes[e] ?? 0,
                        h = D.splice(Math.floor(Math.random() * D.length), 1)[0]),
                        h && (a.anIndexes[e] = h)
            }
            t.yOffset = t.finalLineHeight || 1.2 * t.finalSize,
            t.ls = t.ls || 0,
            t.ascent = Number(k.ascent) * t.finalSize / 100
        }
        copyData(t, e) {
            let s = Object.keys(e)
              , {length: i} = s;
            for (let r = 0; r < i; r++)
                Object.hasOwn(e, s[r] ?? "") && (t[s[r]] = e[s[r]]);
            return t
        }
        getExpressionValue(t, e) {
            throw Error(`${this.constructor.name}: Method getExpressionValue is not implemented`)
        }
        getKeyframeValue() {
            let t = this.data.d?.k ?? []
              , e = Number(this.elem.comp?.renderedFrame)
              , s = 0
              , i = t.length;
            for (; s <= i - 1 && !(s === i - 1 || Number(t[s + 1]?.t) > e); )
                s++;
            return this.keysIndex !== s && (this.keysIndex = s),
            this.data.d?.k[this.keysIndex]?.s
        }
        getValue(t) {
            if ((this.elem.globalData?.frameId === this.frameId || 0 === this.effectsSequence.length) && !t)
                return 0;
            let e = this.data.d?.k[this.keysIndex]?.s.t;
            void 0 !== e && (this.currentData.t = e);
            let s = this.currentData
              , i = this.keysIndex;
            if (this.lock)
                return this.setCurrentData(this.currentData),
                0;
            this.lock = !0,
            this._mdf = !1;
            let {length: r} = this.effectsSequence
              , a = t ?? this.data.d?.k[this.keysIndex]?.s;
            for (let t = 0; t < r; t++)
                a = i === this.keysIndex ? this.effectsSequence[t]?.(this.currentData, a?.t) : this.effectsSequence[t]?.(a, a?.t);
            return s !== a && this.setCurrentData(a),
            this.v = this.currentData,
            this.pv = this.v,
            this.lock = !1,
            this.frameId = this.elem.globalData?.frameId,
            0
        }
        recalculate(t) {
            if (!this.data.d)
                throw Error(`${this.constructor.name}: data.k (TextData -> DocumentData) is not implemented`);
            let e = this.data.d.k[t]?.s;
            e && (e.__complete = !1),
            this.keysIndex = 0,
            this._isFirstFrame = !0,
            this.getValue(e)
        }
        searchExpressions() {
            throw Error(`${this.constructor.name}: Method searchExpressions is not implemented`)
        }
        searchKeyframes() {
            if (!this.data.d)
                throw Error(`${this.constructor.name}: data.k (TextData -> DocumentData) is not implemented`);
            return this.kf = this.data.d.k.length > 1,
            this.kf && this.addEffect(this.getKeyframeValue.bind(this)),
            this.kf
        }
        searchProperty() {
            return this.searchKeyframes()
        }
        setCurrentData(t) {
            t.__complete || this.completeTextData(t),
            this.currentData = t,
            this.currentData.boxWidth = this.currentData.boxWidth || this.defaultBoxWidth,
            this._mdf = !0
        }
        setMinimumFontSize(t) {
            this.minimumFontSize = Math.floor(t) || 1,
            this.recalculate(this.keysIndex),
            this.elem.addDynamicProperty(this)
        }
        updateDocumentData(t, e) {
            if (!this.data.d)
                throw Error(`${this.constructor.name}: data.k (TextData -> DocumentData) is not implemented`);
            let s = e;
            s = s ?? this.keysIndex;
            let i = this.copyData({}, this.data.d.k[s]?.s ?? {});
            i = this.copyData(i, t),
            (this.data.d.k[s] ?? {
                s: null
            }).s = i,
            this.recalculate(s),
            this.setCurrentData(i),
            this.elem.addDynamicProperty(this)
        }
    }
    class sV extends eP {
        applyTextPropertiesToMatrix(t, e, s, i, r) {
            switch (t.ps && e.translate(t.ps[0], t.ps[1] + Number(t.ascent), 0),
            e.translate(0, -Number(t.ls), 0),
            t.j) {
            case 1:
                e.translate(Number(t.justifyOffset) + (Number(t.boxWidth) - Number(t.lineWidths[s])), 0, 0);
                break;
            case 2:
                e.translate(Number(t.justifyOffset) + (Number(t.boxWidth) - Number(t.lineWidths[s])) / 2, 0, 0)
            }
            e.translate(i, r, 0)
        }
        buildColor(t) {
            return `rgb(${Math.round(255 * t[0])},${Math.round(255 * t[1])},${Math.round(255 * t[2])})`
        }
        buildNewText() {
            throw Error(`${this.constructor.name}: Method buildNewText is not implemented`)
        }
        canResizeFont(t) {
            this.textProperty?.canResizeFont(t)
        }
        createPathShape(t, e) {
            let s, i = "", {length: r} = e;
            for (let a = 0; a < r; a++)
                e[a]?.ty === O.Path && e[a]?.ks?.k && (i += e3(s = e[a]?.ks?.k, s.i.length, !0, t));
            return i
        }
        initElement(t, e, s) {
            if (!t.t)
                throw Error(`${this.constructor.name}: data.t (LottieLayer -> TextData) can't be undefined`);
            this.emptyProp = new s_,
            this.lettersChangedFlag = !0,
            this.initFrame(),
            this.initBaseData(t, e, s),
            this.textProperty = new sO(this,t.t),
            this.textAnimator = new sC(t.t,this.renderType || N.SVG,this),
            this.initTransform(),
            this.initHierarchy(),
            this.initRenderable(),
            this.initRendererElement(),
            this.createContainerElements(),
            this.createRenderableComponents(),
            this.createContent(),
            this.hide(),
            this.textAnimator.searchProperties(this.dynamicProperties)
        }
        prepareFrame(t) {
            this._mdf = !1,
            this.prepareRenderableFrame(t),
            this.prepareProperties(t, this.isInRange)
        }
        setMinimumFontSize(t) {
            this.textProperty?.setMinimumFontSize(t)
        }
        updateDocumentData(t, e) {
            this.textProperty?.updateDocumentData(t, e)
        }
        validateText() {
            (this.textProperty?._mdf || this.textProperty?._isFirstFrame) && (this.buildNewText(),
            this.textProperty._isFirstFrame = !1,
            this.textProperty._mdf = !1)
        }
    }
    let sz = {
        shapes: []
    };
    class sR extends sV {
        constructor(t, e, s) {
            super(),
            this.createContainerElements = eK.prototype.createContainerElements,
            this.createRenderableComponents = eK.prototype.createRenderableComponents,
            this.destroyBaseElement = eK.prototype.destroyBaseElement,
            this.getBaseElement = eK.prototype.getBaseElement,
            this.getMatte = eK.prototype.getMatte,
            this.initRendererElement = eK.prototype.initRendererElement,
            this.renderedLetters = [],
            this.renderElement = eK.prototype.renderElement,
            this.setMatte = eK.prototype.setMatte,
            this.textSpans = [],
            this.renderType = N.SVG,
            this.initElement(t, e, s)
        }
        buildNewText() {
            let t, e;
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (!this.globalData)
                throw Error(`${this.constructor.name}: globalData is not implemented`);
            if (!this.layerElement)
                throw Error(`${this.constructor.name}: layerElement is not implemented`);
            if (!this.textProperty?.currentData)
                throw Error(`${this.constructor.name}: DocumentData is not implemented`);
            this.addDynamicProperty(this);
            let s = this.textProperty.currentData;
            this.renderedLetters = tG(s.l.length || 0),
            s.fc ? this.layerElement.setAttribute("fill", this.buildColor(s.fc)) : this.layerElement.setAttribute("fill", "rgba(0,0,0,0)"),
            s.sc && (this.layerElement.setAttribute("stroke", this.buildColor(s.sc)),
            this.layerElement.setAttribute("stroke-width", `${s.sw || 0}`)),
            this.layerElement.setAttribute("font-size", `${s.finalSize || 0}`);
            let i = this.globalData.fontManager?.getFontByName(s.f);
            if (i?.fClass)
                this.layerElement.classList.add(i.fClass);
            else {
                i?.fFamily && this.layerElement.setAttribute("font-family", i.fFamily);
                let {fWeight: t} = s
                  , {fStyle: e} = s;
                this.layerElement.setAttribute("font-style", e),
                this.layerElement.setAttribute("font-weight", t)
            }
            this.layerElement.ariaLabel = `${s.t}`;
            let r = s.l
              , a = !!this.globalData.fontManager?.chars;
            e = r.length;
            let n = null
              , o = this.mHelper
              , {singleShape: h} = this.data
              , l = 0
              , p = 0
              , m = !0
              , d = .001 * s.tr * Number(s.finalSize);
            if (!h || a || s.sz) {
                let c, u = this.textSpans.length;
                for (t = 0; t < e; t++) {
                    if (this.textSpans[t] = this.textSpans[t] ?? {
                        childSpan: null,
                        glyph: null,
                        span: null
                    },
                    !a || !h || 0 === t) {
                        if (!(n = (u > t ? this.textSpans[t]?.span : tA(a ? "g" : "text")) ?? null))
                            throw Error("Could not create tSpan");
                        if (u <= t) {
                            if (n.setAttribute("stroke-linecap", "butt"),
                            n.setAttribute("stroke-linejoin", "round"),
                            n.setAttribute("stroke-miterlimit", "4"),
                            this.textSpans[t] && (this.textSpans[t].span = n),
                            a) {
                                let e = tA("g");
                                n.appendChild(e),
                                this.textSpans[t] && (this.textSpans[t].childSpan = e)
                            }
                            this.textSpans[t] && (this.textSpans[t].span = n),
                            this.layerElement.appendChild(n)
                        }
                        n.style.display = "inherit"
                    }
                    if (o.reset(),
                    h && (r[t]?.n && (l = -d,
                    p += Number(s.yOffset),
                    p += +!!m,
                    m = !1),
                    this.applyTextPropertiesToMatrix(s, o, r[t]?.line ?? 0, l, p),
                    l += r[t]?.l || 0,
                    l += d),
                    a) {
                        let e;
                        if (c = this.globalData.fontManager?.getCharData(s.finalText[t] ?? "", i?.fStyle, this.globalData.fontManager.getFontByName(s.f).fFamily),
                        c?.t === 1)
                            e = new sW(c.data,this.globalData,this);
                        else {
                            let t = sz;
                            c?.data?.shapes && (t = this.buildShapeData(c.data, Number(s.finalSize))),
                            e = new sv(t,this.globalData,this)
                        }
                        if (this.textSpans[t]) {
                            let {glyph: s} = this.textSpans[t] ?? {};
                            s && (s.layerElement && this.textSpans[t]?.childSpan?.removeChild(s.layerElement),
                            s.destroy()),
                            this.textSpans[t].glyph = e
                        }
                        e._debug = !0,
                        e.prepareFrame(0),
                        e.renderFrame(),
                        e.layerElement && this.textSpans[t]?.childSpan?.appendChild(e.layerElement),
                        c?.t === 1 && this.textSpans[t]?.childSpan?.setAttribute("transform", `scale(${Number(s.finalSize) / 100},${Number(s.finalSize) / 100})`);
                        continue
                    }
                    n && (h && n.setAttribute("transform", `translate(${o.props[12]},${o.props[13]})`),
                    n.textContent = r[t]?.val ?? "",
                    n.style.whiteSpace = "preserve")
                }
                h && n?.setAttribute("d", "")
            } else {
                let i, r = this.textContainer;
                switch (s.j) {
                case 1:
                    i = "end";
                    break;
                case 2:
                    i = "middle";
                    break;
                default:
                    i = "start"
                }
                r?.setAttribute("text-anchor", i),
                r?.setAttribute("letter-spacing", `${d}`);
                let a = this.buildTextContents(s.finalText);
                for (t = 0,
                e = a.length,
                p = s.ps ? s.ps[1] + Number(s.ascent) : 0; t < e; t++)
                    (n = this.textSpans[t]?.span ?? tA("tspan")).textContent = a[t] ?? "",
                    n.setAttribute("x", "0"),
                    n.setAttribute("y", `${p}`),
                    n.style.display = "inherit",
                    r?.appendChild(n),
                    this.textSpans[t] = this.textSpans[t] ?? {
                        glyph: null,
                        span: null
                    },
                    this.textSpans[t].span = n,
                    p += Number(s.finalLineHeight);
                r && this.layerElement.appendChild(r)
            }
            let c = this.textSpans[t]?.span;
            if (c)
                for (; t < this.textSpans.length; )
                    c.style.display = "none",
                    t++;
            this._sizeChanged = !0
        }
        buildShapeData(t, e) {
            if (t.shapes.length > 0) {
                let s = t.shapes[0];
                if (s?.it) {
                    let t = s.it[s.it.length - 1];
                    t?.s && (t.s.k[0] = e,
                    t.s.k[1] = e)
                }
            }
            return t
        }
        buildTextContents(t) {
            let e = 0
              , {length: s} = t
              , i = []
              , r = "";
            for (; e < s; )
                "\r" === t[e] || "\x03" === t[e] ? (i.push(r),
                r = "") : r += t[e] ?? "",
                e++;
            return i.push(r),
            i
        }
        createContent() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (!this.globalData)
                throw Error(`${this.constructor.name}: globalData is not implemented`);
            this.data.singleShape && !this.globalData.fontManager?.chars && (this.textContainer = tA("text"))
        }
        getValue() {
            let t;
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            let {length: e} = this.textSpans;
            this.renderedFrame = this.comp?.renderedFrame;
            for (let s = 0; s < e; s++)
                (t = this.textSpans[s]?.glyph) && (t.prepareFrame(Number(this.comp?.renderedFrame) - this.data.st),
                t._mdf && (this._mdf = !0))
        }
        renderInnerContent() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (this.validateText(),
            !this.data.singleShape || this._mdf) {
                if (!this.textProperty)
                    throw Error(`${this.constructor.name}: textProperty is not initialized`);
                if (this.textAnimator?.getMeasures(this.textProperty.currentData, this.lettersChangedFlag),
                this.lettersChangedFlag || this.textAnimator?.lettersChangedFlag) {
                    let t, e, s;
                    this._sizeChanged = !0;
                    let i = this.textAnimator?.renderedLetters
                      , r = this.textProperty.currentData.l
                      , {length: a} = r;
                    for (let n = 0; n < a; n++)
                        !r[n]?.n && (t = i?.[n],
                        e = this.textSpans[n]?.span,
                        (s = this.textSpans[n]?.glyph) && s.renderFrame(),
                        t?._mdf.m && e?.setAttribute("transform", t.m),
                        t?._mdf.o && e?.setAttribute("opacity", `${t.o ?? 1}`),
                        t?._mdf.sw && e?.setAttribute("stroke-width", `${t.sw || 0}`),
                        t?._mdf.sc && e?.setAttribute("stroke", t.sc),
                        t?._mdf.fc && e?.setAttribute("fill", t.fc))
                }
            }
        }
        sourceRectAtTime() {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            if (!this.comp)
                throw Error(`${this.constructor.name}: comp (ElementInterface) is not implemented`);
            if (!this.layerElement)
                throw Error(`${this.constructor.name}: layerElement is not implemented`);
            if (this.prepareFrame(Number(this.comp.renderedFrame) - this.data.st),
            this.renderInnerContent(),
            this._sizeChanged) {
                this._sizeChanged = !1;
                let t = this.layerElement.getBBox();
                return this.bbox = {
                    height: t.height,
                    left: t.x,
                    top: t.y,
                    width: t.width
                },
                this.bbox
            }
            return null
        }
    }
    class sB extends ex {
        constructor(t, e, s) {
            super(),
            this.initFrame(),
            this.initRenderable(),
            this.assetData = e.getAssetData(t.refId) ?? null,
            this.initBaseData(t, e, s),
            this._isPlaying = !1,
            this._canPlay = !1;
            let i = this.globalData?.getAssetsPath(this.assetData);
            this.audio = this.globalData?.audioController?.createAudio(i),
            this._currentTime = 0,
            this.globalData?.audioController?.addAudio(this),
            this._volumeMultiplier = 1,
            this._volume = 1,
            this._previousVolume = null,
            this.tm = t.tm ? ea.getProp(this, t.tm, 0, e.frameRate, this) : {
                _placeholder: !0
            },
            this.lv = ea.getProp(this, t.au?.lv ?? {
                k: [100]
            }, 1, .01, this)
        }
        getBaseElement() {
            return null
        }
        hide() {
            this.audio.pause(),
            this._isPlaying = !1
        }
        initExpressions() {}
        pause() {
            this.audio.pause(),
            this._isPlaying = !1,
            this._canPlay = !1
        }
        prepareFrame(t) {
            if (!this.data)
                throw Error(`${this.constructor.name}: data (LottieLayer) is not implemented`);
            this.prepareRenderableFrame(t, !0),
            this.prepareProperties(t, !0),
            this.tm._placeholder ? this._currentTime = t / Number(this.data.sr) : this._currentTime = this.tm.v,
            this._volume = this.lv.v[0];
            let e = this._volume * Number(this._volumeMultiplier);
            this._previousVolume !== e && (this._previousVolume = e,
            this.audio.volume(e))
        }
        renderFrame(t) {
            if (this.isInRange && this._canPlay) {
                if (!this._isPlaying) {
                    this.audio.play(),
                    this.audio.seek(this._currentTime / Number(this.globalData?.frameRate)),
                    this._isPlaying = !0;
                    return
                }
                (!this.audio.playing() || Math.abs(this._currentTime / Number(this.globalData?.frameRate) - this.audio.seek()) > .1) && this.audio.seek(this._currentTime / Number(this.globalData?.frameRate))
            }
        }
        resume() {
            this._canPlay = !0
        }
        setRate(t) {
            this.audio.rate(t)
        }
        show() {}
        sourceRectAtTime() {
            return null
        }
        volume(t) {
            this._volumeMultiplier = t,
            this._previousVolume = t * this._volume,
            this.audio.volume(this._previousVolume)
        }
    }
    class sq extends ex {
        constructor(t, e, s) {
            if (super(),
            this.assetData = null,
            this.initFrame(),
            this.initRenderable(),
            t.refId && (this.assetData = e.getAssetData(t.refId) ?? null),
            !e.imageLoader)
                throw Error(`${this.constructor.name}: imageLoader is not implemented in globalData`);
            this.footageData = e.imageLoader.getAsset(this.assetData),
            this.initBaseData(t, e, s)
        }
        getBaseElement() {
            return null
        }
        getFootageData() {
            return this.footageData
        }
        initExpressions() {}
        prepareFrame() {}
        renderFrame() {}
    }
    class sj {
        constructor(t) {
            this.animationData = t
        }
        getProp(t) {
            let {sid: e} = t;
            return e && this.animationData.slots?.[e] ? Object.assign(t, this.animationData.slots[e].p) : t
        }
    }
    class sH extends eM {
        addPendingElement(t) {
            this.pendingElements.push(t)
        }
        buildAllItems() {
            let {length: t} = this.layers;
            for (let e = 0; e < t; e++)
                this.buildItem(e);
            this.checkPendingElements()
        }
        buildElementParenting(t, e, s=[]) {
            let {elements: i, layers: r} = this
              , {length: a} = r
              , n = 0;
            for (; n < a; ) {
                if (r[n]?.ind !== e) {
                    n++;
                    continue
                }
                if (!i[n] || !0 === i[n]) {
                    this.buildItem(n),
                    this.addPendingElement(t),
                    n++;
                    continue
                }
                if (s.push(i[n]),
                i[n].setAsParent(),
                r[n]?.parent === void 0) {
                    t.setHierarchy(s),
                    n++;
                    continue
                }
                this.buildElementParenting(t, r[n]?.parent, s),
                n++
            }
        }
        buildItem(t) {
            throw Error(`${this.constructor.name}: Method buildItem not yet implemented`)
        }
        checkLayers(t) {
            this.completeLayers = !0;
            let {length: e} = this.layers;
            for (let s = e - 1; s >= 0; s--) {
                let e = this.layers[s];
                !this.elements[s] && e && e.ip - e.st <= Number(t) - e.st && e.op - e.st > Number(t) - e.st && this.buildItem(s),
                this.completeLayers = !!this.elements[s] && this.completeLayers
            }
            this.checkPendingElements()
        }
        checkPendingElements() {
            throw Error(`${this.constructor.name}: Method checkPendingElements not yet implemented`)
        }
        createAudio(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            return new sB(t,this.globalData,this)
        }
        createCamera(t) {
            throw Error("You're using a 3d camera. Try the html renderer.")
        }
        createComp(t, e, s, i) {
            throw Error(`${this.constructor.name}: Method createComp not yet implemented`)
        }
        createFootage(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            return new sq(t,this.globalData,this)
        }
        createImage(t) {
            throw Error(`${this.constructor.name}: Method createImage is not implemented`)
        }
        createItem(t) {
            switch (t.ty) {
            case 2:
                return this.createImage(t);
            case 0:
                return this.createComp(t);
            case 1:
                return this.createSolid(t);
            case 3:
            default:
                return this.createNull(t);
            case 4:
                return this.createShape(t);
            case 5:
                return this.createText(t);
            case 6:
                return this.createAudio(t);
            case 13:
                return this.createCamera(t);
            case 15:
                return this.createFootage(t)
            }
        }
        createNull(t) {
            throw Error(`${this.constructor.name}: Method createNull not implemented`)
        }
        createShape(t) {
            throw Error(`${this.constructor.name}: Method createShape not implemented`)
        }
        createSolid(t) {
            throw Error(`${this.constructor.name}: Method createSolid not implemented`)
        }
        createText(t) {
            throw Error(`${this.constructor.name}: Method createText not implemented`)
        }
        getElementById(t) {
            let {length: e} = this.elements;
            for (let s = 0; s < e; s++)
                if (this.elements[s]?.data.ind === t)
                    return this.elements[s];
            return null
        }
        getElementByPath(t) {
            let e, s = t.shift();
            if ("number" == typeof s)
                e = this.elements[s];
            else {
                let {length: t} = this.elements;
                for (let i = 0; i < t; i++)
                    if (this.elements[i]?.data.nm === s) {
                        e = this.elements[i];
                        break
                    }
            }
            return 0 === t.length ? e : e?.getElementByPath(t)
        }
        includeLayers(t) {
            this.completeLayers = !1;
            let {length: e} = t
              , {length: s} = this.layers;
            for (let i = 0; i < e; i++) {
                let e = 0;
                for (; e < s; ) {
                    if (this.layers[e]?.id === t[i]?.id) {
                        this.layers[e] = t[i];
                        break
                    }
                    e++
                }
            }
        }
        initItems() {
            this.globalData?.progressiveLoad || this.buildAllItems()
        }
        prepareFrame(t) {
            throw Error(`${this.constructor.name}: Method prepareFrame not yet implemented`)
        }
        searchExtraCompositions(t) {
            let {length: e} = t;
            for (let s = 0; s < e; s++)
                if (t[s]?.xt) {
                    let e = this.createComp(t[s]);
                    e.initExpressions(),
                    this.globalData?.projectInterface.registerComposition(e)
                }
        }
        setProjectInterface(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: globalData is not implemented`);
            t && (this.globalData.projectInterface = t)
        }
        setupGlobalData(t, e) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: globalData is not implemented`);
            if (!this.animationItem)
                throw Error(`${this.constructor.name}: animationItem is not implemented`);
            this.globalData.fontManager = new sN,
            this.globalData.slotManager = new sj(t),
            this.globalData.fontManager.addChars(t.chars),
            this.globalData.fontManager.addFonts(t.fonts, e),
            this.globalData.getAssetData = this.animationItem.getAssetData.bind(this.animationItem),
            this.globalData.getAssetsPath = this.animationItem.getAssetsPath.bind(this.animationItem),
            this.globalData.imageLoader = this.animationItem.imagePreloader,
            this.globalData.audioController = this.animationItem.audioController,
            this.globalData.frameId = 0,
            this.globalData.frameRate = t.fr || 60,
            this.globalData.nm = t.nm,
            this.globalData.compSize = {
                h: t.h,
                w: t.w
            }
        }
        constructor(...t) {
            super(...t),
            this.currentFrame = 0,
            this.elements = [],
            this.layers = [],
            this.pendingElements = [],
            this.renderedFrame = -1
        }
    }
    class sG extends sH {
        appendElementInPos(t, e) {
            let s = t.getBaseElement();
            if (!s)
                return;
            let i = 0, r;
            for (; i < e; )
                !0 !== this.elements[i] && this.elements[i]?.getBaseElement() && (r = this.elements[i]?.getBaseElement()),
                i++;
            if (r)
                return void this.layerElement?.insertBefore(s, r);
            this.layerElement?.appendChild(s)
        }
        buildItem(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: globalData it not implemented`);
            let {elements: e, globalData: s, layers: i} = this;
            if (e[t] || i[t]?.ty === 99)
                return;
            e[t] = !0;
            let r = this.createItem(i[t]);
            if (e[t] = r,
            this.appendElementInPos(r, t),
            i[t]?.tt) {
                let s = "tp"in i[t] ? this.findIndexByInd(i[t].tp) : t - 1;
                if (-1 === s)
                    return;
                if (!e[s] || !0 === e[s]) {
                    this.buildItem(s),
                    this.addPendingElement(r);
                    return
                }
                let a = e[s].getMatte(i[t].tt);
                r.setMatte(a)
            }
        }
        checkPendingElements() {
            for (; this.pendingElements.length > 0; ) {
                let t = this.pendingElements.pop();
                if (t?.checkParenting(),
                t?.data.tt) {
                    let e = 0
                      , {length: s} = this.elements;
                    for (; e < s; ) {
                        if (this.elements[e] !== t) {
                            e++;
                            continue
                        }
                        let s = "tp"in t.data ? this.findIndexByInd(t.data.tp) : e - 1
                          , i = this.elements[s]?.getMatte(this.layers[e]?.tt) ?? "";
                        t.setMatte(i);
                        break
                    }
                }
            }
        }
        configAnimation(t) {
            try {
                if (!this.animationItem)
                    throw Error(`${this.constructor.name}: Can't access animationItem`);
                if (!this.globalData)
                    throw Error(`${this.constructor.name}: Can't access globalData`);
                if (!this.renderConfig)
                    throw Error(`${this.constructor.name}: Can't access renderConfig`);
                if (!this.svgElement)
                    throw Error(`${this.constructor.name}: Can't access svgElement`);
                this.svgElement.setAttribute("xmlns", K),
                this.renderConfig.viewBoxSize ? this.svgElement.setAttribute("viewBox", this.renderConfig.viewBoxSize) : this.svgElement.setAttribute("viewBox", `0 0 ${t.w} ${t.h}`),
                !this.renderConfig.viewBoxOnly && (this.svgElement.setAttribute("width", `${t.w}`),
                this.svgElement.setAttribute("height", `${t.h}`),
                this.svgElement.style.width = "100%",
                this.svgElement.style.height = "100%",
                this.svgElement.style.transform = "translate3d(0,0,0)",
                this.renderConfig.contentVisibility && (this.svgElement.style.contentVisibility = this.renderConfig.contentVisibility)),
                this.renderConfig.width && this.svgElement.setAttribute("width", `${this.renderConfig.width}`),
                this.renderConfig.height && this.svgElement.setAttribute("height", `${this.renderConfig.height}`),
                this.renderConfig.className && this.svgElement.classList.add(this.renderConfig.className),
                this.renderConfig.id && (this.svgElement.id = this.renderConfig.id),
                void 0 !== this.renderConfig.focusable && this.svgElement.setAttribute("focusable", `${this.renderConfig.focusable}`),
                this.renderConfig.preserveAspectRatio && this.svgElement.setAttribute("preserveAspectRatio", this.renderConfig.preserveAspectRatio),
                this.animationItem.wrapper?.appendChild(this.svgElement);
                let {defs: e} = this.globalData;
                this.setupGlobalData(t, e),
                this.globalData.progressiveLoad = this.renderConfig.progressiveLoad,
                this.data = t;
                let s = tA("clipPath")
                  , i = tA("rect");
                i.width.baseVal.value = t.w,
                i.height.baseVal.value = t.h,
                i.x.baseVal.value = 0,
                i.y.baseVal.value = 0;
                let r = W();
                s.id = r,
                s.appendChild(i),
                this.layerElement?.setAttribute("clip-path", `url(${eA()}#${r})`),
                e.appendChild(s),
                this.layers = t.layers,
                this.elements = tG(t.layers.length)
            } catch (t) {
                console.error(`${this.constructor.name}:
`, t)
            }
        }
        createImage(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            return new eQ(t,this.globalData,this)
        }
        createNull(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            return new e0(t,this.globalData,this)
        }
        createShape(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            return new sv(t,this.globalData,this)
        }
        createSolid(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            return new e1(t,this.globalData,this)
        }
        createText(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            return new sR(t,this.globalData,this)
        }
        destroy() {
            if (!this.animationItem)
                throw Error(`${this.constructor.name}: Can't access animationItem`);
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            this.animationItem.wrapper && (this.animationItem.wrapper.innerText = ""),
            this.layerElement = null,
            this.globalData.defs = null;
            let {length: t} = this.layers;
            for (let e = 0; e < t; e++)
                this.elements[e]?.destroy();
            this.elements.length = 0,
            this.destroyed = !0,
            this.animationItem = null
        }
        findIndexByInd(t) {
            let {length: e} = this.layers;
            for (let s = 0; s < e; s++)
                if (this.layers[s]?.ind === t)
                    return s;
            return -1
        }
        hide() {
            if (!this.layerElement)
                throw Error(`${this.constructor.name}: layerElement is not implemented`);
            this.layerElement.style.display = "none"
        }
        renderFrame(t) {
            try {
                if (!this.globalData || this.renderedFrame === t || this.destroyed)
                    return;
                let e = t;
                null === e ? e = this.renderedFrame : this.renderedFrame = Number(e),
                this.globalData.frameNum = e,
                this.globalData.frameId++,
                this.globalData.projectInterface.currentFrame = e || 0,
                this.globalData._mdf = !1;
                let {length: s} = this.layers;
                this.completeLayers || this.checkLayers(e);
                for (let t = s - 1; t >= 0; t--)
                    "boolean" != typeof this.elements[t] && (this.completeLayers || this.elements[t]) && this.elements[t]?.prepareFrame(Number(e) - (this.layers[t]?.st ?? 0));
                if (this.globalData._mdf)
                    for (let t = 0; t < s; t++)
                        "boolean" != typeof this.elements[t] && (this.completeLayers || this.elements[t]) && this.elements[t]?.renderFrame()
            } catch (t) {
                console.error(this.constructor.name, t)
            }
        }
        show() {
            if (!this.layerElement)
                throw Error(`${this.constructor.name}: layerElement is not implemented`);
            this.layerElement.style.display = "block"
        }
        updateContainerSize(t, e) {
            throw Error(`${this.constructor.name}: Method updateContainerSize is not implemented`)
        }
    }
    class sW extends eK {
        constructor(t, e, s) {
            super(),
            this.addPendingElement = sG.prototype.addPendingElement,
            this.appendElementInPos = sG.prototype.appendElementInPos,
            this.buildAllItems = sG.prototype.buildAllItems,
            this.buildElementParenting = sG.prototype.buildElementParenting,
            this.buildItem = sG.prototype.buildItem,
            this.checkLayers = sG.prototype.checkLayers,
            this.checkPendingElements = sG.prototype.checkPendingElements,
            this.configAnimation = sG.prototype.configAnimation,
            this.createAudio = sG.prototype.createAudio,
            this.createCamera = sG.prototype.createCamera,
            this.createFootage = sG.prototype.createFootage,
            this.createImage = sG.prototype.createImage,
            this.createItem = sG.prototype.createItem,
            this.createNull = sG.prototype.createNull,
            this.createShape = sG.prototype.createShape,
            this.createSolid = sG.prototype.createSolid,
            this.createText = sG.prototype.createText,
            this.currentFrame = 0,
            this.destroy = eC.prototype.destroy,
            this.destroyElements = eC.prototype.destroyElements,
            this.findIndexByInd = sG.prototype.findIndexByInd,
            this.getElementById = sG.prototype.getElementById,
            this.getElementByPath = sG.prototype.getElementByPath,
            this.getElements = eC.prototype.getElements,
            this.hide = eC.prototype.hide,
            this.includeLayers = sG.prototype.includeLayers,
            this.initElement = eC.prototype.initElement,
            this.initItems = sG.prototype.initItems,
            this.prepareFrame = eC.prototype.prepareFrame,
            this.renderedFrame = -1,
            this.renderFrame = eC.prototype.renderFrame,
            this.renderInnerContent = eC.prototype.renderInnerContent,
            this.searchExtraCompositions = sG.prototype.searchExtraCompositions,
            this.setElements = eC.prototype.setElements,
            this.setProjectInterface = sG.prototype.setProjectInterface,
            this.setupGlobalData = sG.prototype.setupGlobalData,
            this.show = eC.prototype.show,
            this.updateContainerSize = sG.prototype.updateContainerSize,
            this.layers = t.layers,
            this.supports3d = !0,
            this.completeLayers = !1,
            this.pendingElements = [],
            this.elements = this.layers ? tG(this.layers.length) : [],
            this.initElement(t, e, s),
            this.tm = t.tm ? ea.getProp(this, t.tm, 0, e.frameRate, this) : {
                _placeholder: !0
            }
        }
        createComp(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Cannot access global data`);
            return new sW(t,this.globalData,this)
        }
    }
    let sU = (t, e, s, i) => [-t + 3 * e - 3 * s + i, 3 * t - 6 * e + 3 * s, -3 * t + 3 * e, t]
      , sY = (t, e, s) => t * (1 - s) + e * s
      , sX = (t, e, s) => [sY(t[0], e[0], s), sY(t[1], e[1], s)]
      , sJ = (t, e, s) => {
        let i = t.boundingBox();
        return {
            bez: t,
            cx: i.cx,
            cy: i.cy,
            height: i.height,
            t: (e + s) / 2,
            t1: e,
            t2: s,
            width: i.width
        }
    }
      , sZ = t => {
        let e = t.bez.split(.5);
        if (!e[0] || !e[1])
            throw Error("Could not set PolynomialBezier");
        return [sJ(e[0], t.t1, t.t), sJ(e[1], t.t, t.t2)]
    }
      , sK = (t, e, s, i, r, a) => {
        if (!(2 * Math.abs(t.cx - e.cx) < t.width + e.width && 2 * Math.abs(t.cy - e.cy) < t.height + e.height))
            return;
        if (s >= a || t.width <= i && t.height <= i && e.width <= i && e.height <= i)
            return void r.push([t.t, e.t]);
        let n = sZ(t)
          , o = sZ(e);
        if (!n[0] || !n[1] || !o[0] || !o[1])
            throw Error("Could not set IntersectData");
        sK(n[0], o[0], s + 1, i, r, a),
        sK(n[0], o[1], s + 1, i, r, a),
        sK(n[1], o[0], s + 1, i, r, a),
        sK(n[1], o[1], s + 1, i, r, a)
    }
      , sQ = t => new s0(t,t,t,t,!1);
    class s0 {
        constructor(t, e, s, i, r) {
            let a = e
              , n = s;
            r && G(t, a) && (a = sX(t, i, 1 / 3)),
            r && G(n, i) && (n = sX(t, i, 2 / 3));
            let o = sU(t[0], a[0], n[0], i[0])
              , h = sU(t[1], a[1], n[1], i[1]);
            this.a = [o[0], h[0]],
            this.b = [o[1], h[1]],
            this.c = [o[2], h[2]],
            this.d = [o[3], h[3]],
            this.points = [t, a, n, i]
        }
        boundingBox() {
            let t = this.bounds();
            return {
                bottom: t.y.max,
                cx: (t.x.max + t.x.min) / 2,
                cy: (t.y.max + t.y.min) / 2,
                height: t.y.max - t.y.min,
                left: t.x.min,
                right: t.x.max,
                top: t.y.min,
                width: t.x.max - t.x.min
            }
        }
        bounds() {
            return {
                x: this._extrema(this, 0),
                y: this._extrema(this, 1)
            }
        }
        derivative(t) {
            return [(3 * t * this.a[0] + 2 * this.b[0]) * t + this.c[0], (3 * t * this.a[1] + 2 * this.b[1]) * t + this.c[1]]
        }
        inflectionPoints() {
            let t = this.a[1] * this.b[0] - this.a[0] * this.b[1];
            if (H(t))
                return [];
            let e = -.5 * (this.a[1] * this.c[0] - this.a[0] * this.c[1]) / t
              , s = e * e - 1 / 3 * (this.b[1] * this.c[0] - this.b[0] * this.c[1]) / t;
            if (s < 0)
                return [];
            let i = Math.sqrt(s);
            return H(i) ? i > 0 && i < 1 ? [e] : [] : [e - i, e + i].filter(t => t > 0 && t < 1)
        }
        intersections(t, e, s) {
            let i = e
              , r = s;
            i = i ?? 2,
            r = r ?? 7;
            let a = [];
            return sK(sJ(this, 0, 1), sJ(t, 0, 1), 0, i, a, r),
            a
        }
        normalAngle(t) {
            let e = this.derivative(t);
            return Math.atan2(e[0] ?? 0, e[1] ?? 0)
        }
        point(t) {
            return [((this.a[0] * t + this.b[0]) * t + this.c[0]) * t + this.d[0], ((this.a[1] * t + this.b[1]) * t + this.c[1]) * t + this.d[1]]
        }
        split(t) {
            if (t <= 0)
                return [sQ(this.points[0]), this];
            if (t >= 1)
                return [this, sQ(this.points[this.points.length - 1])];
            let e = sX(this.points[0], this.points[1], t)
              , s = sX(this.points[1], this.points[2], t)
              , i = sX(this.points[2], this.points[3], t)
              , r = sX(e, s, t)
              , a = sX(s, i, t)
              , n = sX(r, a, t);
            return [new s0(this.points[0],e,r,n,!0), new s0(n,a,i,this.points[3],!0)]
        }
        tangentAngle(t) {
            let e = this.derivative(t);
            return Math.atan2(e[1] ?? 0, e[0] ?? 0)
        }
        _extrema(t, e) {
            let s = t.points[0][e] ?? 0
              , i = t.points[t.points.length - 1]?.[e] ?? 0;
            if (s > i) {
                let t = i;
                i = s,
                s = t
            }
            let r = ( (t, e, s) => {
                if (0 === t)
                    return [];
                let i = e * e - 4 * t * s;
                if (i < 0)
                    return [];
                let r = -e / (2 * t);
                if (0 === i)
                    return [r];
                let a = Math.sqrt(i) / (2 * t);
                return [r - a, r + a]
            }
            )(3 * (t.a[e] ?? 0), 2 * (t.b[e] ?? 0), t.c[e] ?? 0)
              , {length: a} = r;
            for (let n = 0; n < a; n++) {
                if ((r[n] ?? 0) <= 0 || (r[n] ?? 0) >= 1)
                    continue;
                let a = t.point(r[n] ?? 0)[e] ?? 0;
                if (a < s) {
                    s = a;
                    continue
                }
                a > i && (i = a)
            }
            return {
                max: i,
                min: s
            }
        }
    }
    function s1(t, e) {
        let s = (e + 1) % t.length();
        return new s0(t.v[e],t.o[e],t.i[s],t.v[s],!0)
    }
    class s2 extends t8 {
        addShape(t) {
            if (this.closed)
                return;
            t.sh?.container?.addDynamicProperty(t.sh);
            let e = {
                data: t,
                localShapeCollection: eO(),
                shape: t.sh
            };
            this.shapes.push(e),
            this.addShapeToModifier(e),
            this._isAnimated && t.setAsAnimated()
        }
        addShapeToModifier(t) {}
        init(t, e, s, i) {
            this.shapes = [],
            this.elem = t,
            this.initDynamicPropertyContainer(t),
            this.initModifierProperties(t, e),
            this.frameId = -999999,
            this.closed = !1,
            this.k = !1,
            this.dynamicProperties.length > 0 ? this.k = !0 : this.getValue(!0)
        }
        initModifierProperties(t, e) {
            throw Error(`${this.constructor.name}: Method initModifierProperties is not implemented`)
        }
        isAnimatedWithShape(t) {
            throw Error(`${this.constructor.name}: Method isAnimatedWithShape is not implemented`)
        }
        processKeys() {
            return this.elem?.globalData?.frameId === this.frameId || (this.frameId = this.elem?.globalData?.frameId,
            this.iterateDynamicProperties()),
            0
        }
        constructor(...t) {
            super(...t),
            this.shapes = []
        }
    }
    let s3 = (t, e) => [t[1] ?? 0 * (e[2] ?? 0) - (t[2] ?? 0) * (e[1] ?? 0), t[2] ?? 0 * (e[0] ?? 0) - (t[0] ?? 0) * (e[2] ?? 0), t[0] ?? 0 * (e[1] ?? 0) - (t[1] ?? 0) * (e[0] ?? 0)]
      , s5 = (t, e, s) => [t[0] + Math.cos(e) * s, t[1] - Math.sin(e) * s]
      , s4 = (t, e, s) => {
        let i = Math.atan2(e[0] - t[0], e[1] - t[1]);
        return [s5(t, i, s), s5(e, i, s)]
    }
      , s6 = (t, e, s, i) => {
        let r = [t[0], t[1], 1]
          , a = [e[0], e[1], 1]
          , n = [s[0], s[1], 1]
          , o = [i[0], i[1], 1]
          , h = s3(s3(r, a), s3(n, o));
        return H(h[2] ?? 0) ? null : [h[0] ?? 0 / (h[2] ?? 0), h[1] ?? 0 / (h[2] ?? 0)]
    }
      , s8 = (t, e) => Math.hypot(t[0] - e[0], t[1] - e[1])
      , s9 = (t, e, s, i, r) => {
        let a = e.points[3]
          , n = s.points[0];
        if (3 === i || G(a, n))
            return a;
        if (2 === i) {
            let i = -e.tangentAngle(1)
              , r = -s.tangentAngle(0) + Math.PI
              , o = s6(a, s5(a, i + Math.PI / 2, 100), n, s5(n, i + Math.PI / 2, 100))
              , h = o ? s8(o, a) : s8(a, n) / 2
              , l = s5(a, i, 2 * h * .5519);
            return t.setXYAt(l[0], l[1], "o", t.length() - 1),
            l = s5(n, r, 2 * h * .5519),
            t.setTripleAt(n[0], n[1], n[0], n[1], l[0], l[1], t.length()),
            n
        }
        let o = G(a, e.points[2]) ? e.points[0] : e.points[2]
          , h = G(n, s.points[1]) ? s.points[3] : s.points[1]
          , l = s6(o, a, n, h);
        return l && s8(l, a) < r ? (t.setTripleAt(l[0], l[1], l[0], l[1], l[0], l[1], t.length()),
        l) : a
    }
      , s7 = (t, e) => {
        let s, i = (s = s4(t.points[0], t.points[1], e))[0], r = s[1], a = (s = s4(t.points[1], t.points[2], e))[0], n = s[1], o = (s = s4(t.points[2], t.points[3], e))[0], h = s[1], l = s6(i, r, a, n);
        l = l ?? r;
        let p = s6(o, h, a, n);
        return new s0(i,l,p = p ?? o,h)
    }
      , it = (t, e) => {
        let s, i, r, a = t.inflectionPoints();
        if (0 === a.length)
            return [s7(t, e)];
        if (1 === a.length || j(a[1] ?? 0, 1))
            return s = (r = t.split(a[0] ?? 0))[0],
            i = r[1],
            [s7(s, e), s7(i, e)];
        s = (r = t.split(a[0] ?? 0))[0];
        let n = (a[1] ?? 0 - (a[0] ?? 0)) / (1 - (a[0] ?? 0))
          , o = (r = r[1]?.split(n))[0];
        return i = r[1],
        [s7(s, e), s7(o, e), s7(i, e)]
    }
      , ie = (t, e) => {
        let s = t.intersections(e);
        return (s.length > 0 && j(s[0]?.[0] ?? 0, 1) && s.shift(),
        s.length > 0) ? s[0] : null
    }
      , is = (t, e) => {
        let s = [...t]
          , i = [...e]
          , r = ie(t[t.length - 1], e[0]);
        return (r && (s[t.length - 1] = t[t.length - 1]?.split(r[0] ?? 0)[0],
        i[0] = e[0]?.split(r[1] ?? 0)[1]),
        t.length > 1 && e.length > 1 && (r = ie(t[0], e[e.length - 1]))) ? [[t[0]?.split(r[0] ?? 0)[0]], [e[e.length - 1]?.split(r[1] ?? 0)[1]]] : [s, i]
    }
      , ii = (t, e, s, i, r, a, n) => {
        let o = s - Math.PI / 2
          , h = s + Math.PI / 2
          , l = e[0] + Math.cos(s) * i * r
          , p = e[1] - Math.sin(s) * i * r;
        t.setTripleAt(l, p, l + Math.cos(o) * a, p - Math.sin(o) * a, l + Math.cos(h) * n, p - Math.sin(h) * n, t.length())
    }
      , ir = (t, e, s, i, r, a, n) => {
        let o = ( (t, e) => {
            let s = 0 === e ? t.length() - 1 : e - 1
              , i = (e + 1) % t.length()
              , r = ( (t, e) => {
                let s = [e[0] - t[0], e[1] - t[1]]
                  , i = -(.5 * Math.PI);
                return [Math.cos(i) * (s[0] ?? 0) - Math.sin(i) * (s[1] ?? 0), Math.sin(i) * (s[0] ?? 0) + Math.cos(i) * (s[1] ?? 0)]
            }
            )(t.v[s], t.v[i]);
            return Math.atan2(0, 1) - Math.atan2(r[1], r[0])
        }
        )(e, s)
          , h = e.v[s % e._length]
          , l = e.v[0 === s ? e._length - 1 : s - 1]
          , p = e.v[(s + 1) % e._length]
          , m = 2 === a ? Math.sqrt(Math.pow(h[0] - l[0], 2) + Math.pow(h[1] - l[1], 2)) : 0
          , d = 2 === a ? Math.sqrt(Math.pow(h[0] - p[0], 2) + Math.pow(h[1] - p[1], 2)) : 0;
        ii(t, e.v[s % e._length] ?? [0, 0], o, n, i, d / ((r + 1) * 2), m / ((r + 1) * 2))
    }
      , ia = (t, e, s, i, r, a) => {
        let n = a;
        for (let a = 0; a < i; a++) {
            let o = (a + 1) / (i + 1)
              , h = 2 === r ? Math.sqrt(Math.pow(e.points[3][0] - e.points[0][0], 2) + Math.pow(e.points[3][1] - e.points[0][1], 2)) : 0
              , l = e.normalAngle(o);
            ii(t, e.point(o), l, n, s, h / ((i + 1) * 2), h / ((i + 1) * 2)),
            n = -n
        }
        return n
    }
      , io = function(t) {
        try {
            let e = new tI;
            return tq(e, null),
            e.setParams(t),
            e
        } catch (t) {
            throw console.error("AnimationManager:\n", t),
            Error("Could not load animation")
        }
    }
      , ih = setInterval( () => {
        Q || "complete" === document.readyState && (clearInterval(ih),
        function() {
            if (Q)
                return;
            let t = [...document.getElementsByClassName("lottie"), ...document.getElementsByClassName("bodymovin")]
              , {length: e} = t;
            for (let s = 0; s < e; s++) {
                let e = t[s];
                e && function(t, e) {
                    try {
                        if (!t)
                            return null;
                        let s = 0;
                        for (; s < t$; ) {
                            if (tO[s]?.elem === t && tO[s]?.elem !== null)
                                return tO[s]?.animation;
                            s++
                        }
                        let i = new tI;
                        tq(i, t),
                        i.setData(t, e)
                    } catch (t) {
                        throw console.error("AnimationManager:\n", t),
                        Error("Could not register animation")
                    }
                }(e, void 0)
            }
        }())
    }
    , 100);
    g = N.SVG,
    y = class extends sG {
        constructor(t, e) {
            super(),
            this.animationItem = t,
            this.layers = [],
            this.renderedFrame = -1,
            this.svgElement = tA("svg");
            let s = "";
            if (e?.title) {
                let t = tA("title")
                  , i = W();
                t.id = i,
                t.textContent = e.title,
                this.svgElement.appendChild(t),
                s += i
            }
            if (e?.description) {
                let t = tA("desc")
                  , i = W();
                t.id = i,
                t.textContent = e.description,
                this.svgElement.appendChild(t),
                s += ` ${i}`
            }
            s && this.svgElement.setAttribute("aria-labelledby", s);
            let i = tA("defs");
            this.svgElement.appendChild(i);
            let r = tA("g");
            this.svgElement.appendChild(r),
            this.layerElement = r,
            this.renderConfig = {
                className: e?.className || "",
                contentVisibility: e?.contentVisibility || "visible",
                filterSize: {
                    height: e?.filterSize?.height || "100%",
                    width: e?.filterSize?.width || "100%",
                    x: e?.filterSize?.x || "0%",
                    y: e?.filterSize?.y || "0%"
                },
                focusable: e?.focusable,
                height: e?.height,
                hideOnTransparent: e?.hideOnTransparent !== !1,
                id: e?.id || "",
                imagePreserveAspectRatio: e?.imagePreserveAspectRatio || "xMidYMid slice",
                preserveAspectRatio: e?.preserveAspectRatio || "xMidYMid meet",
                progressiveLoad: e?.progressiveLoad || !1,
                runExpressions: !e || void 0 === e.runExpressions || e.runExpressions,
                viewBoxOnly: e?.viewBoxOnly || !1,
                viewBoxSize: e?.viewBoxSize || !1,
                width: e?.width
            },
            this.globalData = {
                _mdf: !1,
                defs: i,
                frameNum: -1,
                frameRate: 60,
                renderConfig: this.renderConfig
            },
            this.elements = [],
            this.pendingElements = [],
            this.destroyed = !1,
            this.rendererType = N.SVG
        }
        createComp(t) {
            if (!this.globalData)
                throw Error(`${this.constructor.name}: Can't access globalData`);
            return new sW(t,this.globalData,this)
        }
    }
    ,
    q[g] = y,
    sb($.TrimModifier, class extends s2 {
        addPaths(t, e) {
            let {length: s} = t;
            for (let i = 0; i < s; i++)
                e.addShape(t[i])
        }
        addSegment(t, e, s, i, r, a, n) {
            r.setXYAt(e[0], e[1], "o", a),
            r.setXYAt(s[0], s[1], "i", a + 1),
            n && r.setXYAt(t[0], t[1], "v", a),
            r.setXYAt(i[0], i[1], "v", a + 1)
        }
        addSegmentFromArray(t, e, s, i) {
            e.setXYAt(t[1] ?? 0, t[5] ?? 0, "o", s),
            e.setXYAt(t[2] ?? 0, t[6] ?? 0, "i", s + 1),
            i && e.setXYAt(t[0] ?? 0, t[4] ?? 0, "v", s),
            e.setXYAt(t[3] ?? 0, t[7] ?? 0, "v", s + 1)
        }
        addShapes(t, e, s) {
            let i = s, {pathsData: r=[], shape: a} = t, n = a?.paths?.shapes ?? [], o = a?.paths?._length || 0, h, l, p = 0, m, d, c, u = [], f, g = !0;
            i ? (d = i._length,
            f = i._length) : (i = eL(),
            d = 0,
            f = 0),
            u.push(i);
            for (let t = 0; t < o; t++) {
                let {lengths: s} = r[t] ?? {
                    lengths: []
                };
                for (h = 1,
                i.c = !!n[t]?.c,
                l = n[t]?.c ? s.length : s.length + 1; h < l; h++) {
                    if (p + (m = s[h - 1]).addedLength < e.s) {
                        p += m.addedLength,
                        i.c = !1;
                        continue
                    }
                    if (p > e.e) {
                        i.c = !1;
                        break
                    }
                    e.s <= p && e.e >= p + m.addedLength ? (this.addSegment(n[t]?.v[h - 1], n[t]?.o[h - 1], n[t]?.i[h], n[t]?.v[h], i, d, g),
                    g = !1) : (c = tZ(n[t]?.v[h - 1], n[t]?.v[h], n[t]?.o[h - 1], n[t]?.i[h], (e.s - p) / m.addedLength, (e.e - p) / m.addedLength, s[h - 1]),
                    this.addSegmentFromArray(c, i, d, g),
                    g = !1,
                    i.c = !1),
                    p += m.addedLength,
                    d++
                }
                if (n[t]?.c && s.length > 0) {
                    if (m = s[h - 1],
                    p <= e.e) {
                        let r = s[h - 1]?.addedLength ?? 0;
                        e.s <= p && e.e >= p + r ? (this.addSegment(n[t]?.v[h - 1], n[t]?.o[h - 1], n[t]?.i[0], n[t]?.v[0], i, d, g),
                        g = !1) : (c = tZ(n[t]?.v[h - 1], n[t]?.v[0], n[t]?.o[h - 1], n[t]?.i[0], (e.s - p) / r, (e.e - p) / r, s[h - 1]),
                        this.addSegmentFromArray(c, i, d, g),
                        g = !1,
                        i.c = !1)
                    } else
                        i.c = !1;
                    p += m?.addedLength ?? 0,
                    d++
                }
                if (i._length && (i.setXYAt(i.v[f]?.[0] ?? 0, i.v[f]?.[1] ?? 0, "i", f),
                i.setXYAt(i.v[i._length - 1]?.[0] ?? 0, i.v[i._length - 1]?.[1] ?? 0, "o", i._length - 1)),
                p > e.e)
                    break;
                t < o - 1 && (i = eL(),
                g = !0,
                u.push(i),
                d = 0)
            }
            return u
        }
        addShapeToModifier(t) {
            t.pathsData = []
        }
        calculateShapeEdges(t, e, s, i, r) {
            let a = [];
            e <= 1 ? a.push({
                e,
                s: t
            }) : t >= 1 ? a.push({
                e: e - 1,
                s: t - 1
            }) : a.push({
                e: 1,
                s: t
            }, {
                e: e - 1,
                s: 0
            });
            let n = []
              , {length: o} = a;
            for (let t = 0; t < o; t++)
                if (!((a[t]?.e ?? 0) * r < i || (a[t]?.s ?? 0) * r > i + s)) {
                    let e, o;
                    e = a[t]?.s ?? 0 * r <= i ? 0 : (a[t]?.s ?? 0 * r - i) / s,
                    o = a[t]?.e ?? 0 * r >= i + s ? 1 : (a[t]?.e ?? 0 * r - i) / s,
                    n.push([e, o])
                }
            return 0 === n.length && n.push([0, 0]),
            n
        }
        initModifierProperties(t, e) {
            this.s = ea.getProp(t, e.s, 0, .01, this),
            this.e = ea.getProp(t, e.e, 0, .01, this),
            this.o = ea.getProp(t, e.o, 0, 0, this),
            this.sValue = 0,
            this.eValue = 0,
            this.getValue = this.processKeys,
            this.m = e.m,
            this._isAnimated = this.s.effectsSequence.length > 0 || this.e.effectsSequence.length > 0 || this.o.effectsSequence.length > 0
        }
        processShapes(t) {
            let e, s;
            if (this._mdf || t) {
                let t = Number(this.o?.v) % 360 / 360;
                if (t < 0 && t++,
                (e = Number(this.s?.v) > 1 ? 1 + t : 0 > Number(this.s?.v) ? 0 + t : Number(this.s?.v) + t) > (s = Number(this.e?.v) > 1 ? 1 + t : 0 > Number(this.e?.v) ? 0 + t : Number(this.e?.v) + t)) {
                    let t = e;
                    e = s,
                    s = t
                }
                e = 1e-4 * Math.round(1e4 * e),
                s = 1e-4 * Math.round(1e4 * s),
                this.sValue = e,
                this.eValue = s
            } else
                e = this.sValue || 0,
                s = this.eValue || 0;
            let i, r, a, n, o, h, l = 0, {length: p} = this.shapes;
            if (s === e)
                for (let t = 0; t < p; t++) {
                    this.shapes[t]?.localShapeCollection?.releaseShapes();
                    let {shape: e} = this.shapes[t] ?? {};
                    e && (e._mdf = !0,
                    e.paths = this.shapes[t]?.localShapeCollection),
                    this._mdf && (this.shapes[t].pathsData.length = 0)
                }
            else if ((1 !== s || 0 !== e) && (0 !== s || 1 !== e)) {
                let m, d, c = [];
                for (let e = 0; e < p; e++)
                    if (m = this.shapes[e],
                    m.shape?._mdf || this._mdf || t || 2 === this.m) {
                        if (i = m.shape?.paths,
                        a = i?._length || 0,
                        h = 0,
                        !m.shape?._mdf && m.pathsData?.length)
                            h = m.totalShapeLength;
                        else {
                            for (r = 0,
                            n = this.releasePathsData(m.pathsData); r < a; r++)
                                i && (o = function(t) {
                                    let e, s = tY.newElement(), i = t.c, r = t.v, a = t.o, n = t.i, o = t._length || 0, {lengths: h} = s, l = 0;
                                    for (e = 0; e < o - 1; e++)
                                        h[e] = t0(r[e], r[e + 1], a[e], n[e + 1]),
                                        l += h[e]?.addedLength ?? 0;
                                    return i && o && (h[e] = t0(r[e], r[0], a[e], n[0]),
                                    l += h[e]?.addedLength ?? 0),
                                    s.totalLength = l,
                                    s
                                }(i.shapes[r]),
                                n.push(o),
                                h += o.totalLength);
                            m.totalShapeLength = h,
                            m.pathsData = n
                        }
                        l += Number(h),
                        m.shape && (m.shape._mdf = !0)
                    } else
                        m.shape && (m.shape.paths = m.localShapeCollection);
                let u = e, f = s, g = 0, y;
                for (let t = p - 1; t >= 0; t--)
                    if (m = this.shapes[t],
                    m.shape?._mdf) {
                        if (!(d = m.localShapeCollection))
                            throw Error(`${this.constructor.name}: Could not set localShapeCollection`);
                        for (d.releaseShapes(),
                        2 === this.m && p > 1 ? (y = this.calculateShapeEdges(e, s, m.totalShapeLength || 0, g, l),
                        g += Number(m.totalShapeLength)) : y = [[u, f]],
                        a = y.length,
                        r = 0; r < a; r++) {
                            u = y[r]?.[0] ?? 0,
                            f = y[r]?.[1] ?? 0,
                            c.length = 0,
                            f <= 1 ? c.push({
                                e: Number(m.totalShapeLength) * f,
                                s: Number(m.totalShapeLength) * u
                            }) : u >= 1 ? c.push({
                                e: Number(m.totalShapeLength) * (f - 1),
                                s: Number(m.totalShapeLength) * (u - 1)
                            }) : c.push({
                                e: Number(m.totalShapeLength),
                                s: Number(m.totalShapeLength) * u
                            }, {
                                e: Number(m.totalShapeLength) * (f - 1),
                                s: 0
                            });
                            let t = {
                                e: 0,
                                s: 0
                            }
                              , e = this.addShapes(m, c[0] ?? t);
                            if (c[0]?.s !== c[0]?.e) {
                                if (c.length > 1) {
                                    let s = m.shape.paths?.shapes[m.shape.paths._length - 1];
                                    if (s?.c) {
                                        let s = e.pop();
                                        this.addPaths(e, d),
                                        e = this.addShapes(m, c[1] ?? t, s)
                                    } else
                                        this.addPaths(e, d),
                                        e = this.addShapes(m, c[1] ?? t)
                                }
                                this.addPaths(e, d)
                            }
                        }
                        m.shape.paths = d
                    }
            } else if (this._mdf)
                for (let t = 0; t < p; t++) {
                    this.shapes[t].pathsData.length = 0;
                    let {shape: e} = this.shapes[t] ?? {};
                    e && (e._mdf = !0)
                }
        }
        releasePathsData(t) {
            let {length: e} = t;
            for (let s = 0; s < e; s++)
                tY.release(t[s]);
            return t.length = 0,
            t
        }
    }
    ),
    sb($.PuckerAndBloatModifier, class extends s2 {
        initModifierProperties(t, e) {
            this.getValue = this.processKeys,
            this.amount = ea.getProp(t, e.a, 0, null, this),
            this._isAnimated = this.amount.effectsSequence.length > 0
        }
        processPath(t, e) {
            let s, i, r, a, n, o, h, l = e / 100, p = [0, 0], m = t._length;
            for (s = 0; s < m; s++)
                p[0] += t.v[s]?.[0] ?? 0,
                p[1] += t.v[s]?.[1] ?? 0;
            p[0] /= m,
            p[1] /= m;
            let d = eL();
            for (s = 0,
            d.c = t.c; s < m; s++)
                i = t.v[s]?.[0] ?? 0 + (p[0] ?? 0 - (t.v[s]?.[0] ?? 0)) * l,
                r = t.v[s]?.[1] ?? 0 + (p[1] ?? 0 - (t.v[s]?.[1] ?? 0)) * l,
                a = t.o[s]?.[0] ?? 0 + -((p[0] ?? 0 - (t.o[s]?.[0] ?? 0)) * l),
                n = t.o[s]?.[1] ?? 0 + -((p[1] ?? 0 - (t.o[s]?.[1] ?? 0)) * l),
                o = t.i[s]?.[0] ?? 0 + -((p[0] ?? 0 - (t.i[s]?.[0] ?? 0)) * l),
                h = t.i[s]?.[1] ?? 0 + -((p[1] ?? 0 - (t.i[s]?.[1] ?? 0)) * l),
                d.setTripleAt(i, r, a, n, o, h, s);
            return d
        }
        processShapes(t) {
            let {length: e} = this.shapes
              , s = this.amount?.v;
            if (0 !== s) {
                let i, r, a;
                for (let n = 0; n < e; n++) {
                    if (a = (r = this.shapes[n]).localShapeCollection,
                    !(!r.shape?._mdf && !this._mdf && !t)) {
                        a?.releaseShapes(),
                        r.shape && (r.shape._mdf = !0),
                        i = r.shape?.paths?.shapes;
                        let t = r.shape?.paths?._length || 0;
                        for (let e = 0; e < t; e++) {
                            let t = i?.[e];
                            t && a?.addShape(this.processPath(t, s || 0))
                        }
                    }
                    r.localShapeCollection && r.shape && (r.shape.paths = r.localShapeCollection)
                }
            }
            0 === this.dynamicProperties.length && (this._mdf = !1)
        }
    }
    ),
    sb($.RepeaterModifier, class extends s2 {
        addShapeToModifier(t) {
            t.pathsData = []
        }
        applyTransforms(t, e, s, i, r, a) {
            if (!i.s || !i.p || !i.a || !i.r)
                throw Error(`${this.constructor.name}: Missing required data from Transform`);
            let n = a ? -1 : 1
              , o = i.s.v[0] + (1 - i.s.v[0]) * (1 - r)
              , h = i.s.v[1] + (1 - i.s.v[1]) * (1 - r);
            t.translate(i.p.v[0] * n * r, i.p.v[1] * n * r, i.p.v[2]),
            e.translate(-i.a.v[0], -i.a.v[1], i.a.v[2]),
            e.rotate(-i.r.v * n * r),
            e.translate(i.a.v[0] ?? 0, i.a.v[1] ?? 0, i.a.v[2]),
            s.translate(-(i.a.v[0] ?? 0), -(i.a.v[1] ?? 0), i.a.v[2]),
            s.scale(a ? 1 / o : o, a ? 1 / h : h),
            s.translate(i.a.v[0] ?? 0, i.a.v[1] ?? 0, i.a.v[2])
        }
        changeGroupRender(t, e) {
            let {length: s} = t;
            for (let i = 0; i < s; i++)
                t[i]._shouldRender = e,
                t[i]?.ty === O.Group && this.changeGroupRender(t[i]?.it, e)
        }
        cloneElements(t) {
            let e = JSON.parse(JSON.stringify(t));
            return this.resetElements(e),
            e
        }
        init(t, e, s, i=[]) {
            if (!U(e))
                throw TypeError(`${this.constructor.name}: Method init, param arr must be array`);
            let r = Number(s);
            for (this.elem = t,
            this.arr = e,
            this.pos = r,
            this.elemsData = i,
            this._currentCopies = 0,
            this._elements = [],
            this._groups = [],
            this.frameId = -1,
            this.initDynamicPropertyContainer(t),
            this.initModifierProperties(t, e[r]); r > 0; )
                r--,
                this._elements.unshift(e[r]);
            if (this.dynamicProperties.length > 0) {
                this.k = !0;
                return
            }
            this.getValue(!0)
        }
        initModifierProperties(t, e) {
            this.getValue = this.processKeys,
            this.c = ea.getProp(t, e.c, 0, null, this),
            this.o = ea.getProp(t, e.o, 0, null, this),
            e.tr && (this.tr = eS(t, e.tr, this),
            this.so = ea.getProp(t, e.tr.so, 0, .01, this),
            this.eo = ea.getProp(t, e.tr.eo, 0, .01, this)),
            this.data = e,
            0 === this.dynamicProperties.length && this.getValue(!0),
            this._isAnimated = this.dynamicProperties.length > 0,
            this.pMatrix = new e_,
            this.rMatrix = new e_,
            this.sMatrix = new e_,
            this.tMatrix = new e_,
            this.matrix = new e_
        }
        processShapes(t) {
            let e, s;
            if (!this.data)
                throw Error(`${this.constructor.name}: data (Shape) is not implemented`);
            let i, r, a, n, o, h = !1;
            if (!this._mdf && !t) {
                for (o = Number(this._currentCopies),
                a = 0,
                n = 1; o; )
                    (i = this.elemsData[a]?.it ?? [])[i.length - 1].transform.mProps._mdf = !1,
                    i[i.length - 1].transform.op._mdf = !1,
                    o--,
                    a += n;
                return h
            }
            let l = Math.ceil(Number(this.c?.v));
            if (this._groups.length < l) {
                for (; this._groups.length < l; ) {
                    let t = {
                        it: this.cloneElements(this._elements),
                        ty: "gr"
                    };
                    t.it?.push({
                        a: {
                            a: 0,
                            ix: 1,
                            k: [0, 0]
                        },
                        nm: "Transform",
                        o: {
                            a: 0,
                            ix: 7,
                            k: 100
                        },
                        p: {
                            a: 0,
                            ix: 2,
                            k: [0, 0]
                        },
                        r: {
                            a: 1,
                            ix: 6,
                            k: [{
                                e: 0,
                                s: 0,
                                t: 0
                            }, {
                                e: 0,
                                s: 0,
                                t: 1
                            }]
                        },
                        s: {
                            a: 0,
                            ix: 3,
                            k: [100, 100]
                        },
                        sa: {
                            a: 0,
                            ix: 5,
                            k: 0
                        },
                        sk: {
                            a: 0,
                            ix: 4,
                            k: 0
                        },
                        ty: O.Transform
                    }),
                    this.arr.splice(0, 0, t),
                    this._groups.splice(0, 0, t),
                    this._currentCopies ? this._currentCopies++ : this._currentCopies = 1
                }
                this.elem?.reloadShapes(),
                h = !0
            }
            o = 0;
            let p = this._groups.length - 1;
            for (a = 0; a <= p - 1; a++) {
                if (e = o < l,
                this._groups[a] && (this._groups[a]._shouldRender = e),
                this.changeGroupRender(this._groups[a]?.it ?? [], e),
                !e) {
                    let t = this.elemsData[a]?.it ?? []
                      , e = t[t.length - 1];
                    e && (0 === e.transform.op.v ? e.transform.op._mdf = !1 : (e.transform.op._mdf = !0,
                    e.transform.op.v = 0))
                }
                o++
            }
            if (this._currentCopies = l,
            !this.matrix || !this.pMatrix || !this.rMatrix || !this.sMatrix || !this.tMatrix)
                throw Error(`${this.constructor.name}: Could not set Matrix`);
            if (!this.tr)
                throw Error(`${this.constructor.name}: Transformproperty is not set`);
            let m = Number(this.o?.v)
              , d = m % 1
              , c = m > 0 ? Math.floor(m) : Math.ceil(m)
              , u = this.pMatrix.props
              , f = this.rMatrix.props
              , g = this.sMatrix.props;
            this.pMatrix.reset(),
            this.rMatrix.reset(),
            this.sMatrix.reset(),
            this.tMatrix.reset(),
            this.matrix.reset();
            let y = 0;
            if (m > 0) {
                for (; y < c; )
                    this.applyTransforms(this.pMatrix, this.rMatrix, this.sMatrix, this.tr, 1, !1),
                    y++;
                d && (this.applyTransforms(this.pMatrix, this.rMatrix, this.sMatrix, this.tr, d, !1),
                y += d)
            } else if (m < 0) {
                for (; y > c; )
                    this.applyTransforms(this.pMatrix, this.rMatrix, this.sMatrix, this.tr, 1, !0),
                    y--;
                d && (this.applyTransforms(this.pMatrix, this.rMatrix, this.sMatrix, this.tr, -d, !0),
                y -= d)
            }
            for (a = 1 === this.data.m ? 0 : this._currentCopies - 1,
            n = 1 === this.data.m ? 1 : -1,
            o = this._currentCopies; o; ) {
                i = this.elemsData[a]?.it ?? [];
                let {length: t} = r = i[i.length - 1]?.transform.mProps.v.props ?? [];
                if (i[i.length - 1] && (i[i.length - 1].transform.mProps._mdf = !0,
                i[i.length - 1].transform.op._mdf = !0,
                i[i.length - 1].transform.op.v = 1 === this._currentCopies ? Number(this.so?.v) : Number(this.so?.v) + (Number(this.eo?.v) - Number(this.so?.v)) * (a / (this._currentCopies - 1))),
                0 === y)
                    for (this.matrix.reset(),
                    s = 0; s < t; s++)
                        r[s] = this.matrix.props[s] ?? 0;
                else {
                    for ((0 !== a && 1 === n || a !== this._currentCopies - 1 && -1 === n) && this.applyTransforms(this.pMatrix, this.rMatrix, this.sMatrix, this.tr, 1, !1),
                    this.matrix.transform(f[0] ?? 0, f[1] ?? 0, f[2] ?? 0, f[3] ?? 0, f[4] ?? 0, f[5] ?? 0, f[6] ?? 0, f[7] ?? 0, f[8] ?? 0, f[9] ?? 0, f[10] ?? 0, f[11] ?? 0, f[12] ?? 0, f[13] ?? 0, f[14] ?? 0, f[15] ?? 0),
                    this.matrix.transform(g[0] ?? 0, g[1] ?? 0, g[2] ?? 0, g[3] ?? 0, g[4] ?? 0, g[5] ?? 0, g[6] ?? 0, g[7] ?? 0, g[8] ?? 0, g[9] ?? 0, g[10] ?? 0, g[11] ?? 0, g[12] ?? 0, g[13] ?? 0, g[14] ?? 0, g[15] ?? 0),
                    this.matrix.transform(u[0] ?? 0, u[1] ?? 0, u[2] ?? 0, u[3] ?? 0, u[4] ?? 0, u[5] ?? 0, u[6] ?? 0, u[7] ?? 0, u[8] ?? 0, u[9] ?? 0, u[10] ?? 0, u[11] ?? 0, u[12] ?? 0, u[13] ?? 0, u[14] ?? 0, u[15] ?? 0),
                    s = 0; s < t; s++)
                        r[s] = this.matrix.props[s] ?? 0;
                    this.matrix.reset()
                }
                y++,
                o--,
                a += n
            }
            return h
        }
        resetElements(t) {
            let {length: e} = t;
            for (let s = 0; s < e; s++) {
                t[s]._processed = !1;
                let {it: e} = t[s] ?? {};
                t[s]?.ty === O.Group && e && this.resetElements(e)
            }
        }
        constructor(...t) {
            super(...t),
            this.arr = [],
            this.elemsData = [],
            this._elements = [],
            this._groups = []
        }
    }
    ),
    sb($.RoundCornersModifier, class extends s2 {
        initModifierProperties(t, e) {
            this.getValue = this.processKeys,
            this.rd = ea.getProp(t, e.r, 0, null, this),
            this._isAnimated = this.rd.effectsSequence.length > 0
        }
        processPath(t, e) {
            let s = eL();
            s.c = t.c;
            let i = t._length, r, a, n, o, h, l, p = 0, m, d, c, u, f, g;
            for (let y = 0; y < i; y++)
                r = t.v[y],
                n = t.o[y],
                a = t.i[y],
                r && r[0] === n[0] && r[1] === n[1] && r[0] === a[0] && r[1] === a[1] ? 0 !== y && y !== i - 1 || t.c ? (o = 0 === y ? t.v[i - 1] : t.v[y - 1],
                l = (h = Math.sqrt(Math.pow(r[0] - o[0], 2) + Math.pow(r[1] - o[1], 2))) ? Math.min(h / 2, e) / h : 0,
                m = f = r[0] + (o[0] - r[0]) * l,
                d = g = r[1] - (r[1] - o[1]) * l,
                c = m - (m - r[0]) * .5519,
                u = d - (d - r[1]) * .5519,
                s.setTripleAt(m, d, c, u, f, g, p),
                p++,
                o = y === i - 1 ? t.v[0] : t.v[y + 1],
                l = (h = Math.sqrt(Math.pow(r[0] - o[0], 2) + Math.pow(r[1] - o[1], 2))) ? Math.min(h / 2, e) / h : 0,
                m = c = r[0] + (o[0] - r[0]) * l,
                d = u = r[1] + (o[1] - r[1]) * l,
                f = m - (m - r[0]) * .5519,
                g = d - (d - r[1]) * .5519,
                s.setTripleAt(m, d, c, u, f, g, p)) : s.setTripleAt(r[0], r[1], n[0], n[1], a[0], a[1], p) : s.setTripleAt(t.v[y]?.[0] ?? 0, t.v[y]?.[1] ?? 0, t.o[y]?.[0] ?? 0, t.o[y]?.[1] ?? 0, t.i[y]?.[0] ?? 0, t.i[y]?.[1] ?? 0, p),
                p++;
            return s
        }
        processShapes(t) {
            let {length: e} = this.shapes
              , s = this.rd?.v;
            if (0 !== s) {
                let i, r, a;
                for (let n = 0; n < e; n++) {
                    if (a = (i = this.shapes[n]).localShapeCollection,
                    !(!i.shape?._mdf && !this._mdf && !t)) {
                        a?.releaseShapes(),
                        i.shape && (i.shape._mdf = !0),
                        r = i.shape?.paths?.shapes ?? [];
                        let t = i.shape?.paths?._length || 0;
                        for (let e = 0; e < t; e++)
                            a?.addShape(this.processPath(r[e], s))
                    }
                    i.shape && (i.shape.paths = i.localShapeCollection)
                }
            }
            0 === this.dynamicProperties.length && (this._mdf = !1)
        }
    }
    ),
    sb($.ZigZagModifier, class extends s2 {
        initModifierProperties(t, e) {
            this.getValue = this.processKeys,
            this.amplitude = ea.getProp(t, e.s, 0, null, this),
            this.frequency = ea.getProp(t, e.r, 0, null, this),
            this.pointsType = ea.getProp(t, e.pt, 0, null, this),
            this._isAnimated = this.amplitude.effectsSequence.length > 0 || this.frequency.effectsSequence.length > 0 || this.pointsType.effectsSequence.length > 0
        }
        processPath(t, e, s, i) {
            let r = t._length
              , a = eL();
            if (a.c = t.c,
            t.c || (r -= 1),
            0 === r)
                return a;
            let n = -1
              , o = s1(t, 0);
            ir(a, t, 0, e, s, i, n);
            for (let h = 0; h < r; h++)
                n = ia(a, o, e, s, i, -n),
                o = h !== r - 1 || t.c ? s1(t, (h + 1) % r) : null,
                ir(a, t, h + 1, e, s, i, n);
            return a
        }
        processShapes(t) {
            let e = Number(this.amplitude?.v)
              , s = Math.max(0, Math.round(Number(this.frequency?.v)))
              , i = Number(this.pointsType?.v);
            if (0 !== e) {
                let r, a, n, {length: o} = this.shapes;
                for (let h = 0; h < o; h++) {
                    if (a = (r = this.shapes[h]).localShapeCollection,
                    !(!r.shape?._mdf && !this._mdf && !t)) {
                        a?.releaseShapes(),
                        r.shape && (r.shape._mdf = !0),
                        n = r.shape?.paths?.shapes ?? [];
                        let {_length: t} = r.shape?.paths ?? {
                            _length: 0
                        };
                        for (let r = 0; r < t; r++)
                            a?.addShape(this.processPath(n[r], e, s, i))
                    }
                    r.shape && (r.shape.paths = r.localShapeCollection)
                }
            }
            0 === this.dynamicProperties.length && (this._mdf = !1)
        }
    }
    ),
    sb($.OffsetPathModifier, class extends s2 {
        initModifierProperties(t, e) {
            this.getValue = this.processKeys,
            this.amount = ea.getProp(t, e.a, 0, null, this),
            this.miterLimit = ea.getProp(t, e.ml, 0, null, this),
            this.lineJoin = e.lj,
            this._isAnimated = this.amount.effectsSequence.length > 0
        }
        processPath(t, e, s, i) {
            let r = eL();
            r.c = t.c;
            let a = t.length();
            t.c || (a -= 1);
            let n, o = [];
            for (let s = 0; s < a; s++)
                n = s1(t, s),
                o.push(it(n, e));
            if (!t.c)
                for (let s = a - 1; s >= 0; s--)
                    n = function(t, e) {
                        let s = (e + 1) % t.length();
                        return new s0(t.v[s],t.i[s],t.o[e],t.v[e],!0)
                    }(t, s),
                    o.push(it(n, e));
            o = (t => {
                let e;
                for (let s = 1; s < t.length; s++)
                    e = is(t[s - 1], t[s]),
                    t[s - 1] = e[0],
                    t[s] = e[1];
                return t.length > 1 && (e = is(t[t.length - 1], t[0]),
                t[t.length - 1] = e[0],
                t[0] = e[1]),
                t
            }
            )(o);
            let h = null
              , l = null
              , {length: p} = o;
            for (let t = 0; t < p; t++) {
                let e = o[t];
                l && (h = s9(r, l, e[0], s, i)),
                l = e[e.length - 1];
                let {length: a} = e;
                for (let t = 0; t < a; t++)
                    n = e[t],
                    h && G(n.points[0], h) ? r.setXYAt(n.points[1][0], n.points[1][1], "o", r.length() - 1) : r.setTripleAt(n.points[0][0], n.points[0][1], n.points[1][0], n.points[1][1], n.points[0][0], n.points[0][1], r.length()),
                    r.setTripleAt(n.points[3][0], n.points[3][1], n.points[3][0], n.points[3][1], n.points[2][0], n.points[2][1], r.length()),
                    h = n.points[3]
            }
            return p > 0 && l && s9(r, l, o[0]?.[0], s, i),
            r
        }
        processShapes(t) {
            let {length: e} = this.shapes
              , s = Number(this.amount?.v)
              , i = Number(this.miterLimit?.v)
              , r = Number(this.lineJoin);
            if (0 !== s) {
                let a, n, o;
                for (let h = 0; h < e; h++) {
                    if (o = (n = this.shapes[h]).localShapeCollection,
                    !(!n.shape?._mdf && !this._mdf && !t)) {
                        o?.releaseShapes(),
                        n.shape && (n.shape._mdf = !0),
                        a = n.shape?.paths?.shapes;
                        let t = n.shape?.paths?._length || 0;
                        for (let e = 0; e < t; e++) {
                            let t = a?.[e];
                            t && o?.addShape(this.processPath(t, s, r, i))
                        }
                    }
                    n.shape && (n.shape.paths = n.localShapeCollection)
                }
            }
            0 === this.dynamicProperties.length && (this._mdf = !1)
        }
    }
    );
    var il = {}
      , ip = function(t, e, s, i, r) {
        var a = new Worker(il[e] || (il[e] = URL.createObjectURL(new Blob([t + ';addEventListener("error",function(e){e=e.error;postMessage({$e$:[e.message,e.code,e.stack]})})'],{
            type: "text/javascript"
        }))));
        return a.onmessage = function(t) {
            var e = t.data
              , s = e.$e$;
            if (s) {
                var i = Error(s[0]);
                i.code = s[1],
                i.stack = s[2],
                r(i, null)
            } else
                r(null, e)
        }
        ,
        a.postMessage(s, i),
        a
    }
      , im = Uint8Array
      , id = Uint16Array
      , ic = Int32Array
      , iu = new im([0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 0, 0, 0, 0])
      , ig = new im([0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12, 13, 13, 0, 0])
      , iy = new im([16, 17, 18, 0, 8, 7, 9, 6, 10, 5, 11, 4, 12, 3, 13, 2, 14, 1, 15])
      , ib = function(t, e) {
        for (var s = new id(31), i = 0; i < 31; ++i)
            s[i] = e += 1 << t[i - 1];
        for (var r = new ic(s[30]), i = 1; i < 30; ++i)
            for (var a = s[i]; a < s[i + 1]; ++a)
                r[a] = a - s[i] << 5 | i;
        return {
            b: s,
            r: r
        }
    }
      , iv = ib(iu, 2)
      , i_ = iv.b
      , iw = iv.r;
    i_[28] = 258,
    iw[258] = 28;
    for (var iS = ib(ig, 0), iE = iS.b, ik = iS.r, iM = new id(32768), ix = 0; ix < 32768; ++ix) {
        var iP = (43690 & ix) >> 1 | (21845 & ix) << 1;
        iP = (61680 & (iP = (52428 & iP) >> 2 | (13107 & iP) << 2)) >> 4 | (3855 & iP) << 4,
        iM[ix] = ((65280 & iP) >> 8 | (255 & iP) << 8) >> 1
    }
    for (var iC = function(t, e, s) {
        for (var i, r = t.length, a = 0, n = new id(e); a < r; ++a)
            t[a] && ++n[t[a] - 1];
        var o = new id(e);
        for (a = 1; a < e; ++a)
            o[a] = o[a - 1] + n[a - 1] << 1;
        if (s) {
            i = new id(1 << e);
            var h = 15 - e;
            for (a = 0; a < r; ++a)
                if (t[a])
                    for (var l = a << 4 | t[a], p = e - t[a], m = o[t[a] - 1]++ << p, d = m | (1 << p) - 1; m <= d; ++m)
                        i[iM[m] >> h] = l
        } else
            for (a = 0,
            i = new id(r); a < r; ++a)
                t[a] && (i[a] = iM[o[t[a] - 1]++] >> 15 - t[a]);
        return i
    }, iA = new im(288), ix = 0; ix < 144; ++ix)
        iA[ix] = 8;
    for (var ix = 144; ix < 256; ++ix)
        iA[ix] = 9;
    for (var ix = 256; ix < 280; ++ix)
        iA[ix] = 7;
    for (var ix = 280; ix < 288; ++ix)
        iA[ix] = 8;
    for (var iT = new im(32), ix = 0; ix < 32; ++ix)
        iT[ix] = 5;
    var iD = iC(iA, 9, 0)
      , iI = iC(iA, 9, 1)
      , iL = iC(iT, 5, 0)
      , iF = iC(iT, 5, 1)
      , i$ = function(t) {
        for (var e = t[0], s = 1; s < t.length; ++s)
            t[s] > e && (e = t[s]);
        return e
    }
      , iN = function(t, e, s) {
        var i = e / 8 | 0;
        return (t[i] | t[i + 1] << 8) >> (7 & e) & s
    }
      , iO = function(t, e) {
        var s = e / 8 | 0;
        return (t[s] | t[s + 1] << 8 | t[s + 2] << 16) >> (7 & e)
    }
      , iV = function(t) {
        return (t + 7) / 8 | 0
    }
      , iz = function(t, e, s) {
        return (null == e || e < 0) && (e = 0),
        (null == s || s > t.length) && (s = t.length),
        new im(t.subarray(e, s))
    }
      , iR = ["unexpected EOF", "invalid block type", "invalid length/literal", "invalid distance", "stream finished", "no stream handler", , "no callback", "invalid UTF-8 data", "extra field too long", "date not in range 1980-2099", "filename too long", "stream finishing", "invalid zip data"]
      , iB = function(t, e, s) {
        var i = Error(e || iR[t]);
        if (i.code = t,
        Error.captureStackTrace && Error.captureStackTrace(i, iB),
        !s)
            throw i;
        return i
    }
      , iq = function(t, e, s, i) {
        var r = t.length
          , a = i ? i.length : 0;
        if (!r || e.f && !e.l)
            return s || new im(0);
        var n = !s
          , o = n || 2 != e.i
          , h = e.i;
        n && (s = new im(3 * r));
        var l = function(t) {
            var e = s.length;
            if (t > e) {
                var i = new im(Math.max(2 * e, t));
                i.set(s),
                s = i
            }
        }
          , p = e.f || 0
          , m = e.p || 0
          , d = e.b || 0
          , c = e.l
          , u = e.d
          , f = e.m
          , g = e.n
          , y = 8 * r;
        do {
            if (!c) {
                p = iN(t, m, 1);
                var b = iN(t, m + 1, 3);
                if (m += 3,
                b)
                    if (1 == b)
                        c = iI,
                        u = iF,
                        f = 9,
                        g = 5;
                    else if (2 == b) {
                        var v = iN(t, m, 31) + 257
                          , _ = iN(t, m + 10, 15) + 4
                          , w = v + iN(t, m + 5, 31) + 1;
                        m += 14;
                        for (var S = new im(w), E = new im(19), k = 0; k < _; ++k)
                            E[iy[k]] = iN(t, m + 3 * k, 7);
                        m += 3 * _;
                        for (var M = i$(E), x = (1 << M) - 1, P = iC(E, M, 1), k = 0; k < w; ) {
                            var C = P[iN(t, m, x)];
                            m += 15 & C;
                            var A = C >> 4;
                            if (A < 16)
                                S[k++] = A;
                            else {
                                var T = 0
                                  , D = 0;
                                for (16 == A ? (D = 3 + iN(t, m, 3),
                                m += 2,
                                T = S[k - 1]) : 17 == A ? (D = 3 + iN(t, m, 7),
                                m += 3) : 18 == A && (D = 11 + iN(t, m, 127),
                                m += 7); D--; )
                                    S[k++] = T
                            }
                        }
                        var I = S.subarray(0, v)
                          , L = S.subarray(v);
                        f = i$(I),
                        g = i$(L),
                        c = iC(I, f, 1),
                        u = iC(L, g, 1)
                    } else
                        iB(1);
                else {
                    var A = iV(m) + 4
                      , F = t[A - 4] | t[A - 3] << 8
                      , $ = A + F;
                    if ($ > r) {
                        h && iB(0);
                        break
                    }
                    o && l(d + F),
                    s.set(t.subarray(A, $), d),
                    e.b = d += F,
                    e.p = m = 8 * $,
                    e.f = p;
                    continue
                }
                if (m > y) {
                    h && iB(0);
                    break
                }
            }
            o && l(d + 131072);
            for (var N = (1 << f) - 1, O = (1 << g) - 1, V = m; ; V = m) {
                var T = c[iO(t, m) & N]
                  , z = T >> 4;
                if ((m += 15 & T) > y) {
                    h && iB(0);
                    break
                }
                if (T || iB(2),
                z < 256)
                    s[d++] = z;
                else if (256 == z) {
                    V = m,
                    c = null;
                    break
                } else {
                    var R = z - 254;
                    if (z > 264) {
                        var k = z - 257
                          , B = iu[k];
                        R = iN(t, m, (1 << B) - 1) + i_[k],
                        m += B
                    }
                    var q = u[iO(t, m) & O]
                      , j = q >> 4;
                    q || iB(3),
                    m += 15 & q;
                    var L = iE[j];
                    if (j > 3) {
                        var B = ig[j];
                        L += iO(t, m) & (1 << B) - 1,
                        m += B
                    }
                    if (m > y) {
                        h && iB(0);
                        break
                    }
                    o && l(d + 131072);
                    var H = d + R;
                    if (d < L) {
                        var G = a - L
                          , W = Math.min(L, H);
                        for (G + d < 0 && iB(3); d < W; ++d)
                            s[d] = i[G + d]
                    }
                    for (; d < H; ++d)
                        s[d] = s[d - L]
                }
            }
            e.l = c,
            e.p = V,
            e.b = d,
            e.f = p,
            c && (p = 1,
            e.m = f,
            e.d = u,
            e.n = g)
        } while (!p);
        return d != s.length && n ? iz(s, 0, d) : s.subarray(0, d)
    }
      , ij = function(t, e, s) {
        s <<= 7 & e;
        var i = e / 8 | 0;
        t[i] |= s,
        t[i + 1] |= s >> 8
    }
      , iH = function(t, e, s) {
        s <<= 7 & e;
        var i = e / 8 | 0;
        t[i] |= s,
        t[i + 1] |= s >> 8,
        t[i + 2] |= s >> 16
    }
      , iG = function(t, e) {
        for (var s = [], i = 0; i < t.length; ++i)
            t[i] && s.push({
                s: i,
                f: t[i]
            });
        var r = s.length
          , a = s.slice();
        if (!r)
            return {
                t: iK,
                l: 0
            };
        if (1 == r) {
            var n = new im(s[0].s + 1);
            return n[s[0].s] = 1,
            {
                t: n,
                l: 1
            }
        }
        s.sort(function(t, e) {
            return t.f - e.f
        }),
        s.push({
            s: -1,
            f: 25001
        });
        var o = s[0]
          , h = s[1]
          , l = 0
          , p = 1
          , m = 2;
        for (s[0] = {
            s: -1,
            f: o.f + h.f,
            l: o,
            r: h
        }; p != r - 1; )
            o = s[s[l].f < s[m].f ? l++ : m++],
            h = s[l != p && s[l].f < s[m].f ? l++ : m++],
            s[p++] = {
                s: -1,
                f: o.f + h.f,
                l: o,
                r: h
            };
        for (var d = a[0].s, i = 1; i < r; ++i)
            a[i].s > d && (d = a[i].s);
        var c = new id(d + 1)
          , u = iW(s[p - 1], c, 0);
        if (u > e) {
            var i = 0
              , f = 0
              , g = u - e
              , y = 1 << g;
            for (a.sort(function(t, e) {
                return c[e.s] - c[t.s] || t.f - e.f
            }); i < r; ++i) {
                var b = a[i].s;
                if (c[b] > e)
                    f += y - (1 << u - c[b]),
                    c[b] = e;
                else
                    break
            }
            for (f >>= g; f > 0; ) {
                var v = a[i].s;
                c[v] < e ? f -= 1 << e - c[v]++ - 1 : ++i
            }
            for (; i >= 0 && f; --i) {
                var _ = a[i].s;
                c[_] == e && (--c[_],
                ++f)
            }
            u = e
        }
        return {
            t: new im(c),
            l: u
        }
    }
      , iW = function(t, e, s) {
        return -1 == t.s ? Math.max(iW(t.l, e, s + 1), iW(t.r, e, s + 1)) : e[t.s] = s
    }
      , iU = function(t) {
        for (var e = t.length; e && !t[--e]; )
            ;
        for (var s = new id(++e), i = 0, r = t[0], a = 1, n = function(t) {
            s[i++] = t
        }, o = 1; o <= e; ++o)
            if (t[o] == r && o != e)
                ++a;
            else {
                if (!r && a > 2) {
                    for (; a > 138; a -= 138)
                        n(32754);
                    a > 2 && (n(a > 10 ? a - 11 << 5 | 28690 : a - 3 << 5 | 12305),
                    a = 0)
                } else if (a > 3) {
                    for (n(r),
                    --a; a > 6; a -= 6)
                        n(8304);
                    a > 2 && (n(a - 3 << 5 | 8208),
                    a = 0)
                }
                for (; a--; )
                    n(r);
                a = 1,
                r = t[o]
            }
        return {
            c: s.subarray(0, i),
            n: e
        }
    }
      , iY = function(t, e) {
        for (var s = 0, i = 0; i < e.length; ++i)
            s += t[i] * e[i];
        return s
    }
      , iX = function(t, e, s) {
        var i = s.length
          , r = iV(e + 2);
        t[r] = 255 & i,
        t[r + 1] = i >> 8,
        t[r + 2] = 255 ^ t[r],
        t[r + 3] = 255 ^ t[r + 1];
        for (var a = 0; a < i; ++a)
            t[r + a + 4] = s[a];
        return (r + 4 + i) * 8
    }
      , iJ = function(t, e, s, i, r, a, n, o, h, l, p) {
        ij(e, p++, s),
        ++r[256];
        for (var m, d, c, u, f = iG(r, 15), g = f.t, y = f.l, b = iG(a, 15), v = b.t, _ = b.l, w = iU(g), S = w.c, E = w.n, k = iU(v), M = k.c, x = k.n, P = new id(19), C = 0; C < S.length; ++C)
            ++P[31 & S[C]];
        for (var C = 0; C < M.length; ++C)
            ++P[31 & M[C]];
        for (var A = iG(P, 7), T = A.t, D = A.l, I = 19; I > 4 && !T[iy[I - 1]]; --I)
            ;
        var L = l + 5 << 3
          , F = iY(r, iA) + iY(a, iT) + n
          , $ = iY(r, g) + iY(a, v) + n + 14 + 3 * I + iY(P, T) + 2 * P[16] + 3 * P[17] + 7 * P[18];
        if (h >= 0 && L <= F && L <= $)
            return iX(e, p, t.subarray(h, h + l));
        if (ij(e, p, 1 + ($ < F)),
        p += 2,
        $ < F) {
            m = iC(g, y, 0),
            d = g,
            c = iC(v, _, 0),
            u = v;
            var N = iC(T, D, 0);
            ij(e, p, E - 257),
            ij(e, p + 5, x - 1),
            ij(e, p + 10, I - 4),
            p += 14;
            for (var C = 0; C < I; ++C)
                ij(e, p + 3 * C, T[iy[C]]);
            p += 3 * I;
            for (var O = [S, M], V = 0; V < 2; ++V)
                for (var z = O[V], C = 0; C < z.length; ++C) {
                    var R = 31 & z[C];
                    ij(e, p, N[R]),
                    p += T[R],
                    R > 15 && (ij(e, p, z[C] >> 5 & 127),
                    p += z[C] >> 12)
                }
        } else
            m = iD,
            d = iA,
            c = iL,
            u = iT;
        for (var C = 0; C < o; ++C) {
            var B = i[C];
            if (B > 255) {
                var R = B >> 18 & 31;
                iH(e, p, m[R + 257]),
                p += d[R + 257],
                R > 7 && (ij(e, p, B >> 23 & 31),
                p += iu[R]);
                var q = 31 & B;
                iH(e, p, c[q]),
                p += u[q],
                q > 3 && (iH(e, p, B >> 5 & 8191),
                p += ig[q])
            } else
                iH(e, p, m[B]),
                p += d[B]
        }
        return iH(e, p, m[256]),
        p + d[256]
    }
      , iZ = new ic([65540, 131080, 131088, 131104, 262176, 1048704, 1048832, 2114560, 2117632])
      , iK = new im(0)
      , iQ = function(t, e, s, i, r, a) {
        var n = a.z || t.length
          , o = new im(i + n + 5 * (1 + Math.ceil(n / 7e3)) + r)
          , h = o.subarray(i, o.length - r)
          , l = a.l
          , p = 7 & (a.r || 0);
        if (e) {
            p && (h[0] = a.r >> 3);
            for (var m = iZ[e - 1], d = m >> 13, c = 8191 & m, u = (1 << s) - 1, f = a.p || new id(32768), g = a.h || new id(u + 1), y = Math.ceil(s / 3), b = 2 * y, v = function(e) {
                return (t[e] ^ t[e + 1] << y ^ t[e + 2] << b) & u
            }, _ = new ic(25e3), w = new id(288), S = new id(32), E = 0, k = 0, M = a.i || 0, x = 0, P = a.w || 0, C = 0; M + 2 < n; ++M) {
                var A = v(M)
                  , T = 32767 & M
                  , D = g[A];
                if (f[T] = D,
                g[A] = T,
                P <= M) {
                    var I = n - M;
                    if ((E > 7e3 || x > 24576) && (I > 423 || !l)) {
                        p = iJ(t, h, 0, _, w, S, k, x, C, M - C, p),
                        x = E = k = 0,
                        C = M;
                        for (var L = 0; L < 286; ++L)
                            w[L] = 0;
                        for (var L = 0; L < 30; ++L)
                            S[L] = 0
                    }
                    var F = 2
                      , $ = 0
                      , N = c
                      , O = T - D & 32767;
                    if (I > 2 && A == v(M - O))
                        for (var V = Math.min(d, I) - 1, z = Math.min(32767, M), R = Math.min(258, I); O <= z && --N && T != D; ) {
                            if (t[M + F] == t[M + F - O]) {
                                for (var B = 0; B < R && t[M + B] == t[M + B - O]; ++B)
                                    ;
                                if (B > F) {
                                    if (F = B,
                                    $ = O,
                                    B > V)
                                        break;
                                    for (var q = Math.min(O, B - 2), j = 0, L = 0; L < q; ++L) {
                                        var H = M - O + L & 32767
                                          , G = f[H]
                                          , W = H - G & 32767;
                                        W > j && (j = W,
                                        D = H)
                                    }
                                }
                            }
                            D = f[T = D],
                            O += T - D & 32767
                        }
                    if ($) {
                        _[x++] = 0x10000000 | iw[F] << 18 | ik[$];
                        var U = 31 & iw[F]
                          , Y = 31 & ik[$];
                        k += iu[U] + ig[Y],
                        ++w[257 + U],
                        ++S[Y],
                        P = M + F,
                        ++E
                    } else
                        _[x++] = t[M],
                        ++w[t[M]]
                }
            }
            for (M = Math.max(M, P); M < n; ++M)
                _[x++] = t[M],
                ++w[t[M]];
            p = iJ(t, h, l, _, w, S, k, x, C, M - C, p),
            l || (a.r = 7 & p | h[p / 8 | 0] << 3,
            p -= 7,
            a.h = g,
            a.p = f,
            a.i = M,
            a.w = P)
        } else {
            for (var M = a.w || 0; M < n + l; M += 65535) {
                var X = M + 65535;
                X >= n && (h[p / 8 | 0] = l,
                X = n),
                p = iX(h, p + 1, t.subarray(M, X))
            }
            a.i = n
        }
        return iz(o, 0, i + iV(p) + r)
    }
      , i0 = function() {
        for (var t = new Int32Array(256), e = 0; e < 256; ++e) {
            for (var s = e, i = 9; --i; )
                s = (1 & s && -0x12477ce0) ^ s >>> 1;
            t[e] = s
        }
        return t
    }()
      , i1 = function() {
        var t = -1;
        return {
            p: function(e) {
                for (var s = t, i = 0; i < e.length; ++i)
                    s = i0[255 & s ^ e[i]] ^ s >>> 8;
                t = s
            },
            d: function() {
                return ~t
            }
        }
    }
      , i2 = function(t, e, s, i, r) {
        if (!r && (r = {
            l: 1
        },
        e.dictionary)) {
            var a = e.dictionary.subarray(-32768)
              , n = new im(a.length + t.length);
            n.set(a),
            n.set(t, a.length),
            t = n,
            r.w = a.length
        }
        return iQ(t, null == e.level ? 6 : e.level, null == e.mem ? r.l ? Math.ceil(1.5 * Math.max(8, Math.min(13, Math.log(t.length)))) : 20 : 12 + e.mem, s, i, r)
    }
      , i3 = function(t, e) {
        var s = {};
        for (var i in t)
            s[i] = t[i];
        for (var i in e)
            s[i] = e[i];
        return s
    }
      , i5 = function(t, e, s) {
        for (var i = t(), r = t.toString(), a = r.slice(r.indexOf("[") + 1, r.lastIndexOf("]")).replace(/\s+/g, "").split(","), n = 0; n < i.length; ++n) {
            var o = i[n]
              , h = a[n];
            if ("function" == typeof o) {
                e += ";" + h + "=";
                var l = o.toString();
                if (o.prototype)
                    if (-1 != l.indexOf("[native code]")) {
                        var p = l.indexOf(" ", 8) + 1;
                        e += l.slice(p, l.indexOf("(", p))
                    } else
                        for (var m in e += l,
                        o.prototype)
                            e += ";" + h + ".prototype." + m + "=" + o.prototype[m].toString();
                else
                    e += l
            } else
                s[h] = o
        }
        return e
    }
      , i4 = []
      , i6 = function(t) {
        var e = [];
        for (var s in t)
            t[s].buffer && e.push((t[s] = new t[s].constructor(t[s])).buffer);
        return e
    }
      , i8 = function(t, e, s, i) {
        if (!i4[s]) {
            for (var r = "", a = {}, n = t.length - 1, o = 0; o < n; ++o)
                r = i5(t[o], r, a);
            i4[s] = {
                c: i5(t[n], r, a),
                e: a
            }
        }
        var h = i3({}, i4[s].e);
        return ip(i4[s].c + ";onmessage=function(e){for(var k in e.data)self[k]=e.data[k];onmessage=" + e.toString() + "}", s, h, i6(h), i)
    }
      , i9 = function() {
        return [im, id, ic, iu, ig, iy, i_, iE, iI, iF, iM, iR, iC, i$, iN, iO, iV, iz, iB, iq, rh, rt, re]
    }
      , i7 = function() {
        return [im, id, ic, iu, ig, iy, iw, ik, iD, iA, iL, iT, iM, iZ, iK, iC, ij, iH, iG, iW, iU, iY, iX, iJ, iV, iz, iQ, i2, ro, rt]
    }
      , rt = function(t) {
        return postMessage(t, [t.buffer])
    }
      , re = function(t) {
        return t && {
            out: t.size && new im(t.size),
            dictionary: t.dictionary
        }
    }
      , rs = function(t, e, s, i, r, a) {
        var n = i8(s, i, r, function(t, e) {
            n.terminate(),
            a(t, e)
        });
        return n.postMessage([t, e], e.consume ? [t.buffer] : []),
        function() {
            n.terminate()
        }
    }
      , ri = function(t, e) {
        return t[e] | t[e + 1] << 8
    }
      , rr = function(t, e) {
        return (t[e] | t[e + 1] << 8 | t[e + 2] << 16 | t[e + 3] << 24) >>> 0
    }
      , ra = function(t, e) {
        return rr(t, e) + 0x100000000 * rr(t, e + 4)
    }
      , rn = function(t, e, s) {
        for (; s; ++e)
            t[e] = s,
            s >>>= 8
    };
    function ro(t, e) {
        return i2(t, e || {}, 0, 0)
    }
    function rh(t, e) {
        return iq(t, {
            i: 2
        }, e && e.out, e && e.dictionary)
    }
    var rl = function(t, e, s, i) {
        for (var r in t) {
            var a = t[r]
              , n = e + r
              , o = i;
            Array.isArray(a) && (o = i3(i, a[1]),
            a = a[0]),
            a instanceof im ? s[n] = [a, o] : (s[n += "/"] = [new im(0), o],
            rl(a, n, s, i))
        }
    }
      , rp = "undefined" != typeof TextEncoder && new TextEncoder
      , rm = "undefined" != typeof TextDecoder && new TextDecoder;
    try {
        rm.decode(iK, {
            stream: !0
        })
    } catch (t) {}
    var rd = function(t) {
        for (var e = "", s = 0; ; ) {
            var i = t[s++]
              , r = (i > 127) + (i > 223) + (i > 239);
            if (s + r > t.length)
                return {
                    s: e,
                    r: iz(t, s - 1)
                };
            r ? 3 == r ? e += String.fromCharCode(55296 | (i = ((15 & i) << 18 | (63 & t[s++]) << 12 | (63 & t[s++]) << 6 | 63 & t[s++]) - 65536) >> 10, 56320 | 1023 & i) : 1 & r ? e += String.fromCharCode((31 & i) << 6 | 63 & t[s++]) : e += String.fromCharCode((15 & i) << 12 | (63 & t[s++]) << 6 | 63 & t[s++]) : e += String.fromCharCode(i)
        }
    };
    function rc(t, e) {
        if (e) {
            for (var s = new im(t.length), i = 0; i < t.length; ++i)
                s[i] = t.charCodeAt(i);
            return s
        }
        if (rp)
            return rp.encode(t);
        for (var r = t.length, a = new im(t.length + (t.length >> 1)), n = 0, o = function(t) {
            a[n++] = t
        }, i = 0; i < r; ++i) {
            if (n + 5 > a.length) {
                var h = new im(n + 8 + (r - i << 1));
                h.set(a),
                a = h
            }
            var l = t.charCodeAt(i);
            l < 128 || e ? o(l) : (l < 2048 ? o(192 | l >> 6) : (l > 55295 && l < 57344 ? (o(240 | (l = 65536 + (1047552 & l) | 1023 & t.charCodeAt(++i)) >> 18),
            o(128 | l >> 12 & 63)) : o(224 | l >> 12),
            o(128 | l >> 6 & 63)),
            o(128 | 63 & l))
        }
        return iz(a, 0, n)
    }
    function ru(t, e) {
        if (e) {
            for (var s = "", i = 0; i < t.length; i += 16384)
                s += String.fromCharCode.apply(null, t.subarray(i, i + 16384));
            return s
        }
        if (rm)
            return rm.decode(t);
        var r = rd(t)
          , a = r.s
          , s = r.r;
        return s.length && iB(8),
        a
    }
    var rf = function(t, e, s) {
        var i = ri(t, e + 28)
          , r = ru(t.subarray(e + 46, e + 46 + i), !(2048 & ri(t, e + 8)))
          , a = e + 46 + i
          , n = rr(t, e + 20)
          , o = s && 0xffffffff == n ? rg(t, a) : [n, rr(t, e + 24), rr(t, e + 42)]
          , h = o[0]
          , l = o[1]
          , p = o[2];
        return [ri(t, e + 10), h, l, r, a + ri(t, e + 30) + ri(t, e + 32), p]
    }
      , rg = function(t, e) {
        for (; 1 != ri(t, e); e += 4 + ri(t, e + 2))
            ;
        return [ra(t, e + 12), ra(t, e + 4), ra(t, e + 20)]
    }
      , ry = function(t) {
        var e = 0;
        if (t)
            for (var s in t) {
                var i = t[s].length;
                i > 65535 && iB(9),
                e += i + 4
            }
        return e
    }
      , rb = function(t, e, s, i, r, a, n, o) {
        var h = i.length
          , l = s.extra
          , p = o && o.length
          , m = ry(l);
        rn(t, e, null != n ? 0x2014b50 : 0x4034b50),
        e += 4,
        null != n && (t[e++] = 20,
        t[e++] = s.os),
        t[e] = 20,
        e += 2,
        t[e++] = s.flag << 1 | (a < 0 && 8),
        t[e++] = r && 8,
        t[e++] = 255 & s.compression,
        t[e++] = s.compression >> 8;
        var d = new Date(null == s.mtime ? Date.now() : s.mtime)
          , c = d.getFullYear() - 1980;
        if ((c < 0 || c > 119) && iB(10),
        rn(t, e, c << 25 | d.getMonth() + 1 << 21 | d.getDate() << 16 | d.getHours() << 11 | d.getMinutes() << 5 | d.getSeconds() >> 1),
        e += 4,
        -1 != a && (rn(t, e, s.crc),
        rn(t, e + 4, a < 0 ? -a - 2 : a),
        rn(t, e + 8, s.size)),
        rn(t, e + 12, h),
        rn(t, e + 14, m),
        e += 16,
        null != n && (rn(t, e, p),
        rn(t, e + 6, s.attrs),
        rn(t, e + 10, n),
        e += 14),
        t.set(i, e),
        e += h,
        m)
            for (var u in l) {
                var f = l[u]
                  , g = f.length;
                rn(t, e, +u),
                rn(t, e + 2, g),
                t.set(f, e + 4),
                e += 4 + g
            }
        return p && (t.set(o, e),
        e += p),
        e
    }
      , rv = function(t, e, s, i, r) {
        rn(t, e, 0x6054b50),
        rn(t, e + 8, s),
        rn(t, e + 10, s),
        rn(t, e + 12, i),
        rn(t, e + 16, r)
    }
      , r_ = "function" == typeof queueMicrotask ? queueMicrotask : "function" == typeof setTimeout ? setTimeout : function(t) {
        t()
    }
    ;
    let rw = (r = 0,
    () => (r++,
    `__lottie_element_${r}`))
      , rS = (t, e) => {
        let s = new Blob([t],{
            type: e?.mimeType
        })
          , i = e?.name || rw()
          , r = URL.createObjectURL(s)
          , a = document.createElement("a");
        a.href = r,
        a.download = i,
        a.hidden = !0,
        document.body.appendChild(a),
        a.click(),
        setTimeout( () => {
            a.remove(),
            URL.revokeObjectURL(r)
        }
        , 1e3)
    }
      , rE = t => {
        if ("string" == typeof t && t && (t => {
            let e = t?.split("/").pop()?.lastIndexOf(".");
            return (e ?? 0) > 1 && t && t.length - 1 > (e ?? 0)
        }
        )(t))
            return t.split(".").pop()?.toLowerCase()
    }
      , rk = t => {
        let e = t.split(":")[1]?.split(";")[0];
        return e?.split("/")[1]?.split("+")[0]
    }
      , rM = (t, e) => (rE(t),
    `${t.split("/").pop()?.replace(/\.[^.]*$/, "").replaceAll(/\W+/g, "-")}`)
      , rx = (t, e) => {
        if (e)
            return rE(e) ? rE(e) === t ? e : `${rM(e)}.${t}` : `${e}.${t}`
    }
      , rP = t => !("h"in t) && !("w"in t) && "p"in t && "e"in t && "u"in t && "id"in t
      , rC = t => "w"in t && "h"in t && !("xt"in t) && "p"in t
      , rA = t => t.slice(Math.max(0, t.indexOf(",") + 1))
      , rT = !("undefined" != typeof window && document)
      , rD = async t => await new Promise( (e, s) => {
        !function(t, e, s) {
            s || (s = e,
            e = {}),
            "function" != typeof s && iB(7);
            var i = {};
            rl(t, "", i, e);
            var r = Object.keys(i)
              , a = r.length
              , n = 0
              , o = 0
              , h = a
              , l = Array(a)
              , p = []
              , m = function() {
                for (var t = 0; t < p.length; ++t)
                    p[t]()
            }
              , d = function(t, e) {
                r_(function() {
                    s(t, e)
                })
            };
            r_(function() {
                d = s
            });
            var c = function() {
                var t = new im(o + 22)
                  , e = n
                  , s = o - n;
                o = 0;
                for (var i = 0; i < h; ++i) {
                    var r = l[i];
                    try {
                        var a = r.c.length;
                        rb(t, o, r, r.f, r.u, a);
                        var p = 30 + r.f.length + ry(r.extra)
                          , m = o + p;
                        t.set(r.c, m),
                        rb(t, n, r, r.f, r.u, a, o, r.m),
                        n += 16 + p + (r.m ? r.m.length : 0),
                        o = m + a
                    } catch (t) {
                        return d(t, null)
                    }
                }
                rv(t, n, l.length, s, e),
                d(null, t)
            };
            a || c();
            for (var u = function(t) {
                var e, s, h = r[t], u = i[h], f = u[0], g = u[1], y = i1(), b = f.length;
                y.p(f);
                var v = rc(h)
                  , _ = v.length
                  , w = g.comment
                  , S = w && rc(w)
                  , E = S && S.length
                  , k = ry(g.extra)
                  , M = 8 * (0 != g.level)
                  , x = function(e, s) {
                    if (e)
                        m(),
                        d(e, null);
                    else {
                        var i = s.length;
                        l[t] = i3(g, {
                            size: b,
                            crc: y.d(),
                            c: s,
                            f: v,
                            m: S,
                            u: _ != h.length || S && w.length != E,
                            compression: M
                        }),
                        n += 30 + _ + k + i,
                        o += 76 + 2 * (_ + k) + (E || 0) + i,
                        --a || c()
                    }
                };
                if (_ > 65535 && x(iB(11, 0, 1), null),
                M)
                    if (b < 16e4)
                        try {
                            x(null, ro(f, g))
                        } catch (t) {
                            x(t, null)
                        }
                    else
                        p.push((e = g,
                        (s = x) || (s = e,
                        e = {}),
                        "function" != typeof s && iB(7),
                        rs(f, e, [i7], function(t) {
                            return rt(ro(t.data[0], t.data[1]))
                        }, 0, s)));
                else
                    x(null, f)
            }, f = 0; f < h; ++f)
                u(f)
        }(t, {
            level: 9
        }, (t, i) => t ? void s(t) : i.buffer instanceof ArrayBuffer ? void e(i.buffer) : void s(Error("Data is not transferable")))
    }
    )
      , rI = async t => {
        let e = await fetch(t)
          , s = await e.blob();
        return new Promise( (t, e) => {
            try {
                let i = new FileReader;
                i.onload = () => {
                    if ("string" == typeof i.result)
                        return void t(i.result);
                    e(Error("Could not create bas64"))
                }
                ,
                i.readAsDataURL(s)
            } catch (t) {
                e(t)
            }
        }
        )
    }
      , rL = t => rc(rT ? Buffer.from(rA(t), "base64").toString("binary") : atob(rA(t)), !0);
    async function rF({animations: t=[], fileName: e, manifest: s, shouldDownload: i=!0}) {
        try {
            if (0 === t.length || !s)
                throw Error(`Missing or malformed required parameter(s):
 ${t.length > 0 ? "- manifest\n" : ""} ${s ? "- animations\n" : ""}`);
            let r = rx("lottie", e) || `${rw()}.lottie`
              , a = {
                "manifest.json": [rc(JSON.stringify(s), !0), {
                    level: 0
                }]
            }
              , {length: n} = t;
            for (let e = 0; e < n; e++) {
                let {length: i} = t[e]?.assets ?? [];
                for (let s = 0; s < i; s++) {
                    let i = t[e]?.assets[s];
                    if (!i?.p || !rC(i) && !rP(i))
                        continue;
                    let {p: r, u: n} = i;
                    if (!r)
                        continue;
                    let o = rw()
                      , h = r.startsWith("data:")
                      , l = h ? rk(r) : rE(r)
                      , p = h ? r : await rI(n ? n.endsWith("/") && `${n}${r}` || `${n}/${r}` : r)
                      , m = t[e]?.assets[s];
                    m && (m.e = 1,
                    m.p = `${o}.${l}`,
                    m.u = ""),
                    a[`${rP(i) ? "audio" : "images"}/${o}.${l}`] = [rL(p), {
                        level: 9
                    }]
                }
                let {length: r} = t[e]?.layers ?? [];
                for (let s = 0; s < r; s++) {
                    let {ks: i} = t[e]?.layers[s] ?? {}
                      , r = Object.keys(i ?? {})
                      , {length: a} = r;
                    for (let n = 0; n < a; n++) {
                        let {x: a} = i?.[r[n]];
                        if (!a)
                            continue;
                        let o = t[e]?.layers[s]?.ks[r[n]];
                        o.x = btoa(a),
                        o.e = 1
                    }
                }
                a[`a/${s.animations[e]?.id}.json`] = [rc(JSON.stringify(t[e]), !0), {
                    level: 9
                }]
            }
            let o = await rD(a);
            if (i)
                return rS(o, {
                    mimeType: "application/zip",
                    name: r
                }),
                null;
            return o
        } catch (t) {
            return console.error(t),
            null
        }
    }
    let r$ = t => {
        switch (t) {
        case "svg":
        case "svg+xml":
            return "image/svg+xml";
        case "jpg":
        case "jpeg":
            return "image/jpeg";
        case "png":
        case "gif":
        case "webp":
        case "avif":
            return `image/${t}`;
        case "mp3":
        case "mpeg":
        case "wav":
            return `audio/${t}`;
        default:
            return ""
        }
    }
      , rN = t => !!t && /^(?:[0-9a-z+/]{4})*(?:[0-9a-z+/]{2}==|[0-9a-z+/]{3}=)?$/i.test(rA(t));
    async function rO(t, e) {
        if (!Array.isArray(e))
            return;
        let s = []
          , {length: i} = e;
        for (let r = 0; r < i; r++) {
            let i = e[r];
            if (!i || !rP(i) && !rC(i))
                continue;
            let a = rC(i) ? "images" : "audio"
              , n = t?.[`${a}/${i.p}`];
            n && s.push(new Promise(t => {
                let e;
                if (rT)
                    e = Buffer.from(n).toString("base64");
                else {
                    let t = ""
                      , {length: s} = n;
                    for (let e = 0; e < s; e++)
                        t += String.fromCharCode(n[e]);
                    e = btoa(t)
                }
                i.p = i.p?.startsWith("data:") || rN(i.p) ? i.p : `data:${r$(rE(i.p))};base64,${e}`,
                i.e = 1,
                i.u = "",
                t()
            }
            ))
        }
        await Promise.all(s)
    }
    let rV = async t => {
        let e = new Uint8Array(await t.arrayBuffer());
        return await new Promise( (t, s) => {
            !function(t, e, s) {
                s || (s = e,
                e = {}),
                "function" != typeof s && iB(7);
                var i = []
                  , r = function() {
                    for (var t = 0; t < i.length; ++t)
                        i[t]()
                }
                  , a = {}
                  , n = function(t, e) {
                    r_(function() {
                        s(t, e)
                    })
                };
                r_(function() {
                    n = s
                });
                for (var o = t.length - 22; 0x6054b50 != rr(t, o); --o)
                    if (!o || t.length - o > 65558)
                        return n(iB(13, 0, 1), null),
                        r;
                var h = ri(t, o + 8);
                if (h) {
                    var l = h
                      , p = rr(t, o + 16)
                      , m = 0xffffffff == p || 65535 == l;
                    if (m) {
                        var d = rr(t, o - 12);
                        (m = 0x6064b50 == rr(t, d)) && (l = h = rr(t, d + 32),
                        p = rr(t, d + 48))
                    }
                    for (var c = e && e.filter, u = 0; u < l; ++u)
                        !function(e) {
                            var s = rf(t, p, m)
                              , o = s[0]
                              , l = s[1]
                              , d = s[2]
                              , u = s[3]
                              , f = s[4]
                              , g = s[5]
                              , y = g + 30 + ri(t, g + 26) + ri(t, g + 28);
                            p = f;
                            var b = function(t, e) {
                                t ? (r(),
                                n(t, null)) : (e && (a[u] = e),
                                --h || n(null, a))
                            };
                            if (!c || c({
                                name: u,
                                size: l,
                                originalSize: d,
                                compression: o
                            }))
                                if (o)
                                    if (8 == o) {
                                        var v, _, w = t.subarray(y, y + l);
                                        if (d < 524288 || l > .8 * d)
                                            try {
                                                b(null, rh(w, {
                                                    out: new im(d)
                                                }))
                                            } catch (t) {
                                                b(t, null)
                                            }
                                        else
                                            i.push((v = {
                                                size: d
                                            },
                                            (_ = b) || (_ = v,
                                            v = {}),
                                            "function" != typeof _ && iB(7),
                                            rs(w, v, [i9], function(t) {
                                                return rt(rh(t.data[0], re(t.data[1])))
                                            }, 1, _)))
                                    } else
                                        b(iB(14, "unknown compression type " + o, 1), null);
                                else
                                    b(null, iz(t, y, y + l));
                            else
                                b(null, null)
                        }(0)
                } else
                    n(null, {})
            }(e, (e, i) => {
                e && s(e),
                t(i)
            }
            )
        }
        )
    }
      , rz = t => t.replaceAll(RegExp(/"""/, "g"), '""').replaceAll(/(["'])(.*?)\1/g, (t, e, s) => `${e}${s}${e}`);
    async function rR(t) {
        let e = await rV(t)
          , s = (t => {
            let e = JSON.parse(ru(t["manifest.json"], !1));
            if (!("animations"in e))
                throw Error("Manifest not found");
            if (0 === e.animations.length)
                throw Error("No animations listed in manifest");
            return e
        }
        )(e)
          , i = []
          , r = []
          , {length: a} = s.animations
          , n = "animations";
        e[`a/${s.animations[0]?.id}.json`] && (n = "a");
        for (let t = 0; t < a; t++) {
            let a = JSON.parse(rz(ru(e[`${n}/${s.animations[t]?.id}.json`])))
              , {length: o} = a.layers;
            for (let t = 0; t < o; t++) {
                let {ks: e} = a.layers[t] ?? {}
                  , s = Object.keys(e)
                  , {length: i} = s;
                for (let r = 0; r < i; r++) {
                    let {e: i, x: n} = e?.[s[r]];
                    if (!n || !i)
                        continue;
                    let o = a.layers[t]?.ks[s[r]];
                    o && (o.x = atob(n))
                }
            }
            r.push(rO(e, a.assets)),
            i.push(a)
        }
        return await Promise.all(r),
        {
            data: i,
            manifest: s
        }
    }
    async function rB(t) {
        try {
            if (!t || "string" != typeof t && "object" != typeof t)
                throw Error("Broken file or invalid file format");
            if ("string" != typeof t)
                return {
                    animations: Array.isArray(t) ? t : [t],
                    isDotLottie: !1,
                    manifest: null
                };
            let e = await fetch(t, {
                headers: {
                    "Content-Type": "application/json; charset=UTF-8"
                }
            });
            if (!e.ok)
                throw Error(e.statusText);
            let s = !0
              , i = e.headers.get("content-type");
            if ("application/zip+dotlottie" === i && (s = !1),
            s) {
                let s = rE(t);
                if ("json" === s)
                    return {
                        animations: [await e.json()],
                        isDotLottie: !1,
                        manifest: null
                    };
                let i = await e.clone().text();
                try {
                    return {
                        animations: [JSON.parse(i)],
                        isDotLottie: !1,
                        manifest: null
                    }
                } catch (t) {}
            }
            let {data: r, manifest: a} = await rR(e);
            return {
                animations: r,
                isDotLottie: !0,
                manifest: a
            }
        } catch (t) {
            return console.error(t),
            {
                animations: void 0,
                isDotLottie: !1,
                manifest: null
            }
        }
    }
    async function rq({animations: t, currentAnimation: e=0, fileName: s, generator: i, isDotLottie: r, manifest: a, shouldDownload: n=!0, src: o, typeCheck: h}) {
        try {
            if (!o && !t?.length)
                throw Error("No animation to convert");
            let l = t;
            if (l || (l = (await rB(o)).animations ?? []),
            h || r) {
                let t = rM(s || o || "converted");
                return l.length > 1 && (t += `-${e + 1}`),
                t += ".json",
                {
                    result: function({animation: t, fileName: e, shouldDownload: s}) {
                        try {
                            if (!t)
                                throw Error("createJSON: Missing or malformed required parameter(s):\n - animation\n'");
                            let i = rx("json", e) || `${rw()}.json`
                              , r = JSON.stringify(t);
                            if (s)
                                return rS(r, {
                                    mimeType: "application/json",
                                    name: i
                                }),
                                null;
                            return r
                        } catch (t) {
                            return console.error(t),
                            null
                        }
                    }({
                        animation: l[e],
                        fileName: t,
                        shouldDownload: n
                    }),
                    success: !0
                }
            }
            return {
                result: await rF({
                    animations: l,
                    fileName: `${rM(s || o || "converted")}.lottie`,
                    manifest: {
                        ...a ?? a,
                        generator: i
                    },
                    shouldDownload: n
                }),
                success: !0
            }
        } catch (t) {
            return {
                error: t.message,
                success: !1
            }
        }
    }
    let rj = Symbol("UPDATE_ON_CONNECTED");
    k && (global.HTMLElement = class {
    }
    );
    class rH extends HTMLElement {
        constructor() {
            super(),
            rj in this && (this[rj] = []);
            let {observedProperties: t=[]} = this.constructor
              , {length: e} = t;
            for (let s = 0; s < e; s++) {
                let e = this[t[s]]
                  , i = Symbol(t[s]);
                this[i] = e,
                Object.defineProperty(this, t[s] ?? "", {
                    get() {
                        return this[i]
                    },
                    set(e) {
                        let r = this[i];
                        this[i] = e,
                        this.propertyChangedCallback(t[s], r, e)
                    }
                }),
                void 0 !== e && rj in this && Array.isArray(this[rj]) && this[rj].push(t[s])
            }
        }
        connectedCallback() {
            let t = [];
            rj in this && Array.isArray(this[rj]) && (t = this[rj]);
            let {length: e} = t;
            for (let s = 0; s < e; s++)
                "propertyChangedCallback"in this && "function" == typeof this.propertyChangedCallback && (t[s] ?? ""in this) && this.propertyChangedCallback(t[s] ?? "", void 0, this[t[s]])
        }
        propertyChangedCallback(t, e, s) {
            throw Error(`${this.constructor.name}: Method propertyChangedCallback is not implemented`)
        }
    }
    let rG = `
  <svg width="24" height="24" aria-hidden="true" focusable="false">
    <path
      d="m11.8 13.2-.3.3c-.5.5-1.1 1.1-1.7 1.5-.5.4-1 .6-1.5.8-.5.2-1.1.3-1.6.3s-1-.1-1.5-.3c-.6-.2-1-.5-1.4-1-.5-.6-.8-1.2-.9-1.9-.2-.9-.1-1.8.3-2.6.3-.7.8-1.2 1.3-1.6.3-.2.6-.4 1-.5.2-.2.5-.2.8-.3.3 0 .7-.1 1 0 .3 0 .6.1.9.2.9.3 1.7.9 2.4 1.5.4.4.8.7 1.1 1.1l.1.1.4-.4c.6-.6 1.2-1.2 1.9-1.6.5-.3 1-.6 1.5-.7.4-.1.7-.2 1-.2h.9c1 .1 1.9.5 2.6 1.4.4.5.7 1.1.8 1.8.2.9.1 1.7-.2 2.5-.4.9-1 1.5-1.8 2-.4.2-.7.4-1.1.4-.4.1-.8.1-1.2.1-.5 0-.9-.1-1.3-.3-.8-.3-1.5-.9-2.1-1.5-.4-.4-.8-.7-1.1-1.1h-.3zm-1.1-1.1c-.1-.1-.1-.1 0 0-.3-.3-.6-.6-.8-.9-.5-.5-1-.9-1.6-1.2-.4-.3-.8-.4-1.3-.4-.4 0-.8 0-1.1.2-.5.2-.9.6-1.1 1-.2.3-.3.7-.3 1.1 0 .3 0 .6.1.9.1.5.4.9.8 1.2.5.4 1.1.5 1.7.5.5 0 1-.2 1.5-.5.6-.4 1.1-.8 1.6-1.3.1-.3.3-.5.5-.6zM13 12c.5.5 1 1 1.5 1.4.5.5 1.1.9 1.9 1 .4.1.8 0 1.2-.1.3-.1.6-.3.9-.5.4-.4.7-.9.8-1.4.1-.5 0-.9-.1-1.4-.3-.8-.8-1.2-1.7-1.4-.4-.1-.8-.1-1.2 0-.5.1-1 .4-1.4.7-.5.4-1 .8-1.4 1.2-.2.2-.4.3-.5.5z"
    />
  </svg>
`
      , rW = `
  <svg
    width="24"
    height="24"
    aria-hidden="true"
    focusable="false"
  >
    <path
      d="M17.016 17.016v-4.031h1.969v6h-12v3l-3.984-3.984 3.984-3.984v3h10.031zM6.984 6.984v4.031H5.015v-6h12v-3l3.984 3.984-3.984 3.984v-3H6.984z"
    />
  </svg>
`
      , rU = `
  <svg
    width="24"
    height="24"
    aria-hidden="true"
    focusable="false"
  >
    <path
      d="M16.8 10.8 12 15.6l-4.8-4.8h3V3.6h3.6v7.2h3zM12 15.6H3v4.8h18v-4.8h-9zm7.8 2.4h-2.4v-1.2h2.4V18z"
    />
  </svg>
`
      , rY = `
  <svg width="24" height="24" aria-hidden="true" focusable="false">
    <path
      d="M17.016 17.016v-4.031h1.969v6h-12v3l-3.984-3.984 3.984-3.984v3h10.031zM6.984 6.984v4.031H5.015v-6h12v-3l3.984 3.984-3.984 3.984v-3H6.984z"
    />
  </svg>
`
      , rX = `
  <svg width="24" height="24" aria-hidden="true" focusable="false">
    <path d="m6.1 5.8 9.8 6.2-9.8 6.2V5.8zM16.4 5.8h1.5v12.4h-1.5z" />
  </svg>
`
      , rJ = `
  <svg width="24" height="24" aria-hidden="true" focusable="false">
    <path d="M8.016 5.016L18.985 12 8.016 18.984V5.015z" />
  </svg>
`
      , rZ = `
  <svg width="24" height="24" aria-hidden="true" focusable="false">
    <path d="M17.9 18.2 8.1 12l9.8-6.2v12.4zm-10.3 0H6.1V5.8h1.5v12.4z" />
  </svg>
`
      , rK = `
  <svg width="24" height="24" aria-hidden="true" focusable="false">
    <circle cx="12" cy="5.4" r="2.5" />
    <circle cx="12" cy="12" r="2.5" />
    <circle cx="12" cy="18.6" r="2.5" />
  </svg>
`
      , rQ = `
  <svg width="24" height="24" aria-hidden="true" focusable="false">
    <path d="M6 6h12v12H6V6z" />
  </svg>
`;
    var r0 = ((b = {}).Contain = "contain",
    b.Cover = "cover",
    b.Fill = "fill",
    b.None = "none",
    b.ScaleDown = "scale-down",
    b)
      , r1 = ((v = {}).Completed = "completed",
    v.Destroyed = "destroyed",
    v.Error = "error",
    v.Frozen = "frozen",
    v.Loading = "loading",
    v.Paused = "paused",
    v.Playing = "playing",
    v.Stopped = "stopped",
    v);
    let r2 = "dotlottie-player";
    function r3() {
        if (!this.shadow)
            throw Error("No Shadow Element");
        let t = this.shadow.querySelector("slot[name=controls]");
        if (!t)
            return;
        if (!this.controls) {
            t.innerHTML = "";
            return
        }
        t.innerHTML = `<div class="lottie-controls toolbar ${this.playerState === r1.Error ? "has-error" : ""}" aria-label="Lottie Animation controls"><button class="togglePlay" data-active="${this.autoplay}" aria-label="Toggle Play/Pause">${rJ}</button> <button class="stop" data-active="${!this.autoplay}" aria-label="Stop">${rQ}</button> <button class="prev" aria-label="Previous animation" hidden="false">${rZ}</button> <button class="next" aria-label="Next animation" hidden>${rX}</button><form class="progress-container${this.simple ? " simple" : ""}"><input type="range" class="seeker" min="0" max="100" step="1" value="${this._seeker.toString()}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="${this._seeker.toString()}" tabindex="0" aria-label="Slider for search"><progress max="100" value="${this._seeker}"></progress></form>${this.simple ? "" : `<button class="toggleLoop" data-active="${this.loop}" tabindex="0" aria-label="Toggle loop">${rY}</button> <button class="toggleBoomerang" data-active="${this.mode === _.Bounce}" aria-label="Toggle boomerang" tabindex="0">${rG}</button> <button class="toggleSettings" aria-label="Settings" aria-haspopup="true" aria-expanded="${this._isSettingsOpen}" aria-controls="${this._identifier}-settings">${rK}</button><div id="${this._identifier}-settings" class="popover" hidden><button class="convert" aria-label="Convert JSON animation to dotLottie format" aria-label="Convert ${this.isDotLottie ? "dotLottie animation to JSON format" : "JSON animation to dotLottie format"}" hidden>${rW} ${this.isDotLottie ? "Convert to JSON" : "Convert to dotLottie"}</button> <button class="snapshot" aria-label="Download still image">${rU} Download still image</button></div>`}</div>`;
        let e = this.shadow.querySelector(".togglePlay");
        e instanceof HTMLButtonElement && (e.onclick = this.togglePlay);
        let s = this.shadow.querySelector(".stop");
        s instanceof HTMLButtonElement && (s.onclick = this.stop);
        let i = this.shadow.querySelector(".prev");
        i instanceof HTMLButtonElement && (this.animations.length > 0 && this.currentAnimation && (i.hidden = !1),
        i.onclick = this.prev);
        let r = this.shadow.querySelector(".next");
        r instanceof HTMLButtonElement && (this.animations.length > 0 && this.currentAnimation < this.animations.length - 1 && (r.hidden = !1),
        r.onclick = this.next);
        let a = this.shadow.querySelector(".seeker");
        if (a instanceof HTMLInputElement && (a.onchange = this._handleSeekChange,
        a.onmousedown = this._freeze),
        !this.simple) {
            let t = this.shadow.querySelector(".toggleLoop");
            t instanceof HTMLButtonElement && (t.onclick = this.toggleLoop);
            let e = this.shadow.querySelector(".toggleBoomerang");
            e instanceof HTMLButtonElement && (e.onclick = this.toggleBoomerang);
            let s = this.shadow.querySelector(".convert");
            s instanceof HTMLButtonElement && (s.onclick = () => {
                rq({
                    isDotLottie: this.isDotLottie,
                    manifest: this.getManifest(),
                    src: this.src || this.source
                })
            }
            );
            let i = this.shadow.querySelector(".snapshot");
            i instanceof HTMLButtonElement && (i.onclick = () => this.snapshot(!0));
            let r = this.shadow.querySelector(".toggleSettings");
            r instanceof HTMLButtonElement && (r.onclick = this._handleSettingsClick,
            r.onblur = this._handleBlur)
        }
    }
    let r5 = `
  <svg width="24" height="24" aria-hidden="true" focusable="false">
    <path
      d="M14.016 5.016H18v13.969h-3.984V5.016zM6 18.984V5.015h3.984v13.969H6z"
    />
  </svg>
`;
    async function r4() {
        if (!this.shadow || !this.template)
            throw Error("No Shadow Element or Template");
        this.template.innerHTML = `<div class="animation-container main" data-controls="${this.controls ?? !1}" lang="${this.description ? document.documentElement.lang : "en"}" aria-label="${this.description ?? "Lottie animation"}" data-loaded="${this._playerState.loaded}"><figure class="animation" style="background:${this.background}">${this.playerState === r1.Error ? `<div class="error"><svg preserveAspectRatio="${S.Cover}" xmlns="http://www.w3.org/2000/svg" width="1920" height="1080" viewBox="0 0 1920 1080" style="white-space:preserve"><path fill="#fff" d="M0 0h1920v1080H0z"/><path fill="#3a6d8b" d="M1190.2 531 1007 212.4c-22-38.2-77.2-38-98.8.5L729.5 531.3c-21.3 37.9 6.1 84.6 49.5 84.6l361.9.3c43.7 0 71.1-47.3 49.3-85.2zM937.3 288.7c.2-7.5 3.3-23.9 23.2-23.9 16.3 0 23 16.1 23 23.5 0 55.3-10.7 197.2-12.2 214.5-.1 1-.9 1.7-1.9 1.7h-18.3c-1 0-1.8-.7-1.9-1.7-1.4-17.5-13.4-162.9-11.9-214.1zm24.2 283.8c-13.1 0-23.7-10.6-23.7-23.7s10.6-23.7 23.7-23.7 23.7 10.6 23.7 23.7-10.6 23.7-23.7 23.7zM722.1 644h112.6v34.4h-70.4V698h58.8v31.7h-58.8v22.6h72.4v36.2H722.1V644zm162 57.1h.6c8.3-12.9 18.2-17.8 31.3-17.8 3 0 5.1.4 6.3 1v32.6h-.8c-22.4-3.8-35.6 6.3-35.6 29.5v42.3h-38.2V685.5h36.4v15.6zm78.9 0h.6c8.3-12.9 18.2-17.8 31.3-17.8 3 0 5.1.4 6.3 1v32.6h-.8c-22.4-3.8-35.6 6.3-35.6 29.5v42.3h-38.2V685.5H963v15.6zm39.5 36.2c0-31.3 22.2-54.8 56.6-54.8 34.4 0 56.2 23.5 56.2 54.8s-21.8 54.6-56.2 54.6c-34.4-.1-56.6-23.3-56.6-54.6zm74 0c0-17.4-6.1-29.1-17.8-29.1-11.7 0-17.4 11.7-17.4 29.1 0 17.4 5.7 29.1 17.4 29.1s17.8-11.8 17.8-29.1zm83.1-36.2h.6c8.3-12.9 18.2-17.8 31.3-17.8 3 0 5.1.4 6.3 1v32.6h-.8c-22.4-3.8-35.6 6.3-35.6 29.5v42.3h-38.2V685.5h36.4v15.6z"/><path fill="none" d="M718.9 807.7h645v285.4h-645z"/><text fill="#3a6d8b" style="text-align:center;position:absolute;left:100%;font-size:47px;font-family:system-ui,-apple-system,BlinkMacSystemFont,'.SFNSText-Regular',sans-serif" x="50%" y="848.017" text-anchor="middle">${this._errorMessage}</text></svg></div>` : ""}</figure><slot name="controls"></slot></div>`,
        this.shadow.adoptedStyleSheets = [await r9.styles()],
        this.shadow.appendChild(this.template.content.cloneNode(!0))
    }
    let r6 = t => {
        let e = {
            message: "Unknown error",
            status: k ? 500 : 400
        };
        return t && "object" == typeof t && ("message"in t && "string" == typeof t.message && (e.message = t.message),
        "status"in t && (e.status = Number(t.status))),
        e
    }
      , r8 = "Method is not implemented";
    class r9 extends rH {
        static get observedAttributes() {
            return ["animateOnScroll", "autoplay", "controls", "direction", "hover", "loop", "mode", "playOnClick", "playOnVisible", "selector", "speed", "src", "subframe"]
        }
        static get observedProperties() {
            return ["playerState", "_isSettingsOpen", "_seeker", "_currentAnimation", "_animations"]
        }
        static get styles() {
            return async () => {
                let t = new CSSStyleSheet;
                return await t.replace("* {\n  box-sizing: border-box;\n}\n\n:host {\n  --lottie-player-toolbar-height: 35px;\n  --lottie-player-toolbar-background-color: #fff;\n  --lottie-player-toolbar-icon-color: #000;\n  --lottie-player-toolbar-icon-hover-color: #000;\n  --lottie-player-toolbar-icon-active-color: #4285f4;\n  --lottie-player-seeker-track-color: rgb(0 0 0 / 20%);\n  --lottie-player-seeker-thumb-color: #4285f4;\n  --lottie-player-seeker-display: block;\n\n  width: 100%;\n  height: 100%;\n\n  &:not([hidden]) {\n    display: block;\n  }\n\n  .main {\n    display: flex;\n    flex-direction: column;\n    height: 100%;\n    width: 100%;\n    margin: 0;\n    padding: 0;\n  }\n\n  .animation {\n    width: 100%;\n    height: 100%;\n    display: flex;\n    margin: 0;\n    padding: 0;\n  }\n\n  [data-controls='true'] .animation {\n    height: calc(100% - 35px);\n  }\n\n  .animation-container {\n    position: relative;\n  }\n\n  .popover {\n    position: absolute;\n    right: 5px;\n    bottom: 40px;\n    background-color: var(--lottie-player-toolbar-background-color);\n    border-radius: 5px;\n    padding: 10px 15px;\n    border: solid 2px var(--lottie-player-toolbar-icon-color);\n    animation: fade-in 0.2s ease-in-out;\n\n    &::before {\n      content: '';\n      right: 10px;\n      border: 7px solid transparent;\n      margin-right: -7px;\n      height: 0;\n      width: 0;\n      position: absolute;\n      pointer-events: none;\n      top: 100%;\n      border-top-color: var(--lottie-player-toolbar-icon-color);\n    }\n  }\n\n  .error {\n    display: flex;\n    margin: auto;\n    justify-content: center;\n    height: 100%;\n    align-items: center;\n\n    & svg {\n      width: 100%;\n      height: auto;\n    }\n  }\n\n  .toolbar {\n    display: flex;\n    place-items: center center;\n    background: var(--lottie-player-toolbar-background-color);\n    margin: 0;\n    height: 35px;\n    padding: 5px;\n    border-radius: 5px;\n    gap: 5px;\n\n    &.has-error {\n      pointer-events: none;\n      opacity: 0.5;\n    }\n\n    & button {\n      cursor: pointer;\n      fill: var(--lottie-player-toolbar-icon-color);\n      color: var(--lottie-player-toolbar-icon-color);\n      background: none;\n      border: 0;\n      padding: 0;\n      outline: 0;\n      height: 100%;\n      margin: 0;\n      align-items: center;\n      gap: 5px;\n      opacity: 0.9;\n\n      &:not([hidden]) {\n        display: flex;\n      }\n\n      &:hover {\n        opacity: 1;\n      }\n\n      &[data-active='true'] {\n        opacity: 1;\n        fill: var(--lottie-player-toolbar-icon-active-color);\n      }\n\n      &:disabled {\n        opacity: 0.5;\n      }\n\n      &:focus {\n        outline: 0;\n      }\n\n      & svg {\n        pointer-events: none;\n\n        & > * {\n          fill: inherit;\n        }\n      }\n\n      &.disabled svg {\n        display: none;\n      }\n    }\n  }\n\n  .progress-container {\n    position: relative;\n    width: 100%;\n\n    &.simple {\n      margin-right: 12px;\n    }\n  }\n\n  .seeker {\n    appearance: none;\n    outline: none;\n    width: 100%;\n    height: 20px;\n    border-radius: 3px;\n    border: 0;\n    cursor: pointer;\n    background-color: transparent;\n\n    display: var(--lottie-player-seeker-display);\n    color: var(--lottie-player-seeker-thumb-color);\n    margin: 0;\n    padding: 7.5px 0;\n    position: relative;\n    z-index: 1;\n\n    &::-webkit-slider-runnable-track,\n    &::-webkit-slider-thumb {\n      appearance: none;\n      outline: none;\n    }\n\n    &::-webkit-slider-thumb {\n      height: 15px;\n      width: 15px;\n      border-radius: 50%;\n      border: 0;\n      background-color: var(--lottie-player-seeker-thumb-color);\n      cursor: pointer;\n      -webkit-transition: transform 0.2s ease-in-out;\n      transition: transform 0.2s ease-in-out;\n      transform: scale(0);\n    }\n\n    &:hover::-webkit-slider-thumb,\n    &:focus::-webkit-slider-thumb {\n      transform: scale(1);\n    }\n\n    &::-moz-range-progress {\n      background-color: var(--lottie-player-seeker-thumb-color);\n      height: 5px;\n      border-radius: 3px;\n    }\n\n    &::-moz-range-thumb {\n      height: 15px;\n      width: 15px;\n      border-radius: 50%;\n      background-color: var(--lottie-player-seeker-thumb-color);\n      border: 0;\n      cursor: pointer;\n      -moz-transition: transform 0.2s ease-in-out;\n      transition: transform 0.2s ease-in-out;\n      transform: scale(0);\n    }\n\n    &:hover::-moz-range-thumb,\n    &:focus::-moz-range-thumb {\n      transform: scale(1);\n    }\n\n    &::-ms-track {\n      width: 100%;\n      height: 5px;\n      cursor: pointer;\n      background: transparent;\n      border-color: transparent;\n      color: transparent;\n    }\n\n    &::-ms-fill-upper {\n      background: var(--lottie-player-seeker-track-color);\n      border-radius: 3px;\n    }\n\n    &::-ms-fill-lower {\n      background-color: var(--lottie-player-seeker-thumb-color);\n      border-radius: 3px;\n    }\n\n    &::-ms-thumb {\n      border: 0;\n      height: 15px;\n      width: 15px;\n      border-radius: 50%;\n      background: var(--lottie-player-seeker-thumb-color);\n      cursor: pointer;\n      -ms-transition: transform 0.2s ease-in-out;\n      transition: transform 0.2s ease-in-out;\n      transform: scale(0);\n    }\n\n    &:hover::-ms-thumb {\n      transform: scale(1);\n    }\n\n    &:focus {\n      &::-ms-thumb {\n        transform: scale(1);\n      }\n\n      &::-ms-fill-lower,\n      &::-ms-fill-upper {\n        background: var(--lottie-player-seeker-track-color);\n      }\n    }\n  }\n\n  & progress {\n    appearance: none;\n    outline: none;\n    position: absolute;\n    width: 100%;\n    height: 5px;\n    border-radius: 3px;\n    border: 0;\n    top: 0;\n    left: 0;\n    margin: 7.5px 0;\n    background-color: var(--lottie-player-seeker-track-color);\n    pointer-events: none;\n\n    &::-webkit-progress-inner-element {\n      border-radius: 3px;\n      overflow: hidden;\n    }\n\n    &::-webkit-slider-runnable-track {\n      background-color: transparent;\n    }\n\n    &::-webkit-progress-value {\n      background-color: var(--lottie-player-seeker-thumb-color);\n    }\n  }\n\n  & *::-moz-progress-bar {\n    background-color: var(--lottie-player-seeker-thumb-color);\n  }\n}\n\n@keyframes fade-in {\n  0% {\n    opacity: 0;\n  }\n\n  100% {\n    opacity: 1;\n  }\n}\n\n@media (prefers-color-scheme: dark) {\n  :host {\n    --lottie-player-toolbar-background-color: #000;\n    --lottie-player-toolbar-icon-color: #fff;\n    --lottie-player-toolbar-icon-hover-color: #fff;\n    --lottie-player-seeker-track-color: rgb(255 255 255 / 60%);\n  }\n}\n"),
                t
            }
        }
        set animateOnScroll(t) {
            this.setAttribute("animateOnScroll", (!!t).toString())
        }
        get animateOnScroll() {
            let t = this.getAttribute("animateOnScroll");
            return "true" === t || "" === t || "1" === t
        }
        get animations() {
            return this._animations
        }
        set autoplay(t) {
            this.setAttribute("autoplay", (!!t).toString())
        }
        get autoplay() {
            let t = this.getAttribute("autoplay");
            return "true" === t || "" === t || "1" === t
        }
        set background(t) {
            this.setAttribute("background", t)
        }
        get background() {
            return this.getAttribute("background") || "transparent"
        }
        set controls(t) {
            this.setAttribute("controls", (!!t).toString())
        }
        get controls() {
            let t = this.getAttribute("controls");
            return "true" === t || "" === t || "1" === t
        }
        set count(t) {
            this.setAttribute("count", t.toString())
        }
        get count() {
            let t = this.getAttribute("count");
            return t ? Number(t) : 0
        }
        get currentAnimation() {
            return this._currentAnimation
        }
        set delay(t) {
            this.setAttribute("delay", t.toString())
        }
        get delay() {
            let t = this.getAttribute("delay");
            return t ? Number(t) : 0
        }
        set description(t) {
            t && this.setAttribute("description", t)
        }
        get description() {
            return this.getAttribute("description")
        }
        set direction(t) {
            this.setAttribute("direction", t.toString())
        }
        get direction() {
            let t = Number(this.getAttribute("direction"));
            return -1 === t ? t : 1
        }
        set dontFreezeOnBlur(t) {
            this.setAttribute("dontFreezeOnBlur", t.toString())
        }
        get dontFreezeOnBlur() {
            let t = this.getAttribute("dontFreezeOnBlur");
            return "true" === t || "" === t || "1" === t
        }
        set hover(t) {
            this.setAttribute("hover", t.toString())
        }
        get hover() {
            let t = this.getAttribute("hover");
            return "true" === t || "" === t || "1" === t
        }
        set intermission(t) {
            this.setAttribute("intermission", t.toString())
        }
        get intermission() {
            let t = Number(this.getAttribute("intermission"));
            return isNaN(t) ? 0 : t
        }
        get isDotLottie() {
            return this._isDotLottie
        }
        set loop(t) {
            this.setAttribute("loop", (!!t).toString())
        }
        get loop() {
            let t = this.getAttribute("loop");
            return "true" === t || "" === t || "1" === t
        }
        set mode(t) {
            this.setAttribute("mode", t)
        }
        get mode() {
            let t = this.getAttribute("mode");
            return t === _.Bounce ? t : _.Normal
        }
        set mouseout(t) {
            this.setAttribute("mouseout", t)
        }
        get mouseout() {
            let t = this.getAttribute("mouseout");
            switch (t) {
            case "void":
            case "pause":
            case "reverse":
                return t;
            default:
                return "stop"
            }
        }
        set objectfit(t) {
            this.setAttribute("objectfit", t)
        }
        get objectfit() {
            let t = this.getAttribute("objectfit");
            return t && Object.values(r0).includes(t) ? t : r0.Contain
        }
        set once(t) {
            this.setAttribute("once", t.toString())
        }
        get once() {
            let t = this.getAttribute("once");
            return "true" === t || "" === t || "1" === t
        }
        set playOnClick(t) {
            this.setAttribute("playOnClick", t.toString())
        }
        get playOnClick() {
            let t = this.getAttribute("playOnClick");
            return "true" === t || "" === t || "1" === t
        }
        set playOnVisible(t) {
            this.setAttribute("playOnVisible", t.toString())
        }
        get playOnVisible() {
            let t = this.getAttribute("playOnVisible");
            return "true" === t || "" === t || "1" === t
        }
        set preserveAspectRatio(t) {
            this.setAttribute("preserveAspectRatio", t || S.Contain)
        }
        get preserveAspectRatio() {
            let t = this.getAttribute("preserveAspectRatio");
            return t && Object.values(S).includes(t) ? t : null
        }
        set renderer(t) {
            this.setAttribute("renderer", t)
        }
        get renderer() {
            let t = this.getAttribute("renderer");
            return t === E.Canvas || t === E.HTML ? t : E.SVG
        }
        set selector(t) {
            if (t)
                return void this.setAttribute("selector", t);
            this.removeAttribute("selector")
        }
        get selector() {
            return this.getAttribute("selector")
        }
        set simple(t) {
            this.setAttribute("simple", t.toString())
        }
        get simple() {
            let t = this.getAttribute("simple");
            return "true" === t || "" === t || "1" === t
        }
        set speed(t) {
            this.setAttribute("speed", t.toString())
        }
        get speed() {
            let t = this.getAttribute("speed");
            return null === t || isNaN(Number(t)) ? 1 : Number(t)
        }
        set src(t) {
            this.setAttribute("src", t || "")
        }
        get src() {
            return this.getAttribute("src")
        }
        set subframe(t) {
            this.setAttribute("subframe", (!!t).toString())
        }
        get subframe() {
            let t = this.getAttribute("subframe");
            return "true" === t || "" === t || "1" === t
        }
        constructor() {
            super(),
            this.isLight = !1,
            this.playerState = r1.Loading,
            this._container = null,
            this._errorMessage = "Something went wrong",
            this._identifier = this.id || a(),
            this._isSettingsOpen = !1,
            this._playerState = {
                count: 0,
                loaded: !1,
                prev: r1.Loading,
                scrollTimeout: null,
                scrollY: 0,
                visible: !1
            },
            this._render = r4,
            this._renderControls = r3,
            this._seeker = 0,
            this._animations = [],
            this._currentAnimation = 0,
            this._isBounce = !1,
            this._isDotLottie = !1,
            this._lottieInstance = null,
            this._multiAnimationSettings = [],
            this._complete = this._complete.bind(this),
            this._dataFailed = this._dataFailed.bind(this),
            this._dataReady = this._dataReady.bind(this),
            this._DOMLoaded = this._DOMLoaded.bind(this),
            this._enterFrame = this._enterFrame.bind(this),
            this._freeze = this._freeze.bind(this),
            this._handleBlur = this._handleBlur.bind(this),
            this._handleClick = this._handleClick.bind(this),
            this._handleScroll = this._handleScroll.bind(this),
            this._handleSeekChange = this._handleSeekChange.bind(this),
            this._handleWindowBlur = this._handleWindowBlur.bind(this),
            this._loopComplete = this._loopComplete.bind(this),
            this._mouseEnter = this._mouseEnter.bind(this),
            this._mouseLeave = this._mouseLeave.bind(this),
            this._onVisibilityChange = this._onVisibilityChange.bind(this),
            this._switchInstance = this._switchInstance.bind(this),
            this._handleSettingsClick = this._handleSettingsClick.bind(this),
            this.togglePlay = this.togglePlay.bind(this),
            this.stop = this.stop.bind(this),
            this.prev = this.prev.bind(this),
            this.next = this.next.bind(this),
            this._renderControls = this._renderControls.bind(this),
            this.snapshot = this.snapshot.bind(this),
            this.toggleLoop = this.toggleLoop.bind(this),
            this.toggleBoomerang = this.toggleBoomerang.bind(this),
            this.destroy = this.destroy.bind(this),
            this.template = document.createElement("template"),
            this.shadow = this.attachShadow({
                mode: "open"
            })
        }
        addAnimation(t) {
            throw Error(r8)
        }
        async attributeChangedCallback(t, e, s) {
            if (this._lottieInstance && this.shadow && this._container)
                switch (t) {
                case "animateOnScroll":
                    if ("" === s || s) {
                        this._lottieInstance.autoplay = !1,
                        addEventListener("scroll", this._handleScroll, {
                            capture: !0,
                            passive: !0
                        });
                        return
                    }
                    removeEventListener("scroll", this._handleScroll, !0);
                    break;
                case "autoplay":
                    if (this.animateOnScroll || this.playOnVisible)
                        return;
                    if ("" === s || s)
                        return void this.play();
                    this.stop();
                    break;
                case "controls":
                    this._renderControls();
                    break;
                case "direction":
                    if (-1 === Number(s))
                        return void this.setDirection(-1);
                    this.setDirection(1);
                    break;
                case "hover":
                    if ("" === s || s) {
                        this._container.addEventListener("mouseenter", this._mouseEnter),
                        this._container.addEventListener("mouseleave", this._mouseLeave);
                        return
                    }
                    this._container.removeEventListener("mouseenter", this._mouseEnter),
                    this._container.removeEventListener("mouseleave", this._mouseLeave);
                    break;
                case "loop":
                    {
                        let t = this.shadow.querySelector(".toggleLoop");
                        t instanceof HTMLButtonElement && (t.dataset.active = s),
                        this.setLoop("" === s || !!s);
                        break
                    }
                case "mode":
                    {
                        let t = this.shadow.querySelector(".toggleBoomerang");
                        t instanceof HTMLButtonElement && (t.dataset.active = (s === _.Bounce).toString()),
                        this._isBounce = s === _.Bounce;
                        break
                    }
                case "playOnClick":
                    if ("" === s || s) {
                        this._lottieInstance.autoplay = !1,
                        this._container.addEventListener("click", this._handleClick);
                        return
                    }
                    this._container.removeEventListener("click", this._handleClick);
                    break;
                case "playOnVisible":
                    ("" === s || s) && (this._lottieInstance.autoplay = !1);
                    break;
                case "selector":
                    {
                        let t = document.getElementById(this.selector ?? "");
                        t?.addEventListener("click", this._handleClick);
                        break
                    }
                case "speed":
                    {
                        let t = Number(s);
                        t && !isNaN(t) && this.setSpeed(t);
                        break
                    }
                case "src":
                    await this.load(s);
                    break;
                case "subframe":
                    this.setSubframe("" === s || !!s)
                }
        }
        connectedCallback() {
            super.connectedCallback();
            try {
                (async () => {
                    if (await this._render(),
                    !this.shadow)
                        throw Error("Missing Shadow element");
                    this._container = this.shadow.querySelector(".animation"),
                    await this.load(this.src),
                    void 0 !== document.hidden && document.addEventListener("visibilitychange", this._onVisibilityChange),
                    this._addIntersectionObserver(),
                    this.dispatchEvent(new CustomEvent(w.Rendered))
                }
                )()
            } catch (t) {
                console.error(t),
                this.dispatchEvent(new CustomEvent(w.Error))
            }
        }
        convert(t) {
            throw Error(r8)
        }
        destroy() {
            this._lottieInstance?.destroy && (this.playerState = r1.Destroyed,
            this._lottieInstance.destroy(),
            this._lottieInstance = null,
            this.dispatchEvent(new CustomEvent(w.Destroyed)),
            this.remove(),
            document.removeEventListener("visibilitychange", this._onVisibilityChange))
        }
        disconnectedCallback() {
            this._intersectionObserver && (this._intersectionObserver.disconnect(),
            this._intersectionObserver = void 0),
            document.removeEventListener("visibilitychange", this._onVisibilityChange),
            this.destroy()
        }
        getLottie() {
            return this._lottieInstance
        }
        getManifest() {
            return this._manifest
        }
        getMultiAnimationSettings() {
            return this._multiAnimationSettings
        }
        getSegment() {
            return this._segment
        }
        async load(t) {
            try {
                if (!this.shadowRoot || !t)
                    return;
                this.source = t;
                let {animations: e, isDotLottie: s, manifest: i} = await rB(t);
                if (!e || e.some(t => !["v", "ip", "op", "layers", "fr", "w", "h"].every(e => Object.hasOwn(t, e))))
                    throw Error("Broken or corrupted file");
                let r = this.parentElement?.querySelector('script[type="application/ld+json"]');
                if (r) {
                    let t = JSON.parse(r.innerHTML);
                    t.selector && (this.selector = t.selector),
                    t.segment && this.setSegment(t.segment),
                    t.multiAnimationSettings && this.setMultiAnimationSettings(t.multiAnimationSettings)
                }
                this._isBounce = this.mode === _.Bounce,
                this._multiAnimationSettings.length > 0 && this._multiAnimationSettings[this._currentAnimation]?.mode && (this._isBounce = this._multiAnimationSettings[this._currentAnimation]?.mode === _.Bounce);
                let n = i?.animations[0];
                n && (n.autoplay = !this.animateOnScroll && !this.playOnVisible && this.autoplay,
                n.loop = this.loop),
                this._isDotLottie = s,
                this._animations = e,
                this._manifest = i ?? {
                    animations: [{
                        autoplay: !this.animateOnScroll && !this.playOnVisible && this.autoplay,
                        direction: this.direction,
                        id: a(),
                        loop: this.loop,
                        mode: this.mode,
                        speed: this.speed
                    }]
                },
                this._lottieInstance?.destroy(),
                this.playerState = r1.Stopped,
                !this.animateOnScroll && (this.autoplay || this._multiAnimationSettings[this._currentAnimation]?.autoplay || this.playOnVisible) && (this.playerState = r1.Playing),
                this._lottieInstance = this.loadAnimation({
                    ...this._getOptions(),
                    animationData: e[this._currentAnimation]
                }),
                this._addEventListeners();
                let o = this._multiAnimationSettings[this._currentAnimation]?.speed ?? this.speed
                  , h = this._multiAnimationSettings[this._currentAnimation]?.direction ?? this.direction;
                if (this._lottieInstance.setSpeed(o),
                this._lottieInstance.setDirection(h),
                this._lottieInstance.setSubframe(!!this.subframe),
                (this.autoplay || this.animateOnScroll || this.playOnVisible) && -1 === this.direction && this.seek("99%"),
                this._renderControls(),
                this.autoplay || this.playOnVisible) {
                    let t = this.shadow?.querySelector(".togglePlay");
                    t && (t.innerHTML = r5)
                }
            } catch (t) {
                console.error(t),
                this._errorMessage = r6(t).message,
                this.playerState = r1.Error,
                this.dispatchEvent(new CustomEvent(w.Error))
            }
        }
        loadAnimation(t) {
            throw Error(r8)
        }
        next() {
            this._currentAnimation++,
            this._switchInstance()
        }
        pause() {
            if (!this._lottieInstance)
                return;
            this._playerState.prev = this.playerState;
            let t = !1;
            try {
                this._lottieInstance.pause(),
                this.dispatchEvent(new CustomEvent(w.Pause))
            } catch (e) {
                t = !0,
                console.error(e)
            } finally {
                this.playerState = t ? r1.Error : r1.Paused
            }
        }
        play() {
            if (!this._lottieInstance)
                return;
            this._playerState.prev = this.playerState;
            let t = !1;
            try {
                this._lottieInstance.play(),
                this.dispatchEvent(new CustomEvent(w.Play))
            } catch (e) {
                t = !0,
                console.error(e)
            } finally {
                this.playerState = t ? r1.Error : r1.Playing
            }
        }
        prev() {
            this._currentAnimation--,
            this._switchInstance(!0)
        }
        propertyChangedCallback(t, e, s) {
            if (!this.shadow)
                return;
            let i = this.shadow.querySelector(".togglePlay")
              , r = this.shadow.querySelector(".stop")
              , a = this.shadow.querySelector(".prev")
              , n = this.shadow.querySelector(".next")
              , o = this.shadow.querySelector(".seeker")
              , h = this.shadow.querySelector("progress")
              , l = this.shadow.querySelector(".popover")
              , p = this.shadow.querySelector(".convert")
              , m = this.shadow.querySelector(".snapshot");
            i instanceof HTMLButtonElement && r instanceof HTMLButtonElement && n instanceof HTMLButtonElement && a instanceof HTMLButtonElement && o instanceof HTMLInputElement && h instanceof HTMLProgressElement && ("playerState" === t && (i.dataset.active = (s === r1.Playing || s === r1.Paused).toString(),
            r.dataset.active = (s === r1.Stopped).toString(),
            s === r1.Playing ? i.innerHTML = r5 : i.innerHTML = rJ),
            "_seeker" === t && "number" == typeof s && (o.value = s.toString(),
            o.ariaValueNow = s.toString(),
            h.value = s),
            "_animations" === t && Array.isArray(s) && this._currentAnimation + 1 < s.length && (n.hidden = !1),
            "_currentAnimation" === t && "number" == typeof s && (n.hidden = s + 1 >= this._animations.length,
            a.hidden = !s),
            "_isSettingsOpen" === t && "boolean" == typeof s && l instanceof HTMLDivElement && p instanceof HTMLButtonElement && m instanceof HTMLButtonElement && (l.hidden = !s,
            p.hidden = this.isLight,
            m.hidden = this.renderer !== E.SVG))
        }
        async reload() {
            this._lottieInstance && this.src && (this._lottieInstance.destroy(),
            await this.load(this.src))
        }
        seek(t) {
            if (!this._lottieInstance)
                return;
            let e = t.toString().match(/^(\d+)(%?)$/);
            if (!e)
                return;
            let s = Math.round("%" === e[2] ? this._lottieInstance.totalFrames * Number(e[1]) / 100 : Number(e[1]));
            if (this._seeker = s,
            this.playerState === r1.Playing || this.playerState === r1.Frozen && this._playerState.prev === r1.Playing) {
                this._lottieInstance.goToAndPlay(s, !0),
                this.playerState = r1.Playing;
                return
            }
            this._lottieInstance.goToAndStop(s, !0),
            this._lottieInstance.pause()
        }
        setCount(t) {
            this.count = t
        }
        setDirection(t) {
            this._lottieInstance && this._lottieInstance.setDirection(t)
        }
        setLoop(t) {
            this._lottieInstance && this._lottieInstance.setLoop(t)
        }
        setMultiAnimationSettings(t) {
            this._multiAnimationSettings = t
        }
        setSegment(t) {
            this._segment = t
        }
        setSpeed(t=1) {
            this._lottieInstance && this._lottieInstance.setSpeed(t)
        }
        setSubframe(t) {
            this._lottieInstance && this._lottieInstance.setSubframe(t)
        }
        snapshot(t=!0, e="AM Lottie") {
            try {
                var s;
                if (!this.shadowRoot)
                    throw Error("Unknown error");
                let i = this.shadowRoot.querySelector(".animation svg");
                if (!i)
                    throw Error("Could not retrieve animation from DOM");
                let r = i instanceof Node ? new XMLSerializer().serializeToString(i) : null;
                if (!r)
                    throw Error("Could not serialize SVG element");
                return t && ( (t, e) => {
                    let s = new Blob([t],{
                        type: e?.mimeType
                    })
                      , i = e?.name || a()
                      , r = URL.createObjectURL(s)
                      , n = document.createElement("a");
                    n.href = r,
                    n.download = i,
                    n.hidden = !0,
                    document.body.appendChild(n),
                    n.click(),
                    setTimeout( () => {
                        n.remove(),
                        URL.revokeObjectURL(r)
                    }
                    , 1e3)
                }
                )(r, {
                    mimeType: "image/svg+xml",
                    name: `${(s = this.src || e,
                    (t => {
                        if ("string" == typeof t && t && (t => {
                            let e = t?.split("/").pop()?.lastIndexOf(".");
                            return (e ?? 0) > 1 && t && t.length - 1 > (e ?? 0)
                        }
                        )(t))
                            return t.split(".").pop()?.toLowerCase()
                    }
                    )(s),
                    `${s.split("/").pop()?.replace(/\.[^.]*$/, "").replaceAll(/\W+/g, "-")}`)}-${((this._seeker ?? 0) + 1).toString().padStart(3, "0")}.svg`
                }),
                r
            } catch (t) {
                return console.error(t),
                null
            }
        }
        stop() {
            if (this._lottieInstance) {
                this._playerState.prev = this.playerState,
                this._playerState.count = 0;
                try {
                    this._lottieInstance.stop(),
                    this.dispatchEvent(new CustomEvent(w.Stop))
                } finally {
                    this.playerState = r1.Stopped
                }
            }
        }
        toggleBoomerang() {
            let t = this._multiAnimationSettings[this._currentAnimation] ?? {};
            if (void 0 !== t.mode) {
                if (t.mode === _.Normal) {
                    t.mode = _.Bounce,
                    this._isBounce = !0;
                    return
                }
                t.mode = _.Normal,
                this._isBounce = !1;
                return
            }
            if (this.mode === _.Normal) {
                this.mode = _.Bounce,
                this._isBounce = !0;
                return
            }
            this.mode = _.Normal,
            this._isBounce = !1
        }
        toggleLoop() {
            let t = !this.loop;
            this.loop = t,
            this.setLoop(t)
        }
        togglePlay() {
            if (!this._lottieInstance)
                return;
            let {currentFrame: t, playDirection: e, totalFrames: s} = this._lottieInstance;
            if (this.playerState === r1.Playing)
                return void this.pause();
            if (this.playerState !== r1.Completed)
                return void this.play();
            if (this.playerState = r1.Playing,
            this._isBounce) {
                this.setDirection(-1 * e),
                this._lottieInstance.goToAndPlay(t, !0);
                return
            }
            if (-1 === e)
                return void this._lottieInstance.goToAndPlay(s, !0);
            this._lottieInstance.goToAndPlay(0, !0)
        }
        _freeze() {
            if (this._lottieInstance) {
                this._playerState.prev = this.playerState;
                try {
                    this._lottieInstance.pause(),
                    this.dispatchEvent(new CustomEvent(w.Freeze))
                } finally {
                    this.playerState = r1.Frozen
                }
            }
        }
        _handleBlur() {
            setTimeout( () => {
                this._toggleSettings(!1)
            }
            , 200)
        }
        _handleClick() {
            (this.playOnClick || this.selector) && this.togglePlay()
        }
        _handleSeekChange({target: t}) {
            !(!(t instanceof HTMLInputElement) || !this._lottieInstance || isNaN(Number(t.value))) && this.seek(Math.round(Number(t.value) / 100 * this._lottieInstance.totalFrames))
        }
        _handleSettingsClick({target: t}) {
            this._toggleSettings(),
            t instanceof HTMLElement && t.focus()
        }
        setOptions(t) {
            throw Error("Method not implemented")
        }
        _addEventListeners() {
            this._toggleEventListeners("add")
        }
        _addIntersectionObserver() {
            if (this._container && !this._intersectionObserver) {
                if (!("IntersectionObserver"in window)) {
                    this._intersectionObserverFallback(),
                    removeEventListener("scroll", this._intersectionObserverFallback, !0),
                    addEventListener("scroll", this._intersectionObserverFallback, {
                        capture: !0,
                        passive: !0
                    });
                    return
                }
                this._intersectionObserver = new IntersectionObserver(t => {
                    let {length: e} = t;
                    for (let s = 0; s < e; s++) {
                        if (!t[s]?.isIntersecting || document.hidden) {
                            this.playerState === r1.Playing && this._freeze(),
                            this._playerState.visible = !1;
                            continue
                        }
                        this.animateOnScroll || this.playOnVisible || this.playerState !== r1.Frozen || this.play(),
                        this.playOnVisible && (this.playerState !== r1.Completed || this.once ? setTimeout( () => {
                            this.play()
                        }
                        , this.delay) : (this.playerState = r1.Playing,
                        this._lottieInstance?.goToAndPlay(1 === this.direction ? 0 : this._lottieInstance.totalFrames))),
                        !this._playerState.scrollY && (t[s]?.boundingClientRect.y || 0) > 0 && (this._playerState.scrollY = scrollY),
                        this._playerState.visible = !0
                    }
                }
                ),
                this._intersectionObserver.observe(this._container)
            }
        }
        _complete() {
            if (!this._lottieInstance)
                return;
            if (this._animations.length > 1) {
                if (this._multiAnimationSettings[this._currentAnimation + 1]?.autoplay)
                    return void this.next();
                if (this.loop && this._currentAnimation === this._animations.length - 1) {
                    this._currentAnimation = 0,
                    this._switchInstance();
                    return
                }
            }
            let {currentFrame: t, totalFrames: e} = this._lottieInstance;
            this._seeker = Math.round(t / e * 100),
            this.playerState = r1.Completed,
            this.dispatchEvent(new CustomEvent(w.Complete,{
                detail: {
                    frame: t,
                    seeker: this._seeker
                }
            }))
        }
        _dataFailed() {
            this.playerState = r1.Error,
            this.dispatchEvent(new CustomEvent(w.Error))
        }
        _dataReady() {
            this.dispatchEvent(new CustomEvent(w.Load))
        }
        _DOMLoaded() {
            this._playerState.loaded = !0,
            this.dispatchEvent(new CustomEvent(w.Ready))
        }
        _enterFrame() {
            if (!this._lottieInstance)
                return;
            let {currentFrame: t, totalFrames: e} = this._lottieInstance;
            this._seeker = Math.round(t / e * 100),
            this.dispatchEvent(new CustomEvent(w.Frame,{
                detail: {
                    frame: t,
                    seeker: this._seeker
                }
            }))
        }
        _getOptions() {
            if (!this._container)
                throw Error("Container not rendered");
            let t = this.preserveAspectRatio ?? (t => {
                switch (t) {
                case r0.Contain:
                case r0.ScaleDown:
                    return S.Contain;
                case r0.Cover:
                    return S.Cover;
                case r0.Fill:
                    return S.Initial;
                case r0.None:
                    return S.None;
                default:
                    return S.Contain
                }
            }
            )(this.objectfit)
              , e = this._multiAnimationSettings.length > 0 ? this._multiAnimationSettings[this._currentAnimation] : void 0
              , s = this._manifest?.animations[this._currentAnimation]
              , i = !!this.loop;
            s?.loop !== void 0 && (i = !!s.loop),
            e?.loop !== void 0 && (i = !!e.loop);
            let r = !!this.autoplay;
            s?.autoplay !== void 0 && (r = !!s.autoplay),
            e?.autoplay !== void 0 && (r = !!e.autoplay),
            this.animateOnScroll && (r = !1);
            let a = this._segment;
            return this._segment?.every(t => t > 0) && (a = [this._segment[0] - 1, this._segment[1] - 1]),
            this._segment?.some(t => t < 0) && (a = void 0),
            this.setOptions({
                container: this._container,
                hasAutoplay: r,
                hasLoop: i,
                initialSegment: a,
                preserveAspectRatio: t,
                rendererType: this.renderer
            })
        }
        _handleScroll() {
            if (this.animateOnScroll && this._lottieInstance) {
                if (k)
                    return void console.warn("DotLottie: Scroll animations might not work properly in a Server Side Rendering context. Try to wrap this in a client component.");
                if (this._playerState.visible) {
                    this._playerState.scrollTimeout && clearTimeout(this._playerState.scrollTimeout),
                    this._playerState.scrollTimeout = setTimeout( () => {
                        this.playerState = r1.Paused
                    }
                    , 400);
                    let t = Math.min(Math.max((scrollY > this._playerState.scrollY ? scrollY - this._playerState.scrollY : this._playerState.scrollY - scrollY) / 3, 1), 3 * this._lottieInstance.totalFrames) / 3;
                    requestAnimationFrame( () => {
                        t < (this._lottieInstance?.totalFrames ?? 0) ? (this.playerState = r1.Playing,
                        this._lottieInstance?.goToAndStop(t, !0)) : this.playerState = r1.Paused
                    }
                    )
                }
            }
        }
        _handleWindowBlur({type: t}) {
            this.dontFreezeOnBlur || (this.playerState === r1.Playing && "blur" === t && this._freeze(),
            this.playerState === r1.Frozen && "focus" === t && this.play())
        }
        _intersectionObserverFallback() {
            if (!this._container)
                return;
            let {bottom: t, left: e, right: s, top: i} = this._container.getBoundingClientRect();
            this._playerState.visible = i >= 0 && e >= 0 && t <= innerHeight && s <= innerWidth,
            (this.autoplay || this.playOnVisible || this.playerState === r1.Playing || this.playerState === r1.Frozen) && (this._playerState.visible ? this.play() : this._freeze())
        }
        _loopComplete() {
            if (!this._lottieInstance)
                return;
            let {playDirection: t, totalFrames: e} = this._lottieInstance
              , s = this._segment ? this._segment[0] : 0
              , i = this._segment ? this._segment[0] : e;
            if (this.count && (this._isBounce ? this._playerState.count += .5 : this._playerState.count += 1,
            this._playerState.count >= this.count)) {
                this.setLoop(!1),
                this.playerState = r1.Completed,
                this.dispatchEvent(new CustomEvent(w.Complete));
                return
            }
            return (this.dispatchEvent(new CustomEvent(w.Loop)),
            this._isBounce) ? (this._lottieInstance.goToAndStop(-1 === t ? s : .99 * i, !0),
            this._lottieInstance.setDirection(-1 * t),
            setTimeout( () => {
                this.animateOnScroll || this._lottieInstance?.play()
            }
            , this.intermission)) : (this._lottieInstance.goToAndStop(-1 === t ? .99 * i : s, !0),
            setTimeout( () => {
                this.animateOnScroll || this._lottieInstance?.play()
            }
            , this.intermission))
        }
        _mouseEnter() {
            if (!(!this.hover || !this._lottieInstance || "ontouchstart"in window)) {
                if ("reverse" === this.mouseout && this._lottieInstance.setDirection(1),
                this.playerState === r1.Completed) {
                    this._lottieInstance.goToAndPlay(0, !0),
                    this.playerState = r1.Playing;
                    return
                }
                this.playerState !== r1.Playing && this.play()
            }
        }
        _mouseLeave() {
            if (!(!this.hover || !this._lottieInstance || "ontouchstart"in window))
                switch (this.mouseout) {
                case "void":
                    break;
                case "pause":
                    this.pause();
                    break;
                case "reverse":
                    this._lottieInstance.setDirection(-1),
                    this.play();
                    break;
                default:
                    this.stop()
                }
        }
        _onVisibilityChange() {
            if (document.hidden && this.playerState === r1.Playing)
                return void this._freeze();
            this.playerState === r1.Frozen && this.play()
        }
        _removeEventListeners() {
            this._toggleEventListeners("remove")
        }
        _switchInstance(t=!1) {
            if (this._animations[this._currentAnimation])
                try {
                    if (this._lottieInstance && this._lottieInstance.destroy(),
                    this._lottieInstance = this.loadAnimation({
                        ...this._getOptions(),
                        animationData: this._animations[this._currentAnimation]
                    }),
                    this._multiAnimationSettings[this._currentAnimation]?.mode && (this._isBounce = this._multiAnimationSettings[this._currentAnimation]?.mode === _.Bounce),
                    this._removeEventListeners(),
                    this._addEventListeners(),
                    this.dispatchEvent(new CustomEvent(t ? w.Previous : w.Next)),
                    this._multiAnimationSettings[this._currentAnimation]?.autoplay ?? this.autoplay) {
                        if (this.animateOnScroll) {
                            this._lottieInstance.goToAndStop(0, !0),
                            this.playerState = r1.Paused;
                            return
                        }
                        this._lottieInstance.goToAndPlay(0, !0),
                        this.playerState = r1.Playing;
                        return
                    }
                    this._lottieInstance.goToAndStop(0, !0),
                    this.playerState = r1.Stopped
                } catch (t) {
                    this._errorMessage = r6(t).message,
                    this.playerState = r1.Error,
                    this.dispatchEvent(new CustomEvent(w.Error))
                }
        }
        _toggleEventListeners(t) {
            let e = "add" === t ? "addEventListener" : "removeEventListener";
            if (this._lottieInstance && (this._lottieInstance[e]("enterFrame", this._enterFrame),
            this._lottieInstance[e]("complete", this._complete),
            this._lottieInstance[e]("loopComplete", this._loopComplete),
            this._lottieInstance[e]("DOMLoaded", this._DOMLoaded),
            this._lottieInstance[e]("data_ready", this._dataReady),
            this._lottieInstance[e]("data_failed", this._dataFailed)),
            this.selector) {
                let t = document.getElementById(this.selector);
                t ? this.hover ? (t[e]("mouseenter", this._mouseEnter),
                t[e]("mouseleave", this._mouseLeave)) : t[e]("click", this._handleClick) : this.selector = null
            }
            this._container && !this.selector && (this.hover && (this._container[e]("mouseenter", this._mouseEnter),
            this._container[e]("mouseleave", this._mouseLeave)),
            this.playOnClick && this._container[e]("click", this._handleClick)),
            window[e]("focus", this._handleWindowBlur, {
                capture: !1,
                passive: !0
            }),
            window[e]("blur", this._handleWindowBlur, {
                capture: !1,
                passive: !0
            }),
            this.animateOnScroll && window[e]("scroll", this._handleScroll, {
                capture: !0,
                passive: !0
            })
        }
        _toggleSettings(t) {
            if (void 0 === t) {
                this._isSettingsOpen = !this._isSettingsOpen;
                return
            }
            this._isSettingsOpen = t
        }
    }
    class r7 extends r9 {
        get renderer() {
            return E.SVG
        }
        constructor() {
            super(),
            this.loadAnimation = io,
            this.isLight = !0
        }
        setOptions({container: t, hasAutoplay: e, hasLoop: s, initialSegment: i, preserveAspectRatio: r}) {
            return {
                autoplay: e,
                container: t,
                initialSegment: i,
                loop: s,
                renderer: E.SVG,
                rendererSettings: {
                    hideOnTransparent: !0,
                    imagePreserveAspectRatio: r,
                    preserveAspectRatio: r,
                    progressiveLoad: !0
                }
            }
        }
    }
    globalThis.dotLottiePlayer = () => new r7,
    k || customElements.define(r2, r7),
    t.PlayMode = _,
    t.PlayerEvents = w,
    t.PlayerState = r1,
    t.default = r7,
    t.tagName = r2,
    Object.defineProperty(t, "__esModule", {
        value: !0
    })
}(this["@aarsteinmedia/dotlottie-player"] = this["@aarsteinmedia/dotlottie-player"] || {});
