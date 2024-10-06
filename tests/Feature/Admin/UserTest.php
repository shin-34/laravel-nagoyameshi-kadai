<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_cannot_access_admin_user_list(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('admin/login');
    }

    public function test_non_admin_user_cannot_access_admin_user_list(): void
    {
        // 一般ユーザーを作成してログイン
        $user = User::factory()->create();

        // ログインして会員一覧ページにアクセス
        $response = $this->actingAs($user)->get('/admin/users');

        // 403 Forbidden ステータスが返される（権限がない場合）
        $response->assertRedirect('admin/login');
    }

    public function test_admin_user_can_access_admin_user_list(): void
    {
        // 管理者ユーザーを作成してログイン
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);

        // ログインして会員一覧ページにアクセス
        $response = $this->actingAs($adminUser,'admin')->get('/admin/users');


        // 200 OK ステータスが返される
        $response->assertStatus(200);
    }




     //1. 未ログインユーザーが管理者側の会員詳細ページにアクセスできない
    public function test_guest_cannot_access_admin_user_detail()
    {

        // 任意のユーザーの詳細ページに未ログインでアクセス
        $response = $this->get('/admin/users');  // 1は仮のユーザーID

        // ログインページへリダイレクトされる
        $response->assertRedirect('admin/login');
    }

    
     //2. ログイン済みの一般ユーザーが管理者側の会員詳細ページにアクセスできない
    public function test_non_admin_user_cannot_access_admin_user_detail()
    {
        // 一般ユーザーを作成してログイン
        $user = User::factory()->create();

        // ログインして会員詳細ページにアクセス
        $response = $this->actingAs($user)->get(route('admin.users.show', $user));

        // ログインページへリダイレクトされる
        $response->assertRedirect('admin/login');
    }

    
     //3. ログイン済みの管理者が管理者側の会員詳細ページにアクセスできる
    public function test_admin_user_can_access_admin_user_detail()
    {
        // 管理者ユーザーを作成してログイン
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);

        $user = User::factory()->create();

        // ログインして会員詳細ページにアクセス
        $response = $this->actingAs($adminUser,'admin')->get(route('admin.users.show', $user)); 

        // 200 OK ステータスが返される
        $response->assertStatus(200);
    }
}
