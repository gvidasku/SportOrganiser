@extends('layouts.account')

@section('content')
  <div class="account-layout  border">
    <div class="account-hdr bg-primary text-white border">
      Organizatorius
    </div>
    <div class="account-bdy p-3">
        <div class="row mb-3">
          <div class="col-xl-4 col-sm-6 py-2">
              <div class="card dashboard-card text-white h-100 shadow">
                  <div class="card-body primary-bg">
                      <div class="rotate">
                          <i class="fas fa-users fa-4x"></i>
                      </div>
                      <h6 class="text-uppercase">Mano sporto užsiėmimai</h6>
                      <h1 class="">{{$organisator? $organisator->posts->count() : 0}}</h1>
                  </div>
              </div>
          </div>
          <div class="col-xl-4 col-sm-6 py-2">
              <div class="card dashboard-card text-white  h-100 shadow">
                  <div class="card-body bg-info">
                      <div class="rotate">
                          <i class="fas fa-th fa-4x"></i>
                      </div>
                      <h6 class="text-uppercase">Aktyvūs sporto užsiėmimai</h6>
                      <h1 class="">{{$livePosts?? 0}}</h1>
                  </div>
              </div>
          </div>
          <div class="col-xl-4 col-sm-6 py-2">
              <a href="{{route('sporteventApplication.index')}}">
                <div class="card dashboard-card text-white h-100 shadow">
                    <div class="card-body bg-danger">
                        <div class="rotate">
                            <i class="fas fa-envelope fa-4x"></i>
                        </div>
                        <h6 class="text-uppercase">Dalyvių skaičius</h6>
                        <h1 class="">{{$applications? $applications->count():0}}</h1>
                    </div>
                </div>
              </a>
          </div>
      </div>

      <section class="author-organisator-info">
          <div class="row">
              <div class="col-sm-12 col-md-12">
                  <div class="card">
                      <div class="card-body">
                          <h4 class="card-title">Informacija</h4>
                          <p class="mb-3 alert alert-info">Norint sukurti sporto užsiėmimą, reikia susikurti profilį.</p>
                          
                          <div class="mb-3 d-flex">
                            @if(!$organisator)
                            <a href="{{route('organisator.create')}}" class="btn primary-btn mr-2">Kurti profilį</a>
                            @else
                            <a href="{{route('organisator.edit')}}" class="btn secondary-btn mr-2">Redaguoti profilį</a>
                            <div class="ml-auto">
                                <form action="{{route('organisator.destroy')}}" id="organisatorDestroyForm" method="POST">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" id="organisatorDestroyBtn" class="btn danger-btn">Ištrinti</a>
                                </form>
                            </div>
                            @endif
                          </div>
                          @if($organisator)
                          <div class="row">
                              <div class="col-sm-12 col-md-12">
                                  <div class="card">
                                      <div class="card-body text-center">
                                          <img src="{{asset($organisator->logo)}}" width="100px" class="img-fluid border p-2" alt="">
                                          <h5>{{$organisator->title}}</h5>
                                          <small>{{$organisator->getcity->city_name}}</small>
                                        <a class="d-block" href="{{$organisator->website}}"><i class="fas fa-globe"></i></a>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          @endif
                      </div>
                  </div>
              </div>
          </div>
      </section>

      <section class="author-posts">
        <div class="row my-4">
          <div class="col-lg-12 col-md-8 col-sm-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-3">Redaguoti sporto užsiėmimus</h4>
                <a href="{{route('post.create')}}" class="btn primary-btn">Sukurti naują sporto užsiėmimą</a>
              </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-inverse">
                        <tr>
                            <th>#</th>
                            <th>Pavadinimas</th>
                            <th>Sporto Rūšis</th>
                            <th>Dalyvių skaičius</th>
                            <th>Data</th>
                            <th>Veiksmai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($organisator)
                            @foreach($organisator->posts as $index=>$post)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td> <a href="{{route('post.show',['sportevent'=>$post])}}" target="_blank" title="Go to this post">{{$post->sportevent_title}}</a></td>
                                <td>{{$post->sport_category}}</td>
                                <td>{{$post->attendance}}</td>
                                <td>@php 
                                    $date = new DateTime($post->date);
                                    $timestamp =  $date->getTimestamp();
                                    $dayMonthYear = date('d/m/Y',$timestamp);
                                    $daysLeft = date('d', $timestamp - time()) .' days remaining';
                                    echo "$dayMonthYear <br> <span class='text-danger'> $daysLeft </span>";
                                @endphp</td>
                                <td>
                                <a href="{{route('post.edit',['post'=>$post])}}" class="btn primary-btn">Redaguoti</a>
                                <form action="{{route('post.destroy',['post'=>$post->id])}}" class="d-inline-block" id="delPostForm" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" id="delPostBtn" class="btn danger-btn">Ištrinti</button>
                                </form>
                                </td> 
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td>Jūs nesate sukūręs sporto užsiėmimo.</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
          </div>
        </div>
      <!--/row-->
      </section>

    </div>
  </div>
@endSection

@push('js')
<script>
    $(document).ready(function(){
        //delete author organisator
        $('#organisatorDestroyBtn').click(function(e){
            e.preventDefault();
            if(window.confirm('Ar jūs tikrai norite ištrinti?')){
                $('#organisatorDestroyForm').submit();
            }
        })
    })
</script>    
@endpush