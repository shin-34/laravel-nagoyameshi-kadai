<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    //index
    public function test_guest_can_access_restaurant_index()
    {
        $response = $this->get('/restaurants');
        $response->assertStatus(200);
    }

    public function test_user_can_access_restaurant_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/restaurants');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_restaurant_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get('/restaurants');
        $response->assertRedirect('/admin/home');
    }


    //show
    public function test_guest_can_access_restaurant_show()
    {
        $response = $this->get('/restaurants');
        $response->assertStatus(200);
    }

    public function test_user_can_access_restaurant_show()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/restaurants');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_restaurant_show()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get('/restaurants');
        $response->assertRedirect('/admin/home');
    }
}
