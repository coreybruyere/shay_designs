<?php
/**
 * Custom fields (text)
 */
class Product_Addon_Field_Custom extends Product_Addon_Field {

	/**
	 * Validate an addon
	 * @return bool pass, or WP_Error
	 */
	public function validate() {
		foreach ( $this->addon['options'] as $key => $option ) {
			$option_key = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
			$posted     = isset( $this->value[ $option_key ] ) ? $this->value[ $option_key ] : '';

			if ( ! empty( $this->addon['required'] ) ) {
				if ( $posted === "" || sizeof( $posted ) == 0 ) {
					return new WP_Error( 'error', sprintf( __( '"%s" is a required field.', 'woocommerce-product-addons' ), $this->addon['name'] ) );
				}
			}

			switch ( $this->addon['type'] ) {
				case "custom_price" :
					if ( ! empty( $option['min'] ) && ! empty( $posted ) && $posted < $option['min'] ) {
						return new WP_Error( 'error', sprintf( __( 'The minimum allowed amount for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['min'] ) );
					}

					if ( ! empty( $option['max'] ) && ! empty( $posted ) && $posted > $option['max'] ) {
						return new WP_Error( 'error', sprintf( __( 'The maximum allowed amount for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['max'] ) );
					}
				break;
				case "input_multiplier" :
					$posted = absint( $posted );

					if ( $posted < 0 ) {
						return new WP_Error( 'error', sprintf( __( 'Please enter a value greater than 0 for "%s - %s".', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'] ) );
					}

					if ( ! empty( $option['min'] ) && ! empty( $posted ) && $posted < $option['min'] ) {
						return new WP_Error( 'error', sprintf( __( 'The minimum allowed value for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['min'] ) );
					}

					if ( ! empty( $option['max'] ) && ! empty( $posted ) && $posted > $option['max'] ) {
						return new WP_Error( 'error', sprintf( __( 'The maximum allowed value for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['max'] ) );
					}
				break;
				case "custom" :
				case "custom_textarea" :
					if ( ! empty( $option['min'] ) && ! empty( $posted ) && strlen( $posted ) < $option['min'] ) {
						return new WP_Error( 'error', sprintf( __( 'The minimum allowed length for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['min'] ) );
					}

					if ( ! empty( $option['max'] ) && ! empty( $posted ) && strlen( $posted ) > $option['max'] ) {
						return new WP_Error( 'error', sprintf( __( 'The maximum allowed length for "%s - %s" is %s.', 'woocommerce-product-addons' ), $this->addon['name'], $option['label'], $option['max'] ) );
					}
				break;
			}

		}
		return true;
	}

	/**
	 * Process this field after being posted
	 * @return array on success, WP_ERROR on failure
	 */
	public function get_cart_item_data() {
		$cart_item_data           = array();
		
		foreach ( $this->addon['options'] as $key => $option ) {
			$option_key = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
			$posted     = isset( $this->value[ $option_key ] ) ? $this->value[ $option_key ] : '';
					
			if ( empty( $posted ) ) {
				continue;
			}

			$label = $this->get_option_label( $option );
			$price = $this->get_option_price( $option );

			switch ( $this->addon['type'] ) {
				case "custom_price" :
					$price = floatval( sanitize_text_field( $posted ) );

					if ( $price >= 0 ) {
						$cart_item_data[] = array(
							'name' 		=> $label,
							'value'		=> $price,
							'price' 	=> $price,
							'display'	=> strip_tags( woocommerce_price( $price ) )
						);
					}
				break;
				case "input_multiplier" :
					$posted = absint( $posted );

					$cart_item_data[] = array(
						'name' 		=> $label,
						'value'		=> $posted,
						'price' 	=> $posted * $price
					);
				break;
				default :
					$cart_item_data[] = array(
						'name' 		=> $label,
						'value'		=> wp_kses_post( $posted ),
						'price' 	=> $price
					);
				break;
			}
		}

		return $cart_item_data;
	}
}