/* SPR Donation Plugin Scripts */
( function( $ ) {
	$( document ).ready( function() {
    $( '.sprdntplgn_bespoke_advanced_srt' ).prop( 'disabled', 'disabled' );
    $( 'select.sprdntplgn_bespoke_advanced_src' ).change( function(){
      if( $( this ).children( 'option:selected' ).val() == "0" ) {
        $( '.sprdntplgn_bespoke_advanced_srt' ).prop( 'disabled', false );
      } else {
        $( '.sprdntplgn_bespoke_advanced_srt' ).prop( 'disabled', 'disabled' );
        $( '.sprdntplgn_bespoke_advanced_srt' ).val( '1' );
      }
    });
  });
})(jQuery)
