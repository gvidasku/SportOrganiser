@extends('layouts.account')

@section('content')
<div class="account-layout border">
  <div class="account-hdr bg-primary text-white border">
    Dalyvavimas sporto užsiėmime
  </div>
  <div class="account-bdy p-3">
    <div class="row">
      <div class="col-sm-12 col-md-12 mb-5">
        <div class="card">
          <div class="card-header">
            Mano profilis
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-3">
                <img src="{{asset('images/user-profile.png')}}" class="img-fluid rounded-circle" alt="">
              </div>
              <div class="col-9">
                <h6 class="text-info text-capitalize">{{auth()->user()->name}}</h6>
                <p class="my-2"><i class="fas fa-envelope"></i> Email: {{auth()->user()->email}}</p>
                <a href="{{route('account.index')}}">Peržiūrėti savo profilį</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-12">
        <div class="card">
          <div class="card-header">
            Pagrindinė informacija
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-3 d-flex align-items-center border p-3">
                <img src="{{asset($organisator->logo)}}" class="img-fluid" alt="">
              </div>
              <div class="col-9">
                <p class="h4 text-info text-capitalize">
                  {{$post->sportevent_title}}
                </p>
                <h6 class="text-uppercase">
                  <a href="{{route('account.Organisator',['Organisator'=>$organisator])}}">{{$organisator->title}}</a>
                </h6>
                <p class="my-2"><i class="fas fa-map-marker-alt"></i> Location: {{$post->sportevent_location}}</p>
                <p class="text-danger small">{{date('l, jS \of F Y',$post->dateTimestamp())}}, ({{ date('d',$post->remainingDays())}} diena iki pradžios)</p>
              </div>
            </div>
            <div class="mb-3 d-flex justify-content-end">
              <div class="my-2">
                <a href="{{route('post.show',['sportevent'=>$post])}}" class="secondary-link"><i class="fas fa-briefcase"></i> Peržiūrėti informaciją</a>|
                <a href="{{route('savedsportevent.store',['id'=>$post->id])}}" class="secondary-link"><i class="fas fa-share-square"></i> Išsaugoti sporto užsiėmimą</a>
              </div>
            </div>
            <div class="mb-3 d-flex justify-content-end">
              <div class="small">
                <a href="{{URL::previous()}}" class="btn primary-outline-btn">Atšaukti</a>
                <form action="{{route('account.applysportevent')}}" method="POST" class="d-inline-block">
                  @csrf
                  <input type="hidden" name="post_id" value="{{$post->id}}">
                  <button type="submit" class="btn primary-btn">Dalyvauti <i class="fas fa-chevron-right"></i></a>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection