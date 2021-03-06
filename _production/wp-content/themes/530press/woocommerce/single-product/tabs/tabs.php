<?php
/**
 * Single Product tabs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( ! empty( $tabs ) ) : ?>



	<div class="woocommerce-tabs  tab" id="js-tabs">
		<ul class="tabs tab__list" role="tablist">

			<?php $index = 1; ?>

			<?php foreach ( $tabs as $key => $tab ) : ?>

				<li class="<?php echo $key ?>_tab  tab__item  js-tab-item" role="tab" data-tab="tab-<?php echo $index; ?>">
					<a href="#tab-<?php echo $key ?>" class="clean-link"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>  
				</li>
				<?php $index++; ?>

			<?php endforeach; ?>
		</ul>

		<?php $index = 1; ?>

		<?php foreach ( $tabs as $key => $tab ) : ?>

			<div class="<?php echo $key ?>_tab  tab__item--drawer  js-drawer-tab">
				<a href="#tab-<?php echo $key ?>" class="clean-link"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
			</div>

			<?php $aria = ($key == 0) ? 'false' : 'true'; ?>

			<article class="tab__panel  js-tab-panel" role="tabpanel" aria-hidden="<?php echo $aria; ?>" id="tab-<?php echo $index; ?>">
				<div class="panel entry-content  tab__article" id="tab-<?php echo $key ?>">
				<?php call_user_func( $tab['callback'], $key, $tab ) ?>
				</div>

				<?php if ($index == 1): ?>
      			<?php get_template_part('templates/social'); ?>
				<?php endif; ?>

			</article> 
			
			<?php $index++; ?>

		<?php endforeach; ?>

	</div>

<?php endif; ?>