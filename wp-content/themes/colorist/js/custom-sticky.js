(function($){
	// Sticky Header Options 
			 
		$(window).scroll(function() {
		if ($(this).scrollTop() > 1){  
		    $('.site-header').addClass("sticky-header");
		    $('.slicknav_menu').addClass("responsive-sticky-header");
		  }
		  else{
		    $('.site-header').removeClass("sticky-header");
		    $('.slicknav_menu').removeClass("responsive-sticky-header");
		  }
		});

})(jQuery); 