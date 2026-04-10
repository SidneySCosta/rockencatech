<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): static
    {
        return $this->actingAs(User::factory()->create(), 'sanctum');
    }

    // -------------------------------------------------------------------------
    // GET /api/categories
    // -------------------------------------------------------------------------

    public function test_index_returns_all_categories_without_authentication(): void
    {
        Category::factory()->createMany([
            ['name' => 'Guitars'],
            ['name' => 'Basses'],
            ['name' => 'Drums'],
        ]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    public function test_index_returns_empty_array_when_no_categories(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJson(['data' => []]);
    }

    public function test_index_returns_categories_ordered_by_name(): void
    {
        Category::factory()->create(['name' => 'Zebra']);
        Category::factory()->create(['name' => 'Alpha']);
        Category::factory()->create(['name' => 'Middle']);

        $names = $this->getJson('/api/categories')->json('data.*.name');

        $this->assertEquals(['Alpha', 'Middle', 'Zebra'], $names);
    }

    // -------------------------------------------------------------------------
    // POST /api/categories
    // -------------------------------------------------------------------------

    public function test_store_creates_category_when_authenticated(): void
    {
        $response = $this->actingAsUser()
                         ->postJson('/api/categories', ['name' => 'Keyboards']);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Keyboards');

        $this->assertDatabaseHas('categories', ['name' => 'Keyboards']);
    }

    public function test_store_requires_authentication(): void
    {
        $this->postJson('/api/categories', ['name' => 'Keyboards'])
             ->assertStatus(401);
    }

    public function test_store_fails_with_missing_name(): void
    {
        $this->actingAsUser()
             ->postJson('/api/categories', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
    }

    public function test_store_fails_with_duplicate_name(): void
    {
        Category::factory()->create(['name' => 'Guitars']);

        $this->actingAsUser()
             ->postJson('/api/categories', ['name' => 'Guitars'])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
    }

    public function test_store_fails_with_blank_name(): void
    {
        $this->actingAsUser()
             ->postJson('/api/categories', ['name' => '   '])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
    }

    // -------------------------------------------------------------------------
    // PUT /api/categories/{id}
    // -------------------------------------------------------------------------

    public function test_update_changes_category_name(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAsUser()
                         ->putJson("/api/categories/{$category->id}", ['name' => 'New Name']);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    public function test_update_requires_authentication(): void
    {
        $category = Category::factory()->create();

        $this->putJson("/api/categories/{$category->id}", ['name' => 'New'])
             ->assertStatus(401);
    }

    public function test_update_returns_404_for_nonexistent_id(): void
    {
        $this->actingAsUser()
             ->putJson('/api/categories/9999', ['name' => 'Test'])
             ->assertStatus(404)
             ->assertJson(['message' => 'Category not found']);
    }

    public function test_update_fails_with_empty_name(): void
    {
        $category = Category::factory()->create();

        $this->actingAsUser()
             ->putJson("/api/categories/{$category->id}", ['name' => ''])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['name']);
    }

    public function test_update_allows_same_name_on_same_category(): void
    {
        $category = Category::factory()->create(['name' => 'Guitars']);

        $this->actingAsUser()
             ->putJson("/api/categories/{$category->id}", ['name' => 'Guitars'])
             ->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // DELETE /api/categories/{id}
    // -------------------------------------------------------------------------

    public function test_destroy_deletes_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAsUser()
             ->deleteJson("/api/categories/{$category->id}")
             ->assertStatus(200)
             ->assertJson(['message' => 'Category deleted']);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $category = Category::factory()->create();

        $this->deleteJson("/api/categories/{$category->id}")
             ->assertStatus(401);
    }

    public function test_destroy_returns_404_for_nonexistent_id(): void
    {
        $this->actingAsUser()
             ->deleteJson('/api/categories/9999')
             ->assertStatus(404)
             ->assertJson(['message' => 'Category not found']);
    }
}
