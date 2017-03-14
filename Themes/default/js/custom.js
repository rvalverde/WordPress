		jQuery.noConflict();
		(function( $ ) {
			$(function() {
				// $("body").prepend('<div id="responsive"></div>');
				// $("#header .search").clone().appendTo("#responsive");
				// $("#header .nav").clone().appendTo("#responsive");

				//<?php if ( !is_user_logged_in() ) : ?>
					$("#create").on("click", function() {
						$.fancybox({
							padding		: 0,
					        href		: '#modal-register',
					        beforeLoad 	: function(){
							    $(".sumome-share-client-wrapper").hide();
							},
							afterClose	: function(){
							    $(".sumome-share-client-wrapper").show();
							}
					    });
					});
				//<?php endif; ?>

				function openSearch() {
					$("#header").find(".search").addClass("active");
					$("#header div.search").show();
					$("#header .logo").hide();
					$("#header .responsive").hide();
					$("#header .search .icon").css({'display':'inline-block'});
					$("#s").focus();
				}

				function closeSearch() {
					$("#header").find(".search").removeClass("active");
					$("#header div.search").hide();
					$("#header .logo").show();
					$("#header .responsive").show();
					$("#header .search .icon").css({'display':'none'});
				}

				function openMenu() {
					$("#header .nav").addClass("active").slideDown();
					$("body").addClass("hidden");
				}

				function closeMenu() {
					$("#header .nav").removeClass("active").slideUp();
					$("body").removeClass("hidden");
					$("#header .responsive li.bars").removeClass("active");
				}

				$("#header .responsive .search").on("click", function() {
					openSearch();
					closeMenu();
				});

				$("#header .search .icon").on("click", function() {
					closeSearch();
				});

				$("#header .responsive li.bars").on("click", function() {
					$(this).toggleClass("active");
					if($(this).hasClass("active")) {
						openMenu();
					}
					else {
						closeMenu();
					}
				});

				function resize() {
					var width = $(window).width() > 890;
					if(width) {
						$("#header .search").removeClass("active").removeAttr("style");
						$("#header .logo").removeAttr("style");
						$("#header .nav").removeAttr("style");
						$("#header .responsive").removeAttr("style");
						$("#header .nav").removeClass("active");
						$("body").removeClass("hidden");
						$("#header .responsive li.bars").removeClass("active");
						$("#header .search .icon").removeAttr("style");
					}
				}

				resize();
				$(window).resize(function() {
					resize();
				});

			});
		})(jQuery);