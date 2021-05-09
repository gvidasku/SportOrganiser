@extends('layouts.account')

@section('content')
<div class="account-layout border">
  <div class="account-hdr bg-primary text-white border " >
    Redagavimas
  </div>
  <div class="account-bdy p-3">
    @if($errors->any())
    {!! implode('', $errors->all('<div>:message</div>')) !!}
@endif
      <div class="row mb-3">
        <div class="col-12">
          <p class="alert alert-primary">Redagavimas : {{$city->city_name}}</p>
          <form action="{{route('city.update',['id'=>$city->id])}}" method="POST">
            @csrf
            @method('put')
            <div class="form-group">
              <label for="">Pasirinkite</label>
              <input type="text" placeholder="" name="city_name" class="form-control @error('city_name') input-error @enderror">
            </div>
            <div class="d-flex">
              <button type="submit" class="btn secondary-btn mr-3">Atnaujinti</button>
              <a href="{{route('account.dashboard')}}" class="btn primary-outline-btn">Atšaukti</a>
            </div>
          </form>
        </div>
      </div>
  </div>
</div>
@endsection