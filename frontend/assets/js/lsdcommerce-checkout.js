/*
Public Javascript and JQuery Function
- Ready 
-- Input Handler
-- Tabs Handler
-- Auto Check Cart
- Collapse Handler
- CartManager
-- AddToCart Function
-- AddToCart Handler
-- Quantity Handler
-- Shipping Calculation
-- Recalculate Checkout
*/
(function ($) {
	'use strict';

	// Initialize Variable 
	let cart = new LSDCCookie('_lsdcommerce_cart');
	let checkoutProducts, checkoutExtras, template, data;

	let checkout, customer, shipping = false; //State Flag
	let order_object = {};

	/* Ready function, Initialize */
	$(document).ready(function () {
		checkoutProducts = $('#checkout-products');
		checkoutExtras = $('#checkout-extras');
		template = $('#checkout-summary-template').html();
		data = {};

		checkout = $('#lsdcommerce-checkout');

		// Checkout Form : Floating Label
		$('.floating-label').on('click', 'label', function () {
			$(this).prev('input').focus();
		});

		// Checking Cart
		let carts = cart.get('formatted');
		if (!carts) {
			$('.card-body').remove();
		} else {
			data['items'] = carts;
			// -----> Calculating Product Total
			var productTotal = 0;
			Object.values(carts).forEach(function (item, index, arr) {
				productTotal += lsdcommerce_currency_clear(item.price);
			});
			checkoutProducts.attr('data-total', productTotal);
		}

		// Updating Extras
		lsdcommerce_update_extras();

		// Rendering Summary Templates
		if (template && data) {
			var render = Mustache.to_html(template, data);
			checkoutProducts.html(render);
			setTimeout(() => {
				// Updating Grand Total
				lsdcommerce_update_grandtotal();
			}, 300);
		}

	});

	// Collpas Toggle Function
	$(document).on('click', '[lsdc-toggle="collapse"]', function (e) {
		e.preventDefault();
		let target = $(this).data('target');
		$(target).toggleClass('show');

		if ($(target).hasClass('show')) {
			$(this).attr('aria-expanded', 'true');
		} else {
			$(this).attr('aria-expanded', 'false');
		}
	});

	//---> Customer ( form validation )
	$(document).on('click', '.lsdcommerce-customer', function (e) {
		e.preventDefault();

		let carts = cart.get('formatted');
		if (!carts) {
			location.reload();
		}
		
		let err_name, err_phone, err_email = false;

		let name = checkout.find('input[name="name"]').val();
		let phone = checkout.find('input[name="phone"]').val();
		let email = checkout.find('input[name="email"]').val();

		// Email
		if (email == '' || lsdcommerce_validate_email(email) == false) {
			checkout.find('input[name="email"]').closest('.lsdp-form-group').css('border', '1px solid red');
			err_email = true;
		} else {
			checkout.find('input[name="email"]').closest('.lsdp-form-group').css('border', 'none');
			err_email = false;
		}

		// Phone
		if (phone == '' || lsdcommerce_validate_phone(phone) == false) {
			checkout.find('input[name="phone"]').closest('.lsdp-form-group').css('border', '1px solid red');
			err_phone = true;
		} else {
			checkout.find('input[name="phone"]').closest('.lsdp-form-group').css('border', 'none');
			err_phone = false;
		}

		// Name
		if (name == '') {
			checkout.find('input[name="name"]').closest('.lsdp-form-group').css('border', '1px solid red');
			err_name = true;
		} else {
			checkout.find('input[name="name"]').closest('.lsdp-form-group').css('border', 'none');
			err_name = false;
		}

		// Validation True -> Slide Tab
		if (err_name == false && err_phone == false && err_email == false) {
			// Set Object
			order_object['form'] = {
				"name": name,
				"phone": phone,
				"email": email
			};

			// Next Slide
			lsdc_checkout_nextslide(1);
			customer = true;
		} else {
			lsdcommerce_checkout_notify('Silahkan isi data pembeli dengan benar');
			lsdc_checkout_nextslide(0)
		}
	});

	/**
	 * Shipping ------------------------------------------------------
	 * Change States get Cities
	 * Reset Shipping Services
	 * Handler Click Shipping Services
	 */
	// Resetting Shipping Services
	$(document).on("change", ".shipping-reset", function (e) {
		$('#physical-shipping div').remove();
		lsdcommerce_shipping_package();
	});

	// States : Change, get Cities
	$(document).on("change", "#states", function (e) {
		$.get(lsdc_pub.plugin_url + 'assets/cache/' + $('#country').val() + '-cities.json', function (data, status) {
			$("#cities option").remove();
			$.each(data, function (i, value) {
				if ($('#states').find(":selected").val() == value.province_id) {
					var option = $('<option value="' + value.city_id + '">' + value.type + ' ' + value.city_name + '</option>');
					$("#cities").append(option);
				}
			});
		});
	});

	// City : on User Choose Shipping Package, Push Extra Cost
	$(document).on('change', 'input[type="radio"][name="physical_courier"]', function (e) {
		let state = checkout.find('#states').val();
		let city = checkout.find('#cities').val();
		let address = checkout.find('#shipping_address').val();
		let shipper_id = checkout.find('input[name="physical_courier"]:checked').attr('id');
		let extras = {};

		/* --- PRO Code --- */
		let coupon_code = $('#coupon-form').find('input').val();
		if (coupon_code) {
			extras['coupon'] = coupon_code;
		}
		/* --- PRO Code --- */

		extras['shipping'] = {
			'physical': {
				"service": shipper_id,
				"state": state,
				"city": city,
				"address": address
			}
		};

		lsdcommerce_update_extras(extras);
	});


	//---> Shipping ( shipping validation)
	$(document).on('click', '.lsdcommerce-shipping', function (e) {
		e.preventDefault();
		let email = checkout.find('input[name="email"]').val();
		let shipping_digital, shipping_physical;

		// DIgital Shipping Available
		if ($('#lsdcommerce-shipping-options').find('[name="digital_courier"]').length) {
			let shipper_id = checkout.find('input[name="digital_courier"]:checked').attr('id');
			let receiver = email; //change receiver based on channel user choose

			if (shipper_id && receiver) {
				order_object['shipping'] = {
					'digital': {
						"service": shipper_id,
						"receiver": receiver
					}
				};
				shipping_digital = true;
			}
		}

		// Physical Shipping
		if ($('#lsdcommerce-shipping-options').find('#shipping_address').length) {

			let city_check, address_check = false;
			// Getting Data
			let state = checkout.find('#states').val();
			let city = checkout.find('#cities').val();
			let address = checkout.find('#shipping_address').val();
			let shipper_id = checkout.find('input[name="physical_courier"]:checked').attr('id');

			// City Check
			if (city == false) {
				checkout.find('#cities').css('border', '1px solid red');
			} else {
				checkout.find('#cities').css('border', '1px solid #e5e5e5');
				city_check = true;
			}

			// Address Check
			if (address == false) {
				checkout.find('#shipping_address').css('border', '1px solid red');
			} else {
				checkout.find('#shipping_address').css('border', '1px solid #e5e5e5');
				address_check = true;
			}

			if (state && city_check && address_check && shipper_id) {
				// Set Object
				order_object['shipping'] = {
					'physical': {
						"service": shipper_id,
						"state": state,
						"city": city,
						"address": address
					}
				};
				shipping_physical = true;
			} else {
				checkout.find('#checkout-alert p').text('Masukan data alamat secara benar');
			}
		}

		if (shipping_digital || shipping_physical) {
			lsdc_checkout_nextslide(2);
			shipping = true;
		}
	});


	/**
	 * Checkout ------------------------------------------------------
	 */
	//--> Event :: Updating Summary
	$(document).on("lsdcommerce_update_total", function (e) {
		e.preventDefault();

		var carts = cart.get('formatted');
		var grandtotal = 0;

		// Iterate to Generate GrandTotal
		Object.values(carts).forEach(function (item, index, arr) {
			grandtotal += lsdcommerce_currency_clear(item.price);
		});

		$('#checkout-products').attr('data-total', grandtotal);
		lsdcommerce_update_product_summary();
	});

	//---> Checkout Fired
	$(document).on("click", ".lsdcommerce-create-order", function (e) {
		e.preventDefault();

		// Cart Available
		var carts = cart.get('formatted');
		if (carts.length != 0) {
			// Set Up Product Object
			var products = [];

			Object.values(carts).forEach(function (item, index, arr) {
				if (item.variations) {
					let key = Object.keys(item.variations);
					products.push({
						'id': item.id,
						'qty': item.qty,
						'variations': item.variations[key].id // PRO Code - Don't Delete
					});
				} else {
					products.push({
						'id': item.id,
						'qty': item.qty
					});
				}

			});
		} else {
			location.reload();
		}

		// Customer -- Shipping -- Products = Exist
		if (customer && shipping && products) {
			$(this).addClass('loading'); // Add Loading
			let that = this;

			order_object['products'] = products; // Setup Order Object Products
			order_object['payment'] = $('input[name="payment_method"]:checked').attr('id'); // Gettubg Payment Method

			// Sending, Nonce, Token, OrderObject [ Products, Customer, Shipping, Payment ]
			// console.log( order_object ); // #Debug Order Object
			$.post(lsdc_pub.ajax_url, {
					action: 'lsdcommerce_create_order',
					nonce: $('#checkout-nonce').val(),
					token: lsdc_getCookie('_lsdcommerce_token'),
					order: order_object,
					security: lsdc_pub.ajax_nonce,
				},
				function (response) {
					// Response Failed :: ( Token Expired ) 
					if (response == '_token_expired') {
						lsdcommerce_checkout_notify('Token Kadaluarsa, Halaman akan di perbaharui');
						setTimeout(() => {
							location.reload();
						}, 1600);
					}

					if ($.trim(response) == '_email_registered') {
						lsdcommerce_checkout_notify('Email sudah terdaftar, silahkan masuk');
						$(that).removeClass('loading');
						lsdc_checkout_nextslide(0);
					}

					// Response Success :: Redirect
					response = JSON.parse(response);
					if ( response.code == '_order_created') {
						cart.reset();
						location.href = response.redirect; // Default Thankyou Page
					}

				}).fail(function () {
				// Auto Reload when Error
				alert('Failed, please check your internet');
				location.reload();
			});
		} else { //Validation False = Back to First Slide
			lsdc_checkout_nextslide(0);
			checkout.find('#checkout-alert p').text(lsdc_pub.translation.data_incorrect);
		}

	});

})(jQuery);