<?php
/**
 * Output a single payment method
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="payment_method_<?php echo $gateway->id; ?>">
	<input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="input-radio input--radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

	<label for="payment_method_<?php echo $gateway->id; ?>">
		<?php echo $gateway->get_title(); ?> 
		<?php $gate_name = $gateway->id; ?>
		<?php if ($gate_name == 'paypal'): ?>
			<div class="pf--box">
				<div class="pf--box__title bg">
					<span aria-hidden="true" class="pf pf-paypal"></span>
				</div>
				<span aria-hidden="true" class="pf pf-visa"></span>
				<span aria-hidden="true" class="pf pf-mastercard"></span>
				<span aria-hidden="true" class="pf pf-american-express"></span>
				<span aria-hidden="true" class="pf pf-discover"></span>
				<span aria-hidden="true" class="pf pf-jcb"></span>
				<span aria-hidden="true" class="pf pf-diners"></span>
				<span aria-hidden="true" class="pf pf-paypal"></span>
			</div>
		<?php elseif ($gate_name == 'stripe'): ?>
			<div class="pf--box">
				<div class="pf--box__title bg">
					<span aria-hidden="true" class="pf pf-stripe"></span> 
				</div>
				<span aria-hidden="true" class="pf pf-visa"></span>
				<span aria-hidden="true" class="pf pf-mastercard"></span>
				<span aria-hidden="true" class="pf pf-american-express"></span>
				<span aria-hidden="true" class="pf pf-jcb"></span>
				<span aria-hidden="true" class="pf pf-discover"></span>
				<span aria-hidden="true" class="pf pf-diners"></span>
			</div>
		<?php endif; ?>
		<?php //echo $gateway->get_icon(); ?>
	</label>
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo $gateway->id; ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
