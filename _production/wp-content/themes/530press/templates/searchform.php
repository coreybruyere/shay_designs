<form role="search" method="get" class="search-form" role="search" action="<?php echo esc_url(home_url('/')); ?>">  

		<a aria-label="Open Search" class="search-form__label js-search-form-prop" role="button" tabindex="0"> 

			<span class="icon-svg no-margin">   
				<svg viewBox="0 0 32 32" role="img" aria-labelledby="search-icon" aria-hidden="true">
				  <title id="search-icon"><?php _e('Search', 'roots'); ?></title>
				  <g filter="">
				    <use xlink:href="#search-2"></use> 
				  </g>
				</svg>
			</span> 

			<label class="is-visually-hidden js-skip" for="s" >Search Terms</label>

		</a><!-- end search-form_label --> 

		<span class="search-form__box js-search-form-box" id="search-box">  

			<span class="input--group">

		    <input type="search" value="<?php echo get_search_query(); ?>" name="s" class="input--group__text search-form__input js-search-form-input" placeholder="<?php _e('Search...', 'roots'); ?>" id="s">

		    <span class="input--group__button search-form__group">

			    <button type="submit" class="search-form__button">

			    	<span class="icon-svg">
			    		<svg viewBox="0 0 32 32" role="img" aria-labelledby="search-icon" aria-hidden="true">
			    		  <title id="search-icon"><?php _e('Search', 'roots'); ?></title>
			    		  <g filter="">
			    		    <use xlink:href="#search-2"></use> 
			    		  </g> 
			    		</svg>
			    	</span>

			    </button><!-- end search-form_button -->  

		    </span><!-- end input-group_button -->  

	    </span><!-- end input-group -->

	  </span><!-- end search-form_box -->

	  <a aria-label="Close Search" class="search-form__close js-search-form-close" role="button" tabindex="0"> 

	  	<span class="icon-svg no-margin">   
	  		<svg viewBox="0 0 32 32" role="img" aria-labelledby="search-close" aria-hidden="true">
	  		  <title id="search-close"><?php _e('Close Search', 'roots'); ?></title>
	  		  <g filter="">
	  		    <use xlink:href="#close"></use> 
	  		  </g>
	  		</svg>
	  	</span> 

	  </a><!-- end search-form_close --> 

</form><!-- end search-form -->
