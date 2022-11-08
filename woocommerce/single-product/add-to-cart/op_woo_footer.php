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
            document.getElementById("dis_sign").innerHTML = '$';
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
        if (jQuery('#productx_link_input').val().replace(/[^.]/g, "").length > 0){
            if(objButton=='.')
            {
                objButton='';
            }
        }
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