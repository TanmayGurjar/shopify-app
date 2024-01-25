<!-- inc=>functions.php -->
<?php

function shopify_call($token, $shop, $api_endpoint, $query = array(), $method = 'GET', $request_headers = array())
{
	$url = "https://" . $shop . ".myshopify.com" . $api_endpoint;
	if (!is_null($query) && in_array($method, array('GET', 'DELETE'))) $url = $url . "?" . http_build_query($query);

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, TRUE);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($curl, CURLOPT_MAXREDIRS, 3);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_USERAGENT, 'My New Shopify App v.1');
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

	$request_headers[] = "";

	if (!is_null($token)) $request_headers[] = "X-Shopify-Access-Token: " . $token;
	curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

	if ($method != 'GET' && in_array($method, array('POST', 'PUT'))) {
		if (is_array($query)) $query = http_build_query($query);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
	}

	$response = curl_exec($curl);
	$error_number = curl_errno($curl);
	$error_message = curl_error($curl);

	curl_close($curl);

	if ($error_number) {
		return $error_message;
	} else {
		$response = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);

		$headers = array();
		$header_data = explode("\n", $response[0]);
		$headers['status'] = $header_data[0];
		array_shift($header_data);
		foreach ($header_data as $part) {
			$h = explode(":", $part);
			$headers[trim($h[0])] = trim($h[1]);
		}

		return array('headers' => $headers, 'response' => $response[1]);
	}
	error_log("Shopify API Request: " . $url);
	error_log("Shopify API Request Headers: " . print_r($request_headers, true));
	error_log("Shopify API Request Body: " . print_r($query, true));
	error_log("Shopify API Response: " . print_r($response, true));
}

function addProductToCollection($token, $shop, $productName, $productImage, $productPrice, $collectionId)
{
	// Validate and upload image to Shopify
	$imageId = uploadProductImageToShopify($token, $shop, $productImage);

	// Prepare product data
	$productData = array(
		'product' => array(
			'title' => $productName,
			'body_html' => '', // Add product description if needed
			'images' => array(
				array('attachment' => $imageId),
			),
			'variants' => array(
				array(
					'price' => $productPrice,
					'sku' => '', // Add SKU if needed
				),
			),
		),
	);

	// Shopify API call to add product to the collection
	shopify_call($token, $shop, "/admin/api/2024-01/products.json", $productData, 'POST');
}

function uploadProductImageToShopify($token, $shop, $productImage)
{
	// Shopify API call to upload an image
	$imageData = array(
		'image' => base64_encode(file_get_contents($productImage)),
	);

	$response = shopify_call($token, $shop, "/admin/api/2024-01/products/images.json", $imageData, 'POST');
	$imageInfo = json_decode($response['response'], true);

	// Log the API response for debugging
	error_log("Shopify API Request: " . print_r($imageData, true));
	error_log("Shopify API Response: " . print_r($imageInfo, true));

	// Check for errors during the image upload
	if (isset($imageInfo['errors'])) {
		die('Error uploading image to Shopify: ' . print_r($imageInfo['errors'], true));
	}

	// Check if the image ID is present in the response
	if (isset($imageInfo['image']['id']) && !empty($imageInfo['image']['id'])) {
		return $imageInfo['image']['id'];
	} else {
		die('Error uploading image to Shopify. Image ID not found. Shopify API Response: ' . print_r($imageInfo, true));
	}
}

function shopifyRestCall($token, $shop, $payload)
{
	$curl = curl_init();

	// Ensure $payload is a JSON string
	if (is_array($payload)) {
		$payload = json_encode($payload);
	}

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://$shop.myshopify.com/admin/api/2024-01/products.json",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $payload,
		// 	'{
		// 	"product": {
		// 		"title": "Burton Custom Freestyle 2",
		// 		"body_html": "<strong>Good snowboard!</strong>",
		// 		"vendor": "Burton",
		// 		"product_type": "Snowboard",
		// 		"images": [
		// 			{
		// 				"attachment":"R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==\\n"
		// 			}
		// 		]
		// 	}
		// }',
		CURLOPT_HTTPHEADER => array(
			"X-Shopify-Access-Token: $token",
			'Content-Type: application/json'
		),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	echo $response;
	exit;
}
