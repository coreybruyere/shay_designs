<?php
/**
 * Show error messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $messages ) return;
?>
<ul class="woocommerce-error error alert js-error">
	<li><span class="alert__icon  js-hide-error">×</span></li>
	<?php foreach ( $messages as $message ) : ?>
		<li><?php echo wp_kses_post( $message ); ?></li>
	<?php endforeach; ?>
</ul>