# BAB XI: ALUR DETAIL ENKRIPSI DAN DEKRIPSI DATA REKAM MEDIS MENGGUNAKAN AES DAN PROTEKSI KUNCI RSA (ILUSTRATIF)

## XI.1 Pendahuluan

Bab ini menyajikan penjelasan mendalam mengenai proses enkripsi dan dekripsi data sensitif dalam aplikasi rekam medis, khususnya pada model `Rekam`. Proses ini menggunakan enkripsi AES untuk data aktual dan sebuah mekanisme proteksi kunci (yang akan kita kan sebagai enkripsi RSA) untuk mengamankan kunci AES tersebut. Kita akan menelusuri langkah demi langkah bagaimana data mentah diolah, dienkripsi, disimpan, dan kemudian didekripsi untuk ditampilkan, berdasarkan kode pada `EncryptionService.php` dan `Rekam.php`.

## XI.2 Komponen Utama

1.  **Data Mentah**: Informasi sensitif yang akan dienkripsi (misalnya, keluhan pasien).
2.  **Model `Rekam`** (<mcfile name="Rekam.php" path="d:\projects\app\app\Models\Rekam.php"></mcfile>): Bertanggung jawab memicu enkripsi saat penyimpanan (melalui *mutator* seperti `setKeluhanAttribute`) dan dekripsi saat pengambilan (melalui *accessor* seperti `getKeluhanAttribute`).
3.  **`EncryptionService`** (<mcfile name="EncryptionService.php" path="d:\projects\app\app\Services\EncryptionService.php"></mcfile>): Menyediakan logika inti untuk:
    *   Menghasilkan kunci AES unik per data.
    *   Mengenkripsi data dengan kunci AES tersebut.
    *   Mengenkripsi kunci AES (menggunakan mekanisme ilustratif 'RSA' yang diimplementasikan oleh `Crypt::encryptString`).
    *   Mendekripsi kunci AES (menggunakan mekanisme ilustratif 'RSA' yang diimplementasikan oleh `Crypt::decryptString`).
    *   Mendekripsi data dengan kunci AES yang telah didekripsi.
4.  **Kunci AES (Data Encryption Key - DEK)**: Kunci simetris (misalnya, AES-128) yang digunakan untuk mengenkripsi dan mendekripsi data aktual. Setiap data sensitif akan memiliki DEK unik.
5.  **Mekanisme Proteksi Kunci AES ( sebagai RSA)**: Kunci AES itu sendiri perlu dilindungi. Dalam implementasi Laravel, ini dilakukan menggunakan `Illuminate\Support\Facades\Crypt`, yang bergantung pada `APP_KEY` aplikasi untuk enkripsi simetris. Untuk tujuan penjelasan ini, kita akan mengkan proses ini seolah-olah kunci AES dienkripsi dengan sebuah "kunci publik RSA" dan didekripsi dengan "kunci privat RSA".

## XI.3 Proses Enkripsi Data: Dari Data Mentah ke Basis Data

Berikut adalah langkah-langkah detail bagaimana data mentah, misalnya `keluhan`, dienkripsi dan disimpan:

1.  **Input Data**: Pengguna memasukkan data keluhan melalui antarmuka aplikasi.

2.  **Pemicu di Model `Rekam`**: Saat data akan disimpan ke model `Rekam`, misalnya `$rekam->keluhan = "sakit kepala";`, *mutator* `setKeluhanAttribute($value)` di <mcfile name="Rekam.php" path="d:\projects\app\app\Models\Rekam.php"></mcfile> akan terpanggil secara otomatis.
    ```php
    // Dalam Rekam.php
    public function setKeluhanAttribute($value)
    {
        // ... (pengecekan nilai kosong)
        $pasien = $this->getAssociatedPasien(); // Mendapatkan pasien terkait
        // ... (penanganan jika pasien tidak ditemukan)

        // Memanggil EncryptionService untuk mengenkripsi data
        $encrypted = $this->getEncryptionService()->encryptData($value, $pasien); 
        // Catatan: Parameter $pasien di encryptData pada EncryptionService.php versi terbaru sudah dihilangkan.
        // Seharusnya menjadi: $encrypted = $this->getEncryptionService()->encryptData($value);

        if ($encrypted) {
            $this->attributes['encrypted_keluhan'] = $encrypted['encrypted_data'];
            $this->attributes['encrypted_keluhan_aes_key'] = $encrypted['encrypted_aes_key'];
        } else {
            // Penanganan jika enkripsi gagal
            Log::error("Rekam ID {$this->id}: Failed to encrypt keluhan.");
            $this->attributes['encrypted_keluhan'] = null;
            $this->attributes['encrypted_keluhan_aes_key'] = null;
        }
    }
    ```

3.  **Pemanggilan `EncryptionService->encryptData()`**: *Mutator* memanggil metode `encryptData(string $data)` (parameter `$pasien` sudah tidak ada di versi terbaru <mcfile name="EncryptionService.php" path="d:\projects\app\app\Services\EncryptionService.php"></mcfile>) dengan data mentah (`$value`) sebagai argumen.

4.  **Generasi Kunci AES dan IV di `EncryptionService`**: Di dalam `encryptData()`:
    *   Sebuah kunci AES 128-bit yang unik (`$aesKey`) dihasilkan menggunakan `openssl_random_pseudo_bytes(16)`.
    *   Sebuah Initialization Vector (IV) yang sesuai untuk metode `AES-128-CBC` (`self::AES_METHOD`) juga dihasilkan menggunakan `openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::AES_METHOD))`.
    ```php
    // Dalam EncryptionService.php -> encryptData()
    $aesKey = openssl_random_pseudo_bytes(16); // 128 bits for AES-128
    $ivLength = openssl_cipher_iv_length(self::AES_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);
    ```

5.  **Enkripsi Data Aktual dengan AES**: Data mentah (`$data`) dienkripsi menggunakan kunci AES (`$aesKey`) dan IV yang baru dibuat dengan fungsi `openssl_encrypt()`.
    ```php
    // Dalam EncryptionService.php -> encryptData()
    $encryptedData = openssl_encrypt($data, self::AES_METHOD, $aesKey, OPENSSL_RAW_DATA, $iv);
    // ... (penanganan error jika $encryptedData === false)
    ```
    IV kemudian digabungkan dengan data terenkripsi dan di-encode menggunakan Base64.
    ```php
    // Dalam EncryptionService.php -> encryptData()
    $encryptedDataWithIv = base64_encode($iv . $encryptedData);
    ```

6.  **Enkripsi Kunci AES ( 'RSA')**: Kunci AES (`$aesKey`) yang tadi digunakan untuk mengenkripsi data, sekarang dienkripsi itu sendiri. Ini dilakukan menggunakan `Crypt::encryptString($aesKey)`. Secara konseptual, ini seperti mengenkripsi `$aesKey` dengan sebuah "kunci publik RSA" agar aman disimpan.
    ```php
    // Dalam EncryptionService.php -> encryptData()
    $encryptedAesKey = Crypt::encryptString($aesKey);
    ```
    Setelah itu, `$aesKey` yang asli (plaintext) dihapus dari memori (`unset($aesKey)`).

7.  **Pengembalian Hasil Enkripsi**: Metode `encryptData()` mengembalikan sebuah array yang berisi data terenkripsi (sudah termasuk IV dan di-Base64) dan kunci AES yang sudah terenkripsi (oleh `Crypt`).
    ```php
    // Dalam EncryptionService.php -> encryptData()
    return [
        'encrypted_data' => $encryptedDataWithIv, 
        'encrypted_aes_key' => $encryptedAesKey 
    ];
    ```

8.  **Penyimpanan ke Atribut Model**: Kembali ke *mutator* di `Rekam.php`, nilai-nilai ini (`encrypted_data` dan `encrypted_aes_key`) disimpan ke kolom-kolom yang sesuai di basis data (misalnya, `encrypted_keluhan` dan `encrypted_keluhan_aes_key`).

9.  **Penyimpanan ke Basis Data**: Saat `$rekam->save()` dipanggil, Eloquent akan menyimpan atribut-atribut ini ke tabel `rekam` di basis data.

## XI.4 Proses Dekripsi Data: Dari Basis Data ke Tampilan Pengguna

Berikut adalah langkah-langkah detail bagaimana data terenkripsi diambil dari basis data dan didekripsi untuk ditampilkan:

1.  **Pengambilan Data**: Aplikasi mengambil data `Rekam` dari basis data, misalnya `$rekam = Rekam::find(1);`.

2.  **Pemicu di Model `Rekam`**: Saat aplikasi mencoba mengakses atribut yang terenkripsi, misalnya `echo $rekam->keluhan;`, *accessor* `getKeluhanAttribute($value)` di <mcfile name="Rekam.php" path="d:\projects\app\app\Models\Rekam.php"></mcfile> akan terpanggil secara otomatis. Parameter `$value` di sini adalah nilai dari kolom `keluhan` lama (jika ada dan belum dimigrasi), namun logika utama akan menggunakan kolom terenkripsi.
    ```php
    // Dalam Rekam.php
    public function getKeluhanAttribute($value)
    {
        if (!empty($this->encrypted_keluhan) && !empty($this->encrypted_keluhan_aes_key)) {
            $pasien = $this->getAssociatedPasien(); // Mendapatkan pasien terkait
            // ... (penanganan jika pasien tidak ditemukan)
            
            // Memanggil EncryptionService untuk mendekripsi data
            $decrypted = $this->getEncryptionService()->decryptData(
                $this->encrypted_keluhan,          // Data terenkripsi (IV + ciphertext)
                $this->encrypted_keluhan_aes_key,  // Kunci AES yang terenkripsi
                $pasien                            // Parameter $pasien sudah tidak ada di versi terbaru
            );
            // Seharusnya menjadi: $decrypted = $this->getEncryptionService()->decryptData($this->encrypted_keluhan, $this->encrypted_keluhan_aes_key);

            // ... (penanganan jika dekripsi gagal)
            return $decrypted;
        }
        return $value; // Fallback ke nilai lama jika tidak ada data terenkripsi
    }
    ```

3.  **Pemanggilan `EncryptionService->decryptData()`**: *Accessor* memanggil metode `decryptData(string $encryptedDataWithIv, string $encryptedAesKey)` (parameter `$pasien` sudah tidak ada di versi terbaru <mcfile name="EncryptionService.php" path="d:\projects\app\app\Services\EncryptionService.php"></mcfile>) dengan data terenkripsi (`$this->encrypted_keluhan`) dan kunci AES terenkripsi (`$this->encrypted_keluhan_aes_key`) sebagai argumen.

4.  **Dekripsi Kunci AES ( 'RSA') di `EncryptionService`**: Di dalam `decryptData()`:
    *   Kunci AES yang terenkripsi (`$encryptedAesKey`) pertama-tama didekripsi menggunakan `Crypt::decryptString($encryptedAesKey)`. Secara konseptual, ini seperti mendekripsi `$encryptedAesKey` dengan sebuah "kunci privat RSA" untuk mendapatkan kunci AES asli.
    ```php
    // Dalam EncryptionService.php -> decryptData()
    try {
         $aesKey = Crypt::decryptString($encryptedAesKey);
    } catch (DecryptException $e) {
         Log::error("Failed to decrypt AES key - " . $e->getMessage());
         return null;
    }
    // ... (pengecekan panjang $aesKey)
    ```

5.  **Ekstraksi IV dan Dekripsi Data Aktual dengan AES**: Setelah kunci AES (`$aesKey`) berhasil didapatkan:
    *   Data terenkripsi (`$encryptedDataWithIv`) di-decode dari Base64.
    *   IV diekstrak dari bagian awal data yang sudah di-decode.
    *   Sisa data (ciphertext aktual) kemudian didekripsi menggunakan kunci AES (`$aesKey`) dan IV yang telah diekstrak, melalui fungsi `openssl_decrypt()`.
    ```php
    // Dalam EncryptionService.php -> decryptData()
    $decodedEncryptedDataWithIv = base64_decode($encryptedDataWithIv);
    $ivLength = openssl_cipher_iv_length(self::AES_METHOD);
    $iv = substr($decodedEncryptedDataWithIv, 0, $ivLength);
    $encryptedData = substr($decodedEncryptedDataWithIv, $ivLength);

    // ... (pengecekan panjang IV)

    $decryptedData = openssl_decrypt($encryptedData, self::AES_METHOD, $aesKey, OPENSSL_RAW_DATA, $iv);
    unset($aesKey); // Hapus kunci AES dari memori

    // ... (penanganan error jika $decryptedData === false)
    ```

6.  **Pengembalian Data Asli**: Metode `decryptData()` mengembalikan data asli yang sudah berhasil didekripsi (`$decryptedData`).

7.  **Penggunaan Data Asli**: Kembali ke *accessor* di `Rekam.php`, nilai yang sudah didekripsi ini dikembalikan dan dapat digunakan oleh aplikasi (misalnya, untuk ditampilkan kepada pengguna).

## XI.5 Catatan Penting

*   **Keamanan `APP_KEY`**: Dalam implementasi Laravel yang sebenarnya, `Crypt::encryptString` dan `Crypt::decryptString` menggunakan `APP_KEY` dari file `.env` Anda sebagai kunci enkripsi simetris. Keamanan seluruh sistem enkripsi ini sangat bergantung pada kerahasiaan `APP_KEY` tersebut. Jika `APP_KEY` bocor, semua data yang dienkripsi menggunakan metode ini dapat didekripsi.
*   **Parameter `$pasien` pada `EncryptionService`**: Berdasarkan kode <mcfile name="EncryptionService.php" path="d:\projects\app\app\Services\EncryptionService.php"></mcfile> yang Anda berikan, parameter `$pasien` telah dihilangkan dari metode `encryptData` dan `decryptData`. Penjelasan di atas telah mencoba merefleksikan ini, namun beberapa contoh kode dari <mcfile name="Rekam.php" path="d:\projects\app\app\Models\Rekam.php"></mcfile> mungkin masih menunjukkan pemanggilan dengan parameter tersebut. Ini perlu disesuaikan agar konsisten.

Dengan mengikuti alur ini, data sensitif dalam aplikasi Anda dilindungi melalui enkripsi berlapis, memastikan kerahasiaan informasi pasien.