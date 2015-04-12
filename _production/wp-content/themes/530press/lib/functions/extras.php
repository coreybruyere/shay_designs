<?php
/**
 * Clean up the_excerpt()
 */
function roots_excerpt_more($more) {
  return ' <a href="' . get_permalink() . '">' . __('[ &hellip; ]', 'roots') . '</a>';
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



/*
 * Manage google fonts of load_google_font()
 * set GOOGLE_FONTS constant in config.php
 */
add_action( 'wp_head', 'load_google_fonts' , 1);
function load_google_fonts() {
  if( ! defined( 'GOOGLE_FONTS' ) ) return;
  echo '<link href="http://fonts.googleapis.com/css?family=' . GOOGLE_FONTS . '" rel="stylesheet" type="text/css" />'."\n"; 
}



/**
 * Load a requested modal via AJAX.
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function load_modal() {

  global $wpdb;
  
  ob_start();

  get_template_part( 'templates/modal', $_POST['modal'] );

  $modal = ob_get_clean();

  echo $modal;
  
  // if ( $modal ) {
  //   echo $modal;
  // } else {
  //   echo 0;
  // }
  
  wp_die(); 
}
add_action( 'wp_ajax_load_modal',        'load_modal' );
add_action( 'wp_ajax_nopriv_load_modal', 'load_modal' );

// wp_localize_script( 'ajaxy', 'cb_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );



/*
 * Clean WP head
 */
add_action('after_setup_theme','start_cleanup');
function start_cleanup() {
  // Initialize the cleanup
  add_action('init', 'cleanup_head');
} 



/*
 * Add thumbnail sizes
 */
if ( function_exists( 'add_image_size' ) ) { 
  add_image_size( 'lrg-home-tout@x2', 1600, 1000, true ); //(cropped)
  add_image_size( 'lrg-home-tout', 800, 500, true ); //(cropped) 
  add_image_size( 'lrg-home-tout@low', 400, 250, true ); //(cropped) 
  add_image_size( 'sm-home-tout@x2', 650, 400, true ); //(cropped)
  add_image_size( 'sm-home-tout', 450, 200, true ); //(cropped)  
  add_image_size( 'sm-home-tout@low', 325, 200, true ); //(cropped)   
}


/*
 * Async for Picfill
 */
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
    if ( 'picfill' !== $handle )
        return $tag;

    return str_replace( ' src', ' async src', $tag );
}, 10, 2 );


/*
 * Remove query strings
 */
function ewp_remove_script_version( $src ){
  return remove_query_arg( 'ver', $src );
}
add_filter( 'script_loader_src', 'ewp_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'ewp_remove_script_version', 15, 1 );


/*
 * WordPress cleanup function
 */ 
function cleanup_head() {
    
  // EditURI link
  remove_action( 'wp_head', 'rsd_link' );

  // Category feed links
  remove_action( 'wp_head', 'feed_links_extra', 3 );

  // Post and comment feed links
  remove_action( 'wp_head', 'feed_links', 2 );
    
  // Windows Live Writer
  remove_action( 'wp_head', 'wlwmanifest_link' );

  // Index link
  remove_action( 'wp_head', 'index_rel_link' );

  // Previous link
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );

  // Start link
  remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );

  // Canonical
  remove_action('wp_head', 'rel_canonical', 10, 0 );

  // Shortlink
  remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

  // Links for adjacent posts
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

  // WP version
  remove_action( 'wp_head', 'wp_generator' );

}

