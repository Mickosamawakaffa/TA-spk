# BAB 4 - PERANCANGAN SISTEM INFORMASI
## 4.2 Struktur Database

Dalam tahap perancangan model, salah satu aspek penting yang perlu dipersiapkan adalah struktur database. Struktur ini dirancang untuk memastikan bahwa penyimpanan dan pengelolaan data dalam aplikasi dapat dilakukan secara efisien dan terorganisir. Dengan adanya struktur database yang jelas, setiap tabel dan atribut yang digunakan dapat dipahami dengan lebih mudah, sehingga memudahkan pengembang dalam proses implementasi dan pengelolaan data di dalam aplikasi.

### 4.2.1 Entity Relationship Diagram (ERD)

[INSERT SCREENSHOT DIAGRAM DI SINI]

Diagram di atas menunjukkan Entity Relationship Diagram (ERD) dari sistem basis data SPK Kontrakan & Laundry. Diagram ini menampilkan 8 tabel inti dengan relasi antar tabel yang menggambarkan bagaimana data saling terhubung dalam sistem.

---

### 4.2.2 Penjelasan Tabel dan Atribut

#### a. Tabel users

Tabel users digunakan untuk menyimpan data pengguna dalam sistem. Setiap pengguna memiliki informasi dasar seperti id, name, email, dan password, yang digunakan untuk autentikasi dan identifikasi. Selain itu, tabel ini juga mencatat alamat pengguna serta tipe_pengguna, yang menentukan peran dalam sistem, yaitu sebagai user (mahasiswa), admin, atau super admin.

Untuk keamanan, tabel ini menyediakan kolom verification_token, email_verified_at, dan is_verified, yang digunakan dalam proses verifikasi akun. Selain itu, terdapat remember_token untuk mendukung fitur "ingat saya" dalam sistem login.

Tabel ini juga menyimpan kolom reset_password_token dan reset_password_expires_at, yang digunakan dalam proses pemulihan kata sandi. reset_password_token menyimpan token unik yang dikirim ke pengguna untuk mereset kata sandi, sementara reset_password_expires_at mencatat batas waktu kedaluwarsa token tersebut.

Terakhir, tabel ini menyimpan waktu pembuatan dan perubahan akun melalui kolom created_at dan updated_at, sehingga memudahkan pengelolaan data pengguna secara terstruktur dan aman. Struktur tabel users dapat dilihat seperti pada Tabel 4.1.

**Tabel 4.1 Tabel users**

| No | Field | Type | Constraint |
|----|-------|------|-----------|
| 1 | id | bigint | Primary Key |
| 2 | name | varchar(255) | Not Null |
| 3 | email | varchar(255) | Unique, Not Null |
| 4 | password | varchar(255) | Not Null |
| 5 | phone | varchar(20) | - |
| 6 | role | varchar(50) | Not Null (super_admin, admin, user) |
| 7 | email_verified_at | timestamp | Nullable |
| 8 | created_at | timestamp | Not Null |
| 9 | updated_at | timestamp | Not Null |
| 10 | deleted_at | timestamp | Nullable (Soft Delete) |

---

#### b. Tabel kontrakans

Tabel kontrakans digunakan untuk menyimpan data kontrakan (tempat tinggal) yang ditawarkan kepada pengguna. Setiap kontrakan memiliki informasi dasar seperti nama, alamat lengkap, dan kontak pemilik melalui no_whatsapp. Selain itu, tabel ini juga mencatat lokasi geografis kontrakan dengan latitude dan longitude untuk fitur pencarian berbasis lokasi.

Tabel ini juga menyimpan informasi harga sewa per bulan dalam kolom harga, serta jarak kontrakan dari kampus dalam kolom jarak untuk membantu pengguna dalam pencarian. Tabel ini mencatat fasilitas yang tersedia, jumlah kamar, dan jumlah kamar mandi.

Tabel ini juga mencatat status kontrakan, yang dapat berupa "tersedia" (available), "dipesan" (booked), atau "terisi" (occupied), untuk memudahkan pemantauan dan pengelolaan ketersediaan kontrakan oleh pihak terkait. Dengan adanya tabel ini, sistem dapat mengelola data kontrakan secara lebih terstruktur dan efisien. Struktur tabel kontrakans dapat dilihat seperti pada Tabel 4.2.

**Tabel 4.2 Tabel kontrakans**

| No | Field | Type | Constraint |
|----|-------|------|-----------|
| 1 | id | bigint | Primary Key |
| 2 | nama | varchar(255) | Not Null |
| 3 | alamat | text | Not Null |
| 4 | no_whatsapp | varchar(20) | - |
| 5 | latitude | decimal(10,8) | - |
| 6 | longitude | decimal(10,8) | - |
| 7 | harga | int | Not Null |
| 8 | jarak | decimal(5,2) | - |
| 9 | fasilitas | text | - |
| 10 | jumlah_kamar | int | - |
| 11 | bathroom_count | int | - |
| 12 | foto | varchar(255) | - |
| 13 | status | varchar(50) | Not Null (available, booked, occupied) |
| 14 | occupied_until | date | Nullable |
| 15 | created_at | timestamp | Not Null |
| 16 | updated_at | timestamp | Not Null |

---

#### c. Tabel bookings

Tabel bookings digunakan untuk menyimpan data pemesanan kontrakan yang dilakukan oleh pengguna. Setiap pemesanan memiliki relasi dengan tabel users melalui user_id dan tabel kontrakans melalui kontrakan_id, untuk mencatat siapa yang melakukan pemesanan dan kontrakan mana yang dipesan.

Tabel ini mencatat tanggal check-in melalui kolom start_date dan tanggal check-out melalui kolom end_date. Status pemesanan dapat berupa "pending", "confirmed", "checked_in", "completed", atau "cancelled", untuk mencerminkan tahap-tahap proses pemesanan.

Tabel ini juga menyimpan jumlah pembayaran dalam kolom amount dan status pembayaran dalam kolom payment_status, yang dapat berupa "unpaid", "paid", atau "refunded". Selain itu, tabel ini juga mencatat metode pembayaran yang digunakan, bukti pembayaran, serta waktu pembayaran melalui kolom payment_method, payment_proof, dan paid_at.

Tabel ini juga menyimpan waktu konfirmasi, check-in, check-out, dan pembatalan melalui kolom confirmed_at, checked_in_at, checked_out_at, dan cancelled_at, untuk audit trail yang lebih lengkap. Struktur tabel bookings dapat dilihat seperti pada Tabel 4.3.

**Tabel 4.3 Tabel bookings**

| No | Field | Type | Constraint |
|----|-------|------|-----------|
| 1 | id | bigint | Primary Key |
| 2 | kontrakan_id | bigint | Foreign Key → kontrakans.id |
| 3 | user_id | bigint | Foreign Key → users.id |
| 4 | start_date | date | Not Null |
| 5 | end_date | date | Not Null |
| 6 | status | varchar(50) | Not Null |
| 7 | amount | decimal(12,2) | Not Null |
| 8 | payment_status | varchar(50) | Not Null |
| 9 | payment_method | varchar(50) | - |
| 10 | payment_proof | varchar(255) | Nullable |
| 11 | paid_at | timestamp | Nullable |
| 12 | confirmed_at | timestamp | Nullable |
| 13 | checked_in_at | timestamp | Nullable |
| 14 | checked_out_at | timestamp | Nullable |
| 15 | cancelled_at | timestamp | Nullable |
| 16 | notes | text | Nullable |
| 17 | created_at | timestamp | Not Null |
| 18 | updated_at | timestamp | Not Null |

---

#### d. Tabel laundry

Tabel laundry digunakan untuk menyimpan data layanan laundry yang tersedia. Setiap layanan laundry memiliki informasi dasar seperti nama, alamat lengkap, dan kontak melalui no_whatsapp. Selain itu, tabel ini juga mencatat lokasi geografis dengan latitude dan longitude untuk fitur pencarian berbasis lokasi.

Tabel ini mencatat jarak dari kampus dalam kolom jarak, serta fasilitas yang tersedia melalui kolom fasilitas. Tabel ini juga menyimpan jam operasional layanan laundry melalui kolom jam_buka dan jam_tutup.

Tabel ini juga mencatat status layanan laundry, yang dapat berupa "active" atau "inactive", untuk memudahkan pengelolaan ketersediaan layanan. Struktur tabel laundry dapat dilihat seperti pada Tabel 4.4.

**Tabel 4.4 Tabel laundry**

| No | Field | Type | Constraint |
|----|-------|------|-----------|
| 1 | id | bigint | Primary Key |
| 2 | nama | varchar(255) | Not Null |
| 3 | alamat | text | Not Null |
| 4 | no_whatsapp | varchar(20) | - |
| 5 | latitude | decimal(10,8) | - |
| 6 | longitude | decimal(10,8) | - |
| 7 | jarak | decimal(5,2) | - |
| 8 | fasilitas | text | - |
| 9 | jam_buka | time | - |
| 10 | jam_tutup | time | - |
| 11 | status | varchar(50) | Not Null (active, inactive) |
| 12 | foto | varchar(255) | - |
| 13 | created_at | timestamp | Not Null |
| 14 | updated_at | timestamp | Not Null |

---

#### e. Tabel layanan_laundry

Tabel layanan_laundry digunakan untuk menyimpan data jenis dan paket layanan yang ditawarkan oleh setiap layanan laundry. Setiap layanan memiliki relasi dengan tabel laundry melalui laundry_id.

Tabel ini mencatat jenis layanan melalui kolom jenis_layanan, nama paket melalui kolom nama_paket, harga melalui kolom harga, dan estimasi waktu penyelesaian melalui kolom estimasi_selesai. Tabel ini juga menyimpan deskripsi layanan dan status ketersediaan layanan melalui kolom deskripsi dan status.

Struktur tabel layanan_laundry dapat dilihat seperti pada Tabel 4.5.

**Tabel 4.5 Tabel layanan_laundry**

| No | Field | Type | Constraint |
|----|-------|------|-----------|
| 1 | id | bigint | Primary Key |
| 2 | laundry_id | bigint | Foreign Key → laundry.id |
| 3 | jenis_layanan | varchar(50) | Not Null |
| 4 | nama_paket | varchar(255) | Not Null |
| 5 | harga | decimal(10,2) | Not Null |
| 6 | estimasi_selesai | varchar(50) | - |
| 7 | deskripsi | text | - |
| 8 | status | varchar(50) | Not Null (active, inactive) |
| 9 | rating | decimal(3,2) | Nullable |
| 10 | waktu_proses | int | - |
| 11 | created_at | timestamp | Not Null |
| 12 | updated_at | timestamp | Not Null |

---

#### f. Tabel reviews

Tabel reviews digunakan untuk menyimpan data review dan rating yang diberikan oleh pengguna terhadap kontrakan atau layanan laundry. Setiap review memiliki relasi dengan tabel users melalui user_id.

Tabel ini menggunakan pendekatan polymorphic relationships, di mana kolom type menentukan jenis item yang di-review (kontrakan atau laundry), dan kolom item_id menyimpan ID dari item yang di-review. Tabel ini juga menyimpan rating dalam skala 1-5 melalui kolom rating, serta isi review melalui kolom review.

Struktur tabel reviews dapat dilihat seperti pada Tabel 4.6.

**Tabel 4.6 Tabel reviews**

| No | Field | Type | Constraint |
|----|-------|------|-----------|
| 1 | id | bigint | Primary Key |
| 2 | user_id | bigint | Foreign Key → users.id |
| 3 | type | varchar(50) | Not Null (kontrakan, laundry) |
| 4 | item_id | int | Not Null |
| 5 | rating | int | Not Null (1-5) |
| 6 | review | text | - |
| 7 | created_at | timestamp | Not Null |
| 8 | updated_at | timestamp | Not Null |

---

#### g. Tabel galeri

Tabel galeri digunakan untuk menyimpan data foto yang terkait dengan kontrakan atau layanan laundry. Setiap foto memiliki relasi dengan item yang di-referensikan melalui pendekatan polymorphic relationships.

Tabel ini menggunakan kolom type untuk menentukan jenis item (kontrakan atau laundry) dan kolom item_id untuk menyimpan ID item. Kolom foto menyimpan nama file atau path foto, kolom urutan menentukan urutan tampilan foto, dan kolom is_primary menandakan apakah foto tersebut adalah foto utama.

Struktur tabel galeri dapat dilihat seperti pada Tabel 4.7.

**Tabel 4.7 Tabel galeri**

| No | Field | Type | Constraint |
|----|-------|------|-----------|
| 1 | id | bigint | Primary Key |
| 2 | type | varchar(50) | Not Null (kontrakan, laundry) |
| 3 | item_id | int | Not Null |
| 4 | foto | varchar(255) | Not Null |
| 5 | urutan | int | Not Null |
| 6 | is_primary | boolean | Not Null |
| 7 | caption | text | Nullable |
| 8 | created_at | timestamp | Not Null |
| 9 | updated_at | timestamp | Not Null |

---

### 4.2.3 Penjelasan Relasi Antar Tabel

Berikut adalah penjelasan mengenai relasi antar tabel dalam sistem basis data:

| Relasi | Jenis | Keterangan |
|--------|-------|-----------|
| USERS → BOOKINGS | 1:M | Seorang pengguna dapat melakukan banyak pemesanan kontrakan |
| KONTRAKANS → BOOKINGS | 1:M | Satu kontrakan dapat di-booking multiple times oleh berbagai pengguna |
| USERS → REVIEWS | 1:M | Seorang pengguna dapat memberikan banyak review |
| REVIEWS → KONTRAKANS/LAUNDRY | M:1 (Polymorphic) | Review dapat diberikan untuk kontrakan atau layanan laundry |
| REVIEWS → USERS | M:1 | Banyak review dapat berasal dari satu pengguna |
| LAUNDRY → LAYANAN_LAUNDRY | 1:M | Satu layanan laundry dapat menawarkan berbagai jenis/paket layanan |
| GALERI → KONTRAKANS/LAUNDRY | M:1 (Polymorphic) | Foto dapat terkait dengan kontrakan atau layanan laundry |

---

### 4.2.4 Normalisasi Database

Database yang dirancang telah dinormalisasi hingga bentuk normal ketiga (3NF) dengan karakteristik sebagai berikut:

1. **Tidak ada redundansi data** - Setiap data hanya disimpan di satu tempat
2. **Setiap atribut non-key bergantung pada key** - Tidak ada ketergantungan pada atribut non-key
3. **Tidak ada transitive dependency** - Setiap atribut hanya bergantung pada primary key
4. **Efficient indexing** - Foreign keys dan kolom pencarian telah diindeks untuk performa query yang optimal

---

### 4.2.5 Fitur Khusus Database

#### Polymorphic Relationships
Sistem menggunakan polymorphic relationships pada tabel reviews dan galeri, memungkinkan kedua tabel tersebut terhubung dengan multiple entities (kontrakan dan laundry) menggunakan kombinasi kolom type dan item_id, sehingga lebih fleksibel dan efficient dalam mengelola data.

#### Auto-Sync Status
Sistem secara otomatis melakukan sinkronisasi status kontrakan berdasarkan status booking. Ketika booking dihapus atau dibatalkan, sistem akan memperbarui status kontrakan secara otomatis:
- Jika ada booking dengan status checked_in → status kontrakan = "occupied"
- Jika ada booking dengan status confirmed/pending → status kontrakan = "booked"
- Jika tidak ada booking aktif → status kontrakan = "available"

#### Soft Delete
Tabel users menggunakan soft delete mechanism, di mana data pengguna tidak benar-benar dihapus dari database melainkan ditandai dengan deleted_at timestamp, sehingga data tetap tersimpan untuk audit trail dan history.

---

Dengan struktur database yang telah dirancang dengan baik ini, sistem dapat mengelola data dengan efisien, terorganisir, dan aman sesuai dengan kebutuhan aplikasi SPK Kontrakan & Laundry.
