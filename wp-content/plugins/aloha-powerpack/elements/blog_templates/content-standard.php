<?php
if (has_post_thumbnail()) {
    $featured_img_attr = array('class' => "img-responsive",);
    ?>
    <?php if (is_single()) { ?>
        <?php the_post_thumbnail($image_size, $featured_img_attr); ?>
    <?php } else { ?>
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail($image_size, $featured_img_attr); ?>
        </a>
    <?php } ?>
<?php } ?>

<div class="post-inner">
    <<?php echo esc_attr($settings['title_size']); ?> class="post-title"><a href="<?php the_permalink(); ?>"><?php echo wp_kses_post(get_the_title()); ?></a></<?php echo esc_attr($settings['title_size']); ?>>

    <?php include('entry-meta-masonry.php'); ?>


    <?php
    //$excerpt = apply_filters('the_excerpt', get_the_excerpt());
    //$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
    $excerpt = get_the_excerpt();
    if ($excerpt != "") {
        ?>
        <div class="entry-content post-excerpt">
            <p>
                <?php echo wp_kses_post($excerpt); ?>
            </p>
        </div>
        <?php
    }
    ?>
    <?php include('entry-meta-footer-masonry.php'); ?>
</div>
