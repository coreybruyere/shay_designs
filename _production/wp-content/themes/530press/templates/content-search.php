<article <?php post_class('post__article'); ?>>
  <div class="flag--top">
    <div class="flag__image">
      <?php $post_type = get_post_type(); ?>
      <?php $post_img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array(100,100, true) ); ?>

      <?php if($post_img['0']): ?>
      <img src="<?php echo $post_img['0']; ?>" class="img" alt="<?php the_title(); ?>" width="100" height="100">
      <?php endif; ?> 
    </div><!-- end flag_image -->
    <div class="flag__body">
      <header>
        <h2 class="entry-title no-margin--t"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php get_template_part('templates/entry-meta'); ?>
      </header>
      <div class="entry-summary">
        <?php the_excerpt(); ?>
      </div>
    </div><!-- end flag_body -->
  </div><!-- end flag -->
</article><!-- end post_article -->
