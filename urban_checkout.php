<?php

/*
Plugin Name: Urban Custom Checkout
Description: Add Custom Functionality to Checkout Page
Version: 15.1
Author: Asad Ali
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: urban-custom-checkout
*/


// Attatch Files
include(plugin_dir_path(__FILE__) . 'includes/urban_checkout_function.php');
include(plugin_dir_path(__FILE__) . 'includes/pickup_api.php');


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
        wp_enqueue_style('urban-style', plugin_dir_url(__FILE__) . 'includes/assets/css/checkout.css', array(), '1.0');
        
        // Enqueue  plugin's JavaScript file
        wp_enqueue_script('urban-js', plugin_dir_url(__FILE__) . 'includes/assets/js/checkout.js', array('jquery'), '1.0', true);
        wp_enqueue_script('modal-js', plugin_dir_url(__FILE__) . 'includes/assets/js/modal.js', array('jquery'), '1.0', true);
        wp_enqueue_script( 'yandex-api', 'https://api-maps.yandex.ru/2.1/?apikey=3e15e208-e30c-4c17-8d6a-3153adf790c2&lang=en_US', array('jquery'), '5.1.3', true );
        wp_enqueue_script( 'yandex-api-jquery', 'https://yandex.st/jquery/2.2.3/jquery.min.js', array('jquery'), '2.2.3', true );

    }

}


// Inject HTML to Checkout
function urban_custom_checkout_add_live_search_box() {
    ?>
 <input type="text" 
                               id="urban_input" 
                               autocomplete="new-password"
                               placeholder= "Город*"
                               name="city_name"
                               class="urb-input">
                               
      			
                        <ul class="urb-dropdown" id="urban_dropdown"></ul>
<div class="level2" id="levelid2">
    <div class="ltext">
<svg width="21" height="21" viewBox="0 0 21 21" fill="none" class="lvl2" xmlns="http://www.w3.org/2000/svg">
<path d="M6.52998 16L11.495 10.705C11.725 10.455 11.92 10.21 12.08 9.97C12.25 9.72 12.375 9.47 12.455 9.22C12.545 8.96 12.59 8.69 12.59 8.41C12.59 8.18 12.545 7.955 12.455 7.735C12.365 7.515 12.23 7.315 12.05 7.135C11.88 6.955 11.67 6.81 11.42 6.7C11.17 6.59 10.885 6.535 10.565 6.535C10.115 6.535 9.72498 6.64 9.39498 6.85C9.07498 7.05 8.82998 7.345 8.65998 7.735C8.48998 8.115 8.40498 8.57 8.40498 9.1H7.12998C7.12998 8.35 7.26498 7.695 7.53498 7.135C7.80498 6.565 8.19498 6.125 8.70498 5.815C9.22498 5.495 9.84498 5.335 10.565 5.335C11.145 5.335 11.645 5.435 12.065 5.635C12.485 5.825 12.83 6.075 13.1 6.385C13.37 6.685 13.57 7.01 13.7 7.36C13.83 7.71 13.895 8.045 13.895 8.365C13.895 8.905 13.765 9.44 13.505 9.97C13.245 10.5 12.91 10.975 12.5 11.395L9.15498 14.8H13.94V16H6.52998Z" fill="#2B2B2B"/>
<circle cx="10.5" cy="10.5" r="10" stroke="#2B2B2B"/>
</svg><h2 class="billing-lv2"> Выберите способ Доставки</h2>
</div>
        <div class="lvl2-opt1" id="l2option_1">
           <label class="custom-radio-btn"> <input type="radio" name="customer-selection" id="radio1" value="option_1">
            САМОВЫВОЗ СДЭК, 0 руб. <span class="checkmark"></span></label>
            <p class="label1-text">Из удобного Пункта Выдачи с отдельной примерочной, <span>2-3 дня</span></p>
            <button type="button" id="openModal" disabled >Выбрать пункт выдачи</button> <br>
            <input type="text" name="pickup_address" id="pickupAddress" class="pickup_adr" readonly >

            
        </div>
        <div class="lvl2-opt2" id="l2option_2">
           <label class="custom-radio-btn"> <input type="radio" name="customer-selection" id="radio2" value="option_2" checked>
            Курьер сдэк, 0 Руб. <span class="checkmark"></span></label>
            <p class="label2-text">К вам домой, на дачу или в офис, <span> 2-3 дня</span></p>
            <input type="text" name="billing_address" id="billingAddress" class="billing_adr" placeholder="Номер дома и название улицы">
        </div>
        <div class="lvl2-opt3" id="l2option_3">
            <label class="custom-radio-btn"><input type="radio" name="customer-selection" id="radio3" value="option_3">
            ПУСТЬ МЕНЕДЖЕР ПОДСКАЖЕТ <span class="checkmark"></span></label>
            <p class="label3-text">Подберем самое удобное для вас, <span>2-3 дня</span></p>
            <input type="text" class="citycodeinput" id="city_code_input" name = "city_code" readonly >
        </div>

    </div>
    <?php
}


function custom_html_for_modal(){
    ?>
        <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal"><svg width="66" height="19" viewBox="0 0 66 19" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0.646446 9.14645C0.451184 9.34171 0.451184 9.65829 0.646446 9.85355L3.82843 13.0355C4.02369 13.2308 4.34027 13.2308 4.53553 13.0355C4.7308 12.8403 4.7308 12.5237 4.53553 12.3284L1.70711 9.5L4.53553 6.67157C4.7308 6.47631 4.7308 6.15973 4.53553 5.96447C4.34027 5.7692 4.02369 5.7692 3.82843 5.96447L0.646446 9.14645ZM15 9L1 9L1 10L15 10L15 9Z" fill="#2B2B2B"/>
<path d="M23.032 9.56V8.28H30.712V9.56H23.032ZM29.8 3.8H31.16V15H29.8V3.8ZM22.28 3.8H23.64V15H22.28V3.8ZM34.2135 12.84C34.2135 13.096 34.2828 13.3147 34.4215 13.496C34.5602 13.6667 34.7468 13.8 34.9815 13.896C35.2162 13.9813 35.4775 14.024 35.7655 14.024C36.1922 14.024 36.5708 13.96 36.9015 13.832C37.2322 13.704 37.4882 13.5013 37.6695 13.224C37.8615 12.9467 37.9575 12.5787 37.9575 12.12L38.2135 12.92C38.2135 13.3893 38.0855 13.7947 37.8295 14.136C37.5842 14.4667 37.2535 14.7227 36.8375 14.904C36.4215 15.0747 35.9575 15.16 35.4455 15.16C34.9868 15.16 34.5655 15.0747 34.1815 14.904C33.7975 14.7333 33.4882 14.4827 33.2535 14.152C33.0295 13.8107 32.9175 13.3893 32.9175 12.888C32.9175 12.3653 33.0455 11.9227 33.3015 11.56C33.5575 11.1973 33.9202 10.92 34.3895 10.728C34.8588 10.5253 35.4135 10.424 36.0535 10.424C36.5228 10.424 36.9228 10.488 37.2535 10.616C37.5948 10.7333 37.8668 10.872 38.0695 11.032C38.2722 11.1813 38.4108 11.304 38.4855 11.4V12.152C38.1228 11.896 37.7548 11.7093 37.3815 11.592C37.0082 11.464 36.5868 11.4 36.1175 11.4C35.6908 11.4 35.3335 11.4587 35.0455 11.576C34.7682 11.6933 34.5602 11.8587 34.4215 12.072C34.2828 12.2853 34.2135 12.5413 34.2135 12.84ZM34.2135 9.368L33.5895 8.392C33.8668 8.18933 34.2402 7.98667 34.7095 7.784C35.1788 7.58133 35.7442 7.48 36.4055 7.48C36.9708 7.48 37.4615 7.57067 37.8775 7.752C38.3042 7.92267 38.6348 8.17333 38.8695 8.504C39.1148 8.83467 39.2375 9.24 39.2375 9.72V15H37.9575V9.72C37.9575 9.34667 37.7975 9.06933 37.4775 8.888C37.1682 8.696 36.8108 8.6 36.4055 8.6C36.0535 8.6 35.7335 8.648 35.4455 8.744C35.1575 8.82933 34.9068 8.936 34.6935 9.064C34.4908 9.18133 34.3308 9.28267 34.2135 9.368ZM44.0111 11.32V10.52C44.5018 10.52 44.8538 10.4293 45.0671 10.248C45.2805 10.056 45.3871 9.82667 45.3871 9.56C45.3871 9.272 45.2805 9.048 45.0671 8.888C44.8538 8.71733 44.5551 8.632 44.1711 8.632C43.8511 8.632 43.5845 8.696 43.3711 8.824C43.1685 8.94133 43.0191 9.09067 42.9231 9.272C42.8271 9.44267 42.7791 9.61333 42.7791 9.784H41.4831C41.4831 9.368 41.6005 8.984 41.8351 8.632C42.0698 8.28 42.3898 8.00267 42.7951 7.8C43.2005 7.58667 43.6591 7.48 44.1711 7.48C44.6511 7.48 45.0831 7.57067 45.4671 7.752C45.8511 7.93333 46.1498 8.17333 46.3631 8.472C46.5871 8.77067 46.6991 9.10133 46.6991 9.464C46.6991 9.99733 46.4591 10.44 45.9791 10.792C45.5098 11.144 44.8538 11.32 44.0111 11.32ZM44.1391 15.16C43.2751 15.16 42.5818 14.9253 42.0591 14.456C41.5471 13.9867 41.2485 13.3947 41.1631 12.68H42.4591C42.5338 13.032 42.6938 13.3467 42.9391 13.624C43.1845 13.9013 43.5845 14.04 44.1391 14.04C44.4805 14.04 44.7685 13.992 45.0031 13.896C45.2485 13.8 45.4351 13.6613 45.5631 13.48C45.6911 13.2987 45.7551 13.0907 45.7551 12.856C45.7551 12.6 45.6858 12.3813 45.5471 12.2C45.4191 12.0187 45.2218 11.88 44.9551 11.784C44.6991 11.688 44.3845 11.64 44.0111 11.64V10.84C44.9285 10.84 45.6591 11 46.2031 11.32C46.7471 11.64 47.0191 12.1467 47.0191 12.84C47.0191 13.32 46.8858 13.736 46.6191 14.088C46.3631 14.4293 46.0165 14.696 45.5791 14.888C45.1418 15.0693 44.6618 15.16 44.1391 15.16ZM49.5104 12.84C49.5104 13.096 49.5797 13.3147 49.7184 13.496C49.857 13.6667 50.0437 13.8 50.2784 13.896C50.513 13.9813 50.7744 14.024 51.0624 14.024C51.489 14.024 51.8677 13.96 52.1984 13.832C52.529 13.704 52.785 13.5013 52.9664 13.224C53.1584 12.9467 53.2544 12.5787 53.2544 12.12L53.5104 12.92C53.5104 13.3893 53.3824 13.7947 53.1264 14.136C52.881 14.4667 52.5504 14.7227 52.1344 14.904C51.7184 15.0747 51.2544 15.16 50.7424 15.16C50.2837 15.16 49.8624 15.0747 49.4784 14.904C49.0944 14.7333 48.785 14.4827 48.5504 14.152C48.3264 13.8107 48.2144 13.3893 48.2144 12.888C48.2144 12.3653 48.3424 11.9227 48.5984 11.56C48.8544 11.1973 49.217 10.92 49.6864 10.728C50.1557 10.5253 50.7104 10.424 51.3504 10.424C51.8197 10.424 52.2197 10.488 52.5504 10.616C52.8917 10.7333 53.1637 10.872 53.3664 11.032C53.569 11.1813 53.7077 11.304 53.7824 11.4V12.152C53.4197 11.896 53.0517 11.7093 52.6784 11.592C52.305 11.464 51.8837 11.4 51.4144 11.4C50.9877 11.4 50.6304 11.4587 50.3424 11.576C50.065 11.6933 49.857 11.8587 49.7184 12.072C49.5797 12.2853 49.5104 12.5413 49.5104 12.84ZM49.5104 9.368L48.8864 8.392C49.1637 8.18933 49.537 7.98667 50.0064 7.784C50.4757 7.58133 51.041 7.48 51.7024 7.48C52.2677 7.48 52.7584 7.57067 53.1744 7.752C53.601 7.92267 53.9317 8.17333 54.1664 8.504C54.4117 8.83467 54.5344 9.24 54.5344 9.72V15H53.2544V9.72C53.2544 9.34667 53.0944 9.06933 52.7744 8.888C52.465 8.696 52.1077 8.6 51.7024 8.6C51.3504 8.6 51.0304 8.648 50.7424 8.744C50.4544 8.82933 50.2037 8.936 49.9904 9.064C49.7877 9.18133 49.6277 9.28267 49.5104 9.368ZM60.7 9.848L58.54 14.52H57.1L60.7 7.08L64.3 14.52H62.86L60.7 9.848ZM63.98 15H57.5V16.44H56.14V13.8H65.26V16.44H63.98V15Z" fill="#2B2B2B"/>
</svg></span>
            <div class="address">
              <div class="search-content">
                  <h3 class = "modal_title">ВЫБЕРИТЕ ПУНКТ</h3>
                  <span class = "size-svg"><svg width="143" height="43" viewBox="0 0 143 43" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M19.6519 17.6818C19.6519 16.891 20.3651 16.25 21.2449 16.25C22.1247 16.25 22.8379 16.891 22.8379 17.6818C22.8379 18.054 22.7035 18.393 22.4694 18.6476C22.0211 19.1353 21.3511 19.6265 21.3511 20.2591V20.4724M21.3511 20.4724C21.8998 20.466 22.4506 20.6184 22.9134 20.9304L28.4878 24.6882C29.4675 25.3487 28.9478 26.75 27.7231 26.75H15.2769C14.0654 26.75 13.5374 25.3736 14.4915 24.7025L19.8133 20.959C20.2651 20.6412 20.807 20.4788 21.3511 20.4724Z" stroke="#2B2B2B" stroke-linecap="round"/>
<path d="M37.4081 21.4C37.4081 22.264 37.6001 23.0267 37.9841 23.688C38.3788 24.3493 38.8908 24.8667 39.5201 25.24C40.1495 25.6133 40.8321 25.8 41.5681 25.8C42.1015 25.8 42.5868 25.7307 43.0241 25.592C43.4721 25.4533 43.8775 25.2613 44.2401 25.016C44.6028 24.76 44.9121 24.4667 45.1681 24.136V25.928C44.6988 26.344 44.1868 26.6533 43.6321 26.856C43.0775 27.0587 42.3895 27.16 41.5681 27.16C40.7895 27.16 40.0588 27.0213 39.3761 26.744C38.7041 26.456 38.1121 26.056 37.6001 25.544C37.0881 25.0213 36.6881 24.408 36.4001 23.704C36.1121 23 35.9681 22.232 35.9681 21.4C35.9681 20.568 36.1121 19.8 36.4001 19.096C36.6881 18.392 37.0881 17.784 37.6001 17.272C38.1121 16.7493 38.7041 16.3493 39.3761 16.072C40.0588 15.784 40.7895 15.64 41.5681 15.64C42.3895 15.64 43.0775 15.7413 43.6321 15.944C44.1868 16.1467 44.6988 16.456 45.1681 16.872V18.664C44.9121 18.3333 44.6028 18.0453 44.2401 17.8C43.8775 17.544 43.4721 17.3467 43.0241 17.208C42.5868 17.0693 42.1015 17 41.5681 17C40.8321 17 40.1495 17.1867 39.5201 17.56C38.8908 17.9333 38.3788 18.4507 37.9841 19.112C37.6001 19.7627 37.4081 20.5253 37.4081 21.4ZM58.2781 19.64V27H56.9981V20.84H53.5581V27H52.2781V19.64H58.2781ZM61.8844 30.52H60.6044V19.64H61.8844V30.52ZM68.0444 23.32C68.0444 24.1093 67.879 24.792 67.5484 25.368C67.2177 25.944 66.775 26.3867 66.2204 26.696C65.6764 27.0053 65.0684 27.16 64.3964 27.16C63.7884 27.16 63.2497 27.0053 62.7804 26.696C62.3217 26.3867 61.959 25.944 61.6924 25.368C61.4364 24.792 61.3084 24.1093 61.3084 23.32C61.3084 22.52 61.4364 21.8373 61.6924 21.272C61.959 20.696 62.3217 20.2533 62.7804 19.944C63.2497 19.6347 63.7884 19.48 64.3964 19.48C65.0684 19.48 65.6764 19.6347 66.2204 19.944C66.775 20.2533 67.2177 20.696 67.5484 21.272C67.879 21.8373 68.0444 22.52 68.0444 23.32ZM66.7484 23.32C66.7484 22.7547 66.631 22.2747 66.3964 21.88C66.1724 21.4853 65.8684 21.1867 65.4844 20.984C65.111 20.7813 64.695 20.68 64.2364 20.68C63.863 20.68 63.495 20.7813 63.1324 20.984C62.7697 21.1867 62.471 21.4853 62.2364 21.88C62.0017 22.2747 61.8844 22.7547 61.8844 23.32C61.8844 23.8853 62.0017 24.3653 62.2364 24.76C62.471 25.1547 62.7697 25.4533 63.1324 25.656C63.495 25.8587 63.863 25.96 64.2364 25.96C64.695 25.96 65.111 25.8587 65.4844 25.656C65.8684 25.4533 66.1724 25.1547 66.3964 24.76C66.631 24.3653 66.7484 23.8853 66.7484 23.32ZM75.9694 20.92L69.8894 27.56L69.9694 25.72L76.0494 19.08L75.9694 20.92ZM71.1694 19.64V25.8L69.8894 27.56V19.64H71.1694ZM76.0494 19.08V27H74.7694V20.84L76.0494 19.08ZM85.5201 27L84.3201 21.16L85.0401 19.08L86.9601 27H85.5201ZM81.6801 25.896L85.0401 19.08L85.2001 21.64L82.1601 27.496L81.6801 25.896ZM82.1601 27.496L79.1681 21.64L79.3281 19.08L82.6401 25.896L82.1601 27.496ZM77.4081 27L79.3281 19.08L80.0481 21.16L78.8481 27H77.4081ZM88.4229 23.64V22.552H93.3349C93.2922 22.168 93.1855 21.832 93.0149 21.544C92.8549 21.2453 92.6255 21.016 92.3269 20.856C92.0389 20.6853 91.6815 20.6 91.2549 20.6C90.8282 20.6 90.4389 20.6907 90.0869 20.872C89.7349 21.0533 89.4522 21.3147 89.2389 21.656C89.0362 21.9867 88.9349 22.3813 88.9349 22.84L88.9029 23.32C88.9029 23.8853 88.9989 24.3653 89.1909 24.76C89.3829 25.1547 89.6549 25.4533 90.0069 25.656C90.3589 25.8587 90.7749 25.96 91.2549 25.96C91.6175 25.96 91.9375 25.9067 92.2149 25.8C92.5029 25.6827 92.7589 25.528 92.9829 25.336C93.2069 25.1333 93.3989 24.8987 93.5589 24.632L94.5989 25.288C94.3535 25.6613 94.0815 25.992 93.7829 26.28C93.4842 26.5573 93.1215 26.776 92.6949 26.936C92.2682 27.0853 91.7349 27.16 91.0949 27.16C90.4335 27.16 89.8362 26.9947 89.3029 26.664C88.7802 26.3333 88.3642 25.88 88.0549 25.304C87.7562 24.7173 87.6069 24.056 87.6069 23.32C87.6069 23.1813 87.6122 23.048 87.6229 22.92C87.6335 22.792 87.6495 22.664 87.6709 22.536C87.7775 21.928 87.9909 21.3947 88.3109 20.936C88.6309 20.4773 89.0415 20.12 89.5429 19.864C90.0549 19.608 90.6255 19.48 91.2549 19.48C91.9482 19.48 92.5562 19.6347 93.0789 19.944C93.6015 20.2533 94.0069 20.6907 94.2949 21.256C94.5829 21.8107 94.7269 22.4773 94.7269 23.256C94.7269 23.32 94.7269 23.384 94.7269 23.448C94.7269 23.512 94.7215 23.576 94.7109 23.64H88.4229ZM97.6813 30.52H96.4013V19.64H97.6813V30.52ZM103.841 23.32C103.841 24.1093 103.676 24.792 103.345 25.368C103.015 25.944 102.572 26.3867 102.017 26.696C101.473 27.0053 100.865 27.16 100.193 27.16C99.5853 27.16 99.0466 27.0053 98.5773 26.696C98.1186 26.3867 97.7559 25.944 97.4893 25.368C97.2333 24.792 97.1053 24.1093 97.1053 23.32C97.1053 22.52 97.2333 21.8373 97.4893 21.272C97.7559 20.696 98.1186 20.2533 98.5773 19.944C99.0466 19.6347 99.5853 19.48 100.193 19.48C100.865 19.48 101.473 19.6347 102.017 19.944C102.572 20.2533 103.015 20.696 103.345 21.272C103.676 21.8373 103.841 22.52 103.841 23.32ZM102.545 23.32C102.545 22.7547 102.428 22.2747 102.193 21.88C101.969 21.4853 101.665 21.1867 101.281 20.984C100.908 20.7813 100.492 20.68 100.033 20.68C99.6599 20.68 99.2919 20.7813 98.9293 20.984C98.5666 21.1867 98.2679 21.4853 98.0333 21.88C97.7986 22.2747 97.6813 22.7547 97.6813 23.32C97.6813 23.8853 97.7986 24.3653 98.0333 24.76C98.2679 25.1547 98.5666 25.4533 98.9293 25.656C99.2919 25.8587 99.6599 25.96 100.033 25.96C100.492 25.96 100.908 25.8587 101.281 25.656C101.665 25.4533 101.969 25.1547 102.193 24.76C102.428 24.3653 102.545 23.8853 102.545 23.32ZM105.526 19.64H106.806V27H105.526V19.64ZM109.526 19.64H111.126L107.926 22.68L111.446 27H109.846L106.326 22.68L109.526 19.64ZM111.779 23.32C111.779 22.5733 111.944 21.912 112.275 21.336C112.616 20.76 113.075 20.3067 113.651 19.976C114.227 19.6453 114.872 19.48 115.587 19.48C116.312 19.48 116.957 19.6453 117.523 19.976C118.099 20.3067 118.552 20.76 118.883 21.336C119.224 21.912 119.395 22.5733 119.395 23.32C119.395 24.056 119.224 24.7173 118.883 25.304C118.552 25.88 118.099 26.3333 117.523 26.664C116.957 26.9947 116.312 27.16 115.587 27.16C114.872 27.16 114.227 26.9947 113.651 26.664C113.075 26.3333 112.616 25.88 112.275 25.304C111.944 24.7173 111.779 24.056 111.779 23.32ZM113.075 23.32C113.075 23.832 113.181 24.2853 113.395 24.68C113.619 25.0747 113.917 25.3893 114.291 25.624C114.675 25.848 115.107 25.96 115.587 25.96C116.067 25.96 116.493 25.848 116.867 25.624C117.251 25.3893 117.549 25.0747 117.763 24.68C117.987 24.2853 118.099 23.832 118.099 23.32C118.099 22.808 117.987 22.3547 117.763 21.96C117.549 21.5547 117.251 21.24 116.867 21.016C116.493 20.792 116.067 20.68 115.587 20.68C115.107 20.68 114.675 20.792 114.291 21.016C113.917 21.24 113.619 21.5547 113.395 21.96C113.181 22.3547 113.075 22.808 113.075 23.32ZM127.313 20.92L121.233 27.56L121.313 25.72L127.393 19.08L127.313 20.92ZM122.513 19.64V25.8L121.233 27.56V19.64H122.513ZM127.393 19.08V27H126.113V20.84L127.393 19.08ZM121.825 15.88H122.961C122.961 16.0187 122.998 16.184 123.073 16.376C123.148 16.5573 123.286 16.7227 123.489 16.872C123.692 17.0107 123.98 17.08 124.353 17.08C124.726 17.08 125.014 17.0107 125.217 16.872C125.42 16.7227 125.558 16.5573 125.633 16.376C125.708 16.184 125.745 16.0187 125.745 15.88H126.881C126.881 16.2427 126.785 16.6 126.593 16.952C126.412 17.2933 126.134 17.576 125.761 17.8C125.388 18.0133 124.918 18.12 124.353 18.12C123.798 18.12 123.329 18.0133 122.945 17.8C122.572 17.576 122.289 17.2933 122.097 16.952C121.916 16.6 121.825 16.2427 121.825 15.88Z" fill="black"/>
<rect x="0.5" y="0.5" width="142" height="42" stroke="#2B2B2B"/>
</svg>
</span>
                  <input type="text" id="searchInput" placeholder="Поиск">
                  <div class="cities_search_results">
                  <ul id="searchResults"></ul>
                  </div>
                  <div id="popup">
                    <button id="close-button">×</button>
                    <div id="popup-content">
                      This is the popup content.
                    </div>
                  </div>                
              </div>
              <div class="map" id="map"></div>
          </div>
        </div>
    </div
    <?php
}

function custom_html_before_order(){
?>
 <div class = "order_row1">
<div class="row1_svg">
<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
  <circle cx="10.5" cy="10.5" r="8.75" stroke="#1C274C" stroke-width="1.5"/>
  <path d="M7.4375 10.9375L9.1875 12.6875L13.5625 8.3125" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</div>
<div class="row1_text">
Оплата после осмотра и примерки   
</div>
</div>

<div class="order_row2">
<div class="row2_svg">
<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
  <path d="M10.5 7V10.5L12.6875 12.6875" stroke="#2B2B2B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M4.90362 4.90362L4.37329 4.37329L4.37329 4.37329L4.90362 4.90362ZM3.79554 6.01171L3.04555 6.01547C3.04762 6.42674 3.3805 6.75963 3.79177 6.7617L3.79554 6.01171ZM6.01913 6.77289C6.43334 6.77497 6.77081 6.44088 6.77289 6.02667C6.77497 5.61246 6.44088 5.27499 6.02667 5.27291L6.01913 6.77289ZM4.53434 3.78058C4.53226 3.36637 4.19479 3.03228 3.78058 3.03436C3.36637 3.03644 3.03228 3.37391 3.03436 3.78812L4.53434 3.78058ZM3.44116 9.44954C3.49741 9.03916 3.21034 8.66088 2.79996 8.60463C2.38958 8.54838 2.01131 8.83546 1.95506 9.24584L3.44116 9.44954ZM16.5707 4.42926C13.1921 1.05061 7.73109 1.0155 4.37329 4.37329L5.43395 5.43395C8.19593 2.67197 12.7071 2.68698 15.5101 5.48992L16.5707 4.42926ZM4.42926 16.5707C7.80791 19.9494 13.2689 19.9845 16.6267 16.6267L15.566 15.566C12.8041 18.328 8.29286 18.313 5.48992 15.5101L4.42926 16.5707ZM16.6267 16.6267C19.9845 13.2689 19.9494 7.80791 16.5707 4.42926L15.5101 5.48992C18.313 8.29286 18.328 12.8041 15.566 15.566L16.6267 16.6267ZM4.37329 4.37329L3.26521 5.48138L4.32587 6.54204L5.43395 5.43395L4.37329 4.37329ZM3.79177 6.7617L6.01913 6.77289L6.02667 5.27291L3.79931 5.26171L3.79177 6.7617ZM4.54553 6.00794L4.53434 3.78058L3.03436 3.78812L3.04555 6.01547L4.54553 6.00794ZM1.95506 9.24584C1.59996 11.8365 2.42693 14.5684 4.42926 16.5707L5.48992 15.5101C3.83087 13.851 3.14753 11.5918 3.44116 9.44954L1.95506 9.24584Z" fill="#2B2B2B"/>
</svg>
</div>
<div class="row2_text">
    Бесплатный и легкий возврат и обмен
</div>
</div>



<?php
}

function custom_html_after_order_btn(){
?>
<div class="phone_block">
<div class="phone_svg">
<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
  <path d="M27 4C27 4 31.6669 4.42426 37.6066 10.364C43.5463 16.3037 43.9706 20.9706 43.9706 20.9706" stroke="#2B2B2B" stroke-width="3" stroke-linecap="round"/>
  <path d="M28.4141 11.0713C28.4141 11.0713 30.394 11.637 33.3638 14.6068C36.3337 17.5767 36.8993 19.5566 36.8993 19.5566" stroke="#2B2B2B" stroke-width="3" stroke-linecap="round"/>
  <path d="M30.2014 30.0544L29.1138 29.0214L30.2014 30.0544ZM31.1123 29.0954L32.1999 30.1284H32.1999L31.1123 29.0954ZM35.9456 28.4246L35.1974 29.7247H35.1974L35.9456 28.4246ZM39.7667 30.624L39.0184 31.924L39.7667 30.624ZM40.8433 37.5169L41.9309 38.5499L40.8433 37.5169ZM38.0022 40.508L36.9147 39.475L38.0022 40.508ZM35.3526 41.9262L35.4999 43.4189L35.3526 41.9262ZM15.6307 32.9504L16.7183 31.9173L15.6307 32.9504ZM6.00578 13.9319L4.50793 14.0123L4.50793 14.0123L6.00578 13.9319ZM18.955 17.0062L20.0426 18.0393H20.0426L18.955 17.0062ZM19.2685 11.3862L20.4933 10.5202L19.2685 11.3862ZM16.7465 7.81923L15.5217 8.6852V8.6852L16.7465 7.81923ZM10.5229 7.21728L11.6105 8.25031L10.5229 7.21728ZM7.3837 10.5223L6.29611 9.48924L6.29611 9.48924L7.3837 10.5223ZM22.1262 26.1118L23.2138 25.0788L22.1262 26.1118ZM31.289 31.0874L32.1999 30.1284L30.0247 28.0624L29.1138 29.0214L31.289 31.0874ZM35.1974 29.7247L39.0184 31.924L40.5149 29.3239L36.6939 27.1246L35.1974 29.7247ZM39.7558 36.4838L36.9147 39.475L39.0898 41.541L41.9309 38.5499L39.7558 36.4838ZM35.2052 40.4334C32.3352 40.7167 24.8467 40.475 16.7183 31.9173L14.5431 33.9834C23.4019 43.31 31.8522 43.779 35.4999 43.4189L35.2052 40.4334ZM16.7183 31.9173C8.96606 23.7557 7.6657 16.8711 7.50362 13.8515L4.50793 14.0123C4.70644 17.7107 6.2768 25.2805 14.5431 33.9834L16.7183 31.9173ZM19.469 18.6432L20.0426 18.0393L17.8674 15.9732L17.2938 16.5771L19.469 18.6432ZM20.4933 10.5202L17.9713 6.95326L15.5217 8.6852L18.0437 12.2522L20.4933 10.5202ZM9.43531 6.18425L6.29611 9.48924L8.47129 11.5553L11.6105 8.25031L9.43531 6.18425ZM18.3814 17.6101C17.2938 16.5771 17.2924 16.5786 17.291 16.58C17.2906 16.5806 17.2892 16.582 17.2882 16.583C17.2863 16.5851 17.2844 16.5871 17.2824 16.5893C17.2785 16.5935 17.2744 16.5979 17.2702 16.6025C17.2618 16.6118 17.2529 16.6217 17.2436 16.6325C17.2249 16.654 17.2043 16.6786 17.1823 16.7064C17.1384 16.762 17.0886 16.8302 17.0364 16.9118C16.9318 17.0753 16.8183 17.2906 16.7222 17.5607C16.5268 18.1098 16.4204 18.837 16.5534 19.7451C16.8148 21.5294 17.984 23.9288 21.0387 27.1448L23.2138 25.0788C20.3585 22.0727 19.6552 20.2213 19.5217 19.3102C19.4573 18.8707 19.5228 18.6391 19.5486 18.5664C19.5632 18.5255 19.5727 18.5142 19.5634 18.5287C19.5589 18.5358 19.5499 18.549 19.5348 18.5681C19.5272 18.5777 19.5181 18.5887 19.5072 18.6013C19.5018 18.6075 19.4959 18.6141 19.4895 18.6211C19.4863 18.6246 19.483 18.6282 19.4796 18.6319C19.4779 18.6337 19.4762 18.6356 19.4744 18.6375C19.4735 18.6384 19.4722 18.6398 19.4717 18.6403C19.4704 18.6417 19.469 18.6432 18.3814 17.6101ZM21.0387 27.1448C24.0844 30.3514 26.3846 31.612 28.1396 31.897C29.0402 32.0433 29.7691 31.9265 30.3211 31.7087C30.591 31.6023 30.8044 31.4773 30.9646 31.3637C31.0446 31.307 31.1111 31.2533 31.1648 31.2061C31.1917 31.1825 31.2154 31.1606 31.2361 31.1407C31.2464 31.1307 31.256 31.1213 31.2648 31.1124C31.2692 31.1079 31.2734 31.1036 31.2775 31.0995C31.2795 31.0974 31.2815 31.0953 31.2834 31.0933C31.2843 31.0923 31.2858 31.0909 31.2862 31.0904C31.2876 31.0889 31.289 31.0874 30.2014 30.0544C29.1138 29.0214 29.1152 29.0199 29.1166 29.0185C29.117 29.018 29.1184 29.0166 29.1193 29.0157C29.1211 29.0138 29.1229 29.012 29.1246 29.0102C29.1281 29.0066 29.1315 29.0031 29.1349 28.9997C29.1416 28.993 29.1479 28.9867 29.154 28.9808C29.1661 28.9692 29.1769 28.9593 29.1865 28.9509C29.2056 28.9341 29.2199 28.9232 29.2291 28.9167C29.2478 28.9034 29.2459 28.9079 29.2204 28.918C29.1818 28.9332 29 28.9974 28.6206 28.9358C27.8155 28.8051 26.0781 28.0943 23.2138 25.0788L21.0387 27.1448ZM17.9713 6.95326C15.9441 4.0861 11.8877 3.60237 9.43531 6.18425L11.6105 8.25031C12.6562 7.14942 14.497 7.2359 15.5217 8.6852L17.9713 6.95326ZM7.50362 13.8515C7.46076 13.0529 7.80851 12.2531 8.47129 11.5553L6.29611 9.48924C5.22441 10.6175 4.40985 12.1849 4.50793 14.0123L7.50362 13.8515ZM36.9147 39.475C36.3566 40.0625 35.7727 40.3774 35.2052 40.4334L35.4999 43.4189C36.994 43.2715 38.2031 42.4746 39.0898 41.541L36.9147 39.475ZM20.0426 18.0393C21.9778 16.0019 22.1147 12.8136 20.4933 10.5202L18.0437 12.2522C18.888 13.4463 18.7585 15.0351 17.8674 15.9732L20.0426 18.0393ZM39.0184 31.924C40.6601 32.8689 40.9813 35.1935 39.7558 36.4838L41.9309 38.5499C44.5409 35.8021 43.7808 31.2037 40.5149 29.3239L39.0184 31.924ZM32.1999 30.1284C32.9708 29.3168 34.172 29.1345 35.1974 29.7247L36.6939 27.1246C34.497 25.8601 31.7723 26.2225 30.0247 28.0624L32.1999 30.1284Z" fill="#2B2B2B"/>
</svg>
</div>
<div class="phone_row1">
Нужна помощь С заказом?
</div>
<div class="phone_row2">
8 800 888 88 88 (с 8:00 до 22:00)
</div>
</div>


<?php
}

// Add text to replace the header on the checkout page
function add_text_to_checkout_page() {
    if (is_checkout() && !is_order_received_page()) {
    ?>
    <div class="checkout_header_wrape">
        <div class="checkout_inner_wrap">
    <div class="checkout_header">
        <div class="checkout_header_text">
            <h3>8 800 888 88 88</h3>
            <p>Заказ можно легко оформить по телефону</p>
        </div>
        <div class="checkout_logo">
        <h1><a href="<?php echo esc_url(home_url('/')); ?>">URBAN<strong>BOSS</strong></a></h1>
        </div>
    </div>
    </div>
    </div>
    <?php
    }
}
function custom_woocommerce_breadcrumbs(){
?>
<div class="woo-crubs">
    <p> <a href="<?php echo esc_url(home_url('/')); ?>">ГЛАВНАЯ</a> / оФОРМЛЕНИЕ ЗАКАЗА</p>
</div>
<?php
}


//                     _______Add Actions____________
add_action('woocommerce_before_checkout_form' , 'custom_woocommerce_breadcrumbs');
add_action('wp_enqueue_scripts', 'enqueue_plugin_assets_on_checkout');
add_action('woocommerce_review_order_before_submit', 'custom_html_before_order');
add_action('woocommerce_review_order_after_submit' , 'custom_html_after_order_btn');
add_action('wp_ajax_live_search', 'live_search_callback');
add_action('wp_ajax_nopriv_live_search', 'live_search_callback'); // Allow for non-logged-in users
add_action('woocommerce_after_checkout_billing_form', 'urban_custom_checkout_add_live_search_box'); // Add live search box
add_action('woocommerce_after_checkout_form', 'custom_html_for_modal');
add_action('wp', 'add_text_to_checkout_page');
add_action('admin_init', 'urban_custom_checkout_check_woocommerce');



