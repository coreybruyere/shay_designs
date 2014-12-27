      <div class="section">
        <div class="clearfix">
  
          <?php global $post; ?>
          <?php $image_link  = wp_get_attachment_url( get_post_thumbnail_id() ); ?>
          <?php $permalink = get_permalink($post->ID); ?>
          <?php $title = get_the_title(); ?>
          <?php $desc = $post->post_excerpt; ?>
          <button class="button--secondary  float-l" id="js-social-share">
          	<span class="icon-svg--color">
	            <svg viewBox="0 0 32 32" title="Share">
	              <g filter="">
	                <use xlink:href="#heart"></use>
	              </g>
	            </svg>
            </span>
            <span>Share</span>
          </button> <!-- icon-svg -->

          <ul class="share" role="menu" id="js-share-menu">
          	<li role="presentation" class="share__item">
          		<a target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo $permalink; ?>&description=<?php echo $desc; ?>&media=<?php echo $image_link; ?>" role="menuitem">
					<span class="icon-svg--lrg">
	            		<svg viewBox="0 0 32 32" title="Share on Pinterest">
		             		<g filter="">
		                		<use xlink:href="#pinterest"></use>
		              		</g>
	            		</svg>
            		</span>
          		</a>
          	</li>

          	<li role="presentation" class="share__item">
          		<a target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo $title; ?>&url=<?php echo $permalink; ?>" role="menuitem">
					<span class="icon-svg--lrg">
	            		<svg viewBox="0 0 32 32" title="Share on Twitter">
		             		<g filter="">
		                		<use xlink:href="#twitter"></use>
		              		</g>
	            		</svg>
            		</span>
          		</a>
          	</li>

          	<li role="presentation" class="share__item">
          		<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $permalink; ?>" role="menuitem">
					<span class="icon-svg--lrg">
	            		<svg viewBox="0 0 32 32" title="Share on Facebook">
		             		<g filter="">
		                		<use xlink:href="#facebook"></use>
		              		</g>
	            		</svg>
            		</span>
          		</a> 
          	</li>

          </ul>

          

        </div><!-- end clearfix -->
      </div><!-- end section -->