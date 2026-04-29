<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Farmer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $supervisor;

    private User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->supervisor = User::factory()->supervisor()->create();
        $this->operator = User::factory()->operator()->create();
    }

    // ─── Users ───────────────────────────────────────────────────────────────

    public function test_users_index_returns_paginated_response(): void
    {
        User::factory()->count(5)->admin()->create();

        $this->actingAs($this->admin)
            ->getJson('/api/v1/users')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.current_page', 1);
    }

    public function test_users_per_page_param_is_respected(): void
    {
        User::factory()->count(10)->admin()->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/users?per_page=3')
            ->assertStatus(200);

        $this->assertCount(3, $response->json('data'));
        $this->assertEquals(3, $response->json('meta.per_page'));
    }

    public function test_users_invalid_per_page_returns_422(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/api/v1/users?per_page=0')
            ->assertStatus(422);
    }

    // ─── Categories ──────────────────────────────────────────────────────────

    public function test_categories_index_returns_paginated_response(): void
    {
        Category::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->getJson('/api/v1/categories')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.current_page', 1);
    }

    public function test_categories_per_page_param_is_respected(): void
    {
        Category::factory()->count(10)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/categories?per_page=4')
            ->assertStatus(200);

        $this->assertCount(4, $response->json('data'));
        $this->assertEquals(4, $response->json('meta.per_page'));
    }

    // ─── Products ────────────────────────────────────────────────────────────

    public function test_products_index_returns_paginated_response(): void
    {
        Product::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->getJson('/api/v1/products')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.current_page', 1);
    }

    public function test_products_per_page_param_is_respected(): void
    {
        Product::factory()->count(10)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/products?per_page=5')
            ->assertStatus(200);

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(5, $response->json('meta.per_page'));
    }

    public function test_products_filter_by_category_id(): void
    {
        $targetCategory = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        Product::factory()->count(3)->create(['category_id' => $targetCategory->id]);
        Product::factory()->count(2)->create(['category_id' => $otherCategory->id]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/products?category_id={$targetCategory->id}")
            ->assertStatus(200);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_products_filter_rejects_nonexistent_category(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/api/v1/products?category_id=9999')
            ->assertStatus(422);
    }

    // ─── Farmers ─────────────────────────────────────────────────────────────

    public function test_farmers_index_returns_paginated_response(): void
    {
        Farmer::factory()->count(5)->create();

        $this->actingAs($this->operator)
            ->getJson('/api/v1/farmers')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.current_page', 1);
    }

    public function test_farmers_per_page_param_is_respected(): void
    {
        Farmer::factory()->count(10)->create();

        $response = $this->actingAs($this->operator)
            ->getJson('/api/v1/farmers?per_page=4')
            ->assertStatus(200);

        $this->assertCount(4, $response->json('data'));
        $this->assertEquals(4, $response->json('meta.per_page'));
    }

    // ─── Transactions ─────────────────────────────────────────────────────────

    public function test_transactions_index_returns_paginated_response(): void
    {
        $farmer = Farmer::factory()->create();

        Transaction::factory()->count(5)->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $this->operator->id,
        ]);

        $this->actingAs($this->operator)
            ->getJson('/api/v1/transactions')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.current_page', 1);
    }

    public function test_transactions_per_page_param_is_respected(): void
    {
        $farmer = Farmer::factory()->create();

        Transaction::factory()->count(10)->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $this->operator->id,
        ]);

        $response = $this->actingAs($this->operator)
            ->getJson('/api/v1/transactions?per_page=3')
            ->assertStatus(200);

        $this->assertCount(3, $response->json('data'));
        $this->assertEquals(3, $response->json('meta.per_page'));
    }

    public function test_transactions_filter_by_farmer_id(): void
    {
        $farmer1 = Farmer::factory()->create();
        $farmer2 = Farmer::factory()->create();

        Transaction::factory()->count(4)->create([
            'farmer_id' => $farmer1->id,
            'operator_id' => $this->operator->id,
        ]);
        Transaction::factory()->count(2)->create([
            'farmer_id' => $farmer2->id,
            'operator_id' => $this->operator->id,
        ]);

        $response = $this->actingAs($this->operator)
            ->getJson("/api/v1/transactions?farmer_id={$farmer1->id}")
            ->assertStatus(200);

        $this->assertCount(4, $response->json('data'));
    }

    public function test_transactions_filter_by_payment_method(): void
    {
        $farmer = Farmer::factory()->create(['credit_limit' => 1_000_000]);

        Transaction::factory()->count(3)->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $this->operator->id,
        ]);
        Transaction::factory()->credit()->count(2)->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $this->operator->id,
        ]);

        $response = $this->actingAs($this->operator)
            ->getJson('/api/v1/transactions?payment_method=cash')
            ->assertStatus(200);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_transactions_filter_by_date_range(): void
    {
        $farmer = Farmer::factory()->create();

        Transaction::factory()->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $this->operator->id,
            'created_at' => '2026-01-15 12:00:00',
        ]);
        Transaction::factory()->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $this->operator->id,
            'created_at' => '2026-02-20 12:00:00',
        ]);
        Transaction::factory()->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $this->operator->id,
            'created_at' => '2026-03-10 12:00:00',
        ]);

        $response = $this->actingAs($this->operator)
            ->getJson('/api/v1/transactions?date_from=2026-01-01&date_to=2026-02-28')
            ->assertStatus(200);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_transactions_date_to_must_be_after_date_from(): void
    {
        $this->actingAs($this->operator)
            ->getJson('/api/v1/transactions?date_from=2026-03-01&date_to=2026-01-01')
            ->assertStatus(422);
    }

    public function test_transactions_invalid_payment_method_returns_422(): void
    {
        $this->actingAs($this->operator)
            ->getJson('/api/v1/transactions?payment_method=barter')
            ->assertStatus(422);
    }
}
