<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\User;

class PasienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Patient data array with name, age, and gender
        $patients = [
            ['nama' => 'Victoria Aryani', 'umur' => 39, 'jk' => 'P'],
            ['nama' => 'Emong Latupono', 'umur' => 56, 'jk' => 'P'],
            ['nama' => 'Ganjaran Nurdiyanti', 'umur' => 54, 'jk' => 'L'],
            ['nama' => 'H. Darsirah Prasasta', 'umur' => 58, 'jk' => 'L'],
            ['nama' => 'Waluyo Farida', 'umur' => 65, 'jk' => 'P'],
            ['nama' => 'Najwa Sitompul', 'umur' => 31, 'jk' => 'L'],
            ['nama' => 'Julia Marbun', 'umur' => 19, 'jk' => 'P'],
            ['nama' => 'Paris Nainggolan', 'umur' => 36, 'jk' => 'L'],
            ['nama' => 'Putri Sitompul', 'umur' => 71, 'jk' => 'P'],
            ['nama' => 'Ida Kusumo', 'umur' => 42, 'jk' => 'P'],
            ['nama' => 'Ulva Santoso', 'umur' => 69, 'jk' => 'P'],
            ['nama' => 'Intan Purwanti', 'umur' => 73, 'jk' => 'P'],
            ['nama' => 'Dinda Mustofa', 'umur' => 37, 'jk' => 'P'],
            ['nama' => 'Harjasa Puspasari', 'umur' => 50, 'jk' => 'P'],
            ['nama' => 'Ami Susanti', 'umur' => 62, 'jk' => 'L'],
            ['nama' => 'Nasrullah Usamah', 'umur' => 23, 'jk' => 'P'],
            ['nama' => 'R.A. Amelia Hutapea', 'umur' => 42, 'jk' => 'L'],
            ['nama' => 'Gawati Uyainah', 'umur' => 22, 'jk' => 'L'],
            ['nama' => 'Febi Nababan', 'umur' => 27, 'jk' => 'P'],
            ['nama' => 'Chelsea Iswahyudi', 'umur' => 61, 'jk' => 'P'],
            ['nama' => 'Yuliana Marpaung', 'umur' => 38, 'jk' => 'P'],
            ['nama' => 'Cut Julia Oktaviani', 'umur' => 75, 'jk' => 'P'],
            ['nama' => 'Hilda Pranowo', 'umur' => 17, 'jk' => 'L'],
            ['nama' => 'Kani Nashiruddin', 'umur' => 13, 'jk' => 'L'],
            ['nama' => 'Tami Irawan', 'umur' => 67, 'jk' => 'L'],
            ['nama' => 'Malika Susanti', 'umur' => 41, 'jk' => 'L'],
            ['nama' => 'Cakrawangsa Suwarno', 'umur' => 4, 'jk' => 'P'],
            ['nama' => 'Kayun Firmansyah', 'umur' => 59, 'jk' => 'P'],
            ['nama' => 'Ana Hutagalung', 'umur' => 31, 'jk' => 'L'],
            ['nama' => 'Talia Safitri', 'umur' => 72, 'jk' => 'P'],
            ['nama' => 'Naradi Susanti', 'umur' => 5, 'jk' => 'P'],
            ['nama' => 'Ilsa Fujiati', 'umur' => 54, 'jk' => 'L'],
            ['nama' => 'Karimah Usada', 'umur' => 78, 'jk' => 'L'],
            ['nama' => 'Dewi Utama', 'umur' => 63, 'jk' => 'L'],
            ['nama' => 'Ami Sitorus', 'umur' => 44, 'jk' => 'L'],
            ['nama' => 'Praba Salahudin', 'umur' => 65, 'jk' => 'L'],
            ['nama' => 'Martana Pranowo', 'umur' => 34, 'jk' => 'P'],
            ['nama' => 'Sabrina Pradana', 'umur' => 6, 'jk' => 'L'],
            ['nama' => 'Diah Prasetyo', 'umur' => 7, 'jk' => 'P'],
            ['nama' => 'Baktiono Firgantoro', 'umur' => 18, 'jk' => 'L'],
            ['nama' => 'Darman Pudjiastuti', 'umur' => 24, 'jk' => 'P'],
            ['nama' => 'Ina Halim', 'umur' => 74, 'jk' => 'L'],
            ['nama' => 'Drajat Aryani', 'umur' => 14, 'jk' => 'P'],
            ['nama' => 'Dt. Pangeran Manullang', 'umur' => 14, 'jk' => 'P'],
            ['nama' => 'Lega Thamrin', 'umur' => 37, 'jk' => 'P'],
            ['nama' => 'Karna Safitri', 'umur' => 16, 'jk' => 'L'],
            ['nama' => 'Artawan Wibowo', 'umur' => 16, 'jk' => 'P'],
            ['nama' => 'Amalia Putra', 'umur' => 62, 'jk' => 'P'],
            ['nama' => 'Hana Mulyani', 'umur' => 69, 'jk' => 'L'],
            ['nama' => 'Anita Tarihoran', 'umur' => 17, 'jk' => 'L'],
            ['nama' => 'Dt. Kala Mahena', 'umur' => 49, 'jk' => 'L'],
            ['nama' => 'Ridwan Astuti', 'umur' => 68, 'jk' => 'L'],
            ['nama' => 'Sadina Maulana', 'umur' => 43, 'jk' => 'P'],
            ['nama' => 'R. Najib Sitorus', 'umur' => 6, 'jk' => 'P'],
            ['nama' => 'Gandi Suwarno', 'umur' => 37, 'jk' => 'L'],
            ['nama' => 'Usyi Siregar', 'umur' => 27, 'jk' => 'L'],
            ['nama' => 'Paramita Hariyah', 'umur' => 64, 'jk' => 'P'],
            ['nama' => 'Amelia Pranowo', 'umur' => 19, 'jk' => 'L'],
            ['nama' => 'Garda Budiman', 'umur' => 62, 'jk' => 'P'],
            ['nama' => 'Irma Pratama', 'umur' => 21, 'jk' => 'L'],
            ['nama' => 'Emin Gunawan', 'umur' => 22, 'jk' => 'P'],
            ['nama' => 'Cahyadi Lailasari', 'umur' => 79, 'jk' => 'P'],
            ['nama' => 'R. Yuliana Wibisono', 'umur' => 14, 'jk' => 'P'],
            ['nama' => 'Elisa Habibi', 'umur' => 6, 'jk' => 'L'],
            ['nama' => 'Almira Simbolon', 'umur' => 7, 'jk' => 'L'],
            ['nama' => 'Harjo Kusmawati', 'umur' => 23, 'jk' => 'L'],
            ['nama' => 'Budi Nasyidah', 'umur' => 61, 'jk' => 'P'],
            ['nama' => 'Adikara Sihotang', 'umur' => 47, 'jk' => 'P'],
            ['nama' => 'Johan Nainggolan', 'umur' => 30, 'jk' => 'L'],
            ['nama' => 'Hj. Cinta Waluyo', 'umur' => 60, 'jk' => 'L'],
            ['nama' => 'Syahrini Tampubolon', 'umur' => 14, 'jk' => 'L'],
            ['nama' => 'Omar Hardiansyah', 'umur' => 27, 'jk' => 'P'],
            ['nama' => 'Farah Purwanti', 'umur' => 65, 'jk' => 'P'],
            ['nama' => 'Zelda Riyanti', 'umur' => 58, 'jk' => 'P'],
            ['nama' => 'Malika Halimah', 'umur' => 42, 'jk' => 'P'],
            ['nama' => 'Clara Halimah', 'umur' => 10, 'jk' => 'P'],
            ['nama' => 'Danang Megantara', 'umur' => 20, 'jk' => 'P'],
            ['nama' => 'Nova Tamba', 'umur' => 71, 'jk' => 'L'],
            ['nama' => 'R.A. Almira Hutapea', 'umur' => 62, 'jk' => 'L'],
            ['nama' => 'Jamal Nasyiah', 'umur' => 41, 'jk' => 'P'],
            ['nama' => 'Suci Handayani', 'umur' => 68, 'jk' => 'L'],
            ['nama' => 'Ida Sihombing', 'umur' => 15, 'jk' => 'P'],
            ['nama' => 'Paulin Waskita', 'umur' => 6, 'jk' => 'P'],
            ['nama' => 'Rina Fujiati', 'umur' => 61, 'jk' => 'P'],
            ['nama' => 'Puti Melinda Marbun', 'umur' => 25, 'jk' => 'L'],
            ['nama' => 'Kairav Fujiati', 'umur' => 48, 'jk' => 'L'],
            ['nama' => 'Janet Budiyanto', 'umur' => 64, 'jk' => 'L'],
            ['nama' => 'Balidin Haryanti', 'umur' => 73, 'jk' => 'L'],
            ['nama' => 'Wawan Wastuti, M.TI.', 'umur' => 67, 'jk' => 'P'],
            ['nama' => 'Ciaobella Wijayanti', 'umur' => 23, 'jk' => 'L'],
            ['nama' => 'Baktianto Haryanti, S.Ked', 'umur' => 23, 'jk' => 'L'],
            ['nama' => 'Xanana Uwais', 'umur' => 12, 'jk' => 'P'],
            ['nama' => 'Darsirah Prayoga', 'umur' => 28, 'jk' => 'L'],
            ['nama' => 'Paulin Pratiwi', 'umur' => 49, 'jk' => 'L'],
            ['nama' => 'Viktor Prasasta', 'umur' => 20, 'jk' => 'P'],
            ['nama' => 'Zamira Laksmiwati, S.IP', 'umur' => 36, 'jk' => 'L'],
            ['nama' => 'Hesti Budiman', 'umur' => 15, 'jk' => 'P'],
            ['nama' => 'Vicky Saputra', 'umur' => 48, 'jk' => 'P'],
            ['nama' => 'Anastasia Prabowo', 'umur' => 68, 'jk' => 'L'],
            ['nama' => 'Hj. Sabrina Halimah', 'umur' => 59, 'jk' => 'P'],
        ];

        // Used RM numbers to avoid duplicates
        $usedRmNumbers = [];
        // Used BPJS numbers to avoid duplicates
        $usedBpjsNumbers = [];
        
        // Current year for age calculation
        $currentYear = Carbon::now()->year;
        
        foreach ($patients as $patient) {
            // Generate random 3-digit RM number that's unique
            do {
                $rmNumber = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            } while (in_array($rmNumber, $usedRmNumbers));
            $usedRmNumbers[] = $rmNumber;
            
            // Generate random 10-digit BPJS number that's unique
            do {
                $bpjsNumber = mt_rand(1000000000, 9999999999);
            } while (in_array($bpjsNumber, $usedBpjsNumbers));
            $usedBpjsNumbers[] = $bpjsNumber;
            
            // Calculate birth year based on age
            $birthYear = $currentYear - $patient['umur'];
            
            // Generate random birth date within that year
            $month = mt_rand(1, 12);
            $day = mt_rand(1, 28); // Using 28 to avoid issues with February
            $birthDate = Carbon::createFromDate($birthYear, $month, $day)->format('Y-m-d');
            
            // Generate random phone number
            $phoneNumber = '08' . mt_rand(1, 9) . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            
            

            

            $user = new User();
                // $user->id = $i+1;
            $user->name = $patient['nama'];
            $user->password = bcrypt('qwe');
            // $user->phone = '00000000'. $i;
            // genereate random 12 number
            $user->phone = mt_rand(100000000000, 999999999999);
            $user->email = strtolower(str_replace(' ', '', $patient['nama'])).'@gmail.com';
            $user->role = 5;
            $user->status = 1;
            $user->email_verified_at = now();
            $user->save();

            // Create the patient record
            $pasien = new Pasien();
            $pasien->user_id = $user->id;
            $pasien->nama = $patient['nama'];
            $pasien->no_rm = $rmNumber;
            $pasien->no_bpjs = $bpjsNumber;
            $pasien->tgl_lahir = $birthDate;
            $pasien->jk = $patient['jk'] == 'L' ? 'Laki-Laki' : 'Perempuan';
            $pasien->no_hp = $phoneNumber;
            // $pasien->alamat = 'Jl. ' . Str::random(10) . ' No. ' . mt_rand(1, 100);
            $pasien->cara_bayar = 'Umum/Mandiri';
            $pasien->save();
        }
    }
}
