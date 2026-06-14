# Laporan Hasil Pengujian Otomatis — Gumolong Farm

**Tanggal Pengujian:** 2026-06-13  
**Framework:** Laravel 11 + PHPUnit  
**Total Pengujian:** 303 lulus, 0 gagal (652 assertion)

---

## Ringkasan per Modul

| No | Modul | Total Uji | Lulus | Gagal |
|----|-------|:---------:|:-----:|:-----:|
| 1 | Autentikasi (Login/Logout) | 15 | 15 | 0 |
| 2 | Auth Breeze (Register, Reset Password, dll.) | 19 | 19 | 0 |
| 3 | Profil Pengguna | 5 | 5 | 0 |
| 4 | Manajemen Pengguna | 13 | 13 | 0 |
| 5 | Hak Akses Berbasis Peran (RBAC) | 8 | 8 | 0 |
| 6 | Data Domba | 14 | 14 | 0 |
| 7 | Kandang | 9 | 9 | 0 |
| 8 | Tugas Harian | 11 | 11 | 0 |
| 9 | Template Tugas | 10 | 10 | 0 |
| 10 | Stok Pakan | 8 | 8 | 0 |
| 11 | Pakan Individual | 9 | 9 | 0 |
| 12 | Obat & Vaksin | 11 | 11 | 0 |
| 13 | Kesehatan (Rekam Medis & Vaksinasi) | 12 | 12 | 0 |
| 14 | Reproduksi (Perkawinan & Kelahiran) | 13 | 13 | 0 |
| 15 | Tracking Pertumbuhan (Penimbangan) | 8 | 8 | 0 |
| 16 | Silsilah & Inbreeding | 11 | 11 | 0 |
| 17 | Notifikasi | 8 | 8 | 0 |
| 18 | Mobile — Pengurus Kandang (PK) | 13 | 13 | 0 |
| 19 | Mobile — Kepala Kandang (KK) | 14 | 14 | 0 |
| 20 | Unit Test — Model Domba | 13 | 13 | 0 |
| 21 | Unit Test — Model Tugas Harian | 11 | 11 | 0 |
| **Total** | | **303** | **303** | **0** |

---

## Detail Skenario per Modul

### 1. Autentikasi (AuthTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Halaman login dapat diakses | ✅ |
| 2 | Super admin login diarahkan ke dashboard utama | ✅ |
| 3 | Admin login diarahkan ke dashboard utama | ✅ |
| 4 | Kepala kandang login diarahkan ke dashboard KK | ✅ |
| 5 | Pengurus kandang login diarahkan ke dashboard PK | ✅ |
| 6 | Login gagal dengan password salah | ✅ |
| 7 | Login gagal dengan email tidak dikenal | ✅ |
| 8 | User nonaktif tidak dapat login | ✅ |
| 9 | Login membutuhkan email dan password | ✅ |
| 10 | Login membutuhkan format email yang valid | ✅ |
| 11 | User terautentikasi dapat logout | ✅ |
| 12 | Admin yang sudah login diarahkan ke dashboard saat akses root | ✅ |
| 13 | PK yang sudah login diarahkan ke dashboard PK saat akses root | ✅ |
| 14 | Akses tanpa autentikasi ke dashboard diarahkan ke login | ✅ |
| 15 | `last_login` diperbarui saat login berhasil | ✅ |

### 2. Auth Breeze (RegistrationTest, PasswordResetTest, dll.)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Halaman registrasi dapat dirender | ✅ |
| 2 | User baru dapat mendaftar | ✅ |
| 3 | Halaman konfirmasi email dapat dirender | ✅ |
| 4 | Email verifikasi dapat dikirim ulang | ✅ |
| 5 | Email dapat diverifikasi | ✅ |
| 6 | Halaman konfirmasi password dapat dirender | ✅ |
| 7 | Password dapat dikonfirmasi | ✅ |
| 8 | Password tidak dapat dikonfirmasi dengan nilai salah | ✅ |
| 9 | Halaman reset password dapat dirender | ✅ |
| 10 | Link reset password dapat diminta | ✅ |
| 11 | Halaman reset password baru dapat dirender | ✅ |
| 12 | Password dapat direset dengan token valid | ✅ |
| 13 | Halaman ubah password dapat dirender | ✅ |
| 14 | Password dapat diperbarui | ✅ |
| 15 | Password benar harus diisi untuk pembaruan | ✅ |
| 16 | Halaman login Breeze dapat dirender | ✅ |
| 17 | User dapat autentikasi via halaman login | ✅ |
| 18 | User tidak dapat autentikasi dengan password salah | ✅ |
| 19 | User dapat logout | ✅ |

### 3. Profil Pengguna (ProfileTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Halaman profil dapat ditampilkan | ✅ |
| 2 | Informasi profil dapat diperbarui | ✅ |
| 3 | Email unik diperlukan saat memperbarui | ✅ |
| 4 | User dapat menghapus akun sendiri | ✅ |
| 5 | Password benar diperlukan untuk menghapus akun | ✅ |

### 4. Manajemen Pengguna (UserManagementTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Super admin dapat melihat daftar pengguna | ✅ |
| 2 | Admin dapat melihat daftar pengguna | ✅ |
| 3 | Kepala kandang tidak dapat mengakses manajemen pengguna (403) | ✅ |
| 4 | Super admin dapat membuat pengguna baru | ✅ |
| 5 | Validasi gagal jika field wajib kosong | ✅ |
| 6 | Validasi gagal jika email sudah terdaftar | ✅ |
| 7 | Admin biasa tidak dapat membuat akun super_admin (403) | ✅ |
| 8 | Super admin dapat memperbarui data pengguna | ✅ |
| 9 | Pengguna tidak dapat menonaktifkan akun sendiri | ✅ |
| 10 | Super admin dapat toggle status aktif pengguna | ✅ |
| 11 | Pengguna tidak dapat toggle status akun sendiri | ✅ |
| 12 | Admin tidak dapat mengubah status akun super_admin (403) | ✅ |
| 13 | Super admin dapat menghapus pengguna | ✅ |
| 14 | Admin tidak dapat menghapus pengguna (403) | ✅ |
| 15 | Super admin tidak dapat menghapus akun sendiri | ✅ |
| 16 | Super admin dapat mereset password pengguna | ✅ |
| 17 | Admin tidak dapat mereset password super_admin (403) | ✅ |

### 5. Hak Akses Berbasis Peran (RoleAccessTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Super admin dapat mengakses dashboard | ✅ |
| 2 | Admin dapat mengakses dashboard | ✅ |
| 3 | Kepala kandang diarahkan ke dashboard KK | ✅ |
| 4 | Pengurus kandang diarahkan ke dashboard PK | ✅ |
| 5 | Pengurus kandang tidak dapat mengakses halaman web admin (redirect mobile) | ✅ |
| 6 | Kepala kandang tidak dapat mengakses rute mobile PK (403) | ✅ |
| 7 | User nonaktif tidak dapat login | ✅ |
| 8 | Tamu tidak dapat mengakses halaman terproteksi | ✅ |

### 6. Data Domba (DombaTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat daftar domba | ✅ |
| 2 | Tamu tidak dapat mengakses daftar domba | ✅ |
| 3 | Halaman mendukung pencarian | ✅ |
| 4 | Halaman mendukung filter jenis kelamin | ✅ |
| 5 | Halaman mendukung filter status | ✅ |
| 6 | Admin dapat melihat detail domba (HTML) | ✅ |
| 7 | Admin dapat mengambil detail domba sebagai JSON | ✅ |
| 8 | Admin dapat membuat domba baru | ✅ |
| 9 | Validasi gagal jika field wajib kosong | ✅ |
| 10 | Validasi gagal jika ear_tag_id sudah digunakan | ✅ |
| 11 | Admin dapat memperbarui data domba | ✅ |
| 12 | Admin dapat menghapus domba (soft delete) | ✅ |
| 13 | Ear tag di-generate otomatis jika tidak diisi | ✅ |
| 14 | Ear tag gagal di-generate untuk jenis kelamin tidak valid | ✅ |

### 7. Kandang (KandangTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat daftar kandang | ✅ |
| 2 | Tamu tidak dapat mengakses daftar kandang | ✅ |
| 3 | Admin dapat membuat kandang baru | ✅ |
| 4 | Validasi gagal jika nama_kandang kosong | ✅ |
| 5 | Validasi gagal jika nama kandang duplikat | ✅ |
| 6 | Admin dapat memperbarui kandang | ✅ |
| 7 | Admin dapat menghapus kandang kosong | ✅ |
| 8 | Kandang dengan domba tidak dapat dihapus | ✅ |
| 9 | Kepala kandang dapat melihat daftar kandang | ✅ |

### 8. Tugas Harian (TugasHarianTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat daftar tugas harian | ✅ |
| 2 | Tamu tidak dapat mengakses tugas harian | ✅ |
| 3 | Admin dapat membuat tugas baru | ✅ |
| 4 | Validasi gagal jika field wajib kosong | ✅ |
| 5 | Admin dapat memperbarui tugas | ✅ |
| 6 | Admin dapat menghapus tugas | ✅ |
| 7 | Admin dapat menandai tugas selesai | ✅ |
| 8 | Halaman mendukung filter tanggal | ✅ |
| 9 | Halaman mendukung filter kandang | ✅ |
| 10 | Tugas harian dapat dilihat di tampilan mobile | ✅ |
| 11 | PK dapat menyelesaikan tugas dari tampilan mobile | ✅ |

### 9. Template Tugas Rutin (TemplateTugasTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat daftar template tugas | ✅ |
| 2 | Tamu tidak dapat mengakses template tugas | ✅ |
| 3 | Admin dapat membuat template tugas baru | ✅ |
| 4 | Validasi gagal jika field wajib kosong | ✅ |
| 5 | Admin dapat memperbarui template | ✅ |
| 6 | Admin dapat menghapus template | ✅ |
| 7 | Generate tugas dari template berhasil | ✅ |
| 8 | Hanya super_admin/admin yang dapat mengelola template | ✅ |
| 9 | Kepala kandang tidak dapat membuat template | ✅ |
| 10 | Template mendukung frekuensi harian/mingguan | ✅ |

### 10. Stok Pakan (StokPakanTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat halaman stok pakan | ✅ |
| 2 | Tamu tidak dapat mengakses stok pakan | ✅ |
| 3 | Pengurus kandang tidak dapat mengakses stok pakan (redirect mobile) | ✅ |
| 4 | Admin dapat mencatat stok masuk (jumlah stok bertambah) | ✅ |
| 5 | Validasi gagal jika pakan_id tidak valid | ✅ |
| 6 | Validasi gagal jika jumlah masuk adalah nol | ✅ |
| 7 | Admin dapat mencatat pemberian pakan ke domba (stok berkurang) | ✅ |
| 8 | Catat keluar gagal jika jumlah melebihi stok tersedia | ✅ |
| 9 | Catat keluar gagal jika ear_tag_id tidak valid | ✅ |
| 10 | Pemberian pakan dicatat ke tabel pemberian_pakan | ✅ |

### 11. Pakan Individual (PakanIndividualTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat halaman pakan individual | ✅ |
| 2 | Tamu tidak dapat mengakses pakan individual | ✅ |
| 3 | Halaman mendukung filter kategori | ✅ |
| 4 | Admin dapat mencatat pemberian pakan individual (JSON response) | ✅ |
| 5 | Store gagal jika stok tidak cukup (HTTP 422) | ✅ |
| 6 | Validasi gagal jika field wajib kosong | ✅ |
| 7 | Validasi gagal jika sesi tidak valid | ✅ |
| 8 | Search domba mengembalikan array kosong jika query kosong | ✅ |
| 9 | Search domba mengembalikan hasil yang sesuai | ✅ |
| 10 | Admin dapat mengambil statistik FCR domba | ✅ |

### 12. Obat & Vaksin (ObatVaksinTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat daftar obat dan vaksin | ✅ |
| 2 | Tamu tidak dapat mengakses halaman obat | ✅ |
| 3 | Admin dapat melihat detail obat sebagai JSON | ✅ |
| 4 | Admin dapat menambah obat/vaksin baru | ✅ |
| 5 | Validasi gagal jika field wajib kosong | ✅ |
| 6 | Validasi gagal jika nama obat duplikat | ✅ |
| 7 | Admin dapat memperbarui data obat | ✅ |
| 8 | Admin dapat menghapus obat yang tidak digunakan | ✅ |
| 9 | Obat yang sedang digunakan tidak dapat dihapus | ✅ |
| 10 | Admin dapat mencari rekam medis untuk obat (JSON) | ✅ |
| 11 | Search rekam medis mengembalikan array | ✅ |

### 13. Kesehatan — Rekam Medis & Vaksinasi (KesehatanTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat halaman kesehatan | ✅ |
| 2 | Tamu tidak dapat mengakses halaman kesehatan | ✅ |
| 3 | Admin dapat mencatat rekam medis baru | ✅ |
| 4 | Validasi gagal jika field wajib kosong | ✅ |
| 5 | Admin dapat memperbarui rekam medis | ✅ |
| 6 | Admin dapat menghapus rekam medis | ✅ |
| 7 | Admin dapat memindahkan domba karantina ke kandang lain | ✅ |
| 8 | Admin dapat mencatat vaksinasi baru | ✅ |
| 9 | Validasi vaksinasi gagal jika field wajib kosong | ✅ |
| 10 | Admin dapat menghapus catatan vaksinasi | ✅ |
| 11 | Admin dapat mengubah status domba menjadi sembuh | ✅ |
| 12 | Admin dapat mengubah status domba menjadi dalam_perawatan | ✅ |

### 14. Reproduksi — Perkawinan & Kelahiran (ReproduksiTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat halaman reproduksi | ✅ |
| 2 | Tamu tidak dapat mengakses halaman reproduksi | ✅ |
| 3 | Admin dapat mencatat perkawinan baru | ✅ |
| 4 | HPL otomatis dihitung 150 hari dari tanggal kawin | ✅ |
| 5 | Validasi gagal jika field wajib kosong | ✅ |
| 6 | Validasi gagal jika metode tidak valid | ✅ |
| 7 | Admin dapat memperbarui data perkawinan | ✅ |
| 8 | Admin dapat menghapus perkawinan tanpa kelahiran | ✅ |
| 9 | Perkawinan dengan kelahiran tidak dapat dihapus | ✅ |
| 10 | Admin dapat konfirmasi kebuntingan | ✅ |
| 11 | Konfirmasi gagal jika status bukan menunggu_konfirmasi | ✅ |
| 12 | Admin dapat mencatat kelahiran | ✅ |
| 13 | Validasi kelahiran gagal jika field wajib kosong | ✅ |

### 15. Tracking Pertumbuhan — Penimbangan (TrackingPertumbuhanTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat halaman tracking pertumbuhan | ✅ |
| 2 | Tamu tidak dapat mengakses tracking pertumbuhan | ✅ |
| 3 | Halaman mendukung filter tanggal dan kandang | ✅ |
| 4 | Admin dapat melihat detail penimbangan satu domba | ✅ |
| 5 | Detail domba tidak ada mengembalikan 404 | ✅ |
| 6 | PK dapat menyimpan data penimbangan (ADG dihitung otomatis) | ✅ |
| 7 | ADG bernilai null untuk penimbangan pertama (tidak ada pembanding) | ✅ |
| 8 | Validasi penimbangan gagal jika field wajib kosong | ✅ |

### 16. Silsilah & Deteksi Inbreeding (SilsilahTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat halaman silsilah | ✅ |
| 2 | Tamu tidak dapat mengakses silsilah | ✅ |
| 3 | Halaman mendukung pencarian | ✅ |
| 4 | Halaman mendukung filter kategori | ✅ |
| 5 | Admin dapat melihat detail silsilah (HTML) | ✅ |
| 6 | Admin dapat mengambil data pedigree sebagai JSON | ✅ |
| 7 | Show mengembalikan 404 untuk domba yang tidak ada | ✅ |
| 8 | Admin dapat cek inbreeding antara dua domba | ✅ |
| 9 | Cek inbreeding gagal jika field kosong | ✅ |
| 10 | Domba tanpa leluhur bersama memiliki COI = 0 dan status aman | ✅ |
| 11 | Admin dapat mendapatkan rekomendasi pejantan | ✅ |
| 12 | Rekomendasi pejantan gagal tanpa induk_id | ✅ |

### 17. Notifikasi (NotifikasiTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Admin dapat melihat halaman notifikasi | ✅ |
| 2 | Tamu tidak dapat mengakses notifikasi | ✅ |
| 3 | Halaman notifikasi dapat difilter berdasarkan tipe | ✅ |
| 4 | Admin dapat menandai satu notifikasi sebagai dibaca | ✅ |
| 5 | Admin dapat menandai semua notifikasi sebagai dibaca | ✅ |
| 6 | Admin dapat mengambil jumlah notifikasi belum dibaca | ✅ |
| 7 | Admin dapat menghapus notifikasi miliknya | ✅ |
| 8 | Admin tidak dapat menghapus notifikasi milik user lain | ✅ |

### 18. Mobile — Pengurus Kandang / PK (MobilePKTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Pengurus kandang dapat mengakses dashboard PK | ✅ |
| 2 | Tamu tidak dapat mengakses dashboard PK | ✅ |
| 3 | Admin tidak dapat mengakses dashboard PK (403) | ✅ |
| 4 | Pengurus kandang dapat melihat daftar tugas | ✅ |
| 5 | Pengurus kandang dapat membuka halaman timbangan | ✅ |
| 6 | PK dapat menyimpan data timbangan | ✅ |
| 7 | Store timbangan gagal jika field wajib kosong | ✅ |
| 8 | Data timbangan tersimpan dengan status pending (menunggu validasi) | ✅ |
| 9 | PK dapat membuka halaman kesehatan | ✅ |
| 10 | PK dapat melaporkan kondisi kesehatan domba | ✅ |
| 11 | Store kesehatan gagal jika field wajib kosong | ✅ |
| 12 | Store kesehatan gagal jika tingkat_keparahan tidak valid | ✅ |
| 13 | PK dapat membuka halaman kelahiran | ✅ |
| 14 | Store kelahiran gagal jika tidak ada perkawinan aktif | ✅ |
| 15 | Store kelahiran berhasil jika ada perkawinan aktif | ✅ |

### 19. Mobile — Kepala Kandang / KK (MobileKKTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Kepala kandang dapat mengakses dashboard KK | ✅ |
| 2 | Tamu tidak dapat mengakses dashboard KK | ✅ |
| 3 | Pengurus kandang tidak dapat mengakses dashboard KK (redirect mobile) | ✅ |
| 4 | Kepala kandang dapat memonitor tugas harian | ✅ |
| 5 | Kepala kandang dapat melihat daftar laporan kesehatan | ✅ |
| 6 | Kepala kandang dapat konfirmasi status kesehatan domba | ✅ |
| 7 | Konfirmasi kesehatan gagal jika action tidak valid | ✅ |
| 8 | Kepala kandang dapat melihat halaman reproduksi | ✅ |
| 9 | Kepala kandang dapat konfirmasi kebuntingan | ✅ |
| 10 | Konfirmasi kebuntingan gagal jika status tidak valid | ✅ |
| 11 | Kepala kandang dapat melihat halaman validasi timbangan | ✅ |
| 12 | Kepala kandang dapat memvalidasi data timbangan (valid) | ✅ |
| 13 | Kepala kandang dapat menolak data timbangan (ditolak) | ✅ |
| 14 | Process validasi gagal jika action tidak valid | ✅ |

### 20. Unit Test — Model Domba (DombaModelTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Generate ear tag menggunakan prefix J untuk jantan | ✅ |
| 2 | Generate ear tag menggunakan prefix B untuk betina | ✅ |
| 3 | Format ear tag sesuai pola | ✅ |
| 4 | Ear tag naik secara sekuensial | ✅ |
| 5 | Urutan sekuensial independen per jenis kelamin | ✅ |
| 6 | `bobot_terakhir` mengembalikan null jika belum ada penimbangan | ✅ |
| 7 | `bobot_terakhir` mengembalikan berat terbaru | ✅ |
| 8 | Nama tabel adalah `domba` | ✅ |
| 9 | Primary key adalah `ear_tag_id` | ✅ |
| 10 | Primary key tidak auto-increment | ✅ |
| 11 | Domba berelasi ke Kandang | ✅ |
| 12 | Domba berelasi ke banyak Penimbangan | ✅ |
| 13 | Domba menggunakan soft deletes | ✅ |

### 21. Unit Test — Model Tugas Harian (TugasHarianModelTest)

| # | Skenario | Hasil |
|---|----------|:-----:|
| 1 | Scope `hariIni` mengembalikan hanya tugas hari ini | ✅ |
| 2 | Scope `tanggal` memfilter berdasarkan tanggal tertentu | ✅ |
| 3 | Filter berdasarkan kandang_id berfungsi | ✅ |
| 4 | Scope `belumSelesai` mengecualikan tugas selesai | ✅ |
| 5 | `prioritas_color` mengembalikan warna yang benar | ✅ |
| 6 | `tipe_label` mengembalikan label yang benar | ✅ |
| 7 | `status_label` mengembalikan label yang benar | ✅ |
| 8 | `durasi` mengembalikan null tanpa waktu | ✅ |
| 9 | `durasi` mengembalikan menit untuk durasi kurang dari 1 jam | ✅ |
| 10 | `durasi` mengembalikan jam untuk durasi lebih dari 1 jam | ✅ |
| 11 | Nama tabel adalah `tugas_harian` | ✅ |
| 12 | Fillable berisi field yang diperlukan | ✅ |

---

## Bug yang Ditemukan dan Diperbaiki

Selama pembuatan pengujian, ditemukan beberapa bug di kode produksi yang langsung diperbaiki:

| # | File | Deskripsi Bug | Perbaikan |
|---|------|---------------|-----------|
| 1 | `app/Http/Controllers/StokPakanController.php` | `PemberianPakan::create()` menyertakan kolom `satuan` dan `sisa_stok` yang tidak ada di tabel `pemberian_pakan` → HTTP 500 | Menghapus kolom tersebut dari array create |
| 2 | `app/Http/Controllers/StokPakanController.php` | Overflow stok melempar `\Exception` yang tidak ditangkap di dalam `DB::transaction()` → HTTP 500 alih-alih redirect dengan error | Menambahkan pengecekan sebelum transaksi dan mengembalikan `redirect()->back()->withErrors()` |
| 3 | `routes/web.php` | Rute `GET /pakan-individual/search-domba` dideklarasikan setelah `GET /pakan-individual/{earTag}` sehingga tidak pernah dicocokkan | Memindahkan rute `search-domba` ke atas `{earTag}` |
| 4 | `routes/web.php` | `Route::resource('notifikasi', ...)` menambahkan rute `GET /notifikasi/{notifikasi}` (show) yang tidak ada pada controller, menyebabkan `GET /notifikasi/unread-count` menghasilkan HTTP 500 | Menambahkan `->except(['show', 'create', 'edit'])` |
| 5 | `tests/` (data uji) | Kolom `is_read` tidak ada; nama kolom yang benar adalah `sudah_dibaca` | Mengganti nama kolom di seluruh test notifikasi |
| 6 | `tests/` (data uji) | Kolom `user_id` NOT NULL pada tabel `perkawinan` dan `kelahiran` tidak disertakan saat insert data uji | Menambahkan `user_id` pada semua insert langsung |

---

## Keterangan Teknis

- **Framework pengujian:** PHPUnit (bawaan Laravel 11)
- **Isolasi database:** Trait `DatabaseTransactions` — semua perubahan DB di-rollback setelah setiap test
- **Vite:** `withoutVite()` ditambahkan di `tests/TestCase.php` agar test tidak memerlukan build Blade
- **Role middleware:** `EnsureRole` diuji secara eksplisit untuk setiap endpoint sensitif
- **File test baru:** StokPakanTest, ObatVaksinTest, KesehatanTest, ReproduksiTest, TrackingPertumbuhanTest, NotifikasiTest, UserManagementTest, SilsilahTest, PakanIndividualTest, MobilePKTest, MobileKKTest
