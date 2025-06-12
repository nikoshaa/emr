<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pasien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EncryptExistingPasienData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pasien:encrypt-existing {--chunk=100 : Process records in chunks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt existing data for specified columns in the pasien table';

    /**
     * List of columns to encrypt (should match model/migration)
     */
    private $encryptedColumns = [
        "no_rm", "nama", "tmp_lahir", "tgl_lahir", "jk", "alamat_lengkap",
        "kelurahan", "kecamatan", "kabupaten", "kodepos", "agama", "status_menikah",
        "pendidikan", "pekerjaan", "kewarganegaraan", "no_hp", "cara_bayar",
        "no_bpjs", "alergi"
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting encryption of existing Pasien data...');
        $chunkSize = (int) $this->option('chunk');

        // Disable Eloquent events during mass update if necessary,
        // but we need the mutators, so we process one by one within chunks.
        // Pasien::withoutEvents(function () use ($chunkSize) { ... }); // Might disable mutators

        DB::table('pasien')->orderBy('id')->chunkById($chunkSize, function ($pasienRecords) {
            $this->info("Processing chunk of " . count($pasienRecords) . " records...");
            $bar = $this->output->createProgressBar(count($pasienRecords));
            $bar->start();

            foreach ($pasienRecords as $record) {
                // Find the Eloquent model instance to trigger mutators
                $pasienModel = Pasien::find($record->id);
                if (!$pasienModel) {
                    Log::warning("Pasien record not found during encryption: ID " . $record->id);
                    $bar->advance();
                    continue;
                }

                $needsSave = false;
                foreach ($this->encryptedColumns as $column) {
                    // Check if already encrypted (simple check: is _encrypted field non-null?)
                    // More robust: check if original field has value and encrypted is null
                    $originalValue = $record->{$column}; // Get original value directly from DB record
                    $encryptedColumn = $column . '_encrypted';

                    // Only encrypt if original value exists and not already encrypted
                    if ($originalValue !== null && $pasienModel->{$encryptedColumn} === null) {
                         // Setting the attribute on the model triggers the __set magic method -> mutator
                        $pasienModel->{$column} = $originalValue;
                        $needsSave = true;
                    }
                }

                if ($needsSave) {
                    try {
                        // Use saveQuietly to avoid firing events again if not needed
                        $pasienModel->saveQuietly();
                    } catch (\Exception $e) {
                         Log::error("Failed to save encrypted data for Pasien ID {$pasienModel->id}: " . $e->getMessage());
                    }
                }
                $bar->advance();
            }
            $bar->finish();
            $this->info("\nChunk processed.");
        });

        $this->info('Encryption process finished.');
        return 0;
    }
}
