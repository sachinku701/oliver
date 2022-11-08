<?php
   global $woocommerce;
   $close_img = plugins_url('public/resource/img/close.svg', dirname(dirname(dirname(__FILE__))));
   ?> 
<div class="before_cart_form">
	<div class="oliver_productx_container">
		<div class="modal-header">
		   <h5 class="modal-title" id="modalLargeLabel" title="<?php the_title();?>">
			  <div><?php the_title();?></div>
		   </h5>
		   <button type="button" class="close close_child_window" data-dismiss="modal" aria-label="Close">
		   <img src="<?php echo $close_img;?>" alt="close_img" class="productx_close_img">
		   </button>
		</div>
		<div class="oliver_pos_productx_first">
		   <?php
			  //product img
			  $product_image = wp_get_attachment_image_src( get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail' );
			  if(empty($product_image[0])){
				$pro_img= plugins_url('public/resource/img/woocommerce-placeholder-416x416.png', dirname(dirname(dirname(__FILE__))));
				
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
		   <div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'ProductX-fir-div', $product ); ?>>
			  <span id="productx-show-error"></span>
			  <div class="summary entry-summary oliver-pos-entry-summary"></div>
		   </div>
		   <?php } ?>
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
												  $delete_img= plugins_url('public/resource/img/LogoutRoundedLeft.svg', dirname(dirname(dirname(__FILE__))));
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
								Discount cannot be more than 100% of the Cart Value !
							 </p>
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
		<div class="oliver_pos_productx_sacond">