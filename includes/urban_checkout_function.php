<?php


function custom_free_shipping_label($label, $method) {
    // Check if the method is the Free Shipping method
    if ($method->method_id === 'free_shipping') {
        $label = 'БЕСПЛАТНО!';
    }
    return $label;
}
add_filter('woocommerce_cart_shipping_method_full_label', 'custom_free_shipping_label', 10, 2);


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
add_filter( 'woocommerce_cart_item_name', 'bbloomer_product_image_review_order_checkout', 9999, 3 );

function bbloomer_product_image_review_order_checkout( $name, $cart_item, $cart_item_key ) {
    if ( ! is_checkout() ) return $name;
    $product = $cart_item['data'];
    $thumbnail = $product->get_image( array( '80', '100' ), array( 'class' => 'alignleft' ) );

    // Wrap the product name in a <span> tag with a class
    $productNameHTML = '<span class="woo-product-name">' . $name . '</span>';
    
    return $thumbnail . $productNameHTML;
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
// function ask_manager_field_validation() {
//     if (empty($_POST['billing_address'])) {
//         wc_add_notice('Please Enter Address or Choose From Map.', 'error');
//     }
// }
// add_action('woocommerce_checkout_process', 'ask_manager_field_validation');





// Add order note for custom Radio
add_action('woocommerce_new_order', 'custom_radio_order_notes');

function custom_radio_order_notes($order_id) {
    // Get the order object
    $order = wc_get_order($order_id);

    // Check if the order exists
    if ($order) {
        // Get the selected radio option
        $radio_option = sanitize_text_field($_POST['customer-selection']);

        // Add a note to the order
        $order->add_order_note("Customer selected radio option: $radio_option");
    }
}







        // woocommerce input validataion and notice
        function custom_validate_city_input_option() {
            if (empty($_POST['city_name'])) {
                wc_add_notice('Please choose a city first.', 'error');
            }
        }
        add_action('woocommerce_checkout_process', 'custom_validate_city_input_option');






        
add_filter('woocommerce_checkout_posted_data', 'update_billing_address_from_custom_field');

function update_billing_address_from_custom_field($posted_data) {
    if (isset($_POST['pickup_address'])) {
        // Get the custom address value
        $customAddress = $_POST['pickup_address'];

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
