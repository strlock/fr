<?php
//-----------------------------------------------------
// mphb_room_type
// MotoPress Accommodation Post Type Add-ons
//-----------------------------------------------------
aloha_load_wp_options();
use IgniteKit\WP\OptionBuilder\Framework;

add_action( 'admin_init', 'th_register_mphb_room_type_meta_boxes' );

function th_register_mphb_room_type_meta_boxes()
{

    //-----------------------------------------------------
    // Page Layout, Sidebar, Content Editor Sort Order
    //-----------------------------------------------------
    $framework = new Framework();
    $framework->register_metabox( array(
        'id' => 'th_rooms_meta_box',
        'title' => __('Accommodation Grid Options', ALOHA_DOMAIN),
        'pages' => array('mphb_room_type'),
        'context' => 'normal',
        'priority' => 'default',
        'fields' => array(
            // START PAGE LAYOUT META BOX

            array(
                'id' => 'th_room_highlight',
                'label' => 'Highlight',
                'type' => 'text',
                'desc' => __('Displayed at the very top in small text. 1 - 3 words recommended', ALOHA_DOMAIN),
            ),
            array(
                'id' => 'th_room_title',
                'label' => 'Title',
                'type' => 'text',
                'desc' => __('Defaults to the page title.', ALOHA_DOMAIN),
            ),
            array(
                'id' => 'th_room_intro',
                'label' => 'Intro',
                'type' => 'text',
                'desc' => __('Displayed below the title. 8 - 10 words recommended', ALOHA_DOMAIN),
            ),
            array(
                'id'    => "th_room_price",
                'label'  =>  'Price',
                'type'  => 'text',
                'desc' => __('Displayed below the title. e.g.: $99', ALOHA_DOMAIN),
            ),
            array(
                'id'    => "th_room_price_before",
                'label'  =>  'Price before',
                'type'  => 'text',
                'desc' => __('Displayed before the price. e.g.: Starting from', ALOHA_DOMAIN),
            ),
            array(
                'id'    => "th_room_price_per",
                'label'  =>  'Price per',
                'type'  => 'text',
                'desc' => __('Displayed after the price. e.g.: /night', ALOHA_DOMAIN),
            ),
            array(
                'id'    => "th_room_location",
                'label'  =>  'Location',
                'type'  => 'text',
                'desc' => __('e.g.: 2 km away from the center', ALOHA_DOMAIN),
            ),
            array(
                'id'    => "th_room_location_link",
                'label'  =>  'Location Link',
                'type'  => 'text',
                'desc' => __('e.g.: A google maps link', ALOHA_DOMAIN),
            ),
            array(
                'id'          => 'th_room_rating',
                'label'       => __( 'Rating', ALOHA_DOMAIN ),
                'desc'        => __( 'e.g: 4.5', ALOHA_DOMAIN ),
                'type'        => 'numeric-slider',
                'min_max_step'=> '0,5,0.5',
            ),
            array(
                'id' => 'th_room_button_text',
                'label' => 'Button Text',
                'type' => 'text',
                'desc' => __('Displayed below the intro.', ALOHA_DOMAIN),
            ),
            array(
                'id'          => "th_room_thumb",
                'label'       => __( 'Alternative Grid Image', ALOHA_DOMAIN),
                'type'        => 'upload',
                'class'       => 'ot-upload-attachment-id',
                'desc' => 'Helpful when using the "Image Format". The theme will use the Alternative Image for the room grid and the Featured Image for the lightbox.',
            ),
            array(
                'id'          => 'th_gallery',
                'label'       => __( 'Gallery', ALOHA_DOMAIN),
                'desc'        => __( 'This will replace the featured image', ALOHA_DOMAIN),
                'type'        => 'gallery',
            ),
            array(
                'id'          => 'th_room_icons_ordering',
                'label'       => __( 'Icons Ordering', ALOHA_DOMAIN),
                'type'        => 'text',
                'class'       => 'icon-hidden',
            ),
            array(
                'id'          => 'th_room_icons',
                'label'       => __( 'Icons', ALOHA_DOMAIN),
                'desc'        => 'Add icons for the listings',
                'type'        => 'th_room_icons',
           ),
            // END PAGE LAYOUT META BOX
        )
    ));
}

// Enable post formats for Motopress Room Custom Post Types.
add_post_type_support( 'mphb_room_type', 'post-formats' );

// Enabel image and link.
function mphb_get_allowed_project_formats() {
    return array( 'image','link' );
}

// Post format filters.
add_action( 'load-post.php',     'mphb_post_format_support_filter' );
add_action( 'load-post-new.php', 'mphb_post_format_support_filter' );
add_action( 'load-edit.php',     'mphb_post_format_support_filter' );

function mphb_post_format_support_filter() {

    $screen = get_current_screen();

    // Bail if not on the projects screen.
    if ( empty( $screen->post_type ) ||  $screen->post_type !== 'mphb_room_type' ) {
        return;
    }

    // Check if the current theme supports formats.
    if ( current_theme_supports( 'post-formats' ) ) {

        $formats = get_theme_support( 'post-formats' );

        // If we have formats, add theme support for only the allowed formats.
        if ( isset( $formats[0] ) ) {


            $new_formats = array_intersect( $formats[0], mphb_get_allowed_project_formats() );

            // Remove post formats support.
            remove_theme_support( 'post-formats' );

            // If the theme supports the allowed formats, add support for them.
            if ( $new_formats )
                add_theme_support( 'post-formats', $new_formats );
        }
    }

    // Filter the default post format for the MPHB room type.
    add_filter( 'option_default_post_format', 'mphb_default_post_format_filter', 95 );
}

function mphb_default_post_format_filter( $format ) {

    return in_array( $format, mphb_get_allowed_project_formats() ) ? $format : 'standard';
}

// Chagne the sort order of the format meta box for the MPHB room type.

add_filter( 'get_user_option_meta-box-order_mphb_room_type', 'wpse25793_one_column_for_all' );
function wpse25793_one_column_for_all( $order )
{
    return array(
        'side'   => join( ",", array(
            'submitdiv',
            'formatdiv',

        ) ),
    );
}
