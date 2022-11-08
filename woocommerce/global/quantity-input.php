<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( $max_value && $min_value === $max_value ) {
    ?>
    <div class="quantity hidden">
        <input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty"
               name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
    </div>
    <?php
}
else {
    if ( $min_value && ( $input_value < $min_value ) ) {
        $input_value = $min_value;
    }
    elseif ( $max_value && ( $input_value > $max_value ) ) {
        $input_value = $max_value;
    }
    elseif ( '' === $input_value ) {
        $input_value = 0;
    }
    ?>
    <div class="qib-button qib-button-wrapper">
        <label class="screen-reader-text"
               for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'wqpmb' ); ?>
        </label>

        <button type="button" class="minus qib-button"><img
                    src="<?php echo plugins_url('oliver-pos/public/resource/img/minus.svg');?>">
        </button>
        <div class="quantity wqpmb_quantity">
            <span>Qty</span>
            <input type="number" id="<?php echo esc_attr( $input_id ); ?>"
                   class="wqpmb_input_text <?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
                   step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>"
                   max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
                   name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>"
                   title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'wqpmb' ); ?>" size="4"
                   placeholder="<?php echo esc_attr( $placeholder ); ?>" inputmode="<?php echo esc_attr( $inputmode ); ?>" />
            <?php do_action( 'woocommerce_after_quantity_input_field' ); ?>
        </div>
        <span class="wqpmb_plain_input hidden"><?php echo esc_html( $input_value ); ?></span>

        <button type="button" class="plus qib-button"><img
                    src="<?php echo plugins_url('oliver-pos/public/resource/img/plus.svg');?>"></button>
    </div>
    <?php
}
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        jQuery(function($) {

            // Make the code work after page load.
            $(document).ready(function() {
                QuantityChange();
            });

            // Make the code work after executing AJAX.
            $(document).ajaxComplete(function() {
                QuantityChange();
            });

            function QuantityChange() {
                $(document).off("click", ".qib-button").on("click", ".qib-button", function() {
                    // Find quantity input field corresponding to increment button clicked.
                    var qty = $(this).siblings(".quantity").find(".qty");
                    // Read value and attributes min, max, step.
                    var val = parseFloat(qty.val());
                    var max = parseFloat(qty.attr("max"));
                    var min = parseFloat(qty.attr("min"));
                    var step = parseFloat(qty.attr("step"));

                    // Change input field value if result is in min and max range.
                    // If the result is above max then change to max and alert user about exceeding max stock.
                    // If the field is empty, fill with min for "-" (0 possible) and step for "+".
                    if ($(this).is(".plus")) {
                        if (val === max)
                            return false;
                        if (isNaN(val)) {
                            qty.val(step);
                            return false;
                        }
                        if (val + step > max) {
                            qty.val(max);
                        } else {
                            qty.val(val + step);
                        }
                    } else {
                        if (val === min)
                            return false;
                        if (isNaN(val)) {
                            qty.val(min);
                            return false;
                        }
                        if (val - step < min) {
                            qty.val(min);
                        } else {
                            qty.val(val - step);
                        }
                    }

                    qty.val(Math.round(qty.val() * 100) / 100);
                    qty.trigger("change");
                });
            }
        });
    });
</script>