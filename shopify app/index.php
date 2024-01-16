<!-- <h1>This is index file</h1> -->

<?php
require_once("inc/functions.php");

$requests = $_GET;
$hmac = $_GET['hmac'];
$serializeArray = serialize($requests);
$requests = array_diff_key($requests, array('hmac' => ''));
ksort($requests);

$token = "shpua_3f7224da24f0b5de0cfef3f7af3ccfc8";
$shop = "appify2022";

$collectionList = shopify_call($token, $shop, "/admin/api/2024-01/custom_collections.json", array(), 'GET');
$collectionList = json_decode($collectionList['response'], JSON_PRETTY_PRINT);
$collection_id = $collectionList['custom_collections'][0]['id'];

echo $collection_id;

$collects = shopify_call($token, $shop, "/admin/api/2024-01/collects.json", array("collection_id" => $collection_id), 'GET');
$collects = json_decode($collects['response'], JSON_PRETTY_PRINT);

// foreach ($collects as $collect) {
//     foreach ($collect as $key => $value) {
//         $products = shopify_call($token, $shop, "/admin/api/2024-01/products/" . $value['product_id'] . ".json", array(), 'GET');
//         $product = json_decode($products['response'], JSON_PRETTY_PRINT);

//         echo $products['product']['title'] . '<br />';
//     }
// }
foreach ($collects as $collect) {
    foreach ($collect as $key => $value) {
        $products = shopify_call($token, $shop, "/admin/api/2024-01/products/" . $value['product_id'] . ".json", array(), 'GET');
        $product = json_decode($products['response'], JSON_PRETTY_PRINT);

        if (isset($product['product'])) {
            echo $product['product']['title'] . '<br />';
        } else {
            echo "Product title not found.<br />";
        }
    }
}

?>