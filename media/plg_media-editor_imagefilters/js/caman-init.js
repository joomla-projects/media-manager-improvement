//
// (function() {
//   var m = document.createElement("input");
//   try {
//     m.type = "range";
//     if (m.type == "range") {
//       return
//     }
//   } catch (k) {
//     return
//   }
//   if (!document.mozSetImageElement ||!("MozAppearance" in m.style)) {
//     return
//   }
//   var g;
//   var j = navigator.platform == "MacIntel";
//   var a = {
//     radius: j ? 9: 6,
//     width: j ? 22: 12,
//     height: j ? 16: 20
//   };
//   var d = "-moz-linear-gradient(top, transparent " + (j ? "6px, #999 6px, #999 7px, #ccc 9px, #bbb 11px, #bbb 12px, transparent 12px" : "9px, #999 9px, #bbb 10px, #fff 11px, transparent 11px") + ", transparent)";
//   var n = {
//     "min-width": a.width + "px",
//     "min-height": a.height + "px",
//     "max-height": a.height + "px",
//     padding: 0,
//     border: 0,
//     "border-radius": 0,
//     cursor: "default",
//     "text-indent": "-999999px"
//   };
//   var l = document.createEvent("HTMLEvents");
//   l.initEvent("change", true, false);
//   if (document.readyState == "loading") {
//     document.addEventListener("DOMContentLoaded", h, true)
//   } else {
//     h()
//   }
//   function h() {
//     Array.forEach(document.querySelectorAll("input[type=range]"), f);
//     document.addEventListener("DOMNodeInserted", i, true)
//   }
//   function i(o) {
//     c(o.target);
//     if (o.target.querySelectorAll) {
//       Array.forEach(o.target.querySelectorAll("input"), c)
//     }
//   }
//   function c(e, o) {
//     if (e.localName != "input" || e.type == "range") {} else {
//       if (e.getAttribute("type") == "range") {
//         f(e)
//       } else {
//         if (!o) {
//           setTimeout(c, 0, e, true)
//         }
//       }
//     }
//   }
//   function f(F) {
//     var w, L, v, K, H, I, u;
//     var G, J, y, D, E = F.value;
//     if (!g) {
//       g = document.body.appendChild(document.createElement("hr"));
//       b(g, {
//         "-moz-appearance": j ? "scale-horizontal": "scalethumb-horizontal",
//         display: "block",
//         visibility: "visible",
//         opacity: 1,
//         position: "fixed",
//         top: "-999999px"
//       });
//       document.mozSetImageElement("__sliderthumb__", g)
//     }
//     var q = function() {
//       return "" + E
//     };
//     var p = function p(M) {
//       E = "" + M;
//       w = true;
//       A();
//       delete F.value;
//       F.value = E;
//       F.__defineGetter__("value", q);
//       F.__defineSetter__("value", p)
//     };
//     F.__defineGetter__("value", q);
//     F.__defineSetter__("value", p);
//     F.__defineGetter__("type", function() {
//       return "range"
//     });
//     ["min", "max", "step"].forEach(function(M) {
//       if (F.hasAttribute(M)) {
//         L = true
//       }
//       F.__defineGetter__(M, function() {
//         return this.hasAttribute(M) ? this.getAttribute(M) : ""
//       });
//       F.__defineSetter__(M, function(N) {
//         N === null ? this.removeAttribute(M) : this.setAttribute(M, N)
//       })
//     });
//     F.readOnly = true;
//     b(F, n);
//     z();
//     F.addEventListener("DOMAttrModified", function(M) {
//       if (M.attrName == "value"&&!w) {
//         E = M.newValue;
//         A()
//       } else {
//         if (~["min", "max", "step"].indexOf(M.attrName)) {
//           z();
//           L = true
//         }
//       }
//     }, true);
//     F.addEventListener("mousedown", x, true);
//     F.addEventListener("keydown", r, true);
//     F.addEventListener("focus", t, true);
//     F.addEventListener("blur", B, true);
//     function x(O) {
//       K = true;
//       setTimeout(function() {
//         K = false
//       }, 0);
//       if (O.button ||!D) {
//         return
//       }
//       var N = parseFloat(getComputedStyle(this, 0).width);
//       var P = (N - a.width) / D;
//       if (!P) {
//         return
//       }
//       var M = O.clientX - this.getBoundingClientRect().left - a.width / 2 - (E - G) * P;
//       if (Math.abs(M) > a.radius) {
//         v = true;
//         this.value-=-M / P
//       }
//       I = E;
//       u = O.clientX;
//       this.addEventListener("mousemove", C, true);
//       this.addEventListener("mouseup", o, true)
//     }
//     function C(N) {
//       var M = parseFloat(getComputedStyle(this, 0).width);
//       var O = (M - a.width) / D;
//       if (!O) {
//         return
//       }
//       I += (N.clientX - u) / O;
//       u = N.clientX;
//       v = true;
//       this.value = I
//     }
//     function o() {
//       this.removeEventListener("mousemove", C, true);
//       this.removeEventListener("mouseup", o, true)
//     }
//     function r(M) {
//       if (M.keyCode > 36 && M.keyCode < 41) {
//         t.call(this);
//         v = true;
//         this.value = E + (M.keyCode == 38 || M.keyCode == 39 ? y : - y)
//       }
//     }
//     function t() {
//       if (!K) {
//         this.style.boxShadow=!j ? "0 0 0 2px #fb0" : "0 0 2px 1px -moz-mac-focusring, inset 0 0 1px -moz-mac-focusring"
//       }
//     }
//     function B() {
//       this.style.boxShadow = ""
//     }
//     function s(M) {
//       return !isNaN(M)&&+M == parseFloat(M)
//     }
//     function z() {
//       G = s(F.min)?+F.min : 0;
//       J = s(F.max)?+F.max : 100;
//       if (J < G) {
//         J = G > 100 ? G : 100
//       }
//       y = s(F.step) && F.step > 0?+F.step : 1;
//       D = J - G;
//       A(true)
//     }
//     function e() {
//       if (!w&&!L) {
//         E = F.getAttribute("value")
//       }
//       if (!s(E)) {
//         E = (G + J) / 2
//       }
//       E = Math.round((E - G) / y) * y + G;
//       if (E < G) {
//         E = G
//       } else {
//         if (E > J) {
//           E = G+~~(D / y) * y
//         }
//       }
//     }
//     function A(O) {
//       e();
//       if (v && E != H) {
//         F.dispatchEvent(l)
//       }
//       v = false;
//       if (!O && E == H) {
//         return
//       }
//       H = E;
//       var M = D ? (E - G) / D * 100: 0;
//       var N = "-moz-element(#__sliderthumb__) " + M + "% no-repeat, ";
//       b(F, {
//         background: N + d
//       })
//     }
//   }
//   function b(e, o) {
//     for (var p in o) {
//       e.style.setProperty(p, o[p], "important")
//     }
//   }
// })();
//
//
// (function() {
//   var f, c, e, b, d, i, a, h, g = {}.hasOwnProperty;
//   c = null;
//   i = null;
//   b = {};
//   f = false;
//   e = false;
//   a = _.throttle(function() {
//     var j, k;
//     if (f) {
//       e = true;
//       return
//     } else {
//       e = false
//     }
//     f = true;
//     c.revert(false);
//     for (j in b) {
//       if (!g.call(b, j)) {
//         continue
//       }
//       k = b[j];
//       k = parseFloat(k, 10);
//       if (k === 0) {
//         continue
//       }
//       c[j](k)
//     }
//     return c.render(function() {
//       f = false;
//       if (e) {
//         return a()
//       }
//     })
//   }, 300);
//   d = false;
//   h = function(k) {
//     var l, j;
//     if (d) {
//       return
//     }
//     $("#PresetFilters a").removeClass("Active");
//     l = $("#PresetFilters a[data-preset='" + k + "']");
//     j = l.html();
//     l.addClass("Active").html("Rendering...");
//     d = true;
//     c.revert(false);
//     c[k]();
//     return c.render(function() {
//       l.html(j);
//       return d = false
//     })
//   };
//   $(document).ready(function() {
//     if (!($("#example").length > 0)) {
//       return
//     }
//     c = Caman("#example");
//     $(".FilterSetting input").each(function() {
//       var j;
//       j = $(this).data("filter");
//       return b[j] = $(this).val()
//     });
//     $("#Filters").on("change", ".FilterSetting input", function() {
//       var j, k;
//       j = $(this).data("filter");
//       k = $(this).val();
//       b[j] = k;
//       $(this).find("~ .FilterValue").html(k);
//       return a()
//     });
//     return $("#PresetFilters").on("click", "a", function() {
//       return h($(this).data("preset"))
//     })
//   })
// }).call(this);
// (function() {
//   var a;
//   a = function(d) {
//     var b, c, e;
//     e = d.attr("id");
//     d.attr("id", "");
//     c = $("<div>").css({
//       position: "absolute",
//       visibility: "hidden",
//       top: $(document).scrollTop() + "px"
//     }).attr("id", e).appendTo(document.body);
//     document.location.hash = "#" + e;
//     c.remove();
//     d.attr("id", e);
//     b = $("#GuideSections li > a").filter("[href=#" + (d.attr("id")) + "]");
//     b.parents("ul").find(".Active").removeClass("Active");
//     return b.parents("li").addClass("Active")
//   };
//   $(document).ready(function() {
//     var b;
//     $("#GuideSections").on("click", "a", function() {
//       var c, d;
//       c = $($(this).attr("href"));
//       d = Math.max(0, c.position().top - 129);
//       document.location.hash = $(this).attr("href");
//       setTimeout(function() {
//         return $("body").scrollTop(d)
//       }, 50);
//       return false
//     });
//     b = _.map($("#GuideSections li > a"), function(c) {
//       return $($(c).attr("href"))
//     });
//     b = b.reverse();
//     return $(document).on("scroll", _.throttle(function() {
//       var e, f, d, c;
//       f = $(document).scrollTop();
//       for (d = 0, c = b.length; d < c; d++) {
//         e = b[d];
//         if (f >= e.position().top - 130) {
//           a(e);
//           return
//         }
//       }
//     }, 200))
//   })
// }).call(this);