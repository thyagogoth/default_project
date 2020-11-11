! function(i) {
    if (i.document) {
        var e, d, t, n, r, o = i.document;
        o.querySelectorAll || (o.querySelectorAll = function(e) {
                var t, n = o.createElement("style"),
                    r = [];
                for (o.documentElement.firstChild.appendChild(n), o._qsa = [], n.styleSheet.cssText = e + "{x-qsa:expression(document._qsa && document._qsa.push(this))}", i.scrollBy(0, 0), n.parentNode.removeChild(n); o._qsa.length;)(t = o._qsa.shift()).style.removeAttribute("x-qsa"), r.push(t);
                return o._qsa = null, r
            }), o.querySelector || (o.querySelector = function(e) {
                var t = o.querySelectorAll(e);
                return t.length ? t[0] : null
            }), o.getElementsByClassName || (o.getElementsByClassName = function(e) {
                return e = String(e).replace(/^|\s+/g, "."), o.querySelectorAll(e)
            }), Object.keys || (Object.keys = function(e) {
                if (e !== Object(e)) throw TypeError("Object.keys called on non-object");
                var t, n = [];
                for (t in e) Object.prototype.hasOwnProperty.call(e, t) && n.push(t);
                return n
            }), Array.prototype.forEach || (Array.prototype.forEach = function(e) {
                if (null == this) throw TypeError();
                var t = Object(this),
                    n = t.length >>> 0;
                if ("function" != typeof e) throw TypeError();
                var r, i = arguments[1];
                for (r = 0; r < n; r++) r in t && e.call(i, t[r], r, t)
            }), d = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", (e = i).atob = e.atob || function(e) {
                var t = 0,
                    n = [],
                    r = 0,
                    i = 0;
                if ((e = (e = String(e)).replace(/\s/g, "")).length % 4 == 0 && (e = e.replace(/=+$/, "")), e.length % 4 == 1) throw Error("InvalidCharacterError");
                if (/[^+/0-9A-Za-z]/.test(e)) throw Error("InvalidCharacterError");
                for (; t < e.length;) r = r << 6 | d.indexOf(e.charAt(t)), 24 === (i += 6) && (n.push(String.fromCharCode(r >> 16 & 255)), n.push(String.fromCharCode(r >> 8 & 255)), n.push(String.fromCharCode(255 & r)), r = i = 0), t += 1;
                return 12 === i ? (r >>= 4, n.push(String.fromCharCode(255 & r))) : 18 === i && (r >>= 2, n.push(String.fromCharCode(r >> 8 & 255)), n.push(String.fromCharCode(255 & r))), n.join("")
            }, e.btoa = e.btoa || function(e) {
                e = String(e);
                var t, n, r, i, o, a, s, l = 0,
                    h = [];
                if (/[^\x00-\xFF]/.test(e)) throw Error("InvalidCharacterError");
                for (; l < e.length;) i = (t = e.charCodeAt(l++)) >> 2, o = (3 & t) << 4 | (n = e.charCodeAt(l++)) >> 4, a = (15 & n) << 2 | (r = e.charCodeAt(l++)) >> 6, s = 63 & r, l === e.length + 2 ? s = a = 64 : l === e.length + 1 && (s = 64), h.push(d.charAt(i), d.charAt(o), d.charAt(a), d.charAt(s));
                return h.join("")
            }, Object.prototype.hasOwnProperty || (Object.prototype.hasOwnProperty = function(e) {
                var t = this.__proto__ || this.constructor.prototype;
                return e in this && (!(e in t) || t[e] !== this[e])
            }),
            function() {
                if ("performance" in i == !1 && (i.performance = {}), Date.now = Date.now || function() {
                        return (new Date).getTime()
                    }, "now" in i.performance == !1) {
                    var e = Date.now();
                    performance.timing && performance.timing.navigationStart && (e = performance.timing.navigationStart), i.performance.now = function() {
                        return Date.now() - e
                    }
                }
            }(), i.requestAnimationFrame || (i.webkitRequestAnimationFrame && i.webkitCancelAnimationFrame ? ((r = i).requestAnimationFrame = function(e) {
                return webkitRequestAnimationFrame(function() {
                    e(r.performance.now())
                })
            }, r.cancelAnimationFrame = r.webkitCancelAnimationFrame) : i.mozRequestAnimationFrame && i.mozCancelAnimationFrame ? ((n = i).requestAnimationFrame = function(e) {
                return mozRequestAnimationFrame(function() {
                    e(n.performance.now())
                })
            }, n.cancelAnimationFrame = n.mozCancelAnimationFrame) : ((t = i).requestAnimationFrame = function(e) {
                return t.setTimeout(e, 1e3 / 60)
            }, t.cancelAnimationFrame = t.clearTimeout))
    }
}(this),
function(e, t) {
    "object" == typeof exports && "object" == typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define([], t) : "object" == typeof exports ? exports.Holder = t() : e.Holder = t()
}(this, function() {
    return i = {}, n.m = r = [function(e, t, n) {
        e.exports = n(1)
    }, function(s, e, z) {
        (function(h) {
            var e = z(2),
                u = z(3),
                E = z(6),
                m = z(7),
                v = z(8),
                y = z(9),
                T = z(10),
                t = z(11),
                d = z(12),
                c = z(15),
                g = m.extend,
                w = m.dimensionCheck,
                b = t.svg_ns,
                r = {
                    version: t.version,
                    addTheme: function(e, t) {
                        return null != e && null != t && (k.settings.themes[e] = t), delete k.vars.cache.themeKeys, this
                    },
                    addImage: function(r, e) {
                        return y.getNodeArray(e).forEach(function(e) {
                            var t = y.newEl("img"),
                                n = {};
                            n[k.setup.dataAttr] = r, y.setAttr(t, n), e.appendChild(t)
                        }), this
                    },
                    setResizeUpdate: function(e, t) {
                        e.holderData && (e.holderData.resizeUpdate = !!t, e.holderData.resizeUpdate && S(e))
                    },
                    run: function(e) {
                        e = e || {};
                        var d = {},
                            c = g(k.settings, e);
                        k.vars.preempted = !0, k.vars.dataAttr = c.dataAttr || k.setup.dataAttr, d.renderer = c.renderer ? c.renderer : k.setup.renderer, -1 === k.setup.renderers.join(",").indexOf(d.renderer) && (d.renderer = k.setup.supportsSVG ? "svg" : k.setup.supportsCanvas ? "canvas" : "html");
                        var t = y.getNodeArray(c.images),
                            n = y.getNodeArray(c.bgnodes),
                            r = y.getNodeArray(c.stylenodes),
                            i = y.getNodeArray(c.objects);
                        return d.stylesheets = [], d.svgXMLStylesheet = !0, d.noFontFallback = !!c.noFontFallback, d.noBackgroundSize = !!c.noBackgroundSize, r.forEach(function(e) {
                            if (e.attributes.rel && e.attributes.href && "stylesheet" == e.attributes.rel.value) {
                                var t = e.attributes.href.value,
                                    n = y.newEl("a");
                                n.href = t;
                                var r = n.protocol + "//" + n.host + n.pathname + n.search;
                                d.stylesheets.push(r)
                            }
                        }), n.forEach(function(e) {
                            if (h.getComputedStyle) {
                                var t = h.getComputedStyle(e, null).getPropertyValue("background-image"),
                                    n = e.getAttribute("data-background-src") || t,
                                    r = null,
                                    i = c.domain + "/",
                                    o = n.indexOf(i);
                                if (0 === o) r = n;
                                else if (1 === o && "?" === n[0]) r = n.slice(1);
                                else {
                                    var a = n.substr(o).match(/([^\"]*)"?\)/);
                                    if (null !== a) r = a[1];
                                    else if (0 === n.indexOf("url(")) throw "Holder: não é possível analisar o URL de segundo plano: " + n
                                }
                                if (r) {
                                    var s = l(r, c);
                                    s && p({
                                        mode: "background",
                                        el: e,
                                        flags: s,
                                        engineSettings: d
                                    })
                                }
                            }
                        }), i.forEach(function(e) {
                            var t = {};
                            try {
                                t.data = e.getAttribute("data"), t.dataSrc = e.getAttribute(k.vars.dataAttr)
                            } catch (e) {}
                            var n = null != t.data && 0 === t.data.indexOf(c.domain),
                                r = null != t.dataSrc && 0 === t.dataSrc.indexOf(c.domain);
                            n ? f(c, d, t.data, e) : r && f(c, d, t.dataSrc, e)
                        }), t.forEach(function(e) {
                            var t = {};
                            try {
                                t.src = e.getAttribute("src"), t.dataSrc = e.getAttribute(k.vars.dataAttr), t.rendered = e.getAttribute("data-holder-rendered")
                            } catch (e) {}
                            var n, r, i, o, a, s = null != t.src,
                                l = null != t.dataSrc && 0 === t.dataSrc.indexOf(c.domain),
                                h = null != t.rendered && "true" == t.rendered;
                            s ? 0 === t.src.indexOf(c.domain) ? f(c, d, t.src, e) : l && (h ? f(c, d, t.dataSrc, e) : (n = t.src, r = c, i = d, o = t.dataSrc, a = e, m.imageExists(n, function(e) {
                                e || f(r, i, o, a)
                            }))) : l && f(c, d, t.dataSrc, e)
                        }), this
                    }
                },
                k = {
                    settings: {
                        domain: "holder.js",
                        images: "img",
                        objects: "object",
                        bgnodes: "body .holderjs",
                        stylenodes: "head link.holderjs",
                        themes: {
                            gray: {
                                bg: "#EEEEEE",
                                fg: "#AAAAAA"
                            },
                            social: {
                                bg: "#3a5a97",
                                fg: "#FFFFFF"
                            },
                            industrial: {
                                bg: "#434A52",
                                fg: "#C2F200"
                            },
                            sky: {
                                bg: "#0D8FDB",
                                fg: "#FFFFFF"
                            },
                            vine: {
                                bg: "#39DBAC",
                                fg: "#1E292C"
                            },
                            lava: {
                                bg: "#F8591A",
                                fg: "#1C2846"
                            }
                        }
                    },
                    defaults: {
                        size: 10,
                        units: "pt",
                        scale: 1 / 16
                    }
                };

            function f(e, t, n, r) {
                var i = l(n.substr(n.lastIndexOf(e.domain)), e);
                i && p({
                    mode: null,
                    el: r,
                    flags: i,
                    engineSettings: t
                })
            }

            function l(e, t) {
                var n = {
                        theme: g(k.settings.themes.gray, null),
                        stylesheets: t.stylesheets,
                        instanceOptions: t
                    },
                    r = e.indexOf("?"),
                    i = [e]; - 1 !== r && (i = [e.slice(0, r), e.slice(r + 1)]);
                var o = i[0].split("/");
                n.holderURL = e;
                var a = o[1],
                    s = a.match(/([\d]+p?)x([\d]+p?)/);
                if (!s) return !1;
                if (n.fluid = -1 !== a.indexOf("p"), n.dimensions = {
                        width: s[1].replace("p", "%"),
                        height: s[2].replace("p", "%")
                    }, 2 === i.length) {
                    var l = u.parse(i[1]);
                    if (m.truthy(l.ratio)) {
                        n.fluid = !0;
                        var h = parseFloat(n.dimensions.width.replace("%", "")),
                            d = parseFloat(n.dimensions.height.replace("%", ""));
                        d = Math.floor(d / h * 100), h = 100, n.dimensions.width = h + "%", n.dimensions.height = d + "%"
                    }
                    if (n.auto = m.truthy(l.auto), l.bg && (n.theme.bg = m.parseColor(l.bg)), l.fg && (n.theme.fg = m.parseColor(l.fg)), l.bg && !l.fg && (n.autoFg = !0), l.theme && n.instanceOptions.themes.hasOwnProperty(l.theme) && (n.theme = g(n.instanceOptions.themes[l.theme], null)), l.text && (n.text = l.text), l.textmode && (n.textmode = l.textmode), l.size && parseFloat(l.size) && (n.size = parseFloat(l.size)), l.font && (n.font = l.font), l.align && (n.align = l.align), l.lineWrap && (n.lineWrap = l.lineWrap), n.nowrap = m.truthy(l.nowrap), n.outline = m.truthy(l.outline), m.truthy(l.random)) {
                        k.vars.cache.themeKeys = k.vars.cache.themeKeys || Object.keys(n.instanceOptions.themes);
                        var c = k.vars.cache.themeKeys[0 | Math.random() * k.vars.cache.themeKeys.length];
                        n.theme = g(n.instanceOptions.themes[c], null)
                    }
                }
                return n
            }

            function p(e) {
                var t = e.mode,
                    n = e.el,
                    r = e.flags,
                    i = e.engineSettings,
                    o = r.dimensions,
                    a = r.theme,
                    s = o.width + "x" + o.height;
                t = null == t ? r.fluid ? "fluid" : "image" : t;
                if (null != r.text && (a.text = r.text, "object" === n.nodeName.toLowerCase())) {
                    for (var l = a.text.split("\\n"), h = 0; h < l.length; h++) l[h] = m.encodeHtmlEntity(l[h]);
                    a.text = l.join("\\n")
                }
                if (a.text) {
                    var d = a.text.match(/holder_([a-z]+)/g);
                    null !== d && d.forEach(function(e) {
                        "holder_dimensions" === e && (a.text = a.text.replace(e, s))
                    })
                }
                var c = r.holderURL,
                    u = g(i, null);
                if (r.font && (a.font = r.font, !u.noFontFallback && "img" === n.nodeName.toLowerCase() && k.setup.supportsCanvas && "svg" === u.renderer && (u = g(u, {
                        renderer: "canvas"
                    }))), r.font && "canvas" == u.renderer && (u.reRender = !0), "background" == t) null == n.getAttribute("data-background-src") && y.setAttr(n, {
                    "data-background-src": c
                });
                else {
                    var f = {};
                    f[k.vars.dataAttr] = c, y.setAttr(n, f)
                }
                r.theme = a, n.holderData = {
                    flags: r,
                    engineSettings: u
                }, "image" != t && "fluid" != t || y.setAttr(n, {
                    alt: a.text ? a.text + " [" + s + "]" : s
                });
                var p = {
                    mode: t,
                    el: n,
                    holderSettings: {
                        dimensions: o,
                        theme: a,
                        flags: r
                    },
                    engineSettings: u
                };
                "image" == t ? (r.auto || (n.style.width = o.width + "px", n.style.height = o.height + "px"), "html" == u.renderer ? n.style.backgroundColor = a.bg : (x(p), "exact" == r.textmode && (n.holderData.resizeUpdate = !0, k.vars.resizableImages.push(n), S(n)))) : "background" == t && "html" != u.renderer ? x(p) : "fluid" == t && (n.holderData.resizeUpdate = !0, "%" == o.height.slice(-1) ? n.style.height = o.height : null != r.auto && r.auto || (n.style.height = o.height + "px"), "%" == o.width.slice(-1) ? n.style.width = o.width : null != r.auto && r.auto || (n.style.width = o.width + "px"), "inline" != n.style.display && "" !== n.style.display && "none" != n.style.display || (n.style.display = "block"), function(e) {
                    if (e.holderData) {
                        var t = w(e);
                        if (t) {
                            var n = e.holderData.flags,
                                r = {
                                    fluidHeight: "%" == n.dimensions.height.slice(-1),
                                    fluidWidth: "%" == n.dimensions.width.slice(-1),
                                    mode: null,
                                    initialDimensions: t
                                };
                            r.fluidWidth && !r.fluidHeight ? (r.mode = "width", r.ratio = r.initialDimensions.width / parseFloat(n.dimensions.height)) : !r.fluidWidth && r.fluidHeight && (r.mode = "height", r.ratio = parseFloat(n.dimensions.width) / r.initialDimensions.height), e.holderData.fluidConfig = r
                        } else A(e)
                    }
                }(n), "html" == u.renderer ? n.style.backgroundColor = a.bg : (k.vars.resizableImages.push(n), S(n)))
            }

            function x(t) {
                var e, n = t.mode,
                    r = t.el,
                    i = t.holderSettings,
                    o = t.engineSettings;
                switch (o.renderer) {
                    case "svg":
                        if (!k.setup.supportsSVG) return;
                        break;
                    case "canvas":
                        if (!k.setup.supportsCanvas) return;
                        break;
                    default:
                        return
                }
                var a = {
                        width: i.dimensions.width,
                        height: i.dimensions.height,
                        theme: i.theme,
                        flags: i.flags
                    },
                    s = function(e) {
                        var t = k.defaults.size;
                        parseFloat(e.theme.size) ? t = e.theme.size : parseFloat(e.flags.size) && (t = e.flags.size);
                        switch (e.font = {
                            family: e.theme.font ? e.theme.font : "Arial, Helvetica, Open Sans, sans-serif",
                            size: function(e, t, n, r) {
                                var i = parseInt(e, 10),
                                    o = parseInt(t, 10),
                                    a = Math.max(i, o),
                                    s = Math.min(i, o),
                                    l = .8 * Math.min(s, a * r);
                                return Math.round(Math.max(n, l))
                            }(e.width, e.height, t, k.defaults.scale),
                            units: e.theme.units ? e.theme.units : k.defaults.units,
                            weight: e.theme.fontweight ? e.theme.fontweight : "bold"
                        }, e.text = e.theme.text || Math.floor(e.width) + "x" + Math.floor(e.height), e.noWrap = e.theme.nowrap || e.flags.nowrap, e.align = e.theme.align || e.flags.align || "center", e.flags.textmode) {
                            case "literal":
                                e.text = e.flags.dimensions.width + "x" + e.flags.dimensions.height;
                                break;
                            case "exact":
                                if (!e.flags.exactDimensions) break;
                                e.text = Math.floor(e.flags.exactDimensions.width) + "x" + Math.floor(e.flags.exactDimensions.height)
                        }
                        var n = e.flags.lineWrap || k.setup.lineWrapRatio,
                            r = e.width * n,
                            i = r,
                            o = new E({
                                width: e.width,
                                height: e.height
                            }),
                            a = o.Shape,
                            s = new a.Rect("holderBg", {
                                fill: e.theme.bg
                            });
                        if (s.resize(e.width, e.height), o.root.add(s), e.flags.outline) {
                            var l = new T(s.properties.fill);
                            l = l.lighten(l.lighterThan("7f7f7f") ? -.1 : .1), s.properties.outline = {
                                fill: l.toHex(!0),
                                width: 2
                            }
                        }
                        var h = e.theme.fg;
                        if (e.flags.autoFg) {
                            var d = new T(s.properties.fill),
                                c = new T("fff"),
                                u = new T("000", {
                                    alpha: .285714
                                });
                            h = d.blendAlpha(d.lighterThan("7f7f7f") ? u : c).toHex(!0)
                        }
                        var f = new a.Group("holderTextGroup", {
                            text: e.text,
                            align: e.align,
                            font: e.font,
                            fill: h
                        });
                        f.moveTo(null, null, 1), o.root.add(f);
                        var p = f.textPositionData = O(o);
                        if (!p) throw "Holder: reserva temporária ainda não suportada.";
                        f.properties.leading = p.boundingBox.height;
                        var g = null,
                            m = null;

                        function v(e, t, n, r) {
                            t.width = n, t.height = r, e.width = Math.max(e.width, t.width), e.height += t.height
                        }
                        if (1 < p.lineCount) {
                            var y, w = 0,
                                b = 0,
                                x = 0;
                            m = new a.Group("line" + x), "left" !== e.align && "right" !== e.align || (i = e.width * (1 - 2 * (1 - n)));
                            for (var S = 0; S < p.words.length; S++) {
                                var A = p.words[S];
                                g = new a.Text(A.text);
                                var C = "\\n" == A.text;
                                !e.noWrap && (w + A.width >= i || !0 == C) && (v(f, m, w, f.properties.leading), f.add(m), w = 0, b += f.properties.leading, x += 1, (m = new a.Group("line" + x)).y = b), !0 != C && (g.moveTo(w, 0), w += p.spaceWidth + A.width, m.add(g))
                            }
                            if (v(f, m, w, f.properties.leading), f.add(m), "left" === e.align) f.moveTo(e.width - r, null, null);
                            else if ("right" === e.align) {
                                for (y in f.children)(m = f.children[y]).moveTo(e.width - m.width, null, null);
                                f.moveTo(0 - (e.width - r), null, null)
                            } else {
                                for (y in f.children)(m = f.children[y]).moveTo((f.width - m.width) / 2, null, null);
                                f.moveTo((e.width - f.width) / 2, null, null)
                            }
                            f.moveTo(null, (e.height - f.height) / 2, null), (e.height - f.height) / 2 < 0 && f.moveTo(null, 0, null)
                        } else g = new a.Text(e.text), (m = new a.Group("line0")).add(g), f.add(m), "left" === e.align ? f.moveTo(e.width - r, null, null) : "right" === e.align ? f.moveTo(0 - (e.width - r), null, null) : f.moveTo((e.width - p.boundingBox.width) / 2, null, null), f.moveTo(null, (e.height - p.boundingBox.height) / 2, null);
                        return o
                    }(a);

                function l() {
                    var e = null;
                    switch (o.renderer) {
                        case "canvas":
                            e = c(s, t);
                            break;
                        case "svg":
                            e = d(s, t);
                            break;
                        default:
                            throw "Holder: representante inválido: " + o.renderer
                    }
                    return e
                }
                if (null == (e = l())) throw "Holder: não foi possível processar o espaço reservado";
                "background" == n ? (r.style.backgroundImage = "url(" + e + ")", o.noBackgroundSize || (r.style.backgroundSize = a.width + "px " + a.height + "px")) : ("img" === r.nodeName.toLowerCase() ? y.setAttr(r, {
                    src: e
                }) : "object" === r.nodeName.toLowerCase() && y.setAttr(r, {
                    data: e,
                    type: "image/svg+xml"
                }), o.reRender && h.setTimeout(function() {
                    var e = l();
                    if (null == e) throw "Holder: não foi possível processar o espaço reservado";
                    "img" === r.nodeName.toLowerCase() ? y.setAttr(r, {
                        src: e
                    }) : "object" === r.nodeName.toLowerCase() && y.setAttr(r, {
                        data: e,
                        type: "image/svg+xml"
                    })
                }, 150)), y.setAttr(r, {
                    "data-holder-rendered": !0
                })
            }

            function S(e) {
                for (var t, n = 0, r = (t = null == e || null == e.nodeType ? k.vars.resizableImages : [e]).length; n < r; n++) {
                    var i = t[n];
                    if (i.holderData) {
                        var o = i.holderData.flags,
                            a = w(i);
                        if (a) {
                            if (!i.holderData.resizeUpdate) continue;
                            if (o.fluid && o.auto) {
                                var s = i.holderData.fluidConfig;
                                switch (s.mode) {
                                    case "width":
                                        a.height = a.width / s.ratio;
                                        break;
                                    case "height":
                                        a.width = a.height * s.ratio
                                }
                            }
                            var l = {
                                mode: "image",
                                holderSettings: {
                                    dimensions: a,
                                    theme: o.theme,
                                    flags: o
                                },
                                el: i,
                                engineSettings: i.holderData.engineSettings
                            };
                            "exact" == o.textmode && (o.exactDimensions = a, l.holderSettings.dimensions = o.dimensions), x(l)
                        } else A(i)
                    }
                }
            }

            function i() {
                var t, n = [];
                Object.keys(k.vars.invisibleImages).forEach(function(e) {
                    t = k.vars.invisibleImages[e], w(t) && "img" == t.nodeName.toLowerCase() && (n.push(t), delete k.vars.invisibleImages[e])
                }), n.length && r.run({
                    images: n
                }), setTimeout(function() {
                    h.requestAnimationFrame(i)
                }, 10)
            }

            function A(e) {
                e.holderData.invisibleId || (k.vars.invisibleId += 1, (k.vars.invisibleImages["i" + k.vars.invisibleId] = e).holderData.invisibleId = k.vars.invisibleId)
            }
            var C, F, j, n, O = (j = F = C = null, function(e) {
                var t, n = e.root;
                if (k.setup.supportsSVG) {
                    var r = !1;
                    null != C && C.parentNode === document.body || (r = !0), (C = v.initSVG(C, n.properties.width, n.properties.height)).style.display = "block", r && (F = y.newEl("text", b), t = null, j = document.createTextNode(t), y.setAttr(F, {
                        x: 0
                    }), F.appendChild(j), C.appendChild(F), document.body.appendChild(C), C.style.visibility = "hidden", C.style.position = "absolute", C.style.top = "-100%", C.style.left = "-100%");
                    var i = n.children.holderTextGroup.properties;
                    y.setAttr(F, {
                        y: i.font.size,
                        style: m.cssProps({
                            "font-weight": i.font.weight,
                            "font-size": i.font.size + i.font.units,
                            "font-family": i.font.family
                        })
                    });
                    var o = y.newEl("textarea");
                    o.innerHTML = i.text, j.nodeValue = o.value;
                    var a = F.getBBox(),
                        s = Math.ceil(a.width / n.properties.width),
                        l = i.text.split(" "),
                        h = i.text.match(/\\n/g);
                    s += null == h ? 0 : h.length, j.nodeValue = i.text.replace(/[ ]+/g, "");
                    var d = F.getComputedTextLength(),
                        c = a.width - d,
                        u = Math.round(c / Math.max(1, l.length - 1)),
                        f = [];
                    if (1 < s) {
                        j.nodeValue = "";
                        for (var p = 0; p < l.length; p++)
                            if (0 !== l[p].length) {
                                j.nodeValue = m.decodeHtmlEntity(l[p]);
                                var g = F.getBBox();
                                f.push({
                                    text: l[p],
                                    width: g.width
                                })
                            }
                    }
                    return C.style.display = "none", {
                        spaceWidth: u,
                        lineCount: s,
                        boundingBox: a,
                        words: f
                    }
                }
                return !1
            });

            function o() {
                ! function(e) {
                    k.vars.debounceTimer || e.call(this), k.vars.debounceTimer && h.clearTimeout(k.vars.debounceTimer), k.vars.debounceTimer = h.setTimeout(function() {
                        k.vars.debounceTimer = null, e.call(this)
                    }, k.setup.debounce)
                }(function() {
                    S(null)
                })
            }
            for (var a in k.flags) k.flags.hasOwnProperty(a) && (k.flags[a].match = function(e) {
                return e.match(this.regex)
            });
            k.setup = {
                renderer: "html",
                debounce: 100,
                ratio: 1,
                supportsCanvas: !1,
                supportsSVG: !1,
                lineWrapRatio: .9,
                dataAttr: "data-src",
                renderers: ["html", "canvas", "svg"]
            }, k.vars = {
                preempted: !1,
                resizableImages: [],
                invisibleImages: {},
                invisibleId: 0,
                visibilityCheckStarted: !1,
                debounceTimer: null,
                cache: {}
            }, (n = y.newEl("canvas")).getContext && -1 != n.toDataURL("image/png").indexOf("data:image/png") && (k.setup.renderer = "canvas", k.setup.supportsCanvas = !0), document.createElementNS && document.createElementNS(b, "svg").createSVGRect && (k.setup.renderer = "svg", k.setup.supportsSVG = !0), k.vars.visibilityCheckStarted || (h.requestAnimationFrame(i), k.vars.visibilityCheckStarted = !0), e && e(function() {
                k.vars.preempted || r.run(), h.addEventListener ? (h.addEventListener("resize", o, !1), h.addEventListener("orientationchange", o, !1)) : h.attachEvent("onresize", o), "object" == typeof h.Turbolinks && h.document.addEventListener("page:change", function() {
                    r.run()
                })
            }), s.exports = r
        }).call(e, function() {
            return this
        }())
    }, function(e, t) {
        e.exports = "undefined" != typeof window && function(e) {
            null == document.readyState && document.addEventListener && (document.addEventListener("DOMContentLoaded", function e() {
                document.removeEventListener("DOMContentLoaded", e, !1), document.readyState = "complete"
            }, !1), document.readyState = "loading");
            var t = e.document,
                n = t.documentElement,
                r = "load",
                i = !1,
                o = "on" + r,
                a = "complete",
                s = "readyState",
                l = "attachEvent",
                h = "detachEvent",
                d = "addEventListener",
                c = "DOMContentLoaded",
                u = "onreadystatechange",
                f = "removeEventListener",
                p = d in t,
                g = i,
                m = i,
                v = [];

            function y(e) {
                if (!m) {
                    if (!t.body) return x(y);
                    for (m = !0; e = v.shift();) x(e)
                }
            }

            function w(e) {
                !p && e.type !== r && t[s] !== a || (b(), y())
            }

            function b() {
                p ? (t[f](c, w, i), e[f](r, w, i)) : (t[h](u, w), e[h](o, w))
            }

            function x(e, t) {
                setTimeout(e, 0 <= +t ? t : 1)
            }
            if (t[s] === a) x(y);
            else if (p) t[d](c, w, i), e[d](r, w, i);
            else {
                t[l](u, w), e[l](o, w);
                try {
                    g = null == e.frameElement && n
                } catch (e) {}
                g && g.doScroll && ! function t() {
                    if (!m) {
                        try {
                            g.doScroll("left")
                        } catch (e) {
                            return x(t, 50)
                        }
                        b(), y()
                    }
                }()
            }

            function S(e) {
                m ? x(e) : v.push(e)
            }
            return S.version = "1.4.0", S.isReady = function() {
                return m
            }, S
        }(window)
    }, function(e, t, n) {
        var o = encodeURIComponent,
            h = decodeURIComponent,
            d = n(4),
            a = n(5),
            c = /(\w+)\[(\d+)\]/,
            u = /\w+\.\w+/;
        t.parse = function(e) {
            if ("string" != typeof e) return {};
            if ("" === (e = d(e))) return {};
            "?" === e.charAt(0) && (e = e.slice(1));
            for (var t = {}, n = e.split("&"), r = 0; r < n.length; r++) {
                var i, o, a, s = n[r].split("="),
                    l = h(s[0]);
                if (i = c.exec(l)) t[i[1]] = t[i[1]] || [], t[i[1]][i[2]] = h(s[1]);
                else if (i = u.test(l)) {
                    for (i = l.split("."), o = t; i.length;)
                        if ((a = i.shift()).length) {
                            if (o[a]) {
                                if (o[a] && "object" != typeof o[a]) break
                            } else o[a] = {};
                            i.length || (o[a] = h(s[1])), o = o[a]
                        }
                } else t[s[0]] = null == s[1] ? "" : h(s[1])
            }
            return t
        }, t.stringify = function(e) {
            if (!e) return "";
            var t = [];
            for (var n in e) {
                var r = e[n];
                if ("array" != a(r)) t.push(o(n) + "=" + o(e[n]));
                else
                    for (var i = 0; i < r.length; ++i) t.push(o(n + "[" + i + "]") + "=" + o(r[i]))
            }
            return t.join("&")
        }
    }, function(e, t) {
        (t = e.exports = function(e) {
            return e.replace(/^\s*|\s*$/g, "")
        }).left = function(e) {
            return e.replace(/^\s*/, "")
        }, t.right = function(e) {
            return e.replace(/\s*$/, "")
        }
    }, function(e, t) {
        var n = Object.prototype.toString;
        e.exports = function(e) {
            switch (n.call(e)) {
                case "[object Date]":
                    return "date";
                case "[object RegExp]":
                    return "regexp";
                case "[object Arguments]":
                    return "arguments";
                case "[object Array]":
                    return "array";
                case "[object Error]":
                    return "error"
            }
            return null === e ? "null" : void 0 === e ? "undefined" : e != e ? "nan" : e && 1 === e.nodeType ? "element" : function(e) {
                return !(null == e || !(e._isBuffer || e.constructor && "function" == typeof e.constructor.isBuffer && e.constructor.isBuffer(e)))
            }(e) ? "buffer" : typeof(e = e.valueOf ? e.valueOf() : Object.prototype.valueOf.apply(e))
        }
    }, function(e, t) {
        e.exports = function(e) {
            var t = 1;

            function n(e) {
                t++, this.parent = null, this.children = {}, this.id = t, this.name = "n" + t, void 0 !== e && (this.name = e), this.x = this.y = this.z = 0, this.width = this.height = 0
            }
            n.prototype.resize = function(e, t) {
                null != e && (this.width = e), null != t && (this.height = t)
            }, n.prototype.moveTo = function(e, t, n) {
                this.x = null != e ? e : this.x, this.y = null != t ? t : this.y, this.z = null != n ? n : this.z
            }, n.prototype.add = function(e) {
                var t = e.name;
                if (void 0 !== this.children[t]) throw "SceneGraph: child already exists: " + t;
                (this.children[t] = e).parent = this
            };

            function r() {
                n.call(this, "root"), this.properties = e
            }
            r.prototype = new n;

            function i(e, t) {
                if (n.call(this, e), this.properties = {
                        fill: "#000000"
                    }, void 0 !== t) ! function(e, t) {
                    for (var n in t) e[n] = t[n]
                }(this.properties, t);
                else if (void 0 !== e && "string" != typeof e) throw "SceneGraph: invalid node name"
            }
            i.prototype = new n;

            function o() {
                i.apply(this, arguments), this.type = "group"
            }
            o.prototype = new i;

            function a() {
                i.apply(this, arguments), this.type = "rect"
            }
            a.prototype = new i;

            function s(e) {
                i.call(this), this.type = "text", this.properties.text = e
            }
            s.prototype = new i;
            var l = new r;
            return this.Shape = {
                Rect: a,
                Text: s,
                Group: o
            }, this.root = l, this
        }
    }, function(e, t) {
        (function(i) {
            t.extend = function(e, t) {
                var n = {};
                for (var r in e) e.hasOwnProperty(r) && (n[r] = e[r]);
                if (null != t)
                    for (var i in t) t.hasOwnProperty(i) && (n[i] = t[i]);
                return n
            }, t.cssProps = function(e) {
                var t = [];
                for (var n in e) e.hasOwnProperty(n) && t.push(n + ":" + e[n]);
                return t.join(";")
            }, t.encodeHtmlEntity = function(e) {
                for (var t = [], n = 0, r = e.length - 1; 0 <= r; r--) 128 < (n = e.charCodeAt(r)) ? t.unshift(["&#", n, ";"].join("")) : t.unshift(e[r]);
                return t.join("")
            }, t.imageExists = function(e, t) {
                var n = new Image;
                n.onerror = function() {
                    t.call(this, !1)
                }, n.onload = function() {
                    t.call(this, !0)
                }, n.src = e
            }, t.decodeHtmlEntity = function(e) {
                return e.replace(/&#(\d+);/g, function(e, t) {
                    return String.fromCharCode(t)
                })
            }, t.dimensionCheck = function(e) {
                var t = {
                    height: e.clientHeight,
                    width: e.clientWidth
                };
                return !(!t.height || !t.width) && t
            }, t.truthy = function(e) {
                return "string" == typeof e ? "true" === e || "yes" === e || "1" === e || "on" === e || "✓" === e : !!e
            }, t.parseColor = function(e) {
                var t, n = e.match(/(^(?:#?)[0-9a-f]{6}$)|(^(?:#?)[0-9a-f]{3}$)/i);
                return null !== n ? "#" !== (t = n[1] || n[2])[0] ? "#" + t : t : null !== (n = e.match(/^rgb\((\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/)) ? t = "rgb(" + n.slice(1).join(",") + ")" : null !== (n = e.match(/^rgba\((\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(0\.\d{1,}|1)\)$/)) ? t = "rgba(" + n.slice(1).join(",") + ")" : null
            }, t.canvasRatio = function() {
                var e = 1,
                    t = 1;
                if (i.document) {
                    var n = i.document.createElement("canvas");
                    if (n.getContext) {
                        var r = n.getContext("2d");
                        e = i.devicePixelRatio || 1, t = r.webkitBackingStorePixelRatio || r.mozBackingStorePixelRatio || r.msBackingStorePixelRatio || r.oBackingStorePixelRatio || r.backingStorePixelRatio || 1
                    }
                }
                return e / t
            }
        }).call(t, function() {
            return this
        }())
    }, function(e, t, n) {
        (function(h) {
            var d = n(9),
                s = "http://www.w3.org/2000/svg";
            t.initSVG = function(e, t, n) {
                var r, i, o = !1;
                e && e.querySelector ? null === (i = e.querySelector("style")) && (o = !0) : (e = d.newEl("svg", s), o = !0), o && (r = d.newEl("defs", s), i = d.newEl("style", s), d.setAttr(i, {
                    type: "text/css"
                }), r.appendChild(i), e.appendChild(r)), e.webkitMatchesSelector && e.setAttribute("xmlns", s);
                for (var a = 0; a < e.childNodes.length; a++) 8 === e.childNodes[a].nodeType && e.removeChild(e.childNodes[a]);
                for (; i.childNodes.length;) i.removeChild(i.childNodes[0]);
                return d.setAttr(e, {
                    width: t,
                    height: n,
                    viewBox: "0 0 " + t + " " + n,
                    preserveAspectRatio: "none"
                }), e
            }, t.svgStringToDataURI = function(e, t) {
                return t ? "data:image/svg+xml;charset=UTF-8;base64," + btoa(h.unescape(encodeURIComponent(e))) : "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(e)
            }, t.serializeSVG = function(e, t) {
                if (h.XMLSerializer) {
                    var n = new XMLSerializer,
                        r = "",
                        i = t.stylesheets;
                    if (t.svgXMLStylesheet) {
                        for (var o = d.createXML(), a = i.length - 1; 0 <= a; a--) {
                            var s = o.createProcessingInstruction("xml-stylesheet", 'href="' + i[a] + '" rel="stylesheet"');
                            o.insertBefore(s, o.firstChild)
                        }
                        o.removeChild(o.documentElement), r = n.serializeToString(o)
                    }
                    var l = n.serializeToString(e);
                    return r + (l = l.replace(/\&amp;(\#[0-9]{2,}\;)/g, "&$1"))
                }
            }
        }).call(t, function() {
            return this
        }())
    }, function(e, t) {
        (function(n) {
            t.newEl = function(e, t) {
                if (n.document) return null == t ? n.document.createElement(e) : n.document.createElementNS(t, e)
            }, t.setAttr = function(e, t) {
                for (var n in t) e.setAttribute(n, t[n])
            }, t.createXML = function() {
                if (n.DOMParser) return (new DOMParser).parseFromString("<xml />", "application/xml")
            }, t.getNodeArray = function(e) {
                var t = null;
                return "string" == typeof e ? t = document.querySelectorAll(e) : n.NodeList && e instanceof n.NodeList ? t = e : n.Node && e instanceof n.Node ? t = [e] : n.HTMLCollection && e instanceof n.HTMLCollection ? t = e : e instanceof Array ? t = e : null === e && (t = []), t = Array.prototype.slice.call(t)
            }
        }).call(t, function() {
            return this
        }())
    }, function(e, t) {
        function a(e, t) {
            "string" == typeof e && ("#" === (this.original = e).charAt(0) && (e = e.slice(1)), /[^a-f0-9]+/i.test(e) || (3 === e.length && (e = e.replace(/./g, "$&$&")), 6 === e.length && (this.alpha = 1, t && t.alpha && (this.alpha = t.alpha), this.set(parseInt(e, 16)))))
        }
        a.rgb2hex = function(e, t, n) {
            return [e, t, n].map(function(e) {
                var t = (0 | e).toString(16);
                return e < 16 && (t = "0" + t), t
            }).join("")
        }, a.hsl2rgb = function(e, t, n) {
            var r = e / 60,
                i = (1 - Math.abs(2 * n - 1)) * t,
                o = i * (1 - Math.abs(parseInt(r) % 2 - 1)),
                a = n - i / 2,
                s = 0,
                l = 0,
                h = 0;
            return 0 <= r && r < 1 ? (s = i, l = o) : 1 <= r && r < 2 ? (s = o, l = i) : 2 <= r && r < 3 ? (l = i, h = o) : 3 <= r && r < 4 ? (l = o, h = i) : 4 <= r && r < 5 ? (s = o, h = i) : 5 <= r && r < 6 && (s = i, h = o), s += a, l += a, h += a, [s = parseInt(255 * s), l = parseInt(255 * l), h = parseInt(255 * h)]
        }, a.prototype.set = function(e) {
            this.raw = e;
            var t = (16711680 & this.raw) >> 16,
                n = (65280 & this.raw) >> 8,
                r = 255 & this.raw,
                i = .2126 * t + .7152 * n + .0722 * r,
                o = -.09991 * t - .33609 * n + .436 * r,
                a = .615 * t - .55861 * n - .05639 * r;
            return this.rgb = {
                r: t,
                g: n,
                b: r
            }, this.yuv = {
                y: i,
                u: o,
                v: a
            }, this
        }, a.prototype.lighten = function(e) {
            var t = 255 * (Math.min(1, Math.max(0, Math.abs(e))) * (e < 0 ? -1 : 1)) | 0,
                n = Math.min(255, Math.max(0, this.rgb.r + t)),
                r = Math.min(255, Math.max(0, this.rgb.g + t)),
                i = Math.min(255, Math.max(0, this.rgb.b + t)),
                o = a.rgb2hex(n, r, i);
            return new a(o)
        }, a.prototype.toHex = function(e) {
            return (e ? "#" : "") + this.raw.toString(16)
        }, a.prototype.lighterThan = function(e) {
            return e instanceof a || (e = new a(e)), this.yuv.y > e.yuv.y
        }, a.prototype.blendAlpha = function(e) {
            e instanceof a || (e = new a(e));
            var t = e,
                n = t.alpha * t.rgb.r + (1 - t.alpha) * this.rgb.r,
                r = t.alpha * t.rgb.g + (1 - t.alpha) * this.rgb.g,
                i = t.alpha * t.rgb.b + (1 - t.alpha) * this.rgb.b;
            return new a(a.rgb2hex(n, r, i))
        }, e.exports = a
    }, function(e, t) {
        e.exports = {
            version: "2.9.6",
            svg_ns: "http://www.w3.org/2000/svg"
        }
    }, function(e, t, n) {
        var y = n(13),
            w = n(8),
            r = n(11),
            b = n(7),
            x = r.svg_ns,
            S = function(e) {
                var t = e.tag,
                    n = e.content || "";
                return delete e.tag, delete e.content, [t, n, e]
            };
        e.exports = function(e, t) {
            var n = t.engineSettings.stylesheets.map(function(e) {
                    return '<?xml-stylesheet rel="stylesheet" href="' + e + '"?>'
                }).join("\n"),
                r = "holder_" + Number(new Date).toString(16),
                i = e.root,
                a = i.children.holderTextGroup,
                o = "#" + r + " text { " + function(e) {
                    return b.cssProps({
                        fill: e.fill,
                        "font-weight": e.font.weight,
                        "font-family": e.font.family + ", monospace",
                        "font-size": e.font.size + e.font.units
                    })
                }(a.properties) + " } ";
            a.y += .8 * a.textPositionData.boundingBox.height;
            var s = [];
            Object.keys(a.children).forEach(function(e) {
                var o = a.children[e];
                Object.keys(o.children).forEach(function(e) {
                    var t = o.children[e],
                        n = a.x + o.x + t.x,
                        r = a.y + o.y + t.y,
                        i = S({
                            tag: "text",
                            content: t.properties.text,
                            x: n,
                            y: r
                        });
                    s.push(i)
                })
            });
            var l = S({
                    tag: "g",
                    content: s
                }),
                h = null;
            if (i.children.holderBg.properties.outline) {
                var d = i.children.holderBg.properties.outline;
                h = S({
                    tag: "path",
                    d: function(e, t, n) {
                        var r = n / 2;
                        return ["M", r, r, "H", e - r, "V", t - r, "H", r, "V", 0, "M", 0, r, "L", e, t - r, "M", 0, t - r, "L", e, r].join(" ")
                    }(i.children.holderBg.width, i.children.holderBg.height, d.width),
                    "stroke-width": d.width,
                    stroke: d.fill,
                    fill: "none"
                })
            }
            var c = function(e, t) {
                    return S({
                        tag: t,
                        width: e.width,
                        height: e.height,
                        fill: e.properties.fill
                    })
                }(i.children.holderBg, "rect"),
                u = [];
            u.push(c), d && u.push(h), u.push(l);
            var f = S({
                    tag: "g",
                    id: r,
                    content: u
                }),
                p = S({
                    tag: "style",
                    content: o,
                    type: "text/css"
                }),
                g = S({
                    tag: "defs",
                    content: p
                }),
                m = S({
                    tag: "svg",
                    content: [g, f],
                    width: i.properties.width,
                    height: i.properties.height,
                    xmlns: x,
                    viewBox: [0, 0, i.properties.width, i.properties.height].join(" "),
                    preserveAspectRatio: "none"
                }),
                v = y(m);
            return /\&amp;(x)?#[0-9A-Fa-f]/.test(v[0]) && (v[0] = v[0].replace(/&amp;#/gm, "&#")), v = n + v[0], w.svgStringToDataURI(v, "background" === t.mode)
        }
    }, function(e, t, n) {
        n(14);
        e.exports = function e(t, n, r) {
            "use strict";
            var i, o, a, s, l, h, d, c, u, f, p, g, m = 1,
                v = !0;

            function y(e, t) {
                if (null !== t && !1 !== t && void 0 !== t) return "string" != typeof t && "object" != typeof t ? String(t) : t
            }
            if (r = r || {}, "string" == typeof t[0]) t[0] = (l = t[0], h = l.match(/^[\w-]+/), d = {
                tag: h ? h[0] : "div",
                attr: {},
                children: []
            }, c = l.match(/#([\w-]+)/), u = l.match(/\$([\w-]+)/), f = l.match(/\.[\w-]+/g), c && (d.attr.id = c[1], r[c[1]] = d), u && (r[u[1]] = d), f && (d.attr.class = f.join(" ").replace(/\./g, "")), l.match(/&$/g) && (v = !1), d);
            else {
                if (!Array.isArray(t[0])) throw new Error("First element of array must be a string, or an array and not " + JSON.stringify(t[0]));
                m = 0
            }
            for (; m < t.length; m++) {
                if (!1 === t[m] || null === t[m]) {
                    t[0] = !1;
                    break
                }
                if (void 0 !== t[m] && !0 !== t[m])
                    if ("string" == typeof t[m]) v && (t[m] = (p = t[m], String(p).replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/'/g, "&apos;").replace(/</g, "&lt;").replace(/>/g, "&gt;"))), t[0].children.push(t[m]);
                    else if ("number" == typeof t[m]) t[0].children.push(t[m]);
                else if (Array.isArray(t[m])) {
                    if (Array.isArray(t[m][0])) {
                        if (t[m].reverse().forEach(function(e) {
                                t.splice(m + 1, 0, e)
                            }), 0 !== m) continue;
                        m++
                    }
                    e(t[m], n, r), t[m][0] && t[0].children.push(t[m][0])
                } else if ("function" == typeof t[m]) a = t[m];
                else {
                    if ("object" != typeof t[m]) throw new TypeError('"' + t[m] + '" is not allowed as a value.');
                    for (o in t[m]) t[m].hasOwnProperty(o) && null !== t[m][o] && !1 !== t[m][o] && ("style" === o && "object" == typeof t[m][o] ? t[0].attr[o] = JSON.stringify(t[m][o], y).slice(2, -2).replace(/","/g, ";").replace(/":"/g, ":").replace(/\\"/g, "'") : t[0].attr[o] = t[m][o])
                }
            }
            if (!1 !== t[0]) {
                for (s in i = "<" + t[0].tag, t[0].attr) t[0].attr.hasOwnProperty(s) && (i += " " + s + '="' + ((g = t[0].attr[s]) || 0 === g ? String(g).replace(/&/g, "&amp;").replace(/"/g, "&quot;") : "") + '"');
                i += ">", t[0].children.forEach(function(e) {
                    i += e
                }), i += "</" + t[0].tag + ">", t[0] = i
            }
            return r[0] = t[0], a && a(t[0]), r
        }
    }, function(e, t) {
        "use strict";
        var s = /["'&<>]/;
        e.exports = function(e) {
            var t, n = "" + e,
                r = s.exec(n);
            if (!r) return n;
            var i = "",
                o = 0,
                a = 0;
            for (o = r.index; o < n.length; o++) {
                switch (n.charCodeAt(o)) {
                    case 34:
                        t = "&quot;";
                        break;
                    case 38:
                        t = "&amp;";
                        break;
                    case 39:
                        t = "&#39;";
                        break;
                    case 60:
                        t = "&lt;";
                        break;
                    case 62:
                        t = "&gt;";
                        break;
                    default:
                        continue
                }
                a !== o && (i += n.substring(a, o)), a = o + 1, i += t
            }
            return a !== o ? i + n.substring(a, o) : i
        }
    }, function(e, t, n) {
        var f, p, r = n(9),
            g = n(7);
        e.exports = (f = r.newEl("canvas"), p = null, function(e) {
            null == p && (p = f.getContext("2d"));
            var t = g.canvasRatio(),
                n = e.root;
            f.width = t * n.properties.width, f.height = t * n.properties.height, p.textBaseline = "middle";
            var r = n.children.holderBg,
                i = t * r.width,
                o = t * r.height;
            p.fillStyle = r.properties.fill, p.fillRect(0, 0, i, o), r.properties.outline && (p.strokeStyle = r.properties.outline.fill, p.lineWidth = r.properties.outline.width, p.moveTo(1, 1), p.lineTo(i - 1, 1), p.lineTo(i - 1, o - 1), p.lineTo(1, o - 1), p.lineTo(1, 1), p.moveTo(0, 1), p.lineTo(i, o - 1), p.moveTo(0, o - 1), p.lineTo(i, 1), p.stroke());
            var a = n.children.holderTextGroup;
            for (var s in p.font = a.properties.font.weight + " " + t * a.properties.font.size + a.properties.font.units + " " + a.properties.font.family + ", monospace", p.fillStyle = a.properties.fill, a.children) {
                var l = a.children[s];
                for (var h in l.children) {
                    var d = l.children[h],
                        c = t * (a.x + l.x + d.x),
                        u = t * (a.y + l.y + d.y + a.properties.leading / 2);
                    p.fillText(d.properties.text, c, u)
                }
            }
            return f.toDataURL("image/png")
        })
    }], n.c = i, n.p = "", n(0);

    function n(e) {
        if (i[e]) return i[e].exports;
        var t = i[e] = {
            exports: {},
            id: e,
            loaded: !1
        };
        return r[e].call(t.exports, t, t.exports, n), t.loaded = !0, t.exports
    }
    var r, i
}),
function(e, t) {
    typeof Meteor !== "undefined" && typeof Package !== "undefined" && (Holder = e.Holder)
}(this);
