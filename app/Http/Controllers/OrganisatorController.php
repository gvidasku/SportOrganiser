<?php

namespace App\Http\Controllers;

use App\Models\organisator;
use App\Models\city;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class organisatorController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->organisator) {
            Alert::toast('You already have a organisator!', 'info');
            return $this->edit();
        }
        $city = city::all();
        return view('organisator.create', compact('city'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateorganisator($request);

        $organisator = new organisator();
        if ($this->organisatorSave($organisator, $request)) {
            Alert::toast('Sėkmingai sukurta!', 'success');
            return redirect()->route('account.authorSection');
        }
        Alert::toast('Nepavyko sukurti!', 'error');
        return redirect()->route('account.authorSection');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $organisator = auth()->user()->organisator;
        $city = city::all();
        return view('organisator.edit', compact('organisator', 'city'));
    }


    public function update(Request $request, $id)
    {
        $this->validateorganisatorUpdate($request);

        $organisator = auth()->user()->organisator;
        if ($this->organisatorUpdate($organisator, $request)) {
            Alert::toast('Sėkmingai sukurta!', 'success');
            return redirect()->route('account.authorSection');
        }
        Alert::toast('Nepavyko sukurti!', 'error');
        return redirect()->route('account.authorSection');
    }

    protected function validateorganisator(Request $request)
    {
        return $request->validate([
            'title' => 'required|min:5',
            'description' => 'required|min:5',
            'logo' => 'required|image|max:2999',
            'city' => 'required',
            'website' => 'required|string',
            'cover_img' => 'sometimes|image|max:3999'
        ]);
    }
    protected function validateorganisatorUpdate(Request $request)
    {
        return $request->validate([
            'title' => 'required|min:5',
            'description' => 'required|min:5',
            'logo' => 'someiimes|image|max:2999',
            'city' => 'required',
            'website' => 'required|string',
            'cover_img' => 'sometimes|image|max:3999'
        ]);
    }
    protected function organisatorSave(organisator $organisator, Request $request)
    {
        $organisator->user_id = auth()->user()->id;
        $organisator->title = $request->title;
        $organisator->description = $request->description;
        $organisator->organisator_city_id = $request->city;
        $organisator->website = $request->website;

        //logo
        $fileNameToStore = $this->getFileName($request->file('logo'));
        $logoPath = $request->file('logo')->storeAs('public/organisators/logos', $fileNameToStore);
        if ($organisator->logo) {
            Storage::delete('public/organisators/logos/' . basename($organisator->logo));
        }
        $organisator->logo = 'storage/organisators/logos/' . $fileNameToStore;
        //cover image 
        if ($request->hasFile('cover_img')) {
            $fileNameToStore = $this->getFileName($request->file('cover_img'));
            $coverPath = $request->file('cover_img')->storeAs('public/organisators/cover', $fileNameToStore);
            if ($organisator->cover_img) {
                Storage::delete('public/organisators/cover/' . basename($organisator->cover_img));
            }
            $organisator->cover_img = 'storage/organisators/cover/' . $fileNameToStore;
        } else {
            $organisator->cover_img = 'nocover';
        }

        if ($organisator->save()) {
            return true;
        }
        return false;
    }

    protected function organisatorUpdate(organisator $organisator, Request $request)
    {
        $organisator->user_id = auth()->user()->id;
        $organisator->title = $request->title;
        $organisator->description = $request->description;
        $organisator->organisator_city_id = $request->city;
        $organisator->website = $request->website;

        //logo should exist but still checking for the name
        if ($request->hasFile('logo')) {
            $fileNameToStore = $this->getFileName($request->file('logo'));
            $logoPath = $request->file('logo')->storeAs('public/organisators/logos', $fileNameToStore);
            if ($organisator->logo) {
                Storage::delete('public/organisators/logos/' . basename($organisator->logo));
            }
            $organisator->logo = 'storage/organisators/logos/' . $fileNameToStore;
        }

        //cover image 
        if ($request->hasFile('cover_img')) {
            $fileNameToStore = $this->getFileName($request->file('cover_img'));
            $coverPath = $request->file('cover_img')->storeAs('public/organisators/cover', $fileNameToStore);
            if ($organisator->cover_img) {
                Storage::delete('public/organisators/cover/' . basename($organisator->cover_img));
            }
            $organisator->cover_img = 'storage/organisators/cover/' . $fileNameToStore;
        }
        $organisator->cover_img = 'nocover';
        if ($organisator->save()) {
            return true;
        }
        return false;
    }
    protected function getFileName($file)
    {
        $fileName = $file->getClientOriginalName();
        $actualFileName = pathinfo($fileName, PATHINFO_FILENAME);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        return $actualFileName . time() . '.' . $fileExtension;
    }

    public function destroy()
    {
        Storage::delete('public/organisators/logos/' . basename(auth()->user()->organisator->logo));
        if (auth()->user()->organisator->delete()) {
            return redirect()->route('account.authorSection');
        }
        return redirect()->route('account.authorSection');
    }
}
