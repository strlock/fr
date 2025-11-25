(function ( $ ) {

    "use strict";

    $( 'select[name="mphbmc_currency_switcher"]' ).on( 'change', function( e ) {

        var date = new Date();
        date.setTime( date.getTime() + ( 10 * 24 * 60 * 60 * 1000 ) ); // 10 days

        document.cookie = "mphbmc_selected_currency=" + encodeURIComponent( e.currentTarget.value ) + 
            "; expires=" + date.toUTCString() +
            "; path=" + MPHBMCWidgetData.baseUrlPath + 
            "; SameSite=Lax";

        location.reload();
    } );

})( jQuery );