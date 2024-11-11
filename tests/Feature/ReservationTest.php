<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Review;
use App\Models\Restaurant;
use App\Models\Reservation;


class ReservationTest extends TestCase
{
    use RefreshDatabase;

    //index
    public function test_guest_user_cannot_access_reservation_index()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('reservations.index', $restaurant));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_access_reservation_index()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('reservations.index', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_can_access_reservation_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('reservations.index', $restaurant));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_reservation_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('reservations.index', $restaurant));
        $response->assertRedirect('/admin/home');
    }



    //create
    public function test_guest_user_cannot_access_reservation_create()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_access_reservation_create()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_can_access_reservation_create()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_reservation_create()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect('/admin/home');
    }



    //store
    public function test_guest_user_cannot_store_reservation()
    {
        $restaurant = Restaurant::factory()->create();
        $reservation_data = [
            'reserved_datetime' => now(),
            'number_of_people' => 5,
        ];
        $response = $this->post(route('restaurants.reservations.store', $restaurant), $reservation_data);
        $this->assertDatabaseMissing('reservations', $reservation_data);
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_store_reservation()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $reservation_data = [
            'reserved_datetime' => now(),
            'number_of_people' => 5,
        ];
        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservation_data);
        $this->assertDatabaseMissing('reservations', $reservation_data);
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_can_store_reservation()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $reservation_date = now()->format('Y-m-d');  // 今日の日付を 'Y-m-d' フォーマットで
        $reservation_time = now()->format('H:i');    // 現在の時刻を 'H:i' フォーマットで

        $reservation_data = [
            'reservation_date' => $reservation_date,
            'reservation_time' => $reservation_time,
            'number_of_people' => 5,
        ];
        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservation_data);
        $this->assertDatabaseHas('reservations', [
            'reserved_datetime' => $reservation_date . ' ' . $reservation_time,  // 結合された日時をチェック
            'number_of_people' => 5,
        ]);
        
        $response->assertRedirect(route('reservations.index', $restaurant));
    }

    public function test_admin_user_cannot_store_reservation()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $reservation_data = [
            'reserved_datetime' => now(),
            'number_of_people' => 5,
        ];
        $response = $this->actingAs($admin, 'admin')->post(route('restaurants.reservations.store', $restaurant), $reservation_data);
        $this->assertDatabaseMissing('reservations', $reservation_data);
        $response->assertRedirect('/admin/home');
    }



    //destroy
    public function test_guest_user_cannot_destroy_reservation()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->delete(route('reservations.destroy', $reservation));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_destroy_reservation()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_cannot_destroy_other_reservation()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $otherUser = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('reservations.index'));
    }

    public function test_paid_user_can_destroy_own_reservation()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id,
        ]);
    }

    public function test_admin_user_cannot_destroy_reservation()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($admin, 'admin')->delete(route('reservations.destroy', $reservation));
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $response->assertRedirect('/admin/home');
    }
}
