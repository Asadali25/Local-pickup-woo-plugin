<?php


//CHANGE TEXT ON CHECKOUT
// add_filter('gettext', 'translate_reply');
// add_filter('ngettext', 'translate_reply');

function translate_reply($translated) {
$translated = str_ireplace('Способы Доставки', 'Выберите способ доставки', $translated);
$translated = str_ireplace('Оплата и доставка', 'Введите ваши данные', $translated);
$translated = str_ireplace('Пункты выдачи заказов', 'Выберите Пункт выдачи заказов', $translated);
$translated = str_ireplace('PICK-UP POINTS', 'Выберите Пункт выдачи заказов', $translated);
$translated = str_ireplace('ENTER YOUR DETAILS', 'Введите ваши данные', $translated);
	$translated = str_ireplace('Free!', 'Бесплатно', $translated);
	$translated = str_ireplace('SHIPPING', 'Доставка', $translated);
	$translated = str_ireplace('Select Pick-up point on the map. Address will be filled automatically.', 'Выберите ПВЗ на карте. Адрес подставиться автоматически.', $translated);
	$translated = str_ireplace('SUBTOTAL', 'Подытог', $translated);
	$translated = str_ireplace('THE SIZE', 'ВЫБЕРИТЕ РАЗМЕР', $translated);
	
	
return $translated;
}





// Remove Payment Methods 
add_action('woocommerce_review_order_before_payment', 'disable_payment_methods_for_checkout');

function disable_payment_methods_for_checkout() {
    // Check if it's the checkout page
    if (is_checkout() && !is_wc_endpoint_url()) {
        // Disable all payment methods
        remove_all_payment_gateways();
    }
}
function remove_all_payment_gateways() {
    $available_gateways = WC()->payment_gateways->payment_gateways();
    foreach ($available_gateways as $gateway_id => $gateway) {
        unset(WC()->payment_gateways->payment_gateways[$gateway_id]);
    }
}



// REMOVE CHECKOUT FIELDS
add_filter('woocommerce_checkout_fields', 'quadlayers_remove_checkout_fields');
function quadlayers_remove_checkout_fields($fields) {
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_last_name']);
    unset($fields['billing']['billing_email']);
    unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_state']);
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_city']);


    return $fields;
}




// Chane Checkout Fields Placeholders
add_filter( 'woocommerce_checkout_fields', 'customize_woo_checkout_fields' );
  function customize_woo_checkout_fields( $fields ) {

	unset( $fields['shipping']['shipping_last_name'] );
    $fields['shipping']['shipping_first_name']['placeholder'] = 'Full name';
    $fields['shipping']['shipping_first_name']['label'] = 'Full name';

	
    unset( $fields['billing']['billing_last_name'] );
    $fields['billing']['billing_first_name']['placeholder'] = 'Full name';
    $fields['billing']['billing_first_name']['label'] = 'Full name';

    return $fields;

}


//CHANGE PREIORITY FIELDS
add_filter('woocommerce_checkout_fields', 'misha_email_first');
function misha_email_first($checkout_fields) {
    $checkout_fields['billing']['billing_phone']['priority'] = 10;
    $checkout_fields['billing']['billing_address_1']['priority'] = 100;
    return $checkout_fields;
}



// Product thumbnail in checkout
add_filter( 'woocommerce_cart_item_name', 'product_thumbnail_in_checkout', 20, 3 );
function product_thumbnail_in_checkout( $product_name, $cart_item, $cart_item_key ){
    if ( is_checkout() ) {

        $thumbnail   = $cart_item['data']->get_image(array( 60, 60));
        $image_html  = '<div class="product-item-thumbnail">'.$thumbnail.'</div> ';

        $product_name = $image_html . $product_name;
    }
    return $product_name;
}




//CHANGE PLACEHOLDER
add_filter( 'woocommerce_checkout_fields' , 'override_billing_checkout_fields', 20, 1 );
function override_billing_checkout_fields( $fields ) {
    $fields['billing']['billing_phone']['placeholder'] = 'Телефон *';
	$fields['billing']['billing_first_name']['placeholder'] = 'Имя *';
    return $fields;
}



// Disable Order Comment
add_filter( 'woocommerce_checkout_fields' , 'njengah_order_notes' );
function njengah_order_notes( $fields ) {
unset($fields['order']['order_comments']);
return $fields;
}

// Remove "Have a Coupen"
function hide_coupon_field_on_cart( $enabled ) {
    if ( is_checkout() ) {
        $enabled = false;
    }
    return $enabled;
    }
    add_filter( 'woocommerce_coupons_enabled', 'hide_coupon_field_on_cart' );