<?php
/**
 * Block Library templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script type="text/template" id="template-thmv-templateLibrary-header-logo">
	<h3><?php _e( 'Block Library', ALOHA_DOMAIN ); ?></h3>
</script>

<script type="text/template" id="template-thmv-templateLibrary-header-back">
	<i class="eicon-" aria-hidden="true"></i>
	<span><?php echo __( 'Back to Library', ALOHA_DOMAIN ); ?></span>
</script>


<script type="text/template" id="template-thmv-templateLibrary-loading">
	<div class="elementor-loader-wrapper">
		<div class="elementor-loading-title"><?php esc_html_e( 'Loading', ALOHA_DOMAIN ); ?></div>
	</div>
</script>
<script type="text/template" id="template-thmv-templateLibrary-empty">
	
	<div class="elementor-template-library-blank-title"><?php esc_html_e( 'You must be registered to use this feature', ALOHA_DOMAIN ); ?></div>
        <div class="elementor-template-library-blank-message">Visit the <a target="_blank" href="<?php echo admin_url('?page='.ALOHA_MENU_SLUG)?>">dashboard</a> to register.</div>
</script>
