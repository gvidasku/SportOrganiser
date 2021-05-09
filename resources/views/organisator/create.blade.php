@extends('layouts.account')

@section('content')
  <div class="account-layout border">
    <div class="account-hdr bg-primary text-white border">
      Profilio kūrimas
    </div>
    <div class="account-bdy p-3">
     <form action="{{route('organisator.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label for="">Pasirinkite sporto užsiėmimo kategoriją</label>
          <select class="form-control" name="city" value="{{ old('city') }}" required>
            @foreach($city as $city)
          <option value="{{$city->id}}">{{$city->city_name}}</option>
            @endforeach
          </select>
        </div>

        <div class="pb-3">
          <div class="py-3">
            <p>Logotipas</p>
          </div>
          <div class="custom-file">
            <input type="file" class="custom-file-input" id="validatedCustomFile" name="logo" required>
            <label class="custom-file-label" for="validatedCustomFile">Pasirinkti...</label>
            @error('logo')
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
        </div>

        <div class="form-group">
          <div class="py-3">
            <p>Organizatorius</p>
          </div>
          <input type="text" placeholder="Organizatorius" class="form-control @error('password') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
            @error('title')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
          <div class="py-3">
            <p>Organizatoriaus svetainės adresas/ socialinių tinklų nuoroda </p>
            <p class="text-primary">Pavyzdžiui : https://www.worldwildlife.org/
            </p>
             <p class="text-primary">https://www.facebook.com/WWF/
            </p>
          </div>
          <input type="text" placeholder="Svetainė" class="form-control @error('website') is-invalid @enderror" name="website" value="{{ old('website')}}" required>
            @error('website')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

      

        <div class="pt-2">
          <p class="mt-3 alert alert-primary">Trumpas aprašymas</p>
        </div>
        <div class="form-group">
          <textarea class="form-control @error('description') is-invalid @enderror" name="description" required>{{ old('description') }}</textarea>
            @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
   
        <div class="line-divider"></div>
        <div class="mt-3">
          <button type="submit" class="btn primary-btn">Sukurti</button>
          <a href="{{route('account.authorSection')}}" class="btn primary-outline-btn">Atšaukti</a>
        </div>
      </form>
    </div>
  </div>
@endSection
