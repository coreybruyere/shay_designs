<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';
?>
<li <?php post_class( 'grid__respond  product', $classes  ); ?>>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	

	<a href="<?php the_permalink(); ?>">

		<figure class="product__figure"> 
			<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
			<figcaption>
				<div class="product__caption">
				<?php if ( $product->is_on_sale() ) : ?>

					<span class="required">{sale}</span>

				<?php endif; ?>
				<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>

				</div>	

				<span class="icon-svg">
				  <svg viewBox="0 0 32 32">
				    <g filter="">
				      <use xlink:href="#info"></use>
				    </g>
				  </svg>
				</span><!-- icon-svg -->
				
				<div class="product__caption-title"><?php the_title(); ?></div> 
			</figcaption>

		</figure>
		

		<?php $prod_title = get_the_title(); ?>
		<h3 title="<?php echo $prod_title; ?>" class="product__title"><?php echo mb_strimwidth($prod_title, 0, 26, '...'); ?></h3>

		<?php
			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
		?>


	</a>

	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

</li>

