<?php

namespace App\Services;

// Pasien model is no longer needed for key management in this service
// use App\Models\Pasien;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log; // For logging errors
// Add DecryptException for catching Crypt errors specifically
use Illuminate\Contracts\Encryption\DecryptException;


class EncryptionService
{
    // RSA_CONFIG is no longer needed
    private const RSA_CONFIG = [
        "digest_alg" => "sha512",
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ];

    private const AES_METHOD = 'aes-128-cbc'; // AES-128 as requested

    /**
     * Generates a new RSA key pair for a patient.
     * Stores the public key directly and the private key encrypted.
     *
     * @param Pasien $pasien
     * @return bool True on success, false on failure.
     */
    // {{ Remove the entire generateAndStorePatientKeys method }}
    /*
    public function generateAndStorePatientKeys(Pasien $pasien): bool
    {
        try {
            // Generate a new private key
            $res = openssl_pkey_new(self::RSA_CONFIG);
            if (!$res) {
                Log::error("Failed to generate new private key for patient ID: " . $pasien->id . " - " . openssl_error_string());
                return false;
            }

            // Extract the private key
            openssl_pkey_export($res, $privateKey);
            if (!$privateKey) {
                 Log::error("Failed to export private key for patient ID: " . $pasien->id . " - " . openssl_error_string());
                 return false;
            }

            // Extract the public key
            $publicKeyDetails = openssl_pkey_get_details($res);
            if (!$publicKeyDetails || !isset($publicKeyDetails["key"])) {
                 Log::error("Failed to get public key details for patient ID: " . $pasien->id . " - " . openssl_error_string());
                 return false;
            }
            $publicKey = $publicKeyDetails["key"];

            // Encrypt the private key using Laravel's built-in encryption (uses APP_KEY)
            $encryptedPrivateKey = Crypt::encryptString($privateKey);

            // Store the keys in the patient model
            $pasien->rsa_public_key = $publicKey;
            $pasien->encrypted_rsa_private_key = $encryptedPrivateKey;
            $pasien->save();

            // Free the key resource
            openssl_pkey_free($res);

            return true;

        } catch (\Exception $e) {
            Log::error("Exception during key generation for patient ID: " . $pasien->id . " - " . $e->getMessage());
            return false;
        }
    }
    */

    /**
     * Retrieves and decrypts the patient's private key.
     * IMPORTANT: Handle this key securely and minimize its exposure.
     *
     * @param Pasien $pasien
     * @return string|null The decrypted private key or null on failure.
     */
    // {{ Remove the entire getDecryptedPrivateKey method }}
    /*
    private function getDecryptedPrivateKey(Pasien $pasien): ?string
    {
       // ... implementation removed ...
    }
    */


    /**
     * Encrypts data using AES, then encrypts the AES key using Laravel's default encryption (APP_KEY).
     *
     * @param string $data The data to encrypt.
     * @return array|null ['encrypted_data' => string, 'encrypted_aes_key' => string] or null on failure.
     */
    // {{ Modify encryptData: Remove Pasien parameter, use Crypt for AES key }}
    public function encryptData(string $data): ?array
    {
        // RSA public key check is removed
        // if (empty($pasien->rsa_public_key)) {

        try {
            // 1. Generate a unique AES key and IV for this data
            $aesKey = openssl_random_pseudo_bytes(16); // 128 bits for AES-128
            $ivLength = openssl_cipher_iv_length(self::AES_METHOD);
            $iv = openssl_random_pseudo_bytes($ivLength);

            // 2. Encrypt the data with AES
            $encryptedData = openssl_encrypt($data, self::AES_METHOD, $aesKey, OPENSSL_RAW_DATA, $iv);
             if ($encryptedData === false) {
                // Log error without patient ID context now
                Log::error("AES encryption failed - " . openssl_error_string());
                return null;
            }
            // Prepend IV to the encrypted data for later use during decryption
            $encryptedDataWithIv = base64_encode($iv . $encryptedData);


            // 3. Encrypt the AES key using Laravel's default encryption (APP_KEY)
            // {{ Replace RSA encryption with Crypt::encryptString }}
            $encryptedAesKey = Crypt::encryptString($aesKey);

            // Clear the plaintext AES key from memory ASAP after encryption
            unset($aesKey);


            return [
                'encrypted_data' => $encryptedDataWithIv, // Base64 encoded IV + Ciphertext
                'encrypted_aes_key' => $encryptedAesKey // Laravel encrypted, Base64 encoded string
            ];

        } catch (\Exception $e) {
             // Log error without patient ID context now
            Log::error("Exception during data encryption - " . $e->getMessage());
            return null;
        }
    }

    /**
     * Decrypts the AES key using Laravel's default encryption (APP_KEY), then decrypts the data using the AES key.
     *
     * @param string $encryptedDataWithIv Base64 encoded IV + encrypted data.
     * @param string $encryptedAesKey Laravel encrypted, Base64 encoded AES key.
     * @return string|null The original data or null on failure.
     */
     // {{ Modify decryptData: Remove Pasien parameter, use Crypt for AES key }}
    public function decryptData(string $encryptedDataWithIv, string $encryptedAesKey): ?string
    {
        try {
            // 1. Decrypt the AES key using Laravel's default encryption (APP_KEY)
            // {{ Replace RSA private key decryption with Crypt::decryptString }}
            try {
                 $aesKey = Crypt::decryptString($encryptedAesKey);
            } catch (DecryptException $e) {
                 // Log error without patient ID context now
                 Log::error("Failed to decrypt AES key - " . $e->getMessage());
                 return null;
            }


             if (strlen($aesKey) !== 16) { // Ensure key length is correct for AES-128
                 // Log error without patient ID context now
                Log::error("Decrypted AES key has incorrect length.");
                return null;
            }


            // 2. Decrypt the data with AES (Step 3 becomes Step 2)
            $decodedEncryptedDataWithIv = base64_decode($encryptedDataWithIv);
            $ivLength = openssl_cipher_iv_length(self::AES_METHOD);
            $iv = substr($decodedEncryptedDataWithIv, 0, $ivLength);
            $encryptedData = substr($decodedEncryptedDataWithIv, $ivLength);

            if (strlen($iv) !== $ivLength) {
                 // Log error without patient ID context now
                Log::error("IV length mismatch during decryption.");
                 // Clear potentially sensitive key from memory on error
                unset($aesKey);
                return null;
            }


            $decryptedData = openssl_decrypt($encryptedData, self::AES_METHOD, $aesKey, OPENSSL_RAW_DATA, $iv);

             // Clear the plaintext AES key from memory ASAP after use
            unset($aesKey);


            if ($decryptedData === false) {
                 // Log error without patient ID context now
                Log::error("AES decryption failed - " . openssl_error_string());
                return null;
            }

            return $decryptedData;

        } catch (\Exception $e) {
             // Log error without patient ID context now
            Log::error("Exception during data decryption - " . $e->getMessage());
             // Ensure key is cleared in case of unexpected exception
            unset($aesKey);
            return null;
        }
    }
}