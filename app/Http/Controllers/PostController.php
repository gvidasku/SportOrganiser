<?php

namespace App\Http\Controllers;

use App\Events\PostViewEvent;
use App\Models\organisator;
use App\Models\city;
use App\Models\Post;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->take(20)->with('organisator')->get();
        $city = city::take(5)->get();
        $topOrganisators = organisator::latest()->take(3)->get();
        return view('home')->with([
            'posts' => $posts,
            'city' => $city,
            'topOrganisators' => $topOrganisators
        ]);
    }

    public function create()
    {
        if (!auth()->user()->organisator) {
            Alert::toast('Pirmiausia susikurkite profilį!', 'info');
            return redirect()->route('organisator.create');
        }
        return view('post.create');
    }

    public function store(Request $request)
    {
        $this->requestValidate($request);

        $postData = array_merge(['organisator_id' => auth()->user()->organisator->id], $request->all());

        $post = Post::create($postData);
        if ($post) {
            Alert::toast('Sėkmingai sukurtas!', 'success');
            return redirect()->route('account.authorSection');
        }
        Alert::toast('Nepavyko sukurti!', 'warning');
        return redirect()->back();
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);

        event(new PostViewEvent($post));
        $organisator = $post->organisator()->first();

        $similarPosts = Post::whereHas('organisator', function ($query) use ($organisator) {
            return $query->where('organisator_city_id', $organisator->organisator_city_id);
        })->where('id', '<>', $post->id)->with('organisator')->take(5)->get();
        return view('post.show')->with([
            'post' => $post,
            'organisator' => $organisator,
            'similarsportevents' => $similarPosts
        ]);
    }

    public function edit(Post $post)
    {
        return view('post.edit', compact('post'));
    }

    public function update(Request $request, $post)
    {
        $this->requestValidate($request);
        $getPost = Post::findOrFail($post);

        $newPost = $getPost->update($request->all());
        if ($newPost) {
            Alert::toast('Sėkmingai atnaujinta!', 'success');
            return redirect()->route('account.authorSection');
        }
        return redirect()->route('post.index');
    }

    public function destroy(Post $post)
    {
        if ($post->delete()) {
            Alert::toast('Sėkmingai ištrinta!', 'success');
            return redirect()->route('account.authorSection');
        }
        return redirect()->back();
    }

    protected function requestValidate($request)
    {
        return $request->validate([
            'sportevent_title' => 'required|min:3',
            'sport_category' => 'required',
            'attendance' => 'required|int',
            'event_type' => 'required',
            'sportevent_location' => 'required',
            'price' => 'required',
            'date' => 'required',
            'level' => 'required',
            'age' => 'required',
            'time' => 'required',
            'description' => 'sometimes|min:5',
        ]);
    }
}
