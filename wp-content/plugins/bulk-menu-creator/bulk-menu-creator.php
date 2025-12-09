<?php
/*
	Plugin Name:	Bulk menu creator
	Plugin URI:		https://wp-speedup.eu/shop/wordpress-plugins/pro-plugins/bulk-menu-creator-pro/
	Description:	Create multiple menu items at once
	Version:		9.6
	Author:			KubiQ
	Author URI:		https://kubiq.sk
	Text Domain:	bulk_menu
	Domain Path:	/languages
*/

class bulk_menu{
	public function __construct(){
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'filter_plugin_actions' ), 10, 2 );

		add_action( 'wp_ajax_bulk_menu_notice_dismissed', array( $this, 'bulk_menu_notice_dismissed' ) );
		add_action( 'admin_notices', function(){
			if( ! get_option( 'bulk_menu_notice_dismissed', false ) ){
				if( function_exists('get_current_screen') && isset( get_current_screen()->id ) && in_array( get_current_screen()->id, array( 'plugins', 'plugin-install', 'nav-menus' ) ) ){ ?>
					<div class="bulk_menu-notice notice notice-success is-dismissible">
						<p><?php printf( esc_html__( 'Thank you for using Bulk menu creator plugin! Please, %scheck out our PRO version%s, it might interest you and make you more efficient.', 'bulk_menu' ), '<a href="https://wp-speedup.eu/shop/wordpress-plugins/pro-plugins/bulk-menu-creator-pro/?self-promo" target="_blank">', '</a>' ) ?></p>
					</div>
					<script>
					jQuery(document).ready(function($){
						$(document).on('click', '.bulk_menu-notice .notice-dismiss', function(){
							$.post( ajaxurl, { action: 'bulk_menu_notice_dismissed', _wpnonce: '<?php echo wp_create_nonce('bulk_menu_notice_dismissed') ?>' });
						});
					});
					</script><?php
				}
			}
		});
	}

	function bulk_menu_notice_dismissed(){
		if( defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('bulk_menu_notice_dismissed') ){
			add_option( 'bulk_menu_notice_dismissed', 1 );
		}
	}

	function filter_plugin_actions( $links, $file ){
		array_unshift( $links, '<a href="https://wp-speedup.eu/shop/wordpress-plugins/pro-plugins/bulk-menu-creator-pro/" target="_blank" style="font-weight:bold;color:#f00">' . __( 'PRO version', 'bulk_menu' ) . '</a>' );
		return $links;
	}

	function activate(){
		global $wpdb;
		$hiddens = $wpdb->get_results("SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'metaboxhidden_nav-menus'");
		foreach( $hiddens as $hidden ){
			if( strpos( $hidden->meta_value, "bulk_menu_creator" ) !== false ){
				$hidden_menus = maybe_unserialize( $hidden->meta_value );
				$key = array_search( "bulk_menu_creator", $hidden_menus );
				unset( $hidden_menus[ $key ] );
				update_user_option( $hidden->user_id, "metaboxhidden_nav-menus", $hidden_menus, true );
			}
		}
	}

	function plugins_loaded(){
		load_plugin_textdomain( 'bulk_menu', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	public function admin_init(){
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_meta_box( 'bulk_menu_creator', __( 'Bulk menu', 'bulk_menu' ), array( $this, 'bulk_menu_box' ), 'nav-menus', 'side', 'high' );
	}

	public function bulk_menu_box(){
		global $_nav_menu_placeholder;
		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1; ?>
		<div id="bulk_menu_fields" class="posttypediv">
			<label>
				<?php _e( 'Menu items labels', 'bulk_menu' ) ?><br>
				<textarea wrap="off" id="bulk-menu-labels" class="numbered"></textarea>
			</label>
			<label>
				<?php _e( 'Menu items URLs', 'bulk_menu' ) ?><br>
				<textarea wrap="off" id="bulk-menu-urls" class="numbered"></textarea>
			</label>
			<button type="button" class="button-secondary" id="process_bulk_menu_fields"><?php _e( 'Generate menu items', 'bulk_menu' ) ?></button>
			<span class="spinner"></span>
			<div style="display:none">
				<div class="tabs-panel tabs-panel-active">
					<ul class="categorychecklist form-no-clear">
						<li>
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-object-id]" value="-1" checked="checked">
							<input type="text" class="menu-item-type" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-type]" value="custom">
							<input type="text" class="menu-item-title" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-title]" value="TEST">
							<input type="text" class="menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder ?>][menu-item-url]" value="#test">
						</li>
					</ul>
				</div>
				<p class="button-controls wp-clearfix">
					<span class="add-to-menu">
						<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php _e( 'Generate menu items', 'bulk_menu' ) ?>" name="add-post-type-menu-item" id="submit-bulk_menu_fields">
						<span class="spinner"></span>
					</span>
				</p>
			</div>
		</div>
		<style>
			#bulk_menu_creator .accordion-section-content{
				overflow: visible;
			}
			#bulk_menu_creator .inside{
				margin: 0;
			}
			#bulk_menu_creator label{
				display: block;
			}
			#bulk_menu_creator textarea{
				position: relative;
				z-index: 20;
				resize: both;
			}
			textarea.numbered{
				width: 100%;
				min-height: 75px;
				margin-bottom: 10px;
				padding: 5px 10px 5px 34px;
				font-family: Consolas, monaco, monospace;
				font-size: 12px;
				line-height: 1.35;
				background: #fff url(<?php echo plugins_url( '/assets/lines.png', __FILE__ ) ?>) 0 -5px no-repeat;
				background-attachment: local;
			}
		</style><?php
	}

	public function admin_enqueue_scripts(){
		if( get_current_screen()->base != 'nav-menus' ) return;
		wp_enqueue_script( 'bulk_menu_js', plugins_url( '/js/nav-menu.js', __FILE__ ), array('jquery'), 1 );

		wp_enqueue_script( 'bulk_menu_quick_copy', plugins_url( '/js/quick-copy.js', __FILE__ ), array('jquery'), 1, 1 );
		wp_enqueue_script( 'bulk_menu_quick_delete', plugins_url( '/js/quick-delete.js', __FILE__ ), array('jquery'), 1, 1 );
		echo '<style>#menu-to-edit :is(.quick-delete,.quick-copy){position:relative;display:inline-block;vertical-align:text-bottom;color:#a00;opacity:0}#menu-to-edit .menu-item-handle:hover :is(.quick-delete,.quick-copy){opacity:1}#menu-to-edit :is(.quick-delete,.quick-copy):before{content:"\f182";font:normal 20px/1 dashicons;speak:never;display:block;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;text-decoration:none}#menu-to-edit .quick-copy:before{content:"\f105";color:#2196f3;margin-right:10px}#menu-to-edit .quick-copy .spinner{position:absolute;top:-4px;left:-30px}</style>';

		wp_localize_script( 'bulk_menu_js', 'emi_data', array(
			'bulk_copy_button' => __( 'Copy', 'bulk_menu' ),
			'bulk_delete' => __( 'Do you also want to delete all subitems?', 'bulk_menu' ),
			'bulk_delete_button' => __( 'Delete', 'bulk_menu' ),
		));
	}
}

$bulk_menu_var = new bulk_menu();
register_activation_hook( __FILE__, array( $bulk_menu_var, 'activate' ) );