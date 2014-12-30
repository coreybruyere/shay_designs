<?php
/**
 * Show messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $messages ) return;
?>

<?php foreach ( $messages as $message ) : ?>
	<div class="woocommerce-message  alert  alert--success  js-error">
		<span class="alert__icon  alert--success__icon  js-hide-error">×</span>
		<span class="alert__msg"><?php echo wp_kses_post( $message ); ?></span>
	</div>
<?php endforeach; ?>
