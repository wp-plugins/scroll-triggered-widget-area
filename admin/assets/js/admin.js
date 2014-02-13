(function ( $ ) {
   $(document).ready( function() {	
		$('.stwa-color-field').wpColorPicker();		
		$('#stwa-clear').click(function(){	
			$.removeCookie('scrollpopup', {path: '/' });										
			$(this).text("Widget area is visible now");
			return false;  
		});

		if( $.cookie("scrollpopup") == undefined  ){
			$("#stwa-clear").text("Widget area is visible now");			
		}
	});
}(jQuery));