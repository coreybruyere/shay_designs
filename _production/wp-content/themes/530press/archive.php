<?php 

/**
 * Custom Post Archive Template
 */

?>


<!-- <?php get_template_part('templates/page', 'header'); ?> -->

<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'roots'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<section class="post">
<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/content', get_post_format()); ?>
<?php endwhile; ?>
</section>

<?php if ($wp_query->max_num_pages > 1) : ?>
  <?php
    if ( function_exists('wp_bootstrap_pagination') )
      wp_bootstrap_pagination();
  ?>
<?php endif; ?>
