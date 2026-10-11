# EduManage - Quản Lý Học Tập & Soạn Giáo Án Thông Minh Tích Hợp AI

Hệ thống quản lý học sinh, nhật ký buổi học, bảng điểm, học phí tự động với mã VietQR động, và phân hệ soạn giáo án / tạo đề kiểm tra thông minh bằng Trí tuệ nhân tạo (AI) bám sát chương trình Giáo dục Phổ thông (GDPT 2018).

---

## 🌟 Tính Năng Nổi Bật

### 1. Phân quyền và Định danh 2 tầng độc lập
- **Tài khoản Giáo viên (`teacher`)**:
  - Tự động sinh **Mã giáo viên độc lập** (`GV-XXXXXXXX`) ngay khi đăng ký.
  - Hiển thị mã trên đầu Dashboard kèm nút sao chép nhanh 1-chạm.
  - Quản lý học sinh, nhật ký buổi học, bảng điểm, nhận xét và cấu hình tài khoản ngân hàng.
- **Mã liên kết học sinh (`student connection code`)**:
  - Tự sinh mã (`HS-XXXXXXXX`) cho từng hồ sơ học sinh.
  - Hiển thị song song với Mã giáo viên trên thanh điều khiển.
- **Tài khoản Phụ huynh (`parent`) & Học sinh (`student`)**:
  - Nhập mã kết nối học sinh để theo dõi toàn bộ nhật ký học tập, bảng điểm và học phí theo thời gian thực (chế độ Read-only).

### 2. Quản lý Nhật ký Buổi học & Chuyên cần
- Ghi nhận chi tiết: môn học (Toán, Tiếng Việt, Tiếng Anh,...), thời lượng, thái độ học tập, % hoàn thành bài tập về nhà và nhận xét sư phạm.
- Bộ lọc theo tháng trực quan.

### 3. Bảng điểm & Nhận xét định kỳ
- Theo dõi các bài kiểm tra 15 phút, 1 tiết, học kỳ kèm chi tiết lỗi sai.
- Đánh giá định kỳ hàng tháng theo phân môn (Đại số, Hình học,...).

### 4. Tự động hóa Học phí & Thanh toán VietQR
- Tự động chốt học phí dựa trên số buổi học và đơn giá.
- Sinh mã **VietQR động chuẩn Napas 247** chứa chính xác số tiền và cú pháp chuyển khoản.

### 5. Soạn Giáo án & Tạo Đề thi bằng Trí tuệ Nhân tạo (AI)
- Tích hợp mô hình AI qua Groq Cloud API.
- Hỗ trợ đầy đủ các khối lớp 1 - 5 bậc Tiểu học theo chuẩn SGK mới.
- Sinh đề trắc nghiệm, tự luận hoặc kết hợp ma trận nhận thức.
- Xem trước chuẩn khổ giấy A4, biên soạn trực tiếp, xuất file Word (.doc) và in ấn.

---

## 🛠️ Công Nghệ Sử Dụng

- **Backend**: PHP 8.x (Kiến trúc MVC + RESTful API, PDO, Bcrypt Hashing)
- **Frontend**: HTML5, Tailwind CSS, FontAwesome 6, Vanilla JavaScript (Fetch API)
- **Database**: MySQL / MariaDB (InnoDB, Foreign Keys, UTF-8 MB4)
- **AI Engine**: Groq Cloud API (Llama 3 / Mixtral)

---

## 🚀 Hướng Dẫn Cài Đặt

1. **Khởi động XAMPP**: Mở Apache và MySQL.
2. **Cài đặt mã nguồn**: Đặt thư mục dự án vào `C:\xampp\htdocs\edumanage`.
3. **Cơ sở dữ liệu**:
   - Truy cập phpMyAdmin (`http://localhost/phpmyadmin`).
   - Tạo cơ sở dữ liệu `edumanage` (Collation: `utf8mb4_unicode_ci`).
   - Import file `database.sql` vào cơ sở dữ liệu `edumanage`.
4. **Cấu hình**: Kiểm tra thông tin kết nối DB trong `config/database.php` và `soan_giao_an/config/constants.php`.
5. **Truy cập hệ thống**:
   - Mở trình duyệt và truy cập: `http://localhost/edumanage/login.php`
