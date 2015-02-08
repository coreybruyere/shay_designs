
<footer class="footer" role="contentinfo" itemscope="itemscope" itemtype="http://schema.org/WPFooter">

  <div class="wrapper">

    <div class="section-region">

        <div class="branding--footer" itemscope itemtype="http://schema.org/Organization">
          <a href="<?php echo esc_url(home_url('/')); ?>" itemprop="url">
            <img src="<?php echo get_bloginfo('template_directory');?>/lib/images/svg/header-logo.svg" onerror="this.src=<?php echo get_bloginfo('template_directory');?>/lib/images/min/main-logo--white.png" alt="<?php bloginfo('name'); ?>" class="no-select branding__img">
          </a>
        </div><!-- end branding-footer -->  

    </div><!-- end section-region -->

    <div class="section-region">

      <nav class="nav--footer" role="navigation">

        <ul class="list-ui" role="menu">

          <li class="nav--footer__item" role="presentation">
            <a href="" class="clean-link" role="menuitem">Contact</a>
          </li><!-- end nav-footer__item[presentation] -->

          <li class="nav--footer__item" role="presentation">
            <a href="" class="clean-link" role="menuitem">FAQ</a>
          </li><!-- end nav-footer__item[presentation] -->

          <li class="nav--footer__item" role="presentation">
            <a href="" class="clean-link" role="menuitem">Policy</a>
          </li><!-- end nav-footer__item[presentation] -->

        </ul><!-- end list-ui[menu] -->

      </nav><!-- end nav[navigation] -->

    </div><!-- end section-region -->


    <div class="section-region" role="menu">


      <?php if(get_field('facebook', 'option')): ?>

        <?php $fb = get_field('facebook', 'option'); ?>

        <?php 
          if((substr_compare($fb,"http://",0,7)) === 0) $fb = $fb;
          else $fb = "http://$fb";
        ?>

        <span class="tooltip  tooltip--top" data-tooltip="Facebook">

          <a href="<?php echo $fb; ?>" class="icon-svg--lrg" role="menuitem" target="_blank">
            <svg viewBox="0 0 32 32" role="img" aria-labelledby="fb-title-foot">
              <title id="fb-title-foot">Facebook Icon</title>
              <g filter="">
                <use xlink:href="#facebook"></use>
              </g>
            </svg>
          </a><!-- icon-svg-lrg[menuitem] -->

        </span><!-- end tooltip[tooltip] -->

      <?php endif; ?>


      <?php if(get_field('pinterest', 'option')): ?>

        <?php $pin = get_field('pinterest', 'option'); ?>

        <?php 
          if((substr_compare($pin,"http://",0,7)) === 0) $pin = $pin;
          else $pin = "http://$pin";
        ?>

        <span class="tooltip  tooltip--top" data-tooltip="Pinterest"> 

          <a href="<?php echo $pin; ?>" class="icon-svg--lrg" role="menuitem" target="_blank">
            <svg viewBox="0 0 32 32" role="img" aria-labelledby="pin-title-foot">
              <title id="pin-title-foot">Pinterest Icon</title>
              <g filter="">
                <use xlink:href="#pinterest"></use>
              </g>
            </svg>
          </a><!-- icon-svg-lrg[menuitem] -->

        </span><!-- end tooltip[tooltip] -->

      <?php endif; ?>


      <?php if(get_field('instagram', 'option')): ?>

        <?php $ig = get_field('instagram', 'option'); ?>

        <?php 
          if((substr_compare($ig,"http://",0,7)) === 0) $ig = $ig;
          else $ig = "http://$ig";
        ?>

        <span class="tooltip  tooltip--top" data-tooltip="Instagram" aria-hidden="true">

          <a href="<?php echo $ig; ?>" class="icon-svg--lrg" role="menuitem" target="_blank">
            <svg viewBox="0 0 32 32" role="img" aria-labelledby="ig-title-foot">
              <title id="ig-title-foot">Instagram Icon</title>
              <g filter="">
                <use xlink:href="#instagram"></use>
              </g>
            </svg>
          </a><!-- icon-svg-lrg[menuitem] -->

        </span><!-- end tooltip[tooltip] -->

      <?php endif; ?>


      <div class="section">

        <small>&copy; <?php bloginfo( 'name' ); ?> <?php echo date('Y'); ?></small>

      </div><!-- end section -->

    </div><!-- end section-region -->

  </div><!-- end wrapper -->

</footer><!-- end footer[content-info] -->
