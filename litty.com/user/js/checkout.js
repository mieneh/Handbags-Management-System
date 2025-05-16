// Hàm cập nhật số tiền giảm giá và tổng tiền cần trả
function updateDiscountAndTotal() {
    var discountSelect = document.getElementById('discount_code');
    var discountLine = document.getElementById('discount_line');
    var discountAmountEl = document.getElementById('discount_amount');

    var selectedShippingMethod = document.querySelector('input[name="shipping_method"]:checked').value;
    var shippingFee = selectedShippingMethod === 'home_delivery' ? 30000 : 0;

    var discountPercent = parseFloat(discountSelect.selectedOptions[0].getAttribute('data-discount')) || 0;
    var discountAmount = totalAmount * (discountPercent / 100);

    var finalTotal = totalAmount - discountAmount + shippingFee;

    // Cập nhật DOM
    if (discountPercent > 0) {
        discountLine.style.display = 'block';
        discountAmountEl.textContent = discountAmount.toLocaleString('vi-VN') + ' VNĐ';
    } else {
        discountLine.style.display = 'none';
        discountAmountEl.textContent = '0 VNĐ';
    }

    document.getElementById('price').textContent = totalAmount.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('shipping_fee').textContent = shippingFee.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('total').textContent = finalTotal.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('intotal').value = Math.round(finalTotal);
 
}

// Sử dụng lại hàm khi phương thức vận chuyển hoặc chiết khấu thay đổi
document.querySelectorAll('input[name="shipping_method"]').forEach(function (method) {
    method.addEventListener('change', updateDiscountAndTotal);
});

document.getElementById('discount_code').addEventListener('change', updateDiscountAndTotal);

updateDiscountAndTotal();