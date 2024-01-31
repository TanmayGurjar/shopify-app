<!-- pages/generate_token.php 

<?php
// require_once '../inc/functions.php';

// $api_key = "61e1e1e6ffd942e3b3a66c9f85bf6684";
// $shared_secret = "b1b164dd481cb8f1ffeae12612b134e9";

// $params = $_GET; // Retrieve all request parameters
// $hmac = $_GET['hmac']; // Retrieve HMAC request parameter

// $params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
// ksort($params); // Sort params lexographically
// $computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

// // Use hmac data to check that the response is from Shopify or not
// if (hash_equals($hmac, $computed_hmac)) {
// 	// Set variables for our request
// 	$query = array(
// 		"client_id" => $api_key, // Your API key
// 		"client_secret" => $shared_secret, // Your app credentials (secret key)
// 		"code" => $params['code'] // Grab the access key from the URL
// 	);
// 	// Generate access token URL
// 	$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";
// 	// Configure curl client and execute request
// 	$ch = curl_init();
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 	curl_setopt($ch, CURLOPT_URL, $access_token_url);
// 	curl_setopt($ch, CURLOPT_POST, count($query));
// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
// 	$result = curl_exec($ch);
// 	curl_close($ch);
// 	// Store the access token
// 	$result = json_decode($result, true);
// 	$access_token = $result['access_token'];
// 	// Show the access token (don't do this in production!)
// 	echo $access_token;
// } else {
// 	// Someone is trying to be shady!
// 	die('This request is NOT from Shopify!');
// }
?>

<?php
require_once '../inc/functions.php';
require_once '../inc/connect.php';

$api_key = "61e1e1e6ffd942e3b3a66c9f85bf6684";
$shared_secret = "b1b164dd481cb8f1ffeae12612b134e9";

$hmac = isset($_GET['hmac']) ? $_GET['hmac'] : $_POST['hmac'];
$shop_url = isset($_GET['shop']) ? $_GET['shop'] : $_POST['shop'];

if ($hmac !== null && $shop_url !== null) {
	$params = $_GET;

	$params = array_diff_key($params, array('hmac' => ''));
	ksort($params);

	$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

	if (hash_equals($hmac, $computed_hmac)) {
		$check_query = "SELECT COUNT(*) FROM example_table WHERE store_url = '$shop_url'";
		$result = mysqli_query($conn, $check_query);

		if ($result) {
			$row = mysqli_fetch_array($result, MYSQLI_NUM);
			$count = $row[0];

			if ($count > 0) {
				header("Location: https://$shop_url/admin/apps/t-20");
				exit();
			} else {
				var_dump("inside else");
				exit;
				$scopes = "read_orders,write_products,write_script_tags";
				$redirect_uri = "https://eac0-103-167-52-64.ngrok-free.app/pages/generate_token.php";
				$install_url = "https://admin.shopify.com/store/" . str_replace(".myshopify.com", "", $shop_url) . "/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
				header("Location: " . $install_url);
				exit();
			}
		} else {
			echo "Error executing query: " . mysqli_error($conn);
			exit();
		}
	} else {
		die('This request is NOT from Shopify!');
	}
} else {
	die('Missing required parameters!');
}
?>