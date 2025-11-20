<?php
$video_embed = sanitize_text_field(get_post_meta( get_the_ID(), '_format_video_embed', true));
$video_shortcode = sanitize_text_field(get_post_meta( get_the_ID(), '_format_video_shortcode', true));


$video_container_class = "";
if ($video_embed > ""){
	$embed_code = wp_oembed_get($video_embed);
	$video_container_class = "video-container";
}elseif($video_shortcode > "" && strpos($video_shortcode, '[embed]') !== FALSE){
	global $wp_embed;
	$embed_code = $wp_embed->run_shortcode($video_shortcode);
	$embed_code = do_shortcode($embed_code);
	$video_container_class = "video-container";
}
elseif($video_shortcode > ""){
	$embed_code = do_shortcode($video_shortcode);
	$video_container_class = "wp-hosted-video";
}
?>

<?php
if (isset($embed_code) && $embed_code > ""){ ?>
    <div class="<?php echo wp_kses_post($video_container_class); ?>">
        <?php echo $embed_code // sanitize_text_field() just above. ;?>
    </div>
<?php } ?>
<div class="post-inner">
	<?php
	if (!is_single()){ ?>
    <<?php echo esc_attr($settings['title_size']); ?> class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo esc_attr($settings['title_size']); ?>>
    <?php }?>
    <?php include('entry-meta-masonry.php'); ?>
     <?php
	if (is_single() || (!is_single() && !$automatic_post_excerpts)){
			$content = apply_filters( 'the_content', get_the_content() );
			$content = str_replace( ']]>', ']]&gt;', $content );
			if($content != ""){ ?>
            	<div class="entry-content">
					<?php echo $content ; // get_the_content() already sanitized. used just above. ?>
                </div>
			<?php }
	}else{
		$excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );
		$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
			if($excerpt != ""){ ?>
            	<div class="entry-content post-excerpt">
					<?php echo wp_kses_post( $excerpt ); ?>
                </div>
			<?php }
    } ?>
	<?php include('entry-meta-footer-masonry.php'); ?>
</div>
