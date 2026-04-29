<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cereales = Category::create(['name' => 'Céréales']);
        Category::create(['name' => 'Maïs',        'parent_id' => $cereales->id]);
        Category::create(['name' => 'Riz local',    'parent_id' => $cereales->id]);
        Category::create(['name' => 'Mil',          'parent_id' => $cereales->id]);
        Category::create(['name' => 'Sorgho',       'parent_id' => $cereales->id]);

        $tubercules = Category::create(['name' => 'Tubercules et racines']);
        Category::create(['name' => 'Igname',       'parent_id' => $tubercules->id]);
        Category::create(['name' => 'Manioc',       'parent_id' => $tubercules->id]);
        Category::create(['name' => 'Patate douce', 'parent_id' => $tubercules->id]);
        Category::create(['name' => 'Taro',         'parent_id' => $tubercules->id]);

        $legumes = Category::create(['name' => 'Légumes']);
        Category::create(['name' => 'Tomate',       'parent_id' => $legumes->id]);
        Category::create(['name' => 'Gombo',        'parent_id' => $legumes->id]);
        Category::create(['name' => 'Aubergine',    'parent_id' => $legumes->id]);
        Category::create(['name' => 'Piment',       'parent_id' => $legumes->id]);
        Category::create(['name' => 'Oignon',       'parent_id' => $legumes->id]);

        $fruits = Category::create(['name' => 'Fruits']);
        Category::create(['name' => 'Plantain',     'parent_id' => $fruits->id]);
        Category::create(['name' => 'Banane douce', 'parent_id' => $fruits->id]);
        Category::create(['name' => 'Ananas',       'parent_id' => $fruits->id]);
        Category::create(['name' => 'Papaye',       'parent_id' => $fruits->id]);
        Category::create(['name' => 'Mangue',       'parent_id' => $fruits->id]);

        $rente = Category::create(['name' => 'Cultures de rente']);
        Category::create(['name' => 'Cacao',        'parent_id' => $rente->id]);
        Category::create(['name' => 'Café',         'parent_id' => $rente->id]);
        Category::create(['name' => 'Anacarde',     'parent_id' => $rente->id]);
        Category::create(['name' => 'Hévéa',        'parent_id' => $rente->id]);

        $legumineuses = Category::create(['name' => 'Légumineuses']);
        Category::create(['name' => 'Arachide',     'parent_id' => $legumineuses->id]);
        Category::create(['name' => 'Niébé',        'parent_id' => $legumineuses->id]);
        Category::create(['name' => 'Soja',         'parent_id' => $legumineuses->id]);
    }
}
