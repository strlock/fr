<?php
aloha_load_wp_options();
use IgniteKit\WP\OptionBuilder\Framework;

add_action('admin_init', function(){
    aloha_ot_meta_box_post_format_quote();
    aloha_ot_meta_box_post_format_audio();
    aloha_ot_meta_box_post_format_link();
    aloha_ot_meta_box_post_format_video();
    aloha_ot_meta_box_post_format_gallery();
    aloha_general_meta_boxes();
});
//======================================================================
// Metabox Plugin Functions
//======================================================================
// Remove BR tag from checkbox list output.
//add_filter('rwmb_themo_meta_box_builder_meta_boxes_html','themo_test');
//function themo_test($html){
//	return strip_tags($html,'<label><input>');
//}

//======================================================================
// 400 - Option Tree Functions, Hooks, Filters
//======================================================================

//-----------------------------------------------------
// ot_override_forced_textarea_simple - filter
// Allows TinyMCE or Textarea metaboxes
//-----------------------------------------------------
//add_filter( 'ot_override_forced_textarea_simple', '__return_true' );

//-----------------------------------------------------
// aloha_ot_meta_box_post_format_quote - filter
// Slight Changes to the quote meta box
//-----------------------------------------------------

function aloha_ot_meta_box_post_format_quote() {
    $pages = ['post'];
    $fields = array(
            array(
                    'id'      => '_format_quote_copy',
                    'label'   => '',
                    'desc'    => esc_html__( 'Quote', 'option-tree' ),
                    'std'     => '',
                    'type'        => 'textarea_simple',
                    'rows'        => '4',
            ),
            array(
                    'id'      => '_format_quote_source_name',
                    'label'   => '',
                    'desc'    => esc_html__( 'Source Name (ex. author, singer, actor)', ALOHA_DOMAIN ),
                    'std'     => '',
                    'type'    => 'text'
            ),
            array(
                    'id'      => '_format_quote_source_title',
                    'label'   => '',
                    'desc'    => esc_html__( 'Source Title (ex. book, song, movie)', ALOHA_DOMAIN ),
                    'std'     => '',
                    'type'    => 'text'
            ));
	
    $framework = new Framework();
    $framework->register_metabox( array(
        'id'       => 'aloha_quote',
	'title'    => __( 'Quote', ALOHA_DOMAIN ),
	'desc'     => '',
	'pages'    => $pages,
	'context'  => 'normal',
	'priority' => 'high',
	'fields'   => $fields
    ));
}

//-----------------------------------------------------
// aloha_ot_meta_box_post_format_audio - filter
// Slight Changes to the audio meta box
//-----------------------------------------------------

function aloha_ot_meta_box_post_format_audio() {
        $pages = ['post'];
	//$pages[] = 'themo_tour';
	//$array['pages'] = $pages;

	$fields = array(
		array(
			'id'      => '_format_audio_shortcode',
			'label'   => 'Upload and Embed Audio to your website',
			'desc'    => esc_html__( 'Use the built-in <code>[audio]</code> shortcode here.', ALOHA_DOMAIN ),
			'std'     => '',
			'type'    => 'textarea_simple'
		)
	);
	$framework = new Framework();
        $framework->register_metabox( array(
            'id'       => 'aloha_audio',
            'title'    => __( 'Audio', ALOHA_DOMAIN ),
            'desc'     => '',
            'pages'    => $pages,
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => $fields
        ));
}

//-----------------------------------------------------
// aloha_ot_meta_box_post_format_link - filter
// Slight Changes to the audio meta box
//-----------------------------------------------------

function aloha_ot_meta_box_post_format_link() {
        $pages = ['post'];
	$pages[] = 'themo_room';
	$pages[] = 'mphb_room_type';
	

	$fields = array(

		array(
			'id'      => '_format_link_url',
			'label'   => '',
			'desc'    => esc_html__( 'Link URL (ex. https://google.com)', ALOHA_DOMAIN ),
			'std'     => '',
			'type'    => 'text'
		),
		array(
			'id'      => '_format_link_title',
			'label'   => '',
			'desc'    => esc_html__( 'Link Title (ex. Check out Google)', ALOHA_DOMAIN ),
			'std'     => '',
			'type'    => 'text'
		),

		array(
			'id'          => '_format_link_target',
			'label'       => esc_html__( 'Link Target', ALOHA_DOMAIN ),
			'type'        => 'checkbox',
			'choices'     => array(
				array(
					'value'       => '_blank',
					'label'       => 'Open link in a new window / tab',
				)
			)
		),
	);
	$framework = new Framework();
        $framework->register_metabox( array(
            'id'       => 'aloha_link',
            'title'    => __( 'Link', ALOHA_DOMAIN ),
            'desc'     => '',
            'pages'    => $pages,
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => $fields
        ));
}

//-----------------------------------------------------
// aloha_ot_meta_box_post_format_video - filter
// Slight Changes to the video meta box
//-----------------------------------------------------

function aloha_ot_meta_box_post_format_video() {
        $pages = ['post'];
	//$pages[] = 'themo_tour';
	//$array['pages'] = $pages;

	$fields = array(
		array(
			'id'      => '_format_video_embed',
			'label'   => 'Insert from URL (Vimeo and Youtube)',
			'desc'    => sprintf( wp_kses_post( __( '(ex. http://vimeo.com/link-to-video). You can find a list of supported oEmbed sites in the %1$s.', ALOHA_DOMAIN )), '<a href="http://codex.wordpress.org/Embeds" target="_blank">' . esc_html__( 'Wordpress Codex', ALOHA_DOMAIN ) .'</a>' ),
			'std'     => '',
			'type'    => 'text'
		),
		array(
			'id'      => '_format_video_shortcode',
			'label'   => 'Upload your own self hosted video',
			'desc'    => wp_kses_post(__( 'Use the built-in <code>[video]</code> shortcode here.', ALOHA_DOMAIN )),
			'std'     => '',
			'type'    => 'textarea'
		)
	);
	$framework = new Framework();
        $framework->register_metabox( array(
            'id'       => 'aloha_video',
            'title'    => __( 'Video', ALOHA_DOMAIN ),
            'desc'     => '',
            'pages'    => $pages,
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => $fields
        ));
}

//-----------------------------------------------------
// aloha_ot_meta_box_post_format_gallery - filter
// Enable Post Format gallery to on custom post type
//-----------------------------------------------------

function aloha_ot_meta_box_post_format_gallery() {

	$pages = ['post'];
	//$pages[] = 'themo_tour';
	//$array['pages'] = $pages;

	$fields = array(
		array(
			'id'      => '_format_gallery',
			'type'    => 'gallery',
                        'std'   => 'off',
		),
		
	);
	$framework = new Framework();
        $framework->register_metabox( array(
            'id'       => 'aloha_gallery',
            'title'    => __( 'Gallery', ALOHA_DOMAIN ),
            'desc'     => '',
            'pages'    => $pages,
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => $fields
        ));
}

//-----------------------------------------------------
// themo_ot_post_formats - filter
// Enable Post Format Types via OT
//-----------------------------------------------------
//add_filter( 'ot_post_formats', 'themo_ot_post_formats');
//
//function themo_ot_post_formats( ) {
//	return true;
//}


//-----------------------------------------------------
// FILTER for modifying field id passed in from OT.
// Need to make a wildcard match on the field ids.
//-----------------------------------------------------
//add_filter( 'ot_field_ID_match', 'themo_filter_field_ID_match', 10, 1 );
//
//function themo_filter_field_ID_match( $content) {
//	return trim(str_replace(range(0,9),'',$content)); // Strip out numbers and pass it back.
//}
//======================================================================
// General Meta Boxes
//======================================================================

function aloha_general_meta_boxes()
{

//-----------------------------------------------------
// Blog Category Filter
//-----------------------------------------------------
    $framework = new Framework();
    $framework->register_metabox( array(
        'id' => 'themo_blog_category_meta_box',
        'title' => __('Category Filter', ALOHA_DOMAIN),
        'pages' => array('page'),
        'context' => 'normal',
        'priority' => 'default',
        'fields' => array(
            // START PAGE LAYOUT META BOX
            array(
                'id' => 'themo_category_checkbox',
                'std' => '',
                'type' => 'category-checkbox',
            ),
            // END PAGE LAYOUT META BOX
        )
    ));
    

//-----------------------------------------------------
// Page Layout, Sidebar, Content Editor Sort Order
//-----------------------------------------------------
    $framework->register_metabox( array(
        'id' => 'themo_page_layout_meta_box',
        'title' => __('Page Layout', ALOHA_DOMAIN),
        'pages' => array('page','themo_room', 'block_template'),
        'context' => 'side',
        'priority' => 'default',
        'fields' => array(
            // START PAGE LAYOUT META BOX
            array(
                'id' => 'themo_transparent_header',
                'label' => 'Transparent Header',
                'std' => 'off',
                'type' => 'on-off',
            ),
            array(
                'id' => 'themo_hide_title',
                'label' => 'Hide Page Title',
                'std' => 'off',
                'type' => 'on-off',
            ),
            array(
                'id' => 'themo_page_layout',
                'label' => 'Sidebar',
                'std' => 'full',
                'type' => 'radio',
                'section' => 'themo_home_page_meta',
                'choices' => array(
                    array(
                        'value' => 'left',
                        'label' => __('Left Sidebar', ALOHA_DOMAIN),

                    ),
                    array(
                        'value' => 'right',
                        'label' => __('Right Sidebar', ALOHA_DOMAIN),

                    ),
                    array(
                        'value' => 'full',
                        'label' => __('No Sidebar', ALOHA_DOMAIN),

                    )
                )
            ),

            // END PAGE LAYOUT META BOX
        )
    ));
    

    //-----------------------------------------------------
    // Page Layout, Sidebar, Content Editor Sort Order
    //-----------------------------------------------------
    $themo_holes_meta_box = array(
        'id' => 'themo_holes_meta_box',
        'title' => __('Hole Page Options', ALOHA_DOMAIN),
        'pages' => array('themo_portfolio'),
        'context' => 'normal',
        'priority' => 'default',
        'fields' => array(
            // START PAGE LAYOUT META BOX

            array(
                'id' => '_holes_number',
                'label' => 'Hole #',
                'type' => 'text',
            ),
            array(
                'id' => '_holes_par',
                'label' => 'Par',
                'type' => 'text',
            ),
            array(
                'id' => '_holes_yards',
                'label' => 'Yards',
                'type' => 'text',
            ),
            array(
                'id' => '_holes_handicap',
                'label' => 'Handicap',
                'type' => 'text',
            ),
            array(
                'id'          => "_holes_image",
                'label'       => __( 'Thumbnail Image', ALOHA_DOMAIN),
                'type'        => 'upload',
                'class'       => 'ot-upload-attachment-id',
                'desc' => 'Sets the thumbnail image for Image post format only.',
            ),
            // END PAGE LAYOUT META BOX
        )
    );
    //ot_register_meta_box($themo_holes_meta_box);

}

