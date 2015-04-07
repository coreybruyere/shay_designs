$( function() {
	
	var loadModal = function( modal ) {
		
		$.post(
			cb_ajax.ajaxurl,
			{
				action: 'load_modal',
				modal:  modal
			},
			/* Handle the response! */
			function ( response ) {
				
				if ( 0 != response ) {
					console.log(response);
					modal = $.parseHTML( response );
					
					/* Add the modal to the body. */
					$( 'body' ).append( modal );
					
					/* Take various actions when the modal is added. */
					$( modal ).addClass( 'modal-show' );
					$( 'body' ).addClass( 'has-modal' );
					
				}
				
			}
		);
		
	}

	$( '.js-modal' ).on( 'click', function( e ) {
		
		e.preventDefault();

		console.log(e);
		console.log($( this ).data( 'modal' ));
		
		var modal = $( this ).data( 'modal' );
		
		loadModal( modal ); 
		
	} );

});