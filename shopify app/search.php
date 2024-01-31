<?php
require_once("inc/functions.php");
require_once("inc/connect.php");

$result = '';
$search = $_POST['term'];
$subdomain = $_POST['store'];


$sql = "SELECT * FROM example_table WHERE store_url='" . $subdomain . ".myshopify.com' LIMIT 1";

$results = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($results);
// var_dump($row['access_token']);
// exit;


if (!empty($row['access_token'])) {
    $token = $row['access_token'];

    // Proceed with API call
    $products = shopify_call($token, $subdomain, "/admin/api/2024-01/products.json", array('fields' => 'id,title'), 'GET');
    $decoded_products = json_decode($products['response'], true);

    // Check for errors in response
    if (isset($decoded_products['errors'])) {
        // Handle API error
        echo "API Error: " . $decoded_products['errors'];
    } else {
        // Process products
        foreach ($decoded_products as $product) {
            foreach ($product as $key => $value) {
                if (isset($value['title']) && stripos($value['title'], $search) !== false) {
                    $result .= '<p>' . $value['title'] . '</p>';
                }
            }
        }
        echo $result;
    }
} else {
    echo "Access token not found or invalid.";
}
