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

	<div class="woocommerce-tabs  tab">
		<ul class="tabs tab__list">
			<?php foreach ( $tabs as $key => $tab ) : ?>

				<li class="<?php echo $key ?>_tab  tab__item">
					<a href="#tab-<?php echo $key ?>" class="clean-link"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
				</li>

			<?php endforeach; ?>
		</ul>

		<?php foreach ( $tabs as $key => $tab ) : ?>

			<div class="<?php echo $key ?>_tab  tab__item--drawer">
				<a href="#tab-<?php echo $key ?>" class="clean-link"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
			</div>

			<div class="tab__panel">
				<div class="panel entry-content  tab__article" id="tab-<?php echo $key ?>">
				<?php call_user_func( $tab['callback'], $key, $tab ) ?>
      			<?php get_template_part('templates/social'); ?>
				</div>
			</div>

		<?php endforeach; ?>
	</div>

<?php endif; ?>