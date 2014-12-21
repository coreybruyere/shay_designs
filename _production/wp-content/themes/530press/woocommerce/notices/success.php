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
	<div class="woocommerce-message  alert--success  js-error"><?php echo wp_kses_post( $message ); ?><span class="alert--success__icon  js-hide-error">×</span></div>
<?php endforeach; ?>
