<div class="flag--top  section">
  <div class="flag__image"><?php echo get_avatar($comment, $size = '50'); ?></div>  
  <div class="flag__body">
    <time datetime="<?php echo get_comment_date('c'); ?>" class="post__time"><a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>"><?php printf(__('%1$s', 'roots'), get_comment_date(),  get_comment_time()); ?></a></time>
    <h4 class="no-margin--t"><?php echo get_comment_author_link(); ?></h4>
    
    <?php edit_comment_link(__('(Edit)', 'roots'), '', ''); ?>

    <?php if ($comment->comment_approved == '0') : ?>
      <div class="alert alert-info">
        <?php _e('Your comment is awaiting moderation.', 'roots'); ?>
      </div>
    <?php endif; ?>

    <?php comment_text(); ?>
    <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>

  </div><!-- end flag__body -->
