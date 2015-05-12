<?php $cta_type = get_field('configure_header_cta', 'option'); ?>

<?php if( $cta_type == 'custom'): ?>

	<?php $cta_text = get_field('cta_text', 'option'); ?>
	<?php $cta_btn_option = get_field('want_a_button', 'option'); ?>
	<?php $cta_btn_text = get_field('cta_button', 'option'); ?>
	<?php $cta_btn_link = get_field('cta_button_link', 'option'); ?>

	<?php if( is_front_page() ): ?>
		<div class="cta-bar">
		  <div class="cta-bar__text"><?php echo $cta_text; ?></div>

		  <?php if ( $cta_btn_option ): ?>
		  	<a href="<?php echo $cta_btn_link; ?>" class="cta-bar__button button--tertiary"><?php echo $cta_btn_text; ?></a>  
			<?php endif; ?>

<!-- 		  <span role="button" class="cta-bar__close" id="js-close-cta">
		    <svg aria-hidden="true" viewBox="0 0 32 32">
		      <g filter="">
		        <use xlink:href="#close"></use>
		      </g>
		    </svg>
		  </span>  -->

		</div>
	<?php endif; ?>

<?php elseif( $cta_type == 'newsletter'): ?>

	<?php $newsletter_text = get_field('newsletter_text', 'option'); ?>
	<?php $newsletter_button = get_field('newsletter_button', 'option'); ?>

	<div class="cta-bar js-cta-bar"> 
	  <div class="cta-bar__text"><?php echo $newsletter_text; ?></div>

	  <span role="button" class="js-modal-toggle cta-bar__button button--tertiary" data-modal="newsletter"><?php echo $newsletter_button; ?></span>   

<!-- 	  <span role="button" class="cta-bar__close" id="js-close-cta">
	    <svg aria-hidden="true" viewBox="0 0 32 32">
	      <g filter="">
	        <use xlink:href="#close"></use>
	      </g>
	    </svg>
	  </span>  -->

	</div>

<?php elseif( $cta_type == 'none'): ?>

	<!-- Ain't nothin to see here -->

<?php endif; ?>
