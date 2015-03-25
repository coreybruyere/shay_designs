
<?php if (!have_posts()) : ?>
  <div class="section-region">
    <div class="alert alert-warning">
      <?php _e('Sorry, no results were found.', 'roots'); ?>
    </div>
    <h4><?php _e('Try searching again..', 'roots'); ?></h4>
    <?php get_search_form(); ?>
  </div>
<?php endif; ?>

<?php if (have_posts()) : ?>
<div class="section-region">
  <h4><?php _e('Showing results for:', 'roots'); ?> <mark><?php the_search_query(); ?></mark></h4>
</div>
<?php endif; ?> 

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/content-search', get_post_format()); ?>
<?php endwhile; ?>

<?php if ($wp_query->max_num_pages > 1) : ?>
  <?php
    if ( function_exists('wp_bootstrap_pagination') )
      wp_bootstrap_pagination();
  ?>
<?php endif; ?>
