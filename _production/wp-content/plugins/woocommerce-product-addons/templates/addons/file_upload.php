<?php foreach ( $addon['options'] as $key => $option ) :

	$price = ($option['price']>0) ? ' (' . woocommerce_price( get_product_addon_price_for_display( $option['price'] ) ) . ')' : '';

	if ( empty( $option['label'] ) ) : ?>

		<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
			<input type="file" class="input-text addon" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>-<?php echo sanitize_title( $option['label'] ); ?>" /> <small><?php echo sprintf( __( '(max file size %s)', 'woocommerce-product-addons' ), $max_size ) ?></small>
		</p>

	<?php else : ?>

		<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
			<label><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?> <input type="file" class="input-text addon" data-price="<?php echo get_product_addon_price_for_display( $option['price'] ); ?>" name="addon-<?php echo sanitize_title( $addon['field-name'] ); ?>-<?php echo sanitize_title( $option['label'] ); ?>" /> <small><?php echo sprintf( __( '(max file size %s)', 'woocommerce-product-addons' ), $max_size ) ?></small></label>
		</p>

	<?php endif; ?>

<?php endforeach; ?>