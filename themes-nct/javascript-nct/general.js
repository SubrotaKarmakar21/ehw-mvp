var $ = jQuery.noConflict();
	$(document).ready(function(){
		$(function () {
		    $('.selectpicker').selectpicker();
		    $(".dropdown-toggle").dropdown();
		});	

		
		$(window).scroll(function(){
	        backToTop()
	    })
	    $(document).ready(function(){
	        backToTop()
	        $(".back-top").click(function(){
	            $("html,body").animate({scrollTop:0},1200)
	        })
	    })
	    function backToTop(){
	        if($(window).scrollTop() > 150){
	           $(".back-top").fadeIn()
	        } else {
	            $(".back-top").fadeOut()
	        }
	    }
	});

