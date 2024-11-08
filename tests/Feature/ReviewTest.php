<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Review;
use App\Models\Restaurant;


class ReviewTest extends TestCase
{
    use RefreshDatabase;

    //index
    public function test_guest_user_cannot_access_review_index()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reviews.index', $restaurant));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_can_access_review_index()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));
        $response->assertStatus(200);
    }
    public function test_paid_user_can_access_review_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_review_index()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.index', $restaurant));
        $response->assertRedirect('/admin/home');
    }



    //create
    public function test_guest_user_cannot_access_review_create()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_access_review_create()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_can_access_review_create()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_review_create()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect('/admin/home');
    }



    //store
    public function test_guest_user_cannot_store_review()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->post(route('restaurants.reviews.store', $restaurant));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_store_review()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_can_store_review()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), [
            'score' => $review->score,
            'content' => $review->content,
        ]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_admin_user_cannot_store_review()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($admin, 'admin')->post(route('restaurants.reviews.store', $restaurant));
        $response->assertRedirect('/admin/home');
    }



    //edit
    public function test_guest_user_cannot_access_review_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_access_review_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_cannot_other_review_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_paid_user_can_access_own_review_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertStatus(200);
    }

    public function test_admin_user_cannot_access_review_edit()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect('/admin/home');
    }



    //update
    public function test_guest_user_cannot_update_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->put(route('restaurants.reviews.update', [$restaurant, $review]));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_update_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant, $review]));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_cannot_update_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_paid_user_can_update_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $updatedData = [
            'score' => 5,
            'content' => 'Updated content',
        ];

        $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant, $review]), $updatedData);
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'score' => 5,
            'content' => 'Updated content',
        ]);
    }

    public function test_admin_user_cannot_update_review()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($admin, 'admin')->put(route('restaurants.reviews.update', [$restaurant, $review]));
        $response->assertRedirect('/admin/home');
    }



    //destroy
    public function test_guest_user_cannot_destroy_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('login'));
    }
    public function test_free_user_cannot_destroy_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('subscription.create'));
    }
    public function test_paid_user_cannot_destroy_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }

    public function test_paid_user_can_destroy_review()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QHbClLapQ2Bq7OuFpmx4zay')->create('pm_card_visa');
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_admin_user_cannot_destroy_review()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($admin, 'admin')->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect('/admin/home');
    }
}
