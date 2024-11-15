<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Company;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    // index
    public function test_guest_cannot_access_admin_home()
    {
        $response = $this->get('/admin/home');
        $response->assertRedirect('admin/login');
    }

    public function test_user_cannot_access_admin_home()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/home');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_home()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $company = Company::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get('/admin/home');
        $response->assertStatus(200);
    }

}
