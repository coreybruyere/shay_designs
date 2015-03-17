<?php
/*
Template Name: Home Page Template
*/
?>

<?php while (have_posts()) : the_post(); ?>

	<section class="wrapper" role="region" id="[#TOUTS]">
	
	  <div class="tout">

	    <div class="tout">

	      <div class="tout__col--large carousel">

	        <div class="tout__item owl-carousel js-slide">

	          <?php if( have_rows('banner_slide') ): ?>
  
			  		<?php while( have_rows('banner_slide') ) : the_row(); ?>
  
		          <?php $large_banner 	   = get_sub_field('large_banner'); ?>
		          <?php $large_banner_link = get_sub_field('large_banner_link'); ?>
		          <?php $large_banner_text = get_sub_field('large_banner_title'); ?>
		          <?php $large_banner_id   = $large_banner['id']; ?>
		          <?php $large_banner_lrg  = $large_banner['sizes']['lrg-home-tout@x2']; ?>
		          <?php $large_banner_sm   = $large_banner['sizes']['lrg-home-tout@low']; ?>
		          <?php $large_banner_h    = $large_banner['sizes']['lrg-home-tout-height']; ?>
		          <?php $large_banner_w    = $large_banner['sizes']['lrg-home-tout-width']; ?>

		          <a href="<?php echo $large_banner_link ?>" class="js-slide-item" title="<?php echo $large_banner['alt']; ?>">
		          	<?php if( $large_banner_text ): ?>
		          	<h1 class="tout__text"><?php echo $large_banner_text; ?></h1> 
		          	<?php endif; ?>   
		          	<img class="tout__image owl-lazy" src="<?php echo $large_banner_sm; ?>" <?php echo tevkori_get_srcset_string( $large_banner_id, $large_banner_lrg ); ?> sizes="(min-width: 730px) 66vw, 100vw" alt="<?php echo $large_banner['alt']; ?>" width="<?php echo $large_banner_w; ?>" height="<?php echo $large_banner_h; ?>" />     
		          </a> 



	      		<?php endwhile; ?>

	      	  <?php endif; ?>

	        </div><!-- end tout__item -->

	        <div class="carousel__prev js-slide-prev" title="Previous"></div>

	        <div class="carousel__next js-slide-next" title="Next"></div>

	      </div><!-- end tout__col-large -->

	      <div class="tout__col--side float-r">

	        <div class="tout__item--side">

	          <?php $top_right      = get_field('top_right_tout'); ?>
	          <?php $top_right_link = get_field('top_right_link'); ?>
	          <?php $top_right_text = get_field('top_right_title'); ?>
	          <?php $top_right_id   = $top_right['id']; ?>
	          <?php $top_right_lrg  = $top_right['sizes']['sm-home-tout@x2']; ?>
	          <?php $top_right_sm   = $top_right['sizes']['sm-home-tout@low']; ?>
	          <?php $top_right_h    = $top_right['sizes']['sm-home-tout-height']; ?>
	          <?php $top_right_w    = $top_right['sizes']['sm-home-tout-width']; ?>

	          <a href="<?php echo $top_right_link ?>" title="<?php echo $top_right['alt']; ?>">
		      		<?php if( $top_right_text ): ?>
	          	<h2 class="tout__text"><?php echo $top_right_text; ?></h2>
	      	  	<?php endif; ?>
	          	<img src="<?php echo $top_right_sm; ?>" <?php echo tevkori_get_srcset_string( $top_right_id, $top_right_size ); ?> sizes="(min-width: 730px) 33vw, 100vw" class="tout__image" width="<?php echo $top_right_w; ?>" height="<?php echo $top_right_h; ?>">     
	          </a>

	        </div><!-- end tout__item-side -->

	        <div class="tout__item--side">

	          <?php $middle_right      = get_field('middle_right_tout'); ?>
	          <?php $middle_right_link = get_field('middle_right_link'); ?>
	          <?php $middle_right_text = get_field('middle_right_title'); ?>
	          <?php $middle_right_id   = $middle_right['id']; ?>
	          <?php $middle_right_lrg  = $middle_right['sizes']['sm-home-tout@x2']; ?>
	          <?php $middle_right_sm   = $middle_right['sizes']['sm-home-tout@low']; ?>
	          <?php $middle_right_h    = $middle_right['sizes']['sm-home-tout-height']; ?>
	          <?php $middle_right_w    = $middle_right['sizes']['sm-home-tout-width']; ?>

	          <a href="<?php echo $middle_right_link ?>">
		      		<?php if( $middle_right_text ): ?>
	          	<h2 class="tout__text"><?php echo $middle_right_text; ?></h2>
	      	  	<?php endif; ?>
	      	  	<img src="<?php echo $middle_right_sm; ?>" <?php echo tevkori_get_srcset_string( $middle_right_id, $middle_right_size ); ?> sizes="(min-width: 730px) 33vw, 100vw" class="tout__image" alt="<?php echo $middle_right['alt']; ?>" width="<?php echo $middle_right_w; ?>" height="<?php echo $middle_right_h; ?>">  
	          </a>

	        </div><!-- end tout__item-side -->

	      </div><!-- end tout__col-side -->

	    </div><!-- end tout[row] -->

	    <div class="tout">

	      <div class="tout__col">

	        <div class="tout__item">

	          <?php $bottom_left      = get_field('bottom_left_tout'); ?>
	          <?php $bottom_left_link = get_field('bottom_left_link'); ?>
	          <?php $bottom_left_text = get_field('bottom_left_title'); ?>
	          <?php $bottom_left_id   = $bottom_left['id']; ?>
	          <?php $bottom_left_lrg  = $bottom_left['sizes']['sm-home-tout@x2']; ?>
	          <?php $bottom_left_sm   = $bottom_left['sizes']['sm-home-tout@low']; ?>
	          <?php $bottom_left_h    = $bottom_left['sizes']['sm-home-tout-height']; ?>
	          <?php $bottom_left_w    = $bottom_left['sizes']['sm-home-tout-width']; ?>

	          <a href="<?php echo $bottom_left_link ?>">
		      	<?php if( $bottom_left_text ): ?>
	          <h2 class="tout__text"><?php echo $bottom_left_text; ?></h2>
	      	  <?php endif; ?>
	      	  <img src="<?php echo $bottom_left_sm; ?>" <?php echo tevkori_get_srcset_string( $bottom_left_id, $bottom_left_size ); ?> sizes="(min-width: 730px) 33vw, 100vw" class="tout__image" alt="<?php echo $bottom_left['alt']; ?>" width="<?php echo $bottom_left_w; ?>" height="<?php echo $bottom_left_h; ?>">  
	          </a>

	        </div><!-- end tout__item -->

	      </div><!-- end tout__col -->

	      <div class="tout__col">

	        <div class="tout__item">

	          <?php $bottom_middle      = get_field('bottom_middle_tout'); ?>
	          <?php $bottom_middle_link = get_field('bottom_middle_link'); ?>
	          <?php $bottom_middle_text = get_field('bottom_middle_title'); ?>
	          <?php $bottom_middle_id   = $bottom_middle['id']; ?>
	          <?php $bottom_middle_lrg  = $bottom_middle['sizes']['sm-home-tout@x2']; ?>
	          <?php $bottom_middle_sm   = $bottom_middle['sizes']['sm-home-tout@low']; ?>
	          <?php $bottom_middle_h    = $bottom_middle['sizes']['sm-home-tout-height']; ?>
	          <?php $bottom_middle_w    = $bottom_middle['sizes']['sm-home-tout-width']; ?>

	          <a href="<?php echo $bottom_middle_link ?>">
		      	<?php if( $bottom_middle_text ): ?>
	          <h2 class="tout__text"><?php echo $bottom_middle_text; ?></h2>
	          <?php endif; ?>
	          <img src="<?php echo $bottom_middle_sm; ?>" <?php echo tevkori_get_srcset_string( $bottom_middle_id, $bottom_middle_size ); ?> sizes="(min-width: 730px) 33vw, 100vw" class="tout__image" alt="<?php echo $bottom_middle['alt']; ?>" width="<?php echo $bottom_middle_w; ?>" height="<?php echo $bottom_middle_h; ?>">  
	          </a>

	        </div><!-- end tout__item -->

	      </div><!-- end tout__col -->

	      <div class="tout__col">

	        <div class="tout__item">

	          <?php $bottom_right      = get_field('bottom_right_tout'); ?>
	          <?php $bottom_right_link = get_field('bottom_right_link'); ?>
	          <?php $bottom_right_text = get_field('bottom_right_title'); ?>
	          <?php $bottom_right_id   = $bottom_right['id']; ?>
	          <?php $bottom_right_lrg  = $bottom_right['sizes']['sm-home-tout@x2']; ?>
	          <?php $bottom_right_sm   = $bottom_right['sizes']['sm-home-tout@low']; ?>
	          <?php $bottom_right_h    = $bottom_right['sizes']['sm-home-tout-height']; ?>
	          <?php $bottom_right_w    = $bottom_right['sizes']['sm-home-tout-width']; ?>

	          <a href="<?php echo $bottom_right_link ?>">
		        <?php if( $bottom_right_text ): ?>
	          <h2 class="tout__text"><?php echo $bottom_right_text; ?></h2>
	          <?php endif; ?>
	          <img src="<?php echo $bottom_right_sm; ?>" <?php echo tevkori_get_srcset_string( $bottom_right_id, $bottom_right_size ); ?> sizes="(min-width: 730px) 33vw, 100vw" class="tout__image" alt="<?php echo $bottom_right['alt']; ?>" width="<?php echo $bottom_right_w; ?>" height="<?php echo $bottom_middle_h; ?>">   
	          </a>

	        </div><!-- end tout__item -->

	      </div><!-- end tout__col -->

	    </div><!-- end tout[row] -->

	  </div><!-- end tout -->

	</section><!-- end wrapper -->

	<section class="form--signup" id="[#FORM]">

	  <div class="island">
		  
			<div id="mc_embed_signup">
				<form action="//shaydesigns.us7.list-manage.com/subscribe/post?u=cf7cf4f1a1ddfaa76f72cc8fc&amp;id=563d2cf888" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="form--group validate" target="_blank" role="form" novalidate>
				    <div id="mc_embed_signup_scroll">
				  	
					  	<div class="mc-field-group">
						  	<label class="form--signup__label" for="mce-EMAIL">Sign Up for the Shay Designs Newsletter!</label> 
						  	<input type="email" class="form--group__item  input--inline" name="EMAIL"id="mce-EMAIL">
						  	<button type="submit" class="form--group__item" name="subscribe" id="mc-embedded-subscribe" >Send</button>
					  	</div>

					  	<div id="mce-responses" class="clear">
					  		<div class="response" id="mce-error-response" style="display:none"></div>
					  		<div class="response" id="mce-success-response" style="display:none"></div>
					  	</div>

				    </div>
				</form>
			</div>
			<!--End mc_embed_signup-->

	  </div><!-- end island -->

	</section><!-- end form-signup -->


<?php endwhile; ?>
