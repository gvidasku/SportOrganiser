<?php

namespace App\Http\Controllers;

use App\Models\city;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class cityController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'city_name' => 'required|min:5'
        ]);
        city::create([
            'city_name' => $request->city_name
        ]);
        Alert::toast('Ištrinta!', 'success');
        return redirect()->route('account.dashboard');
    }

    public function edit(city $city)
    {
        return view('city.edit', compact('city'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'city_name' => 'required|min:5'
        ]);
        $city = city::find($id);
        $city->update([
            'city_name' => $request->city_name
        ]);
        Alert::toast('Atnaujinta!', 'success');
        return redirect()->route('account.dashboard');
    }

    public function destroy($id)
    {
        $city = city::find($id);
        $city->delete();
        Alert::toast('Ištrinta!', 'success');
        return redirect()->route('account.dashboard');
    }
}
