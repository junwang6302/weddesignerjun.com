$(document).ready(function() {
    equalheight = function(e) {
        var o = 0,
            i = 0,
            t = new Array,
            n, a = 0;
        $(e).each(function() {
            if (n = $(this), $(n).height("auto"), topPostion = n.position().top, i != topPostion) {
                for (currentDiv = 0; currentDiv < t.length; currentDiv++) t[currentDiv].height(o);
                t.length = 0, i = topPostion, o = n.height(), t.push(n)
            } else t.push(n), o = o < n.height() ? n.height() : o;
            for (currentDiv = 0; currentDiv < t.length; currentDiv++) t[currentDiv].height(o)
        })
    }, $(window).on("load resize", function() {
        equalheight(".sameHeight > li"), equalheight(".sameHeight > a"), equalheight(".sameHeight > ul"), equalheight(".sameHeight > .sh")
    }), $(".loadingWrapper").delay(1e3).animate({
        top: "100%",
        opacity: .9
    }, 500).fadeOut(0), $(window).scroll(function() {
        $(window).scrollTop() > 100 ? $(".websiteHeaderSticky").addClass("aktiv") : $(".websiteHeaderSticky").removeClass("aktiv")
    }), $(".js_showSearch").on("click", function(e) {
        e.preventDefault(), $(".sec .js_showSearch").toggleClass("aktiv"), $(".js_searchBox").toggleClass("js_searchBoxAktiv", function() {
            $(".searchInput").focus()
        })
    }), $(".js_showMobileNav").on("click", function(e) {
        $("body").toggleClass("js_mobileNavOpen"), $(".websiteHeader").toggleClass("darken"), $(".js_searchBox").hasClass("js_searchBoxAktiv") && $(".js_searchBox").toggleClass("js_searchBoxAktiv")
    }), $(".js_hideMobileNav").on("click", function(e) {
        $("body").removeClass("js_mobileNavOpen")
    }), $("a[href^=#]:not(a[href^=#--])").on("click", function(e) {
        e.preventDefault();
        var o = $(this).attr("href"),
            i = 60;
        $("html,body").stop(!0, !0).animate({
            scrollTop: $(o).offset().top - i
        }, 500)
    }), $(function() {
        var e = function() {
                $('<div id="imagelightbox-loading"><div></div></div>').appendTo("body")
            },
            o = function() {
                $("#imagelightbox-loading").remove()
            },
            i = function() {
                $('<div id="imagelightbox-overlay"></div>').appendTo("body")
            },
            t = function() {
                $("#imagelightbox-overlay").remove()
            },
            n = function(e) {
                $('<a href="#" id="imagelightbox-close">Close</a>').appendTo("body").on("click", function() {
                    return $(this).remove(), e.quitImageLightbox(), !1
                })
            },
            a = function() {
                $("#imagelightbox-close").remove()
            },
            s = $('a[href*=".png"],a[href*=".gif"],a[href*=".jpg"]').imageLightbox({
                onStart: function() {
                    i(), n(s)
                },
                onEnd: function() {
                    t(), o(), a()
                },
                onLoadStart: function() {
                    e()
                },
                onLoadEnd: function() {
                    o()
                }
            })
    })
});