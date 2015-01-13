jQuery( function( $ ) {

	// wc_single_product_params is required to continue, ensure the object exists
	if ( typeof wc_single_product_params === 'undefined' ) {
		return false;
	}


	$(document).ready(function() {

		// Tabs
		var $tabs =  $('#js-tabs');
		var $tabItem = $('#js-tabs .js-tab-item');
		var $tabDrawer = $('#js-tabs .js-drawer-tab');
		var $tabPanel = $('.js-tab-panel');  

		$tabPanel.eq(0).addClass('is-active');
		$tabItem.eq(0).addClass('is-active-tab'); 

		$tabItem.click(function() {

			var $tab = $( this ),
				$tabId = $tab.attr('data-tab'),
				$tabPanel = $('.js-tab-panel');  

			$tabItem.removeClass('is-active-tab');
			$tab.addClass('is-active-tab');
			$tabPanel.removeClass('is-active');
			$tabPanel.attr("aria-hidden", "true"); 
			$("#"+$tabId).addClass('is-active');
			$("#"+$tabId).attr('aria-hidden', 'false');

			return false;
		});

	}); // end tab doc ready


	// -- Small screen drawer tabs
	// $tabDrawer.click(function() {

	//     $tabs.find('[aria-hidden="false"]').attr('aria-hidden','true');
	  
	//     // var activeTab = $(this).attr("rel"); 
	//     // $("#"+activeTab).attr('aria-hidden','false');

	//     $tabDrawer.removeClass("is-active-tab");
	//     $(this).addClass("is-active-tab");
	    
	// });

	// $( '.woocommerce-tabs' ).each( function() {
	// 	var hash	= window.location.hash,
	// 		url		= window.location.href,
	// 		tabs	= $( this );

	// 	if ( hash.toLowerCase().indexOf( "comment-" ) >= 0 ) {
	// 		$('ul.tabs li.reviews_tab a', tabs ).click();

	// 	} else if ( url.indexOf( "comment-page-" ) > 0 || url.indexOf( "cpage=" ) > 0 ) {
	// 		$( 'ul.tabs li.reviews_tab a', $( this ) ).click();

	// 	} else {
	// 		$( 'ul.tabs li:first a', tabs ).click();
	// 	}
	// });

	// $( 'a.woocommerce-review-link' ).click( function() {
	// 	$( '.reviews_tab a' ).click();
	// 	return true;
	// });

	// Star ratings for comments
	$( '#rating' ).hide().before( '<p class="stars"><span><a class="star-1" href="#">1</a><a class="star-2" href="#">2</a><a class="star-3" href="#">3</a><a class="star-4" href="#">4</a><a class="star-5" href="#">5</a></span></p>' );

	$( 'body' )
		.on( 'click', '#respond p.stars a', function() {
			var $star   = $( this ),
				$rating = $( this ).closest( '#respond' ).find( '#rating' );

			$rating.val( $star.text() );
			$star.siblings( 'a' ).removeClass( 'active' );
			$star.addClass( 'active' );

			return false;
		})
		.on( 'click', '#respond #submit', function() {
			var $rating = $( this ).closest( '#respond' ).find( '#rating' ),
				rating  = $rating.val();

			if ( $rating.size() > 0 && ! rating && wc_single_product_params.review_rating_required === 'yes' ) {
				alert( wc_single_product_params.i18n_required_rating_text );

				return false;
			}
		});

	// prevent double form submission
	$( 'form.cart' ).submit( function() {
		$( this ).find( ':submit' ).attr( 'disabled','disabled' );
	});
});
