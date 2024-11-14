<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;


class Restaurant extends Model
{
    use HasFactory, Sortable;

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function regular_holidays()
    {
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    //追加　店舗の平均評価順
    public function ratingSortable($query, $direction) {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    //並べ替え機能をアップデート
    public function popularSortable() {
        $restaurants = Restaurant::withCount('reservations')->orderBy('reservations_count', 'desc')->get();
        return $restaurants;
    }
    
    public function favorited_users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
