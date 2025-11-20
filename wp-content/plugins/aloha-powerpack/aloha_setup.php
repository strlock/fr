<?php

define('THEMO_VERSION', '2.1.8');
define('THEMO__FILE__', ALOHA__FILE);
define('THEMO_PLUGIN_BASE', plugin_basename(THEMO__FILE__));
define('THEMO_URL', plugins_url('/', THEMO__FILE__));
define('THEMO_PATH', plugin_dir_path(THEMO__FILE__));
define('THEMO_ASSETS_PATH', THEMO_PATH . 'assets/');
define('THEMO_ASSETS_URL', THEMO_URL . 'assets/');
define('THEMO_COLOR_PRIMARY', '#1F231C');
define('THEMO_COLOR_ACCENT', '#151515');
define('ENABLE_BLOCK_LIBRARY', true);

/**
 * Define Elementor Partner ID
 */
if (!defined('ELEMENTOR_PARTNER_ID')) {
    define('ELEMENTOR_PARTNER_ID', 2129);
}

$th_theme = wp_get_theme(); // get theme info and save theme name as constant.
if ($th_theme->get('Name') > "") {
    $th_theme_name_arr = explode("-", $th_theme->get('Name'), 2); // clean up child theme name
    $th_theme_name_arr2 = explode(" ", trim($th_theme_name_arr[0]), 2); // clean up child theme name
    $th_theme_name = trim(strtolower($th_theme_name_arr2[0]));
    define("THEMO_CURRENT_THEME", $th_theme_name);
}

if (defined('ELEMENTOR_PATH')) {
// Run Setup
    require_once THEMO_PATH . 'inc/setup.php';
}

// Making the plugin translation ready
if (!function_exists('th_translation_ready')) :

    function th_translation_ready() {
        $locale = apply_filters('plugin_locale', get_locale(), ALOHA_DOMAIN);
        load_textdomain("aloha-powerpack", WP_LANG_DIR . '/aloha-powerpack/' . 'aloha-powerpack' . '-' . $locale . '.mo');
        load_plugin_textdomain('aloha-powerpack', FALSE, basename(dirname(__FILE__)) . '/languages/');
    }

endif;

// Enable white label for HFE and deactivate analytics tracking.
function thmv_set_white_label_opt() {
    $thmv_white_label_opt = array("option" => true);
    return $thmv_white_label_opt;
}

add_filter('bsf_white_label_options', 'thmv_set_white_label_opt');


/*
 * Add the duplicate link to action list for post_row_actions
 */
function th_duplicate_post_link( $actions, $post ) {
	if (current_user_can('edit_posts')) {
		$actions['duplicate'] = '<a href="admin.php?action=rd_duplicate_post_as_draft&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
	}
	return $actions;
}
/*
 * Function creates post duplicate as a draft and redirects then to the edit post screen
 */
function th_duplicate_post_as_draft(){
    
        if(!current_user_can('edit_posts')){
            wp_die('No access');
        }
        
	global $wpdb;
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
		wp_die('No post to duplicate has been supplied!');
	}

	/*
	 * get the original post id
	 */
	$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
	/*
	 * and all the original post data then
	 */
	$post = get_post( $post_id );

	/*
	 * if you don't want current user to be the new post author,
	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	 */
	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;

	/*
	 * if post data exists, create the post duplicate
	 */
	if (isset( $post ) && $post != null) {

		/*
		 * new post data array
		 */
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);

		/*
		 * insert the post by wp_insert_post() function
		 */
		$new_post_id = wp_insert_post( $args );

		/*
		 * get all current post terms ad set them to the new post draft
		 */
		$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}

		/*
		 * duplicate all post meta just in two SQL queries
		 */
		$post_meta_infos = $wpdb->get_results($wpdb->prepare(
		                        "SELECT meta_key, meta_value 
                                FROM $wpdb->postmeta
                                WHERE post_id=%d",
                                $post_id));
		if (count($post_meta_infos)!=0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes($meta_info->meta_value);
                $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			//%1$d,%2$s,%3$s
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query);
		}

                //remove elementor meta field so it is regenerates the styles
                delete_post_meta($new_post_id, '_elementor_css');
		/*
		 * finally, redirect to the edit post screen for the new draft
		 */
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	} else {
		wp_die('Post creation failed, could not find original post: ' . $post_id);
	}
}
add_action( 'admin_action_rd_duplicate_post_as_draft', 'th_duplicate_post_as_draft' );

/**
 * Detect duplicate post plugin. Don't add our won duplicate option if plugin is installed and active.
 */

if( function_exists( 'duplicate_post_plugin_actions' ) ) {
	//plugin is activated
}else{
	add_filter( 'post_row_actions', 'th_duplicate_post_link', 10, 2 );
	add_filter('page_row_actions', 'th_duplicate_post_link', 10, 2);
}

function aloha_debug_mode(){
    return isset($_GET['aloha_debug']);
}