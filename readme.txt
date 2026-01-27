# Google Review Slider Pro - Hướng Dẫn Cập Nhật

## Tính Năng Mới: Chọn Layout Hiển Thị

Plugin đã được cập nhật với 3 kiểu hiển thị:
1. **Slider** - Hiển thị dạng slider với nút điều hướng
2. **Grid** - Hiển thị dạng lưới cột (2-5 cột)
3. **List** - Hiển thị dạng danh sách dọc

---

## Cài Đặt

### Bước 1: Thay Thế Files

Thay thế các file sau trong plugin:

```
/includes/class-grs-settings.php    → Thay bằng file mới
/includes/class-grs-frontend.php    → Thay bằng file mới
/assets/css/style.css               → Thay bằng file mới
```

### Bước 2: Cài Đặt Trong Admin

1. Vào **WordPress Admin** → **Google Reviews** → **Settings**
2. Tìm section **"Cài Đặt Hiển Thị"**
3. Chọn **"Kiểu Hiển Thị"**:
   - Slider (Swiper)
   - Grid (Lưới cột)
   - List (Danh sách)
4. Nếu chọn **Grid**, chọn số cột (2-5 cột)
5. Click **"Save Changes"**

---

## Sử Dụng Shortcode

### Cách 1: Sử Dụng Setting Mặc Định

```php
[google_review_slider limit="10"]
```
→ Sẽ sử dụng layout và số cột đã cài đặt trong Settings

### Cách 2: Ghi Đè Bằng Tham Số Shortcode

#### Hiển thị dạng Grid 3 cột:
```php
[google_review_slider limit="12" layout="grid" columns="3"]
```

#### Hiển thị dạng Grid 4 cột:
```php
[google_review_slider limit="16" layout="grid" columns="4"]
```

#### Hiển thị dạng List:
```php
[google_review_slider limit="8" layout="list"]
```

#### Hiển thị dạng Slider:
```php
[google_review_slider limit="10" layout="slider"]
```

---

## Các Tham Số Shortcode

| Tham số | Giá trị | Mô tả | Mặc định |
|---------|---------|-------|----------|
| `limit` | Số nguyên | Số lượng review hiển thị | 10 |
| `layout` | slider, grid, list | Kiểu hiển thị | Lấy từ Settings |
| `columns` | 2, 3, 4, 5 | Số cột (chỉ cho Grid) | Lấy từ Settings |

---

## Responsive Design

### Desktop (>992px)
- **Grid 5 cột**: Hiển thị 5 cột
- **Grid 4 cột**: Hiển thị 4 cột
- **Grid 3 cột**: Hiển thị 3 cột
- **Grid 2 cột**: Hiển thị 2 cột

### Tablet (768px - 992px)
- **Grid 4-5 cột**: Tự động xuống 3 cột
- **Grid 2-3 cột**: Giữ nguyên

### Mobile Landscape (576px - 768px)
- **Grid 3-5 cột**: Tự động xuống 2 cột
- **Grid 2 cột**: Giữ nguyên

### Mobile Portrait (<576px)
- **Tất cả Grid**: Tự động xuống 1 cột (full width)

---

## Ví Dụ Thực Tế

### Trang Chủ - Slider
```php
[google_review_slider limit="6" layout="slider"]
```

### Trang Reviews - Grid 3 Cột
```php
[google_review_slider limit="9" layout="grid" columns="3"]
```

### Sidebar - List 5 Review
```php
[google_review_slider limit="5" layout="list"]
```

### Landing Page - Grid 4 Cột
```php
[google_review_slider limit="12" layout="grid" columns="4"]
```

---

## Tùy Chỉnh CSS (Optional)

Nếu muốn tùy chỉnh thêm, thêm CSS vào theme:

```css
/* Tùy chỉnh màu sắc */
.grs-review-card {
    background: #f9f9f9; /* Màu nền card */
    border: 1px solid #e0e0e0; /* Viền */
}

/* Tùy chỉnh khoảng cách Grid */
.grs-grid-container {
    gap: 30px; /* Khoảng cách giữa các card */
}

/* Tùy chỉnh màu star */
.grs-star.filled {
    color: #ff9800; /* Màu sao */
}
```

---

## Troubleshooting

### Vấn đề: Layout không thay đổi
**Giải pháp**: 
1. Xóa cache browser (Ctrl + F5)
2. Xóa cache plugin (nếu dùng W3 Total Cache, WP Rocket...)
3. Kiểm tra xem đã Save Settings chưa

### Vấn đề: Grid hiển thị sai số cột
**Giải pháp**:
1. Kiểm tra shortcode có đúng syntax không
2. Kiểm tra trong Settings đã chọn đúng số cột chưa
3. Xóa cache CSS

### Vấn đề: Responsive không hoạt động
**Giải pháp**:
1. Kiểm tra file `style.css` đã được thay thế chưa
2. Xóa cache
3. Test trên trình duyệt khác

---

## Changelog

### Version 2.0.0
- ✅ Thêm tính năng chọn layout (Slider, Grid, List)
- ✅ Thêm tùy chọn số cột cho Grid (2-5 cột)
- ✅ Cải thiện responsive design
- ✅ Tối ưu hóa performance
- ✅ Thêm tham số shortcode để ghi đè settings

---

## Hỗ Trợ

Nếu có vấn đề, vui lòng:
1. Kiểm tra file đã thay thế đúng chưa
2. Xóa cache
3. Kiểm tra console browser có lỗi JavaScript không
4. Liên hệ support

---

**Developed by TrongNhanDev**
**Version**: 2.0.0
**Last Updated**: January 2026
