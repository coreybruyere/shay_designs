// -------------------------------------
//   Start Doc Ready
// -------------------------------------
 
jQuery(document).ready(function($) {
	   
   
 	// -------------------------------------         
 	//   Carousels 
 	// -------------------------------------

	$('.js-slide').slick({  
		dots: true,
		arrows: true,
		infinite: true, 
		autoplay: true, 
		speed: 500,
		fade: true,
		slide: '.js-slide-item',
		cssEase: 'linear'
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
		// alert("hi"); 
		$(this).closest($alert).remove();
	});



	// ------------------------------------- 
	//   Add custom inputs to radio and checkbox
	// -------------------------------------

	// $('input[type=radio').each(function() {

	//     if( !$(this).hasClass('input--radio') ) {
	//     	$(this).addClass('input--radio');
	//     }

	// });

	// $('input[type=checkbox').each(function() {
 
	//     if( !$(this).hasClass('input--checkbox') ) {
	//     	$(this).addClass('input--checkbox');
	//     }

	// });



}); 
// -- End Doc Ready
