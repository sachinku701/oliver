<?php
/**
 * Bundle add-to-cart button template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/add-to-cart/bundle-button.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 4.7.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $product;
do_action( 'woocommerce_before_add_to_cart_button' );
?><button type="submit" class="single_add_to_cart_button bundle_add_to_cart_button button alt productX_addtocard">Add To Cart<span id="productx_price" product_price="<?php echo sprintf("%.2f", $product->get_price()); ?>"><?php echo sprintf("%.2f", $product->get_price()); ?></span></button>
