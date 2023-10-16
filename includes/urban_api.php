
<?php
require_once(__DIR__ . '/../../../../wp-load.php');
if (isset($_POST['s'])) {
    global $wpdb;
    $key = '%' . $wpdb->esc_like($_POST['s']) . '%';
        $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT city_name, region, subregion FROM pickup_points
             WHERE city_name LIKE %s
             LIMIT 5",
            $key
        )
    );
    if ($results) {
        foreach ($results as $result) {
            echo '<li>';
            echo '<span class="list-city">' . esc_html($result->city_name) . '</span>';
            echo '<br>';
            echo '<span class="list-region">' . esc_html($result->region . ' - ' . $result->subregion) . '</span>';
            echo '</li>';

        }
    } else {
        echo 'not found';
    }
}
?>