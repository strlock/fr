<?php
function themo_add_elementor_widget_categories( $elements_manager ) {

    $tempName = 'Themovation'; 
    
    $elements_manager->add_category(
        'themo-elements',
        [
            'title' => $tempName." ".__( 'General', ALOHA_DOMAIN ),
            'icon' => 'font',
        ]
    );
    $elements_manager->add_category(
        'themo-site',
        [
            'title' => $tempName." ".__( 'Site', ALOHA_DOMAIN ),
            'icon' => 'font',
        ]
    );

}
add_action( 'elementor/elements/categories_registered', 'themo_add_elementor_widget_categories' );


