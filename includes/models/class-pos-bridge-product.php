<?php
namespace bridge_models;
defined( 'ABSPATH' ) || exit;

use WC_Product;
use WP_Query;
use WC_Product_Bundle;
use WC_Product_Composite;
/**
 *  This class perform operation's on product
 */
class Pos_Bridge_Product {
    private $decimal_precision;
    function __construct() {
        $this->decimal_precision = absint( get_option( 'woocommerce_price_num_decimals', 2 ));	//digits after decimal
    }

    /**
     * Get products (pagination)
     * @param int $page page number
     * @param int $limit records limit
     * @return array Return array of products
     */
    public function oliver_pos_get_paged_products( $page, $limit ) {
        $products = array();
        $args = array(
            'post_type' => 'product',
            'post_status' => array('publish', 'private'),
            'posts_per_page' => $limit,
            'paged' => $page,
            'orderby' => 'title',
            'order'   => 'ASC',
        );
        $loop = new WP_Query($args);
        //print_r($loop); die();
        while ($loop->have_posts()):
            $loop->the_post();
            $product_id = (int) $loop->post->ID;
            array_push($products, $this->oliver_pos_get_product_data( $product_id ));
        endwhile;
        return $products;
    }

    /**
     * Get remaining products
     * @param int $remainig number of orders needs to get
     * @return array Return array of products
     */
    public function oliver_pos_get_remainig_products( $remainig ) {
        $products = array();
        $args = array(
            'post_type' => 'product',
            'post_status' => array('publish', 'private'),
            'posts_per_page' => $remainig,
            'orderby' => 'id',
            'order'   => 'DESC',
        );
        $loop = new WP_Query($args);
        //print_r($loop); die();
        while ($loop->have_posts()):
            $loop->the_post();
            $product_id = (int) $loop->post->ID;
            array_push($products, $this->oliver_pos_get_product_data( $product_id ));
        endwhile;
        return $products;
    }

    /**
     * Get product detail
     * @param int $product product id
     * @return object Return product detail
     */
    public function oliver_pos_get_product_data( $product ) {
        if ( is_numeric( $product ) ) {
            $product = wc_get_product( $product );
        }
        if( empty ( $product )) {
	        return oliver_pos_api_response('Invalid Product ID', -1);
        }
        // if ( ! is_a( $product, 'WC_Product' ) ) {
        // 	return array();
        // }

        $tags = wc_get_object_terms( $product->get_id(), 'product_tag', 'slug' );
        //Add Since 2.3.8.4 and edit from 2.3.8.9
        //price measurement

        if( metadata_exists( 'post', $product->get_id(), '_wc_price_calculator' ) ) {
            $measurement_price = get_post_meta($product->get_id(), '_wc_price_calculator', true);
            if ( ! empty( $measurement_price['calculator_type'] ) ) {
                array_push($tags,'oliver_produt_x');
                oliver_log("{$product->get_id()}=price measurement");
            } else {
                //Check for addons
                $tags = $this->oliver_pos_get_addons_productx($product, $tags);
            }
        } else {
            //Add Since 2.3.8.9
            //product Addons
            //Check for addons
            $tags = $this->oliver_pos_get_addons_productx($product, $tags);
        }
        $data = [];
        if('booking' == $product->get_type()) {
            array_push($data , [
                'meta_type' => 0,
                'slug' => 'oliver_booking_data',
                'value' => $this->oliver_pos_get_booking_product_data( $product ),
            ]);
        }
        if('bundle' == $product->get_type()) {
            array_push($data , [
                'meta_type' => 0,
                'slug' => 'oliver_bundle_data',
                'value' => $this->oliver_pos_get_bundle_product_data( $product ),
            ]);
        }
        if('composite' == $product->get_type()) {
            array_push($data , [
                'meta_type' => 0,
                'slug' => 'oliver_composite_data',
                'value' => $this->oliver_pos_get_composite_product_data( $product ),
            ]);
        }
        if(metadata_exists('post', $product->get_id(), '_wc_price_calculator')) {
            $measurement_price = get_post_meta($product->get_id(), '_wc_price_calculator', true);
            if(!empty($measurement_price['calculator_type'])) {
                array_push($data , [
                    'meta_type' => 0,
                    'slug' => 'oliver_measurement_data',
                    'value' => $this->oliver_pos_get_measurement_product_data( $product ),
                ]);
            }
        }
        if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' )) {
            $tags_addons = $this->oliver_pos_get_add_ons_product_data($product);
            if(!empty($tags_addons)) {
                array_push($data , [
                    'meta_type' => 0,
                    'slug' => 'oliver_addons_data',
                    'value' => $this->oliver_pos_get_add_ons_product_data( $product ),
                ]);
            }
        }
        if(metadata_exists('post', $product->get_id(), '_tc_is_ticket')) {
            array_push($data , [
                'meta_type' => 0,
                'slug' => 'oliver_tickera_data',
                'value' => $this->oliver_pos_get_ticket_data( $product->get_id() ),
            ]);
        }
        $prices_precision = wc_get_price_decimals();
        //add cost and profit from 2.3.9.8
        $cost = (float) esc_attr( (! empty(get_post_meta( $product->get_id(), 'product_cost', true ))) ? get_post_meta( $product->get_id(), 'product_cost', true ) : 0 );
        if ( COST_OF_GOODS_FOR_WOO==true ) {
            if(!empty(get_post_meta( $product->get_id(), '_alg_wc_cog_cost', true ))) {
                $cost = get_post_meta($product->get_id(), '_alg_wc_cog_cost', true);
            }
        }
        if( YITH_COST_OF_GOODS_FOR_WOO == true ) {
            if( !empty(get_post_meta( $product->get_id(), 'yith_cog_cost', true ))) {
                $cost = get_post_meta($product->get_id(), 'yith_cog_cost', true );
            }
        }
        return array(
            'title'              => $product->get_name(),
            'id'                 => $product->get_id(),
            'type'               => $product->get_type(),
            'status'             => $product->get_status(),
            'downloadable'       => (boolean) $product->is_downloadable(),
            'virtual'            => $product->is_virtual(),
            'permalink'          => $product->get_permalink(),
            'sku'                => $product->get_sku(),
            'barcode'            => esc_attr( get_post_meta( $product->get_id(), 'oliver_barcode', true ) ),
            'cost'               => $cost,
            'price'              => (float) (is_null($product->get_price()) || empty($product->get_price())) ? (is_null($product->get_regular_price()) || empty($product->get_regular_price())) ? 0 : $product->get_regular_price() : $product->get_price(),
            'regular_price'      => (float) (is_null($product->get_regular_price()) || empty($product->get_regular_price())) ? 0 : $product->get_regular_price(),
            'sale_price'         => (float) (is_null($product->get_sale_price()) || empty($product->get_sale_price())) ? 0 : $product->get_sale_price(),
            'price_html'         => $product->get_price_html(),
            'taxable'            => $product->is_taxable(),
            'tax_status'         => $product->get_tax_status(),
            'tax_class'          => $product->get_tax_class(),
            'managing_stock'     => $product->managing_stock(),
            'stock_quantity'     => is_null($product->get_stock_quantity()) ? 0 : (int) $product->get_stock_quantity(),
            'in_stock'           => $product->is_in_stock(),
            'stock_status'       => $product->get_stock_status(),
            'backorders_allowed' => $product->backorders_allowed(),
            'backordered'        => $product->is_on_backorder(),
            'sold_individually'  => $product->is_sold_individually(),
            'purchaseable'       => $product->is_purchasable(),
            'featured'           => $product->is_featured(),
            'visible'            => $product->is_visible(),
            'catalog_visibility' => $product->get_catalog_visibility(),
            'on_sale'            => $product->is_on_sale(),
            'product_url'        => $product->is_type( 'external' ) ? $product->get_product_url() : '',
            'button_text'        => $product->is_type( 'external' ) ? $product->get_button_text() : '',
            'weight'             => $product->get_weight() ? $product->get_weight() : null,
            'dimensions'         => array(
                'length' => $product->get_length(),
                'width'  => $product->get_width(),
                'height' => $product->get_height(),
                'unit'   => get_option( 'woocommerce_dimension_unit' ),
            ),
            'shipping_required'  => $product->needs_shipping(),
            'shipping_taxable'   => $product->is_shipping_taxable(),
            'shipping_class'     => $product->get_shipping_class(),
            'shipping_class_id'  => ( 0 !== $product->get_shipping_class_id() ) ? $product->get_shipping_class_id() : null,
            'description'        => wpautop( do_shortcode( $product->get_description() ) ),
            'short_description'  => apply_filters( 'woocommerce_short_description', $product->get_short_description() ),
            'reviews_allowed'    => $product->get_reviews_allowed(),
            'average_rating'     => $product->get_average_rating(),
            'rating_count'       => $product->get_rating_count(),
            'related_ids'        => array_map( 'absint', array_values( wc_get_related_products( $product->get_id() ) ) ),
            'upsell_ids'         => array_map( 'absint', $product->get_upsell_ids() ),
            'cross_sell_ids'     => array_map( 'absint', $product->get_cross_sell_ids() ),
            'parent_id'          => $product->get_parent_id(),
            'categories'         => wc_get_object_terms( $product->get_id(), 'product_cat', 'slug' ),
            'tags'               => $tags,
            'images'             => $this->oliver_pos_get_images( $product ),
            'featured_src'       => wp_get_attachment_url( get_post_thumbnail_id( $product->get_id() ) ),
            'attributes'         => $this->oliver_pos_get_attributes( $product ),
            'downloads'          => $this->oliver_pos_get_downloads( $product ),
            'warehouse'          => $this->oliver_pos_get_warehouse( $product ),
            'download_limit'     => $product->get_download_limit(),
            'download_expiry'    => $product->get_download_expiry(),
            'download_type'      => 'standard',
            'purchase_note'      => wpautop( do_shortcode( wp_kses_post( $product->get_purchase_note() ) ) ),
            'total_sales'        => $product->get_total_sales(),
            'variations'         => $this->oliver_pos_get_variation_data( $product ),
            'parent'             => array(),
            'is_ticket'          => $this->oliver_pos_product_is_ticket( $product->get_id() ),
            'ticket_info'        => $this->oliver_pos_get_ticket_data( $product->get_id() ),
            'product_metas'			 => $data,
            'visibility_oliver_pos' => get_post_meta( $product->get_id() , 'visibility_oliver_pos', true ),
        );
    }

    /**
     * Get all product tags with productX.
     * @since 2.3.8.9
     */
    public function oliver_pos_get_addons_productx($product, $tags) {
        if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' )) {
            // Check for single product addons
            $product_addons = get_post_meta($product->get_id(), '_product_addons', true);
            if(!empty($product_addons)) {
                array_push($tags, 'oliver_produt_x');
                oliver_log("{$product->get_id()}=product addons 0");
            } else {
                // Check for single all products (global) addons
                $args = array(
                    'post_type'=> 'global_product_addon',
                    'order'    => 'ASC'
                );
                $add_ons_posts = get_posts( $args );
                foreach($add_ons_posts as $add_ons_post) {
                    $addons_id =  $add_ons_post->ID;
                    $product_addons_all_products = get_post_meta($addons_id, '_all_products', true);
                    if($product_addons_all_products == 1) {
                        array_push($tags, 'oliver_produt_x');
                        oliver_log("{$product->get_id()}=product addons 1");
                    } else {
                        global $wpdb;
                        $cat_add_ons = $wpdb->get_results("SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE (object_id = '". $addons_id ."')");
                        $prod_add_ons_cat = array();
                        $terms = get_the_terms( $product->get_id(), 'product_cat' );
                        $product_cat = array();
                        foreach ($terms as $term) {
                            $product_cat[] = $term->term_id;
                        }
                        foreach($cat_add_ons as $cat_add_ons_id) {
                            if(in_array($cat_add_ons_id->term_taxonomy_id, $product_cat)) {
                                $cat_matches =  $cat_add_ons_id->term_taxonomy_id;
                                if(!empty($cat_matches)) {
                                    array_push($tags, 'oliver_produt_x');
                                    oliver_log("{$product->get_id()}=product addons 3");
                                }
                            }
                        }
                    }
                }
            }
        }
        return $tags;
    }

    /**
     * Get all product posts in loop.
     * @since 2.1.3.2
     * @param array $query_args custom query args
     * @return array Returns product posts array.
     */
    public function oliver_pos_get_all_product_post($query_args = array()) {
        $args = array(
            'post_type' => 'product',
            'post_status' => array('publish', 'private'),
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order'   => 'ASC',
        );
        $loop = new WP_Query($args);
        return $loop;
    }

    /**
     * Get id and inventory of all products.
     * @since 2.1.3.2
     * @return array Returns products array.
     */
    public function oliver_pos_get_products_stock_quantity() {
        $products = array();
        $loop = $this->oliver_pos_get_all_product_post();
        //start post loop
        while ($loop->have_posts()):
            $loop->the_post();
            $product_id = (int) $loop->post->ID;
            $product = wc_get_product( $product_id );
            $product_type = $product->get_type();
            $products[] = array(
                'id'   => $product_id,
                'stock'=> is_null($product->get_stock_quantity()) ? 0 : (int) $product->get_stock_quantity(),
            );
            if ($product_type == 'variable') {
                foreach ( $product->get_children() as $child_id ) {
                    $variation = wc_get_product( $child_id );
                    $products[] = array(
                        'id'   => $child_id,
                        'stock'=> is_null($variation->get_stock_quantity()) ? 0 : (int) $variation->get_stock_quantity(),
                    );
                }
            }
        endwhile;
        return $products;
    }

    /**
     * Get id,title and price of all products.
     * @since 2.1.3.2
     * @return array Returns products array.
     */
    public function oliver_pos_get_products_price_with_title() {
        $products = array();
        $loop = $this->oliver_pos_get_all_product_post();

        //start post loop
        while ($loop->have_posts()):
            $loop->the_post();
            $product_id = (int) $loop->post->ID;
            $product = wc_get_product( $product_id );
            $product_type = $product->get_type();
            $products[] = array(
                'id'    => $product_id,
                'title' => $product->get_name(),
                'price' => (float) (is_null($product->get_price()) || empty($product->get_price())) ? (is_null($product->get_regular_price()) || empty($product->get_regular_price())) ? 0 : $product->get_regular_price() :$product->get_price(),
            );
            if ($product_type == 'variable') {
                foreach ( $product->get_children() as $child_id ) {
                    $variation = wc_get_product( $child_id );
                    $products[] = array(
                        'id'    => $child_id,
                        'title' => $variation->get_name(),
                        'price' => (float) (is_null($variation->get_price()) || empty($variation->get_price())) ? (is_null($variation->get_regular_price()) || empty($variation->get_regular_price())) ? 0 : $variation->get_regular_price() : $variation->get_price(),
                    );
                }
            }
        endwhile;
        return $products;
    }

    /**
     * Get product id and their child id.
     * @since 2.3.5.1
     * @return array Returns products array.
     */
    public function oliver_pos_get_products_id_and_child_id() {
        $products = array();
        $loop = $this->oliver_pos_get_all_product_post();

        //start post loop
        while ($loop->have_posts()):
            $loop->the_post();
            $product_id = (int) $loop->post->ID;
            $product = wc_get_product( $product_id );
            $products[]	= $product_id;
            if ( $product->has_child() ) {
                foreach ( $product->get_children() as $key => $child_id ) {
                    $products[] = $child_id;
                }
            }
        endwhile;
        return $products;
    }

    /**
     * Get the images for a product or product variation
     *
     * @param WC_Product|WC_Product_Variation $product
     * @return array
     */
    private function oliver_pos_get_images( $product ) {
        $images        = $attachment_ids = array();
        $product_image = $product->get_image_id();
        // Add featured image.
        if ( ! empty( $product_image ) ) {
            $attachment_ids[] = $product_image;
        }

        // Add gallery images.
        $attachment_ids = array_merge( $attachment_ids, $product->get_gallery_image_ids() );

        // Build image data.
        foreach ( $attachment_ids as $position => $attachment_id ) {
            $attachment_post = get_post( $attachment_id );
            if ( is_null( $attachment_post ) ) {
                continue;
            }
            $attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
            if ( ! is_array( $attachment ) ) {
                continue;
            }
            $images[] = array(
                'id'         => (int) $attachment_id,
                'created_at' => $attachment_post->post_date_gmt,
                'updated_at' => $attachment_post->post_modified_gmt,
                'src'        => current( $attachment ),
                'title'      => get_the_title( $attachment_id ),
                'alt'        => esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ),
                'position'   => (int) $position,
            );
        }

        // Set a placeholder image if the product has no images set.
        if ( empty( $images ) ) {
            $images[] = array(
                'id'         => 0,
                'created_at' => time(), // Default to now.
                'updated_at' => time(),
                'src'        => wc_placeholder_img_src(),
                'title'      => __( 'Placeholder', 'woocommerce' ),
                'alt'        => __( 'Placeholder', 'woocommerce' ),
                'position'   => 0,
            );
        }
        return $images;
    }

    /**
     * Get the attributes for a product or product variation
     *
     * @param WC_Product|WC_Product_Variation $product
     * @return array
     */
    private function oliver_pos_get_attributes( $product ) {
        $attributes = array();
        if ( $product->is_type( 'variation' ) ) {

            // variation attributes
            foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {

                // taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`
                $attributes[] = array(
                    'name'   => wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ) ),
                    'slug'   => str_replace( 'attribute_', '', str_replace( 'pa_', '', $attribute_name ) ),
                    'option' => $attribute,
                );
            }
        } else {
            foreach ( $product->get_attributes() as $attribute ) {
                $attributes[] = array(
                    'name'      => wc_attribute_label( $attribute['name'] ),
                    'slug'      => str_replace( 'pa_', '', $attribute['name'] ),
                    'position'  => (int) $attribute['position'],
                    'visible'   => (bool) $attribute['is_visible'],
                    'variation' => (bool) $attribute['is_variation'],
                    'is_taxonomy'=> (bool) $attribute['is_taxonomy'], // return true if global attribute else false
                    'options'   => $this->oliver_pos_get_attribute_options( $product->get_id(), $attribute ),
                    'options_all'=> $this->oliver_pos_get_attribute_options_with_all_fields( $product->get_id(), $attribute ), //since 2.1.2.1
                );
            }
        }
        return $attributes;
    }

    /**
     * Get the downloads for a product or product variation
     *
     * @param WC_Product|WC_Product_Variation $product
     * @return array
     */
    private function oliver_pos_get_downloads( $product ) {
        $downloads = array();
        if ( $product->is_downloadable() ) {
            foreach ( $product->get_downloads() as $file_id => $file ) {
                $downloads[] = array(
                    'id'   => $file_id, // do not cast as int as this is a hash
                    'name' => $file['name'],
                    'file' => $file['file'],
                );
            }
        }
        return $downloads;
    }

    /**
     * Get product ticket data.
     * @param int $id product id
     * @return array Returns product ticket data.
     */
    private function oliver_pos_get_ticket_data( $id ) {
        if ( $this->oliver_pos_product_is_ticket( $id ) ) {
            return array(
                '_event_name'   	=> 	esc_attr( get_post_meta($id, '_event_name', true) ),
                '_available_checkins_per_ticket'   	=> 	esc_attr( get_post_meta($id, '_available_checkins_per_ticket', true) ),
                '_ticket_template'   	=> 	esc_attr( get_post_meta($id, '_ticket_template', true) ),
                '_owner_form_template'   	=> 	esc_attr( get_post_meta($id, '_owner_form_template', true) ),
                '_ticket_checkin_availability'   	=> 	esc_attr( get_post_meta($id, '_ticket_checkin_availability', true) ),
                '_ticket_checkin_availability_from_date'   	=> 	esc_attr( get_post_meta($id, '_ticket_checkin_availability_from_date', true) ),
                '_ticket_checkin_availability_to_date'   	=> 	esc_attr( get_post_meta($id, '_ticket_checkin_availability_to_date', true) ),
                '_ticket_availability'   	=> 	esc_attr( get_post_meta($id, '_ticket_availability', true) ),
                '_ticket_availability_from_date'   	=> 	esc_attr( get_post_meta($id, '_ticket_availability_from_date', true) ),
                '_ticket_availability_to_date'   	=> 	esc_attr( get_post_meta($id, '_ticket_availability_to_date', true) ),
                '_time_after_order_days'   	=> 	esc_attr( get_post_meta($id, '_time_after_order_days', true) ),
                '_time_after_order_hours'   	=> 	esc_attr( get_post_meta($id, '_time_after_order_hours', true) ),
                '_time_after_order_minutes'   	=> 	esc_attr( get_post_meta($id, '_time_after_order_minutes', true) ),
                '_tc_used_for_seatings'   	=> 	esc_attr( get_post_meta($id, '_tc_used_for_seatings', true) ),
                '_seat_color'   	=> 	esc_attr( get_post_meta($id, '_seat_color', true) ),
            );
        }
        return array();
    }

    /**
     * Check if product is ticket
     * @param int $id product id
     * @return boolean Returns true if yes || false.
     */
    public function oliver_pos_product_is_ticket( $id ) {
        return strtoupper( esc_attr( get_post_meta($id, '_tc_is_ticket', true) ) ) == "YES" ? true : false;
    }

    /**
     * Update product inventory
     * @param int $product_id product id
     * @param int $variation_id variation product id
     * @param int $quantity product quantity
     * @return boolean Returns true.
     */
    public function oliver_pos_update_oliver_inventory( $product_id, $variation_id = 0, $quantity =0, $warehouse_id = 0 ) {
        global $wpdb;
        $product_id = ($variation_id > 0) ? $variation_id : $product_id;
        $data_warehouse = $wpdb->get_results( "SELECT isdefault FROM {$wpdb->prefix}pos_warehouse WHERE oliver_warehouseid = '". $warehouse_id ."'", OBJECT );
        oliver_log( 'warehouse_id = ' . $warehouse_id );
        oliver_log( 'product_id = ' . $product_id );
        oliver_log( 'quantity = ' . $quantity );

	    if ( empty($data_warehouse) || $data_warehouse[0]->isdefault == 1 || $warehouse_id==0  ) {
            $product = wc_get_product( $product_id );
            $product->set_manage_stock( true );
            $product->set_stock_quantity( $quantity );
            $product->save();
        } else {
            update_post_meta( $product_id, '_warehouse_'.$warehouse_id , $quantity);
        }
        return true;
    }

    /**
     * Get attribute options.
     *
     * @param int $product_id
     * @param array $attribute
     * @return array
     */
    protected function oliver_pos_get_attribute_options( $product_id, $attribute ) {
        if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
            return wc_get_product_terms( $product_id, $attribute['name'], array( 'fields' => 'names' ) );
        } elseif ( isset( $attribute['value'] ) ) {
            return array_map( 'trim', explode( '|', $attribute['value'] ) );
        }
        return array();
    }

    /**
     * Get attribute options.
     *
     * @since 2.1.2.1
     * @param int $product_id
     * @param array $attribute
     * @return array
     */
    protected function oliver_pos_get_attribute_options_with_all_fields( $product_id, $attribute ) {
        if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
            return wc_get_product_terms( $product_id, $attribute['name'], array( 'fields' => 'all' ) );
        } elseif ( isset( $attribute['value'] ) ) {
            return array_map( 'trim', explode( '|', $attribute['value'] ) );
        }
        return array();
    }

    /**
     * Get product attribute combination
     * @param array $attributes product attributess
     * @return string Return combination.
     */
    private function oliver_pos_get_variation_combination( $attributes ) {
        $combination = '';
        $count = count($attributes);
        if ( !empty($attributes) ) {
            foreach ($attributes as $key => $attribute) {
                $is_slash = ($count == ( $key + 1 )) ? '' : '~';
                $combination .= (empty($attribute['option']) ? "**" : $attribute['option']).$is_slash;
            }
        }
        return $combination;
    }

    /**
     * Get an individual variation's data
     *
     * @param WC_Product $product
     * @return array
     */
    private function oliver_pos_get_variation_data( $product ) {
        $prices_precision = wc_get_price_decimals();
        $variations       = array();
        foreach ( $product->get_children() as $child_id ) {
            $variation = wc_get_product( $child_id );
            if ( ! $variation || ! $variation->exists() ) {
                continue;
            }

            // $variations[] 	=  $child_id;
            array_push($variations, $this->oliver_pos_variation_product_data( $variation ));
        }
        return $variations;
    }

    /**
     * Get variation product details
     * @param int $variation_id variation product id
     * @return array Return variation product details.
     */
    public function oliver_pos_get_variation_product_data( $variation_id ) {
        $variation = wc_get_product( $variation_id );
        return $this->oliver_pos_variation_product_data( $variation );
    }

    /**
     * Get variation product details
     * @param object $variation variation product instance
     * @return array Return variation product details.
     */
    public function oliver_pos_variation_product_data( $variation ) {
        //add cost and profit from 2.3.9.8
        $cost = (float) esc_attr( (! empty(get_post_meta( $variation->get_id(), 'product_cost', true ))) ? get_post_meta( $variation->get_id(), 'product_cost', true ) : 0 );
        if ( COST_OF_GOODS_FOR_WOO == true ) {
            if(!empty(get_post_meta( $variation->get_id(), '_alg_wc_cog_cost', true ))) {
                $cost = get_post_meta($variation->get_id(), '_alg_wc_cog_cost', true);
            }
        }
	    if( YITH_COST_OF_GOODS_FOR_WOO == true ) {
		    if( !empty(get_post_meta( $variation->get_id(), 'yith_cog_cost', true ))) {
			    $cost = get_post_meta($variation->get_id(), 'yith_cog_cost', true );
		    }
	    }
        return array(
            'id'                => $variation->get_id(),
            'parent_id'         => $variation->get_parent_id(),
            'title'             => $variation->get_name(),
            'type'              => $variation->get_type(),
            'status'            => $variation->get_status(),
            'downloadable'      => (boolean) $variation->is_downloadable(),
            'virtual'           => $variation->is_virtual(),
            'permalink'         => $variation->get_permalink(),
            'sku'               => $variation->get_sku(),
            'barcode'           => esc_attr( get_post_meta( $variation->get_id(), 'var_product_barcode', true ) ),
            'cost'           	=> $cost,
            'price'             => (float) (is_null($variation->get_price()) || empty($variation->get_price())) ? (is_null($variation->get_regular_price()) || empty($variation->get_regular_price())) ? 0 : $variation->get_regular_price() : $variation->get_price(),
            'regular_price'     => (float) (is_null($variation->get_regular_price()) || empty($variation->get_regular_price())) ? 0 : $variation->get_regular_price(),
            'sale_price'        => (float) (is_null($variation->get_sale_price()) || empty($variation->get_sale_price())) ? 0 : $variation->get_sale_price(),
            'price_html'        => $variation->get_price_html(),
            'taxable'           => $variation->is_taxable(),
            'tax_status'        => $variation->get_tax_status(),
            'tax_class'         => $variation->get_tax_class(),
            'managing_stock'    => $variation->managing_stock(),
            'stock_quantity'    => is_null($variation->get_stock_quantity()) ? 0 : (int) $variation->get_stock_quantity(),
            'in_stock'          => $variation->is_in_stock(),
            'stock_status'      => $variation->get_stock_status(),
            'backordered'       => $variation->is_on_backorder(),
            'purchaseable'      => $variation->is_purchasable(),
            // 'visible'           => $variation->variation_is_visible(), give 500 error
            'visible'           => true,
            'on_sale'           => $variation->is_on_sale(),
            'weight'            => $variation->get_weight() ? $variation->get_weight() : null,
            'dimensions'        => array(
                'length' => $variation->get_length(),
                'width'  => $variation->get_width(),
                'height' => $variation->get_height(),
                'unit'   => get_option( 'woocommerce_dimension_unit' ),
            ),
            'shipping_class'    => $variation->get_shipping_class(),
            'shipping_class_id' => ( 0 !== $variation->get_shipping_class_id() ) ? $variation->get_shipping_class_id() : null,
            'images'             => $this->oliver_pos_get_images( $variation ),
            'attributes'        => $this->oliver_pos_get_attributes( $variation ),
            'combination'       => $this->oliver_pos_get_variation_combination( $this->oliver_pos_get_attributes( $variation ) ),
            'downloads'         => $this->oliver_pos_get_downloads( $variation ),
            'download_limit'    => -1,
            'download_expiry'   => -1,
            'is_ticket'         => $this->oliver_pos_product_is_ticket( $variation->get_parent_id() ),
            'ticket_info'       => $this->oliver_pos_get_ticket_data( $variation->get_parent_id() ),
        );
    }

    /**
     * Return all data of bundle product otherwies array
     * @since 2.3.3.1
     * @return array bundle product data
     */
    public function oliver_pos_get_bundle_product_data( $product ) {
        $data = array();
        if ($product->get_type() != 'bundle') {
            return $data;
        }
        $bundled_product = new WC_Product_Bundle($product->get_id());
        $bundled_items = $bundled_product->get_bundled_items();
        if ( ! empty($bundled_items)) {
            foreach ($bundled_items as $key => $bundled_item) {
                $data[] = [
                    'layout'  		=> $bundled_product->get_layout(),
                    'form_location' => $bundled_product->get_add_to_cart_form_location(),
                    'item_grouping' => $bundled_product->get_group_mode(),
                    'edit_in_cart'  => $bundled_product->get_editable_in_cart(),
                    'bundled_items' => $bundled_item->get_data(),
                ];
            }
        }
        return $data;
    }

    /**
     * Return all data of composite product otherwies array
     * @since 2.3.4.2
     * @return array composite product data
     */
    public function oliver_pos_get_composite_product_data( $product ) {
        $data = array();
        if ($product->get_type() === 'composite') {
            $composite_product = new WC_Product_Composite($product);
            $data[] = [
                'from_location'	=>	$composite_product->get_add_to_cart_form_location(),
                'layout'		=>	$composite_product->get_layout(),
                'edit_in_cart'	=>	$composite_product->get_editable_in_cart(),
                'catelog_price'	=>	$composite_product->get_shop_price_calc(),
                'composite_data'=>	$composite_product->get_composite_data()
            ];
            return $data;
        } else {
            return $data;
        }
    }

    /**
     * Add Warehouse 2.3.9.8
     */
    public function oliver_pos_get_warehouse( $product ) {
        global $post;
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE '_warehouse_%' AND post_id = '". $product->get_id() ."'");
        $data = array();
        foreach($results as $key => $result) {
            $data[] = array(
                'warehouse_id'    => trim(str_replace('_warehouse_','',$result->meta_key)),
                'quantity'        => $result->meta_value,
            );
        }
        return $data;
    }

    /**
     * Return all data of measurement product otherwise array
     *
     * @since 2.3.9.8
     *
     * @return array measurement product data
     */
    public function oliver_pos_get_measurement_product_data($product) {
        $data = [];
        if(metadata_exists('post', $product->get_id(), '_wc_price_calculator')) {
            $data = get_post_meta($product->get_id(), '_wc_price_calculator',  true);
            return $data;
        } else {
            return $data;
        }
    }

    /**
     * Return all data of Booking product otherwies array
     *
     * @since 2.3.9.8
     *
     * @return array Booking product data
     */
    public function oliver_pos_get_booking_product_data($product) {
        $data = [];
        if(metadata_exists('post', $product->get_id(), '_wc_booking_availability')) {
            $data['booking_availability'] = get_post_meta($product->get_id(), '_wc_booking_availability', true);
            $data['additional_costs'] = get_post_meta($product->get_id(), '_has_additional_costs', true);
            $data['booking_apply_adjacent_buffer'] = get_post_meta($product->get_id(), '_wc_booking_apply_adjacent_buffer', true);
            $data['booking_block_cost'] = get_post_meta($product->get_id(), '_wc_booking_block_cost', true);
            $data['booking_buffer_period'] = get_post_meta($product->get_id(), '_wc_booking_buffer_period', true);
            $data['booking_calendar_display_mode'] = get_post_meta($product->get_id(), '_wc_booking_calendar_display_mode', true);
            $data['booking_cancel_limit_unit'] = get_post_meta($product->get_id(), '_wc_booking_cancel_limit_unit', true);
            $data['booking_cancel_limit'] = get_post_meta($product->get_id(), '_wc_booking_cancel_limit', true);
            $data['booking_check_availability_against'] = get_post_meta($product->get_id(), '_wc_booking_check_availability_against', true);
            $data['booking_cost'] = get_post_meta($product->get_id(), '_wc_booking_cost', true);
            $data['booking_default_date_availability'] = get_post_meta($product->get_id(), '_wc_booking_default_date_availability', true);
            $data['booking_default_date_availability'] = get_post_meta($product->get_id(), '_wc_booking_default_date_availability', true);
            $data['booking_duration_type'] = get_post_meta($product->get_id(), '_wc_booking_duration_type', true);
            $data['booking_duration_unit'] = get_post_meta($product->get_id(), '_wc_booking_duration_unit', true);
            $data['booking_duration'] = get_post_meta($product->get_id(), '_wc_booking_duration', true);
            $data['booking_enable_range_picker'] = get_post_meta($product->get_id(), '_wc_booking_enable_range_picker', true);
            $data['booking_first_block_time'] = get_post_meta($product->get_id(), '_wc_booking_first_block_time', true);
            $data['booking_has_person_types'] = get_post_meta($product->get_id(), '_wc_booking_has_person_types', true);
            $data['booking_has_persons'] = get_post_meta($product->get_id(), '_wc_booking_has_persons', true);
            $data['booking_has_resources'] = get_post_meta($product->get_id(), '_wc_booking_has_resources', true);
            $data['booking_has_restricted_days'] = get_post_meta($product->get_id(), '_wc_booking_has_restricted_days', true);
            $data['booking_max_date_unit'] = get_post_meta($product->get_id(), '_wc_booking_max_date_unit', true);
            $data['booking_max_date'] = get_post_meta($product->get_id(), '_wc_booking_max_date', true);
            $data['booking_max_duration'] = get_post_meta($product->get_id(), '_wc_booking_max_duration', true);
            $data['booking_max_persons_group'] = get_post_meta($product->get_id(), '_wc_booking_max_persons_group', true);
            $data['booking_min_date_unit'] = get_post_meta($product->get_id(), '_wc_booking_min_date_unit', true);
            $data['booking_min_date'] = get_post_meta($product->get_id(), '_wc_booking_min_date', true);
            $data['booking_min_duration'] = get_post_meta($product->get_id(), '_wc_booking_min_duration', true);
            $data['booking_min_persons_group'] = get_post_meta($product->get_id(), '_wc_booking_min_persons_group', true);
            $data['booking_person_cost_multiplier'] = get_post_meta($product->get_id(), '_wc_booking_person_cost_multiplier', true);
            $data['booking_person_qty_multiplier'] = get_post_meta($product->get_id(), '_wc_booking_person_qty_multiplier', true);
            $data['booking_pricing'] = get_post_meta($product->get_id(), '_wc_booking_pricing', true);
            $data['booking_qty'] = get_post_meta($product->get_id(), '_wc_booking_qty', true);
            $data['booking_requires_confirmation'] = get_post_meta($product->get_id(), '_wc_booking_requires_confirmation', true);
            $data['booking_resources_assignment'] = get_post_meta($product->get_id(), '_wc_booking_resources_assignment', true);
            $data['booking_restricted_days'] = get_post_meta($product->get_id(), '_wc_booking_restricted_days', true);
            $data['booking_user_can_cancel'] = get_post_meta($product->get_id(), '_wc_booking_user_can_cancel', true);
            $data['display_cost'] = get_post_meta($product->get_id(), '_wc_display_cost', true);
            $data['booking_resource_label'] = get_post_meta($product->get_id(), 'wc_booking_resource_label', true);
            $data['points_earned'] = get_post_meta($product->get_id(), '_wc_points_earned', true);
            $data['points_max_discount'] = get_post_meta($product->get_id(), '_wc_points_max_discount', true);
            return $data;

        } else {
            return $data;
        }
    }

    /**
     * Return all data of add ons product otherwies array
     *
     * @since 2.3.9.8
     *
     * @return array add ons product data
     */
    public function oliver_pos_get_add_ons_product_data($product) {
        if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' )) {
            $data = [];
            // Check for single product addons
            $product_addons = get_post_meta($product->get_id(), '_product_addons', true);

            if(!empty($product_addons)) {
                $data =  $product_addons;
                return $data;
            } else {
                // Check for single all products (global) addons
                $args = array(
                    'post_type'=> 'global_product_addon',
                    'order'    => 'ASC'
                );
                $add_ons_posts = get_posts( $args );
                foreach($add_ons_posts as $add_ons_post) {
                    $addons_id =  $add_ons_post->ID;
                    $product_addons_all_products = get_post_meta($addons_id, '_all_products', true);
                    $product_addons_all_cat = get_post_meta($addons_id, '_product_addons', true);
                    if($product_addons_all_products == 1) {
                        $data =  $product_addons_all_cat;
                        return $data;
                    } else {
                        global $wpdb;
                        $cat_add_ons = $wpdb->get_results("SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE (object_id = '". $addons_id ."')");
                        $prod_add_ons_cat = array();
                        $terms = get_the_terms( $product->get_id(), 'product_cat' );
                        $product_cat = array();
                        foreach ($terms as $term) {
                            $product_cat[] = $term->term_id;
                        }
                        foreach($cat_add_ons as $cat_add_ons_id) {
                            if(in_array($cat_add_ons_id->term_taxonomy_id, $product_cat)) {
                                $cat_matches =  $cat_add_ons_id->term_taxonomy_id;
                                if(!empty($cat_matches)) {
                                    $data = $product_addons_all_cat;
                                    return $data;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $data;
    }
}
