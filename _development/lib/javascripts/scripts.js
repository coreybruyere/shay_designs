// -------------------------------------
//   Start Doc Ready
// -------------------------------------
 
jQuery(document).ready(function($) {
	   
   

 	// -------------------------------------         
 	//   Carousels 
 	// -------------------------------------

	// $('.js-slide').slick({  
	// 	dots: true,
	// 	arrows: true,
	// 	infinite: true, 
	// 	autoplay: true, 
	// 	speed: 500,
	// 	fade: true,
	// 	slide: '.js-slide-item',
	// 	lazyLoad: 'ondemand', 
	// 	centerMode: true,
	// 	cssEase: 'linear'
	// });

	// $('.js-slide').owlCarousel({   
	// 	items: 1,
	// 	lazyLoad: true,
	// 	loop: true,
	// 	autoHeight: true, 
	// }); 
	
	var $carousel = $('.js-slide');
	var $carouselPrev = $('.js-slide-prev');
	var $carouselNext = $('.js-slide-next');

	$carousel.owlCarousel({
	  singleItem: true,
    items: 1, 
    lazyLoad: true,
    loop: true,
    transitionStyle: "fade"
	});

	$carouselNext.click(function() {
	    $carousel.trigger('next.owl.carousel');
	});

	$carouselPrev.click(function() {
	    $carousel.trigger('prev.owl.carousel');
	});



	// -------------------------------------         
	//   Unveil - Retina and lazy load
	// -------------------------------------
	$img = $('img'); 

	$img.unveil(200, function() {
	  $(this).load(function() {
	    this.style.opacity = 1;
	  });
	});     



	// -------------------------------------         
	//   Header Search Form
	// -------------------------------------

	var $searchBtn = $('#js-header-menu #js-search-form-btn');
	var $searchProp = $('#js-header-menu #js-search-form-prop');
	var $searchField = $('#js-search-form-field');   
	var $searchClose = $('#js-search-form-close');

	$searchBtn.addClass('disabled');

	$searchProp.click(function(event) {
		if ($searchBtn.hasClass('disabled')) { 
			event.stopImmediatePropagation();
			$searchClose.addClass('is-visible-close');  
			$searchField.addClass('is-active-search');  
			$searchProp.addClass('no-margin');
			$searchBtn.removeClass('disabled'); 
			return(false);  
		} else {
			$searchBtn.addClass('disabled');
		}
	});

	$searchClose.click(function() {
		$(this).removeClass('is-visible-close');
		$searchBtn.addClass('disabled');
		$searchField.removeClass('is-active-search');
		$searchProp.removeClass('no-margin'); 
	});




	// -------------------------------------
	//   Menu Toggle
	// -------------------------------------
	
	var $navToggle = $('#js-nav-toggle'); 
	var $navList = $('.js-nav'); 
	// var $navIcon = $('#js-nav-icon'); 

	$navToggle.click(function() {
	    // $(this).toggleClass('is-active-burger'); 
	  	// $navIcon.toggleClass('is-active-icon');
	  	$(this).toggleClass('is-active-btn');
	  	$navList.toggleClass('is-active-nav');
	  	if ($(this).attr('aria-pressed') == 'true') {
	  	  $(this).attr('aria-pressed', 'false');
	  	}
	  	else {
	  	  $(this).attr('aria-pressed', 'true');
	  	}
	  	return false;
  	});  



	// ------------------------------------- 
	//   Remove Alert
	// -------------------------------------

	var $toTop = $('#js-to-top');

	$(document).on( 'scroll', function(){
	 
		if ($(window).scrollTop() > 100) {
			$toTop.addClass('is-active-top');
		} else {
			$toTop.removeClass('is-active-top');
		}
	});
	 
	$toTop.on('click', scrollToTop);

	function scrollToTop() {
		verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
		element = $('body');
		offset = element.offset();
		offsetTop = offset.top;
		$('html, body').animate({scrollTop: offsetTop}, 500, 'linear');
	}



	// ------------------------------------- 
	//   Remove Alert
	// -------------------------------------

	var $removeAlert = $('.js-hide-error');
	var $alert = $('.js-error');

	$removeAlert.click(function() { 
		alert("hi"); 
		$(this).closest($alert).remove();
	});



	// ------------------------------------- 
	//   Add custom inputs to radio and checkbox
	// -------------------------------------

	$('input[type=radio').not('.js-skip').each(function() {

	    if( !$(this).hasClass('input--radio') ) { 
	    	$(this).addClass('input--radio');
	    } 

	});

	$('input[type=checkbox').not('.js-skip').each(function() {
 
	    if( !$(this).hasClass('input--checkbox') ) {
	    	$(this).addClass('input--checkbox');
	    }

	});



}); 
// -- End Doc Ready
