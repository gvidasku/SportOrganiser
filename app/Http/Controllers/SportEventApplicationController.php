<?php

namespace App\Http\Controllers;

use App\Models\sporteventApplication;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class sporteventApplicationController extends Controller
{
    public function index()
    {
        $applicationsWithPostAndUser = null;
        $organisator = auth()->user()->organisator;

        if ($organisator) {
            $ids =  $organisator->posts()->pluck('id');
            $applications = sporteventApplication::whereIn('post_id', $ids);
            $applicationsWithPostAndUser = $applications->with('user', 'post')->latest()->paginate(10);
        }

        return view('sportevent-application.index')->with([
            'applications' => $applicationsWithPostAndUser,
        ]);
    }
    public function show($id)
    {
        $application = sporteventApplication::find($id);

        $post = $application->post()->first();
        $userId = $application->user_id;
        $applicant = User::find($userId);

        $organisator = $post->organisator()->first();
        return view('sportevent-application.show')->with([
            'applicant' => $applicant,
            'post' => $post,
            'organisator' => $organisator,
            'application' => $application
        ]);
    }
    public function destroy(Request $request)
    {
        $application = sporteventApplication::find($request->application_id);
        $application->delete();
        Alert::toast('Ištrinta', 'warning');
        return redirect()->route('sporteventApplication.index');
    }
}
