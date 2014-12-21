<?php
/**
 * Clean up the_excerpt()
 */
function roots_excerpt_more($more) {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'roots') . '</a>';
}
add_filter('excerpt_more', 'roots_excerpt_more');



/**
 * Manage output of wp_title()
 */
function roots_wp_title($title) {
  if (is_feed()) {
    return $title;
  }

  $title .= get_bloginfo('name');

  return $title;
}
add_filter('wp_title', 'roots_wp_title', 10);



/**
 * Remove class from post_class() function.
 */
function remove_class( $classes ) {
	$classes = array_diff( $classes, array( 'hentry', 'post' ) ); // seperate with commas for more than one class.
	return $classes;
}
add_filter( 'post_class', 'remove_class' );


add_filter('body_class','browser_body_class');
function browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) $classes[] = 'ie';
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';
	return $classes;
}


/*
*  Change the Options Page menu to 'Theme Options'
*/

if( function_exists('acf_set_options_page_title') )
{
    acf_set_options_page_title( __('Global Theme Options') );
}


/*
*  Change the Options Page menu to 'Extra'
*/

if( function_exists('acf_set_options_page_menu') )
{
    acf_set_options_page_menu( __('Global Options') );
}


/*
*  Add Schema for SEO
*/

function html_tag_schema() {
    $schema = 'http://schema.org/';

    // Is single post
    if( is_single() ) {
        $type = "Article";
    }

    // Is author page
    elseif( is_author() ) { 
        $type = 'ProfilePage';
    }
    
    // Is search results page
    elseif( is_search() ) {
        $type = 'SearchResultsPage';
    }

    // Is product page
    elseif( function_exists(is_woocommerce) && is_woocommerce() ) {
        $type = 'Product';
    }

    else {
        $type = 'WebPage';
    }

    echo 'itemscope="itemscope" itemtype="' . $schema . $type . '"';
}



/*
*  Custom Title Length
*/  
function short_title($after = '', $length) {
    $mytitle = explode(' ', get_the_title(), $length);
    if (count($mytitle)>=$length) {
        array_pop($mytitle);
        $mytitle = implode(" ",$mytitle). $after;
    } else {
        $mytitle = implode(" ",$mytitle);
    }
    return $mytitle;
} 


// function gk_social_buttons($content) {
//     global $post;
//     $permalink = get_permalink($post->ID);
//     $title = get_the_title();
//     if(!is_feed() && !is_home() && !is_page()) {
//         $content = $content . '<div class="clearfix">

//         <a class="icon-svg" href="https://www.pinterest.com/pin/create/button/?url='.$permalink.'&description='.$title.'"data-pin-do="buttonPin"data-pin-config="above">
//           <svg viewBox="0 0 32 32" title="Pinterest" aria-hidden="true">
//             <g filter="">
//               <use xlink:href="#pinterest"></use>
//             </g>
//           </svg>
//         </a>  
//         <a class="icon-svg" href="https://www.facebook.com/sharer/sharer.php?u='.$permalink.'"
//              onclick="window.open(this.href, \'facebook-share\',\'width=580,height=296\');return false;">
//           <svg viewBox="0 0 32 32" title="Facebook" aria-hidden="true">
//             <g filter="">
//               <use xlink:href="#facebook"></use>
//             </g>
//           </svg>
//         </a>  
//         <a class="icon-svg" href="http://twitter.com/share?text='.$title.'&url='.$permalink.'"
//             onclick="window.open(this.href, \'twitter-share\', \'width=550,height=235\');return false;">
//           <svg viewBox="0 0 32 32" title="Twitter" aria-hidden="true">
//             <g filter="">
//               <use xlink:href="#twitter"></use>
//             </g>
//           </svg>
//         </a>  
//     </div>';
//     }
//     return $content;
// }
// add_filter('the_content', 'gk_social_buttons');


// function pin_js(){
//    echo '<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>';
// }
// add_action('wp_footer','pin_js');

