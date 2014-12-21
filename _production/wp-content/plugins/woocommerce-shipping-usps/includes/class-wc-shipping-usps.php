<?php
/**
 * WC_Shipping_USPS class.
 *
 * @extends WC_Shipping_Method
 */
class WC_Shipping_USPS extends WC_Shipping_Method {

	private $endpoint        = 'http://production.shippingapis.com/shippingapi.dll';
	//private $endpoint        = 'http://stg-production.shippingapis.com/ShippingApi.dll';
	private $default_user_id = '150WOOTH2143';
	private $domestic        = array( "US", "PR", "VI" );
	private $found_rates;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'usps';
		$this->method_title       = __( 'USPS', 'woocommerce-shipping-usps' );
		$this->method_description = __( 'The <strong>USPS</strong> extension obtains rates dynamically from the USPS API during cart/checkout.', 'woocommerce-shipping-usps' );
		$this->services           = include( 'data/data-services.php' );
		$this->flat_rate_boxes    = include( 'data/data-flat-rate-boxes.php' );
		$this->flat_rate_pricing  = include( 'data/data-flat-rate-box-pricing.php' );
		$this->init();
	}

    /**
     * init function.
     *
     * @access public
     * @return void
     */
    private function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->enabled                  = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : $this->enabled;
		$this->title                    = isset( $this->settings['title'] ) ? $this->settings['title'] : $this->method_title;
		$this->availability             = isset( $this->settings['availability'] ) ? $this->settings['availability'] : 'all';
		$this->countries                = isset( $this->settings['countries'] ) ? $this->settings['countries'] : array();
		$this->origin                   = isset( $this->settings['origin'] ) ? $this->settings['origin'] : '';
		$this->user_id                  = ! empty( $this->settings['user_id'] ) ? $this->settings['user_id'] : $this->default_user_id;
		$this->packing_method           = isset( $this->settings['packing_method'] ) ? $this->settings['packing_method'] : 'per_item';
		$this->boxes                    = isset( $this->settings['boxes'] ) ? $this->settings['boxes'] : array();
		$this->custom_services          = isset( $this->settings['services'] ) ? $this->settings['services'] : array();
		$this->offer_rates              = isset( $this->settings['offer_rates'] ) ? $this->settings['offer_rates'] : 'all';
		$this->fallback                 = ! empty( $this->settings['fallback'] ) ? $this->settings['fallback'] : '';
		$this->flat_rate_fee            = ! empty( $this->settings['flat_rate_fee'] ) ? $this->settings['flat_rate_fee'] : '';
		$this->mediamail_restriction    = isset( $this->settings['mediamail_restriction'] ) ? $this->settings['mediamail_restriction'] : array();
		$this->mediamail_restriction    = array_filter( (array) $this->mediamail_restriction );
		$this->unpacked_item_handling   = ! empty( $this->settings['unpacked_item_handling'] ) ? $this->settings['unpacked_item_handling'] : '';
		$this->enable_standard_services = isset( $this->settings['enable_standard_services'] ) && $this->settings['enable_standard_services'] == 'yes' ? true : false;
		$this->enable_flat_rate_boxes   = isset( $this->settings['enable_flat_rate_boxes'] ) ? $this->settings['enable_flat_rate_boxes'] : 'yes';
		$this->debug                    = isset( $this->settings['debug_mode'] ) && $this->settings['debug_mode'] == 'yes' ? true : false;
		$this->flat_rate_boxes          = apply_filters( 'usps_flat_rate_boxes', $this->flat_rate_boxes );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );
	}

	/**
	 * environment_check function.
	 *
	 * @access public
	 * @return void
	 */
	private function environment_check() {
		global $woocommerce;

		$admin_page = version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ? 'wc-settings' : 'woocommerce_settings';

		if ( get_woocommerce_currency() != "USD" ) {
			echo '<div class="error">
				<p>' . sprintf( __( 'USPS requires that the <a href="%s">currency</a> is set to US Dollars.', 'woocommerce-shipping-usps' ), admin_url( 'admin.php?page=' . $admin_page . '&tab=general' ) ) . '</p>
			</div>';
		}

		elseif ( ! in_array( $woocommerce->countries->get_base_country(), $this->domestic ) ) {
			echo '<div class="error">
				<p>' . sprintf( __( 'USPS requires that the <a href="%s">base country/region</a> is the United States.', 'woocommerce-shipping-usps' ), admin_url( 'admin.php?page=' . $admin_page . '&tab=general' ) ) . '</p>
			</div>';
		}

		elseif ( ! $this->origin && $this->enabled == 'yes' ) {
			echo '<div class="error">
				<p>' . __( 'USPS is enabled, but the origin postcode has not been set.', 'woocommerce-shipping-usps' ) . '</p>
			</div>';
		}
	}

	/**
	 * admin_options function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_options() {
		// Check users environment supports this method
		$this->environment_check();

		// Show settings
		parent::admin_options();
	}

	/**
	 * generate_services_html function.
	 */
	public function generate_services_html() {
		ob_start();
		include( 'views/html-services.php' );
		return ob_get_clean();
	}

	/**
	 * generate_box_packing_html function.
	 */
	public function generate_box_packing_html() {
		ob_start();
		include( 'views/html-box-packing.php' );
		return ob_get_clean();
	}

	/**
	 * validate_box_packing_field function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	public function validate_box_packing_field( $key ) {
		$boxes = array();

		if ( isset( $_POST['boxes_outer_length'] ) ) {
			$boxes_name         = isset( $_POST['boxes_name'] ) ? $_POST['boxes_name'] : array();
			$boxes_outer_length = $_POST['boxes_outer_length'];
			$boxes_outer_width  = $_POST['boxes_outer_width'];
			$boxes_outer_height = $_POST['boxes_outer_height'];
			$boxes_inner_length = $_POST['boxes_inner_length'];
			$boxes_inner_width  = $_POST['boxes_inner_width'];
			$boxes_inner_height = $_POST['boxes_inner_height'];
			$boxes_box_weight   = $_POST['boxes_box_weight'];
			$boxes_max_weight   = $_POST['boxes_max_weight'];
			$boxes_is_letter    = isset( $_POST['boxes_is_letter'] ) ? $_POST['boxes_is_letter'] : array();

			for ( $i = 0; $i < sizeof( $boxes_outer_length ); $i ++ ) {

				if ( $boxes_outer_length[ $i ] && $boxes_outer_width[ $i ] && $boxes_outer_height[ $i ] && $boxes_inner_length[ $i ] && $boxes_inner_width[ $i ] && $boxes_inner_height[ $i ] ) {

					$boxes[] = array(
						'name'         => woocommerce_clean( $boxes_name[ $i ] ),
						'outer_length' => floatval( $boxes_outer_length[ $i ] ),
						'outer_width'  => floatval( $boxes_outer_width[ $i ] ),
						'outer_height' => floatval( $boxes_outer_height[ $i ] ),
						'inner_length' => floatval( $boxes_inner_length[ $i ] ),
						'inner_width'  => floatval( $boxes_inner_width[ $i ] ),
						'inner_height' => floatval( $boxes_inner_height[ $i ] ),
						'box_weight'   => floatval( $boxes_box_weight[ $i ] ),
						'max_weight'   => floatval( $boxes_max_weight[ $i ] ),
						'is_letter'    => isset( $boxes_is_letter[ $i ] ) ? true : false
					);

				}

			}
		}

		return $boxes;
	}

	/**
	 * validate_services_field function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return void
	 */
	public function validate_services_field( $key ) {
		$services         = array();
		$posted_services  = $_POST['usps_service'];

		foreach ( $posted_services as $code => $settings ) {

			$services[ $code ] = array(
				'name'               => woocommerce_clean( $settings['name'] ),
				'order'              => woocommerce_clean( $settings['order'] )
			);

			foreach ( $this->services[$code]['services'] as $key => $name ) {
				$services[ $code ][ $key ]['enabled'] = isset( $settings[ $key ]['enabled'] ) ? true : false;
				$services[ $code ][ $key ]['adjustment'] = woocommerce_clean( $settings[ $key ]['adjustment'] );
				$services[ $code ][ $key ]['adjustment_percent'] = woocommerce_clean( $settings[ $key ]['adjustment_percent'] );
			}

		}

		return $services;
	}

	/**
	 * clear_transients function.
	 *
	 * @access public
	 * @return void
	 */
	public function clear_transients() {
		global $wpdb;

		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_usps_quote_%') OR `option_name` LIKE ('_transient_timeout_usps_quote_%')" );
	}

    /**
     * init_form_fields function.
     *
     * @access public
     * @return void
     */
    public function init_form_fields() {
	    global $woocommerce;

	    $shipping_classes = array();
	    $classes = ( $classes = get_terms( 'product_shipping_class', array( 'hide_empty' => '0' ) ) ) ? $classes : array();

	    foreach ( $classes as $class )
	    	$shipping_classes[ $class->term_id ] = $class->name;

    	$this->form_fields  = array(
			'enabled'          => array(
				'title'           => __( 'Enable/Disable', 'woocommerce-shipping-usps' ),
				'type'            => 'checkbox',
				'label'           => __( 'Enable this shipping method', 'woocommerce-shipping-usps' ),
				'default'         => 'no'
			),
			'title'            => array(
				'title'           => __( 'Method Title', 'woocommerce-shipping-usps' ),
				'type'            => 'text',
				'description'     => __( 'This controls the title which the user sees during checkout.', 'woocommerce-shipping-usps' ),
				'default'         => __( 'USPS', 'woocommerce-shipping-usps' )
			),
			'origin'           => array(
				'title'           => __( 'Origin Postcode', 'woocommerce-shipping-usps' ),
				'type'            => 'text',
				'description'     => __( 'Enter the postcode for the <strong>sender</strong>.', 'woocommerce-shipping-usps' ),
				'default'         => ''
		    ),
		    'availability'  => array(
				'title'           => __( 'Method Availability', 'woocommerce-shipping-usps' ),
				'type'            => 'select',
				'default'         => 'all',
				'class'           => 'availability',
				'options'         => array(
					'all'            => __( 'All Countries', 'woocommerce-shipping-usps' ),
					'specific'       => __( 'Specific Countries', 'woocommerce-shipping-usps' ),
				),
			),
			'countries'        => array(
				'title'           => __( 'Specific Countries', 'woocommerce-shipping-usps' ),
				'type'            => 'multiselect',
				'class'           => 'chosen_select',
				'css'             => 'width: 450px;',
				'default'         => '',
				'options'         => $woocommerce->countries->get_allowed_countries(),
			),
		    'api'           => array(
				'title'           => __( 'API Settings', 'woocommerce-shipping-usps' ),
				'type'            => 'title',
				'description'     => sprintf( __( 'You can obtain a USPS user ID by %s, or just use ours by leaving the field blank. This is optional.', 'woocommerce-shipping-usps' ), '<a href="https://www.usps.com/">' . __( 'signing up on the USPS website', 'woocommerce-shipping-usps' ) . '</a>' ),
		    ),
		    'user_id'           => array(
				'title'           => __( 'USPS User ID', 'woocommerce-shipping-usps' ),
				'type'            => 'text',
				'description'     => __( 'Obtained from USPS after getting an account.', 'woocommerce-shipping-usps' ),
				'default'         => '',
				'placeholder'     => $this->default_user_id
		    ),
		    'debug_mode'  => array(
				'title'           => __( 'Debug Mode', 'woocommerce-shipping-usps' ),
				'label'           => __( 'Enable debug mode', 'woocommerce-shipping-usps' ),
				'type'            => 'checkbox',
				'default'         => 'no',
				'description'     => __( 'Enable debug mode to show debugging information on your cart/checkout.', 'woocommerce-shipping-usps' )
			),
		    'rates'           => array(
				'title'           => __( 'Rates Options', 'woocommerce-shipping-usps' ),
				'type'            => 'title',
				'description'     => __( 'The following settings determine the rates you offer your customers.', 'woocommerce-shipping-usps' ),
		    ),
			'shippingrates'  => array(
				'title'           => __( 'Shipping Rates', 'woocommerce-shipping-usps' ),
				'type'            => 'select',
				'default'         => 'ONLINE',
				'options'         => array(
					'ONLINE'      => __( 'Use ONLINE Rates', 'woocommerce-shipping-usps' ),
					'ALL'         => __( 'Use OFFLINE rates', 'woocommerce-shipping-usps' ),
				),
				'description'     => __( 'Choose which rates to show your customers, ONLINE rates are normally cheaper than OFFLINE', 'woocommerce-shipping-usps' ),
			),
			 'fallback' => array(
				'title'       => __( 'Fallback', 'woocommerce-shipping-usps' ),
				'type'        => 'text',
				'description' => __( 'If USPS returns no matching rates, offer this amount for shipping so that the user can still checkout. Leave blank to disable.', 'woocommerce-shipping-usps' ),
				'default'     => ''
			),
			'flat_rates'           => array(
				'title'           => __( 'Flat Rates', 'woocommerce-shipping-usps' ),
				'type'            => 'title',
		    ),
		    'enable_flat_rate_boxes'  => array(
				'title'           => __( 'Flat Rate Boxes &amp; envelopes', 'woocommerce-shipping-usps' ),
				'type'            => 'select',
				'default'         => 'yes',
				'options'         => array(
					'yes'         => __( 'Yes - Enable flat rate services', 'woocommerce-shipping-usps' ),
					'no'          => __( 'No - Disable flat rate services', 'woocommerce-shipping-usps' ),
					'priority'    => __( 'Enable Priority flat rate services only', 'woocommerce-shipping-usps' ),
					'express'     => __( 'Enable Express flat rate services only', 'woocommerce-shipping-usps' ),
				),
				'description'     => __( 'Enable this option to offer shipping using USPS Flat Rate services. Items will be packed into the boxes/envelopes and the customer will be offered a single rate from these.', 'woocommerce-shipping-usps' )
			),
			'flat_rate_express_title'           => array(
				'title'           => __( 'Express Flat Rate Service Name', 'woocommerce-shipping-usps' ),
				'type'            => 'text',
				'description'     => '',
				'default'         => '',
				'placeholder'     => 'Priority Mail Express Flat Rate&#0174;'
		    ),
		    'flat_rate_priority_title'           => array(
				'title'           => __( 'Priority Flat Rate Service Name', 'woocommerce-shipping-usps' ),
				'type'            => 'text',
				'description'     => '',
				'default'         => '',
				'placeholder'     => 'Priority Mail Flat Rate&#0174;'
		    ),
		    'flat_rate_fee' => array(
				'title' 		=> __( 'Flat Rate Fee', 'woocommerce' ),
				'type' 			=> 'text',
				'description'	=> __( 'Fee per-box excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce' ),
				'default'		=> '',
			),
		    'standard_rates'           => array(
				'title'           => __( 'API Rates', 'woocommerce-shipping-usps' ),
				'type'            => 'title',
		    ),
			'enable_standard_services'  => array(
				'title'           => __( 'Standard Services', 'woocommerce-shipping-usps' ),
				'label'           => __( 'Enable Standard Services from the API', 'woocommerce-shipping-usps' ),
				'type'            => 'checkbox',
				'default'         => 'no',
				'description'     => __( 'Enable non-flat rate services.', 'woocommerce-shipping-usps' )
			),
			'packing_method'  => array(
				'title'           => __( 'Parcel Packing Method', 'woocommerce-shipping-usps' ),
				'type'            => 'select',
				'default'         => '',
				'class'           => 'packing_method',
				'options'         => array(
					'per_item'       => __( 'Default: Pack items individually', 'woocommerce-shipping-usps' ),
					'box_packing'    => __( 'Recommended: Pack into boxes with weights and dimensions', 'woocommerce-shipping-usps' ),
					'weight_based'    => __( 'Weight based: Regular sized items (< 12 inches) are grouped and quoted for weights only. Large items are quoted individually.', 'woocommerce-shipping-usps' ),
				),
			),
			'boxes'  => array(
				'type'            => 'box_packing'
			),
			'unpacked_item_handling'   => array(
				'title'           => __( 'Unpacked item handling', 'woocommerce-shipping-usps' ),
				'type'            => 'select',
				'description'     => '',
				'default'         => 'all',
				'options'         => array(
					''         => __( 'Get a quote for the unpacked item by itself', 'woocommerce-shipping-usps' ),
					'ingore'   => __( 'Ignore the item - do not quote', 'woocommerce-shipping-usps' ),
					'fallback' => __( 'Use the fallback price (above)', 'woocommerce-shipping-usps' ),
					'abort'    => __( 'Abort - do not return any quotes for the standard services', 'woocommerce-shipping-usps' ),
				),
		    ),
			'offer_rates'   => array(
				'title'           => __( 'Offer Rates', 'woocommerce-shipping-usps' ),
				'type'            => 'select',
				'description'     => '',
				'default'         => 'all',
				'options'         => array(
				    'all'         => __( 'Offer the customer all returned rates', 'woocommerce-shipping-usps' ),
				    'cheapest'    => __( 'Offer the customer the cheapest rate only', 'woocommerce-shipping-usps' ),
				),
		    ),
			'services'  => array(
				'type'            => 'services'
			),
			'mediamail_restriction'        => array(
				'title'           => __( 'Restrict Media Mail to...', 'woocommerce-shipping-usps' ),
				'type'            => 'multiselect',
				'class'           => 'chosen_select',
				'css'             => 'width: 450px;',
				'default'         => '',
				'options'         => $shipping_classes,
				'custom_attributes'      => array(
					'data-placeholder' => __( 'No restrictions', 'woocommerce-shipping-usps' ),
				)
			),
		);
    }

    /**
     * calculate_shipping function.
     *
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping( $package ) {
    	global $woocommerce;

		$this->rates               = array();
		$this->unpacked_item_costs = 0;
		$domestic                  = in_array( $package['destination']['country'], $this->domestic ) ? true : false;

    	$this->debug( __( 'USPS debug mode is on - to hide these messages, turn debug mode off in the settings.', 'woocommerce-shipping-usps' ) );

    	if ( $this->enable_standard_services ) {

	    	$package_requests = $this->get_package_requests( $package );
	    	$api              = $domestic ? 'RateV4' : 'IntlRateV2';

	    	libxml_use_internal_errors( true );

	    	if ( $package_requests ) {

	    		$request  = '<' . $api . 'Request USERID="' . $this->user_id . '">' . "\n";
	    		$request .= '<Revision>2</Revision>' . "\n";

	    		foreach ( $package_requests as $key => $package_request ) {
	    			$request .= $package_request;
	    		}

	    		$request .= '</' . $api . 'Request>' . "\n";
	    		$request = 'API=' . $api . '&XML=' . str_replace( array( "\n", "\r" ), '', $request );

	    		$transient       = 'usps_quote_' . md5( $request );
				$cached_response = get_transient( $transient );

				$this->debug( 'USPS REQUEST: <pre>' . print_r( htmlspecialchars( $request ), true ) . '</pre>' );

				if ( $cached_response !== false ) {
					$response = $cached_response;

			    	$this->debug( 'USPS CACHED RESPONSE: <pre style="height: 200px; overflow:auto;">' . print_r( htmlspecialchars( $response ), true ) . '</pre>' );
				} else {
					$response = wp_remote_post( $this->endpoint,
			    		array(
							'timeout'   => 70,
							'sslverify' => 0,
							'body'      => $request
					    )
					);

					if ( is_wp_error( $response ) ) {
		    			$this->debug( 'USPS REQUEST FAILED' );

		    			$response = false;
		    		} else {
			    		$response = $response['body'];

			    		$this->debug( 'USPS RESPONSE: <pre style="height: 200px; overflow:auto;">' . print_r( htmlspecialchars( $response ), true ) . '</pre>' );

						set_transient( $transient, $response, YEAR_IN_SECONDS );
					}
				}

	    		if ( $response ) {

					$xml = simplexml_load_string( '<root>' . preg_replace('/<\?xml.*\?>/', '', $response ) . '</root>' );

					if ( ! $xml ) {
						$this->debug( 'Failed loading XML', 'error' );
					}

					if ( ! empty( $xml->{ $api . 'Response' } ) ) {

						$usps_packages = $xml->{ $api . 'Response' }->children();

						if ( $usps_packages ) {

							$index = 0;

							foreach ( $usps_packages as $usps_package ) {

								// Get package data
								list( $package_item_id, $cart_item_qty, $package_length, $package_width, $package_height, $package_weight ) = explode( ':', $usps_package->attributes()->ID );
								$quotes              = $usps_package->children();

								if ( $this->debug ) {
									$found_quotes = array();

									foreach ( $quotes as $quote ) {
										if ( $domestic ) {
											$code = strval( $quote->attributes()->CLASSID );
											$name = strip_tags( htmlspecialchars_decode( (string) $quote->{'MailService'} ) );
										} else {
											$code = strval( $quote->attributes()->ID );
											$name = strip_tags( htmlspecialchars_decode( (string) $quote->{'SvcDescription'} ) );
										}

										if ( $name && $code ) {
											$found_quotes[ $code ] = $name;
										} elseif ( $name ) {
											$found_quotes[ $code . '-' . sanitize_title( $name ) ] = $name;
										}
									}

									if ( $found_quotes ) {
										ksort( $found_quotes );
										$found_quotes_html = '';
										foreach ( $found_quotes as $code => $name ) {
											if ( ! strstr( $name, "Flat Rate" ) ) {
												$found_quotes_html .= '<li>' . $code . ' - ' . $name . '</li>';
											}
										}
										$this->debug( 'The following quotes were returned by USPS: <ul>' . $found_quotes_html . '</ul> If any of these do not display, they may not be enabled in USPS settings.', 'success' );
									}
								}

								// Loop our known services
								foreach ( $this->services as $service => $values ) {

									if ( $domestic && strpos( $service, 'D_' ) !== 0 ) {
										continue;
									}

									if ( ! $domestic && strpos( $service, 'I_' ) !== 0 ) {
										continue;
									}

									$rate_code = (string) $service;
									$rate_id   = $this->id . ':' . $rate_code;
									$rate_name = (string) $values['name'] . ' (' . $this->title . ')';
									$rate_cost = null;

									foreach ( $quotes as $quote ) {

										if ( $domestic ) {
											$code = strval( $quote->attributes()->CLASSID );
										} else {
											$code = strval( $quote->attributes()->ID );
										}

										if ( $code !== "" && in_array( $code, array_keys( $values['services'] ) ) ) {

											if ( $domestic ) {

												if ( ! empty( $quote->{'CommercialRate'} ) ) {
													$cost = (float) $quote->{'CommercialRate'} * $cart_item_qty;
												} else {
													$cost = (float) $quote->{'Rate'} * $cart_item_qty;
												}

											} else {

												if ( ! empty( $quote->{'CommercialPostage'} ) ) {
													$cost = (float) $quote->{'CommercialPostage'} * $cart_item_qty;
												} else {
													$cost = (float) $quote->{'Postage'} * $cart_item_qty;
												}

											}

											// Cost adjustment %
											if ( ! empty( $this->custom_services[ $rate_code ][ $code ]['adjustment_percent'] ) )
												$cost = $cost + ( $cost * ( floatval( $this->custom_services[ $rate_code ][ $code ]['adjustment_percent'] ) / 100 ) );

											// Cost adjustment
											if ( ! empty( $this->custom_services[ $rate_code ][ $code ]['adjustment'] ) )
												$cost = $cost + floatval( $this->custom_services[ $rate_code ][ $code ]['adjustment'] );

											// Enabled check
											if ( isset( $this->custom_services[ $rate_code ][ $code ] ) && empty( $this->custom_services[ $rate_code ][ $code ]['enabled'] ) )
												continue;

											if ( $domestic ) {
												switch ( $code ) {
													// Handle first class - there are multiple d0 rates and we need to handle size retrictions because the API is lame
													case "0" :
														$service_name = strip_tags( htmlspecialchars_decode( (string) $quote->{'MailService'} ) );

														if ( apply_filters( 'usps_disable_first_class_rate_' . sanitize_title( $service_name ), false) ) {
															continue 2;
														}
													break;
													// Media mail has restrictions - check here
													case "6" :
														if ( sizeof( $this->mediamail_restriction ) > 0 ) {
															$invalid = false;

															foreach ( $package['contents'] as $package_item ) {
																if ( ! in_array( $package_item['data']->get_shipping_class_id(), $this->mediamail_restriction ) ) {
																	$invalid = true;
																}
															}

															if ( $invalid ) {
																$this->debug( 'Skipping media mail' );
															}

															if ( $invalid ) {
																continue 2;
															}
														}
													break;
												}
											}

											if ( $domestic && $package_length && $package_width && $package_height ) {
												switch ( $code ) {
													// Regional rate boxes need additonal checks to deal with USPS's crap API
													case "47" :
														if ( $package_length > 10.125 || $package_width > 7.125 || $package_height > 5 ) {
															continue 2;
														} else {
															// Valid
															break;
														}
														if ( $package_length > 13.0625 || $package_width > 11.0625 || $package_height > 2.5 ) {
															continue 2;
														} else {
															// Valid
															break;
														}
													break;
													case "49" :
														if ( $package_length > 12.25 || $package_width > 10.5 || $package_height > 5.5 ) {
															continue 2;
														} else {
															// Valid
															break;
														}
														if ( $package_length > 16.25 || $package_width > 14.5 || $package_height > 3 ) {
															continue 2;
														} else {
															// Valid
															break;
														}
													break;
													case "58" :
														if ( $package_length > 15 || $package_width > 12 || $package_height > 12 ) {
															continue 2;
														} else {
															// Valid
															break;
														}
													break;
													// Handle first class - there are multiple d0 rates and we need to handle size retrictions because the API is lame
													case "0" :
														$service_name = strip_tags( htmlspecialchars_decode( (string) $quote->{'MailService'} ) );

														if ( strstr( $service_name, 'Postcards' ) ) {

															if ( $package_length > 6 || $package_length < 5 ) {
																continue 2;
															}
															if ( $package_width > 4.25 || $package_width < 3.5 ) {
																continue 2;
															}
															if ( $package_height > 0.016 || $package_height < 0.007 ) {
																continue 2;
															}

														} elseif ( strstr( $service_name, 'Large Envelope' ) ) {

															if ( $package_length > 15 || $package_length < 11.5 ) {
																continue 2;
															}
															if ( $package_width > 12 || $package_width < 6 ) {
																continue 2;
															}
															if ( $package_height > 0.75 || $package_height < 0.25 ) {
																continue 2;
															}

														} elseif ( strstr( $service_name, 'Letter' ) ) {

															if ( $package_length > 11.5 || $package_length < 5 ) {
																continue 2;
															}
															if ( $package_width > 6.125 || $package_width < 3.5 ) {
																continue 2;
															}
															if ( $package_height > 0.25 || $package_height < 0.007 ) {
																continue 2;
															}

														} elseif ( strstr( $service_name, 'Parcel' ) ) {

															$girth = ( $package_width + $package_height ) * 2;

															if ( $girth + $package_length > 108 ) {
																continue 2;
															}

														} else {
															continue 2;
														}
													break;
												}
											}

											if ( is_null( $rate_cost ) ) {
												$rate_cost = $cost;
											} elseif ( $cost < $rate_cost ) {
												$rate_cost = $cost;
											}
										}
									}

									if ( $rate_cost ) {
										$this->prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost );
									}
								}

								$index++;
							}
						}

					} else {
						// No rates
						$this->debug( 'Invalid request; no rates returned', 'error' );
					}
				}
			}

			// Ensure rates were found for all packages
			if ( $this->found_rates ) {
				foreach ( $this->found_rates as $key => $value ) {
					if ( $value['packages'] < sizeof( $package_requests ) ) {
						unset( $this->found_rates[ $key ] );
					}

					if ( $this->unpacked_item_costs ) {
						$this->debug( sprintf( __( 'Adding unpacked item costs to rate %s', 'woocommerce-shipping-usps' ), $key ) );
						$this->found_rates[ $key ]['cost'] += $this->unpacked_item_costs;
					}
				}
			}
		}

		// Flat Rate boxes quote
		if ( $this->enable_flat_rate_boxes == 'yes' || $this->enable_flat_rate_boxes == 'priority' ) {
			// Priority
			$flat_rate = $this->calculate_flat_rate_box_rate( $package, 'priority' );
			if ( $flat_rate )
				$this->found_rates[ $flat_rate['id'] ] = $flat_rate;
		}
		if ( $this->enable_flat_rate_boxes == 'yes' || $this->enable_flat_rate_boxes == 'express' ) {
			// Express
			$flat_rate = $this->calculate_flat_rate_box_rate( $package, 'express' );
			if ( $flat_rate )
				$this->found_rates[ $flat_rate['id'] ] = $flat_rate;
		}

		// Add rates
		if ( $this->found_rates ) {

			if ( $this->offer_rates == 'all' ) {

				uasort( $this->found_rates, array( $this, 'sort_rates' ) );

				foreach ( $this->found_rates as $key => $rate ) {
					$this->add_rate( $rate );
				}

			} else {

				$cheapest_rate = '';

				foreach ( $this->found_rates as $key => $rate ) {
					if ( ! $cheapest_rate || $cheapest_rate['cost'] > $rate['cost'] )
						$cheapest_rate = $rate;
				}

				$cheapest_rate['label'] = $this->title;

				$this->add_rate( $cheapest_rate );

			}

		// Fallback
		} elseif ( $this->fallback ) {
			$this->add_rate( array(
				'id' 	=> $this->id . '_fallback',
				'label' => $this->title,
				'cost' 	=> $this->fallback,
				'sort'  => 0
			) );
		}

    }

    /**
     * prepare_rate function.
     *
     * @access private
     * @param mixed $rate_code
     * @param mixed $rate_id
     * @param mixed $rate_name
     * @param mixed $rate_cost
     * @return void
     */
    private function prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost ) {

	    // Name adjustment
		if ( ! empty( $this->custom_services[ $rate_code ]['name'] ) )
			$rate_name = $this->custom_services[ $rate_code ]['name'];

		// Merging
		if ( isset( $this->found_rates[ $rate_id ] ) ) {
			$rate_cost = $rate_cost + $this->found_rates[ $rate_id ]['cost'];
			$packages  = 1 + $this->found_rates[ $rate_id ]['packages'];
		} else {
			$packages = 1;
		}

		// Sort
		if ( isset( $this->custom_services[ $rate_code ]['order'] ) ) {
			$sort = $this->custom_services[ $rate_code ]['order'];
		} else {
			$sort = 999;
		}

		$this->found_rates[ $rate_id ] = array(
			'id'       => $rate_id,
			'label'    => $rate_name,
			'cost'     => $rate_cost,
			'sort'     => $sort,
			'packages' => $packages
		);
    }

    /**
     * sort_rates function.
     *
     * @access public
     * @param mixed $a
     * @param mixed $b
     * @return void
     */
    public function sort_rates( $a, $b ) {
		if ( $a['sort'] == $b['sort'] ) return 0;
		return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
    }

    /**
     * get_request function.
     *
     * @access private
     * @return void
     */
    private function get_package_requests( $package ) {

	    // Choose selected packing
    	switch ( $this->packing_method ) {
	    	case 'box_packing' :
	    		$requests = $this->box_shipping( $package );
	    	break;
	    	case 'weight_based' :
	    		$requests = $this->weight_based_shipping( $package );
	    	break;
	    	case 'per_item' :
	    	default :
	    		$requests = $this->per_item_shipping( $package );
	    	break;
    	}

    	return $requests;
    }

    /**
     * per_item_shipping function.
     *
     * @access private
     * @param mixed $package
     * @return void
     */
    private function per_item_shipping( $package ) {
	    global $woocommerce;

	    $requests = array();
	    $domestic = in_array( $package['destination']['country'], $this->domestic ) ? true : false;

    	// Get weight of order
    	foreach ( $package['contents'] as $item_id => $values ) {

    		if ( ! $values['data']->needs_shipping() ) {
    			$this->debug( sprintf( __( 'Product # is virtual. Skipping.', 'woocommerce-shipping-usps' ), $item_id ) );
    			continue;
    		}

    		if ( ! $values['data']->get_weight() ) {
	    		$this->debug( sprintf( __( 'Product # is missing weight. Using 1lb.', 'woocommerce-shipping-usps' ), $item_id ) );

	    		$weight = 1;
    		} else {
    			$weight = woocommerce_get_weight( $values['data']->get_weight(), 'lbs' );
    		}

    		$size   = 'REGULAR';

    		if ( $values['data']->length && $values['data']->height && $values['data']->width ) {

				$dimensions = array( woocommerce_get_dimension( $values['data']->length, 'in' ), woocommerce_get_dimension( $values['data']->height, 'in' ), woocommerce_get_dimension( $values['data']->width, 'in' ) );

				sort( $dimensions );

				if ( max( $dimensions ) > 12 ) {
					$size   = 'LARGE';
				}

				$girth = $dimensions[0] + $dimensions[0] + $dimensions[1] + $dimensions[1];
			} else {
				$dimensions = array( 0, 0, 0 );
				$girth      = 0;
			}

			if ( $domestic ) {

				$request  = '<Package ID="' . $this->generate_package_id( $item_id, $values['quantity'], $dimensions[2], $dimensions[1], $dimensions[0], $weight ) . '">' . "\n";
				$request .= '	<Service>' . ( ! $this->settings['shippingrates'] ? 'ONLINE' : $this->settings['shippingrates'] ) . '</Service>' . "\n";
				$request .= '	<ZipOrigination>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</ZipOrigination>' . "\n";
				$request .= '	<ZipDestination>' . strtoupper( substr( $package['destination']['postcode'], 0, 5 ) ) . '</ZipDestination>' . "\n";
				$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
				$request .= '	<Ounces>' . number_format( ( $weight - floor( $weight ) ) * 16, 2 ) . '</Ounces>' . "\n";
				$request .= '	<Container>RECTANGULAR</Container>' . "\n";
				$request .= '	<Size>' . $size . '</Size>' . "\n";
				$request .= '	<Width>' . $dimensions[1] . '</Width>' . "\n";
				$request .= '	<Length>' . $dimensions[2] . '</Length>' . "\n";
				$request .= '	<Height>' . $dimensions[0] . '</Height>' . "\n";
				$request .= '	<Girth>' . round( $girth ) . '</Girth>' . "\n";
				$request .= '	<Machinable>true</Machinable> ' . "\n";
				$request .= '	<ShipDate>' . date( "d-M-Y", ( current_time('timestamp') + (60 * 60 * 24) ) ) . '</ShipDate>' . "\n";
				$request .= '</Package>' . "\n";

			} else {

				$request  = '<Package ID="' . $this->generate_package_id( $item_id, $values['quantity'], $dimensions[2], $dimensions[1], $dimensions[0], $weight ) . '">' . "\n";
				$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
				$request .= '	<Ounces>' . number_format( ( $weight - floor( $weight ) ) * 16, 2 ) . '</Ounces>' . "\n";
				$request .= '	<Machinable>true</Machinable> ' . "\n";
				$request .= '	<MailType>Package</MailType>' . "\n";
				$request .= '	<ValueOfContents>' . $values['data']->get_price() . '</ValueOfContents>' . "\n";
				$request .= '	<Country>' . $this->get_country_name( $package['destination']['country'] ) . '</Country>' . "\n";
				$request .= '	<Container>RECTANGULAR</Container>' . "\n";
				$request .= '	<Size>' . $size . '</Size>' . "\n";
				$request .= '	<Width>' . $dimensions[1] . '</Width>' . "\n";
				$request .= '	<Length>' . $dimensions[2] . '</Length>' . "\n";
				$request .= '	<Height>' . $dimensions[0] . '</Height>' . "\n";
				$request .= '	<Girth>' . round( $girth ) . '</Girth>' . "\n";
				$request .= '	<OriginZip>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</OriginZip>' . "\n";
				$request .= '	<CommercialFlag>' . ( $this->settings['shippingrates'] == "ONLINE" ? 'Y' : 'N' ) . '</CommercialFlag>' . "\n";
				$request .= '</Package>' . "\n";

			}

			$requests[] = $request;
    	}

		return $requests;
    }

    /**
     * Generate shipping request for weights only
     * @param  array $package
     * @return array
     */
    private function weight_based_shipping( $package ) {
    	global $woocommerce;

		$requests                  = array();
		$domestic                  = in_array( $package['destination']['country'], $this->domestic ) ? true : false;
		$total_regular_item_weight = 0;

    	// Add requests for larger items
    	foreach ( $package['contents'] as $item_id => $values ) {

    		if ( ! $values['data']->needs_shipping() ) {
    			$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-usps' ), $item_id ) );
    			continue;
    		}

    		if ( ! $values['data']->get_weight() ) {
	    		$this->debug( sprintf( __( 'Product #%d is missing weight. Using 1lb.', 'woocommerce-shipping-usps' ), $item_id ), 'error' );

	    		$weight = 1;
    		} else {
    			$weight = woocommerce_get_weight( $values['data']->get_weight(), 'lbs' );
    		}

			$dimensions = array( woocommerce_get_dimension( $values['data']->length, 'in' ), woocommerce_get_dimension( $values['data']->height, 'in' ), woocommerce_get_dimension( $values['data']->width, 'in' ) );

			sort( $dimensions );

			if ( max( $dimensions ) <= 12 ) {
				$total_regular_item_weight += ( $weight * $values['quantity'] );
    			continue;
			}

			$girth = $dimensions[0] + $dimensions[0] + $dimensions[1] + $dimensions[1];

			if ( $domestic ) {
				$request  = '<Package ID="' . $this->generate_package_id( $item_id, $values['quantity'], $dimensions[2], $dimensions[1], $dimensions[0], $weight ) . '">' . "\n";
				$request .= '	<Service>' . ( !$this->settings['shippingrates'] ? 'ONLINE' : $this->settings['shippingrates'] ) . '</Service>' . "\n";
				$request .= '	<ZipOrigination>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</ZipOrigination>' . "\n";
				$request .= '	<ZipDestination>' . strtoupper( substr( $package['destination']['postcode'], 0, 5 ) ) . '</ZipDestination>' . "\n";
				$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
				$request .= '	<Ounces>' . number_format( ( $weight - floor( $weight ) ) * 16, 2 ) . '</Ounces>' . "\n";
				$request .= '	<Container>RECTANGULAR</Container>' . "\n";
				$request .= '	<Size>LARGE</Size>' . "\n";
				$request .= '	<Width>' . $dimensions[1] . '</Width>' . "\n";
				$request .= '	<Length>' . $dimensions[2] . '</Length>' . "\n";
				$request .= '	<Height>' . $dimensions[0] . '</Height>' . "\n";
				$request .= '	<Girth>' . round( $girth ) . '</Girth>' . "\n";
				$request .= '	<Machinable>true</Machinable> ' . "\n";
				$request .= '	<ShipDate>' . date( "d-M-Y", ( current_time('timestamp') + (60 * 60 * 24) ) ) . '</ShipDate>' . "\n";
				$request .= '</Package>' . "\n";
			} else {
				$request  = '<Package ID="' . $this->generate_package_id( $item_id, $values['quantity'], $dimensions[2], $dimensions[1], $dimensions[0], $weight ) . '">' . "\n";
				$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
				$request .= '	<Ounces>' . number_format( ( $weight - floor( $weight ) ) * 16, 2 ) . '</Ounces>' . "\n";
				$request .= '	<Machinable>true</Machinable> ' . "\n";
				$request .= '	<MailType>Package</MailType>' . "\n";
				$request .= '	<ValueOfContents>' . $values['data']->get_price() . '</ValueOfContents>' . "\n";
				$request .= '	<Country>' . $this->get_country_name( $package['destination']['country'] ) . '</Country>' . "\n";
				$request .= '	<Container>RECTANGULAR</Container>' . "\n";
				$request .= '	<Size>LARGE</Size>' . "\n";
				$request .= '	<Width>' . $dimensions[1] . '</Width>' . "\n";
				$request .= '	<Length>' . $dimensions[2] . '</Length>' . "\n";
				$request .= '	<Height>' . $dimensions[0] . '</Height>' . "\n";
				$request .= '	<Girth>' . round( $girth ) . '</Girth>' . "\n";
				$request .= '	<OriginZip>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</OriginZip>' . "\n";
				$request .= '	<CommercialFlag>' . ( $this->settings['shippingrates'] == "ONLINE" ? 'Y' : 'N' ) . '</CommercialFlag>' . "\n";
				$request .= '</Package>' . "\n";
			}

			$requests[] = $request;
    	}

    	// Regular package
    	if ( $total_regular_item_weight > 0 ) {
    		$max_package_weight = ( $domestic || $package['destination']['country'] == 'MX' ) ? 70 : 44;
    		$package_weights    = array();

    		$full_packages      = floor( $total_regular_item_weight / $max_package_weight );
    		for ( $i = 0; $i < $full_packages; $i ++ )
    			$package_weights[] = $max_package_weight;

    		if ( $remainder = fmod( $total_regular_item_weight, $max_package_weight ) )
    			$package_weights[] = $remainder;

    		foreach ( $package_weights as $key => $weight ) {
				if ( $domestic ) {
					$request  = '<Package ID="' . $this->generate_package_id( 'regular_' . $key, 1, 0, 0, 0, 0 ) . '">' . "\n";
					$request .= '	<Service>' . ( !$this->settings['shippingrates'] ? 'ONLINE' : $this->settings['shippingrates'] ) . '</Service>' . "\n";
					$request .= '	<ZipOrigination>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</ZipOrigination>' . "\n";
					$request .= '	<ZipDestination>' . strtoupper( substr( $package['destination']['postcode'], 0, 5 ) ) . '</ZipDestination>' . "\n";
					$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
					$request .= '	<Ounces>' . number_format( ( $weight - floor( $weight ) ) * 16, 2 ) . '</Ounces>' . "\n";
					$request .= '	<Container />' . "\n";
					$request .= '	<Size>REGULAR</Size>' . "\n";
					$request .= '	<Machinable>true</Machinable> ' . "\n";
					$request .= '	<ShipDate>' . date( "d-M-Y", ( current_time('timestamp') + (60 * 60 * 24) ) ) . '</ShipDate>' . "\n";
					$request .= '</Package>' . "\n";
				} else {
					$request  = '<Package ID="' . $this->generate_package_id( 'regular_' . $key, 1, 0, 0, 0, 0 ) . '">' . "\n";
					$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
					$request .= '	<Ounces>' . number_format( ( $weight - floor( $weight ) ) * 16, 2 ) . '</Ounces>' . "\n";
					$request .= '	<Machinable>true</Machinable> ' . "\n";
					$request .= '	<MailType>Package</MailType>' . "\n";
					$request .= '	<ValueOfContents>' . $values['data']->get_price() . '</ValueOfContents>' . "\n";
					$request .= '	<Country>' . $this->get_country_name( $package['destination']['country'] ) . '</Country>' . "\n";
					$request .= '	<Container />' . "\n";
					$request .= '	<Size>REGULAR</Size>' . "\n";
					$request .= '	<Width />' . "\n";
					$request .= '	<Length />' . "\n";
					$request .= '	<Height />' . "\n";
					$request .= '	<Girth />' . "\n";
					$request .= '	<OriginZip>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</OriginZip>' . "\n";
					$request .= '	<CommercialFlag>' . ( $this->settings['shippingrates'] == "ONLINE" ? 'Y' : 'N' ) . '</CommercialFlag>' . "\n";
					$request .= '</Package>' . "\n";
				}

				$requests[] = $request;
			}
    	}

		return $requests;
    }

    /**
     * Generate a package ID for the request
     *
     * Contains qty and dimension info so we can look at it again later when it comes back from USPS if needed
     *
     * @return string
     */
    public function generate_package_id( $id, $qty, $l, $w, $h, $w ) {
    	return implode( ':', array( $id, $qty, $l, $w, $h, $w ) );
    }

    /**
     * box_shipping function.
     *
     * @access private
     * @param mixed $package
     * @return void
     */
    private function box_shipping( $package ) {
	    global $woocommerce;

	    $requests = array();
	    $domestic = in_array( $package['destination']['country'], $this->domestic ) ? true : false;

	  	if ( ! class_exists( 'WC_Boxpack' ) ) {
	  		include_once 'box-packer/class-wc-boxpack.php';
	  	}

	    $boxpack = new WC_Boxpack();

	    // Define boxes
		foreach ( $this->boxes as $key => $box ) {

			$newbox = $boxpack->add_box( $box['outer_length'], $box['outer_width'], $box['outer_height'], $box['box_weight'] );

			$newbox->set_id( isset( $box['name'] ) ? $box['name'] : $key );
			$newbox->set_inner_dimensions( $box['inner_length'], $box['inner_width'], $box['inner_height'] );

			if ( $box['max_weight'] ) {
				$newbox->set_max_weight( $box['max_weight'] );
			}
		}

		// Add items
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				continue;
			}

			if ( $values['data']->length && $values['data']->height && $values['data']->width && $values['data']->weight ) {

				$dimensions = array( $values['data']->length, $values['data']->height, $values['data']->width );

			} else {
				$this->debug( sprintf( __( 'Product #%d is missing dimensions. Using 1x1x1.', 'woocommerce-shipping-usps' ), $item_id ), 'error' );

				$dimensions = array( 1, 1, 1 );
			}

			for ( $i = 0; $i < $values['quantity']; $i ++ ) {
				$boxpack->add_item(
					woocommerce_get_dimension( $dimensions[2], 'in' ),
					woocommerce_get_dimension( $dimensions[1], 'in' ),
					woocommerce_get_dimension( $dimensions[0], 'in' ),
					woocommerce_get_weight( $values['data']->get_weight(), 'lbs' ),
					$values['data']->get_price()
				);
			}
		}

		// Pack it
		$boxpack->pack();

		// Get packages
		$box_packages = $boxpack->get_packages();

		foreach ( $box_packages as $key => $box_package ) {

			if ( ! empty( $box_package->unpacked ) ) {
				$this->debug( 'Unpacked Item' );

				switch ( $this->unpacked_item_handling ) {
					case 'fallback' :
						// No request, just a fallback
						$this->unpacked_item_costs += $this->fallback;
						continue;
					break;
					case 'ignore' :
						// No request
						continue;
					break;
					case 'abort' :
						// No requests!
						return false;
					break;
				}
			} else {
				$this->debug( 'Packed ' . $box_package->id );
			}

			$weight     = $box_package->weight;
    		$size       = 'REGULAR';
    		$dimensions = array( $box_package->length, $box_package->width, $box_package->height );

			sort( $dimensions );

			if ( max( $dimensions ) > 12 ) {
				$size   = 'LARGE';
			}

			$girth = $dimensions[0] + $dimensions[0] + $dimensions[1] + $dimensions[1];

			if ( $domestic ) {

				$request  = '<Package ID="' . $this->generate_package_id( $key, 1, $dimensions[2], $dimensions[1], $dimensions[0], $weight ) . '">' . "\n";
				$request .= '	<Service>' . ( ! $this->settings['shippingrates'] ? 'ONLINE' : $this->settings['shippingrates'] ) . '</Service>' . "\n";
				$request .= '	<ZipOrigination>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</ZipOrigination>' . "\n";
				$request .= '	<ZipDestination>' . strtoupper( substr( $package['destination']['postcode'], 0, 5 ) ) . '</ZipDestination>' . "\n";
				$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
				$request .= '	<Ounces>' . number_format( ( $weight - floor( $weight ) ) * 16, 2 ) . '</Ounces>' . "\n";
				$request .= '	<Container>RECTANGULAR</Container>' . "\n";
				$request .= '	<Size>' . $size . '</Size>' . "\n";
				$request .= '	<Width>' . $dimensions[1] . '</Width>' . "\n";
				$request .= '	<Length>' . $dimensions[2] . '</Length>' . "\n";
				$request .= '	<Height>' . $dimensions[0] . '</Height>' . "\n";
				$request .= '	<Girth>' . round( $girth ) . '</Girth>' . "\n";
				$request .= '	<Machinable>true</Machinable> ' . "\n";
				$request .= '	<ShipDate>' . date( "d-M-Y", ( current_time('timestamp') + (60 * 60 * 24) ) ) . '</ShipDate>' . "\n";
				$request .= '</Package>' . "\n";

			} else {

				$request  = '<Package ID="' . $this->generate_package_id( $key, 1, $dimensions[2], $dimensions[1], $dimensions[0], $weight ) . '">' . "\n";
				$request .= '	<Pounds>' . floor( $weight ) . '</Pounds>' . "\n";
				$request .= '	<Ounces>' . number_format( ( $weight - floor( $weight ) ) * 16, 2 ) . '</Ounces>' . "\n";
				$request .= '	<Machinable>true</Machinable> ' . "\n";
				$request .= '	<MailType>' . ( empty( $this->boxes[ $box_package->id ]['is_letter'] ) ? 'PACKAGE' : 'ENVELOPE' ) . '</MailType>' . "\n";
				$request .= '	<ValueOfContents>' . $values['data']->get_price() . '</ValueOfContents>' . "\n";
				$request .= '	<Country>' . $this->get_country_name( $package['destination']['country'] ) . '</Country>' . "\n";
				$request .= '	<Container>RECTANGULAR</Container>' . "\n";
				$request .= '	<Size>' . $size . '</Size>' . "\n";
				$request .= '	<Width>' . $dimensions[1] . '</Width>' . "\n";
				$request .= '	<Length>' . $dimensions[2] . '</Length>' . "\n";
				$request .= '	<Height>' . $dimensions[0] . '</Height>' . "\n";
				$request .= '	<Girth>' . round( $girth ) . '</Girth>' . "\n";
				$request .= '	<OriginZip>' . str_replace( ' ', '', strtoupper( $this->origin ) ) . '</OriginZip>' . "\n";
				$request .= '	<CommercialFlag>' . ( $this->settings['shippingrates'] == "ONLINE" ? 'Y' : 'N' ) . '</CommercialFlag>' . "\n";
				$request .= '</Package>' . "\n";

			}

    		$requests[] = $request;
		}

		return $requests;
    }

    /**
     * get_country_name function.
     *
     * @access private
     * @return void
     */
    private function get_country_name( $code ) {
		$countries = apply_filters( 'usps_countries', array(
			'AF' => __( 'Afghanistan', 'woocommerce-shipping-usps' ),
			'AX' => __( '&#197;land Islands', 'woocommerce-shipping-usps' ),
			'AL' => __( 'Albania', 'woocommerce-shipping-usps' ),
			'DZ' => __( 'Algeria', 'woocommerce-shipping-usps' ),
			'AD' => __( 'Andorra', 'woocommerce-shipping-usps' ),
			'AO' => __( 'Angola', 'woocommerce-shipping-usps' ),
			'AI' => __( 'Anguilla', 'woocommerce-shipping-usps' ),
			'AQ' => __( 'Antarctica', 'woocommerce-shipping-usps' ),
			'AG' => __( 'Antigua and Barbuda', 'woocommerce-shipping-usps' ),
			'AR' => __( 'Argentina', 'woocommerce-shipping-usps' ),
			'AM' => __( 'Armenia', 'woocommerce-shipping-usps' ),
			'AW' => __( 'Aruba', 'woocommerce-shipping-usps' ),
			'AU' => __( 'Australia', 'woocommerce-shipping-usps' ),
			'AT' => __( 'Austria', 'woocommerce-shipping-usps' ),
			'AZ' => __( 'Azerbaijan', 'woocommerce-shipping-usps' ),
			'BS' => __( 'Bahamas', 'woocommerce-shipping-usps' ),
			'BH' => __( 'Bahrain', 'woocommerce-shipping-usps' ),
			'BD' => __( 'Bangladesh', 'woocommerce-shipping-usps' ),
			'BB' => __( 'Barbados', 'woocommerce-shipping-usps' ),
			'BY' => __( 'Belarus', 'woocommerce-shipping-usps' ),
			'BE' => __( 'Belgium', 'woocommerce-shipping-usps' ),
			'PW' => __( 'Belau', 'woocommerce-shipping-usps' ),
			'BZ' => __( 'Belize', 'woocommerce-shipping-usps' ),
			'BJ' => __( 'Benin', 'woocommerce-shipping-usps' ),
			'BM' => __( 'Bermuda', 'woocommerce-shipping-usps' ),
			'BT' => __( 'Bhutan', 'woocommerce-shipping-usps' ),
			'BO' => __( 'Bolivia', 'woocommerce-shipping-usps' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'woocommerce-shipping-usps' ),
			'BA' => __( 'Bosnia and Herzegovina', 'woocommerce-shipping-usps' ),
			'BW' => __( 'Botswana', 'woocommerce-shipping-usps' ),
			'BV' => __( 'Bouvet Island', 'woocommerce-shipping-usps' ),
			'BR' => __( 'Brazil', 'woocommerce-shipping-usps' ),
			'IO' => __( 'British Indian Ocean Territory', 'woocommerce-shipping-usps' ),
			'VG' => __( 'British Virgin Islands', 'woocommerce-shipping-usps' ),
			'BN' => __( 'Brunei', 'woocommerce-shipping-usps' ),
			'BG' => __( 'Bulgaria', 'woocommerce-shipping-usps' ),
			'BF' => __( 'Burkina Faso', 'woocommerce-shipping-usps' ),
			'BI' => __( 'Burundi', 'woocommerce-shipping-usps' ),
			'KH' => __( 'Cambodia', 'woocommerce-shipping-usps' ),
			'CM' => __( 'Cameroon', 'woocommerce-shipping-usps' ),
			'CA' => __( 'Canada', 'woocommerce-shipping-usps' ),
			'CV' => __( 'Cape Verde', 'woocommerce-shipping-usps' ),
			'KY' => __( 'Cayman Islands', 'woocommerce-shipping-usps' ),
			'CF' => __( 'Central African Republic', 'woocommerce-shipping-usps' ),
			'TD' => __( 'Chad', 'woocommerce-shipping-usps' ),
			'CL' => __( 'Chile', 'woocommerce-shipping-usps' ),
			'CN' => __( 'China', 'woocommerce-shipping-usps' ),
			'CX' => __( 'Christmas Island', 'woocommerce-shipping-usps' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'woocommerce-shipping-usps' ),
			'CO' => __( 'Colombia', 'woocommerce-shipping-usps' ),
			'KM' => __( 'Comoros', 'woocommerce-shipping-usps' ),
			'CG' => __( 'Congo (Brazzaville)', 'woocommerce-shipping-usps' ),
			'CD' => __( 'Congo (Kinshasa)', 'woocommerce-shipping-usps' ),
			'CK' => __( 'Cook Islands', 'woocommerce-shipping-usps' ),
			'CR' => __( 'Costa Rica', 'woocommerce-shipping-usps' ),
			'HR' => __( 'Croatia', 'woocommerce-shipping-usps' ),
			'CU' => __( 'Cuba', 'woocommerce-shipping-usps' ),
			'CW' => __( 'Cura&Ccedil;ao', 'woocommerce-shipping-usps' ),
			'CY' => __( 'Cyprus', 'woocommerce-shipping-usps' ),
			'CZ' => __( 'Czech Republic', 'woocommerce-shipping-usps' ),
			'DK' => __( 'Denmark', 'woocommerce-shipping-usps' ),
			'DJ' => __( 'Djibouti', 'woocommerce-shipping-usps' ),
			'DM' => __( 'Dominica', 'woocommerce-shipping-usps' ),
			'DO' => __( 'Dominican Republic', 'woocommerce-shipping-usps' ),
			'EC' => __( 'Ecuador', 'woocommerce-shipping-usps' ),
			'EG' => __( 'Egypt', 'woocommerce-shipping-usps' ),
			'SV' => __( 'El Salvador', 'woocommerce-shipping-usps' ),
			'GQ' => __( 'Equatorial Guinea', 'woocommerce-shipping-usps' ),
			'ER' => __( 'Eritrea', 'woocommerce-shipping-usps' ),
			'EE' => __( 'Estonia', 'woocommerce-shipping-usps' ),
			'ET' => __( 'Ethiopia', 'woocommerce-shipping-usps' ),
			'FK' => __( 'Falkland Islands', 'woocommerce-shipping-usps' ),
			'FO' => __( 'Faroe Islands', 'woocommerce-shipping-usps' ),
			'FJ' => __( 'Fiji', 'woocommerce-shipping-usps' ),
			'FI' => __( 'Finland', 'woocommerce-shipping-usps' ),
			'FR' => __( 'France', 'woocommerce-shipping-usps' ),
			'GF' => __( 'French Guiana', 'woocommerce-shipping-usps' ),
			'PF' => __( 'French Polynesia', 'woocommerce-shipping-usps' ),
			'TF' => __( 'French Southern Territories', 'woocommerce-shipping-usps' ),
			'GA' => __( 'Gabon', 'woocommerce-shipping-usps' ),
			'GM' => __( 'Gambia', 'woocommerce-shipping-usps' ),
			'GE' => __( 'Georgia', 'woocommerce-shipping-usps' ),
			'DE' => __( 'Germany', 'woocommerce-shipping-usps' ),
			'GH' => __( 'Ghana', 'woocommerce-shipping-usps' ),
			'GI' => __( 'Gibraltar', 'woocommerce-shipping-usps' ),
			'GR' => __( 'Greece', 'woocommerce-shipping-usps' ),
			'GL' => __( 'Greenland', 'woocommerce-shipping-usps' ),
			'GD' => __( 'Grenada', 'woocommerce-shipping-usps' ),
			'GP' => __( 'Guadeloupe', 'woocommerce-shipping-usps' ),
			'GT' => __( 'Guatemala', 'woocommerce-shipping-usps' ),
			'GG' => __( 'Guernsey', 'woocommerce-shipping-usps' ),
			'GN' => __( 'Guinea', 'woocommerce-shipping-usps' ),
			'GW' => __( 'Guinea-Bissau', 'woocommerce-shipping-usps' ),
			'GY' => __( 'Guyana', 'woocommerce-shipping-usps' ),
			'HT' => __( 'Haiti', 'woocommerce-shipping-usps' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'woocommerce-shipping-usps' ),
			'HN' => __( 'Honduras', 'woocommerce-shipping-usps' ),
			'HK' => __( 'Hong Kong', 'woocommerce-shipping-usps' ),
			'HU' => __( 'Hungary', 'woocommerce-shipping-usps' ),
			'IS' => __( 'Iceland', 'woocommerce-shipping-usps' ),
			'IN' => __( 'India', 'woocommerce-shipping-usps' ),
			'ID' => __( 'Indonesia', 'woocommerce-shipping-usps' ),
			'IR' => __( 'Iran', 'woocommerce-shipping-usps' ),
			'IQ' => __( 'Iraq', 'woocommerce-shipping-usps' ),
			'IE' => __( 'Ireland', 'woocommerce-shipping-usps' ),
			'IM' => __( 'Isle of Man', 'woocommerce-shipping-usps' ),
			'IL' => __( 'Israel', 'woocommerce-shipping-usps' ),
			'IT' => __( 'Italy', 'woocommerce-shipping-usps' ),
			'CI' => __( 'Ivory Coast', 'woocommerce-shipping-usps' ),
			'JM' => __( 'Jamaica', 'woocommerce-shipping-usps' ),
			'JP' => __( 'Japan', 'woocommerce-shipping-usps' ),
			'JE' => __( 'Jersey', 'woocommerce-shipping-usps' ),
			'JO' => __( 'Jordan', 'woocommerce-shipping-usps' ),
			'KZ' => __( 'Kazakhstan', 'woocommerce-shipping-usps' ),
			'KE' => __( 'Kenya', 'woocommerce-shipping-usps' ),
			'KI' => __( 'Kiribati', 'woocommerce-shipping-usps' ),
			'KW' => __( 'Kuwait', 'woocommerce-shipping-usps' ),
			'KG' => __( 'Kyrgyzstan', 'woocommerce-shipping-usps' ),
			'LA' => __( 'Laos', 'woocommerce-shipping-usps' ),
			'LV' => __( 'Latvia', 'woocommerce-shipping-usps' ),
			'LB' => __( 'Lebanon', 'woocommerce-shipping-usps' ),
			'LS' => __( 'Lesotho', 'woocommerce-shipping-usps' ),
			'LR' => __( 'Liberia', 'woocommerce-shipping-usps' ),
			'LY' => __( 'Libya', 'woocommerce-shipping-usps' ),
			'LI' => __( 'Liechtenstein', 'woocommerce-shipping-usps' ),
			'LT' => __( 'Lithuania', 'woocommerce-shipping-usps' ),
			'LU' => __( 'Luxembourg', 'woocommerce-shipping-usps' ),
			'MO' => __( 'Macao S.A.R., China', 'woocommerce-shipping-usps' ),
			'MK' => __( 'Macedonia', 'woocommerce-shipping-usps' ),
			'MG' => __( 'Madagascar', 'woocommerce-shipping-usps' ),
			'MW' => __( 'Malawi', 'woocommerce-shipping-usps' ),
			'MY' => __( 'Malaysia', 'woocommerce-shipping-usps' ),
			'MV' => __( 'Maldives', 'woocommerce-shipping-usps' ),
			'ML' => __( 'Mali', 'woocommerce-shipping-usps' ),
			'MT' => __( 'Malta', 'woocommerce-shipping-usps' ),
			'MH' => __( 'Marshall Islands', 'woocommerce-shipping-usps' ),
			'MQ' => __( 'Martinique', 'woocommerce-shipping-usps' ),
			'MR' => __( 'Mauritania', 'woocommerce-shipping-usps' ),
			'MU' => __( 'Mauritius', 'woocommerce-shipping-usps' ),
			'YT' => __( 'Mayotte', 'woocommerce-shipping-usps' ),
			'MX' => __( 'Mexico', 'woocommerce-shipping-usps' ),
			'FM' => __( 'Micronesia', 'woocommerce-shipping-usps' ),
			'MD' => __( 'Moldova', 'woocommerce-shipping-usps' ),
			'MC' => __( 'Monaco', 'woocommerce-shipping-usps' ),
			'MN' => __( 'Mongolia', 'woocommerce-shipping-usps' ),
			'ME' => __( 'Montenegro', 'woocommerce-shipping-usps' ),
			'MS' => __( 'Montserrat', 'woocommerce-shipping-usps' ),
			'MA' => __( 'Morocco', 'woocommerce-shipping-usps' ),
			'MZ' => __( 'Mozambique', 'woocommerce-shipping-usps' ),
			'MM' => __( 'Myanmar', 'woocommerce-shipping-usps' ),
			'NA' => __( 'Namibia', 'woocommerce-shipping-usps' ),
			'NR' => __( 'Nauru', 'woocommerce-shipping-usps' ),
			'NP' => __( 'Nepal', 'woocommerce-shipping-usps' ),
			'NL' => __( 'Netherlands', 'woocommerce-shipping-usps' ),
			'AN' => __( 'Netherlands Antilles', 'woocommerce-shipping-usps' ),
			'NC' => __( 'New Caledonia', 'woocommerce-shipping-usps' ),
			'NZ' => __( 'New Zealand', 'woocommerce-shipping-usps' ),
			'NI' => __( 'Nicaragua', 'woocommerce-shipping-usps' ),
			'NE' => __( 'Niger', 'woocommerce-shipping-usps' ),
			'NG' => __( 'Nigeria', 'woocommerce-shipping-usps' ),
			'NU' => __( 'Niue', 'woocommerce-shipping-usps' ),
			'NF' => __( 'Norfolk Island', 'woocommerce-shipping-usps' ),
			'KP' => __( 'North Korea', 'woocommerce-shipping-usps' ),
			'NO' => __( 'Norway', 'woocommerce-shipping-usps' ),
			'OM' => __( 'Oman', 'woocommerce-shipping-usps' ),
			'PK' => __( 'Pakistan', 'woocommerce-shipping-usps' ),
			'PS' => __( 'Palestinian Territory', 'woocommerce-shipping-usps' ),
			'PA' => __( 'Panama', 'woocommerce-shipping-usps' ),
			'PG' => __( 'Papua New Guinea', 'woocommerce-shipping-usps' ),
			'PY' => __( 'Paraguay', 'woocommerce-shipping-usps' ),
			'PE' => __( 'Peru', 'woocommerce-shipping-usps' ),
			'PH' => __( 'Philippines', 'woocommerce-shipping-usps' ),
			'PN' => __( 'Pitcairn', 'woocommerce-shipping-usps' ),
			'PL' => __( 'Poland', 'woocommerce-shipping-usps' ),
			'PT' => __( 'Portugal', 'woocommerce-shipping-usps' ),
			'QA' => __( 'Qatar', 'woocommerce-shipping-usps' ),
			'RE' => __( 'Reunion', 'woocommerce-shipping-usps' ),
			'RO' => __( 'Romania', 'woocommerce-shipping-usps' ),
			'RU' => __( 'Russia', 'woocommerce-shipping-usps' ),
			'RW' => __( 'Rwanda', 'woocommerce-shipping-usps' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'woocommerce-shipping-usps' ),
			'SH' => __( 'Saint Helena', 'woocommerce-shipping-usps' ),
			'KN' => __( 'Saint Kitts and Nevis', 'woocommerce-shipping-usps' ),
			'LC' => __( 'Saint Lucia', 'woocommerce-shipping-usps' ),
			'MF' => __( 'Saint Martin (French part)', 'woocommerce-shipping-usps' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'woocommerce-shipping-usps' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'woocommerce-shipping-usps' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'woocommerce-shipping-usps' ),
			'SM' => __( 'San Marino', 'woocommerce-shipping-usps' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'woocommerce-shipping-usps' ),
			'SA' => __( 'Saudi Arabia', 'woocommerce-shipping-usps' ),
			'SN' => __( 'Senegal', 'woocommerce-shipping-usps' ),
			'RS' => __( 'Serbia', 'woocommerce-shipping-usps' ),
			'SC' => __( 'Seychelles', 'woocommerce-shipping-usps' ),
			'SL' => __( 'Sierra Leone', 'woocommerce-shipping-usps' ),
			'SG' => __( 'Singapore', 'woocommerce-shipping-usps' ),
			'SK' => __( 'Slovakia', 'woocommerce-shipping-usps' ),
			'SI' => __( 'Slovenia', 'woocommerce-shipping-usps' ),
			'SB' => __( 'Solomon Islands', 'woocommerce-shipping-usps' ),
			'SO' => __( 'Somalia', 'woocommerce-shipping-usps' ),
			'ZA' => __( 'South Africa', 'woocommerce-shipping-usps' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'woocommerce-shipping-usps' ),
			'KR' => __( 'South Korea', 'woocommerce-shipping-usps' ),
			'SS' => __( 'South Sudan', 'woocommerce-shipping-usps' ),
			'ES' => __( 'Spain', 'woocommerce-shipping-usps' ),
			'LK' => __( 'Sri Lanka', 'woocommerce-shipping-usps' ),
			'SD' => __( 'Sudan', 'woocommerce-shipping-usps' ),
			'SR' => __( 'Suriname', 'woocommerce-shipping-usps' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'woocommerce-shipping-usps' ),
			'SZ' => __( 'Swaziland', 'woocommerce-shipping-usps' ),
			'SE' => __( 'Sweden', 'woocommerce-shipping-usps' ),
			'CH' => __( 'Switzerland', 'woocommerce-shipping-usps' ),
			'SY' => __( 'Syria', 'woocommerce-shipping-usps' ),
			'TW' => __( 'Taiwan', 'woocommerce-shipping-usps' ),
			'TJ' => __( 'Tajikistan', 'woocommerce-shipping-usps' ),
			'TZ' => __( 'Tanzania', 'woocommerce-shipping-usps' ),
			'TH' => __( 'Thailand', 'woocommerce-shipping-usps' ),
			'TL' => __( 'Timor-Leste', 'woocommerce-shipping-usps' ),
			'TG' => __( 'Togo', 'woocommerce-shipping-usps' ),
			'TK' => __( 'Tokelau', 'woocommerce-shipping-usps' ),
			'TO' => __( 'Tonga', 'woocommerce-shipping-usps' ),
			'TT' => __( 'Trinidad and Tobago', 'woocommerce-shipping-usps' ),
			'TN' => __( 'Tunisia', 'woocommerce-shipping-usps' ),
			'TR' => __( 'Turkey', 'woocommerce-shipping-usps' ),
			'TM' => __( 'Turkmenistan', 'woocommerce-shipping-usps' ),
			'TC' => __( 'Turks and Caicos Islands', 'woocommerce-shipping-usps' ),
			'TV' => __( 'Tuvalu', 'woocommerce-shipping-usps' ),
			'UG' => __( 'Uganda', 'woocommerce-shipping-usps' ),
			'UA' => __( 'Ukraine', 'woocommerce-shipping-usps' ),
			'AE' => __( 'United Arab Emirates', 'woocommerce-shipping-usps' ),
			'GB' => __( 'United Kingdom', 'woocommerce-shipping-usps' ),
			'US' => __( 'United States', 'woocommerce-shipping-usps' ),
			'UY' => __( 'Uruguay', 'woocommerce-shipping-usps' ),
			'UZ' => __( 'Uzbekistan', 'woocommerce-shipping-usps' ),
			'VU' => __( 'Vanuatu', 'woocommerce-shipping-usps' ),
			'VA' => __( 'Vatican', 'woocommerce-shipping-usps' ),
			'VE' => __( 'Venezuela', 'woocommerce-shipping-usps' ),
			'VN' => __( 'Vietnam', 'woocommerce-shipping-usps' ),
			'WF' => __( 'Wallis and Futuna', 'woocommerce-shipping-usps' ),
			'EH' => __( 'Western Sahara', 'woocommerce-shipping-usps' ),
			'WS' => __( 'Western Samoa', 'woocommerce-shipping-usps' ),
			'YE' => __( 'Yemen', 'woocommerce-shipping-usps' ),
			'ZM' => __( 'Zambia', 'woocommerce-shipping-usps' ),
			'ZW' => __( 'Zimbabwe', 'woocommerce' )
		));

	    if ( isset( $countries[ $code ] ) ) {
		    return strtoupper( $countries[ $code ] );
	    } else {
		    return false;
	    }
    }

    /**
     * calculate_flat_rate_box_rate function.
     *
     * @access private
     * @param mixed $package
     * @return void
     */
    private function calculate_flat_rate_box_rate( $package, $box_type = 'priority' ) {
	    global $woocommerce;

	    $cost = 0;

	  	if ( ! class_exists( 'WC_Boxpack' ) )
	  		include_once 'box-packer/class-wc-boxpack.php';

	    $boxpack  = new WC_Boxpack();
	    $domestic = in_array( $package['destination']['country'], $this->domestic ) ? true : false;
	    $added    = array();

	    // Define boxes
		foreach ( $this->flat_rate_boxes as $service_code => $box ) {

			if ( $box['box_type'] != $box_type )
				continue;

			$domestic_service = substr( $service_code, 0, 1 ) == 'd' ? true : false;

			if ( $domestic && $domestic_service || ! $domestic && ! $domestic_service ) {
				$newbox = $boxpack->add_box( $box['length'], $box['width'], $box['height'] );

				$newbox->set_max_weight( $box['weight'] );
				$newbox->set_id( $service_code );

				if ( isset( $box['volume'] ) && method_exists( $newbox, 'set_volume' ) ) {
					$newbox->set_volume( $box['volume'] );
				}

				if ( isset( $box['type'] ) && method_exists( $newbox, 'set_type' ) ) {
					$newbox->set_type( $box['type'] );
				}

				$added[] = $service_code . ' - ' . $box['name'] . ' (' . $box['length'] . 'x' . $box['width'] . 'x' . $box['height'] . ')';
			}
		}

		$this->debug( 'Calculating USPS Flat Rate with boxes: ' . implode( ', ', $added ) );

		// Add items
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() )
				continue;

			if ( $values['data']->length && $values['data']->height && $values['data']->width && $values['data']->weight ) {

				$dimensions = array( $values['data']->length, $values['data']->height, $values['data']->width );

			} else {
				$this->debug( sprintf( __( 'Product #%d is missing dimensions! Using 1x1x1.', 'woocommerce-shipping-usps' ), $item_id ), 'error' );

				$dimensions = array( 1, 1, 1 );
			}

			for ( $i = 0; $i < $values['quantity']; $i ++ ) {
				$boxpack->add_item(
					woocommerce_get_dimension( $dimensions[2], 'in' ),
					woocommerce_get_dimension( $dimensions[1], 'in' ),
					woocommerce_get_dimension( $dimensions[0], 'in' ),
					woocommerce_get_weight( $values['data']->get_weight(), 'lbs' ),
					$values['data']->get_price()
				);
			}
		}

		// Pack it
		$boxpack->pack();

		// Get packages
		$flat_packages = $boxpack->get_packages();

		if ( $flat_packages ) {
			foreach ( $flat_packages as $flat_package ) {

				if ( isset( $this->flat_rate_boxes[ $flat_package->id ] ) ) {

					$this->debug( 'Packed ' . $flat_package->id . ' - ' . $this->flat_rate_boxes[ $flat_package->id ]['name'] );

					// Get pricing
					$box_pricing  = $this->settings['shippingrates'] == 'ONLINE' && isset( $this->flat_rate_pricing[ $flat_package->id ]['online'] ) ? $this->flat_rate_pricing[ $flat_package->id ]['online'] : $this->flat_rate_pricing[ $flat_package->id ]['retail'];

					if ( is_array( $box_pricing ) ) {
						if ( isset( $box_pricing[ $package['destination']['country'] ] ) ) {
							$box_cost = $box_pricing[ $package['destination']['country'] ];
						} else {
							$box_cost = $box_pricing['*'];
						}
					} else {
						$box_cost = $box_pricing;
					}

					// Fees
					if ( ! empty( $this->flat_rate_fee ) ) {
						$sym = substr( $this->flat_rate_fee, 0, 1 );
						$fee = $sym == '-' ? substr( $this->flat_rate_fee, 1 ) : $this->flat_rate_fee;

						if ( strstr( $fee, '%' ) ) {
							$fee = str_replace( '%', '', $fee );

							if ( $sym == '-' )
								$box_cost = $box_cost - ( $box_cost * ( floatval( $fee ) / 100 ) );
							else
								$box_cost = $box_cost + ( $box_cost * ( floatval( $fee ) / 100 ) );
						} else {
							if ( $sym == '-' )
								$box_cost = $box_cost - $fee;
							else
								$box_cost += $fee;
						}

						if ( $box_cost < 0 )
							$box_cost = 0;
					}

					$cost += $box_cost;

				} else {
					return; // no match
				}

			}

			if ( $box_type == 'express' ) {
				$label = ! empty( $this->settings['flat_rate_express_title'] ) ? $this->settings['flat_rate_express_title'] : ( $domestic ? '' : 'International ' ) . 'Priority Mail Express Flat Rate&#0174;';
			} else {
				$label = ! empty( $this->settings['flat_rate_priority_title'] ) ? $this->settings['flat_rate_priority_title'] : ( $domestic ? '' : 'International ' ) . 'Priority Mail Flat Rate&#0174;';
			}

			return array(
				'id' 	=> $this->id . ':flat_rate_box_' . $box_type,
				'label' => $label,
				'cost' 	=> $cost,
				'sort'  => ( $box_type == 'express' ? -1 : -2 )
			);
		}
    }

    public function debug( $message, $type = 'notice' ) {
    	if ( $this->debug ) {
    		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
    			wc_add_notice( $message, $type );
    		} else {
    			global $woocommerce;

    			$woocommerce->add_message( $message );
    		}
		}
    }
}
