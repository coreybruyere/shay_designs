<?php
/**
 * Plugin Name: WooCommerce Print Invoice/Packing list
 * Plugin URI: http://www.woothemes.com/products/print-invoices-packing-lists/
 * Description: Customize and print invoice/packing lists for WooCommerce orders from the WordPress Admin
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com
 * Version: 2.4.4
 * Text Domain: woocommerce-pip
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2011-2014 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Print-Invoice-Packing-List
 * @author    SkyVerge
 * @category  Plugin
 * @copyright Copyright (c) 2011-2014, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

//Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '465de1126817cdfb42d97ebca7eea717', '18666' );

// Check if WooCommerce is active and deactivate extension if it's not
if ( ! is_woocommerce_active() ) {
	return;
}

// WC version check
if ( version_compare( get_option( 'woocommerce_db_version' ), '2.1', '<' ) ) {

	function woocommerce_pip_outdated_version_notice() {

		$message = sprintf(
			__( '%sWooCommerce Print Invoice/Packaging List is inactive.%s This version requires WooCommerce 2.1 or newer. Please %supdate WooCommerce to version 2.1 or newer%s', 'woocommerce-pip' ),
			'<strong>',
			'</strong>',
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'&nbsp;&raquo;</a>'
		);

		echo sprintf( '<div class="error"><p>%s</p></div>', $message );
	}

	add_action( 'admin_notices', 'woocommerce_pip_outdated_version_notice' );

	return;
}

add_action( 'plugins_loaded', 'woocommerce_init_pip', 0 );


/**
 * init_woocommerce_pip function.
 *
 * @access public
 * @return void
 */
function woocommerce_init_pip() {

	register_activation_hook( __FILE__, 'woocommerce_pip_activate' );

	//Add needed action and filter hooks
	add_action( 'init', 'woocommerce_pip_load_translation' );
	add_action( 'woocommerce_admin_order_actions_end', 'woocommerce_pip_alter_order_actions' );
	add_action( 'admin_init', 'woocommerce_pip_window' );
	add_action( 'init', 'woocommerce_pip_client_window' );
	add_action( 'wp_enqueue_scripts', 'woocommerce_pip_client_scripts' );
	add_action( 'admin_menu', 'woocommerce_pip_admin_menu' );
	add_action( 'add_meta_boxes', 'woocommerce_pip_add_box' );
	add_action( 'admin_print_scripts-edit.php', 'woocommerce_pip_scripts' );
	add_action( 'admin_print_scripts-post.php', 'woocommerce_pip_scripts' );
	add_action( 'admin_enqueue_scripts', 'woocommerce_pip_admin_scripts' );
	add_action( 'woocommerce_payment_complete', 'woocommerce_pip_send_email' );
	add_action( 'woocommerce_order_status_on-hold_to_completed', 'woocommerce_pip_send_email' );
	add_action( 'woocommerce_order_status_failed_to_completed', 'woocommerce_pip_send_email' );
	add_action( 'admin_footer', 'woocommerce_pip_bulk_admin_footer', 10 );
	add_action( 'load-edit.php', 'woocommerce_pip_order_bulk_action' );
	add_filter( 'woocommerce_my_account_my_orders_actions', 'woocommerce_pip_my_orders_action', 10, 2 );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woocommerce_pip_action_links' );
	add_action( 'admin_init', 'woocommerce_pip_resend_email' );
	add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', 'woocommerce_pip_remove_subscription_renewal_order_meta', 10, 4 );

}


/**
 * woocommerce_pip_load_translation function.
 *
 * @access public
 * @return void
 */
function woocommerce_pip_load_translation() {

	load_plugin_textdomain( 'woocommerce-pip', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
}


/**
 * woocommerce_pip_activate function.
 *
 * @access public
 * @return void
 */
function woocommerce_pip_activate() {

	if ( ! get_option( 'woocommerce_pip_invoice_start' ) ) {
		update_option( 'woocommerce_pip_invoice_start', '1' );
	}
}


/**
 * woocommerce_pip_action_links function.
 *
 * @access public
 * @param mixed $links
 * @return void
 */
function woocommerce_pip_action_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=woocommerce_pip' ) . '">' . __( 'Settings', 'woocommerce-pip' ) . '</a>',
		'<a href="http://docs.woothemes.com/document/woocommerce-print-invoice-packing-list/">' . __( 'Docs', 'woocommerce-pip' ) . '</a>',
		'<a href="http://support.woothemes.com/">' . __( 'Support', 'woocommerce-pip' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}


/**
 * woocommerce_pip_scripts function.
 *
 * @access public
 * @return void
 */
function woocommerce_pip_scripts() {

	// Version number for scripts
	$version = '2.4.2';
	wp_register_script( 'woocommerce-pip-js', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/js/woocommerce-pip.js', array('jquery'), $version );
	wp_enqueue_script( 'woocommerce-pip-js');
}


/**
 * woocommerce_pip_client_scripts function.
 *
 * @access public
 * @return void
 */
function woocommerce_pip_client_scripts() {

	// Version number for scripts
	$version = '2.4.2';
	wp_register_script( 'woocommerce-pip-client-js', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/js/woocommerce-pip-client.js', array('jquery'), $version, true );
	if (is_page( get_option( 'woocommerce_view_order_page_id' ) ) ) {
		wp_enqueue_script( 'woocommerce-pip-client-js');
	}
}


/**
 * woocommerce_pip_admin_scripts function.
 *
 * @access public
 * @param mixed $hook
 * @return void
 */
function woocommerce_pip_admin_scripts( $hook ) {

	global $pip_settings_page;

	if( $hook != $pip_settings_page ) {
		return;
	}

	// Version number for scripts
	$version = '2.4.2';
	wp_register_script( 'woocommerce-pip-admin-js', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/js/woocommerce-pip-admin.js', array( 'jquery' ), $version );
	wp_register_script( 'woocommerce-pip-validate', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/js/jquery.validate.min.js', array( 'jquery' ), $version );
	wp_enqueue_script( 'woocommerce-pip-admin-js' );
	wp_enqueue_script( 'woocommerce-pip-validate' );
}


/**
 * woocommerce_pip_admin_menu function.
 *
 * @access public
 * @return void
 */
function woocommerce_pip_admin_menu() {

	global $pip_settings_page;
	$pip_settings_page = add_submenu_page('woocommerce', __( 'PIP settings', 'woocommerce-pip' ), __( 'PIP settings', 'woocommerce-pip' ), 'manage_woocommerce', 'woocommerce_pip', 'woocommerce_pip_page' );
}


/**
 * woocommerce_pip_bulk_admin_footer function.
 *
 * @access public
 * @return void
 */
function woocommerce_pip_bulk_admin_footer() {

	global $post_type;

	if ( 'shop_order' == $post_type ) {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('<option>').val('print_invoice').text('<?php _e( 'Print invoice', 'woocommerce-pip' )?>').appendTo("select[name='action']");
			jQuery('<option>').val('print_invoice').text('<?php _e( 'Print invoice', 'woocommerce-pip' )?>').appendTo("select[name='action2']");

			jQuery('<option>').val('print_packing').text('<?php _e( 'Print packing list', 'woocommerce-pip' )?>').appendTo("select[name='action']");
			jQuery('<option>').val('print_packing').text('<?php _e( 'Print packing list', 'woocommerce-pip' )?>').appendTo("select[name='action2']");
		});
		</script>
		<?php
	}
}


/**
 * woocommerce_pip_my_orders_action function.
 *
 * Add HTML invoice button to my orders page so customers can view their invoices.
 * @access public
 * @param mixed $actions
 * @param mixed $order
 * @return void
 */
function woocommerce_pip_my_orders_action( $actions, $order ) {

	if ( in_array( $order->status, array( 'processing', 'completed' ) ) ) {
		$actions[] = array(
			'url'	 => wp_nonce_url( site_url( '?print_pip_invoice=true&post='.$order->id ), 'client-print-pip' ),
			'name' => __( 'View invoice', 'woocommerce-pip' )
		);
	}
	return $actions;
}


/**
 * woocommerce_pip_page function.
 *
 * WordPress Settings Page
 * @access public
 * @return void
 */
function woocommerce_pip_page() {

	//Check the user capabilities
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'woocommerce-pip' ) );
	}
	//Load needed WP resources for media uploader
	wp_enqueue_media();

	//Save the field values
	if ( isset( $_POST['pip_fields_submitted'] ) && $_POST['pip_fields_submitted'] == 'submitted' ) {
		foreach ( $_POST as $key => $value ) {
			if ( $key == 'woocommerce_pip_invoice_start' && isset( $_POST['woocommerce_pip_reset_start'] ) ) {
				if ( $_POST['woocommerce_pip_reset_start'] == 'Yes' ) {
					update_option( $key, ltrim( $value, '0') );
				}
			} elseif ( $key == 'woocommerce_pip_reset_start' ) {
			} else {
				if ( get_option( $key ) != $value ) {
					update_option( $key, $value );
				} else {
					add_option( $key, $value, '', 'no' );
				}
			}
		}
	}
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32">
			<br />
		</div>
		<h2><?php _e( 'WooCommerce - Print invoice/packing list settings', 'woocommerce-pip' ); ?></h2>
		<?php if ( isset( $_POST['pip_fields_submitted'] ) && $_POST['pip_fields_submitted'] == 'submitted' ) { ?>
		<div id="message" class="updated fade"><p><strong><?php _e( 'Your settings have been saved.', 'woocommerce-pip' ); ?></strong></p></div>
		<?php } ?>
		<p><?php _e( 'Change settings for print invoice/packing list.', 'woocommerce-pip' ); ?></p>
		<div id="content">
			 <form method="post" action="" id="pip_settings">
				<input type="hidden" name="pip_fields_submitted" value="submitted">
				<div id="poststuff">
					<div class="postbox">
						<h3 class="hndle"><?php _e( 'Settings', 'woocommerce-pip' ); ?></h3>
						<div class="inside pip-preview">
							 <table class="form-table">
								 <tr>
									<th>
										<label for="woocommerce_pip_company_name"><b><?php _e( 'Company name:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
										<input type="text" name="woocommerce_pip_company_name" class="regular-text" value="<?php echo stripslashes( get_option( 'woocommerce_pip_company_name' ) ); ?>" /><br />
										<span class="description"><?php
											echo __( 'Your custom company name for the print.', 'woocommerce-pip' );
											echo '<br /><strong>' . __( 'Note:', 'woocommerce-pip' ) . '</strong> ';
											echo __( 'Leave blank to not to print a company name.', 'woocommerce-pip' );
										?></span>
									</td>
								</tr>
								<tr>
									<th>
										<label for="woocommerce_pip_logo"><b><?php _e( 'Custom logo:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
										<input id="woocommerce_pip_logo" type="text" size="36" name="woocommerce_pip_logo" value="<?php echo get_option( 'woocommerce_pip_logo' ); ?>" />
										<input id="upload_image_button" type="button" value="<?php _e( 'Upload Image', 'woocommerce-pip' ); ?>" />
										<br />
										<span class="description"><?php
											echo __( 'Your custom logo for the print.', 'woocommerce-pip' );
											echo '<br /><strong>' . __( 'Note:', 'woocommerce-pip' ) . '</strong> ';
											echo __( 'Leave blank to not to use a custom logo.', 'woocommerce-pip' );
										?></span>
									</td>
								</tr>
								<tr>
									<th>
										<label for="woocommerce_pip_company_extra"><b><?php _e( 'Company extra info:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
										<textarea name="woocommerce_pip_company_extra" cols="45" rows="3" class="regular-text"><?php echo stripslashes( get_option( 'woocommerce_pip_company_extra' ) ); ?></textarea><br />
										<span class="description"><?php
											echo __( 'Some extra info that is displayed under company name.', 'woocommerce-pip' );
											echo '<br /><strong>' . __( 'Note:', 'woocommerce-pip' ) . '</strong> ';
											echo __( 'Leave blank to not to print the info.', 'woocommerce-pip' );
										?></span>
									</td>
								</tr>
								<tr>
									<th>
										<label for="woocommerce_pip_return_policy"><b><?php _e( 'Returns Policy, Conditions, etc.:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
										<textarea name="woocommerce_pip_return_policy" cols="45" rows="3" class="regular-text"><?php echo stripslashes( get_option( 'woocommerce_pip_return_policy' ) ); ?></textarea><br />
										<span class="description"><?php
											echo __( 'Here you can add some policies, conditions etc. For example add a returns policy in case the client would like to send back some goods.', 'woocommerce-pip' );
											echo '<br /><strong>' . __( 'Note:', 'woocommerce-pip' ) . '</strong> ';
											echo __( 'Leave blank to not to print any policy.', 'woocommerce-pip' );
										?></span>
									</td>
								</tr>
								<tr>
									<th>
										<label for="woocommerce_pip_footer"><b><?php _e( 'Custom footer:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
										<textarea name="woocommerce_pip_footer" cols="45" rows="3" class="regular-text"><?php echo stripslashes( get_option( 'woocommerce_pip_footer' ) ); ?></textarea><br />
										<span class="description"><?php
											echo __( 'Your custom footer for the print.', 'woocommerce-pip' );
											echo '<br /><strong>' . __( 'Note:', 'woocommerce-pip' ) . '</strong> ';
											echo __( 'Leave blank to not to print a footer.', 'woocommerce-pip' );
										?></span>
									</td>
								</tr>
								<tr>
									<th>
										<label for="woocommerce_pip_invoice_start"><b><?php _e( 'Invoice counter start:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
										<input type="checkbox" id="woocommerce_pip_reset_start" name="woocommerce_pip_reset_start" value="Yes" /> <?php _e( 'Reset invoice numbering', 'woocommerce-pip' ); ?><br />
										<input type="text" readonly="true" id="woocommerce_pip_invoice_start" name="woocommerce_pip_invoice_start" class="regular-text" value="<?php echo wp_kses_stripslashes( get_option( 'woocommerce_pip_invoice_start' ) ); ?>" /><br />
										<span class="description"><?php
											echo __( 'Reset the invoice counter to start your custom position for example 103. Leading zeros will be trimmed. Use prefix instead.', 'woocommerce-pip' );
											echo '<br /><strong>' . __( 'Note:', 'woocommerce-pip' ) . '</strong> ';
											echo __( 'You need to check the checkbox to actually reset the value.', 'woocommerce-pip' );
										?></span>
									</td>
								</tr>
								<tr>
									<th>
										<label for="woocommerce_pip_invoice_prefix"><b><?php _e( 'Invoice numbering prefix:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
										<input type="text" name="woocommerce_pip_invoice_prefix" class="regular-text" value="<?php echo stripslashes( get_option( 'woocommerce_pip_invoice_prefix' ) ); ?>" /><br />
										<span class="description"><?php
											echo __( 'Set your custom prefix for the invoice numbering.', 'woocommerce-pip' );
										?></span>
									</td>
								</tr>
								<tr>
									<th>
										<label for="woocommerce_pip_invoice_suffix"><b><?php _e( 'Invoice numbering suffix:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
										<input type="text" name="woocommerce_pip_invoice_suffix" class="regular-text" value="<?php echo stripslashes( get_option( 'woocommerce_pip_invoice_suffix' ) ); ?>" /><br />
										<span class="description"><?php
											echo __( 'Set your custom suffix for the invoice numbering.', 'woocommerce-pip' );
										?></span>
									</td>
								</tr>
								<tr>
									 <th>
										<label for="preview"><b><?php _e( 'Preview before printing:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
											<?php if ( get_option( 'woocommerce_pip_preview' ) == 'enabled') { ?>
											<input type="radio" name="woocommerce_pip_preview" value="enabled" id="pip-preview" class="input-radio" checked="yes" />
											<label for="woocommerce_pip_preview"><?php _e( 'Enabled', 'woocommerce-pip' ); ?></label><br />
											<input type="radio" name="woocommerce_pip_preview" value="disabled" id="pip-preview" class="input-radio" />
											<label for="woocommerce_pip_preview"><?php _e( 'Disabled', 'woocommerce-pip' ); ?></label><br />
											<?php } else { ?>
											<input type="radio" name="woocommerce_pip_preview" value="enabled" id="pip-preview" class="input-radio" />
											<label for="woocommerce_pip_preview"><?php _e( 'Enabled', 'woocommerce-pip' ); ?></label><br />
											<input type="radio" name="woocommerce_pip_preview" value="disabled" id="pip-preview" class="input-radio" checked="yes" />
											<label for="woocommerce_pip_preview"><?php _e( 'Disabled', 'woocommerce-pip' ); ?></label><br />
											<?php } ?>
									</td>
								</tr>
								<tr>
									 <th>
										<label for="preview"><b><?php _e( 'Send invoice as HTML email:', 'woocommerce-pip' ); ?></b></label>
									</th>
									<td>
											<?php if ( get_option( 'woocommerce_pip_send_email' ) == 'enabled') { ?>
											<input type="radio" name="woocommerce_pip_send_email" value="enabled" id="pip-send-email" class="input-radio" checked="yes" />
											<label for="woocommerce_pip_send_email"><?php _e( 'Enabled', 'woocommerce-pip' ); ?></label><br />
											<input type="radio" name="woocommerce_pip_send_email" value="disabled" id="pip-send-email" class="input-radio" />
											<label for="woocommerce_pip_send_email"><?php _e( 'Disabled', 'woocommerce-pip' ); ?></label><br />
											<?php } else { ?>
											<input type="radio" name="woocommerce_pip_send_email" value="enabled" id="pip-send-email" class="input-radio" />
											<label for="woocommerce_pip_preview"><?php _e( 'Enabled', 'woocommerce-pip' ); ?></label><br />
											<input type="radio" name="woocommerce_pip_send_email" value="disabled" id="pip-send-email" class="input-radio" checked="yes" />
											<label for="woocommerce_pip_send_email"><?php _e( 'Disabled', 'woocommerce-pip' ); ?></label><br />
											<?php } ?>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			 <p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'woocommerce-pip' ); ?>" />
			 </p>
			</form>
		</div>
	</div>
	<?php
}


/**
 * woocommerce_pip_add_box function.
 *
 * Add the meta box on the single order page
 * @access public
 * @return void
 */
function woocommerce_pip_add_box() {

	add_meta_box( 'woocommerce-pip-box', __( 'Print invoice/packing list', 'woocommerce-pip' ), 'woocommerce_pip_create_box_content', 'shop_order', 'side', 'default' );
}


/**
 * woocommerce_pip_create_box_content function.
 *
 * Create the meta box content on the single order page
 * @access public
 * @return void
 */
function woocommerce_pip_create_box_content() {

	global $post_id;

	?>
	<table class="form-table">
		<?php if ( get_post_meta( $post_id, '_pip_invoice_number', true ) ) { ?>
		<tr>
			<td><?php _e( 'Invoice: #', 'woocommerce-pip' ); echo get_post_meta( $post_id, '_pip_invoice_number', true ); ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td><a class="button pip-link" href="<?php echo wp_nonce_url( admin_url( '?print_pip=true&post=' . $post_id . '&type=print_invoice' ), 'print-pip' ); ?>"><?php _e('Print Invoice', 'woocommerce-pip' ); ?></a>
				<a class="button pip-link" href="<?php echo wp_nonce_url( admin_url( '?print_pip=true&post=' . $post_id . '&type=print_packing' ), 'print-pip' ); ?>"><?php _e( 'Print Packing List', 'woocommerce-pip' ); ?></a>
				<?php if ( get_option( 'woocommerce_pip_send_email' ) == 'enabled' ) { ?>
				<a class="button pip-email" href="<?php echo wp_nonce_url( admin_url( '?email_pip=true&post=' . $post_id ), 'email-pip' ); ?>"><?php _e( 'Send Email Invoice', 'woocommerce-pip' ); ?></a>
				<?php } ?>
				</td>
		</tr>
	</table>
	<?php
}


/**
 * woocommerce_pip_alter_order_actions function.
 *
 * Insert buttons to orders page
 * @access public
 * @param mixed $order
 * @return void
 */
function woocommerce_pip_alter_order_actions( $order ) {

			?>
				<a class="button tips pip-link" data-tip="<?php esc_attr_e( 'Print Invoice', 'woocommerce-pip' ); ?>" href="<?php echo wp_nonce_url( admin_url( '?print_pip=true&post='.$order->id.'&type=print_invoice'), 'print-pip' ); ?>"><img src="<?php echo woocommerce_pip_get_plugin_url() . '/assets/images/invoice-icon.png'; ?>" alt="<?php esc_attr_e( 'Print Invoice', 'woocommerce-pip' ); ?>" width="14"></a>
				<a class="button tips pip-link" data-tip="<?php esc_attr_e( 'Print Packing List', 'woocommerce-pip' ); ?>" href="<?php echo wp_nonce_url( admin_url( '?print_pip=true&post=' . $order->id.'&type=print_packing' ), 'print-pip' ); ?>"><img src="<?php echo woocommerce_pip_get_plugin_url() . '/assets/images/packing-list-icon.png'; ?>" alt="<?php esc_attr_e( 'Print Packing List', 'woocommerce-pip' ); ?>" width="14"></a>
			<?php
}


/**
 * woocommerce_pip_order_items_table function.
 *
 * Output items for display
 * @access public
 * @param mixed $order
 * @param mixed $show_price (default: FALSE)
 * @return void
 */
function woocommerce_pip_order_items_table( $order, $show_price = FALSE ) {

	$return = '';

	foreach( $order->get_items() as $item ) {

		// get the product; if this variation or product has been deleted, this will return null...
		$_product = $order->get_product_from_item( $item );

		$sku = $variation = '';

		if ( $_product ) $sku = $_product->get_sku();
		$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );

		// first, is there order item meta avaialble to display?
		$variation = $item_meta->display( true, true );

		if ( ! $variation && $_product && isset( $_product->variation_data ) ) {
			// otherwise (for an order added through the admin) lets display the formatted variation data so we have something to fall back to
			$variation = wc_get_formatted_variation( $_product->variation_data, true );
		}

		if ( $variation ) {
			$variation = '<br/><small>' . $variation . '</small>';
		}

		$return .= '<tr>
			<td style="text-align:left; padding: 3px;">' . $sku . '</td>
			<td style="text-align:left; padding: 3px;">' . apply_filters( 'woocommerce_order_product_title', $item['name'], $_product ) . $variation . '</td>
			<td style="text-align:left; padding: 3px;">'.$item['qty'].'</td>';
		if ( $show_price ) {
			$return .= '<td style="text-align:left; padding: 3px;">';
				if ( $order->display_cart_ex_tax || !$order->prices_include_tax ) {
					$ex_tax_label = ( $order->prices_include_tax ) ? 1 : 0;
					$return .= wc_price( $order->get_line_subtotal( $item ), array( 'ex_tax_label' => $ex_tax_label ) );
				} else {
					$return .= wc_price( $order->get_line_subtotal( $item, TRUE ) );
				}
		$return .= '
			</td>';
		} else {
			$return .= '<td style="text-align:left; padding: 3px;">';
			$return .= ( $_product && $_product->get_weight() ) ? $_product->get_weight() * $item['qty'] . ' ' . get_option( 'woocommerce_weight_unit' ) : __( 'n/a', 'woocommerce-pip' );
			$return .= '</td>';
		}
		$return .= '</tr>';

	}

	$return = apply_filters( 'woocommerce_pip_order_items_table', $return );

	return $return;

}


/**
 * woocommerce_pip_template function.
 *
 * Get template directory
 * @access public
 * @param mixed $type
 * @param mixed $template
 * @return void
 */
function woocommerce_pip_template( $type, $template ) {

	 $templates = array();
	if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'woocommerce/woocommerce-pip-template/' . $template ) ) {
		$templates['uri'] = trailingslashit( get_stylesheet_directory_uri() ) . 'woocommerce/woocommerce-pip-template/';
		$templates['dir'] = trailingslashit( get_stylesheet_directory() ) . 'woocommerce/woocommerce-pip-template/';
	}
	else {
		$templates['uri'] = woocommerce_pip_get_plugin_url() . '/woocommerce-pip-template/';
		$templates['dir'] = woocommerce_pip_get_plugin_path() . '/woocommerce-pip-template/';
	}

	return $templates[$type];
}


/**
 * woocommerce_pip_preview function.
 *
 * Output preview if needed
 * @access public
 * @return void
 */
function woocommerce_pip_preview() {

	if ( get_option( 'woocommerce_pip_preview' ) != 'enabled' ) {
		return 'onload="window.print()"';
	}
}


/**
 * woocommerce_pip_print_logo function.
 *
 * Output logo if needed
 * @access public
 * @return void
 */
function woocommerce_pip_print_logo() {

	if ( get_option( 'woocommerce_pip_logo' ) != '' ) {
		return '<img src="' . get_option( 'woocommerce_pip_logo' ) . '" /><br />';
	}
}


/**
 * woocommerce_pip_print_company_name function.
 *
 * Output company name if needed
 * @access public
 * @return void
 */
function woocommerce_pip_print_company_name() {

	if ( get_option( 'woocommerce_pip_company_name' ) != '' ) {
		return get_option( 'woocommerce_pip_company_name' ) . '<br />';
	}
}


/**
 * woocommerce_pip_print_company_extra function.
 *
 * Output company extra if needed
 * @access public
 * @return void
 */
function woocommerce_pip_print_company_extra() {

	if ( get_option( 'woocommerce_pip_company_extra' ) != '' ) {
		 return nl2br( stripslashes( get_option( 'woocommerce_pip_company_extra' ) ) );
	 }
}


/**
 * woocommerce_pip_print_return_policy function.
 *
 * Output return policy if needed
 * @access public
 * @return void
 */
function woocommerce_pip_print_return_policy() {

	if ( get_option( 'woocommerce_pip_return_policy' ) != '' ) {
		return nl2br( stripslashes( get_option( 'woocommerce_pip_return_policy' ) ) );
	}
}


/**
 * woocommerce_pip_print_footer function.
 *
 * Output footer if needed
 * @access public
 * @return void
 */
function woocommerce_pip_print_footer() {

	if ( get_option( 'woocommerce_pip_footer' ) != '' ) {
		return nl2br( stripslashes( get_option( 'woocommerce_pip_footer' ) ) );
	}
}


/**
 * woocommerce_pip_invoice_number function.
 *
 * Output invoice number if needed
 * @access public
 * @param mixed $order_id
 * @return void
 */
function woocommerce_pip_invoice_number( $order_id ) {

	$invoice_number = get_option( 'woocommerce_pip_invoice_start' );

	if ( add_post_meta( $order_id, '_pip_invoice_number', get_option( 'woocommerce_pip_invoice_prefix' ) . $invoice_number . get_option( 'woocommerce_pip_invoice_suffix' ), true) ) {

		update_option( 'woocommerce_pip_invoice_start', $invoice_number + 1 );

	}

	return get_post_meta( $order_id, '_pip_invoice_number', true );
}


/**
 * woocommerce_pip_order_fees function.
 *
* Output order fees if needed
 * @access public
 * @param mixed $order
 * @return void
 */
function woocommerce_pip_order_fees( $order ) {

	$print_fees = '';
	if ( $fees = $order->get_fees() ) {
		 foreach( $fees as $id => $fee ) {
			 $print_fees .= '<tr><th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
											<th scope="row" style="text-align:right;">' . $fee["name"] . '</th>
											<td style="text-align:left;">' . wc_price( $fee["line_total"] ) . '</td>
											</tr>';
		 }
	}
	return $print_fees;
}

 /**
  * woocommerce_pip_user_access function.
  *
 * Helper function to check access rights.
 * Support for 1.6.6 and 2.0.
  * @access public
  * @return void
  */
function woocommerce_pip_user_access() {

	$access = ( current_user_can( 'edit_shop_orders' ) || current_user_can( 'manage_woocommerce_orders' ) ) ? false : true;
	return $access;
}


/**
 * woocommerce_pip_window function.
 *
 * Function to output the printing window for single item and bulk printing.
 * @access public
 * @return void
 */
function woocommerce_pip_window() {

	if ( isset($_GET['print_pip'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];

		$client = false;
		// Check that current user has needed access rights.
		if ( ! wp_verify_nonce( $nonce, 'print-pip' ) || ! is_user_logged_in() || woocommerce_pip_user_access() ) die( 'You are not allowed to view this page.' );

		// unhook the admin bar to compensate for crappy NextGEN Gallery by Photocrati which causes it to be rendered on our pip windows
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );

		$orders           = explode( ',', $_GET['post'] );
		$action           = $_GET['type'];
		$number_of_orders = count( $orders );
		$order_loop       = 0;

		// Build the output.
		ob_start();
		require_once woocommerce_pip_template( 'dir', 'template-header.php' ) . 'template-header.php';
		$content = ob_get_clean();

		// Loop through all orders (bulk printing).
		foreach ( $orders as $order_id ) {
			$order_loop++;
			$order = new WC_Order( $order_id );
			ob_start();
			include woocommerce_pip_template( 'dir', 'template-body.php' ) . 'template-body.php';
			$content .= ob_get_clean();
			if ( $number_of_orders > 1 && $order_loop < $number_of_orders ) {
				$content .= '<p class="pagebreak"></p>';
			}
		}

		ob_start();
		require_once woocommerce_pip_template( 'dir', 'template-footer.php' ) . 'template-footer.php';
		$content .= ob_get_clean();

		echo $content;
		exit;
	}
}


/**
 * woocommerce_pip_client_window function.
 *
* Function to output the printing window for single item for customers.
 * @access public
 * @return void
 */
function woocommerce_pip_client_window() {
	if ( isset($_GET['print_pip_invoice'] ) && isset( $_GET['post'] ) ) {
		$nonce        = $_REQUEST['_wpnonce'];
		$order_id     = $_GET['post'];
		$order        = new WC_Order( $order_id );
		$current_user = wp_get_current_user();
		$action       = 'print_invoice';
		$client       = true;

		// Check that current user has needed access rights.
		if ( ! wp_verify_nonce( $nonce, 'client-print-pip' ) || ! is_user_logged_in() || $order->user_id != $current_user->ID ) die( 'You are not allowed to view this page.' );

		// unhook the admin bar to compensate for crappy NextGEN Gallery by Photocrati which causes it to be rendered on our pip windows (actually this one might not be needed, are the customers in the admin for this?)
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );

		// Build the output.
		ob_start();
		require_once woocommerce_pip_template( 'dir', 'template-header.php' ) . 'template-header.php';
		$content = ob_get_clean();

		ob_start();
		include woocommerce_pip_template( 'dir', 'template-body.php' ) . 'template-body.php';
		$content .= ob_get_clean();

		ob_start();
		require_once woocommerce_pip_template( 'dir', 'template-footer.php' ) . 'template-footer.php';
		$content .= ob_get_clean();

		echo $content;
		exit;
	}
}


/**
 * woocommerce_pip_order_bulk_action function.
 *
 * Process the new bulk actions for printing invoices and packing lists.
 * @access public
 * @return void
 */
function woocommerce_pip_order_bulk_action() {

	$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
	$action        = $wp_list_table->current_action();

	if ( $action == 'print_invoice' || $action=='print_packing' ) {
		$posts = '';

		foreach( $_REQUEST['post'] as $post_id ) {
			if ( empty( $posts ) ) {
				$posts = $post_id;
			} else {
				$posts .= ','.$post_id;
			}
		}

		$forward = wp_nonce_url( admin_url(), 'print-pip' );
		$forward = add_query_arg( array( 'print_pip' => 'true', 'post' => $posts, 'type' => $action ), $forward );
		wp_redirect( $forward );
		exit();
	}
}


/**
 * woocommerce_pip_resend_email function.
 *
 * @access public
 * @return void
 */
function woocommerce_pip_resend_email() {

	if ( isset($_GET['email_pip'] ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		// Check that current user has needed access rights.
		if ( ! wp_verify_nonce( $nonce, 'email-pip' ) || ! is_user_logged_in() || woocommerce_pip_user_access() ) die( 'You are not allowed to view this page.' );

		woocommerce_pip_send_email( $_GET['post'] );
	}
}


/**
 * woocommerce_pip_send_email function.
 *
 * Function to send invoice as email
 * @access public
 * @param mixed $order_id
 * @return void
 */
function woocommerce_pip_send_email( $order_id ) {
	if ( get_option( 'woocommerce_pip_send_email' ) == 'enabled' ) {
		// Build email information
		$order        = new WC_Order( $order_id );
		$to           = $order->billing_email;
		$subject      = __( 'Order invoice', 'woocommerce-pip' );
		$subject      = '[' . get_bloginfo('name') . '] ' . $subject;
		$attachments  = '';

		// Read the file
		ob_start();
		require_once woocommerce_pip_template( 'dir', 'email-template.php' ) . 'email-template.php';
		$message = ob_get_clean();

		 // Send the mail
		wc_mail( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments );
	}
}




/**
 * Don't copy over plugin-specific order meta when creating a parent renewal order.
 *
 * @since 2.4.3
 * @param array $order_meta_query MySQL query for pulling the metadata
 * @param int $original_order_id Post ID of the order being used to purchased the subscription being renewed
 * @param int $renewal_order_id Post ID of the order created for renewing the subscription
 * @param string $new_order_role The role the renewal order is taking, one of 'parent' or 'child'
 * @return string MySQL meta query for pulling the metadata, excluding data added by this gateway
 */
function woocommerce_pip_remove_subscription_renewal_order_meta( $order_meta_query, $original_order_id, $renewal_order_id, $new_order_role ) {

	// guessing we don't want to carry the pip invoice number meta to either parent or child orders
	$order_meta_query .= " AND `meta_key` NOT IN ( '_pip_invoice_number' )";

	return $order_meta_query;
}


/**
 * woocommerce_pip_get_plugin_url function.
 *
 * Gets the plugin url without a trailing slash, e.g. http://skyverge.com/wp-content/plugins/plugin-directory
 *
 * @since 2.3.1
 * @return string the plugin url
 * @access public
 * @return void
 */
function woocommerce_pip_get_plugin_url() {

	return untrailingslashit( plugins_url( '/', __FILE__ ) );
}


/**
 * Gets the absolute plugin path without a trailing slash, e.g.
 * /path/to/wp-content/plugins/plugin-directory
 *
 * @since 2.3.1
 * @return string plugin path
 */
function woocommerce_pip_get_plugin_path() {

	return untrailingslashit( plugin_dir_path( __FILE__ ) );
}
