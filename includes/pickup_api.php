<?php
// Ajax request
// Enqueue jQuery and set AJAX URL
require_once(__DIR__ . '/../../../../wp-load.php');
function enqueue_jquery_and_set_ajax_url() {
    wp_enqueue_script('jquery');
    wp_localize_script('jquery', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'enqueue_jquery_and_set_ajax_url');

// AJAX handler for your JavaScript data
function handle_ajax_req() {
    if (isset($_REQUEST['php_var'])) {
        $value = $_REQUEST['php_var'];
        // SQL REQUEST
        global $wpdb;
        $pickups = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT pickup_points FROM pickup_points
                 WHERE id = %d",
                $value
            )
        );
       $json_pickups =  json_decode($pickups);
        echo json_encode($json_pickups);

    }
    wp_die();
}
add_action('wp_ajax_my_ajax_action', 'handle_ajax_req'); 
add_action('wp_ajax_nopriv_my_ajax_action', 'handle_ajax_req');



?>