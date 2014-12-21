<form role="search" method="get" class="form--group  section" role="search" action="<?php echo esc_url(home_url('/')); ?>">
    <input type="search" value="<?php echo get_search_query(); ?>" name="s" class="span-3-4  input--inline" placeholder="<?php _e('Search...', 'roots'); ?> ">
    <button type="submit" class="span-1-4"><?php _e('Go', 'roots'); ?></button>
</form>
