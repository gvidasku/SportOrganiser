<?php

namespace App\Http\Controllers;

use App\Models\organisator;
use App\Models\city;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class sporteventController extends Controller
{
    public function index()
    {
        return view('sportevent.index');
    }

    //api route
    public function search(Request $request)
    {
        if ($request->q) {
            $posts = Post::where('sportevent_title', 'LIKE', '%' . $request->q . '%');
        } elseif ($request->city_id) {
            $posts = Post::whereHas('organisator', function ($query) use ($request) {
                return $query->where('organisator_city_id', $request->city_id);
            });
        } elseif ($request->sport_category) {
            $posts = Post::where('sport_category', 'Like', '%' . $request->sport_category . '%');
        } elseif ($request->level) {
            $posts = Post::where('level', 'Like', '%' . $request->level . '%');
        } elseif ($request->event_type) {
            $posts = Post::where('event_type', 'Like', '%' . $request->event_type . '%');
        } else {
            $posts = Post::take(30);
        }

        $posts = $posts->has('organisator')->with('organisator')->paginate(6);

        return $posts->toJson();
    }
    public function getcity()
    {
        $city = city::all();
        return $city->toJson();
    }
    public function getAllOrganization()
    {
        $organisators = organisator::all();
        return $organisators->toJson();
    }
    public function getAllByTitle()
    {
        $posts = Post::where('date', '>', Carbon::now())->get()->pluck('id', 'sportevent_title');
        return $posts->toJson();
    }
}
