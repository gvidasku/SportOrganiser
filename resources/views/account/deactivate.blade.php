@extends('layouts.account')

@section('content')
  <div class="account-layout border">
    <div class="account-hdr bg-primary text-white border">
      Paskyros išaktyvavimas
    </div>
    <div class="account-bdy p-3">
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <p class="lead">Paskyros ištrynimas</p>
         
        </div>
        <div class="col-sm-12 col-md-8">
          <div class="py-3">
            <p class="mb-3">Atsijungimas</p>
            <a href="{{route('account.logout')}}" class="btn primary-outline-btn">Atsijungti</a>
          </div>
          
          <div>
            <p class="text-sm"><i class="fas fa-info-circle"></i> <span class="font-weight-bold">Ištrynę negalėsite susigrąžinti savo paskyros.</span> </p>
            <div class="my-4">
            <p class="my-3">Spustelėkite mygtuką, kad ištrintumėte šią paskyrą.</p>
              <form action="{{route('account.delete')}}" method="POST">
                @csrf
                @method('delete')
                <div class="form-group">
                  <div class="d-flex">
                    <button type="submit" class="btn danger-btn"> Ištrinti paskyrą</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endSection

