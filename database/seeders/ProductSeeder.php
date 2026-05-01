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
            // Pesticides - Herbicides
            ['name' => 'Glyphosate Herbicide (1L)', 'description' => 'Broad-spectrum systemic herbicide for weed control', 'price' => 2500, 'category' => 'Herbicides'],
            ['name' => 'Paraquat Herbicide (1L)', 'description' => 'Contact herbicide for non-selective weed control', 'price' => 1800, 'category' => 'Herbicides'],
            ['name' => '2,4-D Herbicide (500ml)', 'description' => 'Selective herbicide for broadleaf weed control', 'price' => 1200, 'category' => 'Herbicides'],

            // Pesticides - Insecticides
            ['name' => 'Pyrethroid Insecticide (1L)', 'description' => 'Broad-spectrum insecticide for crop protection', 'price' => 3200, 'category' => 'Insecticides'],
            ['name' => 'Neonicotinoid Insecticide (500ml)', 'description' => 'Systemic insecticide for sucking pest control', 'price' => 2800, 'category' => 'Insecticides'],
            ['name' => 'Carbaryl Insecticide (1kg)', 'description' => 'Contact insecticide for chewing insects', 'price' => 1500, 'category' => 'Insecticides'],

            // Pesticides - Fungicides
            ['name' => 'Mancozeb Fungicide (1kg)', 'description' => 'Protective fungicide for disease prevention', 'price' => 2200, 'category' => 'Fungicides'],
            ['name' => 'Copper Fungicide (1kg)', 'description' => 'Broad-spectrum fungicide for fungal diseases', 'price' => 1800, 'category' => 'Fungicides'],
            ['name' => 'Systemic Fungicide (500ml)', 'description' => 'Systemic treatment for fungal infections', 'price' => 3500, 'category' => 'Fungicides'],

            // Fertilizers - Nitrogen
            ['name' => 'Urea Fertilizer (50kg)', 'description' => 'High nitrogen fertilizer (46% N) for rapid growth', 'price' => 12000, 'category' => 'Nitrogen Fertilizers'],
            ['name' => 'Ammonium Nitrate (50kg)', 'description' => 'Nitrogen fertilizer (34% N) for vegetative growth', 'price' => 15000, 'category' => 'Nitrogen Fertilizers'],
            ['name' => 'Calcium Nitrate (50kg)', 'description' => 'Nitrogen and calcium fertilizer for quality produce', 'price' => 18000, 'category' => 'Nitrogen Fertilizers'],

            // Fertilizers - Phosphorus
            ['name' => 'Single Super Phosphate (50kg)', 'description' => 'Phosphorus fertilizer (18% P2O5) for root development', 'price' => 10000, 'category' => 'Phosphorus Fertilizers'],
            ['name' => 'Triple Super Phosphate (50kg)', 'description' => 'High phosphorus fertilizer (45% P2O5)', 'price' => 16000, 'category' => 'Phosphorus Fertilizers'],

            // Fertilizers - Potassium
            ['name' => 'Muriate of Potash (50kg)', 'description' => 'Potassium fertilizer (60% K2O) for fruit quality', 'price' => 14000, 'category' => 'Potassium Fertilizers'],
            ['name' => 'Sulfate of Potash (50kg)', 'description' => 'Potassium and sulfur fertilizer for balanced nutrition', 'price' => 17000, 'category' => 'Potassium Fertilizers'],

            // Fertilizers - Compound
            ['name' => 'NPK 15-15-15 (50kg)', 'description' => 'Balanced compound fertilizer for general use', 'price' => 20000, 'category' => 'Compound Fertilizers'],
            ['name' => 'NPK 20-10-10 (50kg)', 'description' => 'High nitrogen compound fertilizer for vegetative growth', 'price' => 22000, 'category' => 'Compound Fertilizers'],
            ['name' => 'NPK 12-24-12 (50kg)', 'description' => 'High phosphorus compound fertilizer for root development', 'price' => 21000, 'category' => 'Compound Fertilizers'],

            // Fertilizers - Organic
            ['name' => 'Composted Manure (50kg)', 'description' => 'Organic fertilizer rich in nutrients and organic matter', 'price' => 3000, 'category' => 'Organic Fertilizers'],
            ['name' => 'Bone Meal (25kg)', 'description' => 'Organic phosphorus source for slow release nutrition', 'price' => 8000, 'category' => 'Organic Fertilizers'],

            // Seeds - Cereal
            ['name' => 'Hybrid Maize Seeds (5kg)', 'description' => 'High yield hybrid maize seeds for season planting', 'price' => 15000, 'category' => 'Cereal Seeds'],
            ['name' => 'Improved Rice Seeds (10kg)', 'description' => 'Disease resistant rice variety for high yield', 'price' => 25000, 'category' => 'Cereal Seeds'],
            ['name' => 'Sorghum Seeds (5kg)', 'description' => 'Drought tolerant sorghum seeds for arid regions', 'price' => 8000, 'category' => 'Cereal Seeds'],

            // Seeds - Vegetable
            ['name' => 'Tomato Seeds (500g)', 'description' => 'Hybrid tomato seeds for high yield production', 'price' => 5000, 'category' => 'Vegetable Seeds'],
            ['name' => 'Pepper Seeds (200g)', 'description' => 'Hot pepper variety for commercial production', 'price' => 3000, 'category' => 'Vegetable Seeds'],
            ['name' => 'Onion Seeds (1kg)', 'description' => 'Improved onion variety for better storage', 'price' => 12000, 'category' => 'Vegetable Seeds'],

            // Seeds - Legume
            ['name' => 'Bean Seeds (5kg)', 'description' => 'High protein bean variety for market production', 'price' => 10000, 'category' => 'Legume Seeds'],
            ['name' => 'Soybean Seeds (10kg)', 'description' => 'Improved soybean variety for oil production', 'price' => 18000, 'category' => 'Legume Seeds'],

            // Tools - Hand Tools
            ['name' => 'Agricultural Hoe', 'description' => 'Heavy duty hoe for soil preparation and weeding', 'price' => 2500, 'category' => 'Hand Tools'],
            ['name' => 'Machete', 'description' => 'Sharp cutting tool for clearing and harvesting', 'price' => 1800, 'category' => 'Hand Tools'],
            ['name' => 'Hand Sprayer (5L)', 'description' => 'Manual sprayer for pesticide application', 'price' => 3500, 'category' => 'Hand Tools'],

            // Tools - Power Tools
            ['name' => 'Power Sprayer (20L)', 'description' => 'Motorized sprayer for large area pesticide application', 'price' => 45000, 'category' => 'Power Tools'],
            ['name' => 'Water Pump (2HP)', 'description' => 'Electric water pump for irrigation', 'price' => 55000, 'category' => 'Power Tools'],

            // Protective Equipment
            ['name' => 'Chemical Resistant Gloves', 'description' => 'Nitrile gloves for pesticide handling', 'price' => 800, 'category' => 'Gloves'],
            ['name' => 'N95 Respirator Mask', 'description' => 'Protective mask for chemical application', 'price' => 1200, 'category' => 'Masks'],
            ['name' => 'Safety Glasses', 'description' => 'UV protection safety glasses for field work', 'price' => 1500, 'category' => 'Safety Glasses'],
            ['name' => 'Protective Coverall', 'description' => 'Chemical resistant protective suit', 'price' => 3500, 'category' => 'Protective Clothing'],
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
