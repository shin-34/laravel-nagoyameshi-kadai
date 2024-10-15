<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;

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
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/{$restaurant->id}');
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
        $response = $this->post('/admin/restaurants/store');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_store_restaurant()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/admin/restaurants/store');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_store_restaurant()
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $response = $this->actingAs($admin, 'admin')->post('/admin/restaurants/store', ['name' => 'New Restaurant Name']);
        $response->assertStatus(200);
    }


    //edit
    public function test_guest_cannot_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get('/admin/restaurants/edit/' . $restaurant->id);
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants/edit/' . $restaurant->id);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_restaurant_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/edit/' . $restaurant->id);
        $response->assertStatus(200);
    }


    //update
    public function test_guest_cannot_update_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->put('/admin/restaurants/update/' . $restaurant->id);
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_update_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put('/admin/restaurants/update/' . $restaurant->id);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_update_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $response = $this->actingAs($admin, 'admin')->put('/admin/restaurants/update/' . $restaurant->id,  ['name' => 'Updated Restaurant Name']);
        $response->assertStatus(200);
    }


    //destroy
    public function test_guest_cannot_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete('/admin/restaurants/destroy/' . $restaurant->id);
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/admin/restaurants/destroy/' . $restaurant->id);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_destroy_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        $response = $this->actingAs($admin, 'admin')->delete('/admin/restaurants/destroy/' . $restaurant->id);
        $response->assertStatus(200);
    }


}
