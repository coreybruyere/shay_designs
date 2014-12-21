<time class="published  post__time" datetime="<?php echo get_the_time('c'); ?>" itemprop="datePublished" pubdate>
	<span class="post__date"><?php echo date('F j'); ?></span> 
	<span class="post__date"><?php echo date('Y'); ?></span> 
</time>
<p class="byline author vcard post__author"><?php echo __('By', 'roots'); ?> <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" rel="author" class="fn"><?php echo get_the_author(); ?></a></p>
