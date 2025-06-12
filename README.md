## About SI-Shina

Adalah Aplikasi Sistem Rekam Medis Pasien untuk klinik Medishina yang mempunyai 2 poli yaitu :
- Poli Umum.
- Poli Gigi.

## Hak Akses

Hak Akses meliputi
1. Admin
2. Pendaftaran
3. Dokter
4. Apotek

Sistem meliputi :
1. Dashboard
2. Pasien
3. Rekam Medis
4. Apotek
    - Permintaan Resep
    - Pengeluaran Obat
5. Pembayaran
6. Master data
    - Petugas
    - Dokter
    - Obat
    - Tindakan
    - ICD X (10469 Entry)

## Flow
Pendaftaran -> Dokter -> Apotek -> Pembayaran (Jika Umum) -> Done

## Poli Umum & Gigi
Input Pendaftaran & Anamnesa => Subject => Object => Assessment => Plan

## Poli Gigi
Poli Gigi Support Pengisian Odontogram dan riwayat odontoragm pasien.
Odontogram adalah suatu gambar peta mengenai keadaan gigi di dalam mulut yang merupakan bagian yang tak terpisahkan dari rekam medis Kedokteran Gigi 

## Installation
1. Clone Repo
2. Move Directory repo
3. Composer Install
4. php artisan migrate
5. php artisan serve
