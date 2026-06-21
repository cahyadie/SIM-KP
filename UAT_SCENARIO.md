
# User Acceptance Testing (UAT) - SIM-KP

**Sistem Informasi Kerja Praktik**  
**Prodi Teknologi Informasi**

---

## Daftar Isi
1. [Role Mahasiswa](#1-role-mahasiswa)
2. [Role Dosen Pembimbing](#2-role-dosen-pembimbing)
3. [Role Kaprodi](#3-role-kaprodi)
4. [Role Admin](#4-role-admin)

---

## 1. Role Mahasiswa

### Modul: Autentikasi

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| M-AUTH-01 | Login dengan email dan password valid | 1. Buka halaman login<br>2. Masukkan email dan password yang terdaftar<br>3. Klik tombol "Login" | Berhasil masuk ke dashboard mahasiswa |
| M-AUTH-02 | Login menggunakan Microsoft Azure SSO | 1. Buka halaman login<br>2. Klik tombol "Masuk dengan Microsoft"<br>3. Login dengan akun Microsoft kampus | Berhasil masuk dan akun ter-link |
| M-AUTH-03 | Login dengan password salah | 1. Masukkan email valid + password salah<br>2. Klik Login | Muncul pesan error "Email atau password salah" |
| M-AUTH-04 | Logout dari sistem | 1. Klik tombol Logout di navbar<br>2. Konfirmasi logout | Kembali ke halaman login, session berakhir |

### Modul: Dashboard Mahasiswa

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| M-DSH-01 | Melihat dashboard setelah login | 1. Login sebagai mahasiswa<br>2. Amati halaman dashboard | Menampilkan profil singkat dan timeline riwayat magang |
| M-DSH-02 | Melihat timeline riwayat magang | 1. Scroll ke bagian timeline<br>2. Amati data magang yang ditampilkan | Timeline menampilkan status magang terbaru hingga terlama |

### Modul: Pendaftaran Magang

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| M-MAG-01 | Mendaftar magang dengan data lengkap | 1. Buka menu "Daftar Magang"<br>2. Pilih dosen pembimbing dari dropdown<br>3. Input nama perusahaan (jika baru) atau pilih perusahaan existing<br>4. Isi alamat perusahaan<br>5. Pilih tanggal mulai dan selesai<br>6. Pilih status gaji (paid/unpaid)<br>7. Isi tema magang<br>8. Upload file surat pendukung<br>9. Klik "Daftar" | Data magang tersimpan, status otomatis "diterima" |
| M-MAG-02 | Mendaftar magang tanpa upload file | 1. Ikuti langkah M-MAG-01 tanpa upload file<br>2. Klik "Daftar" | Data magang tetap tersimpan (file opsional) |
| M-MAG-03 | Mendaftar magang dengan perusahaan sudah ada | 1. Mulai ketik nama perusahaan<br>2. Pilih dari daftar auto-complete/suggestion | Data perusahaan terisi otomatis (alamat, dll) |
| M-MAG-04 | Melihat riwayat magang yang sudah didaftarkan | 1. Buka halaman dashboard<br>2. Lihat bagian timeline/riwayat | Semua pendaftaran magang tampil dengan statusnya |

### Modul: Logbook

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| M-LOG-01 | Mengisi logbook mingguan | 1. Buka menu "Logbook"<br>2. Klik "Isi Logbook"<br>3. Isi kegiatan untuk hari Senin-Jumat<br>4. Tulis permasalahan dan solusi per hari<br>5. Klik "Simpan" | Logbook tersimpan dengan nomor minggu terhitung otomatis |
| M-LOG-02 | Melihat riwayat logbook yang sudah diisi | 1. Buka menu "Logbook"<br>2. Lihat daftar logbook per minggu | Semua logbook tampil dengan status ACC |
| M-LOG-03 | Logbook otomatis terisi minggu ke-1 dari tgl_mulai | 1. Buka form isi logbook<br>2. Periksa field minggu_ke, tgl_mulai, tgl_selesai | Tanggal otomatis sesuai dengan minggu berjalan sejak tgl_mulai magang |

### Modul: SKP / Seminar

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| M-SKP-01 | Mengajukan 3 opsi jadwal seminar | 1. Buka menu "Seminar/SKP"<br>2. Isi 3 opsi tanggal dan jam (semua tanggal berbeda dan di masa depan)<br>3. Isi ruangan seminar<br>4. Klik "Ajukan Jadwal" | Jadwal tersimpan, status_jadwal_skp = "menunggu" |
| M-SKP-02 | Mengajukan jadwal dengan tanggal sudah lewat | 1. Pilih tanggal yang sudah berlalu<br>2. Klik "Ajukan Jadwal" | Sistem menolak, muncul validasi tanggal harus di masa depan |
| M-SKP-03 | Mengajukan jadwal dengan 2 tanggal sama | 1. Isi opsi 1 dan opsi 2 dengan tanggal sama<br>2. Klik "Ajukan Jadwal" | Sistem menolak, muncul validasi tanggal harus berbeda |
| M-SKP-04 | Submit nilai seminar dan file seminar | 1. Setelah jadwal disetujui dosen<br>2. Isi nilai (A/B/C/D/E)<br>3. Upload file seminar (PDF)<br>4. Klik "Simpan" | Data tersimpan, status_skp otomatis menjadi "sudah" |
| M-SKP-05 | Melihat status SKP setelah submit | 1. Buka menu Seminar/SKP<br>2. Lihat status SKP | Status SKP = "sudah" |

### Modul: Profil

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| M-PRF-01 | Mengedit profil pribadi | 1. Buka menu "Profil"<br>2. Ubah nama/email/no_hp<br>3. Klik "Simpan" | Data profil berhasil diperbarui |

### Modul: Info Lowongan & Tempat Magang

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| M-LOW-01 | Melihat daftar lowongan magang | 1. Buka menu "Info Lowongan"<br>2. Gunakan fitur search/filter | Menampilkan daftar pengumuman lowongan |
| M-LOW-02 | Melihat detail lowongan | 1. Klik salah satu lowongan<br>2. Lihat detail informasi | Informasi lengkap (syarat, fasilitas, link daftar) |
| M-DIR-01 | Melihat direktori perusahaan | 1. Buka menu "Tempat Magang"<br>2. Cari perusahaan | Daftar perusahaan tampil |
| M-DIR-02 | Memberikan review perusahaan (pernah magang) | 1. Buka detail perusahaan<br>2. Klik "Tambah Review"<br>3. Beri rating 1-5 dan komentar<br>4. Klik "Simpan" | Review tersimpan (hanya jika mahasiswa magang di sana dengan status diterima) |
| M-DIR-03 | Melihat review yang sudah diberikan | 1. Buka detail perusahaan<br>2. Scroll ke bagian review | Review yang sudah ditulis tampil |

---

## 2. Role Dosen Pembimbing

### Modul: Autentikasi

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| D-AUTH-01 | Login dengan email dan password valid | 1. Buka halaman login<br>2. Masukkan email dan password dosen<br>3. Klik Login | Masuk ke dashboard dosen |

### Modul: Dashboard Dosen

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| D-DSH-01 | Melihat dashboard dosen | 1. Login sebagai dosen<br>2. Amati dashboard | Map lokasi mahasiswa bimbingan tampil dengan marker berbeda (aktif, seminar, selesai SKP) |
| D-DSH-02 | Melihat statistik bimbingan | 1. Lihat kartu statistik di dashboard | Menampilkan total bimbingan, jumlah aktif, jumlah selesai magang, jumlah SKP |

### Modul: Bimbingan & Logbook

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| D-BIM-01 | Melihat daftar mahasiswa bimbingan aktif | 1. Buka menu "Bimbingan"<br>2. Lihat daftar mahasiswa | Semua mahasiswa bimbingan dengan status magang aktif tampil |
| D-BIM-02 | Melihat detail bimbingan mahasiswa | 1. Klik salah satu mahasiswa<br>2. Lihat detail | Detail magang, progress logbook, dan status SKP |
| D-BIM-03 | Melihat isi logbook mahasiswa | 1. Buka detail mahasiswa<br>2. Klik "Lihat Logbook" | Isi logbook per minggu tampil (kegiatan, permasalahan, solusi) |
| D-BIM-04 | Memberi komentar pada logbook | 1. Buka logbook mahasiswa<br>2. Tulis komentar<br>3. Klik "Simpan" | Komentar tersimpan |
| D-BIM-05 | ACC logbook mahasiswa | 1. Buka logbook mahasiswa<br>2. Klik tombol "ACC"<br>3. Konfirmasi | Status ACC logbook berubah menjadi benar (tercentang) |
| D-BIM-06 | Membatalkan ACC logbook | 1. Klik tombol "Batal ACC"<br>2. Konfirmasi | Status ACC kembali menjadi false |

### Modul: Jadwal SKP (Seminar)

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| D-SKP-01 | Melihat daftar pengajuan jadwal SKP | 1. Buka menu "Jadwal SKP"<br>2. Lihat daftar mahasiswa yang sudah mengajukan | Mahasiswa yang sudah isi jadwal seminar tampil dengan status "menunggu" |
| D-SKP-02 | Menyetujui salah satu opsi jadwal | 1. Klik "Respon" pada mahasiswa<br>2. Pilih salah satu opsi jadwal yang disetujui<br>3. Klik "Setujui" | Status jadwal berubah menjadi "disetujui", jadwal_terpilih terisi |
| D-SKP-03 | Menolak semua opsi jadwal | 1. Klik "Respon"<br>2. Pilih "Tolak Semua"<br>3. Isi alasan penolakan<br>4. Klik "Tolak" | Status jadwal berubah menjadi "ditolak", alasan tersimpan |
| D-SKP-04 | Menolak tanpa mengisi alasan | 1. Pilih "Tolak Semua"<br>2. Kosongkan alasan<br>3. Klik "Tolak" | Sistem mencegah, validasi alasan required |

### Modul: Riwayat Magang

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| D-RIW-01 | Melihat riwayat magang mahasiswa bimbingan | 1. Buka menu "Riwayat Magang"<br>2. Gunakan filter/search | Data magang mahasiswa bimbingan tampil |
| D-RIW-02 | Export riwayat magang ke PDF/Excel | 1. Klik tombol Export PDF/Excel<br>2. Tunggu proses | File PDF/Excel terdownload |

---

## 3. Role Kaprodi

### Modul: Autentikasi

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| K-AUTH-01 | Login sebagai Kaprodi | 1. Login dengan akun kaprodi<br>2. Verifikasi akses | Masuk ke dashboard kaprodi dengan menu sesuai hak akses |

### Modul: Dashboard Kaprodi (Analitik)

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| K-DSH-01 | Melihat dashboard kaprodi | 1. Buka dashboard<br>2. Amati seluruh widget | Menampilkan: total mahasiswa, magang aktif, SKP pending, SKP selesai |
| K-DSH-02 | Melihat grafik statistik pengajuan | 1. Scroll ke bagian grafik<br>2. Filter tahun | Grafik total pengajuan, diterima, pending, ditolak tampil |
| K-DSH-03 | Melihat grafik status gaji | 1. Cari chart "Status Gaji"<br>2. Amati perbandingan paid vs unpaid | Pie chart status gaji tampil |
| K-DSH-04 | Melihat top perusahaan chart | 1. Cari chart "Perusahaan Terpopuler"<br>2. Amati | Bar chart perusahaan dengan mahasiswa terbanyak tampil |
| K-DSH-05 | Melihat distribusi industri chart | 1. Cari chart distribusi industri<br>2. Amati | Pie chart kategori industri tampil |
| K-DSH-06 | Melihat peta lokasi magang | 1. Cari peta Leaflet<br>2. Klik marker | Peta dengan marker lokasi magang tampil, klik marker menunjukkan info mahasiswa |
| K-DSH-07 | Melihat tabel mahasiswa magang aktif | 1. Scroll ke "Magang Aktif" | Tabel mahasiswa yang sedang magang tampil |
| K-DSH-08 | Melihat tabel mahasiswa lulus SKP | 1. Scroll ke "Lulus SKP" | Tabel mahasiswa yang sudah lulus SKP tampil |

### Modul: Statistik Detail

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| K-STAT-01 | Membuka statistik detail dari kartu dashboard | 1. Klik salah satu kartu statistik di dashboard (misal: "Pengajuan")<br>2. Amati halaman detail | Halaman statistik detail dengan drill-down chart tampil |
| K-STAT-02 | Filter statistik berdasarkan tahun | 1. Pilih tahun pada dropdown filter<br>2. Data berubah sesuai tahun | Grafik dan data menyesuaikan filter tahun |

### Modul: Pantauan SKP

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| K-PSKP-01 | Melihat daftar mahasiswa yang terlambat SKP | 1. Buka menu "Pantauan SKP"<br>2. Amati daftar | Mahasiswa dengan magang selesai >30 hari tapi belum SKP tampil |
| K-PSKP-02 | Export PDF Pantauan SKP | 1. Klik "Export PDF"<br>2. Tunggu | File PDF terdownload dengan data pantauan |

### Modul: Riwayat Magang & Data SKP

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| K-RIW-01 | Melihat riwayat magang (read-only) | 1. Buka "Riwayat Magang"<br>2. Filter bulan/tahun/status<br>3. Cari/search | Data riwayat magang tampil |
| K-RIW-02 | Export riwayat magang | 1. Klik Export Excel/PDF | File terdownload |
| K-SKP-01 | Melihat data SKP | 1. Buka "Data SKP"<br>2. Filter/search | Data SKP seluruh mahasiswa tampil |

### Modul: Monitoring Logbook

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| K-MON-01 | Melihat monitoring logbook mahasiswa aktif | 1. Buka menu "Monitoring"<br>2. Lihat tabel progress | Progress pengisian logbook per mahasiswa tampil |
| K-MON-02 | Melihat detail logbook mahasiswa | 1. Klik nama mahasiswa<br>2. Lihat detail | Logbook per minggu dari mahasiswa tersebut tampil |

### Modul: Direktori Perusahaan

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| K-DIR-01 | Melihat direktori perusahaan | 1. Buka "Tempat Magang"<br>2. Cari/navigasi | Daftar perusahaan tampil |

---

## 4. Role Admin

### Modul: Autentikasi

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| A-AUTH-01 | Login sebagai Admin | 1. Login dengan akun admin<br>2. Verifikasi akses | Masuk ke dashboard admin |

### Modul: Dashboard Admin

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| A-DSH-01 | Melihat dashboard admin | 1. Buka dashboard<br>2. Amati widget | Widget: pendaftar baru, magang aktif, SKP pending, total mahasiswa tampil |
| A-DSH-02 | Melihat peta lokasi magang | 1. Scroll ke peta<br>2. Klik marker | Peta Leaflet dengan marker lokasi magang tampil |

### Modul: Manajemen Data - Riwayat Magang

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| A-RIW-01 | Melihat semua riwayat magang | 1. Buka "Riwayat Magang"<br>2. Amati tabel | Semua data magang seluruh mahasiswa tampil |
| A-RIW-02 | Filter riwayat magang | 1. Pilih filter bulan<br>2. Pilih filter tahun<br>3. Pilih filter status<br>4. Klik "Filter" | Data terfilter sesuai kriteria |
| A-RIW-03 | Mencari mahasiswa di riwayat magang | 1. Ketik nama/NIM di search box<br>2. Lihat hasil | Data terfilter sesuai keyword pencarian |
| A-RIW-04 | Melihat detail riwayat magang | 1. Klik tombol "Detail"/"Lihat"<br>2. Amati | Detail lengkap data magang mahasiswa tampil |
| A-RIW-05 | Export riwayat magang ke Excel | 1. Klik "Export Excel"<br>2. Tunggu proses | File Excel (.xlsx) terdownload dengan data sesuai filter |
| A-RIW-06 | Export riwayat magang ke PDF | 1. Klik "Export PDF"<br>2. Tunggu proses | File PDF terdownload dengan data sesuai filter |

### Modul: Manajemen Data - Data SKP

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| A-SKP-01 | Melihat daftar SKP | 1. Buka "Data SKP"<br>2. Filter/search | Data SKP seluruh mahasiswa tampil |
| A-SKP-02 | Verifikasi SKP | 1. Klik "Verifikasi" pada SKP status tertentu<br>2. Konfirmasi | Status SKP terverifikasi |
| A-SKP-03 | Menolak/revisi SKP | 1. Klik "Tolak" pada SKP<br>2. Isi catatan revisi<br>3. Konfirmasi | Status SKP ditolak, catatan revisi tersimpan, mahasiswa bisa perbaiki |
| A-SKP-04 | Membatalkan verifikasi SKP | 1. Klik "Batal Verifikasi" pada SKP yang sudah diverifikasi<br>2. Konfirmasi | Verifikasi dibatalkan |
| A-SKP-05 | Export data SKP ke Excel | 1. Klik "Export Excel"<br>2. Tunggu | File Excel terdownload |
| A-SKP-06 | Export data SKP ke PDF | 1. Klik "Export PDF"<br>2. Tunggu | File PDF terdownload |

### Modul: Kelola Pengumuman

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| A-PGN-01 | Membuat pengumuman lowongan baru | 1. Buka "Kelola Pengumuman"<br>2. Klik "Tambah Pengumuman"<br>3. Isi judul, deskripsi, lokasi<br>4. Isi info gaji, fasilitas, syarat<br>5. Isi target angkatan<br>6. Isi link pendaftaran<br>7. Klik "Simpan" | Pengumuman tersimpan, tampil di daftar |
| A-PGN-02 | Mengedit pengumuman yang sudah ada | 1. Klik "Edit" pada pengumuman<br>2. Ubah beberapa field<br>3. Klik "Update" | Data pengumuman berubah |
| A-PGN-03 | Menghapus pengumuman | 1. Klik "Hapus" pada pengumuman<br>2. Konfirmasi penghapusan | Pengumuman terhapus dari database |
| A-PGN-04 | Melihat detail pengumuman | 1. Klik judul pengumuman<br>2. Amati | Detail pengumuman tampil |

### Modul: Manajemen Akun

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| A-USR-01 | Melihat daftar user (non-mahasiswa) | 1. Buka "Manajemen Akun"<br>2. Lihat tabel | Daftar user admin, kaprodi, dan dosen tampil |
| A-USR-02 | Menambahkan user baru (dosen/admin/kaprodi) | 1. Klik "Tambah User"<br>2. Pilih role (admin/kaprodi/dosen)<br>3. Isi nama, email, NIP, password<br>4. Klik "Simpan" | User baru tersimpan, bisa login |
| A-USR-03 | Mengedit user yang sudah ada | 1. Klik "Edit" pada user<br>2. Ubah data<br>3. Klik "Update" | Data user berubah |
| A-USR-04 | Menghapus user | 1. Klik "Hapus" pada user<br>2. Konfirmasi | User terhapus dari database |

### Modul: Direktori Perusahaan

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| A-DIR-01 | Melihat direktori perusahaan | 1. Buka "Tempat Magang"<br>2. Lihat daftar | Semua perusahaan tampil |
| A-DIR-02 | Menghapus review perusahaan | 1. Buka detail perusahaan<br>2. Klik "Hapus" pada Review yang tidak pantas<br>3. Konfirmasi | Review terhapus |
| A-DIR-03 | Menghapus data magang perusahaan | 1. Buka detail perusahaan<br>2. Klik "Hapus" pada data magang terkait perusahaan<br>3. Konfirmasi | Data magang terhapus |

### Modul: Info Lowongan (Publik)

| ID | Skenario | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|
| A-LOW-01 | Melihat info lowongan dari sisi publik | 1. Buka "Info Lowongan"<br>2. Cari/search | Lowongan yang sudah dibuat admin tampil dengan baik |

---

## Ringkasan Skenario

| Role | Jumlah Skenario |
|---|---|
| Mahasiswa | 23 |
| Dosen Pembimbing | 12 |
| Kaprodi | 14 |
| Admin | 20 |
| **Total** | **69** |
