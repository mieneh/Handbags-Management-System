// Cập nhật danh sách quận/huyện khi chọn tỉnh/thành phố
document.getElementById('city').addEventListener('change', function () {
    var city = this.value;
    var districtSelect = document.getElementById('district');
    var wardSelect = document.getElementById('ward');

    districtSelect.innerHTML = '<option value="" disabled selected>Chọn Quận / Huyện</option>';
    wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường / Xã</option>';

    if (locations[city]) {
        for (var district in locations[city]) {
            var option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        }
    }
});

// Cập nhật danh sách phường/xã khi chọn quận/huyện
document.getElementById('district').addEventListener('change', function () {
    var city = document.getElementById('city').value;
    var district = this.value;
    var wardSelect = document.getElementById('ward');

    wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường / Xã</option>';

    if (locations[city] && locations[city][district]) {
        locations[city][district].forEach(function (ward) {
            var option = document.createElement('option');
            option.value = ward;
            option.textContent = ward;
            wardSelect.appendChild(option);
        });
    }
});