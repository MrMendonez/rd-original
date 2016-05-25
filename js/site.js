//Image Loaded Plugin
function veovidfunc(vid){
	
	if(vid==1){
		frameUrl = 'http://player.vimeo.com/video/81575089?autoplay=1';
		$(".motion-design").addClass("active");
		$(".info-graph").removeClass("active")
		$(".animation-reel").removeClass("active");
	}	
	if(vid==2){
		frameUrl = 'http://player.vimeo.com/video/73573086?autoplay=1';
		$(".info-graph").addClass("active");
		$(".motion-design").removeClass("active")
		$(".animation-reel").removeClass("active");
	}
	if(vid==3){
		frameUrl = 'http://player.vimeo.com/video/73516575?autoplay=1';
		$(".animation-reel").addClass("active");
		$(".motion-design").removeClass("active")
		$(".info-graph").removeClass("active");
		
	}
	
	$("nav").find("li").removeClass("pact active");
	$("nav").find(".showreels").addClass("pact active")
	$("#iframe-container").attr('src', frameUrl);
	
}
// function prevVideo(){
	
// 	if($(".animation-reel").hasClass("active")){
// 		veovidfunc(2);
// 		$(this).preventDefault();
// 	}
// 	if($(".info-graph").hasClass("active")){
// 		veovidfunc(1);
// 		$(this).preventDefault();
// 	}
// 	if($(".motion-design").hasClass("active")){
// 		return true;
// 	}
	
// }

function prevVideo() {
	Shadowbox.close(), prevGallery.length && ($("#prev-overlay").css({
		display: "block",
		width: $(window).width(),
		height: $(window).height()
	}), setTimeout(function() {
		$(prevGallery).bind("click").trigger("click")
	}, 500))
}

function nextVideo() {
	Shadowbox.close(), nextGallery.length && ($("#next-overlay").css({
		display: "block",
		width: $(window).width(),
		height: $(window).height()
	}), setTimeout(function() {
		$(nextGallery).bind("click").trigger("click")
	}, 500))
}

// function nextVideo(){
	
// 	if($(".motion-design").hasClass("active")){
// 		veovidfunc(2);
// 		$(this).preventDefault();
// 	}
// 	if($(".info-graph").hasClass("active")){
// 		veovidfunc(3);
// 		$(this).preventDefault();
// 	}
// 	if($(".animation-reel").hasClass("active")){
// 		return true;
// 	}
	
// }
function goShowReels(vid){
				
				$( "#sb-body-inner iframe" ).remove();
				$("#sb-body-inner img#sb-player").remove();
				$('#sb-player').html('');
				var mcont = '<div class="mwrap"><div class="sreels"><ul><li class="motion-design"><a href="#" data-rel="reels" onClick="veovidfunc(1)">MOTION DESIGN REEL</a></li><li class="info-graph"><a href="#" onClick="veovidfunc(2)">INFO GRAPHICS REEL</a></li><li class="animation-reel"><a href="#" onClick="veovidfunc(3)">ANIMATION REEL</a></li></ul></div><iframe id="iframe-container" width="98%" height="510" frameborder="0" scrolling="auto" src="http://player.vimeo.com/video/81575089?autoplay=1"  style="visibility: visible;" marginheight="0" marginwidth="0" name="sb-player" id="sb-player"></iframe><div id="custom-container" class="clearfix"><div id="employer"><div id="agency">REEL 2014</div><div id="city">USA</div></div><div id="caption">This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls.</div><div class="clearfix" id="category">reel</div></div></div>';
				
			   if ($('#sb-player').is(':empty')){
				 $("#sb-player").append(mcont);
			   }
			   $("#sb-nav-next").show();
			    $("#sb-nav-previous").show();
				$("#sb-wrapper").removeAttr("class"), $("#sb-wrapper").addClass("temp1-modal showreels"), $("#sb-wrapper-inner").css({height: "795px"}), $("#sb-wrapper").css({width: "960px"});
				$("#sb-body-inner, #sb-nav-previous, #sb-nav-next").css({
					opacity: 0
				});
				setTimeout(function() {
					$("#sb-body-inner, #sb-nav-previous, #sb-nav-next").animate({opacity: 1});
				}, 400);
			   $("#sb-nav-close").css({	opacity: 0});
			 
			   veovidfunc(vid);
			   
				
				
				
				
}

(function(e, t) {
	var n = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
	e.fn.imagesLoaded = function(r) {
		function i() {
			var t = e(p),
				n = e(d);
			u && (d.length ? u.reject(f, t, n) : u.resolve(f)), e.isFunction(r) && r.call(o, f, t, n)
		}
		function s(t, r) {
			t.src === n || -1 !== e.inArray(t, h) || (h.push(t), r ? d.push(t) : p.push(t), e.data(t, "imagesLoaded", {
				isBroken: r,
				src: t.src
			}), a && u.notifyWith(e(t), [r, f, e(p), e(d)]), f.length === h.length && (setTimeout(i), f.unbind(".imagesLoaded")))
		}
		var o = this,
			u = e.isFunction(e.Deferred) ? e.Deferred() : 0,
			a = e.isFunction(u.notify),
			f = o.find("img").add(o.filter("img")),
			h = [],
			p = [],
			d = [];
		return e.isPlainObject(r) && e.each(r, function(e, t) {
			"callback" === e ? r = t : u && u[e](t)
		}), f.length ? f.bind("load.imagesLoaded error.imagesLoaded", function(e) {
			s(e.target, "error" === e.type)
		}).each(function(r, i) {
			var o = i.src,
				u = e.data(i, "imagesLoaded");
			if (u && u.src === o) s(i, u.isBroken);
			else if (i.complete && i.naturalWidth !== t) s(i, 0 === i.naturalWidth || 0 === i.naturalHeight);
			else if (i.readyState || i.complete) i.src = n, i.src = o
		}) : i(), u ? u.promise(o) : o
	}
})(jQuery), 




$(window).load(function() {
	var e = $("#workGrid");
	e.imagesLoaded(function() {
		$('body').addClass('imagesLoaded');
		$("#scrollContainer").css({
			background: "none"
		}), e.animate({
			opacity: 1
		}, 600), e.isotope({
			itemSelector: ".item-container",
			layoutMode: "fitRows"
		}), $("#filter").find("a").click(function() {
			var n = $(this).attr("data-filter");
			return e.isotope({
				filter: n
			}), $("#filter").find("a").removeClass("active"), $(this).addClass("active"), window.navigator.userAgent.toLowerCase().indexOf("mobile") === -1 && setTimeout(t, 800), !1
		})
		var delay = 0;
		$('.promoted-item').each(function(index){
			var self = this;
			delay += 250;
			window.setTimeout(function(){
				$(self).addClass('promoted-fadein');
				$("#filter ul li li:first-child a").trigger('click');
			}, delay);
			
		});
	});
	var t = function() {
		$("#scrollContainer").mCustomScrollbar("update")
	}

}),




function(e) {
	e(window).load(function() {
		window.navigator.userAgent.toLowerCase().indexOf("mobile") === -1 && e("#scrollContainer").mCustomScrollbar({
			autoDraggerLength: !1,
			setHeight: true
		})
	})

}(jQuery), $(function() {
	Shadowbox.init({
		continuous: true,
		counterLimit: 20,
		counterType:'skip',
		initialHeight: 765,
		initialWidth: 960,
		animate: !1,
		onOpen: function() {
			$("#sb-wrapper").css({
				opacity: 0
			}), setTimeout(function() {
				$("#sb-wrapper").animate({
					opacity: 1
				}, 800, "swing")
			}, 1e3)
		},
		onClose: function() {
			$("nav").find("li").removeClass("active"), $("#sb-wrapper").removeAttr("class"), $("#news").length && $("nav").find(".first").addClass("active"), $("#next-overlay").css({
				display: "none"
			})
		}
	});
	
	var e = function() {
		var e = $("#slider"),
			t = e.find("img").length,
			n = 0,
			r = e.find("img")[n],
			i = $("#headline").find("h2");
		e.find("ul").css("width", t * 960), i.text($(r).data("caption")), $("#controlLeft").on("click", function() {
			window.clearInterval(o), n > 0 ? (n--, e.find("ul").animate({
				left: -960 * n
			}, 400, "swing")) : (n = t - 1, e.find("ul").animate({
				left: -960 * (t - 1)
			}, 400, "swing")), i.fadeOut("fast", function() {
				r = e.find("img")[n], i.text($(r).data("caption")).fadeIn("fast")
			})
		}), $("#controlRight").on("click", function() {
			window.clearInterval(o), s()
		});
		var s = function() {
			n < t - 1 ? (n++, e.find("ul").animate({
				left: -960 * n
			}, 400, "swing")) : (n = 0, e.find("ul").animate({
				left: 0
			}, 400, "swing")), i.fadeOut("fast", function() {
				r = e.find("img")[n], i.text($(r).data("caption")).fadeIn("fast")
			})
		};
		$("#controlLeft").on({
			mouseenter: function() {
				$(this).animate({
					left: -10
				}, 100, "swing")
			},
			mouseleave: function() {
				$(this).animate({
					left: 0
				}, 300, "swing")
			}
		}), $("#controlRight").on({
			mouseenter: function() {
				$(this).animate({
					right: -10
				}, 100, "swing")
			},
			mouseleave: function() {
				$(this).animate({
					right: 0
				}, 300, "swing")
			}
		});
		var o = setInterval(function() {
			s()
		}, 3e3);
		t <= 1 && (window.clearInterval(o), $("#controlLeft, #controlRight").remove(), i.text($("iframe").attr("rel")))
	};
	$("#feature").length && e();
	var t = function() {
		$("#workToggle").find("li").on({
			click: function() {
				$(this).hasClass("active") || ($("#workToggle").find("li").removeClass("active"), $(this).addClass("active"), n($(this).data("section")))
			}
		})
	}, n = function(e) {
		e === "work" ? ($("#awards").css("display", "none"), $("#filter, #scrollContainer").css("display", "block"), $("#workContainer").animate({
			height: 690
		})) : ($("#filter, #scrollContainer").css("display", "none"), $("#awards").css("display", "block"), $("#workContainer").animate({
			height: $("#awards").outerHeight(!0)
		}, "fast", "swing"))
	};
	$("#work").length && t();
	var r = function() {
		$("#awards").find(".col-left").each(function() {
			var e = $(this).parent().find("ul").outerHeight();
			$(this).css("height", e)
		})
	}, i = function() {
		$("#sb-nav-previous").on({
			mouseenter: function() {
				$(this).animate({
					left: -150
				}, 100, "swing"), $("#sb-counter").find("a").animate({
					top: 0
				}, "fast", "swing")
			},
			mouseleave: function() {
				$(this).animate({
					left: -140
				}, 300, "swing"), $("#sb-counter").find("a").animate({
					top: 30
				}, "fast", "swing")
			}
		}), $("#sb-nav-next").on({
			mouseenter: function() {
				$(this).animate({
					right: -150
				}, 100, "swing"), $("#sb-counter").find("a").animate({
					top: 0
				}, "fast", "swing")
			},
			mouseleave: function() {
				$(this).animate({
					right: -140
				}, 300, "swing"), $("#sb-counter").find("a").animate({
					top: 30
				}, "fast", "swing")
			}
		}), $("#sb-wrapper").on({
			mouseenter: function() {
				$("#sb-counter").find("a").animate({
					top: 0
				}, "fast", "swing")
			},
			mouseleave: function() {
				$("#sb-counter").find("a").animate({
					top: 30
				}, "fast", "swing")
			}
		})
	};
	
	$(".item-container").on("click", function() {
		i(), $("#sb-wrapper").css({
			display: "block"
		});
		var e = $(this).children("a").data("agency"),
			t = $(this).children("a").data("city"),
			n = $(this).children("a").data("caption"),
			r = $(this).children("a").data("filter"),
			s = $("#filter").find(".active").data("filter") !== "*" ? $("#filter").find(".active").data("filter") : "";
		prevGallery = $(this).prevAll(s + ":first").children("a"), $("#agency").text(e), $("#city").text(t), $("#caption").text(n), $("#category").text(r); 
		nextGallery = $(this).nextAll(s + ":first").children("a"), $("#agency").text(e), $("#city").text(t), $("#caption").text(n), $("#category").text(r); 
		nextGallery.length === 0 ? $("#next-project").hide() : $("#next-project").show();
		$("#next-project").unbind("click").on({
			click: function() {
				Shadowbox.close(), nextGallery.length && ($("#next-overlay").css({
					display: "block",
					width: $(window).width(),
					height: $(window).height()
				}), setTimeout(function() {
					$(nextGallery).bind("click").trigger("click")
				}, 500))
			}
		})
	}), $(".item-container").on({
		mouseenter: function() {
			$(this).find("p").addClass("hover")
		},
		mouseleave: function() {
			$(this).find("p").removeClass("hover")
		}
	});
	var s = function() {
		var url = window.location.href;
		
		function navCheck(){
			$( "nav li" ).each(function() {
				$(this).removeClass('active');
			});
			if(url == "links/"){
				$('nav li.news').addClass('pact');
				
				
			} else if(url == "links/work.php"){
				$('nav li.work').addClass('pact');
			}
			$("#sb-wrapper").removeAttr("class");
		}
		
		function myScroll(){
			$("#bfContainer").mCustomScrollbar();
		}
		
		$("nav li").on({
			click: function() {
				$( "nav li" ).each(function() {
					if($(this).hasClass('pact')){
						$(this).removeClass('pact');
					} 
				});
				
				$('div#sb-body-inner:not(:has(div#sb-player))').each(function(){
				  $('<div id="sb-player" class="html"></div>')
					.appendTo(this);
				});
			} 
		}), $(".about").on({
			click: function() {
				return Shadowbox.open({
					content: '<div id="about-container"><div class="column-container clearfix"><div class="left-col"><div class="img-container"><img src="' + $(".about-data").data("photo") + '"></div></div><div class="right-col"><span>Capabilities</span><ul><li>after effects</li><li>illustrator</li><li>photoshop</li><li>cinema 4d</li><li>final cut</li><li>maya</li></ul><a class="linkedin" href="http://linkedin.com/pub/ryan-decarlo/9/551/59" target="_blank"></a></div></div><p class="bio">' + $(".about-data").data("bio") + "</p></div>",
					player: "html",
					height: 500,
					width: 500
				}), $(this).parent().find("li").removeClass("active"), $(this).addClass("active"), $("#sb-wrapper").removeAttr("class"), $("#sb-wrapper").addClass("contact-modal"), $("#sb-body-inner").css({
					opacity: 0
				}), setTimeout(function() {
					$("#sb-body-inner").animate({
						opacity: 1
					}, 400)
				}, 1e3), !1
			}
		}), $(".contact").on({
			click: function() {
				$( "#sb-body-inner iframe" ).remove();
				$("#sb-body-inner img#sb-player").remove();
				$('#sb-player').html('');
				var mcont = '<div id="contact-container"><h3>Contact</h3><div class="column-container clearfix"><div class="left-col"><span class="name">Ryan Decarlo</span><span class="title">Design + Creative Direction</span><span class="web">LOS ANGELES, CA 90025<br/>(TEL)  +1.518.421.0616</span></div><div class="right-col"><a href="mailto:ryandecarlo@gmail.com"><div class="contact-img"></div></a></div></div><hr/><div class="msocial"><h4>Follow Me</h4><a href="https://vimeo.com/user3129735" target="_blank"><img src="images/vimeo.png"></a><a href="https://www.linkedin.com/in/ryandecarlo" target="_blank"><img src="images/linkedin.png"></a><a href="https://www.behance.net/ryandecarlo" target="_blank"><img src="images/be.png"></a><br/>Share<a href="mailto:ryandecarlo@gmail.com" class="share-lnk"><img class="socion" src="images/share.png"></a></div>';
				
			   if ($('#sb-player').is(':empty')){
				 $("#sb-player").append(mcont);
			   }  
			   
				return Shadowbox.open({
					content: mcont,
					player: "html",
					height: 795,
					width: 960,
					options: { 
							onClose: navCheck
					}
				}), $(this).parent().find("li").removeClass("active"), $(this).addClass("active"), $("#sb-wrapper").removeAttr("class"), $("#sb-wrapper").addClass("contact-modal"), $("#sb-wrapper-inner").css({height: "795px"}), $("#sb-wrapper").css({width: "960px"}), $("#sb-body-inner, #sb-nav-close").css({
					opacity: 0
				}), setTimeout(function() {
					$("#sb-body-inner, #sb-nav-close").animate({
						opacity: 1
					}, 400)
				}, 1e3), !1 
			}
		}), $(".whatido").on({
			click: function() {
				$( "#sb-body-inner iframe" ).remove();
				$("#sb-body-inner img#sb-player").remove();
				$('#sb-player').html('');
				
				var mbio = $(".about-data").data("bio");
				var mimg = $(".about-data").data("photo");
				
				mbio = mbio.replace(/  /g, "</p><p>");
				
				var mcont = '<div class="mwrap"><img src="'+ mimg +'" class="mimg"><div id="bfContainer" class="bioinfo"><p>' + mbio + '</p></div><hr/><div class="msocial"><h4>Follow Me</h4><a href="https://vimeo.com/user3129735" target="_blank"><img src="images/vimeo.png"></a><a href="https://www.linkedin.com/in/ryandecarlo"><img src="images/linkedin.png"></a><br/>Share<a href="mailto:ryandecarlo@gmail.com" class="share-lnk"><img class="socion" src="images/share.png"></a></div></div>';
				
			   if ($('#sb-player').is(':empty')){
				 $("#sb-player").append(mcont);
			   } 
				
				return Shadowbox.open({
					content: mcont,
					player: "html",
					height: 795,
					width: 960,
					options: { 
							onClose: navCheck,
							onFinish: myScroll
					}
				}), $(this).parent().find("li").removeClass("active"), $("#bfContainer").mCustomScrollbar(), $(this).addClass("active"),$("#sb-wrapper").removeAttr("class"), $("#sb-wrapper").addClass("whatido-modal"), $("#sb-wrapper-inner").css({height: "795px"}), $("#sb-wrapper").css({width: "960px"}), $("#sb-body-inner, #sb-nav-close").css({
					opacity: 0
				}), setTimeout(function() {
					$("#sb-body-inner, #sb-nav-close").animate({
						opacity: 1
					}, 400)
				}, 1e3), !1
			}
		}), $(".showreels").on({
			click: function() {
				$( "#sb-body-inner iframe" ).remove();
				$("#sb-body-inner img#sb-player").remove();
				$('#sb-player').html('');
				var mcont = '<div class="mwrap"><div class="sreels"><ul><li class="motion-design"><a href="#" data-rel="reels" onClick="veovidfunc(1)">MOTION DESIGN REEL</a></li><li class="info-graph"><a href="#" onClick="veovidfunc(2)">INFO GRAPHICS REEL</a></li><li class="animation-reel"><a href="#" onClick="veovidfunc(3)">ANIMATION REEL</a></li></ul></div><iframe id="iframe-container" width="98%" height="510" frameborder="0" scrolling="auto" src="http://player.vimeo.com/video/81575089?autoplay=1"  style="visibility: visible;" marginheight="0" marginwidth="0" name="sb-player" id="sb-player"></iframe><div id="custom-container" class="clearfix"><div id="employer"><div id="agency">REEL 2014</div><div id="city">USA</div></div><div id="caption">This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls.</div><div class="clearfix" id="category">reel</div></div></div>';
				
			   if ($('#sb-player').is(':empty')){
				 $("#sb-player").append(mcont);
			   }  
			   		$(".motion-design").addClass("active");
					$(".info-graph").removeClass("active")
					$(".animation-reel").removeClass("active");
				return Shadowbox.open({
					content: mcont,
					player: "html",
					height: 795,
					width: 960,
					options: { 
							onClose: navCheck
					}
				}), $(this).parent().find("li").removeClass("active"), $(this).addClass("active"), $("#sb-wrapper").removeAttr("class"), $("#sb-wrapper").addClass("temp1-modal showreels"), $("#sb-wrapper-inner").css({height: "795px"}), $("#sb-wrapper").css({width: "960px"}), $("#sb-body-inner, #sb-nav-previous, #sb-nav-next").css({
					opacity: 0
				}),setTimeout(function() {
					$("#sb-body-inner, #sb-nav-previous, #sb-nav-next").animate({
						opacity: 1
					}, 400);
					veovidfunc(1);
				}, 1e3), !1
			}
		}),$(".news").on({
			click: function() {
				
				
				 $("nav").find(".news").addClass("pact active");
				 $("#inner-content").css({
					opacity: 0
					}), setTimeout(function() {
								$("#inner-content").animate({
									opacity: 1
								}, 400)
							}, 700);
					
				
			}
		}), $(".bio").on({
			click: function() {
				$( "#sb-body-inner iframe" ).remove();
				$("#sb-body-inner img#sb-player").remove();
				$('#sb-player').html('');
				var mcont = '<div class="mwrap"><div id="bfContainer" class="bioinfo"><p>This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullyin.</p><p>This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. nd functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls.This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated s both as an anti-bullying.</p><p>This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. nd functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls.This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated s both as an anti-bullying.</p><p>This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. nd functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls.This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated s both as an anti-bullying.</p><p>This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. nd functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls.This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated campaign for Secret deodorant targets teens, and functions both as an anti-bullying initiative and a brand act that positions Secret as an ally for girls. This integrated s both as an anti-bullying.</p><div class="bsocial">SHARE<a href="mailto:ryandecarlo@gmail.com" class="share-lnk"><img class="socion" src="images/share.png"></a>FOLLOW<a href="https://vimeo.com/user3129735" target="_blank"><img class="socion" src="images/follow.png"></a></div></div><br/><hr/><div class="sreels"><ul><li><a href="#showreels" onClick="goShowReels(1)">MOTION DESIGN REEL</a></li><li><a href="#showreels"  onClick="goShowReels(2)">INFO GRAPHICS REEL</a></li><li><a href="#showreels" onClick="goShowReels(3)">ANIMATION REEL</a></li></ul><br/><img src="images/bio.jpg"><img src="images/bio.jpg"><img src="images/bio.jpg" class="last"></div></div>';
				
			   if ($('#sb-player').is(':empty')){
				 $("#sb-player").append(mcont);
			   }  
			   
				
				return Shadowbox.open({
					content: mcont,
					player: "html",
					height: 795,
					width: 960,
					options: { 
							onClose: navCheck,
							onFinish: myScroll
					}
				}), $(this).parent().find("li").removeClass("active"), $("#bfContainer").mCustomScrollbar(), $(this).addClass("active"), $("#sb-wrapper").removeAttr("class"), $("#sb-wrapper").addClass("temp1-modal bio"), $("#sb-wrapper-inner").css({height: "795px"}), $("#sb-wrapper").css({width: "960px"}), $("#sb-body-inner, #sb-nav-close").css({
					opacity: 0
				}), setTimeout(function() {
					$("#sb-body-inner, #sb-nav-close").animate({
						opacity: 1
					}, 400)
				}, 1e3), !1
			}
		})
	};
	$("nav").length && s()
});

$(function() {
	if ($('#workGrid').length) {
		$('#workGrid').find('.item-container').children('a').on({
			click: function(e) {
				e.preventDefault();
				$(this).siblings('.entries').find('a').eq(0).trigger('click');
				return false;
			}
		});
		$('body').on({
			click : function () {
				if($(this).children('img').length){
					window.parent.Shadowbox.next();
					console.log('next');
				} else {
					console.log('no where to go');
				}
			}
		}, '#sb-body-inner');
		$('.reel').find('entries').find('a').eq(1).remove();
		
	}
	
		$("nav li.pag").on({
			click: function(e) {
			Shadowbox.close();
			navCheck();
			}
		});
		$("#sb-nav-next").click(function(){
			console.log("test");
		});
		
	
});