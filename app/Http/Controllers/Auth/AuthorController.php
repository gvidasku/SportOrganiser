<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\organisator;
use App\Models\sporteventApplication;
use Carbon\Carbon;

class AuthorController extends Controller
{
    /** Author dashboard */
    public function authorSection()
    {
        $livePosts = null;
        $organisator = null;
        $applications = null;

        if ($this->hasorganisator()) {
            //without the if block the posts relationship returns error
            $organisator = auth()->user()->organisator;
            $posts = $organisator->posts()->get();

            if ($organisator->posts->count()) {
                $livePosts = $posts->where('date', '>', Carbon::now())->count();
                $ids = $posts->pluck('id');
                $applications = sporteventApplication::whereIn('post_id', $ids)->get();
            }
        }

        //doesnt have organisator
        return view('account.author-section')->with([
            'organisator' => $organisator,
            'applications' => $applications,
            'livePosts' => $livePosts
        ]);
    }

    // Author Organisator panel
    //Organisator is organisator of author
    public function Organisator($Organisator)
    {
        $organisator = organisator::find($Organisator)->with('posts')->first();
        return view('account.Organisator')->with([
            'organisator' => $organisator,
        ]);
    }

    //check if has organisator
    protected function hasorganisator()
    {
        return auth()->user()->organisator ? true : false;
    }
}
