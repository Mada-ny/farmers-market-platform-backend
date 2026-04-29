<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $operators = User::where('role', 'operator')->get();
        $farmers = Farmer::all();

        if ($operators->isEmpty() || $farmers->isEmpty()) {
            return;
        }

        foreach ($farmers->take(5) as $farmer) {
            Transaction::factory()->count(2)->create([
                'farmer_id' => $farmer->id,
                'operator_id' => $operators->random()->id,
            ]);

            Transaction::factory()->credit()->create([
                'farmer_id' => $farmer->id,
                'operator_id' => $operators->random()->id,
            ]);
        }
    }
}
