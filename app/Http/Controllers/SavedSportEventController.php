<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class savedsporteventController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->posts;
        return view('account.saved-sportevent', compact('posts'));
    }
    public function store($id)
    {
        $user = User::find(auth()->user()->id);
        $hasPost = $user->posts()->where('id', $id)->get();
        //check if the post is already saved
        if (count($hasPost)) {
            Alert::toast('Jūs jau išsisaugojote šį užsiėmima!', 'success');
            return redirect()->back();
        } else {
            Alert::toast('Išsaugota', 'success');
            $user->posts()->attach($id);
            return redirect()->route('savedsportevent.index');
        }
    }
    public function destroy($id)
    {
        $user = User::find(auth()->user()->id);
        $user->posts()->detach($id);
        Alert::toast('Ištrinta!', 'success');
        return redirect()->route('savedsportevent.index');
    }
}
