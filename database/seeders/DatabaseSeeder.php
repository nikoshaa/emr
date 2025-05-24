<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Poli;
use App\Models\Dokter;
use Database\Seeders\PasienSeeder;
use Database\Seeders\ObatSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Comment out or remove the random user factory
        // User::factory(10)->create();
        
        // Create polis first
        $polis = [
            [
                'nama' => 'Poli Umum',
                'status' => 1
            ],
            [
                'nama' => 'Poli Gigi',
               'status' => 1
            ],
            [
                'nama' => 'Poli Anak',
               'status' => 1
            ],
            [
                'nama' => 'Poli Penyakit Dalam',
              'status' => 1
            ],
            [
                'nama' => 'Poli Kulit & Kelamin',
             'status' => 1
            ]
            ];
        for ($i=0; $i < count($polis); $i++) {
            $p = new Poli();
            $p->nama = $polis[$i]['nama'];
            $p->status = $polis[$i]['status'];
            $p->save();
        };
        
        // Create doctors with their corresponding users
        $dokters = [
            [
                'nama' => 'Dr. Andi Setiawan',
                'poli' => 'Poli Umum',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Budi Santoso',
                'poli' => 'Poli Gigi',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Citra Dewi',
                'poli' => 'Poli Anak',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Dian Pratama',
                'poli' => 'Poli Penyakit Dalam',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Eka Wulandari',
                'poli' => 'Poli Kulit & Kelamin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Farhan Nugroho',
                'poli' => 'Poli Umum',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Gina Maharani',
                'poli' => 'Poli Gigi',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Hadi Wijaya',
                'poli' => 'Poli Anak',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Intan Ramadhani',
                'poli' => 'Poli Penyakit Dalam',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                
                'nama' => 'Dr. Joko Susanto',
                'poli' => 'Poli Kulit & Kelamin',
                'created_at' => now(),
                'updated_at' => now()
            ]

            
            ];
            for ($i=0; $i < count($dokters); $i++) {
                // Create user first to get a valid user_id
                $user = new User();
                // $user->id = $i+1;
                $user->name = $dokters[$i]['nama'];
                $user->password = bcrypt('12345678');
                $user->phone = '000000000'. $i;
                $user->email = strtolower(str_replace(' ', '', $dokters[$i]['nama'])).'@gmail.com';
                $user->role = 3;
                $user->status = 1;
                $user->email_verified_at = now();
                $user->save();

                
            };

            for ($i=0; $i < count($dokters); $i++) {
                // Now create the doctor with the valid user_id
                $d = new Dokter();
                $d->nip = '000000000'. $i;
                $d->nama = $dokters[$i]['nama'];
                $d->poli = $dokters[$i]['poli'];
                $d->created_at = $dokters[$i]['created_at'];
                $d->updated_at = $dokters[$i]['updated_at'];
                $d->user_id = $i+1; // Assuming user_id starts from 1
                $d->no_hp = $user->phone; // Add missing required fields
                $d->alamat = 'Alamat Dokter ' . ($i+1); // Add missing required fields
                $d->status = 1; // Add status field
                echo $d;
                $d->save();
            }

        // If you still want random users, add them after the doctors
        User::factory(10)->create();
        $this->call(PasienSeeder::class);
        $this->call(ObatSeeder::class);

        
    }
}
