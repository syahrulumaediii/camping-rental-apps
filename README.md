# 🏕️ Camping Rental System

Sistem manajemen rental peralatan camping berbasis web menggunakan PHP, MySQL, dan Bootstrap 5.

## 📋 Fitur Utama

### User Features

- ✅ Registrasi & Login User
- 🔍 Browse Katalog Peralatan Camping
- 📅 Booking Peralatan dengan Tanggal
- 💳 Sistem Pembayaran
- 📊 Track Status Booking
- 👤 Manajemen Profile

### Admin Features

- 📊 Dashboard Analytics
- 📦 Manajemen Items/Produk
- 📅 Manajemen Bookings
- 💰 Manajemen Payments
- 👥 Manajemen Users
- 📈 Laporan & Reports
- 📉 Revenue Tracking

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP 8.3+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 5.3
- **Chart:** Chart.js
- **Icons:** Bootstrap Icons
- **Architecture:** MVC Pattern

## 📁 Struktur Folder

```
camping_rental/
│
├── admin/
│   ├── index.php                   # Dashboard admin
│   ├── items.php                   # Kelola peralatan camping
│   ├── bookings.php                # Kelola pemesanan
│   ├── payments.php                # Kelola pembayaran
│   ├── users.php                   # Kelola pengguna
│   ├── reports.php                 # Laporan sistem
│   ├── get_booking.php             # AJAX: ambil detail booking
│   ├── process_booking.php         # Handler: kelola booking (konfirmasi/batalkan)
│   ├── process_items.php           # Handler: CRUD peralatan
│   ├── process_payments.php        # Handler: kelola pembayaran
│   └── process_users.php           # Handler: kelola user
│
├── assets/
│   ├── css/
│   │   ├── style.css               # Gaya global
│   │   ├── home.css                # Gaya halaman utama (publik)
│   │   ├── homeadmin.css           # Gaya dashboard admin
│   │   ├── login.css               # Gaya halaman login & register
│   │   └── reports.css             # Gaya laporan
│   │
│   ├── js/
│   │   └── main.js                 # JavaScript utama (form, modal, dll.)
│   │
│   ├── images/                     # Gambar sistem (logo, ikon, placeholder)
│   └── uploads/                    # File unggahan (foto peralatan, dll.)
│
├── config/
│   ├── app.php                     # Konfigurasi aplikasi (URL, session, dll.)
│   ├── database.php                # Koneksi database (PDO/MySQLi)
│   └── menu.json                   # Data menu (opsional)
│
├── controllers/
│   ├── BookingController.php
│   ├── CatalogController.php
│   └── PaymentController.php
│
├── lib/
│   ├── auth.php                    # Autentikasi & otorisasi
│   ├── functions.php               # Fungsi helper (formatRupiah, formatDate, dll.)
│   └── middleware.php              # Middleware keamanan (opsional)
│
├── models/
│   ├── Booking.php
│   ├── Item.php                    # Disarankan: nama singular
│   ├── Payment.php
│   └── User.php
│
├── views/
│   ├── booking/
│   │   ├── form.php                # Form pemesanan
│   │   └── status.php              # Status booking pengguna
│   │
│   ├── catalog/
│   │   ├── list.php                # Daftar peralatan (perbaikan: "lits" → "list")
│   │   └── detail.php              # Detail peralatan
│   │
│   ├── payment/
│   │   ├── checkout.php            # Halaman checkout
│   │   └── success.php             # Halaman sukses pembayaran
│   │
│   ├── footer.php                  # Footer umum
│   ├── header.php                  # Header & meta tags
│   ├── profile.php                 # Profil pengguna
│   ├── sidebar.php                 # Sidebar admin
│   └── topnav.php                  # Navbar atas
│
├── .env                            # Konfigurasi sensitif (DB, API key)
├── .htaccess                       # Aturan Apache (proteksi file, redirect)
├── composer.json                   # Dependensi PHP (jika pakai Composer)
├── composer.lock
│
├── index.php                       # Halaman utama (publik)
├── login.php                       # Halaman login
├── register.php                    # Halaman registrasi
├── logout.php                      # Proses logout
│
├── process_booking.php             # Handler pemesanan (publik)
├── process_cancel_booking.php      # Batalkan booking
├── process_payment.php             # Proses pembayaran
│
├── README.md                       # Dokumentasi proyek
├── setup.bat                       # Skrip setup (Windows)
└── screenshot/                     # Folder kumpulan tangkapan layar

```

## 🚀 Instalasi

### 1. Persyaratan Sistem

- PHP 8.3 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache Web Server (XAMPP/WAMP/LARAGON)
- Browser modern (Chrome, Firefox, Edge)

### 2. Langkah Instalasi

**A. Clone/Download Project**

```bash
# Clone repository (jika menggunakan git)
git clone [repository-url]

# Atau extract ZIP ke folder htdocs
C:\xampp\htdocs\camping_rental\
```

**B. Setup Database**

1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Buat database baru: `db_camping_rental`
3. Import file SQL yang disediakan
4. Atau jalankan query dari file SQL secara manual

**C. Konfigurasi Environment**

Edit file `.env`:

```env
APP_NAME="Camping Rental"
APP_URL=http://localhost/camping_rental

DB_HOST=localhost
DB_NAME=db_camping_rental
DB_USER=root
DB_PASS=

SESSION_LIFETIME=7200
UPLOAD_MAX_SIZE=5242880
```

**D. Set Permission**

Pastikan folder `assets/uploads/` memiliki permission write:

```bash
chmod 777 assets/uploads/
```

**E. Update Password Admin**

Jalankan query ini di phpMyAdmin:

```sql
-- Password: admin123
UPDATE users
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin';

-- Password: admin123
UPDATE users
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'user1';
```

### 3. Akses Aplikasi

**URL Aplikasi:**

```
http://localhost/camping_rental/
```

**Login Credentials:**

Admin:

- Username: `admin`
- Password: `admin123`

User:

- Username: `user1`
- Password: `admin123`

## 📖 Penggunaan

### Untuk User

1. **Register Akun Baru**

   - Klik "Register" di halaman utama
   - Isi form registrasi
   - Login dengan akun yang telah dibuat

2. **Browse & Booking**

   - Lihat katalog peralatan
   - Pilih item yang diinginkan
   - Klik "Lihat Detail"
   - Isi tanggal booking & jumlah
   - Klik "Booking Sekarang"

3. **Payment**

   - Pilih metode pembayaran
   - Konfirmasi pembayaran
   - Tunggu konfirmasi dari admin

4. **Track Booking**
   - Buka menu "My Bookings"
   - Lihat status booking Anda

### Untuk Admin

1. **Login Admin**

   - Login dengan akun admin
   - Akses admin panel

2. **Manage Items**

   - Tambah, edit, hapus items
   - Upload foto produk
   - Set harga & stok

3. **Manage Bookings**

   - Lihat semua bookings
   - Update status booking
   - Konfirmasi/batalkan booking

4. **Manage Payments**

   - Lihat semua pembayaran
   - Konfirmasi/tolak pembayaran
   - Track revenue

5. **Reports**
   - Lihat laporan revenue
   - Analytics booking
   - Export data

## 🔧 Konfigurasi

### Upload File Settings

Edit di `config/app.php`:

```php
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
```

### Session Settings

Edit di `config/app.php`:

```php
define('SESSION_LIFETIME', 7200); // 2 hours
```

### Email Settings (Optional)

Untuk fitur email notification, tambahkan:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-password');
```

## 🐛 Troubleshooting

### Error: Connection Failed

**Solusi:**

- Pastikan MySQL sudah running
- Cek kredensial database di `.env`
- Cek nama database sudah benar

### Error: Permission Denied (Upload)

**Solusi:**

```bash
chmod 777 assets/uploads/
```

### Error: Session

**Solusi:**

- Cek `session.save_path` di `php.ini`
- Pastikan folder session writable

### Error: 404 Not Found

**Solusi:**

- Pastikan `.htaccess` aktif
- Cek `mod_rewrite` enabled di Apache
- Cek base URL di `.env`

## 📝 Database Schema

### Tables

- **users** - User accounts
- **items** - Rental items
- **bookings** - Booking transactions
- **payments** - Payment records
- **reviews** - Item reviews
- **invoices** - Invoice records
- **inventory_history** - Stock history

## 🔐 Security Features

- Password hashing dengan bcrypt
- SQL Injection prevention (PDO Prepared Statements)
- XSS Protection
- CSRF Protection
- Session management
- File upload validation
- Input sanitization

## 📱 Responsive Design

Aplikasi ini fully responsive dan dapat diakses dari:

- 💻 Desktop
- 📱 Mobile
- 📱 Tablet

## 🎨 Customization

### Change Theme Color

Edit `assets/css/style.css`:

```css
:root {
  --primary-color: #0d6efd; /* Ganti dengan warna pilihan */
}
```

### Change Logo

Replace file di `assets/images/logo.png`

### Add Custom Menu

Edit `config/menu.json`

## 📊 Performance

- Menggunakan PDO untuk efisiensi database
- Image optimization
- CSS/JS minification
- Browser caching
- Gzip compression

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the project
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## 📄 License

This project is open-source and available under the MIT License.

## 👨‍💻 Developer

Developed with ❤️ for Camping Rental Management

## 📞 Support

Jika ada pertanyaan atau masalah:

- Create an issue
- Email: support@campingrental.com
- Documentation: [Link to docs]

## 🔄 Changelog

### Version 1.0.0 (2024)

- Initial release
- User management
- Booking system
- Payment system
- Admin panel
- Reports & analytics

## 🚀 Future Features

- [ ] Email notifications
- [ ] SMS notifications
- [ ] Online payment gateway
- [ ] Multi-language support
- [ ] Mobile app
- [ ] Invoice PDF export
- [ ] Advanced analytics
- [ ] Customer loyalty program

## 📚 Documentation

Untuk dokumentasi lengkap, kunjungi:

- User Guide: [link]
- Admin Guide: [link]
- API Documentation: [link]
- Developer Guide: [link]

---

**Happy Camping! 🏕️⛺**
