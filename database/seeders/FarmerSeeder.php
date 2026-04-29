<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Farmer;
use Illuminate\Database\Seeder;

class FarmerSeeder extends Seeder
{
    public function run(): void
    {
        $farmers = [
            ['identifier' => 'CI-ABJ-00001', 'firstname' => 'Kouassi',   'lastname' => 'Eba Brou',       'phone' => '+2250701234501', 'credit_limit' => 500000],
            ['identifier' => 'CI-ABJ-00002', 'firstname' => 'Adjoua',    'lastname' => 'Amenan Konan',   'phone' => '+2250701234502', 'credit_limit' => 350000],
            ['identifier' => 'CI-YAM-00003', 'firstname' => 'Bakary',    'lastname' => 'Coulibaly',      'phone' => '+2250701234503', 'credit_limit' => 600000],
            ['identifier' => 'CI-YAM-00004', 'firstname' => 'Mariam',    'lastname' => 'Konaté',         'phone' => '+2250701234504', 'credit_limit' => 250000],
            ['identifier' => 'CI-KOR-00005', 'firstname' => 'Gnénéfoly', 'lastname' => 'Silué',          'phone' => '+2250701234505', 'credit_limit' => 750000],
            ['identifier' => 'CI-KOR-00006', 'firstname' => 'Affouê',    'lastname' => 'Ahou Koffi',     'phone' => '+2250701234506', 'credit_limit' => 400000],
            ['identifier' => 'CI-DAL-00007', 'firstname' => 'Souleymane', 'lastname' => 'Traoré',         'phone' => '+2250701234507', 'credit_limit' => 1000000],
            ['identifier' => 'CI-DAL-00008', 'firstname' => 'Aminata',   'lastname' => 'Bamba',          'phone' => '+2250701234508', 'credit_limit' => 300000],
            ['identifier' => 'CI-BOU-00009', 'firstname' => 'Koffi',     'lastname' => 'N\'Dri Yao',     'phone' => '+2250701234509', 'credit_limit' => 550000],
            ['identifier' => 'CI-BOU-00010', 'firstname' => 'Fatou',     'lastname' => 'Diabaté',        'phone' => '+2250701234510', 'credit_limit' => 450000],
            ['identifier' => 'CI-MAN-00011', 'firstname' => 'Yao',       'lastname' => 'Kra Kouamé',     'phone' => '+2250701234511', 'credit_limit' => 800000],
            ['identifier' => 'CI-MAN-00012', 'firstname' => 'Awa',       'lastname' => 'Sanogo',         'phone' => '+2250701234512', 'credit_limit' => 200000],
            ['identifier' => 'CI-GAN-00013', 'firstname' => 'Kpodé',     'lastname' => 'Gbagbo Amani',   'phone' => '+2250701234513', 'credit_limit' => 650000],
            ['identifier' => 'CI-GAN-00014', 'firstname' => 'Salamata',  'lastname' => 'Ouédraogo',      'phone' => '+2250701234514', 'credit_limit' => 500000],
            ['identifier' => 'CI-ODI-00015', 'firstname' => 'N\'Goran',  'lastname' => 'Assi Kouassi',   'phone' => '+2250701234515', 'credit_limit' => 900000],
        ];

        foreach ($farmers as $data) {
            Farmer::create($data);
        }
    }
}
