@extends('layouts.post')

@section('content')
<section class="show-page pt-4 mb-5">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-8">
        <div class="sportevent-listing border">
          <div class="organisator-info">
            <div class="organisator-banner">
              <div class="banner-overlay"></div>
              @if($organisator->cover_img == 'nocover')
              <img src="{{asset('images/organisators/nocover.jpg')}}" class="organisator-banner-img img-fluid" alt="">
              @else
              <img src="{{asset($organisator->cover_img)}}" class="organisator-banner-img img-fluid" alt="">
              @endif
              <div class="organisator-media">
                <img src="{{asset($organisator->logo)}}" alt="" class="organisator-logo">
                <div>
                  <a href="{{route('account.Organisator',['Organisator'=>$organisator])}}" class="secondary-link">
                    <p class="font-weight-bold">{{$organisator->title}}</p>
                    <p class="city">{{$organisator->getcity->city_name}}</p>
                  </a>
                </div>
              </div>
              <div class="organisator-website">
                <a href="{{$organisator->website}}" target="_blank"><i class="fas fa-globe"></i></a>
              </div>
            </div>

            {{-- organisator information --}}
            <div class="p-3">
              <p>{{$organisator->description}}</p>
            </div>
          </div>

          {{-- sportevent information --}}
          <div class="sportevent-info">
            <div class="sportevent-hdr p-3">
              <p class="sportevent-title">{{$post->sportevent_title}}</p>
              <div class="">
                <p class="sportevent-views">
                  <span class="text-success">Peržiūrėjo: {{$post->views}} </span> |
                  <span class="text-danger">Data: {{date('d',$post->remainingDays())}} days</span>
                </p>
              </div>
            </div>
            <div class="sportevent-bdy p-3 my-3">
              <div class="sportevent-level-description">
                <p class="font-weight-bold">Aprašymas</p>
                <table class="table table-hover">
                  <tbody>
                    <tr>
                      <td width="33%">Miestas</td>
                      <td width="3%">:</td>
                      <td width="64%"><a href="/sportevents">{{$organisator->getcity->city_name}}</a></td>
                    </tr>
                    <tr>
                      <td width="33%">Sporto šaka</td>
                      <td width="3%">:</td>
                      <td width="64%">{{$post->sport_category}}</td>
                    </tr>
                    <tr>
                      <td width="33%">Dalyvių skaičius</td>
                      <td width="3%">:</td>
                      <td width="64%"> <strong>{{$post->attendance}}</strong> </td>
                    </tr>
                    <tr>
                      <td width="33%">Užsiėmimo tipas</td>
                      <td width="3%">:</td>
                      <td width="64%">{{$post->event_type}}</td>
                    </tr>
                    <tr>
                      <td width="33%">Kaina</td>
                      <td width="3%">:</td>
                      <td width="64%">{{$post->price}}</td>
                    </tr>
                    <tr>
                      <td width="33%">Data</td>
                      <td width="3%">:</td>
                      <td width="64%" class="text-danger">{{date('l, jS \of F Y',$post->dateTimestamp())}}, ({{ date('d',$post->remainingDays())}} days from now)</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="sportevent-level-description">
                <table class="table table-hover">
                  <tbody>
                    <tr>
                      <td width="33%">Užsiėmimo tipas</td>
                      <td width="3%">:</td>
                      <td width="64%"><a href="/sportevents"> {{$post->level}}</a></td>
                    </tr>
                    <tr>
                      <td width="33%">Amžius</td>
                      <td width="3%">:</td>
                      <td width="64%">{{$post->age}}</td>
                    </tr>
                    <tr>
                      <td width="33%">Laikas</td>
                      <td width="3%">:</td>
                      <td width="64%">
                        @foreach($post->gettime() as $skill)
                        <span class="badge badge-primary">{{$skill}}</span>
                        @endforeach
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="sportevent-level-description">
                {{-- <p class="font-weight-bold">Daugiau</p> --}}
                <p class="py-2">{!!$post->description!!}</p>
              </div>
              <br>
              <hr>
              <div class="d-flex justify-content-between">
                <div>
                  <a href="{{route('account.applysportevent',['post_id'=>$post])}}" class="btn primary-btn">Dalyvauti</a>
                  <a href="{{route('savedsportevent.store',['id'=>$post])}}" class="btn primary-outline-btn"><i class="fas fa-star"></i> Išsaugoti</a>
                </div>
                <div class="social-links">
                  <a href="https://www.facebook.com"  target="_blank" class="btn btn-primary"><i class="fab fa-facebook"></i></a>
                  <a href="https://www.twitter.com" target="_blank"  class="btn btn-primary"><i class="fab fa-twitter"></i></a>
                  <a href="https://www.linkedin.com"  target="_blank" class="btn btn-primary"><i class="fab fa-linkedin"></i></a>
                  <a href="https://www.gmail.com" target="_blank"  class="btn btn-primary"><i class="fas fa-envelope"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-4">
        <div class="card d-none d-md-block mb-3">
          <div class="card-header">
            Veiksmai
          </div>
          <div class="card-body">
            <div class="btn-group w-100">
              <a href="{{route('account.applysportevent',['post_id'=>$post->id])}}" class="btn primary-outline-btn float-left">Dalyvauti</a>
              <a href="{{route('savedsportevent.store',['id'=>$post->id])}}" class="btn primary-btn"><i class="fas fa-star"></i> Išsaugoti</a>
            </div>
          </div>
        </div>
        <div class="card ">
          <div class="card-header">
            To paties organizatoriaus užsiėmimai
          </div>
          <div class="card-body">
            <div class="similar-sportevents">
              @foreach ($similarsportevents as $sportevent)
              @if($similarsportevents)
                <div class="sportevent-item border-bottom row">
                  <div class="col-4">
                    <img src="{{asset($sportevent->organisator->logo)}}" class="organisator-logo" alt="">
                  </div>
                  <div class="sportevent-desc col-8">
                    <a href="{{route('post.show',['sportevent'=>$post])}}" class="sportevent-city text-muted font-weight-bold">
                      <p class="text-muted h6">{{$sportevent->sportevent_title}}</p>
                      <p class="small">{{$sportevent->organisator->title}}</p>
                      <p class="font-weight-normal small text-danger">Prasideda už: {{date('d',$sportevent->remainingDays())}} dienų</p>
                    </a>
                  </div>
                </div>
                @else
                <div class="card">
                  <div class="card-header">
                    <p>Nieko panašaus nepavyko rasti</p>
                  </div>
                </div>
                @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('css')
<style>
  .organisator-banner {
    min-height: 20vh;
    position: relative;
    overflow: hidden;
  }

  .organisator-banner-img {
    width: 100%;
    height: auto;
    overflow: hidden;
  }

  .banner-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, .3));
    width: 100%;
    height: 200px;
  }

  .organisator-website {
    position: absolute;
    right: 20px;
    bottom: 20px;
    color: white;
  }

  .organisator-media {
    position: absolute;
    display: flex;
    align-items: center;
    left: 2rem;
    bottom: 1rem;
    color: #333;
    padding-right: 2rem;
    background-color:rgba(255,255,255,.8);
  }

  .organisator-logo {
    max-width: 100px;
    height: auto;
    margin-right: 1rem;
    padding: 1rem;
    background-color: white;
  }

  .city {
    font-size: 1.3rem;
  }

  .organisator-link:hover {
    color: #ddd;
  }

  .sportevent-title {
    font-size: 1.3rem;
    font-weight: bold;
  }

  .sportevent-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(to right, #e1edf7, #EDF2F7)
  }

  .sportevent-item{
    margin-bottom: .5rem;
    padding:.5rem 0;
  }
  .sportevent-item:hover {
    background-color:#eee;
  } 

</style>
@endpush

@push('js')

@endpush