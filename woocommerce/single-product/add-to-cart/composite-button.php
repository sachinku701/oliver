<?php
/**
 * Composite add-to-cart button template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/add-to-cart/composite-button.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 2.5.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $product;
global $woocommerce;
//do_action( 'woocommerce_before_add_to_cart_button' );
?>
<button type="submit" class="single_add_to_cart_button composite_add_to_cart_button button alt productX_addtocard ">
    <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
    <span id="productx_price" product_price="<?php echo sprintf("%.2f", $product->get_composite_price_data()); ?>">
		<div class="oliver_compositive">
			<div class="composite_price"></div>
		</div>
		<?php echo sprintf('<div id="cart_total_price" style="display:none">%s %s</div>',__('','woocommerce'),'<div class="composite_price"><p class="price"><span class="woocommerce-Price-amount amount"></p></div>');
        ?>
	</span>
</button>

<style>

    #cart_total_price .price{
        margin: 0;
    }
    #cart_total_price .price .woocommerce-Price-amount{
        margin: 0;
    }
    .woocommerce-Price-currencySymbol{
        display:none;
    }
    .composite_form .composite_price p.price, .single-product .composite_form .composite_price p.price {
        margin: 6px 0 14px !important;
        line-height: 1em !important;;
    }
    .amount {
        color: #fff;
        font-size: 22px;
    }
</style>
