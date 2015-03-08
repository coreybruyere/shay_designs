<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class('post__article'); ?>>
    <header>
      <?php get_template_part('templates/entry-time'); ?>
      <h1 class="entry-title no-margin--t"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content  section">
      <?php the_content(); ?>
    </div>
    <footer class="section-region">

      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?> 
