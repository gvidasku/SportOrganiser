@extends('layouts.account')

@section('content')
  <div class="account-layout border">
    <div class="account-hdr border bg-primary text-white shadow">
      Išsaugoti Sporto Užsiėmimai
    </div>
    <div class="account-bdy p-3">
      <div class="my-2">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead class="bg-light small">
              <tr>
                <th>Pavadinimas</th>
                <th>Sporto Rūšis</th>
                <th>Organizatorius</th>
                <th>Vietų skaičius</th>
                <th>Data</th>
                <th>Veiksmas</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($posts as $post)
                @if($posts->count() >0)
                <tr>
                  <td><a href="{{route('post.show',['sportevent'=>$post])}}">{{$post->sportevent_title}}</a></td>
                  <td><a href="#">{{$post->sport_category}}</a></td>
                  <td><a href="{{route('account.Organisator',['Organisator'=>$post->organisator])}}">{{substr($post->organisator->title,0,14)}}..</a></td>
                  <td>{{$post->attendance}}</td>
                  <td>{{date('d/m/Y',$post->dateTimestamp())}}, {{date('d',$post->remainingDays()) }} days</td>
                  <td><form action="{{route('savedsportevent.destroy',['id'=>$post])}}" method="POST">
                    @csrf
                    @method("delete")
                    <button type="submit" href="#" class="btn secondary-outline-btn">Neišsaugoti</button>
                  </form></td>
                </tr>
                @else
                <tr>
                  <td>Neturite išsaugotų sporto užsiėmimų.</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endSection
