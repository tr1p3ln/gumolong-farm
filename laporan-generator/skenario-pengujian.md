# Dokumen Skenario Pengujian — Gumolong Farm

**Tanggal:** 2026-06-13  
**Framework:** Laravel 11 + PHPUnit  
**Total Kasus Uji:** 303 | **Lulus:** 303 | **Gagal:** 0

> **Kolom tabel:**  
> **ID** — kode unik kasus uji  
> **Skenario** — nama skenario  
> **Prasyarat / Login sebagai** — kondisi awal  
> **Langkah Pengujian** — aksi yang dilakukan (URL, klik, isi form, input)  
> **Data Input** — nilai yang dimasukkan  
> **Hasil yang Diharapkan** — respons / perubahan DB yang diharapkan  
> **Status** — ✅ Lulus / ❌ Gagal

---

## Modul 1 — Autentikasi (Login & Logout)

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| A-01 | Halaman login dapat diakses | Tidak login | Buka `GET /login` | — | HTTP 200, halaman login tampil | ✅ |
| A-02 | Super Admin login → dashboard utama | Tidak login | Buka `/login`, isi form, klik tombol **Masuk** | Email: akun super_admin aktif, Password: `password` | Redirect ke `/dashboard` | ✅ |
| A-03 | Admin login → dashboard utama | Tidak login | Buka `/login`, isi form, klik tombol **Masuk** | Email: akun admin aktif, Password: `password` | Redirect ke `/dashboard` | ✅ |
| A-04 | Kepala Kandang login → dashboard KK | Tidak login | Buka `/login`, isi form, klik tombol **Masuk** | Email: akun kepala_kandang aktif, Password: `password` | Redirect ke `/kk/dashboard` | ✅ |
| A-05 | Pengurus Kandang login → dashboard PK | Tidak login | Buka `/login`, isi form, klik tombol **Masuk** | Email: akun pengurus_kandang aktif, Password: `password` | Redirect ke `/pk/dashboard` | ✅ |
| A-06 | Login gagal — password salah | Tidak login | Buka `/login`, isi form, klik tombol **Masuk** | Email: akun valid, Password: `salah123` | Tetap di `/login`, muncul pesan error pada field email, tidak tersesi | ✅ |
| A-07 | Login gagal — email tidak terdaftar | Tidak login | Buka `/login`, isi form, klik tombol **Masuk** | Email: `tidak@ada.com`, Password: `password` | Tetap di `/login`, muncul pesan error pada field email | ✅ |
| A-08 | Akun nonaktif tidak dapat login | Tidak login | Buka `/login`, isi form, klik tombol **Masuk** | Email: akun berstatus `nonaktif`, Password: `password` | Tetap di `/login`, muncul pesan error, tidak tersesi | ✅ |
| A-09 | Validasi — email dan password wajib diisi | Tidak login | Buka `/login`, langsung klik tombol **Masuk** tanpa isi form | — (form kosong) | Muncul pesan validasi pada field `email` dan `password` | ✅ |
| A-10 | Validasi — format email harus valid | Tidak login | Buka `/login`, isi field Email dengan teks bukan email | Email: `bukan-email`, Password: `password` | Muncul pesan validasi format email | ✅ |
| A-11 | Logout berhasil | Login sebagai user mana saja | Klik tombol **Keluar / Logout** di navbar | — | Redirect ke `/login`, sesi berakhir | ✅ |
| A-12 | Admin yang sudah login akses root `/` → redirect dashboard | Login sebagai Admin | Buka `GET /` | — | Redirect ke `/dashboard` | ✅ |
| A-13 | PK yang sudah login akses root `/` → redirect dashboard PK | Login sebagai Pengurus Kandang | Buka `GET /` | — | Redirect ke `/pk/dashboard` | ✅ |
| A-14 | Akses halaman terproteksi tanpa login → redirect login | Tidak login | Buka `GET /dashboard` | — | Redirect ke `/login` | ✅ |
| A-15 | `last_login` diperbarui setelah login berhasil | Tidak login | Lakukan login berhasil | Email & password valid | Kolom `last_login` di tabel `user` terisi dengan waktu terkini | ✅ |

---

## Modul 2 — Autentikasi Breeze (Registrasi & Reset Password)

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| B-01 | Halaman registrasi dapat dirender | Tidak login | Buka `GET /register` | — | HTTP 200 | ✅ |
| B-02 | User baru dapat mendaftar | Tidak login | Buka `/register`, isi semua field, klik **Daftar** | name: `User Baru`, email: email unik, password: `Password1!`, password_confirmation: sama | Akun dibuat, user tersesi, redirect ke dashboard | ✅ |
| B-03 | Halaman konfirmasi email dapat dirender | Login, email belum terverifikasi | Buka `GET /verify-email` | — | HTTP 200 | ✅ |
| B-04 | Email verifikasi dapat dikirim ulang | Login, email belum terverifikasi | Klik tombol **Kirim Ulang Email Verifikasi** | — | HTTP 200, notifikasi terkirim | ✅ |
| B-05 | Email dapat diverifikasi | Login, punya link verifikasi | Buka URL verifikasi yang dikirim ke email | URL token verifikasi valid | Email terverifikasi | ✅ |
| B-06 | Halaman konfirmasi password dapat dirender | Login | Buka `GET /confirm-password` | — | HTTP 200 | ✅ |
| B-07 | Password dapat dikonfirmasi | Login | Isi form konfirmasi password dengan password yang benar | Password: `password` | HTTP 302, akses diberikan | ✅ |
| B-08 | Konfirmasi password gagal dengan password salah | Login | Isi form konfirmasi password dengan password salah | Password: `salah123` | Muncul pesan error validasi | ✅ |
| B-09 | Halaman lupa password dapat dirender | Tidak login | Buka `GET /forgot-password` | — | HTTP 200 | ✅ |
| B-10 | Link reset password dapat diminta | Tidak login | Buka `/forgot-password`, isi email, klik **Kirim Link Reset** | Email: akun terdaftar | HTTP 200, link reset dikirim ke email | ✅ |
| B-11 | Halaman reset password dapat dirender | Punya token reset valid | Buka `GET /reset-password/{token}` | — | HTTP 200 | ✅ |
| B-12 | Password dapat direset dengan token valid | Punya token reset valid | Isi form reset password, klik **Reset Password** | Email: terdaftar, Password: `NewPass123!`, Konfirmasi: sama | Password berhasil direset, redirect ke login | ✅ |
| B-13 | Halaman ubah password dapat dirender | Login | Buka `GET /profile` (bagian ubah password) | — | HTTP 200 | ✅ |
| B-14 | Password dapat diperbarui | Login | Isi form ubah password, klik **Simpan** | current_password: `password`, password: `NewPass123!`, konfirmasi: sama | Password berhasil diperbarui | ✅ |
| B-15 | Password benar wajib diisi untuk ubah password | Login | Isi password lama dengan nilai salah | current_password: `salah`, password: `New123!`, konfirmasi: sama | Muncul error validasi pada `current_password` | ✅ |

---

## Modul 3 — Profil Pengguna

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| P-01 | Halaman profil dapat ditampilkan | Login sebagai user mana saja | Buka `GET /profile` | — | HTTP 200 | ✅ |
| P-02 | Informasi profil dapat diperbarui | Login | Buka `/profile`, ubah nama, klik **Simpan** | name: `Nama Baru`, email: email terdaftar | Data `name` diperbarui di DB | ✅ |
| P-03 | Email unik diperlukan saat update profil | Login | Ubah email ke email milik user lain | email: email milik user lain | Muncul error validasi pada field `email` | ✅ |
| P-04 | User dapat menghapus akun sendiri | Login | Klik **Hapus Akun**, masukkan password konfirmasi | password: `password` | Akun terhapus, sesi berakhir, redirect ke halaman utama | ✅ |
| P-05 | Password benar diperlukan untuk hapus akun | Login | Klik **Hapus Akun**, masukkan password salah | password: `salah123` | Muncul error validasi, akun tidak terhapus | ✅ |

---

## Modul 4 — Manajemen Pengguna

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| U-01 | Super Admin dapat melihat daftar pengguna | Login sebagai Super Admin | Buka `GET /users` | — | HTTP 200, tabel daftar user tampil | ✅ |
| U-02 | Admin dapat melihat daftar pengguna | Login sebagai Admin | Buka `GET /users` | — | HTTP 200 | ✅ |
| U-03 | Kepala Kandang tidak dapat akses manajemen user | Login sebagai Kepala Kandang | Buka `GET /users` | — | HTTP 403 Forbidden | ✅ |
| U-04 | Super Admin membuat pengguna baru | Login sebagai Super Admin | Klik **Tambah Pengguna**, isi form, klik **Simpan** | Nama: `Pengguna Baru`, Email: email unik, Password: `Password123`, Role: `admin`, Status: `aktif` | Redirect, user baru tersimpan di tabel `user` | ✅ |
| U-05 | Validasi form tambah user — field kosong | Login sebagai Super Admin | Klik **Tambah Pengguna**, submit form kosong | — (semua field kosong) | Muncul validasi pada: nama, email, password, role, status | ✅ |
| U-06 | Validasi — email duplikat tidak dapat digunakan | Login sebagai Super Admin | Isi form dengan email yang sudah terdaftar | Email: email milik user yang sudah ada | Muncul error validasi: `email sudah digunakan` | ✅ |
| U-07 | Admin biasa tidak dapat membuat akun Super Admin | Login sebagai Admin | Isi form tambah user dengan role `super_admin`, klik **Simpan** | Role: `super_admin` | HTTP 403 Forbidden | ✅ |
| U-08 | Super Admin dapat memperbarui data pengguna | Login sebagai Super Admin | Klik **Edit** pada user, ubah nama, klik **Simpan** | Nama: `Nama Diperbarui`, Role: `admin`, Status: `aktif` | Redirect, nama user berubah di DB | ✅ |
| U-09 | User tidak dapat menonaktifkan akun sendiri | Login sebagai Super Admin | Edit profil sendiri, ubah Status menjadi `nonaktif` | Status: `nonaktif` | Muncul error validasi pada field `status` | ✅ |
| U-10 | Super Admin dapat toggle status aktif/nonaktif user lain | Login sebagai Super Admin | Klik ikon toggle status pada user admin | — | HTTP 200 JSON, status berubah menjadi `nonaktif` | ✅ |
| U-11 | User tidak dapat toggle status akun sendiri | Login sebagai Super Admin | Klik toggle status pada akun sendiri | — | Muncul error pada session key `toggle` | ✅ |
| U-12 | Admin tidak dapat toggle status Super Admin | Login sebagai Admin | Klik toggle status pada akun Super Admin | — | HTTP 403 Forbidden | ✅ |
| U-13 | Super Admin dapat menghapus pengguna | Login sebagai Super Admin | Klik **Hapus** pada user admin, konfirmasi | — | Redirect, user terhapus dari tabel `user` | ✅ |
| U-14 | Admin tidak dapat menghapus pengguna | Login sebagai Admin | Klik **Hapus** pada user mana saja | — | HTTP 403 Forbidden | ✅ |
| U-15 | Super Admin tidak dapat menghapus akun sendiri | Login sebagai Super Admin | Klik **Hapus** pada akun sendiri, konfirmasi | — | Muncul error pada session key `delete` | ✅ |
| U-16 | Super Admin dapat mereset password pengguna | Login sebagai Super Admin | Klik **Reset Password** pada user admin, isi form | Password: `NewPassword123`, Konfirmasi: `NewPassword123` | Redirect, password berhasil diperbarui | ✅ |
| U-17 | Admin tidak dapat mereset password Super Admin | Login sebagai Admin | Klik **Reset Password** pada akun Super Admin | Password: `NewPassword123`, Konfirmasi: `NewPassword123` | HTTP 403 Forbidden | ✅ |

---

## Modul 5 — Hak Akses Berbasis Peran (RBAC)

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| R-01 | Super Admin akses `/dashboard` | Login sebagai Super Admin | Buka `GET /dashboard` | — | HTTP 200 | ✅ |
| R-02 | Admin akses `/dashboard` | Login sebagai Admin | Buka `GET /dashboard` | — | HTTP 200 | ✅ |
| R-03 | Kepala Kandang akses `/dashboard` | Login sebagai Kepala Kandang | Buka `GET /dashboard` | — | Redirect ke `/kk/dashboard` | ✅ |
| R-04 | Pengurus Kandang akses `/dashboard` | Login sebagai Pengurus Kandang | Buka `GET /dashboard` | — | Redirect ke `/pk/dashboard` | ✅ |
| R-05 | Pengurus Kandang tidak dapat akses halaman web admin | Login sebagai Pengurus Kandang | Buka `GET /kandang` atau halaman admin lainnya | — | Redirect ke `/tugas-harian/mobile` | ✅ |
| R-06 | Kepala Kandang tidak dapat akses rute mobile PK | Login sebagai Kepala Kandang | Buka `GET /pk/dashboard` | — | HTTP 403 Forbidden | ✅ |
| R-07 | Akun nonaktif tidak dapat login dan tersesi | Tidak login | Login dengan akun berstatus `nonaktif` | Email & password valid | Gagal login, tidak tersesi | ✅ |
| R-08 | Tamu tidak dapat akses halaman terproteksi | Tidak login | Buka `GET /dashboard` atau halaman lain | — | Redirect ke `/login` | ✅ |

---

## Modul 6 — Data Domba

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| D-01 | Admin melihat daftar domba | Login sebagai Admin | Buka `GET /domba` | — | HTTP 200, tabel daftar domba tampil | ✅ |
| D-02 | Tamu tidak dapat akses daftar domba | Tidak login | Buka `GET /domba` | — | Redirect ke `/login` | ✅ |
| D-03 | Filter daftar domba berdasarkan kategori | Login sebagai Admin | Buka `/domba?kategori=cempe` | Query: `kategori=cempe` | HTTP 200, hanya tampil domba berkategori cempe | ✅ |
| D-04 | Filter daftar domba berdasarkan status | Login sebagai Admin | Buka `/domba?status=aktif` | Query: `status=aktif` | HTTP 200, hanya tampil domba berstatus aktif | ✅ |
| D-05 | Cari domba dengan keyword | Login sebagai Admin | Isi kotak pencarian, tekan Enter | Query: `search=garut` | HTTP 200, hasil pencarian tampil | ✅ |
| D-06 | Lihat detail domba (JSON) | Login sebagai Admin | Klik ikon detail / `GET /domba/{ear_tag_id}` via AJAX | ear_tag_id valid | HTTP 200 JSON, `success: true`, data domba lengkap | ✅ |
| D-07 | Detail domba — ID tidak ada → 404 | Login sebagai Admin | `GET /domba/X-999` via JSON | ear_tag_id: `X-999` | HTTP 404 | ✅ |
| D-08 | Tambah domba baru | Login sebagai Admin | Klik **Tambah Domba**, isi form, klik **Simpan** | Jenis Kelamin: `jantan`, Ras: `Merino`, Kategori: `pejantan`, Kandang: kandang valid | HTTP 201 JSON `success: true`, domba tersimpan di DB, ear_tag_id auto-generate | ✅ |
| D-09 | Tambah domba dengan berat awal | Login sebagai Admin | Isi form tambah domba dengan berat awal | Jenis Kelamin: `jantan`, Ras: `Garut`, Kategori: `cempe`, Kandang: valid, Berat Awal: `25.5`, Tgl Timbang: hari ini | HTTP 201, data penimbangan awal tersimpan di tabel `penimbangan` | ✅ |
| D-10 | Ear tag di-generate otomatis | Login sebagai Admin | Isi form tanpa ear_tag_id (kosong) | Jenis Kelamin: `betina`, Ras: `Dorper`, Kategori: `indukan`, Kandang: valid | Ear tag otomatis diisi dengan pola `B-XXX` | ✅ |
| D-11 | Generate ear tag — API jantan | Login sebagai Admin | `GET /domba/generate-ear-tag?jenis_kelamin=jantan` | jenis_kelamin: `jantan` | HTTP 200 JSON, ear_tag diawali `J-` | ✅ |
| D-12 | Generate ear tag — API betina | Login sebagai Admin | `GET /domba/generate-ear-tag?jenis_kelamin=betina` | jenis_kelamin: `betina` | HTTP 200 JSON, ear_tag diawali `B-` | ✅ |
| D-13 | Generate ear tag — jenis kelamin tidak valid | Login sebagai Admin | `GET /domba/generate-ear-tag?jenis_kelamin=invalid` | jenis_kelamin: `invalid` | HTTP 422 Unprocessable | ✅ |
| D-14 | Validasi tambah domba — field wajib kosong | Login sebagai Admin | Submit form tambah domba kosong | — (semua kosong) | HTTP 422, error pada: jenis_kelamin, ras, kategori, kandang_id | ✅ |
| D-15 | Validasi — kandang_id tidak valid | Login sebagai Admin | Isi form dengan kandang_id yang tidak ada | kandang_id: `99999` | HTTP 422, error pada `kandang_id` | ✅ |
| D-16 | Validasi — jenis_kelamin harus valid | Login sebagai Admin | Isi form dengan jenis kelamin salah | jenis_kelamin: `tidak_valid` | HTTP 422, error pada `jenis_kelamin` | ✅ |
| D-17 | Validasi — kategori harus valid | Login sebagai Admin | Isi form dengan kategori salah | kategori: `salah` | HTTP 422, error pada `kategori` | ✅ |
| D-18 | Perbarui data domba | Login sebagai Admin | Klik **Edit** pada domba, ubah ras dan kategori, klik **Simpan** | Ras: `Suffolk`, Kategori: `indukan`, Status: `aktif`, Kandang: valid | HTTP 200 JSON `success: true`, data domba diperbarui di DB | ✅ |
| D-19 | Ear tag tidak dapat diubah saat update | Login sebagai Admin | Edit domba, coba kirim ear_tag_id berbeda | ear_tag_id: nilai berbeda | ear_tag_id di DB tetap sama (tidak berubah) | ✅ |
| D-20 | Super Admin dapat menghapus domba (soft delete) | Login sebagai Super Admin | Klik **Hapus** pada domba, konfirmasi | — | HTTP 200 JSON `success: true`, kolom `deleted_at` terisi | ✅ |
| D-21 | Admin biasa tidak dapat menghapus domba | Login sebagai Admin | Klik **Hapus** pada domba | — | HTTP 403 JSON `success: false` | ✅ |

---

## Modul 7 — Kandang

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| K-01 | Admin melihat daftar kandang | Login sebagai Admin | Buka `GET /kandang` | — | HTTP 200 | ✅ |
| K-02 | Tamu tidak dapat akses halaman kandang | Tidak login | Buka `GET /kandang` | — | Redirect ke `/login` | ✅ |
| K-03 | Pengurus Kandang tidak dapat akses manajemen kandang | Login sebagai Pengurus Kandang | Buka `GET /kandang` | — | Redirect ke `/tugas-harian/mobile` | ✅ |
| K-04 | Admin menambah kandang baru | Login sebagai Admin | Klik **Tambah Kandang**, isi form, klik **Simpan** | Nama Kandang: `Kandang A`, Tipe: `utama`, Kapasitas: `50` | HTTP 201 JSON `success: true`, kandang tersimpan di DB | ✅ |
| K-05 | Validasi — nama kandang wajib diisi | Login sebagai Admin | Submit form kandang kosong | — (semua kosong) | HTTP 422, error pada: nama_kandang, tipe, kapasitas | ✅ |
| K-06 | Validasi — nama kandang harus unik | Login sebagai Admin | Isi form dengan nama kandang yang sudah ada | nama_kandang: nama yang sudah terdaftar | HTTP 422, error: `nama_kandang sudah digunakan` | ✅ |
| K-07 | Validasi — tipe kandang harus valid | Login sebagai Admin | Isi form dengan tipe yang tidak dikenal | Tipe: `invalid_tipe` | HTTP 422, error pada `tipe` | ✅ |
| K-08 | Validasi — kapasitas minimal 1 | Login sebagai Admin | Isi kapasitas dengan 0 | Kapasitas: `0` | HTTP 422, error pada `kapasitas` | ✅ |
| K-09 | Lihat detail kandang | Login sebagai Admin | `GET /kandang/{id}` via JSON | id kandang valid | HTTP 200 JSON, data kandang lengkap | ✅ |
| K-10 | Detail kandang — ID tidak ada → 404 | Login sebagai Admin | `GET /kandang/99999` via JSON | id: `99999` | HTTP 404 | ✅ |
| K-11 | Admin memperbarui kandang | Login sebagai Admin | Klik **Edit** pada kandang, ubah tipe dan kapasitas, klik **Simpan** | Nama: sama, Tipe: `isolasi`, Kapasitas: `60` | HTTP 200 JSON `success: true` | ✅ |
| K-12 | Validasi update — kapasitas tidak boleh lebih kecil dari jumlah domba aktif | Login sebagai Admin | Edit kandang yang memiliki 5 domba aktif, set kapasitas ke 3 | Kapasitas: `3` | HTTP 422 JSON `success: false` | ✅ |
| K-13 | Update — nama yang sama boleh dipakai pada kandang yang sama | Login sebagai Admin | Edit kandang, pertahankan nama yang sama | nama_kandang: nama yang sudah ada (milik kandang ini) | HTTP 200 JSON `success: true` | ✅ |
| K-14 | Admin menghapus kandang kosong | Login sebagai Admin | Klik **Hapus** pada kandang tanpa domba, konfirmasi | — | HTTP 200 JSON `success: true`, kandang terhapus dari DB | ✅ |
| K-15 | Kandang berisi domba aktif tidak dapat dihapus | Login sebagai Admin | Klik **Hapus** pada kandang yang berisi domba | — | HTTP 422 JSON `success: false` | ✅ |
| K-16 | Kepala Kandang tidak dapat menghapus kandang | Login sebagai Kepala Kandang | Klik **Hapus** pada kandang | — | HTTP 403 Forbidden | ✅ |

---

## Modul 8 — Tugas Harian

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| TH-01 | Admin melihat daftar tugas harian | Login sebagai Admin | Buka `GET /tugas-harian` | — | HTTP 200 | ✅ |
| TH-02 | Filter tugas berdasarkan tanggal | Login sebagai Admin | Buka `/tugas-harian?tanggal=2026-06-01` | tanggal: `2026-06-01` | HTTP 200, tugas tanggal 2026-06-01 tampil | ✅ |
| TH-03 | Tamu tidak dapat akses tugas harian | Tidak login | Buka `GET /tugas-harian` | — | Redirect ke `/login` | ✅ |
| TH-04 | Admin menambah tugas baru | Login sebagai Admin | Klik **Tambah Tugas**, isi form, klik **Simpan** | Judul: `Tugas Baru`, Kandang: valid, Tanggal: hari ini, Tipe: `rutin`, Prioritas: `sedang` | HTTP 200 JSON `success: true`, tugas tersimpan dengan `status: belum` | ✅ |
| TH-05 | Status tugas baru otomatis diset `belum` | Login sebagai Admin | Tambah tugas baru | Judul: `Tugas Auto Status`, Tipe: `kondisional`, Prioritas: `tinggi` | Kolom `status` di DB berisi `belum` | ✅ |
| TH-06 | Validasi — field wajib kosong | Login sebagai Admin | Submit form tambah tugas kosong | — | HTTP 422, error pada: judul, kandang_id, tanggal, tipe, prioritas | ✅ |
| TH-07 | Validasi — kandang_id tidak valid | Login sebagai Admin | Isi kandang_id dengan ID yang tidak ada | kandang_id: `99999` | HTTP 422, error pada `kandang_id` | ✅ |
| TH-08 | Validasi — tipe harus valid | Login sebagai Admin | Isi tipe dengan nilai salah | tipe: `tidak_valid` | HTTP 422, error pada `tipe` | ✅ |
| TH-09 | Validasi — prioritas harus valid | Login sebagai Admin | Isi prioritas dengan nilai salah | prioritas: `super_penting` | HTTP 422, error pada `prioritas` | ✅ |
| TH-10 | Lihat detail tugas | Login sebagai Admin | `GET /tugas-harian/{id}` via JSON | id tugas valid | HTTP 200 JSON berisi data tugas | ✅ |
| TH-11 | Detail tugas — ID tidak ada → 404 | Login sebagai Admin | `GET /tugas-harian/99999` via JSON | id: `99999` | HTTP 404 | ✅ |
| TH-12 | Admin memperbarui tugas | Login sebagai Admin | Klik **Edit**, ubah judul dan prioritas, klik **Simpan** | Judul: `Judul Updated`, Tipe: `kondisional`, Prioritas: `tinggi` | HTTP 200 JSON `success: true`, judul berubah di DB | ✅ |
| TH-13 | Update status tugas → `dalam_proses` | Login sebagai Admin | Klik **Mulai** pada tugas berstatus `belum` | status: `dalam_proses` | HTTP 200, `waktu_mulai` terisi otomatis di DB | ✅ |
| TH-14 | Update status tugas → `selesai` | Login sebagai Admin | Klik **Selesai** pada tugas berstatus `dalam_proses` | status: `selesai` | HTTP 200, `waktu_selesai` terisi otomatis di DB | ✅ |
| TH-15 | Update status tugas → `dilewati` | Login sebagai Admin | Klik **Lewati** pada tugas | status: `dilewati` | HTTP 200 JSON, `status: dilewati` | ✅ |
| TH-16 | Validasi update status — nilai tidak dikenal | Login sebagai Admin | Kirim PATCH dengan status tidak valid | status: `status_aneh` | HTTP 422, error pada `status` | ✅ |
| TH-17 | Admin menghapus tugas | Login sebagai Admin | Klik **Hapus** pada tugas, konfirmasi | — | HTTP 200 JSON `success: true`, tugas terhapus dari DB | ✅ |
| TH-18 | Admin dapat bulk-assign tugas ke petugas | Login sebagai Admin | Pilih beberapa tugas, pilih petugas, klik **Assign** | ids: [id1, id2], user_id: id pengurus kandang | HTTP 200 JSON `success: true`, `count: 2`, tugas-tugas diperbarui | ✅ |
| TH-19 | Validasi bulk-assign — ids dan user_id wajib | Login sebagai Admin | Submit bulk-assign tanpa data | — | HTTP 422, error pada: ids, user_id | ✅ |
| TH-20 | Validasi bulk-assign — user_id harus valid | Login sebagai Admin | Bulk-assign ke user yang tidak ada | user_id: `99999` | HTTP 422, error pada `user_id` | ✅ |
| TH-21 | Generate tugas dari template aktif | Login sebagai Admin | Klik **Generate Tugas Rutin**, pilih tanggal, klik **Generate** | tanggal: `2026-12-01` | HTTP 200 JSON `success: true`, tugas-tugas baru dibuat dari template aktif | ✅ |
| TH-22 | Generate rutin tidak membuat duplikat | Login sebagai Admin | Klik **Generate** dua kali untuk tanggal yang sama | tanggal: `2026-11-15` | Pada generate kedua, `generated: 0`, `skipped: N` | ✅ |
| TH-23 | Template tidak aktif tidak di-generate | Login sebagai Admin | Generate tugas ketika hanya ada template nonaktif | tanggal valid | HTTP 200, tidak ada error | ✅ |
| TH-24 | Validasi generate rutin — tanggal wajib | Login sebagai Admin | Submit generate tanpa tanggal | — | HTTP 422, error pada `tanggal` | ✅ |
| TH-25 | Kepala Kandang tidak dapat generate rutin | Login sebagai Kepala Kandang | Kirim POST ke `/tugas-harian/generate-rutin` | tanggal: hari ini | HTTP 403 Forbidden | ✅ |

---

## Modul 9 — Template Tugas Rutin

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| TT-01 | Admin melihat daftar template | Login sebagai Admin | Buka `GET /template-tugas` | — | HTTP 200 | ✅ |
| TT-02 | Tamu tidak dapat akses halaman template | Tidak login | Buka `GET /template-tugas` | — | Redirect ke `/login` | ✅ |
| TT-03 | Kepala Kandang tidak dapat akses manajemen template | Login sebagai Kepala Kandang | Buka `GET /template-tugas` | — | HTTP 403 Forbidden | ✅ |
| TT-04 | Admin membuat template baru | Login sebagai Admin | Klik **Tambah Template**, isi form, klik **Simpan** | Judul: `Template Makan Pagi`, Kandang: valid, Prioritas: `sedang` | HTTP 200 JSON `success: true`, template tersimpan di DB dengan `is_active: true` | ✅ |
| TT-05 | Template baru otomatis berstatus aktif | Login sebagai Admin | Buat template baru | Judul: `Template Default Active`, Prioritas: `rendah` | Kolom `is_active` di DB berisi `true` | ✅ |
| TT-06 | Validasi — field wajib kosong | Login sebagai Admin | Submit form template kosong | — | HTTP 422, error pada: judul, kandang_id, prioritas | ✅ |
| TT-07 | Validasi — kandang_id tidak valid | Login sebagai Admin | Isi form dengan kandang tidak ada | kandang_id: `99999` | HTTP 422, error pada `kandang_id` | ✅ |
| TT-08 | Validasi — format waktu harus `HH:MM` | Login sebagai Admin | Isi waktu_default dengan format salah | waktu_default: `bukan-waktu` | HTTP 422, error pada `waktu_default` | ✅ |
| TT-09 | Admin memperbarui template | Login sebagai Admin | Klik **Edit** pada template, ubah judul, klik **Simpan** | Judul: `Judul Template Updated`, Prioritas: `tinggi` | HTTP 200 JSON `success: true`, judul berubah di DB | ✅ |
| TT-10 | Update — template tidak ada → 404 | Login sebagai Admin | Kirim PUT ke `/template-tugas/99999` | Judul: `Test` | HTTP 404 | ✅ |
| TT-11 | Admin menonaktifkan template (toggle off) | Login sebagai Admin | Klik tombol toggle aktif/nonaktif pada template aktif | — | HTTP 200 JSON `data.is_active: false` | ✅ |
| TT-12 | Admin mengaktifkan kembali template (toggle on) | Login sebagai Admin | Klik tombol toggle pada template nonaktif | — | HTTP 200 JSON `data.is_active: true` | ✅ |
| TT-13 | Admin menghapus template | Login sebagai Admin | Klik **Hapus** pada template, konfirmasi | — | HTTP 200 JSON `success: true`, template terhapus dari DB | ✅ |
| TT-14 | Hapus — template tidak ada → 404 | Login sebagai Admin | Kirim DELETE ke `/template-tugas/99999` | — | HTTP 404 | ✅ |

---

## Modul 10 — Stok Pakan

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| SP-01 | Admin melihat halaman stok pakan | Login sebagai Admin | Buka `GET /stok-pakan` | — | HTTP 200, daftar stok tampil | ✅ |
| SP-02 | Tamu tidak dapat akses stok pakan | Tidak login | Buka `GET /stok-pakan` | — | Redirect ke `/login` | ✅ |
| SP-03 | Pengurus Kandang tidak dapat akses stok pakan | Login sebagai Pengurus Kandang | Buka `GET /stok-pakan` | — | Redirect ke `/tugas-harian/mobile` | ✅ |
| SP-04 | Admin mencatat stok pakan masuk | Login sebagai Admin | Klik **Catat Masuk**, isi form, klik **Simpan** | Jenis Pakan: pilih stok valid, Jumlah: `50`, Keterangan: `Pembelian dari supplier` | Redirect ke `/stok-pakan`, `jumlah_stok` bertambah 50 di DB | ✅ |
| SP-05 | Validasi masuk — pakan_id tidak valid | Login sebagai Admin | Isi pakan_id dengan ID yang tidak ada | pakan_id: `99999` | Muncul error validasi pada `pakan_id` | ✅ |
| SP-06 | Validasi masuk — jumlah tidak boleh nol | Login sebagai Admin | Isi jumlah masuk dengan 0 | jumlah: `0` | Muncul error validasi pada `jumlah` | ✅ |
| SP-07 | Admin mencatat pemberian pakan (keluar) | Login sebagai Admin | Klik **Catat Keluar**, isi form, klik **Simpan** | Pakan: pilih stok valid (100 kg), Domba: ear_tag valid, Jumlah: `5`, Tanggal: hari ini | Redirect ke `/stok-pakan`, `jumlah_stok` berkurang 5, riwayat tersimpan di `pemberian_pakan` | ✅ |
| SP-08 | Catat keluar gagal — jumlah melebihi stok | Login sebagai Admin | Isi jumlah lebih besar dari stok tersedia | Jumlah: `999` (stok hanya 100 kg) | Redirect back dengan pesan error pada field `jumlah` | ✅ |
| SP-09 | Catat keluar gagal — ear_tag_id tidak valid | Login sebagai Admin | Isi ear_tag_id dengan kode yang tidak terdaftar | ear_tag_id: `X-INVALID` | Muncul error validasi pada `ear_tag_id` | ✅ |
| SP-10 | Pemberian pakan tercatat di tabel `pemberian_pakan` | Login sebagai Admin | Catat keluar berhasil | Pakan: valid, Domba: valid, Jumlah: `3` | Row baru muncul di tabel `pemberian_pakan` dengan pakan_id dan ear_tag_id yang sesuai | ✅ |

---

## Modul 11 — Pakan Individual

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| PI-01 | Admin melihat halaman pakan individual | Login sebagai Admin | Buka `GET /pakan-individual` | — | HTTP 200 | ✅ |
| PI-02 | Tamu tidak dapat akses pakan individual | Tidak login | Buka `GET /pakan-individual` | — | Redirect ke `/login` | ✅ |
| PI-03 | Filter pakan individual berdasarkan kategori | Login sebagai Admin | Buka `/pakan-individual?kategori=indukan` | kategori: `indukan` | HTTP 200, data terfilter | ✅ |
| PI-04 | Admin mencatat pemberian pakan individual | Login sebagai Admin | Pilih domba, pilih pakan, isi jumlah, klik **Simpan** (AJAX) | ear_tag_id: valid, pakan_id: valid (stok 200 kg), Tanggal: hari ini, Sesi: `pagi`, Jumlah Gram: `500` | HTTP 200 JSON `success: true`, data tersimpan di `pemberian_pakan` | ✅ |
| PI-05 | Store gagal — stok tidak cukup | Login sebagai Admin | Isi jumlah gram sangat besar melebihi stok | Jumlah Gram: `99999999` (melebihi stok) | HTTP 422 JSON `success: false` | ✅ |
| PI-06 | Validasi — field wajib kosong | Login sebagai Admin | Submit form pakan individual tanpa data | — (semua kosong) | HTTP 422, error pada: ear_tag_id, pakan_id, tanggal_pemberian, sesi, jumlah_gram | ✅ |
| PI-07 | Validasi — sesi harus valid | Login sebagai Admin | Isi sesi dengan nilai yang tidak dikenal | sesi: `malam` | HTTP 422, error pada `sesi` | ✅ |
| PI-08 | Search domba — query kosong mengembalikan array kosong | Login sebagai Admin | Ketik di kolom pencarian domba tanpa isi (`q=`) | q: `""` | HTTP 200 JSON `[]` (array kosong) | ✅ |
| PI-09 | Search domba — query menghasilkan daftar domba | Login sebagai Admin | Ketik 2 karakter awal ear_tag di kolom pencarian | q: 2 karakter pertama ear_tag | HTTP 200 JSON array berisi domba yang sesuai | ✅ |
| PI-10 | Admin melihat statistik FCR domba | Login sebagai Admin | Klik ikon statistik pada domba / `GET /pakan-individual/{ear_tag}/stats` | ear_tag_id valid | HTTP 200 JSON berisi: `fcr`, `total_pakan_30hr`, `rata_pakan_harian_gr`, `kenaikan_bb` | ✅ |

---

## Modul 12 — Obat & Vaksin

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| OV-01 | Admin melihat daftar obat/vaksin | Login sebagai Admin | Buka `GET /obat-vaksin` | — | HTTP 200 | ✅ |
| OV-02 | Tamu tidak dapat akses halaman obat | Tidak login | Buka `GET /obat-vaksin` | — | Redirect ke `/login` | ✅ |
| OV-03 | Lihat detail obat sebagai JSON | Login sebagai Admin | Klik detail / `GET /obat-vaksin/{id}` via JSON | id obat valid | HTTP 200 JSON berisi data obat lengkap | ✅ |
| OV-04 | Admin menambah obat/vaksin baru | Login sebagai Admin | Klik **Tambah Obat**, isi form, klik **Simpan** | Nama Obat: `Obat Uji`, Tipe: `obat`, Satuan: `ml`, Stok: `100`, Stok Minimum: `10` | Redirect, obat tersimpan di tabel `obat_vaksin` | ✅ |
| OV-05 | Validasi — field wajib kosong | Login sebagai Admin | Submit form tambah obat kosong | — | Muncul error pada: nama_obat, tipe, satuan, stok, stok_minimum | ✅ |
| OV-06 | Validasi — nama obat harus unik | Login sebagai Admin | Isi form dengan nama obat yang sudah ada | nama_obat: nama yang sudah terdaftar | Muncul error validasi `nama_obat sudah digunakan` | ✅ |
| OV-07 | Admin memperbarui data obat | Login sebagai Admin | Klik **Edit** pada obat, ubah stok minimum, klik **Simpan** | Stok Minimum: `20` | Redirect, data obat diperbarui di DB | ✅ |
| OV-08 | Admin menghapus obat yang tidak digunakan | Login sebagai Admin | Klik **Hapus** pada obat yang belum pernah dipakai | — | Redirect, obat terhapus dari DB | ✅ |
| OV-09 | Obat yang sedang digunakan tidak dapat dihapus | Login sebagai Admin | Klik **Hapus** pada obat yang punya riwayat pemakaian | — | Redirect back dengan pesan error | ✅ |
| OV-10 | Cari rekam medis untuk obat (AJAX) | Login sebagai Admin | Klik **Tambah Pemakaian Obat**, ketik ear_tag di search | Query: `GET /obat-vaksin/search-rekam?q=...` | HTTP 200 JSON array rekam medis yang sesuai | ✅ |
| OV-11 | Search rekam medis mengembalikan array | Login sebagai Admin | Kirim request search dengan query | q: valid | HTTP 200, response berupa JSON array | ✅ |

---

## Modul 13 — Kesehatan (Rekam Medis & Vaksinasi)

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| KS-01 | Admin melihat halaman kesehatan | Login sebagai Admin | Buka `GET /kesehatan` | — | HTTP 200 | ✅ |
| KS-02 | Tamu tidak dapat akses halaman kesehatan | Tidak login | Buka `GET /kesehatan` | — | Redirect ke `/login` | ✅ |
| KS-03 | Admin mencatat rekam medis baru (domba sakit) | Login sebagai Admin | Klik **Tambah Rekam Medis**, isi form, klik **Simpan** | Ear Tag: domba valid, Tanggal Sakit: hari ini, Status: `sakit`, Gejala: `Demam tinggi dan nafsu makan turun` | Redirect, rekam medis tersimpan di tabel `medical_record` | ✅ |
| KS-04 | Validasi rekam medis — field wajib kosong | Login sebagai Admin | Submit form rekam medis kosong | — | Muncul error pada: ear_tag_id, tanggal_sakit, status, gejala | ✅ |
| KS-05 | Validasi rekam medis — status tidak valid | Login sebagai Admin | Isi status dengan nilai yang tidak dikenal | status: `tidak_valid` | Muncul error validasi pada `status` | ✅ |
| KS-06 | Tandai karantina sekaligus pindah kandang | Login sebagai Admin | Isi form rekam medis, centang **Tandai Karantina**, pilih kandang isolasi | Status: `dalam_perawatan`, Tandai Karantina: `ya`, Kandang Karantina: kandang isolasi | Status domba di tabel `domba` berubah menjadi `karantina` | ✅ |
| KS-07 | Admin memperbarui rekam medis | Login sebagai Admin | Klik **Edit** pada rekam medis, ubah status menjadi `sembuh` | Status: `sembuh`, Tanggal Sembuh: hari ini | Redirect, kolom `status` berubah menjadi `sembuh` di DB | ✅ |
| KS-08 | Update status `sembuh` → domba karantina kembali aktif | Login sebagai Admin | Update rekam medis domba yang berstatus `karantina` menjadi `sembuh` | Status: `sembuh` | Status domba di tabel `domba` berubah kembali menjadi `aktif` | ✅ |
| KS-09 | Admin menghapus rekam medis | Login sebagai Admin | Klik **Hapus** pada rekam medis, konfirmasi | — | Redirect, rekam medis terhapus dari `medical_record` | ✅ |
| KS-10 | Admin mencatat vaksinasi baru | Login sebagai Admin | Klik **Tambah Vaksinasi**, pilih domba dan vaksin, klik **Simpan** | Ear Tag: domba valid, Obat (Vaksin): vaksin valid, Tanggal Vaksinasi: hari ini | Redirect, data tersimpan di tabel `vaksinasi` | ✅ |
| KS-11 | Validasi vaksinasi — field wajib kosong | Login sebagai Admin | Submit form vaksinasi kosong | — | Muncul error pada: ear_tag_id, obat_id, tanggal_vaksinasi | ✅ |
| KS-12 | Admin menghapus catatan vaksinasi | Login sebagai Admin | Klik **Hapus** pada data vaksinasi, konfirmasi | — | Redirect, data vaksinasi terhapus dari `vaksinasi` | ✅ |
| KS-13 | Admin memindahkan domba karantina ke kandang lain | Login sebagai Admin | Klik **Pindah Kandang** pada domba karantina, pilih kandang tujuan | Kandang: kandang utama baru | Redirect, `kandang_id` domba berubah di DB | ✅ |

---

## Modul 14 — Reproduksi (Perkawinan & Kelahiran)

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| RP-01 | Admin melihat halaman reproduksi | Login sebagai Admin | Buka `GET /reproduksi` | — | HTTP 200 | ✅ |
| RP-02 | Tamu tidak dapat akses halaman reproduksi | Tidak login | Buka `GET /reproduksi` | — | Redirect ke `/login` | ✅ |
| RP-03 | Admin mencatat perkawinan baru | Login sebagai Admin | Klik **Tambah Perkawinan**, isi form, klik **Simpan** | Pejantan: domba jantan valid, Indukan: [domba betina valid], Tanggal Kawin: hari ini, Metode: `alami` | Redirect, perkawinan tersimpan dengan `status: menunggu_konfirmasi` | ✅ |
| RP-04 | HPL otomatis dihitung 150 hari | Login sebagai Admin | Catat perkawinan dengan tanggal hari ini | Tanggal Kawin: hari ini, Metode: `inseminasi_buatan` | Kolom `estimasi_lahir` di DB = hari ini + 150 hari | ✅ |
| RP-05 | Validasi perkawinan — field wajib kosong | Login sebagai Admin | Submit form perkawinan kosong | — | Muncul error pada: pejantan_id, indukan_ids, tanggal_perkawinan, metode | ✅ |
| RP-06 | Validasi perkawinan — metode tidak valid | Login sebagai Admin | Isi metode dengan nilai yang tidak dikenal | metode: `metode_tidak_valid` | Muncul error validasi pada `metode` | ✅ |
| RP-07 | Admin memperbarui data perkawinan | Login sebagai Admin | Klik **Edit** pada perkawinan, ubah metode, klik **Simpan** | metode: `inseminasi_buatan` | Redirect, kolom `metode` berubah di DB | ✅ |
| RP-08 | Admin menghapus perkawinan tanpa kelahiran | Login sebagai Admin | Klik **Hapus** pada perkawinan yang belum ada kelahiran | — | Redirect, perkawinan terhapus dari DB | ✅ |
| RP-09 | Perkawinan dengan kelahiran tidak dapat dihapus | Login sebagai Admin | Klik **Hapus** pada perkawinan yang sudah punya data kelahiran | — | Redirect (batal hapus), data perkawinan tetap ada di DB | ✅ |
| RP-10 | Admin konfirmasi kebuntingan | Login sebagai Admin | Klik **Konfirmasi Kebuntingan**, isi form, klik **Simpan** | Hasil: `bunting`, Metode Konfirmasi: `USG`, Tgl Konfirmasi: hari ini | Redirect, `status` perkawinan berubah menjadi `bunting` | ✅ |
| RP-11 | Konfirmasi gagal — status bukan `menunggu_konfirmasi` | Login sebagai Admin | Coba konfirmasi pada perkawinan yang sudah berstatus `bunting` | Hasil: `bunting`, Metode: `USG` | HTTP 422 Unprocessable | ✅ |
| RP-12 | Admin mencatat kelahiran | Login sebagai Admin | Klik **Catat Kelahiran** pada perkawinan berstatus `bunting`, isi form | Tanggal Lahir: hari ini, Jml Anak Hidup: `2`, Jml Anak Mati: `0`, Bobot Rata-rata: `3.5` | Redirect, data tersimpan di `kelahiran`, status perkawinan berubah menjadi `lahir` | ✅ |
| RP-13 | Validasi kelahiran — field wajib kosong | Login sebagai Admin | Submit form kelahiran kosong | — | Muncul error pada: tanggal_kelahiran, jml_anak_hidup, jml_anak_mati | ✅ |

---

## Modul 15 — Tracking Pertumbuhan (Penimbangan)

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| TP-01 | Admin melihat halaman tracking pertumbuhan | Login sebagai Admin | Buka `GET /tracking-pertumbuhan` | — | HTTP 200 | ✅ |
| TP-02 | Tamu tidak dapat akses tracking pertumbuhan | Tidak login | Buka `GET /tracking-pertumbuhan` | — | Redirect ke `/login` | ✅ |
| TP-03 | Filter berdasarkan tanggal dan kandang | Login sebagai Admin | Buka `/tracking-pertumbuhan?dari=2026-01-01&kandang_id=1` | dari: `2026-01-01`, kandang_id: valid | HTTP 200, data terfilter | ✅ |
| TP-04 | Lihat detail penimbangan satu domba | Login sebagai Admin | Klik nama domba / `GET /tracking-pertumbuhan/{ear_tag}` | ear_tag valid | HTTP 200, riwayat timbang domba tampil | ✅ |
| TP-05 | Detail domba tidak ada → 404 | Login sebagai Admin | Buka `GET /tracking-pertumbuhan/X-TIDAKADA` | ear_tag: `X-TIDAKADA` | HTTP 404 | ✅ |
| TP-06 | PK menyimpan data timbangan (ADG dihitung otomatis) | Login sebagai Pengurus Kandang | Di halaman mobile, isi form timbang, klik **Simpan** | Ear Tag: domba dengan penimbangan sebelumnya, Berat: `35`, Tanggal: hari ini | ADG dihitung otomatis berdasarkan selisih berat dan hari | ✅ |
| TP-07 | ADG = null untuk penimbangan pertama | Login sebagai Pengurus Kandang | Simpan timbangan pertama untuk domba yang belum pernah ditimbang | Ear Tag: domba baru tanpa riwayat timbang, Berat: `20` | Kolom `adg` di DB bernilai `null` (tidak ada pembanding) | ✅ |
| TP-08 | Validasi penimbangan — field wajib kosong | Login sebagai Pengurus Kandang | Submit form timbang tanpa data | — | Muncul error pada: ear_tag_id, berat_kg, tanggal_timbang | ✅ |

---

## Modul 16 — Silsilah & Deteksi Inbreeding

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| SS-01 | Admin melihat halaman silsilah | Login sebagai Admin | Buka `GET /silsilah` | — | HTTP 200 | ✅ |
| SS-02 | Tamu tidak dapat akses halaman silsilah | Tidak login | Buka `GET /silsilah` | — | Redirect ke `/login` | ✅ |
| SS-03 | Cari domba di halaman silsilah | Login sebagai Admin | Isi kotak pencarian dengan kode `B-` | search: `B-` | HTTP 200, hasil pencarian tampil | ✅ |
| SS-04 | Filter silsilah berdasarkan kategori | Login sebagai Admin | Buka `/silsilah?kategori=indukan` | kategori: `indukan` | HTTP 200, data terfilter | ✅ |
| SS-05 | Lihat detail silsilah domba (HTML) | Login sebagai Admin | Klik nama domba di halaman silsilah | — | HTTP 200, halaman silsilah domba tampil | ✅ |
| SS-06 | Ambil data pedigree domba (JSON) | Login sebagai Admin | `GET /silsilah/{ear_tag}` via JSON Accept header | ear_tag valid | HTTP 200 JSON berisi: `domba`, `pedigree`, `coi`, `coi_persen`, `status_inbreeding` | ✅ |
| SS-07 | Detail domba tidak ada → 404 | Login sebagai Admin | `GET /silsilah/X-TIDAK-ADA` via JSON | ear_tag: `X-TIDAK-ADA` | HTTP 404 | ✅ |
| SS-08 | Cek inbreeding antara dua domba | Login sebagai Admin | Klik **Cek Inbreeding**, pilih indukan dan pejantan, klik **Cek** | induk_id: ear_tag betina valid, pejantan_id: ear_tag jantan valid | HTTP 200 JSON berisi: `coi`, `coi_persen`, `status`, `rekomendasi`, `aman` | ✅ |
| SS-09 | Cek inbreeding — field kosong | Login sebagai Admin | Submit form cek inbreeding tanpa isi | — | HTTP 422, error pada: induk_id, pejantan_id | ✅ |
| SS-10 | Domba tanpa leluhur bersama → COI = 0, status aman | Login sebagai Admin | Cek inbreeding dua domba yang tidak ada hubungan silsilah | induk_id & pejantan_id: domba tanpa leluhur bersama | HTTP 200 JSON, `coi: 0.0`, `aman: true` | ✅ |
| SS-11 | Dapatkan rekomendasi pejantan untuk indukan | Login sebagai Admin | Pilih indukan di form rekomendasi, klik **Dapatkan Rekomendasi** | induk_id: ear_tag betina valid | HTTP 200 JSON berisi: `induk_id`, `rekomendasi` (array pejantan) | ✅ |
| SS-12 | Rekomendasi pejantan gagal tanpa induk_id | Login sebagai Admin | Submit form rekomendasi tanpa pilih indukan | — | HTTP 422, error pada `induk_id` | ✅ |

---

## Modul 17 — Notifikasi

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| N-01 | Admin melihat halaman notifikasi | Login sebagai Admin | Buka `GET /notifikasi` | — | HTTP 200 | ✅ |
| N-02 | Tamu tidak dapat akses notifikasi | Tidak login | Buka `GET /notifikasi` | — | Redirect ke `/login` | ✅ |
| N-03 | Filter notifikasi berdasarkan tipe | Login sebagai Admin | Buka `/notifikasi?tipe=stok_menipis` | tipe: `stok_menipis` | HTTP 200, hanya notifikasi tipe stok_menipis tampil | ✅ |
| N-04 | Admin menandai satu notifikasi sebagai dibaca | Login sebagai Admin | Klik ikon centang / **Tandai Dibaca** pada notifikasi (AJAX) | notifikasi_id: ID notifikasi milik user ini | HTTP 200 JSON `success: true` | ✅ |
| N-05 | Admin menandai semua notifikasi sebagai dibaca | Login sebagai Admin | Klik tombol **Tandai Semua Dibaca** | — | HTTP 200 JSON `success: true`, semua notifikasi milik user ditandai `sudah_dibaca: true` | ✅ |
| N-06 | Admin mengambil jumlah notifikasi belum dibaca | Login sebagai Admin | Polling badge lonceng / `GET /notifikasi/unread-count` | — | HTTP 200 JSON berisi `count: N` (jumlah yang belum dibaca) | ✅ |
| N-07 | Admin menghapus notifikasi miliknya | Login sebagai Admin | Klik ikon **X / Hapus** pada notifikasi milik sendiri (AJAX) | notifikasi_id: milik user ini | HTTP 200 JSON `success: true`, notifikasi terhapus dari DB | ✅ |
| N-08 | Admin tidak dapat menghapus notifikasi milik user lain | Login sebagai Admin A | Kirim DELETE ke notifikasi milik Admin B | notifikasi_id: milik user lain | Notifikasi milik user lain tetap ada di DB (tidak terhapus) | ✅ |

---

## Modul 18 — Mobile Pengurus Kandang (PK)

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| PK-01 | Pengurus Kandang dapat akses dashboard PK | Login sebagai Pengurus Kandang | Buka `GET /pk/dashboard` | — | HTTP 200 | ✅ |
| PK-02 | Tamu tidak dapat akses dashboard PK | Tidak login | Buka `GET /pk/dashboard` | — | Redirect ke `/login` | ✅ |
| PK-03 | Admin tidak dapat akses dashboard PK | Login sebagai Admin | Buka `GET /pk/dashboard` | — | HTTP 403 Forbidden | ✅ |
| PK-04 | PK dapat melihat daftar tugas | Login sebagai Pengurus Kandang | Buka `GET /pk/tugas` | — | HTTP 200, daftar tugas hari ini tampil | ✅ |
| PK-05 | PK dapat membuka halaman timbang | Login sebagai Pengurus Kandang | Buka `GET /pk/timbangan` | — | HTTP 200, form timbang tampil | ✅ |
| PK-06 | PK menyimpan data timbangan | Login sebagai Pengurus Kandang | Isi form timbang, klik **Simpan** | Ear Tag: domba valid, Berat (kg): `28.5`, Tanggal: hari ini | Redirect ke `/pk/timbangan`, data tersimpan di `penimbangan` dengan `status_validasi: pending` | ✅ |
| PK-07 | Validasi timbangan — field wajib kosong | Login sebagai Pengurus Kandang | Submit form timbang kosong | — | Muncul error pada: ear_tag_id, berat_kg, tanggal_timbang | ✅ |
| PK-08 | Status timbangan baru = pending (menunggu validasi KK) | Login sebagai Pengurus Kandang | Simpan data timbangan | Ear Tag: valid, Berat: `30` | Kolom `status_validasi` di DB berisi `pending` | ✅ |
| PK-09 | PK dapat membuka halaman kesehatan | Login sebagai Pengurus Kandang | Buka `GET /pk/kesehatan` | — | HTTP 200 | ✅ |
| PK-10 | PK melaporkan kondisi kesehatan domba | Login sebagai Pengurus Kandang | Isi form laporan kesehatan, klik **Laporkan** | Ear Tag: domba valid, Gejala: `Tidak mau makan sejak pagi`, Tingkat Keparahan: `ringan` | Redirect ke `/pk/dashboard`, rekam medis tersimpan di `medical_record` dengan `status: sakit` | ✅ |
| PK-11 | Validasi laporan kesehatan — field wajib kosong | Login sebagai Pengurus Kandang | Submit form laporan kesehatan kosong | — | Muncul error pada: ear_tag_id, gejala, tingkat_keparahan | ✅ |
| PK-12 | Validasi laporan kesehatan — tingkat keparahan tidak valid | Login sebagai Pengurus Kandang | Isi tingkat keparahan dengan nilai tidak dikenal | tingkat_keparahan: `sangat_parah` | Muncul error validasi pada `tingkat_keparahan` | ✅ |
| PK-13 | PK dapat membuka halaman kelahiran | Login sebagai Pengurus Kandang | Buka `GET /pk/kelahiran` | — | HTTP 200 | ✅ |
| PK-14 | Catat kelahiran gagal — tidak ada perkawinan aktif | Login sebagai Pengurus Kandang | Submit form kelahiran untuk domba tanpa perkawinan aktif | indukan_id: domba tanpa perkawinan berstatus `bunting`, Tanggal: hari ini, Jml Anak Hidup: `1` | Muncul error validasi pada `indukan_id` | ✅ |
| PK-15 | Catat kelahiran berhasil — ada perkawinan aktif | Login sebagai Pengurus Kandang | Submit form kelahiran untuk indukan yang punya perkawinan `bunting` | indukan_id: indukan dengan perkawinan berstatus `bunting`, Tanggal: hari ini, Jml Anak Hidup: `2`, Jml Anak Mati: `0` | Redirect ke `/pk/dashboard`, data tersimpan di tabel `kelahiran` | ✅ |

---

## Modul 19 — Mobile Kepala Kandang (KK)

| ID | Skenario | Prasyarat | Langkah Pengujian | Data Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|------------|----------------------|:------:|
| KK-01 | Kepala Kandang dapat akses dashboard KK | Login sebagai Kepala Kandang | Buka `GET /kk/dashboard` | — | HTTP 200, statistik kandang tampil | ✅ |
| KK-02 | Tamu tidak dapat akses dashboard KK | Tidak login | Buka `GET /kk/dashboard` | — | Redirect ke `/login` | ✅ |
| KK-03 | Pengurus Kandang tidak dapat akses dashboard KK | Login sebagai Pengurus Kandang | Buka `GET /kk/dashboard` | — | Redirect ke `/tugas-harian/mobile` | ✅ |
| KK-04 | KK dapat memonitor tugas harian semua PK | Login sebagai Kepala Kandang | Buka `GET /kk/monitor-tugas` | — | HTTP 200, daftar tugas hari ini tampil | ✅ |
| KK-05 | KK dapat melihat daftar laporan kesehatan | Login sebagai Kepala Kandang | Buka `GET /kk/kesehatan` | — | HTTP 200, daftar rekam medis aktif tampil | ✅ |
| KK-06 | KK mengkonfirmasi status kesehatan domba | Login sebagai Kepala Kandang | Klik **Konfirmasi** pada laporan kesehatan, pilih aksi | action: `dalam_perawatan`, rekam medis ID valid | Redirect ke `/kk/kesehatan`, status rekam medis berubah di DB | ✅ |
| KK-07 | Validasi konfirmasi kesehatan — action tidak valid | Login sebagai Kepala Kandang | Submit konfirmasi dengan action tidak dikenal | action: `action_tidak_valid` | Muncul error validasi pada `action` | ✅ |
| KK-08 | KK dapat melihat halaman reproduksi | Login sebagai Kepala Kandang | Buka `GET /kk/reproduksi` | — | HTTP 200, data kelahiran & perkawinan tampil | ✅ |
| KK-09 | KK mengkonfirmasi kebuntingan | Login sebagai Kepala Kandang | Klik **Konfirmasi Kebuntingan** pada perkawinan, pilih status | status: `bunting`, perkawinan ID valid | Redirect ke `/kk/reproduksi`, status perkawinan berubah menjadi `bunting` | ✅ |
| KK-10 | Validasi konfirmasi kebuntingan — status tidak valid | Login sebagai Kepala Kandang | Submit konfirmasi dengan status tidak dikenal | status: `tidak_valid` | Muncul error validasi pada `status` | ✅ |
| KK-11 | KK dapat melihat halaman validasi timbangan | Login sebagai Kepala Kandang | Buka `GET /kk/validasi-timbangan` | — | HTTP 200, daftar timbangan pending tampil | ✅ |
| KK-12 | KK memvalidasi data timbangan (terima) | Login sebagai Kepala Kandang | Klik **Validasi** pada data timbangan, pilih aksi `valid` | action: `valid`, timbangan_id: ID timbangan pending | Redirect ke `/kk/validasi-timbangan`, `status_validasi` berubah menjadi `valid` | ✅ |
| KK-13 | KK menolak data timbangan | Login sebagai Kepala Kandang | Klik **Tolak** pada data timbangan | action: `ditolak`, timbangan_id: ID timbangan pending | Redirect ke `/kk/validasi-timbangan`, `status_validasi` berubah menjadi `ditolak` | ✅ |
| KK-14 | Validasi process timbangan — action tidak valid | Login sebagai Kepala Kandang | Submit aksi dengan nilai tidak dikenal | action: `asal_approve` | Muncul error validasi pada `action` | ✅ |

---

## Modul 20 — Unit Test Model Domba

| ID | Skenario | Prasyarat | Langkah Pengujian | Kondisi / Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|-----------------|----------------------|:------:|
| UD-01 | Generate ear tag — prefix `J-` untuk jantan | — | Panggil `Domba::generateEarTag('jantan')` | jenis_kelamin: `jantan` | Ear tag dimulai dengan `J-` | ✅ |
| UD-02 | Generate ear tag — prefix `B-` untuk betina | — | Panggil `Domba::generateEarTag('betina')` | jenis_kelamin: `betina` | Ear tag dimulai dengan `B-` | ✅ |
| UD-03 | Format ear tag sesuai pola `X-NNN` | — | Generate ear tag | — | Ear tag sesuai regex `/^[JB]-\d{3}$/` | ✅ |
| UD-04 | Nomor urut ear tag naik otomatis | — | Generate ear tag beberapa kali | — | Nomor urut bertambah 1 setiap kali | ✅ |
| UD-05 | Urutan ear tag jantan dan betina independen | — | Generate ear tag jantan dan betina | — | Nomor urut jantan dan betina tidak saling mempengaruhi | ✅ |
| UD-06 | `bobot_terakhir` mengembalikan `null` jika belum pernah ditimbang | — | Akses properti `$domba->bobot_terakhir` | Domba tanpa riwayat timbang | Nilai `null` | ✅ |
| UD-07 | `bobot_terakhir` mengembalikan berat penimbangan terbaru | — | Akses `$domba->bobot_terakhir` setelah ada penimbangan | Domba dengan 2 riwayat timbang | Berat penimbangan terbaru (bukan yang pertama) | ✅ |
| UD-08 | Nama tabel model adalah `domba` | — | Cek `$domba->getTable()` | — | `"domba"` | ✅ |
| UD-09 | Primary key adalah `ear_tag_id` | — | Cek `$domba->getKeyName()` | — | `"ear_tag_id"` | ✅ |
| UD-10 | Primary key tidak auto-increment | — | Cek `$domba->getIncrementing()` | — | `false` | ✅ |
| UD-11 | Domba berelasi ke Kandang (belongsTo) | — | Cek `$domba->kandang` | Domba yang punya kandang | Instansi Kandang yang benar | ✅ |
| UD-12 | Domba berelasi ke banyak Penimbangan (hasMany) | — | Cek `$domba->penimbangan` | Domba dengan 2 riwayat timbang | Collection berisi 2 item | ✅ |
| UD-13 | Domba menggunakan soft deletes | — | Hapus domba, cek `deleted_at` | — | `deleted_at` terisi, domba masih di DB | ✅ |

---

## Modul 21 — Unit Test Model Tugas Harian

| ID | Skenario | Prasyarat | Langkah Pengujian | Kondisi / Input | Hasil yang Diharapkan | Status |
|----|----------|-----------|-------------------|-----------------|----------------------|:------:|
| UT-01 | Scope `hariIni` hanya mengembalikan tugas hari ini | — | Query `TugasHarian::hariIni()->get()` | DB berisi tugas hari ini dan kemarin | Hanya tugas dengan tanggal hari ini yang dikembalikan | ✅ |
| UT-02 | Scope `tanggal` memfilter berdasarkan tanggal tertentu | — | `TugasHarian::tanggal('2026-06-01')->get()` | tanggal: `2026-06-01` | Hanya tugas pada 2026-06-01 | ✅ |
| UT-03 | Filter `kandang_id` berfungsi | — | `TugasHarian::where('kandang_id', $id)->get()` | kandang_id: ID kandang tertentu | Hanya tugas milik kandang tersebut | ✅ |
| UT-04 | Scope `belumSelesai` mengecualikan tugas berstatus selesai | — | `TugasHarian::belumSelesai()->get()` | DB berisi tugas `belum` dan `selesai` | Tugas berstatus `selesai` tidak muncul | ✅ |
| UT-05 | `prioritas_color` mengembalikan warna kode hex yang benar | — | `$tugas->prioritas_color` | prioritas: `tinggi` / `sedang` / `rendah` | Kode warna hex sesuai prioritas | ✅ |
| UT-06 | `tipe_label` mengembalikan label bahasa Indonesia | — | `$tugas->tipe_label` | tipe: `rutin` / `kondisional` | Label yang sesuai (mis. `"Rutin"`) | ✅ |
| UT-07 | `status_label` mengembalikan label bahasa Indonesia | — | `$tugas->status_label` | status: `belum` / `dalam_proses` / `selesai` | Label yang sesuai (mis. `"Belum Dikerjakan"`) | ✅ |
| UT-08 | `durasi` mengembalikan `null` jika `waktu_mulai` tidak ada | — | `$tugas->durasi` | waktu_mulai: null | `null` | ✅ |
| UT-09 | `durasi` mengembalikan format menit untuk < 1 jam | — | `$tugas->durasi` | durasi 45 menit | `"45 menit"` | ✅ |
| UT-10 | `durasi` mengembalikan format jam untuk ≥ 1 jam | — | `$tugas->durasi` | durasi 90 menit | `"1 jam 30 menit"` atau sejenisnya | ✅ |
| UT-11 | Nama tabel adalah `tugas_harian` | — | `$tugas->getTable()` | — | `"tugas_harian"` | ✅ |
| UT-12 | Fillable mengandung field yang diperlukan | — | Cek `$tugas->getFillable()` | — | Array berisi field seperti `judul`, `kandang_id`, `tanggal`, `tipe`, `prioritas`, `status` | ✅ |

---

## Catatan Bug yang Ditemukan Selama Pengujian

| # | Lokasi | Bug | Dampak | Perbaikan yang Dilakukan |
|---|--------|-----|--------|--------------------------|
| 1 | `app/Http/Controllers/StokPakanController.php` | `PemberianPakan::create()` menyertakan field `satuan` dan `sisa_stok` yang tidak ada kolumnya di tabel `pemberian_pakan` | HTTP 500 saat catat pemberian pakan | Hapus field tersebut dari array create |
| 2 | `app/Http/Controllers/StokPakanController.php` | Overflow stok melempar `\Exception` tanpa ditangkap di dalam `DB::transaction()`, sehingga tidak ada pesan error ke user | HTTP 500 alih-alih redirect dengan pesan error | Tambah pengecekan sebelum transaksi, kembalikan `redirect()->back()->withErrors()` |
| 3 | `routes/web.php` | Route `GET /pakan-individual/search-domba` dideklarasikan **setelah** `GET /pakan-individual/{earTag}`, sehingga Laravel mencocokan `search-domba` sebagai `{earTag}` dan memanggil `show()` bukan `searchDomba()` | Endpoint search tidak pernah terpanggil, selalu redirect (302) | Pindahkan route `search-domba` ke atas route `{earTag}` |
| 4 | `routes/web.php` | `Route::resource('notifikasi', ...)` mendaftarkan route `GET /notifikasi/{id}` yang memanggil `show()`, padahal method tersebut tidak ada di `NotifikasiController`. Route ini mengintersep `GET /notifikasi/unread-count` | HTTP 500 saat mengambil jumlah notifikasi belum dibaca | Tambah `->except(['show', 'create', 'edit'])` pada resource declaration |
| 5 | `tests/Feature/NotifikasiTest.php` | Nama kolom yang digunakan adalah `is_read`, sedangkan kolom sebenarnya di DB adalah `sudah_dibaca` | QueryException pada semua test Notifikasi | Ganti semua `is_read` menjadi `sudah_dibaca` |
| 6 | `tests/Feature/ReproduksiTest.php`, `MobileKKTest.php`, `MobilePKTest.php` | Kolom `user_id` pada tabel `perkawinan` bersifat NOT NULL, tetapi data test diinsert tanpa `user_id` | QueryException pada semua test yang memasukkan data ke tabel perkawinan | Tambahkan `user_id` ke setiap perintah insert tabel `perkawinan` dan `kelahiran` |

---

## Keterangan Teknis

| Item | Detail |
|------|--------|
| Framework Pengujian | PHPUnit (bawaan Laravel 11) |
| Metode Isolasi DB | `DatabaseTransactions` — semua perubahan di-rollback setelah setiap test |
| Asset Frontend | `withoutVite()` di `tests/TestCase.php` — test tidak memerlukan build Vite |
| Autentikasi Test | `$this->actingAs(User)` untuk simulasi login |
| Data Test | Factory (`User`, `Kandang`, `Domba`, `TugasHarian`) + `DB::table()->insertGetId()` untuk tabel tanpa factory |
| Database | PostgreSQL (sesuai konfigurasi proyek) |
| Total Test Lulus | **303 / 303** (652 assertion) |
