/* 
== malihu jquery custom scrollbars plugin == 
version: 2.1 
author: malihu (http://manos.malihu.gr) 
plugin home: http://manos.malihu.gr/jquery-custom-content-scroller 
*/
(function(e) {
  var t = {
    init: function(t) {
      function r() {
        return "ontouchstart" in window ? 1 : 0
      }
      var n = {
          set_width: !1,
          set_height: !1,
          horizontalScroll: !1,
          scrollInertia: 550,
          scrollEasing: "easeOutCirc",
          mouseWheel: "auto",
          autoDraggerLength: !0,
          scrollButtons: {
            enable: !1,
            scrollType: "continuous",
            scrollSpeed: 20,
            scrollAmount: 40
          },
          advanced: {
            updateOnBrowserResize: !0,
            updateOnContentResize: !1,
            autoExpandHorizontalScroll: !1
          },
          callbacks: {
            onScroll: function() {},
            onTotalScroll: function() {},
            onTotalScrollOffset: 0
          }
        },
        t = e.extend(!0, n, t);
      return e(document).data("mCS-is-touch-device", !1), r() && e(document).data("mCS-is-touch-device", !0), this.each(function() {
        var n = e(this);
        t.set_width && n.css("width", t.set_width), t.set_height && n.css("height", t.set_height);
        if (!e(document).data("mCustomScrollbar-index")) e(document).data("mCustomScrollbar-index", "1");
        else {
          var i = parseInt(e(document).data("mCustomScrollbar-index"));
          e(document).data("mCustomScrollbar-index", i + 1)
        }
        n.wrapInner("<div class='mCustomScrollBox' id='mCSB_" + e(document).data("mCustomScrollbar-index") + "' style='position:relative; height:100%; overflow:hidden; max-width:100%;' />").addClass("mCustomScrollbar _mCS_" + e(document).data("mCustomScrollbar-index"));
        var s = n.children(".mCustomScrollBox");
        if (t.horizontalScroll) {
          s.addClass("mCSB_horizontal").wrapInner("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />");
          var o = s.children(".mCSB_h_wrapper");
          o.wrapInner("<div class='mCSB_container' style='position:absolute; left:0;' />").children(".mCSB_container").css({
            width: o.children().outerWidth(),
            position: "relative"
          }).unwrap()
        } else s.wrapInner("<div class='mCSB_container' style='position:relative; top:0;' />");
        var u = s.children(".mCSB_container");
        if (!e(document).data("mCS-is-touch-device")) {
          u.after("<div class='mCSB_scrollTools' style='position:absolute;'><div class='mCSB_draggerContainer' style='position:relative;'><div class='mCSB_dragger' style='position:absolute;'><div class='mCSB_dragger_bar' style='position:relative;'></div></div><div class='mCSB_draggerRail'></div></div></div>");
          var a = s.children(".mCSB_scrollTools"),
            f = a.children(".mCSB_draggerContainer"),
            l = f.children(".mCSB_dragger");
          t.horizontalScroll ? l.data("minDraggerWidth", l.width()) : l.data("minDraggerHeight", l.height()), t.scrollButtons.enable && (t.horizontalScroll ? a.prepend("<a class='mCSB_buttonLeft' style='display:block; position:relative;'></a>").append("<a class='mCSB_buttonRight' style='display:block; position:relative;'></a>") : a.prepend("<a class='mCSB_buttonUp' style='display:block; position:relative;'></a>").append("<a class='mCSB_buttonDown' style='display:block; position:relative;'></a>")), s.bind("scroll", function() {
            s.scrollTop(0).scrollLeft(0)
          }), n.data({
            horizontalScroll: t.horizontalScroll,
            scrollInertia: t.scrollInertia,
            scrollEasing: t.scrollEasing,
            mouseWheel: t.mouseWheel,
            autoDraggerLength: t.autoDraggerLength,
            "scrollButtons-enable": t.scrollButtons.enable,
            "scrollButtons-scrollType": t.scrollButtons.scrollType,
            "scrollButtons-scrollSpeed": t.scrollButtons.scrollSpeed,
            "scrollButtons-scrollAmount": t.scrollButtons.scrollAmount,
            autoExpandHorizontalScroll: t.advanced.autoExpandHorizontalScroll,
            "onScroll-Callback": t.callbacks.onScroll,
            "onTotalScroll-Callback": t.callbacks.onTotalScroll,
            "onTotalScroll-Offset": t.callbacks.onTotalScrollOffset
          }).mCustomScrollbar("update");
          if (t.advanced.updateOnBrowserResize) {
            var c;
            e(window).resize(function() {
              c && clearTimeout(c), c = setTimeout(function() {
                n.mCustomScrollbar("update")
              }, 150)
            })
          }
        } else {
          var h = navigator.userAgent;
          if (h.indexOf("Android") != -1) {
            var p = parseFloat(h.slice(h.indexOf("Android") + 8));
            p < 3 ? d("mCSB_" + e(document).data("mCustomScrollbar-index")) : s.css({
              overflow: "auto",
              "-webkit-overflow-scrolling": "touch"
            })
          } else s.css({
            overflow: "auto",
            "-webkit-overflow-scrolling": "touch"
          });
          u.addClass("mCS_no_scrollbar mCS_touch"), n.data({
            horizontalScroll: t.horizontalScroll,
            scrollInertia: t.scrollInertia,
            scrollEasing: t.scrollEasing,
            autoExpandHorizontalScroll: t.advanced.autoExpandHorizontalScroll,
            "onScroll-Callback": t.callbacks.onScroll,
            "onTotalScroll-Callback": t.callbacks.onTotalScroll,
            "onTotalScroll-Offset": t.callbacks.onTotalScrollOffset
          }), s.scroll(function() {
            n.mCustomScrollbar("callbacks", s, u)
          });

          function d(e) {
            var t = document.getElementById(e),
              n = 0,
              r = 0;
            document.getElementById(e).addEventListener("touchstart", function(e) {
              n = this.scrollTop + e.touches[0].pageY, r = this.scrollLeft + e.touches[0].pageX
            }, !1), document.getElementById(e).addEventListener("touchmove", function(e) {
              (this.scrollTop < this.scrollHeight - this.offsetHeight && this.scrollTop + e.touches[0].pageY < n - 5 || this.scrollTop != 0 && this.scrollTop + e.touches[0].pageY > n + 5) && e.preventDefault(), (this.scrollLeft < this.scrollWidth - this.offsetWidth && this.scrollLeft + e.touches[0].pageX < r - 5 || this.scrollLeft != 0 && this.scrollLeft + e.touches[0].pageX > r + 5) && e.preventDefault(), this.scrollTop = n - e.touches[0].pageY, this.scrollLeft = r - e.touches[0].pageX
            }, !1)
          }
        }
        if (t.advanced.updateOnContentResize) {
          var v;
          if (t.horizontalScroll) {
            var m = u.outerWidth();
            r() && s.css({
              "-webkit-overflow-scrolling": "auto"
            })
          } else var m = u.outerHeight();
          v = setInterval(function() {
            if (t.horizontalScroll) {
              t.advanced.autoExpandHorizontalScroll && u.css({
                position: "absolute",
                width: "auto"
              }).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({
                width: u.outerWidth(),
                position: "relative"
              }).unwrap();
              var e = u.outerWidth()
            } else var e = u.outerHeight();
            e != m && (n.mCustomScrollbar("update"), m = e)
          }, 300)
        }
      })
    },
    update: function() {
      var t = e(this),
        n = t.children(".mCustomScrollBox"),
        r = n.children(".mCSB_container");
      e(document).data("mCS-is-touch-device") || r.removeClass("mCS_no_scrollbar");
      var i = n.children(".mCSB_scrollTools"),
        s = i.children(".mCSB_draggerContainer"),
        o = s.children(".mCSB_dragger");
      if (t.data("horizontalScroll")) {
        var u = i.children(".mCSB_buttonLeft"),
          a = i.children(".mCSB_buttonRight"),
          f = n.width();
        t.data("autoExpandHorizontalScroll") && r.css({
          position: "absolute",
          width: "auto"
        }).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({
          width: r.outerWidth(),
          position: "relative"
        }).unwrap();
        var l = r.outerWidth()
      } else var c = i.children(".mCSB_buttonUp"),
        h = i.children(".mCSB_buttonDown"),
        p = n.height(),
        d = r.outerHeight();
      if (d > p && !t.data("horizontalScroll") && !e(document).data("mCS-is-touch-device")) {
        i.css("display", "block");
        var v = s.height();
        if (t.data("autoDraggerLength")) {
          var m = Math.round(p / d * v),
            g = o.data("minDraggerHeight");
          if (m <= g) o.css({
            height: g
          });
          else if (m >= v - 10) {
            var y = v - 10;
            o.css({
              height: y
            })
          } else o.css({
            height: m
          });
          o.children(".mCSB_dragger_bar").css({
            "line-height": o.height() + "px"
          })
        }
        var b = o.height(),
          w = (d - p) / (v - b);
        t.data("scrollAmount", w), t.mCustomScrollbar("scrolling", n, r, s, o, c, h, u, a);
        var E = Math.abs(Math.round(r.position().top));
        t.mCustomScrollbar("scrollTo", E, {
          callback: !1
        })
      } else if (l > f && t.data("horizontalScroll") && !e(document).data("mCS-is-touch-device")) {
        i.css("display", "block");
        var S = s.width();
        if (t.data("autoDraggerLength")) {
          var x = Math.round(f / l * S),
            T = o.data("minDraggerWidth");
          if (x <= T) o.css({
            width: T
          });
          else if (x >= S - 10) {
            var N = S - 10;
            o.css({
              width: N
            })
          } else o.css({
            width: x
          })
        }
        var C = o.width(),
          w = (l - f) / (S - C);
        t.data("scrollAmount", w), t.mCustomScrollbar("scrolling", n, r, s, o, c, h, u, a);
        var E = Math.abs(Math.round(r.position().left));
        t.mCustomScrollbar("scrollTo", E, {
          callback: !1
        })
      } else n.unbind("mousewheel"), n.unbind("focusin"), t.data("horizontalScroll") ? o.add(r).css("left", 0) : o.add(r).css("top", 0), i.css("display", "none"), r.addClass("mCS_no_scrollbar")
    },
    scrolling: function(t, n, r, i, s, o, u, a) {
      var f = e(this);
      if (!i.hasClass("ui-draggable")) {
        if (f.data("horizontalScroll")) var l = "x";
        else var l = "y";
        i.draggable({
          axis: l,
          containment: "parent",
          drag: function(e, t) {
            f.mCustomScrollbar("scroll"), i.addClass("mCSB_dragger_onDrag")
          },
          stop: function(e, t) {
            i.removeClass("mCSB_dragger_onDrag")
          }
        })
      }
      r.unbind("click").bind("click", function(e) {
        if (f.data("horizontalScroll")) {
          var t = e.pageX - r.offset().left;
          if (t < i.position().left || t > i.position().left + i.width()) {
            var n = t;
            n >= r.width() - i.width() && (n = r.width() - i.width()), i.css("left", n), f.mCustomScrollbar("scroll")
          }
        } else {
          var t = e.pageY - r.offset().top;
          if (t < i.position().top || t > i.position().top + i.height()) {
            var n = t;
            n >= r.height() - i.height() && (n = r.height() - i.height()), i.css("top", n), f.mCustomScrollbar("scroll")
          }
        }
      });
      if (f.data("mouseWheel")) {
        var c = f.data("mouseWheel");
        if (f.data("mouseWheel") === "auto") {
          c = 8;
          var h = navigator.userAgent;
          h.indexOf("Mac") != -1 && h.indexOf("Safari") != -1 && h.indexOf("AppleWebKit") != -1 && h.indexOf("Chrome") == -1 && (c = 1)
        }
        t.unbind("mousewheel").bind("mousewheel", function(e, t) {
          e.preventDefault();
          var n = Math.abs(t * c);
          if (f.data("horizontalScroll")) {
            var s = i.position().left - t * n;
            i.css("left", s), i.position().left < 0 && i.css("left", 0);
            var o = r.width(),
              u = i.width();
            i.position().left > o - u && i.css("left", o - u)
          } else {
            var a = i.position().top - t * n;
            i.css("top", a), i.position().top < 0 && i.css("top", 0);
            var l = r.height(),
              h = i.height();
            i.position().top > l - h && i.css("top", l - h)
          }
          f.mCustomScrollbar("scroll")
        })
      }
      if (f.data("scrollButtons-enable"))
        if (f.data("scrollButtons-scrollType") === "pixels") {
          var p;
          e.browser.msie && parseInt(e.browser.version) < 9 && f.data("scrollInertia", 0), f.data("horizontalScroll") ? (a.add(u).unbind("click mousedown mouseup mouseout", g, b), a.bind("click", function(e) {
            e.preventDefault(), n.is(":animated") || (p = Math.abs(n.position().left) + f.data("scrollButtons-scrollAmount"), f.mCustomScrollbar("scrollTo", p))
          }), u.bind("click", function(e) {
            e.preventDefault(), n.is(":animated") || (p = Math.abs(n.position().left) - f.data("scrollButtons-scrollAmount"), n.position().left >= -f.data("scrollButtons-scrollAmount") && (p = "left"), f.mCustomScrollbar("scrollTo", p))
          })) : (o.add(s).unbind("click mousedown mouseup mouseout", x, N), o.bind("click", function(e) {
            e.preventDefault(), n.is(":animated") || (p = Math.abs(n.position().top) + f.data("scrollButtons-scrollAmount"), f.mCustomScrollbar("scrollTo", p))
          }), s.bind("click", function(e) {
            e.preventDefault(), n.is(":animated") || (p = Math.abs(n.position().top) - f.data("scrollButtons-scrollAmount"), n.position().top >= -f.data("scrollButtons-scrollAmount") && (p = "top"), f.mCustomScrollbar("scrollTo", p))
          }))
        } else if (f.data("horizontalScroll")) {
        a.add(u).unbind("click mousedown mouseup mouseout", g, b);
        var d, v = r.width(),
          m = i.width();
        a.bind("mousedown", function(e) {
          e.preventDefault();
          var t = v - m;
          d = setInterval(function() {
            var e = Math.abs(i.position().left - t) * (100 / f.data("scrollButtons-scrollSpeed"));
            i.stop().animate({
              left: t
            }, e, "linear"), f.mCustomScrollbar("scroll")
          }, 20)
        });
        var g = function(e) {
          e.preventDefault(), clearInterval(d), i.stop()
        };
        a.bind("mouseup mouseout", g);
        var y;
        u.bind("mousedown", function(e) {
          e.preventDefault();
          var t = 0;
          y = setInterval(function() {
            var e = Math.abs(i.position().left - t) * (100 / f.data("scrollButtons-scrollSpeed"));
            i.stop().animate({
              left: t
            }, e, "linear"), f.mCustomScrollbar("scroll")
          }, 20)
        });
        var b = function(e) {
          e.preventDefault(), clearInterval(y), i.stop()
        };
        u.bind("mouseup mouseout", b)
      } else {
        o.add(s).unbind("click mousedown mouseup mouseout", x, N);
        var w, E = r.height(),
          S = i.height();
        o.bind("mousedown", function(e) {
          e.preventDefault();
          var t = E - S;
          w = setInterval(function() {
            var e = Math.abs(i.position().top - t) * (100 / f.data("scrollButtons-scrollSpeed"));
            i.stop().animate({
              top: t
            }, e, "linear"), f.mCustomScrollbar("scroll")
          }, 20)
        });
        var x = function(e) {
          e.preventDefault(), clearInterval(w), i.stop()
        };
        o.bind("mouseup mouseout", x);
        var T;
        s.bind("mousedown", function(e) {
          e.preventDefault();
          var t = 0;
          T = setInterval(function() {
            var e = Math.abs(i.position().top - t) * (100 / f.data("scrollButtons-scrollSpeed"));
            i.stop().animate({
              top: t
            }, e, "linear"), f.mCustomScrollbar("scroll")
          }, 20)
        });
        var N = function(e) {
          e.preventDefault(), clearInterval(T), i.stop()
        };
        s.bind("mouseup mouseout", N)
      }
      t.unbind("focusin").bind("focusin", function() {
        t.scrollTop(0).scrollLeft(0);
        var s = e(document.activeElement);
        if (s.is("input,textarea,select,button,a[tabindex],area,object"))
          if (f.data("horizontalScroll")) {
            var o = n.position().left,
              u = s.position().left,
              a = t.width(),
              l = s.outerWidth();
            if (!(o + u >= 0 && o + u <= a - l)) {
              var c = u / f.data("scrollAmount");
              c >= r.width() - i.width() && (c = r.width() - i.width()), i.css("left", c), f.mCustomScrollbar("scroll")
            }
          } else {
            var h = n.position().top,
              p = s.position().top,
              d = t.height(),
              v = s.outerHeight();
            if (!(h + p >= 0 && h + p <= d - v)) {
              var c = p / f.data("scrollAmount");
              c >= r.height() - i.height() && (c = r.height() - i.height()), i.css("top", c), f.mCustomScrollbar("scroll")
            }
          }
      })
    },
    scroll: function(t) {
      var n = e(this),
        r = n.find(".mCSB_dragger"),
        i = n.find(".mCSB_container"),
        s = n.find(".mCustomScrollBox");
      if (n.data("horizontalScroll")) var o = r.position().left,
        u = -o * n.data("scrollAmount"),
        a = i.position().left,
        f = Math.round(a - u);
      else var l = r.position().top,
        c = -l * n.data("scrollAmount"),
        h = i.position().top,
        p = Math.round(h - c);
      if (e.browser.webkit) var d = (window.outerWidth - 8) / window.innerWidth,
        v = d < .98 || d > 1.02;
      n.data("scrollInertia") === 0 || v ? (n.data("horizontalScroll") ? i.css("left", u) : i.css("top", c), t || n.mCustomScrollbar("callbacks", s, i)) : n.data("horizontalScroll") ? i.stop().animate({
        left: "-=" + f
      }, n.data("scrollInertia"), n.data("scrollEasing"), function() {
        t || n.mCustomScrollbar("callbacks", s, i)
      }) : i.stop().animate({
        top: "-=" + p
      }, n.data("scrollInertia"), n.data("scrollEasing"), function() {
        t || n.mCustomScrollbar("callbacks", s, i)
      })
    },
    scrollTo: function(t, n) {
      var r = {
          moveDragger: !1,
          callback: !0
        },
        n = e.extend(r, n),
        i = e(this),
        s, o = i.find(".mCustomScrollBox"),
        u = o.children(".mCSB_container");
      if (!e(document).data("mCS-is-touch-device")) var a = i.find(".mCSB_draggerContainer"),
        f = a.children(".mCSB_dragger");
      var l;
      if (t) {
        if (typeof t == "number") n.moveDragger ? s = t : (l = t, s = Math.round(l / i.data("scrollAmount")));
        else if (typeof t == "string") {
          var c;
          t === "top" ? c = 0 : t === "bottom" && !i.data("horizontalScroll") ? c = u.outerHeight() - o.height() : t === "left" ? c = 0 : t === "right" && i.data("horizontalScroll") ? c = u.outerWidth() - o.width() : t === "first" ? c = i.find(".mCSB_container").find(":first") : t === "last" ? c = i.find(".mCSB_container").find(":last") : c = i.find(t), c.length === 1 ? (i.data("horizontalScroll") ? l = c.position().left : l = c.position().top, e(document).data("mCS-is-touch-device") ? s = l : s = Math.ceil(l / i.data("scrollAmount"))) : s = c
        }
        e(document).data("mCS-is-touch-device") ? i.data("horizontalScroll") ? o.stop().animate({
          scrollLeft: s
        }, i.data("scrollInertia"), i.data("scrollEasing"), function() {
          n.callback && i.mCustomScrollbar("callbacks", o, u)
        }) : o.stop().animate({
          scrollTop: s
        }, i.data("scrollInertia"), i.data("scrollEasing"), function() {
          n.callback && i.mCustomScrollbar("callbacks", o, u)
        }) : (i.data("horizontalScroll") ? (s >= a.width() - f.width() && (s = a.width() - f.width()), f.css("left", s)) : (s >= a.height() - f.height() && (s = a.height() - f.height()), f.css("top", s)), n.callback ? i.mCustomScrollbar("scroll") : i.mCustomScrollbar("scroll", !0))
      }
    },
    callbacks: function(t, n) {
      var r = e(this);
      if (!e(document).data("mCS-is-touch-device"))
        if (r.data("horizontalScroll")) {
          var i = Math.round(n.position().left);
          i < 0 && i <= t.width() - n.outerWidth() + r.data("onTotalScroll-Offset") ? r.data("onTotalScroll-Callback").call() : r.data("onScroll-Callback").call()
        } else {
          var s = Math.round(n.position().top);
          s < 0 && s <= t.height() - n.outerHeight() + r.data("onTotalScroll-Offset") ? r.data("onTotalScroll-Callback").call() : r.data("onScroll-Callback").call()
        }
      else if (r.data("horizontalScroll")) {
        var o = Math.round(t.scrollLeft());
        o > 0 && o >= n.outerWidth() - r.width() - r.data("onTotalScroll-Offset") ? r.data("onTotalScroll-Callback").call() : r.data("onScroll-Callback").call()
      } else {
        var u = Math.round(t.scrollTop());
        u > 0 && u >= n.outerHeight() - r.height() - r.data("onTotalScroll-Offset") ? r.data("onTotalScroll-Callback").call() : r.data("onScroll-Callback").call()
      }
    }
  };
  e.fn.mCustomScrollbar = function(n) {
    if (t[n]) return t[n].apply(this, Array.prototype.slice.call(arguments, 1));
    if (typeof n == "object" || !n) return t.init.apply(this, arguments);
    e.error("Method " + n + " does not exist")
  }
})(jQuery);