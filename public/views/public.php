<?php
/**
 * Represents the view for the public-facing component of the plugin.
 */
?>

<?php

function stwa_visible(){      
 
	$options = get_option( 'stwa_settings');	
    if( ( is_home() || is_front_page() ) && isset( $options['stwa_show']['frontpage'] ) ){
    	return true;
    }else if(is_single() && isset($options['stwa_show']['post'])){
    	return true;
    }else if(is_page() && isset($options['stwa_show']['page'])){
    	return true;
    } else {
    	return false;
    }
   
}


if ( stwa_visible() ){

    if( $options['stwa_placement'] == 'left' ) {	
		echo '<div class="stwa_arrow"><img src="'.plugins_url('../assets/images/arrow_left.png', __FILE__).'"/></div>';
	} else {
		echo '<div class="stwa_arrow"><img src="'.plugins_url('../assets/images/arrow_right.png', __FILE__).'"/></div>';
	}

	dynamic_sidebar( 'scroll-triggered-widget-area' );
}

$defaults = array(
	stwa_animation => "fadeInLeft fadeOutLeft",
	stwa_cookie => "3",
	stwa_display_height => "2000"
);



$options = wp_parse_args( $options, $defaults );
$stwa_placement = $options["stwa_placement"];
$animation = split( " ", $options['stwa_animation'] );
$cookie_expire = $options['stwa_cookie'];  
$scroll_height = $options['stwa_display_height'] == null ? 0 : $options['stwa_display_height'];


$script = 'jQuery(document).ready(function($) {	

				if( $.cookie("scrollpopup") != undefined ){
						$(".stwa_arrow").show();
				}		

				$stwa_box = $("#scroll-item");
				var lastScrollTop = 0;				
				$(window).scroll(function () {
					var st = $(this).scrollTop();
					if(st > lastScrollTop){
					    if ( ( $( window ).scrollTop() + $(window).height() >= $(document).height() -'. $scroll_height .') 
					    	&& ( $.cookie("scrollpopup") == undefined ) ) { 		    	
					    	$(".stwa_arrow").hide();				    	
					    	$stwa_box.removeClass("animated '. $animation[1] .'");				    	
					    	$stwa_box.show();		    	
					    	$stwa_box.addClass("animated '. $animation[0] .'");
					    } 				    
					}

					lastScrollTop = st;
				});

				$(".stwa-close").on("click", function(){					
					$stwa_box.removeClass("animated '. $animation[0] .'");
			    	$stwa_box.addClass("animated '. $animation[1] .'");
			    	$(".stwa_arrow").show();
			    	$.cookie("scrollpopup", "true", { expires:'.$cookie_expire.', path: "/" });
				});

				$(".stwa_arrow").on("click", function(){					
					$(this).hide();
					$.removeCookie("scrollpopup", {path: "/" });
					$stwa_box.removeClass("animated '. $animation[1] .'");				    	
			    	$stwa_box.show();		    	
			    	$stwa_box.addClass("animated '. $animation[0] .'");
				});

				var stwa_arrow_animate = function() {
		   			$(".stwa_arrow").animate({"'.$stwa_placement.'": "-20px"}, 1000, function() { 
		        	$(this).animate({"'.$stwa_placement.'": "20px"}, 1000) 
		   		})

		    		setTimeout(stwa_arrow_animate, 2000);
				}

				stwa_arrow_animate();	
				
			});';

//$script = str_replace( array( "\n", "\t", "\r" ), '', $script );

echo '<script type="text/javascript">' . $script . '</script>';