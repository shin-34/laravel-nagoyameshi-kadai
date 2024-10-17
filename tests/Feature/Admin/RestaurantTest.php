<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    //index
    public function test_guest_cannot_access_restaurant_index()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_access_restaurant_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_restaurant_index()
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants');
        $response->assertStatus(200);
    }

    // show
    public function test_guest_cannot_access_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get('/admin/restaurants/{$restaurant->id}');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_access_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants/{$restaurant->id}');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_restaurant_show()
    {
        $restaurant = Restaurant::factory()->create();
        //$admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        //$response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/{$restaurant->id}');
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show', $restaurant));
        $response->assertStatus(200);
    }


    //create
    public function test_guest_cannot_access_restaurant_create()
    {
        $response = $this->get('/admin/restaurants/create');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_access_restaurant_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants/create');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_restaurant_create()
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/create');
        $response->assertStatus(200);
    }

    //store
    public function test_guest_cannot_store_restaurant()
    {
        $restaurant = Restaurant::factory()->make()->toArray();
        $response = $this->post('/admin/restaurants', $restaurant);
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_store_restaurant()
    {
        $restaurant = Restaurant::factory()->make()->toArray();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/admin/restaurants', $restaurant);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_store_restaurant()
    {
        $restaurant = Restaurant::factory()->make()->toArray();
        //$admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        //$response = $this->actingAs($admin, 'admin')->post('/admin/restaurants', ['name' => 'New Restaurant Name']);
        $response = $this->actingAs($admin, 'admin')->post(route('admin.restaurants.store'), $restaurant);
        $response->assertRedirect('/admin/restaurants');
    }


    //edit
    public function test_guest_cannot_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get('/admin/restaurants/{$restaurant->id}/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants/{$restaurant->id}/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        //$admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        //$response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/{$restaurant->id}/edit');
        $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit', $restaurant));
        $response->assertStatus(200);
    }


    //update
    public function test_guest_cannot_update_restaurant()
    {
        //$restaurant = Restaurant::factory()->make()->toArray();
        $response = $this->put('/admin/restaurants/{$restaurant->id}');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_update_restaurant()
    {
        //$restaurant = Restaurant::factory()->make()->toArray();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put('/admin/restaurants/{$restaurant->id}');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_update_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        //$admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $updateData = [
            'name' => '更新されたレストラン名',
            'description' => '更新された説明',
            'lowest_price' => 1200,
            'highest_price' => 6000,
            'postal_code' => '1111111',
            'address' => '更新された住所',
            'opening_time' => '11:00:00',
            'closing_time' => '21:00:00',
            'seating_capacity' => 60,
        ];
        //$response = $this->actingAs($admin, 'admin')->put('/admin/restaurants/update/' . $restaurant->id,  ['name' => 'Updated Restaurant Name']);
        $response = $this->actingAs($admin, 'admin')->put(route('admin.restaurants.update', $restaurant), $updateData);
        //~atは含まない
        unset($restaurant['updated_at'], $restaurant['created_at']);
        $this->assertDatabaseHas('restaurants', array_merge(['id' => $restaurant->id], $updateData));
        //「route('admin.restaurants.show', $restaurant)」は「/admin/restaurants/{restaurant}（例えば、/admin/restaurants/1）」に遷移
        $response->assertRedirect(route('admin.restaurants.show', $restaurant));
    }


    //destroy
    public function test_guest_cannot_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete('/admin/restaurants/{$restaurant->id}');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_destroy_restaurant()
    {
        //$restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/admin/restaurants/{$restaurant->id}');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        
        //$admin = User::factory()->create(['email' => 'admin@example.com']);
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        //$response = $this->actingAs($admin, 'admin')->delete('/admin/restaurants/destroy/' . $restaurant->id);
        $response = $this->actingAs($admin, 'admin')->delete(route('admin.restaurants.destroy', $restaurant->id));
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect('/admin/restaurants');
    }


}
