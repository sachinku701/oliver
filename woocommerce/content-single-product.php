<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
global $woocommerce;
/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
$close_img= plugin_dir_url( dirname( __FILE__ ) ).'public/resource/img/close.svg';
?>
<div class="oliver_productx_container">
    <div class="modal-header">
        <h5 class="modal-title" id="modalLargeLabel" title="<?php the_title();?>">
            <div><?php the_title();?></div>
        </h5>
        <button type="button" class="close close_child_window" data-dismiss="modal" aria-label="Close">
            <img src="<?php echo $close_img;?>" alt="close_img" class="productx_close_img">
        </button>
    </div>
    <?php
    //product img
    $product_image = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail' );
    if(empty($product_image[0])){
        $pro_img= plugin_dir_url( dirname( __FILE__ ) ).'public/resource/img/woocommerce-placeholder-416x416.png';
    }
    else{
        $pro_img= $product_image[0];
    }
    //quantity
    if($product->get_stock_quantity()){
        $stock_status = $product->get_stock_quantity();
    }
    elseif ( method_exists( $product, 'get_stock_status' ) ) {
        $stock_status = $product->get_stock_status(); // For version 3.0+
    } else {
        $stock_status = $product->stock_status; // Older than version 3.0
    }
    ?>
    <?php
    if($stock_status !== 'outofstock')
    {?>
        <div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'ProductX-fir-div', $product ); ?>><span
                    id="productx-show-error"></span>

            <?php
            /**
             * Hook: woocommerce_before_single_product_summary.
             *
             * @hooked woocommerce_show_product_sale_flash - 10
             * @hooked woocommerce_show_product_images - 20
             */
            do_action( 'woocommerce_before_single_product_summary' );
            ?>

            <div class="summary entry-summary oliver-pos-entry-summary">
                <?php
                /**
                 * Hook: woocommerce_single_product_summary.
                 *
                 * @hooked woocommerce_template_single_title - 5
                 * @hooked woocommerce_template_single_rating - 10
                 * @hooked woocommerce_template_single_price - 10
                 * @hooked woocommerce_template_single_excerpt - 20
                 * @hooked woocommerce_template_single_add_to_cart - 30
                 * @hooked woocommerce_template_single_meta - 40
                 * @hooked woocommerce_template_single_sharing - 50
                 * @hooked WC_Structured_Data::generate_product_data() - 60
                 */
                do_action( 'woocommerce_single_product_summary' );
                ?>
            </div>

            <?php
            /**
             * Hook: woocommerce_after_single_product_summary.
             *
             * @hooked woocommerce_output_product_data_tabs - 10
             * @hooked woocommerce_upsell_display - 15
             * @hooked woocommerce_output_related_products - 20
             */
            do_action( 'woocommerce_after_single_product_summary' );
            ?>
        </div>
        <?php
    } ?>
    <div class="vproductx_product_parent">
        <div class="vproductx_product">
            <div class="vproductx_product_header">
                <div class="vproductx_product_img">
                    <img src="<?php echo $pro_img; ?>" id="prdImg">
                </div>
            </div>
            <div class="vproductx_product_body">
                <h5 class="vproductx_product_title">APPS</h5>
                <p class="vproductx_product_subtitle">
                    Apps are not support in advance products.
                </p>
            </div>
            <div class="vproductx_product_footer vproductx_product_footer2">
                <div class="vproductx_product_row">
                    <div class="vproductx_product_font1">Inventory</div>
                    <div class="vproductx_product_font2">
						<span id="oliver_inventry_value">
							<?php echo ucfirst($stock_status);?>
						</span>
                    </div>
                </div>
                <div class="vproductx_product_row vproductx_product_footer">
                    <div class="vproductx_product_font1">Discount</div>
                    <div class="vproductx_product_font2">
                        <span onclick="openModal()" id="disprice">ADD</span>
                    </div>
                </div>
            </div>
            <?php
            if($stock_status == 'outofstock')
            {?>
                <div class="vproductx_product_row vproductx_product_background">
                    <div>Out of Stock</div>
                    <div class="vproductx_product_right"></div>
                </div>
            <?php } ?>
        </div>
        <!-- end stock quantity check -->
        <!-- Modal start -->
        <div id="pos-modal-window" class="shadow">
            <div>
                <div class="main-modal">
                    <div class="productx_popup">
                        <div class="productx_popup_header">
                            Add Discount <span title="<?php the_title();?>">( <?php the_title();?> )</span>
                            <img onclick="closeModel()" src="<?php echo $close_img;?>" alt="close_img">
                        </div>
                        <div class="productx_popup_body">
                            <div class="productx_grid">
                                <div class="productx_grid_items_25">
                                    <div class="productx_grid_items_padding" id="productx_grid_items_padding">

                                        <script>
                                            var queryString = window.location.search;
                                            var urlParams = new URLSearchParams(queryString);

                                            var product = urlParams.get('discountList')
                                            str = product.substring(0, product.length - 1);

                                            var testd = JSON.parse(str);
                                            var appendhtml = '';
                                            var offertypeSymbol = '<?php echo get_woocommerce_currency_symbol();?>';
                                            testd.forEach(function(entry) {
                                                appendhtml += '<div offertypeSymbol="' + offertypeSymbol +
                                                    '"  class="productx_radio predefine_diss productx_radio_sm" discount_type="' +
                                                    entry.Type + '" discount_offer="' + entry.Amount +
                                                    '" id="predefine_diss"><input type="radio" name="radio-single"><span class="checkmark"><span class="checkmark-text">' +
                                                    entry.Name + '(' + (entry.Type == "Number" ? offertypeSymbol :
                                                        '%') + entry.Amount + ')</span></span></div>';
                                            });
                                            document.getElementById('productx_grid_items_padding').innerHTML = appendhtml;
                                        </script>

                                        <div class="clear_discount productx_radio productx_radio_sm">
                                            <input type="radio" name="radio-single">
                                            <span class="checkmark">
												<span class="checkmark-text">Clear Discount</span>
											</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="productx_grid_items_75">
                                    <div class="productx_calculator">
                                        <table>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="productx_grid">
                                                        <div class="productx_grid_items_75">
                                                            <input type="text" id="productx_link_input"
                                                                   class="productx_link_input" name="productx_link_input"
                                                                   value=0>
                                                        </div>
                                                        <div class="productx_grid_items_25">
                                                            <div id="productx_discount_sign" class='productx_discount_sign'>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $delete_img= plugin_dir_url( dirname( __FILE__ ) ).'public/resource/img/LogoutRoundedLeft.svg';
                                                    ?>
                                                    <img id="deletedigt" onclick='deletenumber()'
                                                         src="<?php echo $delete_img ;?>" alt="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td onclick='get_discount_val(1)'>1</td>
                                                <td onclick='get_discount_val(2)'>2</td>
                                                <td onclick='get_discount_val(3)'>3</td>
                                            </tr>
                                            <tr>
                                                <td onclick='get_discount_val(4)'>4</td>
                                                <td onclick='get_discount_val(5)'>5</td>
                                                <td onclick='get_discount_val(6)'>6</td>
                                            </tr>
                                            <tr>
                                                <td onclick='get_discount_val(7)'>7</td>
                                                <td onclick='get_discount_val(8)'>8</td>
                                                <td onclick='get_discount_val(9)'>9</td>
                                            </tr>
                                            <tr>
                                                <td onclick='get_discount_sign()' id="dis_sign">%</td>
                                                <td onclick='get_discount_val(".")'>.</td>
                                                <td onclick='get_discount_val(0)'>0</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                                class="productx_popup_footer productx_popup_footer_primary productx_popup_footer_center productx_popup_footer_uppercase">
                            <button class="btn btn-primary btn-productx-link" onclick='add_diss(0)'> Add Discount</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->
        <!-- Modal2 Start -->
        <div id="modal-window-discount" class="shadow">
            <div>
                <div class="main-modal">
                    <div class="productx_popup">
                        <div class="productx_popup_header">
                            Message
                            <img onclick="closeDisModel()" src="<?php echo $close_img;?>" alt="close">
                        </div>
                        <div class="productx_popup_body">
                            <p style=" padding-top: 15px; padding-bottom: 15px; font-size: 20px; text-align: center; ">
                                Discount cannot be more than 100% of the Cart Value !</p>
                        </div>
                        <div
                                class="productx_popup_footer productx_popup_footer_primary productx_popup_footer_center productx_popup_footer_uppercase">
                            <button class="btn btn-primary btn-productx-link" onclick="closeDisModel()">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal2 End -->
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        //add class to product addons for design
        jQuery('input[type="file"]').each(function() {
            var parentdiv = jQuery(this).parent().parent();
            if (parentdiv) {
                jQuery(parentdiv).addClass("pos-addons-img");
            }
        });
        //show image name for product addons
        jQuery('.wc-pao-addon-file-upload').change(function() {
            if (jQuery(this)[0].files[0]) {
                var file = jQuery(this)[0].files[0].name;
                jQuery(this).next('small').append('<b class="show_addon_img" title="' + file + '"> ' + file
                    .slice(-30) + '</b>');
            }
        });
        //show error message
        jQuery(".woocommerce-error.productX-error").detach().appendTo(
            '.oliver_productx_container .ProductX-fir-div #productx-show-error');
    });
    jQuery('.clear_discount').on('click', function() {
        jQuery("#productx_link_input").val(0);
    });

    function openModal() {
        document.getElementById("pos-modal-window").style.display = "block";
        document.getElementById("productx_link_input").value = 0;
        let modal = document.querySelector('#pos-modal-window');
        modal.classList.add("showModal");
    }

    function closeModel() {
        document.getElementById("pos-modal-window").style.display = "none";
    }

    function closeDisModel() {
        document.getElementById("modal-window-discount").style.display = "none";
    }

    function get_discount_sign() {
        objButton = document.getElementById("dis_sign").innerHTML;
        if (objButton == "%") {
            document.getElementById("dis_sign").innerHTML = offertypeSymbol;
            document.getElementById("productx_discount_sign").innerHTML = objButton;
            document.getElementById("discount_type").value = "Percentage";
        } else {
            document.getElementById("dis_sign").innerHTML = "%";
            document.getElementById("productx_discount_sign").innerHTML = "";
            document.getElementById("discount_type").value = "Number";
        }
    }

    function deletenumber() {
        var deletenum = document.getElementById("productx_link_input").value;
        document.getElementById("productx_link_input").value = Math.floor(deletenum / 10);
    }

    function get_discount_val(objButton) {
        var dis_data = document.getElementById("productx_link_input").value;
        var dis_check = parseInt(dis_data);
        if (dis_check === 0 && dis_check !== null && dis_check !== '' && dis_check !== undefined) {
            document.getElementById("productx_link_input").value = objButton;
        } else {
            document.getElementById("productx_link_input").value = dis_data + objButton;
        }
    }

    function add_diss(objButton) {
        var productx_Dis_price = document.getElementById("productx_link_input").value;

        var prox_price = document.getElementById("productx_price").getAttribute("product_price");
        var price_sign = document.getElementById("productx_discount_sign").innerHTML;
        if (price_sign == "%") {
            if (Number(productx_Dis_price) > 100) {
                document.getElementById("modal-window-discount").style.display = "block";
            } else {
                document.getElementById("disprice").innerHTML = productx_Dis_price + "%";
                document.getElementById("add_product_discount").value = productx_Dis_price;
                document.getElementById("discount_type").value = 'Percentage';
                document.getElementById("pos-modal-window").style.display = "none";
            }
        } else if (Number(productx_Dis_price) > Number(prox_price)) {
            document.getElementById("modal-window-discount").style.display = "block";
        } else {
            document.getElementById("disprice").innerHTML = productx_Dis_price;
            document.getElementById("add_product_discount").value = productx_Dis_price;
            document.getElementById("discount_type").value = 'Number';
            document.getElementById("pos-modal-window").style.display = "none";
        }
    }
</script>
<?php do_action( 'woocommerce_after_single_product' ); ?>
<script>
    jQuery(function($) {

        $('[name=quantity]').change(function() {
            <?php if($product->get_sale_price())
            {
            ?>
            var compositive_pricep = $(
                '.oliver_compositive .composite_price p.price ins .woocommerce-Price-amount.amount')
                .text().replace(/[^\d\.]/g, '');
            <?php
            }
            else{
            ?>
            var compositive_pricep = $(
                '.oliver_compositive .composite_price p.price .woocommerce-Price-amount.amount').text()
                .replace(/[^\d\.]/g, '');
            <?php
            }?>
            if (compositive_pricep) {
                console.log('compositive_pricep');
                price = compositive_pricep;
                current_cart_total = <?php echo $woocommerce->cart->cart_contents_total; ?>;

                if (!(this.value < 1)) {

                    var product_total = parseFloat(price * this.value),
                        cart_total = parseFloat(product_total + current_cart_total);

                    //$('#product_total_price .price').html(product_total.toFixed(2) + currency);
                    $('#cart_total_price .woocommerce-Price-amount').html(cart_total.toFixed(2));
                }
                if (this.value >= 2) {
                    $(".oliver_compositive").hide();
                } else {
                    $(".oliver_compositive").show();
                }
                $('#product_total_price,#cart_total_price').toggle(!(this.value <= 1));
            }
        });
    });
</script>