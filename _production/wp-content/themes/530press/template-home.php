<?php
/*
Template Name: Home Page Template
*/
?>

<?php while (have_posts()) : the_post(); ?>

	<section class="wrapper" role="region" id="[#TOUTS]">
	
	  <div class="tout">

	    <div class="tout">

	      <div class="tout__col--large">

	        <div class="tout__item  js-slide">

	          <?php if( have_rows('banner_slide') ): ?>
  
			  	<?php while ( have_rows('banner_slide') ) : the_row(); ?>
  
		          <?php $large_banner = get_sub_field('large_banner'); ?>
		          <?php $large_banner_link = get_sub_field('large_banner_link'); ?>
		          <?php $large_banner_text = get_sub_field('large_banner_title'); ?>

		          <a href="<?php echo $large_banner_link ?>" class="js-slide-item">
		          	<?php if( $large_banner_text ): ?>
		          	<h1 class="tout__text"><?php echo $large_banner_text; ?></h1> 
		          	<?php endif; ?>
		          	<img class="tout__image" src="<?php echo $large_banner['url']; ?>" alt="<?php echo $large_banner['alt']; ?>">
		          </a> 

	      		<?php endwhile; ?>

	      	  <?php endif; ?>

	        </div><!-- end tout__item -->

	      </div><!-- end tout__col-large -->

	      <div class="tout__col--side">

	        <div class="tout__item--side">

	          <?php $top_right = get_field('top_right_tout'); ?>
	          <?php $top_right_link = get_field('top_right_link'); ?>
	          <?php $top_right_text = get_field('top_right_title'); ?>

	          <a href="<?php echo $top_right_link ?>">
		      <?php if( $top_right_text ): ?>
	          <h2 class="tout__text"><?php echo $top_right_text; ?></h2>
	      	  <?php endif; ?>
	          <img class="tout__image" src="<?php echo $top_right['url']; ?>" alt="<?php echo $top_right['alt']; ?>">
	          </a>

	        </div><!-- end tout__item-side -->

	        <div class="tout__item--side">

	          <?php $middle_right = get_field('middle_right_tout'); ?>
	          <?php $middle_right_link = get_field('middle_right_link'); ?>
	          <?php $middle_right_text = get_field('middle_right_title'); ?>

	          <a href="<?php echo $middle_right_link ?>">
		      <?php if( $middle_right_text ): ?>
	          <h2 class="tout__text"><?php echo $middle_right_text; ?></h2>
	      	  <?php endif; ?>
	          <img class="tout__image" src="<?php echo $middle_right['url']; ?>" alt="<?php echo $middle_right['alt']; ?>">
	          </a>

	        </div><!-- end tout__item-side -->

	      </div><!-- end tout__col-side -->

	    </div><!-- end tout[row] -->

	    <div class="tout">

	      <div class="tout__col">

	        <div class="tout__item">

	          <?php $bottom_left = get_field('bottom_left_tout'); ?>
	          <?php $bottom_left_link = get_field('bottom_left_link'); ?>
	          <?php $bottom_left_text = get_field('bottom_left_title'); ?>

	          <a href="<?php echo $bottom_left_link ?>">
		      <?php if( $bottom_left_text ): ?>
	          <h2 class="tout__text"><?php echo $bottom_left_text; ?></h2>
	      	  <?php endif; ?>
	          <img class="tout__image" src="<?php echo $bottom_left['url']; ?>" alt="<?php echo $bottom_left['alt']; ?>">
	          </a>

	        </div><!-- end tout__item -->

	      </div><!-- end tout__col -->

	      <div class="tout__col">

	        <div class="tout__item">

	          <?php $bottom_middle = get_field('bottom_middle_tout'); ?>
	          <?php $bottom_middle_link = get_field('bottom_middle_link'); ?>
	          <?php $bottom_middle_text = get_field('bottom_middle_title'); ?>

	          <a href="<?php echo $bottom_middle_link ?>">
		      <?php if( $bottom_middle_text ): ?>
	          <h2 class="tout__text"><?php echo $bottom_middle_text; ?></h2>
	          <?php endif; ?>
	          <img class="tout__image" src="<?php echo $bottom_middle['url']; ?>" alt="<?php echo $bottom_middle['alt']; ?>">
	          </a>

	        </div><!-- end tout__item -->

	      </div><!-- end tout__col -->

	      <div class="tout__col">

	        <div class="tout__item">

	          <?php $bottom_right = get_field('bottom_right_tout'); ?>
	          <?php $bottom_right_link = get_field('bottom_right_link'); ?>
	          <?php $bottom_right_text = get_field('bottom_right_title'); ?>

	          <a href="<?php echo $bottom_right_link ?>">
		      <?php if( $bottom_right_text ): ?>
	          <h2 class="tout__text"><?php echo $bottom_right_text; ?></h2>
	          <?php endif; ?>
	          <img class="tout__image" src="<?php echo $bottom_right['url']; ?>" alt="<?php echo $bottom_right['alt']; ?>">
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
