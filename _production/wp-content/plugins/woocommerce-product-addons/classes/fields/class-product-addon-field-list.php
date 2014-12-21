<?php
/**
 * Checkbox/radios field
 */
class Product_Addon_Field_List extends Product_Addon_Field {

	/**
	 * Validate an addon
	 * @return bool pass or fail, or WP_Error
	 */
	public function validate() {
		if ( ! empty( $this->addon['required'] ) ) {
			if ( ! $this->value || sizeof( $this->value ) == 0 ) {
				return new WP_Error( 'error', sprintf( __( '"%s" is a required field.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
			}
		}
		return true;
	}

	/**
	 * Process this field after being posted
	 * @return array on success, WP_ERROR on failure
	 */
	public function get_cart_item_data() {
		$cart_item_data = array();
		
		if ( empty( $this->value ) ) {
			return false;
		}

		foreach ( $this->addon['options'] as $option ) {
			if ( in_array( strtolower( sanitize_title( $option['label'] ) ), array_map( 'strtolower', array_values( $this->value ) ) ) ) {
				$cart_item_data[] = array(
					'name'  => $this->addon['name'],
					'value' => $option['label'],
					'price' => $this->get_option_price( $option )
				);
			}
		}

		return $cart_item_data;
	}
}