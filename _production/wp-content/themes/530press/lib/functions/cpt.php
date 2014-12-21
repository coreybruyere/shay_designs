<?php

/** 
 * Custom Slug for Posts
 * 
 */

add_action( 'init', 'my_new_default_post_type', 1 );
function my_new_default_post_type() {
 
    register_post_type( 'post', array(
        'labels' => array(
            'name_admin_bar' => _x( 'Post', 'add new on admin bar' ),
        ),
        'public'  => true,
        '_builtin' => false, 
        '_edit_link' => 'post.php?post=%d', 
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array( 'slug' => 'blog' ),
        'query_var' => false,
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'post-formats' ),
    ) );
}


  // add_filter('roots_wrap_base', 'roots_wrap_base_cpts'); // Add our function to the roots_wrap_base filter

  // function roots_wrap_base_cpts($templates) {
  //   $cpt = get_post_type(); // Get the current post type
  //   if ($cpt) {
  //      array_unshift($templates, 'base-' . $cpt . '.php'); // Shift the template to the front of the array
  //   }
  //   return $templates; // Return our modified array with base-$cpt.php at the front of the queue
  // }



  // // ProductS
  // function custom_post_type() {

  //     $labels = array(
  //         'name'                => _x( 'Products', 'Product General Name', 'text_domain' ),
  //         'singular_name'       => _x( 'Product', 'Product Singular Name', 'text_domain' ),
  //         'menu_name'           => __( 'Products', 'text_domain' ),
  //         'parent_item_colon'   => __( 'Parent Product:', 'text_domain' ),
  //         'all_items'           => __( 'All Products', 'text_domain' ),
  //         'view_item'           => __( 'View Product', 'text_domain' ),
  //         'add_new_item'        => __( 'Add New Product', 'text_domain' ),
  //         'add_new'             => __( 'Add New', 'text_domain' ),
  //         'edit_item'           => __( 'Edit Product', 'text_domain' ),
  //         'update_item'         => __( 'Update Product', 'text_domain' ),
  //         'search_items'        => __( 'Search Product', 'text_domain' ),
  //         'not_found'           => __( 'Not found', 'text_domain' ),
  //         'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
  //     );
  //     $args = array(
  //         'label'               => __( 'products', 'text_domain' ),
  //         'description'         => __( 'Product Description', 'text_domain' ),
  //         'labels'              => $labels,
  //         'supports'            => array( 'title', 'editor', 'thumbnail' ),
  //         'taxonomies'          => array( 'category', 'post_tag' ),
  //         'hierarchical'        => false,
  //         'public'              => true,
  //         'show_ui'             => true,
  //         'show_in_menu'        => true,
  //         'show_in_nav_menus'   => true,
  //         'show_in_admin_bar'   => true,
  //         'menu_position'       => 5,
  //         'menu_icon'           => 'dashicons-share',
  //         'can_export'          => true,
  //         'has_archive'         => true,
  //         'exclude_from_search' => false,
  //         'publicly_queryable'  => true,
  //         'capability_type'     => 'page'
  //     );
  //     register_post_type( 'products', $args );
  // }

  // // Hook into the 'init' action
  // add_action( 'init', 'custom_post_type', 0 );
