<form role="search" method="get" class="search-form" role="search" action="<?php echo esc_url(home_url('/')); ?>">  
		<a class="js-search-form-prop search-form__label" role="button" tabindex="0"> 
			<span class="icon-svg no-margin">   
				<svg viewBox="0 0 32 32" role="img" aria-labelledby="search-icon" aria-hidden="true">
				  <title id="search-icon"><?php _e('Search', 'roots'); ?></title>
				  <g filter="">
				    <use xlink:href="#search-2"></use> 
				  </g>
				</svg>
			</span>      
			<label class="is-visually-hidden" for="s" >Search Terms</label>
		</a><!-- end search-form_label --> 
		<span class="js-search-form-box search-form__box">  
			<span class="input--group">
		    <input type="search" value="<?php echo get_search_query(); ?>" name="s" class="js-search-form-input input--group__text search-form__input" placeholder="<?php _e('Search...', 'roots'); ?>" id="s">
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
			    </button>    
		    </span><!-- end input-group_button -->  
	    </span><!-- end input-group -->
	  </span><!-- end search-form_box -->
</form><!-- end search-form -->
