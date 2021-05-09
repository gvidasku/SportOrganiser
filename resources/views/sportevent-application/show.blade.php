@extends('layouts.account')

@section('content')
<div class="account-layout border">
  <div class="account-hdr bg-primary text-white border">
    Informacija
  </div>
  <div class="account-bdy p-3">
  
    <div class="row">
      <div class="col-sm-12 col-md-12 mb-5">
        <div class="card">
          <div class="card-header">
            Dalyvis
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-3">
                <img src="{{asset('images/user-profile.png')}}" class="img-fluid rounded-circle" alt="">
              </div>
              <div class="col-9">
                <h6 class="text-info text-capitalize">{{$applicant->name}}</h6>
                <p class="my-2"><i class="fas fa-envelope"></i> El.Paštas: {{$applicant->email}}</p>
                <a href="mailto:{{$applicant->email}}" class="btn primary-btn" title="click to send email">Send user an email</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-12">
        <div class="card">
          <div class="card-header">
            Užsiėmimo informacija
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
                <p class="my-2"><i class="fas fa-map-marker-alt"></i> Adresas: {{$post->sportevent_location}}</p>
                <p class="text-danger small">{{date('l, jS \of F Y',$post->dateTimestamp())}}, už {{ date('d',$post->remainingDays())}} dienų nuo dabar</p>
              </div>
            </div>
            <div class="mb-3 d-flex justify-content-end">
              <div class="my-2">
                <a href="{{route('post.show',['sportevent'=>$post])}}" class="secondary-link"><i class="fas fa-briefcase"></i> Peržiūrėti</a>
              </div>
            </div>
            <div class="mb-3 d-flex justify-content-end">
              <div class="small">
                <a href="{{route('sporteventApplication.index')}}" class="btn primary-outline-btn">Atgal</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection