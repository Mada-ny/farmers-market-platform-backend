<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $byName = Category::pluck('id', 'name');

        $products = [
            // Céréales
            ['name' => 'Maïs blanc (sac 50 kg)',        'description' => 'Maïs blanc séché, variété locale, sac de 50 kg.',                        'price' => 7500,  'category' => 'Maïs'],
            ['name' => 'Riz paddy local (sac 50 kg)',   'description' => 'Riz paddy non décortiqué, production du centre-ouest.',                   'price' => 14000, 'category' => 'Riz local'],
            ['name' => 'Mil en grains (sac 25 kg)',     'description' => 'Mil perlé séché, idéal pour la fabrication de bouillie.',                 'price' => 5500,  'category' => 'Mil'],
            ['name' => 'Sorgho rouge (sac 25 kg)',      'description' => 'Sorgho rouge grain sec, culture nordique.',                               'price' => 5000,  'category' => 'Sorgho'],

            // Tubercules et racines
            ['name' => 'Igname Kponan (tas 10 kg)',     'description' => "Igname Kponan fraîche, variété la plus prisée en Côte d'Ivoire.",        'price' => 3500,  'category' => 'Igname'],
            ['name' => 'Igname Florido (tas 10 kg)',    'description' => 'Igname Florido, chair ferme, bonne conservation.',                        'price' => 2800,  'category' => 'Igname'],
            ['name' => 'Manioc doux (tas 10 kg)',       'description' => 'Manioc doux frais, récolte de la semaine.',                               'price' => 1200,  'category' => 'Manioc'],
            ['name' => 'Patate douce orange (sac 25 kg)', 'description' => 'Patate douce à chair orange, riche en bêta-carotène.',                 'price' => 4500,  'category' => 'Patate douce'],
            ['name' => 'Taro (colocase) 10 kg',        'description' => 'Colocase blanche, production de la région des Lacs.',                     'price' => 2500,  'category' => 'Taro'],

            // Légumes
            ['name' => 'Tomate locale (caisse 5 kg)',   'description' => 'Tomates rondes fraîches, cultivées sans intrants chimiques.',             'price' => 2000,  'category' => 'Tomate'],
            ['name' => 'Gombo frais (botte 1 kg)',      'description' => 'Gombo vert frais récolté le matin même.',                                 'price' => 600,   'category' => 'Gombo'],
            ['name' => 'Aubergine locale (kg)',         'description' => 'Aubergine africaine ronde, idéale pour la sauce graine et le kedjenou.',  'price' => 350,   'category' => 'Aubergine'],
            ['name' => 'Piment fort (botte 500 g)',     'description' => 'Piment végétarien et piment fort mélangé, récolte fraîche.',              'price' => 500,   'category' => 'Piment'],
            ['name' => 'Oignon rouge (filet 10 kg)',    'description' => 'Oignon rouge séché, bonne conservation, production locale.',              'price' => 3000,  'category' => 'Oignon'],

            // Fruits
            ['name' => 'Régime de plantain (environ 15 kg)', 'description' => 'Plantain mûr à point pour la friture ou la cuisson.',               'price' => 2500,  'category' => 'Plantain'],
            ['name' => 'Banane douce (régime 10 kg)',   'description' => 'Banane dessert sucrée, variété Gros Michel.',                             'price' => 1800,  'category' => 'Banane douce'],
            ['name' => 'Ananas Pain de sucre (pièce)', 'description' => "Ananas Pain de sucre, spécialité de Côte d'Ivoire, très sucré.",         'price' => 400,   'category' => 'Ananas'],
            ['name' => 'Papaye (pièce 1–2 kg)',         'description' => 'Papaye rouge mûre, chair ferme et sucrée.',                               'price' => 350,   'category' => 'Papaye'],
            ['name' => 'Mangue Kent (kg)',              'description' => 'Mangue Kent charnue, faible teneur en fibres, excellente à la saison.',   'price' => 300,   'category' => 'Mangue'],

            // Cultures de rente
            ['name' => 'Cacao fermenté séché (sac 60 kg)', 'description' => 'Fèves de cacao grade 1, bien fermentées et séchées au soleil.',      'price' => 90000, 'category' => 'Cacao'],
            ['name' => 'Café robusta (sac 60 kg)',      'description' => 'Café robusta vert, humidité < 12 %, récolte de la saison.',               'price' => 75000, 'category' => 'Café'],
            ['name' => 'Noix de cajou brutes (sac 80 kg)', 'description' => 'Noix de cajou brutes, humidité 8–10 %, taux de rendement KOR 48.',    'price' => 56000, 'category' => 'Anacarde'],
            ['name' => 'Latex coagulé (cup lump 1 kg)', 'description' => 'Latex hévéa coagulé en cup lump, DRC 50–55 %.',                         'price' => 600,   'category' => 'Hévéa'],

            // Légumineuses
            ['name' => 'Arachide décortiquée (sac 50 kg)', 'description' => 'Arachide blanche décortiquée, séchée, grade export.',                'price' => 35000, 'category' => 'Arachide'],
            ['name' => 'Niébé blanc (sac 25 kg)',       'description' => 'Niébé blanc à œil noir, séché et trié, production du nord.',             'price' => 15000, 'category' => 'Niébé'],
            ['name' => 'Soja graine (sac 50 kg)',       'description' => 'Soja grain séché, humidité < 13 %, destination huilerie ou alimentation.', 'price' => 22000, 'category' => 'Soja'],
        ];

        foreach ($products as $data) {
            Product::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'category_id' => $byName[$data['category']],
            ]);
        }
    }
}
