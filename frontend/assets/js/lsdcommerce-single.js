/*
Javascript Code in Single Product
*/
(function ($) {
	'use strict';

	// Initialize Variable 
	let productID,
		productPrice,
		productThumb,
		productTitle,
		productLimit,
		productQty;

	let single, cart, cartTemplate, cartItem, cartPopup = false; //State Flag
	let controlQty, controlCartQty, total = false;
	let order_object = {};

	function lsdcommerce_single_cartmanager(counter, text) {
		$('.cart-footer-info h4').text(text);
		$('.cart-manager span').text(counter);
	}


	/* Ready function, Initialize */
	$(document).ready(function () {
		// Cart Manager - Show Total and Counter Item
		cart = new LSDCCookie('_lsdcommerce_cart');
		total = cart.get('total');
		let totalText = null;
		if (total == undefined) {
			total = {
				'total_qty': 0
			};
			totalText = lsdc_pub.translation.cart_empty; // Empty
		} else {
			totalText = lsdcommerce_currency_format(true, total.total_price);
		}
		lsdcommerce_single_cartmanager(total.total_qty, totalText);

		single = $('#product-detail');
		cartTemplate = $('#item-template').html();
		cartItem = $('#cart-items');
		cartPopup = $('#cart-popup');
		controlQty = $('.cart-qty-float');

		// Set Product Variable
		if (single.length) {
			productID = parseInt(single.attr('data-id'));
			productPrice = parseInt(single.attr('data-price'));
			productThumb = single.attr('data-thumbnail');
			productTitle = single.attr('data-title');
			productLimit = single.attr('data-limit') == null ? 1 : parseInt(single.attr('data-limit'));
		}


	});

	// Cart Manager :: Show Product Lists
	$(document).on('click', '.cart-manager', function (e) {
		e.preventDefault();

		var data = {};
		var dataFormatted = cart.get('formatted');
		data['items'] = dataFormatted;

		if (dataFormatted.length) {
			// Add Overlay
			cartPopup.addClass('overlay');
			cartPopup.find('.cart-body').removeClass('hidden');
			controlQty.hide();

			if (cartTemplate && data) {
				// Templating
				var render = Mustache.to_html(cartTemplate, data);
				jQuery(cartItem).html(render);

				setTimeout(() => {
					// Showing Up
					jQuery(cartItem).fadeIn('fast');
				}, 1000);
			} else {
				jQuery(cartItem).hide().html('Error Load Data...').fadeIn('slow'); // FallBack
			}
		}
	});

	// Cart Manager :: Hide Product Lists by Click Overlay
	$(document).on('click', '.overlay', function (e) {
		if (e.target == this) {
			e.preventDefault();
			// controlQty.show();
			cartPopup.removeClass('overlay');
			cartPopup.find('.cart-body').addClass('hidden');
		}
	});


	// ------------- Add to Cart ----------------- //
	function lsdcommerce_addto_cart(inType, inID, inQTY, inPrice, inTitle) {

		if( productID || inID ){

		if( ! Number.isInteger( productID ) ){
			productID = productID.split( '-', 2 )[0];
		} 
		let cartProduct = cart.get('product', productID);

		let variationElement = $('.product-variant .container .variant-item'); 
		let variationID, variationName, variationPrice = '';
		let variationObject = {};
		let inputable = false;
		let singleQtyControl = $('#single-qty input[name="qty"]'); // get single qty


		// Set Qty to Zero if Undefined
		if ( cartProduct == undefined ){
			cartProduct = {}; 
			cartProduct['qty'] = 0;
		}else{
			singleQtyControl.val( cartProduct.qty );
		}

		let singleQty = parseInt(singleQtyControl.val()) == null ? 0 : cartProduct.qty;

		// Reset Product ID from Variation ID

		// Barang Masuk Ke keranjang
		if (inType == 'add') {
			/* PRO Code -- Ignore, But Don't Delete */
			if( variationElement.length ){
				let variantSelected = {};
				let variantQtyLimit = 0;
				variationID = '';
				// Iterate Variations
				variationElement.each(function (i, obj) {
					// Get Variant ID 
					let variantID = $(obj).find('.variant-name').attr('data-id');
					// Get Variant Selected in Variant List
					let variant = $(obj).find('input[type="radio"][name="' + variantID + '"]:checked');
					let variantName = $(obj).find('label[for="' + variant.attr('id') + '"]').text();
					variantQtyLimit = variant.attr('qty');

					// Create Object Variant Selected
					variantSelected[variant.attr('id')] = {
						'name': variantName,
						'price': variant.attr('price')
					}

					// Populate Variation
					if( variant.attr('id') ){
						variationID += variant.attr('id') + '-'; // Create ID Variation 123-hitam-xl
						variationName = ' - ' + variantName;
						variationPrice = parseInt(inPrice != null ? inPrice : productPrice) + parseInt(variant.attr('price'));
						variationObject[variantID] = variantSelected;
					}

				});

				// Removing Last Char '-' from add recrusive
				variationID = variationID.slice(0, -1);

				// Redefine Product ID if Variation Exists
				if (variationID) {
					variationID = productID + '-' + variationID;
				} else {
					variationID = productID;
				}

				// Limit Add to Cart over Quantity Max
				// let cartProduct = cart.get('product', variationID);

				// if (cartProduct.qty < parseInt(variantQtyLimit)) {
					productQty = singleQty + 1;
					inputable = true;
				// }
			}else{
				// Limit Order
				if (cartProduct.qty < parseInt(productLimit)) {
					productQty = singleQty + 1;
					inputable = true;
				}
			}

			
		} else if (inType == 'sub') {
			inputable = true;
			// Sub Product Variation
		}
		
		// Check Variation ID Avaialble
		if( inputable ){
			if( variationID ){
				cart.set(inType, {
					"id": variationID,
					"qty": inQTY != null ? inQTY : productQty,
					"title": inTitle != null ? inTitle : productTitle + variationName,
					"price": inPrice != null ? inPrice : variationPrice,
					"thumbnail": productThumb,
					"variation_id" : variationID,
					"variations": variationObject,
				});
				return variationID;
			}else{
				cart.set(inType, {
					"id": inID != null ? inID : productID,
					"qty": inQTY != null ? inQTY : productQty,
					"title": inTitle != null ? inTitle : productTitle,
					"price": inPrice != null ? inPrice : productPrice,
					"thumbnail": productThumb,
				});
				return productID;
			}
		}
	}
	
	}

	// AddtoCart via Button
	$(document).on('click', '.lsdc-addto-cart', function (e) {
		e.preventDefault();

		// Reset Cart Manager
		cartPopup.addClass('show');
		cartPopup.removeClass('overlay');
		cartPopup.find('.cart-body').addClass('hidden');
		controlQty.show(); // Show Qty

		// Adding to Carta
		productID = lsdcommerce_addto_cart('add');
		let carts = cart.get('product', productID);
		 //Change ID for Variation
		controlQty.attr( 'product-id', productID );
		// Set Qty and CartManager by Carts Quantity
		if( carts ){
			controlQty.find('.lsdc-qty input[name="qty"]').val(carts.qty);
		}

		let total = cart.get('total');
		lsdcommerce_single_cartmanager(total.total_qty, lsdcommerce_currency_format(true, total.total_price));
	});

	//  AddtoCart via QTY Add
	$(document).on('click', '.plus', function (e) {
		let inputable = false;
		let plusInCart, plusInFloat = null; // Add Qty on Cart
		controlCartQty = $(this).closest('.lsdc-qty').find('input[name="qty"]');
		productQty = controlCartQty.val() == null ? 1 : parseInt(controlCartQty.val()); // Force set 1 if empty
		productID = $(this).closest('.item').attr('id'); // Get Product ID

		if (productID == undefined) // Minus in Float Qty not on Cart Manager
		{
			plusInFloat = true;
			productID = $(this).closest('.cart-qty-float').attr('product-id'); // Get Product ID
		} else {
			plusInCart = true;
		}

		/* PRO Code -- Ignore, But Don't Delete */
		let variationElement = $('.product-variant .container .variant-item'); 
		let variantQtyLimit = 0;
		if( variationElement.length ){
			// Iterate Variations
			variationElement.each(function (i, obj) {
				let variantID = $(obj).find('.variant-name').attr('data-id');
				let variant = $(obj).find('input[type="radio"][name="' + variantID + '"]:checked');
				variantQtyLimit = variant.attr('qty');
			});

			if ( productQty < variantQtyLimit ) { // Limit Order
				inputable = true;
			}

		}else{

			if ( productQty < productLimit ) { // Limit Order
				inputable = true;
			}
	
		}

		if( inputable ){
			controlCartQty.val(++productQty); // Increase Quantity
			controlQty.find('input[name="qty"]').val(productQty); // Sync and Set Qty

			let product_cart = cart.get('product', productID); //Get Detail Product by ID

			$(this).closest('.cart-basket')
				.find('.item[id="' + productID + '"] .price')
				.text(lsdcommerce_currency_format(true, product_cart.price * productQty)); // Refersh New Price based On Qty

			// Update Cart
			lsdcommerce_addto_cart('add', productID, productQty, product_cart.price, productTitle);

			// ---> Updating Cart Manager
			let total = cart.get('total'); // Getting Total Cart
			lsdcommerce_single_cartmanager(total.total_qty, lsdcommerce_currency_format(true, total.total_price));
		}
	});

	// Qty Sub - Buggy
	$(document).on('click', '.minus', function (e) {
		let minusInCart = null;
		let minusInFloat = null;

		controlCartQty = $(this).closest('.lsdc-qty').find('input[name="qty"]'); // Control Qty dynamic based on user click
		productQty = parseInt((controlCartQty.val()) == null ? 1 : controlCartQty.val()); // Force set 1 if empty
		productID = $(this).closest('.item').attr('id'); // Get Product ID

		// Minus in Float Qty not on Cart Manager
		if (productID == undefined) {
			minusInFloat = true;
			productID = $(this).closest('.cart-qty-float').attr('product-id'); // Get Product ID
		} else {
			minusInCart = true; 

		}

		// Decrease Qty on click
		controlCartQty.val(--productQty);

		let product_cart = cart.get('product', productID); //Get Detail Product by ID

		if (minusInFloat && productQty == 0) { // Hold Product if Minus in Float and Cart not Show

			controlCartQty.val(1); // Updating Quantity UI
			lsdcommerce_addto_cart('hold', productID, productQty, productPrice, productTitle);
			$(this).closest('.item').find('.price').text(lsdcommerce_currency_format(true, product_cart.price * 1)); // find price text set new product qty

		} else {

			controlCartQty.val(productQty); // Sync and Set Qty
			$(this).closest('.item').find('.price').text(lsdcommerce_currency_format(true, product_cart.price * productQty)); // Refersh New Price based On Qty
			lsdcommerce_addto_cart('sub', productID, productQty, product_cart.price, productTitle); // Decrease Qty
			
			// Minus in Cart -> Remove Product
			if (minusInCart && productQty == 0) {
				productID = $(this).closest('.item').attr('id'); // Get Product ID
				cart.delete( productID );
				$('.cart-qty-float input').val(0);
				$(this).closest('.item').remove();
			}
		}

		// ---> Updating UI
		let total = cart.get('total');

		if (total == undefined) {
			total = {
				'total_qty': 0
			};
			cartPopup.removeClass('show');
			$('#cart-popup.overlay').trigger('click');
			lsdcommerce_single_cartmanager(total.total_qty, lsdc_pub.translation.cart_empty);
			controlQty.hide();
		} else {
			lsdcommerce_single_cartmanager(total.total_qty, lsdcommerce_currency_format(true, total.total_price));
		}
	});
	

})(jQuery);

// Produk Biasa
// "58":{
// 	"id":"58",
// 	"qty":2,
// 	"title":"Multimeter Digital",
// 	"price":50000,
// 	"thumbnail":"https://tokoalus.com/wp-content/uploads/2020/09/multitester-digital-ukuran-kantong.jpg",
// }}

// Produk Variasi
// "58-hitam-xl":{
// 	"id":"58",
// 	"qty":2,
// 	"title":"Multimeter Digital",
// 	"price":50000,
// 	"thumbnail":"https://tokoalus.com/wp-content/uploads/2020/09/multitester-digital-ukuran-kantong.jpg",
// 	"variations": {
// 		'hitam' : {
// 			'price' : 10000,
// 			'title' : 'Hitam'
// 		},
// 		'ukuran' : {
// 			'price' : 20000,
// 			'title' : 'XL'
// 		}
// 	}
// }}
