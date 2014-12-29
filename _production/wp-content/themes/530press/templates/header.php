
<div class="header--menu" role="menubar">

  <div role="menu">

    <?php if(get_field('facebook', 'option')): ?>

      <?php $fb = get_field('facebook', 'option'); ?>

      <?php 
        if((substr_compare($fb,"http://",0,7)) === 0) $fb = $fb;
        else $fb = "http://$fb";
      ?>
      <a href="<?php echo $fb; ?>" class="icon-svg" role="menuitem" target="_blank">
        <svg viewBox="0 0 32 32" role="img" aria-labelledby="fb-title-head">
          <title id="fb-title-head">Facebook Icon</title>
          <g filter="">
            <use xlink:href="#facebook"></use>
          </g>
        </svg>
      </a> <!-- icon-svg -->
    <?php endif; ?>


    <?php if(get_field('pinterest', 'option')): ?>

      <?php $pin = get_field('pinterest', 'option'); ?>

      <?php 
        if((substr_compare($pin,"http://",0,7)) === 0) $pin = $pin;
        else $pin = "http://$pin";
      ?>
      <a href="<?php echo $pin; ?>" class="icon-svg" role="menuitem" target="_blank">
        <svg viewBox="0 0 32 32" role="img" aria-labelledby="pin-title-head">
          <title id="pin-title-head">Pinterest Icon</title>
          <g filter="">
            <use xlink:href="#pinterest"></use>
          </g>
        </svg>
      </a> <!-- icon-svg -->
    <?php endif; ?>


    <?php if(get_field('instagram', 'option')): ?>

      <?php $ig = get_field('instagram', 'option'); ?>

      <?php 
        if((substr_compare($ig,"http://",0,7)) === 0) $ig = $ig;
        else $ig = "http://$ig";
      ?>
      <a href="<?php echo $ig; ?>" class="icon-svg" role="menuitem" target="_blank">
        <svg viewBox="0 0 32 32" role="img" aria-labelledby="ig-title-head">
          <title id="ig-title-head">Instagram Icon</title>
          <g filter="">
            <use xlink:href="#instagram"></use>
          </g>
        </svg>
      </a> <!-- icon-svg -->
    <?php endif; ?>


  </div><!-- end role[menu] -->

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

  <div class="branding" itemscope itemtype="http://schema.org/Organization">
    <a href="<?php echo esc_url(home_url('/')); ?>" itemprop="url">
      <img src="<?php echo get_bloginfo('template_directory');?>/lib/images/svg/header-logo.svg" alt="<?php bloginfo('name'); ?>" itemprop="logo" onerror="this.src='<?php echo get_bloginfo('template_directory');?>/lib/images/min/head_logo.png'" class="no-select">
    </a>
  </div><!-- end branding -->

  <div class="wrapper--bare">

    <div class="header__burger" id="js-nav-toggle" role="button" data-target=".nav">
      <span class="header__burger-icon" id="js-nav-icon"></span>
    </div> <!-- end header__burger -->



    <nav class="nav" id="js-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">

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

  </div><!--end wrapper-bare -->

</header><!-- end header[banner] -->  
