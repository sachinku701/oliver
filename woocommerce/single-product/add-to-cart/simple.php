<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;
global $product;

if ( ! $product->is_purchasable() ) {
    return;
}
echo wc_get_stock_html( $product ); // WPCS: XSS ok.
include dirname( __FILE__ ) . '/op_woo_header.php';
if ( $product->is_in_stock() ) : ?>
    <form class="cart cst-simple-form" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype='multipart/form-data'>
        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
        <?php
        do_action( 'woocommerce_before_add_to_cart_quantity' );

        woocommerce_quantity_input(
            array(
                'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
            )
        );

        do_action( 'woocommerce_after_add_to_cart_quantity' );?>
        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt productX_addtocard ">
            Add To Cart
            <span id="productx_price" class="addonsprice" product_price="<?php echo sprintf("%.2f", $product->get_price()); ?>"><?php echo sprintf("%.2f", $product->get_price()); ?>
			</span>
        </button>
        <input type="hidden" name="add_product_discount" id="add_product_discount" value="0">
        <input type="hidden" name="discount_type" id="discount_type" value="Number">
    </form>
    </div>
    </div>
    </div>
<?php endif; ?>
    <script>
        jQuery(function($) {
            $('[name=quantity]').on('change', function() {
                var price = $('.wc-pao-subtotal-line span').text().replace(/[^\d\.]/g, '');
                if (price != "undefined" && price != '' && price != null) {
                    $('.addonsprice').hide();
                }
            });
        });
    </script>
<?php include dirname( __FILE__ ) . '/op_woo_footer.php';?>