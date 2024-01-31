<!-- pages/install.php -->
<?php
// Set variables for our request
$shop = $_GET['shop'];
$api_key = "61e1e1e6ffd942e3b3a66c9f85bf6684";
$scopes = "read_orders,write_products,write_script_tags";
$redirect_uri = "https://f0a2-103-167-52-37.ngrok-free.app/pages/generate_token.php";

// Build install/approval URL to redirect to
$install_url = "https://admin.shopify.com/store/" . str_replace(".myshopify.com", "", $shop) . "/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);

// Redirect
header("Location: " . $install_url);
die();
