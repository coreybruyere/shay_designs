<div class="wrap woocommerce">
	<div class="icon32 icon32-posts-product" id="icon-woocommerce"><br/></div>

    <h2><?php _e( 'Global Add-ons', 'woocommerce-product-addons' ) ?> <a href="<?php echo add_query_arg( 'add', true, admin_url( 'edit.php?post_type=product&page=global_addons' ) ); ?>" class="add-new-h2"><?php _e( 'Add Global Add-on', 'woocommerce-product-addons' ); ?></a></h2><br/>

	<table id="global-addons-table" class="wp-list-table widefat" cellspacing="0">
		<thead>
			<tr>
				<th scope="col"><?php _e( 'Reference', 'woocommerce-product-addons' ); ?></th>
				<th><?php _e( 'Number of Fields', 'woocommerce-product-addons' ); ?></th>
				<th><?php _e( 'Priority', 'woocommerce-product-addons' ); ?></th>
				<th><?php _e( 'Applies to...', 'woocommerce-product-addons' ); ?></th>
				<th><?php _e( 'Actions', 'woocommerce-product-addons' ); ?></th>
			</tr>
		</thead>
		<tbody id="the-list">
			<?php
				$args = array(
					'posts_per_page'  => -1,
					'orderby'         => 'title',
					'order'           => 'ASC',
					'post_type'       => 'global_product_addon',
					'post_status'     => 'any',
					'suppress_filters' => true
				);

				$global_addons = get_posts( $args );

				if ( $global_addons ) {
					foreach ( $global_addons as $global_addon ) {
						$reference      = $global_addon->post_title;
						$priority       = get_post_meta( $global_addon->ID, '_priority', true );
						$objects        = (array) wp_get_post_terms( $global_addon->ID, apply_filters( 'woocommerce_product_addons_global_post_terms', array( 'product_cat' ) ), array( 'fields' => 'ids' ) );
						$product_addons = array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) );
						if ( get_post_meta( $global_addon->ID, '_all_products', true ) == 1 ) {
							$objects[] = 0;
						}
						?>
						<tr>
							<td><?php echo $reference; ?></td>
							<td><?php echo sizeof( $product_addons ); ?></td>
							<td><?php echo $priority; ?></td>
							<td><?php

								if ( in_array( 0, $objects ) ) {
									_e( 'All Products', 'woocommerce-product-addons' );
								} else {
									$term_names = array();
									foreach ( $objects as $object_id ) {
										$term = get_term_by( 'id', $object_id, 'product_cat' );
										if ( $term ) {
											$term_names[] = $term->name;
										}
									}

									$term_names = apply_filters( 'woocommerce_product_addons_global_display_term_names', $term_names, $objects );

									echo implode( ', ', $term_names );
								}

							?></td>
							<td>
								<a href="<?php echo add_query_arg( 'edit', $global_addon->ID, admin_url( 'edit.php?post_type=product&page=global_addons' ) ); ?>" class="button"><?php _e( 'Edit', 'woocommerce-product-addons' ); ?></a> <a href="<?php echo wp_nonce_url( add_query_arg( 'delete', $global_addon->ID, admin_url( 'edit.php?post_type=product&page=global_addons' ) ), 'delete_addon' ); ?>" class="button"><?php _e( 'Delete', 'woocommerce-product-addons' ); ?></a>
							</td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="5"><?php _e( 'No global add-ons exists yet.', 'woocommerce-product-addons' ); ?> <a href="<?php echo add_query_arg( 'add', true, admin_url( 'edit.php?post_type=product&page=global_addons' ) ); ?>"><?php _e( 'Add one?', 'woocommerce-product-addons' ); ?></a></td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
</div>
