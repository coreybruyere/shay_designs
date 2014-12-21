      <div class="section">
        <div class="clearfix">
          <div class="title--small">{share}</div>
  
          <?php global $post; ?>
          <?php $permalink = get_permalink($post->ID);  ?>
          <?php $title = get_the_title();  ?>
          
          <div class="social-share" id="js-social-share"></div>

        </div><!-- end clearfix -->
      </div><!-- end section -->