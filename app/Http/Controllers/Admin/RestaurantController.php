<?php

namespace App\Http\Controllers\Admin;

use App\Models\Restaurant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        if ($keyword !== null) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->paginate(15);
        } else {
            $restaurants = Restaurant::paginate(15);
        }

        $total = Restaurant::count();

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }


    public function show(Restaurant $Restaurant)
    {
        return view('admin.restaurants.show', compact('restaurants'));
    }


    public function create()
    {
        $restaurants = Restaurant::all();

        return view('admin.restaurants.create', compact('restaurants'));
    }


    public function store(Request $request)
    {
        $Restaurant = new Restaurant();
        $Restaurant->name = $request->input('name');
        $Restaurant->description = $request->input('description');
        $Restaurant->price = $request->input('price');
        $Restaurant->category_id = $request->input('category_id');
        $Restaurant->save();

        return to_route('Restaurants.index');
    }
}
