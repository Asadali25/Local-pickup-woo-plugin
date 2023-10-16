<?php

/*
Plugin Name: Urban Custom Checkout
Description: Add Custom Functionality to Checkout Page
Version: 1.0
Author: Asad Ali
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: urban-custom-checkout
*/


// Attatch Files
include(plugin_dir_path(__FILE__) . 'includes/urban_checkout_function.php');



//  _____All Functions______

function urban_custom_checkout_check_woocommerce() {
    // Check if WooCommerce is installed and activated
    if (!class_exists('WooCommerce')) {
        // Deactivate the plugin
        deactivate_plugins(plugin_basename(__FILE__));
        
        // Display a notice to the admin
        add_action('admin_notices', 'urban_custom_checkout_missing_woocommerce_notice');
    }
}

function urban_custom_checkout_missing_woocommerce_notice() {
    ?>
    <div class="error">
        <p><?php _e('Urban Custom Checkout requires WooCommerce to be installed and activated.', 'urban-custom-checkout'); ?></p>
    </div>
    <?php
}

// Enque Scripts
function enqueue_plugin_assets_on_checkout() {
    if (is_checkout() && !is_wc_endpoint_url()) {
        // Enqueue jQuery (if not already loaded)
        wp_enqueue_script('jquery');

        // Enqueue plugin's CSS file
        wp_enqueue_style('urban-checkout', plugin_dir_url(__FILE__) . 'includes/assets/css/checkout.css', array(), '1.0');

        // Enqueue  plugin's JavaScript file
        wp_enqueue_script('urban-checkout', plugin_dir_url(__FILE__) . 'includes/assets/js/checkout.js', array('jquery'), '1.0', true);
    
    }

}

// Inject HTML to Checkout
function urban_custom_checkout_add_live_search_box() {
    ?>
 <input type="text" 
                               id="urban_input" 
                               autocomplete="new-password"
                               placeholder= "Поиск города..."
                               class="urb-input">
                               
      			
                        <ul class="urb-dropdown" id="urban_dropdown"></ul>
<div class="level2">
    <div class="ltext">
<svg width="21" height="21" viewBox="0 0 21 21" fill="none" class="lvl2" xmlns="http://www.w3.org/2000/svg">
<path d="M6.52998 16L11.495 10.705C11.725 10.455 11.92 10.21 12.08 9.97C12.25 9.72 12.375 9.47 12.455 9.22C12.545 8.96 12.59 8.69 12.59 8.41C12.59 8.18 12.545 7.955 12.455 7.735C12.365 7.515 12.23 7.315 12.05 7.135C11.88 6.955 11.67 6.81 11.42 6.7C11.17 6.59 10.885 6.535 10.565 6.535C10.115 6.535 9.72498 6.64 9.39498 6.85C9.07498 7.05 8.82998 7.345 8.65998 7.735C8.48998 8.115 8.40498 8.57 8.40498 9.1H7.12998C7.12998 8.35 7.26498 7.695 7.53498 7.135C7.80498 6.565 8.19498 6.125 8.70498 5.815C9.22498 5.495 9.84498 5.335 10.565 5.335C11.145 5.335 11.645 5.435 12.065 5.635C12.485 5.825 12.83 6.075 13.1 6.385C13.37 6.685 13.57 7.01 13.7 7.36C13.83 7.71 13.895 8.045 13.895 8.365C13.895 8.905 13.765 9.44 13.505 9.97C13.245 10.5 12.91 10.975 12.5 11.395L9.15498 14.8H13.94V16H6.52998Z" fill="#2B2B2B"/>
<circle cx="10.5" cy="10.5" r="10" stroke="#2B2B2B"/>
</svg><h2 class="billing-lv2"> Выберите способ Доставки</h2>
</div>
        <div class="lvl2-opt1" id="l2option_1">
            <input type="radio" name="customer-selection" id="radio1" value="option_1">
            <label for="radio1">САМОВЫВОЗ СДЭК, 0 руб.</label>
            <p class="label1-text">Из удобного Пункта Выдачи с отдельной примерочной, <span>2-3 дня</span></p>
            
        </div>
        <div class="lvl2-opt2" id="l2option_2">
            <input type="radio" name="customer-selection" id="radio2" value="option_2">
            <label for="radio2">Курьер сдэк, 0 Руб.</label>
            <p class="label2-text">К вам домой, на дачу или в офис, <span> 2-3 дня</span></p>
            <input type="text" name="billing_address" id="billingAddress" class="billing_adr" placeholder="Номер дома и название улицы">
        </div>
        <div class="lvl2-opt3" id="l2option_3">
            <input type="radio" name="customer-selection" id="radio3" value="option_3">
            <label for="radio3">ПУСТЬ МЕНЕДЖЕР ПОДСКАЖЕТ</label>
            <p class="label3-text">Подберем самое удобное для вас, <span>2-3 дня</span></p>
        </div>

    </div>
    <?php
}




//    _______Add Actions____________

add_action('wp_enqueue_scripts', 'enqueue_plugin_assets_on_checkout');
add_action('wp_ajax_live_search', 'live_search_callback');
add_action('wp_ajax_nopriv_live_search', 'live_search_callback'); // Allow for non-logged-in users
add_action('woocommerce_after_checkout_billing_form', 'urban_custom_checkout_add_live_search_box'); // Add live search box
add_action('admin_init', 'urban_custom_checkout_check_woocommerce');



