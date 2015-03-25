<!-- Skip to Content Link -->
<a href="#content" class="skip-to-main" id="js-skip-to">Skip to content</a>

<div class="header--menu" id="js-header-menu" role="menubar"> 

  <?php get_template_part('templates/searchform'); ?> 

  <div class="nav--menu" role="menu">

    <?php if ( is_user_logged_in() ) : ?>
      <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>" class="nav--menu__link" role="menuitem">
    <?php else: ?>
      <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Register','woothemes'); ?>" class="nav--menu__link" role="menuitem">
    <?php endif; ?>
      <span class="nav--menu__icon">
        <svg viewBox="0 0 32 32">
          <g filter="">
            <use xlink:href="#user-3"></use>
          </g>
        </svg>
      </span> <!-- icon-svg -->
    <?php if ( is_user_logged_in() ) : ?>
      <span><?php _e('My Account','woothemes'); ?></span>
    <?php else: ?>
      <span><?php _e('Login','woothemes'); ?></span>
    <?php endif; ?>
      </a>
      
    <?php 
    global $woocommerce;
    $cart_contents_count = $woocommerce->cart->cart_contents_count;
    if ( $cart_contents_count > 0 ) {
      $cart_number = $cart_contents_count;
    }
    // echo $cart_number;
    ?>

    <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('Cart','woothemes'); ?>" class="nav--menu__link  js-cart" role="menuitem" data-cart="<?php echo sprintf(_n('%d', '%d', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?>">
      <span class="nav--menu__icon">
        <svg viewBox="0 0 32 32">
          <g filter="">
            <use xlink:href="#trolley"></use>
          </g>
        </svg>
      </span> <!-- icon-svg -->
      <span>Cart</span>
    </a><!-- end role[menuitem] -->

  </div><!-- end role[menu] -->

</div><!-- end role[menubar] -->



<header class="header  bg" role="banner" itemscope itemtype="http://schema.org/WPHeader">

  <div class="wrapper--bare flex-center">

    <div class="header__toggle" id="js-nav-toggle" role="button" aria-pressed="false">
      <span>menu</span>
    </div> <!-- end header__burger -->

    <div class="branding" itemscope itemtype="http://schema.org/Organization">
      <a href="<?php echo esc_url(home_url('/')); ?>" itemprop="url">
        <img src="<?php echo get_bloginfo('template_directory');?>/lib/images/svg/header-logo--color.svg" onerror="this.src=<?php echo get_bloginfo('template_directory');?>/lib/images/min/main-logo--white.png" alt="<?php bloginfo('name'); ?>" class="no-select branding__img">
      </a>
    </div><!-- end branding --> 

    <nav class="nav js-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">

      <!-- <ul class="list-ui" role="menu"> -->

        <?php
          if (has_nav_menu('primary_navigation')) : 
            wp_nav_menu(array(
              'theme_location' => 'primary_navigation',
              'menu_class' => 'list-ui',
              'container' => false,                           // class of container 
              'menu' => __( 'Header Menu' ),                  // nav name
              'depth' => 0,                                   // limit the depth of the nav
              'items_wrap'=> '<ul class="list-ui" role="menu">%3$s</ul>',
              'walker' => new Roots_Nav_Walker                  // build walker 
            ));
          endif;
        ?>

    </nav><!-- end nav -->

    <nav class="nav  nav--secondary js-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">    

      <!-- <ul class="list-ui" role="menu"> -->

        <?php
          if (has_nav_menu('secondary_navigation')) : 
            wp_nav_menu(array(
              'theme_location' => 'secondary_navigation',
              'menu_class' => 'list-ui',
              'container' => false,                           // class of container 
              'menu' => __( 'Header Menu' ),                  // nav name
              'depth' => 0,                                   // limit the depth of the nav
              'items_wrap'=> '<ul class="list-ui" role="menu">%3$s</ul>',
              'walker' => new Roots_Nav_Walker                  // build walker 
            ));
          endif;
        ?>

    </nav><!-- end nav -->

  </div><!--end wrapper-bare -->

</header><!-- end header[banner] -->  
