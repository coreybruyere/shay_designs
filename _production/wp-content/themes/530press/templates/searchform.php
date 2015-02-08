<form role="search" method="get" class="search-form" role="search" action="<?php echo esc_url(home_url('/')); ?>">
    <input type="search" value="<?php echo get_search_query(); ?>" name="s" class="search-form__input" placeholder="<?php _e('Search...', 'roots'); ?>" id="js-search-form-field">
    <div class="search-form__prop" id="js-search-form-prop">
	    <button type="submit" class="search-form__button" id="js-search-form-btn">
	    	<span class="icon-svg">
					<svg viewBox="0 0 32 32" role="img" aria-labelledby="search-icon">
					  <title id="search-icon"><?php _e('Search', 'roots'); ?></title>
					  <g filter="">
					    <use xlink:href="#search-2"></use>
					  </g>
					</svg>
				</span>
	    </button> 
    </div>
    <div class="search-form__close" id="js-search-form-close">&#10005;</div>
</form>
