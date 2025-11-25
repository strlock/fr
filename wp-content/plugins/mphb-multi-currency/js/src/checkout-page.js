(function ($) {

    "use strict";

    function getTextElementFromNodeList( nodeList ) {

        var length = nodeList.length;

        for( var i = 0; i < length; i++ ) {
            if ( Node.TEXT_NODE === nodeList[i].nodeType ) {
                return nodeList[i];
            }
        }
        return null;
    }

    $( '.mphb_sc_checkout-form' ).on( 'CheckoutDataChanged', function (e) {

        var total = document.querySelector('.mphb-total-price .mphb-price'),
            deposit = document.querySelector('.mphb-deposit-amount .mphb-price'),
            sumForPayment = document.querySelector('#mphbmc-payment-warning .mphb-price'),
            sumForConversion = null;

        if ( null === sumForPayment ) return;

        if ( null !== total ) {

            sumForConversion = getTextElementFromNodeList( total.childNodes );
        }

        if ( null !== deposit ) {

            sumForConversion = getTextElementFromNodeList( deposit.childNodes );
        }

        if ( null !== sumForConversion ) {

            sumForConversion = sumForConversion.nodeValue.trim();

        } else {

            console.log( 'ERROR: Could not find sum for conversion in the total or deposite element!' );
            return;
        }

        $.ajax( {
            url: MPHBMCData.ajaxUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'mphbmc_convert_sum_from_selected_to_default_currency',
                sum_for_conversion: sumForConversion
            },
        } ).done( function( response ) {

            if ( typeof response.data !== 'undefined' && typeof response.data.converted_sum !== 'undefined' ) {

                sumForPayment = getTextElementFromNodeList( sumForPayment.childNodes );

                if ( null !== sumForPayment ) {

                    sumForPayment.nodeValue = response.data.converted_sum.replace( '&nbsp;', String.fromCharCode(160) );
                }
            }
        })
    });

})(jQuery);