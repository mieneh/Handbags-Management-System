// Function to update shipping fee and total price based on the selected method
function updateShippingFeeAndTotal() {
    var shippingFeeElement = document.getElementById('shipping_fee');
    var totalPriceElement = document.getElementById('total_price');
    var finalTotalElement = document.getElementById('final_total');

    var selectedShippingMethod = document.querySelector('input[name="shipping_method"]:checked').value;

    var shippingFee = selectedShippingMethod === 'home_delivery' ? 30000 : 0; // Shipping fee in VND
    var total = totalAmount + shippingFee; // Calculate total

    shippingFeeElement.textContent = shippingFee.toLocaleString('vi-VN') + ' VNĐ'; // Format fee
    finalTotalElement.textContent = total.toLocaleString('vi-VN') + ' VNĐ'; // Format final total
}

// Add event listener for shipping method changes
var shippingMethods = document.querySelectorAll('input[name="shipping_method"]');
shippingMethods.forEach(function (method) {
    method.addEventListener('change', updateShippingFeeAndTotal);
});

// Initial call to set up shipping fee and total
updateShippingFeeAndTotal();