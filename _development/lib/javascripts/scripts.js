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
	// var $body = $('#js-body');
	// var $bodyActive = $('.is-active-body'); 

	$navToggle.click(function() {
	    $(this).toggleClass('is-active-burger');
	  	$navList.toggleClass('is-active-nav');
	  	$navIcon.toggleClass('is-active-icon');
	  	// $body.toggleClass('is-active-body'); 
	  	return false;
  	});  



	// -------------------------------------
	//   Search Toggle
	// -------------------------------------

	var $searchToggle = $('#js-search-toggle');
	var $searchForm = $('#js-search-form');
	var $searchDismiss = $('#js-search-dismiss');

	$searchToggle.click(function() {
		$searchForm.attr('aria-hidden','false');
	    $('html,body').animate({ scrollTop: 0 }, 'slow');
	    return false; 
	});

	$searchDismiss.click(function() {
	    dismissThis( $searchForm );
	});

	function dismissFunc(thisObj) {
	    thisObj.attr('aria-hidden','true'); 
	}



	// -------------------------------------
	//   Search Toggle
	// -------------------------------------




	// -------------------------------------
	//   Tabs
	// -------------------------------------

	// var $tabs =  $('#js-tabs');
	// var $tabItem = $('#js-tabs .js-tab-item');
	// var $tabDrawer = $('#js-tabs .js-drawer-tab');

	// // -- Large screen tabs
	// $tabItem.click(function() {

	//   	$tabs.find('[aria-hidden="false"]').attr('aria-hidden','true');
	
	//   	var activeTab = $(this).attr("rel"); 
	//  	$("#"+activeTab).attr('aria-hidden','false');
	
	//   	$tabItem.removeClass("is-active-tab");
	//   	$(this).addClass("is-active-tab");

	// });

	// // -- Small screen drawer tabs
	// $tabDrawer.click(function() {

	//     $tabs.find('[aria-hidden="false"]').attr('aria-hidden','true');
	  
	//     var activeTab = $(this).attr("rel"); 
	//     $("#"+activeTab).attr('aria-hidden','false');

	//     $tabDrawer.removeClass("is-active-tab");
	//     $(this).addClass("is-active-tab");
	    
	// });



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

	$('input[type=radio').each(function() {

	    if( !$(this).hasClass('input--radio') ) {
	    	$(this).addClass('input--radio');
	    }

	});

	$('input[type=checkbox').each(function() {

	    if( !$(this).hasClass('input--checkbox') ) {
	    	$(this).addClass('input--checkbox');
	    }

	});




	// ------------------------------------- 
	//   Social Media
	// -------------------------------------

	$shareBtn = $('#js-social-share');
	$shareMenu = $('#js-share-menu');

	$shareBtn.click(function() {
	    $shareMenu.toggleClass('is-active-share');
		alert("wtf"); 
	  	return false;
	});   




}); 
// -- End Doc Ready
