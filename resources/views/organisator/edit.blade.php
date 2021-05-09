@extends('layouts.account')

@section('content')
  <div class="account-layout border">
    <div class="account-hdr bg-primary text-white border">
      Redagavimas
    </div>
    <div class="account-bdy p-3">
     <form action="{{route('organisator.update',['id'=>$organisator])}}" method="POST" enctype="multipart/form-data">
      @if($errors->any())
        {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif

        @csrf
        @method('put')
        <div class="form-group">
          <label for="">Pasirinkite sporto užsiėmimo kategoriją</label>
          <select class="form-control" name="city" value="{{ old('city')??$organisator->organisator_city_id }}"  required>
            @foreach ($city as $city)
              <option value="{{$city->id}}">{{$city->city_name}}</option>
            @endforeach
          </select>
        </div>

        <div class="pb-3">
          <div class="py-3">
            <p>Logotipas</p>
            <img src="{{asset($organisator->logo)}}" width="80px" alt="">
          </div>
          <div class="custom-file">
            <input type="file" class="custom-file-input"  name="logo">
            <label class="custom-file-label" >Pasirinkti...</label>
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
          <input type="text" placeholder="Organizatorius" class="form-control @error('password') is-invalid @enderror" name="title" value="{{ old('title')??$organisator->title }}" required>
            @error('title')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
          <div class="pt-3">
            <p>Organizatoriaus svetainės adresas/ socialinių tinklų nuoroda</p>
            <p class="text-primary">Pavyzdžiui : https://www.worldwildlife.org/</p>
            <p class="text-primary">https://www.facebook.com/WWF/
            </p>
          </div>
          <input type="text" placeholder="organisator Website" class="form-control @error('website') is-invalid @enderror" name="website" value="{{ old('website')??$organisator->website }}" required>
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
          <textarea class="form-control @error('description') is-invalid @enderror" name="description" required>{{ old('description')??$organisator->description }}</textarea>
            @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
   
        <div class="line-divider"></div>
        <div class="mt-3">
          <button type="submit" class="btn primary-btn">Atnaujinti</button>
          <a href="{{route('account.authorSection')}}" class="btn primary-outline-btn">Atšaukti</a>
        </div>
      </form>
    </div>
  </div>
@endSection
