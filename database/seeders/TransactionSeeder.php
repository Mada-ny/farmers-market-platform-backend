<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    private array $productIds = [];

    public function run(): void
    {
        $operator = User::where('role', 'operator')->first();
        $farmers = Farmer::orderBy('id')->take(5)->get()->keyBy('identifier');

        if (! $operator || $farmers->isEmpty()) {
            return;
        }

        $this->productIds = Product::pluck('id', 'name')->all();

        // CI-ABJ-00001
        $f1 = $farmers['CI-ABJ-00001'];
        $this->cash($f1, $operator, [
            ['Glyphosate Herbicide (1L)', 4, 2500],
            ['Urea Fertilizer (50kg)', 2, 12000],
        ]);
        $this->cash($f1, $operator, [
            ['Hybrid Maize Seeds (5kg)', 3, 15000],
            ['Agricultural Hoe', 5, 2500],
        ]);
        $this->credit($f1, $operator, [
            ['Power Sprayer (20L)', 2, 45000],
        ]);

        // CI-ABJ-00002
        $f2 = $farmers['CI-ABJ-00002'];
        $this->cash($f2, $operator, [
            ['NPK 15-15-15 (50kg)', 6, 20000],
            ['Pyrethroid Insecticide (1L)', 4, 3200],
        ]);
        $this->cash($f2, $operator, [
            ['Tomato Seeds (500g)', 2, 5000],
            ['Hand Sprayer (5L)', 8, 3500],
        ]);
        $this->credit($f2, $operator, [
            ['Water Pump (2HP)', 1, 55000],
        ]);

        // CI-YAM-00003
        $f3 = $farmers['CI-YAM-00003'];
        $this->cash($f3, $operator, [
            ['Ammonium Nitrate (50kg)', 3, 15000],
            ['Chemical Resistant Gloves', 2, 800],
        ]);
        $this->cash($f3, $operator, [
            ['Mancozeb Fungicide (1kg)', 10, 2200],
            ['Improved Rice Seeds (10kg)', 5, 25000],
        ]);
        $this->credit($f3, $operator, [
            ['NPK 20-10-10 (50kg)', 2, 22000],
        ]);

        // CI-YAM-00004
        $f4 = $farmers['CI-YAM-00004'];
        $this->cash($f4, $operator, [
            ['Calcium Nitrate (50kg)', 5, 18000],
            ['N95 Respirator Mask', 4, 1200],
        ]);
        $this->cash($f4, $operator, [
            ['Single Super Phosphate (50kg)', 3, 10000],
            ['Safety Glasses', 6, 1500],
        ]);
        $this->credit($f4, $operator, [
            ['Muriate of Potash (50kg)', 1, 14000],
            ['Composted Manure (50kg)', 1, 3000],
        ]);

        // CI-KOR-00005
        $f5 = $farmers['CI-KOR-00005'];
        $this->cash($f5, $operator, [
            ['Sulfate of Potash (50kg)', 2, 17000],
            ['Bean Seeds (5kg)', 3, 10000],
        ]);
        $this->cash($f5, $operator, [
            ['Protective Coverall', 4, 3500],
            ['Machete', 5, 1800],
        ]);
        $this->credit($f5, $operator, [
            ['Triple Super Phosphate (50kg)', 1, 16000],
            ['Soybean Seeds (10kg)', 1, 18000],
        ]);
    }

    private function cash(Farmer $farmer, User $operator, array $lines): void
    {
        $this->makeTransaction($farmer, $operator, $lines, PaymentMethod::Cash);
    }

    private function credit(Farmer $farmer, User $operator, array $lines, float $rate = 10): void
    {
        $this->makeTransaction($farmer, $operator, $lines, PaymentMethod::Credit, $rate);
    }

    private function makeTransaction(
        Farmer $farmer,
        User $operator,
        array $lines,
        PaymentMethod $method,
        float $interestRate = 10
    ): void {
        $total = 0.0;

        foreach ($lines as [, $qty, $price]) {
            $total += $qty * $price;
        }

        $creditedAmount = null;
        $storedRate = null;

        if ($method === PaymentMethod::Credit) {
            $storedRate = $interestRate;
            $creditedAmount = round($total * (1 + $interestRate / 100), 2);
        }

        $transaction = Transaction::create([
            'farmer_id' => $farmer->id,
            'operator_id' => $operator->id,
            'total_fcfa' => $total,
            'payment_method' => $method,
            'interest_rate' => $storedRate,
            'credited_amount' => $creditedAmount,
        ]);

        foreach ($lines as [$name, $qty, $price]) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $this->productIds[$name],
                'quantity' => $qty,
                'unit_price' => $price,
            ]);
        }

        if ($method === PaymentMethod::Credit) {
            Debt::create([
                'transaction_id' => $transaction->id,
                'farmer_id' => $farmer->id,
                'amount_fcfa' => $creditedAmount,
                'remaining_amount' => $creditedAmount,
            ]);
        }
    }
}
