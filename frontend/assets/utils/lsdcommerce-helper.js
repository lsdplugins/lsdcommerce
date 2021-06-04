/**
 * Set Function for Cookie
 * Source : https://stackoverflow.com/questions/2144386/how-to-delete-a-cookie
 * @param {string} name 
 * @param {string} value 
 * @param {int} days 
 * 
 * usage : lsdc_setCookie( 'name_cookie' , { data:cookie }, 10 ); //10 Days
 */
function lsdc_setCookie(name, value, days) {
    var expires = "";
    var date = new Date();

    if (days) {
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    } else {
        date.setTime(date.getTime() + (0.0001 * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
}

function lsdc_getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function lsdc_eraseCookie(name) {
    lsdc_setCookie(name, null, null);
}

function lsdc_checkVal(obj) {
    if (Object.values(obj).length) {
        return true;
    } else {
        return false;
    }
}

/**
 * Class for CRUD Cookie
 * Using for Cart Purpose
 */
class LSDCCookie {
    constructor(name) {
        this.name = name
    }

    /**
     * Insert and Update data to Cookie, based on Command
     * @param {string} command 
     * @param {object} data 
     */
    set(command, data) {
        var stack = lsdc_getCookie(this.name) ? JSON.parse(lsdc_getCookie(this.name)) : {}; // get and parse from cookie

        if (Object.values(stack).length >= 1) {
            // if data more than 1
            if (!stack[data.id]) // if stack not exist
            {
                stack[data.id] = data; // push new data
            } else // if exist
            {
                //Iterate to Check Item Collision
                Object.values(stack).forEach(function (item, index, arr) {
                    if (item.id == data.id) // if data collision
                    {
                        if (command == 'add') { // on Add
                            item.qty = parseInt(item.qty) + 1;
                        } else if (command == 'hold') {
                            item.qty = parseInt(item.qty);
                        } else if (command == 'sub') {
                            item.qty = parseInt(item.qty) - 1;
                        }

                        // Update New Price
                        if (item.price != data.price) {
                            item.price = data.price;
                        }
                        stack[data.id] = item;
                    }
                });
            }

        } else { // if data one or zero
            stack[data.id] = data; // push new data
        }
        lsdc_setCookie( this.name, JSON.stringify(stack), 10); // saving to local expired 10 days
    }

    /**
     * Getting Cookie Data
     * @param {string} method 
     * @param {int} product_id 
     */
    get(method, product_id = false) {
        let stack = lsdc_getCookie(this.name) ? JSON.parse(lsdc_getCookie(this.name)) : {}; // getting and parse from cookie

        /**
         * Raw :: Raw Data
         * Total :: Total
         * Formatted :: With Formatting
         * Product :: Get Single by ProductID
         */
        switch (method) {
            case 'raw':
                return stack;
                break;

            case 'total':
                // Checking Empty
         
                    if (Object.values(stack).length == 0) return;

                    // Merge Stack
                    let merge_stack = {};
                    if (Object.values(stack).length > 1) { // if more than 1
                        let price = 0;
                        let qty = 0;
                        let data = {};

                        Object.values(stack).forEach(function (item, index, arr) { // loop data
                            if( item.id != undefined ){
                                price += parseInt(item.price) * parseInt(item.qty);
                                qty += parseInt(item.qty);
                                data[item.id] = item.qty;
                            }
                        });

                        merge_stack['total_price'] = price; //calc total price
                        merge_stack['total_qty'] = qty;
                        merge_stack['data_qty'] = data;
                    } else {
                        let data = {};
                        let key = Object.keys(stack)[0];
                        
                        if( key != undefined ){
                            merge_stack['total_price'] = stack[key].price * stack[key].qty; //calc total price
                            merge_stack['total_qty'] = stack[key].qty;
                            data[stack[key].id] = stack[key].qty;
                            merge_stack['data_qty'] = data;
                        }
                   
                }
                return merge_stack;
                break;
            case 'formatted': // with Format Currency
                let ready = [];

                Object.values(stack).forEach(function (item, index, arr) { // loop data
                    if( item.id != undefined ){
                        let price = 0;
                        price += parseInt(item.price) * parseInt(item.qty);
                        item.unit_price = lsdcommerce_currency_format(false, item.price);
                        item.price = lsdcommerce_currency_format(true, price);
                        ready.push(item);
                    }
                });
                return ready;

            case 'product': // by Product ID
                return stack[product_id];
        }
    }

    /**
     * Delete Item by productID
     * @param {int} product_id 
     */
    delete(productID = false) {
        var stack = JSON.parse(lsdc_getCookie(this.name));

        if (productID) {
            delete stack[productID]
            lsdc_setCookie(this.name, JSON.stringify(stack), 7);
        }
    }

    /**
     * ResetCookie
     */
    reset() {
        lsdc_eraseCookie(this.name);
    }
}

// --> Currency - Format
function lsdcommerce_currency_format(symbol = true, number, currency = 'IDR') {

    let currency_rule = [];
    currency_rule['IDR'] = ['Rp ', '.'];
    currency_rule['USD'] = ['$', ','];

    if (isNaN(number)) return 0; // Return Zero if NaN

    number += ''; // convert to string
    let split = number.split('.'); //convert to array
    let string_array = split[0]; //set array 0

    var regex_rule = /(\d+)(\d{3})/; // split every 3 digit
    while (regex_rule.test(string_array)) {
        string_array = string_array.replace(regex_rule, '$1' + currency_rule[currency][1] + '$2'); // formatting and replace
    }

    if (number == 0 || number == '0') {
        if (symbol) {
            return currency_rule[currency][0] + '0';
        } else {
            return 'Gratis';
        }
    } else {
        if (symbol) {
            return currency_rule[currency][0] + string_array;
        } else {
            return string_array;
        }
    }

}

// --> Currency - Clear
function lsdcommerce_currency_clear(formatted) {
    if (formatted == 'Gratis') {
        return 0;
    } else {
        formatted = formatted.toString();
        return parseInt(formatted
            .replace(/[^0-9]/g, "")
            .replace(/\,\./, '')); // Expected : int 10000
    }
}

// --> Check Empt String
function lsdcommerce_empty(str) {
    return (!str || 0 === str.length);
}

// --> Copy Function
function lsdcommerce_copy(element, as) {
    var $temp = jQuery("<input>");
    jQuery("body").append($temp);
    $temp.val(jQuery(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
    jQuery(as).css('background', '#f75d5d').css('border', 'none');
}

//--> Sanitize : String
function lsdcommerce_sanitize(string) {
    if (string) {
        const map = {
            '&': '',
            '<': '',
            '>': '',
            '"': '',
            "'": '',
            "/": '',
            "?": '',
            "(": '',
            ")": '',
        };
        const reg = /[&<>"'/]/ig;
        return string.replace(reg, (match) => (map[match])).replace(/ +(?= )/g, '');
    }
}

//--> Validate : Email
function lsdcommerce_validate_email(email) {
    var email_regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return email_regex.test(String(email).toLowerCase());
}

//--> Validate : Phone
function lsdcommerce_validate_phone(phone_number) {
    var phone_regex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
    return phone_regex.test(phone_number);
}

// Source : https://www.w3resource.com/javascript/form/letters-numbers-field.php
function lsdcommerce_validate_alphanumeric( text ){
    var letters = /^[0-9a-zA-Z]+$/;

    if( text.match(letters) )
    {
        return true;
    }
    else
    {
        return false;
    }
}


/**---------------------------------------------------------------------------------------------- */

/**
 * @block Checkout
 * Swiper Checkout
 */
var swiperTabsContent = null;
var swiperTabsNav = null;

jQuery(window).on("load", function () {
    if (jQuery('body').hasClass('lsdcommerce') == true) {
        swiperTabsNav = new Swiper('.swiper-tabs-nav', {
            spaceBetween: 0,
            slidesPerView: 3,
            loop: false,
            centeredSlides: false,
            loopedSlides: 5,
            autoHeight: false,
            resistanceRatio: 0,
            watchOverflow: true,
            watchSlidesVisibility: true,
            watchSlidesProgress: true,
        });

        // Swiper Content
        swiperTabsContent = new Swiper('.swiper-tabs-content', {
            spaceBetween: 0,
            loop: false,
            autoHeight: true,
            longSwipes: true,
            resistanceRatio: 0, // Disable First and Last Swiper
            watchOverflow: true,
            loopedSlides: 5,
            thumbs: {
                swiper: swiperTabsNav,
            },
            paginationClickable: false,
        });
        // swiperTabsNav.update();

        jQuery(document).on('click', '.lsdc-toggle-collapse, .shipping-reset, input[name="physical_courier"]', function (e) {
            swiperTabsContent.updateAutoHeight();
        });
    }



});

function lsdc_checkout_nextslide(position = 1) {
    swiperTabsContent.slideTo(position);
}

/**
 * @block Checkout
 * @subblock Shipping
 * Loading Shipping Services
 */
function lsdcommerce_shipping_package() {
    let checkout = jQuery('#lsdcommerce-checkout');
    let state = checkout.find('#states').val();
    let city = checkout.find('#cities').val();
    let token = lsdc_getCookie('lsdcommerce_payment');

    let cart = new LSDCCookie('_lsdcommerce_cart')
    var carts = cart.get('formatted');
    let target = {
        'state': state,
        'city': city
    }

    // Shipping Package POST
    jQuery.post(lsdc_pub.ajax_url, {
        action: 'lsdcommerce_shipping_package',
        shipping: {
            'order_key': token,
            'target': target,
            'products': carts
        },
        nonce: jQuery('#checkout-nonce').val(),
        security: lsdc_pub.ajax_nonce,
    }, function (respons_shipping) {

        if (respons_shipping) {
            jQuery('#physical-shipping div').remove();
            jQuery('#physical-shipping').append(respons_shipping);
            jQuery('#physical-shipping input[type="radio"]:first').prop("checked", true).trigger("change");
            // Updating Height SwiperJS
            swiperTabsContent.updateAutoHeight();
        } else {
            // location.reload(); // Fallback Reload
        }
    }).fail(function () {
        alert('Failed, please check your internet');
        return false;
    }).complete(function () {});
}
/**
 * @block Checkout
 * @subblock Order Summary
 * Updating Product Summary
 */
function lsdcommerce_update_product_summary() {
    let cart = new LSDCCookie('_lsdcommerce_cart')
    var carts = cart.get('formatted');

    var template = jQuery('#order-summary-template').html();
    var data = {};
    data['items'] = carts;

    // Rendering Template
    if (template && data) {
        var render = Mustache.to_html(template, data);
        jQuery('#checkout-products').html(render);
        lsdcommerce_update_grandtotal();
    }
}

/**
 * @block Checkout
 * @subblock Order Summary
 * Updating GrandTotal
 */
function lsdcommerce_update_grandtotal() {
    let $product_total = jQuery('#checkout-products').attr('data-total') == undefined ? 0 : jQuery('#checkout-products').attr('data-total');
    let $extra_total = jQuery('#checkout-extras').attr('data-total') == undefined ? 0 : jQuery('#checkout-extras').attr('data-total');

    let $grandtotal = parseInt($product_total) + parseInt($extra_total);
    if (isNaN($grandtotal)) $grandtotal = 0;
    if ( 0 > $grandtotal ) $grandtotal = 0;
    jQuery('#grandtotal').text( lsdcommerce_currency_format(true, $grandtotal) );
}

/**
 * @block Checkout
 * Updating Extras Item based On Coupon, Shipping, Uniqe Code and Extras
 */
function lsdcommerce_update_extras(extras_data) {
    let cart     = new LSDCCookie('_lsdcommerce_cart');
    var products = cart.get('formatted');

    let token = lsdc_getCookie('lsdcommerce_payment');

    // Ajax Can't Callback Causing Async
    callback = jQuery.post(lsdc_pub.ajax_url, {
        action: 'lsdcommerce_checkout_extra_pre',
        extras: {
            'order_key' : token,
            'extras'    : extras_data,
            'products'  : products
        },
        nonce: jQuery('#checkout-nonce').val(),
        security: lsdc_pub.ajax_nonce,
    }, function ( response_extras ) {
        let response = JSON.parse( response_extras );
        if ( response.template ) {
            jQuery('#checkout-extras').remove();
            jQuery('#summary').append( response.template );
        }

		if( response.error ){
            lsdcommerce_checkout_notify( Object.values( response.error ) );
            jQuery('#coupon-form button').text('Apply'); // PRO Code
        }
    }).fail(function () {
        alert('Failed, please check your internet');
        return false;
    }).complete(function () {
        lsdcommerce_update_grandtotal();
    });

}

/**
 * @block Checkout
 * Notification for Checkout
 */
function lsdcommerce_checkout_notify( text, elm = false ){
    if( ! lsdcommerce_empty( text ) ){

        if( elm ){
            elm = elm;
        }else{
            elm = '#lsdcommerce-checkout';
        }
        
        jQuery( elm ).find('#checkout-alert').removeClass('lsdp-hidden');
        jQuery( elm ).find('#checkout-alert p').text( text );
    
        setTimeout(function(){ jQuery(  elm  ).find('#checkout-alert').addClass('lsdp-hidden'); }, 3000);
    }
}

