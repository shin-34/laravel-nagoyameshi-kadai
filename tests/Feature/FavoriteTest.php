<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Review;
use App\Models\Restaurant;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    // index
    public function test_guest_cannot_access_favorites_index()
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_free_user_cannot_access_favorites_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertRedirect(route('subscription.create'));
    }

    public function test_premium_user_can_access_favorites_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_favorites_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get(route('favorites.index'));
        $response->assertRedirect(route('admin.home'));
    }



    // store
    public function test_guest_cannot_access_favorites_store()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->post(route('favorites.store', $restaurant));
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id,]);
        $response ->assertStatus(302);
    }

    public function test_free_user_cannot_access_favorites_store()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant));
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id,]);
        $response ->assertStatus(302);
    }

    public function test_premium_user_can_access_favorites_store()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant));
        $this->assertDatabaseHas('restaurant_user', ['user_id' => $user->id, 'restaurant_id' => $restaurant->id,]);
        $response->assertRedirect(route('home'));
    }

    public function test_admin_cannot_access_favorites_store()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->post(route('favorites.store', $restaurant));
        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id,]);
        $response->assertRedirect(route('admin.home'));
    }



    //destroy
    public function test_guest_cannot_access_favorites_destroy()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->favorite_restaurants()->attach($restaurant->id);
        $response = $this->delete(route('favorites.destroy', $restaurant));
        $this->assertDatabaseHas('restaurant_user', ['user_id' => $user->id, 'restaurant_id' => $restaurant->id,]);
        $response ->assertStatus(302);
    }

    public function test_free_user_cannot_access_favorites_destroy()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $user->favorite_restaurants()->attach($restaurant->id);
        $response = $this->actingAs($user)->delete(route('favorites.destroy', $restaurant));
        $this->assertDatabaseHas('restaurant_user', ['user_id' => $user->id, 'restaurant_id' => $restaurant->id,]);
        $response ->assertStatus(302);
    }

    public function test_premium_user_can_access_favorites_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $user->favorite_restaurants()->attach($restaurant->id);
        $response = $this->actingAs($user)->delete(route('favorites.destroy', $restaurant));
        $this->assertDatabaseMissing('restaurant_user', ['user_id' => $user->id, 'restaurant_id' => $restaurant->id,]);
        $response->assertRedirect(route('home'));
    }

    public function test_admin_cannot_access_favorites_destroy()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->favorite_restaurants()->attach($restaurant->id);
        $response = $this->actingAs($admin, 'admin')->delete(route('favorites.destroy', $restaurant));
        $this->assertDatabaseHas('restaurant_user', ['user_id' => $user->id, 'restaurant_id' => $restaurant->id,]);
        $response->assertRedirect(route('admin.home'));
    }

}