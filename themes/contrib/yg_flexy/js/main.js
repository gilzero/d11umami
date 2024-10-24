(function ($) {
 Drupal.behaviors.yg_technology = {
   attach: function (context, settings) {
		"use strict";
		jQuery(window).scroll(function(){
			var scroll = $(window).scrollTop();
			if (scroll >= 100) {
			    $(".navbar").addClass("sticky");
			} else {
			    $(".navbar").removeClass("sticky");
			}

		});

		if (!document.getElementById("counter")) {
		} 
		else {
		
		var lastWasLower = false;
			$(document).scroll(function(){
			
			var p = $( "#counter" );
			var position = p.position();
			var position2 = position.top;
		
			if ($(document).scrollTop() > position2-300){
			if (!lastWasLower)
				$(".counter-value").each( function(){
					var id=$(this).attr("counter-id");
					id= "#"+id;
					$(id).html($(this).text());
				});
				
			lastWasLower = true;
				} else {
			lastWasLower = false;
			}
			});		
		};

		 $('.clients').owlCarousel( {
            loop:true, nav:false, dots:false, autoplay:true, autoplayTimeout:3000, responsiveClass:true, autoplayHoverPause:false, responsive: {
                0: {
                    items: 2, margin: 20
                }
                , 768: {
                    items: 3, margin: 40,
                }
                , 992: {
                    items: 4, margin: 60,
                }
                , 1200: {
                    items: 5, margin: 80,
                }
            }
        });
		$("#testimonial-slider").owlCarousel({
	        items:1,
	        itemsDesktop:[1000,1],
	        itemsDesktopSmall:[979,1],
	        itemsTablet:[768,1],
	        pagination: true,
	        autoPlay:true
	    });
	    $(document).click(function(e) {
		    if (!$(e.target).is('.panel-body')) {
		      $('.collapse').collapse('hide');      
		    }
	    });
	    $('.carousel-item:first-child').addClass('active');


}}})(jQuery, Drupal);// End of use strict