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
	//   Header Search Form
	// -------------------------------------

	var $searchHead = $('.js-search-head');
	var $searchProp = $('.js-search-head .js-search-form-prop'); 
	var $searchBox = $('.js-search-head .js-search-form-box'); 
	var $searchInput = $('.js-search-head .js-search-form-input'); 
	var $searchClose = $('.js-search-head .js-search-form-close'); 
	var $mainHead = $('.js-main-header');
	var $mainBrand = $('.js-branding');    

	// $searchProp.bind("click keydown", function(e) {
	// 	if (e.type == "keydown" && e.which == 39 || e.type == "click") 
	// 		$(this).toggleClass('is-toggled-search');  
	// 		$searchBox.toggleClass('is-active-search');
	// 		$searchInput.toggleClass('is-active-input');  
	// });

	$searchProp.click(function() {
		searchToglr( $(this) );
	}); 
  
  // Enter keydown for accesibility 
	$searchProp.keydown(function(event) {
	  if (event.keyCode == 13) {
	    searchToglr( $(this) ); 
	  }
	});


	// function to pass to event types
	function searchToglr(thisObj) {
		thisObj.toggleClass('is-toggled-search');   
		thisObj.siblings($searchBox).toggleClass('is-active-search');
		thisObj.closest($searchHead)
					 .find($searchInput)
					 .focus()
					 .toggleClass('is-active-input');  
					 

		var $closestHead = thisObj.closest($searchHead);
		if( $closestHead.hasClass('s-search--mobile') ) {
			$mainHead 
						 .find($mainBrand)
						 .toggleClass('is-transparent'); 
			$closestHead
						 .find($searchClose)
						 .toggleClass('is-active'); 
		}
	}

	$searchClose.click(function() {
		// remove all classes starting with 'is-'
		$(this).closest($searchHead)
					 .find( $('[class*="is-"]:not(.js-skip)') )
					 .removeClass(function (index, css) {
    					return (css.match (/(^|\s)is-\S+/g) || []).join(' ');
					 });
		$(this).closest($searchHead).find($searchInput).blur();    
 		$mainBrand.removeClass('is-transparent');
	});



	// $searchClose.click(function() {
	// 	$(this).removeClass('is-visible-close');
	// 	$searchBtn.addClass('disabled');
	// 	$searchField.removeClass('is-active-search');
	// 	$searchProp.removeClass('no-margin'); 
	// });

	// Accesibility focus and blur for header only
	// $searchInput.focus(function() {
	// 		$searchForm.addClass('is-focused-form');  
	// }).blur(function() { 
	// 		$searchForm.removeClass('is-focused-form');      
	// });




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
	//   To Top
	// -------------------------------------

	var $toTop = $('#js-to-top');

	$(document).on( 'scroll', function(){
	 
		if ($(window).scrollTop() > 100) {
			$toTop.addClass('is-active-top');
		} else {
			$toTop.removeClass('is-active-top');
		}
	});
	 
	$toTop.on('click touchstart', scrollToTop);  

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
		$(this).closest($alert).remove();
	});



	// ------------------------------------- 
	//   Touch Screen Hover Fix
	// -------------------------------------

	// $('a').on('click touchend', function(e) {
	//     var el = $(this);
	//     var link = el.attr('href');
	//     window.location = link;  
	// }); 



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
	//   AJAX Modals
	// -------------------------------------
	// Global JS is localized in scripts.php
	// Template part called from extras.php

	// -- Load a modal when any element with a modal specified is clicked 
	var loadModal = function( modal ) {
		
		$.post(
			cb_ajax.ajaxurl,
			{
				action: 'load_modal',
				modal:  modal
			},
			// -- Handle response
			function ( response ) {
				
				if ( 0 !== response ) {

					var modalId = modal;

					modal = $.parseHTML( response );
					
					// -- Add the modal to the body. 
					$( 'body' ).append( modal );
					
					// -- Take various actions when the modal is added. 
					$(modal).addClass('is-active-modal');
					$(modal).attr('aria-hidden', 'false'); 
					$(modal).attr('id', modalId);  
					$('body').addClass('is-covered');
				}
				
			}
		);
		
	};

	// -- Click Event
	var $modalToggle = $('.js-modal-toggle');

	$modalToggle.click(function(e) {

		e.preventDefault();

		// -- Button State
		$(this).prop('disabled', true);                 
		$(this).addClass('is-loading');  
		// $('[data-modal=' + modal + ']').focus();
 
		// -- Get Modal Template
		var modal = $(this).data('modal');
		loadModal(modal);  

	});

	// -- Close Modal func
	var closeDialog = function() {
	  $(document).find('.js-modal').remove(); 
		$modalToggle.prop('disabled', false).removeClass('is-loading');     
	};

	// -- Run closeDialog() on click of close button
	$(document).on("click","#js-close-modal",function() { 
		closeDialog();    
	});
	  
	// -- Also run closeDialog() on ESC 
	$(document).keyup(function(e) {
	  if (e.keyCode == 27) {
	    closeDialog();
	  }
	});  

	// -- Close CTA Bar 
	var $closeCTA = $('#js-close-cta');

	$closeCTA.click(function() {
		$(this).closest('.js-cta-bar').addClass('is-closed'); 
	});



}); 
// -- End Doc Ready
