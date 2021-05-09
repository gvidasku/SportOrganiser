@extends('layouts.post')

@section('content')
  <section class="home-page pt-4">
    <div class="container">
      <form action="{{route('sportevent.index')}}">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="px-4">
              <div class="rounded-text">
                <p>
                  Raskite norimus sporto užsiėmimus
                </p>
              </div>
              <div class="home-search-bar">
                  <input type="text" name="q" placeholder="Ieškoti" class="home-search-input form-control">
                  <button type="submit" class="secondary-btn"><i class="fas fa-search"></i></button>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6">
            <div class="py-5 px-5 text-center">
              <div class="text-light">
                <h4>Vienas žmogus yra svarbi komandos dalis, bet vienas žmogus niekada nebus komanda.
              </h4>
              </div>
            </div>
            </div>
        </div>   
      </form>
    </div>
  </section>
  
  {{-- sportevents list --}}
  <section class="sportevents-section py-5">
    <div class="container-fluid px-0">
      <div class="row ">
        <div class="col-sm-12 col-md-7 ml-auto">
          <div class="card">
            <div class="card-header">
              <p class="card-title font-weight-bold"><i class="far fa-calendar-alt"></i> Populiariausi</p>
            </div>
            <div class="card-body">
              <div class="top-sportevents" >
                <div class="row">

                  @foreach ($posts as $post)
                    @if ($post->organisator)
                    <div class="col-sm-6 col-md-6 col-lg-4 col-sm-6 mb-sm-3">
                      <a href="{{route('post.show',['sportevent'=>$post->id])}}">
                      <div class="sportevent-item border row h-100">
                        <div class="col-xs-3 col-sm-4 col-md-5">
                          <img src="{{asset($post->organisator->logo)}}" alt="sportevent listings" class="img-fluid p-2">
                        </div>
                        <div class="sportevent-description col-xs-9 col-sm-8 col-md-7">
                        <p class="organisator-name" title="{{$post->organisator->title}}">{{$post->organisator->title}}</p>
                          <ul class="organisator-listings">
                            <li>•{{substr($post->sportevent_title, 0, 27)}}</li>
                        </ul>
                        </div>
                      </div>
                      </a>
                    </div>
                    @endif
                  @endforeach

                 </div>
               </div>
              </div>
              <a class="btn secondary-btn" href="{{route('sportevent.index')}}">Rodyti visus</a>
            </div>
          </div>
       
        <div class="col-sm-12 col-md-3 mr-auto">

          <div class="card mb-4">
            <div class="card-header">
              <p class="font-weight-bold"><i class="fas fa-user-friends"></i> Populiariausi organizatoriai</p>
            </div>
            <div class="card-body">
              <div class="top-Organisators">
              @foreach ($topOrganisators as $Organisator)
                <div class="top-Organisator">
                  <a href="{{route('account.Organisator',['Organisator'=>$Organisator])}}">
                    <img src="{{asset($Organisator->logo)}}" width="60px" class="img-fluid" alt="">
                  </a>
                </div> 
              @endforeach
              </div>
            </div>
          </div>

            <div class="card mb-4 sportevent-by-city">
              <div class="card-header">
                <p class="font-weight-bold"><i class="fas fa-map-marker-alt"></i> Miestai</p>
              </div>
              <div class="card-body">
                <div class="sportevents-city mb-3 mt-0">
                  @foreach ($city as $city)
                  <div class="hover-shadow p-1"><a href="{{URL::to('search?city_id='.$city->id)}}" class="text-muted">{{$city->city_name}}</a> </div>
                  @endforeach
                  <a class="p-1 text-info" href="{{route('sportevent.index')}}">Daugiau..</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

