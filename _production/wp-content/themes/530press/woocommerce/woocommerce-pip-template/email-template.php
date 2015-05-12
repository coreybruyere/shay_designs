<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo get_bloginfo('name'); ?></title>
	<style type="text/css">
		/* Client-specific/Reset Styles */
		#outlook a{padding:0;} /* Force Outlook to provide a "view in browser" button. */
		body{
			width:100% !important; /* Force Hotmail to display emails at full width. */
			-webkit-text-size-adjust:none; /* Prevent Webkit platforms from changing default text sizes. */
			background: #ffffff; /* Remember to set this also inside body tag. */
			margin:0;
			padding:0;
			color:black;
			font:normal 14px/150% Verdana, Arial, Helvetica, sans-serif;
			-webkit-print-color-adjust:exact; }
		#backgroundTable{height:100% !important; margin:0; padding:0; width:100% !important;}
		.datagrid table {
			border-collapse: collapse;
			text-align: left;
			width: 100%; }
		.datagrid {
			font: normal 14px/150% Verdana, Arial, Helvetica, sans-serif;
			background: #fff;
			overflow: hidden;
			border: 1px solid #FFFFFF;
			margin-top: 15px;
			-webkit-print-color-adjust: exact; }
		.datagrid table td, .datagrid table th {
			padding: 3px 4px; }
		.datagrid table thead th {
			color: #000;
			font-size: 14px;
			font-weight: bold;
			-webkit-print-color-adjust: exact; }
		.datagrid table tbody td {
			color: #000000;
			border: 2px solid #FFFFFF;
			font-size: 14px;
			font-weight: normal;
			-webkit-print-color-adjust: exact; }
		.datagrid table tfoot td {
			color: #000000;
			border: 2px solid #FFFFFF;
			font-size: 14px;
			font-weight: normal;
			-webkit-print-color-adjust: exact; }
	</style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background: #ffffff;">
	<?php $order = new WC_Order($order_id); ?>
	<center style="padding: 70px 0 0 0;">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="600" style="-webkit-box-shadow:0 0 0 3px rgba(0,0,0,0.025); -webkit-border-radius:6px;background-color: #fff">
						<tr>
							<td>
								<table border="0" cellpadding="0" cellspacing="0" width="600" style="-webkit-border-top-left-radius:6px; -webkit-border-top-right-radius:6px; color:#ffffff; font-family:Arial; font-weight:bold; line-height:100%; vertical-align:middle; background-color:#557da1; -webkit-box-shadow:0 0 0 3px rgba(0,0,0,0.025);">
									<tr>
										<td style="padding:24px;">
											<h1 class="h1" style="color: #fff !important; margin:0; text-shadow:0 1px 0 #7797b4;"><?php _e('Invoice', 'woocommerce-pip'); ?> (<?php echo woocommerce_pip_invoice_number($order_id); ?>)</h1>
										</td>
									</tr>
								</table>
								<table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#fff;">
									<tr>
										<td valign="top" style="padding:24px;">
											<?php echo woocommerce_pip_print_logo(); ?>
											<?php _e('Invoice', 'woocommerce-pip'); ?> (<?php echo woocommerce_pip_invoice_number($order_id); ?>)<br />
											<?php _e('Order', 'woocommerce-pip'); ?> <?php echo $order->get_order_number(); ?> &mdash; <time datetime="<?php echo date("Y/m/d", strtotime($order->order_date)); ?>"><?php echo date("Y/m/d", strtotime($order->order_date)); ?></time>
										</td>
										<td valign="top" align="right" style="padding:24px;">
											<?php echo woocommerce_pip_print_company_name(); ?>
											<?php echo woocommerce_pip_print_company_extra(); ?>
										</td>
									</tr>
								</table>
								<table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #fff;">
									<tr>
										<td valign="top" style="padding:24px;">
											<h3><?php _e('Billing address', 'woocommerce-pip'); ?></h3>
											<p>
												<?php echo $order->get_formatted_billing_address(); ?>
											</p>
											<?php if (get_post_meta($order->id, 'VAT Number', TRUE)) : ?>
												<p><strong><?php _e('VAT:', 'woocommerce-pip'); ?></strong> <?php echo get_post_meta($order->id, 'VAT Number', TRUE); ?></p>
											<?php endif; ?>
											<?php if ($order->billing_email) : ?>
												<p><strong><?php _e('Email:', 'woocommerce-pip'); ?></strong> <?php echo $order->billing_email; ?></p>
											<?php endif; ?>
											<?php if ($order->billing_phone) : ?>
												<p><strong><?php _e('Tel:', 'woocommerce-pip'); ?></strong> <?php echo $order->billing_phone; ?></p>
											<?php endif; ?>
										</td>
										<td valign="top" style="padding:24px;">
											<h3><?php _e('Shipping address', 'woocommerce-pip'); ?></h3>
											<p><?php echo $order->get_formatted_shipping_address(); ?></p>
										</td>
									</tr>
								</table>
								<table border="0" cellpadding="0" cellspacing="0" width="600"style="background-color: #fff">
									<?php if ($order->customer_note) { ?>
									<tr>
										<td style="padding:24px;">
											<h3><?php _e('Order notes', 'woocommerce-pip'); ?></h3>
											<?php echo $order->customer_note; ?>
										</td>
									</tr>
									<?php } ?>
									<tr>
										<td style="padding:24px;">
											<table border="0" cellpadding="0" cellspacing="0" width="552">
												<thead>
													<tr>
														<th scope="col" style="color: #fff; border: 1px solid #FFFFFF; text-align:left; width: 15%; background-color:#557da1; padding: 3px;"><?php _e('SKU', 'woocommerce-pip'); ?></th>
														<th scope="col" style="color: #fff; border: 1px solid #FFFFFF; text-align:left; width: 40%; background-color:#557da1; padding: 3px;"><?php _e('Product', 'woocommerce-pip'); ?></th>
														<th scope="col" style="color: #fff; border: 1px solid #FFFFFF; text-align:left; width: 25%; background-color:#557da1; padding: 3px;"><?php _e('Quantity', 'woocommerce-pip'); ?></th>
														<th scope="col" style="color: #fff; border: 1px solid #FFFFFF; text-align:left; width: 20%; background-color:#557da1; padding: 3px;"><?php _e('Price', 'woocommerce-pip'); ?></th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
														<th scope="row" style="text-align:right; padding-top: 12px; padding-right: 10px;"><?php _e('Subtotal:', 'woocommerce-pip'); ?></th>
														<td style="text-align:left; padding-top: 12px;"><?php echo $order->get_subtotal_to_display(); ?></td>
													</tr>
													<tr>
														<th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
														<th scope="row" style="text-align:right; padding-right: 10px;"><?php _e('Shipping:', 'woocommerce-pip'); ?></th>
														<td style="text-align:left;"><?php echo $order->get_shipping_to_display(); ?></td>
													</tr>
													<?php echo woocommerce_pip_order_fees($order); ?>
													<?php if ($order->cart_discount > 0) : ?><tr>
														<th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
														<th scope="row" style="text-align:right;"><?php _e('Cart Discount:', 'woocommerce-pip'); ?></th>
														<td style="text-align:left;"><?php echo woocommerce_price($order->cart_discount); ?></td>
													</tr><?php endif; ?>
													<?php if ($order->order_discount > 0) : ?><tr>
														<th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
														<th scope="row" style="text-align:right;"><?php _e('Order Discount:', 'woocommerce-pip'); ?></th>
														<td style="text-align:left;"><?php echo woocommerce_price($order->order_discount); ?></td>
													</tr><?php endif; ?>
													<tr>
														<th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
														<th scope="row" style="text-align:right; padding-right: 10px;"><?php _e('Tax:', 'woocommerce-pip'); ?></th>
														<td style="text-align:left;"><?php echo woocommerce_price($order->get_total_tax()); ?></td>
													</tr>
													<tr>
														<th colspan="2" style="text-align:left; padding-top: 12px;">&nbsp;</th>
														<th scope="row" style="text-align:right; padding-right: 10px;"><?php _e('Total:', 'woocommerce-pip'); ?></th>
														<td style="text-align:left;"><?php echo woocommerce_price($order->order_total); ?></td>
													</tr>
												</tfoot>
												<tbody>
													<?php echo woocommerce_pip_order_items_table($order, TRUE); ?>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td style="padding:24px;"><?php echo woocommerce_pip_print_return_policy(); ?></td>
									</tr>
									<tr>
										<td style="padding:24px;">
											<center>
											<?php echo woocommerce_pip_print_footer(); ?>
											</center>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</table>
	</center>
</body>
</html>
