<?php get_template_part('templates/head'); ?>

<body <?php body_class(); ?> id="body">
  <?php include_once("lib/images/svg/svg-defs.svg"); ?>

  <!--[if lt IE 8]>
    <div class="alert alert-warning">
      <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'roots'); ?>
    </div>
  <![endif]-->

  <?php
    do_action('get_header');
    get_template_part('templates/header');
  ?>
  <?php if( is_front_page() ): ?>
  <div role="document">
  <?php else: ?>
  <div class="wrapper site" role="document">
  <?php endif; ?>
    <main class="main" role="main" itemprop="mainContentOfPage">
     <!-- <?php echo basename( get_page_template() ) ; ?>  -->

    <?php if( !is_product() && is_single() || is_author() || is_home() ): ?>
      <div class="post">
        <?php include roots_template_path(); ?>
      </div>
    <?php else: ?>
      <?php include roots_template_path(); ?>
    <?php endif; ?>
    <?php if (roots_display_sidebar()) : ?>
      <aside class="sidebar" role="complementary">
        <div class="sidebar__inner">
          <?php include roots_sidebar_path(); ?>
        </div>
      </aside><!-- /.sidebar -->
    <?php endif; ?>
    </main><!-- /.main -->
  </div><!-- /.wrap -->

  <div class="to-top" id="js-to-top"></div>


  <?php get_template_part('templates/footer'); ?>

  <?php wp_footer(); ?>

</body>
</html>
