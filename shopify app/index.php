<?php
require_once("inc/functions.php");
require_once("inc/connect.php");

$data = $_GET;
$hmac = $_GET['hmac'];
$array_serialized = serialize($data);

$requests = array_diff_key($data, array('hmac' => ''));
ksort($requests);

$sql = "SELECT * FROM example_table WHERE store_url='" . $requests['shop'] . "' LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$token = $row['access_token'];
$shop_url = "https://" . $row['store_url'];



$parsedURL = parse_url($shop_url);
if ($parsedURL === false || !isset($parsedURL['host'])) {
    echo "Error parsing URL or missing host component: " . $shop_url;
} else {
    $host = explode('.', $parsedURL['host']);
    if (count($host) >= 3) {
        $subdomain = $host[0];
    } else {
        $subdomain = $parsedURL['host'];
    }
    echo "Subdomain: " . $subdomain;
}
$subdomain = $host[0];

?>


<!DOCTYPE html>
<html>

<head>
    <title> Example App</title>
</head>

<body>
    <div style="padding: 20px; margin: 0 25px;">
        <input type="text" class="searchInput" name="serachInput" placeholder="search for an item..">
        <input type="hidden" class="subdomain" name="subdomain" value="<?php echo $subdomain; ?>">

        <div class="product_lists">
        </div>
    </div>
</body>


<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>
    $('.searchInput').keypress(function(e) {
        if (e.which == 13) {
            var search = $(this).val();
            var subdomain = $('.subdomain').val();

            $.ajax({
                type: 'POST',
                data: {
                    term: search,
                    store: subdomain
                },
                url: 'search.php',
                dataType: 'html',
                success: function(response) {
                    $('.product_lists').html(response);
                }
            });

            return false;
        }
    });
</script>

</html>