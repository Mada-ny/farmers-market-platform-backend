<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $pesticides = Category::create(['name' => 'Pesticides']);
        Category::create(['name' => 'Herbicides', 'parent_id' => $pesticides->id]);
        Category::create(['name' => 'Insecticides', 'parent_id' => $pesticides->id]);
        Category::create(['name' => 'Fungicides', 'parent_id' => $pesticides->id]);
        Category::create(['name' => 'Rodenticides', 'parent_id' => $pesticides->id]);

        $fertilizers = Category::create(['name' => 'Fertilizers']);
        Category::create(['name' => 'Nitrogen Fertilizers', 'parent_id' => $fertilizers->id]);
        Category::create(['name' => 'Phosphorus Fertilizers', 'parent_id' => $fertilizers->id]);
        Category::create(['name' => 'Potassium Fertilizers', 'parent_id' => $fertilizers->id]);
        Category::create(['name' => 'Compound Fertilizers', 'parent_id' => $fertilizers->id]);
        Category::create(['name' => 'Organic Fertilizers', 'parent_id' => $fertilizers->id]);

        $seeds = Category::create(['name' => 'Seeds']);
        Category::create(['name' => 'Cereal Seeds', 'parent_id' => $seeds->id]);
        Category::create(['name' => 'Vegetable Seeds', 'parent_id' => $seeds->id]);
        Category::create(['name' => 'Legume Seeds', 'parent_id' => $seeds->id]);
        Category::create(['name' => 'Fruit Seeds', 'parent_id' => $seeds->id]);

        $tools = Category::create(['name' => 'Farm Tools']);
        Category::create(['name' => 'Hand Tools', 'parent_id' => $tools->id]);
        Category::create(['name' => 'Power Tools', 'parent_id' => $tools->id]);
        Category::create(['name' => 'Irrigation Equipment', 'parent_id' => $tools->id]);

        $equipment = Category::create(['name' => 'Protective Equipment']);
        Category::create(['name' => 'Gloves', 'parent_id' => $equipment->id]);
        Category::create(['name' => 'Masks', 'parent_id' => $equipment->id]);
        Category::create(['name' => 'Safety Glasses', 'parent_id' => $equipment->id]);
        Category::create(['name' => 'Protective Clothing', 'parent_id' => $equipment->id]);
    }
}
