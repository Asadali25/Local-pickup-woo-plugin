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




// Just hide woocommerce billing country
add_action('woocommerce_before_checkout_form', 'hide_checkout_billing_country', 5);
function hide_checkout_billing_country() {
    echo '<style>#billing_country_field{display:none;}</style>';
}



// Hide Woocommerce fields
add_filter('woocommerce_billing_fields', 'customize_billing_fields', 100);
function customize_billing_fields($fields ) {
    if (is_checkout()) {
        // HERE set the required key fields below
        $chosen_fields = array( 'last_name', 'address_1', 'address_2', 'city', 'postcode', 'country', 'state' , 'email' , 'company');

        foreach ($chosen_fields as $key) {
            if (isset($fields['billing_'.$key]) && $key !== 'country') {
                unset($fields['billing_'.$key]); // Remove all define fields except country
            }
        }
    }
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
    // $checkout_fields['billing']['billing_address_1']['priority'] = 100;
    return $checkout_fields;
}



// Product thumbnail in checkout
add_filter( 'woocommerce_cart_item_name', 'product_thumbnail_in_checkout', 20, 3 );
function product_thumbnail_in_checkout( $product_name, $cart_item, $cart_item_key ){
    if ( is_checkout() ) {

        $thumbnail   = $cart_item['data']->get_image(array( 80, 100));
        $image_html  = '<div class="product-item-thumbnail">'.$thumbnail.'</div> ';

        $product_name = $image_html . $product_name;
    }
    return $product_name;
}




//CHANGE PLACEHOLDER
add_filter( 'woocommerce_checkout_fields' , 'override_billing_checkout_fields', 20, 1 );
function override_billing_checkout_fields( $fields ) {
    $fields['billing']['billing_first_name']['placeholder'] = 'Имя *';
    $fields['billing']['billing_phone']['placeholder'] = 'Телефон *';
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


    // woocommerce input validataion and notice
// Add a notice if the customer doesn't choose an option
function custom_validate_radio_option() {
    if (empty($_POST['customer-selection'])) {
        wc_add_notice('Please choose an option before proceeding to checkout.', 'error');
    }
}
add_action('woocommerce_checkout_process', 'custom_validate_radio_option');







// Add an order note with the customer's selection
function custom_add_order_note($order_id) {
    $customer_selection = isset($_POST['customer-selection']) ? $_POST['customer-selection'] : '';
    
    if (!empty($customer_selection)) {
        $order = wc_get_order($order_id);
        $order->add_order_note('Customer selected: ' . $customer_selection);
    }
}
add_action('woocommerce_new_order', 'custom_add_order_note');





        // woocommerce input validataion and notice
        function custom_validate_city_input_option() {
            if (empty($_POST['city_name'])) {
                wc_add_notice('Please choose a city first.', 'error');
            }
        }
        add_action('woocommerce_checkout_process', 'custom_validate_city_input_option');




add_filter('woocommerce_checkout_posted_data', 'update_billing_address_from_custom_field');

function update_billing_address_from_custom_field($posted_data) {
    if (isset($_POST['billing_address'])) {
        // Get the custom address value
        $customAddress = $_POST['billing_address'];

        // Update the billing address with the custom address
        $posted_data['billing_address_1'] = $customAddress;
    }

    return $posted_data;
}






add_filter('woocommerce_checkout_posted_data', 'update_billing_city_from_custom_field');

function update_billing_city_from_custom_field($posted_data) {
    if (isset($_POST['city_name'])) {
        // Get the custom city value
        $customCity = $_POST['city_name'];

        // Update the billing city with the custom address
        $posted_data['billing_city'] = $customCity;
    }

    return $posted_data;
}
