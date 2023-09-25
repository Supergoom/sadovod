jQuery(function( $ ) {

	// настройка
	wp.customize( 'footer_copyright_text', function( value ) {
		value.bind( function( newVal ) {
			$( '.copyright-text' ).text( newVal );
		});
	});

});