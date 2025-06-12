## SIMRS

Perlindungan data pribadi, khususnya dalam konteks rekam medis elektronik, merupakan isu krusial yang dihadapi oleh banyak institusi kesehatan. Undang-Undang Perlindungan Data Pribadi memberikan kerangka hukum untuk melindungi data pribadi individu dari penyalahgunaan. Penelitian ini bertujuan untuk menganalisis dan mengimplementasikan enkripsi end-to-end sebagai solusi untuk meningkatkan keamanan dan privasi data rekam medis elektronik. Metode yang digunakan dalam penelitian ini mencakup analisis literatur terkait enkripsi, desain sistem enkripsi end-to-end, serta implementasi prototipe aplikasi berbasis web yang mengintegrasikan enkripsi data. Hasil penelitian menunjukkan bahwa enkripsi end-to-end dapat secara signifikan mengurangi risiko kebocoran data dengan memastikan bahwa hanya pihak yang berwenang yang dapat mengakses informasi sensitif. Selain itu, penelitian ini juga mengeksplorasi tantangan dan solusi dalam penerapan enkripsi dalam sistem rekam medis elektronik. Temuan ini diharapkan dapat memberikan kontribusi terhadap pemahaman yang lebih baik mengenai perlindungan data pribadi dan mendukung institusi kesehatan dalam mematuhi regulasi yang berlaku.


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
    - Pengeluaran Obat
5. Master data
    - Petugas
    - Dokter
    - Obat
    - Tindakan

## Flow

Pendaftaran -> Dokter -> Apotek -> Pasien -> Done 

## Installation

1. Clone Repo
2. Move Directory repo
3. Composer Install
4. php artisan migrate
5. php artisan serve
