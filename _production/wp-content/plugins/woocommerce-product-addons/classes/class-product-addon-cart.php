<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product_Addon_cart class.
 */
class Product_Addon_Cart {

	/**
	 * Constructor
	 */
	function __construct() {
		// Add to cart
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 20, 1 );

		// Load cart data per page load
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 20, 2 );

		// Get item data to display
		add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );

		// Add item data to the cart
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

		// Validate when adding to cart
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 3 );

		// Add meta to order
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 2 );

		// order again functionality
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 're_add_cart_item_data' ), 10, 3 );
	}

    /**
     * Add an error
     */
    public function add_error( $error ) {
		if ( function_exists( 'wc_add_notice' ) ) {
			wc_add_notice( $error, 'error' );
		} else {
			global $woocommerce;
			$woocommerce->add_error( $error );
		}
	}

	/**
	 * add_cart_item function.
	 *
	 * @access public
	 * @param mixed $cart_item
	 * @return void
	 */
	public function add_cart_item( $cart_item ) {
		// Adjust price if addons are set
		if ( ! empty( $cart_item['addons'] ) && apply_filters( 'woocommerce_product_addons_adjust_price', true, $cart_item ) ) {

			$extra_cost = 0;

			foreach ( $cart_item['addons'] as $addon ) {
				if ( $addon['price'] > 0 ) {
					$extra_cost += $addon['price'];
				}
			}

			$cart_item['data']->adjust_price( $extra_cost );
		}

		return $cart_item;
	}

	/**
	 * get_cart_item_from_session function.
	 *
	 * @access public
	 * @param mixed $cart_item
	 * @param mixed $values
	 * @return void
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( ! empty( $values['addons'] ) ) {
			$cart_item['addons'] = $values['addons'];
			$cart_item = $this->add_cart_item( $cart_item );
		}
		return $cart_item;
	}

	/**
	 * get_item_data function.
	 *
	 * @access public
	 * @param mixed $other_data
	 * @param mixed $cart_item
	 * @return void
	 */
	public function get_item_data( $other_data, $cart_item ) {
		if ( ! empty( $cart_item['addons'] ) ) {
			foreach ( $cart_item['addons'] as $addon ) {
				$name = $addon['name'];

				if ( $addon['price'] > 0 && apply_filters( 'woocommerce_addons_add_price_to_name', '__return_true' ) ) {
					$name .= ' (' . woocommerce_price( $addon['price'] ) . ')';
				}

				$other_data[] = array(
					'name'    => $name,
					'value'   => $addon['value'],
					'display' => isset( $addon['display'] ) ? $addon['display'] : ''
				);
			}
		}
		return $other_data;
	}

	/**
	 * add_cart_item_data function.
	 *
	 * @access public
	 * @param mixed $cart_item_meta
	 * @param mixed $product_id
	 * @return void
	 */
	public function add_cart_item_data( $cart_item_meta, $product_id, $post_data = null ) {
		global $woocommerce;

		if ( is_null( $post_data ) ) {
			$post_data = $_POST;
		}

		$product_addons = get_product_addons( $product_id );

		if ( empty( $cart_item_meta['addons'] ) ) {
			$cart_item_meta['addons'] = array();
		}

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once( 'fields/abstract-class-product-addon-field.php' );

			foreach ( $product_addons as $addon ) {

				$value = isset( $post_data[ 'addon-' . $addon['field-name'] ] ) ? $post_data[ 'addon-' . $addon['field-name'] ] : '';

				if ( is_array( $value ) ) {
					$value = array_map( 'stripslashes', $value );
				} else {
					$value = stripslashes( $value );
				}

				switch ( $addon['type'] ) {
					case "checkbox" :
					case "radiobutton" :
						include_once( 'fields/class-product-addon-field-list.php' );
						$field = new Product_Addon_Field_List( $addon, $value );
					break;
					case "custom" :
					case "custom_textarea" :
					case "custom_price" :
					case "input_multiplier" :
						include_once( 'fields/class-product-addon-field-custom.php' );
						$field = new Product_Addon_Field_Custom( $addon, $value );
					break;
					case "select" :
						include_once( 'fields/class-product-addon-field-select.php' );
						$field = new Product_Addon_Field_Select( $addon, $value );
					break;
					case "file_upload" :
						include_once( 'fields/class-product-addon-field-file-upload.php' );
						$field = new Product_Addon_Field_File_Upload( $addon, $value );
					break;
				}

				$data = $field->get_cart_item_data();

				if ( is_wp_error( $data ) ) {
					$this->add_error( $data->get_error_message() );
				} elseif ( $data ) {
					$cart_item_meta['addons'] = array_merge( $cart_item_meta['addons'], apply_filters( 'woocommerce_product_addon_cart_item_data', $data, $addon, $product_id, $post_data ) );
				}
			}
		}

		return $cart_item_meta;
	}

	/**
	 * validate_add_cart_item function.
	 *
	 * @access public
	 * @param mixed $passed
	 * @param mixed $product_id
	 * @param mixed $qty
	 * @return bool
	 */
	public function validate_add_cart_item( $passed, $product_id, $qty, $post_data = null ) {
		global $woocommerce;

		if ( is_null( $post_data ) ) {
			$post_data = $_POST;
		}

		$product_addons = get_product_addons( $product_id );

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once( 'fields/abstract-class-product-addon-field.php' );

			foreach ( $product_addons as $addon ) {

				$value = isset( $post_data[ 'addon-' . $addon['field-name'] ] ) ? $post_data[ 'addon-' . $addon['field-name'] ] : '';

				if ( is_array( $value ) ) {
					$value = array_map( 'stripslashes', $value );
				} else {
					$value = stripslashes( $value );
				}

				switch ( $addon['type'] ) {
					case "checkbox" :
					case "radiobutton" :
						include_once( 'fields/class-product-addon-field-list.php' );
						$field = new Product_Addon_Field_List( $addon, $value );
					break;
					case "custom" :
					case "custom_textarea" :
					case "custom_price" :
					case "input_multiplier" :
						include_once( 'fields/class-product-addon-field-custom.php' );
						$field = new Product_Addon_Field_Custom( $addon, $value );
					break;
					case "select" :
						include_once( 'fields/class-product-addon-field-select.php' );
						$field = new Product_Addon_Field_Select( $addon, $value );
					break;
					case "file_upload" :
						include_once( 'fields/class-product-addon-field-file-upload.php' );
						$field = new Product_Addon_Field_File_Upload( $addon, $value );
					break;
				}

				$data = $field->validate();

				if ( is_wp_error( $data ) ) {
					$this->add_error( $data->get_error_message() );
					return false;
				}

				do_action( 'woocommerce_validate_posted_addon_data', $addon );
			}
		}

		return $passed;
	}

	/**
	 * Add meta to orders
	 *
	 * @access public
	 * @param mixed $item_id
	 * @param mixed $values
	 * @return void
	 */
	public function order_item_meta( $item_id, $values ) {
		if ( ! empty( $values['addons'] ) ) {
			foreach ( $values['addons'] as $addon ) {

				$name = $addon['name'];

				if ( $addon['price'] > 0 && apply_filters( 'woocommerce_addons_add_price_to_name', true ) ) {
					$name .= ' (' . strip_tags( woocommerce_price( $addon['price'] ) ) . ')';
				}

				woocommerce_add_order_item_meta( $item_id, $name, $addon['value'] );
			}
		}
	}

	/**
	 * Re-order
	 */
	public function re_add_cart_item_data( $cart_item_meta, $product, $order ) {
		global $woocommerce;

		// Disable validation
		remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 3 );

		// Get addon data
		$product_addons = get_product_addons( $product['product_id'] );

		if ( empty( $cart_item_meta['addons'] ) ) {
			$cart_item_meta['addons'] = array();
		}

		if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
			include_once( 'fields/abstract-class-product-addon-field.php' );

			foreach ( $product_addons as $addon ) {
				$value = '';

				switch ( $addon['type'] ) {
					case "checkbox" :
					case "radiobutton" :
						include_once( 'fields/class-product-addon-field-list.php' );

						$value = array();

						foreach ( $product['item_meta'] as $key => $meta ) {
							if ( stripos( $key, $addon['name'] ) === 0 ) {
								$value[] = sanitize_title( $meta[0] );
							}
						}

						if ( empty( $value ) ) {
							continue;
						}

						$field = new Product_Addon_Field_List( $addon, $value );
					break;
					case "select" :
						include_once( 'fields/class-product-addon-field-select.php' );

						$value = '';

						foreach ( $product['item_meta'] as $key => $meta ) {
							if ( stripos( $key, $addon['name'] ) === 0 ) {
								$value = sanitize_title( $meta[0] );
							}
						}

						if ( empty( $value ) ) {
							continue;
						}

						$chosen_option = '';
						$loop          = 0;

						foreach ( $addon['options'] as $option ) {
							$loop++;
							if ( sanitize_title( $option['label'] ) == $value ) {
								$value = $value . '-' . $loop;
								break;
							}
						}

						$field = new Product_Addon_Field_Select( $addon, $value );
					break;
					case "custom" :
					case "custom_textarea" :
					case "custom_price" :
					case "input_multiplier" :
						include_once( 'fields/class-product-addon-field-custom.php' );

						$value = array();

						foreach ( $product['item_meta'] as $key => $meta ) {
							foreach ( $addon['options'] as $option ) {
								if ( stripos( $key, $addon['name'] ) === 0 && stristr( $key, $option['label'] ) ) {
									$value[ sanitize_title( $option['label'] ) ] = $meta[0];
								}
							}
						}

						if ( empty( $value ) ) {
							continue;
						}

						$field = new Product_Addon_Field_Custom( $addon, $value );
					break;
					case "file_upload" :
						include_once( 'fields/class-product-addon-field-file-upload.php' );

						$value = array();

						foreach ( $product['item_meta'] as $key => $meta ) {
							foreach ( $addon['options'] as $option ) {
								if ( stripos( $key, $addon['name'] ) === 0 && stristr( $key, $option['label'] ) ) {
									$value[ sanitize_title( $option['label'] ) ] = $meta[0];
								}
							}
						}

						if ( empty( $value ) ) {
							continue;
						}

						$field = new Product_Addon_Field_File_Upload( $addon, $value );
					break;
				}

				// make sure a field is set (if not it could be product with no add-ons)
				if ( isset( $field ) ) {

					$data = $field->get_cart_item_data();

					if ( is_wp_error( $data ) ) {
						$this->add_error( $data->get_error_message() );
					} elseif ( $data ) {
						// get the post data
						$post_data = $_POST;

						$cart_item_meta['addons'] = array_merge( $cart_item_meta['addons'], apply_filters( 'woocommerce_product_addon_reorder_cart_item_data', $data, $addon, $product['product_id'], $post_data ) );
					}
				}

			}
		}

		return $cart_item_meta;
	}
}

$GLOBALS['Product_Addon_Cart'] = new Product_Addon_Cart();
