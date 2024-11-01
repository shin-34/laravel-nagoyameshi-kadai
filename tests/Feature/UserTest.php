<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;

class UserTest extends TestCase
{
    use RefreshDatabase;

    //index
    public function test_guest_cannot_access_user_index()
    {
        $response = $this->get('/user');
        $response->assertRedirect('/login');
    }

    public function test_user_can_access_user_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/user');
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_user_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get('/user');
        $response->assertRedirect('/admin/home');
    }



    //edit
    public function test_guest_cannot_access_user_edit()
    {
        $response = $this->get('/user/{$user->id}/edit');
        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_other_user_edit()
    {
        $user = User::factory()->create();
        $otheruser = User::factory()->create();
        $response = $this->actingAs($user)->get("/user/{$otheruser->id}/edit");
        $response->assertRedirect('/user');
    }

    public function test_user_can_access_user_edit()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("/user/{$user->id}/edit");
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_user_edit()
    {
        $user = User::factory()->create();
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get("/user/{$user->id}/edit");
        $response->assertRedirect('/admin/home');
    }



    //update
    public function test_guest_cannot_access_user_update()
    {
        $old_user = User::factory()->create();
        $new_user_data = [
            'name' => '更新されたユーザー名',
            'kana' => 'コウシンサレタカナ',
            'email' => 'user@example.com',
            'postal_code' => '1111111',
            'address' => '更新された住所',
            'phone_number' => '11111111111',
            'birthday' => '11111111',
            'occupation' => '更新された職業',
        ];

        $response = $this->patch(route('user.update', $old_user), $new_user_data);

        $this->assertDatabaseMissing('users', $new_user_data);
        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_other_user_update()
    {
        $user = User::factory()->create();
        $otheruser = User::factory()->create();

        $old_user = User::factory()->create();
        $new_user_data = [
            'name' => '更新されたユーザー名',
            'kana' => 'コウシンサレタカナ',
            'email' => 'user@example.com',
            'postal_code' => '1111111',
            'address' => '更新された住所',
            'phone_number' => '11111111111',
            'birthday' => '11111111',
            'occupation' => '更新された職業',
        ];

        $response = $this->actingAs($user)->patch(route('user.update', $old_user), $new_user_data);

        $this->assertDatabaseMissing('users', $new_user_data);
        $response->assertRedirect('/user');
    }

    public function test_user_can_access_user_update()
    {
        $user = User::factory()->create();
        $otheruser = User::factory()->create();

        $old_user = User::factory()->create();
        $new_user_data = [
            'name' => '更新されたユーザー名',
            'kana' => 'コウシンサレタカナ',
            'email' => 'user@example.com',
            'postal_code' => '1111111',
            'address' => '更新された住所',
            'phone_number' => '11111111111',
            'birthday' => '11111111',
            'occupation' => '更新された職業',
        ];

        $response = $this->actingAs($user)->patch(route('user.update', $old_user), $new_user_data);

        $this->assertDatabaseMissing('users', $new_user_data);
        $response->assertRedirect(route('user.index'));
    }

    public function test_admin_cannot_access_user_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_user = User::factory()->create();
        $new_user_data = [
            'name' => '更新されたユーザー名',
            'kana' => 'コウシンサレタカナ',
            'email' => 'user@example.com',
            'postal_code' => '1111111',
            'address' => '更新された住所',
            'phone_number' => '11111111111',
            'birthday' => '11111111',
            'occupation' => '更新された職業',
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('user.update', $old_user), $new_user_data);

        $this->assertDatabaseMissing('users', $new_user_data);
        $response->assertRedirect('/admin/home');
    }

}
