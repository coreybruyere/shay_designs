<?php
/*
Template Name: Sidenav Template
*/
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <div class="post">
  	<?php get_template_part('templates/content', 'page'); ?>
  </div>
<?php endwhile; ?>
