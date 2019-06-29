/* SPR Donation Plugin Scripts */
( function( $ ) {
	$( document ).ready( function() {
    $( '.sprdntplgn_bespoke_advanced_p3' ).prop( 'disabled', 'disabled' );
    $( '.sprdntplgn_bespoke_advanced_fixed_p3' ).prop( 'disabled', false );
    $( 'select.sprdntplgn_bespoke_advanced_src' ).change( function(){
      if( $( this ).children( 'option:selected' ).val() == "0" ) {
        $( '.sprdntplgn_bespoke_advanced_fixed_p3' ).prop( 'disabled', 'disabled' );
        $( '.sprdntplgn_bespoke_advanced_p3' ).prop( 'disabled', false );
      } else {
        $( '.sprdntplgn_bespoke_advanced_fixed_p3' ).prop( 'disabled', false );
        $( '.sprdntplgn_bespoke_advanced_p3' ).prop( 'disabled', 'disabled' );
        $( '.sprdntplgn_bespoke_advanced_p3' ).val( '1' );
      }
    });
  });
})(jQuery)