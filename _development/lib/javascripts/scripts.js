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
	//   Menu Toggle
	// -------------------------------------
	
	var $navToggle = $('#js-nav-toggle'); 
	var $navIcon = $('#js-nav-icon'); 
	var $navList = $('#js-nav');

	$navToggle.click(function() {
	    $(this).toggleClass('is-active-burger');
	  	$navList.toggleClass('is-active-nav');
	  	$navIcon.toggleClass('is-active-icon');
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




	// ------------------------------------- 
	//   Social Media
	// -------------------------------------

	$shareBtn = $('#js-social-share');
	$shareMenu = $('#js-share-menu');

	$shareBtn.click(function() {
	    $shareMenu.toggleClass('is-active-share');
		// alert("wtf"); 
	  	// return false;
	});   




}); 
// -- End Doc Ready
