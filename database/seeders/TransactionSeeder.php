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
            ['Maïs blanc (sac 50 kg)', 4, 7500],
            ['Tomate locale (caisse 5 kg)', 2, 2000],
        ]);
        $this->cash($f1, $operator, [
            ['Igname Kponan (tas 10 kg)', 3, 3500],
            ['Gombo frais (botte 1 kg)', 5, 600],
        ]);
        $this->credit($f1, $operator, [
            ['Noix de cajou brutes (sac 80 kg)', 2, 56000],
        ]);

        // CI-ABJ-00002
        $f2 = $farmers['CI-ABJ-00002'];
        $this->cash($f2, $operator, [
            ['Manioc doux (tas 10 kg)', 6, 1200],
            ['Régime de plantain (environ 15 kg)', 4, 2500],
        ]);
        $this->cash($f2, $operator, [
            ['Patate douce orange (sac 25 kg)', 2, 4500],
            ['Aubergine locale (kg)', 8, 350],
        ]);
        $this->credit($f2, $operator, [
            ['Cacao fermenté séché (sac 60 kg)', 1, 90000],
        ]);

        // CI-YAM-00003
        $f3 = $farmers['CI-YAM-00003'];
        $this->cash($f3, $operator, [
            ['Riz paddy local (sac 50 kg)', 3, 14000],
            ['Oignon rouge (filet 10 kg)', 2, 3000],
        ]);
        $this->cash($f3, $operator, [
            ['Ananas Pain de sucre (pièce)', 10, 400],
            ['Banane douce (régime 10 kg)', 5, 1800],
        ]);
        $this->credit($f3, $operator, [
            ['Café robusta (sac 60 kg)', 2, 75000],
        ]);

        // CI-YAM-00004
        $f4 = $farmers['CI-YAM-00004'];
        $this->cash($f4, $operator, [
            ['Mil en grains (sac 25 kg)', 5, 5500],
            ['Piment fort (botte 500 g)', 4, 500],
        ]);
        $this->cash($f4, $operator, [
            ['Taro (colocase) 10 kg', 3, 2500],
            ['Papaye (pièce 1–2 kg)', 6, 350],
        ]);
        $this->credit($f4, $operator, [
            ['Arachide décortiquée (sac 50 kg)', 1, 35000],
            ['Niébé blanc (sac 25 kg)', 1, 15000],
        ]);

        // CI-KOR-00005
        $f5 = $farmers['CI-KOR-00005'];
        $this->cash($f5, $operator, [
            ['Soja graine (sac 50 kg)', 2, 22000],
            ['Igname Florido (tas 10 kg)', 3, 2800],
        ]);
        $this->cash($f5, $operator, [
            ['Mangue Kent (kg)', 4, 300],
            ['Gombo frais (botte 1 kg)', 5, 600],
        ]);
        $this->credit($f5, $operator, [
            ['Noix de cajou brutes (sac 80 kg)', 1, 56000],
            ['Arachide décortiquée (sac 50 kg)', 1, 35000],
        ]);
    }

    private function cash(Farmer $farmer, User $operator, array $lines): void
    {
        $this->makeTransaction($farmer, $operator, $lines, PaymentMethod::Cash);
    }

    private function credit(Farmer $farmer, User $operator, array $lines, float $rate = 0.10): void
    {
        $this->makeTransaction($farmer, $operator, $lines, PaymentMethod::Credit, $rate);
    }

    private function makeTransaction(
        Farmer $farmer,
        User $operator,
        array $lines,
        PaymentMethod $method,
        float $interestRate = 0.10
    ): void {
        $total = 0.0;

        foreach ($lines as [, $qty, $price]) {
            $total += $qty * $price;
        }

        $creditedAmount = null;
        $storedRate = null;

        if ($method === PaymentMethod::Credit) {
            $storedRate = $interestRate;
            $creditedAmount = round($total * (1 + $interestRate), 2);
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
