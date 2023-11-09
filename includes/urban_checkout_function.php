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






// Disable Woocommerce fields
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






    // Get Billing Address from Custom field
    add_filter('woocommerce_checkout_posted_data', 'update_billing_address_from_custom_field');

    function update_billing_address_from_custom_field($posted_data) {

        if (!empty($_POST['pickup_address1'])) {
            // If pickup_address1 is filled, update billing address with it
            $posted_data['billing_address_1'] = $_POST['pickup_address1'];
        } elseif (!empty($_POST['pickup_address2'])) {
            // If pickup_address2 is filled, update billing address with it
            $posted_data['billing_address_1'] = $_POST['pickup_address2'];
        }
    
        return $posted_data;
    }
    






function option_field_validation() {
    // Check if the specific radio button is selected
    if (isset($_POST['customer-selection']) && $_POST['customer-selection'] === 'option_3') {
        // If the radio button is selected, do not perform any validation
        return;
    }

    // Check if both fields are filled
    if (empty($_POST['pickup_address1']) && empty($_POST['pickup_address2'])) {
        wc_add_notice('Please fill one of the pickup address fields.', 'error');
    }
}
add_action('woocommerce_checkout_process', 'option_field_validation');





// Get city name from custom city field
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





// Get City code from custom field from checkout
add_filter('woocommerce_checkout_posted_data', 'update_billing_city_code_from_custom_field');

function update_billing_city_code_from_custom_field($posted_data) {
    if (isset($_POST['city_code'])) {
        // Get the custom city value
        $customCityCode = $_POST['city_code'];

        // Update the billing city with the custom address
        $posted_data['billing_postcode'] = $customCityCode;
    }

    return $posted_data;
}





// Add slashed regular price on checkout products subtotal
function add_slashed_regular_price_on_checkout_subtotal( $subtotal, $cart_item, $cart_item_key ) {
    $product = $cart_item['data'];

    if ( $product->is_on_sale() ) {
        $regular_price = wc_price( $product->get_regular_price() );

        // Create a string with the slashed regular price and the sale price
        $item_price = ' <del>' . $regular_price . '</del>';

        // Append the item price to the subtotal
        $subtotal .= ' ' . $item_price;
    }

    return $subtotal;
}
add_filter( 'woocommerce_cart_item_subtotal', 'add_slashed_regular_price_on_checkout_subtotal', 10, 3 );






// Redirect user to custom thankyou page
add_action('template_redirect', 'custom_thankyou_page_redirect');

function custom_thankyou_page_redirect() {
    if (is_wc_endpoint_url('order-received')) {
        // Replace 'your-custom-thankyou-page' with the slug of your custom "Thank You" page
        $redirect_url = home_url('/order-received/');
        wp_safe_redirect($redirect_url);
        exit;
    }
}






// Create Custom Shotcode to display order ID on Page
function custom_latest_order_number_shortcode() {
    $latest_order = wc_get_orders(array(
        'numberposts' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    if (!empty($latest_order)) {
        return $latest_order[0]->get_order_number();
    } else {
        return 'No orders found';
    }
}

add_shortcode('latest_order_number', 'custom_latest_order_number_shortcode');









// Add slashed regular price on cart items
function woodiscpr_change_cart_table_price_display( $price, $values, $cart_item_key ) {
	$slashed_price = $values['data']->get_price_html();
	$is_on_sale = $values['data']->is_on_sale();
	if ( $is_on_sale ) {
		$price = $slashed_price;
	}
	return $price;
}
add_filter( 'woocommerce_cart_item_price', 'woodiscpr_change_cart_table_price_display', 30, 3 );









// Add discout to the order total
function custom_order_total_html($total_html) {
    $regular_price_total = 0;

    foreach (WC()->cart->get_cart() as $cart_item) {
        $product = $cart_item['data'];
        $regular_price = $product->get_regular_price();
        $regular_price_total += $regular_price;
    }
 $regular_price_total = wc_price($regular_price_total);
    // Format the regular price total
    $final_total = '<del>' . $regular_price_total . '</del>';
    
    // Append the final total to the left side of the order total HTML
    $total_html = $final_total . ' ' . $total_html;

    return $total_html;
}

add_filter('woocommerce_cart_totals_order_total_html', 'custom_order_total_html');

