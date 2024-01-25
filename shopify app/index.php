<!-- index.php -->
<?php
require_once("inc/functions.php");

$token = "shpua_2a49e6124a502ffa6cbce7ee538c40a2";
$shop = "appify2022";

function getProducts($collection_id, $token, $shop)
{
    $productsArray = array();

    $collects = shopify_call($token, $shop, "/admin/api/2024-01/collects.json", array("collection_id" => $collection_id), 'GET');
    $collects = json_decode($collects['response'], JSON_PRETTY_PRINT);

    if (isset($collects['collects'])) {
        foreach ($collects['collects'] as $collect) {
            $products = shopify_call($token, $shop, "/admin/api/2024-01/products/" . $collect['product_id'] . ".json", array(), 'GET');
            $product = json_decode($products['response'], JSON_PRETTY_PRINT);

            if (isset($product['product']) && is_array($product['product'])) {
                $title = $product['product']['title'];
            } else {
                $title = "Product title not found.";
            }

            $images = shopify_call($token, $shop, "/admin/api/2024-01/products/" . $collect['product_id'] . "/images.json", array(), 'GET');
            $images = json_decode($images['response'], JSON_PRETTY_PRINT);

            if (isset($images['images'][0]['src']) && !empty($images['images'][0]['src'])) {
                $image = $images['images'][0]['src'];
            } else {
                $image = "Image not found.";
            }

            $productsArray[] = array('id' => $collect['product_id'], 'title' => $title, 'image' => $image);
        }
    }

    return $productsArray;
}

function getStaticProducts($staticProductIds, $token, $shop)
{
    $productsArray = array();

    foreach ($staticProductIds as $productId) {
        $products = shopify_call($token, $shop, "/admin/api/2024-01/products/" . $productId . ".json", array(), 'GET');
        $product = json_decode($products['response'], JSON_PRETTY_PRINT);

        if (isset($product['product']) && is_array($product['product'])) {
            $title = $product['product']['title'];
        } else {
            $title = "Product title not found.";
        }

        $images = shopify_call($token, $shop, "/admin/api/2024-01/products/" . $productId . "/images.json", array(), 'GET');
        $images = json_decode($images['response'], JSON_PRETTY_PRINT);

        if (isset($images['images'][0]['src']) && !empty($images['images'][0]['src'])) {
            $image = $images['images'][0]['src'];
        } else {
            $image = "Image not found.";
        }

        $productsArray[] = array('id' => $productId, 'title' => $title, 'image' => $image);
    }

    return $productsArray;
}

function getProductDetails($productId, $token, $shop)
{
    $productDetails = shopify_call($token, $shop, "/admin/api/2024-01/products/{$productId}.json", array(), 'GET');
    $productDetails = json_decode($productDetails['response'], JSON_PRETTY_PRINT);

    return $productDetails;
}

$collectionList = shopify_call($token, $shop, "/admin/api/2024-01/custom_collections.json", array(), 'GET');
$collectionList = json_decode($collectionList['response'], JSON_PRETTY_PRINT);
$collection_id = $collectionList['custom_collections'][0]['id'];
$productsArray = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set']) && $_POST['set'] == 'addProduct') {
        $title = $_POST['title'];
        $image = $_FILES['image']['tmp_name'];
    }
} elseif (isset($_GET['set'])) {
    if ($_GET['set'] == 'getProductDetails' && isset($_GET['product_id'])) {
        $productId = $_GET['product_id'];
        $productDetails = getProductDetails($productId, $token, $shop);
        header('Content-Type: application/json');
        echo json_encode($productDetails);
        // exit;
    } elseif ($_GET['set'] == 'default') {
        $productsArray = getProducts($collection_id, $token, $shop);
    } elseif ($_GET['set'] == 'static') {
        $staticProductIds = array(6795305910349, 6983036895309, 6983036829773);

        $productsArray = array();

        $staticProducts = getStaticProducts($staticProductIds, $token, $shop);
        $productsArray = array_merge($productsArray, $staticProducts);
    } elseif ($_GET['set'] == 'clear') {
        $productsArray = array();
    }
}

function addProductFromForm($token, $shop)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['set']) && $_POST['set'] == 'addProduct') {
            // Get user input
            $productName = $_POST['title'];
            $productImage = $_FILES['image']['tmp_name'];
            $productPrice = $_POST['price'];

            // Collection ID (replace with your specific collection ID)
            $collectionId = 'your_collection_id';
            $imageData = array(
                'image' => base64_encode(file_get_contents($productImage)),
            );

            $payload = [
                "product" => [
                    "title" => $productName,
                    "images" => [
                        ['attachment' => base64_encode(file_get_contents($productImage))]
                    ]
                ]
            ];
            // var_dump($payload);
            $res = shopifyRestCall($token, $shop, $payload);
            // Call the function to add the product to the collection
            // addProductToCollection($token, $shop, $productName, $productImage, $productPrice, $collectionId);
        }
    }
}

// Call the function to add a product if the form is submitted
addProductFromForm($token, $shop);

$script_array = array(
    'script_tag' => array(
        'event' => 'onload',
        'src' => 'https://4a85-103-167-52-59.ngrok-free.app/js/script.js'
    )
);
$scriptTag = shopify_call($token, $shop, "/admin/api/2024-01/script_tags.json", $script_array, 'POST');
$scriptTag = json_decode($scriptTag['response'], JSON_PRETTY_PRINT);

?>

<!DOCTYPE html>
<html>

<head>
    <title> demo with image</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <h1>Shopify app example </h1>

    <form method="get" action="">
        <button type="submit" name="set" value="default">Display Default Products</button>
    </form>

    <form method="get" action="">
        <button type="submit" name="set" value="static">Display Static Products</button>
    </form>
    <form method="get" action="">
        <button type="submit" name="set" value="clear">Clear Products</button>
    </form>
    <div>
        <h2>Add Product</h2>
        <button onclick="showPopup()">Add Product</button>

        <div id="popup" style="display: none;">
            <form method="post" action="" enctype="multipart/form-data">
                <label for="title">Product Title:</label>
                <input type="text" name="title" required><br>

                <label for="image">Product Image:</label>
                <input type="file" name="image" accept="image/*" required><br>

                <label for="price">Product Price:</label>
                <input type="text" name="price" required><br>

                <button type="submit" name="set" value="addProduct">Add Product</button>
                <button type="button" onclick="hidePopup()">Cancel</button>
            </form>
        </div>
        <script>
            function showPopup() {
                var popup = document.getElementById("popup");
                popup.style.display = "block";
            }

            function hidePopup() {
                var popup = document.getElementById("popup");
                popup.style.display = "none";
            }
        </script>
    </div>
    <div id="productDetails"></div>

    <?php
    if (isset($productsArray) && is_array($productsArray)) {
        foreach ($productsArray as $productInfo) {
            if (is_array($productInfo)) {
                echo '<img src="' . $productInfo['image'] . '" alt="Product Image" style="width: 250px;">';
                echo '<p>' . $productInfo['title'] . '</p>';
                echo '<button onclick="showProductDetails(' . $productInfo['id'] . ')">Details</button>';
                echo '<hr>';
            }
        }
    } elseif (isset($_GET['set']) && $_GET['set'] == 'clear') {
        echo 'Products cleared.';
    } else {
        echo 'No products to display.';
    }
    ?>

    <script src="js/main.js"></script>
    <!-- <script src="js/script.js"></script> -->
</body>

</html>