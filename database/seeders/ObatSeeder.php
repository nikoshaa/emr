<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\User;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $obats = [
        [
            'kd_obat' => 'U001',
            'nama' => 'Paracetamol',
            'satuan' => 'Tablet',
            'stok' => 50,
            'harga' => 2000,
            'poli_id' => 1
        ],
        ['kd_obat' => 'U002', 'nama' => 'Ibuprofen', 'satuan' => 'Tablet', 'stok' => 52, 'harga' => 2500, 'poli_id' => 1],
            ['kd_obat' => 'U003', 'nama' => 'Vitamin C', 'satuan' => 'Tablet', 'stok' => 54, 'harga' => 3000, 'poli_id' => 1],
            ['kd_obat' => 'U004', 'nama' => 'Cetirizine', 'satuan' => 'Tablet', 'stok' => 56, 'harga' => 3500, 'poli_id' => 1],
            ['kd_obat' => 'U005', 'nama' => 'Amoxicillin', 'satuan' => 'Tablet', 'stok' => 58, 'harga' => 4000, 'poli_id' => 1],
            ['kd_obat' => 'U006', 'nama' => 'Metformin', 'satuan' => 'Tablet', 'stok' => 60, 'harga' => 4500, 'poli_id' => 1],
            ['kd_obat' => 'U007', 'nama' => 'Simvastatin', 'satuan' => 'Tablet', 'stok' => 62, 'harga' => 5000, 'poli_id' => 1],
            ['kd_obat' => 'U008', 'nama' => 'Omeprazole', 'satuan' => 'Tablet', 'stok' => 64, 'harga' => 5500, 'poli_id' => 1],
            ['kd_obat' => 'U009', 'nama' => 'Ranitidine', 'satuan' => 'Tablet', 'stok' => 66, 'harga' => 6000, 'poli_id' => 1],
            ['kd_obat' => 'U010', 'nama' => 'Dexamethasone', 'satuan' => 'Tablet', 'stok' => 68, 'harga' => 6500, 'poli_id' => 1],
            ['kd_obat' => 'U011', 'nama' => 'Loratadine', 'satuan' => 'Tablet', 'stok' => 70, 'harga' => 7000, 'poli_id' => 1],
            ['kd_obat' => 'U012', 'nama' => 'Salbutamol', 'satuan' => 'Tablet', 'stok' => 72, 'harga' => 7500, 'poli_id' => 1],
            ['kd_obat' => 'U013', 'nama' => 'Loperamide', 'satuan' => 'Tablet', 'stok' => 74, 'harga' => 8000, 'poli_id' => 1],
            ['kd_obat' => 'U014', 'nama' => 'Zinc Sulfate', 'satuan' => 'Tablet', 'stok' => 76, 'harga' => 8500, 'poli_id' => 1],
            ['kd_obat' => 'U015', 'nama' => 'Acetylcysteine_U', 'satuan' => 'Tablet', 'stok' => 78, 'harga' => 9000, 'poli_id' => 1],
            ['kd_obat' => 'U016', 'nama' => 'Mefenamic Acid', 'satuan' => 'Tablet', 'stok' => 80, 'harga' => 9500, 'poli_id' => 1],
            ['kd_obat' => 'U017', 'nama' => 'Codeine', 'satuan' => 'Tablet', 'stok' => 82, 'harga' => 10000, 'poli_id' => 1],
            ['kd_obat' => 'U018', 'nama' => 'Chlorpheniramine', 'satuan' => 'Tablet', 'stok' => 84, 'harga' => 10500, 'poli_id' => 1],
            ['kd_obat' => 'U019', 'nama' => 'Diclofenac', 'satuan' => 'Tablet', 'stok' => 86, 'harga' => 11000, 'poli_id' => 1],
            ['kd_obat' => 'U020', 'nama' => 'Metoclopramide', 'satuan' => 'Tablet', 'stok' => 88, 'harga' => 11500, 'poli_id' => 1],
            ['kd_obat' => 'G001', 'nama' => 'Amoxicillin_G', 'satuan' => 'Tablet', 'stok' => 50, 'harga' => 2000, 'poli_id' => 2],
            ['kd_obat' => 'G002', 'nama' => 'Metronidazole', 'satuan' => 'Tablet', 'stok' => 52, 'harga' => 2500, 'poli_id' => 2],
            ['kd_obat' => 'G003', 'nama' => 'Chlorhexidine', 'satuan' => 'Tablet', 'stok' => 54, 'harga' => 3000, 'poli_id' => 2],
            ['kd_obat' => 'G004', 'nama' => 'Ibuprofen_G', 'satuan' => 'Tablet', 'stok' => 56, 'harga' => 3500, 'poli_id' => 2],
            ['kd_obat' => 'G005', 'nama' => 'Acetaminophen', 'satuan' => 'Tablet', 'stok' => 58, 'harga' => 4000, 'poli_id' => 2],
            ['kd_obat' => 'G006', 'nama' => 'Dexamethasone_G', 'satuan' => 'Tablet', 'stok' => 60, 'harga' => 4500, 'poli_id' => 2],
            ['kd_obat' => 'G007', 'nama' => 'Lidocaine Gel', 'satuan' => 'Tablet', 'stok' => 62, 'harga' => 5000, 'poli_id' => 2],
            ['kd_obat' => 'G008', 'nama' => 'Clindamycin_G', 'satuan' => 'Tablet', 'stok' => 64, 'harga' => 5500, 'poli_id' => 2],
            ['kd_obat' => 'G009', 'nama' => 'Naproxen', 'satuan' => 'Tablet', 'stok' => 66, 'harga' => 6000, 'poli_id' => 2],
            ['kd_obat' => 'G010', 'nama' => 'Prednisolone', 'satuan' => 'Tablet', 'stok' => 68, 'harga' => 6500, 'poli_id' => 2],
            ['kd_obat' => 'G011', 'nama' => 'Miconazole_G', 'satuan' => 'Tablet', 'stok' => 70, 'harga' => 7000, 'poli_id' => 2],
            ['kd_obat' => 'G012', 'nama' => 'Chloramphenicol', 'satuan' => 'Tablet', 'stok' => 72, 'harga' => 7500, 'poli_id' => 2],
            ['kd_obat' => 'G013', 'nama' => 'Calcium Hydroxide', 'satuan' => 'Tablet', 'stok' => 74, 'harga' => 8000, 'poli_id' => 2],
            ['kd_obat' => 'G014', 'nama' => 'Eugenol', 'satuan' => 'Tablet', 'stok' => 76, 'harga' => 8500, 'poli_id' => 2],
            ['kd_obat' => 'G015', 'nama' => 'Tetracycline', 'satuan' => 'Tablet', 'stok' => 78, 'harga' => 9000, 'poli_id' => 2],
            ['kd_obat' => 'G016', 'nama' => 'Diazepam', 'satuan' => 'Tablet', 'stok' => 80, 'harga' => 9500, 'poli_id' => 2],
            ['kd_obat' => 'G017', 'nama' => 'Chlorpheniramine_G', 'satuan' => 'Tablet', 'stok' => 82, 'harga' => 10000, 'poli_id' => 2],
            ['kd_obat' => 'G018', 'nama' => 'Naloxone', 'satuan' => 'Tablet', 'stok' => 84, 'harga' => 10500, 'poli_id' => 2],
            ['kd_obat' => 'G019', 'nama' => 'Hydrocortisone', 'satuan' => 'Tablet', 'stok' => 86, 'harga' => 11000, 'poli_id' => 2],
            ['kd_obat' => 'G020', 'nama' => 'Ketorolac', 'satuan' => 'Tablet', 'stok' => 88, 'harga' => 11500, 'poli_id' => 2],
            ['kd_obat' => 'A001', 'nama' => 'Paracetamol Susp', 'satuan' => 'Sirup', 'stok' => 50, 'harga' => 2000, 'poli_id' => 3],
        ['kd_obat' => 'A002', 'nama' => 'Ibuprofen Susp', 'satuan' => 'Sirup', 'stok' => 52, 'harga' => 2500, 'poli_id' => 3],
        ['kd_obat' => 'A003', 'nama' => 'Amoxicillin Susp', 'satuan' => 'Sirup', 'stok' => 54, 'harga' => 3000, 'poli_id' => 3],
        ['kd_obat' => 'A004', 'nama' => 'Salbutamol Inhaler', 'satuan' => 'Tablet', 'stok' => 56, 'harga' => 3500, 'poli_id' => 3],
        ['kd_obat' => 'A005', 'nama' => 'Cetirizine Juniors', 'satuan' => 'Tablet', 'stok' => 58, 'harga' => 4000, 'poli_id' => 3],
        ['kd_obat' => 'A006', 'nama' => 'Loratadine Susp', 'satuan' => 'Sirup', 'stok' => 60, 'harga' => 4500, 'poli_id' => 3],
        ['kd_obat' => 'A007', 'nama' => 'Oralit', 'satuan' => 'Tablet', 'stok' => 62, 'harga' => 5000, 'poli_id' => 3],
        ['kd_obat' => 'A008', 'nama' => 'Zinc Sulfate Junior', 'satuan' => 'Tablet', 'stok' => 64, 'harga' => 5500, 'poli_id' => 3],
        ['kd_obat' => 'A009', 'nama' => 'Dextromethorphan', 'satuan' => 'Tablet', 'stok' => 66, 'harga' => 6000, 'poli_id' => 3],
        ['kd_obat' => 'A010', 'nama' => 'Chlorpheniramine Susp', 'satuan' => 'Sirup', 'stok' => 68, 'harga' => 6500, 'poli_id' => 3],
        ['kd_obat' => 'A011', 'nama' => 'Azithromycin Susp', 'satuan' => 'Sirup', 'stok' => 70, 'harga' => 7000, 'poli_id' => 3],
        ['kd_obat' => 'A012', 'nama' => 'Mebendazole Susp', 'satuan' => 'Sirup', 'stok' => 72, 'harga' => 7500, 'poli_id' => 3],
        ['kd_obat' => 'A013', 'nama' => 'Calcium Carbonate', 'satuan' => 'Tablet', 'stok' => 74, 'harga' => 8000, 'poli_id' => 3],
        ['kd_obat' => 'A014', 'nama' => 'Vitamin D3', 'satuan' => 'Tablet', 'stok' => 76, 'harga' => 8500, 'poli_id' => 3],
        ['kd_obat' => 'A015', 'nama' => 'Multivitamin Kids', 'satuan' => 'Tablet', 'stok' => 78, 'harga' => 9000, 'poli_id' => 3],
        ['kd_obat' => 'A016', 'nama' => 'Ranitidine Susp', 'satuan' => 'Sirup', 'stok' => 80, 'harga' => 9500, 'poli_id' => 3],
        ['kd_obat' => 'A017', 'nama' => 'Ondansetron Susp', 'satuan' => 'Sirup', 'stok' => 82, 'harga' => 10000, 'poli_id' => 3],
        ['kd_obat' => 'A018', 'nama' => 'Clavulanate Susp', 'satuan' => 'Sirup', 'stok' => 84, 'harga' => 10500, 'poli_id' => 3],
        ['kd_obat' => 'A019', 'nama' => 'Propanolol', 'satuan' => 'Tablet', 'stok' => 86, 'harga' => 11000, 'poli_id' => 3],
        ['kd_obat' => 'A020', 'nama' => 'Fexofenadine', 'satuan' => 'Tablet', 'stok' => 88, 'harga' => 11500, 'poli_id' => 3],

        // Poli Penyakit Dalam (poli_id = 4)
        ['kd_obat' => 'D001', 'nama' => 'Metformin_D', 'satuan' => 'Tablet', 'stok' => 50, 'harga' => 2000, 'poli_id' => 4],
        ['kd_obat' => 'D002', 'nama' => 'Glibenclamide', 'satuan' => 'Tablet', 'stok' => 52, 'harga' => 2500, 'poli_id' => 4],
        ['kd_obat' => 'D003', 'nama' => 'Lisinopril', 'satuan' => 'Tablet', 'stok' => 54, 'harga' => 3000, 'poli_id' => 4],
        ['kd_obat' => 'D004', 'nama' => 'Amlodipine', 'satuan' => 'Tablet', 'stok' => 56, 'harga' => 3500, 'poli_id' => 4],
        ['kd_obat' => 'D005', 'nama' => 'Atorvastatin', 'satuan' => 'Tablet', 'stok' => 58, 'harga' => 4000, 'poli_id' => 4],
        ['kd_obat' => 'D006', 'nama' => 'Simvastatin_D', 'satuan' => 'Tablet', 'stok' => 60, 'harga' => 4500, 'poli_id' => 4],
        ['kd_obat' => 'D007', 'nama' => 'Warfarin', 'satuan' => 'Tablet', 'stok' => 62, 'harga' => 5000, 'poli_id' => 4],
        ['kd_obat' => 'D008', 'nama' => 'Rivaroxaban', 'satuan' => 'Tablet', 'stok' => 64, 'harga' => 5500, 'poli_id' => 4],
        ['kd_obat' => 'D009', 'nama' => 'Furosemide', 'satuan' => 'Tablet', 'stok' => 66, 'harga' => 6000, 'poli_id' => 4],
        ['kd_obat' => 'D010', 'nama' => 'Spironolactone', 'satuan' => 'Tablet', 'stok' => 68, 'harga' => 6500, 'poli_id' => 4],
        ['kd_obat' => 'D011', 'nama' => 'Omeprazole_D', 'satuan' => 'Tablet', 'stok' => 70, 'harga' => 7000, 'poli_id' => 4],
        ['kd_obat' => 'D012', 'nama' => 'Pantoprazole', 'satuan' => 'Tablet', 'stok' => 72, 'harga' => 7500, 'poli_id' => 4],
        ['kd_obat' => 'D013', 'nama' => 'Metoclopramide_D', 'satuan' => 'Tablet', 'stok' => 74, 'harga' => 8000, 'poli_id' => 4],
        ['kd_obat' => 'D014', 'nama' => 'Prednisone', 'satuan' => 'Tablet', 'stok' => 76, 'harga' => 8500, 'poli_id' => 4],
        ['kd_obat' => 'D015', 'nama' => 'Levothyroxine', 'satuan' => 'Tablet', 'stok' => 78, 'harga' => 9000, 'poli_id' => 4],
        ['kd_obat' => 'D016', 'nama' => 'Methimazole', 'satuan' => 'Tablet', 'stok' => 80, 'harga' => 9500, 'poli_id' => 4],
        ['kd_obat' => 'D017', 'nama' => 'Allopurinol', 'satuan' => 'Tablet', 'stok' => 82, 'harga' => 10000, 'poli_id' => 4],
        ['kd_obat' => 'D018', 'nama' => 'Acetylcysteine', 'satuan' => 'Tablet', 'stok' => 84, 'harga' => 10500, 'poli_id' => 4],
        ['kd_obat' => 'D019', 'nama' => 'Enalapril', 'satuan' => 'Tablet', 'stok' => 86, 'harga' => 11000, 'poli_id' => 4],
        ['kd_obat' => 'D020', 'nama' => 'Clopidogrel', 'satuan' => 'Tablet', 'stok' => 88, 'harga' => 11500, 'poli_id' => 4],

        // Poli Kulit & Kelamin (poli_id = 5)
        ['kd_obat' => 'K001', 'nama' => 'Clotrimazole', 'satuan' => 'Tablet', 'stok' => 50, 'harga' => 2000, 'poli_id' => 5],
        ['kd_obat' => 'K002', 'nama' => 'Miconazole', 'satuan' => 'Tablet', 'stok' => 52, 'harga' => 2500, 'poli_id' => 5],
        ['kd_obat' => 'K003', 'nama' => 'Hydrocortisone_K', 'satuan' => 'Tablet', 'stok' => 54, 'harga' => 3000, 'poli_id' => 5],
        ['kd_obat' => 'K004', 'nama' => 'Betamethasone', 'satuan' => 'Tablet', 'stok' => 56, 'harga' => 3500, 'poli_id' => 5],
        ['kd_obat' => 'K005', 'nama' => 'Tacrolimus', 'satuan' => 'Tablet', 'stok' => 58, 'harga' => 4000, 'poli_id' => 5],
        ['kd_obat' => 'K006', 'nama' => 'Clindamycin', 'satuan' => 'Tablet', 'stok' => 60, 'harga' => 4500, 'poli_id' => 5],
        ['kd_obat' => 'K007', 'nama' => 'Erythromycin', 'satuan' => 'Tablet', 'stok' => 62, 'harga' => 5000, 'poli_id' => 5],
        ['kd_obat' => 'K008', 'nama' => 'Fluconazole', 'satuan' => 'Tablet', 'stok' => 64, 'harga' => 5500, 'poli_id' => 5],
        ['kd_obat' => 'K009', 'nama' => 'Acyclovir', 'satuan' => 'Tablet', 'stok' => 66, 'harga' => 6000, 'poli_id' => 5],
        ['kd_obat' => 'K010', 'nama' => 'Permethrin', 'satuan' => 'Tablet', 'stok' => 68, 'harga' => 6500, 'poli_id' => 5],
        ['kd_obat' => 'K011', 'nama' => 'Tretinoin', 'satuan' => 'Tablet', 'stok' => 70, 'harga' => 7000, 'poli_id' => 5],
        ['kd_obat' => 'K012', 'nama' => 'Isotretinoin', 'satuan' => 'Tablet', 'stok' => 72, 'harga' => 7500, 'poli_id' => 5],
        ['kd_obat' => 'K013', 'nama' => 'Benzoyl Peroxide', 'satuan' => 'Tablet', 'stok' => 74, 'harga' => 8000, 'poli_id' => 5],
        ['kd_obat' => 'K014', 'nama' => 'Salicylic Acid', 'satuan' => 'Tablet', 'stok' => 76, 'harga' => 8500, 'poli_id' => 5],
        ['kd_obat' => 'K015', 'nama' => 'Ketoconazole', 'satuan' => 'Tablet', 'stok' => 78, 'harga' => 9000, 'poli_id' => 5],
        ['kd_obat' => 'K016', 'nama' => 'Sulfur Ointment', 'satuan' => 'Tablet', 'stok' => 80, 'harga' => 9500, 'poli_id' => 5],
        ['kd_obat' => 'K017', 'nama' => 'Azelaic Acid', 'satuan' => 'Tablet', 'stok' => 82, 'harga' => 10000, 'poli_id' => 5],
        ['kd_obat' => 'K018', 'nama' => 'Hydrocortisone Acetate', 'satuan' => 'Tablet', 'stok' => 84, 'harga' => 10500, 'poli_id' => 5],
        ['kd_obat' => 'K019', 'nama' => 'Clobetasol Propionate', 'satuan' => 'Tablet', 'stok' => 86, 'harga' => 11000, 'poli_id' => 5],
        ['kd_obat' => 'K020', 'nama' => 'Doxycycline', 'satuan' => 'Tablet', 'stok' => 88, 'harga' => 11500, 'poli_id' => 5],
    ];
    foreach ($obats as $obat) {
        $newObat = new Obat();
        $newObat->kd_obat = $obat['kd_obat'];
        $newObat->nama = $obat['nama'];
        $newObat->satuan = $obat['satuan'];
        $newObat->stok = $obat['stok'];
        $newObat->harga = $obat['harga'];
        $newObat->poli_id = $obat['poli_id'];
        $newObat->save();
    }
    }
}
