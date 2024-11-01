<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\Admin;
use App\Models\User;




class HomeTest extends TestCase
{
    use RefreshDatabase;

    //index
    public function test_guest_can_access_top_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_user_can_access_top_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_top_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get('/');
        $response->assertRedirect('/admin/home');
    }

}
