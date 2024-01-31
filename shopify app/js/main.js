// js/main.script

var lastClickedProductId;
function showProductDetails(productId) {
  lastClickedProductId = productId;
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        var productDetails = JSON.parse(xhr.responseText);
        var detailsContainer = document.getElementById("productDetails");
        // Display variant details
        detailsContainer.innerHTML += "<h3>Variant Details:</h3>";
        productDetails.product.variants.forEach(function (variant) {
          detailsContainer.innerHTML += "<p>Title: " + variant.title + "</p>";
          detailsContainer.innerHTML += "<p>SKU: " + variant.sku + "</p>";
        });
        detailsContainer.innerHTML += "<h3>Image Details:</h3>";
        productDetails.product.images.forEach(function (image) {
          detailsContainer.innerHTML +=
            "<p>Image with Variant IDs: " +
            image.variant_ids.join(", ") +
            "</p>";
          detailsContainer.innerHTML += "<hr>";
        });
      } else {
        alert("Error fetching product details.");
      }
    }
  };
  xhr.open(
    "GET",
    "index.php?set=getProductDetails&product_id=" + productId,
    true
  );
  xhr.send();
}
function showPopup() {
  document.getElementById("popup").style.display = "block";
}
function hidePopup() {
  document.getElementById("popup").style.display = "none";
}
