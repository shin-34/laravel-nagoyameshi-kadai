<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // index
    public function test_guest_cannot_access_admin_companies_index()
    {
        $response = $this->get('/admin/company');
        $response->assertRedirect('admin/login');
    }

    public function test_user_cannot_access_admin_companies_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/company');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_admin_companies_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.index'));
        $response->assertStatus(200);
    }



    //edit
    public function test_guest_cannot_access_companies_edit()
    {
        $company = Company::factory()->create();
        $response = $this->get('/admin/company/{$company->id}/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_user_cannot_access_companies_edit()
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/company/{$company->id}/edit');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_companies_edit()
    {
        $company = Company::factory()->create();

        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company));
        $response->assertStatus(200);
    }



    //update
    public function test_guest_cannot_access_admin_companies_update()
    {
        $old_company = Company::factory()->create();
        $new_company_data = [
            'name' => 'テスト更新',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新',
        ];
        $response = $this->patch(route('admin.company.update', $old_company), $new_company_data);

        $this->assertDatabaseMissing('companies', $new_company_data);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_companies_update()
    {
        $user = User::factory()->create();

        $old_company = Company::factory()->create();
        $new_company_data = [
            'name' => 'テスト更新',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新',
        ];
        $response = $this->actingAs($user)->patch(route('admin.company.update', $old_company), $new_company_data);

        $this->assertDatabaseMissing('companies', $new_company_data);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_admin_companies_update()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_company = Company::factory()->create();
        $new_company_data = [
            'name' => 'テスト更新',
            'postal_code' => '1111111',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新',
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $old_company), $new_company_data);

        $this->assertDatabaseHas('companies', $new_company_data);
        $response->assertRedirect(route('admin.company.index', $old_company));
    }
}
