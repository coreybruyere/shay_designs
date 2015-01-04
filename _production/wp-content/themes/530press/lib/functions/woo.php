<?php 

/** 
 * Optimize WooCommerce Scripts
 * Remove WooCommerce Generator tag, styles, and scripts from non WooCommerce pages.
 */



add_action( 'wp_enqueue_scripts', 'custom_woo', 99 ); 
 
function custom_woo() {
	//remove generator meta tag
	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
 
	//first check that woo exists to prevent fatal errors
	if ( function_exists( 'is_woocommerce' ) ) {
		//dequeue scripts and styles
		if ( !is_woocommerce() && !is_cart() && !is_checkout() ) {
			wp_dequeue_style( 'woocommerce_frontend_styles' );
			wp_dequeue_style( 'woocommerce_fancybox_styles' );
			wp_dequeue_style( 'woocommerce_chosen_styles' );
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			wp_dequeue_script( 'wc_price_slider' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-add-to-cart' );
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_dequeue_script( 'wc-checkout' );
			wp_dequeue_script( 'wc-add-to-cart-variation' );
			wp_dequeue_script( 'wc-single-product' );
			wp_dequeue_script( 'wc-cart' );
			wp_dequeue_script( 'wc-chosen' );
			wp_dequeue_script( 'woocommerce' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_script( 'fancybox' );
			// wp_dequeue_script( 'jquery-blockui' );
			// wp_dequeue_script( 'jquery-placeholder' );
			// wp_dequeue_script( 'jqueryui' );
		}
	}
}

add_action( 'wp', 'init' );

function init() {

	/** 
	 * The Over Rides
	 * 
	 */
	global $post;

	$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$lightbox_en          = get_option( 'woocommerce_enable_lightbox' ) == 'yes' ? true : false;
	$ajax_cart_en         = get_option( 'woocommerce_enable_ajax_add_to_cart' ) == 'yes' ? true : false;
	$assets_path          = str_replace( array( 'http:', 'https:' ), '', get_bloginfo( 'stylesheet_directory' ) ) . '/lib/';
	$frontend_script_path = $assets_path . 'javascripts/frontend/';
	$chosen_script_path = $assets_path . 'javascripts/chosen/';

	// Register any scripts for later use, or used as dependencies
	wp_register_script( 'chosen', $chosen_script_path . 'chosen.jquery' . $suffix . '.js', array( 'jquery' ), '1.0.0', true );
	// wp_register_script( 'jquery-blockui', $assets_path . 'jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.60', true );
	// wp_register_script( 'jquery-payment', $assets_path . 'jquery-payment/jquery.payment' . $suffix . '.js', array( 'jquery' ), '1.0.2', true );
	wp_register_script( 'wc-credit-card-form', $frontend_script_path . 'credit-card-form' . $suffix . '.js', array( 'jquery', 'jquery-payment' ), WC_VERSION, true );

	wp_register_script( 'wc-add-to-cart-variation', $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
	wp_register_script( 'wc-single-product', $frontend_script_path . 'single-product' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
	wp_register_script( 'wc-country-select', $frontend_script_path . 'country-select' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
	wp_register_script( 'wc-address-i18n', $frontend_script_path . 'address-i18n' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
	// wp_register_script( 'jquery-cookie', $assets_path . 'jquery-cookie/jquery.cookie' . $suffix . '.js', array( 'jquery' ), '1.3.1', true );

	// Queue frontend scripts conditionally
	if ( $ajax_cart_en )
		wp_enqueue_script( 'wc-add-to-cart', $frontend_script_path . 'add-to-cart' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );

	if ( is_cart() )
		wp_enqueue_script( 'wc-cart', $frontend_script_path . 'cart' . $suffix . '.js', array( 'jquery', 'wc-country-select' ), WC_VERSION, true );


	if ( is_checkout() ) {

		if ( get_option( 'woocommerce_enable_chosen' ) == 'yes' ) {
			wp_enqueue_script( 'wc-chosen', $frontend_script_path . 'chosen-frontend' . $suffix . '.js', array( 'chosen' ), WC_VERSION, true );
			wp_enqueue_style( 'woocommerce_chosen_styles', $assets_path . 'styles/css/chosen.css' );
		}

		// wp_enqueue_script( 'wc-checkout', $frontend_script_path . 'checkout' . $suffix . '.js', array('jquery'), WC_VERSION, true );
	}

	if ( is_page( get_option( 'woocommerce_myaccount_page_id' ) ) ) {

		if ( get_option( 'woocommerce_enable_chosen' ) == 'yes' ) {
			wp_enqueue_script( 'wc-chosen', $frontend_script_path . 'chosen-frontend' . $suffix . '.js', array( 'chosen' ), WC_VERSION, true );
			wp_enqueue_style( 'woocommerce_chosen_styles', $assets_path . 'styles/css/chosen.css' );
		}
	}

	if ( is_add_payment_method_page() )
		wp_enqueue_script( 'wc-add-payment-method', $frontend_script_path . 'add-payment-method' . $suffix . '.js', array( 'jquery', 'woocommerce' ), WC_VERSION, true );

		// if ( $lightbox_en && ( is_product() || ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) ) {
		// 	wp_enqueue_script( 'prettyPhoto', $assets_path . 'javascripts/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
		// 	wp_enqueue_script( 'prettyPhoto-init', $assets_path . 'javascripts/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery','prettyPhoto' ), WC_VERSION, true );
		// 	wp_enqueue_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css' );
		// }

	if ( is_product() )
		wp_enqueue_script( 'wc-single-product' );

	// Global frontend scripts
	wp_enqueue_script( 'woocommerce', $frontend_script_path . 'woocommerce' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), WC_VERSION, true );
	wp_enqueue_script( 'wc-cart-fragments', $frontend_script_path . 'cart-fragments' . $suffix . '.js', array( 'jquery', 'jquery-cookie' ), WC_VERSION, true );



}



/**
 * Make theme compatible with WC
 */
add_action( 'after_setup_theme', 'woocommerce_support' );

function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}


/**
 * Remove Woo Theme Assets
 */
// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 ); 
add_filter( 'woocommerce_show_page_title', '__return_false' ); 
add_filter( 'woocommerce_enqueue_styles', '__return_false' );
add_filter( 'woocommerce_product_tabs', 'wcs_woo_remove_reviews_tab', 98 );
function wcs_woo_remove_reviews_tab($tabs) {
	unset($tabs['reviews']);
	return $tabs;
}


/**
 * Change number of related products on pdp
 */
add_filter( 'woocommerce_output_related_products_args', 'jk_related_products_args' );
  function jk_related_products_args( $args ) {

	$args['posts_per_page'] = 4; // 4 related products
	// $args['columns'] = 0; // arranged in 2 columns
	return $args;
}


/**
 * Change number of related products on pdp
 */
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;
    ob_start(); ?>
 
    <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('Cart','woothemes'); ?>" class="nav--menu__link  js-cart" role="menuitem" data-cart="<?php echo sprintf(_n('%d', '%d', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?>">
      <span class="nav--menu__icon">
        <svg viewBox="0 0 32 32">
          <g filter=""> 
            <use xlink:href="#trolley"></use>
          </g>
        </svg>
      </span>
      <span>Cart</span>

    <?php
    $fragments['a.js-cart'] = ob_get_clean();
    return $fragments;
}


/**
 * Add Breadcrumbs
 */
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
            'delimiter'   => ' / ',
            'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
        );
}


/**
 * Add Cart Variation Fix
 */
// function mv_my_theme_scripts() {

// wp_enqueue_script('add-to-cart-variation', get_template_directory_uri() . '/lib/javascripts/frontend/add-to-cart-variation.js',array('jquery'),'1.0',true);
// }

// add_action('wp_enqueue_scripts','mv_my_theme_scripts');

function add_to_cart_script(){
  if(is_product()){
    wp_enqueue_script('wc-add-to-cart-variation', get_template_directory_uri() . '/lib/javascripts/frontend/add-to-cart-variation.js',array('jquery'),'1.0',true);
  }
}
add_action('wp_head','add_to_cart_script');


/**
 * Update Price
 */
add_filter( 'woocommerce_variation_option_name', 'display_price_in_variation_option_name' );

function display_price_in_variation_option_name( $term ) {
    global $wpdb, $product;

    $result = $wpdb->get_col( "SELECT slug FROM {$wpdb->prefix}terms WHERE name = '$term'" );

    $term_slug = ( !empty( $result ) ) ? $result[0] : $term;


    $query = "SELECT postmeta.post_id AS product_id
                FROM {$wpdb->prefix}postmeta AS postmeta
                    LEFT JOIN {$wpdb->prefix}posts AS products ON ( products.ID = postmeta.post_id )
                WHERE postmeta.meta_key LIKE 'attribute_%'
                    AND postmeta.meta_value = '$term_slug'
                    AND products.post_parent = $product->id";

    $variation_id = $wpdb->get_col( $query );

    $parent = wp_get_post_parent_id( $variation_id[0] );

    if ( $parent > 0 ) {
        $_product = new WC_Product_Variation( $variation_id[0] );
        return $term . ' (' . woocommerce_price( $_product->get_price() ) . ')';
    }
    return $term;

}


/**
* Returns min price for variably priced products
**/
// add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2);
// function custom_variation_price( $price, $product ) {
// $price = '';
// $price .= woocommerce_price($product->min_variation_price);
// return $price;
// }


