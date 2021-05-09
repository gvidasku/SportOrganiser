@extends('layouts.account')
@section('content')
<div class="account-layout  border">
  <div class="account-hdr bg-primary text-white border">
   Tapti organizatoriumi
  </div>
  <div class="account-bdy p-3">
    <div class="row">
      <div class="col-sm-12 col-md-4">
        <p class="lead">Organizatoriaus rolė</p>
      </div>
      <div class="col-sm-12 col-md-8">
        <div>
          <div class="my-4">
          <p class="my-3">Paspauskite mygtuką norėdami tapti <span class="text-primary">Organizatoriumi</span>.</p>
            <form action="{{route('account.becomeOrganisator')}}" method="POST">
              @csrf
              <div class="form-group">
                <div class="d-flex">
                  <button type="submit" class="btn primary-outline-btn">Tapti organizatoriumi</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection