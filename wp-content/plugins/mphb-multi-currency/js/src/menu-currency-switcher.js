(function ( $ ) {

    "use strict";

    $( '.mphbmc-menu-currency-switcher-item a' ).on( 'click', function( e ) {
        
        e.preventDefault();
        e.stopPropagation();
        
        if ( !e.currentTarget.parentElement.classList.contains( 'mphbmc-menu-currency-switcher-item-selected' ) &&
            !e.currentTarget.parentElement.parentElement.classList.contains( 'mphbmc-menu-currency-switcher-item-selected' ) ) {

            var date = new Date();
            date.setTime( date.getTime() + ( 10 * 24 * 60 * 60 * 1000 ) ); // 10 days

            document.cookie = "mphbmc_selected_currency=" + encodeURIComponent( e.currentTarget.title ) + 
                "; expires=" + date.toUTCString() +
                "; path=" + MPHBMCMenuData.baseUrlPath +
                "; SameSite=Lax";

            location.reload();
        }
    } );

})( jQuery );