<?php

/**  
* Output items for display  
*/  
function woocommerce_pip_custom_order_items_table( $order, $show_price = FALSE ) {  

$return = '';  

foreach($order->get_items() as $item) {  
$_product = $order->get_product_from_item( $item );  
$sku = $variation = '';  
$sku = $_product->get_sku();  
$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );  
$variation = '<br/><small>' . $item_meta->display( TRUE, TRUE ) . '</small>';  
$return .= '<tr>  
<td class="pip_image" style="text-align:left; padding: 3px;">' . $_product->get_image() . '</td>  
<td style="text-align:left; padding: 3px;">' . $sku . '</td>  
<td style="text-align:left; padding: 3px;">' . apply_filters('woocommerce\_order_product_title', $item['name'], $_product) . $variation . '</td>  
<td style="text-align:left; padding: 3px;">'.$item['qty'].'</td>';  
if ($show_price) {  
$return .= '<td style="text-align:left; padding: 3px;">';  
if ( $order->display_cart_ex_tax || !$order->prices_include_tax ) :  
$ex_tax_label = ( $order->prices_include_tax ) ? 1 : 0;  
$return .= woocommerce_price( $order->get_line_subtotal( $item ), array('ex_tax_label' => $ex_tax_label ));  
else :  
$return .= woocommerce_price( $order->get_line_subtotal( $item, TRUE ) );  
endif;  
$return .= '  
</td>';  
}  
else {  
$return .= '<td style="text-align:left; padding: 3px;">';  
$return .= ($_product->get_weight()) ? $_product->get_weight() . ' ' . get_option('woocommerce_weight_unit') : __( 'n/a', 'woocommerce-pip' );  
$return .= '</td>';  
}  
$return .= '</tr>';  
}  
$return = apply_filters( 'woocommerce_pip_order_items_table', $return );  
return $return;

}