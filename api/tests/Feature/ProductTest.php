<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): static
    {
        return $this->actingAs(User::factory()->create(), 'sanctum');
    }

    private function createCategory(string $name = 'Guitars'): Category
    {
        return Category::factory()->create(['name' => $name]);
    }

    private function createProduct(array $attrs = []): Product
    {
        $category = $this->createCategory();

        return Product::factory()->create(array_merge(
            ['category_id' => $category->id],
            $attrs
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/products
    // -------------------------------------------------------------------------

    public function test_index_returns_paginated_products_without_authentication(): void
    {
        $category = $this->createCategory();
        Product::factory()->count(5)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [['id', 'name', 'description', 'price', 'image_url', 'category']],
                     'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                 ]);

        $this->assertEquals(5, $response->json('meta.total'));
    }

    public function test_index_returns_empty_data_when_no_products(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJson(['data' => []])
                 ->assertJsonPath('meta.total', 0);
    }

    public function test_index_paginates_with_12_per_page(): void
    {
        $category = $this->createCategory();
        Product::factory()->count(15)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertJsonPath('meta.per_page', 12)
                 ->assertJsonPath('meta.total', 15)
                 ->assertJsonPath('meta.last_page', 2)
                 ->assertJsonCount(12, 'data');
    }

    public function test_index_returns_second_page(): void
    {
        $category = $this->createCategory();
        Product::factory()->count(15)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products?page=2');

        $response->assertJsonPath('meta.current_page', 2)
                 ->assertJsonCount(3, 'data');
    }

    public function test_index_includes_category_data_in_each_product(): void
    {
        $category = $this->createCategory('Keyboards');
        Product::factory()->create(['category_id' => $category->id]);

        $product = $this->getJson('/api/products')->json('data.0');

        $this->assertEquals('Keyboards', $product['category']['name']);
    }

    // -------------------------------------------------------------------------
    // GET /api/products?category={id}
    // -------------------------------------------------------------------------

    public function test_index_filters_by_category(): void
    {
        $guitars  = $this->createCategory('Guitars');
        $drums    = $this->createCategory('Drums');

        Product::factory()->count(3)->create(['category_id' => $guitars->id]);
        Product::factory()->count(2)->create(['category_id' => $drums->id]);

        $response = $this->getJson("/api/products?category={$guitars->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('meta.total', 3);

        foreach ($response->json('data') as $product) {
            $this->assertEquals($guitars->id, $product['category']['id']);
        }
    }

    public function test_index_returns_empty_for_nonexistent_category_filter(): void
    {
        $this->createProduct();

        $response = $this->getJson('/api/products?category=9999');

        $response->assertStatus(200)
                 ->assertJson(['data' => []])
                 ->assertJsonPath('meta.total', 0);
    }

    // -------------------------------------------------------------------------
    // GET /api/products?search={query}
    // -------------------------------------------------------------------------

    public function test_index_searches_by_product_name(): void
    {
        $category = $this->createCategory();
        Product::factory()->create(['name' => 'Fender Stratocaster', 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Gibson Les Paul', 'category_id' => $category->id]);

        $response = $this->getJson('/api/products?search=fender');

        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonPath('data.0.name', 'Fender Stratocaster');
    }

    public function test_index_searches_by_product_description(): void
    {
        $category = $this->createCategory();
        Product::factory()->create([
            'name'        => 'Generic Guitar',
            'description' => 'Made with alder body',
            'category_id' => $category->id,
        ]);
        Product::factory()->create([
            'name'        => 'Other Guitar',
            'description' => 'Made with mahogany',
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/products?search=alder');

        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonPath('data.0.name', 'Generic Guitar');
    }

    public function test_index_applies_category_and_search_filters_together(): void
    {
        $guitars = $this->createCategory('Guitars');
        $basses  = $this->createCategory('Basses');

        Product::factory()->create(['name' => 'Fender Strat', 'category_id' => $guitars->id]);
        Product::factory()->create(['name' => 'Fender Jazz Bass', 'category_id' => $basses->id]);

        $response = $this->getJson("/api/products?category={$guitars->id}&search=fender");

        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonPath('data.0.name', 'Fender Strat');
    }

    public function test_index_returns_all_products_when_search_is_empty(): void
    {
        $category = $this->createCategory();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products?search=');

        $response->assertJsonPath('meta.total', 3);
    }

    // -------------------------------------------------------------------------
    // GET /api/products/{id}
    // -------------------------------------------------------------------------

    public function test_show_returns_product_with_category(): void
    {
        $category = $this->createCategory('Drums');
        $product  = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'name', 'description', 'price', 'image_url', 'category']])
                 ->assertJsonPath('data.category.name', 'Drums');
    }

    public function test_show_returns_404_for_nonexistent_product(): void
    {
        $this->getJson('/api/products/9999')
             ->assertStatus(404)
             ->assertJson(['message' => 'Product not found']);
    }

    public function test_show_does_not_require_authentication(): void
    {
        $product = $this->createProduct();

        $this->getJson("/api/products/{$product->id}")
             ->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // POST /api/products
    // -------------------------------------------------------------------------

    public function test_store_creates_product_when_authenticated(): void
    {
        $category = $this->createCategory();

        $response = $this->actingAsUser()
                         ->postJson('/api/products', [
                             'name'        => 'Shure SM57',
                             'description' => 'Dynamic microphone',
                             'price'       => 99.00,
                             'category_id' => $category->id,
                         ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Shure SM57')
                 ->assertJsonPath('data.category.id', $category->id);

        $this->assertDatabaseHas('products', ['name' => 'Shure SM57']);
    }

    public function test_store_requires_authentication(): void
    {
        $category = $this->createCategory();

        $this->postJson('/api/products', [
            'name'        => 'Test',
            'price'       => 10.00,
            'category_id' => $category->id,
        ])->assertStatus(401);
    }

    public function test_store_fails_with_missing_required_fields(): void
    {
        $this->actingAsUser()
             ->postJson('/api/products', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['name', 'price', 'category_id']);
    }

    public function test_store_fails_with_negative_price(): void
    {
        $category = $this->createCategory();

        $this->actingAsUser()
             ->postJson('/api/products', [
                 'name'        => 'Test',
                 'price'       => -1,
                 'category_id' => $category->id,
             ])->assertStatus(422)
               ->assertJsonValidationErrors(['price']);
    }

    public function test_store_fails_with_nonexistent_category_id(): void
    {
        $this->actingAsUser()
             ->postJson('/api/products', [
                 'name'        => 'Test',
                 'price'       => 10.00,
                 'category_id' => 9999,
             ])->assertStatus(422)
               ->assertJsonValidationErrors(['category_id']);
    }

    // -------------------------------------------------------------------------
    // PUT /api/products/{id}
    // -------------------------------------------------------------------------

    public function test_update_changes_product_fields(): void
    {
        $product = $this->createProduct(['price' => 100.00]);

        $response = $this->actingAsUser()
                         ->putJson("/api/products/{$product->id}", ['price' => 199.99]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.price', '199.99');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 199.99]);
    }

    public function test_update_is_partial(): void
    {
        $product = $this->createProduct(['name' => 'Original Name', 'price' => 100.00]);

        $this->actingAsUser()
             ->putJson("/api/products/{$product->id}", ['price' => 200.00]);

        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'Original Name',
            'price' => 200.00,
        ]);
    }

    public function test_update_requires_authentication(): void
    {
        $product = $this->createProduct();

        $this->putJson("/api/products/{$product->id}", ['price' => 50.00])
             ->assertStatus(401);
    }

    public function test_update_returns_404_for_nonexistent_product(): void
    {
        $this->actingAsUser()
             ->putJson('/api/products/9999', ['price' => 50.00])
             ->assertStatus(404)
             ->assertJson(['message' => 'Product not found']);
    }

    public function test_update_fails_with_invalid_category_id(): void
    {
        $product = $this->createProduct();

        $this->actingAsUser()
             ->putJson("/api/products/{$product->id}", ['category_id' => 9999])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['category_id']);
    }

    // -------------------------------------------------------------------------
    // DELETE /api/products/{id}
    // -------------------------------------------------------------------------

    public function test_destroy_deletes_product(): void
    {
        $product = $this->createProduct();

        $this->actingAsUser()
             ->deleteJson("/api/products/{$product->id}")
             ->assertStatus(200)
             ->assertJson(['message' => 'Product deleted']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $product = $this->createProduct();

        $this->deleteJson("/api/products/{$product->id}")
             ->assertStatus(401);
    }

    public function test_destroy_returns_404_for_nonexistent_product(): void
    {
        $this->actingAsUser()
             ->deleteJson('/api/products/9999')
             ->assertStatus(404)
             ->assertJson(['message' => 'Product not found']);
    }

    public function test_destroy_makes_product_unreachable_via_show(): void
    {
        $product = $this->createProduct();

        $this->actingAsUser()->deleteJson("/api/products/{$product->id}");

        $this->getJson("/api/products/{$product->id}")
             ->assertStatus(404);
    }
}
