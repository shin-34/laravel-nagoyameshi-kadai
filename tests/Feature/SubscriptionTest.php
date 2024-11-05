<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;


class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    //create
    public function test_guest_user_cannot_access_create_page()
    {
        $response = $this->get(route('subscription.create'));
        $response->assertRedirect(route('login'));
    }
    public function test_free_member_can_access_create_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('subscription.create'));
        $response->assertStatus(200);
    }
    public function test_paid_member_cannot_access_create_page()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('subscription.create'));
        $response->assertRedirect(route('subscription.edit'));
    }

    public function test_admin_user_cannot_access_create_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get(route('subscription.create'));
        $response->assertRedirect('/admin/home');
    }


    //store
    public function test_guest_user_cannot_store_create_page()
    {
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
        $response = $this->post(route('subscription.store', $request_parameter));
        $response->assertRedirect(route('login'));
    }
    public function test_free_member_can_store_create_page()
    {
        $user = User::factory()->create();
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
        $response = $this->actingAs($user)->post(route('subscription.store', $request_parameter));
        $user->refresh();
        $this->assertTrue($user->subscribed('premium_plan'));
    }
    public function test_paid_member_cannot_store_create_page()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
        $response = $this->actingAs($user)->post(route('subscription.store', $request_parameter));
        $response->assertRedirect(route('subscription.edit'));
    }

    public function test_admin_user_cannot_store_create_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
        $response = $this->actingAs($admin, 'admin')->post(route('subscription.store', $request_parameter));
        $response->assertRedirect('/admin/home');
    }


    //edit
    public function test_guest_user_cannot_access_edit_page()
    {
        $response = $this->get(route('subscription.edit'));
        $response->assertRedirect(route('login'));
    }
    public function test_free_member_cannot_access_edit_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('subscription.edit'));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_member_can_access_edit_page()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('subscription.edit'));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_edit_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get(route('subscription.edit'));
        $response->assertRedirect('/admin/home');
    }


    //update
    public function test_guest_user_cannot_update_payment_method()
    {
        $request_parameter = ['paymentMethodId' => 'pm_card_mastercard'];
        $response = $this->patch(route('subscription.update', $request_parameter));
        $response->assertRedirect(route('login'));
    }
    public function test_free_member_cannot_update_payment_method()
    {
        $user = User::factory()->create();
        $request_parameter = ['paymentMethodId' => 'pm_card_mastercard'];
        $response = $this->actingAs($user)->patch(route('subscription.update', $request_parameter));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_member_can_update_payment_method()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $request_parameter = ['paymentMethodId' => 'pm_card_mastercard'];
        $response = $this->actingAs($user)->patch(route('subscription.update', $request_parameter));
        $default_payment_method_id = $user->defaultPaymentMethod()->id;
        $this->assertNotEquals($request_parameter['paymentMethodId'], $default_payment_method_id);
    }

    public function test_admin_user_cannot_update_payment_method()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $request_parameter = ['paymentMethodId' => 'pm_card_mastercard'];
        $response = $this->actingAs($admin, 'admin')->patch(route('subscription.update', $request_parameter));
        $response->assertRedirect('/admin/home');
    }


    //cancel
    public function test_guest_user_cannot_access_cancel_page()
    {
        $response = $this->get(route('subscription.cancel'));
        $response->assertRedirect(route('login'));
    }
    public function test_free_member_cannot_access_cancel_page()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('subscription.cancel'));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_member_can_access_cancel_page()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('subscription.cancel'));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_cancel_page()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $response = $this->actingAs($admin, 'admin')->get(route('subscription.cancel'));
        $response->assertRedirect('/admin/home');
    }


    //destroy
    public function test_guest_user_cannot_destroy_subscription()
    {
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
        $response = $this->delete(route('subscription.destroy', $request_parameter));
        $response->assertRedirect(route('login'));
    }
    public function test_free_member_cannot_destroy_subscription()
    {
        $user = User::factory()->create();
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
        $response = $this->actingAs($user)->delete(route('subscription.destroy', $request_parameter));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_member_can_destroy_subscription()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $response = $this->actingAs($user)->delete(route('subscription.destroy'));
        $this->assertFalse($user->subscribed('premium_plan'));
        $response->assertRedirect(route('home'));
    }

    public function test_admin_user_cannot_destroy_subscription()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
        $response = $this->actingAs($admin, 'admin')->delete(route('subscription.destroy', $request_parameter));
        $response->assertRedirect('/admin/home');
    }
}
