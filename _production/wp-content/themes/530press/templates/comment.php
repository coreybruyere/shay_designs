<div class="flag--top  section-region">
  <div class="flag__image"><?php echo get_avatar($comment, $size = '50'); ?></div>  
  <div class="flag__body">
    <time datetime="<?php echo get_comment_date('c'); ?>" class="time"><small><a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>"><?php printf(__('%1$s', 'roots'), get_comment_date(),  get_comment_time()); ?></small></a></time>
    <h5 class="no-margin--t"><?php echo get_comment_author_link(); ?></h5> 

    <?php if ($comment->comment_approved == '0') : ?>
      <div class="alert alert-info">
        <?php _e('Your comment is awaiting moderation.', 'roots'); ?>
      </div>
    <?php endif; ?>

    <?php comment_text(); ?>

    <div class="clearfix">
    <small class="float-l"><?php edit_comment_link(__('(Edit)', 'roots'), '', ''); ?></small>
    <small class="float-r"><?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?></small>
    </div>

  </div><!-- end flag__body -->
