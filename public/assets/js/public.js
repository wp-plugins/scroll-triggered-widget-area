(function ( $ ) {
   $(document).ready( function() {	
		var stwa_height = $("#scroll-item").height();
		if( stwa_height > $(window).height() - 200 ){
			 jQuery("#scroll-item").css("height", "400");
			 jQuery("#scroll-item").css("overflow", "scroll");
		}
	});
}(jQuery));